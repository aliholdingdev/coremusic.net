// test_biquad.cpp — ADR-0105 §5 birim test kapıları (biquad katsayı, DC gain, blok eşitliği, ring wrap).
// Basit assert; ctest ile çalışır. RT bağımlılığı yok (offline, donanımsız — §5.2a DSP-alanı).

#include "neva/biquad.h"
#include "neva/eq_chain.h"
#include "neva/spsc_ring.h"

#include <cassert>
#include <cmath>
#include <complex>
#include <cstdio>
#include <cstring>

using namespace neva;

// §5.2a: RBJ peaking 1 kHz katsayı doğruluğu (f0=1k, fs=48k, Q=0.707, +6 dB).
static void test_rbj_peaking_coeff() {
  PeakingParams p;
  p.fs = 48000.0;
  p.f0 = 1000.0;
  p.q = 0.7071;
  p.gain_db = 6.0;
  const BiquadCoeffs c = compute_peaking(p);

  const double w0 = 2.0 * 3.14159265358979323846 * 1000.0 / 48000.0;
  const double expect_b1 = -2.0 * std::cos(w0);   // a0'a normalize öncesi
  // b1 ve a1 eşittir (RBJ peaking: b1 = a1 = -2cos(w0)/a0) → fark ~0.
  assert(std::fabs(c.b1 - c.a1) < 1e-12);
  // +6 dB → |H(f0)| ≈ 10^(6/20) ≈ 1.9953.
  // H(z) = (b0 + b1 z^-1 + b2 z^-2)/(1 + a1 z^-1 + a2 z^-2); z = e^{jw0}.
  const double wr = w0;
  const std::complex<double> zinv = std::exp(std::complex<double>(0.0, -wr));
  const std::complex<double> num = c.b0 + c.b1 * zinv + c.b2 * zinv * zinv;
  const std::complex<double> den = 1.0 + c.a1 * zinv + c.a2 * zinv * zinv;
  const double mag = std::abs(num / den);
  assert(std::fabs(mag - 1.99526231497) < 1e-6);
  (void)expect_b1;
  std::printf("[ok] rbj_peaking_coeff (mag=%.6f expect=1.995262, |b1-a1|=%.3e)\n", mag,
              std::fabs(c.b1 - c.a1));
}

// DC gain (z=1): peaking EQ DC'de unity (0 dB) → |H(1)| = 1.
static void test_dc_gain() {
  PeakingParams p;
  p.f0 = 1000.0;
  p.gain_db = 6.0;
  const BiquadCoeffs c = compute_peaking(p);
  const double num = c.b0 + c.b1 + c.b2;
  const double den = 1.0 + c.a1 + c.a2;
  assert(std::fabs(num / den - 1.0) < 1e-9);
  std::printf("[ok] dc_gain (H(1)=%.9f)\n", num / den);
}

// Blok işleme byte-eşitliği: scalar process() zinciri == process_block().
static void test_block_byte_equality() {
  PeakingParams p;
  p.f0 = 1000.0;
  p.gain_db = 3.0;
  const BiquadCoeffs c = compute_peaking(p);

  constexpr int N = 256;
  float in[N];
  for (int i = 0; i < N; ++i) in[i] = static_cast<float>(std::sin(0.07 * i)) * 0.5f;

  Biquad a, b;
  a.set_coefficients(c);
  b.set_coefficients(c);
  a.reset();
  b.reset();

  float scalar[N], block[N];
  for (int i = 0; i < N; ++i) scalar[i] = a.process(in[i]);
  b.process_block(in, block, N);

  assert(std::memcmp(scalar, block, sizeof(block)) == 0);
  std::printf("[ok] block_byte_equality (%d ornek)\n", N);
}

// Ring wrap-around: power-of-2 kapasite, dolu/bos kenar durumları, sıralı veri.
static void test_ring_wrap() {
  SpscRing<int, 8> ring;
  int v = 0;
  // 8 kapasite → 7 kullanılabilir (dolu kenarı 1 slot boş).
  for (int i = 0; i < 7; ++i) assert(ring.push(i));
  assert(!ring.push(99));                 // dolu → false
  for (int i = 0; i < 7; ++i) {
    assert(ring.pop(v));
    assert(v == i);                        // FIFO sırası
  }
  assert(!ring.pop(v));                    // boş → false

  // Wrap-around: indeks 0'a sarılmış hâlde tekrar push/pop.
  for (int i = 100; i < 107; ++i) assert(ring.push(i));
  for (int i = 100; i < 107; ++i) {
    assert(ring.pop(v));
    assert(v == i);
  }
  std::printf("[ok] ring_wrap\n");
}

// EqChain: set_gain publish → process ramp → unity bypass (0 dB'de geçiş).
static void test_eq_chain_publish_ramp() {
  EqChain eq;
  eq.prepare(48000.0);
  constexpr int N = 128;
  float l[N], r[N];
  for (int i = 0; i < N; ++i) {
    l[i] = static_cast<float>(std::sin(0.05 * i));
    r[i] = l[i];
  }
  // 0 dB publish → unity bypass: çıktı girişe yakın (ramp henüz tamamlanmadı olabilir; toleranslı).
  eq.set_gain(10, 0, 0.0, 10.0);
  eq.set_gain(10, 1, 0.0, 10.0);
  float lcopy[N];
  std::memcpy(lcopy, l, sizeof(l));
  eq.process(l, r, N);
  // 0 dB → katsayı unity → sinyal korunur (denormal/DIV farkı dışında birebir).
  assert(std::fabs(l[64] - lcopy[64]) < 1e-4);
  std::printf("[ok] eq_chain_publish_ramp\n");
}

int main() {
  test_rbj_peaking_coeff();
  test_dc_gain();
  test_block_byte_equality();
  test_ring_wrap();
  test_eq_chain_publish_ramp();
  std::printf("ALL TESTS PASSED\n");
  return 0;
}
