# CoreMusic AI Configuration

> **Single Source of Truth (SSOT)** — ADR-042 (Vault Restructuring)
>
> This file is a **pointer**. All AI instructions, guardrails, and architecture live in `.ai/`.
> **Do not use this file for instructions.** Read `.ai/CLAUDE.md` instead.

## Quick Context

- **Project:** Enterprise digital media management platform (music, audio hardware, AI)
- **Stack:** PHP 8.4, Vanilla JS, C++20 (NevaEngine), MySQL 9 (18 BCNF), Class AB amplifier
- **Architecture:** 21 layers (K0-K20), 1000+ components, 10 web panels, 7 microservices
- **Key Rules:** No ORM (PDO only), No frameworks (Vanilla JS only), CSRF token = `csrf_token`
- **Amplifier:** Class AB Darlington, MJL21194/93, ±35V boost, 6S LiPo (22.2V)

## SSOT Links (Read These)

| Priority | File | Purpose |
|----------|------|---------|
| 1 | [.ai/CLAUDE.md](.ai/CLAUDE.md) | **AI Constitution** — All rules, guardrails, architecture |
| 2 | [.ai/AGENTS.md](.ai/AGENTS.md) | Agent registry, permissions, handover protocols |
| 3 | [.ai/WORKFLOW.md](.ai/WORKFLOW.md) | Processes, phases, workflow rules |
| 4 | [.ai/brain.md](.ai/brain.md) | ADR decisions, engineering constraints |
| 5 | [.ai/index.md](.ai/index.md) | Master catalog (787+ files) |
| 6 | [.ai/engine.md](.ai/engine.md) | Orchestration engine, task dispatch |

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
