---
title: "CoreMusic — prompt Bağlam"
type: context
folder: "prompt"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# prompt — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/archives/]]

## 1. Bağlam

İki planlama promptu barındırır; ikisi de vault'a (ADR + architecture klasörleri) bilgi aktarmış plan girdileridir. Canlı akış değildir — referans amaçlıdır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 2 |
| Durum | API planı → ADR-084 uygulanmış; elektronik vault planı → `.ai/electronic/` doluluk durumuyla eşleşmiş görünüyor |
| Tarih arşivi | `.ai/archives/` prompt0-3 versiyonları (2026-08-15, 2026-09-01) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Tarihsel arşiv | [[../.ai/archives/]] | Versiyonlu prompt geçmişi |
| Hedef ADR'ler | [[../.ai/decisions/index.md]] | Plan çıktılarının karar kayıtları |

## 4. Değişiklik Protokolü

1. Yeni prompt ekleme → ADR-036 formatı + adlandırma pattern'i
2. Mevcut prompt güncelleme → tarih + durum notu, eski içerik silinmez
3. Audit `[[../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
