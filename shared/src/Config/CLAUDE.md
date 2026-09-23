---
title: "CoreMusic — shared/src/Config Bağlam"
type: context
folder: "shared/src/Config"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Config — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k0-isletim-sistemi]]

---

## 1. Bağlam

Uygulama yapılandırma yönetimi. ConfigManager, DomainConfig, EnvParser (ADR-015) ve AuthRouteConfig bileşenleri. Tüm yapılandırma `.env` ve config dosyalarından okunur.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 4 PHP dosyası |
| ADR | ADR-015 (env parser strategy) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `ConfigManager.php` | Merkezi yapılandırma yöneticisi |
| `DomainConfig.php` | Domain bazlı yapılandırma (9 subdomain) |
| `EnvParser.php` | `.env` dosyası okuyucu (ADR-015) |
| `AuthRouteConfig.php` | Auth route yapılandırması |

---

## 3. Domain Yapılandırması

| Domain | Port | Stack |
|--------|------|-------|
| coremusic.net | 80 | Vanilla JS |
| music.coremusic.net | 81 | PHP 8.4 + JS |
| admin.coremusic.net | 80 | PHP 8.4 |
| download.coremusic.net | 3001 | Node.js + TS |
| media.coremusic.net | 5000/6000 | PHP + FFmpeg |
| auth.coremusic.net | — | PHP 8.4 |
| home.coremusic.net | 81 | Vanilla JS |
| car.coremusic.net | — | Vanilla JS |
| studio.coremusic.net | 81 | Vanilla JS |

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../Bootstrap/CLAUDE.md]] | Runtime bootstrap |
| Kullanıcı | [[../PageRouter/CLAUDE.md]] | Route yapılandırması |
| Referans | [[../../.ai/architecture/k0-isletim-sistemi]] | OS katmanı |
| ADR | [[../../.ai/decisions/accepted/ADR-015-env-parser-strategy]] | Env parser |

---

## 5. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | `.env` dosyasını vault'a yazmak | Secret sızıntısı |
| 2 | Hardcoded config | Config-based yapılandırma |
| 3 | Config'i runtime'da değiştirme | Tutarsızlık |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
