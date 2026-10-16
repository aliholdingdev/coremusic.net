---
node_id: K010-identitiy/account/account-kapsam
node_type: subdomain-scope
status: draft
parent_id: K010-identitiy/account
children: []
depends_on:
  - K010-identitiy
evidence:
  - ".ai/.sql/mysql/coremusic_auth.sql CREATE TABLE users / user_tokens — 2026-10-12"
  - "auth.coremusic.net/include/Repository/UserRepository.php L153 INSERT · L233 reset-token SELECT — 2026-10-12"
validation: "account-dogrulama.md §1 (12 iddia · 8 doğrulanmış · 2 VR · 2 UNKNOWN)"
group: mimari
open_questions:
  - "R1: KVKK unutulma hakkı — soft-delete mi, hard delete mi? (ADR-097 K4 bekliyor)"
  - "R3/R4: e-posta doğrulama use-case kodu ve account_type tier yetkisi — UNKNOWN (A4)"
---

# account — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`identity-master.md` §2 satır 27 bu dalı **"Hesap yaşam döngüsü"** olarak yazar.
Basitçe: bir kullanıcının kaydı doğar, e-postası doğrulanır, gerekirse askıya
alınır veya silinir — işte bu yaşam döngüsünün evi. Kimlik *kime olduğunu*
(user-identity) söyler; hesap ise o kimliğin **operasyonel kaydı**: hangi tier'da,
aktif mi, doğrulandı mı, ne zaman silindi (GDPR unutulma hakkı burada başlar).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Hesap oluşturma kaydı (`users` INSERT yaşam döngüsü) | Kimlik id'sinin tanımı ve sahipliği | user-identity |
| 2 | E-posta doğrulama + parola sıfırlama token akışı (`user_tokens.token_type`) | Token imzası/rotasyon mekaniği | tokens |
| 3 | Askıya alma / ban / soft-delete bayrakları (`is_active` · `is_banned` · `is_deleted`) | Parola hash üretimi | credentials |
| 4 | Hesap tier'ı (`account_type` enum) | Rol atfı ve izin matrisi | roles · permissions |
| 5 | Deneme/kilitlenme sayacı (`failed_login_attempts`) + son giriş metadata'sı | Oturum tanımı ve timeout | session |
| 6 | GDPR/KVKK unutulma hakkının yaşam döngüsü kancası (master §14/2) | Kişisel veri saklama şemasının tamamı | K007 (master §12) |

**Sınır notu:** v2 §2.10 satır 340 bu dalı ayrı düğüm olarak tutar; `account` ↔
`user-identity` birleştirilebilir mi bilinmiyor — bağımlılık defteri bu ayrımı
"bilinen belirsizlik" olarak kaydeder (`identity-bagimliliklar.md` §Ç-10.7/4).

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | account ↔ user-identity tek aggregate mi, iki düğüm mü? | ⚠️ v2 iki satır tutuyor (339, 340) — birleşim kararı yok | identity-bagimliliklar.md §Ç-10.7/4 |
| A2 | Hesap silme ↔ KVKK — hard delete mi, soft delete mi? | ⚠️ ADR-097 K4 bekliyor | identity-master.md §2 risk satırı |
| A3 | E-posta doğrulama akışının uygulama kodu (send/verify use-case) | **UNKNOWN** — A4 kod incelemesi | identity-dogrulama.md §G-10.5 A4 |
| A4 | `account_type` tier değişimi kimin yetkisi? | **UNKNOWN** — rol modeli ile çakışabilir | roles / permissions bu turda işaretli |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Hesap yaşam döngüsü — oluşturma, e-posta doğrulama, askıya alma, silme" | `.ai/architecture/K010-identitiy/identity-master.md` §2 satır 27 | ✅ dosya okundu |
| 2 | `users` tablosu yaşam döngüsü kolonları: `is_active` · `is_banned` · `is_verified` · `failed_login_attempts` · `last_login_at` · `password_changed_at` · soft-delete (`is_deleted`, `deleted_at`) | `.ai/.sql/mysql/coremusic_auth.sql` CREATE TABLE `users` (canlı dump, 2026-10-07) | ✅ dosya okundu |
| 3 | Hesap tier'ı: `account_type enum('free','premium','studio','admin')` | aynı dosya, `users` kolonu | ✅ dosya okundu |
| 4 | E-posta doğrulama + parola sıfırlama token'ları `user_tokens` içinde `token_type enum('password_reset','email_verify',…)` + tek kullanımlık `used_at` | `.ai/.sql/mysql/coremusic_auth.sql` CREATE TABLE `user_tokens` | ✅ dosya okundu |
| 5 | Hesap oluşturma runtime kodu: `INSERT INTO users (id, username, email, password_hash, …)` | `auth.coremusic.net/include/Repository/UserRepository.php` satır 153 | ✅ dosya okundu |
| 6 | Parola sıfırlama sorgusu tek kullanımlık token + aktif hesap koşulu bağlar (`used_at IS NULL AND expires_at > NOW() AND u.is_active = 1 AND u.is_deleted = 0`) | `auth.coremusic.net/include/Repository/UserRepository.php` satır 233 | ✅ dosya okundu |
| 7 | Entity katmanı `password_hash`'i dışa sızdırmaz (`to_array` çıkarımı) | `auth.coremusic.net/include/Domain/Entity/User.php` (satır 52 doc) + testi `tests/Unit/Domain/Entity/UserTest.php` satır 66 | ✅ dosya okundu |
| 8 | Hesap olayları domain event olarak yayınlanır: `UserRegisteredEvent` · `UserLoggedInEvent` · `UserLoggedOutEvent` | `shared/src/Events/Domain/` (3 dosya, `final class … implements DomainEventInterface`) | ✅ dosya varlığı doğrulandı |

