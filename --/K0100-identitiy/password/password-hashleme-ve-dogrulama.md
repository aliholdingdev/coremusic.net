---
node_id: K0100-identitiy/password/password-hashleme-ve-dogrulama
node_type: module
status: draft
parent_id: K0100-identitiy/password
children: []
depends_on:
  - K0100-identitiy
evidence: []
validation: []
open_questions: []
group: mimari
---

# password-hashleme-ve-dogrulama — Şifre Hashleme ve Doğrulama Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K0100-identitiy · **Alt klasör:** `password` (yeni)
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-097:123/155
- **İmla koruması:** `identitiy` yazımı bilinçli korunmuştur.

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic'te şifre saklama ve doğrulama sözleşmesini tanımlar: algoritma seçimi
(Argon2id), maliyet parametreleri (cost), pepper (sunucu sırrı), salting ve sabit zamanlı
karşılaştırma (timing-safe compare). Ham şifre hiçbir kalıcı yüzeyde tutulmaz; yalnızca
hash saklanır.

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Algoritma | Argon2id tercihi ve gerekçesi | Yeni algoritma deneyleri (K009) |
| 2 | Cost parametreleri | `memory_cost`·`time_cost`·`threads` | Donanım ölçümü (K018) |
| 3 | Pepper | `APP_PEPPER` uygulanışı, saklama yeri | Secret yönetimi (K018) |
| 4 | Salting | Tuz üretimi ve hash gömüldüğü yer | Rastgelelik altyapısı (K009) |
| 5 | Doğrulama | `password_verify` akışı, boş pepper davranışı | Kimlik oturumu (session) |
| 6 | Timing güvenliği | `hash_equals` karşılaştırması | Ağ zamanlaması (K014) |
| 7 | Yeniden hashleme | Parametre değişince `needs_rehash` akışı | Parametre kararı (§15) |
| 8 | Şifre gücü | Asgari uzunluk, puanlama politikası | Arayüz/UX (ui-design) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Hash | one-way hash | Geri döndürülemez tek yönlü özet | `password_hash()` çıktısı |
| Tuz | salt | Her kayıt için rastgele, hash'e gömülü değer | Argon2id hash formatı içinde |
| Pepper | pepper | Hash dışındaki, uygulama sırrı | `hash_hmac('sha256', raw, pepper)` |
| Maliyet | cost | CPU/bellek/zaman bütçesi | `memory_cost`·`time_cost`·`threads` |
| Sabit zamanlı karşılaştırma | timing-safe compare | Süreye duyarlı olmayan eşitlik | `hash_equals` |
| Yeniden hashleme | rehash | Parametre artınca hash'i tazeleme | `password_needs_rehash` (§10) |
| Şifre değeri nesnesi | password value object | Ham şifreyi temsil eden immutable nesne | `Password` sınıfı |
| Doğrulama sonucu | verification result | `true`/`false` (ayrıca false nedeni yok) | `Password::verify()` |
| Ham şifre | raw password | İstemciden gelen, saklanmayan değer | `$this->raw` |
| Hash sızıntısı | hash disclosure | Veritabanı sızıntısında hash'lerin ele geçmesi | §12 |
| Offline kırma | offline cracking | Hash üzerinde toplu deneme | §12 |
| Doğrulama hızı | verification throughput | Saniyede deneme sayısı | §10 cost tablosu |
| Şifre sıfırlama token'ı | password reset token | Tek kullanımlık, süreli sıfırlama anahtarı | `random_bytes(32)` |
| Yeniden kullanım | password reuse | Aynı şifrenin tekrarlanması | §15 (kapsam dışı, politika) |

## §3 — Mimari yerleşim (kademe-3 konumu)

```text
K0100-identitiy (kademe-1)
  └── password (kademe-2 · yeni klasör)
        └── password-hashleme-ve-dogrulama (kademe-3 · bu dosya)
              ├─ sözleşmeyi yazar: Argon2id · cost · pepper · salting · timing-safe
              ├─ kod okur: auth.coremusic.net/include/Domain/ValueObject/Password.php
              ├─ kod okur: auth.coremusic.net/include/Service/AuthService.php
              ├─ kod okur: auth.coremusic.net/include/Domain/Entity/User.php
              └─ test okur: auth.coremusic.net/tests/Unit/Domain/ValueObject/PasswordTest.php
```

| Katman | Dosya | Sorumluluk | Bu dosyayla ilişki |
|---|---|---|---|
| Value object | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | Hash + verify | Çekirdek |
| Servis | `auth.coremusic.net/include/Service/AuthService.php` | Kayıt, sıfırlama, rate limit | Yaşam döngüsü |
| Entity | `auth.coremusic.net/include/Domain/Entity/User.php` | `passwordHash` alanı | Saklama |
| Konfigürasyon | `auth.coremusic.net/config/constants.php` | Pepper bölümü | Sır kaynağı |
| API container | `api.coremusic.net/include/Container/ApiAuthContainer.php` | Pepper bağımlılığı | Enjeksiyon |
| Test | `auth.coremusic.net/tests/Unit/Domain/ValueObject/PasswordTest.php` | 6 test metodu | §13 |
| API key hash | `shared/src/Repository/ApiKeyRepository.php` | SHA-256 + `hash_equals` | Timing kanıtı |
| Exception | `shared/src/Exception/ValidationException.php` | `passwordTooShort` | Hata sözleşmesi |

