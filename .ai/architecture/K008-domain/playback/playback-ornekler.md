---
node_id: "K008-playback-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-playback-kapsam"
evidence:
  - ".ai/.sql/mysql/coremusic_user.sql satır 18–31 · 110–114"
  - "assets.coremusic.net/js/coreplayer/coreplayer.controls.js satır 7–38 (2. tur disk okuması)"
  - "shared/src/Events/Domain/MusicPlayedEvent.php satır 20–40 (2. tur disk okuması)"
validation: "2026-10-15 — 4 örnek; 2. tur disk okumasıyla 3 uyuşmazlık düzeltildi (COOKIE_DAYS 365 · eventName() method · ENGINE comment)"
open_questions:
  - "Oynatma PHP servisi/controller örneği → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# playback — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Dört örnek de disk'ten birebir kopyalanmıştır. Bu dosya 2. tur disk okumasıyla
> yeniden yazıldı — ilk taslakta üç uyuşmazlık bulunup düzeltildi (kayıt: §5).

## §1 ÖRNEK 1 — SQL: playback_queue (kuyruk · satır 18–31)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_user.sql · satır 18-33
CREATE TABLE `playback_queue` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
  `position` int NOT NULL COMMENT 'Queue position (0-based)',
  `source` enum('user','ai','radio','podcast') COLLATE utf8mb4_unicode_ci DEFAULT 'user' COMMENT 'Queue source type',
  `source_id` binary(16) DEFAULT NULL COMMENT 'Source entity ID (playlist, radio station, etc.)',
  `is_playing` tinyint(1) DEFAULT '0' COMMENT 'Currently playing flag',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_queue_user_position` (`user_id`,`position`) COMMENT 'User queue ordered retrieval',
  KEY `idx_queue_music` (`music_id`) COMMENT 'Track queue lookup',
  CONSTRAINT `fk_queue_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_queue_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Playback queue — per-user track queue with ordering';
```

**Ne kanıtlıyor:** kuyruk bir **sıradır** (satır 22 — 0-tabanlı `position`), kaynağı
bellidir (satır 23 — `user/ai/radio/podcast`) ve anlık durum (`is_playing`, satır 25)
tablonun bir parçasıdır; tablo comment'i de "per-user track queue with ordering" der
(satır 33).

## §2 ÖRNEK 2 — JS: PlaybackRepository (frontend durumu · satır 7–38)

```js
// Dosya: assets.coremusic.net/js/coreplayer/coreplayer.controls.js · satır 7-38
const PlaybackRepository = {
    _mediaElement: null,

    setMediaElement(el) {
      this._mediaElement = el;
    },

    getCookie(name) {
      return window.CorePlayerShared.getCookie(name);
    },

    setCookie(name, value, days) {
      window.CorePlayerShared.setCookie(name, value, days);
    },

    COOKIE_DAYS: 365,

    getPlayingState() {
      return this.getCookie('MM_PlayingState');
    },

    savePlayingState(val) {
      this.setCookie('MM_PlayingState', val, this.COOKIE_DAYS);
    },

    getCurrentSongId() {
      return this.getCookie('MM_CurrentSongID');
    },

    getCurrentSongType() {
      return this.getCookie('MM_CurrentSongType');
    },
```

**Ne kanıtlıyor:** anlık oynatma durumu üç cookie'dedir — okuma satır 24–25
(`MM_PlayingState`), satır 32–33 (`MM_CurrentSongID`), satır 36–37
(`MM_CurrentSongType`); yazma satır 28–29. Cookie helper'ları dosya içi değil,
`window.CorePlayerShared`'e delege edilir (satır 14–20) — çekirdek paylaşılmış
modüldür. Ömür `COOKIE_DAYS: 365` (satır 22).

## §3 ÖRNEK 3 — PHP: MusicPlayedEvent (domain event · satır 20–40)

```php
<?php
// Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · satır 20-40
final class MusicPlayedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $musicId,
        private readonly string $userId,
        private readonly int $duration
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'music.played';
    }
```

**Ne kanıtlıyor:** olay sözleşmesi üç alanla sınırlıdır (ctor satır 26–32: musicId ·
userId · duration + `occurredOn`), adı `eventName()` **methoduyla** döner (satır
37–40) — sabit `const` DEĞİLDİR. `DomainEventInterface` + `StoppableEventTrait`
kullanır (satır 20, 22).

## §4 ÖRNEK 4 — SQL: user_preferences (oynatma tercihleri · satır 110–114)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_user.sql · satır 110-114 (kolon kesiti)
`auto_play` tinyint(1) DEFAULT '1' COMMENT 'Auto-play next track',                    -- satır 110
`crossfade_duration` int DEFAULT '0' COMMENT 'Crossfade duration in seconds',         -- satır 111
`normalize_audio` tinyint(1) DEFAULT '1' COMMENT 'Audio normalization enabled',       -- satır 112
`explicit_content` tinyint(1) DEFAULT '1' COMMENT 'Allow explicit content',           -- satır 113
`autoplay_recommendations` tinyint(1) DEFAULT '1' COMMENT 'Auto-play AI recommendations', -- satır 114
```

**Ne kanıtlıyor:** crossfade ve ses eşitleme **backend tercihidir** (kullanıcı
düzeyinde) — bunları okuyan oynatma kodu henüz kanıtlanmamıştır.

## §5 DÜZELTME KAYDI (2026-10-15, 2. tur disk okuması)

| # | İlk taslak iddiası | Disk gerçeği | Durum |
|---|---|---|---|
| 1 | `COOKIE_DAYS: 3` / D-PL9'da "22 gün" | `COOKIE_DAYS: 365` (satır 22; 22 = satır no) | ✅ düzeltildi |
| 2 | `public const NAME = 'music.played'` | `eventName()` methodu (satır 37–40) | ✅ düzeltildi |
| 3 | ENGINE comment `AI-powered playback queue…` | `Playback queue — per-user track queue with ordering` (satır 33) | ✅ düzeltildi |

**Kardeş dosyalar:** [[playback-kapsam]] · [[playback-dogrulama]] · [[playback-web-arastirma]] · [[playback-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
