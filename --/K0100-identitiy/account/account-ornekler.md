---
node_id: K0100-identitiy/account/account-ornekler
node_type: subdomain-scope
status: draft
parent_id: K0100-identitiy/account
children: []
depends_on:
  - K0100-identitiy
evidence:
  - "auth.coremusic.net/include/Repository/UserRepository.php L153 (prepared INSERT) · L233 (tek kullanımlık token SELECT) — 2026-10-12"
  - ".ai/.sql/mysql/coremusic_auth.sql users yaşam döngüsü kolonları — 2026-10-12"
validation: "account-dogrulama.md §1 (12 iddia) · §5-§8 resmi PHP kaynağı HTTP 200 doğrulandı"
group: kod
open_questions:
  - "A3: e-posta doğrulama use-case kodu hâlâ UNKNOWN — örnekler yalnız kanıtlanmış satırları içerir"
  - "A4: account_type tier değişim yetkisi UNKNOWN — örnek üretilmedi"
---

# account — Örnekler

## §1 Gerçek repo kodu (kanıt — uydurma yok)

### Örnek 1.1 — Hesap oluşturma (runtime INSERT)

`auth.coremusic.net/include/Repository/UserRepository.php` satır 153 (okundu, 2026-10-12):

```php
'INSERT INTO users (id, username, email, password_hash, display_name, gender, account_type, is_active, created_at, updated_at) VALUES (:id, :username, :email, :password_hash, :display_name, :gender, :account_type, :is_active, NOW(), NOW())',
```

### Örnek 1.2 — Tek kullanımlık parola sıfırlama token'ı (hesap yaşam döngüsü koşulları)

`auth.coremusic.net/include/Repository/UserRepository.php` satır 233 (okundu):

```php
"SELECT ut.id, ut.user_id, ut.token_hash, ut.expires_at, u.email, u.password_hash FROM user_tokens ut JOIN users u ON u.id = ut.user_id WHERE ut.token_hash = :token_hash AND ut.token_type = 'password_reset' AND ut.used_at IS NULL AND ut.expires_at > NOW() AND u.is_active = 1 AND u.is_deleted = 0 LIMIT 1",
```

### Örnek 1.3 — Yaşam döngüsü bayrakları (şema, canlı dump)

`.ai/.sql/mysql/coremusic_auth.sql` — `users` kolonlarından seçme:

```sql
`account_type` enum('free','premium','studio','admin') DEFAULT 'free' COMMENT 'Account tier',
`is_active` tinyint(1) DEFAULT '1' COMMENT 'Account active flag',
`is_banned` tinyint(1) DEFAULT '0' COMMENT 'Banned status flag',
`is_verified` tinyint(1) DEFAULT '0' COMMENT 'Email verified flag',
`failed_login_attempts` int DEFAULT '0' COMMENT 'Consecutive failed login count',
```

## §2 Resmi doküman örneği

Bu dalın iddiaları disk-tabanlıdır; web paketi bu turda bu dala özgü ayrıca
URL üretmez (hesap yaşam döngüsü bir standart tanımı değil, ürün kararıdır).
KVKK/GBPR bağlantısı master §14/2'de taşınır: unutulma hakkı bu düğümün
yaşam döngüsü kancasında başlar (`identity-master.md` §14).

## §3 Uygulama notu

- Hesap silme kararı (ADR-097 K4) öncesi `is_deleted` soft-delete tek disk gerçeğidir — hard delete akışı yoktur/kanıtlanmamıştır.
- `failed_login_attempts` sayacı kilitleme politikası bu kademe-2 düzeyinde tanımlanmaz; politika A4 incelemesine bağlıdır.

## §4 İlişkili dosyalar

- `K0100-identitiy/account/account-kapsam.md` — sınır ve disk kanıtları
- `K0100-identitiy/account/account-dogrulama.md` — iddia tablosu (12 satır)
- `identity-master.md` §2 · `identity-dogrulama.md` §G-10.1

---

## §5 — PHP PDO prepared statements resmi kılavuzu ↔ kanıtlanmış INSERT/SELECT

> **Kaynak:** https://www.php.net/manual/en/pdo.prepared-statements.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PHP resmi kılavuzu, `PDO::prepare()` + `execute()` ile parametreli sorgu
kullanımını tanımlar: placeholder'lar (`:name` veya `?`) veri ile sorguyu ayırır
ve SQL injection'ı engeller. §1'deki iki kanıtlanmış sorgu bu kontratın
doğrudan uygulamalarıdır.

