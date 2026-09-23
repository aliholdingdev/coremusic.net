---
title: "CoreMusic — shared/database/migrations Baðlam"
type: context
folder: "shared/database/migrations"
category: layer0-infrastructure
date: 2026-09-21
status: active
version: 1.0.0
---

# shared/database/migrations — CLAUDE.md

OAuth connection ve state migration'larý.

| Dosya | Amaç |
|-------|------|
| oauth_connections.php | OAuth baðlantý tablosu |
| oauth_states.php | OAuth state tablosu |

**Kural:** Forward-only migration (ADR-014). Geri migration yasak.