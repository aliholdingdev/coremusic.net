// null_backend.cpp — kill-switch null backend (ADR-019 §2.2c son çare: sessizlik, state=ERROR).
// ADR-019 fallback zinciri terminal basamağı. Callback'i ÇAĞIRMAZ → çıktı daima sessizlik.
// RT yasağına uygun: tahsisat/lock/log yok, sadece durum bayrağı.

#include "neva/iaudio_backend.h"

namespace neva {
namespace {

class NullBackend final : public IAudioBackend {
public:
  BackendResult enumerate(DeviceList& out) override {
    out.count = 0; // cihaz yok
    return BackendResult::Ok;
  }

  BackendResult open(const StreamConfig& cfg) override {
    cfg_ = cfg;
    opened_ = true;
    return BackendResult::Ok; // her zaman açılır (kill-switch güvenli)
  }

  BackendResult start(RenderCallback, void*) noexcept override {
    if (!opened_) return BackendResult::Fatal;
    running_ = true;
    // Callback ÇAĞRILMAZ — sessizlik üretilir (ADR-019 §2.2c).
    return BackendResult::Ok;
  }

  BackendResult switchMode(StreamMode) override { return BackendResult::Ok; }
  BackendResult setDeviceRate(std::uint32_t rate) override {
    cfg_.sampleRate = rate;
    return BackendResult::Ok;
  }

  void stop() noexcept override { running_ = false; }
  void kill() noexcept override {
    running_ = false;
    opened_ = false;
  }

private:
  StreamConfig cfg_{};
  bool opened_ = false;
  bool running_ = false;
};

} // namespace

std::unique_ptr<IAudioBackend> createNullBackend() {
  return std::unique_ptr<IAudioBackend>(new NullBackend());
}

} // namespace neva