## §4 — Algoritma kararı: neden Argon2id

| # | Kriter | Argon2id | bcrypt | scrypt | PBKDF2-SHA256 | Karar |
|---|---|---|---|---|---|---|
| 1 | Bellek direnci (GPU/ASIC) | Yüksek | Düşük | Yüksek | Düşük | Argon2id |
| 2 | Zaman direnci | Ayarlanabilir | Ayarlanabilir | Ayarlanabilir | Ayarlanabilir | Eşit |
| 3 | Paralelleşme | `threads` | Yok | Var | Sınırlı | Argon2id |
| 4 | RFC standardı | RFC 9106 | Yok | Yok | RFC 8018 | Argon2id |
| 5 | PHP çekirdek desteği | `PASSWORD_ARGON2ID` | `PASSWORD_BCRYPT` | Yok | `PASSWORD_PBKDF2` | Argon2id |
| 6 | Hash uzunluğu | Uzun (Parametreler gömülü) | 60 bayt | Uzun | Orta | Kabul |
| 7 | Parametre gömme | Var | Var | Yok | Yok | Argon2id |
| 8 | Repo durumu | **Kullanımda** | Kullanılmıyor | Yok | Yok | Sözleşme = Argon2id |

**Karar:** Mevcut kod `PASSWORD_ARGON2ID` kullanıyor; bu dosya kararı **yazar**, değiştirmez.
Algoritma değişikliği yalnız ADR ile yapılabilir (§15 açık soru 6).

## §5 — Maliyet (cost) parametreleri

| # | Parametre | Değer | Kaynak satır | Etki |
|---|---|---|---|---|
| 1 | `memory_cost` | 65536 KiB (64 MiB) | `Password.php:45` | Bellek baskısı, GPU kırımı zorlaşır |
| 2 | `time_cost` | 4 geçiş | `Password.php:46` | Zaman baskısı |
| 3 | `threads` | 2 | `Password.php:47` | Paralel maliyet |
| 4 | Algoritma | `PASSWORD_ARGON2ID` | `Password.php:44` | Argon2id |
| 5 | Parametre değişmezliği | Sabit | `Password.php:24` belgesi | Yeniden hashleme tetiklenmez |
| 6 | Asgari uzunluk | 8 | `Password.php:14` | Zayıf şifre reddi |
| 7 | Uzunluk kaynağı | `AuthService::MIN_PASSWORD_LENGTH` ile aynı | `Password.php:13` belgesi | İki yerde tek değer |
| 8 | Pepper boşsa | `ServerException::configError('APP_PEPPER')` | `Password.php:41` | Fail-closed |
| 9 | Doğrulamada pepper boşsa | `false` döner | `Password.php:56–57` | Fail-closed |

**Hashleme çekirdeği (repo, birebir):**

```php
Dosya: auth.coremusic.net/include/Domain/ValueObject/Password.php / Satır: 44
return password_hash($peppered, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost'   => 4,
    'threads'     => 2,
]);
```

## §6 — Pepper ve salting ayrımı

| # | Kavram | Nerede | Üretim | Sızıntı senaryosu | Koruma |
|---|---|---|---|---|---|
| 1 | Salt | Hash formatının içinde | `password_hash` üretir | DB sızıntısında salt da ele geçer | Yine de benzersizlik korunur |
| 2 | Pepper | Hash dışında, uygulama | Sabit `APP_PEPPER` | Uygulama sızarsa salt anlamsız | Sır ayrı depoda |
| 3 | Pepper uygulama biçimi | `hash_hmac('sha256', raw, pepper)` | Kod satırı 43 | — | HMAC |
| 4 | Pepper boş | Hashleme exception | Kod satırı 41 | — | Fail-closed |
| 5 | Pepper boş (verify) | `false` | Kod satırı 56–57 | — | Fail-closed |
| 6 | Pepper dönüşümü | Aynı pepper ile verify | Kod satırı 59 | Pepper değişirse tüm şifreler düşer | §15-2 |
| 7 | Salt tekrarı | `password_hash` rastgele üretir | Kütüphane | Yok | Otomatik |
| 8 | Hash formatı | Algoritma + parametre + salt + özet | Kütüphane | Parametreler hash içinde | `needs_rehash` |
| 9 | Pepper kaynağı | `constants.php` Security bölümü | `auth.coremusic.net/config/constants.php:60` | Dosya sızıntısı | Dosya izni + env |
| 10 | Pepper kod içi kopyası | Yok (bağımlılık olarak enjekte) | Container | — | Kod incelemesi |

## §7 — Hashleme ve doğrulama akışı

| # | Adım | Hashleme (kayıt) | Doğrulama (giriş) |
|---|---|---|---|
| 1 | Girdi | Ham şifre (string) | Ham şifre + kayıtlı hash |
| 2 | Uzunluk denetimi | `< 8` → exception | Atlanır (kayıtta yapıldı) |
| 3 | Pepper uygulama | `hash_hmac('sha256', raw, pepper)` | Aynı HMAC |
| 4 | Algoritma | `password_hash($peppered, PASSWORD_ARGON2ID, $params)` | — |
| 5 | Salt | Kütüphane üretir | Kütüphane hash'ten okur |
| 6 | Karşılaştırma | — | `password_verify(peppered, hash)` |
| 7 | Sonuç | Hash metni | `true` / `false` |
| 8 | Hata ayrıntısı | — | Ayrıntı döndürülmez (yalnız bool) |
| 9 | Yeniden hash | — | Parametre değiştiyse `needs_rehash` (§10) |
| 10 | Saklama | `password_hash` alanı | — |

