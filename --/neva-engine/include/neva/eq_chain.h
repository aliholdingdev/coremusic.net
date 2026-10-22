/* eq_chain.h — 31-band (62 biquad) zincir (ADR-0105 §2.2, §2.3).
 * process(float* L, float* R, size_t n): RT-yolu, tahsisat/lock yok.
 * set_gain(band, ch, db, ramp_ms): RT-DISI katsayi uretimi + atomik publish + per-block ramp.
 * Cascade (124 biquad) VARSAYILAN DEGIL (§2.2): bant basina ikinci biquad gerekcesi yazilmadan kullanilmaz.
 *
 * ADR-106 §2.2b kural 4-5 (secilebilir bant): biquad'lar 31-band icin SABIT ALLOCATE;
 * bant sayisi degisimi = per-band BYPASS MASK (yeni zincir tahsisi/takasi YOK).
 * set_active_bands(mask, ramp_ms): RT-disi maske yayini (release) + pending bump ->
 * callback maskeyi ramp ile uygular; pasif bant unity'ye rampalanir ve ramp bitince ISLENMEZ
 * (bypass). Maske publish'i pending_ bump'indan ÖNCE yapilir (release-acquire yayilimi). */
#ifndef NEVA_EQ_CHAIN_H
#define NEVA_EQ_CHAIN_H

#include "biquad.h"
#include <array>
#include <atomic>
#include <cstddef>
#include <cstdint>

namespace neva {

inline constexpr std::uint32_t kEqBands = 31;
inline constexpr std::uint32_t kEqChannels = 2;
inline constexpr std::uint32_t kEqBiquads = kEqBands * kEqChannels;  /* 62 temel (§2.2) */
inline constexpr std::uint32_t kEqMaskAll = (1u << kEqBands) - 1u;   /* tum bantlar aktif */

/* 31-band 1/3-oktav ISO merkez frekanslari (Hz). */
inline constexpr std::array<double, kEqBands> kEqFrequencies = {
  20.0, 25.0, 31.5, 40.0, 50.0, 63.0, 80.0, 100.0, 125.0, 160.0,
  200.0, 250.0, 315.0, 400.0, 500.0, 630.0, 800.0, 1000.0, 1250.0, 1600.0,
  2000.0, 2500.0, 3150.0, 4000.0, 5000.0, 6300.0, 8000.0, 10000.0, 12500.0, 16000.0,
  20000.0
};

/* 62 biquad = 31 bant x 2 kanal (§2.2). Double katsayi + double state (§2.3). */
class EqChain {
public:
  /* prepare: RT-disi. Bantlari 0 dB'ye (bypass katsayi) baslatir, tum bantlar aktif. */
  void prepare(double sample_rate) noexcept {
    fs_ = sample_rate;
    for (auto& b : biquads_) b.set_coefficients(kunity);
    for (auto& p : ramp_pos_) p = 1.0;   // ramp pasif — katsayilar unity kalir (§2.3)
    for (auto& g : gains_db_) g = 0.0;
    active_mask_.store(kEqMaskAll, std::memory_order_release);
  }

  /* RT-disi katsayi uretimi + atomik publish. ramp_ms: 5-20 ms (§2.3 click yasagi).
   * Kazanc gains_db_'ye kaydedilir (ADR-106 kural 5: pasif→aktif geciste geri yuklenir). */
  void set_gain(std::uint32_t band, std::uint32_t ch, double db, double ramp_ms) noexcept {
    if (band >= kEqBands || ch >= kEqChannels) return;
    const std::uint32_t idx = band * kEqChannels + ch;
    gains_db_[band] = db;
    PeakingParams p;
    p.fs = fs_;
    p.f0 = kEqFrequencies[band];
    p.gain_db = db;
    const BiquadCoeffs target = compute_peaking(p);

    /* ramp_ms -> per-block adim (block = 256 ornek varsayimi; TODO ADR-0105 §5.6 benchmark). */
    const double blocks = (ramp_ms > 0.0 ? ramp_ms : 10.0) / 1000.0 * fs_ / 256.0;
    targets_[idx] = target;
    steps_[idx] = (blocks > 1.0) ? (1.0 / blocks) : 1.0;
    pending_.fetch_add(1, std::memory_order_release);   /* publish (RT acquire ile okur) */
  }

  /* ADR-106 §2.2b kural 5: bant sayisi degisimi — RT-disi maske yayini + ramp (tahsis YOK).
   * Inaktif bantlar unity'ye rampalanir; ramp bitince o bant ISLENMEZ (bypass, kural 4).
   * Aktif bantlar gains_db_'deki son kazanca geri rampalanir. Zincir takasi/degerismez. */
  void set_active_bands(std::uint32_t mask, double ramp_ms) noexcept {
    mask &= kEqMaskAll;
    for (std::uint32_t band = 0; band < kEqBands; ++band) {
      const double db = ((mask >> band) & 1u) ? gains_db_[band] : 0.0;
      PeakingParams p;
      p.fs = fs_;
      p.f0 = kEqFrequencies[band];
      p.gain_db = db;
      const BiquadCoeffs target = compute_peaking(p);
      targets_[band * kEqChannels + 0] = target;
      targets_[band * kEqChannels + 1] = target;
    }
    const double blocks = (ramp_ms > 0.0 ? ramp_ms : 10.0) / 1000.0 * fs_ / 256.0;
    const double step = (blocks > 1.0) ? (1.0 / blocks) : 1.0;
    for (auto& s : steps_) s = step;
    active_mask_.store(mask, std::memory_order_release);  /* maske ONCE yayimlanir */
    pending_.fetch_add(1, std::memory_order_release);      /* publish (release-acquire zinciri) */
  }

