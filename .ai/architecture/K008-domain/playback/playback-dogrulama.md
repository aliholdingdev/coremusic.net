---
node_id: "K008-playback-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-playback-kapsam"
  - "K008-playback-web-arastirma"
evidence:
  - "satır D-PL1…D-PL14 — 2026-10-15 kaydı"
  - "repo kanıtları: coremusic_user.sql 18–31/104–114 · MusicPlayedEvent.php 20–39 · coreplayer.controls.js 7–38"
validation: "2026-10-15 — 14 iddia: 11 Dosya/Satır, 2 web pointer (§1), 1 negatif"
open_questions:
  - "MusicPlayedEvent yayıncısı → ⚠️ VERIFICATION REQUIRED (tarama yapılmadı)"
  - "PlaybackQueueUpdatedEvent → dosya YOK (D-PL11) → SINIR AÇIK"
group: kanit
---

# playback — Doğrulama Defteri (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Web kanıtları URL ile değil [[playback-web-arastirma]] §1 pointer'ı ile verilir (C4).
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN**.

## §1 İDDİA TABLOSU

| # | İddia | Repo kanıtı (Dosya/Satır) | Web kanıtı | Tür | Durum |
|---|-------|---------------------------|-----------|-----|-------|
| D-PL1 | `playback_queue` tablosu şemada var | `.ai/.sql/mysql/coremusic_user.sql` satır 18–31 | — | T4 | ✅ |
| D-PL2 | kuyruk pozisyonu 0-tabanlı `int` | aynı dosya satır 22 (`position` — "0-based") | — | T4 | ✅ |
| D-PL3 | kuyruk kaynağı enum'u `user/ai/radio/podcast` | aynı dosya satır 23 | — | T4 | ✅ |
| D-PL4 | "şu an çalıyor" bayrağı şemada | aynı dosya satır 25 (`is_playing`) | — | T4 | ✅ |
| D-PL5 | kuyruk sıralı erişim indeksi var | aynı dosya satır 29 (`idx_queue_user_position`) | — | T4 | ✅ |
| D-PL6 | oynatma tercihleri şemada (auto_play · crossfade · normalize · explicit · autoplay_rec) | aynı dosya satır 110–114 | — | T4 | ✅ |
| D-PL7 | `MusicPlayedEvent` sınıfı diskte; ctor (musicId/userId/duration) | `shared/src/Events/Domain/MusicPlayedEvent.php` satır 20 · 26–30 | — | T4 | ✅ |
| D-PL8 | eventName `music.played` | aynı dosya satır 39 | — | T4 | ✅ |
| D-PL9 | frontend oynatma durumu cookie tabanlı (MM_PlayingState / MM_CurrentSongID / MM_CurrentSongType; cookie ömrü 365 gün) | `assets.coremusic.net/js/coreplayer/coreplayer.controls.js` satır 7–38 — `COOKIE_DAYS: 365` (satır 22) | — | T4 | ✅ |
| D-PL10 | coreplayer çekirdeği tam 5 dosya (controls · seekbar · progressbar · volume · shared) | dizin glob `coreplayer/*.js` = 5 (2026-10-15) | — | T4 | ✅ |
| D-PL11 | `PlaybackQueueUpdatedEvent` diskte **YOK** (master §3.1 satır 92'de adı geçiyor) | domain-master.md §7 negatif kaydı (master'ın kendi "not on disk" tespiti) + `shared/src/Events/Domain/` envanteri (9 dosya) | — | negatif | ✅ NEGATİF — SINIR AÇIK |
| D-PL12 | HTMLMediaElement / Media Session oynatma yüzeyi referansı | — | [[playback-web-arastirma]] §1 W-2, W-3, W-10 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-PL13 | loudness/eşitleme kavramı (`normalize_audio` gerekçesi) | — | aynı dosya §1 W-4, W-8 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-PL14 | `MusicPlayedEvent`'i **yayınlayan** kod (dispatch) | tarama yapılmadı | — | — | ⚠️ VERIFICATION REQUIRED |

## §2 NOTLAR

- D-PL11 bir **negatif** kanıttır: olay adı master'da geçiyor ama `shared/src/Events/Domain/`
  altında dosyası yok — olay sözleşmesi gerçek değil, kuyruk entegrasyonu açık.
- D-PL9 ile D-PL6 arasındaki boşluk: tercihler backend'de (şema), anlık durum frontend'de
  (cookie) — ikisi arasında tek yönlü senkronizasyon kanıtı yok → ⚠️ VERIFICATION REQUIRED.

**Kardeş dosyalar:** [[playback-kapsam]] · [[playback-web-arastirma]] · [[playback-ornekler]] · [[playback-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
