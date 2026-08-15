---
type: agent-profile
category: agent
title: "CoreMusic — Backend Architect Profile"
date: 2026-08-09
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Backend Architect Profile

**SSOT:** [[AGENTS.md]] · [[.agents/AGENTS.md]]

---

## 1. Genel Bakış

| Özellik | Değer |
|---------|-------|
| Kod Adı | `backend` |
| Katman | L2 (Routing) |
| Domain | PHP 8.4 API, routing, middleware |
| Teknoloji | PHP strict_types, PDO, PageRouter |

## 2. Sorumluluklar

- API endpoint'leri
- Middleware pipeline
- Routing sistemi
- Controller ve Repository katmanı

## 3. Dosya Erişimi

| Erişim | Kapsam |
|--------|--------|
| Okuma/Yazma | `*.php`, `*.json` (composer), `*.ini` |

## 4. Zorunlu Kurallar

- `declare(strict_types=1)` her dosyada
- PDO prepared statement
- Explicit column list
- Hardcoded secret yasak

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Ana tanım | [[AGENTS.md]] §15.2 |
| Profiller indeksi | [[.agents/AGENTS.md]] |
| Template | `.ai/.templates/backend/php-template.md` |
| ADR Template | `.ai/.templates/adr/adr-template.md` |

---

*Backend Architect Profile v1.0.0 — CoreMusic Agent Registry*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
