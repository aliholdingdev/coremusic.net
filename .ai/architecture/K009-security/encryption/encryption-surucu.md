---
node_id: K009-security/encryption/encryption-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/encryption/encryption-anayasa
evidence:
  - "shared/src/OAuth/OAuthManager.php:188-235 — şifreleme/çözme sürüş gövdeleri (okundu)"
  - "shared/src/Security/JwtService.php:44-109 — issue/validate sürüşü (okundu)"
  - "shared/src/PageRouter/PageRouterKernel.php:330-341 — middleware sırası (okundu)"
validation:
  - "2026-10-15 — sürüş akışları §3-§10; openssl parametre sırası satır aralıklarıyla doğrulandı"
  - "2026-10-15 — sodium_* grep = 0 isabet (ChaCha20 sürüşü yok)"
open_questions:
  - "encryptionKey üretim akışı (anahtar üretimi adımı) → ⚠️ VERIFICATION REQUIRED"
  - "Rotasyon akışı (eski anahtarla okuma penceresi) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · encryption · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Rol: Security Engineer

> **Rol:** şifreleme ve çözmenin **nasıl sürdüğü** — anahtar üretimi →
> şifreleme → nonce → doğrulama → çözme akışları, hata kolları (fail-closed),
> PHP `openssl_*` çağrı sırası. Kurallar `encryption-anayasa.md`'de, birebir
> kod `encryption-ornekler.md`'dedir.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/encryption/encryption-surucu |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | sürüş akışları · hata kolları · openssl/sodium çağrı sırası · doğrulama zinciri · pseudocode |

## §2 Sürüş Sözlüğü

| Türkçe | English | Açıklama |
|---|---|---|
| Sürüş | Drive / execution path | Bir işlemin adım adım yürütülmesi |
| Hata kolu | Error branch | false/exception yolunun sürülmesi |
| Fail-closed | Fail-closed | Belirsizlikte reddet, varsayılan üretme |
| Etiket doğrulama | Tag verification | GCM auth tag kontrolü |
| Sarma | Envelope | iv ‖ tag ‖ ciphertext birleşimi |
| Çağrı sırası | Call order | openssl parametre dizilimi |

## §3 Anahtar Üretimi Akışı (bilinen/ bilinmeyen ayrımı)

| Adım | Beklenen Eylem | Repo Durumu |
|---|---|---|
| K-1 | Rastgelelik kaynağı seç (`random_bytes` / KDF) | ⚠️ VERIFICATION REQUIRED — `encryptionKey` üretimi kodda okunmadı |
| K-2 | Uzunluk: AES-256 için 32 byte | ⚠️ VERIFICATION REQUIRED |
| K-3 | Saklama: `.env` / secret deposu | `.gitignore:6` (`.env`) — şablon `shared/config/.env.example:41` (APP_PEPPER örneği) |
| K-4 | Hizmete enjeksiyon (constructor DI) | ✅ kanıtlı — `$this->encryptionKey` (`OAuthManager.php:196`) |
| K-5 | Yaş/rotasyon | ⚠️ VERIFICATION REQUIRED (ADR yok) |
| K-6 | Emeklilik (revokasyon) | ⚠️ VERIFICATION REQUIRED |

> Bu akışın 6 adımından yalnız **K-3 ve K-4** disk kanıtlıdır. Geri kalanı
> anayasa §7 / U-1..U-2 işaretini taşır — uydurulmaz.

## §4 Şifreleme Sürüşü (Happy Path — OAuthManager · 188-205)

