---
type: architecture
category: auth
title: "Enterprise Auth — Cross-Domain Authentication"
date: 2026-08-09
updated: 2026-08-12
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Cross-Domain Authentication

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Çoklu subdomain yapısında cross-domain authentication akışını tanımlar. Tüm subdomain'ler auth.coremusic.net'e güvenerek çalışır.

## 1.1 Zorunlu Merkezi Auth Kuralı

**Hiçbir subdomain kendi başına bağımsız bir kimlik doğrulama sistemi (login) çalıştırmaz.**

Tüm subdomain'ler (music, admin, api, media, home, pro, studio, car, download, coremusic.net) **zorunlu olarak** auth.coremusic.net'i kullanır.

*Kaynak: [[ADR-043-auth-subdomain-consolidation]]*

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

| Domain | Port (Dev) | Port (Prod) | Protokol (Dev) | Protokol (Prod) | Kullanım |
|--------|-----------|-------------|----------------|-----------------|----------|
| `auth.coremusic.net` | 81 | 80/443 | HTTP | HTTP/HTTPS | Kimlik doğrulama |
| `home.coremusic.net` | 81 | 80/443 | HTTP | HTTP/HTTPS | Ev medya merkezi (RPi5) |
| `pro.coremusic.net` | 81 | 80/443 | HTTP | HTTP/HTTPS | Profesyonel panel (RPi5) |
| `studio.coremusic.net` | 81 | 80/443 | HTTP | HTTP/HTTPS | Stüdyo sistemi (RPi5) |
| `car.coremusic.net` | 80 | 80/443 | HTTP | HTTP/HTTPS | Araç içi |
| `admin.coremusic.net` | 80 | 80/443 | HTTP | HTTP/HTTPS | Yönetim |
| `media.coremusic.net` | 5000/6000 | 5000/6000 | HTTP | HTTP/HTTPS | Medya servisi |
| `api.coremusic.net` | 81 | 80/443 | HTTP | HTTP/HTTPS | API |
| `download.coremusic.net` | 3001 | 3001 | HTTP | HTTP/WS | İndirme |
| `music.coremusic.net` | 81 | 80/443 | HTTP | HTTP/HTTPS | Ana medya paneli |
| `coremusic.net` | 80 | 80/443 | HTTP | HTTPS | Landing page |

**Desteklenen Portlar:** 80, 81, 443, 4433

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
| Version | 2.0.0 |
| Subdomains | 11 |
| CORS Rules | 11 domain |
| Security Features | 6 |
| Ports | 80, 81, 443, 4433 |
| Protocols | HTTP (dev), HTTPS (prod) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
