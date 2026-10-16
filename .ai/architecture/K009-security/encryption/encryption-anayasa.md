---
node_id: K009-security/encryption/encryption-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/encryption
  - K009-security/secrets
evidence:
  - "shared/src/OAuth/OAuthManager.php:188-235 — AES-256-GCM encrypt/decrypt (okundu)"
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php:43-48, 59-60 — HMAC-SHA256 pepper + argon2id (okundu)"
  - "shared/src/Security/JwtService.php:24, 64, 90 — RS256 imza (okundu)"
  - ".gitignore:6, 69-71, 79-80 — .env / .pem commit dışı (grep)"
validation:
  - "2026-10-15 — 34 kanıt dosyası okundu; URL tablosu YOK (mimari grup, tahsis yok)"
  - "2026-10-15 — md5() taraması: 4 non-crypto isabet (§12), güvenlik amaçlı md5/SHA-1/DES 0"
open_questions:
  - "encryptionKey üretim/rotasyon yolu kodda okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "ChaCha20-Poly1305 repo çağrısı yok (sodium_* grep = 0 isabet) → ⚠️ VERIFICATION REQUIRED"
  - "Sunucu TLS sürüm/cipher yapılandırması depo dışı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · encryption · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Rol: Security Engineer

> **Rol:** Bu dosya K009-security/encryption çocuğunun **bağlayıcı kripto
> kurallarını** taşır. Sürüş akışı `encryption-surucu.md`'de, gerçek kod
> alıntıları `encryption-ornekler.md`'dedir, sınır `encryption-kapsam.md`'dedir.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/encryption/encryption-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | algoritma izin/yasak listesi · nonce/IV tek-kullanımlık · anahtar yaşı/rotasyon · taşıma vs at-rest · hash ≠ şifreleme |
| Kapsam dışı | sırın saklandığı yer (`secrets`) · oturum cookie'si (`session-security`) · parola doğrulama akımı (`authentication`) · sunucu TLS kurulumu (deploy katmanı) |

## §2 Terimler (Türkçe / English / Kod)

| Türkçe | English | Kod Karşılığı |
|---|---|---|
| AEAD | Authenticated Encryption with Associated Data | `openssl_encrypt(..., 'aes-256-gcm', ..., $tag, ...)` — `OAuthManager.php:193` |
| Doğrulama etiketi | Authentication tag | 16 byte `$tag` — `OAuthManager.php:201` |
| Tek-kullanımlık gizli sayı | Nonce / IV | `random_bytes(12)` — `OAuthManager.php:190` |
| Anahtar türetme | Key derivation (KDF/HKDF) | repo kanıtı **YOK** → ⚠️ VERIFICATION REQUIRED |
| Parola özeti | Password hashing | `password_hash(..., PASSWORD_ARGON2ID, ...)` — `Password.php:44` |
| Ek giz katmanı | Pepper (HMAC) | `hash_hmac('sha256', $raw, $pepper)` — `Password.php:43` |
| Zaman-güvenli karşılaştırma | Timing-safe compare | `hash_equals(...)` — `CsrfMiddleware.php:42` |
| Asimetrik imza | Asymmetric signature | `JWT::encode/decode` RS256 — `JwtService.php:64, 90` |
| Kanal güvenliği | Transport security | TLS — sunucu yapılandırması, depo dışı ⚠️ |

## §3 Algoritma İzin ve Yasak Listesi

| Sınıf | Algoritma | Durum | Dayanak |
|---|---|---|---|
| Simetrik AEAD | **AES-256-GCM** | **ZORUNLU / mevcut** | `OAuthManager.php:195` kanıtlanmış tek implement |
| Simetrik AEAD | **ChaCha20-Poly1305** | Kuralda izinli, repo'da **çağrı yok** | `sodium_*` taraması 0 isabet (2026-10-15) → ⚠️ VERIFICATION REQUIRED |
| Parola hash | **Argon2id** + HMAC-SHA-256 pepper | **ZORUNLU / mevcut** | `Password.php:43-48` |
| İmza (asimetrik) | RS256 (private.pem / public.pem) | **Mevcut** | `JwtService.php:24, 64, 90` |
| İmza (asimetrik) | ES256 | İzinli, repo'da yok | kural — implementasyon kanıtı YOK |
| İstek imzası | HMAC-SHA-256 (`hash_hmac`) | **Mevcut** | `Password.php:43` · `auth index.php:132` |
| Özet (crypto dışı) | SHA-256+ | izinli | `ApiKeyRepository.php:171` (sha256 raw hash eşleşmesi) |
| Özet | **MD5 / SHA-1** (güvenlik amaçlı) | **YASAK** | `.ai/CLAUDE.md` §Security; repo'da güvenlik amaçlı 0 isabet |
| Blok şifre | **DES / 3DES / RC4 / Blowfish** | **YASAK** | K009 kuralı; repo'da 0 isabet |
| Mod | **ECB** | **YASAK** | desen sızıntısı; repo'da 0 isabet |
| Özet (eski) | CRC32 güvenlik yerine | **YASAK** | bütünlük için AEAD tag yeterli (§6) |

