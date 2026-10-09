---
title: "Workflow"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 28.4.4
updated: 2026-10-06
total_files: 720
total_adr: 80
total_adr_disk: 60
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---# Workflow## Workflow

### §13 Deployment Modes

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

### §14 Platform Tiers

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Destekli |
| Tier 5 | ReactOS | ⚠️ Experimental |

---

### §20 Teknoloji Yığını Özeti (Dinamik Stack)

**İlke:** Programlama dili ve teknoloji yığını proje gereksinimlerine göre belirlenir — Node.js · C++ · C# · PHP + proje niteliğinin gerektirdiği diğerleri. Detay: [[engine.md]] §9, [[ROLE.md]] §11.

| Katman | Teknoloji | Durum | Referans |
|--------|-----------|-------|----------|
| PHP servis altyapısı | PHP 8.4 + PSR + php-di | IMPLEMENTED | 4 composer.json |
| Web panel frontend | Vanilla JS + ITCSS + BEM | PLANNED (kod) / IMPLEMENTED (spec) | ADR-001 |
| Audio/embedded | C++20, JUCE, XMOS xcc | PLANNED | electronic/, ADR-017/038 |
| I/O servisi | Node.js 20+ | PLANNED | ADR-026 |
| Windows araçları | C# / WDK | PLANNED | AGENTS.md #11 |
| Veri | MySQL şema (18 BCNF) | IMPLEMENTED (şema) | .ai/.sql/mysql/ |

---

