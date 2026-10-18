---
node_id: K009-security/encryption/kapsam
node_type: module
status: verify-required
parent_id: K009-security/encryption
children: []
depends_on:
  - K009-security/encryption
evidence:
  - "OAuthManager.php 188-235 · Password.php 24, 43, 59 · SecurityHeadersMiddleware 38-41"
validation:
  - "2026-10-12 — her § iddiası satır aralığına bağlandı"
open_questions:
  - "encryptionKey kaynak/üretim yolu → UNKNOWN (kod okunmadı)"
group: mimari
---

# encryption-kapsam.md — Şifreleme Kapsamı

## §1 Üç Yüzey

| Yüzey | Teknoloji | Konum | Geri döndürülebilir mi? |
|---|---|---|---|
| Taşıma | TLS 1.2/1.3 | sunucu yapılandırması | evet (anahtar sunucuda) |
| Bekleme | AES-256-GCM | OAuthManager·188-205 | evet (encryptionKey) |
| Parola | argon2id + HMAC-SHA256 | Password·43, 59 | **HAYIR** (hash) |

Kural karıştırması yasak: parola HASHLENİR, token ŞİFRELENİR. İkisinin de
"izometrik" görünmesi, geri dönüş farkını gizler.

## §2 AES-256-GCM Bloğu (OAuthManager · 188-205)

| Öğe | Değer | Kaynak |
|---|---|---|
| Algoritma | aes-256-gcm (AEAD) | satır 195 |
| IV | random_bytes(12) — 96 bit | satır 190 |
| Auth tag | 16 byte (satır 201) | GCM doğrulaması |
| Sarma | base64(iv ‖ tag ‖ ciphertext) | satır 204 |
| Çözme | uzunluk ≥29 denetimi (satır 213) | ayrıştırma 217-219 |

Neden GCM: şifreleme + bütünlük tek adımda (tag). CRC/HMAC ayrımı gerekmez.
Neden 12 byte IV: GCM'ın önerilen varsayılanı; tekrar IV kullanmak GCM'de
MANA'dır → IV rastgele üretilir ve TEK KULLANIMLIKTIR.

## §3 Parola Tarafı (Password.php)

| Adım | İşlem | Satır |
|---|---|---|
| 1 | min 8 karakter | 16 |
| 2 | argon2id parametreleri (65536/4/2) | 24 (yorum) |
| 3 | pepper ile HMAC-SHA256 | 43, 59 |
| 4 | password_hash (argon2id) | 26-33 |
| 5 | verify → hash_equals mantığı | 54-59 |

Pepper ≠ salt: salt hash içinde saklanır (kendinden açık), pepper DIŞARIDADIR
(dosya/env) → veritabanı sızıntısında pepper olmadan kaba kuvvet pahalıdır.

## §4 Taşıma Tarafı (TLS)

Kapsam: HTTPS zorunluluğu, protokol sürümü, cipher suite tercihi.
Kanıt durumu: başlıklar (HSTS/COOP) doğrulandı; sunucu TLS yapılandırması
(TLS 1.2 altı kapalı mı?) bu depoda DEĞİLDİR → UNKNOWN, deploy katmanına devir.

## §5 Algoritma Yasakları (K009 kuralı)

| Yasak | Neden | Karşılık |
|---|---|---|
| MD5 / SHA-1 (güvenlik amaçlı) | çarpışma | SHA-256+ / argon2id |
| DES / 3DES / RC4 | zayıf | AES-256-GCM / ChaCha20 |
| ECB modu | desen sızıntısı | GCM |
| Parolayı şifreleyip saklamak | anahtar sızarsa hepsi | hash (argon2id) |
| IV'yi tekrar kullanmak | GCM'de güven çöküşü | random_bytes(12) her seferinde |

## §6 Kapsam Dışı (bu düğüm DEĞİL)

1. pepper/API key saklama yeri → secrets/.
2. Session cookie şifrelemez (HttpOnly + Secure yeterli) → session-security/.
3. JWT imzası (RS256) → authentication/ (imza ≠ şifreleme).
4. Sertifika yenileme otomasyonu → devops (dalga-3).

## §7 Kabul Kriterleri

1. AES bloğunda IV/tag/sarma satır aralıklarıyla kanıtlanmalı (§2).
2. Hash ≠ şifreleme ayrımı her yerde korunmalı (§1, §3).
3. Bilinmeyen anahtar kaynağı UNKNOWN olarak kalmalı — UYDURULMAZ.
4. Yasaklı algoritma taraması yapılmalı (doğrulama §6).

## §8 Anahtar Yaşam Döngüsü (plan — kanıt eksikleri işaretli)

| Aşama | Durum | Kanıt |
|---|---|---|
| Üretim (KDF/rastgelelik) | ⚠️ UNKNOWN | kod okunmadı |
| Saklama (env/dosya/secret manager) | ⚠️ UNKNOWN | secrets/ devri |
| Kullanım (encrypt çağrısı) | ✅ kanıtlı | OAuthManager·193-202 |
| Dönüşme (rotasyon) | ⚠️ UNKNOWN | ADR bekliyor |
| Emeklilik (revokasyon) | ⚠️ UNKNOWN | ADR bekliyor |

Beş aşamadan yalnız biri disk kanıtlıdır → bu, encryption'ın kademe-3'te
"anahtar yönetimi" iş kalemine ihtiyaç duyduğunu gösterir (bilesenler §7).

## §9 Hash ≠ Şifreleme Karşılaştırma

