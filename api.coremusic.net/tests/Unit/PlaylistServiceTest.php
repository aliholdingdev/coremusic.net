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
        $this->assertSame([], $this->repo->calls, 'Gizli liste için track okunmamalı');
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
        $this->assertSame([], $this->repo->calls, 'Sahiplik ihlalinde appendTrack çağrılmaz');
    }

    public function testAddTrackAppendsAndReturnsPosition(): void
    {
        $this->seedPlaylist();
        $this->repo->appendTrackResult = 5;

        $result = $this->service->addTrack(self::LIST, self::T1, self::OWNER);

        $this->assertSame(5, $result['position']);
        $this->assertSame(['appendTrack', self::LIST, self::T1], $this->repo->calls);
    }

    // ─── removeTrack ───

    public function testRemoveTrackByNonOwnerReturnsFalseWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertFalse($this->service->removeTrack(self::LIST, self::T1, self::OTHER));
        $this->assertSame([], $this->repo->calls);
    }

    public function testRemoveTrackDeletesForOwner(): void
    {
        $this->seedPlaylist();
        $this->repo->deleteTrackResult = true;

        $this->assertTrue($this->service->removeTrack(self::LIST, self::T1, self::OWNER));
        $this->assertSame(['deleteTrack', self::LIST, self::T1], $this->repo->calls);
    }

    // ─── update (updateMeta) ───

    public function testUpdateByNonOwnerReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->update(self::LIST, ['name' => 'X'], self::OTHER));
        $this->assertSame([], $this->repo->calls);
    }

    public function testUpdateWithNoMutableFieldReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->update(self::LIST, [], self::OWNER));
        $this->assertSame([], $this->repo->calls, 'Boş alan setinde updateMeta çağrılmaz');
    }

    public function testUpdateWritesOnlyProvidedTrimmedFields(): void
    {
        $this->seedPlaylist();
        $this->repo->findByIdResult = ['playlist_id' => self::LIST, 'name' => '  Yeni  '];

        $result = $this->service->update(self::LIST, ['name' => '  Yeni  '], self::OWNER);

        $this->assertSame(['updateMeta', self::LIST, ['name' => 'Yeni']], $this->repo->calls);
        $this->assertSame('Yeni', $result['name']);
    }

    public function testUpdateAllowsExplicitNullDescription(): void
    {
        $this->seedPlaylist();
        $this->repo->findByIdResult = ['playlist_id' => self::LIST];

        $this->service->update(self::LIST, ['description' => null], self::OWNER);

        $this->assertSame(['updateMeta', self::LIST, ['description' => null]], $this->repo->calls);
    }

    // ─── delete (soft delete tx) ───

    public function testDeleteByNonOwnerReturnsFalseWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertFalse($this->service->delete(self::LIST, self::OTHER));
        $this->assertSame([], $this->repo->calls);
    }

    public function testDeleteSoftDeletesPlaylistWithTracks(): void
    {
        $this->seedPlaylist();
        $this->repo->softDeleteResult = true;

        $this->assertTrue($this->service->delete(self::LIST, self::OWNER));
        $this->assertSame(['softDeleteWithTracks', self::LIST], $this->repo->calls);
    }

    // ─── reorder (normalizasyon + kalıcı yazım) ───

    public function testReorderByNonOwnerReturnsNullWithoutWrite(): void
    {
        $this->seedPlaylist();

        $this->assertNull($this->service->reorder(self::LIST, [self::T1], self::OTHER));
        $this->assertSame([], $this->repo->calls);
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
        $this->assertSame(['replacePositions', self::LIST, $expected], $this->repo->calls);
    }

    public function testReorderWithLowercaseNormalizationIsIdempotent(): void
    {
        $this->seedPlaylist();
        $upperT1  = strtoupper(self::T1);
        $this->repo->trackIds[self::LIST] = [self::T1, self::T2];

        $first = $this->service->reorder(self::LIST, [strtoupper(self::T2), $upperT1], self::OWNER);

        $expected = [self::T2, self::T1];
        $this->assertSame($expected, $first['order'], 'Büyük harf id küçük harfe normalleştirilir');
        $this->assertSame(['replacePositions', self::LIST, $expected], $this->repo->calls);
    }

    public function testReorderWithEmptyRequestKeepsCurrentOrder(): void
    {
        $this->seedPlaylist();
        $this->repo->trackIds[self::LIST] = [self::T1, self::T2];

        $result = $this->service->reorder(self::LIST, [], self::OWNER);

        $this->assertSame([self::T1, self::T2], $result['order']);
        $this->assertSame(['replacePositions', self::LIST, [self::T1, self::T2]], $this->repo->calls);
    }

    // ─── create ───

    public function testCreateTrimsNameAndPassesOwner(): void
    {
        $this->repo->insertResult = self::LIST;
        $this->seedPlaylist();

        $result = $this->service->create(self::OWNER, ['name' => '  Yeni Liste  ']);

        $this->assertSame(
            ['insert', ['user_id' => self::OWNER, 'name' => 'Yeni Liste', 'description' => null]],
            $this->repo->calls
        );
        $this->assertSame(self::LIST, $result['playlist_id']);
    }
}

/**
 * Fake IPlaylistRepository — her çağrıyı tek satır olarak kaydeder.
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

    /** @var list<mixed> son çağrının [method, ...args] kaydı */
    public array $calls = [];

    public ?string $insertResult = null;
    public int $appendTrackResult = 0;
    public bool $deleteTrackResult = false;
    public bool $softDeleteResult = false;
    public ?array $findByIdResult = null;

    public function findByUserId(string $userId): array
    {
        $this->calls = ['findByUserId', $userId];

        return array_values(array_filter(
            $this->playlists,
            static fn (array $p): bool => ($p['owner_id_hex'] ?? '') === $userId
        ));
    }

    public function findById(string $playlistId): ?array
    {
        $this->calls = ['findById', $playlistId];

        return $this->findByIdResult ?? $this->playlists[$playlistId] ?? null;
    }

    public function insert(array $data): string
    {
        $this->calls = ['insert', $data];

        return $this->insertResult ?? 'new-id';
    }

    public function appendTrack(string $playlistId, string $trackId): int
    {
        $this->calls = ['appendTrack', $playlistId, $trackId];

        return $this->appendTrackResult;
    }

    public function deleteTrack(string $playlistId, string $trackId): bool
    {
        $this->calls = ['deleteTrack', $playlistId, $trackId];

        return $this->deleteTrackResult;
    }

    public function updateMeta(string $playlistId, array $fields): bool
    {
        $this->calls = ['updateMeta', $playlistId, $fields];

        return true;
    }

    public function softDeleteWithTracks(string $playlistId): bool
    {
        $this->calls = ['softDeleteWithTracks', $playlistId];

        return $this->softDeleteResult;
    }

    public function findTrackIds(string $playlistId): array
    {
        $this->calls = ['findTrackIds', $playlistId];

        return $this->trackIds[$playlistId] ?? [];
    }

    public function replacePositions(string $playlistId, array $musicIds): bool
    {
        $this->calls = ['replacePositions', $playlistId, $musicIds];

        return true;
    }

    public function findTracks(string $playlistId, int $limit): array
    {
        $this->calls = ['findTracks', $playlistId, $limit];

        return $this->tracks[$playlistId] ?? [];
    }
}
