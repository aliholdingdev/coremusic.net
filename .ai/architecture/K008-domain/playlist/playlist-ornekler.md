---
node_id: "K008-playlist-ornekler"
node_type: "kod"
status: "active"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-playlist-kapsam"
evidence:
  - ".ai/.sql/mysql/coremusic_playlist.sql"
  - ".ai/.sql/mysql/coremusic_user.sql"
  - "shared/src/Api/Dto/Request/PlaylistAddTrackRequest.php"
  - "shared/src/Events/Domain/PlaylistCreatedEvent.php"
validation: "2026-10-15 — 4 örnek, tümü disk kanıtlı (SQL ×2 · PHP ×2; satır aralıkları birebir okundu)"
open_questions: ["OQ-P1: playlistController implementasyonu bulunamadı (route kaydı var) → ⚠️ VERIFICATION REQUIRED"]
group: kod
---

# playlist — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15

## §1 ÖRNEK 1 — SQL: playlists + playlist_tracks (çekirdek şema)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_playlist.sql · satır 84-107
CREATE TABLE `playlists` (
  `id` binary(16) NOT NULL,                  -- UUID v7
  `user_id` binary(16) NOT NULL,             -- sahipsiz liste yok (D-P2)
  `name` varchar(200) NOT NULL,              -- benzersiz DEĞİL (D-P10)
  `description` text,
  `cover_art_url` varchar(500) DEFAULT NULL,
  `visibility` enum('public','private','unlisted') NOT NULL DEFAULT 'public',  -- varsayılan açık (D-P3)
  `playlist_type` enum('user','ai','auto_generated','radio','curated') NOT NULL DEFAULT 'user',  -- (D-P5)
  `is_editable` tinyint(1) NOT NULL DEFAULT '1',
  `total_tracks` int NOT NULL DEFAULT '0',   -- önbellek sayaç (D-P6)
  `total_duration_sec` int NOT NULL DEFAULT '0',
  `follow_count` int NOT NULL DEFAULT '0',
  `like_count` int NOT NULL DEFAULT '0',
  `play_count` bigint NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',   -- yumuşak silme (D-P9)
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_playlists_user` (`user_id`),      -- satır 103
  FULLTEXT KEY `idx_playlists_fulltext` (`name`,`description`)  -- satır 106
)
-- satır 67-82 playlist_tracks:
CREATE TABLE `playlist_tracks` (
  `id` binary(16) NOT NULL,
  `playlist_id` binary(16) NOT NULL,
  `music_id` binary(16) NOT NULL,            -- medya parçası referansı (satır 70)
  `position` int NOT NULL,                   -- sıra (satır 71 — D-P4)
  `added_by` binary(16) DEFAULT NULL,        -- ekleyen nullable
  `added_at` timestamp NOT NULL DEFAULT current_timestamp,
  UNIQUE KEY (`playlist_id`,`music_id`,`position`)  -- satır 78
)
```

## §2 ÖRNEK 2 — SQL: user_favorites + user_downloads (likedList kümeleri)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_user.sql · satır 59-69 user_favorites
CREATE TABLE `user_favorites` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL,               -- FK coremusic_auth.users.id
  `item_type` enum('music','album','playlist','artist','podcast') NOT NULL,  -- satır 62
  `item_id` binary(16) NOT NULL,               -- polymorphic hedef (satır 63)
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_favorites_unique` (`user_id`,`item_type`,`item_id`)        -- satır 66
);
-- satır 35-57 user_downloads
CREATE TABLE `user_downloads` (
  `id` binary(16) NOT NULL, `user_id` binary(16) NOT NULL, `music_id` binary(16) NOT NULL,
  `download_status` enum('pending','downloading','completed','failed','expired')
      DEFAULT 'pending',                       -- satır 39
  `file_path` varchar(500) DEFAULT NULL,       -- satır 40
  `file_size` bigint DEFAULT NULL,             -- satır 41
  `file_format` enum('mp3','flac','wav','aac','ogg') DEFAULT 'flac',   -- satır 42
  `quality` enum('128','192','320','lossless') DEFAULT 'lossless',     -- satır 43
  `downloaded_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,    -- çevrimdışı erişim süresi
  `is_deleted` tinyint(1) DEFAULT '0',
  UNIQUE KEY `idx_downloads_unique` (`user_id`,`music_id`)             -- satır 51
)
```

## §3 ÖRNEK 3 — PHP: AddTrack isteği (position alanı — API sıralaması)

```php
// Dosya: shared/src/Api/Dto/Request/PlaylistAddTrackRequest.php · satır 17-47
final class PlaylistAddTrackRequest
{
    public function __construct(
        private readonly string $playlistId,
        private readonly string $musicId,
        private readonly int $position = -1     // satır 22: -1 = sona ekle
    ) {}

    public function getPosition(): int          // satır 44-47
    {
        return $this->position;
    }
}
```

Not: İstemcide playlist sırası modeli (`playlist-order.js` vb.) **repoda yok** — sıralama
yalnızca SQL (`playlist_tracks.position`) ve API DTO'su (`position`) kanıtlıdır (D-P11).

## §4 ÖRNEK 4 — PHP: PlaylistCreatedEvent (context-mapping çıkışı)

```php
// Dosya: shared/src/Events/Domain/PlaylistCreatedEvent.php · satır 20-61
final class PlaylistCreatedEvent implements DomainEventInterface   // satır 20
{
    private readonly \DateTimeImmutable $occurredOn;               // satır 24

    public function __construct(                                   // satır 26-32
        private readonly string $playlistId,
        private readonly string $createdBy,
        private readonly string $name
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function eventName(): string                            // satır 37-40
    {
        return 'playlist.created';
    }

    public function payload(): array                               // satır 53-61
    {
        return [
            'playlistId' => $this->playlistId,
            'createdBy'  => $this->createdBy,
            'name'       => $this->name,
            'timestamp'  => $this->occurredOn->format('c'),
        ];
    }
}
```

**Kardeş dosyalar:** [[index]] · [[playlist-kapsam]] · [[playlist-dogrulama]] · [[playlist-web-arastirma]] · [[playlist-bilesenler]]

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
