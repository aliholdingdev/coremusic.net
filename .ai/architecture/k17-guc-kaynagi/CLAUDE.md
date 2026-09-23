---
title: "CoreMusic — K17 Güç Kaynağı CLAUDE.md"
type: layer-guide
folder: "architecture/k17-guc-kaynagi"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K17 Güç Kaynağı — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | DC-Only | AC gürültüsü |
| 2 | ±35V simetrik | Dengesiz çıkış |
| 3 | Koruma devreleri zorunlu | Donanım hasarı |

## 2. Güç Kısıtları

| Parametre | Değer |
|-----------|-------|
| Giriş | 22.2V (6S LiPo) |
| Çıkış | ±35V simetrik |
| Verimlilik | %96 |
| Ripple | <50mV |
| UVP | 18V (3.0V/cell) |
| OVP | 25.2V (4.2V/cell) |

---

*K17 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
