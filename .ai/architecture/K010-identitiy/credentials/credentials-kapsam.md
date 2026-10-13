---
node_id: K010-identitiy/credentials/credentials-kapsam
node_type: subdomain-scope
status: draft
parent_id: K010-identitiy/credentials
children: []
depends_on:
  - K010-identitiy
evidence:
  - ".ai/.sql/mysql/coremusic_auth.sql credential_vault / credential_keys / credential_audit — 2026-10-12"
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php L44 Argon2id+pepper · L60 verify fail-closed — 2026-10-12"
validation: "credentials-dogrulama.md §1 (12 iddia · 9 doğrulanmış · 2 VR · 1 UNKNOWN)"
group: mimari
open_questions:
  - "R1/R3: parola hash ve credential_vault sahipliği — ADR-097 K4 bekliyor"
  - "R2/R4: APP_PEPPER yönetimi ve vault password tipi — UNKNOWN"
---

# credentials — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`identity-master.md` §3 satır 34 bu dalı **"Parola / API-anahtar saklama arayüzü"**
olarak yazar. Basitçe: kullanıcıların gizli bilgilerini (parola, API anahtarı,
harici servis sırrı) **nasıl sakladığımızın** kural dosyası — ham gizli asla
saklanmaz, hep hash/şifreli hali saklanır. Bu dal bir *arayüz* tanımıdır:
"credential'ı sakla / doğrula" kontratını verir; algoritma seçimi ve üretimin
nerede duracağı ayrı bir karar alanıdır (master §3: K007 pgcrypto çakışması,
ADR-097 K4).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Parola saklama kontratı: ham parola asla saklanmaz, yalnız hash | Hash algoritmasının *hangi katmanda* üretildiği kararı | K007 pgcrypto (master §3 çakışma satırı) — ADR-097 K4 |
| 2 | Parola hash runtime gerçeği: Argon2id + pepper (repo kanıtı §4/5) | Parola politikası (uzunluk/karmaşıklık kural seti) | account (policy) · OWASP M05 (master §15) |
| 3 | Credential vault: AES-256-GCM şifreli servis sırları + rotasyon aralığı | İmzalama anahtarının doğrulayıcı tarafı | tokens (JWT) · K009 (master §13) |
| 4 | Anahtar sürüm yönetimi: hash-only key registry (`credential_keys`) | Ham anahtar üretimi/dagitimi operasyonu | UNKNOWN (ops yok) |
| 5 | Credential erişim denetim izi (append-only audit) | Denetim olaylarının toplanması/saklanması altyapısı | K009 risk defteri (master §3 sızıntı satırı) |
| 6 | API anahtarının hash-only saklanması (`api_keys.key_hash`) | API anahtarının scopes denetimi | permissions |

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | Parola hash'i kimin işi — K010 credentials (arayüz) mi, K007 pgcrypto (üretim) mi? | ⚠️ ADR-097 K4 bekliyor | identity-master.md §3 satır 37 · §11/5 |
| A2 | Credential vault'taki "password" tipi (enum) kullanıcı parolası mı, servis sırrı mı? | ⚠️ çift yorum — `credential_vault.credential_type` enum'u `password` içerir; kullanım alanı A4 | coremusic_auth.sql (dump) |
| A3 | Pepper (`APP_PEPPER`) kaynağı ve rotasyonu | **UNKNOWN** — repo'da pepper şartı var, yönetimi kanıtlanmadı | auth.coremusic.net Password.php (configError('APP_PEPPER')) |
| A4 | credential_vault üretimi K010'a mı, altyapı/ops katmanına mı ait? | ⚠️ master §13 Infrastructure satırı yalnız hash üretimini K007'ye yazar | identity-master.md §13 |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Parola / API-anahtar saklama arayüzü — hash algoritması seçmez" | `identity-master.md` §3 satır 34 | ✅ dosya okundu |
| 2 | `users.password_hash` kolonu: `varchar(255) … 'Argon2id hashed password'` | `.ai/.sql/mysql/coremusic_auth.sql` CREATE TABLE `users` | ✅ dosya okundu (canlı dump) |
| 3 | Hash runtime kodu gerçek: `password_hash($peppered, PASSWORD_ARGON2ID, ['memory_cost'=>65536,'time_cost'=>4,'threads'=>2])` + HMAC-SHA256 pepper | `auth.coremusic.net/include/Domain/ValueObject/Password.php` satır 44 | ✅ dosya okundu |
| 4 | Hash doğrulama: `password_verify($peppered, $hash)` + boş pepper'da fail-closed (`false`) | aynı dosya satır 60 | ✅ dosya okundu |
| 5 | Argon2id parametreleri test ile kilitli: `assertStringStartsWith('$argon2id$', $hash)` | `auth.coremusic.net/tests/Unit/Domain/ValueObject/PasswordTest.php` satır 36 | ✅ dosya okundu |
| 6 | `credential_vault`: AES-256-GCM encrypted value + IV + auth tag + `rotation_interval_days int DEFAULT '90'` | `.ai/.sql/mysql/coremusic_auth.sql` CREATE TABLE `credential_vault` | ✅ dosya okundu |
| 7 | `credential_keys`: hash-only key registry — `key_hash char(64)` UNIQUE, "no raw keys" | aynı dosya, `credential_keys` | ✅ dosya okundu |
| 8 | `credential_audit`: append-only denetim izi — action CHECK (`read/write/rotate/revoke/create`), status CHECK (`allowed/denied`) | aynı dosya, `credential_audit` | ✅ dosya okundu |
| 9 | API anahtarı ham değeri DB'ye yazılır yalnız hash + prefix; doğrulama `hash_equals(key_hash, sha256(raw))` | `shared/src/Repository/ApiKeyRepository.php` başlık docblock + `RAW_KEY_PATTERN` | ✅ dosya okundu |
| 10 | Sızıntı senaryosu → incident defteri (credential sızıntısı) | `identity-master.md` §3 satır 38 (incident-coordinator referansı) | ✅ dosya okundu (bağlantı) |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Parola hash'i hangi katmanın işi? (ADR-097 K4) | ⚠️ VERIFICATION REQUIRED |
| R2 | `APP_PEpper`/pepper anahtarının saklanması ve rotasyonu? | ⚠️ UNKNOWN |
| R3 | `credential_vault` sahipliği (K010 mu, K007 mi)? | ⚠️ VERIFICATION REQUIRED |
| R4 | `credential_vault.credential_type='password'` kullanıcı parolası mı? | ⚠️ UNKNOWN (A4) |