**Doğrulama notu (2026-10-15 taraması):** `md5(` repo genelinde bulunur —
yalnız **güvenlik dışı** sınıf (§12). `DES`/`sha1(` taraması güvenlik amaçlı
0 isabet. Uydurma temizlik iddiası yazılmaz: md5 varlığı §12'de sayımıyla
kaydedilmiştir.

## §4 Üç Güvenlik Yüzeyi (Yüzey Ayrımı Zorunlu)

| Yüzey | Soru | Mekanizma | Repo Kanıtı | Geri döndürülebilir mi? |
|---|---|---|---|---|
| Taşıma (in-transit) | Kanal dinlenemez mi? | TLS 1.2/1.3 | sunucu yapılandırması — **depoda YOK** → ⚠️ VERIFICATION REQUIRED; HSTS başlığı kapsam dosyasında tartışmalı (`.htaccess` iddiası doğrulanmadı) | evet (anahtar/sertifika sunucuda) |
| Bekleme (at-rest) | Depodaki veri okunamaz mı? | AES-256-GCM | `OAuthManager.php:193-202` (şifreli access token) | **evet** (anahtar gerekli) |
| Parola | DB sızınca kaba kuvvet pahalı mı? | argon2id + pepper | `Password.php:44-48` | **HAYIR** (hash, tek yönlü) |

**Ayrım kuralı (madde A-09):** üç yüzey birbirine **devredilemez**. Taşıma
güvenliği at-rest'i, at-rest parola özetini, parola özeti taşımayı **kurtaramaz**.

## §5 Hash ≠ Şifreleme (Karışım Yasağı)

| Özellik | Şifreleme (AES-256-GCM) | Hash (argon2id) |
|---|---|---|
| Amaç | gizlilik | kimlik doğrulama |
| Tersi | `decrypt` ile mümkün | imkânsız |
| Anahtar/ek | `encryptionKey` | `pepper` + salt (hash içinde) |
| Örnek | OAuth access token — `OAuthManager.php:188` | parola — `Password.php:44` |
| Doğrulama | GCM tag'i ile | yeniden hesapla + `password_verify` — `Password.php:60` |
| Anahtar sızarsa | tüm veri açılır | parolalar yine kapalı (argon2id pahalı) |

**Yasak senaryo:** parolayı AES ile şifreleyip saklamak — anahtar sızınca
TÜM parolalar açılır. Kanıt tarafı: `Password.php` dosyasında openssl
çağrısı **yoktur** (dosya tamamı okundu, 67 satır — 0 openssl isabeti).

## §6 Nonce / IV Tek-Kullanımlık Kuralları

| # | Kural | Repo Kanıtı | Yaptırım |
|---|---|---|---|
| N-01 | GCM IV **12 byte** (96 bit) üretilir | `OAuthManager.php:190` — `random_bytes(12)` | RED |
| N-02 | IV **her çağrıda yeniden** üretilir; sabitlenmez | `OAuthManager.php:190` (fonksiyon içinde) | RED |
| N-03 | Aynı (anahtar, IV) çifti **asla tekrarlanmaz** — GCM'de güven çöküşü | kural (kanıt: her çağrı yeni IV) | BLOCK |
| N-04 | IV **sayısal sayaçla** (artırılarak) üretilmez | repo'da sayaç yok — 0 isabet | RED |
| N-05 | GCM'de `tag` **zorunlu**; tag'siz şifreleme yapılmaz | `OAuthManager.php:191, 199-201` — `$tag` referansla doldurulur | RED |
| N-06 | AAD (associated data) gerekiyorsa `''` yerine bağlayıcı veri verilir | mevcut çağrıda AAD `''` — `OAuthManager.php:200` | kural (iş ihtiyacı belirsiz ⚠️) |
| N-07 | ChaCha20-Poly1305 seçilirse nonce 12 byte / 96 bit + tek kullanım | repo'da implement yok → ⚠️ VERIFICATION REQUIRED | kural |

