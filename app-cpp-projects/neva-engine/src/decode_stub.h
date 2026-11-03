/* decode_stub.h — iç (internal) arayüz. Sadece src/ altındaki TUs'lar kullanır; public değil.
 * Minimal WAV çözümleyici + normalize (ADR-0105 §2.4: kapsayıcısız WAV çekirdekten).
 * TODO (sonraki faz): FLAC (libFLAC/dr_flac), DSD (libdsd DoP-first), ffmpeg gateway (§2.4). */
#ifndef NEVA_DECODE_STUB_H
#define NEVA_DECODE_STUB_H

#include <cstddef>
#include <cstdint>

namespace neva::decode {

struct WavInfo {
  std::uint32_t sample_rate = 48000;
  std::uint16_t channels = 2;
  std::uint16_t bits_per_sample = 32;
  bool is_float = true;
  std::uint32_t frames = 0;
};

/* Minimal RIFF/WAVE header parse (little-endian). Destek: PCM int16 ve IEEE float32.
 * pcm/bytes = data chunk'a işaretçi ve boyutu (girdi buffer'ı içinde). */
bool parse_wav(const std::uint8_t* data, std::size_t size, WavInfo& info,
               const std::uint8_t*& pcm, std::size_t& pcm_bytes);

/* Interleaved PCM -> interleaved float32 [-1,1]. Dönen değer: yazılan frame sayısı. */
std::uint32_t normalize_to_float(const std::uint8_t* pcm, std::size_t bytes,
                                 const WavInfo& info, float* out, std::uint32_t max_frames);

} // namespace neva::decode
#endif /* NEVA_DECODE_STUB_H */
