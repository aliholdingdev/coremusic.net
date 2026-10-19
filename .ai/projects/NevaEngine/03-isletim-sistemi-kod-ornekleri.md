---
title: "Neva Engine — İşletim Sistemi Bazlı Kod Örnekleri (K2 Backends)"
type: doc
category: audio-engine
version: 1.1.0
status: planlama
authority: "SSOT: .ai/CLAUDE.md §19 · §24 · skill audio-engine-cpp (realtime-rules.md)"
updated: 2026-10-15
---

# Neva Engine — İşletim Sistemi Bazlı Kod Örnekleri

**Zorunlu Bağlantılar:** [[00-sdk-lib-envanter]] · [[01-gelistirme-plani]] · [[02-dsp-zincir-mimari]] · [[brain.md]] §7

> **BÖLÜNMÜŞ (2026-10-14):** OS bazlı ayrıntılı örnekler `os/` altına taşındı —
> [[os/01-windows-asio]] (enum + bufferSwitch + latency + fallback) ·
> [[os/02-windows-wasapi]] (exclusive + loopback) ·
> [[os/03-linux-alsa-pipewire]] (xrun + SCHED_FIFO + RPi5 NEON) ·
> [[os/04-macos-coreaudio]] (AUHAL).
> Bu dosya artık özet iskelettir; mimari kayıtlar K000/K001/K002/K003/K004 index'lerinde.

> **STATÜ: ŞABLON/ÖRNEK — planlama modu.** Bu dosyadaki kod vault dokümanıdır;
> repo'ya (`neva.engine/src/`) Faz 0–1'de, onay sonrası, bu örnekler temel alınarak taşınır.
> Her örnek zero-allocation / lock-free / noexcept guardrail'lerine (audio-engine-cpp §4) uyar.

## §1 Ortak Arayüz (tüm OS'ler aynı DSP çağırır)

```cpp
// neva_engine_bridge.h — OS bağımsız çekirdek (tek implementasyon)
#pragma once
#include <atomic>
#include <cstddef>

struct EngineParams {                    // UI → engine lock-free
    std::atomic<float> inputGainDb {0.f};
    std::atomic<float> limiterCeilingDb {-0.1f};
    std::atomic<int>   reverbMode   {0}; // 0=Konser 1=Düğün 2=Oda 3=Stüdyo
};

class NevaEngineCore {
public:
    void prepare(double sampleRate, int maxBlock) noexcept; // tahsis BURADA (prepare)
    void process(float* const* io, int channels, int frames) noexcept; // RT-safe
private:
    alignas(64) float eqState_[8][2][31]{}; // biquad state — 8 kanal × 2 biquad × 31 band
};
```

**Kural:** her OS backend'i yalnız `process()` çağırır; DSP tek yerde yaşar (DRY).

## §2 Windows — ASIO (ana backend, <10ms)

```cpp
// drivers/asio_backend.cpp — ASIO SDK 2.3.x host iskeleti (GPLv3 açık varyant)
#include "asiosdk/common/asio.h"
#include "neva_engine_bridge.h"

static NevaEngineCore gEngine;           // statik: callback içinde new YOK
static float* gOutBuf[8]{};              // ASIO buffer switch'te doldurulur
static long   gBlockFrames = 0;

// ASIO bufferSwitch zaman kritik — noexcept, tahsis yok
static void asioBufferSwitch(long index, ASIOBool processNow) noexcept {
    ASIOBufferInfo* in  = nullptr;
    (void)in;
    // 1) ASIO buffer'larını float** kabuğuna map'le (kanal başına)
    float* channels[8];
    for (int ch = 0; ch < 8; ++ch) channels[ch] = gOutBuf[ch] + index * gBlockFrames;
    // 2) DSP zinciri (EQ → comp → reverb → crossover → limiter → maximizer)
    gEngine.process(channels, 8, gBlockFrames);
}

// Buffer switch zamanlayıcısı (ASIO host protokolü)
static void asioSampleRateDidChange(ASIOSampleRate sRate) noexcept { (void)sRate; }
static long asioMessage(long selector, long value, void* msg, double* opt) noexcept {
    switch (selector) {
        case kAsioEngineVersion: return 2;
        case kAsioResetRequest:  return 1; // host durumu sıfırla (cihaz kaybı → WASAPI fallback, ADR-017)
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
    return ASIOInit(&cb);                     // exclusive lock: tek uygulama! (.ai/brain.md §kritik #6)
}
```

