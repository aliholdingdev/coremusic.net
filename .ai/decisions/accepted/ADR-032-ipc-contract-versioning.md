---
title: "ADR-032: IPC Contract Versioning"
status: frozen
date: 2026-06-10
tags: [architecture, ipc, versioning, contract, frozen]
---

# ADR-032: IPC Contract Versioning

---

## 1. Executive Summary

CoreMusic servisleri arasÄ± iletiÅŸim (IPC) versiyonlu sÃ¶zleÅŸmeler ile yÃ¶netilir. Her API sÃ¶zleÅŸmesi versiyon numarasÄ± taÅŸÄ±r.

## 2. Decision

### Versiyon FormatÄ±

```
/api/v{major}/{resource}
```

| # | Kural | Durum |
|---|-------|-------|
| 1 | URL-based versioning | âœ… Zorunlu |
| 2 | Major version zorunlu | âœ… Zorunlu |
| 3 | Breaking change = major bump | âœ… Zorunlu |
| 4 | Backward compatibility | âœ… Zorunlu |
| 5 | Deprecation header | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-032: IPC Contract Versioning v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*