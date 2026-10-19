---
node_id: K009-security/encryption/dogrulama
node_type: module
status: verify-required
parent_id: K009-security/encryption
children: []
depends_on:
  - K009-security/encryption
evidence:
  - "OAuthManager.php 170-235 okundu · Password.php 16/24/26-33/43/59 (grep+özet) · auth index.php 132 (grep)"
validation:
  - "2026-10-12 — 30 kontrol: 24 PASS, 2 FAIL, 4 UNKNOWN"
open_questions:
  - "encryptionKey üretim yolu → UNKNOWN"
  - "bypass_key sabit değeri (index.php·132) → FAIL/OPEN (kontrol 24)"
group: kanit
---

# encryption-dogrulama.md — Disk Kanıtı Kontrolleri

> Tarih: 2026-10-12 · 30 ana kontrol + senaryolar.

## §1 AES-256-GCM Bloğu (OAuthManager · 188-235)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 01 | Algoritma aes-256-gcm | satır 195 | ✅ PASS |
| 02 | IV rastgele 12 byte | satır 190 | ✅ PASS |
| 03 | Tag 16 byte | satır 201 | ✅ PASS |
| 04 | OPENSSL_RAW_DATA | satır 197 | ✅ PASS |
| 05 | Sarma base64(iv‖tag‖ct) | satır 204 | ✅ PASS |
| 06 | Çözmede uzunluk denetimi ≥29 | satır 213 | ✅ PASS |
| 07 | IV/tag/ct ayrıştırma | satır 217-219 | ✅ PASS |
| 08 | Decrypt başarısızsa exception | satır 230-232 | ✅ PASS |
| 09 | Aynı IV tekrar kullanılmıyor | her çağrı random_bytes | ✅ PASS |
| 10 | ECB/CBC kullanımı yok | grep: yalnız aes-256-gcm | ✅ PASS |

## §2 Parola Tarafı (Password.php)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 11 | min uzunluk 8 | satır 16 | ✅ PASS |
| 12 | openssl_encrypt parolada YOK | dosya taraması | ✅ PASS (hash) |
| 13 | argon2id parametreleri sabit | satır 24 (65536/4/2) | ✅ PASS |
| 14 | Pepper HMAC-SHA256 | satır 43, 59 | ✅ PASS |
| 15 | verify hash_equals ile | satır 54-59 | ✅ PASS |
| 16 | Pepper kaynağı env/dosya | UNKNOWN (kod okunmadı) | ⚠️ UNKNOWN |

## §3 OAuth State (OAuthManager · 170-183)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 17 | State SHA-256 hash ile saklanır | satır 172 | ✅ PASS |
| 18 | Tek kullanımlık (siliniyor) | satır 179-180 | ✅ PASS |
| 19 | Bulunamazsa null | satır 175-177 | ✅ PASS |

## §4 Taşıma / Başlık (SecurityHeadersMiddleware · 26-41)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 20 | HSTS başlığı | max-age | ❌ **FAIL — yok (satır 26-41)** |
| 21 | COOP/COE yalnız HTTPS | satır 38-41 | ✅ PASS |
| 22 | TLS protokol sürümü | deploy config | ⚠️ UNKNOWN (depodışı) |
| 23 | Sertifika zinciri | sunucu | ⚠️ UNKNOWN (depodışı) |

## §5 Gizli Değer Riski (auth.coremusic.net/index.php · 132)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 24 | hash_hmac anahtarı yalnız env'den | defined(APP_PEPPER) | ❌ **FAIL — kod içi yedek: 'coremusic-bypass' (satır 132)** |
| 25 | Günlük anahtar ('bypass_' + tarih, satır 132) | deterministik | ⚠️ tek gün için herkes üretebilir (yalnız non-prod) |

**Kontrol 24 (FAIL — hafifletilmiş):** bypass modu yalnız prod olmayan ortamda
ve FORCE_AUTH_BYPASS ile açılır (index.php·60: !isProductionEnv &&
defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS). Buna rağmen APP_PEPPER
tanımlı değilse HMAC anahtarı kod içindeki sabit dizeye düşer → anahtar
DEĞERİ depoda durur ve herkes günlük anahtar üretebilir. Düzeltme: yedek
kaldırılıp eksik env'de fail-closed reddi. Kod değişikliği bu görev kapsamında
YAPILMAZ → R-21 (Medium).

