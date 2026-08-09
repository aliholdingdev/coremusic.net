---
type: workflow
category: code-review
title: "Code Review Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Code Review Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Kod değişikliklerinin çok eksenli olarak incelenmesini ve kalite güvencesi sağlanmasını sağlayan iş akışı.

## 2. Adımlar

| # | Adım | Aksiyon | Sorumlu |
|---|------|---------|---------|
| 1 | Değişiklik Listesi | `git diff` ile değişiklikleri al | İnceleyen |
| 2 | Etkilenen Dosyalar | Max 15 dosya (MSA limiti) | İnceleyen |
| 3 | ADR Kontrolü | İlgili ADR'leri kontrol et | İnceleyen |
| 4 | Kod Standartları | PSR-12, BEM, ITCSS uyumluluğu | İnceleyen |
| 5 | Güvenlik | OWASP, CSRF, CSP kontrolleri | [[security-engineer]] |
| 6 | Test Coverage | Min %80 coverage | [[qa-engineer]] |
| 7 | Cross-reference | Wiki-link doğrulama | İnceleyen |
| 8 | Rapor | İnceleme raporu oluştur | İnceleyen |

## 3. Kontrol Matrisi

| Eksen | Kontrol | Kriter |
|-------|---------|--------|
| **Doğruluk** | Kod istenen sonucu üretiyor mu? | Test geçmişi |
| **Güvenlik** | Güvenlik açığı var mı? | OWASP kontrol |
| **Performans** | Performans düşüşü var mı? | Benchmark |
| **Bakım** | Kod okunabilir ve sürdürülebilir mi? | PSR-12 |
| **Uyumluluk** | ADR'lere uygun mu? | ADR kontrol |

## 4. Onay Akışı

```text
İnceleme Başlat → Değişiklikleri Kontrol Et → Rapor Oluştur
    ↓                                              ↓
Onay Ver → Merge ← Onay Al ← Düzeltmeleri Uygula
```

## 5. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| Test Coverage | ≥%80 |
| Güvenlik Açığı | 0 |
| ADR Uyumluluğu | %100 |
| Kod Standartları | PSR-12/BEM/ITCSS |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
