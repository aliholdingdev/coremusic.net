---
node_id: "K008-playback-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-playback-kapsam"
  - "K008-playback-ornekler"
evidence:
  - ".ai/.sql/mysql/coremusic_user.sql satır 18–31 (playback_queue) · 110–114 (tercihler)"
  - "assets.coremusic.net/js/coreplayer/coreplayer.controls.js — dizinde tam 5 JS dosyası"
  - "shared/src/Events/Domain/MusicPlayedEvent.php (var) · PlaybackQueueUpdatedEvent (YOK — master §7)"
validation: "2026-10-15 — §1 mevcut kademe (4+1 satır) + §2 altı PLANNED satır"
open_questions:
  - "PlaybackSession aggregate + invariant → ADR-097 K4"
  - "Oynatma PHP servisi → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
  - "MusicPlayedEvent yayıncısı → ⚠️ VERIFICATION REQUIRED (D-PL14)"
group: mimari
---

# playback — Bileşenler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> §1 mevcut olanı, §2 ise **PLANNED** kademe-3 planını taşır. Disk'te karşılığı
> olmayan sınıf/dosya adı "var" gibi sunulmaz.

## §1 MEVCUT KADEME

| # | Bileşen | Kanıt | Durum |
|---|---|---|---|
| 1 | `playback_queue` (kuyruk şeması) | `.ai/.sql/mysql/coremusic_user.sql` satır 18–31 | ✅ DISKTE |
| 2 | `user_preferences` oynatma tercihleri | aynı dosya satır 110–114 | ✅ DISKTE |
| 3 | `MusicPlayedEvent` (domain event) | `shared/src/Events/Domain/MusicPlayedEvent.php` satır 20–40 | ✅ DISKTE (yayıncı → ⚠️ D-PL14) |
| 4 | coreplayer çekirdeği — 5 JS modülü (controls · seekbar · progressbar · volume · shared) | `assets.coremusic.net/js/coreplayer/*.js` = 5 (2026-10-15 glob) | ✅ DISKTE (frontend) |
| 5 | Oynatma PHP servisi / controller | — | ⚠️ VERIFICATION REQUIRED (tarama yok) |

## §2 KADEME-3 PLANI (PLANNED — ADR-097 K4 bekliyor)

| # | Bileşen (aday) | Sorumluluk | Dayanak | Durum |
|---|---|---|---|---|
| 1 | PlaybackSession (aggregate — ad master satır 92) | oturum bazlı çalma durumu + sıra | [[playback-kapsam]] §3 satır 2 | ⚠️ PLANNED |
| 2 | Kuyruk servisi (Repository — ad ADR-097 K4) | sıra ekleme/çıkarma/sıralama, `source` kaynak üretimi | `playback_queue` satır 18–31 | ⚠️ PLANNED — kod kanıtı yok |
| 3 | Durum senkronizasyonu | frontend cookie (`MM_*`) ↔ backend kuyruk | [[playback-ornekler]] §2 + D-PL9 | ⚠️ PLANNED — iki yön de kanıtsız |
| 4 | Tercih uygulayıcısı | crossfade/normalize/auto_play'i oynatma motoruna aktarma | satır 110–114 | ⚠️ PLANNED — okuyan kod yok |
| 5 | Kuyruk domain event'i (adı diskte YOK) | sıra değişimini yayınlama | master §7 (PlaybackQueueUpdatedEvent "not on disk") | ⚠️ PLANNED — olay adı uydurulmaz |
| 6 | Media Session entegrasyonu | lock ekranı / sistem kontrolleri | [[playback-web-arastirma]] §1 W-3, W-10 (pointer) | ⚠️ PLANNED — kod kanıtı yok |

**Yasak:** §2 adları **tasarım adayıdır**; disk'te karşılığı doğrulanmadan "mevcut"
gibi raporlanmaz. Geçiş: kod diske yazıldı + ADR-097 K4 kilitlendi → `active`.

**Kardeş dosyalar:** [[playback-kapsam]] · [[playback-dogrulama]] · [[playback-web-arastirma]] · [[playback-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
