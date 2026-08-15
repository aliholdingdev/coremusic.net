---
type: agent-profile
category: agent
title: "CoreMusic — Data Engineer Profile"
date: 2026-08-09
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Data Engineer Profile

**SSOT:** [[AGENTS.md]] · [[.agents/AGENTS.md]]

---

## 1. Genel Bakış

| Özellik | Değer |
|---------|-------|
| Kod Adı | `data` |
| Katman | L0 (Infrastructure) |
| Domain | MySQL 18 BCNF, PDO, migration |
| Teknoloji | MySQL 9, PDO, BCNF |

## 2. Sorumluluklar

- 18 BCNF DB yönetimi
- Schema tasarımı
- Migration süreçleri
- Query optimization

## 3. Dosya Erişimi

| Erişim | Kapsam |
|--------|--------|
| Okuma/Yazma | `*.sql`, `*.php` (migration), `*.json` (schema) |

## 4. Zorunlu Kurallar

- ORM yasak (ADR-002)
- SELECT * yasak
- BCNF zorunlu
- Prepared statement zorunlu

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Ana tanım | [[AGENTS.md]] §15.5 |
| Profiller indeksi | [[.agents/AGENTS.md]] |
| ADR Database | `.ai/.templates/adr/adr-database-template.md` |
| Query Template | `.ai/.templates/query/Query-Template.md` |
| Migration | `.ai/.templates/infrastructure/migration-template.md` |
| Skill | `.opencode/skills/database-normalize-maker/SKILL.md` |

---

*Data Engineer Profile v1.0.0 — CoreMusic Agent Registry*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
