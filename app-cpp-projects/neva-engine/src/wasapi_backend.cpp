// wasapi_backend.cpp — WASAPI exclusive backend SKELETİ (ADR-0105 §2.5, ADR-019 fallback zinciri).
// Birincil yol: AUDCLNT_SHAREMODE_EXCLUSIVE (bit-perfect). Exclusive başarısızsa Shared'a düşer (ADR-019).
// Hot-plug: IMMNotificationClient non-RT thread'de atomic flag yazar → callback sessizlik üretir (§2.5).
// RT render thread: MMCSS "Pro Audio" (§2.5), event-driven, tahsisat/lock YOK (ADR-017).
//
// ⚠️ VERIFICATION REQUIRED: exclusive loopback capture davranışı gerçek cihazda teyit edilecek (ADR-0105 §5.1).
// ⚠️ VERIFICATION REQUIRED: native DSD waveform API'si Windows SDK header'da doğrulanmadı (§2.5).
// Non-Windows'ta createWasapiBackend() nullptr döner → session NullBackend'a fallback yapar (ADR-019).

#include "neva/iaudio_backend.h"

#if defined(_WIN32)

#ifndef NOMINMAX
#define NOMINMAX
#endif
#include <windows.h>
#include <mmdeviceapi.h>
#include <audioclient.h>
#include <avrt.h>          // AvSetMmThreadCharacteristicsW
#include <wrl/client.h>    // ComPtr (Windows SDK, header-only)
#include <functiondiscoverykeys_devpkey.h>  // PKEY_Device_FriendlyName
#include <propvarutil.h>   // PropVariantInit/Clear

#include <atomic>
#include <cstring>
#include <thread>

#pragma comment(lib, "ole32.lib")
#pragma comment(lib, "oleaut32.lib")
#pragma comment(lib, "avrt.lib")

namespace neva {
namespace {

using Microsoft::WRL::ComPtr;

// Hot-plug notifier: cihaz kopması → atomic flag. RT thread'de re-open YAPILMAZ (§2.5).
class DeviceNotifier final : public IMMNotificationClient {
public:
  std::atomic<bool> device_lost{false};

  IFACEMETHODIMP QueryInterface(REFIID riid, void** ppv) override {
    if (riid == __uuidof(IMMNotificationClient) || riid == IID_IUnknown) {
      *ppv = static_cast<IMMNotificationClient*>(this);
      AddRef();
      return S_OK;
    }
    *ppv = nullptr;
    return E_NOINTERFACE;
  }
  IFACEMETHODIMP_(ULONG) AddRef() override { return ++ref_; }
  IFACEMETHODIMP_(ULONG) Release() override {
    ULONG r = --ref_;
    if (r == 0) delete this;   // WRL ComPtr sözleşmesi: 0 → delete
    return r;
  }

  IFACEMETHODIMP OnDeviceStateChanged(LPCWSTR, DWORD) override { return S_OK; }
  IFACEMETHODIMP OnDeviceAdded(LPCWSTR) override { return S_OK; }
  IFACEMETHODIMP OnDeviceRemoved(LPCWSTR) override { device_lost.store(true, std::memory_order_release); return S_OK; }
  IFACEMETHODIMP OnDefaultDeviceChanged(EDataFlow, ERole, LPCWSTR) override { return S_OK; }
  IFACEMETHODIMP OnPropertyValueChanged(LPCWSTR, const PROPERTYKEY) override { return S_OK; }

private:
  ULONG ref_ = 1;
};

class WasapiBackend final : public IAudioBackend {
public:
  WasapiBackend() {
    CoInitializeEx(nullptr, COMINIT_MULTITHREADED);
    // WRL ComPtr out-param idiomu: IID_PPV_ARGS ham T** ister (ComPtrRef ile derlenmez).
    ::IMMDeviceEnumerator* raw = nullptr;
    HRESULT hr = CoCreateInstance(__uuidof(MMDeviceEnumerator), nullptr, CLSCTX_ALL,
                                  __uuidof(::IMMDeviceEnumerator), reinterpret_cast<void**>(&raw));
    if (SUCCEEDED(hr)) enumerator_.Attach(raw);
    // Hot-plug kaydı (§2.5): notifier non-RT thread'de atomic flag yazar.
    if (enumerator_) {
      notifier_.Attach(new DeviceNotifier());
      enumerator_->RegisterEndpointNotificationCallback(notifier_.Get());
    }
  }
  ~WasapiBackend() override {
    kill();
    if (enumerator_ && notifier_) enumerator_->UnregisterEndpointNotificationCallback(notifier_.Get());
    notifier_.Reset();
    CoUninitialize();
  }

