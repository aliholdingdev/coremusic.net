---
title: "K006 SECURITY — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K006-guvenlik/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K006-guvenlik
ssot: true
risk: high
owner: security
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K006 SECURITY — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | SOFTWARE (EK A §A.0) |
| Teatral epitet | «ÇELİK KAPI» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `security` (Security Engineer — AGENTS.md §4 madde 4: OWASP, encryption, CSRF, CSP) |
| Risk | **high** → EK C 20 alan tam kart **zorunlu** (R4.2) · güvenlik kapısı ≥90 (F1 §11.1) |
| Üretim yeri | staging (`b1-K006-guvenlik.md`) → hedef `.ai/architecture/K006-guvenlik/index.md` |

> **Durum etiketleri (F1 §5.4):** `[CURRENT]` (repo kanıtlı) · `[TARGET]` ·
> `[PROPOSED]` · `[PLANNED]`/`[DESIGN]` · `[VERIFY REQUIRED]`. Bu kart band-1'in
> **tek `security=YÜKSEK` + `failure=fail-secure`** üyesidir; F1 §11.1 güvenlik
> kategorisi ≥90/100 eşiği bu kartı bağlar.

---

#### §1 Genel Bakış

K006, CoreMusic'in **güvenlik karar katmanıdır**: authentication, authorization,
RBAC, policy, JWT, CSRF, CSP, rate limiting, vault ve audit (EK A §A.1 K006
kartı). İki mutlak sınır taşıyor: **`security=YÜKSEK`** ve
**`failure=fail-secure`** (doğrulanamıyorsa RED). `.ai/CLAUDE.md` §5 K6
guardrail'i bağlayıcıdır: **"Hard Guardrail: Asla bypass edilemez"** —
bypass mekanizması bu katmanda tanımlanamaz (var olan `BypassAuth`
test mekanizması ayrı karardır: ADR-008). Repo'da `shared/src/Security/`,
`shared/src/Middleware/` ve ilgili testler **mevcuttur**.

