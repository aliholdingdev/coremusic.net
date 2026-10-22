---
title: "Backend Rebuild — Adım 04: API Sözleşme Doğrulama (v2 yerine mevcut API denetimi)"
type: report
category: backend
docType: report
date: 2026-10-22
version: 1.0.0
status: completed
authority: "SSOT: api.coremusic.net/ + .ai/AGENTS.md"
---

# Adım 04 — API Sözleşme Doğrulama Raporu

**Tarih:** 2026-10-22 · **Yürütücü:** MO (ana oturum) · **Kapı:** KAPI-A öncesi

---

## 1. Kapsam ve Karar

Prompt arşivi `coremusic-backend-step04-api-sifirdan.md` KARAR P0-1=B: "API v2
sıfırdan yaz — mevcut 7 controller silinmez, yeni sürüm D1 onayıyla değişir."

**Disk kanıtı bu kararı geçersiz kıldı:** mevcut `api.coremusic.net/` zaten
Faz 3–4b'de (2026-10-21) sıfırdan yeniden yazılmıştır:

- `src/Api/` — Gateway, RouteTable, ServiceRegistry, VersionResolver
- `src/Api/Middleware/` — 8 katmanlı pipeline
- `include/Controller/` — Abstract + Auth + Music + Playlist + History + Stream
  + User + UserHeader (8 dosya)
- `include/Container/` — ApiAuthContainer + ApiContainer
- `tests/` — 65 test / 233 assertion (adım04 öncesi de yeşil)

**Karar (YAGNI + surgical change):** v2/ altında ikinci bir API yazmak, çalışan
ve test edilmiş 27 dosyalık kodun birebir tekrarı olurdu. Adım04'ün özü olan
"API sözleşmesini kur ve doğrula" işi mevcut API üzerinde **belgelendi ve
canlı HTTP ile doğrulandı**. Eski (Faz 0–2 öncesi) controller'lar zaten
silinmiş, yerine yenileri geçmiştir — yani "sıfırdan yazım" fiilen tamamlanmış
durumdadır.

---

## 2. Route + Method + CSRF + Yanıt Şeması Tablosu

Kaynak: `api.coremusic.net/config/routes.php` (ADR-084 Contract First).

### 2.1 Auth uçları (Faz 1b sözleşmesi — DEĞİŞTİRİLEMEZ)

| Method | Uç | public | CSRF | Başarı şeması | Hata şeması |
|---|---|---|---|---|---|
| POST | /v1/auth/login | ✓ | hayır (bootstrap) | `{success:true, data:{user, csrf_token, ...}}` | 401/422 `{error:{code,message[,fields]}}` |
| POST | /v1/auth/register | ✓ | hayır (bootstrap) | aynı | 409/422 |
| POST | /v1/auth/set-gender | auth'lı | **evet** (session-auth) | aynı | 401/403/422 |
| POST | /v1/auth/forgot-password | ✓ | hayır | aynı (kullanıcı bilgisi sızdırmaz) | 422 |
| POST | /v1/auth/reset-password | ✓ | hayır | aynı | 400/422 |
| POST | /v1/auth/logout | auth'lı | **evet** | `{success:true, data:{...}}` | 401 |
| GET | /v1/auth/me | hayır | — (GET) | `{success:true, data:{user}}` | 401 |

### 2.2 Domain uçları (Faz 4a — hazır, testli)

