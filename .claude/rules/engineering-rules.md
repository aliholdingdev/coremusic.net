---
type: rules
category: engineering-rules
title: "CoreMusic — Electronics Engineering Rules"
date: 2026-08-09
updated: 2026-08-10
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Electronics Engineering Rules

**See also:** [[ADR-061-electronics-architecture]] · [[ADR-062-dsp-pipeline-architecture]] · [[ADR-063-hardware-design-standards]] · [[ADR-038-8.1-sound-card-chip-selection]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Zero-Allocation (Audio Thread) — Web-Verified

**Kaynak:** Timur Doumler (ADC'20), Ross Bencina, Niccolo Abate

Audio thread'de ❌ yasak:
- `malloc()`, `free()`, `new`, `delete` — OS allocator lock kullanır
- `std::make_shared`, `std::vector::push_back` — heap reallocation
- `std::string` operations — heap allocation
- `std::any`, `std::variant` (non-trivial types) — type erasure = allocation
- I/O blocking — unbounded latency
- `throw`, `catch` — unwind info runtime'da üretilir

✅ İzin:
- Stack tahsisi (`std::array`, `static_vector`)
- `std::atomic` (lock-free, single instruction)
- SIMD (SSE2/AVX2/NEON)
- `constexpr` (compile-time computation)
- `alignas(64)` / `alignas(std::hardware_destructive_interference_size)`
- `std::span<T>` (zero-cost view, no copy)
- `std::pmr::monotonic_buffer_resource` (pre-allocated pool)

**Pre-Allocation Stratejisi:**
```
Tüm buffer'lar audio thread başlamadan önce allocate edilir.
Stack-allocated fixed buffers tercih edilir.
Heap allocation sadece non-RT thread'de yapılır.
```

## 2. Lock-Free (Audio Thread) — Web-Verified

**Kaynak:** Timur Doumler (ADC'20), C++ Standard

Audio thread'de ❌ yasak:
- `std::mutex::lock()` — thread'i bloklar
- `std::mutex::try_lock()` — unlock() system call tetikler, RT-safe değil
- `std::lock_guard`, `std::unique_lock` — RAII destructor unlock çağırır
- `std::condition_variable` — system call
- `semaphore` — OS primitive

✅ İzin:
- `std::atomic` (memory_order_relaxed/acquire/release)
- Progressive back-off spinlock (Stage 1: `_mm_pause()`, Stage 2: 10x `_mm_pause()`, Stage 3: `yield()`)
- Lock-free SPSC ring buffer
- Immutable data structures (thread-safe by design)

**Memory Order Kuralları:**
| Kullanım | Doğru Memory Order |
|----------|-------------------|
| Parameter update (fire-and-forget) | `memory_order_relaxed` |
| Data publish (producer→consumer) | `release` (write) + `acquire` (read) |
| Spinlock try_lock | `memory_order_acquire` |
| Spinlock unlock | `memory_order_release` |

**Audio Thread Sadece `try_lock()` Kullanır:**
```
Audio thread: try_lock() → başarısızsa → fallback stratejisi
Message thread: lock() → unlock() (RT-safe değil, ama message thread'de OK)
```

## 3. Layer Violation

L0 → L2/L3 veya L1 → L3 gibi kural ihlalleri tespit edilirse derhal revert + log ERROR.

## 4. PCM5122 Yasak (H001)

PCM5122 ile 8.1 surround YAPILAMAZ. Sadece 2 kanal destekler. PCM3168A veya AK4458 kullanın.

## 5. Middleware Sırası

SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf. Sıra değiştirilmez.

## 6. CSRF Token

CSRF Token Key = `csrf_token`. `_csrf_token` yasak.

## 7. ORM Yasak

Sadece PDO prepared statement. SELECT * yasak.

## 8. Framework Yasak

Sadece Vanilla JS + ITCSS.

## 9. MSA Limit

Görev başına max 15 dosya.

## 10. Zero Code Before Plan

Plan onayı olmadan kod yazma yasağı.

## 11. XMOS Firmware Rules (Web-Verified: XMOS Documentation)

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Language | XC + C | XMOS lib_i2s v6.0.1 |
| I2S MCLK | 24.576 MHz (48kHz) / 22.5792 MHz (44.1kHz) | XMOS lib_xua v5.5.0 |
| I2S Data Bits | 32-bit default | XMOS AN00162 |
| I2S Channels | 2 (stereo) / 8+ (TDM) | XMOS lib_i2s |
| Port Types | 1-bit ports, 32-bit buffered | XMOS lib_xua |
| Clock Blocks | 2 required (bit + master) | XMOS lib_xua |
| I2C Config | Remote I2C for DAC/ADC | XMOS AN00162 |
| Build System | XCommon CMake | XMOS lib_i2s |
| XC Tools | 15.3.1+ | XMOS AN00162 |

## 12. JUCE Audio Engine Rules (Web-Verified: JUCE 9, ADC 2026)

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Framework | JUCE 9.0.0 | JUCE 9 release |
| C++ Standard | C++20 minimum | JUCE CMake |
| Audio Callback | `processBlock()` | JUCE API |
| Parameter System | APVTS | JUCE best practice |
| Thread Safety | `std::atomic` for shared params | JUCE documentation |
| Denormals | `ScopedNoDenormals` in processBlock | JUCE best practice |
| Buffer Sizes | 64-1024 (128 recommended) | JUCE community |
| Plugin Formats | VST3, AU, AAX, CLAP, LV2 | JUCE 8+ |
| Build System | CMake 3.22+ | JUCE 9 |

**JUCE processBlock Yasakları:**
- `new` / `delete` / `malloc` — heap allocation
- `mutex.lock()` — blocking
- `std::vector::push_back` — reallocation
- `String` concatenation — allocation
- `DBG()` in release — I/O
- Virtual function calls on unknown objects — vtable indirection

## 13. PHP ↔ C++ Interface Rules

| Kural | Değer | ADR |
|-------|-------|-----|
| Communication | API layer (HTTP/WebSocket) | [[ADR-039-7-service-platform-architecture]] |
| Direct call | ❌ YASAK | — |
| Data format | JSON / msgpack | — |
| Authentication | JWT token | [[ADR-052-hybrid-auth-architecture]] |
| Error handling | HTTP status codes + JSON error | [[ADR-053-enterprise-router-architecture]] |

## 14. Latency Targets (Web-Verified)

| API | Platform | Round-Trip | Buffer Size |
|-----|----------|-----------|-------------|
| ASIO | Windows | 2-10ms | 32-256 |
| CoreAudio | macOS | 5-15ms | 64-512 |
| WASAPI Exclusive | Windows | 10-30ms | 128-1024 |
| ALSA | Linux | 15-50ms | 256-2048 |
| JACK | Linux | 3-15ms | 64-512 |

**ASIO Buffer Budget:** 512 samples @ 48kHz = 10.67ms total. DSP processing <5ms olmalı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
