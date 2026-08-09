---
type: rules
category: engineering-rules
title: "CoreMusic — Electronics Engineering Rules"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Electronics Engineering Rules

**See also:** [[ADR-061-electronics-architecture]] · [[ADR-062-dsp-pipeline-architecture]] · [[ADR-063-hardware-design-standards]]

---

## 1. Zero-Allocation (Audio Thread)

Audio thread'de ❌ yasak:
- `malloc()`, `free()`, `new`, `delete`
- `std::make_shared`, `std::vector` push_back
- I/O blocking
- `throw`, `catch`

✅ İzin:
- Stack tahsisi
- `std::atomic`
- SIMD (SSE2/AVX2/NEON)
- `constexpr`, `alignas(64)`

## 2. Lock-Free (Audio Thread)

Audio thread'de ❌ yasak:
- `mutex`, `lock_guard`, `unique_lock`
- `condition_variable`
- `semaphore`

✅ İzin:
- `std::atomic` (memory_order_relaxed/acquire/release)
- Spinlock (kısa süreli)

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

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