**Kapanış:** status: draft kalır. R1/R3 ADR-097 K4 kararı, R2/R4 A4 kod
incelemesi ile kapanır.

---

## §6 — OWASP Password Storage Cheat Sheet ile parola saklama kontratı eşlemesi

> **Kaynak:** https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

OWASP Password Storage sayfası, parola saklamanın gereksinimlerini tanımlar
(Argon2id önerisi, tuz, pepper, cost ayarı, upgrade). §1'deki "parola saklama
kontratı" (ham asla saklanmaz) bu gereksinimlerle doğrulanır.

### §6.1 — OWASP maddeleri ↔ CoreMusic parola yüzeyi

| # | OWASP maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Argon2id önerilir (ilk tercih) | `PASSWORD_ARGON2ID` | `Password.php` satır 44 | ✅ kanıtlı |
| 2 | unique salt (kütüphane otomatik) | `password_hash` otomatik salt | kütüphane davranışı | ✅ (PHP kontratı) |
| 3 | Pepper sunucu tarafında (opsiyonel) | HMAC-SHA256 pepper | `Password.php` satır 44 | ✅ kanıtlı |
| 4 | Pepper ortam değişkeninde | `APP_PEPPER` şartı | `configError('APP_PEPPER')` | ✅ kanıtlı (isim) · ⚠️ yönetim VR |
| 5 | Cost parametreleri ayarlanmalı | m=65536 t=4 p=2 | satır 44 | ✅ kanıtlı |
| 6 | Maliyet artınca upgrade (`needs_rehash`) | — | — | ❌ (kanıt yok — `identity-dogrulama.md` §11) |
| 7 | Ham parola asla saklanmaz | `password_hash` kolonu (Argon2id comment) | `coremusic_auth.sql` | ✅ kanıtlı |
| 8 | Parola uzunluk sınırı (≥64) | — | — | ⚠️ VR (politika yok) |
| 9 | Doğrulama `password_verify` | fail-closed (boş pepper → false) | `Password.php` satır 60 | ✅ kanıtlı |
| 10 | Kaba kuvvet koruması (rate limit) | K009 sahipliği | `identity-kapsam.md` §3 #7 | ✅ sahiplik |

### §6.2 — Pepper kontratı doğrulama matrisi

