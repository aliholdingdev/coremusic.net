---
type: decision
id: "088"
title: "ADR-088: Gender-Based Social OAuth System"
category: "security"
status: "active"
date: "2026-08-17"
updated: "2026-08-17"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [security, oauth, social, gender, authentication, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-044-dynamic-user-theme-engine]]"
  - "[[decisions/accepted/ADR-052-hybrid-auth-architecture]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-072-social-database-schema]]"
  - "[[architecture/l1-security]]"
---

# ADR-088: Gender-Based Social OAuth System

---

## 1. Executive Summary

CoreMusic'e **cinsiyet bazlı sosyal medya OAuth sistemi** eklenir. Kullanıcının `data-gender` attribute'una göre (female/male/neutral) en çok kullanılan sosyal medya platformları gösterilir ve OAuth 2.0 ile bağlanır.

### 1.1 Kararın Özeti

Kullanıcı cinsiyetine göre filtrelenmiş sosyal medya OAuth entegrasyonu. 10 platform: Pinterest, Instagram, TikTok, Snapchat, YouTube (female-倾向), Discord, Reddit, X/Twitter, LinkedIn, YouTube (male-倾向).

### 1.2 Temel Gerekçe

- Kullanıcı deneyimini kişiselleştirme (ADR-044 gender theme engine ile uyumlu)
- Sosyal medya entegrasyonunu cinsiyet tercihlerine göre optimize etme
- OAuth token güvenliğini sağlama (AES-256-GCM ile şifreleme)

### 1.3 Beklenen Sonuçlar

- Female kullanıcılar: Pinterest, Instagram, TikTok, Snapchat, YouTube
- Male kullanıcılar: Discord, Reddit, X/Twitter, LinkedIn, YouTube
- Neutral kullanıcılar: YouTube, Facebook

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | active |
| **Versiyon** | 1.0.0 |
| **Oluşturma Tarihi** | 2026-08-17 |
| **Son Güncelleme** | 2026-08-17 |
| **Otorite** | Security Engineer |
| **Onay** | Red Team · Human Mode · Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

CoreMusic kullanıcıları farklı sosyal medya platformları kullanmaktadır. Cinsiyete göre platform tercihleri önemli ölçüde farklılık gösterir. Bu farklılık, OAuth entegrasyonunda kişiselleştirilmiş bir deneyim sunma fırsatı yaratır.

### 3.2 Gender Distribution (Nisan 2026 — Statista/DataReportal/GWI)

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

### 3.3 Cinsiyet Bazlı Gruplandırma

**FEMALE (5 platform):** Pinterest → Instagram → TikTok → Snapchat → YouTube
**MALE (5 platform):** Discord → Reddit → X → LinkedIn → YouTube
**NEUTRAL (2 platform):** YouTube, Facebook

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Açıklama | İlgili ADR |
|-----------|----------|------------|
| OAuth token şifreleme | AES-256-GCM zorunlu | ADR-022 |
| CSRF koruması | OAuth callback'de CSRF token zorunlu | ADR-010 |
| Gender verisi | ADR-044'ten `data-gender` okunur | ADR-044 |
| No ORM | Raw PDO prepared statement | ADR-002 |
| PKCE zorunlu | X/Twitter OAuth 2.0 PKCE gerektirir | — |

---

## 4. Decision

### 4.1 Karar Bildirimi

CoreMusic, gender-based social OAuth sistemi kullanır. Kullanıcının cinsiyetine göre OAuth provider listesi filtrelenir ve gösterilir.

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | OAuth token'ları AES-256-GCM ile şifrelenir | ✅ Zorunlu |
| 2 | CSRF token OAuth callback'de doğrulanır | ✅ Zorunlu |
| 3 | X/Twitter OAuth PKCE gerektirir | ✅ Zorunlu |
| 4 | Meta OAuth (Instagram/Facebook) Business/Creator hesap gerektirir | ✅ Zorunlu |
| 5 | Token'lar HttpOnly, Secure, SameSite=Lax cookie'de saklanır | ✅ Zorunlu |
| 6 | Refresh token Rotasyonu zorunlu (her platform için farklı) | ✅ Zorunlu |
| 7 | Gender neutral ise tüm platformlar gösterilir | ✅ Zorunlu |
| 8 | Credential'lar .env'de saklanır, kodda hardcoded yasak | ✅ Zorunlu |

### 4.3 Platform Konfigürasyonu

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
┌─────────────────────────────────────────────────────────┐
│                  GENDER-BASED OAUTH FLOW                 │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Kullanıcı → auth.coremusic.net → Gender kontrol         │
│    │                                                    │
│    ├── female → Pinterest, Instagram, TikTok, Snap, YT  │
│    ├── male   → Discord, Reddit, X, LinkedIn, YT        │
│    └── neutral→ YouTube, Facebook                       │
│                                                         │
│  OAuth Flow:                                            │
│    1. Kullanıcı platform seçer                          │
│    2. OAuthManager → Provider::getAuthorizationUrl()    │
│    3. Redirect → Platform OAuth sayfası                 │
│    4. Kullanıcı yetkilendirir                           │
│    5. Callback → code alır                              │
│    6. Provider::exchangeCodeForToken($code)             │
│    7. Token AES-256-GCM ile şifrelenir                  │
│    8. DB'ye kaydedilir (oauth_connections)               │
│    9. Dashboard'a yönlendirilir                         │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### 5.2 Katman Etkileşimi