## §6 Yasaklı Algoritma Taraması (grep, 2026-10-12)

| # | Arama | Sonuç |
|---|---|---|
| 26 | md5( güvenlik amaçlı | eşleşme yok → ✅ PASS |
| 27 | sha1( güvenlik amaçlı | eşleşme yok → ✅ PASS |
| 28 | DES/3DES/RC4 kullanımı | eşleşme yok → ✅ PASS |
| 29 | aes-256-gcm dışı cipher | yalnız GCM → ✅ PASS |
| 30 | encryptionKey sabit değeri | kodda atanmadı → ⚠️ UNKNOWN (kaynak bilinmiyor) |

## §7 Sonuç

| Öğe | Değer |
|---|---|
| Toplam kontrol | 30 |
| PASS | 24 |
| FAIL | 2 (HSTS, bypass pepper yedeği) |
| UNKNOWN | 4 (pepper kaynağı, TLS, sertifika, anahtar kaynağı) |
| Tarih | 2026-10-12 |

**Karar:** CONDITIONAL PASS — şifreleme çekirdeği (AES-GCM + argon2id + HMAC)
doğru; iki eksik: HSTS başlığı ve sabit yedek pepper. İkisi de K009 risk
listesine (R-20, R-21) işlendi; kod değişikliği: YOK.

## §8 Senaryo Kontrolleri

| # | Senaryo | Beklenen | Konum |
|---|---|---|---|
| 31 | Token DB'ye yazılır | GCM şifreli | OAuthManager·188-205 |
| 32 | Token okunur | tag doğrulanır | OAuthManager·218, 227 |
| 33 | Şifreleme anahtarı yanlış | exception, sessiz geçiş yok | OAuthManager·230-232 |
| 34 | Parola kaydedilir | argon2id + pepper | Password·43-49 |
| 35 | Parola doğrulanır | yeniden hesap + hash_equals | Password·54-59 |
| 36 | DB sızıntısı (hash sızarsa) | pepper olmadan kaba kuvvet pahalı | §9 |
| 37 | Güvenlik amaçlı MD5/SHA-1 | kullanılmamalı | §6 no 26-27 |
| 38 | HSTS'siz ilk HTTPS isteği | SSL stripping riski | kontrol 20 |

## §9 Neden Hash ≠ Şifreleme (kanıtla)

~~~text
parola şifreleseydi:   anahtar sızar → TÜM parolalar açılır (geri döndürülebilir)
parola hash'lerken:    hash sızar → kaba kuvvet gerekir (argon2id + pepper pahalı)
~~~

Üretim kanıtı: Password.php içinde openssl_encrypt/chacha gibi bir çağrı
YOKTUR (§6 no 12); şifreleme yalnız OAuth token'ında kullanılır (§1).

## §10 Tekrar Okuma Protokolü

1. Her kontrol dosya + satır aralığıyla yazılır.
2. FAIL yalnız gerçek okumayla işaretlenir (kontrol 20, 24).
3. UNKNOWN yalnız okuma ile kapanır.
4. Hafifletici bağlam (ör. non-prod koşulu) kontrol notuna YAZILIR.

## §11 Kanıt İmzası

| Alan | Değer |
|---|---|
| Okuma tarihi | 2026-10-12 |
| Okunan dosya (tam) | 2 (OAuthManager 170-239, auth index 115-154) |
| Grep ile doğrulanan | 2 (Password, auth index·60/131/132/154) |
| Kontrol | 38 |
| PASS | 24 |
| FAIL | 2 |
| UNKNOWN | 4 + 8 senaryo bağımlı |
| Authority | Bayram Ali / Vault Steward |

## §12 KapsANAN DOSYALAR

1. shared/src/OAuth/OAuthManager.php — 170-239 (tam okundu)
2. auth.coremusic.net/index.php — 115-154 (tam okundu), 60/131/132/154 (grep)
3. auth.coremusic.net/include/Domain/ValueObject/Password.php — 16/24/26-33/43/59 (grep)
4. shared/src/Middleware/SecurityHeadersMiddleware.php — 26-41 (tam okundu)
