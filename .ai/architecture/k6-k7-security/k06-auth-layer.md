# K06: Auth Katmanı Detayı

## Genel Bakış
CoreMusic authentication sistemi, enterprise seviyede merkezi auth.coremusic.net üzerinden yönetilir.

## Auth Mimarisi

```
┌─────────────────────────────────────────────────────────────────────┐
│                    AUTH MİMARİSİ                                     │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ Kullanıcı    │────►│ Auth Gateway │────►│ Auth Service  │       │
│  │ (Browser)    │     │ (Middleware) │     │ (PHP 8.4)    │       │
│  └──────────────┘     └──────────────┘     └──────────────┘       │
│                              │                     │                │
│                              ▼                     ▼                │
│                       ┌──────────────┐     ┌──────────────┐       │
│                       │ JWT Token    │     │ Session      │       │
│                       │ (Access)     │     │ (HTTPOnly)   │       │
│                       └──────────────┘     └──────────────┘       │
│                                                                     │
│  Kimlik Doğrulama Yöntemleri:                                       │
│  ├── JWT (JSON Web Token) — Stateless, API için                    │
│  ├── Session — Stateful, SPA için                                  │
│  ├── OAuth2 — Üçüncü parti entegrasyon                            │
│  ├── API Key — Servisler arası iletişim                            │
│  ├── MFA/TOTP — İki faktörlü doğrulama                            │
│  └── Passkey — Şifresiz kimlik doğrulama (FIDO2)                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## JWT Token Yapısı

```json
{
  "header": {
    "alg": "RS256",
    "typ": "JWT",
    "kid": "key-2026-09"
  },
  "payload": {
    "sub": "user-uuid-v7",
    "iss": "auth.coremusic.net",
    "aud": "api.coremusic.net",
    "exp": 1726723200,
    "iat": 1726719600,
    "roles": ["regular", "premium"],
    "permissions": ["read", "write", "stream"]
  }
}
```

## Session Yönetimi

| Parametre | Değer |
|-----------|-------|
| Session ID | UUID v7 |
| Cookie Name | `cm_session` |
| Cookie Flags | HttpOnly, Secure, SameSite=Strict |
| Idle Timeout | 3600s (1 saat) |
| Absolute Timeout | 86400s (24 saat) |
| Storage | Server-side (Redis) |
| Regeneration | Her login'de |

## RBAC Roller

| Rol | Yetki Seviyesi | Kullanım |
|-----|----------------|----------|
| regular | Düşük | Temel dinleme, kütüphane |
| premium | Orta | Yüksek kalite, offline |
| studio | Yüksek | Stüdyo özellikleri |
| car | Orta | Araç içi erişim |
| admin | Yüksek | Yönetim paneli |
| system | Tam | Sistem yönetimi |

## Auth Flow

```
Kullanıcı → Login Formu → Auth Service → Password Verify (Argon2id)
    │                                          │
    │                                          ▼
    │                                    JWT Token Üret
    │                                          │
    ▼                                          ▼
Session Oluştur ←─────────────────────── Token Response
    │
    ▼
Cookie Set (HttpOnly) → SPA Routing Başlat
```

## Güvenlik Kuralları

| Kural | Açıklama |
|-------|----------|
| Argon2id | Şifre hashleme (64MB, 4 iterasyon) |
| CSRF Token | `csrf_token` key (NOT `_csrf_token`) |
| Rate Limit | 5 deneme/15dk (login) |
| Lockout | 10 başarısız → 30dk hesap kilidi |
| Secure Cookie | Production'da zorunlu |
| HSTS | max-age=31536000 |

## İlgili Dosyalar

- [[k6-security]] — Genel güvenlik katmanı
- [[electronics/amplifier-classab-circuit]] — Donanım auth entegrasyonu
- [[electronics/power-supply-classab]] — Güç durumu auth koruması

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
