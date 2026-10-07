---
title: "12-domain-d03-donanim-surucu — Mimari Domain Tablosu d03"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d03 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d03
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 12-domain-d03-donanim-surucu — Domain d03: Donanım & Sürücü (K100–K149)

> **Kapsam:** Ses donanımı bileşenleri (DAC/ADC/SoC/amplifikatör) ve OS sürücü yığını.
> **Eski dizin karşılıkları:** [[architecture/k1-donanim]] · [[architecture/k2-surucu]] · **Legacy K1 + K2** → bu domain.
> **Gerçeklik notu (2026-10-07):** Donanım satırları yalnız **tasarım** düzeyindedir (DESIGN); fiziksel üretim/PCB yok. Sürücü satırları hedef platformdur (PLANNED) — çalıştırma kanıtı yok.

## Katman Tablosu (K100–K149 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K100 | XMOS XU316 USB audio SoC | DESIGN | .claude/CLAUDE.md §5 (K1) · kök CLAUDE.md §Web Verification (xmos.com) |
| K101 | PCM3168A codec (ADC+DAC, 8.1) | DESIGN | ADR-038 · kök CLAUDE.md §Web Verification (ti.com) |
| K102 | AK4458 32-bit 8ch DAC | DESIGN | .claude/CLAUDE.md §5 (K1) · kök CLAUDE.md §Web Verification (akm.com) |
| K103 | Class AB amplifikatör modülü (H1) | DESIGN | ADR-089 · .ai/brain.md §5 (H1) |
| K104 | MJL21194/93 çıkış çifti (TO-264) | DESIGN | ADR-089 · .ai/brain.md §5 (H1 tablosu) |
| K105 | Boost ±35V (LM5122 ×2) | DESIGN | ADR-089 · .claude/CLAUDE.md §5 (K17) |
| K106 | 6S LiPo (22.2V) güç girişi | DESIGN | ADR-089 |
| K107 | 14-konnektör / ses giriş-çıkış dizisi | DESIGN | .claude/CLAUDE.md §5 (K1 satırı) |
| K108 | ASIO sürücü katmanı (ASIO SDK 2.3.4) | PLANNED | ADR-017 · kök CLAUDE.md §24 (SDK indirme referansı) |
| K109 | WASAPI sürücü katmanı | PLANNED | .claude/CLAUDE.md §13 (Tier 1 ses sürücüsü) |
| K110 | ALSA sürücü katmanı | PLANNED | .claude/CLAUDE.md §13 (Tier 2) |
| K111 | PipeWire sürücü katmanı | PLANNED | .claude/CLAUDE.md §13 (Tier 2) |
| K112 | CoreAudio sürücü katmanı | PLANNED | .claude/CLAUDE.md §13 (Tier 3) |
| K113 | I2S (RPi5 DAC) | PLANNED | .claude/CLAUDE.md §5 (K2 satırı) |
| K114 | USB audio sınıfı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K115 | Bluetooth audio | PLANNED | .claude/CLAUDE.md §5 (K2) · ⚠️ kod kanıtı yok |
| K116 | DLNA render (donanım) | PLANNED | .claude/CLAUDE.md §5 (K2) · ⚠️ kod kanıtı yok |
| K117 | Donanım-yazılım köprüsü (K2 kısıtı) | PLANNED | .claude/CLAUDE.md §5 (K2 Hard Guardrail) |
| K118 | Electronics mimari kararı | DESIGN | ADR-061 (.ai/.decisions/accepted/ADR-061-electronics-architecture.md) |
| K119 | Electronics platform mimarisi | DESIGN | ADR-064 (.ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md) |
| K120 | Donanım tasarım standartları | DESIGN | ADR-063 (.ai/.decisions/accepted/ADR-063-hardware-design-standards.md) |
| K121 | 8.1 ses kartı çip seçimi (H001) | DESIGN | ADR-038 · .claude/CLAUDE.md §22 (PCM5122 yasak) |
| K122 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K123 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K124 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K125 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K126 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K127 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K128 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K129 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K130 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K131 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K132 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K133 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K134 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K135 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K136 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K137 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K138 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K139 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K140 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K141 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K142 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K143 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K144 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K145 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K146 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K147 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K148 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K149 | Rezerve — d03 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K100–K149) · DESIGN 14 · PLANNED 36 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-038 · ADR-061 · ADR-063 · ADR-064 · ADR-089 (`.ai/.decisions/accepted/`)
