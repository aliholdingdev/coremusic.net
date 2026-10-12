<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use CoreMusic\Middleware\OriginCheckMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * F-04 regresyonu — allowlist girdi formatı normalizasyonu.
 *
 * cors.php, CORS_ALLOWED_ORIGINS env değerini olduğu gibi okur; değer scheme'li
 * ("https://auth.coremusic.net") gelirse OriginCheck çıplak-host karşılaştırması
 * hiç eşleşmez ve tüm cross-origin 403 olurdu (fail-broken). CorsMiddleware her
 * iki biçimi de karşıladığından OriginCheck de karşılamalıdır.
 */
final class OriginCheckMiddlewareTest extends TestCase
{
    /** @return array<string, mixed> */
    private static function probe(OriginCheckMiddleware $mw, string $origin): array
    {
        $server = $origin === '' ? [] : ['HTTP_ORIGIN' => $origin];
        return $mw->handle(
            ['server' => $server],
            static fn (array $req): array => ['httpStatus' => 0, 'type' => 'json', 'halt' => false],
        );
    }

    public function testBareHostAllowlistEntry_allowsMatchingOrigin(): void
    {
        $mw = new OriginCheckMiddleware(true, ['allowed_origins' => ['music.coremusic.net']]);

        $result = self::probe($mw, 'https://music.coremusic.net');

        $this->assertSame(0, $result['httpStatus'], 'Çıplak host girdisi eşleşmeli');
    }

    public function testSchemefulAllowlistEntry_allowsMatchingOrigin(): void
    {
        // F-04: scheme'li env değeri artık fail-broken değil.
        $mw = new OriginCheckMiddleware(true, ['allowed_origins' => ['https://auth.coremusic.net']]);

        $result = self::probe($mw, 'https://auth.coremusic.net');

        $this->assertSame(0, $result['httpStatus'], 'Scheme\'li girdi host\'una normalize edilmeli');
    }

    public function testSchemefulAllowlistEntry_stillRejectsOtherHosts(): void
    {
        // Normalizasyon güveni gevşetmemeli: izinsiz host yine 403.
        $mw = new OriginCheckMiddleware(true, ['allowed_origins' => ['https://auth.coremusic.net']]);

        $result = self::probe($mw, 'https://evil.example.test');

        $this->assertSame(403, $result['httpStatus']);
        $this->assertTrue($result['halt'] ?? false);
    }

    public function testMissingOrigin_passesThrough(): void
    {
        $mw = new OriginCheckMiddleware(true, ['allowed_origins' => ['auth.coremusic.net']]);

        $result = self::probe($mw, '');

        $this->assertSame(0, $result['httpStatus'], 'Origin yoksa (server-to-server) geçmeli');
    }

    public function testEmptyAllowlistInProduction_rejectsOrigin(): void
    {
        $mw = new OriginCheckMiddleware(true, ['allowed_origins' => []]);

        $result = self::probe($mw, 'https://auth.coremusic.net');

        $this->assertSame(403, $result['httpStatus'], 'Prod + boş allowlist = fail-closed');
    }
}
