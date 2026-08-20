---
title: "ADR-088: Gender-Based Social OAuth System"
status: active
date: 2026-08-17
tags: [security, oauth, social, gender, authentication, active]
---

# ADR-088: Gender-Based Social OAuth System

---

## 1. Executive Summary

CoreMusic'e **cinsiyet bazlÄ± sosyal medya OAuth sistemi** eklenir. KullanÄ±cÄ±nÄ±n `data-gender` attribute'una gÃ¶re (female/male/neutral) en Ã§ok kullanÄ±lan sosyal medya platformlarÄ± gÃ¶sterilir ve OAuth 2.0 ile baÄŸlanÄ±r.

### 1.1 KararÄ±n Ã–zeti

KullanÄ±cÄ± cinsiyetine gÃ¶re filtrelenmiÅŸ sosyal medya OAuth entegrasyonu. 10 platform: Pinterest, Instagram, TikTok, Snapchat, YouTube (female-å€¾å‘), Discord, Reddit, X/Twitter, LinkedIn, YouTube (male-å€¾å‘).

### 1.2 Temel GerekÃ§e

- KullanÄ±cÄ± deneyimini kiÅŸiselleÅŸtirme (ADR-044 gender theme engine ile uyumlu)
- Sosyal medya entegrasyonunu cinsiyet tercihlerine gÃ¶re optimize etme
- OAuth token gÃ¼venliÄŸini saÄŸlama (AES-256-GCM ile ÅŸifreleme)

### 1.3 Beklenen SonuÃ§lar

- Female kullanÄ±cÄ±lar: Pinterest, Instagram, TikTok, Snapchat, YouTube
- Male kullanÄ±cÄ±lar: Discord, Reddit, X/Twitter, LinkedIn, YouTube
- Neutral kullanÄ±cÄ±lar: YouTube, Facebook

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | active |
| **Versiyon** | 1.0.0 |
| **OluÅŸturma Tarihi** | 2026-08-17 |
| **Son GÃ¼ncelleme** | 2026-08-17 |
| **Otorite** | Security Engineer |
| **Onay** | Red Team Â· Human Mode Â· Truth Mode |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

CoreMusic kullanÄ±cÄ±larÄ± farklÄ± sosyal medya platformlarÄ± kullanmaktadÄ±r. Cinsiyete gÃ¶re platform tercihleri Ã¶nemli Ã¶lÃ§Ã¼de farklÄ±lÄ±k gÃ¶sterir. Bu farklÄ±lÄ±k, OAuth entegrasyonunda kiÅŸiselleÅŸtirilmiÅŸ bir deneyim sunma fÄ±rsatÄ± yaratÄ±r.

### 3.2 Gender Distribution (Nisan 2026 â€” Statista/DataReportal/GWI)

| Platform | Female % | Male % | Skew | OAuth |
|----------|----------|--------|------|-------|
| Pinterest | 69.4% | 30.6% | STRONG FEMALE | OAuth 2.0 v5 |
| Instagram | 58.2% | 41.8% | MODERATE FEMALE | OAuth 2.0 (Meta) |
| TikTok | 54.0% | 46.0% | SLIGHT FEMALE | OAuth 2.0 |
| Snapchat | 52.6% | 47.4% | SLIGHT FEMALE | OAuth 2.0 |
| YouTube | 45.2% | 54.8% | Near Equal | OAuth 2.0 (Google) |
| Facebook | 43.2% | 56.8% | MODERATE MALE | OAuth 2.0 (Meta) |
| LinkedIn | 38.6% | 61.4% | STRONG MALE | OAuth 2.0 |
| X/Twitter | 38.4% | 61.6% | STRONG MALE | OAuth 2.0 (PKCE) |
| Reddit | 38.2% | 61.8% | STRONG MALE | OAuth 2.0 |
| Discord | 32.6% | 67.4% | STRONG MALE | OAuth 2.0 |

### 3.3 Cinsiyet BazlÄ± GruplandÄ±rma

**FEMALE (5 platform):** Pinterest â†’ Instagram â†’ TikTok â†’ Snapchat â†’ YouTube
**MALE (5 platform):** Discord â†’ Reddit â†’ X â†’ LinkedIn â†’ YouTube
**NEUTRAL (2 platform):** YouTube, Facebook

### 3.4 Teknik KÄ±sÄ±tlamalar

| KÄ±sÄ±tlama | AÃ§Ä±klama | Ä°lgili ADR |
|-----------|----------|------------|
| OAuth token ÅŸifreleme | AES-256-GCM zorunlu | ADR-022 |
| CSRF korumasÄ± | OAuth callback'de CSRF token zorunlu | ADR-010 |
| Gender verisi | ADR-044'ten `data-gender` okunur | ADR-044 |
| No ORM | Raw PDO prepared statement | ADR-002 |
| PKCE zorunlu | X/Twitter OAuth 2.0 PKCE gerektirir | â€” |

---

## 4. Decision

### 4.1 Karar Bildirimi

