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
- **Architecture:** 21 Layers (K0-K20), 1130 Components, 10 Web Panels, 7 Microservices.
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
| 7 | [.ai/architecture/master-architecture-index.md](.ai/architecture/master-architecture-index.md) | Master 21-layer architecture index (1130 components) |

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
*Vault Steward: Bayram Ali | SSOT: .ai/ | Last Updated: 2026-09-19*
