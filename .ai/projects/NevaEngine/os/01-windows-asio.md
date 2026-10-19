---
title: "Neva Engine — Windows ASIO Backend (OS Kod Örneği)"
type: doc
category: audio-engine
version: 1.0.0
status: planlama
authority: "SSOT: .ai/projects/NevaEngine/00-sdk-lib-envanter · K003-audio/backend/asio/*"
updated: 2026-10-14
---

# Windows — ASIO Backend (ana backend, <10ms)

**Zorunlu Bağlantılar:** [[03-isletim-sistemi-kod-ornekleri]] · [[02-dsp-zincir-mimari]] · K003-audio/backend/asio/ (anayasa, latency, hata-yonetimi, hot-swap)

> Ayrıntılı ASIO konsept dokümanı K003-audio/backend/asio/ altındadır (21 md).
> Bu dosya: Neva Engine'e özgü **entegrasyon örneği** (ASIO SDK 2.3.x · GPLv3 açık varyant).

## §1 Cihaz Enumerasyonu

```cpp
// drivers/asio_backend_enum.cpp
#include "asiosdk/common/asio.h"
#include <cstdio>

struct AsioDeviceInfo { long driverIndex; char name[64]; double sampleRate; long minBlock, maxBlock; };

// Enumerasyon: sürücü kayıt defteri üzerinden (ASIOInit öncesi)
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

## §2 BufferSwitch Callback (RT-hot-path)

```cpp
// drivers/asio_backend.cpp — audio-engine-cpp realtime-rules.md uyumlu
#include "asiosdk/common/asio.h"
#include "../neva_engine_bridge.h"

static NevaEngineCore  gEngine;              // statik tahsis — callback'de new YOK
static float*          gChans[8]{};          // kanal buffer pointer'ları (prepare'da doldurulur)
static long            gBlockFrames = 512;   // brain.md §8: varsayılan 512 (64–1024)

// asioBufferSwitch: ASIO zamanlayıcı çağırır — noexcept, tahsis yok, log yok
static void asioBufferSwitch(long index, ASIOBool processNow) noexcept {
    (void)processNow;
    float* io[8];
    for (int ch = 0; ch < 8; ++ch)
        io[ch] = gChans[ch] + index * gBlockFrames;   // double-buffer offset
    gEngine.process(io, 8, gBlockFrames);             // EQ→comp→reverb→crossover→limiter→maximizer
}

static void asioSampleRateDidChange(ASIOSampleRate sRate) noexcept {
    // Örnekleme değişimi: UI thread'e kuyruk ile bildir; audio thread'de yeniden prepare YOK
    (void)sRate;
}

static long asioMessage(long selector, long value, void* msg, double* opt) noexcept {
    (void)value; (void)msg; (void)opt;
    switch (selector) {
        case kAsioEngineVersion:  return 2;
        case kAsioResetRequest:   return 1;  // host durumu sıfırla → WASAPI fallback (ADR-017)
        case kAsioResyncRequest:  return 1;
        case kAsioBufferSizeChange: return 0; // resize yalnız prepare döngüsünde
        default: return 0;
    }
}

// Init: exclusive lock — aynı anda TEK uygulama (.ai/brain.md §kritik #6)
ASIOError initAsio(double rate, long blockFrames, int channels) {
    ASIOCallbacks cb{};
    cb.bufferSwitch             = &asioBufferSwitch;
    cb.sampleRateDidChange      = &asioSampleRateDidChange;
    cb.asioMessage              = &asioMessage;
    cb.bufferSwitchTimeInfo     = nullptr;
    gBlockFrames = blockFrames;
    gEngine.prepare(rate, static_cast<int>(blockFrames));   // TÜM tahsis burada
    return ASIOCreateBuffers(nullptr, 0, 0, nullptr);       // (kanal bilgisi ASIOGetChannels ile)
    // Not: tam init akışı → ASIOGetChannels → ASIOGetBufferSize → ASIOCreateBuffers → ASIOGetBufferInfo
    //      → gChans[] doldur → ASIOOutputReady → ASIOStart (K003 asio-anayasa.md §akış)
}
```

## §3 Latency & Buffer Kararı

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Varsayılan buffer | 512 sample @48kHz ≈ 10.67ms | brain.md §8 |
| İzinli aralık | 64–1024 | brain.md §8 |
| Hedef roundtrip | <10ms | CLAUDE §19 |
| 64 sample'a düşürme | Yalnız ölçüm sonrası (xrun riski) | K003 asio-latency-ve-jitter |

## §4 Hata Yönetimi & Fallback Zinciri

```
ASIOInit başarısız / kAsioResetRequest (cihaz kaybı)
  → WASAPI exclusive (<20ms)
    → WASAPI shared + loopback
      → Null Output (sessiz fallback, UI "çıktı yok" bildirimi)
```

Kaynak: ADR-017 · K003 asio-hata-yonetimi-ve-fallback · asio-hot-swap-ve-device-loss.

**Authority:** Embedded Engineer (L1) · Windows SW Engineer (L1) · Statü: PLANNING
