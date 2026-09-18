#pragma once
// ============================================================================
// NevaEngine — Main Audio Engine Facade
// 8.1 Surround, ASIO + WASAPI, C++20
// ============================================================================

#include "../core/types.h"
#include "../core/audio_processor.h"
#include "../driver/audio_driver.h"
#include "../buffer/lock_free_ring_buffer.h"

#include <memory>
#include <array>
#include <atomic>
#include <string>
#include <functional>

namespace cm::neva {

// ---------------------------------------------------------------------------
// Engine configuration
// ---------------------------------------------------------------------------

struct EngineConfig {
    AudioFormat format{};
    std::string driverName;       // "ASIO" or "WASAPI"
    bool exclusiveMode = false;   // WASAPI exclusive
    bool lowLatency = true;       // ASIO preferred
    std::uint32_t ringBufferMs = 100;  // control channel buffer
};

// ---------------------------------------------------------------------------
// Engine metrics (real-time safe)
// ---------------------------------------------------------------------------

struct EngineMetrics {
    float cpuLoad = 0.0f;
    float latencyMs = 0.0f;
    std::uint64_t totalBlocks = 0;
    std::uint64_t overruns = 0;
    std::uint64_t underruns = 0;
    EngineState state = EngineState::Uninitialized;
};

// ---------------------------------------------------------------------------
// NevaEngine — the main audio engine
// ---------------------------------------------------------------------------

class NevaEngine {
public:
    NevaEngine() = default;
    ~NevaEngine() { shutdown(); }

    // Non-copyable
    NevaEngine(const NevaEngine&) = delete;
    NevaEngine& operator=(const NevaEngine&) = delete;

    // -----------------------------------------------------------------------
    // Lifecycle
    // -----------------------------------------------------------------------

    bool initialize(const EngineConfig& config) noexcept {
        if (state_.load(std::memory_order_acquire) != EngineState::Uninitialized) {
            shutdown();
        }

        config_ = config;
        processor_.initialize(config.format);
        state_.store(EngineState::Initialized, std::memory_order_release);
        return true;
    }

    bool start() noexcept {
        if (state_.load(std::memory_order_acquire) != EngineState::Initialized) {
            return false;
        }

        processor_.start();
        state_.store(EngineState::Running, std::memory_order_release);
        return true;
    }

    void stop() noexcept {
        processor_.stop();
        state_.store(EngineState::Suspended, std::memory_order_release);
    }

    void shutdown() noexcept {
        stop();
        processor_.reset();
        state_.store(EngineState::Uninitialized, std::memory_order_release);
    }

    // -----------------------------------------------------------------------
    // Parameter control (thread-safe via lock-free)
    // -----------------------------------------------------------------------

    void setInputGain(float db) noexcept {
        processor_.setInputGainDb(db);
    }

    void setOutputGain(float db) noexcept {
        processor_.setOutputGainDb(db);
    }

    void setEqBand(std::size_t channel, std::size_t band,
                    dsp::FilterType type, float freq, float Q,
                    float gainDb) noexcept {
        processor_.setEqBand(channel, band, type, freq, Q, gainDb);
    }

    void setCompressor(float thresholdDb, float ratio,
                       float attackMs, float releaseMs) noexcept {
        processor_.setCompressorParams(thresholdDb, ratio, attackMs, releaseMs);
    }

    void setLimiter(float thresholdDb, float ceilingDb) noexcept {
        processor_.setLimiterParams(thresholdDb, ceilingDb);
    }

    void setCrossoverFrequency(float freqHz) noexcept {
        processor_.setCrossoverFreq(freqHz);
    }

    // -----------------------------------------------------------------------
    // Query
    // -----------------------------------------------------------------------

    [[nodiscard]] EngineState getState() const noexcept {
        return state_.load(std::memory_order_acquire);
    }

    [[nodiscard]] EngineMetrics getMetrics() const noexcept {
        EngineMetrics m;
        m.cpuLoad = processor_.getCpuLoad();
        m.totalBlocks = processor_.getTotalBlocks();
        m.state = processor_.getState();
        if (driver_) {
            m.latencyMs = driver_->getLatencyMs();
            m.overruns = driver_->getOverruns();
            m.underruns = driver_->getUnderruns();
        }
        return m;
    }

    [[nodiscard]] const AudioFormat& getFormat() const noexcept {
        return processor_.getFormat();
    }

    // -----------------------------------------------------------------------
    // Driver management
    // -----------------------------------------------------------------------

    void setDriver(std::unique_ptr<AudioDriver> driver) noexcept {
        driver_ = std::move(driver);
        if (driver_) {
            driver_->setAudioProcessor(&processor_);
        }
    }

    [[nodiscard]] AudioDriver* getDriver() noexcept { return driver_.get(); }

    // -----------------------------------------------------------------------
    // Audio callback (called by driver)
    // -----------------------------------------------------------------------

    void processBlock(float** output, const float** input,
                      std::uint32_t channels, std::uint32_t samples) noexcept {
        processor_.processBlock(output, input, channels, samples);
    }

    // -----------------------------------------------------------------------
    // Analysis
    // -----------------------------------------------------------------------

    void setAnalysisCallback(AudioCallback cb) noexcept {
        processor_.setAnalysisCallback(std::move(cb));
    }

    // -----------------------------------------------------------------------
    // Preset management
    // -----------------------------------------------------------------------

    struct ChannelPreset {
        float inputGainDb = 0.0f;
        float outputGainDb = 0.0f;
        float delayMs = 0.0f;
        std::array<std::array<float, 4>, 10> eqParams{};  // type, freq, Q, gain
    };

    void loadPreset(const std::array<ChannelPreset, kSurround81Channels>& preset) noexcept {
        for (std::size_t ch = 0; ch < kSurround81Channels; ++ch) {
            const auto& p = preset[ch];
            // Apply via lock-free parameter updates
            // EQ bands set individually to avoid atomic races
            for (std::size_t band = 0; band < 10; ++band) {
                processor_.setEqBand(ch, band,
                    static_cast<dsp::FilterType>(static_cast<std::uint8_t>(p.eqParams[band][0])),
                    p.eqParams[band][1], p.eqParams[band][2], p.eqParams[band][3]);
            }
        }
    }

private:
    EngineConfig config_{};
    AudioProcessor processor_;
    std::unique_ptr<AudioDriver> driver_;
    std::atomic<EngineState> state_{EngineState::Uninitialized};
};

} // namespace cm::neva
