---
title: "Neva Player — Ürün Gereksinim Dokümanı (PRD) v1.0"
type: prd
category: audio-player
version: 1.0.0
status: draft
date: 2026-10-23
author: Bayram Ali (Vault Steward) — PRD orkestrasyonu: CoreMusic Master Orchestrator
language: Turkish
related_adr: "ADR-004 (ürün mimarisi) · ADR-017 (DSP hard-RT) · ADR-018 (web footer player) · ADR-019 (Per-OS, IAudioBackend) · ADR-074 (radio DB şeması) · ADR-0105 (medya stack — indeks kaydı, dosya diskte YOK) · ADR-106 (radyo + EQ, accepted) · ADR-107 (draft — engine platform)"
tags: [coremusic, prd, neva-player, wasapi-exclusive, gapless, internet-radio, eq, libplacebo]
change_log:
  - "v1.0.0 (2026-10-23): İlk üretim — vault-kanıt temelli. Kaynaklar: accepted/ADR-106 (tam metin) + accepted/ADR-019 (tam metin, §2'ye kadar) + .ai/.decisions/index.md:152 (ADR-0105 kaydı — dosya diskte YOK) + accepted/ADR-074 + research/radio-and-hires.md (84 kaynak) + research/lib-matrix.md (28 kaynak) + reverse/aimp-dump.md + personas + ADR-107 draft."
---

# NEVA PLAYER — ÜRÜN GEREKSİNİM DOKÜMANI (PRD) v1.0

> **Doküman durumu:** Taslak (draft). ADR-0105'in kendisi diskte **yok** (yalnız
> `.ai/.decisions/index.md:152` indeks kaydı) → bu PRD'de ADR-0105'e atıf yapan her madde
> `⚠️ VR (ADR-105 indeks kaydı)` etiketlidir ve ADR dosyası bulunana kadar **kesin sayılmaz**.
> Engine bağımlılığı ayrı bir PRD'de tanımlıdır (Neva Engine PRD v1.0 — kardeş doküman).

---

## §00 Doküman Kontrol

### §00.1 Kimlik

| Alan | Değer |
|---|---|
| Doküman adı | Neva Player — Ürün Gereksinim Dokümanı |
| Sürüm | 1.0.0 |
| Tarih | 2026-10-23 |
| Durum | Draft |
| Sahip | CoreMusic Vault Steward (Bayram Ali) |
| Teknik sahiplik | Embedded Engineer (çekirdek/DSP) · Windows SW Engineer (WASAPI) · UI Designer (player UI) · Security Engineer (URL validator/fuzzing) |
| Kardeş doküman | Neva Engine PRD v1.0 (engine katmanı) |
| Dil | Türkçe (teknik terimler İngilizce) |

### §00.2 Kaynak Envanteri (2026-10-23 disk kanıtları)

| # | Kaynak | Tür | Durum | Kullanım |
|---|---|---|---|---|
| S1 | `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md` (v1.1.1) | ADR, accepted | diskte VAR (tam metin okundu) | §07 radyo, §08 EQ, §12 güvenlik, §11 kapılar |
| S2 | `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` (v1.0.1) | ADR, accepted | diskte VAR (§2'ye kadar okundu) | §03 mimari, §04 per-OS, §09 backend, §10 durum makinesi |
| S3 | `.ai/.decisions/index.md:152` (ADR-0105 kaydı — v1.4.0, 2026-10-20) | ADR indeks kaydı | ⚠️ dosya diskte YOK | §05 medya pipeline, §06 bütçeler, §11 kapı 5.10-5.13, §14 lisans |
| S4 | `.ai/.decisions/index.md:153` (ADR-106 kaydı) + S1 | ADR | VAR | §07/§08 teyit |
| S5 | `.ai/projects/NevaPlayer/research/radio-and-hires.md` (53.7 KB, 84 kaynak) | Araştırma | ADR-106 §1.1'e göre diskte VAR | §07 ICY/HLS, §11 radyo testleri |
| S6 | `.ai/projects/NevaPlayer/research/lib-matrix.md` (28 kaynak) | Araştırma | ADR-106 §1.1'e göre diskte VAR | §14 lisans matrisi (libcurl 8.23.0, mbedTLS, FFmpeg 9.0.2, BASS KULLANMA) |
| S7 | `.ai/projects/NevaPlayer/reverse/aimp-dump.md` | Reverse engineering | VAR (ADR-106 §1.1) | §08 EQ emsali (AIMP 20-band, Catalogs-Radio.ini, bass_hls) |
| S8 | `.ai/.decisions/accepted/ADR-074-radio-database-schema.md` | ADR | VAR (ADR-106 related listesi) | §07.6 radyo kataloğu |
| S9 | `.ai/.personas/yetiskin-kadin/*.md` (en az 5 persona dosyası) | Persona | VAR | §02 persona & senaryolar |
| S10 | `.ai/architecture/neva-player/index.md` (v1.0.0) | Mimari | VAR (ADR-106 §1.1) | §03 katmanlar; ADR-106 §1.1 notu: "ses/yönetim diyagramında radyo satırı yok" → bu PRD'de radyo satırı eklendi |
| S11 | `--/neva-engine/` (C ABI: neva_session.h) | Kod | VAR | §04 engine entegrasyonu (gerçek API yüzeyi) |
| S12 | `.ai/.decisions/draft/ADR-107-...md` | ADR draft | VAR | §04 neva-player exe tanımı, §14 ASIO/VST3 lisans kapısı |

### §00.3 Bilinen çelişkiler ve uyuşmazlıklar (gizlenmez)

| # | Uyuşmazlık | Tutum |
|---|---|---|
| D1 | ADR-0105 "accepted v1.4.0" indekste ama dosya diskte YOK (glob 2026-10-23) | İçerik yalnız indeks kaydı düzeyinde; her atıf `⚠️ VR (ADR-105 indeks kaydı)` etiketli |
| D2 | ADR-019 §2.2b `IAudioBackend` imzası (enumerate/open/start/switchMode/setDeviceRate/stop/kill) ↔ ADR-107/disk `IAudioBackend` (kind/name/enumerate/supported_formats/open/start/stop/close/negotiated_format) | İkisi de `IAudioBackend`; imza setleri farklı evrelerin tasarımları. Bu PRD §09.2'de disk sürümünü (S3 §3.2 = ADR-107 prompt) esas alır, ADR-019 eklerini (switchMode/setDeviceRate/kill) §09.3'te taşır |
| D3 | ADR-106 §2.2b kural 3: 10-band = ISO 63 Hz (VLC 62.5 teyidi v1.1.1'de alındı, Neva ISO'da kalır) | Bağlayıcı — §08.3 |
| D4 | VISION.md "Ay 6 v1.0 · Ay 14 8.1" ↔ §15 milestone planı | VISION = referans; kapılar ADR'lere bağlı |
| D5 | VLC EQ frekansları "dump bitiminde doldurulacak" (ADR-106 §1.4) | §18.4 açık madde |

### §00.4 Kısaltmalar

| Kısaltma | Açılım |
|---|---|
| A/V sync | Audio/Video synchronisation |
| ABORT | Adaptive Bitrate (HLS) |
| AAC | Advanced Audio Coding |
| C ABI | C binary interface |
| DAC | Digital-to-Analog Converter |
| DASH | Dynamic Adaptive Streaming over HTTP |
| DSD | Direct Stream Digital |
| EQ | Equalizer |
| FFI | Foreign Function Interface |
| FLAC | Free Lossless Audio Codec |
| GUI | Graphical User Interface |
| HLS | HTTP Live Streaming |
| ICY | Icecast Cast (Shoutcast uyumlu metaveri protokolü) |
| LUFS | Loudness Units Full Scale (ITU-R BS.1770) |
| M3U/M3U8 | Playlist formatı / HLS playlist |
| OPUS | Opus ses codec'i |
| PCM | Pulse-Code Modulation |
| PEQ | Parametric Equalizer |
| RBJ | Robert Bristow-Johnson (biquad cookbook) |
| RT | Real-Time |
| SPSC | Single-Producer Single-Consumer |
| SSRF | Server-Side Request Forgery |
| TLS | Transport Layer Security |
| WASAPI | Windows Audio Session API |

---

## §01 Vizyon & Ürün Özeti

### §01.1 Neva Player nedir?

Neva Player, CoreMusic platformunun **per-OS masaüstü müzik/video oynatıcısıdır**
(ADR-019: Windows ASIO/WASAPI · macOS CoreAudio · Linux ALSA/PipeWire). C++20 Neva Engine
çekirdeği üzerine kurulur; medya stack'i (ADR-0105 indeks kaydı) açık kaynak decode
(dr_libs + libdsd + ffmpeg-gateway) + WASAPI Exclusive bit-perfect çıkış + libplacebo ile
4K/HDR video + 31-bant parametrik EQ'dan oluşur. ADR-106 ile internet radyosu
(ICY/Shoutcast özel tuner + HLS FFmpeg gateway) ve seçilebilir bant EQ (10/15/20/31) eklenmiştir.

### §01.2 Temel değer önerisi

| # | Öneri | Dayanak |
|---|---|---|
| P-1 | OS mikserini baypas eden bit-perfect ses (yerel dosya) | ADR-0105 §5.1 null-test kapısı (indeks kaydı ⚠️) · ADR-019 |
| P-2 | Hi-res: 32-bit float / 192 kHz hedefi | ADR-107 K3 · ADR-0105 indeks kaydı |
| P-3 | Gapless: MP3(LAME/Xing), FLAC(seektable), Opus(granule), DSD(DoP/DSF marker) | ADR-0105 AK-5 (indeks kaydı) |
| P-4 | İnternet radyosu: ICY v1+v2 + HLS, özel tuner, reconnect backoff | ADR-106 §2.2a |
| P-5 | Kullanıcı-seçilebilir bant EQ (10/15/20/31, maks 31, ISO 1/3-oktav grid) | ADR-106 §2.2b |
| P-6 | Düşük gecikme: ASIO <10 ms / WASAPI <20 ms bütçesi | ADR-006 (ADR-019 §1.4) |
| P-7 | 4K/HDR video yolu (libplacebo) + A/V sync P95 ≤20 ms | ADR-0105 indeks kaydı (kapı 5.11) |
| P-8 | Hostile-input dayanıklılığı: format başına ≥1000 fuzz corpus, 0 crash | ADR-0105 kapı 5.13 (indeks kaydı) |

### §01.3 Ürün ailesindeki yeri

```text
CoreMusic platformu
  ├─ Web SPA (PHP 8.x + Vanilla JS) — web footer player (ADR-018, durum makinesi semantiği ortak)
  └─ Neva Player (masaüstü, per-OS) ← BU PRD
       ├─ Neva Engine (neva-core statik lib + neva_session C ABI) — kardeş PRD
       ├─ Medya stack (decode/resample/gateway) — §05
       └─ UI katmanı (Toolkit UNKNOWN — §18.1)
```

### §01.4 Başarı tanımı

1. §10'daki NE-PLR gereksinimlerinin tamamı ADR-019/106 + ADR-0105 indeks kaydına izlenebilir (§17).
2. §11 test planı kapı 5.10 (gecikme ölçümü), 5.11 (A/V sync eşiği), 5.12 (gapless format
   vektörü), 5.13 (hostile input fuzzing) kapılarını mutlaka kapsar.
3. §14 lisans matrisi: mpv REDDEDİLDİ (GPL-2.0 copyleft + black-box) kaydı korunur; BASS KULLANMA; FFmpeg LGPL build.
4. Güvenlik: URL Validator tek kapı (ADR-106 §2.2a kural 6) + metadata sanitizasyonu (kural 7) + parser limitleri (kural 8) §12'de eksiksiz.

---

## §02 Kapsam

### §02.1 Kapsam İÇİ

| # | Kapsam alanı | ADR dayanağı |
|---|---|---|
| I-1 | Per-OS mimari: tek çekirdek + ince adapter (IAudioBackend) | ADR-019 §2 |
| I-2 | Medya pipeline: decode (dr_libs/libdsd/ffmpeg-gateway) → soxr → SPSC ring → DSP → backend | ADR-0105 indeks kaydı ⚠️ · ADR-106 §2.2a |
| I-3 | WASAPI Exclusive bit-perfect çıkış + format negosyasyonu | ADR-0105 indeks kaydı ⚠️ · ADR-106 §1.3 |
| I-4 | Gapless oynatma (dosya→dosya) + gapless radio (akış→akış) | ADR-0105 AK-5 ⚠️ |
| I-5 | İnternet radyosu: ICY/Shoutcast v1+v2 özel tuner + HLS (FFmpeg gateway) + M3U/M3U8 | ADR-106 §2.2a |
| I-6 | EQ: 31-band ISO 1/3-oktav grid, izinli bant sayıları 10/15/20/31, preset biçimi | ADR-106 §2.2b |
| I-7 | Radyo kataloğu: yerel SQLite (ADR-074 şeması) + Radio Browser API keşfi (opsiyonel) | ADR-106 §2.2a kural 3 |
| I-8 | Bütçeler: sistem RAM ≤400 MB, gecikme <20 ms (blok ≥1024@48k yasak), VRAM ~400-700 MB (video), A/V sync P95 ≤20 ms | ADR-0105 indeks kaydı (§2.8-2.10) ⚠️ |
| I-9 | Durum makinesi: STOPPED/PLAYING/PAUSED/LOADING/ERROR (web footer player ile aynı semantik) | ADR-019 §1.4 (ADR-004/018) |
| I-10 | Güvenlik: URL Validator, SSRF/DNS-rebinding koruması, metadata XSS sanitizasyonu, parser limitleri, fuzzing | ADR-106 kural 6-8 · ADR-0105 kapı 5.13 ⚠️ |
| I-11 | GUI (neva-player UI + neva-eq ayrı pencere + neva-matrix 8×8) | ADR-107 K2 / ADR-0105 indeks kaydı |

### §02.2 Kapsam DIŞI

| # | Dışı | Sahibi |
|---|---|---|
| O-1 | Streaming **sunucusu** (istemci tunerüz — sunucu değil) | ADR-106 §1.4 scope |
| O-2 | DASH radyo yayını | ADR-106 kural 1 — Aşama 2 |
| O-3 | Sanal ses cihazı sürücüsü | ADR-107 §5 Faz 5 (ayrı iş) |
| O-4 | Web SPA player'ı (footer player) | ADR-018 — aynı durum makinesi semantiği, ayrı kod yolu |
| O-5 | FDK-AAC | ADR-106 kural 9 — lisans teyidi yok, teyide kadar kullanılmaz |
| O-6 | BASS / bass_hls | ADR-105 lisans kararı (KULLANMA) |
| O-7 | libmpv "hibrit fallback" | ADR-0105 v1.1.1 REDDEDİLDİ (GPL-2.0 copyleft + black-box) |
| O-8 | Kullanıcının tek tek band frekansını ayarlaması (serbest parametrik) | ADR-106 kural 7 — grafik EQ grid sabit; parametrik ayrı mod |
| O-9 | iOS/Android | Yok — per-OS masaüstü (ADR-019) |

### §02.3 Bağımlılıklar

| Bağımlılık | Yön | Not |
|---|---|---|
| Neva Engine (neva-core + neva_session) | Player → Engine | Kardeş PRD; C ABI sınırı §04.2 |
| ADR-107 engine platform onayı | Engine faz kapıları | Player M0-M2, engine M1-M2'ye bağımlı |
| CoreMusic web platformu (DB, auth) | Player ↔ Platform | Radyo kataloğu ADR-074 şeması; çevrimdışı öncelik (SQLite) |
| ASIO SDK lisans kararı (U1) | Release kapısı | ADR-107 K4 — `UNKNOWN` |
---

## §03 Persona & Kullanıcı Senaryoları

### §03.1 Persona envanteri (S9 — `.ai/.personas/yetiskin-kadin/` diskteki gerçek persona dosyaları)

| # | Persona | Dosya | Profil özeti (dosya içeriğinden) | Neva Player relevance |
|---|---|---|---|---|
| 1 | Ayşe Yılmaz (Romantik) | `ayse-yilmaz-romantik.md` | Türkçe pop 80-100 BPM, duygusal dinleme | Kütüphane + çalma listesi + EQ preset ("Sıcak") |
| 2 | Buket Kaya (Enerjik) | `buket-kaya-enerjik.md` | Dance 110-135 / House 110-128 / Electro 90-130 BPM, parti modu | Radyo (dance istasyonları) + EQ preset ("Bass Boost") + gapless |
| 3 | Ceyda Demir (Melankolik) | `ceyda-demir-melankolik.md` | Türkçe pop 80-120 BPM (çocuğuyla ortak dinleme), rap boom-bap 85-95 / trap 70-110 | Sessiz mod, zamanlayıcı, düşük ses zararlı koruması `⚠️ VR (zamanlayıcı gereksinimi bu PRD'nin ekidir — persona dosyasından türetildi)` |
| 4 | Deniz Öztürk (Profesyonel) | `deniz-ozturk-profesyonel.md` | Pop 80-100 · House 110-128 BPM, ofis/arka plan akışları | Bit-perfect hi-res + WASAPI exclusive + 31-band EQ + çoklu cihaz |
| 5 | Ebru Arslan (Anne) | `ebru-arslan-anne.md` | Pop 80-120 BPM, aile dinleme | Basit UI, kilitli mod `⚠️ VR (türetme)`, çevrimdışı kütüphane |

> Not: BPM aralıkları persona dosyalarında `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`
> etiketlidir (turkipedia.com/Beats_per_minute + nevamuzik.com.tr). Bu PRD BPM değerlerini
> **kullanıcı senaryosu zenginleştirici** olarak kullanır; DSP'ye dayanak yapmaz.

### §03.2 Senaryolar (persona × kritik yol)

| ID | Persona | Senaryo | Kritik gereksinimler |
|---|---|---|---|
| SC-01 | Deniz Öztürk | Harici ASIO DAC'e 192 kHz/32f FLAC bağlar, exclusive modda dinler | NE-PLR-041 (bit-perfect), NE-PLR-052 (format negosyasyon), NE-PLR-061 (gecikme <10 ms ASIO) |
| SC-02 | Buket Kaya | Dance radyo istasyonuna bağlanır, EQ "Bass Boost" 20-band preset'i uygular, istasyon kopunca otomatik reconnect | NE-PLR-101 (ICY), NE-PLR-115 (backoff), NE-PLR-131 (EQ preset), NE-PLR-152 (URL validator) |
| SC-03 | Ayşe Yılmaz | FLAC albüm sırasını gapless dinler, dosyalar arası boşluk duymaz | NE-PLR-071 (gapless AK-5), NE-PLR-074 (dosya→dosya geçiş) |
| SC-04 | Ceyda Demir | Çocuğuyla birlikte düşük ses seviyesinde dinler, gece zamanlayıcı ile uyur | NE-PLR-141 (LUFS normalizasyon), NE-PLR-162 (zamanlayıcı) |
| SC-05 | Ebru Arslan | Çevrimdışı uçak modunda FLAC kütüphanesini dinler | NE-PLR-081 (çevrimdışı SQLite kütüphane), NE-PLR-071 |
| SC-06 | Deniz Öztürk | 4K HDR konser videosunu libplacebo ile oynatır, A/V sync ölçer | NE-PLR-091 (libplacebo), NE-PLR-093 (A/V sync P95 ≤20 ms — kapı 5.11) |
| SC-07 | Buket Kaya | DSD (DSF) dosyasını DoP üzerinden DAC'ine gönderir | NE-PLR-076 (DoP/DSF marker continuity), NE-PLR-052 |
| SC-08 | Tümü | WASAPI exclusive başka bir uygulama tarafından işgal edilir | NE-PLR-062 (fallback → shared → ASIO yoksa durum makinesi ERROR/uyarı), §09.5 |
| SC-09 | Deniz Öztürk | AI asistan üzerinden (`neva-mcp`) oynatmayı durdurur, EQ gain değiştirir | NE-PLR-171 (C ABI event), engine NE-ENG-240 |
| SC-10 | Güvenlik testçisi | Kullanıcı zararlı M3U8 playlist URL'si girer (SSRF, billion laughs, 10 GB segment) | NE-PLR-152/153/154 (validator + parser limitleri), §12 |

### §03.3 Kullanıcı hikâyesi formatı (özet — detay §10)

| ID | Hikâye | Öncelik |
|---|---|---|
| US-01 | Bir dinleyici olarak, OS mikserinin sesi bozmadığından emin olmak isterim (bit-perfect) | MUST |
| US-02 | Bir dinleyici olarak, albüm geçişlerinde boşluk/kopukluk duymamak isterim (gapless) | MUST |
| US-03 | Bir dinleyici olarak, internet radyosunda da EQ kullanabilmek isterim | MUST |
| US-04 | Bir dinleyici olarak, band sayısını 10/15/20/31 arasından seçebilmek isterim | MUST |
| US-05 | Bir hi-res dinleyicisi olarak, 192 kHz/32f dosyayı donanım formatına birebir aktarmak isterim | MUST |
| US-06 | Bir video izleyicisi olarak, ses ve görüntünün senkron kalmasını isterim | SHOULD |
| US-07 | Bir kullanıcı olarak, uygulamanın 400 MB RAM'i aşmamasını isterim | MUST |
| US-08 | Bir kullanıcı olarak, bozuk/malicious dosya uygulamanın çökmesine yol açmasın isterim | MUST |
| US-09 | Bir geliştirici olarak, C ABI üzerinden kendi arayüzümü bağlamak isterim | SHOULD |
| US-10 | Bir AI-agent geliştiricisi olarak, MCP üzerinden oynatıcıyı kontrol etmek isterim | MAY |

---

## §04 Mimari (Player katmanı)

### §04.1 Katman diyagramı (ADR-019 §2 + ADR-106 §2.2a — bağlayıcı)

```text
┌──────────────────────────────────────────────────────────────────────┐
│ UI KATMANI (neva-player GUI · neva-eq · neva-matrix — toolkit §18.1) │
│  durum makinesi: STOPPED/PLAYING/PAUSED/LOADING/ERROR (ADR-019)      │
├──────────────────────────────────────────────────────────────────────┤
│ UYGULAMA KATMANI (kütüphane, queue, gapless planlayıcı, radyo katalog│
│  SQLite (ADR-074 şeması), zamanlayıcı, preset yönetimi)             │
├──────────────────────────────────────────────────────────────────────┤
│ C ABI — neva_session (engine kardeş PRD §05): opaque handle,         │
│  error-code, api_version handshake, char16_t path, metadata event    │
├──────────────────────────────────────────────────────────────────────┤
│ NEVA ENGINE (neva-core statik lib — kardeş PRD)                     │
│  DSP: 31-band biquad EQ + limiter · spsc_ring · BackendRegistry      │
├──────────────────────────────────────────────────────────────────────┤
│ MEDYA STACK (player katmanı — bu PRD §05)                            │
│  decode: dr_libs (WAV/FLAC) · libdsd (DSD) · ffmpeg-gateway (MP3/    │
│  AAC/Opus/OGG/video demux + HLS demux) · soxr resample (producer)    │
├──────────────────────────────────────────────────────────────────────┤
│ IAudioBackend (engine) → ASIO / WASAPI Excl/Shared / WDM-KS /        │
│  PipeWire / ALSA / CoreAudio / Null                                  │
├──────────────────────────────────────────────────────────────────────┤
│ AĞ (tuner — non-RT producer thread): libcurl + mbedTLS + ICY parser  │
│  (icy_parser.h — engine çekirdeği) + FFmpeg HLS gateway              │
└──────────────────────────────────────────────────────────────────────┘
```

### §04.2 Thread topolojisi (ADR-106 §2.2a şeması — bağlayıcı; ADR-0105 §2.5'e atıfla ⚠️)

```text
[TunerThread — non-RT producer]              [RT render callback]
  URL → URL Validator (tek kapı)
    → libcurl HTTP GET (redirect ≤5, timeout 10s,
       TLS mbedTLS, HTTP09Allowed=0)
      ├─ icy-* header / Shoutcast v2 XML
      ├─ HLS (m3u8): FFmpeg gateway demux
      └─ ICY: icy-metaint × N byte → text blok
  → decoder (native aac/flac/mp3/opus)
  → soxr (producer-side, cihaz formatına)
  → SPSC ring (PCM)
  [non-RT yan thread] R128 loudness sidechain
  [non-RT] metadata sanitizasyon → lock-free kuyruk
  → C ABI metadata event                    ring → biquad EQ zinciri
  Reconnect: backoff 1s→30s cap, ±%20 jitter;  (aktif bant maskesi + ramp)
    3 başarısız → state=ERROR                 → true-peak limiter
  Ring STARVE → fade-to-silence (≤50ms ramp)  → IAudioBackend cihazına yaz
  RING OVERFLOW → drop (akış sürekli)           [master clock = CİHAZ]
  Reconnect kutusu: prebuffer 2-4s (LOADING)
    → decoder reset → sessizlik → fade-in 20ms
```

> **Debate kaydı (ADR-106 v1.0 → v1.1):** v1.0 şeması DSP'yi producer'a koymuştu (EQ ring'den
> önce) — architect "ring invariant" ve embedded "İ1" itirazı ADR-0105 §2.5'e dayanarak
> düzeltti: **biquad/limiter RT callback'de, R128 non-RT.** Bu şema bağlayıcıdır.

### §04.3 Dosya/yol oynatma pipeline'ı (yerel dosya — ADR-0105 indeks kaydı ⚠️)

```text
Dosya yolu (char16_t) → neva_session_open_file (C ABI)
  → Demux (FFmpeg gateway — opsiyonel; FLAC/WAV/dr_libs doğrudan)
  → Decode: FLAC/MP3/Opus/AAC (FFmpeg native) · WAV (dr_libs) · DSD/DSF/DFF (libdsd)
  → Xing/LAME delay+padding · FLAC SEEKTABLE · Opus granule okuma (gapless AK-5)
  → soxr resample (producer-side, cihaz formatına; mismatch F3 kuralı)
  → SPSC ring (PCM float)
  → RT callback: EQ (31-band grid + bypass maskesi + ramp) → true-peak limiter
  → IAudioBackend.write (WASAPI Exclusive bit-perfect / ASIO / ...)
  → Null-test kapısı (yalnız yerel dosya — kapı 5.1, CI guard ile kanıtlanır)
```

### §04.4 Engine entegrasyonu (gerçek C ABI yüzeyi — S11 disk kanıtı)

| C ABI fonksiyonu (neva_session.h) | Player kullanımı |
|---|---|
| `neva_session_api_version()` | Açılışta handshake; `0x00010000` (1.0.0) — ABI kıran değişim major artışı ister |
| `neva_session_create(&out)` | Oynatıcı oturumu |
| `neva_session_destroy(s)` | Kapanış |
| `neva_session_open_file(s, char16_t* path)` | Kütüphane/queue seçimi (Windows Unicode yolu) |
| `neva_session_set_output_device(s, uint32_t index)` | Cihaz seçimi UI |
| `neva_session_play/pause/stop(s)` | Transport kontrolleri |
| `neva_session_set_eq_gain(s, uint32_t band, double db)` | EQ UI — band 0..30, her iki kanala uygulanır (62 biquad yolu: 31×2); per-kanal erişim C++ `EqChain::set_gain(band, ch, db, ramp_ms)` |

**Eksik yüzey (player'ın engine'den talep ettiği genişletmeler — kardeş PRD'ye bağımlı):**
metadata event callback · gapless dosya→dosya geçişi (queue prefetch) · radyo tuner
kontrolleri (connect/disconnect/status) · cihaz hot-plug olayı · LUFS okuması · true-peak
okuması. Tümü §10 NE-PLR-17x + kardeş PRD NE-ENG-2xx maddelerine bağlı.

### §04.5 Durum makinesi (ADR-019 §1.4 — web footer player (ADR-018) ile aynı semantik)

```text
STOPPED ──open_file/connect──▶ LOADING ──prebuffer/dosya hazır──▶ PLAYING
PLAYING ──pause──▶ PAUSED ──play──▶ PLAYING
PLAYING/PAUSED ──stop/kapat──▶ STOPPED
LOADING ──başarısız (decode/connect)──▶ ERROR
PLAYING ──ağ kopması (radyo)──▶ LOADING (reconnect; 3 başarısız → ERROR)
PLAYING ──backend kaybı (device loss)──▶ fallback zinciri (§09.5) → PLAYING | ERROR
ERROR ──open_file/connect/yeniden dene──▶ LOADING
```

| Geçiş | Tetik | Uygulama katmanı |
|---|---|---|
| STOPPED→LOADING | `open_file` / tuner connect | C ABI çağrısı + decoder prefetch |
| LOADING→PLAYING | prebuffer 2-4 s (radyo) / ilk ring doldurma (dosya) | App katmanı; ring threshold |
| PLAYING→PAUSED | pause; ring drop edilmez (sayaç duraklar) | Engine session |
| PLAYING→LOADING | radyo ağ kopması | Tuner backoff döngüsü |
| *→ERROR | 3 başarısız reconnect · decode hatası · backend tümü başarısız | Uygulama katmanı; UI mesajı (§13) |
---

## §05 Medya Pipeline (Ayrıntı)

### §05.1 Decode matrisi (ADR-0105 indeks kaydı ⚠️ + ADR-106 kural 9)

| Format | Kütüphane | Lisans | Bit-perfect kapsamı | Not |
|---|---|---|---|---|
| WAV/PCM | dr_libs (dr_wav) | Public domain / MIT-tarzı `⚠️ VR (tam lisans metni lib-matrix'te)` | Evet (null-test) | Doğrudan decode |
| FLAC | dr_libs (dr_flac) | Aynı | Evet (null-test) | SEEKTABLE gapless |
| MP3 | dr_libs (dr_mp3) / FFmpeg native | Aynı / LGPL | PCM çıktısı "bit-perfect" iddiası **kapsam dışı** (lossy) | LAME/Xing delay+padding gapless |
| Opus | FFmpeg native | LGPL build | Kapsam dışı (lossy) | Granule gapless |
| AAC (radyo) | **FFmpeg native `aac`** | LGPL | Kapsam dışı | FDK-AAC **kullanılmaz** (lisans teyidi yok — ADR-106 kural 9) |
| OGG Vorbis | FFmpeg native | LGPL | Kapsam dışı | `⚠️ VR (ADR-0105 indeks metninde OGG geçmiyor — kapsam kararı)` |
| DSD (DSF/DFF) | libdsd | `UNKNOWN` (ADR-0105 v1.2.0: "libdsd UNKNOWN") | DoP/DSF marker continuity (AK-5) | Release kapısı K4 |
| Video (MP4/MKV...) | FFmpeg demux/decode | LGPL build (`--enable-gpl` yasak) | N/A | libplacebo render §05.4 |
| HLS | FFmpeg `hls` demuxer | LGPL | N/A (radyo/video akış) | Protocol whitelist http/https |

### §05.2 Bit-perfect yolu (yerel dosya — kapı 5.1 ⚠️ ADR-0105 indeks)

```text
Dosya (FLAC/WAV 24/96 vb.) → decode (kayıpsız) → [soxr BYPASS — format eşitse]
  → SPSC ring → [EQ BYPASS — flat preset] → [limiter BYPASS — pasif]
  → WASAPI Exclusive / ASIO (donanım pin formatı birebir)
CI KAPISI: WAV hash byte-eşitlik (giriş == çıkış); negotiated_format() == target ZORUNLU
```

| # | Kural | Kaynak |
|---|---|---|
| B1 | Null-test **yalnız yerel dosyalarda**; radyoda CI'da inverted guard ile **engellenir** | ADR-106 kural 4 |
| B2 | "Yaklaşık eşitlik" ile PASS yasak; byte-eşitlik zorunlu | ADR-107 K3 (kardeş PRD) |
| B3 | UI'da bit-perfect rozeti yalnız `negotiated_format() == dosya formatı` && EQ flat && limiter bypass ise gösterilir `[ÖNERİ — NE-PLR-043]` | Bu PRD |
| B4 | DSD→DAC: DoP (DSD over PCM) marker continuity; native DSD çıkış `⚠️ VR (donanım/ASIO DSD desteği doğrulanmadı)` | ADR-0105 AK-5 indeks |

### §05.3 Gapless (AK-5 format vektörü — ADR-0105 kapı 5.12 ⚠️ indeks)

| Format | Gapless mekaniği | Test vektörü |
|---|---|---|
| MP3 (LAME/Xing) | 12-bit delay+padding okuma; giriş/çıkışta padding kırpma | Dosya→dosya; beklenen: ortak sınırda 0 örnek boşluk/faz kayması |
| FLAC | SEEKTABLE + ilk/son frame padding | Aynı |
| Opus | Granule pozisyonu | Aynı |
| DSD (DoP/DSF) | Marker continuity (DoP işaret akışı) | Aynı |
| Radyo (akış→akış) | Reconnect sonrası konum koruma; prebuffer 2-4 s | Stream→stream; DAC loopback korelasyon ±5 ms; >20 ms = FAIL |

Kabul (kapı 5.12): DAC loopback korelasyonu ±5 ms içinde; dosya→dosya boşluk = 0 frame
`⚠️ VR (eşikler ADR-0105 indeks kaydından; ADR dosyası diskte YOK)`.

### §05.4 Video yolu (libplacebo — ADR-0105 indeks ⚠️)

| # | Madde | Değer | Durum |
|---|---|---|---|
| 1 | Render | libplacebo (Vulkan/GL) | İndeks kaydı |
| 2 | HDR | 4K/HDR hedefi | İndeks kaydı |
| 3 | VRAM bütçesi | ~400-700 MB (derived — §2.8) | İndeks kaydı v1.2.1 |
| 4 | VRAM ölçüm | Bench: GTX 1660 · 6144 MB · sürücü 610.62 · Win10 19045 · masaüstü baseline 1511 MiB/1706 MB | İndeks kaydı v1.3.2; §7.5: "NevaPlayer henüz derlenmediği için peak ölçümü açık kalır" |
| 5 | Tükenme merdiveni | V0-V5 (VRAM pressure ladder) | İndeks kaydı |
| 6 | Kapı 5.6 | VRAM peak ölçüm kapısı | İndeks kaydı |
| 7 | A/V sync | Master clock = cihaz (ses); video audio'a hizalanır (ADR-106 kural 11); kapı 5.11: P95 ≤20 ms | İndeks kaydı |

### §05.5 Format negosyasyonu (WASAPI Exclusive — ADR-106 §1.3 + ADR-019 spec)

| # | Kural | Kaynak |
|---|---|---|
| N1 | Bit-perfect için format cihazla **birebir** eşleşmeli: 32f → 32i → 24-in-32 negosyasyon sırası | ADR-106 §1.3 (radio-and-hires §3-§4) |
| N2 | Cihazlar float reddedebilir → negosyasyon başarısızsa sıradaki formata düş | Aynı |
| N3 | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` kurtarma yolu + periyot ayarı yazılacak | Aynı |
| N4 | `IsFormatSupported` iki deneme: WAVEFORMATEX → WAVEFORMATEXTENSIBLE | ADR-107 prompt (S3 §3.4) |
| N5 | exclusive↔shared otomatik geçiş durum makinesi | ADR-106 §1.3 |

---

## §06 Bütçeler

> **Kaynak uyarısı:** Bu bölümdeki tüm sayısal eşikler ADR-0105 v1.3.x indeks kaydından
> (`index.md:152`) alınmıştır; ADR-0105 dosyası diskte YOK → her madde `⚠️ VR (ADR-105
> indeks kaydı)` etiketlidir ve release öncesi ADR dosyası bulunana/üretilene kadar
> **kesin eşik sayılmaz** (bileşik belge: kapı 5.10-5.13).

### §06.1 Sistem RAM bütçesi (§2.9 indeks)

| Kalem | Bütçe | Not |
|---|---|---|
| Toplam sistem RAM (player) | **≤400 MB** | İndeks v1.3.0 |
| Decode buffer | `⚠️ VR (alt kalem payı indeks metninde parçalı)` | |
| Ring buffer | `⚠️ VR` | |
| Video (libplacebo) | VRAM ayrı; sistem RAM'e dahil payı `⚠️ VR` | |
| UI | `⚠️ VR` | |

### §06.2 Gecikme bütçesi (§2.10 indeks — bağlayıcı çerçeve)

| Kural | Değer |
|---|---|
| Toplam gecikme | **<20 ms** (WASAPI); ASIO hedefi <10 ms (ADR-006 — ADR-019 §1.4) |
| Blok boyutu | **≥1024 @48 kHz BLOKLANDI** (kaynak: indeks §2.10) |
| Merdiven | L1-L4 (küçükten büyüğe blok/latency seçenekleri) `⚠️ VR (L1-L4 değerleri indeks metninde yok)` |
| Ölçüm | Kapı 5.10 (gecikme ölçümü) |
| A/V sync | P95 ≤20 ms (kapı 5.11) |

### §06.3 VRAM bütçesi (§2.8 indeks v1.2.1-1.3.2)

| Kalem | Değer |
|---|---|
| Tahmini bütçe | ~400-700 MB (derived) |
| Kapanan boşluk | P010 düzeltmesi 12,4 → 24,9 MB/kare |
| Bench donanımı | GTX 1660 · 6144 MB · sürücü 610.62 · Win10 19045 (3 kaynak teyitli) |
| Masaüstü baseline | 1511 MiB / 1706 MB |
| Uyarı | Bench ≠ hedef-sınıf (1060 3 GB'ta 600 MB = %19,5) |
| Açık madde | §7.5: NevaPlayer henüz derlenmediği için peak ölçümü **açık** |

### §06.4 CPU bütçesi

| İş | Bütçe | Kaynak |
|---|---|---|
| EQ (31-band, en kötü durum) @192 kHz | ≤%5 tek çekirdek | ADR-0105 §5.6 ⚠️ (ADR-106 §1.3 hesabı ~59.5 M MAC/s ile uyumlu) |
| Decode (FLAC 192k/24) | `⚠️ VR` | |
| Video (4K HDR) | `⚠️ VR` | |
| Fuzzing CI | ≥1000 corpus/format, CI süresi ≤10 dk | ADR-0105 kapı 5.13 ⚠️ |

### §06.5 Bütçe ihlali davranışları

| İhlal | Davranış |
|---|---|
| RAM eşiği | UI uyarı + video kalitesi düşürme (V merdiveni) `⚠️ VR (davranış indeks metninde yok — NE-PLR-065 önerisi)` |
| Gecikme eşiği | L merdiveninden büyük bloğa geçiş (ses kesintisizliği korunur) `[ÖNERİ]` |
| VRAM eşiği | V0-V5 tükenme merdiveni (downscale/HDR kapatma/sessiz video) `⚠️ VR` |
| CPU eşiği | Benchmark kapısı FAIL — release bloke (ADR-0105 §5.6) |
---

## §07 İnternet Radyosu (ADR-106 — bağlayıcı)

### §07.1 Protokol sırası (ADR-106 §2.2a kural 1)

| Sıra | Protokol | Uygulama | Durum |
|---|---|---|---|
| 1 (birincil) | ICY/Shoutcast v1+v2 | **Özel parser** (`icy_parser.h` çekirdeği IMPLEMENTED — S7) | M3 |
| 2 (ikincil) | HLS (m3u8) | FFmpeg gateway demux (protocol whitelist http/https) | M3 |
| 3 (Aşama 2) | DASH | FFmpeg demux | M5+ — daha az yaygın (BBC HLS/DASH'a geçti, ICY'yi bıraktı) |

### §07.2 ICY durum makinesi (radio-and-hires.md pseudokodu — ADR-106 §1.3)

```text
CONNECT → GET (icy-metadata:1 gönder)
  → HTTP 200 → icy-metaint oku (varsayılan RED — §07.5 kural 6 aralığı)
  → N byte veri bloğu → ring'e yaz
  → icy-metaint byte'lık metin bloğu → StreamTitle/StreamUrl ayrıştır
  → tekrar (N byte + metin bloğu döngüsü)
HATA: timeout (10s) / bağlantı kopması → Reconnect döngüsü
```

### §07.3 Reconnect politikası (ADR-106 §1.3 — v1.1 doğrulanmış değerler)

| Parametre | Değer |
|---|---|
| Backoff | Üstel: **1s → 30s cap, ±%20 jitter** (v1.0'daki "0.5s→8s" v1.1'de düzeltildi) |
| Başarısızlık eşiği | **3 başarısız → state=ERROR** |
| Reconnect kutusu | Prebuffer **2-4 s** (LOADING) → decoder reset → sessizlik → **fade-in 20 ms** |
| Ring STARVE | Fade-to-silence ≤50 ms ramp |
| RING OVERFLOW | Drop (akış sürekli) |
| Master clock | **Cihaz** (ADR-106 kural 11 — v1.0 düzeltmesi) |

### §07.4 Codec matrisi (radyo — ADR-106 §1.3)

| Codec | Yaygınlık | Karar |
|---|---|---|
| AAC-LC | Yaygın (44.1/48k) | FFmpeg native `aac` (LGPL) |
| HE-AAC v1/v2 | Yaygın | Aynı |
| Opus | 48k | FFmpeg native |
| MP3 | Yaygın | FFmpeg native |
| FLAC (radyo) | Nadir | Desteklenir |
| FDK-AAC | Muhtemel daha iyi AAC | **KULLANMA** — lisans teyidi yok (kural 9; architect S4) |

### §07.5 Güvenlik kuralları (ADR-106 kural 6-8 — §12'ye çapraz; bağlayıcı)

| # | Kural | Ayrıntı |
|---|---|---|
| K6 | URL Validator (tek kapı) | Tüm URL'ler (tuner + FFmpeg gateway) tek validator'dan geçer: **her redirect hop'ta yeniden + post-connect gerçek IP kontrolü (DNS rebinding)**; HTTPS zorunlu, http yalnız kullanıcı açık onayıyla (domain başına); **private/loopback/link-local aralıklar default RED (SSRF)**; `userinfo` (user:pass) log'a/URL'ye yazılmaz → `[REDACTED]`; curl `HTTP09Allowed=0`; redirect zinciri **≤5**; playlist boyutu **≤1 MB** |
| K7 | Metadata sanitizasyonu (XSS) | UTF-8 doğrula (hatalı → drop), kontrol karakterleri strip, **256 bayt cap**, render'da HTML-escape (native ve web UI); `StreamUrl` **hyperlink olarak render edilmez** (yalnız düz metin) |
| K8 | Parser limit tablosu | M3U8 entry **≤1000** · playlist derinlik **≤1** (EXT-X ile iç içe yasak) · segment **≤50 MB** · XML'de DTD/external entity **kapalı** (billion-laughs) · `icy-metaint` **32 B – 1 MiB** aralığı dışında → stream RED · ICY text blok **≤1 MB** |

### §07.6 Katalog & keşif (ADR-106 kural 3)

| # | Kural |
|---|---|
| 1 | Yerel kütüphane: **SQLite** — ADR-074 radio DB şemasıyla uyumlu |
| 2 | Radio Browser API (açık kaynak dizin) opsiyonel keşif — **yalnız https mirror + `url_resolved` validator** |
| 3 | Emsal: AIMP `Catalogs-Radio.ini` (katalog ayrı katman) |

### §07.7 Radyo DSP kapı ayrımı (ADR-106 kural 4 — bağlayıcı)

| Yol | DSP | Bit-perfect null-test |
|---|---|---|
| Yerel dosya | EQ + limiter (RT callback) | **UYGULANIR** (kapı 5.1) |
| Radyo (lossy ağ akışı) | Aynı EQ + limiter | **UYGULANMAZ** — CI'da inverted guard ile kanıtlanır (ADR-106 §5.1 Ş4) |

---

## §08 EQ (Kullanıcı Sözleşmesi — ADR-106 §2.2b)

### §08.1 Bant grid'i (bağlayıcı — 31-band ISO 1/3-oktav)

20 · 25 · 31.5 · 40 · 50 · 63 · 80 · 100 · 125 · 160 · 200 · 250 · 315 · 400 · 500 · 630 ·
800 · 1000 · 1250 · 1600 · 2000 · 2500 · 3150 · 4000 · 5000 · 6300 · 8000 · 10000 ·
12500 · 16000 · 20000 (Hz)

### §08.2 İzinli bant sayıları ve alt kümeler

| Seçim | Alt küme | Not |
|---|---|---|
| **10** | 31.5 · 63 · 125 · 250 · 500 · 1000 · 2000 · 4000 · 8000 · 16000 | ISO 1/3-oktav alt kümesi (Winamp/VLC geleneğinde 62.5 Hz yerine ISO 63 — fark ~14 sent; VLC preset'leri kendi setiyle okunur, zorla map edilmez) |
| **15** | Eşit log-aralık alt küme | `⚠️ VR — 2/3-oktav standart listesi web kaynağı ile teyit edilmedi` |
| **20** | AIMP emsal aralığı (Band0..19) | `⚠️ VR — gerçek frekans listesi AIMP dump'da kanıtlanmadı; eq_grids'de sabitlenir` |
| **31** | Tam grid (yukarıdaki) | En kötü durum benchmark'ı |

### §08.3 Uygulama kuralları (ADR-106 §2.2b kural 4-7)

| # | Kural |
|---|---|
| 1 | Biquad bütçesi **sabit allocate: 31×2 = 62 (cascade 124)**; daha düşük bant sayılarında fazlalar bypass maskesiyle pasif |
| 2 | Bant sayısı değişimi: RT dışı maske hesabı → atomik yayın → callback 5-20 ms ramp ile uygular (tahsis yok, zincir takası yok) |
| 3 | Cascade mod değişimi (1↔2 biquad/bant): nadir → fade-out/re-init/fade-in |
| 4 | Preset = `{grid_version, band_count, per-band gain dB, preamp}` — band sayısı preset'in parçası; yüklerken değişirse kural 2 işletilir |
| 5 | Kullanıcı tek tek band frekansı **ayarlayamaz** (grid sabit); **parametrik mod ayrıdır** (ADR-0105 §2.1 — RBJ serbest mod korunur) |
| 6 | 10-band preset'i 31-band kasa içinde geri yüklenebilir (eksik bantlar default flat) |
| 7 | Radyoda da EQ uygulanır (aynı zincir) |

### §08.4 EQ UI gereksinimleri (bu PRD ekidir — engine/ADR dışı, `[ÖNERİ]` etiketi NE-PLR-13x)

| # | UI öğesi |
|---|---|
| 1 | Bant sayısı seçici (10/15/20/31 radyo düğmesi) |
| 2 | Preset listesi (fabrika + kullanıcı) + kaydet/adlandır/sil |
| 3 | Her bant için gain slider (-12…+12 dB `[ÖNERİ]` — eşik ADR'de yok) + hover'da frekans etiketi |
| 4 | Preamp slider |
| 5 | Flat/Bypass düğmesi (bit-perfect rozeti §05.2 B3 ile senkron) |
| 6 | Parametrik mod sekmesi (RBJ: f/Q/gain per bant — M5+) |

---

## §09 Backend & Cihaz Yönetimi (Player Açıdan)

### §09.1 Cihaz seçimi (S11 C ABI + ADR-019)

| # | Gereksinim |
|---|---|
| 1 | Cihaz listesi UI: `neva_session_set_output_device(index)` + liste kaynağı engine `enumerate()` |
| 2 | Varsayılan cihaz: OS default'u; kullanıcı tercihi kalıcı (config dosyası) `[ÖNERİ]` |
| 3 | Cihaz değiştirme canlı yapılır (PLAYING iken): backend close → open → ring korunur `[ÖNERİ — NE-PLR-063]` |

### §09.2 Backend fallback senaryoları (Player UX — §10 NE-PLR-06x'e hizalı)

| Senaryo | Engine davranışı | Player UX |
|---|---|---|
| ASIO sürücüsü yok | Zincir 2'ye (WASAPI Excl) düşer | UI'da "ASIO yok — WASAPI Exclusive kullanılıyor" bilgi satırı `[ÖNERİ]` |
| WASAPI Exclusive `AUDCLNT_E_DEVICE_IN_USE` | Shared'a düş (spec wasapi-exclusive.md:126) | Uyarı + "Exclusive kilitli değil" rozeti `[ÖNERİ]` |
| Cihaz hot-plug (kopma) | Backend error → fallback → Null | Toast + durum ERROR; yeniden bağlanmayı dene `[ÖNERİ]` |
| Tüm backend'ler başarısız | Null (sessiz) | ERROR durumu + açıklama; çalma durmaz (sessiz devam) `[ÖNERİ — ADR-019 "Null Output" ruhu]` |

### §09.3 Exclusive/shared geçişi (ADR-019 F2 + ADR-106 §1.3)

| Tetik | Aksiyon |
|---|---|
| Kullanıcı Exclusive ister ama cihaz reddeder | Shared'a düş + UI bilgisi |
| Exclusive açıkken ikinci uygulama cihazı ister | `AUDCLNT_E_DEVICE_IN_USE` → Shared fallback (spec) |
| Kullanıcı manuel kilit | `switchMode()` (faz 2 genişletmesi — kardeş PRD NE-ENG-031) |

### §09.4 Hot-plug (ADR-019 F1)

| Platform | Mekanizma | Durum |
|---|---|---|
| macOS | `kAudioHardwarePropertyDevices` property dinleme | Spec (core-audio-macos.md:46-57) |
| Windows | WASAPI device notification `⚠️ VR (ADR-019 §1.1-C: olay API'si çaprazsız)` | PLANNED |
| Linux | PipeWire node olayları `⚠️ VR` | PLANNED |

### §09.5 Kill-switch (ADR-019 F4)

`kill()` noexcept — en sonda: fade-out → 50 ms sessizlik → restart (ADR-017 xrun politikası,
brain.md:869). Player'da tetikleyici: UI'da "acil durdur" + backend zinciri tükenmesi.

---

## §10 Ürün Gereksinimleri (NE-PLR Serisi)

### §10.1 Sınıflandırma

| Etiket | Anlam |
|---|---|
| MUST | Zorunlu — yoksa release kabul edilmez |
| SHOULD | Önemli — gerekçeli erteleme possible |
| MAY | Opsiyonel |
| P0 | Kritik yol (bit-perfect/ses sürekliliği) |
| P1 | Yüksek (radyo/EQ — ADR-106 çekirdek) |
| P2 | Orta |
| P3 | Düşük |
| `[ÖNERİ]` | Bu PRD ekidir — ADR'de karşılığı yok, onay gerekli |
| `⚠️ VR` | Dayandığı ADR dosyası diskte yok/eksik (özellikle ADR-0105 indeks kaydı) — doğrulanana kadar kesin sayılmaz |

### §10.2 Gereksinim sayısı özeti

| Grup | Aralık | Konu | Tahmini |
|---|---|---|---|
| A | NE-PLR-001..015 | Mimari & engine entegrasyonu | 15 |
| B | NE-PLR-016..035 | Medya pipeline & decode | 20 |
| C | NE-PLR-036..055 | Bit-perfect & cihaz çıkışı | 20 |
| D | NE-PLR-056..075 | Gapless | 20 |
| E | NE-PLR-076..095 | Video & A/V sync | 20 |
| F | NE-PLR-096..120 | İnternet radyosu (ADR-106) | 25 |
| G | NE-PLR-121..145 | EQ (ADR-106 §2.2b) | 25 |
| H | NE-PLR-146..165 | Kütüphane, UI, durum makinesi | 20 |
| I | NE-PLR-166..180 | Güvenlik | 15 |
| J | NE-PLR-181..200 | Bütçeler & test kapıları | 20 |
| K | NE-PLR-201..215 | FFI, MCP, dış API | 15 |
| | | **Toplam** | **215** |

### §10.3 Grup A — Mimari & Engine Entegrasyonu

#### NE-PLR-001 — Per-OS ortak çekirdek + ince adapter
- **Açıklama:** Tek C++ çekirdek (neva-core) + platform adapter'ları (IAudioBackend); player UI/uygulama katmanı OS'tan bağımsız.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Windows/Linux build'leri aynı uygulama katmanı kodunu paylaşır; OS'a özgü yalnız backend + ince satırlar.
- **ADR izi:** ADR-019 §2.2 (Ortak Çekirdek + İnce Adapter, 15/5/5/5/5 consensus)
- **İlgili test:** TC-PLR-001

#### NE-PLR-002 — C ABI üzerinden engine kullanımı
- **Açıklama:** Player, engine'e yalnız `neva_session.h` C ABI'sinden bağlanır (FFI sınırı).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Player kaynak kodunda `neva::` C++ iç tiplerine doğrudan erişim yok (yalnız `neva_session_*` çağrıları) `[ÖNERİ — istisna: EqChain per-kanal erişimi]`.
- **ADR izi:** disk `neva_session.h` · kardeş PRD NE-ENG-001
- **İlgili test:** TC-PLR-002

#### NE-PLR-003 — api_version handshake kontrolü
- **Açıklama:** Açılışta engine sürümü doğrulanır; uyuşmazlıkta kullanıcıya net hata.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `0x00010000` beklentisi; uyuşmazlık → ERROR durumu + sürüm mesajı (crash yok).
- **ADR izi:** disk `neva_session.h:26`
- **İlgili test:** TC-PLR-003

#### NE-PLR-004 — Durum makinesi (ADR-019 §1.4)
- **Açıklama:** STOPPED/PLAYING/PAUSED/LOADING/ERROR — web footer player (ADR-018) ile aynı semantik.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §04.5 geçiş tablosu birebir; geçersiz geçiş (PAUSED→LOADING doğrudan) reddedilir.
- **ADR izi:** ADR-019 §1.4 · ADR-004 · ADR-018
- **İlgili test:** TC-PLR-004

#### NE-PLR-005 — Event callback (metadata/state/error)
- **Açıklama:** C ABI genişletmesi E1: metadata (StreamTitle), durum değişimi, hata event'leri UI'a akar.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Event'ler non-RT kuyruktan gelir; UI thread'e marshal edilir; ABI minor sürümle eklenir (mevcut fonksiyonlar değişmez).
- **ADR izi:** ADR-106 kural 2 (minor ek — ABI kırmaz) · kardeş PRD §05.2 E1
- **İlgili test:** TC-PLR-005

#### NE-PLR-006 — Konum/düre erişimi (E3/E4)
- **Açıklama:** `get_position/duration` + `seek` — UI progress/seek çubuğu.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Seek doğrulanır (dosya sınırında clamp); radyoda seek devre dışı (akış).
- **ADR izi:** kardeş PRD §05.2 E3/E4
- **İlgili test:** TC-PLR-006

#### NE-PLR-007 — Cihaz listesi & seçimi (E8)
- **Açıklama:** `get_device_list` + `set_output_device(index)` + hot-plug event.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Liste engine `enumerate()`'inden gelir; seçim canlı yapılır, tercih kalıcı saklanır.
- **ADR izi:** ADR-019 F1 · disk `neva_session_set_output_device`
- **İlgili test:** TC-PLR-007

#### NE-PLR-008 — UI katmanı ayrımı
- **Açıklama:** Uygulama katmanı (queue, kütüphane, radyo tuner yönetimi) ile UI (pencere/çizim) ayrık; engine RT'den UI'a veri yalnız kuyruklarla akar.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** UI thread'inde engine RT veri yapısına doğrudan erişim yok.
- **ADR izi:** ADR-017 (RT kısıtları) · kardeş PRD §04.3
- **İlgili test:** TC-PLR-008

#### NE-PLR-009 — UI toolkit kararı açık
- **Açıklama:** GUI framework seçimi (Qt/FLTK/JUCE-skin/özel) vault'ta kararlaştırılmamış.
- **Tip:** MUST (karar) · **Öncelik:** P1
- **Kabul kriteri:** Karar ADR olarak tescil edilmeden UI fazı (M4) başlatılmaz; seçenek listesi §18.1'de.
- **ADR izi:** YOK — açık madde §18.1
- **İlgili test:** — (karar kapısı)

#### NE-PLR-010 — Config kalıcılığı
- **Açıklama:** Son cihaz, EQ preset, bant sayısı, son çalınan, pencere durumu kalıcı (kullanıcı config dosyası).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Yeniden açılışta son durum geri gelir; bozuk config → default + uyarı (crash yok).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-010

#### NE-PLR-011 — Engine bağımlılık sürümü
- **Açıklama:** Player, `NEVA_API_VERSION` minor uyumlu; ABI-major uyumsuzlukta release bloke.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI'da player + engine birlikte derlenir; handshake testi yeşil.
- **ADR izi:** kardeş PRD §05.4
- **İlgili test:** TC-PLR-011

#### NE-PLR-012 — neva-eq / neva-matrix ayrı hedefler
- **Açıklama:** 31-band EQ penceresi (VoiceMeeter BUSGEQ15 emsal) ve 8×8 matris (BUSMatrix8 emsal) ayrı executable.
- **Tip:** SHOULD · **Öncelik:** P2 (M4/M5)
- **Kabul kriteri:** Ana player ile ayrı süreç; C ABI üzerinden konuşur; kapatma ana süreci etkilemez.
- **ADR izi:** ADR-107 K2 · S3 §4.1 (RE emsal)
- **İlgili test:** TC-PLR-012

#### NE-PLR-013 — Çevrimdışı öncelik
- **Açıklama:** Yerel oynatma (kütüphane, dosya) tamamen çevrimdışı çalışır; ağ yalnız radyo/keşif için.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Ağ yokken uygulama açılır, dosya çalar; ağ çağrıları sadece radyo/dizin akışlarında.
- **ADR izi:** §02.1 I-7 · AGENTS §17 Edge 9 (Offline-First)
- **İlgili test:** TC-PLR-013

#### NE-PLR-014 — RTL/konum testi yok (kapsam notu)
- **Açıklama:** Masaüstü uygulamasında web RTL kuralları geçerli değil; UI yerelleştirme (TR/EN) `⚠️ VR (yerelleştirme kararı yok)`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Karar §18.2'de açık; UI fazında ele alınır.
- **ADR izi:** YOK
- **İlgili test:** —

#### NE-PLR-015 — Kardeş PRD sınırı
- **Açıklama:** Engine iç davranışı (DSP, ring, backend) bu PRD'de yalnızca sözleşmesi kadar kullanılır; ayrıntı kardeş PRD'de.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Bu doküman engine implementasyonuna emir vermez; yalnız API/ölçüm sözleşmesi çaprazreferanslıdır.
- **ADR izi:** §02.3
- **İlgili test:** —

### §10.4 Grup B — Medya Pipeline & Decode

#### NE-PLR-016 — Decode matrisi (dr_libs + libdsd + FFmpeg)
- **Açıklama:** WAV/FLAC → dr_libs; DSD(DSF/DFF) → libdsd; MP3/AAC/Opus/OGG/video + HLS → FFmpeg native.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §05.1 matrisindeki her format açılır; bilmeyen format `UNSUPPORTED_FORMAT` (kod 3).
- **ADR izi:** ADR-0105 indeks kaydı ⚠️ (v1.0.2 §§3-4)
- **İlgili test:** TC-PLR-016

#### NE-PLR-017 — Yerel dosya demux önceliği (FFmpeg dışı)
- **Açıklama:** FLAC/WAV doğrudan dr_libs ile açılır (FFmpeg yüklenmez); FFmpeg yalnız video/HLS/MP3-AAC-Opus yolu.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** FLAC açılışında FFmpeg process/module yüklenmez `[ÖNERİ — ölçüm log ile]`.
- **ADR izi:** ADR-106 §1.3 (çözüm önerisi M3 — ses akışında FFmpeg'i EN AZ kullan)
- **İlgili test:** TC-PLR-017

#### NE-PLR-018 — soxr producer-side resample
- **Açıklama:** Format dönüşümü (örn. 44.1→48) ring'den önce; RT callback'te resample YOK.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** RT yolunda soxr çağrısı yok (statik + profil); master clock = cihaz.
- **ADR izi:** ADR-106 kural 5 (ADR-0105 §2.5) ⚠️
- **İlgili test:** TC-PLR-018

#### NE-PLR-019 — soxr lisansı (LGPL-2.1)
- **Açıklama:** Dinamik link ✅; statik link lisans kapısı.
- **Tip:** MUST · **Öncelik:** P0 (release)
- **Kabul kriteri:** §14 lisans matrisinde durum: LGPL-2.1 — statik link seçeneği `⚠️ LİSANS` etiketli.
- **ADR izi:** ADR-0105 v1.2.0 düzeltmesi (indeks) ⚠️
- **İlgili test:** TC-PLR-019 (lisans denetimi)

#### NE-PLR-020 — FFmpeg LGPL build
- **Açıklama:** `--enable-gpl` yasak; olabildiğince paylaşım; FDK-AAC yok.
- **Tip:** MUST · **Öncelik:** P0 (release)
- **Kabul kriteri:** Build bayrakları envanterde; LGPL dış codec (x264 vb.) yok (video encode kapsam dışı).
- **ADR izi:** ADR-0105 indeks (v1.3.x lisans satırları) ⚠️ · lib-matrix
- **İlgili test:** TC-PLR-020

#### NE-PLR-021 — Protocol whitelist (FFmpeg)
- **Açıklama:** HLS/playlist sadece `http`/`https`; `file:`/`concat:`/`crypto:` vb. reddedilir.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `file:///C:/...` URL'si validator'da RED; FFmpeg'e hiç ulaşmaz.
- **ADR izi:** ADR-106 kural 6 (protokol beyaz listesi)
- **İlgili test:** TC-PLR-021

#### NE-PLR-022 — HTTP09Allowed=0
- **Açıklama:** curl yapılandırmasında HTTP/0.9 kapalı (response splitting/uyumsuzluk koruması).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Tüm tuner isteklerinde `CURLOPT_HTTP09ALLOWED = 0`.
- **ADR izi:** ADR-106 kural 6 · radio-and-hires §5.6
- **İlgili test:** TC-PLR-022

#### NE-PLR-023 — Redirect zinciri ≤5
- **Açıklama:** Her hop yeniden validator'dan geçer; 5 aşıldıysa RED.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Zincirleme redirect (6. hop) reddi; her hop'ta IP kontrolü tekrarlanır.
- **ADR izi:** ADR-106 kural 6
- **İlgili test:** TC-PLR-023

#### NE-PLR-024 — Timeout 10 sn (tuner)
- **Açıklama:** Bağlantı/okuma timeout 10 s; aşım → reconnect döngüsü.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Askıda istek 10 sn'de kopar; backoff'a geçer (NE-PLR-115).
- **ADR izi:** ADR-106 kural 6 · radio-and-hires §5.7
- **İlgili test:** TC-PLR-024

#### NE-PLR-025 — TLS (mbedTLS)
- **Açıklama:** HTTPS doğrulaması mbedTLS ile; sertifika zinciri kontrol edilir; `kör kabul` yasak.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Self-signed/bozuk sertifika RED (kullanıcı açık onayı ayrı istisna `[ÖNERİ]`).
- **ADR izi:** ADR-106 §5.4 (mbedTLS Apache-2.0 OR GPL-2.0, 9.13.0)
- **İlgili test:** TC-PLR-025

#### NE-PLR-026 — decode → float32 ring
- **Açıklama:** Tüm decode çıktıları float32 PCM'e normalize edilip SPSC ring'e yazılır.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Çıkış formatı tutarlı (interleaved float32); ring invariant'ı korunur (writer okuyucuyu geçmez).
- **ADR izi:** ADR-0105 §2.5 ⚠️ · kardeş PRD §07.4
- **İlgili test:** TC-PLR-026

#### NE-PLR-027 — Bit derinliği dönüşüm politikası
- **Açıklama:** 24-bit → 32f kayıpsız (solda hizalı ölçek); 16→32f tam; çıkışta negosyasyon (N1).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 24-bit kaynak dosya → 24-in-32 çıktı hash'i kaynak byte'larla uyumlu (kayıpsız yolla) `⚠️ VR (detay ADR-0105 §2.x indeksinde yok)`.
- **ADR izi:** ADR-106 §1.3 · ADR-0105 indeks ⚠️
- **İlgili test:** TC-PLR-027

#### NE-PLR-028 — Queue prefetch (gapless ön koşulu)
- **Açıklama:** Sıradaki dosya decode'ı arka planda başlar (ring'i besler) — dosya→dosya boşluğu 0.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** E2 API (`open_queue/preload_next`) ile sıradaki dosya zamanında hazır.
- **ADR izi:** ADR-0105 AK-5 ⚠️ · kardeş PRD §05.2 E2
- **İlgili test:** TC-PLR-028

#### NE-PLR-029 — Decode hata yönetimi (bozuk dosya)
- **Açıklama:** Bozuk/kısmi dosya: çökme YOK; skip + UI uyarısı; kuyruk devam eder.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 1000+ bozuk dosya fuzz corpus (kapı 5.13) 0 crash; tek dosya hatası queue'yu durdurmaz.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️
- **İlgili test:** TC-PLR-029

#### NE-PLR-030 — Aynı anda tek decode oturumu (dosya)
- **Açıklama:** Dosya oynatmada aktif decode 1 (prefetch +1); radyo ile dosya aynı anda değil (tek transport).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Mode değişimi (radyo↔dosya) mevcut decode'u temiz kapatır.
- **ADR izi:** YOK — `[ÖNERİ]` (taşıma modeli §04.3)
- **İlgili test:** TC-PLR-030

#### NE-PLR-031 — Metadata okuma (ID3/Vorbis/FLAC tags)
- **Açıklama:** Başlık/album/sanatçı/tür/yıl/kapak okunur; UI'da gösterilir.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Eksik tag → dosya adı fallback; UTF-8 + ID3 v2.3/v2.4 encoding doğru.
- **ADR izi:** YOK — `[ÖNERİ]` (parça okuma kapısı ADR-0105'te `⚠️ VR`)
- **İlgili test:** TC-PLR-031

#### NE-PLR-032 — Kapak görseli (embedded art)
- **Açıklama:** Gömülü kapak (FLAC/MP3) okunur ve gösterilir; bellek bütçesi içinde (≤5 MB `⚠️ VR`).
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** Büyük görsel → ölçekle + reddet; bozuk görsel crash değil.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-032

#### NE-PLR-033 — ReplayGain / loudness okuma
- **Açıklama:** ReplayGain tag varsa uygulanır; yoksa R128 sidechain tahmini (NE-PLR-141).
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** Kapalıysa hiç gain yok (bit-perfect zinciri bozulmaz); açıkken bile EQ flat+limiter bypass şartı rozet için gerekli.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-033

#### NE-PLR-034 — Çoklu kaynak URL açılışı
- **Açıklama:** `open_file` yerine `open_source(uri)` genişletmesi: dosya yolu + http(s) stream tek kapıda.
- **Tip:** SHOULD · **Öncelik:** P1 (radyo ile paylaşımlı)
- **Kabul kriteri:** URI ayrıştırma validator'dan geçer (NE-PLR-152); dosya yolu lokal ise validator bypass → sadece yolu normalize eder `[ÖNERİ]`.
- **ADR izi:** kardeş PRD §05.2 (tuner E9) ile hizalı
- **İlgili test:** TC-PLR-034

#### NE-PLR-035 — Decode performerans bütçesi
- **Açıklama:** FLAC 192k/24 decode CPU payı ölçülmeli (`⚠️ VR — eşik tanımsız`).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Bench raporu (§06.4); eşik ADR-0105 dosyası bulununca bağlanır.
- **ADR izi:** ADR-0105 indeks §5.6 ⚠️
- **İlgili test:** TC-PLR-035

### §10.5 Grup C — Bit-perfect & Cihaz Çıkışı

#### NE-PLR-036 — WASAPI Exclusive bit-perfect yolu
- **Açıklama:** Dosya formatı == donanım formatı: birebir akış (OS mikser bypass).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Kapı 5.1: WAV hash byte-eşitlik (giriş==çıkış) — kardeş PRD K3 ile birlikte.
- **ADR izi:** ADR-0105 kapı 5.1 ⚠️ (indeks) · ADR-106 §1.3
- **İlgili test:** TC-PLR-036

#### NE-PLR-037 — Bit-perfect kapsamı (yalnız kayıpsız)
- **Açıklama:** WAV/FLAC kapsama dahil; MP3/AAC/Opus PCM çıktısı "bit-perfect" iddiası KAPSAM DIŞI (lossy).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** UI/pazarlama dili ve testler bu ayrımı zorunlu kılar; lossy akışta rozet YOK.
- **ADR izi:** ADR-0105 v1.0.2 §6 ⚠️
- **İlgili test:** TC-PLR-037

#### NE-PLR-038 — Format negosyasyon sırası (32f→32i→24-in-32)
- **Açıklama:** Cihaz float reddedebilir; negosyasyon sırası §05.5 N1.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Her basamak `IsFormatSupported` ile; tamamı başarısız → shared/fallback.
- **ADR izi:** ADR-106 §1.3
- **İlgili test:** TC-PLR-038

#### NE-PLR-039 — `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` kurtarma
- **Açıklama:** Periyot yeniden hesap + tekrar deneme (N3).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Hata yutulmaz; yeniden hesap başarısızsa zincir düşer.
- **ADR izi:** ADR-106 §1.3
- **İlgili test:** TC-PLR-039

#### NE-PLR-040 — EQ flat + limiter bypass bit-perfect ön koşulu
- **Açıklama:** Hash kapısı flat preset ve limiter pasifken geçer; EQ aktifken iddia düşer.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI'da EQ aktif + hash = FAIL beklenir (inverted guard kardeş PRD §11.2 Ş4).
- **ADR izi:** ADR-106 kural 4/§5.1 Ş4
- **İlgili test:** TC-PLR-040

#### NE-PLR-041 — Hi-res 192 kHz / 32-bit float
- **Açıklama:** Dosya + cihaz destekliyorsa uçtan uca 32f/192k.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 192k/32f kaynak hash doğrulaması; negosyasyon N1 sırası.
- **ADR izi:** ADR-107 K3 · ADR-0105 indeks ⚠️
- **İlgili test:** TC-PLR-041

#### NE-PLR-042 — ASIO yolu (lisans U1'e bağlı)
- **Açıklama:** ASIO SDK ile donanım pin formatı birebir; <10 ms gecikme hedefi.
- **Tip:** MUST · **Öncelik:** P0 (U1 verildiğinde)
- **Kabul kriteri:** ASIO driver varlığında exclusive benzeri doğrudan yol; fallback zinciri §09.2.
- **ADR izi:** ADR-019 §1.4 · kardeş PRD NE-ENG-023
- **İlgili test:** TC-PLR-042

#### NE-PLR-043 — Bit-perfect rozeti `[ÖNERİ]`
- **Açıklama:** UI rozeti yalnız: negotiated==dosya formatı && EQ flat && limiter bypass.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Üç koşul false ise rozet gizlenir (yanıltıcı görsel yok).
- **ADR izi:** kardeş PRD §05.2 B3 `[ÖNERİ]`
- **İlgili test:** TC-PLR-043

#### NE-PLR-044 — DSD/DoP çıkışı
- **Açıklama:** DSF/DFF → DoP marker continuity; native DSD `⚠️ VR`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** DoP akışında marker bütünlüğü doğrulanır (test dosyası); native DSD kararı ayrı ADR.
- **ADR izi:** ADR-0105 AK-5 ⚠️
- **İlgili test:** TC-PLR-044

#### NE-PLR-045 — Shared mod rozet/uyarı ayrımı
- **Açıklama:** Shared (OS mikser dahil) modda bit-perfect iddiası + rozeti YOK; süreklilik korunur.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Fallback sonrası UI bilgilendirmesi; hash testi shared modda koşul dışı.
- **ADR izi:** kardeş PRD NE-ENG-022
- **İlgili test:** TC-PLR-045

#### NE-PLR-046 — Exclusive↔shared otomatik geçiş
- **Açıklama:** `AUDCLNT_E_DEVICE_IN_USE` / cihaz reddi → shared'a düş + durum raporu (N5).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Geçişte ses kesintisi minimize; event E8 ile UI'a akar.
- **ADR izi:** ADR-106 §1.3 · ADR-019 F2
- **İlgili test:** TC-PLR-046

#### NE-PLR-047 — Cihaz hızı uyuşmazlığı (F3 sırası)
- **Açıklama:** 44.1k dosya / 48k cihaz: setDeviceRate → soxr → fallback sırası.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** RT'de resample yok; hash yalnız eşit formatta.
- **ADR izi:** ADR-019 §2 · kardeş PRD NE-ENG-015
- **İlgili test:** TC-PLR-047

#### NE-PLR-048 — Hot-plug kaybı davranışı
- **Açıklama:** Cihaz koparsa: zincir → fallback → Null; session ERROR/uyarı (§09.4).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Test: cihaz kaldırma simülasyonu; yeniden bağlanma; kullanıcı mesajı.
- **ADR izi:** ADR-019 F1
- **İlgili test:** TC-PLR-048

#### NE-PLR-049 — Bit-perfect CI hattı (dosya→Null→hash)
- **Açıklama:** Donanım gerektirmeyen hash yolu Null backend ile CI'da; gerçek DAC loopback release öncesi manuel/onaylı test.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI yeşil + release checklist'te loopback maddesi (§11.2).
- **ADR izi:** kardeş PRD §11.2 · ADR-107 K3
- **İlgili test:** TC-PLR-049

#### NE-PLR-050 — 12 kombinasyon matrisi
- **Açıklama:** 44.1/48/96/192 kHz × 16/24/32f — hepsi hash'ten geçer.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 12/12 yeşil; eksik kombinasyon = release bloke.
- **ADR izi:** kardeş PRD K3/§11.2
- **İlgili test:** TC-PLR-050

#### NE-PLR-051 — OS ses efektlerinin devre dışı olması (exclusive)
- **Açıklama:** Exclusive/ASIO/hw: yollarında OS EQ/efekt/session-volume animasyonu etkisiz.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Windows'ta session volume 1.0 sabit; "ses iyileştirme" bayrakları kapalı.
- **ADR izi:** kardeş PRD NE-ENG-037 · VISION V-5
- **İlgili test:** TC-PLR-051

#### NE-PLR-052 — Null-test CI guard (inverted)
- **Açıklama:** Radyo/lossy akışta null-test CI'da ENGELLENİR (sahte FAIL yasak — ADR-106 §5.1 Ş4).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI script: radyo akışında hash testi koşul dışı; sahte-PASS/FAIL denetimi.
- **ADR izi:** ADR-106 §5.1 Ş4 (QA debate düzeltmesi)
- **İlgili test:** TC-PLR-052

#### NE-PLR-053 — Exclusive kilit tercihi (kullanıcı)
- **Açıklama:** Kullanıcı exclusive'i sabitleyebilir (fallback istemeyen pro kullanıcı) `[ÖNERİ]`.
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** Tercih açıkken cihaz kaybında otomatik shared'a geçmez; uyarı gösterir.
- **ADR izi:** ADR-019 §1.1 (`⚠️ VR — eşik/varsayılan`)
- **İlgili test:** TC-PLR-053

#### NE-PLR-054 — Uzun süreli çalışma kararlılığı (bit-perfect)
- **Açıklama:** 4 sa sürekli exclusive oynatma; hash/bozulma ve xrun=0.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Süreç testi; bellek sızıntısı 0 (leak check).
- **ADR izi:** kardeş PRD §11 stres
- **İlgili test:** TC-PLR-054

#### NE-PLR-055 — Dither (16-bit çıkış)
- **Açıklama:** 24/32f kaynaktan 16-bit cihaza çıkışta dither `[ÖNERİ — TPDF]`; bit-perfect (16→16) yolda YOK.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Dither yalnız dönüşüm varsa; 16→16 hash yolu bozulmaz.
- **ADR izi:** YOK — `[ÖNERİ]` (N1 negosyasyonla uyumlu)
- **İlgili test:** TC-PLR-055

### §10.6 Grup D — Gapless (AK-5)

#### NE-PLR-056 — MP3 (LAME/Xing) gapless
- **Açıklama:** 12-bit delay+padding okuma; giriş/çıkış padding kırpma.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Dosya→dosya sınırında 0 örnek boşluk; faz kayması yok (kapı 5.12 vektörü).
- **ADR izi:** ADR-0105 AK-5 ⚠️
- **İlgili test:** TC-PLR-056

#### NE-PLR-057 — FLAC gapless
- **Açıklama:** SEEKTABLE + ilk/son frame padding bilgisi.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** FLAC→FLAC sınırı boşluksuz; hash doğrulamalı referans vektör.
- **ADR izi:** ADR-0105 AK-5 ⚠️
- **İlgili test:** TC-PLR-057

#### NE-PLR-058 — Opus gapless
- **Açıklama:** Granule pozisyonu ile hizalama.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Opus→Opus sınır testi.
- **ADR izi:** ADR-0105 AK-5 ⚠️
- **İlgili test:** TC-PLR-058

#### NE-PLR-059 — DSD gapless (DoP/DSF marker continuity)
- **Açıklama:** Marker akış bütünlüğü ile geçiş.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** DSF→DSF sınırında marker deseni bozulmaz.
- **ADR izi:** ADR-0105 AK-5 ⚠️
- **İlgili test:** TC-PLR-059

#### NE-PLR-060 — Dosya→dosya geçişte kesintisizlik ölçümü
- **Açıklama:** DAC loopback korelasyonu ±5 ms; >20 ms = FAIL.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Kapı 5.12: korelasyon ölçümü raporu; ölçüm düzeneği §11.3.
- **ADR izi:** ADR-0105 kapı 5.12 ⚠️
- **İlgili test:** TC-PLR-060

#### NE-PLR-061 — Queue prefetch boyutu
- **Açıklama:** Prefetch penceresi ≥1 saniye PCM (ring dolumu) `[ÖNERİ]`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Geçiş anında ring'de yeterli veri; starve fade tetiklenmez.
- **ADR izi:** kardeş PRD §07.4 `[ÖNERİ]`
- **İlgili test:** TC-PLR-061

#### NE-PLR-062 — Karışık format queue (FLAC→MP3→FLAC)
- **Açıklama:** Farklı sample-rate/format dosyalar arası geçiş; her dosya ayrı negosyasyonla.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Geçişte cihaz formatı korunur; gerekirse soxr; kesinti ≤1 geçiş fade `[ÖNERİ]`.
- **ADR izi:** kardeş PRD §06.5 · ADR-019 F3
- **İlgili test:** TC-PLR-062

#### NE-PLR-063 — Shuffle + gapless
- **Açıklama:** Karışık sırada da prefetch/gapless korunur (sıradaki biliniyor).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Shuffle'da gapless testi geçer.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-063

#### NE-PLR-064 — Repeat (tek/liste) gapless
- **Açıklama:** Tekrar döngüsünde sınır geçişi gapless; sonsuz döngüde bellek sızıntısı 0.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** 1 saat repeat testi; RSS artış ≤0 `[ÖNERİ]`.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-064

#### NE-PLR-065 — Son dosya→STOPPED
- **Açıklama:** Queue bittiğinde durum STOPPED; fade-out yok (planlı bitiş) `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Son frame sonrası 50 ms ramp + STOPPED event.
- **ADR izi:** kardeş PRD §07.4 `[ÖNERİ]`
- **İlgili test:** TC-PLR-065

#### NE-PLR-066 — Gapless bozuk tag fallback
- **Açıklama:** Xing/seektable yoksa: tahmin yok — dosya başı normal başlangıç (boşluk kabul).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Eksik bilgide uydurma kırpma YOK (yanlış kırpma bozulma üretir).
- **ADR izi:** ADR-0105 AK-5 ruhu ⚠️
- **İlgili test:** TC-PLR-066

#### NE-PLR-067 — Crossfade ayrımı
- **Açıklama:** Gapless ≠ crossfade; crossfade opsiyonel özellik `[ÖNERİ — M5]`; açıkken gapless iddiası düşer.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** UI'da ikisi aynı anda iddia edilmez; crossfade ≥2 decode oturumu ister (bütçe).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-067

#### NE-PLR-068 — Gapless CI vektör kütüphanesi
- **Açıklama:** Referans kaynaklı test dosyaları (Xing'li MP3 çifti, seektable'lı FLAC çifti) repo'da saklanır.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Vektörler + beklenen sonuçlar (byte maskesi) CI'da çalışır.
- **ADR izi:** ADR-0105 kapı 5.12 ⚠️
- **İlgili test:** TC-PLR-068

#### NE-PLR-069 — Konum sayaçları gapless uyumu
- **Açıklama:** Geçişte position sayaçları sıfırlanır; queue üzerindeki topplam konum `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** UI'da sıradaki/şu anki net ayrımı.
- **ADR izi:** kardeş PRD §05.2 E3 `[ÖNERİ]`
- **İlgili test:** TC-PLR-069

#### NE-PLR-070 — DSD→PCM queue geçişi
- **Açıklama:** DoP akışından 2.0 PCM dosyasına geçiş; marker temizliği (state reset).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Geçişte pop transient'sız (NE-ENG-078 reset).
- **ADR izi:** kardeş PRD NE-ENG-078
- **İlgili test:** TC-PLR-070

#### NE-PLR-071 — 5.1 kaynaklı gapless
- **Açıklama:** Çok kanallı dosyalar arası geçiş; kanal sayacı uyumu.
- **Tip:** MAY · **Öncelik:** P3 (M6)
- **Kabul kriteri:** 5.1→5.1; 5.1→2.0 downmix geçişi `[ÖNERİ — downmix matrisi kararı §18.3]`.
- **ADR izi:** VISION V-8
- **İlgili test:** TC-PLR-071

#### NE-PLR-072 — Seek sonrası gapless bozulmaz
- **Açıklama:** Dosya ortasından seek + queue devamı; sıradaki geçiş hâlâ gapless.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Seek→geçiş testi; padding bilgileri korunur.
- **ADR izi:** kardeş PRD §05.2 E4
- **İlgili test:** TC-PLR-072

#### NE-PLR-073 — Pause/Resume arası gapless
- **Açıklama:** Pause iken geçiş sınırına gelinmesi durumu: pause sınırda bekler `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Bekleme bellek maliyeti yok (decode duraklar); resume gapless korunur.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-073

#### NE-PLR-074 — Dosya→dosya: 0 frame boşluk tanımı
- **Açıklama:** "Boşluk" = sınırda sessiz örnek sayısı; hedef 0 (fade hariç geçiş rampası ≤2 ms `[ÖNERİ]`).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Ölçüm raporu kapı 5.12'de.
- **ADR izi:** ADR-0105 kapı 5.12 ⚠️
- **İlgili test:** TC-PLR-074

#### NE-PLR-075 — Radio gapless (akış→akış) — reconnect dahil
- **Açıklama:** Reconnect sonrası: konum koruma, prebuffer 2-4 s, decoder reset, sessizlik → fade-in 20 ms.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Stream→stream vektör; DAC loopback ±5 ms (kapı 5.12 Ş2).
- **ADR izi:** ADR-0105 AK-5 ⚠️ · ADR-106 §2.2a
- **İlgili test:** TC-PLR-075

### §10.7 Grup E — Video & A/V Sync

#### NE-PLR-076 — libplacebo render yolu
- **Açıklama:** Video render libplacebo (Vulkan/GL); 4K/HDR hedefi.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** HDR metadata doğru aktarım; render video-only (ses engine'de).
- **ADR izi:** ADR-0105 indeks (v1.2.x) ⚠️
- **İlgili test:** TC-PLR-076

#### NE-PLR-077 — Master clock = cihaz (ses)
- **Açıklama:** Ses cihazı saatidir; video audio'a hizalanır — tersi değil.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 2 saat video testte drift kaymayla düzeltilir; video ileri/geri hız ayarı (stretch) `[ÖNERİ]`.
- **ADR izi:** ADR-106 kural 11 · ADR-0105 kapı 5.11 ⚠️
- **İlgili test:** TC-PLR-077

#### NE-PLR-078 — A/V sync P95 ≤20 ms (kapı 5.11)
- **Açıklama:** Ölçüm eşiği.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Loopback/ışık ölçüm düzeneği (§11.3); P95 raporu.
- **ADR izi:** ADR-0105 kapı 5.11 ⚠️
- **İlgili test:** TC-PLR-078

#### NE-PLR-079 — VRAM bütçesi (~400-700 MB)
- **Açıklama:** Video açıkken VRAM tahmini; P010 düzeltmesi 12,4→24,9 MB/kare.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Peak ölçümü (§06.3; §7.5 açık madde kapanır).
- **ADR izi:** ADR-0105 §2.8 v1.2.1-1.3.2 ⚠️
- **İlgili test:** TC-PLR-079

#### NE-PLR-080 — VRAM tükenme merdiveni (V0-V5)
- **Açıklama:** Basınç altında kademeli kalite düşürme.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Her kademede davranış tanımlı (downscale → HDR kapatma → video gizle `⚠️ VR — kademe içerikleri indeks metninde yok)`.
- **ADR izi:** ADR-0105 §7.4 ⚠️
- **İlgili test:** TC-PLR-080

#### NE-PLR-081 — VRAM ölçüm altyapısı (GTX 1660 bench)
- **Açıklama:** Referans bench: GTX 1660 · 6144 MB · sürücü 610.62 · Win10 19045 · baseline 1511 MiB.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Aynı ortamda repeatable ölçüm; hedef-sınıf uyarı satırı raporda (1060 3 GB: 600 MB = %19,5).
- **ADR izi:** ADR-0105 §7.1-§7.3 v1.3.1-1.3.2 ⚠️
- **İlgili test:** TC-PLR-081

#### NE-PLR-082 — Sistem RAM ≤400 MB (video dahil)
- **Açıklama:** Bütçe ihlali davranışları §06.5.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Profil raporu; ihlalde uyarı `[ÖNERİ — davranış NE-PLR-065 benzeri tanımlanacak]`.
- **ADR izi:** ADR-0105 §2.9 ⚠️
- **İlgili test:** TC-PLR-082

#### NE-PLR-083 — Gecikme <20 ms bütçesi
- **Açıklama:** Blok ≥1024@48k BLOKLANDI; L1-L4 merdiveni `⚠️ VR`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Kapı 5.10 gecikme ölçümü; blok seçimi bu sınırlar içinde.
- **ADR izi:** ADR-0105 §2.10 ⚠️
- **İlgili test:** TC-PLR-083

#### NE-PLR-084 — Kapı 5.10 (gecikme ölçüm kapısı)
- **Açıklama:** Release öncesi zorunlu ölçüm.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Ölçüm düzeneği + rapor arşivi; eşiği aşan build release adayı değil.
- **ADR izi:** ADR-0105 kapı 5.10 ⚠️
- **İlgili test:** TC-PLR-084

#### NE-PLR-085 — Kapı 5.13 (hostile input fuzzing)
- **Açıklama:** Format başına ≥1000 sentetik corpus; toplam ≥8000; 0 ASan/UBSan crash; 0 OOB; CI ≤10 dk.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Fuzz raporu release dosyasında; ADR-106 Ş7 (her decoder + ICY + HLS playlist) tam.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️ · ADR-106 §5.2 Ş7
- **İlgili test:** TC-PLR-085

#### NE-PLR-086 — Kapı 5.12 (gapless format vektörü)
- **Açıklama:** AK-5 vektörlerinin tamamı + radio stream→stream + DAC loopback ±5 ms.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §10 Grup D testleri yeşil + loopback raporu.
- **ADR izi:** ADR-0105 kapı 5.12 ⚠️
- **İlgili test:** TC-PLR-086

#### NE-PLR-087 — Kapı 5.1 (bit-perfect null-test + inverted guard)
- **Açıklama:** §11.2 hattı; radyo/lossy'de test engellenir.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI guard + §11.2 Ş4; sahte-PASS/FAIL denetimi.
- **ADR izi:** ADR-0105 kapı 5.1 ⚠️ · ADR-106 §5.1
- **İlgili test:** TC-PLR-087

#### NE-PLR-088 — Video format kapsamı
- **Açıklama:** MP4/MKV demux + FFmpeg decode; encode (dönüştürme) video KAPSAM DIŞI.
- **Tip:** SHOULD · **Öncelik:** P3
- **Kabul kriteri:** Test: 1 MP4 + 1 MKV oynar; altyazı `⚠️ VR (karar yok — MAY)` .
- **ADR izi:** ADR-105 indeks (video yolu) ⚠️
- **İlgili test:** TC-PLR-088

#### NE-PLR-089 — Video modu geçişi (ses↔video)
- **Açıklama:** Dosya→video→dosya; engine oturumu korunur (backend değişmez).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Geçişte cihaz/format negosyasyonu tekrarlanmaz (aynı session).
- **ADR izi:** kardeş PRD NE-ENG-035
- **İlgili test:** TC-PLR-089

#### NE-PLR-090 — Pencere/video senkronu (drift ölçümü)
- **Açıklama:** Uzun testte P95 + maksimum sapma raporu.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Raporda max sapma; kapı 5.11 P95.
- **ADR izi:** ADR-0105 kapı 5.11 ⚠️
- **İlgili test:** TC-PLR-090

#### NE-PLR-091 — Ses kalitesi: SNR >105 dB
- **Açıklama:** VISION V-1 hedefi.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** §11.3 ölçüm düzeneği (DAC loopback); rapor release dosyasında.
- **ADR izi:** VISION.md (satır ~100/327) · §00.3-D5 `⚠️ VR (ADR-0105'te teyit yok)`
- **İlgili test:** TC-PLR-091

#### NE-PLR-092 — THD+N <%0.005 @1 kHz
- **Açıklama:** VISION V-2 hedefi.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** §11.3 ölçüm; eşiğin altında.
- **ADR izi:** VISION.md · `⚠️ VR` (aynı)
- **İlgili test:** TC-PLR-092

#### NE-PLR-093 — 32-bit float DSP zinciri (uçtan uca)
- **Açıklama:** Decode → DSP → çıkış zinciri float32; erken quantization yok.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Zincirde tek bir integer kesme yok (statik denetim + hash testi).
- **ADR izi:** VISION V-6 · kardeş PRD §06
- **İlgili test:** TC-PLR-093

#### NE-PLR-094 — mpv/libmpv reddi korunur
- **Açıklama:** GPL-2.0 copyleft + black-box reddi (v1.1.1) değişmez; tekrar değerlendirme ADR ister.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kod tabanında mpv bağımlılığı yok; §14 RED satırı.
- **ADR izi:** ADR-0105 v1.1.1 indeks
- **İlgili test:** TC-PLR-094

#### NE-PLR-095 — Video performans bütçesi
- **Açıklama:** 4K HDR decode+render CPU payı `⚠️ VR (eşik yok)`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Bench raporu; eşik kararı ADR-0105 dosyası ile.
- **ADR izi:** ADR-0105 indeks ⚠️
- **İlgili test:** TC-PLR-095

### §10.8 Grup F — İnternet Radyosu (ADR-106)

#### NE-PLR-096 — ICY/Shoutcast v1+v2 özel tuner
- **Açıklama:** Özel parser (engine `icy_parser.h` IMPLEMENTED + player thread); genel demuxer'a devredilmez.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** v1 (icy-metaint metin blokları) + v2 (XML metaveri) ayrıştırılır; StreamTitle event akar.
- **ADR izi:** ADR-106 §2.2a kural 1 · S11
- **İlgili test:** TC-PLR-096

#### NE-PLR-097 — HLS (m3u8) FFmpeg gateway
- **Açıklama:** FFmpeg `hls` demuxer; protocol whitelist http/https.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** M3U8 akışı oynar; `file://` RED (NE-PLR-021).
- **ADR izi:** ADR-106 kural 1 · lib-matrix (FFmpeg 9.0.2 HLS demux)
- **İlgili test:** TC-PLR-097

#### NE-PLR-098 — DASH Aşama 2
- **Açıklama:** FFmpeg demux; daha az yaygın radyoda.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Aşama 2 karar ADR ile; UI'da gizli seçenek.
- **ADR izi:** ADR-106 §2.2a kural 1
- **İlgili test:** TC-PLR-098

#### NE-PLR-099 — Codec matrisi (AAC-LC/HE-AAC/Opus/MP3/FLAC)
- **Açıklama:** FFmpeg native codec'ler; FDK-AAC YOK.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Her codec için canlı/lokal akış testi; FDK bayrağı build'de yok.
- **ADR izi:** ADR-106 kural 9 §1.3
- **İlgili test:** TC-PLR-099

#### NE-PLR-100 — FDK-AAC yasağı
- **Açıklama:** Lisans teyidi (GPL-vs-LGPL) yok; kullanılmaz.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** §14 matris + build envanteri; teyit gelirse ayrı ADR.
- **ADR izi:** ADR-106 kural 9 (architect S4)
- **İlgili test:** TC-PLR-100

#### NE-PLR-101 — StreamTitle sanitizasyonu (kural 7)
- **Açıklama:** UTF-8 doğrula, kontrol karakterleri strip, 256 bayt cap, HTML-escape.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** XSS payload'lı StreamTitle düz metin gösterilir; cap aşımı kırpılır.
- **ADR izi:** ADR-106 kural 7 (security S1)
- **İlgili test:** TC-PLR-101

#### NE-PLR-102 — StreamUrl hyperlink yasağı
- **Açıklama:** URL düz metin; tıklanabilir link DEĞİL.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** UI'da `onclick`/anchor yok; kopyala seçeneği `[ÖNERİ]`.
- **ADR izi:** ADR-106 kural 7
- **İlgili test:** TC-PLR-102

#### NE-PLR-103 — Parser limitleri (kural 8)
- **Açıklama:** M3U8 entry ≤1000 · derinlik ≤1 · segment ≤50 MB · XML DTD kapalı · icy-metaint 32 B–1 MiB · text ≤1 MB.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Sınır aşımında RED/skip; fuzz corpus bu limitlerle doğrulanır.
- **ADR izi:** ADR-106 kural 8 · §07.5 Ş8
- **İlgili test:** TC-PLR-103

#### NE-PLR-104 — URL Validator tek kapı (kural 6)
- **Açıklama:** Tüm URL'ler tek validator'dan; her redirect hop + post-connect IP (DNS rebinding); private/loopback/link-local RED; HTTPS zorunlu (http = kullanıcı onayı); userinfo [REDACTED]; playlist ≤1 MB.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §12.2 test vektörleri; validator bypass yok (statik denetim: tuner FFmpeg'e ham URL vermez).
- **ADR izi:** ADR-106 kural 6 · S1 §2.2a (27 şart)
- **İlgili test:** TC-PLR-104

#### NE-PLR-105 — Reconnect backoff (1s→30s cap, ±%20 jitter)
- **Açıklama:** Üstel backoff; jitter (sync-thundering koruması).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Ölçüm: ilk 6 deneme süreleri tablosu; cap'te sabitlenir.
- **ADR izi:** ADR-106 §1.3 (v1.1 düzeltmesi)
- **İlgili test:** TC-PLR-105

#### NE-PLR-106 — 3 başarısız → ERROR
- **Açıklama:** Backoff zinciri sonunda durum ERROR; kullanıcı mesajı.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** 3. başarısızdan sonra yeni deneme YOK (elle "yeniden dene" hariç).
- **ADR izi:** ADR-106 kural 8 (3 başarısız → ERROR)
- **İlgili test:** TC-PLR-106

#### NE-PLR-107 — Prebuffer 2-4 s (reconnect kutusu)
- **Açıklama:** Yeniden bağlanınca tampon → LOADING → PLAYING.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** İlk ses öncesi ≤4 s bekleme; kesintisiz akış (starve fade yok).
- **ADR izi:** ADR-106 §2.2a
- **İlgili test:** TC-PLR-107

#### NE-PLR-108 — Fade-in 20 ms (reconnect)
- **Açıklama:** Decoder reset sonrası sessizlik → 20 ms fade-in (pop yasağı).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Waveform analizi; rampa süresi.
- **ADR izi:** ADR-106 §2.2a
- **İlgili test:** TC-PLR-108

#### NE-PLR-109 — Ring STARVE/OVERFLOW davranışları
- **Açıklama:** STARVE → fade ≤50 ms; OVERFLOW → drop (akış sürekli).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Ağ yavaşlatma (tc/netem) senaryosu; §04.2 şeması.
- **ADR izi:** ADR-106 §2.2a · kardeş PRD §07.4
- **İlgili test:** TC-PLR-109

#### NE-PLR-110 — TunerThread non-RT ayrımı
- **Açıklama:** Ağ/decode/parse non-RT; RT yalnız ring→DSP→backend.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Statik + thread adlandırma; RT'de ağ çağrısı yok.
- **ADR izi:** ADR-106 §2.2a · ADR-017
- **İlgili test:** TC-PLR-110

#### NE-PLR-111 — R128 sidechain non-RT
- **Açıklama:** Loudness hesap non-RT yan thread; UI/LUFS.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** RT etkilenmez (profil); canlı LUFS akar.
- **ADR izi:** ADR-106 §2.2a
- **İlgili test:** TC-PLR-111

#### NE-PLR-112 — Metadata event zinciri (sanitize → lock-free kuyruk → C ABI)
- **Açıklama:** §04.2 şeması; UI thread marshal.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** 1000 mesaj/sn spam istasyonunda UI donmaz (kuyruk cap + drop sayacı `[ÖNERİ]`).
- **ADR izi:** ADR-106 §2.2a · kardeş PRD §05.2 E1
- **İlgili test:** TC-PLR-112

#### NE-PLR-113 — Radyo kataloğu (SQLite, ADR-074)
- **Açıklama:** Yerel katalog şeması ADR-074 ile uyumlu; çevrimdışı öncelik.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Migrate edilebilir şema; ekleme/düzenleme/silme.
- **ADR izi:** ADR-074 · ADR-106 kural 3
- **İlgili test:** TC-PLR-113

#### NE-PLR-114 — Radio Browser API keşfi (opsiyonel)
- **Açıklama:** Açık kaynak dizin; https mirror + `url_resolved` validator.
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** Ağ yoksa tamamen gizli; keşif sonuçları validator'dan geçmeden listelenmez.
- **ADR izi:** ADR-106 kural 3
- **İlgili test:** TC-PLR-114

#### NE-PLR-115 — İstasyon test aracı (URL test/validate)
- **Açıklama:** Kullanıcı kendi istasyonunu eklerken test etme.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Test: ICY header kontrolü + ilk N saniye decode + codec raporu.
- **ADR izi:** ADR-106 §2.2a (AIMP emsal Catalogs-Radio.ini)
- **İlgili test:** TC-PLR-115

#### NE-PLR-116 — Son istasyon hatırlama
- **Açıklama:** Açılışta son istasyon (tercih kalıcı) `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Config §06 ile aynı depo.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-116

#### NE-PLR-117 — Plasyon listesi UI (kategori/favori)
- **Açıklama:** Favori + son oynatılanlar; arama.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Katalog verisi ADR-074 alanları; render sanitizasyonlu (NE-PLR-101).
- **ADR izi:** ADR-106 kural 3 · ADR-074
- **İlgili test:** TC-PLR-117

#### NE-PLR-118 — 100 istasyon stres testi
- **Açıklama:** ADR-106 §5.2 Ş3/Ş5/Ş6: 100 istasyonu 1× hızda oynat; 0 çökme; metaveri akıyor; reconnect 1s/4s/8s.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Rapor: oynatılan/istenen; backoff ölçümleri.
- **ADR izi:** ADR-106 §5.2 Ş3/Ş5/Ş6
- **İlgili test:** TC-PLR-118

#### NE-PLR-119 — 10 istasyon paralel (tuning kuyruğu)
- **Açıklama:** ADR-106 Ş2: 10 paralel tuning; gerçekçi 1-2 aktif akış (kuyruk yönetimi).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Aynı anda 10 connect isteği: 1-2 aktif; kalanlar kuyruk/bırakma politikası (`⚠️ VR — politika detayı ADR-106'da yok; önerü: kullanıcı sinyali bekleyenleri iptal eder, arka planda kuyruk ≤2 devam eder)` .
- **ADR izi:** ADR-106 §5.2 Ş2
- **İlgili test:** TC-PLR-119

#### NE-PLR-120 — Fuzz: HLS playlist + ICY corpus
- **Açıklama:** ADR-106 Ş7: her decoder + ICY + HLS playlist için corpus.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 0 crash; kapı 5.13'e entegre.
- **ADR izi:** ADR-106 §5.2 Ş7 · ADR-0105 kapı 5.13 ⚠️
- **İlgili test:** TC-PLR-120

### §10.9 Grup G — EQ (Kullanıcı Sözleşmesi)

#### NE-PLR-121 — Bant sayısı seçimi (10/15/20/31)
- **Açıklama:** UI'da dört seçenekten biri (ADR-106 §2.2b kural 2); varsayılan 31? — varsayılan `⚠️ VR (ADR-106 varsayılan belirtmiyor; önerü: 31)` .
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Seçim anında aktif; E5 API ile engine'e iletilir; maskeler atomik yayılır.
- **ADR izi:** ADR-106 §2.2b kural 2
- **İlgili test:** TC-PLR-121

#### NE-PLR-122 — 31-band tam grid
- **Açıklama:** §08.1 frekans listesi (20…20000 Hz, ISO 1/3-oktav).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Her bandın frekans etiketi grid'e birebir; `eq_grids_tests` yeşil.
- **ADR izi:** ADR-106 §2.2b · disk `eq_grids.h`
- **İlgili test:** TC-PLR-122

#### NE-PLR-123 — 10-band ISO alt küme
- **Açıklama:** 31.5/63/125/250/500/1k/2k/4k/8k/16k (ISO; VLC 62.5 farkı bilinçli).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** 10-band modda slider'lar bu frekanslarda; VLC preset'i kendi setiyle okunur (zorla map yok).
- **ADR izi:** ADR-106 §2.2b kural 3
- **İlgili test:** TC-PLR-123

#### NE-PLR-124 — 20-band (AIMP emsal aralığı)
- **Açıklama:** Band0..19 aralığı; gerçek frekanslar `⚠️ VR`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** `eq_grids` tanımı sabit + teyit edilir; etiket korunur.
- **ADR izi:** ADR-106 §2.2b kural 4 · ADR-106 §1.4
- **İlgili test:** TC-PLR-124

#### NE-PLR-125 — 15-band alt küme
- **Açıklama:** Eşit log-aralık `⚠️ VR`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** TEyitli listeyi kadar kilitli `[ÖNERİ — teyide kadar UI'da gizli]`.
- **ADR izi:** ADR-106 §2.2b kural 5
- **İlgili test:** TC-PLR-125

#### NE-PLR-126 — Biquad bütçesi sabitliği
- **Açıklama:** 31×2=62 (cascade 124) sabit allocate; bant sayısı azalışında tahsis YOK (bypass maskesi).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Bant değiştirme sırasında tahsis yok (profil); 31-band worst-case benchmark.
- **ADR izi:** ADR-106 kural 4/6 · kardeş PRD §06.2
- **İlgili test:** TC-PLR-126

#### NE-PLR-127 — Ramp 5-20 ms (click yasağı)
- **Açıklama:** Gain/bant değişimi ramp'li.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Slider spam'de pop yok (waveform); ramp süresi seçilebilir `[ÖNERİ — UI'da 5/10/20 ms]`.
- **ADR izi:** ADR-105 §2.3 (ADR-106 kural 5) ⚠️
- **İlgili test:** TC-PLR-127

#### NE-PLR-128 — Aktif olmayan band bypass
- **Açıklama:** 10-band modda 21 pasif biquad maskeyle devre dışı.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CPU profili 10-band ile 31-band arasında (masraf düşer ama tahsis kalır).
- **ADR izi:** ADR-106 kural 4
- **İlgili test:** TC-PLR-128

#### NE-PLR-129 — Preset biçimi (taşıma)
- **Açıklama:** `{grid_version, band_count, per-band gain, preamp}` — JSON dosyası `[ÖNERİ — format]`.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Farklı makineye taşınabilir; grid_version uyuşmazlığında düzeltme + uyarı.
- **ADR izi:** ADR-106 kural 6
- **İlgili test:** TC-PLR-129

#### NE-PLR-130 — Preset kaydet/yükle/sil/adlandır
- **Açıklama:** Fabrika + kullanıcı preset'leri; kullanıcı yazma alanı config altı.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CRUD; fabrika preset'leri silinemez `[ÖNERİ]`.
- **ADR izi:** ADR-106 kural 6 · §10 NE-PLR-010
- **İlgili test:** TC-PLR-130

#### NE-PLR-131 — Fabrika preset seti
- **Açıklama:** Flat · Bass Boost · Treble Boost · Vocal · Loudness · Pop/Rock/Jazz `⚠️ VR (set içeriği ADR'de yok — önerü)`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Her preset 31-band vektörü (taşıma formatında); Flat = all-zero (bit-perfect ön koşulu).
- **ADR izi:** YOK — `[ÖNERİ]` (ADR-106 yalnız preset biçimini tanımlar)
- **İlgili test:** TC-PLR-131

#### NE-PLR-132 — Flat/Bypass düğmesi
- **Açıklama:** Flat (0 dB tümü) ≠ Bypass (maske tamamen pasif — zincir bypass) — ikisi de bit-perfect'e izin verir `[ÖNERİ — fark]`.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** İkisi de hash yolunu açar; UI'da ayrım net.
- **ADR izi:** kardeş PRD §06.1 D3 · §05.2 B3
- **İlgili test:** TC-PLR-132

#### NE-PLR-133 — Preamp slider'ı
- **Açıklama:** Zincirde global gain; clip önleme uyarısı `⚠️ VR (eşiq — önerü: +6 dB'de uyarı, true-peak limiter korur)`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Limiter aşımı engeller; UI'da clip göstergesi `[ÖNERİ]`.
- **ADR izi:** kardeş PRD §06.4
- **İlgili test:** TC-PLR-133

#### NE-PLR-134 — Bant frekansı değiştirilemez
- **Açıklama:** Grid sabit; kullanıcı serbest frekans ayarlayamaz (parametrik ayrı mod).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** UI'da frekans alanı salt-okunur (etiket).
- **ADR izi:** ADR-106 kural 7 · ADR-0105 §2.1
- **İlgili test:** TC-PLR-134

#### NE-PLR-135 — Parametrik mod (ayrı sekme, M5+)
- **Açıklama:** RBJ serbest: f/Q/gain per bant; grafik grid'den bağımsız state.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** İki mod geçişinde ramp; preset'ler ayrı kasa `[ÖNERİ]`.
- **ADR izi:** ADR-106 §2.2b kural 7 · ADR-0105 §2.1
- **İlgili test:** TC-PLR-135

#### NE-PLR-136 — Radyoda EQ aktif
- **Açıklama:** Akış da aynı zincirden geçer (ADR-106 kural 4 — kullanıcı radyoda EQ ister).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Radyo modunda EQ preset uygulanır; bit-perfect rozeti YOK (lossy).
- **ADR izi:** ADR-106 kural 4
- **İlgili test:** TC-PLR-136

#### NE-PLR-137 — EQ UI widget'ları
- **Açıklama:** Slider'lar (31'e kadar), hover frekans etiketi, dB değeri, klavye ok tuşu `[ÖNERİ]`; erişilebilirlik §02.3 I-11.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** 31 slider performanslı (virtualize `[ÖNERİ]`); keyboard-only erişim.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-137

#### NE-PLR-138 — EQ görselleştirme (yanıt eğrisi)
- **Açıklama:** Toplam frekans yanıtı çizimi (biquad katsayılarından hesap) `[ÖNERİ]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** 31 bant toplamı; ≤10 Hz yenileme non-RT.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-138

#### NE-PLR-139 — neva-eq ayrı pencere
- **Açıklama:** VoiceMeeter BUSGEQ15 emsal — EQ ayrı exe/pencere.
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** Ayrı süreç; C ABI E5/E6; ana UI ile senkron event.
- **ADR izi:** ADR-107 K2 · kardeş PRD §04.7
- **İlgili test:** TC-PLR-139

#### NE-PLR-140 — EQ test vektörleri (radyo+dosya)
- **Açıklama:** ADR-106 §5.2 Ş5: 10/15/20/31 mod geçişleri + ramp ölçümleri.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Ş5: 0 çökme; ramp süresi ölçüm raporu.
- **ADR izi:** ADR-106 §5.2 Ş5
- **İlgili test:** TC-PLR-140

#### NE-PLR-141 — LUFS hedefleme (normalizasyon)
- **Açıklama:** R128 sidechain'den okunan LUFS + hedef gain `[ÖNERİ — hedef: -14 LUFS; ADR-VR]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Kapalıyken 0 etki (bit-perfect); açıkken limiter ön koşulu.
- **ADR izi:** kardeş PRD §06.4 `[ÖNERİ]`
- **İlgili test:** TC-PLR-141

#### NE-PLR-142 — Crossfade ≠ gapless ayrımı (EQ etkileşimi)
- **Açıklama:** Crossfade açıkken iki decode oturumu → EQ state geçişi `[ÖNERİ]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Geçişte EQ korunur (state yeni oturuma kopyalanır + ramp).
- **ADR izi:** kardeş PRD NE-PLR-067
- **İlgili test:** TC-PLR-142

#### NE-PLR-143 — Cihaz-bağımlı EQ hatırlama
- **Açıklama:** EQ preset'i cihaza bağlı hatırlanabilir `[ÖNERİ]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Cihaz değişince preset değişimi teyitli.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-143

#### NE-PLR-144 — Preset import/export (dosya)
- **Açıklama:** Taşıma formatı dosya paylaşımı (AIMP/VLC preset dönüştürücü `⚠️ VR — dönüştürücü kapsamı ayrı karar)` .
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Export/import; dönüştürücü yalnız kendi frekans setiyle.
- **ADR izi:** ADR-106 §1.4 (VLC/AIMP dump) · kardeş PRD §08.2
- **İlgili test:** TC-PLR-144

#### NE-PLR-145 — Grid teyit işi (15/20-band kapanışı)
- **Açıklama:** §00.3-D5/NE-PLR-124/125 açık maddelerinin kapanışı: `eq_grids` tanım dosyası + kaynak teyidi.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** ADR-106 §1.4'teki açık madde kapanır; etiketler kaldırılır/gerekçelenir.
- **ADR izi:** ADR-106 §1.4
- **İlgili test:** TC-PLR-145

### §10.10 Grup H — Kütüphane, UI & Durum Makinesi

#### NE-PLR-146 — Yerel kütüphane (SQLite)
- **Açıklama:** Taranan/eklenen dosyalar + metadata; ADR-074/ADR-004 şemaları.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Tarama → indeks → arama (sanatçı/album/tür/yıl); yeniden tarama artımlı.
- **ADR izi:** ADR-074 · ADR-004
- **İlgili test:** TC-PLR-146

#### NE-PLR-147 — Çevrimdışı öncelik (kütüphane)
- **Açıklama:** Ağ yokken tüm yerel işlevler (NE-PLR-013).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Uçak modu testi.
- **ADR izi:** AGENTS §17 Edge 9
- **İlgili test:** TC-PLR-147

#### NE-PLR-148 — Queue düzenleme
- **Açıklama:** Sıralama/düzenleme/silme; gapless ön koşulu (sıradaki biliniyor).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Sıra değişiminde prefetch yeniden planlanır.
- **ADR izi:** kardeş PRD §05.2 E2
- **İlgili test:** TC-PLR-148

#### NE-PLR-149 — Durum makinesi geçişleri (ADR-019 §1.4)
- **Açıklama:** §04.5 tablo; web footer player ile aynı semantik.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Geçersiz geçişler reddedilir; her geçiş event E1 ile UI'a akar.
- **ADR izi:** ADR-019 §1.4 · ADR-018
- **İlgili test:** TC-PLR-149

#### NE-PLR-150 — LOADING durumu UX
- **Açıklama:** Dosya prefetch + radyo prebuffer 2-4 s; spinner + istasyon adı.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** LOADING→PLAYING geçiş süresi ölçülür; 4 s aşımları kullanıcıya yansır.
- **ADR izi:** ADR-106 §2.2a
- **İlgili test:** TC-PLR-150

#### NE-PLR-151 — ERROR durumu UX
- **Açıklama:** Açıklanabilir mesaj (Türkçe) + "yeniden dene" `[ÖNERİ]`; crash YOK.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 3 başarısız reconnect → ERROR + istasyon adı + son hata kodu.
- **ADR izi:** ADR-106 kural 8 · kardeş PRD §13
- **İlgili test:** TC-PLR-151

#### NE-PLR-152 — Transport kontrolleri
- **Açıklama:** Play/pause/stop/next/prev/seek; kısayollar `[ÖNERI]`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** C ABI E3/E4; radyoda seek devre dışı, next/prev katalog gezinmesi.
- **ADR izi:** kardeş PRD §05.2
- **İlgili test:** TC-PLR-152

#### NE-PLR-153 — Cihaz seçimi UI (liste + varsayılan)
- **Açıklama:** Liste + varsayılan hatırlama + hot-plug güncelleme.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Liste canlı (event E8); tercih kalıcı.
- **ADR izi:** ADR-019 F1 · kardeş PRD §05.2 E8
- **İlgili test:** TC-PLR-153

#### NE-PLR-154 — Fallback bilgilendirmesi (UI)
- **Açıklama:** Exclusive→shared / ASIO→WASAPI geçişleri görünür (§09.2).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Bilgi satırı + rozet durumu (NE-PLR-043).
- **ADR izi:** kardeş PRD §04.4-§04.5
- **İlgili test:** TC-PLR-154

#### NE-PLR-155 — Metadata gösterimi
- **Açıklama:** Başlık/album/sanatçı + kapak; sanitizasyonlu (NE-PLR-101); 256 bayt cap UI'da da.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Uzun metin kırpma + tooltip `[ÖNERİ]`; XSS payload düz metin.
- **ADR izi:** ADR-106 kural 7
- **İlgili test:** TC-PLR-155

#### NE-PLR-156 — Zamanlayıcı (uyku modu) `[ÖNERİ]`
- **Açıklama:** Persona Ceyda (SC-04): süre/dosya sonu + kapat.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Süre bitiminde fade → STOPPED; dosya sonunda queue bitişi.
- **ADR izi:** YOK — `[ÖNERİ — persona türetme]`
- **İlgili test:** TC-PLR-156

#### NE-PLR-157 — Sıcaklık/kulak zarar koruması `[ÖNERİ]`
- **Açıklama:** Düşük ses + uzun süre uyarısı (persona Ceyda).
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Eşik LUFS+sure; kapatılabilir.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-157

#### NE-PLR-158 — Klavye kısayolları
- **Açıklama:** Boşluk=play/pause, ok=seek/volume `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Çakışma yok (EQ sekmesi odaktayken slider okları korunur).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-158

#### NE-PLR-159 — Tray/sistem tepsisi `[ÖNERİ]`
- **Açıklama:** Kapatınca kapat (minimize değil); hızlı transport.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Tray menü; oynatma kapanışta durur (engine session temiz).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-159

#### NE-PLR-160 — Çoklu pencere (main + eq + matrix)
- **Açıklama:** neva-host köprüsü ile; tek instance lock `[ÖNERİ]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** İkinci instance açılışta öne çıkarır (single-instance).
- **ADR izi:** kardeş PRD NE-ENG-105 `[ÖNERİ]`
- **İlgili test:** TC-PLR-160

#### NE-PLR-161 — UI erişilebilirliği (klavye/focus)
- **Açıklama:** Masaüstü a11y: focus görünür, tüm işlevler keyboard-only `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Checklist; ekran okuyucu uyumu `⚠️ VR (toolkit seçimi §18.1)` .
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-161

#### NE-PLR-162 — Karanlık/aydınlık tema `[ÖNERİ]`
- **Açıklama:** Sistem teması takibi; token tabanlı (CoreMusic `--cm-*` ruhu).
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** İki tema; sabit değer yok (token).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-162

#### NE-PLR-163 — UI performans (oynatma sırasında)
- **Açıklama:** UI thread'i RT'yi engellemez; 60 fps transport animasyonu `⚠️ VR (eşik önerü)`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** UI spam'de xrun 0.
- **ADR izi:** kardeş PRD §07 · NE-PLR-008
- **İlgili test:** TC-PLR-163

#### NE-PLR-164 — Config şeması sürümleme
- **Açıklama:** Config + preset + katalog dosyalarında schema_version.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Eski sürüm → migrate; okunamayan → default + yedek.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-164

#### NE-PLR-165 — Crash kurtarma (yeniden başlatma)
- **Açıklama:** Çökme sonrası yeniden açılış: son durum kurtarılır `[ÖNERİ — otopsi dosyası yok]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Sağlam kapanış bayrağı; crash sonrası sessiz başlangıç.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-165

### §10.11 Grup I — Güvenlik

#### NE-PLR-166 — OWASP tabanlı tehdit modeli
- **Açıklama:** Masaüstü oynatıcı yüzeyi: dosya girişi, ağ (radyo/HLS), metadata render, config/preset dosyaları, FFI sınırı.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §12.1 tehdit tablosu her release'de gözden geçirilir; yeni saldırı yüzeyi (yeni format/protokol) tabloya eklenmeden merge yok.
- **ADR izi:** ADR-106 §5 (security debate S1-S8) · AGENTS §5
- **İlgili test:** TC-PLR-166

#### NE-PLR-167 — SSRF/özel-IP reddi (URL validator)
- **Açıklama:** 127.0.0.0/8 · 10/8 · 172.16/12 · 192.168/16 · 169.254/16 (cloud metadata) · ::1 · fc00::/7 · link-local RED; her redirect hop'ta + post-connect IP kontrolü (DNS rebinding).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §12.2 vektörleri: `http://127.0.0.1` · `http://169.254.169.254/latest/meta-data/` · `http://[::1]` · zincirleme redirect'te private IP → hepsi RED; yalnız IPv4 literal değil, DNS çözümü de kontrol edilir (çözüm → RED, sonra bağlanmadan iptal).
- **ADR izi:** ADR-106 kural 6 (S3)
- **İlgili test:** TC-PLR-167

#### NE-PLR-168 — Zincirleme redirect ≤5 (güvenlik sürümü)
- **Açıklama:** Her hop validator'dan geçer; 5. hop son; kullanıcıya sürpriz hedef yok.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 6 hop'lu zincir RED; her hop'ta protocol + IP tekrar doğrulanır (NE-PLR-023 ile aynı test).
- **ADR izi:** ADR-106 kural 6
- **İlgili test:** TC-PLR-168

#### NE-PLR-169 — HTTPS zorunlu + sertifika doğrulaması
- **Açıklama:** mbedTLS zincir doğrulaması; kör kabul yasak; http yalnız kullanıcı açık onayı ile `[ÖNERİ — onay kutusu tek sefer + hatırla]`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Self-signed / süresi dolmuş / hostname uyuşmazlığı RED; kapatma bayrağı yok (ayar gizli değil, varsayılan kapalı).
- **ADR izi:** ADR-106 §5.4 (mbedTLS 9.13.0)
- **İlgili test:** TC-PLR-169

#### NE-PLR-170 — HTML-escape (StreamTitle/StreamUrl/render)
- **Açıklama:** Tüm metadata UI'a escape edilerek akar; ham string render yolu yok.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `<script>` payload'lı başlık düz metin görünür; escape katmanı tek yerde (render öncesi) — profil: escape edilmemiş yol varsa statik denetim FAIL.
- **ADR izi:** ADR-106 kural 7 (security S1)
- **İlgili test:** TC-PLR-170

#### NE-PLR-171 — 256 bayt cap + UTF-8 doğrulama (metadata)
- **Açıklama:** Text blok ≤1 MB; tek alan ≤256 bayt; geçersiz UTF-8 → kırp/temizle (çökme yok).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Fuzz corpus'unda geçersiz UTF-8 dizgileri; 0 crash; UI'da mojibake yok (replace karakter).
- **ADR izi:** ADR-106 kural 7/8
- **İlgili test:** TC-PLR-171

#### NE-PLR-172 — XML DTD/kök XXE reddi (ICY v2)
- **Açıklama:** DTD çözümleme kapalı; yalnız şema gerekli alanlar okunur; 1 MB cap.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `<!DOCTYPE ... SYSTEM "file:///C:/...">` içeren metaveri → reddedilir; dosya erişimi tetiklenmez.
- **ADR izi:** ADR-106 kural 8 (Ş8)
- **İlgili test:** TC-PLR-172

#### NE-PLR-173 — HLS segment cap (50 MB) + entry cap (1000) + derinlik (≤1)
- **Açıklama:** M3U8 bütçe kapıları; taşma = RED (bellek bütçesi koruması).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 1001 girdili playlist RED; 51 MB segment RED; iç içe playlist RED.
- **ADR izi:** ADR-106 kural 8 (Ş8) · §07.5 Ş8
- **İlgili test:** TC-PLR-173

#### NE-PLR-174 — Protocol whitelist (dosya/concat/crypto reddi)
- **Açıklama:** FFmpeg'e yalnız http/https; `file:`/`concat:`/`crypto:`/`subfile:` RED.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `concat:http|file` gibi karışık dizgiler de RED (parser bypass denemeleri §12.2).
- **ADR izi:** ADR-106 kural 6
- **İlgili test:** TC-PLR-174

#### NE-PLR-175 — Config/preset dosyalarında güvenli ayrıştırma
- **Açıklama:** JSON/parser; `eval`/`unserialize` benzeri yasak (ADR-091); bilinmeyen anahtarlar ignore + cap (bellek taşırma yok).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 10 MB'luk "preset" dosyası RED/ kırpılır; derin iç içe JSON → depth cap `[ÖNERİ — 64]`.
- **ADR izi:** ADR-091 · AGENTS §5 (unserialize(user_input) yasak)
- **İlgili test:** TC-PLR-175

#### NE-PLR-176 — Katalog/SQLite injection koruması
- **Açıklama:** Parametreli sorgu zorunlu; string concat SQL yasak.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `O'Brien` sanatçı adı + SQL payload arama sorguları; sonuç beklenen; statik denetim (concat sorgu yok).
- **ADR izi:** AGENTS §5 (parametreli sorgu zorunlu, SELECT * yasak)
- **İlgili test:** TC-PLR-176

#### NE-PLR-177 — Yol güvenliği (path traversal)
- **Açıklama:** Tarama kökleri dışına çıkamama; `../` + sembollik/UNC yolları (share) `[ÖNERİ — UNC kopyalama politikası: çalma serbest, indeksleme kök sınırlı]`.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Tarama dizininde `..` kaçışı RED; kısayol/AGPL içeriği değil sadece indeks kapsamı.
- **ADR izi:** YOK — `[ÖNERİ]` (klasik dosya güvenliği)
- **İlgili test:** TC-PLR-177

#### NE-PLR-178 — FFI sınırında clamp (engine'le ortak)
- **Açıklama:** C ABI çağrılarında band index/gain/ms/path clamp; NULL/uzunluk kontrolü.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Negatif index · 1e9 ms · 10000 karakter path → INVALID_ARG (NE-ENG-157 ile aynı vektör).
- **ADR izi:** kardeş PRD §05.1
- **İlgili test:** TC-PLR-178

#### NE-PLR-179 — Tek instance + IPC yüzeyi (neva-host)
- **Açıklama:** IPC yalnız localhost/pipes + basit kimlik (aynı kullanıcı SID) `[ÖNERİ — SID kontrolü Windows; Linux UID]`; ağ portu ADR kararına bağlı (U4).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Farklı kullanıcıdan gelen IPC mesajı reddedilir; mesaj şeması doğrulanır (bilinmeyen mesaj → drop).
- **ADR izi:** ADR-107 §8.6 (U4) · kardeş PRD NE-ENG-105
- **İlgili test:** TC-PLR-179

#### NE-PLR-180 — Supply chain (bağımlılık taraması)
- **Açıklama:** CVE taraması + sürüm sabitleme (lock); gizli veri yok.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** CI dependency-audit; kritik CVE = release bloke; lock dosyası repo'da (FFmpeg harici vendor `⚠️ VR — vendor stratejisi §14.5)`.
- **ADR izi:** AGENTS §5 · kardeş PRD NE-ENG-114
- **İlgili test:** TC-PLR-180

### §10.12 Grup J — Bütçeler & Test Kapıları

#### NE-PLR-181 — RAM ≤400 MB bütçesi (ölçümü)
- **Açıklama:** Kütüphane + decode + video OFF durumu; video ON ayrı satır (§06.5).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Ölçüm: 1000 dosyalık kütüphane + oynatma + EQ 31-band; RSS ≤400 MB.
- **ADR izi:** ADR-0105 §2.9 ⚠️
- **İlgili test:** TC-PLR-181

#### NE-PLR-182 — CPU bütçesi (ses-only)
- **Açıklama:** 192k/32f decode + 31-band EQ + limiter → tek çekirdek ≤%5 EQ payı (NE-ENG-077); decode payı `⚠️ VR`.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Bench raporu §06.4.
- **ADR izi:** ADR-0105 §5.6 ⚠️ · ADR-106 §1.3
- **İlgili test:** TC-PLR-182

#### NE-PLR-183 — Latency <20 ms (ölçüm)
- **Açıklama:** Kapı 5.10; L1-L4 `⚠️ VR`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Rapor; kardeş PRD TC-ENG-089/090 ile ortak düzenek.
- **ADR izi:** ADR-0105 §2.10 ⚠️
- **İlgili test:** TC-PLR-183

#### NE-PLR-184 — Kapı 5.1 (bit-perfect) — player koşulları
- **Açıklama:** §11.2 hattı + inverted guard + flat/bypass ön koşulu.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 12/12 + guard negatif denetimi.
- **ADR izi:** ADR-0105 kapı 5.1 ⚠️ · ADR-106 §5.1 Ş1/Ş4
- **İlgili test:** TC-PLR-087/040

#### NE-PLR-185 — Kapı 5.10 (gecikme)
- **Açıklama:** Release adayı zorunlu ölçüm.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Rapor arşivi (NE-PLR-084 ile aynı).
- **ADR izi:** ADR-0105 kapı 5.10 ⚠️
- **İlgili test:** TC-PLR-084/183

#### NE-PLR-186 — Kapı 5.11 (A/V sync P95 ≤20 ms)
- **Açıklama:** Video fazı başladığında zorunlu; ses fazında koşul dışı.
- **Tip:** MUST · **Öncelik:** P2 (video fazında P1)
- **Kabul kriteri:** P95 raporu + max sapma.
- **ADR izi:** ADR-0105 kapı 5.11 ⚠️
- **İlgili test:** TC-PLR-078/090

#### NE-PLR-187 — Kapı 5.12 (gapless vektörleri)
- **Açıklama:** MP3/FLAC/Opus/DSD çiftleri + radio stream→stream + loopback ±5 ms.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Grup D testlerinin tamamı + ölçüm raporu.
- **ADR izi:** ADR-0105 kapı 5.12 ⚠️
- **İlgili test:** TC-PLR-086

#### NE-PLR-188 — Kapı 5.13 (fuzz)
- **Açıklama:** Format başına ≥1000 · toplam ≥8000 · 0 crash · CI ≤10 dk; radyo: her decoder + ICY + HLS playlist.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Fuzz raporu release dosyasında.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️ · ADR-106 Ş7
- **İlgili test:** TC-PLR-085/120

#### NE-PLR-189 — RT stres (player:UI spam + uzun süre)
- **Açıklama:** 30 dk oynatma + UI otomatik tıklama; xrun=0, UI crash=0.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kardeş PRD G4 ile birlikte raporlanır.
- **ADR izi:** S3 §6.1 · NE-PLR-163
- **İlgili test:** TC-PLR-163/054

#### NE-PLR-190 — Leak (1000 cycle play/stop + katalog tarama)
- **Açıklama:** Net artış 0.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** LSan/Windows LeakSanitizer temiz `⚠️ VR (Windows aracı seçimi)` .
- **ADR izi:** kardeş PRD NE-ENG-141
- **İlgili test:** TC-PLR-190

#### NE-PLR-191 — TSan (çoklu-thread: UI + tuner + control spam)
- **Açıklama:** 0 data race.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Linux CI sanitizörlü build.
- **ADR izi:** kardeş PRD NE-ENG-131
- **İlgili test:** TC-PLR-191

#### NE-PLR-192 — Radyo stres vektörleri (ADR-106 §5.2 Ş1-Ş7)
- **Açıklama:** Ş1 slow-start · Ş2 10 paralel · Ş3 100 istasyon · Ş4 inverted guard · Ş5 EQ vektörleri · Ş6 oynatma bütçesi · Ş7 fuzz.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** 7/7 rapor; §11.5'te tek tek.
- **ADR izi:** ADR-106 §5.2
- **İlgili test:** TC-PLR-118/119/140/120

#### NE-PLR-193 — UI a11y checklist (klavye/focus/renk)
- **Açıklama:** Masaüstü yüzeyi; §02.3 I-11.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Checklist release öncesi doldurulur.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-161

#### NE-PLR-194 — Yerelleştirme testi (TR/EN) — karara bağlı
- **Açıklama:** §18.2 kararı verilirse.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Karar yoksa test yok (kapsam dışı etiketi korunur).
- **ADR izi:** YOK
- **İlgili test:** TC-PLR-194 (şartlı)

#### NE-PLR-195 — Metrik regresyon kapısı (%10)
- **Açıklama:** RAM/CPU/boyut baseline; kötüleşme etiketler (politika: warn).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Baseline + PR etiketi.
- **ADR izi:** kardeş PRD NE-ENG-100 `[ÖNERİ]`
- **İlgili test:** TC-PLR-195

#### NE-PLR-196 — Test raporu şablonu (release)
- **Açıklama:** Kapı 5.1/5.10-5.13 + radyo Ş1-Ş7 tek dosyada.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Şablon doldurulmuş olarak release PR'ında.
- **ADR izi:** kardeş PRD NE-ENG-144
- **İlgili test:** —

#### NE-PLR-197 — Determinism (2× çalıştırma hash)
- **Açıklama:** Aynı girdi → aynı hash.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI 2× çalıştırma.
- **ADR izi:** kardeş PRD NE-ENG-145
- **İlgili test:** TC-PLR-197

#### NE-PLR-198 — ABI sembol denetimi (player birleşimi)
- **Açıklama:** Player yalnız `neva_*` çağırır; iç sembol yok.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CI grep.
- **ADR izi:** kardeş PRD NE-ENG-142
- **İlgili test:** TC-PLR-198

#### NE-PLR-199 — Kontrat testi (durum makinesi + event zinciri)
- **Açıklama:** §04.5 tablo + E1-E10 event'leri otomatik.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Geçersiz geçiş reddi + event sırası doğrulaması.
- **ADR izi:** ADR-019 §1.4 · kardeş PRD §05.2
- **İlgili test:** TC-PLR-149/199

#### NE-PLR-200 — Flaky test yasağı
- **Açıklama:** Yeniden çalışınca geçen = hata; yeniden-dene döngüsü yok.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CI'da retry=0.
- **ADR izi:** kardeş PRD §11.5 (bu PRD) `[ÖNERİ]`
- **İlgili test:** —

### §10.13 Grup K — FFI, MCP & Dış API

#### NE-PLR-201 — C ABI minor sürüm genişlemesi (E1-E10)
- **Açıklama:** Event/queue/position/EQ/tuner API'leri minor sürümle eklenir; mevcut 9 fonksiyon + 5 kod değişmez.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** ABI diff raporu; eski header ile yeni DLL çalışır (forward uyum).
- **ADR izi:** ADR-106 kural 2 · kardeş PRD §05.2/§05.4
- **İlgili test:** TC-PLR-201

#### NE-PLR-202 — FFI dilleri (Python ctypes · C# P/Invoke · 25 dil)
- **Açıklama:** ABI C99+ olan her dilden bağlanabilir (Vision: 25 dil U2 `[ÖNERİ — tanım NE-PLR-203]`).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Örnek: Python ctypes ile play/stop smoke testi (CI opsiyonel).
- **ADR izi:** ADR-0105 v1.0.2 §3 ⚠️ · Vision (25 dil)
- **İlgili test:** TC-PLR-202

#### NE-PLR-203 — "25 dil" tanımı (metrik)
- **Açıklama:** U2 kapsamındaki 25 dil listesi yok (`UNKNOWN`); ölçüm bu PRD'de tanımsız.
- **Tip:** SHOULD (karar) · **Öncelik:** P3
- **Kabul kriteri:** Karar ADR veya bu PRD §18.5'te listelenir; ölçüm "C ABI + 5 dil smoke" olarak daraltılabilir `[ÖNERİ]`.
- **ADR izi:** ADR-0105 U2 ⚠️ · Vision
- **İlgili test:** TC-PLR-203

#### NE-PLR-204 — neva-mcp (MCP sunucu)
- **Açıklama:** Dış AI/agent araçlarından transport kontrolü; dil `UNKNOWN` (U5).
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Karar sonrası: bridge C ABI üzerinden; yetki modeli minimal `[ÖNERİ — salt localhost + yalnız transport komutları]`.
- **ADR izi:** ADR-0105 v1.4.0 `[ÖNERİ]` · ADR-107 §8.5 (kardeş)
- **İlgili test:** TC-PLR-204 (şartlı)

#### NE-PLR-205 — neva-remote (VoiceMeeterRemote/VBAN emsal)
- **Açıklama:** Uzaktan kontrol API'si; ağ yüzeyi ayrı hedefte.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Tasarım ADR (auth modeli `[ÖNERİ — token/localhost varsayılan]`); core'a ağ girmez ( kardeş NE-ENG-108/159).
- **ADR izi:** ADR-0105 v1.4.0 `[ÖNERİ]` · ADR-107 K2
- **İlgili test:** TC-PLR-205 (şartlı)

#### NE-PLR-206 — Web UI (UPnify emsal) — U4 kararı
- **Açıklama:** Tarayıcıdan kontrol emsal var (UPnify); Neva kararı `UNKNOWN`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Karar ADR; şifre/localhost varsayılan `[ÖNERİ]` (UPnify: şifreli işlevsellik — emsal, kopya değil).
- **ADR izi:** ADR-0105 U4 ⚠️ · ADR-107 §8.6
- **İlgili test:** TC-PLR-206 (şartlı)

#### NE-PLR-207 — Katalog dışa aktarma (CSV/M3U) `[ÖNERI]`
- **Açıklama:** Çalma listesi paylaşımı (dosya yolları dışa aktarımı — gizlilik notu `[ÖNERİ]`).
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** M3U/CSV; yol yazımı kullanıcı onaylı (yerel yol sızıntısı bilinçli).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-207

#### NE-PLR-208 — M3U/M3U8 çalma listesi içe aktarma (yerel)
- **Açıklama:** Yerel playlist içe aktarma; iç satırlar validator'dan geçer (http satırları) `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** 1000 satır cap; `file:` satırları kök sınırlamasıyla (NE-PLR-177).
- **ADR izi:** YOK — `[ÖNERİ]` (radyo playlist'i ile ayrık: ADR-106 kural 8)
- **İlgili test:** TC-PLR-208

#### NE-PLR-209 — Hotkey/global kısayol (OS kaydı) `[ÖNERİ]`
- **Açıklama:** Medya tuşları (play/pause/next) OS seviyesinde.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Çakışma yönetimi; kayıt geri alınabilir.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-209

#### NE-PLR-210 — Bildirim (OS toast) `[ÖNERİ]`
- **Açıklama:** İstasyon değişimi/şarkı değişimi bildirimi; kapatılabilir.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Metadata sanitizasyonlu (NE-PLR-101); varsayılan kapalı `[ÖNERİ]`.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-210

#### NE-PLR-211 — Plugin yüzeyi yok (kapsam kararı)
- **Açıklama:** Player'da üçüncü parti plugin/loading KAPSAM DIŞI (FFmpeg/VST3 ayrı hedefler, kardeş Grup F).
- **Tip:** MUST · **Öncelik:** P3
- **Kabul kriteri:** Kodda plugin loader yok; karar tersine dönerse ADR gerekir.
- **ADR izi:** YOK — `[ÖNERİ — kapsam teyidi]`
- **İlgili test:** TC-PLR-211 (statik denetim)

#### NE-PLR-212 — Sürücü/eklenti kurulumu yok (taşıma)
- **Açıklama:** ASIO driver kullanıcı tarafında kurulu olur; uygulama sürücü KURMAZ (admin/yükleyici yok).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kurulum/first-run yalnız var olan cihazları listeler; eksik sürücüde yol gösterir `[ÖNERİ — indirme linki UI'da, otomatik indirme yok]`.
- **ADR izi:** kardeş PRD §04.2 (platform ayrımı) `[ÖNERİ]`
- **İlgili test:** TC-PLR-212

#### NE-PLR-213 — Sürüm/telemetri ağ çağrısı yok
- **Açıklama:** Otomatik güncelleme/sürüm kontrolü `⚠️ VR (ADR-0105 sürüm kontrolü belirtmiyor — önerü: kapalı varsayılan, elle kontrol)`; varsayılan çevrimdışı (NE-PLR-013).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Ağ trafiği profili: yalnız radyo/keşif; gizli çağrı yok (firewall testi).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-PLR-213

#### NE-PLR-214 — Log hijyeni (secret/URL sızıntısı)
- **Açıklama:** Log'larda sertifika/parola/token yok; stream URL'leri redaksiyon `[ÖNERİ — query string tamamen maskelenir]`.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Log örnekleri gözden geçirilir; CI'da secret-scan log dizinine de bakar.
- **ADR izi:** kardeş PRD NE-ENG-156 · AGENTS §5
- **İlgili test:** TC-PLR-214

#### NE-PLR-215 — Dış API envanteri (tek tablo)
- **Açıklama:** Dışa/ağa giden tüm API'ler: HTTP(S) tuner · HLS · Radio Browser · (ops.) web UI/remote/MCP — her satırda protokol + validator + lisans.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Tablo §14.5/§17.2 ile hizalı; yeni satır = tehdit tablosu güncellemesi (NE-PLR-166).
- **ADR izi:** ADR-106 kural 3/6 · kardeş PRD §17 (izlenebilirlik)
- **İlgili test:** TC-PLR-215 (envanter denetimi)

---

## §11 Test Planı (Player)

> **Kapı disiplini:** ADR-0105 kapıları 5.1 / 5.10 / 5.11 / 5.12 / 5.13 release blocker'dır;
> ADR-106 §5.1-§5.2 testleri (Ş1-Ş7) radyo fazının kabul kriteridir.
> **Sahne yasağı:** Koşulları sağlanmayan test SAHTE FAIL/SAHTE PASS üretmez — inverted guard (NE-PLR-052/087).

### §11.1 Test Piramidi (Player)

| Katman | Kapsam | Otomasyon | Sıklık |
|---|---|---|---|
| L1 Birim | URL validator, StreamTitle sanitizasyon, backoff hesabı, preset ayrıştırma, queue mantığı, durum makinesi geçiş fonksiyonları | %100 CI | Her commit |
| L2 Entegrasyon | C ABI zinciri (mock engine/null), radyo tuner (localhost mock sunucu), gapless vektörleri, EQ rampa, UI state | %100 CI | Her PR |
| Kapı & Ölçüm | 5.1 hash · 5.10 latency · 5.11 A/V · 5.12 gapless loopback · 5.13 fuzz · SNR/THD | Otomatik + donanımlı release | Her PR / release |
| Manuel & UX | UI akışları, erişilebilirlik, gerçek istasyon canlı testi, görsel onay | Checklist | Release adayı |

### §11.2 Kapılar — Player Koşulları

#### Kapı 5.1 — Bit-perfect (bitirmişlik koşulları)
- **Hat:** Kardeş PRD §11.2 (dosya → session → Null/DAC → WAV → SHA-256 byte-eşitlik).
- **Player ek koşulları:** EQ Flat/Bypass · limiter bypass · gain 0 dB · negotiated==dosya formatı (NE-PLR-036/040).
- **Inverted guard:** EQ aktif / lossy / radyo / shared mod → test KOŞUL DIŞI (SAHTE FAIL yok).
- **Matris:** 12 kombinasyon (kardeş §11.2 tablosu) + 2× determinism.
- **Release:** Gerçek DAC loopback doğrulaması manuel (NE-PLR-049).

#### Kapı 5.10 — Gecikme
- **Eşik:** <20 ms (bütçe §06.2); L1-L4 merdiveni `⚠️ VR` — eşik tanımlanana kadar en küçük desteklenen blok.
- **Düzenek:** Kardeş §11.3-A (loopback BLIP korelasyonu).
- **Kayıt:** blok boyutu + backend + cihaz + sürücü sürümü raporda.

#### Kapı 5.11 — A/V Sync
- **Eşik:** P95 ≤20 ms (2 saat video).
- **Düzenek:** Ses cihazı saat referansı; video frame damgası korelasyonu `[ÖNERİ — yöntem: ekran fotoğrafı/ışık sensörü ayrımı release'de teyit edilir]`.
- **Ek metrik:** Maksimum sapma (NE-PLR-090).
- **Koşul:** Yalnız video fazında anlamlı (ses-only fazda koşul dışı).

#### Kapı 5.12 — Gapless
- **Vektörler:** MP3 (Xing) çifti · FLAC (seektable) çifti · Opus (granule) · DSD/DoP (marker) · radio stream→stream.
- **Ölçüm:** Sınır korelasyonu ±5 ms (Null: örnek sayacı; release: loopback); >20 ms FAIL.
- **Ek:** Karışık format kuyruk (FLAC→MP3→FLAC), shuffle, repeat, seek-sonrası-geçiş (NE-PLR-062/063/072).

#### Kapı 5.13 — Fuzz
- **Hedefler:** Her decoder (WAV/FLAC/MP3/AAC/Opus/OGG/DSD) + ICY parser + HLS playlist + preset/config parser + C ABI girdileri.
- **Eşik:** Format başına ≥1000 · toplam ≥8000 corpus · 0 ASan/UBSan crash · 0 OOB · CI ≤10 dk.
- **Ek:** ADR-106 Ş7 — radyo decoder'ları ayrı hedeflerdir.

### §11.3 Radyo Test Altyapısı (ADR-106 §5.1-§5.2)

**Yerel mock istasyon düzeni** `[ÖNERİ — araç adları teklif]`:

| Bileşen | Görev |
|---|---|
| Mock ICY sunucu | icy-metaint varyantları (32 B / 4 KB / 1 MiB), StreamTitle spam (1000/sn), geçersiz UTF-8, 256+ bayt metin |
| Mock HLS sunucu | m3u8 varyantları (1000 entry / 1001 RED / derinlik 2 RED / 51 MB segment RED), chunk UUID taklidi |
| Ağ körletici | tc/netem veya uygulama-içi throttle: slow-start, 5 sn kesinti, 3 ardışık başarısızlık |
| DNS/validator sahtecisi | private IP yanıtı (127.0.0.1 / 169.254.169.254 / [::1]), 6 hop redirect zinciri |
| TLS sahtecisi | self-signed, süresi dolmuş, hostname uyuşmazlığı (mbedTLS RED yolları) |

**Yerel düzenek neden zorunlu:** Canlı internet istasyonları deterministik değildir (bant/başlık değişir) —
canlı test yalnız "manuel canlı doğrulama" satırında kalır; otomatik kapılar her zaman mock ile çalışır.

### §11.4 Ölçüm Düzeneği (Player Tarafı)

| Ölçüm | Eşik | Düzenek | Sahip |
|---|---|---|---|
| Gecikme | <20 ms (ASIO <10 ms) | Loopback BLIP (kardeş §11.3-A) | Kardeş PRD §11.3 |
| SNR | >105 dB | 1 kHz tone loopback FFT (VISION V-1) | Kardeş PRD §11.3-B |
| THD+N | <%0.005 @1 kHz | Aynı (VISION V-2) | Kardeş PRD §11.3-B |
| Gapless | ±5 ms korelasyon | Loopback + örnek sayacı | Ortak (§11.2 5.12) |
| A/V sync | P95 ≤20 ms | Frame damgası korelasyonu | **Player (bu PRD)** |
| RAM | ≤400 MB | RSS ölçümü (kütüphane + oynatma ± video) | **Player (bu PRD)** |
| VRAM | ~400-700 MB | GPU sayaçları (GTX 1660 bench — NE-PLR-081) | **Player (bu PRD)** |
| CPU | EQ ≤%5 @192k stereo | Bench (31-band worst-case) | Kardeş PRD (EQ payı) |

### §11.5 Manuel Checklist (Release Adayı)

1. **Canlı istasyon doğrulaması:** 10 gerçek istasyon (farklı codec: AAC/MP3/Opus/FLAC) — başlık akıyor, reconnect temiz (ADR-106 Ş6).
2. **Görsel onay:** EQ penceresi (31 slider), cihaz listesi, radyo tuner — `.ai/ui-design/` mockup ile karşılaştırma `[ÖNERİ — masaüstü mockup henüz yok; §18.7 açık madde]`.
3. **Erişilebilirlik:** Keyboard-only transport + EQ (NE-PLR-161).
4. **Türkçe metin:** Uzun/özel karakterli başlıklar (ğ ı ş İ ö ü ç) — mojibake yok.
5. **Kapat/aç kararlılığı:** Sağlam kapanış → son durum geri (NE-PLR-010/165).
6. **Exclusive kilit senaryosu:** Diğer uygulama sesi kilitlemiş → fallback mesajı net.
7. **Uzun süre:** ≥2 saat karışık kuyruk (dosya+radyo+EQ) — xrun/leak/performans raporu (NE-PLR-054/163).

### §11.6 Test Senaryo Defteri — TC-PLR (Gereksinim başına eşleşen senaryo)

> `TC-PLR-NNN` ↔ `NE-PLR-NNN`. Kısıtlı kalan senaryolar (şartlı U4/U5 vb.) "şartlı" etiketlidir ve
> karar verilmeden release koşusuna girmez.

#### TC-PLR-001 — Ortak çekirdek + ince adapter
- **Gereksinim:** NE-PLR-001 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Windows + Linux build'inde uygulama katmanı dosya listesini karşılaştır (diff raporu).
- **Beklenen:** Paylaşımlı dosyalar aynı; OS'a özgü yalnız adapter/ince satırlar; paylaşımsız kod artışı = red notu.
- **Kanıt:** ADR-019 §2.2 (15/5/5/5/5)

#### TC-PLR-002 — C ABI üzerinden engine kullanımı
- **Gereksinim:** NE-PLR-002 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Player kaynaklarında `neva::` iç tip erişimi grep'i (istisna: EqChain per-kanal erişim notu `[ÖNERİ]`).
- **Beklenen:** Yalnız `neva_session_*`; aksi FAIL.
- **Kanıt:** disk `neva_session.h` · kardeş NE-ENG-001

#### TC-PLR-003 — api_version handshake
- **Gereksinim:** NE-PLR-003 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Beklenen sürümü bilerek kaydır (test build) → player açılışı.
- **Beklenen:** ERROR durumu + sürüm mesajı; crash yok; doğru sürümde açılır.
- **Kanıt:** disk `neva_session.h:26`

#### TC-PLR-004 — Durum makinesi kontratı
- **Gereksinim:** NE-PLR-004 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** §04.5 tablosundaki her geçişi tetikle + 6 geçersiz geçişi dene.
- **Beklenen:** Geçerli: kabul + event; geçersiz: reddedilir (durum değişmez, hata event'i).
- **Kanıt:** ADR-019 §1.4 · ADR-004 · ADR-018

#### TC-PLR-005 — Event callback (E1)
- **Gereksinim:** NE-PLR-005 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** state/metadata/error event'lerini tetikle; thread id + sıra kaydı.
- **Beklenen:** Event'ler non-RT kuyruktan; UI thread marshal; ABI minor sürüm (eski fonksiyonlar sabit).
- **Kanıt:** ADR-106 kural 2 · kardeş §05.2 E1

#### TC-PLR-006 — position/duration + seek erişimi
- **Gereksinim:** NE-PLR-006 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Dosya oynat + 100× okuma; radyoda duration talebi.
- **Beklenen:** Bilinen aralık monoton; radyoda seek devre dışı/red; duration yok kod.
- **Kanıt:** kardeş §05.2 E3/E4

#### TC-PLR-007 — Cihaz listesi + seçim (E8)
- **Gereksinim:** NE-PLR-007 · **Katman:** L2 · **Otomasyon:** CI (mock) + manuel
- **Adımlar:** Listele → seç → tercih sakla → yeniden başlat → doğrula; hot-plug simülasyonu.
- **Beklenen:** Liste engine'den; seçim canlı; tercih kalıcı; hot-plug event gelir.
- **Kanıt:** ADR-019 F1 · disk `neva_session_set_output_device`

#### TC-PLR-008 — UI↔RT ayrımı
- **Gereksinim:** NE-PLR-008 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** UI kodunda ring/DSP iç yapısına doğrudan erişim grep'i; event akışı incelemesi.
- **Beklenen:** Doğrudan erişim 0; veri yalnız kuyruklarla akar.
- **Kanıt:** ADR-017 · kardeş §04.3

#### TC-PLR-009 — UI toolkit kararı kapısı
- **Gereksinim:** NE-PLR-009 · **Katman:** proses · **Otomasyon:** — (karar)
- **Adımlar:** M4 (UI fazı) başlangıcında ADR var mı kontrolü.
- **Beklenen:** ADR yoksa UI fazı başlatılmaz (§15 gate); seçenek listesi §18.1.
- **Kanıt:** YOK — açık madde §18.1

#### TC-PLR-010 — Config kalıcılığı
- **Gereksinim:** NE-PLR-010 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Ayarları değiştir → kapat/aç → doğrula; bozuk config yaz → aç.
- **Beklenen:** Son durum geri gelir; bozuk config → default + uyarı (crash yok; yedek `[ÖNERİ]`).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-011 — Engine sürüm birleşimi
- **Gereksinim:** NE-PLR-011 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Player + engine birlikte derleme + handshake smoke.
- **Beklenen:** Minor uyumlu; major uyuşmazlıkta net reddi (NE-PLR-003).
- **Kanıt:** kardeş §05.4

#### TC-PLR-012 — neva-eq / neva-matrix süreç ayrımı
- **Gereksinim:** NE-PLR-012 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** Ayrı executable'ları başlat/kapat; ana süreç etkileşimi.
- **Beklenen:** Ayrı süreç; C ABI; kapatma ana oturumu etkilemez.
- **Kanıt:** ADR-107 K2 · kardeş §04.7

#### TC-PLR-013 — Çevrimdışı öncelik
- **Gereksinim:** NE-PLR-013 · **Katman:** L2 · **Otomasyon:** CI (ağ kapalı)
- **Adımlar:** Ağ kapalı ortamda açılış + yerel dosya çalma + katalog arama.
- **Beklenen:** Tam işlevsel; ağ çağrısı denemesi bile yok (yerel dosya yolu); radyo sekmesi ağsız reddi net.
- **Kanıt:** §02.1 I-7 · AGENTS §17 Edge 9

#### TC-PLR-014 — Yerelleştirme kapsam notu
- **Gereksinim:** NE-PLR-014 · **Katman:** statik · **Otomasyon:** — (şartlı)
- **Adımlar:** §18.2 kararı verilmeden test yazılmaz.
- **Beklenen:** Kapsam etiketi `⚠️ VR` korunur.
- **Kanıt:** YOK

#### TC-PLR-015 — Kardeş PRD sınırı
- **Gereksinim:** NE-PLR-015 · **Katman:** statik · **Otomasyon:** CI (doküman denetimi)
- **Adımlar:** Bu PRD'de engine implementasyon emri veren ifadeler taraması (mutlak "şöyle implemente edilecek" ).
- **Beklenen:** Yalnız sözleşmeler (API/ölçüm); implementasyon talimatı yok — varsa review'da düzeltilir.
- **Kanıt:** §02.3

#### TC-PLR-016 — Decode matrisi
- **Gereksinim:** NE-PLR-016 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** §05.1 matrisindeki her formattan örnek dosya aç (WAV/FLAC/DSF/DFF/MP3/AAC/Opus/OGG + HLS variant).
- **Beklenen:** Hepsi açılır; bilinmeyen uzantı `UNSUPPORTED_FORMAT` (kod 3); 0 crash.
- **Kanıt:** ADR-0105 v1.0.2 §§3-4 ⚠️

#### TC-PLR-017 — Yerel demux önceliği
- **Gereksinim:** NE-PLR-017 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** FLAC açılışında FFmpeg modül yükleme logu/module yükü.
- **Beklenen:** FFmpeg yüklenmez `[ÖNERİ — log ile kanıt]`; MP3 yolunda yüklenir (kapsam dahili).
- **Kanıt:** ADR-106 §1.3 (çözüm M3)

#### TC-PLR-018 — soxr producer-side
- **Gereksinim:** NE-PLR-018 · **Katman:** statik/L2 · **Otomasyon:** CI
- **Adımlar:** RT callback kodunda resampler çağrısı grep'i + 44.1→48 akış profili.
- **Beklenen:** RT'de resampler 0; dönüşüm producer'da; master clock cihaz.
- **Kanıt:** ADR-106 kural 5 (ADR-0105 §2.5) ⚠️

#### TC-PLR-019 — soxr lisansı (LGPL-2.1)
- **Gereksinim:** NE-PLR-019 · **Katman:** lisans · **Otomasyon:** CI (envanter)
- **Adımlar:** Bağımlılık envanteri + link tipi (dinamik/statik) denetimi.
- **Beklenen:** Dinamik ✅; statik ise `⚠️ LİSANS` etiketi §14'te; kaynak lisans dosyası dağıtımda.
- **Kanıt:** ADR-0105 v1.2.0 ⚠️

#### TC-PLR-020 — FFmpeg LGPL build
- **Gereksinim:** NE-PLR-020 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** Build bayrakları dump (`--enable-gpl` grep'i) + LGPL dış codec (x264 vb.) yokluğu.
- **Beklenen:** GPL bayrağı 0; lisans raporu eki.
- **Kanıt:** ADR-0105 indeks ⚠️ · lib-matrix

#### TC-PLR-021 — Protocol whitelist
- **Gereksinim:** NE-PLR-021 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `file:///C:/` · `concat:` · `crypto:` · `subfile:` · `http(s)` vektörleri.
- **Beklenen:** http/https geçer; kalanları RED; FFmpeg'e ulaşmaz (katman kanıtı).
- **Kanıt:** ADR-106 kural 6

#### TC-PLR-022 — HTTP09Allowed=0
- **Gereksinim:** NE-PLR-022 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** curl yapılandırması doğrulama + HTTP/0.9 sahte sunucu denemesi.
- **Beklenen:** Bayrak 0; HTTP/0.9 yanıtı reddedilir.
- **Kanıt:** ADR-106 kural 6 · radio-and-hires §5.6

#### TC-PLR-023 — Redirect ≤5 + her hop validator
- **Gereksinim:** NE-PLR-023 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** 5 hop OK · 6 hop RED · 3. hopta private IP RED (IP kontrolü tekrarı).
- **Beklenen:** Sınır doğru; her hop tam validator'dan geçer.
- **Kanıt:** ADR-106 kural 6

#### TC-PLR-024 — Timeout 10 sn
- **Gereksinim:** NE-PLR-024 · **Katman:** L1/L2 · **Otomasyon:** CI (mock sunucu bekler)
- **Adımlar:** Cevap vermeyen sunucu → süre ölç.
- **Beklenen:** ≤10 sn `±tolerans [ÖNERİ — 1 sn]` → backoff zinciri (TC-PLR-105).
- **Kanıt:** ADR-106 kural 6 · §5.7

#### TC-PLR-025 — TLS doğrulama (mbedTLS)
- **Gereksinim:** NE-PLR-025 · **Katman:** L1/L2 · **Otomasyon:** CI (mock TLS)
- **Adımlar:** self-signed · süresi dolmuş · hostname uyuşmazlığı · geçerli sertifika.
- **Beklenen:** İlk üç RED; geçerli kabul; kör kabul yolu yok (bayrak denetimi).
- **Kanıt:** ADR-106 §5.4 (9.13.0)

#### TC-PLR-026 — decode → float32 ring
- **Gereksinim:** NE-PLR-026 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Her format için ring çıktısının formatını doğrula (interleaved float32); invariant assert.
- **Beklenen:** Çıkış formatı tutarlı; writer okuyucuyu geçmez; ASan temiz.
- **Kanıt:** ADR-0105 §2.5 ⚠️ · kardeş §07.4

#### TC-PLR-027 — 24-bit kayıpsız yol
- **Gereksinim:** NE-PLR-027 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** 24-bit kaynak → 32f ring → 24-in-32 çıkış → hash karşılaştırması (kayıpsız yolla).
- **Beklenen:** Byte eşitlik kaynak 24-bit gövdeyle uyumlu `⚠️ VR (detay indeks'te yok)`; erken quantization yok.
- **Kanıt:** ADR-106 §1.3 · ADR-0105 indeks ⚠️

#### TC-PLR-028 — Queue prefetch hazır olma
- **Gereksinim:** NE-PLR-028 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Sıradaki dosyanın decode'unu bilerek geciktir → geçişi izle.
- **Beklenen:** Normal koşulda geçişte starve 0; gecikme senaryosunda fade (§07.4) — ikisi de raporlanır.
- **Kanıt:** ADR-0105 AK-5 ⚠️ · kardeş §05.2 E2

#### TC-PLR-029 — Bozuk dosya dayanıklılığı
- **Gereksinim:** NE-PLR-029 · **Katman:** Kapı (5.13) · **Otomasyon:** CI
- **Adımlar:** Bozuk/parsiyel dosya corpus (WAV/FLAC/MP3) → oynatma denemesi.
- **Beklenen:** 0 crash; skip + UI uyarısı; kuyruk devam eder (tek dosya queue'yu durdurmaz).
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️

#### TC-PLR-030 — Tek transport (dosya ↔ radyo)
- **Gereksinim:** NE-PLR-030 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Dosya çalarken radyo başlat → decode oturumu sayısı; sonra geri.
- **Beklenen:** Aynı anda aktif decode 1 (+prefetch); mod değişimi eski oturumu temiz kapatır (leak 0).
- **Kanıt:** YOK — `[ÖNERİ]` (§04.3)

#### TC-PLR-031 — Metadata okuma
- **Gereksinim:** NE-PLR-031 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Tag'lı örnek dosyalar (ID3v2.3/v2.4 · VorbisComment · FLAC) + tagsiz dosya; Türkçe/emoji metin.
- **Beklenen:** Alanlar okunur; tagsiz → dosya adı fallback; encoding doğru (mojibake yok).
- **Kanıt:** YOK — `[ÖNERİ]` (parça okuma `⚠️ VR` ADR-0105)

#### TC-PLR-032 — Kapak görseli güvenliği
- **Gereksinim:** NE-PLR-032 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Gömülü kapak + 50 MB dev görsel + bozuk görsel.
- **Beklenen:** Normal gösterim; büyük → ölçek/RED (bellek cap `⚠️ VR`); bozuk → crash yok.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-033 — ReplayGain davranışı
- **Gereksinim:** NE-PLR-033 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** RG tag'li dosya: kapalı/açık iki koşul; bit-perfect hash (kapalıyken).
- **Beklenen:** Kapalı = 0 gain (hash geçer); açık = uygulanır + rozet koşulu güncellenir.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-034 — open_source URI ayrıştırma
- **Gereksinim:** NE-PLR-034 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Yol · `http://` · `https://` · `file://` · `C:\path` vektörleri.
- **Beklenen:** Dosya yolu lokal normalize; http(s) validator'a; `file://` RED (kural 6).
- **Kanıt:** kardeş §05.2 E9 ile hizalı

#### TC-PLR-035 — Decode CPU bench
- **Gereksinim:** NE-PLR-035 · **Katman:** bench · **Otomasyon:** CI (rapor)
- **Adımlar:** FLAC 192k/24 decode throughput; FFmpeg vs dr_libs karşılaştırma.
- **Beklenen:** Rapor üretilir; eşik `⚠️ VR` — ADR-0105 dosyası ile bağlanır.
- **Kanıt:** ADR-0105 indeks §5.6 ⚠️

#### TC-PLR-036 — Bit-perfect WASAPI Exclusive
- **Gereksinim:** NE-PLR-036 · **Katman:** Kapı (5.1) · **Otomasyon:** CI (Null) + manuel (DAC)
- **Adımlar:** §11.2 hattı; exclusive koşulu doğrula.
- **Beklenen:** Byte-eşit; şartlar sağlanmazsa koşul dışı (sahte sonucu yok).
- **Kanıt:** ADR-0105 kapı 5.1 ⚠️ · ADR-106 §1.3

#### TC-PLR-037 — Lossy kapsam ayrımı
- **Gereksinim:** NE-PLR-037 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Rozet/dil denetimi: "bit-perfect" ifadelerinin yalnız kayıpsız bağlamda kullanımı + test koşulunda lossy reddi.
- **Beklenen:** Lossy akışta rozet YOK; iddia metni ayrımı tutarlı.
- **Kanıt:** ADR-0105 v1.0.2 §6 ⚠️

#### TC-PLR-038 — Format negosyasyon sırası (N1)
- **Gereksinim:** NE-PLR-038 · **Katman:** L2 · **Otomasyon:** mock
- **Adımlar:** Mock cihaz: 32f reddet → 32i dene → 24-in-32; üçünü de reddet (fallback).
- **Beklenen:** Sıra 32f→32i→24-in-32; hepsi başarısızsa shared/fallback; log zinciri doğru.
- **Kanıt:** ADR-106 §1.3

#### TC-PLR-039 — BUFFER_SIZE_NOT_ALIGNED kurtarma
- **Gereksinim:** NE-PLR-039 · **Katman:** L2 · **Otomasyon:** mock
- **Adımlar:** Hata enjeksiyonu → periyot yeniden hesap → tekrar deneme.
- **Beklenen:** Yutulmaz; kurtarma başarılıysa devam, değilse zincir düşüşü.
- **Kanıt:** ADR-106 §1.3

#### TC-PLR-040 — EQ aktifken hash engeli (inverted guard)
- **Gereksinim:** NE-PLR-040 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** (1) Flat → hash PASS; (2) +3 dB band → hash testi KOŞUL DIŞI; (3) tekrar Flat → PASS.
- **Beklenen:** İkinci adımda SAHTE FAIL yok (koşul dışı); üçüncü adımda tekrar çalışır.
- **Kanıt:** ADR-106 kural 4/§5.1 Ş4

#### TC-PLR-041 — Hi-res 192k/32f uçtan uca
- **Gereksinim:** NE-PLR-041 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** 192k/32f kaynak + destekleyen mock cihaz → hash.
- **Beklenen:** Byte-eşit; N1 sırası; desteklemeyen cihazda negosyasyon düşüşü.
- **Kanıt:** ADR-107 K3 · ADR-0105 indeks ⚠️

#### TC-PLR-042 — ASIO yolu (şartlı U1)
- **Gereksinim:** NE-PLR-042 · **Katman:** L2 · **Otomasyon:** manuel (şartlı)
- **Adımlar:** ASIO driver varlığında oynatma + gecikme ölçümü; yokken zincir davranışı.
- **Beklenen:** Var: donanım pin formatı birebir + <10 ms (TC-ENG-089); yok: WASAPI'ye düşüş.
- **Kanıt:** ADR-019 §1.4 · kardeş NE-ENG-023

#### TC-PLR-043 — Bit-perfect rozet üç koşulu
- **Gereksinim:** NE-PLR-043 · **Katman:** L2 · **Otomasyon:** CI (UI state)
- **Adımlar:** (a) negotiated!=dosya, (b) EQ aktif, (c) shared mod → rozet durumu.
- **Beklenen:** Üçünde de rozet GİZLİ; üçü tamamsa görünür.
- **Kanıt:** kardeş §05.2 B3 `[ÖNERİ]`

#### TC-PLR-044 — DoP marker continuity
- **Gereksinim:** NE-PLR-044 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** DSF → DoP akışında marker deseni doğrulama (0x05/0xFA alternasyonu `[ÖNERİ — marker değerleri teyit]`).
- **Beklenen:** Marker bütünlüğü; native DSD `⚠️ VR` ayrı karar.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-PLR-045 — Shared mod rozet/uyarı
- **Gereksinim:** NE-PLR-045 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Fallback sonrası UI durumu + hash koşulu.
- **Beklenen:** Rozet YOK + bilgilendirme; hash testi koşul dışı (shared'da PASS yazılamaz).
- **Kanıt:** kardeş NE-ENG-022

#### TC-PLR-046 — Otomatik exclusive→shared geçiş
- **Gereksinim:** NE-PLR-046 · **Katman:** L2 · **Otomasyon:** mock + manuel
- **Adımlar:** `AUDCLNT_E_DEVICE_IN_USE` enjeksiyonu → geçiş → süre ölç + event.
- **Beklenen:** Süreklilik; event E8 ile UI bilgilendirmesi; kullanıcı kurtarma eşiği.
- **Kanıt:** ADR-106 §1.3 · ADR-019 F2

#### TC-PLR-047 — Hız uyuşmazlığı sırası (F3)
- **Gereksinim:** NE-PLR-047 · **Katman:** L2 · **Otomasyon:** CI (44.1k dosya / 48k mock cihaz)
- **Adımlar:** (1) setDeviceRate (2) soxr (3) fallback → sırayı logla; RT'de resampler denetimi.
- **Beklenen:** Sıra birebir; RT'de resampler 0; hash yalnız eşit formatta.
- **Kanıt:** ADR-019 §2 · kardeş NE-ENG-015

#### TC-PLR-048 — Hot-plug kaybı
- **Gereksinim:** NE-PLR-048 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** Çalma sırasında cihaz kaldır → zincir → Null/fallback → yeniden tak.
- **Beklenen:** Durum raporu (ERROR/bilgi); yeniden bağlanma; kullanıcı mesajı net; çökme yok.
- **Kanıt:** ADR-019 F1

#### TC-PLR-049 — CI hash yolu (Null) + loopback ayrımı
- **Gereksinim:** NE-PLR-049 · **Katman:** Kapı · **Otomasyon:** CI + release checklist
- **Adımlar:** CI'da Null hattı; release checklist'te loopback maddesi imzası.
- **Beklenen:** CI yeşil; loopback imzasız release yok.
- **Kanıt:** kardeş §11.2 · ADR-107 K3

#### TC-PLR-050 — 12 kombinasyon matrisi
- **Gereksinim:** NE-PLR-050 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** §11.2 tablosunun 12 satırı.
- **Beklenen:** 12/12; eksik kombinasyon = release bloke.
- **Kanıt:** kardeş K3/§11.2

#### TC-PLR-051 — OS efektleri baypas (exclusive)
- **Gereksinim:** NE-PLR-051 · **Katman:** L2 · **Otomasyon:** manuel (Windows)
- **Adımlar:** Exclusive akışta sistem ses seviyesi + "ses efekti" ayarlarını değiştir.
- **Beklenen:** Etki yok (session volume 1.0 sabit, efekt bayrakları kapalı); shared'da etki vardır + raporlanır.
- **Kanıt:** kardeş NE-ENG-037 · VISION V-5

#### TC-PLR-052 — Null-test guard (radyo/lossy engeli)
- **Gereksinim:** NE-PLR-052 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Radyo akışında hash koşulunu çalıştır → guard; sahte-PASS/FAIL denetimi (guard'ın kendisi ters test).
- **Beklenen:** Koşul dışı (sahte FAIL yok); guard'sız koşul = FAIL (negatif denetim).
- **Kanıt:** ADR-106 §5.1 Ş4

#### TC-PLR-053 — Exclusive tercihi (kullanıcı kilidi)
- **Gereksinim:** NE-PLR-053 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Tercihi aç → cihaz kaybı simülasyonu.
- **Beklenen:** Otomatik shared'a geçmez; uyarı gösterir (kullanıcı onayı bekler `[ÖNERİ]`).
- **Kanıt:** ADR-019 §1.1 `⚠️ VR`

#### TC-PLR-054 — 4 saat kararlılık
- **Gereksinim:** NE-PLR-054 · **Katman:** Kapı · **Otomasyon:** scheduled
- **Adımlar:** 4 sa exclusive continuous play; xrun/leak/hash kontrolü.
- **Beklenen:** xrun 0 `⚠️ VR (4 sa eşiği bu PRD önerisi)`; leak 0; hash değişmedi (determinizm).
- **Kanıt:** kardeş §11 stres

#### TC-PLR-055 — Dither davranışı
- **Gereksinim:** NE-PLR-055 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** 24f→16-bit çıkış (dither devrede mi? gürültü tabanı artışı beklenir); 16→16 (hash yolu).
- **Beklenen:** Dither yalnız dönüşümde; 16→16 hash byte-eşit (dither yok).
- **Kanıt:** YOK — `[ÖNERİ]` (N1 ile uyumlu)

#### TC-PLR-056 — MP3 Xing gapless
- **Gereksinim:** NE-PLR-056 · **Katman:** Kapı (5.12) · **Otomasyon:** CI
- **Adımlar:** Xing'li MP3 çifti → sınır örnek sayacı + korelasyon.
- **Beklenen:** 0 boşluk (padding kırpma); faz kayması yok.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-PLR-057 — FLAC gapless
- **Gereksinim:** NE-PLR-057 · **Katman:** Kapı (5.12) · **Otomasyon:** CI
- **Adımlar:** SEEKTABLE'lı FLAC çifti.
- **Beklenen:** Boşluksuz geçiş; hash doğrulamalı referans.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-PLR-058 — Opus gapless
- **Gereksinim:** NE-PLR-058 · **Katman:** Kapı (5.12) · **Otomasyon:** CI
- **Adımlar:** Opus çifti (granule hizalama).
- **Beklenen:** Boşluksuz geçiş.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-PLR-059 — DSD gapless (DoP)
- **Gereksinim:** NE-PLR-059 · **Katman:** Kapı (5.12) · **Otomasyon:** CI
- **Adımlar:** DSF→DSF geçiş; marker deseni.
- **Beklenen:** Marker bütünlüğü bozulmaz; geçiş temiz.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-PLR-060 — Sınır korelasyonu ±5 ms
- **Gereksinim:** NE-PLR-060 · **Katman:** Kapı (5.12) · **Otomasyon:** CI (Null) + release (loopback)
- **Adımlar:** Geçiş sınırında korelasyon hesabı (Null: örnek; release: loopback).
- **Beklenen:** ≤±5 ms; >20 ms = FAIL.
- **Kanıt:** ADR-0105 kapı 5.12 ⚠️

#### TC-PLR-061 — Prefetch penceresi (≥1 sn)
- **Gereksinim:** NE-PLR-061 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Geçiş anında ring doluluğunu ölç.
- **Beklenen:** ≥1 saniyelik PCM hazır (starve fade tetiklenmez); eksiklikte fade davranışı §07.4.
- **Kanıt:** kardeş §07.4 `[ÖNERİ]`

#### TC-PLR-062 — Karışık format kuyruk
- **Gereksinim:** NE-PLR-062 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** FLAC(44.1/24) → MP3(44.1/16) → FLAC(96/24) sırası + negosyasyon logu.
- **Beklenen:** Cihaz formatı korunur; gerekiyorsa soxr; geçiş fade ≤1 `[ÖNERİ]`; 0 starve.
- **Kanıt:** kardeş §06.5 · ADR-019 F3

#### TC-PLR-063 — Shuffle gapless
- **Gereksinim:** NE-PLR-063 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Shuffle aktif 50 geçiş; starve/boşluk sayacı.
- **Beklenen:** Her geçişte prefetch hazır; 0 starve.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-064 — Repeat + leak
- **Gereksinim:** NE-PLR-064 · **Katman:** L2 · **Otomasyon:** CI (1 saat)
- **Adımlar:** Tekrar döngüsü 1 saat; RSS artışı + sınır geçişleri.
- **Beklenen:** RSS artışı ≤0 `[ÖNERİ]`; geçişler gapless.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-065 — Son dosya → STOPPED
- **Gereksinim:** NE-PLR-065 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Kuyruk bitişi; son 50 ms dalga formu + durum eventi.
- **Beklenen:** Ramp'li bitiş → STOPPED event; ani kesme yok `[ÖNERİ]`.
- **Kanıt:** kardeş §07.4 `[ÖNERİ]`

#### TC-PLR-066 — Eksik tag fallback (uydurma yok)
- **Gereksinim:** NE-PLR-066 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Xing'siz MP3 + seektable'sız FLAC → geçiş davranışı.
- **Beklenen:** Kırpma YOK (normal başlangıç / boşluk kabul); uydurma kırpma = FAIL (bozulma üretir).
- **Kanıt:** ADR-0105 AK-5 ruhu ⚠️

#### TC-PLR-067 — Crossfade ayrımı
- **Gereksinim:** NE-PLR-067 · **Katman:** L2 · **Otomasyon:** CI (şartlı faz)
- **Adımlar:** Crossfade açıkken gapless iddiası + decode oturumu sayısı.
- **Beklenen:** İddia kapatılır (UI dil); 2 decode oturumu bütçe etkisi raporlanır.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-068 — Gapless vektör kütüphanesi
- **Gereksinim:** NE-PLR-068 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Repo vektörlerini CI'da çalıştır (çiftler + beklenen byte maskesi).
- **Beklenen:** Tüm vektörler yeşil; eksik vektör = kapı eksik (release bloke).
- **Kanıt:** ADR-0105 kapı 5.12 ⚠️

#### TC-PLR-069 — Konum sayaçları geçişi
- **Gereksinim:** NE-PLR-069 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Geçiş anında position okumaları (toplam vs parça).
- **Beklenen:** Parça sayacı sıfırlanır; toplam konum `[ÖNERİ]` tutarlı; UI ayrımı net.
- **Kanıt:** kardeş §05.2 E3 `[ÖNERİ]`

#### TC-PLR-070 — DSD→PCM geçişi
- **Gereksinim:** NE-PLR-070 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** DoP akışından 2.0 PCM'ye kuyruk geçişi; ilk periyot dalga formu.
- **Beklenen:** State reset (TC-ENG-078); pop transient'sız.
- **Kanıt:** kardeş NE-ENG-078

#### TC-PLR-071 — 5.1 kuyruk geçişi
- **Gereksinim:** NE-PLR-071 · **Katman:** L2 · **Otomasyon:** CI (şartlı M6)
- **Adımlar:** 5.1→5.1 ve 5.1→2.0 geçişleri (downmix kararı §18.3).
- **Beklenen:** Kanal sayacı uyumu; downmix matrisi kararı olmadan son adım `[ÖNERİ]`.
- **Kanıt:** VISION V-8

#### TC-PLR-072 — Seek sonrası gapless
- **Gereksinim:** NE-PLR-072 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Dosya ortasına seek → sınır geçişi (padding bilgileri korunuyor mu?).
- **Beklenen:** Geçiş hâlâ gapless; padding bilgisi seek'te kaybolmaz.
- **Kanıt:** kardeş §05.2 E4

#### TC-PLR-073 — Pause/sınır beklemesi
- **Gereksinim:** NE-PLR-073 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Dosyayı sınırın 1 sn öncesinde duraklat → 30 sn bekle → devam et.
- **Beklenen:** Beklemede decode duraklar (bellek artışı yok `[ÖNERİ]`); devam gapless korunur.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-074 — Sınır boşluğu tanımı (0 frame)
- **Gereksinim:** NE-PLR-074 · **Katman:** Kapı (5.12) · **Otomasyon:** CI
- **Adımlar:** Sınırda sessiz örnek sayısını ölç (geçiş rampası hariç).
- **Beklenen:** 0 frame boşluk; geçiş rampası ≤2 ms `[ÖNERİ]`; ölçüm raporu kapı dosyasında.
- **Kanıt:** ADR-0105 kapı 5.12 ⚠️

#### TC-PLR-075 — Radio stream→stream (reconnect dahil)
- **Gereksinim:** NE-PLR-075 · **Katman:** Kapı (5.12) · **Otomasyon:** CI (mock)
- **Adımlar:** Mock sunucu 5 sn kesinti → reconnect (prebuffer/fade) → stream→stream geçiş vektörü; loopback ±5 ms.
- **Beklenen:** Konum koruma · prebuffer 2-4 s · decoder reset · 20 ms fade-in; korelasyon ≤±5 ms.
- **Kanıt:** ADR-0105 AK-5 ⚠️ · ADR-106 §2.2a

#### TC-PLR-076 — libplacebo render (şartlı faz)
- **Gereksinim:** NE-PLR-076 · **Katman:** L2 · **Otomasyon:** şartlı (video fazı)
- **Adımlar:** 4K HDR örnek → render + metadata doğrulama (Vulkan/GL).
- **Beklenen:** HDR metadata doğru; ses engine'de (çift yol yok).
- **Kanıt:** ADR-0105 indeks v1.2.x ⚠️

#### TC-PLR-077 — Master clock = cihaz
- **Gereksinim:** NE-PLR-077 · **Katman:** L2 · **Otomasyon:** şartlı (video fazı)
- **Adımlar:** 2 saat video; drif ti izle; ses hıza ayarlı (video stretch `[ÖNERİ]`).
- **Beklenen:** Drift düzeltilir; video saatine göre ses kayması yok (ses referans).
- **Kanıt:** ADR-106 kural 11 · kapı 5.11 ⚠️

#### TC-PLR-078 — A/V sync P95 ≤20 ms (5.11)
- **Gereksinim:** NE-PLR-078 · **Katman:** Kapı (5.11) · **Otomasyon:** şartlı (ölçüm)
- **Adımlar:** §11.4 düzenek; 2 saat; P95 hesabı.
- **Beklenen:** P95 ≤20 ms; rapor arşivi.
- **Kanıt:** ADR-0105 kapı 5.11 ⚠️

#### TC-PLR-079 — VRAM bütçesi ölçümü
- **Gereksinim:** NE-PLR-079 · **Katman:** bench · **Otomasyon:** manuel (GTX 1660)
- **Adımlar:** Video açıkken GPU sayaçları (baseline 1511 MiB üstü).
- **Beklenen:** ~400-700 MB `⚠️ VR`; §7.5 açık maddesi kapanır (ölçüm raporu).
- **Kanıt:** ADR-0105 §2.8 v1.2.1-1.3.2 ⚠️

#### TC-PLR-080 — VRAM merdiveni (V0-V5)
- **Gereksinim:** NE-PLR-080 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** GPU basınç simülasyonu → kademe geçişleri.
- **Beklenen:** Her kademe davranışı tanımlı `⚠️ VR (kademe içerikleri indeks'te yok)`; ses etkilenmez.
- **Kanıt:** ADR-0105 §7.4 ⚠️

#### TC-PLR-081 — VRAM bench ortamı
- **Gereksinim:** NE-PLR-081 · **Katman:** bench · **Otomasyon:** manuel
- **Adımlar:** GTX 1660 · 6144 MB · 610.62 · Win10 19045 ortamında tekrarlı ölçüm; 1060 3 GB uyarı satırı.
- **Beklenen:** Repeatable sonuç + hedef-sınıf uyarısı (%19,5 hesabı) raporda.
- **Kanıt:** ADR-0105 §7.1-§7.3 v1.3.1-1.3.2 ⚠️

#### TC-PLR-082 — RAM ≤400 MB (video dahil bütçe)
- **Gereksinim:** NE-PLR-082 · **Katman:** Kapı · **Otomasyon:** CI + manuel
- **Adımlar:** Kütüphane + oynatma + video açık; RSS ölçümü; ihlal simülasyonu (bellek stresi).
- **Beklenen:** ≤400 MB; ihlalde tanımlı davranış/uyarı `[ÖNERİ]` .
- **Kanıt:** ADR-0105 §2.9 ⚠️

#### TC-PLR-083 — Gecikme bütçesi (blok sınırı)
- **Gereksinim:** NE-PLR-083 · **Katman:** Kapı (5.10) · **Otomasyon:** manuel
- **Adımlar:** Blok boyutu seçimlerini dene; ≥1024@48k denenmez (yasağı); ölçüm.
- **Beklenen:** <20 ms; L1-L4 `⚠️ VR` tanımlanana kadar en küçük desteklenen blok.
- **Kanıt:** ADR-0105 §2.10 ⚠️

#### TC-PLR-084 — Kapı 5.10 raporu
- **Gereksinim:** NE-PLR-084 · **Katman:** Kapı · **Otomasyon:** release checklist
- **Adımlar:** Ölçüm raporu arşivi; eşik aşımı senaryosu.
- **Beklenen:** Eşiği aşan build release adayı değil; rapor dosyası mevcut.
- **Kanıt:** ADR-0105 kapı 5.10 ⚠️

#### TC-PLR-085 — Kapı 5.13 (fuzz)
- **Gereksinim:** NE-PLR-085 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Format başına ≥1000 · toplam ≥8000 corpus · ASan/UBSan · ≤10 dk.
- **Beklenen:** 0 crash · 0 OOB; rapor release dosyasında.
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️ · ADR-106 Ş7

#### TC-PLR-086 — Kapı 5.12 (gapless vektörleri)
- **Gereksinim:** NE-PLR-086 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Grup D vektörlerinin tamamı + radio stream→stream + loopback.
- **Beklenen:** Hepsi yeşil; ölçüm raporu (±5 ms).
- **Kanıt:** ADR-0105 kapı 5.12 ⚠️

#### TC-PLR-087 — Kapı 5.1 + inverted guard (player)
- **Gereksinim:** NE-PLR-087 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** §11.2 hattı + guard vektörleri (EQ/radyo/lossy/shared → koşul dışı).
- **Beklenen:** 12/12 + guard doğru davranış + sahte-PASS/FAIL denetimi.
- **Kanıt:** ADR-0105 kapı 5.1 ⚠️ · ADR-106 §5.1

#### TC-PLR-088 — Video format kapsamı
- **Gereksinim:** NE-PLR-088 · **Katman:** L2 · **Otomasyon:** CI (örnek dosya)
- **Adımlar:** 1 MP4 + 1 MKV oynatma; altyazı `⚠️ VR`.
- **Beklenen:** İkisi de oynar; encode kapsam dışı (deneme reddedilir).
- **Kanıt:** ADR-0105 indeks ⚠️

#### TC-PLR-089 — Ses↔video mod geçişi
- **Gereksinim:** NE-PLR-089 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Dosya → video → dosya geçişleri; session logları.
- **Beklenen:** Backend oturumu korunur (negosyasyon tekrarı yok); süreklilik.
- **Kanıt:** kardeş NE-ENG-035

#### TC-PLR-090 — Pencere/video drift
- **Gereksinim:** NE-PLR-090 · **Katman:** Kapı (5.11) · **Otomasyon:** şartlı
- **Adımlar:** Uzun testte P95 + maksimum sapma raporu.
- **Beklenen:** P95 ≤20 ms + maks sapma belgelenir.
- **Kanıt:** ADR-0105 kapı 5.11 ⚠️

#### TC-PLR-091 — SNR >105 dB
- **Gereksinim:** NE-PLR-091 · **Katman:** Kapı · **Otomasyon:** manuel (release)
- **Adımlar:** Kardeş §11.3-B düzenek; player zinciri (decode→DSP bypass→exclusive).
- **Beklenen:** >105 dB; düzeneği tabanı notu.
- **Kanıt:** VISION.md · `⚠️ VR` (ADR-0105 teyidi yok)

#### TC-PLR-092 — THD+N <%0.005
- **Gereksinim:** NE-PLR-092 · **Katman:** Kapı · **Otomasyon:** manuel (release)
- **Adımlar:** Aynı düzenek @1 kHz.
- **Beklenen:** <%0.005; rapor.
- **Kanıt:** VISION.md · `⚠️ VR`

#### TC-PLR-093 — Uçtan uca float32
- **Gereksinim:** NE-PLR-093 · **Katman:** statik/L2 · **Otomasyon:** CI
- **Adımlar:** Zincirde integer kesme grep'i + hash karşılaştırması.
- **Beklenen:** Tek integer kesme yok; hash şart sağlanınca byte-eşit.
- **Kanıt:** VISION V-6 · kardeş §06

#### TC-PLR-094 — mpv yokluğu
- **Gereksinim:** NE-PLR-094 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** mpv/libmpv bağımlılığı grep + §14 RED satırı.
- **Beklenen:** 0 bağımlılık; RED satırı korunur.
- **Kanıt:** ADR-0105 v1.1.1 indeks

#### TC-PLR-095 — Video bench (şartlı)
- **Gereksinim:** NE-PLR-095 · **Katman:** bench · **Otomasyon:** şartlı
- **Adımlar:** 4K HDR decode+render CPU payı ölçümü.
- **Beklenen:** Rapor; eşik `⚠️ VR` (ADR-0105 dosyası).
- **Kanıt:** ADR-0105 indeks ⚠️

#### TC-PLR-096 — ICY v1+v2 ayrıştırma
- **Gereksinim:** NE-PLR-096 · **Katman:** L2 · **Otomasyon:** CI (mock)
- **Adımlar:** Mock v1 (icy-metaint + metin blokları) + v2 (XML metaveri) akışları.
- **Beklenen:** StreamTitle/StreamUrl event akıyor; parser engine'de (`icy_parser.h`), player thread işler.
- **Kanıt:** ADR-106 §2.2a kural 1 · S11

#### TC-PLR-097 — HLS oynatma
- **Gereksinim:** NE-PLR-097 · **Katman:** L2 · **Otomasyon:** CI (mock)
- **Adımlar:** Mock m3u8 + segmentler; `file://` RED denemesi.
- **Beklenen:** Akış oynar; whitelist RED (NE-PLR-021 zinciri).
- **Kanıt:** ADR-106 kural 1 · lib-matrix (FFmpeg 9.0.2)

#### TC-PLR-098 — DASH (şartlı faz 2)
- **Gereksinim:** NE-PLR-098 · **Katman:** şartlı · **Otomasyon:** şartlı
- **Adımlar:** Karar sonrası mock DASH akışı.
- **Beklenen:** Karar yoksa UI'da gizli; test koşul dışı.
- **Kanıt:** ADR-106 §2.2a kural 1

#### TC-PLR-099 — Codec matrisi
- **Gereksinim:** NE-PLR-099 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** AAC-LC · HE-AAC · Opus · MP3 · FLAC akış örnekleri (mock).
- **Beklenen:** Hepsi oynar; FDK bayrağı build'de yok.
- **Kanıt:** ADR-106 kural 9 §1.3

#### TC-PLR-100 — FDK-AAC yasağı
- **Gereksinim:** NE-PLR-100 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `fdk` bağımlılığı/bayrak grep.
- **Beklenen:** 0; teyit gelirse ayrı ADR.
- **Kanıt:** ADR-106 kural 9

#### TC-PLR-101 — StreamTitle sanitizasyon
- **Gereksinim:** NE-PLR-101 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `<script>` payload · 300 baytlık başlık · geçersiz UTF-8 · kontrol karakterleri (null byte) vektörleri.
- **Beklenen:** Escape + 256 bayt cap + UTF-8 doğrulama; düz metin render; 0 crash.
- **Kanıt:** ADR-106 kural 7 (S1)

#### TC-PLR-102 — StreamUrl tıklanamaz
- **Gereksinim:** NE-PLR-102 · **Katman:** L2 · **Otomasyon:** CI (UI state)
- **Adımlar:** `javascript:` / `http://` URL'li başlık → UI.
- **Beklenen:** Düz metin; anchor/onclick yok; kopyala seçeneği `[ÖNERİ]`.
- **Kanıt:** ADR-106 kural 7

#### TC-PLR-103 — Parser limitleri (5.13 Ş8)
- **Gereksinim:** NE-PLR-103 · **Katman:** L1/Kapı · **Otomasyon:** CI
- **Adımlar:** M3U8 1000/1001 entry · derinlik 1/2 · segment 50/51 MB · XML DTD · icy-metaint sınırları · text 1 MB.
- **Beklenen:** Sınır içi OK, dışı RED; fuzz corpus ile doğrulanır.
- **Kanıt:** ADR-106 kural 8 · §07.5 Ş8

#### TC-PLR-104 — URL Validator tek kapı
- **Gereksinim:** NE-PLR-104 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** §12.2 vektörlerinin tamamı (27 şart); "tuner validator'u atlıyor mu?" statik denetim.
- **Beklenen:** Hepsi RED/OK doğru; validator bypass 0; her redirect hop + post-connect IP doğrulaması.
- **Kanıt:** ADR-106 kural 6 · S1 §2.2a

#### TC-PLR-105 — Backoff (1s→30s ±%20)
- **Gereksinim:** NE-PLR-105 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** 10 deneme sürelerini kaydet (mock clock); jitter dağılımı.
- **Beklenen:** 1,2,4,8,16,30,30… cap; jitter ±%20 (sync-thundering); tablo raporda.
- **Kanıt:** ADR-106 §1.3 (v1.1 düzeltmesi)

#### TC-PLR-106 — 3 başarısız → ERROR
- **Gereksinim:** NE-PLR-106 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** 3 ardışık başarısızlık → otomatik deneme sayacı.
- **Beklenen:** Otomatik deneme durur; durum ERROR + mesaj; elle "yeniden dene" serbest.
- **Kanıt:** ADR-106 kural 8

#### TC-PLR-107 — Prebuffer 2-4 s
- **Gereksinim:** NE-PLR-107 · **Katman:** L2 · **Otomasyon:** CI (mock)
- **Adımlar:** Reconnect → LOADING → PLAYING süre ölçümü.
- **Beklenen:** İlk ses ≤4 s; starve fade yok (tampon yeterli).
- **Kanıt:** ADR-106 §2.2a

#### TC-PLR-108 — Fade-in 20 ms
- **Gereksinim:** NE-PLR-108 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Decoder reset sonrası ilk 100 ms dalga formu.
- **Beklenen:** 20 ms rampa; pop yok (spektral).
- **Kanıt:** ADR-106 §2.2a

#### TC-PLR-109 — STARVE/OVERFLOW davranışı
- **Gereksinim:** NE-PLR-109 · **Katman:** L2 · **Otomasyon:** CI (throttle)
- **Adımlar:** (1) Ağ yavaşlat → starve (fade ≤50 ms); (2) RT yavaşlat → overflow (drop).
- **Beklenen:** §04.2 şeması birebir; akış sürekliliği korunur (radyoda drop politikası).
- **Kanıt:** ADR-106 §2.2a · kardeş §07.4

#### TC-PLR-110 — TunerThread non-RT
- **Gereksinim:** NE-PLR-110 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** RT callback'te ağ çağrısı grep'i + thread adlandırma/profil.
- **Beklenen:** 0; ağ/parse yalnız non-RT.
- **Kanıt:** ADR-106 §2.2a · ADR-017

#### TC-PLR-111 — R128 sidechain non-RT
- **Gereksinim:** NE-PLR-111 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** LUFS hesap thread'i profil üzerinde RT etkisi.
- **Beklenen:** RT süresinde artış yok; canlı LUFS akıyor.
- **Kanıt:** ADR-106 §2.2a

#### TC-PLR-112 — Metadata zinciri (spam dayanıklılık)
- **Gereksinim:** NE-PLR-112 · **Katman:** L2 · **Otomasyon:** CI (mock spam)
- **Adımlar:** 1000 mesaj/sn spam başlık → kuyruk → UI.
- **Beklenen:** UI donmaz; kuyruk cap + drop sayacı `[ÖNERİ]`; sanitizasyon zinciri korunur.
- **Kanıt:** ADR-106 §2.2a · kardeş §05.2 E1

#### TC-PLR-113 — Radyo kataloğu (ADR-074)
- **Gereksinim:** NE-PLR-113 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Şema migrate + CRUD + arama.
- **Beklenen:** ADR-074 alanları; parametreli sorgu (TC-PLR-176 ile ortak); migrate temiz.
- **Kanıt:** ADR-074 · ADR-106 kural 3

#### TC-PLR-114 — Radio Browser keşfi (şartlı ağ)
- **Gereksinim:** NE-PLR-114 · **Katman:** L2 · **Otomasyon:** şartlı (ağ)
- **Adımlar:** https mirror + `url_resolved` doğrulama; ağ yok senaryosu.
- **Beklenen:** Ağ yoksa tamamen gizli; sonuçlar validator'dan geçmeden listelenmez.
- **Kanıt:** ADR-106 kural 3

#### TC-PLR-115 — İstasyon test aracı
- **Gereksinim:** NE-PLR-115 · **Katman:** L2 · **Otomasyon:** CI (mock)
- **Adımlar:** Geçerli/çalışmayan/yanlış-ICY üç URL testi.
- **Beklenen:** ICY header kontrolü + ilk N sn decode + codec raporu net.
- **Kanıt:** ADR-106 §2.2a (AIMP emsal)

#### TC-PLR-116 — Son istasyon hatırlama
- **Gereksinim:** NE-PLR-116 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** İstasyon seç → kapat/aç → kontrol.
- **Beklenen:** Son istasyon gelir (config kalıcılığı §06 depo).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-117 — Katalog UI (arama/favori)
- **Gereksinim:** NE-PLR-117 · **Katman:** L2 · **Otomasyon:** CI + UI test
- **Adımlar:** Arama (Türkçe karakter) + favori ekle/sil + render sanitizasyonu.
- **Beklenen:** Doğru filtre; XSS payload başlık düz metin (TC-PLR-101 zinciri).
- **Kanıt:** ADR-106 kural 3 · ADR-074

#### TC-PLR-118 — 100 istasyon stres (Ş3/Ş6)
- **Gereksinim:** NE-PLR-118 · **Katman:** Kapı · **Otomasyon:** CI (mock)
- **Adımlar:** 100 istasyonu 1× hızda oynat + backoff 1s/4s/8s ölçümleri.
- **Beklenen:** 0 çökme; metaveri akıyor; oynatılan/istenen raporda.
- **Kanıt:** ADR-106 §5.2 Ş3/Ş5/Ş6

#### TC-PLR-119 — 10 paralel tuning (Ş2)
- **Gereksinim:** NE-PLR-119 · **Katman:** Kapı · **Otomasyon:** CI (mock)
- **Adımlar:** Aynı anda 10 connect isteği; kuyruk/aktif sayacı.
- **Beklenen:** Gerçekçi 1-2 aktif akış; kalanlar kuyruk/bırakma politikası `⚠️ VR (önerü: kullanıcı sinyali bekleyenleri iptal, arka plan kuyruk ≤2)`.
- **Kanıt:** ADR-106 §5.2 Ş2

#### TC-PLR-120 — Fuzz: HLS + ICY corpus (Ş7)
- **Gereksinim:** NE-PLR-120 · **Katman:** Kapı (5.13) · **Otomasyon:** CI
- **Adımlar:** Her decoder + ICY + HLS playlist corpus.
- **Beklenen:** 0 crash; kapı 5.13'e entegre.
- **Kanıt:** ADR-106 §5.2 Ş7

#### TC-PLR-121 — Bant sayısı seçimi (E5)
- **Gereksinim:** NE-PLR-121 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** 10/15/20/31 geçişleri (çalma sırasında) + mask sayacı.
- **Beklenen:** Geçiş anında aktif; tahsis 0; varsayılan `⚠️ VR` (önerü 31).
- **Kanıt:** ADR-106 §2.2b kural 2

#### TC-PLR-122 — 31-band frekans doğruluğu
- **Gereksinim:** NE-PLR-122 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** UI etiketleri ↔ `eq_grids` ↔ EqChain indeksleri üçlü karşılaştırma.
- **Beklenen:** Birebir; `eq_grids_tests` yeşil.
- **Kanıt:** ADR-106 §2.2b · disk `eq_grids.h`

#### TC-PLR-123 — 10-band ISO + VLC ayrımı
- **Gereksinim:** NE-PLR-123 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** 10-band etiketleri (31.5…16k) + VLC 62.5 preset'i okuma.
- **Beklenen:** ISO listesi; VLC seti kendi frekanslarıyla okunur (zorla map yok — hata verir/atlama).
- **Kanıt:** ADR-106 §2.2b kural 3

#### TC-PLR-124 — 20-band (teyitli aralık)
- **Gereksinim:** NE-PLR-124 · **Katman:** L1 · **Otomasyon:** CI (şartlı)
- **Adımlar:** `eq_grids` 20-band tanımı var mı + teyit notu.
- **Beklenen:** Teyit yoksa `⚠️ VR` korunur; tanımsızsa UI gizli `[ÖNERİ]`.
- **Kanıt:** ADR-106 §2.2b kural 4 · §1.4

#### TC-PLR-125 — 15-band (teyitli)
- **Gereksinim:** NE-PLR-125 · **Katman:** L1 · **Otomasyon:** CI (şartlı)
- **Adımlar:** Aynı (TC-PLR-124 deseni).
- **Beklenen:** Teyide kadar kilitli/gizli `[ÖNERİ]`.
- **Kanıt:** ADR-106 §2.2b kural 5

#### TC-PLR-126 — Biquad bütçesi sabitliği (E5)
- **Gereksinim:** NE-PLR-126 · **Katman:** L2 · **Otomasyon:** CI (tahsis sayacı)
- **Adımlar:** 31→10→31 geçişleri sırasında tahsis + 31-band benchmark.
- **Beklenen:** Geçişte tahsis 0; benchmark worst-case (31) raporlanır.
- **Kanıt:** ADR-106 kural 4/6 · kardeş §06.2

#### TC-PLR-127 — Ramp 5-20 ms (pop analizi)
- **Gereksinim:** NE-PLR-127 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Slider spam simülasyonu (100 değişim/sn) → dalga formu + rampa süresi.
- **Beklenen:** Pop yok; süre 5/10/20 ms seçeneklerine uyar `[ÖNERİ]`.
- **Kanıt:** ADR-105 §2.3 ⚠️ (ADR-106 kural 5)

#### TC-PLR-128 — Pasif band bypass (CPU)
- **Gereksinim:** NE-PLR-128 · **Katman:** L2 · **Otomasyon:** CI bench
- **Adımlar:** 10-band vs 31-band CPU profili.
- **Beklenen:** 10-band'te masraf düşer; tahsis kalır (bypass maskesi).
- **Kanıt:** ADR-106 kural 4

#### TC-PLR-129 — Preset taşıma biçimi
- **Gereksinim:** NE-PLR-129 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Preset JSON üret/yükle; farklı band_count/grid_version varyantları.
- **Beklenen:** Taşınabilir; uyuşmazlıkta düzeltme + uyarı; bilinmeyen anahtar ignore (TC-PLR-175 ile).
- **Kanıt:** ADR-106 kural 6

#### TC-PLR-130 — Preset CRUD
- **Gereksinim:** NE-PLR-130 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Kaydet/yükle/sil/adlandır + fabrika koruması.
- **Beklenen:** CRUD; fabrika preset'leri silinemez `[ÖNERİ]`; çakışma adı uyarısı `[ÖNERİ]`.
- **Kanıt:** ADR-106 kural 6 · NE-PLR-010

#### TC-PLR-131 — Fabrika preset seti
- **Gereksinim:** NE-PLR-131 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Her preset vektörünü yükle → toplam yanıt kontrolü; Flat = all-zero.
- **Beklenen:** Flat 0 dB tümü (hash yolunu açar); diğerleri §08.3 taslak `⚠️ VR (set önerü)`.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-132 — Flat vs Bypass ayrımı
- **Gereksinim:** NE-PLR-132 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** (a) Flat 0 dB, (b) Bypass (maske pasif) → hash ikisinde de.
- **Beklenen:** İkisi de byte-eşit; UI'da ayrı düğme/durum; rozet ikinde de görünür.
- **Kanıt:** kardeş §06.1 D3 · §05.2 B3

#### TC-PLR-133 — Preamp + clip göstergesi
- **Gereksinim:** NE-PLR-133 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** +12 dB preamp → limiter davranışı + UI clip göstergesi `[ÖNERİ]`.
- **Beklenen:** True-peak ceiling aşılmaz; uyarı eşiği `⚠️ VR (önerü +6 dB)`; bit-perfect yolda 0 dB = identity.
- **Kanıt:** kardeş §06.4

#### TC-PLR-134 — Frekans etiketi salt-okunur
- **Gereksinim:** NE-PLR-134 · **Katman:** statik/L2 · **Otomasyon:** CI (UI state)
- **Adımlar:** Frekans alanına düzenleme denemesi.
- **Beklenen:** Salt-okunur (etiket); parametrik mod ayrı.
- **Kanıt:** ADR-106 kural 7 · ADR-0105 §2.1

#### TC-PLR-135 — Parametrik mod ayrımı (şartlı faz)
- **Gereksinim:** NE-PLR-135 · **Katman:** L2 · **Otomasyon:** şartlı (M5+)
- **Adımlar:** Grafik ↔ parametrik geçiş; state ayrımı + ramp.
- **Beklenen:** İki ayrı state/kasa `[ÖNERİ]`; geçiş ramp'li.
- **Kanıt:** ADR-106 §2.2b kural 7 · ADR-0105 §2.1

#### TC-PLR-136 — Radyoda EQ aktif
- **Gereksinim:** NE-PLR-136 · **Katman:** L2 · **Otomasyon:** CI (mock radyo)
- **Adımlar:** Radyo akışında preset uygula → toplam yanıt + rozet.
- **Beklenen:** EQ devrede (kural 4); rozet YOK (lossy).
- **Kanıt:** ADR-106 kural 4

#### TC-PLR-137 — EQ widget performansı + keyboard
- **Gereksinim:** NE-PLR-137 · **Katman:** L2 · **Otomasyon:** UI test
- **Adımlar:** 31 slider render FPS + keyboard-only erişim (odak/ok tuşları).
- **Beklenen:** Oynatma sırasında akıcı `[ÖNERİ — 60 fps]`; keyboard-only tam erişim.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-138 — Yanıt eğrisi çizimi (şartlı)
- **Gereksinim:** NE-PLR-138 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Bilinen preset eğrisi vs analitik toplam.
- **Beklenen:** Tutarlılık toleransı `⚠️ VR`; yenileme ≤10 Hz non-RT.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-139 — neva-eq entegrasyonu (şartlı)
- **Gereksinim:** NE-PLR-139 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** Ayrı pencere açıkken ana UI senkronu.
- **Beklenen:** E5/E6 çağrıları yansır; kapatma ana oturumu etkilemez.
- **Kanıt:** ADR-107 K2 · kardeş §04.7

#### TC-PLR-140 — EQ vektör testleri (Ş5)
- **Gereksinim:** NE-PLR-140 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** 10/15/20/31 mod geçişleri + ramp ölçümleri (dosya + radyo iki ortamda).
- **Beklenen:** 0 çökme; rampa ölçüm raporu.
- **Kanıt:** ADR-106 §5.2 Ş5

#### TC-PLR-141 — LUFS hedefleme (şartlı)
- **Gereksinim:** NE-PLR-141 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Açık/kapalı iki koşul; kapalıyken hash.
- **Beklenen:** Kapalı = 0 etki (hash geçer); açık = hedef gain + limiter ön koşulu `⚠️ VR (hedef -14 LUFS önerü)`.
- **Kanıt:** kardeş §06.4 `[ÖNERİ]`

#### TC-PLR-142 — Crossfade↔EQ state geçişi (şartlı)
- **Gereksinim:** NE-PLR-142 · **Katman:** L2 · **Otomasyon:** şartlı (crossfade fazı)
- **Adımlar:** Crossfade açıkken preset değişimi.
- **Beklenen:** EQ korunur + ramp; iki oturum aynı preset.
- **Kanıt:** kardeş NE-PLR-067

#### TC-PLR-143 — Cihaza bağlı EQ hatırlama (şartlı)
- **Gereksinim:** NE-PLR-143 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** İki cihaz + iki preset; geçişte teyit akışı.
- **Beklenen:** Tercihler ayrışır; teyit zorunluluğu `[ÖNERİ]`.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-144 — Preset import/export (+dönüştürücü şartlı)
- **Gereksinim:** NE-PLR-144 · **Katman:** L2 · **Otomasyon:** CI (şartlı dönüştürücü)
- **Adımlar:** Export → import round-trip; VLC/AIMP dönüştürücü `⚠️ VR`.
- **Beklenen:** Round-trip byte-eşit (kendi formatı); dönüştürücü yalnız kendi frekans setiyle.
- **Kanıt:** ADR-106 §1.4 · kardeş §08.2

#### TC-PLR-145 — Flat preset hash yolu (§11.2 ön koşulu)
- **Gereksinim:** NE-PLR-145 · **Katman:** Kapı (5.1) · **Otomasyon:** CI
- **Adımlar:** Flat/Bypass + limiter bypass + gain 0 + eşit format → 12 kombinasyon hash.
- **Beklenen:** 12/12 byte-eşit; Flat=all-zero kanıtı.
- **Kanıt:** kardeş §11.2 · ADR-106 kural 4

#### TC-PLR-146 — Preset yedeği/alma (atomik write)
- **Gereksinim:** NE-PLR-146 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** (1) Yazma sırasında kapat (yarı yazılmış dosya); (2) yeniden açılışta geri alma.
- **Beklenen:** Son tutarlı preset geri gelir; bozuk dosya → yedekten/fabrika + uyarı (crash yok).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-147 — 10/15/20/31 grid üretim doğruluğu
- **Gereksinim:** NE-PLR-147 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Dört grid için frekans listesi üret → teyitli kaynakla (ISO 266/126 + ADR-106) karşılaştır.
- **Beklenen:** 31 tam ISO; 10 ISO alt küme; 15/20 teyitli listeler `⚠️ VR` tanımlanana kadar kilitli/gizli.
- **Kanıt:** ADR-106 §2.2b kural 3-5 · kardeş §06.1

#### TC-PLR-148 — Grid ↔ EqChain band maskesi hizası
- **Gereksinim:** NE-PLR-148 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Her gridde mask boyutu + aktif band dizini doğrulaması (31→10 geçişinde 10. band geride kalıyor mu?).
- **Beklenen:** Mask boyutu = band sayısı; fazlalık/banklar pasif (taşıma sıfır — TC-ENG-051/066 ile ortak).
- **Kanıt:** ADR-106 kural 4/6 · kardeş EqChain

#### TC-PLR-149 — Durum makinesi kontratı (otomatik)
- **Gereksinim:** NE-PLR-149 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** §04.5 + §10 durum makinesi geçiş matrisi (geçerli/geçersiz tüm çiftler).
- **Beklenen:** Geçerli geçişler kabul + doğru event; geçersizler reddedilir (durum değişmez). ADR-004/ADR-018/ADR-019 §1.4 uyumu.
- **Kanıt:** ADR-019 §1.4 · kardeş §05.2 E1

#### TC-PLR-150 — Konum/uzunluk eventleri (E3/E4/E7)
- **Gereksinim:** NE-PLR-150 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Oynat/seek/duraklat + radyo modunda aynı akışlar; event frekansı ve değerleri.
- **Beklenen:** Event akıyor; radyoda position akar, duration gelmez (0/yok); seek radyoda reddedilir.
- **Kanıt:** kardeş §05.2 E3/E4/E7 · ADR-019 §1.4

#### TC-PLR-151 — ERROR durumunda tekrar-dene/geri-ok
- **Gereksinim:** NE-PLR-151 · **Katman:** L2/UX · **Otomasyon:** UI test + manuel
- **Adımlar:** İstasyon hatalı → ERROR ekranı → yeniden dene → URL girişi → geri bağlantı.
- **Beklenen:** İki buton/senaryo çalışır; geri otomatik değil; otomatik deneme 3'te durmuş (TC-PLR-106 ile hizalı).
- **Kanıt:** ADR-106 kural 8 · kardeş §12 hata UX (bu PRD §13)

#### TC-PLR-152 — Geçersiz girdi hata durumu (form)
- **Gereksinim:** NE-PLR-152 · **Katman:** L2/UX · **Otomasyon:** UI test
- **Adımlar:** Boş URL · yanlış şema · 10000 karakter girdi → form gönderimi.
- **Beklenen:** Alan bazında hata metni; kırmızı durum (WCAG); gönderim engellenir/kabul edilmez; UI donmaz.
- **Kanıt:** kardeş NE-ENG-157 · AGENTS §5 UI/UX

#### TC-PLR-153 — Bozuk dosya → uyarı + atla
- **Gereksinim:** NE-PLR-153 · **Katman:** L2/UX · **Otomasyon:** CI (UI state) + manuel
- **Adımlar:** Kuyrukta bozuk dosya (dosya1) → oynatma.
- **Beklenen:** Uyarı (dosya adı ile) + otomatik/atla; kuyruk sonraki dosyaya geçer; uygulama çalışır.
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️

#### TC-PLR-154 — Desteklenmeyen format mesajı (3)
- **Gereksinim:** NE-PLR-154 · **Katman:** L2/UX · **Otomasyon:** CI
- **Adımlar:** Kapsam dışı format (ör. WMA) açma denemesi.
- **Beklenen:** Kod 3 → "desteklenmeyen format" mesajı; engine açık başarısız değil (player uyumu); sonraki dosya devam.
- **Kanıt:** kardeş NE-ENG-011/§12.3 · ADR-0105 indeks ⚠️

#### TC-PLR-155 — Engine bağlantısı koptu (ERROR-3 köprüsü)
- **Gereksinim:** NE-PLR-155 · **Katman:** L2/UX · **Otomasyon:** manuel
- **Adımlar:** neva-host/engine köprüsünü zorla kapat → UI durumu → yeniden bağlanma.
- **Beklenen:** Tek ERROR-3 destekleyici satır (NE-ENG-107); kuyruk durumu korunur; kurtarma akışı net.
- **Kanıt:** kardeş NE-ENG-107 · ADR-107 §8.6 (U4)

#### TC-PLR-156 — Dahili hata: log + geri kurtarma
- **Gereksinim:** NE-PLR-156 · **Katman:** L2 · **Otomasyon:** CI (mock)
- **Adımlar:** `NEVA_ERR_INTERNAL` (4) enjeksiyonu.
- **Beklenen:** Kullanıcıya jargon yok ("bir şeyler ters gitti" + deneyin); log'a detay; oturum yeniden kurulur; crash yok.
- **Kanıt:** kardeş §12.3 · NE-ENG-157

#### TC-PLR-157 — İlk-iş akışı + ön tampon (LOADING)
- **Gereksinim:** NE-PLR-157 · **Katman:** L2/UX · **Otomasyon:** CI + UI test
- **Adımlar:** İlk istasyon denemesi → LOADING → PREBUFFER → PLAYING zaman çizelgesi; çabuk dene/iptal.
- **Beklenen:** ≤4 s ilk ses (TC-PLR-107); iptal butonu `⚠️ VR (ön tarayıcı notu)`; iki akış da net.
- **Kanıt:** ADR-106 kural 6 · kardeş §05.2 E9/E10

#### TC-PLR-158 — 3 başarısız → teklik mesajı
- **Gereksinim:** NE-PLR-158 · **Katman:** L2/UX · **Otomasyon:** CI
- **Adımlar:** 3 başarısız istasyon → durum mesajı.
- **Beklenen:** Mesaj tek ve net ("3 denemeden sonra durdu — yeniden deneyin"); spam yok (otomatik döngü durdu).
- **Kanıt:** ADR-106 kural 8 · kardeş §12.6 (kullanıcı dostu)

#### TC-PLR-159 — Katalog dahili hata (bozuk şema)
- **Gereksinim:** NE-PLR-159 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Bozuk/corrupt DB → açılış davranışı.
- **Beklenen:** Düzeltme + yedek/geri alma `[ÖNERİ]`; katalog yeniden indekslenir; uygulama açılır.
- **Kanıt:** kardeş §12.5 FM-06 `[ÖNERİ]` · ADR-074

#### TC-PLR-160 — Fallback sembolü (UI görseli)
- **Gereksinim:** NE-PLR-160 · **Katman:** UX · **Otomasyon:** UI test
- **Adımlar:** Cihaz bulunamadı/null-a-düştü durumu → görsel.
- **Beklenen:** Belirsiz/varsayılan ikon (metin yerine) — uygulama "cihaz yok" bilgisini görsel sembolle verir `[ÖNERİ — ikon tasarımı açık]`.
- **Kanıt:** kardeş §05.2 `[ÖNERİ]`

#### TC-PLR-161 — Klavye erişimi (transport + EQ)
- **Gereksinim:** NE-PLR-161 · **Katman:** UX · **Otomasyon:** UI test (a11y)
- **Adımlar:** Sadece klavye ile: oynat/duraklat/sonraki → radyo geçişi → 31 slider ok tuşları → preset kaydet.
- **Beklenen:** Tüm işlevler erişilebilir; odak görünür (≥3px `[ÖNERİ]`); odak tuzağı yok; §02.3 I-11 karşılığı.
- **Kanıt:** AGENTS §17 Edge 8 (EREG) · YOK — `[ÖNERİ]` (masaüstü checklist)

#### TC-PLR-162 — Kurgu dışı açılış (cold start)
- **Gereksinim:** NE-PLR-162 · **Katman:** L2 · **Otomasyon:** CI (sıcaklık etiketli)
- **Adımlar:** Sistem açılışı sonrası ilk başlatma (mümkünse taze oturum) → süre ölçümü.
- **Beklenen:** Açılış ≤500 ms `⚠️ VR` (bu PRD aralığı; kardeş 300 ms engine çıkışı) — ölçüm raporda sıcak/soğuk ayrımı.
- **Kanıt:** YOK — `[ÖNERİ]` · kardeş NE-ENG-098

#### TC-PLR-163 — 24 saat kararlılık + 1000 cycle
- **Gereksinim:** NE-PLR-163 · **Katman:** Kapı · **Otomasyon:** scheduled (gece)
- **Adımlar:** 24 sa karışık oynatma (dosya+radyo+EQ+video yok) + 1000 play/stop cycle; RSS/xrun/UI canlılık.
- **Beklenen:** RSS artışı ≤0 `[ÖNERİ]`; xrun 0; UI yanıt verir (crash/donma 0).
- **Kanıt:** kardeş §11 stres · S3 §6.1

#### TC-PLR-164 — Sağlam kapanış → son durum geri
- **Gereksinim:** NE-PLR-164 · **Katman:** L2 · **Otomasyon:** CI + manuel
- **Adımlar:** (a) Sağlam kapat (pozisyon + mod + kuyruk); (b) SIGKILL benzeri ani kapatma.
- **Beklenen:** (a) Son durum geri (dosya/pozisyon/mod/EQ/cihaz); (b) veri tutarlı (sondan önceki güvenli `[ÖNERİ]`), crash artifact yok.
- **Kanıt:** kardeş §06 depo · ADR-074

#### TC-PLR-165 — UI↔engine state kayması (tekrar senkron)
- **Gereksinim:** NE-PLR-165 · **Katman:** L2 · **Otomasyon:** CI (invariant)
- **Adımlar:** 1000 rastgele komut (play/pause/seek/EQ/device) sonrası invariant: UI durumu = engine durumu.
- **Beklenen:** 0 kayma; invariant ihlali = FAIL (yanlış gösterim asla kabul değil).
- **Kanıt:** ADR-019 §1.4 ruhu

#### TC-PLR-166 — OWASP tehdit tablosu gözden geçirme
- **Gereksinim:** NE-PLR-166 · **Katman:** proses · **Otomasyon:** — (release)
- **Adımlar:** §12.1 tablosu release checklist'te; yeni yüzey (yeni format/protokol) = tablo güncellemesi zorunlu.
- **Beklenen:** Tablo güncel + imzalı; eksik = release yok.
- **Kanıt:** ADR-106 §5 · AGENTS §5

#### TC-PLR-167 — SSRF özel-IP reddi
- **Gereksinim:** NE-PLR-167 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `http://127.0.0.1` · `http://10.0.0.1` · `http://172.16.0.1` · `http://192.168.1.1` · `http://169.254.169.254/latest/meta-data/` · `http://[::1]` · `http://[fd00::1]` + DNS çözümü → private IP (rebinding) vektörleri.
- **Beklenen:** Hepsi RED; DNS çözümü sonra tekrar kontrol (bağlanmadan önce) — post-connect IP kontrolü dahil.
- **Kanıt:** ADR-106 kural 6 (S3)

#### TC-PLR-168 — Zincirleme redirect ≤5 (güvenlik)
- **Gereksinim:** NE-PLR-168 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** 6 hop'lu zincir (3'ünde private IP hedefi).
- **Beklenen:** 6. hop RED; 3. hoptaki private IP RED (her hop validator); zincir sayısı sayacı sıfırlanmaz.
- **Kanıt:** ADR-106 kural 6

#### TC-PLR-169 — TLS sertifika reddi yolları
- **Gereksinim:** NE-PLR-169 · **Katman:** L1/L2 · **Otomasyon:** CI (mock TLS)
- **Adımlar:** Self-signed · expired · hostname mismatch · geçersiz zincir.
- **Beklenen:** Dördü de RED; kör kabul bayrağı yok (code grep); http yalnız onay akışıyla.
- **Kanıt:** ADR-106 §5.4 (mbedTLS 9.13.0)

#### TC-PLR-170 — HTML-escape (tüm render yolları)
- **Gereksinim:** NE-PLR-170 · **Katman:** L1 · **Otomasyon:** CI + statik
- **Adımlar:** `<img src=x onerror=alert(1)>` · `<script>` · `javascript:` payload'ları StreamTitle/StreamUrl/katalog alanında; ayrıca escape edilmemiş render yolu grep'i.
- **Beklenen:** Düz metin görünür; ham render yolu 0 (statik).
- **Kanıt:** ADR-106 kural 7 (S1)

#### TC-PLR-171 — 256 bayt cap + UTF-8 doğrulama
- **Gereksinim:** NE-PLR-171 · **Katman:** L1 · **Otomasyon:** CI + fuzz
- **Adımlar:** 300 baytlık başlık · tek byte'lık geçersiz UTF-8 dizisi · 1 MB text blok sınırı.
- **Beklenen:** ≤256 kırpma/temizleme; geçersiz UTF-8 → replacement (çökme yok); 1 MB cap RED.
- **Kanıt:** ADR-106 kural 7/8

#### TC-PLR-172 — XXE reddi (ICY v2 XML)
- **Gereksinim:** NE-PLR-172 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `<!DOCTYPE x [<!ENTITY xxe SYSTEM "file:///C:/Windows/win.ini">]>` içeren metaveri; ayrıca `file://`/`ftp:` external entity.
- **Beklenen:** DTD çözümleme yok; dosya erişimi tetiklenmez; entity ham metin kalır/reddedilir.
- **Kanıt:** ADR-106 kural 8 (Ş8)

#### TC-PLR-173 — HLS kapıları (50 MB / 1000 entry / derinlik 1)
- **Gereksinim:** NE-PLR-173 · **Katman:** L1/Kapı · **Otomasyon:** CI + fuzz
- **Adımlar:** 1001 entry'li playlist · 51 MB segment · iç içe playlist vektörleri.
- **Beklenen:** Üçü de RED; sınır değerler dahil (50 MB OK / 51 MB RED; 1000 OK / 1001 RED; derinlik 1 OK / 2 RED).
- **Kanıt:** ADR-106 kural 8 (Ş8) · §07.5 Ş8

#### TC-PLR-174 — Protocol whitelist bypass denemeleri
- **Gereksinim:** NE-PLR-174 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `concat:http://a|file:///c:/x` · `file:/etc/passwd` · `subfile,,0,,` · `crypto+http://` · `%66ile://` (URL-encoded) vektörleri.
- **Beklenen:** Hepsi RED; parser bypass yok; http/https dışında 0 geçiş.
- **Kanıt:** ADR-106 kural 6

#### TC-PLR-175 — Preset/config parser dayanıklılığı
- **Gereksinim:** NE-PLR-175 · **Katman:** L1 · **Otomasyon:** CI + fuzz
- **Adımlar:** 10 MB preset · depth 1000 iç içe JSON · 1e9 sayı · unknown key bombası (10000 anahtar).
- **Beklenen:** Cap/kırpma; depth cap `[ÖNERİ — 64]`; eval/serileştirme yasağı (statik); crash yok.
- **Kanıt:** ADR-091 · AGENTS §5

#### TC-PLR-176 — SQL injection + Türkçe adlar
- **Gereksinim:** NE-PLR-176 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Arama: `' OR 1=1 --` · `O'Brien` · `"' UNION SELECT ...` · `Şarkı İöüç`.
- **Beklenen:** Payload'lar satır getirmez (parametreli); `O'Brien`/Türkçe sonuç verir; `SELECT *` yok (statik).
- **Kanıt:** AGENTS §5 · ADR-074

#### TC-PLR-177 — Path traversal (tarama kökü)
- **Gereksinim:** NE-PLR-177 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Tarama kökünde `../../../Windows/System32` · UNC `\\evil\share` vektörleri (indeksleme kapsamı).
- **Beklenen:** Kök dışına indeksleme RED; NormalPath çözümleme; çalma davranışı ayrı politika `[ÖNERİ]`.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-178 — FFI clamp (player birleşimi)
- **Gereksinim:** NE-PLR-178 · **Katman:** L1 · **Otomasyon:** CI + fuzz
- **Adımlar:** band=-1/999999999 · gain=±1e9 · ms=-500 · NULL path → C ABI.
- **Beklenen:** `INVALID_ARG`/clamp; UB yok (ASan); kardeşi TC-ENG-157 ile aynı vektör seti.
- **Kanıt:** kardeş §05.1

#### TC-PLR-179 — Tek instance + IPC kimlik reddi
- **Gereksinim:** NE-PLR-179 · **Katman:** L2 · **Otomasyon:** manuel + CI (şartlı)
- **Adımlar:** İkinci başlatma → odak; farklı kullanıcıdan IPC mesajı denemesi; bilinmeyen mesaj şeması.
- **Beklenen:** İkinci = mevcut pencereye odak; farklı kullanıcı RED; bilinmeyen mesaj drop (uzantı yok).
- **Kanıt:** ADR-107 §8.6 (U4) `[ÖNERİ — SID/UID]`

#### TC-PLR-180 — Supply chain taraması
- **Gereksinim:** NE-PLR-180 · **Katman:** güvenlik · **Otomasyon:** CI
- **Adımlar:** Bağımlılık envanteri + CVE taraması + lock dosyası doğrulaması.
- **Beklenen:** Kritik CVE = release bloke; lock repo'da; FFmpeg vendor `⚠️ VR (§14.5)`.
- **Kanıt:** AGENTS §5 · kardeş NE-ENG-114

#### TC-PLR-181 — RAM ≤400 MB ölçümü
- **Gereksinim:** NE-PLR-181 · **Katman:** Kapı · **Otomasyon:** CI + manuel
- **Adımlar:** 1000 dosyalık katalog + oynatma + 31-band EQ; RSS tepe; video OFF/ON iki satır.
- **Beklenen:** ≤400 MB (ses); video ON satırı §06.5 ayrı `⚠️ VR`; rapor release'de.
- **Kanıt:** ADR-0105 §2.9 ⚠️

#### TC-PLR-182 — CPU bütçesi (EQ payı)
- **Gereksinim:** NE-PLR-182 · **Katman:** bench · **Otomasyon:** CI (rapor)
- **Adımlar:** 192k/32f + 31-band + limiter tek çekirdek ölçümü.
- **Beklenen:** EQ payı ≤%5; decode payı `⚠️ VR` raporda ayrı satır.
- **Kanıt:** ADR-0105 §5.6 ⚠️ · ADR-106 §1.3

#### TC-PLR-183 — Latency <20 ms (player ölçümü)
- **Gereksinim:** NE-PLR-183 · **Katman:** Kapı (5.10) · **Otomasyon:** manuel (release)
- **Adımlar:** Kardeş §11.3-A düzenek; WASAPI Excl/Shared ayrımı; blok ≥1024@48k denenmez.
- **Beklenen:** <20 ms; L1-L4 `⚠️ VR` tanımlanana kadar en küçük desteklenen blok.
- **Kanıt:** ADR-0105 §2.10 ⚠️ · kapı 5.10

#### TC-PLR-184 — Kapı 5.1 (player koşulları + guard)
- **Gereksinim:** NE-PLR-184 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** §11.2 hattı + Flat ön koşulu + inverted guard negatif denetimi (TC-PLR-040/087 ile hizalı).
- **Beklenen:** 12/12 + guard doğru; sahte sonuç yok.
- **Kanıt:** ADR-0105 kapı 5.1 ⚠️ · ADR-106 §5.1 Ş1/Ş4

#### TC-PLR-185 — Kapı 5.10 rapor arşivi
- **Gereksinim:** NE-PLR-185 · **Katman:** Kapı · **Otomasyon:** release checklist
- **Adımlar:** Ölçüm raporu dosyası + eşik satırı.
- **Beklenen:** Rapor yok/eseşik üstü = release adayı değil.
- **Kanıt:** ADR-0105 kapı 5.10 ⚠️

#### TC-PLR-186 — Kapı 5.11 (A/V sync) — koşul kontrolü
- **Gereksinim:** NE-PLR-186 · **Katman:** Kapı · **Otomasyon:** şartlı (video fazı)
- **Adımlar:** (a) Ses-only fazda kapı koşul dışı mı? (b) Video fazında P95 ölçümü.
- **Beklenen:** (a) Koşul dışı (sahte FAIL yok); (b) P95 ≤20 ms raporu.
- **Kanıt:** ADR-0105 kapı 5.11 ⚠️

#### TC-PLR-187 — Kapı 5.12 (gapless) — tam vektör
- **Gereksinim:** NE-PLR-187 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Grup D vektörleri + radio stream→stream + ölçüm raporu.
- **Beklenen:** Hepsi yeşil; ±5 ms ölçüm.
- **Kanıt:** ADR-0105 kapı 5.12 ⚠️

#### TC-PLR-188 — Kapı 5.13 (fuzz) — sayım raporu
- **Gereksinim:** NE-PLR-188 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Corpus sayıları format bazında rapor; süre ölçümü.
- **Beklenen:** ≥1000/format · ≥8000 toplam · 0 crash · ≤10 dk.
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️ · ADR-106 Ş7

#### TC-PLR-189 — UI spam + uzun süre stres
- **Gereksinim:** NE-PLR-189 · **Katman:** Kapı · **Otomasyon:** scheduled
- **Adımlar:** 30 dk oynatma + otomatik tıklama botu (transport/EQ/radyo karışık).
- **Beklenen:** xrun 0 · UI crash 0 · yanıt gecikmesi `[ÖNERİ — <100 ms]`.
- **Kanıt:** S3 §6.1 · kardeş G4

#### TC-PLR-190 — Leak (1000 cycle + tarama)
- **Gereksinim:** NE-PLR-190 · **Katman:** L2 · **Otomasyon:** CI (LSan)
- **Adımlar:** 1000 play/stop + katalog taraması tekrarı; net artış.
- **Beklenen:** 0; sanitizer temiz.
- **Kanıt:** kardeş NE-ENG-141

#### TC-PLR-191 — TSan (UI + tuner + control spam)
- **Gereksinim:** NE-PLR-191 · **Katman:** L2 · **Otomasyon:** CI (Linux TSan)
- **Adımlar:** 10 thread × karışık komut 60 s; tuner thread + UI thread + control.
- **Beklenen:** 0 data race.
- **Kanıt:** kardeş NE-ENG-131

#### TC-PLR-192 — Radyo stres Ş1-Ş7 raporu
- **Gereksinim:** NE-PLR-192 · **Katman:** Kapı · **Otomasyon:** CI + scheduled
- **Adımlar:** 7 şablonu tek raporda topla (Ş1 slow-start · Ş2 10 paralel · Ş3 100 istasyon · Ş4 guard · Ş5 EQ · Ş6 oynatma · Ş7 fuzz).
- **Beklenen:** 7/7 raporlanmış; her biri ayrı onay satırı.
- **Kanıt:** ADR-106 §5.2

#### TC-PLR-193 — UI a11y checklist
- **Gereksinim:** NE-PLR-193 · **Katman:** UX · **Otomasyon:** — (release)
- **Adımlar:** Klavye/focus/kontrast/etiket checklist doldurma (§02.3 I-11 + AGENTS §17 EREG karşılığı).
- **Beklenen:** Checklist dolu; açık madde = release notu.
- **Kanıt:** AGENTS §17 Edge 8 · YOK — `[ÖNERİ]`

#### TC-PLR-194 — Yerelleştirme testi (şartlı)
- **Gereksinim:** NE-PLR-194 · **Katman:** L2 · **Otomasyon:** şartlı (§18.2 kararı)
- **Adımlar:** Karar verilirse TR/EN string sweep + Türkçe metin kırılması.
- **Beklenen:** Karar yoksa test KOŞUL DIŞI (kapsam dışı etiketi korunur).
- **Kanıt:** YOK

#### TC-PLR-195 — Metrik regresyon etiketi (%10)
- **Gereksinim:** NE-PLR-195 · **Katman:** proses · **Otomasyon:** CI
- **Adımlar:** Baseline RAM/boyut/CPU; %10 kötüleşme simülasyonu.
- **Beklenen:** PR etiketi/warn basar; etiket yoksa FAIL.
- **Kanıt:** kardeş NE-ENG-100 `[ÖNERİ]`

#### TC-PLR-196 — Release test raporu şablonu
- **Gereksinim:** NE-PLR-196 · **Katman:** proses · **Otomasyon:** — (release)
- **Adımlar:** Şablon doldurulmuş mu denetimi (kapı 5.1/5.10-5.13 + Ş1-Ş7 + SNR/THD).
- **Beklenen:** Boş satır yok; release PR'ında eki.
- **Kanıt:** kardeş NE-ENG-144 · Guardrail #16 ruhu

#### TC-PLR-197 — Determinism (2× hash)
- **Gereksinim:** NE-PLR-197 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Aynı 12 kombinasyonu 2× çalıştır → diff.
- **Beklenen:** Eşitlik; fark = atomik/non-atomik hatası.
- **Kanıt:** kardeş NE-ENG-145 · §11.2

#### TC-PLR-198 — ABI sembol denetimi (player)
- **Gereksinim:** NE-PLR-198 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Player binary export/import listesi; `neva-core` iç sembol kullanımı.
- **Beklenen:** Player yalnız `neva_*` import eder; iç sembol yok.
- **Kanıt:** kardeş NE-ENG-142 · NE-PLR-002

#### TC-PLR-199 — Event zinciri kontratı (E1-E10)
- **Gereksinim:** NE-PLR-199 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Her event tetikleyicisi → beklenen event sırası + payload.
- **Beklenen:** §05.2 tablo birebir; eksik/yanlış sıra = FAIL.
- **Kanıt:** kardeş §05.2 · ADR-106 kural 2

#### TC-PLR-200 — Flaky test yasağı (retry=0)
- **Gereksinim:** NE-PLR-200 · **Katman:** proses · **Otomasyon:** CI (ayar denetimi)
- **Adımlar:** CI config'te retry ayarı denetimi + kararsız test log taraması.
- **Beklenen:** retry=0; kararsız test = hata sayılıp düzeltilir.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-201 — ABI minor genişleme (E1-E10)
- **Gereksinim:** NE-PLR-201 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Header diff (eski 9 fonksiyon sabit mi) + eski header ile yeni DLL link.
- **Beklenen:** 9 fonksiyon + 5 kod değişmez; ekleme minor; kırılma = majör.
- **Kanıt:** kardeş §05.4 · ADR-106 kural 2

#### TC-PLR-202 — Python ctypes smoke
- **Gereksinim:** NE-PLR-202 · **Katman:** L2 · **Otomasyon:** CI (opsiyonel)
- **Adımlar:** ctypes ile session open → play → stop → close.
- **Beklenen:** C99 ABI'dan bağlanır; 0 hata.
- **Kanıt:** ADR-0105 v1.0.2 §3 ⚠️ · Vision (25 dil)

#### TC-PLR-203 — "25 dil" tanımı kararı
- **Gereksinim:** NE-PLR-203 · **Katman:** proses · **Otomasyon:** — (karar)
- **Adımlar:** §18.5 karar satırı kontrolü.
- **Beklenen:** Karar verilmeden ölçüm "25" iddiası yapılmaz (UNKNOWN korunur); daraltma `[ÖNERİ]` kayda geçer.
- **Kanıt:** ADR-0105 U2 ⚠️

#### TC-PLR-204 — neva-mcp (şartlı)
- **Gereksinim:** NE-PLR-204 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Karar sonrası: bridge üzerinden transport komut turu + yetki reddi.
- **Beklenen:** C ABI üzerinden; yetki modeli §18.5 kararına bağlı; karar yok test yok.
- **Kanıt:** ADR-0105 v1.4.0 `[ÖNERİ]`

#### TC-PLR-205 — neva-remote (şartlı)
- **Gereksinim:** NE-PLR-205 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Karar sonrası: localhost token doğrulama + yanlış token RED + basit komut.
- **Beklenen:** Varsayılan localhost; auth reddi doğru; core'da ağ yok (statik).
- **Kanıt:** ADR-0105 v1.4.0 `[ÖNERİ]` · ADR-107 K2

#### TC-PLR-206 — Web UI (şartlı U4)
- **Gereksinim:** NE-PLR-206 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Karar sonrası: şifre akışı (UPnify emsal) + açık port taraması.
- **Beklenen:** Şifresiz erişim varsayılan değil `[ÖNERİ]`; karar yok test yok.
- **Kanıt:** ADR-0105 U4 ⚠️ · ADR-107 §8.6

#### TC-PLR-207 — Katalog dışa aktarma (CSV/M3U)
- **Gereksinim:** NE-PLR-207 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Dışa aktarma → dosya içeriği + onay akışı.
- **Beklenen:** CSV/M3U geçerli; yol yazımı kullanıcı onaylı (gizlilik notu).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-208 — Yerel M3U içe aktarma
- **Gereksinim:** NE-PLR-208 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** 1000 satırlık M3U · `file:` satırı · `http:` satırı · `javascript:` satırı.
- **Beklenen:** Cap 1000; http satırı validator'dan; file satırı kök sınırlı; zararlı satır RED.
- **Kanıt:** YOK — `[ÖNERİ]` · ADR-106 kural 8 (radyo playlist'i ile ayrık)

#### TC-PLR-209 — Hotkey kaydı/geri alma (şartlı)
- **Gereksinim:** NE-PLR-209 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** Kısayol ata → çakışan kısayolla dene → geri al.
- **Beklenen:** Çakışma uyarısı; geri alma temiz (OS kaydını siler).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-210 — Bildirim sanitizasyonu (şartlı)
- **Gereksinim:** NE-PLR-210 · **Katman:** L2 · **Otomasyon:** CI (şartlı)
- **Adımlar:** XSS payload'lı başlık → OS bildirimi.
- **Beklenen:** Düz metin; varsayılan kapalı `[ÖNERİ]`.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-PLR-211 — Plugin loader yokluğu (statik)
- **Gereksinim:** NE-PLR-211 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Player kodunda plugin/dlopen/LoadLibrary yükleyici grep'i.
- **Beklenen:** 0 isabet (OS DLL yüklemeleri hariç gerekçeli); aksi FAIL.
- **Kanıt:** YOK — `[ÖNERİ — kapsam]`

#### TC-PLR-212 — Sürücü kurulumu yok (first-run)
- **Gereksinim:** NE-PLR-212 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** ASIO driver kurulu olmayan makinede ilk çalıştırma.
- **Beklenen:** Cihaz listesi dolu (mevcut cihazlar); ASIO satırı yok/şartlı; kurulumu otomatik yapmaz; yönlendirme mesajı.
- **Kanıt:** kardeş §04.2 `[ÖNERİ]`

#### TC-PLR-213 — Gizli ağ çağrısı yok
- **Gereksinim:** NE-PLR-213 · **Katman:** güvenlik · **Otomasyon:** CI (ağ profili)
- **Adımlar:** Ağ kapalı/uçak modunda tam iş akışı; izinli alanlar listesi.
- **Beklenen:** Sadece kullanıcı tetikli radyo/keşif; telemetri/sürüm/sahte analytics 0.
- **Kanıt:** YOK — `[ÖNERİ]` · NE-PLR-013

#### TC-PLR-214 — Log hijyeni
- **Gereksinim:** NE-PLR-214 · **Katman:** güvenlik · **Otomasyon:** CI (secret-scan)
- **Adımlar:** Örnek loglar üzerinde: parola/token/sertifika pattern + URL query string taraması.
- **Beklenen:** 0 sızıntı; stream URL query maskelenmiş.
- **Kanıt:** AGENTS §5 · kardeş NE-ENG-156

#### TC-PLR-215 — Dış API envanteri denetimi
- **Gereksinim:** NE-PLR-215 · **Katman:** proses · **Otomasyon:** CI (doküman denetimi)
- **Adımlar:** §14.5/§17.2 tablosu ↔ kodda ağ çağrıları diff (curl/FFmpeg/network grep).
- **Beklenen:** Her çağrı envanterde; envanterde olmayan = FAIL.
- **Kanıt:** ADR-106 kural 3/6 · §12.1 tehdit tablosu

### §11.7 Regresyon & Sürekli Entegrasyon Özeti (Player)

**Her PR koşusu:** L1 + L2 + kapı 5.1 (hash+guard) + kapı 5.12 (vektörler) + kapı 5.13 (fuzz kısa mod) + statik grep kapıları (whitelist/escape/SQL/plugins) + sanitizer build.
**Zamanlanmış (gece):** fuzz tam mod (≥8000 corpus) + Ş1-Ş6 radyo stres + 1000 cycle leak + TSan + 30 dk UI spam.
**Release adayı:** kapılar 5.1/5.10/5.11/5.12/5.13 + SNR/THD + A/V + RAM/VRAM raporları + manuel checklist (§11.5) + tehdit tablosu gözden geçirmesi.
**Başarısızlık:** Kapı FAIL = release engelleyici; istisna yalnız gerekçeli bilinçli regresyon etiketi (NE-PLR-195 warn politikası).

---

## §12 Güvenlik (Player — OWASP Top 10:2025 · ADR-106 §5 Debate)

> **Kaynak:** ADR-106 §5 (S1-S8 security debate · G1-G6 gate) · ADR-074 · ADR-091 · AGENTS §5 · kardeş PRD §13 (motor).
> **İlke:** Güvenlik kararları **UX ile müzakere edilir ama güvenlik pazarlık edilemez** — varsayılan güvenli, geri uyumlu değil.

### §12.1 Tehdit Tablosu (OWASP Top 10:2025 ↔ Player)

| OWASP | Player tehdidi | Yüzey | Savunma | Öncelik | Test |
|---|---|---|---|---|---|
| **A01** Yetki ihlali | Yetkisiz cihaz/kontrol erişimi; IPC sahteciliği | neva-host köprüsü · remote/MCP (şartlı) | localhost + SID/UID kimlik `[ÖNERİ]` · komut şeması doğrulaması | P1 | TC-PLR-179 |
| **A02** Kriptografik başarısızlık | TLS doğrulama kapatma; kör kabul | https istasyonlar | mbedTLS 9.13.0 zincir doğrulama; kapatma bayrağı yok | P0 | TC-PLR-169 |
| **A03** Injection | XSS (başlık/katalog) · SQLi (arama) · path traversal | Metadata render · katalog · tarama | HTML-escape · parametreli SQL · normalizasyon | P0 | TC-PLR-170/176/177 |
| **A04** Güvensiz tasarım | Validator bypass; çift kapı (biri zayıf) | URL/protokol yolu | **Tek kapı** (ADR-106 kural 6) + statik denetim | P0 | TC-PLR-104/215 |
| **A05** Güvensiz yapılandırma | Varsayılan gevşek (FuzzedFiles/open_media, HTTP09Allowed) | FFmpeg/curl yapıları | Varsayılan kapatma (kural 6: `{ FuzzedFiles=0, ... }`) · `HTTP09Allowed=0` | P0 | TC-PLR-021/022 |
| **A06** Zafiyetli bileşen | CVE'li FFmpeg/mbedTLS/libcurl | Vendor bağımlılıkları | Sürüm sabitleme + CVE taraması; FFmpeg vendor `⚠️ VR` | P1 | TC-PLR-180 |
| **A07** Kimlik doğrulama başarısızlığı | Şifresiz web UI/remote (eğer eklenirse) | Uzak yüzey (şartlı U4/U5) | Varsayılan localhost + token `[ÖNERİ]`; karar kapıları | P2 | TC-PLR-179/205/206 |
| **A08** Veri bütünlüğü başarısızlığı | Bozuk preset/config; yarı yazma | Config/PRESET depo | Atomik write + yedek `[ÖNERİ]` · depth cap | P1 | TC-PLR-146/175 |
| **A09** Log/izleme başarısızlığı | Log'da URL/secret sızıntısı; olay izlenememe | Dosya logları | Log hijyeni + secret-scan | P1 | TC-PLR-214 |
| **A10** SSRF | Özel IP/metadata erişimi (cloud) | İstasyon URL'leri | IP/redir validator (T0-T27) | P0 | TC-PLR-167/168 |

### §12.2 URL/Protokol Vektör Kataloğu (T0-T27 — S1-S3 Kapsamı)

> Her vektör: **girdi → beklenen (RED/OK) → test**. Hepsi TC-PLR-104'te tek senaryoda çalışır.

#### T0-T5 — Şema doğrulaması
| # | Girdi | Beklenen | Neden |
|---|---|---|---|
| T0 | `https://ok.example/stream` | OK (sınır) | Basit https |
| T1 | `http://plain.example/x` | OK* (*onay `[ÖNERİ]`) | http yalnız kullanıcı onayı; varsayılan RED |
| T2 | `file:///C:/Windows/System32/evil.dll` | RED | Yerel dosya (ADR-106 kural 6) |
| T3 | `file://localhost/c:/x` | RED | Şema varyantı |
| T4 | `ftp://remote/x` | RED | Şema dışı |
| T5 | `javascript:alert(1)` | RED | Komut/yürütme şeması |

#### T6-T11 — Özel-IP / SSRF
| # | Girdi | Beklenen |
|---|---|---|
| T6 | `http://127.0.0.1:8080/` | RED (loopback) |
| T7 | `http://[::1]/` | RED (IPv6 loopback) |
| T8 | `http://10.1.2.3/x` | RED (10/8) |
| T9 | `http://172.16.0.1/x` | RED (172.16/12) |
| T10 | `http://192.168.1.1/x` | RED (192.168/16) |
| T11 | `http://169.254.169.254/latest/meta-data/` | RED (cloud metadata — link-local) |

#### T12-T17 — IPv6/uzun form + kaçış
| # | Girdi | Beklenen |
|---|---|---|
| T12 | `http://[fd00::1]/` | RED (ULA fc00::/7) |
| T13 | `http://[fe80::1]/` | RED (IPv6 link-local) |
| T14 | `http://0x7f.0.0.1/` | RED (hex IP çözümü) |
| T15 | `http://2130706433/` | RED (decimal IP — 127.0.0.1) |
| T16 | `http://127.1/` | RED (kısaltılmış loopback) |
| T17 | `http://[0:0:0:0:0:ffff:127.0.0.1]/` | RED (IPv4-mapped IPv6) |

#### T18-T22 — Redirect zinciri
| # | Senaryo | Beklenen |
|---|---|---|
| T18 | 5 hop https→https | OK (sınır dahili) |
| T19 | 6 hop | RED (5. hop son) |
| T20 | 3. hop'ta private IP hedefi | RED (her hop validator) |
| T21 | 1. hop'ta `file:` hedefi | RED (şema tekrarı) |
| T22 | Loop (A→B→A) | RED (hop sayacı taşar) |

#### T23-T27 — DNS / TTL / edge
| # | Senaryo | Beklenen |
|---|---|---|
| T23 | DNS çözümü → 127.0.0.1 (rebinding) | RED (çözüm sonrası tekrar kontrol + post-connect IP) |
| T24 | TTL=0 DNS (saldırgan değiştirir) | RED (bağlantı anında IP teyidi) |
| T25 | IDN/punycode host (`http://еxample.com`) | Bilinmeyen host politikası `⚠️ VR (karar: karalama listesi yoksa OK + kullanıcı uyarısı)` |
| T26 | Username/password içeren URL (`http://user:pass@h`) | RED `[ÖNERİ]` (credenitals URL — log sızıntısı riski) |
| T27 | Port dışı (`https://x:22`) | İzinli portlar: 80/443/radyo yaygın portlar `[ÖNERİ — liste açık: yalnız 80/443 + kullanıcı onayı]` |

### §12.3 Radyo/Yüzey Güvenlik Kuralları (S4-S8 ↔ Kod)

| Kural (ADR-106) | Somut doğrulama | Test |
|---|---|---|
| S4/`HTTP09Allowed=0` | Yapılandırma + sahte HTTP/0.9 sunucu | TC-PLR-022 |
| S5 TLS (mbedTLS 9.13.0) | 3 RED yolu + geçerli kabul | TC-PLR-169 |
| S6 Protocol whitelist | `file/concat/crypto/subfile` + bypass denemeleri | TC-PLR-021/174 |
| S7 text blok 1 MB + icy-metaint 32 B..1 MiB | Sınır testleri + fuzz | TC-PLR-103 |
| S8 HLS kapıları (50 MB/1000 entry/derinlik 1) + DTD/XXE + URI (`file|ftp`) | Kapı testleri | TC-PLR-172/173 |
| Radio v2 XXE (`<!ENTITY SYSTEM file://>`) | Entity kapalı | TC-PLR-172 |
| escaping/double-escape (StreamTitle → UI) | `&lt;script&gt;` benzeri + ham payload | TC-PLR-101/170 |

### §12.4 FFmpeg/Curl Güvenli Yapılandırma Şablonu

```text
FFmpeg (URL-based input, yalnız player yüzeyinde):
  FuzzedFiles=0 · ReallocateEvenBigger=0 · ReallocateReallocate=0   → ADR-106 kural 6 (güvenli varsayılan)
  yasaklı protokoller (whitelist ile): file · concat · crypto · subfile · data
curl:
  HTTP09Allowed=0 · RedirectMax=5 · timeout=10s ·
  SSL_VERIFY_PEER=1 (kör kabul yok) · IP blacklist (T6-T17) her hop
mbedTLS (radio cipher suite 9.13.0):
  TLS1.2/1.3 · zayıf cipher yok `⚠️ VR (cipher listesi indeks'te yok — önerü: varsayılan “normal” profili)`
```

**Doğrulama:** Şablon CI'da statik olarak koddan okunur (regex) — yapılandırma drift'i FAIL.

### §12.5 Veri & Depo Güvenliği

| Depo | İçerik | Tehdit | Savunma | Test |
|---|---|---|---|---|
| Config JSON | Ayarlar/cihaz tercihi | Yarı yazma · depth bombası | Atomik write + yedek `[ÖNERİ]` · depth cap 64 `[ÖNERİ]` | TC-PLR-146/175 |
| Preset JSON | EQ değerleri | Bilinmeyen key · dev boyut | ignore + cap | TC-PLR-175 |
| Katalog DB (SQLite, ADR-074) | İstasyon/müzik | SQLi · bozuk şema | Parametreli SQL · migrate + geri alma | TC-PLR-176/159 |
| Log dosyaları | Olaylar | URL/secret sızıntısı | Hijyeni + redaksiyon `[ÖNERİ]` | TC-PLR-214 |
| Çalma listesi (M3U) | Dosya yolları | Traversal · zararlı satır | Kök sınırlaması + validator | TC-PLR-208 |

### §12.6 Güvenlik Gate G1-G6 (ADR-106 §5 — Release Kapıları)

| Gate | İçerik | Test |
|---|---|---|
| G1 | URL/protokol whitelist + SSRF/redir | TC-PLR-104/167/168/174 |
| G2 | FuzzedFiles/HTTP09 varsayılanları | TC-PLR-022/175 |
| G3 | TLS doğrulama | TC-PLR-169 |
| G4 | Metadata escape/cap/UTF-8 | TC-PLR-101/170/171 |
| G5 | HLS/ICY parser kapıları | TC-PLR-103/172/173 |
| G6 | Log hijyeni + secret-scan | TC-PLR-214/180 |

**Tüm gate'ler release blocker'dır;** P0 bulgu = çıkış yok (ADR-106 §5 ruhu: "güvenlik pazarlık edilemez").

### §12.7 Savunma Derinliği Özeti

```
Kullanıcı girdisi (URL/M3U/preset/metadata)
  → Katman 1: Validator/parser (tek kapı · cap · escape)
  → Katman 2: API sınırı (FFI clamp · kod reddi)
  → Katman 3: Motor izolasyonu (ağ yok · RT yasakları)
  → Katman 4: Gözlem (statik kapı · fuzz · tehdit tablosu revizyonu)
```

**Tekrarlanan ihlal cevabı:** Aynı saldırı iki kez gelirse kapı iki yerde değişmez — **tek kapıda** değişir (ADR-106 kural 6) ve tehdit tablosuna satır eklenir (NE-PLR-166).

---

## §13 Hata Durumları & Hata UX (Player)

> **Kaynak:** ADR-106 kural 8 (kullanıcı dostu mesaj) · kardeş PRD §12 (hata modları + kod tablosu) · ADR-019 §1.4 (durum makinesi) · NE-PLR-151..160.
> **İlke:** Her hatanın **3 katmanı** vardır: **(1) teknik kod** (log/metric) → **(2) durum** (makine) → **(3) kullanıcı mesajı** (Türkçe, jargon yok, eylem önerili).

### §13.1 Durum Makinesi ↔ Hata X (bu PRD'nin kendi Sınıf X'i)

| Kaynak durum | Tetikleyici | Çıkış durum | Kullanıcı mesajı (şablon) | Eylem |
|---|---|---|---|---|
| STOPPED → ERROR | Engine hata kodu (≠0 · eşleme kardeş §12.3) | ERROR | "Dosya açılamadı. Detay: {kısa kod}" | Yeniden dene · Geri |
| PREBUFFER → ERROR | 3 başarısızlık (backoff bitti) | ERROR | "İstasyon 3 denemeden sonra durdu. Yeniden deneyin." | Yeniden dene (elle) · URL düzenle |
| PLAYING → ERROR | Cihaz kaybı + zincir tükendi | ERROR | "Ses cihazı bulunamadı. Cihazı kontrol edin." | Cihaz listesini aç |
| * → ERROR-K (invalid_arg) | Kullanıcı girdisi | Kalıcı form hatası (durum değişmez) | Alan bazında: "Geçerli bir URL girin" | Form düzeltmesi |
| * → UYARI (unsupported_format) | Kod 3 | Durum korunur + toast | "Bu format desteklenmiyor: {ad}" | Sonraki dosya (kuyruk) |
| * → UYARI (bozuk dosya) | Decode FAIL | Durum korunur + toast | "Dosya okunamadı, atlanıyor: {ad}" | Atla devam |
| Köprü koptu (ERROR-3) | neva-host/engine kopması | ERROR-3 (tek destekleyici) | "Oynatma motoruyla bağlantı koptu. Yeniden bağlanılıyor…" | Otomatik yeniden bağlan (1 kez) + elle |

**Mesaj kuralları (ADR-106 kural 8):**
1. Kullanıcıya **jargon yok** (no "AUDCLNT_E_DEVICE_INVALIDATED"); teknik kod log'da.
2. Mesaj **eylem önerir** ("yeniden deneyin" / "cihazı kontrol edin").
3. Tek seferde **tek hata** gösterilir (kaskad bastırılır — önceki hatanın devamı görsel spam yaratmaz).
4. ERROR'da **veri korunur** (kuyruk/pozisyon/ayarlar) — kurtarma sonrası kaldığı yerden.

### §13.2 Hata UX Senaryoları (Akış Haritası)

#### Senaryo H1 — İlk istasyon hatası (ne ilk-iş, ne son-çare)
```
Kullanıcı URL girer → PREBUFFER → (sunucu yok) → backoff 1s/2s/4s (otomatik, sessiz)
  → 3 başarısız → ERROR: "İstasyon 3 denemeden sonra durdu. Yeniden deneyin."
  → [Yeniden dene] → elle deneme (otomatik sayaç sıfırlanır) veya [Düzenle] → URL formu
```
- Görsel: Yalnız **tek** mesaj kartı; backoff sırasında ince yükleniyor göstergesi (odaklanılabilir `[ÖNERİ]`).
- Test: TC-PLR-106 · TC-PLR-151 · TC-PLR-158.

#### Senaryo H2 — Cihaz kaybı (çalarken)
```
PLAYING → cihaz çıkarıldı → Engine zincir → fallback cihazı VAR → devam + bilgi balonu:
  "Ses cihazı değişti: {eski} → {yeni}"
  → fallback YOK → ERROR: "Ses cihazı bulunamadı. Cihazı kontrol edin." + cihaz listesi açılır
```
- Rozet: exclusive kaybettiyse bit-perfect rozeti gizlenir (NE-PLR-045).
- Test: TC-PLR-048 · TC-PLR-160.

#### Senaryo H3 — Desteklenmeyen format (kuyrukta)
```
PLAYING → {dosya}.wma → kod 3 → toast: "Bu format desteklenmiyor: dosya.wma"
  → kuyruk otomatik sonraki dosyaya geçer → oynatma kesintisiz devam eder
```
- Kural: Uygulama kuyruğu **durmaz**, yalnız dosya atlanır (tek dosya hatası kuyruğu öldürmez).
- Test: TC-PLR-154 · TC-PLR-153.

#### Senaryo H4 — Bozuk dosya (kaynak medya)
```
FLAC bozuk → decode FAIL → toast + log → atla → sonraki → (aynı dosya 2× FAIL) →
  kullanıcıya kalıcı uyarı: "{dosya} tekrar okunamadı — listeden kaldırıldı mı?" [Kaldır] [Kal]
```
- Veri güvenliği: Katalogdan **silme yalnız kullanıcı onayıyla** (veri kaybı yok — FM-06).
- Test: TC-PLR-029 · TC-PLR-153.

#### Senaryo H5 — Engine köprüsü koptu (ERROR-3 — tek destekleyici)
```
UI ← (neva-host köprüsü) → koptu → ERROR-3: "Oynatma motoruyla bağlantı koptu. Yeniden bağlanıyor…"
  → otomatik yeniden bağlanma (1 kez, 2 sn timeout `[ÖNERİ]`) → başarısız →
  elle: [Yeniden başlat] → oturum durumu korunur (config'den geri)
```
- NE-ENG-107 ile birebir: yalnız **bir** ERROR-3 destekleyici satır vardır.
- Test: TC-PLR-155 · kardeş TC-ENG-105.

#### Senaryo H6 — Dahili hata (kod 4 — "bir şeyler ters gitti")
```
INTERNAL (kod 4) → log (kod+stack) → kullanıcı: "Beklenmeyen bir sorun oluştu. Otomatik toparlanıyor…"
  → oturum yeniden kurulumu → devam → (başarısız) → ERROR-1 + [Yeniden başlat]
```
- Jargon yok; log'a teknik detay; telemetri opt-in.
- Test: TC-PLR-156.

#### Senaryo H7 — Sağlam kapanış → geri dönüş (lame duck değil)
```
Kapat → son durum kaydedilir (pozisyon/mod/cihaz/EQ/kuyruk) → yeniden açılış →
  Son durum geri: "Kaldığınız yerden devam ediliyor: {parça} {pozisyon}" (bilgi, zorunlu değil)
```
- Ani kapatmada: son güvenli checkpoint (`⚠️ VR — write-stratejisi §06`).
- Test: TC-PLR-164 · TC-PLR-010.

### §13.3 Hata Görsel Dili (Bileşen Kuralları)

| Durum | Bileşen | Konum | Odak |
|---|---|---|---|
| Geçici (toast) | Bilgi/uyarı balonu (otomatik kaybolur `⚠️ VR — 4 sn önerü`) | Köşe | Odak **çalma** (engellemez) |
| Hata kartı (ERROR) | Kalıcı kart + ikon + eylem butonları | İçerik alanı | İlk butona odak (klavye) |
| Form hatası | Alan altı metin (kırmızı + ikon — renk tek başına değil) | Form | İlgili alana odak |
| Yükleniyor/Prebuffer | İnce progress (deterministik olmayan ilerleme → belirsiz mod `[ÖNERİ]`) | Yerinde | Odak korunur |
| Fallback bilgisi | Bilgi satırı (rozet + açıklama) | Üst bar | Odaklanmaz |

**WCAG:** Renk tek başına sinyal değil (ikon + metin) · dokunma/klik hedefi ≥24px · odak outline görünür `[ÖNERİ — 3px]` (AGENTS §5 UI/UX).

### §13.4 Hata Olayları ↔ Telemetri

| Olay | Teknik log | UI durumu | Metric sayaç |
|---|---|---|---|
| E10 error event (engine) | `error code={n} backend={b}` | §13.1 tablosuna göre | `errors_total{code}` |
| Backoff | `retry k=3 next=+4s` | Sessiz (yükleme) | `radio_backoff_total` |
| Fallback | `E8 reason=...` | Bilgi satırı | `fallback_total` |
| Atla | `skip file=... reason=decode` | Toast | `skips_total` |
| Bloom (metadata spam) | `streamtitle_drop=n` | Sessiz | `meta_dropped` |

**Kural:** Metrik/telemetri varsayılan **kapalı-dışa** (`⚠️ VR — ADR-0105 sürüm kontrolü belirtmiyor`; önerü: yalnız yerel log, dışa aktarım kullanıcı onayı — NE-PLR-213).

---

## §14 Lisans Matrisi (Player)

> **Kaynak:** ADR-0105 lib-matrix (indes — `⚠️ VR` işaretli) · ADR-106 §1.3/§5.4/kural 9 · kardeş PRD §14 (L-numaraları ortak) · AGENTS §5.
> **Kural:** `UNKNOWN` = RED (release paketinde yok); bilinmeyen lisans asla sessizce paketlenmez (G3 ruhu).

### §14.1 Player Lisans Matrisi

| # | Bileşen | Rol | Lisans | Durum |
|---|---|---|---|---|
| P01 | Neva Engine paketi (motor) | Ses motoru | Motor L01 kararına bağlı (§18.8 kardeş) | Devralınır — motor paketi |
| P02 | **FFmpeg 9.0.2** (G pl6/gpl1) | Decode (MP3/AAC/OGG/Opus + HLS demux) | LGPL-2.1+ — `--enable-gpl` YOK · GPL codec yok (x264 vb.) | Teyitli (isim+sürüm) · build CI |
| P03 | **dr_libs** (dr_flac/dr_mp3/dr_wav/dr_imaadpcm) | Yerel decode (FLAC/WAV) | MIT ailesi `⚠️ VR (tam metin lib-matrix)` | Teyitli (isim) |
| P04 | **soxr** | Resampler (producer-side) | **LGPL-2.1** — dinamik `✅`, statik = `⚠️ LİSANS` | Teyitli |
| P05 | **mbedTLS 9.13.0** | TLS | Apache-2.0 | Teyitli |
| P06 | **libcurl 8.23.0** | HTTP (radio/keşif) | curl/MIT `⚠️ VR` | Teyitli (isim+sürüm) |
| P07 | **libsndfile** | WAV I/O adayı | LGPL-2.1 `⚠️ VR` | Statik tartışma |
| P08 | **libopus** | Opus | BSD-3-Clause | Teyitli |
| P09 | **libFLAC / dr_flac** | FLAC | FLAC analog BSD / MIT `⚠️ VR` | Teyitli (isim) |
| P10 | **libvorbis / libogg** | OGG | BSD ailesi `⚠️ VR` | Teyitli (isim) |
| P11 | **FDK-AAC** | AAC | **RED** (patentli) | RED |
| P12 | **BASS** | decode emsal | **RED** | RED |
| P13 | **libmpv / mpv** | video emsal | **RED** | RED |
| P14 | **libplacebo 7.350.0** | Video render (şartlı faz) | **UNKNOWN/LGPL? `⚠️ VR (ADR-0105 indeks — lisans satırı teyit edilmedi)`** | Kapalı (video fazı) |
| P15 | **libdsd** | DSD decode | **UNKNOWN** | RED (U3) |
| P16 | **VST3** | Plugin (K4) | Steinberg License | Kapalı |
| P17 | **Qt/ImGui/Tk (UI toolkit)** | Arayüz (§18.1 kararı) | **UNKNOWN — karara bağlı** (LGPL/commercial) | Kapalı (M4 gate) |
| P18 | OS SDK (WASAPI/CoreAudio/ALSA/PipeWire) | Backend (motor) | OS EULA | Not |
| P19 | ASIO SDK | Backend (U1) | Steinberg dual `⚠️` | RED (kapalı) |

### §14.2 RED/KAPALı Liste (Player)

1. FDK-AAC (kural 9) · 2. BASS · 3. mpv/libmpv (v1.1.1) · 4. libdsd (U3) · 5. VST3 (K4) · 6. ASIO (U1) · 7. UI toolkit lisansı bilinmeden **çalışma zamanı dağıtımı** (M4) · 8. GPL-only codec'ler (`--enable-gpl`) · 9. FxSound kaynak kopyası (K4).

### §14.3 LGPL Yükümlülükleri (dinamik bağlama + kaynak erişimi notu)

| Yükümlülük | Uygulama | Test |
|---|---|---|
| Lisans metni + copyright dağıtımı | LICENSE/NOTICE pakette | TC-PLR-200 (envanter) |
| Değiştirilen LGPL kaynak erişimi | Kaynak reposu/teklif `[ÖNERİ — LGPL §6 variant]` | Doküman |
| Dinamik bağlama (statik varsa relink) | Link raporu | `⚠️ LİSANS` etiketi → karar |
| FFmpeg build config tutarlılığı | `--enable-gpl` grep CI | TC-PLR-020 |

### §14.4 Attribution Şablonu (paket içi NOTICE)

```text
Neva Player — Üçüncü parti bileşenler (örnek satır düzeni; tam metinler LICENSE/ALTındadır):
  FFmpeg 9.0.2 — LGPL-2.1+ — https://ffmpeg.org  (build: --enable-gpl YOK)
  mbedTLS 9.13.0 — Apache-2.0
  soxr — LGPL-2.1 (dinamik bağlantı)
  dr_libs — MIT ailesi [⚠️ tam metin teyit]
  libopus — BSD-3-Clause
  ...
```

### §14.5 FFmpeg Vendor & Dağıtım Stratejisi `⚠️ VR`

| Seçenek | Not | Karar |
|---|---|---|
| (a) Vendor-artı-sistem | Sistem FFmpeg varsa kullan, yoksa vendor | **Önerü** `[ÖNERİ]` |
| (b) Statik vendor | LGPL relink yükümlülüğü | Ağırlaştırıcı |
| (c) Sistem bağımlı (dyn) | En temiz LGPL ama UX: kullanıcı kurulumu | Alternatif |

**Karar sahibi:** §18.9 (kardeş §14.5 ile birlikte tek ADR).

### §14.6 Player Paketleme & Lisans Çıktısı

| Paket | İçerik | Koşul |
|---|---|---|
| **Player Paketi A** | Motor + player + FFmpeg (LGPL) + tüm lisans metinleri | Motor Paket A/B (§14.4 kardeş) + P17 kararlı |
| **Geçiş paketi (M4 öncesi)** | CLI/headeless (UI yok) | Toolkit kararı bekler — demo/erken erişim `[ÖNERİ]` |
| NOTICE/LICENSE | §14.4 şablonu | G6 gate |

**Motor↔player uyumu:** L-numaraları kardeş PRD §14 ile aynı envanteri kullanır (çift sayım yok — biri ölçer, diğeri referans verir: kardeş §11.6 kuralı gibi `[ÖNERİ — lisans için de ortak envanter]`).

---

## §15 Yol Haritası (Player — Fazlar · Kapılar · Milestone)

> **Kaynak:** ADR-019 (per-OS paketleme — player fazları ayrık) · ADR-106 (radio fazı kuralları) · ADR-074 (katalog) · kardeş PRD §15 (motor fazları — hizalı) · ADR-105 (oturum geçmişi M1-M5).
> **Kural:** Her faz onaysız başlamaz (AGENTS §1) · UI fazı (M4) **UI toolkit ADR'si olmadan başlamaz** (NE-PLR-009).

### §15.1 Faz Genel Görünüm

| Faz | Ad | Kapsam | Çıkış | Kapı |
|---|---|---|---|---|
| **P0** | Temel | ABI handshake + durum makinesi + config + CLI smoke (motor baseline) | Çalışan iskelet | Motor G0/A0 (kardeş) |
| **P1** | Decode & Pipeline | §05.1 matrisi (WAV/FLAC/MP3/AAC/Opus/OGG) · ring · worker · kuyruk | Dosya oynatma | Door 5.1 (12 kombinasyon başlar) |
| **P2** | Gapless | Prefetch · format çiftleri · shuffle/repeat/seek | Kapı 5.12 yeşil | Door 5.12 |
| **P3** | Radyo fazı | ICY v1/v2 · HLS · validator (T0-T27) · backoff · katalog (ADR-074) | Canlı akış | ADR-106 Ş1-Ş7 + G1-G6 |
| **P4** | EQ & Ses final | 31/10/15/20 grid · preset · preamp · LUFS (şartlı) | EQ odası | ADR-106 kural 4-7 · Ş5 |
| **P5** | Ölçüm & Kapılar | 5.10 latency · SNR/THD · RAM · A/V (şartlı video) | Ölçüm raporları | Door 5.10/5.11 |
| **M4-UI** | Arayüz | UI toolkit (ADR zorunlu) · transport · tuner · cihaz · erişilebilirlik | UI tam | NE-PLR-009 gate + §18.1 |
| **P6** | Kararlılık | 24 sa stres · 1000 cycle · leak/TSan · UI spam | G4 benzeri | NE-PLR-163/189 |
| **V1** | Video fazı (opsiyonel) | libplacebo · MP4/MKV · A/V sync | Kapı 5.11 | VRAM `⚠️ VR` + §18.4 |
| **PAKET** | Dağıtım | Per-OS paket (ADR-019 §1.5) + lisans NOTICE | Release | G6 + motor G3 |

### §15.2 Aşama Detayları

#### P0 — Temel
- `NEVA_API_VERSION` handshake + durum makinesi (§04.5) + config atomik + CLI smoke.
- Kabul: TC-PLR-003/004/010/011 · kontrat testi (TC-PLR-199).

#### P1 — Decode & Pipeline
- Yerel decode önceliği (FLAC/WAV → dr_libs) + FFmpeg (MP3/AAC/OGG/Opus) · `decode → float32 ring` · worker non-RT.
- Kabul: TC-PLR-016/017/026/029 + kapı 5.1 kombinasyonları başlar (TC-PLR-036..050).
- Çıkış: Bit-perfect yolu ilk kez doğrulanır (Flat + eşit format).

#### P2 — Gapless
- Prefetch ≥1 sn · Xing/seektable/DoP + karışık kuyruk · eksik metadata (uydurma yok).
- Kabul: kapı 5.12 (TC-PLR-056..068) + ±5 ms ölçüm.

#### P3 — Radyo Fazı (ADR-106)
- ICY v1/v2 · HLS (kapılar) · **tek kapı validator (T0-T27)** · backoff 1s→30s ±%20 + 3 fails → ERROR · prebuffer 2-4 s · fade-in 20 ms · katalog ADR-074 + (şartlı) Radio Browser.
- Kabul: Ş1-Ş7 (TC-PLR-118/119/140/120) + G1-G6 gate + TC-PLR-096..117.
- Manuel: 10 canlı istasyon doğrulama (§11.5).

#### P4 — EQ & Ses Final
- 31-band ISO (varsayılan `⚠️ VR`) + 10/15/20 (teyitli) · ramp 5-20 ms · preset taşıma + CRUD + Flat=hash · preamp + true-peak limiter · (şartlı) LUFS/parametrik.
- Kabul: Ş5 (TC-PLR-140) + EqChain/Core EQ bütçesi (TC-PLR-126/127) + TC-PLR-121..144.

#### P5 — Ölçüm & Kapılar
- 5.10 loopback (<20 ms) · SNR >105 dB / THD+N <%0.005 · RAM ≤400 MB · (video) 5.11.
- Kabul: rapor arşivi + eşik karşılaştırması (TC-PLR-083/084/091/092/181).

#### M4-UI — Arayüz
- **Ön koşul:** §18.1 UI toolkit kararı (ADR) — yoksa bu faz BAŞLAMAZ.
- Transport/pozisyon/seek · radyo tuner + katalog UI · cihaz listesi + tercih · EQ odası (31 slider, klavye) · hata UX (§13 akışları) · EREG erişilebilirlik (keyboard-only + renk).
- Kabul: TC-PLR-137/161/151..160 + §11.5 manuel checklist.

#### P6 — Kararlılık
- 24 sa stres + 1000 cycle + UI spam + leak/TSan (NE-PLR-163/189/190/191).
- Kabul: 0 xrun · leak 0 · UI yanıt `[ÖNERİ <100 ms]`.

#### V1 — Video (opsiyonel faz)
- libplacebo 7.350.0 render · master clock = ses cihazı (A/V drift düzeltme) · VRAM merdiveni V0-V5 · MP4/MKV (+altyazı `⚠️ VR`).
- Kapı: 5.11 P95 ≤20 ms · VRAM ölçümleri (TC-PLR-076..081).
- Karar: §18.4 (video fazı onay ister).

#### PAKET — Per-OS Dağıtım (ADR-019 §1.5)
- `Neva-Player-Windows-x64` · `Neva-Player-Linux-x64` (per-OS kurulum dosyaları — §15.6 kitapçığı ile).
- Lisans: NOTICE (§14.4) + G6 gate + motor paketi referansı (P01).

### §15.3 Bağımlılık Grafiği (kritik yol)

```
P0 → P1 → P2 → P3 → P4 → P5 → M4-UI → P6 → (V1) → PAKET
             │        │        ↑
             │        │        └── §18.1 UI toolkit ADR  [BLOKAYICI]
             │        └── ADR-074 katalog şeması
             └── motor A0/A1 (kardeş)
P3 ← §18.6 (radio-katalog AI kararı) [opsiyonel eklenti]
V1 ← §18.4 onayı + VRAM ölçümü
PAKET ← G6 + motor G3
```

**Bloke eden açık maddeler:** §18.1 (toolkit — UI) · §18.4 (video fazı onayı) · §18.6 (katalog AI) · §18.7 (masaüstü mockup yokluğu — UI görsel gate).

### §15.4 Faz Checklist (kapanış)

1. §10 gereksinimler (bu faz): kabul kriteri + TC'ler yeşil (§11.7).
2. Kapılar: ilgili kapı raporu dosyalandı (5.1/5.12/5.13/P3 Ş'ler).
3. Güvenlik: tehdit tablosu (§12.1) yeni yüzey içeriyor mu + G1-G6 güncelse tekrar.
4. Hata UX: §13 akışlarıyla yeni hata durumu varsa eklendi mi.
5. Lisans: §14 matrisinde yeni satır (ör. yeni decode eklentisi).
6. Vault: ADR (gerekiyorsa) + log.md + commit (orkestratör).

### §15.5 Erken Teslim Stratejisi

- **Kısmi yayın (YAGNI):** İlk teslim = P0+P1+P2 + **CLI/headeless demo** (UI bekler) `[ÖNERİ — erken geri bildirim]` — AGENTS §17 Edge 7 ruhu.
- **Per-OS matris:** Windows paketi erken (mevcut OEM verisi), Linux aynı çekirdekten (ortak kod §15 M5 kuralı); macOS `⚠️ VR` (M6 motor bağımlı).
- **Geri alınabilir:** Bayraklar (video/radio-katalog/DSD) tek satırla kapanır (Edge 4); varsayılanlar güvenli (kaynak yalnız).
- **Ölçümden önce iddia yok (Edge 3):** "Gapless" / "bit-perfect" / "A/V sync" kelimeleri kapı raporu olmadan sürüm notuna yazılmaz.

### §15.6 Milestone ↔ Sürüm Eşlemesi (taslak)

| Sürüm | Faz tamamlanışı | Kapı |
|---|---|---|
| 0.9 (iç) | P0+P1 (dosya oynatma + ilk hash) | 5.1 başlangıç |
| 0.95 | P2+P3 (gapless + radyo) | 5.12 + Ş1-Ş7 |
| 1.0-rc | P4+P5 (EQ + ölçüm) | 5.10 + SNR/THD |
| 1.0 | M4-UI + P6 (UI + kararlılık) | §18.1 + manuel checklist |
| 1.x | V1 (video) · DSD (U3) · katalog AI (§18.6) | 5.11 + kararlar |

*(Tarih yok `[ÖNERİ]`; ritim: her faz 1 kayıt — ADR-107 §5 ruhu.)*

---

## §16 Risk Kayıt Defteri (Player)

> **Format:** R-NN · Açıklama · O×E (1-5, `[ÖNERİ]`) · Risk · Azaltma · Sahip · Durum.
> **Kaynaklar:** ADR-106 §4 (radio roadmap riskleri) · ADR-019 §6 (yalın-iş emsal "donma" olayı) · ADR-0105 `⚠️ VR` bolluğu · kardeş PRD §16.

### §16.1 Kayıt Tablosu

| ID | Risk | O×E | Risk | Azaltma | Sahip | Durum |
|---|---|---|---|---|---|---|
| **R-P01** | **UI toolkit kararı verilmez** (§18.1) → M4/UI bloke, release sarkar | 4×5 | **20** | M4 öncesi zorunlu ADR · P0-P3 (backend/CLI) M4'süz ilerler · geçiş paketi CLI `[ÖNERİ]` | Ürün | **KRİTİK AÇIK** |
| **R-P02** | **ADR-0105 içerik surrogatesi** (dosya diskte YOK — §00.3 D2) → v1.0.x detayları eksik | 5×4 | **20** | `⚠️ VR` etiketli madde başına doğrulama + Vault Steward'ın ADR-0105'i iadesi (kardeş NE-ENG-182); bu PRD etiketle çalışır | Vault | **KRİTİK AÇIK** |
| **R-P03** | **Masadaüstü UI mockup yok** (§18.7) → mockup gate kuralı (AGENTS §13) UI koduna takılır | 4×4 | 16 | `.ai/ui-design/` şablonu (§13.5 Guardrail #16) ile süreç başlatılır; M4 girişinde PNG şart | Tasarım | **AÇIK** |
| **R-P04** | **Video fazı VRAM iddiası ölçülemez** (`⚠️ §7.5`) → V1 fazı spekülasyonla başlar | 4×3 | 12 | GTX 1660 bench tekrarı (TC-PLR-081) + açık kart (1060-3GB %19,5) + V1 onay gate (§18.4) | Ölçüm | Açık (V1) |
| **R-P05** | **FFmpeg vendor stratejisi belirsiz** (§14.5) → LGPL uyumu release'e sarkar | 3×4 | 12 | §18.9 tek karar (kardeş §14.5 ile birleşik ADR) + link raporu G6 | Lisans | Açık (G6) |
| **R-P06** | **Validator bypass (tek kapı kuralı ihlali)** — bir yerde ikinci URL yolu açılırsa SSRF doğar | 3×5 | 15 | Statik kapı: kodda ikinci validator/çağrı yokluğu grep (TC-PLR-104/215) + tehdit tablosu revizyonu | Güvenlik | Sürekli |
| **R-P07** | **Radyo canlı testlerinin deterministik olmaması** → kapılar sahte | 4×3 | 12 | Mock istasyon altyapısı (§11.3) zorunlu; canlı test yalnız manuel checklist'te | QA | Açık (P3) |
| **R-P08** | **100 istasyon stresi/paralel tuning** (Ş2/Ş3) bellek/süreç şişirir | 3×4 | 12 | Kuyruk politikası `⚠️ VR` (önerü: ≤2 aktif + kullanıcı sinyali bekleyenleri iptal) + Ş2/Ş3 kapı | Backend | Açık (P3) |
| **R-P09** | **15/20-band frekans listeleri teyit edilemez** (§1.3/§1.4) → EQ fazında hatalı grid | 4×3 | 12 | TEYİTLİ KURAL: teyit gelene kadar 15/20 **gizli/kilitli** `[ÖNERİ]`; 31+10 teyitli | EQ | Açık (P4) |
| **R-P10** | **ADB/FxSound emsal kaynak koduna yapışma** (K4) | 2×5 | 10 | KOPYALAMA YASAK + inceleme; yalnız mimari referans | Güvenlik/Mimari | Sürekli |
| **R-P11** | **Eksik fonksiyonlar** (`set_mode/reset_gain/set_loop_mode` — kardeş §05.5) unutulur | 3×4 | 12 | Eksik fonksiyon defteri (Z/n0/7) — kardeş NE-PLR üzerinden | Backend | Kardeş |
| **R-P12** | **Yarış: UI↔engine state kayması** (emsal: donma olayı — ADR-019 §6 "çalışırken aniden dondu") | 3×5 | 15 | TSan + invariant (TC-PLR-165) + çoklu-thread spam (TC-PLR-191) + uzun koşu (24 sa) | QA | Sürekli |
| **R-P13** | **Bütçe ihlali** (RAM 400 / CPU %5 / A/V P95 20 / gapless ±5) | 3×4 | 12 | Baseline + %10 etiket (NE-PLR-195) + kapı raporları zorunlu | Performans | Açık (P5/P6) |
| **R-P14** | **Yerelleştirme kapsamı** (§18.2) — TR/EN kararı verilmezse metin katmanı yarım kalır | 3×2 | 6 | Karar etiketi + kapsam dışı etiketi korunur (NE-PLR-014/194) | Ürün | Açık |
| **R-P15** | **HLS/DASH genişlemesi** kapıları geçerken protokol yüzeyi büyür (SSRF/doS) | 3×4 | 12 | Kapı 5.13 + HLS 50 MB/1000/derinlik + whitelist (Ş8) | Güvenlik | Açık (P3) |
| **R-P16** | **MCP/remote/web uzak yüzeyi** (U4/U5) yetkilendirme olmadan açılırsa | 2×5 | 10 | Karar kapıları kapalı; açılışta ADR (auth modeli `[ÖNERİ: localhost+token]`) | Ürün/Güvenlik | Açık (M6) |
| **R-P17** | **Plugin/loader kapsam şişmesi** (player çekirdeğine plugin gelirse) | 3×4 | 12 | NE-PLR-211 kapsam kararı (loader YOK) + statik grep | Mimari | Sürekli |
| **R-P18** | **Dual decode (FLAC yerel + FFmpeg) sürücü sürükler** | 3×3 | 9 | Decode matrisi CI (TC-PLR-016) + yerel öncelik kuralı (NE-PLR-017) | Backend | Açık (P1) |
| **R-P19** | **Ölçüm düzeneği yok** → 5.10/SNR/THD kapıları tiyatro (Edge 3 ihlali) | 4×4 | 16 | M5 milestone zorunlu · kapı olmadan iddia dili yasak | QA | Açık (P5) |
| **R-P20** | **Katalog verisi kaybı** (bozuk DB/preset — FM-06) | 3×3 | 9 | Atomik write + yedek `[ÖNERİ]` + migrate (ADR-074) | Backend | Açık (P3) |
| **R-P21** | **Kararlılık/uzun test altyapısı yok** (24 sa + UI spam otomasyonu) | 4×3 | 12 | Gece CI schedule (P6) + rapor şablonu (NE-PLR-196) | QA | Açık (P6) |
| **R-P22** | **Per-OS paket sürprizleri** (Windows vs Linux davranışı — ADR-019 §1.5) | 3×3 | 9 | Ortak çekirdek kuralı (NE-PLR-001) + her OS'ta smoke (P0-P2) | DevOps | Açık |
| **R-P23** | **Metin/UI etkileşiminde XSS** (başlık → bildirim/UI — kural 7 ihlali) | 3×5 | 15 | Escape tek katman + statik grep (TC-PLR-170) + bildirim sanitizasyonu | Güvenlik | Sürekli |
| **R-P24** | **A/V sync metodu yanlış seçilir** (5.11 ölçümü güvenilmez) | 3×3 | 9 | Düzeneği kalibrasyonu (kardeş TC-ENG-137 ruhu) + iki yöntem `[ÖNERİ]` | Ölçüm | Açık (V1) |
| **R-P25** | **Kullanıcı dostu mesaj yerine teknik kod** (ADR-106 kural 8 ihlali) | 3×3 | 9 | §13.1 şablonları + UX review (manuel checklist madde) | Ürün | Açık (M4) |

### §16.2 Öncelik Sıralaması (Risk ≥ 15)

1. **R-P01 (20)** — UI toolkit kararı: tek en büyük blokaj (kritik yol §15.3).
2. **R-P02 (20)** — ADR-0105 surrogate: en geniş `⚠️ VR` kaynağı; Vault Steward iadesi + madde madde doğrulama.
3. **R-P03 (16)** — Mockup yokluğu: Guardrail #16 gate'i (şablon + PNG) M4 öncesi.
4. **R-P19 (16)** — Ölçüm düzeneği: kapılar olmadan iddia yasağı.
5. **R-P06/R-P12/R-P23 (15'ler)** — validator bypass · state race · XSS: statik kapı + TSan + escape tek katman.

### §16.3 Risk ↔ Gereksinim ↔ Test (kısa)

| Risk | Gereksinim(ler) | Test |
|---|---|---|
| R-P01 | NE-PLR-009/161/179 | §15.4 checklist · M4 gate |
| R-P02 | Tüm `⚠️ VR` satırları (§00.3) | §18.10 madde madde denetim |
| R-P04 | NE-PLR-079/080/081/186 | TC-PLR-079/080/081 |
| R-P05/R-P13 | NE-PLR-019/020/180/181/182 | TC-PLR-019/020/180/181/182 + G6 |
| R-P06 | NE-PLR-104/167/168/174/215 | TC-PLR-104/167/168/174/215 |
| R-P07 | NE-PLR-118/119/192 | Ş1-Ş7 |
| R-P09 | NE-PLR-124/125/147 | TC-PLR-124/125/147 |
| R-P12 | NE-PLR-165/191 | TC-PLR-165/191 |
| R-P19 | NE-PLR-083/084/091/092 | kapı 5.10 + SNR/THD |
| R-P23 | NE-PLR-101/102/170/210 | TC-PLR-101/170 |

---

## §17 İzlenebilirlik (Player — Gereksinim → Kaynak → Test → Faz)

> **Amaç:** 215 gereksinimin kaynağını (ADR/disk/bu PRD) ve testini tek yerde göstermek.
> **Okuma kuralı:** `ADR-xxx` bağlayıcı · `disk` = okunan kanıt · `bu PRD §x` = türetme · `YOK/[ÖNERİ]` = öneridir.

### §17.1 Grup Bazlı Matris

| Grup | Kapsam | ID aralığı | Başlıca kaynak | Test | Faz |
|---|---|---|---|---|---|
| A — Platform & paketleme | Ortak çekirdek, C ABI, durum makinesi, çevrimdışı | 001-015 | ADR-019 §2.2 · disk `neva_session.h` · ADR-019 §1.4 | TC-PLR-001..015 | P0 |
| B — Medya pipeline & decode | Matris, yerel öncelik, queue, metadata | 016-035 | ADR-0105 indeks §§3-4 ⚠️ · ADR-106 §1.3 (çözüm M3) | TC-PLR-016..035 | P1 |
| C — Bit-perfect & negosyasyon | Kapı 5.1, N1, rozet, shared | 036-055 | ADR-0105 kapı 5.1 ⚠️ · ADR-106 §1.3 · kardeş §05.2 | TC-PLR-036..055 | P1/P5 |
| D — Gapless (AK-5) | MP3/FLAC/Opus/DSD + queue mantığı | 056-075 | ADR-0105 AK-5 ⚠️ · kapı 5.12 | TC-PLR-056..075 | P2 |
| E — Video (M6) | libplacebo, master clock, VRAM, RAM | 076-095 | ADR-0105 §§2.8-2.10/§7 ⚠️ · ADR-106 kural 11 · VISION | TC-PLR-076..095 | V1 |
| F — İnternet radyo | ICY v1/v2, HLS, backoff, katalog, Ş1-Ş7 | 096-120 | **ADR-106 (tam)** · ADR-074 · disk `icy_parser.h` | TC-PLR-096..120 | P3 |
| G — EQ & ses (selectable band) | Grid'ler, ramp, preset, preamp, LUFS | 121-145 | **ADR-106 §2.2b kural 1-7** · disk `eq_grids.h/eq_chain.h` | TC-PLR-121..145 | P4 |
| H — Durum & UX (Sınıf X) | ERROR akışları, mesajlar, kapanış | 146-165 | ADR-106 kural 8 · kardeş §12 · ADR-004/ADR-018 | TC-PLR-146..165 | M4-UI |
| I — Güvenlik (OWASP) | SSRF, escape, XXE, HLS kapıları, SQL | 166-180 | ADR-106 §5 (S1-S8/G1-G6) · AGENTS §5 | TC-PLR-166..180 | P3/PAKET |
| J — Bütçeler & test kapıları | 5.1/5.10-5.13, stress, a11y, regresyon | 181-200 | ADR-0105 kapıları ⚠️ · ADR-106 §5.2 · S3 §6.1 | TC-PLR-181..200 | P5/P6 |
| K — FFI/MCP & dış API | ABI genişleme, 25 dil, remote/web/mcp, envanter | 201-215 | ADR-0105 v1.0.2 §3/v1.4.0 ⚠️ · ADR-107 K2/§8.6 · Vision | TC-PLR-201..215 | M6 |

### §17.2 Kaynak Dökümü (okunan disk/ADR kanıtları)

| Kaynak | Tür | Kullanıldığı yerler |
|---|---|---|
| `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md` | Accepted ADR (ana) | §00-§02 · Grup F/G/I · kapılar §11 · §12 |
| `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` | Accepted ADR | §03-§04 (durum makinesi §1.4, ~5/5/5/5/5 §2.2, zincir §1.1) · Grup A · §15 |
| `.ai/.decisions/index.md:152` | İndeks (ADR-0105 dosyasız) | Tüm `⚠️ VR` etiketleri · §00.3 D2 · §18.10 · §7.5 |
| `--/neva-engine/icy_parser.h` + `test_icy_parser.cpp` | Disk kod | Grup F (ICY limitleri) |
| `--/neva-engine/eq_grids.h` + `test_eq_grids.cpp` · `eq_chain.h` · `biquad.h` | Disk kod | Grup G (31/10-band teyitli) |
| `--/neva-engine/neva_session.h/cpp` (9 fonksiyon + 5 kod + zincir + registry) | Disk kod | Grup A/C · §04.4/§05 |
| ADR-074 (radyo katalog şeması) — indeks | ADR | Grup F (NE-PLR-113/117/176) |
| `VISION.md` (V-1/V-2/V-6/V-8) | Vizyon | §08 (SNR/THD/float32/5.1) · Grup E |
| `.ai/CLAUDE.md` · kök `AGENTS.md` | Kural | §12 (OWASP §5) · §16 (Edge) · Guardrail #16 (§18.7) |

### §17.3 Test ↔ Kapı Eşlemesi

| Test grubu | Sayı | Kapı |
|---|---|---|
| TC-PLR-001..035 (A/B) | 35 | ABI · decode matrisi · pipeline |
| TC-PLR-036..055 (C) | 20 | **5.1 (hash + guard)** |
| TC-PLR-056..075 (D) | 20 | **5.12 (gapless)** |
| TC-PLR-076..095 (E) | 20 | **5.11 + VRAM/RAM** |
| TC-PLR-096..120 (F) | 25 | **Ş1-Ş7 + G1-G6** |
| TC-PLR-121..145 (G) | 25 | **Ş5 + EqChain bütçesi** |
| TC-PLR-146..165 (H) | 20 | UX checklist + kontrat |
| TC-PLR-166..180 (I) | 15 | **G1-G6 güvenlik** |
| TC-PLR-181..200 (J) | 20 | **5.10/5.13 + stres + leak/TSan** |
| TC-PLR-201..215 (K) | 15 | ABI minor + şartlı (U4/U5) |
| **Toplam** | **215** | (her gereksinim ≥1 test) |

### §17.4 Çapraz Doküman Bağlantıları (bu PRD ↔ kardeş PRD)

| Konu | Bu PRD (player) | Kardeş (motor) |
|---|---|---|
| Hash hattı 12 kombinasyon + guard | §11.2 koşulları + rozet (036/040/087/145) | §11.2 hat sahibi (126/127) |
| Loopback düzeneği (latency/SNR/THD) | §11.4 (referans) | §11.3-A/B (sahip) |
| Fallback zincir 7 basamak | §04.4 kullanır (NE-PLR-046/154) | §04.4 + NE-ENG-008 (sahip) |
| Error kodu 0-4 | §13.1 mesaj (sahip: UX) | §12.3 tablo (sahip: kod) |
| E1-E10 event API | §05.2 üzerinden dinler (NE-PLR-005/199) | §05.2 üretici (NE-ENG-047) |
| URL validator tek kapı | §12.2 T0-T27 (sahip) | Ağ yok (SE-03) |
| Lisans envanteri | §14.1 P-numaraları | §14.1 L-numaraları (ortak) |
| RT yasakları / xrun | §07.5 kullanır (NE-PLR-109/110) | §07 yasak listesi (sahip) |
| Gapless vektör altyapısı | §11.2 (format vektörleri sahibi) | E2/E5 API (048/139) |
| UI toolkit · mockup | §18.1 · §18.7 (sahip) | — |

### §17.5 Kapsam Dışı ↔ İlgili

| Kapsam dışı | İlgili | Gerekçe |
|---|---|---|
| Motor iç implementasyon (ring/biquad/backends) | kardeş §04-§08 | K2 apps/ + kardeş sınırı |
| Yerel decode kütüphanesi kodu (dr_libs bağlama detayı) | NE-PLR-017 | ADR-0106 çözüm M3 (karar ADR) |
| DASH implementasyonu | NE-PLR-098 (şartlı) | ADR-106 §2.2a faz 2 |
| Native DSD (DoP hariç) | NE-PLR-044 `⚠️ VR` | ADR-0105 AK-5 `⚠️` karar |
| Plugin loader | NE-PLR-211 | Kapsam kararı (K4 bağlanması) |
| `git commit` (subagent) | §20.2 | AGENTS §16.5 |

### §17.6 Izlenebilirlik Bütünlük Denetimi (PR adımı)

1. Her `NE-PLR-NNN` ≥1 `TC-PLR-NNN` içeriyor mu? (eksik = review notu).
2. Her TC'nin gereksinim referansı gerçek ID mi? (kırık = FAIL).
3. `⚠️ VR` etiketi sayısı ↓ (§18.10 defteri ile karşılaştırmalı).
4. §12.2 tehdit tablosu ↔ kod ağ çağrıları tutarlı (NE-PLR-215 envanter diff).
5. §02.3 kapsam dışı ihlali yeni kod var mı (R-P17 statik kapı).

---

## §18 Açık Maddeler & Karar Kapıları (Player)

> **Amaç:** Bu PRD'nin kaynağı olmayan her maddeyi karar formuyla listelemek (AGENTS §3 — `UNKNOWN`/`⚠️ VR`/[VARSAYIM]/[ÖNERİ] işaretli).
> **Kural:** Karar verilmeden ilgili bayrak/faz **başlamaz**; varsayılan her zaman **güvenli taraftır** (kapalı/kısıtlı/etiketli).

### §18.0 Karar Kapısı Çerçevesi

Her açık madde dört alan taşır: **Q** (soru) · **Kapsam** (bloke edilen) · **Seçenekler** (artı/eksi) · **Varsayılan** (güvenli) + **Karar sahibi** + **Durum**.
Onay zinciri: karar → ADR (`.ai/.decisions/draft/` → `accepted`) → §10 gereksinimi güncelle → §17 denetim → vault-sync + log.md.

### §18.1 UI Toolkit Seçimi (KRİTİK — M4 Bloklayıcı)

- **Q:** Neva Player arayüzü hangi araç setiyle yazılır: Qt, ImGui, Tk, Webview (Electron benzeri), yoksa platform-native (WinUI/Cocoa/GTK)?
- **Kaynak durumu:** Vault'ta masaüstü UI toolkit kararı YOK `⚠️ VERIFICATION REQUIRED` · `.ai/ui-design/**` masaüstü mockup'ı yok (§18.7) · kardeş M4 bağımlılığı.
- **Kapsam:** NE-PLR-009 (UI toolkit gate) · M4-UI fazı (§15.2) · NE-PLR-137/151-161 (UX) · R-P01 (risk 20 — KRİTİK).
- **Seçenekler:**
  - **(a) Qt (LGPL/commercial):** Artı: olgun, cross-platform, WCAG erişilebilirlik API'leri güçlü. Eksi: LGPL dinamik bağlama + boyut (~100 MB+) `[VARSAYIM — boyut ölçülmedi]`.
  - **(b) Dear ImGui:** Artı: hızlı, audio-uygulamalarına özgü, C++ ile doğal. Eksi: erişilebilirlik (WCAG) ve native metin girişi zayıf `⚠️ VR (erken sürüm)` — EREG/Edge 8 riski.
  - **(c) Platform-native:** Artı: en iyi a11y + görünüm. Eksi: 3 ayrı UI kodu (K2 apps/ kuralı ihlal riski — R-23).
  - **(d) Webview (WebView2/WebKit):** Artı: HTML/CSS/ES tasarım sistemi (CoreMusic'i yansıtır). Eksi: RAM/VRAM artışı + paket şişmesi + ayrı süreç riski `⚠️ VR`.
  - **(e) Tk/Python:** Artı: MVP hızlı. Eksi: profesyonel görünüm + yüksek yenileme (EQ 31 slider) `⚠️ VR`.
- **Varsayılan:** **Karar YOK → M4 BAŞLAMAZ**; P0-P3 (backend/CLI) ilerler; geçiş: CLI/headeless demo `[ÖNERİ]`.
- **Karar sahibi:** Ürün sahibi + ui-designer + accessibility-tester (Expert+Senior+Junior — AGENTS §6.1) → ADR.
- **Kapanış ölçütü:** ADR `accepted` + ilk mockup (§18.7) + a11y spike raporu (klavye/focus testi).

### §18.2 Yerelleştirme Kapsamı (TR/EN + 25 Dil İddiası)

- **Q:** İlk sürümde kaç dil, çeviri altyapısı nasıl (dizgi/parametre)?
- **Kaynak durumu:** VISION "25 dilde arayüz" `⚠️ VR (ADR-0105 dosyasız)` · kardeş U2 ile ortak · bu PRD NE-PLR-014/194/203.
- **Kapsam:** NE-PLR-014 (kapsam dışı etiketi) · NE-PLR-194 (test) · NE-PLR-203 (tanım) · R-P14.
- **Seçenekler:**
  - **(a) İlk sürüm TR+EN:** Artı: hızlı + iki ana pazar. Eksi: "25 dil" iddiası ertelenir.
  - **(b) Tam 25 dil:** Artı: vizyon karşılanır. Eksi: çeviri bütçesi + QA `[VARSAYIM — bütçe bilinmiyor]`.
  - **(c) Yalnız EN (kaynak dil):** Artı: en az iş. Eksi: TR kullanıcı (Ana kullanıcı TR — ADR-097 K5).
- **Varsayılan:** **(c)+(a) hibrit:** Kaynak dil EN, TR çeviri zorunlu `[ÖNERİ — CoreMusic TR odaklı]`; "25 dil" `UNKNOWN` olarak kalır (ölçülmez).
- **Karar sahibi:** Ürün → kardeş PRD §18.2 ile tek karar.
- **Kapanış ölçütü:** Dil listesi kararı + çeviri dosyası şeması + NE-PLR-203 kapanışı.

### §18.3 Downmix Matrisi (Çok Kanallı → Stereo/Stereo → Çok Kanallı)

- **Q:** 5.1/7.1/8.1 kaynaklar stereo cihaza, stereo kaynaklar çok kanallı cihaza nasıl karıştırılır (standart matris)?
- **Kaynak durumu:** ADR-0105 (kanal matrisi detayı dosyada YOK) `⚠️ VR` · VISION V-8 (5.1/8.1) · kardeş §18.3 (motor) ile ortak.
- **Kapsam:** NE-PLR-018/019 (kanal) · kardeş NE-ENG-078 (downmix) · V1/P5 ölçüm.
- **Seçenekler:**
  - **(a) ITU-R BS.775 / ATSC A/52 standart matrisi:** Artı: endüstri standardı. Eksi: referans metin vault'ta yok — doğrulama gerekir `⚠️ VR`.
  - **(b) Basit L=R (yanlış):** Eksi: standarda aykırı — **önerilmez**.
  - **(c) Yalnız 1:1 kanal eşlemesi + kullanıcı uyarısı:** Artı: şeffaf. Eksi: uyumsuz cihazlarda ses kaybı.
- **Varsayılan:** **(a) iddiası `⚠️ VR` işaretli** — matris katsayıları doğrulanana kadar yalnız 1:1 eşleme + uyarı `[ÖNERİ]`.
- **Karar sahibi:** DSP (embedded-engineer) + mimari → ADR (kardeş §18.3 ile tek).
- **Kapanış ölçütü:** Matris tablosu (katsayılar) + test (kardeş TC-ENG-078 genişletilir).

### §18.4 Video Fazı Onayı (V1 — Opsiyonel)

- **Q:** Video oynatma (MP4/MKV, libplacebo) bu ürünün kapsamına giriyor mu, ne zaman?
- **Kaynak durumu:** ADR-0105 §7 `⚠️ VR` (video fazı detayı) · ADR-106 kural 11 (video ≠ ses motoru) · kardeş §15 M6 · NE-PLR-076..095.
- **Kapsam:** Grup E (20 gereksinim) · kapı 5.11 · VRAM merdiveni V0-V5 · R-P04 (risk 12).
- **Seçenekler:**
  - **(a) V1 fazı olarak ertele (önerü):** Ses 1.0'dan sonra ayrı faz. Artı: odak (YAGNI — Edge 7). Eksi: vizyon maddesi gecikir.
  - **(b) 1.0'a dahil:** Artı: erken değer. Eksi: kapsam şişmesi (R-23 motor kuralı ihlali riski) + VRAM ölçümü yok.
  - **(c) Kapsam dışı bırak:** Artı: net. Eksi: ADR-0105 `⚠️` maddesi askıda kalır.
- **Varsayılan:** **(a)** — V1 = koşullu faz; VRAM eşikleri `⚠️ VR` (§7.5).
- **Karar sahibi:** Ürün → ADR (P5 tamamlanınca).
- **Kapanış ölçütü:** Faz kararı + VRAM ölçüm raporu (TC-PLR-081) + kapı 5.11 kapsamı.

### §18.5 Uzak Yüzey: Remote / Web / MCP (U4-U5)

- **Q:** Uzaktan kontrol (neva-remote · web UI · neva-mcp) hangi sırayla, hangi yetki modeliyle açılır?
- **Kaynak durumu:** ADR-0105 v1.4.0 `⚠️` · ADR-107 §8.6 (U4) · kardeş U4/U5 · NE-PLR-204/205/206.
- **Kapsam:** Grup K (201-215) · R-P16 (risk 10) · §12.1 A01/A07.
- **Seçenekler:**
  - **(a) Sıralı: remote (localhost) → web (şifreli) → mcp:** Artı: risk kademeli. Eksi: süre.
  - **(b) Hepsini ertelemek:** Artı: yüzey küçülür. Eksi: vizyon.
  - **(c) Tek "uzak köprü" servisi (hepsini besler):** Artı: tek güvenlik kapısı (S3 ruhu). Eksi: tasarım işi.
- **Varsayılan:** **(b)** — tümü `OFF`; yalnız C ABI bridge (NE-ENG-109) hazır.
- **Karar sahibi:** Ürün + security-engineer → ADR (auth modeli: localhost + token `[ÖNERİ]` — UPnify emsal, ADR-105 v1.4.0 `⚠️`).
- **Kapanış ölçütü:** ADR + şifre akışı + açık port denetimi (TC-PLR-179/205/206).

### §18.6 Katalog AI / Radio Browser Entegrasyonu (Opsiyonel)

- **Q:** İstasyon keşfi (Radio Browser / AI öneri) hangi veri kaynağıyla ve hangi fazda?
- **Kaynak durumu:** ADR-074 (katalog şeması — dosya indekste) `⚠️` · ADR-106 §2.2a (faz 2 / uç akış) · NE-PLR-113/117.
- **Kapsam:** Grup F (P3 fazı) · NE-PLR-013 (ağ yalnız kullanıcı tetikli) · R-25/NE-PLR-213.
- **Seçenekler:**
  - **(a) Yerel M3U/RSS + isteğe bağlı Radio Browser:** Artı: çevrimdışı temel + keşif açık. **Önerü.**
  - **(b) AI öneri (LLM/embedding):** Artı: kişiselleştirme. Eksi: gizlilik + ağ + maliyet `⚠️ VR`.
  - **(c) Yalnız yerel dosya taraması:** Artı: en basit. Eksi: keşif yok.
- **Varsayılan:** **(a)** — Radio Browser `OFF` (açılışta ADR + gizlilik notu).
- **Karar sahibi:** Ürün → ADR (P3 girişinde).

### §18.7 Masaüstü Mockup Yokluğu (Mockup Gate — Guardrail #16)

- **Q:** `.ai/ui-design/**` altında masaüstü player ekranları (main/tuner/EQ/device) ne zaman ve hangi kalıpla üretilecek?
- **Kaynak durumu:** AGENTS §13 "Mockup Before Frontend" + Guardrail #16 (şablon zorunlu) · bu PRD §13.3/§15.2 M4 · R-P03 (risk 16).
- **Kapsam:** M4-UI (UI kodu yasağı mockupsız) · NE-PLR-137 · §11.5 manuel checklist.
- **Seçenekler:**
  - **(a) M4 girişinde PNG + Kalıp A-D şablonu (önerü):** `.ai/.templates/ui-design/{reference,flow,prompt,screen-spec}-template` kullanılır. Artı: süreç uyumu. Eksi: M4 öncesi tasarım işi.
  - **(b) Kod ile birlikte tasarım:** Eksi: Guardrail #16 ihlali — **yasak**.
- **Varsayılan:** **(a)** — M4 kapısı: mockup yoksa UI kodu yazılmaz.
- **Karar sahibi:** ui-designer (Senior) → `.ai/ui-design/screens/` + index.
- **Kapanış ölçütü:** ≥3 ekran PNG (main · tuner · EQ) + screen-spec dosyaları.

### §18.8 Preset/Config Formatı & Depo Konumu

- **Q:** EQ preset + ayarlar hangi biçimde (JSON/SQLite/ikisi) ve nerede saklanır (roaming/AppData)?
- **Kaynak durumu:** ADR-074 (katalog = SQLite indekste `⚠️`) · ADR-091 (config kalıcılığı — indeks) · kardeş §06 (config) · bu PRD §06 (Preset CRU).
- **Kapsam:** NE-PLR-146 (atomik write) · NE-PLR-175 (parser) · TC-PLR-176 (SQL) · R-P20.
- **Seçenekler:**
  - **(a) Preset JSON + Katalog SQLite (önerü):** Basit ayarlar JSON, büyük veri SQLite. **Önerü.**
  - **(b) Her şey SQLite:** Artı: tek teknoloji. Eksi: küçük dosyalar için ağır `[VARSAYIM]`.
  - **(c) Her şey JSON:** Artı: basit. Eksi: 1000+ istasyon sorgu performansı.
- **Varsayılan:** **(a)** `[ÖNERİ]` — depo yolu platform varsayılanı (roaming), ADR ile teyit.
- **Karar sahibi:** data-engineer + mimari → ADR (P3 girişinde, ADR-074 ile birlikte).
- **Kapanış ölçütü:** Şema dosyası + migrate stratejisi + atomik write testi.

### §18.9 FFmpeg Vendor Stratejisi (§14.5 — kardeş §18.7 ile tek ADR)

- **Q:** FFmpeg statik vendor mı, dinamik sistem mi, vendor-artı-sistem mi?
- **Kaynak durumu:** ADR-0105 `⚠️ vendor stratejisi belirsiz` · kardeş §14.5/§18.7.
- **Kapsam:** §14.5 · NE-PLR-019/020 · G6 · R-P05 (risk 12).
- **Seçenekler:** §14.5 tablosu (a/b/c).
- **Varsayılan:** **(a) vendor-artı-sistem** `[ÖNERİ]` + dinamik (LGPL temiz).
- **Karar sahibi:** Lisans + build → ortak ADR (kardeş U7 ile).
- **Kapanış ölçütü:** Link raporu + NOTICE + `--enable-gpl` grep CI yeşil.

### §18.10 Uçak Modu / Çevrimdışı Tanımı (Edge 9)

- **Q:** "Çevrimdışı çalışır" tam olarak neyi kapsar (radyo hariç mi, güncelleme mi, katalog senkronu mu)?
- **Kaynak durumu:** AGENTS §17 Edge 9 (uçak modu) · NE-PLR-013 (ağ yalnız kullanıcı tetikli) · ADR-106 (radyo = ağ).
- **Kapsam:** NE-PLR-013 · NE-PLR-112 (katalog) · NE-PLR-213 (gizli ağ çağrısı yok) · R-25/NE-PLR-213.
- **Seçenekler:**
  - **(a) Temel oynatma + katalog çevrimdışı (önerü):** Dosya oynatma, preset, ayarlar ağsız çalışır; radyo/keşif = kullanıcı tetikli ve başarısızlık zararsız.
  - **(b) Tam çevrimdışı iddiası:** Eksi: radyo imkânsız — yanlış iddia.
- **Varsayılan:** **(a)** — iddia yalnız (a) kapsamıyla yazılır.
- **Karar sahibi:** Ürün → doküman/ADR (gerekirse).
- **Kapanış ölçütü:** Uçak modu iş akışı testi (TC-PLR-213 genişletilir).

### §18.11 Bildirim & Medya Tuşları Kapsamı (Opsiyonel)

- **Q:** OS medya tuşları (tuş takımı/F13), masaüstü bildirimleri ve widget/kilit ekranı desteği var mı?
- **Kaynak durumu:** Vault'ta karar YOK `⚠️ VERIFICATION REQUIRED` · NE-PLR-209/210 `[ÖNERİ]`.
- **Kapsam:** NE-PLR-209 (hotkey) · NE-PLR-210 (bildirim sanitizasyonu) · E1 eventleri.
- **Seçenekler:**
  - **(a) OS medya tuşları + basit bildirim (önerü):** Standart beklenti. Eksi: platform API işi.
  - **(b) Kapsam dışı:** Artı: basit. Eksi: kullanıcı beklentisi kırılır `⚠️ VR (kullanıcı araştırması yok)`.
- **Varsayılan:** **(a) `[ÖNERİ]`** — bildirim varsayılan kapalı + sanitizasyon (TC-PLR-210).
- **Karar sahibi:** Ürün + ui-designer → karar (M4).

### §18.12 Windows Kurulum/Portabilite Modeli

- **Q:** Windows paketi kurulumlu mu (installer), taşınabilir mi (portable/zip), yoksa ikisi mi?
- **Kaynak durumu:** ADR-019 §1.5 (per-OS paketleme) · kardeş §14.4 (paket A/B) · bu PRD §15 PAKET · `⚠️ VR (kurulum modeli belirsiz)`.
- **Kapsam:** NE-PLR-001 (per-OS paket) · §15.6 kitapçığı · R-P22.
- **Seçenekler:**
  - **(a) Kurulumlu + taşınabilir (ikisi — önerü):** Kullanıcı seçimi. Eksi: 2 artefakt.
  - **(b) Yalnız taşınabilir:** Artı: temiz uninstall yok. Eksi: kısayol/başlangıç kaydı zayıf `⚠️ VR`.
  - **(c) Yalnız kurulum:** Eksi: "taşınabilir" bekleyen kullanıcı.
- **Varsayılan:** **(a) `[ÖNERİ]`** — ilk sürüm portable + kurulum sonra (YAGNI).
- **Karar sahibi:** DevOps + ürün → paket dokümanı (ADR-019 §1.5 eki).

### §18.13 Açık Madde Kayıt Defteri (özet)

| ID | Konu | Öncelik | Bloke ettiği | Varsayılan | Durum |
|---|---|---|---|---|---|
| §18.1 | UI toolkit | **P0** | M4-UI · R-P01 (20) | M4 BAŞLAMAZ + CLI demo | **KRİTİK AÇIK** |
| §18.7 | Masaüstü mockup | **P0** | UI kodu (Guardrail #16) | Şablon A-D + PNG | **AÇIK** |
| §18.4 | Video fazı onayı | P1 | Grup E (V1) · kapı 5.11 | Ertelenmiş faz | AÇIK |
| §18.2 | Yerelleştirme / 25 dil | P1 | NE-PLR-014/194/203 | TR+EN `[ÖNERİ]`, "25" UNKNOWN | AÇIK |
| §18.3 | Downmix matrisi | P1 | NE-PLR-018/019 · V-8 | 1:1 eşleme + uyarı | AÇIK |
| §18.5 | Uzak yüzey (U4/U5) | P2 | Grup K (204-206) | Kapalı (OFF) | AÇIK |
| §18.6 | Katalog AI / Radio Browser | P2 | NE-PLR-113/117 | Yerel M3U + OFF | AÇIK |
| §18.8 | Preset/depo formatı | P1 | NE-PLR-146/175/176 | JSON+SQLite `[ÖNERİ]` | AÇIK |
| §18.9 | FFmpeg vendor | P1 | §14.5 · G6 | vendor-artı-sistem `[ÖNERİ]` | Kardeş U7 ile |
| §18.10 | Çevrimdışı tanımı | P2 | NE-PLR-013/213 | Temel oynatma ağsız | AÇIK |
| §18.11 | Bildirim/medya tuşları | P2 | NE-PLR-209/210 | Bildirim kapalı + sanitize | AÇIK |
| §18.12 | Windows kurulum modeli | P2 | NE-PLR-001 · §15.6 | Portable + kurulum sonra `[ÖNERİ]` | AÇIK |
| §18.14 | ADR-0105 surrogate iadesi | **P0** | Tüm `⚠️ VR` satırları (D2) | Etiketli çalışma + Vault Steward | **KRİTİK AÇIK** |

### §18.14 ADR-0105 İçerik Surrogatesi (D2 — Kritik Açık)

- **Q:** `ADR-0105` (neva-player/neva-engine geçmiş oturumların kaydı, v1.0.x-v1.4.0) içeriği nerede ve geri getirilecek mi?
- **Kaynak durumu:** `.ai/.decisions/index.md:152` yalnız **indeks satırı** veriyor; `accepted/` veya `draft/` altında ADR-0105 dosyası **diskte YOK** (§00.3 D2 kanıtı).
- **Etki:** Bu PRD'deki ~40+ `⚠️ VR (ADR-0105 …)` etiketi doğrudan bundan doğuyor (R-P02 risk 20).
- **Varsayılan:** Etiketli çalışma (gizleme yok) + Vault Steward'a iade talebi (kardeş NE-ENG-182/NE-PLR karşılığı).
- **Karar sahibi:** Vault Steward (vault-updater) + ürün → dosya iadesi veya "ADR-0105 yok edildi" kaydı.
- **Kapanış ölçütü:** Dosya vault'a iade edildiyse `⚠️ VR` etiketleri tek tek doğrulanır ve kaldırılır; yoksa resmi "yok" kaydı yapılır (bilinmeyen = `UNKNOWN`).

### §18.15 Bilinen Çelişki / Tutarsızlık Defteri (Player)

| # | Çelişki | Kaynaklar | Bu PRD'nin tutumu |
|---|---|---|---|
| CP-01 | ADR-0105 dosyasız (surrogate) | index.md:152 vs disk | D2 + §18.14 + R-P02 — `⚠️ VR` etiketli. |
| CP-02 | "25 dil" (VISION) vs kapsam | VISION vs NE-PLR-014 | "25" `UNKNOWN`; TR+EN `[ÖNERİ]` (§18.2). |
| CP-03 | 10/20-band listeleri (VLC/AIMP) teyitsiz | kardeş §06.1 `⚠️` | 15/20 gizli/kilitli `[ÖNERİ]` (R-P09). |
| CP-04 | Bit-perfect vs "shared modda da tam" | ADR-0105 kapı 5.1 `⚠️` vs ADR-106 §1.3 | Rozet yalnız exclusive (NE-PLR-045); shared iddiası ölçülmeden yazılmaz. |
| CP-05 | Gapless ±5 ms (AK-5) vs ölçüm yokluğu | kapı 5.12 `⚠️` | Eşik `⚠️ VR`; ölçüm raporu olmadan iddia yasak (Edge 3). |
| CP-06 | Video VRAM (GTX 1660 400-700 MB) vs açık kart (1060-3GB %19,5) | §7.5 `⚠️` | İki satır birlikte raporlanır; kart adları `⚠️ VR` (R-P04). |
| CP-07 | ADR-019 "5/5/5/5/5" bant genişliği vs bu PRD §03 | ADR-019 §2.2 | Bant genişliği = önbellek bandı yorumu; §03'te `⚠️ VR` ile iki yorum da yazıldı. |
| CP-08 | Xerces-4.6 XXE emsal vs ICY v2 parser | radio-and-hires §4 `⚠️` | DTD/entity kapalı + fuzz (TC-PLR-172) — R-P15. |

### §18.16 Karar Formu Şablonu

```markdown
## ADR-NNN — <karar başlığı>
- Tarih / Durum: <proposed|accepted|superseded>
- Bağlam: <§18.x açık madde + R-xx risk>
- Karar: <net cümle — ne yapılır/ne yapılmaz>
- Alternatifler: <(a)/(b)/(c) + reddedilen + gerekçe>
- Sonuçlar: <kapı/gereksinim etkisi (NE-PLR-NNN · TC-PLR-NNN)>
- Vault: `.ai/.decisions/accepted/ADR-NNN-<slug>.md` + log.md
```

**Zorunluluk:** §18.1 (UI toolkit) + §18.7 (mockup) + §18.14 (ADR-0105 iadesi) **M4 öncesi** zorunlu; §18.9 (vendor) PAKET öncesi; diğerleri etiketli ilerleyebilir (§15.3 kritik yol).

---

## §19 Sözlük & Kısaltmalar (Player — Bölüm 1: A–L)

> Her terim: **TR karşılığı** · **tanım (bu PRD bağlamında)** · **kaynak** · **çapraz referans**. Kaynaksız tanım yok (AGENTS §3).

### §19.1 Terimler (A–C)

| Terim / TR | Tanım (bu PRD) | Kaynak | Xref |
|---|---|---|---|
| **A/V sync (audio-video synchronisation)** | Ses ve video arasındaki kayma; P95 ≤20 ms `⚠️` (kapı 5.11). | ADR-0105 kapı 5.11 `⚠️` | §08 · TC-PLR-079 |
| **ABI (Application Binary Interface)** | `neva_*` C arayüzü — player motorla tek kapı. | ADR-107 K1 | §05.1 · NE-PLR-002 |
| **Acceptance gate** / kabul kapısı | Faz sonu onay (AGENTS §1). | AGENTS §1 | §15.4 |
| **Adaptive streaming** / uyarlanabilir akış | HLS segment seçimi; kapıları (50 MB/1000/derinlik 1). | ADR-106 kural 8 | §07.5 |
| **ADR** / mimari karar kaydı | Bağlayıcı karar belgesi. | `.ai/.decisions/` | §18 |
| **AES-128 (HLS şifreleme)** | HLS segment şifreleme anahtarı (`#EXT-X-KEY`); `⚠️ VR` (kapı kapsamı). | ADR-106 kural 8 `⚠️` | §07.5 |
| **ALSA / CoreAudio / PipeWire** | Linux/macOS/alternatif ses altyapıları (motor backend'i). | ADR-019 §2.1 | §04.4 |
| **API version** | `NEVA_API_VERSION 0x00010000` handshake'i. | disk `neva_session.h` | §05.4 |
| **ASIO** | Steinberg düşük gecikme sürücüsü (U1 lisans — motor). | ADR-107 §8.1 | §03.3 |
| **Atomic write** / atomik yazma | Yarı yazma önleme (config/preset). | [ÖNERİ] | NE-PLR-146 |
| **Attribute / attribution** | Lisans metni dağıtımı (NOTICE §14.4). | §14.3 | G6 |
| **Backend** | OS ses API katmanı (motorun 7 basamağı). | ADR-019 §2 | §04.4 |
| **Backoff** / geri çekilme | Başarısız istekler arasında üssel artan bekleme: 1s→30s ±%20; 3 fails → ERROR. | ADR-106 kural 8 | §07.4 · TC-PLR-106 |
| **Band (EQ)** | EQ merkez frekansı; 31/10/15/20 seçilebilir grid. | ADR-106 §2.2b | §06.1 |
| **Bit-perfect** | Değiştirilmemiş bit akışı (exclusive + Flat + eşit format + hash). | kapı 5.1 | §11.2 · TC-PLR-036 |
| **Bloom (metadata)** | Başlık spam'i — drop + sayaç (metaveri kuralı). | ADR-106 kural 7 | §07.3 · FM-E15 |
| **Block size** / blok boyutu | Callback başına örnek (≤256 `⚠️ VR`). | ADR-0105 §2.10 `⚠️` | §03.4 |
| **Buffering / prebuffer** | Radyo öntamponu 2-4 s (+ilk ses ≤4 s). | ADR-106 kural 6 | §07.4 · TC-PLR-107 |
| **Catalog / katalog** | İstasyon/dosya indeksi (SQLite — ADR-074). | ADR-074 indeks `⚠️` | §06.5 · §18.8 |
| **CLI** | Komut satırı arayüzü (M4 öncesi geçiş). | §15.5 | NE-PLR-001 |
| **Cooldown** | Yeniden deneme öncesi sabit bekleme (backoff'tan farklı). | ADR-106 kural 8 | §07.4 |
| **Crossfade** | Parçalar arası üst üste binen geçiş (gapless ile FARKLI — kapsam `[ÖNERİ]`). | bu PRD | §02.3 |
| **CSS/ITCSS/BEM** | Kardeş UI stil mimarisi (token `--cm-*`). | AGENTS §10 | kardeş §13.3 |
| **CTA/SEEK** | Oynatma konumu değiştirme isteği (radyoda RED). | kardeş §05.2 | §05.3 |

### §19.2 Terimler (D–H)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Decode** | Sıkıştırılmış → PCM (FLAC/MP3/AAC/OGG/Opus/DSD). | ADR-0105 `⚠️` | §05.1 · Grup B |
| **Determinism** | Aynı girdi → aynı çıktı (2× hash). | kapı 5.1 | TC-PLR-197 |
| **DoP (DSD over PCM)** | DSD'in PCM çerçevelerinde taşınması (gapless vektörleri D05). | ADR-0105 AK-5 `⚠️` | §07.2 · TC-PLR-072 |
| **DSD/DSDIFF/DSF** | 1 bit format ailesi; native yol U3 kapalı. | ADR-0105 `⚠️` | §05.1 · U3 |
| **Downmix** / karıştırma | Çok kanallı → stereo dönüşüm matrisi (`⚠️ VR` katsayılar). | VISION V-8 | §18.3 |
| **DSP** | EQ/limiter/resampler zinciri (motor). | ADR-106 | §06 |
| **E1–E10 (event)** | Motor→UI olayları (durum/konum/hata/buffer…). | kardeş §05.2 | §05.2 |
| **Edge (AGENTS §17)** | 10 davranış kuralı (ölçüm/yarım/EREG…). | AGENTS §17 | §15.5 |
| **EGRESS / erişilebilirlik (EREG)** | Klavye/odak/kontrast denetimi. | AGENTS §17 Edge 8 | TC-PLR-161 |
| **Escape (HTML)** | Metaverinin zararlı HTML'den arındırılması (tek katman). | ADR-106 kural 7 | §12.3 · TC-PLR-170 |
| **EQ (Equalizer)** | Frekans tepkisi düzeltici; 31/10/15/20 band. | ADR-106 §2.2b | §06 |
| **Event-driven** | WASAPI exclusive olay kipi (motor). | ADR-019 §2.4 | §04.3 |
| **Exclusive mode** | Cihazın tek süreçte ele geçirilmesi (bit-perfect ön koşulu). | ADR-019 §1.1 | §04.3 · TC-PLR-045 |
| **Fallback** | Backend zinciri düşüşü (7 basamak). | ADR-019 §2.2b | §04.4 |
| **Fade-in** | İlk ses açılışında 20 ms kademeli geçiş (pop önleme). | ADR-106 kural 6 | §07.4 |
| **FDK-AAC / BASS / mpv** | RED lisanslı bileşenler. | ADR-106 kural 9 · ADR-0105 | §14.2 |
| **FFmpeg** | Decode/demux kütüphanesi 9.0.2 (LGPL build — `--enable-gpl` YOK). | ADR-0105 `⚠️` · ADR-106 | §14.1 P02 |
| **Flat (EQ)** | Tüm bantlar 0 dB — hash ön koşulu (Flat=all-zero kanıtı). | ADR-106 kural 4 | §11.2 · TC-PLR-040 |
| **Format negotiation** | Dosya ↔ cihaz hızı uyuşmazlığı çözümü (N1 → soxr → zincir). | ADR-019 F3 · ADR-106 §1.3 | §04.4 |
| **Fuzzing** | Bozuk girdiyle çökme arama (kapı 5.13). | kapı 5.13 `⚠️` | TC-PLR-188 |
| **Gapless** | Kesintisiz geçiş (AK-5 ±5 ms `⚠️`). | ADR-0105 AK-5 `⚠️` | §07.2 · kapı 5.12 |
| **Grid (EQ)** | Bant frekans listesi (31 ISO teyitli; 10 alt küme; 15/20 gizli `⚠️`). | ADR-106 §2.2b | §06.1 · TC-PLR-147 |
| **Guard** | Negatif denetim (sahte PASS önleme). | kapı 5.1 | TC-PLR-040 |
| **HLS** | HTTP Live Streaming (segment/playlist kapıları). | ADR-106 kural 8 | §07.5 |

### §19.3 Terimler (I–L)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **ICY (v1/v2)** | Radyo metaveri protokolü; v2 `X-Stream-*` (XML `⚠️ VR` + XXE kapalı). | disk `icy_parser.h` · ADR-106 | §07.3 |
| **IDN / punycode** | Uluslararasılaştırılmış etki alanı adları (T25 vektörü). | [ÖNERİ] | §12.2 |
| **IIR / biquad** | EQ filtresi ailesi (motor `biquad.h`). | disk `biquad.h` | §06.2 |
| **InitContainer (K2)** | Derleme katmanı — kod `apps/` altında. | ADR-107 K2 | §04.6 |
| **Injection (OWASP A03)** | XSS/SQLi/path enjeksiyonu ailesi. | AGENTS §5 | §12.1 |
| **ISO 266 / 126** | Band ızgara standartları. | ADR-106 §2.2b | §06.1 |
| **JSON depth cap** | İç içe parse derinlik sınırı (64 `[ÖNERİ]`). | [ÖNERİ] | NE-PLR-175 |
| **Kbps / bitrate** | Bit hızı (128-320 kbps `⚠️ VR` liste). | ADR-106 §2.2a `⚠️` | §07.5 |
| **Keşif (discovery)** | Radyo istasyonu bulma (yerel M3U/RSS + Radio Browser `OFF`). | ADR-074 · §18.6 | §06.5 |
| **Latency** | Gecikme (<20 ms — kapı 5.10). | ADR-0105 §2.10 `⚠️` | §08 · TC-PLR-183 |
| **Leak** | Bellek sızıntısı (1000 cycle + LSan). | — | TC-PLR-190 |
| **LGPL-2.1** | Dinamik bağlama şartı (soxr/FFmpeg). | ADR-0105 `⚠️` | §14.3 |
| **libplacebo 7.350.0** | Video render kütüphanesi (V1 fazı; lisans `⚠️ VR`). | ADR-0105 `⚠️` | §14.1 P14 |
| **Loudness / LUFS** | Ses seviyesi standardı (şartlı — §18.9 kardeş). | ADR-0105 `⚠️` | kardeş §18.9 |
| **Loopback** | Ölçüm düzeneği (latency/SNR/THD). | kardeş §11.3-A | §11.4 |

*(Bölüm 2: M–Z + kısaltma listesi bir sonraki parçada.)*

---

## §19 Sözlük & Kısaltmalar (Player — Bölüm 2: M–Z + Kısaltmalar)

### §19.4 Terimler (M–O)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Main clock / master clock** | A/V senkronunun referansı = **ses cihazı saatı**. | ADR-0105 §7 `⚠️` | §08 · TC-PLR-079 |
| **Manifest (HLS)** | `#EXTM3U` playlist — kapılar (entry/derinlik). | ADR-106 kural 8 | §07.5 |
| **mbedTLS 9.13.0** | TLS (Apache-2.0). | ADR-106 §5.4 | §14.1 P05 |
| **Metadata / metaveri** | Başlık/URL/istasyon bilgisi (256 bayt cap + UTF-8). | ADR-106 kural 7 | §07.3 |
| **Migrate / migration** | Şema sürümleme (katalog — ADR-074). | ADR-074 | §06.5 |
| **Mock istasyon** | Test için sahte radyo sunucusu (deterministik kapılar). | §11.3 | TC-PLR-118 |
| **M3U / M3U8** | Çalma listesi biçimi (yerel içe/dışa aktarım). | [ÖNERİ] | TC-PLR-208 |
| **Mixer bypass** | OS mikserinin devre dışı bırakılması (exclusive). | ADR-019 §1.1 | §04.3 |
| **mpv/libmpv** | RED (video emsal — ADR-0105 v1.1.1). | ADR-0105 `⚠️` | §14.2 |
| **Mutex / lock** | Kilit — RT'de yasak (motor). | S3 §6.1 | §07.5 |
| **N1** | Hız uyuşmazlığı çözüm sırası (setDeviceRate → soxr → zincir). | ADR-019 F3 | §04.4 · TC-PLR-047 |
| **neva-core / neva-host** | Motor çekirdeği / UI köprüsü süreçleri. | ADR-107 K2/§8.6 | §04.6 · NE-PLR-179 |
| **NOW PLAYING** | Oynatılıyor göstergesi (durum makinesi). | ADR-019 §1.4 | §04.5 |
| **Null backend** | Zincir sonu — ses yok, uygulama yaşar. | disk backends/ | §04.4 |
| **Offline / çevrimdışı** | Ağsız çalışma tanımı (§18.10). | AGENTS Edge 9 | §18.10 |
| **OSD (On-Screen Display)** | Ekranda bilgi katmanı (kardeş UI). | — | kardeş §13.3 |
| **Overrun** | Halka taşması (xrun ailesi). | V-3 | §07.5 |

### §19.5 Terimler (P–R)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Packet (ICY)** | Metaint aralığındaki metin çerçevesi (31 B RED/32 B OK). | disk `icy_parser.h` | §07.3 · TC-PLR-103 |
| **Pause / Stop** | Duraklat (konum korunur) / Durdur (konum sıfırlanır — karar `[ÖNERİ]`). | kardeş §05.2 | §04.5 |
| **Playlist (queue)** | Sıra: sıradaki/önceki/tekrar/shuffle — gapless kuyruğu. | ADR-0105 AK-5 `⚠️` | §07.2 |
| **Player** | Masaüstü uygulama (bu PRD'nin konusu). | ADR-019 §2.3 | §01 |
| **Podcast / RSS** | Yayın akışı kaynakları (yerel M3U/RSS — `OFF`). | §18.6 | §06.5 |
| **Position / duration** | Konum/süre (E3/E4 eventleri; radyoda duration yok `⚠️ VR`). | kardeş §05.2 | §05.2 |
| **Preamp** | Öngiriş kazancı (EQ öncesi). | ADR-106 kural 4 | §06.4 |
| **Prebuffer** | Radyo öntamponu 2-4 s. | ADR-106 kural 6 | §07.4 |
| **Preset (EQ)** | Kayıtlı bant değerleri (CRU + Flat + taşıma). | ADR-106 kural 4 | §06.5 |
| **Progressive download** | Tam indirme+oynatma (HLS'e alternatif `⚠️ VR`). | ADR-106 §2.2a `⚠️` | §07.5 |
| **Protocol whitelist** | Yalnız http/https (T0-T27). | ADR-106 kural 6 | §12.2 |
| **Queue / kuyruk** | Oynatma sırası + bozuk dosya atlama. | — | §13.1 H3 |
| **Radio Browser** | Açık istasyon veritabanı (opsiyonel keşif — OFF). | §18.6 | NE-PLR-113 |
| **Ramp (EQ)** | Bant geçişi 5-20 ms kademeli. | ADR-106 kural 4 | §06.3 |
| **Reachability / erişilebilirlik** | WCAG/EREG (klavye/odak/kontrast). | AGENTS §17 Edge 8 | TC-PLR-161 |
| **Redirect** | Yönlendirme — ≤5 hop (T18-T22). | ADR-106 kural 6 | §12.2 |
| **Remote (neva-remote)** | Uzak kontrol (localhost+token `ÖNERİ` — OFF). | ADR-0105 v1.4.0 `⚠️` | §18.5 |
| **Repeat / Shuffle** | Tekrar/karışık kuyruk modları. | ADR-0105 AK-5 `⚠️` | §07.2 |
| **Resampler / soxr** | Yeniden örnekleme (LGPL-2.1). | ADR-0105 `⚠️` | §06.6 |
| **Ring buffer** | Halka tampon (motor SPSC). | disk `spsc_ring.h` | §07.5 |
| **RT (realtime)** | Zaman kısıtlı katman (motor) — player dokunmaz. | S3 §6.1 | §07.5 |
| **RSS (Really Simple Syndication)** | Podcast/yayın beslemesi (yerel destek `OFF`). | §18.6 | §06.5 |

### §19.6 Terimler (S–Z)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **SABIT / sabit (preset)** | Kayıtlı EQ değeri (JSON). | ADR-106 kural 4 | §06.5 |
| **Seek** | Konuma atlama (radyoda RED — canlı akış). | kardeş §05.2 | §05.3 · TC-PLR-150 |
| **Segment (HLS)** | Akış dilimi (50 MB kapı). | ADR-106 kural 8 | §07.5 |
| **Self-signed / expired** | Sahte/süresi dolmuş sertifika (RED — T yolu). | ADR-106 §5.4 | TC-PLR-169 |
| **Session / oturum** | Motor yaşam döngüsü (open/start/stop/close). | disk `neva_session.h` | §04.5 |
| **Shared mode** | OS mikserli mod (bit-perfect ROZETİ YOK). | ADR-019 §1.1 | §04.3 · TC-PLR-046 |
| **Shuffle** | Karışık sıralama (gapless ile uyum vektörleri). | ADR-0105 AK-5 `⚠️` | §07.2 |
| **SID/UID** | Sistem/ kullanıcı kimliği (IPC reddi `ÖNERİ`). | [ÖNERİ] | TC-PLR-179 |
| **SNR / THD+N** | Sinyal-gürültü / bozulma ölçütleri (>105 dB / <%0.005 `⚠️`). | VISION V-1/V-2 | §08.1 |
| **Socket / curl** | Ağ primitifleri — **player dışında** (motor SE-03). | S3 §4.3 | §12.7 |
| **SQL injection** | `' OR 1=1` ailesi — parametreli sorgu. | AGENTS §5 | TC-PLR-176 |
| **SSRF (Server-Side Request Forgery)** | Özel IP/metadata erişimi (T6-T17 RED). | ADR-106 kural 6 | §12.2 |
| **State machine** | Durum makinesi: STOPPED/LOADING/PREBUFFER/PLAYING/PAUSED/ERROR. | ADR-019 §1.4 | §04.5 |
| **StreamTitle / StreamUrl** | ICY başlık/URL alanları (escape + cap). | ADR-106 kural 7 | §07.3 |
| **StreamError** | Motor hata olayı (E10). | kardeş §05.2 | §05.2 |
| **STRIDE** | Tehdit sınıflandırması. | OWASP | §12.1 |
| **Stop / teardown** | Oturum kapatma (son durum kaydı — TC-PLR-164). | — | §13.1 H7 |
| **Supply chain** | Bağımlılık zehirlenmesi (CVE taraması). | AGENTS §5 | TC-PLR-180 |
| **Test corpus** | Fuzz girdi kümesi (≥1000/format). | kapı 5.13 `⚠️` | §11.5 |
| **THD+N / SNR** | Ölçüm eşikleri (§08.1). | VISION | §08.1 |
| **Timeout** | Zaman aşımı (10 s `⚠️` — curl). | ADR-106 §5.4 | §07.4 |
| **TLS** | Taşıma katmanı şifrelemesi (mbedTLS). | ADR-106 §5.4 | §12.2 |
| **Token** | Uzak erişim anahtarı (`ÖNERİ` — localhost+token). | [ÖNERİ] | §18.5 |
| **Transport** | Oynat/duraklat/önceki/sonraki kontrolleri. | kardeş §05.2 | §13.3 |
| **True-peak limiter** | Örnekleme tepe sınırıcı (EQ zinciri sonu). | [ÖNERİ] | §06.4 |
| **TSan / LSan** | Yara/sızıntı dedektörleri. | S3 §5 | TC-PLR-190/191 |
| **Unicode / UTF-8** | Karakter kodlaması (başlık doğrulaması). | ADR-106 kural 7 | TC-PLR-171 |
| **URL validator** | Tek kapı doğrulayıcı (T0-T27 — bu PRD sahibi). | ADR-106 kural 6 | §12.2 |
| **User-Agent** | İstemci etiketi (keşif istekleri `⚠️ VR`). | `⚠️ VR` | §07.3 |
| **VBR/CBR / Xing** | MP3 bit hızı + seek tablosu (gapless). | ADR-0105 AK-5 `⚠️` | §07.2 |
| **VLC / AIMP (10/20-band)** | Referans EQ listeleri — **teyitsiz `⚠️ VR`**. | kardeş §06.1 `⚠️` | TC-PLR-124/125 |
| **Volume / ses seviyesi** | OS mikseri (exclusive'de bypass — bit-perfect). | ADR-019 §1.1 | §04.3 |
| **WASAPI / WDM-KS** | Windows backend'leri (motor). | ADR-019 §2.4 | §04.4 |
| **Web UI** | Tarayıcı arayüzü (U4 — OFF, UPnify emsal şifre akışı). | ADR-0105 v1.4.0 `⚠️` | §18.5 |
| **Widget** | Küçük UI bileşeni (mimari ≠ CoreMusic web widget'ı). | kardeş §02 | §02.2 |
| **XML (ICY v2)** | v2 metaveri biçimi — DTD/entity kapalı (XXE). | ADR-106 kural 8 | TC-PLR-172 |
| **Xrun** | Örnek kaybı — 0 kabul (stres kapıları). | V-3 | §07.5 |
| **Zero-allocation** | Motor RT kuralı (player korur, ihlal etmez). | S3 §6.1 | §07.5 |

### §19.7 Kısaltmalar Listesi

| Kısaltma | Açılımı | Xref |
|---|---|---|
| A/V sync | Audio/Video synchronisation | kapı 5.11 |
| ABI | Application Binary Interface | §05.1 |
| AES | Advanced Encryption Standard (HLS `⚠️ VR`) | §07.5 |
| ALSA | Advanced Linux Sound Architecture | §04.4 |
| API | Application Programming Interface | §05 |
| ASIO | Audio Stream Input/Output (motor/U1) | §03.3 |
| ADB / AIMP / VLC | Referans uygulamalar (K4/EMsal — kopya YASAK) | kardeş §06.1 |
| CI | Continuous Integration | §11.7 |
| CLI | Command-Line Interface | §15.5 |
| CRU | Create/Read/Update (preset işlemi) | §06.5 |
| CSS/ITCSS/BEM | Stil mimarisi (kardeş UI) | kardeş §13.3 |
| DAC | Digital-to-Analog Converter | §11.4 |
| DASH | Dynamic Adaptive Streaming over HTTP (`⚠️ VR`) | §07.5 |
| DSP | Digital Signal Processing (motor) | §06 |
| DSD / DoP | Direct Stream Digital / DSD over PCM | §05.1 |
| EQ | Equalizer | §06 |
| EREG | Erişilebilirlik (AGENTS Edge 8) | TC-PLR-161 |
| FDE | File Discovery Engine | ADR-005 |
| FFDK/BASS/mpv | RED lisanslılar | §14.2 |
| FFMPEG | Decode/demux 9.0.2 (LGPL) | §14.1 P02 |
| FMEA | Failure Mode Analysis (kardeş §12) | kardeş |
| Fuzzing | Bozuk girdi testi | kapı 5.13 |
| HLS | HTTP Live Streaming | §07.5 |
| HTTP/HTTPS | HyperText Transfer Protocol (Secure) | §12.2 |
| ICY | Radyo metaveri protokolü (v1/v2) | §07.3 |
| IPC | Inter-Process Communication | TC-PLR-179 |
| ISO | International Organization for Standardization | §06.1 |
| JSON | JavaScript Object Notation (config/preset) | §06.5 |
| LGPL | GNU Lesser General Public License | §14.1 |
| LUFS | Loudness Units Full Scale (şartlı) | kardeş §18.9 |
| M3U/M3U8 | Çalma listesi biçimleri | TC-PLR-208 |
| MCP | Model Context Protocol (U5) | §18.5 |
| MP4/MKV | Video kapsayıcıları (V1) | §07.5 |
| OWASP | Open Web Application Security Project | §12.1 |
| P0-P6 / V1 | Player fazları | §15 |
| PCM | Pulse-Code Modulation | §05.1 |
| PREBUFFER | Öntampon durumu | §04.5 |
| RAM/VRAM | Bellek bütçeleri (400 MB / 400-700 MB `⚠️`) | §08.3 · §07.5 |
| RSS | Really Simple Syndication | §06.5 |
| RT | Realtime (motor katmanı) | §07.5 |
| SID/UID | Sistem/kullanıcı kimliği (`ÖNERİ`) | TC-PLR-179 |
| SNR / THD+N | Sinyal-gürültü / bozulma | §08.1 |
| SPSC | Single-Producer Single-Consumer (motor) | §07.5 |
| SQL/SQLite | Katalog motoru (ADR-074) | §06.5 |
| SSRF | Server-Side Request Forgery | §12.2 |
| STRIDE | Tehdit sınıflandırması | §12.1 |
| TLS | Transport Layer Security | §12.2 |
| URL | Uniform Resource Locator | §07.3 |
| UTF-8 | Unicode kodlaması | §07.3 |
| VBR/CBR/Xing | MP3 bit hızı/seek | §07.2 |
| WASAPI / WDM-KS | Windows backend'leri (motor) | §04.4 |
| WCAG | Web Content Accessibility Guidelines | §13.3 |
| Xrun | Underrun + Overrun | §07.5 |

### §19.8 İşaret Lejandı (tüm PRD boyunca)

| İşaret | Anlamı | Kural |
|---|---|---|
| `UNKNOWN` | Bilinmeyen bilgi | Doğrulanana kadar yazılmaz (AGENTS §3) |
| `⚠️ VERIFICATION REQUIRED` / `⚠️ VR` | Doğrulanamayan iddia (kaynak eksik/parça) | İddia etiketli kalır; release iddiası yapılmaz |
| `[VARSAYIM]` | Dayanağı zayıf varsayım | Gerekçesi yazılır; ölçümle değişir |
| `[ÖNERİ]` | Bu PRD'nin önerisi (ADR'siz) | Bağlayıcı değil; ADR ile teyit |
| `⚠️ LİSANS` | Lisans bilinmeyenliği | RED varsayılan (G3/G6) |
| `[KRİTİK]` | Faz bloklayıcı açık madde | Karar kapısı (§18) |

---

## §20 Onay & Doküman Kontrolü (Player)

### §20.1 Onay Kapısı (AGENTS §1 — Workflow)

Bu PRD **üretim öncesi onay beklemektedir**. Onay verilmeden §15 fazlarından hiçbiri başlamaz
(özelikle **M4-UI** — §18.1 UI toolkit kararı + §18.7 mockup kapısı ayrı onay ister).

| Kapı | Onaylayıcı | Rol (AGENTS §6.1) | Kapsam |
|---|---|---|---|
| K-1 Mimari | `architect` | Expert | §03-§05 · durum makinesi · katmanlar |
| K-2 UX/Tasarım | `ui-designer` + `accessibility-tester` | Senior + uzman | §13 hata UX · §18.1/§18.7 · WCAG |
| K-3 Güvenlik | `security-engineer` | Expert | §12 (OWASP · T0-T27 · G1-G6) |
| K-4 Ölçüm/Test | `qa-engineer` + `performance-engineer` | Expert + Senior | §08 · §11 (215 TC) · kapılar |
| K-5 Ürün | Ürün sahibi (lider) | — | §01-§02 · §15 · §18 karar kapıları |
| **K-6 PRD** | **Orkestratör (ana oturum)** | — | **Tüm doküman — tek imza** |

**Onay durumu:** `ONAYLANDI` (2026-10-23) — üretim + ölçüm raporundan sonra onay sorusuna kullanıcı yanıtı **"DEVAM ET"**; kapsam (§00.4) + içerik onayı verildi. K-1..K-5 faz kapıları ayrıdır (§15 faz başlangıçlarında uygulanır).

### §20.2 Doküman Kontrolü

| Alan | Değer |
|---|---|
| Doküman adı | `coremusic-neva-player-prd.md` |
| Sürüm | v1.0.0 (taslak — onay öncesi) |
| Tarih | 2026-10-23 |
| Dil | Türkçe (teknik terimler İngilizce korunur) |
| Kardeş doküman | `coremusic-neva-engine-prd.md` (182 gereksinim) |
| Hedef uzunluk | ≥8000 satır (ölçüm kanıtı §20.5'te) |
| Kaynak hiyerarşisi | AGENTS §3: kullanıcı > ADR > vault > resmi doküman > genel bilgi |
| SSOT | ADR-106 (radyo/EQ) · ADR-019 (durum/paket) · `.ai/.decisions/index.md` (ADR-0105 surrogate) |
| Sahip | CoreMusic — Neva Player çalışma grubu |
| Sınıf | Teknik gereksinim dokümanı (PRD) — kabul kriteri düzeyi |

### §20.3 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 0.1 | 2026-10-23 | İskelet (§00-§09) + kaynaklar (ADR-019, ADR-106, ADR-105 indeksi, `neva_session.h`, `icy_parser.h`, `eq_grids.h`) | Orkestratör |
| 0.5 | 2026-10-23 | §10 gereksinimler (215) + §11 test planı (215 TC) | Orkestratör |
| 0.8 | 2026-10-23 | §12-§17 (güvenlik/hata UX/lisans/yol haritası/risk/izlenebilirlik) | Orkestratör |
| 1.0.0 | 2026-10-23 | §18-§21 (açık maddeler/sözlük/onay/ekler) — **onaylandı** (K-6, 2026-10-23) | Orkestratör |

### §20.4 Onay Satırı (imza alanı)

```
Neva Player PRD v1.0.0 — ONAY

K-1 Mimari .......... [ ] onaylandı / [ ] revize
K-2 UX/Tasarım ...... [ ] onaylandı / [ ] revize
K-3 Güvenlik ........ [ ] onaylandı / [ ] revize
K-4 Ölçüm/Test ...... [ ] onaylandı / [ ] revize
K-5 Ürün ............ [ ] onaylandı / [ ] revize
K-6 PRD (toplam) .... [x] ONAYLANDI (2026-10-23 — "DEVAM ET") / [ ] REVİZE / [ ] RED

Tarih: 2026-10-23  İmza: K-6 (orkestratör) — onay kanıtı: kullanıcı yanıtı "DEVAM ET" (§20.1)
```

**Onaylanan neyi taşır:** §10'daki 215 gereksinimin bağlayıcılığı · §11'deki 215 testin kapsamı · §18'deki açık maddelerin **bilinçli açık** olarak kabulü · §15 faz sırası (P0→P3→P4→M4-UI→P6→PAKET).

### §20.5 Ölçüm Kanıtı (satır sayısı)

```powershell
$f = "G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-neva-player-prd.md"
(Get-Content $f | Measure-Object -Line).Lines   # boş satırlar HARİÇ
(Get-Content $f).Count                          # ham satır (boş dahil) — KABUL: >= 8000
(Get-Item $f).Length                            # bayt
```

### §20.6 Nihai Ölçüm Raporu (üretim sonrası dolduruldu)

| Ölçüt | Değer | Durum |
|---|---|---|
| Ham satır (boş dahil) | **8006** — `(Get-Content f -Encoding UTF8).Count` · 42 parça (plr-p01..p42) birleştirildi | ✅ ≥8000 |
| Dolu satır (boş hariç) | **6624** — `Where-Object {$_ -ne ''} \| Count` | ✅ |
| Bayt | **458307** — `(Get-Item f).Length` · son ölçüm 2026-10-23 (K-6 onay editleri + CJK düzeltmesi sonrası) | ✅ |
| Gereksinim sayısı | 215 (NE-PLR-001..215) | ✅ |
| Test senaryosu sayısı | 215 (TC-PLR-001..215) | ✅ |
| Açık madde (§18) | 13 karar kapısı + ADR-0105 iadesi (3 KRİTİK → T-P01..T-P03) | ✅ işaretli |
| `⚠️ VR` / `[ÖNERİ]` / `UNKNOWN` | **208 / 334 / 43** · + `[VARSAYIM]`=4 · `⚠️ LİSANS`=8 · toplam `⚠️`=440 (regex-escape sayımı) | ✅ işaretli |

### §20.7 Kapsam Dışı Beyanı (tek cümle)

Bu PRD **kod yazmaz** — yalnız gereksinim, test kapsamı, kapılar ve karar kapılarını tanımlar; UI implementasyonu §18.1 kararı ve §18.7 mockup kapısından sonra kardeş planla yapılır.

---

# EKLER (§21)

## §21 Ek A — Gereksinim İndeksi (Player · 215 madde)

> Bu tablo **§10'daki başlıklardan otomatik olarak çıkarıldı** (kaynak dosyalardan — elle yazılmadı).
> Sütunlar: **ID · Başlık · Kaynak parça**.

| ID | Başlık | Kaynak parça |
|---|---|---|
| NE-PLR-001 | Per-OS ortak çekirdek + ince adapter | plr-p05 |
| NE-PLR-002 | C ABI üzerinden engine kullanımı | plr-p05 |
| NE-PLR-003 | api_version handshake kontrolü | plr-p05 |
| NE-PLR-004 | Durum makinesi (ADR-019 §1.4) | plr-p05 |
| NE-PLR-005 | Event callback (metadata/state/error) | plr-p05 |
| NE-PLR-006 | Konum/düre erişimi (E3/E4) | plr-p05 |
| NE-PLR-007 | Cihaz listesi & seçimi (E8) | plr-p05 |
| NE-PLR-008 | UI katmanı ayrımı | plr-p05 |
| NE-PLR-009 | UI toolkit kararı açık | plr-p05 |
| NE-PLR-010 | Config kalıcılığı | plr-p05 |
| NE-PLR-011 | Engine bağımlılık sürümü | plr-p05 |
| NE-PLR-012 | neva-eq / neva-matrix ayrı hedefler | plr-p05 |
| NE-PLR-013 | Çevrimdışı öncelik | plr-p05 |
| NE-PLR-014 | RTL/konum testi yok (kapsam notu) | plr-p05 |
| NE-PLR-015 | Kardeş PRD sınırı | plr-p05 |
| NE-PLR-016 | Decode matrisi (dr_libs + libdsd + FFmpeg) | plr-p05 |
| NE-PLR-017 | Yerel dosya demux önceliği (FFmpeg dışı) | plr-p05 |
| NE-PLR-018 | soxr producer-side resample | plr-p05 |
| NE-PLR-019 | soxr lisansı (LGPL-2.1) | plr-p05 |
| NE-PLR-020 | FFmpeg LGPL build | plr-p05 |
| NE-PLR-021 | Protocol whitelist (FFmpeg) | plr-p05 |
| NE-PLR-022 | HTTP09Allowed=0 | plr-p05 |
| NE-PLR-023 | Redirect zinciri ≤5 | plr-p05 |
| NE-PLR-024 | Timeout 10 sn (tuner) | plr-p05 |
| NE-PLR-025 | TLS (mbedTLS) | plr-p05 |
| NE-PLR-026 | decode → float32 ring | plr-p05 |
| NE-PLR-027 | Bit derinliği dönüşüm politikası | plr-p05 |
| NE-PLR-028 | Queue prefetch (gapless ön koşulu) | plr-p05 |
| NE-PLR-029 | Decode hata yönetimi (bozuk dosya) | plr-p05 |
| NE-PLR-030 | Aynı anda tek decode oturumu (dosya) | plr-p05 |
| NE-PLR-031 | Metadata okuma (ID3/Vorbis/FLAC tags) | plr-p05 |
| NE-PLR-032 | Kapak görseli (embedded art) | plr-p05 |
| NE-PLR-033 | ReplayGain / loudness okuma | plr-p05 |
| NE-PLR-034 | Çoklu kaynak URL açılışı | plr-p05 |
| NE-PLR-035 | Decode performerans bütçesi | plr-p05 |
| NE-PLR-036 | WASAPI Exclusive bit-perfect yolu | plr-p06 |
| NE-PLR-037 | Bit-perfect kapsamı (yalnız kayıpsız) | plr-p06 |
| NE-PLR-038 | Format negosyasyon sırası (32f→32i→24-in-32) | plr-p06 |
| NE-PLR-039 | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` kurtarma | plr-p06 |
| NE-PLR-040 | EQ flat + limiter bypass bit-perfect ön koşulu | plr-p06 |
| NE-PLR-041 | Hi-res 192 kHz / 32-bit float | plr-p06 |
| NE-PLR-042 | ASIO yolu (lisans U1'e bağlı) | plr-p06 |
| NE-PLR-043 | Bit-perfect rozeti `[ÖNERİ]` | plr-p06 |
| NE-PLR-044 | DSD/DoP çıkışı | plr-p06 |
| NE-PLR-045 | Shared mod rozet/uyarı ayrımı | plr-p06 |
| NE-PLR-046 | Exclusive↔shared otomatik geçiş | plr-p06 |
| NE-PLR-047 | Cihaz hızı uyuşmazlığı (F3 sırası) | plr-p06 |
| NE-PLR-048 | Hot-plug kaybı davranışı | plr-p06 |
| NE-PLR-049 | Bit-perfect CI hattı (dosya→Null→hash) | plr-p06 |
| NE-PLR-050 | 12 kombinasyon matrisi | plr-p06 |
| NE-PLR-051 | OS ses efektlerinin devre dışı olması (exclusive) | plr-p06 |
| NE-PLR-052 | Null-test CI guard (inverted) | plr-p06 |
| NE-PLR-053 | Exclusive kilit tercihi (kullanıcı) | plr-p06 |
| NE-PLR-054 | Uzun süreli çalışma kararlılığı (bit-perfect) | plr-p06 |
| NE-PLR-055 | Dither (16-bit çıkış) | plr-p06 |
| NE-PLR-056 | MP3 (LAME/Xing) gapless | plr-p06 |
| NE-PLR-057 | FLAC gapless | plr-p06 |
| NE-PLR-058 | Opus gapless | plr-p06 |
| NE-PLR-059 | DSD gapless (DoP/DSF marker continuity) | plr-p06 |
| NE-PLR-060 | Dosya→dosya geçişte kesintisizlik ölçümü | plr-p06 |
| NE-PLR-061 | Queue prefetch boyutu | plr-p06 |
| NE-PLR-062 | Karışık format queue (FLAC→MP3→FLAC) | plr-p06 |
| NE-PLR-063 | Shuffle + gapless | plr-p06 |
| NE-PLR-064 | Repeat (tek/liste) gapless | plr-p06 |
| NE-PLR-065 | Son dosya→STOPPED | plr-p06 |
| NE-PLR-066 | Gapless bozuk tag fallback | plr-p06 |
| NE-PLR-067 | Crossfade ayrımı | plr-p06 |
| NE-PLR-068 | Gapless CI vektör kütüphanesi | plr-p06 |
| NE-PLR-069 | Konum sayaçları gapless uyumu | plr-p06 |
| NE-PLR-070 | DSD→PCM queue geçişi | plr-p06 |
| NE-PLR-071 | 5.1 kaynaklı gapless | plr-p06 |
| NE-PLR-072 | Seek sonrası gapless bozulmaz | plr-p06 |
| NE-PLR-073 | Pause/Resume arası gapless | plr-p06 |
| NE-PLR-074 | Dosya→dosya: 0 frame boşluk tanımı | plr-p06 |
| NE-PLR-075 | Radio gapless (akış→akış) — reconnect dahil | plr-p06 |
| NE-PLR-076 | libplacebo render yolu | plr-p07 |
| NE-PLR-077 | Master clock = cihaz (ses) | plr-p07 |
| NE-PLR-078 | A/V sync P95 ≤20 ms (kapı 5.11) | plr-p07 |
| NE-PLR-079 | VRAM bütçesi (~400-700 MB) | plr-p07 |
| NE-PLR-080 | VRAM tükenme merdiveni (V0-V5) | plr-p07 |
| NE-PLR-081 | VRAM ölçüm altyapısı (GTX 1660 bench) | plr-p07 |
| NE-PLR-082 | Sistem RAM ≤400 MB (video dahil) | plr-p07 |
| NE-PLR-083 | Gecikme <20 ms bütçesi | plr-p07 |
| NE-PLR-084 | Kapı 5.10 (gecikme ölçüm kapısı) | plr-p07 |
| NE-PLR-085 | Kapı 5.13 (hostile input fuzzing) | plr-p07 |
| NE-PLR-086 | Kapı 5.12 (gapless format vektörü) | plr-p07 |
| NE-PLR-087 | Kapı 5.1 (bit-perfect null-test + inverted guard) | plr-p07 |
| NE-PLR-088 | Video format kapsamı | plr-p07 |
| NE-PLR-089 | Video modu geçişi (ses↔video) | plr-p07 |
| NE-PLR-090 | Pencere/video senkronu (drift ölçümü) | plr-p07 |
| NE-PLR-091 | Ses kalitesi: SNR >105 dB | plr-p07 |
| NE-PLR-092 | THD+N <%0.005 @1 kHz | plr-p07 |
| NE-PLR-093 | 32-bit float DSP zinciri (uçtan uca) | plr-p07 |
| NE-PLR-094 | mpv/libmpv reddi korunur | plr-p07 |
| NE-PLR-095 | Video performans bütçesi | plr-p07 |
| NE-PLR-096 | ICY/Shoutcast v1+v2 özel tuner | plr-p07 |
| NE-PLR-097 | HLS (m3u8) FFmpeg gateway | plr-p07 |
| NE-PLR-098 | DASH Aşama 2 | plr-p07 |
| NE-PLR-099 | Codec matrisi (AAC-LC/HE-AAC/Opus/MP3/FLAC) | plr-p07 |
| NE-PLR-100 | FDK-AAC yasağı | plr-p07 |
| NE-PLR-101 | StreamTitle sanitizasyonu (kural 7) | plr-p07 |
| NE-PLR-102 | StreamUrl hyperlink yasağı | plr-p07 |
| NE-PLR-103 | Parser limitleri (kural 8) | plr-p07 |
| NE-PLR-104 | URL Validator tek kapı (kural 6) | plr-p07 |
| NE-PLR-105 | Reconnect backoff (1s→30s cap, ±%20 jitter) | plr-p07 |
| NE-PLR-106 | 3 başarısız → ERROR | plr-p07 |
| NE-PLR-107 | Prebuffer 2-4 s (reconnect kutusu) | plr-p07 |
| NE-PLR-108 | Fade-in 20 ms (reconnect) | plr-p07 |
| NE-PLR-109 | Ring STARVE/OVERFLOW davranışları | plr-p07 |
| NE-PLR-110 | TunerThread non-RT ayrımı | plr-p07 |
| NE-PLR-111 | R128 sidechain non-RT | plr-p07 |
| NE-PLR-112 | Metadata event zinciri (sanitize → lock-free kuyruk → C ABI) | plr-p07 |
| NE-PLR-113 | Radyo kataloğu (SQLite, ADR-074) | plr-p07 |
| NE-PLR-114 | Radio Browser API keşfi (opsiyonel) | plr-p07 |
| NE-PLR-115 | İstasyon test aracı (URL test/validate) | plr-p07 |
| NE-PLR-116 | Son istasyon hatırlama | plr-p07 |
| NE-PLR-117 | Plasyon listesi UI (kategori/favori) | plr-p07 |
| NE-PLR-118 | 100 istasyon stres testi | plr-p07 |
| NE-PLR-119 | 10 istasyon paralel (tuning kuyruğu) | plr-p07 |
| NE-PLR-120 | Fuzz: HLS playlist + ICY corpus | plr-p07 |
| NE-PLR-121 | Bant sayısı seçimi (10/15/20/31) | plr-p08 |
| NE-PLR-122 | 31-band tam grid | plr-p08 |
| NE-PLR-123 | 10-band ISO alt küme | plr-p08 |
| NE-PLR-124 | 20-band (AIMP emsal aralığı) | plr-p08 |
| NE-PLR-125 | 15-band alt küme | plr-p08 |
| NE-PLR-126 | Biquad bütçesi sabitliği | plr-p08 |
| NE-PLR-127 | Ramp 5-20 ms (click yasağı) | plr-p08 |
| NE-PLR-128 | Aktif olmayan band bypass | plr-p08 |
| NE-PLR-129 | Preset biçimi (taşıma) | plr-p08 |
| NE-PLR-130 | Preset kaydet/yükle/sil/adlandır | plr-p08 |
| NE-PLR-131 | Fabrika preset seti | plr-p08 |
| NE-PLR-132 | Flat/Bypass düğmesi | plr-p08 |
| NE-PLR-133 | Preamp slider'ı | plr-p08 |
| NE-PLR-134 | Bant frekansı değiştirilemez | plr-p08 |
| NE-PLR-135 | Parametrik mod (ayrı sekme, M5+) | plr-p08 |
| NE-PLR-136 | Radyoda EQ aktif | plr-p08 |
| NE-PLR-137 | EQ UI widget'ları | plr-p08 |
| NE-PLR-138 | EQ görselleştirme (yanıt eğrisi) | plr-p08 |
| NE-PLR-139 | neva-eq ayrı pencere | plr-p08 |
| NE-PLR-140 | EQ test vektörleri (radyo+dosya) | plr-p08 |
| NE-PLR-141 | LUFS hedefleme (normalizasyon) | plr-p08 |
| NE-PLR-142 | Crossfade ≠ gapless ayrımı (EQ etkileşimi) | plr-p08 |
| NE-PLR-143 | Cihaz-bağımlı EQ hatırlama | plr-p08 |
| NE-PLR-144 | Preset import/export (dosya) | plr-p08 |
| NE-PLR-145 | Grid teyit işi (15/20-band kapanışı) | plr-p08 |
| NE-PLR-146 | Yerel kütüphane (SQLite) | plr-p08 |
| NE-PLR-147 | Çevrimdışı öncelik (kütüphane) | plr-p08 |
| NE-PLR-148 | Queue düzenleme | plr-p08 |
| NE-PLR-149 | Durum makinesi geçişleri (ADR-019 §1.4) | plr-p08 |
| NE-PLR-150 | LOADING durumu UX | plr-p08 |
| NE-PLR-151 | ERROR durumu UX | plr-p08 |
| NE-PLR-152 | Transport kontrolleri | plr-p08 |
| NE-PLR-153 | Cihaz seçimi UI (liste + varsayılan) | plr-p08 |
| NE-PLR-154 | Fallback bilgilendirmesi (UI) | plr-p08 |
| NE-PLR-155 | Metadata gösterimi | plr-p08 |
| NE-PLR-156 | Zamanlayıcı (uyku modu) `[ÖNERİ]` | plr-p08 |
| NE-PLR-157 | Sıcaklık/kulak zarar koruması `[ÖNERİ]` | plr-p08 |
| NE-PLR-158 | Klavye kısayolları | plr-p08 |
| NE-PLR-159 | Tray/sistem tepsisi `[ÖNERİ]` | plr-p08 |
| NE-PLR-160 | Çoklu pencere (main + eq + matrix) | plr-p08 |
| NE-PLR-161 | UI erişilebilirliği (klavye/focus) | plr-p08 |
| NE-PLR-162 | Karanlık/aydınlık tema `[ÖNERİ]` | plr-p08 |
| NE-PLR-163 | UI performans (oynatma sırasında) | plr-p08 |
| NE-PLR-164 | Config şeması sürümleme | plr-p08 |
| NE-PLR-165 | Crash kurtarma (yeniden başlatma) | plr-p08 |
| NE-PLR-166 | OWASP tabanlı tehdit modeli | plr-p09 |
| NE-PLR-167 | SSRF/özel-IP reddi (URL validator) | plr-p09 |
| NE-PLR-168 | Zincirleme redirect ≤5 (güvenlik sürümü) | plr-p09 |
| NE-PLR-169 | HTTPS zorunlu + sertifika doğrulaması | plr-p09 |
| NE-PLR-170 | HTML-escape (StreamTitle/StreamUrl/render) | plr-p09 |
| NE-PLR-171 | 256 bayt cap + UTF-8 doğrulama (metadata) | plr-p09 |
| NE-PLR-172 | XML DTD/kök XXE reddi (ICY v2) | plr-p09 |
| NE-PLR-173 | HLS segment cap (50 MB) + entry cap (1000) + derinlik (≤1) | plr-p09 |
| NE-PLR-174 | Protocol whitelist (dosya/concat/crypto reddi) | plr-p09 |
| NE-PLR-175 | Config/preset dosyalarında güvenli ayrıştırma | plr-p09 |
| NE-PLR-176 | Katalog/SQLite injection koruması | plr-p09 |
| NE-PLR-177 | Yol güvenliği (path traversal) | plr-p09 |
| NE-PLR-178 | FFI sınırında clamp (engine'le ortak) | plr-p09 |
| NE-PLR-179 | Tek instance + IPC yüzeyi (neva-host) | plr-p09 |
| NE-PLR-180 | Supply chain (bağımlılık taraması) | plr-p09 |
| NE-PLR-181 | RAM ≤400 MB bütçesi (ölçümü) | plr-p09 |
| NE-PLR-182 | CPU bütçesi (ses-only) | plr-p09 |
| NE-PLR-183 | Latency <20 ms (ölçüm) | plr-p09 |
| NE-PLR-184 | Kapı 5.1 (bit-perfect) — player koşulları | plr-p09 |
| NE-PLR-185 | Kapı 5.10 (gecikme) | plr-p09 |
| NE-PLR-186 | Kapı 5.11 (A/V sync P95 ≤20 ms) | plr-p09 |
| NE-PLR-187 | Kapı 5.12 (gapless vektörleri) | plr-p09 |
| NE-PLR-188 | Kapı 5.13 (fuzz) | plr-p09 |
| NE-PLR-189 | RT stres (player:UI spam + uzun süre) | plr-p09 |
| NE-PLR-190 | Leak (1000 cycle play/stop + katalog tarama) | plr-p09 |
| NE-PLR-191 | TSan (çoklu-thread: UI + tuner + control spam) | plr-p09 |
| NE-PLR-192 | Radyo stres vektörleri (ADR-106 §5.2 Ş1-Ş7) | plr-p09 |
| NE-PLR-193 | UI a11y checklist (klavye/focus/renk) | plr-p09 |
| NE-PLR-194 | Yerelleştirme testi (TR/EN) — karara bağlı | plr-p09 |
| NE-PLR-195 | Metrik regresyon kapısı (%10) | plr-p09 |
| NE-PLR-196 | Test raporu şablonu (release) | plr-p09 |
| NE-PLR-197 | Determinism (2× çalıştırma hash) | plr-p09 |
| NE-PLR-198 | ABI sembol denetimi (player birleşimi) | plr-p09 |
| NE-PLR-199 | Kontrat testi (durum makinesi + event zinciri) | plr-p09 |
| NE-PLR-200 | Flaky test yasağı | plr-p09 |
| NE-PLR-201 | C ABI minor sürüm genişlemesi (E1-E10) | plr-p09 |
| NE-PLR-202 | FFI dilleri (Python ctypes · C# P/Invoke · 25 dil) | plr-p09 |
| NE-PLR-203 | "25 dil" tanımı (metrik) | plr-p09 |
| NE-PLR-204 | neva-mcp (MCP sunucu) | plr-p09 |
| NE-PLR-205 | neva-remote (VoiceMeeterRemote/VBAN emsal) | plr-p09 |
| NE-PLR-206 | Web UI (UPnify emsal) — U4 kararı | plr-p09 |
| NE-PLR-207 | Katalog dışa aktarma (CSV/M3U) `[ÖNERI]` | plr-p09 |
| NE-PLR-208 | M3U/M3U8 çalma listesi içe aktarma (yerel) | plr-p09 |
| NE-PLR-209 | Hotkey/global kısayol (OS kaydı) `[ÖNERİ]` | plr-p09 |
| NE-PLR-210 | Bildirim (OS toast) `[ÖNERİ]` | plr-p09 |
| NE-PLR-211 | Plugin yüzeyi yok (kapsam kararı) | plr-p09 |
| NE-PLR-212 | Sürücü/eklenti kurulumu yok (taşıma) | plr-p09 |
| NE-PLR-213 | Sürüm/telemetri ağ çağrısı yok | plr-p09 |
| NE-PLR-214 | Log hijyeni (secret/URL sızıntısı) | plr-p09 |
| NE-PLR-215 | Dış API envanteri (tek tablo) | plr-p09 |

## §21 Ek B — Test Senaryosu İndeksi (Player · 215 senaryo)

> **§11'deki TC başlıklarından otomatik çıkarıldı.** Sütunlar: **TC · Başlık · Eşleşen gereksinim · Kaynak parça**.
> `TC-PLR-NNN` ↔ `NE-PLR-NNN` aynı numarayı taşır (§11.0 kuralı).

| TC | Başlık | Gereksinim | Kaynak parça |
|---|---|---|---|
| TC-PLR-001 | Ortak çekirdek + ince adapter | NE-PLR-001 | plr-p11 |
| TC-PLR-002 | C ABI üzerinden engine kullanımı | NE-PLR-002 | plr-p11 |
| TC-PLR-003 | api_version handshake | NE-PLR-003 | plr-p11 |
| TC-PLR-004 | Durum makinesi kontratı | NE-PLR-004 | plr-p11 |
| TC-PLR-005 | Event callback (E1) | NE-PLR-005 | plr-p11 |
| TC-PLR-006 | position/duration + seek erişimi | NE-PLR-006 | plr-p11 |
| TC-PLR-007 | Cihaz listesi + seçim (E8) | NE-PLR-007 | plr-p11 |
| TC-PLR-008 | UI↔RT ayrımı | NE-PLR-008 | plr-p11 |
| TC-PLR-009 | UI toolkit kararı kapısı | NE-PLR-009 | plr-p11 |
| TC-PLR-010 | Config kalıcılığı | NE-PLR-010 | plr-p11 |
| TC-PLR-011 | Engine sürüm birleşimi | NE-PLR-011 | plr-p11 |
| TC-PLR-012 | neva-eq / neva-matrix süreç ayrımı | NE-PLR-012 | plr-p11 |
| TC-PLR-013 | Çevrimdışı öncelik | NE-PLR-013 | plr-p11 |
| TC-PLR-014 | Yerelleştirme kapsam notu | NE-PLR-014 | plr-p11 |
| TC-PLR-015 | Kardeş PRD sınırı | NE-PLR-015 | plr-p11 |
| TC-PLR-016 | Decode matrisi | NE-PLR-016 | plr-p11 |
| TC-PLR-017 | Yerel demux önceliği | NE-PLR-017 | plr-p11 |
| TC-PLR-018 | soxr producer-side | NE-PLR-018 | plr-p11 |
| TC-PLR-019 | soxr lisansı (LGPL-2.1) | NE-PLR-019 | plr-p11 |
| TC-PLR-020 | FFmpeg LGPL build | NE-PLR-020 | plr-p11 |
| TC-PLR-021 | Protocol whitelist | NE-PLR-021 | plr-p11 |
| TC-PLR-022 | HTTP09Allowed=0 | NE-PLR-022 | plr-p11 |
| TC-PLR-023 | Redirect ≤5 + her hop validator | NE-PLR-023 | plr-p11 |
| TC-PLR-024 | Timeout 10 sn | NE-PLR-024 | plr-p11 |
| TC-PLR-025 | TLS doğrulama (mbedTLS) | NE-PLR-025 | plr-p11 |
| TC-PLR-026 | decode → float32 ring | NE-PLR-026 | plr-p11 |
| TC-PLR-027 | 24-bit kayıpsız yol | NE-PLR-027 | plr-p11 |
| TC-PLR-028 | Queue prefetch hazır olma | NE-PLR-028 | plr-p11 |
| TC-PLR-029 | Bozuk dosya dayanıklılığı | NE-PLR-029 | plr-p11 |
| TC-PLR-030 | Tek transport (dosya ↔ radyo) | NE-PLR-030 | plr-p11 |
| TC-PLR-031 | Metadata okuma | NE-PLR-031 | plr-p11 |
| TC-PLR-032 | Kapak görseli güvenliği | NE-PLR-032 | plr-p11 |
| TC-PLR-033 | ReplayGain davranışı | NE-PLR-033 | plr-p11 |
| TC-PLR-034 | open_source URI ayrıştırma | NE-PLR-034 | plr-p11 |
| TC-PLR-035 | Decode CPU bench | NE-PLR-035 | plr-p11 |
| TC-PLR-036 | Bit-perfect WASAPI Exclusive | NE-PLR-036 | plr-p11 |
| TC-PLR-037 | Lossy kapsam ayrımı | NE-PLR-037 | plr-p11 |
| TC-PLR-038 | Format negosyasyon sırası (N1) | NE-PLR-038 | plr-p11 |
| TC-PLR-039 | BUFFER_SIZE_NOT_ALIGNED kurtarma | NE-PLR-039 | plr-p11 |
| TC-PLR-040 | EQ aktifken hash engeli (inverted guard) | NE-PLR-040 | plr-p11 |
| TC-PLR-041 | Hi-res 192k/32f uçtan uca | NE-PLR-041 | plr-p11 |
| TC-PLR-042 | ASIO yolu (şartlı U1) | NE-PLR-042 | plr-p11 |
| TC-PLR-043 | Bit-perfect rozet üç koşulu | NE-PLR-043 | plr-p11 |
| TC-PLR-044 | DoP marker continuity | NE-PLR-044 | plr-p11 |
| TC-PLR-045 | Shared mod rozet/uyarı | NE-PLR-045 | plr-p11 |
| TC-PLR-046 | Otomatik exclusive→shared geçiş | NE-PLR-046 | plr-p11 |
| TC-PLR-047 | Hız uyuşmazlığı sırası (F3) | NE-PLR-047 | plr-p11 |
| TC-PLR-048 | Hot-plug kaybı | NE-PLR-048 | plr-p11 |
| TC-PLR-049 | CI hash yolu (Null) + loopback ayrımı | NE-PLR-049 | plr-p11 |
| TC-PLR-050 | 12 kombinasyon matrisi | NE-PLR-050 | plr-p11 |
| TC-PLR-051 | OS efektleri baypas (exclusive) | NE-PLR-051 | plr-p11 |
| TC-PLR-052 | Null-test guard (radyo/lossy engeli) | NE-PLR-052 | plr-p11 |
| TC-PLR-053 | Exclusive tercihi (kullanıcı kilidi) | NE-PLR-053 | plr-p11 |
| TC-PLR-054 | 4 saat kararlılık | NE-PLR-054 | plr-p11 |
| TC-PLR-055 | Dither davranışı | NE-PLR-055 | plr-p11 |
| TC-PLR-056 | MP3 Xing gapless | NE-PLR-056 | plr-p11 |
| TC-PLR-057 | FLAC gapless | NE-PLR-057 | plr-p11 |
| TC-PLR-058 | Opus gapless | NE-PLR-058 | plr-p11 |
| TC-PLR-059 | DSD gapless (DoP) | NE-PLR-059 | plr-p11 |
| TC-PLR-060 | Sınır korelasyonu ±5 ms | NE-PLR-060 | plr-p11 |
| TC-PLR-061 | Prefetch penceresi (≥1 sn) | NE-PLR-061 | plr-p11 |
| TC-PLR-062 | Karışık format kuyruk | NE-PLR-062 | plr-p11 |
| TC-PLR-063 | Shuffle gapless | NE-PLR-063 | plr-p11 |
| TC-PLR-064 | Repeat + leak | NE-PLR-064 | plr-p11 |
| TC-PLR-065 | Son dosya → STOPPED | NE-PLR-065 | plr-p11 |
| TC-PLR-066 | Eksik tag fallback (uydurma yok) | NE-PLR-066 | plr-p11 |
| TC-PLR-067 | Crossfade ayrımı | NE-PLR-067 | plr-p11 |
| TC-PLR-068 | Gapless vektör kütüphanesi | NE-PLR-068 | plr-p11 |
| TC-PLR-069 | Konum sayaçları geçişi | NE-PLR-069 | plr-p11 |
| TC-PLR-070 | DSD→PCM geçişi | NE-PLR-070 | plr-p11 |
| TC-PLR-071 | 5.1 kuyruk geçişi | NE-PLR-071 | plr-p11 |
| TC-PLR-072 | Seek sonrası gapless | NE-PLR-072 | plr-p11 |
| TC-PLR-073 | Pause/sınır beklemesi | NE-PLR-073 | plr-p12 |
| TC-PLR-074 | Sınır boşluğu tanımı (0 frame) | NE-PLR-074 | plr-p12 |
| TC-PLR-075 | Radio stream→stream (reconnect dahil) | NE-PLR-075 | plr-p12 |
| TC-PLR-076 | libplacebo render (şartlı faz) | NE-PLR-076 | plr-p12 |
| TC-PLR-077 | Master clock = cihaz | NE-PLR-077 | plr-p12 |
| TC-PLR-078 | A/V sync P95 ≤20 ms (5.11) | NE-PLR-078 | plr-p12 |
| TC-PLR-079 | VRAM bütçesi ölçümü | NE-PLR-079 | plr-p12 |
| TC-PLR-080 | VRAM merdiveni (V0-V5) | NE-PLR-080 | plr-p12 |
| TC-PLR-081 | VRAM bench ortamı | NE-PLR-081 | plr-p12 |
| TC-PLR-082 | RAM ≤400 MB (video dahil bütçe) | NE-PLR-082 | plr-p12 |
| TC-PLR-083 | Gecikme bütçesi (blok sınırı) | NE-PLR-083 | plr-p12 |
| TC-PLR-084 | Kapı 5.10 raporu | NE-PLR-084 | plr-p12 |
| TC-PLR-085 | Kapı 5.13 (fuzz) | NE-PLR-085 | plr-p12 |
| TC-PLR-086 | Kapı 5.12 (gapless vektörleri) | NE-PLR-086 | plr-p12 |
| TC-PLR-087 | Kapı 5.1 + inverted guard (player) | NE-PLR-087 | plr-p12 |
| TC-PLR-088 | Video format kapsamı | NE-PLR-088 | plr-p12 |
| TC-PLR-089 | Ses↔video mod geçişi | NE-PLR-089 | plr-p12 |
| TC-PLR-090 | Pencere/video drift | NE-PLR-090 | plr-p12 |
| TC-PLR-091 | SNR >105 dB | NE-PLR-091 | plr-p12 |
| TC-PLR-092 | THD+N <%0.005 | NE-PLR-092 | plr-p12 |
| TC-PLR-093 | Uçtan uca float32 | NE-PLR-093 | plr-p12 |
| TC-PLR-094 | mpv yokluğu | NE-PLR-094 | plr-p12 |
| TC-PLR-095 | Video bench (şartlı) | NE-PLR-095 | plr-p12 |
| TC-PLR-096 | ICY v1+v2 ayrıştırma | NE-PLR-096 | plr-p12 |
| TC-PLR-097 | HLS oynatma | NE-PLR-097 | plr-p12 |
| TC-PLR-098 | DASH (şartlı faz 2) | NE-PLR-098 | plr-p12 |
| TC-PLR-099 | Codec matrisi | NE-PLR-099 | plr-p12 |
| TC-PLR-100 | FDK-AAC yasağı | NE-PLR-100 | plr-p12 |
| TC-PLR-101 | StreamTitle sanitizasyon | NE-PLR-101 | plr-p12 |
| TC-PLR-102 | StreamUrl tıklanamaz | NE-PLR-102 | plr-p12 |
| TC-PLR-103 | Parser limitleri (5.13 Ş8) | NE-PLR-103 | plr-p12 |
| TC-PLR-104 | URL Validator tek kapı | NE-PLR-104 | plr-p12 |
| TC-PLR-105 | Backoff (1s→30s ±%20) | NE-PLR-105 | plr-p12 |
| TC-PLR-106 | 3 başarısız → ERROR | NE-PLR-106 | plr-p12 |
| TC-PLR-107 | Prebuffer 2-4 s | NE-PLR-107 | plr-p12 |
| TC-PLR-108 | Fade-in 20 ms | NE-PLR-108 | plr-p12 |
| TC-PLR-109 | STARVE/OVERFLOW davranışı | NE-PLR-109 | plr-p12 |
| TC-PLR-110 | TunerThread non-RT | NE-PLR-110 | plr-p12 |
| TC-PLR-111 | R128 sidechain non-RT | NE-PLR-111 | plr-p12 |
| TC-PLR-112 | Metadata zinciri (spam dayanıklılık) | NE-PLR-112 | plr-p12 |
| TC-PLR-113 | Radyo kataloğu (ADR-074) | NE-PLR-113 | plr-p12 |
| TC-PLR-114 | Radio Browser keşfi (şartlı ağ) | NE-PLR-114 | plr-p12 |
| TC-PLR-115 | İstasyon test aracı | NE-PLR-115 | plr-p12 |
| TC-PLR-116 | Son istasyon hatırlama | NE-PLR-116 | plr-p12 |
| TC-PLR-117 | Katalog UI (arama/favori) | NE-PLR-117 | plr-p12 |
| TC-PLR-118 | 100 istasyon stres (Ş3/Ş6) | NE-PLR-118 | plr-p12 |
| TC-PLR-119 | 10 paralel tuning (Ş2) | NE-PLR-119 | plr-p12 |
| TC-PLR-120 | Fuzz: HLS + ICY corpus (Ş7) | NE-PLR-120 | plr-p12 |
| TC-PLR-121 | Bant sayısı seçimi (E5) | NE-PLR-121 | plr-p12 |
| TC-PLR-122 | 31-band frekans doğruluğu | NE-PLR-122 | plr-p12 |
| TC-PLR-123 | 10-band ISO + VLC ayrımı | NE-PLR-123 | plr-p12 |
| TC-PLR-124 | 20-band (teyitli aralık) | NE-PLR-124 | plr-p12 |
| TC-PLR-125 | 15-band (teyitli) | NE-PLR-125 | plr-p12 |
| TC-PLR-126 | Biquad bütçesi sabitliği (E5) | NE-PLR-126 | plr-p12 |
| TC-PLR-127 | Ramp 5-20 ms (pop analizi) | NE-PLR-127 | plr-p12 |
| TC-PLR-128 | Pasif band bypass (CPU) | NE-PLR-128 | plr-p12 |
| TC-PLR-129 | Preset taşıma biçimi | NE-PLR-129 | plr-p12 |
| TC-PLR-130 | Preset CRUD | NE-PLR-130 | plr-p12 |
| TC-PLR-131 | Fabrika preset seti | NE-PLR-131 | plr-p12 |
| TC-PLR-132 | Flat vs Bypass ayrımı | NE-PLR-132 | plr-p12 |
| TC-PLR-133 | Preamp + clip göstergesi | NE-PLR-133 | plr-p12 |
| TC-PLR-134 | Frekans etiketi salt-okunur | NE-PLR-134 | plr-p12 |
| TC-PLR-135 | Parametrik mod ayrımı (şartlı faz) | NE-PLR-135 | plr-p12 |
| TC-PLR-136 | Radyoda EQ aktif | NE-PLR-136 | plr-p12 |
| TC-PLR-137 | EQ widget performansı + keyboard | NE-PLR-137 | plr-p12 |
| TC-PLR-138 | Yanıt eğrisi çizimi (şartlı) | NE-PLR-138 | plr-p12 |
| TC-PLR-139 | neva-eq entegrasyonu (şartlı) | NE-PLR-139 | plr-p12 |
| TC-PLR-140 | EQ vektör testleri (Ş5) | NE-PLR-140 | plr-p12 |
| TC-PLR-141 | LUFS hedefleme (şartlı) | NE-PLR-141 | plr-p12 |
| TC-PLR-142 | Crossfade↔EQ state geçişi (şartlı) | NE-PLR-142 | plr-p12 |
| TC-PLR-143 | Cihaza bağlı EQ hatırlama (şartlı) | NE-PLR-143 | plr-p12 |
| TC-PLR-144 | Preset import/export (+dönüştürücü şartlı) | NE-PLR-144 | plr-p12 |
| TC-PLR-145 | Flat preset hash yolu (§11.2 ön koşulu) | NE-PLR-145 | plr-p13 |
| TC-PLR-146 | Preset yedeği/alma (atomik write) | NE-PLR-146 | plr-p13 |
| TC-PLR-147 | 10/15/20/31 grid üretim doğruluğu | NE-PLR-147 | plr-p13 |
| TC-PLR-148 | Grid ↔ EqChain band maskesi hizası | NE-PLR-148 | plr-p13 |
| TC-PLR-149 | Durum makinesi kontratı (otomatik) | NE-PLR-149 | plr-p13 |
| TC-PLR-150 | Konum/uzunluk eventleri (E3/E4/E7) | NE-PLR-150 | plr-p13 |
| TC-PLR-151 | ERROR durumunda tekrar-dene/geri-ok | NE-PLR-151 | plr-p13 |
| TC-PLR-152 | Geçersiz girdi hata durumu (form) | NE-PLR-152 | plr-p13 |
| TC-PLR-153 | Bozuk dosya → uyarı + atla | NE-PLR-153 | plr-p13 |
| TC-PLR-154 | Desteklenmeyen format mesajı (3) | NE-PLR-154 | plr-p13 |
| TC-PLR-155 | Engine bağlantısı koptu (ERROR-3 köprüsü) | NE-PLR-155 | plr-p13 |
| TC-PLR-156 | Dahili hata: log + geri kurtarma | NE-PLR-156 | plr-p13 |
| TC-PLR-157 | İlk-iş akışı + ön tampon (LOADING) | NE-PLR-157 | plr-p13 |
| TC-PLR-158 | 3 başarısız → teklik mesajı | NE-PLR-158 | plr-p13 |
| TC-PLR-159 | Katalog dahili hata (bozuk şema) | NE-PLR-159 | plr-p13 |
| TC-PLR-160 | Fallback sembolü (UI görseli) | NE-PLR-160 | plr-p13 |
| TC-PLR-161 | Klavye erişimi (transport + EQ) | NE-PLR-161 | plr-p13 |
| TC-PLR-162 | Kurgu dışı açılış (cold start) | NE-PLR-162 | plr-p13 |
| TC-PLR-163 | 24 saat kararlılık + 1000 cycle | NE-PLR-163 | plr-p13 |
| TC-PLR-164 | Sağlam kapanış → son durum geri | NE-PLR-164 | plr-p13 |
| TC-PLR-165 | UI↔engine state kayması (tekrar senkron) | NE-PLR-165 | plr-p13 |
| TC-PLR-166 | OWASP tehdit tablosu gözden geçirme | NE-PLR-166 | plr-p13 |
| TC-PLR-167 | SSRF özel-IP reddi | NE-PLR-167 | plr-p13 |
| TC-PLR-168 | Zincirleme redirect ≤5 (güvenlik) | NE-PLR-168 | plr-p13 |
| TC-PLR-169 | TLS sertifika reddi yolları | NE-PLR-169 | plr-p13 |
| TC-PLR-170 | HTML-escape (tüm render yolları) | NE-PLR-170 | plr-p13 |
| TC-PLR-171 | 256 bayt cap + UTF-8 doğrulama | NE-PLR-171 | plr-p13 |
| TC-PLR-172 | XXE reddi (ICY v2 XML) | NE-PLR-172 | plr-p13 |
| TC-PLR-173 | HLS kapıları (50 MB / 1000 entry / derinlik 1) | NE-PLR-173 | plr-p13 |
| TC-PLR-174 | Protocol whitelist bypass denemeleri | NE-PLR-174 | plr-p13 |
| TC-PLR-175 | Preset/config parser dayanıklılığı | NE-PLR-175 | plr-p13 |
| TC-PLR-176 | SQL injection + Türkçe adlar | NE-PLR-176 | plr-p13 |
| TC-PLR-177 | Path traversal (tarama kökü) | NE-PLR-177 | plr-p13 |
| TC-PLR-178 | FFI clamp (player birleşimi) | NE-PLR-178 | plr-p13 |
| TC-PLR-179 | Tek instance + IPC kimlik reddi | NE-PLR-179 | plr-p13 |
| TC-PLR-180 | Supply chain taraması | NE-PLR-180 | plr-p13 |
| TC-PLR-181 | RAM ≤400 MB ölçümü | NE-PLR-181 | plr-p13 |
| TC-PLR-182 | CPU bütçesi (EQ payı) | NE-PLR-182 | plr-p13 |
| TC-PLR-183 | Latency <20 ms (player ölçümü) | NE-PLR-183 | plr-p13 |
| TC-PLR-184 | Kapı 5.1 (player koşulları + guard) | NE-PLR-184 | plr-p13 |
| TC-PLR-185 | Kapı 5.10 rapor arşivi | NE-PLR-185 | plr-p13 |
| TC-PLR-186 | Kapı 5.11 (A/V sync) — koşul kontrolü | NE-PLR-186 | plr-p13 |
| TC-PLR-187 | Kapı 5.12 (gapless) — tam vektör | NE-PLR-187 | plr-p13 |
| TC-PLR-188 | Kapı 5.13 (fuzz) — sayım raporu | NE-PLR-188 | plr-p13 |
| TC-PLR-189 | UI spam + uzun süre stres | NE-PLR-189 | plr-p13 |
| TC-PLR-190 | Leak (1000 cycle + tarama) | NE-PLR-190 | plr-p13 |
| TC-PLR-191 | TSan (UI + tuner + control spam) | NE-PLR-191 | plr-p13 |
| TC-PLR-192 | Radyo stres Ş1-Ş7 raporu | NE-PLR-192 | plr-p13 |
| TC-PLR-193 | UI a11y checklist | NE-PLR-193 | plr-p13 |
| TC-PLR-194 | Yerelleştirme testi (şartlı) | NE-PLR-194 | plr-p13 |
| TC-PLR-195 | Metrik regresyon etiketi (%10) | NE-PLR-195 | plr-p13 |
| TC-PLR-196 | Release test raporu şablonu | NE-PLR-196 | plr-p13 |
| TC-PLR-197 | Determinism (2× hash) | NE-PLR-197 | plr-p13 |
| TC-PLR-198 | ABI sembol denetimi (player) | NE-PLR-198 | plr-p13 |
| TC-PLR-199 | Event zinciri kontratı (E1-E10) | NE-PLR-199 | plr-p13 |
| TC-PLR-200 | Flaky test yasağı (retry=0) | NE-PLR-200 | plr-p13 |
| TC-PLR-201 | ABI minor genişleme (E1-E10) | NE-PLR-201 | plr-p13 |
| TC-PLR-202 | Python ctypes smoke | NE-PLR-202 | plr-p13 |
| TC-PLR-203 | "25 dil" tanımı kararı | NE-PLR-203 | plr-p13 |
| TC-PLR-204 | neva-mcp (şartlı) | NE-PLR-204 | plr-p13 |
| TC-PLR-205 | neva-remote (şartlı) | NE-PLR-205 | plr-p13 |
| TC-PLR-206 | Web UI (şartlı U4) | NE-PLR-206 | plr-p13 |
| TC-PLR-207 | Katalog dışa aktarma (CSV/M3U) | NE-PLR-207 | plr-p13 |
| TC-PLR-208 | Yerel M3U içe aktarma | NE-PLR-208 | plr-p13 |
| TC-PLR-209 | Hotkey kaydı/geri alma (şartlı) | NE-PLR-209 | plr-p13 |
| TC-PLR-210 | Bildirim sanitizasyonu (şartlı) | NE-PLR-210 | plr-p13 |
| TC-PLR-211 | Plugin loader yokluğu (statik) | NE-PLR-211 | plr-p13 |
| TC-PLR-212 | Sürücü kurulumu yok (first-run) | NE-PLR-212 | plr-p13 |
| TC-PLR-213 | Gizli ağ çağrısı yok | NE-PLR-213 | plr-p13 |
| TC-PLR-214 | Log hijyeni | NE-PLR-214 | plr-p13 |
| TC-PLR-215 | Dış API envanteri denetimi | NE-PLR-215 | plr-p13 |

## §21 Ek C — Gereksinim → Test İzlenebilirlik Matrisi (Player)

> **Otomatik üretildi** (§21 Ek B ters çevrildi). 215 gereksinimin eşleşen test(leri).

| Gereksinim | Eşleşen test(ler) |
|---|---|
| NE-PLR-001 | TC-PLR-001 |
| NE-PLR-002 | TC-PLR-002 |
| NE-PLR-003 | TC-PLR-003 |
| NE-PLR-004 | TC-PLR-004 |
| NE-PLR-005 | TC-PLR-005 |
| NE-PLR-006 | TC-PLR-006 |
| NE-PLR-007 | TC-PLR-007 |
| NE-PLR-008 | TC-PLR-008 |
| NE-PLR-009 | TC-PLR-009 |
| NE-PLR-010 | TC-PLR-010 |
| NE-PLR-011 | TC-PLR-011 |
| NE-PLR-012 | TC-PLR-012 |
| NE-PLR-013 | TC-PLR-013 |
| NE-PLR-014 | TC-PLR-014 |
| NE-PLR-015 | TC-PLR-015 |
| NE-PLR-016 | TC-PLR-016 |
| NE-PLR-017 | TC-PLR-017 |
| NE-PLR-018 | TC-PLR-018 |
| NE-PLR-019 | TC-PLR-019 |
| NE-PLR-020 | TC-PLR-020 |
| NE-PLR-021 | TC-PLR-021 |
| NE-PLR-022 | TC-PLR-022 |
| NE-PLR-023 | TC-PLR-023 |
| NE-PLR-024 | TC-PLR-024 |
| NE-PLR-025 | TC-PLR-025 |
| NE-PLR-026 | TC-PLR-026 |
| NE-PLR-027 | TC-PLR-027 |
| NE-PLR-028 | TC-PLR-028 |
| NE-PLR-029 | TC-PLR-029 |
| NE-PLR-030 | TC-PLR-030 |
| NE-PLR-031 | TC-PLR-031 |
| NE-PLR-032 | TC-PLR-032 |
| NE-PLR-033 | TC-PLR-033 |
| NE-PLR-034 | TC-PLR-034 |
| NE-PLR-035 | TC-PLR-035 |
| NE-PLR-036 | TC-PLR-036 |
| NE-PLR-037 | TC-PLR-037 |
| NE-PLR-038 | TC-PLR-038 |
| NE-PLR-039 | TC-PLR-039 |
| NE-PLR-040 | TC-PLR-040 |
| NE-PLR-041 | TC-PLR-041 |
| NE-PLR-042 | TC-PLR-042 |
| NE-PLR-043 | TC-PLR-043 |
| NE-PLR-044 | TC-PLR-044 |
| NE-PLR-045 | TC-PLR-045 |
| NE-PLR-046 | TC-PLR-046 |
| NE-PLR-047 | TC-PLR-047 |
| NE-PLR-048 | TC-PLR-048 |
| NE-PLR-049 | TC-PLR-049 |
| NE-PLR-050 | TC-PLR-050 |
| NE-PLR-051 | TC-PLR-051 |
| NE-PLR-052 | TC-PLR-052 |
| NE-PLR-053 | TC-PLR-053 |
| NE-PLR-054 | TC-PLR-054 |
| NE-PLR-055 | TC-PLR-055 |
| NE-PLR-056 | TC-PLR-056 |
| NE-PLR-057 | TC-PLR-057 |
| NE-PLR-058 | TC-PLR-058 |
| NE-PLR-059 | TC-PLR-059 |
| NE-PLR-060 | TC-PLR-060 |
| NE-PLR-061 | TC-PLR-061 |
| NE-PLR-062 | TC-PLR-062 |
| NE-PLR-063 | TC-PLR-063 |
| NE-PLR-064 | TC-PLR-064 |
| NE-PLR-065 | TC-PLR-065 |
| NE-PLR-066 | TC-PLR-066 |
| NE-PLR-067 | TC-PLR-067 |
| NE-PLR-068 | TC-PLR-068 |
| NE-PLR-069 | TC-PLR-069 |
| NE-PLR-070 | TC-PLR-070 |
| NE-PLR-071 | TC-PLR-071 |
| NE-PLR-072 | TC-PLR-072 |
| NE-PLR-073 | TC-PLR-073 |
| NE-PLR-074 | TC-PLR-074 |
| NE-PLR-075 | TC-PLR-075 |
| NE-PLR-076 | TC-PLR-076 |
| NE-PLR-077 | TC-PLR-077 |
| NE-PLR-078 | TC-PLR-078 |
| NE-PLR-079 | TC-PLR-079 |
| NE-PLR-080 | TC-PLR-080 |
| NE-PLR-081 | TC-PLR-081 |
| NE-PLR-082 | TC-PLR-082 |
| NE-PLR-083 | TC-PLR-083 |
| NE-PLR-084 | TC-PLR-084 |
| NE-PLR-085 | TC-PLR-085 |
| NE-PLR-086 | TC-PLR-086 |
| NE-PLR-087 | TC-PLR-087 |
| NE-PLR-088 | TC-PLR-088 |
| NE-PLR-089 | TC-PLR-089 |
| NE-PLR-090 | TC-PLR-090 |
| NE-PLR-091 | TC-PLR-091 |
| NE-PLR-092 | TC-PLR-092 |
| NE-PLR-093 | TC-PLR-093 |
| NE-PLR-094 | TC-PLR-094 |
| NE-PLR-095 | TC-PLR-095 |
| NE-PLR-096 | TC-PLR-096 |
| NE-PLR-097 | TC-PLR-097 |
| NE-PLR-098 | TC-PLR-098 |
| NE-PLR-099 | TC-PLR-099 |
| NE-PLR-100 | TC-PLR-100 |
| NE-PLR-101 | TC-PLR-101 |
| NE-PLR-102 | TC-PLR-102 |
| NE-PLR-103 | TC-PLR-103 |
| NE-PLR-104 | TC-PLR-104 |
| NE-PLR-105 | TC-PLR-105 |
| NE-PLR-106 | TC-PLR-106 |
| NE-PLR-107 | TC-PLR-107 |
| NE-PLR-108 | TC-PLR-108 |
| NE-PLR-109 | TC-PLR-109 |
| NE-PLR-110 | TC-PLR-110 |
| NE-PLR-111 | TC-PLR-111 |
| NE-PLR-112 | TC-PLR-112 |
| NE-PLR-113 | TC-PLR-113 |
| NE-PLR-114 | TC-PLR-114 |
| NE-PLR-115 | TC-PLR-115 |
| NE-PLR-116 | TC-PLR-116 |
| NE-PLR-117 | TC-PLR-117 |
| NE-PLR-118 | TC-PLR-118 |
| NE-PLR-119 | TC-PLR-119 |
| NE-PLR-120 | TC-PLR-120 |
| NE-PLR-121 | TC-PLR-121 |
| NE-PLR-122 | TC-PLR-122 |
| NE-PLR-123 | TC-PLR-123 |
| NE-PLR-124 | TC-PLR-124 |
| NE-PLR-125 | TC-PLR-125 |
| NE-PLR-126 | TC-PLR-126 |
| NE-PLR-127 | TC-PLR-127 |
| NE-PLR-128 | TC-PLR-128 |
| NE-PLR-129 | TC-PLR-129 |
| NE-PLR-130 | TC-PLR-130 |
| NE-PLR-131 | TC-PLR-131 |
| NE-PLR-132 | TC-PLR-132 |
| NE-PLR-133 | TC-PLR-133 |
| NE-PLR-134 | TC-PLR-134 |
| NE-PLR-135 | TC-PLR-135 |
| NE-PLR-136 | TC-PLR-136 |
| NE-PLR-137 | TC-PLR-137 |
| NE-PLR-138 | TC-PLR-138 |
| NE-PLR-139 | TC-PLR-139 |
| NE-PLR-140 | TC-PLR-140 |
| NE-PLR-141 | TC-PLR-141 |
| NE-PLR-142 | TC-PLR-142 |
| NE-PLR-143 | TC-PLR-143 |
| NE-PLR-144 | TC-PLR-144 |
| NE-PLR-145 | TC-PLR-145 |
| NE-PLR-146 | TC-PLR-146 |
| NE-PLR-147 | TC-PLR-147 |
| NE-PLR-148 | TC-PLR-148 |
| NE-PLR-149 | TC-PLR-149 |
| NE-PLR-150 | TC-PLR-150 |
| NE-PLR-151 | TC-PLR-151 |
| NE-PLR-152 | TC-PLR-152 |
| NE-PLR-153 | TC-PLR-153 |
| NE-PLR-154 | TC-PLR-154 |
| NE-PLR-155 | TC-PLR-155 |
| NE-PLR-156 | TC-PLR-156 |
| NE-PLR-157 | TC-PLR-157 |
| NE-PLR-158 | TC-PLR-158 |
| NE-PLR-159 | TC-PLR-159 |
| NE-PLR-160 | TC-PLR-160 |
| NE-PLR-161 | TC-PLR-161 |
| NE-PLR-162 | TC-PLR-162 |
| NE-PLR-163 | TC-PLR-163 |
| NE-PLR-164 | TC-PLR-164 |
| NE-PLR-165 | TC-PLR-165 |
| NE-PLR-166 | TC-PLR-166 |
| NE-PLR-167 | TC-PLR-167 |
| NE-PLR-168 | TC-PLR-168 |
| NE-PLR-169 | TC-PLR-169 |
| NE-PLR-170 | TC-PLR-170 |
| NE-PLR-171 | TC-PLR-171 |
| NE-PLR-172 | TC-PLR-172 |
| NE-PLR-173 | TC-PLR-173 |
| NE-PLR-174 | TC-PLR-174 |
| NE-PLR-175 | TC-PLR-175 |
| NE-PLR-176 | TC-PLR-176 |
| NE-PLR-177 | TC-PLR-177 |
| NE-PLR-178 | TC-PLR-178 |
| NE-PLR-179 | TC-PLR-179 |
| NE-PLR-180 | TC-PLR-180 |
| NE-PLR-181 | TC-PLR-181 |
| NE-PLR-182 | TC-PLR-182 |
| NE-PLR-183 | TC-PLR-183 |
| NE-PLR-184 | TC-PLR-184 |
| NE-PLR-185 | TC-PLR-185 |
| NE-PLR-186 | TC-PLR-186 |
| NE-PLR-187 | TC-PLR-187 |
| NE-PLR-188 | TC-PLR-188 |
| NE-PLR-189 | TC-PLR-189 |
| NE-PLR-190 | TC-PLR-190 |
| NE-PLR-191 | TC-PLR-191 |
| NE-PLR-192 | TC-PLR-192 |
| NE-PLR-193 | TC-PLR-193 |
| NE-PLR-194 | TC-PLR-194 |
| NE-PLR-195 | TC-PLR-195 |
| NE-PLR-196 | TC-PLR-196 |
| NE-PLR-197 | TC-PLR-197 |
| NE-PLR-198 | TC-PLR-198 |
| NE-PLR-199 | TC-PLR-199 |
| NE-PLR-200 | TC-PLR-200 |
| NE-PLR-201 | TC-PLR-201 |
| NE-PLR-202 | TC-PLR-202 |
| NE-PLR-203 | TC-PLR-203 |
| NE-PLR-204 | TC-PLR-204 |
| NE-PLR-205 | TC-PLR-205 |
| NE-PLR-206 | TC-PLR-206 |
| NE-PLR-207 | TC-PLR-207 |
| NE-PLR-208 | TC-PLR-208 |
| NE-PLR-209 | TC-PLR-209 |
| NE-PLR-210 | TC-PLR-210 |
| NE-PLR-211 | TC-PLR-211 |
| NE-PLR-212 | TC-PLR-212 |
| NE-PLR-213 | TC-PLR-213 |
| NE-PLR-214 | TC-PLR-214 |
| NE-PLR-215 | TC-PLR-215 |

## §21 Ek J — Gereksinim Künyesi Sözlüğü (Player · otomatik extraction)

> **Otomatik üretildi** §10 bloklarından (başlık + kabul kriteri + ADR izi + test atıfı) — elle yazılmadı.
> Bağlayıcı metin §10'dur; bu ek özet referanstır. Kardeş PRD: coremusic-neva-engine-prd.md (§21.D envanter + §12.3 hata kodu eşlemesi).

**NE-PLR-001 — Per-OS ortak çekirdek + ince adapter**
- Kabul: Windows/Linux build'leri aynı uygulama katmanı kodunu paylaşır; OS'a özgü yalnız backend + ince satırlar.
- ADR: ADR-019 §2.2 (Ortak Çekirdek + İnce Adapter, 15/5/5/5/5 consensus) · Test: TC-PLR-001 · Blok: plr-p05.md

**NE-PLR-002 — C ABI üzerinden engine kullanımı**
- Kabul: Player kaynak kodunda `neva::` C++ iç tiplerine doğrudan erişim yok (yalnız `neva_session_*` çağrıları) `[ÖNERİ — istisna: EqChain per-kanal erişimi]`.
- ADR: disk `neva_session.h` · kardeş PRD NE-ENG-001 · Test: TC-PLR-002 · Blok: plr-p05.md

**NE-PLR-003 — api_version handshake kontrolü**
- Kabul: `0x00010000` beklentisi; uyuşmazlık → ERROR durumu + sürüm mesajı (crash yok).
- ADR: disk `neva_session.h:26` · Test: TC-PLR-003 · Blok: plr-p05.md

**NE-PLR-004 — Durum makinesi (ADR-019 §1.4)**
- Kabul: §04.5 geçiş tablosu birebir; geçersiz geçiş (PAUSED→LOADING doğrudan) reddedilir.
- ADR: ADR-019 §1.4 · ADR-004 · ADR-018 · Test: TC-PLR-004 · Blok: plr-p05.md

**NE-PLR-005 — Event callback (metadata/state/error)**
- Kabul: Event'ler non-RT kuyruktan gelir; UI thread'e marshal edilir; ABI minor sürümle eklenir (mevcut fonksiyonlar değişmez).
- ADR: ADR-106 kural 2 (minor ek — ABI kırmaz) · kardeş PRD §05.2 E1 · Test: TC-PLR-005 · Blok: plr-p05.md

**NE-PLR-006 — Konum/düre erişimi (E3/E4)**
- Kabul: Seek doğrulanır (dosya sınırında clamp); radyoda seek devre dışı (akış).
- ADR: kardeş PRD §05.2 E3/E4 · Test: TC-PLR-006 · Blok: plr-p05.md

**NE-PLR-007 — Cihaz listesi & seçimi (E8)**
- Kabul: Liste engine `enumerate()`'inden gelir; seçim canlı yapılır, tercih kalıcı saklanır.
- ADR: ADR-019 F1 · disk `neva_session_set_output_device` · Test: TC-PLR-007 · Blok: plr-p05.md

**NE-PLR-008 — UI katmanı ayrımı**
- Kabul: UI thread'inde engine RT veri yapısına doğrudan erişim yok.
- ADR: ADR-017 (RT kısıtları) · kardeş PRD §04.3 · Test: TC-PLR-008 · Blok: plr-p05.md

**NE-PLR-009 — UI toolkit kararı açık**
- Kabul: Karar ADR olarak tescil edilmeden UI fazı (M4) başlatılmaz; seçenek listesi §18.1'de.
- ADR: YOK — açık madde §18.1 · Test: — (karar kapısı) · Blok: plr-p05.md

**NE-PLR-010 — Config kalıcılığı**
- Kabul: Yeniden açılışta son durum geri gelir; bozuk config → default + uyarı (crash yok).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-010 · Blok: plr-p05.md

**NE-PLR-011 — Engine bağımlılık sürümü**
- Kabul: CI'da player + engine birlikte derlenir; handshake testi yeşil.
- ADR: kardeş PRD §05.4 · Test: TC-PLR-011 · Blok: plr-p05.md

**NE-PLR-012 — neva-eq / neva-matrix ayrı hedefler**
- Kabul: Ana player ile ayrı süreç; C ABI üzerinden konuşur; kapatma ana süreci etkilemez.
- ADR: ADR-107 K2 · S3 §4.1 (RE emsal) · Test: TC-PLR-012 · Blok: plr-p05.md

**NE-PLR-013 — Çevrimdışı öncelik**
- Kabul: Ağ yokken uygulama açılır, dosya çalar; ağ çağrıları sadece radyo/dizin akışlarında.
- ADR: §02.1 I-7 · AGENTS §17 Edge 9 (Offline-First) · Test: TC-PLR-013 · Blok: plr-p05.md

**NE-PLR-014 — RTL/konum testi yok (kapsam notu)**
- Kabul: Karar §18.2'de açık; UI fazında ele alınır.
- ADR: YOK · Test: — · Blok: plr-p05.md

**NE-PLR-015 — Kardeş PRD sınırı**
- Kabul: Bu doküman engine implementasyonuna emir vermez; yalnız API/ölçüm sözleşmesi çaprazreferanslıdır.
- ADR: §02.3 · Test: — · Blok: plr-p05.md

**NE-PLR-016 — Decode matrisi (dr_libs + libdsd + FFmpeg)**
- Kabul: §05.1 matrisindeki her format açılır; bilmeyen format `UNSUPPORTED_FORMAT` (kod 3).
- ADR: ADR-0105 indeks kaydı ⚠️ (v1.0.2 §§3-4) · Test: TC-PLR-016 · Blok: plr-p05.md

**NE-PLR-017 — Yerel dosya demux önceliği (FFmpeg dışı)**
- Kabul: FLAC açılışında FFmpeg process/module yüklenmez `[ÖNERİ — ölçüm log ile]`.
- ADR: ADR-106 §1.3 (çözüm önerisi M3 — ses akışında FFmpeg'i EN AZ kullan) · Test: TC-PLR-017 · Blok: plr-p05.md

**NE-PLR-018 — soxr producer-side resample**
- Kabul: RT yolunda soxr çağrısı yok (statik + profil); master clock = cihaz.
- ADR: ADR-106 kural 5 (ADR-0105 §2.5) ⚠️ · Test: TC-PLR-018 · Blok: plr-p05.md

**NE-PLR-019 — soxr lisansı (LGPL-2.1)**
- Kabul: §14 lisans matrisinde durum: LGPL-2.1 — statik link seçeneği `⚠️ LİSANS` etiketli.
- ADR: ADR-0105 v1.2.0 düzeltmesi (indeks) ⚠️ · Test: TC-PLR-019 (lisans denetimi) · Blok: plr-p05.md

**NE-PLR-020 — FFmpeg LGPL build**
- Kabul: Build bayrakları envanterde; LGPL dış codec (x264 vb.) yok (video encode kapsam dışı).
- ADR: ADR-0105 indeks (v1.3.x lisans satırları) ⚠️ · lib-matrix · Test: TC-PLR-020 · Blok: plr-p05.md

**NE-PLR-021 — Protocol whitelist (FFmpeg)**
- Kabul: `file:///C:/...` URL'si validator'da RED; FFmpeg'e hiç ulaşmaz.
- ADR: ADR-106 kural 6 (protokol beyaz listesi) · Test: TC-PLR-021 · Blok: plr-p05.md

**NE-PLR-022 — HTTP09Allowed=0**
- Kabul: Tüm tuner isteklerinde `CURLOPT_HTTP09ALLOWED = 0`.
- ADR: ADR-106 kural 6 · radio-and-hires §5.6 · Test: TC-PLR-022 · Blok: plr-p05.md

**NE-PLR-023 — Redirect zinciri ≤5**
- Kabul: Zincirleme redirect (6. hop) reddi; her hop'ta IP kontrolü tekrarlanır.
- ADR: ADR-106 kural 6 · Test: TC-PLR-023 · Blok: plr-p05.md

**NE-PLR-024 — Timeout 10 sn (tuner)**
- Kabul: Askıda istek 10 sn'de kopar; backoff'a geçer (NE-PLR-115).
- ADR: ADR-106 kural 6 · radio-and-hires §5.7 · Test: TC-PLR-024 · Blok: plr-p05.md

**NE-PLR-025 — TLS (mbedTLS)**
- Kabul: Self-signed/bozuk sertifika RED (kullanıcı açık onayı ayrı istisna `[ÖNERİ]`).
- ADR: ADR-106 §5.4 (mbedTLS Apache-2.0 OR GPL-2.0, 9.13.0) · Test: TC-PLR-025 · Blok: plr-p05.md

**NE-PLR-026 — decode → float32 ring**
- Kabul: Çıkış formatı tutarlı (interleaved float32); ring invariant'ı korunur (writer okuyucuyu geçmez).
- ADR: ADR-0105 §2.5 ⚠️ · kardeş PRD §07.4 · Test: TC-PLR-026 · Blok: plr-p05.md

**NE-PLR-027 — Bit derinliği dönüşüm politikası**
- Kabul: 24-bit kaynak dosya → 24-in-32 çıktı hash'i kaynak byte'larla uyumlu (kayıpsız yolla) `⚠️ VR (detay ADR-0105 §2.x indeksinde yok)`.
- ADR: ADR-106 §1.3 · ADR-0105 indeks ⚠️ · Test: TC-PLR-027 · Blok: plr-p05.md

**NE-PLR-028 — Queue prefetch (gapless ön koşulu)**
- Kabul: E2 API (`open_queue/preload_next`) ile sıradaki dosya zamanında hazır.
- ADR: ADR-0105 AK-5 ⚠️ · kardeş PRD §05.2 E2 · Test: TC-PLR-028 · Blok: plr-p05.md

**NE-PLR-029 — Decode hata yönetimi (bozuk dosya)**
- Kabul: 1000+ bozuk dosya fuzz corpus (kapı 5.13) 0 crash; tek dosya hatası queue'yu durdurmaz.
- ADR: ADR-0105 kapı 5.13 ⚠️ · Test: TC-PLR-029 · Blok: plr-p05.md

**NE-PLR-030 — Aynı anda tek decode oturumu (dosya)**
- Kabul: Mode değişimi (radyo↔dosya) mevcut decode'u temiz kapatır.
- ADR: YOK — `[ÖNERİ]` (taşıma modeli §04.3) · Test: TC-PLR-030 · Blok: plr-p05.md

**NE-PLR-031 — Metadata okuma (ID3/Vorbis/FLAC tags)**
- Kabul: Eksik tag → dosya adı fallback; UTF-8 + ID3 v2.3/v2.4 encoding doğru.
- ADR: YOK — `[ÖNERİ]` (parça okuma kapısı ADR-0105'te `⚠️ VR`) · Test: TC-PLR-031 · Blok: plr-p05.md

**NE-PLR-032 — Kapak görseli (embedded art)**
- Kabul: Büyük görsel → ölçekle + reddet; bozuk görsel crash değil.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-032 · Blok: plr-p05.md

**NE-PLR-033 — ReplayGain / loudness okuma**
- Kabul: Kapalıysa hiç gain yok (bit-perfect zinciri bozulmaz); açıkken bile EQ flat+limiter bypass şartı rozet için gerekli.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-033 · Blok: plr-p05.md

**NE-PLR-034 — Çoklu kaynak URL açılışı**
- Kabul: URI ayrıştırma validator'dan geçer (NE-PLR-152); dosya yolu lokal ise validator bypass → sadece yolu normalize eder `[ÖNERİ]`.
- ADR: kardeş PRD §05.2 (tuner E9) ile hizalı · Test: TC-PLR-034 · Blok: plr-p05.md

**NE-PLR-035 — Decode performerans bütçesi**
- Kabul: Bench raporu (§06.4); eşik ADR-0105 dosyası bulununca bağlanır.
- ADR: ADR-0105 indeks §5.6 ⚠️ · Test: TC-PLR-035 · Blok: plr-p05.md

**NE-PLR-036 — WASAPI Exclusive bit-perfect yolu**
- Kabul: Kapı 5.1: WAV hash byte-eşitlik (giriş==çıkış) — kardeş PRD K3 ile birlikte.
- ADR: ADR-0105 kapı 5.1 ⚠️ (indeks) · ADR-106 §1.3 · Test: TC-PLR-036 · Blok: plr-p06.md

**NE-PLR-037 — Bit-perfect kapsamı (yalnız kayıpsız)**
- Kabul: UI/pazarlama dili ve testler bu ayrımı zorunlu kılar; lossy akışta rozet YOK.
- ADR: ADR-0105 v1.0.2 §6 ⚠️ · Test: TC-PLR-037 · Blok: plr-p06.md

**NE-PLR-038 — Format negosyasyon sırası (32f→32i→24-in-32)**
- Kabul: Her basamak `IsFormatSupported` ile; tamamı başarısız → shared/fallback.
- ADR: ADR-106 §1.3 · Test: TC-PLR-038 · Blok: plr-p06.md

**NE-PLR-039 — `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` kurtarma**
- Kabul: Hata yutulmaz; yeniden hesap başarısızsa zincir düşer.
- ADR: ADR-106 §1.3 · Test: TC-PLR-039 · Blok: plr-p06.md

**NE-PLR-040 — EQ flat + limiter bypass bit-perfect ön koşulu**
- Kabul: CI'da EQ aktif + hash = FAIL beklenir (inverted guard kardeş PRD §11.2 Ş4).
- ADR: ADR-106 kural 4/§5.1 Ş4 · Test: TC-PLR-040 · Blok: plr-p06.md

**NE-PLR-041 — Hi-res 192 kHz / 32-bit float**
- Kabul: 192k/32f kaynak hash doğrulaması; negosyasyon N1 sırası.
- ADR: ADR-107 K3 · ADR-0105 indeks ⚠️ · Test: TC-PLR-041 · Blok: plr-p06.md

**NE-PLR-042 — ASIO yolu (lisans U1'e bağlı)**
- Kabul: ASIO driver varlığında exclusive benzeri doğrudan yol; fallback zinciri §09.2.
- ADR: ADR-019 §1.4 · kardeş PRD NE-ENG-023 · Test: TC-PLR-042 · Blok: plr-p06.md

**NE-PLR-043 — Bit-perfect rozeti `[ÖNERİ]`**
- Kabul: Üç koşul false ise rozet gizlenir (yanıltıcı görsel yok).
- ADR: kardeş PRD §05.2 B3 `[ÖNERİ]` · Test: TC-PLR-043 · Blok: plr-p06.md

**NE-PLR-044 — DSD/DoP çıkışı**
- Kabul: DoP akışında marker bütünlüğü doğrulanır (test dosyası); native DSD kararı ayrı ADR.
- ADR: ADR-0105 AK-5 ⚠️ · Test: TC-PLR-044 · Blok: plr-p06.md

**NE-PLR-045 — Shared mod rozet/uyarı ayrımı**
- Kabul: Fallback sonrası UI bilgilendirmesi; hash testi shared modda koşul dışı.
- ADR: kardeş PRD NE-ENG-022 · Test: TC-PLR-045 · Blok: plr-p06.md

**NE-PLR-046 — Exclusive↔shared otomatik geçiş**
- Kabul: Geçişte ses kesintisi minimize; event E8 ile UI'a akar.
- ADR: ADR-106 §1.3 · ADR-019 F2 · Test: TC-PLR-046 · Blok: plr-p06.md

**NE-PLR-047 — Cihaz hızı uyuşmazlığı (F3 sırası)**
- Kabul: RT'de resample yok; hash yalnız eşit formatta.
- ADR: ADR-019 §2 · kardeş PRD NE-ENG-015 · Test: TC-PLR-047 · Blok: plr-p06.md

**NE-PLR-048 — Hot-plug kaybı davranışı**
- Kabul: Test: cihaz kaldırma simülasyonu; yeniden bağlanma; kullanıcı mesajı.
- ADR: ADR-019 F1 · Test: TC-PLR-048 · Blok: plr-p06.md

**NE-PLR-049 — Bit-perfect CI hattı (dosya→Null→hash)**
- Kabul: CI yeşil + release checklist'te loopback maddesi (§11.2).
- ADR: kardeş PRD §11.2 · ADR-107 K3 · Test: TC-PLR-049 · Blok: plr-p06.md

**NE-PLR-050 — 12 kombinasyon matrisi**
- Kabul: 12/12 yeşil; eksik kombinasyon = release bloke.
- ADR: kardeş PRD K3/§11.2 · Test: TC-PLR-050 · Blok: plr-p06.md

**NE-PLR-051 — OS ses efektlerinin devre dışı olması (exclusive)**
- Kabul: Windows'ta session volume 1.0 sabit; "ses iyileştirme" bayrakları kapalı.
- ADR: kardeş PRD NE-ENG-037 · VISION V-5 · Test: TC-PLR-051 · Blok: plr-p06.md

**NE-PLR-052 — Null-test CI guard (inverted)**
- Kabul: CI script: radyo akışında hash testi koşul dışı; sahte-PASS/FAIL denetimi.
- ADR: ADR-106 §5.1 Ş4 (QA debate düzeltmesi) · Test: TC-PLR-052 · Blok: plr-p06.md

**NE-PLR-053 — Exclusive kilit tercihi (kullanıcı)**
- Kabul: Tercih açıkken cihaz kaybında otomatik shared'a geçmez; uyarı gösterir.
- ADR: ADR-019 §1.1 (`⚠️ VR — eşik/varsayılan`) · Test: TC-PLR-053 · Blok: plr-p06.md

**NE-PLR-054 — Uzun süreli çalışma kararlılığı (bit-perfect)**
- Kabul: Süreç testi; bellek sızıntısı 0 (leak check).
- ADR: kardeş PRD §11 stres · Test: TC-PLR-054 · Blok: plr-p06.md

**NE-PLR-055 — Dither (16-bit çıkış)**
- Kabul: Dither yalnız dönüşüm varsa; 16→16 hash yolu bozulmaz.
- ADR: YOK — `[ÖNERİ]` (N1 negosyasyonla uyumlu) · Test: TC-PLR-055 · Blok: plr-p06.md

**NE-PLR-056 — MP3 (LAME/Xing) gapless**
- Kabul: Dosya→dosya sınırında 0 örnek boşluk; faz kayması yok (kapı 5.12 vektörü).
- ADR: ADR-0105 AK-5 ⚠️ · Test: TC-PLR-056 · Blok: plr-p06.md

**NE-PLR-057 — FLAC gapless**
- Kabul: FLAC→FLAC sınırı boşluksuz; hash doğrulamalı referans vektör.
- ADR: ADR-0105 AK-5 ⚠️ · Test: TC-PLR-057 · Blok: plr-p06.md

**NE-PLR-058 — Opus gapless**
- Kabul: Opus→Opus sınır testi.
- ADR: ADR-0105 AK-5 ⚠️ · Test: TC-PLR-058 · Blok: plr-p06.md

**NE-PLR-059 — DSD gapless (DoP/DSF marker continuity)**
- Kabul: DSF→DSF sınırında marker deseni bozulmaz.
- ADR: ADR-0105 AK-5 ⚠️ · Test: TC-PLR-059 · Blok: plr-p06.md

**NE-PLR-060 — Dosya→dosya geçişte kesintisizlik ölçümü**
- Kabul: Kapı 5.12: korelasyon ölçümü raporu; ölçüm düzeneği §11.3.
- ADR: ADR-0105 kapı 5.12 ⚠️ · Test: TC-PLR-060 · Blok: plr-p06.md

**NE-PLR-061 — Queue prefetch boyutu**
- Kabul: Geçiş anında ring'de yeterli veri; starve fade tetiklenmez.
- ADR: kardeş PRD §07.4 `[ÖNERİ]` · Test: TC-PLR-061 · Blok: plr-p06.md

**NE-PLR-062 — Karışık format queue (FLAC→MP3→FLAC)**
- Kabul: Geçişte cihaz formatı korunur; gerekirse soxr; kesinti ≤1 geçiş fade `[ÖNERİ]`.
- ADR: kardeş PRD §06.5 · ADR-019 F3 · Test: TC-PLR-062 · Blok: plr-p06.md

**NE-PLR-063 — Shuffle + gapless**
- Kabul: Shuffle'da gapless testi geçer.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-063 · Blok: plr-p06.md

**NE-PLR-064 — Repeat (tek/liste) gapless**
- Kabul: 1 saat repeat testi; RSS artış ≤0 `[ÖNERİ]`.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-064 · Blok: plr-p06.md

**NE-PLR-065 — Son dosya→STOPPED**
- Kabul: Son frame sonrası 50 ms ramp + STOPPED event.
- ADR: kardeş PRD §07.4 `[ÖNERİ]` · Test: TC-PLR-065 · Blok: plr-p06.md

**NE-PLR-066 — Gapless bozuk tag fallback**
- Kabul: Eksik bilgide uydurma kırpma YOK (yanlış kırpma bozulma üretir).
- ADR: ADR-0105 AK-5 ruhu ⚠️ · Test: TC-PLR-066 · Blok: plr-p06.md

**NE-PLR-067 — Crossfade ayrımı**
- Kabul: UI'da ikisi aynı anda iddia edilmez; crossfade ≥2 decode oturumu ister (bütçe).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-067 · Blok: plr-p06.md

**NE-PLR-068 — Gapless CI vektör kütüphanesi**
- Kabul: Vektörler + beklenen sonuçlar (byte maskesi) CI'da çalışır.
- ADR: ADR-0105 kapı 5.12 ⚠️ · Test: TC-PLR-068 · Blok: plr-p06.md

**NE-PLR-069 — Konum sayaçları gapless uyumu**
- Kabul: UI'da sıradaki/şu anki net ayrımı.
- ADR: kardeş PRD §05.2 E3 `[ÖNERİ]` · Test: TC-PLR-069 · Blok: plr-p06.md

**NE-PLR-070 — DSD→PCM queue geçişi**
- Kabul: Geçişte pop transient'sız (NE-ENG-078 reset).
- ADR: kardeş PRD NE-ENG-078 · Test: TC-PLR-070 · Blok: plr-p06.md

**NE-PLR-071 — 5.1 kaynaklı gapless**
- Kabul: 5.1→5.1; 5.1→2.0 downmix geçişi `[ÖNERİ — downmix matrisi kararı §18.3]`.
- ADR: VISION V-8 · Test: TC-PLR-071 · Blok: plr-p06.md

**NE-PLR-072 — Seek sonrası gapless bozulmaz**
- Kabul: Seek→geçiş testi; padding bilgileri korunur.
- ADR: kardeş PRD §05.2 E4 · Test: TC-PLR-072 · Blok: plr-p06.md

**NE-PLR-073 — Pause/Resume arası gapless**
- Kabul: Bekleme bellek maliyeti yok (decode duraklar); resume gapless korunur.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-073 · Blok: plr-p06.md

**NE-PLR-074 — Dosya→dosya: 0 frame boşluk tanımı**
- Kabul: Ölçüm raporu kapı 5.12'de.
- ADR: ADR-0105 kapı 5.12 ⚠️ · Test: TC-PLR-074 · Blok: plr-p06.md

**NE-PLR-075 — Radio gapless (akış→akış) — reconnect dahil**
- Kabul: Stream→stream vektör; DAC loopback ±5 ms (kapı 5.12 Ş2).
- ADR: ADR-0105 AK-5 ⚠️ · ADR-106 §2.2a · Test: TC-PLR-075 · Blok: plr-p06.md

**NE-PLR-076 — libplacebo render yolu**
- Kabul: HDR metadata doğru aktarım; render video-only (ses engine'de).
- ADR: ADR-0105 indeks (v1.2.x) ⚠️ · Test: TC-PLR-076 · Blok: plr-p07.md

**NE-PLR-077 — Master clock = cihaz (ses)**
- Kabul: 2 saat video testte drift kaymayla düzeltilir; video ileri/geri hız ayarı (stretch) `[ÖNERİ]`.
- ADR: ADR-106 kural 11 · ADR-0105 kapı 5.11 ⚠️ · Test: TC-PLR-077 · Blok: plr-p07.md

**NE-PLR-078 — A/V sync P95 ≤20 ms (kapı 5.11)**
- Kabul: Loopback/ışık ölçüm düzeneği (§11.3); P95 raporu.
- ADR: ADR-0105 kapı 5.11 ⚠️ · Test: TC-PLR-078 · Blok: plr-p07.md

**NE-PLR-079 — VRAM bütçesi (~400-700 MB)**
- Kabul: Peak ölçümü (§06.3; §7.5 açık madde kapanır).
- ADR: ADR-0105 §2.8 v1.2.1-1.3.2 ⚠️ · Test: TC-PLR-079 · Blok: plr-p07.md

**NE-PLR-080 — VRAM tükenme merdiveni (V0-V5)**
- Kabul: Her kademede davranış tanımlı (downscale → HDR kapatma → video gizle `⚠️ VR — kademe içerikleri indeks metninde yok)`.
- ADR: ADR-0105 §7.4 ⚠️ · Test: TC-PLR-080 · Blok: plr-p07.md

**NE-PLR-081 — VRAM ölçüm altyapısı (GTX 1660 bench)**
- Kabul: Aynı ortamda repeatable ölçüm; hedef-sınıf uyarı satırı raporda (1060 3 GB: 600 MB = %19,5).
- ADR: ADR-0105 §7.1-§7.3 v1.3.1-1.3.2 ⚠️ · Test: TC-PLR-081 · Blok: plr-p07.md

**NE-PLR-082 — Sistem RAM ≤400 MB (video dahil)**
- Kabul: Profil raporu; ihlalde uyarı `[ÖNERİ — davranış NE-PLR-065 benzeri tanımlanacak]`.
- ADR: ADR-0105 §2.9 ⚠️ · Test: TC-PLR-082 · Blok: plr-p07.md

**NE-PLR-083 — Gecikme <20 ms bütçesi**
- Kabul: Kapı 5.10 gecikme ölçümü; blok seçimi bu sınırlar içinde.
- ADR: ADR-0105 §2.10 ⚠️ · Test: TC-PLR-083 · Blok: plr-p07.md

**NE-PLR-084 — Kapı 5.10 (gecikme ölçüm kapısı)**
- Kabul: Ölçüm düzeneği + rapor arşivi; eşiği aşan build release adayı değil.
- ADR: ADR-0105 kapı 5.10 ⚠️ · Test: TC-PLR-084 · Blok: plr-p07.md

**NE-PLR-085 — Kapı 5.13 (hostile input fuzzing)**
- Kabul: Fuzz raporu release dosyasında; ADR-106 Ş7 (her decoder + ICY + HLS playlist) tam.
- ADR: ADR-0105 kapı 5.13 ⚠️ · ADR-106 §5.2 Ş7 · Test: TC-PLR-085 · Blok: plr-p07.md

**NE-PLR-086 — Kapı 5.12 (gapless format vektörü)**
- Kabul: §10 Grup D testleri yeşil + loopback raporu.
- ADR: ADR-0105 kapı 5.12 ⚠️ · Test: TC-PLR-086 · Blok: plr-p07.md

**NE-PLR-087 — Kapı 5.1 (bit-perfect null-test + inverted guard)**
- Kabul: CI guard + §11.2 Ş4; sahte-PASS/FAIL denetimi.
- ADR: ADR-0105 kapı 5.1 ⚠️ · ADR-106 §5.1 · Test: TC-PLR-087 · Blok: plr-p07.md

**NE-PLR-088 — Video format kapsamı**
- Kabul: Test: 1 MP4 + 1 MKV oynar; altyazı `⚠️ VR (karar yok — MAY)` .
- ADR: ADR-105 indeks (video yolu) ⚠️ · Test: TC-PLR-088 · Blok: plr-p07.md

**NE-PLR-089 — Video modu geçişi (ses↔video)**
- Kabul: Geçişte cihaz/format negosyasyonu tekrarlanmaz (aynı session).
- ADR: kardeş PRD NE-ENG-035 · Test: TC-PLR-089 · Blok: plr-p07.md

**NE-PLR-090 — Pencere/video senkronu (drift ölçümü)**
- Kabul: Raporda max sapma; kapı 5.11 P95.
- ADR: ADR-0105 kapı 5.11 ⚠️ · Test: TC-PLR-090 · Blok: plr-p07.md

**NE-PLR-091 — Ses kalitesi: SNR >105 dB**
- Kabul: §11.3 ölçüm düzeneği (DAC loopback); rapor release dosyasında.
- ADR: VISION.md (satır ~100/327) · §00.3-D5 `⚠️ VR (ADR-0105'te teyit yok)` · Test: TC-PLR-091 · Blok: plr-p07.md

**NE-PLR-092 — THD+N <%0.005 @1 kHz**
- Kabul: §11.3 ölçüm; eşiğin altında.
- ADR: VISION.md · `⚠️ VR` (aynı) · Test: TC-PLR-092 · Blok: plr-p07.md

**NE-PLR-093 — 32-bit float DSP zinciri (uçtan uca)**
- Kabul: Zincirde tek bir integer kesme yok (statik denetim + hash testi).
- ADR: VISION V-6 · kardeş PRD §06 · Test: TC-PLR-093 · Blok: plr-p07.md

**NE-PLR-094 — mpv/libmpv reddi korunur**
- Kabul: Kod tabanında mpv bağımlılığı yok; §14 RED satırı.
- ADR: ADR-0105 v1.1.1 indeks · Test: TC-PLR-094 · Blok: plr-p07.md

**NE-PLR-095 — Video performans bütçesi**
- Kabul: Bench raporu; eşik kararı ADR-0105 dosyası ile.
- ADR: ADR-0105 indeks ⚠️ · Test: TC-PLR-095 · Blok: plr-p07.md

**NE-PLR-096 — ICY/Shoutcast v1+v2 özel tuner**
- Kabul: v1 (icy-metaint metin blokları) + v2 (XML metaveri) ayrıştırılır; StreamTitle event akar.
- ADR: ADR-106 §2.2a kural 1 · S11 · Test: TC-PLR-096 · Blok: plr-p07.md

**NE-PLR-097 — HLS (m3u8) FFmpeg gateway**
- Kabul: M3U8 akışı oynar; `file://` RED (NE-PLR-021).
- ADR: ADR-106 kural 1 · lib-matrix (FFmpeg 9.0.2 HLS demux) · Test: TC-PLR-097 · Blok: plr-p07.md

**NE-PLR-098 — DASH Aşama 2**
- Kabul: Aşama 2 karar ADR ile; UI'da gizli seçenek.
- ADR: ADR-106 §2.2a kural 1 · Test: TC-PLR-098 · Blok: plr-p07.md

**NE-PLR-099 — Codec matrisi (AAC-LC/HE-AAC/Opus/MP3/FLAC)**
- Kabul: Her codec için canlı/lokal akış testi; FDK bayrağı build'de yok.
- ADR: ADR-106 kural 9 §1.3 · Test: TC-PLR-099 · Blok: plr-p07.md

**NE-PLR-100 — FDK-AAC yasağı**
- Kabul: §14 matris + build envanteri; teyit gelirse ayrı ADR.
- ADR: ADR-106 kural 9 (architect S4) · Test: TC-PLR-100 · Blok: plr-p07.md

**NE-PLR-101 — StreamTitle sanitizasyonu (kural 7)**
- Kabul: XSS payload'lı StreamTitle düz metin gösterilir; cap aşımı kırpılır.
- ADR: ADR-106 kural 7 (security S1) · Test: TC-PLR-101 · Blok: plr-p07.md

**NE-PLR-102 — StreamUrl hyperlink yasağı**
- Kabul: UI'da `onclick`/anchor yok; kopyala seçeneği `[ÖNERİ]`.
- ADR: ADR-106 kural 7 · Test: TC-PLR-102 · Blok: plr-p07.md

**NE-PLR-103 — Parser limitleri (kural 8)**
- Kabul: Sınır aşımında RED/skip; fuzz corpus bu limitlerle doğrulanır.
- ADR: ADR-106 kural 8 · §07.5 Ş8 · Test: TC-PLR-103 · Blok: plr-p07.md

**NE-PLR-104 — URL Validator tek kapı (kural 6)**
- Kabul: §12.2 test vektörleri; validator bypass yok (statik denetim: tuner FFmpeg'e ham URL vermez).
- ADR: ADR-106 kural 6 · S1 §2.2a (27 şart) · Test: TC-PLR-104 · Blok: plr-p07.md

**NE-PLR-105 — Reconnect backoff (1s→30s cap, ±%20 jitter)**
- Kabul: Ölçüm: ilk 6 deneme süreleri tablosu; cap'te sabitlenir.
- ADR: ADR-106 §1.3 (v1.1 düzeltmesi) · Test: TC-PLR-105 · Blok: plr-p07.md

**NE-PLR-106 — 3 başarısız → ERROR**
- Kabul: 3. başarısızdan sonra yeni deneme YOK (elle "yeniden dene" hariç).
- ADR: ADR-106 kural 8 (3 başarısız → ERROR) · Test: TC-PLR-106 · Blok: plr-p07.md

**NE-PLR-107 — Prebuffer 2-4 s (reconnect kutusu)**
- Kabul: İlk ses öncesi ≤4 s bekleme; kesintisiz akış (starve fade yok).
- ADR: ADR-106 §2.2a · Test: TC-PLR-107 · Blok: plr-p07.md

**NE-PLR-108 — Fade-in 20 ms (reconnect)**
- Kabul: Waveform analizi; rampa süresi.
- ADR: ADR-106 §2.2a · Test: TC-PLR-108 · Blok: plr-p07.md

**NE-PLR-109 — Ring STARVE/OVERFLOW davranışları**
- Kabul: Ağ yavaşlatma (tc/netem) senaryosu; §04.2 şeması.
- ADR: ADR-106 §2.2a · kardeş PRD §07.4 · Test: TC-PLR-109 · Blok: plr-p07.md

**NE-PLR-110 — TunerThread non-RT ayrımı**
- Kabul: Statik + thread adlandırma; RT'de ağ çağrısı yok.
- ADR: ADR-106 §2.2a · ADR-017 · Test: TC-PLR-110 · Blok: plr-p07.md

**NE-PLR-111 — R128 sidechain non-RT**
- Kabul: RT etkilenmez (profil); canlı LUFS akar.
- ADR: ADR-106 §2.2a · Test: TC-PLR-111 · Blok: plr-p07.md

**NE-PLR-112 — Metadata event zinciri (sanitize → lock-free kuyruk → C ABI)**
- Kabul: 1000 mesaj/sn spam istasyonunda UI donmaz (kuyruk cap + drop sayacı `[ÖNERİ]`).
- ADR: ADR-106 §2.2a · kardeş PRD §05.2 E1 · Test: TC-PLR-112 · Blok: plr-p07.md

**NE-PLR-113 — Radyo kataloğu (SQLite, ADR-074)**
- Kabul: Migrate edilebilir şema; ekleme/düzenleme/silme.
- ADR: ADR-074 · ADR-106 kural 3 · Test: TC-PLR-113 · Blok: plr-p07.md

**NE-PLR-114 — Radio Browser API keşfi (opsiyonel)**
- Kabul: Ağ yoksa tamamen gizli; keşif sonuçları validator'dan geçmeden listelenmez.
- ADR: ADR-106 kural 3 · Test: TC-PLR-114 · Blok: plr-p07.md

**NE-PLR-115 — İstasyon test aracı (URL test/validate)**
- Kabul: Test: ICY header kontrolü + ilk N saniye decode + codec raporu.
- ADR: ADR-106 §2.2a (AIMP emsal Catalogs-Radio.ini) · Test: TC-PLR-115 · Blok: plr-p07.md

**NE-PLR-116 — Son istasyon hatırlama**
- Kabul: Config §06 ile aynı depo.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-116 · Blok: plr-p07.md

**NE-PLR-117 — Plasyon listesi UI (kategori/favori)**
- Kabul: Katalog verisi ADR-074 alanları; render sanitizasyonlu (NE-PLR-101).
- ADR: ADR-106 kural 3 · ADR-074 · Test: TC-PLR-117 · Blok: plr-p07.md

**NE-PLR-118 — 100 istasyon stres testi**
- Kabul: Rapor: oynatılan/istenen; backoff ölçümleri.
- ADR: ADR-106 §5.2 Ş3/Ş5/Ş6 · Test: TC-PLR-118 · Blok: plr-p07.md

**NE-PLR-119 — 10 istasyon paralel (tuning kuyruğu)**
- Kabul: Aynı anda 10 connect isteği: 1-2 aktif; kalanlar kuyruk/bırakma politikası (`⚠️ VR — politika detayı ADR-106'da yok; önerü: kullanıcı sinyali bekleyenleri iptal eder, arka planda kuyruk ≤2 devam eder)` .
- ADR: ADR-106 §5.2 Ş2 · Test: TC-PLR-119 · Blok: plr-p07.md

**NE-PLR-120 — Fuzz: HLS playlist + ICY corpus**
- Kabul: 0 crash; kapı 5.13'e entegre.
- ADR: ADR-106 §5.2 Ş7 · ADR-0105 kapı 5.13 ⚠️ · Test: TC-PLR-120 · Blok: plr-p07.md

**NE-PLR-121 — Bant sayısı seçimi (10/15/20/31)**
- Kabul: Seçim anında aktif; E5 API ile engine'e iletilir; maskeler atomik yayılır.
- ADR: ADR-106 §2.2b kural 2 · Test: TC-PLR-121 · Blok: plr-p08.md

**NE-PLR-122 — 31-band tam grid**
- Kabul: Her bandın frekans etiketi grid'e birebir; `eq_grids_tests` yeşil.
- ADR: ADR-106 §2.2b · disk `eq_grids.h` · Test: TC-PLR-122 · Blok: plr-p08.md

**NE-PLR-123 — 10-band ISO alt küme**
- Kabul: 10-band modda slider'lar bu frekanslarda; VLC preset'i kendi setiyle okunur (zorla map yok).
- ADR: ADR-106 §2.2b kural 3 · Test: TC-PLR-123 · Blok: plr-p08.md

**NE-PLR-124 — 20-band (AIMP emsal aralığı)**
- Kabul: `eq_grids` tanımı sabit + teyit edilir; etiket korunur.
- ADR: ADR-106 §2.2b kural 4 · ADR-106 §1.4 · Test: TC-PLR-124 · Blok: plr-p08.md

**NE-PLR-125 — 15-band alt küme**
- Kabul: TEyitli listeyi kadar kilitli `[ÖNERİ — teyide kadar UI'da gizli]`.
- ADR: ADR-106 §2.2b kural 5 · Test: TC-PLR-125 · Blok: plr-p08.md

**NE-PLR-126 — Biquad bütçesi sabitliği**
- Kabul: Bant değiştirme sırasında tahsis yok (profil); 31-band worst-case benchmark.
- ADR: ADR-106 kural 4/6 · kardeş PRD §06.2 · Test: TC-PLR-126 · Blok: plr-p08.md

**NE-PLR-127 — Ramp 5-20 ms (click yasağı)**
- Kabul: Slider spam'de pop yok (waveform); ramp süresi seçilebilir `[ÖNERİ — UI'da 5/10/20 ms]`.
- ADR: ADR-105 §2.3 (ADR-106 kural 5) ⚠️ · Test: TC-PLR-127 · Blok: plr-p08.md

**NE-PLR-128 — Aktif olmayan band bypass**
- Kabul: CPU profili 10-band ile 31-band arasında (masraf düşer ama tahsis kalır).
- ADR: ADR-106 kural 4 · Test: TC-PLR-128 · Blok: plr-p08.md

**NE-PLR-129 — Preset biçimi (taşıma)**
- Kabul: Farklı makineye taşınabilir; grid_version uyuşmazlığında düzeltme + uyarı.
- ADR: ADR-106 kural 6 · Test: TC-PLR-129 · Blok: plr-p08.md

**NE-PLR-130 — Preset kaydet/yükle/sil/adlandır**
- Kabul: CRUD; fabrika preset'leri silinemez `[ÖNERİ]`.
- ADR: ADR-106 kural 6 · §10 NE-PLR-010 · Test: TC-PLR-130 · Blok: plr-p08.md

**NE-PLR-131 — Fabrika preset seti**
- Kabul: Her preset 31-band vektörü (taşıma formatında); Flat = all-zero (bit-perfect ön koşulu).
- ADR: YOK — `[ÖNERİ]` (ADR-106 yalnız preset biçimini tanımlar) · Test: TC-PLR-131 · Blok: plr-p08.md

**NE-PLR-132 — Flat/Bypass düğmesi**
- Kabul: İkisi de hash yolunu açar; UI'da ayrım net.
- ADR: kardeş PRD §06.1 D3 · §05.2 B3 · Test: TC-PLR-132 · Blok: plr-p08.md

**NE-PLR-133 — Preamp slider'ı**
- Kabul: Limiter aşımı engeller; UI'da clip göstergesi `[ÖNERİ]`.
- ADR: kardeş PRD §06.4 · Test: TC-PLR-133 · Blok: plr-p08.md

**NE-PLR-134 — Bant frekansı değiştirilemez**
- Kabul: UI'da frekans alanı salt-okunur (etiket).
- ADR: ADR-106 kural 7 · ADR-0105 §2.1 · Test: TC-PLR-134 · Blok: plr-p08.md

**NE-PLR-135 — Parametrik mod (ayrı sekme, M5+)**
- Kabul: İki mod geçişinde ramp; preset'ler ayrı kasa `[ÖNERİ]`.
- ADR: ADR-106 §2.2b kural 7 · ADR-0105 §2.1 · Test: TC-PLR-135 · Blok: plr-p08.md

**NE-PLR-136 — Radyoda EQ aktif**
- Kabul: Radyo modunda EQ preset uygulanır; bit-perfect rozeti YOK (lossy).
- ADR: ADR-106 kural 4 · Test: TC-PLR-136 · Blok: plr-p08.md

**NE-PLR-137 — EQ UI widget'ları**
- Kabul: 31 slider performanslı (virtualize `[ÖNERİ]`); keyboard-only erişim.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-137 · Blok: plr-p08.md

**NE-PLR-138 — EQ görselleştirme (yanıt eğrisi)**
- Kabul: 31 bant toplamı; ≤10 Hz yenileme non-RT.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-138 · Blok: plr-p08.md

**NE-PLR-139 — neva-eq ayrı pencere**
- Kabul: Ayrı süreç; C ABI E5/E6; ana UI ile senkron event.
- ADR: ADR-107 K2 · kardeş PRD §04.7 · Test: TC-PLR-139 · Blok: plr-p08.md

**NE-PLR-140 — EQ test vektörleri (radyo+dosya)**
- Kabul: Ş5: 0 çökme; ramp süresi ölçüm raporu.
- ADR: ADR-106 §5.2 Ş5 · Test: TC-PLR-140 · Blok: plr-p08.md

**NE-PLR-141 — LUFS hedefleme (normalizasyon)**
- Kabul: Kapalıyken 0 etki (bit-perfect); açıkken limiter ön koşulu.
- ADR: kardeş PRD §06.4 `[ÖNERİ]` · Test: TC-PLR-141 · Blok: plr-p08.md

**NE-PLR-142 — Crossfade ≠ gapless ayrımı (EQ etkileşimi)**
- Kabul: Geçişte EQ korunur (state yeni oturuma kopyalanır + ramp).
- ADR: kardeş PRD NE-PLR-067 · Test: TC-PLR-142 · Blok: plr-p08.md

**NE-PLR-143 — Cihaz-bağımlı EQ hatırlama**
- Kabul: Cihaz değişince preset değişimi teyitli.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-143 · Blok: plr-p08.md

**NE-PLR-144 — Preset import/export (dosya)**
- Kabul: Export/import; dönüştürücü yalnız kendi frekans setiyle.
- ADR: ADR-106 §1.4 (VLC/AIMP dump) · kardeş PRD §08.2 · Test: TC-PLR-144 · Blok: plr-p08.md

**NE-PLR-145 — Grid teyit işi (15/20-band kapanışı)**
- Kabul: ADR-106 §1.4'teki açık madde kapanır; etiketler kaldırılır/gerekçelenir.
- ADR: ADR-106 §1.4 · Test: TC-PLR-145 · Blok: plr-p08.md

**NE-PLR-146 — Yerel kütüphane (SQLite)**
- Kabul: Tarama → indeks → arama (sanatçı/album/tür/yıl); yeniden tarama artımlı.
- ADR: ADR-074 · ADR-004 · Test: TC-PLR-146 · Blok: plr-p08.md

**NE-PLR-147 — Çevrimdışı öncelik (kütüphane)**
- Kabul: Uçak modu testi.
- ADR: AGENTS §17 Edge 9 · Test: TC-PLR-147 · Blok: plr-p08.md

**NE-PLR-148 — Queue düzenleme**
- Kabul: Sıra değişiminde prefetch yeniden planlanır.
- ADR: kardeş PRD §05.2 E2 · Test: TC-PLR-148 · Blok: plr-p08.md

**NE-PLR-149 — Durum makinesi geçişleri (ADR-019 §1.4)**
- Kabul: Geçersiz geçişler reddedilir; her geçiş event E1 ile UI'a akar.
- ADR: ADR-019 §1.4 · ADR-018 · Test: TC-PLR-149 · Blok: plr-p08.md

**NE-PLR-150 — LOADING durumu UX**
- Kabul: LOADING→PLAYING geçiş süresi ölçülür; 4 s aşımları kullanıcıya yansır.
- ADR: ADR-106 §2.2a · Test: TC-PLR-150 · Blok: plr-p08.md

**NE-PLR-151 — ERROR durumu UX**
- Kabul: 3 başarısız reconnect → ERROR + istasyon adı + son hata kodu.
- ADR: ADR-106 kural 8 · kardeş PRD §13 · Test: TC-PLR-151 · Blok: plr-p08.md

**NE-PLR-152 — Transport kontrolleri**
- Kabul: C ABI E3/E4; radyoda seek devre dışı, next/prev katalog gezinmesi.
- ADR: kardeş PRD §05.2 · Test: TC-PLR-152 · Blok: plr-p08.md

**NE-PLR-153 — Cihaz seçimi UI (liste + varsayılan)**
- Kabul: Liste canlı (event E8); tercih kalıcı.
- ADR: ADR-019 F1 · kardeş PRD §05.2 E8 · Test: TC-PLR-153 · Blok: plr-p08.md

**NE-PLR-154 — Fallback bilgilendirmesi (UI)**
- Kabul: Bilgi satırı + rozet durumu (NE-PLR-043).
- ADR: kardeş PRD §04.4-§04.5 · Test: TC-PLR-154 · Blok: plr-p08.md

**NE-PLR-155 — Metadata gösterimi**
- Kabul: Uzun metin kırpma + tooltip `[ÖNERİ]`; XSS payload düz metin.
- ADR: ADR-106 kural 7 · Test: TC-PLR-155 · Blok: plr-p08.md

**NE-PLR-156 — Zamanlayıcı (uyku modu) `[ÖNERİ]`**
- Kabul: Süre bitiminde fade → STOPPED; dosya sonunda queue bitişi.
- ADR: YOK — `[ÖNERİ — persona türetme]` · Test: TC-PLR-156 · Blok: plr-p08.md

**NE-PLR-157 — Sıcaklık/kulak zarar koruması `[ÖNERİ]`**
- Kabul: Eşik LUFS+sure; kapatılabilir.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-157 · Blok: plr-p08.md

**NE-PLR-158 — Klavye kısayolları**
- Kabul: Çakışma yok (EQ sekmesi odaktayken slider okları korunur).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-158 · Blok: plr-p08.md

**NE-PLR-159 — Tray/sistem tepsisi `[ÖNERİ]`**
- Kabul: Tray menü; oynatma kapanışta durur (engine session temiz).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-159 · Blok: plr-p08.md

**NE-PLR-160 — Çoklu pencere (main + eq + matrix)**
- Kabul: İkinci instance açılışta öne çıkarır (single-instance).
- ADR: kardeş PRD NE-ENG-105 `[ÖNERİ]` · Test: TC-PLR-160 · Blok: plr-p08.md

**NE-PLR-161 — UI erişilebilirliği (klavye/focus)**
- Kabul: Checklist; ekran okuyucu uyumu `⚠️ VR (toolkit seçimi §18.1)` .
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-161 · Blok: plr-p08.md

**NE-PLR-162 — Karanlık/aydınlık tema `[ÖNERİ]`**
- Kabul: İki tema; sabit değer yok (token).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-162 · Blok: plr-p08.md

**NE-PLR-163 — UI performans (oynatma sırasında)**
- Kabul: UI spam'de xrun 0.
- ADR: kardeş PRD §07 · NE-PLR-008 · Test: TC-PLR-163 · Blok: plr-p08.md

**NE-PLR-164 — Config şeması sürümleme**
- Kabul: Eski sürüm → migrate; okunamayan → default + yedek.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-164 · Blok: plr-p08.md

**NE-PLR-165 — Crash kurtarma (yeniden başlatma)**
- Kabul: Sağlam kapanış bayrağı; crash sonrası sessiz başlangıç.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-165 · Blok: plr-p08.md

**NE-PLR-166 — OWASP tabanlı tehdit modeli**
- Kabul: §12.1 tehdit tablosu her release'de gözden geçirilir; yeni saldırı yüzeyi (yeni format/protokol) tabloya eklenmeden merge yok.
- ADR: ADR-106 §5 (security debate S1-S8) · AGENTS §5 · Test: TC-PLR-166 · Blok: plr-p09.md

**NE-PLR-167 — SSRF/özel-IP reddi (URL validator)**
- Kabul: §12.2 vektörleri: `http://127.0.0.1` · `http://169.254.169.254/latest/meta-data/` · `http://[::1]` · zincirleme redirect'te private IP → hepsi RED; yalnız IPv4 literal değil, DNS çözümü de kontrol edilir (çözüm → RED, sonra bağlanmadan iptal).
- ADR: ADR-106 kural 6 (S3) · Test: TC-PLR-167 · Blok: plr-p09.md

**NE-PLR-168 — Zincirleme redirect ≤5 (güvenlik sürümü)**
- Kabul: 6 hop'lu zincir RED; her hop'ta protocol + IP tekrar doğrulanır (NE-PLR-023 ile aynı test).
- ADR: ADR-106 kural 6 · Test: TC-PLR-168 · Blok: plr-p09.md

**NE-PLR-169 — HTTPS zorunlu + sertifika doğrulaması**
- Kabul: Self-signed / süresi dolmuş / hostname uyuşmazlığı RED; kapatma bayrağı yok (ayar gizli değil, varsayılan kapalı).
- ADR: ADR-106 §5.4 (mbedTLS 9.13.0) · Test: TC-PLR-169 · Blok: plr-p09.md

**NE-PLR-170 — HTML-escape (StreamTitle/StreamUrl/render)**
- Kabul: `<script>` payload'lı başlık düz metin görünür; escape katmanı tek yerde (render öncesi) — profil: escape edilmemiş yol varsa statik denetim FAIL.
- ADR: ADR-106 kural 7 (security S1) · Test: TC-PLR-170 · Blok: plr-p09.md

**NE-PLR-171 — 256 bayt cap + UTF-8 doğrulama (metadata)**
- Kabul: Fuzz corpus'unda geçersiz UTF-8 dizgileri; 0 crash; UI'da mojibake yok (replace karakter).
- ADR: ADR-106 kural 7/8 · Test: TC-PLR-171 · Blok: plr-p09.md

**NE-PLR-172 — XML DTD/kök XXE reddi (ICY v2)**
- Kabul: `<!DOCTYPE ... SYSTEM "file:///C:/...">` içeren metaveri → reddedilir; dosya erişimi tetiklenmez.
- ADR: ADR-106 kural 8 (Ş8) · Test: TC-PLR-172 · Blok: plr-p09.md

**NE-PLR-173 — HLS segment cap (50 MB) + entry cap (1000) + derinlik (≤1)**
- Kabul: 1001 girdili playlist RED; 51 MB segment RED; iç içe playlist RED.
- ADR: ADR-106 kural 8 (Ş8) · §07.5 Ş8 · Test: TC-PLR-173 · Blok: plr-p09.md

**NE-PLR-174 — Protocol whitelist (dosya/concat/crypto reddi)**
- Kabul: `concat:http|file` gibi karışık dizgiler de RED (parser bypass denemeleri §12.2).
- ADR: ADR-106 kural 6 · Test: TC-PLR-174 · Blok: plr-p09.md

**NE-PLR-175 — Config/preset dosyalarında güvenli ayrıştırma**
- Kabul: 10 MB'luk "preset" dosyası RED/ kırpılır; derin iç içe JSON → depth cap `[ÖNERİ — 64]`.
- ADR: ADR-091 · AGENTS §5 (unserialize(user_input) yasak) · Test: TC-PLR-175 · Blok: plr-p09.md

**NE-PLR-176 — Katalog/SQLite injection koruması**
- Kabul: `O'Brien` sanatçı adı + SQL payload arama sorguları; sonuç beklenen; statik denetim (concat sorgu yok).
- ADR: AGENTS §5 (parametreli sorgu zorunlu, SELECT * yasak) · Test: TC-PLR-176 · Blok: plr-p09.md

**NE-PLR-177 — Yol güvenliği (path traversal)**
- Kabul: Tarama dizininde `..` kaçışı RED; kısayol/AGPL içeriği değil sadece indeks kapsamı.
- ADR: YOK — `[ÖNERİ]` (klasik dosya güvenliği) · Test: TC-PLR-177 · Blok: plr-p09.md

**NE-PLR-178 — FFI sınırında clamp (engine'le ortak)**
- Kabul: Negatif index · 1e9 ms · 10000 karakter path → INVALID_ARG (NE-ENG-157 ile aynı vektör).
- ADR: kardeş PRD §05.1 · Test: TC-PLR-178 · Blok: plr-p09.md

**NE-PLR-179 — Tek instance + IPC yüzeyi (neva-host)**
- Kabul: Farklı kullanıcıdan gelen IPC mesajı reddedilir; mesaj şeması doğrulanır (bilinmeyen mesaj → drop).
- ADR: ADR-107 §8.6 (U4) · kardeş PRD NE-ENG-105 · Test: TC-PLR-179 · Blok: plr-p09.md

**NE-PLR-180 — Supply chain (bağımlılık taraması)**
- Kabul: CI dependency-audit; kritik CVE = release bloke; lock dosyası repo'da (FFmpeg harici vendor `⚠️ VR — vendor stratejisi §14.5)`.
- ADR: AGENTS §5 · kardeş PRD NE-ENG-114 · Test: TC-PLR-180 · Blok: plr-p09.md

**NE-PLR-181 — RAM ≤400 MB bütçesi (ölçümü)**
- Kabul: Ölçüm: 1000 dosyalık kütüphane + oynatma + EQ 31-band; RSS ≤400 MB.
- ADR: ADR-0105 §2.9 ⚠️ · Test: TC-PLR-181 · Blok: plr-p09.md

**NE-PLR-182 — CPU bütçesi (ses-only)**
- Kabul: Bench raporu §06.4.
- ADR: ADR-0105 §5.6 ⚠️ · ADR-106 §1.3 · Test: TC-PLR-182 · Blok: plr-p09.md

**NE-PLR-183 — Latency <20 ms (ölçüm)**
- Kabul: Rapor; kardeş PRD TC-ENG-089/090 ile ortak düzenek.
- ADR: ADR-0105 §2.10 ⚠️ · Test: TC-PLR-183 · Blok: plr-p09.md

**NE-PLR-184 — Kapı 5.1 (bit-perfect) — player koşulları**
- Kabul: 12/12 + guard negatif denetimi.
- ADR: ADR-0105 kapı 5.1 ⚠️ · ADR-106 §5.1 Ş1/Ş4 · Test: TC-PLR-087/040 · Blok: plr-p09.md

**NE-PLR-185 — Kapı 5.10 (gecikme)**
- Kabul: Rapor arşivi (NE-PLR-084 ile aynı).
- ADR: ADR-0105 kapı 5.10 ⚠️ · Test: TC-PLR-084/183 · Blok: plr-p09.md

**NE-PLR-186 — Kapı 5.11 (A/V sync P95 ≤20 ms)**
- Kabul: P95 raporu + max sapma.
- ADR: ADR-0105 kapı 5.11 ⚠️ · Test: TC-PLR-078/090 · Blok: plr-p09.md

**NE-PLR-187 — Kapı 5.12 (gapless vektörleri)**
- Kabul: Grup D testlerinin tamamı + ölçüm raporu.
- ADR: ADR-0105 kapı 5.12 ⚠️ · Test: TC-PLR-086 · Blok: plr-p09.md

**NE-PLR-188 — Kapı 5.13 (fuzz)**
- Kabul: Fuzz raporu release dosyasında.
- ADR: ADR-0105 kapı 5.13 ⚠️ · ADR-106 Ş7 · Test: TC-PLR-085/120 · Blok: plr-p09.md

**NE-PLR-189 — RT stres (player:UI spam + uzun süre)**
- Kabul: Kardeş PRD G4 ile birlikte raporlanır.
- ADR: S3 §6.1 · NE-PLR-163 · Test: TC-PLR-163/054 · Blok: plr-p09.md

**NE-PLR-190 — Leak (1000 cycle play/stop + katalog tarama)**
- Kabul: LSan/Windows LeakSanitizer temiz `⚠️ VR (Windows aracı seçimi)` .
- ADR: kardeş PRD NE-ENG-141 · Test: TC-PLR-190 · Blok: plr-p09.md

**NE-PLR-191 — TSan (çoklu-thread: UI + tuner + control spam)**
- Kabul: Linux CI sanitizörlü build.
- ADR: kardeş PRD NE-ENG-131 · Test: TC-PLR-191 · Blok: plr-p09.md

**NE-PLR-192 — Radyo stres vektörleri (ADR-106 §5.2 Ş1-Ş7)**
- Kabul: 7/7 rapor; §11.5'te tek tek.
- ADR: ADR-106 §5.2 · Test: TC-PLR-118/119/140/120 · Blok: plr-p09.md

**NE-PLR-193 — UI a11y checklist (klavye/focus/renk)**
- Kabul: Checklist release öncesi doldurulur.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-161 · Blok: plr-p09.md

**NE-PLR-194 — Yerelleştirme testi (TR/EN) — karara bağlı**
- Kabul: Karar yoksa test yok (kapsam dışı etiketi korunur).
- ADR: YOK · Test: TC-PLR-194 (şartlı) · Blok: plr-p09.md

**NE-PLR-195 — Metrik regresyon kapısı (%10)**
- Kabul: Baseline + PR etiketi.
- ADR: kardeş PRD NE-ENG-100 `[ÖNERİ]` · Test: TC-PLR-195 · Blok: plr-p09.md

**NE-PLR-196 — Test raporu şablonu (release)**
- Kabul: Şablon doldurulmuş olarak release PR'ında.
- ADR: kardeş PRD NE-ENG-144 · Test: — · Blok: plr-p09.md

**NE-PLR-197 — Determinism (2× çalıştırma hash)**
- Kabul: CI 2× çalıştırma.
- ADR: kardeş PRD NE-ENG-145 · Test: TC-PLR-197 · Blok: plr-p09.md

**NE-PLR-198 — ABI sembol denetimi (player birleşimi)**
- Kabul: CI grep.
- ADR: kardeş PRD NE-ENG-142 · Test: TC-PLR-198 · Blok: plr-p09.md

**NE-PLR-199 — Kontrat testi (durum makinesi + event zinciri)**
- Kabul: Geçersiz geçiş reddi + event sırası doğrulaması.
- ADR: ADR-019 §1.4 · kardeş PRD §05.2 · Test: TC-PLR-149/199 · Blok: plr-p09.md

**NE-PLR-200 — Flaky test yasağı**
- Kabul: CI'da retry=0.
- ADR: kardeş PRD §11.5 (bu PRD) `[ÖNERİ]` · Test: — · Blok: plr-p09.md

**NE-PLR-201 — C ABI minor sürüm genişlemesi (E1-E10)**
- Kabul: ABI diff raporu; eski header ile yeni DLL çalışır (forward uyum).
- ADR: ADR-106 kural 2 · kardeş PRD §05.2/§05.4 · Test: TC-PLR-201 · Blok: plr-p09.md

**NE-PLR-202 — FFI dilleri (Python ctypes · C# P/Invoke · 25 dil)**
- Kabul: Örnek: Python ctypes ile play/stop smoke testi (CI opsiyonel).
- ADR: ADR-0105 v1.0.2 §3 ⚠️ · Vision (25 dil) · Test: TC-PLR-202 · Blok: plr-p09.md

**NE-PLR-203 — "25 dil" tanımı (metrik)**
- Kabul: Karar ADR veya bu PRD §18.5'te listelenir; ölçüm "C ABI + 5 dil smoke" olarak daraltılabilir `[ÖNERİ]`.
- ADR: ADR-0105 U2 ⚠️ · Vision · Test: TC-PLR-203 · Blok: plr-p09.md

**NE-PLR-204 — neva-mcp (MCP sunucu)**
- Kabul: Karar sonrası: bridge C ABI üzerinden; yetki modeli minimal `[ÖNERİ — salt localhost + yalnız transport komutları]`.
- ADR: ADR-0105 v1.4.0 `[ÖNERİ]` · ADR-107 §8.5 (kardeş) · Test: TC-PLR-204 (şartlı) · Blok: plr-p09.md

**NE-PLR-205 — neva-remote (VoiceMeeterRemote/VBAN emsal)**
- Kabul: Tasarım ADR (auth modeli `[ÖNERİ — token/localhost varsayılan]`); core'a ağ girmez ( kardeş NE-ENG-108/159).
- ADR: ADR-0105 v1.4.0 `[ÖNERİ]` · ADR-107 K2 · Test: TC-PLR-205 (şartlı) · Blok: plr-p09.md

**NE-PLR-206 — Web UI (UPnify emsal) — U4 kararı**
- Kabul: Karar ADR; şifre/localhost varsayılan `[ÖNERİ]` (UPnify: şifreli işlevsellik — emsal, kopya değil).
- ADR: ADR-0105 U4 ⚠️ · ADR-107 §8.6 · Test: TC-PLR-206 (şartlı) · Blok: plr-p09.md

**NE-PLR-207 — Katalog dışa aktarma (CSV/M3U) `[ÖNERI]`**
- Kabul: M3U/CSV; yol yazımı kullanıcı onaylı (yerel yol sızıntısı bilinçli).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-207 · Blok: plr-p09.md

**NE-PLR-208 — M3U/M3U8 çalma listesi içe aktarma (yerel)**
- Kabul: 1000 satır cap; `file:` satırları kök sınırlamasıyla (NE-PLR-177).
- ADR: YOK — `[ÖNERİ]` (radyo playlist'i ile ayrık: ADR-106 kural 8) · Test: TC-PLR-208 · Blok: plr-p09.md

**NE-PLR-209 — Hotkey/global kısayol (OS kaydı) `[ÖNERİ]`**
- Kabul: Çakışma yönetimi; kayıt geri alınabilir.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-209 · Blok: plr-p09.md

**NE-PLR-210 — Bildirim (OS toast) `[ÖNERİ]`**
- Kabul: Metadata sanitizasyonlu (NE-PLR-101); varsayılan kapalı `[ÖNERİ]`.
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-210 · Blok: plr-p09.md

**NE-PLR-211 — Plugin yüzeyi yok (kapsam kararı)**
- Kabul: Kodda plugin loader yok; karar tersine dönerse ADR gerekir.
- ADR: YOK — `[ÖNERİ — kapsam teyidi]` · Test: TC-PLR-211 (statik denetim) · Blok: plr-p09.md

**NE-PLR-212 — Sürücü/eklenti kurulumu yok (taşıma)**
- Kabul: Kurulum/first-run yalnız var olan cihazları listeler; eksik sürücüde yol gösterir `[ÖNERİ — indirme linki UI'da, otomatik indirme yok]`.
- ADR: kardeş PRD §04.2 (platform ayrımı) `[ÖNERİ]` · Test: TC-PLR-212 · Blok: plr-p09.md

**NE-PLR-213 — Sürüm/telemetri ağ çağrısı yok**
- Kabul: Ağ trafiği profili: yalnız radyo/keşif; gizli çağrı yok (firewall testi).
- ADR: YOK — `[ÖNERİ]` · Test: TC-PLR-213 · Blok: plr-p09.md

**NE-PLR-214 — Log hijyeni (secret/URL sızıntısı)**
- Kabul: Log örnekleri gözden geçirilir; CI'da secret-scan log dizinine de bakar.
- ADR: kardeş PRD NE-ENG-156 · AGENTS §5 · Test: TC-PLR-214 · Blok: plr-p09.md

**NE-PLR-215 — Dış API envanteri (tek tablo)**
- Kabul: Tablo §14.5/§17.2 ile hizalı; yeni satır = tehdit tablosu güncellemesi (NE-PLR-166).
- ADR: ADR-106 kural 3/6 · kardeş PRD §17 (izlenebilirlik) · Test: TC-PLR-215 (envanter denetimi) · Blok: plr-p09.md


## §21 Ek K — Test Künyesi Sözlüğü (Player · otomatik extraction)

> **Otomatik üretildi** §11 bloklarından (başlık + gereksinim + katman + otomasyon + beklenen). Bağlayıcı metin §11'dir.
> Kapsam: 215 blok (TC-PLR-001..215, grup blokları dahil).

**TC-PLR-001 — Ortak çekirdek + ince adapter**
- Gereksinim: NE-PLR-001 · Katman: build · · Otomasyon: -
- Beklenen (özet): Paylaşımlı dosyalar aynı; OS'a özgü yalnız adapter/ince satırlar; paylaşımsız kod artışı = red notu.

**TC-PLR-002 — C ABI üzerinden engine kullanımı**
- Gereksinim: NE-PLR-002 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Yalnız `neva_session_*`; aksi FAIL.

**TC-PLR-003 — api_version handshake**
- Gereksinim: NE-PLR-003 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): ERROR durumu + sürüm mesajı; crash yok; doğru sürümde açılır.

**TC-PLR-004 — Durum makinesi kontratı**
- Gereksinim: NE-PLR-004 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Geçerli: kabul + event; geçersiz: reddedilir (durum değişmez, hata event'i).

**TC-PLR-005 — Event callback (E1)**
- Gereksinim: NE-PLR-005 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Event'ler non-RT kuyruktan; UI thread marshal; ABI minor sürüm (eski fonksiyonlar sabit).

**TC-PLR-006 — position/duration + seek erişimi**
- Gereksinim: NE-PLR-006 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Bilinen aralık monoton; radyoda seek devre dışı/red; duration yok kod.

**TC-PLR-007 — Cihaz listesi + seçim (E8)**
- Gereksinim: NE-PLR-007 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Liste engine'den; seçim canlı; tercih kalıcı; hot-plug event gelir.

**TC-PLR-008 — UI↔RT ayrımı**
- Gereksinim: NE-PLR-008 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Doğrudan erişim 0; veri yalnız kuyruklarla akar.

**TC-PLR-009 — UI toolkit kararı kapısı**
- Gereksinim: NE-PLR-009 · Katman: proses · · Otomasyon: -
- Beklenen (özet): ADR yoksa UI fazı başlatılmaz (§15 gate); seçenek listesi §18.1.

**TC-PLR-010 — Config kalıcılığı**
- Gereksinim: NE-PLR-010 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Son durum geri gelir; bozuk config → default + uyarı (crash yok; yedek `[ÖNERİ]`).

**TC-PLR-011 — Engine sürüm birleşimi**
- Gereksinim: NE-PLR-011 · Katman: build · · Otomasyon: -
- Beklenen (özet): Minor uyumlu; major uyuşmazlıkta net reddi (NE-PLR-003).

**TC-PLR-012 — neva-eq / neva-matrix süreç ayrımı**
- Gereksinim: NE-PLR-012 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ayrı süreç; C ABI; kapatma ana oturumu etkilemez.

**TC-PLR-013 — Çevrimdışı öncelik**
- Gereksinim: NE-PLR-013 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Tam işlevsel; ağ çağrısı denemesi bile yok (yerel dosya yolu); radyo sekmesi ağsız reddi net.

**TC-PLR-014 — Yerelleştirme kapsam notu**
- Gereksinim: NE-PLR-014 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Kapsam etiketi `⚠️ VR` korunur.

**TC-PLR-015 — Kardeş PRD sınırı**
- Gereksinim: NE-PLR-015 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Yalnız sözleşmeler (API/ölçüm); implementasyon talimatı yok — varsa review'da düzeltilir.

**TC-PLR-016 — Decode matrisi**
- Gereksinim: NE-PLR-016 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Hepsi açılır; bilinmeyen uzantı `UNSUPPORTED_FORMAT` (kod 3); 0 crash.

**TC-PLR-017 — Yerel demux önceliği**
- Gereksinim: NE-PLR-017 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): FFmpeg yüklenmez `[ÖNERİ — log ile kanıt]`; MP3 yolunda yüklenir (kapsam dahili).

**TC-PLR-018 — soxr producer-side**
- Gereksinim: NE-PLR-018 · Katman: statik/L2 · · Otomasyon: -
- Beklenen (özet): RT'de resampler 0; dönüşüm producer'da; master clock cihaz.

**TC-PLR-019 — soxr lisansı (LGPL-2.1)**
- Gereksinim: NE-PLR-019 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): Dinamik ✅; statik ise `⚠️ LİSANS` etiketi §14'te; kaynak lisans dosyası dağıtımda.

**TC-PLR-020 — FFmpeg LGPL build**
- Gereksinim: NE-PLR-020 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): GPL bayrağı 0; lisans raporu eki.

**TC-PLR-021 — Protocol whitelist**
- Gereksinim: NE-PLR-021 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): http/https geçer; kalanları RED; FFmpeg'e ulaşmaz (katman kanıtı).

**TC-PLR-022 — HTTP09Allowed=0**
- Gereksinim: NE-PLR-022 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Bayrak 0; HTTP/0.9 yanıtı reddedilir.

**TC-PLR-023 — Redirect ≤5 + her hop validator**
- Gereksinim: NE-PLR-023 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Sınır doğru; her hop tam validator'dan geçer.

**TC-PLR-024 — Timeout 10 sn**
- Gereksinim: NE-PLR-024 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): ≤10 sn `±tolerans [ÖNERİ — 1 sn]` → backoff zinciri (TC-PLR-105).

**TC-PLR-025 — TLS doğrulama (mbedTLS)**
- Gereksinim: NE-PLR-025 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): İlk üç RED; geçerli kabul; kör kabul yolu yok (bayrak denetimi).

**TC-PLR-026 — decode → float32 ring**
- Gereksinim: NE-PLR-026 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Çıkış formatı tutarlı; writer okuyucuyu geçmez; ASan temiz.

**TC-PLR-027 — 24-bit kayıpsız yol**
- Gereksinim: NE-PLR-027 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Byte eşitlik kaynak 24-bit gövdeyle uyumlu `⚠️ VR (detay indeks'te yok)`; erken quantization yok.

**TC-PLR-028 — Queue prefetch hazır olma**
- Gereksinim: NE-PLR-028 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Normal koşulda geçişte starve 0; gecikme senaryosunda fade (§07.4) — ikisi de raporlanır.

**TC-PLR-029 — Bozuk dosya dayanıklılığı**
- Gereksinim: NE-PLR-029 · Katman: Kapı (5.13) · · Otomasyon: -
- Beklenen (özet): 0 crash; skip + UI uyarısı; kuyruk devam eder (tek dosya queue'yu durdurmaz).

**TC-PLR-030 — Tek transport (dosya ↔ radyo)**
- Gereksinim: NE-PLR-030 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Aynı anda aktif decode 1 (+prefetch); mod değişimi eski oturumu temiz kapatır (leak 0).

**TC-PLR-031 — Metadata okuma**
- Gereksinim: NE-PLR-031 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Alanlar okunur; tagsiz → dosya adı fallback; encoding doğru (mojibake yok).

**TC-PLR-032 — Kapak görseli güvenliği**
- Gereksinim: NE-PLR-032 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Normal gösterim; büyük → ölçek/RED (bellek cap `⚠️ VR`); bozuk → crash yok.

**TC-PLR-033 — ReplayGain davranışı**
- Gereksinim: NE-PLR-033 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kapalı = 0 gain (hash geçer); açık = uygulanır + rozet koşulu güncellenir.

**TC-PLR-034 — open_source URI ayrıştırma**
- Gereksinim: NE-PLR-034 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Dosya yolu lokal normalize; http(s) validator'a; `file://` RED (kural 6).

**TC-PLR-035 — Decode CPU bench**
- Gereksinim: NE-PLR-035 · Katman: bench · · Otomasyon: -
- Beklenen (özet): Rapor üretilir; eşik `⚠️ VR` — ADR-0105 dosyası ile bağlanır.

**TC-PLR-036 — Bit-perfect WASAPI Exclusive**
- Gereksinim: NE-PLR-036 · Katman: Kapı (5.1) · · Otomasyon: -
- Beklenen (özet): Byte-eşit; şartlar sağlanmazsa koşul dışı (sahte sonucu yok).

**TC-PLR-037 — Lossy kapsam ayrımı**
- Gereksinim: NE-PLR-037 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Lossy akışta rozet YOK; iddia metni ayrımı tutarlı.

**TC-PLR-038 — Format negosyasyon sırası (N1)**
- Gereksinim: NE-PLR-038 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Sıra 32f→32i→24-in-32; hepsi başarısızsa shared/fallback; log zinciri doğru.

**TC-PLR-039 — BUFFER_SIZE_NOT_ALIGNED kurtarma**
- Gereksinim: NE-PLR-039 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Yutulmaz; kurtarma başarılıysa devam, değilse zincir düşüşü.

**TC-PLR-040 — EQ aktifken hash engeli (inverted guard)**
- Gereksinim: NE-PLR-040 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): İkinci adımda SAHTE FAIL yok (koşul dışı); üçüncü adımda tekrar çalışır.

**TC-PLR-041 — Hi-res 192k/32f uçtan uca**
- Gereksinim: NE-PLR-041 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Byte-eşit; N1 sırası; desteklemeyen cihazda negosyasyon düşüşü.

**TC-PLR-042 — ASIO yolu (şartlı U1)**
- Gereksinim: NE-PLR-042 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Var: donanım pin formatı birebir + <10 ms (TC-ENG-089); yok: WASAPI'ye düşüş.

**TC-PLR-043 — Bit-perfect rozet üç koşulu**
- Gereksinim: NE-PLR-043 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Üçünde de rozet GİZLİ; üçü tamamsa görünür.

**TC-PLR-044 — DoP marker continuity**
- Gereksinim: NE-PLR-044 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Marker bütünlüğü; native DSD `⚠️ VR` ayrı karar.

**TC-PLR-045 — Shared mod rozet/uyarı**
- Gereksinim: NE-PLR-045 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Rozet YOK + bilgilendirme; hash testi koşul dışı (shared'da PASS yazılamaz).

**TC-PLR-046 — Otomatik exclusive→shared geçiş**
- Gereksinim: NE-PLR-046 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Süreklilik; event E8 ile UI bilgilendirmesi; kullanıcı kurtarma eşiği.

**TC-PLR-047 — Hız uyuşmazlığı sırası (F3)**
- Gereksinim: NE-PLR-047 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Sıra birebir; RT'de resampler 0; hash yalnız eşit formatta.

**TC-PLR-048 — Hot-plug kaybı**
- Gereksinim: NE-PLR-048 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Durum raporu (ERROR/bilgi); yeniden bağlanma; kullanıcı mesajı net; çökme yok.

**TC-PLR-049 — CI hash yolu (Null) + loopback ayrımı**
- Gereksinim: NE-PLR-049 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): CI yeşil; loopback imzasız release yok.

**TC-PLR-050 — 12 kombinasyon matrisi**
- Gereksinim: NE-PLR-050 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 12/12; eksik kombinasyon = release bloke.

**TC-PLR-051 — OS efektleri baypas (exclusive)**
- Gereksinim: NE-PLR-051 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Etki yok (session volume 1.0 sabit, efekt bayrakları kapalı); shared'da etki vardır + raporlanır.

**TC-PLR-052 — Null-test guard (radyo/lossy engeli)**
- Gereksinim: NE-PLR-052 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Koşul dışı (sahte FAIL yok); guard'sız koşul = FAIL (negatif denetim).

**TC-PLR-053 — Exclusive tercihi (kullanıcı kilidi)**
- Gereksinim: NE-PLR-053 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Otomatik shared'a geçmez; uyarı gösterir (kullanıcı onayı bekler `[ÖNERİ]`).

**TC-PLR-054 — 4 saat kararlılık**
- Gereksinim: NE-PLR-054 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): xrun 0 `⚠️ VR (4 sa eşiği bu PRD önerisi)`; leak 0; hash değişmedi (determinizm).

**TC-PLR-055 — Dither davranışı**
- Gereksinim: NE-PLR-055 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Dither yalnız dönüşümde; 16→16 hash byte-eşit (dither yok).

**TC-PLR-056 — MP3 Xing gapless**
- Gereksinim: NE-PLR-056 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): 0 boşluk (padding kırpma); faz kayması yok.

**TC-PLR-057 — FLAC gapless**
- Gereksinim: NE-PLR-057 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): Boşluksuz geçiş; hash doğrulamalı referans.

**TC-PLR-058 — Opus gapless**
- Gereksinim: NE-PLR-058 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): Boşluksuz geçiş.

**TC-PLR-059 — DSD gapless (DoP)**
- Gereksinim: NE-PLR-059 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): Marker bütünlüğü bozulmaz; geçiş temiz.

**TC-PLR-060 — Sınır korelasyonu ±5 ms**
- Gereksinim: NE-PLR-060 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): ≤±5 ms; >20 ms = FAIL.

**TC-PLR-061 — Prefetch penceresi (≥1 sn)**
- Gereksinim: NE-PLR-061 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): ≥1 saniyelik PCM hazır (starve fade tetiklenmez); eksiklikte fade davranışı §07.4.

**TC-PLR-062 — Karışık format kuyruk**
- Gereksinim: NE-PLR-062 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Cihaz formatı korunur; gerekiyorsa soxr; geçiş fade ≤1 `[ÖNERİ]`; 0 starve.

**TC-PLR-063 — Shuffle gapless**
- Gereksinim: NE-PLR-063 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Her geçişte prefetch hazır; 0 starve.

**TC-PLR-064 — Repeat + leak**
- Gereksinim: NE-PLR-064 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): RSS artışı ≤0 `[ÖNERİ]`; geçişler gapless.

**TC-PLR-065 — Son dosya → STOPPED**
- Gereksinim: NE-PLR-065 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ramp'li bitiş → STOPPED event; ani kesme yok `[ÖNERİ]`.

**TC-PLR-066 — Eksik tag fallback (uydurma yok)**
- Gereksinim: NE-PLR-066 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kırpma YOK (normal başlangıç / boşluk kabul); uydurma kırpma = FAIL (bozulma üretir).

**TC-PLR-067 — Crossfade ayrımı**
- Gereksinim: NE-PLR-067 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İddia kapatılır (UI dil); 2 decode oturumu bütçe etkisi raporlanır.

**TC-PLR-068 — Gapless vektör kütüphanesi**
- Gereksinim: NE-PLR-068 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Tüm vektörler yeşil; eksik vektör = kapı eksik (release bloke).

**TC-PLR-069 — Konum sayaçları geçişi**
- Gereksinim: NE-PLR-069 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Parça sayacı sıfırlanır; toplam konum `[ÖNERİ]` tutarlı; UI ayrımı net.

**TC-PLR-070 — DSD→PCM geçişi**
- Gereksinim: NE-PLR-070 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): State reset (TC-ENG-078); pop transient'sız.

**TC-PLR-071 — 5.1 kuyruk geçişi**
- Gereksinim: NE-PLR-071 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kanal sayacı uyumu; downmix matrisi kararı olmadan son adım `[ÖNERİ]`.

**TC-PLR-072 — Seek sonrası gapless**
- Gereksinim: NE-PLR-072 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçiş hâlâ gapless; padding bilgisi seek'te kaybolmaz.

**TC-PLR-073 — Pause/sınır beklemesi**
- Gereksinim: NE-PLR-073 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Beklemede decode duraklar (bellek artışı yok `[ÖNERİ]`); devam gapless korunur.

**TC-PLR-074 — Sınır boşluğu tanımı (0 frame)**
- Gereksinim: NE-PLR-074 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): 0 frame boşluk; geçiş rampası ≤2 ms `[ÖNERİ]`; ölçüm raporu kapı dosyasında.

**TC-PLR-075 — Radio stream→stream (reconnect dahil)**
- Gereksinim: NE-PLR-075 · Katman: Kapı (5.12) · · Otomasyon: -
- Beklenen (özet): Konum koruma · prebuffer 2-4 s · decoder reset · 20 ms fade-in; korelasyon ≤±5 ms.

**TC-PLR-076 — libplacebo render (şartlı faz)**
- Gereksinim: NE-PLR-076 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): HDR metadata doğru; ses engine'de (çift yol yok).

**TC-PLR-077 — Master clock = cihaz**
- Gereksinim: NE-PLR-077 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Drift düzeltilir; video saatine göre ses kayması yok (ses referans).

**TC-PLR-078 — A/V sync P95 ≤20 ms (5.11)**
- Gereksinim: NE-PLR-078 · Katman: Kapı (5.11) · · Otomasyon: -
- Beklenen (özet): P95 ≤20 ms; rapor arşivi.

**TC-PLR-079 — VRAM bütçesi ölçümü**
- Gereksinim: NE-PLR-079 · Katman: bench · · Otomasyon: -
- Beklenen (özet): ~400-700 MB `⚠️ VR`; §7.5 açık maddesi kapanır (ölçüm raporu).

**TC-PLR-080 — VRAM merdiveni (V0-V5)**
- Gereksinim: NE-PLR-080 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Her kademe davranışı tanımlı `⚠️ VR (kademe içerikleri indeks'te yok)`; ses etkilenmez.

**TC-PLR-081 — VRAM bench ortamı**
- Gereksinim: NE-PLR-081 · Katman: bench · · Otomasyon: -
- Beklenen (özet): Repeatable sonuç + hedef-sınıf uyarısı (%19,5 hesabı) raporda.

**TC-PLR-082 — RAM ≤400 MB (video dahil bütçe)**
- Gereksinim: NE-PLR-082 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): ≤400 MB; ihlalde tanımlı davranış/uyarı `[ÖNERİ]` .

**TC-PLR-083 — Gecikme bütçesi (blok sınırı)**
- Gereksinim: NE-PLR-083 · Katman: Kapı (5.10) · · Otomasyon: -
- Beklenen (özet): <20 ms; L1-L4 `⚠️ VR` tanımlanana kadar en küçük desteklenen blok.

**TC-PLR-084 — Kapı 5.10 raporu**
- Gereksinim: NE-PLR-084 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Eşiği aşan build release adayı değil; rapor dosyası mevcut.

**TC-PLR-085 — Kapı 5.13 (fuzz)**
- Gereksinim: NE-PLR-085 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 0 crash · 0 OOB; rapor release dosyasında.

**TC-PLR-086 — Kapı 5.12 (gapless vektörleri)**
- Gereksinim: NE-PLR-086 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Hepsi yeşil; ölçüm raporu (±5 ms).

**TC-PLR-087 — Kapı 5.1 + inverted guard (player)**
- Gereksinim: NE-PLR-087 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 12/12 + guard doğru davranış + sahte-PASS/FAIL denetimi.

**TC-PLR-088 — Video format kapsamı**
- Gereksinim: NE-PLR-088 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İkisi de oynar; encode kapsam dışı (deneme reddedilir).

**TC-PLR-089 — Ses↔video mod geçişi**
- Gereksinim: NE-PLR-089 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Backend oturumu korunur (negosyasyon tekrarı yok); süreklilik.

**TC-PLR-090 — Pencere/video drift**
- Gereksinim: NE-PLR-090 · Katman: Kapı (5.11) · · Otomasyon: -
- Beklenen (özet): P95 ≤20 ms + maks sapma belgelenir.

**TC-PLR-091 — SNR >105 dB**
- Gereksinim: NE-PLR-091 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): >105 dB; düzeneği tabanı notu.

**TC-PLR-092 — THD+N <%0.005**
- Gereksinim: NE-PLR-092 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): <%0.005; rapor.

**TC-PLR-093 — Uçtan uca float32**
- Gereksinim: NE-PLR-093 · Katman: statik/L2 · · Otomasyon: -
- Beklenen (özet): Tek integer kesme yok; hash şart sağlanınca byte-eşit.

**TC-PLR-094 — mpv yokluğu**
- Gereksinim: NE-PLR-094 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 bağımlılık; RED satırı korunur.

**TC-PLR-095 — Video bench (şartlı)**
- Gereksinim: NE-PLR-095 · Katman: bench · · Otomasyon: -
- Beklenen (özet): Rapor; eşik `⚠️ VR` (ADR-0105 dosyası).

**TC-PLR-096 — ICY v1+v2 ayrıştırma**
- Gereksinim: NE-PLR-096 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): StreamTitle/StreamUrl event akıyor; parser engine'de (`icy_parser.h`), player thread işler.

**TC-PLR-097 — HLS oynatma**
- Gereksinim: NE-PLR-097 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Akış oynar; whitelist RED (NE-PLR-021 zinciri).

**TC-PLR-098 — DASH (şartlı faz 2)**
- Gereksinim: NE-PLR-098 · Katman: şartlı · · Otomasyon: -
- Beklenen (özet): Karar yoksa UI'da gizli; test koşul dışı.

**TC-PLR-099 — Codec matrisi**
- Gereksinim: NE-PLR-099 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Hepsi oynar; FDK bayrağı build'de yok.

**TC-PLR-100 — FDK-AAC yasağı**
- Gereksinim: NE-PLR-100 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0; teyit gelirse ayrı ADR.

**TC-PLR-101 — StreamTitle sanitizasyon**
- Gereksinim: NE-PLR-101 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Escape + 256 bayt cap + UTF-8 doğrulama; düz metin render; 0 crash.

**TC-PLR-102 — StreamUrl tıklanamaz**
- Gereksinim: NE-PLR-102 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Düz metin; anchor/onclick yok; kopyala seçeneği `[ÖNERİ]`.

**TC-PLR-103 — Parser limitleri (5.13 Ş8)**
- Gereksinim: NE-PLR-103 · Katman: L1/Kapı · · Otomasyon: -
- Beklenen (özet): Sınır içi OK, dışı RED; fuzz corpus ile doğrulanır.

**TC-PLR-104 — URL Validator tek kapı**
- Gereksinim: NE-PLR-104 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Hepsi RED/OK doğru; validator bypass 0; her redirect hop + post-connect IP doğrulaması.

**TC-PLR-105 — Backoff (1s→30s ±%20)**
- Gereksinim: NE-PLR-105 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): 1,2,4,8,16,30,30… cap; jitter ±%20 (sync-thundering); tablo raporda.

**TC-PLR-106 — 3 başarısız → ERROR**
- Gereksinim: NE-PLR-106 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Otomatik deneme durur; durum ERROR + mesaj; elle "yeniden dene" serbest.

**TC-PLR-107 — Prebuffer 2-4 s**
- Gereksinim: NE-PLR-107 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İlk ses ≤4 s; starve fade yok (tampon yeterli).

**TC-PLR-108 — Fade-in 20 ms**
- Gereksinim: NE-PLR-108 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 20 ms rampa; pop yok (spektral).

**TC-PLR-109 — STARVE/OVERFLOW davranışı**
- Gereksinim: NE-PLR-109 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): §04.2 şeması birebir; akış sürekliliği korunur (radyoda drop politikası).

**TC-PLR-110 — TunerThread non-RT**
- Gereksinim: NE-PLR-110 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0; ağ/parse yalnız non-RT.

**TC-PLR-111 — R128 sidechain non-RT**
- Gereksinim: NE-PLR-111 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): RT süresinde artış yok; canlı LUFS akıyor.

**TC-PLR-112 — Metadata zinciri (spam dayanıklılık)**
- Gereksinim: NE-PLR-112 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): UI donmaz; kuyruk cap + drop sayacı `[ÖNERİ]`; sanitizasyon zinciri korunur.

**TC-PLR-113 — Radyo kataloğu (ADR-074)**
- Gereksinim: NE-PLR-113 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): ADR-074 alanları; parametreli sorgu (TC-PLR-176 ile ortak); migrate temiz.

**TC-PLR-114 — Radio Browser keşfi (şartlı ağ)**
- Gereksinim: NE-PLR-114 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ağ yoksa tamamen gizli; sonuçlar validator'dan geçmeden listelenmez.

**TC-PLR-115 — İstasyon test aracı**
- Gereksinim: NE-PLR-115 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): ICY header kontrolü + ilk N sn decode + codec raporu net.

**TC-PLR-116 — Son istasyon hatırlama**
- Gereksinim: NE-PLR-116 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Son istasyon gelir (config kalıcılığı §06 depo).

**TC-PLR-117 — Katalog UI (arama/favori)**
- Gereksinim: NE-PLR-117 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Doğru filtre; XSS payload başlık düz metin (TC-PLR-101 zinciri).

**TC-PLR-118 — 100 istasyon stres (Ş3/Ş6)**
- Gereksinim: NE-PLR-118 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 0 çökme; metaveri akıyor; oynatılan/istenen raporda.

**TC-PLR-119 — 10 paralel tuning (Ş2)**
- Gereksinim: NE-PLR-119 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Gerçekçi 1-2 aktif akış; kalanlar kuyruk/bırakma politikası `⚠️ VR (önerü: kullanıcı sinyali bekleyenleri iptal, arka plan kuyruk ≤2)`.

**TC-PLR-120 — Fuzz: HLS + ICY corpus (Ş7)**
- Gereksinim: NE-PLR-120 · Katman: Kapı (5.13) · · Otomasyon: -
- Beklenen (özet): 0 crash; kapı 5.13'e entegre.

**TC-PLR-121 — Bant sayısı seçimi (E5)**
- Gereksinim: NE-PLR-121 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçiş anında aktif; tahsis 0; varsayılan `⚠️ VR` (önerü 31).

**TC-PLR-122 — 31-band frekans doğruluğu**
- Gereksinim: NE-PLR-122 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Birebir; `eq_grids_tests` yeşil.

**TC-PLR-123 — 10-band ISO + VLC ayrımı**
- Gereksinim: NE-PLR-123 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): ISO listesi; VLC seti kendi frekanslarıyla okunur (zorla map yok — hata verir/atlama).

**TC-PLR-124 — 20-band (teyitli aralık)**
- Gereksinim: NE-PLR-124 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Teyit yoksa `⚠️ VR` korunur; tanımsızsa UI gizli `[ÖNERİ]`.

**TC-PLR-125 — 15-band (teyitli)**
- Gereksinim: NE-PLR-125 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Teyide kadar kilitli/gizli `[ÖNERİ]`.

**TC-PLR-126 — Biquad bütçesi sabitliği (E5)**
- Gereksinim: NE-PLR-126 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçişte tahsis 0; benchmark worst-case (31) raporlanır.

**TC-PLR-127 — Ramp 5-20 ms (pop analizi)**
- Gereksinim: NE-PLR-127 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Pop yok; süre 5/10/20 ms seçeneklerine uyar `[ÖNERİ]`.

**TC-PLR-128 — Pasif band bypass (CPU)**
- Gereksinim: NE-PLR-128 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 10-band'te masraf düşer; tahsis kalır (bypass maskesi).

**TC-PLR-129 — Preset taşıma biçimi**
- Gereksinim: NE-PLR-129 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Taşınabilir; uyuşmazlıkta düzeltme + uyarı; bilinmeyen anahtar ignore (TC-PLR-175 ile).

**TC-PLR-130 — Preset CRUD**
- Gereksinim: NE-PLR-130 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): CRUD; fabrika preset'leri silinemez `[ÖNERİ]`; çakışma adı uyarısı `[ÖNERİ]`.

**TC-PLR-131 — Fabrika preset seti**
- Gereksinim: NE-PLR-131 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Flat 0 dB tümü (hash yolunu açar); diğerleri §08.3 taslak `⚠️ VR (set önerü)`.

**TC-PLR-132 — Flat vs Bypass ayrımı**
- Gereksinim: NE-PLR-132 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İkisi de byte-eşit; UI'da ayrı düğme/durum; rozet ikinde de görünür.

**TC-PLR-133 — Preamp + clip göstergesi**
- Gereksinim: NE-PLR-133 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): True-peak ceiling aşılmaz; uyarı eşiği `⚠️ VR (önerü +6 dB)`; bit-perfect yolda 0 dB = identity.

**TC-PLR-134 — Frekans etiketi salt-okunur**
- Gereksinim: NE-PLR-134 · Katman: statik/L2 · · Otomasyon: -
- Beklenen (özet): Salt-okunur (etiket); parametrik mod ayrı.

**TC-PLR-135 — Parametrik mod ayrımı (şartlı faz)**
- Gereksinim: NE-PLR-135 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İki ayrı state/kasa `[ÖNERİ]`; geçiş ramp'li.

**TC-PLR-136 — Radyoda EQ aktif**
- Gereksinim: NE-PLR-136 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): EQ devrede (kural 4); rozet YOK (lossy).

**TC-PLR-137 — EQ widget performansı + keyboard**
- Gereksinim: NE-PLR-137 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Oynatma sırasında akıcı `[ÖNERİ — 60 fps]`; keyboard-only tam erişim.

**TC-PLR-138 — Yanıt eğrisi çizimi (şartlı)**
- Gereksinim: NE-PLR-138 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Tutarlılık toleransı `⚠️ VR`; yenileme ≤10 Hz non-RT.

**TC-PLR-139 — neva-eq entegrasyonu (şartlı)**
- Gereksinim: NE-PLR-139 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): E5/E6 çağrıları yansır; kapatma ana oturumu etkilemez.

**TC-PLR-140 — EQ vektör testleri (Ş5)**
- Gereksinim: NE-PLR-140 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 0 çökme; rampa ölçüm raporu.

**TC-PLR-141 — LUFS hedefleme (şartlı)**
- Gereksinim: NE-PLR-141 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kapalı = 0 etki (hash geçer); açık = hedef gain + limiter ön koşulu `⚠️ VR (hedef -14 LUFS önerü)`.

**TC-PLR-142 — Crossfade↔EQ state geçişi (şartlı)**
- Gereksinim: NE-PLR-142 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): EQ korunur + ramp; iki oturum aynı preset.

**TC-PLR-143 — Cihaza bağlı EQ hatırlama (şartlı)**
- Gereksinim: NE-PLR-143 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Tercihler ayrışır; teyit zorunluluğu `[ÖNERİ]`.

**TC-PLR-144 — Preset import/export (+dönüştürücü şartlı)**
- Gereksinim: NE-PLR-144 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Round-trip byte-eşit (kendi formatı); dönüştürücü yalnız kendi frekans setiyle.

**TC-PLR-145 — Flat preset hash yolu (§11.2 ön koşulu)**
- Gereksinim: NE-PLR-145 · Katman: Kapı (5.1) · · Otomasyon: -
- Beklenen (özet): 12/12 byte-eşit; Flat=all-zero kanıtı.

**TC-PLR-146 — Preset yedeği/alma (atomik write)**
- Gereksinim: NE-PLR-146 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Son tutarlı preset geri gelir; bozuk dosya → yedekten/fabrika + uyarı (crash yok).

**TC-PLR-147 — 10/15/20/31 grid üretim doğruluğu**
- Gereksinim: NE-PLR-147 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): 31 tam ISO; 10 ISO alt küme; 15/20 teyitli listeler `⚠️ VR` tanımlanana kadar kilitli/gizli.

**TC-PLR-148 — Grid ↔ EqChain band maskesi hizası**
- Gereksinim: NE-PLR-148 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Mask boyutu = band sayısı; fazlalık/banklar pasif (taşıma sıfır — TC-ENG-051/066 ile ortak).

**TC-PLR-149 — Durum makinesi kontratı (otomatik)**
- Gereksinim: NE-PLR-149 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçerli geçişler kabul + doğru event; geçersizler reddedilir (durum değişmez). ADR-004/ADR-018/ADR-019 §1.4 uyumu.

**TC-PLR-150 — Konum/uzunluk eventleri (E3/E4/E7)**
- Gereksinim: NE-PLR-150 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Event akıyor; radyoda position akar, duration gelmez (0/yok); seek radyoda reddedilir.

**TC-PLR-151 — ERROR durumunda tekrar-dene/geri-ok**
- Gereksinim: NE-PLR-151 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): İki buton/senaryo çalışır; geri otomatik değil; otomatik deneme 3'te durmuş (TC-PLR-106 ile hizalı).

**TC-PLR-152 — Geçersiz girdi hata durumu (form)**
- Gereksinim: NE-PLR-152 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): Alan bazında hata metni; kırmızı durum (WCAG); gönderim engellenir/kabul edilmez; UI donmaz.

**TC-PLR-153 — Bozuk dosya → uyarı + atla**
- Gereksinim: NE-PLR-153 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): Uyarı (dosya adı ile) + otomatik/atla; kuyruk sonraki dosyaya geçer; uygulama çalışır.

**TC-PLR-154 — Desteklenmeyen format mesajı (3)**
- Gereksinim: NE-PLR-154 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): Kod 3 → "desteklenmeyen format" mesajı; engine açık başarısız değil (player uyumu); sonraki dosya devam.

**TC-PLR-155 — Engine bağlantısı koptu (ERROR-3 köprüsü)**
- Gereksinim: NE-PLR-155 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): Tek ERROR-3 destekleyici satır (NE-ENG-107); kuyruk durumu korunur; kurtarma akışı net.

**TC-PLR-156 — Dahili hata: log + geri kurtarma**
- Gereksinim: NE-PLR-156 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kullanıcıya jargon yok ("bir şeyler ters gitti" + deneyin); log'a detay; oturum yeniden kurulur; crash yok.

**TC-PLR-157 — İlk-iş akışı + ön tampon (LOADING)**
- Gereksinim: NE-PLR-157 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): ≤4 s ilk ses (TC-PLR-107); iptal butonu `⚠️ VR (ön tarayıcı notu)`; iki akış da net.

**TC-PLR-158 — 3 başarısız → teklik mesajı**
- Gereksinim: NE-PLR-158 · Katman: L2/UX · · Otomasyon: -
- Beklenen (özet): Mesaj tek ve net ("3 denemeden sonra durdu — yeniden deneyin"); spam yok (otomatik döngü durdu).

**TC-PLR-159 — Katalog dahili hata (bozuk şema)**
- Gereksinim: NE-PLR-159 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Düzeltme + yedek/geri alma `[ÖNERİ]`; katalog yeniden indekslenir; uygulama açılır.

**TC-PLR-160 — Fallback sembolü (UI görseli)**
- Gereksinim: NE-PLR-160 · Katman: UX · · Otomasyon: -
- Beklenen (özet): Belirsiz/varsayılan ikon (metin yerine) — uygulama "cihaz yok" bilgisini görsel sembolle verir `[ÖNERİ — ikon tasarımı açık]`.

**TC-PLR-161 — Klavye erişimi (transport + EQ)**
- Gereksinim: NE-PLR-161 · Katman: UX · · Otomasyon: -
- Beklenen (özet): Tüm işlevler erişilebilir; odak görünür (≥3px `[ÖNERİ]`); odak tuzağı yok; §02.3 I-11 karşılığı.

**TC-PLR-162 — Kurgu dışı açılış (cold start)**
- Gereksinim: NE-PLR-162 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Açılış ≤500 ms `⚠️ VR` (bu PRD aralığı; kardeş 300 ms engine çıkışı) — ölçüm raporda sıcak/soğuk ayrımı.

**TC-PLR-163 — 24 saat kararlılık + 1000 cycle**
- Gereksinim: NE-PLR-163 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): RSS artışı ≤0 `[ÖNERİ]`; xrun 0; UI yanıt verir (crash/donma 0).

**TC-PLR-164 — Sağlam kapanış → son durum geri**
- Gereksinim: NE-PLR-164 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): (a) Son durum geri (dosya/pozisyon/mod/EQ/cihaz); (b) veri tutarlı (sondan önceki güvenli `[ÖNERİ]`), crash artifact yok.

**TC-PLR-165 — UI↔engine state kayması (tekrar senkron)**
- Gereksinim: NE-PLR-165 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0 kayma; invariant ihlali = FAIL (yanlış gösterim asla kabul değil).

**TC-PLR-166 — OWASP tehdit tablosu gözden geçirme**
- Gereksinim: NE-PLR-166 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Tablo güncel + imzalı; eksik = release yok.

**TC-PLR-167 — SSRF özel-IP reddi**
- Gereksinim: NE-PLR-167 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Hepsi RED; DNS çözümü sonra tekrar kontrol (bağlanmadan önce) — post-connect IP kontrolü dahil.

**TC-PLR-168 — Zincirleme redirect ≤5 (güvenlik)**
- Gereksinim: NE-PLR-168 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): 6. hop RED; 3. hoptaki private IP RED (her hop validator); zincir sayısı sayacı sıfırlanmaz.

**TC-PLR-169 — TLS sertifika reddi yolları**
- Gereksinim: NE-PLR-169 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Dördü de RED; kör kabul bayrağı yok (code grep); http yalnız onay akışıyla.

**TC-PLR-170 — HTML-escape (tüm render yolları)**
- Gereksinim: NE-PLR-170 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Düz metin görünür; ham render yolu 0 (statik).

**TC-PLR-171 — 256 bayt cap + UTF-8 doğrulama**
- Gereksinim: NE-PLR-171 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): ≤256 kırpma/temizleme; geçersiz UTF-8 → replacement (çökme yok); 1 MB cap RED.

**TC-PLR-172 — XXE reddi (ICY v2 XML)**
- Gereksinim: NE-PLR-172 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): DTD çözümleme yok; dosya erişimi tetiklenmez; entity ham metin kalır/reddedilir.

**TC-PLR-173 — HLS kapıları (50 MB / 1000 entry / derinlik 1)**
- Gereksinim: NE-PLR-173 · Katman: L1/Kapı · · Otomasyon: -
- Beklenen (özet): Üçü de RED; sınır değerler dahil (50 MB OK / 51 MB RED; 1000 OK / 1001 RED; derinlik 1 OK / 2 RED).

**TC-PLR-174 — Protocol whitelist bypass denemeleri**
- Gereksinim: NE-PLR-174 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Hepsi RED; parser bypass yok; http/https dışında 0 geçiş.

**TC-PLR-175 — Preset/config parser dayanıklılığı**
- Gereksinim: NE-PLR-175 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Cap/kırpma; depth cap `[ÖNERİ — 64]`; eval/serileştirme yasağı (statik); crash yok.

**TC-PLR-176 — SQL injection + Türkçe adlar**
- Gereksinim: NE-PLR-176 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Payload'lar satır getirmez (parametreli); `O'Brien`/Türkçe sonuç verir; `SELECT *` yok (statik).

**TC-PLR-177 — Path traversal (tarama kökü)**
- Gereksinim: NE-PLR-177 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Kök dışına indeksleme RED; NormalPath çözümleme; çalma davranışı ayrı politika `[ÖNERİ]`.

**TC-PLR-178 — FFI clamp (player birleşimi)**
- Gereksinim: NE-PLR-178 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): `INVALID_ARG`/clamp; UB yok (ASan); kardeşi TC-ENG-157 ile aynı vektör seti.

**TC-PLR-179 — Tek instance + IPC kimlik reddi**
- Gereksinim: NE-PLR-179 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İkinci = mevcut pencereye odak; farklı kullanıcı RED; bilinmeyen mesaj drop (uzantı yok).

**TC-PLR-180 — Supply chain taraması**
- Gereksinim: NE-PLR-180 · Katman: güvenlik · · Otomasyon: -
- Beklenen (özet): Kritik CVE = release bloke; lock repo'da; FFmpeg vendor `⚠️ VR (§14.5)`.

**TC-PLR-181 — RAM ≤400 MB ölçümü**
- Gereksinim: NE-PLR-181 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): ≤400 MB (ses); video ON satırı §06.5 ayrı `⚠️ VR`; rapor release'de.

**TC-PLR-182 — CPU bütçesi (EQ payı)**
- Gereksinim: NE-PLR-182 · Katman: bench · · Otomasyon: -
- Beklenen (özet): EQ payı ≤%5; decode payı `⚠️ VR` raporda ayrı satır.

**TC-PLR-183 — Latency <20 ms (player ölçümü)**
- Gereksinim: NE-PLR-183 · Katman: Kapı (5.10) · · Otomasyon: -
- Beklenen (özet): <20 ms; L1-L4 `⚠️ VR` tanımlanana kadar en küçük desteklenen blok.

**TC-PLR-184 — Kapı 5.1 (player koşulları + guard)**
- Gereksinim: NE-PLR-184 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 12/12 + guard doğru; sahte sonuç yok.

**TC-PLR-185 — Kapı 5.10 rapor arşivi**
- Gereksinim: NE-PLR-185 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Rapor yok/eseşik üstü = release adayı değil.

**TC-PLR-186 — Kapı 5.11 (A/V sync) — koşul kontrolü**
- Gereksinim: NE-PLR-186 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): (a) Koşul dışı (sahte FAIL yok); (b) P95 ≤20 ms raporu.

**TC-PLR-187 — Kapı 5.12 (gapless) — tam vektör**
- Gereksinim: NE-PLR-187 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Hepsi yeşil; ±5 ms ölçüm.

**TC-PLR-188 — Kapı 5.13 (fuzz) — sayım raporu**
- Gereksinim: NE-PLR-188 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): ≥1000/format · ≥8000 toplam · 0 crash · ≤10 dk.

**TC-PLR-189 — UI spam + uzun süre stres**
- Gereksinim: NE-PLR-189 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): xrun 0 · UI crash 0 · yanıt gecikmesi `[ÖNERİ — <100 ms]`.

**TC-PLR-190 — Leak (1000 cycle + tarama)**
- Gereksinim: NE-PLR-190 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0; sanitizer temiz.

**TC-PLR-191 — TSan (UI + tuner + control spam)**
- Gereksinim: NE-PLR-191 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0 data race.

**TC-PLR-192 — Radyo stres Ş1-Ş7 raporu**
- Gereksinim: NE-PLR-192 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 7/7 raporlanmış; her biri ayrı onay satırı.

**TC-PLR-193 — UI a11y checklist**
- Gereksinim: NE-PLR-193 · Katman: UX · · Otomasyon: -
- Beklenen (özet): Checklist dolu; açık madde = release notu.

**TC-PLR-194 — Yerelleştirme testi (şartlı)**
- Gereksinim: NE-PLR-194 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Karar yoksa test KOŞUL DIŞI (kapsam dışı etiketi korunur).

**TC-PLR-195 — Metrik regresyon etiketi (%10)**
- Gereksinim: NE-PLR-195 · Katman: proses · · Otomasyon: -
- Beklenen (özet): PR etiketi/warn basar; etiket yoksa FAIL.

**TC-PLR-196 — Release test raporu şablonu**
- Gereksinim: NE-PLR-196 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Boş satır yok; release PR'ında eki.

**TC-PLR-197 — Determinism (2× hash)**
- Gereksinim: NE-PLR-197 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Eşitlik; fark = atomik/non-atomik hatası.

**TC-PLR-198 — ABI sembol denetimi (player)**
- Gereksinim: NE-PLR-198 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Player yalnız `neva_*` import eder; iç sembol yok.

**TC-PLR-199 — Event zinciri kontratı (E1-E10)**
- Gereksinim: NE-PLR-199 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): §05.2 tablo birebir; eksik/yanlış sıra = FAIL.

**TC-PLR-200 — Flaky test yasağı (retry=0)**
- Gereksinim: NE-PLR-200 · Katman: proses · · Otomasyon: -
- Beklenen (özet): retry=0; kararsız test = hata sayılıp düzeltilir.

**TC-PLR-201 — ABI minor genişleme (E1-E10)**
- Gereksinim: NE-PLR-201 · Katman: build · · Otomasyon: -
- Beklenen (özet): 9 fonksiyon + 5 kod değişmez; ekleme minor; kırılma = majör.

**TC-PLR-202 — Python ctypes smoke**
- Gereksinim: NE-PLR-202 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): C99 ABI'dan bağlanır; 0 hata.

**TC-PLR-203 — "25 dil" tanımı kararı**
- Gereksinim: NE-PLR-203 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Karar verilmeden ölçüm "25" iddiası yapılmaz (UNKNOWN korunur); daraltma `[ÖNERİ]` kayda geçer.

**TC-PLR-204 — neva-mcp (şartlı)**
- Gereksinim: NE-PLR-204 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): C ABI üzerinden; yetki modeli §18.5 kararına bağlı; karar yok test yok.

**TC-PLR-205 — neva-remote (şartlı)**
- Gereksinim: NE-PLR-205 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Varsayılan localhost; auth reddi doğru; core'da ağ yok (statik).

**TC-PLR-206 — Web UI (şartlı U4)**
- Gereksinim: NE-PLR-206 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Şifresiz erişim varsayılan değil `[ÖNERİ]`; karar yok test yok.

**TC-PLR-207 — Katalog dışa aktarma (CSV/M3U)**
- Gereksinim: NE-PLR-207 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): CSV/M3U geçerli; yol yazımı kullanıcı onaylı (gizlilik notu).

**TC-PLR-208 — Yerel M3U içe aktarma**
- Gereksinim: NE-PLR-208 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Cap 1000; http satırı validator'dan; file satırı kök sınırlı; zararlı satır RED.

**TC-PLR-209 — Hotkey kaydı/geri alma (şartlı)**
- Gereksinim: NE-PLR-209 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Çakışma uyarısı; geri alma temiz (OS kaydını siler).

**TC-PLR-210 — Bildirim sanitizasyonu (şartlı)**
- Gereksinim: NE-PLR-210 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Düz metin; varsayılan kapalı `[ÖNERİ]`.

**TC-PLR-211 — Plugin loader yokluğu (statik)**
- Gereksinim: NE-PLR-211 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 isabet (OS DLL yüklemeleri hariç gerekçeli); aksi FAIL.

**TC-PLR-212 — Sürücü kurulumu yok (first-run)**
- Gereksinim: NE-PLR-212 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Cihaz listesi dolu (mevcut cihazlar); ASIO satırı yok/şartlı; kurulumu otomatik yapmaz; yönlendirme mesajı.

**TC-PLR-213 — Gizli ağ çağrısı yok**
- Gereksinim: NE-PLR-213 · Katman: güvenlik · · Otomasyon: -
- Beklenen (özet): Sadece kullanıcı tetikli radyo/keşif; telemetri/sürüm/sahte analytics 0.

**TC-PLR-214 — Log hijyeni**
- Gereksinim: NE-PLR-214 · Katman: güvenlik · · Otomasyon: -
- Beklenen (özet): 0 sızıntı; stream URL query maskelenmiş.

**TC-PLR-215 — Dış API envanteri denetimi**
- Gereksinim: NE-PLR-215 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Her çağrı envanterde; envanterde olmayan = FAIL.


## §21 Ek L — Örnek ADR Taslakları (Player · karar kapıları §18.1/§18.7/§18.14/§18.2)

> Taslaklar §18.16 karar formu şablonuyla üretilir; `status: proposed` hiçbir
> gate'i kırmaz. Vault Steward `.ai/.decisions/draft/` altına taşıyıp onaya sunar.
> **Zorunlu ADR'ler:** T-P01 (UI toolkit, M4 engelleyici), T-P02 (mockup gate),
> T-P03 (ADR-0105 iadesi) — §18.1/§18.7/§18.14 KRİTİK maddeleri.

---

### §21.L.1 ADR Taslağı T-P01 — UI Toolkit Seçimi (§18.1 · KRİTİK · M4 engelleyici)

```markdown
## ADR-TP01 - Neva Player UI toolkit seçimi
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: §18.1 (KRİTİK) · M4-M6 fazları · ADR-0105 §3.3-3.6 (surrogate `⚠️ VR`) ·
  M4-UI engelleyicisi. Player PRD'de UI toolkit bilinmiyor (UNKNOWN); M4 başlamadan
  bu karar alınmalıdır.
- Karar: <(a)/(b)/(c)/(d) seçilecek>
- Alternatifler:
  (a) Qt 6 (LGPL-3.0 / ticari): olgun · cross-platform · A/V senkron widget'ları ·
      ama LGPL dinamik-link şartı (statik link = ticari lisans) + paket boyutu
      (~80-120 MB) + G3-5 lisans raporu ek yükü.
  (b) Avalonia 11 (MIT): C#/.NET · cross-platform · modern XAML · .NET runtime
      bağımlılığı (~30-70 MB) · NE-PLR UI testi (.NET) ekosistemi güçlü.
  (c) Native WinUI3 + GTK4 (platform başına): en küçük paket + en iyi his;
      ama iki ayrı UI kod tabanı (bakım ×2) · ADR-019 per-OS yaklaşımıyla uyumlu.
  (d) Tk/Flutter-Desktop/SDL: ADR-0105 §3.6 "SDL fallback" iddiası `⚠️ VR` —
      SDL oyun/medya odaklı, widget eksik.
- Sonuçlar: (a) lisans + paket boyutu pahalı; (c) bakım ×2; (b) .NET bağımlılığı;
  [ÖNERİ — (a) Qt 6 LGPL-dinamik veya (c) WinUI3+GTK4 ADR-019 per-OS ile hizalı].
- İzlenebilirlik: NE-PLR-166 (UI toolkit kararı) → M4-UI → §18.1
- Vault: `.ai/.decisions/draft/ADR-TP01-ui-toolkit.md` + log.md satırı
```

**Teyit adımları:** (1) ADR-0105 §3.x dosyasını Vault Steward'dan iste (T-P03);
(2) her seçenek için lisans metnini vendor/ altına al; (3) prototip (1 ekran:
play/pause + EQ + playlist) 3 toolkit'te de dene → his + paket boyutu ölç.

---

### §21.L.2 ADR Taslağı T-P02 — Mockup Gate Uygulaması (§18.7 · KRİTİK)

```markdown
## ADR-TP02 - UI mockup gate'inin M4 öncesi bağlayıcılığı
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: §18.7 (KRİTİK) · AGENTS §13 (Mockup Before Frontend) ·
  `.ai/ui-design/screens/**` + `.ai/.png/**` (salt-okunur SSOT).
- Karar: M4-UI kodundan ÖNCE `.ai/ui-design/` altında ilgili ekran mockup'ları
  (Kalıp D `screen-spec-template`) üretilir ve `.ai/.png/**` görseli okunmadan
  CSS/HTML/UI kodu YAZILMAZ (AGENTS §13 hard gate).
- Alternatifler:
  (a) Gate zorunlu (AGENTS §13'a tam uyum): mockup yoksa M4 başlamaz. [ÖNERİ]
  (b) Gate esnek (taslak mockup ile M4'e başla, sonra güncelle): risk —
      uygunsuz UI + AGENTS §13 ihlali.
- Sonuçlar: (a) ile M4 süresi uzar ama UI/UX tutarlılığı garantilenir;
  mockup şablonları `.ai/.templates/ui-design/` Kalıp A-D'den üretilir.
- İzlenebilirlik: NE-PLR-167 → M4-UI → §18.7 · AGENTS §13 · §16.5
- Vault: `.ai/.decisions/draft/ADR-TP02-mockup-gate.md` + log.md satırı
```

---

### §21.L.3 ADR Taslağı T-P03 — ADR-0105 İçerik İadesi (§18.14 · KRİTİK)

```markdown
## ADR-TP03 - ADR-0105 dosyasının vault'a iadesi (surrogate kapatma)
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: §18.14 (KRİTİK) · R-P02 (dosyasız ADR) · `.ai/.decisions/index.md:152`
  (tek satır surrogate; dosya diskte YOK). PRD'deki 40+ `⚠️ VR` etiketi bu
  dosyanın içeriğine bağlı.
- Karar: Vault Steward'dan `.ai/.decisions/accepted/ADR-0105-<slug>.md` dosyasının
  (veya ekstre) talep edilmesi; içerik gelene kadar PRD'deki tüm §2.10/§2.9/§5.x
  atıfları `⚠️ VR` olarak kalır ve release notunda görünür.
- Alternatifler:
  (a) Vault'tan dosya iste + geldiğinde tüm VR'ları kaldır. [ÖNERİ]
  (b) Dosya gelmezse PRD'deki VR'ları sil (iddia bağlayıcı olur): ZORUNSUZ —
      AGENTS §3 zero-hallucination ihlali.
  (c) ADR-0105'i superseded yap + yeni ADR ile bütçeleri yeniden yaz: ek yük.
- Sonuçlar: (a) ile kritik bütçeler (RAM/latency/A-V sync) doğrulanır;
  K-6 öncesi bu kapanmazsa release notunda "bağlayıcı olmayan hedef" olarak
  kalır (T-04 kardeş).
- İzlenebilirlik: NE-PLR-168 → §18.14 · R-P02 · kardeş Engine §18.8
- Vault: `.ai/.decisions/draft/ADR-TP03-adr0105-iadesi.md` + log.md satırı
```

---

### §21.L.4 ADR Taslağı T-P04 — Yerelleştirme (§18.2 · 25 dil)

```markdown
## ADR-TP04 - Neva Player yerelleştirme stratejisi
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: §18.2 · ADR-0105 §3.3 "25 dil" iddiası `⚠️ VR` (surrogate) ·
  UI toolkit T-P01'e bağımlı.
- Karar: <(a)/(b) seçilecek>
- Alternatifler:
  (a) Qt Linguist (.ts/.qm) veya Avalonia Resx: araç destekli · dil listesi
      25 dil TANIMLANANA KADAR yalnız TR/EN çıkarım. [ÖNERİ]
  (b) Manuel çeviri dosyaları: sürdürülemez.
- Sonuçlar: dil listesi UNKNOWN (§18.2) — karar alınsa bile "25 dil" iddiası
  bağlayıcı değildir; release notunda "TR/EN + altyapı" yazılır.
- İzlenebilirlik: NE-PLR-169 → §18.2 · kardeş Engine U-sayımı
- Vault: `.ai/.decisions/draft/ADR-TP04-yerellesme.md` + log.md satırı
```

---

### §21.L.5 Taslak → Kapı Eşleştirme (Player)

| Taslak | Açık madde | Zorunlu | Bağlı kapı | Bloke ettiği faz |
|---|---|---|---|---|
| T-P01 UI toolkit | §18.1 | Evet | M4-UI | M4 (tüm UI) |
| T-P02 Mockup gate | §18.7 | Evet | M4-UI · AGENTS §13 | M4 (kod yazımı) |
| T-P03 ADR-0105 iadesi | §18.14 | Evet (K-6 öncesi) | K-6 · R-P02 | PAKET (release notu) |
| T-P04 Yerelleştirme | §18.2 | Hayır (etiketli) | M6 | M6 (dil dosyaları) |
| T-P05 Windows kurulum | §18.12 | Hayır (etiketli) | PAKET-Windows | PAKET-Windows |

## §21 Ek M — CI & Paketleme Taslağı, Bağımlılık ve Teknik Borç (Player)

### §21.M.1 CI Pipeline (YAML taslağı `[ÖNERİ]` — sağlayıcı §18/U15)

```yaml
# .github/workflows/neva-player.yml  [ÖNERİ]
name: neva-player-ci
on: { push: { branches: [main] }, pull_request: }
jobs:
  statik-kapilar:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: RTL kaynak (test/analiz kodu)
        run: ! grep -rn '// RTL' --include='*.qml' --include='*.xaml' --include='*.cs' src/
      - name: XSS (UI metin) — innerHTML / dangerouslySetInnerHTML
        run: ! grep -rnE 'innerHTML|dangerouslySetInnerHTML' src/
      - name: Jargon yasağı (kullanıcı metinleri)
        run: ! grep -rnE 'nullptr|exception|segmentation|underrun' --include='*ui*' src/   # §12.1 kuralı
  derleme-matrix:
    needs: statik-kapilar
    strategy:
      matrix:
        os: [windows-latest, ubuntu-latest]   # macOS: ADR-019 A2 (test donanımı UNKNOWN)
    runs-on: ${{ matrix.os }}
    steps:
      - uses: actions/checkout@v4
      - run: cmake -S . -B build && cmake --build build --config Release
      - run: ctest --test-dir build --output-on-failure   # §11 kütüphanesi (M3)
  engine-hash-bridge:
    needs: derleme-matrix
    runs-on: windows-latest
    steps:
      - uses: actions/checkout@v4
      - name: K3 köprü — engine hash paketi + player düzgün akış
        run: ctest -R hash_bridge     # kardeş §21.F.2 raporu
  paket:
    needs: [engine-hash-bridge]
    runs-on: ${{ matrix.os }}
    strategy: { matrix: { os: [windows-latest, ubuntu-latest] } }
    steps:
      - uses: actions/checkout@v4
      - run: cpack --config build/CPackConfig.cmake    # §14.4 PAKET
      - name: SHA-256 + G3 ek (LICENSE/NOTICE)
        run: sha256sum *.exe *.AppImage 2>/dev/null; test -f LICENSE && test -f NOTICE
```

### §21.M.2 Paketleme Matrisi (ADR-019 §1.5 per-OS — `[ÖNERİ]` biçimler)

| OS | Biçim `[ÖNERİ]` | Engine bağlantısı | Kurulum notu | İmza |
|---|---|---|---|---|
| Windows 10/11 | MSIX (store) + NSIS (portable) `[ÖNERİ]` | neva_session.dll (dynamic) | §18.12 (WASAPI varsayılan) | Authenticode `⚠️ VR (imza anahtarı)` |
| Linux | AppImage + deb/rpm `[ÖNERİ]` | libneva_session.so (dynamic) | bağımlılık: libasound2/pipewire | — |
| macOS | .app + dmg (A2) `[ÖNERİ]` | .dylib (A2 fazı) | notarization gerekli `⚠️ VR` | Apple Developer ID `UNKNOWN` |

**Kural:** Statik link yasak (§14.4) · engine ayrı artifact ( kardeş §21.F.9) ·
her paket LICENSE + NOTICE + "bilinen sınırlar" (release notu) içerir.

### §21.M.3 Bağımlılık Envanteri (Player gözünden)

| Bağımlılık | Sürüm | Rol | Lisans | Durum |
|---|---|---|---|---|
| neva_session (engine) | 1.0.0 | çekirdek | (T-02 T-P02 kardeş) | Disk (9 fonksiyon) |
| FFmpeg | 9.0.2 `⚠️ VR` | extended decode | LGPL-2.1+ | YOK (T-03/kardeş) |
| libdsd | `UNKNOWN` | DSD/DoP | `UNKNOWN` (U3) | YOK |
| soxr | `UNKNOWN` | samplerate | LGPL-2.1 | YOK |
| mbedTLS | 9.13.0 `⚠️ VR` | TLS (radyo S1-S8) | Apache-2.0 | YOK |
| libcurl | 8.23.0 `⚠️ VR` | HTTP/radyo | MIT | YOK |
| UI toolkit | `UNKNOWN` (§18.1) | arayüz | Qt LGPL / Avalonia MIT / native | YOK (T-P01) |
| .NET runtime | `UNKNOWN` | Avalonia seçilirse | MIT | YOK (bağımlı) |
| Test framework | `UNKNOWN` | §11 test kütüphanesi | MIT | YOK (M3) |

### §21.M.4 Teknik Borç Envanteri (Player)

| ID | Borç | Kanıt | Sahip | Ödeme fazı |
|---|---|---|---|---|
| TD-P01 | UI yok (M4 başlamadı) | §02.3 sınır | NE-PLR-003 | M4 |
| TD-P02 | Mockup yok (§18.7 KRİTİK) | `.ai/ui-design` boş/eşleşmiyor `⚠️ VR` | NE-PLR-167 | M4 öncesi |
| TD-P03 | ADR-0105 dosyasız (40+ VR) | index.md:152 surrogate | NE-PLR-168 | K-6 öncesi |
| TD-P04 | Katalog verisi yok (M5) | §09.2 | NE-PLR-053 | M5 |
| TD-P05 | Test kütüphanesi yok (M3) | §11 | NE-PLR-162 | M3 |
| TD-P06 | Radyo UI'sı yok (P4) | §10.1 | NE-PLR-101 | P4 |
| TD-P07 | Preset formatı kararsız (§18.8) | ADR-0105 §2.8 surrogate | NE-PLR-088 | M2 |
| TD-P08 | Yerelleştirme altyapısı yok | §18.2 | NE-PLR-169 | M6 |
| TD-P09 | Uzak yüzey (web/MCP) kapsamı belirsiz | §18.5 · U4/U5 | NE-PLR-170 | P4+ |
| TD-P10 | Windows kurulum şekli belirsiz | §18.12 | NE-PLR-171 | PAKET-Windows |
| TD-P11 | Bildirim/tek-instance davranışı ADR-097 bekliyor | kardeş §06.2 | NE-PLR-068 | M4 |
| TD-P12 | Downmix kuralı (§18.3) | ADR-105 §2.4 surrogate | NE-PLR-060 | A2 |

### §21.M.5 Hızlı Başvuru Kartı (Player — 1 sayfa)

```
NEVA PLAYER — per-OS masaüstü oynatıcı (engine üstünde)
┌─────────────────────────────────────────────────────────────────┐
│ Durum: Taslak/PRD — kod YAZILMADI (onay kapıları K-1..K-6)       │
│ Kapsam: oynatıcı + EQ UI + katalog + radyo + çevrimdışı           │
│ Sınır: motor/engine PRD'ye ait · UI M4'ten önce mockup gate      │
├─────────────────────────────────────────────────────────────────┤
│ Engine sözleşmesi (kardeş §12.3):                                │
│  9 fonksiyon · 5 kod (0 OK · 1 INVALID_ARG · 2 NO_DEVICE ·      │
│  3 UNSUPPORTED · 4 INTERNAL) · E1-E10 event 1.1.0 (minor)        │
│  Hata sahipliği: engine asla kullanıcı metni üretmez             │
├─────────────────────────────────────────────────────────────────┤
│ KRİTİK açık (M4/PAKET engelleyici):                               │
│  §18.1 UI toolkit (T-P01) · §18.7 mockup gate (T-P02) ·          │
│  §18.14 ADR-0105 iadesi (T-P03)                                   │
├─────────────────────────────────────────────────────────────────┤
│ Bütçeler (⚠️ VR — kardeş PRD'den): RAM ≤400 MB · A/V P95 ≤20 ms · │
│  gapless ±5 ms · fuzz ≥1000/hedef · hash K3 12 kombinasyon       │
├─────────────────────────────────────────────────────────────────┤
│ Fazlar: M3 test kütüphanesi → M4 UI (mockup gate) → M5 katalog →  │
│  M6 fuzz+yerelleştirme → V1 (validation) → PAKET (per-OS)         │
├─────────────────────────────────────────────────────────────────┤
│ Paket: MSIX/NSIS · AppImage/deb · .app/dmg · statik link YASAK ·  │
│  LICENSE+NOTICE+release notu ZORUNLU (G3 kardeş)                   │
└─────────────────────────────────────────────────────────────────┘
```

## §21 Ek N — Uçtan Uca Senaryo Matrisi & SSS (Player)

### §21.N.1 Kurulum & İlk Açılış Senaryoları

**S-P01 — Windows kurulumu**
- Amaç: İlk kez kurulumdan ilk ses çıkışına kadar akış.
- Adımlar: MSIX/NSIS indir → kur → (imza kontrolü) → ilk açılış → varsayılan cihaz.
- Beklenen: İlk açılışta engine `create` + `api_version` handshake OK; WASAPI varsayılan (§18.12); lisans ekranı (varsa) G3.
- Kapı: NE-PLR-171 · NE-PLR-003 · kardeş TC-ENG-049.

**S-P02 — İlk açılış ( boş durum )**
- Adımlar: Uygulamayı aç → katalog boş → dosya aç.
- Beklenen: EMPTY durumu (§03.3) + CTA ("Dosya aç"); crash yok; RAM ≤ bütçe.
- Kapı: NE-PLR-014 · TC-PLR-011.

**S-P03 — Linux kurulumu**
- Adımlar: AppImage/deb kur → ALSA/PipeWire tespiti → aç.
- Beklenen: Zincir kaydı 5-8 (PipeWire/ALSA/Null) çalışır; ses yoksa Null'a düşer.
- Kapı: kardeş NE-ENG-025..027 · TC-PLR-025.

**S-P04 — Engine ABI uyuşmazlığı**
- Adımlar: Eski neva_session.dll ile yeni player (veya tersi).
- Beklenen: `api_version` handshake reddi → kullanıcı mesajı (teknik jargon yok) → indirme/yardım bağlantısı.
- Kapı: kardeş TC-ENG-049 · NE-PLR-156 (H6).

### §21.N.2 Oynatma & Katalog Senaryoları

**S-P05 — Dosya açma (Unicode yol)**
- Adımlar: `C:\Müzik\Türkçe\şarkı.flac` aç → play.
- Beklenen: char16_t yolu bozulmaz; FLAC (M2) oynar; desteklenmeyen format skip + toast (UYARI).
- Kapı: NE-PLR-154/157 · kardeş TC-ENG-005.

**S-P06 — Gapless kuyruk**
- Adımlar: 3 dosyalık kuyruk + gapless ON → kesintisiz geçiş.
- Beklenen: Geçiş boşluğu ±5 ms (AK-5 `⚠️ VR`); crossfade OFF'da bit-perfect korunur; ON'da rozet düşer.
- Kapı: NE-PLR-152 · kardeş kapı 5.12.

**S-P07 — Katalog taraması (M5)**
- Adımlar: Klasör seç → 10.000 dosya tara → listele → arama.
- Beklenen: Tarama arka planda (UI donmaz; INP bütçesi); arama ≤200 ms `⚠️ VR`; RAM ≤400 MB; durdurulabilir.
- Kapı: NE-PLR-053..055 · TC-PLR-055/056.

**S-P08 — Cihaz değiştirme**
- Adımlar: Çalarken cihaz listesinden DAC seç.
- Beklenen: `set_output_device` → kısa geçiş (≤200 ms `[ÖNERİ]`) → devam; sayaçlar sıfır; geri dönüş serbest.
- Kapı: NE-PLR-035 · kardeş TC-ENG-030.

### §21.N.3 EQ & Radyo Senaryoları

**S-P09 — 31-band EQ + Flat**
- Adımlar: EQ paneli → 31-band → 1 kHz +6 dB → Flat.
- Beklenen: Tıklama yok (rampa); Flat = all-zero (bit-perfect geri); 10-band modda 21 bant gray/disabled.
- Kapı: NE-PLR-061/064 · kardeş TC-ENG-051/056/064.

**S-P10 — Preset kaydet/yükle**
- Adımlar: "Rock" preset kaydet → 10-band moda geç → yükle (farklı grid_version).
- Beklenen: RET + Türkçe uyarı (jargon yok); grid_version uyuşmazlığı yüzünden sessiz maske sıfırlanma YOK.
- Kapı: NE-PLR-088..090 · kardeş §21.H.5.

**S-P11 — İnternet radyo (P4)**
- Adımlar: ICY URL ekle → PREBUFFER (2-4 s) → PLAYING → başlık değişimi.
- Beklenen: Metaveri ≤256 B; HTML escape; başarısızlıkta backoff 1s→30s; 3 fail → ERROR mesajı + yeniden dene.
- Kapı: NE-PLR-101..108 · kardeş NE-ENG-080..088 · TC-PLR-103/108.

**S-P12 — Radyo hata + bloom**
- Adımlar: Sahte sunucu → geçersiz UTF-8 / >256 B başlık / 3 başarısız bağlantı.
- Beklenen: Drop + sayaç; bloom uyarısı ("Çok sık başlık değişimi"); kullanıcıda jargon yok.
- Kapı: kardeş FM-E15 · TC-PLR-114/116.

### §21.N.4 Hata & Dayanıklılık Senaryoları

**S-P13 — Hot-plug (çıkarma)**
- Adımlar: Play sırasında DAC çıkar.
- Beklenen: Zincir → Null/ERROR; mesaj "Ses cihazı bulunamadı. Cihazı kontrol edin."; hang yok; yeniden dene aktif.
- Kapı: kardeş FM-E02 · NE-PLR-153.

**S-P14 — 30 dk dayanıklılık**
- Adımlar: Kardeş §21.P.4 stres + player UI açık (katalog + EQ).
- Beklenen: UI bellek sızıntısı yok (LeakCanary/.NET leak test `[ÖNERİ]`); xrun 0; sayaçlar 0.
- Kapı: G4 kardeş · TC-PLR-215 (grup).

**S-P15 — Fuzz (parser + ABI)**
- Adımlar: Kardeş §21.P.3 runner (wav/icy/abi/preset) + player katalog parser'ı.
- Beklenen: ≥1000 vaka/hedef; 0 crash/UB/OOB/UAF; CI ≤10 dk.
- Kapı: kapı 5.13 kardeş · TC-PLR-202..207.

**S-P16 — PAKET cut (per-OS)**
- Adımlar: Kardeş §21.F.9 + player paketleri (§21.M.2) → SHA-256 + imza + release notu.
- Beklenen: LICENSE + NOTICE + "bilinen sınırlar" (L1-L4 ⚠️ · UI toolkit · 15/20-band gizli · ADR-0105 VR).
- Kapı: K-6 · NE-PLR-149 · kardeş PAKET.

### §21.N.5 Senaryo ↔ Faz Matrisi (Player)

| Senaryo | M3 | M4-UI | M5 | M6 | V1 | PAKET-W/L/M |
|---|---|---|---|---|---|---|
| S-P01/03 kurulum | | | | | ● | ● |
| S-P02 boş durum | | ● | | | ● | |
| S-P04 ABI uyuşmazlık | ● | | | | ● | |
| S-P05 dosya/Unicode | ● | | | | ● | |
| S-P06 gapless | ● | | | | ● | |
| S-P07 katalog | | | ● | | ● | |
| S-P08 cihaz | | ● | | | ● | |
| S-P09/10 EQ+preset | | ● | | | ● | |
| S-P11/12 radyo | | | | | ● | |
| S-P13 hot-plug | ● | | | | ● | |
| S-P14 dayanıklılık | | | | ● | ● | |
| S-P15 fuzz | | | | ● | | |
| S-P16 paket | | | | | | ● |

### §21.N.6 SSS (Player)

**S-SSS-1 — UI neden henüz yok?** §15 fazları: M3 (test kütüphanesi) → M4 (UI). Kod yazımı K-1 onayı + §13 mockup gate'den sonra (AGENTS §13).
**S-SSS-2 — UI toolkit neden söylenmiyor?** §18.1 KRİTİK açık — T-P01 ADR taslağı onay bekliyor; uydurma isim yasak (zero-hallucination).
**S-SSS-3 — 15/20-band neden gizli?** Kardeş karar D-4: frekans listeleri `⚠️ VR`; `kSubset15/20={false,0,{}}` diskte kilitli.
**S-SSS-4 — Hata metni kimde?** Her zaman player'da (§12.1): engine yalnız kod döner; metin şablonu §13.1 tablosunda.
**S-SSS-5 — Neden per-OS paket?** ADR-019 §1.5 (frozen): her OS kendi backend + kurulum + imza zincirine sahip.
**S-SSS-6 — Katalog verisi nereden?** Dosya tarayıcı + tag okuma (M5); bulut/katalog servisi YOK (S3 kapsam dışı).
**S-SSS-7 — Bit-perfect rozeti player'da görünür mü?** Evet — yalnız Exclusive + Flat + eşit hız; Shared'da rozet YOK (kardeş NE-ENG-045).
**S-SSS-8 — Radyo neden P4?** HTTP/TLS akış + backoff + başlık akışı ayrı faz (ADR-106 kural 6); M2'de decode (FLAC/DSD) önce gelir.
**S-SSS-9 — Video oynatma var mı?** §18.4 açık: ADR-097 video onayı `⚠️ VR` — bu PRD'de YOK varsayıldı.
**S-SSS-10 — Çevrimdışı?** §18.10: yerel dosyalar + katalog çevrimdışı; radyo ağ gerektirir (öngörülü).
**S-SSS-11 — Tek-instance?** Kardeş §06.2: ADR-097/ADR-004 beklemede; ikinci lansman = birinci pencereye odak `[ÖNERİ]`.
**S-SSS-12 — Preset dosyası nerede?** `%APPDATA%/Neva/presets/*.json` `[ÖNERİ]` (format §18.8 açık: `{grid_version, band_count, gains[], preamp}`).
**S-SSS-13 — Neva-host ile fark?** Neva-host = engine köprüsü (U12, engine PRD §18.12 kardeş); player = ürün UI'ı.
**S-SSS-14 — MCP/uzak yüzey?** §18.5 açık (U4/U5): kapsam belirsiz; P4+ etiketli; yetki modeli kararı ayrı ADR.
**S-SSS-15 — Bu PRD ne zaman "bağlayıcı" olur?** K-6 kullanıcı onayı ile; o ana kadar taslak (AGENTS §1 hard gate).

### §21.N.7 Hata Ayıklama Tablosu (Player)

| Belirti | Kök neden | Çözüm |
|---|---|---|
| "Dosya açılamadı" | Kod 2/3 (NO_DEVICE/UNSUPPORTED) — §12.3 kardeş eşlemesi | Format matrisi · cihaz listesi |
| "Ses cihazı bulunamadı" | Zincir tükendi (Null dahil) | Cihaz tak → yeniden dene (S-P13) |
| Rozet yok (bit-perfect) | Shared mod · EQ Flat değil · hız dönüşümü | Exclusive + Flat + eşit hız (S-P09) |
| Preset yüklenmiyor | grid_version/band_count uyuşmazlığı | RET + uyarı — S10; sessiz sıfırlama YOK |
| Radyo bağlanmıyor | backoff / 3 fail → ERROR | Elle yeniden dene · URL düzenle (§13.1 H2) |
| Başlık boş | UTF-8 geçersiz · >256 B drop | Bloom sayacı · FM-E15 |
| UI donuyor | Tarama/senkron IO (M5) | Arka plan task'ı · durdurma butonu (S-P07) |
| RAM yükseliyor | Katalog cache sızıntısı | G4 stres · leak testi (S-P14) |
| ABI link hatası | STATIC/DLL karışıklığı | kardeş §21.D.3 · S04 semver |
| Türkçe yolu bozuk | char16_t daraltma | kardeş TC-ENG-005 (S-P05) |

## §21 Ek O — UI Ekran Envanteri & Üretim Günlüğü (Player)

### §21.O.1 Planlanan Ekranlar (M4 envanteri — taslak `[ÖNERİ]`)

> Bu envanter **planlama** içindir; her ekran §13 mockup gate'den geçmeden kodlanamaz
> (AGENTS §13 · T-P02). Bileşen listeleri bileşen-shelf (§08.2) ile hizalanır.

**E-01 Ana Oynatıcı**
- Bileşenler: cover/art `[VARSAYIM — tag yoksa placeholder]` · başlık/şarkıcı · seek bar · play/pause/prev/next · süre · ses seviyesi · bit-perfect rozeti (şartlı) · cihaz seçici (kısayol).
- Durum matrisi: `DEFAULT HOVER ACTIVE FOCUSED DISABLED ERROR LOADING EMPTY SELECTED` — tümü bileşen-shelf'te (§08.2).
- WCAG: play/pause 24×24 px min · focus outline 3 px · kontrast AA (§08.3).
- Kısayollar: Space · ←/→ 5 s · ↑/↑%5 · F11 tam ekran · Ctrl+O.

**E-02 EQ Paneli**
- Bileşenler: 31-band slider (ISO grid etiketleri: 20/25/31.5/.../20k `[ÖNERİ — etiket listesi ADR-106 grid'inden türetilecek]`) · bant sayacı (10/31 toggle; 15/20 gizli) · bypass toggle · Flat butonu · preset seç/kaydet/sil · preamp `[ÖNERİ — E6]`.
- Kilit: 10-band modda 21 bant `disabled` + gray (TC-PLR-147).
- Erişilebilirlik: slider'lar keyboard (Home/End/±0.5 dB) · screen-reader etiketi "1 kilohertz, artı 6 desibel" (jargon yasak §12.1).

**E-03 Katalog (M5)**
- Bileşenler: klasör tarama (ekle/çıkar) · sanatçı/album/tür ağacı · tablo (virtualized) · arama kutusu · filtre · sıralama · durdur/tara.
- Performans: 10.000+ satır virtualization · arama debounce `[ÖNERİ — 150 ms]` · tarama arka plan task'ı.
- EMPTY/LOADING durumları CTA ile (§03.3).

**E-04 Çalma Listesi (Kuyruk)**
- Bileşenler: sürükle-bırak sıralama `[ÖNERİ]` · çoklu seçim · kuyruğa ekle/sil · gapless anahtarı · çapraz geçiş anahtarı + süre `[ÖNERİ — ADR-019/ADR-106'dan]`.

**E-05 İnternet Radyo (P4)**
- Bileşenler: istasyon listesi · URL ekleme formu (validate §06.2) · PREBUFFER göstergesi · başlık/URL alanı (HTML-escape temiz) · yeniden dene · backoff durumu (gizli telemetri).
- Durumlar: `IDLE → PREBUFFERING → PLAYING → PAUSED → STOPPED → ERROR` (§06.1).

**E-06 Ayarlar**
- Bölümüler: ses (cihaz · bit derinliği · hız eşleşmesi) · EQ · radyo · görünüm (tema `[ÖNERİ]`) · dil (T-P04) · gizlilik (telemetri opt-in §07) · hakkında (lisans ekranı G3).
- Kalıcılık: config (§06.3) — preset dosyalarından bağımsız.

**E-07 Cihaz Seçici**
- Bileşenler: liste (ad + mod rozeti Excl/Shared) · varsayılan · yenile · hot-plug olayları (DeviceNotifier kardeş §21.D.7).

**E-08 Lisans/Hakkında**
- Bileşenler: LICENSE + NOTICE görüntüleyici · bağımlılık listesi (kardeş §21.M.3) · sürüm + ABI sürümü (`api_version` 0x00010000).

### §21.O.2 Ekran ↔ Gereksinim ↔ Test Eşleştirme

| Ekran | Gereksinim grubu | Test grubu | Gate |
|---|---|---|---|
| E-01 Ana | NE-PLR-003..020 | TC-PLR-001..030 | M4-UI |
| E-02 EQ | NE-PLR-060..090 | TC-PLR-060..092 | M4-UI |
| E-03 Katalog | NE-PLR-053..059 | TC-PLR-053..059 | M5 |
| E-04 Kuyruk | NE-PLR-025..034 | TC-PLR-025..041 | M4-UI |
| E-05 Radyo | NE-PLR-100..116 | TC-PLR-101..116 | P4 |
| E-06 Ayarlar | NE-PLR-065..080 · 092..094 | TC-PLR-093..097 | M4-UI |
| E-07 Cihaz | NE-PLR-035..041 | TC-PLR-030..041 | M4-UI |
| E-08 Lisans | NE-PLR-149 | TC-PLR-208..210 | PAKET |

### §21.O.3 PRD Üretim Günlüğü (Player · denetim izi)

#### Okunan kaynaklar

| # | Kaynak | PRD yeri |
|---|---|---|
| 1 | `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md` | §06 · §07 · §10.1 |
| 2 | `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` | §04 · §06.2 · §16 |
| 3 | `.ai/.decisions/index.md` (ADR-0105 surrogate satır 152 · ADR-097/ADR-074 kayıtları) | §18.14 · §18.4 |
| 4 | `--/neva-engine/include/neva/neva_session.h` (tamamı — 9 fonksiyon/5 kod) | §05.2 · §12.3 kardeş |
| 5 | `--/neva-engine/` envanter (§21.D kardeş Ek D) | §02.4 bağımlılık |
| 6 | Kardeş PRD (`coremusic-neva-engine-prd.md`) bölümleri | §12.3 eşleme · §14 · §18 |
| 7 | `AGENTS.md` (§13 mockup gate · §16.1-16.5 dokunulmaz kurallar · §6.1 persona) | §13 · §16 · §20 |
| 8 | `.ai/.templates/ui-design/` Kalıp A-D (gate kuralı) | §13 (M4 ön koşulu) |
| 9 | `~/.config/opencode/AGENTS.md` (§5 standartlar · §7 output stili) | §12 · §20.5 |

#### Otomatik üretim kayıtları

| Ek | Yöntem | Doğrulama |
|---|---|---|
| §21 Ek A | §10 bloklarından indeks | 215/215 |
| §21 Ek B | §11 bloklarından indeks | 215/215 |
| §21 Ek C | req→TC matrisi (çaprazlama) | 215 satır, 0 unmatched |
| §21 Ek J | gereksinim künyesi (215 blok) | 0 eksik kabul kriteri |
| §21 Ek K | test künyesi (215 blok) | 215/215 kapsama |

#### Yapılan tutarsızlık düzeltmeleri (kardeş C-01 ile hizalı)

| # | Bulgu | Düzeltme | Kanıt |
|---|---|---|---|
| P-C01 | "8 fonksiyon" (plr-p09/p13/p18) | 9 fonksiyon | kardeş §21.D.3 |
| P-C02 | `neva_invalid_handle` (plr-p13 TC-201) | `NEVA_ERR_INTERNAL` (4) | enum: 4=INTERNAL |
| P-C03 | "invalid_handle → log" (plr-p15 H6) | "INTERNAL (kod 4) → log" | enum |
| P-C04 | "Engine `neva_error` (1)" (plr-p15 §13.1) | "Engine hata kodu (≠0 · kardeş §12.3)" | enum'da `neva_error` YOK |

#### İşaret taraması (§20.6 için — birleştirme sonrası sayım)

(Aynı motor sayım tablosu kardeş §21.Q.4 ile birlikte birleştirilmiş haliyle §20.6'da.)

### §21.O.4 Kapanış Kontrol Listesi (Player — K-6 öncesi)

- [ ] 215 gereksinim + 215 test çapraz okundu (Ek A/B/C otomatik)
- [ ] CP-01..CP-08 çelişki defteri işlendi (§18.15)
- [ ] KRİTİK açık: §18.1 UI toolkit (T-P01) · §18.7 mockup (T-P02) · §18.14 ADR-0105 (T-P03) — kapatılmadan M4/PAKET başlamaz
- [ ] Kardeş PRD (engine) ile §12.3 hata kodu eşlemesi birebir
- [ ] Ölçüm şablonları bağlandı (kardeş §21.F ↔ §11)
- [ ] Senaryo ↔ faz matrisi (§21.N.5) onaylı
- [ ] Kullanıcı onayı alındı (§20.4 — BEKLİYOR)

## §21 Ek P — Erişilebilirlik & UI Standartları (Player · M4 bağlayıcı kontrol listesi)

> Bu ek, global AGENTS §5 UI/UX kurallarının **masaüstü player'a uyarlanmış** bağlayıcı
> listesidir; M4 kod incelemesi bu listeyle yapılır. Web'e özgü maddeler (ITCSS/BEM/
> `--cm-*`) yalnız player web-arayüz variantı (§18.5 uzak yüzey) için geçerlidir.

### §21.P.1 WCAG 2.2 AA Zorunlulukları (her ekran — E-01..E-08)

| Madde | Kural | Denetim | Eşik |
|---|---|---|---|
| Kontrast | Metin/arkaplan ≥ 4.5:1; büyük metin (≥18.66 px bold / 24 px) ≥3:1 | kontrast denetleyici | AA |
| Odak (focus) | Görünür outline ≥3 px (token `--focus-w: 3px` `[ÖNERİ]`) | klavye turu | her ekranda |
| Klavye | Tüm eylemler klavyeyle erişilir; tab sırası görsel sıra ile | tab turu | %100 |
| Kısayollar | Space · ←/→ 5 s · ↑/↓ %5 · Ctrl+O · Ctrl+F (katalog) · F11 | shortcut matrisi | §21.O.1 |
| Hedef boyut | Tıklanabilir ≥24×24 px (masaüstü minimum; dokunmatik ≥44 px `[ÖNERİ]`) | UI ölçümü | her bileşen |
| Durum metni | Yükleniyor/hata durumları yalnız renkle değil metin/ikonla da | ekran okuyucu testi | §03.3 |
| Error identification | Hata mesajı ilgili alanla ilişkili; jargon yok (§12.1) | kod incelemesi | §13.1 |
| Undo | Yıkıcı eylemler (kuyruk sil · preset sil) geri alınabilir `[ÖNERİ]` | senaryo S-P04 | M4 |
| Renk bağımsızlığı | Rozet/uyarı yalnız renge dayanmaz (ikon/metin) | gözden geçirme | AA |
| Ölçek | UI %200 sistem ölçeğinde bozulmaz | DPI testi (100/150/200%) | M4 |
| Animasyon | `prefers-reduced-motion` saygılı (varsa animasyon) `[ÖNERİ]` | ayarlar | P2 |

### §21.P.2 Bileşen Durum Matrisi (her bileşen-shelf öğesi — bağlayıcı)

```
DEFAULT · HOVER · ACTIVE · FOCUSED · DISABLED · ERROR · LOADING · EMPTY · SELECTED
```
- Her durumun renk + outline + (gerekiyorsa) ikon karşılığı tanımlı olmalı.
- `DISABLED`: opacity YETERLİ DEĞİL — erişilebilir ad + neden (tooltip/metin).
- `ERROR`: kırmızı + ikon + kılavuz metni; alan bazında (§13.1 şablonları).
- `SELECTED`: kuyruk/katalog seçimi — outline + arkaplan farkı (kontrast AA).
- Eşik: shelf'teki her bileşen için 9/9 durum; eksik = M4 review FAIL.

### §21.P.3 Tipografi & Tema (masaüstü karşılığı)

| Karar | `[ÖNERİ]` değer | Not |
|---|---|---|
| Yazı ailesi | OS varsayılanı (Segoe UI/SF Pro/GNOME) | paket boyutu 0 · yerel his |
| Ölçek | 12/14/16/20/24 px sabit ölçek | DPI bağımsız; %200 test |
| Tema | Açık/koyu + OS takibi (`AppThemeBinding`-tarzı) | token'lar: `--bg · --fg · --accent · --error · --focus-w` |
| Kontrast token | açık tema ≥4.5:1 · koyu tema ≥4.5:1 | her tema için ölçüm |
| Rozet (bit-perfect) | Yeşil + metin "Bit-perfect" + tooltip (Shared'da yok) | NE-PLR bit-perfect |

### §21.P.4 Performans Bütçeleri (UI — INP/olay benzeri)

| Metrik | Bütçe | Ölçüm | Sahip |
|---|---|---|---|
| Olay → UI tepki (play/pause tık) | ≤50 ms P95 `[ÖNERİ]` | UI profiler | TC-PLR-013 |
| Katalog arama sonuç | ≤200 ms P95 (10k satır) `⚠️ VR` | stopwatch testi | TC-PLR-055 |
| Katalog tarama | UI donmaz; iptal edilebilir | senaryo S-P07 | TC-PLR-056 |
| Seek bar sürükleme | ≥30 fps `[ÖNERİ]` | manuel + profiler | TC-PLR-017 |
| RAM (toplam, UI dahil) | ≤400 MB (kardeş bütçe) | G4 stres | kardeş §21.F.6 |
| İlk açılış (cold start) | ≤2 s `[ÖNERİ]` | kronometre | TC-PLR-011 |
| Görsel (cover) yükleme | placeholder + lazy; blok yok | kod incelemesi | TC-PLR-058 |

### §21.P.5 Kullanıcı Metni & Yerelleştirme İskeleti

- **Şablonlar §13.1 tablosundan gelir**; kod içinde dağılmış string yasak.
- Yerelleştirme anahtarı biçimi `[ÖNERİ]`: `plr.<ekran>.<bileşen>.<durum>`.
- Örnekler: `plr.eq.load.mismatch.grid` → "Preset bu EQ ayarıyla kullanılamıyor (grid farkı)."
- Varsayılan dil: TR (proje sahibi) + EN; T-P04 ile 25 dil iddiası `⚠️ VR`.
- Tarih/sayı formatı OS locale; UTF-8 her yer (Unicode yol kardeş TC-ENG-005).

### §21.P.6 Gizlilik & Telemetri UI (§07 · S3)

- Telemetri **opt-in** (varsayılan KAPALI) + Ayarlar'da tek anahtar + açıklama metni.
- Çökme raporu gönderimi ayrı onay; kişisel veri yok (dosya yolu hash'lenir `[ÖNERİ]`).
- Radyo başlığı/metadata: yalnız istasyonun gönderdiği metin; UI'da escape'li (S-SSS-6 kardeş).

### §21.P.7 M4 Review Kapı Listesi (bu ek → kod incelemesi)

1. §21.P.1 WCAG satırları: 11/11 işaretli.
2. §21.P.2 durum matrisi: her bileşen 9/9.
3. §21.P.4 bütçeler: ölçüm raporu ekli (ölçülmeyen satır = NOT TESTED).
4. Mockup gate: `.ai/ui-design/**` ekranı + `.ai/.png/**` görseli okundu (T-P02).
5. Metin şablonları: §13.1 ile birebir; jargon taraması temiz (CI kapıları §21.M.1).
6. Kısayol matrisi: §21.O.1 ile birebir.
7. Tema: açık/koyu + %200 DPI ekran görüntüleri (release dosyasına).
8. Keyboard-only senaryo: E-01→E-08 turu tamam (ekran görüntüsü kanıtı).

## §21 Ek Q — Karar Günlüğü, Sürüm Notu & Bakım Rehberi (Player)

### §21.Q.1 Bu PRD Üretiminde Alınan Kararlar (P-D — denetim defteri)

| # | Karar | Gerekçe | Kanıt | Tarih |
|---|---|---|---|---|
| P-D01 | Test/gereksinim numaraları NE-PLR-NNN ↔ TC-PLR-NNN birebir (215/215) | izlenebilirlik; 0 unmatched | Ek A/B/C otomatik | 2026-10-23 |
| P-D02 | Hata kodu eşlemesi kardeş §12.3'e bağlandı (engine asla kullanıcı metni üretmez) | ADR-019/ADR-106 sınırı | kardeş §12.3 · §13.1 | 2026-10-23 |
| P-D03 | `neva_error`/`neva_invalid_handle` uydurma adları kaldırıldı (P-C02..P-C04) | sıfır halüsinasyon; enum 0-4 | `neva_session.h:31-37` | 2026-10-23 |
| P-D04 | "8→9 fonksiyon" düzeltmesi (plr-p09/p13/p18) | disk kanıtı 9 imza | kardeş §21.D.3 · C-01 | 2026-10-23 |
| P-D05 | 15/20-band gizli kalır (kSubset15/20 kilitli) | frekans listeleri `⚠️ VR` | `eq_grids.h:51-52` · TC-PLR-147 | 2026-10-23 |
| P-D06 | UI toolkit ismi yazılmadı (yalnız seçenekler + T-P01 taslağı) | §18.1 KRİTİK; uydurma yasak | §18.1 · T-P01 | 2026-10-23 |
| P-D07 | ADR-0105 atıfları `⚠️ VR` kaldı (dosya diskte YOK) | surrogate yalnız indeks satırı | index.md:152 · §18.14 | 2026-10-23 |
| P-D08 | Video oynatma bu PRD'de YOK varsayıldı (§18.4 açık) | ADR-097 video onayı `⚠️ VR` | §18.4 | 2026-10-23 |
| P-D09 | Paket biçimleri MSIX/NSIS/AppImage/deb `[ÖNERİ]` (bağlayıcı değil) | §18.12 açık | §21.M.2 | 2026-10-23 |
| P-D10 | Ekran envanteri E-01..E-08 taslak `[ÖNERİ]` — mockup gate öncesi | AGENTS §13 hard gate | §21.O.1 · T-P02 | 2026-10-23 |

### §21.Q.2 Sürüm Notu Şablonu (Player PAKET için)

```markdown
## neva-player vX.Y.Z (engine ABI x.y.z) — <tarih>
### Değişti · Eklendi · Kilitlendi
- (madde · NE-PLR-NNN · TC-PLR-NNN)
### Bilinen sınırlar (release notu ZORUNLU)
- 15/20-band EQ: gizli (teyit bekliyor)
- Bit-perfect rozeti: yalnız WASAPI Exclusive + Flat + eşit hız
- ADR-0105 bütçeleri: `⚠️ VR` (T-P03 iadesi bekleniyor)
- Yerelleştirme: TR/EN + altyapı (25 dil iddiası bağlayıcı değil — T-P04)
### Ölçülmüş olanlar (ekli rapor)
- UI bütçeleri (§21.P.4) · G4 stres · K3 hash köprüsü · fuzz
### Onaylar
- K-1..K-6 (§20.1)
```

### §21.Q.3 Yeni Özellik Karar Akışı (Player — adım adım rehber)

```
1) İstek gelir (ekran/eylem/veri)
2) §00.3 kapsam kontrolü: engine mi player mı? (sınır: motor=kardeş PRD)
3) §18 açık madde çakışması var mı? (KRİTİK ise DUR + ADR taslağı)
4) Gereksinim yazımı: NE-PLR-NNN (MUST/SHOULD · öncelik · kabul kriteri · ADR izi)
5) Test yazımı: TC-PLR-NNN (aynı N · §13.1 mesaj şablonu · otomasyon)
6) Mockup: ekran ise `.ai/ui-design/**` Kalıp D + `.ai/.png/**` gate (T-P02)
7) UI kodu: bileşen-shelf §08.2 · durum matrisi 9/9 · WCAG AA (§21.P.1-2)
8) Ölçüm: §21.P.4 bütçelerinden etkilenen satır = ölçüm raporu (NOT TESTED yasak)
9) Ek güncelleme: Ek A/B/C/J/K yeniden üretilir (elle değil — §21.O.3)
10) Vault Steward: log.md + PRD kök indeks + çelişki defteri (CP-*)
```

### §21.Q.4 PRD Bakım Talimatı (onay sonrası)

| Tetik | Eylem | Sahip |
|---|---|---|
| K-6 onayı | PRD Vault'a kopyalanır; kök `PRD.md` indeksine kayıt; `log.md` satırı | Vault Steward |
| ADR accepted | §18.x "kapatıldı" + §17 izlenebilirlik + §21.Q.1 karar defteri | Vault Steward |
| Yeni gereksinim | §10 + Ek A/B/C/J/K yeniden üretim + faz matrisi kontrolü | agent önerir, onay Vault |
| Ölçüm (M3/M4) | §21.P.4 tablosu + kardeş §21.F raporları güncellenir | test otomasyonu |
| Çelişki | §18.15 CP defterine ekle (silme yasak; "kapatıldı (kanıt)" etiketi) | herkes, onay Vault |
| Sürüm | §21.Q.2 şablonu doldurulur + kardeş engine sürümüyle hizalanır | release sahibi |

### §21.Q.5 Kapanış Sayfası (Player PRD)

| Özet | Değer |
|---|---|
| Gereksinim | 215 (NE-PLR-001..215 · §10 · Ek A) |
| Test | 215 (TC-PLR-001..215 · §11 · Ek B) |
| Kapsama | 215/215 (Ek C · 0 unmatched) |
| Güvenlik | T0-T27 + G1-G6 (§12) |
| Hata UX | H1-H7 durum makinesi + §13.1 mesaj şablonları |
| Lisans maddesi | P01..P19 (§14 · kardeş G3) |
| Açık madde | §18.1..§18.15 (3 KRİTİK: UI toolkit · mockup · ADR-0105) |
| Çelişki defteri | CP-01..CP-08 (§18.15) + P-C01..P-C04 (§21.O.3) |
| Senaryo | S-P01..S-P16 (§21.N) |
| Ekran envanteri | E-01..E-08 (§21.O.1 — taslak, mockup gate öncesi) |
| Teknik borç | TD-P01..TD-P12 (§21.M.4) |
| Onay kapısı | K-1..K-6 (§20.1) — K-6 ONAYLANDI (2026-10-23) |

**Son cümle:** Player PRD, motor sözleşmesinin (kardeş PRD) üzerine inşa edilen
**ürün sözleşmesidir**; M4-UI kodu yalnız T-P01+T-P02 kapandıktan sonra ve K-1
onayıyla başlar. Bu belge onaysız taslaktır (AGENTS §1 hard gate).

## §21 Ek R — Test Ortamı, Ölçüm Betikleri & Donanım Planı (Player)

### §21.R.1 Yazılım Ortamları (Player CI + lokal)

| Ortam | Sürüm | Amaç | Kapı | Durum |
|---|---|---|---|---|
| Windows 11 | 23H2+ `⚠️ VR` | MSIX/NSIS · WASAPI köprü · UI test | M4-UI · PAKET-Windows | `UNKNOWN` |
| Windows 10 | 22H2 `⚠️ VR` | geriye uyumluluk | PAKET-Windows | `UNKNOWN` |
| Ubuntu | 24.04 LTS `[ÖNERİ]` | AppImage/deb · PipeWire | PAKET-Linux | `UNKNOWN` |
| .NET SDK | `UNKNOWN` | Avalonia seçilirse (T-P01) | M4-UI | `UNKNOWN` |
| Qt SDK | 6.x `⚠️ VR` | Qt seçilirse (T-P01, LGPL dinamik) | M4-UI | `UNKNOWN` |
| Ekran okuyucu | NVDA (Windows) `[ÖNERİ]` | WCAG denetimi §21.P.1 | M4 review | `UNKNOWN` |
| DPI ortamı | 100/150/200% | §21.P.1 ölçek | M4 review | `UNKNOWN` |
| macOS/Xcode | `UNKNOWN` | PAKET-macOS (A2) | PAKET-macOS | `UNKNOWN` |

### §21.R.2 UI Test Betikleri (taslak `[ÖNERI]`)

```powershell
# scripts/ui-budget-check.ps1  — §21.P.4 bütçeleri
# (a) ilk acilis: process baslangic -> ilk pencere render
$sw = [Diagnostics.Stopwatch]::StartNew()
Start-Process .\build\NevaPlayer.exe
while(-not (Get-Process NevaPlayer -ErrorAction SilentlyContinue).MainWindowHandle){ Start-Sleep -Milliseconds 20 }
$sw.Stop()
Write-Host ("cold_start_ms={0} (butce: 2000ms [ONERI])" -f $sw.ElapsedMilliseconds)
# (b) RAM tepe (G4 kardesi):
$p = Get-Process NevaPlayer
Write-Host ("peak_ws_mb={0} (butce: 400MB - kardes butce ⚠️ VR)" -f ($p.WorkingSet64/1MB))
# (c) olay tepki (play tık -> UI guncelleme): UI profiler cikti (framework bagimli, T-P01)
```

```bash
#!/usr/bin/env bash
# scripts/katalog-bench.sh  — TC-PLR-055/056 (10k satir arama)
./neva_player --bench-catalog ./testdata/tags-10k.csv --query "beatles" --json
# Beklenen: search_ms ≤200 (P95) [⚠️ VR esik]; UI donma: iptal edilebilir satir
```

### §21.R.3 Hash Köprüsü Betiği (K3 — player kuyruk/çıkış)

```powershell
# scripts/hash_bridge_check.ps1  — TC-PLR-202 (kardeş kapı 5.1 köprüsü)
# 1) engine paket referans hash'i (kardeş §21.P.2 ciktilari)
$ref = Import-Csv .\testdata\bitperfect-ref.csv   # rate,depth,sha256
# 2) player kuyruk modu: ayni 12 dosya -> play -> loopback capture (exclusive)
foreach($row in $ref){
  & .\NevaPlayer.exe --headless --queue .\testdata\ref_$($row.rate)_$($row.depth).wav `
     --capture out.raw --eq flat --device exclusive
  $h = (Get-FileHash out.raw -Algorithm SHA256).Hash
  $pass = $h -eq $row.sha256
  "rate=$($row.rate) depth=$($row.depth) bridge=$pass"
  if(-not $pass){ exit 1 }
}
```

### §21.R.4 Fuzz & Dayanıklılık (M6)

```bash
#!/usr/bin/env bash
# scripts/player-fuzz.sh — §21.N.4 S-P15 (katalog parser + preset JSON + ICY metaveri)
RUNS=${RUNS:-1000}
for h in catalog preset icy_meta; do
  ./build-fuzz/fuzz_$h -runs=$RUNS -max_len=65536 ./corpus/$h || exit 1
done
# CI: timeout 10 dk (kapi 5.13 kardesi); Asan/UBSan/TSan derlemeli
```

```powershell
# scripts/player-stress-30min.ps1 — S-P14 (UI + engine)
$deadline=(Get-Date).AddMinutes(30)
while((Get-Date) -lt $deadline){
  & .\scripts\scenario-runner.ps1 -Scene (Get-Random -Set seed 42)  # play/pause·skip·eq·device
  $p=Get-Process NevaPlayer; if($p.WorkingSet64 -gt 400MB){ Write-Warning "butce asimi" }
}
# CIkis: xrun=0 · leak=0 (LSan/.NET leak test [ONERI]) · sayac sifirlar
```

### §21.R.5 Donanım & Ortam Karar Adımları (Player U-kapanış)

1. UI toolkit kararı (T-P01) → SDK ortamı (§21.R.1 satırları dolar).
2. Ekran okuyucu + DPI denetimi için fiziksel/uzak masaüstü (M4 review kanıtı).
3. Loopback/DAC: kardeş §21.R.2 envanteri ortak kullanılır (tek satın alma listesi).
4. CI runner: self-hosted Windows makinesi (imza + MSIX + WASAPI köprü) `[ÖNERİ]`.
5. Sonuç: §18.1/§18.7/§18.14 KRİTİK kapanışları + bu ekteki `UNKNOWN` hücreleri
   → M4-UI gate açılır → release notu "ölçülmüş" diline geçer.

**Kural (AGENTS §3):** Ölçülmeyen satır NOT TESTED kalır; "başarılı" yazmak yasak.

## §21 Ek S — Kardeş PRD ile Ortak Sözleşme Kartı (Player ↔ Engine)

> Bu ek, iki PRD arasındaki **sözleşme yüzeyini** tek yerde toplar; iki dosya
> birlikte okunmasa bile bu ek yeterli başlangıçtır.

### §21.S.1 Tam Hata Kodu Eşlemesi (§12.3 kardeş tablosunun player görünümü)

| Engine kodu (enum) | Kod | Engine davranışı | Player durum | Mesaj şablonu (§13.1) | Eylem | Test |
|---|---|---|---|---|---|---|
| `NEVA_OK` | 0 | ilerleme | PLAYING/PAUSED korunur | — | — | TC-PLR-001.. |
| `NEVA_ERR_INVALID_ARG` | 1 | girdi reddi (clamp) | durum DEĞİŞMEZ (form hatası) | "Geçerli bir değer girin: {alan}" | form düzeltmesi | TC-PLR-152 |
| `NEVA_ERR_NO_DEVICE` | 2 | cihaz yok/kayıp | ERROR + E10 (1.1.0 sonrası) | "Ses cihazı bulunamadı. Cihazı kontrol edin." | cihaz listesini aç | TC-PLR-153 |
| `NEVA_ERR_UNSUPPORTED_FORMAT` | 3 | decode reddi | UYARI + skip (durum korunur) | "Bu format desteklenmiyor: {ad}" | sonraki dosya | TC-PLR-154/157 |
| `NEVA_ERR_INTERNAL` | 4 | iç hata | ERROR + kurtarma denemesi | "Beklenmeyen bir sorun oluştu. Otomatik toparlanıyor…" | yeniden başlat | TC-PLR-156 |

**Kanıt:** `neva_session.h:31-37` (5 kod) · kardeş §12.3 · §13.1 durum makinesi.
**Kural:** Kod adları uydurulmaz (`neva_error`/`invalid_handle` YOK — P-C02/P-C03);
player kod ekleyemez (engine 5 kodda sabit, NE-ENG-061/100).

### §21.S.2 E1-E10 Event Genişlemesi Sözleşmesi (engine 1.1.0 · minor)

| # | Event | Tetikleyici | Payload `[ÖNERİ]` | Player tepkisi |
|---|---|---|---|---|
| E1 | `playback_state` | play/pause/stop geçişi | eski+yeni durum | buton state · §03.3 |
| E2 | `position` | periyodik (≥250 ms `[ÖNERİ]`) | ms (u64) | seek bar |
| E3 | `duration` | dosya açılışında | ms (u64) · UNKNOWN=-1 | süre etiketi |
| E4 | `metadata` | dosya/radyo başlığı | başlık/şarkıcı/album (temiz) | E-01/E-05 alanları |
| E5 | `device_changed` | hot-plug / seçici | cihaz index + mod | E-07 listesi |
| E6 | `xrun` | underflow/clip | sayaç değeri | (gizli) telemetri |
| E7 | `error` | StreamError / durum hatası | kod (1-4) + context enum | §13.1 mesaj eşlemesi |
| E8 | `buffering` | radyo PREBUFFER | yüzde `[ÖNERİ]` | E-05 progress |
| E9 | `bit_perfect` | mod değişimi | true/false + neden enum | rozet görünürlüğü |
| E10 | `eq_changed` | bant/preset/bypass | band + dB + grid_version | E-02 senkron |

**Sözleşme kuralları:** (a) event'ler RT thread'den doğrudan UI'a gitmez —
event-queue (SpscRing kardeşi) + UI tarafında dispatch; (b) payload sabit boyutlu
( heap string YOK — fixed char[N] `[ÖNERİ — N=256 icy cap ile hizalı]` ); (c) yeni
event eklemek = minor (1.1.0 → 1.2.0); mevcut event payload'ını kırmak = major.
**Durum:** YOK (TD-P05 kardeş TD-05) — bu tablo 1.1.0 şartnamesidir.

### §21.S.3 K3 Hash Köprüsü Sözleşmesi (kapı 5.1 ↔ player kuyruk modu)

```
[kardeş §21.P.2]  engine bitperfect_check      → 12 satır: rate,depth,sha256
       ↓ (testdata/bitperfect-ref.csv release artifact'ı)
[plr-p40 §21.R.3] player --headless --queue    → loopback capture (exclusive)
       ↓
SHA-256(capture) == sha256(ref)  12/12  +  Flat=all-zero guard (kardeş inverted guard)
```
- Player köprüsü **yalnız Exclusive + Flat + eşit hız**'da çalıştırılır (rozet ön koşulları).
- Eşitsiz hızda: capture yerine "hash atlandı (dönüşüm var)" raporu — iddia düşer (NE-PLR-038).
- CI: `engine-hash-bridge` job'u (plr-p35 §21.M.1) kardeş `bit-perfect-hash` job'unu bekler.

### §21.S.4 ABI Uyum Kartı (1.0.0 → minor)

| Değişiklik | Sürüm etkisi | Örnek | Kapı |
|---|---|---|---|
| Yeni `neva_*` fonksiyonu (E5-E7) | minor | `set_eq_band_count` | TC-PLR-201 header diff |
| Yeni event (E1-E10 sonrası) | minor | E11 `[ÖNERİ]` | TC-PLR-201 |
| Mevcut imza değişikliği | **MAJOR (2.0.0)** | parametre ekleme/çıkarma | TC-ENG-142 · A0 |
| Error code eklenmesi | **YASAK** | "kod 5" | NE-ENG-100 · NE-PLR-160 |
| Handle semantiği değişimi | **MAJOR** | destroy sonrası farklı davranış | SE-02 |
| Header C++ tipleri sızması | **YASAK** | `std::` görünürse | statik kapı · kardeş §21.D.3 |

**Doğrulama:** Player CI `neva_session.dll`'i eski header ile link eder
(kardeş §21.D.8 semver raporu) — kırılma = majör artışı zorunlu.

### §21.S.5 Ortak Bütçe Kartı (tek satır özet)

```
RAM ≤400 MB · latency <20 ms (ASIO <10) · A/V sync P95 ≤20 ms · gapless ±5 ms (AK-5)
fuzz ≥1000/hedef · CI ≤10 dk · bit-perfect hash 12/12 (K3) · EQ grid: 31 ISO (10 teyitli)
radyo backoff 1s→30s (%20 jitter) · prebuffer 2-4 s · fade-in 20 ms · metaveri cap 256 B
(tümü ⚠️ VR = ADR-0105 surrogate + ADR-106; bağlayıcılık T-04/T-P03 kararıyla)
```

**Kapanış:** Bu ek kardeş §12.3/§21.F/§21.P ile birlikte tek "sözleşme paketi"dir;
taraflardan biri değişince (event ekleme · kod eşlemesi · bütçe) diğeri bu ekten
güncellenir ve çelişki defterine (C-*/CP-*) yazılır.

## §21 Ek T — Nihai Ölçüm Kaydı & Kapanış Özeti (Player)

> Bu ek birleştirme (concat) sonrasında gerçek dosyadan ölçülmüştür; sayımlar iki
> yöntemle alınmıştır (ham satır + boşluksuz satır).

### §21.T.1 Ölçüm Tablosu (birleştirme + doldurma sonrası)

| Ölçüm | Değer | Yöntem |
|---|---|---|
| Parça dosyası sayısı | 42 (`plr-p01..p42`) | dosya sayımı |
| Ham satır (toplam) | **8006** (§20.6 ile aynı ölçüm) | `(Get-Content f -Encoding UTF8).Count` |
| Boşluksuz satır | **6624** (§20.6 ile aynı ölçüm) | `Measure-Object -Line` |
| Bayt | **458307** (§20.6 ile aynı ölçüm, 2026-10-23 son ölçüm) | `(Get-Item f).Length` |
| Hedef | ≥8000 ham satır | kullanıcı şartı |
| Bölüm sayısı | §00..§21 (+ Ek A..T) | başlık taraması |
| Gereksinim / Test | 215 / 215 (0 unmatched) | Ek A/B/C otomatik |
| Güvenlik / Hata UX | T0-T27 + G1-G6 · H1-H7 | §12 · §13.1 |
| Açık madde (KRİTİK 3) | §18.1 UI toolkit · §18.7 mockup · §18.14 ADR-0105 | T-P01..T-P03 |
| Çelişki defteri | CP-01..CP-08 + P-C01..P-C04 | §18.15 · §21.O.3 |
| Senaryo / Ekran | S-P01..S-P16 · E-01..E-08 (taslak) | §21.N · §21.O.1 |
| Teknik borç | TD-P01..TD-P12 | §21.M.4 |

### §21.T.2 Nihai İşaret Sayımı (§20.6 girdisi — tarama yöntemi)

| İşaret | Sayım (bu dosya) | Anlam |
|---|---|---|
| `⚠️ VR` | **208** (§20.6 taraması) | doğrulanmamış amaç değer (ADR-0105/ADR-106 surrogate) |
| `[ÖNERİ]` | **334** (§20.6 taraması) | taslak/öneri — bağlayıcı değil |
| `UNKNOWN` | **43** (§20.6 taraması) | bilinmeyen (envanter/sürüm/teçhizat) |
| `[VARSAYIM]` | **4** (§20.6 taraması) | açık varsayım |
| `⚠️ LİSANS` | **8** (§20.6 taraması) | lisans karar bağımlı (L*/P* maddeleri) |

**Tarama yöntemi:** `Select-String -Pattern '<işaret>' -AllMatches | Measure-Object`
(satır bazlı; çoklu geçen satır 1 kez sayılır — §20.6 notu).

### §21.T.3 Kardeş PRD Hizalama Son Kontrolü

| Yüzey | Kardeş (engine) | Bu PRD (player) | Hizalı? |
|---|---|---|---|
| ABI fonksiyon sayısı | 9 (`neva_session.h:39-49`) | 9 (§05.2 · P-D04) | ✅ |
| Error kodu | 5 (0-4) | 5 (§12.3 · §21.S.1) | ✅ |
| Event genişlemesi | E1-E10 (1.1.0) | E1-E10 (§21.S.2) | ✅ |
| Bit-perfect köprüsü | §21.P.2 + §21.F.2 | §21.R.3 + §21.S.3 | ✅ |
| Zincir basamağı | 8 kayıt (Null dahil) | §04.2 8 kayıt | ✅ |
| 15/20-band gizli | D-4 (EqBandCount) | P-D05 (TC-PLR-147) | ✅ |
| Bütçeler | §21.F (⚠️ VR) | §21.S.5 kartı | ✅ |
| Onay kapıları | K-1..K-6 (K-6 ONAYLANDI 2026-10-23) | K-1..K-6 (K-6 ONAYLANDI 2026-10-23) | ✅ |

### §21.T.4 Kapanış Sayfası (Player — bu ek ile)

- **Bu belge taslaktır** — K-6 kullanıcı onayı bağlayıcılığı başlatır (AGENTS §1).
- **Kod yazılmadı** — bu oturumda yalnız `.md` üretildi; `git add/commit` YOK
  (AGENTS §16.5 — commit kararı orkestratöre/kullanıcıya ait).
- **KRİTİK 3 açık** M4/PAKET engelleyicidir: T-P01 (toolkit) · T-P02 (mockup) ·
  T-P03 (ADR-0105 iadesi). Bunlar kapatılmadan UI kodu ve release NOTU bağlayıcı olmaz.
- **Ölçülmeyen = NOT TESTED** — §21.P.4/§21.R bütçe satırları M3-M6'da doldurulur.
- **Bakım:** §21.Q.4 tablosu (Vault Steward sahipliğinde) — yeni gereksinim eklemek
  = §10 + Ek A/B/C/J/K yeniden üretim (elle değil).

**Son cümle:** Player PRD, motor sözleşmesinin (kardeş PRD) üzerine inşa edilen
**ürün sözleşmesidir**; M4-UI kodu yalnız T-P01+T-P02 kapandıktan ve K-1 onayı
alındıktan sonra başlar. Onaysız hiçbir satır bağlayıcı değildir.

### §21.T.5 PRD Sürüm Günlüğü (bu belgenin kendisi — revizyon izi)

| Rev | Tarih | Değişiklik | Kapı etkisi |
|---|---|---|---|
| v0.1 | 2026-10-23 | İskelet: §00-§21 bölüm planı + §01-§09 çekirdek | — (taslak) |
| v0.2 | 2026-10-23 | §10 (215 gereksinim) + §11 (215 test) + §12-§17 tamamlandı | — |
| v0.3 | 2026-10-23 | Otomatik ekler: Ek A/B/C/J/K (extraction script — §21.O.3) | — |
| v0.4 | 2026-10-23 | Düzeltmeler: P-C01..P-C04 (9 fonksiyon · gerçek enum adları) | §12.3 hizası |
| v0.5 | 2026-10-23 | Ek L (ADR taslakları T-P01..T-P05) + Ek M (CI/paketleme/borç/kart) | T-P01..T-P03 kapısı |
| v0.6 | 2026-10-23 | Ek N/O/P (senaryo·SSS · ekran envanteri · WCAG/ölçüm betikleri) | M4 review listesi |
| v0.7 | 2026-10-23 | Ek Q/R/S/T (karar günlüğü · test ortamı · kardeş sözleşme kartı · ölçüm) | K-6 kanıt paketi |
| v1.0 | 2026-10-23 | Kullanıcı onayı (§20.1 K-6) — yanıtı "DEVAM ET" ile alındı; Vault'a kayıt yapıldı | **bağlayıcı başlangıcı** |

**Güncelleme kuralı (§21.Q.4):** Bu tabloya satır yalnız Vault Steward onayıyla
eklenir; değişiklik gerekçesi + §21.Q.1 karar defteri (P-D*) ile birlikte yazılır.

### §21.T.6 Belge Kaydı (imza bloğu)

| Alan | Değer |
|---|---|
| Belge | coremusic-neva-player-prd.md (PRD · Neva Player) |
| Revizyon | v1.0-taslak (§21.T.5 günlüğü) |
| Tarih | 2026-10-23 |
| Üretim | agent oturumu — kaynak okuma §21.O.3, otomatik ekler §21.O.3 tablosu |
| Durum | **Onaylandı — K-6 (2026-10-23, "DEVAM ET")** (§20.1) |
| Bağlı belgeler | coremusic-neva-engine-prd.md (kardeş · §21.S sözleşme kartı) |
| Vault hedefi | `.ai/` altına Vault Steward ile (onay sonrası) |
| Değişiklik yetkisi | Vault Steward + proje sahibi (§21.Q.4) |
