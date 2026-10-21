# Kaynaklar (Sources) — Truth Mode Doğrulama

> Skill: `security-hardening` v4.0.0 · CoreMusic · **Erişim tarihi: 2026-10-21**
>
> Skill-maker §6 (Truth Mode) kuralı: her harici iddia 2-3 ayrı resmi kaynaktan
> doğrulanır; her satır = **kaynak adı · URL · erişim tarihi · doğrulanan şey**.
> Doğrulanamayan iddia bu dosyada listelenmez — SKILL.md içine
> `⚠️ VERIFICATION REQUIRED` olarak düşer.

---

## 1. OWASP Top 10:2025 (kurulan/kurulan)

| # | Kaynak | URL | Erişim | Doğrulanan şey |
|---|---|---|---|---|
| 1 | OWASP Top 10:2025 — Official (2025-sürüm sayfası) | https://owasp.org/Top10/2025/en/ | 2026-10-21 | Sürümün **2025** olduğu; A01-A10 listesi ve adları |
| 2 | (Aynı OWASP sitesi — kategori sayfaları; 1. bağlantının altında) | https://owasp.org/Top10/ (k kökü) | 2026-10-21 | Top 10'in son sürüm göstergesi (2025) |

**Doğrulanan içerik (adlar):**
A01 Broken Access Control · A02 Cryptographic Failures · A03 Software Supply
Chain Failures · A04 Injection · A05 Security Misconfiguration · A06 Insecure
Design · A07 Identification and Authentication Failures · A08 Software and
Data Integrity Failures · A09 Security Logging and Monitoring Failures · A10
Mışandling of Exceptional Conditions.

**Önemli sürüm farkları (2021 → 2025, doğrulanmış):**

| 2021 | 2025 | Not |
|---|---|---|
| A03 Injection | **A04 Injection** | Yeri değişti |
| A04 Insecure Design | **A06 Insecure Design** | Yeri değişti |
| A05 Security Misconfiguration | **A05 Security Misconfiguration** | Aynı sıra |
| — | **A03 Software Supply Chain Failures** (YENİ) | 2021'de ayrı kategori değil |
| — | **A10 Exceptional Conditions** (YENİ) | 2021 A09'a benzer ama "exception" odaklı |
| A08 Software & Data Integrity Failures | A08 (aynı) | Aynı sıra |
| A09 Logging & Monitoring | A09 (aynı) | Aynı sıra |
| A10 SSRF | **A01'e katıldı** (bkz. sayfanın SSRF notu) | A10 artık SSRF değil |
| A06 Vulnerable Components | — (liste dışı / A03 içine sızdı) | 2025'te ayrı satır yok |
| A07 Auth Failures | **A07 Identification and Authentication Failures** (Aynı isim genişledi) | ADR-011 referansı |
| A01 Broken Access Control | A01 (aynı sıra) | — |

**⚠️ VERIFICATION REQUIRED:** (a) A03'ün "CI evidence tabanlı" doğrulama
mantığı — sayfada `ci evidence` geçiyor ama kriter detayı bu oturumda
doğu-batı karşılaştırması yapılmadı; (b) SSRF'in A01'e "fold" edildiğinin
kesin dili — 2025 sayfasında A01 geniş tanımı SSRF'i içerir mi, ayrı
A10 mı kaldırıldı, satır bazında teyit gerekir; (c) "Misconfiguration" vs
"Misconfigurations" yazım varyantı (sayfada biri görüldü — ikinci kontrol).

---

## 2. CSP (Content Security Policy) — W3C

| # | Kaynak | URL | Erişim | Doğrulanan şey |
|---|---|---|---|---|
| 1 | W3C — Content Security Policy Level 3 (Working Draft) | https://www.w3.org/TR/CSP | 2026-10-21 | **CSP Level 3 = Working Draft (16 Sept 2026)** — ÖNERİ (Rec) DEĞİL |
| 2 | W3C — CSP Level 2 (W3C Recommendation) | https://www.w3.org/TR/CSP2/ | 2026-10-21 | **CSP2 = W3C Recommendation (2016)** — Rec statüsü taşıyan sürüm L2 |

