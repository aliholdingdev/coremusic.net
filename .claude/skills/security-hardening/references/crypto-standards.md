# Kripto Standartları — CoreMusic (ADR-022/023/024/027)

> Skill: `security-hardening` v4.0.0 · CoreMusic · Güncel: 2026-10-21
>
> Kaynak: `.ai/.decisions/accepted/ADR-022` (şifreleme algoritmaları — FROZEN),
> `ADR-023` (cert pinning), `ADR-024` (secrets yönetimi), `ADR-027` (Argon2id
> doğrulama), `.ai/CLAUDE.md` §23 (API Gateway JWT), `.ai/AGENTS.md` kural 8
> (Hybrid Auth — ADR-052). ADR 001-037 donmuş: **yorum/düzeltme yok, uygulama var.**

---

## 1. Algoritma kararı tablosu (ADR-022)

| Amaç | ZORUNLU | Parametre | Yasak / Not |
|---|---|---|---|
| Credential vault (DB'de parola/secret) | **AES-256-GCM** | 96-bit IV (12 byte) · 16-byte tag | CBC/ECB yasak (§4.1) |
| Alternatif (kütüphane/performans) | **XChaCha20-Poly1305** | 192-bit nonce · 16-byte tag | ADT-006 doğrulanmış — **AES-ECB yasak** |
| Parola hash | **Argon2id** | m=64MB · t=4 · p=2 | md5/sha1 yasak; `PASSWORD_DEFAULT` = bcrypt (PHP 8.4) |
| Token/OTP hash (süreli sayaçlı) | SHA-256 (+ doğru yerde salt) | — | **Zamanlamalı** karşılaştırma: `hash_equals` |
| Email doğrulama tokenı | Salt-okunur (hash YOK) | — | ADR-020 §2 — geri dönülemez olmamalı |
| Blind index (PII arama) | Deterministik hash | Kullanıcı başına key | SSS — ⚠️ VERIFICATION REQUIRED (vault'ta tanım yok) |
| Symmetric vault anahtarı | 32-byte (256-bit) | `random_bytes` / OS KMS | ADR-024 §2 |
| Asymmetric (kayıt/şifreleme anahtarı) | RSA / ECDSA / Ed25519 | — | ADR-022 §2 — özel anahtar: password veya vault |

**Not (neden GCM?):** AEAD (Authenticated Encryption with Associated Data) —
şifreleme + bütünlük tek geçişte. CBC'de önce MAC almak gerekir (padding oracle
riski); GCM'de tag doğrulama decryption'ın parçası. Kural: tag doğrulanmadan
plaintext'e dokunulmaz.

---

## 2. AES-256-GCM — doğru kullanım (ADR-022 §2)

```php
// KAYIT (encrypt): iv 96-bit = 12 byte — random_bytes (asla 0 / sayaç / az)
$iv      = random_bytes(12);
$tag     = '';                            // 16-byte dönecek
$cipher  = openssl_encrypt($plain, 'aes-256-gcm', $key,
                           OPENSSL_RAW_DATA, $iv, $tag, '');
$record  = $iv . $tag . $cipher;          // tek kayıt: iv||tag||cipher

// OKUMA (decrypt): tag YANLIŞSA openssl false döner — plaintext'e dokunma
$iv      = substr($record, 0, 12);
$tag     = substr($record, 12, 16);
$cipher  = substr($record, 28);
$plain   = openssl_decrypt($cipher, 'aes-256-gcm', $key,
                           OPENSSL_RAW_DATA, $iv, $tag);
if ($plain === false) {
    throw new CryptoException('authentication_failed');   // fail-closed
}
```

### 2.1 IV/nonce yönetimi (en sık hata)

| Desen | Sonuç | Kural |
|---|---|---|
| Aynı key + aynı IV | GCM'de **tag çökmesi** — iki ciphertext aynı stream'i ele verir | Her kayıtta yeni `random_bytes(12)` |
| IV = 0 / sayaç / timestamp | Örüntü sızıntısı | Sayaç yalnız nonce-zorunlu şemalarda (GCM-SIV benzeri) — vault'ta yok |
| IV'ü secret olarak saklama | — | IV gizli değil; key gizli |
| `tag` doğrulamadan veri kullanma | Padding/tag oracle | decrypt false → hata, plaintext yok |

**Adversary senaryosu (kaydetme):** Aynı anahtar + tekrarlanan IV ile iki
ciphertext `C1 = P1 ^ KS`, `C2 = P2 ^ KS` → `C1 ^ C2 = P1 ^ P2` — iki plaintext'in
XOR'ı. Salırgan iki kaydı yan yana koyup alan farklarını çıkarır (credential
vault'ta: şifrelenmiş sütun aynı IV'le yazılıyorsa). 96-bit IV random_bytes →
çakışma olasılığı ≈ `q²/2^97` — q = milyarlar kayıtta bile ihmal edilebilir.

