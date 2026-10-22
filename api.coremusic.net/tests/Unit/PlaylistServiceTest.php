<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Service\PlaylistService;
use PHPUnit\Framework\TestCase;

/**
 * PlaylistService — sahiplik matrisi + repo çağrı güvencesi (fake IPlaylistRepository).
 *
 * Adım 07 güvenceleri:
 *   - sahibi değil → null/false, DEĞİŞTİRİCİ repo çağrısı YOK (IDOR koruma)
 *   - reorder → bilinmeyen/tekrar eden id yok sayılır, eksikler mevcut sırayla sona (idempotent)
 *   - delete → softDeleteWithTracks çağrılır (hard DELETE yok)
 *
 * Not: service önce findById (sahiplik okuma) çağırır; "yazım yok" güvencesi
 * yalnız DEĞİŞTİRİCİ metodların (appendTrack/updateMeta/...) çağrılmadığını ölçer.
 */
final class PlaylistServiceTest extends TestCase
{
    private const OWNER = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    private const OTHER = 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb';
    private const LIST  = 'cccccccccccccccccccccccccccccccc';
    private const T1    = '11111111111111111111111111111111';
    private const T2    = '22222222222222222222222222222222';
    private const T3    = '33333333333333333333333333333333';

    private FakePlaylistRepository $repo;
    private PlaylistService $service;

    protected function setUp(): void
    {
        $this->repo    = new FakePlaylistRepository();
        $this->service = new PlaylistService($this->repo);
    }

    /** @param array<string, mixed> $playlist */
    private function seedPlaylist(array $playlist = []): void
    {
        $this->repo->playlists[self::LIST] = $playlist + [
            'playlist_id'  => self::LIST,
            'owner_id_hex' => self::OWNER,
            'visibility'   => 'private',
            'name'         => 'Listem',
        ];
    }

    // ─── getWithTracks (visibility + ownership) ───

    public function testGetWithTracksReturnsNullForMissingPlaylist(): void
    {
        $this->assertNull($this->service->getWithTracks(self::LIST, self::OWNER));
    }