| Özellik | Şifreleme (AES-GCM) | Hash (argon2id) |
|---|---|---|
| Amaç | gizlilik | kimlik doğrulama |
| Tersi | decrypt ile mümkün | imkânsız |
| Anahtar | encryptionKey gerekli | gerekmez (yalnız pepper/salt) |
| Örnek | OAuth token (OAuthManager·188) | parola (Password·26-33) |
| Doğrulama | tag ile | yeniden hesapla + hash_equals |

Karışım hatası senaryosu: parola AES ile şifrelenip saklansa → anahtar
sızınca TÜM parolalar açılır. Bu yüzden Password dosyasında openssl
çağrısı YOKTUR (doğrulama kontrol 12).

## §10 İlişkili Düğümler ve Devir

| Konu | Giden düğüm | Gerekçe |
|---|---|---|
| encryptionKey / pepper dosyası | secrets/ | anahtar yönetimi |
| RS256 imzası, JWT | authentication/ | imza kimlik için |
| HTTPS/HSTS | session-security/ (HSTS) + bu düğüm §4 | taşıma |
| TLS cipher suite sunucuda | devops (dalga-3) | depo dışı yapılandırma |

Devir kuralı: bu düğüm ALGORİTMA ve MOD seçer; anahtarın nerede durduğu
başka düğümdür. İkisi aynı dosyada yazırsa sahiplik belirsizleşir.

## §11 Özet Karar Tablosu

| # | Karar | Kanıt | Durum |
|---|---|---|---|
| E-01 | AES-256-GCM (AEAD) | OAuthManager·195 | aktif |
| E-02 | IV = random_bytes(12), tek kullanım | OAuthManager·190 | aktif |
| E-03 | Tag 16 byte doğrulama | OAuthManager·201, 218 | aktif |
| E-04 | Çözmede uzunluk denetimi | OAuthManager·213 | aktif |
| E-05 | Parola: argon2id + HMAC pepper | Password·43, 59 | aktif |
| E-06 | MD5/SHA-1/DES/ECB yasak | K009 kuralı | kural |
| E-07 | Sunucu TLS ayarı depo dışı | UNKNOWN | devir |
| E-08 | encryptionKey kaynağı bilinmiyor | UNKNOWN | secrets/ devri |

## §12 AES-GCM PARAMETRE DERİNLİĞİ VE NONCE KURALLARI (2026-10-18 — APPEND-ONLY)

> §1-§11 silinmez; E-01..E-08 tablosunun teknik zeminidir. Kaynak: NIST SP 800-38D
> (security-web-arastirma.md §22 satır 6 — HTTP 200, Legacy PDF, 2026-10-18).

### 12.1 GCM Bileşenleri ve CoreMusic Karşılığı

| Bileşen | Boyut | Üretim | CoreMusic kanıtı | Kural |
|---|---|---|---|---|
| Anahtar | 256 bit | CSPRNG / secrets manager | E-08 (kaynak UNKNOWN ⚠️) | anahtar asla log/commit |
| IV/Nonce | 96 bit (12B) | `random_bytes(12)` her şifrelemede YENİ | E-02 (OAuthManager·190) | tekrar YASAK — aynı (anahtar,nonce) iki kez kullanılmaz |
| Tag | 128 bit (16B) | GCM üretir | E-03 (·201, ·218) | çözümde ÖNCE tag doğrulama; başarısızsa RED |
| AAD | değişken | bağlam verisi (ör. kullanıcı ID) | ⚠️ K009 kodunda kullanımı doğrulanmadı | AAD, veriyi bağlar — kimlik değişikliğini reddeder |

### 12.2 Nonce Yeniden Kullanımının Sonucu (neden kritik)

| Hata | Sonuç |
|---|---|
| Aynı anahtar + aynı nonce ile iki farklı plaintext | Poly1305 MAC anahtarı sızar; gizlilik+güvenlik BİRLİKTE çöker (GCMNonce tekrarı → authenticated encryption ihlali) |
| Sayaç nonce + sıfırlama | restart'ta sayaç başa döner → tekrar → ihlal |
| Rastgele 12B nonce (2⁹⁶ uzay) | pratikte tekrar olasılığı ihmal edilebilir — K009 seçimi (E-02) |
| 16B nonce (96+32) | GCM'de desteklenir ama performans/uyum maliyeti; 12B önerilir |

### 12.3 GCM vs Alternatifler (bu projede neden GCM)

| Mod | AEAD | Öneri | K009 |
|---|---|---|---|
| GCM | evet | yüksek performans, geniş donanım desteği | SEÇİLİ (E-01) |
| CBC + HMAC | iki geçiş | iyi ama iki ayrı primitive senkronizasyonu (encrypt-then-MAC zorunlu) | yok |
| ChaCha20-Poly1305 | evet | donanım-AES olmayan platformlar (libsodium PHP'de mevcut) | sodium §20.1 şablonu |
| ECB | HAYIR | desen sızıntısı | YASAK (E-06) |
| CTR | HAYIR (bütünlük yok) | yalnız GCM içinde | — |

### 12.4 Doğrulama Adımları (çözüm tarafı — E-03/E-04 derinliği)

| Adım | Kural | Başarısızsa |
|---|---|---|
| 1 | ciphertext uzunluğu ≥ tag boyutu (en az 16B) | RED (E-04 · ·213) |
| 2 | tag'ı ayıkla | — |
| 3 | `openssl_decrypt`/eşdeğeri GCM'de tag ÖNCE doğrulanır; PHP `openssl_decrypt` tag parametresi `OPENSSL_RAW_DATA` + ayrı `$tag` argümanı ile geçilir (php.net manual §22 satır 12) | false → RED + log + olay |
| 4 | plaintext kullanılır | — |
| 5 | bellekte tutulan çözülmüş veri S1 sınıfı ise temizlenir | — |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