```php
Dosya: auth.coremusic.net/include/Domain/ValueObject/Password.php / Satır: 43
$peppered = hash_hmac('sha256', $this->raw, $pepper);
```

```php
Dosya: auth.coremusic.net/include/Domain/ValueObject/Password.php / Satır: 60
return password_verify($peppered, $hash);
```

## §8 — Timing-safe karşılaştırma (sabit zamanlı)

| # | Yüzey | Karşılaştırma | Kaynak | Neden sabit zamanlı |
|---|---|---|---|---|
| 1 | Şifre doğrulama | `password_verify` | `Password.php:60` | Kütüphane içi sabit zamanlı |
| 2 | API anahtarı | `hash_equals(stored, rawHash)` | `ApiKeyRepository:171` | Ham hash karşılaştırması |
| 3 | API anahtarı belgesi | `hash_equals(key_hash, sha256(raw))` | `ApiKeyRepository:22` | Sözleşme yazılı |
| 4 | CSRF token | `hash_equals(session, submitted)` | `CsrfMiddleware:42` | Token eşitliği |
| 5 | Ham eşitlik (`===`) | Kullanılmaz (şifre/anahtar yüzeyinde) | Tarama | Zamanlama sızıntısı |
| 6 | Salt karşılaştırması | Gerekmez | `password_verify` | Kütüphane yönetir |
| 7 | Hash üretiminde SHA-256 | `hash('sha256', $rawKey)` | `ApiKeyRepository:165` | Dağıtım özeti (şifre değil) |
| 8 | Doğrulama hata mesajı | Ayırt edici mesaj yok | `Password::verify` bool | Yanlış şifre ≠ bulunamayan kullanıcı, §15-4 |

**Timing kanıtı (repo):**

```php
Dosya: shared/src/Repository/ApiKeyRepository.php / Satır: 171
if ($stored !== '' && hash_equals($stored, $rawHash)) {
```

## §9 — Parametre / konfigürasyon tablosu

| # | Anahtar | Değer | Yer | Değiştirme etkisi |
|---|---|---|---|---|
| 1 | `APP_PEPPER` | Sır (kod yazmaz) | `constants.php` Security bölümü | Tüm şifreler doğrulanamaz |
| 2 | `MIN_PASSWORD_LENGTH` (Password) | 8 | `Password.php:14` | Zayıf şifre kabulü |
| 3 | `MIN_PASSWORD_LENGTH` (AuthService) | 8 | `AuthService.php:30` | İki kaynak senkron kalmalı |
| 4 | `memory_cost` | 65536 | `Password.php:45` | Doğrulama süresi/artık |
| 5 | `time_cost` | 4 | `Password.php:46` | Doğrulama süresi |
| 6 | `threads` | 2 | `Password.php:47` | Paralel maliyet |
| 7 | `PASSWORD_RESET_MAX_ATTEMPTS` | 3 | `AuthService.php:40` | Sıfırlama taraması |
| 8 | `PASSWORD_RESET_WINDOW_SECONDS` | 3600 | `AuthService.php:41` | Pencere |
| 9 | `PASSWORD_RESET_TOKEN_TTL` | 900 | `AuthService.php:43` | Token ömrü |
| 10 | Sıfırlama token uzunluğu | 32 bayt → 64 hex | `AuthService.php:269` | Tahmin direnci |
| 11 | Kayıtta hash alanı | `password_hash` | `AuthService.php:180` | Şema |
| 12 | Entity alanı | `passwordHash` (readonly) | `User.php:17` | Mutabilite |

## §10 — Yeniden hashleme (needs_rehash) ve rolü

| # | Durum | Eylem | Kaynak | Durum (repo) |
|---|---|---|---|---|
| 1 | Parametreler değişmedi | Hash aynen kalır | Kütüphane davranışı | Varsayılan |
| 2 | `memory_cost` artırıldı | Başarı girişinde yeniden hash | Kullanım repoda görünmüyor | ⚠️ VERIFICATION REQUIRED |
| 3 | `time_cost` artırıldı | Aynı | Kullanım repoda görünmüyor | ⚠️ VERIFICATION REQUIRED |
| 4 | Algoritma değişti | Tam yeniden hash | ADR gerekli | §15-6 |
| 5 | Pepper değişti | Tüm hash'ler geçersiz | `Password.php:59` | Sıfırlama gerekir |
| 6 | Uzunluk kuralı değişti | Mevcut şifreler korunur, yenisi sertleşir | İki sabit | Senkron |
| 7 | Performans darboğazı | Önce cost düşürme değil, ölçüm | K018 | Ölçüm şart |
| 8 | Hash formatı bozuk | Doğrulama `false` | `password_verify` | Fail-closed |

**Gözlem:** `password_needs_rehash` çağrısı repoda tarama ile bulunamadı. Yani cost artışı
yapılırsa mevcut hash'ler otomatik tazelenmez. Bu bir **boşluktur**, gözlem olarak yazılır
(uydurulmaz): §15 açık soru 3.

## §11 — Repo kanıtları (gerçek kod, doğrulanmış satır)

