---
node_id: "K008-media-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "11 URL — OGG/Vorbis/FLAC/Opus + RTP akış (RFC 5215) (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 12, 38, 50, 51, 54, 59, 73, 82, 85, 86, 88"
validation: "2026-10-15 — 11 benzersiz URL; kök + kardeş çakışması 0 (C4 taraması)"
open_questions:
  - "CoreMusic'in gerçek codec envanteri şemada: `file_format enum('mp3','flac','wav','aac','ogg')` (`coremusic_media.sql` satır 57) — opus/vorbis ayrı format mı → UNKNOWN"
group: kanit
---

# media — Web Araştırması (K008)

> **Authority: Bayram Ali / Vault Steward.** 11 URL **yalnız bu dosyada** (C4).
> Doğrulama: **2026-10-15 (HTTP GET, hepsi 200)**; biçim havuzdaki gibi korunmuştur.

## §1 YENİ TARAMA (M-1…M-11 · 2026-10-15)

| # | URL | Ne için | Durum |
|---|-----|---------|-------|
| M-1 | https://datatracker.ietf.org/doc/html/rfc3533 | OGG kapsayıcı standardı (RFC) | HTTP 200 |
| M-2 | https://en.wikipedia.org/wiki/flac | Kayıpsız codec — format sözlüğü | HTTP 200 |
| M-3 | https://en.wikipedia.org/wiki/ogg | OGG kapsayıcı — format sözlüğü | HTTP 200 |
| M-4 | https://en.wikipedia.org/wiki/opus_(audio_format) | Opus codec — format sözlüğü | HTTP 200 |
| M-5 | https://en.wikipedia.org/wiki/sample-rate_conversion | Örnekleme hızı dönüşümü — transcode terimi | HTTP 200 |
| M-6 | https://en.wikipedia.org/wiki/vorbis | Vorbis codec — format sözlüğü | HTTP 200 |
| M-7 | https://wiki.xiph.org/oggvorbis | Xiph Ogg/Vorbis wiki | HTTP 200 |
| M-8 | https://www.rfc-editor.org/info/rfc5215 | Ogg Vorbis RTP payload formatı (akış) | HTTP 200 |
| M-9 | https://www.xiph.org/vorbis/doc | Xiph Vorbis dokümanları kökü | HTTP 200 |
| M-10 | https://www.xiph.org/vorbis/doc/vorbis_ı_spec.html | Vorbis I spesifikasyonu | HTTP 200 |
| M-11 | https://xiph.org/vorbis/doc/oggstream.html | Ogg akış yapısı (bitstream) | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Kontrol | Sonuç |
|---|---|
| Benzersiz URL | 11 (M-1…M-11) |
| Kök havuz (75) + diğer çocuk havuzları (80) ile kesişim | 0 (2026-10-15 `POOL-COLLISION: 0`) |

## §3 KAPSAM DIŞI

| Dışarıda | Nereye |
|---|---|
| HLS yayın akışı (rfc8216) | [[playlist-web-arastirma]] (M-…→P- serisi) |
| Loudness/normalizasyon | [[playback-web-arastirma]] |
| Transcode iş akışı / cihaz senkronu | `media-kapsam.md` §2 (sınır), kod kanıtı `media-dogrulama.md` |

**Kardeş dosyalar:** [[media-kapsam]] · [[media-dogrulama]] · [[media-ornekler]] · [[media-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
