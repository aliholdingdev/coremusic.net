---
title: "CoreMusic — auth.coremusic.net Bağlam"
type: context
folder: "auth.coremusic.net"
category: domain
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# auth.coremusic.net — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/architecture/08-auth/index.md]]

## 1. Bağlam

Tek giriş noktası: tüm subdomainler kimlik doğrulamayı bu servise delege eder (ADR-043 consolidation). `home.coremusic.net` `HomeAuthBridge` üzerinden bağlanır. Oturum durumu ADR-011'e göre taşınır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Katman mimarisi | Hexagonal (Controller/Handler → Service → Repository ↔ Domain) |
| Middleware sayısı | 5 aktif + interface |
| Sayfa sayısı | 7 (login, register, forgot/reset password, gender, logout) |
| Test kapsamı | ValueObject + DTO (Email, Gender, Password, User, LoginRequest) |
| Bilinen risk | Test kapsamı Service/Handler katmanını kapsamıyor |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Tüketen | [[../home.coremusic.net/CLAUDE.md]] | HomeAuthBridge + auth_callback.php akışı |
| Asset sağlanan | [[../assets.coremusic.net/CLAUDE.md]] | login/register CSS (d-auth-*) ve JS (js/auth/*) |
| Paylaşılan altyapı | [[../shared/CLAUDE.md]] | Middleware, Session, Security bileşenleri |
| Vault referansı | [[../.ai/subdomains/auth.coremusic.net/index.md]] | Subdomain vault kaydı |
| DB | `coremusic_auth` | [[../.ai/.sql/mysql/coremusic_auth.sql]] |

## 4. Değişiklik Protokolü

1. Auth akışı değişikliği → önce `[[../.ai/architecture/08-auth/auth-flow.md]]` okunur → uyumsuzsa DUR + ADR önerisi
2. Yeni endpoint → DTO + Service + Handler + test birlikte
3. Güvenlik etkili her değişiklik → security-audit workflow'u tetiklenir
4. Audit `[[../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