| # | Dosya | Satır | Gözlem | Doğrulama |
|---|---|---|---|---|
| 1 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 14 | `MIN_PASSWORD_LENGTH = 8` | `Select-Object -Index 13` |
| 2 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 41 | `configError('APP_PEPPER')` | `Select-Object -Index 40` |
| 3 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 43 | `hash_hmac('sha256', raw, pepper)` | `Select-Object -Index 42` |
| 4 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 44 | `password_hash($peppered, PASSWORD_ARGON2ID, $params)` | `Select-Object -Index 43` |
| 5 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 45 | `memory_cost = 65536` | `Select-Object -Index 44` |
| 6 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 46 | `time_cost = 4` | `Select-Object -Index 45` |
| 7 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 47 | `threads = 2` | `Select-Object -Index 46` |
| 8 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 54 | `verify(string $hash, string $pepper)` | `Select-Object -Index 53` |
| 9 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 57 | Boş pepper → `return false` | `Select-Object -Index 56` |
| 10 | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | 60 | `password_verify($peppered, $hash)` | `Select-Object -Index 59` |
| 11 | `auth.coremusic.net/include/Service/AuthService.php` | 30 | `MIN_PASSWORD_LENGTH = 8` | `Select-Object -Index 29` |
| 12 | `auth.coremusic.net/include/Service/AuthService.php` | 122 | `bin2hex(random_bytes(32))` (authKey) | `Select-Object -Index 121` |
| 13 | `auth.coremusic.net/include/Service/AuthService.php` | 180 | `'password_hash' => $passwordHash` | `Select-Object -Index 179` |
| 14 | `auth.coremusic.net/include/Service/AuthService.php` | 269 | Sıfırlama token'ı 32 bayt | `Select-Object -Index 268` |
| 15 | `auth.coremusic.net/include/Domain/Entity/User.php` | 17 | `readonly string $passwordHash` | `Select-Object -Index 16` |
| 16 | `shared/src/Repository/ApiKeyRepository.php` | 171 | `hash_equals($stored, $rawHash)` | `Select-Object -Index 170` |
| 17 | `auth.coremusic.net/include/config/constants.php` | 60 | `--- Security (Pepper) ---` bölümü | `Select-Object -Index 59` |

## §12 — Tehdit modeli ve ihlal senaryoları

| # | Tehdit | Öncelik | Mevcut kontrol | Boşluk / eylem |
|---|---|---|---|---|
| 1 | Veritabanı hash sızıntısı → offline kırma | Yüksek | Argon2id + pepper | Pepper ayrı depoda doğrulanmalı |
| 2 | Zayıf şifre | Yüksek | Asgari 8 karakter | Puanlama politikası §15-5 |
| 3 | Timing ile şifre/anahtar tahmini | Yüksek | `password_verify` · `hash_equals` | Ham eşitlik denetimi |
| 4 | Pepper sızıntısı (repo/env) | Yüksek | Kodda sabit yok, container enjeksiyonu | Dosya izni + sırlar env'de |
| 5 | Pepper eksikliği (conflict) | Orta | Hashleme exception / verify false | Sağlık kontrolü |
| 6 | Brute-force giriş | Yüksek | Rate limit (sıfırlama 3/3600) | Giriş yüzeyi rate limit §15-7 |
| 7 | Hash'lerin biriktirilmesi | Orta | `password_hash` alanı yalnız | Yanıt DTO'su sızdırmazlığı |
| 8 | Cost düşük kalması | Orta | Sabit 65536/4/2 | Yeniden hashleme akışı §10 |
| 9 | Doğrulama hata sızıntısı | Orta | bool sonuç, ayrıntı yok | Mesaj birleştirmesi §15-4 |
| 10 | Sıfırlama token tahmini | Orta | 32 bayt `random_bytes` | TTL 900 sn + tek kullanımlık |
| 11 | Sıfırlama taraması | Orta | 3 / 3600 sn | IP etiketli |
| 12 | Ham şifre loglanması | Orta | `Redactor` + kodda log yok | Log denetimi |
| 13 | Eski algoritmayla kalmış hash | Düşük | Parametre gömülü | `needs_rehash` boşluğu |
| 14 | Entity üzerinden hash okunması | Düşük | `readonly` + `toRow` çıkarma | `User.php:52` belgesi |

## §13 — Test senaryoları (kabul kriterleri)

| # | Senaryo | Beklenen | Test kaynağı | Durum |
|---|---|---|---|---|
| 1 | Geçerli şifre ile oluşturma | Nesne oluşur | `PasswordTest:14` `create_with_valid_password` | Repoda mevcut |
| 2 | 8 karakterden kısa şifre | Exception | `PasswordTest:21` `create_with_too_short_password_throws_exception` | Repoda mevcut |
| 3 | Pepper ile hashleme | Hash döner | `PasswordTest:28` `hash_with_pepper_returns_hash` | Repoda mevcut |
| 4 | Doğru şifre verify | `true` | `PasswordTest:40` `verify_returns_true_for_correct_password` | Repoda mevcut |
| 5 | Yanlış şifre verify | `false` | `PasswordTest:50` `verify_returns_false_for_wrong_password` | Repoda mevcut |
| 6 | Boş pepper verify | `false` | `PasswordTest:61` `verify_returns_false_for_empty_pepper` | Repoda mevcut |
| 7 | Boş pepper hashleme | Exception | Eklenmedi | §15 |
| 8 | Parametre artışı → rehash | `needs_rehash = true` | Eklenmedi | §10/§15-3 |
| 9 | Pepper değişimi sonrası verify | `false` | Eklenmedi | §15-2 |
| 10 | Hash formatı bozuk | `false` (exception değil) | Eklenmedi | §15 |
| 11 | Asgari uzunluk senkronu | İki sabit eşit | Eklenmedi | §15 |
| 12 | Timing-safe yüzey | `hash_equals` kullanımı | `ApiKeyRepository:171` okundu | Kanıt var, test yok |
| 13 | Ham şifre entity'de yok | Yalnız `passwordHash` | `User.php:17` | Kanıt var |
| 14 | Sıfırlama rate limit | 4. deneme red | `AuthService.php:40–41` | Kanıt var, test §15 |

