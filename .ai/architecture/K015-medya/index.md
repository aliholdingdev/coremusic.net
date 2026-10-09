---
title: "K015 MEDIA «MÜHÜR» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K015-medya/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: medya
ssot: true
risk: medium
owner: backend
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K015 MEDIA «MÜHÜR» — Katman Index

> **Authority:** Bu dosya K015 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K015 kartı > `.ai/CLAUDE.md` §5/§18/§22 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** SOFTWARE PLANE (anayasa §A.0 anahtar tablosu: `K015 · MEDIA · «MÜHÜR» · SOFTWARE`).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K015 |
| Kanonik Ad | MEDIA |
| Teatral Epitet | «MÜHÜR» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2 — SOFTWARE PLANE'nin son katmanı; K016'dan itibaren PHYSICAL) |
| Dizin deseni | `.ai/architecture/K015-medya/index.md` (R2.2 — dizin henüz üretilmedi, ls 2026-10-08) |
| Tier / Domain | 3 / medya |
| Owner (`.ai/AGENTS.md` §4 registry) | backend (Backend Architect — `backend`, AGENTS.md L81) |
| Risk | medium — katalog/akış/kayıp yüzeyi geniş + veri bütünlüğü (medya metadata), ancak doğrudan güvenlik-bypass ve fiziksel-güvenlik yok; high değil (R4.4) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-017 · ADR-019 · ADR-026 · ADR-073 · ADR-074 · ADR-076 · ADR-081 · ADR-092 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K015 MEDIA «MÜHÜR», CoreMusic'in medya kütüphanesi, oynatma, indirme ve önbellek katmanıdır;
anayasa §5 K15 satırı bu katmanın **"Yalnızca K14 üzerinden iletişim"** kısıtını koyar — yani tüm
ağ erişimi K014 NETWORK'e bağlıdır, doğrudan soket/ağ erişimi yoktur. Kapsam: Music Library ·
Local/Cloud Playback · Offline-First · FLAC/WAV/MP3 · Metadata · Queue · Download · Media Cache
(EK A §A.1). Repo durumu ikili: katalog-mühür ayağı (`media.coremusic.net/` — 22 dosya, ls
2026-10-08) IMPLEMENTED'dır; FFmpeg/HLS/DASH/işleme-akış ayağı `grep -ril "ffmpeg"` → **0 isabet**
ile PLANNED'dır. Kapsam dışı: ağ taşıması (K014), DSP/ses işleme (K003), dosya-şeması/kalıcılık
(K005), güvenlik kararı (K006), cihaz senkron donanımı (K001/K002).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K015 | MEDIA | «MÜHÜR» | medya | PHP 8.4 (media.coremusic.net — bin/ + src/Media/) · Node.js+TS download servisi (port 3001, §10 #7) · FFmpeg iş-akışı HEDEF | Music Library · Local/Cloud Playback · Offline-First · FLAC/WAV/MP3 · Metadata · Queue · Download · Media Cache (EK A §A.1 · 35 bileşen — anayasa §5 K15) | medya dosyaları/etiket girdisi (ID3/taxonomy) · katalog yazarları · indirme kaynakları (API) · K014 taşıma çıktısı (akış) | mühürlenmiş katalog kaydı (ULID) · oynatılabilir akış/kuyruk · indirme kuyruğu/önbellek · metadata kaydı | K000-K014 (EK A aralık: OS + K014 ağ — **ağ yalnız K14 üzerinden**, anayasa §5 K15 kısıtı) + port/adapter | K015 → K016-K020'e doğrudan erişim (H20) · **K014 dışına ağ erişimi** (K15 kısıtı) · K005'e doğrudan SQL (repository/port dışında) · H19 · DSP/K003 sinyal zincirine müdahale | katalog/medya metadata (`coremusic_musics` · `coremusic_media` 8 tablo · `coremusic_download` 4 tablo · `media_catalog` 9 tablo PLANNED · `novasearch` 7 tablo canlı — §18) | erişim kontrolü `media_access` + `media_audit` (K006 RBAC ile hizalı) · imzalı tek-kullanımlık indirme URL + cihaz limiti + abuse önlemi (ADR-026) · K006/K007 atlanamaz | fail-over (EK A) · ağ kesintisi → Offline-First (§22) · indirme hatası → byte-range resume (ADR-026) · dosya kaybı → yeniden-tara (scan) · parça bozulma → validator red | `media_audit` kaydı · tarama/ingest logları (`bin/scan.php`, `bin/ingest.php`, `bin/audit.php`) · indirme geçmişi (`download_history`); merkezi metrik PLANNED | `media.coremusic.net/` test dosyası GÖZLENMEDİ (composer.json/phpunit var mı ls edilmedi → ⚠️); hedef test tanımı §6.4 · hedef ≥80% (anayasa §17) | repo: `media.coremusic.net/src/Media/{CatalogWriter,Slugger,Taxonomy,Ulid,Validator}.php` + `bin/{scan,ingest,audit}.php` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K15 + §18 (DB listesi)` + `00-kspace-anayasa.md §A.1 K015` · ADR: ADR-017/019/026/073/074/076/081/092 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K015 |
| 2 | KANONİK_AD | MEDIA |
| 3 | TEATRAL_EPİTET | «MÜHÜR» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | medya |
| 5 | SUBDOMAIN | music-library · playback-local-cloud · offline-first · format-codec · metadata-id3 · queue · download · media-cache · streaming-hls-dash · catalog-axes |
| 6 | BOUNDED_CONTEXT | Media Library, Playback & Acquisition — içerik kütüphanesi ve kazanım; sinyal işlemez, taşıma yapmaz, kimlik kararı vermez |
| 7 | RUNTIME | PHP 8.4 (media.coremusic.net: bin/ iş-emirleri + src/Media/ kütüphanesi) · Node.js + TypeScript download servisi (port 3001 — §10 #7, repo YOK → PLANNED) · FFmpeg işlem hattı (HEDEF — repo 0 isabet) |
| 8 | SORUMLULUK | Music Library · Local/Cloud Playback · Offline-First · FLAC/WAV/MP3 · Metadata · Queue · Download · Media Cache (EK A §A.1 K015 sorumluluk satırı) |
| 9 | GIRDI | medya dosyası (FLAC/WAV/MP3) + yan dosyalar (kapak/lirik) · taxonomy/şema (`config/media.schema.json`, `config/taxonomy.json`) · indirme kaynağı API'leri (`download_sources`) · K014 taşıma çıktısı (akış/teslim) · K003-K004 oynatma/içerik analizi talepleri (olay) |
| 10 | CIKTI | mühürlenmiş katalog kaydı (ULID + slug + taxonomy) · oynatılabilir kuyruk/kaynak · indirme kuyruğu/önbellek kaydı · metadata kaydı · audit satırı (`media_audit`) |
| 11 | IZINLI_BAGIMLILIK | K000-K013 alt katmanlar (EK A aralık), **K014 NETWORK — ağ erişiminin TEK yolu** (anayasa §5 K15 kısıtı), K005 repository/port (DB erişimi), K003/K004 oynatma-işleme arayüzleri (olay/port), port/adapter |
| 12 | YASAK_BAGIMLILIK | K015 → K016-K020 doğrudan erişim / geri çağrı (H20) · **K014 dışına doğrudan ağ erişimi** (TCP/soket/ağ kütüphanesi kullanımı) · K005'e doğrudan SQL (ORM/`SELECT *` zaten yasak) · H19 veri paylaşımı · DSP sinyal zincirine müdahale (K003) · sinyal/güç donanımına dokunmak (K016-K019) |
| 13 | DATA_BOUNDARY | katalog/metadata: `coremusic_musics` (§18 #3) · `coremusic_media` (§18 #8 — devices, device_sync_history, device_playlists, device_tracks, device_types, media_access, media_audit, media_metadata) · `coremusic_download` (§18 #15 — download_cache, download_history, download_queue, download_sources) · `media_catalog` (9 tablo, .sql VAR/deploy YOK → PLANNED — §18 ek not) · `novasearch` (7 tablo, canlı, knex — 18'lik sayımın DIŞINDA — §18 ek not) |
| 14 | SECURITY_BOUNDARY | erişim: `media_access` + `media_audit` (K006 RBAC kararıyla hizalı; K015 karar üretmez) · indirme yüzeyi: imzalı tek-kullanımlık URL · cihaz limiti · hız/kota (ADR-013 ile) · byte-range resume · abuse önleme (ADR-026) · K006/K007 atlanamaz; DLNA/AirPlay gibi dış-teslim yolları K014'te politikasız açılmaz |
| 15 | FAILURE_MODE | fail-over (EK A) · ağ kesintisi → Offline-First + yerel kuyruk (anayasa §22) · indirme yarıda kesilir → byte-range resume (ADR-026) · dosya/şema uyuşmazlığı → `Validator` reddi · medya dosyası bozuk → tarama (`bin/scan.php`) ile dışlanma · queue taşması → kuyruk-sıra politikası (⚠️ tanımsız) |
| 16 | OBSERVABILITY | `media_audit` kaydı · tarama/ingest logları (`bin/audit.php`) · `download_history` + `download_cache` ölçümü · oynatma/kuyruk olayları (K008 event → K012); merkezi metrik/trace (Prometheus) PLANNED |
| 17 | TEST | `media.coremusic.net/` altında test dosyası GÖZLENMEDİ (ls 2026-10-08 — yalnız 22 dosya: bin/config/docs/src) → ⚠️; hedef test tanımı §6.4 (validator/slug/taxonomy/ULID + offline + resume) · hedef ≥80% (anayasa §17) |
| 18 | KANIT | repo: `media.coremusic.net/src/Media/*.php` (5 dosya) + `bin/{scan,ingest,audit}.php` + `config/{media.schema,taxonomy}.json` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K15` + `§18` (DB + ek sistem notu) + `00-kspace-anayasa.md §A.1 K015` · ADR: ADR-017/019/026/073/074/076/081/092 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (R9) |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls/grep + vault read + accepted/ ls) |
| 20 | EPİTET_KALİTE_NOTU | «MÜHÜR» — içeriği mühürleyen/kilitleyen metafor; 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K015 · MEDIA · «MÜHÜR» · SOFTWARE` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI'da K014 = ağ erişiminin
tek yolu; YASAK'ta "K014 DIŞINA ağ erişimi" — farklı küme: izinli taşıyıcı K014, yasak olan K014'ün
dışındaki her yol → kesişim ∅; H20 aralığı K016-K020 = IZINLI aralığının dışında) · (c) KANIT 3'lü
format — GEÇTİ (repo | vault/ADR | web ⚠️) · (d) veri sınırı tek katmana ait (katalog/indirme
şemaları; ağ şeması K014'te, sinyal K003'te) — GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K015 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Music Library · Local/Cloud Playback ·
Offline-First · FLAC/WAV/MP3 · Metadata · Queue · Download · Media Cache".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Music Library | katalog taraması, slug/taxonomy, ULID kimlik, kayıt yazımı | IMPLEMENTED (repo) | `media.coremusic.net/src/Media/{CatalogWriter,Slugger,Taxonomy,Ulid,Validator}.php` + `bin/scan.php` (ls 2026-10-08) · ADR-092 |
| Local/Cloud Playback | yerel dosya oynatma + bulut/kayıt kaynağı | DESIGN/PLANNED — oynatıcı repo kanıtı yok; ADR-019 (per-OS Neva player) kararı var | ADR-019 (accepted/ ls) · ADR-017 (DSP Hardware Mode — kablosuz oynatma zinciri) · repo grep "ffmpeg" 0 |
| Offline-First | çevrimdışı erişim + kuyruk (anayasa §1/§22) | DESIGN — şemalar var (`download_queue`, `sync_history`); uygulama kodu yok | `.ai/.sql/mysql/coremusic_download.sql` (ls 2026-10-08) · `.ai/CLAUDE.md §22` |
| FLAC/WAV/MP3 | desteklenen format seti (anayasa §1: FLAC/WAV/MP3) | DESIGN (format hedefi anayasada) · kodlayıcı/decode kanıtı yok | `.ai/CLAUDE.md §1` · grep "flac|id3" shared/src → 0 (2026-10-08) |
| Metadata | etiket okuma/yazma (ID3), taxonomy, şema doğrulama | IMPLEMENTED (taxonomy/şema/validator) · ID3 okuyucu YOK | `config/media.schema.json` + `config/taxonomy.json` + `src/Media/{Validator,Taxonomy}.php` (ls) · §5 K15 "ID3" → ⚠️ |
| Queue | oynatma kuyruğu + indirme kuyruğu | DESIGN — `download_queue` şeması var; oynatma-kuyruk kodu yok | `.ai/.sql/mysql/coremusic_download.sql` (ls) · ADR-019 ⚠️ |
| Download | kaynak API'lerden indirme, resume, önbellek | DESIGN — şema (4 tablo) + ADR-026 kararı; servis repo'su YOK (download.coremusic.net dizini root'ta yok, ls 2026-10-08) | `.ai/.sql/mysql/coremusic_download.sql` · ADR-026 · `.ai/CLAUDE.md §9 #4` (Faz-1 gerçeklik notu) |
| Media Cache | indirme/akış önbelleği | DESIGN — `download_cache` şeması var | `.ai/.sql/mysql/coremusic_download.sql` (ls) |

### §4.2 Anayasa §5 K-Matrix Satırı (K15) — 35 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K15 Medya & Streaming | FFmpeg, FLAC, HLS,
DASH, ID3, Radio | 35 bileşen | Yalnızca K14 üzerinden iletişim.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| FFmpeg | transkod/dönüşüm/işleme hattı | PLANNED (grep "ffmpeg" shared/ → 0 isabet, 2026-10-08) | `.ai/CLAUDE.md §5 K15` + §10 #2 (Media Service: PHP + FFmpeg) · grep 0 → ⚠️ |
| FLAC | kayıpsız format kodlama/çözme | PLANNED (grep "flac" shared/src → 0) | `.ai/CLAUDE.md §1` + §5 K15 |
| HLS | HTTP canlı/arşiv segmentleme akışı | PLANNED (grep m3u8/hls → 0) | `.ai/CLAUDE.md §5 K15` · §15 (HLS hedefi) |
| DASH | uyarlanabilir segmentli akış | PLANNED | `.ai/CLAUDE.md §5 K15` + §15 |
| ID3 | dosya etiket okuma/yazma (başlık/sanatçı/albüm) | PLANNED (grep "getid3|id3v" → 0) | `.ai/CLAUDE.md §5 K15` · §18 #3 (metadata tabloları) |
| Radio | radyo akışı/kanal kapsamı | DESIGN — `coremusic_musics` kapsamı "radio" içeriyor (§18 #3) · radyo-akış kodu yok | `.ai/CLAUDE.md §18 #3` (radio satırı) · ADR-074 radio-database-schema (accepted/ ls) |
| "35 bileşen" sayımı | §5 K15 bileşen sayısı (envanter — HEDEF) | HEDEF · alt-bileşen dökümü ⚠️ | `.ai/CLAUDE.md §5 K15 satırı` · H10: hedef ≠ kanıt (repo'da 5 kütüphane dosyası + 3 iş-emri) |
| Kısıt: "Yalnızca K14 üzerinden iletişim" | tüm ağ erişimi K014'e bağlı | BAĞLAYICI (anayasa kısıtı) | `.ai/CLAUDE.md §5 K15 satırı` + §5 K14 satırı |

### §4.3 Alt-Sistem Bazlı Derinlik (11 kalem)

#### §4.3.1 Kütüphane · Katalog Yazımı (IMPLEMENTED)

| Boyut | İçerik |
|---|---|
| Kural | katalog kaydı: taxonomy → slug → ULID → yazım (`CatalogWriter`) — tekrarsız kimlik + okunabilir slug |
| Repo durumu | IMPLEMENTED: `src/Media/{CatalogWriter,Slugger,Taxonomy,Ulid,Validator}.php` + `bin/{scan,ingest,audit}.php` (ls 2026-10-08) |
| Edge case | aynı isimli iki kayıt (slug çakışması) · bozuk/eksik dosya · mojibake başlıklar (config/mojibake-fix.json var) |
| Sınır | yalnız katalog/metadata; dosyanın kendisi K005 dosya katmanında |
| Kanıt | ADR-092 (medya dizin ekseni + ULID — accepted/ ls) · `media.coremusic.net/config/media.schema.json` (ls) |

#### §4.3.2 Biçim · FLAC / WAV / MP3 (+ FFmpeg hattı)

| Boyut | İçerik |
|---|---|
| Kural | format seti anayasa §1'de: FLAC, WAV, MP3 (Offline-First koleksiyonu) |
| Durum | PLANNED — `grep -ril "ffmpeg"` shared/ → 0; `grep -ril "flac|getid3|id3v"` shared/src → 0 (2026-10-08) |
| Edge case | değişken bit hızı · bozuk sonlandırma · biçim-dışı uzantı · transkod gereksinimi (kaynak≠hedef) |
| Kapsam dışı | ses işleme/EQ (K003), DSP donanım modu (ADR-017 üzerinden K002/K003) |
| Kanıt | `.ai/CLAUDE.md §1` + §5 K15 · §10 #2 (Media Service: PHP + FFmpeg — hedef) · ⚠️ |

#### §4.3.3 Oynatma · Local / Cloud Playback

| Boyut | İçerik |
|---|---|
| Kural | yerel koleksiyon + kayıt/kayıt-kaynağı oynatma; per-OS oynatıcı kararı ADR-019 |
| Durum | DESIGN — ADR-017 (3 katman: XMOS/xCORE firmware · JUCE plugin DSP · ASIO/WASAPI host) + ADR-019 (per-OS Neva player) kararları var; oynatıcı repo kodu YOK |
| Edge case | ASIO exclusive lock (anayasa §23 #6) · USB cihaz çıkarma → WASAPI fallback (§22) · format-değişimi (48kHz) |
| Kapsam dışı | sürücü/kart (K002), DSP zinciri (K003), amplifikatör (K016) |
| Kanıt | ADR-017 · ADR-019 (accepted/ ls) · `.ai/CLAUDE.md §19` (Audio Engine standartları) · §22 |

#### §4.3.4 Çevrimdışı · Offline-First

| Boyut | İçerik |
|---|---|
| Kural | internet olmadan koleksiyon erişimi (anayasa §1 "Offline-First mimarisi") + kesintide kuyruk |
| Durum | DESIGN — şema kanıtı: `download_queue`, `download_cache`, `download_history` (ls 2026-10-08); `sync_history` K014'ün wireless şemasında |
| Edge case | kuyruk taşması · kısmi indirme · çakışma (aynı kayıt iki cihazda) · sıra önceliği |
| İlişki | senkron deseni ADR-081 (MySQL SSOT + outbox) ile hizalı; K014 taşıması üzerinden |
| Kanıt | `.ai/CLAUDE.md §1` + §22 (Network outage satırı) · `.ai/.sql/mysql/coremusic_download.sql` (ls) · ADR-081 (accepted/ ls) |

#### §4.3.5 Metadata · Etiket + Taxonomy

| Boyut | İçerik |
|---|---|
| Kural | alan şeması + taxonomy + doğrulama (`media.schema.json`, `taxonomy.json`, `Validator`) |
| Repo durumu | IMPLEMENTED (şema+validator) · ID3 okuyucu (§5 K15 "ID3") PLANNED — grep 0 |
| Edge case | eksik albüm/adı · mojibake (config/mojibake-fix.json) · çok-dilli etiket (i18n — K005/K011) |
| Sınır | K015 metadata üretir; kalıcı saklama K005, gösterim K011 |
| Kanıt | `media.coremusic.net/config/media.schema.json` · `taxonomy.json` · `src/Media/{Validator,Taxonomy}.php` (ls) |

#### §4.3.6 Kuyruk · Oynatma + İndirme Kuyruğu

| Boyut | İçerik |
|---|---|
| Kural | iki ayrı kuyruk: oynatma sırası (client/K010) + indirme sırası (`download_queue`) |
| Durum | DESIGN — `download_queue` şeması var; oynatma-kuyruk kodu yok (grep 0) |
| Edge case | öncelik (manuel vs otomatik) · kuyruk taşıması · aynı şarkının tekrarı · sıraya eklenen cihaz-kısıtı (ADR-026 cihaz limiti) |
| Kapsam dışı | sıra UI'ı (K011), sıra verisi sahipliği tartışması (K005 şeması) |
| Kanıt | `.ai/.sql/mysql/coremusic_download.sql → download_queue` (ls) · ADR-026 (accepted/ ls) |

#### §4.3.7 İndirme · Download Servisi

| Boyut | İçerik |
|---|---|
| Kural | imzalı tek-kullanımlık URL · cihaz limiti · hız/kota (ADR-013) · byte-range resume · abuse önleme · uygulama-içi stream + CDN opsiyonel |
| Durum | DESIGN (ADR-026 kararı + 4 şema tablosu) · Node.js+TS servis repo'su YOK (root'ta download.coremusic.net dizini yok, ls 2026-10-08) |
| Edge case | URL sızıntısı · kota aşımı · yarıda kesik indirme (resume) · aynı-url ikinci kullanım (tek-kullanımlık) |
| Güvenlik | imzalı URL + cihaz limiti + abuse → K006 ile hizalı; K015 karar üretmez |
| Kanıt | ADR-026 (accepted/ ls) · `.ai/CLAUDE.md §9 #4` + §10 #7 + §11 port 3001 · `.ai/.sql/mysql/coremusic_download.sql` (ls) |

#### §4.3.8 Önbellek · Media Cache

| Boyut | İçerik |
|---|---|
| Kural | indirme/akış önbelleği; tekrar-indirmeyi önler, offline erişimi besler |
| Durum | DESIGN — `download_cache` şeması var; önbellek politikası (TTL/boyut) kodda yok → ⚠️ |
| Edge case | dolu disk · bayat içerik (eski sürüm) · paylaşımlı-önbellek eşzamanlılığı |
| İlişki | uygulama-önbelleği (APCu/CacheManager — §12) ile karıştırılmaz: burada medya-byte önbelleği |
| Kanıt | `.ai/.sql/mysql/coremusic_download.sql → download_cache` (ls) · `.ai/CLAUDE.md §12` (Cache satırı) |

#### §4.3.9 Akış · HLS / DASH

| Boyut | İçerik |
|---|---|
| Kural | uyarlanabilir segmentli akış (§5 K15 kalemleri) + canlı/arşiv |
| Durum | PLANNED (grep m3u8/dash → 0 isabet; yalnız vendor/da false-positive "dashboard") |
| Edge case | segment süresi/bant-değişimi · DRM yokluğu (⚠️: DRM kararı vault'ta tanımsız) · CDN (ADR-026 "CDN opsiyonel") |
| Kapsam dışı | taşıma (K014 HTTP), kodlayıcı (FFmpeg hattı) |
| Kanıt | `.ai/CLAUDE.md §5 K15` + §15 (HLS/DASH hedefi) · grep (2026-10-08) → ⚠️ |

#### §4.3.10 Yayın · Radio / Podcast / Video

| Boyut | İçerik |
|---|---|
| Kural | radyo akışı + podcast/video içerik tipleri (§18 #3 kapsamı "podcasts, videos, radio") |
| Durum | DESIGN — şema kararları: ADR-073 (podcast), ADR-074 (radio), ADR-076 (video) accepted; akış kodu yok |
| Edge case | canlı yayın gecikmesi · bölüm-yayın sırası (podcast) · telif/erişim (`media_access`) |
| İlişki | içerik verisi K005 (`coremusic_musics`), erişim K006, teslim K014 |
| Kanıt | `.ai/CLAUDE.md §18 #3` · ADR-073/074/076 (accepted/ ls) |

#### §4.3.11 Kimlik & Dizin Ekseni · ULID

| Boyut | İçerik |
|---|---|
| Kural | medya kayıtları ULID ile; dizin ekseni ADR-092 kararı (medya arşivi dizin ekseni) |
| Durum | IMPLEMENTED (repo: `src/Media/Ulid.php` + `Slugger.php`) |
| Edge case | ULID sıralı-doğal-sıra (zaman-kodlu) → takip/sızıntı analizi ⚠️ · slug-ULID çiftinin senkronu |
| Sınır | kimlik üretimi K015'in; erişim-anahtarı/API-key K006/K009 alanı (§18 #13 api_keys) |
| Kanıt | ADR-092 (accepted/ ls) · `media.coremusic.net/src/Media/Ulid.php` (ls 2026-10-08) |

### §4.4 Kapsam Dışı / Sınır Tanımı (K015'in YAPMADIĞI)

| Aday konu | Neden K015 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| Ağ taşıması/protokol | anayasa kısıtı: K15 yalnız K14 üzerinden iletişim | K014 NETWORK | `.ai/CLAUDE.md §5 K15` |
| EQ/DSP/çözücü/ses işleme | sinyal işleme zinciri | K003 AUDIO ENGINE | anayasa §A.1 K003 · `.ai/CLAUDE.md §5 K3` |
| Kalıcı depolama/şema sahipliği | veri katmanı | K005 DATA | `.ai/CLAUDE.md §5 K5` |
| RBAC/erişim kararı | güvenlik kararı | K006 SECURITY | `.ai/CLAUDE.md §5 K6` |
| Cihaz radyosu/USB/DAC | donanım + sürücü | K001/K002 | ADR-096 §2.2 |
| Amplifikatör/güç/termal/PCB/üretim | PHYSICAL plane | K016-K020 | anayasa §A.0 (uçak) · ADR-096 §2.2 |

### §4.5 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K15 hedefi | 35 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (ls/grep 2026-10-08) | `media.coremusic.net/` 22 dosya (5 kütüphane sınıfı + 3 iş-emri + 3 config + 5 doküman + iskelet); FFmpeg/FLAC/ID3/HLS/DASH grep → 0 |
| Şema kanıtı | `coremusic_media` 8 tablo · `coremusic_download` 4 tablo · `media_catalog` 9 tablo (PLANNED) · `novasearch` 7 tablo (canlı, 18'lik sayım dışı) — ls 2026-10-08 |
| Durum dağılımı | IMPLEMENTED: 3 (katalog yazımı · şema/validator · ULID/slug) · DESIGN: 5 (offline · queue · download · cache · metadata-şema) · PLANNED: 5 (format/FFmpeg · streaming · ID3 okuyucu · radio/podcast/video akışı · oynatıcı kodu) |
| Test | `media.coremusic.net/` test dosyası GÖZLENMEDİ → ⚠️ (ls kapsamı: 22 dosya) |
| Web research | 0 URL bu dosyada → ⚠️ (R9 3'lü eksik; F1 EK B kapısı) |

### §4.6 K015 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K015 etkisi |
|---|---|---|
| ADR-017-dsp-hardware-mode | DSP Hardware Mode (3 katman: XMOS/xCORE firmware · JUCE plugin DSP · ASIO/WASAPI host — buffer/latency/xrun) | oynatma zincirinin donanım-yazılım sınırı (K002/K003 ile ortak) |
| ADR-019-per-os-neva-player | Per-OS Neva Player | yerel oynatıcının platform-spesifik kararı |
| ADR-026-download-service-architecture | İmzalı tek kullanımlık URL · cihaz limiti · hız/kota · byte-range resume · abuse · uygulama stream + CDN | indirme alt-sisteminin güvenlik + dayanıklılık kuralları |
| ADR-073-podcast-database-schema | Podcast veritabanı şeması | podcast içerik tipi (§4.3.10) |
| ADR-074-radio-database-schema | Radio veritabanı şeması | radyo içerik tipi (§5 K15 "Radio") |
| ADR-076-video-database-schema | Video veritabanı şeması | video içerik tipi |
| ADR-081-multi-provider-data-sync | Çoklu-provider veri senkronizasyonu (MySQL SSOT + outbox) | offline/indirme senkron deseni (outbox) |
| ADR-092-media-dizin-ekseni-ve-ulid | Medya arşivi dizin ekseni ve ULID kimliği | katalog kimliği + dizin ekseni (§4.3.11) |

### §4.7 K015 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K015'in verdiği | K015'in beklediği | Kanıt |
|---|---|---|---|---|
| K014 NETWORK | taşıma kanalı (tek ağ yolu) | akış/istek talepleri (K14 üzerinden) | güvenilir taşıma + offline olayları | `.ai/CLAUDE.md §5 K15 kısıtı` |
| K005 DATA | repository/port | katalog/metadata/indirme kayıtları | BCNF şema + hazır statement (ORM/`SELECT *` yasak) | `.ai/CLAUDE.md §18` · ADR-002 |
| K003 AUDIO ENGINE | oynatma/işleme arayüzü (olay/port) | çözülmüş/format-uymuş kaynak | DSP talepleri (EQ/reverb) geri bildirimi | `.ai/CLAUDE.md §5 K3` + §19 |
| K006 SECURITY | erişim kararı | `media_access` için RBAC kararı | K015 audit satırları (`media_audit`) | `.ai/CLAUDE.md §5 K6` · §18 #8 |
| K010/K011 | panel/içerik arayüzü | katalog/kuyruk/akış verisi (API/K009 üzerinden) | istek/DTO sözleşmesi (§6A.5 — SPA ApiClient) | `.ai/CLAUDE.md §6A.5` · §9 |
| K016-K020 | PHYSICAL plane | (senkron bağ YOK — çift uçak) yalnız sürücü/API sınırı | oynatma/güç durum olayları (olay yukarı) | ADR-096 §2.2 · R6.2 |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K014 NETWORK | aşağı (tek ağ yolu) | anayasa kısıtı: "Yalnızca K14 üzerinden iletişim" | `.ai/CLAUDE.md §5 K15` · anayasa §A.1 K015 "izinli=K000-K014" |
| K000-K013 | aşağı | EK A aralık (OS, donanım, sürücü, DSP, AI, data, güvenlik, middleware, servis, API, uygulama, UX, izleme, CI/CD) | `00-kspace-anayasa.md §A.1 K015 Sınır satırı` |
| K005 DATA (repository/port) | aşağı | kalıcı kayıt; raw PDO + explicit columns | `.ai/CLAUDE.md §18` · ADR-002 (accepted/ ls) |
| K003/K004 (olay/port) | aşağı (arayüz) | oynatma/içerik analizi talepleri port/olay ile | `.ai/CLAUDE.md §5 K3/K4` |
| port/adapter | yan | EK A istisnası (R6.3) | `00-kspace-anayasa.md §A.1 K015` |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K015 → K016-K020 doğrudan erişim / geri çağrı (H20) | klasik yön; PHYSICAL plane ile senkron bağ yok | `rules.md R6.1` · `ADR-096 §2` |
| **K014 dışına doğrudan ağ erişimi** (soket/kütüphane) | anayasa kısıtı: medya ağı yalnız K14 üzerinden | `.ai/CLAUDE.md §5 K15` |
| K005'e doğrudan SQL / ORM / `SELECT *` | ADR-002 + Guardrail #9 + §18 kuralları | ADR-002 (accepted/ ls) · `.ai/CLAUDE.md §18` |
| K006/K007 kararlarını atlamak (auth/RBAC/CSRF) | güvenlik katmanı atlanamaz | `.ai/CLAUDE.md §5 K6` + §6 |
| DSP/sinyal zincirine müdahale | K003 münhasır alanı; "sinyal zincirine parazit yasak" | `.ai/CLAUDE.md §5 K1` + §5 K3 |
| H19 doğrudan veri paylaşımı (dosya paylaşımı) | veri sınırı ihlali (R4.4d) | `rules.md R6.1` · `ADR-096 §2` |
| `SELECT *` / ORM / framework | ADR-001/002 mutlak yasakları | `rules.md R17` |

### §5.3 Boundary Matrisi

| Boundary | K015 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | katalog/metadata/indirme şemaları (musics · media 8 · download 4 · media_catalog 9 PLANNED · novasearch 7 canlı) | K005 (şema sahibi/DB) · K014 (bağlantı şeması) |
| SECURITY_BOUNDARY | `media_access`/`media_audit` + ADR-026 indirme yüzeyi (imzalı URL, cihaz limiti, kota, abuse) | K006 (karar) · K007 (istek hattı) |
| FAILURE_MODE | fail-over · offline kuyruk · resume · validator/scan red · (queue-taşma politikası ⚠️) | K014 (kesinti taşıması) · K012 (olay) |
| RUNTIME boundary | PHP media iş-emirleri + Node.js download (repo YOK) + FFmpeg (hedef) | K000 (runtime) · K013 (dağıtım) |
| FORMAT boundary | FLAC/WAV/MP3 + hedef dönüşüm (FFmpeg); format-kararı K015'te kalır | K003 (işleme) · K011 (gösterim) |
| Olay (event) yukarı serbest | katalog/indirme/oynatma olayları yukarı (K008/K012); senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay/log yayını, yukarı SERBEST)  ←  K015 katalog/indirme/oynatma olayları
      ↑                                                          │
      │ (okuma)                                        [senkron çağıramaz — H20]
      └──────────── K008 SERVICES (Event Bus) ────────────┘
                         │
   K015 yalnız alttan beslenir: K014 taşıma + K005 repository + K003/K004 arayüz  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K015 → K012 log/olay | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K015 → K008 senkron çağrı | — | YASAK (H20) | `rules.md R6.1` · ADR-086 |
| K015 → K014 ağ talebi | aşağı | tek ağ yolu (K15 kısıtı) | `.ai/CLAUDE.md §5 K15` |
| K015 → K005 repository | aşağı | raw PDO · explicit columns | ADR-002 · `.ai/CLAUDE.md §18` |
| K016-K020 → K015 (olay) | yukarı (fiziksel→yazılım) | yalnız olay/durum yayını; senkron yok | ADR-096 §2.2 · R6.2 |

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K015 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K015 / library-playback-download-cache-metadata | SUBDOMAIN §3/5 |
| Module → Component | `media.coremusic.net/src/Media/*` modülleri (CatalogWriter · Slugger · Taxonomy · Ulid · Validator) | repo kanıtı (ls) |
| Service | Media Service (port 5000/6000 — §10 #2) · Download Service (port 3001 — §10 #7) | hedef/PLANNED (repo yok) |
| Adapter | K014 taşıma adaptörü · K005 repository adaptörü · indirme kaynak adaptörleri | port/adapter ile sıçrama |
| Döngü toleransı | sıfır (R6.6) | `rules.md R6.6` |

### §5.6 K015 Risk Güvenlik Notları (anayasa §23 hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| İmzalı URL sızıntısı / tek-kullanımlık ihlali | yetkisiz indirme | ADR-026: tek kullanımlık + cihaz limiti + abuse | ADR-026 (accepted/ ls) |
| Medya erişim kontrolü atlanması | yetkisiz içerik | `media_access` + K006 RBAC; K014/K007 atlanamaz | `.ai/CLAUDE.md §18 #8` + §5 K6 |
| Offline kuyruk/çakışma veri kaybı | kullanıcı verisi tutarsızlığı | Offline-First + outbox (ADR-081) | `.ai/CLAUDE.md §22` · ADR-081 |
| "FFmpeg/HLS implemente" iddiasının kanıtsızlığı | halüsinasyon riski | grep 0 → PLANNED yazıldı (H1/R9) | `rules.md R16.3` |
| `media_catalog` / `novasearch` sayım-belirsizliği | 18 DB sayımında ikilem | §18 ek notu: 18'lik sayımın DIŞINDA; durum PLANNED/canlı | `.ai/CLAUDE.md §18 ek notu` |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| Tarama (scan) | medya dosyası ağacı | aday kayıt listesi | `bin/scan.php` (ls) |
| İçe aktarma (ingest) | aday + taxonomy + şema | mühürlenmiş katalog kaydı (ULID+slug) | validator reddi olabilir |
| Metadata | dosya etiketi (ID3 — hedef) | normalize metadata | ID3 okuyucu PLANNED |
| Kuyruk | oynatma/indirme sırası | sıralı iş emri | `download_queue` şeması |
| İndirme | kaynak API + imzalı URL | dosya + `download_history` | ADR-026 (resume/kota) |
| Önbellek | indirilen byte | `download_cache` | TTL/boyut politikası ⚠️ |
| Oynatma | kaynak (yerel/kayıt) | çalınabilir akış (K003/K002'ye devir) | ASIO/WASAPI — §23 #6 |
| Akış (hedef) | segmentlenmiş içerik | HLS/DASH akışı (K14 üzerinden) | PLANNED |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Katalog motoru | PHP 8.4 — `media.coremusic.net` (bin/ + src/Media/) | repo ls 2026-10-08 |
| İndirme servisi | Node.js + LTS (§24) · port 3001 HTTP/WS | `.ai/CLAUDE.md §10 #7` + §11 + §24 · repo YOK → PLANNED |
| Medya servisi | PHP + FFmpeg · port 5000/6000 | `.ai/CLAUDE.md §10 #2` + §11 · FFmpeg grep 0 → PLANNED |
| Şema/alan seti | `media.schema.json` + `taxonomy.json` | repo ls 2026-10-08 |
| Format hedefi | FLAC · WAV · MP3 (§1) · 48kHz/Float32 (§19 — K003 ile ortak) | `.ai/CLAUDE.md §1` + §19 |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Medya denetimi | `media_audit` (coremusic_media) + `bin/audit.php` | IMPLEMENTED (şema + iş-emri) |
| Tarama/ingest logu | `bin/scan.php` / `bin/ingest.php` çıktısı | IMPLEMENTED (betik var · çıktı biçimi ⚠️) |
| İndirme geçmişi/önbellek ölçümü | `download_history` · `download_cache` | IMPLEMENTED (şema) |
| Oynatma/kuyruk olayları | K008 event → K012 | PLANNED (K012 metrik altyapısı ile) |
| Merkezi metrik/trace | Prometheus/Grafana (anayasa §5 K12) | PLANNED |

### §6.4 Test

| Katman | Test | Durum | Hedef |
|---|---|---|---|
| Katalog birimi (slug/taxonomy/ULID/validator) | `media.coremusic.net` test suite | GÖZLENMEDİ (ls 2026-10-08) → ⚠️ | ≥80% (anayasa §17) |
| Şema doğrulama | `media.schema.json` + `Validator` uçtan-uca | TANIMLI, YAZILMADI | hedef test tanımı |
| İndirme resume/kota/tek-kullanım | ADR-026 davranış kilitleri | TANIMLI, YAZILMADI | ADR-026 sözleşmesi |
| Offline kuyruk tutarlılığı | kesinti → kuyruk → yakalama | TANIMLI, YAZILMADI | §22 senaryosu |
| Biçim/akış (FFmpeg/HLS/DASH) | transkod + segment testi | TANIMLI (PLANNED katman) | anayasa §17 |
| E2E (panel üzerinden) | Playwright (`assets.coremusic.net/playwright.config.ts` — ls) | altyapı var · K015 kapsamı yok | KAPI 9 / §11 |

### §6.5 Failure Mode Senaryoları (failure=fail-over)

| # | Senaryo | K015 davranışı | Kullanıcı etkisi | Kanıt |
|---|---|---|---|---|
| 1 | Ağ kesintisi (çevrimdışı) | Offline-First: yerel koleksiyon + kuyruk | kesintisiz yerel dinleme | `.ai/CLAUDE.md §1` + §22 |
| 2 | İndirme yarıda kesilir | byte-range resume (kaldığı yerden) | indirme kaybı yok | ADR-026 (accepted/ ls) |
| 3 | Dosya/şema uyuşmazlığı | `Validator` reddi → kayıt açılmaz | hatalı içerik içeri girmez | `src/Media/Validator.php` (ls) |
| 4 | Bozuk/eksik medya dosyası | tarama ile dışlanma (`scan`) | parça oynatılamaz | `bin/scan.php` (ls) |
| 5 | URL tekrar kullanımı (indirme) | tek-kullanımlık URL reddi | yeniden istek gerekir | ADR-026 |
| 6 | Kota/cihaz limiti aşımı | indirme reddi (kota/limit) | indirme engeli | ADR-026 + ADR-013 |
| 7 | Kuyruk taşması | politika ⚠️ tanımsız | sıradan düşme/erteleme belirsiz | ⚠️ (research kapısı) |
| 8 | `media.coremusic.net` DB'si erişilemez | ingest/scan hata → iş emri durur | katalog güncellenmez | ⚠️ (hata davranışı kodda doğrulanmadı) |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K015 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K015 + §5/§18/§19/§22 + rules.md + ADR-096 okundu |
| KAPI 9 hallucination damgası | `⚠️` ayağlar §7.1'de (FFmpeg/HLS/DASH/ID3/WS-akışı testleri, kuyruk politikası, download servis repo'su, web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — 0 isabetli grep sonuçları PLANNED yazıldı |
| Guardrail #9/#10 (ORM/framework yasak) | K015 kodu ORM içermiyor (5 PHP sınıfı + JSON şema) · tam grep ⚠️ |
| H10 (hedef ≠ kanıt) | Uygun — "35 bileşen" hedef ile 22 dosya kanıtı ayrı |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) |
| K3 | K15 kısıtının kavranması | "Yalnızca K14 üzerinden iletişim" her yerde | dolu (§1/§3/§5) |
| K4 | Veri sınırı | 5 şemanın sahipliği yazılı | dolu (§3/13 · §5.3) |
| K5 | Kapsam dağılımı | 13 kalem durumu | 3 IMPLEMENTED · 5 DESIGN · 5 PLANNED |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G7, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K015 - MEDIA «MÜHÜR»` (+ §A.0 anahtar satırı) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt satırları — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K15 + K1 kısıtı) · `§1` (Offline-First/format) · `§10 #2/#7` · `§11` · `§18` (DB + ek sistem notu) · `§19` · `§22` | dosya yolu (vault read 2026-10-08) | K-matrix + veri sınırı + edge case |
| 3 | `media.coremusic.net/` (22 dosya ls) · grep (ffmpeg/flac/id3/hls/dash → 0) | repo ls/grep (2026-10-08) | durum etiketleri |
| 4 | `.ai/.sql/mysql/{coremusic_media,coremusic_download,coremusic_musics,media_catalog,novasearch}.sql` | dosya yolu (ls 2026-10-08) | DATA_BOUNDARY (tablo adları) |
| 5 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-017 · ADR-019 · ADR-026 · ADR-073 · ADR-074 · ADR-076 · ADR-081 · ADR-092 | ADR (ls teyitli) | karar atıfları |
| 6 | `rules.md R2/R3/R4/R6/R9/R16.3` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` | dosya yolu (vault read) | format + bağımlılık yönü + durum etiketi |
| 7 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | FFmpeg/HLS/DASH/ID3 implementasyonu yok (grep 0) | §4.2 · EK C 8/17 | hedef plan + research (R14) |
| G2 | Download servisi (Node.js+TS) repo'su yok | §4.3.7 · EK C 7 | servis deposunun açılması/KAPI 9 |
| G3 | `media.coremusic.net` test dosyası gözlenmedi | §4.5 · §6.4 | test ağacı ls + suite açma |
| G4 | Oynatıcı kodu yok (ADR-017/019 kararları mevcut, uygulama yok) | §4.3.3 | ADR-017/019 uygulama planı |
| G5 | Kuyruk-taşma/önbellek-TTL politikası tanımsız | §4.3.6/§4.3.8 · §6.5 #7 | politika kararı 👤 veya ADR |
| G6 | DRM kararı vault'ta yok (§4.3.9) | §4.3.9 | research + 👤 karar (R14.4) |
| G7 | Web kanıtı (URL+tarih) — medya/akış iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G8 | `bin/*.php` çıktı biçimi/hata davranışı okunmadı | §6.3 · §6.5 #8 | kod okuması |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2).

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK».
Üst/sağ (H20 yasak yönü): K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» · K018 THERMAL «MEZİT» ·
K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL».
İlişki türü: kardeş katmanlar arası yalnız `refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K014-ag.md | K014 NETWORK «ÇARK» | band-1 setinin parçası |
| b1-K015-medya.md | K015 MEDIA «MÜHÜR» | bu dosya (draft) |
| b1-K016-amplifikator.md | K016 AMPLIFIER «ZAR» | band-1 setinin parçası |
| b1-K017-guc-kaynagi.md | K017 POWER «KANTAR» | band-1 setinin parçası |
| b1-K018-termal.md | K018 THERMAL «MEZİT» | band-1 setinin parçası |
| b1-K019-pcb.md | K019 PCB «ALEV» | band-1 setinin parçası |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
