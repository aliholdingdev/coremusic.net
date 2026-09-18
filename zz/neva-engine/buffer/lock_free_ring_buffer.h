#pragma once
// ============================================================================
// Lock-Free SPSC Ring Buffer — Cache-Line Aligned
// Single Producer Single Consumer, zero-allocation, noexcept
// For audio thread ↔ UI/control thread communication
// ============================================================================

#include <atomic>
#include <array>
#include <cstddef>
#include <span>
#include <new>        // std::hardware_destructive_interference_size
#include <algorithm>
#include <cassert>

namespace cm::neva {

// Cache line size (x86-64/ARM64)
inline constexpr std::size_t kCacheLineSize = 64;

// ---------------------------------------------------------------------------
// LockFreeRingBuffer<T, Capacity>
// Capacity must be power of two for fast modulo via bitmask
// ---------------------------------------------------------------------------

template <typename T, std::size_t Capacity>
    requires (Capacity > 0) && ((Capacity & (Capacity - 1)) == 0)
class LockFreeRingBuffer {
public:
    LockFreeRingBuffer() = default;
    ~LockFreeRingBuffer() = default;

    // Non-copyable, non-movable (atomic members)
    LockFreeRingBuffer(const LockFreeRingBuffer&) = delete;
    LockFreeRingBuffer& operator=(const LockFreeRingBuffer&) = delete;
    LockFreeRingBuffer(LockFreeRingBuffer&&) = delete;
    LockFreeRingBuffer& operator=(LockFreeRingBuffer&&) = delete;

    // Producer: push single element (audio thread)
    [[nodiscard]] bool push(const T& item) noexcept {
        const auto w = writePos_.load(std::memory_order_relaxed);
        const auto r = readPos_.load(std::memory_order_acquire);
        if ((w - r) >= Capacity) {
            return false;  // full — drop frame
        }
        buffer_[w & kMask] = item;
        writePos_.store(w + 1, std::memory_order_release);
        return true;
    }

    // Producer: push span of elements
    [[nodiscard]] std::size_t pushBulk(std::span<const T> items) noexcept {
        const auto w = writePos_.load(std::memory_order_relaxed);
        const auto r = readPos_.load(std::memory_order_acquire);
        const auto available = Capacity - (w - r);
        const auto count = std::min(items.size(), available);
        for (std::size_t i = 0; i < count; ++i) {
            buffer_[(w + i) & kMask] = items[i];
        }
        writePos_.store(w + count, std::memory_order_release);
        return count;
    }

    // Consumer: pop single element (control thread)
    [[nodiscard]] bool pop(T& item) noexcept {
        const auto r = readPos_.load(std::memory_order_relaxed);
        const auto w = writePos_.load(std::memory_order_acquire);
        if (r == w) {
            return false;  // empty
        }
        item = buffer_[r & kMask];
        readPos_.store(r + 1, std::memory_order_release);
        return true;
    }

    // Consumer: pop span
    [[nodiscard]] std::size_t popBulk(std::span<T> items) noexcept {
        const auto r = readPos_.load(std::memory_order_relaxed);
        const auto w = writePos_.load(std::memory_order_acquire);
        const auto available = w - r;
        const auto count = std::min(items.size(), available);
        for (std::size_t i = 0; i < count; ++i) {
            items[i] = buffer_[(r + i) & kMask];
        }
        readPos_.store(r + count, std::memory_order_release);
        return count;
    }

    // Query
    [[nodiscard]] std::size_t size() const noexcept {
        const auto w = writePos_.load(std::memory_order_acquire);
        const auto r = readPos_.load(std::memory_order_acquire);
        return w - r;
    }

    [[nodiscard]] bool empty() const noexcept { return size() == 0; }

    [[nodiscard]] bool full() const noexcept { return size() >= Capacity; }

    void reset() noexcept {
        readPos_.store(0, std::memory_order_relaxed);
        writePos_.store(0, std::memory_order_relaxed);
    }

private:
    static constexpr std::size_t kMask = Capacity - 1;

    // Separate cache lines to avoid false sharing
    alignas(kCacheLineSize) std::atomic<std::size_t> writePos_{0};
    alignas(kCacheLineSize) std::atomic<std::size_t> readPos_{0};
    alignas(kCacheLineSize) std::array<T, Capacity> buffer_{};
};

// ---------------------------------------------------------------------------
// Specialization for float audio samples (SIMD-friendly bulk copy)
// ---------------------------------------------------------------------------

template <std::size_t Capacity>
    requires (Capacity > 0) && ((Capacity & (Capacity - 1)) == 0)
class LockFreeRingBuffer<float, Capacity> {
public:
    LockFreeRingBuffer() = default;
    ~LockFreeRingBuffer() = default;

