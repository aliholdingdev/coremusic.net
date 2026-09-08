---
title: "CoreMusic — .ai/architecture/sql Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/sql"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# sql — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]]

## 1. Amaç
İlk şema + seed SQL (001-initial-schema.sql, 002-seed-data.sql). Tarihsel başlangıç kaydı; güncel şemalar `.ai/.sql/mysql/`'de.

## 2. İçerik Envanteri
`001-initial-schema.sql` · `002-seed-data.sql`

## 3. Agent Kuralları
1. Bu klasör tarihseldir; şema değişikliği `.ai/.sql/mysql/` + migration'a gider
2. Seed verisinde gerçek kullanıcı/credential olmaz

## 5. İlgili Kaynaklar
[[../../.sql/mysql/AGENTS.md]] *(üretilecek)* · [[../05-data/AGENTS.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
