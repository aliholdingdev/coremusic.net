---
title: "ADR-033: SQL Normalization Strategy"
status: frozen
date: 2026-06-15
tags: [database, sql, normalization, bcnf, frozen]
---

# ADR-033: SQL Normalization Strategy

---

## 1. Executive Summary

CoreMusic veritabanÄ± ÅŸemalarÄ± **BCNF (Boyce-Codd Normal Form)** standardÄ±nda normalize edilir. 3NF minimum gereksinimdir, BCNF hedeftir. Her tablo aday anahtar ile tam baÄŸÄ±mlÄ± olmalÄ±dÄ±r.

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Risk Seviyesi** | high |

## 3. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | 3NF minimum | âœ… Zorunlu |
| 3 | Transitive baÄŸÄ±mlÄ±lÄ±k yasak | âŒ Yasak |
| 4 | Partial baÄŸÄ±mlÄ±lÄ±k yasak | âŒ Yasak |
| 5 | snake_case tablo/sÃ¼tun adÄ± | âœ… Zorunlu |
| 6 | Soft delete (is_deleted) | âœ… Zorunlu |
| 7 | Timestamp (created_at, updated_at) | âœ… Zorunlu |

### BCNF Kontrol Listesi

| # | Kontrol | AÃ§Ä±klama |
|---|---------|----------|
| 1 | Her tabloda PK var mÄ±? | âœ… Zorunlu |
| 2 | Non-key sÃ¼tunlar PK'ya tam baÄŸÄ±mlÄ± mÄ±? | âœ… Zorunlu |
| 3 | Aday anahtarlar baÄŸÄ±msÄ±z mÄ±? | âœ… Zorunlu |
| 4 | Transitive baÄŸÄ±mlÄ±lÄ±k yok mu? | âœ… Zorunlu |
| 5 | NULL deÄŸer minimal mi? | âš ï¸ Tercih |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-033: SQL Normalization Strategy v2.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*