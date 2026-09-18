#pragma once
// ============================================================================
// ASIO Driver — CoreMusic NevaEngine
// Steinberg ASIO SDK 2.3.4 integration
// 8.1 Surround (8 channels), exclusive lock
// ============================================================================

#include "audio_driver.h"

// Forward declare ASIO SDK types (avoid header dependency in header)
struct ASIODriver;
typedef long ASIOSampleRate;
typedef struct ASIOSampleRate_s { double sampleRate; } ASIOSampleRate_t;

namespace cm::neva {

// ---------------------------------------------------------------------------
// ASIO Driver Implementation
// ---------------------------------------------------------------------------

class AsioDriver final : public AudioDriver {
public:
    AsioDriver() = default;
    ~AsioDriver() override { shutdown(); }

    // Non-copyable
    AsioDriver(const AsioDriver&) = delete;
    AsioDriver& operator=(const AsioDriver&) = delete;

    // -----------------------------------------------------------------------
    // AudioDriver interface
    // -----------------------------------------------------------------------

    bool initialize(const AudioFormat& format) override {
        format_ = format;

        // ASIO SDK initialization
        // In production: CoInitialize, find ASIO driver by name, create COM object
        // Here: abstract the SDK calls
        asioBufferSize_ = format.bufferFrames;
        asioSampleRate_ = format.sampleRate;

        state_.store(DriverState::Connected, std::memory_order_release);
        return true;
    }

    bool start() override {
        if (state_.load(std::memory_order_acquire) != DriverState::Connected) {
            return false;
        }

        // ASIOStart() — begins calling callback
        state_.store(DriverState::Running, std::memory_order_release);
        return true;
    }

    bool stop() override {
        // ASIOStop()
        state_.store(DriverState::Connected, std::memory_order_release);
        return true;
    }

    bool shutdown() override {
        stop();
        // ASIODisposeDriver(), CoUninitialize()
        state_.store(DriverState::Disconnected, std::memory_order_release);
        return true;
    }

    DriverCapabilities getCapabilities() const override {
        DriverCapabilities caps;
        caps.name = "ASIO Driver";
        caps.minBufferSize = 64;
        caps.maxBufferSize = 8192;
        caps.preferredBufferSize = 512;
        caps.supportedSampleRates = { 44100, 48000, 88200, 96000, 176400, 192000 };
        caps.inputChannels = 8;    // 7.1 surround
        caps.outputChannels = 8;
        caps.supportsExclusive = true;
        caps.supportsAsio = true;
        return caps;
    }

    DriverState getState() const override {
        return state_.load(std::memory_order_acquire);
    }

    std::string getLastError() const override {
        return lastError_;
    }

    bool setBufferSize(std::uint32_t frames) override {
        if (frames < 64 || frames > 8192) {
            lastError_ = "Buffer size out of ASIO range (64-8192)";
            return false;
        }
        asioBufferSize_ = frames;
        // ASIOSetBufferSize(frames) — may require restart
        return true;
    }

    bool setSampleRate(std::uint32_t rate) override {
        asioSampleRate_ = rate;
        // ASIOSetSampleRate(static_cast<double>(rate))
        return true;
    }

    bool setInputChannelName(std::uint32_t index, const std::string& name) override {
        if (index >= 8) return false;
        inputChannelNames_[index] = name;
        return true;
    }

    bool setOutputChannelName(std::uint32_t index, const std::string& name) override {
        if (index >= 8) return false;
        outputChannelNames_[index] = name;
        return true;
    }

    // -----------------------------------------------------------------------
    // ASIO callback — called from ASIO driver thread
    // This is the real-time audio callback (noexcept critical)
    // -----------------------------------------------------------------------

    void asioCallback(long bufferIndex, bool /*directProcess*/) noexcept {
        if (state_.load(std::memory_order_acquire) != DriverState::Running) return;
        if (!processor_) return;

        const auto numSamples = static_cast<std::uint32_t>(asioBufferSize_);
        const auto numChannels = format_.channels;

        // Build channel pointer arrays from ASIO buffer info
        // In production: read from ASIOBufferInfo structures
        std::array<float*, kMaxChannels> inputPtrs{};
        std::array<float*, kMaxChannels> outputPtrs{};

        // Point to ASIO double-buffer (interleaved → deinterleaved)
        for (std::uint32_t ch = 0; ch < numChannels && ch < kMaxChannels; ++ch) {
            inputPtrs[ch] = asioInputBuffers_[bufferIndex][ch].data();
            outputPtrs[ch] = asioOutputBuffers_[bufferIndex][ch].data();
        }

        // Process through engine
        processor_->processBlock(outputPtrs.data(),
                                 const_cast<const float**>(inputPtrs.data()),
                                 numChannels, numSamples);
    }

    // -----------------------------------------------------------------------
    // ASIO buffer switch (double-buffering)
    // -----------------------------------------------------------------------

    void bufferSwitch(long bufferIndex, bool directProcess) noexcept {
        asioCallback(bufferIndex, directProcess);
    }

    // -----------------------------------------------------------------------
    // Sample rate changed callback
    // -----------------------------------------------------------------------

    void sampleRateChanged(ASIOSampleRate newRate) noexcept {
        asioSampleRate_ = static_cast<std::uint32_t>(newRate.sampleRate);
    }

    // -----------------------------------------------------------------------
    // Reset callback
    // -----------------------------------------------------------------------

    void resetRequest() noexcept {
        // ASIO driver requests reset (buffer size / sample rate change)
        stop();
        // Re-initialize will be called by application
    }

private:
    AudioFormat format_{};
    std::uint32_t asioBufferSize_ = 512;
    std::uint32_t asioSampleRate_ = 48000;

    // Channel names
    std::array<std::string, 8> inputChannelNames_ = {
        "FL", "FR", "C", "LFE", "SL", "SR", "RL", "RR"
    };
    std::array<std::string, 8> outputChannelNames_ = {
        "FL", "FR", "C", "LFE", "SL", "SR", "RL", "RR"
    };

    // Double-buffer storage (in production: managed by ASIO SDK)
    static constexpr std::size_t kMaxAsioBuffers = 2;
    static constexpr std::uint32_t kMaxBufferFrames = 8192;

    std::array<std::array<std::array<float, kMaxBufferFrames>, kMaxChannels>, kMaxAsioBuffers>
        asioInputBuffers_{};
    std::array<std::array<std::array<float, kMaxBufferFrames>, kMaxChannels>, kMaxAsioBuffers>
        asioOutputBuffers_{};
};

} // namespace cm::neva
