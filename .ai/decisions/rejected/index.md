---
type: index
category: decisions-rejected
title: "CoreMusic — Rejected ADR Index"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total: 12
---

# CoreMusic — Rejected ADR Index

## Reddedilen ADR'ler (12)

| # | ADR | Başlık | Kategori | Red Nedeni | Reddeden ADR |
|---|-----|--------|----------|------------|--------------|
| 1 | R-001 | Redux-Style State | Frontend | Framework yasağı | ADR-001 |
| 2 | R-002 | MongoDB | Database | BCNF uyumsuz | ADR-002, ADR-040 |
| 3 | R-003 | jQuery | Frontend | Framework yasağı | ADR-001 |
| 4 | R-004 | Webpack | Frontend | Over-engineering | ADR-001 |
| 5 | R-005 | REST-Only API | Architecture | WebSocket gerekli | ADR-039, ADR-084 |
| 6 | R-006 | Laravel Eloquent | Database | ORM yasak | ADR-002, ADR-001 |
| 7 | R-007 | Firebase Auth | Security | Harici bağımlılık | ADR-043 |
| 8 | R-008 | MyISAM Engine | Database | Transaction eksik | ADR-040 |
| 9 | R-009 | Single Database | Database | Güvenlik/performans | ADR-003, ADR-040 |
| 10 | R-010 | Node.js Full Stack | Architecture | PHP zorunlu | ADR-039 |
| 11 | R-011 | GraphQL API | Architecture | Over-engineering | ADR-084 |
| 12 | R-012 | Microservices | Architecture | Erken optimizasyon | ADR-039, ADR-087 |

## Kategori Bazlı Red Sayıları

| Kategori | Red Sayısı |
|----------|------------|
| Frontend | 3 |
| Database | 3 |
| Architecture | 4 |
| Security | 1 |
| **TOPLAM** | **12** |

---

*Rejected ADR Index v1.0.0 — CoreMusic Vault*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