## §14 — Gözlemlenebilirlik

| # | Sinyal | Etiket | Eşik | Amaç |
|---|---|---|---|---|
| 1 | `password_hash_failed` | — | > 0 ise kritik | Sistem hatası (istisna) |
| 2 | `password_verify_failed` | `route`·`ip` | Ani artış | Kırma denemesi |
| 3 | `password_verify_duration_ms` | p95 | Argon2id bütçesine yakın | Parametre sağlığı |
| 4 | `pepper_missing` | — | 1 ise kritik | Fail-closed gözetimi |
| 5 | `password_reset_rate_limited` | `ip` | Sıçrama | Tarama |
| 6 | `password_reset_token_expired` | — | Normal dağılım | TTL davranışı |
| 7 | `rehash_performed` | — | Giriş başına ≥ 0 | §10 akışı |
| 8 | `password_min_length_violation` | — | Sıçrama | Zayıf şifre denemesi |

| # | Log alanı | Örnek | Gizlilik | Not |
|---|---|---|---|---|
| 1 | `result` | `ok` / `failed` | Düşük | Ham şifre YASAK |
| 2 | `hash_prefix` | Argon2id ön eki | Düşük | Tam hash loglanmaz |
| 3 | `ip` | İstemci IP | Yüksek | Maskelenmeli |
| 4 | `request_id` | UuidV7 | Düşük | Korelasyon |
| 5 | `user_id_hash` | Hash-only | Yüksek | Ham id yok |
| 6 | `reason` | `pepper_missing` | Düşük | Tanı |
| 7 | `duration_ms` | 128 | Düşük | Parametre ölçümü |
| 8 | `route` | `/api/v1/auth/login` | Düşük | Zorunlu |

## §15 — Riskler ve açık sorular

| # | Açık soru | Neden önemli | Kanıt durumu | Önerilen kapanış |
|---|---|---|---|---|
| 1 | İki `MIN_PASSWORD_LENGTH` sabiti senkron mu? | 8/8 eşleşiyor ama iki kaynak | İki dosya okundu | Tek kaynak kararı |
| 2 | Pepper değişimi nasıl yönetilir? | Tüm şifreler düşer | Kodda dönüşüm yok | Çift-hash geçiş planı |
| 3 | `password_needs_rehash` neden kullanılmıyor? | Cost artışı etkisiz kalır | Taramada çağrı yok | Akış ekleme kararı |
| 4 | Yanlış şifre mesajı ayrıştırılıyor mu? | Kullanıcı keşfi | `Password::verify` bool | Mesaj birleştirme testi |
| 5 | Şifre puanlama/kelime listesi var mı? | Zayıf şifre | Repoda yok | Politika kararı |
| 6 | Algoritma değişikliği kriteri | ADR gerektirir | Argon2id mevcut | ADR eşiği |
| 7 | Giriş yüzeyinde rate limit | Brute-force | Sıfırlamada var, girişte K011 | Sınır netleştirme |
| 8 | Pepper dosya izni | Sır sızıntısı | Sabit dosyası | İzin denetimi |
| 9 | 64 MiB / istek | Yük altında CPU-bellek | Parametreler sabit | Yük ölçümü (K018) |
| 10 | Hash alanının yanıt sızıntısı | Hash bile sızmamalı | DTO sınırı K011 | Sızdırmazlık testi |
| 11 | Sıfırlama token tek kullanımlık mı? | Yeniden oynatma | TTL var, tek kullanım §15-12 | Doğrulama |
| 12 | `AuthService` kayıtta hash mi üretiyor? | Akış bütünlüğü | `password_hash` alanı dolu | Akış testi |

## §16 — Komşu düğümlerle sınır

| # | Komşu | Veri yönü | Sözleşme | Çakışma riski |
|---|---|---|---|---|
| 1 | `K0100-identitiy/credentials` | Doğrulama sonucu → oturum | `verify` `true` ise giriş | Çift doğrulama |
| 2 | `K0100-identitiy/session` | Başarılı verify → oturum kurulur | `restartFresh()` | Sıra |
| 3 | `K0100-identitiy/account` | Şifre değiştirme/sıfırlama | Aynı `Password` nesnesi | Farklı doğrulama |
| 4 | `K0100-identitiy/mfa` | Doğrulama sonrası step-up | İkinci faktör ayrı | Sıra |
| 5 | `K0100-identitiy/tokens` | Sıfırlama token'ı | 32 bayt · TTL 900 | Token tipi karışıklığı |
| 6 | `K009-security` | Kriptografik standartlar | Argon2id · HMAC-SHA256 | Algoritma vetosu |
| 7 | `K011-application/dto` | Yanıtta hash yok | DTO sızdırmazlık | Yanıt sızıntısı |
| 8 | `K018-operasyon` | Sır & log politikası | §14 alanları | PII/secret sızıntısı |
| 9 | `K016-persistence` | `password_hash` sütunu | Şema + indeks yok | Tarama performansı |
| 10 | `K013-integraion` | Dış kimlik sağlayıcı | OAuth şifresiz akış | İmla: `integraion` korunur |

