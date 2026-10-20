/* eq_grids.h — ADR-106 §2.2b: sabit 31-band ISO grid + secilebilir bant alt kumeleri.
 *
 * Kural 1 (grid sabit):  grid = eq_chain.h kEqFrequencies (31 ISO 1/3-oktav, 20..20000 Hz).
 * Kural 2 (izinli sayi): 10 · 15 · 20 · 31 — baskasi UI'da secilemez.
 * Kural 3 (alt küme):    10-band = ISO alt kümesi (31.5, 63, 125, 250, 500, 1k, 2k, 4k, 8k, 16k).
 *                        Karar kaniti: ADR-106 v1.1.1 + VLC dump (vlc-dump.md §5 — VLC 62.5 kullanir,
 *                        Neva ISO 63'te kalir; VLC preset'leri kendi frekansiyla okunur).
 *                        15/20-band frekans listeleri dump/web kaynagiyla Teyit edilmedi =>
 *                        ⚠️ VERIFICATION REQUIRED — zero-hallucination geregi listeler YAZILMAZ,
 *                        alt küme "unverified" doner ve tum bantlar bypass edilir (guvenli default).
 * Kural 4-5 (bypass):    Zincir boyutu degismez (62 biquad sabit allocate); bant sayisi degisimi
 *                        RT-disi maske yayini + ramp (EqChain::set_active_bands).
 *
 * Kaynak: .ai/.decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md (accepted v1.1.1) */
#ifndef NEVA_EQ_GRIDS_H
#define NEVA_EQ_GRIDS_H

#include "eq_chain.h"   /* kEqBands, kEqChannels, kEqFrequencies */

#include <array>
#include <cstdint>

namespace neva {

enum class EqBandCount : std::uint32_t { Band10 = 10, Band15 = 15, Band20 = 20, Band31 = 31 };

/* kEqMaskAll (31-bit tum bantlar aktif) eq_chain.h'de tanimli — burada yeniden tanimlanmaz. */

/* Alt küme tanimi: verified=false → frekans listesi kaynakla teyit edilmedi (mask_for 0 dondurur). */
struct EqGridSubset {
  bool verified;
  std::uint32_t count;                              /* aktif bant sayisi (unverified → 0) */
  std::array<std::uint32_t, kEqBands> grid_index;   /* kEqFrequencies indeksleri, sirali */
};

/* 10-band ISO alt küme (ADR-106 §2.2b kural 3 — teyitli):
 * 31.5(2), 63(5), 125(8), 250(11), 500(14), 1000(17), 2000(20), 4000(23), 8000(26), 16000(29).
 * static_assert ile grid'e kilitlenir — kEqFrequencies degistirse derleme patlar (zero-hallucination).
 * (grid_index kalan elemanlari deger verilmezse value-init ile 0 dolar — maske hesabi icin onemsizdir.) */
inline constexpr EqGridSubset kSubset10{true, 10,
    {2, 5, 8, 11, 14, 17, 20, 23, 26, 29}};

/* 31-band = tam grid (kural 2). */
inline constexpr EqGridSubset kSubset31{true, 31,
    {0,  1,  2,  3,  4,  5,  6,  7,  8,  9,
     10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
     20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30}};

/* 15-band (2/3-oktav) ve 20-band (AIMP emsali) — ⚠️ VERIFICATION REQUIRED (ADR-106 §2.2b kural 3).
 * Frekans listeleri web/AIMP dump kaynagiyla teyit edilmeden uretilmez; verified=false → tumu bypass. */
inline constexpr EqGridSubset kSubset15{false, 0, {}};
inline constexpr EqGridSubset kSubset20{false, 0, {}};

inline constexpr const EqGridSubset& subset_for(EqBandCount bc) noexcept {
  switch (bc) {
    case EqBandCount::Band10: return kSubset10;
    case EqBandCount::Band15: return kSubset15;
    case EqBandCount::Band20: return kSubset20;
    case EqBandCount::Band31: return kSubset31;
  }
  return kSubset31;
}

/* 31-bit bypass maskesi: bit i set → band i aktif (EqChain::set_active_bands girdisi).
 * Unverified alt küme → 0 (tum bantlar bypass — guvenli default). */
inline constexpr std::uint32_t mask_for(const EqGridSubset& s) noexcept {
  if (!s.verified) return 0u;
  std::uint32_t m = 0u;
  for (std::uint32_t i = 0; i < s.count; ++i) m |= (1u << s.grid_index[i]);
  return m & kEqMaskAll;
}

/* Preset (kural 6): {grid_version, band_count, per-band gain dB (grid indeksli), preamp}.
 * band_count preset'in parcasidir; yuklenirken degistiyse EqChain::set_active_bands isletilir. */
struct EqPreset {
  std::uint32_t grid_version = 1;                     /* grid degisirse preset yeniden map edilir */
  EqBandCount band_count = EqBandCount::Band31;
  std::array<float, kEqBands> gain_db{};              /* grid indeksli; eksik bantlar 0.0 (flat) */
  float preamp_db = 0.0f;
};

/* Grid- kilitleme static_assert'lari (kEqFrequencies teyitli 10-band listesiyle birebir olmali). */
static_assert(kEqFrequencies[2] == 31.5 && kEqFrequencies[5] == 63.0 && kEqFrequencies[8] == 125.0 &&
              kEqFrequencies[11] == 250.0 && kEqFrequencies[14] == 500.0 && kEqFrequencies[17] == 1000.0 &&
              kEqFrequencies[20] == 2000.0 && kEqFrequencies[23] == 4000.0 && kEqFrequencies[26] == 8000.0 &&
              kEqFrequencies[29] == 16000.0,
              "10-band alt küme indeksleri kEqFrequencies ile uyumsuz — grid degismis (ADR-106 §2.2b kural 3)");
static_assert(mask_for(kSubset10) ==
                  ((1u << 2) | (1u << 5) | (1u << 8) | (1u << 11) | (1u << 14) | (1u << 17) | (1u << 20) |
                   (1u << 23) | (1u << 26) | (1u << 29)),
              "10-band maskesi beklenen bit deseniyle uyumsuz");
static_assert(mask_for(kSubset31) == kEqMaskAll, "31-band maskesi tum bitleri icermeli");
static_assert(mask_for(kSubset15) == 0 && mask_for(kSubset20) == 0,
              "unverified alt kümeler guvenli default olarak tumu bypass olmali (zero-hallucination)");

} // namespace neva
#endif /* NEVA_EQ_GRIDS_H */