| Katman | Etki | Açıklama |
|--------|------|----------|
| **L0 Infrastructure** | DB tablosu | `oauth_connections` tablosu |
| **L1 Security** | Token şifreleme | AES-256-GCM, CSRF, rate limit |
| **L2 Routing** | OAuth routes | `auth.coremusic.net/oauth/*` |
| **L3 Presentation** | OAuth UI | Gender-based platform listesi |

---

## 6. Alternatives Considered

### 6.1 Tüm Kullanıcılara Tüm Platformları Gösterme

**Neden Reddedildi:** Kullanıcı deneyimi kötü, alakasız platformlar gösterilmiş olur.

### 6.2 Sadece Tek Platform (Instagram)

**Neden Reddedildi:** Kapsam dar, farklı kullanıcılar farklı platformlar kullanıyor.

### 6.3 Unified API (Zernio/Phyllo)

**Neden Reddedildi:** Üçüncü bağımlılık, maliyet, kontrol kaybı. CoreMusic kendi OAuth management'ını yapar.

---

## 7. Consequences

### 7.1 Olumlu Sonuçlar

| # | Sonuç | Etki |
|---|-------|------|
| 1 | Kişiselleştirilmiş OAuth deneyimi | Yüksek |
| 2 | ADR-044 gender theme engine ile tam uyum | Yüksek |
| 3 | Token güvenliği (AES-256-GCM) | Yüksek |
| 4 | 10 platform desteği | Orta |

### 7.2 Olumsuz Sonuçlar

| # | Sonuç | Risk | Mitigation |
|---|-------|------|------------|
| 1 | Meta App Review süreci | Orta | Sandbox ile başla |
| 2 | X API ücretli ($0.20/post) | Düşük | Read-only scope ile minimal kullanım |
| 3 | Token rotasyon karmaşası | Orta | Per-platform refresh logic |

---

## 8. Implementation Roadmap

### 8.1 Faz 1: Backend Core (6-8 saat)

| # | Görev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | OAuthProvider interface | Backend Architect | ⏳ |
| 2 | BaseOAuthProvider abstract | Backend Architect | ⏳ |
| 3 | 10 Provider sınıfı | Backend Architect | ⏳ |
| 4 | OAuthManager | Backend Architect | ⏳ |
| 5 | Platform config | Backend Architect | ⏳ |

### 8.2 Faz 2: Database + Security (2-3 saat)

| # | Görev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | oauth_connections tablosu | Data Engineer | ⏳ |
| 2 | Token encryption | Security Engineer | ⏳ |
| 3 | CSRF integration | Security Engineer | ⏳ |

### 8.3 Faz 3: Frontend (3-4 saat)

| # | Görev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | OAuth manager JS | UI Designer | ⏳ |
| 2 | Gender-based UI | UI Designer | ⏳ |
| 3 | Connection status | UI Designer | ⏳ |

### 8.4 Faz 4: Routes + Integration (2-3 saat)

| # | Görev | Sorumlu | Durum |
|---|-------|---------|-------|
| 1 | OAuth routes | Backend Architect | ⏳ |
| 2 | Middleware entegrasyonu | Backend Architect | ⏳ |
| 3 | Testler | QA Engineer | ⏳ |

---

## 9. Testing Strategy

| Test Türü | Hedef | Senaryo |
|-----------|-------|---------|
| Unit | ≥80% | Provider sınıfları, token exchange |
| Integration | ≥70% | OAuth flow, DB kayıt |
| E2E | Kritik | Connect → Callback → Dashboard |

---

## 10. Security Considerations

| OWASP Sınıfı | Durum | Açıklama |
|--------------|-------|----------|
| A01: Broken Access Control | ✅ | RBAC + gender-based filtreleme |
| A04: Cryptographic Failures | ✅ | AES-256-GCM token şifreleme |
| A05: Injection | ✅ | PDO prepared statement |
| A07: Authentication Failures | ✅ | OAuth 2.0 + PKCE |

---

## 11. Edge Cases

| Durum | Çözüm |
|-------|-------|
| Kullanıcı cinsiyet seçmemiş | Neutral fallback → YouTube, Facebook |
| OAuth token süresi dolmuş | Refresh token ile yenileme |
| Platform API değişikliği | Provider abstraction layer |
| Meta App Review reddi | Sandbox mode, alternatif platform |
| X API rate limit | Per-user rate tracking |

---

## 12. Glossary

| Terim | Tanım |
|-------|-------|
| OAuth 2.0 | Open Authorization standardı |
| PKCE | Proof Key for Code Exchange |
| AES-256-GCM | Authentication encryption |
| Provider | Sosyal medya OAuth sağlayıcı |
| Scope | OAuth izin kapsamı |
| Token Exchange | Authorization code → Access token dönüşümü |

---

## 13. Related Decisions

| ADR | Başlık | İlişki |
|-----|--------|--------|
| ADR-044 | Dynamic Theme Engine | Bağımlı (gender data) |
| ADR-052 | Hybrid Auth Architecture | Bağımlı (JWT integration) |
| ADR-022 | DB Hardened Security | Bağımlı (encryption) |
| ADR-010 | CSRF Protection | Bağımlı (OAuth callback) |
| ADR-072 | Social DB Schema | Bağımlı (oauth_connections) |

---

*ADR-088: Gender-Based Social OAuth System v1.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-17*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
