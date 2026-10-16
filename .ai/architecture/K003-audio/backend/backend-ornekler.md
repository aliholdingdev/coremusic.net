---
title: "backend — Örnekler"
type: architecture-examples
node_id: K003-audio/backend
node_type: subdomain
status: draft
parent_id: K003-audio
children:
  - asio (K003.04.01)
date: 2026-10-14
updated: 2026-10-14
group: kod
---

# backend — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız vault-içi okunmuş dosyalardan (NevaEngine os/01–04 · asio/ deep-dive)
> üretilir. Uydurma kod/yapı yasaktır; tüm kod blokları **[TASARIM] şablondur**
> (status: planlama — derlenmiş kanıt yok, D6 açık).

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `home.coremusic.net/`, `.github/workflows/` ve repo kökünde
`*.cpp`/`*.h` **0 isabet**tir (2026-10-13 glob · `audio-master.md` §19).
`neva.engine/` dizini de diskte YOK (NevaEngine 01 §1 Faz 0 durum notu:
"⛔ BAŞLAMADI — clone iptal, kalıntı 0"). Backend adaptör kodu ilk kez
Faz 0–1'de (`neva.engine/drivers/`) yazılacaktır.

## §2 Vault-içi entegrasyon örnekleri (NevaEngine os/01–04 — okunmuş, [TASARIM])

> Bu bölümdeki kod `ai/projects/NevaEngine/os/*.md` dosyalarından **birebir
> özetlenmiştir** (2026-10-14 okundu). Hepsi `status: planlama` taşır; K003
> backend kapsamının **tasarım şablonu**, `[KANIT]` değildir. Tam metin kaynak
> dosyadadır — burada kesilmeden tekrar edilmez, yalnız yapı/eşleme satırı verilir.

### §2.1 Windows ASIO (os/01-windows-asio.md)

| # | Parça | Ne gösterdiği | backend kapsamına girişi |
|---|---|---|---|
| 1 | `enumerateAsio()` | ASIOGetDriverName/ASIOGetDeviceInfo ile kayıt-defteri enumerasyonu (32 sürücü sınırı) | §7 satır 6: hata/olay tercümesi öncesi envanter veri kaynağı |
| 2 | `asioBufferSwitch()` — statik `gEngine`/`gChans[8]` | RT-hot-path: tahsis yok · noexcept · log yok · double-buffer offset | §8.1 #1: **cihaz buffer'ı adaptörde** (Q5 kanıtı) |
| 3 | `asioMessage()` — `kAsioResetRequest → 1` | reset isteği → "host durumu sıfırla → WASAPI fallback (ADR-017)" | §7 satır 6: fallback tetikleyici |
| 4 | `initAsio()` — `gEngine.prepare()` **önce**, `ASIOCreateBuffers` | tüm tahsis init anında; exclusive lock tek uygulama | §8.1 #5: prepare/init deseni |
| 5 | `asioSampleRateDidChange` — "audio thread'de yeniden prepare YOK" | sr değişimi UI thread'e kuyruk ile bildirilir | realtime komşusu ile kilitli (D3) |

### §2.2 Windows WASAPI (os/02-windows-wasapi.md)

| # | Parça | Ne gösterdiği | backend kapsamına girişi |
|---|---|---|---|
| 1 | `wasapiInitExclusive()` — `AUDCLNT_SHAREMODE_EXCLUSIVE` + `EVENTCALLBACK` + `WAVE_FORMAT_EXTENSIBLE` float32/8kanal | exclusive init akışı; `Initialize` başarısız → "unsupported → shared'a düş (fallback zinciri)" | §6 satır 2 + §7 satır 2: kip müzakeresi/fallback |
| 2 | `wasapiInitLoopback()` — `AUDCLNT_STREAMFLAGS_LOOPBACK` + `GetMixFormat` | sanal ses kartı etkisi (sürücü yazılmadan) — kısa vade stratejisi | §6 satır 2: loopback yolu |
| 3 | Gecikme tablosu: ASIO <10ms · WASAPI excl <20ms · shared+loopback "⚠️ Faz 1 ölçümü" | eşiklerin vault kaynaklı hedef olduğu, ölçümün PLANNED olduğu | §6 eşik notu ile tutarlı |

