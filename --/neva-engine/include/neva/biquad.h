/* biquad.h — RBJ biquad (ADR-0105 §2.3).
 * Double state + double katsayi, alignas(64), TDF2 (Transposed Direct Form II).
 * FTZ/DAZ yardimi: thread baslangicinda (callback'te DEGIL) cagirilir (ADR-0105 §2.3).
 * Katsayi hesabi RT-disi; process() RT-yolu (tahsisat/lock yok). */
#ifndef NEVA_BIQUAD_H
#define NEVA_BIQUAD_H

#include <cmath>
#include <cstddef>
#include <cstdint>
#if defined(__SSE2__) || defined(_M_X64) || (defined(_M_IX86_FP) && _M_IX86_FP >= 2)
  #include <xmmintrin.h>  /* MXCSR (SSE) */
  #include <pmmintrin.h>  /* _MM_SET_DENORMALS_ZERO_MODE (DAZ, SSE3 header'ı) */
#endif

namespace neva {

/* RBJ cookbook peaking EQ katsayilari (RT-disi, double).
 * fs: sample-rate, f0: center Hz, q: quality, gain_db: dB kazanc. */
struct PeakingParams {
  double fs = 48000.0;
  double f0 = 1000.0;
  double q = 0.7071;   /* ~1/sqrt(2) — butunlesik EQ default */
  double gain_db = 0.0;
};

/* Biquad katsayi seti (TDF2): y[n] = b0*x[n] + s1; s1 = b1*x[n] - a1*y[n] + s2; s2 = b2*x[n] - a2*y[n]. */
struct BiquadCoeffs {
  double b0 = 1.0, b1 = 0.0, b2 = 0.0;
  double a1 = 0.0, a2 = 0.0;   /* a0'a normalize (a0=1) */
};

/* RT-disi katsayi uretimi. RBJ peaking EQ. */
inline BiquadCoeffs compute_peaking(const PeakingParams& p) noexcept {
  const double A = std::pow(10.0, p.gain_db / 40.0);   /* sqrt(10^(dB/20)) */
  const double w0 = 2.0 * 3.14159265358979323846 * p.f0 / p.fs;
  const double cw = std::cos(w0);
  const double alpha = std::sin(w0) / (2.0 * p.q);

  const double a0 = 1.0 + alpha / A;
  BiquadCoeffs c;
  c.b0 = (1.0 + alpha * A) / a0;
  c.b1 = (-2.0 * cw) / a0;
  c.b2 = (1.0 - alpha * A) / a0;
  c.a1 = (-2.0 * cw) / a0;
  c.a2 = (1.0 - alpha / A) / a0;
  return c;
}

/* Bir biquad kanal. alignas(64): state+katsayi kendi cache-line'inda (L1 uyumlu).
 * Yalniz tek kanal; 31-band x 2 kanal = 62 biquad EqChain'de (ADR-0105 §2.2). */
class alignas(64) Biquad {
public:
  Biquad() = default;

  void set_coefficients(const BiquadCoeffs& c) noexcept { c_ = c; }
  const BiquadCoeffs& coefficients() const noexcept { return c_; }

  void reset() noexcept { s1_ = 0.0; s2_ = 0.0; }

  /* Tek ornek (RT). TDF2, double state. */
  float process(float x) noexcept {
    const double y = c_.b0 * static_cast<double>(x) + s1_;
    s1_ = c_.b1 * static_cast<double>(x) - c_.a1 * y + s2_;
    s2_ = c_.b2 * static_cast<double>(x) - c_.a2 * y;
    return static_cast<float>(y);
  }

  /* Blok isleme (RT). Scalar process() ile byte-esit (test_biquad dogrular). */
  void process_block(const float* in, float* out, std::size_t n) noexcept {
    for (std::size_t i = 0; i < n; ++i) {
      const double y = c_.b0 * static_cast<double>(in[i]) + s1_;
      s1_ = c_.b1 * static_cast<double>(in[i]) - c_.a1 * y + s2_;
      s2_ = c_.b2 * static_cast<double>(in[i]) - c_.a2 * y;
      out[i] = static_cast<float>(y);
    }
  }

private:
  BiquadCoeffs c_{};        /* a0'a normalize katsayi */
  double s1_ = 0.0, s2_ = 0.0;  /* TDF2 state */
};

/* FTZ/DAZ (denormal flush) — SSE2 MXCSR. CALLBACK'TE DEGIL, thread baslangicinda (ADR-0105 §2.3). */
inline void enable_ftz_daz() noexcept {
#if defined(__SSE2__) || defined(_M_X64) || (defined(_M_IX86_FP) && _M_IX86_FP >= 2)
  _MM_SET_FLUSH_ZERO_MODE(_MM_FLUSH_ZERO_ON);
  _MM_SET_DENORMALS_ZERO_MODE(_MM_DENORMALS_ZERO_ON);
#endif
}

} // namespace neva
#endif /* NEVA_BIQUAD_H */