> **Not:** dump'taki tablolar canlı şemanın SSOT'u olmakla birlikte **kademe-3**
> (tablo kolon tasarımı) bu dalın bütçesi dışındadır — burada yalnız yaşam
> döngüsü bayraklarının varlığı kanıtlanır.

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Hesap silme ↔ KVKK unutulma hakkı: soft-delete yeterli mi, hard delete gerekiyor mu? | ⚠️ ADR-097 K4 bekliyor |
| R2 | account ↔ user-identity ayrı aggregate mı (master §11/6 açık sorusu)? | ⚠️ VERIFICATION REQUIRED |
| R3 | E-posta doğrulama akışının use-case kodu nerede? | ⚠️ UNKNOWN (A4) |
| R4 | `account_type` tier değişimi hangi rolün yetkisi? | ⚠️ UNKNOWN |

**Kapanış:** status: draft kalır. §3'teki iki belirsizlik kapanmadan bu dosyada
hesap yaşam döngüsü sahipliği iddiası yazılmaz (identity-master §11 · ADR-097 K4).

---

## §6 — RFC 5322 (Internet Message Format) ile e-posta doğrulama kapsamı eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc5322 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 5322, e-posta adresinin (`addr-spec = local-part "@" domain`) formatını
tanımlar. §1 #2'deki "e-posta doğrulama" kapsam satırının dilini bu standart
kurar; bu dal yalnızca `email_verify` token akışının **şema/runtime** kanıtlarını
taşır — adres formatı doğrulayıcısının kendisi UNKNOWN (A3).

### §6.1 — RFC 5322 maddeleri ↔ CoreMusic e-posta yüzeyi

