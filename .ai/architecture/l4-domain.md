---
title: "l4-domain — Eski L4 Domain Katmanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-layers
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# l4-domain — L4 Domain (Alan) Katmanı (stub)

**Durum:** `architecture/l4-domain(.md)` **diskte YOK** (eski ağaç silindi) — 4 link referansı
(eski 4/7-katmanlı PDF mimarisinin "Alan (Domain)" katmanı — `.ai/CLAUDE.md` §32).

## Bugünkü Karşılığı (gerçek kanıt)

| Domain katmanı parçası | Dosya | Durum |
|------------------------|-------|-------|
| Contracts (AI/Api/Auth/Config/Database/Events/Middleware/Security) | `shared/src/Contracts/` (8 alt dizin) | IMPLEMENTED |
| Domain event'ler | `shared/src/Events/Domain/` (9 dosya) | IMPLEMENTED |
| Repository (domain erişimi) | `shared/src/Repository/` | IMPLEMENTED |
| Domain-specific servis sınıfları | `auth.coremusic.net/include/Domain/` · `include/Repository/` | IMPLEMENTED |

**Modern karşılık:** K-mimarisinde domain katmanı, 10-domain tablosuyla temsil edilir →
[[architecture/00-master-index]] §2 · örn. d06 [[architecture/15-domain-d06-servis-api]].
