---
node_id: "K008-playback-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 92 — Oynatma | playback | PlaybackSession | MusicPlayedEvent / PlaybackQueueUpdatedEvent"
  - ".ai/.sql/mysql/coremusic_user.sql satır 18–31 (playback_queue) · 104–114 (user_preferences oynatma tercihleri)"
  - "shared/src/Events/Domain/MusicPlayedEvent.php satır 20 · 26–30 · 39"
  - "assets.coremusic.net/js/coreplayer/coreplayer.controls.js satır 7–38 (PlaybackRepository)"
validation: "2026-10-15 — kapsam sınırı SQL + JS + master satır kanıtlarıyla çizildi"
open_questions:
  - "playback Bounded Context / PlaybackSession aggregate → ADR-097 K4'te kilitlenmedi → taslak"
  - "MusicPlayedEvent yayıncısı (dispatch eden kod) → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
  - "PlaybackQueueUpdatedEvent → diskte dosya YOK (bkz [[playback-dogrulama]] D-PL11) → SINIR AÇIK"
group: mimari
---

# playback — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `playback`, **oynatma anı**nın durumudur: sıra (kuyruk), çal/duraklat, pozisyon,
> çalan şarkının kimliği. Şarkının kendisi `music`'in, ne kadar dinlendiği `library`'nin,
> hedef cihaz `device-management`'in işidir.

## §1 ROL (ELI15)

Kullanıcı bir şarkıyı başlatınca sistem üç şeyi birden tutar: **sıradaki ne** (kuyruk),
**şu an ne çalıyor** (aktif durum) ve **nereye çalıyor** (cihaz — komşu bağlam).
Master §3.1 satır 92 bu bağlamı `PlaybackSession` aggregate'i ile adlandırır
(⚠️ tasarım iddiası — ADR-097 K4). Frontend bunu `MM_*` çerez değişkenleriyle
(`coreplayer.controls.js` satır 7–38) tutar; backend `playback_queue` tablosuyla
(satır 18–31). Çalma tercihleri (otomatik çal, crossfade, ses eşitleme)
`user_preferences` satır 110–114'tedir.

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Kuyruk durumu: sıra, kaynak, çalıyor bayrağı (`playback_queue` satır 22–25) | Dinleme geçmişi: ne kadar dinlendi (süre/yüzde) | `library` |
| 2 | Oynatma tercihleri: auto_play · crossfade · normalize (`user_preferences` satır 110–112) | Kullanıcı kimliği / profil | `user` |
| 3 | Çalan şarkının kimliği + anlık durum (`MM_CurrentSongID` / `MM_PlayingState`) | Şarkının kendisi (tanım, katalog) | `music` |
| 4 | HTML5 oynatma çekirdeği (controls/seekbar/volume JS) | Ses dosyası / transcode / bitrate | `media` |
| 5 | Çalma hedefinin UI seçimi (anlık) | Cihaz keşfi, eşleştirme, senkron | `device-management` |
| 6 | — | **Kuyruk domain event'i** (`PlaybackQueueUpdatedEvent` diskte YOK) | SINIR AÇIK — master §7 satır 216–217 |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Oynatma (`playback`) | ✅ domain-master §3.1 satır 92 |
| 2 | Aggregate (aday) | PlaybackSession | ⚠️ TASARIM İDDİASI — master satır 92; ADR-097 K4 |
| 3 | Entity / kayıt | `playback_queue` satırı (user_id + music_id + position) | ✅ coremusic_user.sql satır 18–31 |
| 4 | Value Object (aday) | `source` enum'u: `user / ai / radio / podcast` | ✅ satır 23 |
| 5 | Ubiquitous language | kuyruk = `playback_queue` · oynatma durumu = `MM_PlayingState` · çalan = `MM_CurrentSongID` | ✅ şema + JS satır 25/29/33 |
| 6 | Domain event | `MusicPlayedEvent` (`music.played`) | ✅ dosya var; **yayıncısı → ⚠️ VERIFICATION REQUIRED** |
| 7 | Invariant | tanımsız | ⚠️ ADR-097 K4 |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/.sql/mysql/coremusic_user.sql` | 18–31 | `playback_queue` — position (0-tabanlı), source enum, is_playing |
| `.ai/.sql/mysql/coremusic_user.sql` | 104–114 | `user_preferences` — auto_play · crossfade_duration · normalize_audio · autoplay_recommendations |
| `shared/src/Events/Domain/MusicPlayedEvent.php` | 20 · 26–30 · 39 | sınıf · ctor (musicId/userId/duration) · eventName `music.played` |
| `assets.coremusic.net/js/coreplayer/coreplayer.controls.js` | 7–38 | PlaybackRepository — cookie tabanlı oynatma durumu (ömrü 365 gün, satır 22) |
| `.ai/architecture/K008-domain/domain-master.md` | 92 | PlaybackSession + MusicPlayedEvent / PlaybackQueueUpdatedEvent eşlemesi |

**Kardeş dosyalar:** [[playback-dogrulama]] · [[playback-web-arastirma]] · [[playback-ornekler]] · [[playback-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