**Neden 12 byte:** GCM'ın önerilen varsayılanı; 12 byte dışında IV uzunluğu
GCM'ı GHASH yolundan hesaplatır (performans/güvenlik tuzağı). Kanıt sınırı:
bu gerekçe standart kaynaklıdır, repo satırı **yalnızca 12 byte üretimini**
gösterir (`OAuthManager.php:190`).

## §7 Anahtar Yaşı ve Rotasyonu

| Aşama | İlke | Repo Durumu |
|---|---|---|
| Üretim | Rastgelelik (`random_bytes`) veya KDF; tahmin edilemez | `encryptionKey` **nereden geldiği kodda okunmadı** → ⚠️ VERIFICATION REQUIRED |
| Saklama | Yalnız `.env` / secret deposu; koda, `.md`'ye, `.log`'a asla | `.gitignore:6` (`.env`), `:80` (`shared/config/jwt/private.pem`) |
| Kullanım | Yalnız şifreleme fonksiyonunun içinde; kopya üretilmez | `OAuthManager.php:196` — constructor DI (`$this->encryptionKey`) |
| Yaş sınırı | Üretim için maksimum ömür tanımlanmalı | repo'da **tanım yok** → ⚠️ VERIFICATION REQUIRED |
| Rotasyon | Periyodik yenileme + eski anahtarla okuma penceresi | ADR **yok** → ⚠️ VERIFICATION REQUIRED |
| Emeklilik (revokasyon) | Eski anahtar silinir/devre dışı bırakılır | ADR **yok** → ⚠️ VERIFICATION REQUIRED |

**Anahtar envanteri (kod okuması):**

| Anahtar | Kullanım | Kanıt | Saklama |
|---|---|---|---|
| `encryptionKey` | AES-256-GCM | `OAuthManager.php:196` | DI — kaynak ⚠️ |
| `APP_PEPPER` | HMAC-SHA-256 pepper | `Password.php:43, 59` (parametre) · fallback `auth index.php:132` | env (fallback kodda — §13 Z-1) |
| `private.pem` (RS256) | JWT imzası | `JwtService.php:64, 111-124` | dosya — `.gitignore:80` |
| `public.pem` (RS256) | JWT doğrulama | `JwtService.php:90, 126-136` | dosya — commit edilir (yorum: `.env.example:99-102`) |

## §8 Bağlayıcı Kurallar (Anayasa Maddeleri)

| Madde | Kural | Yaptırım |
|---|---|---|
| A-01 | Veri şifrelemede **yalnız AEAD** kullanılır: AES-256-GCM veya ChaCha20-Poly1305 | Kod incelemesinde RED |
| A-02 | Parola **asla** şifrelenmez; yalnız argon2id + pepper ile hash'lenir | RED |
| A-03 | MD5 / SHA-1 / DES / 3DES / RC4 / ECB **güvenlik amaçlı** kullanılmaz | RED |
| A-04 | IV/nonce her üretimde yeniden üretilir; tekrar yasak (N-02/N-03) | RED |
| A-05 | GCM çıktısında auth tag **taşınır ve doğrulanır**; tag'siz veri işlenmez | BLOCK |
| A-06 | Çözme başarısızsa **fail-closed**: exception → veri işlenmez, varsayılan yok | BLOCK |
| A-07 | Anahtar değeri `.md` / `.json` / `.log` / kaynak kod içine yazılmaz | RED |
| A-08 | Zaman-güvenli karşılaştırma (`hash_equals`) dışındaki eşitleme kriptografik doğrulamada kullanılmaz | RED |
| A-09 | Yüzeyler karıştırılmaz: taşıma ≠ at-rest ≠ hash (§4) | BLOCK |
| A-10 | Anahtar rotasyonu tanımlı değilse sistem **rotasyonsuz yayına alınmaz** (ADR zorunlu) | BLOCK — açık ⚠️ |
| A-11 | Algoritma/uzunluk değişikliği ADR gerektirir (parola parametreleri dâhil) | RED |
| A-12 | `openssl_*` / `sodium_*` çağrısı yalnız merkezî güvenlik katmanında yapılır (dağıtık şifreleme yasak) | BLOCK |

