---
title: "CoreMusic — shared Bağlam"
type: context
folder: "shared"
category: shared
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# shared — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/architecture/00-overview/dependency-graph.md]]

## 1. Bağlam

Tüm subdomainler bu kütüphaneye bağımlıdır (L0→L2 ortak katman). Değişiklik yayılımı en geniş klasördür: tek middleware değişikliği 9 domaini etkileyebilir. `packages/shared` paketi bu klasörün sözleşme alt kümesidir (ADR-085 hibrit geçiş).

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| src alt klasör sayısı | 24+ |
| Middleware sayısı | 10+ (HTTP) + 6 (API) |
| Domain event | 9, Integration event | 3 |
| OAuth provider | 12 |
| PageRouter dosyası | 14 |
| Test klasörleri | 4 grup (Api, Events, OAuth, Unit×4) |
| Bilinen risk | `packages/shared` ile çift kaynak; `Theme`/`ViewMode` tek dosyalık — test kapsamı dışında |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Tüketen | [[../auth.coremusic.net/CLAUDE.md]] | Middleware + Session + Security |
| Tüketen | [[../home.coremusic.net/CLAUDE.md]] | RuntimeBootstrap + Config + Session |
| Sözleşme paketi | [[../packages/shared/CLAUDE.md]] | Contract alt kümesi |
| Device CSS köprüsü | [[../assets.coremusic.net/CLAUDE.md]] | DeviceCssMap ↔ devices.config.js |

## 4. Değişiklik Protokolü

1. Katman değişikliği → ilgili `.ai/architecture/l*/` dokümanı okunur → uyum → test → audit
2. Güvenlik etkili değişiklik → security-audit workflow → `.ai/log.md` kaydı
3. Breaking contract değişikliği → ADR + packages/shared senkronu aynı iş biriminde
4. Vault etkisi varsa → vault-sync workflow çalıştırılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