  BackendResult enumerate(DeviceList& out) override {
    if (!enumerator_) return BackendResult::Fatal;
    out.count = 0;
    ComPtr<IMMDeviceCollection> coll;
    if (FAILED(enumerator_->EnumAudioEndpoints(eRender, DEVICE_STATE_ACTIVE, coll.ReleaseAndGetAddressOf())))
      return BackendResult::Fatal;
    UINT n = 0;
    coll->GetCount(&n);
    for (UINT i = 0; i < n && out.count < DeviceList::kMax; ++i) {
      ComPtr<IMMDevice> dev;
      if (FAILED(coll->Item(i, dev.ReleaseAndGetAddressOf()))) continue;
      ComPtr<IPropertyStore> ps;
      if (SUCCEEDED(dev->OpenPropertyStore(STGM_READ, ps.ReleaseAndGetAddressOf()))) {
        PROPVARIANT var;
        PropVariantInit(&var);
        if (SUCCEEDED(ps->GetValue(PKEY_Device_FriendlyName, &var)) && var.vt == VT_LPWSTR) {
          WideCharToMultiByte(CP_UTF8, 0, var.pwszVal, -1, out.names[out.count], 64, nullptr, nullptr);
        }
        PropVariantClear(&var);
      }
      ++out.count;
    }
    return BackendResult::Ok;
  }

  BackendResult open(const StreamConfig& cfg) override {
    close_stream();
    cfg_ = cfg;
    if (!device_) {
      if (!enumerator_) return BackendResult::Fatal;
      if (FAILED(enumerator_->GetDefaultAudioEndpoint(eRender, eConsole, device_.ReleaseAndGetAddressOf())))
        return BackendResult::DeviceLost;
    }
    // Activate/GetService void** ister → T** (ComPtr) reinterpret edilir.
    if (FAILED(device_->Activate(__uuidof(IAudioClient), CLSCTX_ALL, nullptr,
                                 reinterpret_cast<void**>(client_.ReleaseAndGetAddressOf()))))
      return BackendResult::Fatal;

    // ⚠️ VERIFICATION REQUIRED: exclusive loopback doğrulama (ADR-0105 §5.1).
    if (open_exclusive(cfg)) { mode_ = StreamMode::Exclusive; return setup_event() ? BackendResult::Ok : BackendResult::Fatal; }
    // ADR-019: exclusive başarısız (ör. AUDCLNT_E_DEVICE_IN_USE) → Shared'a düş.
    if (open_shared(cfg)) { mode_ = StreamMode::Shared; return setup_event() ? BackendResult::Ok : BackendResult::Fatal; }
    return BackendResult::Fatal;
  }

  BackendResult start(RenderCallback cb, void* userData) noexcept override {
    if (!client_ || !cb) return BackendResult::Fatal;
    cb_ = cb;
    user_data_ = userData;
    if (thread_.joinable()) thread_.join();   // önceki thread'i temizle (std::thread atanamazken terminate)
    running_.store(true, std::memory_order_release);
    // MMCSS "Pro Audio" render thread içinde (§2.5).
    thread_ = std::thread([this] { this->render_thread(); });
    if (FAILED(client_->Start())) { running_.store(false, std::memory_order_release); return BackendResult::Fatal; }
    return BackendResult::Ok;
  }