## §9 Yasaklar (Forbidden)

| # | Yasak | Sınıf | Repo Durumu |
|---|---|---|---|
| Y-01 | ECB modu | sızıntı | 0 isabet ✅ |
| Y-02 | Sabit/tekrarlı IV | kripto çöküşü | 0 isabet ✅ |
| Y-03 | Parolayı şifreleyip saklamak | anahtar riski | 0 isabet ✅ (`Password.php` 67 satır, openssl yok) |
| Y-04 | `md5()`/`sha1()` ile güvenlik kararı (imza/token/doğrulama) | çarpışma | 0 isabet ✅ (§12 — md5 yalnız cache/ETag) |
| Y-05 | Anahtarın log'a, hataya, izlene dökümüne yazılması | sızıntı | kanıt: `JwtService.php:92` hata mesajında ayrıntı **yok** ("hiçbir ayrıntı sızdırılmaz") |
| Y-06 | `hash()` çıktısını düz `===` ile karşılaştırmak | timing | kanıt: karşılaştırma noktaları `hash_equals` (`CsrfMiddleware.php:42`, `ApiKeyRepository.php:171`) |
| Y-07 | Kendi kripto implementasyonu yazmak (AES/RSA elde) | hata | kanıt: `JwtService.php:13` "şifreleme implement edilmez — kütüphane sınırı" |
| Y-08 | Kriptografik nonce'ü `rand()`/`mt_rand()` ile üretmek | öngörülebilirlik | 0 isabet; üretim `random_bytes` (`OAuthManager.php:190`, `SecurityHeadersMiddleware.php:18`) |
| Y-09 | Parola hash'inde md5/sha1/PASSWORD_DEFAULT (gizli argon2) | zayıf özet | 0 isabet — `PASSWORD_ARGON2ID` (`Password.php:44`) |
| Y-10 | At-rest şifrelemede CBC/CTR (tagsiz) kullanmak | bütünlüksüz | repo'da yalnız GCM (§11) |

## §10 Doğrulama Sorusu Seti (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Yeni şifreleme eklendi mi? | A-01: AEAD mı? | RED |
| 2 | IV/nonce sabit mi veya sayaç mı? | A-04 ihlali → BLOCK | devam |
| 3 | GCM tag'i üretilip doğrulanıyor mu? | A-05 ✓ | BLOCK |
| 4 | Çözme hatası yutuluyor mu (boş string/varsayılan)? | A-06 ihlali → BLOCK | devam |
| 5 | Parola yolu mı? | hash (argon2id+pepper), asla encrypt | — |
| 6 | Güvenlik kararında md5/sha1 var mı? | Y-04 → RED | — |
| 7 | Anahtar literal kodda mı? | A-07 ihlali → RED | — |
| 8 | Karşılaştırma `===` mi (kriptografik değer)? | Y-06 → RED | — |
| 9 | Algoritma uzunluk/parametre değişti mi? | A-11: ADR gerekli | — |
| 10 | Yeni ChaCha/ES256 yolu mı? | §6-N07 + ADR | — |

## §11 Repo Karşılığı — Kod Kanıtları (Dosya · Satır)

