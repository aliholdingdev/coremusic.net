<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Controller\MusicController;
use CoreMusic\Api\Service\IMusicService;
use PHPUnit\Framework\TestCase;

/**
 * MusicController — HTTP çeviri katmanı (stub IMusicService).
 * Faz 4: sözleşme {success:true,data:{...}} / {error:{code,message}} (Faz 1b DEĞİŞMEZ).
 */
final class MusicControllerTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $getBackup = [];

    protected function setUp(): void
    {
        $this->getBackup = $_GET;
        $_GET = [];
        http_response_code(200);
    }

    protected function tearDown(): void
    {
        $_GET = $this->getBackup;
    }

    /** @param array<string, mixed> $overrides */
    private function service(array $overrides = []): IMusicService
    {
        return new class ($overrides) implements IMusicService {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function listTracks(int $page, int $perPage): array
            {
                return $this->config['list'] ?? ['items' => [['music_hex' => 'aa', 'title' => 'T']], 'total' => 1, 'page' => $page, 'per_page' => $perPage];
            }

            public function searchTracks(string $query, int $limit): array
            {
                return $this->config['search'] ?? ['items' => [], 'total' => 0];
            }

            public function getTrack(string $trackId): ?array
            {
                return $this->config['track'] ?? null;
            }
        };
    }

    private function handle(IMusicService $service, string $action): array
    {
        return (new MusicController($service))->handle(['action' => $action], ['body' => []]);
    }

    public function testTracksReturnsPaginatedSuccess(): void
    {
        $_GET = ['page' => '2', 'per_page' => '10'];

        $result = $this->handle($this->service(), 'tracks');

        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['data']['page']);
        $this->assertSame(10, $result['data']['per_page']);
    }

    public function testSearchWithoutQueryReturns422(): void
    {
        $result = $this->handle($this->service(), 'search');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertSame(422, http_response_code());
    }

    public function testSearchWithQueryReturnsSuccess(): void
    {
        $_GET = ['q' => 'test'];

        $result = $this->handle($this->service(), 'search');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('items', $result['data']);
    }

    public function testShowReturns404WhenTrackMissing(): void
    {
        $result = $this->handle($this->service(), 'show');

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testShowReturnsTrackWhenFound(): void
    {
        $_GET = ['id' => 'abc'];
        $result = $this->handle($this->service(['track' => ['music_hex' => 'abc', 'title' => 'Şarkı']]), 'show');

        $this->assertTrue($result['success']);
        $this->assertSame('Şarkı', $result['data']['track']['title']);
    }
}