  /* RT-yolu: per-block ramp + 62 biquad isleme. Tahsisat/lock/log YOK (ADR-017). */
  void process(float* L, float* R, std::size_t n) noexcept {
    /* Yeni publish vardi mi? (acquire). Varsa hedefleri kopyala, ramp baslat.
     * Pasif bantlarin ramp_to'si unity'ye zorlanir (mask acquire ile okunur). */
    const std::uint32_t seq = pending_.load(std::memory_order_acquire);
    const std::uint32_t mask = active_mask_.load(std::memory_order_acquire);
    if (seq != applied_) {
      for (std::uint32_t i = 0; i < kEqBiquads; ++i) {
        const std::uint32_t band = i / kEqChannels;
        ramp_from_[i] = biquads_[i].coefficients();
        ramp_to_[i] = ((mask >> band) & 1u) ? targets_[i] : kunity;
        ramp_pos_[i] = 0.0;
      }
      applied_ = seq;
    }

    /* Per-block ramp: katsayiyi ramp_from -> ramp_to dogrusal kaydir (§2.3).
     * Once konum ilerletilir; son adimda s==1.0 → hedef katsayi TAM uygulanir
     * (eski sira: s=0'da 'from' yazilir, pos clamp'lenir, hedef hic gelmezdi). */
    for (std::uint32_t i = 0; i < kEqBiquads; ++i) {
      if (ramp_pos_[i] >= 1.0) continue;
      ramp_pos_[i] += steps_[i];
      if (ramp_pos_[i] > 1.0) ramp_pos_[i] = 1.0;
      const double s = ramp_pos_[i];
      BiquadCoeffs c;
      c.b0 = ramp_from_[i].b0 + (ramp_to_[i].b0 - ramp_from_[i].b0) * s;
      c.b1 = ramp_from_[i].b1 + (ramp_to_[i].b1 - ramp_from_[i].b1) * s;
      c.b2 = ramp_from_[i].b2 + (ramp_to_[i].b2 - ramp_from_[i].b2) * s;
      c.a1 = ramp_from_[i].a1 + (ramp_to_[i].a1 - ramp_from_[i].a1) * s;
      c.a2 = ramp_from_[i].a2 + (ramp_to_[i].a2 - ramp_from_[i].a2) * s;
      biquads_[i].set_coefficients(c);
    }

    /* 31 bant x 2 kanal zincirleme isleme (her bant tek biquad — §2.2).
     * Bypass (ADR-106 kural 4): pasif bant + ramp tamam -> isleme atlanir (CPU kazanci). */
    for (std::uint32_t band = 0; band < kEqBands; ++band) {
      const std::uint32_t base = band * kEqChannels;
      if (!((mask >> band) & 1u) && ramp_pos_[base] >= 1.0 && ramp_pos_[base + 1] >= 1.0) continue;
      biquads_[base + 0].process_block(L, L, n);
      biquads_[base + 1].process_block(R, R, n);
    }
  }

  std::uint32_t active_mask() const noexcept {
    return active_mask_.load(std::memory_order_acquire);
  }
  double sample_rate() const noexcept { return fs_; }

private:
  static constexpr BiquadCoeffs kunity{1.0, 0.0, 0.0, 0.0, 0.0};

  std::array<Biquad, kEqBiquads> biquads_{};   /* alignas(64) — L1 uyumlu (§2.3) */
  std::array<BiquadCoeffs, kEqBiquads> targets_{};   /* RT-disi yazilir */
  std::array<BiquadCoeffs, kEqBiquads> ramp_from_{}; /* RT okur */
  std::array<BiquadCoeffs, kEqBiquads> ramp_to_{};   /* RT okur */
  std::array<double, kEqBiquads> ramp_pos_{};        /* RT yazar */
  std::array<double, kEqBiquads> steps_{};           /* RT-disi yazilir */
  std::array<double, kEqBands> gains_db_{};          /* RT-disi: bant bazli son kazanc (ADR-106 kural 5) */
  std::atomic<std::uint32_t> pending_{0};            /* publish sayaci */
  std::atomic<std::uint32_t> active_mask_{kEqMaskAll}; /* RT-disi yazar, RT acquire okur (ADR-106) */
  std::uint32_t applied_ = 0;                        /* RT gorur */
  double fs_ = 48000.0;
};

} // namespace neva
#endif /* NEVA_EQ_CHAIN_H */
