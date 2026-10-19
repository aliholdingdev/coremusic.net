---
title: "CoreMusic — shared/src/OAuth Bağlam"
type: context
folder: "shared/src/OAuth"
category: layer1-security
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# OAuth — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k6-guvenlik]]

---

## 1. Bağlam

OAuth sağlayıcı entegrasyonu. 12 sosyal medya sağlayıcısı ile token yönetimi, kullanıcı eşleştirme ve cinsiyet bazlı OAuth (ADR-088).

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 2 PHP dosyası (OAuth/, OAuth/Provider/) |
| Provider sayısı | 12 |
| ADR | ADR-088 (Gender-Based Social OAuth) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `OAuthManager.php` | Merkezi OAuth yöneticisi |
| `Provider/*.php` | 12 provider (Base + Discord, Facebook, Instagram, LinkedIn, Pinterest, Reddit, Snapchat, TikTok...) |

---

## 3. OAuth Provider Listesi

| # | Provider | Cinsiyet Desteği |
|---|----------|-----------------|
| 1 | Discord | Yok |
| 2 | Facebook | Var |
| 3 | Instagram | Var |
| 4 | LinkedIn | Yok |
| 5 | Pinterest | Kadın ağırlıklı |
| 6 | Reddit | Yok |
| 7 | Snapchat | Gençdemografi |
| 8 | TikTok | Gençdemografi |
| 9-12 | Diğer | Değişken |

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../../auth.coremusic.net/CLAUDE.md]] | Auth OAuth akışı |
| Referans | [[../../.ai/architecture/k6-guvenlik]] | Güvenlik mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-088-gender-based-social-oauth]] | OAuth |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
