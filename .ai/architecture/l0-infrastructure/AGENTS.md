---
title: "CoreMusic — .ai/architecture/l0-infrastructure Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/l0-infrastructure"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# l0-infrastructure — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
L0 altyapı katmanı: cache, credential vault, database, filesystem soyutlamaları.

## 2. İçerik Envanteri
`index.md` + `cache.md` + `credential-vault.md` + `database.md` + `filesystem.md`

## 3. Agent Kuralları
1. Cache anahtarları ADR-007 namespace standardına uyar
2. Credential vault içeriği dokümante edilmez (ADR-034)
3. Kod karşılığı: `shared/src/Cache/` + `shared/src/Database/`

## 5. İlgili Kaynaklar
[[../../../shared/src/Cache/]] · [[../../../decisions/accepted/ADR-007-cache-namespace.md]] · [[../../../decisions/accepted/ADR-034-credential-vault-normalization.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
