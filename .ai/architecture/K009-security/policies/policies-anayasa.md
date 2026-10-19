---
node_id: K009-security/policies/policies-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/policies
  - K009-security/security-master
evidence: "policies/index.md §1-§2 (bağlayıcı liste) · .ai/CLAUDE.md satır 112-113/336/682/687/719/721 · .ai/AGENTS.md satır 93/204/222/577 · ADR-010 satır 104/123-126/180 · Password.php 14/28-30/40-48/56-60 · CsrfMiddleware.php 24/42/49-63 · SecurityHeadersMiddleware.php 27-35/54-56/71-73"
validation: "2026-10-15 — 18 bağlayıcı politika satırı (MUST/SHOULD + kaynak + ihlal sonucu) · RFC2119 §2 · 0 uydurma kanıt"
open_questions: "Parola min 8 vs OWASP 12+ ⚠️ ONAY BEKLİYOR (index satır 21/49) · index'in `.ai/CLAUDE.md §Security` atfı diskte YOK (2026-10-15 başlık taraması 0 isabet) ⚠️ · OriginCheck fail-open (OriginCheckMiddleware.php:39-41) ADR-010 fail-closed ile çelişir ⚠️ · KVKK saklama politikası metni vault'ta yazılmamış ⚠️"
group: mimari
---

# K009 · policies · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Rol: mimari — bağlayıcı politika seti (RFC2119 diliyle yazılmış MUST/SHOULD satırları).
> Kural: her satır = **politika + kaynak (dosya·satır) + ihlal sonucu (kapı/kırmızı)**. Kaynaksız politika sayılmaz; doğrulanamayan kaynak `⚠️` ile işaretlenir.

## §1 AMAÇ VE KAPSAM

| Alan | Değer |
|---|---|
| node_id | K009-security/policies/policies-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | parola · kripto · sır (secret) · katman · fail-safe · OWASP · CSRF · CSP/güvenlik başlığı |
| Kapsam dışı | kimlik deposu tasarımı (⚠️) · OS/patch politikası (K000-isletim-sistemi) · sürüm/deploy gate (K17-devops) |
| Bağlayıcılık | Bu dosyadaki MUST satırı vault'ta bağlayıcıdır; kod bu satırı ihlal ederse kapı RED/BLOCK ile durur (§12) |
| Sürüş | `policies-surucu.md` (politika → kod → test zinciri) |
| Kanıt | `policies-ornekler.md` (gerçek PHP/vault alıntıları) · `policies-dogrulama.md` (AD-xx) |

**Kaynak hiyerarşisi (bu dosyada):**

| Sıra | Kaynak | Not |
|---|---|---|
| 1 | `.ai/.decisions/accepted/*` (ADR) | ADR-010 accepted — frozen değil, değiştirilebilir |
| 2 | `.ai/CLAUDE.md` / `.ai/AGENTS.md` | Anayasa + agent standartları (SSOT: `.ai/CLAUDE.md` §2.1) |
| 3 | Subdomain `AGENTS.md` (`shared/`, `auth.coremusic.net/`) | Klasör kuralı |
| 4 | `policies/index.md` §1-§2 | Bu çocuğun bağlayıcı politika listesi |
| 5 | Web (RFC/NIST/OWASP/ISO/KVKK) | Yalnız kural gerekçelendirmesi — kod kanıtı değildir (`policies-web-arastirma.md`) |

## §2 RFC2119 DİLBİLGİSİ (bağlayıcı sözcükler)

> Kaynak: `https://www.rfc-editor.org/rfc/rfc2119.html` (HTTP 200 · 2026-10-15 — `policies-web-arastirma.md` §1).

| Anahtar kelime | Türkçe bağlam | Anlamı (bu dosyada) | İhlali |
|---|---|---|---|
| **MUST** | Zorunlu / kesin | Bu satır tartışmasız uygulanır; istisna yalnız ADR ile açılır | Kapı RED veya BLOCK (§12) |
| **MUST NOT** | Kesin yasak | Bu satırın yapılması yasaktır | Kapı RED — kod revert (`.ai/AGENTS.md` §18 satır 222) |
| **SHOULD** | Önerilir / varsayılan zorunlu | Yapılmazsa gerekçe yazılırsa geçerli; gerekçesiz sapma ihlaldir | Kapı BLOCK + gerekçe istenir |
| **SHOULD NOT** | Önerilmez | Yapılırsa gerekçe + risk kaydı gerekir | BLOCK |
| **MAY** | Serbest | Seçime bağlı; bu dosyada MAY satırı **yoktur** | — |
| (not) | RFC 2119'e ek olarak RFC 8174 belirsizlik kaldırır | "should" küçük harf belirsizdir; bu dosyada yalnız büyük harf kullanılır | — |