**Windows notları:**
- Exclusive lock: ASIO aynı anda tek uygulama; çoklu deneme → sürücü çökmesi (AGENTS §17 #6).
- Cihaz kaybı → `kAsioResetRequest` → **WASAPI fallback** (ADR-017).
- Buffer varsayılan 512 sample @48kHz ≈ 10.67ms (`brain.md` §8) — 64'ye düşürme sadece ölçüm sonrası.

## §3 Windows — WASAPI Exclusive/Loopback (fallback + sanal ses)

```cpp
// drivers/wasapi_backend.cpp — Windows 10/11 · IMMDevice + IAudioClient3 iskeleti
#include <mmdeviceapi.h>
#include <audioclient.h>
#include "neva_engine_bridge.h"

// WASAPI exclusive: SharedMode=FALSE → <20ms hedefi; loopback: capture render stream
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

    // Exclusive: AUDCLNT_SHAREMODE_EXCLUSIVE · event-driven: AUDCLNT_STREAMFLAGS_EVENTCALLBACK
    REFERENCE_TIME dur = static_cast<REFERENCE_TIME>(10'000'000.0 * block / rate); // 100ns birim
    hr = client->Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE,
                            AUDCLNT_STREAMFLAGS_EVENTCALLBACK, dur, 0, &wfx, nullptr);
    if (FAILED(hr)) return hr; // desteklenmiyorsa shared moda düş (fallback zinciri)
    engine.prepare(rate, static_cast<int>(block));
    return client->Start();
}
```

**WASAPI notları:**
- Exclusive başarısız → `AUDCLNT_SHAREMODE_SHARED` + `AUDCLNT_STREAMFLAGS_LOOPBACK` (sanal ses kartı kısa vade stratejisi — envanter §3).
- Loopback capture: uygulama çıkışını Neva'ya yönlendirme (EQ/master FX) — sürücü yazmadan sanal kablo etkisi.

## §4 Linux — ALSA (Tier 2 + Raspberry Pi 5)

```cpp
// drivers/alsa_backend.cpp — pcm.h · non-blockinge poll döngüsü
#include <alsa/asoundlib.h>
#include "neva_engine_bridge.h"

int runAlsa(NevaEngineCore& engine, const char* hw, double rate, unsigned block) {
    snd_pcm_t* pcm = nullptr;
    if (snd_pcm_open(&pcm, hw, SND_PCM_STREAM_PLAYBACK, 0) < 0) return -1;

    snd_pcm_set_params(pcm,
        SND_PCM_FORMAT_FLOAT_LE,          // 32-bit float — .ai/CLAUDE.md §19
        SND_PCM_ACCESS_RW_INTERLEAVED,
        8,                                 // 8.1 kanal
        static_cast<unsigned>(rate),       // 48000
        1,                                 // soft resample
        100'000);                          // 100ms latency tampon payı

    engine.prepare(rate, static_cast<int>(block));

    // Real-time döngü: SCHED_FIFO öncelik + poll; tahsis yok (static buffer)
    alignas(64) static float buf[8][1024];
    float* planes[8];
    for (int ch = 0; ch < 8; ++ch) planes[ch] = buf[ch];

    for (;;) {
        // hw_ptr senkron: period boundary'de blok işle
        engine.process(planes, 8, static_cast<int>(block));
        snd_pcm_sframes_t w = snd_pcm_writen(pcm,
            reinterpret_cast<void**>(planes), static_cast<snd_pcm_uframes_t>(block));
        if (w == -EPIPE) snd_pcm_prepare(pcm);   // xrun → recover, motor durmaz
    }
}
```

**Linux notları:**
- PipeWire Tier 2: ALSA üstünde çalışır; doğrudan PipeWire native API sonraki faz (⚠️ PLANNED).
- Raspberry Pi 5 (ARM64): NEON intrinsics ile biquad SIMD'ye hızlandırma (`brain.md` §7.1 izinli SIMD).
- xrun → `snd_pcm_prepare` ile otomatik kurtarma (audio thread'de throw yasak).

## §5 macOS — CoreAudio (AUHAL/HALOutput)

```cpp
// drivers/coreaudio_backend.cpp — AudioUnit AUHAL iskeleti (macOS 12+)
#include <AudioToolbox/AudioToolbox.h>
#include "neva_engine_bridge.h"

static NevaEngineCore gEngine;

static OSStatus renderProc(void* inRefCon, AudioUnitRenderActionFlags* flags,
                           const AudioTimeStamp* time, UInt32 bus,
                           AudioBufferList* ioData, AudioUnitRenderActionFlags* outFlags) noexcept {
    (void)inRefCon; (void)flags; (void)time; (void)bus; (void)outFlags;
    const UInt32 chans = ioData->mNumberBuffers < 8 ? ioData->mNumberBuffers : 8;
    const int frames = static_cast<int>(ioData->mBuffers[0].mDataByteSize / sizeof(float));
    float* planes[8];
    for (UInt32 ch = 0; ch < chans; ++ch) planes[ch] = static_cast<float*>(ioData->mBuffers[ch].mData);
    gEngine.process(planes, static_cast<int>(chans), frames); // 32-bit float render bus
    return noErr;
}

OSStatus initCoreAudio(AudioUnit* outUnit, double rate, UInt32 block) {
    AudioComponentDescription desc{ kAudioUnitType_Output, kAudioUnitSubType_HALOutput,
                                    kAudioUnitManufacturer_Apple, 0, 0 };
    AudioComponent comp = AudioComponentFindNext(nullptr, &desc);
    AudioUnit unit = nullptr;
    AudioComponentInstanceNew(comp, &unit);

    UInt32 enableIO = 1;
    AudioUnitSetProperty(unit, kAudioOutputUnitProperty_EnableIO,
                         kAudioUnitScope_Input, 1, &enableIO, sizeof(enableIO)); // input bus kapalı

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

    AURenderCallbackStruct cb{ &renderProc, nullptr };
    AudioUnitSetProperty(unit, kAudioOutputUnitProperty_SetInputCallback,
                         kAudioUnitScope_Global, 0, &cb, sizeof(cb));
    gEngine.prepare(rate, static_cast<int>(block));
    *outUnit = unit;
    return AudioUnitInitialize(unit);
}
```

## §6 OS Karşılaştırma Özeti

| OS | Backend | Gecikme hedefi | Durum |
|----|---------|----------------|-------|
| Windows | ASIO (ana) → WASAPI exclusive → WASAPI loopback | <10ms / <20ms | Faz 1 spike |
| Linux | ALSA (PipeWire üstünde) | <15ms | Faz 1 paralel |
| macOS | CoreAudio AUHAL | <10ms | Faz 1 sonu |
| Raspberry Pi 5 | ALSA + NEON SIMD | <20ms | Faz 2 (ARM64) |

## §7 Guardrails (her örnekte ortak)

1. Callback/render proc: `noexcept` — `new`/`delete`/`throw`/mutex yok (H001 RED).
2. Tüm tahsis `prepare()`/`Initialize()` aşamasında.
3. 64-byte align: RT buffer'lar `alignas(64)`.
4. Cihaz kaybı: ASIO reset → WASAPI → Null (ADR-017 fallback zinciri).
5. Bu dosya planlama şablonudur; repo koduna Faz 0–1'de taşınır (ADR-007).

## §8 Çok Dilli Örnek Paketi (doc 11 — 2026-10-15)

OS API örnekleri (§1-§5, C++ odağı) tamamlayıcısı: **10 dil × 6 örnek** paketi →
`[11-cok-dilli-kod-ornekleri.md](11-cok-dilli-kod-ornekleri.md)` + `11-examples/`
(01-c11 · 02-cpp20 · 03-csharp · 04-php8 · 05-javascript · 06-typescript ·
07-python · 08-go · 09-rust · 10-html-css).

Her dosya: biquad EQ (R16) · 32f buffer (K2/K4/K5) · SPSC ring (R10) · WAV 32f (K3) ·
compressor+limiter (R10) · OS/cihaz enum (R18). HTML/CSS dosyası UI örneklerine uyarlar.
K1-K6 (doc 10) bağlayıcı. **Örnekler derlenmedi** — Faz 0 build+test kapısı geçerli.

**Authority:** Embedded Engineer (L1) · Windows SW Engineer (L1)
**Statü:** PLANNING — repo'ya kod taşınması onay sonrasi (Faz 0–1)
