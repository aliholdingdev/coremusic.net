---
title: "CoreMusic — .ai/architecture Bağlam"
type: context
folder: ".ai/architecture"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .ai/architecture — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../brain.md]]

## 1. Bağlam

Boot protocol sonrası mimari soruların birincil referansı. 7 Service Platform (ADR-039) ve Electronics katmanı (ADR-061-064) bu ağaçta detaylanır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Alt klasör | 17 (+ports/protocols/roles gibi 3 mikro klasör) |
| En yoğun | 03-contracts (36 dosya), 07-security (14), l3-presentation (13), ai (13) |
| index'li klasörler | 00-overview yok, 02-deployment/03-contracts benzeri bazıları index'li |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Vault kökü |
| Karar kaynağı | [[../decisions/]] | ADR'ler |
| Kod karşılığı | [[../../shared/AGENTS.md]] | L0-L2 implementasyonu |
| Presentation kodu | [[../../assets.coremusic.net/AGENTS.md]] | L3 implementasyonu |

## 4. Değişiklik Protokolü

1. Mimari değişiklik → ADR önce → doküman sonra
2. Index'li klasörlerde index güncellemesi zorunlu
3. Audit: `[[../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
