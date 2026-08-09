---
type: workflow
category: deployment
title: "Deployment Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Deployment Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Production deployment'ının planlanmasını, uygulanmasını ve doğrulanmasını sağlayan iş akışı.

## 2. Adımlar

| # | Adım | Aksiyon | Sorumlu |
|---|------|---------|---------|
| 1 | Pre-flight | Deployment checklist | [[devops-engineer]] |
| 2 | Test | Tüm testlerin geçtiğini doğrula | [[qa-engineer]] |
| 3 | Vault-sync | Vault'u güncelle | [[master-orchestrator]] |
| 4 | Onay | Kullanıcı onayını al (Hard Gate) | Product Owner |
| 5 | Deploy | Deployment'ı başlat | [[devops-engineer]] |
| 6 | Health Check | Health check'leri kontrol et | [[devops-engineer]] |
| 7 | Validation | Post-deployment doğrulama | [[devops-engineer]] |
| 8 | Loglama | Tamamlanma kaydı | [[devops-engineer]] |

## 3. Pre-flight Checklist

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | Tüm testler geçti mi? | ☐ |
| 2 | Vault-sync yapıldı mı? | ☐ |
| 3 | Kullanıcı onayı alındı mı? | ☐ |
| 4 | Database backup alındı mı? | ☐ |
| 5 | Rollback planı hazır mı? | ☐ |

## 4. Deployment Stratejileri

| Strateji | Açıklama |
|----------|----------|
| **Blue-Green** | İki paralel ortam |
| **Canary** | Kademeli deployment |
| **Rolling** | Kademeli güncelleme |

## 5. Health Check Endpoint

```json
{
  "status": "healthy",
  "timestamp": 1691500000,
  "version": "1.0.0",
  "services": {
    "database": "healthy",
    "cache": "healthy",
    "storage": "healthy"
  }
}
```

## 6. Rollback Stratejisi

| Senaryo | Aksiyon |
|---------|---------|
| Health check başarısız | Otomatik rollback |
| Test başarısız | Manuel rollback |
| Performance düşüşü | Alert + rollback |

## 7. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| Health Check | Başarılı |
| Test Coverage | ≥%80 |
| Rollback | Hazır |
| Downtime | <5 dakika |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