| # | Madde | Kanıt | Durum |
|---|---|---|---|
| 1 | Boş pepper → fail-closed | `Password.php` satır 60 (`false`) | ✅ |
| 2 | Boş pepper → configError (hash üretiminde) | satır 44 öncesi | ✅ |
| 3 | Pepper kaynağı (env) | `APP_PEPPER` ismi | ✅ isim · ⚠️ saklama VR |
| 4 | Pepper rotasyonu | — | ⚠️ VR (§5 R2) |
| 5 | `hash_hmac('sha256', …)` | satır 44 | ✅ |

### §6.3 — Salt/pepper katman karşılaştırması

| # | Katman | CoreMusic | Durum |
|---|---|---|---|
| 1 | Salt (hash içinde, otomatik) | PHP Argon2id entegre | ✅ |
| 2 | Pepper (önceden HMAC) | `hash_hmac` + `APP_PEPPER` | ✅ |
| 3 | Şifreleme anahtarı (vault) | `credential_keys` (hash-only) | ✅ (§4 #7) |
| 4 | Rotasyon (pepper) | — | ⚠️ VR |

**Eşleme sonucu:** OWASP maddelerinin 7'si kanıtla örtüşür; iki madde (upgrade
akış pepper rotasyonu) bu dalda tanımsızdır — deftere ek madde olarak taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 — RFC 7516 (JWE) ile credential vault şifreleme eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc7516 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 7516, JSON Web Encryption çerçevesini tanımlar: key management (`alg`) +
content encryption (`enc`), IV, authentication tag. CoreMusic vault'u
AES-256-GCM kullanır — bu bölüm şifreleme kontratını RFC diline bağlar.

### §7.1 — RFC 7516 bileşenleri ↔ `credential_vault` kolonları

