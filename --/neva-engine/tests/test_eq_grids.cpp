// test_eq_grids.cpp — ADR-106 §2.2b test kapilari (grid alt küme, bypass maskesi, bant degisimi).
// Kapi 1: 10-band maskesi dogru bit deseni. Kapi 2: unverified alt küme guvenli default (0 = hepsi bypass? HAYIR:
// mask 0 = tum bantlar pasif — EqChain'de anlami "EQ kapali", sinyal unity'den gecer; bu guvenli default).
// Kapi 3: set_active_bands → ramp → bypass (pasif bant isleme atlanir, sinyal korunur).
// Kapi 4: pasif→aktif gecis geri yukleme (gains_db_) — aktif bant tekrar kazanc uygular.

#include "neva/eq_chain.h"
#include "neva/eq_grids.h"

#include <cassert>
#include <cmath>
#include <cstdio>
#include <cstring>

using namespace neva;

// Kapi 1: maskeler (static_assert zaten derlemede; runtime popcount da dogrula).
static void test_masks() {
  const std::uint32_t m10 = mask_for(kSubset10);
  int bits = 0;
  for (std::uint32_t m = m10; m; m &= m - 1) ++bits;
  assert(bits == 10);
  assert(mask_for(kSubset31) == kEqMaskAll);
  assert(kSubset10.verified && kSubset31.verified);
  assert(!kSubset15.verified && !kSubset20.verified);
  assert(mask_for(kSubset15) == 0 && mask_for(kSubset20) == 0);
  std::printf("[ok] grid_masks (10-band popcount=%d, 31=0x%08X, 15/20=unverified->0)\n", bits, kEqMaskAll);
}

// Kapi 3: bypass — set_active_bands(0) → hepsi pasif → unity sinyal gecmeli (ramp sonrasi).
static void test_bypass_all_unity() {
  EqChain eq;
  eq.prepare(48000.0);
  eq.set_gain(5, 0, 12.0, 5.0);   // band 5 (63 Hz) +12 dB — pasif yapilacak
  eq.set_gain(5, 1, 12.0, 5.0);
  eq.set_active_bands(0u, 5.0);   // tum bantlar pasif (EQ kapali)
  assert(eq.active_mask() == 0u);

  constexpr int N = 256;
  constexpr int kBlocks = 8;      // 5 ms ramp @256 blok = ~1 blok; 8 blok fazlasıyla yeterli
  float l[N], r[N];
  for (int i = 0; i < N; ++i) l[i] = r[i] = static_cast<float>(std::sin(0.02 * i));
  for (int b = 0; b < kBlocks; ++b) {
    float copy[N];
    std::memcpy(copy, l, sizeof(l));
    eq.process(l, r, N);
    if (b == kBlocks - 1) {
      // Son blok: unity — sinyal birebir korunmali (ramp bitti, bypass = isleme atlanti, state sifir).
      for (int i = 0; i < N; ++i) assert(std::fabs(l[i] - copy[i]) < 1e-6);
    }
  }
  std::printf("[ok] bypass_all_unity (mask=0, 8 blok, son blok birebir)\n");
}

// Kapi 3b: 10-band maskesi — 10 bant aktif, 21 pasif; sinyal hala gecer (aktif bantlar 0 dB).
static void test_10band_mask_process() {
  EqChain eq;
  eq.prepare(48000.0);
  eq.set_active_bands(mask_for(kSubset10), 5.0);
  assert(eq.active_mask() == mask_for(kSubset10));

  constexpr int N = 256;
  float l[N], r[N];
  for (int i = 0; i < N; ++i) l[i] = r[i] = static_cast<float>(std::sin(0.03 * i)) * 0.5f;
  for (int b = 0; b < 8; ++b) {
    float copy[N];
    std::memcpy(copy, l, sizeof(l));
    eq.process(l, r, N);
    if (b == 7) {
      // 10 aktif bant 0 dB (kazanc verilmedi) → unity → sinyal korunur (ramp bitti).
      for (int i = 0; i < N; ++i) assert(std::fabs(l[i] - copy[i]) < 1e-6);
    }
  }
  std::printf("[ok] 10band_mask_process (mask=0x%08X, 10 aktif, unity sinyal)\n", mask_for(kSubset10));
}

// Kapi 4: pasif→aktif geri yukleme — band 5'e +12 dB ver, pasif yap, aktif yap → kazanc uygulanir mi?
// (Ramp hedefi aktif→gains_db_[5]=+12 dB olmali — kural 5.)
static void test_reactivate_restores_gain() {
  EqChain eq;
  eq.prepare(48000.0);
  eq.set_gain(17, 0, 9.0, 5.0);   // band 17 (1000 Hz) +9 dB
  eq.set_gain(17, 1, 9.0, 5.0);
  eq.set_active_bands(0u, 5.0);        // pasif
  eq.set_active_bands(kEqMaskAll, 5.0); // geri aktif (31-band)
  assert(eq.active_mask() == kEqMaskAll);

  // 1 kHz sin gir; 8 blok sonra genlik buyumeli (+9 dB ≈ x2.82).
  constexpr int N = 256;
  constexpr double w = 2.0 * 3.14159265358979323846 * 1000.0 / 48000.0;
  float l[N], r[N];
  double in_peak = 0.0, out_peak = 0.0;
  for (int b = 0; b < 8; ++b) {
    for (int i = 0; i < N; ++i) l[i] = r[i] = static_cast<float>(std::sin(w * (b * N + i)));
    for (int i = 0; i < N; ++i) in_peak = std::fmax(in_peak, std::fabs(static_cast<double>(l[i])));
    eq.process(l, r, N);
    if (b >= 4)  // ramp sonrasi
      for (int i = 0; i < N; ++i) out_peak = std::fmax(out_peak, std::fabs(static_cast<double>(l[i])));
  }
  const double ratio = out_peak / in_peak;
  assert(ratio > 2.4 && ratio < 3.3);   // ~2.82 hedef (toleransli)
  std::printf("[ok] reactivate_restores_gain (+9 dB → ratio=%.3f, hedef~2.82)\n", ratio);
}

int main() {
  test_masks();
  test_bypass_all_unity();
  test_10band_mask_process();
  test_reactivate_restores_gain();
  std::printf("ALL EQ_GRID TESTS PASSED\n");
  return 0;
}
