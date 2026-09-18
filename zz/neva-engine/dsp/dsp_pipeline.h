#pragma once
// ============================================================================
// 15-Stage DSP Pipeline — CoreMusic NevaEngine
// Processes one audio block (512 samples × 8 channels)
// Zero-allocation, noexcept, 32-bit float
// ============================================================================

#include "../core/types.h"
#include "biquad_filter.h"
#include "dynamics.h"
#include "crossover.h"

#include <array>
#include <span>
#include <cmath>

namespace cm::neva::dsp {

// ---------------------------------------------------------------------------
// Per-channel gain stage
// ---------------------------------------------------------------------------

class GainStage {
public:
    void setGainDb(float db) noexcept {
        gain_ = std::pow(10.0f, db / 20.0f);
    }

    [[nodiscard]] float process(float s) const noexcept { return s * gain_; }

    void processBlock(float* data, std::size_t count) const noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] *= gain_;
        }
    }

private:
    float gain_{1.0f};
};

// ---------------------------------------------------------------------------
// Delay line (variable length, 0–1000ms)
// ---------------------------------------------------------------------------

class DelayLine {
public:
    static constexpr std::size_t kMaxDelaySamples = 48000;  // 1s @ 48kHz

    void setDelayMs(float ms, float sampleRate) noexcept {
        delaySamples_ = static_cast<std::uint32_t>(ms * 0.001f * sampleRate);
        delaySamples_ = std::min(delaySamples_, kMaxDelaySamples);
    }

    [[nodiscard]] float process(float input) noexcept {
        float output = buffer_[readPos_];
        buffer_[writePos_] = input;
        writePos_ = (writePos_ + 1) % kMaxDelaySamples;
        readPos_ = (readPos_ + 1) % kMaxDelaySamples;
        return output;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = process(data[i]);
        }
    }

    void reset() noexcept {
        buffer_.fill(0.0f);
        readPos_ = 0;
        writePos_ = 0;
    }

private:
    std::array<float, kMaxDelaySamples> buffer_{};
    std::uint32_t readPos_{0};
    std::uint32_t writePos_{0};
    std::uint32_t delaySamples_{0};
};

// ---------------------------------------------------------------------------
// Simple stereo reverb (algorithmic, Schroeder-style)
// ---------------------------------------------------------------------------

class Reverb {
public:
    void setParameters(float roomSize, float damping, float wetDry,
                       float sampleRate) noexcept {
        wetDry_ = std::clamp(wetDry, 0.0f, 1.0f);
        const float decayTime = 0.1f + roomSize * 2.9f;  // 0.1–3.0s
        const float decayPerTap = std::pow(0.001f, 1.0f / (decayTime * sampleRate));

        // Allpass delays (ms → samples)
        constexpr float apDelays[] = {5.0f, 1.7f, 4.0f, 2.9f};
        for (std::size_t i = 0; i < 4; ++i) {
            const auto samples = static_cast<std::uint32_t>(apDelays[i] * 0.001f * sampleRate);
            allpassDelays_[i] = std::min(samples, kMaxDelaySamples - 1);
            allpassCoeff_[i] = 0.5f;
        }

        // Comb delays (ms → samples)
        constexpr float combDelays[] = {30.0f, 37.0f, 41.0f, 43.0f, 50.0f};
        for (std::size_t i = 0; i < 5; ++i) {
            const auto samples = static_cast<std::uint32_t>(combDelays[i] * 0.001f * sampleRate);
            combDelays_[i] = std::min(samples, kMaxDelaySamples - 1);
            combFeedback_[i] = damping * decayPerTap;
        }
    }

    [[nodiscard]] float process(float input) noexcept {
        // Comb filters (parallel)
        float combOut = 0.0f;
        for (std::size_t i = 0; i < 5; ++i) {
            float out = combBuffer_[i][combRead_[i]];
            combBuffer_[i][combWrite_[i]] = input + out * combFeedback_[i];
            combWrite_[i] = (combWrite_[i] + 1) % combDelays_[i];
            combRead_[i] = (combRead_[i] + 1) % combDelays_[i];
            combOut += out;
        }
        combOut *= 0.2f;  // normalize

        // Allpass filters (series)
        float apOut = combOut;
        for (std::size_t i = 0; i < 4; ++i) {
            float buf = allpassBuffer_[i][allpassRead_[i]];
            allpassBuffer_[i][allpassWrite_[i]] = apOut + buf * allpassCoeff_[i];
            allpassWrite_[i] = (allpassWrite_[i] + 1) % allpassDelays_[i];
            allpassRead_[i] = (allpassRead_[i] + 1) % allpassDelays_[i];
            apOut = buf - apOut * allpassCoeff_[i];
        }

        // Wet/dry mix
        return input * (1.0f - wetDry_) + apOut * wetDry_;
    }

    void processBlock(float* data, std::size_t count) noexcept {
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = process(data[i]);
        }
    }

    void reset() noexcept {
        for (auto& buf : combBuffer_) buf.fill(0.0f);
        for (auto& buf : allpassBuffer_) buf.fill(0.0f);
        combRead_ = {}; combWrite_ = {};
        allpassRead_ = {}; allpassWrite_ = {};
    }