| # | RFC 7516 bileşeni | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Content encryption (`enc: A256GCM`) | `encryption_algorithm = 'AES-256-GCM'` | `coremusic_auth.sql` `credential_vault` | ✅ kanıtlı |
| 2 | IV / nonce | IV kolonu | `coremusic_auth.sql` (IV + auth tag) | ✅ kanıtlı |
| 3 | Authentication tag | auth tag kolonu | aynı | ✅ kanıtlı |
| 4 | Ciphertext | `encrypted_value text` | aynı | ✅ kanıtlı |
| 5 | Key management (`alg`) | anahtar yönetimi ayrı (`credential_keys`) | §4 #7 | ✅ (ayrı katman) |
| 6 | `aad` (authenticated additional data) | — | — | ⚠️ VR |
| 7 | `kid` (key id) | — | — | ⚠️ VR (§4 #7 hash-only) |
| 8 | Compact serialization | — | — | ⚠️ VR (repo biçimi) |

### §7.2 — Vault şifreleme doğrulama matrisi

| # | Doğrulama | Kanıt | Durum |
|---|---|---|---|
| 1 | AES-256-GCM (authenticated encryption) | `encryption_algorithm` | ✅ |
| 2 | IV saklanıyor | IV kolonu | ✅ |
| 3 | Tag saklanıyor | auth tag kolonu | ✅ |
| 4 | IV benzersizliği (her şifrelemede yeni) | — | ⚠️ VR (üretim kodu okunmadı) |
| 5 | Anahtar doğrulama (GCM tag) | — | ⚠️ VR (doğrulama kodu) |
| 6 | Rotasyon aralığı (90 gün) | `rotation_interval_days` | ✅ şema · ⚠️ süreç VR |

**Eşleme sonucu:** Vault şifreleme kontratı AES-256-GCM + IV + tag olarak
kanıtlanmıştır; IV üretimi ve tag doğrulama kodu okunmadı → `⚠️` ek madde
olarak defter taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 — PHP `password_hash` resmi kılavuzu ↔ parola hash kontratı

> **Kaynak:** https://www.php.net/manual/en/function.password-hash.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PHP `password_hash()`, verilen algoritmayla (örn. `PASSWORD_ARGON2ID`) parola
özetini üretir; salt otomatik eklenir. §1 #2'deki "parola hash runtime gerçeği"
bu kontratla doğrulanır.

### §8.1 — Kılavuz maddeleri ↔ `Password` ValueObject

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `password_hash($pw, $algo, $options)` | `$peppered` girdisi | `Password.php` satır 44 | ✅ kanıtlı |
| 2 | `PASSWORD_ARGON2ID` sabiti | kullanılan algoritma | satır 44 | ✅ kanıtlı |
| 3 | `memory_cost` (KB) | 65536 | satır 44 | ✅ kanıtlı |
| 4 | `time_cost` | 4 | satır 44 | ✅ kanıtlı |
| 5 | `threads` | 2 | satır 44 | ✅ kanıtlı |
| 6 | Salt otomatik (parametre yok) | — | — | ✅ (PHP kontratı) |
| 7 | uzun string döndürür | `password_hash varchar(255)` | `coremusic_auth.sql` | ✅ şema |
| 8 | `false`/hata dönüşü (Argon2id başarısız) | hata yönetimi okunmadı | — | ⚠️ VR |
| 9 | `PASSWORD_DEFAULT` ayrı sabit | — | — | bilgi |
| 10 | Girdi uzun sınırı (72 byte bcrypt) | Argon2id'de sınır yok | — | bilgi |

### §8.2 — Hash üretim zinciri doğrulama matrisi

| # | Adım | Kanıt | Durum |
|---|---|---|---|
| 1 | `APP_PEPPER` zorunluluğu | `configError('APP_PEPPER')` | ✅ |
| 2 | `hash_hmac('sha256', raw, pepper)` | satır 44 | ✅ |
| 3 | `password_hash(peppered, ARGON2ID, opts)` | satır 44 | ✅ |
| 4 | Test `$argon2id$` öneki | `PasswordTest.php` satır 36 | ✅ |
| 5 | Doğrulama `password_verify` | satır 60 | ✅ |
| 6 | Kolon uzunluğu (255) hash'e yeterli | `varchar(255)` | ✅ |

**Eşleme sonucu:** `password_hash` kontratı satır-içi kanıtlanmıştır (üretim +
test + şema); hata yönetimi ve girdi sınırı davranışı ek madde olarak `⚠️` taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 — OWASP Secrets Management Cheat Sheet ↔ vault/anahtar yönetimi eşlemesi

> **Kaynak:** https://cheatsheetseries.owasp.org/cheatsheets/Secrets_Management_Cheat_Sheet.html · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

OWASP Secrets Management, sırların (secret) güvenli saklanması/rotasyonu/
erişim denetimini tanımlar. §1 #3-#5 (vault, key registry, audit) bu
gereksinimlerle eşlenir.

### §9.1 — OWASP maddeleri ↔ CoreMusic sırrı yüzeyi

| # | OWASP maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Sırlar kodda/DB'de ham tutulmaz | API anahtarı hash-only | `ApiKeyRepository.php` docblock | ✅ kanıtlı |
| 2 | Şifreli depolama (authenticated encryption) | AES-256-GCM vault | `credential_vault` | ✅ kanıtlı |
| 3 | Anahtar versiyonlama | `credential_keys` hash-only registry | §4 #7 | ✅ kanıtlı |
| 4 | Düzenli rotasyon | `rotation_interval_days=90` | §4 #6 | ✅ şema · ⚠️ süreç VR |
| 5 | Erişim denetimi (audit) | `credential_audit` append-only | §4 #8 | ✅ kanıtlı |
| 6 | Erişim red durumu kaydı | `status CHECK ('allowed','denied')` | §4 #8 | ✅ kanıtlı |
| 7 | Sır rotasyonu sonrası eski anahtar temizliği | — | — | ⚠️ VR |
| 8 | Pepper (`APP_PEPPER`) yönetimi | — | — | ⚠️ VR (§5 R2) |
| 9 | Runtime erişim (least privilege) | — | — | ⚠️ VR |
| 10 | Log/redaction | — | — | ⚠️ VR (account-dogrulama §5) |

### §9.2 — Vault denetim doğrulama matrisi

| # | Denetim olayı (şema CHECK) | Kanıt | Durum |
|---|---|---|---|
| 1 | `create` | `credential_audit` CHECK | ✅ |
| 2 | `read` | CHECK | ✅ |
| 3 | `write` | CHECK | ✅ |
| 4 | `rotate` | CHECK | ✅ |
| 5 | `revoke` | CHECK | ✅ |
| 6 | `allowed`/`denied` | CHECK | ✅ |
| 7 | Audit'e yazan runtime kodu | — | ⚠️ VR |

**Eşleme sonucu:** OWASP maddelerinin altısı şema/Repository kanıtıyla örtüşür;
rotasyon süreci, pepper yönetimi ve runtime erişim denetimi bu dalda
kanıtlanmamıştır → `⚠️` ek madde.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
