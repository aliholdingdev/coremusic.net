---
title: "11-domain-d02-ses-motoru-dsp — Mimari Domain Tablosu d02"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d02 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d02
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 11-domain-d02-ses-motoru-dsp — Domain d02: Ses Motoru & DSP (K050–K099)

> **Kapsam:** Web oynatıcı (coreplayer), DSP tasarımı (Neva Engine, EQ, reverb), donanım-mod ses yolu hedefleri.
> **Eski dizin karşılığı:** [[architecture/k3-ses-motoru]] · **Legacy K3** → bu domain.
> **Gerçeklik notu (2026-10-07):** C++ ses motoru kodu **diskte YOK** (repo genelinde derlenebilir C++ dosyası = 1); tüm native DSP satırları `DESIGN` (tasarım belgesi) veya `PLANNED`'dır. Web oynatıcı tarafı gerçektir (IMPLEMENTED).

## Katman Tablosu (K050–K099 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K050 | Web oynatıcı çekirdeği (coreplayer) | IMPLEMENTED | assets.coremusic.net/js/coreplayer/ (5 dosya) |
| K051 | Oynatıcı manager katmanı | IMPLEMENTED | assets.coremusic.net/js/managers/ (5 dosya) |
| K052 | Footer player | IMPLEMENTED | assets.coremusic.net/js/core/footer.init.js · ADR-018 |
| K053 | Theme/JS entegrasyonu (oyuncu temaları) | IMPLEMENTED | assets.coremusic.net/js/managers/ThemeManager.js · ADR-044 |
| K054 | NEVA EQ/DSP ayar şeması (veri) | IMPLEMENTED | .ai/.sql/mysql/coremusic_neva.sql |
| K055 | Neva Engine (C++20 / JUCE 9) | DESIGN | ADR-062 · kod diskte YOK (find: 1 C++ dosyası — 2026-10-07) |
| K056 | DSP chain — 31-band EQ | DESIGN | .claude/CLAUDE.md §19 · ADR-025 |
| K057 | Reverb modları (4) | DESIGN | .claude/CLAUDE.md §19 |
| K058 | Compressor / Limiter | DESIGN | .claude/CLAUDE.md §19 |
| K059 | 8.1 surround karışım (7.1 + LFE) | DESIGN | .claude/CLAUDE.md §19 · ADR-038 |
| K060 | Latency hedefi — <10ms ASIO / <20ms WASAPI | DESIGN | .claude/CLAUDE.md §19 |
| K061 | ASIO Exclusive Mode | DESIGN | ADR-017 |
| K062 | WASAPI fallback | DESIGN | ADR-017 · .claude/CLAUDE.md §22 (edge case) |
| K063 | Per-OS NEVA player | DESIGN | ADR-019 |
| K064 | Professional EQ sistemi (presets) | DESIGN | ADR-025 |
| K065 | C++ guardrails (zero-allocation, lock-free, noexcept) | DESIGN | .claude/CLAUDE.md §19 |
| K066 | Sample format/rate standardı (Float32 · 48kHz) | DESIGN | .claude/CLAUDE.md §19 |
| K067 | Audio engine test altyapısı (Google Test ≥80%) | PLANNED | .claude/CLAUDE.md §17 · ⚠️ VERIFICATION REQUIRED (test yok) |
| K068 | Dolby Atmos / DTS:X işleme | DESIGN | .claude/CLAUDE.md §5 (K3 satırı) · kod yok |
| K069 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K070 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K071 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K072 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K073 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K074 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K075 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K076 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K077 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K078 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K079 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K080 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K081 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K082 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K083 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K084 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K085 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K086 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K087 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K088 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K089 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K090 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K091 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K092 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K093 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K094 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K095 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K096 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K097 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K098 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K099 | Rezerve — d02 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K050–K099) · IMPLEMENTED 5 · DESIGN 14 · PLANNED 31 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-017 · ADR-018 · ADR-019 · ADR-025 · ADR-038 · ADR-062 (`.ai/.decisions/accepted/`)