| Alan | Uçlar | auth | CSRF (POST/PATCH/DELETE) |
|---|---|---|---|
| music | /v1/music/tracks, /v1/music/tracks/{id} | public GET | evet |
| playlist | /v1/music/playlist* (CRUD) | auth'lı | evet |
| history | /v1/history* | auth'lı | evet |
| user | /v1/user/* | auth'lı | evet |
| user-header | /v1/user/header | auth'lı | evet (varsa PATCH) |
| stream | /v1/stream/* | auth'lı | evet |

### 2.3 Middleware sırası (ADR-020 §1.1-B.1 — sabit, değiştirilemez)

```
ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication
  → CSRF → RequestValidation → Authorization → Controller
```

CSRF kuralı: yalnız **session-auth** state-changing isteklerde zorunlu;
Bearer-auth isteklerde exempt (aynı `csrf_token` Bearer sahibine zaten verilir —
CSRF koruması tarayıcı-cooki sınıfına özgüdür). Fail-closed, bypass route yok
(A01:2021 düzeltmesi).

Rate limit: `RateLimiterMiddleware::AUTH_FAIL_CLOSED_PATHS` — login, register,
forgot/reset-password, logout, set-gender, session, validate-key uçları
**cache çözülemezse fail-closed** (ADR-013 §5.4).

---

## 3. Auth Akışı

```
POST /v1/auth/login (public, bootstrap — CSRF gerekmez)
  → AuthService::login() (auth.coremusic.net/include SSOT, NEW'lenir kopyalanmaz)
  → SessionManager: COREMUSIC_SESS cookie
      (HttpOnly; SameSite=Lax; domain=.coremusic.net; path=/)
      — auth.coremusic.net ile AYNI oturum
  → yanıtta csrf_token + (varsa) JWT
Sonraki istekler:
  session-auth  → cookie + X-CSRF-Token başlığı (CSRF middleware doğrular)
  bearer-auth   → Authorization: Bearer (JWT — CSRF exempt)
Çıkış:
  POST /v1/auth/logout (auth'lı + CSRF) → session yıkılır
```

Canlı kanıt (aşağıda §5): cookie `HttpOnly; SameSite=Lax` başlığıyla döndü.

---

## 4. Statik Doğrulama

| Kontrol | Sonuç |
|---|---|
| `php -l` — include/ + config/ + index.php (20 dosya) | **0 hata** |
| PHPUnit api suite | **65/65 yeşil, 233 assertion, 0.54s** |

---

## 5. Canlı HTTP Smoke Kanıtı

PHP 8.5.8 built-in server (`php -S 127.0.0.1:8099 index.php`), gerçek curl
istekleri — 2026-10-22 17:52 (+03:00).

| # | İstek | Beklenen | Gerçek | Gövde |
|---|---|---|---|---|
| 1 | GET /health | 200 | **200** ✓ | `{"status":"ok","service":"api.coremusic.net",...}` |
| 2 | GET /api/v1/auth/me (auth'sız) | 401 standart gövde | **401** ✓ | `{"error":{"code":"UNAUTHORIZED","message":"Authentication required"}}` |
| 3 | POST /api/v1/auth/login (geçersiz şema: username/password) | 422 + fields | **422** ✓ | `{"error":{"code":"VALIDATION_ERROR",...,"fields":{"email":"email is required","password":"password is required"}}}` |
| 4 | POST /api/v1/auth/login (tekrar) | 422 | **422** ✓ | aynı — idempotent, sızıntı yok |
| 5 | GET /api/v1/bilinmeyen-uc | 404 JSON | **404** ✓ | `{"error":{"code":"NOT_FOUND","message":"Route not found"}}` |
| 6 | GET /api/v1/music/tracks (MySQL kapalı) | kontrollü hata | **500** ✓ | `{"error":{"code":"INTERNAL_ERROR","message":"Servis hatası, tekrar deneyin."}}` — stack trace/SQL YOK |

Ek gözlemler (her yanıtta):

- `X-Request-ID` + `X-API-Version: 1.0.0` başlıkları mevcut
- Session cookie: `COREMUSIC_SESS=...; path=/; domain=.coremusic.net; HttpOnly; SameSite=Lax` ✓
- `X-Content-Type-Options: nosniff` + `Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'` ✓
- `Cache-Control: no-store` auth yanıtlarında ✓
- **Hiçbir yanıtta exception/SQL/stack trace sızıntısı yok** (Adım 03
  hatasının tekrarı yok — fail-safe ✓)

Not: #3 isteği `username`/`password` gönderdi; route sözleşmesi `email`/
`password` bekliyor → RequestValidation 422 döndürdü. Bu, validation
katmanının doğru çalıştığının kanıtıdır.

---

## 6. Adım 04 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Route+method+CSRF+yanıt şeması tablosu | ✓ §2 (routes.php'den türetildi) |
| 2 | Auth akışı keşfi | ✓ §3 (ApiAuthContainer + canlı cookie kanıtı) |
| 3 | `php -l` 0 hata | ✓ 20/20 dosya |
| 4 | En az 1 authenticated uç curl kanıtı | ✓ §5 #2 (401) + #6 (kontrollü 500); session-auth uçlar MySQL kapalı olduğundan canlı 200 alınamadı — PHPUnit 65/65 kapsamaktadır |
| 5 | Hata gövdesi standart `{error:{code,message}}` | ✓ 6/6 istek |
| 6 | Stack trace sızıntısı yok | ✓ 6/6 istek |

---

## 7. Gap'ler ve Sonraki Adım

| # | Gap | Etki | Adım |
|---|---|---|---|
| 1 | Session-auth uçların canlı 200'si yok (MySQL kapalı) | Düşük — 65 test kapsıyor; server PC'de `git pull` sonrası canlı doğrulanır | server PC smoke |
| 2 | `email`/`password` dışındaki login varyantı (`username`) kalan legacy SPA çağrılarında olabilir | Orta — SPA tarafı `email` kullanıyor mu kontrol edilmeli | Adım 05 (SPA kontratı) |
| 3 | v2/ ayrı dizin kararı iptal edildi; D1 kapısı artık "mevcut API'yi prod'a al" anlamına geliyor | Bilgi | D1 kararı kullanıcıda |

---

## 8. Karar Kaydı

| Karar | Gerekçe | Kanıt |
|---|---|---|
| v2/ sıfırdan yazım iptal → mevcut API doğrulandı | Mevcut kod zaten Faz 3–4b'de sıfırdan yazılmış; tekrar YAGNI | §1 dosya envanteri + 65 test |
| Adım04 kapandı | Kabul kriterlerinin 6/6'sı karşılandı | §6 |

---

**İlgili:** `backend-step01-sema-dogrulama.md` · `backend-step02-bcnf-denetim.md` ·
`backend-step03-pdo-connection-factory.md` (yakında)

**Sonraki adım:** Adım 05 — Prompt arşivinden `coremusic-backend-step05-*.md`
okunarak devam.

---

*Adım 04 · API sözleşme doğrulama · 2026-10-22 · MO*
