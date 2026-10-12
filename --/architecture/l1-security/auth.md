---
title: "l1-security/auth — Eski L1 Auth Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-l1-security
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# auth — L1 Auth (stub)

**Durum:** `architecture/l1-security/auth` **diskte YOK** (eski ağaç silindi). `l1-security/` dizini de yeni oluşturuldu (link hedefi).

## Bugünkü Karşılığı (gerçek kanıt)

| Bileşen | Dosya | Karar |
|---------|-------|-------|
| Merkezi auth servisi | `auth.coremusic.net/` (index.php, include/, pages/, handler/) | ADR-043 · ADR-058 |
| Hybrid session + JWT | `shared/src/Middleware/AuthMiddleware.php` | ADR-052 |
| RS256 JWT + revocation | `shared/src/Security/JwtService.php` | ADR-095 |
| Session yaşam döngüsü | `shared/src/Session/` (4 sınıf) | ADR-011 |
| RBAC (Permission) | `shared/src/Middleware/PermissionMiddleware.php` | `.claude/CLAUDE.md` §6 |
| Login/Logout/Şifre sayfaları | `auth.coremusic.net/pages/` | IMPLEMENTED |
| OAuth | `auth.coremusic.net/handler/OAuthPostHandler.php` · `shared/src/OAuth/` | IMPLEMENTED |

**Domain tablosu:** [[architecture/14-domain-d05-guvenlik-middleware]] (K207–K208, K211–K214, K223)
