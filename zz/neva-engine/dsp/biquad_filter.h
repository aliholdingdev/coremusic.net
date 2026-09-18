#pragma once
// ============================================================================
// Biquad Filter — Direct Form II Transposed
// Zero-allocation, constexpr coefficients, noexcept
// Supports: LPF, HPF, BPF, Notch, Peak, LowShelf, HighShelf
// ============================================================================

#include <cmath>
#include <array>
#include <span>
#include <algorithm>

namespace cm::neva::dsp {

// ---------------------------------------------------------------------------
// Filter type enumeration
// ---------------------------------------------------------------------------

enum class FilterType : std::uint8_t {
    LowPass   = 0,
    HighPass  = 1,
    BandPass  = 2,
    Notch     = 3,
    Peak      = 4,
    LowShelf  = 5,
    HighShelf = 6,
};

// ---------------------------------------------------------------------------
// Biquad coefficients (5 coefficients + 2 state variables)
// ---------------------------------------------------------------------------

struct BiquadCoefficients {
    float b0 = 1.0f;
    float b1 = 0.0f;
    float b2 = 0.0f;
    float a1 = 0.0f;
    float a2 = 0.0f;
};

// ---------------------------------------------------------------------------
// Compute biquad coefficients from parameters
// ---------------------------------------------------------------------------

constexpr BiquadCoefficients computeBiquad(
    FilterType type, float frequency, float Q, float gainDb,
    float sampleRate) noexcept
{
    const float nyquist = sampleRate * 0.5f;
    const float normFreq = std::clamp(frequency / nyquist, 0.001f, 0.999f);
    const float omega = 2.0f * 3.14159265f * normFreq;
    const float sinOmega = std::sin(omega);
    const float cosOmega = std::cos(omega);
    const float alpha = sinOmega / (2.0f * Q);
    const float A = std::pow(10.0f, gainDb / 40.0f);
    const float sqrtA = std::sqrt(A);

    BiquadCoefficients c;

    switch (type) {
        case FilterType::LowPass:
            c.b0 = (1.0f - cosOmega) * 0.5f;
            c.b1 =  1.0f - cosOmega;
            c.b2 = (1.0f - cosOmega) * 0.5f;
            c.a1 = -2.0f * cosOmega;
            c.a2 =  1.0f - alpha;
            break;

        case FilterType::HighPass:
            c.b0 = (1.0f + cosOmega) * 0.5f;
            c.b1 = -(1.0f + cosOmega);
            c.b2 = (1.0f + cosOmega) * 0.5f;
            c.a1 = -2.0f * cosOmega;
            c.a2 =  1.0f - alpha;
            break;

        case FilterType::BandPass:
            c.b0 = alpha;
            c.b1 = 0.0f;
            c.b2 = -alpha;
            c.a1 = -2.0f * cosOmega;
            c.a2 = 1.0f - alpha;
            break;

        case FilterType::Notch:
            c.b0 = 1.0f;
            c.b1 = -2.0f * cosOmega;
            c.b2 = 1.0f;
            c.a1 = -2.0f * cosOmega;
            c.a2 = 1.0f - alpha;
            break;

        case FilterType::Peak:
            c.b0 = 1.0f + alpha * A;
            c.b1 = -2.0f * cosOmega;
            c.b2 = 1.0f - alpha * A;
            c.a1 = -2.0f * cosOmega;
            c.a2 = 1.0f - alpha / A;
            break;

        case FilterType::LowShelf: {
            const float twoSqrtAAlpha = 2.0f * sqrtA * alpha;
            c.b0 =    A * (1.0f + (A + 1.0f) - (A - 1.0f) * cosOmega + twoSqrtAAlpha);
            c.b1 = 2.0f * A * ((A - 1.0f) - (A + 1.0f) * cosOmega);
            c.b2 =    A * (1.0f + (A + 1.0f) - (A - 1.0f) * cosOmega - twoSqrtAAlpha);
            c.a1 = -2.0f * ((A - 1.0f) + (A + 1.0f) * cosOmega);
            c.a2 =        (A + 1.0f) + (A - 1.0f) * cosOmega - twoSqrtAAlpha;
            break;
        }

        case FilterType::HighShelf: {
            const float twoSqrtAAlpha = 2.0f * sqrtA * alpha;
            c.b0 =    A * (1.0f + (A + 1.0f) + (A - 1.0f) * cosOmega + twoSqrtAAlpha);
            c.b1 = -2.0f * A * ((A - 1.0f) + (A + 1.0f) * cosOmega);
            c.b2 =    A * (1.0f + (A + 1.0f) + (A - 1.0f) * cosOmega - twoSqrtAAlpha);
            c.a1 =  2.0f * ((A - 1.0f) - (A + 1.0f) * cosOmega);
            c.a2 =         (A + 1.0f) - (A - 1.0f) * cosOmega - twoSqrtAAlpha;
            break;
        }
    }

    // Normalize by a0
    const float invA0 = 1.0f / (1.0f + alpha);
    c.b0 *= invA0;
    c.b1 *= invA0;
    c.b2 *= invA0;
    c.a1 *= invA0;
    c.a2 *= invA0;

    return c;
}

// ---------------------------------------------------------------------------
// BiquadFilter — Direct Form II Transposed (single channel)
// ---------------------------------------------------------------------------

class BiquadFilter {
public:
    BiquadFilter() = default;

    void setCoefficients(const BiquadCoefficients& c) noexcept {
        b0_ = c.b0; b1_ = c.b1; b2_ = c.b2;
        a1_ = c.a1; a2_ = c.a2;
    }

    void setFilter(FilterType type, float freq, float Q, float gainDb,
                   float sampleRate) noexcept {
        auto c = computeBiquad(type, freq, Q, gainDb, sampleRate);
        setCoefficients(c);
    }

    // Process single sample — noexcept, zero-allocation
    [[nodiscard]] float process(float input) noexcept {
        float output = b0_ * input + z1_;
        z1_ = b1_ * input - a1_ * output + z2_;
        z2_ = b2_ * input - a2_ * output;
        return output;
    }

    // Process block in-place
    void processBlock(float* data, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = process(data[i]);
        }
    }

    // Process block with separate input/output
    void processBlock(const float* input, float* output, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            output[i] = process(input[i]);
        }
    }

    void reset() noexcept { z1_ = 0.0f; z2_ = 0.0f; }

private:
    float b0_{1.0f}, b1_{0.0f}, b2_{0.0f};
    float a1_{0.0f}, a2_{0.0f};
    float z1_{0.0f}, z2_{0.0f};  // state variables
};

// ---------------------------------------------------------------------------
// Multi-band parametric EQ (N bands, cascaded biquads)
// ---------------------------------------------------------------------------

template <std::size_t NumBands>
class ParametricEq {
public:
    ParametricEq() = default;

    void setBand(std::size_t index, FilterType type, float freq, float Q,
                 float gainDb, float sampleRate) noexcept {
        if (index < NumBands) {
            bands_[index].setFilter(type, freq, Q, gainDb, sampleRate);
        }
    }

    [[nodiscard]] float process(float input) noexcept {
        float sample = input;
        for (auto& band : bands_) {
            sample = band.process(sample);
        }
        return sample;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        for (auto& band : bands_) {
            band.processBlock(data, count);
        }
    }

    void reset() noexcept {
        for (auto& band : bands_) band.reset();
    }

private:
    std::array<BiquadFilter, NumBands> bands_;
};

// 10-band parametric EQ (studio standard)
using ParametricEq10 = ParametricEq<10>;

// 31-band graphic EQ
using ParametricEq31 = ParametricEq<31>;

} // namespace cm::neva::dsp
