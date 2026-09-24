---
title: "CoreMusic — K13 CI/CD CLAUDE.md"
type: layer-guide
folder: "architecture/k13-cicd"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K13 CI/CD — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Deploy öncesi onay zorunlu | Hard Gate |
| 2 | Test coverage ≥80% | Kalite düşüşü |
| 3 | Security scan zorunlu | Güvenlik açığı |
| 4 | Rollback planı hazır | Kurtarma zor |

## 2. Pipeline Aşamaları

```
Code Push → Lint → Test → Security → Build → Deploy → Verify
```

## 3. Deployment Stratejileri

| Strateji | Kullanım | Risk |
|----------|---------|------|
| Blue/Green | Major release | Düşük |
| Canary | Feature rollout | Orta |
| Rolling | Bug fix | Düşük |

---

*K13 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
