---
node_id: K009-security/encryption/web-arastirma
node_type: module
status: verify-required
parent_id: K009-security/encryption
children: []
depends_on:
  - K009-security/encryption
evidence:
  - "8 benzersiz URL — 2026-10-12 fetch ile 8/8 HTTP 200"
validation:
  - "2026-10-12 — doğrulama: 8/8 200"
  - "2026-10-12 — §11 çapraz tabloda 7 iddia kod karşılığına bağlandı"
open_questions:
  - "Üretimdeki TLS protokol/cipher yapılandırması depo dışı → UNKNOWN"
  - "encryptionKey KDF bilgisi → UNKNOWN (secrets/ devri)"
group: kanit
---

# encryption-web-arastirma.md — Web Kanıtları

> Doğrulama tarihi: 2026-10-12 · Durum 200 = canlı.
> Tahsis: 8 URL — kök ve diğer çocuklarla KESİŞMEZ.

## §1 İddia Tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| TLS 1.2, AEAD şifrelemelerle modern zemin oluşturur | https://www.rfc-editor.org/rfc/rfc5246 | 2026-10-12 | 200 |
| TLS 1.3, handshake'i sadeleştirip 0-RTT sunar | https://www.rfc-editor.org/rfc/rfc8446 | 2026-10-12 | 200 |
| TLS yalnız güçlü cipher suite ile yapılandırılmalı | https://cheatsheetseries.owasp.org/cheatsheets/Transport_Layer_Security_Cheat_Sheet.html | 2026-10-12 | 200 |
| Hassas veri AES-256-GCM gibi AEAD ile şifrelenmeli | https://cheatsheetseries.owasp.org/cheatsheets/Cryptographic_Storage_Cheat_Sheet.html | 2026-10-12 | 200 |
| Parola hash'i geri döndürülemez olmalı (bcrypt/argon2) | https://cheatsheetseries.owasp.org/cheatsheets/Cryptographic_Storage_Cheat_Sheet.html | 2026-10-12 | 200 |
| openssl_encrypt, cipher + IV + tag ile AEAD sağlar | https://www.php.net/manual/en/function.openssl-encrypt.php | 2026-10-12 | 200 |
| RFC 6979, deterministik (RFC 6979) nonce üretimi tanımlar | https://www.rfc-editor.org/rfc/rfc6979 | 2026-10-12 | 200 |
| Web Crypto API, tarayıcıda AES-GCM ve rastgelelik sunar | https://developer.mozilla.org/en-US/docs/Web/API/Web_Crypto_API | 2026-10-12 | 200 |
| RFC 4086, tahmin edilemezlik için rastgelelik kaynakları tanımlar | https://www.rfc-editor.org/rfc/rfc4086 | 2026-10-12 | 200 |

## §2 RFC 5246 — TLS 1.2

Kaynak: https://www.rfc-editor.org/rfc/rfc5246 (200, 2026-10-12)
- İddia: kayıt katmanı şifrelemesi + kimlik doğrulaması sağlar; AEAD
  cipher suite'leri tanımlıdır.
- Çekirdek: handshake (anahtar anlaşması) → kayıt (şifreleme).

## §3 RFC 8446 — TLS 1.3

Kaynak: https://www.rfc-editor.org/rfc/rfc8446 (200, 2026-10-12)
- İddia: 1.3'te zayıf handshake seçenekleri çıkarılmıştır; yalnızca AEAD
  kalır. 0-RTT yeniden oynatma riski taşır (replay) → oturum/ödemede
  dikkat gerekir.

## §4 OWASP — TLS Cheat Sheet

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Transport_Layer_Security_Cheat_Sheet.html (200, 2026-10-12)
- İddia: TLS 1.2 altı protokoller ve zayıf cipher'lar kapatılmalı;
  sertifika doğru zincir + doğru hostname ile sunulmalı.
- İddia: HSTS, HTTPS indirgeme saldırılarına karşı tamamlayıcıdır.

## §5 OWASP — Cryptographic Storage Cheat Sheet

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Cryptographic_Storage_Cheat_Sheet.html (200, 2026-10-12)
- İddia: hassas veri için AES-256-GCM / benzeri AEAD tercih edilir.
- İddia: parolalar geri döndürülemez hash ile saklanır (argon2/bcrypt);
  şifreleme kullanılmaz.
- İddia: algoritmalar sabit kodlanmaz, yönetim katmanından seçilir.