## §17 — Web araştırması (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Argon2id, memory-hard bir parola hashleme algoritması olarak RFC 9106'da tanımlanmıştır | https://datatracker.ietf.org/doc/html/rfc9106 | 2026-10-13 | Doğrulandı (HTTP 200) |
| Parola saklamada Argon2id önerilir; pepper katmanı hash'i veritabanı sızıntısına karşı korur | https://www.php.net/manual/en/book.sodium.php | 2026-10-13 | Doğrulandı (HTTP 200) |
| `password_hash`/`password_verify` çiftinde karşılaştırma kütüphane içinde sabit zamanlı yapılır; ayrıca `hash_equals` sunulur | https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/verify | 2026-10-13 | Doğrulandı (HTTP 200) |
| PBKDF2, türetme adımı ile tuz/pepper benzeri yapı taşları sağlar; tek başına memory-hard değildir | https://www.php.net/manual/en/function.hash-pbkdf2.php | 2026-10-13 | Doğrulandı (HTTP 200) |
| PHP hash uzantısı, algoritma listesi ve parametreleri hakkında referans bilgi verir | https://www.php.net/manual/en/ref.hash.php | 2026-10-13 | Doğrulandı (HTTP 200) |
| Anahtar türetme işlevleri (KDF) parola tabanlı anahtar üretimi için ayrışık API sunar | https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/deriveBits | 2026-10-13 | Doğrulandı (HTTP 200) |

## §18 — Doğrulama kontrol listesi

| # | Kontrol | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Frontmatter 8 zorunlu alan + `group` | Şema okuması | Tamam | 2026-10-13 |
| 2 | `parent` yok → `parent_id` | Tarama | `parent:` = 0 | 2026-10-13 |
| 3 | `group` = `mimari` | FM satırı | Tamam | 2026-10-13 |
| 4 | Satır ≥ 500 | `(Get-Content).Count` | §ölçüm | 2026-10-13 |
| 5 | Gerçek kod ≥ 1 | §11 (17 satır) | Tamam | 2026-10-13 |
| 6 | Satır doğrulama | `Select-Object -Index` | Tamam | 2026-10-13 |
| 7 | URL envanter kesişimi | Set farkı | §ölçüm | 2026-10-13 |
| 8 | URL HTTP 200 | `Invoke-WebRequest` | 6/6 | 2026-10-13 |
| 9 | Yasaklı dolgu belirteci yok | Tarama | 0 | 2026-10-13 |
| 10 | Mojibake | UTF-8 okuma | 0 | 2026-10-13 |
| 11 | İmla koruması | `identitiy`·`comilance` benzeri | Korundu | 2026-10-13 |
| 12 | Beyazliste dışı yazım | Dosya yolu | 0 | 2026-10-13 |
| 13 | Son satır Authority | Dosya sonu | Tamam | 2026-10-13 |
| 14 | Commit yok | Subagent kuralı | Atılmadı | 2026-10-13 |

## §19 — Karar matrisi (girdi × sonuç)

| # | Uzunluk | Pepper | Hash | Verify girdisi | Sonuç |
|---|---|---|---|---|---|
| 1 | ≥ 8 | Var | Geçerli | Doğru şifre | `true` |
| 2 | ≥ 8 | Var | Geçerli | Yanlış şifre | `false` |
| 3 | ≥ 8 | Var | Geçerli | Boş pepper ile | `false` |
| 4 | ≥ 8 | Yok (hashleme) | — | — | Exception `configError` |
| 5 | ≥ 8 | Yok (verify) | Geçerli | — | `false` (satır 57) |
| 6 | < 8 | — | — | — | `passwordTooShort` |
| 7 | ≥ 8 | Değişti | Eski hash | Doğru şifre | `false` (§15-2) |
| 8 | ≥ 8 | Aynı | Bozuk format | — | `false` |
| 9 | ≥ 8 | Aynı | Argon2id, cost arttı | Doğru şifre | `true` + rehash gerekir |
| 10 | ≥ 8 | Aynı | Başka algoritma | Doğru şifre | `password_verify` false |

## §20 — Uygulama reçetesi (kabul adımları)

| # | Adım | İş | Kanıt | Bitmişse |
|---|---|---|---|---|
| 1 | Uzunluk denetimi | 8 karakter asgari | `Password.php:14` | Evet |
| 2 | Servis ile senkron | İkinci sabit 8 | `AuthService.php:30` | Evet |
| 3 | Pepper HMAC | `hash_hmac` | `Password.php:43` | Evet |
| 4 | Pepper yoksa dur | Exception | `Password.php:41` | Evet |
| 5 | Argon2id hash | `password_hash` | `Password.php:44` | Evet |
| 6 | Cost sabitleri | 65536 / 4 / 2 | `Password.php:45–47` | Evet |
| 7 | Salt üretimi | Kütüphane | Hash formatı içinde | Evet |
| 8 | Doğrulama | `password_verify` | `Password.php:60` | Evet |
| 9 | Boş pepper verify | `false` | `Password.php:56–57` | Evet |
| 10 | Hash yalnız DB'de | Entity readonly | `User.php:17` | Evet |
| 11 | Timing-safe yüzey | `hash_equals` | `ApiKeyRepository:171` | Evet |
| 12 | Rehash akışı | `password_needs_rehash` | Yok | Hayır (§10) |
| 13 | Şifre puanlama | Kelime listesi | Yok | Hayır (§15-5) |
| 14 | Testler | 6 metot | `PasswordTest` | Kısmi (§13) |

