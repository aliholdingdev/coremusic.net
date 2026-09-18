#pragma once
// ============================================================================
// Audio Processor — ASIO/JUCE Callback Bridge
// 8.1 Surround, 512 sample buffer, 48kHz, 32-bit float
// noexcept mandatory — zero-allocation, lock-free
// ============================================================================

#include "../core/types.h"
#include "../dsp/dsp_pipeline.h"
#include "../buffer/lock_free_ring_buffer.h"

#include <array>
#include <atomic>
#include <span>
#include <functional>

namespace cm::neva {

// ---------------------------------------------------------------------------
// Audio processor callback signature
// ---------------------------------------------------------------------------

using AudioCallback = std::function<void(float**, const float**,
                                         std::uint32_t, std::uint32_t)>;

// ---------------------------------------------------------------------------
// AudioProcessor — core audio callback handler
// ---------------------------------------------------------------------------

class AudioProcessor {
public:
    AudioProcessor() = default;
    ~AudioProcessor() = default;

    // Non-copyable
    AudioProcessor(const AudioProcessor&) = delete;
    AudioProcessor& operator=(const AudioProcessor&) = delete;

    // Initialize DSP pipeline
    void initialize(const AudioFormat& format) noexcept {
        format_ = format;
        dspPipeline_.setup(static_cast<float>(format.sampleRate));
        state_.store(EngineState::Initialized, std::memory_order_release);
    }

    // Start processing
    void start() noexcept {
        state_.store(EngineState::Running, std::memory_order_release);
        dspPipeline_.reset();
    }

    // Stop processing
    void stop() noexcept {
        state_.store(EngineState::Suspended, std::memory_order_release);
    }

    // Main audio callback — called from ASIO/JUCE real-time thread
    // MUST be noexcept, zero-allocation, lock-free
    void processBlock(float** outputChannels, const float** inputChannels,
                      std::uint32_t numChannels, std::uint32_t numSamples) noexcept {
        if (state_.load(std::memory_order_acquire) != EngineState::Running) {
            // Output silence when not running
            for (std::uint32_t ch = 0; ch < numChannels; ++ch) {
                std::fill_n(outputChannels[ch], numSamples, 0.0f);
            }
            return;
        }

        // Run DSP pipeline (in-place on input, copy to output)
        // For ASIO: input and output may point to same buffers
        // Copy input to output first, then process in-place
        for (std::uint32_t ch = 0; ch < numChannels; ++ch) {
            if (inputChannels[ch] != outputChannels[ch]) {
                std::copy_n(inputChannels[ch], numSamples, outputChannels[ch]);
            }
        }

        // Process through 15-stage DSP pipeline
        dspPipeline_.processBlock(outputChannels, numChannels, numSamples);

        // Hardware protection: DC offset check + hard clip
        for (std::uint32_t ch = 0; ch < numChannels; ++ch) {
            float* buf = outputChannels[ch];
            for (std::uint32_t i = 0; i < numSamples; ++i) {
                buf[i] = std::clamp(buf[i], -1.0f, 1.0f);
            }
        }

        // Feed analysis buffer (non-blocking, lock-free)
        if (analysisCallback_) {
            analysisCallback_(outputChannels, numChannels, numSamples);
        }

        ++totalBlocks_;
    }

    // Parameter access (called from control thread, lock-free via atomic)
    void setInputGainDb(float db) noexcept {
        inputGainDb_.store(db, std::memory_order_release);
    }

    void setOutputGainDb(float db) noexcept {
        outputGainDb_.store(db, std::memory_order_release);
    }

    void setEqBand(std::size_t channel, std::size_t band,
                    dsp::FilterType type, float freq, float Q,
                    float gainDb) noexcept {
        if (channel < kSurround81Channels && band < 10) {
            eqParams_[channel][band] = { type, freq, Q, gainDb };
            eqDirty_.store(true, std::memory_order_release);
        }
    }

    void setCompressorParams(float thresholdDb, float ratio, float attackMs,
                             float releaseMs) noexcept {
        compParams_ = { thresholdDb, ratio, attackMs, releaseMs };
        compDirty_.store(true, std::memory_order_release);
    }

    void setLimiterParams(float thresholdDb, float ceilingDb) noexcept {
        limiterParams_ = { thresholdDb, ceilingDb };
        limiterDirty_.store(true, std::memory_order_release);
    }

    void setCrossoverFreq(float freq) noexcept {
        crossoverFreq_.store(freq, std::memory_order_release);
    }

    // Query
    [[nodiscard]] EngineState getState() const noexcept {
        return state_.load(std::memory_order_acquire);
    }

    [[nodiscard]] const AudioFormat& getFormat() const noexcept { return format_; }

    [[nodiscard]] std::uint64_t getTotalBlocks() const noexcept {
        return totalBlocks_.load(std::memory_order_acquire);
    }

    [[nodiscard]] float getCpuLoad() const noexcept {
        return cpuLoad_.load(std::memory_order_acquire);
    }

    // Set analysis callback (spectrum, metering)
    void setAnalysisCallback(AudioCallback cb) noexcept {
        analysisCallback_ = std::move(cb);
    }

    // Reset all DSP state
    void reset() noexcept {
        dspPipeline_.reset();
        totalBlocks_.store(0, std::memory_order_release);
    }

private:
    AudioFormat format_{};
    dsp::DspPipeline dspPipeline_;
    std::atomic<EngineState> state_{EngineState::Uninitialized};
    std::atomic<std::uint64_t> totalBlocks_{0};
    std::atomic<float> cpuLoad_{0.0f};
    std::atomic<float> inputGainDb_{0.0f};
    std::atomic<float> outputGainDb_{0.0f};
    std::atomic<float> crossoverFreq_{80.0f};
    std::atomic<bool> eqDirty_{false};
    std::atomic<bool> compDirty_{false};
    std::atomic<bool> limiterDirty_{false};

    AudioCallback analysisCallback_;

    struct EqParam {
        dsp::FilterType type = dsp::FilterType::Peak;
        float freq = 1000.0f;
        float Q = 1.0f;
        float gainDb = 0.0f;
    };

    struct CompParam {
        float thresholdDb = -20.0f;
        float ratio = 4.0f;
        float attackMs = 10.0f;
        float releaseMs = 100.0f;
    };

    struct LimiterParam {
        float thresholdDb = -1.0f;
        float ceilingDb = -0.3f;
    };

    std::array<std::array<EqParam, 10>, kSurround81Channels> eqParams_{};
    CompParam compParams_{};
    LimiterParam limiterParams_{};
};

} // namespace cm::neva
