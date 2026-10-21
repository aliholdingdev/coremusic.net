# OWASP Top 10:2025 ↔ CoreMusic Savunma Eşlemesi

> Skill: `security-hardening` v4.0.0 · CoreMusic · Güncel: 2026-10-21
>
> Kaynak: OWASP Top 10:2025 resmi sayfası (erişim 2026-10-21 —
> `references/sources.md` §1). Yerel otorite: `.ai/CLAUDE.md` §6/§7/§21/§23,
> `.ai/.decisions/accepted/` ADR'ler. **Bu dosya skill-içidir — `.ai/`'ye
> yazım yok.**

---

## 1. Sürüm geçişi: 2021 → 2025 (neden bu skill güncellendi)

`.ai/.templates/documentation/security-audit-template.md` §4.1 hâlâ **2021**
listesini taşır (authority: reference — bu görevde okunmadı/düzeltilmedi;
SKILL.md §7.1'deki fark tablosu bu yüzden bağlayıcıdır). Audit raporunu
**2025** listesiyle yaz; şablonun 2021 satırlarını "şablondan geliyor"
diye kopyalama.

| 2021 | 2025 | CoreMusic etkisi |
|---|---|---|
| A01 Broken Access Control | **A01 Broken Access Control** (SSRF geniş içeriğiyle) | RBAC (Permission middleware), OriginCheck, fetch metadata |
| A02 Cryptographic Failures | **A02 Cryptographic Failures** | AES-256-GCM, Argon2id (ADR-022) |
| A03 Injection | **A04 Injection** | PDO parametreli sorgu, `htmlspecialchars`, ReDoS |
| A04 Insecure Design | **A06 Insecure Design** | ADR süreçleri, rate limit tasarımı (ADR-013) |
| A05 Security Misconfiguration | **A05 Security Misconfiguration** | SecurityHeaders, CORS whitelist, CSP nonce |
| A06 Vulnerable/Outdated Components | **A03 Software Supply Chain Failures (YENİ)** | Composer audit, bağımlılık zinciri |
| A07 Identification and Auth Failures | **A07 Identification and Authentication Failures** | Argon2id, MFA (ADR-059), rate limit, session regen |
| A08 Software and Data Integrity Failures | **A08 Software and Data Integrity Failures** | JWT imzası, unserialize yasağı, dosya bütünlüğü |
| A09 Logging and Monitoring Failures | **A09 Logging and Monitoring Failures** | security event log (ADR-010 §2 olay listesi) |
| A10 SSRF | **A10 Mishandling of Exceptional Conditions (YENİ)** | try/catch fail-open, error leakage |

---

## 2. Kategori ↔ CoreMusic savunma ↔ kaynak dosya

| OWASP 2025 | CoreMusic savunması | Kanıt (dosya/ADR) | Skill kaynağı |
|---|---|---|---|
| **A01** Broken Access Control | Auth → Permission pipeline; OriginCheck; CORS whitelist; RBAC | `.ai/CLAUDE.md` §6 sıra; ADR-009 §2 | §3, references/middleware-security-order.md |
| **A02** Cryptographic Failures | AES-256-GCM (12B IV, 16B tag); Argon2id 64MB/t4/p2; secret yok kodda | ADR-022 §1-2 | references/crypto-standards.md |
| **A03** Software Supply Chain Failures | Composer bağımlılık denetimi; hardcoded bag yok; npm/composer lock | ⚠️ VERIFICATION REQUIRED (CI evidence kanıtı vault'ta yok) | §7.4 |
| **A04** Injection | PDO prepared; `htmlspecialchars` (charset öncesi); `eval/exec` veto | `.ai/CLAUDE.md` §21; kural 8/9 | references/forbidden-patterns.md §1.1 |
| **A05** Security Misconfiguration | 10-header sabiti; CSP nonce (ADR-012); CORS `*` yasağı (ADR-009) | `.ai/CLAUDE.md` §6; ADR-012 §1-2 | SKILL.md §6, references/owasp-mapping §5 |
| **A06** Insecure Design | Rate limit sunucu tarafı (ADR-013); temiz-katman; ADR-001..037 donmuş | ADR-013 §1 | §6, §7.4 |
| **A07** Identification & Auth Failures | Argon2id; login sonrası `session_regenerate_id(true)`; MFA TOTP; rate limit | ADR-022/027/059; kural 7/10 | references/crypto-standards.md §3, §5 |
| **A08** Software & Data Integrity Failures | JWT imzalı (hybrid ADR-052); `unserialize(user_input)` yasak; imzasız paket | `.ai/CLAUDE.md` §5 (PHP kuralı); ADR-052 | §7.4, forbidden-patterns §1 |
| **A09** Logging & Monitoring Failures | `bootstrap_engineering.security.<event>` olay adları; CSRF/Origin/Bypass log | ADR-010 §2; ADR-055 §2; ADR-035 §2 | §4.4, §7.5 |
| **A10** Mishandling of Exceptional Conditions | Fail-closed her yer (CSRF/419, RateLimiter/429, Origin/403); exception sızıntısı yok | `.ai/CLAUDE.md` §7 (fail-safe); rule 15 | §6.4, forbidden-patterns §1.4 |

---

## 3. Saldırı senaryoları (girişim → savunma → tespit)

> Her senaryo: **saldırgan ne yapar → hangi katman karşılar → hangi olay log'lanır.**
> Log adları ADR'lerdeki `bootstrap_engineering.security.*` desenine uygundur;
> event-name tam eşleşmesi vault kanıtı olanlar için geçerlidir, olmayanlar
> **⚠️ VERIFICATION REQUIRED** (event adı uydurulmadı — "log + event" şeklinde
> işaretlenmiştir).

### 3.1 CSRF token hırsızlığı (A01/A08)

1. **Girişim:** Saldirgan third-party sayfada `<img src="...">` ile
   state-changing POST tetikler; kullanıcı cookie'si (SameSite=Lax
   cross-site POST'ta gönderilmez — CSS/CSRF Cheat Sheet §3: SameSite
   *depth* katmanı) veya subdomain sızıntısıyla cookie taşınır.
2. **Karşılayan katman:** OriginCheck (7/10, önce) + CSRF (7/10, sonra):
   `Origin` farklıysa **403** (ADR-035 §2); Origin yoksa token zorunlu —
   `hash_equals` başarısız → **403** (ADR-010 §2).
3. **Tespit:** `security.csrf_failure` → **⚠️ VERIFICATION REQUIRED**
   (ADR-010 §2 olay listesi okunmadı; desen `bootstrap_engineering.security.<event>`).
4. **Kalan risk:** Aynı-origin XSS varsa CSRF anlamsız (XSS önce çözülür).

### 3.2 Rate limit bypass / brute-force (A07/A06)

1. **Girişim:** 10k istek/dk login endpoint'ine; script password denemeleri.
2. **Karşılayan:** RateLimiter (3/10) — APCu fixed window `60/60s`,
   `apcu_inc` fail-closed (sayaç yoksa reddet → ADR-013 §1 fail-open yasağı).
3. **Tespit:** 429 + `Retry-After: 60` başlığı; login başarısızlık sayacı
   (ADR-059 §2 MFA başarısızlık kilitleri — sayaçlı).
4. **Bypass girişimleri:** (a) kimlik kestirme (IP spoof `X-Forwarded-For`) →
   sağlayıcı gateway'den gelen IP-only (ADR-013 §2); (b) client_id kestirme →
   API client id DB'den doğrulanır (ADR-013 §2); (c) header-değiştirerek
   pencere sıfırlama → pencere başlangıcı `apcu_add` atomik (ADR-013 §2 —
   race-edge SKILL.md §10.3).

### 3.3 CSP nonce sızıntısı (A05/A03)

1. **Girişim:** XSS ile saldırgan script enjekte etmeye çalışır; nonce
   olmadan inline script `report-only`'de bile çalışır (ADT-009).
2. **Karşılayan:** Nonce SecurityHeaders'da üretilir (4/10), session (6/10),
   CSRF hidden input (7/10), controller view (8/10) — 2. kez yazılmaz
   (ADR-012 §2). Saldırgan nonce'u HTML'den okuyamaz (script execution
   kendi script'inde nonce yok → engellenir). Log + event: **⚠️ VERIFICATION
   REQUIRED** (ADR-012 §2 olay kaydı doğrulanmadı).
3. **Artık-yetimli:** `unsafe-inline` yok (ADR-012 §1 yasağı).

### 3.4 Vault dump (A02/A08)

1. **Girişim:** DB sızıntısı → credential sütunlarını okuma.
2. **Karşılayan:** AES-256-GCM (ADR-022 §1): key `.env`/vault ayrıdır
   (ADR-024 §2) → dump tek başına yetersiz.
3. **Tespit:** Log'da plaintext yok (sütun log'lanmaz); key erişimi
   ADR-024 §2 `@coremusic.net` hesabı kısıtı.
4. **Bypass:** key dump'la birlikte (aynı host) → şifreleme koruması düşer;
  bu yüzden ADR-024 §1 "kod içi key / dış servise key gönderimi" veto.

---

## 4. Audit denetim checklist'i (hızlı tarama — read-only)

> Her satır: **komut/gözlem → beklenen → bulgu yoksa PASS**. Bulgu varsa
> `path:line` + `.ai/reports/security/` raporu (fix owner: backend-architect).

| # | Kontrol | Komut/gözlem | Beklenen | Sonuç |
|---|---|---|---|---|
| 1 | CSRF token adı | grep `_csrf_token` (kod) | 0 isabet | ☐ PASS/BLOCK |
| 2 | CSRF read-only regen | `grep -n "regenerate" <Csrf>` | yalnız `writeToken` dışında regen YOK; `readToken`'da regen yok | ☐ |
| 3 | Hash yerine plaintext token | grep `hash.*csrf_token` | ADR-020 §1 ihlali (email token) | ☐ |
| 4 | `eval/exec/preg_replace e` | grep bu desenler | 0 isabet | ☐ |
| 5 | `PASSWORD_DEFAULT`/md5/sha1 parola | grep `password_hash\|md5(\|sha1(` | Argon2id explicit | ☐ |
| 6 | AES modu | grep `aes-256-cbc\|ecb\|des-` | GCM/ChaCha yoksa BLOCK | ☐ |
| 7 | Hardcoded secret | grep `password *= *['"]\|secret *= *['"]\|api_key` (kod) | 0 (env/vault) | ☐ |
| 8 | CORS wildcard | grep `Allow-Origin: \*` | 0 (ADR-009) | ☐ |
| 9 | Origin'siz by-pass | `OriginCheck::handle()` okuma | Origin yoksa diğer 2 kanal + reddetme sırası §1.4 | ☐ |
| 10 | Rate limit limiter | `RateLimiter` kodu | APCu, 60/60s, fail-closed | ☐ |
| 11 | CSP nonce zinciri | `grep -rn csp_nonce shared/src` | 4 dosya (SecurityHeaders/Session/Csrf/Controller) | ☐ |
| 12 | `unsafe-inline`/`unsafe-eval` | `grep -rn "unsafe-" shared/src` | report-only'de geçici注 + süre + ADR | ☐ |
| 13 | BypassAuth prod | `BypassAuth::handle()` | prod → 403 + dashboard log | ☐ |
| 14 | Session regen (login) | Auth controller | yalnız `password_verify` BAŞARISINDAN sonra | ☐ |
| 15 | Pipeline dışı auth/permission | `grep -rn "Auth::check\|hasPermission" Controller` | 0 (kural 26) | ☐ |
| 16 | `unserialize(` user input | grep | 0 (PHP kuralı §5) | ☐ |
| 17 | `htmlspecialchars` charset | grep çağrılar | 3. arg charset/ENT_SUBSTITUTE | ☐ |
| 18 | Log sızıntısı | grep `var_dump\|print_r` prod kodu | 0 | ☐ |
| 19 | Session timeout | Auth/session kodu | idle+absolute val (ADR-011 §2 — değerler ⚠️) | ☐ |
| 20 | `hash_equals` kullanımı | grep `===` secret karşılaştırması | hash_equals (kural 11) | ☐ |

**Çıktı biçimi:** `.ai/reports/security/[YYYY-MM-DD]-security-audit.md` —
şablon `.ai/.templates/documentation/security-audit-template.md` (Guardrail
#16: §frontmatter-prompt-yellow notu: rapor README/şablona gömülmez; OWASP
kisminda 2025 listesi kullan — §7.1).

---

## 5. Header matrisi (tam yetki)

| Header | Değer (CoreMusic) | Kaynak | Bozarsan ne olur | tür |
|---|---|---|---|---|
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` | `.ai/CLAUDE.md` §6 | ssl-strip / downgrade (A02/A05) | zorunlu |
| `Content-Security-Policy` | nonce-üret + `default-src 'self'`; `unsafe-inline` YOK | ADR-012 §1-2 | XSS açık (A03/A05) | zorunlu |
| `X-Content-Type-Options` | `nosniff` | §6 | MIME-sniff XSS (A05) | zorunlu |
| `X-Frame-Options` | `DENY` | §6 | clickjacking (A01) | zorunlu |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | §6 | URL/PII sızıntısı (A01) | zorunlu |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=()` | §6 | istenmeyen cihaz erişimi (A05) | zorunlu |
| `Cross-Origin-Opener-Policy` | `same-origin` | §6 | cross-origin pencereleri (A05) | zorunlu |
| `Cross-Origin-Resource-Policy` | `same-origin` | §6 | cross-origin kaynak okuması | zorunlu |
| `Cross-Origin-Embedder-Policy` | `require-corp` | §6 | COEP'siz cross-origin embed | zorunlu |
| `X-XSS-Protection` | `0` | §6 | eski XSS auditor'u kendi XSS'i (A05 — koruma DEĞİL) | zorunlu |
| `Cache-Control` | `no-store, private` (auth yanıtları) | §6 | proxy cache'de oturum sızıntısı (A01) | zorunlu |
| `Access-Control-Allow-Origin` | whitelist exact-match + `Vary: Origin` | ADR-009 §2 | wildcard = ADR-009 §1 ihlali | koşullu |
| `Retry-After` | `60` (429'da) | ADR-013 §2 | istemci tekrar deneme fırtınası | koşullu |
| `Set-Cookie` | `HttpOnly; Secure; SameSite=Lax; Path=/` (+ session) | ADR-011 §2 | cookie hırsızlığı (A07) | zorunlu |

`report-only` → `enforce` geçişi ve `unsafe-*` geçici yorumu: SKILL.md §6.4.

---

## 6. A03 (Supply Chain) — CoreMusic'e ilk kez giren kategori

2021'de ayrı satır yoktu; 2025'te **A03 Software Supply Chain Failures**.
CoreMusic yüzeyi:

| Yüzey | Denetim | Durum |
|---|---|---|
| Composer (`vendor/`) | `composer audit` / lock değişikliği review | ⚠️ VERIFICATION REQUIRED (CI kanıtı yok) |
| npm (varsa) | lockfile + audit | ⚠️ VERIFICATION REQUIRED (npm yüzeyi bu oturumda tespit edilmedi) |
| Bağımlılık ekleme | yasak: onaysız bağımlılık (Faz 1) | kural 4 — PASS (kural olarak) |
| Post-install script | composer `scripts` denetimi | ⚠️ VERIFICATION REQUIRED |
| Paket kaynağı | yalnız resmi registry (composer.json `repositories`) | ⚠️ VERIFICATION REQUIRED |

**Kural:** yeni bağımlılık = onay + sürüm sabitleme + kaynak denetimi;
`vendor/` commit politikası vault'ta aranır (bulunamazsa UNKNOWN).

---

## 7. Kapsam dışı (bu dosya cevaplamaz)

- A03 CI evidence akışının tam mantığı (§6).
- ASVS 5.0.0 bölüm numaraları (sources.md §4 ⚠️).
- Vault şablonunun 2021→2025 güncellemesi (şablon FROZEN — düzeltilmesi
  ayrı görev/ADR gerektirir).