| # | RFC 5322 maddesi | CoreMusic karşılığı | Kanıt (bu vault) | Durum |
|---|---|---|---|---|
| 1 | `addr-spec = local-part "@" domain` (§3.4.1) | `users.email` kolonu | `coremusic_auth.sql` CREATE TABLE `users` | ✅ kanıtlı (kolon) |
| 2 | `local-part` kuralları (dot-atom / quoted-string) | doğrulayıcı kodu bu revizyonla okunmadı | — | ⚠️ VR (A3) |
| 3 | `domain` DNS formu (dot-atom) | — | — | ⚠️ VR (A3) |
| 4 | `obs-*` (eski-syntax) kısıtlamaları | — | — | ⚠️ VR (A3) |
| 5 | Uzunluk sınırları (§2.1: satır 998, RFC 5321 SMTP 254/1000) | `users.email` tipi | `coremusic_auth.sql` | ⚠️ VR (tip uzunluğu okunmadı) |
| 6 | Tekilleşme (aynı adres iki hesapta olmamalı) | unique index kanıtlanmadı | — | ⚠️ VR |
| 7 | Doğrulama token'ı (email_verify) tek kullanımlık | `used_at IS NULL` + `expires_at > NOW()` | `UserRepository.php` satır 233 | ✅ kanıtlı |
| 8 | `email_verify` token tipi şemada | `token_type enum('password_reset','email_verify',…)` | `coremusic_auth.sql` `user_tokens` | ✅ kanıtlı |
| 9 | `is_verified` bayrağı | şemada `tinyint(1) DEFAULT 0` | `coremusic_auth.sql` `users` | ✅ kanıtlı |
| 10 | Doğrulama sonrası UPDATE akışı | use-case kodu A4 | — | ⚠️ UNKNOWN (A3/A4) |

### §6.2 — E-posta doğrulama akışının kanıt zinciri

