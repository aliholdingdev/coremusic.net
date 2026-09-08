---
title: "CoreMusic — .ai/architecture/05-data Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/05-data"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/05-data — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç

Veri katmanı dokümantasyonu: 18 BCNF veritabanı yönetimi (ADR-040), normalizasyon kuralları, migration stratejisi, repository pattern.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Klasör dizini |
| `database_master.md` | Veritabanı master haritası |
| `bcnf-normalization.md` | BCNF kuralları (ADR-003/040/041) |
| `migration-strategy.md` | Multi-DB migration stratejisi (ADR-014/050) |
| `repository-pattern.md` | Repository desen standardı |

## 3. Agent Kuralları

### Zorunlu
1. Yeni tablo → BCNF doğrulama (database-normalize-maker skill) + ilgili `.sql` dosyası senkronu
2. Migration → ADR-014 stratejisi + migration-template

### Yasak
1. BCNF ihlali bilinçli ekleme (gerekçe → yeni ADR)
2. ORM (ADR-002)
3. Tek-DB birleştirme önerisi (R-009 reddi yürürlükte)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| SQL dosyaları | [[../../.sql/mysql/]] (18 DB) |
| ADR-040 | [[../../decisions/accepted/ADR-040-database-authority.md]] |
| Kod | [[../../../shared/src/Database/]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
