<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Controller\PlaylistController;
use CoreMusic\Api\Service\IPlaylistService;
use PHPUnit\Framework\TestCase;

/**
 * PlaylistController — kimlik + validasyon + sahiplik hata haritası (stub IPlaylistService).
 * Faz 4: private uçlar `_auth_user.id` ile; service null/[] → 404 (enumerate koruma).
 */
final class PlaylistControllerTest extends TestCase
{
    private const USER = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

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
    private function service(array $overrides = []): IPlaylistService
    {
        return new class ($overrides) implements IPlaylistService {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function listForUser(string $userId): array
            {
                return $this->config['list'] ?? [['playlist_id' => 'p1', 'name' => 'Listem']];
            }

            public function create(string $userId, array $data): array
            {
                return $this->config['created'] ?? ['playlist_id' => 'p1', 'name' => $data['name'] ?? ''];
            }

            public function getWithTracks(string $playlistId, string $userId): ?array
            {
                return $this->config['playlist'] ?? null;
            }

            public function addTrack(string $playlistId, string $trackId, string $userId): ?array
            {
                return $this->config['added'] ?? null;
            }

            public function removeTrack(string $playlistId, string $trackId, string $userId): bool
            {
                return (bool) ($this->config['removed'] ?? false);
            }

            public function update(string $playlistId, array $data, string $userId): ?array
            {
                if (array_key_exists('updated', $this->config)) {
                    return $this->config['updated'];
                }

                return ['playlist_id' => $playlistId] + $data;
            }

            public function delete(string $playlistId, string $userId): bool
            {
                return (bool) ($this->config['deleted'] ?? false);
            }

            public function reorder(string $playlistId, array $trackIds, string $userId): ?array
            {
                if (array_key_exists('reordered', $this->config)) {
                    return $this->config['reordered'];
                }

                return ['playlist_id' => $playlistId, 'order' => $trackIds];
            }
        };
    }

    /** @param array<string, mixed> $body */
    private function handle(IPlaylistService $service, string $action, ?string $userId = self::USER, array $body = []): array
    {
        $request = ['body' => $body];
        if ($userId !== null) {
            $request['_auth_user'] = ['id' => $userId, 'authenticated' => true, 'method' => 'session'];
        }

        return (new PlaylistController($service))->handle(['action' => $action], $request);
    }

    public function testIndexReturns401WithoutAuth(): void
    {
        $result = $this->handle($this->service(), 'index', null);

        $this->assertSame('UNAUTHORIZED', $result['error']['code']);
        $this->assertSame(401, http_response_code());
    }

    public function testIndexReturnsUserPlaylists(): void
    {
        $result = $this->handle($this->service(), 'index');

        $this->assertTrue($result['success']);
        $this->assertSame('Listem', $result['data']['items'][0]['name']);
    }

    public function testCreateWithoutNameReturns422(): void
    {
        $result = $this->handle($this->service(), 'create', self::USER, []);

        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertSame(422, http_response_code());
    }

    public function testCreateReturnsPlaylist(): void
    {
        $result = $this->handle($this->service(), 'create', self::USER, ['name' => 'Yeni']);

        $this->assertTrue($result['success']);
        $this->assertSame('Yeni', $result['data']['name']);
    }