### §5.1 — Kılavuz maddeleri ↔ kanıtlanmış sorgular

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `prepare()` sorguyu sunucuya önceden gönderir | INSERT + SELECT prepare edilir | §1 Örnek 1.1 · 1.2 | ✅ kanıtlı (yer tutucular) |
| 2 | İsimli placeholder `:name` | `:id` · `:username` · `:email` · `:password_hash` · `:display_name` · `:gender` · `:account_type` · `:is_active` | Örnek 1.1 (satır 153) | ✅ kanıtlı |
| 3 | İsimli placeholder (ikinci örnek) | `:token_hash` | Örnek 1.2 (satır 233) | ✅ kanıtlı |
| 4 | Veri `execute()` dizisiyle bağlanır | çağrının execute satırı bu revizyonla okunmadı | — | ⚠️ VR |
| 5 | Emulation modu (`PDO::ATTR_EMULATE_PREPARES`) | ayar kanıtlanmadı | — | ⚠️ VR |
| 6 | `SELECT *` yerine kolon listesi | SELECT kolon listeli (`ut.id, ut.user_id, …`) | Örnek 1.2 | ✅ kanıtlı |
| 7 | `LIMIT 1` ile tek satır hedefleme | `LIMIT 1` | Örnek 1.2 | ✅ kanıtlı |

### §5.2 — Örnek 1.1 (INSERT) kılavuz ile hizalama

| # | INSERT bileşeni | Kılavuz karşılığı | Durum |
|---|---|---|---|
| 1 | Kolon listesi sabit | prepare edilmiş sabit metin | ✅ |
| 2 | Değerler placeholder | `:id` … `:is_active` (8 adet) | ✅ |
| 3 | `NOW()` ifadesi | veri bağlanmayan sabit ifade | ✅ (§8 §7 zaman kaynağı) |
| 4 | `RETURNING`/sonuç id | — | ⚠️ VR |

### §5.3 — Örnek 1.2 (SELECT) kılavuz ile hizalama

| # | SELECT bileşeni | Kılavuz karşılığı | Durum |
|---|---|---|---|
| 1 | JOIN `users` ile hesap durumu | sorgu tek prepare'da | ✅ |
| 2 | 6 koşul (`token_hash`, `token_type`, `used_at`, `expires_at`, `is_active`, `is_deleted`) | sabit koşul metni + 1 placeholder | ✅ |
| 3 | Şartname sırası (RFC/OWASP §8.1 hizası) | OWASP madde 3/8/9 ile örtüşür | ✅ |

**Uygulama notu:** Kılavuzun "hash'leri de parametreleştirin" ruhu, `token_hash`
değerinin nerede üretildiğini (`hash()` — §6) sormayı zorunlu kılar; bu madde
§6'ya devredilir (kanıt zinciri).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §6 — PHP `hash()` resmi kılavuzu ↔ `token_hash` üretim zinciri

> **Kaynak:** https://www.php.net/manual/en/function.hash.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PHP `hash()`, verilen algoritmayla (örn. `sha256`) girdi özeti üretir. K010'da
`user_tokens.token_hash` kolonu kanıtlanmıştır; hangi algoritmayla üretildiği bu
dalın iddia alanının dışındadır — kılavuz eşlemesi yalnızca üretim sözleşmesini
tanımlar.

### §6.1 — Kılavuz maddeleri ↔ token_hash yüzeyi

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `hash($algo, $data, $raw_output)` imzası | token_hash üretimi algoritması | — | ⚠️ UNKNOWN (üretim okunmadı) |
| 2 | `sha256` algoritması yaygın kullanım | `OAuthManager` `hash('sha256', $state)` | `OAuthManager.php` satır 160 (kök dosya) | ✅ kanıtlı (state — komşu yüzey) |
| 3 | `sha256(jti)` eşlemesi (bu vault iddiası) | `user_tokens.token_hash = sha256(jti)` | `identity-bagimliliklar.md` §1 #7 | ✅ vault iddiası · ⚠️ üretim satırı VR |
| 4 | Ham token DB'ye yazılmaz (hash-only) | `token_hash` kolonu | `coremusic_auth.sql` `user_tokens` | ✅ kanıtlı |
| 5 | `hash_equals` ile karşılaştırma | kullanıcı-token karşılaştırma yolu | — | ⚠️ VR (`account-dogrulama.md` §6) |
| 6 | `hash_hmac` ayrı işlevdir | pepper (credentials) | `Password.php` satır 44 | ✅ komşu kanıt |

### §6.2 — Token üretim zinciri (bilinen halkalar)

| # | Halka | Kanıt | Durum |
|---|---|---|---|
| 1 | Token ham değeri üretilir | — | ⚠️ VR |
| 2 | Hash alınır (`sha256` varsayımı vault iddiası) | vault §1 #7 | ⚠️ üretim VR |
| 3 | `user_tokens`'a yazılır (`token_hash`, `expires_at`, `used_at=NULL`) | şema | ✅ |
| 4 | Ham token kullanıcıya iletilir | — | ⚠️ VR |
| 5 | Doğrulama: hash karşılaştırması (satır 233) | SELECT | ✅ |

