---
title: "CoreMusic — .ai/architecture/02-deployment Bağlam"
type: context
folder: ".ai/architecture/02-deployment"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/architecture/02-deployment — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]]

## 1. Bağlam

DevOps agent'ının ana referansı; deployment workflow'u buradaki hedeflerle doğrulanır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 5 (index dahil) |
| Hedefler | Uptime >%99.9, TTFB <200ms, Error <%1, CPU/Disk <%80 |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Mimari kök |
| Uygulama | [[../../../.workflows/deployment.md]] | Çalıştırılabilir akış |
| Health endpoint | [[../../../home.coremusic.net/CLAUDE.md]] | health.php |

## 4. Değişiklik Protokolü

1. Hedef değişikliği → ADR-006 revizyonu önce
2. Audit: `[[../../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