**Doğrulanan kural (SKILL.md §6):** "CSP Level 3 **önerisi** (Working Draft
16 Sept 2026); Rec = L2 (2016)." — Kılavuzda `nonce`/`strict-dynamic`
yapıları L3 WD içindedir; destek matrisi tarayıcı-bazlıdır →
**L2-only tarayıcı fallback matrisi ⚠️ VERIFICATION REQUIRED** (caniuse/MDN
destek oranları bu oturumda kontrol edilmedi).

Ek doğrulama (nonce + inline yasağı): OWASP XSS Prevention Cheat Sheet ve
W3C sources — **2. kaynak bu oturumda ayrıca açılmadı** → nonce "her script
için gerekli" iddiası W3C CSP3 §nonce-eleme bölümünden geliyor; satır-bazlı
alıntı **⚠️ VERIFICATION REQUIRED**.

---

## 3. CSRF — OWASP Cheat Sheet

| # | Kaynak | URL | Erişim | Doğrulanan şey |
|---|---|---|---|---|
| 1 | OWASP CSRF Prevention Cheat Sheet | https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html | 2026-10-21 | (a) **Synchronizer token pattern** ana savunma; (b) **SameSite = depth-of-defense, tek başına yeterli değil**; (c) **Fetch Metadata (Sec-Fetch-Site) ek savunma**; (d) token doğrulama = `hash_equals` benzeri zamanlamalı karşılaştırma (DoS nedeniyle saf `==` yerine) |

**Doğrulanmış cümleler (paraphrase):**
- "Synchronizer token'lar en yaygın savunmadır; token benzersiz, tahmin
  edilemez, oturum yaşam döngüsüne bağlı olmalı."
- "SameSite cookie, derinlik katmanıdır — eski tarayıcılar ve cross-site
  navigasyon edge-case'leri nedeniyle tek başına CSRF çözümü değildir."
- "Fetch Metadata başlıkları (Sec-Fetch-Site: cross-site) sunucu tarafında
  reddetme için kullanılabilir — ek, temel savunma."

**Yerel karşılığı:** ADR-010 §2 = synchronizer + hash-equals + 15dk süre +
X-CSRF-Token (Fetch Metadata OriginCheck'te kullanımı **⚠️ VERIFICATION
REQUIRED** — ADR-035/OriginCheck kodu bu oturumda okunmadı).

---

## 4. ASVS (Application Security Verification Standard)

| # | Kaynak | URL | Erişim | Doğrulanan şey |
|---|---|---|---|---|
| 1 | OWASP ASVS (repo — sürüm okuma) | https://raw.githubusercontent.com/OWASP/ASVS/master/1.0.0/en/0x00-Header.md | 2026-10-21 | **ASVS 5.0.0** (May 2025) — sürüm tarihi |
| 2 | ASVS arama (site içi) | https://owasp.org/www-project-application-security-verification-standard/ | 2026-10-21 | Proje ana sayfası = ASVS güncel sürümün 5.0.0 olduğu (proje sayfası üzerinden) |

**Doğrulanan format:** Gereksinim kimliği `v5.0.0-<bölüm>.<alt-bölüm>.<gereksinim>`
(ör. `v5.0.0-2.1.1`) — 4.x'teki `2.1.1` düz formatından farklı.

**⚠️ VERIFICATION REQUIRED:** `.ai/CLAUDE.md` §23'ün "ASVS 5.0.0 §2-3"
eşlemesi ve `.ai/.decisions/accepted/ADR-011`'in "ASVS 7.2.4" referansının
5.0.0 içindeki karşılığı (7.2.4 = 4.x formatı olabilir; 5.0.0'da bölüm
numaralandırması değişti mi, bölüm bazında okunmadı).

