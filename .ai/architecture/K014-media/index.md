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
updated: 2026-10-18
---


# K014-media — Şarkı Dosyaları Dünyası

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K014-media |
| **K Adı** | Medya (Media — şarkı dosyaları) |
| **Kapsam** | Şarkı dosyaları dünyası — kapak resimleri, akış, indirme, çevrimdışı. Müziğin kendisini ve ona dair her şeyi taşır: kitaplık, bilgi (metadata), kapak (artwork), dizin, çalma, indirme/çevrimdışı, önbellek ve eşitleme. Ses sinyalinin işlenmesi K003-audio/K004-dsp'dir; burası dosya ve akıştır. 11 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K003-audio · K007-data · K010-network · K013-integraion (kanıt: FM depends_on) |
| **Durum** | draft (11 kademe-2 çocuk) |
| **Son Güncelleme** | 2026-10-12 (çocuk adları v2 §2.15 ile birebir 11/11 · Jellyfin referans kanıtı) |

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


## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K014-media | [[K014-media]] | ./K014-media/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ↔ | bağımlı (depends_on) | M→15 · M←4 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | → | bağımlı (depends_on) | M→18 · M←0 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ↔ | bağımlı (depends_on) | M→24 · M←17 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | → | güçlü çapraz referans | M→15 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ↔ | iki yönlü güçlü referans | M→11 · M←15 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ↔ | bağımlı (depends_on) | M→18 · M←6 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ← | karşı taraf referanslıyor | M→0 · M←7 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

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