    public function testGetWithTracksHidesPrivatePlaylistFromNonOwner(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->getWithTracks(self::LIST, self::OTHER));
        $this->assertSame(['findById'], $this->repo->methods(), 'Gizli listede findTracks hiç çağrılmaz');
    }

    public function testGetWithTracksReturnsPublicPlaylistToNonOwner(): void
    {
        $this->seedPlaylist(['visibility' => 'public']);
        $this->repo->tracks[self::LIST] = [['music_id' => self::T1]];

        $result = $this->service->getWithTracks(self::LIST, self::OTHER);

        $this->assertNotNull($result);
        $this->assertSame(self::T1, $result['tracks'][0]['music_id']);
    }

    public function testGetWithTracksReturnsPrivatePlaylistToOwner(): void
    {
        $this->seedPlaylist();

        $result = $this->service->getWithTracks(self::LIST, self::OWNER);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('tracks', $result);
    }

    // ─── addTrack (appendTrack tx) ───

    public function testAddTrackByNonOwnerReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $result = $this->service->addTrack(self::LIST, self::T1, self::OTHER);

        $this->assertNull($result);
        $this->assertSame([], $this->repo->writes(), 'Sahiplik ihlalinde appendTrack çağrılmaz');
    }

    public function testAddTrackAppendsAndReturnsPosition(): void
    {
        $this->seedPlaylist();
        $this->repo->appendTrackResult = 5;

        $result = $this->service->addTrack(self::LIST, self::T1, self::OWNER);

        $this->assertSame(5, $result['position']);
        $this->assertSame([['appendTrack', self::LIST, self::T1]], $this->repo->writes());
    }

    // ─── removeTrack ───

    public function testRemoveTrackByNonOwnerReturnsFalseWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertFalse($this->service->removeTrack(self::LIST, self::T1, self::OTHER));
        $this->assertSame([], $this->repo->writes(), 'Sahiplik ihlalinde deleteTrack çağrılmaz');
    }

    public function testRemoveTrackDeletesForOwner(): void
    {
        $this->seedPlaylist();
        $this->repo->deleteTrackResult = true;

        $this->assertTrue($this->service->removeTrack(self::LIST, self::T1, self::OWNER));
        $this->assertSame([['deleteTrack', self::LIST, self::T1]], $this->repo->writes());
    }

    // ─── update (updateMeta) ───

    public function testUpdateByNonOwnerReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->update(self::LIST, ['name' => 'X'], self::OTHER));
        $this->assertSame([], $this->repo->writes(), 'Sahiplik ihlalinde updateMeta çağrılmaz');
    }

    public function testUpdateWithNoMutableFieldReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->update(self::LIST, [], self::OWNER));
        $this->assertSame([], $this->repo->writes(), 'Boş alan setinde updateMeta çağrılmaz');
    }

    public function testUpdateWritesOnlyProvidedTrimmedFields(): void
    {
        $this->seedPlaylist();
        $this->repo->findByIdResult = [
            'playlist_id' => self::LIST, 'owner_id_hex' => self::OWNER, 'name' => 'Yeni',
        ];

        $result = $this->service->update(self::LIST, ['name' => '  Yeni  '], self::OWNER);

        $this->assertSame([['updateMeta', self::LIST, ['name' => 'Yeni']]], $this->repo->writes());
        $this->assertSame('Yeni', $result['name']);
    }

    public function testUpdateAllowsExplicitNullDescription(): void
    {
        $this->seedPlaylist();
        $this->repo->findByIdResult = [
            'playlist_id' => self::LIST, 'owner_id_hex' => self::OWNER,
        ];

        $this->service->update(self::LIST, ['description' => null], self::OWNER);

        $this->assertSame(
            [['updateMeta', self::LIST, ['description' => null]]],
            $this->repo->writes(),
            'null description bilinçli olarak yazılır (alan temizleme)'
        );
    }

    // ─── delete (soft delete tx) ───

    public function testDeleteByNonOwnerReturnsFalseWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertFalse($this->service->delete(self::LIST, self::OTHER));
        $this->assertSame([], $this->repo->writes(), 'Sahiplik ihlalinde softDeleteWithTracks çağrılmaz');
    }

    public function testDeleteSoftDeletesPlaylistWithTracks(): void
    {
        $this->seedPlaylist();
        $this->repo->softDeleteResult = true;

        $this->assertTrue($this->service->delete(self::LIST, self::OWNER));
        $this->assertSame([['softDeleteWithTracks', self::LIST]], $this->repo->writes());
    }

    // ─── reorder (normalizasyon + kalıcı yazım) ───

    public function testReorderByNonOwnerReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->reorder(self::LIST, [self::T1], self::OTHER));
        $this->assertSame([], $this->repo->writes(), 'Sahiplik ihlalinde replacePositions çağrılmaz');
    }

    public function testReorderNormalizesUnknownDuplicateAndOmittedIds(): void
    {
        $this->seedPlaylist();
        $this->repo->trackIds[self::LIST] = [self::T1, self::T2, self::T3];

        // T3 öne, T1 tekrar ediyor, T9 bilinmeyen, T2 eksik (sona eklenmeli).
        $result = $this->service->reorder(self::LIST, [
            self::T3, self::T1, self::T1, '99999999999999999999999999999999',
        ], self::OWNER);

        $expected = [self::T3, self::T1, self::T2];
        $this->assertSame($expected, $result['order']);
        $this->assertSame([['replacePositions', self::LIST, $expected]], $this->repo->writes());
    }

    public function testReorderWithLowercaseNormalizationIsIdempotent(): void
    {
        $this->seedPlaylist();
        $this->repo->trackIds[self::LIST] = [self::T1, self::T2];

        $first = $this->service->reorder(self::LIST, [strtoupper(self::T2), strtoupper(self::T1)], self::OWNER);

        $expected = [self::T2, self::T1];
        $this->assertSame($expected, $first['order'], 'Büyük harf id küçük harfe normalleştirilir');
        $this->assertSame([['replacePositions', self::LIST, $expected]], $this->repo->writes());
    }

    public function testReorderWithEmptyRequestKeepsCurrentOrder(): void
    {
        $this->seedPlaylist();
        $this->repo->trackIds[self::LIST] = [self::T1, self::T2];

        $result = $this->service->reorder(self::LIST, [], self::OWNER);

        $this->assertSame([self::T1, self::T2], $result['order']);
        $this->assertSame([['replacePositions', self::LIST, [self::T1, self::T2]]], $this->repo->writes());
    }

    // ─── create ───

    public function testCreateTrimsNameAndPassesOwner(): void
    {
        $this->repo->insertResult = self::LIST;
        $this->seedPlaylist();

        $result = $this->service->create(self::OWNER, ['name' => '  Yeni Liste  ']);

        $this->assertSame(
            [['insert', ['user_id' => self::OWNER, 'name' => 'Yeni Liste', 'description' => null]]],
            $this->repo->writes()
        );
        $this->assertSame(self::LIST, $result['playlist_id']);
    }
}