  BackendResult switchMode(StreamMode mode) override {
    StreamConfig c = cfg_;
    c.mode = mode;
    return open(c);
  }

  BackendResult setDeviceRate(std::uint32_t rate) override {
    StreamConfig c = cfg_;
    c.sampleRate = rate;
    return open(c);   // mismatch: önce cihaz hızı (§2.5)
  }

  void stop() noexcept override {
    running_.store(false, std::memory_order_release);
    if (event_) SetEvent(event_);
    if (thread_.joinable()) thread_.join();
    if (client_) client_->Stop();
  }

  void kill() noexcept override {
    stop();
    close_stream();
  }

private:
  // Event handle Initialize'dan sonra, Start'tan ÖNCE kurulmalı (aksihalde
  // IAudioClient::Start AUDCLNT_E_EVENTHANDLE_NOT_SET döner → race olmasın diye
  // render thread'de değil burada yapılır).
  bool setup_event() {
    if (!event_) event_ = ::CreateEventW(nullptr, FALSE, FALSE, nullptr);
    if (!event_ || !client_) return false;
    return SUCCEEDED(client_->SetEventHandle(event_));
  }

  bool open_exclusive(const StreamConfig& cfg) {
    // ⚠️ VERIFICATION REQUIRED: native DSD waveform API Windows SDK'da doğrulanmadı (§2.5) — şimdilik PCM float.
    WAVEFORMATEXTENSIBLE wfx{};
    std::memset(&wfx, 0, sizeof(wfx));
    wfx.Format.wFormatTag = WAVE_FORMAT_EXTENSIBLE;
    wfx.Format.nChannels = cfg.channels;
    wfx.Format.nSamplesPerSec = cfg.sampleRate;
    wfx.Format.wBitsPerSample = 32;
    wfx.Format.nBlockAlign = static_cast<WORD>(cfg.channels * 4);
    wfx.Format.nAvgBytesPerSec = wfx.Format.nSamplesPerSec * wfx.Format.nBlockAlign;
    wfx.Format.cbSize = sizeof(WAVEFORMATEXTENSIBLE) - sizeof(WAVEFORMATEX);
    wfx.Samples.wValidBitsPerSample = 32;
    wfx.dwChannelMask = (cfg.channels >= 2) ? (SPEAKER_FRONT_LEFT | SPEAKER_FRONT_RIGHT) : SPEAKER_FRONT_CENTER;
    wfx.SubFormat = KSDATAFORMAT_SUBTYPE_IEEE_FLOAT;

    // IsFormatSupported: S_OK = tam destek, S_FALSE = desteklenmiyor (closest) → exclusive'te S_OK şart.
    if (client_->IsFormatSupported(AUDCLNT_SHAREMODE_EXCLUSIVE, &wfx.Format, nullptr) != S_OK) return false;

    const REFERENCE_TIME dur = 200000;   // 20 ms buffer (§2.5: 192k'ta 1024/2048'ye yükseltilir — TODO benchmark §5.6)
    if (FAILED(client_->Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE, AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
                                   dur, 0, &wfx.Format, nullptr))) return false;

    if (FAILED(client_->GetBufferSize(&buffer_frames_))) return false;
    if (FAILED(client_->GetService(__uuidof(IAudioRenderClient),
                                   reinterpret_cast<void**>(render_.ReleaseAndGetAddressOf()))))
      return false;
    channels_ = cfg.channels;
    return true;
  }

