---
type: decision
id: "049"
title: "ADR-049: Startup Prompt Loader"
category: "ai"
status: "active"
date: "2026-08-08"
updated: "2026-08-15"
authority: "Master Orchestrator"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [ai, prompt, startup, loader, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
---

# ADR-049: Startup Prompt Loader

---

## 1. Executive Summary

CoreMusic'te her AI oturum başlangıcında **4 ana prompt** (prompt0-3) sırasıyla yüklenir. Prompt'lar vault'a işlenmiştir ve referans olarak kullanılır.

## 2. Decision

### Prompt Sırası

| Sıra | Prompt | Dosya | Max Süre |
|------|--------|-------|----------|
| 1 | prompt0 (Genel Ana) | archives/prompt0-genel-ana-prompt | 5s |
| 2 | prompt1 (SPA Router) | archives/prompt1-spa-router | 3s |
| 3 | prompt2 (Auth) | archives/prompt2-auth | 3s |
| 4 | prompt3 (API) | archives/prompt3-api | 3s |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 4 prompt sıralı yükleme | ✅ Zorunlu |
| 2 | Max 14s toplam süre | ✅ Zorunlu |
| 3 | Vault'a işlenmiş prompt | ✅ Zorunlu |
| 4 | Domain bazlı prompt seçimi | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-049: Startup Prompt Loader v2.0.0 — CoreMusic AI*
*Authority: Master Orchestrator · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