private:
    static constexpr std::size_t kMaxDelaySamples = 4800;  // 100ms max

    float wetDry_{0.2f};

    std::array<std::array<float, kMaxDelaySamples>, 5> combBuffer_{};
    std::array<std::uint32_t, 5> combDelays_{};
    std::array<float, 5> combFeedback_{};
    std::array<std::uint32_t, 5> combRead_{};
    std::array<std::uint32_t, 5> combWrite_{};

    std::array<std::array<float, kMaxDelaySamples>, 4> allpassBuffer_{};
    std::array<std::uint32_t, 4> allpassDelays_{};
    std::array<float, 4> allpassCoeff_{};
    std::array<std::uint32_t, 4> allpassRead_{};
    std::array<std::uint32_t, 4> allpassWrite_{};
};

// ---------------------------------------------------------------------------
// Output routing matrix (channel mapping + level)
// ---------------------------------------------------------------------------

class OutputRouter {
public:
    // Set output level per channel
    void setOutputGain(std::size_t channel, float db) noexcept {
        if (channel < kSurround81Channels) {
            outputGain_[channel] = std::pow(10.0f, db / 20.0f);
        }
    }

    // Process: apply gain + hard clip protection
    [[nodiscard]] float process(float input, std::size_t channel) noexcept {
        if (channel >= kSurround81Channels) return 0.0f;
        float output = input * outputGain_[channel];
        return std::clamp(output, -1.0f, 1.0f);
    }

    void processBlock(float* data, std::size_t channel, std::size_t count) noexcept {
        if (channel >= kSurround81Channels) return;
        const float g = outputGain_[channel];
        for (std::size_t i = 0; i < count; ++i) {
            data[i] = std::clamp(data[i] * g, -1.0f, 1.0f);
        }
    }

private:
    std::array<float, kSurround81Channels> outputGain_{};
};

// ---------------------------------------------------------------------------
// DspPipeline — 15-stage processing chain per channel
// ---------------------------------------------------------------------------

class DspPipeline {
public:
    static constexpr std::size_t kMaxStages = static_cast<std::size_t>(DspStage::COUNT);

    void setup(float sampleRate) noexcept {
        sampleRate_ = sampleRate;
        for (auto& ch : channels_) {
            ch.gainIn.setGainDb(0.0f);
            ch.noiseGate.setParameters(-40.0f, 0.1f, 100.0f, 100.0f, sampleRate);
            ch.hpf.setFilter(FilterType::HighPass, 20.0f, 0.707f, 0.0f, sampleRate);
            ch.lpf.setFilter(FilterType::LowPass, 20000.0f, 0.707f, 0.0f, sampleRate);
            ch.dynamics.setup(sampleRate);
            ch.delay.setDelayMs(0.0f, sampleRate);
            ch.reverb.setParameters(0.5f, 0.5f, 0.0f, sampleRate);
            ch.gainOut.setGainDb(0.0f);
        }
    }

    // Process one block for all channels (called from audio callback)
    void processBlock(float** channels, std::size_t numChannels,
                      std::size_t numSamples) noexcept {
        const auto maxCh = std::min(numChannels, kSurround81Channels);

        for (std::size_t ch = 0; ch < maxCh; ++ch) {
            auto& proc = channels_[ch];

            // Stage 1-2: Input Gain
            proc.gainIn.processBlock(channels[ch], numSamples);

            // Stage 3: Noise Gate (only for non-LFE)
            if (ch != kLFEChannel) {
                proc.noiseGate.processBlock(channels[ch], numSamples);
            }

            // Stage 4: High-Pass Filter
            proc.hpf.processBlock(channels[ch], numSamples);

            // Stage 5: Low-Pass Filter
            proc.lpf.processBlock(channels[ch], numSamples);

            // Stages 6-7: EQ (placeholder — uses biquad chain)
            for (auto& band : proc.eq) {
                band.processBlock(channels[ch], numSamples);
            }

            // Stages 8-9-10: Dynamics (Gate → Compressor → Limiter)
            proc.dynamics.processBlock(channels[ch], numSamples);

            // Stage 11: Crossover (subwoofer routing handled externally)
            // Stage 12: Delay
            if (ch != kLFEChannel) {
                proc.delay.processBlock(channels[ch], numSamples);
            }

            // Stage 13: Reverb (skip for LFE)
            if (ch != kLFEChannel) {
                proc.reverb.processBlock(channels[ch], numSamples);
            }

            // Stage 14-15: Output Gain + Routing (hard clip)
            proc.gainOut.processBlock(channels[ch], numSamples);
        }
    }

    // Per-channel accessors for parameter setting
    [[nodiscard]] auto& channel(std::size_t ch) noexcept { return channels_[ch]; }
    [[nodiscard]] const auto& channel(std::size_t ch) const noexcept { return channels_[ch]; }

    void reset() noexcept {
        for (auto& ch : channels_) {
            ch.gainIn = GainStage{};
            ch.noiseGate.reset();
            ch.hpf.reset();
            ch.lpf.reset();
            ch.dynamics.reset();
            for (auto& b : ch.eq) b.reset();
            ch.delay.reset();
            ch.reverb.reset();
            ch.gainOut = GainStage{};
        }
    }

private:
    float sampleRate_{static_cast<float>(kSampleRate)};

    struct ChannelProc {
        GainStage       gainIn;
        NoiseGate       noiseGate;
        BiquadFilter    hpf;
        BiquadFilter    lpf;
        std::array<BiquadFilter, 10> eq;   // 10-band parametric
        ChannelDynamics dynamics;
        DelayLine       delay;
        Reverb          reverb;
        GainStage       gainOut;
    };

    std::array<ChannelProc, kSurround81Channels> channels_;
};

} // namespace cm::neva::dsp
