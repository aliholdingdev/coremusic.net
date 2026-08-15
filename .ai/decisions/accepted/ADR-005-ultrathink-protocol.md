---
type: decision
id: "005"
title: "ADR-005: Ultrathink Protocol (Zero Hallucination)"
category: "architecture"
status: "frozen"
date: "2026-02-05"
updated: "2026-08-15"
authority: "Master Orchestrator"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, quality, hallucination, verification, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
---

# ADR-005: Ultrathink Protocol (Zero Hallucination)

---

## 1. Executive Summary

CoreMusic'te **Zero Hallucination** prensibi uygulanır. Doğrulanamayan bilgi `VERIFICATION REQUIRED` olarak işaretlenir. Hiçbir AI ajanı doğrulanmamış bilgiyi kesin gibi sunamaz.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Doğrulanamayan bilgi → VERIFICATION REQUIRED | ✅ Zorunlu |
| 2 | Uydurma API/endpoint yasak | ❌ Yasak |
| 3 | Uydurma versiyon yasak | ❌ Yasak |
| 4 | Uydurma CVE yasak | ❌ Yasak |
| 5 | Kaynak gösterme zorunlu | ✅ Zorunlu |
| 6 | Vault-first okuma | ✅ Zorunlu |

### Hallüsinasyon Türleri

| Tür | Örnek | Çözüm |
|-----|-------|-------|
| API Hallüsinasyonu | Var olmayan endpoint | Doğrula |
| Versiyon Hallüsinasyonu | Doğrulanmamış sürüm | Kaynak bul |
| Performans Hallüsinasyonu | Kanıtsız benchmark | Ölç |
| CVE Hallüsinasyonu | Uydurma güvenlik açığı | Doğrula |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-005: Ultrathink Protocol v2.0.0 — CoreMusic Quality*
*Authority: Master Orchestrator · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
