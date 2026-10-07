---
title: "19-domain-d10-elektronik-tasarim — Mimari Domain Tablosu d10"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d10 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d10
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 19-domain-d10-elektronik-tasarim — Domain d10: Elektronik Tasarım (K450–K499)

> **Kapsam:** Class AB amplifikatör, güç kaynağı, termal, PCB, BOM — **tasarım aşaması** donanım katmanları (K16–K20 + firmware).
> **Eski dizin karşılıkları:** [[architecture/k16-class-ab]] · [[architecture/k17-guc-kaynagi]] · [[architecture/k18-termal]] · [[architecture/k19-pcb]] · [[architecture/k20-bom]] · [[architecture/firmware]] · **Legacy K16–K20** → bu domain.
> **Gerçeklik notu (2026-10-07):** Bu domainde **hiçbir satır IMPLEMENTED değildir** — kod/fiziksel üretim yok. Tasarım kaynakları: `.ai/brain.md` §5 (Electronics Registry, H1-H5), `.claude/CLAUDE.md` §5 (K16-K20 tabloları), ADR-089/090. ⚠️ Ayrıntılı tasarım dosyaları (`bom-classab.md`, `amplifier-classab-circuit.md`, `pcb-classab.md`, `thermal-design-classab.md`, `power-supply-classab.md`) **diskte YOK** (eski ağaçla silindi — 2026-10-07 glob: 0 isabet).

## Katman Tablosu (K450–K499 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K450 | Class AB topolojisi (Darlington, 50W/kanal) | DESIGN | ADR-089 · .claude/CLAUDE.md §5 (K16) |
| K451 | MJL21194/93 çıkış transistörleri (TO-264) | DESIGN | ADR-089 · .ai/brain.md §5 (H1 tablosu) |
| K452 | 8 kanal modüler amplifikatör | DESIGN | ADR-089 · ADR-090 (kanal varyant ürün ailesi) |
| K453 | Enable pin / kanal bağımsızlığı | DESIGN | .claude/CLAUDE.md §5 (K16 kısıtı) |
| K454 | THD <0.005% hedefi | DESIGN | .claude/CLAUDE.md §5 (K16) |
| K455 | Bias devresi (Vbe çarpımı, diferansiyel çift) | DESIGN | .ai/brain.md §5 (H1: Q1/Q2/Q5/Q9/Q10/Q15/Q17) |
| K456 | MCU telemetri (STM32/RP2040) | DESIGN | .claude/CLAUDE.md §5 (H1) |
| K457 | LM5122 boost + inverting (±35V) | DESIGN | ADR-089 · .claude/CLAUDE.md §5 (K17) · kök CLAUDE.md §Web Verification (ti.com) |
| K458 | 6S LiPo (22.2V) / güç girişi | DESIGN | ADR-089 |
| K459 | OR-ing güç kaynağı | DESIGN | .claude/CLAUDE.md §5 (K17) |
| K460 | UVP / OVP / OCP / OTP koruma | DESIGN | .claude/CLAUDE.md §5 (K17 kısıtı) |
| K461 | %96 verim hedefi | DESIGN | .claude/CLAUDE.md §5 (K17) |
| K462 | ±40V Push-Pull alternatif (SG3525 — H2 eski tasarım) | DESIGN | .ai/brain.md §5 (H2 · Electronics Registry) |
| K463 | Heatsink — Fischer SK53-100-SA | DESIGN | .claude/CLAUDE.md §5 (K18) |
| K464 | 80mm PWM fan | DESIGN | .claude/CLAUDE.md §5 (K18) |
| K465 | KSD301 thermal cutoff | DESIGN | .claude/CLAUDE.md §5 (K18 kısıtı) |
| K466 | 41W/kanal ısı yönetimi hedefi | DESIGN | .claude/CLAUDE.md §5 (K18) |
| K467 | PCB — 6-layer stackup | DESIGN | .claude/CLAUDE.md §5 (K19) |
| K468 | PCB — ENIG finish / 2oz copper | DESIGN | .claude/CLAUDE.md §5 (K19 kısıtı) |
| K469 | PCB — star ground / thermal vias | DESIGN | .claude/CLAUDE.md §5 (K19) |
| K470 | PCB — impedans eşleme (90Ω USB / 50Ω I2S) | DESIGN | .claude/CLAUDE.md §5 (H4) |
| K471 | BOM — 1.775 satır (~$682 sistem) | DESIGN | .claude/CLAUDE.md §5 (K20) · ⚠️ bom-classab.md diskte YOK (2026-10-07) |
| K472 | BOM — 639 bileşen / 8 kanal (~$1.067) | DESIGN | .ai/brain.md §5 (Electronics Registry satırı) · ⚠️ dosya silinmiş |
| K473 | Kanal varyant SKU ailesi (mono → 8+1) | DESIGN | ADR-090 (.ai/.decisions/accepted/ADR-090-channel-variant-product-family.md) |
| K474 | Donanım tasarım standartları | DESIGN | ADR-063 · ADR-064 |
| K475 | Electronics mimarisi | DESIGN | ADR-061 (.ai/.decisions/accepted/ADR-061-electronics-architecture.md) |
| K476 | XMOS firmware | PLANNED | .ai/architecture/firmware/index.md (iskelet) · ⚠️ kaynak kod YOK |
| K477 | STM32/RP2040 MCU firmware | PLANNED | ⚠️ VERIFICATION REQUIRED — kaynak kod yok |
| K478 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K479 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K480 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K481 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K482 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K483 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K484 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K485 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K486 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K487 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K488 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K489 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K490 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K491 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K492 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K493 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K494 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K495 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K496 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K497 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K498 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K499 | Rezerve — d10 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K450–K499) · DESIGN 26 · PLANNED 24 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 (`.ai/.decisions/accepted/`)
