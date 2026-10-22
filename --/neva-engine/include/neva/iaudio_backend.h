/* iaudio_backend.h — ADR-019 §2.2b ortak arayuz (Bridge + Adapter).
 * KURAL: Neva cekirdegi (decode/DSP/state machine) YALNIZ bu arayuzu gorur.
 * OS API'si cekirdekte YASAK; #ifdef/OS kodu yalniz adapter dosyalarinda (ADR-019 kural 1).
 * Not: ADR'deki imza (enumerate() -> BackendInfo, start(CallbackFn)) derlenebilirlik
 * icin minik duzeltildi: enumerate(DeviceList&) out-param + start(cb, userData). Semantik ayni. */
#ifndef NEVA_IAUDIO_BACKEND_H
#define NEVA_IAUDIO_BACKEND_H

#include <cstdint>
#include <memory>

namespace neva {

/* ADR-019 kural 4: tum adapter'lar ayni hata enum'unu dondurur -> fallback tablosu OS'tan bagimsiz. */
enum class BackendResult : std::uint32_t {
  Ok = 0, DeviceLost, RateUnsupported, Busy, Permission, Fatal
};

enum class StreamMode : std::uint32_t { Exclusive, Shared };

struct StreamConfig {
  std::uint32_t sampleRate = 48000;
  std::uint16_t channels = 2;
  StreamMode mode = StreamMode::Exclusive;
};

/* RT render callback (ADR-017): icinde tahsisat/lock/log YOK.
 * out = interleaved float cikis buffer'i (cihaz formatinda), frames = kare sayisi. */
using RenderCallback = void (*)(void* userData, float* out, std::uint32_t frames,
                                std::uint16_t channels) noexcept;

/* Cihaz listesi — sabit boyut (RT-disi; yine de tahsisatsiz). */
struct DeviceList {
  static constexpr std::uint32_t kMax = 16;
  std::uint32_t count = 0;
  char names[kMax][64] = {};
};

class IAudioBackend {
public:
  virtual ~IAudioBackend() = default;
  virtual BackendResult enumerate(DeviceList& out) = 0;
  virtual BackendResult open(const StreamConfig& cfg) = 0;
  virtual BackendResult start(RenderCallback cb, void* userData) noexcept = 0;
  virtual BackendResult switchMode(StreamMode mode) = 0;
  virtual BackendResult setDeviceRate(std::uint32_t rate) = 0;
  virtual void stop() noexcept = 0;
  virtual void kill() noexcept = 0; /* kill-switch (ADR-019 §2.2c) */
};

/* Adapter fabrikalari. Cekirdek yalniz IAudioBackend gormeye devam eder.
 * createWasapiBackend: Windows disinda veya basarisizlikta nullptr dondurur. */
std::unique_ptr<IAudioBackend> createNullBackend();
std::unique_ptr<IAudioBackend> createWasapiBackend();

} // namespace neva
#endif /* NEVA_IAUDIO_BACKEND_H */
