// neva_session.cpp — C ABI implementasyonu (ADR-0105 §2.6).
// Opak handle, hata kodu dönüşü, api_version handshake. Session orkestrasyonu — minimal.
// RT yolu: render_cb içinde new/mutex/log YOK (ADR-017). Tüm tahsisat open/play (kontrol tarafı) önceden yapılır.

#include "neva/neva_session.h"

#include "neva/iaudio_backend.h"
#include "neva/eq_chain.h"
#include "neva/spsc_ring.h"
#include "decode_stub.h"

#include <algorithm>
#include <cstring>
#include <filesystem>
#include <fstream>
#include <memory>
#include <new>
#include <vector>

namespace {
constexpr std::uint32_t kApiVersion = NEVA_API_VERSION;
constexpr std::size_t kRingCapacity = 8192;             // power-of-2 float (≈4096 stereo frame)
constexpr std::size_t kMaxBlock = 4096;                  // RT scratch üst sınırı (frame)
constexpr double kEqRampMs = 10.0;                       // §2.3: 5–20 ms ramp
} // namespace

// Opak handle'in C++ tanımı — header'da yalnız forward-declared (§2.6 C++ tipi sınırı geçemez).
struct neva_session {
  std::unique_ptr<neva::IAudioBackend> backend;
  neva::EqChain eq;
  neva::SpscRing<float, kRingCapacity> ring;             // decode → RT (ADR-0105 §2.5)
  std::vector<float> decoded;                             // kontrol tarafı, open_file'da tahsis
  std::uint32_t sample_rate = 48000;
  std::uint16_t channels = 2;
  bool opened = false;
  bool playing = false;

  // RT scratch (baştan allocate — callback'te tahsisat yok, §2.3).
  alignas(64) float scratch_l[kMaxBlock] = {};
  alignas(64) float scratch_r[kMaxBlock] = {};

  neva::decode::WavInfo wav{};
};

namespace {

// RT render callback (noexcept). Ring'den interleaved pop → EQ → interleaved out. Underrun → sessizlik.
// §2.5: decode/resample callback'te YASAK; burada yalnız ring pop + biquad zinciri var.
void render_cb(void* ud, float* out, std::uint32_t frames, std::uint16_t ch) noexcept {
  auto* s = static_cast<neva_session*>(ud);
  if (!out || frames == 0) return;

  const std::uint32_t n = (frames < kMaxBlock) ? frames : static_cast<std::uint32_t>(kMaxBlock);

  // Ring'den interleaved pop → planar scratch. Boşsa sessizlik (kill-switch uyumlu).
  for (std::uint32_t i = 0; i < n; ++i) {
    float l = 0.0f, r = 0.0f;
    if (s->ring.pop(l)) {
      if (ch >= 2) s->ring.pop(r); else r = l;
    }
    s->scratch_l[i] = l;
    s->scratch_r[i] = (ch >= 2) ? r : l;
  }

  s->eq.process(s->scratch_l, s->scratch_r, n);           // 62 biquad zinciri (§2.2)

  // Planar → interleaved out. frames > n kalırsa kalanı sessizlik.
  for (std::uint32_t i = 0; i < n; ++i) {
    out[i * ch] = s->scratch_l[i];
    if (ch >= 2) out[i * ch + 1] = s->scratch_r[i];
  }
  for (std::uint32_t i = n; i < frames; ++i) {
    out[i * ch] = 0.0f;
    if (ch >= 2) out[i * ch + 1] = 0.0f;
  }
}

// ADR-019 fallback: WASAPI → Null (kill-switch). createWasapiBackend() nullptr ise null.
std::unique_ptr<neva::IAudioBackend> make_backend() {
  auto b = neva::createWasapiBackend();
  if (b) return b;
  return neva::createNullBackend();
}

neva_result_t to_result(neva::BackendResult r) {
  using BR = neva::BackendResult;
  switch (r) {
    case BR::Ok: return NEVA_OK;
    case BR::RateUnsupported: return NEVA_ERR_UNSUPPORTED_FORMAT;
    case BR::DeviceLost:
    case BR::Busy: return NEVA_ERR_NO_DEVICE;
    case BR::Permission:
    case BR::Fatal: return NEVA_ERR_INTERNAL;
  }
  return NEVA_ERR_INTERNAL;
}

} // namespace

