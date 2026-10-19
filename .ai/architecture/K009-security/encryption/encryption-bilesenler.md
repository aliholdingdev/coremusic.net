---
node_id: K009-security/encryption/bilesenler
node_type: module
status: verify-required
parent_id: K009-security/encryption
children: []
depends_on:
  - K009-security/encryption
evidence:
  - "OAuthManager (encrypt/decrypt/validateState) + Password + SecurityHeadersMiddleware disk kanıtı"
validation:
  - "2026-10-12 — glob ile OAuth/Provider listesi doğrulandı (12 provider)"
open_questions:
  - "encryptionKey üreten sınıf → UNKNOWN (bulunmadı)"
  - "Anahtar rotasyonu ADR'si yok → ADR-097 K4"
group: mimari
---

# encryption-bilesenler.md — Kademe-3 Planı

## §1 Kademe-3 Bileşen Listesi

| # | Bileşen | Dosya | Görev |
|---|---|---|---|
| E-01 | TokenŞifreleyici | shared/src/OAuth/OAuthManager.php (encrypt/decrypt) | AES-256-GCM |
| E-02 | ParolaDeğeri | auth.coremusic.net/include/Domain/ValueObject/Password.php | argon2id + pepper |
| E-03 | TaşımaBaşlıkları | shared/src/Middleware/SecurityHeadersMiddleware.php | HSTS/COOP/CSP |
| E-04 | BypassAnahtarı | auth.coremusic.net/index.php·132 | dev kısayolu (izleme altında) |
| E-05 | AnahtarKaynağı | **YOK — boşluk** | encryptionKey üretimi |
| E-06 | TLS Yapılandırması | **DEPO DIŞI** | sunucu/sertifika |

## §2 Kullanım Bağlantıları

~~~text
OAuth token kaydedilir → E-01::encrypt (OAuthManager·188)
OAuth token okunur     → E-01::decrypt (·210)
Parola kaydedilir      → E-02::fromRaw (Password·26-49)
Parola doğrulanır      → E-02::verify  (·54-59)
Her yanıt              → E-03 başlıkları (·26-41)
Dev bypass             → E-04 (yalnız non-prod, index·60)
~~~

## §3 Boşluklar (kademe-3 iş kalemleri)

| Boşluk | Etki | Öncelik | Hedef |
|---|---|---|---|
| E-05 anahtar kaynağı | tüm şifrelemenin temeli | P0 | secrets/ + ADR |
| E-06 TLS yapılandırması | taşıma güvenliği | P1 | deploy |
| HSTS başlığı eksik | SSL stripping | P1 | SecurityHeaders |
| E-04 yedek sabit anahtar | anahtarın depoda olması | P2 | index.php·132 |

## §4 Dosya Haritası (kademe-4)

~~~text
shared/src/OAuth/
  OAuthManager.php            (170-235: state, encrypt, decrypt)
  Provider/BaseOAuthProvider.php (token akışı — okunmadı)
auth.coremusic.net/include/Domain/ValueObject/
  Password.php                (16, 24, 26-33, 43, 49, 54-59)
auth.coremusic.net/index.php  (60, 129-137, 154)
shared/src/Middleware/
  SecurityHeadersMiddleware.php (26-41, 48-74)
~~~

## §5 Bağımlılık Tablosu

| Kaynak | Hedef | Tür | Not |
|---|---|---|---|
| OAuthManager::encrypt | encryptionKey | gizli girdi | kaynak UNKNOWN |
| OAuthManager | OpenSSL (php ext) | platform | aes-256-gcm |
| Password | password_* (php) | platform | argon2id |
| Password | pepper kaynağı | dosya/env | secrets/ devri |
| SecurityHeaders | DomainConfig::isHttps() | config | koşullu başlık |
| E-04 | APP_PEPPER / FORCE_AUTH_BYPASS | env | non-prod |

## §6 Katman Kuralı

- Şifreleme **Infrastructure** katmanındadır; Domain yalnız Password gibi
  değer nesnesiyle temsil edilir (dış bağımlılık YOK — katman kuralı).
- Algoritma adı ('aes-256-gcm') sabit kodlu → OWASP "yönetim katmanından
  seç" önerisine KISMEN UYUM (web §5, §11) — iyileştirme adayı.
- Anahdar (key) asla log'a/vault md'ye yazılmaz (kural).

## §7 Kademe-4 Alt Birimleri

| Bileşen | Alt birim |
|---|---|
| E-01 | encrypt / decrypt / sarma biçimi (iv‖tag‖ct) |
| E-02 | fromRaw / hashWithPepper / verify / needsRehash |
| E-03 | headerSet / buildCsp / isHttps koşulları |
| E-05 | KeyProvider (üretim, saklama, rotasyon) → YENİ |

## §8 Test Yüzeyi Matrisi

| Bileşen | Test türü | Senaryo (doğrulama §8) |
|---|---|---|
| E-01 | unit | 31, 32, 33 |
| E-02 | unit | 34, 35, 36 |
| E-03 | unit + header | 38, kontrol 20-21 |
| E-04 | unit (non-prod) | kontrol 24-25 |
| E-05 | — | test yok (boşluk) |

## §9 Değişiklik Riski

| Dosya | Risk | Gerekçe |
|---|---|---|
| OAuthManager encrypt/decrypt | Yüksek | veri kaybı/okunamaz token |
| Password | Yüksek | hesap güvenliği |
| SecurityHeaders | Orta | başlık ekleme geri dönüşü kolay |
| index.php bypass | Orta | yalnız dev |

Kural: bu dosyalarda değişiklik → security-audit workflow (shared AGENTS.md §3).

## §10 Tahmini İş Yükü (dalga-3)

| # | İş | Tahmin | Bağımlılık |
|---|---|---|---|
| 1 | E-05 KeyProvider tasarımı (KDF + saklama) | 1 ADR + 1 dosya | secrets/ |
| 2 | HSTS başlığı ekleme (şartlı: HTTPS) | 1 satır + test | E-03 |
| 3 | E-04 yedek anahtarın kaldırılması | 1 satır + fail-closed | env denetimi |
| 4 | Algoritma adını config'e taşıma | 1 config + 2 çağrı | kararlar.md |
| 5 | TLS/cipher deploy kanıtı toplama | ortam okuma | devops |

Toplam 5 iş; 1 ve 5 ADR/çevre gerektirdiği için bu oturumda YAPILMAZ.
