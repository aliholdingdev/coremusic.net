---
title: "CoreMusic — K1 Donanım CLAUDE.md"
type: layer-guide
folder: "architecture/k1-donanim"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K1 Donanım — CLAUDE.md

**Bu dosya K1 katmanı için özel AI talimatlarını içerir.**

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | PCM5122 REDDEDİLMİŞ (ADR-038) | Yanlış donanım |
| 2 | Class AB zorunlu (ADR-089) | Yanlış topoloji |
| 3 | DC-Only güç kaynağı | Sistem hatası |
| 4 | Impedans eşleştirme | Sinyal kaybı |
| 5 | Thermal hesaplama zorunlu | Aşırı ısınma |

## 2. Bileşen Seçim Kısıtları

| Bileşen | Seçim | Neden |
|---------|-------|-------|
| USB Audio | XMOS XU316 | USB Audio Class 2.0 |
| DAC | PCM3168A | 8-out, 24-bit |
| Amplifikatör | MJL21194/MJL21193 | Class AB Darlington |
| Boost | LM5122 | ±35V converter |
| Batarya | 6S LiPo | 22.2V nominal |

## 3. Yasaklı Bileşenler

| ❌ Yasaklı | Neden | Doğru |
|-----------|-------|-------|
| PCM5122 | 2 kanal, 8.1 için yetersiz | PCM3168A |
| Class D (TPA3255) | CoreMusic Class AB tercih ediyor | MJL21194/MJL21193 |
| SMPS güç kaynağı | Gürültü | Linear + Boost |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

*K1 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
