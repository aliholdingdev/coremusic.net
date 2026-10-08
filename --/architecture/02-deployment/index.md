---
title: "02-deployment index — Eski Deployment Klasörü (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-deployment
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 02-deployment index — Deployment (stub)

**Durum:** `architecture/02-deployment(/index.md)` **diskte YOK** (eski ağaç silindi) — 5 link referansı.

## Bugünkü Karşılığı (gerçek kanıt)

| Alan | Dosya | Durum |
|------|-------|-------|
| CI (build + test) | `.github/workflows/ci.yml` | IMPLEMENTED |
| Secret scanning | `.github/workflows/secret-scan.yml` · `.gitleaks.toml` | IMPLEMENTED |
| Deployment akışı (doküman) | `.workflows/deployment.md` | PLANNED (doküman; uygulama kanıtı yok) |
| Docker / K8s | — | ⚠️ VERIFICATION REQUIRED — Dockerfile glob 0 (2026-10-07) |
| Playwright E2E bağımlılığı | `package.json` (`playwright ^1.62.1`) | PARTIAL (senaryo kanıtı yok) |

**İlgili domain:** d01 (platform/CI tarafı) + d09 → [[architecture/18-domain-d09-izleme-cicd-ag]] (K409–K415)