    LockFreeRingBuffer(const LockFreeRingBuffer&) = delete;
    LockFreeRingBuffer& operator=(const LockFreeRingBuffer&) = delete;

    [[nodiscard]] bool push(float sample) noexcept {
        const auto w = writePos_.load(std::memory_order_relaxed);
        const auto r = readPos_.load(std::memory_order_acquire);
        if ((w - r) >= Capacity) return false;
        buffer_[w & kMask] = sample;
        writePos_.store(w + 1, std::memory_order_release);
        return true;
    }

    [[nodiscard]] bool pop(float& sample) noexcept {
        const auto r = readPos_.load(std::memory_order_relaxed);
        const auto w = writePos_.load(std::memory_order_acquire);
        if (r == w) return false;
        sample = buffer_[r & kMask];
        readPos_.store(r + 1, std::memory_order_release);
        return true;
    }

    // Bulk transfer for audio processing blocks
    [[nodiscard]] std::size_t pushBlock(const float* data, std::size_t count) noexcept {
        const auto w = writePos_.load(std::memory_order_relaxed);
        const auto r = readPos_.load(std::memory_order_acquire);
        const auto space = Capacity - (w - r);
        const auto n = std::min(count, space);
        const auto startIdx = w & kMask;
        // Handle wrap-around
        const auto firstChunk = std::min(n, Capacity - startIdx);
        std::copy_n(data, firstChunk, &buffer_[startIdx]);
        if (firstChunk < n) {
            std::copy_n(data + firstChunk, n - firstChunk, buffer_.data());
        }
        writePos_.store(w + n, std::memory_order_release);
        return n;
    }

    [[nodiscard]] std::size_t popBlock(float* data, std::size_t count) noexcept {
        const auto r = readPos_.load(std::memory_order_relaxed);
        const auto w = writePos_.load(std::memory_order_acquire);
        const auto avail = w - r;
        const auto n = std::min(count, avail);
        const auto startIdx = r & kMask;
        const auto firstChunk = std::min(n, Capacity - startIdx);
        std::copy_n(&buffer_[startIdx], firstChunk, data);
        if (firstChunk < n) {
            std::copy_n(buffer_.data(), n - firstChunk, data + firstChunk);
        }
        readPos_.store(r + n, std::memory_order_release);
        return n;
    }

    [[nodiscard]] std::size_t size() const noexcept {
        return writePos_.load(std::memory_order_acquire) -
               readPos_.load(std::memory_order_acquire);
    }

    [[nodiscard]] bool empty() const noexcept { return size() == 0; }

    void reset() noexcept {
        readPos_.store(0, std::memory_order_relaxed);
        writePos_.store(0, std::memory_order_relaxed);
    }

private:
    static constexpr std::size_t kMask = Capacity - 1;

    alignas(kCacheLineSize) std::atomic<std::size_t> writePos_{0};
    alignas(kCacheLineSize) std::atomic<std::size_t> readPos_{0};
    alignas(kCacheLineSize) std::array<float, Capacity> buffer_{};
};

// ---------------------------------------------------------------------------
// Multi-channel ring buffer (one SPSC per channel)
// ---------------------------------------------------------------------------

template <std::size_t Channels, std::size_t Capacity>
    requires (Channels > 0) && (Capacity > 0) && ((Capacity & (Capacity - 1)) == 0)
class MultiChannelRingBuffer {
public:
    [[nodiscard]] bool pushChannel(std::size_t ch, float sample) noexcept {
        if (ch >= Channels) return false;
        return channels_[ch].push(sample);
    }

    [[nodiscard]] bool popChannel(std::size_t ch, float& sample) noexcept {
        if (ch >= Channels) return false;
        return channels_[ch].pop(sample);
    }

    [[nodiscard]] std::size_t pushBlock(std::size_t ch, const float* data, std::size_t n) noexcept {
        if (ch >= Channels) return 0;
        return channels_[ch].pushBlock(data, n);
    }

    [[nodiscard]] std::size_t popBlock(std::size_t ch, float* data, std::size_t n) noexcept {
        if (ch >= Channels) return 0;
        return channels_[ch].popBlock(data, n);
    }

    void reset() noexcept {
        for (auto& ch : channels_) ch.reset();
    }

private:
    std::array<LockFreeRingBuffer<float, Capacity>, Channels> channels_;
};

} // namespace cm::neva
