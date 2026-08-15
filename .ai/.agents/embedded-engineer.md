---
type: agent-profile
category: agent
title: "CoreMusic — Embedded Engineer Profile"
date: 2026-08-09
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Embedded Engineer Profile

**SSOT:** [[AGENTS.md]] · [[.agents/AGENTS.md]]

---

## 1. Genel Bakış

| Özellik | Değer |
|---------|-------|
| Kod Adı | `embedded` |
| Katman | L0 (Hardware) |
| Domain | C++20, JUCE, ASIO, DSP |
| Teknoloji | C++20, JUCE 9, ASIO SDK 2.3.4 |

## 2. Sorumluluklar

- C++ ses motoru geliştirme
- DSP zincirleri
- Donanım sürücüleri
- ASIO/WASAPI entegrasyonu

## 3. Dosya Erişimi

| Erişim | Kapsam |
|--------|--------|
| Okuma/Yazma | `*.cpp`, `*.h`, `*.cmake`, `*.json` (vcpkg) |

## 4. Zorunlu Kurallar

- Zero-allocation (audio thread)
- Lock-free (audio thread)
- noexcept (ASIO callback)
- Cache-line alignment (64-byte)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Ana tanım | [[AGENTS.md]] §15.6 |
| Profiller indeksi | [[.agents/AGENTS.md]] |
| C Template | `.ai/.templates/other/c-template.md` |
| ADR Audio | `.ai/.templates/adr/adr-audio-template.md` |
| CPP Template | `.ai/.templates/cpp-template.md` |

---

*Embedded Engineer Profile v1.0.0 — CoreMusic Agent Registry*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