extern "C" {

std::uint32_t neva_session_api_version(void) { return kApiVersion; }

neva_result_t neva_session_create(neva_session_t** out) {
  if (!out) return NEVA_ERR_INVALID_ARG;
  *out = nullptr;
  try {
    auto* s = new neva_session();
    s->eq.prepare(48000.0);
    *out = s;
    return NEVA_OK;
  } catch (const std::bad_alloc&) {
    return NEVA_ERR_INTERNAL;
  }
}

void neva_session_destroy(neva_session_t* s) { delete s; }

neva_result_t neva_session_open_file(neva_session_t* s, const char16_t* path) {
  if (!s || !path) return NEVA_ERR_INVALID_ARG;
  try {
    const std::filesystem::path p(reinterpret_cast<const char16_t*>(path));
    std::ifstream f(p, std::ios::binary);
    if (!f) return NEVA_ERR_INVALID_ARG;

    std::vector<std::uint8_t> bytes((std::istreambuf_iterator<char>(f)), std::istreambuf_iterator<char>());
    if (bytes.empty()) return NEVA_ERR_UNSUPPORTED_FORMAT;

    const std::uint8_t* pcm = nullptr;
    std::size_t pcm_bytes = 0;
    if (!neva::decode::parse_wav(bytes.data(), bytes.size(), s->wav, pcm, pcm_bytes))
      return NEVA_ERR_UNSUPPORTED_FORMAT;   // FLAC/DSD/gateway sonraki faz (§2.4)

    const std::uint32_t frames = s->wav.frames;
    const std::uint16_t ch = s->wav.channels;
    s->decoded.assign(static_cast<std::size_t>(frames) * ch, 0.0f);
    const std::uint32_t got = neva::decode::normalize_to_float(pcm, pcm_bytes, s->wav,
                                                               s->decoded.data(), frames);
    if (got == 0) return NEVA_ERR_UNSUPPORTED_FORMAT;

    s->sample_rate = s->wav.sample_rate;
    s->channels = ch;
    s->eq.prepare(static_cast<double>(s->sample_rate));
    s->opened = true;
    return NEVA_OK;
  } catch (const std::bad_alloc&) {
    return NEVA_ERR_INTERNAL;
  } catch (...) {
    return NEVA_ERR_INTERNAL;
  }
}

neva_result_t neva_session_set_output_device(neva_session_t* s, std::uint32_t index) {
  if (!s) return NEVA_ERR_INVALID_ARG;
  if (!s->backend) s->backend = make_backend();
  neva::DeviceList dl{};
  if (s->backend->enumerate(dl) != neva::BackendResult::Ok) return NEVA_ERR_NO_DEVICE;
  if (index >= dl.count && dl.count > 0) return NEVA_ERR_INVALID_ARG;
  return NEVA_OK;   // TODO: seçili cihazı open'a geçir (sonraki faz)
}

neva_result_t neva_session_play(neva_session_t* s) {
  if (!s || !s->opened) return NEVA_ERR_INVALID_ARG;
  try {
    if (!s->backend) s->backend = make_backend();

    neva::StreamConfig cfg;
    cfg.sampleRate = s->sample_rate;
    cfg.channels = s->channels;
    cfg.mode = neva::StreamMode::Exclusive;   // ADR-0105: exclusive bit-perfect birincil

    if (s->backend->open(cfg) != neva::BackendResult::Ok) {
      s->backend = neva::createNullBackend();   // fallback (ADR-019)
      s->backend->open(cfg);
    }

    // Ring'i decode çıktısıyla doldur (kontrol tarafı). TODO: canlı decode thread (sonraki faz — §2.5).
    // Not: SpscRing std::atomic içerir → taşınamaz/kopyalanamaz; yeniden oynatmada reset metodu eklenmeli (TODO).
    const std::size_t total = s->decoded.size();
    const std::size_t cap = kRingCapacity - 1;
    const std::size_t n = (total < cap) ? total : cap;
    for (std::size_t i = 0; i < n; ++i) s->ring.push(s->decoded[i]);

    if (s->backend->start(render_cb, s) != neva::BackendResult::Ok) return NEVA_ERR_NO_DEVICE;
    s->playing = true;
    return NEVA_OK;
  } catch (...) {
    return NEVA_ERR_INTERNAL;
  }
}

neva_result_t neva_session_pause(neva_session_t* s) {
  if (!s) return NEVA_ERR_INVALID_ARG;
  if (s->backend) s->backend->stop();
  s->playing = false;
  return NEVA_OK;
}

neva_result_t neva_session_stop(neva_session_t* s) {
  if (!s) return NEVA_ERR_INVALID_ARG;
  if (s->backend) s->backend->stop();
  s->playing = false;
  return NEVA_OK;
}

neva_result_t neva_session_set_eq_gain(neva_session_t* s, std::uint32_t band, double db) {
  if (!s) return NEVA_ERR_INVALID_ARG;
  if (band >= 31) return NEVA_ERR_INVALID_ARG;   // 31-band (§2.2)
  // C ABI her iki kanala uygular (62 biquad yolu). Katsayı RT-dışı hesap + atomik publish (§2.3).
  s->eq.set_gain(band, 0, db, kEqRampMs);
  s->eq.set_gain(band, 1, db, kEqRampMs);
  return NEVA_OK;
}

} // extern "C"