**Yazım kuralı:** her politika satırı büyük harf MUST/SHOULD ile başlar; yanında **kaynak** (dosya·satır) ve **ihlal sonucu** (kapı) bulunur. Kaynak `⚠️` ise politika yine bağlayıcıdır ama **uygulanıp uygulanmadığı kanıtlanmamıştır** → `policies-dogrulama.md` AD satırına düşer.

## §3 POLİTİKA SÖZLÜĞÜ (TR / EN / Kod)

| Türkçe | English | Kod karşılığı |
|---|---|---|
| Bağlayıcı politika | Binding policy | Vault satırı + kapı |
| Kapı (gate) | Gate | Test/inceleme RED · BLOCK · PASS |
| Kırmızı | Red / revert | Kod revert edilir (`.ai/AGENTS.md` §18-6) |
| Fail-closed | Fail-closed | Eksik bilgide RED (BypassAuth `return []`) |
| Fail-open | Fail-open | Eksik bilgide geçme — **MUST NOT** (P-09) |
| Sır | Secret | `.env` / credential vault — `.md`/`.json`/`.log` **MUST NOT** |
| Katman ihlali | Layer violation | Geri alma + log ERROR |
| Zaman-duyarlı karşılaştırma | Timing-safe compare | `hash_equals()` |
| İstek sahteciliği | CSRF | `csrf_token` (ADR-010) |
| İçerik güvenlik politikası | CSP | nonce + `strict-dynamic` |

## §4 BAĞLAYICI POLİTİKA LİSTESİ (P-01 .. P-18)

| # | Politika (MUST/SHOULD satırı) | Kaynak (dosya·satır) | İhlal sonucu |
|---|---|---|---|
| P-01 | Parola **MUST** en az 8 karakter olmalıdır (repo sabiti) | `auth.coremusic.net/include/Domain/ValueObject/Password.php:14` + `:28-30` | ValidationException → kayıt RED (kapı: auth testleri) |
| P-02 | Parola hash'i **MUST** Argon2id olmalıdır; başka algoritma **MUST NOT** kullanılır | `Password.php:44` · `auth.coremusic.net/AGENTS.md:56` · `.ai/CLAUDE.md:336` | RED — kod revert |
| P-03 | Pepper **MUST** uygulanır; pepper tanımsızsa **MUST** fail-closed davran (hash üretme / verify=false) | `Password.php:40-42` · `:56-58` | Kapı BLOCK — configError/false |
| P-04 | Ham parola **MUST NOT** DB'ye veya log'a yazılır | `Password.php:9` (doküman) · `auth.coremusic.net/AGENTS.md:63` · `shared/tests/Unit/Log/LoggerRedactionTest.php:74` | RED + sızıntı olayı (securityEvent) |
| P-05 | Şifreleme **MUST** AES-256-GCM veya ChaCha20-Poly1305 kullanır | `policies/index.md` §1 satır 35 (`.ai/CLAUDE.md §Security` atfı) · `.ai/CLAUDE.md:336` (AES-256-GCM) | RED |
| P-06 | MD5 / SHA-1 / DES **MUST NOT** kriptografik amaçla kullanılır | `policies/index.md` §1 satır 35 · `K009-security/security-master.md:49` | RED — kod revert |
| P-07 | Sır (secret) `.md`/`.json`/`.log`'a **MUST NOT** yazılır; **MUST** `.env` / env substitution kullanılır | `.ai/CLAUDE.md:687` · `.ai/CLAUDE.md:721` · `auth.coremusic.net/AGENTS.md:61` | Kapı RED + olay kaydı; otorite: security-engineer veto |
| P-08 | Katman bağımlılığı **MUST** tek yönlüdür; K6 güvenlik doğrulaması **MUST NOT** atlanır | `.ai/CLAUDE.md:112-113` · `.ai/AGENTS.md` §5 (satır 93) | Layer violation → **derhal revert + log ERROR** (`.ai/AGENTS.md:204` / `:222`) |
| P-09 | Güvenlik kontrolünde **MUST** fail-closed uygulanır; fail-open **MUST NOT** kullanılır (istisna: ADR ile yazılı genel uçlar) | `ADR-010-csrf-protection-strategy.md:104` · `BypassAuthMiddleware.php:29-30,42-43` · `RateLimiterMiddleware.php:20-21,55-57` | Kapı RED (bilgi belirsizse istek durur) |
| P-10 | OWASP Top 10:2025 uyumu **MUST** %100'dür (Security satırı) | `.ai/AGENTS.md` §16 (satır 571-577) | Kapı BLOCK — QA gate |
| P-11 | CSRF anahtarı **MUST** `csrf_token` olmalıdır; `_csrf_token` **MUST NOT** kullanılır | `.ai/CLAUDE.md:682` · `CsrfMiddleware.php:42` · `shared/tests/Middleware/CsrfMiddlewareTest.php:11` | RED |
| P-12 | State-changing her istek **MUST** CSRF token taşır (GET/HEAD/OPTIONS hariç); token **MUST** `hash_equals` ile karşılaştırılır | `CsrfMiddleware.php:24` · `:42` · `ADR-010:123` | 403 `csrf_invalid` + `securityEvent` (CsrfMiddleware.php:49-63) |
| P-13 | CSRF **MUST** üç katmandır: synchronizer token + SameSite + Origin/Referer **fail-closed** | `ADR-010:104` · `ADR-010:123-126` · `ADR-010:180` | Kapı BLOCK — ADR şartı açık (PLANNED kalemler §15) |
| P-14 | CSP **MUST** nonce + `strict-dynamic` üretir; middleware sırası **MUST NOT** değiştirilir | `.ai/CLAUDE.md:207` · `.ai/CLAUDE.md:719` · `SecurityHeadersMiddleware.php:54-56` | RED — CSP nonce bozulur (`.ai/CLAUDE.md:719`) |
| P-15 | Güvenlik başlıkları **MUST** gönderilir: nosniff · DENY · strict-origin-when-cross-origin · frame-ancestors 'none' · form-action 'self' | `SecurityHeadersMiddleware.php:27-35` · `:71` · `:73` | Kapı BLOCK (test: başlık varlığı) |
| P-16 | Hassas veri log'da **MUST** `[REDACTED]` ile maskelenir | `.ai/AGENTS.md` §17 satır ~199 (Sensitive data log → `[REDACTED]`) · `LoggerRedactionTest.php:74-82` | RED — sızıntı olayı |
| P-17 | Auth yüzeyi **MUST** fail-closed rate-limit'e tabidir; genel uçlar ADR-013 ile fail-open kalır (gerekçeli) | `RateLimiterMiddleware.php:15-16` · `:20-21` · `:55-57` · `:66-67` | Kapı BLOCK — ADR-013 şart 1a |
| P-18 | Bypass / bypass kapsam genişletme **MUST NOT** yapılır; yalnız ADR ile değişir | `shared/AGENTS.md:85` · `BypassAuthMiddleware.php:29-31` | RED — kapsam genişletme yasak (ADR-008) |

