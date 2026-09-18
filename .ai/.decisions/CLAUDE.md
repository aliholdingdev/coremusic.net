---
title: "CoreMusic — .ai/decisions Bağlam"
type: context
folder: ".ai/decisions"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .ai/decisions — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../brain.md]]

## 1. Bağlam

Boot protocol 6. adımındaki `brain.md`'nin ayrıntılı kayıt deposu. Agent karar verirken önce `index.md` → ilgili ADR → brain özeti sırasını izler.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Accepted | 66 (ADR-001→087, bazı numaralar atlanmış: 51-60 arası boşluk index'te görünüyor) |
| Rejected | 12 (R-001→012) |
| Frozen/Active bölgesi | 001-037 frozen; 038-087 active |
| Draft | klasör mevcut, doluluk doğrulanmadı |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Vault kökü |
| Özet | [[../brain.md]] | ADR 001-087 özet tablosu |
| Yaşam döngüsü | [[../architecture/04-decisions/adr-lifecycle.md]] | Süreç detayı |
| Tüketen | [[../../shared/AGENTS.md]] | Kod tarafı ADR atıfları |

## 4. Değişiklik Protokolü

1. ADR eklenince → index.md + brain.md özeti + log.md üçlü senkron
2. Red edilen karar → `rejected/` + red nedeni + karşı ADR referansı
3. Audit append-only: `[[../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
