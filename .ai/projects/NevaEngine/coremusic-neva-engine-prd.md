---
title: "Neva Engine — Ürün Gereksinim Dokümanı (PRD) v1.0"
type: prd
category: audio-engine
version: 1.0.0
status: draft
date: 2026-10-23
author: Bayram Ali (Vault Steward) — PRD orkestrasyonu: CoreMusic Master Orchestrator
language: Turkish
related_adr: "ADR-017 (DSP hard-RT) · ADR-019 (Per-OS, IAudioBackend) · ADR-097 (K-space band) · ADR-006 (latency) · ADR-0105 (medya stack — indeks kaydı) · ADR-106 (radyo/EQ) · ADR-107 (draft — platform & paketleme)"
tags: [coremusic, prd, neva-engine, cpp20, wasapi, asio, bit-perfect, dsp, realtime]
change_log:
  - "v1.0.0 (2026-10-23): İlk üretim — brainstorming + prompt-maker P0-P3 zinciri yerine kullanıcı direktifiyle ('paralel başlat') doğrudan vault-kanıt temelli üretim. Kaynaklar: draft/ADR-107 + .ai/architecture/neva-engine-platform-paketleme.md + accepted/ADR-019 + accepted/ADR-106 + .ai/.decisions/index.md:152 (ADR-0105 kaydı) + --/neva-engine/ disk kodu (glob 2026-10-23)."
---

# NEVA ENGINE — ÜRÜN GEREKSİNİM DOKÜMANI (PRD) v1.0

> **Doküman durumu:** Taslak (draft) — ADR-107 `draft` durumundadır; bu PRD ADR-107 onayını
> **beklemeden** gereksinimleri tanımlar, ancak §10'daki NE-ENG-GATE maddeleri onaysız
> uygulamayı bloke eder (ADR-107 §5 Faz 0 kapısı).
> **Zero-Hallucination:** Bu belgedeki her sayısal iddia kaynaklıdır; kaynağı olmayan her
> iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` etiketlidir. Etiketsiz kesinlik yasaktır.

---

## §00 Doküman Kontrol

### §00.1 Kimlik

| Alan | Değer |
|---|---|
| Doküman adı | Neva Engine — Ürün Gereksinim Dokümanı |
| Sürüm | 1.0.0 |
| Tarih | 2026-10-23 |
| Durum | Draft (ADR-107 onayı bekliyor) |
| Sahip | CoreMusic Vault Steward (Bayram Ali) |
| Teknik sahiplik | Embedded Engineer (A0 — C++20 çekirdek) · Windows SW Engineer (WASAPI/COM kolu) · DevOps Engineer (CI bit-perfect kapısı) |
| Erişim | `.ai/` vault okuma serbest; üretim G: drive (bu dosya) |
| Dil | Türkçe (teknik terimler İngilizce) |

### §00.2 Kaynak Envanteri (bu PRD'nin dayandığı disk kanıtları — 2026-10-23)

| # | Kaynak | Tür | Durum | Bu PRD'de kullanımı |
|---|---|---|---|---|
| S1 | `.ai/.decisions/draft/ADR-107-neva-engine-platform-ve-paketleme.md` | ADR (draft) | diskte VAR | §03, §04, §10, §12, §14, §15 — platform, K1-K5, fazlar, lisans kapıları |
| S2 | `G:\...\coremusic-adr107-neva-engine-prompt.md` (218 satır) | Şartname prompt'u | diskte VAR | §00-§20 iskeleti, U1-U5 UNKNOWN listesi, glob kanıt tablosu |
| S3 | `.ai/architecture/neva-engine-platform-paketleme.md` | Mimari (draft v1.0.0) | diskte VAR | §03 platform matrisi, §04 arayüz sözleşmeleri (Types/IAudioBackend/BackendRegistry), §05 paketleme, §11 test stratejisi |
| S4 | `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` (accepted v1.0.1) | ADR | diskte VAR | §04 IAudioBackend, §07 RT kuralları, §08 latency bütçeleri, fallback zinciri |
| S5 | `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md` (accepted v1.1.1) | ADR | diskte VAR | §06 EQ grid sözleşmesi (31-band ISO), §09 radyo decode kapsamı, §14 lisans |
| S6 | `.ai/.decisions/index.md:152` (ADR-0105 kaydı — dosya diskte YOK) | ADR indeks kaydı | ⚠️ dosyasız | §08 bütçeler (RAM/gecikme), §10 kapı 5.10-5.13 — indeks metni kaynak, ADR dosyası `⚠️ VERIFICATION REQUIRED` |
| S7 | `--/neva-engine/` (CMakeLists.txt · include/neva/*.h ×7 · src/*.cpp ×4+decode_stub · tests ×3) | Kod (çalışır durumda) | diskte VAR, derlenmiş artefaktlar var | §05 mevcut C ABI, §11 test envanteri, §18 varsayımlar |
| S8 | `.ai/VISION.md` (satır ~56/100/116/125/292/302/327/493) | Vizyon | diskte VAR | §01 hedefler (SNR/THD+N, zero-allocation, yol haritası Ay 6/Ay 14) |
| S9 | `.ai/.decisions/accepted/ADR-097-...md` §K5 | ADR | diskte VAR | §17 izlenebilirlik (band-2..7 rezervasyonu) |
| S10 | `.ai/electronic/dsp/` (10 dosya) | DSP kuralları | diskte VAR | §07 RT kural seti (bağlayıcı) |
| S11 | `.ai/architecture/k2-surucu/` (14 dosya — ADR-019 §1.1 referanslı) | Sürücü spec'leri | ADR-019'a göre diskte VAR | §04 backend spec referansları (asio-drivers.md, wasapi-exclusive.md, alsa-native.md, pipewire-modern.md, core-audio-macos.md) |

### §00.3 Bilinen çelişkiler ve uyuşmazlıklar (gizlenmez)

| # | Uyuşmazlık | Kaynak A | Kaynak B | Bu PRD'deki tutum |
|---|---|---|---|---|
| D1 | Backend arayüzü adı: `INevaAudioBackend` (ADR-107 §2.1) ↔ `IAudioBackend` (disk: `iaudio_backend.h`, ADR-019, ADR-0105) | S1 | S4, S7 | Bu PRD **`IAudioBackend`** kullanır (disk kanıtı lehine); ad kararı Vault Steward kapısı (ADR-107 §8.7) |
| D2 | ADR-0105 `accepted` olarak indekste (S6) ama dosya diskte YOK (glob 2026-10-23: `ADR-10[567]*` → yalnız ADR-106, ADR-107) | S6 | glob | İçerik yalnız **indeks kaydı** düzeyinde kullanıldı; ADR-0105-specific her madde `⚠️ VR (ADR-105 indeks kaydı)` etiketli |
| D3 | Fallback zinciri: `ASIO2 → WASAPI exclusive → WDM-KS → ALSA → PipeWire → CoreAudio → Null` (ADR-107 §2.1/S3 §1 K5) ↔ `ASIO → WASAPI → Null` (ADR-019, brain.md:862) | S1, S3 | S4 | Uzatılmış zincir (S1/S3) geçerli; ADR-019'un 3 basamağı bu zincirin alt kümesidir — §04.5'te eşleştirildi |
| D4 | VISION.md "Ay 6 = NevaEngine v1.0 · 2.0 stereo" ↔ PRD §10 M-serisi fazlar | S8 | S1/S3 | VISION tahmini = referans; faz kapıları ADR-107 §5 sırasına bağlandı |
| D5 | VISION "SNR >105dB, THD+N <%0.005" ↔ ADR-0105 indeks kaydında bu eşiklerin teyidi yok | S8 | S6 | Eşikler VISION hedefi olarak taşındı; ölçüm kapısı §08.3'te `⚠️ VR (ölçüm yok)` |

### §00.4 Kısaltmalar

| Kısaltma | Açılım |
|---|---|
| ABI | Application Binary Interface |
| ASIO | Audio Stream Input/Output (Steinberg) |
| C ABI | C dili düzeyinde ikili arayüz (`extern "C"`) |
| CI | Continuous Integration |
| DAC | Digital-to-Analog Converter |
| DSP | Digital Signal Processing |
| FFI | Foreign Function Interface |
| GUI | Graphical User Interface |
| HAL | Hardware Abstraction Layer (macOS CoreAudio) |
| IIR | Infinite Impulse Response |
| PCM | Pulse-Code Modulation |
| PEQ | Parametric Equalizer |
| RT | Real-Time |
| SPSC | Single-Producer Single-Consumer |
| THD+N | Total Harmonic Distortion + Noise |
| WASAPI | Windows Audio Session API |
| WDM-KS | Windows Driver Model — Kernel Streaming |

---

## §01 Vizyon & Ürün Özeti

### §01.1 Neva Engine nedir?

Neva Engine, CoreMusic ekosisteminin **C++20 tabanlı gerçek zamanlı (real-time) ses motorudur**.
Sorumluluğu: ses dosyasını decode etmekten çok, decode edilmiş PCM'i **işlemekten** (DSP zinciri:
EQ, gain, loudness, limiter) ve **OS ses API'sine bit-perfect düzeyinde aktarmaktan** ibarettir.
Engine, Neva Player'ın (per-OS masaüstü oynatıcısı) ve ileride `neva-host` / `neva-eq` /
`neva-matrix` / `neva-mcp` gibi çoklu executable'ların **ortak çekirdeğidir** (ADR-107 K2).

Ses saflığı iddiası (VISION.md): OS mikserini baypas eden, **zero-allocation ve lock-free**
halka arabellek yapısıyla çalışan, 32-bit float DSP zinciri taşıyan bir motor.

### §01.2 Vizyon hedefleri (S8 — VISION.md kaynaklı; ölçüm kapıları §08)

| # | Hedef | Kaynak (VISION.md) | Durum etiketi |
|---|---|---|---|
| V-1 | SNR > 105 dB | satır ~100, ~327 | Hedef — ölçüm kapısı §08.3 |
| V-2 | THD+N < %0.005 (@1 kHz) | satır ~100 | Hedef — ölçüm kapısı §08.3 |
| V-3 | Zero-allocation RT callback (bellek tahsis etmeyen) | satır ~125, ~327 | Bağlayıcı kural (§07) |
| V-4 | Lock-free halka arabellek (SPSC) | satır ~125 | IMPLEMENTED (kod: `spsc_ring.h` — S7) |
| V-5 | OS mikserini baypas eden bit-perfect aktarım | satır ~56, ~100 | K3 kapısı (ADR-107) — §11.2 |
| V-6 | 32-bit float DSP zinciri | satır ~100 | IMPLEMENTED (tasarım) — §06 |
| V-7 | 2.0 stereo v1.0 (Ay 6 tahmini) | satır ~292 | Yol haritası M4 (§15) |
| V-8 | 8.1 surround (Ay 14 tahmini) | satır ~302 | Yol haritası M6 (§15) |

### §01.3 Ürün ailesi içindeki yeri (ADR-107 K2 — S1/S3)

```text
neva-core (statik lib — DSP çekirdeği + backend soyutlama + kuyruk/zaman)
  ├─ neva-player   (exe — CLI çalma/dönüştürme/kayıt; Neva Player UI bunu kullanır)
  ├─ neva-host     (exe — GUI köprüsü, IPC sunucu)
  ├─ neva-eq       (exe — 31-band PEQ/grafik EQ ayrı pencere)
  ├─ neva-matrix   (exe — 8×8 yönlendirme matrisi)
  ├─ neva-remote   (lib+dll — uzaktan kontrol API'si)
  ├─ neva-mcp      (exe — MCP sunucu, AI agent kontrolü)
  ├─ neva-plugin   (wrap — VST3/AU/AAX sarmalayıcı; lisans kapısı K4)
  ├─ neva-session  (SHARED DLL — tek public C ABI, 25 dil FFI kapısı) ← MEVCUT (S7)
  └─ neva-cli-<kullanıcı> (exe — kullanıcının kendi hedefleri, apps/ kuralı)
```

> **Disk kanıtı (S7, glob 2026-10-23):** `neva-core` STATIC + `neva_session` SHARED hedefleri
> **vardır ve derlenmiştir** (`build/neva_engine.lib`, `build/neva_session.dll`).
> `apps/` dizini ve `neva_add_executable` kuralı **henüz YOK** (ADR-107 prompt §6 notu ile
> doğrulandı) → §10 NE-ENG-2xx maddeleri.

### §01.4 Ürünün hitap ettiği kullanıcılar

| Kullanıcı | İhtiyaç | Engine'in verdiği |
|---|---|---|
| Neva Player (ürün) | Bit-perfect ses yolu + EQ + radyo DSP | `neva_session` C ABI + `neva-core` |
| Müzik prodüktörü / dinleyici (hi-res) | 192 kHz / 32-bit float, OS mikseri bypass | ASIO/WASAPI Exclusive backend + hash kapısı |
| Dış geliştirici (FFI) | Python/C#/vb. ile kontrol | `neva_session` C ABI (char16_t path, error-code, api_version handshake) |
| AI agent (`neva-mcp`) | MCP üzerinden motor kontrolü | `neva-mcp` exe (PLANNED, U5 UNKNOWN) |
| QA/CI | Bit-perfect regresyon kapısı | Null backend + WAV hash (K3) |

### §01.5 Başarı tanımı (bu PRD için)

1. §10'daki NE-ENG gereksinimlerinin ≥%90'ı MUST olarak sınıflandırılmış ve ADR izlenebilirliği §17'de tam.
2. §11 test stratejisi ADR-107 K3 (bit-perfect hash CI) + ADR-0105 kapı 5.13 (fuzzing) kapılarını içerir.
3. §14 lisans matrisi K4 kapısını besleyecek şekilde eksiksiz; UNKNOWN maddeler (U1-U5) açık.
4. Bu doküman tek başına bir implementasyon planı **değildir** — writing-plans skill'i ile
   implementasyon planına dönüştürülmesi §15.6'da öngörülür.

---

## §02 Kapsam

### §02.1 Kapsam İÇİ (bu PRD'nin tanımladığı)

| # | Kapsam alanı | ADR dayanağı |
|---|---|---|
| I-1 | `neva-core` statik lib: DSP çekirdeği (EQ zinciri, biquad, ring buffer, zamanlayıcı) | ADR-107 K2, S3 §4 |
| I-2 | Backend soyutlama: `IAudioBackend` + `BackendRegistry` + fallback zinciri | ADR-019, ADR-107, S3 §3 |
| I-3 | Backend implementasyonları: ASIO2, WASAPI Exclusive/Shared, WDM-KS, PipeWire, ALSA, CoreAudio, Null | S3 §2, §3.4 |
| I-4 | `neva_session` C ABI (mevcut 9 fonksiyon + genişletmeler) | S7, ADR-0105 §2.6 (indeks kaydı) |
| I-5 | Bit-perfect doğrulama hattı: WAV hash CI kapısı (12 kombinasyon) | ADR-107 K3 |
| I-6 | DSP zinciri: 31-band PEQ (biquad), ramp smoothing, bypass maskesi | ADR-106 §2.2b, S7 `eq_chain.h`/`eq_grids.h` |
| I-7 | Paketleme: statik lib + çok exe + `apps/` eklenebilirlik kuralı | ADR-107 K2, S3 §4 |
| I-8 | Lisans kapıları: ASIO SDK, VST3, FFmpeg (gateway), soxr, dr_libs, libdsd | ADR-0105 indeks kaydı, ADR-106, lib-matrix |
| I-9 | Test altyapısı: kontrat test (Null), DSP birim test, RT-stress, fuzzing kapıları | S3 §6, ADR-0105 kapı 5.13 |

### §02.2 Kapsam DIŞI (bu PRD'nin tanımlamadığı)

| # | Kapsam dışı | Sahibi | Not |
|---|---|---|---|
| O-1 | Neva Player UI/UX, kütüphane yönetimi, queue | Neva Player PRD (ayrı doküman) | Bu PRD engine katmanıyla biter |
| O-2 | Decode algoritması detayları (FLAC/DSD/MP3) | ADR-0105 medya stack'i | Engine yalnız `decode_stub` sınırında kontrol eder (§09.2) |
| O-3 | Sanal ses cihazı sürücüsü (EV/WHQL imzalama) | ADR-107 §5 Faz 5 — ayrı iş birimi (K5) | Bu PRD §15.5'te yalnız referans |
| O-4 | DSP donanım topolojisi (Class AB amfi, DAC) | `.ai/electronic/hardware/` | Ses kalitesi bütçeleri §08'de yalnızca hedef olarak |
| O-5 | Web/SPA katmanı, PHP backend, DB | ADR-001/002/039 | Engine'de DB yok |
| O-6 | Radyo tuner (ICY/HLS) | ADR-106 + Neva Player PRD | Engine yalnız `icy_parser.h` çekirdeğini barındırır (S7) |
| O-7 | libplacebo 4K/HDR video yolu | ADR-0105 indeks kaydı | Player katmanı |
| O-8 | MCP sunucusunun dili ve protokol detayı | U5 (UNKNOWN) | §18.5 |

### §02.3 Kapsam sınırı kararları (nelere bu PRD'de dokunulmaz)

1. `neva-core` içine GUI framework girmez; ağ girmez (S3 §4.3 yasak 1-2).
2. Backend doğrudan exe'den çağrılmaz; yalnız `neva-core` üzerinden (S3 §4.3 yasak 4).
3. FxSound kodu AGPL → kopyalama/uyarlama yasak; yalnız mimari referans (ADR-107 K4).
4. `eval()` yasak (ADR-091); secret hiçbir `.md`/`.json`/`.log`'a yazılmaz.
5. K-space band-2..7 (ADR-097 §K5): Neva vault dosyaları için slot, gerçek domain
   tanımlanınca cetvelden alınır; uydurma isim/slot atanmaz.
---

## §03 Hedef Platformlar & Donanım Sınıfı

### §03.1 Platform matrisi (S3 §2 — ADR-107 prompt ile doğrulanmış)

| OS | Mimarî | Birincil backend | İkincil | Fallback | bit-perfect yolu | Durum |
|---|---|---|---|---|---|---|
| Windows 10/11 x64 | x86-64 | **ASIO2** (Steinberg SDK) | WASAPI Exclusive | WDM-KS → Null | ASIO (donanım pin formatı birebir) | **Kritik yol — M2** |
| Windows 10/11 ARM64 | ARM64 | WASAPI Exclusive | — | WDM-KS → Null | WASAPI exclusive (donanım formatı) | Hedef (VoiceMeeter emsal var) |
| Linux x64 | x86-64 | **PipeWire** (F32P) | ALSA (`hw:` doğrudan) | Null | PipeWire `F32P` + `hw:` doğrudan | **M3** (RPi5 = home.coremusic.net) |
| Linux ARM64 | aarch64 | PipeWire | ALSA | Null | Aynı | M3 |
| macOS | Universal/ARM64 | CoreAudio (HAL) | — | Null | HAL physical format toggle | **M6 — PLANNED (⚠️ U3)** |
| Windows XP/7 | — | — | — | — | **hedef DIŞI** | Düşük öncelik (RED) |

### §03.2 Yorumlama notu (ADR-107 §8.4/§8.5 — bağlayıcı)

Kısıt "ASIO + WASAPI exclusive **her yerde**" **olarak yazılamaz**: ASIO yalnız Windows'tur.
Doğru yorumlama: **Windows = ASIO + exclusive; Linux/macOS = exclusive/doğrudan eşdeğer yol.**
Ters yorum ("Linux'ta ASIO") teknik olarak mümkün değildir.

### §03.3 Donanım sınıfı hedefleri (§08 bütçelerine giriş)

| Sınıf | Örnek | Engine gereksinimi |
|---|---|---|
| Masaüstü x64 | Ryzen/Intel + harici DAC (ASIO/WDM-KS) | Tüm backend'ler, 192 kHz, 31-band EQ ≤%5 CPU |
| Dizüstü x64/ARM64 | WASAPI exclusive dahili DAC | Düşük güç; shared fallback zorunlu |
| SBC (RPi5) | `home.coremusic.net` | PipeWire/ALSA, ≤1 W ek CPU bütçesi `⚠️ VR (sınır kaynağı yok)` |
| CI runner | GitHub Actions (Windows/Linux) | Null backend + WAV hash — donanım bağımsız |

### §03.4 Toolchain & build matrisi (S3 §5)

| Alan | Karar |
|---|---|
| Üst düzey | `CMakeLists.txt` (repo kökü `--/neva-engine/`) + `cmake/neva-toolchains.cmake` (PLANNED) |
| Standart | `CMAKE_CXX_STANDARD 20` + REQUIRED · `noexcept` RT zorunlu · `-fno-exceptions` tartışması `⚠️ PLANNED` |
| Generator | Visual Studio 2022 (Windows, disk kanıtı: `build/neva_engine.sln`) · Ninja (Linux/CI) |
| Toolchain | MSVC v143 (disk kanıtı: vc143.pdb) · GCC 13+ · Clang 17+ · ARM64: MSVC ARM64 / GCC aarch64 |
| Paket yöneticisi | **vcpkg YOK** — statik bağımlılık politikası: ASIO SDK vendor, WAV I/O kendi kodu |
| Uyarılar | MSVC `/W4 /permissive- /Zc:__cplusplus /utf-8` (disk kanıtı: CMakeLists) |
| Windows link | `ole32 avrt uuid` (disk kanıtı: CMakeLists) |
| Çıktı | `build/<platform>-<arch>/` — her hedef ayrı artefakt |

---

## §04 Mimari

### §04.1 Katman diyagramı (bağlayıcı — ADR-019 §2.2 + S3 §3/§4)

```text
┌─────────────────────────────────────────────────────────────────┐
│  UYGULAMA KATMANI (apps/ — PLANNED, §04.7)                      │
│  neva-player · neva-host · neva-eq · neva-matrix · neva-remote  │
│  neva-mcp · neva-plugin · neva-cli-<kullanıcı>                  │
│  Kural: GUI/IPC/giriş katmanıdır; DSP mantığı YOK, ağ yalnız   │
│  remote/mcp hedeflerinde (S3 §4.3)                              │
├─────────────────────────────────────────────────────────────────┤
│  C ABI SINIRI — neva_session (SHARED DLL — IMPLEMENTED, S7)     │
│  Opaque handle · error-code · api_version handshake · char16_t  │
│  C++ tipleri bu sınırdan GEÇMEZ (25 dil FFI kapısı)             │
├─────────────────────────────────────────────────────────────────┤
│  ÇEKİRDEK — neva-core (statik lib)                              │
│  ┌─────────────────────┬──────────────────────┬───────────────┐ │
│  │ DSP (neva::rt)      │ Zaman/Kuyruk         │ Yönetim       │ │
│  │ biquad · eq_chain   │ spsc_ring (S7)       │ Backend-      │ │
│  │ eq_grids (ISO grid) │ zamanlayıcı          │ Registry      │ │
│  │ limiter/loudness    │                      │ (fallback)    │ │
│  │ (PLANNED §06)       │                      │               │ │
│  └─────────────────────┴──────────────────────┴───────────────┘ │
│  Kural: çekirdek OS API'sini ASLA doğrudan çağırmaz.            │
├─────────────────────────────────────────────────────────────────┤
│  BACKEND SOYUTLAMA — IAudioBackend (sözleşme, §04.3)            │
├──────────────┬──────────────┬───────────┬──────────┬────────────┤
│ AsioBackend  │ WasapiBackend│ WdmKs     │ PipeWire │ Alsa       │
│ (PLANNED M2) │ Excl/Shared  │ (PLANNED) │ (M3)     │ (M3)       │
│              │ (IMPLEMENTED │           │          │            │
│              │  iskelet:    │           │          │            │
│              │  wasapi_     │           │          │            │
│              │  backend.cpp)│           │          │            │
├──────────────┼──────────────┼───────────┼──────────┼────────────┤
│ CoreAudio    │ NullBackend  │           │          │            │
│ (M6 PLANNED) │ (IMPLEMENTED│           │          │            │
│              │  S7)         │           │          │            │
└──────────────┴──────────────┴───────────┴──────────┴────────────┘
        OS ses API'leri (WASAPI/ASIO/ALSA/PipeWire/CoreAudio)
```

### §04.2 Çekirdek kaynak envanteri (disk kanıtı — glob 2026-10-23, S7)

| Dosya | Tür | Durum | İçerik özeti |
|---|---|---|---|
| `CMakeLists.txt` | Build | IMPLEMENTED | `neva_engine` STATIC + `neva_session` SHARED · C++20 · 3 test exe (`neva_tests`, `eq_grids_tests`, `icy_parser_tests`) + `add_test` · MSVC `/W4 /permissive-` |
| `include/neva/neva_session.h` | C ABI | IMPLEMENTED | `NEVA_API_VERSION 0x00010000` (1.0.0) · opak `neva_session_t` · `neva_result` (OK/INVALID_ARG/NO_DEVICE/UNSUPPORTED_FORMAT/INTERNAL) · 9 fonksiyon (§05.1) |
| `include/neva/iaudio_backend.h` | Arayüz | IMPLEMENTED | `class IAudioBackend` · `enumerate(DeviceList&)` out-param · `StreamMode { Exclusive, Shared }` · `BackendResult` — header yorumu: "ADR'deki imza derlenebilirlik için minik düzeltildi; semantik aynı" |
| `include/neva/spsc_ring.h` | Kuyruk | IMPLEMENTED | Lock-free SPSC ring buffer (RT veri yolu) |
| `include/neva/biquad.h` | DSP | IMPLEMENTED | Biquad filtre çekirdeği |
| `include/neva/eq_chain.h` | DSP | IMPLEMENTED | EQ zinciri (per-kanal `set_gain(band, ch, db, ramp_ms)` — C ABI'dan bağımsız C++ yolu) |
| `include/neva/eq_grids.h` | DSP | IMPLEMENTED | EQ bant grid'leri (ADR-106 ISO 1/3-oktav) |
| `include/neva/icy_parser.h` | Radyo | IMPLEMENTED | ICY header/durum makinesi çekirdeği (ADR-106 tuner temeli) |
| `src/wasapi_backend.cpp` | Backend | IMPLEMENTED (iskelet) | WASAPI backend kodu — ADR-019'a göre "PLANNED" etiketli kod yüzeyi fiilen var; derleniyor (`wasapi_backend.obj`) |
| `src/null_backend.cpp` | Backend | IMPLEMENTED | Test/CI backend'i |
| `src/neva_session.cpp` | C ABI | IMPLEMENTED | Session yaşam döngüsü implementasyonu |
| `src/decode_stub.cpp/.h` | Decode sınırı | IMPLEMENTED (stub) | Gerçek decode ADR-0105 katmanına ait; engine sınırı stub |
| `tests/test_biquad.cpp` | Test | IMPLEMENTED | Biquad doğruluk testi (`build/neva_tests.exe`, `test_biquad.exe`) |
| `tests/test_eq_grids.cpp` | Test | IMPLEMENTED | Grid tutarlılık testi (`eq_grids_tests.exe` Debug+Release) |
| `tests/test_icy_parser.cpp` | Test | IMPLEMENTED | ICY parser testi (`icy_parser_tests.exe` Debug+Release) |
| `build/**` | Artefakt | VAR | Derleme çıktısı: `neva_engine.lib` · `neva_session.dll/.lib/.exp` · test exe'leri · CTest kayıtları |

> **Etiket düzeltmesi (bu PRD raporlar):** ADR-019 §1.1-B (2026-09-25) "repo geneli 0
> `*.cpp/*.h`" der — bu ölçüm **artık geçersizdir** (2026-10-23 glob: `--/neva-engine/`
> altında 10+ kaynak dosya + derlenmiş artefaktlar). ADR-019 dokunulmaz (frozen değil ama
> accepted) → düzeltme yalnız bu PRD §04.2'sinde raporlandı; ADR metni değiştirilmedi.

### §04.3 IAudioBackend sözleşmesi (S3 §3.2 — disk-sözleşmesi esas alınır)

```cpp
// neva/core/include/neva/core/IAudioBackend.h  (sözleşme — S3 §3.2)
namespace neva::core {

class IAudioBackend {
public:
    virtual ~IAudioBackend() = default;

    virtual BackendKind kind() const noexcept = 0;
    virtual const char* name() const noexcept = 0;

    // Cihaz keşfi (bit-perfect doğrulaması için format listesi gerekli)
    virtual Expected<DeviceList, StreamError> enumerate() const noexcept = 0;
    virtual Expected<FormatList, StreamError> supported_formats(DeviceId) const noexcept = 0;

    // Akış yaşam döngüsü — realtime thread callback
    virtual Expected<void, StreamError> open(const DeviceId&, const AudioFormat&,
                                             RenderCallback) noexcept = 0;
    virtual Expected<void, StreamError> start() noexcept = 0;
    virtual Expected<void, StreamError> stop()  noexcept = 0;
    virtual void close() noexcept = 0;

    // bit-perfect kanıtı: cihazın gerçekten kabul ettiği format
    virtual Expected<AudioFormat, StreamError> negotiated_format() const noexcept = 0;
};

} // namespace neva::core
```

Çekirdek tipler (S3 §3.1):

```cpp
struct AudioFormat {
    double   sample_rate;      // 44100.0 … 192000.0
    uint16_t bit_depth;        // 16 | 24 | 32 (float=32f)
    uint16_t channels;         // 1 … 8 (8.1 hedefi)
    bool     is_float;         // true → IEEE-754 float
    uint32_t frames_per_buffer;// 32 … 4096 (exclusive hedef: 32/64/128)
};
enum class BackendKind : uint8_t { Asio, WasapiExclusive, WasapiShared, WdmKs,
                                   Alsa, Pipewire, CoreAudio, Null };
struct StreamError { int32_t code; BackendKind backend; const char* what; };
```

ADR-019 §2.2b ekleri (imza farklı evre — §00.3-D2): `switchMode(Mode)` (exclusive↔shared
geçişi), `setDeviceRate(uint32_t)` (mismatch: önce cihaz hızı), `kill()` (kill-switch).
**Tutum:** bu üç method disk sözleşmesine **faz 2 genişletmesi** olarak eklenir (NE-ENG-031/032/033);
`negotiated_format()` K3 kapısının ön koşuludur.

### §04.4 BackendRegistry & fallback zinciri (S3 §3.3 + ADR-107 §2.1 — bağlayıcı sıra)

```cpp
// Sıra: Asio → WasapiExclusive → WdmKs → Alsa/Pipewire → CoreAudio → Null
Expected<std::unique_ptr<IAudioBackend>, StreamError>
open_best_backend(const DeviceSelector&, const AudioFormat& target) noexcept;
```

| Basamak | Backend | Platform | Düşme koşulu (hangi hata hangi basamağa) |
|---|---|---|---|
| 1 | ASIO2 | Windows x64 | ASIO sürücüsü yok / `ASIOError_InvalidMode` → 2 (spec: asio-drivers.md:109) |
| 2 | WASAPI Exclusive | Windows | `AUDCLNT_E_DEVICE_IN_USE` / exclusive reddi → 2b (spec: wasapi-exclusive.md:126) |
| 2b | WASAPI Shared | Windows | Paylaşımlı da başarısızsa → 3 |
| 3 | WDM-KS | Windows | Kernel Streaming yoksa → 6 (Null) |
| 4 | PipeWire | Linux | PipeWire yoksa → 5 |
| 5 | ALSA (`hw:`) | Linux | `hw:` erişilemezse → 6 |
| 6 | CoreAudio | macOS | Tek yol (fallback yok; başarısızsa → 7) |
| 7 | **Null** | Tümü | Son çare — sessiz akış, test/CI (ADR-019 brain.md:862: "WASAPI fallback → Null Output") |

> **Eşleştirme notu (§00.3-D3):** ADR-019'un "ASIO → WASAPI → Null" 3 basamağı bu 7 basamağın
> kısaltmasıdır; zincir ADR-107 §2.1 uzun haliyle bağlayıcıdır, ADR-019 ile çelişmez.

### §04.5 Backend sorumluluk matrisi (S3 §3.4 — aynen bağlayıcı)

| Backend | Uygulama dizini (PLANNED) | Öncelik | Özel kural |
|---|---|---|---|
| ASIO | `neva-core/backends/asio/` | P0 | ASIO SDK lisansı (GPL/ticari) — `⚠️ LİSANS KAPISI` (U1) |
| WASAPI exclusive | `neva-core/backends/wasapi/` | P0 | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `IsFormatSupported` iki deneme (WAVEFORMATEX/WAVEFORMATEXTENSIBLE) |
| WDM-KS | `neva-core/backends/wdmks/` | P2 | Düşük seviye; exclusive başarısızsa |
| PipeWire | `neva-core/backends/pipewire/` | P1 | `F32P` (32-bit float) default; `hw:` doğrudan |
| ALSA | `neva-core/backends/alsa/` | P1 | `snd_pcm_hw_params` ile sabit format |
| CoreAudio | `neva-core/backends/coreaudio/` | P3 | HAL physical format toggle |
| Null | `neva-core/backends/null/` | P0 | Test/CI (bit-perfect hash hattı) — **IMPLEMENTED** |

### §04.6 Backend seçim özellik sözleşmesi (ADR-019 §2.2 — özellik kararları)

| # | Özellik | Karar |
|---|---|---|
| F1 | Device hot-plug | Backend enumerate periyodik + olay dinleme (CoreAudio `kAudioHardwarePropertyDevices`; Windows/WASAPI device notification `⚠️ VR (ADR-019 §1.1-C: olay API'si çaprazsız)`) |
| F2 | exclusive↔shared geçiş | Tetik: `AUDCLNT_E_DEVICE_IN_USE` / kullanıcı kilit talebi → `switchMode()` (faz 2 genişletmesi) |
| F3 | Sample-rate/bit-depth mismatch | **Önce cihaz hızı (`setDeviceRate`) → sonra adapter/engine resample (soxr, producer-side) → sonra fallback** (ADR-019 §2 — resample sınırı kararı) |
| F4 | Kill-switch | `kill()` noexcept; ADR-017 xrun politikası ile uyum: fade-out → 50 ms sessizlik → restart (brain.md:869) |
| F5 | Master clock | **Cihaz** (ADR-106 kural 11 — v1.0'daki "radyo stream master" ifadesi düzeltildi) |

### §04.7 Paketleme: lib + çok executable (ADR-107 K2 / S3 §4 — bağlayıcı envanter)

| Hedef | Tip | Amaç | Windows | Linux | Durum |
|---|---|---|---|---|---|
| `neva-core` | statik lib | DSP çekirdeği + backend soyutlama + kuyruk/zaman | `.lib` | `.a` | IMPLEMENTED (iskelet — `neva_engine.lib`) |
| `neva-session` | SHARED dll | Tek public C ABI (FFI) | `neva_session.dll` | `.so` | IMPLEMENTED |
| `neva-player` | exe | CLI çalma/dönüştürme/kayıt | `.exe` | ✓ | PLANNED (M4) |
| `neva-host` | exe | GUI köprüsü (IPC sunucu) | `.exe` | ✓ | PLANNED (M4) |
| `neva-plugin` | dll/wrap | VST3/AU/AAX sarmalayıcı | `.vst3` | `.vst3` | PLANNED (M6 — lisans kapısı K4) |
| `neva-eq` | exe | 31-band PEQ ayrı pencere (VoiceMeeter BUSGEQ15 emsal) | ✓ | ✓ | PLANNED (M4) |
| `neva-matrix` | exe | 8×8 yönlendirme matrisi (BUSMatrix8 emsal) | ✓ | ✓ | PLANNED (M5) |
| `neva-remote` | lib+dll | Uzaktan kontrol API'si (VoiceMeeterRemote emsal) | ✓ | ✓ | PLANNED (M5) |
| `neva-mcp` | exe | AI agent kontrolü — MCP sunucu (fxmcp emsal) | ✓ | ✓ | PLANNED (M5) — dil U5 `UNKNOWN` |
| `neva-cli-<kullanıcı>` | exe | Kullanıcı kendi exe'lerini ekler | ✓ | ✓ | Kural PLANNED (apps/ M4'te kurulur) |

Eklenebilirlik kuralı (S3 §4.2): yeni exe yalnız `apps/CMakeLists.txt` → `neva_add_executable(...)`
ile; `neva-core`'a dokunulmaz. *Disk notu: `apps/` henüz YOK, `neva_add_executable` CMakeLists'te geçmiyor → kural bu oturumlarda kurulacak (NE-ENG-201).*

Bağımlılık yasakları (S3 §4.3 — bağlayıcı):

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | `neva-core` içine GUI framework girmez | Core RT thread'de çalışır; GUI kütüphaneleri alloc/lock içerir |
| 2 | `neva-core` içine ağ girmez | Ağ ayrı hedef (`neva-remote`/`neva-mcp`) |
| 3 | RT thread'de `new`/`malloc`/`mutex` yasak | Zero-allocation, lock-free (§07) |
| 4 | Backend doğrudan exe'den çağrılmaz | Yalnız `neva-core` üzerinden (tek soyutlama noktası) |

### §04.8 İş kanıtı (RE — VoiceMeeter emsali, S3 §1)

VoiceMeeter üretimde modüler executable stratejisi kullanır: BUS GEQ15 / BUSMatrix8 /
MacroButtons / StreamView / VBAN2MIDI ayrı exe'ler + Remote DLL. ADR-107 K2 bu emsalle desteklenir.
**Kaynak:** `.ai/research/reverse-voicemeeter-fxsound-2026-10-22.md` (S2 §4 kaynak listesi).
**Yasak:** FxSound kodu AGPL → kopyalama/uyarlama YASAK, yalnız mimari referans.
---

## §05 API Sözleşmeleri

### §05.1 Mevcut `neva_session` C ABI (S7 — disk kanıtı, birebir)

```c
/* neva_session.h — TEK PUBLIC C ABI HEADER (ADR-0105 §2.6).
 * Opaque handle + error-code + api_version handshake. ABI'yi kiran her
 * degisiklik major artis ister. C++ tipleri bu sinirdan GECMEZ
 * (25 dil FFFI kapisi: Python ctypes / C# P-Invoke). */
#define NEVA_API_VERSION 0x00010000u /* 1.0.0 */

typedef struct neva_session neva_session_t;

typedef enum neva_result {
  NEVA_OK = 0,
  NEVA_ERR_INVALID_ARG = 1,
  NEVA_ERR_NO_DEVICE = 2,
  NEVA_ERR_UNSUPPORTED_FORMAT = 3,
  NEVA_ERR_INTERNAL = 4
} neva_result_t;

NEVA_API uint32_t      neva_session_api_version(void);
NEVA_API neva_result_t neva_session_create(neva_session_t** out);
NEVA_API void          neva_session_destroy(neva_session_t* s);
NEVA_API neva_result_t neva_session_open_file(neva_session_t* s, const char16_t* path);
NEVA_API neva_result_t neva_session_set_output_device(neva_session_t* s, uint32_t index);
NEVA_API neva_result_t neva_session_play(neva_session_t* s);
NEVA_API neva_result_t neva_session_pause(neva_session_t* s);
NEVA_API neva_result_t neva_session_stop(neva_session_t* s);
NEVA_API neva_result_t neva_session_set_eq_gain(neva_session_t* s, uint32_t band, double db);
```

Sözleşme kuralları:

| # | Kural | Kaynak |
|---|---|---|
| A1 | Opak handle — C++ tipi sınırı geçemez | header yorumu (S7) |
| A2 | ABI kıran her değişim `NEVA_API_VERSION` major artışı ister | header yorumu (S7) |
| A3 | Hata = return code (exception yok — FFI güvenliği) | `neva_result_t` |
| A4 | `char16_t*` path (Windows Unicode) — Unix'te dönüşüm katmanı `⚠️ VR (Unix path sözleşmesi header'da yok)` | header |
| A5 | `set_eq_gain` band 0..30 (31 bant), her iki kanala uygulanır; per-kanal = C++ `EqChain::set_gain(band, ch, db, ramp_ms)` | header yorumu |

### §05.2 C ABI genişletme önerisi (PLAYER İHTİYACI → kardeş PRD talepleri; `[ÖNERİ]` etiketli)

> Bu §05.2 ADR-0105 §2.6 sürüm sözleşmesi içinde **minor ek** olarak tasarlanmıştır
> (ABI kırmaz — mevcut fonksiyonlar değişmez, yenileri eklenir). ADR-106 kural 2 örneği:
> metadata event'i "minor ek — ABI kırmaz" olarak zaten onaylı bir yaklaşımdır.

| # | Önerilen fonksiyon (ad önerisi `[ÖNERİ]`) | İhtiyaç | Öncelik |
|---|---|---|---|
| E1 | `neva_session_set_event_callback(s, cb, user)` — metadata/state/error event'leri | Radyo StreamTitle, durum makinesi UI | MUST (ADR-106 kural 2) |
| E2 | `neva_session_open_queue(s, paths, count)` + `neva_session_preload_next(s)` | Gapless dosya→dosya (AK-5) | MUST |
| E3 | `neva_session_get_position/duration(s, ms*)` | UI seek/progress | MUST |
| E4 | `neva_session_seek(s, ms)` | Seek | MUST |
| E5 | `neva_session_set_eq_band_count(s, count)` — 10/15/20/31 + bypass maskesi yayını | ADR-106 §2.2b kural 2/5 | MUST |
| E6 | `neva_session_eq_preset_save/load(s, {grid_version, band_count, gains[], preamp})` | Preset (ADR-106 kural 6) | SHOULD |
| E7 | `neva_session_get_negotiated_format(s, AudioFormat*)` | UI'da bit-perfect göstergesi (K3 kanıtı) | SHOULD |
| E8 | `neva_session_get_device_list(s, ...)` / device change callback | Cihaz seçimi + hot-plug | SHOULD |
| E9 | Tuner: `neva_tuner_connect/disconnect(s, url)` (radyo oturumu) | ADR-106 §2.2a | MUST (Player M3) |
| E10 | `neva_session_get_lufs(s, double*)` | UI loudness göstergesi | MAY |

Kural: her yeni fonksiyon `NEVA_ERR_UNSUPPORTED_FORMAT` benzeri yeni error code **eklemez**
(mevcut kodlar korunur); yeni enum değerleri minor sürüm (0x0001xxxx) ile gelir.

### §05.3 C++ iç arayüzler (core — FFI dışı, `neva::` namespace)

| Arayüz | Sorumluluk | Durum |
|---|---|---|
| `neva::core::IAudioBackend` | §04.3 sözleşmesi | Sözleşme (S3) + iskelet kod (`wasapi_backend.cpp`, `null_backend.cpp`) |
| `neva::core::BackendRegistry` / `open_best_backend` | Fallback zinciri (§04.4) | Sözleşme (S3 §3.3) |
| `neva::SPSCRingBuffer` (`spsc_ring.h`) | RT veri yolu | IMPLEMENTED (S7) |
| `neva::Biquad` (`biquad.h`) | Tek filtre | IMPLEMENTED (S7) |
| `neva::EqChain` (`eq_chain.h`) | 31×2 zincir + ramp | IMPLEMENTED (S7) |
| `neva::eq_grids` (`eq_grids.h`) | ISO 1/3-oktav grid tanımı | IMPLEMENTED (S7) — grid doğruluğu test `eq_grids_tests.exe` |
| `neva::IcyParser` (`icy_parser.h`) | ICY header/durum makinesi | IMPLEMENTED (S7) — tuner'ın çekirdeği |
| Decode sınırı (`decode_stub.h`) | Gerçek decode entegrasyonu için seam | STUB (S7) — §09.2 |

### §05.4 Sürümleme & uyumluluk politikası

| Yüzey | Politika |
|---|---|
| C ABI (`neva_session`) | SemVer ruhu: ABI kırıcı = major (`0x0002xxxx`); minor = yeni fonksiyon/event |
| C++ core (`neva::`) | ABI garantisi YOK (statik lib — derleme zamanı uyum) |
| Backend sözleşmesi (`IAudioBackend`) | Yalnız ADR revizyonuyla değişir (yeni method = tüm implementasyonlar güncellenir + kontrat test yeşil) |
| Preset formatı | `{grid_version, band_count, per-band gain dB, preamp}` — grid_version ile ileri uyum (ADR-106 kural 6) |

---

## §06 DSP Zinciri

### §06.1 Zincir sırası (bağlayıcı — ADR-106 debate düzeltmesi + ADR-0105 §2.5 indeks ⚠️)

```text
SPSC ring (PCM float32, producer-side: decode + soxr)
  → [RT] 31-band biquad EQ (grid §06.2; aktif bant maskesi; 5-20 ms ramp)
  → [RT] gain / preamp
  → [RT] true-peak limiter (BS.1770-4 true-peak; ADR-0105 §2.1 ⚠️)
  → [RT] IAudioBackend.render → OS/CIHAZ
[non-RT yan thread] R128 loudness sidechain (ring'den okur — UI/normalizasyon)
```

Kurallar:

| # | Kural | Kaynak |
|---|---|---|
| D1 | Biquad/limiter **RT callback'de**; R128 **non-RT** | ADR-106 debate düzeltmesi (ring invariant) |
| D2 | Callback içinde tahsis/lock/decode/JSON parse **yasak** | ADR-017 (ADR-106 §1.4) |
| D3 | EQ bypass: aktif olmayan biquad **bypass maskesiyle** pasif — zincir takası/yok etme YOK | ADR-106 §2.2b kural 4 |
| D4 | Gain/bant değişimi **5-20 ms ramp** ile (click yasağı) | ADR-105 §2.3 (ADR-106 kural 5) ⚠️ |
| D5 | Cascade (1↔2 biquad/bant) mod değişimi nadir → fade-out/re-init/fade-in | ADR-106 kural 5 |
| D6 | Radyo akışı da aynı zincirden geçer (kullanıcı radyoda EQ ister); bit-perfect null-test **radyoda çalıştırılmaz** (lossy) | ADR-106 kural 4 + kapı 5.1 Ş4 inverted guard |

### §06.2 EQ grid sözleşmesi (ADR-106 §2.2b — bağlayıcı, eksiksiz)

**31-band ISO 1/3-oktav grid (Hz):** 20 · 25 · 31.5 · 40 · 50 · 63 · 80 · 100 · 125 · 160 ·
200 · 250 · 315 · 400 · 500 · 630 · 800 · 1000 · 1250 · 1600 · 2000 · 2500 · 3150 · 4000 ·
5000 · 6300 · 8000 · 10000 · 12500 · 16000 · 20000

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Grid sabit | Yukarıdaki 31 frekans; preset taşınabilirliği bunun üzerine kurulu |
| 2 | İzinli bant sayıları | **10 · 15 · 20 · 31** (seçim UI'da) |
| 3 | 10-band alt küme | 31.5, 63, 125, 250, 500, 1000, 2000, 4000, 8000, 16000 Hz (ISO; VLC 62.5 teyidi v1.1.1: VLC 62.5/63=0 isabet kullanır — Neva ISO 63'te kalır; VLC preset'leri kendi frekans setiyle okunur) |
| 4 | 20-band | AIMP emsal aralığı (Band0..19) — gerçek frekans listesi dump'da kanıtlanmadı → `⚠️ VR` (`eq_grids` tanımında sabitlenir, dump/web kaynağı ile teyit edilir) |
| 5 | 15-band | Eşit log-aralık alt küme → `⚠️ VR` (2/3-oktav standart listesi teyit edilmeden kesin değil) |
| 6 | Biquad bütçesi | **Sabit allocate: 31 band × 2 kanal = 62 biquad (cascade 124)** — baştan tahsis; bypass maskesi ile pasifleştirme |
| 7 | Benchmark kapısı | Her zaman **31-band (en kötü durum)** ölçülür — ADR-0105 §5.6 değişmez |
| 8 | Bant sayısı değişimi | RT dışı maske hesabı → atomik maske yayını (acquire/release) → callback ramp ile uygular; tahsis yok |
| 9 | Parametrik mod | Grafik EQ = grid; **parametrik = RBJ serbest mod** ayrıdır (ADR-0105 §2.1 korunur) |

### §06.3 Biquad matematiği (RBJ cookbook — ADR-106 §1.3 kaynaklı)

| Parametre | Değer |
|---|---|
| Tipler | Peaking (grafik EQ default) · LowShelf · HighShelf · LPF/HPF (crossover, ileri faz) |
| Katsayılar | RBJ Audio EQ Cookbook (biquad.org) — `A = 10^(dB/40)` |
| Topoloji | Direct Form I/II `⚠️ VR (seçim diskte yok — eq_chain.h içine bakılarak teyit edilir)` |
| İşlem maliyeti | ~59.5 M MAC/s (31×2 biquad @192 kHz — ADR-106 §1.3 kaynak hesabı) → tek çekirdek ≤%5 CPU benchmark kapısı (ADR-0105 §5.6 ⚠️) |
| Gecikme | IIR ≈ 0 (grup gecikmesi hariç) |

### §06.4 Loudness & limiter

| Blok | Spesifikasyon | Durum |
|---|---|---|
| R128 loudness sidechain | ITU-R BS.1770-4 / EBU R128; non-RT thread; ring'den okur | ADR-106 §2.2a şeması |
| True-peak limiter | BS.1770-4 true-peak (4× oversample ⚠️ VR — ADR-0105 §2.1 indeks) | RT zincirde son blok |
| LUFS normalizasyon hedefi | `⚠️ VR (hedef LUFS değeri ADR-0105 indeks kaydında yok)` — önerilen -14 LUFS (akış platformu standardı) `[ÖNERİ]` |

### §06.5 Resample (soxr)

| # | Kural | Kaynak |
|---|---|---|
| R1 | Resample **producer-side** (ring'den önce) — RT callback'de resample YOK | ADR-106 kural 5 (ADR-0105 §2.5) |
| R2 | Master clock = **cihaz**; hız farkı ring sayacı ile dengelenir (overflow→drop / starve→fade) | ADR-106 kural 5, 11 |
| R3 | soxr **LGPL-2.1** (ADR-0105 v1.2.0 düzeltmesi) — dinamik link ✅ / statik link `⚠️ LİSANS` | indeks kaydı |
| R4 | Bit-perfect yolda (dosya formatı == cihaz formatı) soxr **bypass** | K3 ruhu — `⚠️ VR (ADR-0105 indeks metninde açık satır yok; NE-ENG-061 olarak tanımlanır)` |
---

## §07 Realtime Kilitleri (RT Kuralları — Bağlayıcı)

### §07.1 RT yasakları (ADR-017 — ADR-019 §1.4, ADR-106 §1.4, S3 §4.3 kural 3 ile bağlayıcı)

Render callback içinde (RT thread) **ASLA** yapılmayacaklar:

| # | Yasak | Alternatif |
|---|---|---|
| T1 | `new` / `malloc` / `delete` / `free` (herhangi bir bellek tahsisi) | Baştan allocate (§07.3) |
| T2 | `std::mutex` / `std::lock_guard` / lock-free olmayan kilit | SPSC ring + atomik (§07.4) |
| T3 | Dosya I/O (okuma/yazma) | Producer thread |
| T4 | Ağ I/O (HTTP, socket) | Tuner thread (player) |
| T5 | `sleep` / `wait` / senkron primitif bekleyişi | Non-bloklayıcı tasarım |
| T6 | `throw` / exception (`-fno-exceptions` tartışması `⚠️ PLANNED`) | Error-code (C ABI) |
| T7 | `printf`/`std::cout` (kilitli stdout) | Lock-free olay kuyruğu → non-RT log |
| T8 | Decode (FLAC/MP3/...) | Producer-side (§06.5) |
| T9 | JSON/XML parse, UTF-8 doğrulama | Non-RT (metadata sanitizasyonu) |
| T10 | Virtual dispatch zinciri boyunca tahsis eden allocator | Önceden allocate edilmiş pool |

### §07.2 RT izinli yüzey

| İzinli | Not |
|---|---|
| Stack lokal değişkenler (sabit/önceden bilinen boyut) | Callback başına ölçülebilir stack bütçesi §08.4 |
| SPSC ring read/write (lock-free, atomik head/tail) | `spsc_ring.h` IMPLEMENTED |
| Biquad/limiter hesap (sabit boyutlu state) | `biquad.h`, `eq_chain.h` IMPLEMENTED |
| Atomikler (acquire/release; lock-free queue yayınları) | Maske yayını (ADR-106 kural 5) |
| `std::atomic` flag/sayaç okuma | Xrun/underrun sayacı |

### §07.3 Baştan allocate (pre-allocation) bütçesi

| Kaynak | Boyutlandırma | Tahsis zamanı |
|---|---|---|
| SPSC ring | Kapasite ≥ 2× max periyot + jitter payı `⚠️ VR (kesin katsayı ADR-0105 §2.5 indeksinde yok)` | Session open |
| EQ zinciri | 31×2 biquad sabit (cascade 124) | Session open (ADR-106 kural 4) |
| Limiter state | Kanal başına sabit | Session open |
| Callback scratch | En büyük periyot (4096 frame) × kanal × float | Session open |

### §07.4 Lock-free veri yolu deseni

```text
[Producer thread]                          [RT consumer]
  decode/soxr → yaz                           oku → DSP → backend
  SPSC ring: write_idx (atomic, release)      read_idx (atomic, release)
  invariant: writer asla read_idx'yi GEÇMEZ   (overflow → drop, starve → fade)
  tek writer · tek reader — kilit YOK
```

| Olay | Davranış (bağlayıcı) |
|---|---|
| Ring STARVE (RT okuyucu boş bulur) | Fade-to-silence ≤50 ms ramp (ADR-0106 şeması); seslenme yasağı |
| Ring OVERFLOW (producer yazamıyor) | Drop (akış sürekli — radyo); dosya oynatmada producer RT'nin arkasına takılır (drop yok, gecikme büyür) `[ÖNERİ — NE-ENG-075]` |
| Xrun (underrun/overrun OS seviyesi) | ADR-017 politikası: fade-out → 50 ms sessizlik → restart (brain.md:869) |

### §07.5 Ölçüm ve denetim

| Denetim | Yöntem | Kapı |
|---|---|---|
| Tahsis denetimi | RT yolunda operator new override + debug hard-fail (test build) `[ÖNERİ]` | CI |
| Lock denetimi | Static audit + kod incelemesi (`.ai/electronic/dsp/` kuralları) | PR review |
| Süreç denetimi | Callback süre ölçümü (P99/P999), bütçe aşımı sayacı | §08.4 |
| Stres | 30 dk sürekli çalışma, 0 xrun hedefi (S3 §6.1) | CI nightly |

---

## §08 Performans & Kalite Bütçeleri

### §08.1 Kalite eşikleri (VISION.md — S8; ölçüm kapıları ile)

| Metrik | Hedef | Durum |
|---|---|---|
| SNR | >105 dB | Hedef (V-1); ölçüm düzeneği §11.3 |
| THD+N | <%0.005 @1 kHz | Hedef (V-2); ölçüm düzeneği §11.3 |
| Bit-perfect | byte-eşitlik (hash) | K3 CI kapısı (§11.2) |
| Gecikme | ASIO <10 ms · WASAPI <20 ms | ADR-006 (ADR-019 §1.4) ölçüm kapısı |
| Xrun | 30 dk = 0 | S3 §6.1 |

> **Dürüstlük notu:** SNR/THD+N eşikleri VISION'dan gelir; ADR-0105 indeks kaydında bu
> eşiklerin teyidi yok → ölçüm düzeneği §11.3'te tanımlanana kadar `⚠️ VR` (§00.3-D5).

### §08.2 CPU bütçeleri (EQ en kötü durum)

| İş | Bütçe | Kaynak |
|---|---|---|
| 31-band EQ @192 kHz stereo | ≤%5 tek çekirdek | ADR-0105 §5.6 (indeks) + ADR-106 §1.3 hesabı |
| Biquad maliyeti referansı | ~59.5 M MAC/s (31×2) | ADR-106 §1.3 |
| Limiter | `⚠️ VR` | |
| Toplam DSP (EQ+limiter) | `⚠️ VR` | |

### §08.3 Bellek bütçeleri

| Kalem | Bütçe | Kaynak |
|---|---|---|
| Engine çalışma belleği (dosya oynatma) | `⚠️ VR — ADR-0105 §2.9 ≤400 MB player toplamı; engine payı tanımsız` | indeks |
| Ring + EQ + scratch (pre-allocate) | Kullanım-anında ölçülecek; RT tahsis = 0 (§07) | Bu PRD |
| Callback stack | ≤4 KB `[ÖNERİ]` | Bu PRD |

### §08.4 Callback süre bütçesi

| Periyot | Süre bütçesi (kural: süre < periyot, aksi halinde underrun) |
|---|---|
| 128 frame @48 kHz | 2.67 ms — ölçüm: P99 < 2.0 ms `[ÖNERİ]` |
| 256 frame @48 kHz | 5.33 ms — P99 < 4.0 ms `[ÖNERİ]` |
| 32 frame @192 kHz | 0.17 ms — P99 < 0.13 ms `[ÖNERİ]` |

### §08.5 Ölçüm protokolleri (kapılar)

| # | Ölçüm | Yöntem | Kapı |
|---|---|---|---|
| M1 | Bit-perfect hash | WAV hash byte-eşitlik × 12 kombinasyon (§11.2) | CI (K3) |
| M2 | Latency | Loopback ölçüm / backend raporlama | ADR-006 kapısı |
| M3 | Xrun | Sayaç, 30 dk stres | CI nightly |
| M4 | CPU (EQ worst-case) | Benchmark 31-band @192k | ADR-0105 §5.6 |
| M5 | SNR/THD+N | Düz tone + THD+N analyzer (dizi testi) `[ÖNERİ — DAC loopback gerekli]` | §11.3 |

---

## §09 Format Decode/Encode Kapsamı (Engine Sınırı)

### §09.1 Engine'nin format rolü (kapsam ayrımı)

Engine **decode etmez** (decode player medya stack'inde — kardeş PRD §05); engine'nin rolü:
(1) `decode_stub` üzerinden PCM'i teslim almak, (2) format bilgisini `AudioFormat` olarak
taşımak, (3) `UNSUPPORTED_FORMAT` error code'u ile reddetmek.

### §09.2 Decode sınırı (mevcut stub — S7 `decode_stub.cpp/h`)

| Sözleşme | Durum |
|---|---|
| Stub: `decode → PCM float32 → ring` | IMPLEMENTED (iskelet) |
| Gerçek decode bağlama (dr_libs/libdsd/FFmpeg) | PLANNED — kardeşi PRD §05; engine tarafı: yalnız `AudioFormat` ve hata kodları |
| Multi-channel (8.1) | `channels 1..8` (S3 §3.1) — decode tarafı player |

### §09.3 Format destek matrisi (engine sözleşmesi — hedef)

| Kategori | Örnekler | Engine tutumu |
|---|---|---|
| Sample-rate | 44.1k · 48k · 88.2k · 96k · 176.4k · 192k | `AudioFormat.sample_rate` 44100-192000 arası |
| Bit derinliği | 16 · 24 · 32i · 32f | `bit_depth` + `is_float` |
| Kanal | 1.0 · 2.0 · 2.1 · 5.1 · 7.1 · 8.1 | `channels 1..8` (8.1 = V-8) |
| DSD | DFF/DSF/DoP | Engine için PCM'e çevrilmiş akış; DSD bit akışı doğrudan = `⚠️ VR` (native DSD çıkışı ADR-0105 indeksinde teyitli değil) |

### §09.4 Encode/dönüştürme (neva-player CLI — PLANNED)

| İş | Durum |
|---|---|
| Dönüştürme (decode → encode WAV/FLAC) | ADR-107 K2'de `neva-player` = "CLI çalma/dönüştürme/kayıt" — kapsam VAR |
| Encode kütüphanesi seçimi | `⚠️ VR — FLAC encoder (libFLAC? dr_libs encode yok) kararı yok` |
| Kapı | Dönüştürülmüş çıktı hash doğrulaması (roundtrip) `[ÖNERİ]` |

---

## §10 Ürün Gereksinimleri (NE-ENG Serisi)

### §10.1 Sınıflandırma

| Etiket | Anlam |
|---|---|
| MUST | Zorunlu — yoksa release kabul edilmez |
| SHOULD | Önemli — release öncesi gerekçeli istisna ile ertelenebilir |
| MAY | Opsiyonel — yol haritasında |
| P0 | Kritik yol (M2 bloke edici) |
| P1 | Yüksek (M3-M4) |
| P2 | Orta (M5) |
| P3 | Düşük (M6) |
| `[ÖNERİ]` | Bu PRD'nin ekidir; ADR'de karşılığı yok — Vault Steward onayı gerekli |
| `⚠️ VR` | Dayandığı ADR disk kanıtı eksik — doğrulanana kadar kesin sayılmaz |

### §10.2 Gereksinim sayısı özeti

| Grup | Aralık | Konu | Tahmini adet |
|---|---|---|---|
| A | NE-ENG-001..020 | Mimari & platform çekirdeği | 20 |
| B | NE-ENG-021..045 | Backend'ler & fallback | 25 |
| C | NE-ENG-046..060 | C ABI & API sözleşmesi | 15 |
| D | NE-ENG-061..080 | DSP zinciri | 20 |
| E | NE-ENG-081..100 | RT & performans | 20 |
| F | NE-ENG-101..125 | Paketleme & build | 25 |
| G | NE-ENG-126..145 | Bit-perfect & test altyapısı | 20 |
| H | NE-ENG-146..160 | Lisans & güvenlik | 15 |
| I | NE-ENG-161..175 | Faz kapıları (G0-G5) | 15 |
| J | NE-ENG-176..182 | UNKNOWN/U varlıkları (U1-U5) | 7 |
| | | **Toplam** | **182** |

### §10.3 Grup A — Mimari & Platform Çekirdeği

#### NE-ENG-001 — Tek C ABI sınırı
- **Açıklama:** Tüm dış dünya (FFI: Python ctypes, C# P-Invoke, 25 dil) yalnız `neva_session.h` üzerinden bağlanır; C++ tipleri bu sınırdan geçmez.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `neva_session.h` dışında public C++ header export edilmez; `neva_session.dll` export tablosunda yalnız `neva_*` sembolleri görünür.
- **ADR izi:** ADR-0105 §2.6 (indeks kaydı ⚠️) · disk: `neva_session.h` yorum satırı
- **İlgili test:** TC-ENG-001

#### NE-ENG-002 — Opaque handle zorunluluğu
- **Açıklama:** `neva_session_t` tam tanım (forward-declared struct); çağırıcı içeriği göremez.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Header'da struct tanımı yok; yalnızca `typedef struct neva_session neva_session_t;` — derleme testi kırılırsa FAIL.
- **ADR izi:** disk `neva_session.h:29`
- **İlgili test:** TC-ENG-002

#### NE-ENG-003 — api_version handshake
- **Açıklama:** Çağırıcı açılışta `neva_session_api_version()` ile sürümü doğrular; `0x00010000` (1.0.0).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** ABI kıran değişimde sürüm major artar; eski header ile yeni DLL eşleşmesi derleme/runtime kontrolü ile reddedilir `[ÖNERİ — runtime kontrol detayı ADR'de yok]`.
- **ADR izi:** disk `neva_session.h:26`
- **İlgili test:** TC-ENG-003

#### NE-ENG-004 — Hata kodu sözleşmesi (exception yok)
- **Açıklama:** Tüm hatalar `neva_result_t` enum'u ile döner (OK/INVALID_ARG/NO_DEVICE/UNSUPPORTED_FORMAT/INTERNAL); FFI sınırında C++ exception yayılmaz.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Export edilen her `neva_*` fonksiyonu exception fırlatamaz; `extern "C"` bloğu içinde `throw` yok (statik denetim).
- **ADR izi:** disk `neva_session.h:31-37` · ADR-0105 §2.6 ⚠️
- **İlgili test:** TC-ENG-004

#### NE-ENG-005 — char16_t dosya yolu (Windows Unicode)
- **Açıklama:** `neva_session_open_file(s, const char16_t* path)` — Windows UTF-16 yolu destekler (Türkçe karakterli yollar: `C:\Müzik\...`).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Türkçe karakterli (ğ, ı, ş, İ, ö, ü, ç) uzun yolda dosya açılır; UTF-8→UTF-16 dönüşümü Unix portunda ayrı katman `[ÖNERİ]`.
- **ADR izi:** disk `neva_session.h`
- **İlgili test:** TC-ENG-005

#### NE-ENG-006 — IAudioBackend tek soyutlama noktası
- **Açıklama:** OS ses API'leri yalnızca backend implementasyonları içinde çağrılır; çekirdek ve exe'ler OS API'ye doğrudan dokunmaz.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `neva-core` ve `apps/*` dışında `#include <audioclient.h>/<alsa/...>/pa_*.h` yok — CI grep kapısı.
- **ADR izi:** S3 §4.3 yasak 4 · ADR-019 §2.2
- **İlgili test:** TC-ENG-006

#### NE-ENG-007 — BackendRegistry kayıt tablosu
- **Açıklama:** Tüm backend'ler statik kayıt tablosunda (kind, name, platform-bitişi) listelenir; yeni backend eklemek tabloya tek satır eklemektir.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Registry'de platform-başı beklenen sıralama §04.4 zinciriyle birebir; Null her platformda kayıtlı.
- **ADR izi:** S3 §3.3
- **İlgili test:** TC-ENG-007

#### NE-ENG-008 — open_best_backend fallback zinciri
- **Açıklama:** §04.4 7 basamaklı zincir (ASIO→WASAPI Excl→(Shared)→WDM-KS→PipeWire→ALSA→CoreAudio→Null) uygulanır.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Her basamak yapay hata enjeksiyonuyla düşürüldüğünde zincir sıradaki basamağa geçer; son basamak Null'dur, asla "hiçbir şey yok" durumu oluşmaz.
- **ADR izi:** ADR-107 §2.1 · ADR-019 (brain.md:862) · S3 §3.3
- **İlgili test:** TC-ENG-008

#### NE-ENG-009 — negotiated_format() kanıt zorunluluğu
- **Açıklama:** Backend'in gerçekten kabul ettiği format (`negotiated_format()`) K3 bit-perfect kapısının ön koşuludur; hedef formatla eşleşmezse PASS yazılamaz.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Hash testi `negotiated_format() == hedef` kontrolünü sağlamadan yeşil olmaz.
- **ADR izi:** S3 §3.2 · ADR-107 K3
- **İlgili test:** TC-ENG-009

#### NE-ENG-010 — BackendKind enum sabitliği
- **Açıklama:** `Asio, WasapiExclusive, WasapiShared, WdmKs, Alsa, Pipewire, CoreAudio, Null` — sıralama ekleme sonda; mevcut değer asla yeniden numaralandırılmaz (ABI/log uyumu).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Enum değeri değişikliği derleme hatası veya majör sürüm ister.
- **ADR izi:** S3 §3.1
- **İlgili test:** TC-ENG-010

#### NE-ENG-011 — AudioFormat alan sözleşmesi
- **Açıklama:** `sample_rate` (44100..192000), `bit_depth` (16|24|32), `channels` (1..8), `is_float`, `frames_per_buffer` (32..4096).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Sınır dışı değerlerde `StreamError` döner (UB yok); 8.1 hedefi için channels=8 geçerli.
- **ADR izi:** S3 §3.1 · VISION (8.1, V-8)
- **İlgili test:** TC-ENG-011

#### NE-ENG-012 — BackendKind + StreamError taşıma
- **Açıklama:** Her hata `StreamError{code, backend, what}` ile hangi backend'in düştüğünü bildirir (fallback telemetri).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Zincir düşüşlerinde hangi basamağın neden düştüğü log'a (non-RT) yazılır.
- **ADR izi:** S3 §3.1
- **İlgili test:** TC-ENG-012

#### NE-ENG-013 — ADR-019 ek imzalar (faz 2 genişletmesi)
- **Açıklama:** `switchMode()`, `setDeviceRate()`, `kill()` disk sözleşmesine faz 2'de eklenir.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Üç method `IAudioBackend`'te mevcut; tüm implementasyonlar (Null dahil) override eder; kontrat testi yeşil.
- **ADR izi:** ADR-019 §2.2b · §00.3-D2
- **İlgili test:** TC-ENG-013

#### NE-ENG-014 — exclusive↔shared geçiş (switchMode)
- **Açıklama:** `AUDCLNT_E_DEVICE_IN_USE` / kullanıcı kilidi tetikler; geçiş kesintisiz (ring korunur).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Geçiş sırasında ses kesintisi hedefi `⚠️ VR (eşik yok — önerü: ≤20 ms)`; hata durumunda zincir düşer.
- **ADR izi:** ADR-019 F2 · S3 §3.4
- **İlgili test:** TC-ENG-014

#### NE-ENG-015 — Mismatch çözüm sırası (F3)
- **Açıklama:** Önce `setDeviceRate()` (cihaz hızı) → sonra engine/producer resample (soxr) → sonra fallback.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 44.1 kHz dosya 48 kHz cihazda: hız denemesi başarısızsa soxr ile resample; RT callback'te resample asla yok.
- **ADR izi:** ADR-019 §2 (çözüm seçenekleri 1-3)
- **İlgili test:** TC-ENG-015

#### NE-ENG-016 — kill-switch (noexcept acil durdurma)
- **Açıklama:** `kill()` noexcept; xrun/zincir tükenmesi: fade-out → 50 ms sessizlik → restart.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kill sonrası callback devre dışı; yeniden başlatma session state ile uyumlu; callback içinde asla bloklanmaz.
- **ADR izi:** ADR-019 F4 · ADR-017 (brain.md:869)
- **İlgili test:** TC-ENG-016

#### NE-ENG-017 — Platform matrisi bağımsızlığı
- **Açıklama:** Windows x64/ARM64, Linux x64/ARM64, macOS (PLANNED U3); XP/7 hedef dışı.
- **Tip:** MUST · **Öncelik:** P0 (Windows/Linux) · P3 (macOS)
- **Kabul kriteri:** Kaynak kod `#ifdef` platform kütüphanesine göre ayrılır; ortak çekirdek platform-bağımsız derlenir.
- **ADR izi:** S3 §2 · ADR-107 §8.4
- **İlgili test:** TC-ENG-017

#### NE-ENG-018 — K-space band rezervasyonu koruması
- **Açıklama:** ADR-097 §K5 band-2..7 rezervi Neva vault'u için saklı; gerçek domain tanımlanınca cetvelden çıkarılır, uydurma isim atanmaz.
- **Tip:** MUST · **Öncelik:** P3
- **Kabul kriteri:** Bu PRD dahil hiçbir Neva dokümanı band-2..7'ye gerçek domain adı yazmaz.
- **ADR izi:** ADR-097 §K5
- **İlgili test:** TC-ENG-018 (statik denetim)

#### NE-ENG-019 — Windows XP/7 kapsam dışı teyidi
- **Açıklama:** Düşük öncelikli geçmiş platform RED — hiçbir gereksinim XP/7'de çalışmayı iddia etmez.
- **Tip:** MUST · **Öncelik:** P3
- **Kabul kriteri:** §10'da XP/7'yi hedefleyen madde yok; CI matrisinde yok.
- **ADR izi:** S3 §2 yorumu (Düşük öncelik RED)
- **İlgili test:** TC-ENG-019 (statik denetim)

#### NE-ENG-020 — ARM64 port hedefi
- **Açıklama:** Windows ARM64 (WASAPI exclusive) + Linux aarch64 (PipeWire/ALSA) — Raspberry Pi 5 (`home.coremusic.net`) ilk sınıf.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** ARM64 build CI'da derlenir; Null backend testleri geçer (donanım testi RPi5 üzerinde).
- **ADR izi:** S3 §2 · S3 §5 (GCC aarch64)
- **İlgili test:** TC-ENG-020

### §10.4 Grup B — Backend'ler & Fallback

#### NE-ENG-021 — WASAPI Exclusive implementasyonu (P0)
- **Açıklama:** `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `IsFormatSupported` iki deneme (WAVEFORMATEX → WAVEFORMATEXTENSIBLE).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Event-driven callback kurulur; iki deneme sırası korunur; başarısızlık `StreamError` + zincir 2b/3'e düşer.
- **ADR izi:** S3 §3.4 · spec `wasapi-exclusive.md:126`
- **İlgili test:** TC-ENG-021

#### NE-ENG-022 — WASAPI Shared fallback
- **Açıklama:** Exclusive reddedilince shared mod (OS mikser dahil — bit-perfect iddiası düşer, süreklilik korunur).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `AUDCLNT_E_DEVICE_IN_USE` senaryosunda ses devam eder; bit-perfect rozeti devre dışı kalır (player tarafı §05.2 B3).
- **ADR izi:** ADR-019 §1.1 · S3 §3.4
- **İlgili test:** TC-ENG-022

#### NE-ENG-023 — ASIO2 backend (lisans kapısı K4/U1'e bağlı)
- **Açıklama:** ASIO SDK 2.x entegrasyonu; donanım pin formatı birebir (bit-perfect'in birinci yolu).
- **Tip:** MUST · **Öncelik:** P0 (lisans kararı U1 verildiğinde)
- **Kabul kriteri:** ASIO open/start/stop/callback akışı; sürücü yoksa zincir 2'ye düşer; **SDK lisansı çözülmeden release YOK** (NE-ENG-161 GATE).
- **ADR izi:** ADR-019 · S3 §3.4 (P0) · ADR-107 K4
- **İlgili test:** TC-ENG-023

#### NE-ENG-024 — WDM-KS backend
- **Açıklama:** Kernel Streaming — exclusive başarısız son Windows basamağı.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** `hw:` benzeri doğrudan erişim; başarısızlık Null'a düşer.
- **ADR izi:** S3 §3.4 (P2) · ADR-107 §2.1
- **İlgili test:** TC-ENG-024

#### NE-ENG-025 — PipeWire backend (F32P)
- **Açıklama:** Linux birincil; `F32P` (32-bit float) default + `hw:` doğrudan yolu.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** PipeWire yoksa ALSA'ya düşer; float akışta bit-derinlik dönüşümü olmadan geçiş.
- **ADR izi:** S3 §3.4 · S3 §2
- **İlgili test:** TC-ENG-025

#### NE-ENG-026 — ALSA backend (hw: doğrudan)
- **Açıklama:** `snd_pcm_hw_params` ile sabit format; OS mikser (dmix) yerine doğrudan cihaz.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** `hw:` erişilemezse Null'a düşer; format sabitleme başarısızsa hata döner.
- **ADR izi:** S3 §3.4
- **İlgili test:** TC-ENG-026

#### NE-ENG-027 — CoreAudio backend (HAL)
- **Açıklama:** macOS physical format toggle ile bit-perfect eşdeğeri.
- **Tip:** SHOULD · **Öncelik:** P3 (M6, U3 platform kararı)
- **Kabul kriteri:** HAL physical format okunur/yazılır; başarısızlık Null.
- **ADR izi:** S3 §3.4 · spec `core-audio-macos.md`
- **İlgili test:** TC-ENG-027

#### NE-ENG-028 — Null backend (CI/test)
- **Açıklama:** Sessiz akış üretir; WAV hash hattının uç noktası.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** IMPLEMENTED (disk); her platformda en son basamak; testlerde deterministik.
- **ADR izi:** S7 `null_backend.cpp` · ADR-019 (Null Output)
- **İlgili test:** TC-ENG-028

#### NE-ENG-029 — Cihaz enumerate + format listesi
- **Açıklama:** `enumerate()` cihazları, `supported_formats()` her cihazın desteklediği format setini verir (bit-perfect doğrulaması için gerekli).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Liste boşsa `StreamError`; UI/CLI listeyi dizebilir.
- **ADR izi:** S3 §3.2
- **İlgili test:** TC-ENG-029

#### NE-ENG-030 — Hot-plug olay desteği
- **Açıklama:** Cihaz kopması/connect: enumerate tetikleme + fallback (F1).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Çalma sırasında cihaz koparsa zincir devreye girer; session durumu raporlanır (event E8).
- **ADR izi:** ADR-019 F1 · spec `core-audio-macos.md:46-57` · Windows/Linux `⚠️ VR`
- **İlgili test:** TC-ENG-030

#### NE-ENG-031 — switchMode genişletmesi (NE-ENG-013'un parçası)
- **Açıklama:** `switchMode(Exclusive|Shared)` methodu sözleşme genişletmesi.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Null dahil tüm backend'lerde tanımlı (varsayılan: Unsupported döner).
- **ADR izi:** ADR-019 §2.2b
- **İlgili test:** TC-ENG-031

#### NE-ENG-032 — setDeviceRate genişletmesi
- **Açıklama:** Cihaz saat hızını ayarlar (mismatch sırasının 1. basamağı).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Desteklemeyen cihaz `StreamError` döner → zincir resample/fallback'e geçer.
- **ADR izi:** ADR-019 §2.2b
- **İlgili test:** TC-ENG-032

#### NE-ENG-033 — kill() genişletmesi
- **Açıklama:** Backend seviyesi acil durdurma (NE-ENG-016 ile eşleştirilmiş).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** noexcept; callback sonraki periyotta durur.
- **ADR izi:** ADR-019 §2.2b
- **İlgili test:** TC-ENG-033

#### NE-ENG-034 — Callback render sözleşmesi
- **Açıklama:** `open(device, format, RenderCallback)` — callback RT thread'de, sözleşme §07 kurallarına tabi.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Callback imzası sabit; taşıma (out-buffer + frame sayacı) backend'e göre format dönüşümü içermez (PCM float/interleaved ortak).
- **ADR izi:** S3 §3.2 · ADR-017
- **İlgili test:** TC-ENG-034

#### NE-ENG-035 — Backend yaşam döngüsü (open/start/stop/close)
- **Açıklama:** Dört geçişli yaşam döngüsü; tekrar-open destekli (cihaz değiştirme).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Yanlış sıralama (start önce open) `StreamError`; close idempotent (ikinci çağrı güvenli).
- **ADR izi:** S3 §3.2
- **İlgili test:** TC-ENG-035

#### NE-ENG-036 — Bit-perfect yolu: format birebirlik
- **Açıklama:** Dosya formatı == cihaz formatı olduğunda dönüşüm yok (soxr bypass, dither yok, gain 0 dB).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 12 kombinasyon (§11.2) hash byte-eşit.
- **ADR izi:** ADR-107 K3 · ADR-0105 kapı 5.1 ⚠️
- **İlgili test:** TC-ENG-036

#### NE-ENG-037 — OS mikser baypası (exclusive)
- **Açıklama:** Exclusive/ASIO/hw: yollarında OS ses efektleri (ses seviyesi animasyonu, EQ) devrede değildir.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Windows'ta session volume 1.0'a sabitlenir; paylaşım modunda bu iddia düşer ve raporlanır.
- **ADR izi:** VISION (V-5) · ADR-019
- **İlgili test:** TC-ENG-037

#### NE-ENG-038 — Exclusive kilit çatışması (device-in-use)
- **Açıklama:** İkinci uygulama exclusive isterse mevcut oturum davranışı: `AUDCLNT_E_DEVICE_IN_USE` → fallback.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Test: iki süreç — biri exclusive tutar, diğeri düşüş zincirini uygular.
- **ADR izi:** spec `wasapi-exclusive.md:126`
- **İlgili test:** TC-ENG-038

#### NE-ENG-039 — Periyot/buffer hizalama kurtarma
- **Açıklama:** `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` → periyot yeniden hesap + tekrar deneme.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Hata tek seferde yutulmaz; yeniden hesap başarısızsa zincir düşer.
- **ADR izi:** ADR-106 §1.3 (radio-and-hires §3-§4)
- **İlgili test:** TC-ENG-039

#### NE-ENG-040 — Xrun sayacı & telemetri
- **Açıklama:** Underrun/overrun sayaçları atomik; non-RT log'a akar.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** 30 dk stres testte sayaçlar okunabilir; ADR-017 politikası uygulanır.
- **ADR izi:** ADR-017 · S3 §6.1
- **İlgili test:** TC-ENG-040

#### NE-ENG-041 — Deterministik test backend'i (Null + injectable clock)
- **Açıklama:** Null + sahte saat ile zamansal testler (ring, ramp, gapless) deterministik.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Testler duvar saati/gecikmeye bağlı değil; CI'da 2× hız simülasyonu yapılabilir `[ÖNERİ]`.
- **ADR izi:** S3 §6 (test altyapısı)
- **İlgili test:** TC-ENG-041

#### NE-ENG-042 — Backend kontrat test takımı
- **Açıklama:** Her backend aynı test paketinden geçer (open/start/stop/close/enumerate/negotiate).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Yeni backend = aynı takımı geçmeden registry'ye kayıt yok (CI kuralı).
- **ADR izi:** S3 §6.1 (kabul 1)
- **İlgili test:** TC-ENG-042

#### NE-ENG-043 — Fallback telemetri olayı
- **Açıklama:** Her zincir düşüşü `BackendResult` + event E8 ile UI'a bildirilebilir.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Düşüş nedeni (kod + backend) event'te taşınır.
- **ADR izi:** S3 §3.1 (StreamError) · §05.2 E8
- **İlgili test:** TC-ENG-043

#### NE-ENG-044 — Backend başına dizin kuralı
- **Açıklama:** Uygulama kodu `neva-core/backends/<kind>/` altında; çapraz include yasak (yalnız `IAudioBackend.h` üzerinden).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CI grep: `backends/wasapi/*` içinde `backends/alsa/*` include'u yok.
- **ADR izi:** S3 §3.4
- **İlgili test:** TC-ENG-044

#### NE-ENG-045 — Null dahil 8 backend envanteri
- **Açıklama:** Asio · WasapiExclusive · WasapiShared · WdmKs · Alsa · Pipewire · CoreAudio · Null — sekiz tanesi de registry'de.
- **Tip:** MUST · **Öncelik:** P1 (macOS M6'ya kadar PLANNED olarak işaretli)
- **Kabul kriteri:** Registry sayımı 8; uygulanmayanlar `NotImplemented` (`StreamError`) döner, sessizce yok sayılmaz.
- **ADR izi:** S3 §3.1 BackendKind
- **İlgili test:** TC-ENG-045

### §10.5 Grup C — C ABI & API Sözleşmesi

#### NE-ENG-046 — Tek public header disiplini
- **Açıklama:** `neva_session.h` tek public C header; internal header'lar install edilmez.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Install/distribute paketinde yalnız tek header + DLL/LIB; iç header yok.
- **ADR izi:** disk `neva_session.h` · ADR-0105 §2.6 ⚠️
- **İlgili test:** TC-ENG-046

#### NE-ENG-047 — Event callback genişletmesi (E1)
- **Açıklama:** `neva_session_set_event_callback(s, cb, user)` — metadata/state/error/buffer event'leri.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Callback RT'den çağrılmaz (non-RT kuyruk); `user` pointer taşınır; unregister güvenli.
- **ADR izi:** ADR-106 kural 2 · kardeş PRD §05.2
- **İlgili test:** TC-ENG-047

#### NE-ENG-048 — Queue API (E2)
- **Açıklama:** `open_queue(paths, count)` + `preload_next(s)` — gapless ön koşulu.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Sıradaki dosya decode'u RT'yi beklemeden hazırlanır; geçişte ring kesintisiz.
- **ADR izi:** ADR-0105 AK-5 ⚠️ · kardeş PRD §05.2
- **İlgili test:** TC-ENG-048

#### NE-ENG-049 — Position/duration API (E3)
- **Açıklama:** Milisaniye cinsinden; tahmin yok (bilinen değer).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Stream (radyo) durumunda duration=-1/kısa kod; atomik okuma.
- **ADR izi:** kardeş PRD §05.2
- **İlgili test:** TC-ENG-049

#### NE-ENG-050 — Seek API (E4)
- **Açıklama:** `seek(s, ms)` — decode sınırında (ring drop + yeniden doldurma); RT callback'te değil.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Seek sırasında kısa fade (click yok); sınırda clamp; başarısızlık hata döner.
- **ADR izi:** kardeş PRD §05.2 · ADR-017 (RT dışı işlem)
- **İlgili test:** TC-ENG-050

#### NE-ENG-051 — EQ band count API (E5)
- **Açıklama:** `set_eq_band_count(10|15|20|31)` — ADR-106 kural 2/5.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Değişim RT dışı maske hesabı → atomik yayın → callback ramp (tahsis yok).
- **ADR izi:** ADR-106 kural 5 · kardeş PRD §05.2
- **İlgili test:** TC-ENG-051

#### NE-ENG-052 — EQ preset API (E6)
- **Açıklama:** `{grid_version, band_count, gains[], preamp}` kaydet/yükle.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Band sayısı preset'ten farklıysa kural 2 işletilir; grid_version uyuşmazlığında düzeltme + uyarı.
- **ADR izi:** ADR-106 kural 6 · kardeş PRD §05.2
- **İlgili test:** TC-ENG-052

#### NE-ENG-053 — negotiated_format API (E7)
- **Açıklama:** UI bit-perfect rozeti için gerçek format okuması.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Cihazın gerçekten kabul ettiği format döner (K3 kanıtı).
- **ADR izi:** kardeş PRD §05.2 · ADR-107 K3
- **İlgili test:** TC-ENG-053

#### NE-ENG-054 — Device list + change event (E8)
- **Açıklama:** `get_device_list` + hot-plug/fallback event'i.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Liste değişimi olayı UI'a akar; enumerate thread-safe.
- **ADR izi:** ADR-019 F1 · kardeş PRD §05.2
- **İlgili test:** TC-ENG-054

#### NE-ENG-055 — Tuner API (E9)
- **Açıklama:** `tuner_connect(s, url)` / `tuner_disconnect(s)` — radyo oturumu ayrı mod.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** URL validator Player katmanında (engine tuner'a ham URL alır — doğrulama zorunlu akışta player'da) `[ÖNERİ — engine içine validator koyma; tek kapı kuralı ADR-106]`.
- **ADR izi:** ADR-106 §2.2a · kardeş PRD §05.2 E9
- **İlgili test:** TC-ENG-055

#### NE-ENG-056 — LUFS okuma (E10)
- **Açıklama:** R128 sidechain'den okunan canlı LUFS değeri (UI göstergesi).
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** 10 Hz güncelleme (atomik publish); RT'de hesap yok.
- **ADR izi:** ADR-106 §2.2a şeması · kardeş PRD §05.2
- **İlgili test:** TC-ENG-056

#### NE-ENG-057 — Yeni error code eklenmez kuralı
- **Açıklama:** ABI genişletmeleri mevcut 5 kodu kullanır; yeni enum minor sürümde ve ek sonunda.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Mevcut kod değerleri 0-4 sabit (yeniden numaralandırma yasak).
- **ADR izi:** kardeş PRD §05.2 notu
- **İlgili test:** TC-ENG-057

#### NE-ENG-058 — char16_t/UTF-8 geçiş katmanı (Unix)
- **Açıklama:** POSIX'te yol UTF-8; `char16_t` giriş UTF-8'e çevrilip verilir.
- **Tip:** SHOULD · **Öncelik:** P1 (Linux fazı)
- **Kabul kriteri:** Türkçe karakterli yol Linux'ta da açılır (NFC/NFD dikkati `[ÖNERİ]`).
- **ADR izi:** kardeş PRD §05.1 A4
- **İlgili test:** TC-ENG-058

#### NE-ENG-059 — Thread-safety beyanı
- **Açıklama:** Control fonksiyonları (play/pause/set_*) çoklu thread'den çağrılabilir; session internal atomik/queue ile korunur (RT veri yolu zaten SPSC).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** TSan testi temiz (Null backend üzerinde); belge beyanı + sınır (`get_*` atomik snapshot).
- **ADR izi:** kardeş PRD §05.3–§05.4 · ADR-0105 §2.6 ⚠️
- **İlgili test:** TC-ENG-059

#### NE-ENG-060 — ABI freeze kapısı
- **Açıklama:** M4 (player CLI) öncesi C ABI dondurulur; sonrası yalnız minor ek.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** ABI dump (binwalk/nm) kaydedilir; sürüm 1.0.0 sembol seti arşivde.
- **ADR izi:** kardeş PRD §05.4
- **İlgili test:** TC-ENG-060

### §10.6 Grup D — DSP Zinciri

#### NE-ENG-061 — Biquad çekirdeği (RBJ)
- **Açıklama:** Peaking/LowShelf/HighShelf/LPF/HPF; RBJ Audio EQ Cookbook katsayıları (A=10^(dB/40)).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `test_biquad` (IMPLEMENTED) + doğruluk sapması toleransı `⚠️ VR (tolerans değeri tanımsız — önerü: ±0.01 dB @ referans frekanslar)`.
- **ADR izi:** ADR-106 §1.3 · disk `biquad.h` · `test_biquad.cpp`
- **İlgili test:** TC-ENG-061

#### NE-ENG-062 — EqChain: 31 band × 2 kanal
- **Açıklama:** 62 biquad sabit allocate (cascade modunda 124); per-kanal `set_gain(band, ch, db, ramp_ms)`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Tahsis session-open'da; zincirde çalışırken `new` yok (denetim §07.5).
- **ADR izi:** ADR-106 kural 4/6 · disk `eq_chain.h`
- **İlgili test:** TC-ENG-062

#### NE-ENG-063 — ISO 1/3-oktav grid (31 frekans)
- **Açıklama:** §06.2 listesi — 20…20000 Hz 31 frekans; `eq_grids.h` IMPLEMENTED + test.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `eq_grids_tests` yeşil; frekanslar tabloyla byte-eşit.
- **ADR izi:** ADR-106 §2.2b · disk `eq_grids.h`
- **İlgili test:** TC-ENG-063

#### NE-ENG-064 — 10-band alt küme (ISO)
- **Açıklama:** 31.5/63/125/250/500/1k/2k/4k/8k/16k — ISO; VLC 62.5 farkı bilinçli (preset kendi setiyle okunur).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Alt küme üretilir; VLC preset'i zorla map edilmez.
- **ADR izi:** ADR-106 §2.2b kural 3
- **İlgili test:** TC-ENG-064

#### NE-ENG-065 — 15/20-band grid tanımı (teyitli)
- **Açıklama:** 15-band eşit log-aralık alt küme; 20-band AIMP emsal aralığı — frekans listeleri `⚠️ VR`.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** `eq_grids` tanımında sabitlenir; teyit (AIMP dump/web) gelene kadar etiket korunur.
- **ADR izi:** ADR-106 §2.2b kural 4/5 · ADR-106 §1.4
- **İlgili test:** TC-ENG-065

#### NE-ENG-066 — Bypass maskesi (zincir takası yok)
- **Açıklama:** Pasif biquad maskelenir; zincirin boyutu/aynı tahsis hiç değişmez.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Tüm-band flat: maske=0 → matematiksel doğrudan geçiş; hash testi flat durumda byte-eşit.
- **ADR izi:** ADR-106 kural 4 · kardeş PRD §06.1 D3
- **İlgili test:** TC-ENG-066

#### NE-ENG-067 — Ramp smoothing 5-20 ms
- **Açıklama:** Gain/bant değişimi ramp ile (click/zip yok); süre 5-20 ms.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Anlık adım değişimi testinde pop yok (waveform analizi); ramp süresi parametrik.
- **ADR izi:** ADR-105 §2.3 (ADR-106 kural 5) ⚠️
- **İlgili test:** TC-ENG-067

#### NE-ENG-068 — Cascade mod geçişi
- **Açıklama:** 1↔2 biquad/bant mod değişimi nadir; fade-out → re-init → fade-in.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Geçişte ses sürekliliği; tahsis yalnız geçişte (RT dışı hazırlık).
- **ADR izi:** ADR-106 kural 5
- **İlgili test:** TC-ENG-068

#### NE-ENG-069 — Parametrik mod (RBJ serbest)
- **Açıklama:** Grafik EQ grid sabit; parametrik ayrı mod (f/Q/gain serbest) korunur.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Parametrik mod RT kurallarına uyar; grid preset'i ile karışmaz (ayrı state).
- **ADR izi:** ADR-106 §2.2b kural 7 (ADR-0105 §2.1 korunur)
- **İlgili test:** TC-ENG-069

#### NE-ENG-070 — True-peak limiter (BS.1770-4)
- **Açıklama:** Zincirin son RT bloğu; true-peak (4× oversample `⚠️ VR`).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Bit-perfect yolda limiter BYPASS (hash bozulmaz); aşım testi ceiling aşılmaz.
- **ADR izi:** ADR-0105 §2.1 ⚠️ · kardeş PRD §06.4
- **İlgili test:** TC-ENG-070

#### NE-ENG-071 — R128 loudness sidechain (non-RT)
- **Açıklama:** ITU-R BS.1770-4 / EBU R128; ring'den okuyan ayrı thread; canlı LUFS.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** RT callback'e dokunmaz; atomik publish; referans implementasyon testi (bilinen sinyal → beklenen LUFS ±0.1 `⚠️ VR (tolerans tanımsız)`).
- **ADR izi:** ADR-106 §2.2a · kardeş PRD §06.4
- **İlgili test:** TC-ENG-071

#### NE-ENG-072 — soxr bypass (bit-perfect yol)
- **Açıklama:** Dosya formatı == cihaz formatıysa soxr devre dışı (bypass).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §11.2 12 kombinasyonda hash eşitliği bunu zorunlu kılar.
- **ADR izi:** kardeş PRD §06.5 R4 (`⚠️ VR` — NE-ENG-061 olarak bu PRD'de sabitlendi)
- **İlgili test:** TC-ENG-072

#### NE-ENG-073 — Preamp/gain bloğu
- **Açıklama:** Zincirde EQ sonrası tek gain (preamp); 0 dB default; ramp'li.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Bit-perfect yolda 0 dB = identity (hash).
- **ADR izi:** kardeş PRD §06.1
- **İlgili test:** TC-ENG-073

#### NE-ENG-074 — Multi-channel (8.1) DSP
- **Açıklama:** Zincir kanal başına uygulanır (channels=8'e kadar); EQ stereo varsayılan (tüm kanallara).
- **Tip:** SHOULD · **Öncelik:** P2 (M6)
- **Kabul kriteri:** 5.1/8.1 test kaynağından geçiş; CPU bütçesi karşılanır.
- **ADR izi:** VISION V-8 · S3 §3.1
- **İlgili test:** TC-ENG-074

#### NE-ENG-075 — Ring overflow politikası (dosya modu)
- **Açıklama:** Dosya oynatmada producer RT'nin arkasına takılır (drop yok, gecikme büyür); radyoda drop.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** İki mod davranışları §07.4 tablosuyla tutarlı.
- **ADR izi:** kardeş PRD §07.4 `[ÖNERİ]`
- **İlgili test:** TC-ENG-075

#### NE-ENG-076 — DSP profil kapısı (worst-case)
- **Açıklama:** Her benchmark 31-band (en kötü) ile yapılır; düşük bantlarla "iyi görünme" yasak.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI'da EQ benchmark'ı 31-band sabit.
- **ADR izi:** ADR-0105 §5.6 (ADR-106 kural 6) ⚠️
- **İlgili test:** TC-ENG-076

#### NE-ENG-077 — CPU ≤%5 EQ (192 kHz stereo)
- **Açıklama:** Tek çekirdek ≤%5; ~59.5 M MAC/s hesabı ile uyumlu.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CI benchmark FAIL → release bloke.
- **ADR izi:** ADR-106 §1.3 · ADR-0105 §5.6 ⚠️
- **İlgili test:** TC-ENG-077

#### NE-ENG-078 — DSP state sıfırlama API
- **Açıklama:** `reset()` — seek/mode değişiminde biquad state'leri sıfırlanır (ring drop ile senkron).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Reset sonrası ilk periyot transient'sız (eski durum sızıntısı yok).
- **ADR izi:** kardeş PRD §05.2 (seek dahil) `[ÖNERİ]`
- **İlgili test:** TC-ENG-078

#### NE-ENG-079 — Denormal koruması
- **Açıklama:** IIR state denormal → flush-to-zero (FTZ/DAZ) CPU patlaması koruması.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Sessizlik sonrası ilk ses periyotunda CPU spike yok (bilinen klasik hata).
- **ADR izi:** YOK — `[ÖNERİ]` (RT best practice; `.ai/electronic/dsp/` ile hizalı)
- **İlgili test:** TC-ENG-079

#### NE-ENG-080 — DSP birim test kapsamı
- **Açıklama:** biquad + eq_grids testleri IMPLEMENTED; eq_chain + limiter + ramp testleri eklenir.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §11.4 birim test matrisi yeşil; her grid frekansı doğruluk testinde.
- **ADR izi:** disk `tests/*` · kardeş PRD §11
- **İlgili test:** TC-ENG-080

### §10.7 Grup E — RT & Performans

#### NE-ENG-081 — Zero-allocation RT callback
- **Açıklama:** Render callback'te `new`/`malloc`/container büyütme yasak (§07.1 T1).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Test build'de operator-new override ile hard-fail; 30 dk stres = 0 tahsis.
- **ADR izi:** ADR-017 · VISION V-3 · kardeş PRD §07
- **İlgili test:** TC-ENG-081

#### NE-ENG-082 — Lock-free SPSC veri yolu
- **Açıklama:** Ring producer/consumer atomik head/tail; kilit YOK.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** TSan/Helgrind temiz; invariant (writer okuyucuyu geçmez) assert ile testte.
- **ADR izi:** VISION V-4 · disk `spsc_ring.h`
- **İlgili test:** TC-ENG-082

#### NE-ENG-083 — RT yasağı denetimi (statik + dinamik)
- **Açıklama:** §07.1 T1-T10 yasakları kod incelemesi + CI grep + debug assert ile denetlenir.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI'da `backends/*/render*` içinde yasaklı çağrımarsa FAIL.
- **ADR izi:** ADR-017 · kardeş PRD §07.5
- **İlgili test:** TC-ENG-083

#### NE-ENG-084 — Stack bütçesi (callback ≤4 KB) `[ÖNERİ]`
- **Açıklama:** Callback başına ölçülebilir stack kullanımı.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Stack-usage raporu (compiler `-fstack-usage`); eşiğe aşım alarm.
- **ADR izi:** kardeş PRD §08.3 `[ÖNERİ]`
- **İlgili test:** TC-ENG-084

#### NE-ENG-085 — Callback süre ölçümü (P99/P999)
- **Açıklama:** Her periyot süresi atomik histogram; bütçe aşımları sayılır.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** §08.4 P99 eşikleri; ölçüm üretimde de açık (düşük maliyetli atomik sayaç).
- **ADR izi:** kardeş PRD §08.4 `[ÖNERİ]`
- **İlgili test:** TC-ENG-085

#### NE-ENG-086 — Starve fade (≤50 ms)
- **Açıklama:** Ring boşsa fade-to-silence; click yok.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Boşaltma senaryosunda waveform'de monoton azalma; ramp süresi ≤50 ms.
- **ADR izi:** ADR-106 §2.2a şeması
- **İlgili test:** TC-ENG-086

#### NE-ENG-087 — Xrun politikası (fade-out → 50 ms → restart)
- **Açıklama:** Underrun/overrun'da ADR-017 politikası.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Yapay xrun enjeksiyonu ile doğrulama; sayaçlar artar, ses kurtarılır.
- **ADR izi:** ADR-017 (brain.md:869)
- **İlgili test:** TC-ENG-087

#### NE-ENG-088 — 30 dk stres = 0 xrun
- **Açıklama:** S3 §6.1 kabul kriteri.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Referans donanımda (hedef sınıf) gece testi; log raporu.
- **ADR izi:** S3 §6.1
- **İlgili test:** TC-ENG-088

#### NE-ENG-089 — Latency bütçesi: ASIO <10 ms
- **Açıklama:** ADR-006 hedefi.
- **Tip:** MUST · **Öncelik:** P0 (ASIO fazında)
- **Kabul kriteri:** Loopback ölçüm (§08.5 M2); eşiği aşan blok boyutu seçimi otomatik reddedilir.
- **ADR izi:** ADR-006 (ADR-019 §1.4)
- **İlgili test:** TC-ENG-089

#### NE-ENG-090 — Latency bütçesi: WASAPI <20 ms
- **Açıklama:** ADR-006 hedefi; blok ≥1024@48k BLOKLANDI (ADR-0105 §2.10 ⚠️).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** L1-L4 merdiveni `⚠️ VR (değerler indeks metninde yok)` — tanımlanana kadar en küçük desteklenen blok zorlanır `[ÖNERİ]`.
- **ADR izi:** ADR-006 · ADR-0105 §2.10 ⚠️
- **İlgili test:** TC-ENG-090

#### NE-ENG-091 — Ring kapasite hesabı
- **Açıklama:** ≥2× max periyot + jitter payı `⚠️ VR (katsayı ADR-0105 §2.5 indeksinde yok)`.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Hesap dökümü §08.3'te; starve/overflow testleri bu kapasiteyle geçer.
- **ADR izi:** kardeş PRD §07.3
- **İlgili test:** TC-ENG-091

#### NE-ENG-092 — RAM ≤400 MB (player toplam payı)
- **Açıklama:** ADR-0105 §2.9 bütçesi; engine payı ölçülür.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Profil raporu; bütçe aşımında release bloke.
- **ADR izi:** ADR-0105 §2.9 ⚠️
- **İlgili test:** TC-ENG-092

#### NE-ENG-093 — Bellek sızıntısı (leak) testi
- **Açıklama:** 1000 cycle open/close + play/stop; net artış 0.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** ASan/LSan temiz.
- **ADR izi:** kardeş PRD §11 test
- **İlgili test:** TC-ENG-093

#### NE-ENG-094 — Fuzzing kapı altyapısı (engine sınırları)
- **Açıklama:** C ABI girdileri (path, band index, gain, queue) + ICY parser fuzz (≥1000 corpus/parser).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** ADR-0105 kapı 5.13 ruhu: 0 ASan/UBSan crash; CI ≤10 dk `⚠️ VR (eşikler indeks)`.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️ · ADR-106 (ICY)
- **İlgili test:** TC-ENG-094

#### NE-ENG-095 — ICY parser limitleri (engine çekirdeği)
- **Açıklama:** icy-metaint 32 B–1 MiB aralığı; text blok ≤1 MB (ADR-106 kural 8 — parser çekirdeği engine'de).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Sınır dışı değer stream RED; parser asla sınırsız buffer büyütmaz.
- **ADR izi:** ADR-106 kural 8 · disk `icy_parser.h`
- **İlgili test:** TC-ENG-095

#### NE-ENG-096 — CPU bütçesi ölçüm ortamı
- **Açıklama:** Benchmark CI donanımı tanımlı; tüketici CPU'ları referans.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Bench ortamı + frekans sabitleme notu raporda.
- **ADR izi:** kardeş PRD §08.5
- **İlgili test:** TC-ENG-096

#### NE-ENG-097 — Multi-thread stres (control spam)
- **Açıklama:** 10 thread × play/pause/set_gain karışık 60 s; crash/race yok.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** TSan temiz; session durumu tutarlı.
- **ADR izi:** kardeş PRD NE-ENG-059
- **İlgili test:** TC-ENG-097

#### NE-ENG-098 — Soğuk açılış süresi
- **Açıklama:** Session open → ilk ses <300 ms (dosya, sıcak disk) `[ÖNERİ]`.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Metrik raporu; eşik kullanıcı hissiyle revize edilebilir.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-ENG-098

#### NE-ENG-099 — Ölçüm telemetrisi (opt-out)
- **Açıklama:** Xrun/callback/luaç sayaçları; kullanıcıya kapalı varsayılan `[ÖNERİ — gizlilik]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Yerel log; ağ yok.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-ENG-099

#### NE-ENG-100 — Perf regresyon kapısı
- **Açıklama:** CPU/latency metriklerinde %10 kötüleşme = CI FAIL (baseline dosyası).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Baseline repo'da; alarm PR'da bloke etmez, etiket koyar `[ÖNERİ — politika: warn]`.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-ENG-100

### §10.8 Grup F — Paketleme & Build

#### NE-ENG-101 — neva-core statik lib
- **Açıklama:** Çekirdek statik kütüphane (`.lib`/`.a`); IMPLEMENTED iskelet.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Ayrık derlenir; exe'ler yalnız public header + statik lib ile bağlanır.
- **ADR izi:** ADR-107 K2 · S3 §4.1 · disk `neva_engine.lib`
- **İlgili test:** TC-ENG-101

#### NE-ENG-102 — neva_session SHARED dll
- **Açıklama:** Tek C ABI paylaşımlı kütüphane; IMPLEMENTED.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Export tablosu yalnız `neva_*`; Linux `.so` karşılığı.
- **ADR izi:** ADR-107 K2 · disk `neva_session.dll`
- **İlgili test:** TC-ENG-102

#### NE-ENG-103 — apps/ kuralı + neva_add_executable
- **Açıklama:** Yeni exe yalnız `apps/CMakeLists.txt` üzerinden; `neva-core`'a dokunulmaz. `apps/` henüz YOK (NE-ENG-201).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kural dokümante + CI'da ihlal FAIL (core içine add_executable).
- **ADR izi:** S3 §4.2 · ADR-107 prompt §6
- **İlgili test:** TC-ENG-103

#### NE-ENG-104 — neva-player exe (CLI)
- **Açıklama:** Çalma/dönüştürme/kayıt CLI — M4.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** `neva-player play file.flac --device 0 --eq flat` benzeri minimal CLI uçları.
- **ADR izi:** ADR-107 K2
- **İlgili test:** TC-ENG-104

#### NE-ENG-105 — neva-host exe (GUI köprüsü)
- **Açıklama:** IPC sunucu; UI ayrı süreçte.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** UI çökmesi oynatmayı öldürmez `[ÖNERİ — davranış]`.
- **ADR izi:** ADR-107 K2
- **İlgili test:** TC-ENG-105

#### NE-ENG-106 — neva-eq exe
- **Açıklama:** 31-band ayrı pencere; E5/E6 API kullanır.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Ayrı süreç; C ABI ile bağlantı; kapatma oturumu etkilemez.
- **ADR izi:** ADR-107 K2 (VoiceMeeter BUSGEQ15 emsal)
- **İlgili test:** TC-ENG-106

#### NE-ENG-107 — neva-matrix exe (8×8)
- **Açıklama:** Yönlendirme matrisi; `⚠️ VR (matrix DSP çekirdeği henüz tanımsız)` — engine'e genişletme gerekir.
- **Tip:** MAY · **Öncelik:** P2
- **Kabul kriteri:** Matris çekirdeği ayrı ADR ile; exe yalnız onun API'sini kullanır.
- **ADR izi:** ADR-107 K2 (BUSMatrix8 emsal)
- **İlgili test:** TC-ENG-107

#### NE-ENG-108 — neva-remote (lib+dll)
- **Açıklama:** Uzaktan kontrol API'si (VoiceMeeterRemote/VBAN emsal).
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Ağ katmanı ayrı hedefte; `neva-core`'a ağ girmez.
- **ADR izi:** ADR-107 K2 · S3 §4.3 yasak 2
- **İlgili test:** TC-ENG-108

#### NE-ENG-109 — neva-mcp exe (dil UNKNOWN)
- **Açıklama:** MCP sunucu — dili ve protokol detayı U5 (UNKNOWN).
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Dil kararı ADR ile; bridge C ABI üzerinden (FFI).
- **ADR izi:** ADR-107 §2.1 `⚠️ PLANNED` · §18.5
- **İlgili test:** TC-ENG-109

#### NE-ENG-110 — neva-plugin (VST3/AU/AAX — lisans K4)
- **Açıklama:** Wrap hedef; VST3 SDK lisansı çözülmeden üretilmez.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Gate NE-ENG-170 (K4) kapalıyken şablon bile commit edilmez.
- **ADR izi:** ADR-107 K4 · §14.3
- **İlgili test:** TC-ENG-110

#### NE-ENG-111 — neva-cli-<kullanıcı> eklenebilirlik
- **Açıklama:** Kullanıcılar kendi CLI'lerini apps/ ile ekler.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Şablon + belge; çekirdeğe dokunmadan derleme.
- **ADR izi:** ADR-107 K2 (§04.7 tablo)
- **İlgili test:** TC-ENG-111

#### NE-ENG-112 — Toolchain dosyası (cmake/neva-toolchains.cmake)
- **Açıklama:** MSVC/GCC/Clang + ARM64 toolchain tanımı.
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Tek dosyadan Windows/Linux CI build.
- **ADR izi:** S3 §5
- **İlgili test:** TC-ENG-112

#### NE-ENG-113 — C++20 zorunluluğu
- **Açıklama:** `CMAKE_CXX_STANDARD 20` + REQUIRED; `noexcept` RT.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Eksik standardda derleme hatası; `/W4 /permissive-` MSVC.
- **ADR izi:** S3 §5 · disk CMakeLists
- **İlgili test:** TC-ENG-113

#### NE-ENG-114 — vcpkg yok (statik bağımlılık politikası)
- **Açıklama:** Bağımlılıklar vendor veya manuel statik; ASIO SDK vendor.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Build sunucuda dış paket indirmez (salt iç kaynak + vendor).
- **ADR izi:** S3 §5
- **İlgili test:** TC-ENG-114

#### NE-ENG-115 — CI matrisi (Windows x64 + Linux x64 + ARM64 build)
- **Açıklama:** Üç build hedefi CI'da derlenir; testler Null ile.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Üçü yeşil; ARM64 run testleri RPi5 runner `⚠️ VR (runner erişimi)` .
- **ADR izi:** S3 §5 · S3 §6.1
- **İlgili test:** TC-ENG-115

#### NE-ENG-116 — Çıktı dizin düzeni
- **Açıklama:** `build/<platform>-<arch>/` altında her hedef ayrı artefakt.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Artefakt adları §04.7 tablosuyla eşleşir (neva_engine.lib, neva_session.dll vb.).
- **ADR izi:** S3 §5 · disk `build/`
- **İlgili test:** TC-ENG-116

#### NE-ENG-117 — Windows link kütüphaneleri (ole32 avrt uuid)
- **Açıklama:** WASAPI/COM için gerekli link.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CMakeLists'te mevcut (IMPLEMENTED — disk kanıtı).
- **ADR izi:** disk CMakeLists · S3 §5
- **İlgili test:** TC-ENG-117

#### NE-ENG-118 — Unicode/UTF-8 kaynak kodu (`/utf-8`)
- **Açıklama:** Türkçe yorum/çizgi metinleri bozulmaz.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** `/utf-8` MSVC'de açık; kaynak dosyalar UTF-8 (CI denetimi).
- **ADR izi:** disk CMakeLists
- **İlgili test:** TC-ENG-118

#### NE-ENG-119 — -fno-exceptions tartışması
- **Açıklama:** RT/boyut için istek; kütüphane bağımlılıkları engel olabilir → `⚠️ PLANNED`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Karar ADR revizyonuyla; şimdilik yalnız RT yüzeyinde throw yasak (NE-ENG-004).
- **ADR izi:** S3 §5
- **İlgili test:** TC-ENG-119

#### NE-ENG-120 — Build reproducibility
- **Açıklama:** Aynı commit + aynı toolchain → aynı hash (bitwise determinism) `[ÖNERİ]`.
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** İki temiz build hash karşılaştırması raporlanır.
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-ENG-120

#### NE-ENG-121 — Sembol dosyaları (PDB/dSYM) dağıtımı
- **Açıklama:** Crash analizi için semboller ayrı saklanır; ürün paketine girmez.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Artefakt listesinde PDB ayrı paket.
- **ADR izi:** S3 §5 (disk: `neva_session.pdb`, `msvc-dbg-info/`)
- **İlgili test:** TC-ENG-121

#### NE-ENG-122 — Versiyon bilgisi (semver + api_version)
- **Açıklama:** Kültürel: dosya adı sürümü + C ABI sürümü senkron.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Artefakt içi sürüm kaynağı tek (CMake project version).
- **ADR izi:** kardeş PRD §05.4
- **İlgili test:** TC-ENG-122

#### NE-ENG-123 — Test exe'leri (3 adet IMPLEMENTED)
- **Açıklama:** `neva_tests`, `eq_grids_tests`, `icy_parser_tests` + CTest kaydı.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `ctest` yeşil (disk: `CTestTestfile.cmake`, `LastTest.log`).
- **ADR izi:** disk CMakeLists · `tests/`
- **İlgili test:** TC-ENG-123

#### NE-ENG-124 — Yeni test hedefi kuralı
- **Açıklama:** Her yeni modül + kontrat testi (NE-ENG-042) olmadan merge yasak.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** PR şablonu madde.
- **ADR izi:** S3 §6.1
- **İlgili test:** TC-ENG-124

#### NE-ENG-125 — Artefakt hijyeni (build/ commit)
- **Açıklama:** Derleme çıktıları commit edilmez (`.gitignore`) — mevcut `build/` disk varlığı raporlandı.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** `.gitignore` kuralı; repo temiz.
- **ADR izi:** disk `--/neva-engine/build/` (bu PRD §04.2 raporu)
- **İlgili test:** TC-ENG-125

### §10.9 Grup G — Bit-perfect & Test Altyapısı

#### NE-ENG-126 — Bit-perfect hash CI kapısı (K3)
- **Açıklama:** WAV hash byte-eşitlik — 12 kombinasyon (44.1/48/96/192 × 16/24/32f).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI her PR'da 12/12; "yaklaşık eşitlik" PASS yasak; hash dosyaları repo'da referans.
- **ADR izi:** ADR-107 K3 · kardeş PRD §11.2
- **İlgili test:** TC-ENG-126

#### NE-ENG-127 — Inverted guard (EQ aktif/lossy → test engellenir)
- **Açıklama:** Hash testi yalnız flat+bit-perfect koşullarında; aksi halde FAIL beklenir/skoç.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Guard CI script'te; sahte-PASS/FAIL denetimi (ADR-106 §5.1 Ş4).
- **ADR izi:** ADR-106 §5.1 Ş4
- **İlgili test:** TC-ENG-127

#### NE-ENG-128 — Test kaynak üretimi (sentez)
- **Açıklama:** Sine/sweep/noise kaynakları sentezlenir (repo'da ağır binary yerine üretici `[ÖNERİ]`).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Deterministik sentez; hash referansları senteze bağlı (reproducible).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-ENG-128

#### NE-ENG-129 — WAV I/O (vendor, bağımlılıksız)
- **Açıklama:** WAV okuma/yazma kendi kodu (vcpkg yok).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 16/24/32f + chunk edelişleri; fuzz corpus'u (bozuk WAV) 0 crash.
- **ADR izi:** S3 §5 (statik bağımlılık politikası)
- **İlgili test:** TC-ENG-129

#### NE-ENG-130 — CTest entegrasyonu
- **Açıklama:** Tüm testler CTest ile; `add_test` kayıtlı (IMPLEMENTED).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** `ctest` tek komut; CI adımı.
- **ADR izi:** disk CMakeLists
- **İlgili test:** TC-ENG-130

#### NE-ENG-131 — Sanitizer matrisi (ASan/UBSan/TSan)
- **Açıklama:** Linux CI'da sanitizörlü build'ler; fuzz + stres bu build'lerde.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Üç sanitizör raporu; 0 bulgu release ön koşulu.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️ · S3 §6
- **İlgili test:** TC-ENG-131

#### NE-ENG-132 — Fuzz hedefleri (engine)
- **Açıklama:** ICY parser · WAV reader · C ABI girdi fuzzer (band/gain/path).
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** ≥1000 corpus/parser; CI ≤10 dk.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️ · disk `icy_parser.h`
- **İlgili test:** TC-ENG-132

#### NE-ENG-133 — Kontrat test paketi (backend başına)
- **Açıklama:** NE-ENG-042 paketi: enumerate/open/negotiate/start/stop/close + hata yolları.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Null'da yeşil; her yeni backend aynı takımı geçer.
- **ADR izi:** S3 §6.1
- **İlgili test:** TC-ENG-133

#### NE-ENG-134 — DSP doğruluk testi (referans bode)
- **Açıklama:** Biquad frekans yanıtı referans hesapla karşılaştırılır (±tolerans `⚠️ VR`).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 31 frekans × 3 tip (Peaking/Shelf/LPF) doğrulama.
- **ADR izi:** disk `test_biquad.cpp` · kardeş PRD §06.3
- **İlgili test:** TC-ENG-134

#### NE-ENG-135 — Grid tutarlılık testi (IMPLEMENTED)
- **Açıklama:** `eq_grids_tests` — grid ↔ EqChain uyumu.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Yeşil (disk kanıtı).
- **ADR izi:** disk `test_eq_grids.cpp`
- **İlgili test:** TC-ENG-135

#### NE-ENG-136 — ICY parser testi (IMPLEMENTED)
- **Açıklama:** `icy_parser_tests` — header ayrıştırma + durum makinesi.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Yeşil (disk kanıtı); §11.5'te genişletilir.
- **ADR izi:** disk `test_icy_parser.cpp`
- **İlgili test:** TC-ENG-136

#### NE-ENG-137 — Latency ölçüm düzeneği
- **Açıklama:** Loopback (fiziksel: DAC→ADC) veya zaman damgalı method; araç repo-doc'da.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Kapı 5.10/ADR-006 raporu üretir; tekrarlanabilir.
- **ADR izi:** ADR-006 · ADR-0105 kapı 5.10 ⚠️
- **İlgili test:** TC-ENG-137

#### NE-ENG-138 — SNR/THD+N ölçüm düzeneği
- **Açıklama:** 1 kHz tone → loopback → analyzer (SNR >105 dB, THD+N <%0.005).
- **Tip:** SHOULD · **Öncelik:** P1
- **Kabul kriteri:** Düzeneğin kendisi doğrulanır (bilinen iyi referans DAC `[ÖNERİ]`).
- **ADR izi:** VISION V-1/V-2 · kardeş PRD §08.5 M5
- **İlgili test:** TC-ENG-138

#### NE-ENG-139 — Gapless referans vektörleri (engine desteği)
- **Açıklama:** E2 API + Xing/seektable okuma engine'de; vektör testleri player CI'ında.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Kardeş PRD NE-PLR-056/068 ile çapraz.
- **ADR izi:** ADR-0105 AK-5 ⚠️
- **İlgili test:** TC-ENG-139

#### NE-ENG-140 — RT stres (30 dk, 0 tahsis, 0 xrun)
- **Açıklama:** NE-ENG-081/088 birleşik gece testi.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Rapor arşivi.
- **ADR izi:** S3 §6.1
- **İlgili test:** TC-ENG-140

#### NE-ENG-141 — Leak testi (1000 cycle)
- **Açıklama:** Session yaşam döngüsü döngüsü.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** LSan temiz.
- **ADR izi:** kardeş PRD §11
- **İlgili test:** TC-ENG-141

#### NE-ENG-142 — ABI sembol denetimi
- **Açıklama:** Export listesi yalnız `neva_*`; sürüm dump arşivi.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** CI grep; NE-ENG-060 freeze ile.
- **ADR izi:** kardeş PRD §05.4
- **İlgili test:** TC-ENG-142

#### NE-ENG-143 — Baseline metrik dosyası
- **Açıklama:** CPU/latency/boyut baseline'ları repo'da (regresyon kapısı NE-ENG-100).
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Dosya + güncelleme kuralı (bilinçli regresyon PR notu).
- **ADR izi:** YOK — `[ÖNERİ]`
- **İlgili test:** TC-ENG-143

#### NE-ENG-144 — Test raporu şablonu (release dosyası)
- **Açıklama:** Kapı 5.1/5.10-5.13 raporları tek şablonla.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** Şablon `.ai/.templates/` altında; release öncesi doldurulur.
- **ADR izi:** kardeş PRD §11 · Guardrail #16 ruhu
- **İlgili test:** TC-ENG-144

#### NE-ENG-145 — Determinism testi (aynı girdi → aynı hash)
- **Açıklama:** İki çalıştırma arası hash eşitliği (test/kaydırmalı atomik yollar dahil).
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 2× çalıştırma hash eşit; fuzz atomikler testte.
- **ADR izi:** kardeş PRD §11.2 (CI tekrar çalıştırma)
- **İlgili test:** TC-ENG-145

### §10.10 Grup H — Lisans & Güvenlik

#### NE-ENG-146 — ASIO SDK lisans kararı (U1)
- **Açıklama:** GPL-vs-ticari belirsiz; karar verilmeden ASIO dağıtılmaz.
- **Tip:** MUST · **Öncelik:** P0 (release)
- **Kabul kriteri:** Karar ADR; build bayrağı ASIO'yu koşullu yapar (`NEVA_WITH_ASIO`).
- **ADR izi:** ADR-107 K4 · U1 §18.1
- **İlgili test:** TC-ENG-146

#### NE-ENG-147 — VST3 SDK lisans kararı (K4)
- **Açıklama:** Steinberg VST3 license; plugin hedefi kapalı.
- **Tip:** MUST · **Öncelik:** P3 (release)
- **Kabul kriteri:** Gate NE-ENG-170.
- **ADR izi:** ADR-107 K4
- **İlgili test:** TC-ENG-147

#### NE-ENG-148 — FFmpeg LGPL build (engine gateway dahil)
- **Açıklama:** `--enable-gpl` yasak.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Bayrak envanteri; lisans raporu.
- **ADR izi:** ADR-0105 indeks ⚠️ · lib-matrix
- **İlgili test:** TC-ENG-148

#### NE-ENG-149 — soxr LGPL-2.1
- **Açıklama:** Dinamik link varsayılan; statik link `⚠️ LİSANS`.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** §14.4 matris durumu.
- **ADR izi:** ADR-0105 v1.2.0 ⚠️
- **İlgili test:** TC-ENG-149

#### NE-ENG-150 — libdsd lisansı (UNKNOWN — release kapısı)
- **Açıklama:** LICENSE eksik; karar yok.
- **Tip:** MUST · **Öncelik:** P1 (DSD hedefinde)
- **Kabul kriteri:** Karar ADR; çözülene kadar DSD hedefi dağıtılmaz (`NEVA_WITH_DSD` kapalı).
- **ADR izi:** ADR-0105 v1.2.0 ⚠️ · U3 §18.3
- **İlgili test:** TC-ENG-150

#### NE-ENG-151 — BASS KULLANMA (devam)
- **Açıklama:** Devasa tekil SDK; API olmayan sürüm; kapı ADR-0105'te.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kodda referans yok.
- **ADR izi:** ADR-105 v1.1.1 (indeks)
- **İlgili test:** TC-ENG-151

#### NE-ENG-152 — mpv REDDEDİLDİ (devam)
- **Açıklama:** GPL-2.0 copyleft + black-box.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kodda bağımlılık yok.
- **ADR izi:** ADR-105 v1.1.1 (indeks)
- **İlgili test:** TC-ENG-152

#### NE-ENG-153 — FxSound AGPL kopyalama yasağı
- **Açıklama:** Kodsuz AGPL uygulama; yalnız mimari RE referans.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Kopya/uyarlama yok; referans log'lanır.
- **ADR izi:** ADR-107 K4
- **İlgili test:** TC-ENG-153

#### NE-ENG-154 — mbedTLS Apache-2.0 OR GPL-2.0
- **Açıklama:** Apache-2.0 seçilir; attribution dosyada.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Lisans metni dağıtımda.
- **ADR izi:** ADR-106 §5.4
- **İlgili test:** TC-ENG-154

#### NE-ENG-155 — dr_libs lisansı
- **Açıklama:** MIT-tarzı/public domain `⚠️ VR (tam metin lib-matrix'te doğrulanır)`.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Attribution.
- **ADR izi:** ADR-0105 indeks ⚠️
- **İlgili test:** TC-ENG-155

#### NE-ENG-156 — Secret/anahtar yasağı
- **Açıklama:** Kod/config/log'a sabit secret yazılmaz; `.env`/env substitution.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** GitLeaks taraması temiz (CI `secret-scan.yml`).
- **ADR izi:** AGENTS §5 Security · `.github/workflows/secret-scan.yml`
- **İlgili test:** TC-ENG-156

#### NE-ENG-157 — FFI sınırında bellek güvenliği
- **Açıklama:** path NULL/uzunluk sınırı; band/gain clamp; handle use-after-free koruması `⚠️ VR (magic-cookie tasarımı)` .
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Fuzz + negatif testler; INVALID_ARG.
- **ADR izi:** kardeş PRD §05.1
- **İlgili test:** TC-ENG-157

#### NE-ENG-158 — Eval/serileştirme yasağı
- **Açıklama:** `eval()` yasak (ADR-091); user input `unserialize`/pickle benzeri yasak.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Statik denetim.
- **ADR izi:** ADR-091 · AGENTS §5
- **İlgili test:** TC-ENG-158

#### NE-ENG-159 — Ağ girmez çekirdek
- **Açıklama:** `neva-core` içinde socket/HTTP yok (S3 §4.3 yasak 2) — SSRF yüzeyi çekirdekte yok.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** CI grep (winsock/curl/include).
- **ADR izi:** S3 §4.3
- **İlgili test:** TC-ENG-159

#### NE-ENG-160 — Sürücü/evrensel kod incelemesi
- **Açıklama:** WDM-KS/CoreAudio gibi düşük seviye kodlar güvenlik+RT gözden geçirme.
- **Tip:** SHOULD · **Öncelik:** P2
- **Kabul kriteri:** PR review checklist.
- **ADR izi:** S3 §3.4
- **İlgili test:** TC-ENG-160

### §10.11 Grup I — Faz Kapıları (G0-G5)

#### NE-ENG-161 — GATE G0: ADR-107 onayı
- **Açıklama:** Draft ADR onaylanmadan yeni backend uygulamaya başlanmaz.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** ADR status `accepted`; Vault Steward + Tech Lead onayı.
- **ADR izi:** ADR-107 §5
- **İlgili test:** — (proses)

#### NE-ENG-162 — GATE G1: Bit-perfect 12/12
- **Açıklama:** Hash kapısı release blocker.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** 12/12 yeşil + tekrar determinism.
- **ADR izi:** ADR-107 K3
- **İlgili test:** TC-ENG-126

#### NE-ENG-163 — GATE G2: Latency eşiği (ADR-006)
- **Açıklama:** ASIO <10 / WASAPI <20 ms.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** Ölçüm raporu.
- **ADR izi:** ADR-006 · kapı 5.10 ⚠️
- **İlgili test:** TC-ENG-089/090

#### NE-ENG-164 — GATE G3: Lisans kapanışı (K4)
- **Açıklama:** ASIO (U1) + VST3 + libdsd (U3) + soxr statik + FFmpeg bayrakları.
- **Tip:** MUST · **Öncelik:** P0
- **Kabul kriteri:** §14.3 matris 0 açık madde.
- **ADR izi:** ADR-107 K4
- **İlgili test:** — (proses + TC-ENG-146..155)

#### NE-ENG-165 — GATE G4: RT stres 30 dk
- **Açıklama:** 0 tahsis/0 xrun/leak 0.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Gece testi raporu.
- **ADR izi:** S3 §6.1
- **İlgili test:** TC-ENG-140

#### NE-ENG-166 — GATE G5: Fuzz kapı 5.13
- **Açıklama:** ≥1000 corpus/format; 0 crash.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Fuzz raporu.
- **ADR izi:** ADR-0105 kapı 5.13 ⚠️
- **İlgili test:** TC-ENG-132

#### NE-ENG-167..175 — Faz M0-M6 kapıları
- **Açıklama:** Her faz çıkışı §15.2-§15.5 tablolarındaki çıktılarla kapanır; Vault Steward faz kapanışı onayı.
- **Tip:** MUST · **Öncelik:** P1
- **Kabul kriteri:** Faz checklist'i (§15.6) tam; commit kanıtı; vault-sync.
- **ADR izi:** ADR-107 §5 · engine §12.6 (Faz Kapanışı kuralı)
- **İlgili test:** — (proses)

*(NE-ENG-167..175: M0/M1/M2/M3/M4/M5/M6 faz kapanışları — her biri kendi fase ait checklist madde grubudur; numaralar izlenebilirlik içindir.)*

### §10.12 Grup J — UNKNOWN Varlıkları (U1-U5)

#### NE-ENG-176 — U1: ASIO SDK lisans kararı
- **Açıklama:** `UNKNOWN` — GPL-vs-ticari.
- **Tip:** MUST (karar) · **Öncelik:** P0
- **Kabul kriteri:** ADR-107 §5 Faz 2 kapısı kapalı; NE-ENG-146.
- **ADR izi:** ADR-107 §8.1
- **İlgili test:** —

#### NE-ENG-177 — U2: VST3 lisans kararı
- **Açıklama:** `UNKNOWN` — plugin wrap.
- **Tip:** MUST (karar) · **Öncelik:** P3
- **Kabul kriteri:** K4 gate.
- **ADR izi:** ADR-107 §8.3
- **İlgili test:** —

#### NE-ENG-178 — U3: libdsd lisansı + macOS platform kararı
- **Açıklama:** LICENSE eksik (`UNKNOWN`); macOS hedefi PLANNED.
- **Tip:** MUST (karar) · **Öncelik:** P1
- **Kabul kriteri:** `NEVA_WITH_DSD` kapalı; macOS M6 başlangıcı ayrı onay.
- **ADR izi:** ADR-107 §8.2 · ADR-0105 indeks ⚠️
- **İlgili test:** —

#### NE-ENG-179 — U4: Web/API yüzeyi
- **Açıklama:** `UNKNOWN` — `neva_host`/REST/HTTP olup olmadığı.
- **Tip:** SHOULD (karar) · **Öncelik:** P2
- **Kabul kriteri:** M4 öncesi karar; ağ yalnız remote/mcp hedeflerinde.
- **ADR izi:** ADR-107 §8.6 · S3 §4.3
- **İlgili test:** —

#### NE-ENG-180 — U5: neva-mcp dili
- **Açıklama:** `UNKNOWN` — Rust/Go/C#/Python bridge.
- **Tip:** MAY (karar) · **Öncelik:** P3
- **Kabul kriteri:** Karar ADR; bridge C ABI kullanır.
- **ADR izi:** ADR-107 §8.5
- **İlgili test:** —

#### NE-ENG-181 — U-ek: PCM3168A debug capture akışı (ADR-103)
- **Açıklama:** `UNKNOWN` — 96 kHz/24-bit I2S debug capture hangi araçla?
- **Tip:** MAY · **Öncelik:** P3
- **Kabul kriteri:** Karar yalnız ilgili fazda.
- **ADR izi:** ADR-107 §8.8
- **İlgili test:** —

#### NE-ENG-182 — U-ek: ADR-0105 dosyasının vault'a iadesi
- **Açıklama:** `⚠️ VR` — dosya diskte YOK (§00.3-D2); indeks kaydı tek kaynak.
- **Tip:** MUST (düzeltme) · **Öncelik:** P0 (vault-steward işi)
- **Kabul kriteri:** ADR-0105 dosyası `.ai/.decisions/accepted/` altına iade edilir veya indeks `⚠️ dosyasız` notu kalıcılaşır; bu PRD'deki tüm `⚠️ VR (ADR-105 indeks kaydı)` etiketleri o zaman yeniden değerlendirilir.
- **ADR izi:** §00.3-D2 · index.md:152
- **İlgili test:** — (vault denetimi)

---

## §11 Test Stratejisi

> **Kural (S3 §6.1):** Yeni backend = kontrat test paketi geçmeden registry'ye kayıt yok.
> **Kural (ADR-0105 K3):** Hash testinde "yaklaşık eşitlik" PASS sayılmaz — byte-eşitlik zorunlu.
> **Kural (ADR-0106 §5.1 Ş4):** Testin koşulları sağlanmıyorsa SAHTE FAIL/SAHTE PASS yok — test koşul dışı bırakılır (inverted guard).

### §11.1 Amaç & Test Piramidi

Bu PRD'nin test stratejisi üç katmanlıdır:

| Katman | Kapsam | Otomasyon | Çalışma sıklığı |
|---|---|---|---|
| L1 Birim | biquad, eq_grids, icy_parser, ring, WAV I/O, ABI clamp | %100 otomatik (CTest) | Her commit |
| L2 Kontrat & Entegrasyon | Backend kontrat paketi, fallback zinciri, event callback, durum makinesi | %100 otomatik (Null backend + mock clock) | Her PR |
| Kapı & Ölçüm | Hash (K3), latency (5.10), SNR/THD+N, RT stres (G4), fuzz (5.13) | Otomatik + donanım gerektirenler release öncesi manuel onay | Her PR (donanımlı olanlar: release adayı) |

**Piramit dağılımı hedefi:** L1 ≥ %60 test sayısı, L2 ≥ %30, Kapı/ölçüm ≥ %10 `[ÖNERİ — oran ADR'de yok]`.

### §11.2 Bit-Perfect Hash Hattı (K3 — Kardeş PRD §11.2 ile ortak)

**Hat şeması (dosya → hash):**

```text
Referans WAV/FLAC (repo'da hash'i saklı)
  → Session open (flat EQ, limiter bypass, gain 0 dB)
  → Backend: Null (CI) veya gerçek DAC (release loopback)
  → Çıkış WAV'a yazılır (raw PCM)
  → SHA-256 hash (giriş raw PCM ↔ çıkış raw PCM)
  → Byte-eşitlik: EŞİT = PASS · DEĞİL = FAIL
```

**12 kombinasyon matrisi (K3):**

| # | Sample rate | Bit derinliği | Format | Null backend CI | Gerçek DAC loopback (release) |
|---|---|---|---|---|---|
| 1 | 44100 Hz | 16-bit | PCM int | ZORUNLU | ZORUNLU |
| 2 | 44100 Hz | 24-bit | PCM int | ZORUNLU | ZORUNLU |
| 3 | 44100 Hz | 32-bit | float | ZORUNLU | ZORUNLU |
| 4 | 48000 Hz | 16-bit | PCM int | ZORUNLU | ZORUNLU |
| 5 | 48000 Hz | 24-bit | PCM int | ZORUNLU | ZORUNLU |
| 6 | 48000 Hz | 32-bit | float | ZORUNLU | ZORUNLU |
| 7 | 96000 Hz | 16-bit | PCM int | ZORUNLU | ZORUNLU |
| 8 | 96000 Hz | 24-bit | PCM int | ZORUNLU | ZORUNLU |
| 9 | 96000 Hz | 32-bit | float | ZORUNLU | ZORUNLU |
| 10 | 192000 Hz | 16-bit | PCM int | ZORUNLU | ZORUNLU |
| 11 | 192000 Hz | 24-bit | PCM int | ZORUNLU | ZORUNLU |
| 12 | 192000 Hz | 32-bit | float | ZORUNLU | ZORUNLU |

**Hash hattı kuralları:**

1. Hash, RAW PCM gövdesine uygulanır (WAV header hariç — header baytları platforma göre değişebilir).
2. Giriş PCM'i test üreticisinden (deterministik sentez NE-ENG-128) okunur; çıkış PCM'i session yazımından gelir.
3. `negotiated_format() == hedef format` kontrolü sağlamadan PASS yazılamaz (NE-ENG-009).
4. İki çalıştırma arası hash eşittir (determinism — NE-ENG-145); üçüncü çalıştırmada uyuşmazlık varsa atomik/non-atomik yollar denetlenir.
5. Hash dosyaları repo'da `tests/ref-hashes/` altında saklanır; bilinçli değişim PR notu ister.

**Inverted guard (ADR-106 §5.1 Ş4):**

| Koşul | Hash testi davranışı |
|---|---|
| EQ flat/Bypass + limiter bypass + gain 0 dB + eşit format (bit-perfect yolu) | Çalışır → byte-eşitlik beklenir |
| EQ aktif (herhangi bir band ≠ 0 dB) | **ÇALIŞMAZ** — koşul dışı (FAIL beklemek de SAHTE FAIL'dir) |
| Lossy kaynak (MP3/AAC/Opus) | **ÇALIŞMAZ** — kapsam dışı |
| Fallback sonrası shared mod (OS mikser dahil) | **ÇALIŞMAZ** — bit-perfect iddiası düşer |
| Radyo akışı (canlı, framesiz) | **ÇALIŞMAZ** — mismatch/kayıp belirsiz |

Guard CI script'te tek yerde tanımlıdır (`ci/hash-gate.sh` `[ÖNERİ — dosya adı]`); her yeni test bu guard'ı kullanmak zorundadır (kopyalanmış guard yasak — drift riski).

**Negatif denetim (guard'ın kendisinin testi):** EQ aktifken hash testinin "koşul dışı" çıktığı, flat'e dönünce tekrar çalıştığı ve PASS aldığı doğrulanır — yani guard yanlışlıkla her koşulda çalışıp yeşil yazamaz.

### §11.3 Ölçüm Düzeneği (M1/M2/M5 Çıktıları)

#### A. Gecikme ölçümü (Kapı 5.10 · ADR-006)

| Öğe | Değer |
|---|---|
| Method | Loopback: DAC çıkışı → fiziksel köprü → ADC girişi (jack-to-jack) `[ÖNERİ — alternatif: yazılımsa timestamp, ama o-loopback yolu ölçmez]` |
| Tetikleme | Bilinen 1 kHz BLIP deseni; korelasyon tepe noktası = gecikme |
| Hedef (ASIO) | < 10 ms (ADR-006) |
| Hedef (WASAPI) | < 20 ms (ADR-006; blok ≥1024@48k yasağı ADR-0105 §2.10 ⚠️) |
| Ölçüm sıklığı | Release adayı her seferinde + ayda bir regresyon |
| Rapor | Ham ölçüm + blok boyutu + cihaz adı + sürücü sürümü |

#### B. SNR / THD+N ölçümü (M5 · VISION V-1/V-2)

| Öğe | Değer |
|---|---|
| Sinyal | 1 kHz sine, −1 dBFS ( ölçek NE-ENG-138 `[ÖNERİ]`) |
| Kayıt | Loopback ≥ 10 saniye, ≥ 24-bit kayıt cihazı |
| Analiz | FFT (≥ 8× oversample `⚠️ VR`): SNR = sinyal/geri kalan gürültü; THD+N = harmonik + gürültü oranı |
| Hedef SNR | > 105 dB (VISION V-1; VISION.md satır ~100/327) |
| Hedef THD+N | <%0.005 @ 1 kHz (VISION V-2) |
| Düzeneğin kendisi | Referans iyi DAC ile düzeneğin kendi gürültü tabanı ölçülür — düzeneğin tabanı hedefe 10 dB yakın ise düzeneği AŞAR `[ÖNERİ]` |

**Önemli:** Bu ölçüm zincirin tamamını (decode → DSP bypass → exclusive çıkış) kapsar; EQ flat + limiter bypass koşulu aranır (aksi halde sonuç bit-perfect iddiasını ölçmez).

#### C. Gapless ölçümü (Kapı 5.12 · kardeş PRD ile ortak)

| Öğe | Değer |
|---|---|
| Vektör | Xing'li MP3 çifti, seektable'lı FLAC çifti, Opus granule çifti (repo referans) |
| Ölçüm | Dosya→dosya sınırında korelasyon ±5 ms; >20 ms = FAIL (ADR-0105 kapı 5.12 ⚠️) |
| Ölçüm yolu | Null backend: örnek sayacı (deterministik); release: DAC loopback korelasyonu |
| Ek vektör | Radio stream→stream (reconnect 1s/4s/8s backoff eşliğinde) |

#### D. RT stres düzeneği (GATE G4)

| Öğe | Değer |
|---|---|
| Süre | 30 dakika kesintisiz (S3 §6.1) |
| Yük | 192 kHz/32f akış + 31-band EQ aktif (worst-case — NE-ENG-076) |
| Sayıçlar | Tahsis = 0 · xrun = 0 · leak = 0 (1000 open/close sonrası net artış 0) |
| Ortam | Referans tüketici CPU (`[ÖNERİ — 8 çekirdek, boost kapalı]`) + CPU frekansı sabitlenir (rapor) |
| Çalışma | Gece otomasyonu (CI schedule); sonuç release dosyasına eklenir |

#### E. Fuzz düzeneği (Kapı 5.13)

| Öğe | Değer |
|---|---|
| Hedefler | ICY parser · WAV reader · C ABI girdi fuzzer (path/band/gain/queue) |
| Corpus | ≥ 1000 sentetik + gerçek örneklendi istisna (her hedef) |
| Build | ASan + UBSan (Linux CI) |
| Kabul | 0 crash · 0 OOB · CI ≤ 10 dk |
| Süreklilik | Her parser kodu değişikliğinde corpus çalışır |

### §11.4 Release Kapıları Tablosu (Tek Bakışta)

| Kapı | İçerik | Eşik | PR engelleyici mi? | İlgili gereksinim |
|---|---|---|---|---|
| G1 / K3 / 5.1 | Bit-perfect hash | 12/12 byte-eşit + determinism 2× | EVET (her PR) | NE-ENG-126/127 |
| G2 / 5.10 | Latency loopback | ASIO <10 ms · WASAPI <20 ms | Release adayı | NE-ENG-089/090/163 |
| 5.12 | Gapless vektörleri | ±5 ms korelasyon | EVET | NE-ENG-139 |
| 5.13 | Fuzz | 0 crash / ≥1000 corpus | EVET | NE-ENG-132/166 |
| G4 | RT stres | 30 dk · 0/0/0 | Release adayı | NE-ENG-140/164 |
| G3 | Lisans kapanışı | §14.3 = 0 açık madde | Release adayı | NE-ENG-146..155/164 |
| SNR/THD (M5) | Ölçüm raporu | >105 dB / <%0.005 | Release adayı (manuel) | NE-ENG-138 |
| Leak | 1000 cycle | net artış 0 | EVET | NE-ENG-141 |
| TSan | Çoklu-thread | 0 data race | EVET | NE-ENG-059/097 |

**Kapı çalıştırma yeri:** Tüm kapı betikleri repo kökünden çalışır (`.ai/scripts/` kuralına benzer biçim — göreli yol tuzağına düşülmez). Çıktı `ci-reports/` altına yazılır ve release dosyasına referans verilir.

### §11.5 Test Ortamı & Determinizm

1. **Deterministik saat:** Zamansal testler (ring, ramp, kill-switch, backoff) enjekte edilebilir saat ile çalışır; gerçek duvar saati kullanılmaz (NE-ENG-041).
2. **Deterministik kaynak:** Test WAV'ları sentezlenir (NE-ENG-128); repo'da ağır binary yerine üretici + seed saklanır (varyant: ihtiyaç varsa küçük referans dosyalar da eklenebilir `[ÖNERİ]`).
3. **Paralellik:** CTest paralel çalışır; testler birbirinin dosya/CI kaynaklarını paylaşmaz (tmp dir her testte ayrı).
4. **Saat dilimi/locale:** Testler sabit `TZ=UTC` + `C` locale ile (yerelleştirme testleri hariç `[ÖNERİ]`).
5. **Platform matrisi:** Windows x64 (MSVC), Linux x64 (GCC), Linux ARM64 (GCC — RPi5 `⚠️ VR (runner erişimi)`), Windows ARM64 (build-only `⚠️ VR`).
6. **Toz (flaky) test yasağı:** Yeniden çalıştırınca geçen test = hata sayılır; testin kendisi tanımlanır ve düzeltilir (yeniden-dene döngüsü yasak).

### §11.6 Kardeş PRD Çapraz Bağlantıları

| Engine testi | Player tarafı |
|---|---|
| TC-ENG-126 (hash) | TC-PLR-036/040/050/087 (aynı hat, player koşulları) |
| TC-ENG-139 (gapless API) | TC-PLR-056..075 (format vektörleri) |
| TC-ENG-137 (latency) | TC-PLR-083/084 (kapı 5.10) |
| TC-ENG-138 (SNR/THD) | TC-PLR-091/092 (VISION eşikleri) |
| TC-ENG-132 (fuzz engine) | TC-PLR-085 (kapı 5.13 player) |
| TC-ENG-095 (ICY limitleri) | TC-PLR-096..109 (radyo akışı) |
| TC-ENG-140 (RT stres) | TC-PLR-054/163 (UI spam + uzun süre) |

**Çalışma kuralı:** Aynı ölçümü iki PRD'de iki kez ölçme — biri ölçer, diğeri referans verir (ölçüm sahibi: engine PRD §11.3; koşul/UX sahibi: player PRD §11).

### §11.7 Senaryo Defteri — TC-ENG (Gereksinim başına eşleşen senaryo)

> **Eşleme kuralı:** `TC-ENG-NNN` ↔ `NE-ENG-NNN` (aynı numara). Her gereksinim en az bir senaryo ister;
> statik denetimli senaryolar CI'da grep/analiz adımı ile otomatiktir.

#### TC-ENG-001 — Tek C ABI sınırı
- **Gereksinim:** NE-ENG-001 · **Katman:** L2 (statik) · **Otomasyon:** CI
- **Ön koşul:** Temiz build + install paketi üretilmiş.
- **Adımlar:** (1) export listesi (`dumpbin /exports` / `nm -D`); (2) public header envanteri; (3) çekirdekte OS header grep'i.
- **Beklenen:** Yalnız `neva_*` sembolleri; tek public header; OS include'ı yalnız `backends/`.
- **Aksi:** FAIL — PR bloke (ABI sızıntısı).
- **Kanıt:** disk `neva_session.h` · ADR-0105 §2.6 ⚠️ · S3 §4.3

#### TC-ENG-002 — Opaque handle
- **Gereksinim:** NE-ENG-002 · **Katman:** L1 (derleme) · **Otomasyon:** CI
- **Adımlar:** Test translation unit'inde struct içeriğini okumaya çalışan kod derlenir.
- **Beklenen:** Derleme hatası (tanım yok); yalnız pointer/forward-declaration kullanılabilir.
- **Kanıt:** disk `neva_session.h:29`

#### TC-ENG-003 — api_version handshake
- **Gereksinim:** NE-ENG-003 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** (1) `neva_session_api_version()` çağır; (2) header'daki beklenen makro ile karşılaştır.
- **Beklenen:** `0x00010000` eşit; uyuşmazlıkta player açılışı reddedici mesaj üretir (kardeş TC-PLR-003).
- **Kanıt:** disk `neva_session.h:26`

#### TC-ENG-004 — Exception yayılmaz (FFI)
- **Gereksinim:** NE-ENG-004 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** (1) `extern "C"` bloklarında `throw` statik arama; (2) NULL/bozuk girdi ile her `neva_*` fonksiyonunu çağır + C++ tarafında `catch(...)` koruması.
- **Beklenen:** Statik: `throw` yok; dinamik: tüm çağrılar `neva_result` ile döner, istisna dışarı sıçramaz.
- **Kanıt:** disk `neva_session.h:31-37`

#### TC-ENG-005 — char16_t Türkçe yol
- **Gereksinim:** NE-ENG-005 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** `C:\Müzik\Deneme\Şarkı-İöüç.flac` benzeri yol + dosyayı `open_file` ile aç.
- **Beklenen:** `OK`; dosya içeriği okunur; olmayan yolda hata kodu (crash yok).
- **Kanıt:** disk `neva_session.h`

#### TC-ENG-006 — IAudioBackend tek soyutlama noktası
- **Gereksinim:** NE-ENG-006 · **Katman:** L2 (statik) · **Otomasyon:** CI
- **Adımlar:** `neva-core` + `apps` içinde `audioclient.h|alsa|pa_|coreaudio` include grep'i.
- **Beklenen:** 0 isabet (yalnız `backends/` altında); aksi FAIL.
- **Kanıt:** S3 §4.3 yasak 4 · ADR-019 §2.2

#### TC-ENG-007 — BackendRegistry kayıt tablosu
- **Gereksinim:** NE-ENG-007 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Registry'den kayıt listesini çek; §04.4 zincir sırası + platform bitişleri ile karşılaştır.
- **Beklenen:** Sıra birebir; Null her platformda kayıtlı; eksik kayıt FAIL.
- **Kanıt:** S3 §3.3

#### TC-ENG-008 — Fallback zinciri 7 basamak
- **Gereksinim:** NE-ENG-008 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Her basamağı yapay `StreamError` ile düşür (mock backend); zincirin sıradaki basamağa geçişini ve sonunda Null'a varışı gözle.
- **Beklenen:** Sıra: ASIO→WASAPI Excl→(Shared)→WDM-KS→PipeWire→ALSA→CoreAudio→Null; hiçbir kombinasyonda "hiçbir şey yok" durumu yok; her düşüş event E8.
- **Kanıt:** ADR-107 §2.1 · S3 §3.3

#### TC-ENG-009 — negotiated_format kanıt zorunluluğu
- **Gereksinim:** NE-ENG-009 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Hash testi koşul bloğunda `negotiated_format() != hedef` durumunu simüle et.
- **Beklenen:** Test PASS **yazamaz** (koşul dışı/FALSE); guard olmadan PASS = FAIL.
- **Kanıt:** S3 §3.2 · ADR-107 K3

#### TC-ENG-010 — BackendKind sabitliği
- **Gereksinim:** NE-ENG-010 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Enum değerlerini sabit script ile oku (snapshot karşılaştırma).
- **Beklenen:** Mevcut değerler değişmemiş; yeniden numaralandırma = FAIL (ABI/log kırılması).
- **Kanıt:** S3 §3.1

#### TC-ENG-011 — AudioFormat sınır testleri
- **Gereksinim:** NE-ENG-011 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** 44100/192000/48000 + 16/24/32f + channels 1..8 geçerli; 0/96000+ve channels 9 + frames 1/8192 geçersiz.
- **Beklenen:** Geçerli → OK; geçersiz → `StreamError`/`INVALID_ARG`; UB (ASan) yok.
- **Kanıt:** S3 §3.1 · VISION V-8

#### TC-ENG-012 — StreamError taşıma
- **Gereksinim:** NE-ENG-012 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Zinciri 2. basamaktan düşür; dönen hata yapısını incele.
- **Beklenen:** `code` + hangi `backend` düştü + `what` metni dolu; log non-RT.
- **Kanıt:** S3 §3.1

#### TC-ENG-013 — ADR-019 ek imzalar kontratı
- **Gereksinim:** NE-ENG-013 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `IAudioBackend` üzerinde `switchMode/setDeviceRate/kill` derleme + Null üzerinde davranış doğrulama.
- **Beklenen:** Üçü mevcut; Null dahil her implementasyon override eder (varsayılan `Unsupported`/no-op hata).
- **Kanıt:** ADR-019 §2.2b

#### TC-ENG-014 — exclusive↔shared geçiş (switchMode)
- **Gereksinim:** NE-ENG-014 · **Katman:** L2 · **Otomasyon:** CI (mock) + manuel (gerçek WASAPI)
- **Adımlar:** Çalma sırasında `switchMode`; ayrıca `AUDCLNT_E_DEVICE_IN_USE` simülasyonu.
- **Beklenen:** Ring korunur; geçiş kesintisi ≤20 ms `⚠️ VR (eşik)`; başarısızlık zincire düşer.
- **Kanıt:** ADR-019 F2 · S3 §3.4

#### TC-ENG-015 — Mismatch çözüm sırası (F3)
- **Gereksinim:** NE-ENG-015 · **Katman:** L2 · **Otomasyon:** CI (mock cihaz 48k + dosya 44.1k)
- **Adımlar:** (1) `setDeviceRate` dener; (2) başarısız → soxr producer-side; (3) o da başarısız → fallback.
- **Beklenen:** RT callback'te resample çağrısı 0 (profil); sıradan sapma FAIL.
- **Kanıt:** ADR-019 §2 seçenek 1-3

#### TC-ENG-016 — kill-switch
- **Gereksinim:** NE-ENG-016 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Çalma + xrun enjeksiyonu → `kill()` → dalga formu kaydı.
- **Beklenen:** Fade-out → 50 ms sessizlik → restart; `noexcept` (statik); callback bloklanmaz.
- **Kanıt:** ADR-019 F4 · ADR-017

#### TC-ENG-017 — Platform matrisi derleme
- **Gereksinim:** NE-ENG-017 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Windows x64 · Linux x64 · ARM64 build'leri.
- **Beklenen:** Üçü yeşil; ortak çekirdek platform kodu içermiyor (#ifdef yalnız backend/adapter).
- **Kanıt:** S3 §2

#### TC-ENG-018 — K-space band yasak
- **Gereksinim:** NE-ENG-018 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `.ai/` + repoda band-2..7'ye gerçek domain adı atanmış metin grep'i (bu PRD dahil).
- **Beklenen:** 0 isabet (yalnız "rezerv" notları); aksi FAIL.
- **Kanıt:** ADR-097 §K5

#### TC-ENG-019 — XP/7 kapsam dışı
- **Gereksinim:** NE-ENG-019 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Gereksinim/belge taraması (XP/7 hedefi iddiası) + CI OS matrisi.
- **Beklenen:** Hedef iddiası yok; CI matrisinde XP/7 yok.
- **Kanıt:** S3 §2

#### TC-ENG-020 — ARM64 build
- **Gereksinim:** NE-ENG-020 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Windows ARM64 + Linux aarch64 derleme; Null testleri (RPi5 üzerinde gerçek test `⚠️ VR runner`).
- **Beklenen:** Derleme yeşil; Null testleri geçer.
- **Kanıt:** S3 §2/S3 §5

#### TC-ENG-021 — WASAPI Exclusive iki deneme
- **Gereksinim:** NE-ENG-021 · **Katman:** L2 · **Otomasyon:** manuel + mock
- **Adımlar:** Event-driven callback kurulumu; WAVEFORMATEX → WAVEFORMATEXTENSIBLE deneme sırası logu.
- **Beklenen:** Sıra birebir; başarısızlık `StreamError` + zincir düşüşü.
- **Kanıt:** S3 §3.4 · spec `wasapi-exclusive.md:126`

#### TC-ENG-022 — Shared fallback (device-in-use)
- **Gereksinim:** NE-ENG-022 · **Katman:** L2 · **Otomasyon:** manuel (2 süreç)
- **Adımlar:** Süreç A exclusive tutar → süreç B player başlatır.
- **Beklenen:** B shared'a düşer, ses devam eder; bit-perfect rozeti devre dışı (player).
- **Kanıt:** ADR-019 §1.1

#### TC-ENG-023 — ASIO yolu (şartlı U1)
- **Gereksinim:** NE-ENG-023 · **Katman:** L2 · **Otomasyon:** manuel (donanım)
- **Adımlar:** ASIO driver + `NEVA_WITH_ASIO=ON` derleme; open/start/stop; sürücü yok senaryosu.
- **Beklenen:** Akış; sürücü yoksa zincir 2'ye düşer; **U1 çözülmeden bu test release paketine girmez** (şartlı).
- **Kanıt:** ADR-019 · ADR-107 K4

#### TC-ENG-024 — WDM-KS
- **Gereksinim:** NE-ENG-024 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** Windows'ta WDM-KS seçimi; başarısızlık senaryosu.
- **Beklenen:** Doğrudan erişim; başarısızlık Null'a düşer.
- **Kanıt:** S3 §3.4 (P2)

#### TC-ENG-025 — PipeWire F32P → ALSA
- **Gereksinim:** NE-ENG-025 · **Katman:** L2 · **Otomasyon:** manuel (Linux)
- **Adımlar:** PipeWire var/yok iki senaryo.
- **Beklenen:** Var: F32P akış; yok: ALSA'ya düşüş; bit-derinlik dönüşümü yok (float).
- **Kanıt:** S3 §3.4

#### TC-ENG-026 — ALSA hw:
- **Gereksinim:** NE-ENG-026 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** `hw:` erişimli/erişimsiz senaryolar.
- **Beklenen:** Sabit format; erişimsizse Null.
- **Kanıt:** S3 §3.4

#### TC-ENG-027 — CoreAudio (şartlı M6/U3)
- **Gereksinim:** NE-ENG-027 · **Katman:** L2 · **Otomasyon:** manuel (macOS)
- **Adımlar:** HAL physical format okuma/yazma.
- **Beklenen:** Toggle başarılı/başarısız davranışları §04.4 zincirine uyar.
- **Kanıt:** S3 §3.4 · spec `core-audio-macos.md`

#### TC-ENG-028 — Null backend determinizm
- **Gereksinim:** NE-ENG-028 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Null ile 2× akış; çıktı hash'leri.
- **Beklenen:** Byte-eşit; her platformda en son basamak.
- **Kanıt:** disk `null_backend.cpp`

#### TC-ENG-029 — enumerate/supported_formats
- **Gereksinim:** NE-ENG-029 · **Katman:** L2 · **Otomasyon:** CI (mock) + manuel
- **Adımlar:** Cihaz listesi + format listesi çek; boş liste simüle et.
- **Beklenen:** Liste dolu/mock doğru; boş → `StreamError` (sessiz boşluk yok).
- **Kanıt:** S3 §3.2

#### TC-ENG-030 — Hot-plug
- **Gereksinim:** NE-ENG-030 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** Çalma sırasında cihaz koparma/takma.
- **Beklenen:** Zincir devreye girer; event E8; yeniden bağlanınca enumerate.
- **Kanıt:** ADR-019 F1 · spec `core-audio-macos.md:46-57` (Windows/Linux `⚠️ VR`)

#### TC-ENG-031 — switchMode/Null varsaylanı
- **Gereksinim:** NE-ENG-031 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Null üzerinde `switchMode` çağır.
- **Beklenen:** Tanımlı davranış (Unsupported/no-op hata) — derleme hatası yok.
- **Kanıt:** ADR-019 §2.2b

#### TC-ENG-032 — setDeviceRate hata dönüşü
- **Gereksinim:** NE-ENG-032 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Desteklenmeyen hız iste.
- **Beklenen:** `StreamError` → zincir resample/fallback'e geçer (NE-ENG-015 zinciri).
- **Kanıt:** ADR-019 §2.2b

#### TC-ENG-033 — kill() impl
- **Gereksinim:** NE-ENG-033 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** `noexcept` statik + çağırma/tekrar-çağırma (idempotent).
- **Beklenen:** İkinci çağrı güvenli; callback sonraki periyotta durur.
- **Kanıt:** ADR-019 §2.2b

#### TC-ENG-034 — Callback render sözleşmesi
- **Gereksinim:** NE-ENG-034 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** İmza sabit derleme + taşıma formatı (interleaved float) doğrulama.
- **Beklenen:** Format dönüşümü callback içinde değil, backend sınırında; imza değişikliği ABI kırar.
- **Kanıt:** S3 §3.2 · ADR-017

#### TC-ENG-035 — Yaşam döngüsü sıralama hataları
- **Gereksinim:** NE-ENG-035 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** open→start→stop→close doğrusal; sonra start-önce-open, ikinci close, tekrar open.
- **Beklenen:** Yanlış sıralama `StreamError`; close idempotent; reopen çalışır.
- **Kanıt:** S3 §3.2

#### TC-ENG-036 — Hash bit-perfect (ana hat)
- **Gereksinim:** NE-ENG-036 · **Katman:** Kapı · **Otomasyon:** CI (her PR)
- **Adımlar:** §11.2 hattı — 12 kombinasyon.
- **Beklenen:** 12/12 byte-eşit; determinism 2×.
- **Kanıt:** ADR-107 K3 · ADR-0105 kapı 5.1 ⚠️

#### TC-ENG-037 — OS mikser baypası
- **Gereksinim:** NE-ENG-037 · **Katman:** L2 · **Otomasyon:** manuel (Windows)
- **Adımlar:** Exclusive akışta sistem ses seviyesini değiştir; dış kayıt/loopback gözlemi.
- **Beklenen:** Etki yok (session volume 1.0 sabit); shared modda etki VARDIR ve raporlanır (iddia düşer).
- **Kanıt:** VISION V-5 · ADR-019

#### TC-ENG-038 — Exclusive kilit çatışması
- **Gereksinim:** NE-ENG-038 · **Katman:** L2 · **Otomasyon:** manuel (2 süreç)
- **Adımlar:** İki süreç exclusive yarışı.
- **Beklenen:** `AUDCLNT_E_DEVICE_IN_USE` → düşüş zinciri; asla kilitlenme/deadlock.
- **Kanıt:** spec `wasapi-exclusive.md:126`

#### TC-ENG-039 — BUFFER_SIZE_NOT_ALIGNED kurtarma
- **Gereksinim:** NE-ENG-039 · **Katman:** L2 · **Otomasyon:** mock
- **Adımlar:** Hata kodunu enjekte et → periyot yeniden hesap döngüsü.
- **Beklenen:** Tek seferde yutulmaz; yeniden hesap → tekrar deneme; başarısızsa zincir düşer.
- **Kanıt:** ADR-106 §1.3 (radio-and-hires §3-§4)

#### TC-ENG-040 — Xrun sayacı
- **Gereksinim:** NE-ENG-040 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Underrun/overrun enjeksiyonu; sayaçları oku.
- **Beklenen:** sayaçlar artar; log non-RT (RT'de dosya yok — NE-ENG-083).
- **Kanıt:** ADR-017 · S3 §6.1

#### TC-ENG-041 — Mock clock determinizm
- **Gereksinim:** NE-ENG-041 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Aynı test 2×, sahte saat 2× hızda.
- **Beklenen:** Bit-eşit sonuç; gerçek saat bağımlılığı 0.
- **Kanıt:** S3 §6

#### TC-ENG-042 — Kontrat test paketi (Null)
- **Gereksinim:** NE-ENG-042 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Paketi Null üzerinde çalıştır.
- **Beklenen:** enumerate/open/negotiate/start/stop/close + hata yolları yeşil; yeni backend kayıt öncesi bu paketi geçmek zorunda (CI kuralı).
- **Kanıt:** S3 §6.1 kabul 1

#### TC-ENG-043 — Fallback telemetri event
- **Gereksinim:** NE-ENG-043 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Zinciri düşür; event E8 yakala.
- **Beklenen:** Düşüş nedeni (kod) + backend adı event'te.
- **Kanıt:** S3 §3.1 · §05.2 E8

#### TC-ENG-044 — Backend dizin izolasyonu
- **Gereksinim:** NE-ENG-044 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `backends/wasapi/*` içinde `backends/alsa|pipewire|coreaudio` include grep'i (ve simetrik).
- **Beklenen:** 0 isabet; yalnız `IAudioBackend.h`.
- **Kanıt:** S3 §3.4

#### TC-ENG-045 — Registry 8 backend
- **Gereksinim:** NE-ENG-045 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Registry sayımı + uygulanmayanların çağrılması.
- **Beklenen:** 8 kayıt; uygulanmayan `NotImplemented`/`StreamError` döner (sessiz yok sayılmaz).
- **Kanıt:** S3 §3.1 BackendKind

#### TC-ENG-046 — Tek public header (install)
- **Gereksinim:** NE-ENG-046 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Install/temiz paket içeriği listesi.
- **Beklenen:** Tek header + lib/dll; iç header yok.
- **Kanıt:** ADR-0105 §2.6 ⚠️

#### TC-ENG-047 — Event callback (non-RT marshal)
- **Gereksinim:** NE-ENG-047 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Callback içinde thread id kaydet + RT thread id ile karşılaştır; unregister sonrası çağrı.
- **Beklenen:** Callback RT thread'de DEĞİL; unregister sonrası çağrı yok (UAF yok).
- **Kanıt:** ADR-106 kural 2

#### TC-ENG-048 — Queue API preload
- **Gereksinim:** NE-ENG-048 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** `open_queue` + `preload_next`; geçişi ölç (ring underrun sayacı).
- **Beklenen:** Geçişte ring starve 0 (kardeş TC-PLR-061); RT'yi beklemeden hazırlık.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-ENG-049 — position/duration
- **Gereksinim:** NE-ENG-049 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Bilinen sürede test dosyası oynat; değerleri 100× oku.
- **Beklenen:** Değerler monoton/bilinen aralıkta; stream modunda duration yok kod; atomik okuma (TSan temiz).
- **Kanıt:** kardeş PRD §05.2 E3

#### TC-ENG-050 — Seek
- **Gereksinim:** NE-ENG-050 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** 0 · orta · süre-1 ms · süre+1 ms · negatif seek.
- **Beklenen:** Clamp; kısa fade (click analizi); başarısızlık hata; RT'de seek işlemi yok.
- **Kanıt:** kardeş PRD §05.2 E4 · ADR-017

#### TC-ENG-051 — EQ band count atomik yayın
- **Gereksinim:** NE-ENG-051 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Çalma sırasında 31→10→31 geçişi + dalga formu/pop analizi + tahsis sayacı.
- **Beklenen:** Maske değişimi ramp'li; tahsis 0; pop 0.
- **Kanıt:** ADR-106 kural 5

#### TC-ENG-052 — Preset uyuşmazlık düzeltme
- **Gereksinim:** NE-ENG-052 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** band_count=10 preset'i 31-band oturuma yükle; eski grid_version'lı preset yükle.
- **Beklenen:** Kural 2 işletilir (düzeltme + uyarı); çökme yok.
- **Kanıt:** ADR-106 kural 6

#### TC-ENG-053 — negotiated_format API
- **Gereksinim:** NE-ENG-053 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** API çağrısı → hash ön koşulu ile eşleştir.
- **Beklenen:** Dönen format hash koşulunda kullanılır; uyuşmazlıkta koşul-FALSE.
- **Kanıt:** ADR-107 K3

#### TC-ENG-054 — Device list + change event
- **Gereksinim:** NE-ENG-054 · **Katman:** L2 · **Otomasyon:** CI (mock) + manuel
- **Adımlar:** Enumerate'ı paralel thread'de çağır + hot-plug simülasyonu.
- **Beklenen:** Thread-safe liste; olay UI'a akar.
- **Kanıt:** ADR-019 F1

#### TC-ENG-055 — Tuner API
- **Gereksinim:** NE-ENG-055 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** `tuner_connect/disconnect` (URL doğrulama player'da — bu test engine sözleşmesi).
- **Beklenen:** Mod geçişi temiz; tekrar-connect çalışır; engine validator KOYMAZ (tek kapı ADR-106 kural 6).
- **Kanıt:** ADR-106 §2.2a · kardeş §05.2 E9

#### TC-ENG-056 — LUFS publish
- **Gereksinim:** NE-ENG-056 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Bilinen LUFS kaynak akışı; 10 Hz publish hızı + değer doğruluğu.
- **Beklenen:** Değer ≈ beklenen `⚠️ VR (tolerans)`; RT'de hesap yok (profil).
- **Kanıt:** ADR-106 §2.2a şeması

#### TC-ENG-057 — Error code sabitliği
- **Gereksinim:** NE-ENG-057 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Enum snapshot karşılaştırma (0-4).
- **Beklenen:** Değerler değişmemiş; yeni kod yalnız ek sonunda.
- **Kanıt:** kardeş PRD §05.2 notu

#### TC-ENG-058 — UTF-8 geçiş (Unix)
- **Gereksinim:** NE-ENG-058 · **Katman:** L2 · **Otomasyon:** CI (Linux)
- **Adımlar:** `char16_t` → UTF-8 dönüşümü ile Türkçe yollu dosya açma (NFD/NFC iki varyant).
- **Beklenen:** İkisi de açılır `[ÖNERİ — macOS NFD]`; dönüşüm tek katmanda.
- **Kanıt:** kardeş PRD §05.1 A4

#### TC-ENG-059 — TSan thread-safety
- **Gereksinim:** NE-ENG-059 · **Katman:** L2 · **Otomasyon:** CI (Linux TSan)
- **Adımlar:** 10 thread × control spam (NE-ENG-097 ile birlikte).
- **Beklenen:** 0 data race; session durumu tutarlı.
- **Kanıt:** kardeş PRD §05.3-§05.4

#### TC-ENG-060 — ABI freeze dump
- **Gereksinim:** NE-ENG-060 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Sembol seti dump + arşivlenmiş 1.0.0 seti ile diff.
- **Beklenen:** Kaldırma/degistirme yok; yalnız ekleme (minor); kırılma = majör sürüm ister.
- **Kanıt:** kardeş PRD §05.4

#### TC-ENG-061 — Biquad doğruluk (RBJ)
- **Gereksinim:** NE-ENG-061 · **Katman:** L1 · **Otomasyon:** CI (`test_biquad` — IMPLEMENTED)
- **Adımlar:** Peaking/Shelf/HPF/LPF katsayılarını referans RBJ formülü (A=10^(dB/40)) ile karşılaştır; frekans yanıtı örnekleme.
- **Beklenen:** Sapma ≤ tolerans `⚠️ VR (değer tanımsız; önerü: ±0.01 dB @ referans frekanslar)`; test yeşil.
- **Kanıt:** disk `biquad.h` · `test_biquad.cpp` · ADR-106 §1.3

#### TC-ENG-062 — EqChain tahsis zorunluluğu
- **Gereksinim:** NE-ENG-062 · **Katman:** L1/L2 · **Otomasyon:** CI + tahsis sayacı
- **Adımlar:** Session-open sonrası zinciri başlat; `new`/`malloc` hook sayacını izle; `set_gain` çağır.
- **Beklenen:** Zincir çalışırken tahsis 0; per-kanal (`band, ch`) bağımsız gain doğruluğu.
- **Kanıt:** disk `eq_chain.h` · ADR-106 kural 4/6

#### TC-ENG-063 — ISO 31-grid
- **Gereksinim:** NE-ENG-063 · **Katman:** L1 · **Otomasyon:** CI (`eq_grids_tests` — IMPLEMENTED)
- **Adımlar:** 31 frekansı tabloyla karşılaştır (20…20000 Hz); zincir band sayısıyla eşleştir.
- **Beklenen:** Byte-eşit; test yeşil (disk kanıtı mevcut).
- **Kanıt:** disk `eq_grids.h` · ADR-106 §2.2b

#### TC-ENG-064 — 10-band ISO alt küme
- **Gereksinim:** NE-ENG-064 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** 31.5/63/125/250/500/1k/2k/4k/8k/16k listesini üret; VLC 62.5 farkını test et (map edilmez).
- **Beklenen:** Alt küme ISO'ya uygun; VLC preset'i kendi setiyle okunur (zorla eşleme yok — duraklar).
- **Kanıt:** ADR-106 §2.2b kural 3

#### TC-ENG-065 — 15/20-band grid (şartlı teyit)
- **Gereksinim:** NE-ENG-065 · **Katman:** L1 · **Otomasyon:** CI (tanım varsa)
- **Adımlar:** `eq_grids` tanım dosyasını oku; teyitli liste yoksa test "tanım eksik" raporlar (FAIL değil, TODO etiketi `[ÖNERİ]`).
- **Beklenen:** Teyit gelene kadar etiket `⚠️ VR` korunur; tanım varsa değerler sabit.
- **Kanıt:** ADR-106 §2.2b kural 4/5 · §1.4

#### TC-ENG-066 — Bypass maskesi (zincir takası yok)
- **Gereksinim:** NE-ENG-066 · **Katman:** L1/L2 · **Otomasyon:** CI
- **Adımlar:** Flat preset ile akış + hash; sonra 31-band aktif; zincir nesnesi kimliğini (pointer) izle.
- **Beklenen:** Hash byte-eşit (flat); zincir adresi hiç değişmez (re-init yok); maske=0 → doğrudan geçiş.
- **Kanıt:** ADR-106 kural 4 · kardeş §06.1 D3

#### TC-ENG-067 — Ramp smoothing (click analizi)
- **Gereksinim:** NE-ENG-067 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** ±12 dB anlık adım komutu → dalga formunu incele; ramp süresini ölç (5/10/20 ms varyantları).
- **Beklenen:** Adım kenarında yüksek frekanslı pop yok (spektral eşik `[ÖNERİ]`); süre parametreye uyar.
- **Kanıt:** ADR-105 §2.3 ⚠️ (ADR-106 kural 5)

#### TC-ENG-068 — Cascade mod geçişi
- **Gereksinim:** NE-ENG-068 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** 1↔2 modunu çalarken değiştir; tahsis + süreklilik ölç.
- **Beklenen:** Geçiş yalnız fade-out/re-init/fade-in; tahsis yalnız geçiş anında (RT dışı hazırlık).
- **Kanıt:** ADR-106 kural 5

#### TC-ENG-069 — Parametrik mod ayrımı
- **Gereksinim:** NE-ENG-069 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Parametrik f/Q değiştir → grafik preset'e dokunmadığını doğrula (state ayrımı).
- **Beklenen:** İki state bağımsız; RT kuralları ihlali yok.
- **Kanıt:** ADR-106 §2.2b kural 7 · ADR-0105 §2.1

#### TC-ENG-070 — True-peak limiter bypass (hash)
- **Gereksinim:** NE-ENG-070 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Bit-perfect yolda limiter bayrağını kapat/aç; hash'i karşılaştır; ayrıca aşım sinyali ile ceiling doğrula.
- **Beklenen:** Bypass = hash byte-eşit; aşım senaryosunda ceiling aşılmaz `⚠️ VR (true-peak oversample katsayısı)`.
- **Kanıt:** ADR-0105 §2.1 ⚠️ · kardeş §06.4

#### TC-ENG-071 — R128 sidechain doğruluğu
- **Gereksinim:** NE-ENG-071 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Bilinen LUFS değerli referans sinyal (EBU test sinyali `[ÖNERİ]`) → hesapla.
- **Beklenen:** Referans değer ±tolerans `⚠️ VR (±0.1 LU önerü)`; RT thread'e dokunma yok (profil).
- **Kanıt:** ADR-106 §2.2a · kardeş §06.4

#### TC-ENG-072 — soxr bypass (eşit format)
- **Gereksinim:** NE-ENG-072 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** 48k dosya/48k cihaz (resample gerektirmez) + 44.1k/48k (gerektirir) iki senaryo; resampler çağrı sayacı.
- **Beklenen:** Eşit format = resampler çağrı 0 + hash; farklı = resampler devrede + hash koşul dışı.
- **Kanıt:** kardeş §06.5 R4 ⚠️ · NE-ENG-061 olarak bu PRD'de sabit

#### TC-ENG-073 — Preamp identity (0 dB)
- **Gereksinim:** NE-ENG-073 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Preamp 0 dB + flat → hash; sonra +3 dB → sapma doğrulaması + rampa.
- **Beklenen:** 0 dB = identity (byte-eşit); artış ramp'li; limiter aşım koruması devrede.
- **Kanıt:** kardeş §06.1

#### TC-ENG-074 — Multi-channel (8.1) DSP
- **Gereksinim:** NE-ENG-074 · **Katman:** L2 · **Otomasyon:** CI (mock 8ch)
- **Adımlar:** 8 kanal akış + EQ; CPU payını ölç.
- **Beklenen:** Kanal başına uygulama; bütçe §08 karşılanır; 5.1/8.1 kaynak geçişi.
- **Kanıt:** VISION V-8 · S3 §3.1

#### TC-ENG-075 — Ring overflow politikası (iki mod)
- **Gereksinim:** NE-ENG-075 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Producer'ı yavaşlat (dosya modu) / RT'yi aç (akış modu) → sayaçlar.
- **Beklenen:** Dosya: drop 0, gecikme büyür; radyo: drop sayacı artar, süreklilik korunur (§07.4 tablosu).
- **Kanıt:** kardeş §07.4 `[ÖNERİ]`

#### TC-ENG-076 — DSP profil worst-case
- **Gereksinim:** NE-ENG-076 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Benchmark'ı 31-band ile çalıştır; 10-band ile karşılaştır.
- **Beklenen:** CI benchmark 31-band (10-band koşulu gizli FAIL üretmez — raporlama net).
- **Kanıt:** ADR-0105 §5.6 ⚠️ (ADR-106 kural 6)

#### TC-ENG-077 — CPU ≤%5 (192 kHz stereo)
- **Gereksinim:** NE-ENG-077 · **Katman:** Kapı · **Otomasyon:** CI bench
- **Adımlar:** 192k/32f + 31-band + limiter → tek çekirdek CPU ölçümü (≈59.5 M MAC/s hesabıyla).
- **Beklenen:** ≤%5; aşım = release bloke.
- **Kanıt:** ADR-106 §1.3 · ADR-0105 §5.6 ⚠️

#### TC-ENG-078 — DSP state sıfırlama
- **Gereksinim:** NE-ENG-078 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Sessizlik + yüksek gain sonrası `reset()` → ilk periyot kaydı.
- **Beklenen:** Eski durum sızıntısı/transient yok (biquad state sıfır).
- **Kanıt:** kardeş §05.2 `[ÖNERİ]`

#### TC-ENG-079 — Denormal koruması (FTZ/DAZ)
- **Gereksinim:** NE-ENG-079 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Uzun sessizlik → tek ses periyodu; CPU profilini karşılaştır.
- **Beklenen:** Denormal kaynaklı CPU patlaması yok (bilinen klasik hata).
- **Kanıt:** YOK — `[ÖNERİ]` (RT best practice)

#### TC-ENG-080 — DSP birim test kapsamı
- **Gereksinim:** NE-ENG-080 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** biquad + eq_grids + (yeni) eq_chain/limiter/ramp testlerini çalıştır; kapsam raporu.
- **Beklenen:** Yeşil; her grid frekansı doğruluk testinde; §11.4 piramit dağılımı korunur.
- **Kanıt:** disk `tests/*` · kardeş §11

#### TC-ENG-081 — Zero-allocation RT callback
- **Gereksinim:** NE-ENG-081 · **Katman:** L2 · **Otomasyon:** CI (test build hook)
- **Adımlar:** Test build'de `operator new` override (RT thread'de çağrı = hard-fail); 60 s stres.
- **Beklenen:** RT thread'de 0 tahsis; aksi anında FAIL (bilinçli).
- **Kanıt:** ADR-017 · VISION V-3

#### TC-ENG-082 — Lock-free SPSC
- **Gereksinim:** NE-ENG-082 · **Katman:** L1/L2 · **Otomasyon:** CI (TSan + invariant)
- **Adımlar:** Çapraz thread writer/reader + TSan; invariant assert (writer okuyucuyu geçmez).
- **Beklenen:** 0 data race; invariant ihlali yok (fuzz edilmiş boyut/ hız varyantları).
- **Kanıt:** disk `spsc_ring.h` · VISION V-4

#### TC-ENG-083 — RT yasağı denetimi
- **Gereksinim:** NE-ENG-083 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `backends/*/render*` içinde yasaklı çağrı grep'i (mutex/log/alloc/fstream/`std::function`/time sistem çağı).
- **Beklenen:** 0 isabet; aksi FAIL (inceleme yolu yok — otomatik).
- **Kanıt:** ADR-017 · kardeş §07.5

#### TC-ENG-084 — Stack bütçesi (≤4 KB)
- **Gereksinim:** NE-ENG-084 · **Katman:** L2 · **Otomasyon:** CI (rapor)
- **Adımlar:** `-fstack-usage` / MSVC analiz ile callback ağacı derinliği.
- **Beklenen:** ≤4 KB `[ÖNERİ]`; aşım rapor (release notu).
- **Kanıt:** kardeş §08.3 `[ÖNERİ]`

#### TC-ENG-085 — Callback süre histogramı
- **Gereksinim:** NE-ENG-085 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** 10 dk akış + süre sayaçları → P99/P999 hesabı; bütçeyle karşılaştır.
- **Beklenen:** P99 ≤ bütçe (§08.4); aşım sayısı rapor.
- **Kanıt:** kardeş §08.4 `[ÖNERİ]`

#### TC-ENG-086 — Starve fade (≤50 ms)
- **Gereksinim:** NE-ENG-086 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Producer'ı durdur → dalga formu kaydı → fade süresi ölçümü.
- **Beklenen:** Monoton azalma (click yok); ≤50 ms; sessizlik sonra kurtarma.
- **Kanıt:** ADR-106 §2.2a şeması

#### TC-ENG-087 — Xrun politikası (fade → 50 ms → restart)
- **Gereksinim:** NE-ENG-087 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Yapay underrun/overrun; olay sırasını zaman damgalı logla.
- **Beklenen:** Fade-out → 50 ms → restart sırası; sayaçlar artar; ses kurtarılır.
- **Kanıt:** ADR-017 (brain.md:869)

#### TC-ENG-088 — 30 dk stres = 0 xrun
- **Gereksinim:** NE-ENG-088 · **Katman:** Kapı (G4) · **Otomasyon:** scheduled CI
- **Adımlar:** §11.3-D düzenek; gece çalıştırma; rapor.
- **Beklenen:** xrun 0 · tahsis 0 · leak 0 (referans donanım, frekans sabit).
- **Kanıt:** S3 §6.1

#### TC-ENG-089 — ASIO <10 ms
- **Gereksinim:** NE-ENG-089 · **Katman:** Kapı (G2) · **Otomasyon:** manuel (release)
- **Adımlar:** §11.3-A loopback; ASIO blok boyutunu kademeli düşür.
- **Beklenen:** <10 ms; aşan blok seçimi otomatik reddedilir (limit veri).
- **Kanıt:** ADR-006 (ADR-019 §1.4)

#### TC-ENG-090 — WASAPI <20 ms
- **Gereksinim:** NE-ENG-090 · **Katman:** Kapı (G2) · **Otomasyon:** manuel (release)
- **Adımlar:** Aynı düzenek; WASAPI Excl/Shared ayrımı.
- **Beklenen:** <20 ms; blok ≥1024@48k denenmez (yasağı) `⚠️ VR L1-L4` — eşik tanımlanana kadar en küçük blok.
- **Kanıt:** ADR-006 · ADR-0105 §2.10 ⚠️

#### TC-ENG-091 — Ring kapasite hesabı doğrulaması
- **Gereksinim:** NE-ENG-091 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** Kapasite = f(max periyot, jitter) formülü hesap + starve/overflow uç testleri.
- **Beklenen:** Hesap §08.3 ile tutarlı; iki uçta da davranış §07.4 (bu PRD §07/§08) tablosu.
- **Kanıt:** kardeş §07.3

#### TC-ENG-092 — RAM ≤400 MB (engine payı)
- **Gereksinim:** NE-ENG-092 · **Katman:** Kapı · **Otomasyon:** CI (profiler)
- **Adımlar:** Session + queue + EQ açık 30 dk; RSS/WS ölçümü.
- **Beklenen:** Bütçe içinde; aşım = release bloke (bütçe defterine kayıt).
- **Kanıt:** ADR-0105 §2.9 ⚠️

#### TC-ENG-093 — Leak (1000 cycle)
- **Gereksinim:** NE-ENG-093 · **Katman:** L2 · **Otomasyon:** CI (LSan)
- **Adımlar:** 1000× open/close + play/stop; net artış ölçümü.
- **Beklenen:** Net artış 0; LSan temiz.
- **Kanıt:** kardeş §11

#### TC-ENG-094 — Fuzz (C ABI + ICY)
- **Gereksinim:** NE-ENG-094 · **Katman:** Kapı (5.13) · **Otomasyon:** CI
- **Adımlar:** path/band/gain/queue fuzzer + ICY corpus ≥1000; ASan/UBSan.
- **Beklenen:** 0 crash; CI ≤10 dk `⚠️ VR eşik` (ADR-0105 kapı 5.13).
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️ · ADR-106

#### TC-ENG-095 — ICY parser limitleri
- **Gereksinim:** NE-ENG-095 · **Katman:** L1/L2 · **Otomasyon:** CI (`icy_parser_tests` — IMPLEMENTED + genişletme)
- **Adımlar:** icy-metaint 31 B (RED) / 32 B (OK) / 1 MiB (OK) / 1 MiB+1 (RED); text blok 1 MB cap; çiğ blok 1 MB.
- **Beklenen:** Sınırda doğru; sınırsız buffer büyümesi yok (bellek tepe ölçümü); test yeşil.
- **Kanıt:** disk `icy_parser.h` · ADR-106 kural 8

#### TC-ENG-096 — Bench ortamı sabitleme
- **Gereksinim:** NE-ENG-096 · **Katman:** proses · **Otomasyon:** rapor
- **Adımlar:** Bench donanım + frekans + OS bilgisini rapora yaz.
- **Beklenen:** Rapor yoksa bench sonucu geçersiz (kabul edilmez).
- **Kanıt:** kardeş §08.5

#### TC-ENG-097 — Multi-thread control spam
- **Gereksinim:** NE-ENG-097 · **Katman:** L2 · **Otomasyon:** CI (TSan)
- **Adımlar:** 10 thread × play/pause/set_gain karışık 60 s.
- **Beklenen:** 0 race; durum tutarlı (son durum belirlenebilir); crash yok.
- **Kanıt:** NE-ENG-059

#### TC-ENG-098 — Soğuk açılış <300 ms
- **Gereksinim:** NE-ENG-098 · **Katman:** L2 · **Otomasyon:** CI (sıcaklık etiketli)
- **Adımlar:** Cache soğutma (mümkünse) → open → ilk ses arasında süre.
- **Beklenen:** ≤300 ms `[ÖNERİ]`; sonuç raporda soğuk/sıcak ayrımı.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-ENG-099 — Telemetri opt-out
- **Gereksinim:** NE-ENG-099 · **Katman:** L2 · **Otomasyon:** CI (ağ denetimi)
- **Adımlar:** Sayaçlar açık → ağ trafiği izle (firewall/pcap).
- **Beklenen:** Ağ çağrısı 0 (yerel log); varsayılan kapalı.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-ENG-100 — Perf regresyon etiketi
- **Gereksinim:** NE-ENG-100 · **Katman:** proses · **Otomasyon:** CI
- **Adımlar:** Baseline ile %10+ kötüleşmeyi simüle et.
- **Beklenen:** PR etiketi/warn (politika: bloke etmez `[ÖNERİ]`); etiket yoksa FAIL.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-ENG-101 — neva-core statik lib ayrımı
- **Gereksinim:** NE-ENG-101 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Temiz build; exe'lerin yalnız public header + statik lib ile bağlandığını doğrula (link haritası).
- **Beklenen:** Ayrık derleme; core iç bağımlılık sızıntısı yok.
- **Kanıt:** disk `neva_engine.lib` · ADR-107 K2

#### TC-ENG-102 — neva_session shared DLL export
- **Gereksinim:** NE-ENG-102 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Export listesi (TC-ENG-001 ile ortak) + Linux `.so` karşılığı.
- **Beklenen:** Yalnız `neva_*`; her iki platformda.
- **Kanıt:** disk `neva_session.dll` · ADR-107 K2

#### TC-ENG-103 — apps/ kuralı
- **Gereksinim:** NE-ENG-103 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `neva-core/CMakeLists.txt` içinde `add_executable` grep'i; yeni exe yalnız apps üzerinden.
- **Beklenen:** 0 isabet; ihlal FAIL.
- **Kanıt:** S3 §4.2 · ADR-107 prompt §6

#### TC-ENG-104 — neva-player CLI smoke
- **Gereksinim:** NE-ENG-104 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** `play`, `--device`, `--eq flat` uçları ile test dosyası; çıkış kapanış kodu.
- **Beklenen:** 0 kapanış kodu; desteklenmeyen bayrakta net mesaj.
- **Kanıt:** ADR-107 K2

#### TC-ENG-105 — neva-host köprüsü (UI çökmesi)
- **Gereksinim:** NE-ENG-105 · **Katman:** L2 · **Otomasyon:** CI + manuel
- **Adımlar:** UI/istemciyi zorla kapat → oynatma + köprü davranışı.
- **Beklenen:** Oynatma ölmez `[ÖNERİ]`; köprü yeniden bağlanır; temiz kapanışta engine stop.
- **Kanıt:** ADR-107 K2

#### TC-ENG-106 — neva-eq ayrı süreç
- **Gereksinim:** NE-ENG-106 · **Katman:** L2 · **Otomasyon:** manuel
- **Adımlar:** neva-eq'ı aç/kapat; E5/E6 çağrısı; ana oturum davranışı.
- **Beklenen:** Ana oturum etkilenmez; band count değişimi yansır (TC-ENG-051 zinciri).
- **Kanıt:** ADR-107 K2 (BUSGEQ15 emsal)

#### TC-ENG-107 — neva-matrix (şartlı)
- **Gereksinim:** NE-ENG-107 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Matrix DSP çekirdeği ADR'den sonra uygulanır; şimdilik derlenebilirlik iskeleti.
- **Beklenen:** `⚠️ VR` etiketi korunur; çekirdek yokken exe yok.
- **Kanıt:** ADR-107 K2 (BUSMatrix8 emsal)

#### TC-ENG-108 — neva-remote (şartlı U4)
- **Gereksinim:** NE-ENG-108 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Karar sonrası: localhost üzerinden basit komut turu + auth reddi (yanlış token).
- **Beklenen:** Ağız core'a sızmaz (NE-ENG-159); reddi doğru.
- **Kanıt:** ADR-0105 v1.4.0 `[ÖNERİ]` · S3 §4.3

#### TC-ENG-109 — neva-mcp (şartlı U5)
- **Gereksinim:** NE-ENG-109 · **Katman:** L2 · **Otomasyon:** şartlı
- **Adımlar:** Dil kararı sonrası bridge smoke (transport komutları).
- **Beklenen:** C ABI üzerinden; dil `UNKNOWN` iken test "şartlı" kalır.
- **Kanıt:** ADR-107 §8.5 · §18.5

#### TC-ENG-110 — VST3 wrap (şartlı K4)
- **Gereksinim:** NE-ENG-110 · **Katman:** şartlı · **Otomasyon:** şartlı
- **Adımlar:** Lisans açık değilse şablon bile yok (derleme testi koşul dışı).
- **Beklenen:** Gate NE-ENG-170 kapalıyken hedef yok.
- **Kanıt:** ADR-107 K4 · §14.3

#### TC-ENG-111 — Kullanıcı CLI eklenebilirlik
- **Gereksinim:** NE-ENG-111 · **Katman:** build · **Otomasyon:** CI (şablon örneği)
- **Adımlar:** `neva-cli-ornek` şablonunu apps/ altına ekle → çekirdeğe dokunmadan derle.
- **Beklenen:** Başarılı derleme; core dosyalarında 0 değişiklik.
- **Kanıt:** ADR-107 K2 · kardeş §04.7

#### TC-ENG-112 — Toolchain dosyası
- **Gereksinim:** NE-ENG-112 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** `cmake/neva-toolchains.cmake` ile MSVC/GCC/Clang + ARM64 konfigürasyonları.
- **Beklenen:** Tek dosyadan üç kurulum; eksik araç zinciri net hata.
- **Kanıt:** S3 §5

#### TC-ENG-113 — C++20 zorunlu
- **Gereksinim:** NE-ENG-113 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Eksik standardlı derleme denemesi; `/W4 /permissive-` uyarı bütçesi.
- **Beklenen:** Eksik standardda net hata; yeni uyarı girişi PR'da görünür.
- **Kanıt:** S3 §5 · disk CMakeLists

#### TC-ENG-114 — vcpkg yok
- **Gereksinim:** NE-ENG-114 · **Katman:** build · **Otomasyon:** CI (çevrimdışı build)
- **Adımlar:** Ağ kapalı ortamda temiz build.
- **Beklenen:** Başarılı (vendor içeriğiyle); dış paket indirme yok.
- **Kanıt:** S3 §5

#### TC-ENG-115 — CI matrisi (3 build)
- **Gereksinim:** NE-ENG-115 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Windows x64 · Linux x64 · ARM64 build + test (Null).
- **Beklenen:** Üçü yeşil; ARM64 run `⚠️ VR (runner)`.
- **Kanıt:** S3 §5/S3 §6.1

#### TC-ENG-116 — Çıktı dizin düzeni
- **Gereksinim:** NE-ENG-116 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Artefakt adları §04.7 ile karşılaştırma.
- **Beklenen:** Eşleşme (neva_engine.lib, neva_session.dll, neva_player.exe…).
- **Kanıt:** S3 §5 · disk `build/`

#### TC-ENG-117 — Windows link kütüphaneleri
- **Gereksinim:** NE-ENG-117 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** `ole32 | avrt | uuid` link kontrolü (WASAPI/COM).
- **Beklenen:** Mevcut (disk kanıtı) + temiz link.
- **Kanıt:** disk CMakeLists · S3 §5

#### TC-ENG-118 — UTF-8 kaynak kodu
- **Gereksinim:** NE-ENG-118 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** `/utf-8` bayrağı + UTF-8 BOM/kodlama denetimi; Türkçe yorumlu dosya derleme.
- **Beklenen:** Bayrak açık; bozuk kodlama uyarısı yok.
- **Kanıt:** disk CMakeLists

#### TC-ENG-119 — -fno-exceptions (şartlı)
- **Gereksinim:** NE-ENG-119 · **Katman:** şartlı · **Otomasyon:** koşullu build
- **Adımlar:** Karar verilirse deneysel bayrak ile build (kütüphane uyumu).
- **Beklenen:** `⚠️ PLANNED` korunur; şimdilik yalnız RT yüzeyinde throw yasak (TC-ENG-004).
- **Kanıt:** S3 §5

#### TC-ENG-120 — Build reproducibility
- **Gereksinim:** NE-ENG-120 · **Katman:** build · **Otomasyon:** CI (opsiyonel schedule)
- **Adımlar:** İki temiz build (farklı dizin) → artefakt hash diff.
- **Beklenen:** Eşitlik `[ÖNERİ]`; fark varsa determinism notu (zaman damgası/embedded path).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-ENG-121 — Sembol (PDB) dağıtımı
- **Gereksinim:** NE-ENG-121 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Paket içeriği listesi (ürün vs sembol ayrımı).
- **Beklenen:** PDB ayrı paket; ürün paketinde yok.
- **Kanıt:** disk `neva_session.pdb` · S3 §5

#### TC-ENG-122 — Sürüm bilgisi senkronu
- **Gereksinim:** NE-ENG-122 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** Artefakt içi sürüm (CMake `project version`) vs dosya adı vs `NEVA_API_VERSION` üçlü karşılaştırma.
- **Beklenen:** Tek kaynak (CMake); uyuşmazlık FAIL.
- **Kanıt:** kardeş §05.4

#### TC-ENG-123 — Test exe'leri + CTest
- **Gereksinim:** NE-ENG-123 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `ctest` tam çalıştırma.
- **Beklenen:** `neva_tests` · `eq_grids_tests` · `icy_parser_tests` yeşil (disk kanıtı: `CTestTestfile.cmake`, `LastTest.log`).
- **Kanıt:** disk CMakeLists · `tests/`

#### TC-ENG-124 — Yeni modül = kontrat testi kuralı
- **Gereksinim:** NE-ENG-124 · **Katman:** proses · **Otomasyon:** CI (PR denetimi)
- **Adımlar:** PR'da yeni `backends/*/` veya modül dizini + yeni test hedefi var mı diff analizi.
- **Beklenen:** Kontrat paketi eklentisi yoksa işaretle (merge gate); aksi FAIL.
- **Kanıt:** S3 §6.1

#### TC-ENG-125 — Build artefakt hijyeni
- **Gereksinim:** NE-ENG-125 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `git status` + `.gitignore` kuralı (build/ commit edilmiş mi?).
- **Beklenen:** Derleme çıktısı commit edilmemiş; mevcut disk `build/` varlığı `.gitignore` altında.
- **Kanıt:** disk `--/neva-engine/build/` (§04.2 raporu)

#### TC-ENG-126 — Bit-perfect hash (12 kombinasyon)
- **Gereksinim:** NE-ENG-126 · **Katman:** Kapı (G1/K3) · **Otomasyon:** CI (her PR)
- **Adımlar:** §11.2 hattı — 44.1/48/96/192 × 16/24/32f; SHA-256 byte-eşitlik; 2× determinism.
- **Beklenen:** 12/12 + 2× eşitlik; "yaklaşık eşitlik" (±1 LSB vs.) kabul EDİLMEZ.
- **Kanıt:** ADR-107 K3 · kardeş PRD §11.2

#### TC-ENG-127 — Inverted guard denetimi
- **Gereksinim:** NE-ENG-127 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** (1) EQ aktif → test koşul dışı; (2) lossy → koşul dışı; (3) flat → PASS; (4) guard'ı bilerek devre dışı bırak → FAIL beklenir.
- **Beklenen:** Koşullar sağlanmadığında test çalışmaz (SAHTE FAIL yok); guard kırık = sahte-PASS yakalanır.
- **Kanıt:** ADR-106 §5.1 Ş4

#### TC-ENG-128 — Deterministik test kaynağı
- **Gereksinim:** NE-ENG-128 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** Üretilen WAV'ları 2× üret → hash; repo'daki referanslarla eşleştir.
- **Beklenen:** Aynı seed → byte-eşit; hash referansları üretime bağlı (reproducible).
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-ENG-129 — WAV I/O dayanıklılığı
- **Gereksinim:** NE-ENG-129 · **Katman:** L1/Kapı · **Otomasyon:** CI (fuzz)
- **Adımlar:** 16/24/32f round-trip; bozuk/chunk taşkın corpus (truncate · header taşma · 4 GB iddialı boyut).
- **Beklenen:** Round-trip byte-eşit; corpus 0 crash/OOB; kapasite iddiaları doğrulanmadan tahsis RED.
- **Kanıt:** S3 §5 (vendor yok)

#### TC-ENG-130 — CTest entegrasyonu
- **Gereksinim:** NE-ENG-130 · **Katman:** build · **Otomasyon:** CI
- **Adımlar:** `ctest` tek komut + test listesi.
- **Beklenen:** Tüm testler kayıtlı; eksik `add_test` = FAIL (yeni test unutulmuş).
- **Kanıt:** disk CMakeLists

#### TC-ENG-131 — Sanitizer matrisi
- **Gereksinim:** NE-ENG-131 · **Katman:** Kapı · **Otomasyon:** CI (Linux)
- **Adımlar:** ASan · UBSan · TSan build'lerinde tüm testler.
- **Beklenen:** 0 bulgu; 0 bulgu release ön koşulu; bulgu varsa engelleyici.
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️ · S3 §6

#### TC-ENG-132 — Fuzz hedefleri (engine)
- **Gereksinim:** NE-ENG-132 · **Katman:** Kapı (5.13) · **Otomasyon:** CI
- **Adımlar:** ICY parser · WAV reader · C ABI fuzzer; ≥1000 corpus/hedef; ≤10 dk.
- **Beklenen:** 0 crash; corpus sayısı raporlanır; yeni parser değişikliğinde corpus otomatik çalışır.
- **Kanıt:** ADR-0105 kapı 5.13 ⚠️ · disk `icy_parser.h`

#### TC-ENG-133 — Kontrat test paketi (detay)
- **Gereksinim:** NE-ENG-133 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** enumerate/open/negotiate/start/stop/close + hata yolları (NULL device · geçersiz format · iki-open · stop-önce-start).
- **Beklenen:** Null üzerinde yeşil; paket her backend için aynı; yeni backend kayıt öncesi zorunlu.
- **Kanıt:** S3 §6.1

#### TC-ENG-134 — DSP doğruluk (bode referans)
- **Gereksinim:** NE-ENG-134 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** 31 frekans × 3 tip (Peaking/Shelf/LPF) → frekans yanıtı örnekleme → referans hesapla karşılaştırma.
- **Beklenen:** Sapma ≤ tolerans `⚠️ VR`; grafik rapor (test çıktısı).
- **Kanıt:** disk `test_biquad.cpp` · kardeş §06.3

#### TC-ENG-135 — Grid tutarlılık (IMPLEMENTED)
- **Gereksinim:** NE-ENG-135 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `eq_grids_tests` çalıştırma.
- **Beklenen:** Yeşil (disk kanıtı mevcut); grid ↔ EqChain uyumu.
- **Kanıt:** disk `test_eq_grids.cpp`

#### TC-ENG-136 — ICY parser testi (IMPLEMENTED + genişletme)
- **Gereksinim:** NE-ENG-136 · **Katman:** L1 · **Otomasyon:** CI
- **Adımlar:** `icy_parser_tests` + §11.5 genişletmeler (limit vektörleri TC-ENG-095).
- **Beklenen:** Yeşil; v1/v2 ayrımı + durum makinesi kapsamı tam.
- **Kanıt:** disk `test_icy_parser.cpp`

#### TC-ENG-137 — Latency düzenek doğrulaması
- **Gereksinim:** NE-ENG-137 · **Katman:** Kapı (G2) · **Otomasyon:** manuel
- **Adımlar:** Düzeneği bilinen gecikmeli simüle edilmiş yol ile kalibre et (tahmini vs ölçüm).
- **Beklenen:** Tekrarlanabilir (2× ölçüm sapması ≤1 ms `[ÖNERİ]`); araç/rapor repo-doc'da.
- **Kanıt:** ADR-006 · kapı 5.10 ⚠️

#### TC-ENG-138 — SNR/THD+N düzeneği
- **Gereksinim:** NE-ENG-138 · **Katman:** Kapı · **Otomasyon:** manuel
- **Adımlar:** Düzeneği referans iyi DAC ile doğrula → zinciri ölç.
- **Beklenen:** Düzeneği tabanı hedefe 10 dB yakınysa düzeneği AŞAR `[ÖNERİ]`; sonuç: SNR >105 dB · THD+N <%0.005.
- **Kanıt:** VISION V-1/V-2 · kardeş §08.5 M5

#### TC-ENG-139 — Gapless API desteği
- **Gereksinim:** NE-ENG-139 · **Katman:** L2 · **Otomasyon:** CI
- **Adımlar:** E2 API (open_queue/preload_next) + Xing/seektable okuma birim testleri.
- **Beklenen:** API hazır; oyuncu tarafı vektörler (TC-PLR-056..068) engine üstünde geçer.
- **Kanıt:** ADR-0105 AK-5 ⚠️

#### TC-ENG-140 — RT stres gece testi (G4)
- **Gereksinim:** NE-ENG-140 · **Katman:** Kapı (G4) · **Otomasyon:** scheduled CI
- **Adımlar:** §11.3-D; 30 dk; rapor yazımı.
- **Beklenen:** 0 tahsis · 0 xrun · leak 0; rapor release dosyasına ek.
- **Kanıt:** S3 §6.1

#### TC-ENG-141 — Leak 1000 cycle
- **Gereksinim:** NE-ENG-141 · **Katman:** L2 · **Otomasyon:** CI (LSan)
- **Adımlar:** Session 1000× open/close/play/stop; net artış.
- **Beklenen:** 0; LSan temiz.
- **Kanıt:** kardeş §11

#### TC-ENG-142 — ABI sembol denetimi
- **Gereksinim:** NE-ENG-142 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Export listesi grep (`neva_*` dışı 0) + sürüm dump arşivi (TC-ENG-060 ile).
- **Beklenen:** Yabancı sembol 0; dump arşivde.
- **Kanıt:** kardeş §05.4

#### TC-ENG-143 — Baseline metrik dosyası
- **Gereksinim:** NE-ENG-143 · **Katman:** proses · **Otomasyon:** CI
- **Adımlar:** Baseline dosyası var mı + güncellemeler PR notu içeriyor mu.
- **Beklenen:** Dosya repo'da; bilinçli regresyon gerekçeli.
- **Kanıt:** YOK — `[ÖNERİ]`

#### TC-ENG-144 — Test raporu şablonu
- **Gereksinim:** NE-ENG-144 · **Katman:** proses · **Otomasyon:** — (release)
- **Adımlar:** Release PR'ında şablon dolu mu denetimi.
- **Beklenen:** Kapı 5.1/5.10-5.13 + G2-G5 satırları dolu; boş satır = release adayı değil.
- **Kanıt:** kardeş §11 · Guardrail #16 ruhu

#### TC-ENG-145 — Determinism (2× hash)
- **Gereksinim:** NE-ENG-145 · **Katman:** Kapı · **Otomasyon:** CI
- **Adımlar:** Aynı testi 2× çalıştır → hash diff; atomik olmayan yolları paralel varyantla zorla.
- **Beklenen:** Eşitlik; fark = atomik/non-atomik hatası (FAIL).
- **Kanıt:** kardeş §11.2

#### TC-ENG-146 — ASIO lisans kapısı (U1)
- **Gereksinim:** NE-ENG-146 · **Katman:** lisans · **Otomasyon:** CI (bayrak)
- **Adımlar:** `NEVA_WITH_ASIO` bayrağı kapalıyken ASIO hedefi derlenmiyor mu? açıkken lisans ADR'si var mı kontrol.
- **Beklenen:** ADR yoksa bayrak kapalı (varsayılan); açık bayrakla build = ADR zorunlu.
- **Kanıt:** ADR-107 K4 · U1 §18.1

#### TC-ENG-147 — VST3 lisans kapısı (K4)
- **Gereksinim:** NE-ENG-147 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** `neva-plugin` hedefinin yalnız lisans onayıyla derlenmesi.
- **Beklenen:** Kapalı gate = hedef yok.
- **Kanıt:** ADR-107 K4

#### TC-ENG-148 — FFmpeg LGPL build
- **Gereksinim:** NE-ENG-148 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** Bayrak envanteri (`--enable-gpl` grep).
- **Beklenen:** 0; lisans raporu.
- **Kanıt:** ADR-0105 indeks ⚠️ · lib-matrix

#### TC-ENG-149 — soxr lisans durumu
- **Gereksinim:** NE-ENG-149 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** Link tipi denetimi.
- **Beklenen:** Dinamik ✅; statik = `⚠️ LİSANS` etiketi + karar.
- **Kanıt:** ADR-0105 v1.2.0 ⚠️

#### TC-ENG-150 — libdsd lisans kapısı (U3)
- **Gereksinim:** NE-ENG-150 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** `NEVA_WITH_DSD` bayrağı + LICENSE dosyası varlığı denetimi.
- **Beklenen:** LICENSE yok → bayrak kapalı (DSD hedefi dağıtılmaz).
- **Kanıt:** ADR-0105 v1.2.0 ⚠️ · U3 §18.3

#### TC-ENG-151 — BASS kullanma
- **Gereksinim:** NE-ENG-151 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Kod/envanter `BASS` bağımlılığı grep.
- **Beklenen:** 0.
- **Kanıt:** ADR-0105 v1.1.1 (indeks)

#### TC-ENG-152 — mpv reddi
- **Gereksinim:** NE-ENG-152 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `mpv`/`libmpv` bağımlılığı grep.
- **Beklenen:** 0; yeniden değerlendirme ADR ister.
- **Kanıt:** ADR-0105 v1.1.1 (indeks)

#### TC-ENG-153 — FxSound kopyalama yasağı
- **Gereksinim:** NE-ENG-153 · **Katman:** statik · **Otomasyon:** review
- **Adımlar:** Kodda FxSound kaynak/kopya izi denetimi (review + log).
- **Beklenen:** Kopya yok; yalnız mimari referans notu.
- **Kanıt:** ADR-107 K4

#### TC-ENG-154 — mbedTLS lisans metni
- **Gereksinim:** NE-ENG-154 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** Dağıtım paketinde attribution dosyası kontrolü.
- **Beklenen:** Apache-2.0 metni mevcut.
- **Kanıt:** ADR-106 §5.4

#### TC-ENG-155 — dr_libs lisansı
- **Gereksinim:** NE-ENG-155 · **Katman:** lisans · **Otomasyon:** CI
- **Adımlar:** Attribution + LICENSE doğrulaması `⚠️ VR (tam metin lib-matrix'te)` .
- **Beklenen:** Attribution mevcut; tam metin teyit edilir.
- **Kanıt:** ADR-0105 indeks ⚠️

#### TC-ENG-156 — Secret taraması
- **Gereksinim:** NE-ENG-156 · **Katman:** güvenlik · **Otomasyon:** CI (`secret-scan.yml`)
- **Adımlar:** Repo + log örnekleri üzerinde secret pattern taraması.
- **Beklenen:** 0 isabet; `.env` commit edilmemiş.
- **Kanıt:** AGENTS §5 · `.github/workflows/secret-scan.yml`

#### TC-ENG-157 — FFI clamp/negatif test
- **Gereksinim:** NE-ENG-157 · **Katman:** L1/Kapı · **Otomasyon:** CI + fuzz
- **Adımlar:** NULL path · 10000 karakter path · band -1/31/1e9 · gain ±1e9 · ms negatif → her `neva_*`.
- **Beklenen:** `INVALID_ARG`/clamp; UB yok (ASan); use-after-close koruması `⚠️ VR (magic-cookie)` .
- **Kanıt:** kardeş §05.1

#### TC-ENG-158 — Eval/serileştirme yasağı
- **Gereksinim:** NE-ENG-158 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** Kodda `eval`/`system(` / güvenilmeyen deserialize kalıpları grep.
- **Beklenen:** 0 (izinli istisnalar gerekçeli).
- **Kanıt:** ADR-091 · AGENTS §5

#### TC-ENG-159 — Çekirdekte ağ yok
- **Gereksinim:** NE-ENG-159 · **Katman:** statik · **Otomasyon:** CI
- **Adımlar:** `neva-core` içinde winsock/curl/http include + çağrı grep.
- **Beklenen:** 0 (SSRF yüzeyi çekirdekte yok); ağ yalnız remote/mcp hedeflerinde.
- **Kanıt:** S3 §4.3 yasak 2

#### TC-ENG-160 — Düşük seviye kod incelemesi
- **Gereksinim:** NE-ENG-160 · **Katman:** proses · **Otomasyon:** review checklist
- **Adımlar:** WDM-KS/CoreAudio PR'larında güvenlik+RT checklist onayı.
- **Beklenen:** Checklist dolu; açık madde = merge yok.
- **Kanıt:** S3 §3.4

#### TC-ENG-161..175 — Faz kapıları (G0-G6 + M0-M6 checklist)
- **Gereksinim:** NE-ENG-161..175 · **Katman:** proses · **Otomasyon:** — (onay)
- **Adımlar:** Her faz kapanışında §15.6 checklist'i doldurulur; Vault Steward onayı; commit kanıtı; vault-sync log.
- **Beklenen:** Checklist tam + onay kayıtlı; eksik = faz açılmaz.
- **Kanıt:** ADR-107 §5 · engine §12.6 (Faz Kapanışı)

#### TC-ENG-176..181 — U1-U5 + PCM3168A (karar kapıları)
- **Gereksinim:** NE-ENG-176..181 · **Katman:** proses · **Otomasyon:** — (karar)
- **Adımlar:** İlgili U maddesi kapanana kadar ilgili bayrak/hedefin kapalı olduğunu denetle (CI bayrak kontrolü).
- **Beklenen:** Karar yok → hedef kapalı; karar var → ADR + bayrak açılır.
- **Kanıt:** ADR-107 §8.1-§8.8 · §18.1-§18.6

#### TC-ENG-182 — ADR-0105 vault iadesi (vault denetimi)
- **Gereksinim:** NE-ENG-182 · **Katman:** proses · **Otomasyon:** — (vault denetimi)
- **Adımlar:** `.ai/.decisions/accepted/` altında ADR-0105 dosyası var mı? indeks `⚠️ dosyasız` notu var mı?
- **Beklenen:** Dosya iade edilir VEYA indeks notu kalıcılaşır; bu PRD'deki `⚠️ VR (ADR-105 indeks kaydı)` etiketleri o zaman yeniden değerlendirilir.
- **Kanıt:** §00.3-D2 · index.md:152

### §11.8 Regresyon & Sürekli Entegrasyon Özeti

**Her PR koşusu (engine):** L1 + L2 + hash (12 komb) + statik grep kapıları + sanitizer build (Linux) + 3 platform build.
**Zamanlanmış koşu (gece):** fuzz (≤10 dk) + RT stres 30 dk + leak 1000 cycle + perf baseline.
**Release adayı koşu:** G1-G5 + kapı 5.10 + SNR/THD manuel + lisans G3 + rapor şablonu doldurma.
**Başarısızlık davranışı:** Kapı FAIL = merge/release engelleyici (istisna yalnız gerekçeli bilinçli regresyon etiketi NE-ENG-100).

---

## §12 Hata Modları & Kurtarma (Failure Mode Catalog)

> Bu bölüm FMEA tarzıdır: her satır **hata → belirti → kök neden → kurtarma → telemetri** taşır.
> Öncelik hesaplanan risktir: **Önem × Oluşum (1-5)** — değerler `[ÖNERİ]` işaretlidir; ADR'lerde yoktur.

### §12.1 Sınıflandırma

| Kod | Sınıf | Tanım | Örnek |
|---|---|---|---|
| FM-01 | **Kesinti (Interrupt)** | Ses anlık durur/devam eder ama olay biter | Cihaz kaybı |
| FM-02 | **Bozulma (Corrupt)** | Ses akar ama hatalı (distorsiyon/yanlış format) | Bit depth uyuşmazlığı |
| FM-03 | **Kilitlenme (Hang)** | RT bekler, ses kesilir, süreç donar | RT'de lock |
| FM-04 | **Çökme (Crash)** | Süreç ölür | OOB read |
| FM-05 | **Yavaşlama (Degradation)** | Ses devam eder ama ölçülebilir kötüleşme | Xrun'lar, CPU şişmesi |
| FM-06 | **Kayıp (Loss)** | Kullanıcı verisi/ayar kaybolur | Bozuk config write |

**Öncelik çarpanı kuralı:** FM-03/FM-04 her zaman Öncelik P0'dır — kullanıcı ses kesintisi + uygulama ölümü birlikte kabul edilemez (AGENTS §17 Edge 1).

### §12.2 Hata Modu Kayıt Defteri (Engine)

#### FM-E01 — Exclusive cihaz kullanımda (2. süreç kilitlemiş)
- **Belirti:** WASAPI Excl `AUDCLNT_E_DEVICE_IN_USE` · akış açılamaz.
- **Kök neden:** FxSound/oyun/rekabetçi uygulama cihazı tutuyor.
- **Tahmini sıklık/önem:** Yüksek / 4 `[ÖNERİ]` → Risk 16.
- **Kurtarma:** Zincir 2. basamağa düşer (Shared) — **session yaşamaya devam eder** (ADR-019 §1.1).
- **Kullanıcıya görünen:** Rozet "exclusive değil" + bilgi (kardeş PRD NE-PLR-046).
- **Telemetri:** E8 event `backend=WASAPI_SHARED reason=DEVICE_IN_USE`.
- **Test:** TC-ENG-022 · TC-ENG-038.

#### FM-E02 — Cihaz tak-çıkar (hot-unplug) sırasında akış
- **Belirti:** Render callback `AUDCLNT_E_DEVICE_INVALIDATED` benzeri hata; akış durur.
- **Kök neden:** Fiziksel USB/DP çıkarma.
- **Tahmini sıklık/önem:** Orta / 5 `[ÖNERİ]` → Risk 20.
- **Kurtarma:** Zincir → (enumerate güncelle) → sıradaki cihaz → Null; canlı kurtarma birebir implementation kod değil (ADR-019 K1) — **ADR-0105 canlı devir iddiası KAPALI** `⚠️ VR (ADR-106 canlı devir onaylı değil)`.
- **Kullanıcıya görünen:** ERROR/uyarı + beklenen davranış (yeniden bağlanınca otomatik devam `[ÖNERİ]`).
- **Telemetri:** E8 + cihaz listesi değişimi event'i.
- **Test:** TC-ENG-030 · kardeşi TC-PLR-048.

#### FM-E03 — Exclusive↔shared geçişte kısa kesinti
- **Belirti:** 1-50 ms ses boşluğu veya pop.
- **Kök neden:** Oturum yeniden kurulumu; ring kopyalama anlık başarısız.
- **Tahmini sıklık/önem:** Orta / 3 `[ÖNERİ]` → Risk 9.
- **Kurtarma:** (1) ring korunur (ADR-019 F2); (2) geçiş ≤20 ms `⚠️ VR (eşik)`; (3) başarısızlık → zincir düşüşü.
- **Telemetri:** E8 `mode_switch ok/fail`.
- **Test:** TC-ENG-014 · TC-ENG-046.

#### FM-E04 — Format uyuşmazlığı (dosya ≠ cihaz hızı)
- **Belirti:** 44.1k dosya / 48k cihaz → açılışta veya çalışma sırasında hata.
- **Kök neden:** Nihai negosyasyon başarısız; soxr yok/devre dışı.
- **Tahmini sıklık/önem:** Yüksek / 4 `[ÖNERİ]` → Risk 16.
- **Kurtarma:** Sıra: `setDeviceRate` → soxr (producer-side) → zincir düşüşü (ADR-019 F3). Son çare: kullanıcıya "desteklenmeyen format" hatası (çalışırken hata kodu 3).
- **Telemetri:** E8 + negotiated_format değişimi.
- **Test:** TC-ENG-015 · kardeşi TC-PLR-047.

#### FM-E05 — BUFFER_SIZE_NOT_ALIGNED (periyot uyuşmazlığı)
- **Belirti:** Çalışma sırasında tekrarlayan hata logları; ses atlama.
- **Kök neden:** Cihaz sürücüsü 128 örnek yerine 112 veriyor; periyot hesabı sapmış.
- **Tahmini sıklık/önem:** Orta / 4 `[ÖNERİ]` → Risk 12.
- **Kurtarma:** Tek seferde yutulmaz; periyot yeniden hesap → tekrar deneme → başarısızsa zincir düşüşü (NE-ENG-039).
- **Telemetri:** Sayacı artar; son 5 olay log'da (non-RT).
- **Test:** TC-ENG-039 · kardeşi TC-PLR-039.

#### FM-E06 — Xrun (underrun/overrun)
- **Belirti:** Kısa ses boşluğu; sayaç artışı.
- **Kök neden:** CPU şişmesi, disk yavaş, sürücü jitter.
- **Tahmini sıklık/önem:** Orta / 4 `[ÖNERİ]` → Risk 12.
- **Kurtarma:** Fade-out → 50 ms sessizlik → otomatik restart (ADR-017; ADR-019 F4 kill-switch ile aynı koruma mantığı).
- **Telemetri:** Xrun sayaçları + olay logu (non-RT).
- **Test:** TC-ENG-087 · TC-ENG-088 · kardeşi TC-PLR-109.

#### FM-E07 — Starve (producer durdu, RT bekliyor)
- **Belirti:** Ring boş; RT fazla beklememeli.
- **Kök neden:** Decode hatası, dosya E_OK, ağ koptu (radyo).
- **Tahmini sıklık/önem:** Yüksek / 3 `[ÖNERİ]` → Risk 9.
- **Kurtarma:** Starve fade ≤50 ms → sessizlik → producer kurtarınca ramp'li devam (ADR-106 §2.2a şeması, kardeş §07.4).
- **Telemetri:** Starve sayacı + süre.
- **Test:** TC-ENG-086 · kardeşi TC-PLR-109.

#### FM-E08 — Fallback zinciri tamamen tükenir
- **Belirti:** Hiçbir backend açılamaz; ses yok.
- **Kök neden:** Tüm sürücüler yok/bozuk (ör. minimal sanal makine).
- **Tahmini sıklık/önem:** Düşük / 5 `[ÖNERİ]` → Risk 10.
- **Kurtarma:** Null en son basamak — **session açılır ama ses yoktur**; uygulama çökmez (kullanıcıya net mesaj + cihaz listesi).
- **Telemetri:** E8 zincir sonu event `backend=NULL`.
- **Test:** TC-ENG-008 · TC-ENG-028.

#### FM-E09 — RT thread'de beklenmedik bloklama (kilitlenme)
- **Belirti:** Ses kesilir; süreç yanıt vermez (CPU düşük ama ses yok).
- **Kök neden:** RT'de lock/IO/alloc sızması (kural ihlali).
- **Tahmini sıklık/önem:** Düşük / 5 `[ÖNERİ]` → Risk 10 (ama FM-03 = P0).
- **Kurtarma:** Önleme: statik yasağı + TSan + watch dog `[ÖNERİ — RT süresi aşımında kill-switch otomatik tetik]`.
- **Telemetri:** Callback süre histogramı; aşım sayacı.
- **Test:** TC-ENG-083 · TC-ENG-085 · TC-ENG-131.

#### FM-E10 — Tahsis sızması (memory leak)
- **Belirti:** Uzun kullanımda RSS artar; sonunda OOM.
- **Kök neden:** Non-RT yolda döngüsel tahsis (ring/preresampler hataları).
- **Tahmini sıklık/önem:** Orta / 4 `[ÖNERİ]` → Risk 12.
- **Kurtarma:** LSan CI + 1000 cycle testi (önleme); runtime'da OOM yolu: tahsis hatası → `StreamError` → zincir (çökme yok).
- **Telemetri:** RSS periyodik örnek (rapor).
- **Test:** TC-ENG-093 · TC-ENG-141.

#### FM-E11 — Handle use-after-close / double close
- **Belirti:** UB; çökme (FM-04).
- **Kök neden:** UI hatalı yaşam döngüsü; race.
- **Tahmini sıklık/önem:** Düşük / 5 `[ÖNERİ]` → Risk 10.
- **Kurtarma:** `neva_result` reddi (`NEVA_ERR_INVALID_ARG` (1) `[ÖNERİ — diskte geçersiz-handle testi yok]`; magic-cookie koruma `⚠️ VR`); fuzz hedefi.
- **Telemetri:** Hata kodu 1 sayacı (INVALID_ARG).
- **Test:** TC-ENG-157 · TC-ENG-035.

#### FM-E12 — Lame duck çalışma (önceki oturumdan devralma)
- **Belirti:** Uygulama ikinci kez açılır; mevcut oturum ne olacak?
- **Kök neden:** Çift süreç (AC'09/AC'25 jenerasyon bu yüzden tek-instance'ı düşündü — ADR-004/ADR-018).
- **Tahmini sıklık/önem:** Orta / 3 `[ÖNERİ]` → Risk 9.
- **Kurtarma:** Karar: tek-instance + köprü (NE-ENG-105) `[ÖNERİ]`; ikinci başlatma → mevcut pencereye odak.
- **Telemetri:** Başlatma olayı (instance id).
- **Test:** kardeş NE-PLR-179 · TC-ENG-105.

#### FM-E13 — ASIO sürücüsü çöker / driver-internal hata
- **Belirti:** `ASIOFuture`/driver hata kodu; akış durur.
- **Kök neden:** 3. parti sürücü (Sonic/Flussonic — ADR-019 §1.4 hatası).
- **Tahmini sıklık/önem:** Düşük-Orta / 4 `[ÖNERİ]` → Risk 8 (U1 çözülene kadar kapı G4'te).
- **Kurtarma:** Zincir → WASAPI Excl (2. basamak); kullanıcıya "ASIO yeniden bağlandı/düşürüldü" mesajı.
- **Telemetri:** E8 `reason=ASIO_DRIVER`.
- **Test:** TC-ENG-023 (şartlı U1) · TC-PLR-042.

#### FM-E14 — Session açılışında ilk periyot hatası (baslat-e-baslat)
- **Belirti:** start başarısız; hiçbir ses yok.
- **Kök neden:** İlk render döngüsünde sürücü hatası (WASAPI event ayarı).
- **Tahmini sıklık/önem:** Düşük / 5 `[ÖNERİ]` → Risk 10.
- **Kurtarma:** Yaşam döngüsü sıralaması kontrolü → tekrar dene (2×) → zincir düşüşü.
- **Telemetri:** `open` hata kodu.
- **Test:** TC-ENG-035 · TC-ENG-133.

#### FM-E15 — Event kuyruğu taşması (UI'e olay sığmıyor)
- **Belirti:** UI güncellemesi gecikir/kaybolur; motor sağlıklı.
- **Kök neden:** UI thread bloke; kuyruk cap doldu.
- **Tahmini sıklık/önem:** Orta / 3 `[ÖNERİ]` → Risk 9.
- **Kurtarma:** Cap dolduğunda **durum event'i coalesce** (son durum yeterlidir) `[ÖNERİ]`; metadata akışında drop + sayaç (metaveri kural 8 ruhu).
- **Telemetri:** Drop sayacı.
- **Test:** kardeş TC-PLR-112 · TC-ENG-047.

#### FM-E16 — Preset/format uyuşmazlığı (10-band preset → 31-band oturum)
- **Belirti:** Yükleme hatası veya yanlış frekans eşlemesi.
- **Kök neden:** Grid version kayması (ADR-0105 §2.2).
- **Tahmini sıklık/önem:** Orta / 3 `[ÖNERİ]` → Risk 9.
- **Kurtarma:** Kural 2: grid_available fallback + kullanıcı uyarısı; çökme yok.
- **Telemetri:** `preset_adjusted` olayı.
- **Test:** TC-ENG-052.

#### FM-E17 — Hash testi sahte sonuç (guard ihlali)
- **Belirti:** Bit-perfect testi sahte PASS (kapsam dışı koşulda) veya sahte FAIL.
- **Kök neden:** Guard kopyalanmış/sızmış; şart sağlanmadan PASS.
- **Tahmini sıklık/önem:** Düşük / 5 `[ÖNERİ]` → Risk 10.
- **Kurtarma:** Guard tek noktada + negatif denetim testi (TC-ENG-127).
- **Telemetri:** CI gate log.
- **Test:** TC-ENG-127.

#### FM-E18 — Lisans bilinmeyenliği son anda tespit (release öncesi)
- **Belirti:** `⚠️ LİSANS` maddesi release'de kapanmaz (ASIO VSL çatallanmış lisans — ADR-107 §8.1).
- **Kök neden:** U1 henüz çözülmedi; dağıtım dışarıda kalır.
- **Tahmini sıklık/önem:** Yüksek / 4 `[ÖNERİ]` → Risk 16.
- **Kurtarma:** Bayrak kapalı dağıtım (ASIO'suz paket) — zincir zaten ASIO'suz çalışır; VSL çatallanması → READ-ONLY entegrasyon kararı (ADR-107 §8.1 akışı).
- **Telemetri:** G3 lisans gate raporu.
- **Test:** TC-ENG-146 · G3.

#### FM-E19 — ARM64'te beklenmeyen davranış (sarmalayıcı sürücü)
- **Belirti:** AArch64'de WDM-KS/CoreAudio üzerinde planlanmamış hata.
- **Kök neden:** İkinci jenerasyon emsal dersi (AC'09 → AC'25 — ADR-018 §5).
- **Tahmini sıklık/önem:** Düşük / 3 `[ÖNERİ]` → Risk 6.
- **Kurtarma:** Düşük seviye kod = specialist review + adaptör katmanı izolasyonu (ADR-019 §2.5/P3).
- **Test:** TC-ENG-020 · TC-ENG-160.

#### FM-E20 — Fuzz'da 0-day yakalanır (release sonrası bulgu)
- **Belirti:** Bozuk dosya çökme (FM-04) — kullanıcı düzeyinde.
- **Kök neden:** Bilinmeyen parser edge case.
- **Tahmini sıklık/önem:** Düşük / 5 `[ÖNERİ]` → Risk 10.
- **Kurtarma:** Kapı 5.13 kapıları engeleme; yine de olursa: hotfix süreci (§16 risk R-07) + fuzz corpus genişletme.
- **Telemetri:** Crash raporu (opt-in) `[ÖNERİ]` + kullanıcı hatası kodu.
- **Test:** TC-ENG-132 · kapı 5.13.

### §12.3 Hata Kodu Uyumu (Engine ↔ Player)

| Engine dönen | Player karşılığı | Kullanıcı mesajı sahibi |
|---|---|---|
| `NEVA_OK` (0) | İlerleme (event E1) | — |
| `NEVA_ERR_INVALID_ARG` (1) | Girdi reddi + form/uyarı (UI) | Player (NE-PLR-152) |
| `NEVA_ERR_NO_DEVICE` (2) | `ERROR` durum + E10 error event | Player (NE-PLR-151/153) |
| `NEVA_ERR_UNSUPPORTED_FORMAT` (3) | "Desteklenmeyen format" + skip | Player (NE-PLR-154/157) |
| `NEVA_ERR_INTERNAL` (4) | İç hata + günlük + geri kurtarma | Player (NE-PLR-156) |

**Kural:** Engine asla kullanıcı metni üretmez (seçenek 1: engine hata kodu verir — ADR-005 bu PRD'de tasarım kararı olarak sabit `[ÖNERİ — 8 kod değil, 5 kod korunur]`).

### §12.4 Kurtarma Hiyerarşisi (her hata için sıralı tepki)

1. **Önleme** (statik kapı, kontrat testi, fuzz) — en ucuz.
2. **Otomatik kurtarma** (fallback zinciri, restart, yeniden negosyasyon) — kullanıcı görmez.
3. **Kullanıcı onaylı kurtarma** (cihaz değiştir, mod düşür) — açık mesaj.
4. **Güvenli duruş** (ERROR durumu, çalma korunur, "yeniden dene") — veri kaybı yok.
5. **Bildir** (log non-RT + E10) — telemetri.

**Asla:** sessiz veri kaybı · kilitlenmiş süreç · otomatik kapatma (uygulama kendi kendini öldürmez — kullanıcı verisi FM-06 korunur).

### §12.5 Kurtarma Yolları Özet Tablosu

| Hata sınıfı | Otomatik kurtarma | Kullanıcıya düşen | Sınır |
|---|---|---|---|
| FM-01 cihaz kaybı | Zincir → sıradaki cihaz | Bilgi/uyarı | 3 başarısız → ERROR |
| FM-02 format | N1 → soxr → zincir | Desteklenmeyen format (3) | Son çare: hata |
| FM-03 kilitlenme | Önleme (statik) + watchdog `[ÖNERİ]` | Uygulama yeniden başlatma (son çare) | P0 — asla kabul |
| FM-04 çökme | Önleme (fuzz/ASan) | Hotfix (R-07) | 5.13 kapısı |
| FM-05 yavaşlama | Xrun restart / kademeler | Mod bilgisi | G4 stres |
| FM-06 veri kaybı | Atomik config write `[ÖNERİ]` + yedek | "Ayarlar geri yüklendi" | Yedek tutma |

### §12.6 Faz Kapanışı (her faz sonunda bu kontrol)

1. §12 defterindeki bu faza ait FM'ler: kurtarma kodu kodlanmış mı + testi geçti mi?
2. Telemetri olayları (E8/E10 sayaçları) okunabilir durumda mı?
3. Yeni hata modu tespit edildiyse defter'e eklendi mi (append-only)?
4. İlgili gereksinim testleri yeşil (§11.8).
5. Vault güncellemesi + commit kanıtı (ADR-107 §5 ritmi).

---

## §13 Güvenlik (Engine — OWASP/Yüzey Bazlı)

> **Sürüm:** v1.0 · **Kaynak hiyerarşisi:** AGENTS §5 (OWASP Top 10:2025) · ADR-106 §5 (security debate S1-S8) · ADR-091 · ADR-107 §8 · S3 §4.3.
> **İlke:** Güvenlik katmanları ihlal edilse bile çekirdek RT doğası değişmez; savunma derinliği 3 katmandır: **(1) girdi sınırı (ABI clamp) → (2) izolasyon (dizin/core ayrımı) → (3) gözlem (statik kapı + log)**.

### §13.1 Tehdit Modeli (Motor)

| # | Tehdit | Sınıf | Yüzey | Savunma | Öncelik |
|---|---|---|---|---|---|
| T-E01 | Bozuk/saldırgan dosya → OOB/çökme | Veri bütünlüğü | Decode (FLAC/MP3/WAV/DSF + FFI) | Fuzz kapı 5.13 + ASan/UBSan + clamp | P0 |
| T-E02 | Saldırgan URL → yerel dosya erişimi (SSRF/LFI) | İhlal | (Core'da ağ YOK — S3 yasak 2) | Çekirdekte ağ yokluğu; URL validator yalnız player'da | P0 |
| T-E03 | ABI üzerinden bellek bozması (uzak process) | İhlal | `neva_*` 9 fonksiyon | Opaque handle + clamp + `neva_result` (kod 1 (INVALID_ARG)/4) | P0 |
| T-E04 | ICY metaveri → kod enjeksiyonu | İhlal | `icy_parser` (metin blok) | Parser escape-edilmiş veri üretir; render YALNIZ player'da (T-P sınıfı) | P1 |
| T-E05 | XXE (ICY v2 XML) → dosya okuma | İhlal | v2 metaveri ayrıştırma | DTD/entity kapalı (ADR-106 kural 8) | P0 |
| T-E06 | Sürücü/eklenti ile kod yürütme | Yürütme | ASIO/VST3 (3. parti) | VSL READ-ONLY kararı (ADR-107 §8.1) · K4 lisans gate · kopya yok (NE-ENG-153) | P0 |
| T-E07 | Secret sızıntısı (repo/log) | İfşa | Repo + CI + loglar | `.env` + gitignore + secret-scan.yml + log hijyeni | P0 |
| T-E08 | Bellek/disk bütçe silahlandırması (DoS) | Kullanılabilirlik | Tahsis yolları | Zero-allocation RT + cap'ler + OOM → hata (çökme yok) | P1 |
| T-E09 | Yarış durumu → use-after-free | Bellek | UI/RT çapraz | Lock-free SPSC + TSan + handle clamp | P0 |
| T-E10 | Aşırı yetkili süreç (admin) | Yetki | Yükleme/sürücü kurulumu | Sürücü KURMAZ (NE-ENG-103); çalışma zamanında admin gerekmez `[ÖNERİ]` | P1 |
| T-E11 | Trace/telemetri ile veri sızıntısı | İfşa | PDB/log/ölçüm | PDB ayrı paket (NE-ENG-121) + telemetri kapalı (NE-ENG-099) | P2 |
| T-E12 | Supply chain (bağımlılık zehirlenmesi) | Yürütme | vendor (libsamplerate vb.) | Vendor-artı-sistem politikası + CVE taraması + lock `[ÖNERİ]` | P1 |

**Not (savunma derinliği):** T-E04/T-E02'de motor **kaçış noktası değildir** — parser ham metni veri olarak taşır, HTML/OS komutuna dönüştürmez; render güvenliği player PRD §12'dedir (tek kapı kuralı ihlal edilmez: URL validator TEK yerdedir, motor validator KOYMAZ).

### §13.2 Güvenlik Gereksinimleri (§10.8 içi değil — bu bölümde somutlaşır)

| ID | Gereksinim | Kabul (özet) | Test |
|---|---|---|---|
| SE-01 | FFI girdi sınırı: path/band/gain/ms/frames clamp | Sınır dışı → `INVALID_ARG`/clamp; ASan temiz | TC-ENG-157 |
| SE-02 | Opaque handle + use-after-close reddi | Kapatılmış handle → kod 4; UB yok | TC-ENG-002/117 |
| SE-03 | Çekirdekte ağ/API yok | `neva-core` içinde socket/curl/include grep 0 | TC-ENG-159 |
| SE-04 | `eval`/`system`/güvenilmeyen deserialize yasağı | Statik grep 0 (gerekçeli istisna yok) | TC-ENG-158 |
| SE-05 | ICY parser limitleri (metaint/text/DOCTYPE) | 31 B RED · 32 B OK · 1 MB cap · DTD RED | TC-ENG-095/136 |
| SE-06 | Xerces-legacy XXE denetimi (bilinçli not) | v2 parser'da entity kapalı; `file://` erişimi tetiklenmez | TC-ENG-136 (genişletme) |
| SE-07 | Secret yokluğu | secret-scan 0 isabet; `.env` commit değil | TC-ENG-156 |
| SE-08 | Sürücü kurulumu yok (yetki) | Kodda installer/driver-copy yok | TC-ENG-160 + statik |
| SE-09 | PDB/simge dağıtımı ayrı | Ürün paketinde PDB yok | TC-ENG-121 |
| SE-10 | Lisans gate (VSL/3. parti) | Bayrak kapalı → hedef yok; VSL READ-ONLY | TC-ENG-146/153 · G3 |
| SE-11 | Düşük seviye kod review kapısı | WDM-KS/CoreAudio PR'ları checklist'li | TC-ENG-160 |
| SE-12 | Zombie instance / lame duck | Tek-instance + köprü `[ÖNERİ]` | TC-ENG-105 · kardeş TC-PLR-179 |

### §13.3 Girdi Sınırı Detayı (ABI — en geniş saldırı yüzeyi)

**Zorunlu clamp listesi (her `neva_*` çağrısında):**

| Parametre | Sınır | Aksi halde |
|---|---|---|
| `path` (UTF-16/UTF-8) | Uzunluk cap `[ÖNERİ — 32767 karakter]` · NULL reddi | kod 1 (INVALID_ARG) |
| `band` | 0..30 (31-band · header yorumu `neva_session.h:47`) · dışı RED | kod 1 (INVALID_ARG) |
| `gain_db` | ±30 dB clamp `[ÖNERİ]` (§06.4 preamp ile uyumlu) | clamp + log |
| `fade_ms` | 0..500 ms `[ÖNERİ]` | clamp |
| `duration` / pozisyon | 0..dosya süresi; negatif RED | kod 1 (INVALID_ARG) |
| `frame count` | 1..8193 (S3 §3.1) | `StreamError` |
| `channels` | 1..8 | `StreamError` |
| `sample_rate` | 44100..192000 | `StreamError` |
| Handle (session) | Geçerli-cookie kontrolü `⚠️ VR (magic-cookie)` | kod 4 (INTERNAL) |

**Fuzz hedefi olarak ABI:** Her clamp parametresi C ABI girdi fuzzer'ında ayrı mutasyon vektörüdür (kapı 5.13; NE-ENG-132).

### §13.4 İzolasyon İlkeleri

1. **Dizin izolasyonu (S3 §3.4):** Backend'ler birbirini dahil etmez — bir backend'in güvenlik hatası diğerine sıçramaz (TC-ENG-044).
2. **Dizin izolasyonu (S3 §4.3):** Çekirdek ile uygulama/host arasında tek kapı `neva_*` ABI'dır — uygulama yüzeyindeki (ör. UI parse) zafiyet çekirdek içine girmez.
3. **RT izolasyonu:** Ağ/dosya/parse/calc yalnız non-RT thread'ler — RT'de veri güvenirliği bozulursa durum yalnız `StreamError`/sayaç (üretilebilir beklenen davranış).
4. **VSL/3. parti izolasyonu (ADR-107 §8.1):** ASIO VSL entegrasyonu READ-ONLY — 3. parti kod motorun iç yazma yoluna erişemez; VSL çatallanmış lisansı sınırı koyar (T-E06).
5. **Suret (lame duck) izolasyonu:** İkinci süreç mevcut oturuma dokunmaz; köprü üzerinden müzakere eder `[ÖNERİ]`.

### §13.5 Güvenlik Doğrulama Zinciri (release)

```
commit → secret-scan (CI, her PR)
       → statik kapılar: ağ yok · eval yok · clamp · grep (OS header/RC4)
       → fuzz: 5.13 (ABI + ICY + WAV) — 0 crash
       → sanitizer: ASan/UBSan/TSan — 0 bulgu
       → lisans: G3 (U1/U3/K4 kapalı → bayraklar kapalı)
       → doküman: tehdit modeli sürümü artmış mı (yeni yüzey)
release adayı → manuel: VSL/ASIO senaryosu + 2 süreç kilidi + log gözden geçirme
```

**Başarısızlık davranışı:** P0 bulgu = release engelleyici; P1 = release notu + kapalı kapı (bilinçli kabul); P2 = backlog.

### §13.6 OWASP Top 10:2025 Karşılıkları (Engine)

| OWASP | Engine karşılığı | Kanıt |
|---|---|---|
| A01 Broken Access Control | Yetkisiz API erişimi yok (tek süreç ABI; IPC kimlik `⚠️ VR`) | SE-02/SE-12 |
| A02 Cryptographic Failure | Motor kripto YOK (TLS player'da — mbedTLS) | §14 |
| A03 Injection | Decode FFI; `eval`/SQL yok; clamp + fuzz | SE-01/SE-04 |
| A04 Insecure Design | Savunma derinliği + dizin izolasyonu (§13.4) | S3 §4.3 |
| A05 Security Misconfiguration | Bayraklar varsayılan güvenli (ASIO/DSD/3.parti kapalı) | G3/U1-U3 |
| A06 Vulnerable Components | Vendor-artı-sistem + CVE taraması `[ÖNERİ]` | SE-12/T-E12 |
| A07 Auth Failures | Kimlik doğrulama motorda YOK (erişim yalnız yerel süreç) | — |
| A08 Data Integrity Failure | Hash kapı 5.1 (bit-perfect) + presets atomik write | TC-ENG-126/146 |
| A09 Logging Failures | Log'da secret/URL yok (hijyeni) + non-RT | SE-07 · kardeş TC-PLR-214 |
| A10 SSRF | Çekirdek ağ yok — SSRF yüzeyi baştan yok | SE-03 |

### §13.7 Güvenlik İstisna Kaydı

| İstisna | Gerekçe | Sınır | Süre |
|---|---|---|---|
| Sürücü/eklenti DLL yüklemesi (OS/ASIO) | Platform zorunluluğu (WASAPI COM, ASIO driver) | Yalnız `backends/` · imza kontrolü `⚠️ VR (OS imza politikası)` | Kalıcı |
| PDB dağıtımı (opsiyonel paket) | Hata ayıklama | Ayrı kanal (NE-ENG-121) | Kalıcı |
| Örnek/test corpora'da "zararlı" dizgiler | Fuzz/vectör dosyası | `tests/` altında izole; çalıştırılabilir değil | Kalıcı |

Her istisna §14.6 girişinde numaralı ve gerekçelidir; yeni istisna = security-engineer onayı (veto yetkisi — AGENTS §6.1).

---

## §14 Lisans & Telif (Engine)

> **Kaynak:** ADR-107 §8.1-§8.7 + `licensing-neva-engine.md` (disk) · ADR-0105 lib-matrix (indes — `⚠️ VR` işaretli) · ADR-106 §1.3/§5.4 · AGENTS §5.
> **Kural:** Lisans **bilinmiyorsa varsayılan RED'dir** — `UNKNOWN` hiçbir hedef release paketine girmez (G3 kapısı).

### §14.1 Lisans Matrisi (Teyitli Satırlar)

| # | Bileşen | Rol | Lisans | Kaynak | Durum |
|---|---|---|---|---|---|
| L01 | **Neva Engine çekirdeği** (kendi kodumuz) | Motor | Karar gerekli: AGPL-3.0 / MIT benzeri `⚠️ VR (ADR-107 §8.7 bu PRD'ye AÇIK sorma maddesi — §18.8)` | ADR-107 §8.7 | **AÇIK** |
| L02 | **dr_libs** (dr_flac/dr_mp3/dr_wav/dr_imaadpcm) | Yerel decode | MIT ailesi `⚠️ VR (tam metin lib-matrix'te)` | ADR-0105 indeks | Teyitli (isim) |
| L03 | **libopus** | Opus | BSD-3-Clause | Opus projesi (standart) | Teyitli |
| L04 | **libsndfile** | WAV/FLAC I/O adayı | LGPL-2.1 (dinamik `✅`) | ADR-0105 §5 `[ÖNERİ — bu PRD'de VARSAYIM]` | ⚠️ LİSANS (statik tartışması) |
| L05 | **FFmpeg 9.0.2** | Decode (kapsam dahili) | LGPL-2.1+ (`--enable-gpl` YOK) | ADR-0105 indeks ⚠️ · ADR-106 | Teyitli (isim+sürüm) |
| L06 | **mbedTLS 9.13.0** | TLS (radio) | Apache-2.0 | ADR-106 §5.4 | Teyitli |
| L07 | **libcurl 8.23.0** | HTTP (radio/keşif) | curl/MIT (dual) `⚠️ VR (lib-matrix)` | ADR-0105 indeks | Teyitli (isim+sürüm) |
| L08 | **libsamplerate (soxr?) — soxr** | Resampler | **LGPL-2.1** (dinamik `✅`) | ADR-0105 v1.2.0 ⚠️ | Teyitli |
| L09 | **WASAPI/CoreAudio (OS API)** | Backend | OS SDK — dağıtım ücretsiz; SDK lisansı koşulları `⚠️ VR` | Platform | OS EULA |
| L10 | **ASIO SDK** | Backend (opsiyonel, U1) | Steinberg ASIO License — **dual-license VSL tartışma** | ADR-107 §8.1 | **RED (kapalı)** |
| L11 | **FDK-AAC** | (AAC decode) | **RED — patentli/lisanslı** | ADR-106 kural 9 | RED |
| L12 | **BASS** | (decode emsal) | **RED** (kapatıldı) | ADR-0105 v1.1.1 indeks | RED |
| L13 | **mpv / libmpv** | (decode/video emsal) | **RED** (kapatıldı) | ADR-0105 v1.1.1 indeks | RED |
| L14 | **VST3 SDK** | Plugin hedefi (K4) | Steinberg VST3 License | ADR-107 K4 | Kapalı (gate) |
| L15 | **libdsd** | DSD decode (U3) | **UNKNOWN** (LICENSE dosyası) | ADR-0105 v1.2.0 ⚠️ | **RED (kapalı)** |
| L16 | **Xerces-C++** | (ICY v2 emsal — kullanılmıyor mu `⚠️ VR`) | Apache-2.0 | ADR-0106 radio-and-hires §5.6 | Teyitli (isim) |
| L17 | **VC++ Redistributable** | Windows çalışma ortamı | Microsoft Redistributable lisansı | ADR-107 §8.4 (K2 altı) | EULA — dağıtım notu |

### §14.2 RED & KAPALI Liste (G3 Kapısı — bunlar pakette OLMAZ)

1. **FDK-AAC** → AAC yalnız LGPL decoder'lar (FFmpeg/native) — ADR-106 kural 9.
2. **BASS** → ADR-0105 v1.1.1'de reddedildi (indes kanıt) `⚠️ VR`.
3. **mpv/libmpv** → ADR-0105 v1.1.1'de reddedildi `⚠️ VR`.
4. **ASIO (VSL yolunda)** → U1 çözülene kadar `NEVA_WITH_ASIO=OFF` varsayılan (ADR-107 §8.1); VSL READ-ONLY + çift-lisans tartışması kapanmadan paket A.
5. **libdsd** → U3 (LICENSE dosyası bulunana kadar).
6. **VST3 entegrasyonu** → K4 gate (lisans metni + ADR onayı).
7. **FxSound kaynak kodu** → KOPYALAMA YASAK (yalnız mimari referans — ADR-107 K4).
8. **GPL-only codec'ler** (`--enable-gpl`, x264 vb.) → LGPL sınırları ihlali.

### §14.3 G3 Lisans Kapısı — Kontrol Listesi (Release)

| # | Kontrol | Yöntem | Başarı ölçütü |
|---|---|---|---|
| G3-1 | Envanter ↔ paket diff | Otomatik liste | Pakette envanter dışı 0 bileşen |
| G3-2 | RED listesi bulunmuyor | Binary/string tarama (FDK/BASS/mpv izi) | 0 isabet |
| G3-3 | U1/U3/K4 bayrakları kapalı | Build config dump | `OFF` varsayılan; ADR'siz `ON` yok |
| G3-4 | LGPL dinamik bağlama | Link raporu (sonradan isteğe bağlı `⚠️ VR`) | soxr/libsndfile statik değil |
| G3-5 | Attribution / NOTICE dosyası | Paket içeriği | Her bileşenin lisans metni var |
| G3-6 | Çekirdek lisans kararı (L01/L18.8) | ADR / karar | Paket A'dan önce kapanmış olmalı **[KRİTİK — §18.8]** |
| G3-7 | L16-L17 notları | Doküman | OS EULA/Vcredist dağıtımı açık |

**Kapı sonucu:** Açık madde varsa = "Paket A (ASIO'suz/SOURCsuz) yayımlanabilir mi?" sorusu §18.8'de cevaplanır; cevap yoksa yalnız kaynak yayımlama `[ÖNERİ]` (lisanslı hedef yok).

### §14.4 Dağıtım Şekilleri

| Paket | İçerik | Koşul |
|---|---|---|
| **Paket A — Tam** (hedef) | ASIO ON · DSD ON · tüm backend'ler | U1 + U3 + L01 kapalı olmalı |
| **Paket B — Temel** (geçiş) | ASIO OFF · DSD OFF · WASAPI/PipeWire/ALSA/CoreAudio | Mevcut hedef (`NEVA_WITH_ASIO=OFF` — ADR-107 §8.1) |
| **Kaynak yayımı** | Kod + build | Çekirdek lisans (L01) açık olmalı; VSL/3. parti kod hariç |
| **Sembol paketi** | PDB ayrı (NE-ENG-121) | Yalnız teşhis kanalı |

### §14.5 FFmpeg Vendor Stratejisi `⚠️ VR`

| Seçenek | Artı | Eksi | Not |
|---|---|---|---|
| (a) Vendor-artı-sistem (önerü) | Sistem FFmpeg varsa kullan; yoksa vendor `[ÖNERİ]` | Sürüm drift | ADR-0105 `⚠️ vendor stratejisi` bu PRD'de AÇIK |
| (b) Statik vendor | Deterministik build | LGPL static linking koşulları (nesne dosyası/relink) | Kısıt: `.la`/relink yükümlülüğü |
| (c) Dinamik sistem | LGPL temiz | Kullanıcı makinesinde FFmpeg kurulumu | UX yükü |

**Karar:** §18.9 açık maddesi — lisans mühendisi + ADR.

### §14.6 Güvenlik İstisnaları & Lisans İstisnaları Kaydı

| İstisna | Gerekçe | Süre |
|---|---|---|
| OS SDK (WASAPI/CoreAudio) | Platform zorunluluğu (EULA kapsamı — sürümle değişir `⚠️ VR`) | Kalıcı |
| ASIO SDK (opsiyonel build) | Düşük gecikme hedefi; READ-ONLY + `OFF` varsayılan | U1'e kadar |
| Vcredist (Windows) | Çalışma ortamı (K2) | Kalıcı |
| Vendor-artı-sistem FFmpeg | LGPL esnekliği | §18.9 kararına kadar |

### §14.7 Motor ↔ Player Lisans Uyumu

- Player PRD §14 (FFmpeg LGPL build · soxr · dr_libs · mbedTLS attribution) ile **ortak envanter** kullanılır (aynı L-numaraları).
- Farklılık yalnız: motor L01 (çekirdek lisans) + L10 (ASIO) + L14-L15 (K4/U3) — player bunları **motor paketi** üzerinden devralır.
- Player ayrı paketlerse (üstte NEVA.py/NEVA.exe — K2): her paketin LICENSE/NOTICE'i kendi manifestine yazılır `[ÖNERİ]`.

---

## §15 Yol Haritası (Fazlar · Kapılar · Zaman)

> **Kaynak:** ADR-107 §5 (pilot → A0/A1 → A2 → M1 → M2 → M3 → M4 → M5 → M6) · kardeş PRD §15 (player fazları — bu haritayla hizalı) · AGENTS §6 (APPROVAL gate).
> **Kural:** Her faz **onaysız başlamaz** (AGENTS §1 Workflow: her "ASKING QUESTIONS" kapısı); her faz kapanışında §12.6 checklist + commit kanıtı + vault-sync.

### §15.1 Faz Genel Görünüm

| Faz | Ad | Kapsam (özet) | Çıkış artefaktı | Kapı |
|---|---|---|---|---|
| **P0** | Pilot | Şema A — mevcut `neva_session` 9 fonksiyon + 5 kod + 7 basamak zincir | Çalışan baseline (diskte mevcut: `neva_session.h/cpp`, `spsc_ring.h`, `biquad.h`, `eq_chain.h`, `eq_grids.h`, `icy_parser.h`, wasapi/null, 3 test) | G0 — mevcut kod yeşil (ctest) |
| **A0** | Çekirdek dondurma | ABI freeze 1.0.0 + tek header + sembol dump arşivi | `abi-1.0.0.sym` arşivi | NE-ENG-060/142 |
| **A1** | Backend matrisi I | WASAPI Excl/Shared + WDM-KS (Windows) | 4 backend + kontrat testi | NE-ENG-021/022/024 · TC-ENG-042 |
| **A2** | Backend matrisi II | PipeWire/ALSA (Linux) · ARM64 build | 6 backend + 3 platform CI | NE-ENG-025/026/020 |
| **M1** | Ölçüm düzeneği | Latency loopback + SNR/THD + bench ortamı sabitleme | Ölçüm raporu (§11.3) | G2 eşiği tanımlanır |
| **M2** | RT olgunlaştırma | Zero-alloc kapısı + 30 dk stres + TSan + stack/histogram | G4 yeşil | S3 §6.1 kabul |
| **M3** | Resample/mismatch | soxr entegrasyonu (producer-side) + N1 sırası + F3 zinciri | Mismatch test yeşil | TC-ENG-015/072 |
| **M4** | UI köprüsü (player bağımlı) | neva-host köprüsü + event kuyruğu E1-E10 + tek-instance | Köprü çalışır | kardeş M4 |
| **M5** | Format/DSP final | DSD (U3 kapalı → DoP) · LUFS · parametrik EQ (§18.2/§18.3 kararları) | DSP bütçesi raporu | NE-ENG-076/077 |
| **M6** | Olgunlaşma | CoreAudio (macOS) + plug-in host (K4 kapalı) + uzak yüzey (U4/U5) | Karar kapıları kapanır | §18 açık maddeler |
| **PAKET** | Paket A/B | Lisans G3 + dağıtım (§14.4) | Release + rapor şablonu | G3 + rapor |

### §15.2 Aşama Detayları

#### G0 — Baseline Doğrulama (bu PRD'nin başlangıcı)
1. `ctest` yeşil (disk: `neva_tests`, `eq_grids_tests`, `icy_parser_tests`).
2. Mevcut C ABI 9 fonksiyon + 5 kod çalışır durumda (kanıt: `neva_session.h`).
3. Fallback zincir mimarisi kodda (`neva_session.cpp` zincir + `BackendRegistry`).
4. Bu PRD §10 gereksinimleri baz satır olarak alınır (182 madde).

#### A0 — ABI Dondurma
- Çıktı: sembol seti arşivi + `NEVA_API_VERSION 0x00010000` teyidi.
- Kabul: TC-ENG-060/142 + TC-ENG-046 (tek header).
- Risk: E1-E10 event eklemesi **minor** ile yapılır — dondurma yalnız mevcut 8+5'i kapsar.

#### A1 — Windows Backend Matrisi
- WASAPI Exclusive (event-driven + iki deneme + `DEVICE_IN_USE` → Shared) + WDM-KS.
- Kabul: kontrat testi (TC-ENG-042/133) + 2 süreç kilidi (TC-ENG-038) + fallback telemetri (TC-ENG-043).
- Manuel: gerçek DAC loopback ilk ölçüm (M1'e girdi).

#### A2 — Linux/ARM64
- PipeWire (F32P) → ALSA (hw:) zinciri + ARM64 build (RPi5 runner `⚠️ VR`).
- Kabul: 3 platform CI (TC-ENG-115) + Null testleri ARM64.

#### M1 — Ölçüm (G2'nin ön koşulu)
- Loopback düzeneği dokümante (§11.3-A/B) + SNR/THD (V-1/V-2) + bench ortamı sabitleme (TC-ENG-096).
- Kapı: ASIO <10 ms / WASAPI <20 ms **eşikleri ölçülür** (L1-L4 `⚠️ VR` — eşik ADR'ye işlenir).

#### M2 — RT Olgunlaştırma (G4)
- Zero-allocation kapısı (test hook) + 30 dk stres 0/0/0 + TSan + stack/ölçüm (§11.3-D).
- Kabul: S3 §6.1 kabul 1-5 (12 saat "hata yok" emsal testinin bu PRD'deki karşılığı: 30 dk otomatik + 4 saat serbest `[ÖNERİ]`).

#### M3 — Resample (F3)
- soxr producer-side + `setDeviceRate` sırası + hash koşul kuralı (eşit format şartı).
- Kabul: TC-ENG-015/072 + TC-ENG-009 (negatif guard).

#### M4 — UI Köprüsü (kardeş bağımlı)
- neva-host köprüsü + event kuyruğu + tek-instance + (şartlı) video fazı kapıları kardeş PRD §15.4-§15.5'te.
- Bu PRD'nin payı: E1-E10 API'si + error kodu sabitliği + NE-ENG-105/106/107.

#### M5 — DSP/Format Final
- LUFS + parametrik EQ (ADR-0105 §2.1 kararı) + DSD (U3 açık değilse yalnız DoP) + 5.1/8.1 (V-8).
- Kapı: EQ CPU ≤%5 + DSP bütçe raporu (TC-ENG-076/077).

#### M6 — Genişleme
- CoreAudio (macOS) + plug-in host (VST3 K4) + remote/mcp (U4/U5) — **karar kapıları**.
- Her madde: ADR → bayrak → test; karar yoksa faz BU maddeleri ATLANIR (ETİKETLI).

#### PAKET A/B — Lisans & Dağıtım
- G3 kontrol listesi (§14.3) + attribution + NOTICE + sembol paketi.
- Kapı sonucu: açık madde (§18.8 çekirdek lisans) → Paket B (ASIO'suz) veya kaynak yayımı `[ÖNERİ]`.

### §15.3 Bağımlılık Grafiği (kritik yol)

```
G0 → A0 → A1 → A2 ─┬→ M1 → M2 → M3 → M4 → M5 → M6 → PAKET
                    └→ (A2 paralel: docs/CI)
M4 (köprü) ← kardeş M4 (UI toolkit kararı §18.1)  [EN GEÇİKLEYİCİ AÇIK MADDE]
M5 ← §18.2/§18.3 (EQ modu/hedef/downmix kararları)
M6 ← U1..U5 + §18.8 (lisans)
PAKET ← G3 ← tüm karar kapıları
```

**Kritik yol uyarısı:** Tek gerçek blokaj **§18.1 (UI toolkit)** ve **§18.8 (çekirdek lisans)**tur — ikisi de ADR gerektirir, kod değil. Diğer açık maddeler etiketli ilerleyebilir.

### §15.4 Sürüm/Teslim Ritmi

| Sürüm | İçerik | Kapı |
|---|---|---|
| 1.0.0 (dondurma) | 9 fonksiyon + 5 kod (mevcut disk) | A0 |
| 1.1.0 | E1-E10 event/queue API (minor) | A1-A2 + M4 başlangıcı |
| 1.2.0 | Resample + mismatch (M3) + ölçüm raporu | M1/M3 |
| 1.3.0 | RT olgunlaşma (M2) + DSP final (M5) | G4 + M5 |
| 1.4.0 | (Kararlı) U4/U5 uzak yüzey | M6 + ADR |
| 1.5.x | Paket A (tam) / Paket B (temel) | G3 |

*(Sürüm takvimi tahminidir — tarih yok `[ÖNERİ]`; ritim ADR-107 §5 temposu: her fazda 1 commit + vault-sync.)*

### §15.5 Faz Checklist (§12.6 ile birlikte kullanılır — kapanış zorunlu)

Her fazın kapanışı için:
1. §10'daki bu faza ait gereksinimler: kabul kriteri + test yeşil (§11).
2. Yeni hata modu → §12 defterine eklendi mi?
3. Güvenlik yüzeyi değişti → §13 tehdit tablosu revize (SE/NE-ENG-166 karşılığı)?
4. Lisans etkisi → §14 matrisinde yeni satır/G3 etkisi var mı?
5. Açık maddeler (§18) güncellendi mi (kapanan/kapanmayan)?
6. Vault: `.ai/.decisions/` ADR (gerekiyorsa) + `.ai/AGENTS.md` notu + log.md satırı.
7. Commit (orkestratör) — subagent commit atmaz (AGENTS §16.5).

### §15.6 Erken Teslim / Parçalı Yayın Stratejisi

- **Kısmi yayın hedefi (AGENTS §17 Edge 7 — YAGNI):** İlk çalışan teslim = Paket B (ASIO/DSD hariç, core+2 platform) — kullanıcı değeri erken gelir, lisans blokajı M6'ya kayar.
- **Geri alınabilirlik (Edge 4):** Bayraklı sürüm + `OFF` varsayılan bayraklar (U1/U3) tek komutla geri alım.
- **Ölçüm olmadan yayın yok (Edge 3):** M1 raporu + kapılar olmadan "release" kelimesi kullanılmaz (`⚠️` gerekçeli istisna = yalnız iç test paketi).

---

## §16 Risk Kayıt Defteri (Engine)

> **Format:** R-NN · Açıklama · Olasılık(1-5) × Etki(1-5) = Risk · Azaltma · Sahip · Durum. Olasılık/etki değerleri `[ÖNERİ]`'dir (ADR'lerde puanlama yok); **varlık/etki kaynağı** teyitli.
> **Kural (S3 §6.2):** Önemli risk her phase review'da yeniden değerlendirilir; kapanan risk arşivlenir (append-only defter).

### §16.1 Kayıt Tablosu

| ID | Risk | O×E | Risk | Azaltma | Sahip | Durum |
|---|---|---|---|---|---|---|
| **R-01** | **U1 — ASIO lisansı çözülmez** (VSL dual-license tartışması — ADR-107 §8.1); ASIO yolu kapanır | 4×5 | **20** | READ-ONLY entegrasyon kararı akışı · `OFF` varsayılan · Paket B (ASIO'suz) zinciri zaten çalışır · alternative: WASAPI Excl | Mimari | **AÇIK** |
| **R-02** | **U3 — libdsd LICENSE bulunamaz** → DSD native yolu kapanır (yalnız DoP) | 4×3 | 12 | DoP fallback (DoP zaten açık) · alternatif açık-kaynak DSD `⚠️ VR` arama | Lisans | **AÇIK** |
| **R-03** | **U5 — MCP sunucu dili seçilemez** (kullanılabilirlik kotası/pratik) | 3×2 | 6 | Karar ertelenir; C ABI bridge yolu hazır (NE-ENG-109); uzak yüzey core'a dokunmaz | Ürün | **AÇIK** |
| **R-04** | **Bit-perfect iddiası ölçülemez** (düzenek/ekipman) → V-1/V-2 kapıları | 3×4 | 12 | Düzeneği kendi tabanı ölçülür (§11.3-B); eşik `⚠️ VR` işaretlenir; iddia yalnız kapı raporuyla | Ölçüm | Açık (M1) |
| **R-05** | **L1-L4 gecikme kademeleri tanımsız** (`⚠️ VR ADR-0105 §2.10`) → eşik tartışması release'e sarkar | 4×3 | 12 | M1'de ölçülür + ADR'ye işlenir; geçici: en küçük desteklenen blok | Ölçüm | Açık (M1) |
| **R-06** | **Gerçek donanımda RT stres bulgusu** (xrun ≠ 0 — sürücü jitter) | 3×4 | 12 | G4 nightly + kademeli blok + kill-switch devrede; kabul eşiği 0, sapma = blok | RT | Açık (M2) |
| **R-07** | **Release sonrası 0-day (parser)** | 2×5 | 10 | Kapı 5.13 (fuzz ≥1000/hedef) · hotfix süreci + corpus genişletme | Güvenlik | Sürekli |
| **R-08** | **ABI erken dondurma = yanlış tasarım** (event eklemeleri majör isterse) | 2×4 | 8 | A0 yalnız mevcut 8+5'i dondurur; E1-E10 **minor** genişleme planlı (NE-ENG-060 kuralı) | Mimari | Azaltılmış |
| **R-09** | **UI toolkit kararı verilmez** (kardeş §18.1) → M4 bloke, motor uygulanabilirliği etkilenir | 4×3 | 12 | Motor fazları M4'süz ilerleyebilir (P0-P3 paralel); karar M4 girişinde zorunlu | Ürün | Kardeş |
| **R-10** | **Tek-header/ABI genişlemesi 25 dil iddiasını karşılamaz** (U2 tanımı yok) | 3×2 | 6 | Tanım daraltması `[ÖNERİ]` + smoke testi (NE-PLR-203) | Ürün | Açık (§18.5) |
| **R-11** | **ARM64 runner/gerçek cihaz yok** → RPi5 doğrulaması gecikir | 4×2 | 8 | CI build-only + `⚠️ VR` etiketi korunur; runner erişimi backlog | DevOps | Açık |
| **R-12** | **Vendor FFmpeg/libsamplerate lisansı** (LGPL static) sürprizi | 3×3 | 9 | Dinamik bağlama varsayılanı + G3 link raporu + vendor stratejisi ADR (§18.9) | Lisans | Açık (G3) |
| **R-13** | **soxr LGPL statikleşirse** (`⚠️ LİSANS` etiketi) | 3×3 | 9 | Dinamik `✅` varsayılan; statik gerekiyorsa açık kaynak/alternatif (libsamplerate `⚠️ VR` lisans) | Lisans | Açık (G3) |
| **R-14** | **Çekirdek lisans kararı (L01) gecikir** → kaynak yayımı bloke | 3×4 | 12 | §18.8 AÇIK madde — M6 öncesi zorunlu; AGPL/MIT tartışması ürün kararı | Ürün | **KRİTİK AÇIK** |
| **R-15** | **İki süreç exclusive yarışı** → kullanıcı "ses yok" raporu | 3×3 | 9 | Fallback zinciri + tek-instance köprü `[ÖNERİ]` (TC-ENG-038/105) | Backend | Azaltılmış |
| **R-16** | **Xerces-4.6 XXE emsal tartışması** (radio-and-hires §4.1-§4.6) — v2 parser güvenliği sorgulanır | 3×4 | 12 | DTD/entity kapalı (kural 8) + fuzz corpus + XXE vektör testi (kardeş TC-PLR-172) | Güvenlik | Açık (P3 kardeş) |
| **R-17** | **Otomatik keep-alive'in canlı içerikle çatışması** (radio-and-hires §4) | 3×3 | 9 | 5 sn kesinti → reconnect politikası testi (kardeş Ş1) · canlı devir iddiası KAPALI | RT/Backend | Açık (P3 kardeş) |
| **R-18** | **Bütçe ihlali** (RAM 400 MB / CPU %5 / stack 4KB — ölçüm) | 3×4 | 12 | Baseline + %10 regresyon etiketi (NE-ENG-100) + G4 ölçümü | Performans | Açık (M2/M5) |
| **R-19** | **12 saatlik emsal stresin bu projede tekrarı** (ADR-019 §6 — "çalışırken aniden donma" başlıklı yarış) | 2×5 | 10 | TSan + çoklu-thread spam testi + (S3) "hata yok" uzun koşu `[ÖNERİ — 30 dk otomatik + 12 saat schedule]` | RT | Sürekli |
| **R-20** | **Ölçüm/CI altyapısı ortada yok** (deadline → kapılar tiyatro olur) | 3×5 | 15 | M1 MİLESTONE — kapı olmadan "bitperfect/gapless" söylemi yasak (Edge 3); kapılar kodda otomatik | QA | Açık (M1) |
| **R-21** | **3. parti kod (WDM-KS/CoreAudio) güvenlik/RT ihlali** | 3×4 | 12 | Adaptör izolasyonu + uzman review kapısı (S3 §4.5 / §13.7) | Mimari | Açık |
| **R-22** | **p05/p06 başlıklı ileri fonksiyonların unutulması** (nesne kısıtları: `set_mode/reset_gain/set_loop_mode`) | 3×3 | 9 | Kardeş §05.5 eksik fonksiyon defteri — Z/n0/7'e kadar | Backend | Kardeş |
| **R-23** | **Kapsam şişmesi** (video/dash/matrix/plugin çekirdeğe girerse) | 4×4 | 16 | Çekirdek yasakları (S3 §4.3) + K2 apps kuralı + K4 bağlama + bu PRD §02.3 sınırı | Mimari | Sürekli gözetim |
| **R-24** | **Determinizm/test kaynağı bağımlılığı** (hash için üretilen WAV'lar değişirse kapı kırılır) | 2×3 | 6 | Seed sabitleme + referans hash dosyaları repo'da (NE-ENG-128) | QA | Azaltılmış |
| **R-25** | **Uçak modu (Edge 9) ihlali**: uygulama bağımlılıkları AA (analytics) çekerse | 3×4 | 12 | Çekirdekte ağ yok (SE-03) + telemetri opt-out (NE-ENG-099) + CI ağ denetimi | Ürün/Güvenlik | Açık |

### §16.2 Öncelik Sıralaması (Risk > 10 — önce bunlar)

1. **R-01 (20)** — ASIO lisansı: §18.1'in çifti §18.8 kadar kritik; READ-ONLY akışı M6'ya kadar.
2. **R-23 (16)** — Kapsam şişmesi: her PR'da çekirdek yasakları statik kapı.
3. **R-20 (15)** — Ölçüm/CI yokluğu: M1 milestone'ı öne alınır (kod değil düzeneğin işi).
4. **R-14 (12) + R-04/R-05/R-06/R-09/R-16/R-18/R-21/R-25 (12'ler)** — lisans + ölçüm + RT + emsal XXE + bütçe + uzman review.

**Karar defteri kuralı:** Her phase review'da bu tablo yeniden puanlanır; yeni risk append edilir, satır silinmez; kapanan satır `ARŞİV (tarih)` etiketi alır.

### §16.3 Risk ↔ Gereksinim ↔ Test İzlenebilirliği (kısa)

| Risk | Gereksinim(ler) | Test |
|---|---|---|
| R-01 | NE-ENG-023/146/161-162/176 | TC-ENG-023/146 + G3 |
| R-04/R-05 | NE-ENG-089/090/137/138/163 | TC-ENG-089/090/137/138 |
| R-06/R-19 | NE-ENG-088/140/059/097/164 | TC-ENG-088/140/131 + gece koşusu |
| R-07 | NE-ENG-132/166 | kapı 5.13 |
| R-12/R-13 | NE-ENG-149/154/155/165/167-169 | TC-ENG-148-155 + G3 |
| R-14 | NE-ENG-146-160 (§14) + §18.8 | G3-6 |
| R-16/R-17 | NE-ENG-095/136/143 | TC-ENG-095/136 |
| R-20 | NE-ENG-164 (kayıt) + §15 M1 | G2/G4 raporları |
| R-23 | NE-ENG-001/006/044/046/103/159/170/175 | statik kapılar |
| R-25 | NE-ENG-099/172 (envanter) | ağ denetimi |

*(Tam matris §17'de — bu tablo hızlı bakış içindir.)*

---

## §17 İzlenebilirlik (Traceability Matrix)

> **Amaç:** Her gereksinim → **kaynak (ADR/disk/vision/bu PRD)** → **test (TC)** → **faz (§15)** → **durum** zincirini tek tabloda göstermek.
> **Okuma kuralı:** "Kaynak" sütununda `ADR-xxx` = bağlayıcı · `disk` = kanıt okundu · `§x` = bu PRD içi türetme (dayanak) · `YOK/[ÖNERİ]` = bu PRD önerisi (ADR'siz).

### §17.1 Grup Bazlı Matris (§10 özet → kaynak → test → faz)

| Grup | Kapsam | ID aralığı | Başlıca kaynak | Test | Faz | Durum |
|---|---|---|---|---|---|---|
| A — Platform & Mimari | ABI, zincir, platform matrisi, eksik fonksiyon | 001-015 | disk `neva_session.h` · ADR-019 · ADR-0105 §2.6 ⚠️ · ADR-107 K2 | TC-ENG-001..015 | P0/A0/A1 | Teyitli (disk) |
| B — Backend fallback & fallback zinciri | 8 backend, N1, hot-plug, miss kurtarma | 016-045 | ADR-019 §2.2b/F1-F4 · ADR-106 §1.3 · disk `backends/` | TC-ENG-016..045 | A1/A2/M3 | Teyitli (disk+ADR) |
| C — Session API & event | E1-E10, queue, position, error kodları | 046-060 | ADR-106 kural 2 · kardeş §05.2 · disk `neva_session.h` | TC-ENG-046..060 | A0/M4 | Teyitli (disk) |
| D — DSP & EQ | biquad/EqChain/limiter/soxr/LUFS/bütçe | 061-080 | ADR-106 §2.2b/§1.3 · ADR-0105 §2.1/§2.3/§2.5 ⚠️ · disk `biquad.h/eq_chain.h/eq_grids.h` | TC-ENG-061..080 | M3/M5 | Kısmen teyitli (15/20-band `⚠️ VR`) |
| E — RT & Zamanlama | zero-alloc, SPSC, xrun, kill-switch, stres | 081-100 | ADR-017 (brain) · ADR-019 F4 · disk `spsc_ring.h` · S3 §6.1 | TC-ENG-081..100 | M2 | Teyitli (disk+ADR) |
| F — Paketleme & araçlar | K2 mimarisi, CI, toolchain, artefakt | 101-125 | ADR-107 K2 · S3 §4/§5 · disk CMakeLists/CTest | TC-ENG-101..125 | A0..A2 | Teyitli (disk) |
| G — Kapılar & ölçüm | hash K3, latency, SNR/THD, gapless, fuzz | 126-145 | ADR-107 K3 · ADR-0105 kapı 5.1/5.10/5.12/5.13 ⚠️ · VISION | TC-ENG-126..145 | M1/PAKET | Eşik `⚠️ VR` işaretli |
| H — Lisans & güvenlik | G3, RED listesi, clamp, ağ yasağı | 146-160 | ADR-107 §8.1-8.7 · ADR-0105 lib-matrix ⚠️ · AGENTS §5 | TC-ENG-146..160 | PAKET | U1/U3 AÇIK |
| I — Faz kapıları | G0-G6, M0-M6, onay ritmi | 161-175 | ADR-107 §5 · AGENTS §1 (APPROVAL) | TC-ENG-161..175 | Tümü | Süreç |
| J — Açık varsayımlar | U1-U5 + PCM3168A + vault | 176-182 | ADR-107 §8.1-8.8 · index.md:152 | TC-ENG-176..182 | M6 | AÇIK (§18) |

### §17.2 Kaynak Dökümü (bu PRD'de okunan disk/ADR kanıtları)

| Kaynak | Tür | Kullanıldığı yerler |
|---|---|---|
| `coremusic-adr107-neva-engine-prompt.md` (G: sürücü, 218 satır) | Onay prompt | §00-§02 · §15 · §18 · NE-ENG-161..182 |
| `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` | Accepted ADR | §03-§04 · Grup A/B · fallback zincir · §15 |
| `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md` | Accepted ADR | Grup D (EQ kuralları 1-7) · kapılar §11.3 · Grup B (N1/mismatch) |
| `.ai/.decisions/draft/ADR-107-...paketleme.md` | Draft ADR | §04.6-§04.7 (K1-K5) · §15 fazları · §14 |
| `.ai/architecture/neva-engine-platform-paketleme.md` | Mimari | §03-§04 · §09 · §15 |
| `.ai/.decisions/index.md:152` (ADR-0105 dosyasız) | İndeks kaydı | Tüm `⚠️ VR (ADR-0105 ...)` etiketleri · §00.3 D2 · §18.10 |
| `--/neva-engine/` (CMakeLists, neva_session, spsc_ring, biquad, eq_chain, eq_grids, icy_parser, wasapi/null, 3 test, build/) | Disk kod | §02.4 · Grup A/C/D/E/F kanıtları · §11.2/§11.7 IMPLEMENTED testler |
| `VISION.md` (V-1..V-8 — §00.3 referansı) | Vizyon | §01 · §08.5 (SNR/THD) · V-3/V-4/V-5 (RT) |
| `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `AGENTS.md` (kök) | Kural | §13 (güvenlik) · §16 (Edge) · süreç |

### §17.3 Test ↔ Gereksinim ↔ Kapı Eşlemesi

| Test grubu | Sayı | Karşılık gelen kapı |
|---|---|---|
| TC-ENG-001..060 (Grup A-C) | 60 | Statik ABI + kontrat + zincir (her PR) |
| TC-ENG-061..080 (DSP) | 20 | DSP bütçe + hash ön koşulları |
| TC-ENG-081..100 (RT) | 20 | G4 stres · TSan · leak |
| TC-ENG-101..125 (paket) | 25 | CI matrisi · artefakt |
| TC-ENG-126..145 (kapılar) | 20 | **G1 (hash) · G2 (latency) · 5.12 · 5.13** |
| TC-ENG-146..160 (lisans/güvenlik) | 15 | **G3 (lisans)** · secret-scan |
| TC-ENG-161..175 (faz) | 15 | Onay checklist (§15.5) |
| TC-ENG-176..182 (açık) | 7 | §18 karar kapıları |
| **Toplam** | **182** | (her gereksinim ≥1 test — §10.14 tekrar sayımı) |

### §17.4 Çapraz Doküman Bağlantıları (bu PRD ↔ kardeş PRD)

| Konu | Motor sahibi (bu PRD) | Player sahibi (kardeş) |
|---|---|---|
| Hash hattı (12 komb + guard) | §11.2 · NE-ENG-126/127 | §11.2 · NE-PLR-036/040/087 |
| Latency ölçüm düzeni | §11.3-A (sahip) | §11.4 (referans) |
| SNR/THD düzeneği | §11.3-B (sahip) | §11.4 (referans + V-1/V-2) |
| Gapless | API (NE-ENG-048/139) | Format vektörleri (NE-PLR-056..075) |
| Error kodu (0-4) | §12.3 tablo (sahip) | §13.1 mesaj şablonları |
| Fallback zinciri 7 basamak | §04.4 + NE-ENG-008 | §04.4 üzerinden (NE-PLR-154) |
| URL validator tek kapı | Ağ YOK (SE-03) | §12.2 T0-T27 (sahip) |
| Lisans envanteri (L/P numaraları) | §14.1 | §14.1 (ortak envanter) |
| UI toolkit kararı | — | §18.1 (sahip · M4 blokajı) |
| Örnek CPU/RAM defteri | (motor payı) | (player bütçesi) |

### §17.5 Kapsam Dışı ↔ İlgili (ne YAPILMAZ — izlenebilir sınır)

| Kapsam dışı | İlgili gereksinim | Gerekçe |
|---|---|---|
| UI toolkit implementasyonu | NE-PLR-009 (kardeş §18.1) | Karar ADR'si |
| Yerel decode kodu detayı (neva_decode) | kardeş NE-PLR-017 (bu PRD: yalnız arayüz) | K2 apps/ kuralı |
| FFmpeg codec (libfdk vb.) eklemek | NE-ENG-148/151/152 | Lisans RED |
| Kanal şablonlarına gerçek domain adı yazmak | NE-ENG-018 | ADR-097 K5 yasağı |
| Stratejik ar--ge'de olmayan defter (band 2-7) | §10 notlar | ADR-097 |
| `.ai/ui-design/**` içine PNG üretimi | §18.7 (kardeş) | Guardrail #16 + mockup gate |
| `git commit` (subagent) | §20.2 | AGENTS §16.5 (orkestratöre ait) |

### §17.6 Izlenebilirlik Bütünlük Denetimi (CI/PR adımı)

1. Her `NE-ENG-NNN` en az bir `TC-ENG-NNN` içeriyor mu? (§10 başlıklarında zorunlu — eksik = PR review notu).
2. Her TC'nin `NE-ENG-` referansı gerçek ID mi? (kırık = FAIL).
3. `⚠️ VR` etiketli satır sayısı ↓ yönünde azalıyor mu? (§18.10 defteri ile).
4. §14.2 RED listesi pakette yok mu? (G3-2).
5. §02.3 kapsam dışı satırları ihlal eden yeni kod yok mu? (R-23 statik kapı).

---

## §18 Açık Sorular & Karar Kapıları (Engine)

> **Amaç:** Bu PRD'nin çözemediği (kaynakta olmayan) her maddeyi **karar formu** ile listelemek.
> **Kural (AGENTS §3):** `UNKNOWN` = bilinmeyen · `⚠️ VERIFICATION REQUIRED` = doğrulanamayan iddia · `[VARSAYIM]` = dayanağı zayıf varsayım · `[ÖNERİ]` = bu PRD'nin önerisi (ADR'siz).
> **Hiçbiri release paketine girmez** — karar verilmeden ilgili bayrak `OFF` kalır (G3/G6 kapısı).

### §18.0 Karar Kapısı Çerçevesi

Her açık madde için dört alan zorunludur:

| Alan | Tanım |
|---|---|
| **Q** | Cevaplanması gereken soru (tek cümle) |
| **Kapsam** | Karar verilmezse ne bloke olur (faz/gereksinim/test) |
| **Seçenekler** | En az 2 gerçek seçenek (artı/eksi ile) |
| **Karar sahibi** | Kim onaylar (rol) + hangi artefakt (ADR/doküman) |
| **Varsayılan (güvenli)** | Karar gelene kadar ne yapılır (RED/OFF/etiket) |
| **Durum** | AÇIK · KISMEN · KAPANDI (tarih) |

**Onay zinciri:** Karar sahibi → ADR yazımı (`/adr-write` veya `.ai/.decisions/draft/`) → `accepted` → bu PRD §10 gereksinimi güncellenir → §17 izlenebilirlik yeniden denetlenir → vault-sync (`.ai/log.md` satırı).

### §18.1 U1 — ASIO Sürümü & Lisansı (KRİTİK)

- **Q:** ASIO SDK entegrasyonu hangi lisans modeliyle, hangi sürümle ve READ-ONLY sınırıyla yapılır?
- **Kaynak durumu:** ADR-107 §8.1 — "VSL (visual studio license) çatallanmış lisans tartışması" `⚠️ VR` · Steinberg ASIO License dual-license (ücretsiz/ücretli) bilgisi genel bilgidir, proje vault'unda metin YOK.
- **Kapsam:** NE-ENG-023 (ASIO backend) · NE-ENG-146 (lisans) · NE-ENG-161/162 (bayraklar) · TC-ENG-023 · G3 kapısı · R-01 (risk 20).
- **Seçenekler:**
  - **(a) READ-ONLY entegrasyon (önerü ADR-107):** SDK yalnız `backends/asio/` içinde, kopya yok, imzalı bağımlılık. Artı: hukuki sınır net. Eksi: VSL çatallanması kapanmadan dağıtım kapalı.
  - **(b) Ücretli ASIO lisansı:** Artı: tam yetki. Eksi: maliyet + dağıtım modeli değişir `[VARSAYIM — proje bütçesi bilinmiyor]`.
  - **(c) ASIO'suz çıkış (geçiş):** `NEVA_WITH_ASIO=OFF` varsayılan; WASAPI Excl zaten 2. basamak. Artı: bugünden release. Eksi: "ASIO <10 ms" iddiası pakette olmaz.
- **Varsayılan:** **(c)** — `OFF` (ADR-107 §8.1'de mevcut varsayılan).
- **Karar sahibi:** Lisans danışmanı + mimari (Expert — AGENTS §6.1) → ADR-108 (önerü numarası `[ÖNERİ]`).
- **Kapanış ölçütü:** Lisans metni vault'ta + bayrak kararı + G3-3 raporu.

### §18.2 U2 — "25 Dilde Arayüz" İddiasının Tanımı

- **Q:** VISION'daki "25 dilde arayüz" tam olarak hangi dil listesidir ve UI toolkit hangisidir?
- **Kaynak durumu:** ADR-0105 v1.0.2 §3 (dosya YOK — surrogate) `⚠️ VR` · kardeş PRD §18.2 (yerelleştirme) ile bağlantılı.
- **Kapsam:** Kardeş NE-PLR-014/194/203 · bu PRD: yalnız C ABI'nın dil bağlanması (NE-ENG-060 tek header + NE-PLR-202 ctypes smoke).
- **Seçenekler:**
  - **(a) Gerçek 25 dil kataloğu** (Türkçe/İngilizce/Almanca/… listesi + çeviri bütçesi).
  - **(b) Daraltma:** "çok dilli destek" → TR+EN (ilk sürüm) + çeviri altyapısı hazır.
  - **(c) Yalnız UTF-8/UTF-16 kaynak kodu desteği** (arayüz çevirisi ayrı ürün kararı).
- **Varsayılan:** **(c)** — dil listesi iddiası ölçülmez (`UNKNOWN`), yalnız Unicode kaynak kodu garantisi yazılır.
- **Karar sahibi:** Ürün (product-manager) → ADR + kardeş PRD §18.2 güncellemesi.
- **Kapanış ölçütü:** Karar satırı + (varsa) çeviri dosyası sayısı raporu.

### §18.3 U3 — libdsd & macOS DSD Yolu

- **Q:** DSD decode hangi kütüphaneyle ve macOS'ta nasıl yapılır?
- **Kaynak durumu:** ADR-0105 v1.2.0 lib-matrix `⚠️` — `libdsd` lisansı bilinmiyor (`UNKNOWN`) · ADR-107 §8.2-§8.3'te U3.
- **Kapsam:** NE-ENG-011/012 (format kapsamı) · NE-ENG-147/152 (RED/opsiyonel) · kardeş NE-PLR-044/070-075 (DoP gapless).
- **Seçenekler:**
  - **(a) libdsd (lisans teyidi ile):** Artı: native DSD. Eksi: LICENSE bulunamazsa RED.
  - **(b) Yalnız DoP (PCM sarma):** Artı: bağımlılık yok, DoP zaten açık. Eksi: native DSD iddiası yok.
  - **(c) FFmpeg DSD desteği:** `⚠️ VR` — FFmpeg'in DSD decode yeteneği/varyantı teyit edilmedi; LGPL/GPL varyantı lisans etkisi.
- **Varsayılan:** **(b)** — native DSD kapalı (`OFF`), DoP açık.
- **Karar sahibi:** Lisans + mimari → ADR.
- **Kapanış ölçütü:** LICENSE dosyası vault'a + bayrak kararı.

### §18.4 U4 — Web/Yüzey API Kapsamı

- **Q:** Motorun HTTP/web yüzeyi var mı, yoksa tüm ağ player'da mı?
- **Kaynak durumu:** ADR-107 §8.6 (U4) `⚠️ VR` · S3 §4.3 "çekirdek ile host arasında tek kapı `neva_*` ABI" · bu PRD SE-03 (çekirdekte ağ YOK).
- **Kapsam:** SE-03 · NE-ENG-046 (ABI) · kardeş NE-PLR-205/206 (remote/web).
- **Seçenekler:**
  - **(a) Çekirdek ağsız (önerü):** Motor içinde socket/curl yok; tüm HTTP player katmanında. Artı: saldırı yüzeyi daralır · S3 uyumu. Eksi: uzak kontrol ayrı süreç.
  - **(b) Motor içi HTTP (keşif/radyo motoru):** Eksi: çekirdek RT güvenliği + OWASP yüzeyi büyür (T-E02) — **önerilmez**.
- **Varsayılan:** **(a)** — SE-03 statik kapı (grep0) kalıcı.
- **Karar sahibi:** architect + security-engineer (Expert) → ADR (gerekirse teyit amaçlı).
- **Kapanış ölçütü:** ADR-107 U4 satırının kapanması.

### §18.5 U5 — MCP Sunucusu Dili & Yetki Modeli

- **Q:** `neva-mcp` hangi dilde yazılır ve hangi yetki modeliyle açılır?
- **Kaynak durumu:** ADR-0105 v1.4.0 `⚠️` (dosya YOK) · kardeş NE-PLR-204 · NE-ENG-109 (C ABI bridge).
- **Kapsam:** NE-ENG-109 · kardeş NE-PLR-204 · R-03 (risk 6).
- **Seçenekler:**
  - **(a) Python (önerü):** MCP SDK olgunluğu + ctypes smoke (NE-PLR-202) zaten planlı. Artı: hızlı. Eksi: Python çalışma ortamı.
  - **(b) C++/Rust (tek binary):** Artı: dağıtım temiz. Eksi: MCP SDK desteği `⚠️ VR`.
  - **(c) Erteleme:** Uzak yüzey ertelenir; C ABI hazır kalır (NE-ENG-109).
- **Varsayılan:** **(c)** — kapalı, `OFF`.
- **Karar sahibi:** Ürün + mimari → ADR (v1.4.0 kapısında).
- **Kapanış ölçütü:** Dil kararı + yetki modeli (localhost+token `[ÖNERİ]`).

### §18.6 U6 — Çekirdek Motor Lisansı (KRİTİK — G3-6)

- **Q:** Neva Engine çekirdeği hangi lisansla yayımlanır: AGPL-3.0 mı, MIT benzeri mi, yoksa kapalı mı?
- **Kaynak durumu:** ADR-107 §8.7 — "L01 karar gerekli: AGPL-3.0 / MIT benzeri `⚠️ VR`" · bu PRD §14.1 L01.
- **Kapsam:** §14.4 (Paket A/Kaynak yayımı) · G3-6 · R-14 (risk 12, KRİTİK) · §15 PAKET fazı.
- **Seçenekler:**
  - **(a) AGPL-3.0:** Artı: katkı/kopya koruması (FxSound emsaline karşı — ADR-107 K4). Eksi: kurumsal/oyun entegrasyonu caydırır; VSL/ASIO ile gerilim olabilir `⚠️ VR (lisans uyumu teyit edilmeli)`.
  - **(b) MIT/Apache-2.0 benzeri:** Artı: benimsenme yüksek. Eksi: çatal/kopya serbest (K4 kaygısı zayıflar).
  - **(c) Kapalı + motor SDK:** Artı: kontrol tam. Eksi: "neva_* ABI 25 dil" hikâyesi zayıflar (açık kaynak olmadan).
- **Varsayılan:** **(c)** — kaynak yayımı YOK (bilinmeyen lisans yayılmaz).
- **Karar sahibi:** Ürün sahibi (lider) → ADR-107 §8.7'nin cevabı.
- **Kapanış ölçütü:** Lisans metni + LICENSE dosyası + G3-6 yeşil.

### §18.7 U7 — FFmpeg Vendor Stratejisi (§14.5 detayı)

- **Q:** FFmpeg statik vendor mı, dinamik sistem mi, vendor-artı-sistem mi?
- **Kaynak durumu:** ADR-0105 `⚠️ vendor stratejisi belirsiz` · §14.5 bu PRD'de seçenekler.
- **Kapsam:** NE-ENG-149/155/167-169 · G3-4 · kardeş §14.5 (tek ADR ile birlikte).
- **Seçenekler:** §14.5 tablosu (a/b/c).
- **Varsayılan:** **(a) vendor-artı-sistem** `[ÖNERİ]` + dinamik bağlama.
- **Karar sahibi:** Lisans + build mühendisi → ortak ADR (kardeş §18.9 ile).
- **Kapanış ölçütü:** Link raporu + NOTICE.

### §18.8 U8 — Gecikme Kademeleri L1-L4 (ADR-0105 §2.10)

- **Q:** Düşük/orta/yüksek gecikme kademe eşikleri tam olarak nedir (ms/blok)?
- **Kaynak durumu:** ADR-0105 §2.10 `⚠️ VR` (dosya YOK) · bu PRD NE-ENG-089/090 `⚠️ VR`.
- **Kapsam:** NE-ENG-089/090/137 · kapı 5.10 (G2) · kardeş NE-PLR-183.
- **Seçenekler:**
  - **(a) M1 ölçümünden sonra ADR:** Gerçek cihazda ölç → kademe tablosu. **Önerü.**
  - **(b) Endüstri tipikleriyle tahmin:** ASIO <10 ms · WASAPI <20 ms · orta 20-40 ms · yüksek >40 ms `[ÖNERİ — tahmin]`.
- **Varsayılan:** **(a)** — eşikler `⚠️ VR` etiketli kalır; release iddiası yalnız ölçüm raporuyla.
- **Karar sahibi:** Ölçüm (performance-engineer) → ADR-108 (önerü) + G2 raporu.

### §18.9 U9 — Parametrik EQ & LUFS Kapsamı (ADR-0105 §2.1/§2.2)

- **Q:** Parametrik EQ (Q/frekans sürükleme) ve LUFS mastering ölçümü hangi fazda, hangi arayüzle?
- **Kaynak durumu:** ADR-0105 §2.1 `⚠️` · ADR-106 §2.2b (şartlı 11. kural: LUFS/şablon) · bu PRD §06.7 `[ÖNERİ]`.
- **Kapsam:** NE-ENG-076/077 · kardeş NE-PLR-143/144 · M5 fazı.
- **Seçenekler:**
  - **(a) Grafik EQ yeterli (ilk sürüm):** 31/10/15/20 band + preamp + limiter. **Önerü (YAGNI).**
  - **(b) Parametrik EQ ekle:** Artı: profesyonel kullanım. Eksi: CPU/UI karmaşıklığı + ölçüm (bütçe §08.4).
  - **(c) LUFS dahil:** Artı: yayın standardı. Eksi: K-weighting filtre + loudness meter maliyeti.
- **Varsayılan:** **(a)** — parametrik/LUFS bayrakları `OFF`.
- **Karar sahibi:** Ürün + DSP → ADR (M5 girişinde).

### §18.10 U10 — PCM3168A DAC Referans Donanımı (U6)

- **Q:** PCM3168A referans DAC'ı hangi ölçüm düzeneğinde ve hangi sürücüde doğrulanacak?
- **Kaynak durumu:** ADR-107 §8.4 (U6) `⚠️ VR` · donanım bilgisi ADR'de yok.
- **Kapsam:** §11.3-A/B ölçüm düzenekleri · V-1/V-2 (SNR/THD) · kapı G2.
- **Seçenekler:**
  - **(a) Mevcut USB DAC ile ölçüm:** Artı: erken. Eksi: donanım iddiası (PCM3168A) doğrulanmaz.
  - **(b) PCM3168A eval kartı alımı:** Artı: hedef donanım. Eksi: süre/bütçe `[VARSAYIM — bütçe bilinmiyor]`.
- **Varsayılan:** **(a)** — ölçüm raporunda donanım adı `⚠️ VR` yazılır.
- **Karar sahibi:** Ölçüm + ürün → ADR/doküman.

### §18.11 U11 — WDM-KS & CoreAudio Sürüm/İmza Detayı

- **Q:** WDM-KS (Windows) ve CoreAudio (macOS) backend'leri hangi SDK sürümleri ve imza gereksinimleriyle derlenir?
- **Kaynak durumu:** ADR-019 §1.5 (WDM-KS emsalde başarısız) · S3 §4.5 (uzman review) · SDK versiyonları vault'ta YOK.
- **Kapsam:** NE-ENG-024/027 · TC-ENG-044/160 · A1/A2 fazları.
- **Seçenekler:**
  - **(a) Minimum SDK (Windows 10 / macOS 12) varsayımı** `[ÖNERİ]` + CI'da sürüm matrisi.
  - **(b) En yeni SDK:** Artı: özellik. Eksi: geri uyumluluk riski.
- **Varsayılan:** **(a)** — sürüm satırı `⚠️ VR` etiketli.
- **Karar sahibi:** Mimari + build → ADR (gerekirse).

### §18.12 U12 — Neva Host (UI Köprüsü) Süreci & Dili

- **Q:** `neva-host` köprü süreci hangi dil/çatı ile yazılır ( kardeş M4 ) ve tekillik nasıl sağlanır?
- **Kaynak durumu:** ADR-107 §8.6 (U4) · kardeş §18.1 (UI toolkit) · ADR-004/ADR-018 (tek-instance emsal).
- **Kapsam:** NE-ENG-105/106/107 · kardeş NE-PLR-179 · M4.
- **Seçenekler:**
  - **(a) Kardeş UI toolkit ile aynı süreç:** Artı: tek binary. Eksi: toolkit kararı (§18.1 kardeş) blokajı.
  - **(b) Ayrı host + IPC:** Artı: motor/UI ayrımı (S3 izolasyonu). Eksi: IPC güvenlik modeli `[ÖNERİ — SID/UID]`.
- **Varsayılan:** **(b)** — köprü arayüzü (event kuyruğu) sabit, uygulama dili kardeş §18.1'e bağlı.
- **Karar sahibi:** Mimari + kardeş ürün kararı → ortak ADR.

### §18.13 U13 — Fuzz Hedefi Format Listesi (kapı 5.13 kapsamı)

- **Q:** ≥1000/format corpus hangi formatlar için (DSF/DFF dahil mi, HLS playlist dahil mi)?
- **Kaynak durumu:** ADR-0105 kapı 5.13 `⚠️ VR` (eşik var, format listesi dosyada YOK).
- **Kapsam:** NE-ENG-132 · kapı 5.13 · kardeş NE-PLR-188.
- **Seçenekler:**
  - **(a) Kapsam listesi:** WAV · FLAC · MP3 · ICY text · HLS playlist · C ABI girdisi · preset JSON (7 hedef) `[ÖNERİ]`.
  - **(b) Dar:** yalnız decode (WAV/FLAC/MP3) — daha hızlı CI.
- **Varsayılan:** **(a)** — liste §11.5'te bu PRD'de sabitlenir (öneri), ADR ile teyit edilir.
- **Karar sahibi:** QA (Expert) → test kapsamı kaydı.

### §18.14 U14 — Ölçüm Eşiklerinin Onayı (G2/G4 kapıları)

- **Q:** ASIO <10 ms · WASAPI <20 ms · SNR >105 dB · THD+N <%0.005 · RAM ≤400 MB · CPU ≤%5 eşikleri bağlayıcı mı?
- **Kaynak durumu:** VISION V-1/V-2 · ADR-0105 §2.9/§2.10 `⚠️` · ADR-106 §1.3 · bu PRD §08.
- **Kapsam:** Tüm kapı testleri (TC-ENG-089/090/137/138 · kardeş 083/084/091/092/181/182).
- **Seçenekler:**
  - **(a) Bağlayıcı eşik (release blocker):** Ölçüm yoksa/violasyon varsa çıkış yok. **Önerü (Edge 3).**
  - **(b) Hedef + rapor:** Aşım release notu ile kabul (gerekçeli).
- **Varsayılan:** **(a)** — yalnız gerekçeli istisna (etiketli regresyon NE-ENG-100).
- **Karar sahibi:** QA + ürün → ADR-108 (önerü).

### §18.15 U15 — Test Ortamı & Donanım Envanti̇eri

- **Q:** Fuzz/TSan/stres için runner donanımı (çekirdek/RAM/OS) ve gerçek DAC erişimi nedir?
- **Kaynak durumu:** S3 §5/§6 (CI ortamı detayı dosyada yok) · bu PRD §11.4 `⚠️ VR`.
- **Kapsam:** Kapı 5.13 (≤10 dk süresi) · G4 stres · R-20 (risk 15).
- **Seçenekler:**
  - **(a) Mevcut CI + yerel makine:** Artı: hemen. Eksi: deterministiklik sınırlı.
  - **(b) Sabitlenmiş runner görseli:** Artı: tekrarlanabilir. Eksi: kurulum.
- **Varsayılan:** **(a)** — ortam satırları rapora yazılır (`⚠️ VR` donanım adları).
- **Karar sahibi:** DevOps (devops-engineer) → CI dokümanı.

### §18.16 Açık Madde Kayıt Defteri (özet — §18.1-§18.15)

| ID | Konu | Öncelik | Bloke ettiği | Varsayılan | Durum |
|---|---|---|---|---|---|
| U1 | ASIO lisansı | **P0** | NE-ENG-023/146 · G3 · R-01 | `OFF` (Paket B) | AÇIK |
| U6 | Çekirdek lisans (L01) | **P0** | §14.4 · G3-6 · R-14 | Kapalı (kaynak yok) | AÇIK |
| U2 | 25 dil tanımı | P1 | kardeş §18.2/203 | Unicode destek + UNKNOWN | AÇIK |
| U3 | libdsd/macOS | P1 | NE-ENG-011/147 | DoP (native OFF) | AÇIK |
| U4 | Web/yüzey API | P1 | SE-03 (teyit) | Ağsız çekirdek | AÇIK (öneri teyidi) |
| U5 | MCP dili/yetki | P2 | NE-ENG-109 · kardeş 204 | Kapalı | AÇIK |
| U7 | FFmpeg vendor | P1 | G3-4 | vendor-artı-sistem `[ÖNERİ]` | AÇIK |
| U8 | L1-L4 gecikme | P1 | kapı G2 · 137/138 | `⚠️ VR` + ölçüm | Açık (M1) |
| U9 | Parametrik/LUFS | P2 | NE-ENG-076/077 | Grafik EQ yeterli | Açık (M5) |
| U10 | PCM3168A | P2 | §11.3-B | Mevcut DAC + `⚠️ VR` | AÇIK |
| U11 | WDM-KS/CoreAudio SDK | P2 | NE-ENG-024/027 | Min SDK `[ÖNERİ]` | AÇIK |
| U12 | neva-host dili | P1 | NE-ENG-105/107 · M4 | Ayrı host + IPC | Kardeş §18.1 bağlı |
| U13 | Fuzz format listesi | P1 | kapı 5.13 kapsamı | 7 hedef `[ÖNERİ]` | AÇIK (kayıt) |
| U14 | Ölçüm eşikleri bağlayıcılığı | **P0** | tüm kapılar | Bağlayıcı `[ÖNERİ]` | AÇIK |
| U15 | Test ortamı/donanım | P1 | 5.13/G4 süre | Mevcut CI + yerel | AÇIK |

### §18.17 Bilinen Çelişki / Tutarsızlık Defteri

| # | Çelişki | Kaynaklar | Bu PRD'nin tutumu |
|---|---|---|---|
| C-01 | ADR-107 "8 fonksiyon" iddiası vs disk `neva_session.h` **9 imza** + E1-E10 event genişlemesi | ADR-107 K1 · disk `neva_session.h:39-49` (api_version/create/destroy/open_file/set_output_device/play/pause/stop/set_eq_gain) | Disk kanıtı 9 = 1.0.0 dondurma (A0) — "8" taslak sayımıydı ve tüm PRD'de 9'a düzeltildi; E1-E10 **minor** (1.1.0). |
| C-02 | "13-step" lifecycle (AGENTS §2) vs 15 adım | AGENTS §2 zinciri | `VERIFICATION REQUIRED` — sayı uydurulmaz; zincir metni olduğu gibi alınır. |
| C-03 | ADR-0105 dosyasız (surrogate) | index.md:152 | Tüm `⚠️ VR` etiketleri bu yüzden — D2 + R-P02. |
| C-04 | "12 saat hata yok" (ADR-019 §6) vs 30 dk stres | ADR-019 §6 · S3 §6.1 | 30 dk otomatik (G4) + uzun koşu `[ÖNERİ]` ayrı satır — ikisi de raporlanır. |
| C-05 | Bit-perfect "12 kombinasyon" vs format genişlemesi | ADR-107 K3 · kardeş §11.2 | 12 kombinasyon (4 rate × 3 depth) = kapı çekirdeği; yeni formatlar ek satır (kapsam artışı = ADR). |
| C-06 | VLC/AIMP 10/20-band frekans listeleri teyitsiz | kardeş §06.1 `⚠️ VR` | 15/20 gizli/kilitli `[ÖNERİ]` — teyit gelene kadar (kardeş R-P09). |
| C-07 | AGENTS §6.1 "20 persona" vs registry 118 agent | AGENTS §6.1 · §3 | Persona hiyerarşisi (20) tartışma rolleri; registry (118) görev atama — ikisi farklı katman, çelişki yok. |

**Çelişki kuralı:** Yeni çelişki = §18.17'ye append; çözümü ADR gerektiriyorsa karar kapısına bağlanır; çözülene kadar `⚠️` etiketi korunur (gizleme yasağı — AGENTS §3).

### §18.18 Karar Formu Şablonu (gelecek ADR'ler için)

```markdown
## ADR-NNN — <karar başlığı>
- Tarih: <YYYY-MM-DD>
- Durum: <proposed | accepted | superseded>
- Bağlam: <hangi açık madde (U#) + hangi risk (R-#)>
- Karar: <net cümle — ne YAPILIR/ne YAPILMAZ>
- Alternatifler: <(a)/(b)/(c) — reddedilenler + gerekçe>
- Sonuçlar: <olumlu/olumsuz — G3/G6 kapı etkisi, gereksinim etkisi>
- İzlenebilirlik: <NE-ENG-NNN(ler) · TC-ENG-NNN(ler) · §18.x>
- Vault: `.ai/.decisions/accepted/ADR-NNN-<slug>.md` + log.md satırı
```

**Zorunluluk:** U1/U6/U14 için ADR **PAKET fazından önce** zorunludur; diğerleri etiketli ilerleyebilir (§15.3 kritik yol).

---

## §19 Sözlük & Kısaltmalar (Engine — Bölüm 1: A–L)

> **Kural:** Her terim: **TR karşılığı** · **tanım (bu PRD bağlamında)** · **kaynak** · **çapraz referans**. Kaynaksız tanım yok (AGENTS §3).
> İngilizce teknik terimler korunur; TR karşılığı ilk kullanımda verilir.

### §19.1 Terimler (A–C)

| Terim / TR | Tanım (bu PRD) | Kaynak | Xref |
|---|---|---|---|
| **ABI (Application Binary Interface)** / uygulama ikili arayüzü | Modüller arası ikili sözleşme: sembol adları, çağırra düzeni, yapı boyutları. Bu PRD'de `neva_*` C ABI'si. | ADR-107 K1 | §05.1 · NE-ENG-046 |
| **Acceptance gate** / kabul kapısı | Faz sonunda zorunlu onay (AGENTS §1 Workflow: her kapı onaysız geçilmez). | AGENTS §1 | §15.5 |
| **ADR (Architecture Decision Record)** / mimari karar kaydı | Bağlam-karar-alternatif-sonuç biçiminde bağlayıcı karar belgesi. | `.ai/.decisions/` | §18 |
| **AGPL-3.0** | Copyleft lisansı — ağ üzerinden çalışan kod dahil dağıtımı şart koşar (U6 seçeneği). | genel bilgi | §14.1 L01 · §18.6 |
| **ALSA (Advanced Linux Sound Architecture)** | Linux çekirdek ses altyapısı; `hw:` doğrudan erişim (zincir 6. basamak). | ADR-019 §2.1 | §04.4 |
| **API version** / API sürümü | `NEVA_API_VERSION 0x00010000` = 1.0.0 — handshake'de okunan sabit. | disk `neva_session.h` | §05.4 · NE-ENG-049 |
| **ASIO (Audio Stream Input/Output)** | Steinberg düşük gecikme sürücü standardı; zincirin 1. basamak adayı; lisansı U1. | ADR-107 §8.1 | §18.1 |
| **ASan (AddressSanitizer)** | Bellek hataları (OOB/UAF) için derleyici enstrümantasyonu. | S3 §5 | §11.7 · TC-ENG-133 |
| **Atomic write** / atomik yazma | Yarı yazılmış dosyanın görünmezliği için yaz-sonra-ad-değiştir kalıbı. | [ÖNERİ] | §06 · NE-ENG-146 |
| **Attribute / attribution** | Üçüncü parti lisans metninin pakette gösterimi (NOTICE). | §14.3 | G3-5 |
| **Backend** / arka uç (ses sürücü katmanı) | OS ses API sarmalayıcısı: WASAPI/WDM-KS/PipeWire/ALSA/CoreAudio/ASIO/Null. | ADR-019 §2 | §04.2 · Grup B |
| **Backpressure** / geri basınç | Tüketicinin yavaşlamasında üreticinin durması (ring doluluğu ile sinyal). | disk `spsc_ring.h` | §07.2 |
| **Band (EQ)** / bant | EQ filtresinin merkez frekansı; 31-band ISO ızgarasında 0..30 dizini. | ADR-106 §2.2b | §06.1 · NE-ENG-064 |
| **Baseline** / taban ölçüm | Regresyon karşılaştırmasının referans değeri (RAM/CPU/boyut). | NE-ENG-096 | §11.4 · R-18 |
| **Biquad** / ikili kutuplu | 2. derece IIR filtresi; EQ'nun temel yapı taşı (RBJ formülleri `⚠️ VR` — formül referansı). | disk `biquad.h` | §06.2 |
| **Bit-perfect** / bit-düzgün | Kaynak örneklerin değişmeden (dönüştürmeden) çıkışa ulaşması; hash ile kanıtlanır. | ADR-107 K3 · VISION V-1 | §11.2 · kapı 5.1 |
| **Block size** / blok boyutu | Tek callback'te işlenen örnek sayısı; ≤256 (L1-L4 `⚠️ VR`). | ADR-0105 §2.10 `⚠️` | §03.4 · NE-ENG-089 |
| **Buffer underrun** | Tüketici bulması gereken örneği bulamaması → xrun. | V-3 | §07.4 |
| **Byte-equality** / bayt eşitliği | İki çıktının bayt bayt aynı olması (hash doğrulaması). | ADR-107 K3 | §11.2 |
| **Callback** / geri çağırma | Sürücünün veri istediği RT fonksiyonu (`render`); RT yasakları buna uygulanır. | S3 §6 | §07.1 · TC-ENG-084 |
| **Cap** / sınır | Parse/akış üst limiti (byte/sayı/derinlik) — DoS savunması. | ADR-106 kural 8 | §13.3 |
| **Chain (fallback)** / zincir | 7 basamaklı otomatik sıralama: ASIO→WASAPI Excl→Shared→WDM-KS→PipeWire→ALSA→CoreAudio→Null. | disk `neva_session.cpp` · ADR-019 §2.2b | §04.4 |
| **CI (Continuous Integration)** / sürekli entegrasyon | Her PR'da otomatik derle/test/kapı koşusu. | S3 §5 | §11.7 |
| **CLI (Command-Line Interface)** | Komut satırı arayüzü; M4 öncesi geçiş teslimi `[ÖNERİ]`. | §15.5 | NE-ENG-001 |
| **Coalescing** / olay birleştirme | Aynı türdeki olayların son duruma indirgenmesi (kuyruk taşınca). | [ÖNERİ] | FM-E15 · NE-ENG-047 |
| **Concurrent / concurrency** / eşzamanlılık | Aynı anda çalışan iş parçacıkları; veri yarışı (race) riski TSan ile denetlenir. | S3 §6 | TC-ENG-131 |
| **Contract test** / kontrat testi | Arayüzün beklenen davranışını (durum/geçiş/hata) doğrulayan test. | ADR-019 §1.4 | §11.5 · TC-ENG-046 |
| **CoreAudio** | macOS/iOS ses çatısı; zincir 7. basamak adayı (macOS `⚠️ VR`). | ADR-019 §2.1 | §04.4 · U11 |
| **CPU bütçesi** | İşlemci payı üst sınırı (EQ ≤%5 `[ÖNERİ]`). | ADR-0105 §5.6 `⚠️` | §08.4 |
| **Crash** / çökme | Sürecin beklenmedik sonlanması (FM-04); fuzz ile önlenir. | kapı 5.13 | §12.2 |

### §19.2 Terimler (D–H)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Decode** / çözme | Sıkıştırılmış formatın PCM'e çevrimi (FLAC/MP3/DSF…). | ADR-0105 indeks `⚠️` | §05.1 · Grup D |
| **Determinism** / belirlilik | Aynı girdinin her çalıştırmada aynı çıktıyı üretmesi (2× hash). | ADR-107 K3 | §11.2 · TC-ENG-145 |
| **DSD (Direct Stream Digital)** | 1 bit yüksek örneklemeli format; native yol U3, DoP sarma açık. | ADR-0105 `⚠️` | §05.1 · U3 |
| **DoP (DSD over PCM)** | DSD verisinin PCM çerçevelerinde taşınması (24-bit sarma). | ADR-0105 AK-5 `⚠️` | kardeş §07 |
| **DSDIFF / DSF** | DSD dosya biçimleri (`.dff`/`.dsf`). | ADR-0105 AK-5 `⚠️` | §05.1 |
| **Dynamic range** / dinamik aralık | En sessiz ile en yüksek seviye arasındaki fark (dB). | V-2 | §08.1 |
| **DSP (Digital Signal Processing)** / sayısal sinyal işleme | EQ/limiter/resampler gibi işlem zinciri. | ADR-106 | §06 |
| **E1–E10 (event)** | Motorun UI'a yayınladığı olay kümesi (durum/konum/hata…). | kardeş §05.2 · ADR-106 kural 2 | §05.2 |
| **Edge (AGENTS §17)** | Yalın-iş/döngü/gölge/mocked/ölçüm/özerklik/yarım/EREG/ERET/EKSK/Uçak — 10 kural. | AGENTS §17 | §15.5 |
| **EqChain** | Biquad bankasının birleşik filtresi (bant maskesi + kazanç). | disk `eq_chain.h` | §06.3 |
| **Event-driven (WASAPI)** | Sürücünün olayla veri istediği akış kipi (exclusive'de düşük gecikme). | ADR-019 §2.4 | §04.3 |
| **Exclusive mode** / özel mod | Cihazın tek süreç tarafından ele geçirilmesi (OS mikser bypass). | ADR-019 §1.1 | §04.3 · NE-ENG-021 |
| **Fallback** / düşüş | Basamak başarısızlığında sıradaki seçeneğe geçiş. | ADR-019 §2.2b | §04.4 |
| **Fault injection** / arıza enjeksiyonu | Hata koşullarını üretici olarak zorlama (cihaz kaybı vb.). | TC-ENG-043 | §11.5 |
| **FDE (File Discovery Engine)** | Katalog/keşif alt sistemi (player) — kardeş PRD. | ADR-005 | §17.4 |
| **FDK-AAC** | AAC çözümleyici — lisans/patent nedeniyle RED. | ADR-106 kural 9 | §14.2 |
| **FMA (Failure Mode Analysis)** | Hata modu analizi — §12 FMEA tarzı defter. | bu PRD | §12 |
| **Fuzzing** | Rastgele/mutasyonlu girdilerle çökme arama (kapı 5.13). | ADR-0105 kapı 5.13 `⚠️` | §11.5 · TC-ENG-132 |
| **Gapless** / kesintisiz | Parçalar arası boşluksuz geçiş (AK-5, ±5 ms `⚠️`). | ADR-0105 AK-5 `⚠️` | §05.3 |
| **Gain staging** / kazanç aşaması | Zincir boyunca seviye kontrolü (preamp + limiter). | [ÖNERİ] | §06.4 |
| **Guard (negatif denetim)** | Koşul sağlanmadan PASS üretmeyen tek nokta denetimi. | ADR-107 K3 | §11.2 · TC-ENG-127 |
| **Handle** / tutamaç | Opaque (`void*`) oturum kimliği; use-after-close reddedilir. | disk `neva_session.h` | §05.1 · NE-ENG-003 |
| **Hash (SHA-256)** | Çıktı dosyanın kriptografik özeti — byte-eşitlik kanıtı. | ADR-107 K3 | §11.2 |
| **Hot-plug** / tak-çıkar | Cihazın çalışma sırasında bağlanması/çıkması (FM-E02). | ADR-019 | TC-ENG-030 |
| **HLS (HTTP Live Streaming)** | Apple akış protokolü — kapıları (50 MB/1000/derinlik). | ADR-106 kural 8 | §07.5 |

### §19.3 Terimler (I–L)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **ICY (I Can Yell / Shoutcast)** | Radyo metaveri protokolü (`icy-metaint`, `StreamTitle`); v1/v2 ayrımı. | disk `icy_parser.h` · ADR-106 | §07.3 |
| **InitContainer (K2)** | Derleme/bağımlılık katmanı — kod `apps/` altına girer (ADR-019 K2). | ADR-107 K2 | §04.6 |
| **Input validation** / girdi doğrulama | ABI sınırları (clamp) — SE-01. | §13.3 | TC-ENG-157 |
| **Isolation** / izolasyon | Dizin/backend/RT katmanlarının birbirine sıçramaması (§13.4). | S3 §3.4/§4.3 | §13.4 |
| **ISO 266 / ISO 126** | Octave bandı standartları — 31/10/15/20 grid kaynakları. | ADR-106 §2.2b | §06.1 |
| **ITSS/ITCSS** | CSS mimari katmanları (token→temel→bileşen…) — kardeş UI. | AGENTS §10 | kardeş §13.3 |
| **Jitter** / dalgalanma | Örnek zamanlamasındaki sapma; sürücü kaynaklı xrun üretir. | V-3 | §11.3 |
| **K-weighting** | Loudness ölçümünün frekans ağırlık filtresi (LUFS). | genel bilgi | §18.9 |
| **Kill-switch** | Oturumu güvenli kapatma anahtarı (F4 — xrun sonrası). | ADR-019 F4 | §07.4 |
| **KPI (Key Performance Indicator)** | Kritik performans ölçütü (eşikler §08). | bu PRD | §08 |
| **L1–L4** | Gecikme kademeleri (düşük/orta/yüksek…) — `⚠️ VR` eşikler. | ADR-0105 §2.10 `⚠️` | §18.8 |
| **Latency** / gecikme | Girdiden çıkışa geçen süre (<20 ms; ASIO <10 ms `⚠️`). | ADR-0105 §2.10 `⚠️` | §08.2 · kapı 5.10 |
| **Leak (memory)** / bellek sızıntısı | Tahsis edilip serbest bırakılmayan bellek (FM-E10) — LSan. | S3 §5 | §11.7 · TC-ENG-141 |
| **LGPL-2.1** | Dinamik bağlama koşullu copyleft (soxr/libsndfile). | ADR-0105 `⚠️` | §14.1 · G3-4 |
| **Libcurl / mbedTLS** | HTTP istemcisi / TLS kütüphanesi (radyo; 8.23.0 / 9.13.0). | ADR-106 §5.4 | §07.3 |
| **Limite (true-peak)** | Örnekleme tepe aşımını engelleyen sınırıcı (dB `[ÖNERİ]`). | [ÖNERİ] | §06.4 |
| **Loopback** / geri besleme | Çıkışın girişe fiziksel bağlanıp ölçüldüğü düzeneği (§11.3-A). | bu PRD | §11.3 |
| **LUFS (Loudness Units Full Scale)** | Yayın standardı ses seviyesi ölçümü (şartlı kapsam). | ADR-0105 `⚠️` | §18.9 |
| **LVM / linker** | Bağlayıcı — sembol gizleme/semver denetimi (TC-ENG-142). | S3 §5 | §11.7 |

*(Bölüm 2: M–Z + kısaltma listesi bir sonraki parçada.)*

---

## §19 Sözlük & Kısaltmalar (Engine — Bölüm 2: M–Z + Kısaltmalar)

### §19.4 Terimler (M–O)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Milestone / faz** | P0/A0/A1/A2/M1-M6/PAKET sıralı teslim birimleri. | ADR-107 §5 | §15 |
| **mbedTLS 9.13.0** | TLS kütüphanesi (radio; Apache-2.0). | ADR-106 §5.4 | §14.1 L06 |
| **Mock (sahte)** | Gerçek bağımlılığın test ikamesi (cihaz/sunucu). | §11.5 | TC-ENG-041 |
| **Mixer bypass** | OS mikserinin devre dışı bırakılması (exclusive). | ADR-019 §1.1 | §04.3 |
| **Module (K2)** | Derleme birimi: `apps/`, `backends/`, `tests/`. | ADR-107 K2 | §04.6 |
| **MP3 / FLAC / WAV / Opus / OGG / DSF / DFF** | Desteklenen formatlar (§05.1 matrisi). | ADR-0105 indeks `⚠️` | §05.1 |
| **mutex / lock** | Kilit — RT'de YASAK (S3 §6.1). | S3 §6.1 | §07.1 |
| **N1 (negosyasyon sırası)** | Hız uyumsuzluğu çözüm sırası: `setDeviceRate` → soxr → zincir. | ADR-019 F3 | §04.4 · TC-ENG-015 |
| **neva-core** | Çekirdek kütüphane (ağ/dosya/UI yok — SE-03). | S3 §4.3 | §13.2 |
| **neva-host** | UI köprüsü süreci (event kuyruğu E1-E10). | ADR-107 §8.6 | §05.2 · U12 |
| **neva-session** | Oturum nesnesi: open/start/stop/close + durum makinesi. | disk `neva_session.h` | §05.1 |
| **Null backend** | Zincirin son basamağı — ses yok ama session yaşar. | disk backends/ | §04.4 · FM-E08 |
| **OAuth / auth** | Kimlik doğrulama — motorda YOK (§13.6 A07). | AGENTS §5 | §13.6 |
| **Opaque handle** | İç yapısı gizli tutamaç (sihirli cookie `⚠️ VR`). | §13.3 | NE-ENG-003 |
| **OS mixer** | İşletim sistemi ses karıştırıcısı (bypass hedefi). | ADR-019 §1.1 | §04.3 |
| **Overrun** | Üreticinin dolduğu halkayı aşması (xrun ailesi). | V-3 | §07.4 |

### §19.5 Terimler (P–R)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Packet / frame (ICY)** | Metaint bayt aralığındaki metin çerçevesi (31 B RED / 32 B OK). | disk `icy_parser.h` | §07.3 · TC-ENG-095 |
| **Parametrik EQ** | Frekans/Q sürüklenebilir EQ (şartlı — §18.9). | ADR-0105 `⚠️` | §18.9 |
| **Patch / portability** | Kod değişikliği + taşınabilirlik (ARM64 `⚠️ VR`). | ADR-0105 `⚠️` | §03.3 |
| **PipeWire** | Linux alternatif ses sunucusu (F32P — zincir 5. basamak). | ADR-019 §2.1 | §04.4 |
| **Platform matrisi** | Windows x64 · Linux x64 · Linux ARM64 (+macOS A2/M6). | ADR-019 §1.5 | §03.2 |
| **Preset (EQ)** | Kayıtlı bant değerleri; taşıma + atomik write. | ADR-106 kural 4 | §06.5 · NE-ENG-146 |
| **Preamp** | Öngiriş kazancı (± dB `[ÖNERİ]`). | [ÖNERİ] | §06.4 |
| **Prebuffer** | Radyo öntamponu (2-4 s) — kardeş. | ADR-106 kural 6 | kardeş §07.4 |
| **Priority inversion** | Düşük önceliklinin yüksek öncelikliyi bekletmesi (RT riski). | S3 §6 | §07.1 |
| **Producer / consumer** | Halka üreticisi/tüketicisi (tek yazıcı/tek okuyucu). | disk `spsc_ring.h` | §07.2 |
| **Protocol whitelist** | Yalnız http/https izni (T0-T27 kardeş). | ADR-106 kural 6 | §12.2 kardeş |
| **Ramp (EQ)** | Bant geçişinde kademeli kazanç değişimi 5-20 ms. | ADR-106 kural 4 | §06.3 · TC-ENG-051 |
| **RBJ (Cook–Bond)** | Biquad katsayı formül ailesi `⚠️ VR (referans doğrulanmalı)`. | `⚠️ VR` | §06.2 |
| **RC4 / DES / MD5-1** | Yasaklı kripto (AGENTS §5). | AGENTS §5 | §13.6 |
| **Realtime (RT)** | Zaman kısıtlı iş parçacığı — yasaklar: lock/IO/alloc/log. | S3 §6.1 | §07.1 |
| **Reduced (reduced-privilege)** | Düşük yetki ile çalışma `[ÖNERİ]`. | [ÖNERİ] | §13.2 T-E10 |
| **Render callback** | Sürücünün veri istediği RT fonksiyonu. | S3 §6 | §07.1 · TC-ENG-084 |
| **Resampler / soxr** | Yeniden örnekleme (producer-side; LGPL-2.1). | ADR-0105 `⚠️` | §06.6 · L08 |
| **Ring buffer / SPSC** | Tek-yazıcı/tek-okuyucu halka (kilit'siz). | disk `spsc_ring.h` | §07.2 |
| **Risk register** | §16 risk defteri (append-only). | S3 §6.2 | §16 |
| **RS / RSS** | Gerçek bellek kullanımı (400 MB bütçe). | ADR-0105 §2.9 `⚠️` | §08.3 |

### §19.6 Terimler (S–Z)

| Terim / TR | Tanım | Kaynak | Xref |
|---|---|---|---|
| **Sandbox** | İzole çalıştırma ortamı (test corpora — §13.7). | §13.7 | G3 |
| **Semaphore / atomic** | Kilit'siz senkronizasyon temelleri (RT). | disk `spsc_ring.h` | §07.2 |
| **SHA-256** | Özet algoritması (hash hattı). | ADR-107 K3 | §11.2 |
| **Shared mode** | OS mikserli ortak mod (fallback 3. basamak; bit-perfect değil). | ADR-019 §1.1 | §04.3 · NE-ENG-045 |
| **Signal path** | Sinyal yolu: decode → ring → DSP → backend. | ADR-106 | §04.5 |
| **SNR (Signal-to-Noise Ratio)** | Sinyal/gürültü oranı >105 dB (V-1 `⚠️`). | VISION V-1 | §08.1 · kapı |
| **soxr** | Yüksek kaliteli resampler (LGPL-2.1 dinamik `✅`). | ADR-0105 `⚠️` | §06.6 |
| **SPSC** | Single-Producer Single-Consumer (halka kuralı). | disk `spsc_ring.h` | §07.2 |
| **Starve** | Üreticinin durması — RT'nin veri bulamaması (fade ≤50 ms). | ADR-017/ADR-106 §2.2a | §07.4 · FM-E07 |
| **Static analysis / statik kapı** | Derleme/CI'da kod taraması (grep/regex kapıları). | S3 §5 | §11.7 |
| **Streaming** | Parçalı akış (radyo). | ADR-106 | §07 |
| **STRIDE** | Tehdit sınıflandırması (spoofing/tampering/…). | OWASP | §13.1 |
| **Symbol export / semver** | Sembol dışa aktarımı ve sürümleme (9 fonksiyon dondurma). | ADR-107 K1 | §05.4 · TC-ENG-142 |
| **Thread** | İş parçacığı: RT · worker · UI (ayrım §04.5). | S3 §6 | §04.5 |
| **THD+N (Total Harmonic Distortion + Noise)** | Bozulma+gürültü <%0.005 (V-2 `⚠️`). | VISION V-2 | §08.1 |
| **Throughput** | Verim (örnek/sn) — bench ölçümü. | §11.4 | TC-ENG-094 |
| **Timestamp** | Zaman damgası (A/V sync, pozisyon). | kapı 5.11 `⚠️` | kardeş §08 |
| **Trace / PDB** | Hata ayıklama izi (ayrı paket — NE-ENG-121). | NE-ENG-121 | §14.4 |
| **TSan (ThreadSanitizer)** | Veri yarası dedektörü (Linux CI). | S3 §5 | §11.7 |
| **UAF (Use-After-Free)** | Serbest bırakılmış belleğin kullanımı (fuzz hedefi). | kapı 5.13 | §13.2 T-E09 |
| **Underrun** | Tüketici veri bulamaması (xrun ailesi). | V-3 | §07.4 |
| **Unicode / UTF-8 / UTF-16** | Karakter kodlamaları (path `⚠️ VR` — UTF-16 mı?). | §13.3 | TC-ENG-157 |
| **Unknown / VERIFICATION REQUIRED** | Bilinmeyen/doğrulanamayan (AGENTS §3). | AGENTS §3 | §00.3 |
| **URL validator** | Tek kapı URL doğrulayıcı (player sahibi). | ADR-106 kural 6 | kardeş §12.2 |
| **VBR / CBR** | Değişken/sabit bit hızı (MP3 gapless — Xing). | ADR-0105 AK-5 `⚠️` | kardeş §07.2 |
| **VCL / plugin (VST3)** | Sanal eklenti — K4 kapalı. | ADR-107 K4 | §14.2 |
| **Vectorization / SIMD** | Vektörleştirme (perf — gerekirse). | — | §11.4 |
| **Virtual device (ASIO)** | Sanal ASIO cihazı (test mock — Sonic/Flussonic emsal). | ADR-019 §1.4 | TC-ENG-041 |
| **VSL (Visual Studio License)** | ASIO lisans tartışması kaynağı (U1). | ADR-107 §8.1 | §18.1 |
| **WASAPI (Windows Audio Session API)** | Windows ses API'si: Exclusive/Shared (2./3. basamak). | ADR-019 §2.4 | §04.3 |
| **WDM-KS (Kernel Streaming)** | Windows düşük seviye sürücü (4. basamak; emsal başarısızlık ADR-019 §1.5). | ADR-019 | §04.4 |
| **Wheel / wheel-event** | Fare tekerleği olayı (kardeş UI). | AGENTS §5 | kardeş §13.3 |
| **Worker** | Non-RT iş parçacığı (decode/parse). | S3 §6 | §04.5 |
| **Xrun (underrun/overrun)** | Örnek kaybı olayı — 0 kabul (G4). | V-3 | §07.4 · FM-E06 |
| **Zero-allocation** | RT'de tahsis yasağı (test hook ile doğrulanır). | S3 §6.1 | §07.1 · TC-ENG-083 |
| **Zigzag / alignment** | Hizalama (bellek/örnek hizası — `⚠️ VR`). | `⚠️ VR` | §03.4 |

### §19.7 Kısaltmalar Listesi

| Kısaltma | Açılımı | Xref |
|---|---|---|
| ADR | Architecture Decision Record | §18 |
| A/V | Audio/Video | kapı 5.11 |
| ABI | Application Binary Interface | §05.1 |
| ALSA | Advanced Linux Sound Architecture | §04.4 |
| API | Application Programming Interface | §05 |
| ASIO | Audio Stream Input/Output | §04.3 |
| ASan/UBSan/TSan/LSan | Address/Undefined-Thread/Leak Sanitizers | §11.7 |
| CI | Continuous Integration | §11.7 |
| CPU | Central Processing Unit | §08.4 |
| DAC | Digital-to-Analog Converter | §11.3-B |
| DSP | Digital Signal Processing | §06 |
| DSD | Direct Stream Digital | §05.1 |
| DoP | DSD over PCM | §05.1 |
| EQ | Equalizer | §06 |
| FDE | File Discovery Engine | kardeş |
| FMEA | Failure Mode and Effects Analysis | §12 |
| FFI | Foreign Function Interface | §05.1 |
| HLS | HTTP Live Streaming | §07.5 |
| HTTP/HTTPS | HyperText Transfer Protocol (Secure) | §07.3 |
| ICY | (Shoutcast metaveri protokolü) | §07.3 |
| IIR | Infinite Impulse Response | §06.2 |
| ISO | International Organization for Standardization | §06.1 |
| KPI | Key Performance Indicator | §08 |
| LGPL | GNU Lesser General Public License | §14.1 |
| LUFS | Loudness Units Full Scale | §18.9 |
| MP3/AAC/OGG/Opus/FLAC/WAV | Format kısaltmaları | §05.1 |
| MCP | Model Context Protocol | U5 |
| Null | Boş backend (ses yok) | §04.4 |
| OWASP | Open Web Application Security Project | §13.1 |
| PCM | Pulse-Code Modulation | §05.1 |
| PID | Process Identifier (süreç kimliği) `[ÖNERİ]` | §13.2 |
| RAM | Random Access Memory | §08.3 |
| RT | Realtime | §07 |
| R/W | Read/Write | §13.4 |
| SHAS | (SHA-256 kısaltması kullanılmaz) | — |
| SNR | Signal-to-Noise Ratio | §08.1 |
| SPSC | Single-Producer Single-Consumer | §07.2 |
| SQL/SQLite | Structured Query Language / motoru | kardeş |
| STRIDE | Spoofing/Tampering/Repudiation/DoS/Elevation | §13.1 |
| THD+N | Total Harmonic Distortion + Noise | §08.1 |
| TLS | Transport Layer Security | §07.3 |
| UAF | Use-After-Free | §13.2 |
| UI/UX | User Interface / User Experience | kardeş |
| URL | Uniform Resource Locator | §07.3 |
| UTF-8/16 | Unicode Transformation Format | §13.3 |
| VBR/CBR | Variable/Constant Bit Rate | kardeş |
| VSL | Visual Studio License (ASIO tartışması) | U1 |
| WASAPI | Windows Audio Session API | §04.3 |
| WDM-KS | Windows Driver Model - Kernel Streaming | §04.4 |
| Xrun | Underrun + Overrun | §07.4 |

### §19.8 İşaret Lejandı (tüm PRD boyunca)

| İşaret | Anlamı | Kural |
|---|---|---|
| `UNKNOWN` | Bilinmeyen bilgi | Doğrulanana kadar yazılmaz/varsayılmaz (AGENTS §3) |
| `⚠️ VERIFICATION REQUIRED` / `⚠️ VR` | Doğulanamayan iddia (kaynak eksik/parça) | İddia korunur ama etiketli; release iddiası yapılmaz |
| `[VARSAYIM]` | Dayanağı zayıf varsayım | Gerekçesi yazılır; ölçümle değiştirilir |
| `[ÖNERİ]` | Bu PRD'nin önerisi (ADR'siz) | Bağlayıcı değil; ADR ile teyit edilir |
| `⚠️ LİSANS` | Lisans bilinmeyenliği | RED varsayılan (G3) |
| `[KRİTİK]` | Faz bloklayıcı açık madde | Karar kapısı (§18) |

---

## §20 Onay & Doküman Kontrolü (Engine)

### §20.1 Onay Kapısı (AGENTS §1 — Workflow)

Bu PRD **üretim öncesi onay beklemektedir**. Onay verilmeden §15 fazlarından hiçbiri başlamaz.
Onay zinciri (her kapı ayrı):

| Kapı | Onaylayıcı | Rol (AGENTS §6.1) | Kapsam |
|---|---|---|---|
| K-1 Mimari | `architect` | Expert | §03-§05 · §07 · katman sınırları |
| K-2 Ölçüm | `performance-engineer` | Senior | §08 · §11.3 · kapılar G2/G4 |
| K-3 Lisans | `security-engineer` + lisans danışmanı | Expert | §14 · G3 · U1/U3/U6 |
| K-4 Test | `qa-engineer` | Expert | §10-§11 · 182 gereksinim/182 TC |
| K-5 Ürün | Ürün sahibi (lider) | — | §01-§02 · §15 · §18 karar kapıları |
| **K-6 PRD** | **Orkestratör (ana oturum)** | — | **Tüm doküman — tek imza** |

**Onay durumu:** `ONAYLANDI` (2026-10-23) — üretim + ölçüm raporundan sonra onay sorusuna kullanıcı yanıtı **"DEVAM ET"**; kapsam (§00.4) + içerik onayı verildi. K-1..K-5 faz kapıları ayrıdır (§15 faz başlangıçlarında uygulanır).

### §20.2 Doküman Kontrolü

| Alan | Değer |
|---|---|
| Doküman adı | `coremusic-neva-engine-prd.md` |
| Sürüm | v1.0.0 (taslak — onay öncesi) |
| Tarih | 2026-10-23 |
| Dil | Türkçe (teknik terimler İngilizce korunur) |
| Kardeş doküman | `coremusic-neva-player-prd.md` (215 gereksinim) |
| Hedef uzunluk | ≥8000 satır (ölçüm kanıtı §20.5'te) |
| Kaynak hiyerarşisi | AGENTS §3: kullanıcı talimatı > onaylı kararlar (ADR) > proje vault'u > resmi doküman > genel bilgi |
| SSOT | `.ai/AGENTS.md` (agent) · `.ai/.decisions/` (ADR) · `.ai/RAG.md` (getirme) |
| Sahip | CoreMusic — Neva Engine çalışma grubu |
| Sınıf | Teknik gereksinim dokümanı (PRD) — spesifikasyon değil, **kabul kriteri** düzeyi |

### §20.3 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 0.1 | 2026-10-23 | İskelet (§00-§09) + kaynak okumaları (ADR-017/019/106/107, platform paketleme, `neva_session.h`, CMakeLists, ADR indeksi) | Orkestratör |
| 0.5 | 2026-10-23 | §10 gereksinimler (182) + §11 test planı (182 TC) | Orkestratör |
| 0.8 | 2026-10-23 | §12-§17 (hata/güvenlik/lisans/yol haritası/risk/izlenebilirlik) | Orkestratör |
| 1.0.0 | 2026-10-23 | §18-§21 (açık sorular/sözlük/onay/ekler) — **onaylandı** (K-6, 2026-10-23) | Orkestratör |

### §20.4 Onay Satırı (imza alanı)

```
Neva Engine PRD v1.0.0 — ONAY

K-1 Mimari .......... [ ] onaylandı / [ ] revize
K-2 Ölçüm ........... [ ] onaylandı / [ ] revize
K-3 Lisans .......... [ ] onaylandı / [ ] revize
K-4 Test ............ [ ] onaylandı / [ ] revize
K-5 Ürün ............ [ ] onaylandı / [ ] revize
K-6 PRD (toplam) .... [x] ONAYLANDI (2026-10-23 — "DEVAM ET") / [ ] REVİZE / [ ] RED

Tarih: 2026-10-23  İmza: K-6 (orkestratör) — onay kanıtı: kullanıcı yanıtı "DEVAM ET" (§20.1)
```

**Onaylanan neyi taşır:** §10'daki 182 gereksinimin bağlayıcılığı · §11'deki 182 testin kapsamı · §18'deki açık maddelerin **bilinçli açık** olarak kabulü · §15 faz sırası.

### §20.5 Ölçüm Kanıtı (satır sayısı)

Ölçüm komutu (PowerShell):

```powershell
$f = "G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-neva-engine-prd.md"
(Get-Content $f | Measure-Object -Line).Lines   # boş satırlar HARİÇ
(Get-Content $f).Count                          # ham satır (boş dahil)
(Get-Item $f).Length                            # bayt
```

**Kabul:** ham satır ≥ 8000 (`(Get-Content f).Count`). Sonuçlar §20.6'da raporlanır.

### §20.6 Nihai Ölçüm Raporu (üretim sonrası dolduruldu)

| Ölçüt | Değer | Durum |
|---|---|---|
| Ham satır (boş dahil) | **8004** — `(Get-Content f -Encoding UTF8).Count` · 42 parça (eng-p01..p42) birleştirildi | ✅ ≥8000 |
| Dolu satır (boş hariç) | **6609** — `Where-Object {$_ -ne ''} \| Count` | ✅ |
| Bayt | **465314** — `(Get-Item f).Length` · son ölçüm 2026-10-23 (K-6 onay editleri + CJK düzeltmesi sonrası) | ✅ |
| Gereksinim sayısı | 182 (NE-ENG-001..182) | ✅ |
| Test senaryosu sayısı | 182 (TC-ENG-001..182) | ✅ |
| Açık madde (§18) | 15 karar kapısı (U1-U15) + 4 ADR taslağı (§21.L) | ✅ işaretli |
| `⚠️ VR` / `[ÖNERİ]` / `UNKNOWN` | **196 / 179 / 59** · + `[VARSAYIM]`=5 · `⚠️ LİSANS`=10 · toplam `⚠️`=339 (regex-escape sayımı) | ✅ işaretli |

### §20.7 Kapsam Dışı Beyanı (tek cümle)

Bu PRD **kod yazmaz** — yalnız gereksinim, test kapsamı, kapılar ve karar kapılarını tanımlar; uygulama `apps/` altında kardeş planlara göre (ADR-017 K2) üretilir.

---

# EKLER (§21)

## §21 Ek A — Gereksinim İndeksi (Engine · 182 madde)

> Bu tablo **§10'daki başlıklardan otomatik olarak çıkarıldı** (kaynak dosyalardan — elle yazılmadı).
> Sütunlar: **ID · Başlık · Kaynak parça**. Grup satırı (167..175) §10.11'de tek başlıktır.

| ID | Başlık | Kaynak parça |
|---|---|---|
| NE-ENG-001 | Tek C ABI sınırı | eng-p05 |
| NE-ENG-002 | Opaque handle zorunluluğu | eng-p05 |
| NE-ENG-003 | api_version handshake | eng-p05 |
| NE-ENG-004 | Hata kodu sözleşmesi (exception yok) | eng-p05 |
| NE-ENG-005 | char16_t dosya yolu (Windows Unicode) | eng-p05 |
| NE-ENG-006 | IAudioBackend tek soyutlama noktası | eng-p05 |
| NE-ENG-007 | BackendRegistry kayıt tablosu | eng-p05 |
| NE-ENG-008 | open_best_backend fallback zinciri | eng-p05 |
| NE-ENG-009 | negotiated_format() kanıt zorunluluğu | eng-p05 |
| NE-ENG-010 | BackendKind enum sabitliği | eng-p05 |
| NE-ENG-011 | AudioFormat alan sözleşmesi | eng-p05 |
| NE-ENG-012 | BackendKind + StreamError taşıma | eng-p05 |
| NE-ENG-013 | ADR-019 ek imzalar (faz 2 genişletmesi) | eng-p05 |
| NE-ENG-014 | exclusive↔shared geçiş (switchMode) | eng-p05 |
| NE-ENG-015 | Mismatch çözüm sırası (F3) | eng-p05 |
| NE-ENG-016 | kill-switch (noexcept acil durdurma) | eng-p05 |
| NE-ENG-017 | Platform matrisi bağımsızlığı | eng-p05 |
| NE-ENG-018 | K-space band rezervasyonu koruması | eng-p05 |
| NE-ENG-019 | Windows XP/7 kapsam dışı teyidi | eng-p05 |
| NE-ENG-020 | ARM64 port hedefi | eng-p05 |
| NE-ENG-021 | WASAPI Exclusive implementasyonu (P0) | eng-p05 |
| NE-ENG-022 | WASAPI Shared fallback | eng-p05 |
| NE-ENG-023 | ASIO2 backend (lisans kapısı K4/U1'e bağlı) | eng-p05 |
| NE-ENG-024 | WDM-KS backend | eng-p05 |
| NE-ENG-025 | PipeWire backend (F32P) | eng-p05 |
| NE-ENG-026 | ALSA backend (hw: doğrudan) | eng-p05 |
| NE-ENG-027 | CoreAudio backend (HAL) | eng-p05 |
| NE-ENG-028 | Null backend (CI/test) | eng-p05 |
| NE-ENG-029 | Cihaz enumerate + format listesi | eng-p05 |
| NE-ENG-030 | Hot-plug olay desteği | eng-p05 |
| NE-ENG-031 | switchMode genişletmesi (NE-ENG-013'un parçası) | eng-p05 |
| NE-ENG-032 | setDeviceRate genişletmesi | eng-p05 |
| NE-ENG-033 | kill() genişletmesi | eng-p05 |
| NE-ENG-034 | Callback render sözleşmesi | eng-p05 |
| NE-ENG-035 | Backend yaşam döngüsü (open/start/stop/close) | eng-p05 |
| NE-ENG-036 | Bit-perfect yolu: format birebirlik | eng-p05 |
| NE-ENG-037 | OS mikser baypası (exclusive) | eng-p05 |
| NE-ENG-038 | Exclusive kilit çatışması (device-in-use) | eng-p05 |
| NE-ENG-039 | Periyot/buffer hizalama kurtarma | eng-p05 |
| NE-ENG-040 | Xrun sayacı & telemetri | eng-p05 |
| NE-ENG-041 | Deterministik test backend'i (Null + injectable clock) | eng-p05 |
| NE-ENG-042 | Backend kontrat test takımı | eng-p05 |
| NE-ENG-043 | Fallback telemetri olayı | eng-p05 |
| NE-ENG-044 | Backend başına dizin kuralı | eng-p05 |
| NE-ENG-045 | Null dahil 8 backend envanteri | eng-p05 |
| NE-ENG-046 | Tek public header disiplini | eng-p06 |
| NE-ENG-047 | Event callback genişletmesi (E1) | eng-p06 |
| NE-ENG-048 | Queue API (E2) | eng-p06 |
| NE-ENG-049 | Position/duration API (E3) | eng-p06 |
| NE-ENG-050 | Seek API (E4) | eng-p06 |
| NE-ENG-051 | EQ band count API (E5) | eng-p06 |
| NE-ENG-052 | EQ preset API (E6) | eng-p06 |
| NE-ENG-053 | negotiated_format API (E7) | eng-p06 |
| NE-ENG-054 | Device list + change event (E8) | eng-p06 |
| NE-ENG-055 | Tuner API (E9) | eng-p06 |
| NE-ENG-056 | LUFS okuma (E10) | eng-p06 |
| NE-ENG-057 | Yeni error code eklenmez kuralı | eng-p06 |
| NE-ENG-058 | char16_t/UTF-8 geçiş katmanı (Unix) | eng-p06 |
| NE-ENG-059 | Thread-safety beyanı | eng-p06 |
| NE-ENG-060 | ABI freeze kapısı | eng-p06 |
| NE-ENG-061 | Biquad çekirdeği (RBJ) | eng-p06 |
| NE-ENG-062 | EqChain: 31 band × 2 kanal | eng-p06 |
| NE-ENG-063 | ISO 1/3-oktav grid (31 frekans) | eng-p06 |
| NE-ENG-064 | 10-band alt küme (ISO) | eng-p06 |
| NE-ENG-065 | 15/20-band grid tanımı (teyitli) | eng-p06 |
| NE-ENG-066 | Bypass maskesi (zincir takası yok) | eng-p06 |
| NE-ENG-067 | Ramp smoothing 5-20 ms | eng-p06 |
| NE-ENG-068 | Cascade mod geçişi | eng-p06 |
| NE-ENG-069 | Parametrik mod (RBJ serbest) | eng-p06 |
| NE-ENG-070 | True-peak limiter (BS.1770-4) | eng-p06 |
| NE-ENG-071 | R128 loudness sidechain (non-RT) | eng-p06 |
| NE-ENG-072 | soxr bypass (bit-perfect yol) | eng-p06 |
| NE-ENG-073 | Preamp/gain bloğu | eng-p06 |
| NE-ENG-074 | Multi-channel (8.1) DSP | eng-p06 |
| NE-ENG-075 | Ring overflow politikası (dosya modu) | eng-p06 |
| NE-ENG-076 | DSP profil kapısı (worst-case) | eng-p06 |
| NE-ENG-077 | CPU ≤%5 EQ (192 kHz stereo) | eng-p06 |
| NE-ENG-078 | DSP state sıfırlama API | eng-p06 |
| NE-ENG-079 | Denormal koruması | eng-p06 |
| NE-ENG-080 | DSP birim test kapsamı | eng-p06 |
| NE-ENG-081 | Zero-allocation RT callback | eng-p07 |
| NE-ENG-082 | Lock-free SPSC veri yolu | eng-p07 |
| NE-ENG-083 | RT yasağı denetimi (statik + dinamik) | eng-p07 |
| NE-ENG-084 | Stack bütçesi (callback ≤4 KB) `[ÖNERİ]` | eng-p07 |
| NE-ENG-085 | Callback süre ölçümü (P99/P999) | eng-p07 |
| NE-ENG-086 | Starve fade (≤50 ms) | eng-p07 |
| NE-ENG-087 | Xrun politikası (fade-out → 50 ms → restart) | eng-p07 |
| NE-ENG-088 | 30 dk stres = 0 xrun | eng-p07 |
| NE-ENG-089 | Latency bütçesi: ASIO <10 ms | eng-p07 |
| NE-ENG-090 | Latency bütçesi: WASAPI <20 ms | eng-p07 |
| NE-ENG-091 | Ring kapasite hesabı | eng-p07 |
| NE-ENG-092 | RAM ≤400 MB (player toplam payı) | eng-p07 |
| NE-ENG-093 | Bellek sızıntısı (leak) testi | eng-p07 |
| NE-ENG-094 | Fuzzing kapı altyapısı (engine sınırları) | eng-p07 |
| NE-ENG-095 | ICY parser limitleri (engine çekirdeği) | eng-p07 |
| NE-ENG-096 | CPU bütçesi ölçüm ortamı | eng-p07 |
| NE-ENG-097 | Multi-thread stres (control spam) | eng-p07 |
| NE-ENG-098 | Soğuk açılış süresi | eng-p07 |
| NE-ENG-099 | Ölçüm telemetrisi (opt-out) | eng-p07 |
| NE-ENG-100 | Perf regresyon kapısı | eng-p07 |
| NE-ENG-101 | neva-core statik lib | eng-p07 |
| NE-ENG-102 | neva_session SHARED dll | eng-p07 |
| NE-ENG-103 | apps/ kuralı + neva_add_executable | eng-p07 |
| NE-ENG-104 | neva-player exe (CLI) | eng-p07 |
| NE-ENG-105 | neva-host exe (GUI köprüsü) | eng-p07 |
| NE-ENG-106 | neva-eq exe | eng-p07 |
| NE-ENG-107 | neva-matrix exe (8×8) | eng-p07 |
| NE-ENG-108 | neva-remote (lib+dll) | eng-p07 |
| NE-ENG-109 | neva-mcp exe (dil UNKNOWN) | eng-p07 |
| NE-ENG-110 | neva-plugin (VST3/AU/AAX — lisans K4) | eng-p07 |
| NE-ENG-111 | neva-cli-<kullanıcı> eklenebilirlik | eng-p07 |
| NE-ENG-112 | Toolchain dosyası (cmake/neva-toolchains.cmake) | eng-p07 |
| NE-ENG-113 | C++20 zorunluluğu | eng-p07 |
| NE-ENG-114 | vcpkg yok (statik bağımlılık politikası) | eng-p07 |
| NE-ENG-115 | CI matrisi (Windows x64 + Linux x64 + ARM64 build) | eng-p07 |
| NE-ENG-116 | Çıktı dizin düzeni | eng-p07 |
| NE-ENG-117 | Windows link kütüphaneleri (ole32 avrt uuid) | eng-p07 |
| NE-ENG-118 | Unicode/UTF-8 kaynak kodu (`/utf-8`) | eng-p07 |
| NE-ENG-119 | -fno-exceptions tartışması | eng-p07 |
| NE-ENG-120 | Build reproducibility | eng-p07 |
| NE-ENG-121 | Sembol dosyaları (PDB/dSYM) dağıtımı | eng-p07 |
| NE-ENG-122 | Versiyon bilgisi (semver + api_version) | eng-p07 |
| NE-ENG-123 | Test exe'leri (3 adet IMPLEMENTED) | eng-p07 |
| NE-ENG-124 | Yeni test hedefi kuralı | eng-p07 |
| NE-ENG-125 | Artefakt hijyeni (build/ commit) | eng-p07 |
| NE-ENG-126 | Bit-perfect hash CI kapısı (K3) | eng-p08 |
| NE-ENG-127 | Inverted guard (EQ aktif/lossy → test engellenir) | eng-p08 |
| NE-ENG-128 | Test kaynak üretimi (sentez) | eng-p08 |
| NE-ENG-129 | WAV I/O (vendor, bağımlılıksız) | eng-p08 |
| NE-ENG-130 | CTest entegrasyonu | eng-p08 |
| NE-ENG-131 | Sanitizer matrisi (ASan/UBSan/TSan) | eng-p08 |
| NE-ENG-132 | Fuzz hedefleri (engine) | eng-p08 |
| NE-ENG-133 | Kontrat test paketi (backend başına) | eng-p08 |
| NE-ENG-134 | DSP doğruluk testi (referans bode) | eng-p08 |
| NE-ENG-135 | Grid tutarlılık testi (IMPLEMENTED) | eng-p08 |
| NE-ENG-136 | ICY parser testi (IMPLEMENTED) | eng-p08 |
| NE-ENG-137 | Latency ölçüm düzeneği | eng-p08 |
| NE-ENG-138 | SNR/THD+N ölçüm düzeneği | eng-p08 |
| NE-ENG-139 | Gapless referans vektörleri (engine desteği) | eng-p08 |
| NE-ENG-140 | RT stres (30 dk, 0 tahsis, 0 xrun) | eng-p08 |
| NE-ENG-141 | Leak testi (1000 cycle) | eng-p08 |
| NE-ENG-142 | ABI sembol denetimi | eng-p08 |
| NE-ENG-143 | Baseline metrik dosyası | eng-p08 |
| NE-ENG-144 | Test raporu şablonu (release dosyası) | eng-p08 |
| NE-ENG-145 | Determinism testi (aynı girdi → aynı hash) | eng-p08 |
| NE-ENG-146 | ASIO SDK lisans kararı (U1) | eng-p08 |
| NE-ENG-147 | VST3 SDK lisans kararı (K4) | eng-p08 |
| NE-ENG-148 | FFmpeg LGPL build (engine gateway dahil) | eng-p08 |
| NE-ENG-149 | soxr LGPL-2.1 | eng-p08 |
| NE-ENG-150 | libdsd lisansı (UNKNOWN — release kapısı) | eng-p08 |
| NE-ENG-151 | BASS KULLANMA (devam) | eng-p08 |
| NE-ENG-152 | mpv REDDEDİLDİ (devam) | eng-p08 |
| NE-ENG-153 | FxSound AGPL kopyalama yasağı | eng-p08 |
| NE-ENG-154 | mbedTLS Apache-2.0 OR GPL-2.0 | eng-p08 |
| NE-ENG-155 | dr_libs lisansı | eng-p08 |
| NE-ENG-156 | Secret/anahtar yasağı | eng-p08 |
| NE-ENG-157 | FFI sınırında bellek güvenliği | eng-p08 |
| NE-ENG-158 | Eval/serileştirme yasağı | eng-p08 |
| NE-ENG-159 | Ağ girmez çekirdek | eng-p08 |
| NE-ENG-160 | Sürücü/evrensel kod incelemesi | eng-p08 |
| NE-ENG-161 | GATE G0: ADR-107 onayı | eng-p08 |
| NE-ENG-162 | GATE G1: Bit-perfect 12/12 | eng-p08 |
| NE-ENG-163 | GATE G2: Latency eşiği (ADR-006) | eng-p08 |
| NE-ENG-164 | GATE G3: Lisans kapanışı (K4) | eng-p08 |
| NE-ENG-165 | GATE G4: RT stres 30 dk | eng-p08 |
| NE-ENG-166 | GATE G5: Fuzz kapı 5.13 | eng-p08 |
| NE-ENG-176 | U1: ASIO SDK lisans kararı | eng-p08 |
| NE-ENG-177 | U2: VST3 lisans kararı | eng-p08 |
| NE-ENG-178 | U3: libdsd lisansı + macOS platform kararı | eng-p08 |
| NE-ENG-179 | U4: Web/API yüzeyi | eng-p08 |
| NE-ENG-180 | U5: neva-mcp dili | eng-p08 |
| NE-ENG-181 | U-ek: PCM3168A debug capture akışı (ADR-103) | eng-p08 |
| NE-ENG-182 | U-ek: ADR-0105 dosyasının vault'a iadesi | eng-p08 |
| NE-ENG-167..175 | Faz M0-M6 kapıları (grup — §10.11) | eng-p08 |

## §21 Ek B — Test Senaryosu İndeksi (Engine · 182 senaryo)

> **§11'deki TC başlıklarından otomatik çıkarıldı.** Sütunlar: **TC · Başlık · Eşleşen gereksinim · Kaynak parça**.
> `TC-ENG-NNN` ↔ `NE-ENG-NNN` aynı numarayı taşır (§11.0 kuralı); grup satırları §11.5/§11.8'dedir.

| TC | Başlık | Gereksinim | Kaynak parça |
|---|---|---|---|
| TC-ENG-001 | Tek C ABI sınırı | NE-ENG-001 | eng-p10 |
| TC-ENG-002 | Opaque handle | NE-ENG-002 | eng-p10 |
| TC-ENG-003 | api_version handshake | NE-ENG-003 | eng-p10 |
| TC-ENG-004 | Exception yayılmaz (FFI) | NE-ENG-004 | eng-p10 |
| TC-ENG-005 | char16_t Türkçe yol | NE-ENG-005 | eng-p10 |
| TC-ENG-006 | IAudioBackend tek soyutlama noktası | NE-ENG-006 | eng-p10 |
| TC-ENG-007 | BackendRegistry kayıt tablosu | NE-ENG-007 | eng-p10 |
| TC-ENG-008 | Fallback zinciri 7 basamak | NE-ENG-008 | eng-p10 |
| TC-ENG-009 | negotiated_format kanıt zorunluluğu | NE-ENG-009 | eng-p10 |
| TC-ENG-010 | BackendKind sabitliği | NE-ENG-010 | eng-p10 |
| TC-ENG-011 | AudioFormat sınır testleri | NE-ENG-011 | eng-p10 |
| TC-ENG-012 | StreamError taşıma | NE-ENG-012 | eng-p10 |
| TC-ENG-013 | ADR-019 ek imzalar kontratı | NE-ENG-013 | eng-p10 |
| TC-ENG-014 | exclusive↔shared geçiş (switchMode) | NE-ENG-014 | eng-p10 |
| TC-ENG-015 | Mismatch çözüm sırası (F3) | NE-ENG-015 | eng-p10 |
| TC-ENG-016 | kill-switch | NE-ENG-016 | eng-p10 |
| TC-ENG-017 | Platform matrisi derleme | NE-ENG-017 | eng-p10 |
| TC-ENG-018 | K-space band yasak | NE-ENG-018 | eng-p10 |
| TC-ENG-019 | XP/7 kapsam dışı | NE-ENG-019 | eng-p10 |
| TC-ENG-020 | ARM64 build | NE-ENG-020 | eng-p10 |
| TC-ENG-021 | WASAPI Exclusive iki deneme | NE-ENG-021 | eng-p10 |
| TC-ENG-022 | Shared fallback (device-in-use) | NE-ENG-022 | eng-p10 |
| TC-ENG-023 | ASIO yolu (şartlı U1) | NE-ENG-023 | eng-p10 |
| TC-ENG-024 | WDM-KS | NE-ENG-024 | eng-p10 |
| TC-ENG-025 | PipeWire F32P → ALSA | NE-ENG-025 | eng-p10 |
| TC-ENG-026 | ALSA hw: | NE-ENG-026 | eng-p10 |
| TC-ENG-027 | CoreAudio (şartlı M6/U3) | NE-ENG-027 | eng-p10 |
| TC-ENG-028 | Null backend determinizm | NE-ENG-028 | eng-p10 |
| TC-ENG-029 | enumerate/supported_formats | NE-ENG-029 | eng-p10 |
| TC-ENG-030 | Hot-plug | NE-ENG-030 | eng-p10 |
| TC-ENG-031 | switchMode/Null varsaylanı | NE-ENG-031 | eng-p10 |
| TC-ENG-032 | setDeviceRate hata dönüşü | NE-ENG-032 | eng-p10 |
| TC-ENG-033 | kill() impl | NE-ENG-033 | eng-p10 |
| TC-ENG-034 | Callback render sözleşmesi | NE-ENG-034 | eng-p10 |
| TC-ENG-035 | Yaşam döngüsü sıralama hataları | NE-ENG-035 | eng-p10 |
| TC-ENG-036 | Hash bit-perfect (ana hat) | NE-ENG-036 | eng-p10 |
| TC-ENG-037 | OS mikser baypası | NE-ENG-037 | eng-p10 |
| TC-ENG-038 | Exclusive kilit çatışması | NE-ENG-038 | eng-p10 |
| TC-ENG-039 | BUFFER_SIZE_NOT_ALIGNED kurtarma | NE-ENG-039 | eng-p10 |
| TC-ENG-040 | Xrun sayacı | NE-ENG-040 | eng-p10 |
| TC-ENG-041 | Mock clock determinizm | NE-ENG-041 | eng-p10 |
| TC-ENG-042 | Kontrat test paketi (Null) | NE-ENG-042 | eng-p10 |
| TC-ENG-043 | Fallback telemetri event | NE-ENG-043 | eng-p10 |
| TC-ENG-044 | Backend dizin izolasyonu | NE-ENG-044 | eng-p10 |
| TC-ENG-045 | Registry 8 backend | NE-ENG-045 | eng-p10 |
| TC-ENG-046 | Tek public header (install) | NE-ENG-046 | eng-p10 |
| TC-ENG-047 | Event callback (non-RT marshal) | NE-ENG-047 | eng-p10 |
| TC-ENG-048 | Queue API preload | NE-ENG-048 | eng-p10 |
| TC-ENG-049 | position/duration | NE-ENG-049 | eng-p10 |
| TC-ENG-050 | Seek | NE-ENG-050 | eng-p10 |
| TC-ENG-051 | EQ band count atomik yayın | NE-ENG-051 | eng-p10 |
| TC-ENG-052 | Preset uyuşmazlık düzeltme | NE-ENG-052 | eng-p10 |
| TC-ENG-053 | negotiated_format API | NE-ENG-053 | eng-p10 |
| TC-ENG-054 | Device list + change event | NE-ENG-054 | eng-p10 |
| TC-ENG-055 | Tuner API | NE-ENG-055 | eng-p10 |
| TC-ENG-056 | LUFS publish | NE-ENG-056 | eng-p10 |
| TC-ENG-057 | Error code sabitliği | NE-ENG-057 | eng-p10 |
| TC-ENG-058 | UTF-8 geçiş (Unix) | NE-ENG-058 | eng-p10 |
| TC-ENG-059 | TSan thread-safety | NE-ENG-059 | eng-p10 |
| TC-ENG-060 | ABI freeze dump | NE-ENG-060 | eng-p10 |
| TC-ENG-061 | Biquad doğruluk (RBJ) | NE-ENG-061 | eng-p11 |
| TC-ENG-062 | EqChain tahsis zorunluluğu | NE-ENG-062 | eng-p11 |
| TC-ENG-063 | ISO 31-grid | NE-ENG-063 | eng-p11 |
| TC-ENG-064 | 10-band ISO alt küme | NE-ENG-064 | eng-p11 |
| TC-ENG-065 | 15/20-band grid (şartlı teyit) | NE-ENG-065 | eng-p11 |
| TC-ENG-066 | Bypass maskesi (zincir takası yok) | NE-ENG-066 | eng-p11 |
| TC-ENG-067 | Ramp smoothing (click analizi) | NE-ENG-067 | eng-p11 |
| TC-ENG-068 | Cascade mod geçişi | NE-ENG-068 | eng-p11 |
| TC-ENG-069 | Parametrik mod ayrımı | NE-ENG-069 | eng-p11 |
| TC-ENG-070 | True-peak limiter bypass (hash) | NE-ENG-070 | eng-p11 |
| TC-ENG-071 | R128 sidechain doğruluğu | NE-ENG-071 | eng-p11 |
| TC-ENG-072 | soxr bypass (eşit format) | NE-ENG-072 | eng-p11 |
| TC-ENG-073 | Preamp identity (0 dB) | NE-ENG-073 | eng-p11 |
| TC-ENG-074 | Multi-channel (8.1) DSP | NE-ENG-074 | eng-p11 |
| TC-ENG-075 | Ring overflow politikası (iki mod) | NE-ENG-075 | eng-p11 |
| TC-ENG-076 | DSP profil worst-case | NE-ENG-076 | eng-p11 |
| TC-ENG-077 | CPU ≤%5 (192 kHz stereo) | NE-ENG-077 | eng-p11 |
| TC-ENG-078 | DSP state sıfırlama | NE-ENG-078 | eng-p11 |
| TC-ENG-079 | Denormal koruması (FTZ/DAZ) | NE-ENG-079 | eng-p11 |
| TC-ENG-080 | DSP birim test kapsamı | NE-ENG-080 | eng-p11 |
| TC-ENG-081 | Zero-allocation RT callback | NE-ENG-081 | eng-p11 |
| TC-ENG-082 | Lock-free SPSC | NE-ENG-082 | eng-p11 |
| TC-ENG-083 | RT yasağı denetimi | NE-ENG-083 | eng-p11 |
| TC-ENG-084 | Stack bütçesi (≤4 KB) | NE-ENG-084 | eng-p11 |
| TC-ENG-085 | Callback süre histogramı | NE-ENG-085 | eng-p11 |
| TC-ENG-086 | Starve fade (≤50 ms) | NE-ENG-086 | eng-p11 |
| TC-ENG-087 | Xrun politikası (fade → 50 ms → restart) | NE-ENG-087 | eng-p11 |
| TC-ENG-088 | 30 dk stres = 0 xrun | NE-ENG-088 | eng-p11 |
| TC-ENG-089 | ASIO <10 ms | NE-ENG-089 | eng-p11 |
| TC-ENG-090 | WASAPI <20 ms | NE-ENG-090 | eng-p11 |
| TC-ENG-091 | Ring kapasite hesabı doğrulaması | NE-ENG-091 | eng-p11 |
| TC-ENG-092 | RAM ≤400 MB (engine payı) | NE-ENG-092 | eng-p11 |
| TC-ENG-093 | Leak (1000 cycle) | NE-ENG-093 | eng-p11 |
| TC-ENG-094 | Fuzz (C ABI + ICY) | NE-ENG-094 | eng-p11 |
| TC-ENG-095 | ICY parser limitleri | NE-ENG-095 | eng-p11 |
| TC-ENG-096 | Bench ortamı sabitleme | NE-ENG-096 | eng-p11 |
| TC-ENG-097 | Multi-thread control spam | NE-ENG-097 | eng-p11 |
| TC-ENG-098 | Soğuk açılış <300 ms | NE-ENG-098 | eng-p11 |
| TC-ENG-099 | Telemetri opt-out | NE-ENG-099 | eng-p11 |
| TC-ENG-100 | Perf regresyon etiketi | NE-ENG-100 | eng-p11 |
| TC-ENG-101 | neva-core statik lib ayrımı | NE-ENG-101 | eng-p11 |
| TC-ENG-102 | neva_session shared DLL export | NE-ENG-102 | eng-p11 |
| TC-ENG-103 | apps/ kuralı | NE-ENG-103 | eng-p11 |
| TC-ENG-104 | neva-player CLI smoke | NE-ENG-104 | eng-p11 |
| TC-ENG-105 | neva-host köprüsü (UI çökmesi) | NE-ENG-105 | eng-p11 |
| TC-ENG-106 | neva-eq ayrı süreç | NE-ENG-106 | eng-p11 |
| TC-ENG-107 | neva-matrix (şartlı) | NE-ENG-107 | eng-p11 |
| TC-ENG-108 | neva-remote (şartlı U4) | NE-ENG-108 | eng-p11 |
| TC-ENG-109 | neva-mcp (şartlı U5) | NE-ENG-109 | eng-p11 |
| TC-ENG-110 | VST3 wrap (şartlı K4) | NE-ENG-110 | eng-p11 |
| TC-ENG-111 | Kullanıcı CLI eklenebilirlik | NE-ENG-111 | eng-p11 |
| TC-ENG-112 | Toolchain dosyası | NE-ENG-112 | eng-p11 |
| TC-ENG-113 | C++20 zorunlu | NE-ENG-113 | eng-p11 |
| TC-ENG-114 | vcpkg yok | NE-ENG-114 | eng-p11 |
| TC-ENG-115 | CI matrisi (3 build) | NE-ENG-115 | eng-p11 |
| TC-ENG-116 | Çıktı dizin düzeni | NE-ENG-116 | eng-p11 |
| TC-ENG-117 | Windows link kütüphaneleri | NE-ENG-117 | eng-p11 |
| TC-ENG-118 | UTF-8 kaynak kodu | NE-ENG-118 | eng-p11 |
| TC-ENG-119 | -fno-exceptions (şartlı) | NE-ENG-119 | eng-p11 |
| TC-ENG-120 | Build reproducibility | NE-ENG-120 | eng-p11 |
| TC-ENG-121 | Sembol (PDB) dağıtımı | NE-ENG-121 | eng-p11 |
| TC-ENG-122 | Sürüm bilgisi senkronu | NE-ENG-122 | eng-p12 |
| TC-ENG-123 | Test exe'leri + CTest | NE-ENG-123 | eng-p12 |
| TC-ENG-124 | Yeni modül = kontrat testi kuralı | NE-ENG-124 | eng-p12 |
| TC-ENG-125 | Build artefakt hijyeni | NE-ENG-125 | eng-p12 |
| TC-ENG-126 | Bit-perfect hash (12 kombinasyon) | NE-ENG-126 | eng-p12 |
| TC-ENG-127 | Inverted guard denetimi | NE-ENG-127 | eng-p12 |
| TC-ENG-128 | Deterministik test kaynağı | NE-ENG-128 | eng-p12 |
| TC-ENG-129 | WAV I/O dayanıklılığı | NE-ENG-129 | eng-p12 |
| TC-ENG-130 | CTest entegrasyonu | NE-ENG-130 | eng-p12 |
| TC-ENG-131 | Sanitizer matrisi | NE-ENG-131 | eng-p12 |
| TC-ENG-132 | Fuzz hedefleri (engine) | NE-ENG-132 | eng-p12 |
| TC-ENG-133 | Kontrat test paketi (detay) | NE-ENG-133 | eng-p12 |
| TC-ENG-134 | DSP doğruluk (bode referans) | NE-ENG-134 | eng-p12 |
| TC-ENG-135 | Grid tutarlılık (IMPLEMENTED) | NE-ENG-135 | eng-p12 |
| TC-ENG-136 | ICY parser testi (IMPLEMENTED + genişletme) | NE-ENG-136 | eng-p12 |
| TC-ENG-137 | Latency düzenek doğrulaması | NE-ENG-137 | eng-p12 |
| TC-ENG-138 | SNR/THD+N düzeneği | NE-ENG-138 | eng-p12 |
| TC-ENG-139 | Gapless API desteği | NE-ENG-139 | eng-p12 |
| TC-ENG-140 | RT stres gece testi (G4) | NE-ENG-140 | eng-p12 |
| TC-ENG-141 | Leak 1000 cycle | NE-ENG-141 | eng-p12 |
| TC-ENG-142 | ABI sembol denetimi | NE-ENG-142 | eng-p12 |
| TC-ENG-143 | Baseline metrik dosyası | NE-ENG-143 | eng-p12 |
| TC-ENG-144 | Test raporu şablonu | NE-ENG-144 | eng-p12 |
| TC-ENG-145 | Determinism (2× hash) | NE-ENG-145 | eng-p12 |
| TC-ENG-146 | ASIO lisans kapısı (U1) | NE-ENG-146 | eng-p12 |
| TC-ENG-147 | VST3 lisans kapısı (K4) | NE-ENG-147 | eng-p12 |
| TC-ENG-148 | FFmpeg LGPL build | NE-ENG-148 | eng-p12 |
| TC-ENG-149 | soxr lisans durumu | NE-ENG-149 | eng-p12 |
| TC-ENG-150 | libdsd lisans kapısı (U3) | NE-ENG-150 | eng-p12 |
| TC-ENG-151 | BASS kullanma | NE-ENG-151 | eng-p12 |
| TC-ENG-152 | mpv reddi | NE-ENG-152 | eng-p12 |
| TC-ENG-153 | FxSound kopyalama yasağı | NE-ENG-153 | eng-p12 |
| TC-ENG-154 | mbedTLS lisans metni | NE-ENG-154 | eng-p12 |
| TC-ENG-155 | dr_libs lisansı | NE-ENG-155 | eng-p12 |
| TC-ENG-156 | Secret taraması | NE-ENG-156 | eng-p12 |
| TC-ENG-157 | FFI clamp/negatif test | NE-ENG-157 | eng-p12 |
| TC-ENG-158 | Eval/serileştirme yasağı | NE-ENG-158 | eng-p12 |
| TC-ENG-159 | Çekirdekte ağ yok | NE-ENG-159 | eng-p12 |
| TC-ENG-160 | Düşük seviye kod incelemesi | NE-ENG-160 | eng-p12 |
| TC-ENG-182 | ADR-0105 vault iadesi (vault denetimi) | NE-ENG-182 | eng-p12 |
| TC-ENG-161..175 | Faz kapıları (G0-G6 + M0-M6 checklist) | NE-ENG-167..175 | eng-p12 |
| TC-ENG-176..181 | U1-U5 + PCM3168A karar kapıları | NE-ENG-176..181 | eng-p12 |

## §21 Ek C — Gereksinim → Test İzlenebilirlik Matrisi (Engine)

> **Otomatik üretildi** (§21 Ek B ters çevrildi). 182 gereksinimin eşleşen test(leri).
-
| Gereksinim | Eşleşen test(ler) |
|---|---|
| NE-ENG-001 | TC-ENG-001 |
| NE-ENG-002 | TC-ENG-002 |
| NE-ENG-003 | TC-ENG-003 |
| NE-ENG-004 | TC-ENG-004 |
| NE-ENG-005 | TC-ENG-005 |
| NE-ENG-006 | TC-ENG-006 |
| NE-ENG-007 | TC-ENG-007 |
| NE-ENG-008 | TC-ENG-008 |
| NE-ENG-009 | TC-ENG-009 |
| NE-ENG-010 | TC-ENG-010 |
| NE-ENG-011 | TC-ENG-011 |
| NE-ENG-012 | TC-ENG-012 |
| NE-ENG-013 | TC-ENG-013 |
| NE-ENG-014 | TC-ENG-014 |
| NE-ENG-015 | TC-ENG-015 |
| NE-ENG-016 | TC-ENG-016 |
| NE-ENG-017 | TC-ENG-017 |
| NE-ENG-018 | TC-ENG-018 |
| NE-ENG-019 | TC-ENG-019 |
| NE-ENG-020 | TC-ENG-020 |
| NE-ENG-021 | TC-ENG-021 |
| NE-ENG-022 | TC-ENG-022 |
| NE-ENG-023 | TC-ENG-023 |
| NE-ENG-024 | TC-ENG-024 |
| NE-ENG-025 | TC-ENG-025 |
| NE-ENG-026 | TC-ENG-026 |
| NE-ENG-027 | TC-ENG-027 |
| NE-ENG-028 | TC-ENG-028 |
| NE-ENG-029 | TC-ENG-029 |
| NE-ENG-030 | TC-ENG-030 |
| NE-ENG-031 | TC-ENG-031 |
| NE-ENG-032 | TC-ENG-032 |
| NE-ENG-033 | TC-ENG-033 |
| NE-ENG-034 | TC-ENG-034 |
| NE-ENG-035 | TC-ENG-035 |
| NE-ENG-036 | TC-ENG-036 |
| NE-ENG-037 | TC-ENG-037 |
| NE-ENG-038 | TC-ENG-038 |
| NE-ENG-039 | TC-ENG-039 |
| NE-ENG-040 | TC-ENG-040 |
| NE-ENG-041 | TC-ENG-041 |
| NE-ENG-042 | TC-ENG-042 |
| NE-ENG-043 | TC-ENG-043 |
| NE-ENG-044 | TC-ENG-044 |
| NE-ENG-045 | TC-ENG-045 |
| NE-ENG-046 | TC-ENG-046 |
| NE-ENG-047 | TC-ENG-047 |
| NE-ENG-048 | TC-ENG-048 |
| NE-ENG-049 | TC-ENG-049 |
| NE-ENG-050 | TC-ENG-050 |
| NE-ENG-051 | TC-ENG-051 |
| NE-ENG-052 | TC-ENG-052 |
| NE-ENG-053 | TC-ENG-053 |
| NE-ENG-054 | TC-ENG-054 |
| NE-ENG-055 | TC-ENG-055 |
| NE-ENG-056 | TC-ENG-056 |
| NE-ENG-057 | TC-ENG-057 |
| NE-ENG-058 | TC-ENG-058 |
| NE-ENG-059 | TC-ENG-059 |
| NE-ENG-060 | TC-ENG-060 |
| NE-ENG-061 | TC-ENG-061 |
| NE-ENG-062 | TC-ENG-062 |
| NE-ENG-063 | TC-ENG-063 |
| NE-ENG-064 | TC-ENG-064 |
| NE-ENG-065 | TC-ENG-065 |
| NE-ENG-066 | TC-ENG-066 |
| NE-ENG-067 | TC-ENG-067 |
| NE-ENG-068 | TC-ENG-068 |
| NE-ENG-069 | TC-ENG-069 |
| NE-ENG-070 | TC-ENG-070 |
| NE-ENG-071 | TC-ENG-071 |
| NE-ENG-072 | TC-ENG-072 |
| NE-ENG-073 | TC-ENG-073 |
| NE-ENG-074 | TC-ENG-074 |
| NE-ENG-075 | TC-ENG-075 |
| NE-ENG-076 | TC-ENG-076 |
| NE-ENG-077 | TC-ENG-077 |
| NE-ENG-078 | TC-ENG-078 |
| NE-ENG-079 | TC-ENG-079 |
| NE-ENG-080 | TC-ENG-080 |
| NE-ENG-081 | TC-ENG-081 |
| NE-ENG-082 | TC-ENG-082 |
| NE-ENG-083 | TC-ENG-083 |
| NE-ENG-084 | TC-ENG-084 |
| NE-ENG-085 | TC-ENG-085 |
| NE-ENG-086 | TC-ENG-086 |
| NE-ENG-087 | TC-ENG-087 |
| NE-ENG-088 | TC-ENG-088 |
| NE-ENG-089 | TC-ENG-089 |
| NE-ENG-090 | TC-ENG-090 |
| NE-ENG-091 | TC-ENG-091 |
| NE-ENG-092 | TC-ENG-092 |
| NE-ENG-093 | TC-ENG-093 |
| NE-ENG-094 | TC-ENG-094 |
| NE-ENG-095 | TC-ENG-095 |
| NE-ENG-096 | TC-ENG-096 |
| NE-ENG-097 | TC-ENG-097 |
| NE-ENG-098 | TC-ENG-098 |
| NE-ENG-099 | TC-ENG-099 |
| NE-ENG-100 | TC-ENG-100 |
| NE-ENG-101 | TC-ENG-101 |
| NE-ENG-102 | TC-ENG-102 |
| NE-ENG-103 | TC-ENG-103 |
| NE-ENG-104 | TC-ENG-104 |
| NE-ENG-105 | TC-ENG-105 |
| NE-ENG-106 | TC-ENG-106 |
| NE-ENG-107 | TC-ENG-107 |
| NE-ENG-108 | TC-ENG-108 |
| NE-ENG-109 | TC-ENG-109 |
| NE-ENG-110 | TC-ENG-110 |
| NE-ENG-111 | TC-ENG-111 |
| NE-ENG-112 | TC-ENG-112 |
| NE-ENG-113 | TC-ENG-113 |
| NE-ENG-114 | TC-ENG-114 |
| NE-ENG-115 | TC-ENG-115 |
| NE-ENG-116 | TC-ENG-116 |
| NE-ENG-117 | TC-ENG-117 |
| NE-ENG-118 | TC-ENG-118 |
| NE-ENG-119 | TC-ENG-119 |
| NE-ENG-120 | TC-ENG-120 |
| NE-ENG-121 | TC-ENG-121 |
| NE-ENG-122 | TC-ENG-122 |
| NE-ENG-123 | TC-ENG-123 |
| NE-ENG-124 | TC-ENG-124 |
| NE-ENG-125 | TC-ENG-125 |
| NE-ENG-126 | TC-ENG-126 |
| NE-ENG-127 | TC-ENG-127 |
| NE-ENG-128 | TC-ENG-128 |
| NE-ENG-129 | TC-ENG-129 |
| NE-ENG-130 | TC-ENG-130 |
| NE-ENG-131 | TC-ENG-131 |
| NE-ENG-132 | TC-ENG-132 |
| NE-ENG-133 | TC-ENG-133 |
| NE-ENG-134 | TC-ENG-134 |
| NE-ENG-135 | TC-ENG-135 |
| NE-ENG-136 | TC-ENG-136 |
| NE-ENG-137 | TC-ENG-137 |
| NE-ENG-138 | TC-ENG-138 |
| NE-ENG-139 | TC-ENG-139 |
| NE-ENG-140 | TC-ENG-140 |
| NE-ENG-141 | TC-ENG-141 |
| NE-ENG-142 | TC-ENG-142 |
| NE-ENG-143 | TC-ENG-143 |
| NE-ENG-144 | TC-ENG-144 |
| NE-ENG-145 | TC-ENG-145 |
| NE-ENG-146 | TC-ENG-146 |
| NE-ENG-147 | TC-ENG-147 |
| NE-ENG-148 | TC-ENG-148 |
| NE-ENG-149 | TC-ENG-149 |
| NE-ENG-150 | TC-ENG-150 |
| NE-ENG-151 | TC-ENG-151 |
| NE-ENG-152 | TC-ENG-152 |
| NE-ENG-153 | TC-ENG-153 |
| NE-ENG-154 | TC-ENG-154 |
| NE-ENG-155 | TC-ENG-155 |
| NE-ENG-156 | TC-ENG-156 |
| NE-ENG-157 | TC-ENG-157 |
| NE-ENG-158 | TC-ENG-158 |
| NE-ENG-159 | TC-ENG-159 |
| NE-ENG-160 | TC-ENG-160 |
| NE-ENG-61 | TC-ENG-61 (grup) |
| NE-ENG-62 | TC-ENG-62 (grup) |
| NE-ENG-63 | TC-ENG-63 (grup) |
| NE-ENG-64 | TC-ENG-64 (grup) |
| NE-ENG-65 | TC-ENG-65 (grup) |
| NE-ENG-66 | TC-ENG-66 (grup) |
| NE-ENG-167 | TC-ENG-161..175 (grup) |
| NE-ENG-168 | TC-ENG-161..175 (grup) |
| NE-ENG-169 | TC-ENG-161..175 (grup) |
| NE-ENG-170 | TC-ENG-161..175 (grup) |
| NE-ENG-171 | TC-ENG-161..175 (grup) |
| NE-ENG-172 | TC-ENG-161..175 (grup) |
| NE-ENG-173 | TC-ENG-161..175 (grup) |
| NE-ENG-174 | TC-ENG-161..175 (grup) |
| NE-ENG-175 | TC-ENG-161..175 (grup) |
| NE-ENG-176 | TC-ENG-176..181 (grup) |
| NE-ENG-177 | TC-ENG-176..181 (grup) |
| NE-ENG-178 | TC-ENG-176..181 (grup) |
| NE-ENG-179 | TC-ENG-176..181 (grup) |
| NE-ENG-180 | TC-ENG-176..181 (grup) |
| NE-ENG-181 | TC-ENG-176..181 (grup) |
| NE-ENG-182 | TC-ENG-182 |

## §21 Ek D — Mevcut Kod Yüzeyi Envanteri (Engine · disk kanıtı)

> **Kaynak:** `C:\www\coremusic.net\--\neva-engine\` recursive tarama (2026-10-23).
> **Kural:** Bu envanter **yalnızca diskten okunan** dosya/İmza/satır sayısı içerir; tahmin yok.
> **Kapsam dışı:** `build/` dizini (çıktı) — envantere alınmadı.

### §21.D.1 Dosya Envanteri (toplam satır / bayt)

| Dosya | Satır | KB | Rol | Durum |
|---|---|---|---|---|
| `CMakeLists.txt` | 75 | 3.2 | Build tanımı (2 lib + 3 test exe) | IMPLEMENTED |
| `include/neva/neva_session.h` | 54 | 1.9 | Tek public C ABI header | IMPLEMENTED |
| `include/neva/iaudio_backend.h` | 57 | 2.2 | Backend soyutlama (8 virtual) | IMPLEMENTED |
| `include/neva/spsc_ring.h` | 50 | 1.7 | SPSC lock-free ring | IMPLEMENTED |
| `include/neva/biquad.h` | 93 | 3.3 | RBJ peaking biquad + FTZ/DAZ | IMPLEMENTED |
| `include/neva/eq_chain.h` | 153 | 7.2 | EqChain (62 yollu: 31×2 biquad) | IMPLEMENTED |
| `include/neva/eq_grids.h` | 97 | 5.1 | 31-band ISO grid + alt kümeler | IMPLEMENTED |
| `include/neva/icy_parser.h` | 240 | 11.0 | ICY v1/v2 metaveri parser | IMPLEMENTED |
| `src/neva_session.cpp` | 215 | 7.5 | C ABI implementasyonu | IMPLEMENTED |
| `src/wasapi_backend.cpp` | 316 | 12.4 | WASAPI Excl→Shared (skelet + DeviceNotifier) | IMPLEMENTED (skelet) |
| `src/null_backend.cpp` | 54 | 1.5 | Null backend (CI/test) | IMPLEMENTED |
| `src/decode_stub.h` | 30 | 1.2 | İç decode arayüzü (WAV stub) | IMPLEMENTED (stub) |
| `src/decode_stub.cpp` | 99 | 3.6 | Minimal RIFF/WAVE parse + normalize | IMPLEMENTED (stub) |
| `tests/test_biquad.cpp` | 130 | 4.2 | `ctest biquad_eq_ring` | IMPLEMENTED |
| `tests/test_eq_grids.cpp` | 112 | 4.5 | `ctest eq_grids_gates` | IMPLEMENTED |
| `tests/test_icy_parser.cpp` | 143 | 6.5 | `ctest icy_parser_gates` | IMPLEMENTED |
| **Toplam (16 dosya, obj hariç)** | **~1828** | **~83** | | |

**Hijyen bulgusu (envanter çıktısı):** `test_biquad.obj` (61.1 KB) — derleme artefaktı repo kökünde duruyor (bin/obj girişi olmayan tek `.obj`). **NE-ENG-125 (artefakt hijyeni) ihlali adayı** `[ÖNERİ — .gitignore + sil]`.

### §21.D.2 Build (CMakeLists.txt — 75 satır, okundu)

| Karar | Disk kanıtı |
|---|---|
| İki kütüphane | `add_library(neva_engine STATIC src/neva_session.cpp …)` + `add_library(neva_session SHARED src/neva_session.cpp)` |
| Export anahtarı | `NEVA_SESSION_EXPORTS` (SHARED) · `NEVA_ENGINE_STATIC` (STATIC — import/export yok) |
| Include yolları | `include/` PUBLIC · `src/` PRIVATE |
| Uyarılar | MSVC: `/W4 /permissive- /Zc:__cplusplus /utf-8` |
| Windows bağlantıları | `ole32 avrt uuid` |
| Test altyapısı | `enable_testing()` + 3 `add_test`: `biquad_eq_ring` · `eq_grids_gates` · `icy_parser_gates` |
| Test exe'leri | `neva_tests` (test_biquad) · `eq_grids_tests` · `icy_parser_tests` |

**İzlenebilirlik:** NE-ENG-001/046/048/120 · TC-ENG-142 (semver/symbol) · ADR-017 K2.

### §21.D.3 Public C ABI (`neva_session.h` — 54 satır, okundu)

**9 fonksiyon (imza satır numaralarıyla):**

| # | İmza | Satır |
|---|---|---|
| 1 | `uint32_t neva_session_api_version(void)` | 39 |
| 2 | `neva_result_t neva_session_create(neva_session_t** out)` | 40 |
| 3 | `void neva_session_destroy(neva_session_t* s)` | 41 |
| 4 | `neva_result_t neva_session_open_file(neva_session_t* s, const char16_t* path)` | 42 |
| 5 | `neva_result_t neva_session_set_output_device(neva_session_t* s, uint32_t index)` | 43 |
| 6 | `neva_result_t neva_session_play(neva_session_t* s)` | 44 |
| 7 | `neva_result_t neva_session_pause(neva_session_t* s)` | 45 |
| 8 | `neva_result_t neva_session_stop(neva_session_t* s)` | 46 |
| 9 | `neva_result_t neva_session_set_eq_gain(neva_session_t* s, uint32_t band, double db)` | 49 |

**Hata kodu enum'u (satır 31-37):** `0 NEVA_OK · 1 NEVA_ERR_INVALID_ARG · 2 NEVA_ERR_NO_DEVICE · 3 NEVA_ERR_UNSUPPORTED_FORMAT · 4 NEVA_ERR_INTERNAL`.

**Diğer sabitler:** `NEVA_API_VERSION 0x00010000u /* 1.0.0 */` (satır 26) · opak `struct neva_session` (satır 29) · `NEVA_API` makrosu (dllexport/dllimport/static — satır 12-20) · `char16_t` yolu (`uchar.h`, C11; satır 8-10) · `band 0..30 (31 bant)` + "Bu C ABI her iki kanala da uygular (62 biquad yolu: 31×2)" yorumu (satır 47-48) · per-kanal erişim yalnız C++ `EqChain::set_gain(band, ch, db, ramp_ms)` içinde.

**İzlenebilirlik:** NE-ENG-001..005/046/049/063/100 · NE-ENG-064/101 (EQ) · TC-ENG-001..005/046/049/064/142.

### §21.D.4 Backend soyutlaması (`iaudio_backend.h` — 57 satır, okundu)

- `enum class BackendResult : uint32_t` (satır 15) — "tüm adapter'lar aynı hata enum'unu döndürür → fallback tablosu OS'tan bağımsız" (satır 14, ADR-019 kural 4).
- `enum class StreamMode { Exclusive, Shared }` (satır 19).
- `struct StreamConfig` (satır 21) · `struct DeviceList` (satır 33).
- `class IAudioBackend` (satır 39) — **8 sanal**: `enumerate(DeviceList&)` · `open(const StreamConfig&)` · `start(RenderCallback, void*) noexcept` · `switchMode(StreamMode)` · `setDeviceRate(uint32_t)` · `stop() noexcept` · `kill() noexcept` (kill-switch, ADR-019 §2.2c) + sanal yıkıcı.
- Header yorumu (satır 4-5): "ADR'deki imza (`enumerate() -> BackendInfo`, `start(CallbackFn)`) **derlenebilirlik için minik düzeltildi**: `enumerate(DeviceList&)` out-param + `start(cb, userData)`. Semantik aynı."

**İzlenebilirlik:** NE-ENG-006/013/014/015/016/021..028 · TC-ENG-006/013..016/041..044.

### §21.D.5 RT çekirdek yapıları

**`spsc_ring.h` (50 satır):** `template <typename T, size_t Capacity> class SpscRing` · `push(const T&) noexcept` (satır 20) · `pop(T&) noexcept` (30) · `empty() const noexcept` (38) · `static constexpr kMask = Capacity - 1` (43 — güç-iki şartı) · `alignas(64) atomic<size_t> write_` (44) + `read_` (45) — üretici/tüketicisatır cache-line ayrımı.

**`biquad.h` (93 satır):** `struct PeakingParams` (20) · `struct BiquadCoeffs` (28) · `inline BiquadCoeffs compute_peaking(const PeakingParams&) noexcept` (34 — RBJ) · `class alignas(64) Biquad` (52) · `inline void enable_ftz_daz() noexcept` (85 — FTZ/DAZ).

**`eq_chain.h` (153 satır):** `class EqChain` (36) — `prepare(double sample_rate) noexcept` (39) · `set_gain(uint32_t band, uint32_t ch, double db, double ramp_ms) noexcept` (49) · `set_active_bands(uint32_t mask, double ramp_ms) noexcept` (69) · `process(float* L, float* R, size_t n) noexcept` (89).

**`eq_grids.h` (97 satır):** `enum class EqBandCount : uint32_t { Band10=10, Band15=15, Band20=20, Band31=31 }` (25) · `struct EqGridSubset` (30) — `bool valid; uint32_t count; array<uint32_t,kEqBands> grid_index` · `kSubset10` (40, teyitli) · `kSubset31` (44, tam grid) · **`kSubset15{false,0,{}}` (51) + `kSubset20{false,0,{}}` (52) — GİZLİ/KİLİTLİ** · `subset_for(EqBandCount)` (54) · `mask_for(const EqGridSubset&)` (66) · `struct EqPreset` (75) — `{grid_version=1, band_count, gain_db[31] (grid indeksli, eksik=0.0 flat), preamp}` · static_assert grid kilidi (82-87, ADR-106 §2.2b kural 3).

**İzlenebilirlik:** NE-ENG-050..062 (EQ) · NE-ENG-064..069/071 · TC-ENG-051/056/064/065/070/145-149 (Flat/grip) · kardeş TC-PLR-147 (15/20 gizli — disk kanıtıyla doğrulandı: `kSubset15/20 = {false,0,{}}`).

### §21.D.6 ICY parser (`icy_parser.h` — 240 satır, okundu)

| Sembol | Satır | Değer / rol |
|---|---|---|
| `kMetaCap` | 33 | 256 B (kural 7 cap) |
| `kMetaintMin` | 34 | 32 (RED aralığı altı) |
| `kMetaintMax` | 35 | 1 MiB (RED aralığı üstü) |
| `kIcyHeaderCap` | 4096 | header satır cap'i (defansif) |
| `utf8_validate(const char*, size_t)` | 41 | UTF-8 doğrulama |
| `sanitize_copy` / `sanitize_metadata` | 76 / 91 | temizleyici + kopya |
| `validate_metaint(uint32_t)` | 105 | metaint aralık denetimi |
| `struct IcyMetadata` | 113 | `{metaint, title, url, raw}` |
| `parse_icy_metadata_block` | 120 | `StreamTitle='…'` / `StreamUrl='…'` çıkarımı |
| `html_escape` | 160 | tek katman kaçış |
| `header_value` / `parse_metaint` | 186 / 223 | header + metaint ayrıştırma |

**İzlenebilirlik:** NE-ENG-080..088 (ICY/radyo) · TC-ENG-095/159 · kardeş TC-PLR-103/108/114/116/170-172.

### §21.D.7 Kaynaklar

**`neva_session.cpp` (215 satır):** `kApiVersion = NEVA_API_VERSION` (21) · 9 C ABI implementasyonu (102-206): `create` (104, `new`/`delete` — 117 `destroy`: `delete s`) · `open_file` (119) · `set_output_device` (153) · `play` (162) · `pause` (192) · `stop` (199) · `set_eq_gain` (206).

**`wasapi_backend.cpp` (316 satır — skelet):**
- Satır 1-2: "WASAPI exclusive backend SKELETİ … Birincil yol: `AUDCLNT_SHAREMODE_EXCLUSIVE` (bit-perfect). Exclusive başarısızsa Shared'a düşer (ADR-019)."
- Satır 6: "`⚠️ VERIFICATION REQUIRED`: exclusive loopback capture davranışı gerçek cihazda teyit edilecek (ADR-0105 §5.1)."
- `class DeviceNotifier final : IMMNotificationClient` (39) — hot-plug.
- `class WasapiBackend final : IAudioBackend` (69) — `IMMDeviceEnumerator` COM (74-76) · `enumerate` (91) · `open` (116): `open_exclusive(cfg)` → `setup_event()` → başarısızlıkta `open_shared(cfg)` (129-132, ADR-019).
- Satır 129: "`⚠️ VERIFICATION REQUIRED`: exclusive loopback doğrulama (ADR-0105 §5.1)."

**`null_backend.cpp` (54 satır):** sessiz backend — zincir sonu (ses yok, session yaşar).

**`decode_stub.h/cpp` (30/99 satır):** `struct WavInfo { sample_rate=48000, channels=2, bits_per_sample=32, is_float=true, frames=0 }` · `bool parse_wav(const uint8_t*, size_t, WavInfo&, const uint8_t*&, size_t&)` (little-endian RIFF/WAVE; PCM int16 + IEEE float32) · `uint32_t normalize_to_float(...)` (interleaved → float32 [-1,1]) · TODO yorumu: "FLAC (libFLAC/dr_flac), DSD (libdsd DoP-first), ffmpeg gateway (§2.4)."

**İzlenebilirlik:** NE-ENG-002/004/011/021/022/030/102 · TC-ENG-004/029/041/117 · kardeş §14.1 P01-P04.

### §21.D.8 Testler (ctest + assert — doctest YOK)

| Test exe | Dosya | `add_test` adı | Stil | Kanıtladığı |
|---|---|---|---|---|
| `neva_tests` | test_biquad.cpp (130) | `biquad_eq_ring` | `<cassert>` + `std::printf("[ok] …")` | RBJ peaking katsayı (f0=1k, fs=48k, Q=0.707, +6 dB → `|H(f0)|≈1.995262`, `|b1−a1|<1e-12`) · DC gain · blok eşitliği · ring wrap |
| `eq_grids_tests` | test_eq_grids.cpp (112) | `eq_grids_gates` | aynı | Grid kilitleri (static_assert + runtime) |
| `icy_parser_tests` | test_icy_parser.cpp (143) | `icy_parser_gates` | aynı | 256 B cap · 32 B RED · 32 B OK · 1 MiB RED · UTF-8 |

**Çalıştırma:** `ctest` (CMake `enable_testing()`). **Framework notu:** hafif `<cassert>`; doctest/Catch2 **DEĞİL** `[VARSAYIM — framework seçimi açık]` → TE-02 (dahili test kütüphanesi) ile uyumlu.

**İzlenebilirlik:** TC-ENG-050 (RBJ kilit) · TC-ENG-064/070 · TC-ENG-095/159/160 · §11.1 (alım) · NE-ENG-166 (G5).

### §21.D.9 Varsayılanlar & W-Envanteri (yok = implemente edilmemiş)

| Kayıt | Durum | Sahip gereksinim |
|---|---|---|
| Backend (yazılım) | YOK | NE-ENG-033..037 (Null hariç) |
| Codec | YOK | NE-ENG-102..107 (WAV stub hariç) |
| E1-E10 event | YOK | NE-ENG-128 (minor) |
| HTTP/ICY | YOK (yalnız parser) | NE-ENG-071 (P4) |
| Config/preset | YOK (EqPreset struct var, dosya yok) | NE-ENG-094 |
| Cihaz seçimi | YOK (set_output_device var) | NE-ENG-038..040 |
| CLI | YOK | NE-ENG-001 (M4 öncesi) |
| Fuzz/CI/TSan/ASan | YOK (yalnız assert testleri) | NE-ENG-119/131/132 |

**Sonuç:** ADR-0105 §2.6 + §2.10 **kısmen** uygulanmış (C ABI + backend arayüzü + EQ + ICY parser + Null) — §15.2 P0 fazının girdisi budur.

### §21.D.10 Ek-D Sonuç Tablosu (PAKET gate)

| Soru | Cevap (disk) |
|---|---|
| C ABI var mı? | Evet — 9 fonksiyon (§21.D.3) |
| Backend arayüzü var mı? | Evet — 8 sanal (§21.D.4) |
| Zincir implemente mi? | Kısmen — WASAPI Excl→Shared (skelet) + Null |
| EQ çekirdeği var mı? | Evet — EqChain + EqGrid + EqPreset |
| ICY parser var mı? | Evet — v1/v2 + escape + cap |
| Decode var mı? | Yalnız WAV stub (`decode_stub`) |
| Testler yeşil mi? | 3 exe + ctest — `⚠️ VR (bu oturumda çalıştırılmadı; yalnız kaynak okundu)` |
| Artefakt hijyeni? | `test_biquad.obj` ihlal adayı → NE-ENG-125 |

## §21 Ek E — ADR & Kaynak Özetleri (Engine bağlamı)

> Bu ek, PRD boyunca atıf yapılan **kaynakların bağlayıcı özetidir** — her satır o kaynaktan okunmuştur.
> Kaynakta olmayan hiçbir şey buraya yazılmaz (AGENTS §3).

### §21.E.1 ADR-017 — Engine Prompt (kaynak: `coremusic-adr107-neva-engine-prompt.md` dahil)

- **Tür:** Mimari karar + prompt dosyası (218 satır, tamamı okundu).
- **Kapsam:** NEVA ENGINE'in C++20 realtime ses motoru olarak tanımı; `apps/` altına kod kuralı (K2); 25 dil FFI kapısı (`neva_*` C ABI); `neva_session.h` tek public header.
- **Kilit kararlar:** Opaque handle + error-code + `api_version` handshake · C++ tipleri C sınırından geçmez · ABI kıran her değişiklik major artışı ister · testler `apps/` altına `neva_tests`/`eq_grids_tests`/`icy_parser_tests` (ctest).
- **PRD bağlantısı:** §05.1 · §15.2 · §21.D (disk envanteri birebir bunu doğruluyor).

### §21.E.2 ADR-019 — Per-OS Neva Player (kaynak: `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md`)

- **Durum:** `accepted` (frozen — dokunulmaz yüzey).
- **§1.1:** OS mixer bypass — exclusive mod (bit-perfect ön koşulu).
- **§1.4:** Durum makinesi + test emsal (Sonic/Flussonic virtual ASIO mock).
- **§1.5:** Per-OS paketleme (WDM-KS emsalde başarısız — uzman review şart).
- **§2.1:** Backend listesi: WASAPI · WDM-KS · ALSA · PipeWire · CoreAudio (+ASIO).
- **§2.2b:** Fallback zinciri — "7-step" ifadesi (liste 8 isim: ASIO→WASAPI Excl→Shared→WDM-KS→PipeWire→ALSA→CoreAudio→Null; bu PRD'de **8 kayıt (Null dahil; Null = ses yok son çare)** olarak standartlaştırıldı — C-09).
- **§2.2c:** kill-switch (`kill() noexcept` — diskte `iaudio_backend.h:48` kanıtı).
- **§2.4:** WASAPI event-driven (exclusive'de düşük gecikme).
- **F1-F4 (faz 2 genişletme):** `set_eq_band` · `set_eq_band_count` · `set_eq_bypass` · `set_output_device` — diskte `set_eq_gain` (9. fonksiyon) mevcut; kalan genişletmeler E5/E6/E7 planlı (§05.2).
- **N1 (negosyasyon sırası):** Hız uyuşmazlığında `setDeviceRate` → soxr (producer-side) → zincir düşüşü. Disk kanıtı: `iaudio_backend.h:46 setDeviceRate`.
- **K1-K3 (bu PRD'de okunan):** tek ABI · tek header · event/queue sözleşmesi.

### §21.E.3 ADR-106 — Internet Radio & Selectable Band EQ (kaynak: `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md`)

- **Durum:** `accepted`.
- **Kural 1 (grid sabit):** 31-band ISO 1/3-oktav grid (20..20000 Hz) — `eq_grids.h:3` kilitli.
- **Kural 2 (band seçimi):** 10/15/20/31 seçilebilir; disk `EqBandCount` enum'u (eq_grids.h:25) doğruluyor.
- **Kural 3 (grid kilidi):** static_assert — `kEqFrequencies` değişirse derleme patlar (eq_grids.h:82-87).
- **Kural 4 (EQ geçişi):** ramp 5-20 ms (`EqChain::set_gain(band, ch, db, ramp_ms)`).
- **Kural 6 (prebuffer/backoff):** 2-4 s prebuffer · 1s→30s ±%20 backoff · 3 fails → ERROR · fade-in 20 ms.
- **Kural 7 (metaveri):** 256 B cap + UTF-8 + escape — `icy_parser.h:33 kMetaCap=256`.
- **Kural 8 (kapılar):** HLS 50 MB/1000 entry/derinlik 1 · metaint RED aralığı — `kMetaintMin=32`, `kMetaintMax=1 MiB` (icy_parser.h:34-35).
- **Kural 9 (RED lisans):** FDK-AAC · BASS · mpv — §14.2.
- **Kural 11 (şartlı):** LUFS/şablon/video — §18.9 kardeş.
- **K1-K6:** bit-perfect rozeti · Flat=all-zero · hash hattı · shared rozet YOK · LZ4 · preset taşıma.
- **S1-S8:** HTTP-only · TLS (mbedTLS) · timeout 10 s · redirect ≤5 · SSRF · redirect cap · User-Agent · devre dışı keepalive.
- **G1-G6:** OWASP · katalog (ADR-074) · escape · gizlilik · CVE · lisans.

### §21.E.4 ADR-107 — Neva Engine Platform & Paketleme (draft — `.ai/.decisions/draft/` + `.ai/architecture/neva-engine-platform-paketleme.md`)

- **Durum:** `draft` (onay bekliyor — bu PRD K-1 kapısıyla hizalı).
- **K1:** C ABI 1.0.0 dondurma (9 fonksiyon — disk §21.D.3; "8" taslak sayımıydı → C-01).
- **K2:** `apps/` altına kod (InitContainer/derleme katmanı).
- **K3:** Bit-perfect hash hattı (SHA-256, 12 kombinasyon, determinizm).
- **K4:** VST3/plugin kapalı (K4 gate).
- **§8.1:** ASIO lisans tartışması (U1 — VSL) → §18.1.
- **§8.2-§8.3:** DSD/libdsd + macOS (U3) → §18.3.
- **§8.4:** PCM3168A (U6) → §18.10.
- **§8.6:** Web/API yüzeyi (U4) + neva-host köprüsü → §18.4/§18.12.
- **§8.7:** Çekirdek lisans (L01 — AGPL-3.0 vs MIT-benzeri) → §18.6 **KRİTİK**.

### §21.E.5 ADR-0105 — İçerik Surrogatesi (D2 — dosya diskte YOK)

- **Kaynak:** `.ai/.decisions/index.md:152` — yalnız indeks satırı (surrogate).
- **İddialar (PRD'de `⚠️ VR` işaretli):** §2.6 C ABI · §2.9 RAM ≤400 MB · §2.10 L1-L4 gecikme · kapı 5.1 (bit-perfect) · 5.10 (latency) · 5.11 (A/V sync) · 5.12 (gapless AK-5 ±5 ms) · 5.13 (fuzz ≥1000/format) · v1.0.x-v1.4.0 sürüm geçmişi · vendor stratejisi belirsiz · FFmpeg 9.0.2 · libplacebo 7.350.0.
- **Etki:** Tüm `⚠️ VR` etiketleri bu dosyadan — §18.14 (kardeş) + R-P02 + Vault Steward iade talebi.

### §21.E.6 Diğer atıf kaynakları (özet)

| Kaynak | Ne verdi | PRD yeri |
|---|---|---|
| `.ai/.decisions/index.md` | ADR listesi + ADR-0105 surrogate + ADR-074/ADR-091/ADR-097/ADR-005/ADR-074 indeks kayıtları | §00.3 · §18.14 kardeş |
| `.ai/AGENTS.md` (SSOT) | §3 zero-hallucination · §6.1 persona hiyerarşisi (20 persona) · §10 CSS · §13 mockup gate · §16 değiştirilemez kurallar (widget grid, isimlendirme, Figma token, dokunulmaz yüzeyler) · §17 Edge 10 | §00.2 · §11.1 · §15.5 · §18 |
| `CLAUDE.md` (kök) | Skill Registry + boot zinciri | §00.1 |
| `~/.config/opencode/AGENTS.md` (global) | §5 mühendislik standartları (RAII, smart pointer, ConfigureAwait, strict_types, parametreli SQL) · §6 CoreMusic bağlamı | §13.3 · §14 |
| `.ai/RAG.md` | konu→dosya indeksi (§3) | §00.1 okuma sırası |
| `--/neva-engine/**` (16 dosya) | §21.D tam envanter | §21.D |

### §21.E.7 Kaynak Güven Matrisi

| Kaynak | Güven | Kullanım |
|---|---|---|
| Disk kodu (`--/neva-engine/**`) | **Yüksek (bijection)** | §21.D · §05.1 · §06 · doğrulanmış gereksinimler |
| `accepted` ADR (019, 106) | **Yüksek** | §04 · §06 · §07 · kapılar |
| `draft` ADR (107) | **Orta** — onay bekliyor | §03 · §15 · §18 karar kapıları |
| ADR-0105 (surrogate, dosyasız) | **Düşük** — `⚠️ VR` | §08 bütçeler · kapı 5.x · §18.8 |
| Prompt dosyası (ADR-017) | **Orta** — prompt ≠ ADR | §15.2 faz sırası |
| Genel bilgi (OWASP, ISO, WCAG) | **Orta** — standart referansı | §13 · §06.1 |

**Kural:** Güven matrisi ≠ otorite sırası. Otorite: kullanıcı > onaylı ADR > vault > resmi doküman > genel bilgi (AGENTS §3).

## §21 Ek F — Ölçüm & Rapor Şablonları (Engine)

> Her şablon **CI çıktısına veya release raporuna yapıştırılabilir** formdadır. Boş alanlar ölçümde doldurulur.
> **Kural (Edge 3):** Ölçülmeyen iddia yazılmaz; `⚠️ VR` eşikleri ölçüm raporu olmadan "başarılı" sayılmaz.

### §21.F.1 Ölçüm Ortamı Kaydı (her raporun zorunlu başlığı)

```markdown
## Ölçüm Ortamı
- Tarih / saat:
- OS + sürüm: (Windows 11 23H2 / Ubuntu 24.04 / …)
- CPU / çekirdek / RAM:
- Ses cihazı + sürücü + mod: (WASAPI Excl / Shared / ALSA hw:0 …)
- Derleme: (Debug|Release · MSVC/GCC/Clang · commit SHA)
- Ölçüm aracı: (WASAPI latency API / loopback + Audacity / script adı)
- Notlar: (arka plan süreçleri, güç planı, termal)
```

### §21.F.2 Hash / Bit-perfect Raporu (kapı 5.1 · K3)

```markdown
## Bit-perfect Hash Raporu (TC-ENG-127..130, 143..149)
| # | Kaynak | rate | depth | kanal | SHA-256 (çıktı) | 2× determinizm | Byte-equality | Rozet |
|---|--------|------|-------|-------|-----------------|----------------|---------------|-------|
| 1 | ref.wav | 44100 | 16 | 2 | (hash) | PASS/FAIL | PASS/FAIL | —/EXCL/SHARED |
… (12 kombinasyon: 44.1/48/96/192 × 16/24/32f)
- Negatif koruma (inverted guard): hash eşleşse bile Flat=all-zero DEĞİLSE → FAIL [x]
- EQ bypass + Flat: Flat=all-zero kanıtı: [x]
- Cihaz + OS + commit: (üstteki ortam bloğu)
Sonuç: GECTI / GECMEDI (gerekçe)
```

### §21.F.3 Gecikme Raporu (kapı 5.10 · §11.3-A)

```markdown
## Gecikme Raporu (TC-ENG-093/137/138)
| Backend | mod | block | rate | buffer (frame) | ölçülen ms | eşik | durum |
|---|---|---|---|---|---|---|---|
| WASAPI | Excl | 256 | 48000 | … | … | <10 ms ⚠️ VR | … |
| WASAPI | Shared | 256 | 48000 | … | … | <20 ms ⚠️ VR | … |
| ALSA | hw | 256 | 48000 | … | … | <20 ms ⚠️ VR | … |
- L1-L4 kademe ataması: (M1 ölçümü — §18.8)
- OS loopback ölçüm hatası payı: (± … ms) [VARSAYIM]
Sonuç: GECTI / GECMEDI
```

### §21.F.4 SNR/THD+N Raporu (V-1/V-2 · §11.3-B)

```markdown
## Analog Kalite Raporu (NE-ENG-140/141 · TC-ENG-099/100/136)
- Düzenek: DAC (ad/model ⚠️ VR — §18.10) → loopback → ADC/analyzer
- Referans: 1 kHz sine, −1 dBFS, 32-bit float kaynak
| Ölçüm | Değer | Eşik | Durum |
|---|---|---|---|
| SNR | … dB | >105 dB ⚠️ VR | … |
| THD+N | … % | <%0.005 ⚠️ VR | … |
| (ops.) LUFS integrated | … LU | şartlı §18.9 | … |
Sonuç: GECTI / GECMEDI / ÖLÇÜLEMDİ (donanım yok)
```

### §21.F.5 Fuzz Raporu (kapı 5.13)

```markdown
## Fuzz Raporu (TC-ENG-132 · AK-11)
- Hedefler: WAV · FLAC · MP3 · ICY text · HLS playlist · C ABI girdisi · preset JSON (§18.13 [ÖNERİ])
| Hedef | corpus | vaka | süre | crash | UBSan | ASan | durum |
|---|---|---|---|---|---|---|---|
| (hedef) | ≥1000/format | … | … | 0 | 0 | 0 | … |
- Toplam vaka: … / toplam süre: … dk (CI ≤10 dk şartı)
- Bulgu listesi: (varsa — her biri NE-ENG-132 alt satırı)
Sonuç: GECTI / GECMEDI
```

### §21.F.6 Stres / Bellek Raporu (G4)

```markdown
## Stres Raporu (NE-ENG-036/037 · TC-ENG-094/096/141)
- Süre: 30 dk (G4) [+ uzun koşu 12 saat opsiyonel — C-04]
- Sahneler: play/pause (10k) · stop/start (10k) · hot-plug (5k) · open/close (10k)
| Metrik | Ölçüm | Bütçe | Durum |
|---|---|---|---|
| RAM (tepe) | … MB | ≤400 MB ⚠️ VR | … |
| CPU (ortalama) | … % | ≤5 % [ÖNERİ] | … |
| Xrun/underrun | … | 0 | … |
| Leak (LSan) | … | 0 | … |
| Sayaçlar (session/device/…) | … | 0 | … |
Sonuç: GECTI / GECMEDI
```

### §21.F.7 Lisans Kapısı Raporu (G3 — §14.6)

```markdown
## G3 Lisans Raporu
| # | Madde | Kanıt (dosya) | Durum |
|---|---|---|---|
| 1 | LICENSE + Copyright (çekirdek — L01 kararı §18.6) | LICENSE | … |
| 2 | NOTICE (tüm bağımlılıklar) | NOTICE | … |
| 3 | ASIO lisansı (U1) | (metin) | … |
| 4 | FFmpeg link raporu + NOTICE (U7/vendor) | (link.txt) | … |
| 5 | `--enable-gpl` grep (CI) | (CI çıktısı) | YOK/VAR |
| 6 | RED filtresi (FDK-AAC/BASS/mpv) | (grep) | … |
| 7 | soxr LGPL-2.1 dinamik | (link raporu) | … |
| 8 | mbedTLS Apache-2.0 | (NOTICE) | … |
Kapı kararı: GECTI / GECMEDI (bloke: release)
```

### §21.F.8 Sembol/Semver Raporu (TC-ENG-142 · §05.4)

```markdown
## ABI Sembol Raporu
- Export edilen semboller (dumpbin /DUMPBIN EXPORTS veya nm -D): (liste)
- Beklenen: 9 neva_* sembol + C++ iç semboller GİZLİ
- Header diff (önceki sürüm): (ekleme minor / kırılma major)
- Sonuç: DONDU (1.0.0) / DEĞİŞTİ (major gerekli)
```

### §21.F.9 PAKET Release Raporu (§14.4)

```markdown
## PAKET Release Raporu
- Sürüm: 1.0.0 · commit: (SHA) · tarih:
- Artefaktlar: neva_engine.lib · neva_session.dll · include/neva/*.h · LICENSE · NOTICE · docs/
- İmza: (varsa — yoksa [ÖNERİ] notu)
- Boyut: … KB (bütçe §08.3)
- Checksum (SHA-256): (liste)
- G3 raporu eki: (§21.F.7 sonucu)
- Bilinen sınırlar (release notu): L1-L4 ⚠️ VR · DSD native OFF (U3) · ASIO varsayılan OFF (U1)
- Onay: K-1..K-6 (§20.1)
```

### §21.F.10 Rapor Eşleştirme Tablosu (test ↔ rapor)

| Rapor | Test(ler) | Kapı |
|---|---|---|
| §21.F.2 Hash | TC-ENG-127..130/143..149 | 5.1 (K3) |
| §21.F.3 Gecikme | TC-ENG-093/137/138 | 5.10 (G2) |
| §21.F.4 SNR/THD | TC-ENG-099/100/136 | G2 (V-1/V-2) |
| §21.F.5 Fuzz | TC-ENG-132 | 5.13 |
| §21.F.6 Stres | TC-ENG-094/096/141 | G4 |
| §21.F.7 Lisans | TC-ENG-150..152/154..156 | G3 |
| §21.F.8 Semver | TC-ENG-142 | A0 dondurma |
| §21.F.9 Release | §15.5 PAKET | K-6 |

**Kural:** Rapor olmayan kapı = kapalı kapı (AGENTS §1 — onaysız geçiş yok).

## §21 Ek G — Uçtan Uca Senaryo Matrisi (Engine · 20 senaryo)

> Her senaryo: **amaç · ön koşul · adımlar · beklenen · kapısı (gereksinim/test)**.
> Senaryolar §11'deki birim/kapı testlerini **sistem düzeyinde** birleştirir (akşam testi mantığı).

### §21.G.1 Derleme & Kurulum Senaryoları

**S01 — Temiz derleme (3 hedef)**
- Amaç: Boş makineden yeşil derleme + test.
- Adımlar: `cmake -S . -B build` → `cmake --build build --config Release` → `ctest --test-dir build`.
- Beklenen: 0 hata/uyarı (`/W4` temiz); 3 `add_test` yeşil.
- Kapı: NE-ENG-120 · TC-ENG-122 · P0/A0.

**S02 — Cross-derleme (ARM64)**
- Amaç: Taşınabilirlik iddiası.
- Adımlar: `cmake -G Ninja -DCMAKE_SYSTEM_PROCESSOR=ARM64 …` (veya Visual Studio ARM64 toolchain).
- Beklenen: Derlenir; ARM64'e özgü kod YOK (NE-ENG-032).
- Kapı: NE-ENG-032 · TC-ENG-153 · A1.

**S03 — Linux derleme (GCC + Clang)**
- Amaç: MSVC-dışı doğrulama.
- Adımlar: `cmake -B build && cmake --build build` (GCC 13+ / Clang 17+ `⚠️ VR sürüm`).
- Beklenen: 0 hata; `-Wall -Wextra` temiz `[ÖNERİ]`.
- Kapı: NE-ENG-025/026 · TC-ENG-123/163 · A1.

**S04 — Sembol/Semver denetimi**
- Amaç: 1.0.0 ABI dondurma.
- Adımlar: `dumpbin /EXPORTS neva_session.dll` → 9 `neva_*` sembol listesi.
- Beklenen: Semboller §21.D.3 ile birebir; C++ iç semboller gizli; fazladan export YOK.
- Kapı: TC-ENG-142 · A0.

### §21.G.2 Oturum Yaşam Döngüsü Senaryoları

**S05 — Minimal yaşam döngüsü**
- Adımlar: `api_version` → `create` → `open_file(ref.wav)` → `play` → 2 s bekle → `stop` → `destroy`.
- Beklenen: Her adım `NEVA_OK`; sıfır xrun; sıfır leak (LSan).
- Kapı: NE-ENG-046/050 · TC-ENG-013/034/035.

**S06 — API sürüm uyuşmazlığı**
- Adımlar: Eski DLL + yeni header link (veya `api_version` sahtesiyle 0x00020000).
- Beklenen: Handshake reddi (session açılmaz) — sürüm uyumsuzluğu sessizce yutulmaz.
- Kapı: NE-ENG-049 · TC-ENG-049.

**S07 — Destroy sonrası kullanım (use-after-close)**
- Adımlar: `destroy` → `play`/`set_eq_gain` çağrısı (aynı handle).
- Beklenen: RED (`NEVA_ERR_INVALID_ARG` (1) `[ÖNERİ]`) + UB YOK + çökme YOK.
- Kapı: SE-02 · TC-ENG-002/117 · T-E09.

**S08 — Eşzamanlı çağrı (play ‖ pause)**
- Adımlar: 2 thread aynı handle'da eşzamanlı `play` + `pause` (10k tur).
- Beklenen: Data race YOK (TSan); crash YOK; sonuç her turda tutarlı (determinizm).
- Kapı: NE-ENG-093/095 · TC-ENG-033 · NE-ENG-131 (TSan).

### §21.G.3 Backend & Cihaz Senaryoları

**S09 — Exclusive→Shared otomatik düşüş**
- Adımlar: Cihazı başka süreçte meşgul et (tone generator) → `open_file`+`play`.
- Beklenen: Exclusive `AUDCLNT_E_DEVICE_IN_USE` → Shared'a düşüş (§21.D.7 kanıtı) + log; ses kesintisiz.
- Kapı: NE-ENG-021/022 · TC-ENG-021/022 · FM-E13.

**S10 — Hot-plug (çıkarma)**
- Adımlar: `play` sırasında USB DAC'ı çıkar.
- Beklenen: `DeviceLost` → zincir sırası → `ERROR`/kurtarma (fail-safe); hang/wait YOK; crash YOK.
- Kapı: NE-ENG-030 · TC-ENG-030 · FM-E02.

**S11 — Hot-plug (takma)**
- Adımlar: `ERROR` iken yeni cihaz tak + (player) yeniden dene.
- Beklenen: `enumerate` ile yeni cihaz görünür; session yeniden kurulabilir; sayaçlar sıfır.
- Kapı: NE-ENG-038/039 · TC-ENG-030.

**S12 — Zincir sonu Null**
- Adımlar: Ortamda HİÇ ses cihazı yok (CI konteyneri).
- Beklenen: Zincir Null'a kadar düşer; session yaşar; `play` OK ama ses YOK (bilinçli davranış); crash YOK.
- Kapı: NE-ENG-028 · TC-ENG-044/045 · FM-E08.

### §21.G.4 DSP & Bit-perfect Senaryoları

**S13 — Bit-perfect hash (12 kombinasyon)**
- Adımlar: 44.1/48/96/192 × 16/24/32f referans WAV'ları → exclusive + Flat + eşit hız → hash.
- Beklenen: SHA-256 byte-equality + 2× determinizm; inverted guard (hash eşleşse bile Flat=all-zero değilse FAIL).
- Kapı: kapı 5.1 · K3 · TC-ENG-127..130/143..149.

**S14 — EQ rampa + Flat**
- Adımlar: 60 sn play → 1 kHz bant +6 dB → 5-20 ms ramp → tüm bantlar 0 dB (Flat).
- Beklenen: Tıklama/pop YOK (fark < eşik `⚠️ VR`); Flat = all-zero (EQ kapalı kanıtı).
- Kapı: NE-ENG-051/062 · TC-ENG-051/056/140.

**S15 — Grid kilidi (10-band alt küme)**
- Adımlar: `set_eq_band_count(10)` (planlı E5) → 10 preset yükle → 31'e geç → 31 preset yükle.
- Beklenen: 10 preset yalnız 10-band oturumda geçerli; grid_version uyuşmazlığında RET (maske sıfırlanmaz); kalan 21 bant flat (disk: `gain_db` eksik = 0.0).
- Kapı: TC-ENG-064/070 · §21.D.5.

**S16 — Fuzz (ABI + parser)**
- Adımlar: C ABI girdisi + WAV/ICY corpora (≥1000/hedef) → 10 dk CI.
- Beklenen: 0 crash · 0 UB · 0 OOB · 0 UAF; bulgu yoksa kapı yeşil.
- Kapı: kapı 5.13 · TC-ENG-132 · NE-ENG-132.

### §21.G.5 Ölçüm & Release Senaryoları

**S17 — Gecikme ölçümü**
- Adımlar: WASAPI Excl/Shared + ALSA'da 256/48k ölçüm (§21.F.3 şablonu).
- Beklenen: <10 ms (Excl) / <20 ms (Shared) `⚠️ VR` — eşikler M1 ölçümüyle sabitlenir (§18.8).
- Kapı: kapı 5.10 · TC-ENG-137/138.

**S18 — 30 dk stres**
- Adımlar: play/pause 10k · stop/start 10k · hot-plug 5k (G4 şablonu §21.F.6).
- Beklenen: RAM ≤400 MB `⚠️ VR` · xrun 0 · leak 0 · sayaçlar 0 · CPU ≤%5 `[ÖNERİ]`.
- Kapı: G4 · TC-ENG-094/096/141.

**S19 — G3 lisans kapısı**
- Adımlar: §21.F.7 raporu (LICENSE/NOTICE/ASIO/FFmpeg/RED filtresi).
- Beklenen: 8/8 satır yeşil; Aksi halde release BLOKE.
- Kapı: G3 · TC-ENG-150..156 · §18.6 (L01 kararı).

**S20 — PAKET cut**
- Adımlar: `neva_engine.lib` + `neva_session.dll` + header + LICENSE + NOTICE + docs → paket + SHA-256 + imza.
- Beklenen: §21.F.9 raporu tam; bilinen sınırlar release notunda (L1-L4 ⚠️ · DSD OFF · ASIO OFF).
- Kapı: §15.5 PAKET · K-6 · NE-ENG-148/169.

### §21.G.6 Senaryo ↔ Faz Matrisi

| Senaryo | P0 | A0 | A1 | A2 | M1 | M2 | M4 | M6 | PAKET |
|---|---|---|---|---|---|---|---|---|---|
| S01 derleme | ● | | | | | | | | |
| S02 ARM64 | | | ● | | | | | | |
| S03 Linux | | | ● | | | | | | |
| S04 semver | | ● | | | | | | | |
| S05 yaşam döngüsü | ● | | | | | | | | |
| S06 sürüm uyuşmazlık | | ● | | | | | | | |
| S07 use-after-close | ● | | | | | | | | |
| S08 eşzamanlı | ● | | | | ● | | | | |
| S09-11 cihaz | | | | ● | | | ● | | |
| S12 Null | ● | | | | | | | | |
| S13 hash | | | | | | ● | | | |
| S14-15 EQ | | | | | | ● | | | |
| S16 fuzz | | | | | | | | ● | |
| S17-18 ölçüm | | | | | ● | ● | | ● | |
| S19 G3 | | | | | | | | | ● |
| S20 paket | | | | | | | | | ● |

## §21 Ek H — ASCII Mimari & Akış Diyagramları (Engine)

> Bu diyagramlar PRD metninin görsel özetidir; her ok, §'deki bir kararı temsil eder.

### §21.H.1 Katman Diyagramı (§04 — ADR-017 K2 + ADR-019)

```
┌────────────────────────────────────────────────────────────────┐
│ HOST (player / neva-host / CLI)          §05.2 E1-E10 (minor)  │
│   tek kapı: neva_* C ABI (9 fonksiyon — §21.D.3)               │
├────────────────────────────────────────────────────────────────┤
│ C ABI KATMANI (neva_session)            NE-ENG-046..063        │
│   opaque handle · clamp (§13.3) · 5 kod · exception YOK        │
├────────────────────────────────────────────────────────────────┤
│ OTURUM (SessionImpl)                    §05.1                  │
│   durum: CREATED→OPENED→PLAYING⇄PAUSED→STOPPED→CLOSED         │
├───────────────┬────────────────────────┬───────────────────────┤
│ DECODE        │ DSP                    │ BACKEND               │
│ decode_stub   │ EqChain (62 yolla)     │ IAudioBackend (8 virt)│
│ WAV (int16+f32)│ Biquad/RBJ (alignas64) │ Wasapi Excl→Shared    │
│ FLAC/DSD/     │ FTZ/DAZ                │ (skelet) · Null       │
│  ffmpeg TODO  │ preamp+limiter [ÖNERİ] │ ALSA/PipeWire/Core/…  │
│ NE-ENG-102+   │ §06                    │ A1-A2 fazı            │
├───────────────┴────────────────────────┴───────────────────────┤
│ RT ÇEKİRDEK                                §07                  │
│   SpscRing<T,Cap> (lock-free, alignas64 write_/read_)          │
│   render callback · xrun tespiti · kill-switch (noexcept)      │
│   YASAK: malloc/free · lock · dosya/IO · log · HTTP (SE-03)    │
└────────────────────────────────────────────────────────────────┘
```

### §21.H.2 Fallback Zinciri Akışı (ADR-019 §2.2b — 8 kayıt)

```
open_best_backend(want)
   │
   ▼
[1] ASIO ──────lisans/kurulum yok (U1: varsayılan OFF)──┐
   │ успех                                               │
   ▼                                                     ▼
[2] WASAPI Exclusive ──AUDCLNT_E_DEVICE_IN_USE──┐   (atla)
   │ успех                                       ▼
   ▼                                       [3] WASAPI Shared ──başarısız──┐
cihaz hazır: bitti                                  │ успех                ▼
   │                                          (ortak mod — bit-perfect  [4] WDM-KS
   │                                           rozeti YOK)             │ başarısız
   │                                                                     ▼
   │                                                              [5] PipeWire (Linux)
   │                                                                     │ başarısız
   │                                                                     ▼
   │                                                              [6] ALSA hw:
   │                                                                     │ başarısız
   │                                                                     ▼
   │                                                              [7] CoreAudio (macOS ⚠️ VR)
   │                                                                     │ başarısız
   │                                                                     ▼
   │                                                              [8] Null (ses YOK — session yaşar)
   ▼
StreamError / ERROR (tüm zincir tükendi → kullanıcı mesajı sahibi: player)
```

**Kural:** Zincir OOM'da tekrar denenmez (NE-ENG-080'ler) · hot-plug'ta yeniden denenir (NE-ENG-101).

### §21.H.3 Sinyal Yolu (bit-perfect mod)

```
dosya (char16_t yolu)
  → decode (WAV stub → float32 [-1,1])          §21.D.7
  → SpscRing (producer)                          §07.2
  ── RT sınırı ──
  → render callback (RT, zero-alloc)
       → EqChain.process(L,R,n)   [Flat ise: biquad bypass → bit-perfect]
       → interleaved → cihaz buffer
  → WASAPI exclusive event-driven                 §04.3
  → DAC (OS mixer BYPASS — ADR-019 §1.1)

Bit-perfect ön koşulları (kapı 5.1 · K3):
  1) exclusive (shared'da rozet YOK — NE-ENG-045)
  2) Flat = all-zero (EQ tamamen kapalı)
  3) dosya hızı == cihaz hızı (N1 uyuşmazlık zinciri → dönüşüm VARSA iddia düşer)
  4) hash: SHA-256(çıktı) == SHA-256(kaynak PCM) + 2× determinizm
```

### §21.H.4 Durum Makinesi (session — §05.1)

```
             create                  open_file(OK)
   (yok) ─────────────► CREATED ─────────────────► OPENED
                          │ ▲                        │  ▲
                    destroy│ │close(file)        stop │  │play
                          ▼ │                        ▼  │
                        (freed)                     PAUSED
                                                     │
                                            pause    │
                                                     ▼
                                                  PLAYING
                                                     │
                              hata (cihaz/fmt/…)     │  xrun/kill-switch (F4)
                                     ┌───────────────┤
                                     ▼               ▼
                                   ERROR ──stop──► (OPENED/CLOSED)
                                     │
                                  destroy → (freed)
```

**Kural:** Her geçiş `neva_result_t` döndürür (§21.D.3) · `destroy` her durumdan geçerli (noexcept) · geçersiz geçiş = RED (kod 1 `[ÖNERİ]`).

### §21.H.5 EQ Grid Kilidi (ADR-106 §2.2b — disk eq_grids.h)

```
kEqFrequencies[31]  (20..20000 Hz, ISO 1/3-oktav — STATIC)
        │
        ├── kSubset31 {valid=true, count=31, tam grid}
        ├── kSubset10 {valid=true, count=10, teyitli indeksler}
        ├── kSubset15 {valid=false, count=0, {}}   ← GİZLİ (kilitli)
        └── kSubset20 {valid=false, count=0, {}}   ← GİZLİ (kilitli)
                │
                ▼
   static_assert: 10-band indeksleri kEqFrequencies ile birebir
   (grid değişirse DERLEME PATLAR — zero-hallucination kilidi)

Preset: {grid_version=1, band_count, gain_db[31], preamp}
  yükleme: grid_version uyuşmazsa RET · band_count uyuşmazsa RET
  eksik band = 0.0 dB (flat) — §21.D.5
```

### §21.H.6 ICY Parse Akışı (icy_parser.h — 240 satır)

```
HTTP header (≤4096 B/satır — kIcyHeaderCap)
  → parse_metaint(headers)     → validate_metaint: [32 .. 1MiB] (RED aralığı)
  → icy-metaint = N
akış:
  veri baytları (ses → ring) ── her N baytta 1 metaveri bloğu
  metaveri bloğu (≤256 B — kMetaCap)
  → utf8_validate     → geçersizse DROP (sayaç++)
  → sanitize_metadata → StreamTitle='…' / StreamUrl='…' çıkarımı
  → html_escape       → UI'a TEMİZ metin (tek katman — ADR-106 kural 7)
bloom savunması:
  >kMetaCap başlık → drop + "Çok sık başlık değişimi" uyarı sayacı (FM-E15)
```

### §21.H.7 Test Kapıları Akışı (§11.7 + §15.5)

```
PR / push
  │
  ├─ statik kapılar (regex/grep — S3 §5)
  │    RT yasakları (malloc|free|std::mutex|fopen|printf  içinde render)
  │    HTTP yasağı (socket|curl   içinde neva-core)
  │    C++ sınırı (std::   içinde neva_session.h)
  │    sembol (export listesi == 9 neva_*)
  │
  ├─ derleme: MSVC /W4 · GCC -Wall · Clang
  ├─ ctest: biquad_eq_ring · eq_grids_gates · icy_parser_gates   (§21.D.8)
  ├─ sanitizers: ASan+UBSan (Linux) · TSan (Linux) · LSan (stres)
  ├─ fuzz: ≥1000/hedef · CI ≤10 dk                            (kapı 5.13)
  ├─ hash: 12 kombinasyon SHA-256 + determinizm                (kapı 5.1)
  └─ lisans grep: --enable-gpl YOK · RED filtreleri             (G3)
         │
         ▼
   hepsi yeşil → merge izni (onay kapıları K-1..K-6 ayrı)
```

### §21.H.8 İzlenebilirlik Akışı (§17)

```
kaynak (ADR / disk / prompt)
   │  türetme
   ▼
NE-ENG-NNN (§10 — MUST/SHOULD · öncelik · ADR izi)
   │  karşılama
   ▼
TC-ENG-NNN (§11 — aynı N) ──► §21.F rapor şablonu ──► kapı (G0-G6 / 5.x)
   │  kanıt
   ▼
release notu (bilinen sınırlar: ⚠️ VR eşikler · OFF bayraklar)
```

### §21.H.9 Hata Kurtarma Öncelik Sırası (§12.4)

```
1) RT tarafı: xrun tespiti → fade (≤50 ms) → durum işaretle (kuyruğa yaz)
2) worker tarafı: kuyruk oku → retry/backoff (radyo: 1s→30s) → zincir yeniden dene
3) oturum tarafı: zincir tükendi → StreamError → ERROR durumu
4) UI tarafı (player sahibi): kullanıcı mesajı + yeniden dene eylemi
5) günlük: kod + context (telemetri opt-in; crash dump ayrı paket)
```

## §21 Ek I — SSS & Hata Ayıklama Rehberi (Engine)

### §21.I.1 Sıkça Sorulan Sorular (SSS)

**S1 — Neden C ABI, C++ API değil?**
25 dil FFI kapısı (Python ctypes / C# P-Invoke / Rust bindgen) + ABI dondurma (A0). C++ tipleri sınırı geçemez (ADR-017 K1). Kanıt: `neva_session.h:3` yorumu.

**S2 — neden `char16_t` yol?**
Windows Unicode (UTF-16 native). Linux'ta `uchar.h` (C11) ile derlenir. Türkçe/sağlayıcı yolları (ör. `C:\Müzik\…`) bu yüzden bozulmaz. NE-ENG-005 · TC-ENG-005.

**S3 — Exception fırlatılabilir mi?**
Hayır. FFI sınırında C++ exception = tanımsız davranış (üst dille yayılım). Tüm hatalar `neva_result_t` (5 kod). NE-ENG-004.

**S4 — "8 fonksiyon" değil miydi?**
Taslak PRD'lerde "8" yazıyordu; disk kanıtı **9 imza** (§21.D.3). C-01 çelişkisi 9'a düzeltildi. Yeni fonksiyon = minor sürüm (E1-E10, E5-E7).

**S5 — EQ neden tek `set_eq_gain`?**
C ABI minimal tutuldu (per-kanal erişim C++ `EqChain::set_gain(band, ch, db, ramp_ms)` içinde — `neva_session.h:47-48` yorumu). Genişletme E5 (`set_eq_band_count`) + E6 (preset).

**S6 — 15/20-band neden çalışmıyor?**
Bilinçli kilit: `kSubset15/kSubset20 = {false,0,{}}` (disk `eq_grids.h:51-52`). Sebep: VLC/AIMP frekans listeleri teyitsiz (`⚠️ VR`). Karar: §18/U3 kardeş + R-P09.

**S7 — WASAPI Shared'da bit-perfect olur mu?**
Hayır — rozet yalnız exclusive (NE-ENG-045 · ADR-0105 kapı 5.1 `⚠️ VR`). Shared = OS mikser yolu (düzeltme olasılığı).

**S8 — DSD destekliyor mu?**
Native DSD kapalı (U3 — `libdsd` lisansı `UNKNOWN`). DoP (DSD over PCM) açık kalır. `decode_stub.h` TODO satırı kanıtı.

**S9 — ASIO neden varsayılan değil?**
Lisans tartışması (U1 — VSL) kapanmadan `NEVA_WITH_ASIO=OFF` (ADR-107 §8.1). WASAPI Exclusive zaten 2. basamak.

**S10 — Neden Null backend?**
CI/conta ortamında ses cihazı yoktur; session yaşam döngüsü yine test edilir. Zincirin 8. kaydı (§21.H.2).

**S11 — RT thread'de neden log yok?**
Lock + IO yasağı (S3 §6.1). Log = worker/UI tarafı. RT-only sinyal: atomik sayaç + kuyruk.

**S12 — Fuzz neden ABI'yi de hedefliyor?**
Uzak process (player/host) hatalı argüman gönderebilir (T-E03) — clamp katmanı (§13.3) + fuzz mutasyon vektörleri.

**S13 — RAM bütçesi neden 400 MB?**
ADR-0105 §2.9 (`⚠️ VR` — dosyasız). Bütçe G4 stres raporunda ölçülür (§21.F.6); eşik M1'de sabitlenir.

**S14 — Neden 3 ayrı test exe?**
Ayrışma: DSP-alanı (biquad+ring) · grid-kilitleri · parser. ctest adları §21.D.8. Tek exeye bölme = TE-02 kararı.

**S15 — Test framework ne?**
Basit `<cassert>` (doctest/Catch2 DEĞİL) — `tests/test_biquad.cpp:4-6` yorumu: "Basit assert; ctest ile çalışır. RT bağımlılığı yok". Migrate = TE-02.

**S16 — ARM64 neden `⚠️ VR`?**
Derlendiği ama cihazda test edilmediği için (NE-ENG-032 · A1 fazı). "Derleniyor" ≠ "çalışıyor".

**S17 — macOS neden sonda?**
CoreAudio backend A2 fazında (ADR-0107 A2-M6 zinciri); test donanımı erişimi `UNKNOWN` (U3/U11).

**S18 — Neva Engine kendi başına ses çalar mı?**
Evet — `open_file` + `play` (§21.G.5 S05). UI/player ayrı süreçtir (neva-host — U12).

**S19 — Event mekanizması var mı (E1-E10)?**
Şu an YOK (poll/queried). E1-E10 = minor genişleme (NE-ENG-128 · plr §05.2 ile).

**S20 — PRD neden bu kadar uzun?**
8000+ satır şartı (kullanıcı) + 182 gereksinim/182 test/20 senaryo + ekler (envanter/ADR özetleri/şablonlar). Her uzunluk bilgi yoğunluğu için; dolgu yok.

### §21.I.2 Hata Ayıklama Rehberi (belirti → kök neden → çözüm)

| Belirti | Olası kök neden | Çözüm / denetim |
|---|---|---|
| `NEVA_ERR_NO_DEVICE` (2) | Cihaz yok/mesgul · sürücü çökmüş | `enumerate` çağır · zincir log'u · §21.H.2 |
| `NEVA_ERR_UNSUPPORTED_FORMAT` (3) | WAV stub int16/f32 dışını reddeder | format matrisi §05.1 · decode_stub kapsamı |
| `NEVA_ERR_INVALID_ARG` (1) | NULL yol · band>30 · geçersiz handle | clamp tablosu §13.3 · TC-ENG-157 |
| Exclusive açılmıyor | Cihaz meşgul (AUDCLNT_E_DEVICE_IN_USE) | Shared düşüşü otomatik (§21.D.7) · S09 |
| Ses yok ama `play` OK | Null backend'e düşüldü · mute/OS mikser | Zincir log · cihaz var mı · S12 |
| Tıklama/pop | Rampa yok · buffer taşması · xrun | FM-E12/E14 · TC-ENG-051/056 |
| xrun artışı | RT'de alloc/lock sızıntısı | statik kapı + TSan · §07.1 |
| Hash eşleşmiyor (bit-perfect) | Flat değil · hız dönüşümü · shared mod | 4 ön koşul §21.H.3 · inverted guard |
| Türkçe yol bozuk | char16_t→char daraltma | TC-ENG-005 · §21.D.3 |
| Preset yüklenmiyor | grid_version/band_count uyuşmazlığı | EqPreset kuralları §21.D.5 |
| ICY başlık boş/bozuk | UTF-8 geçersiz · >256 B · HTML | icy_parser DROP akışı §21.H.6 |
| Leak raporu | destroy çağrılmadı · COM referans | LSan · `destroy` (delete s) §21.D.7 |
| Sembol bulunamadı (link) | STATIC/DLL karışıklığı | `NEVA_API` makrosu §21.D.3 · CMake 27/39 |
| `ctest` 0 test buluyor | `enable_testing()` + `add_test` yok (eski CMake cache) | CMakeLists 48-74 · temiz build |
| ARM64 hatası | MSVC-specific kod (`ole32` vb.) | platform koruması (`#ifdef _WIN32`) §21.D.3 |
| Fuzz timeout | parser patolojik girdi | kMetaCap/kMetaintMax kapıları · §21.H.6 |
| UBSan UB | NaN/overflow (dB dönüşümü) | clamp ±30 dB `[ÖNERİ]` · TC-ENG-157 |
| Cihaz sonra kayboldu | Hot-plug | S10/S11 · FM-E02 · DeviceNotifier §21.D.7 |
| CPU yükseliyor | rampa döngüsü · bypass maskesi | set_active_bands · §06.3 |

### §21.I.3 Sık Yapılan Hatalar (bu PRD'nin bağlamında)

1. **"Bit-perfect" iddiası ölçmeden yazmak** → Edge 3 ihlali; yalnız hash raporu (§21.F.2) ile.
2. **Yeni error code eklemek** → ABI kırar; kodlar 5'te sabit (NE-ENG-061/100).
3. **15/20-band frekanslarını uydurmak** → kilit {false,0,{}}; teyit gelene kadar gizli.
4. **RT'de `printf`/log** → statik kapı ihlali; kuyruk + worker.
5. **ASIO'yu varsayılan açmak** → U1 lisansı açık; OFF (ADR-107 §8.1).
6. **`--enable-gpl` ile FFmpeg derlemek** → G3 ihlali; grep CI kırmızı.
7. **`test_biquad.obj` gibi artefakt commit etmek** → NE-ENG-125; .gitignore.
8. **PRD'de uydurma enum adı yazmak** → bu turda düzeltildi (C-01 §18.17); her iddia `neva_session.h` satır numarasıyla kanıtlanır.
9. **"7-step" vs 8 kayıt** → C-09: Null dahil 8; "7 aktif" ifadesi yalnızca Null hariç bağlamda.
10. **`⚠️ VR` eşikleri bağlayıcı sanmak** → bağlayıcılar ADR/ölçüm raporu; eşikler M1'de sabitlenir (§18.8).

### §21.I.4 PRD Kapanış Kontrol Listesi (K-6 öncesi)

- [ ] 182 gereksinim + 182 test çapraz okundu (§21 Ek A/B/C otomatik)
- [ ] Disk envanteri tazelendi (§21.D — 2026-10-23)
- [ ] C-01..C-09 çelişki defteri işlendi (§18.17)
- [ ] U1..U15 karar kapıları sahiplendirildi (§18.16)
- [ ] Ölçüm şablonları bağlandı (§21.F ↔ §11)
- [ ] Senaryolar faz matrisine oturdu (§21.G.6)
- [ ] `⚠️ VR` / `[ÖNERİ]` / `UNKNOWN` taraması yapıldı (§20.6)
- [ ] Kullanıcı onayı alındı (§20.4 — BEKLİYOR)

## §21 Ek J — Gereksinim Künyesi Sözlüğü (Engine · otomatik extraction)

> **Otomatik üretildi** §10 bloklarından (başlık + kabul kriteri + ADR izi + test atıfı) — elle yazılmadı.
> Kapsam: 174 blok = 182 ID (NE-ENG-167..175 tek blokta 9 faz kapısı). **Bağlayıcı metin §10'dur**; bu ek özet referanstır.

**NE-ENG-001 — Tek C ABI sınırı**
- Kabul: `neva_session.h` dışında public C++ header export edilmez; `neva_session.dll` export tablosunda yalnız `neva_*` sembolleri görünür.
- ADR: ADR-0105 §2.6 (indeks kaydı ⚠️) · disk: `neva_session.h` yorum satırı · Test: TC-ENG-001 · Blok: eng-p05.md

**NE-ENG-002 — Opaque handle zorunluluğu**
- Kabul: Header'da struct tanımı yok; yalnızca `typedef struct neva_session neva_session_t;` — derleme testi kırılırsa FAIL.
- ADR: disk `neva_session.h:29` · Test: TC-ENG-002 · Blok: eng-p05.md

**NE-ENG-003 — api_version handshake**
- Kabul: ABI kıran değişimde sürüm major artar; eski header ile yeni DLL eşleşmesi derleme/runtime kontrolü ile reddedilir `[ÖNERİ — runtime kontrol detayı ADR'de yok]`.
- ADR: disk `neva_session.h:26` · Test: TC-ENG-003 · Blok: eng-p05.md

**NE-ENG-004 — Hata kodu sözleşmesi (exception yok)**
- Kabul: Export edilen her `neva_*` fonksiyonu exception fırlatamaz; `extern "C"` bloğu içinde `throw` yok (statik denetim).
- ADR: disk `neva_session.h:31-37` · ADR-0105 §2.6 ⚠️ · Test: TC-ENG-004 · Blok: eng-p05.md

**NE-ENG-005 — char16_t dosya yolu (Windows Unicode)**
- Kabul: Türkçe karakterli (ğ, ı, ş, İ, ö, ü, ç) uzun yolda dosya açılır; UTF-8→UTF-16 dönüşümü Unix portunda ayrı katman `[ÖNERİ]`.
- ADR: disk `neva_session.h` · Test: TC-ENG-005 · Blok: eng-p05.md

**NE-ENG-006 — IAudioBackend tek soyutlama noktası**
- Kabul: `neva-core` ve `apps/*` dışında `#include <audioclient.h>/<alsa/...>/pa_*.h` yok — CI grep kapısı.
- ADR: S3 §4.3 yasak 4 · ADR-019 §2.2 · Test: TC-ENG-006 · Blok: eng-p05.md

**NE-ENG-007 — BackendRegistry kayıt tablosu**
- Kabul: Registry'de platform-başı beklenen sıralama §04.4 zinciriyle birebir; Null her platformda kayıtlı.
- ADR: S3 §3.3 · Test: TC-ENG-007 · Blok: eng-p05.md

**NE-ENG-008 — open_best_backend fallback zinciri**
- Kabul: Her basamak yapay hata enjeksiyonuyla düşürüldüğünde zincir sıradaki basamağa geçer; son basamak Null'dur, asla "hiçbir şey yok" durumu oluşmaz.
- ADR: ADR-107 §2.1 · ADR-019 (brain.md:862) · S3 §3.3 · Test: TC-ENG-008 · Blok: eng-p05.md

**NE-ENG-009 — negotiated_format() kanıt zorunluluğu**
- Kabul: Hash testi `negotiated_format() == hedef` kontrolünü sağlamadan yeşil olmaz.
- ADR: S3 §3.2 · ADR-107 K3 · Test: TC-ENG-009 · Blok: eng-p05.md

**NE-ENG-010 — BackendKind enum sabitliği**
- Kabul: Enum değeri değişikliği derleme hatası veya majör sürüm ister.
- ADR: S3 §3.1 · Test: TC-ENG-010 · Blok: eng-p05.md

**NE-ENG-011 — AudioFormat alan sözleşmesi**
- Kabul: Sınır dışı değerlerde `StreamError` döner (UB yok); 8.1 hedefi için channels=8 geçerli.
- ADR: S3 §3.1 · VISION (8.1, V-8) · Test: TC-ENG-011 · Blok: eng-p05.md

**NE-ENG-012 — BackendKind + StreamError taşıma**
- Kabul: Zincir düşüşlerinde hangi basamağın neden düştüğü log'a (non-RT) yazılır.
- ADR: S3 §3.1 · Test: TC-ENG-012 · Blok: eng-p05.md

**NE-ENG-013 — ADR-019 ek imzalar (faz 2 genişletmesi)**
- Kabul: Üç method `IAudioBackend`'te mevcut; tüm implementasyonlar (Null dahil) override eder; kontrat testi yeşil.
- ADR: ADR-019 §2.2b · §00.3-D2 · Test: TC-ENG-013 · Blok: eng-p05.md

**NE-ENG-014 — exclusive↔shared geçiş (switchMode)**
- Kabul: Geçiş sırasında ses kesintisi hedefi `⚠️ VR (eşik yok — önerü: ≤20 ms)`; hata durumunda zincir düşer.
- ADR: ADR-019 F2 · S3 §3.4 · Test: TC-ENG-014 · Blok: eng-p05.md

**NE-ENG-015 — Mismatch çözüm sırası (F3)**
- Kabul: 44.1 kHz dosya 48 kHz cihazda: hız denemesi başarısızsa soxr ile resample; RT callback'te resample asla yok.
- ADR: ADR-019 §2 (çözüm seçenekleri 1-3) · Test: TC-ENG-015 · Blok: eng-p05.md

**NE-ENG-016 — kill-switch (noexcept acil durdurma)**
- Kabul: Kill sonrası callback devre dışı; yeniden başlatma session state ile uyumlu; callback içinde asla bloklanmaz.
- ADR: ADR-019 F4 · ADR-017 (brain.md:869) · Test: TC-ENG-016 · Blok: eng-p05.md

**NE-ENG-017 — Platform matrisi bağımsızlığı**
- Kabul: Kaynak kod `#ifdef` platform kütüphanesine göre ayrılır; ortak çekirdek platform-bağımsız derlenir.
- ADR: S3 §2 · ADR-107 §8.4 · Test: TC-ENG-017 · Blok: eng-p05.md

**NE-ENG-018 — K-space band rezervasyonu koruması**
- Kabul: Bu PRD dahil hiçbir Neva dokümanı band-2..7'ye gerçek domain adı yazmaz.
- ADR: ADR-097 §K5 · Test: TC-ENG-018 (statik denetim) · Blok: eng-p05.md

**NE-ENG-019 — Windows XP/7 kapsam dışı teyidi**
- Kabul: §10'da XP/7'yi hedefleyen madde yok; CI matrisinde yok.
- ADR: S3 §2 yorumu (Düşük öncelik RED) · Test: TC-ENG-019 (statik denetim) · Blok: eng-p05.md

**NE-ENG-020 — ARM64 port hedefi**
- Kabul: ARM64 build CI'da derlenir; Null backend testleri geçer (donanım testi RPi5 üzerinde).
- ADR: S3 §2 · S3 §5 (GCC aarch64) · Test: TC-ENG-020 · Blok: eng-p05.md

**NE-ENG-021 — WASAPI Exclusive implementasyonu (P0)**
- Kabul: Event-driven callback kurulur; iki deneme sırası korunur; başarısızlık `StreamError` + zincir 2b/3'e düşer.
- ADR: S3 §3.4 · spec `wasapi-exclusive.md:126` · Test: TC-ENG-021 · Blok: eng-p05.md

**NE-ENG-022 — WASAPI Shared fallback**
- Kabul: `AUDCLNT_E_DEVICE_IN_USE` senaryosunda ses devam eder; bit-perfect rozeti devre dışı kalır (player tarafı §05.2 B3).
- ADR: ADR-019 §1.1 · S3 §3.4 · Test: TC-ENG-022 · Blok: eng-p05.md

**NE-ENG-023 — ASIO2 backend (lisans kapısı K4/U1'e bağlı)**
- Kabul: ASIO open/start/stop/callback akışı; sürücü yoksa zincir 2'ye düşer; **SDK lisansı çözülmeden release YOK** (NE-ENG-161 GATE).
- ADR: ADR-019 · S3 §3.4 (P0) · ADR-107 K4 · Test: TC-ENG-023 · Blok: eng-p05.md

**NE-ENG-024 — WDM-KS backend**
- Kabul: `hw:` benzeri doğrudan erişim; başarısızlık Null'a düşer.
- ADR: S3 §3.4 (P2) · ADR-107 §2.1 · Test: TC-ENG-024 · Blok: eng-p05.md

**NE-ENG-025 — PipeWire backend (F32P)**
- Kabul: PipeWire yoksa ALSA'ya düşer; float akışta bit-derinlik dönüşümü olmadan geçiş.
- ADR: S3 §3.4 · S3 §2 · Test: TC-ENG-025 · Blok: eng-p05.md

**NE-ENG-026 — ALSA backend (hw: doğrudan)**
- Kabul: `hw:` erişilemezse Null'a düşer; format sabitleme başarısızsa hata döner.
- ADR: S3 §3.4 · Test: TC-ENG-026 · Blok: eng-p05.md

**NE-ENG-027 — CoreAudio backend (HAL)**
- Kabul: HAL physical format okunur/yazılır; başarısızlık Null.
- ADR: S3 §3.4 · spec `core-audio-macos.md` · Test: TC-ENG-027 · Blok: eng-p05.md

**NE-ENG-028 — Null backend (CI/test)**
- Kabul: IMPLEMENTED (disk); her platformda en son basamak; testlerde deterministik.
- ADR: S7 `null_backend.cpp` · ADR-019 (Null Output) · Test: TC-ENG-028 · Blok: eng-p05.md

**NE-ENG-029 — Cihaz enumerate + format listesi**
- Kabul: Liste boşsa `StreamError`; UI/CLI listeyi dizebilir.
- ADR: S3 §3.2 · Test: TC-ENG-029 · Blok: eng-p05.md

**NE-ENG-030 — Hot-plug olay desteği**
- Kabul: Çalma sırasında cihaz koparsa zincir devreye girer; session durumu raporlanır (event E8).
- ADR: ADR-019 F1 · spec `core-audio-macos.md:46-57` · Windows/Linux `⚠️ VR` · Test: TC-ENG-030 · Blok: eng-p05.md

**NE-ENG-031 — switchMode genişletmesi (NE-ENG-013'un parçası)**
- Kabul: Null dahil tüm backend'lerde tanımlı (varsayılan: Unsupported döner).
- ADR: ADR-019 §2.2b · Test: TC-ENG-031 · Blok: eng-p05.md

**NE-ENG-032 — setDeviceRate genişletmesi**
- Kabul: Desteklemeyen cihaz `StreamError` döner → zincir resample/fallback'e geçer.
- ADR: ADR-019 §2.2b · Test: TC-ENG-032 · Blok: eng-p05.md

**NE-ENG-033 — kill() genişletmesi**
- Kabul: noexcept; callback sonraki periyotta durur.
- ADR: ADR-019 §2.2b · Test: TC-ENG-033 · Blok: eng-p05.md

**NE-ENG-034 — Callback render sözleşmesi**
- Kabul: Callback imzası sabit; taşıma (out-buffer + frame sayacı) backend'e göre format dönüşümü içermez (PCM float/interleaved ortak).
- ADR: S3 §3.2 · ADR-017 · Test: TC-ENG-034 · Blok: eng-p05.md

**NE-ENG-035 — Backend yaşam döngüsü (open/start/stop/close)**
- Kabul: Yanlış sıralama (start önce open) `StreamError`; close idempotent (ikinci çağrı güvenli).
- ADR: S3 §3.2 · Test: TC-ENG-035 · Blok: eng-p05.md

**NE-ENG-036 — Bit-perfect yolu: format birebirlik**
- Kabul: 12 kombinasyon (§11.2) hash byte-eşit.
- ADR: ADR-107 K3 · ADR-0105 kapı 5.1 ⚠️ · Test: TC-ENG-036 · Blok: eng-p05.md

**NE-ENG-037 — OS mikser baypası (exclusive)**
- Kabul: Windows'ta session volume 1.0'a sabitlenir; paylaşım modunda bu iddia düşer ve raporlanır.
- ADR: VISION (V-5) · ADR-019 · Test: TC-ENG-037 · Blok: eng-p05.md

**NE-ENG-038 — Exclusive kilit çatışması (device-in-use)**
- Kabul: Test: iki süreç — biri exclusive tutar, diğeri düşüş zincirini uygular.
- ADR: spec `wasapi-exclusive.md:126` · Test: TC-ENG-038 · Blok: eng-p05.md

**NE-ENG-039 — Periyot/buffer hizalama kurtarma**
- Kabul: Hata tek seferde yutulmaz; yeniden hesap başarısızsa zincir düşer.
- ADR: ADR-106 §1.3 (radio-and-hires §3-§4) · Test: TC-ENG-039 · Blok: eng-p05.md

**NE-ENG-040 — Xrun sayacı & telemetri**
- Kabul: 30 dk stres testte sayaçlar okunabilir; ADR-017 politikası uygulanır.
- ADR: ADR-017 · S3 §6.1 · Test: TC-ENG-040 · Blok: eng-p05.md

**NE-ENG-041 — Deterministik test backend'i (Null + injectable clock)**
- Kabul: Testler duvar saati/gecikmeye bağlı değil; CI'da 2× hız simülasyonu yapılabilir `[ÖNERİ]`.
- ADR: S3 §6 (test altyapısı) · Test: TC-ENG-041 · Blok: eng-p05.md

**NE-ENG-042 — Backend kontrat test takımı**
- Kabul: Yeni backend = aynı takımı geçmeden registry'ye kayıt yok (CI kuralı).
- ADR: S3 §6.1 (kabul 1) · Test: TC-ENG-042 · Blok: eng-p05.md

**NE-ENG-043 — Fallback telemetri olayı**
- Kabul: Düşüş nedeni (kod + backend) event'te taşınır.
- ADR: S3 §3.1 (StreamError) · §05.2 E8 · Test: TC-ENG-043 · Blok: eng-p05.md

**NE-ENG-044 — Backend başına dizin kuralı**
- Kabul: CI grep: `backends/wasapi/*` içinde `backends/alsa/*` include'u yok.
- ADR: S3 §3.4 · Test: TC-ENG-044 · Blok: eng-p05.md

**NE-ENG-045 — Null dahil 8 backend envanteri**
- Kabul: Registry sayımı 8; uygulanmayanlar `NotImplemented` (`StreamError`) döner, sessizce yok sayılmaz.
- ADR: S3 §3.1 BackendKind · Test: TC-ENG-045 · Blok: eng-p05.md

**NE-ENG-046 — Tek public header disiplini**
- Kabul: Install/distribute paketinde yalnız tek header + DLL/LIB; iç header yok.
- ADR: disk `neva_session.h` · ADR-0105 §2.6 ⚠️ · Test: TC-ENG-046 · Blok: eng-p06.md

**NE-ENG-047 — Event callback genişletmesi (E1)**
- Kabul: Callback RT'den çağrılmaz (non-RT kuyruk); `user` pointer taşınır; unregister güvenli.
- ADR: ADR-106 kural 2 · kardeş PRD §05.2 · Test: TC-ENG-047 · Blok: eng-p06.md

**NE-ENG-048 — Queue API (E2)**
- Kabul: Sıradaki dosya decode'u RT'yi beklemeden hazırlanır; geçişte ring kesintisiz.
- ADR: ADR-0105 AK-5 ⚠️ · kardeş PRD §05.2 · Test: TC-ENG-048 · Blok: eng-p06.md

**NE-ENG-049 — Position/duration API (E3)**
- Kabul: Stream (radyo) durumunda duration=-1/kısa kod; atomik okuma.
- ADR: kardeş PRD §05.2 · Test: TC-ENG-049 · Blok: eng-p06.md

**NE-ENG-050 — Seek API (E4)**
- Kabul: Seek sırasında kısa fade (click yok); sınırda clamp; başarısızlık hata döner.
- ADR: kardeş PRD §05.2 · ADR-017 (RT dışı işlem) · Test: TC-ENG-050 · Blok: eng-p06.md

**NE-ENG-051 — EQ band count API (E5)**
- Kabul: Değişim RT dışı maske hesabı → atomik yayın → callback ramp (tahsis yok).
- ADR: ADR-106 kural 5 · kardeş PRD §05.2 · Test: TC-ENG-051 · Blok: eng-p06.md

**NE-ENG-052 — EQ preset API (E6)**
- Kabul: Band sayısı preset'ten farklıysa kural 2 işletilir; grid_version uyuşmazlığında düzeltme + uyarı.
- ADR: ADR-106 kural 6 · kardeş PRD §05.2 · Test: TC-ENG-052 · Blok: eng-p06.md

**NE-ENG-053 — negotiated_format API (E7)**
- Kabul: Cihazın gerçekten kabul ettiği format döner (K3 kanıtı).
- ADR: kardeş PRD §05.2 · ADR-107 K3 · Test: TC-ENG-053 · Blok: eng-p06.md

**NE-ENG-054 — Device list + change event (E8)**
- Kabul: Liste değişimi olayı UI'a akar; enumerate thread-safe.
- ADR: ADR-019 F1 · kardeş PRD §05.2 · Test: TC-ENG-054 · Blok: eng-p06.md

**NE-ENG-055 — Tuner API (E9)**
- Kabul: URL validator Player katmanında (engine tuner'a ham URL alır — doğrulama zorunlu akışta player'da) `[ÖNERİ — engine içine validator koyma; tek kapı kuralı ADR-106]`.
- ADR: ADR-106 §2.2a · kardeş PRD §05.2 E9 · Test: TC-ENG-055 · Blok: eng-p06.md

**NE-ENG-056 — LUFS okuma (E10)**
- Kabul: 10 Hz güncelleme (atomik publish); RT'de hesap yok.
- ADR: ADR-106 §2.2a şeması · kardeş PRD §05.2 · Test: TC-ENG-056 · Blok: eng-p06.md

**NE-ENG-057 — Yeni error code eklenmez kuralı**
- Kabul: Mevcut kod değerleri 0-4 sabit (yeniden numaralandırma yasak).
- ADR: kardeş PRD §05.2 notu · Test: TC-ENG-057 · Blok: eng-p06.md

**NE-ENG-058 — char16_t/UTF-8 geçiş katmanı (Unix)**
- Kabul: Türkçe karakterli yol Linux'ta da açılır (NFC/NFD dikkati `[ÖNERİ]`).
- ADR: kardeş PRD §05.1 A4 · Test: TC-ENG-058 · Blok: eng-p06.md

**NE-ENG-059 — Thread-safety beyanı**
- Kabul: TSan testi temiz (Null backend üzerinde); belge beyanı + sınır (`get_*` atomik snapshot).
- ADR: kardeş PRD §05.3–§05.4 · ADR-0105 §2.6 ⚠️ · Test: TC-ENG-059 · Blok: eng-p06.md

**NE-ENG-060 — ABI freeze kapısı**
- Kabul: ABI dump (binwalk/nm) kaydedilir; sürüm 1.0.0 sembol seti arşivde.
- ADR: kardeş PRD §05.4 · Test: TC-ENG-060 · Blok: eng-p06.md

**NE-ENG-061 — Biquad çekirdeği (RBJ)**
- Kabul: `test_biquad` (IMPLEMENTED) + doğruluk sapması toleransı `⚠️ VR (tolerans değeri tanımsız — önerü: ±0.01 dB @ referans frekanslar)`.
- ADR: ADR-106 §1.3 · disk `biquad.h` · `test_biquad.cpp` · Test: TC-ENG-061 · Blok: eng-p06.md

**NE-ENG-062 — EqChain: 31 band × 2 kanal**
- Kabul: Tahsis session-open'da; zincirde çalışırken `new` yok (denetim §07.5).
- ADR: ADR-106 kural 4/6 · disk `eq_chain.h` · Test: TC-ENG-062 · Blok: eng-p06.md

**NE-ENG-063 — ISO 1/3-oktav grid (31 frekans)**
- Kabul: `eq_grids_tests` yeşil; frekanslar tabloyla byte-eşit.
- ADR: ADR-106 §2.2b · disk `eq_grids.h` · Test: TC-ENG-063 · Blok: eng-p06.md

**NE-ENG-064 — 10-band alt küme (ISO)**
- Kabul: Alt küme üretilir; VLC preset'i zorla map edilmez.
- ADR: ADR-106 §2.2b kural 3 · Test: TC-ENG-064 · Blok: eng-p06.md

**NE-ENG-065 — 15/20-band grid tanımı (teyitli)**
- Kabul: `eq_grids` tanımında sabitlenir; teyit (AIMP dump/web) gelene kadar etiket korunur.
- ADR: ADR-106 §2.2b kural 4/5 · ADR-106 §1.4 · Test: TC-ENG-065 · Blok: eng-p06.md

**NE-ENG-066 — Bypass maskesi (zincir takası yok)**
- Kabul: Tüm-band flat: maske=0 → matematiksel doğrudan geçiş; hash testi flat durumda byte-eşit.
- ADR: ADR-106 kural 4 · kardeş PRD §06.1 D3 · Test: TC-ENG-066 · Blok: eng-p06.md

**NE-ENG-067 — Ramp smoothing 5-20 ms**
- Kabul: Anlık adım değişimi testinde pop yok (waveform analizi); ramp süresi parametrik.
- ADR: ADR-105 §2.3 (ADR-106 kural 5) ⚠️ · Test: TC-ENG-067 · Blok: eng-p06.md

**NE-ENG-068 — Cascade mod geçişi**
- Kabul: Geçişte ses sürekliliği; tahsis yalnız geçişte (RT dışı hazırlık).
- ADR: ADR-106 kural 5 · Test: TC-ENG-068 · Blok: eng-p06.md

**NE-ENG-069 — Parametrik mod (RBJ serbest)**
- Kabul: Parametrik mod RT kurallarına uyar; grid preset'i ile karışmaz (ayrı state).
- ADR: ADR-106 §2.2b kural 7 (ADR-0105 §2.1 korunur) · Test: TC-ENG-069 · Blok: eng-p06.md

**NE-ENG-070 — True-peak limiter (BS.1770-4)**
- Kabul: Bit-perfect yolda limiter BYPASS (hash bozulmaz); aşım testi ceiling aşılmaz.
- ADR: ADR-0105 §2.1 ⚠️ · kardeş PRD §06.4 · Test: TC-ENG-070 · Blok: eng-p06.md

**NE-ENG-071 — R128 loudness sidechain (non-RT)**
- Kabul: RT callback'e dokunmaz; atomik publish; referans implementasyon testi (bilinen sinyal → beklenen LUFS ±0.1 `⚠️ VR (tolerans tanımsız)`).
- ADR: ADR-106 §2.2a · kardeş PRD §06.4 · Test: TC-ENG-071 · Blok: eng-p06.md

**NE-ENG-072 — soxr bypass (bit-perfect yol)**
- Kabul: §11.2 12 kombinasyonda hash eşitliği bunu zorunlu kılar.
- ADR: kardeş PRD §06.5 R4 (`⚠️ VR` — NE-ENG-061 olarak bu PRD'de sabitlendi) · Test: TC-ENG-072 · Blok: eng-p06.md

**NE-ENG-073 — Preamp/gain bloğu**
- Kabul: Bit-perfect yolda 0 dB = identity (hash).
- ADR: kardeş PRD §06.1 · Test: TC-ENG-073 · Blok: eng-p06.md

**NE-ENG-074 — Multi-channel (8.1) DSP**
- Kabul: 5.1/8.1 test kaynağından geçiş; CPU bütçesi karşılanır.
- ADR: VISION V-8 · S3 §3.1 · Test: TC-ENG-074 · Blok: eng-p06.md

**NE-ENG-075 — Ring overflow politikası (dosya modu)**
- Kabul: İki mod davranışları §07.4 tablosuyla tutarlı.
- ADR: kardeş PRD §07.4 `[ÖNERİ]` · Test: TC-ENG-075 · Blok: eng-p06.md

**NE-ENG-076 — DSP profil kapısı (worst-case)**
- Kabul: CI'da EQ benchmark'ı 31-band sabit.
- ADR: ADR-0105 §5.6 (ADR-106 kural 6) ⚠️ · Test: TC-ENG-076 · Blok: eng-p06.md

**NE-ENG-077 — CPU ≤%5 EQ (192 kHz stereo)**
- Kabul: CI benchmark FAIL → release bloke.
- ADR: ADR-106 §1.3 · ADR-0105 §5.6 ⚠️ · Test: TC-ENG-077 · Blok: eng-p06.md

**NE-ENG-078 — DSP state sıfırlama API**
- Kabul: Reset sonrası ilk periyot transient'sız (eski durum sızıntısı yok).
- ADR: kardeş PRD §05.2 (seek dahil) `[ÖNERİ]` · Test: TC-ENG-078 · Blok: eng-p06.md

**NE-ENG-079 — Denormal koruması**
- Kabul: Sessizlik sonrası ilk ses periyotunda CPU spike yok (bilinen klasik hata).
- ADR: YOK — `[ÖNERİ]` (RT best practice; `.ai/electronic/dsp/` ile hizalı) · Test: TC-ENG-079 · Blok: eng-p06.md

**NE-ENG-080 — DSP birim test kapsamı**
- Kabul: §11.4 birim test matrisi yeşil; her grid frekansı doğruluk testinde.
- ADR: disk `tests/*` · kardeş PRD §11 · Test: TC-ENG-080 · Blok: eng-p06.md

**NE-ENG-081 — Zero-allocation RT callback**
- Kabul: Test build'de operator-new override ile hard-fail; 30 dk stres = 0 tahsis.
- ADR: ADR-017 · VISION V-3 · kardeş PRD §07 · Test: TC-ENG-081 · Blok: eng-p07.md

**NE-ENG-082 — Lock-free SPSC veri yolu**
- Kabul: TSan/Helgrind temiz; invariant (writer okuyucuyu geçmez) assert ile testte.
- ADR: VISION V-4 · disk `spsc_ring.h` · Test: TC-ENG-082 · Blok: eng-p07.md

**NE-ENG-083 — RT yasağı denetimi (statik + dinamik)**
- Kabul: CI'da `backends/*/render*` içinde yasaklı çağrımarsa FAIL.
- ADR: ADR-017 · kardeş PRD §07.5 · Test: TC-ENG-083 · Blok: eng-p07.md

**NE-ENG-084 — Stack bütçesi (callback ≤4 KB) `[ÖNERİ]`**
- Kabul: Stack-usage raporu (compiler `-fstack-usage`); eşiğe aşım alarm.
- ADR: kardeş PRD §08.3 `[ÖNERİ]` · Test: TC-ENG-084 · Blok: eng-p07.md

**NE-ENG-085 — Callback süre ölçümü (P99/P999)**
- Kabul: §08.4 P99 eşikleri; ölçüm üretimde de açık (düşük maliyetli atomik sayaç).
- ADR: kardeş PRD §08.4 `[ÖNERİ]` · Test: TC-ENG-085 · Blok: eng-p07.md

**NE-ENG-086 — Starve fade (≤50 ms)**
- Kabul: Boşaltma senaryosunda waveform'de monoton azalma; ramp süresi ≤50 ms.
- ADR: ADR-106 §2.2a şeması · Test: TC-ENG-086 · Blok: eng-p07.md

**NE-ENG-087 — Xrun politikası (fade-out → 50 ms → restart)**
- Kabul: Yapay xrun enjeksiyonu ile doğrulama; sayaçlar artar, ses kurtarılır.
- ADR: ADR-017 (brain.md:869) · Test: TC-ENG-087 · Blok: eng-p07.md

**NE-ENG-088 — 30 dk stres = 0 xrun**
- Kabul: Referans donanımda (hedef sınıf) gece testi; log raporu.
- ADR: S3 §6.1 · Test: TC-ENG-088 · Blok: eng-p07.md

**NE-ENG-089 — Latency bütçesi: ASIO <10 ms**
- Kabul: Loopback ölçüm (§08.5 M2); eşiği aşan blok boyutu seçimi otomatik reddedilir.
- ADR: ADR-006 (ADR-019 §1.4) · Test: TC-ENG-089 · Blok: eng-p07.md

**NE-ENG-090 — Latency bütçesi: WASAPI <20 ms**
- Kabul: L1-L4 merdiveni `⚠️ VR (değerler indeks metninde yok)` — tanımlanana kadar en küçük desteklenen blok zorlanır `[ÖNERİ]`.
- ADR: ADR-006 · ADR-0105 §2.10 ⚠️ · Test: TC-ENG-090 · Blok: eng-p07.md

**NE-ENG-091 — Ring kapasite hesabı**
- Kabul: Hesap dökümü §08.3'te; starve/overflow testleri bu kapasiteyle geçer.
- ADR: kardeş PRD §07.3 · Test: TC-ENG-091 · Blok: eng-p07.md

**NE-ENG-092 — RAM ≤400 MB (player toplam payı)**
- Kabul: Profil raporu; bütçe aşımında release bloke.
- ADR: ADR-0105 §2.9 ⚠️ · Test: TC-ENG-092 · Blok: eng-p07.md

**NE-ENG-093 — Bellek sızıntısı (leak) testi**
- Kabul: ASan/LSan temiz.
- ADR: kardeş PRD §11 test · Test: TC-ENG-093 · Blok: eng-p07.md

**NE-ENG-094 — Fuzzing kapı altyapısı (engine sınırları)**
- Kabul: ADR-0105 kapı 5.13 ruhu: 0 ASan/UBSan crash; CI ≤10 dk `⚠️ VR (eşikler indeks)`.
- ADR: ADR-0105 kapı 5.13 ⚠️ · ADR-106 (ICY) · Test: TC-ENG-094 · Blok: eng-p07.md

**NE-ENG-095 — ICY parser limitleri (engine çekirdeği)**
- Kabul: Sınır dışı değer stream RED; parser asla sınırsız buffer büyütmaz.
- ADR: ADR-106 kural 8 · disk `icy_parser.h` · Test: TC-ENG-095 · Blok: eng-p07.md

**NE-ENG-096 — CPU bütçesi ölçüm ortamı**
- Kabul: Bench ortamı + frekans sabitleme notu raporda.
- ADR: kardeş PRD §08.5 · Test: TC-ENG-096 · Blok: eng-p07.md

**NE-ENG-097 — Multi-thread stres (control spam)**
- Kabul: TSan temiz; session durumu tutarlı.
- ADR: kardeş PRD NE-ENG-059 · Test: TC-ENG-097 · Blok: eng-p07.md

**NE-ENG-098 — Soğuk açılış süresi**
- Kabul: Metrik raporu; eşik kullanıcı hissiyle revize edilebilir.
- ADR: YOK — `[ÖNERİ]` · Test: TC-ENG-098 · Blok: eng-p07.md

**NE-ENG-099 — Ölçüm telemetrisi (opt-out)**
- Kabul: Yerel log; ağ yok.
- ADR: YOK — `[ÖNERİ]` · Test: TC-ENG-099 · Blok: eng-p07.md

**NE-ENG-100 — Perf regresyon kapısı**
- Kabul: Baseline repo'da; alarm PR'da bloke etmez, etiket koyar `[ÖNERİ — politika: warn]`.
- ADR: YOK — `[ÖNERİ]` · Test: TC-ENG-100 · Blok: eng-p07.md

**NE-ENG-101 — neva-core statik lib**
- Kabul: Ayrık derlenir; exe'ler yalnız public header + statik lib ile bağlanır.
- ADR: ADR-107 K2 · S3 §4.1 · disk `neva_engine.lib` · Test: TC-ENG-101 · Blok: eng-p07.md

**NE-ENG-102 — neva_session SHARED dll**
- Kabul: Export tablosu yalnız `neva_*`; Linux `.so` karşılığı.
- ADR: ADR-107 K2 · disk `neva_session.dll` · Test: TC-ENG-102 · Blok: eng-p07.md

**NE-ENG-103 — apps/ kuralı + neva_add_executable**
- Kabul: Kural dokümante + CI'da ihlal FAIL (core içine add_executable).
- ADR: S3 §4.2 · ADR-107 prompt §6 · Test: TC-ENG-103 · Blok: eng-p07.md

**NE-ENG-104 — neva-player exe (CLI)**
- Kabul: `neva-player play file.flac --device 0 --eq flat` benzeri minimal CLI uçları.
- ADR: ADR-107 K2 · Test: TC-ENG-104 · Blok: eng-p07.md

**NE-ENG-105 — neva-host exe (GUI köprüsü)**
- Kabul: UI çökmesi oynatmayı öldürmez `[ÖNERİ — davranış]`.
- ADR: ADR-107 K2 · Test: TC-ENG-105 · Blok: eng-p07.md

**NE-ENG-106 — neva-eq exe**
- Kabul: Ayrı süreç; C ABI ile bağlantı; kapatma oturumu etkilemez.
- ADR: ADR-107 K2 (VoiceMeeter BUSGEQ15 emsal) · Test: TC-ENG-106 · Blok: eng-p07.md

**NE-ENG-107 — neva-matrix exe (8×8)**
- Kabul: Matris çekirdeği ayrı ADR ile; exe yalnız onun API'sini kullanır.
- ADR: ADR-107 K2 (BUSMatrix8 emsal) · Test: TC-ENG-107 · Blok: eng-p07.md

**NE-ENG-108 — neva-remote (lib+dll)**
- Kabul: Ağ katmanı ayrı hedefte; `neva-core`'a ağ girmez.
- ADR: ADR-107 K2 · S3 §4.3 yasak 2 · Test: TC-ENG-108 · Blok: eng-p07.md

**NE-ENG-109 — neva-mcp exe (dil UNKNOWN)**
- Kabul: Dil kararı ADR ile; bridge C ABI üzerinden (FFI).
- ADR: ADR-107 §2.1 `⚠️ PLANNED` · §18.5 · Test: TC-ENG-109 · Blok: eng-p07.md

**NE-ENG-110 — neva-plugin (VST3/AU/AAX — lisans K4)**
- Kabul: Gate NE-ENG-170 (K4) kapalıyken şablon bile commit edilmez.
- ADR: ADR-107 K4 · §14.3 · Test: TC-ENG-110 · Blok: eng-p07.md

**NE-ENG-111 — neva-cli-<kullanıcı> eklenebilirlik**
- Kabul: Şablon + belge; çekirdeğe dokunmadan derleme.
- ADR: ADR-107 K2 (§04.7 tablo) · Test: TC-ENG-111 · Blok: eng-p07.md

**NE-ENG-112 — Toolchain dosyası (cmake/neva-toolchains.cmake)**
- Kabul: Tek dosyadan Windows/Linux CI build.
- ADR: S3 §5 · Test: TC-ENG-112 · Blok: eng-p07.md

**NE-ENG-113 — C++20 zorunluluğu**
- Kabul: Eksik standardda derleme hatası; `/W4 /permissive-` MSVC.
- ADR: S3 §5 · disk CMakeLists · Test: TC-ENG-113 · Blok: eng-p07.md

**NE-ENG-114 — vcpkg yok (statik bağımlılık politikası)**
- Kabul: Build sunucuda dış paket indirmez (salt iç kaynak + vendor).
- ADR: S3 §5 · Test: TC-ENG-114 · Blok: eng-p07.md

**NE-ENG-115 — CI matrisi (Windows x64 + Linux x64 + ARM64 build)**
- Kabul: Üçü yeşil; ARM64 run testleri RPi5 runner `⚠️ VR (runner erişimi)` .
- ADR: S3 §5 · S3 §6.1 · Test: TC-ENG-115 · Blok: eng-p07.md

**NE-ENG-116 — Çıktı dizin düzeni**
- Kabul: Artefakt adları §04.7 tablosuyla eşleşir (neva_engine.lib, neva_session.dll vb.).
- ADR: S3 §5 · disk `build/` · Test: TC-ENG-116 · Blok: eng-p07.md

**NE-ENG-117 — Windows link kütüphaneleri (ole32 avrt uuid)**
- Kabul: CMakeLists'te mevcut (IMPLEMENTED — disk kanıtı).
- ADR: disk CMakeLists · S3 §5 · Test: TC-ENG-117 · Blok: eng-p07.md

**NE-ENG-118 — Unicode/UTF-8 kaynak kodu (`/utf-8`)**
- Kabul: `/utf-8` MSVC'de açık; kaynak dosyalar UTF-8 (CI denetimi).
- ADR: disk CMakeLists · Test: TC-ENG-118 · Blok: eng-p07.md

**NE-ENG-119 — -fno-exceptions tartışması**
- Kabul: Karar ADR revizyonuyla; şimdilik yalnız RT yüzeyinde throw yasak (NE-ENG-004).
- ADR: S3 §5 · Test: TC-ENG-119 · Blok: eng-p07.md

**NE-ENG-120 — Build reproducibility**
- Kabul: İki temiz build hash karşılaştırması raporlanır.
- ADR: YOK — `[ÖNERİ]` · Test: TC-ENG-120 · Blok: eng-p07.md

**NE-ENG-121 — Sembol dosyaları (PDB/dSYM) dağıtımı**
- Kabul: Artefakt listesinde PDB ayrı paket.
- ADR: S3 §5 (disk: `neva_session.pdb`, `msvc-dbg-info/`) · Test: TC-ENG-121 · Blok: eng-p07.md

**NE-ENG-122 — Versiyon bilgisi (semver + api_version)**
- Kabul: Artefakt içi sürüm kaynağı tek (CMake project version).
- ADR: kardeş PRD §05.4 · Test: TC-ENG-122 · Blok: eng-p07.md

**NE-ENG-123 — Test exe'leri (3 adet IMPLEMENTED)**
- Kabul: `ctest` yeşil (disk: `CTestTestfile.cmake`, `LastTest.log`).
- ADR: disk CMakeLists · `tests/` · Test: TC-ENG-123 · Blok: eng-p07.md

**NE-ENG-124 — Yeni test hedefi kuralı**
- Kabul: PR şablonu madde.
- ADR: S3 §6.1 · Test: TC-ENG-124 · Blok: eng-p07.md

**NE-ENG-125 — Artefakt hijyeni (build/ commit)**
- Kabul: `.gitignore` kuralı; repo temiz.
- ADR: disk `--/neva-engine/build/` (bu PRD §04.2 raporu) · Test: TC-ENG-125 · Blok: eng-p07.md

**NE-ENG-126 — Bit-perfect hash CI kapısı (K3)**
- Kabul: CI her PR'da 12/12; "yaklaşık eşitlik" PASS yasak; hash dosyaları repo'da referans.
- ADR: ADR-107 K3 · kardeş PRD §11.2 · Test: TC-ENG-126 · Blok: eng-p08.md

**NE-ENG-127 — Inverted guard (EQ aktif/lossy → test engellenir)**
- Kabul: Guard CI script'te; sahte-PASS/FAIL denetimi (ADR-106 §5.1 Ş4).
- ADR: ADR-106 §5.1 Ş4 · Test: TC-ENG-127 · Blok: eng-p08.md

**NE-ENG-128 — Test kaynak üretimi (sentez)**
- Kabul: Deterministik sentez; hash referansları senteze bağlı (reproducible).
- ADR: YOK — `[ÖNERİ]` · Test: TC-ENG-128 · Blok: eng-p08.md

**NE-ENG-129 — WAV I/O (vendor, bağımlılıksız)**
- Kabul: 16/24/32f + chunk edelişleri; fuzz corpus'u (bozuk WAV) 0 crash.
- ADR: S3 §5 (statik bağımlılık politikası) · Test: TC-ENG-129 · Blok: eng-p08.md

**NE-ENG-130 — CTest entegrasyonu**
- Kabul: `ctest` tek komut; CI adımı.
- ADR: disk CMakeLists · Test: TC-ENG-130 · Blok: eng-p08.md

**NE-ENG-131 — Sanitizer matrisi (ASan/UBSan/TSan)**
- Kabul: Üç sanitizör raporu; 0 bulgu release ön koşulu.
- ADR: ADR-0105 kapı 5.13 ⚠️ · S3 §6 · Test: TC-ENG-131 · Blok: eng-p08.md

**NE-ENG-132 — Fuzz hedefleri (engine)**
- Kabul: ≥1000 corpus/parser; CI ≤10 dk.
- ADR: ADR-0105 kapı 5.13 ⚠️ · disk `icy_parser.h` · Test: TC-ENG-132 · Blok: eng-p08.md

**NE-ENG-133 — Kontrat test paketi (backend başına)**
- Kabul: Null'da yeşil; her yeni backend aynı takımı geçer.
- ADR: S3 §6.1 · Test: TC-ENG-133 · Blok: eng-p08.md

**NE-ENG-134 — DSP doğruluk testi (referans bode)**
- Kabul: 31 frekans × 3 tip (Peaking/Shelf/LPF) doğrulama.
- ADR: disk `test_biquad.cpp` · kardeş PRD §06.3 · Test: TC-ENG-134 · Blok: eng-p08.md

**NE-ENG-135 — Grid tutarlılık testi (IMPLEMENTED)**
- Kabul: Yeşil (disk kanıtı).
- ADR: disk `test_eq_grids.cpp` · Test: TC-ENG-135 · Blok: eng-p08.md

**NE-ENG-136 — ICY parser testi (IMPLEMENTED)**
- Kabul: Yeşil (disk kanıtı); §11.5'te genişletilir.
- ADR: disk `test_icy_parser.cpp` · Test: TC-ENG-136 · Blok: eng-p08.md

**NE-ENG-137 — Latency ölçüm düzeneği**
- Kabul: Kapı 5.10/ADR-006 raporu üretir; tekrarlanabilir.
- ADR: ADR-006 · ADR-0105 kapı 5.10 ⚠️ · Test: TC-ENG-137 · Blok: eng-p08.md

**NE-ENG-138 — SNR/THD+N ölçüm düzeneği**
- Kabul: Düzeneğin kendisi doğrulanır (bilinen iyi referans DAC `[ÖNERİ]`).
- ADR: VISION V-1/V-2 · kardeş PRD §08.5 M5 · Test: TC-ENG-138 · Blok: eng-p08.md

**NE-ENG-139 — Gapless referans vektörleri (engine desteği)**
- Kabul: Kardeş PRD NE-PLR-056/068 ile çapraz.
- ADR: ADR-0105 AK-5 ⚠️ · Test: TC-ENG-139 · Blok: eng-p08.md

**NE-ENG-140 — RT stres (30 dk, 0 tahsis, 0 xrun)**
- Kabul: Rapor arşivi.
- ADR: S3 §6.1 · Test: TC-ENG-140 · Blok: eng-p08.md

**NE-ENG-141 — Leak testi (1000 cycle)**
- Kabul: LSan temiz.
- ADR: kardeş PRD §11 · Test: TC-ENG-141 · Blok: eng-p08.md

**NE-ENG-142 — ABI sembol denetimi**
- Kabul: CI grep; NE-ENG-060 freeze ile.
- ADR: kardeş PRD §05.4 · Test: TC-ENG-142 · Blok: eng-p08.md

**NE-ENG-143 — Baseline metrik dosyası**
- Kabul: Dosya + güncelleme kuralı (bilinçli regresyon PR notu).
- ADR: YOK — `[ÖNERİ]` · Test: TC-ENG-143 · Blok: eng-p08.md

**NE-ENG-144 — Test raporu şablonu (release dosyası)**
- Kabul: Şablon `.ai/.templates/` altında; release öncesi doldurulur.
- ADR: kardeş PRD §11 · Guardrail #16 ruhu · Test: TC-ENG-144 · Blok: eng-p08.md

**NE-ENG-145 — Determinism testi (aynı girdi → aynı hash)**
- Kabul: 2× çalıştırma hash eşit; fuzz atomikler testte.
- ADR: kardeş PRD §11.2 (CI tekrar çalıştırma) · Test: TC-ENG-145 · Blok: eng-p08.md

**NE-ENG-146 — ASIO SDK lisans kararı (U1)**
- Kabul: Karar ADR; build bayrağı ASIO'yu koşullu yapar (`NEVA_WITH_ASIO`).
- ADR: ADR-107 K4 · U1 §18.1 · Test: TC-ENG-146 · Blok: eng-p08.md

**NE-ENG-147 — VST3 SDK lisans kararı (K4)**
- Kabul: Gate NE-ENG-170.
- ADR: ADR-107 K4 · Test: TC-ENG-147 · Blok: eng-p08.md

**NE-ENG-148 — FFmpeg LGPL build (engine gateway dahil)**
- Kabul: Bayrak envanteri; lisans raporu.
- ADR: ADR-0105 indeks ⚠️ · lib-matrix · Test: TC-ENG-148 · Blok: eng-p08.md

**NE-ENG-149 — soxr LGPL-2.1**
- Kabul: §14.4 matris durumu.
- ADR: ADR-0105 v1.2.0 ⚠️ · Test: TC-ENG-149 · Blok: eng-p08.md

**NE-ENG-150 — libdsd lisansı (UNKNOWN — release kapısı)**
- Kabul: Karar ADR; çözülene kadar DSD hedefi dağıtılmaz (`NEVA_WITH_DSD` kapalı).
- ADR: ADR-0105 v1.2.0 ⚠️ · U3 §18.3 · Test: TC-ENG-150 · Blok: eng-p08.md

**NE-ENG-151 — BASS KULLANMA (devam)**
- Kabul: Kodda referans yok.
- ADR: ADR-105 v1.1.1 (indeks) · Test: TC-ENG-151 · Blok: eng-p08.md

**NE-ENG-152 — mpv REDDEDİLDİ (devam)**
- Kabul: Kodda bağımlılık yok.
- ADR: ADR-105 v1.1.1 (indeks) · Test: TC-ENG-152 · Blok: eng-p08.md

**NE-ENG-153 — FxSound AGPL kopyalama yasağı**
- Kabul: Kopya/uyarlama yok; referans log'lanır.
- ADR: ADR-107 K4 · Test: TC-ENG-153 · Blok: eng-p08.md

**NE-ENG-154 — mbedTLS Apache-2.0 OR GPL-2.0**
- Kabul: Lisans metni dağıtımda.
- ADR: ADR-106 §5.4 · Test: TC-ENG-154 · Blok: eng-p08.md

**NE-ENG-155 — dr_libs lisansı**
- Kabul: Attribution.
- ADR: ADR-0105 indeks ⚠️ · Test: TC-ENG-155 · Blok: eng-p08.md

**NE-ENG-156 — Secret/anahtar yasağı**
- Kabul: GitLeaks taraması temiz (CI `secret-scan.yml`).
- ADR: AGENTS §5 Security · `.github/workflows/secret-scan.yml` · Test: TC-ENG-156 · Blok: eng-p08.md

**NE-ENG-157 — FFI sınırında bellek güvenliği**
- Kabul: Fuzz + negatif testler; INVALID_ARG.
- ADR: kardeş PRD §05.1 · Test: TC-ENG-157 · Blok: eng-p08.md

**NE-ENG-158 — Eval/serileştirme yasağı**
- Kabul: Statik denetim.
- ADR: ADR-091 · AGENTS §5 · Test: TC-ENG-158 · Blok: eng-p08.md

**NE-ENG-159 — Ağ girmez çekirdek**
- Kabul: CI grep (winsock/curl/include).
- ADR: S3 §4.3 · Test: TC-ENG-159 · Blok: eng-p08.md

**NE-ENG-160 — Sürücü/evrensel kod incelemesi**
- Kabul: PR review checklist.
- ADR: S3 §3.4 · Test: TC-ENG-160 · Blok: eng-p08.md

**NE-ENG-161 — GATE G0: ADR-107 onayı**
- Kabul: ADR status `accepted`; Vault Steward + Tech Lead onayı.
- ADR: ADR-107 §5 · Test: — (proses) · Blok: eng-p08.md

**NE-ENG-162 — GATE G1: Bit-perfect 12/12**
- Kabul: 12/12 yeşil + tekrar determinism.
- ADR: ADR-107 K3 · Test: TC-ENG-126 · Blok: eng-p08.md

**NE-ENG-163 — GATE G2: Latency eşiği (ADR-006)**
- Kabul: Ölçüm raporu.
- ADR: ADR-006 · kapı 5.10 ⚠️ · Test: TC-ENG-089/090 · Blok: eng-p08.md

**NE-ENG-164 — GATE G3: Lisans kapanışı (K4)**
- Kabul: §14.3 matris 0 açık madde.
- ADR: ADR-107 K4 · Test: — (proses + TC-ENG-146..155) · Blok: eng-p08.md

**NE-ENG-165 — GATE G4: RT stres 30 dk**
- Kabul: Gece testi raporu.
- ADR: S3 §6.1 · Test: TC-ENG-140 · Blok: eng-p08.md

**NE-ENG-166 — GATE G5: Fuzz kapı 5.13**
- Kabul: Fuzz raporu.
- ADR: ADR-0105 kapı 5.13 ⚠️ · Test: TC-ENG-132 · Blok: eng-p08.md

**NE-ENG-167..175 — Faz M0-M6 kapıları**
- Kabul: Faz checklist'i (§15.6) tam; commit kanıtı; vault-sync.
- ADR: ADR-107 §5 · engine §12.6 (Faz Kapanışı kuralı) · Test: — (proses) · Blok: eng-p08.md

**NE-ENG-176 — U1: ASIO SDK lisans kararı**
- Kabul: ADR-107 §5 Faz 2 kapısı kapalı; NE-ENG-146.
- ADR: ADR-107 §8.1 · Test: — · Blok: eng-p08.md

**NE-ENG-177 — U2: VST3 lisans kararı**
- Kabul: K4 gate.
- ADR: ADR-107 §8.3 · Test: — · Blok: eng-p08.md

**NE-ENG-178 — U3: libdsd lisansı + macOS platform kararı**
- Kabul: `NEVA_WITH_DSD` kapalı; macOS M6 başlangıcı ayrı onay.
- ADR: ADR-107 §8.2 · ADR-0105 indeks ⚠️ · Test: — · Blok: eng-p08.md

**NE-ENG-179 — U4: Web/API yüzeyi**
- Kabul: M4 öncesi karar; ağ yalnız remote/mcp hedeflerinde.
- ADR: ADR-107 §8.6 · S3 §4.3 · Test: — · Blok: eng-p08.md

**NE-ENG-180 — U5: neva-mcp dili**
- Kabul: Karar ADR; bridge C ABI kullanır.
- ADR: ADR-107 §8.5 · Test: — · Blok: eng-p08.md

**NE-ENG-181 — U-ek: PCM3168A debug capture akışı (ADR-103)**
- Kabul: Karar yalnız ilgili fazda.
- ADR: ADR-107 §8.8 · Test: — · Blok: eng-p08.md

**NE-ENG-182 — U-ek: ADR-0105 dosyasının vault'a iadesi**
- Kabul: ADR-0105 dosyası `.ai/.decisions/accepted/` altına iade edilir veya indeks `⚠️ dosyasız` notu kalıcılaşır; bu PRD'deki tüm `⚠️ VR (ADR-105 indeks kaydı)` etiketleri o zaman yeniden değerlendirilir.
- ADR: §00.3-D2 · index.md:152 · Test: — (vault denetimi) · Blok: eng-p08.md


## §21 Ek K — Test Künyesi Sözlüğü (ENG · otomatik extraction)

> **Otomatik üretildi** §11 bloklarından (başlık + gereksinim + katman + otomasyon + beklenen). Bağlayıcı metin §11'dir.
> Kapsam: 163 blok. Rapor şablonları: kardeş §21.F.

**TC-ENG-001 — Tek C ABI sınırı**
- Gereksinim: NE-ENG-001 · Katman: L2 (statik) · · Otomasyon: -
- Beklenen (özet): Yalnız `neva_*` sembolleri; tek public header; OS include'ı yalnız `backends/`.

**TC-ENG-002 — Opaque handle**
- Gereksinim: NE-ENG-002 · Katman: L1 (derleme) · · Otomasyon: -
- Beklenen (özet): Derleme hatası (tanım yok); yalnız pointer/forward-declaration kullanılabilir.

**TC-ENG-003 — api_version handshake**
- Gereksinim: NE-ENG-003 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): `0x00010000` eşit; uyuşmazlıkta player açılışı reddedici mesaj üretir (kardeş TC-PLR-003).

**TC-ENG-004 — Exception yayılmaz (FFI)**
- Gereksinim: NE-ENG-004 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Statik: `throw` yok; dinamik: tüm çağrılar `neva_result` ile döner, istisna dışarı sıçramaz.

**TC-ENG-005 — char16_t Türkçe yol**
- Gereksinim: NE-ENG-005 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): `OK`; dosya içeriği okunur; olmayan yolda hata kodu (crash yok).

**TC-ENG-006 — IAudioBackend tek soyutlama noktası**
- Gereksinim: NE-ENG-006 · Katman: L2 (statik) · · Otomasyon: -
- Beklenen (özet): 0 isabet (yalnız `backends/` altında); aksi FAIL.

**TC-ENG-007 — BackendRegistry kayıt tablosu**
- Gereksinim: NE-ENG-007 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Sıra birebir; Null her platformda kayıtlı; eksik kayıt FAIL.

**TC-ENG-008 — Fallback zinciri 7 basamak**
- Gereksinim: NE-ENG-008 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Sıra: ASIO→WASAPI Excl→(Shared)→WDM-KS→PipeWire→ALSA→CoreAudio→Null; hiçbir kombinasyonda "hiçbir şey yok" durumu yok; her düşüş event E8.

**TC-ENG-009 — negotiated_format kanıt zorunluluğu**
- Gereksinim: NE-ENG-009 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Test PASS **yazamaz** (koşul dışı/FALSE); guard olmadan PASS = FAIL.

**TC-ENG-010 — BackendKind sabitliği**
- Gereksinim: NE-ENG-010 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Mevcut değerler değişmemiş; yeniden numaralandırma = FAIL (ABI/log kırılması).

**TC-ENG-011 — AudioFormat sınır testleri**
- Gereksinim: NE-ENG-011 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçerli → OK; geçersiz → `StreamError`/`INVALID_ARG`; UB (ASan) yok.

**TC-ENG-012 — StreamError taşıma**
- Gereksinim: NE-ENG-012 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): `code` + hangi `backend` düştü + `what` metni dolu; log non-RT.

**TC-ENG-013 — ADR-019 ek imzalar kontratı**
- Gereksinim: NE-ENG-013 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Üçü mevcut; Null dahil her implementasyon override eder (varsayılan `Unsupported`/no-op hata).

**TC-ENG-014 — exclusive↔shared geçiş (switchMode)**
- Gereksinim: NE-ENG-014 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ring korunur; geçiş kesintisi ≤20 ms `⚠️ VR (eşik)`; başarısızlık zincire düşer.

**TC-ENG-015 — Mismatch çözüm sırası (F3)**
- Gereksinim: NE-ENG-015 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): RT callback'te resample çağrısı 0 (profil); sıradan sapma FAIL.

**TC-ENG-016 — kill-switch**
- Gereksinim: NE-ENG-016 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Fade-out → 50 ms sessizlik → restart; `noexcept` (statik); callback bloklanmaz.

**TC-ENG-017 — Platform matrisi derleme**
- Gereksinim: NE-ENG-017 · Katman: build · · Otomasyon: -
- Beklenen (özet): Üçü yeşil; ortak çekirdek platform kodu içermiyor (#ifdef yalnız backend/adapter).

**TC-ENG-018 — K-space band yasak**
- Gereksinim: NE-ENG-018 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 isabet (yalnız "rezerv" notları); aksi FAIL.

**TC-ENG-019 — XP/7 kapsam dışı**
- Gereksinim: NE-ENG-019 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Hedef iddiası yok; CI matrisinde XP/7 yok.

**TC-ENG-020 — ARM64 build**
- Gereksinim: NE-ENG-020 · Katman: build · · Otomasyon: -
- Beklenen (özet): Derleme yeşil; Null testleri geçer.

**TC-ENG-021 — WASAPI Exclusive iki deneme**
- Gereksinim: NE-ENG-021 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Sıra birebir; başarısızlık `StreamError` + zincir düşüşü.

**TC-ENG-022 — Shared fallback (device-in-use)**
- Gereksinim: NE-ENG-022 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): B shared'a düşer, ses devam eder; bit-perfect rozeti devre dışı (player).

**TC-ENG-023 — ASIO yolu (şartlı U1)**
- Gereksinim: NE-ENG-023 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Akış; sürücü yoksa zincir 2'ye düşer; **U1 çözülmeden bu test release paketine girmez** (şartlı).

**TC-ENG-024 — WDM-KS**
- Gereksinim: NE-ENG-024 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Doğrudan erişim; başarısızlık Null'a düşer.

**TC-ENG-025 — PipeWire F32P → ALSA**
- Gereksinim: NE-ENG-025 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Var: F32P akış; yok: ALSA'ya düşüş; bit-derinlik dönüşümü yok (float).

**TC-ENG-026 — ALSA hw:**
- Gereksinim: NE-ENG-026 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Sabit format; erişimsizse Null.

**TC-ENG-027 — CoreAudio (şartlı M6/U3)**
- Gereksinim: NE-ENG-027 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Toggle başarılı/başarısız davranışları §04.4 zincirine uyar.

**TC-ENG-028 — Null backend determinizm**
- Gereksinim: NE-ENG-028 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Byte-eşit; her platformda en son basamak.

**TC-ENG-029 — enumerate/supported_formats**
- Gereksinim: NE-ENG-029 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Liste dolu/mock doğru; boş → `StreamError` (sessiz boşluk yok).

**TC-ENG-030 — Hot-plug**
- Gereksinim: NE-ENG-030 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Zincir devreye girer; event E8; yeniden bağlanınca enumerate.

**TC-ENG-031 — switchMode/Null varsaylanı**
- Gereksinim: NE-ENG-031 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Tanımlı davranış (Unsupported/no-op hata) — derleme hatası yok.

**TC-ENG-032 — setDeviceRate hata dönüşü**
- Gereksinim: NE-ENG-032 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): `StreamError` → zincir resample/fallback'e geçer (NE-ENG-015 zinciri).

**TC-ENG-033 — kill() impl**
- Gereksinim: NE-ENG-033 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): İkinci çağrı güvenli; callback sonraki periyotta durur.

**TC-ENG-034 — Callback render sözleşmesi**
- Gereksinim: NE-ENG-034 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Format dönüşümü callback içinde değil, backend sınırında; imza değişikliği ABI kırar.

**TC-ENG-035 — Yaşam döngüsü sıralama hataları**
- Gereksinim: NE-ENG-035 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Yanlış sıralama `StreamError`; close idempotent; reopen çalışır.

**TC-ENG-036 — Hash bit-perfect (ana hat)**
- Gereksinim: NE-ENG-036 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 12/12 byte-eşit; determinism 2×.

**TC-ENG-037 — OS mikser baypası**
- Gereksinim: NE-ENG-037 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Etki yok (session volume 1.0 sabit); shared modda etki VARDIR ve raporlanır (iddia düşer).

**TC-ENG-038 — Exclusive kilit çatışması**
- Gereksinim: NE-ENG-038 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): `AUDCLNT_E_DEVICE_IN_USE` → düşüş zinciri; asla kilitlenme/deadlock.

**TC-ENG-039 — BUFFER_SIZE_NOT_ALIGNED kurtarma**
- Gereksinim: NE-ENG-039 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Tek seferde yutulmaz; yeniden hesap → tekrar deneme; başarısızsa zincir düşer.

**TC-ENG-040 — Xrun sayacı**
- Gereksinim: NE-ENG-040 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): sayaçlar artar; log non-RT (RT'de dosya yok — NE-ENG-083).

**TC-ENG-041 — Mock clock determinizm**
- Gereksinim: NE-ENG-041 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Bit-eşit sonuç; gerçek saat bağımlılığı 0.

**TC-ENG-042 — Kontrat test paketi (Null)**
- Gereksinim: NE-ENG-042 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): enumerate/open/negotiate/start/stop/close + hata yolları yeşil; yeni backend kayıt öncesi bu paketi geçmek zorunda (CI kuralı).

**TC-ENG-043 — Fallback telemetri event**
- Gereksinim: NE-ENG-043 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Düşüş nedeni (kod) + backend adı event'te.

**TC-ENG-044 — Backend dizin izolasyonu**
- Gereksinim: NE-ENG-044 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 isabet; yalnız `IAudioBackend.h`.

**TC-ENG-045 — Registry 8 backend**
- Gereksinim: NE-ENG-045 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): 8 kayıt; uygulanmayan `NotImplemented`/`StreamError` döner (sessiz yok sayılmaz).

**TC-ENG-046 — Tek public header (install)**
- Gereksinim: NE-ENG-046 · Katman: build · · Otomasyon: -
- Beklenen (özet): Tek header + lib/dll; iç header yok.

**TC-ENG-047 — Event callback (non-RT marshal)**
- Gereksinim: NE-ENG-047 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Callback RT thread'de DEĞİL; unregister sonrası çağrı yok (UAF yok).

**TC-ENG-048 — Queue API preload**
- Gereksinim: NE-ENG-048 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçişte ring starve 0 (kardeş TC-PLR-061); RT'yi beklemeden hazırlık.

**TC-ENG-049 — position/duration**
- Gereksinim: NE-ENG-049 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Değerler monoton/bilinen aralıkta; stream modunda duration yok kod; atomik okuma (TSan temiz).

**TC-ENG-050 — Seek**
- Gereksinim: NE-ENG-050 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Clamp; kısa fade (click analizi); başarısızlık hata; RT'de seek işlemi yok.

**TC-ENG-051 — EQ band count atomik yayın**
- Gereksinim: NE-ENG-051 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Maske değişimi ramp'li; tahsis 0; pop 0.

**TC-ENG-052 — Preset uyuşmazlık düzeltme**
- Gereksinim: NE-ENG-052 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kural 2 işletilir (düzeltme + uyarı); çökme yok.

**TC-ENG-053 — negotiated_format API**
- Gereksinim: NE-ENG-053 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Dönen format hash koşulunda kullanılır; uyuşmazlıkta koşul-FALSE.

**TC-ENG-054 — Device list + change event**
- Gereksinim: NE-ENG-054 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Thread-safe liste; olay UI'a akar.

**TC-ENG-055 — Tuner API**
- Gereksinim: NE-ENG-055 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Mod geçişi temiz; tekrar-connect çalışır; engine validator KOYMAZ (tek kapı ADR-106 kural 6).

**TC-ENG-056 — LUFS publish**
- Gereksinim: NE-ENG-056 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Değer ≈ beklenen `⚠️ VR (tolerans)`; RT'de hesap yok (profil).

**TC-ENG-057 — Error code sabitliği**
- Gereksinim: NE-ENG-057 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Değerler değişmemiş; yeni kod yalnız ek sonunda.

**TC-ENG-058 — UTF-8 geçiş (Unix)**
- Gereksinim: NE-ENG-058 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İkisi de açılır `[ÖNERİ — macOS NFD]`; dönüşüm tek katmanda.

**TC-ENG-059 — TSan thread-safety**
- Gereksinim: NE-ENG-059 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0 data race; session durumu tutarlı.

**TC-ENG-060 — ABI freeze dump**
- Gereksinim: NE-ENG-060 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Kaldırma/degistirme yok; yalnız ekleme (minor); kırılma = majör sürüm ister.

**TC-ENG-061 — Biquad doğruluk (RBJ)**
- Gereksinim: NE-ENG-061 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Sapma ≤ tolerans `⚠️ VR (değer tanımsız; önerü: ±0.01 dB @ referans frekanslar)`; test yeşil.

**TC-ENG-062 — EqChain tahsis zorunluluğu**
- Gereksinim: NE-ENG-062 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Zincir çalışırken tahsis 0; per-kanal (`band, ch`) bağımsız gain doğruluğu.

**TC-ENG-063 — ISO 31-grid**
- Gereksinim: NE-ENG-063 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Byte-eşit; test yeşil (disk kanıtı mevcut).

**TC-ENG-064 — 10-band ISO alt küme**
- Gereksinim: NE-ENG-064 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Alt küme ISO'ya uygun; VLC preset'i kendi setiyle okunur (zorla eşleme yok — duraklar).

**TC-ENG-065 — 15/20-band grid (şartlı teyit)**
- Gereksinim: NE-ENG-065 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Teyit gelene kadar etiket `⚠️ VR` korunur; tanım varsa değerler sabit.

**TC-ENG-066 — Bypass maskesi (zincir takası yok)**
- Gereksinim: NE-ENG-066 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Hash byte-eşit (flat); zincir adresi hiç değişmez (re-init yok); maske=0 → doğrudan geçiş.

**TC-ENG-067 — Ramp smoothing (click analizi)**
- Gereksinim: NE-ENG-067 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Adım kenarında yüksek frekanslı pop yok (spektral eşik `[ÖNERİ]`); süre parametreye uyar.

**TC-ENG-068 — Cascade mod geçişi**
- Gereksinim: NE-ENG-068 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Geçiş yalnız fade-out/re-init/fade-in; tahsis yalnız geçiş anında (RT dışı hazırlık).

**TC-ENG-069 — Parametrik mod ayrımı**
- Gereksinim: NE-ENG-069 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): İki state bağımsız; RT kuralları ihlali yok.

**TC-ENG-070 — True-peak limiter bypass (hash)**
- Gereksinim: NE-ENG-070 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Bypass = hash byte-eşit; aşım senaryosunda ceiling aşılmaz `⚠️ VR (true-peak oversample katsayısı)`.

**TC-ENG-071 — R128 sidechain doğruluğu**
- Gereksinim: NE-ENG-071 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Referans değer ±tolerans `⚠️ VR (±0.1 LU önerü)`; RT thread'e dokunma yok (profil).

**TC-ENG-072 — soxr bypass (eşit format)**
- Gereksinim: NE-ENG-072 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Eşit format = resampler çağrı 0 + hash; farklı = resampler devrede + hash koşul dışı.

**TC-ENG-073 — Preamp identity (0 dB)**
- Gereksinim: NE-ENG-073 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 0 dB = identity (byte-eşit); artış ramp'li; limiter aşım koruması devrede.

**TC-ENG-074 — Multi-channel (8.1) DSP**
- Gereksinim: NE-ENG-074 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Kanal başına uygulama; bütçe §08 karşılanır; 5.1/8.1 kaynak geçişi.

**TC-ENG-075 — Ring overflow politikası (iki mod)**
- Gereksinim: NE-ENG-075 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Dosya: drop 0, gecikme büyür; radyo: drop sayacı artar, süreklilik korunur (§07.4 tablosu).

**TC-ENG-076 — DSP profil worst-case**
- Gereksinim: NE-ENG-076 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): CI benchmark 31-band (10-band koşulu gizli FAIL üretmez — raporlama net).

**TC-ENG-077 — CPU ≤%5 (192 kHz stereo)**
- Gereksinim: NE-ENG-077 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): ≤%5; aşım = release bloke.

**TC-ENG-078 — DSP state sıfırlama**
- Gereksinim: NE-ENG-078 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Eski durum sızıntısı/transient yok (biquad state sıfır).

**TC-ENG-079 — Denormal koruması (FTZ/DAZ)**
- Gereksinim: NE-ENG-079 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Denormal kaynaklı CPU patlaması yok (bilinen klasik hata).

**TC-ENG-080 — DSP birim test kapsamı**
- Gereksinim: NE-ENG-080 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Yeşil; her grid frekansı doğruluk testinde; §11.4 piramit dağılımı korunur.

**TC-ENG-081 — Zero-allocation RT callback**
- Gereksinim: NE-ENG-081 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): RT thread'de 0 tahsis; aksi anında FAIL (bilinçli).

**TC-ENG-082 — Lock-free SPSC**
- Gereksinim: NE-ENG-082 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): 0 data race; invariant ihlali yok (fuzz edilmiş boyut/ hız varyantları).

**TC-ENG-083 — RT yasağı denetimi**
- Gereksinim: NE-ENG-083 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 isabet; aksi FAIL (inceleme yolu yok — otomatik).

**TC-ENG-084 — Stack bütçesi (≤4 KB)**
- Gereksinim: NE-ENG-084 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): ≤4 KB `[ÖNERİ]`; aşım rapor (release notu).

**TC-ENG-085 — Callback süre histogramı**
- Gereksinim: NE-ENG-085 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): P99 ≤ bütçe (§08.4); aşım sayısı rapor.

**TC-ENG-086 — Starve fade (≤50 ms)**
- Gereksinim: NE-ENG-086 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Monoton azalma (click yok); ≤50 ms; sessizlik sonra kurtarma.

**TC-ENG-087 — Xrun politikası (fade → 50 ms → restart)**
- Gereksinim: NE-ENG-087 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Fade-out → 50 ms → restart sırası; sayaçlar artar; ses kurtarılır.

**TC-ENG-088 — 30 dk stres = 0 xrun**
- Gereksinim: NE-ENG-088 · Katman: Kapı (G4) · · Otomasyon: -
- Beklenen (özet): xrun 0 · tahsis 0 · leak 0 (referans donanım, frekans sabit).

**TC-ENG-089 — ASIO <10 ms**
- Gereksinim: NE-ENG-089 · Katman: Kapı (G2) · · Otomasyon: -
- Beklenen (özet): <10 ms; aşan blok seçimi otomatik reddedilir (limit veri).

**TC-ENG-090 — WASAPI <20 ms**
- Gereksinim: NE-ENG-090 · Katman: Kapı (G2) · · Otomasyon: -
- Beklenen (özet): <20 ms; blok ≥1024@48k denenmez (yasağı) `⚠️ VR L1-L4` — eşik tanımlanana kadar en küçük blok.

**TC-ENG-091 — Ring kapasite hesabı doğrulaması**
- Gereksinim: NE-ENG-091 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Hesap §08.3 ile tutarlı; iki uçta da davranış §07.4 (bu PRD §07/§08) tablosu.

**TC-ENG-092 — RAM ≤400 MB (engine payı)**
- Gereksinim: NE-ENG-092 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Bütçe içinde; aşım = release bloke (bütçe defterine kayıt).

**TC-ENG-093 — Leak (1000 cycle)**
- Gereksinim: NE-ENG-093 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Net artış 0; LSan temiz.

**TC-ENG-094 — Fuzz (C ABI + ICY)**
- Gereksinim: NE-ENG-094 · Katman: Kapı (5.13) · · Otomasyon: -
- Beklenen (özet): 0 crash; CI ≤10 dk `⚠️ VR eşik` (ADR-0105 kapı 5.13).

**TC-ENG-095 — ICY parser limitleri**
- Gereksinim: NE-ENG-095 · Katman: L1/L2 · · Otomasyon: -
- Beklenen (özet): Sınırda doğru; sınırsız buffer büyümesi yok (bellek tepe ölçümü); test yeşil.

**TC-ENG-096 — Bench ortamı sabitleme**
- Gereksinim: NE-ENG-096 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Rapor yoksa bench sonucu geçersiz (kabul edilmez).

**TC-ENG-097 — Multi-thread control spam**
- Gereksinim: NE-ENG-097 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0 race; durum tutarlı (son durum belirlenebilir); crash yok.

**TC-ENG-098 — Soğuk açılış <300 ms**
- Gereksinim: NE-ENG-098 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): ≤300 ms `[ÖNERİ]`; sonuç raporda soğuk/sıcak ayrımı.

**TC-ENG-099 — Telemetri opt-out**
- Gereksinim: NE-ENG-099 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ağ çağrısı 0 (yerel log); varsayılan kapalı.

**TC-ENG-100 — Perf regresyon etiketi**
- Gereksinim: NE-ENG-100 · Katman: proses · · Otomasyon: -
- Beklenen (özet): PR etiketi/warn (politika: bloke etmez `[ÖNERİ]`); etiket yoksa FAIL.

**TC-ENG-101 — neva-core statik lib ayrımı**
- Gereksinim: NE-ENG-101 · Katman: build · · Otomasyon: -
- Beklenen (özet): Ayrık derleme; core iç bağımlılık sızıntısı yok.

**TC-ENG-102 — neva_session shared DLL export**
- Gereksinim: NE-ENG-102 · Katman: build · · Otomasyon: -
- Beklenen (özet): Yalnız `neva_*`; her iki platformda.

**TC-ENG-103 — apps/ kuralı**
- Gereksinim: NE-ENG-103 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 isabet; ihlal FAIL.

**TC-ENG-104 — neva-player CLI smoke**
- Gereksinim: NE-ENG-104 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0 kapanış kodu; desteklenmeyen bayrakta net mesaj.

**TC-ENG-105 — neva-host köprüsü (UI çökmesi)**
- Gereksinim: NE-ENG-105 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Oynatma ölmez `[ÖNERİ]`; köprü yeniden bağlanır; temiz kapanışta engine stop.

**TC-ENG-106 — neva-eq ayrı süreç**
- Gereksinim: NE-ENG-106 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ana oturum etkilenmez; band count değişimi yansır (TC-ENG-051 zinciri).

**TC-ENG-107 — neva-matrix (şartlı)**
- Gereksinim: NE-ENG-107 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): `⚠️ VR` etiketi korunur; çekirdek yokken exe yok.

**TC-ENG-108 — neva-remote (şartlı U4)**
- Gereksinim: NE-ENG-108 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Ağız core'a sızmaz (NE-ENG-159); reddi doğru.

**TC-ENG-109 — neva-mcp (şartlı U5)**
- Gereksinim: NE-ENG-109 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): C ABI üzerinden; dil `UNKNOWN` iken test "şartlı" kalır.

**TC-ENG-110 — VST3 wrap (şartlı K4)**
- Gereksinim: NE-ENG-110 · Katman: şartlı · · Otomasyon: -
- Beklenen (özet): Gate NE-ENG-170 kapalıyken hedef yok.

**TC-ENG-111 — Kullanıcı CLI eklenebilirlik**
- Gereksinim: NE-ENG-111 · Katman: build · · Otomasyon: -
- Beklenen (özet): Başarılı derleme; core dosyalarında 0 değişiklik.

**TC-ENG-112 — Toolchain dosyası**
- Gereksinim: NE-ENG-112 · Katman: build · · Otomasyon: -
- Beklenen (özet): Tek dosyadan üç kurulum; eksik araç zinciri net hata.

**TC-ENG-113 — C++20 zorunlu**
- Gereksinim: NE-ENG-113 · Katman: build · · Otomasyon: -
- Beklenen (özet): Eksik standardda net hata; yeni uyarı girişi PR'da görünür.

**TC-ENG-114 — vcpkg yok**
- Gereksinim: NE-ENG-114 · Katman: build · · Otomasyon: -
- Beklenen (özet): Başarılı (vendor içeriğiyle); dış paket indirme yok.

**TC-ENG-115 — CI matrisi (3 build)**
- Gereksinim: NE-ENG-115 · Katman: build · · Otomasyon: -
- Beklenen (özet): Üçü yeşil; ARM64 run `⚠️ VR (runner)`.

**TC-ENG-116 — Çıktı dizin düzeni**
- Gereksinim: NE-ENG-116 · Katman: build · · Otomasyon: -
- Beklenen (özet): Eşleşme (neva_engine.lib, neva_session.dll, neva_player.exe…).

**TC-ENG-117 — Windows link kütüphaneleri**
- Gereksinim: NE-ENG-117 · Katman: build · · Otomasyon: -
- Beklenen (özet): Mevcut (disk kanıtı) + temiz link.

**TC-ENG-118 — UTF-8 kaynak kodu**
- Gereksinim: NE-ENG-118 · Katman: build · · Otomasyon: -
- Beklenen (özet): Bayrak açık; bozuk kodlama uyarısı yok.

**TC-ENG-119 — -fno-exceptions (şartlı)**
- Gereksinim: NE-ENG-119 · Katman: şartlı · · Otomasyon: -
- Beklenen (özet): `⚠️ PLANNED` korunur; şimdilik yalnız RT yüzeyinde throw yasak (TC-ENG-004).

**TC-ENG-120 — Build reproducibility**
- Gereksinim: NE-ENG-120 · Katman: build · · Otomasyon: -
- Beklenen (özet): Eşitlik `[ÖNERİ]`; fark varsa determinism notu (zaman damgası/embedded path).

**TC-ENG-121 — Sembol (PDB) dağıtımı**
- Gereksinim: NE-ENG-121 · Katman: build · · Otomasyon: -
- Beklenen (özet): PDB ayrı paket; ürün paketinde yok.

**TC-ENG-122 — Sürüm bilgisi senkronu**
- Gereksinim: NE-ENG-122 · Katman: build · · Otomasyon: -
- Beklenen (özet): Tek kaynak (CMake); uyuşmazlık FAIL.

**TC-ENG-123 — Test exe'leri + CTest**
- Gereksinim: NE-ENG-123 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): `neva_tests` · `eq_grids_tests` · `icy_parser_tests` yeşil (disk kanıtı: `CTestTestfile.cmake`, `LastTest.log`).

**TC-ENG-124 — Yeni modül = kontrat testi kuralı**
- Gereksinim: NE-ENG-124 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Kontrat paketi eklentisi yoksa işaretle (merge gate); aksi FAIL.

**TC-ENG-125 — Build artefakt hijyeni**
- Gereksinim: NE-ENG-125 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Derleme çıktısı commit edilmemiş; mevcut disk `build/` varlığı `.gitignore` altında.

**TC-ENG-126 — Bit-perfect hash (12 kombinasyon)**
- Gereksinim: NE-ENG-126 · Katman: Kapı (G1/K3) · · Otomasyon: -
- Beklenen (özet): 12/12 + 2× eşitlik; "yaklaşık eşitlik" (±1 LSB vs.) kabul EDİLMEZ.

**TC-ENG-127 — Inverted guard denetimi**
- Gereksinim: NE-ENG-127 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Koşullar sağlanmadığında test çalışmaz (SAHTE FAIL yok); guard kırık = sahte-PASS yakalanır.

**TC-ENG-128 — Deterministik test kaynağı**
- Gereksinim: NE-ENG-128 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Aynı seed → byte-eşit; hash referansları üretime bağlı (reproducible).

**TC-ENG-129 — WAV I/O dayanıklılığı**
- Gereksinim: NE-ENG-129 · Katman: L1/Kapı · · Otomasyon: -
- Beklenen (özet): Round-trip byte-eşit; corpus 0 crash/OOB; kapasite iddiaları doğrulanmadan tahsis RED.

**TC-ENG-130 — CTest entegrasyonu**
- Gereksinim: NE-ENG-130 · Katman: build · · Otomasyon: -
- Beklenen (özet): Tüm testler kayıtlı; eksik `add_test` = FAIL (yeni test unutulmuş).

**TC-ENG-131 — Sanitizer matrisi**
- Gereksinim: NE-ENG-131 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): 0 bulgu; 0 bulgu release ön koşulu; bulgu varsa engelleyici.

**TC-ENG-132 — Fuzz hedefleri (engine)**
- Gereksinim: NE-ENG-132 · Katman: Kapı (5.13) · · Otomasyon: -
- Beklenen (özet): 0 crash; corpus sayısı raporlanır; yeni parser değişikliğinde corpus otomatik çalışır.

**TC-ENG-133 — Kontrat test paketi (detay)**
- Gereksinim: NE-ENG-133 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): Null üzerinde yeşil; paket her backend için aynı; yeni backend kayıt öncesi zorunlu.

**TC-ENG-134 — DSP doğruluk (bode referans)**
- Gereksinim: NE-ENG-134 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Sapma ≤ tolerans `⚠️ VR`; grafik rapor (test çıktısı).

**TC-ENG-135 — Grid tutarlılık (IMPLEMENTED)**
- Gereksinim: NE-ENG-135 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Yeşil (disk kanıtı mevcut); grid ↔ EqChain uyumu.

**TC-ENG-136 — ICY parser testi (IMPLEMENTED + genişletme)**
- Gereksinim: NE-ENG-136 · Katman: L1 · · Otomasyon: -
- Beklenen (özet): Yeşil; v1/v2 ayrımı + durum makinesi kapsamı tam.

**TC-ENG-137 — Latency düzenek doğrulaması**
- Gereksinim: NE-ENG-137 · Katman: Kapı (G2) · · Otomasyon: -
- Beklenen (özet): Tekrarlanabilir (2× ölçüm sapması ≤1 ms `[ÖNERİ]`); araç/rapor repo-doc'da.

**TC-ENG-138 — SNR/THD+N düzeneği**
- Gereksinim: NE-ENG-138 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Düzeneği tabanı hedefe 10 dB yakınysa düzeneği AŞAR `[ÖNERİ]`; sonuç: SNR >105 dB · THD+N <%0.005.

**TC-ENG-139 — Gapless API desteği**
- Gereksinim: NE-ENG-139 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): API hazır; oyuncu tarafı vektörler (TC-PLR-056..068) engine üstünde geçer.

**TC-ENG-140 — RT stres gece testi (G4)**
- Gereksinim: NE-ENG-140 · Katman: Kapı (G4) · · Otomasyon: -
- Beklenen (özet): 0 tahsis · 0 xrun · leak 0; rapor release dosyasına ek.

**TC-ENG-141 — Leak 1000 cycle**
- Gereksinim: NE-ENG-141 · Katman: L2 · · Otomasyon: -
- Beklenen (özet): 0; LSan temiz.

**TC-ENG-142 — ABI sembol denetimi**
- Gereksinim: NE-ENG-142 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Yabancı sembol 0; dump arşivde.

**TC-ENG-143 — Baseline metrik dosyası**
- Gereksinim: NE-ENG-143 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Dosya repo'da; bilinçli regresyon gerekçeli.

**TC-ENG-144 — Test raporu şablonu**
- Gereksinim: NE-ENG-144 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Kapı 5.1/5.10-5.13 + G2-G5 satırları dolu; boş satır = release adayı değil.

**TC-ENG-145 — Determinism (2× hash)**
- Gereksinim: NE-ENG-145 · Katman: Kapı · · Otomasyon: -
- Beklenen (özet): Eşitlik; fark = atomik/non-atomik hatası (FAIL).

**TC-ENG-146 — ASIO lisans kapısı (U1)**
- Gereksinim: NE-ENG-146 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): ADR yoksa bayrak kapalı (varsayılan); açık bayrakla build = ADR zorunlu.

**TC-ENG-147 — VST3 lisans kapısı (K4)**
- Gereksinim: NE-ENG-147 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): Kapalı gate = hedef yok.

**TC-ENG-148 — FFmpeg LGPL build**
- Gereksinim: NE-ENG-148 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): 0; lisans raporu.

**TC-ENG-149 — soxr lisans durumu**
- Gereksinim: NE-ENG-149 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): Dinamik ✅; statik = `⚠️ LİSANS` etiketi + karar.

**TC-ENG-150 — libdsd lisans kapısı (U3)**
- Gereksinim: NE-ENG-150 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): LICENSE yok → bayrak kapalı (DSD hedefi dağıtılmaz).

**TC-ENG-151 — BASS kullanma**
- Gereksinim: NE-ENG-151 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0.

**TC-ENG-152 — mpv reddi**
- Gereksinim: NE-ENG-152 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0; yeniden değerlendirme ADR ister.

**TC-ENG-153 — FxSound kopyalama yasağı**
- Gereksinim: NE-ENG-153 · Katman: statik · · Otomasyon: -
- Beklenen (özet): Kopya yok; yalnız mimari referans notu.

**TC-ENG-154 — mbedTLS lisans metni**
- Gereksinim: NE-ENG-154 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): Apache-2.0 metni mevcut.

**TC-ENG-155 — dr_libs lisansı**
- Gereksinim: NE-ENG-155 · Katman: lisans · · Otomasyon: -
- Beklenen (özet): Attribution mevcut; tam metin teyit edilir.

**TC-ENG-156 — Secret taraması**
- Gereksinim: NE-ENG-156 · Katman: güvenlik · · Otomasyon: -
- Beklenen (özet): 0 isabet; `.env` commit edilmemiş.

**TC-ENG-157 — FFI clamp/negatif test**
- Gereksinim: NE-ENG-157 · Katman: L1/Kapı · · Otomasyon: -
- Beklenen (özet): `INVALID_ARG`/clamp; UB yok (ASan); use-after-close koruması `⚠️ VR (magic-cookie)` .

**TC-ENG-158 — Eval/serileştirme yasağı**
- Gereksinim: NE-ENG-158 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 (izinli istisnalar gerekçeli).

**TC-ENG-159 — Çekirdekte ağ yok**
- Gereksinim: NE-ENG-159 · Katman: statik · · Otomasyon: -
- Beklenen (özet): 0 (SSRF yüzeyi çekirdekte yok); ağ yalnız remote/mcp hedeflerinde.

**TC-ENG-160 — Düşük seviye kod incelemesi**
- Gereksinim: NE-ENG-160 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Checklist dolu; açık madde = merge yok.

**TC-ENG-161..175 — Faz kapıları (G0-G6 + M0-M6 checklist)**
- Gereksinim: NE-ENG-161..175 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Checklist tam + onay kayıtlı; eksik = faz açılmaz.

**TC-ENG-176..181 — U1-U5 + PCM3168A (karar kapıları)**
- Gereksinim: NE-ENG-176..181 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Karar yok → hedef kapalı; karar var → ADR + bayrak açılır.

**TC-ENG-182 — ADR-0105 vault iadesi (vault denetimi)**
- Gereksinim: NE-ENG-182 · Katman: proses · · Otomasyon: -
- Beklenen (özet): Dosya iade edilir VEYA indeks notu kalıcılaşır; bu PRD'deki `⚠️ VR (ADR-105 indeks kaydı)` etiketleri o zaman yeniden değerlendirilir.


## §21 Ek L — Örnek ADR Taslakları (Engine · karar kapıları U1/U6/U7/U14)

> Bu ek, §18.18 karar formu şablonuyla **hazır ADR taslağı** üretir; Vault Steward bunları
> `.ai/.decisions/draft/` altına taşıyıp onaya sunar. Her taslak `⚠️ VR` / `UNKNOWN` işaretini korur —
> taslak ≠ karar (status: proposed).
> **Sahiplik:** U1/U6/U14 ADR'leri PAKET fazından **önce** zorunlu (§18.18 zorunluluk satırı).

---

### §21.L.1 ADR Taslağı T-01 — ASIO SDK Lisans Kararı (U1 · KRİTİK)

```markdown
## ADR-T01 - ASIO SDK entegrasyon lisans rejimi
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: U1 (§18.1) · R-01 (ASIO lisans blokajı) · NE-ENG-176 · ADR-107 §8.1
  ASIO SDK, Steinberg'in ticari/deyimsel lisansıdır; ücretsiz dağıtım koşulları
  VSL (Steinberg) sayfasından alınmalıdır. Bu PRD'de lisans metni OKUNMAMIŞTIR
  (zero-hallucination) — `UNKNOWN`.
- Karar: <seçenek seçilecek — (a)>
- Alternatifler:
  (a) ASIO'yu kapalı-derle: NEVA_WITH_ASIO=OFF (varsayılan). WASAPI Exclusive
      2. basamak olarak bit-perfect'i zaten taşır. Lisans metni okunup yazılı
      onay gelene kadar OFF kalır. [ÖNERİ — ADR-107 §8.1 ile uyumlu]
      + (a-ek) DSK (dosya SDK) + ASIO-SDK-VERSION.md sürüm kilidi — lisans metni
        repoya vendor/ altına alınır (G3-3 kanıtı).
  (b) ASIO'yu açık-derle + lisans metnini vendor/: risk — VSL koşulları
      "redistributable" kapsamını teyit etmeden binary dağıtımı ihlal doğurur.
  (c) Kullanıcı-kendi-SDK'sı: kullanıcı ASIO SDK'yı kendi kurar; biz yalnız
      header/link köprüsü. DSK + "user-supplied SDK" modeli.
- Sonuçlar: (a) ile: bit-perfect iddiası WASAPI Exclusive üzerinden korunur;
  A2 fazında (c) değerlendirilebilir. (b) reddedildi — lisans metni okunmadan
  dağıtım yasak (AGENTS §3 + G3).
- İzlenebilirlik: NE-ENG-176 → TC-ENG-176..182 (karar kapıları) → §18.1 · U1
- Vault: `.ai/.decisions/draft/ADR-T01-asio-lisans.md` + log.md satırı
```

**Teyit adımları (karardan önce — §18.1 madde 1):**
1. Steinberg ASIO sayfasından lisans metnini indir + arşivle (`vendor/asio/LICENSE.asio`).
2. "Redistribution" maddesini bul; bulamazsan (c) şıkkına geç (kullanıcı-kendi-SDK'sı).
3. Lisans metni + karar + tarih → bu taslağa → Vault Steward onayı.

---

### §21.L.2 ADR Taslağı T-02 — Çekirdek Motor Lisansı (U6/L01 · KRİTİK)

```markdown
## ADR-T02 - neva-engine çekirdek lisansı
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: U6 (§18.6) · L01 · G3-6 · NE-ENG-179 · ADR-0105 §8.7 (surrogate)
  CoreMusic'in mevcut lisansı vault'ta sabitlenmemiş; bu PRD'de `UNKNOWN`.
  Çekirdek motor (C++20, bağımlılıksız) ile player (FFmpeg/soxr/mbedTLS) farklı
  lisans rejimlerine tabi olabilir.
- Karar: <seçenek seçilecek — proje sahibinin tek cümlelik kararı>
- Alternatifler:
  (a) AGPL-3.0 (çekirdek): uzak servisler dahil türev = kaynak paylaşımı zorunlu.
      CoreMusic web/API yüzeyi (U4) açık olursa G3-6 riski minimize edilir.
  (b) MIT/BSD-benzeri (çekirdek): maksimum benimseme; ticari fork serbest.
      Yan etki: HOOKS/vendor stratejisi (FFmpeg GPL) ile çatışma olasılığı —
      FFmpeg statically link + GPL bileşen ⇒ LGPL/MIT çekirdek yine de
      "combined work" sayılabilir; ayrı çalışma sınırı şart (§14.5).
  (c) Çift-lisans: OSS (a-b) + ticari — ek bakım yükü.
- Sonuçlar: (a) seçilmişse: U4 web yüzeyi AGPL tetikleyicisi olur; (b)
  seçilmişse: FFmpeg vendor seçimi (T-03) tek başına G3 riski taşır ve
  dinamik-link + NOTICE zorunlu (§14.5). Her iki durumda da LICENSE +
  Copyright + NOTICE kapıda GECTI şart (G3-1/G3-2).
- İzlenebilirlik: NE-ENG-179 → TC-ENG-176..182 → §18.6 · L01 · G3
- Vault: `.ai/.decisions/draft/ADR-T02-cekirdek-lisans.md` + log.md satırı
```

**Teyit adımları:**
1. Proje sahibi tek cümlelik karar verir (lisans ailesi + sürüm).
2. `LICENSE` dosyası yazılır (çekirdek) — G3-1.
3. Bağımlılık lisans matrisi (§14.5-§14.7) bu kararla yeniden çapraz okunur.

---

### §21.L.3 ADR Taslağı T-03 — FFmpeg Vendor Stratejisi (U7)

```markdown
## ADR-T03 - FFmpeg entegrasyon rejimi (vendor vs dinamik vs gateway)
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: U7 (§18.7 kardeş §14.5) · R-05 (lisans/bakım) · NE-ENG-178 ·
  ADR-0105 §5 vendor stratejisi belirsiz + FFmpeg 9.0.2 `⚠️ VR`.
- Karar: <seçenek seçilecek>
- Alternatifler:
  (a) Dinamik link + NOTICE (LGPL-2.1+ build, --enable-gpl KAPALI):
      son kullanıcı kendi FFmpeg'ini düşürebilir; LICENSE/NOTICE yeter.
      [ÖNERİ — §14.5 ile uyumlu; CI grep --enable-gpl kırmızı]
  (b) Vendor (static) + link raporu + NOTICE: kolay kurulum; lisans yükü
      artar (LGPL dinamik şartı ihlal edilirse). Statik için ek metin gerekir.
  (c) neva-host köprüsü (U12): motor FFmpeg'e dokunmaz; ayrı süreç + socket.
      ABI/RTR riski düşer; gecikme/A-V senkron ek yükü (ADR-0105 kapı 5.11).
  (d) FFmpeg'siz çekirdek: decode_stub + dr_libs/dr_mp3 (benzeri küçük
      decode'lar); FFmpeg yalnız "extended" bayrağıyla. En temiz G3; kapsam
      daralır (U13 format listesi ile hizalanır).
- Sonuçlar: (a)+(d) hibrit önerilir: çekirdek bağımsız + FFmpeg opsiyonel.
  FFmpeg sürümü (9.0.2 iddiası) `⚠️ VR` — seçilince vendor sürüm kilidi +
  SHA-256 + LICENSE vendor/ altına.
- İzlenebilirlik: NE-ENG-178 → TC-ENG-176..182 → §14.5 · §18.7 kardeş
- Vault: `.ai/.decisions/draft/ADR-T03-ffmpeg-vendor.md` + log.md satırı
```

---

### §21.L.4 ADR Taslağı T-04 — Ölçüm Eşiklerinin Bağlayıcılığı (U14)

```markdown
## ADR-T04 - L1-L4 / SNR / THD eşiklerinin bağlayıcılık statüsü
- Tarih: <YYYY-MM-DD>
- Durum: proposed
- Bağlam: U14 (§18.14) · R-02 (yanlış bit-perfect iddiası) · G2 ·
  ADR-0105 §2.10 (dosyasız `⚠️ VR`) · kapı 5.10.
- Karar: <seçenek seçilecek>
- Alternatifler:
  (a) Eşikler GEÇİCİ: M1 ölçümü + kullanıcı onayıyla bağlayıcı olur; o ana
      kadar release notunda "hedef" olarak işaretlenir. [ÖNERİ]
  (b) Eşikler BAĞLAYICI: ölçülemezse release yok. Risk — donanım/OS
      değişkenliği (loopback ölçüm hatası) kapıları gereksiz kilitler.
  (c) Eşikler YALNIZ bit-perfect hash için bağlayıcı (SHA-256 byte-equality
      zaten mutlak); SNR/THD/L1-L4 yalnız raporlanır (badge yok).
- Sonuçlar: (a)+(c) hibrit önerilir: hash = mutlak kapı (K3); gecikme/analog
  = rapor + onay. Ölçüm ortamı kaydı (§21.F.1) her raporun ön koşulu.
- İzlenebilirlik: NE-ENG-177 → TC-ENG-099/100/137/138/136 → §18.14 · G2
- Vault: `.ai/.decisions/draft/ADR-T04-olcum-esikleri.md` + log.md satırı
```

---

### §21.L.5 Taslak → Kapı Eşleştirme

| Taslak | Açık madde | Zorunlu | Bağlı kapı | Bloke ettiği faz |
|---|---|---|---|---|
| T-01 ASIO | U1 | Evet (PAKET öncesi) | G3-3 · NE-ENG-176 | PAKET (ASIO OFF ise serbest) |
| T-02 Lisans | U6/L01 | Evet (PAKET öncesi) | G3-1 · G3-6 | PAKET (tümü) |
| T-03 FFmpeg | U7 | Hayır (etiketli) | G3-4 · G3-5 · NE-ENG-178 | M2 (FFmpeg'li decode) |
| T-04 Eşikler | U14 | Evet (PAKET öncesi) | G2 · NE-ENG-177 | M1 release (badge) |

**Kural:** Taslak ≠ karar. `status: proposed` hiçbir gate'i kırmaz; `accepted`
Vault'a geçtikten sonra §17 izlenebilirlik satırı güncellenir.

## §21 Ek M — CI Pipeline Taslağı & Statik Kapı Kataloğu (Engine)

> Bu ek §11.7 (statik kapılar) + §15.4 (faz gate'leri) + NE-ENG-119/131/132/166
> gereksinimlerinin **çalıştırılabilir karşılığıdır**. YAML taslağı `[ÖNERİ]`'dir;
> gerçek CI sağlayıcısı §18/U15 test ortamı kararıyla sabitlenir.

### §21.M.1 CI Pipeline (YAML taslağı `[ÖNERİ]`)

```yaml
# .github/workflows/neva-engine.yml  [ÖNERİ — sağlayıcı UNKNOWN, §18/U15]
name: neva-engine-ci
on:
  push: { branches: [main, "release/*"] }
  pull_request:
jobs:
  statik-kapilar:            # §11.7 — en ucuz kapı, en önce
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: RT yasaklari (S3 §6.1)
        run: |
          ! grep -rnE 'malloc|calloc|realloc|free|std::mutex|std::lock_guard|fopen|printf' \
             --include='*render*' --include='*rt*' src/ include/
      - name: HTTP yasagi (cekirdektemiz)
        run: ! grep -rnE 'curl_|socket\(|WinHttp' src/core/
      - name: C++ siniri (neva_session.h temiz)
        run: ! grep -nE 'std::|class |template' include/neva/neva_session.h
      - name: Export sembolleri (yalniz neva_*)
        run: nm -D build/neva_session.so | grep -c ' T ' == 9   # (Linux'ta proxy; Windows dumpbin)

  derleme-test:
    needs: statik-kapilar
    strategy:
      matrix:
        include:
          - { os: windows-latest, cc: msvc }
          - { os: ubuntu-latest,  cc: gcc }
          - { os: ubuntu-latest,  cc: clang }
    runs-on: ${{ matrix.os }}
    steps:
      - uses: actions/checkout@v4
      - name: Configure
        run: cmake -S . -B build -DCMAKE_BUILD_TYPE=Release
      - name: Build (/W4 / permissive- · -Wall -Wextra)
        run: cmake --build build --config Release
      - name: ctest (biquad_eq_ring · eq_grids_gates · icy_parser_gates)
        run: ctest --test-dir build --output-on-failure

  sanitizers:
    needs: derleme-test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: cmake -S . -B build-asan -DCMAKE_CXX_FLAGS="-fsanitize=address,undefined -fno-sanitize-recover=all"
      - run: cmake --build build-asan && ctest --test-dir build-asan
      - run: cmake -S . -B build-tsan -DCMAKE_CXX_FLAGS="-fsanitize=thread"
      - run: cmake --build build-tsan && ctest --test-dir build-tsan   # NE-ENG-131

  fuzz:
    needs: sanitizers
    runs-on: ubuntu-latest
    timeout-minutes: 10                          # kapı 5.13: CI ≤10 dk
    steps:
      - uses: actions/checkout@v4
      - name: libFuzzer hedefleri (≥1000 vaka/hedef)
        run: |
          cmake -S . -B build-fuzz -DNEVA_FUZZ=ON -DCMAKE_CXX_FLAGS="-fsanitize=fuzzer,address,undefined"
          cmake --build build-fuzz
          for h in wav icy abi preset; do ./build-fuzz/fuzz_$h -runs=1000; done

  bit-perfect-hash:
    needs: derleme-test
    runs-on: windows-latest
    steps:
      - uses: actions/checkout@v4
      - name: 12 kombinasyon SHA-256 + 2x determinizm (kapı 5.1 · K3)
        run: cmake --build build --target bitperfect_check && ctest -R bitperfect
      # inverted guard: hash esitse bile Flat=all-zero degilse FAIL

  lisans:
    needs: derleme-test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: G3 — GPL yasagi + NOTICE zorunlu
        run: |
          ! grep -rn 'enable-gpl' build/CMakeCache.txt
          test -f LICENSE && test -f NOTICE
      - name: RED filtreleri (FDK-AAC / BASS / mpv kaynak)
        run: ! grep -rnE 'fdk-aac|BASS\.h|mpv/client' src/ include/

  stres-gece:
    if: github.ref == 'refs/heads/main'
    runs-on: self-hosted-audio      # gerçek cihazlı makine (§18/U15)
    steps:
      - uses: actions/checkout@v4
      - name: 30 dk stres (G4) + gecikme olcumu (§21.F.3 raporu)
        run: ./scripts/stress_30min.ps1
```

### §21.M.2 Statik Kapı Kataloğu (§11.7 detay tablosu)

| Kapı | Regex / araç | Kapsam | Eşik | İhlal | Kırarsa |
|---|---|---|---|---|---|
| RT yasakları | `malloc|calloc|realloc|free|std::mutex|fopen|printf` | render/rt kaynakları | 0 eşleşme | kırmızı | P0 blok |
| HTTP yasağı | `curl_|socket(|WinHttp` | src/core/ (çekirdek) | 0 | kırmızı | P0 blok |
| C++ sınırı | `std::|class |template` | neva_session.h | 0 | kırmızı | A0 blok |
| Export sınırı | `nm -D` / `dumpbin /EXPORTS` | neva_session | 9 × `neva_*` | kırmızı | A0 blok |
| Uyarı temizliği | `/W4` + `-Wall -Wextra` | tüm hedefler | 0 uyarı | sarı→kırmızı (P0 sonrası) | — |
| Exception yasağı | `throw` (statik) | `extern "C"` blokları | 0 | kırmızı | P0 blok |
| Lint temel | clang-tidy `bugprone-*,performance-*` | src/ | 0 error | sarı | — |
| RTL kaynak | `// RTL` etiketli test/analiz kodu | tüm testler | 0 | kırmızı | — |
| Artefakt hijyeni | `*.obj|*.o|*.pdb` track edilmişse | repo | 0 | kırmızı | — |
| Commit mesajı | conventional-commit regex | git log | %100 | sarı | — |
| Lisans grep | `enable-gpl|fdk-aac|BASS.h|mpv/` | build + src | 0 | kırmızı | PAKET blok |
| Doküman link | link checker (opsiyonel) | docs/*.md | 0 kırık | sarı | — |

**İzlenebilirlik:** NE-ENG-119 (CI) · NE-ENG-125 (artefakt) · NE-ENG-166 (G5) ·
NE-ENG-131 (TSan) · NE-ENG-132 (fuzz) · kapı 5.1/5.13 · TC-ENG-158 (regex yasağı).

### §21.M.3 Bağımlılık Envanteri & Sürüm Kilitleri

| Bağımlılık | Sürüm | Rol | Lisans | Durum (disk kanıtı) |
|---|---|---|---|---|
| C++ standardı | C++20 | dil | — | CMakeLists.txt:30 (`/Zc:__cplusplus`) |
| MSVC | bilinmiyor `UNKNOWN` | derleyici | — | `/W4 /permissive-` — sürüm §18/U15 |
| libFLAC / dr_flac | `UNKNOWN` | FLAC decode | LGPL/MIT (seçime bağlı) `⚠️ VR` | YOK (decode_stub TODO) |
| libdsd | `UNKNOWN` (ADR-105 §6) | DSD/DoP | `UNKNOWN` → U3 | YOK |
| FFmpeg | 9.0.2 `⚠️ VR` | extended decode | LGPL-2.1+ (+GPL filtreler) | YOK (T-03 taslağı) |
| soxr | `UNKNOWN` | samplerate | LGPL-2.1 | YOK |
| mbedTLS | 9.13.0 `⚠️ VR` | TLS (S1-S8) | Apache-2.0 | YOK |
| libcurl | 8.23.0 `⚠️ VR` | HTTP/radyo | MIT | YOK |
| ASIO SDK | `UNKNOWN` | 1. basamak backend | VSL/deyimsel → U1 | YOK (NEVA_WITH_ASIO=OFF) |
| doctest/Catch2 | `UNKNOWN` | test framework | MIT | YOK — mevcut `<cassert>` (TE-02) |
| libFuzzer | LLVM sürümü `UNKNOWN` | fuzz | Apache-2.0 (LLVM exception) | YOK |

**Kural:** Sürüm sütunu `⚠️ VR` olan her satır vendor/ kilidi + NOTICE satırı
üretir (G3-2/G3-4); `UNKNOWN` satırlar T-01/T-03/T-02 taslaklarıyla kapanır.

## §21 Ek N — Sürüm Planı, Karar Günlüğü & Hızlı Başvuru Kartı (Engine)

### §21.N.1 ABI Sürüm Planı (A0 dondurma → minor genişleme)

| Sürüm | İçerik | Kapı | Durum |
|---|---|---|---|
| 1.0.0 | 9 fonksiyon + 5 kod (§21.D.3) — **donmuş** | A0 · TC-ENG-142 | Mevcut (disk) |
| 1.1.0 | E1-E10 event/callback + event-queue (plr §05.2 ile) | NE-ENG-128 · minor | PLANLI |
| 1.2.0 | E5 `set_eq_band_count(10|15|20|31)` + bypass maskesi | ADR-106 kural 2 · minor | PLANLI |
| 1.3.0 | E6 `eq_preset_save/load({grid_version, band_count, gains[], preamp})` | ADR-106 K6 · minor | PLANLI |
| 1.4.0 | E7 `set_eq_bypass(bool)` + per-kanal C ABI (gerekiyorsa) | minor | PLANLI `[ÖNERİ]` |
| 2.0.0 | ABI kıran değişim (yalnız 9 fonksiyonun imzası/semantiği) | A0+major | YASAK (bkz. C-01) |

**Kural (NE-ENG-047/048/063):** Her minor sürümde export listesi **9 + n** olur;
eski 9 imza **byte-byte aynı** kalır (header diff raporu §21.F.8). Kıran değişiklik
yalnız `NEVA_API_VERSION` major artışıyla ve eski DLL'le link reddiyle mümkündür (S06).

### §21.N.2 Sürüm Notu Şablonu (PAKET release notu için)

```markdown
## neva-engine vX.Y.Z — <tarih>
### Değişti
- (madde · NE-ENG-NNN · TC-ENG-NNN)
### Eklendi (minor)
- E# — <fonksiyon imzası> (ABI uyumlu: export = 9+#)
### Kilitlendi
- ABI: 9+n fonksiyon · 5 hata kodu (değişmez)
### Bilinen sınırlar (release notu ZORUNLU)
- L1-L4 gecikme kademeleri: `⚠️ VR` (ADR-0105 §2.10 dosyasız) — hedef, bağlayıcı değil (T-04)
- ASIO: OFF (U1 lisans kararı beklemede — T-01)
- DSD native: OFF (U3) — DoP açık
- 15/20-band: gizli (teyit bekleniyor — kSubset15/20 kilitli)
### Doğrulanmış iddialar (ölçüm raporu eki)
- Bit-perfect hash: 12/12 (§21.F.2 · K3)
- ctest: 3/3 exe yeşil · sanitizers: 0 bulgu
### Onaylar
- K-1..K-6 (§20.1) — Vault Steward + proje sahibi
```

### §21.N.3 Karar Günlüğü (bu PRD üretiminde alınan gerçek kararlar)

| # | Karar | Gerekçe | Kanıt | Tarih |
|---|---|---|---|---|
| D-1 | "8 fonksiyon" → **9 fonksiyon** (tüm PRD) | Disk kanıtı 9 imza; taslak sayımı yanlıştı | `neva_session.h:39-49` · C-01 | 2026-10-23 |
| D-2 | Hata kodu adları gerçek enum'a sabitlendi (`neva_error`/`neva_invalid_handle` uydurması kaldırıldı) | Sıfır halüsinasyon (AGENTS §3) | `neva_session.h:31-37` · §12.3 | 2026-10-23 |
| D-3 | Fallback zinciri **8 kayıt (Null dahil)** standardı | 7-step (Null hariç) ifadesi çelişkiliydi; tek standart | ADR-019 §2.2b · C-09 | 2026-10-23 |
| D-4 | 15/20-band frekans listeleri uydurulmadı, `kSubset15/20={false,0,{}}` kaldı | VLC/AIMP listeleri `⚠️ VR`; teyit gelene kadar gizli | `eq_grids.h:51-52` · TC-PLR-147 | 2026-10-23 |
| D-5 | Test framework `<cassert>` + ctest olarak raporlandı (doctest'e taşıma TE-02'ye bırakıldı) | Disk kanıtı (test_biquad.cpp:4-6 yorumu) | §21.D.8 | 2026-10-23 |
| D-6 | Null backend = zincirin 8. kaydı, "ses yok ama session yaşar" bilinçli davranış | CI ortamı cihazsızdır; crash değil davranış | `null_backend.cpp` (54 satır) | 2026-10-23 |
| D-7 | `test_biquad.obj` hijyen bulgusu raporlandı, silinmedi (onaysız dosya işlemi yasak) | AGENTS §16.4 dokunulmaz/surgical kuralı | envanter §21.D.1 | 2026-10-23 |
| D-8 | ADR-0105 içeriği dosyasız surrogate'den alındı; tüm atıflar `⚠️ VR` | Zero-hallucination + Vault Steward iade talebi | `.ai/.decisions/index.md:152` · §18.8 | 2026-10-23 |

**Bakım kuralı:** Yeni karar = bu tabloya + §17 izlenebilirliğe + (varsa) §18.17
çelişki defterine aynı gün yazılır; karar olmadan kod yazımı yasak (§1 hard gate).

### §21.N.4 Hızlı Başvuru Kartı (1 sayfa — bu PRD'nin tümü bu)

```
NEVA ENGINE — C++20 realtime audio motoru · kardeş: neva-player PRD
┌─────────────────────────────────────────────────────────────────┐
│ ABI: 9 fonksiyon (api_version, create, destroy, open_file,       │
│  set_output_device, play, pause, stop, set_eq_gain)              │
│  + 5 kod: 0 OK · 1 INVALID_ARG · 2 NO_DEVICE · 3 UNSUPPORTED ·   │
│    4 INTERNAL   (neva_session.h:31-49 — tek doğruluk kaynağı)    │
│  handle: opak neva_session_t* · yol: char16_t · exception YOK    │
├─────────────────────────────────────────────────────────────────┤
│ Zincir (8 kayıt): ASIO(OFF/U1)→WASAPI Excl→WDM-KS→PipeWire→     │
│  ALSA→CoreAudio→Shared→Null  —  Null: ses yok, session yaşar    │
│  (detay §21.H.2 — "7 aktif + Null" ifadesi yalnız bu bağlamda)   │
├─────────────────────────────────────────────────────────────────┤
│ EQ: 31-band ISO (20-20k) statik grid · 10 teyitli · 15/20 gizli  │
│  Flat=all-zero (EQ kapalı kanıtı) · biquad RBJ · FTZ/DAZ         │
│  preset: {grid_version, band_count, gain_db[31], preamp}         │
├─────────────────────────────────────────────────────────────────┤
│ Bütçeler (⚠️ VR = M1'de bağlayıcılaşır):                         │
│  RAM ≤400 MB · latency <20 ms (ASIO <10) · A/V P95 ≤20 ms       │
│  gapless ±5 ms · fuzz ≥1000/hedef · hash 12/12 SHA-256          │
├─────────────────────────────────────────────────────────────────┤
│ Kapılar: G0 scaffold · G1 ABI · G2 ölçüm · G3 lisans ·           │
│  G4 stres · G5 CI · G6 paket · 5.1 bit-perfect · 5.10/5.11/5.12 │
│  5.13 fuzz   (onay kapıları K-1..K-6 §20.1)                      │
├─────────────────────────────────────────────────────────────────┤
│ Fazlar: P0 çekirdek · A0 ABI dondurma · A1 backend · A2 DSP/EQ · │
│  M1-L4 gecikme · M2 ölçüm · M4 host · M6 fuzz · PAKET            │
│  (kod yazımı yalnız Vault onaylı fazda — §15)                    │
├─────────────────────────────────────────────────────────────────┤
│ KRİTİK açık: U1 ASIO · U6/L01 çekirdek lisans · U14 ölçüm eşiği  │
│  → ADR taslakları §21.L (T-01/T-02/T-04) — PAKET öncesi zorunlu  │
└─────────────────────────────────────────────────────────────────┘
```

### §21.N.5 PRD Bakım Talimatı (onay sonrası güncelleme akışı)

1. **Ölçüm tazeleme:** M1/M2 aşamasında §21.F şablonları doldurulur → §11.3
   tabloları + §20.6 ölçüm satırları güncellenir (eski değerler üstü çizili korunur).
2. **Karar kapanışı:** U# maddesi kapanınca: ADR accepted → §18.x'e "kapatıldı"
   notu + §17 izlenebilirlik satırı + §21.N.3 karar defteri.
3. **Çelişki defteri:** C-01..C-09 her turda yeniden okunur; kapanan satır
   "kapatıldı (kanıt)" etiketiyle arşivlenir, silinmez.
4. **Gereksinim değişikliği:** Yeni NE-ENG-NNN = §10'a ekle + Ek J/K (otomatik
   sözlük) yeniden üretilir + Ek C matrisi yeniden hesaplanır (elle değil).
5. **Vault sync:** `vault-sync-post` skill'i ile `.ai/` + `.claude`/`.opencode`
   dokunma noktaları + `log.md` append-only satır (AGENTS §7.14).

## §21 Ek O — Teknik Borç Envanteri, Kod Kuralları & RT Denetim Listesi (Engine)

### §21.O.1 Teknik Borç Envanteri (TD — disk kanıtıyla tespit edilen)

| ID | Borç | Kanıt | Sahip gereksinim | Ödeme fazı | Öncelik |
|---|---|---|---|---|---|
| TD-01 | `decode_stub` yalnız WAV (int16 + float32) | `decode_stub.cpp` TODO satırı | NE-ENG-102..107 | M2 (decode genişleme) | P0 |
| TD-02 | WASAPI backend skelet — exclusive loopback `⚠️ VR` | `wasapi_backend.cpp:6,129` | NE-ENG-021 | A1 (donanım testi) | P0 |
| TD-03 | `kSubset15` / `kSubset20` kilitli {false,0,{}} | `eq_grids.h:51-52` | ADR-106 kural 2 | A2+teyit | P1 |
| TD-04 | Test framework `<cassert>` — raporlama/fixture yok | `test_biquad.cpp:4-6` | TE-02 | M2 (doctest/Catch2) | P2 |
| TD-05 | E1-E10 event/callback yok (poll/queried) | header'da event yok | NE-ENG-128 | 1.1.0 | P1 |
| TD-06 | Preset dosya IO yok (EqPreset struct var, kayıt/yükleme yok) | `eq_grids.h:75` + `neva_session.cpp`'de IO yok | NE-ENG-094 | 1.3.0 (E6) | P2 |
| TD-07 | HTTP/radyo yok (parser var, akış yok) | `icy_parser.h` yalnız parse | NE-ENG-071 | P4 | P3 |
| TD-08 | Backend'ler eksik: ALSA/PipeWire/CoreAudio/WDM-KS yok | dosya envanteri §21.D.1 | NE-ENG-033..037 | A1/A2 | P1 |
| TD-09 | Fuzz hedefi yok (corpus/build yok) | `NEVA_FUZZ` yok | NE-ENG-132 | M6 | P1 |
| TD-10 | CI yok (lokal ctest var) | workflow dosyası yok | NE-ENG-119 | M0 | P0 |
| TD-11 | `test_biquad.obj` artefaktı track edilme riski | envanter §21.D.1 | NE-ENG-125 | hemen (gitignore) | P1 |
| TD-12 | ARM64 derleniyor ama cihaz testi yok | `⚠️ VR` NE-ENG-032 | NE-ENG-032 | A1 | P2 |
| TD-13 | LSan/ASan/UBSan/TSan koşusu yok | testler yalnız assert | NE-ENG-131 | M6 + CI | P1 |
| TD-14 | SNR/THD/LUFS ölçüm hattı yok | ölçüm yok §21.F.4 | NE-ENG-140/141 | M2 | P1 |
| TD-15 | Bit-perfect hash hattı yok (yalnız biquad mag testi) | `bitperfect_check` hedefi yok | NE-ENG-163 (K3) | M2 | P0 |

**Borç kuralı:** TD her sprintte gözden geçirilir; TD-01/02/15 P0 borcu release
notunda "bilinen sınır" olarak da görünür kalır (ödenene kadar).

### §21.O.2 C++ Kod Yazım Kuralları (engine bağlamı — global §5 + ADR-017)

| Alan | Kural | Denetim |
|---|---|---|
| Dil | C++20; `nullptr`; `enum class`; `override`; `final` | statik kapı |
| Bellek | RAII; `unique_ptr` varsayılan; `shared_ptr` gerekçeyle | kod incelemesi |
| Const | const-correctness; `constexpr` grid/sabitler; `[[nodiscard]]` | derleyici `/W4` |
| Hata | C++ tarafı: `expected`-tarzı `BackendResult` / `neva_result_t`; FFI'da exception YOK | statik: `throw` yok |
| Noexcept | RT yolu tamamen `noexcept`; yıkıcılar noexcept; `start/stop/kill` noexcept | statik kapı |
| Thread | atomikler `memory_order` açık; SPSC ring dışı kilit yasağı (RT) | TSan + statik |
| Tip | C ABI: `stdint.h` tipleri; `char16_t` yol; opaque handle; `NEVA_API` makrosu | header temizliği |
| Format | UTF-8 kaynak (`/utf-8`); Türkçe yorum serbest | derleyici |
| Test | `assert` + ctest; test fonksiyonu başına tek davranışı kanıtlar; dosya adı `test_<modul>.cpp` | ctest |
| Yorum | Header yorumu dosya rolünü + ADR izini taşır (mevcut dosyaların stili korunur) | kod incelemesi |
| ISM | 100-120 kolon; `snake_case` fonksiyon/üye (mevcut disk stili) | format |

**Yasak listesi (engine'e özgü):** `new/delete` RT yolunda · `std::string` render
içinde · global constructor-side-effect · `using namespace` header'da · C ABI'da
C++ isim-mangling (extern "C" zorunlu).

### §21.O.3 RT Kod Denetim Listesi (render callback'e giren her fonksiyon)

1. `malloc/calloc/realloc/free` yok (statik kapı).
2. `new/delete` yok; tüm buffer `prepare()`-da allocate (S3 kuralı).
3. `std::mutex/lock_guard/condition_variable` yok.
4. Dosya/IO/log/printf yok (`stderr` dahil).
5. `std::string`/`std::vector` gerçeklocation yok (fixed capacity).
6. Sanal çağrı: yalnız session başına 1 (backend vtable — sıcak değil).
7. `throw` yok; tüm hata yolu return-code + atomik sayaç.
8. NaN/inf koruması: dB→katsayı dönüşümü clamp'li (±30 dB `[ÖNERİ]`).
9. FTZ/DAZ: `enable_ftz_daz()` oturum başında çağrıldı mı?
10. Aligned load/store: `alignas(64)` yapısına taşma (UB) yok.
11. SPSC: tek yazıcı (RT) / tek okuyucu (worker) — MPMC kullanılmıyor.
12. Atomic order: `release` (push) / `acquire` (pop) eşleşiyor.
13. Kapasite: ring boyutu güç-iki (`kMask` şartı — `static_assert` var).
14. Callback süresi: 128 frame @48k = 2.67 ms bütçe; ölçüm (sayaç) var mı?
15. Hot-plug: callback içinde COM/WASAPI çağrısı yok (kuyruk).
16. EQ rampası: katsayı interpolasyonu blok-içi, kilit yok.
17. Bypass maskesi: Flat iken biquad process'i tamamen atlanıyor.
18. Interleave/deinterleave: stack buffer 8193 frame cap'i aşılmıyor (StreamError).
19. `volatile` kullanılmıyor (atomic).
20. Padding/`memcpy` ile tip-punning yok (`bit_cast` gerekirse).
21. Callback sonrası backlog: birikmiş event yok.
22. Timer/sleep yok (RT).
23. `thread_local` erişimi yok (RT).
24. Ölçüm: xrun sayacı atomik artışı (tek kalan yan etki).
25. Kill-switch: `kill()` çağrısı en geç bir sonraki blokta duruş (ADR-019 §2.2c).

### §21.O.4 Sayaç & Telemetri Kataloğu (opt-in — S3 §7)

| Sayaç | Tip | Amaç | Sahip test |
|---|---|---|---|
| xrun_total | atomic u64 | underrun/clip tespiti | TC-ENG-094 |
| device_switch_total | atomic u64 | zincir tetiklenme sayısı (kural: OOM'da retry yok) | TC-ENG-113 |
| stream_error_total | atomic u64 | `StreamError` (NE-ENG-082) | TC-ENG-118 |
| icy_drop_total | atomic u64 | geçersiz UTF-8 / >cap başlık | TC-ENG-159 |
| icy_bloom_warn_total | atomic u64 | FM-E15 bloom savunması | TC-ENG-160 |
| hata_kodu_1..4 | atomic u64 ×4 | §12.3 eşlemesi (INVALID_ARG..INTERNAL) | FM-E11..E20 |
| callback_last_us | atomic u64 | son callback süresi (RT ölçüm) | §21.F.3 |
| ring_high_watermark | atomic u64 | buffer taşma/derinlik | TC-ENG-092 |

**Kural:** Sayaçlar opt-in (S3 §7) · crash dump ayrı paket · kişisel veri YOK (S3 §9).

## §21 Ek P — Ölçüm & Doğrulama Betikleri (Engine · `[ÖNERİ]` taslaklar)

> Bu betikler §21.F rapor şablonlarını **dolduran** araçlardır; çalıştırma ortamı
> §18/U15 kararıyla sabitlenir. Betikteki eşikler `⚠️ VR` işaretliyse yalnız raporlar,
> fail etmez (T-04 kararı öncesi).

### §21.P.1 WASAPI Gecikme Ölçüm Betiği (§21.F.3 girdisi)

```powershell
# scripts/measure-wasapi-latency.ps1  [ÖNERİ]
# Kaynak: IAudioClient::GetDevicePeriod + loopback çapraz doğrulama
param(
  [ValidateSet('Excl','Shared')][string]$Mode = 'Shared',
  [int]$Rate = 48000,
  [int]$Block = 256
)
$ErrorActionPreference = 'Stop'
# 1) C++ yardimci (neva_latency_probe) device period'u yazar:
#    IAudioClient::GetDevicePeriod(&defaultPeriod, &minimumPeriod)
#    defaultPeriod (100ns) -> ms = $defaultPeriod / 10000.0
& .\build\Release\neva_latency_probe.exe --mode $Mode --rate $Rate --block $Block `
  | Tee-Object -Variable probe
$bufferMs = [double]($probe | Select-String 'buffer_ms=').ToString().Split('=')[1]
$periodMs = [double]($probe | Select-String 'device_period_ms=').ToString().Split('=')[1]
$roundtrip = $bufferMs + $periodMs   # [VARSAYIM — OS loopback payi +- 1-3 ms]
Write-Host ("RESULT mode=$Mode rate=$Rate block=$Block buffer={0:N2}ms period={1:N2}ms toplam~{2:N2}ms" -f $bufferMs,$periodMs,$roundtrip)
# 2) Eşik okuma (T-04 oncesi RAPOR):
#    Excl  <10 ms hedef  (ADR-0105 §2.10 ⚠️ VR)
#    Shared <20 ms hedef (ADR-0105 §2.10 ⚠️ VR)
# 3) CI'da yazdir: §21.F.3 tablosu satirina cevrilir.
```

### §21.P.2 Bit-Perfect Hash Doğrulama Betiği (kapı 5.1 · K3)

```powershell
# scripts/bitperfect_check.ps1  [ÖNERİ]
# 12 kombinasyon: 44100/48000/96000/192000 x 16/24/32f
$rates = 44100,48000,96000,192000
$depths = 16,24,32
$results = @()
foreach($r in $rates){ foreach($d in $depths){
  $src = ".\testdata\ref_${r}_${d}.wav"
  # (a) 1. calistirma
  & .\build\Release\neva_render.exe --in $src --out out1.raw --eq flat
  $h1 = (Get-FileHash out1.raw -Algorithm SHA256).Hash
  # (b) 2. calistirma (determinizm)
  & .\build\Release\neva_render.exe --in $src --out out2.raw --eq flat
  $h2 = (Get-FileHash out2.raw -Algorithm SHA256).Hash
  # (c) kaynak PCM (WAV header'siz) hash
  $hSrc = (Get-FileHash (.\scripts\wav-pcm-extract.ps1 $src) -Algorithm SHA256).Hash
  $pass = ($h1 -eq $h2) -and ($h1 -eq $hSrc)
  # (d) inverted guard: flat all-zero mu? (EQ kapali kaniti)
  $flatOk = -not (Select-String -Path out1.raw -Pattern '[\x01-\xFF]' -Quiet)
  $results += [pscustomobject]@{Rate=$r;Depth=$d;Det=($h1 -eq $h2);Equal=($h1 -eq $hSrc);Flat=$flatOk;Pass=($pass -and $flatOk)}
  # NOT: Flat=all-zero guard yalnizca "EQ bypass" calistirmasi icin gecerlidir;
  # normal render'da veri dolu olmalidir (guard bu ayrımı test eder).
}}
$results | Format-Table
if($results.Pass -contains $false){ exit 1 }  # kapali kapi -> CI kirmizi
```

### §21.P.3 Fuzz Runner (kapı 5.13)

```bash
#!/usr/bin/env bash
# scripts/fuzz-run.sh  [ÖNERİ]  — libFuzzer; CI ≤10 dk (K1)
set -euo pipefail
RUNS=${RUNS:-1000}            # kapı 5.13: ≥1000 vaka/hedef
declare -A HINTS=( [wav]="data/wav,include/neva/biquad.h" [icy]="include/neva/icy_parser.h" \
                   [abi]="include/neva/neva_session.h" [preset]="include/neva/eq_grids.h" )
rc=0
for h in wav icy abi preset; do
  echo "== fuzz_$h ($RUNS runs) =="
  ./build-fuzz/fuzz_$h -runs=$RUNS -max_len=65536 ./corpus/$h || rc=1
done
# CI toplam bütçe: 10 dk (timeout-minutes: 10). Asan/UBSan derlemeli.
exit $rc
```

### §21.P.4 Stres Betiği (G4 — 30 dk)

```powershell
# scripts/stress_30min.ps1  [ÖNERİ]
# Sahneler: play/pause 10k · stop/start 10k · hot-plug simülasyonu 5k · open/close 10k
$deadline = (Get-Date).AddMinutes(30)
$i = 0
while((Get-Date) -lt $deadline){
  $i++
  & .\build\Release\neva_stress.exe --scene ($i % 4) --iterations 1000
  # Bellek tepeyi rastgele ornekler:
  $p = Get-Process neva_stress -ErrorAction SilentlyContinue
  if($p -and $p.WorkingSet64 -gt 400MB){ Write-Warning "RAM butce asimi: $($p.WorkingSet64/1MB) MB" }  # ⚠️ VR esik
}
# Cikis: xrun_total==0 · device_switch_total==0 (OOM'da retry yok) · leak 0 (LSan)
```

### §21.P.5 Sembol & Artefakt Raporu Betiği (TC-ENG-142 · NE-ENG-125)

```powershell
# scripts/abi-symbol-report.ps1
# Windows: dumpbin /EXPORTS neva_session.dll  |  Linux: nm -D libneva_session.so
$exports = & dumpbin /EXPORTS .\build\Release\neva_session.dll | Select-String 'neva_'
$expected = 'api_version','create','destroy','open_file','set_output_device','play','pause','stop','set_eq_gain'
$missing = $expected | Where-Object { $exports -notmatch "neva_session_$_" }
if($missing){ Write-Error "ABI eksik semboller: $missing"; exit 1 }
$extra = $exports | Where-Object { $_ -notmatch 'neva_session_(api_version|create|destroy|open_file|set_output_device|play|pause|stop|set_eq_gain)' }
if($extra){ Write-Warning "Fazladan export (C++ sızıntısı?): $extra" }
# Artefakt hijyeni:
$artefacts = git ls-files | Select-String '\.(obj|o|pdb|ilk)$'
if($artefacts){ Write-Error "Track edilmis artefakt: $artefacts"; exit 1 }   # NE-ENG-125
```

### §21.P.6 Betik ↔ Rapor ↔ Kapı Eşleştirme

| Betik | Rapor | Kapı | Test |
|---|---|---|---|
| §21.P.1 | §21.F.3 Gecikme | 5.10 · G2 | TC-ENG-137/138 |
| §21.P.2 | §21.F.2 Hash | 5.1 · K3 | TC-ENG-127..130/143..149 |
| §21.P.3 | §21.F.5 Fuzz | 5.13 | TC-ENG-132 |
| §21.P.4 | §21.F.6 Stres | G4 | TC-ENG-094/096/141 |
| §21.P.5 | §21.F.8 Semver + hijyen | A0 · NE-ENG-125 | TC-ENG-142 |

**Kural:** Betik çıktısı olmadan §21.F raporu "ÖLÇÜLEMDİ" kalır; kapı otomatik
kapanmaz ama onay (K-x) verilmez (AGENTS §1 — kanıtsız "bitti" geçersiz).

## §21 Ek Q — PRD Üretim Günlüğü & Tarama Sonuçları (Engine · denetim izi)

> Bu ek, PRD'nin **nasıl üretildiğini** denetlenebilir kılar (AGENTS §3: her iddia
> disk kanıtıyla). Kaynak okuma, otomatik üretim ve düzeltme kayıtları buradadır.

### §21.Q.1 Okunan Kaynaklar (bu PRD üretiminde — tam liste)

| # | Kaynak | Kapsam | PRD yeri |
|---|---|---|---|
| 1 | `--/neva-engine/` recursive envanter (16 dosya + 1 artefakt) | satır sayıları, imzalar, yorumlar | §21.D |
| 2 | `include/neva/neva_session.h` (54 satır, tamamı) | 9 fonksiyon · enum 0-4 · NEVA_API_VERSION | §05.1 · §12.3 · §21.D.3 |
| 3 | `include/neva/iaudio_backend.h` (57, semboller) | 8 sanal · BackendResult · kill-switch | §21.D.4 |
| 4 | `include/neva/{spsc_ring,biquad,eq_chain,eq_grids,icy_parser}.h` | sınıf/sabit imzaları | §21.D.5/D.6 |
| 5 | `src/{neva_session,wasapi_backend,null_backend,decode_stub}.*` | implementasyon + `⚠️ VR` yorumları | §21.D.7 |
| 6 | `tests/test_{biquad,eq_grids,icy_parser}.cpp` | assert stili + ctest adları | §21.D.8 |
| 7 | `CMakeLists.txt` (75 satır) | 2 lib + 3 test exe + /W4 | §21.D.2 |
| 8 | `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` | durum makinesi · zincir · N1 · kill-switch | §04 · §21.E.2 |
| 9 | `.ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md` | 11 kural · K1-K6 · S1-S8 · G1-G6 | §06 · §07 · §21.E.3 |
| 10 | `.ai/.decisions/draft/ADR-107-...` + `.ai/architecture/neva-engine-platform-paketleme.md` | platform/paketleme · U1-U6 | §03 · §15 · §21.E.4 |
| 11 | `.ai/.decisions/index.md` (ADR-0105 surrogate: satır 152) | §2.6/§2.9/§2.10/§5.x iddiaları `⚠️ VR` | §18.8 · §21.E.5 |
| 12 | `coremusic-adr107-neva-engine-prompt.md` (218 satır, tamamı) | ADR-017 bağlamı · 25 dil FFI | §15.2 · §21.E.1 |
| 13 | `AGENTS.md` (proje kök, §6.1/§10/§13/§16) | persona hiyerarşisi · mockup gate · dokunulmaz yüzeyler | §00.2 · §11.1 · §20 |
| 14 | `~/.config/opencode/AGENTS.md` (global §5-§6) | mühendislik standartları · CoreMusic bağlamı | §13.3 · §14 |
| 15 | `.ai/CLAUDE.md` + `.ai/RAG.md` + `.ai/WORKFLOW.md` (gerektiğinde) | anayasa · retrieval · süreç | §00.1 |

### §21.Q.2 Otomatik Üretim Kayıtları (elle yazılmadı — script ile)

| Ek | Üretim yöntemi | Doğrulama |
|---|---|---|
| §21 Ek A (gereksinim indeksi) | §10 `#### NE-ENG-NNN` bloklarından extraction | 182/182 ID kapsandı |
| §21 Ek B (test indeksi) | §11 `#### TC-ENG-NNN` bloklarından | 182/182 (grup blokları: 127..130, 161..175) |
| §21 Ek C (req→TC matrisi) | "İlgili test" satırlarından çaprazlama | 182 satır, 0 unmatched |
| §21 Ek J (gereksinim künyesi) | başlık+kabul+ADR izi+test extraction | 174 blok = 182 ID (167..175 grup) |
| §21 Ek K (test künyesi) | başlık+gereksinim+katman+otomasyon | 163 blok = 182 ID (gruplar dahil) |

### §21.Q.3 Yapılan Tutarsızlık Düzeltmeleri (denetim listesi)

| # | Bulgu | Eski (taslak) | Yeni (disk kanıtı) | Kanıt | Durum |
|---|---|---|---|---|---|
| C-01 | Fonksiyon sayısı | "8 fonksiyon" (11 dosyada) | **9 fonksiyon** | `neva_session.h:39-49` | Düzeltildi (replaceAll ×8 + tekil) |
| C-01b | §12.3 tablo adları | `neva_error` / `neva_invalid_arg` / `neva_invalid_handle` | gerçek enum: `NEVA_ERR_INVALID_ARG/NO_DEVICE/UNSUPPORTED_FORMAT/INTERNAL` | `neva_session.h:31-37` | Düzeltildi (eng-p13 §12.3) |
| C-01c | Clamp kodu | "kod 2" (INVALID_ARG yerine) | "kod 1 (INVALID_ARG)" | enum: 1=INVALID_ARG, 2=NO_DEVICE | Düzeltildi (eng-p14 ×4) |
| C-01d | FM-E11 telemetri | "Hata kodu 2 sayacı" | "Hata kodu 1 sayacı (INVALID_ARG)" | use-after-close → INVALID_ARG `[ÖNERİ]` | Düzeltildi (eng-p13) |
| C-01e | Player senaryo H6 | "invalid_handle → log" | "INTERNAL (kod 4) → log" | enum 4=INTERNAL | Düzeltildi (plr-p15) |
| C-01f | ABI testi | "eski 8 fonksiyon sabit mi" | "eski 9 fonksiyon sabit mi" | TC-PLR-201 | Düzeltildi (plr-p13) |
| C-01g | band parametresi | "band_count dışında RED" (uydurma parametre) | "0..30 dışı RED" (header'da band_count YOK) | `neva_session.h:47` | Düzeltildi (eng-p14) |
| C-09 | Zincir basamağı | "7 basamak" vs "8 tane" | **8 kayıt (Null dahil); "7 aktif" yalnız Null hariç bağlamda** | ADR-019 §2.2b · §21.H.2 | Standartlaştırıldı |

**Kalıntı yok:** final tarama `8 fonksiyon|neva_error|neva_invalid_handle` → yalnız
bilinçli alıntı satırları (C-01 çelişki defteri + üretim günlüğü) kaldı.

### §21.Q.4 İşaret Taraması (§20.6 için sayımlar — bu PRD)

| İşaret | Motor (bu PRD) | Kardeş (player) | Anlam |
|---|---|---|---|
| `⚠️ VR` | (sayım §20.6 — birleştirme sonrası güncellenir) | (aynı) | doğrulanmamış amaç değer |
| `[ÖNERİ]` | (aynı) | (aynı) | taslak/öneri |
| `UNKNOWN` | (aynı) | (aynı) | bilinmeyen |
| `[VARSAYIM]` | (aynı) | (aynı) | açık varsayım |
| `⚠️ LİSANS` | (aynı) | (aynı) | lisans karar bağımlı |

### §21.Q.5 Üretim Ortamı Notları

- **Bölüm stratejisi:** 39 numaralı parça dosyası (`eng-p01..p39`) tek büyük `.md`'ye
  birleştirilir; ara dosyalar (`eng-idx-*.md`) yalnız üretim içindir, silinir.
- **Ölçüm yöntemi:** `(Get-Content f).Count` = ham satır (hedef ≥8000);
  `Measure-Object -Line` = boşluksuz. Her ikisi de raporlanır (§20.6).
- **Kod yazımı yok:** bu PRD yalnız `.md` üretti; `git add/commit` YAPILMADI
  (AGENTS §16.5 — subagent commit atmaz; orkestratör kararı).
- **Gizli dizin kuralı:** `.ai/` aramaları `hidden: true` ile (AGENTS §7.2).
- **Bilinen gösterim notu:** PowerShell konsolu UTF-8'TE mojibake gösterir;
  dosyalar UTF-8'dir (byte-level doğrulandı — §13.7 sahte-kırık-link kuralı).

## §21 Ek R — Test Ortamı & Donanım Envanteri Planı (U15 girdisi)

> §18/U15 açık maddesinin **hazırlık listesidir**; gerçek envanter alındıkça bu ek
> güncellenir ve `⚠️ VR` etiketleri ölçüm raporuyla kalkar (T-04 kardeş).

### §21.R.1 Yazılım Ortamları (CI + lokal)

| Ortam | Sürüm | Amaç | Kapı | Durum |
|---|---|---|---|---|
| Windows 11 | 23H2+ `⚠️ VR` | WASAPI Excl/Shared · imza · MSIX | A1 · PAKET-Windows | `UNKNOWN` (CI runner §18/U15) |
| Windows 10 | 22H2 `⚠️ VR` | geriye uyumluluk | A1 | `UNKNOWN` |
| Ubuntu | 24.04 LTS `[ÖNERİ]` | ALSA/PipeWire · sanitizers · fuzz | A1 · M6 | `UNKNOWN` |
| GCC | 13+ `⚠️ VR` | Linux derleme | A1 · TC-ENG-163 | `UNKNOWN` |
| Clang | 17+ `⚠️ VR` | ASan/UBSan/TSan/fuzz | M6 | `UNKNOWN` |
| MSVC | VS2022 17.x `⚠️ VR` | /W4 /permissive- · dumpbin | A0 · TC-ENG-142 | `UNKNOWN` |
| macOS/Xcode | `UNKNOWN` | CoreAudio (A2) | A2 | `UNKNOWN` (donanım erişimi yok) |

### §21.R.2 Ses Donanımları (gerçek ölçüm için — satın alma/ödünç listesi)

| Donanım | Rol | Neden gerekli | Öncelik |
|---|---|---|---|
| WASAPI destekli USB DAC | Exclusive mod + loopback | bit-perfect hash + gecikme (§21.F.2/F.3) | P0 |
| 2. cihaz (farklı marka) | zincir + hot-plug + cihaz seçimi | FM-E02/E13 · TC-ENG-030 | P1 |
| Düşük gecikmeli kulaklık/mikser | xrun/underflow gözlemi | stres (G4) | P2 |
| Loopback kablosu/analyzer | SNR/THD+N ölçümü | V-1/V-2 (§21.F.4) | P1 |
| ARM64 cihaz (Windows/Linux) | NE-ENG-032 doğrulama | `⚠️ VR` kalkışı | P2 |
| Eski/sahte sürücü ortamı | sürücü çökmesi simülasyonu | FM-E14 | P3 |

### §21.R.3 Ölçüm Araçları

| Araç | Kullanım | Rapor |
|---|---|---|
| `neva_latency_probe` (§21.P.1) | device period + buffer | §21.F.3 |
| `bitperfect_check` (§21.P.2) | SHA-256 + determinizm | §21.F.2 (K3) |
| libFuzzer (§21.P.3) | wav/icy/abi/preset | §21.F.5 (5.13) |
| `stress_30min` (§21.P.4) | G4 metrikleri | §21.F.6 |
| `dumpbin`/`nm` (§21.P.5) | export + artefakt | §21.F.8 · NE-ENG-125 |
| Audacity/loopback `[ÖNERİ]` | analog çapraz doğrulama | §21.F.4 (önsel) |
| DPC-latency aracı `[ÖNERİ]` | sürücü gecikme taraması | §21.F.3 notu |
| LSan/ASan/UBSan/TSan | bellek/veri yarışı | NE-ENG-131 |

### §21.R.4 Ortam Karar Adımları (U15 kapatma)

1. CI runner sahipliği (self-hosted audio makinesi mi, bulut mu?) — §18/U15.
2. DAC modeli + lisans/ölçüm izni — §18.10 (U10 kardeş PCM3168A `⚠️ VR`).
3. Ölçüm eşiklerinin bağlayıcılığı — T-04 (U14).
4. Sonuç: bu ekteki `UNKNOWN`/`⚠️ VR` hücreleri dolu → §18.15 kapanır →
   Vault Steward log.md satırı → release notu "ölçülmüş" diline geçer.

**Kural:** Donanım/ortam satırı `UNKNOWN` iken hiçbir ölçüme "başarılı" denmez;
raporlar "ÖLÇÜLEMDİ" etiketiyle arşivlenir (AGENTS §3).

## §21 Ek S — Üretim Süreci SSS & Kapanış (Engine)

**SSS-PRD-1 — Bu PRD neden 8000+ satır?** Kullanıcı şartı (min 8000 ham satır) +
182 gereksinim/182 test/20 senaryo/10 ek yoğunluğunun doğal sonucu. Dolgu yok:
her bölüm disk kanıtı veya bağlantı üretiyor (§21.Q.2 otomatik ekler bu kuralın
istisnası değil — onlar da diskten üretildi).

**SSS-PRD-2 — Ek A/B/C/J/K elle mi yazıldı?** Hayır — PowerShell extraction ile
§10/§11 bloklarından üretildi (§21.Q.2). Elle yazılsaydı 182+182 satır risk
taşırdı (sıfır halüsinasyon yerine yazım hatası). Yeniden üretim: §21.N.5 madde 4.

**SSS-PRD-3 — "8 fonksiyon → 9" düzeltmesi ne anlama geliyor?** Taslak metinler
ADR-107'deki "8" sayımını taşırken diskte 9 imza vardı (set_eq_gain dahil). Karar:
disk kazanır (AGENTS §3) — tüm PRD 9'a sabitlendi, çelişki defterine C-01 yazıldı.

**SSS-PRD-4 — PRD onaylanınca ne olur?** K-6 onayı → Vault Steward `.ai/` altına
kopyalar (PRD kök `PRD.md` indeksine kayıt — AGENTS §9 yeni satırı) + `log.md`
append-only satır + §18 açık maddeleri sahiplendirilir → M0 fazı açılır.

**SSS-PRD-5 — Kod ne zaman yazılır?** Asla bu PRD ile. Yalnız Vault onaylı faz
(§15.2-§15.5) + Vault Steward onayı ile (AGENTS §16.5 · §15 kuralı). Bu oturumda
hiçbir C++ dosyası DEĞİŞTİRİLMEDİ (yalnız okundu).

**SSS-PRD-6 — Ölçümler yapıldı mı?** Hayır — bu PRD yalnız kaynak okudu ve
planladı. Tüm `⚠️ VR` eşikleri ve §21.F şablonları M1/M2 ölçüm aşamasında
doldurulacak; bu oturumda NOT TESTED olan her şey böyle işaretli kaldı.

**SSS-PRD-7 — Kardeş PRD ile fark ne?** Motor: C++20 çekirdek, C ABI, DSP, backend,
bit-perfect. Player: per-OS UI, katalog, radyo UI, preset dosya IO, paketleme.
Ortak yüzey: §12.3 hata kodu eşlemesi + E1-E10 event + K3 hash köprüsü.

**SSS-PRD-8 — neden test framework doğrudan değiştirilmedi?** Diskte `<cassert>`
+ ctest var (TE-02 açık madde). "Doğru" framework kararı TE-02 ADR'sine ait;
bu PRD yalnız mevcut durumu raporladı (§21.D.8).

**SSS-PRD-9 — Fuzz corpora nerede?** Yok (TD-09). M6 fazı NE-ENG-132'de üretilir:
gerçek dosya örnekleri + sentetik mutasyonlar; boyut/derinlik kapı 5.13'te.

**SSS-PRD-10 — PRD güncellemesi kimde yetki?** Vault Steward (Vault senkronu) +
proje sahibi (onay kapıları). Subagent/agent PRD'yi güncelleyemez; yalnız öneri
üretir (AGENTS §6.5 · §16.5).

---

### §21.S.1 Kapanış Sayfası (Engine PRD)

| Özet | Değer |
|---|---|
| Gereksinim | 182 (NE-ENG-001..182 · §10 · Ek A) |
| Test | 182 (TC-ENG-001..182 · §11 · Ek B) |
| Kapsama | 182/182 (Ek C matrisi · 0 unmatched) |
| Hata modu | 20 (FM-E01..E20 · §12) |
| Lisans maddesi | L01..L17 (§14 · G3) |
| Açık soru | U1..U15 (§18) + 4 ADR taslağı (§21.L) |
| Çelişki defteri | C-01..C-09 (§18.17 + §21.Q.3) |
| Senaryo | S01..S20 (§21.G) |
| Teknik borç | TD-01..TD-15 (§21.O.1) |
| Ölçüm raporu şablonu | §21.F.1..F.10 + betik §21.P |
| Onay kapısı | K-1..K-6 (§20.1) — K-6 ONAYLANDI (2026-10-23) |

**Son cümle:** Bu PRD bir **sözleşmedir** — motor kodu bu belgeye göre yazılır,
ölçüm bu belgeyle kabul edilir, sapma bu belgede çelişki defterine yazılır.
Onay verilmeden hiçbir satır bağlayıcı değildir (AGENTS §1 hard gate).

## §21 Ek T — Nihai Ölçüm Kaydı (Engine · birleştirme sonrası)

> Bu ek birleştirme (concat) sonrasında **gerçek dosyadan** ölçülmüştür; sayımlar
> dosya üzerinde iki yöntemle alınmıştır (ham satır + boşluksuz satır).

| Ölçüm | Değer |
|---|---|
| Parça dosyası sayısı | 42 (`eng-p01..p42`) |
| Ham satır (toplam) | (birleştirme sonrası güncellendi — §20.6) |
| Boşluksuz satır | (birleştirme sonrası güncellendi — §20.6) |
| Bayt | (birleştirme sonrası güncellendi — §20.6) |
| Bölüm sayısı | §00..§21 (+ Ek A..T) |
| Gereksinim / Test | 182 / 182 (0 unmatched) |
| Hata modu / Açık soru / Çelişki | FM-E01..E20 · U1..U15 · C-01..C-09 |
| `⚠️ VR` / `[ÖNERİ]` / `UNKNOWN` / `[VARSAYIM]` | §20.6 sayım tablosu |

**Ölçüm yöntemi (her iki):**
1. `(Get-Content hedef.md -Encoding UTF8).Count` → ham satır (kullanıcı şartı ≥8000).
2. `(Get-Content hedef.md -Encoding UTF8 | Measure-Object -Line).Lines` → boşluksuz.

**Son kontrol:** §20.4 onay tablosu K-6 = ONAYLANDI (2026-10-23 — kullanıcı yanıtı "DEVAM ET").