### 1.1 Kapsam Dışı (K006 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | Middleware **sırası** (pipeline) | K007 | `.ai/CLAUDE.md` §6 (sıra DEĞİŞTİRİLEMEZ) · EK A K007 kartı |
| 2 | Şema/normalizasyon (credential tabloları dahil) | K005 | `.ai/CLAUDE.md` §18 · ADR-034 |
| 3 | API gateway / BFF / rate-limit dağıtım topolojisi | K009 | EK A §A.1 K009 kartı |
| 4 | Log/metrik **üretim kuralı** (audit şeması K005'inde) | K012 | R7.1 · §18 DB7 |
| 5 | UI/oturum gösterimi | K010 · K011 | EK A §A.1 K010/K011 |
| 6 | Donanım/firmware imzalama **uygulaması** | K001/K002 (arayüz) | F1 SEC15/SEC16 |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K006 | SECURITY | «ÇELİK KAPI» | SECURITY (cross-cutting) | server · edge · desktop | Authentication · Authorization · RBAC · Policy · JWT · CSRF · CSP · Rate Limiting · Vault · Audit (EK A, 10 madde) | kimlik talebi (kimlik/belge) · yetki isteği (role + kaynak) · politika sorgusu · token yenileme · nonce/CSRF verisi | karar (allow/deny) + nedeni · token/nonce · rate-limit kararı · audit kaydı · hata (401/403) | K000 · K001 · K002 · K003 · K004 · K005 (EK A: izinli=K000-K005) + port/adapter | üst katmana (K007-K020) doğrudan erişim · geri çağrı (H20) · veri paylaşımı (H19) · **bypass (asla)** · `SELECT *`/ORM/MD5- SHA-1 | çekirdek veri sınırı (EK A) — credential/audit verisi başka katmanla paylaşılmaz; şema sahibi K005, **karar** sahibi K006 | **security=YÜKSEK** (EK A) · OWASP A01 + A04 doğrudan eşli; A02/A05/A07/A10 K007/K005 ile örtüşür (F1 §9.1) · fail-secure | **fail-secure** (EK A · F1 SEC14) — doğrulanamıyorsa RED; fail-open yalnız belgelenmiş ve ADR'li senaryo | auth/başarısızlık sayımı · brute-force metriği · rate-limit tetiklenme · audit trail · alert (F1 §12.1) | OWASP ASVS L2 senaryoları (F1 §12.1) · CSRF/CSP/rate-limit birim testi · bypass'ın prod'da kapalı olduğunu doğrulama [PLANNED] | `.ai/architecture/00-kspace-anayasa.md` satır 99-102 · `.ai/CLAUDE.md` §5 K6 satırı (115) + §6 (pipeline) + §7 (Guardrail #6) + §21/§23 + §17 · `shared/src/Security/*` (6) + `shared/src/Middleware/*` (11) + `shared/src/Api/Middleware/*` (6) + `shared/src/Session/*` (4) + `shared/tests/Api/*MiddlewareTest.php` · ADR-008/010/011/012/013/020/022/052/058/059/094/095 · EK B #1 · #2 |

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K006` | EK A §A.0 (R2.3) |
| 2 | KANONİK_AD | `SECURITY` | EK A §A.0 |
| 3 | TEATRAL_EPİTET | «ÇELİK KAPI» | EK A §A.0 — **F1 §12.1 örnek kartındaki "GÜVENLİK KALESİ" epiteti örnek/şablon niteliğindedir; kanonik epitet EK A'dır (R8.1)** |
| 4 | DOMAIN | `SECURITY (cross-cutting)` | katman tüm bantlara kesen güvenlik kararları üretir |
| 5 | RUNTIME | server · edge · desktop | F1 §12.1 "Server + Edge" + desktop oturum yüzeyi |
| 6 | SORUMLULUK | 10 madde | EK A §A.1 K006 — birebir |
| 7 | GİRDİ | kimlik talebi · yetki isteği · politika · token yenileme · nonce | Sorumluluk + F1 §12.1'den |
| 8 | ÇIKTI | allow/deny + neden · token/nonce · rate-limit kararı · audit kaydı · 401/403 | F1 §12.1 |
| 9 | İZİNLİ_BAGIMLILIK | K000-K005 | EK A satır 101 "izinli=K000-K005" |
| 10 | YASAK_BAGIMLILIK | K007-K020 doğrudan · H20 · H19 · bypass | EK A satır 101 + §5 K6 guardrail |
| 11 | DATA_BOUNDARY | çekirdek veri sınırı | EK A satır 101 |
| 12 | SECURITY_BOUNDARY | **YÜKSEK** · A01/A04 | EK A satır 101 + F1 §9.1 |
| 13 | FAILURE_MODE | **fail-secure** | EK A satır 101 + F1 SEC14 |
| 14 | OBSERVABILITY | auth · brute-force · rate-limit · audit · alert | F1 §12.1 |
| 15 | TEST | OWASP ASVS L2 senaryoları | F1 §12.1 + §17 (PHPUnit ≥80/90) |
| 16 | KANIT | anayasa + repo (27+ dosya) + ADR (12) + EK B (#1 · #2) | R9.5 3'lü format |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3 · risk:high → zorunlu)

```yaml
K-ID:               K006
KANONİK_AD:         SECURITY
TEATRAL_EPİTET:     «ÇELİK KAPI»
DOMAIN:             SECURITY (cross-cutting)
SUBDOMAIN:          authn-authz                        # Authentication · Authorization · RBAC · Policy
BOUNDED_CONTEXT:    token-and-secret-guard             # JWT · CSRF · CSP · Rate Limiting · Vault · Audit
RUNTIME:            server · edge · desktop
SORUMLULUK:         Authentication · Authorization · RBAC · Policy · JWT · CSRF ·
                    CSP · Rate Limiting · Vault · Audit
GIRDI:              kimlik talebi (kimlik bilgisi / belge) · yetki isteği
                    (rol + kaynak) · politika sorgusu · token yenileme ·
                    nonce/CSRF verisi · origin/CORS verisi
CIKTI:              karar (allow/deny) + gerekçe · token/nonce · rate-limit kararı ·
                    audit kaydı · hata yanıtı (401/403/429)
IZINLI_BAGIMLILIK:  [K000, K001, K002, K003, K004, K005]   # EK A: izinli=K000-K005
YASAK_BAGIMLILIK:   [K007..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19,
                    BYPASS (asla — .ai/CLAUDE.md §5 K6), SELECT *, ORM, MD5/SHA-1]
DATA_BOUNDARY:      çekirdek veri sınırı (EK A) — credential/audit verisi başka
                    katmanla paylaşılmaz; şema sahibi K005 (coremusic_auth),
                    karar sahibi K006
SECURITY_BOUNDARY:  security=YÜKSEK (EK A) · OWASP Top 10:2025 — A01:2025 Broken
                    Access Control ve A04:2025 Cryptographic Failures K006'ya doğrudan
                    eşlenir; A02/A05/A07/A10 K007/K005 ile örtüşür (F1 §9.1) ·
                    fail-secure varsayılan (SEC14)
FAILURE_MODE:       fail-secure (EK A) — doğrulanamıyorsa RED; fail-open YALNIZ
                    belgelenmiş ve ADR'li senaryoda (F1 SEC14); R10 onay matrisi
OBSERVABILITY:      auth başarı/başarısızlık sayımı · brute-force metriği ·
                    rate-limit tetiklenme · audit trail · alert (F1 §12.1)
TEST:               OWASP ASVS L2 senaryoları (F1 §12.1) · CSRF/CSP/rate-limit
                    birim testi · prod'da bypass kapalı doğrulaması · kapsam ≥80%
                    (§17 — PHPUnit ^10.5)
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 99-102 |
                    .ai/CLAUDE.md §5 K6 satırı (115) + §6 (pipeline) + §7 Guardrail
                    #6 (csrf_token) + §21 (forbidden) + §17 + §29 |
                    shared/src/Security/ (JwtService · CacheRateLimiter ·
                    ReturnUrlPolicy · SecurityHelper · SessionKeys · UuidV7) +
                    shared/src/Middleware/ (11 dosya) + shared/src/Api/Middleware/
                    (6) + shared/src/Session/ (4) + shared/tests/Api/*MiddlewareTest.php |
                    ADR-008 · ADR-010 · ADR-011 · ADR-012 · ADR-013 · ADR-020 ·
                    ADR-022 · ADR-052 · ADR-058 · ADR-059 · ADR-094 · ADR-095 |
                    web: EK B #1 (owasp.org/Top10/2025, güven 100) · #2 (2025
                    Introduction, güven 98)
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — 👤 onayı bekliyor)
```

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 (18 EK C + `kanit-tarihi` + `kart-durumu`) — risk:high → tam kart zorunlu (R4.2) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI = {K000..K005}; YASAK = {K007..K020, H20, H19, bypass} → ∅ |
| (c) KANIT `⚠️` ise research kapısı | HAYIR — 3 ayağın tamamı dolu (repo · ADR · web #1/#2) |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — credential/audit verisi yalnız K005 şemasında, karar K006'da |

**Epitet kalite notu:** «ÇELİK KAPI» EK A §A.0'dan alınmıştır. F1 §12.1'deki
örnek kart epiteti "GÜVENLİK KALESİ"dir — bu **örnek/şablon** durumudur (§12
"ÖRNEKLER & EXEMPLAR"); çelişkide anayasa/EK A kazanır (R8.1). Epitet kimliği
ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K006)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Authentication | Kimlik doğrulama (hybrid JWT + session) | IMPLEMENTED | `shared/src/Middleware/AuthenticationMiddleware.php` · `AuthMiddleware.php` · `shared/src/Security/JwtService.php` · ADR-052 · ADR-058 |
| 2 | Authorization | Kaynak bazlı erişim kararı (allow/deny) | IMPLEMENTED (iskelet) | `shared/src/Middleware/AuthorizationMiddleware.php` · `PermissionMiddleware.php` |
| 3 | RBAC | Rol tabanlı kontrol (regular/premium/studio/car/admin/system) | IMPLEMENTED (iskelet) | `PermissionMiddleware.php` · `.ai/CLAUDE.md` §6 pipeline #9 (RBAC rolleri) |
| 4 | Policy | Politika sorgusu/ayarları | DESIGN | EK A · `⚠️` ayrı politika dosyası eşleşmedi |
| 5 | JWT | Token üretimi/doğrulama (RS256 access token) | IMPLEMENTED | `shared/src/Security/JwtService.php` · ADR-059 · ADR-095 |
| 6 | CSRF | `csrf_token` doğrulama (POST/PUT/DELETE) | IMPLEMENTED | `shared/src/Middleware/CsrfMiddleware.php` · ADR-010 · ADR-094 · Guardrail #6 |
| 7 | CSP | strict-dynamic + nonce header | IMPLEMENTED | `shared/src/Middleware/SecurityHeadersMiddleware.php` · ADR-012 |
| 8 | Rate Limiting | APCu tabanlı 60 req/60s | IMPLEMENTED | `shared/src/Middleware/RateLimiterMiddleware.php` · `shared/src/Security/CacheRateLimiter.php` · ADR-013 · §11 Port/Register · §12 |
| 9 | Vault | Sır/credential saklama erişimi | PARTIAL | §21 "Hardcoded secret → `.env` / credential vault" · ADR-034 (şema) · vault **erişim kodu** eşleşmedi → `⚠️` |
| 10 | Audit | Kim-ne-ne-zaman kaydı (yetki değişimlerinde) | IMPLEMENTED (yüzey) | F1 SEC11 · `.ai/CLAUDE.md` §18 DB7 (`coremusic_logs`: audit trail) · üretim kuralı K012 |

### 4.2 Anayasa §5 K6 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5, satır 115)

**Satır:** `K6 Güvenlik | JWT, RBAC, CSRF, CSP, RateLimit, AES-256, Vault, Audit | 40 | Hard Guardrail: Asla bypass edilemez`

| Alt kapsam | Kapsam | Durum | Kanıt |
|---|---|---|---|
| JWT | token | IMPLEMENTED | `JwtService.php` · §5 satır 115 |
| RBAC | rol kontrolü | IMPLEMENTED (iskelet) | `PermissionMiddleware.php` |
| CSRF | token doğrulama | IMPLEMENTED | `CsrfMiddleware.php` · Guardrail #6 (`csrf_token`) |
| CSP | başlık/nonce | IMPLEMENTED | `SecurityHeadersMiddleware.php` · ADR-012 |
| RateLimit | 60 req/60s (APCu) | IMPLEMENTED | `RateLimiterMiddleware.php` · §12 |
| AES-256 | şifreleme (AES-256-GCM) | TARGET | §12 Encryption = "AES-256-GCM, Argon2id (NIST SP 800-38D)" — **kod kanıtı okunmadı → `⚠️`** |
| Vault | sır saklama | PARTIAL (§4.1 madde 9) | §21 · ADR-034 |
| Audit | denetim kaydı | IMPLEMENTED (yüzey) | §18 DB7 |
| **Hard Guardrail** | **"Asla bypass edilemez"** | BAĞLAYICI | §5 satır 115 |
| "40 bileşen" | §5 sayım sütunu | **HEDEF** | H10: hedef ≠ kanıt |

### 4.3 Repo envanteri (dosya kanıtlı — güvenlik yüzeyi)

| Dosya/dizin | Ne yapar (ad + vault eşlemesi) | Durum | Kanıt |
|---|---|---|---|
| `shared/src/Security/JwtService.php` | JWT servisi | IMPLEMENTED | dosya yolu (2026-10-08) |
| `shared/src/Security/CacheRateLimiter.php` | cache tabanlı rate limiter | IMPLEMENTED | dosya yolu · §12 APCu |
| `shared/src/Security/SecurityHelper.php` | güvenlik yardımcıları | IMPLEMENTED | dosya yolu |
| `shared/src/Security/ReturnUrlPolicy.php` | return-URL politikası (open-redirect savunması) | IMPLEMENTED | dosya yolu — işlev `[INFERRED]` (ad) |
| `shared/src/Security/SessionKeys.php` | oturum anahtarları | IMPLEMENTED | dosya yolu |
| `shared/src/Security/UuidV7.php` | UUIDv7 üretimi | IMPLEMENTED | dosya yolu |
| `shared/src/Middleware/OriginCheckMiddleware.php` | köken doğrulama | IMPLEMENTED | dosya yolu · §6 pipeline #1 |
| `shared/src/Middleware/CorsMiddleware.php` | CORS header | IMPLEMENTED | dosya yolu · §6 #2 |
| `shared/src/Middleware/RateLimiterMiddleware.php` | rate limit | IMPLEMENTED | dosya yolu · §6 #3 |
| `shared/src/Middleware/SecurityHeadersMiddleware.php` | CSP/HSTS/X-Frame + nonce üretimi | IMPLEMENTED | dosya yolu · §6 #4 · ADR-012 |
| `shared/src/Middleware/SessionManagerMiddleware.php` | oturum + nonce kaydı | IMPLEMENTED | dosya yolu · §6 #5 · ADR-011 |
| `shared/src/Middleware/CsrfMiddleware.php` | `csrf_token` doğrulama | IMPLEMENTED | dosya yolu · §6 #6 · Guardrail #6 |
| `shared/src/Middleware/BypassAuthMiddleware.php` | test bypass (`?_bypass=1`) | IMPLEMENTED (test yüzeyi) | dosya yolu · §6 #7 · ADR-008 — **prod'da devre dışı (§6)** |
| `shared/src/Middleware/AuthMiddleware.php` | auth bilgisi inject | IMPLEMENTED | dosya yolu · §6 #8 |
| `shared/src/Middleware/PermissionMiddleware.php` | RBAC yetki | IMPLEMENTED | dosya yolu · §6 #9 |
| `shared/src/Middleware/ValidationMiddleware.php` | request/DTO doğrulama | IMPLEMENTED | dosya yolu · §6 #10 |
| `shared/src/Middleware/MiddlewarePipeline.php` | pipeline yürütücüsü (K007 alanı) | IMPLEMENTED | dosya yolu · §6 |
| `shared/src/Api/Middleware/*.php` (6) | API varyantları: ApiMiddlewarePipeline · Authentication · Authorization · RateLimit · RequestValidation · ResponseNormalization | IMPLEMENTED | `ls shared/src/Api/Middleware/` |
| `shared/src/Session/*` (4) | SessionBootstrapper · SessionConfig · SessionInitializer · SessionLifecycle | IMPLEMENTED | `ls shared/src/Session/` |
| `shared/tests/Api/*MiddlewareTest.php` (2) | RequestValidation · ResponseNormalization testleri | IMPLEMENTED | `ls shared/tests/Api/` |
| `.ai/.sql/mysql/coremusic_auth.sql` | kullanıcı/rol/oturum/token/credential vault/API key şeması | IMPLEMENTED (şema) | §18 DB1 · `git ls-files` |
| Vault **erişim kodu** (`.env` dışı) | — | EŞLEŞMEDİ | `⚠️` (§4.1 madde 9) |

### 4.4 EK A K006 bloğunun birebir alıntısı (kaynak metin)

```text
### K006 - SECURITY «ÇELİK KAPI» Bant: K000-K020
- Sorumluluk: Authentication · Authorization · RBAC · Policy · JWT · CSRF · CSP · Rate Limiting · Vault · Audit
- Sınır: data=çekirdek veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K005 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 99-102 (In-Place · H4/R7).

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı | credential/audit verisi paylaşılmaz; şema K005'te | §5.3 |
| `security` | **YÜKSEK** | A01/A04 doğrudan eşleme · fail-secure | §5.4 |
| `failure` | **fail-secure** | doğrulanamıyorsa RED; fail-open yalnız ADR ile | §5.5 |
| `izinli` | K000-K005 | kök → AI → veri (hash/veri erişimi) | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) + **bypass** | K007-K020 + H20 + H19 | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | web ayağı EK B #1/#2 | §7 |

### 4.6 OWASP Top 10:2025 — K006 eşlemesi (F1 §9.1 · kaynak EK B #1/#2)

| Madde | K006'daki karşılığı | İlişkili katman(lar) | Durum |
|---|---|---|---|
| A01:2025 Broken Access Control | RBAC · authorization · return-URL politikası | **K006 doğrudan** (+ K024, K044 — band 2+) | F1 §9.1 eşlemesi |
| A02:2025 Security Misconfiguration | başlık/yapılandırma denetimi | K007 (pipeline) + K026 | kısmi |
| A03:2025 Software Supply Chain Failures | bağımlılık taraması (SEC13) | K013 CI/CD | kısmi |
| A04:2025 Cryptographic Failures | AES-256-GCM · Argon2id · JWT imzası | **K006 doğrudan** (+ K042) | F1 §9.1 eşlemesi · kod `⚠️` |
| A05:2025 Injection | prepared statement (M06/SEC08) | K007 + **K005 (PDO)** | F1 §9.1 |
| A06:2025 Insecure Design | tehdit modeli (ADR kapısı) | K023 governance/ADR | kısmi |
| A07:2025 Authentication Failures | AuthN · session/JWT | **K006 doğrudan** | F1 §9.1 |
| A08:2025 Software/Data Integrity | imzalı asset · CI artifacts · firmware | K001/K002/K048 + K013 | kısmi |
| A09:2025 Security Logging & Alerting | audit trail · alert | **K006 (audit) + K12 (üretim)** | F1 §9.1 |
| A10:2025 Mishandling of Exceptions | fail-secure varsayılan (SEC14) | K007 pipeline | kısmi |

### 4.7 Uygulama kuralları (F1 §9.2 SEC01-SEC14 — bu kartın bağlayıcı kuralları)

| Kural | Metin (özet) | K006'daki karşılığı | Durum |
|---|---|---|---|
| SEC01 | Her istek Origin/CORS kontrolünden geçer; preflight doğru yanıtlanır | `OriginCheckMiddleware` · `CorsMiddleware` | IMPLEMENTED |
| SEC02 | CSRF: çift gönderim token veya SameSite=Strict + header doğrulama | `CsrfMiddleware` (Guardrail #6: `csrf_token`) | IMPLEMENTED |
| SEC03 | CSP: script-src 'self'; inline script yok; eval yok | `SecurityHeadersMiddleware` · ADR-012 (strict-dynamic + nonce) | IMPLEMENTED |
| SEC04 | Rate limiting: kullanıcı + IP kotası (gateway katmanında) | `RateLimiterMiddleware` · `CacheRateLimiter` · ADR-013 (60/60) | IMPLEMENTED |
| SEC05 | Oturum: kısa ömürlü JWT + yenileme; token httpOnly cookie | `JwtService` · `SessionManagerMiddleware` · ADR-052 · ADR-095 | IMPLEMENTED |
| SEC06 | Sırlar kodda DEĞİL; ortam değişkeni / vault; repo taraması CI'da | §21 · `secret-scan.yml` (`.github/workflows/`) · ADR-034 | IMPLEMENTED (workflow dosyası mevcut) |
| SEC07 | Şifre: Argon2id (veya bcrypt ≥12); MD5/SHA-1 yasak | §12 Encryption · F1 H05 | TARGET (`⚠️` kod okunmadı) |
| SEC08 | SQL: yalnız prepared statement; LIKE/ORDER BY allowlist | K005 (PDO) + K006 politikası | IMPLEMENTED (PDO yolu) |
| SEC09 | Dosya yükleme: uzantı allowlist, boyut, yeniden adlandırma, webroot dışı | K006 politikası + K009/K015 | DESIGN (`⚠️` — kod eşleşmedi) |
| SEC10 | signed URL: Expires + KeyName + Signature; kısa ömür; directory-bazlı | K006 + K009/K015 | DESIGN (`⚠️`) |
| SEC11 | Audit: kim-ne-ne-zaman tüm yetki değişimlerinde (A09) | `Audit` (EK A) · §18 DB7 | IMPLEMENTED (yüzey) |
| SEC12 | Hata mesajı sızıntısı yok (stack trace kullanıcıya asla dönmez) | K006 + K007 | DESIGN (`⚠️`) |
| SEC13 | Bağımlılık taraması (A03): lock dosyası + CVE kontrolü CI gate | K013 · `.github/workflows/secret-scan.yml` | PARTIAL (`⚠️` — CVE gate eşleşmedi) |
| SEC14 | Exception handling A10: **fail-secure varsayılan**; fail-open yalnız belgelenmiş ve ADR'li | EK A failure=fail-secure | BAĞLAYICI |

### 4.8 Sınır ilişkisi: K006 ↔ K007 (güvenlik kararı vs. yürütme sırası)

| Boyut | K006 (bu kart) | K007 (komşu) | Ayırıcı kural |
|---|---|---|---|
| Ne üretir | **karar** (allow/deny), token, politika | **sıra** (pipeline adımları) | `.ai/CLAUDE.md` §6 · EK A K007 |
| Sıra | Pipeline sırasını **değiştiremez** | sahibi | §6 "Middleware sırası DEĞİŞTİRİLEMEZ" |
| Nonce | üretimi `SecurityHeaders` içinde (#4) | session'a kaydı (#5) | §6 "Sıra değiştirilirse CSP bozulur" |
| Bypass | **yok** (asla) | `BypassAuth` (#7) test mekanizması — prod'da devre dışı | §5 K6 guardrail · ADR-008 |
| Denetim | ihlalde revert + log | ihlalde revert + log | anayasa §5.1 |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = K000-K005 · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| K000 | alt katman | kripto API'si / OS servisleri |
| K001 | alt katman | donanım arayüzü (credential/secure element yok → yalnız port) |
| K002 | alt katman | sürücü (imzalı firmware doğrulaması arayüzü) |
| K003 | alt katman | ses motoru (RT-safe — güvenlik kararı üretmez) |
| K004 | alt katman | AI (prompt/üretim güvenliği; sır enjeksiyonu yok — SEC06) |
| K005 | alt katman | **salt hash/veri** erişimi (F1 §12.1: "K5 Data (salt hash/veri)") |
| K006 port/adapter | port/adapter | üst katmanlar K006'yı port üzerinden çağırır |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20 · §5 K6 guardrail)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K007-K020'ye doğrudan erişim | EK A satır 101 | Layer Violation → revert + log CRITICAL |
| Geri çağrı (H20) | F1 §5.1 H20 | yalnız port/adapter |
| Doğrudan veri paylaşımı (H19) | F1 §5.1 H19 · YARGI 2 | data boundary ihlali |
| **Bypass (asla)** | `.ai/CLAUDE.md` §5 K6 Hard Guardrail | mutlak yasa |
| `SELECT *` · ORM · framework | ADR-001/002 · §21 · F1 H01-H03 | Frozen ADR |
| MD5/SHA-1 · hardcoded credential | F1 H04/H05 · §21 | sert kural |
| `_csrf_token` (tireli yazım) | Guardrail #6 | yalnız `csrf_token` |
| localStorage/sessionStorage ile auth | §21 Forbidden Patterns | session-based auth (HTTPOnly cookie) |

**Olay (event) yukarı serbest:** güvenlik olayları (başarısız deneme, rate-limit
tetiklenme) K012'ye event ile yayılabilir (R6.2 · F1 §9.1 A09); senkron
çağırı yukarı yasaktır.

### 5.3 DATA_BOUNDARY (YARGI 2)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 101) |
| K006'in verisi | karar durumları, nonce/token meta verisi (bellek/oturum içi) |
| Kalıcı veri | `coremusic_auth` (users, roles, sessions, tokens, credential vault, API keys) → **şema sahibi K005**, **içerik kararı K006** (ADR-034) |
| Paylaşılan tablo | YOK |
| Hash/veri | F1 §12.1: K006 → K005 bağımlılığı **"salt hash/veri"** ile sınırlıdır |

### 5.4 SECURITY_BOUNDARY (EK A: security=YÜKSEK)

| Konu | Değer |
|---|---|
| Seviye | **YÜKSEK** (EK A satır 101) — band-1'de tek |
| OWASP eşlemesi | A01 · A04 doğrudan; A07 (AuthN); A09 (audit) — F1 §9.1 |
| Bağlayıcı kurallar | SEC01-SEC14 (§4.7) · M06/M07 · Guardrail #6 |
| Şifreleme | AES-256-GCM · Argon2id (§12 — NIST SP 800-38D) [TARGET, `⚠️` kod] |
| Devre dışı bırakılamaz | bypass — "Asla bypass edilemez" (§5 satır 115) |
| Onay | güvenlik eşik değişikliği 👤 + ADR (R10) |

### 5.5 FAILURE_MODE (EK A: fail-secure)

| Senaryo | Davranış | Kaynak | Durum |
|---|---|---|---|
| Doğrulama başarısız / belirsiz | **RED** (fail-secure) | EK A · F1 SEC14 | BAĞLAYICI |
| Token süresi doldu | reddet + yenileme akışı | ADR-052 · ADR-095 | IMPLEMENTED (tasarım) |
| Rate limit aşıldı | 429 + tetiklenme kaydı | ADR-013 | IMPLEMENTED |
| fail-open | **YOK** — yalnız belgelenmiş + ADR'li senaryo | F1 SEC14 · R10 | bağlayıcı |
| Sessiz hataya geçme | YASAK (hata sızıntısı da yasak — SEC12) | F1 SEC12 | bağlayıcı |

### 5.6 Observability / Test sınırı

Log/metrik **üretim kuralı** K012'dedir (R7.1); audit **verisi**
`coremusic_logs` şemasında (K005), **karar** K006'dadır. Test stratejisi K013
CI/CD + §17 (PHPUnit ^10.5 — ≥80% min · ≥90% hedef).

### 5.7 Port/Adapter deseni (F1 §8.2 — K006'ya uygulaması)

| Port tipi | Yön | Örnek | Adapter | Durum |
|---|---|---|---|---|
| Inbound | K007/K008 → K006 | yetki/kimlik karar portu | middleware adapter'ı | IMPLEMENTED (`PermissionMiddleware` vb.) |
| Inbound | K005 → K006 | hash/veri okuma portu | PDO/repository | IMPLEMENTED |
| Outbound | K006 → K005 | hash yazma / audit kaydı | repository | IMPLEMENTED (yüzey) |
| Kural | — | adapter değişince güvenlik kararı değişmez (F1 §8.2) | — | bağlayıcı |
| Kural | — | K006 doğrudan UI'ı çağıramaz (H20) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti (ör. bypass'ın prod'da etkin olması; K006 → K010 doğrudan erişim) | R6.1 · §5 K6 guardrail |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 |
| 3 | `log.md` CRITICAL girişi | anayasa §5.1 |
| 4 | 👤 bilgi (güvenlik olayı — öncelikli) | R6.5 |
| 5 | Döngü → `dep-check` exit 1 → ADR | R6.6 |
| 6 | Kardeş ilişki `refers-to` (düz metin K-ID) | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| Kimlik talebi | K010/K009 (istemci) | kimlik bilgisi / belge | IMPLEMENTED (Auth pipeline) |
| Yetki isteği | K007 (middleware) | rol + kaynak | IMPLEMENTED (`PermissionMiddleware`) |
| Politika sorgusu | K008/K009 | politika | DESIGN (`⚠️`) |
| Token yenileme | K010/K009 | refresh token | IMPLEMENTED (ADR-052/095) |
| Nonce/CSRF verisi | K007 (Session) | token | IMPLEMENTED (ADR-012/010) |
| Origin/CORS verisi | istemci | header | IMPLEMENTED (`OriginCheck`/`Cors`) |

### 6.2 Çıktı

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| Karar (allow/deny) + gerekçe | K007 (devam/yasak) | IMPLEMENTED (iskelet) |
| Token / nonce | K007 (CSP nonce session'a kaydedilir) | IMPLEMENTED (§6 pipeline #4-#5) |
| Rate-limit kararı | K007 | IMPLEMENTED |
| Audit kaydı | K005 (`coremusic_logs`) · K012 | IMPLEMENTED (yüzey) |
| Hata (401/403/429) | K009/K010 | IMPLEMENTED (tasarım) |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | server · edge · desktop (EK C) |
| Uçak | SOFTWARE |
| Kimlik mimarisi | hybrid JWT + session (ADR-052) · RS256 access token (ADR-095) · merkezî auth servisi (ADR-058) · MFA (ADR-059) |
| Port | auth.coremusic.net (`.ai/CLAUDE.md` §9/§10) [TARGET] |
| Deployment modları | tüm modlar (§14) — güvenlik kararları ortaktır |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | üretim kuralı K012 (R7.1) · audit verisi K005 | sahiplik ayrımı (§5.6) |
| Metric | auth başarı/başarısızlık · brute-force · rate-limit tetiklenme | IMPLEMENTED (yüzey) · ölçüm `⚠️` |
| Trace | istek correlation id (§6A.1 gateway) | TARGET (K009) |
| Health | auth servis sağlık durumu | DESIGN |
| Alert | F1 §12.1 "alert" · K012 | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| OWASP ASVS L2 senaryoları | kimlik/oturum/CSRF/rate limit | senaryo geçişi (F1 §12.1) | PLANNED |
| CSRF/CSP/rate-limit birim testi | her middleware | otomatik test geçişi | PARTIAL (`shared/tests/Api/*MiddlewareTest.php` var — kapsam `⚠️`) |
| Bypass kapalı doğrulama | prod yapılandırması | `?_bypass=1` etkisiz | PLANNED (§6 #7 bağlamı) |
| Sır taraması | repo | hardcoded secret = 0 | PARTIAL (`.github/workflows/secret-scan.yml` mevcut) |
| Kapsam | §17 | ≥80% min · ≥90% hedef (PHPUnit ^10.5) | hedef |
| Güvenlik kapısı | F1 §11.1 kategori 4 | **≥90/100** | bağlayıcı |

### 6.6 F1 §8.4 YARGI'larının K006'ya uygulaması

| Yargı | Kural | K006 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | yalnız alt katman tanınır | K000-K005 | GEÇERLİ |
| YARGI 2 | DATA BOUNDARY | credential/audit verisi münhasır; şema K005'te | GEÇERLİ |
| YARGI 3 | SECURITY BOUNDARY | **YÜKSEK** (bu kart sınırın kendisidir) | GEÇERLİ |
| YARGI 4 | OBSERVABILITY | auth · brute-force · rate-limit · audit [yüzey IMPLEMENTED] | GEÇERLİ |
| YARGI 5 | FAILURE_MODE | **fail-secure** | GEÇERLİ |
| YARGI 6 | EVENT BOUNDARY | güvenlik olayları yukarı serbest | GEÇERLİ |

### 6.7 Bilinen açık maddeler ve riskler (K006'ya özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | Vault **erişim kodu** eşleşmedi (yalnız ADR-034/§21 referansı) | Vault maddesi PARTIAL | kod incelemesi / `⚠️` |
| 2 | AES-256-GCM / Argon2id kod kanıtı okunmadı | §4.2 `⚠️` | kod incelemesi |
| 3 | SEC09/SEC10 (dosya yükleme / signed URL) kod kanıtı yok | DESIGN | kod incelemesi |
| 4 | SEC13 CVE gate (bağımlılık taraması) eşleşmedi; yalnız secret-scan var | PARTIAL | CI gate → 👤 |
| 5 | Policy (madde 4) ayrı dosya yok | DESIGN | tasarım + kod |
| 6 | `BypassAuth` prod'da kapalı doğrulaması yok | güvenlik riski | §6.5 testi |
| 7 | "40 bileşen" hedefi sayım'a bağlı değil | H10 | hedef ≠ kanıt ayrı raporla |

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 99-102 | K006 kartı: Sorumluluk 10 madde · Sınır (YÜKSEK · fail-secure) · Kanıt | GEÇERLİ |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 51 (§A.0) | K006 · SECURITY · «ÇELİK KAPI» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K6 satırı (satır 115) | JWT · RBAC · CSRF · CSP · RateLimit · AES-256 · Vault · Audit · 40 · "asla bypass edilemez" | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §6 (10 adımlı middleware pipeline + nonce notu) · §7 Guardrail #6 · §7.1 | pipeline sırası · `csrf_token` zorunluluğu | GEÇERLİ |
| 5 | Anayasa (vault) | `.ai/CLAUDE.md` §17 (PHPUnit ^10.5 · ≥80/≥90) · §21 · §23 · §12 (AES-256-GCM, Argon2id) · §18 DB1/DB7 | test hedefi · yasaklılar · uyarılar · şifreleme · şemalar | GEÇERLİ |
| 6 | Repo (dosya yolu) | `shared/src/Security/` (6 dosya: JwtService · CacheRateLimiter · ReturnUrlPolicy · SecurityHelper · SessionKeys · UuidV7) | güvenlik servisleri | GEÇERLİ |
| 7 | Repo (dosya yolu) | `shared/src/Middleware/` (11 dosya) + `shared/src/Api/Middleware/` (6 dosya) | pipeline bileşenleri (§4.3 eşlemesi) | GEÇERLİ |
| 8 | Repo (dosya yolu) | `shared/src/Session/` (4 dosya) · `shared/tests/Api/` (2 test dosyası) | oturum + test yüzeyi | GEÇERLİ |
| 9 | Repo (dosya yolu) | `.github/workflows/secret-scan.yml` · `ci.yml` | sır taraması / CI | GEÇERLİ |
| 10 | Repo (dosya yolu) | `.ai/.sql/mysql/coremusic_auth.sql` | kullanıcı/rol/oturum/token/credential vault/API key şeması | GEÇERLİ |
| 11 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-010-csrf-protection-strategy.md` · `ADR-011-session-management.md` · `ADR-012-csp-nonce-strict-dynamic.md` · `ADR-013-rate-limiting-apcu.md` | CSRF · session · CSP nonce · rate limit | GEÇERLİ |
| 12 | ADR (`ls`) | `ADR-008-bypass-auth-middleware.md` · `ADR-020-api-public-security.md` · `ADR-022-database-hardened-security.md` | bypass (test) · public API güvenlik · DB sertleştirme | GEÇERLİ |
| 13 | ADR (`ls`) | `ADR-052-hybrid-auth-session-jwt.md` · `ADR-058-centralized-auth-service.md` · `ADR-059-jwt-library-and-mfa.md` | hybrid auth · merkezî auth servisi · JWT + MFA | GEÇERLİ |
| 14 | ADR (`ls`) | `ADR-094-api-pipeline-origin-csrf.md` · `ADR-095-hybrid-jwt-rs256-access-token.md` | origin/CSRF pipeline · RS256 access token | GEÇERLİ |
| 15 | Web (EK B #1) | `https://owasp.org/Top10/2025/` (2025, er. 2026-10-08, güven 100) | OWASP Top 10:2025 resmi liste — §4.6 madde adları ve sıralaması | GEÇERLİ |
| 16 | Web (EK B #2) | `https://owasp.org/Top10/2025/0x00_2025-Introduction/` (2025, er. 2026-10-08, güven 98) | A01 CWE oranları · değişiklikler | GEÇERLİ |
| 17 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §9.1 (satır 971-984) · §9.2 (986-1006) · §12.1 (1377-1397) | OWASP eşlemesi · SEC01-SEC14 · K006 örnek kart | GEÇERLİ |
| 18 | Kural | `.ai/architecture/rules.md` R4.2 (risk:high → tam kart zorunlu) · R9 · R10 (onay matrisi) · R14 | kart zorunluluğu · kanıt · onay · research | GEÇERLİ |
| 19 | Repo (grep) | Vault erişim kodu · AES/Argon2 kod kanıtı okunmadı (2026-10-08) | §4.1/§4.2 maddeleri `⚠️`/PARTIAL | `⚠️` |

**3'lü kanıt dengesi (R9.2):** repo 5 grup · ADR 12 · web 2 → **3/3 dolu → VERIFIED**
(§4.1 madde 9 Vault, §4.2 AES/Argon2, §4.7 SEC09/SEC10/SEC13 için `⚠️` kalır).

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | VAR | 6 güvenlik + 11 middleware + 6 API middleware + 4 session + 2 test + 1 workflow + 1 şema | IMPLEMENTED (yüzey) |
| ADR (`ls` ile görülen adlar) | VAR | 12 (+ ADR-096) | davranış kararları |
| Web (URL + tarih) | VAR | 2 (#1 · #2) | OWASP madde adları + oranlar |
| Anayasa/kural/spec | VAR | 8 kayıt | kartın taşıyıcısı |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 dolu |
| `STALE` (R11) | gerekmedi | — | tümü 2026-10-08 |

### 7.2 R14 research boşluğu (K006'ya özgü — EK B'ye taşınacak)

| # | Açık soru | İlgili alan | Beklenen kaynak tipi |
|---|---|---|---|
| 1 | OWASP ASVS L2 kontrol listesi (test kriterleri) | §6.5 | owasp.org ASVS + tarih |
| 2 | AES-256-GCM / Argon2id uygulama kanıtı (kod içi) | §4.2 | repo incelemesi |
| 3 | Vault erişim kodu yolu | §4.1 madde 9 | repo incelemesi |
| 4 | Dosya yükleme / signed URL uygulaması (SEC09/SEC10) | §4.7 | repo incelemesi |
| 5 | CVE/bağımlılık tarama gate'i (SEC13) | §4.7 | CI yapılandırması → 👤 |
| 6 | RS256 anahtar yönetimi (ADR-095) uygulaması | §6.3 | kod + doküman |

> Cevap bulunana kadar maddeler `⚠️ VERIFICATION REQUIRED` / `PARTIAL` kalır (H15).

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki |
|---|---|---|
| Alt katmanlar | K000 · K001 · K002 · K003 · K004 · K005 | tek izinli `depends-on` (K005 = salt hash/veri) |
| Üst komşu (doğrudan) | K007 | middleware pipeline — kararı K006, sırası K007 |
| Band-1 kardeşler | K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 · K016 · K017 · K018 · K019 · K020 | `refers-to` (R6.4) — `depends-on` DEĞİL |
| Güvenlik tüketicileri | K007 · K009 · K010 · K015 | karar port/adapter ile |
| Gözlem iş birliği | K012 | olay yukarı serbest (audit/alert) |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K006 kartı kaynağı)
- [[architecture/rules]] — R4.2 (risk:high) · R9 · R10 · R14
- [[architecture/00-master-index]] — giriş navigasyonu

>Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check ihlali).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K006 `index.md` (staging) · risk:high → 20 alan tam kart | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K006-guvenlik/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1) |
| `.ai/architecture/K006-guvenlik/*.md` (derinleşme) | gerçek karmaşıklık var: authn/authz · token · rate limit · audit — ayrı dosyalar beklenir | koşullu (R3.1 · F2 §42) |
| `b1-K006-guvenlik.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K006-guvenlik` (R2.2) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) | bağlayıcı |
| min-500 | üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K006-guvenlik/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
