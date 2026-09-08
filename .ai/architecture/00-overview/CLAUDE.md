---
title: "CoreMusic — .ai/architecture/00-overview Bağlam"
type: context
folder: ".ai/architecture/00-overview"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/architecture/00-overview — CLAUDE.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

## 1. Bağlam

Mimari soruların giriş kapısı. Agent ilk kez mimari okurken `architecture-master.md` → `dependency-graph.md` sırasını izler.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 3 |
| Kapsam | 7 servis platformu (ADR-039), 9 subdomain |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Mimari kök |
| Kod | [[../../../shared/CLAUDE.md]] | RuntimeBootstrap |

## 4. Değişiklik Protokolü

1. Master güncelleme → ADR referans kontrolü → log
2. Audit: `[[../../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
