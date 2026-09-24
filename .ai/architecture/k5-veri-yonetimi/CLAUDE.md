---
title: "CoreMusic — K5 Veri Yönetimi CLAUDE.md"
type: layer-guide
folder: "architecture/k5-veri-yonetimi"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K5 Veri Yönetimi — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | BCNF zorunlu (ADR-040) | Normalizasyon hatası |
| 2 | SELECT * yasak | SQL injection |
| 3 | ORM yasak (ADR-002) | Bağımlılık |
| 4 | Soft delete zorunlu | Veri kaybı |
| 5 | Prepared statement zorunlu | SQL injection |

## 2. Yasaklı SQL

```sql
-- ❌ YASAK
SELECT * FROM users WHERE id = 1;
SELECT * FROM users;

-- ✅ DOĞRU
SELECT id, name, email FROM users WHERE id = :id AND is_deleted = 0;
SELECT id, name FROM users WHERE is_deleted = 0 LIMIT :limit OFFSET :offset;
```

## 3. BCNF Kontrol Listesi

| Kontrol | Durum |
|---------|-------|
| Primary key var mı? | ✅/❌ |
| Functional dependency uyumlu mu? | ✅/❌ |
| Soft delete var mı? | ✅/❌ |
| Timestamp'ler var mı? | ✅/❌ |
| Snake case mi? | ✅/❌ |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-002 | PDO mandatory, ORM yasak |
| ADR-040 | 18 BCNF veritabanı otoritesi |

---

*K5 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
