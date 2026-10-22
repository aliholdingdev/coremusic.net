---
title: "Neva Engine — Platform Kararı, Backend Soyutlama ve Paketleme (Lib + Çok Executable)"
type: architecture
category: neva-engine
status: draft
version: 1.0.0
date: 2026-10-22
authority: "Embedded Engineer — L0 Audio Core"
tier: 3
domain: audio
ssot: false
risk: high
owner: "MO"
depends-on: ["ADR-107", ".ai/research/reverse-voicemeeter-fxsound-2026-10-22.md", ".ai/research/sanal-audio-cihaz-arastirmasi-2026-10-22.md", ".ai/electronic/dsp"]
---

# Neva Engine — Platform, Backend Soyutlama ve Paketleme

**Karar durumu:** Tescil taslağı → `.ai/.decisions/draft/ADR-107-neva-engine-platform-ve-paketleme.md`
**Kapsam:** Neva Engine'in hangi platform/teknoloji üstünde, hangi paketleme stratejisiyle üretileceği.
**Kapsam dışı:** DSP algoritma kodu (`.ai/electronic/dsp/`), donanım topolojisi (`.ai/electronic/hardware/`), web/backend servisleri.

---

## §1 Bağlayıcı Kararlar (kullanıcı direktifi, 2026-10-22)

| # | Karar | Ayrıntı |
|---|---|---|
| K1 | **Özel C++20 çekirdek + backend soyutlama** | JUCE'ye bağımlı değil; `INevaAudioBackend` altında platform API'leri |
| K2 | **Statik lib + çok executable** | `neva-core` (statik lib) + `neva-player` + `neva-host` + `neva-plugin` + **kullanıcı kendi exe'lerini ekleyebilir** |
| K3 | **Hi-res hedef** | 32-bit float / 192 kHz **bit-perfect** |
| K4 | **Platform yelpazesi** | Windows + **Linux** + macOS (+ ARM64) |
| K5 | **Fallback zinciri** | ASIO → WASAPI exclusive → WDM-KS → ALSA/PipeWire → CoreAudio |

