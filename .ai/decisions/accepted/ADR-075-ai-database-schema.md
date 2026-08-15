---
type: decision
id: "075"
title: "ADR-075: AI Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, ai, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-075: AI Database Schema

---

## 1. Executive Summary

AI veritabanı, kullanıcı tercih profillerini, dinleme özelliklerini, önerileri ve model verilerini yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | ai_user_preferences | Kullanıcı tercih profilleri |
| 2 | ai_listening_features | Dinleme özelliği çıkarımları |
| 3 | ai_recommendations | Öneri sonuçları |
| 4 | ai_models | ML model bilgileri |
| 5 | ai_training_data | Eğitim verileri |
| 6 | ai_feedback | Kullanıcı geri bildirimi |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | Feature vector storage | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-075: AI Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
