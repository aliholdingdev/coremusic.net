---
title: "Neva Engine — macOS CoreAudio Backend (OS Kod Örneği)"
type: doc
category: audio-engine
version: 1.0.0
status: planlama
authority: "SSOT: .ai/projects/NevaEngine/00-sdk-lib-envanter · K000-isletim-sistemi/isletim-sistemi-audio/coreaudio"
updated: 2026-10-14
---

# macOS — CoreAudio (AUHAL/HALOutput)

**Zorunlu Bağlantılar:** [[03-isletim-sistemi-kod-ornekleri]] · K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/index.md

## §1 AUHAL Render Callback (Float32 · 8 kanal)

```cpp
// drivers/coreaudio_backend.cpp — macOS 12+ (Monterey, Tier 3)
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
    for (UInt32 ch = 0; ch < chans; ++ch)
        planes[ch] = static_cast<float*>(ioData->mBuffers[ch].mData);
    gEngine.process(planes, static_cast<int>(chans), frames);  // 32-bit float render bus
    return noErr;   // tahsis yok · noexcept · log yok
}

OSStatus initCoreAudio(AudioUnit* outUnit, double rate, UInt32 block) {
    AudioComponentDescription desc{ kAudioUnitType_Output, kAudioUnitSubType_HALOutput,
                                    kAudioUnitManufacturer_Apple, 0, 0 };
    AudioComponent comp = AudioComponentFindNext(nullptr, &desc);
    AudioUnit unit = nullptr;
    AudioComponentInstanceNew(comp, &unit);

    UInt32 enableIO = 0;  // output-only render (input bus kapalı)
    AudioUnitSetProperty(unit, kAudioOutputUnitProperty_EnableIO,
                         kAudioUnitScope_Input, 1, &enableIO, sizeof(enableIO));

    AudioStreamBasicDescription asbd{};
    asbd.mFormatID         = kAudioFormatLinearPCM;
    asbd.mFormatFlags      = kAudioFormatFlagIsFloat | kAudioFormatFlagIsPacked;
    asbd.mSampleRate       = rate;                    // 48000
    asbd.mBitsPerChannel   = 32;                      // Float32
    asbd.mChannelsPerFrame = 8;
    asbd.mFramesPerPacket  = 1;
    asbd.mBytesPerFrame    = 8 * sizeof(float);
    AudioUnitSetProperty(unit, kAudioUnitProperty_StreamFormat,
                         kAudioUnitScope_Input, 0, &asbd, sizeof(asbd));

    AURenderCallbackStruct cb{ &renderProc, nullptr };
    AudioUnitSetProperty(unit, kAudioOutputUnitProperty_SetInputCallback,
                         kAudioUnitScope_Global, 0, &cb, sizeof(cb));
    gEngine.prepare(rate, static_cast<int>(block));   // tüm tahsis burada
    *outUnit = unit;
    return AudioUnitInitialize(unit);
}
```

## §2 macOS Notları

| Konu | Karar |
|------|-------|
| Hedef sürümler | Monterey 12+ (Tier 3 · brain.md §15) |
| Input gerekirse | AUHAL input bus'a `AudioUnitRender` — kayıt Faz 3+ |
| Gecikme | CoreAudio jenerik <15ms (kabul); ölçüm Faz 1 |
| JUCE yolu | JUCE `AudioDeviceType::CoreAudio` backend'i bu AUHAL'ı sarar — ikili strateji: JUCE ana, raw CoreAudio doğrulama harness'ı |

**Authority:** Embedded Engineer (L1) · Statü: PLANNING
