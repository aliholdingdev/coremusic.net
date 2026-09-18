#pragma once
// ============================================================================
// NevaEngine Core Types — C++20
// CoreMusic Audio Engine (L6 Electronics)
// ADR-017: 32-bit float PCM | ADR-038: 8.1 Surround
// ============================================================================

#include <cstdint>
#include <array>
#include <atomic>
#include <span>
#include <string_view>

namespace cm::neva {

// ---------------------------------------------------------------------------
// Constants (constexpr — zero runtime cost)
// ---------------------------------------------------------------------------

inline constexpr std::uint32_t kSampleRate       = 48'000;
inline constexpr std::uint32_t kDefaultBufferFrames = 512;
inline constexpr std::uint32_t kMaxChannels       = 16;       // future-proof
inline constexpr std::uint32_t kSurround81Channels = 8;       // 7.1
inline constexpr std::uint32_t kLFEChannel        = 7;        // subwoofer index
inline constexpr float         kPi                = 3.14159265358979323846f;
inline constexpr float         kTwoPi             = 2.0f * kPi;
inline constexpr float         kInvSampleRate     = 1.0f / static_cast<float>(kSampleRate);
inline constexpr float         kEpsilon           = 1e-20f;

// ---------------------------------------------------------------------------
// Channel identifiers (7.1 surround layout)
// ---------------------------------------------------------------------------

enum class Channel : std::uint8_t {
    FrontLeft   = 0,
    FrontRight  = 1,
    Center      = 2,
    LFE         = 3,   // subwoofer
    SurroundLeft  = 4,
    SurroundRight = 5,
    RearLeft   = 6,
    RearRight  = 7,
};

inline constexpr std::array<std::string_view, kSurround81Channels> kChannelNames = {{
    "FL", "FR", "C", "LFE", "SL", "SR", "RL", "RR"
}};

// ---------------------------------------------------------------------------
// Audio format descriptor (immutable after construction)
// ---------------------------------------------------------------------------

struct AudioFormat {
    std::uint32_t sampleRate   = kSampleRate;
    std::uint32_t bufferFrames = kDefaultBufferFrames;
    std::uint32_t channels     = kSurround81Channels;
    std::uint32_t bitsPerSample = 32;
    bool          isFloat      = true;  // ADR-017: always 32-bit float
};

// ---------------------------------------------------------------------------
// Engine state (lock-free atomic)
// ---------------------------------------------------------------------------

enum class EngineState : std::uint8_t {
    Uninitialized = 0,
    Initialized   = 1,
    Running       = 2,
    Suspended     = 3,
    Error         = 4,
};

// ---------------------------------------------------------------------------
// DSP stage identifiers
// ---------------------------------------------------------------------------

enum class DspStage : std::uint8_t {
    InputGain     = 0,
    NoiseGate     = 1,
    HighPass      = 2,
    LowPass       = 3,
    ParametricEq  = 4,
    GraphicEq     = 5,
    Compressor    = 6,
    Limiter       = 7,
    Loudness      = 8,
    Crossover     = 9,
    Delay         = 10,
    Reverb        = 11,
    OutputGain    = 12,
    OutputRouting = 13,
    COUNT         = 14,
};

// ---------------------------------------------------------------------------
// Protection thresholds (hardware watchdog)
// ---------------------------------------------------------------------------

struct ProtectionLimits {
    float maxOutputLevel  =  1.0f;    // hard clip
    float dcOffsetWarn    =  0.05f;   // 0.5V via 10:1 divider
    float dcOffsetFault   =  0.50f;   // 5.0V — relay trip
    float maxTemperature  = 85.0f;    // °C — fan ramp
    float faultTemperature= 95.0f;    // °C — shutdown
    float minSupplyVoltage= 18.0f;    // V — under-voltage
    float maxSupplyVoltage= 40.0f;    // V — over-voltage
};

} // namespace cm::neva
