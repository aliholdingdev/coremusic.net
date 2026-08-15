---
type: decision
id: "R-011"
title: "REJECTED: GraphQL API"
category: "architecture"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, architecture, graphql, api]
risk-level: "medium"
rejection-reason: "Over-engineering, REST yeterli"
rejected-by: "ADR-084"
references:
  - "[[decisions/accepted/ADR-084-api-gateway-architecture]]"
---

# REJECTED: GraphQL API

---

## 1. Executive Summary

GraphQL API kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | Over-engineering | Yüksek |
| 2 | REST yeterli | Yüksek |
| 3 | Öğrenme eğrisi | Orta |
| 4 | Cache karmaşıklığı | Orta |

## 3. Alternatif Çözüm

**Seçilen:** REST API + OpenAPI (ADR-084)
- Basit ve anlaşılır
- Geniş destek
- Kolay cache
- OpenAPI spec ile sözleşme

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
