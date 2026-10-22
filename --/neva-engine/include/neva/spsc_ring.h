/* spsc_ring.h — header-only lock-free SPSC ring (ADR-0105 §2.5).
 * Tek uretici (decode thread) + tek tuketici (RT callback). Power-of-two kapasite,
 * acquire/release, run-time tahsisat YOK (std::array inline). Uretici/tuketici
 * indeksleri ayrI cache-line'da (false-sharing yok). */
#ifndef NEVA_SPSC_RING_H
#define NEVA_SPSC_RING_H

#include <array>
#include <atomic>
#include <cstddef>

namespace neva {

template <typename T, std::size_t Capacity>
class SpscRing {
  static_assert(Capacity >= 2 && (Capacity & (Capacity - 1)) == 0, "Capacity power-of-two olmali");

public:
  /* Uretici (decode thread). Doluysa false. */
  bool push(const T& v) noexcept {
    const std::size_t w = write_.load(std::memory_order_relaxed);
    const std::size_t next = (w + 1) & kMask;
    if (next == read_.load(std::memory_order_acquire)) return false; /* dolu */
    buf_[w] = v;
    write_.store(next, std::memory_order_release);
    return true;
  }

  /* Tuketici (RT thread). Bossa false. */
  bool pop(T& v) noexcept {
    const std::size_t r = read_.load(std::memory_order_relaxed);
    if (r == write_.load(std::memory_order_acquire)) return false; /* bos */
    v = buf_[r];
    read_.store((r + 1) & kMask, std::memory_order_release);
    return true;
  }

  bool empty() const noexcept {
    return read_.load(std::memory_order_acquire) == write_.load(std::memory_order_acquire);
  }

private:
  static constexpr std::size_t kMask = Capacity - 1;
  alignas(64) std::atomic<std::size_t> write_{0}; /* uretici cache-line */
  alignas(64) std::atomic<std::size_t> read_{0};  /* tuketici cache-line */
  std::array<T, Capacity> buf_{};
};

} // namespace neva
#endif /* NEVA_SPSC_RING_H */