| # | Dosya : Satır | Ne gösteriyor |
|---|---|---|
| 1 | `shared/src/OAuth/OAuthManager.php:188` | `encrypt()` imzası |
| 2 | `shared/src/OAuth/OAuthManager.php:190` | IV = `random_bytes(12)` (tek kullanım) |
| 3 | `shared/src/OAuth/OAuthManager.php:193-202` | `openssl_encrypt` + `aes-256-gcm` + tag 16 |
| 4 | `shared/src/OAuth/OAuthManager.php:204` | sarma: `base64(iv . tag . ciphertext)` |
| 5 | `shared/src/OAuth/OAuthManager.php:210` | `decrypt()` imzası |
| 6 | `shared/src/OAuth/OAuthManager.php:213` | min uzunluk 29 denetimi |
| 7 | `shared/src/OAuth/OAuthManager.php:217-219` | IV/tag/ciphertext ayrıştırma |
| 8 | `shared/src/OAuth/OAuthManager.php:221-228` | `openssl_decrypt` + tag |
| 9 | `shared/src/OAuth/OAuthManager.php:230-232` | başarısız çözme → exception (fail-closed) |
| 10 | `auth.coremusic.net/include/Domain/ValueObject/Password.php:43` | `hash_hmac('sha256', ..., $pepper)` |
| 11 | `auth.coremusic.net/include/Domain/ValueObject/Password.php:44-48` | `password_hash` + `PASSWORD_ARGON2ID` (65536/4/2) |
| 12 | `auth.coremusic.net/include/Domain/ValueObject/Password.php:59-60` | verify yolu: aynı HMAC + `password_verify` |
| 13 | `shared/src/Security/JwtService.php:24` | `ALGORITHM = 'RS256'` |
| 14 | `shared/src/Security/JwtService.php:51` | `jti = bin2hex(random_bytes(16))` |
| 15 | `shared/src/Security/JwtService.php:64` | `JWT::encode(..., RS256)` |
| 16 | `shared/src/Security/JwtService.php:90` | `JWT::decode` + `Key(publicKey, RS256)` |
| 17 | `shared/src/Security/JwtService.php:96-104` | iss/aud/sub/jti elle kontrol (fail-closed) |
| 18 | `shared/src/Middleware/SecurityHeadersMiddleware.php:18` | CSP nonce = `bin2hex(random_bytes(32))` |
| 19 | `shared/src/Middleware/CsrfMiddleware.php:42` | `hash_equals` timing-safe CSRF karşılaştırması |
| 20 | `shared/src/Repository/ApiKeyRepository.php:171` | `hash_equals($stored, $rawHash)` (sha256) |
| 21 | `auth.coremusic.net/index.php:132` | bypass anahtarı HMAC — **fallback sabit kodda (Z-1)** |
| 22 | `.gitignore:6` | `.env` commit dışı |
| 23 | `.gitignore:80` | `shared/config/jwt/private.pem` commit dışı |
| 24 | `shared/config/.env.example:41` | `APP_PEPPER=` (boş şablon, gerçek değer yok) |

## §12 md5() Kullanım Sınıflandırması (dürüst sayım — 2026-10-15 taraması)