CoreMusic, gender-based social OAuth sistemi kullanÄ±r. KullanÄ±cÄ±nÄ±n cinsiyetine gÃ¶re OAuth provider listesi filtrelenir ve gÃ¶sterilir.

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | OAuth token'larÄ± AES-256-GCM ile ÅŸifrelenir | âœ… Zorunlu |
| 2 | CSRF token OAuth callback'de doÄŸrulanÄ±r | âœ… Zorunlu |
| 3 | X/Twitter OAuth PKCE gerektirir | âœ… Zorunlu |
| 4 | Meta OAuth (Instagram/Facebook) Business/Creator hesap gerektirir | âœ… Zorunlu |
| 5 | Token'lar HttpOnly, Secure, SameSite=Lax cookie'de saklanÄ±r | âœ… Zorunlu |
| 6 | Refresh token Rotasyonu zorunlu (her platform iÃ§in farklÄ±) | âœ… Zorunlu |
| 7 | Gender neutral ise tÃ¼m platformlar gÃ¶sterilir | âœ… Zorunlu |
| 8 | Credential'lar .env'de saklanÄ±r, kodda hardcoded yasak | âœ… Zorunlu |

### 4.3 Platform KonfigÃ¼rasyonu

```php
// shared/config/oauth-platforms.php
'female' => [
    'pinterest'  => ['priority' => 1, 'female%' => 69.4, 'scopes' => ['boards:read','pins:read']],
    'instagram'  => ['priority' => 2, 'female%' => 58.2, 'scopes' => ['instagram_basic']],
    'tiktok'     => ['priority' => 3, 'female%' => 54.0, 'scopes' => ['user.info.basic']],
    'snapchat'   => ['priority' => 4, 'female%' => 52.6, 'scopes' => ['user.display_name']],
    'youtube'    => ['priority' => 5, 'female%' => 45.2, 'scopes' => ['youtube.readonly']],
],
'male' => [
    'discord'    => ['priority' => 1, 'male%' => 67.4,   'scopes' => ['identify','email']],
    'reddit'     => ['priority' => 2, 'male%' => 61.8,   'scopes' => ['identity','read']],
    'x'          => ['priority' => 3, 'male%' => 61.6,   'scopes' => ['tweet.read','users.read'], 'pkce' => true],
    'linkedin'   => ['priority' => 4, 'male%' => 61.4,   'scopes' => ['r_liteprofile','r_emailaddress']],
    'youtube'    => ['priority' => 5, 'male%' => 54.8,   'scopes' => ['youtube.readonly']],
],
```

---

## 5. Architecture

### 5.1 Mimari Diyagram

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                  GENDER-BASED OAUTH FLOW                 â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚                                                         â”‚
â”‚  KullanÄ±cÄ± â†’ auth.coremusic.net â†’ Gender kontrol         â”‚
â”‚    â”‚                                                    â”‚
â”‚    â”œâ”€â”€ female â†’ Pinterest, Instagram, TikTok, Snap, YT  â”‚
â”‚    â”œâ”€â”€ male   â†’ Discord, Reddit, X, LinkedIn, YT        â”‚
â”‚    â””â”€â”€ neutralâ†’ YouTube, Facebook                       â”‚
â”‚                                                         â”‚
â”‚  OAuth Flow:                                            â”‚
â”‚    1. KullanÄ±cÄ± platform seÃ§er                          â”‚
â”‚    2. OAuthManager â†’ Provider::getAuthorizationUrl()    â”‚
â”‚    3. Redirect â†’ Platform OAuth sayfasÄ±                 â”‚
â”‚    4. KullanÄ±cÄ± yetkilendirir                           â”‚
â”‚    5. Callback â†’ code alÄ±r                              â”‚
â”‚    6. Provider::exchangeCodeForToken($code)             â”‚
â”‚    7. Token AES-256-GCM ile ÅŸifrelenir                  â”‚
â”‚    8. DB'ye kaydedilir (oauth_connections)               â”‚
â”‚    9. Dashboard'a yÃ¶nlendirilir                         â”‚
â”‚                                                         â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 5.2 Katman EtkileÅŸimi

| Katman | Etki | AÃ§Ä±klama |
|--------|------|----------|
| **L0 Infrastructure** | DB tablosu | `oauth_connections` tablosu |
| **L1 Security** | Token ÅŸifreleme | AES-256-GCM, CSRF, rate limit |
| **L2 Routing** | OAuth routes | `auth.coremusic.net/oauth/*` |
| **L3 Presentation** | OAuth UI | Gender-based platform listesi |

---

## 6. Alternatives Considered

### 6.1 TÃ¼m KullanÄ±cÄ±lara TÃ¼m PlatformlarÄ± GÃ¶sterme

**Neden Reddedildi:** KullanÄ±cÄ± deneyimi kÃ¶tÃ¼, alakasÄ±z platformlar gÃ¶sterilmiÅŸ olur.

### 6.2 Sadece Tek Platform (Instagram)

**Neden Reddedildi:** Kapsam dar, farklÄ± kullanÄ±cÄ±lar farklÄ± platformlar kullanÄ±yor.

### 6.3 Unified API (Zernio/Phyllo)

