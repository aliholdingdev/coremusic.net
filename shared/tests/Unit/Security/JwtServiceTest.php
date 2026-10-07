<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Security;

use CoreMusic\Security\JwtService;
use PHPUnit\Framework\TestCase;

/**
 * P1-9 (B-F-01) regresyonu — RS256 JWT issue/validate.
 *
 * Keypair'ler COMMIT EDİLMİŞ test fixture'larıdır
 * (shared/tests/Fixtures/jwt/test*-*.pem) — production key'ine dokunulmaz.
 * Not: PHP'de openssl_pkey_new cnf gerektirir (Windows); signing doğrulama
 * gerektirmez → fixture + openssl_sign yolu runtime'da da çalışır.
 *
 * Kritik yollar: imza doğrulama, süre (exp), issuer/audience, tamper reddi,
 * eksik sub/jti reddi (fail-closed).
 */
final class JwtServiceTest extends TestCase
{
    private string $privatePath;
    private string $publicPath;
    private string $foreignPrivatePath;
    private JwtService $jwt;

    protected function setUp(): void
    {
        $fixtures = dirname(__DIR__, 2) . '/Fixtures/jwt';
        $this->privatePath        = $fixtures . '/test-private.pem';
        $this->publicPath         = $fixtures . '/test-public.pem';
        $this->foreignPrivatePath = $fixtures . '/test2-private.pem';

        $this->assertFileExists($this->privatePath);
        $this->assertFileExists($this->publicPath);
        $this->assertFileExists($this->foreignPrivatePath);

        $this->jwt = new JwtService(
            privateKeyPath: $this->privatePath,
            publicKeyPath: $this->publicPath,
            issuer: 'coremusic',
            audience: 'coremusic-api',
            ttlSeconds: 60,
        );
    }

    public function testIssueAndValidate_roundTrip(): void
    {
        $issued = $this->jwt->issue('018f00000000000000000000000000aa');

        $this->assertNotEmpty($issued['token']);
        $this->assertSame('Bearer', $issued['token_type']);
        $this->assertSame(60, $issued['expires_in']);
        $this->assertSame(32, strlen($issued['jti']), 'jti = 16 byte hex (32 karakter)');

        $claims = $this->jwt->validate($issued['token']);

        $this->assertIsArray($claims);
        $this->assertSame('018f00000000000000000000000000aa', $claims['sub']);
        $this->assertSame($issued['jti'], $claims['jti']);
        $this->assertSame('coremusic', $claims['iss']);
        $this->assertSame('coremusic-api', $claims['aud']);
    }

    public function testValidate_rejectsTamperedPayload(): void
    {
        $issued = $this->jwt->issue('018f00000000000000000000000000aa');
        [$header, $payload, $signature] = explode('.', $issued['token']);

        $forgedPayload = strtr(base64_encode((string) json_encode([
            'iss' => 'coremusic', 'aud' => 'coremusic-api',
            'sub' => 'ffffffffffffffffffffffffffffffff',
            'iat' => time(), 'nbf' => time(), 'exp' => time() + 3600,
            'jti' => 'forgedjti',
        ])), '+/', '-_');

        $forged = $header . '.' . $forgedPayload . '.' . $signature;

        $this->assertNull($this->jwt->validate($forged), 'Imzasi kirilmis token reddedilmeli');
    }

    public function testValidate_rejectsGarbageAndEmpty(): void
    {
        $this->assertNull($this->jwt->validate(''));
        $this->assertNull($this->jwt->validate('not-a-jwt'));
        $this->assertNull($this->jwt->validate('a.b.c'));
    }

    public function testValidate_rejectsExpiredToken(): void
    {
        $shortLived = new JwtService($this->privatePath, $this->publicPath, 'coremusic', 'coremusic-api', ttlSeconds: -10);
        $issued = $shortLived->issue('018f00000000000000000000000000aa');

        $this->assertNull($this->jwt->validate($issued['token']), 'Suresi gecmis token reddedilmeli');
    }

    public function testValidate_rejectsWrongIssuerOrAudience(): void
    {
        $otherIssuer = new JwtService($this->privatePath, $this->publicPath, 'evil-issuer', 'coremusic-api', 60);
        $issued = $otherIssuer->issue('018f00000000000000000000000000aa');
        $this->assertNull($this->jwt->validate($issued['token']), 'Yanlis iss reddedilmeli');

        $otherAudience = new JwtService($this->privatePath, $this->publicPath, 'coremusic', 'other-aud', 60);
        $issued2 = $otherAudience->issue('018f00000000000000000000000000aa');
        $this->assertNull($this->jwt->validate($issued2['token']), 'Yanlis aud reddedilmeli');
    }

    public function testValidate_rejectsTokenSignedByDifferentKey(): void
    {
        // Yabancı private key ile imzalanmış token — bizim public key doğrulayamaz
        $foreign = new JwtService($this->foreignPrivatePath, $this->foreignPrivatePath, 'coremusic', 'coremusic-api', 60);
        $issued  = $foreign->issue('018f00000000000000000000000000aa');

        $this->assertNull($this->jwt->validate($issued['token']), 'Yabanci imza reddedilmeli');
    }

    public function testIssue_rejectsEmptySubject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->jwt->issue('');
    }

    public function testValidate_rejectsMissingSubOrJti(): void
    {
        // sub/jti OLMAYAN ama dogru key ile imzalanmis token — claim eksikligi fail-closed
        $token = \Firebase\JWT\JWT::encode([
            'iss' => 'coremusic', 'aud' => 'coremusic-api',
            'iat' => time(), 'nbf' => time(), 'exp' => time() + 60,
        ], file_get_contents($this->privatePath), 'RS256');

        $this->assertNull($this->jwt->validate($token));
    }

    public function testIssue_missingPrivateKeyFile_throws(): void
    {
        $broken = new JwtService(
            dirname($this->privatePath) . '/definitely-missing.pem',
            $this->publicPath,
        );

        $this->expectException(\RuntimeException::class);
        $broken->issue('018f00000000000000000000000000aa');
    }
}
