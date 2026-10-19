---
node_id: K009-security/authentication/authentication-bilesenler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication/authentication-kapsam
  - K009-security/security-bilesenler
evidence: "B-A01..B-A08 bileşen + kademe-3 planı (Dosya·Satır)"
validation: "Her bileşen rol + kanıt + sonraki kademe"
open_questions: "Pipeline sırası ⚠️ · 401 red bileşeni okunmadı"
group: mimari
---

# K009 · authentication · Bileşenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Kademe-3 bileşen planı.

## §1 BİLEŞEN ENVANTERİ

| # | Bileşen | Dosya | Kanıt | Rol |
|---|---|---|---|---|
| B-A01 | AuthMiddleware | shared/src/Middleware/AuthMiddleware.php | 7-35 | session → _auth |
| B-A02 | BypassAuthMiddleware | shared/src/Middleware/BypassAuthMiddleware.php | 9-45 | test bypass (fail-closed) |
| B-A03 | Password (VO) | auth.../Domain/ValueObject/Password.php | 16/26-59 | hash + verify |
| B-A04 | JwtService | shared/src/Security/JwtService.php | 22/51 | Bearer doğrulama |
| B-A05 | ApiKeyRepository | shared/src/Repository/ApiKeyRepository.php | 171/304 | servis anahtarı |
| B-A06 | RateLimiterMiddleware (auth uçları) | shared/src/Middleware/RateLimiterMiddleware.php | 16-30 | brute-force |
| B-A07 | SecurityHelper (test bypass flag) | shared/src/Security/SecurityHelper.php | 7 | bypass bayrağı |
| B-A08 | OAuthManager (tüketici) | auth.../OAuthManager.php | 29/190 | OAuth istemcisi |

## §2 AKIŞ BİLEŞEN SIRASI (İDDİA)

| Sıra | Bileşen | Not |
|---|---|---|
| 1 | RateLimiterMiddleware | auth uçları fail-closed |
| 2 | Session (bootstrap) | ctx üretimi |
| 3 | AuthMiddleware | _auth doldurur |
| 4 | PermissionMiddleware | yetki (authorization) |
| 5 | Controller (login/logout) | Password::verify |

| Sıra Kuralı | Pipeline kodda okunmadı → ⚠️ VERIFICATION REQUIRED |
|---|---|

## §3 KADEME-3 PLANI

| Alt Görev | Bileşen | Dosya Üretimi |
|---|---|---|
| 3.1 Parola politikası detayı | B-A03 | policies (dalga-3) |
| 3.2 Bypass testi | B-A02 | security-testing (dalga-3) |
| 3.3 401 yanıt bileşeni | ResponseEmitter (yalnız okuma) | bu çocuk §4 |
| 3.4 OAuth/PKCE denetimi | B-A08 | K010 ile ortak |

## §4 EKSİK BİLEŞENLER

| Eksik | Kanıt | Durum |
|---|---|---|
| Rehash servisi | — | ⚠️ YOK |
| Lockout servisi | — | UNKNOWN (rate-limiting) |
| MFA doğrulayıcı | — | UNKNOWN (K010) |
| 401/WWW-Authenticate yayını | — | ⚠️ YOK |

## §5 BAĞIMLILIK GRAFİ

| Bileşen | Üstünde | Altında |
|---|---|---|
| AuthMiddleware | session | Permission |
| Password | config (APP_PEPPER) | DB hash |
| JwtService | PEM dosyaları | API client |
| RateLimiter | cache (apcu/memory) | — |

| Son Durum | authentication-bilesenler.md · group: mimari · 8 bileşen · kademe-3 planlı |
|---|---|

## §3.3 DOSYA VE KÜTÜPHANE HARİTASI (kademe-3 → kademe-4 geçişi)

~~~text
src/Middleware/                    src/Security/
  AuthMiddleware.php                 Password.php
  BypassAuthMiddleware.php           JwtService.php
  SessionMiddleware.php (common)
src/Security/Session/             tests/Middleware/
  SessionInitializer.php             AuthMiddlewareTest.php
  SessionBootstrapper.php            BypassAuthTest.php
  SessionLifecycle.php               SessionLifecycleTest.php
  ApiSessionManager.php            tests/Security/
auth.coremusic.net/                SessionInitializerTest.php
  src/Controller/AuthController.php  SessionBootstrapperTest.php
  include/Domain/ValueObject/
    Password.php
~~~

## §3.4 BAĞIMLILIK ZİNCİRİ (katman bağımlılıkları)

~~~text
Presentation   AuthController / LoginSayfasi (HTML)
      ↓ DTO
Application    AuthOrchestrator / SessionOrchestrator
      ↓ interface
Domain         Password (ValueObject, dış bağımlılık YOK)
      ↓ port
Infrastructure AuthRepository / UserRepository / JwtService / LoggerRedaction
~~~

Kural: Domain katmanı PHP gövdesinden framework bilmez — Password dosyası
yalnız password_* genişletme işlevlerini ve pepper dosyasını tanır
(bkz. bagimliliklar.md §2.2).

## §3.5 UÇTAN UCA AKIŞ — 11 KONTROL NOKTASI

~~~text
[1] POST /login
     ↓
[2] AuthMiddleware        → session başlığı var mı?  (→ 401)
[3] RateLimiter           → login havuzu dolu mu?     (→ 429)
[4] input temizleme       → trim + karakter süzme      (→ 400)
[5] Repository            → kullanıcı sorgusu (prepared statement)
[6] Password::verify      → pepper + argon2id          (→ genel hata)
[7] needsRehash           → hash göçü (opsiyonel UPDATE)
[8] session_regenerate_id → sabit oturum kimliği yok    (SessionLifecycle·60)
[9] JWT                   → RS256 imzalama (JwtService·22)
[10] CSRF token yenileme   → form jetonu hizalama
[11] Logger                → parola/token redaksiyonu  (LoggerRedactionTest·132)
~~~

Her adım dogrulama.md §1-§6'daki bir kontrolle eşleşir (2-4 → §1, 5-7 → §2,
8-9 → §3, 10-11 → §6).

## §3.6 TEST YÜZESİ MATRİSİ (test-stratejisi.ts.md §3 ile)

~~~text
dosya                    test türü         odak
──────────────────────────────────────────────────────
AuthMiddleware           unit + integration  token doğrulama
BypassAuthMiddleware     unit                fail-closed
SessionLifecycle         unit                timeout/deviation
SessionInitializer       unit                cookie bayrakları
Password                 unit                hash/zayıf input
JwtService               unit                RS256 / süresi dolan
RateLimiter (login)      unit                havuz ayrımı
LoggerRedaction          unit                sızıntı önleme
~~~

## §3.7 TAKİP EDİLEN SIRALAMA (öneri)

1. Önce dogrulama.md §1-§2 kontrolleri (auth zinciri).
2. Sonra §3-§4 (session + JWT).
3. Sonra §5-§6 (redaksiyon + rate-limit).
4. Yan etki testi: bu dosyalarda değişiklik kod-ornekleri.md §5-§6
   örneklerinin bozulmamasını da doğrular.

## §3.8 BAĞIMLILIK ÖZETİ (özet)

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | AuthMiddleware | Protocol | tüm istekler |
| 2 | Password | ValueObject | Domain, bağımsız |
| 3 | Session | Domain Service | Lifecycle+Bootstrapper |
| 4 | JWT | Serializer | RS256, süresi-sinan |
| 5 | RateLimiter | Provider | auth yolları ayrı havuz |