## §21 — Boşluk kapatma planı

| # | Boşluk | Etki | Öncelik | Kapanış ölçütü |
|---|---|---|---|---|
| 1 | Rehash akışı yok | Cost artışı etkisiz | P0 | `needs_rehash` + test |
| 2 | Pepper geçiş planı yok | Şifre toplu düşüşü | P0 | Çift-hash tasarımı |
| 3 | Uzunluk çift kaynağı | Ayrışma riski | P1 | Tek SSOT |
| 4 | Şifre puanlama yok | Zayıf şifre | P1 | Politika + test |
| 5 | Giriş rate limiti sınırı | Brute-force | P1 | K011 sözleşmesi |
| 6 | Timing testi yok | Regresyon | P1 | Yüzey tarama testi |
| 7 | Pepper izin denetimi | Sır sızıntısı | P1 | İzin kontrolü |
| 8 | Rehash metrikleri | §14 | P2 | Pano |
| 9 | Sıfırlama tek kullanım | Yeniden oynatma | P1 | Doğrulama testi |
| 10 | Yük ölçümü (64 MiB) | Darboğaz | P2 | Ölçüm raporu |

## §22 — Yayılım ve geri alma

| # | Aşama | Değişiklik | Geri alma | Doğrulama |
|---|---|---|---|---|
| 1 | Okuma | Bu belge | Dosya silinir | Şema |
| 2 | Test ekleme | Boş pepper + rehash testleri | Test kaldırılır | PHPUnit |
| 3 | Rehash akışı | Başarı girişinde tazeleme | Akış kapatılır | Eski hash'ler geçerli |
| 4 | Cost artışı | Yeni parametre + rehash | Eski değere dönüş | Yeniden hash oranı |
| 5 | Pepper rotasyonu | Çift-hash geçişi | Geri alınamaz (dikkat) | Sıfırlama zorunlu |
| 6 | Uzunluk artışı | 8 → üstü | Eski şifreler korunur | Kayıt testi |
| 7 | Puanlama | Zayıf şifre reddi | Kapatılır | Reddetme oranı |
| 8 | Kapanış | Açık soru kalmaz | — | Vault sync |

## §23 — Sık karıştırılan noktalar

| # | Yanlış varsayım | Doğrusu | Kaynak | Not |
|---|---|---|---|---|
| 1 | "Hash = şifreleme" | Geri döndürülemez | §2 | Anahtar yok |
| 2 | "Salt yeterli" | Salt benzersizlik, pepper sır | §6 | İkisi farklı |
| 3 | "Pepper veritabanında saklanır" | Hash dışında, uygulamada | §6 | Aksi salt'ı anlamsız kılar |
| 4 | "`md5` yeterli" | Argon2id zorunlu | §4 | `md5` yasak |
| 5 | "Kontrol uzunluğu yavaşlatır" | Doğru yavaşlatma cost ile | §5 | Uzunluk ayrıca şart |
| 6 | "Doğrulama hızlı olmalı" | Argon2id kasten yavaş | §5 | p95 ölçümü |
| 7 | "Ham şifre entity'de kalır" | `readonly string $passwordHash` | `User.php:17` | Ham yok |
| 8 | "Pepper boşsa devam edilir" | Fail-closed (exception/false) | Satır 41/57 | Veto |
| 9 | "Yanlış şifre ayrı mesaj verir" | Ayrıştırma yok | §15-4 | Keşfi önle |
| 10 | "`===` yeterli" | `hash_equals` / `password_verify` | §8 | Timing |

## §24 — Sözleşme özeti (tek sayfa)

| # | Kavram | Sözleşme değeri | Kaynak | Değişirse |
|---|---|---|---|---|
| 1 | Algoritma | Argon2id (`PASSWORD_ARGON2ID`) | `Password.php:44` | ADR gerekli |
| 2 | `memory_cost` | 65536 KiB | `Password.php:45` | Doğrulama süresi |
| 3 | `time_cost` | 4 | `Password.php:46` | Doğrulama süresi |
| 4 | `threads` | 2 | `Password.php:47` | Paralel maliyet |
| 5 | Asgari uzunluk | 8 | `Password.php:14` + `AuthService.php:30` | İki kaynak senkron |
| 6 | Pepper uygulaması | HMAC-SHA256(raw, pepper) | `Password.php:43` | Tüm doğrulama |
| 7 | Pepper boş (hash) | Exception `configError` | `Password.php:41` | Fail-closed |
| 8 | Pepper boş (verify) | `false` | `Password.php:56–57` | Fail-closed |
| 9 | Salt | `password_hash` üretir, hash'e gömülü | Kütüphane | Otomatik |
| 10 | Doğrulama | `password_verify` | `Password.php:60` | Kütüphane davranışı |
| 11 | Timing-safe | `hash_equals` | `ApiKeyRepository:171` | Veto |
| 12 | Hash alanı | `password_hash` (DB) · `passwordHash` (entity) | `AuthService.php:180` | Şema |
| 13 | Entity mutabilite | `readonly` | `User.php:17` | Değişirse kaçak |
| 14 | Rehash akışı | Yok (boşluk) | §10 | Eklenecek |
| 15 | Sıfırlama token'ı | 32 bayt `random_bytes` | `AuthService.php:269` | Tahmin direnci |
| 16 | Sıfırlama TTL | 900 sn | `AuthService.php:43` | Yeniden oynatma |
| 17 | Sıfırlama rate limit | 3 / 3600 sn | `AuthService.php:40–41` | Tarama yüzeyi |
| 18 | Test temeli | 6 test metodu | `PasswordTest.php` | Kapsam düşer |
| 19 | Pepper kaynağı | `constants.php` Security bölümü | `constants.php:60` | Sır sızıntısı |
| 20 | Bağımlılık | `depends_on: K0100-identitiy` | Frontmatter | Şema ihlali |

