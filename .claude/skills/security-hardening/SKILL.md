---
name: security-hardening
description: "Use when changing auth, session, CSRF, CSP, rate-limit, encryption, or security headers in CoreMusic (PHP 8.x + Vanilla JS + MySQL) — Tetikleyiciler: 'güvenlik değişikliği', 'CSRF ekle', 'CSP ayarı', 'auth kodu'."
license: MIT
metadata:
  version: 4.0.0
  previous-version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: security
  tags: [security, owasp, csrf, csp, session, auth, encryption, rate-limit, rbac, coremusic]
  updated: 2026-10-21
---

# security-hardening — CoreMusic Güvenlik Değişiklik Protokolü (v4.0.0)

> Bu skill, CoreMusic'te **güvenlik yüzeyinde herhangi bir değişikliği** (auth · session · CSRF ·
> CSP · rate-limit · şifreleme · security header) bağlayıcı protokolle yürütür. Denetim **okuma**
> işlemidir; kodu güvenlik sahibi domain agent'ı düzeltir (`.ai/.templates/documentation/security-audit-template.md` §1.1).
>
> Format otoritesi: `skill-maker` v3.0 (§5 N1–N10). İçerik otoritesi: `.ai/CLAUDE.md` + ADR-008/010/011/012/013/022/052/058/059.
> Dış kaynak doğrulaması: `references/sources.md` (erişim 2026-10-21).

---

## §1 Genel Bakış — Üç Mutlak Gerçek

