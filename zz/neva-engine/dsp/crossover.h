#pragma once
// ============================================================================
// Linkwitz-Riley Crossover — 4th order (24dB/oct)
// Butterworth cascade, phase-aligned
// ============================================================================

#include "biquad_filter.h"
#include <array>
#include <cmath>

namespace cm::neva::dsp {

// ---------------------------------------------------------------------------
// LR4 Crossover (two cascaded biquads per band)
// ---------------------------------------------------------------------------

class LR4Crossover {
public:
    LR4Crossover() = default;

    void setFrequency(float crossoverFreq, float sampleRate) noexcept {
        // LR4 = two cascaded Butterworth HPF + LPF
        constexpr float butterQ = 0.7071f;  // Butterworth Q

        // Low-pass: two cascaded Butterworth LPFs
        lp_[0].setFilter(FilterType::LowPass, crossoverFreq, butterQ, 0.0f, sampleRate);
        lp_[1].setFilter(FilterType::LowPass, crossoverFreq, butterQ, 0.0f, sampleRate);

        // High-pass: two cascaded Butterworth HPFs
        hp_[0].setFilter(FilterType::HighPass, crossoverFreq, butterQ, 0.0f, sampleRate);
        hp_[1].setFilter(FilterType::HighPass, crossoverFreq, butterQ, 0.0f, sampleRate);
    }

    // Split single sample
    void process(float input, float& lowOutput, float& highOutput) noexcept {
        float lp = lp_[0].process(lp_[1].process(input));
        float hp = hp_[0].process(hp_[1].process(input));
        lowOutput = lp;
        highOutput = hp;
    }

    // Split block
    void processBlock(const float* input, float* lowOut, float* highOut,
                      std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            float lp = lp_[0].process(lp_[1].process(input[i]));
            float hp = hp_[0].process(hp_[1].process(input[i]));
            lowOut[i] = lp;
            highOut[i] = hp;
        }
    }

    void reset() noexcept {
        for (auto& f : lp_) f.reset();
        for (auto& f : hp_) f.reset();
    }

private:
    std::array<BiquadFilter, 2> lp_;
    std::array<BiquadFilter, 2> hp_;
};

// ---------------------------------------------------------------------------
// Multi-way crossover (splits into N bands)
// ---------------------------------------------------------------------------

template <std::size_t NumBands>
class MultiWayCrossover {
public:
    static constexpr std::size_t kNumCrossovers = NumBands - 1;

    void setFrequencies(const std::array<float, kNumCrossovers>& freqs,
                        float sampleRate) noexcept {
        for (std::size_t i = 0; i < kNumCrossovers; ++i) {
            crossovers_[i].setFrequency(freqs[i], sampleRate);
        }
    }

    // Process: input → N band outputs
    void process(float input, std::array<float, NumBands>& outputs) noexcept {
        // First crossover: split input into low1 / high1
        float low, high;
        crossovers_[0].process(input, low, high);
        outputs[0] = low;

        // Chain remaining crossovers
        for (std::size_t i = 1; i < kNumCrossovers; ++i) {
            crossovers_[i].process(high, low, high);
            outputs[i] = low;
        }
        outputs[NumBands - 1] = high;
    }

    void reset() noexcept {
        for (auto& c : crossovers_) c.reset();
    }

private:
    std::array<LR4Crossover, kNumCrossovers> crossovers_;
};

// 2-way: Woofer + Tweeter (80Hz default)
using Crossover2Way = MultiWayCrossover<2>;
// 3-way: Woofer + Mid + Tweeter
using Crossover3Way = MultiWayCrossover<3>;

} // namespace cm::neva::dsp
