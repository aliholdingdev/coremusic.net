#pragma once
// ============================================================================
// Dynamics Processor — Compressor + Limiter + Noise Gate
// Zero-allocation, noexcept, 32-bit float
// ============================================================================

#include <cmath>
#include <algorithm>
#include <array>

namespace cm::neva::dsp {

// ---------------------------------------------------------------------------
// Compressor
// ---------------------------------------------------------------------------

class Compressor {
public:
    Compressor() = default;

    void setParameters(float thresholdDb, float ratio, float attackMs,
                       float releaseMs, float kneeDb, float makeupGainDb,
                       float sampleRate) noexcept {
        threshold_ = std::pow(10.0f, thresholdDb / 20.0f);
        ratio_ = ratio;
        attackCoeff_ = std::exp(-1.0f / (attackMs * 0.001f * sampleRate));
        releaseCoeff_ = std::exp(-1.0f / (releaseMs * 0.001f * sampleRate));
        kneeWidth_ = std::pow(10.0f, kneeDb / 20.0f);
        makeupGain_ = std::pow(10.0f, makeupGainDb / 20.0f);
    }

    [[nodiscard]] float process(float input) noexcept {
        const float absIn = std::abs(input);
        const float level = std::max(absIn, 1e-20f);

        // Gain computation
        float gain = 1.0f;
        if (level > threshold_) {
            // Over threshold — apply compression
            float overDb = 20.0f * std::log10(level / threshold_);
            float compressedDb = overDb * (1.0f / ratio_ - 1.0f);
            gain = std::pow(10.0f, compressedDb / 20.0f);
        } else if (kneeWidth_ > 1.0f) {
            // Soft knee region
            float kneeRatio = (level - threshold_ * kneeWidth_) /
                              (threshold_ * (1.0f - kneeWidth_));
            if (kneeRatio > 0.0f) {
                float overDb = 20.0f * std::log10(level / threshold_);
                float compressedDb = overDb * (1.0f / ratio_ - 1.0f) * kneeRatio;
                gain = std::pow(10.0f, compressedDb / 20.0f);
            }
        }

        // Envelope follower
        float envTarget = gain;
        float coeff = (envTarget < envelope_) ? attackCoeff_ : releaseCoeff_;
        envelope_ = coeff * envelope_ + (1.0f - coeff) * envTarget;

        return input * envelope_ * makeupGain_;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = process(data[i]);
        }
    }

    void reset() noexcept { envelope_ = 1.0f; }

private:
    float threshold_{0.1f};
    float ratio_{4.0f};
    float attackCoeff_{0.9f};
    float releaseCoeff_{0.999f};
    float kneeWidth_{1.0f};
    float makeupGain_{1.0f};
    float envelope_{1.0f};
};

// ---------------------------------------------------------------------------
// True Peak Limiter (brick wall)
// ---------------------------------------------------------------------------

class Limiter {
public:
    Limiter() = default;

    void setParameters(float thresholdDb, float attackMs, float releaseMs,
                       float ceilingDb, float sampleRate) noexcept {
        threshold_ = std::pow(10.0f, thresholdDb / 20.0f);
        ceiling_ = std::pow(10.0f, ceilingDb / 20.0f);
        attackCoeff_ = std::exp(-1.0f / (attackMs * 0.001f * sampleRate));
        releaseCoeff_ = std::exp(-1.0f / (releaseMs * 0.001f * sampleRate));
    }

    [[nodiscard]] float process(float input) noexcept {
        const float absIn = std::abs(input);

        // Gain reduction
        float gain = 1.0f;
        if (absIn > threshold_) {
            gain = threshold_ / std::max(absIn, 1e-20f);
        }

        // Envelope follower (faster attack for limiter)
        float coeff = (gain < envelope_) ? attackCoeff_ : releaseCoeff_;
        envelope_ = coeff * envelope_ + (1.0f - coeff) * gain;

        float output = input * envelope_;

        // Hard ceiling clamp
        output = std::clamp(output, -ceiling_, ceiling_);

        return output;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = process(data[i]);
        }
    }

    void reset() noexcept { envelope_ = 1.0f; }

private:
    float threshold_{0.9f};
    float ceiling_{0.95f};
    float attackCoeff_{0.999f};
    float releaseCoeff_{0.9999f};
    float envelope_{1.0f};
};

// ---------------------------------------------------------------------------
// Noise Gate
// ---------------------------------------------------------------------------

class NoiseGate {
public:
    NoiseGate() = default;

    void setParameters(float thresholdDb, float attackMs, float releaseMs,
                       float holdMs, float sampleRate) noexcept {
        threshold_ = std::pow(10.0f, thresholdDb / 20.0f);
        attackCoeff_ = std::exp(-1.0f / (attackMs * 0.001f * sampleRate));
        releaseCoeff_ = std::exp(-1.0f / (releaseMs * 0.001f * sampleRate));
        holdSamples_ = static_cast<std::uint32_t>(holdMs * 0.001f * sampleRate);
    }

    [[nodiscard]] float process(float input) noexcept {
        const float absIn = std::abs(input);

        if (absIn > threshold_) {
            holdCounter_ = holdSamples_;
            state_ = 1.0f;
        } else if (holdCounter_ > 0) {
            --holdCounter_;
        } else {
            state_ = 0.0f;
        }

        float coeff = (state_ > envelope_) ? attackCoeff_ : releaseCoeff_;
        envelope_ = coeff * envelope_ + (1.0f - coeff) * state_;

        return input * envelope_;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = process(data[i]);
        }
    }

    void reset() noexcept {
        envelope_ = 0.0f;
        state_ = 0.0f;
        holdCounter_ = 0;
    }

private:
    float threshold_{0.01f};
    float attackCoeff_{0.9f};
    float releaseCoeff_{0.999f};
    std::uint32_t holdSamples_{4800};   // 100ms @ 48kHz
    std::uint32_t holdCounter_{0};
    float envelope_{0.0f};
    float state_{0.0f};
};

// ---------------------------------------------------------------------------
// Channel dynamics chain (Gate → Compressor → Limiter)
// ---------------------------------------------------------------------------

class ChannelDynamics {
public:
    ChannelDynamics() = default;

    void setup(float sampleRate) noexcept {
        gate_.setParameters(-40.0f, 0.1f, 100.0f, 100.0f, sampleRate);
        compressor_.setParameters(-20.0f, 4.0f, 10.0f, 100.0f, 6.0f, 0.0f, sampleRate);
        limiter_.setParameters(-1.0f, 0.1f, 100.0f, -0.3f, sampleRate);
    }

    [[nodiscard]] float process(float input) noexcept {
        float s = gate_.process(input);
        s = compressor_.process(s);
        s = limiter_.process(s);
        return s;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        gate_.processBlock(data, count);
        compressor_.processBlock(data, count);
        limiter_.processBlock(data, count);
    }

    void reset() noexcept {
        gate_.reset();
        compressor_.reset();
        limiter_.reset();
    }

private:
    NoiseGate   gate_;
    Compressor  compressor_;
    Limiter     limiter_;
};

} // namespace cm::neva::dsp