**Neden Reddedildi:** ÃœÃ§Ã¼ncÃ¼ baÄŸÄ±mlÄ±lÄ±k, maliyet, kontrol kaybÄ±. CoreMusic kendi OAuth management'Ä±nÄ± yapar.

---

## 7. Consequences

### 7.1 Olumlu SonuÃ§lar

| # | SonuÃ§ | Etki |
|---|-------|------|
| 1 | KiÅŸiselleÅŸtirilmiÅŸ OAuth deneyimi | YÃ¼ksek |
| 2 | ADR-044 gender theme engine ile tam uyum | YÃ¼ksek |
| 3 | Token gÃ¼venliÄŸi (AES-256-GCM) | YÃ¼ksek |
| 4 | 10 platform desteÄŸi | Orta |

### 7.2 Olumsuz SonuÃ§lar

| # | SonuÃ§ | Risk | Mitigation |
|---|-------|------|------------|
| 1 | Meta App Review sÃ¼reci | Orta | Sandbox ile baÅŸla |
| 2 | X API Ã¼cretli ($0.20/post) | DÃ¼ÅŸÃ¼k | Read-only scope ile minimal kullanÄ±m |
| 3 | Token rotasyon karmaÅŸasÄ± | Orta | Per-platform refresh logic |

---

## 8. Implementation Roadmap

### 8.1 Faz 1: Backend Core (6-8 saat)

| # | GÃ¶rev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | OAuthProvider interface | Backend Architect | â³ |
| 2 | BaseOAuthProvider abstract | Backend Architect | â³ |
| 3 | 10 Provider sÄ±nÄ±fÄ± | Backend Architect | â³ |
| 4 | OAuthManager | Backend Architect | â³ |
| 5 | Platform config | Backend Architect | â³ |

### 8.2 Faz 2: Database + Security (2-3 saat)

| # | GÃ¶rev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | oauth_connections tablosu | Data Engineer | â³ |
| 2 | Token encryption | Security Engineer | â³ |
| 3 | CSRF integration | Security Engineer | â³ |

### 8.3 Faz 3: Frontend (3-4 saat)

| # | GÃ¶rev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | OAuth manager JS | UI Designer | â³ |
| 2 | Gender-based UI | UI Designer | â³ |
| 3 | Connection status | UI Designer | â³ |

### 8.4 Faz 4: Routes + Integration (2-3 saat)

| # | GÃ¶rev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | OAuth routes | Backend Architect | â³ |
| 2 | Middleware entegrasyonu | Backend Architect | â³ |
| 3 | Testler | QA Engineer | â³ |

---

## 9. Testing Strategy

| Test TÃ¼rÃ¼ | Hedef | Senaryo |
|-----------|-------|---------|
| Unit | â‰¥80% | Provider sÄ±nÄ±flarÄ±, token exchange |
| Integration | â‰¥70% | OAuth flow, DB kayÄ±t |
| E2E | Kritik | Connect â†’ Callback â†’ Dashboard |

---

## 10. Security Considerations

| OWASP SÄ±nÄ±fÄ± | Durum | AÃ§Ä±klama |
|--------------|-------|----------|
| A01: Broken Access Control | âœ… | RBAC + gender-based filtreleme |
| A04: Cryptographic Failures | âœ… | AES-256-GCM token ÅŸifreleme |
| A05: Injection | âœ… | PDO prepared statement |
| A07: Authentication Failures | âœ… | OAuth 2.0 + PKCE |

---

## 11. Edge Cases

| Durum | Ã‡Ã¶zÃ¼m |
|-------|-------|
| KullanÄ±cÄ± cinsiyet seÃ§memiÅŸ | Neutral fallback â†’ YouTube, Facebook |
| OAuth token sÃ¼resi dolmuÅŸ | Refresh token ile yenileme |
| Platform API deÄŸiÅŸikliÄŸi | Provider abstraction layer |
| Meta App Review reddi | Sandbox mode, alternatif platform |
| X API rate limit | Per-user rate tracking |

---

## 12. Glossary

| Terim | TanÄ±m |
|-------|-------|
| OAuth 2.0 | Open Authorization standardÄ± |
| PKCE | Proof Key for Code Exchange |
| AES-256-GCM | Authentication encryption |
| Provider | Sosyal medya OAuth saÄŸlayÄ±cÄ± |
| Scope | OAuth izin kapsamÄ± |
| Token Exchange | Authorization code â†’ Access token dÃ¶nÃ¼ÅŸÃ¼mÃ¼ |

---

## 13. Related Decisions

| ADR | BaÅŸlÄ±k | Ä°liÅŸki |
|-----|--------|--------|
| ADR-044 | Dynamic Theme Engine | BaÄŸÄ±mlÄ± (gender data) |
| ADR-022 | DB Hardened Security | BaÄŸÄ±mlÄ± (encryption) |
| ADR-010 | CSRF Protection | BaÄŸÄ±mlÄ± (OAuth callback) |
| ADR-072 | Social DB Schema | BaÄŸÄ±mlÄ± (oauth_connections) |

---

*ADR-088: Gender-Based Social OAuth System v1.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-17*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*