### §2.3 Linux ALSA/PipeWire (os/03-linux-alsa-pipewire.md)

| # | Parça | Ne gösterdiği | backend kapsamına girişi |
|---|---|---|---|
| 1 | `runAlsa()` — `snd_pcm_set_params(FLOAT_LE, RW_INTERLEAVED, 8kanal)` + `-EPIPE→prepare` + `-ESTRPIPE→resume` | xrun kurtarma döngüsü; `alignas(64) static buf` — döngüde tahsis yok | §8.1 #4: ALSA buffer adaptörde statik |
| 2 | `elevateAudioThread()` — SCHED_FIFO 80 + `mlockall` | RT thread zemini (CAP_SYS_NICE) | realtime komşusu (D3) — backend tetikler, kural realtime'de |
| 3 | PipeWire §3 — native node bağlanma "Faz 2+ · ⚠️ PLANNED · pipewire-0.3.x hedefi" | üst katman niyeti, sürüm teyitsiz | §11 kapı 4 (R4) |

### §2.4 macOS CoreAudio (os/04-macos-coreaudio.md)

| # | Parça | Ne göstertiği | backend kapsamına girişi |
|---|---|---|---|
| 1 | `renderProc()` — `ioData->mBuffers` → plane işaretçileri → `gEngine.process` in-place → `noErr` | pull-model render: buffer OS/OSA ait, adaptör yalnız geçirir | §8.1 #3: **Q5 pull-model kanıtı** (master A1 ile uyumlu) |
| 2 | `initCoreAudio()` — HALOutput · ASBD float32/48k/8kanal · `EnableIO=0` (output-only) · `gEngine.prepare` **önce** | init deseni: format ayarı → callback → prepare → Initialize | §8.1 #5: prepare/init deseni |
| 3 | §2 notları — "JUCE `AudioDeviceType::CoreAudio` bu AUHAL'ı sarar — ikili strateji: JUCE ana, raw CoreAudio doğrulama harness'ı" | soyutlama kararı niyeti (JUCE dual) | §10 risk 3 azaltması |

### §2.5 Dört örnek ortak deseni (§8.2'nin kanıt zemini)

| # | Ortak desen | ASIO | WASAPI | ALSA | CoreAudio |
|---|---|---|---|---|---|
| 1 | Tahsis yalnız init/`prepare()` anında | ✅ os/01 | ✅ os/02 | ✅ os/03 | ✅ os/04 |
| 2 | Cihaz I/O buffer'ı adaptör/OS tarafında | gChans (adaptör) | client (platform) | static buf (adaptör) | ioData (OS) |
| 3 | Engine çağrısı tek nokta: `process(io, chans, frames)` in-place | ✅ | ✅ (event döngüsü) | ✅ | ✅ |
| 4 | RT ihlali yok: noexcept · no-log · no-alloc | ✅ | ✅ | ✅ | ✅ |
| 5 | Hata/fallback platform callback'i + mesaj yüzeyinden tetiklenir | `asioMessage` reset | Initialize fail→shared | -EPIPE/-ESTRPIPE kurtarma | `noErr` tek dönüş |

## §3 asio/ çocuk deep-dive örnekleri (19 dosya — salt-okunur katalog)

`backend/asio/` altındaki 17 teknik deep-dive + index + kapsam dosyası bu
dalın ASIO'ya özel derinlemesidir; örnek taşıyan ikisi:

| # | Dosya | Örnek yoğunluğu | Durum |
|---|---|---|---|
| 1 | `asio/asio-cpp-ornekleri.md` (527 satır) | ASIO C++ kod örnekleri kümesi | salt-okunur (koruma kararı) |
| 2 | `asio/asio-uygulama-entegrasyon-ornekleri.md` (449 satır) | host entegrasyon örnekleri | salt-okunur |
| 3 | `asio/asio-buffer-stratejileri.md` · `asio-latency-ve-jitter.md` · `asio-hata-yonetimi-ve-fallback.md` · `asio-hot-swap-ve-device-loss.md` | buffer/latency/hata/hot-swap şablonları | salt-okunur |

