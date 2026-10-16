---
node_id: K010-identitiy/account/account-dogrulama
node_type: subdomain-scope
status: draft
parent_id: K010-identitiy/account
children: []
depends_on:
  - K010-identitiy
evidence:
  - "§1 iddia tablosu 12 satır (8 ✅ · 2 ⚠️ · 2 UNKNOWN) — 2026-10-12"
  - ".ai/.sql/mysql/coremusic_auth.sql · auth.coremusic.net UserRepository.php L153/L233 — 2026-10-12"
validation: "Bu dosyanın kendisi account dalının doğrulama defteridir"
group: kanit
open_questions:
  - "Satır 9-10: account ↔ user-identity birleşimi ve KVKK kararı ADR-097 bekliyor"
  - "Satır 11-12: e-posta doğrulama use-case kodu ve tier yetkisi UNKNOWN"
---

# account — Kanıt Tablosu

> **Kaynak notu:** (a) bu dalın kendi disk kanıtları (repo PHP + canlı SQL dump),
> (b) bu oturumda doğrulanan web URL'leri. **K009-security'den iddialar
> TAŞINMAZ** — bu tablo account'un kendi sınırı üzerinden yazıldı.
> Yasak: URL, sürüm, sınıf uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | `users` tablosunda hesap yaşam döngüsü kolonları vardır: `is_active` · `is_banned` · `is_verified` · `failed_login_attempts` · soft-delete (`is_deleted`/`deleted_at`) | `.ai/.sql/mysql/coremusic_auth.sql` CREATE TABLE `users` | 2026-10-12 | ✅ dosya okundu (canlı dump 2026-10-07) |
| 2 | Hesap tier'ı `account_type enum('free','premium','studio','admin')` olarak şemada | aynı dosya, `users.account_type` | 2026-10-12 | ✅ dosya okundu |
| 3 | Hesap oluşturma runtime'da prepared INSERT ile yapılır | `auth.coremusic.net/include/Repository/UserRepository.php` satır 153 | 2026-10-12 | ✅ dosya okundu |
| 4 | Parola sıfırlama token'ı tek kullanımlıktır ve aktif hesaba bağlanır | `auth.coremusic.net/include/Repository/UserRepository.php` satır 233 (`used_at IS NULL AND expires_at > NOW() AND u.is_active = 1`) | 2026-10-12 | ✅ dosya okundu |
| 5 | `user_tokens.token_type` enum'u `password_reset` + `email_verify` içerir (e-posta doğrulama akışının şema karşılığı) | `.ai/.sql/mysql/coremusic_auth.sql` CREATE TABLE `user_tokens` | 2026-10-12 | ✅ dosya okundu |
| 6 | Domain entity `password_hash`'i dışa vermez; bunun testi vardır | `auth.coremusic.net/include/Domain/Entity/User.php` + `tests/Unit/Domain/Entity/UserTest.php` satır 66 (`assertArrayNotHasKey`) | 2026-10-12 | ✅ dosya okundu |
| 7 | Kayıt/giriş/çıkış olayları domain event'tir | `shared/src/Events/Domain/UserRegisteredEvent.php` · `UserLoggedInEvent.php` · `UserLoggedOutEvent.php` | 2026-10-12 | ✅ dosya varlığı doğrulandı (grep) |
| 8 | Çocuk rolü = "Hesap yaşam döngüsü" (master §2) | `identity-master.md` satır 27 | 2026-10-12 | ✅ dosya okundu |
| 9 | account ↔ user-identity birleşimi v2'de ayrı satır olarak tutuldu; birleşim kararı yok | `identity-bagimliliklar.md` §Ç-10.7/4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 10 | Hesap silme ↔ KVKK kararı (ADR-097 K4) verilmedi | `identity-master.md` §2 risk satırı | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | E-posta doğrulama akışının use-case/runtime kodu | — | 2026-10-12 | **UNKNOWN** (A4 kod incelemesi) |
| 12 | `account_type` tier değişim yetkisi (kim, hangi rol) | — | 2026-10-12 | **UNKNOWN** (rol modeli bağlanmadı) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| İşlenen benzersiz dosya yolu | 6 (2 PHP · 1 SQL dump · 1 entity+test · 3 event klasörü tek satırda) |
| Benzersiz web URL | 0 (bu dalın iddiaları disk-tabanlıdır) |
| ✅ doğrulandı | 8 (1–8) |
| ⚠️ VERIFICATION REQUIRED | 2 (9 · 10) |
| UNKNOWN | 2 (11 · 12) |

**Geçiş kuralı:** satır 9–10 ADR-097 K4/G2 kararıyla, 11–12 A4 kod incelemesiyle
kapanır; kanıt gelene kadar UNKNOWN kalır (identity-dogrulama §G-10.10/3).

---

## §3 — PHP `DateTime` resmi kılavuzu ↔ zaman kolonu doğrulama