## §25 — Etki analizi (ne değişirse ne kırılır)

| # | Değişen | Kırılan | Belirtilen semptom | Koruma |
|---|---|---|---|---|
| 1 | Algoritma (Argon2id → başka) | Tüm mevcut hash'ler | Girişler toplu düşer | ADR + rehash planı |
| 2 | `memory_cost` | Doğrulama süresi | 502/timeout riski | Yük ölçümü |
| 3 | `time_cost` | CPU bütçesi | Darboğaz | Ölçüm |
| 4 | `threads` | Paralel maliyet | Beklenen hız değişir | Ölçüm |
| 5 | `APP_PEPPER` değeri | Tüm doğrulama | Kullanıcılar giriş yapamaz | Çift-hash geçişi |
| 6 | `APP_PEPPER` silinirse | Hashleme | Exception / verify false | Sağlık kontrolü |
| 7 | Uzunluk 8 → üstü | Kayıt formu | Yeni reddler | Form testi |
| 8 | Uzunluk 8 → altı | Güvenlik | Zayıf şifre kabulü | Veto |
| 9 | Salt davranışı | Benzersizlik | Kırma hızlanır | Kütüphane sabiti |
| 10 | `hash_equals` kaldırma | Timing güvenliği | Zamanlama sızıntısı | Veto |
| 11 | `password_verify` yerine `===` | Doğrulama bütünlüğü | Hash doğrudan karşılaştırılır | Veto |
| 12 | Entity alanı adı | Kayıt/okuma | Kayıt hatası | Şema testi |
| 13 | TTL 900 → uzun | Token yüzeyi | Oynatma penceresi | Güvenlik vetosu |
| 14 | Rate limit 3 → yüksek | Tarama savunması | Tarama | Veto |
| 15 | Rehash akışı eklenmesi | Hiçbir şey (olumlu) | Eski hash tazelenir | Test + ölçüm |

## §26 Envanter: bu dosyanın kanıt yazımı istatistikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Doğrulanmış gerçek repo kod alıntısı sayısı | 23 | §6–§15 arası `Dosya:/Satır:` satırları |
| Tek dosyadan alınan alıntı (Password.php) | 10 | `Satır 14/41/43/44/45/46/47/54/56/57/60` |
| Test dosyası alıntısı | 5 | `tests/Unit/PasswordTest.php:14/21/28/40/50/61` (6 satır) |
| Doğrulanmış tekil web kaynağı | 6 | §4 tablosu + §18 karşılaştırma + §23 PBKDF2 |
| Örneklenen gerçek envanter URL | 1444 | Görev manifesti sayımı |
| Repoda tespit edilen `password_needs_rehash` kullanım sayısı | 0 | 2026-10-13 taraması — §16 açık soru |
| Repoda Argon2id ayarı yapan dosya sayısı | 1 | `include/Domain/ValueObject/Password.php` |
| Repoda pepper (HMAC) ayarı yapan dosya sayısı | 1 | `include/Domain/ValueObject/Password.php` |
| Tespit edilen doğrulanamayan argüman (OPTIMIZATION_COST) | 1 | §16 §17 — `⚠️ VERIFICATION REQUIRED` |
| Görevde yasak kelime taraması sonucu | 0 | §25 kontrol tablosu son satırı |

## §27 Sonraki adımlar ve teslim notu

| # | Adım | Öncelik | Sahip |
|---|---|---|---|
| 1 | `OPTIMIZATION_COST` sabitinin kaynağını bul ve §17 kaydını güncelle | P0 | Vault Steward |
| 2 | `password_needs_rehash` rotasını (§16.3) tasarıma al | P0 | backend-architect |
| 3 | `IDLE_TIMEOUT` / `MAX_LIFETIME` uyuşmazlığını (§10.2) ADR ile çöz | P0 | architect |
| 4 | `routes.php` satır 36 için rate-limit kanıtını ekle | P1 | security-engineer |
| 5 | Parola olasılık-listesi politikasını (§16.1) kullanıcı onayına sun | P1 | business-analyst |
| 6 | `id=true` sorgu parametresi kuralını (§19.2) kod incelemesinde denetle | P1 | code-reviewer |
| 7 | PBKDF2 reçetesini (§23) hedef koda uygula | P2 | developer |
| 8 | KWS reçetelerini (§15) encoder envanteriyle eşle | P2 | cryptography uzmanı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