---

## 3. Argon2id — parametre ve doğrulama (ADR-022 §2 + ADR-027)

```php
// HASH — 64MB/t=4/p=2 (ADR-022 §1'deki kural; ADR-027 §2 ile uyumlu)
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,    // 64MB (KB cinsinden)
    'time_cost'   => 4,
    'threads'     => 2,
]);

// VERIFY — salt hash'ten gelir; yanlış hash → false
if (!password_verify($input, $hash)) { ... }

// REHASH — parametre artınca (PHP sürüm/AKS günceli) tek geçişte
if (password_needs_rehash($hash, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2,
])) { $newHash = password_hash($input, PASSWORD_ARGON2ID, [...]); }
```

### 3.1 Neden Argon2id?

- **bcrypt'in zayıf yönü:** GPU'da paralelleşir; cost yalnız CPU maliyetini
  yükseltir, bellek maliyetini değil. Yavaş hash ≠ pahalı hash.
- **Argon2id:** bellek-tüketimi (GPU'da 64MB × paralel eşik) + t=4 geçiş +
  p=2 thread. Kriptografik yarışma galibi (2015; PHC). İd = side-channel'e
  karşı hibrit (Data-Independent ilk geçiş + Data-Dependent sonrakiler).
- **AMD/INTEL seviyesi (`.ai/CLAUDE.md` §23):** `m=19MB, t=2, p=1, 64-bit` —
  API Gateway JWT doğrulaması içindir (stateless, hızlı), kullanıcı parolası
  DEĞİL. Kullanıcı parolası = 64MB/t=4/p=2 (ADR-022/027). **İki profil farklı
  amaç — karıştırma yasak.**

### 3.2 Hash formatı (PHP `password_hash` Argon2 çıktısı)

```
$argon2id$v=19$m=65536,t=4,p=2$<salt-b64>$<hash-b64>
```

`password_get_info()` ile algoritma teşhisi; eski bcrypt kaydı geçince
`password_needs_rehash` → rehash (kayıtta bekleyen plaintext yok, verify
başarısında yeni hash yazılır — eski hash ile karşılaştırma güvenli).

### 3.3 Doğrulama (ADR-027)

- Doğrulama: `password_verify` (salt-hash'te).
- Mail doğrulama tokenı: salt-okunur (ADR-020 §2; ADR-027 §1).
- Sayaçlı/süreli token (OTP/CSRF): SHA-256 + `hash_equals` (ADR-027 §1 —
  "hash'e geçirme + zamanlamalı karşılaştırma").

**⚠️ VERIFICATION REQUIRED:** ADR-027 §2'nin "zamanlamalı karşılaştırma"
iadesinin `hash_equals` olduğu varsayımı (ADR-010 §2'deki desenle aynı) —
ADR-027 metni bu oturumda satır satır okunmadı (§kapsam dışı).

---

## 4. Anahtar saklama (ADR-024 FROZEN)

### 4.1 Yasak listesi

```php
// YASAK — hardcoded (rule 6)
$key = 'super-secret-key';
// YASAK — .md / .json / .log
file_put_contents('keys.md', $key);        // .ai CLAUDE §5 Security
// YASAK — DSN içine password (ADT-016)
'dsn' => 'mysql:host=db;password=...'
// YASAK — koddan dış servise anahtar gönderme (ADR-024 §1)
$this->external->setKey($key);
// YASAK — aynı key'i her ortamda paylaşma (ADR-024 §1)
```

### 4.2 Doğru

```
.env (gitignored: .gitignore / .gitignore:69 — FIGMA_TOKEN örneği)
  └─ APP_ENCRYPTION_KEY=   ← credential vault (ADT-006) veya OS keyring / KMS
  └─ Yalnızca <service>@coremusic.net tarafından okunur (ADR-024 §2)
```

- Key uzunluğu: 32 byte (AES-256) — `random_bytes(32)` → base64/hex encoding.
- Key rotation: yeni kayıt = yeni key; eski kayıtlar eski key ile okunur
  (key id ekleme şeması **⚠️ VERIFICATION REQUIRED** — ADR-024 §2'de
  rotation mekaniği detayı bu oturumda doğrulanmadı).
- CI/CD: secret yönetim servisi + env substitution (global kural §5).

### 4.3 Şifreleme ≠ koruma

| Saldırı | AES-256-GCM savunması | Savunma SAVUNMAZ |
|---|---|---|
| Vault sızıntısı (DB dump) | Ciphertext okunamaz | Key `.env`'de ayrıysa TAMAM; key dump'la birlikteyse her şey gider |
| Statik analiz / source leak | Key koda gömülüyse anlamsız | **Kod içinde key = şifreleme yok** |
| Runtime memory dump | — | Bellekten key çalınır — sahip olduğu: ayrı process/işlem |
| Log leakage | — | Plaintext log'lanırsa cipher'i unut (rule: PII/credential log'lanmaz) |

---

## 5. Hybrid Auth + oturum (ADR-052 / ADR-011 / kural 7-8-10)

```
İstek: Authorization: Bearer <JWT>
  └─ session yoksa JWT → claims (role/jti/exp) → session (ADT-008 akışı)
  └─ session VARSA: session (lazy-start; ADR-011 §2)
  └─ Tüm state-changing: CSRF (session üzerinde — ADR-010 §1.1 hybrid kontrol)
```

- JWT doğrulama: `validateJwtToken` (ADR-010 §1.1 — **stub**; ⚠️ VERIFICATION
  REQUIRED: gerçek imza doğrulama implementasyonu vault'ta yok).
- Session fixation: giriş başarılı → `session_regenerate_id(true)` (kural 7).
- Logout: session sil + JWT revocation (jti listesi — ⚠️ VERIFICATION REQUIRED:
  revocation mekaniği ADR-052 §2'de var mı doğrulanmadı).
- MFA: TOTP yalnız `pragmarx/google2fa` (ADR-059) — SMS taşıma yasak.

---

## 6. Karşılaştırma çiftleri (skill içi audit)

| # | YASAK (bullet) | DOĞRU (code) |
|---|---|---|
| 1 | `password_hash($p, PASSWORD_DEFAULT)` | `PASSWORD_ARGON2ID` + m=65536,t=4,p=2 |
| 2 | `md5($p)` / `sha1($p)` / `crypt()` deseni | `password_hash` + `password_verify` |
| 3 | `aes-256-cbc` + `strlen($iv)` pad | `aes-256-gcm` + 12-byte IV + 16-byte tag |
| 4 | `openssl_encrypt(..., 'aes-256-ecb')` | GCM / XChaCha20-Poly1305 |
| 5 | `$key` literal / `.md` kayıt | `.env` / vault (ADT-006) |
| 6 | `hash('sha256', $csrf)` (CSRF: salt-okunur'da değil) | CSRF: salt-okunur `bin2hex(random_bytes(32))` (ADR-010 §2) |
| 7 | OTP `=== $code` | `hash_equals($expected, $userCode)` (kural 11) |
| 8 | Email tokenı hash'le (ADR-020 §1 ihlali) | salt-okunur gönder + DB'de `expires_at` (ADR-020 §2) |
| 9 | Session token sabit string | per-user random (ADR-011 §2) |
| 10 | API'de sabit X-Client-Key (ADR-028 §1) | rotating X-Client-Key (ADR-028 §2 — ADR-028 FROZEN) |

---

## 7. Kapsam dışı (bu dosya cevaplamaz)

- RSA/ECDSA imzalama spesifik key-boyu seçimi (ADR-022 §2 "RSA, ECDSA veya
  Ed25519" der — **boyu/runtime seçimi ⚠️ VERIFICATION REQUIRED**).
- HSM / KMS sağlayıcı seçimi (ADR-024 §2 bir isim vermez).
- TLS cert rotation periyodu (ADR-023 §1 "expiration süresinde rotation" der —
  gün sayısı vermez).
- Blind index implementasyonu (§1 tablosu — vault'ta yok).

Kapak: `.ai/.decisions/accepted/ADR-022..027` (okunmadıysa oku) +
`references/forbidden-patterns.md` §1.5-1.6.
