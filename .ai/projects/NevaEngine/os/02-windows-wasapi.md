---
title: "Neva Engine — Windows WASAPI Backend (OS Kod Örneği)"
type: doc
category: audio-engine
version: 1.0.0
status: planlama
authority: "SSOT: .ai/projects/NevaEngine/00-sdk-lib-envanter · .ai/ecosystem/asio-wasapi-rehber.md"
updated: 2026-10-14
---

# Windows — WASAPI Backend (fallback + sanal ses/loopback)

**Zorunlu Bağlantılar:** [[01-windows-asio]] · [[03-isletim-sistemi-kod-ornekleri]] · `.ai/ecosystem/asio-wasapi-rehber.md` §3.2–3.4

## §1 Exclusive Mode Init (32-bit float · 8 kanal)

```cpp
// drivers/wasapi_backend.cpp — IMMDevice + IAudioClient3
#include <mmdeviceapi.h>
#include <audioclient.h>
#include "neva_engine_bridge.h"

struct WasapiState { IAudioClient* client; HANDLE event; NevaEngineCore* engine; };

HRESULT wasapiInitExclusive(IMMDevice* dev, WasapiState& st, double rate, UINT32 block) {
    HRESULT hr = dev->Activate(__uuidof(IAudioClient), CLSCTX_ALL, nullptr,
                               reinterpret_cast<void**>(&st.client));
    if (FAILED(hr)) return hr;

    WAVEFORMATEX wfx{};
    wfx.wFormatTag      = WAVE_FORMAT_EXTENSIBLE;  // 32-bit float
    wfx.nChannels       = 8;
    wfx.nSamplesPerSec  = static_cast<DWORD>(rate);
    wfx.wBitsPerSample  = 32;
    wfx.nBlockAlign     = wfx.nChannels * (wfx.wBitsPerSample / 8);
    wfx.nAvgBytesPerSec = wfx.nSamplesPerSec * wfx.nBlockAlign;

    REFERENCE_TIME dur = static_cast<REFERENCE_TIME>(10'000'000.0 * block / rate); // 100ns
    hr = st.client->Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE,
                               AUDCLNT_STREAMFLAGS_EVENTCALLBACK, dur, 0, &wfx, nullptr);
    if (FAILED(hr)) return hr;   // unsupported → shared'a düş (fallback zinciri)
    st.engine->prepare(rate, static_cast<int>(block));
    st.event = CreateEventW(nullptr, FALSE, FALSE, nullptr);
    st.client->SetEventHandle(st.event);
    return st.client->Start();
}
```

## §2 Loopback Capture (sanal ses kartı — kısa vade stratejisi)

```cpp
// drivers/wasapi_loopback.cpp — uygulama çıkışını Neva'ya geri besleme (EQ/master FX)
#include <audioclient.h>

HRESULT wasapiInitLoopback(IMMDevice* renderDev, IAudioClient** out, double rate, UINT32 block) {
    // Loopback: render akışını capture — sanal sürücü yazılmadan "sanal kablo" etkisi
    HRESULT hr = renderDev->Activate(__uuidof(IAudioClient), CLSCTX_ALL, nullptr,
                                     reinterpret_cast<void**>(out));
    if (FAILED(hr)) return hr;
    WAVEFORMATEX* mix = nullptr;
    hr = (*out)->GetMixFormat(&mix);           // shared mod formatı
    if (FAILED(hr)) return hr;
    REFERENCE_TIME dur = static_cast<REFERENCE_TIME>(10'000'000.0 * block / rate);
    hr = (*out)->Initialize(AUDCLNT_SHAREMODE_SHARED,
                            AUDCLNT_STREAMFLAGS_LOOPBACK, dur, 0, mix, nullptr);
    CoTaskMemFree(mix);
    return SUCCEEDED(hr) ? (*out)->Start() : hr;
}
```

## §3 Gecikme Karşılaştırma (vault verisi)

| Mod | Hedef | Kaynak |
|-----|-------|--------|
| ASIO exclusive | <10ms | CLAUDE §19 |
| WASAPI exclusive | <20ms | asio-wasapi-rehber §3.2 |
| WASAPI shared+loopback | ölçülecek (kabul: <40ms, monitor yolu) | ⚠️ Faz 1 ölçümü |

**Authority:** Windows SW Engineer (L1) · Statü: PLANNING