| Dosya : Satır | Kullanım | Sınıf | Karar |
|---|---|---|---|
| `shared/src/Cache/PageCacheAdapter.php:31` | cache anahtarı `md5($uri)` | non-crypto | izinli (Y-04 kapsamı dışı) |
| `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php:70` | ETag üretimi | non-crypto | izinli |
| `shared/tests/Api/ResponseNormalizationMiddlewareTest.php:47, 66, 79` | ETag testi | non-crypto (test) | izinli |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php:80` | dosya ETag'i | non-crypto | izinli |
| `shared/src/Middleware/RateLimiterMiddleware.php:72` | `md5($ip)` cache key | non-crypto | izinli — **not:** güvenlik *sonucu* üretmez, yalnız anahtar |

**Sonuç:** güvenlik amaçlı md5/SHA-1/DES **0 isabet**; yukarıdaki 4 üretim
kodu non-crypto sınıftadır. `RateLimiterMiddleware.php:72` için sınır notu:
bu md5 bir anahtarlama işlemidir, doğrulama/değer üretmez; yine de yeni
kodda `hash('sha256', ...)` tercih edilir (kural, gelecek revizyon).

## §13 Uyum Matrisi (Kural ↔ Repo)

| Kural | Kaynak | Uyum |
|---|---|---|
| AES-256-GCM / ChaCha20-Poly1305 zorunlu | `.ai/CLAUDE.md` §Security (Global AGENTS §5 Security) | ✅ GCM kanıtlı · ChaCha ⚠️ yok |
| MD5/SHA-1/DES yasak | `.ai/CLAUDE.md` §Security | ✅ güvenlik amaçlı 0 (§12 ile birlikte) |
| Sır dosyaya yazılmaz | `.ai/AGENTS.md` Zero-Hallucination + Guardrail | ⚠️ Z-1: `auth index.php:132` fallback `'coremusic-bypass'` sabiti kodda |
| Zaman-güvenli karşılaştırma | OWASP pratik (kardeş `encryption-kapsam` §3) | ✅ `hash_equals` 3 noktada |
| Kütüphane sınırı (kendi kripto yok) | `JwtService.php:8-21` doküman-notu | ✅ firebase/php-jwt |
| Rotasyon/yaş politikası | anayasa §7 | ❌ ADR yok → ⚠️ |

## §14 Bilinmeyenler Defteri (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | `encryptionKey` üretim yolu (rastgele mi KDF mi) | ⚠️ VERIFICATION REQUIRED |
| U-2 | Anahtar rotasyon periyodu ve prosedürü | ⚠️ VERIFICATION REQUIRED |
| U-3 | ChaCha20-Poly1305 / sodium kullanımı için PHP eklenti envanteri | ⚠️ VERIFICATION REQUIRED |
| U-4 | Sunucu TLS sürümü (1.2 altı kapalı mı) + cipher suite | ⚠️ VERIFICATION REQUIRED |
| U-5 | HSTS başlığı gerçek sunucuda yayında mı | ⚠️ VERIFICATION REQUIRED |
| U-6 | `private.pem` rotasyonu (RS256) — jti-üretimli token'lar ne olur | ⚠️ VERIFICATION REQUIRED |
| U-7 | GCM AAD kullanımı gereksinimi (şu an `''`) | UNKNOWN |
| U-8 | Parola pepper'ının saklama yeri/erişim izinleri | UNKNOWN |
| U-9 | at-rest şifreleme kapsamı: yalnız OAuth token mı, başka veri var mı | UNKNOWN |

## §15 Test Gereksinimleri

| # | Test | Kriter |
|---|---|---|
| T-1 | Aynı plaintext iki kez şifrelenir | çıktılar farklı (IV tekrar yok) |
| T-2 | Tag bozulmuş ciphertext çözülür | exception — fail-closed (`OAuthManager.php:230`) |
| T-3 | 28 byte'lık kısa veri çözülür | "Invalid encrypted token" (`:213`) |
| T-4 | Parola hash'i tersine çevrilme denemesi | imkânsız; openssl çağrısı yok |
| T-5 | Yanlış pepper ile verify | false (`Password.php:56-58` — pepper boşsa false) |
| T-6 | Süresi dolmuş JWT | decode exception → null (`JwtService.php:91-94`) |
| T-7 | Yanlış iss/aud JWT | null (`:96-101`) |
| T-8 | md5/DES/SHA-1 güvenlik kullanımı taraması | 0 isabet (§12) |
| T-9 | Koddaki sabit sır taraması (Z-1 sınıfı) | ihlal listesi → ADR |

## §16 Karar Ağacı (Decision Tree)

```text
Yeni veri koruma ihtiyacı var mı?
  ├─ Parola/ölçülebilir gizli (bir kez doğrulanacak) → HASH
  │     └─ argon2id + HMAC pepper (Password.php:43-48)
  ├─ Geri okunacak veri (token, PII, anahtar) → AEAD
  │     ├─ varsayılan → AES-256-GCM (OAuthManager:193)
  │     └─ ChaCha20-Poly1305 → ADR + sodium envanteri (U-3)
  ├─ Kimlik/karar kanıtı → İMZA (RS256 — JwtService:64)
  └─ Kanal güvensiz mi → TLS (U-4, deploy katmanı)