---

## 5. PHP — password_hash / Argon2id

| # | Kaynak | URL | Erişim | Doğrulanan şey |
|---|---|---|---|---|
| 1 | php.net — password_hash | https://www.php.net/manual/en/function.password-hash.php | 2026-10-21 | (a) `PASSWORD_ARGON2ID` sabiti — **Argon2 ile derlenmiş PHP'de mevcut**; (b) `PASSWORD_DEFAULT` = "en güçlü mevcut algoritma" — **PHP 8.4'te bcrypt maliyet 10 → 12 değişti** |

**Doğrulanmış noktalar:**
- `PASSWORD_ARGON2ID` = "Argon2id — PHP'nin Argon2 ile derlenmiş sürümlerinde
  kullanılabilir" (dokümanda open_basedir/Windows notları da var — kapsam dışı).
- `PASSWORD_DEFAULT` sabit algoritma DEĞİLDİR; PHP sürümüne göre taşar — bu
  nedenle **Argon2id explicit** zorunlu (ADR-022 §1).
- PHP 8.4: bcrypt varsayılan maliyet 10 → **12** (Argon2id kararı
  etkilemez — biz explicit Argon2id kullanıyoruz).

**⚠️ VERIFICATION REQUIRED:** Argon2id'nin PHP'ye dahil olduğu **minimum PHP
sürümü** (dokümanda "compiled with Argon2" diyor, sürüm vermiyor — CoreMusic
PHP 8.x olduğu için pratikte mevcut, ama sürüm-tabanlı iddia yazılmadı).

---

## 6. Yerel otorite (harici değil — vault kanıtı)

| # | Kaynak | Erişim | Doğrulanan şey |
|---|---|---|---|
| 1 | `.ai/.decisions/accepted/ADR-008..059` (disk) | 2026-10-21 | ADR varlıkları, başlıklar, §1/§2 desenleri (oturum içi `glob`+`read` kanıtı) |
| 2 | `shared/src/Middleware/` (11 dosya: 10 middleware + MiddlewarePipeline.php) | 2026-10-21 | Pipeline bileşen adları ve 10-sıra (`glob`) |
| 3 | `.ai/CLAUDE.md` §6/§7/§21/§23 · `.ai/RAG.md` §3 · `.ai/keys.md` | 2026-10-21 | Güvenlik kuralları, forbidden patterns, pipeline yasağı |
| 4 | `.ai/.templates/documentation/security-audit-template.md` (authority: reference) | 2026-10-21 | Audit rapor formatı (§4.1 OWASP **2021** listesi — bkz. SKILL.md §7.1 uyarı) |

---

## 7. Doğrulanmayan / uydurulmayan (bu skill'de yazılmaz)

Aşağıdakiler için **kanıt bu oturumda bulunmadı** — ilgili yerlerde
`⚠️ VERIFICATION REQUIRED` işaretlidir:

1. CVE kimlikleri, exploit detayları, saldırı-poç kimlikleri — **hiçbir CVE
   numarası yazılmadı** (uydurma yasağı).
2. ADR-013'ün login-özel limiti (60/60s genel; login için ayrı eşik vault'ta
   görünmedi).
3. `.ai/.templates/documentation/security-audit-template.md` §4.1'in 2021
   OWASP'ı — şablon FROZEN/authority: reference → düzeltilmedi, SKILL.md §7.1
   fark tablosu eklendi.
4. ASVS 5.0.0 bölüm eşlemeleri (§2-3, 7.2.4 karşılığı) — bölüm bazlı okuma
   yapılmadı.
5. Tarayıcı CSP destek yüzdeleri (L2 fallback matrisi).
6. `pragmarx/google2fa` paket sürümü — vault'ta ad var, sürüm kanıtı yok
   (ADR-059 §2 paketi adı; composer.lock okunmadı).
7. PHP Argon2id min sürümü (bkz. §5).
