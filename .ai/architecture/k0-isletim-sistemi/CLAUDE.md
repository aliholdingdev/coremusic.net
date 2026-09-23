---
title: "CoreMusic — K0 İşletim Sistemi CLAUDE.md"
type: layer-guide
folder: "architecture/k0-isletim-sistemi"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K0 İşletim Sistemi — CLAUDE.md

**Bu dosya K0 katmanı için özel AI talimatlarını içerir.**

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Audio thread malloc yasak | Crash |
| 2 | Audio thread mutex yasak | Deadlock |
| 3 | Platform API wrapper ile soyutlanmalı | Portability hatası |
| 4 | Thread öncelik hiyerarşisine uy | Ses takılması |

## 2. Teknoloji Kısıtlamaları

| Kısıt | Değer |
|-------|-------|
| Dil | C++20 |
| Minimum Standard | C++17 |
| Compiler | MSVC 17+, GCC 12+, Clang 15+ |
| Memory Model | C++11 memory model |
| Threading | std::thread, platform-specific |

## 3. Yasaklı Örüntüler

```cpp
// ❌ YASAK — Audio thread'de
malloc(), free(), new, delete, std::vector::push_back

// ✅ DOĞRU
alignas(64) float buffer[4096];
std::atomic<size_t> head;
```

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |

---

*K0 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
