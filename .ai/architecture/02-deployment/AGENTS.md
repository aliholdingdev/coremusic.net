---
title: "CoreMusic — .ai/architecture/02-deployment Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/02-deployment"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/02-deployment — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç

Dağıtım mimarisi: 5 deployment modu (Home/Car/Studio/NAS/DAC), CI/CD pipeline, container stratejisi, observability hedefleri (uptime %99.9, TTFB <200ms).

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Klasör dizini |
| `deployment-architecture.md` | Deployment modları + pipeline + monitoring master |
| `deployment.md` | Dağıtım süreci detayı |
| `ci-cd-pipeline.md` | Code→Lint→Test→Security→Build→Deploy→Health zinciri |
| `observability.md` | Metrikler ve izleme |

## 3. Agent Kuralları

### Zorunlu
1. Yeni pipeline aşaması → ci-cd-pipeline.md + `.github/` karşılığı birlikte
2. Container ekleme → mevcut image standardı (node:lts, php:8.4-apache, mysql:9, redis:alpine)

### Yasak
1. Health check hedeflerini (uptime/TTFB) onaysız gevşetmek (ADR-006)
2. Pipeline secret'ını dokümante etmek

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Workflow | [[../../.workflows/deployment.md]] |
| GitHub config | [[../../../.github/AGENTS.md]] |
| ADR-006 | [[../../decisions/accepted/ADR-006-performance-targets.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
