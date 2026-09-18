#pragma once
// ============================================================================
// Audio Driver Interface — ASIO / WASAPI abstraction
// Platform-specific implementations inherit from this
// ============================================================================

#include "../core/types.h"
#include "../core/audio_processor.h"

#include <string>
#include <vector>
#include <functional>
#include <atomic>

namespace cm::neva {

// ---------------------------------------------------------------------------
// Driver capabilities
// ---------------------------------------------------------------------------

struct DriverCapabilities {
    std::string name;
    std::uint32_t minBufferSize = 64;
    std::uint32_t maxBufferSize = 8192;
    std::uint32_t preferredBufferSize = 512;
    std::vector<std::uint32_t> supportedSampleRates = { 44100, 48000, 88200, 96000 };
    std::uint32_t inputChannels = 8;
    std::uint32_t outputChannels = 8;
    bool supportsExclusive = false;
    bool supportsWasapi = false;
    bool supportsAsio = false;
};

// ---------------------------------------------------------------------------
// Driver state
// ---------------------------------------------------------------------------

enum class DriverState : std::uint8_t {
    Disconnected = 0,
    Connected    = 1,
    Running      = 2,
    Error        = 3,
};

// ---------------------------------------------------------------------------
// AudioDriver — abstract interface
// ---------------------------------------------------------------------------

class AudioDriver {
public:
    virtual ~AudioDriver() = default;

    // Lifecycle
    virtual bool initialize(const AudioFormat& format) = 0;
    virtual bool start() = 0;
    virtual bool stop() = 0;
    virtual bool shutdown() = 0;

    // Query
    [[nodiscard]] virtual DriverCapabilities getCapabilities() const = 0;
    [[nodiscard]] virtual DriverState getState() const = 0;
    [[nodiscard]] virtual std::string getLastError() const = 0;

    // Buffer management
    virtual bool setBufferSize(std::uint32_t frames) = 0;
    virtual bool setSampleRate(std::uint32_t rate) = 0;

    // Channel mapping (for multi-channel ASIO)
    virtual bool setInputChannelName(std::uint32_t index, const std::string& name) = 0;
    virtual bool setOutputChannelName(std::uint32_t index, const std::string& name) = 0;

    // Callback registration
    void setAudioProcessor(AudioProcessor* processor) noexcept {
        processor_ = processor;
    }

    // Metrics
    [[nodiscard]] float getLatencyMs() const noexcept {
        return latencyMs_.load(std::memory_order_acquire);
    }

    [[nodiscard]] std::uint64_t getOverruns() const noexcept {
        return overruns_.load(std::memory_order_acquire);
    }

    [[nodiscard]] std::uint64_t getUnderruns() const noexcept {
        return underruns_.load(std::memory_order_acquire);
    }

protected:
    AudioProcessor* processor_ = nullptr;
    std::atomic<float> latencyMs_{0.0f};
    std::atomic<std::uint64_t> overruns_{0};
    std::atomic<std::uint64_t> underruns_{0};
    std::atomic<DriverState> state_{DriverState::Disconnected};
    std::string lastError_;
};

// ---------------------------------------------------------------------------
// Driver event callback
// ---------------------------------------------------------------------------

enum class DriverEvent : std::uint8_t {
    DeviceConnected,
    DeviceDisconnected,
    BufferSizeChanged,
    SampleRateChanged,
    Error,
};

using DriverEventCallback = std::function<void(DriverEvent, const std::string&)>;

} // namespace cm::neva