Bu üç kural **kaynaksız ihlal edilemez**; çelişen istek DUR + kullanıcı onayı gerektirir
(Guardrail #7/#12/#14, `.ai/CLAUDE.md` §6/§7):

1. **Middleware sırası DEĞİŞTİRİLEMEZ:**

   ```text
   OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
   → BypassAuth → Auth → Permission → Validation → Controller
   ```

   CSP nonce üretimi SecurityHeaders (#4) içindedir, SessionManager (#5) bunu oturuma kaydeder;
   sıra değişirse CSP + CSRF birlikte bozulur (ADR-010/011/012/013/022).

2. **K6 güvenlik katmanı = hard guardrail — asla bypass edilemez** (Layer Violation → revert + log CRITICAL).

3. **Token adı `csrf_token`'dır; `_csrf_token` yasaktır** (Guardrail #6, 2026-05-30).

Ek bağlayıcı gerçekler (aynı otorite):
- **Fail-closed her zaman** — doğrulanamayan istek reddedilir; fail-open genişletme veto nedenidir (§13).
- **BypassAuth prod'da devre dışı** (ADR-008, Soft Constraint #4).
- **Hiçbir gerçek secret/anahtar/token değeri skill dosyalarına yazılmaz** — yalnız `.env` / env-substitution gösterimi (ADR-015; REDACTED).

---

## §2 When-to-use (trigger tablosu)

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| 'güvenlik değişikliği' | Auth/session/CSRF/CSP/rate-limit/header koduna dokunulacağı zaman | ✅ |
| 'CSRF ekle' / 'CSRF güçlendir' | Token veya koruma katmanı eklenirken/değiştirilirken | ✅ |
| 'CSP ayarı' | Politika, nonce veya header değişirken | ✅ |
| 'auth kodu' | Login/session/RBAC/MFA akışı yazılırken | ✅ |
| 'güvenlik denetimi' / OWASP kontrolü | Denetim raporu üretilirken (şablon: Guardrail #16) | ✅ |
| 'rate limit' / brute force | APCu sayaç, 429, login throttling işinde | ✅ |
| Vault `.md` / prompt işi | Belge/prompt üretimi | ❌ → `skill-maker` / `.ai/.templates/` |
| UI mockup / CSS katman işi | Görsel üretim | ❌ → `ui-code-generator` / `.ai/.templates/frontend/` |

**Kapsam dışı:** DB şema denetimi (→ Data Engineer) · CI/CD secret taraması (→ DevOps, `secret-scan.yml`) ·
donanım/driver güvenliği (→ Embedded/Audio HW) · performans testi (→ QA).

---

## §3 Otonom Çalışma Protokolü (bağlayıcı akış)

```text
[1] Change tanımla → [2] Threat check → [3] Order check → [4] Uygula (min. diff)
    → [5] Verify (Truth Mode + test kapısı) → [6] Rapor (severity + dosya:satır + sahip)
```

| Adım | Eylem | Çıktı | Atlarsan ne olur |
|------|-------|-------|------------------|
| 1 | **Change tanımla:** hangi güvenlik yüzeyi değişiyor (tek cümle) | Tek cümlelik change tanımı | Kapsam kayması, ilgisiz dosya değişimi |
| 2 | **Threat check:** bu değişiklik hangi saldırı sınıfını etkiler (XSS · CSRF · brute force · sızıntı · clickjacking · SSRF)? İlgili ADR'yi oku | Sınıf + ADR referansı | Tahminleme → halüsinasyon (Guardrail #3) |
| 3 | **Order check:** pipeline #1–#10'a dokunuyor mu? **Dokunuyorsa DUR** — sıra değişimi yalnız `MiddlewarePipelineTest` + ADR güncellemesi + kullanıcı onayıyla (Guardrail #7/#14). Nonce zinciri (#4 üretir → #5 kaydeder) bozulamaz | Sıra etkisi kararı | Sistem durması / sessiz CSP+CSRF bozulması |
| 4 | **Uygula:** minimal diff; mevcut stil korunur; hard-coded secret, fail-open, inline JS **yok** | Kod değişikliği | K6 katmanı ihlali → veto |
| 5 | **Verify (Truth Mode):** (a) yasaklı kalıp taraması (§13 + `references/forbidden-patterns.md`), (b) test kapısı (§16), (c) her iddia dosya+satır/ADR/kaynak kanıtlı | Kanıt seti | Kanıtsız "bitti" geçersiz (Guardrail #3) |
| 6 | **Rapor:** bulgu/seçim → severity (CRITICAL/HIGH/MEDIUM/LOW) + `dosya:satır` + düzeltme sahibi (security-audit-template §4.5) | Denetim satırı | İzlenebilirlik kaybı; kapanışsız bulgu |

**Denetim–düzeltme ayrımı (zorunlu):** Bu skill denetçi rolündedir — bulgu üretir, **kod değiştirmez**;
düzeltmeyi backend-architect uygular, security yeniden denetleyip kapatır (security-audit-template §1.1, §6.3).

### §3.1 Değişiklik Sınıfları → Tehdit → Zorunlu Kontrol

Adım 2 (threat check) somutlaştırması: yapılacak iş bu tablodan sınıfını bulur; satır yoksa
sınıfı **tanımla + kullanıcıya sor** (tahminleme yasak).

| Değişiklik sınıfı | Tehdit sınıfı | Zorunlu kontrol (minimum) | Otorite |
|---|---|---|---|
| Yeni mutating uç (POST/PUT/DELETE/PATCH) | CSRF | `csrf_token` + `hash_equals` + eksik/yanlış → 403; GET'te state-change yok | ADR-010 |
| Login/logout/şifre-değişikliği | Fixation · credential stuffing | `session_regenerate_id(true)` **yalnız başarıda**; RateLimiter #3 önceden | kural 7, ADR-011, ADR-013 |
| Yeni inline script / event handler | XSS (A04) | nonce 4/10'da üretilir; `unsafe-inline` yok | ADR-012 |
| Yeni harici origin / CORS / subdomain | Cross-origin veri hırsızlığı | exact-match whitelist; `*` yasak | ADR-009 |
| Yeni şifreleme veya PII sütunu | Vault dump (A02) | AES-256-GCM + key `.env`; sütun log'lanmaz | ADR-022/024 |
| Yeni bağımlılık / vendor dosyası | Supply chain (A03) | onay (kural 4) + `composer audit` çıktısı | ⚠️ CI kanıtı |
| Yeni test bypass yolu / `?_bypass` | Auth bypass | prod → 403 + dashboard log; kod prod'da pasif | ADR-008/055, kural 13 |
| Yeni MFA/OTP adımı | OTP interception/bypass | TOTP sunucu tarafı + `hash_equals`; SMS/e-mail taşıma yok | ADR-059 |
| Yeni API client / anahtar | Kimlik kestirme | rotating `X-Client-Key`; kimlik merdiveni (§10.1) | ADR-028, ADR-013 |
| Pipeline'a ekle/taşı/sil | CSP+CSRF sessiz bozulma | **DUR** → onay + ADR (≥088) + `MiddlewarePipelineTest` | Guardrail #7/#14 |
| Log/hata mesajı biçimi | Bilgi sızıntısı (A09/A10) | `[REDACTED]`; 4xx/5xx gövdesinde stack trace yok | security-audit-template §4.3 |

### §3.2 Tipik Talepler → Bağlayıcı Yanıt (diyalog kataloğu)

Bu skill'in bağladığı sık istisna talepler; ❌ satır asla üretilmez:

| Talep | ❌ Yanlış yanıt | ✅ Bağlayıcı yanıt |
|---|---|---|
| "Test için CSRF'yi geçici kapat" | koda `if (false)` / ölü branch | ADR-008 test bypass (test ortamı) — prod'da asla; ya da feature flag **raporlanarak** (§16) |
| "Sayfa hızlı açılsın, `unsafe-inline` ekleyelim" | ekleyip geçme | nonce'lu inline veya harici `/assets/*.js` dosyası (`'self'`); geçici genişletme = ADR notu + süre + rapor |
| "Rate limit 60 → 200'e çıksın" | sessiz düzenleme | orantılı gerekçe + ADR-013 §1 denetimi + brute-force etki notu + kullanıcı onayı |
| "Bu uçta auth olmasın, herkes okusun" | Auth/Permission middleware'ini silmek | Permission'da public-rol tanımı (middleware silme yasak — §6.1) |
| "CORS'a `*.coremusic.net` ekle" | wildcard/regex | exact-match liste kaydı (ADR-009); her alt origin ayrı satır |
| "Parolayı base64 ile saklayalım" | dönüşümü şifreleme sanma | Argon2id **hash** (ADR-022); base64 şifreleme/değildir hash'dir |
| "Login'i hızlandırmak için hash maliyetini düşür" | cost/memory azaltma | Argon2id parametresi donmuş (ADR-022); hız = rate limit + akış optimizasyonu, hash değil |
| "Bulgu düzeltmesini sen yap" (subagent) | kod değişikliği | düzeltme backend-architect'e gider; bu skill **yalnız** raporlar (rule 16) |

---

## §4 Zorunlu Okumalar (references/)

> Kural: bu tabloda olmayan `references/` dosyası = orphan hatadır (skill-maker §5-N4).

| # | Dosya | İçerik | Ne zaman okunur |
|---|-------|--------|-----------------|
| 1 | `references/middleware-security-order.md` | Tam pipeline tablosu · immutability · CSP nonce zinciri · sıra bozulması sonuç matrisi · middleware kayıt/pasif-kod tuzağı | **Her güvenlik değişikliğinde (önce)** |
| 2 | `references/forbidden-patterns.md` | Yasaklı↔doğru kalıp tablosu, snippet'li (token adı, auth storage, secret, eval, innerHTML, unserialize, fail-open, log sızıntısı…) | Kod yazım öncesi + doğrulama adımında |
| 3 | `references/crypto-standards.md` | AES-256-GCM · ChaCha20-Poly1305 · nonce/IV kuralları · Argon2id parametreleri · anahtar saklama · hibrit auth · session · MFA · blind index | Auth/şifreleme/oturum işinde |
| 4 | `references/owasp-mapping.md` | **OWASP Top 10:2025** ↔ CoreMusic kontrol eşlemesi · 2021→2025 farkları · header matrisi · saldırı senaryoları · denetim derinliği | Denetim/rapor üretirken, threat check'te |
| 5 | `references/sources.md` | Dış kaynak doğrulaması (ad · URL · erişim tarihi · ne doğrulandı) — Truth Mode kanıtı | Kaynaklı iddia yazılırken / raporlanırken |
| 6 | `references/changelog.md` | Sürüm geçmişi **3.0.0 → 4.0.0** (git kanıtlı; 1.0.0/2.0.0 izi yok → UNKNOWN) — N10: kök `changelog[]` yok | Sürüm davranışı sorgulandığında |

### §4.1 Okuma Sırası (token ekonomisi — "tümünü oku" yasak)

| Durum | Sıra |
|---|---|
| Hızlı değişim (tek uç, sıra riski yok) | §3.1 satırını bul → ilgili **tek** derinlik dosyası → §16 test kapısı |
| Sıra riski var (pipeline #1-#10) | `middleware-security-order.md` (§2-§4) **önce** → sonra derinlik dosyası |
| Denetim raporu | `owasp-mapping.md` §2-§4 → `sources.md` (her iddia için) → şablon (Guardrail #16) |
| Auth/kripto işi | `crypto-standards.md` §1-§5 → ADR-022/052/059 |
| Şüpheli iddia (ADR/satır/sürüm) | `sources.md` §7 listesi → `⚠️ VERIFICATION REQUIRED` |

Tek dosya okuyarak iş biten yerde ikinci dosya okunmaz (guardrail #2 vault-first değil,
**ihtiyaç anında** okuma — kök AGENTS.md §9 kuralı).

---

## §5 Örnekler (examples/)

> `examples/` zorunludur (skill-maker §5-N5); her örnek girdi → çıktı **tam döngü** gösterir.

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/csrf-change-walkthrough.md` | "CSRF ekle/güçlendir" isteği → threat check → sırasız uygulama (`csrf_token`, session-bound tek token, `hash_equals`) → 9 adımlı test kapısı → ters sıralama neyi kırardı |
| `examples/rate-limit-csp-walkthrough.md` | "Login brute force + CSP sızıntısı" isteği → RateLimiter (#3) ve SecurityHeaders (#4) değişikliği → APCu sayaç + 429 + nonce doğrulaması → token hırsızlığı senaryosu kontrolü |

---

## §6 Middleware Pipeline Özeti (frozen — detay: `references/middleware-security-order.md`)

| # | Middleware | Görev | Güvenlik rolü |
|---|-----------|-------|---------------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist); Origin yoksa Referer, ikisi yoksa **fail-closed 403** | CSRF 3. katmanı (ADR-010 Katman 3) |
| 2 | **Cors** | CORS header yönetimi; `X-CSRF-Token` allowlist | SPA token kanalı (ADR-010) |
| 3 | **RateLimiter** | APCu **60 req/60s** per-IP; aşım → 429 + `Retry-After` | Brute force; oturum maliyeti ödemeden reddetme (ADR-013) |
| 4 | **SecurityHeaders** | CSP nonce üretimi (`bin2hex(random_bytes(32))`) + strict-dynamic, HSTS, X-Content-Type-Options, frame-ancestors, Referrer-Policy, Trusted Types | XSS çalışma-zamanı etkisi (ADR-012) |
| 5 | **SessionManager** | Oturum başlatır; **#4'ün nonce'u `$_SESSION['csp_nonce']`a kaydedilir**; idle 3600s uzatma | Nonce↔oturum bağı (ADR-011) |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE/PATCH); `hash_equals`; eksik/yanlış → 403 `csrf_invalid` | State-changing sahtecilik (ADR-010 Katman 1) |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), **prod'da devre dışı** | TestOnly; genişletme = güvenlik değişikliği (ADR-008) |
| 8 | **Auth** | Auth inject (JWT + Session hibrit); Bearer `Authorization` header asla otomatik gitmez | Kimlik doğrulama (ADR-011/ADR-052) |
| 9 | **Permission** | RBAC: `regular / premium / studio / car / admin / system` | En az yetki |
| 10 | **Validation** | Request/DTO validasyonu | Injection/bozuk girdi kapısı |
| → | **Controller** | İş mantığı (K6 tarafından korunur) | — |

**Dosya kanıtı (disk ölçümü 2026-10-21):** `shared/src/Middleware/` altında **11 PHP dosyası** =
10 middleware + `MiddlewarePipeline.php` (taşıyıcı sınıf). Registry'deki "Middleware ×11" sayımı
taşıyıcı sınıfı içerir; pipeline **10 aşamadır** (CLAUDE.md §6).

### §6.1 Pipeline'a Dokunma Kararı (akış — hepsi "önce DUR" ile başlar)

```text
Değişiklik pipeline'ın herhangi bir satırına dokunuyor mu?
  ├─ HAYIR → §3 akışına normal devam.
  └─ EVET → DUR (Guardrail #7/#14) → soru üçlüsü:
       (a) Yeni middleware mi ekleniyor?
           → ADR taslağı (>=088) + kullanıcı onayı + buildDefaultMiddlewares KAYDI
             (kayıtsız = pasif kod, ADR-013 §2.2f) + MiddlewarePipelineTest yeşil.
       (b) Var olan bir middleware sırada mı taşınıyor mu?
           → veto: ADR-010/011/012/013 zinciri (nonce 4->5, session 5->csrf 6) kırılır;
             alternatif çözüm öner (ör. origin kontrolünü Cors'ta değil OriginCheck'te tut).
       (c) Var olan siliniyor mu?
           → yasak: koruma silinmez (koruma, koruduğu tehdidin kendisidir);
             kaldırma = yeni ADR + security veto review (rule 16).
  Hepsi: sir degisikligi sonra MiddlewarePipelineTest KIRMIZI ise derhal revert (§16).
```

**Onay kanıtı zorunlu:** (a)/(b)/(c) dallarından biri seçildiyse rapora karar satırı:
`karar: <a|b|c> · onay: <kullanıcı|ADR-xxx> · test: MiddlewarePipelineTest=<durum>`.

---

## §7 OWASP Top 10:2025 ↔ CoreMusic Eşlemesi (özet — derinlik: `references/owasp-mapping.md`)

> Doğrulanmış liste: OWASP Top 10:2025 (resmi kaynak — `references/sources.md` #1, erişim 2026-10-21).
> **Dikkat:** 2021 listesiyle karıştırılmaz — kategori adları ve sırası değişmiştir (§7.1).

| # | OWASP 2025 | CoreMusic kontrolü | Kaynak |
|---|------------|--------------------|--------|
| A01 | Broken Access Control (SSRF bu kategoriye katıldı) | RBAC (#9) · OriginCheck fail-closed · whitelist-only SSRF | CLAUDE §6, ADR-010 |
| A02 | Cryptographic Failures | AES-256-GCM (96-bit IV, 16B tag) · Argon2id (64MB/t=4/p=2) · MD5/SHA-1/DES yasak | ADR-022, crypto-standards.md |
| A03 | Software Supply Chain Failures | `composer audit` · GitLeaks `secret-scan.yml` (kanıt: `.github/workflows/secret-scan.yml:19-29`) · bağımlılık onay kapısı (kural 4) | CI çıktısı denetlenmeden ✅ verilmez |
| A04 | Injection | Raw PDO prepared statement tek kapı · `SELECT *` yasak · `eval`/`unserialize` yasak | ADR-002, ADR-022 |
| A05 | Security Misconfiguration | CSP nonce strict-dynamic · HSTS · prod'da BypassAuth kapalı | ADR-012, ADR-008 |
| A06 | Insecure Design | Threat check (§3 adım 2) · fail-closed varsayılan · savunma derinliği | Bu skill §3/§14 |
| A07 | Identification and Authentication Failures | Hibrit auth (session SSOT + JWT ≤15dk) · `session_regenerate_id(true)` · MFA TOTP · rate limit | ADR-011/052/058/059, ADR-013 |
| A08 | Software or Data Integrity Failures | `csrf_token` (synchronizer) · `hash_equals` · HMAC blind index | ADR-010, ADR-022 |
| A09 | Security Logging and Alerting Failures | Structured log · `[REDACTED]` maskesi · secret log'a asla yazılmaz | security-audit-template §4.3 |
| A10 | Mishandling of Exceptional Conditions (YENİ) | Hata akışında fail-closed; 403/429 gövdeleri stack trace sızdırmaz | Bu skill §13/§14 |

### §7.1 2021 → 2025 Kayma Uyarısı (denetim raporu yazarken bağlayıcı)

| 2021 (ESKİ — artık kullanılmaz) | 2025 (GEÇERLİ) |
|----------------------------------|----------------|
| A01 Broken Access Control | **A01** Broken Access Control (SSRF içeri katlandı — aşağıdaki SSRF satırı) |
| A02 Cryptographic Failures | **A02** Cryptographic Failures (aynı sıra) |
| A03 Injection | **A04** Injection (sıra değişti) |
| A04 Insecure Design | **A06** Insecure Design (sıra değişti) |
| A05 Security Misconfiguration | **A05** Security Misconfiguration (aynı sıra — 2021'deki gibi) |
| A06 Vulnerable and Outdated Components | **A03** Software Supply Chain Failures (genişledi) |
| A07 Identification and Authentication Failures | **A07** Identification and Authentication Failures (ad korundu) |
| A08 Software and Data Integrity Failures | **A08** (aynı sıra) |
| A09 Logging and Monitoring Failures | **A09** Security Logging and Monitoring (aynı sıra) |
| A10 SSRF | **A01** Broken Access Control içine katlandı |
| — | **A10** Mishandling of Exceptional Conditions (yeni) |

**Uyarı (vault tutarlılığı):** `.ai/.templates/documentation/security-audit-template.md` §4.1 tablosu
kategorileri **2021 listesini** taşır (A03=Injection, A10=SSRF). Şablon `authority: reference`'tır ve
bu iş kapsamında **dokunulmaz**; denetim raporu üretirken bu skill §7 listesi esas alınır, şablon
§4.1 ile çelişki raporlanır (Guardrail #12 → DUR + sor).

**ASVS referansı:** OWASP ASVS **5.0.0** (May 2025 — `references/sources.md` #4); identifer formatı
`v5.0.0-<chapter>.<section>.<requirement>`; ADR-011'in ASVS 7.2.4 atfı **eski 4.x numaralandırmasıdır** —
5.0.0 karşılığı kanıtlanmadan yeniden numaralandırma yapılmaz (`⚠️ VERIFICATION REQUIRED`).

### §7.2 Kategori Başına Hızlı Denetim Soruları (5 tur, 10 kategori)

Rapor üretirken her kategori için bu sorulara cevap aranır; "bilmiyorum" → `UNKNOWN`/`⚠️`,
**"evet" asla tahminle yazılmaz:**

| Kategori | Hızlı sorular |
|---|---|
| A01 | (1) Bu uç Permission (#9) sırasından geçiyor mu? (2) Nesne sahipliği kontrolü (başkasının id'si 404/403) var mı? (3) OriginCheck fail-closed mu? |
| A02 | (1) Parola Argon2id mi? (2) PII sütunu AES-256-GCM mi? (3) Kodda literal secret 0 isabet mi (§15 komutu)? |
| A03 | (1) Yeni bağımlılık onaysız mı eklendi (kural 4)? (2) `composer audit` çıktısı raporda var mı? (3) GitLeaks workflow hâlâ çalışıyor mu (`secret-scan.yml`)? |
| A04 | (1) Tüm SQL prepared mı? (2) `eval`/`unserialize(user_input)` 0 mi? (3) Çıktı escaping charset-öncesi mi (`htmlspecialchars` + encoding)? |
| A05 | (1) 10 sabit header eksiksiz mi (§9/§15)? (2) `unsafe-inline`/`unsafe-eval` 0 mı? (3) HSTS `max-age=31536000` var mı? |
| A06 | (1) Threat check (§3.1) yapıldı mı? (2) Fail-closed varsayılan mı? (3) Yeni uç için rate limit düşünüldü mü? |
| A07 | (1) Login sonrası `session_regenerate_id(true)` sadece başarıda mı? (2) MFA TOTP sunucu tarafında mı? (3) Brute-force 429 ile mi bitiyor? |
| A08 | (1) Token query string'de taşınıyor mu (yasağı §10)? (2) Paket/imza doğrulaması var mı (JWT)? (3) `hash_equals` her secret karşılaştırmasında mı (kural 11)? |
| A09 | (1) CSRF/Origin/Bypass olayları log'lanıyor mu? (2) Log'da `[REDACTED]` maskesi uygulanıyor mu? (3) Log gövdesinde PII/secret 0 mı? |
| A10 | (1) catch blokları fail-closed (geçme yok)? (2) 4xx/5xx gövdesinde stack trace 0 mı? (3) Kilitlenme/timeout durumunda ne olur — belgelendi mi? |

---

## §8 CSRF Kuralları (özet — snippet'ler: `references/forbidden-patterns.md`, örnek: §5)

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Desen | **Synchronizer token** (stateful CoreMusic için OWASP tercihi) | OWASP CSRF Cheat Sheet |
| Token adı | `csrf_token` — `_csrf_token` yasak | Guardrail #6 |
| Üretim | Sunucu tarafında, CSPRNG: `bin2hex(random_bytes(32))` | ADR-010 |
| Taşıma | Gizli form alanı **veya** `X-CSRF-Token` header; **cookie ile TAŞINMAZ** | OWASP CSRF Cheat Sheet |
| Doğrulama | `hash_equals()` — timing-safe; eksik/yanlış → **403 fail-closed** | ADR-010, CLAUDE §6 #6 |
| Kapsam | GET/HEAD/OPTIONS hariç **her** mutating istek (POST/PUT/DELETE/PATCH) | ADR-010 |
| Bağlama | Token oturuma bağlı; **multi-tab: session-bound tek token** (tüm sekmeler aynı token) | CLAUDE §22, ADR-010 |
| Rotasyon | Privilege change (rol/şifre/email) sonrası token döner → eski sekme 403, JS 1 kez yeniden dener | ADR-010 §2.2c, ASVS (7.x — §7.1 uyarısı) |
| SameSite | `Lax` = **savunma derinliği katmanı**, tek başına CSRF koruması DEĞİLDİR | OWASP CSRF Cheat Sheet |
| Muafiyet | Yalnızca **tekil Bearer** uçlar (tarayıcı `Authorization` header'ını otomatik göndermez); hibrit/cookie-fallback her uçta `csrf_token` zorunlu | ADR-010 §2.2b |
| GET ile state-change | Yasak; zorunlu kalırsa o uç da CSRF kapsamına alınır | OWASP CSRF Cheat Sheet |

**Ek doğrulanmış katman (2026 OWASP CSRF Cheat Sheet):** Fetch Metadata header'ları
(`Sec-Fetch-Site` vb.) modern tarayıcılarda cross-site mutating istekleri reddetmek için **ek**
mitigasyon olarak önerilir — mevcut katmanların yerine **değil**, üstüne eklenir. CoreMusic'te
kullanımı `⚠️ VERIFICATION REQUIRED`: OriginCheck (#1) Fetch Metadata kullanıyor mu kodda teyit
edilmeden bu katman "var" diye yazılmaz.

### §8.1 CSRF Doğrulama Akışı (ADR-010 §2 — karar ağacı)

```text
Mutating istek geldi (POST/PUT/DELETE/PATCH):
  [1] csrf_token yoksa (POST body'de ve X-CSRF-Token header'ında ikisi de) → 400 (kural 15)
  [2] Token var → hash_equals($_SESSION['csrf_token'], gelen)
        ├─ başarısız → 403 + bootstrap_engineering.security.<event> log
        └─ başarılı ↓
  [3] Süre kontrolü: token yaşı > 15 dk → session'dan sil + 403
  [4] BAŞARILI → output validation — session_regenerate_id() YASAK (read-only değil output!)
  [5] Privilege change (rol/şifre/email) sonrası → eski token döner; istemci 1 kez yeniden dener
GET/HEAD/OPTIONS → token doğrulanmaz; writeToken() idempotent basar (ADR-010 §2)
```

**İki tipik kırılma noktası:** (a) [4]'te regenerate → legitimate her istek 403 (ADT-012 `-1`);
(b) [1]'de yalnız POST body kontrolü → header kanalı görmezden gelinir (S44 çift-kanal zorunlu).

---

## §9 CSP Kuralları (özet — ADR-012; spec durumu: `references/sources.md` #2)

| Kural | Değer |
|-------|-------|
| Politika | `script-src 'strict-dynamic' 'nonce-{64hex}'` — nonce her istekte yeniden üretilir |
| Nonce üretimi | **#4 SecurityHeaders**: `bin2hex(random_bytes(32))` (256-bit) |
| Nonce kaydı | **#5 SessionManager** → `$_SESSION['csp_nonce']` → `<meta name="csp-nonce">` + `<script nonce="...">` |
| `unsafe-inline` / `unsafe-eval` | **Kalıcı kapalı** — ADR-012; açmak = veto nedeni (§13) |
| Frame | `frame-ancestors 'none'` (+ legacy `X-Frame-Options` katmanı) |
| Form | `form-action 'self'` |
| Trusted Types | `Require-Trusted-Types-For 'script'` (ADR-012) |
| Spec durumu | CSP Level 3 = **W3C Working Draft** (16 Eylül 2026) — Recommendation **DEĞİL**; Level 2 (2016) Recommendation'dır. `'strict-dynamic'` + nonce L3 davranışına dayanır; L2-only tarayıcı fallback'i `⚠️ VERIFICATION REQUIRED` (ADR-012'de tarayıcı matrisi teyit edilmeden iddia yazılmaz) |

### §9.1 CSP Uygulama Akışı + Report-only Geçişi (ADT-009)

**Nonce ömrü (tek istek):**

```text
[4 SecurityHeaders] random_bytes(32) → header'a 'nonce-...' + request attribute
      ↓ (aynı değer)
[5 SessionManager]  $_SESSION['csp_nonce']
      ↓
[7 Csrf]            hidden input (istemci tekrar aynı değeri gönderebilsin)
      ↓
[8 Controller/View] <script nonce="..."> — response'a 2. kez header YAZMA (ADR-012 §2)
```

**Report-only → enforce geçişi (ADT-009, tek satır kararı):**

| Adım | Yapılan | Kapı |
|---|---|---|
| 1 | Aynı policy `Content-Security-Policy-Report-Only` + `report-uri /csp-report` | rapor uç noktası vault'ta kanıtlanır (`⚠️ VERIFICATION REQUIRED`) |
| 2 | ≥1 gün gözlem: console + rapor | gerçek ihlal 0 kalmalı |
| 3 | Başlık `Content-Security-Policy` (enforce) | `unsafe-inline` hâlâ yok |
| 4 | Rapor hâlâ satır üretiyorsa | kaynağı nonce'la/harici dosyaya taşı → **asla** `unsafe-inline` eklenmez |

**Sık hata:** enforce geçilirken report-only **ikisi birlikte** bırakılırsa iki header yarışı;
geçişte eski başlık silinir (tek policy prensibi).

---

## §10 Rate Limiting & Brute Force (ADR-013)

| Parametre | Değer |
|-----------|-------|
| Sayaç | APCu, per-IP |
| Eşik | **60 istek / 60 saniye** |
| Aşım yanıtı | **429** + `Retry-After` header |
| Konum | Pipeline **#3** — session/auth'dan **önce** (maliyet ödemeden reddetme) |
| Login yüzeyi | Genel eşik üstüne login'e özgü ek throttling `⚠️ VERIFICATION REQUIRED` (ADR-013'te login-specific eşik kodda teyit edilmeden "var" diye yazılmaz) |
| Yeniden kayıt tuzağı | Middleware eklenip `buildDefaultMiddlewares()` kaydına yazılmazsa **pasif kod** — fiilen çalışmaz (ADR-013 §2.2f) |

**Token hırsızlığı ↔ rate-limit etkileşimi (senaryo denetimi):** Token query string'de taşınıyorsa
(ör. `?token=`) → Referer header'ı, proxy/access log, tarayıcı geçmişi ve analytics'e sızar;
sızan token + rate-limit bypass (farklı IP) = hesap ele geçirme. OWASP: synchronizer token
**cookie'de ve URL'de taşınmaz**; log'lara `[REDACTED]` yazılır.

### §10.1 Pencere Sınırı ve Kimlik Keşme Tuzakları (ADR-013 — saha bilgisi)

**Tuzak 1 — fixed window kayması:** pencere `[59sn, 119sn]` iki pencereye düşerse sınır
60+60 = **120 istek / 2×60sn** olur. Değerlendirme: kabul edilebilir (statik limit, ADR-013 §2);
*istisna talep gelirse* → sliding window = **yeni algoritma = ADR değişikliği** (token bucket
zaten §1 yasağı). Sahada kabul: kayma biliniyor, bilinçli.

**Tuzak 2 — interval < window:** 5 istek/sn × 60s = 300/60s. Limit 60 ise istemci hızı
**kural ihlali değil, sayaç mantığı** — 60. istekte kesilir; ama `usleep`-siz toplu script
429'u 0.4sn'de görür → brute-force hâlâ işler. Koruma: **login uçlarına ek sayaç**
(sayaç adımda → §10 Login yüzeyi `⚠️ VERIFICATION REQUIRED`) + `Retry-After` uyumu.

**Tuzak 3 — kimlik keşme (client_id sahteleme):** `X-Client-Id: <rastgele>` her istekte
**yeni kova** açar → limit etkisiz. Zorunlu: client_id **DB'den doğrulanır** (ADR-013 §2
kimlik merdiveni: signed URL → user_id → client_id → ip; doğrulanamayan kimlik → alt basamağa düş).

**Tuzak 4 — IP spoofing:** `X-Forwarded-For` header'ı istemciden gelir → spoof edilebilir.
Zorunlu: **yalnız sağlayıcı gateway'inin yazdığı IP** okunur (ADR-013 §2); uygulama kodunda
`$_SERVER['HTTP_X_FORWARDED_FOR']` doğrudan kullanılmaz (`⚠️ VERIFICATION REQUIRED` —
OriginCheck/RateLimiter kodu bu oturumda okunmadı).

**Denetim sırası (read-only):** (1) `RateLimiter` dosyasını oku → identity() hangi değeri
kullanıyor? (2) `apcu_inc` atomik mi, sayaç yoksa geçiyor mu (fail-open §1.4)? (3) login
uçlarında ek sayaç var mı? (4) 61. istek testi §16.

---

## §11 Session & Auth Kuralları (özet — `references/crypto-standards.md`)

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Cookie | `COREMUSIC_SESS` · **HTTPOnly** · **SameSite=Lax** · HTTPS'te **Secure** · domain `.coremusic.net` (subdomain-geniş — dikkat: ortak cookie yüzeyi, ADR-004) | ADR-011 |
| Timeout | idle **3600s** (CLAUDE §6); ADR-011 hedef düzeltmesi: idle 30 dk / absolute 8 saat — nihai değerler `SessionConfig.php` kodda teyit edilmeden "uygulandı" **denmez** | ADR-011, ⚠️ |
| Kimlik | CSPRNG ≥128 bit; **login'de `session_regenerate_id(true)`** (fixation) | ADR-011 |
| Rotasyon | Zamanlanmış rotasyon (30 dk) + **privilege change'de** oturum + CSRF token yenileme | ADR-011 §2.2c |
| Hibrit auth | Birincil: HTTPOnly session cookie (SSOT — logout/iptal mümkün); API: Bearer JWT kısa ömürlü access (≤15 dk) + rotasyonlu refresh | ADR-011/ADR-052 |
| Merkezî auth | Kimlik doğrulama **yalnız** `auth.coremusic.net` (dağıtık login yasak) | ADR-058, keys.md §4 |
| MFA | TOTP (`pragmarx/google2fa`) — paket sürümü `composer.json`'dan teyit edilir, burada sürüm yazılmaz | ADR-059 |
| Client storage | `localStorage`/`sessionStorage` auth için **yasak**; yalnız HTTPOnly cookie | CLAUDE §21 |
| Bypass | `?_bypass=1` yalnız test ortamı | ADR-008 |

**⚠️ VERIFICATION REQUIRED (ADR-010 §1.1):** `validateJwtToken` stub'unun gerçek imza doğrulamasına
bağlanıp bağlanmadığı kodda teyit edilmeden **Bearer muafiyeti geçerli sayılmaz** (stub `null` dönerse
her istek "kimliksiz" sayılır ve CSRF muafiyeti hatalı uygulanmış olur).

### §11.1 Oturum Saldırı Senaryoları (5 zincir — girişim → katman → tespit)

| # | Zincir | Girişim | Karşılayan katman | Tespit |
|---|---|---|---|---|
| 1 | **Session fixation** | Saldirgan kendi session id'sini kullanıcıya verir; kullanıcı login olur | kural 7: `session_regenerate_id(true)` yalnız `password_verify` BAŞARISINDAN sonra | yeni id → eski id artık geçersiz (403) |
| 2 | **Cookie hırsızlığı (XSS)** | Inline script ile `document.cookie` | HTTPOnly (JS okuyamaz) + CSP nonce script-src | CSP raporu; `document.cookie` okuma hatası |
| 3 | **Cross-site cookie taşıma (CSRF)** | Third-party form POST'u | SameSite=Lax (derinlik katmanı) + OriginCheck 403 + `csrf_token` | `security` log satırı (ADR-035/010) |
| 4 | **Brute-force (A07)** | 10k login denemesi | RateLimiter #3 → 429 + `Retry-After`; MFA TOTP (ADR-059) | 429 sayısında sıçrama + başarısızlık sayacı |
| 5 | **Yetki yükseltme (A01)** | Düşük rolle premium uç | Permission #9 RBAC (`regular..system`) | 403 + rol logu |

**Kural (hiyerarşi):** katman **ekleme** üstüne, mevcut katmanı **kaldırma asla**
(§14 savunma derinliği ilkesi). Örnek ihlal: SameSite'ı tek CSRF savunması sanıp token'ı
kaldırmak → 2025 OWASP'a göre depth-of-defense ihlali (sources.md #3).

---

## §12 Kripto Özeti (tam tablolar: `references/crypto-standards.md`)

| Öğe | Standart |
|-----|----------|
| Veri şifreleme (PII, token) | **AES-256-GCM** — 96-bit (12 bayt) IV, 16 bayt tag, paket `iv‖tag‖ciphertext`; IV asla tekrarlanmaz (benzersizlik = anahtar başına tek seferlik üretim kuralı) |
| Alternatif (eşdeğer sınıf) | **ChaCha20-Poly1305** — AES-NI olmayan platformlar; nonce 12 bayt, tekrar yasak |
| Şifre hashing | **Argon2id** (`PASSWORD_ARGON2ID`) — 64 MB memory, t=4, p=2; doğrulama `password_verify` (timing-safe); manuel sile **PHP 8.0+**'ta yok sayılır |
| bcrypt | Çekirdek kural Argon2id'dir; PHP 8.4'te bcrypt varsayılan cost 10→12 oldu (php.net — `sources.md` #5) |
| Aranabilir PII | normalize + **HMAC blind index** (kısmi eşleşme/sıralama yapılmaz) | ADR-022 |
| Anahtar | `.env` → DI `Config` servisi (ADR-015); kodda/logda hardcode secret **yasak**; hiçbir skill/vault `.md`'ye gerçek anahtar **asla** yazılmaz |
| Eski algoritmalar | MD5 · SHA-1 (hash dışı amaç dahil) · DES/3DES · ECB — **yasak, H001 red** |

### §12.1 Kripto Karar Ağacı (hangi durumda hangi primitive)

```text
Veri saklanacak mı? (DB sütunu, dosya)
  ├─ İnsan parolası → Argon2id (64MB/t=4/p=2) — doğrulama password_verify
  ├─ API anahtarı / credential → AES-256-GCM (12B IV random, 16B tag) — key .env/vault
  ├─ Aranabilir PII (email arama) → normalize + HMAC blind index
  │     (kısmi eşleşme/sıralama YOK — ADR-022; normalizasyon adımı kanıtsız → ⚠️)
  └─ Sadece bozulma (bütünlük, gizlilik değil) → SHA-256 HMAC (kitchen sink'a yer yok)
Kod içinde karşılaştırma mı var? (token/OTP/secret)
  └─ hash_equals() — ASLA === / strcmp (kural 11)
Token üretilecek mi?
  ├─ Email doğrulama (salt-okunur, DB'ye düz yazılır, süre 1 saat) → bin2hex(random_bytes(32))
  ├─ Rate-limit / psikolojik direnç (geri dönülemez) → üret + SHA-256 hash sakla
  └─ CSRF → salt-okunur 256-bit, session'da, 15 dk (ADR-010/020)
```

**H001 red tetikleyicileri (bu ağaçtan sapma):** `md5(`/`sha1(` parola yerine,
`aes-*-cbc`/`ecb`, `base64_encode` = "şifreleme" sanmak, secret'ı kodda tutmak,
`===` ile token karşılaştırmak → **STOP + rapor** (§13).

---

## §13 Yasaklı Kalıp Özeti (tam liste + snippet: `references/forbidden-patterns.md`)

| # | ❌ Yasak | ✅ Doğru | Kaynak |
|---|----------|----------|--------|
| 1 | `_csrf_token` | `csrf_token` | Guardrail #6 |
| 2 | `localStorage`/`sessionStorage` auth | HTTPOnly session cookie | CLAUDE §21 |
| 3 | Hardcoded secret (kod/log/`.md`) | `.env` / credential vault | ADR-015, §21 |
| 4 | `SELECT *` | Explicit column listesi | ADR-022 |
| 5 | `eval()` / `new Function()` | `JSON.parse`, `DOMParser` | §21 |
| 6 | `innerHTML` (untrusted) | `DOMParser` + Trusted Types | §21, ADR-012 |
| 7 | `unserialize(user_input)` | JSON decode | §21 (seri nesne enjeksiyonu) |
| 8 | MD5/SHA-1/DES/ECB | Argon2id / AES-256-GCM | ADR-022 |
| 9 | Fail-open (hata → geç) | Fail-closed (hata → 403/429) | §6, ADR-010 |
| 10 | URL query string'de token | Body/header + `[REDACTED]` log | OWASP CSRF |
| 11 | `Access-Control-Allow-Origin: *` | CORS whitelist yalnız | §12 (rol kuralı) |
| 12 | Inline `<script>` / inline event handler | nonce'lu harici script | ADR-012, veto |
| 13 | ORM (Eloquent/Doctrine) | Raw PDO prepared statement | ADR-002, Guardrail #9 |
| 14 | React/Vue/Angular, `var`, `any` | Vanilla JS, `const`/`let` | ADR-001, Guardrail #10 |
| 15 | Error yanıtında stack trace | Genel mesaj + log'a detay | §4.3 sensitive |

**Veto yüzeyleri (role kuralı — STOP + rapor):** `eval`/`exec`/`shell_exec` · inline JS · hard-coded
secret · fail-open genişletme · wildcard CORS · middleware sırası değişikliği onaysız.

### §13.1 En Sık 6 Hata — Yanlış/Doğru Çiftleri (denetimde ilk bakış)

| # | ❌ Yanlış (üretilmez) | ✅ Doğru (çekirdek satır) | Neden |
|---|---|---|---|
| 1 | `if (!apcu_inc(...)) return;` // sayaç yoksa geç | `if (!$ok \|\| $count > 60) return 429` | fail-open = ADR-013 §1 ihlali (§1.4) |
| 2 | `password_hash($p, PASSWORD_DEFAULT)` | `password_hash($p, PASSWORD_ARGON2ID, ['memory_cost'=>65536,'time_cost'=>4,'threads'=>2])` | PHP 8.4 default = bcrypt (ADR-022 §1) |
| 3 | `header('Content-Security-Policy: ... unsafe-inline ...')` | `'nonce-' . $cspNonce` + `'strict-dynamic'` | ADR-012 §1 — veto |
| 4 | `if ($this->auth->check()) { ... }` (controller içinde, Validation'dan önce) | Akış: pipeline #8 Auth → #9 Permission → #10 Validation → Controller | kural 26 + §3.2 #4 |
| 5 | `$_SESSION['csrf_token'] = bin2hex(random_bytes(32));` // her GET'te | `if (!isset($_SESSION['csrf_token'])) { ... }` | idempotent — ADT-012 `-1` |
| 6 | `echo json_encode(['error' => $e->getMessage()])` // 500'de | Genel mesaj + olay id; detay yalnız sunucu logu | A10/A09 — §14 satır 11 |

**Denetim kısayolu:** bu 6 satırın tersi diff'te görünüyorsa → doğrudan §15.2 raporu yazılabilir
(tehdit sınıfı zaten §3.1'de tanımlı, tekrar analiz gerekmez).

---

## §14 Hata Modları & Savunma Derinliği

| # | Hata modu | Etki | Savunma katmanı |
|---|-----------|------|-----------------|
| 1 | #4 ↔ #5 yer değiştirme | Nonce oturuma kaydedilemez → şablonda boş nonce → tüm inline script bloklanır + nonce oturumsuz (XSS riski) | Pipeline immutability + `MiddlewarePipelineTest` |
| 2 | Csrf (#6), SessionManager (#5) öncesine | `$_SESSION['csrf_token']` boş → legitimate her POST 403 (kesinti) veya doğrulanamazlık (güvenlik boşu) | Aynı |
| 3 | Csrf, Auth (#8) sonrasına | Kimlik kontrolüne kadar token'sız erişim penceresi | Bağımlılık zinciri tablosu (`middleware-security-order.md` §4) |
| 4 | RateLimiter (#3), session/auth sonrasına | Oturum açma maliyeti ödenerek reddetme; brute force oturum kaynaklarını tüketir | ADR-013 |
| 5 | BypassAuth'ın prod'da açık kalması | Yetkisiz erişim — H001 | ADR-008; deploy gate |
| 6 | XSS → CSRF'i etkisizleştirme | XSS her CSRF katmanını yener (OWASP: "any XSS defeats all CSRF mitigations") | CSP nonce + Trusted Types + output escaping |
| 7 | CORS wildcard + cookie | Cross-origin token/veri hırsızlığı | Whitelist-only (§13 #11) |
| 8 | Query string'de token | Referer/log/proxy sızıntısı | §13 #10 + log `[REDACTED]` |
| 9 | Session fixation | Sabitlenmiş oturum kimliği | `session_regenerate_id(true)` |
| 10 | Pasif middleware (kayıtsız) | Koruma fiilen yok ama "var" görünür | ADR-013 §2.2f — kayıt + pipeline testi |
| 11 | Eksik exception handling (A10:2025) | Hata gövdesinde sızıntı / kontrolsüz kilitlenme | Fail-closed catch → genel 4xx/5xx + structured log |
| 12 | Clickjacking | UI redressing | `frame-ancestors 'none'` + X-Frame-Options |

**Savunma derinliği ilkesi:** tek katmana güvenilmez; CSRF = synchronizer token + SameSite(Lax, katman)
+ OriginCheck (fail-closed) + (muaf uçlar için) yedek. Her katman tek başına düşse bile toplam
düşmez; **katman ekleme üstüne**, mevcut katmanı kaldırma **asla** (OWASP CSRF "Defense in Depth").

---

## §15 Denetim Checklist'i (compact — tam format: security-audit-template)

Denetim raporu üretimi Guardrail #16 ile `.ai/.templates/documentation/security-audit-template.md`'den
başlar; bu skill denetim sırasında şu kapıları uygular:

| # | Kontrol | Beklenen | İhlal |
|---|---------|----------|-------|
| 1 | Pipeline sırası 1-10 | Birebir aynı | revert + log ERROR |
| 2 | `csrf_token` adı + `hash_equals` | Var | 403 verilemez |
| 3 | CSP nonce per-request + strict-dynamic | Var; `unsafe-*` kapalı | XSS yüzeyi |
| 4 | HSTS `max-age=31536000` (SecurityHeaders) | Var | downgrade |
| 5 | Argon2id 64MB/t=4/p=2 + `password_verify` | Var | A02 açığı |
| 6 | AES-256-GCM IV 12 bayt benzersiz + tag 16 | Var | A02 açığı |
| 7 | Session cookie HTTPOnly+Secure+SameSite | Var | çalıntı cookie |
| 8 | Rate limit 60/60s + 429 | Var | brute force |
| 9 | BypassAuth prod kapalı | Var | auth bypass |
| 10 | Hardcoded secret / log sızıntısı | 0 | REDACTED ihlali |
| 11 | `composer audit` çıktısı (A03:2025) | Temiz/yorumlu | ⚠️ açık |
| 12 | OWASP 10/10 işaretli (2025 listesi) | A01–A10 | eksik denetim |
| 13 | Bulgular severity + `dosya:satır` + sahip | Dolu | sahipsiz bulgu |
| 14 | `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` kullanımı | Tahmin yok | halüsinasyon |

**Read-only doğrulama komutları (repo kökünden):**

```bash
# Yasaklı kalıp taraması (değişen dosyalarda) — sonuç 0 olmalı
grep -rnE '_csrf_token|eval\(|new Function\(|unserialize\(\s*\$_(POST|GET|REQUEST)' shared/src/ --include='*.php'
# Hardcoded secret sızıntısı — sonuç 0 olmalı
grep -rniE "(password|secret|api[_-]?key)\s*=\s*['\"][^'\"]{8,}" shared/src/ --include='*.php'
# Middleware kayıt/pasif kod kontrolü
grep -n 'Middleware' shared/src/PageRouter/*.php
# Kripto ihlali (ADR-022) — sonuç 0 olmalı
grep -rnE "PASSWORD_DEFAULT|md5\(\s*\$|aes-256-cbc|aes-256-ecb" shared/src/ --include='*.php'
# CSP ihlali — unsafe-* ve controller'da ikinci header() yazımı
grep -rn "unsafe-inline\|unsafe-eval" shared/src/ --include='*.php'
grep -rn "header\s*(\s*['\"]Content-Security-Policy" shared/src/ --include='*.php'
```

*(Grep desenleri kural metnidir; gerçek değerler `.env`'den okunur ve rapora `[REDACTED]` yazılır.)*

### §15.1 Bulgu Satırı Kalıbı (§15.2 raporu için kopyala-kullan)

```text
| <SEC-ID> | <A0x:2025> | <CRITICAL/HIGH/MEDIUM/LOW> | <dosya:şu-satır> | <kanıtlı gözlem —
  tarama çıktısı veya kod satırı özeti> | <fix: backend-architect> | <durum: OPEN/VERIFY/NOT TESTED> |
```

- **SEC-ID** benzersiz ve artan (`SEC-026-01` — rapor tarihi öneki); aynı bulgu tekrar
  açılırsa eski ID'ye atıf (`dup: SEC-xxx`).
- **OWASP sütunu 2025 listesi** (§7/§7.1) — 2021 adı yazılan rapor **iade** (§7.1 uyarısı).
- **Kanıt sütunu**: `$output`/satır — "muhtemelen" yazımı yok (Truth Mode §17).
- **Durum kapısı**: `VERIFY` yalnız security-engineer yeniden denetimden sonra; `NOT TESTED`
  test kapısı çalışmadıysa (§16 — "geçti" denmez).

---

## §16 Test Kapısı (kabul kriteri — kanıtsız "bitti" YASAK)

| Katman | Araç | Kapı |
|--------|------|------|
| Pipeline sıra | `MiddlewarePipelineTest` | **Yeşil** — kırmızıysa derhal revert |
| CSRF | 9 senaryo (örnek §5 Adım 4) | Token'sız/sahte → 403; safe method → geçer |
| CSP | Nonce doğrulama + report-only turu | Boş nonce yok; `unsafe-*` yok |
| Rate limit | 61. istek | 429 + `Retry-After` |
| Kriyo | `password_needs_rehash` akışı | Parametre değişikliğinde true |
| Tarama | §15 grep komutları | 0 ihlal |

Test edilmemiş iddia = kabul yok (rol kuralı). Test çalıştırılamıyorsa durum **NOT TESTED** olarak
raporlanır — "geçti" **denmez**.

---

## §17 Truth Mode, H001 Reddi & Sınır

- **Zero Hallucination:** repository/class/ADR/satır/dış API kanıtsız iddia yazılmaz →
  `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN`. Dış güvenlik iddiası yalnız
  `references/sources.md`'deki 3+ resmi kaynakla (owasp.org · w3.org · php.net · asvs.owasp.org)
  doğrulanır.
- **Secret yasak:** gerçek anahtar/token/ticket değeri hiçbir skill dosyasına yazılmaz
  (`.env`/env-substitution gösterimi — ADR-015; REDACTED).
- **H001 Reddi:** mimariye ters/güvensiz yapı (async-scope, `_csrf_token`, `unsafe-inline`,
  fail-open, wildcard CORS, inline JS) **reddedilir** ve kullanıcı uyarılır.
- **Yetki genişletme yasak:** bu skill izin/MCP/allow-list değiştiren, güvenlik kapısını
  **gevşeten** öneri üretmez (fail-open yasak — rol kuralı 8).
- **Veto:** `eval`/`exec`/`shell_exec` · inline JS · hard-coded secret → **STOP + rapor** (§13).
- **Onay kapıları:** yıkıcı eylem (deploy, silme), middleware sırası değişikliği, secret
  üretimi → kullanıcı onayı (Guardrail #14; subagent `git commit` ATMAZ).

---

## §18 Otorite & Vault Bağlantıları

| Kaynak | Bağlantı |
|--------|----------|
| `.ai/CLAUDE.md` §6 | Middleware pipeline (immutable) + CSP nonce zinciri |
| `.ai/CLAUDE.md` §7 | Hard Guardrail'lar (#2 vault-first, #3 zero-hallucination, #6 CSRF adı, #7 sıra, #9 no-ORM, #14 onay, #16 şablon) |
| `.ai/CLAUDE.md` §21 | Forbidden Patterns |
| `.ai/CLAUDE.md` §22 | Edge cases (Multi-Tab CSRF → session-bound tek token) |
| `.ai/CLAUDE.md` §23 | Critical Warnings (1-7) |
| `.ai/.decisions/accepted/ADR-008-bypass-auth-middleware.md` | Test bypass; prod'da devre dışı |
| `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` | Üç katmanlı CSRF, `csrf_token`, #6 sırası, Bearer muafiyet sınırı |
| `.ai/.decisions/accepted/ADR-011-session-management.md` | idle 3600s, HTTPOnly cookie, oturum hijyeni, rotasyon |
| `.ai/.decisions/accepted/ADR-012-csp-nonce-strict-dynamic.md` | CSP strict-dynamic + nonce; `unsafe-*` kalıcı kapalı |
| `.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md` | APCu 60 req/60s, brute force, pasif-kod tuzağı |
| `.ai/.decisions/accepted/ADR-022-database-hardened-security.md` | PII AES-256-GCM, Argon2id, blind index, prepared statement |
| `.ai/.decisions/accepted/ADR-052-hybrid-auth-session-jwt.md` | Hibrit auth (session + JWT) |
| `.ai/.decisions/accepted/ADR-058-centralized-auth-service.md` | Merkezî auth: `auth.coremusic.net` |
| `.ai/.decisions/accepted/ADR-059-jwt-library-and-mfa.md` | JWT kütüphanesi + MFA (TOTP) |
| `.ai/.templates/documentation/security-audit-template.md` | Denetim raporu iskeleti (Guardrail #16) |
| `.ai/keys.md` §4 | Security keyword → ADR eşlemesi |
| `.ai/RAG.md` §3 (satır 19) | Güvenlik: CSRF/CSP/JWT/session → bu skill |

**Çelişki kuralı:** SSOT/vault çelişkisi → **DUR + kullanıcıya sor** (Guardrail #12). Vault-içi
öncelik: `.ai/CLAUDE.md > .ai/AGENTS.md > .ai/WORKFLOW.md` (CLAUDE §2.1). Dış kaynak (OWASP/W3C/php.net)
vault ile çelişirse: vault **kural olarak** bağlayıcıdır, fark raporlanır — sessizce örtbas edilmez
(ör. §7.1 2021/2025 şablon farkı).

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
