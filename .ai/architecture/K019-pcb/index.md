---
type: architecture
category: layer
title: "K019 — PCB"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K019 — PCB

## §1 Kimlik
- Katman: K019 · Alan: **A5** (K16-K20).
- Kapsam: PCB tasarımı ve donanım tasarım standartları.

## §2 Sorumluluk
1. Donanım tasarım standartlarına uygun PCB yerleşimi (ADR-063).
2. Sinyal/güç/termal katman planı (şartname düzeyi).
3. BOM ve üretim dosyalarının hazırlanması (üretim = K020).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 zemini (K000–K005) — sayısal kural (plan §2-1).
- **Üst (çağıran):** K016 · K017 · K018 (fiziksel taşıyıcı) · K020.
- A5 içi kenarlar plan §3'te yok → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-063 | Hardware Design Standards |
| ADR-061 | Electronics Architecture (L6) — üst şema |

## §5 Durum
**PLANNED** — ADR-063 dosyası diskte; PCB tasarım dosyası yok: `**/*.{kicad_pcb,gbr,gerber,brd}` glob = 0 (2026-10-09).

## §6 Risk / Not
- DRC/gerber kanıtı yok → üretim-öncesi doğrulama yapılmadı (**UNKNOWN**).
