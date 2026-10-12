---
title: "CoreMusic — .github Bağlam"
type: context
folder: ".github"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .github — CLAUDE.md

**Zorunlu Bağlantılar:** · [[../AGENTS.md]]

## 1. Bağlam

Bu klasör GitHub platformunun repo-düzeyi davranışını tanımlar. Halen minimal (1 issue şablonu); CI/CD tanımları eklenirse DevOps Engineer sorumluluğundadır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Workflow (Actions) | Yok — CI/CD tanımı vault dokümantasyon aşamasında |
| Issue şablonu | 1 (bug report) |
| Secret yönetimi | GitHub Actions secrets (repo dışı) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Deployment mimarisi | [[../.ai/architecture/02-deployment/index.md]] | CI/CD karar kaynağı |
| DevOps profili | [[../.ai/.agents/devops-engineer.md]] | Sorumlu agent |
| Şablon kaynağı | [[../.ai/.templates/infrastructure/github-actions-template.md]] | Template Mandatory |

## 4. Değişiklik Protokolü

1. Yeni workflow ekleme → DevOps Engineer taslağı → kullanıcı onayı → uygulama
2. Workflow secret referansları vault'a yazılmaz; GitHub settings'te tanımlanır
3. Audit kaydı `[[../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