Her üretimde: yeni IV/nonce (§6) · her okumada: tag doğrula (§6)
Değişiklik ADR gerektirir mi? → algoritma/uzunluk/parametre/rotasyon: EVET (A-11/A-10)
```

## §17 OpenSSL Çağrı Sözleşmesi ve Hata Yolları (2026-10-15 doğrudan okuma)

### §17.1 `encrypt()` — çağrı sırası (`OAuthManager.php:188-205`)

| Adım | Satır | Çağrı / İşlem |
|---|---|---|
| 1 | :190 | `$iv = random_bytes(12)` — tek-kullanımlık nonce (N-01) |
| 2 | :191 | `$tag = ''` — referansla doldurulacak |
| 3 | :193-202 | `openssl_encrypt($plaintext, 'aes-256-gcm', $this->encryptionKey, OPENSSL_RAW_DATA, $iv, $tag, '', 16)` — AAD `''`, tag 16 byte |
| 4 | :204 | `base64_encode($iv . $tag . $ciphertext)` — sarma (iv-first) |

**⚠️ H-03 (dürüst bulgu — VERIFICATION REQUIRED):** adım 3'ün **dönüş değeri
kontrol edilmiyor** (:202 → :204 arasında `false` denetimi yok). `openssl_encrypt`
başarısız olursa `false` → `$ciphertext` olarak kullanılır ve **sessizce** bozuk
sarma üretilir (`base64(iv.tag.'')`). Yazım yolu fail-open; okuma yolu ise
fail-closed'dur (`decrypt :230-232`) → veri kaybı gecikmeli fark edilir.
Doğrulama: `encryption-dogrulama` testine yazıldı.

### §17.2 `decrypt()` — giriş denetimleri (`OAuthManager.php:210-235`)

| Adım | Satır | Denetim | Hata |
|---|---|---|---|
| 1 | :212 | `base64_decode(..., true)` strict | `false` → exception |
| 2 | :213 | `strlen($decoded) < 29` | `RuntimeException('Invalid encrypted token')` :214 |
| 3 | :217-219 | `iv=0..12` · `tag=12..28` · `ct=28..` ayrıştırma | — |
| 4 | :221-228 | `openssl_decrypt($ct, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag)` | — |
| 5 | :230-232 | `$plaintext === false` → `RuntimeException('Decryption failed')` | **fail-closed** (A-06) |

**Dedüksiyon (D-9):** eşik **29 = 12 (iv) + 16 (tag) + 1 (en az 1 bayt ciphertext)** —
GCM'de tag'siz/iv'siz çözme mümkün olmadığından bu minimum mantıklı sınır.

### §17.3 `Password` — pepper sözleşmesi asimetrisi (`Password.php:38-61`)

| Yol | Pepper boşsa | Davranış | Sınıf |
|---|---|---|---|
| `hashWithPepper` :38-49 | `ServerException::configError('APP_PEPPER')` | **throw** :40-42 | fail-closed (üretim) |
| `verify` :54-61 | `return false` | **false** :56-58 | fail-closed (doğrulama → reddet) |

**Gerekçe:** üretim yolu sistem yapılandırması gerektirir (yoksa dur); doğrulama
yolu kullanıcının isteğidir (yanlış pepper = geçersiz kimlik → reddet, çökme değil).
Her iki yolda da ham şifre asla saklanmaz (:9 doküman-notu); argon2id parametreleri
`:44-48` sabit (65536/4/2 — A-11: değişiklik ADR ister).

## §18 İlişkili Düğümler

| Düğüm | İlişki |
|---|---|
| `K009-security/encryption` (parent-child index) | bu anayasanın künyesi |
| `K009-security/secrets` | anahtar/gizli kaynağı — §7 devri |
| `K009-security/session-security` | oturum jetonu nonce'ları, cookie güvenliği |
| `K009-security/authentication` | parola hash doğrulama, JWT |
| `K009-security` (parent) | domen anayasasına tabi |

## §19 BAĞLANTILAR (Wiki — yalnız diskte var olan adlar)

| Bağlantı | Not |
|---|---|
| [[encryption-kapsam]] | üç yüzey + AES bloğu sınırı |
| [[encryption-ornekler]] | birebir PHP alıntıları |
| [[encryption-dogrulama]] | iddia/ kontrol tabloları |
| [[encryption-bilesenler]] | kademe-3 bileşen planı |
| [[encryption-web-arastirma]] | FIPS/RFC kayıtları (URL tablosu orada) |
| [[secrets-kapsam]] | sır envanteri, Z-1 zayıf noktası |
| [[secrets-web-arastirma]] | OWASP/NIST sır kayıtları |
| [[session-security-kapsam]] | cookie bayrakları, timeout |
| [[security-master]] | K009 kök mimari |
| [[security-kod-ornekleri]] | kök kod kanıtları |

## §20 Revizyon ve Sürüm

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 24 kanıt satırı, md5 sınıf-sayımı (§12), 9 açık soru | Security Engineer (tek yazar) |
| 2026-10-15 | §17 OpenSSL sözleşme + H-03 (encrypt false kontrolsüz) + D-9 (29 bayt eşiği) eklendi; §17→§20 yeniden numaralandı | Security Engineer (tek yazar) |

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Durum | draft (U-1..U-9 açık) |
| Otorite | Bayram Ali / Vault Steward |
| Erişim | `.ai/architecture/K009-security/encryption/encryption-anayasa.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