> **Atıf notu (Zero-Hallucination):** `policies/index.md` §1 satır 34-36 kaynak olarak `.ai/CLAUDE.md §Security`'yi gösterir. 2026-10-15 başlık taraması (`.ai/CLAUDE.md` içinde 78 başlık) **`§Security` başlığına 0 isabet** vermiştir → atıf `⚠️ VERIFICATION REQUIRED`. Yerine geçen **gerçek** bağlayıcı satırlar: §5 K6 (satır 113), §12 (satır 336), §21 (satır 682, 687), §23 (satır 719, 721). Politikaların kendisi bağlayıcıdır; yalnızca kaynak satırı düzeltilmelidir.

### §4.1 Alt Politika Sayım Özeti (§5-§11)

| Grup | Üst politika | Alt politika (§) | Sayı | Birincil kapı | Doğrulama AD |
|---|---|---|---|---|---|
| Parola | P-01..P-04 | §5 (P-01a-c, P-02a-b, P-03a-b, P-04a-b) | 9 | auth test / RED | AD-09..AD-11 |
| Kripto | P-05..P-06 | §6 (P-05a-c, P-06a-d) | 7 | security-audit / RED | AD-16 |
| Sır | P-07 | §7 (P-07a-f) | 6 | olay kaydı / RED | AD-17 |
| Katman | P-08 | §8 (P-08a-f) | 6 | revert + log ERROR | AD-06 |
| Fail-safe | P-09 | §9 (P-09a-f) | 6 | RED / OPEN | AD-08, AD-14, AD-15 |
| OWASP | P-10 | §10 (P-10a-e) | 5 | QA gate BLOCK | — (gate) |
| CSRF | P-11..P-13 | §11 (P-11a, P-12a-d, P-13a-b) | 7 | 403 / BLOCK | AD-01..AD-05 |
| CSP/başlık | P-14..P-15 | §11 (P-14a-b, P-15a-b) | 4 | başlık testi BLOCK | AD-12, AD-13 |
| Log | P-16 | §4 (tek satır) | 1 | RED | AD-07 |
| Rate-limit | P-17 | §4 (tek satır) | 1 | BLOCK | AD-15 |
| Bypass | P-18 | §4 (tek satır) | 1 | RED | AD-08 |
| **Toplam** | **18 üst** | **§5-§11** | **50 alt** | §12 sınıfları | 17 AD |

