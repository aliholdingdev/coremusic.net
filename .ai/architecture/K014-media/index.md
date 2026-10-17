---
node_id: K014-media
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - library
  - metadata
  - artwork
  - catalog
  - playback
  - streaming
  - downloads
  - offline
  - media-cache
  - synchronization
  - media-testing
depends_on:
  - K003-audio
  - K007-data
  - K013-integraion
  - K010-network
evidence:
  - "v2 §2.15 K014-media/ — çocuk listesi kaynağı (11 çocuk, birebir)"
  - ".ai/architecture/index.md §5/15 — media-master.md adı kaydı"
  - "https://github.com/jellyfin/jellyfin — Jellyfin · 55732★ · GPL-2.0 · .NET · Emby 3.5.2 fork (2018-12-09) · 2026-10-12"
validation:
  - "Çocuk adları v2 §2.15 ile birebir karşılaştırıldı (11/11) — 2026-10-12"
  - "Web kanıtları media-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "playback (K015) ile K012/playback-service sahipliği → ⚠️ VERIFICATION REQUIRED"
  - "synchronization (K015) ile K013/synchronization ve K014/offline-sync sınırı → ⚠️ VERIFICATION REQUIRED"
  - "Referans ürünler (Plexamp · Finamp · Navidrome) kapsam kararı etkiler mi → onay bekliyor"
  - "media-testing ile K019-testing kapsam sınırı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---


# K014-media — Şarkı Dosyaları Dünyası

## 1. ELI15 — bu oda ne işe yarar?

v2 §2.15: *"Şarkı dosyaları dünyası — kapak resimleri, akış, indirme, çevrimdışı."*
Müziğin **kendisini ve ona dair her şeyi** taşır: kitaplık (library), bilgi (metadata),
kapak (artwork), dizin (catalog), çalma (playback · streaming), indirme ve çevrimdışı
(downloads · offline), önbellek (media-cache) ve eşitleme (synchronization).
Ses sinyalinin **işlenmesi** K003-audio/K004-dsp'dir; burası dosya ve akıştır.

## 2. Çocuk kataloğu (kademe-2 — v2 §2.15 ile birebir)

> ELI15 sütunu **TASARIM İDDİASI**'dır (isimden türetildi, kanıt değildir).

| # | Çocuk | ELI15 rol (tasarım iddiası) | Durum |
|---|---|---|---|
| 1 | library | Şarkılardan oluşan kitaplık (koleksiyon) | draft |
| 2 | metadata | Şarkı bilgileri (sanatçı, albüm, süre vb.) | draft |
| 3 | artwork | Kapak resimleri | draft |
| 4 | catalog | Listeleme/dizin verisi (görünüm + arama kaynağı) | draft |
| 5 | playback | Çalma işlemi ve durumu (dosyanın çalınması) | draft |
| 6 | streaming | Akışla dinleme | draft |
| 7 | downloads | İndirme | draft |
| 8 | offline | Çevrimdışı kullanılabilirlik | draft |
| 9 | media-cache | Medya önbelleği | draft |
| 10 | synchronization | Medya verisinin eşitlenmesi | draft |
| 11 | media-testing | Medya testleri | draft |

## 3. Bağımlılık özeti (tam metin: media-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K003-audio | Ses motoruna çalma komutu/girişi (decode/playback) |
| depends_on → | K007-data | Kitaplık/metadata kalıcılığı |
| depends_on → | K013-integraion | Dış katalog/akış sistemiyle eşitleme |
| depends_on → | K010-network | Akış ve indirme taşıması |
| ← used_by | K011-application | Arayüzün medya gösterimi |
| ← used_by | K012-services | Servislerin medya verisi okuması |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: media-bagimliliklar.md (§1–§5) · media-dogrulama.md (§1–§6).
- Kısa liste:
  1. playback ↔ K012/playback-service sahipliği → ⚠️ VERIFICATION REQUIRED
  2. synchronization ↔ K013/synchronization ↔ K014/offline-sync (üç eşitleme) → ⚠️
  3. Referans ürün analizi (Plexamp · Finamp · Navidrome) → onay bekliyor
  4. Jellyfin/Plex/Emby kanıtları referans analizi mi, karar mı → onay bekliyor

**İlişkili vault projesi (2026-10-15):** **Neva Engine / Neva Player** — `.ai/projects/NevaEngine/` (11 md). `decode` (dr_libs PCM/WAV decode) + `playback` (ADR-019 Per-OS Neva Player) hattında K003-audio'nun uygulayıcısı; müzik dosyası formatı/sözleşmesi K015'te kalır, motor K003/Neva'da.

**Kaynak:** v2 §2.15 · .ai/architecture/index.md §5/15 · ADR-097 §5


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `media-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `media-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `media-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| kod | `library/ornekler.md` | 15 dil · koleksiyon/track modeli · dedup · playlist | Kademe-3 |
| kod | `metadata/ornekler.md` | 15 dil · ID3/Vorbis/MP4-ilst · EBU R128 / ReplayGain | Kademe-3 |
| kod | `artwork/ornekler.md` | 15 dil · CAA 250/500/1200 px · immutable + ETag | Kademe-3 |
| kod | `catalog/ornekler.md` | 15 dil · indeks/arama · content-hash dedup · ARC | Kademe-3 |
| kod | `playback/ornekler.md` | 15 dil · durum makinesi · Xing/LAME + SEEKTABLE | Kademe-3 |
| kod | `streaming/ornekler.md` | 15 dil · HTTP Range 206 + Content-Range · M3U8 | Kademe-3 |
| kod | `downloads/ornekler.md` | 15 dil · Range resume · .part + atomik rename · SHA-256 | Kademe-3 |
| kod | `offline/ornekler.md` | 15 dil · is_offline_ready · kota/LRU-TTL eviction | Kademe-3 |
| kod | `media-cache/ornekler.md` | 15 dil · ARC (T1/T2/B1/B2) · TTL sweep · ETag 304 | Kademe-3 |
| kod | `synchronization/ornekler.md` | 15 dil · vector clock / LWW · sync queue · yön AÇIK (§12/9) | Kademe-3 |
| kod | `media-testing/ornekler.md` | 15 dil · PCM SHA-256 golden · EBU R128 · Range 206 sözleşmesi | Kademe-3 |
