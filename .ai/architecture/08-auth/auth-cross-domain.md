---
type: architecture
category: auth
title: "Enterprise Auth — Cross-Domain Authentication"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Cross-Domain Authentication

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Çoklu subdomain yapısında cross-domain authentication akışını tanımlar. Tüm subdomain'ler auth.coremusic.net'e güvenerek çalışır.

## 2. Cross-Domain Architecture

```
                 auth.coremusic.net (Identity Provider)
                         │
      ┌──────────────────┼──────────────────┐
      │                  │                  │
      ▼                  ▼                  ▼
music.coremusic.net   home.coremusic.net  studio.coremusic.net
      │                  │                  │
      ▼                  ▼                  ▼
car.coremusic.net     admin.coremusic.net  pro.coremusic.net
      │                  │                  │
      ▼                  ▼                  ▼
api.coremusic.net     media.coremusic.net  coremusic.net
      │                  │                  │
      ▼                  ▼                  ▼
      │          download.coremusic.net     │
      │                  │                  │
      └──────────────┬───┴──────────────────┘
                     ▼
             Session Validation API
                     │
                     ▼
        ┌────────────────────────┐
        │ GET /api/session/check │
        │ → valid: true/false    │
        │ → user: {id, role}     │
        │ → permissions: [...]   │
        └────────────────────────┘
```

## 3. CORS Whitelist

Tüm subdomain'ler sadece aşağıdaki listedeki domainlere istek atabilir:

| Domain | Port | Kullanım |
|--------|------|----------|
| `auth.coremusic.net` | 80/443 | Kimlik doğrulama |
| `home.coremusic.net` | 81/443 | Ev medya merkezi |
| `pro.coremusic.net` | 81/443 | Profesyonel panel |
| `studio.coremusic.net` | 81/443 | Stüdyo sistemi |
| `car.coremusic.net` | 80/443 | Araç içi |
| `admin.coremusic.net` | 80/443 | Yönetim |
| `media.coremusic.net` | 5000/6000 | Medya servisi |
| `api.coremusic.net` | 80/443 | API |
| `download.coremusic.net` | 3001 | İndirme |
| `coremusic.net` | 80/443 | Landing page |

**Kural:** Whitelist'te olmayan hiçbir domain'e CORS izni verilmez.

## 4. CORS / Origin Flow

```
Request
   │
   ▼
Origin Exists?
   │
   ├── No
   │      ▼
   │    Reject (403 Forbidden)
   │
   └── Yes
          │
          ▼
Whitelist?
          │
     ├────┴────┐
     │         │
    No        Yes
     │         │
   403      Continue
     │         │
   BLOCK    Next Middleware
```

## 5. Cookie Configuration

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| **Name** | `COREMUSIC_SESS` | Session cookie adı |
| **Domain** | `.coremusic.net` | Tüm subdomain'ler |
| **Path** | `/` | Tüm site |
| **HttpOnly** | `true` | JS erişimi yasak |
| **Secure** | `true` | HTTPS only |
| **SameSite** | `Lax` | CSRF koruması |
| **Max-Age** | `86400` | 24 saat |

## 6. Session Sharing

```
auth.coremusic.net (Session Authority)
 │
 ├── Session Create → MySQL/Redis
 │
 └── Session ID → Cookie (.coremusic.net)
      │
      ├── home.coremusic.net → validates session via auth API
      ├── studio.coremusic.net → validates session via auth API
      ├── pro.coremusic.net → validates session via auth API
      ├── car.coremusic.net → validates session via auth API
      ├── media.coremusic.net → validates session via auth API
      └── music.coremusic.net → validates session via auth API
```

**Kural:** Hiçbir subdomain kendi session'ını oluşturmaz. Tüm session'lar auth.coremusic.net tarafından yönetilir.

## 7. Auth Redirect Flow

```
Kullanıcı → herhangi bir subdomain'e erişir (login olmadan)
    │
    ▼
Subdomain → Session cookie kontrolü
    │
    ├── Cookie yok → auth.coremusic.net/login'e redirect
    │                  │
    │                  ▼
    │              Login formu
    │                  │
    │                  ▼
    │              Credentials doğrula
    │                  │
    │                  ▼
    │              Session oluştur
    │                  │
    │                  ▼
    │              Cookie set (.coremusic.net)
    │                  │
    │                  ▼
    │              Orijin subdomain'e redirect
    │
    └── Cookie var → auth.coremusic.net/api/session/check
                       │
                       ├── Geçerli → Dashboard göster
                       │
                       └── Geçersiz → Login'e redirect
```

## 8. Security Considerations

| KorumA | Yöntem |
|--------|--------|
| **CSRF** | SameSite=Lax cookie |
| **XSS** | HttpOnly cookie |
| **Session Hijacking** | IP + User-Agent binding |
| **Session Fixation** | Regenerate after login |
| **MITM** | HTTPS zorunlu |
| **CORS Bypass** | Whitelist origin check |

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Subdomains | 10 |
| CORS Rules | 10 domain |
| Security Features | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