## §5 PAROLA POLİTİKASI (detay)

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-01a | Min uzunluk **MUST** 8'dir (`MIN_PASSWORD_LENGTH = 8`) | `Password.php:14` | ValidationException (`:29`) → RED |
| P-01b | Uzunluk ihlali **MUST** `ValidationException::passwordTooShort` fırlatır | `Password.php:28-30` | Sessiz kabul yasak → BLOCK |
| P-01c | OWASP 12+ önerisi ile repo 8 arasındaki fark **MUST** açıkça kayıtlıdır | `policies/index.md:21` · `:49` | Karar yoksa `⚠️ ONAY BEKLİYOR` — kapı kapatılmaz |
| P-02a | Hash algoritması **MUST** `PASSWORD_ARGON2ID` | `Password.php:44` | RED |
| P-02b | Argon2id maliyeti **MUST** memory 65536 / time 4 / threads 2 | `Password.php:45-47` | Sapma → BLOCK (ADR-022 zinciri) |
| P-03a | Pepper boşsa hash üretimi **MUST NOT** devam eder | `Password.php:40-42` (`configError('APP_PEPPER')`) | fail-closed ihlali → RED (P-09) |
| P-03b | Pepper boşsa doğrulama **MUST** `false` döner | `Password.php:56-58` | fail-open ihlali → RED |
| P-04a | Ham parola `Password` VO dokümanında **MUST** saklanmaz (entity'ye asla gitmez) | `Password.php:9` | Kanıt yoksuzluğu → `⚠️` (test: `LoggerRedactionTest`) |
| P-04b | Log/redaksiyon **MUST** `password=` değerini `[REDACTED]` yapar | `LoggerRedactionTest.php:74` | RED |

## §6 KRİTİF ALANLAR (Kripto)

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-05a | AES-256-GCM **MUST** birincil simetrik şifrelemedir | `.ai/CLAUDE.md:336` · `policies/index.md:35` | RED |
| P-05b | ChaCha20-Poly1305 **MUST** destekli alternatiftir | `policies/index.md:35` | — |
| P-06a | MD5 **MUST NOT** kriptografik amaç kullanılır (checksum vb. de tartışmalı → varsayılan yasak) | `K009-security/security-master.md:49` | RED |
| P-06b | SHA-1 **MUST NOT** kullanılır | `security-master.md:49` | RED |
| P-06c | DES / 3DES **MUST NOT** kullanılır | `security-master.md:49` | RED |
| P-06d | ECB mod **MUST NOT** kullanılır | `encryption-kapsam.md:136` (E-06) | RED |
| P-05c | Kripto seçim gerekçesi **MUST** vault ADR'sinde kayıtlıdır | ADR-022 (kripto) — `.ai/.decisions/index.md` üzerinden | Kayıtsız seçim → BLOCK |

## §7 SIR (SECRET) POLİTİKASI

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-07a | Secret **MUST NOT** kaynak kodda hardcoded olmalıdır (`.md`/`.json`/`.log` dahil) | `.ai/CLAUDE.md:687` · `:721` | RED + olay ("Veri sızıntısı") |
| P-07b | Secret **MUST** `.env` veya credential vault üzerinden okunur | `.ai/CLAUDE.md:687` | Eksikse fail-closed (P-09) |
| P-07c | `config/.env` içeriği **MUST NOT** vault'a veya chat'e yazılır | `auth.coremusic.net/AGENTS.md:61` | Guardrail ihlali → oturum durdurma |
| P-07d | Session/cookie değeri **MUST NOT** log'a yazılır | `auth.coremusic.net/AGENTS.md:63` | RED |
| P-07e | Log maskesi **MUST** `[REDACTED]` formatındadır | `LoggerRedactionTest.php:74-82` · `:127` | BLOCK |
| P-07f | Token/secret ADR metnine **MUST NOT** yazılır (REDACTED politikası) | `ADR-010:97` | RED |

## §8 KATMAN POLİTİKASI

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-08a | Katman bağımlılığı **MUST** tek yönlüdür (Presentation → Application → Domain → Infrastructure) | `.ai/AGENTS.md` §5 (satır 93 — dosya tipi/sorumlu tablosu) | Layer violation → revert (P-08d) |
| P-08b | K7 middleware, K6 güvenlik doğrulamasını **MUST NOT** atlar | `.ai/CLAUDE.md:112` | RED |
| P-08c | K6 Güvenlik katmanı **MUST** "Asla bypass edilemez" Hard Guardrail'idir | `.ai/CLAUDE.md:113` | RED — bypass üretimi yasak |
| P-08d | Layer violation **MUST** derhal revert + `log ERROR` ile kaydedilir | `.ai/AGENTS.md:204` · `:222` | Sistem durur, MO müdahale eder |
| P-08e | `shared/src/` içine subdomain'e özel iş kuralı **MUST NOT** gömülür | `shared/AGENTS.md:83` (Yasak 1) | RED |
| P-08f | Yeni middleware **MUST** `MiddlewarePipeline` kaydına eklenir | `shared/AGENTS.md:78` · `PageRouterKernel.php:322-341` | BLOCK |

## §9 FAIL-SAFE (FAIL-CLOSED) POLİTİKASI

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-09a | Bilgi belirsizse istek **MUST** reddedilir (fail-closed) | `ADR-010:104` · `:180` | fail-open → RED |
| P-09b | Bypass credential'larından biri eksikse bypass **MUST** devre dışı kalır | `BypassAuthMiddleware.php:29-31` | `[]` yerine kimlik üretimi → RED |
| P-09c | Config boşsa handle() **MUST** sahipsiz akıtmaz (ikinci fail-closed) | `BypassAuthMiddleware.php:42-43` | RED |
| P-09d | Auth uçlarında rate-limit cache çözülemezse **MUST** fail-closed | `RateLimiterMiddleware.php:55-57` | RED |
| P-09e | Genel uçlarda fail-open **SHOULD** yalnız ADR-013 gerekçesiyle kalır + log zorunlu | `RateLimiterMiddleware.php:66-67` | Log'suz fail-open → BLOCK |
| P-09f | Origin başlığı yoksa istek **MUST** reddedilir (ADR-010 katman 3) | `ADR-010:179-180` — **kodda ihlal**: `OriginCheckMiddleware.php:39-41` geçer | ⚠️ PLANNED — §15 |

## §10 OWASP POLİTİKASI

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-10a | Security kalite standardı **MUST** "OWASP Top 10, CSRF=`csrf_token`, Argon2id" ve hedef **MUST** %100 | `.ai/AGENTS.md` §16 satır 577 | QA gate BLOCK |
| P-10b | Yasaklı kalıplar (ORM, `SELECT *`, `eval`, hardcoded secret, `localStorage` auth) **MUST NOT** kullanılır | `.ai/CLAUDE.md:680-691` (§21 Forbidden Patterns) | RED |
| P-10c | `_csrf_token` adı **MUST NOT** kullanılır; doğrusu `csrf_token` | `.ai/CLAUDE.md:682` | RED (test: `CsrfMiddlewareTest:330`) |
| P-10d | OWASP bağlamı (A01-A10) **MUST** K009 risk envanteriyle eşleştirilir | `K009-security/security-riskler.md` · `security-master.md:49` | Eşleşmeyen risk → `⚠️` |
| P-10e | Parola politikası OWASP CS (12+ önerisi) ile karşılaştırılır; fark **MUST** onaylanır | `policies/index.md:49` | ONAY BEKLİYOR — kapı açık |

## §11 CSRF VE CSP POLİTİKASI

| # | Politika | Kaynak | İhlal |
|---|---|---|---|
| P-11a | Token anahtarı **MUST** `csrf_token` | `.ai/CLAUDE.md:682` · `CsrfMiddlewareTest.php:11` | RED |
| P-12a | GET/HEAD/OPTIONS **MUST** hariç; kalan her metot token ister | `CsrfMiddleware.php:24` | Kapsam dışı metot → BLOCK |
| P-12b | Karşılaştırma **MUST** `hash_equals` (timing-safe) | `CsrfMiddleware.php:42` | `==`/`strcmp` kullanımı → RED |
| P-12c | Başarısızlık **MUST** 403 + `csrf_invalid` + securityEvent | `CsrfMiddleware.php:49-63` | Sessiz 403 (log yok) → BLOCK |
| P-12d | Bypass route listesi **MUST** boş kalır (set-gender kaldırıldı) | `CsrfMiddleware.php:15-17` | Liste eklemek yalnız ADR ile → RED |
| P-13a | Katman 2: oturum cookie'si **MUST** `SameSite=Lax` + HttpOnly | `ADR-010:124` (`SessionInitializer.php:40-47`) | RED |
| P-13b | Katman 3: Origin yoksa Referer, ikisi yoksa **MUST** 403 | `ADR-010:125` · `:180` | PLANNED (kod fail-open §9 P-09f) |
| P-14a | CSP **MUST** nonce üretir ve `'strict-dynamic'` kullanır | `SecurityHeadersMiddleware.php:54-56` · `.ai/CLAUDE.md:207` | RED |
| P-14b | Nonce üretimi SecurityHeaders (#4) içinde **MUST** olur; sıra **MUST NOT** değişir | `.ai/CLAUDE.md:207` · `MiddlewarePipelineTest.php:13` | RED — test kırmızı |
| P-15a | `frame-ancestors 'none'` ve `form-action 'self'` **MUST** gönderilir | `SecurityHeadersMiddleware.php:71,73` | BLOCK |
| P-15b | XSS savunması **MUST** `Require-Trusted-Types-For: 'script'` + `X-Content-Type-Options: nosniff` içerir | `SecurityHeadersMiddleware.php:27,32` | BLOCK |

## §12 İHLAL SONUÇ SINIFLARI (kapı / kırmızı)

| Sınıf | Ne zaman | Sonuç | Kaynak |
|---|---|---|---|
| **RED (kırmızı)** | MUST / MUST NOT ihlali; katman ihlali; hardcoded secret; MD5/SHA-1/DES; `_csrf_token` | Kod **revert** edilir; iş devam etmez | `.ai/AGENTS.md:222` · §24.4 |
| **BLOCK** | SHOULD ihlali; kanıtsız iddia; eksik test; log'suz fail-open | Kapıda durur; gerekçe veya kanıt istenir | `policies-dogrulama.md` AD satırı |
| **DUR (çelişki)** | Kaynak çelişkisi (örn. parola 8 vs OWASP 12+; ADR vs kod) | Üst karar / ADR açılır | `.ai/AGENTS.md` §24.4 (Çelişki → DUR + kullanıcıya sor) |
| **⚠️ işaret** | Doğrulanamayan iddia | Etiketlenir, "uygulandı" sayılmaz | `.ai/AGENTS.md` §18-3 |
| **VETO** | Güvenlik zafiyet tespiti | security-engineer veto kullanır | agent registry (§6 routing) |

## §13 ÖZET MATRİS (politika × kaynak × kapı)

| Politika grubu | Politika # | Birincil kaynak | Kod yüzeyi | Kapı |
|---|---|---|---|---|
| Parola | P-01..P-04 | Password.php · auth AGENTS.md | `include/Domain/ValueObject/Password.php` | Password/Auth testleri |
| Kripto | P-05..P-06 | index §1 · security-master:49 | (encryption çocuğu) | security-audit |
| Sır | P-07 | `.ai/CLAUDE.md:687,721` · auth AGENTS.md:61 | `.env` / Logger / Redactor | LoggerRedactionTest |
| Katman | P-08 | `.ai/CLAUDE.md:112-113` · `.ai/AGENTS.md` §5 | PageRouterKernel pipeline | MiddlewarePipelineTest |
| Fail-safe | P-09 | ADR-010 · RateLimiter/BypassAuth | BypassAuth · RateLimiter · OriginCheck | FailClosedTest |
| OWASP | P-10 | `.ai/AGENTS.md` §16 · `.ai/CLAUDE.md` §21 | genel kod taraması | QA gate |
| CSRF | P-11..P-13 | ADR-010 · `.ai/CLAUDE.md:682` | CsrfMiddleware | CsrfMiddlewareTest |
| CSP/başlık | P-14..P-15 | `.ai/CLAUDE.md:207,719` | SecurityHeadersMiddleware | başlık testleri |

## §14 KAPSAM SINIRI VE DEVİRLER (özet — detay `policies-kapsam.md`)

| Politika | Bu dosyada | Devredilen |
|---|---|---|
| Parola uzunluğu/Argon2id/pepper | Evet (P-01..P-04) | Kayıt/şifre sıfırlama akışı → `K0100-identitiy` |
| Kripto yasağı | Evet (P-05..P-06) | Anahtar üretimi/saklama ayrıntısı → `encryption` · `secrets` |
| Sır yazım yasağı | Evet (P-07) | `.env` envanteri → `secrets` |
| Katman tek yön | Evet (P-08) | OS düzeyi izin → `K000-isletim-sistemi/security` |
| Fail-safe | Evet (P-09) | Rate limit kotası → `rate-limiting` (politika P-17 köprüsü) |
| OWASP | Evet (P-10) | OWASP tarama/testleri → `security-testing` |
| CSRF/CSP | Evet (P-11..P-15) | CI gate / deploy → `K17-devops` |
| KVKK saklama | Kısmi — yalnız çerçeve (§15) ⚠️ | Veri saklama süresi → `K019-comilance` (⚠️ metin yok) |

## §15 BİLİNMEYENLER VE AÇIK SORULAR

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| U-1 | Parola min 8 vs OWASP 12+ kararı | ⚠️ ONAY BEKLİYOR (index:21) | Vault Steward |
| U-2 | `.ai/CLAUDE.md §Security` atfı gerçek bölüm değil | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| U-3 | OriginCheck fail-open (satır 39-41) — ADR-010 katman 3 kapalı değil | ⚠️ PLANNED | security-engineer |
| U-4 | KVKK saklama/ imha politikası metni | ⚠️ VERIFICATION REQUIRED | K020-comilance |
| U-5 | Bearer/JWT doğrulama stub'u (`validateJwtToken` hep null) | ⚠️ (ADR-010:98) | backend-architect |
| U-6 | Parola karmaşıklık/sözlük kontrolü kodda var mı? | ⚠️ KANIT YOK | policies-dogrulama |
| U-7 | `password_needs_rehash` akışı | ⚠️ KANIT YOK (authentication AD-12) | authentication |
| U-8 | CSRF privilege-change rotasyonu | PLANNED (ADR-010:159) | backend-architect |

## §16 DOĞRULAMA SORULARI (her kod incelemesinde)

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Yeni kural MUST/SHOULD satırı + kaynak + kapı taşıyor mu? | Devam | BLOCK (bu dosyaya ekle) |
| 2 | Kaynak dosya·satır diskte okunabiliyor mu? | Devam | `⚠️` + AD satırı |
| 3 | Sır `.md`/`.json`/`.log`'a mı yazıldı? | RED (P-07) | Devam |
| 4 | Fail-open davranış var mı ve ADR ile gerekçeli mi? | Gerekçe yoksa RED (P-09) | Devam |
| 5 | `_csrf_token` / `strcmp` / MD5-DES kullanımı var mı? | RED (P-06/P-11) | Devam |
| 6 | Middleware sırası değişti mi? | RED (P-14b) | Devam |
| 7 | Katman ihlali var mı? | revert + log ERROR (P-08d) | Devam |
| 8 | Test, politikayı kanıtlıyor mu? | Devam | BLOCK (`policies-surucu.md` §5) |

### §16.1 Soru İşletme Rehberi (§16'nın kanıtsal karşılığı)

| # | Soru | Kanıt nerede görülür | Görülmezse |
|---|---|---|---|
| 1 | MUST/SHOULD + kaynak + kapı | §4 satırının 3 sütunu dolu | satır yazılmaz |
| 2 | Kaynak Dosya·Satır okunabilir mi | ilgili dosya açılınca satır | `⚠️` + AD satırı |
| 3 | Sır `.md`/`.json`/`.log`'a mı yazıldı | tarama sonucu (AD-17) | RED |
| 4 | Fail-open gerekçeli mi | ADR satırı (ADR-010/013) | C2 DUR |
| 5 | Yasaklı kalıp (`_csrf_token`, `strcmp`, MD5) | kod/AD-01..03, AD-16 | RED |
| 6 | Sıra değişti mi | `MiddlewarePipelineTest` (AD-06) | C4 revert |
| 7 | Katman ihlali | inceleme notu + log | revert + ERROR |
| 8 | Test politikayı kanıtlıyor mu | koşturulmuş test çıktısı | BLOCK |

## §17 İLİŞKİLİ POLİTİKA BELGELERİ

| Dosya | Rol |
|---|---|
| `policies-surucu.md` | Politika → kod yüzeyi → test zinciri sürüşü |
| `policies-ornekler.md` | Gerçek PHP/vault alıntıları |
| `policies-kapsam.md` | Hangi politika bu çocukta, hangisi devredilmiş |
| `policies-dogrulama.md` | AD-xx kanıt tablosu |
| `policies-bilesenler.md` | Politika-kod eşleme envanteri |
| `policies-web-arastirma.md` | 7 doğrulanmış URL (2026-10-15) |

## §18 ÖNCELİK VE ÇATIŞMA KURALLARI

Kaynak hiyerarşisi §1'dedir (ADR > CLAUDE/AGENTS > subdomain AGENTS > index > web);
bu bölüm çatışma **karar kurallarını** verir.

| # | Çatışma tipi | Karar kuralı | Bilinen örnek (bu dosyada) |
|---|---|---|---|
| 1 | ADR (accepted) vs vault kuralı | ADR kazanır; vault satırı revize edilir (ONAY) | ADR-010 fail-closed (P-09f) vs index atfı |
| 2 | ADR (accepted) vs kod davranışı | DUR — kod uymuyorsa C2 çelişki, üst karar | OriginCheck fail-open (`OriginCheckMiddleware.php:39-41`, AD-14 OPEN) |
| 3 | Vault kuralı vs dış kaynak (OWASP vb.) | Vault kazanır (web yalnız gerekçe); fark ONAY ile kapatılır | Parola min 8 (P-01a) vs OWASP 12+ (P-01c → `⚠️ ONAY BEKLİYOR`) |
| 4 | MUST vs MUST NOT (aynı konu) | DUR + üst karar — yorumla çözülmez | — (bu dosyada bilinen ikinci vaka yok) |
| 5 | DOĞRULANMAMIŞ atıf (§4 notu) | Atıf `⚠️`, politika bağlayıcı kalır; kaynak satır düzeltilir | index §1 `.ai/CLAUDE.md §Security` (0 başlık isabeti) |
| 6 | İki ADR çatışır | L3/üst karar (`.ai/AGENTS.md` §10 — Mimari çelişki) | ADR-010 vs ADR-013 fail-open gerekçesi (P-09e/P-17) |

**Yasak**: çatışma lehine-çözüm icat etmek; “tahmini uyum” yazmak. Doğru davranış:
DUR, `⚠️`, sahibi atamak.

## §19 KANIT VE DURUM SEMANTİĞİ

**Politika durumu** (bu dosyada `status:` ve P satırları):

| Durum | Anlamı | Taşıdığı kapı |
|---|---|---|
| `draft` | Politika yazıldı; uygulama kanıtı toplanmadı | — |
| `verify-required` | Kanıt kısmi/RED; “uygulanıyor” denemez | BLOCK (dogrulama AD) |
| `accepted` | Davranış ADR ile mühürlü | Değişiklik yalnız yeni ADR ile |

**Doğrulama durumu** (`policies-dogrulama.md` AD-xx): `GREEN` (ilk el kanıt okundu) ·
`RED` (kanıt yok/koşturulmadı) · `OPEN` (karar üst mercide).

**⚠️ etiket türleri**:

| Etiket | Anlamı | Nereye düşer |
|---|---|---|
| `⚠️ ONAY BEKLİYOR` | Karar insan merciinde | §15 + dogrulama §6 |
| `⚠️ VERIFICATION REQUIRED` | İddia doğrulanamadı (kaynak yok) | AD satırı + §15 |
| `⚠️ KANIT YOK` | Beklenen kanıt dosyada hiç yok | AD = RED |
| `⚠️ PLANNED` | Politika bağlayıcı, kod henüz uygulamadı | §15 (örn. P-09f/P-13b) |

**Kanıt türleri (güç sırası)**: (1) test koşusu çıktısı → (2) ilk el kod okuması
(Dosya·Satır) → (3) vault kural metni (Dosya·Satır) → (4) dış URL (yalnız
gerekçe) → (5) ikinci el aktarım (asla tek başına kanıt sayılmaz).

## §20 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 18 bağlayıcı politika + RFC2119 §2 + kaynak kanıtları | Vault Steward (subagent) |
| 2026-10-15 | `.ai/CLAUDE.md §Security` atfı `⚠️` olarak işaretlendi (0 başlık isabeti) | Vault Steward (subagent) |
| 2026-10-15 | §18 çatışma kuralları + §19 kanıt semantiği eklendi; eski §18/§19 → §20/§21 | Vault Steward (subagent) |

## §21 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[security-master]] | yukarı | Domen anayasası |
| [[index]] (index) | üst | Bağlayıcı politika listesi §1-§2 |
| [[../encryption/index]] · [[../secrets/index]] | uygulama | Kripto ve sır politikasının kod yüzeyi |
| [[../authentication/index]] · [[../authorization/index]] | uygulama | Parola ve yetki politikası |
| [[../session-security/index]] · [[../rate-limiting/index]] | uygulama | SameSite/oturum · fail-closed rate limit |
| [[../security-testing/index]] | sonrası | Politika uyum testi |
| [[../audit/index]] · [[../threat-model/index]] | gözlem | Olay kaydı · tehdit kapsamı |
| [[index]] | katalog | K009 kök indeksi |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
