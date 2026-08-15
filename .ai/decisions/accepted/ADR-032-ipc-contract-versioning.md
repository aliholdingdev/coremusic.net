---
type: decision
id: "032"
title: "ADR-032: IPC Contract Versioning"
category: "architecture"
status: "frozen"
date: "2026-06-10"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, ipc, versioning, contract, frozen]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-039-7-service-platform-architecture]]"
---

# ADR-032: IPC Contract Versioning

---

## 1. Executive Summary

CoreMusic servisleri arası iletişim (IPC) versiyonlu sözleşmeler ile yönetilir. Her API sözleşmesi versiyon numarası taşır.

## 2. Decision

### Versiyon Formatı

```
/api/v{major}/{resource}
```

| # | Kural | Durum |
|---|-------|-------|
| 1 | URL-based versioning | ✅ Zorunlu |
| 2 | Major version zorunlu | ✅ Zorunlu |
| 3 | Breaking change = major bump | ✅ Zorunlu |
| 4 | Backward compatibility | ✅ Zorunlu |
| 5 | Deprecation header | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-032: IPC Contract Versioning v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