**İş kanıtı (RE raporu §2.3):** VoiceMeeter üretimde **modüler executable** stratejisi kullanıyor (BUS GEQ15 / BUSMatrix8 / MacroButtons / StreamView / VBAN2MIDI ayrı exe'ler + Remote DLL). K2 bu emsalle desteklenir.

---

## §2 Platform Matrisi (OS × Backend × bit-perfect yolu)

| OS | Birincil backend | İkincil | Fallback | bit-perfect yolu | ARM64 | Durum |
|---|---|---|---|---|---|---|
| Windows 10/11 x64 | **ASIO2** (Steinberg SDK) | WASAPI exclusive | WDM-KS | ASIO (donanım pin formatı birebir) | ✅ hedef | Kritik yol |
| Windows 10/11 ARM64 | WASAPI exclusive | — | WDM-KS | WASAPI exclusive (donanım formatı) | ✅ hedef | VoiceMeeter emsal var |
| Linux (x64/ARM64) | **PipeWire** | ALSA (`hw:` doğrudan) | PulseAudio (bit-perfect DEĞİL) | PipeWire `F32P` + `hw:` doğrudan erişim | ✅ hedef | RPi5 = `home.coremusic.net` |
| macOS | **CoreAudio** (HAL, `kAudioStreamPropertyAvailablePhysicalFormat`) | — | — | HAL bit-depth/swap toggle | ⚠️ PLANNED | 2. kadeh |
| Windows XP/7 | — | — | — | **hedef DIŞI** | — | Düşük öncelik |

**Kısıt doğrulaması ("ASIO + WASAPI exclusive her yerde"):** ASIO yalnız Windows'tur; bu nedenle kısıt **"Windows'ta ASIO+exclusive, diğerlerinde exclusive/doğrudan"** olarak yorumlanmıştır (K5). Ters yorum (Linux'ta ASIO) teknik olarak mümkün değildir → `⚠️ yorumlama notu`.

---

## §3 Backend Soyutlama Katmanı (arayüz sözleşmeleri)

> Bu bölüm **header düzeyinde sözleşme** verir; implementasyon **bu belgede YOK** (Zero Code Before Plan).

### §3.1 Çekirdek tipler

```cpp
// neva/core/include/neva/core/Types.h  (sözleşme — implementasyon PLANNED)
namespace neva::core {

struct AudioFormat {
    double   sample_rate;   // 44100.0 … 192000.0
    uint16_t bit_depth;     // 16 | 24 | 32 (float=32f)
    uint16_t channels;      // 1 … 8 (8.1 hedefi)
    bool     is_float;      // true → IEEE-754 float
    uint32_t frames_per_buffer; // 32 … 4096 (exclusive hedef: 32/64/128)
};

enum class BackendKind : uint8_t { Asio, WasapiExclusive, WasapiShared, WdmKs,
                                   Alsa, Pipewire, CoreAudio, Null };

struct StreamError { int32_t code; BackendKind backend; const char* what; };

} // namespace neva::core
```

### §3.2 Backend arayüzü

```cpp
// neva/core/include/neva/core/IAudioBackend.h  (sözleşme)
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

### §3.3 Backend kayıt/factory (fallback zinciri K5)

```cpp
// neva/core/include/neva/core/BackendRegistry.h  (sözleşme)
// Sıra: Asio → WasapiExclusive → WdmKs → Alsa/Pipewire → CoreAudio → Null
Expected<std::unique_ptr<IAudioBackend>, StreamError>
open_best_backend(const DeviceSelector&, const AudioFormat& target) noexcept;
```

### §3.4 Backend sorumluluk matrisi

| Backend | Uygulama dosyası (PLANNED) | Öncelik | Özel kural |
|---|---|---|---|
| ASIO | `neva-core/backends/asio/` | P0 | ASIO SDK lisansı (GPL/ticari) — `⚠️ LİSANS KAPISI` |
| WASAPI exclusive | `neva-core/backends/wasapi/` | P0 | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `IsFormatSupported` iki deneme (WAVEFORMATEX/WAVEFORMATEXTENSIBLE) |
| WDM-KS | `neva-core/backends/wdmks/` | P2 | Düşük seviye; exclusive başarısızsa |
| PipeWire | `neva-core/backends/pipewire/` | P1 | `F32P` (32-bit float) default; `hw:` doğrudan |
| ALSA | `neva-core/backends/alsa/` | P1 | `snd_pcm_hw_params` ile sabit format |
| CoreAudio | `neva-core/backends/coreaudio/` | P3 | HAL physical format toggle |
| Null | `neva-core/backends/null/` | P0 | Test/CI (bit-perfect hash hattı) |

---

## §4 Paketleme — Lib + Çok Executable

### §4.1 Hedef envanteri

| Hedef | Tip | Amaç | Windows | Linux |
|---|---|---|---|---|
| `neva-core` | **statik lib** | DSP çekirdeği + backend soyutlama + kuyruk/zaman | ✅ `.lib`/`.a` | ✅ `.a` |
| `neva-player` | exe | CLI çalma/dönüştürme/kayıt | `neva-player.exe` | `neva-player` |
| `neva-host` | exe | GUI köprüsü (Electron/Qt/Vanilla köprü: IPC sunucu) | `neva-host.exe` | `neva-host` |
| `neva-plugin` | exe/dll (wrap) | VST3 / AU / AAX sarmalayıcı | `.vst3` | `.vst3` |
| `neva-eq` | exe | 31-band PEQ/Grafik EQ ayrı pencere (VoiceMeeter BUSGEQ15 emsal) | ✅ | ✅ |
| `neva-matrix` | exe | 8×8 yönlendirme matrisi (BUSMatrix8 emsal) | ✅ | ✅ |
| `neva-remote` | lib+dll | Uzaktan kontrol API'si (VoiceMeeterRemote emsal) | ✅ | ✅ |
| `neva-mcp` | exe | **AI agent kontrolü — MCP sunucu** (fxmcp emsal) | ✅ | ✅ |
| `neva-cli-<kullanıcı>` | exe | **Kullanıcı kendi executable'ını ekler** (`neva-core` + registry) | ✅ | ✅ |

### §4.2 Eklenebilir executable kuralı (kullanıcı direktifi: "ileride kendi projelerimi ekleyeceğim")

```cmake
# apps/CMakeLists.txt — yeni exe eklemek için tek satır kayıt
neva_add_executable(NAME      "neva-eq"
                    SOURCES   eq/main.cpp
                    LINK      neva_core      # statik lib
                    PLATFORMS WIN32 LINUX    # ARM64 dâhil
                    ENTRY     neva::app::eq_entry)
```

Kural: **yeni executable yalnız `apps/` altına eklenir**, `neva-core`'a dokunmaz; DSP mantığı core'dadır, exe yalnız **giriş/IPC/UI** katmanıdır. Böylece kullanıcı ileride `neva-myplugin` gibi yeni hedefler ekleyebilir.

### §4.3 Bağımlılık yasakları

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | `neva-core` içine GUI framework **girmez** | Core, real-time thread'de çalışır; GUI kütüphaneleri alloc/lock içerir |
| 2 | `neva-core` içine **ağ** girmez | Ağ ayrı hedef (`neva-remote`/`neva-mcp`) |
| 3 | Real-time thread'de `new`/`malloc`/`mutex` **yasak** | Zero-allocation, lock-free (vault `electronic/dsp/` kuralı) |
| 4 | Backend doğrudan exe'den **çağrılır** | Yalnız `neva-core` üzerinden (tek soyutlama noktası) |

---

## §5 Build Stratejisi (CMake + toolchain matrisi)

| Alan | Karar |
|---|---|
| Üst düzey | `CMakeLists.txt` (repo kökü) + `cmake/neva-toolchains.cmake` |
| Standart | `CMAKE_CXX_STANDARD 20` · `noexcept` zorunlu · `-fno-exceptions` tartışması `⚠️ PLANNED` |
| Generator | Visual Studio 2022 (Windows), Ninja (Linux/CI) |
| Toolchain | MSVC v143 · GCC 13+ · Clang 17+ · (ARM64: MSVC ARM64, GCC aarch64) |
| Paket yöneticisi | **vcpkg YOK** — statik bağımlılık politikası: ASIO SDK vendor, WAV I/O kendi kodu |
| CI | `.github/workflows/` (mevcut `ci.yml` genişletilecek) — Windows/Linux matrisi |
| Çıktı | `build/<platform>-<arch>/` — her hedef ayrı artefakt |

---

## §6 Test & Doğrulama Stratejisi

### §6.1 Birim/test hattı

| Katman | Araç | Kapsam |
|---|---|---|
| DSP birim test | **Catch2/GoogleTest** (seçim ADR-107) | EQ/reverb/limiter tepki doğruluğu, THD+N/SNR ölçümlü |
| Backend kontrat test | `Null` backend + sahte cihaz | `IAudioBackend` tüm implementasyonları aynı testi geçer |
| **bit-perfect doğrulama** | WAV hash | Çalınan dosyanın byte-byte hash'i çıkışta birebir mi? (`Null` + loopback karşılaştırma) |
| xrun/underrun | sayaç | 30 dk sürekli çalışma, 0 xrun hedefi |
| CI kapı | GitHub Actions | Windows + Linux × x64/ARM64 |

### §6.2 bit-perfect doğrulama planı (kritik)

```text
1. Referans WAV üret (32-bit float, 192 kHz, sine/noise/kick seti) — hash kaydet
2. Backend'i exclusive ile aç, format = referans formatı (negotiated_format() == target)
3. Çıkışı kaydet (loopback capture veya sanal cihaz üzerinden)
4. Hash karşılaştır → eşit = bit-perfect ✓
5. 44.1k/48k/96k/192k × 16/24/32f matrisi × backend
```

---

## §7 Sanal Ses Cihazı İlişkisi

Neva Engine, **var olan** sanal cihazı (VoiceMeeter VAIO / FxSound fxvad) **kullanmak zorunda değil**; CoreMusic'in kendi sanal cihazı hedefi var:

| Konu | Karar |
|---|---|
| Sanal cihaz | Ayrı iş: `VirtualDrivers/Virtual-Audio-Driver` fork + SysVAD pin formatı 32-bit float/192 kHz (bkz. `sanal-audio-cihaz-arastirmasi-2026-10-22.md`) |
| Neva ↔ sanal cihaz | Neva, sanal cihazı **sadece bir backend cihazı** olarak listeler (DeviceId) |
| ASIO bridge | Neva'nın kendi sanal cihazı varsa, ona ASIO bridge verilir (VoiceMeeter emsal, RE raporu §6.2) |
| Cross-platform | macOS: **BlackHole** · Linux: PipeWire null-sink / `snd-aloop` (192k sınırına dikkat) |

---

## §8 VST3 / AU / AAX Sarmalama

| Format | Hedef | Not |
|---|---|---|
| VST3 | `neva-plugin` | JUCE VST3 wrapper **kullanılmaz** (bağımlılık) → `⚠️ VST3 SDK lisansı` (GPLv3/commercial — ASIO benzeri) |
| AU | macOS | `⚠️ PLANNED` — 2. kadeh |
| AAX | Pro Tools | `⚠️ PLANNED` — Pro Tools SDK erişimi doğrulanmalı |

Kural: sarmalayıcı yalnız `neva-core`'u yükler; DSP core'un kendisi format bağımsızdır.

---

## §9 AI / MCP Katmanı (`neva-mcp`)

| Alan | Karar |
|---|---|
| Amaç | AI agent'ların EQ/reverb/limiter/preset/cihaz parametrelerini aracı olarak sürmesi |
| Emresal | FxSound `fxmcp` (Go, MCP SDK v1.6.1) — `fxsound-app-source-analizi-2026-10-22.md` §5 |
| Dil seçimi | `⚠️ PLANNED — ADR-107'ye açık madde` (Go mu, Node/PHP bridge mi) |
| Taşıma | Yerel IPC (`neva-remote` üzerinden) |
| Güvenlik | Yetki sınırı (yalnız EQ/preset yazımı), `security_test` benzeri denetim |

---

## §10 Risk Matrisi

| # | Risk | Etki | Önlem |
|---|---|---|---|
| R1 | **ASIO SDK lisansı** (GPLv3/ticari) | Yayın engeli | ADR kapısı: GPLv3 uygulama mı, ticari lisans mı |
| R2 | **VST3 SDK lisansı** | Aynı | ADR kapısı |
| R3 | Sürücü imzalama (sanal cihaz) | Kurulum engeli | EV/Attestation planı (research raporu §4) |
| R4 | PipeWire 192k sınırı (`snd-aloop`) | Linux bit-perfect | PipeWire `F32P` + `hw:` doğrudan |
| R5 | Çok executable → sürüm senkronu | Parçalanmış sürüm | Tek repo + tek CMake + tek sürüm dosyası |
| R6 | Zero-allocation ihlali (3. parti kütüphane) | Xrun | Vendor kod incelemesi + CI xrun testi |

---

## §11 UNKNOWN / açık maddeler

| # | Madde | Durum |
|---|---|---|
| U1 | VST3 SDK sürüm + lisans koşulu | `⚠️ VERIFICATION REQUIRED` |
| U2 | ASIO SDK 2.3.4 güncel sürüm + lisans metni | `⚠️ VERIFICATION REQUIRED` |
| U3 | macOS CoreAudio HAL bit-perfect toggle davranışı | `⚠️ VERIFICATION REQUIRED` |
| U4 | PipeWire 192 kHz `hw:` doğrudan erişim kanıtı | `⚠️ VERIFICATION REQUIRED` (research raporunda kaynaklı, cihaz testi yok) |
| U5 | `neva-mcp` dil seçimi | Kullanıcı kararı bekliyor |

---

## §12 İlişkili kararlar

| Dosya | İlişki |
|---|---|
| `.ai/.decisions/draft/ADR-107-neva-engine-platform-ve-paketleme.md` | Tescilli karar |
| ADR-017 (DSP hardware mode) | ASIO/WASAPI tercih zinciriyle uyum |
| `.ai/electronic/dsp/` (10 dosya) | Zero-allocation, lock-free, noexcept kuralları |
| `.ai/electronic/drivers/` (10 dosya) | Sürücü mimarisi |
| `.ai/research/reverse-voicemeeter-fxsound-2026-10-22.md` | Emsal RE bulguları |

---

## §13 Backend Uygulama Şartnameleri (adım-adım, kod SÖZLEŞME düzeyi)

### §13.1 WASAPI exclusive (P0 — Windows)

```text
1. IMMDeviceEnumerator::GetDefaultAudioEndpoint(eRender, eConsole) → DeviceId
2. IAudioClient::IsFormatSupported(AUDCLNT_SHAREMODE_EXCLUSIVE, &wfex_target)
   ├─ S_OK      → istenen format birebir destekleniyor
   ├─ S_FALSE   → önerilen wfes döner (bit-perfect DEĞİL → reddet, hataya yaz)
   └─ AUDCLNT_E_UNSUPPORTED_FORMAT → hedef WAVEFORMATEX ise bir kez daha
        WAVEFORMATEXTENSIBLE (24-bit/8-kanal için zorunlu) ile dene → hâlâ yoksa
        cihaz desteklemiyor StreamError{UnsupportedFormat}
3. IAudioClient::Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE,
        AUDCLNT_STREAMFLAGS_EVENTCALLBACK | AUDCLNT_STREAMFLAGS_NOPERSIST,
        hnsBufferDuration, 0, &wfex, nullptr)
4. GetService(IAudioClient2/IAudioRenderClient) → buffer erişimi
5. İade: negotiated_format() = Initialize'a verilen wfex (hash testi bunu okur)
6. Event handle ile render thread → callback (zero-alloc) → Stop/Close
```

**Kritik kural:** `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` ve `SRC_DEFAULT_QUALITY` **asla kullanılmaz** (resampler devreye girerse bit-perfect kırılır). Go/CLSID yardımcıları yerine COM vtab çağrıları (header sözleşmesi: `neva/core/backends/wasapi/ComPtr.h`).

### §13.2 ASIO2 (P0 — Windows, DAW yolu)

```text
1. asioDriverInfo() → ASIO driver listesi (ASIO SDK)
2. driver->init(device) → getChannels / getSampleRate / getBufferSizeSizes
3. bufferSwitchTimeInfo (callback) → neva render callback (double/int32 ibuket)
4. bit-perfect: ASIO hedef = donanımın native formatı (örn. 32f/192k) —
   sample rate getSampleRate() ile hedef eşleşmiyorsa REDDET (asla upsample)
5. Latency: minSupportedLatency ile P50 hedef ≤ 5 ms (ASIO varsa), P99 ≤ 10 ms
```

**Lisans kapısı:** ASIO SDK (ADR-107 §2.4) — `K4` onayı olmadan build CI'da bu hedef **dışarıda** (`NEVA_WITH_ASIO=OFF`).

### §13.3 PipeWire (P1 — Linux)

```text
1. pw_main_loop_new → pw_context_connect
2. pw_stream_new_simple(node, {PW_KEY_MEDIA_TYPE=Audio,
     PW_KEY_MEDIA_CATEGORY=Playback, PW_KEY_FORMAT_DSP=format=DSP,rate=192000,
     channels=2, position=FL,FR})
3. param: SPA_PARAM_EnumFormat → F32 / 192000 / stereo  (F32P = 32-bit float ✅)
4. Process callback: pw_stream_dequeue_buffer → render → queue_buffer
5. bit-perfect doğrulama: PipeWire node'unu doğrudan ALSA `hw:` çıkışına bağla
   (resample-rate otomatik olmasın: `PW_LATENCY` + node ayarı `⚠️ cihaz testi gerekli`)
```

### §13.4 ALSA (P1 — fallback)

```text
1. snd_pcm_open(&pcm, "hw:0,0", SND_PCM_STREAM_PLAYBACK, 0)   ← plug/../default DEĞİL
2. snd_pcm_hw_params_set_rate_resample(pcm, params, 0)          ← resampler KAPALI
3. snd_pcm_hw_params_set_format(S32_LE veya FLOAT_LE) + set_rate_near(192000)
4. snd_pcm_hw_params / snd_pcm_prepare → writei loop → XRUN: snd_pcm_recover
5. negotiated: snd_pcm_hw_params_get_rate / get_format doğrulaması
```

### §13.5 CoreAudio (P3 — macOS, PLANNED)

```text
1. AudioObjectGetPropertyData(kAudioHardwarePropertyDefaultOutputDevice)
2. kAudioStreamPropertyAvailablePhysicalFormat → gerçek donanım formatı
3. kAudioStreamPropertyPhysicalFormat = kAudioStreamPropertyActualFormat
   (bit-depth/swap toggle — 192k/32f hedefi donanım desteğiyle sınırlı)
4. AudioDeviceCreateIOProcID → render IOProc
```
`⚠️ VERIFICATION REQUIRED`: HAL toggle'ının loopback/örtük resampler içerip içermediği.

### §13.6 Null backend (P0 — test)

Formatı **olduğu gibi** kabul eder, callback'e frame üretir (sine/noise üreteci), çıktıyı hash'e yazar. Tüm CI bit-perfect matrisi bu backend üzerinden çalışır (cihaz bağımsız determinizm).

---

## §14 Dizin Ağacı ve Dosya Sözleşmeleri (PLANNED)

```text
app-cpp-projects/neva-engine/
├── CMakeLists.txt                    ← üst + `neva_add_executable()` fonksiyonu
├── cmake/
│   ├── neva-toolchains.cmake         ← MSVC/GCC/Clang + ARM64
│   └── neva-options.cmake            ← NEVA_WITH_ASIO / WITH_VST3 / WITH_MCP
├── core/
│   ├── include/neva/
│   │   ├── Types.h                   ← AudioFormat, BackendKind, StreamError, Expected
│   │   ├── IAudioBackend.h           ← §3.2 arayüz
│   │   ├── BackendRegistry.h         ← §3.3 fallback zinciri
│   │   ├── RenderCallback.h          ← fn_ptr + user_data (std::function YASAK RT yolda)
│   │   ├── spsc_ring.h               ← kuyruk (mevcut HEAD içeriğinden — bkz §19)
│   │   └── dsp/{biquad.h, eq_chain.h, eq_grids.h, ...}
│   ├── src/
│   │   ├── backends/{null,wasapi,asio,wdmks,pipewire,alsa,coreaudio}/
│   │   ├── dsp/{eq,reverb,limiter,wide}/ + *_params.h
│   │   └── io/{wav_reader.h, wav_writer.h}
│   └── tests/                        ← contract + unit (Catch2)
├── apps/
│   ├── player/ · host/ · eq/ · matrix/ · mcp/ · remote/ · <kullanıcı>/ 
│   └── CMakeLists.txt                ← tek satır hedef kaydı
├── plugins/vst3/                     ← wrap (lisans kapısı)
└── docs/                             ← bu vault dosyalarının kopyası DEĞİL, bağ referansı
```

---

## §15 CMake İskeleti (sözleşme — REVİZYON YOK)

```cmake
cmake_minimum_required(VERSION 3.31)
project(neva VERSION 0.1.0 LANGUAGES CXX)
set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

option(NEVA_WITH_ASIO  "ASIO2 backend (ASIO SDK lisans onayı gerekli)" OFF)
option(NEVA_WITH_VST3  "VST3 plugin wrap" OFF)
option(NEVA_WITH_MCP   "neva-mcp AI sunucu" ON)

add_library(neva_core STATIC ...)        # core/src/** + header-only dsp
target_compile_definitions(neva_core PRIVATE NEVA_RT_NOALLOC=1)

function(neva_add_executable)
  # NAME · SOURCES · LINK · PLATFORMS · ENTRY
  add_executable(${NAME} ${SOURCES})
  target_link_libraries(${NAME} PRIVATE neva_core)
endfunction()

neva_add_executable(NAME neva-player SOURCES apps/player/main.cpp)
neva_add_executable(NAME neva-host    SOURCES apps/host/main.cpp)
# ... kullanıcı yeni hedefleri yalnız bu dosyaya ekler
```

---

## §16 Test Matrisi (CI kapısı)

| Os | Arch | Backend | Format matrisi | Ölçüm |
|---|---|---|---|---|
| Windows | x64 | Null | 44.1k/48k/96k/192k × 16/24/32f | hash eşitliği (her hücre) |
| Windows | x64 | WASAPI exclusive | donanım destekli alt küme | hash + negotiated == target |
| Windows | x64 | ASIO (`NEVA_WITH_ASIO=ON`) | donanım native | hash + latency p50/p99 |
| Windows | ARM64 | WASAPI exclusive | destekli alt küme | hash |
| Linux | x64 | Null + ALSA `hw:` + PipeWire | 48k/192k × 24/32f | hash + 30 dk xrun=0 |
| Linux | ARM64 (RPi5) | PipeWire | 192k × 32f | hash + 30 dk xrun=0 |
| macOS | arm64 | CoreAudio (P3) | `⚠️ PLANNED` | hash (faz 6) |

**Kabul:** her hücrede `sha256(çıktı) == sha256(girdi)` **VE** `negotiated_format() == hedef` **VE** 30 dk `xrun == 0`.

---

## §17 `neva-remote` IPC Sözleşmesi (VoiceMeeterRemote emsal)

| Alan | Karar |
|---|---|
| Taşıma | Windows: **named pipe** · Unix: **Unix domain socket** (aynı framing) |
| Protokol | uzunluk-prefixed JSON (kolay AI/MCP köprüsü) — `⚠️ protobuf tartışması ADR-107 açık madde` |
| Komut yüzeyi | `get_status` · `set_gain(strip,db)` · `set_eq(bands[])` · `set_reverb(preset)` · `set_limiter(threshold)` · `set_output(device)` · `list_devices` |
| Yetki | varsayılan salt-okunur; yazma için `--allow-write` bayrağı (MCP güvenliği) |
| Sürüm | `hello {proto:1, neva:"0.1.0"}` — uyuşmazlıkta red |

---

## §18 Sürüm & Dağıtım Matrisi

| Artefakt | Windows | Linux | Not |
|---|---|---|---|
| `neva-core` (statik) | `neva-core.lib` | `neva-core.a` | tek sürüm numarası (`CMake project(VERSION)`) |
| CLI | `neva-player.exe` | `neva-player` | ilk yayın hedefi |
| Host | `neva-host.exe` | `neva-host` | |
| Plugins | `neva.vst3` | `neva.vst3` | lisans kapısı |
| MCP | `neva-mcp.exe` | `neva-mcp` | dil seçimi açık |
| Sürücü | `.sys` paketi (EV/Attestation) | — | ayrı iş, ayrı sürüm |
| Sanal cihaz | `.sys`+`.inf`+`.cat` | `snd-aloop`/null-sink | research raporu planı |

**Kural:** her exe **aynı** versiyon dosyasından (`version.h.in`) beslenir → parçalanmış sürüm yasak.

---

## §19 Mevcut `neva-engine` Depo Durumu (disk kanıtı — önemli)

`.ai/` dışı gözlem (bu rapor kapsamı dışında, yalnız bildirim):

- Git HEAD'de `app-cpp-projects/neva-engine/` **izlenen** dosyalar mevcut (`CMakeLists.txt`, `include/neva/{biquad,eq_chain,eq_grids,iaudio_backend,icy_parser,neva_session,spsc_ring}.h`, `src/*`, `tests/*`) — yani **önceden yazılmış bir iskelet var** (`null_backend.cpp`, `wasapi_backend.cpp` dâhil).
- Çalışma ağacında bu dosyalar **silinmiş görünüyor** (`git status: D`) ve içerik `C:\www\coremusic.net\--\neva-engine\` altında (ayrıca `Css copy 2/3`, `K0100-identitiy`, `K020-licence` kopyaları da `--/` altında).
- **Bu rapor / bu oturum bu taşımayı YAPMADI.** Yıkıcı düzeltme onay gerektirir; olası kurtarma `git restore app-cpp-projects/neva-engine` (HEAD blob'ları) **veya** `--/neva-engine` içeriğini karşılaştırıp taşımak.
- §14'teki PLANNED iskelet, HEAD'deki mevcut iskeletle **uyumlu adlandırma** kullanır (`include/neva/*.h`) → çakışma yok; mevcut dosyalar §3.2 arayüzüne evrilecektir.

---

## §20 İlişkili kararlar ve Açık Maddeler Özeti

**İlişkili:** ADR-107 · ADR-017 · `.ai/electronic/dsp/` (10) · `.ai/electronic/drivers/` (10) · `.ai/research/{reverse-voicemeeter-fxsound, sanal-audio-cihaz-arastirmasi, fxsound-app-source-analizi}-2026-10-22.md` · `.ai/architecture/neva-katmanli-mimari-revizyon.md` §4.3.

**Açık maddeler (5):** U1 ASIO SDK lisans metni · U2 VST3 SDK sürüm/lisans · U3 CoreAudio HAL toggle · U4 PipeWire 192k `hw:` kanıtı · U5 `neva-mcp` dil seçimi.
**Karar bekleyen (2):** K4 lisans kapısı (GPLv3 vs ticari) · U5 MCP dili.

---

## §21 Gerçek Zamanlı Bütçe ve Gecikme Matematiği

### §21.1 Formüller

```text
gecikme (ms) = buffer_frames / sample_rate * 1000
roundtrip   = giriş buffer + çıkış buffer + DONANIM tamponu + (varsa) DMA gecikmesi
xrun riski  > callback süresi / buffer süresi  (CPU yükü)
```

### §21.2 Tablo (hedefler)

| Örnekleme | 32 örnek | 64 | 128 | 256 | 512 | 1024 |
|---|---|---|---|---|---|---|
| 44,1 kHz | 0,73 ms | 1,45 | 2,90 | 5,80 | 11,61 | 23,22 |
| 48 kHz | 0,67 ms | 1,33 | 2,67 | 5,33 | 10,67 | 21,33 |
| 96 kHz | 0,33 ms | 0,67 | 1,33 | 2,67 | 5,33 | 10,67 |
| 192 kHz | **0,17 ms** | 0,33 | **0,67** | 1,33 | 2,67 | 5,33 |

**Hedefler (ADR-107 ile):**
- Exclusive yolu: **128 örnek @48k = 2,67 ms** buffer → P50 round-trip **≤ 5 ms**, P99 **≤ 10 ms**.
- 192 kHz'te 4096 örnek (VoiceMeeter VAIO `Max Latency`) = 21,33 ms → **Neva sanal cihazı bu sınırı aşmamalı**, hedef ≤ 1024 (5,33 ms).
- 192 kHz × 64 örnek = 0,33 ms → **callback bütçesi**: DSP zinciri (31-band EQ + reverb + limiter) bu süreyi aşarsa xrun.

### §21.3 Callback Bütçesi Kuralı (RT yol)

```text
callback bütçesi (µs) ≈ buffer_frames / sample_rate * 1e6 * 0,7   (%70 doluluk tavanı)
128 @48k  → 1 866 µs bütçe → hedef kullanımda ≤ 1 300 µs
64  @192k →   333 µs bütçe → hedef kullanımda ≤   233 µs   ← kritik
```

**Ölçüm zorunlu:** `neva-diag --json` her callback'te süre sayacı tutar (p50/p99), çıktı `xrun` sayacı.

---

## §22 Hata Sözleşmesi (Backend ortak davranışı)

| Durum | Backend davranışı | Üst katman |
|---|---|---|
| Cihaz bulunamadı | `StreamError{NotFound}` — **sessiz fallback YOK** | Kullanıcıya açık cihaz listesi |
| Format desteklenmiyor | `StreamError{UnsupportedFormat}` + **desteklenen format listesi** | Yakalama/yeniden deneme seçimi |
| Exclusive reddedildi | K5 zincirinde **sonraki** backend'e geç + **log** (`reason=AccessDenied`) | Karar izlenebilir |
| Xrun/underrun | Sayaç + **otomatik buffer büyütme (yalnız non-RT thread)** | Uyarı UI |
| Backend açılamıyor | Son çare `Null` **yalnız test/CI'da** — üretimde `Fatal` | Başlatma hatası |
| Uygulama kapanışı | `stop()` → `close()` sırası zorunlu (RAII `Stream` sarmalayıcısı) | — |

**Yasak:** RT callback içinde exception (`noexcept`), `throw`, `new/delete`, lock, loglama (yalnız lock-free sayaç yazımı).

---

## §23 Gözlemlenebilirlik (metrics)

| Metrik | Tip | Kaynak |
|---|---|---|
| `neva_callback_duration_us{p50,p99}` | histogram | backend callback |
| `neva_xrun_total` | counter | ALSA/PipeWire/WASAPI event |
| `neva_negotiated_rate` / `_bitdepth` | gauge | `negotiated_format()` |
| `neva_backend_info{kind,device}` | gauge | registry |
| `neva_bitperfect_hash_pass` | gauge (0/1) | CI + teşhis |
| `neva_buffer_underrun_total` | counter | ring (SPSC) |

Çıktı: `neva-diag --json` (fxdiag emsal) + opsiyonel `neva-mcp` resources.

---

## §24 Bellek & Tahsis Kuralları (vault `electronic/dsp/` ile ortak)

| # | Kural | Denetim |
|---|---|---|
| 1 | Tüm DSP buffer'ları **open() anında** allocate edilir | kod incelemesi |
| 2 | RT thread'de `malloc/new/std::function/string` **yasak** | CI static check (`NEVA_RT_NOALLOC`) |
| 3 | Kuyruk: **SPSC ring** (`spsc_ring.h` — mevcut HEAD dosyası) | birim test (wrap-around) |
| 4 | Hızlı yol: SSE/AVX/NEON **yalnız iç** (`vectorization` kuralı) | doğrulama testi |
| 5 | DSP state `alignas(32)` | — |

---

## §25 Sözlük

| Terim | Anlam |
|---|---|
| **bit-perfect** | Girdi → çıktı byte-birebir; resampler/mixer yok |
| **exclusive** | Uygulamanın tek başına donanımın tamponunu kullanması (WASAPI/ALSA) |
| **loopback** | Başka uygulamanın sesini yakalama (FxSound modeli — bit-perfect değil) |
| **xrun** | Buffer taşması/tıkanması (ALSA `EPIPE`) — ses çatlaması |
| **VAD** | Virtual Audio Driver (sanal ses sürücüsü) |
| **WaveRT / PortClass** | Windows ses miniport modelleri (VoiceMeeter `.sys` kanıtı) |
| **F32P** | PipeWire formatı: 32-bit float, planar |
| **negotiated_format** | Cihazın gerçekten kabul ettiği format |
| **K5 zinciri** | ASIO → WASAPI-excl → WDM-KS → ALSA/PipeWire → CoreAudio → Null |

---

## §26 Onay Kapısı Özeti (bu ADR'nin bekledikleri)

| # | Soru | Bekleyen |
|---|---|---|
| 1 | ADR-107 onayı (platform + paketleme) | Kullanıcı |
| 2 | K4: ASIO SDK GPLv3 mü, ticari lisans mı? | Kullanıcı + lisans okuması |
| 3 | VST3 SDK lisansı | Araştırma (U2) |
| 4 | `neva-mcp` dili: Go / Node / PHP bridge | Kullanıcı (U5) |
| 5 | `--/neva-engine` + silinmiş `app-cpp-projects/neva-engine` çözümü | Kullanıcı (§19) |

---

## §27 Sprint-0 Hazırlık Kontrol Listesi (kod yazmadan önce)

| # | Kontrol | Kanıt | Durum |
|---|---|---|---|
| 1 | `app-cpp-projects/neva-engine` çalışma ağacı durumu çözüldü (§19) | `git status` + kurtarma kararı | ⏳ kullanıcı onayı |
| 2 | ASIO SDK indirildi + lisans metni okundu | LICENSE dosyası | ⏳ |
| 3 | VST3 SDK sürümü + lisansı belirlendi | LICENSE.md | ⏳ |
| 4 | Hedef derleyici sürümleri sabitlendi (MSVC 19.4x / GCC 14 / Clang 18) | `--version` çıktısı | ⏳ |
| 5 | CI OS matrisi onaylandı (§16 tablosu) | `.github/workflows` yasası | ⏳ |
| 6 | `neva-mcp` dili seçildi (U5) | kullanıcı kararı | ⏳ |
| 7 | Test framework seçimi (Catch2/GTest) | `.ai/projects/NevaEngine/` okuması | ⏳ |
| 8 | Bit-perfect WAV referans seti üretildi (44.1/48/96/192 × 16/24/32f) | SHA-256 manifest | ⏳ Faz 1'de |

---

## §28 Karar Kaydı Etkileşim Tablosu (güncel)

| ADR | Durum | Bu dokümanla ilişkisi |
|---|---|---|
| **ADR-107** | **draft** (`.ai/.decisions/draft/`) | Bu dokümanın kararları ADR-107'ye taşındı |
| ADR-017 | accepted (frozen — dokunulmaz) | DSP hardware mode + ASIO/WASAPI fallback ile uyumlu |
| ADR-014 / ADR-040 | accepted | MySQL kapsamı: novasearch hariç (mysql raporu § kapsam) |
| ADR-094 / 099 / 104 | accepted | PHP katman düzeltmesi + PageRouter God-class bölme — katmanlı revizyon §3 ile ilişkili |
| ADR-097 / 098 | draft (K-space) | **Bu ADR'ler dokunulmaz**; K-space çelişkisi yalnız raporlandı |

**Eski UNKNOWN durumu (§19 U1–U5 özeti — güncel hâli):**

| Madde | Eski | Güncel |
|---|---|---|
| U1 ASIO SDK lisans metni | `⚠️` | `⚠️` hâlâ açık (Sprint-0 #2) |
| U2 VST3 SDK sürüm/lisans | `⚠️` | `⚠️` hâlâ açık (Sprint-0 #3) |
| U3 CoreAudio HAL toggle | `⚠️` | `⚠️` hâlâ açık (Faz 6) |
| U4 PipeWire 192k `hw:` kanıtı | `⚠️` | `⚠️` hâlâ açık (Faz 3 cihaz testi) |
| U5 `neva-mcp` dili | Go/Node/PHP | `⚠️` **kullanıcı kararı bekliyor** |
| — | — | **U6 eklendi:** FxSound forumunun "exclusive kullanır" iddiası kod kanıtına aykırı (kod lehine çözüldü; çıkış yönü tam tarama `⚠️`) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-22
**Mode:** Red Team · Human Mode · Truth Mode
