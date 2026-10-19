// decode_stub.cpp — minimal WAV çözümleyici + normalize (ADR-0105 §2.4: kapsayıcısız WAV çekirdekten).
// KONTROL TARAFI (decode thread / open_file) — tahsisat burada serbest (ADR-017 RT yasağı yalnız callback).
// TODO (sonraki faz): FLAC (libFLAC/dr_flac), DSD (libdsd, DoP-first §2.5), ffmpeg gateway (§2.4 kapsayıcı).

#include "decode_stub.h"

#include <cstring>

namespace neva {
namespace decode {
namespace {

inline std::uint16_t rd16(const std::uint8_t* p) noexcept {
  return static_cast<std::uint16_t>(p[0] | (p[1] << 8));
}
inline std::uint32_t rd32(const std::uint8_t* p) noexcept {
  return static_cast<std::uint32_t>(p[0]) | (static_cast<std::uint32_t>(p[1]) << 8) |
         (static_cast<std::uint32_t>(p[2]) << 16) | (static_cast<std::uint32_t>(p[3]) << 24);
}

} // namespace

bool parse_wav(const std::uint8_t* data, std::size_t size, WavInfo& info,
               const std::uint8_t*& pcm, std::size_t& pcm_bytes) {
  if (!data || size < 44) return false;
  if (std::memcmp(data, "RIFF", 4) != 0) return false;
  if (std::memcmp(data + 8, "WAVE", 4) != 0) return false;

  bool have_fmt = false;
  const std::uint8_t* data_ptr = nullptr;
  std::size_t data_sz = 0;

  std::size_t off = 12;
  while (off + 8 <= size) {
    const std::uint8_t* ck = data + off;
    const std::uint32_t ck_size = rd32(ck + 4);
    const std::uint8_t* body = ck + 8;
    if (off + 8 + ck_size > size) return false;

    if (std::memcmp(ck, "fmt ", 4) == 0 && ck_size >= 16) {
      const std::uint16_t format = rd16(body + 0); // 1=PCM, 3=IEEE float
      info.channels = rd16(body + 2);
      info.sample_rate = rd32(body + 4);
      info.bits_per_sample = rd16(body + 14);
      if (format == 3) {
        info.is_float = true;
      } else if (format == 1) {
        info.is_float = false;
      } else {
        return false; // desteklenmeyen format (MP3/FLAC kapsayıcı değil — §2.4)
      }
      have_fmt = true;
    } else if (std::memcmp(ck, "data", 4) == 0) {
      data_ptr = body;
      data_sz = ck_size;
    }
    off += 8 + ck_size + (ck_size & 1u); // chunk word-aligned
  }

  if (!have_fmt || !data_ptr || info.channels == 0) return false;
  const std::uint32_t bytes_per_sample = info.bits_per_sample / 8u;
  if (bytes_per_sample == 0) return false;
  const std::uint32_t block_align = info.channels * bytes_per_sample;
  info.frames = static_cast<std::uint32_t>(data_sz / block_align);

  pcm = data_ptr;
  pcm_bytes = data_sz;
  return true;
}

std::uint32_t normalize_to_float(const std::uint8_t* pcm, std::size_t bytes,
                                 const WavInfo& info, float* out, std::uint32_t max_frames) {
  if (!pcm || !out || info.channels == 0) return 0;
  const std::uint32_t ch = info.channels;
  const std::uint32_t bytes_per_sample = info.bits_per_sample / 8u;
  const std::uint32_t block_align = ch * bytes_per_sample;
  if (block_align == 0) return 0;

  std::uint32_t frames = static_cast<std::uint32_t>(bytes / block_align);
  if (frames > max_frames) frames = max_frames;

  if (info.is_float && info.bits_per_sample == 32) {
    std::memcpy(out, pcm, static_cast<std::size_t>(frames) * block_align);
    return frames;
  }

  if (!info.is_float && info.bits_per_sample == 16) {
    const std::int16_t* src = reinterpret_cast<const std::int16_t*>(pcm);
    const std::size_t n = static_cast<std::size_t>(frames) * ch;
    for (std::size_t i = 0; i < n; ++i) out[i] = static_cast<float>(src[i]) * (1.0f / 32768.0f);
    return frames;
  }

  // TODO: int24/int32 → float (sonraki faz). Şimdilik desteklenmiyor.
  return 0;
}

} // namespace decode
} // namespace neva