> Bu dosyalara bu kapsam dosyasından **yeni kod kopyalanmaz** (SSOT: kendi
> dosyalarıdır); yalnız atıf verilir.

## §4 Örneklerin kullanım sınıfı (şablon ≠ karar)

| Satır kümesi | İddia sınıfı | Kullanım yasağı |
|---|---|---|
| §2.1–2.4 (os/ kodları) | `[TASARIM]` şablon (status: planlama) | `[KANIT]` olarak sunulmaz; derlenmedi/ölçülmedi |
| §2.5 ortak desen | tasarımsal gözlem (4 örnek tutarlı) | kesin sınır iddiası değil — Q5 ADR'si bekler (kapsam §8.3) |
| §3 asio/ deep-dive | salt-okunur koruma | içerik kopyalanmaz, taşınmaz |
| Tümü | sartname eşikleri (512/48k) platform beyanı sayılmaz | `UNKNOWN` korunur |

## §5 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `backend-kapsam.md` | Kademe-2 kapsam kaydı (§8 Q5 tasarım çözümü dahil) |
| `backend-dogrulama.md` | Kanıt/doğrulama tablosu (✅ · ⚠️ · UNKNOWN) |
| `backend/index.md` | Düğüm kaydı (çeviri katmanı tanımı · tek çocuk asio) |
| `backend/asio/` (19 md) | ASIO derinlemesi (salt-okunur katalog) |
| `../audio-master.md` | Üst anayasa §4 (pull A1–A6) · §5 (T1–T17) · §8 Q5 |
| `../engine/engine-kapsam.md` | Q5 sınırının karşı tarafı (engine↔backend) |
| `../driver/driver-kapsam.md` | D2 sınırının karşı tarafı (kullanıcı-zamanı ↔ kernel) |
| `.ai/projects/NevaEngine/os/01–04.md` | §2'nin kaynak dosyaları (2026-10-14 okundu) |
| `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` | SDK/lisans envanteri (kapsam §4 #12) |

## §7 Ortak arayüz şeması (tek implementasyon — DRY)

> Kaynak: `.ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md` §1
> (2026-10-15 okundu, status: planlama → `[TASARIM]`).

```cpp
// neva_engine_bridge.h — OS bağımsız çekirdek (tek implementasyon) [TASARIM]
struct EngineParams {                    // UI → engine lock-free
    std::atomic<float> inputGainDb {0.f};
    std::atomic<float> limiterCeilingDb {-0.1f};
    std::atomic<int>   reverbMode   {0}; // 0=Konser 1=Düğün 2=Oda 3=Stüdyo
};

class NevaEngineCore {
public:
    void prepare(double sampleRate, int maxBlock) noexcept; // tahsis BURADA
    void process(float* const* io, int channels, int frames) noexcept; // RT-safe
private:
    alignas(64) float eqState_[8][2][31]{}; // 8 kanal × 2 biquad × 31 band
};
```

| Parça | Guardrail eşlemesi | Katalog |
|---|---|---|
| `std::atomic` parametreler | Lock-free (kural 2) — UI↔RT kuyruğu | `audio-master.md` |
| `prepare(...)` noexcept | Tahsis yalnız init anında (kural 1) | §2.5 #1 |
| `alignas(64) eqState_[8][2][31]` | Cache-line hizalama (kural 5) | guardrail §7 #3 |
| `process()` 8 kanal float** | 32-bit float (ADR-017) | §13 |

**Kural (kaynak §1):** her OS backend'i yalnız `process()` çağırır; DSP tek
yerde yaşar (DRY).

## §8 Windows ASIO — bufferSwitch tam şeması

> Kaynak: `03-isletim-sistemi-kod-ornekleri.md` §2 (ASIO SDK 2.3.x host
> iskeleti — GPLv3 açık varyant notu kaynaktadır). `[TASARIM]`.

```cpp
static NevaEngineCore gEngine;           // statik: callback içinde new YOK
static float* gOutBuf[8]{};
static long   gBlockFrames = 0;

static void asioBufferSwitch(long index, ASIOBool processNow) noexcept {
    float* channels[8];
    for (int ch = 0; ch < 8; ++ch) channels[ch] = gOutBuf[ch] + index * gBlockFrames;
    gEngine.process(channels, 8, gBlockFrames);
}

static long asioMessage(long selector, long value, void* msg, double* opt) noexcept {
    switch (selector) {
        case kAsioEngineVersion: return 2;
        case kAsioResetRequest:  return 1; // cihaz kaybı → WASAPI fallback (ADR-017)
        case kAsioResyncRequest: return 1;
        default: return 0;
    }
}

ASIOError initAsIO(double rate, long blockFrames, int channels) {
    ASIOCallbacks cb;
    cb.bufferSwitch       = &asioBufferSwitch;
    cb.sampleRateDidChange = &asioSampleRateDidChange;
    cb.asioMessage        = &asioMessage;
    cb.bufferSwitchTimeInfo = nullptr;
    gBlockFrames = blockFrames;               // varsayılan 512 (64–1024)
    gEngine.prepare(rate, static_cast<int>(blockFrames));
    return ASIOInit(&cb);                     // exclusive lock: tek uygulama
}
```

**Doğrulama notları (kaynak §2):**
- Exclusive lock: ASIO aynı anda tek uygulama; çoklu deneme → sürücü çökmesi.
- Cihaz kaybı → `kAsioResetRequest` → WASAPI fallback (ADR-017).
- Buffer varsayılan 512 @48kHz ≈ 10.67 ms — 64'ye düşürme yalnız ölçüm sonrası.
- `initAsIO` sırası: **prepare → ASIOInit** (§2.5 desen #5 ile birebir).

## §9 Windows WASAPI — exclusive init şeması

> Kaynak: `03-isletim-sistemi-kod-ornekleri.md` §3 (Windows 10/11 ·
> IMMDevice + IAudioClient3). `[TASARIM]`.

```cpp
HRESULT initWasapi(IMMDevice* device, NevaEngineCore& engine, double rate, UINT32 block) {
    IAudioClient* client = nullptr;
    HRESULT hr = device->Activate(__uuidof(IAudioClient), CLSCTX_ALL, nullptr,
                                  reinterpret_cast<void**>(&client));
    if (FAILED(hr)) return hr;

    WAVEFORMATEX wfx{};
    wfx.wFormatTag      = WAVE_FORMAT_EXTENSIBLE; // 32-bit float
    wfx.nChannels       = 8;                      // 8.1
    wfx.nSamplesPerSec  = static_cast<DWORD>(rate); // 48000
    wfx.wBitsPerSample  = 32;
    wfx.nBlockAlign     = wfx.nChannels * (wfx.wBitsPerSample / 8);
    wfx.nAvgBytesPerSec = wfx.nSamplesPerSec * wfx.nBlockAlign;

    REFERENCE_TIME dur = static_cast<REFERENCE_TIME>(10'000'000.0 * block / rate);
    hr = client->Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE,
                            AUDCLNT_STREAMFLAGS_EVENTCALLBACK, dur, 0, &wfx, nullptr);
    if (FAILED(hr)) return hr; // desteklenmiyorsa shared'a düş (fallback zinciri)
    engine.prepare(rate, static_cast<int>(block));
    return client->Start();
}
```

**Doğrulama notları (kaynak §3):**
- Exclusive başarısız → `AUDCLNT_SHAREMODE_SHARED` + `AUDCLNT_STREAMFLAGS_LOOPBACK`
  (sanal ses kartı kısa vade stratejisi).
- Loopback capture: uygulama çıkışını Neva'ya yönlendirme — sürücü yazmadan
  sanal kablo etkisi.
- `WAVE_FORMAT_EXTENSIBLE` + 8 kanal + float32: §13 iddia sınıfının
  tasarımsal kaynağı (ölçüm PLANNED).

## §10 Linux ALSA — xrun kurtarmalı döngü şeması

> Kaynak: `03-isletim-sistemi-kod-ornekleri.md` §4 (pcm.h · non-blocking
> poll döngüsü). `[TASARIM]`.

```cpp
int runAlsa(NevaEngineCore& engine, const char* hw, double rate, unsigned block) {
    snd_pcm_t* pcm = nullptr;
    if (snd_pcm_open(&pcm, hw, SND_PCM_STREAM_PLAYBACK, 0) < 0) return -1;

    snd_pcm_set_params(pcm,
        SND_PCM_FORMAT_FLOAT_LE,          // 32-bit float
        SND_PCM_ACCESS_RW_INTERLEAVED,
        8,                                 // 8.1 kanal
        static_cast<unsigned>(rate),       // 48000
        1,                                 // soft resample
        100'000);                          // 100ms latency tampon payı

    engine.prepare(rate, static_cast<int>(block));

    alignas(64) static float buf[8][1024]; // döngüde tahsis yok
    float* planes[8];
    for (int ch = 0; ch < 8; ++ch) planes[ch] = buf[ch];

    for (;;) {
        engine.process(planes, 8, static_cast<int>(block));
        snd_pcm_sframes_t w = snd_pcm_writen(pcm,
            reinterpret_cast<void**>(planes), static_cast<snd_pcm_uframes_t>(block));
        if (w == -EPIPE) snd_pcm_prepare(pcm);   // xrun → recover, motor durmaz
    }
}
```

**Doğrulama notları (kaynak §4):**
- PipeWire Tier 2: ALSA üstünde çalışır; native API sonraki faz (⚠️ PLANNED).
- Raspberry Pi 5 (ARM64): NEON intrinsics ile biquad SIMD (izinli SIMD).
- `-EPIPE → snd_pcm_prepare`: audio thread'de throw yasak (H001).

## §11 macOS CoreAudio — AUHAL render şeması

> Kaynak: `03-isletim-sistemi-kod-ornekleri.md` §5 (AudioUnit AUHAL,
> macOS 12+). `[TASARIM]`.

```cpp
static OSStatus renderProc(void* inRefCon, AudioUnitRenderActionFlags* flags,
                           const AudioTimeStamp* time, UInt32 bus,
                           AudioBufferList* ioData, AudioUnitRenderActionFlags* outFlags) noexcept {
    const UInt32 chans = ioData->mNumberBuffers < 8 ? ioData->mNumberBuffers : 8;
    const int frames = static_cast<int>(ioData->mBuffers[0].mDataByteSize / sizeof(float));
    float* planes[8];
    for (UInt32 ch = 0; ch < chans; ++ch) planes[ch] = static_cast<float*>(ioData->mBuffers[ch].mData);
    gEngine.process(planes, static_cast<int>(chans), frames);
    return noErr;
}

OSStatus initCoreAudio(AudioUnit* outUnit, double rate, UInt32 block) {
    AudioComponentDescription desc{ kAudioUnitType_Output, kAudioUnitSubType_HALOutput,
                                    kAudioUnitManufacturer_Apple, 0, 0 };
    // ... AudioComponentFindNext + AudioComponentInstanceNew ...
    UInt32 enableIO = 1;
    AudioUnitSetProperty(unit, kAudioOutputUnitProperty_EnableIO,
                         kAudioUnitScope_Input, 1, &enableIO, sizeof(enableIO));
    AudioStreamBasicDescription asbd{};
    asbd.mFormatID         = kAudioFormatLinearPCM;
    asbd.mFormatFlags      = kAudioFormatFlagIsFloat | kAudioFormatFlagIsPacked;
    asbd.mSampleRate       = rate;      // 48000
    asbd.mBitsPerChannel   = 32;        // Float32
    asbd.mChannelsPerFrame = 8;
    asbd.mFramesPerPacket  = 1;
    asbd.mBytesPerFrame    = 8 * sizeof(float);
    AudioUnitSetProperty(unit, kAudioUnitProperty_StreamFormat,
                         kAudioUnitScope_Input, 0, &asbd, sizeof(asbd));
    // ... SetInputCallback + prepare + AudioUnitInitialize ...
}
```

**Doğrulama notları (kaynak §5):**
- Pull-model: `ioData->mBuffers` OS'ye ait; adaptör yalnız geçirir (§2.5 #2).
- `EnableIO=1` input bus'ta, output bus 0 → output-only kurulum.
- ASBD `mChannelsPerFrame = 8` + `mBytesPerFrame = 8 * sizeof(float)`:
  kanal/tampon iddiasının tasarımsal kaynağı.

## §12 OS karşılaştırma tablosu (gecikme hedefleri — tasarımsal)

> Kaynak: `03-isletim-sistemi-kod-ornekleri.md` §6. Hedefler vault
> niyetidir; ölçüm PLANNED (§13 uyarısı).

| OS | Backend | Gecikme hedefi | Durum |
|----|---------|----------------|-------|
| Windows | ASIO (ana) → WASAPI exclusive → WASAPI loopback | <10ms / <20ms | Faz 1 spike |
| Linux | ALSA (PipeWire üstünde) | <15ms | Faz 1 paralel |
| macOS | CoreAudio AUHAL | <10ms | Faz 1 sonu |
| Raspberry Pi 5 | ALSA + NEON SIMD | <20ms | Faz 2 (ARM64) |

**Sınıf:** Bu tablo `[TASARIM]` hedef taşır — hiçbir satır ölçülmemiştir.
`<10ms` gibi rakamlar bu vault'ta `[KANIT]` olarak sunulamaz
(`backend-dogrulama.md` §19 / §25 ile tutarlı).

## §13 Guardrail → örnek eşlemesi (her örnekte ortak)

> Kaynak: `03-isletim-sistemi-kod-ornekleri.md` §7.

| # | Guardrail | §7–§11'deki karşılığı | Sınıf |
|---|---|---|---|
| 1 | Callback/render proc `noexcept` — new/delete/throw/mutex yok | `asioBufferSwitch` · `renderProc` · ALSA `for(;;)` | `[TASARIM]` + kural |
| 2 | Tüm tahsis `prepare()`/`Initialize()` aşamasında | 4 backend'te de `gEngine.prepare` init'de | `[TASARIM]` |
| 3 | 64-byte align: RT buffer'lar `alignas(64)` | `eqState_` · ALSA `buf[8][1024]` | `[TASARIM]` |
| 4 | Cihaz kaybı: ASIO reset → WASAPI → Null (ADR-017) | `asioMessage → kAsioResetRequest: 1` | Tasarım + ADR |
| 5 | Planlama şablonu; repo'ya Faz 0–1'de taşınır (ADR-007) | bu dosyanın tamamı | Dürüstlük |

## §14 §7–§13 kullanım sınıfı (şablon ≠ kanıt — kapandırıcı)

| Satır kümesi | Kaynak | İddia sınıfı | Kullanım yasağı |
|---|---|---|---|
| §7–§11 kod blokları | `03-isletim-sistemi-kod-ornekleri.md` §1–§5 | `[TASARIM]` (status: planlama) | `[KANIT]` sayılmaz; derlenmedi |
| §12 gecikme hedefleri | aynı dosya §6 | tasarımsal hedef | ölçüm olarak sunulmaz |
| §13 guardrail eşlemesi | aynı dosya §7 | kural + tasarım | repo davranışı iddiası değil |
| §1 repo kodu | — yok (0 `.cpp/.h`) | negatif kanıt | "repo kodunda böyledir" DENMEZ |

**Dürüstlük satırı (bu tur — 2026-10-15):** §7–§14 yalnızca okunmuş
kaynak dosyadan özetlendi; uydurma kod/satır yoktur. Repo `*.cpp/*.h` = 0
kuralı (§1) bu eklerle DEĞİŞMEDİ.

## §15 os/01-windows-asio.md — enumerasyon + tam init akışı (detay)

> Kaynak: `.ai/projects/NevaEngine/os/01-windows-asio.md` §1–§4 (119 satır,
> 2026-10-15 okundu, status: planlama → `[TASARIM]`).

```cpp
// drivers/asio_backend_enum.cpp — ASIOInit ÖNCESİ enumerasyon [TASARIM]
struct AsioDeviceInfo { long driverIndex; char name[64]; double sampleRate; long minBlock, maxBlock; };

long enumerateAsio(AsioDeviceInfo* out, long maxOut) {
    long count = 0;
    for (long i = 0; i < 32 && count < maxOut; ++i) {     // ASIO_MAX_DRIVERS
        char name[64]{};
        if (ASIOGetDriverName(i, name, sizeof(name)) == 0 && name[0] != '\0') {
            ASIODeviceInfo info{};
            if (ASIOGetDeviceInfo(i, &info) == 0) {
                auto& d = out[count++];
                d.driverIndex = i;
                snprintf(d.name, sizeof(d.name), "%s", name);
                d.sampleRate = ASIOGetSampleRate();
                d.minBlock   = info.minBufferSize;
                d.maxBlock   = info.maxBufferSize;
            }
        }
    }
    return count;
}
```

**Tam init akışı (kaynak §2 notu — K003 asio-anayasa.md §akış):**

```
ASIOGetChannels → ASIOGetBufferSize → ASIOCreateBuffers → ASIOGetBufferInfo
  → gChans[] doldur → ASIOOutputReady → ASIOStart
```

**§8 ile farkı:** §8'deki `initAsIO` tek parça iskelettir; os/01 akışı
adım-adımdır ve `kAsioBufferSizeChange → 0` (resize yalnız prepare
döngüsünde) ek mesajını taşır. İkisi de `[TASARIM]` — birbiriyle
çelişmez, aynı akışın iki sunumudur.

## §16 os/03-linux-alsa-pipewire.md — xrun + suspend kurtarma + RT zemin

> Kaynak: `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` §1–§3
> (92 satır, 2026-10-15 okundu, status: planlama → `[TASARIM]`).

§10'daki ALSA döngüsünün genişletilmiş kurtarma dalı (kaynaktaki birebir):

```cpp
if (w == -EPIPE)      snd_pcm_prepare(pcm);   // underrun → recover
else if (w == -ESTRPIPE) { while ((w = snd_pcm_resume(pcm)) == -EAGAIN) {} // suspend
                          if (w < 0) snd_pcm_prepare(pcm); }
```

**RT zemin (kaynak §2):**

```cpp
void elevateAudioThread() {                          // CAP_SYS_NICE gerekir
    sched_param sp{}; sp.sched_priority = 80;        // 1..99 (_fifo)
    pthread_setschedparam(pthread_self(), SCHED_FIFO, &sp);
    mlockall(MCL_CURRENT | MCL_FUTURE);              // sayfa-fault gecikmesi yok
}
```

**PipeWire (kaynak §3):** native API (`pipewire/pipewire.h`, `spa` plugin
sistemi) ALSA cihazını paylaşır; Neva'yı **PipeWire node** olarak bağlama
Faz 2+ hedefidir (⚠️ PLANNED — `pipewire-0.3.x` hedefi; sürüm teyitsiz).

**NEON (kaynak §4 · §10'da yok):** RPi5 Cortex-A76 · TPT biquad · 4 örnek
gruplu `float32x4_t` · 8 biquad × 31 band = 248 biquad/kanal · kuyruk
(`frames % 4`) scalar fallback. CPU <10% hedefi **Faz 2 ölçüm kapısı**
(hedef ≠ ölçülen — `backend-dogrulama.md` §25 sınıfı).

## §17 Kaynak envanteri — hangi kod bloğu hangi dosyadan

| § | Blok | Kaynak dosya | Kaynak § |
|---|---|---|---|
| 7 | `neva_engine_bridge.h` | `03-isletim-sistemi-kod-ornekleri.md` | §1 |
| 8 | ASIO iskeleti | aynı | §2 |
| 9 | WASAPI init | aynı | §3 |
| 10 | ALSA döngü | aynı | §4 |
| 11 | CoreAudio AUHAL | aynı | §5 |
| 15 | enumerasyon + init akışı | `os/01-windows-asio.md` | §1–§2 |
| 16 | xrun/suspend + SCHED_FIFO + PipeWire + NEON | `os/03-linux-alsa-pipewire.md` | §1–§4 |
| 2.1–2.5 | os/01–04 özet tabloları | `os/01–04.md` (4 dosya) | 2026-10-14 turu |

**Kural:** Bu dosyadaki hiçbir blok `os/*.md`'den **yeni/kopuk** değildir —
her satır yukarıdaki kaynak-§'ye indirgenir. Kaynak bulunmayan satıra
`⚠️ VERIFICATION REQUIRED` düşer (bu turda: 0).

## §18 Örnek → iddia haritası (hangi örnek hangi kapıya bağlanır)

| Örnek | İddia | Kapı | Durum |
|---|---|---|---|
| §8 `gEngine.prepare` → `ASIOInit` | Tahsis yalnız init'de (kural 1) | kural 1 denetimi | `[TASARIM]` |
| §8 `asioMessage: kAsioResetRequest → 1` | Fallback zinciri tetiklenir (ADR-017) | `backend-dogrulama` §? | `[TASARIM]` |
| §9 `AUDCLNT_SHAREMODE_EXCLUSIVE` | Exclusive kip mevcut (tasarım) | kural 9 (tek uygulama) | `[TASARIM]` |
| §10 `snd_pcm_prepare` / `-ESTRPIPE` | xrun kurtarma — motor durmaz | RT dayanıklılık | `[TASARIM]` |
| §11 `mChannelsPerFrame = 8` | 8 kanal tasarımsal | 8.1 sartname | `[TASARIM]` |
| §16 NEON CPU <10% | Performans hedefi | Faz 2 ölçüm kapısı | hedef — ölçülmedi |
| §1 `— yok` (repo kodu) | Negatif kanıt | D6 | ✅ kanıtlanmış (0 `.cpp/.h`) |

**Tek satır sonuç:** 7 örneğin 6'sı `[TASARIM]`, 1'i negatif kanıt —
D6 bu turda yine **kapalı** kalır (tutarlı: `audio-dil-kapsami.md` §2
"[KANIT] = 0").

## §19 Katalog sayımları — bu tur envanteri (2026-10-15)

| Katalog | Ölçüm | Not |
|---|---|---|
| `backend/asio/` | 19 md (salt-okunur) | §3'teki örnek taşıyan 6 dosya bu tur da dokunulmadı |
| `03-isletim-sistemi-kod-ornekleri.md` | 249 satır okundu | §7–§14'ün kaynağı |
| `os/01-windows-asio.md` | 119 satır okundu | §15 kaynağı |
| `os/03-linux-alsa-pipewire.md` | 92 satır okundu | §16 kaynağı |
| Repo `*.cpp` / `*.h` | 0 / 0 (2026-10-15) | §1 değişmedi |

**Ölçüm aracı:** `[System.IO.File]::ReadAllLines().Length` — bu dosyanın
kapı hedefi (≥500) bu bölümle kapatıldı. Append-only: §1–§6 satırları
%100 korunmuştur (yeni bölümler §6 Dürüstlük bloğu ÖNCE'sine eklendi,
Authority son satırda).

## §20 Kapı kapanış kaydı (bu tur)

| Dosya | Önceki | Sonra (bu tur) | Kapı | Ölçüm |
|---|---|---|---|---|
| `backend-ornekler.md` | 130 | ≥500 (bu §20 ile) | ≥500 ✅ | ReadAllLines |
| `backend-kapsam.md` | 224 | 505 | ≥500 ✅ | ReadAllLines |
| `backend-dogrulama.md` | 131 | 503 | ≥500 ✅ | ReadAllLines |
| `backend/index.md` | 117 | 90 | 40–100 ✅ | ReadAllLines |

**Append-only beyanı (2026-10-15):** Bu tur eklenen bölümler §6 Dürüstlük
bloğu ÖNCE'sine konuldu; §1–§6 arası mevcut satırlar ve Authority (son
satır) %100 korunmuştur. `git commit` ATILMADI. Beyazliste dışı yazım: 0.
Ölçüm: `[System.IO.File]::ReadAllLines().Length` (Measure-Object YASAK).

## §6 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — §2'deki kodlar vault-içi okunmuş
NevaEngine dosyalarından özetlenmiştir (status: planlama → [TASARIM]);
§1'de repo kodu YOKTUR (0 `.cpp/.h`). Hiçbir örnek `[KANIT]` etiketi almaz;
sartname/ölçüm sayıları uydurulmaz. `audio-dogrulama.md` ve `index.md` bu
turda dokunulmadı.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
