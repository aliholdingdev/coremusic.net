---
type: decision
id: "077"
title: "ADR-077: Studio Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, studio, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-077: Studio Database Schema

---

## 1. Executive Summary

Stüdyo veritabanı, stüdyo oturumlarını, parçaları, preset'leri ve ekipman bilgilerini yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | studio_sessions | Stüdyo oturumları |
| 2 | studio_tracks | Kayıt parçaları |
| 3 | studio_presets | Ses preset'leri |
| 4 | studio_equipment | Ekipman bilgileri |
| 5 | studio_templates | Şablonlar |
| 6 | studio_collaborators | İşbirlikçiler |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | Equipment compatibility | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-077: Studio Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