/**
 * Fake IPlaylistRepository — her çağrıyı kaydeder; writes() yalnız
 * DEĞİŞTİRİCİ metodların [method, ...args] kaydını döner.
 *
 * @internal test doubles — yalnız bu dosyada kullanılır
 */
final class FakePlaylistRepository implements IPlaylistRepository
{
    /** @var array<string, array<string, mixed>> */
    public array $playlists = [];

    /** @var array<string, list<string>> */
    public array $trackIds = [];

    /** @var array<string, list<array<string, mixed>>> */
    public array $tracks = [];

    /** @var list<list<mixed>> tüm çağrılar [method, ...args] sırasıyla */
    private array $log = [];

    public ?string $insertResult = null;
    public int $appendTrackResult = 0;
    public bool $deleteTrackResult = false;
    public bool $softDeleteResult = false;
    public ?array $findByIdResult = null;

    /** @return list<string> çağrılan tüm metodlar (okuma + yazma, sırayla) */
    public function methods(): array
    {
        return array_map(static fn (array $call): string => (string) $call[0], $this->log);
    }

    /**
     * Yalnız DEĞİŞTİRİCİ metodların kayıtları.
     *
     * @return list<list<mixed>>
     */
    public function writes(): array
    {
        $writes = ['insert', 'appendTrack', 'deleteTrack', 'updateMeta', 'softDeleteWithTracks', 'replacePositions'];

        return array_values(array_filter(
            $this->log,
            static fn (array $call): bool => in_array($call[0], $writes, true)
        ));
    }

    /** @param list<mixed> $call */
    private function record(array $call): void
    {
        $this->log[] = $call;
    }

    public function findByUserId(string $userId): array
    {
        $this->record(['findByUserId', $userId]);

        return array_values(array_filter(
            $this->playlists,
            static fn (array $p): bool => ($p['owner_id_hex'] ?? '') === $userId
        ));
    }

    public function findById(string $playlistId): ?array
    {
        $this->record(['findById', $playlistId]);

        return $this->findByIdResult ?? $this->playlists[$playlistId] ?? null;
    }

    public function insert(array $data): string
    {
        $this->record(['insert', $data]);

        return $this->insertResult ?? 'new-id';
    }

    public function appendTrack(string $playlistId, string $trackId): int
    {
        $this->record(['appendTrack', $playlistId, $trackId]);

        return $this->appendTrackResult;
    }

    public function deleteTrack(string $playlistId, string $trackId): bool
    {
        $this->record(['deleteTrack', $playlistId, $trackId]);

        return $this->deleteTrackResult;
    }

    public function updateMeta(string $playlistId, array $fields): bool
    {
        $this->record(['updateMeta', $playlistId, $fields]);

        return true;
    }

    public function softDeleteWithTracks(string $playlistId): bool
    {
        $this->record(['softDeleteWithTracks', $playlistId]);

        return $this->softDeleteResult;
    }

    public function findTrackIds(string $playlistId): array
    {
        $this->record(['findTrackIds', $playlistId]);

        return $this->trackIds[$playlistId] ?? [];
    }

    public function replacePositions(string $playlistId, array $musicIds): bool
    {
        $this->record(['replacePositions', $playlistId, $musicIds]);

        return true;
    }

    public function findTracks(string $playlistId, int $limit): array
    {
        $this->record(['findTracks', $playlistId, $limit]);

        return $this->tracks[$playlistId] ?? [];
    }
}