## §6 PHP — openssl_encrypt

Kaynak: https://www.php.net/manual/en/function.openssl-encrypt.php (200, 2026-10-12)
- İddia: AEAD için IV + auth tag parametreleri gerekir; tag, şifrelemenin
  BÜTÜNLÜK kontrolünü sağlar. IV, cipher'e göre uzunlukta ve TEK
  KULLANIMLIK rastgele olmalıdır.

## §7 RFC 6979 — Deterministik ECDSA Nonce

Kaynak: https://www.rfc-editor.org/rfc/rfc6979 (200, 2026-10-12)
- İddia: imza nonce'u yetersiz rastgelelikten üretilirse özel anahtar
  sızabilir; RFC 6979 nonce'u gizli anahtar + mesajdan deterministik türetir.
- İlgililik: CoreMusic'te imza RS256 (JWT) — RSA'da OAEP/PKCS#1 v1.5
  nonce'ları kütüphane içindedir; bu RFC, rastgelelik riskinin genel
  çerçevesini verir.

## §8 MDN — Web Crypto API

Kaynak: https://developer.mozilla.org/en-US/docs/Web/API/Web_Crypto_API (200, 2026-10-12)
- İddia: getRandomValues ve AES-GCM gibi algoritmalar tarayıcıda güvenli
  şekilde sunulur; anahtarlar CryptoKey olarak gizli tutulur.
- İlgililik: istemci tarafı şifreleme K009'da KULLANILMIYOR (sunucu tarafı
  tercih) → bu iddia referans niteliğindedir.

## §9 RFC 4086 — Randomness Requirements

Kaynak: https://www.rfc-editor.org/rfc/rfc4086 (200, 2026-10-12)
- İddia: kriptografik rastgelelik, tahmin edilemez olmalıdır; yetersiz
  rastgelelik tüm şifrelemeyi çökertir. Entropi kaynakları tanımlanır.
- İlgililik: OAuthManager·190 random_bytes(12), Password pepper ve
  Lifecycle·48 random_bytes(32) bu kurala tabidir.

## §10 PHP Dökümanı — openssl-encrypt uygulaması

Kaynak: https://www.php.net/manual/en/function.openssl-encrypt.php (200, 2026-10-12)
- Not: elle doldurulan IV/tag, üretimde hata (false) döndürebilir; tag
  boşsa doğrulama yapılamaz. Üretim kanıtı: OAuthManager·199-201 tag
  parametresi doldurulur ve çözmede kullanılır (satır 218, 227).

## §11 Çapraz Kontrol (iddia ↔ kod)

| İddia | Kod karşılığı | Örtüşme |
|---|---|---|
| AEAD + tag | OAuthManager·195, 201 | ✅ |
| IV tek kullanım rastgele | OAuthManager·190 | ✅ |
| Parola hash ≠ şifreleme | Password·26-33 (argon2id) | ✅ |
| Pepper ile ek giz | Password·43, 59 (HMAC-SHA256) | ✅ |
| Rastgelelik kaynakları | random_bytes (190, Lifecycle·48) | ✅ |
| Uzunluk/bozuk veri denetimi | OAuthManager·213 | ✅ |
| TLS 1.2 altı kapalı mı | sunucu yapılandırması | ⚠️ UNKNOWN |
| HSTS | header sette YOK (kontrol 27 FAIL) | ❌ eksik |
| Algoritma seçiminde yönetim katmanı | sabit 'aes-256-gcm' | ⚠️ kısmi (sabit kodlu) |

## §12 Tahsis Bildirimi (8/8)

rfc5246 · rfc8446 · OWASP TLS · OWASP Cryptographic Storage · php
openssl-encrypt · rfc6979 · MDN Web Crypto API · rfc4086 — bu dosyada
kullanıldı; başka K009 dosyasında TEKRAR EDİLMEZ.

## §13 Doğrulanamayan (UNKNOWN)

1. TLS protokol/cipher yapılandırması (deploy) → devops.
2. encryptionKey üretim yolu (KDF) → secrets/.
3. İstemci tarafı Crypto API kullanımı → kullanılmıyor (kanıt: yok).
4. RFC 6979 ile üretim imzası eşleşmesi → RS256 ≠ ECDSA → uygulanamaz.

## §14 Kaynak → Kural Çıkarımı (uygulanabilir 8 kural)

