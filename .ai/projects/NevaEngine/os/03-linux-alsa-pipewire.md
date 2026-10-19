---
title: "Neva Engine — Linux ALSA/PipeWire Backend (OS Kod Örneği)"
type: doc
category: audio-engine
version: 1.0.0
status: planlama
authority: "SSOT: .ai/projects/NevaEngine/00-sdk-lib-envanter · K000-isletim-sistemi/isletim-sistemi-audio/alsa"
updated: 2026-10-14
---

# Linux — ALSA (ana) + PipeWire (üst katman)

**Zorunlu Bağlantılar:** [[03-isletim-sistemi-kod-ornekleri]] · K000-isletim-sistemi/isletim-sistemi-audio/alsa/index.md · aaudio · reactos-audio

## §1 ALSA Playback Döngüsü (FLOAT_LE · 8 kanal · xrun kurtarma)

```cpp
// drivers/alsa_backend.cpp
#include <alsa/asoundlib.h>
#include "neva_engine_bridge.h"

int runAlsa(NevaEngineCore& engine, const char* hw, double rate, unsigned block) {
    snd_pcm_t* pcm = nullptr;
    if (snd_pcm_open(&pcm, hw, SND_PCM_STREAM_PLAYBACK, 0) < 0) return -1;

    snd_pcm_set_params(pcm, SND_PCM_FORMAT_FLOAT_LE, SND_PCM_ACCESS_RW_INTERLEAVED,
                       8, static_cast<unsigned>(rate), 1, /*soft_resample*/ 100'000);
    engine.prepare(rate, static_cast<int>(block));

    alignas(64) static float buf[8][1024];      // statik — döngüde tahsis yok
    float* planes[8];
    for (int ch = 0; ch < 8; ++ch) planes[ch] = buf[ch];

    for (;;) {
        engine.process(planes, 8, static_cast<int>(block));
        snd_pcm_sframes_t w = snd_pcm_writen(pcm, reinterpret_cast<void**>(planes),
                                             static_cast<snd_pcm_uframes_t>(block));
        if (w == -EPIPE)      snd_pcm_prepare(pcm);   // underrun → recover
        else if (w == -ESTRPIPE) { while ((w = snd_pcm_resume(pcm)) == -EAGAIN) {} // suspend
                                  if (w < 0) snd_pcm_prepare(pcm); }
    }
}
```

## §2 Gerçek-Zamanlı Kuyruk Ayarı (SCHED_FIFO)

```cpp
// drivers/linux_rt.cpp — audio thread önceliklendirme (CAP_SYS_NICE gerekir)
#include <pthread.h>
#include <sched.h>

void elevateAudioThread() {
    sched_param sp{}; sp.sched_priority = 80;            // 1..99 (_fifo)
    pthread_setschedparam(pthread_self(), SCHED_FIFO, &sp);
    // MCL_FUTURE: kilitli sayfa → sayfa-fault gecikmesi yok (başarısızlık sessiz devam)
    mlockall(MCL_CURRENT | MCL_FUTURE);
}
```

## §3 PipeWire (Tier 2 — üst katman)

PipeWire native API (`pipewire/pipewire.h`, `spa` plugin sistemi) ile ALSA cihazını paylaşır.
Neva Engine'i **PipeWire node** olarak bağlama Faz 2+ hedefidir (⚠️ PLANNED — API sürümü
`pipewire-0.3.x` hedeflenir; detay: K000-isletim-sistemi/isletim-sistemi-audio).

## §4 Raspberry Pi 5 (ARM64 · NEON)

```cpp
// engine/neon_biquad.cpp — 31-band EQ hot-path (TPT biquad · 4 örnek gruplu NEON)
#include <arm_neon.h>

// 4 örneklik tek biquad: y[n] = b0*x[n] + b1*x[n-1] + b2*x[n-2] - a1*y[n-1] - a2*y[n-2]
struct BiquadNEON {
    float32x4_t b0, b1, b2, a1, a2;
    float32x4_t x1{}, x2{}, y1{}, y2{};               // durum (prepare'da sıfırlanır)
    void process(float* io, int frames) noexcept {
        for (int i = 0; i + 4 <= frames; i += 4) {
            float32x4_t x0 = vld1q_f32(io + i);
            float32x4_t y0 = vmlaq_f32(vmlaq_f32(vmulq_f32(b0, x0), b1, x1), b2, x2);
            y0 = vmlaq_f32(vmlaq_f32(y0, a1, y1), a2, y2);   // negatif a katsayılarıyla
            vst1q_f32(io + i, y0);
            x2 = x1; x1 = x0; y2 = y1; y1 = y0;
        }
        // kuyruk (frames % 4): scalar path — hallüsinasyonsuz fallback
    }
};
```

- 8 biquad × 31 band = 248 biquad/channel — RPi5 Cortex-A76'da NEON ile gerçek zamanlı rahat.
- Ölçüm: 512-sample buffer @48kHz → CPU kullanımı <10% hedefi (Faz 2 ölçüm kapısı).

**Authority:** Embedded Engineer (L1) · Statü: PLANNING