    public function testShowReturns404WhenPrivateOrMissing(): void
    {
        $_GET = ['id' => 'p1'];

        $result = $this->handle($this->service(), 'show');

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testShowReturnsPlaylistWithTracks(): void
    {
        $_GET = ['id' => 'p1'];
        $result = $this->handle($this->service(['playlist' => ['playlist_id' => 'p1', 'tracks' => []]]), 'show');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('tracks', $result['data']['playlist']);
    }

    public function testAddTrackWithoutIdsReturns422(): void
    {
        $result = $this->handle($this->service(), 'addTrack', self::USER, ['playlist_id' => '']);

        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertArrayHasKey('fields', $result['error']);
    }

    public function testAddTrackOwnershipViolationReturns404(): void
    {
        $result = $this->handle($this->service(), 'addTrack', self::USER, [
            'playlist_id' => 'p1', 'track_id' => 't1',
        ]);

        // Service null (liste yok / sahibi değil) → 404 — enumerate koruma.
        $this->assertSame('NOT_FOUND', $result['error']['code']);
    }

    public function testAddTrackReturnsRelation(): void
    {
        $result = $this->handle($this->service(['added' => ['playlist_id' => 'p1', 'track_id' => 't1', 'position' => 3]]), 'addTrack', self::USER, [
            'playlist_id' => 'p1', 'track_id' => 't1',
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame(3, $result['data']['position']);
    }

    // ─── Adım 07: detay · güncelle · sil · çıkar · sırala ───

    public function testCreateReturns201Created(): void
    {
        $result = $this->handle($this->service(), 'create', self::USER, ['name' => 'Yeni']);

        $this->assertTrue($result['success']);
        $this->assertSame(201, http_response_code(), 'POST create → 201 Created (Adım 07)');
    }

    public function testShowWithoutIdReturns404(): void
    {
        $result = $this->handle($this->service(), 'show');

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testUpdateWithoutIdReturns422(): void
    {
        $result = $this->handle($this->service(), 'update', self::USER, ['name' => 'X']);

        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertSame(422, http_response_code());
        $this->assertArrayHasKey('id', $result['error']['fields']);
    }

    public function testUpdateWithoutMutableFieldReturns422(): void
    {
        $result = $this->handle($this->service(), 'update', self::USER, ['id' => 'p1']);

        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertSame(422, http_response_code());
    }

    public function testUpdateWithInvalidVisibilityReturns422(): void
    {
        $result = $this->handle($this->service(), 'update', self::USER, [
            'id' => 'p1', 'visibility' => 'everyone',
        ]);

        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertSame(422, http_response_code());
        $this->assertStringContainsString('visibility', (string) ($result['error']['fields']['visibility'] ?? ''));
    }

    public function testUpdateOwnershipViolationReturns404(): void
    {
        $result = $this->handle($this->service(['updated' => null]), 'update', self::USER, [
            'id' => 'p1', 'name' => 'Başkasının listesi',
        ]);

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testUpdateReturnsPlaylist(): void
    {
        $result = $this->handle($this->service(), 'update', self::USER, [
            'id' => 'p1', 'name' => 'Yeni Ad',
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('Yeni Ad', $result['data']['playlist']['name']);
    }

    public function testDeleteReturns204WhenOwned(): void
    {
        $_GET = ['id' => 'p1'];

        $result = $this->handle($this->service(['deleted' => true]), 'delete');

        $this->assertTrue((bool) ($result['halt'] ?? false), 'DELETE → halt (gövdesiz)');
        $this->assertSame(204, (int) ($result['httpStatus'] ?? 0));
        $this->assertSame('', $result['body'] ?? null);
    }

    public function testDeleteOwnershipViolationReturns404(): void
    {
        $_GET = ['id' => 'p1'];

        $result = $this->handle($this->service(['deleted' => false]), 'delete');

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testRemoveTrackReturnsSuccess(): void
    {
        $_GET = ['playlist_id' => 'p1', 'track_id' => 't1'];

        $result = $this->handle($this->service(['removed' => true]), 'removeTrack');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['data']['removed']);
    }

    public function testRemoveTrackOwnershipViolationReturns404(): void
    {
        $_GET = ['playlist_id' => 'p1', 'track_id' => 't1'];

        $result = $this->handle($this->service(['removed' => false]), 'removeTrack');

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testReorderReturnsPersistedOrder(): void
    {
        $ids = ['bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'];

        $result = $this->handle($this->service(), 'reorder', self::USER, [
            'playlist_id' => 'p1',
            'track_ids'   => $ids,
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('p1', $result['data']['playlist_id']);
        $this->assertSame($ids, $result['data']['order']);
        $this->assertSame(200, http_response_code());
    }

    public function testReorderWithInvalidTrackIdReturns422(): void
    {
        $result = $this->handle($this->service(), 'reorder', self::USER, [
            'playlist_id' => 'p1',
            'track_ids'   => ['not-a-hex-id'],
        ]);

        $this->assertSame('VALIDATION_ERROR', $result['error']['code']);
        $this->assertSame(422, http_response_code());
        $this->assertArrayHasKey('track_ids.0', $result['error']['fields']);
    }

    public function testReorderOwnershipViolationReturns404(): void
    {
        $result = $this->handle($this->service(['reordered' => null]), 'reorder', self::USER, [
            'playlist_id' => 'p1',
            'track_ids'   => ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'],
        ]);

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testAllPlaylistActionsReturn401WithoutAuth(): void
    {
        foreach (['index', 'create', 'show', 'update', 'delete', 'addTrack', 'removeTrack', 'reorder'] as $action) {
            $result = $this->handle($this->service(), $action, null);

            $this->assertSame('UNAUTHORIZED', $result['error']['code'], "{$action} auth'sız 401");
            $this->assertSame(401, http_response_code(), "{$action} auth'sız 401 status");
        }
    }
}
