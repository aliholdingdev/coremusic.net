# CoreMusic AI Configuration

> **Single Source of Truth (SSOT)** — ADR-042 (Vault Restructuring)
>
> This file is a **pointer**. All AI instructions, guardrails, architecture, and vision live in `.ai/`.
> **Do not use this file for instructions.** Read `.ai/CLAUDE.md` instead.

## Quick Context

- **Project:** CoreMusic — Commercial Digital Media Ecosystem & Revenue Platform (Software, Audio Hardware, AI).
- **Vision:** *"Aynı Müzik Her Yerde Seninle"* — Absolute Data Ownership (Offline-First), Seamless Handoff, Bit-Perfect Hi-Fi Audio.
- **Problems Solved:** Fragmented platforms → Unified ecosystem; Lossy audio → 32-bit Float Neva Engine; Rental lock-in → Absolute ownership; Static UI → Ambient Aura & AI Theme Maker; Device mismatch → Handoff & Multi-Room; Lack of pro tools → Integrated 8.1 Surround, LUFS, 31-band EQ.
- **Stack:** PHP 8.4, Vanilla JS, C++20 (NevaEngine), MySQL 9 (18 BCNF DBs, 156 Tables), Class AB 8x50W Amplifier (MJL21194/93).
- **Architecture:** 21 Layers (K0-K20) in 6 alt-layers (A0-A5: A0=K0-K5, A1=K6-K7, A2=K8-K9, A3=K10-K11, A4=K12-K15, A5=K16-K20), 1,095 Components, 10 Web Panels, 7 Microservices.
- **Vault Inventory:** 24 folders (21 K layers + firmware + adr + scripts) — 340 MD files on disk, matrix §10 matches disk exactly (verified 2026-09-24; scripts/ holds katman-sayim.ps1, non-MD, counted as note only).
- **ADR Series (two, do not merge — ADR-026 §3.4):** `.ai/.decisions/` = numbered series 001-089 (001-037 frozen, next new = 088+); `.ai/architecture/adr/` = architectural series 023-026 (023 hibrit-derinlik, 024 sürücü-firmware birleşme, 025 K8↔K15 sınırı, 026 sayım birimi 5.000).
- **Key Rules:** No ORM (PDO only), No JS frameworks (Vanilla JS only), CSRF token = `csrf_token`, Strict BCNF 3NF+.
- **Hardware Power:** ±35V LM5122 Interleaved Dual Boost, 6S LiPo (22.2V) or 19-24V DC laptop adapter (DC-ONLY).

## SSOT Links (Read These)

| Priority | File | Purpose |
|:---:|:---|:---|
| 1 | [.ai/CLAUDE.md](.ai/CLAUDE.md) | **AI Constitution** — 16 Hard Guardrails, architecture, all rules |
| 2 | [.ai/VISION.md](.ai/VISION.md) | **Vision & Philosophy** — Market crisis, ownership, problem-solution matrix |
| 3 | [.ai/PROJECTS.md](.ai/PROJECTS.md) | **Project Definition** — 10 core capabilities, 6 target users, 6 sectors |
| 4 | [.ai/AGENTS.md](.ai/AGENTS.md) | Agent registry, permissions, handover protocols |
| 5 | [.ai/WORKFLOW.md](.ai/WORKFLOW.md) | Processes, phases, workflow rules, hard gates |
| 6 | [.ai/brain.md](.ai/brain.md) | ADR decisions (001-089), engineering constraints |
| 7 | [.ai/architecture/master-architecture-index.md](.ai/architecture/master-architecture-index.md) | Master 21-layer architecture index (1,095 components) · ⚠️ hedef dosya diskte yok |
| 8 | [.ai/architecture/adr/](.ai/architecture/adr/) | Architectural ADR series (ADR-023-026) — separate from `.ai/.decisions/` numbered series |

## Quick Commands

```bash
# PHP dev server (port 81)
php -S localhost:81 -t public/

# Download service (port 3001)
cd download-service && npm run dev

# Tests
cd shared && vendor/bin/phpunit
```

---
*Vault Steward: Bayram Ali | SSOT: .ai/ | Last Updated: 2026-09-24*
