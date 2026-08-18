---
type: subdomain
title: "Home Subdomain — home.coremusic.net"
category: "media-center"
date: "2026-08-17"
updated: "2026-08-17"
status: "active"
version: "1.0.0"
authority: "SSOT"
references:
  - "[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]"
  - "[[architecture/l3-presentation]]"
---

# Home Subdomain — home.coremusic.net

## 1. Genel Bakış

| Alan | Değer |
|------|-------|
| Entry Point | `home.coremusic.net/index.php` |
| Port | 81 |
| Stack | PHP 8.4, Vanilla JS, ITCSS |
| Session Cookie | `COREMUSIC_SESS`, domain `.coremusic.net` |
| Ana Sayfa | Ev medya merkezi (RPi5 hedefli) |

## 2. Auth Callback Flow

```
GET /auth/callback?auth_key=XXX
  → auth_key var mı?
    ├── HAYIR → /login redirect
    └── EVET → Session başlat
              → HomeAuthBridge::validateAndCreateSession()
                → cURL POST → auth.coremusic.net/validate-key
                → Başarılı → HomeSessionManager::setAuthUser()
                → session_write_close()
              → /home redirect
              → Başarısız → /login?error=invalid_key redirect
```

## 3. Entry Point Davranışları

| URL | Davranış |
|-----|----------|
| `/health` | Health check → JSON |
| `/auth/callback` | Auth key validate + session oluşturma (kernel'den önce) |
| `/*` | PageRouterKernel → SPA Router |

## 4. Auth Bridge

`HomeAuthBridge` sınıfı cross-domain session transfer sağlar:
- `auth.coremusic.net/validate-key` endpoint'ine cURL POST
- Timeout: 5s, Max retry: 2
- auth_key 300s TTL, tek kullanımlık (30s grace window)

## 5. Session

- Cookie domain: `.coremusic.net` (auth.coremusic.net ile aynı)
- Session save path: `C:\temp` (auth.coremusic.net ile aynı)
- Session keys: `MM_UserID`, `MM_Username`, `MM_Email`, `MM_DisplayName`, `MM_AccountType`, `MM_Gender`

## 6. View Modları

| Mod | Açıklama |
|-----|----------|
| Home | Ana medya paneli |
| Pro | Profesyonel görünüm |
| Studio | Stüdyo görünümü |

---

*Home Subdomain v1.0.0 — CoreMusic Vault*
*Last Updated: 2026-08-17*
