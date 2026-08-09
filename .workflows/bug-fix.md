---
type: workflow
category: bug-fix
title: "Bug Fix Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Bug Fix Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Hataların kök neden analizini, düzeltilmesini ve regresyon testlerini sağlayan iş akışı.

## 2. Adımlar

| # | Adım | Aksiyon | Sorumlu |
|---|------|---------|---------|
| 1 | Kök Neden | Hata tanımını analiz et | Sorumlu ajan |
| 2 | Etkilenen Dosyalar | Hatanın etkilediği dosyaları tespit et | Sorumlu ajan |
| 3 | ADR Kontrolü | İlgili ADR'leri kontrol et | Sorumlu ajan |
| 4 | Düzeltme | Düzeltmeyi uygula | Sorumlu ajan |
| 5 | Test | Testleri çalıştır | [[qa-engineer]] |
| 6 | Regresyon | Regresyon testi yap | [[qa-engineer]] |
| 7 | Dokümantasyon | Dokümantasyonu güncelle | Sorumlu ajan |
| 8 | Loglama | log.md'ye kaydet | Sorumlu ajan |

## 3. Öncelik Seviyeleri

| Öncelik | Tanım | Süre |
|---------|-------|------|
| **CRITICAL** | Sistem durması, veri kaybı | 1 saat |
| **HIGH** | Kritik işlev kaybı | 4 saat |
| **MEDIUM** | Normal hata | 1 gün |
| **LOW** | Kosmetik hata | 1 hafta |

## 4. Kök Neden Analizi

| Teknik | Açıklama |
|--------|----------|
| **5 Whys** | 5 kez "Neden?" sorusu |
| **Fishbone** | Ishikawa diyagramı |
| **Binary Search** | Kodun yarısını eleyerek bulma |
| **Log Analysis** | Log dosyalarını analiz etme |

## 5. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| Kök Neden | Tespit edildi |
| Düzeltme | Uygulandı |
| Regresyon | Başarılı |
| Test Coverage | ≥%80 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