| # | Adım | Kanıt | Durum |
|---|---|---|---|
| 1 | Kayıtta e-posta alınır | `UserRepository.php` satır 153 (`INSERT … email …`) | ✅ |
| 2 | Token üretilir + `user_tokens`'a yazılır | şema (`token_type='email_verify'`) | ✅ şema |
| 3 | Token gönderilir (mail) | gönderim kodu okunmadı | ⚠️ VR |
| 4 | Token doğrulanır (tek kullanımlık) | SELECT koşulları satır 233 (şablon `password_reset`) | ⚠️ VR (`email_verify` SELECT'i kanıtlanmadı) |
| 5 | `is_verified=1` güncellenir | kanıt yok | ⚠️ VR |
| 6 | Olay yayılır (`UserRegisteredEvent` vb.) | event dosyaları var | ✅ dosya var |

**Eşleme sonucu:** RFC 5322, bu dalın e-posta terminolojisini kurar; şema
kanıtları (`users.email`, `email_verify` token tipi, `is_verified`) sabittir
ancak doğrulayıcı kod (A3) ve akış use-case'i (A4) UNKNOWN olarak kalır —
uydurulmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 — RFC 3339 (Date on the Internet) ile yaşam döngüsü zaman damgaları eşlemesi

> **Kaynak:** https://datatracker.ietf.org/doc/html/rfc3339 · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

RFC 3339, internet protokollerinde tarih/saat biçiminin profilidir
(`full-date "T" partial-time`, `Z` veya `±hh:mm` ofset). Hesap yaşam döngüsü
kolonlarının zaman biçimi bu standartla karşılaştırılır.

### §7.1 — RFC 3339 maddeleri ↔ `users` / `user_tokens` zaman kolonları

| # | Kolon | Yaşam döngüsü rolü | RFC 3339 biçimi kanıtı | Durum |
|---|---|---|---|---|
| 1 | `created_at` | hesap doğuşu | `NOW()` (DB sunucu saati) — biçim okunmadı | ⚠️ VR |
| 2 | `updated_at` | son güncelleme | `NOW()` (satır 153) | ⚠️ VR (biçim) |
| 3 | `last_login_at` | son giriş metadata'sı | biçim/akış kanıtlanmadı | ⚠️ VR |
| 4 | `password_changed_at` | parola yaşı | biçim kanıtlanmadı | ⚠️ VR |
| 5 | `deleted_at` | soft-delete anı | biçim kanıtlanmadı | ⚠️ VR |
| 6 | `user_tokens.expires_at` | token sonu | `expires_at > NOW()` karşılaştırması | ✅ karşılaştırma kanıtlı (satır 233) · ⚠️ biçim |
| 7 | `used_at` | tek kullanımlık kilidi | `used_at IS NULL` | ✅ koşul kanıtlı (satır 233) |

### §7.2 — Zaman kaynağı matrisi

| # | Zaman kaynağı | Kullanım | Kanıt | Durum |
|---|---|---|---|---|
| 1 | DB `NOW()` | INSERT/SELECT zamanı | `UserRepository.php` satır 153 · 233 | ✅ kanıtlı |
| 2 | PHP `time()`/`DateTime` | uygulama zamanı | bu revizyonla okunmadı | ⚠️ VR |
| 3 | TZ ayarı (sunucu/DB) | ofset tutarlılığı | kanıtlanmadı | ⚠️ VR |
| 4 | RFC 3339 `Z` (UTC) taahhüdü | API dışa aktarım | kanıtlanmadı | ⚠️ VR |

### §7.3 — Süre karşılaştırmalarının doğrulama etkisi

| # | Karşılaştırma | Davranış | Durum |
|---|---|---|---|
| 1 | `expires_at > NOW()` | token süresi dolmuşsa reddet | ✅ kanıtlı |
| 2 | `used_at IS NULL` | tek kullanımlık | ✅ kanıtlı |
| 3 | `is_active=1 AND is_deleted=0` | aktif hesap şartı | ✅ kanıtlı |
| 4 | `MAX_LIFETIME`/paradox benzeri süre tutarsızlığı | — | ⚠️ VR (TZ bilinmiyor) |

**Eşleme sonucu:** Zaman kolonları `NOW()` üzerinden karşılaştırılır (kanıtlı);
kolonların RFC 3339 biçim uyumu ve TZ tutarlılığı okunmadı → A4 incelemesine
ek madde olarak taşınır (uydurulmaz).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 — OWASP Forgot Password Cheat Sheet ile sıfırlama akışı eşlemesi

> **Kaynak:** https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

OWASP Forgot Password sayfası, parola sıfırlama akışının güvenlik gereksinimlerini
listeler. CoreMusic sıfırlama akışının kanıtlı parçaları bu gereksinimlerle
eşlenir; kanıtsız parçalar `⚠️`/UNKNOWN olarak kalır.

### §8.1 — OWASP maddeleri ↔ CoreMusic sıfırlama akışı

| # | OWASP maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Sıfırlama bağlantısı/token rastgele üretilmeli | `user_tokens.token_hash` + tek kullanımlık | `UserRepository.php` satır 233 | ✅ kanıtlı (şema+koşul) |
| 2 | Token hızla sonlanmalı (kısa ömür) | `expires_at > NOW()` | aynı satır | ✅ kanıtlı (koşul) · ⚠️ süre değeri VR |
| 3 | Tek kullanımlık olmalı | `used_at IS NULL` | aynı satır | ✅ kanıtlı |
| 4 | Başarısız istekte hesap varlığını açıklama | — | — | ⚠️ VR (yanıt davranışı) |
| 5 | Rate limit (istek başına) | K009 kapsamı (`CacheRateLimiter`) | `identity-kapsam.md` §3 #7 | ✅ sahiplik (K009) · ⚠️ uygulama VR |
| 6 | Sıfırlama sonrası tüm oturumları sonlandır | — | — | ⚠️ VR (akış kanıtlanmadı) |
| 7 | Kullanıcıya bildirim e-postası | gönderim kodu okunmadı | — | ⚠️ VR |
| 8 | Hesap aktif şartı | `is_active=1` | satır 233 | ✅ kanıtlı |
| 9 | Soft-delete hesabı reddet | `is_deleted=0` | satır 233 | ✅ kanıtlı |
| 10 | Token hash'i DB'de saklı (ham değil) | `token_hash` kolonu | `coremusic_auth.sql` `user_tokens` | ✅ kanıtlı (şema) |

### §8.2 — Akış adımları ↔ kanıt durumu

| # | Adım | Durum |
|---|---|---|
| 1 | "Forgot password" isteği (`email` alır) | ⚠️ VR (endpoint kanıtlanmadı) |
| 2 | Token üretimi + `token_type='password_reset'` kayıt | ✅ şema |
| 3 | E-posta gönderimi | ⚠️ VR |
| 4 | Token doğrulama SELECT (satır 233) | ✅ |
| 5 | Yeni parola yazımı (hash) | ✅ (`Password.php` L44) · ⚠️ UPDATE kanıtlanmadı |
| 6 | Token'ı `used_at` ile kapatma | ⚠️ VR (UPDATE kanıtlanmadı) |
| 7 | Oturum sonlandırma | ⚠️ VR |

### §8.3 — Kapsam notları

| # | Not | Kaynak |
|---|---|---|
| 1 | Sıfırlama akışının UI akışı ui-design vault'unda | `identity-kapsam.md` §2 #7 |
| 2 | Rate limit politikası K009'da | §2 #1 |
| 3 | Token imzası/sha256 dönüştürme detayı tokens dalında | §2 #2 |

**Eşleme sonucu:** OWASP maddelerinin 4'ü kanıtla örtüşür (tek kullanımlık +
süre + aktif hesap + ham olmayan token); 6 madde `⚠️ VERIFICATION REQUIRED`
kalır — akış use-case kodu A4 incelemesine bağlıdır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 — OWASP Authentication Cheat Sheet ile hesap yaşam döngüsü eşlemesi

