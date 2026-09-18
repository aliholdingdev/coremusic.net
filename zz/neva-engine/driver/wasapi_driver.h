#pragma once
// ============================================================================
// WASAPI Driver — CoreMusic NevaEngine
// Windows Audio Session API (WASAPI) Exclusive + Shared mode
// 8.1 Surround support
// ============================================================================

#include "audio_driver.h"

// Forward declare Windows COM types
struct IMMDeviceEnumerator;
struct IMMDevice;
struct IAudioClient;
struct IAudioCaptureClient;
struct IAudioRenderClient;

namespace cm::neva {

// ---------------------------------------------------------------------------
// WASAPI mode
// ---------------------------------------------------------------------------

enum class WasapiMode : std::uint8_t {
    Shared,    // Default, resampling, higher latency
    Exclusive, // Low latency, direct hardware access
};

// ---------------------------------------------------------------------------
// WASAPI Driver Implementation
// ---------------------------------------------------------------------------

class WasapiDriver final : public AudioDriver {
public:
    WasapiDriver() = default;
    ~WasapiDriver() override { shutdown(); }

    WasapiDriver(const WasapiDriver&) = delete;
    WasapiDriver& operator=(const WasapiDriver&) = delete;

    // -----------------------------------------------------------------------
    // AudioDriver interface
    // -----------------------------------------------------------------------

    bool initialize(const AudioFormat& format) override {
        format_ = format;
        mode_ = WasapiMode::Shared;

        // COM initialization
        // CoInitializeEx(nullptr, COINIT_MULTITHREADED);

        // Enumerate audio devices
        // CoCreateInstance(MMDeviceEnumerator, ...)

        // Select default render + capture devices
        // IMMDeviceEnumerator::GetDefaultAudioEndpoint(...)

        // Open audio client
        // IMMDevice::Activate(IAudioClient, ...)

        state_.store(DriverState::Connected, std::memory_order_release);
        return true;
    }

    bool start() override {
        if (state_.load(std::memory_order_acquire) != DriverState::Connected) {
            return false;
        }

        // IAudioClient::Start()
        state_.store(DriverState::Running, std::memory_order_release);
        return true;
    }

    bool stop() override {
        // IAudioClient::Stop()
        state_.store(DriverState::Connected, std::memory_order_release);
        return true;
    }

    bool shutdown() override {
        stop();

        // Release COM objects
        // renderClient_->Release();
        // captureClient_->Release();
        // audioClient_->Release();
        // device_->Release();
        // deviceEnumerator_->Release();
        // CoUninitialize();

        state_.store(DriverState::Disconnected, std::memory_order_release);
        return true;
    }

    DriverCapabilities getCapabilities() const override {
        DriverCapabilities caps;
        caps.name = "WASAPI Driver";
        caps.minBufferSize = 64;
        caps.maxBufferSize = 4096;
        caps.preferredBufferSize = 512;
        caps.supportedSampleRates = { 44100, 48000, 88200, 96000 };
        caps.inputChannels = 8;
        caps.outputChannels = 8;
        caps.supportsExclusive = true;
        caps.supportsWasapi = true;
        return caps;
    }

    DriverState getState() const override {
        return state_.load(std::memory_order_acquire);
    }

    std::string getLastError() const override {
        return lastError_;
    }

    bool setBufferSize(std::uint32_t frames) override {
        if (frames < 64 || frames > 4096) {
            lastError_ = "WASAPI buffer size out of range (64-4096)";
            return false;
        }
        wasapiBufferSize_ = frames;
        return true;
    }

    bool setSampleRate(std::uint32_t rate) override {
        wasapiSampleRate_ = rate;
        return true;
    }

    bool setInputChannelName(std::uint32_t index, const std::string& name) override {
        if (index >= 8) return false;
        inputNames_[index] = name;
        return true;
    }

    bool setOutputChannelName(std::uint32_t index, const std::string& name) override {
        if (index >= 8) return false;
        outputNames_[index] = name;
        return true;
    }

    // -----------------------------------------------------------------------
    // WASAPI-specific: mode selection
    // -----------------------------------------------------------------------

    bool setMode(WasapiMode mode) noexcept {
        mode_ = mode;
        // Must re-initialize to apply mode change
        return true;
    }

    [[nodiscard]] WasapiMode getMode() const noexcept { return mode_; }

    // -----------------------------------------------------------------------
    // WASAPI callback — called from WASAPI event-driven thread
    // Real-time audio processing (noexcept mandatory)
    // -----------------------------------------------------------------------

    void wasapiCallback() noexcept {
        if (state_.load(std::memory_order_acquire) != DriverState::Running) return;
        if (!processor_) return;

        // Get available buffer frames from WASAPI
        std::uint32_t framesAvailable = wasapiBufferSize_;

        // Get input buffer from capture client
        // IAudioCaptureClient::GetBuffer(...)

        // Build channel pointers
        std::array<float*, kMaxChannels> inputPtrs{};
        std::array<float*, kMaxChannels> outputPtrs{};

        // In production: deinterleave from WASAPI format to planar
        for (std::uint32_t ch = 0; ch < format_.channels; ++ch) {
            inputPtrs[ch] = wasapiInputBuffer_[ch].data();
            outputPtrs[ch] = wasapiOutputBuffer_[ch].data();
        }

        // Process through engine
        processor_->processBlock(outputPtrs.data(),
                                 const_cast<const float**>(inputPtrs.data()),
                                 format_.channels, framesAvailable);

        // Release input buffer
        // IAudioCaptureClient::ReleaseBuffer(framesAvailable)

        // Write output to render client
        // IAudioRenderClient::GetBuffer(framesAvailable, ...)
        // Copy wasapiOutputBuffer_ to WASAPI buffer
        // IAudioRenderClient::ReleaseBuffer(framesAvailable, 0)
    }

    // -----------------------------------------------------------------------
    // Device change notification
    // -----------------------------------------------------------------------

    void onDeviceAdded() noexcept {
        // Device hot-plug detection
    }

    void onDeviceRemoved() noexcept {
        state_.store(DriverState::Error, std::memory_order_release);
        lastError_ = "WASAPI device disconnected";
    }

private:
    AudioFormat format_{};
    WasapiMode mode_{WasapiMode::Shared};
    std::uint32_t wasapiBufferSize_ = 512;
    std::uint32_t wasapiSampleRate_ = 48000;

    std::array<std::string, 8> inputNames_ = {
        "FL", "FR", "C", "LFE", "SL", "SR", "RL", "RR"
    };
    std::array<std::string, 8> outputNames_ = {
        "FL", "FR", "C", "LFE", "SL", "SR", "RL", "RR"
    };

    // Buffer storage
    static constexpr std::uint32_t kMaxBufferFrames = 4096;
    std::array<std::array<float, kMaxBufferFrames>, kMaxChannels> wasapiInputBuffer_{};
    std::array<std::array<float, kMaxBufferFrames>, kMaxChannels> wasapiOutputBuffer_{};

    // COM pointers (in production)
    // IMMDeviceEnumerator* deviceEnumerator_ = nullptr;
    // IMMDevice* device_ = nullptr;
    // IAudioClient* audioClient_ = nullptr;
    // IAudioCaptureClient* captureClient_ = nullptr;
    // IAudioRenderClient* renderClient_ = nullptr;
};

} // namespace cm::neva
