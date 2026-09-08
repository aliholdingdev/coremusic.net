---
title: "CoreMusic — .github Agent Talimatları"
type: agent-registry
folder: ".github"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .github — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç

GitHub platform konfigürasyonu: issue şablonları ve (gelecekte) workflows. CI/CD pipeline tanımları [[../.ai/architecture/02-deployment/ci-cd-pipeline.md]] ile uyumlu tutulur.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `ISSUE_TEMPLATE/01-bug-report.md` | Hata bildirim şablonu |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| DevOps Engineer | Yeni workflow dosyaları eklerken `.ai/architecture/02-deployment/ci-cd-pipeline.md` ile eşleştirir; `.ai/.templates/infrastructure/github-actions-template.md` şablonunu kullanır |
| QA Engineer | Issue şablonunun test raporlama ihtiyaçlarını karşıladığını denetler |

## 4. Kurallar

### Zorunlu
1. Yeni GitHub Actions workflow'u, vault'taki github-actions-template.md'den türetilir (Template Mandatory guardrail)
2. Secret'lar yalnızca GitHub Actions secrets mekanizmasıyla; repo içine secret yazmak yasak (Guardrail: Secret Yok)
3. Issue şablonları Türkçe + İngilizce teknik terminoloji ile yazılır

### Yasak
1. Repo içine `.env` veya credential commit etmek
2. `ISSUE_TEMPLATE` dosyalarını onaysız silmek
3. CI pipeline'ı vault'taki deployment mimarisinden bağımsız tasarlamak

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Deployment mimarisi | [[../.ai/architecture/02-deployment/deployment-architecture.md]] |
| CI/CD pipeline | [[../.ai/architecture/02-deployment/ci-cd-pipeline.md]] |
| Şablon | [[../.ai/.templates/infrastructure/github-actions-template.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