> **Kaynak:** https://www.php.net/manual/en/class.datetime.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

`DateTime` (mutable) PHP'in tarih/saat nesnesidir; `modify()` nesneyi değiştirir.
Bu bölüm, `users`/`user_tokens` zaman kolonlarının **doğrulama** boyutunu
tutar: kolon varlıkları kanıtlanmıştır (§1 #1), biçim/saat dilimi doğrulanmamıştır.

### §3.1 — Kılavuz maddeleri ↔ zaman kolonu doğrulaması

| # | Kılavuz maddesi | Doğrulama karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `DateTime::getTimestamp()` Unix zamanı döndürür | — | — | bilgi |
| 2 | `modify()` mutable değiştirme | — | — | bilgi |
| 3 | `format()` çıktı biçimi | kolon biçimleri okunmadı | — | ⚠️ VR |
| 4 | `TZ` farkı (`DateTimeZone`) | DB `NOW()` vs PHP saati | `UserRepository.php` satır 153 · 233 | ⚠️ VR (TZ kanıtlanmadı) |
| 5 | `setTimestamp`/`createFromFormat` | — | — | bilgi |
| 6 | DB `NOW()` kullanımı | kanıtlanmış tek zaman kaynağı | satır 153 · 233 | ✅ |

### §3.2 — Zaman kolonu doğrulama matrisi

| # | Kolon | Doğrulanan iddia | Durum |
|---|---|---|---|
| 1 | `created_at` | mevcut (INSERT) | ✅ kolon · ⚠️ biçim |
| 2 | `updated_at` | mevcut (INSERT) | ✅ kolon · ⚠️ biçim |
| 3 | `last_login_at` | kolon mevcut | ✅ kolon · ⚠️ akış |
| 4 | `password_changed_at` | kolon mevcut | ✅ kolon · ⚠️ akış |
| 5 | `deleted_at` | soft-delete anı | ✅ kolon · ⚠️ akış |
| 6 | `expires_at` | `> NOW()` koşulu | ✅ koşul |
| 7 | `used_at` | `IS NULL` koşulu | ✅ koşul |

**Sonuç:** Zaman kolonlarının **varlığı** ve `NOW()` karşılaştırmaları doğrulanmıştır;
PHP tarafındaki sınıf kullanımı ve TZ tutarlılığı `⚠️ VERIFICATION REQUIRED`
olarak ek madde taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §4 — PHP `strtotime` resmi kılavuzu ↔ süre karşılaştırma doğrulaması

> **Kaynak:** https://www.php.net/manual/en/function.strtotime.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

`strtotime()`, İngilizce tarih ifadelerini Unix zaman damgasına çevirir
(`false` döndürebilir). Token süresi ve hesap yaşı karşılaştırmalarında süre
hesaplama sözleşmesi bu kılavuzla eşlenir.

### §4.1 — Kılavuz maddeleri ↔ süre karşılaştırmaları

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `strtotime($time, $base)` → int/`false` | süre hesabı PHP tarafında kanıtlanmadı | — | ⚠️ VR |
| 2 | `false` dönüşü hata yönetimini zorunlu kılar | — | — | bilgi |
| 3 | DB `NOW()` ile karşılaştırma (SQL) | `expires_at > NOW()` | satır 233 | ✅ kanıtlı (SQL) |
| 4 | Token ömrü değeri (ör. +1 saat) | değer okunmadı | — | ⚠️ VR |
| 5 | Hesap silme penceresi (ör. 30 gün) | — | — | ⚠️ VR (KVKK ADR-097 K4) |
| 6 | `failed_login_attempts` penceresi | — | — | ⚠️ VR (A4) |

### §4.2 — Süre karşılaştırma doğrulama tablosu

| # | Karşılaştırma | Doğrulama | Durum |
|---|---|---|---|
| 1 | `expires_at > NOW()` | satır 233'de SQL'de | ✅ |
| 2 | `used_at IS NULL` | satır 233 | ✅ |
| 3 | Token ömrü değeri | — | ⚠️ VR |
| 4 | PHP-side süre hesabı | — | ⚠️ VR |
| 5 | Zaman dilimi uyumu | — | ⚠️ VR |

**Sonuç:** Süre karşılaştırmalarının **SQL tarafı** doğrulanmıştır; PHP tarafındaki
süre hesapları ve değerler bu revizyonla okunmadı → ek madde olarak `⚠️` taşınır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §5 — OWASP Logging Cheat Sheet ↔ güvenlik olayı günlüğü doğrulaması

> **Kaynak:** https://cheatsheetseries.owasp.org/cheatsheets/Logging_Cheat_Sheet.html · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

OWASP Logging sayfası, güvenlik olaylarının loglanmasını (giriş denemeleri,
başarısızlıklar) ve log'lara sır/PII yazılmamasını tanımlar. Bu dalın hesap
yaşam döngüsü olayları bu gereksinimlerle doğrulanır.

### §5.1 — OWASP maddeleri ↔ CoreMusic log yüzeyi

| # | OWASP maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Güvenlik olayları loglanmalı (giriş/çıkış) | K009'a devredildi (`identity-bilesenler.md` §2 #1) | — | ✅ sahiplik (K009) |
| 2 | Başarısız giriş denemeleri | `failed_login_attempts` kolonu | `coremusic_auth.sql` | ✅ kolon |
| 3 | `authz_denied` log kaydı | `PermissionMiddleware` securityEvent | `PermissionMiddleware.php` satır 50-54 | ✅ (komşu yüzey) |
| 4 | Sır/parola log'a yazılmaz | `password_hash` dışa sızdırılmaz | `User.php` + `UserTest.php` satır 66 | ✅ |
| 5 | Token log'a yazılmaz | — | — | ⚠️ VR (log akışı okunmadı) |
| 6 | Olay log'u append-only | `log.md` vault kuralı | `.ai/AGENTS.md` §5 | ✅ (vault) |
| 7 | Log korunması (erişim) | — | — | ⚠️ VR (K009/K010) |
| 8 | UTC/standart zaman damgası | — | — | ⚠️ VR (§3 #4) |
| 9 | Domain event yayılımı | 5 domain event | `Events\Domain\` | ✅ dosya var · ⚠️ yayıncı VR |

### §5.2 — Hesap yaşam döngüsü olay log matrisi

| # | Olay | Log kanıtı | Durum |
|---|---|---|---|
| 1 | Kayıt (`UserRegisteredEvent`) | event dosyası var | ✅ |
| 2 | Giriş (`UserLoggedInEvent`) | event dosyası var | ✅ |
| 3 | Çıkış (`UserLoggedOutEvent`) | event dosyası var | ✅ |
| 4 | Sıfırlama (`PasswordResetRequestedEvent`) | event dosyası var | ✅ |
| 5 | Gender set (`GenderSetEvent`) | event dosyası var | ✅ |
| 6 | Hesap silme/askıya alma | — | ⚠️ VR |
| 7 | Başarısız giriş (kilitlenme) | kolon var · log akışı VR | ⚠️ |

**Sonuç:** Güvenlik olaylarının bir kısmı kanıtlanmıştır (event dosyaları +
`authz_denied` + parola gizliliği); token log gizliliği, UTC damgası ve log
erişim koruması bu dalda doğrulanmamıştır → `⚠️` ek madde.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §6 — PHP `hash_equals` resmi kılavuzu ↔ timing-safe karşılaştırma doğrulaması

> **Kaynak:** https://www.php.net/manual/en/function.hash-equals.php · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

`hash_equals()`, bilinen ve kullanıcı girdisini **süreye duyarlı olmayan**
biçimde karşılaştırır (timing attack koruması). Token ve API anahtarı
karşılaştırmalarının doğrulama boyutu bu kılavuzla bağlanır.

### §6.1 — Kılavuz maddeleri ↔ CoreMusic karşılaştırma yüzeyi

| # | Kılavuz maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `hash_equals($known, $user)` imzası | API anahtarı doğrulama | `ApiKeyRepository.php` (docblock + `hash_equals` kullanımı) | ✅ kanıtlı |
| 2 | Uzunluk farkında erken `false` | — | — | bilgi |
| 3 | Timing-safe eşitlik | `hash_equals` kullanımı | aynı dosya | ✅ |
| 4 | Kullanıcı-token karşılaştırma yolu | `user_tokens` SELECT | satır 233 | ⚠️ VR (karşılaştırma yolu okunmadı) |
| 5 | OAuth state karşılaştırma | `validateState` | `OAuthManager.php` satır 180 | ⚠️ VR (karşılaştırma yöntemi) |
| 6 | CSRF token karşılaştırma | — | — | ⚠️ VR (K009/K010) |

### §6.2 — Karşılaştırma doğrulama matrisi

| # | Yüzey | Yöntem | Durum |
|---|---|---|---|
| 1 | API anahtarı | `hash_equals(key_hash, sha256(raw))` | ✅ |
| 2 | Kullanıcı token'ı | SELECT + `LIMIT 1` | ✅ sorgu · ⚠️ karşılaştırma |
| 3 | OAuth state | `validateState` + silme | ✅ davranış · ⚠️ yöntem |
| 4 | CSRF token | — | ⚠️ VR |
| 5 | Password verify | `password_verify` (kütüphane) | ✅ (credentials §1 #5) |

**Sonuç:** API anahtarı doğrulaması `hash_equals` ile kanıtlanmıştır; kullanıcı
token'ı, OAuth state ve CSRF karşılaştırmalarının yöntemi bu revizyonla
okunmadı → `⚠️` ek madde (A4 incelemesine taşınır).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