  bool open_shared(const StreamConfig& cfg) {
    ComPtr<IAudioClient> fresh;
    if (FAILED(device_->Activate(__uuidof(IAudioClient), CLSCTX_ALL, nullptr,
                                 reinterpret_cast<void**>(fresh.ReleaseAndGetAddressOf())))) return false;
    client_ = fresh;
    WAVEFORMATEX* mix = nullptr;
    if (FAILED(client_->GetMixFormat(&mix))) return false;
    // Shared'ta kanal sayısı cihaz mix formatından gelir (ör. 5.1 cihazda stride doğru olsun).
    const std::uint16_t dev_ch = (mix && mix->nChannels > 0) ? mix->nChannels : cfg.channels;
    const REFERENCE_TIME dur = 200000;
    const HRESULT hr = client_->Initialize(AUDCLNT_SHAREMODE_SHARED, AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
                                           dur, 0, mix, nullptr);
    CoTaskMemFree(mix);
    if (FAILED(hr)) return false;
    if (FAILED(client_->GetBufferSize(&buffer_frames_))) return false;
    if (FAILED(client_->GetService(__uuidof(IAudioRenderClient),
                                   reinterpret_cast<void**>(render_.ReleaseAndGetAddressOf()))))
      return false;
    channels_ = dev_ch;
    (void)cfg;
    return true;
  }

  void render_thread() {
    // MMCSS "Pro Audio" — ses thread'i (§2.5).
    DWORD task_index = 0;
    HANDLE task = AvSetMmThreadCharacteristicsW(L"Pro Audio", &task_index);

    // Event handle open() içinde kuruldu (setup_event) — Start/SetEventHandle race yok.
    if (!event_ || !render_) {
      if (task) AvRevertMmThreadCharacteristics(task);
      return;
    }

    while (running_.load(std::memory_order_acquire)) {
      DWORD w = WaitForSingleObject(event_, 2000);
      if (w != WAIT_OBJECT_0) break;

      // Hot-plug: cihaz koptu → sessizlik üret, re-open YAPMA (§2.5; fallback non-RT'te).
      if (notifier_ && notifier_->device_lost.load(std::memory_order_acquire)) {
        BYTE* data = nullptr;
        if (SUCCEEDED(render_->GetBuffer(buffer_frames_, &data)) && data) {
          std::memset(data, 0, static_cast<size_t>(buffer_frames_) * channels_ * sizeof(float));
          render_->ReleaseBuffer(buffer_frames_, 0);
        }
        continue;
      }

      UINT32 pad = 0;
      client_->GetCurrentPadding(&pad);
      const UINT32 avail = buffer_frames_ - pad;
      if (avail == 0) continue;

      BYTE* data = nullptr;
      if (SUCCEEDED(render_->GetBuffer(avail, &data)) && data) {
        // Çok kanallı cihazda kalan kanallar sessiz kalsın (GetBuffer içeriği tanımsız olabilir).
        std::memset(data, 0, static_cast<size_t>(avail) * channels_ * sizeof(float));
        cb_(user_data_, reinterpret_cast<float*>(data), avail, channels_);  // RT callback (ADR-017)
        render_->ReleaseBuffer(avail, 0);
      }
    }

    if (task) AvRevertMmThreadCharacteristics(task);
  }

  void close_stream() {
    if (client_) { client_->Stop(); client_->Reset(); }
    render_.Reset();
    client_.Reset();
    if (event_) { CloseHandle(event_); event_ = nullptr; }
    buffer_frames_ = 0;
  }

  ComPtr<IMMDeviceEnumerator> enumerator_;
  ComPtr<IMMDevice> device_;
  ComPtr<IAudioClient> client_;
  ComPtr<IAudioRenderClient> render_;
  ComPtr<DeviceNotifier> notifier_;
  HANDLE event_ = nullptr;
  std::thread thread_;
  std::atomic<bool> running_{false};
  RenderCallback cb_ = nullptr;
  void* user_data_ = nullptr;
  UINT32 buffer_frames_ = 0;
  std::uint16_t channels_ = 2;
  StreamMode mode_ = StreamMode::Exclusive;
  StreamConfig cfg_{};
};

} // namespace

std::unique_ptr<IAudioBackend> createWasapiBackend() {
  auto b = std::unique_ptr<WasapiBackend>(new WasapiBackend());
  return b;
}

} // namespace neva

#else  // !_WIN32

namespace neva {
std::unique_ptr<IAudioBackend> createWasapiBackend() { return nullptr; }  // ADR-019: non-Windows adapter sonraki faz
}

#endif