**Uygulama notu:** `OAuthManager`'daki `hash('sha256', $state)` kanıtı yalnız
OAuth state yüzeyine aittir; `user_tokens.token_hash` üretimi aynı algoritmayı
kullanıyor **olabilir** ancak satır kanıtı yok → UNKNOWN korunur, uydurulmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 — PHP `filter_var(FILTER_VALIDATE_EMAIL)` ↔ e-posta doğrulama yüzeyi

> **Kaynak:** https://www.php.net/manual/en/function.filter-var.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

PHP `filter_var` + `FILTER_VALIDATE_EMAIL`, e-posta adresini basitleştirilmiş
bir kalıpla doğrular (tam RFC 5322/5321 uygulaması **değildir**). A3 (e-posta
doğrulama use-case kodu) UNKNOWN olduğu için bu bölüm yalnızca kontratı tanımlar.

### §7.1 — Kılavuz maddeleri ↔ CoreMusic e-posta doğrulama

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `FILTER_VALIDATE_EMAIL` sabiti | doğrulayıcı kodu okunmadı | — | ⚠️ UNKNOWN (A3) |
| 2 | Basitleştirilmiş kalıp (kısmi RFC) | — | — | bilgi |
| 3 | `false` dönüşü geçersiz adres | akış davranışı kanıtlanmadı | — | ⚠️ VR |
| 4 | Unicode/IDN sınırlaması | — | — | bilgi |
| 5 | `filter_var` genel arayüzü (flags) | — | — | bilgi |

### §7.2 — E-posta doğrulama karar ağacı (bilinen halkalar)

| # | Adım | Durum |
|---|---|---|
| 1 | Kayıtta format doğrulama | ⚠️ UNKNOWN (kod okunmadı) |
| 2 | `users.email` yazımı | ✅ (INSERT satır 153) |
| 3 | `email_verify` token üretimi | ✅ şema · ⚠️ üretim VR |
| 4 | Doğrulama linki ile `used_at`/`is_verified` | ⚠️ VR (UPDATE kanıtı yok) |

**Uygulama notu:** `FILTER_VALIDATE_EMAIL` tek başına RFC 5322 uyumu garanti
etmez (kılavuz uyarısı); bu nedenle A3 incelemesi `filter_var` mı yoksa farklı
doğrulayıcı mı kullanıldığını okumalıdır — bilinmeyen yazılır, tahmin edilmez.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 — PHP `DateTimeImmutable` resmi kılavuzu ↔ zaman damgası örneği

> **Kaynak:** https://www.php.net/manual/en/class.datetimeimmutable.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

`DateTimeImmutable`, değiştirmeyen (immutable) tarih/saat nesnesidir: her
değiştirme işlemi **yeni** bir nesne döndürür. Hesap yaşam döngüsü zaman
kolonları DB'de `NOW()` ile yazılır (kanıtlanmıştır — §7); PHP tarafında zaman
üretimi hangi sınıfla yapılıyor bu dalın iddia alanının dışındadır.

### §8.1 — Kılavuz maddeleri ↔ CoreMusic zaman yüzeyi

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Değiştirme yeni nesne döndürür (immutable) | PHP tarafı okunmadı | — | ⚠️ VR |
| 2 | `format()` ile çıktı biçimi | — | — | ⚠️ VR |
| 3 | `createFromFormat` ayrıştırma | — | — | ⚠️ VR |
| 4 | DB `NOW()` tercih ediliyor | `INSERT … NOW()` | `UserRepository.php` satır 153 | ✅ kanıtlı |
| 5 | Token süresi karşılaştırması DB'de | `expires_at > NOW()` | satır 233 | ✅ kanıtlı |
| 6 | `DateTime` (mutable) ayrımı | — | — | bilgi |

### §8.2 — Örnek senaryo (şartname düzeyinde — kanıt değildir)

| # | Senaryo | Beklenen PHP kullanımı | Durum |
|---|---|---|---|
| 1 | Token son kullanma hesabı | `DateTimeImmutable('now')->modify('+1 hour')` | PLANNED (tasarım) |
| 2 | Yaşam döngüsü kararı (180 gün inactive) | immutable karşılaştırma | PLANNED (policy yok) |
| 3 | DB'ye yazım | hâlâ `NOW()` (kanıtlanmış tercih) | ✅ |

**Uygulama notu:** Bu bölüm bir **kullanım örneği şartnamesidir**; CoreMusic
runtime'ında `DateTimeImmutable` kullanımı kanıtlanmamıştır (§7.2 #4 aynı
boşluğu taşır). Dolgu kod yazılmaz, yalnız kontrat tanımlanır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
