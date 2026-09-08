---
title: "CoreMusic — .ai/decisions/accepted Bağlam"
type: context
folder: ".ai/decisions/accepted"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .ai/decisions/accepted — CLAUDE.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

## 1. Bağlam

Kod değişikliği sırasında atıf yapılan ADR'lerin tam metinleri. Agent'lar "ADR-XXX" gördüğünde buradaki dosyayı açar.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 68 (66 ADR + olası ek kayıt) |
| Frozen | 37 (001-037) |
| Active | 29 (038-087) |
| Atlanan numara | 051-060 aralığı index'te boş görünüyor (VERIFICATION REQUIRED) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Karar merkezi |
| Red karşılığı | [[../rejected/index.md]] | Karşıt kararlar |
| Özet | [[../../brain.md]] | Boot protocol kaynağı |

## 4. Değişiklik Protokolü

1. Yeni ADR kabulü → draft'tan taşınır → index + brain + log güncellemesi
2. Süpersede → eski ADR başlığına not, yeni ADR referansı
3. Audit: `[[../../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