| Adım | Eylem | Dosya : Satır |
|---|---|---|
| E-1 | `encrypt(string $plaintext)` çağrılır | `OAuthManager.php:188` |
| E-2 | IV üretilir: `$iv = random_bytes(12)` | `:190` |
| E-3 | `$tag = ''` referans hazırlanır | `:191` |
| E-4 | `openssl_encrypt(...)` — GCM, RAW_DATA, tag 16 | `:193-202` |
| E-5 | Dönüş `false` kontrolü (PHP el kitabı: hata `false` döner) | ⚠️ **repo'da `false` kontrolü YOK** → §8 H-03 |
| E-6 | Sarma: `base64_encode($iv . $tag . $ciphertext)` | `:204` |
| E-7 | Sarmalı çağıran katmana döner (DB'ye yazan) | çağıran katman |

| Durum | Sonraki |
|---|---|
| IV üretildi | → E-4 |
| E-4 false dönerse | §8 H-03 (kontrol eksikliği — bulgu) |
| E-4 true | → E-6 → döner |

## §5 Nonce (IV) Üretim Sürüşü

| Kural | Sürüş | Kanıt |
|---|---|---|
| Yeni üretim | her `encrypt` çağrısında `random_bytes(12)` | `OAuthManager.php:190` |
| Paylaşım | IV **yalnız** bu çağrıya özeldir, dışarı çıkartılmaz | yerel değişken |
| Alım | çözmede sarmadan `substr(..., 0, 12)` ile geri okunur | `:217` |
| Tekrar | aynı IV iki kez asla üretilmez (paralel çağrılar dâhil) | kural — anayasa N-03 |
| ChaCha20 yolu | repo sürüşü **yok** (`sodium_*` grep = 0) | ⚠️ VERIFICATION REQUIRED |

## §6 Doğrulama (GCM Auth Tag) Akışı

```text
ŞİFRELEME YOLU:
  iv   = random_bytes(12)                (OAuthManager:190)
  tag  = openssl_encrypt(..., iv, tag, '', 16)   (:193-202)
  out  = base64(iv ‖ tag ‖ ciphertext)   (:204)

ÇÖZME YOLU:
  raw  = base64_decode(out, strict)      (:212)
  raw=false VEYA |raw| < 29 → throw      (:213-215)  ← fail-closed
  iv   = raw[0..12)   tag = raw[12..28)  (:217-218)
  ct   = raw[28..]                        (:219)
  pt   = openssl_decrypt(ct, aes-256-gcm, key, RAW, iv, tag)  (:221-228)
  pt === false → throw RuntimeException  (:230-232) ← TAG MISMATCH FAIL-CLOSED
  return pt                              (:234)
```

| Doğrulama Katmanı | Mekanizma | Dosya : Satır |
|---|---|---|
| 1 — yapı | `base64_decode` strict + min 29 byte | `OAuthManager.php:212-215` |
| 2 — bütünlük | GCM auth tag (16 byte) `openssl_decrypt` içinde | `:221-228` |
| 3 — hata | `false` → exception, veri **asla** işlenmez | `:230-232` |

**29 sayısının anlamı:** 12 (IV) + 16 (tag) + 1 (en az 1 byte ciphertext) —
bozuk/kısa veri daha ayrıştırmada reddedilir.

## §7 Çözme Sürüşü (Failure-First Tablo)

| Tetikleyici | Sürüş | Sonuç |
|---|---|---|
| `base64_decode` false | throw `Invalid encrypted token` | fail-closed (`:213-215`) |
| Uzunluk < 29 | aynı throw | fail-closed |
| Tag eşleşmiyor | `openssl_decrypt` false → throw `Decryption failed` | fail-closed (`:230-232`) |
| Anahtar yanlış/eksik | çözme false → aynı throw | fail-closed |
| `decrypt('')` | decode `''` → uzunluk 0 → throw | fail-closed |

**Yasak sürüş (RED):** hata yolunda `return ''` / `return $encrypted` /
`?? null` gibi **varsayılan döndürmek** — anayasa A-06. Repo sürüşü bu kurala
uygundur (yakalama noktası tek: `:230`).

## §8 Hata Kolları (Error Branches)

| # | Kolu | Durum | Etiket |
|---|---|---|---|
| H-01 | Çözme minimum uzunluk reddi | ✅ uygulanmış | `OAuthManager.php:213` |
| H-02 | Tag mismatch → exception | ✅ uygulanmış | `:230-232` |
| H-03 | **Şifreleme** sonucu `false` kontrolü | ❌ **repo'da yok** — `openssl_encrypt` dönüşü doğrudan `base64_encode`'a giriyor | ⚠️ VERIFICATION REQUIRED (olgu: `:193-204` arası `false` testi yok) |
| H-04 | Anahtar okunamazsa (JWT) | ✅ exception — yol + üretim komutu mesajında | `JwtService.php:114-120` |
| H-05 | JWT decode hata sızıntısı | ✅ `catch (\Throwable) → null`, ayrıntı yok | `JwtService.php:91-94` |
| H-06 | Pepper boşsa hash | ✅ exception (`configError`) | `Password.php:40-42` |
| H-07 | Pepper boşsa verify | ✅ `false` (fail-closed) | `Password.php:56-58` |

## §9 PHP `openssl_encrypt` Çağrı Sırası (parametre haritası)

| # | Parametre | Repo Değeri | Satır | Not |
|---|---|---|---|---|
| 1 | `$data` (plaintext) | `$plaintext` | `:194` | |
| 2 | `$cipher_algo` | `'aes-256-gcm'` | `:195` | küçük harf GCM |
| 3 | `$passphrase` (anahtar) | `$this->encryptionKey` | `:196` | 32 byte beklenir (U-2 ⚠️) |
| 4 | `$options` | `OPENSSL_RAW_DATA` | `:197` | base64 **sonra** (`:204`) |
| 5 | `$iv` | `random_bytes(12)` | `:198` ← `:190` | 96 bit |
| 6 | `$tag` (ref) | `''` → doldurulur | `:199` | referans dönüşü |
| 7 | `$aad` | `''` | `:200` | AAD yok (U-7) |
| 8 | `$tag_length` | `16` | `:201` | 128 bit tag |

| # | Parametre (`openssl_decrypt`) | Repo Değeri | Satır |
|---|---|---|---|
| 1 | `$data` | `$ciphertext` | `:222` |
| 2 | `$cipher_algo` | `'aes-256-gcm'` | `:223` |
| 3 | `$passphrase` | `$this->encryptionKey` | `:224` |
| 4 | `$options` | `OPENSSL_RAW_DATA` | `:225` |
| 5 | `$iv` | `substr($decoded, 0, 12)` | `:226` ← `:217` |
| 6 | `$tag` | `substr($decoded, 12, 16)` | `:227` ← `:218` |
| 7 | `$aad` | (yok — GCM'de tag yeterli) | — |

**Sıra kuralı (madde):** `OPENSSL_RAW_DATA` kullanıldığında sarma/base64
**sonradan** yapılır; `openssl_*` içine base64 vermek yasaktır (çift sarma
bozuk ayrıştırma üretir). Repo sürüşü doğru sıradadır (`:197` + `:204`).

## §10 libsodium Sürüşü (ChaCha20-Poly1305 — YOK)

| İddia | Kanıt | Durum |
|---|---|---|
| `sodium_*` çağrısı repo'da var | grep = **0 isabet** (2026-10-15, `*.php`) | HAYIR |
| ChaCha20-Poly1305 üretiliyor | — | ⚠️ VERIFICATION REQUIRED (kuralda izinli, sürüş yok) |
| PHP eklenti envanteri (`ext-sodium` kurulu mu) | composer/ortam okunmadı | ⚠️ VERIFICATION REQUIRED |

**Sürüş sonucu:** AES-256-GCM tek aktif AEAD yoludur (`OAuthManager.php:193`).
ChaCha20-Poly1305 sürüşü **yazılmadan önce** ADR + ortam doğrulaması gerekir.

## §11 Parola Hash Sürüşü (Password.php — 38-61)

| Adım | Eylem | Satır |
|---|---|---|
| P-1 | Uzunluk ≥ 8 kontrolü | `:28-30` |
| P-2 | pepper boş mu → exception | `:40-42` |
| P-3 | `hash_hmac('sha256', raw, pepper)` | `:43` |
| P-4 | `password_hash(peppered, PASSWORD_ARGON2ID, [65536/4/2])` | `:44-48` |
| P-5 | verify: pepper boş → `false` | `:56-58` |
| P-6 | verify: aynı HMAC → `password_verify` | `:59-60` |

```text
hash yolu:  raw --HMAC(pepper)--> peppered --argon2id--> hash (DB)
verify yolu: raw --HMAC(pepper)--> peppered --password_verify(hash)--> bool
```

**Ters sürüş yok:** hash'ten ham parolaya yol yok (anayasa §5) — bu akışın
"çözme" adımı **bool doğrulamadır**, plaintext değil.

## §12 JWT İmza Sürüşü (JwtService)

| Adım | Eylem | Satır |
|---|---|---|
| J-1 | `issue(userId, extra)` — boş sub → exception | `:44-48` |
| J-2 | `jti = bin2hex(random_bytes(16))` | `:51` |
| J-3 | `exp = now + ttl` (varsayılan 3600) | `:52, 34` |
| J-4 | `iss/aud/sub/iat/nbf/exp/jti` — `extra` ezilmez üzerine | `:54-62` |
| J-5 | `JWT::encode(claims, private, RS256)` | `:64` |
| J-6 | `validate(token)` — boş → `null` | `:85-87` |
| J-7 | `JWT::decode(public, RS256)` — hata → `null` (ayrıntı yok) | `:90-94` |
| J-8 | `iss`/`aud`/`sub`/`jti` elle — uyuşmazlık → `null` | `:96-104` |
| J-9 | claims array döner | `:107-108` |
| J-10 | revocation (jti geçerliliği) **çağrıran katmanda** | `:77-79` (doküman notu) + `api index.php:157-158` |

**Fail-closed kanıtı:** `validate()` hiçbir hata yolunda exception **fırlatmaz
/ true döndürmez**; başarısızlık tek dönüş değeri `null` (`:83-109`).

## §13 Ortak Güvenlik Zinciri Sürüşü (pipeline bağlamı)

| # | Sıra | Middleware | Kanıt |
|---|---|---|---|
| 1 | OriginCheck | `OriginCheckMiddleware` | `PageRouterKernel.php:331` |
| 2 | CORS | `CorsMiddleware` | `:332` |
| 3 | Rate limit | `RateLimiterMiddleware` | `:333` |
| 4 | Güvenlik başlıkları + CSP nonce üretimi | `SecurityHeadersMiddleware` | `:334` (+ `:18` nonce) |
| 5 | Oturum başlat/uzat | `SessionManagerMiddleware` | `:335` |
| 6 | CSRF | `CsrfMiddleware` | `:336` |
| 7 | Bypass/Auth/Permission/Validation | `:337-340` | `PageRouterKernel.php:337-340` |

**Nonce zinciri (sürüş):** `SecurityHeadersMiddleware` request'e `_csp_nonce`
yazar (`:17-19`) → `SessionManagerMiddleware` onu `startOrExtend`'e geçirir ve
session'a yazar (`SessionManagerMiddleware.php:18` + `SessionLifecycle.php:52`).
Yani **başlık katmanı → oturum katmanı** yönünde tek yönlü akış vardır.

## §14 Durum Matrisi (durum → sonuç)

| Durum | Çözme | Şifreleme | Parola hash | JWT validate |
|---|---|---|---|---|
| Tam girdi | plaintext | base64 sarma | hash | claims |
| Bozuk/eksi | exception (`:213`) | — | — | `null` (`:86`) |
| Etiket yanlış | exception (`:230`) | — | — | `null` (`:91-94`) |
| Anahtar yanlış | exception | ⚠️ H-03 (`false` kontrolsüz) | — | exception (`:116`) |
| Pepper yok | — | — | exception / false (`:40`, `:56`) | — |
| Süre dolmuş | — | — | — | `null` (kütüphane, `:91`) |

## §15 Doğrulama Adımları (Verification Drive)

| # | Adım | Yöntem | Kriter | Sonuç (2026-10-15) |
|---|---|---|---|---|
| V-1 | IV her çağrıda yeni | `OAuthManager.php:190` okundu | fonksiyon içi `random_bytes` | ✅ |
| V-2 | Tag 16 byte | `:201` | `$tag_length = 16` | ✅ |
| V-3 | Min uzunluk denetimi | `:213` | `< 29` | ✅ |
| V-4 | Fail-closed çözme | `:230-232` | exception | ✅ |
| V-5 | Şifreleme `false` kontrolü | `:193-204` arası okundu | `=== false` yok | ❌ → H-03 ⚠️ |
| V-6 | sodium/ChaCha | grep `sodium_` | 0 isabet | ❌ sürüş yok ⚠️ |
| V-7 | MD5/DES güvenlik kullanımı | grep | 0 isabet | ✅ |
| V-8 | Parola yolunda openssl | `Password.php` 67 satır | 0 isabet | ✅ |
| V-9 | `hash_equals` kullanımı | 3 nokta | Csrf:42 · ApiKey:171 · Theme:253/278 | ✅ |

## §16 Sürüş Senaryoları (Drive Scenarios)

| # | Senaryo | Sürüş | Sonuç |
|---|---|---|---|
| D-1 | Token şifreleme | E-1..E-7 | base64 sarma |
| D-2 | Aynı plaintext tekrar | yeni IV | farklı çıktı |
| D-3 | Sarmada 1 byte kırpma | `:213` throw | fail-closed |
| D-4 | Tag değiştirme | `:230` throw | fail-closed |
| D-5 | Doğru sarma + doğru anahtar | `:234` plaintext | |
| D-6 | Parola hash + doğru pepper | `password_verify` true | |
| D-7 | Parola verify + yanlış pepper | false | |
| D-8 | JWT: doğru imza, yanlış aud | `null` (`:99-101`) | |
| D-9 | JWT: süresi dolmuş | `null` (kütüphane) | |
| D-10 | Şifrelemede `openssl_encrypt` false | ⚠️ H-03 — davranış tanımsız | ADR/açık |
| D-11 | CSP nonce akışı | başlık → oturum → script | §13 |
| D-12 | ChaCha20 çağrısı | sürüş yok | ⚠️ U-3 |

## §17 Pseudocode Şablonları

```text
ŞABLON-1 (AES-256-GCM şifreleme — repo kalıbı)
  iv  = random_bytes(12)
  tag = ''
  ct  = openssl_encrypt(pt, 'aes-256-gcm', key, OPENSSL_RAW_DATA, iv, tag, '', 16)
  -- önerilen ek (H-03 düzeltmesi): if ct === false -> THROW (şu an YOK)
  return base64(iv ‖ tag ‖ ct)

ŞABLON-2 (çözme — fail-closed)
  raw = base64_decode(s, strict)
  if raw === false || len(raw) < 29 -> THROW
  pt = openssl_decrypt(raw[28..], 'aes-256-gcm', key, RAW, raw[0..12), raw[12..28))
  if pt === false -> THROW          # tag mismatch
  return pt

ŞABLON-3 (parola — hash tek yön)
  peppered = hmac_sha256(raw, pepper)
  hash = argon2id(peppered, m=65536 t=4 p=2)
  verify: return password_verify(hmac_sha256(raw, pepper), hash)
```

> Pseudocod'dur; birebir kod `encryption-ornekler.md` §1-§2'dedir.

## §18 Performans ve Zamanlama Sürücüsü

| Faktör | Etki | Sürüş notu |
|---|---|---|
| `random_bytes(12)` | bloklayıcı entropy okuması | her çağrıda 1 kez — `:190` |
| argon2id (65536 KB) | kasıtlı yavaşlık | login başına ~(m/t/p) — `Password.php:45-47` |
| HMAC-SHA-256 | argon2 öncesi hızlı katman | `:43, 59` |
| RS256 encode/decode | RSA imzası — GCM'den ağır | `JwtService.php:64, 90` |
| PEM dosya okuması | ilk çağrıda 1 kez, bellekte cache | `:111-136` |
| `hash_equals` | sabit zamanlı | `CsrfMiddleware.php:42` |

## §19 Bilinmeyenler (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | anahtar üretimi akışı (§3 K-1/K-2) | ⚠️ VERIFICATION REQUIRED |
| U-2 | anahtar uzunluğu gerçekten 32 byte mu | ⚠️ VERIFICATION REQUIRED |
| U-3 | `ext-sodium` ortam envanteri + ChaCha planı | ⚠️ VERIFICATION REQUIRED |
| U-4 | H-03 (`openssl_encrypt` false) gerçekleşme olasılığı ve davranışı | ⚠️ VERIFICATION REQUIRED |
| U-5 | GCM AAD gereksinimi | UNKNOWN |
| U-6 | rotasyon sürüşü (eski anahtarla okuma) | ⚠️ VERIFICATION REQUIRED |
| U-7 | şifreli token'ların DB'de yeniden şifreleme/rotasyon akışı | UNKNOWN |

## §20 Test Sürüşü (Test Drive)

| Test | Sürüş | Beklenen |
|---|---|---|
| Round-trip | encrypt → decrypt | plaintext birebir |
| IV tekrarı | iki encrypt | çıktı != çıktı |
| Tag mutasyonu | sarmadaki byte'ı değiştir | exception |
| Kısa sarma | 28 byte | exception |
| Boş girdi | `decrypt('')` | exception |
| Pepper yok | `hashWithPepper('')` | exception (`Password.php:41`) |
| JWT sırasız | `validate('garbage')` | `null` |

## §21 Sürüş Rolleri (Drive Ownership)

| Görev | Sorumlu |
|---|---|
| AEAD sürüş kalıbı | Security Engineer |
| H-03 düzeltmesi (false kontrolü) | Security Engineer → code review |
| Anahtar üretim/rotasyon ADR'si | Architect + Security Engineer |
| Sürüş testleri (§20) | QA Engineer |
| ChaCha/sodium envanteri | DevOps Engineer |

## §22 BAĞLANTILAR (Wiki — yalnız diskte var olan adlar)

| Bağlantı | Not |
|---|---|
| [[encryption-anayasa]] | kurallar bu dosyadan gelir (madde A-01..A-12) |
| [[encryption-ornekler]] | gerçek kod alıntıları |
| [[encryption-kapsam]] | üç yüzey sınırı |
| [[encryption-dogrulama]] | kontrol listesi V-1..V-9 karşılığı |
| [[secrets-kapsam]] | anahtar kaynağı devri |
| [[session-security-kapsam]] | nonce/CSRF akışı komşusu |
| [[security-master]] | K009 kök |

## §23 Revizyon ve Sürüm

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk yazım — şifreleme/çözme/parola/JWT sürüşleri + 9 doğrulama adımı; H-03 bulgusu |

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-15 |
| Durum | draft (U-1..U-7 açık) |
| Erişim | `.ai/architecture/K009-security/encryption/encryption-surucu.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