> **Kaynak:** https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html · **Doğrulama:** HTTP 200 (HEAD) · **Tarih:** 2026-10-13

OWASP Authentication sayfası, kimlik doğrulama ve hesap yaşam döngüsü politikalarını
tanımlar (parola politikası, MFA, kurtarma, credential reuse screening). Bu dalın
kapsam sınırı (§2) içindeki hesap bayrakları bu politikalarla eşlenir.

### §9.1 — OWASP maddeleri ↔ CoreMusic hesap bayrakları

| # | OWASP maddesi | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Kimlik bilgisi reuse screening (ihlal parolaları) | — | — | ❌ PLANNED |
| 2 | MFA (özellikle admin) | MFA implementasyonu yok | `identity-kapsam.md` §1 #8 | ❌ PLANNED |
| 3 | Credential recovery güçlü olmalı | sıfırlama akışı §8 | ✅ kısmi | 
| 4 | Parola değişimi sonrası oturum temizliği | — | — | ⚠️ VR |
| 5 | Başarısız girişlerde standart mesaj | — | — | ⚠️ VR |
| 6 | Hesap kilidi / lockout politikası | `failed_login_attempts` sayacı | `coremusic_auth.sql` `users` | ✅ kolon · ⚠️ politika UNKNOWN (A4) |
| 7 | Deneme sayacı sıfırlama (başarılı giriş) | — | — | ⚠️ VR |
| 8 | `is_banned` bayrağı | şemada mevcut | `coremusic_auth.sql` | ✅ kanıtlı (kolon) |
| 9 | `is_active` bayrağı (durdurma) | şemada mevcut | `coremusic_auth.sql` | ✅ kanıtlı (kolon) |
| 10 | Giriş reddi akışı (banlı hesap) | — | — | ⚠️ VR |

### §9.2 — Hesap durum matrisi (şema ↔ davranış)

| # | Durum kombinasyonu | Beklenen davranış | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `is_active=0` | giriş reddi | `is_active=1` şartı (satır 233) | ✅ (token akışında) · ⚠️ login akışı VR |
| 2 | `is_banned=1` | giriş reddi | — | ⚠️ VR |
| 3 | `is_deleted=1` | giriş reddi + KVKK | `is_deleted=0` şartı (satır 233) | ✅ (token akışında) |
| 4 | `failed_login_attempts ≥ eşik` | kilitleme | — | ⚠️ UNKNOWN (eşik yok) |
| 5 | `is_verified=0` | kısıtlı erişim? | — | ⚠️ VR (politika yok) |

### §9.3 — Kapsam dışı bırakılanlar (bu dal iddia etmez)

| # | İddia EDİLMEZ | Sahip |
|---|---|---|
| 1 | "Login rate limit uygulanır" | K009 (`identity-kapsam.md` §5 #5) |
| 2 | "MFA vardır" | PLANNED (`identity-kapsam.md` §5 #1) |
| 3 | "Parola politikası tanımlıdır" | account policy + OWASP M05 (credentials §2 #2) |
| 4 | "Kilitlenme eşiği belirlenmiştir" | UNKNOWN (A4) |

**Eşleme sonucu:** OWASP Authentication maddelerinin dördü şema kanıtıyla
örtüşür (bayraklar + sayacı); davranışsal politikalar (lockout eşiği, standart
mesaj, oturum temizliği) bu dalda **tanımsızdır** — ADR-097 K4 ve A4 incelemesi
öncesi iddia edilmez.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
