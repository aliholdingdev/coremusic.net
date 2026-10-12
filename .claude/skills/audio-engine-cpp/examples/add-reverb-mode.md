# Örnek — Yeni Reverb Modu Ekleme (girdi → çıktı tam döngü)

> **Senaryo:** Neva Engine reverb aşamasına 5. bir mod (`Stage` / "Sahne") eklemek.
> 4 mevcut mod (Geniş Konser, Düğün Salonu, Oda, Stüdyo) korunur ve regresyonla doğrulanır.
> Sıra: param struct → enum → DSP insertion point → Google Test → doğrulama.
> ⚠️ Bu örnek **iskelet**tir: Neva Engine gerçek kaynak dosyaları bu skill kapsamında
> okunmadı → mevcut struct/enum adları diskteki engine koduyla birebir teyit edilmelidir
> (`⚠️ VERIFICATION REQUIRED`).

## ADIM 1 — Parametre struct'ı (hot-path hazır, önceden tahsisli)

```cpp
// engine/reverb_types.h — prepare() dışında tahsis YOK; alanlar POD/sabit dizi (R1)
#pragma once
#include <array>
#include <cstdint>

inline constexpr std::size_t kNumReverbDelays = 8;   // ⚠️ VERIFICATION REQUIRED —
                                                     // gerçek engine değerini oku

struct ReverbParams {
    float roomSize      = 0.5f;   // 0..1
    float damping       = 0.5f;   // 0..1
    float wetGainDb     = -12.0f;
    float dryGainDb     = 0.0f;
    float width         = 1.0f;   // 0..1 stereo genişlik
    std::array<float, kNumReverbDelays> delayTimesMs{};  // sabit boyut — tahsis yok
};
```

## ADIM 2 — Mode enum'ına değer ekle

```cpp
// ⚠️ Contradiction Gate: mod ADLARI vault içinde çelişebilir
// (CLAUDE §19: Geniş Konser/Düğün Salonu/Oda/Stüdyo vs PROJECTS §2 farklı adlar).
// Enum adını sabitlemeden önce DUR + kullanıcı onayı (Guardrail #12/#14).
enum class ReverbMode : std::uint8_t {
    kConcert,   // Geniş Konser
    kWedding,   // Düğün Salonu
    kRoom,      // Oda
    kStudio,    // Stüdyo
    kStage      // YENİ: Sahne — varsayılan preset aşağıda
};

inline ReverbParams stagePreset() noexcept {
    ReverbParams p{};
    p.roomSize   = 0.75f;
    p.damping    = 0.35f;
    p.wetGainDb  = -10.0f;
    p.dryGainDb  = -1.0f;
    p.width      = 0.9f;
    p.delayTimesMs = {13.7f, 17.9f, 21.3f, 24.7f,
                      29.1f, 33.7f, 37.9f, 41.3f};  // preset — algoritma teyidi gerekir
    return p;                                        // ⚠️ VERIFICATION REQUIRED (değerler)
}
```

## ADIM 3 — DSP insertion point (pipeline: EQ → Reverb → Crossover → Limiter)

```cpp
// engine/dsp_chain.cpp — reverb, EQ'dan SONRA, crossover'dan ÖNCE çağrılır.
// Parametre güncellemesi blok SINIRINDA atomik snapshot'lanır; mid-callback mutation yok (R2).
void DspChain::processBlock(float* const* channels, int numCh, int numSamples) noexcept {
    eq_.process(channels, numCh, numSamples);          // 1) EQ 31-band

    ReverbParams snap = paramSnapshot_.load(std::memory_order_acquire);  // blok başı oku
    reverb_.setParams(snap);                                        // stateful ama tahsisiz
    reverb_.process(channels, numCh, numSamples);                  // 2) Reverb (yeni mod dahil)

    crossover_.process(channels, numCh, numSamples);               // 3) Crossover (+LFE)
    limiter_.process(channels, numCh, numSamples);                 // 4) True Peak Brickwall
}

// UI thread → callback sınırı (lock-free, örnek: references/realtime-rules.md §2)
void DspChain::setMode(ReverbMode m) noexcept {
    ReverbParams p = (m == ReverbMode::kStage) ? stagePreset()
                                               : presetFor(m);
    paramSnapshot_.store(p, std::memory_order_release);   // ⚠️ atomik struct tümü —
}                                                         // gerçek engine'de ring/slot
                                                          // mekanizması teyit edilmeli
```

## ADIM 4 — Google Test (coverage hedefi ≥80% min / ≥90%)

```cpp
// tests/reverb_stage_test.cpp
#include <gtest/gtest.h>
#include <cmath>
#include "engine/dsp_chain.h"

TEST(ReverbStage, NewModeProducesFiniteOutput) {
    DspChain chain;
    chain.setMode(ReverbMode::kStage);

    constexpr int kN = 256;
    std::array<float, kN> left{};                         // önceden tahsisli (R1)
    std::array<float, kN> right{};
    float* chans[] = {left.data(), right.data()};

    left[0] = 1.0f;                                       // impulse girdisi
    chain.processBlock(chans, 2, kN);                     // noexcept — throw yok (R3)

    for (int i = 0; i < kN; ++i) {
        ASSERT_TRUE(std::isfinite(left[i]))   << "i=" << i;   // NaN/Inf yok
        ASSERT_TRUE(std::isfinite(right[i]))  << "i=" << i;
        ASSERT_LE(std::fabs(left[i]), 4.0f)   << "gain patlaması (limiter aşımı)";  // ⚠️ eşik
    }
}

TEST(ReverbStage, OriginalFourModesUnaffected) {          // regresyon: 4 eski mod
    for (auto m : {ReverbMode::kConcert, ReverbMode::kWedding,
                   ReverbMode::kRoom,    ReverbMode::kStudio}) {
        DspChain chain; chain.setMode(m);
        std::array<float, 64> l{}, r{}; float* c[] = {l.data(), r.data()};
        l[0] = 1.0f;
        chain.processBlock(c, 2, 64);
        for (float v : l) ASSERT_TRUE(std::isfinite(v));
    }
}
```

## ADIM 5 — Doğrulama adımları (teslim öncesi)

1. **Derle:** C++20 derleyici ile temiz derleme; uyarı yok sayılır mı → hayır, yorumla.
2. **Test çalıştır:** `google-test` hedefini çalıştır (komut/vitesti vault'ta tanımlı değil →
   `⚠️ VERIFICATION REQUIRED`; projedeki mevcut test komutunu kullan).
3. **Coverage:** ≥80% min / ≥90% hedef — yeni testler bu dosyayı kapsar (PROJECTS §7.1.1).
4. **Zero-allocation denetimi:** `processBlock` yolu tahsis içermemeli (heap allocation
   sayacı ile hot-path testi — teknik seçimi vault'ta yok → `⚠️ VERIFICATION REQUIRED`).
5. **Latency regresyonu:** Callback süresi buffer deadline'ını aşmamalı, xrun = 0
   (`references/latency-targets.md` §4).
6. **Kulak testi:** 4 özgün mod + yeni mod A/B dinlenir; bit-perfect/mikser bypass bozulmaz.
7. **Zincir bütünlüğü:** Değişiklik yalnız Reverb aşamasında; EQ → Reverb → Crossover →
   Limiter sırası korunur (`references/dsp-chain-map.md` §2/§5).
8. **Rapor:** Değişen dosyalar, test sonucu, coverage, açık `⚠️` maddeleri kullanıcıya
   sıfır-halüsinasyon ile listelenir.

*CoreMusic Skill v3.0 — examples/add-reverb-mode.md — Updated: 2026-10-07*