<?php declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Middleware\ResponseNormalizationMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Faz 2 açık #5 — `no-store` + ETag çelişkisi ve 304 `exit` bypass'ı.
 *
 *  1. ETag yalnız `cacheable` route'da üretilir (no-store yanıtta ETag YOK).
 *  2. 304 yolu `exit`/`die` KULLANMAZ → `halt` + boş gövde döner; böylece
 *     header kopyalama / index.php akışı bozulmaz.
 *
 * Not: CLI SAPI'de `header()` yazımı gözlemlenemez (`headers_list()` boştur);
 * bu yüzden sözleşmenin gözlenebilir tarafı (304/halt/birakma) + kaynakta
 * `exit` bulunmaması sınanır.
 */
final class ResponseNormalizationMiddlewareTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    public function testMiddlewareContainsNoExitOrDieBypass(): void
    {
        $source = (string) file_get_contents(
            dirname(__DIR__, 2) . '/src/Api/Middleware/ResponseNormalizationMiddleware.php'
        );

        $this->assertDoesNotMatchRegularExpression('/\b(exit|die)\b\s*[(;]/', $source, '304 yolu exit ile index.php\'yi bypass edemez');
        $this->assertStringContainsString("'halt'", $source, '304 gövdesiz halt ile dönmeli');
    }

    public function testCacheableRouteWithMatchingEtagReturnsHalt304(): void
    {
        $response = ['data' => ['service' => 'auth'], 'meta' => ['version' => '1.0.0']];
        $etag     = md5((string) json_encode($response));

        $_SERVER['HTTP_IF_NONE_MATCH'] = '"' . $etag . '"';

        $out = $this->process(['_route' => ['cacheable' => true, 'cacheTtl' => 60]], $response);

        $this->assertSame(304, $out['httpStatus']);
        $this->assertTrue($out['halt'], 'Gövdesiz 304: halt bayrağı set edilmeli');
        $this->assertSame('', $out['body']);
        // exit olsaydı bu satırlara hiç ulaşılmazdı.
        $this->assertSame(['service' => 'auth'], $out['data']);
    }

    public function testNonCacheableRouteNeverProduces304(): void
    {
        $response = ['data' => ['service' => 'auth']];

        // Aynı ETag değeri gönderilsin: no-store yanıtta ETag basılmadığı için
        // 304 tetiklenmemeli.
        $_SERVER['HTTP_IF_NONE_MATCH'] = '"' . md5((string) json_encode($response)) . '"';

        $out = $this->process(['_route' => ['cacheable' => false, 'cacheTtl' => 0]], $response);

        $this->assertArrayNotHasKey('httpStatus', $out, 'no-store yanıtta 304 yolu çalışmamalı');
        $this->assertArrayNotHasKey('halt', $out);
        $this->assertSame(['service' => 'auth'], $out['data']);
    }

    public function testMissingRouteIsTreatedAsNonCacheable(): void
    {
        $response = ['data' => ['service' => 'auth']];

        $_SERVER['HTTP_IF_NONE_MATCH'] = '"' . md5((string) json_encode($response)) . '"';

        $out = $this->process([], $response);

        $this->assertArrayNotHasKey('halt', $out, '_route yokken (404/405) cacheable DEĞİL');
        $this->assertSame(['service' => 'auth'], $out['data']);
    }

    public function testErrorResponsesPassThrough(): void
    {
        $error = ['error' => ['code' => 'NOT_FOUND', 'message' => 'Route not found']];

        $out = $this->process(['_route' => ['cacheable' => true, 'cacheTtl' => 60]], $error);

        $this->assertArrayNotHasKey('halt', $out, 'Boş data\'lı yanıtta ETag basılmaz');
        $this->assertSame('NOT_FOUND', $out['error']['code']);
    }

    /**
     * @param array<string, mixed> $request
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    private function process(array $request, array $response): array
    {
        $middleware = new ResponseNormalizationMiddleware();

        return $middleware($request, static fn (): array => $response);
    }
}
