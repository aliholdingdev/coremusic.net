# Middleware Pipeline (Immutable)

> Kaynak: `.ai/CLAUDE.md` §6 — **Immutable: ADR-010/011/012/013/022**. Guardrail #7:
> sıra değişmez; ihlal → sistem durdurulur.

## 1. Pipeline (tek doğrulanmış sıra)

```text
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS) | — |
| 2 | **Cors** | CORS header yönetimi | — |
| 3 | **RateLimiter** | APCu tabanlı, **60 req/60s** | 60s |
| 4 | **SecurityHeaders** | CSP strict-dynamic, X-Frame-Options, HSTS — **CSP nonce ÜRETİMİ burada** | — |
| 5 | **SessionManager** | Session başlatır; **#4'ün nonce'unu session'a kaydeder** | 3600s idle |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) — adı `csrf_token`; `_csrf_token` YASAK | — |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), **prod'da devre dışı** | — |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session) | — |
| 9 | **Permission** | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | — |
| 10 | **Validation** | Request/DTO validasyonu | — |
| — | **Controller** | Endpoint iş mantığı (pipeline'ın hedefi) | — |

## 2. İmmutability Kuralı

- Sıra **DEĞİŞTİRİLEMEZ** (Guardrail #7 — Middleware Order Immutable).
- Yeni middleware eklenirken hangi konuma oturduğu vault'ta tanımlanmadıysa
  `⚠️ VERIFICATION REQUIRED` + kullanıcı onayı (Human Approval Gate) gerekir.
- Endpoint kodu pipeline'ı kopyalamaz; Controller yalnız sıradan sonra çalışır.

## 3. CSP Nonce Mekaniği

1. **SecurityHeaders (#4)** CSP başlığını `strict-dynamic` ile üretir ve nonce'u oluşturur.
2. **SessionManager (#5)** bu nonce'u session'a kaydeder; istemci render'da aynı nonce okunur.
3. Bağımlılık yönü: #4 → #5 (üretim → saklama). #5, #4'ten önce çalışırsa kaydedilecek
   nonce henüz yoktur.

## 4. Yeniden Sıralamanın Sonuçları (Critical Warning #1)

| Hatalı hamle | Sonuç |
|--------------|-------|
| SecurityHeaders ↔ SessionManager takası | Nonce eşleşmez → **CSP bozulur, güvenlik açığı** |
| RateLimiter'ı sona almak | Korumasız istekler auth/validation çalıştırır (DoS yüzeyi) |
| Csrf'ı Validation/Auth sonrasına almak | Doğrulanmamış istek iş mantığına erişebilir |
| Auth/Permission'ı öne çekmeden Controller'a erişim | Yetkisiz erişim (K6 bypass — asla) |
| BypassAuth'i prod'da aktif bırakmak | Auth tamamen atlanır |

**İhlal prosedürü:** tespit → derhal revert + `log.md` CRITICAL (§5.1 Layer Violation emsali).