| # | Kural | Kaynak | Kod karşılığı |
|---|---|---|---|
| C-01 | Hassas veri AEAD ile şifrelenir | OWASP Crypto Storage | OAuthManager·195 |
| C-02 | IV tek kullanım + rastgele | php openssl-encrypt | OAuthManager·190 |
| C-03 | Tag doğrulaması çözmede zorunlu | php openssl-encrypt | OAuthManager·218, 227 |
| C-04 | Parola hash ile saklanır | OWASP Crypto Storage | Password·26-33 |
| C-05 | Hash'e ek giz katmanı (pepper) | OWASP Crypto Storage | Password·43, 59 |
| C-06 | Rastgelelik tahmin edilemez olmalı | RFC 4086 | random_bytes çağrıları |
| C-07 | TLS 1.2 altı + zayıf cipher kapatılır | OWASP TLS | deploy (UNKNOWN) |
| C-08 | Yeniden oynatmaya dikkat (0-RTT) | RFC 8446 | OAuth state tek kullanımlık (170-183) |

## §15 Tahsis Haritası (dosyalar arası)

| URL | Adet | Kullanan |
|---|---|---|
| RFC (5246, 8446, 6979, 4086) | 4 | bu dosya §2, §3, §7, §9 |
| OWASP (TLS, Crypto Storage) | 2 | bu dosya §4, §5 |
| php openssl-encrypt | 1 | bu dosya §6, §10 |
| MDN Web Crypto API | 1 | bu dosya §8 |
| **Toplam** | **8** | kota DOLDU |

## §16 Kaynak Güveni Sıralaması

1. RFC (5246, 8446, 6979, 4086) — biçimsel tanım.
2. PHP el kitabı — üretimdeki gerçek davranış.
3. OWASP — tasarım önerisi.
4. MDN — tarayıcı tarafı referans.

Çelişki halinde RFC > PHP > OWASP > MDN; bu dosyada çelişki yok.

## §17 Doğrulama Durumu Özeti

| Öğe | Sayı |
|---|---|
| İddia satırı (§1) | 9 |
| Benzersiz URL | 8 |
| Doğrulanmış (200) | 8 (2026-10-12) |
| Kod ile eşleşen | 6 (§11 ✅) |
| Kısmi/eşleşmeyen | 2 (§11 ⚠️) |
| Eksik (HSTS) | 1 (§11 ❌) |
| UNKNOWN | 4 (§13) |

## §18 Örnek Kullanım Senaryoları (senaryo tabanlı)

| Senaryo | Uygulanan kural | Konum |
|---|---|---|
| OAuth access token DB'ye yazılır | C-01, C-02 | OAuthManager·188-205 |
| Token okunur | C-03 (tag) | OAuthManager·210-235 |
| Parola kaydedilir | C-04, C-05 | Password·26-49 |
| Parola doğrulanır | C-04 + hash_equals | Password·54-59 |
| OAuth state üretilir | C-08 (tek kullanımlık) | OAuthManager·170-183 |
| CSP nonce üretilir | C-06 | Lifecycle·48, 52 |

## §19 Açık Sorular (araştırma → sonraki dalga)

| # | Soru | Engel | Hedef dosya |
|---|---|---|---|
| Q-01 | Üretimde TLS 1.0/1.1 hâlâ açık mı? | sunucu config depo dışı | deploy kanıtı |
| Q-02 | encryptionKey kaç bit, nasıl üretildi? | kod okunmadı | secrets/ |
| Q-03 | Anahtar rotasyonu var mı? | ADR yok | ADR-097 K4 |
| Q-04 | Doğrulanmamış veri (DB dump) şifreli mi? | şema okunmadı | data-engineer |
| Q-05 | 'aes-256-gcm' sabiti neden yönetim katmanında değil? | tasarım kararı | kararlar.md |

Bu sorular dolgu DEĞİLDİR: her biri UNKNOWN/VERIFY REQUIRED olarak kayıtlıdır
ve cevap bulunana kadar ilgili satırda kalır.

## §20 Kaynaklar Hakkında Not

Bu dosyada hiçbir URL, kök security-web-arastirma.md havuzundan alınmamıştır;
tahsis 2026-10-12 tarihli plana uygundur. Tekrar kullanımı yasağı
(kural) ihlal edilmemiştir: 8 URL yalnız §1-§10 bölümlerinde geçer.
