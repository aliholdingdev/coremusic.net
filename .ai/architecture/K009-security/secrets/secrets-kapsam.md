---
node_id: K009-security/secrets/kapsam
node_type: module
status: verify-required
parent_id: K009-security/secrets
children: []
depends_on:
  - K009-security/secrets
evidence:
  - "Redactor.php 28-105 · ConfigManager.php 53 · OAuthManager 33-39, 77-78 · auth index.php 132"
validation:
  - "2026-10-12 — her § satır aralığına bağlandı"
open_questions:
  - ".env içeriği Guardrail nedeniyle OKUNMADI"
group: mimari
---

# secrets-kapsam.md — Sırlar Yönetimi Kapsamı

## §1 Bilinen Sırlar Envanteri (kod kanıtlı)

| Sır | Kullanım yeri | Kanıt | Saklama |
|---|---|---|---|
| APP_PEPPER | auth index.php·132 (HMAC) | grep | env — kodda yedek VAR |
| encryptionKey | OAuthManager·33-39, 196, 224 | constructor DI | kaynak UNKNOWN |
| OAuth client_secret | OAuthManager·77-78 getenv | grep | env |
| API key başlığı | CorsMiddleware·42 (X-Api-Key izni) | okundu | runtime |
| JWT RS256 özel anahtarı | JwtService (authentication/) | özet | UNKNOWN |
| session_id / token | Redactor·31 maskelenir | okundu | runtime |

Envanter kuralı: kodda geçen HER gizli anahtar bu tabloya girer; girmeyen
yeni anahtar denetimde "sır sızıntısı" sayılır.

## §2 Okuma Yolu (ConfigManager · 53)

~~~php
return $_ENV[$key] ?? getenv($key) ?: $default;
~~~

Kural: sırlar yalnız env üzerinden okunur; .env içeriği Guardrail gereği bu
görevde OKUNMAZ ve hiçbir .md / .json / .log içine YAZILMAZ (kural).

## §3 Redaksiyon (Redactor · 28-56)

| Kural | Konum | Ne maskeler |
|---|---|---|
| MASK = [REDACTED] | satır 28 | ortak değer |
| SENSITIVE_KEYS | satır 31 | auth_key, password, passwd, csrf_token, token, secret, api_key, session_id, session, authorization, cookie |
| key=value deseni | satır 40 | tırnaklı / Bearer / Basic biçimleri |
| 64-char hash | satır 49 | hex/base64 (UUID hariç, satır 47) |
| e-posta | satır 56 | yerel kısım ***, alan kalır |

Kapsam sınırı: maskeleme YAZILAN satırı etkiler, geçmiş .log dosyalarına
dokunulmaz (satır 22).

## §4 Yaşam Döngüsü

1. Üretim — rastgelelik (openssl_random_pseudo-bytes / random_bytes).
2. Saklama — env veya dosya (dizin izinleri UNKNOWN → dalga-3).
3. Kullanım — yalnız kod içinde; log'a yazma yasağı.
4. Maskelene — log yolunda Redactor (tek nokta, satır 10-11).
5. Emeklilik — rotasyon/revokasyon → ADR YOK (UNKNOWN).

## §5 Kapsam Dışı (bu düğüm DEĞİL)

1. Algoritma (AES/argon2) seçimi → encryption/.
2. Cookie bayrakları → session-security/.
3. Backup / HSM / sır kasası → depo dışı, dalga-3.
4. .env içeriği okuma → Guardrail yasak (auth AGENTS.md §4 Yasak 1).

## §6 Bilinen Zayıf Noktalar (kanıtlanan)

| # | Bulgu | Kanıt | Etki |
|---|---|---|---|
| Z-1 | APP_PEPPER yedeği koda sabit | auth index.php·132 | anahtar depoda |
| Z-2 | encryptionKey kaynağı bilinmiyor | OAuthManager·35 (DI) | üretim/rotasyon doğrulanamıyor |
| Z-3 | Günlük bypass anahtarı deterministik | index.php·132 | non-prod'da herkes üretebilir |
| Z-4 | Redaksiyon yalnız yazım anında | Redactor·22 | eski loglar temizlenmez |

## §7 Kabul Kriterleri

1. Envanter tablosu (§1) kod okumasıyla güncellenir; tahmin yok.
2. Her sır için OKUMA yolu ve MASKELLEME kanıtı bulunmalı.
3. Bilinmeyen saklama yeri UNKNOWN kalır; "güvenli varsayılır" yazılmaz.
4. Sır asla bu dosyalarda TAM DEĞERİYLE yazılmaz.

## §8 Redaksiyon Kapsamı Analizi (neyi KAÇIRIR)

| Veri türü | Yakalanıyor mu | Kanıt |
|---|---|---|
| password=x / "password":"x" | ✅ | Redactor·40 |
| Authorization: Bearer token | ✅ | desen alternatifi (·40) |
| 64 hex/base64 hash | ✅ | ·49 |
| E-posta | ✅ kısmi (alan kalır) | ·56 |
| UUID | ❌ bilinçli HARİÇ | ·47, ·20 |
| 32-char (64 değil) kısa token | ❌ eşleşmez | ·49 (64 zorunlu) |
| Doğrudan echo edilmiş sır (anahtar adı olmadan) | ❌ | yalnız key=value biçimleri |

Son iki satır REDAKSİYON BOŞLUĞUDUR → doğrulama.md §5'te kontrol edilir.

## §9 Sır ≠ Hassas Veri Ayrımı

| | Sır | Hassas veri (PII) |
|---|---|---|
| Örnek | pepper, client_secret | e-posta, parola hash'i |
| Kural | asla log'a/vault'a | maskelebilir (·56) |
| Saklama | env/dosya | DB |
| Dönüş | döndürülemez (revokasyon) | erişim politikası |

## §10 İlişkili Düğümler ve Devir

| Konu | Giden düğüm | Gerekçe |
|---|---|---|
| encryptionKey kullanımı | encryption/ | algoritma tarafı |
| Pepper ile hash akışı | authentication/ | parola akımı |
| RS256 özel anahtarı | authentication/ (JwtService) | imza |
| Log politikası bütünü | K009 kök kod-ornekleri | redaksiyon ortak |

## §11 Özet Karar Tablosu

| # | Karar | Kanıt | Durum |
|---|---|---|---|
| S-01 | Sırlar yalnız env'den okunur | ConfigManager·53 | aktif |
| S-02 | Tek maskeleme noktası | Redactor·10-11 | aktif |
| S-03 | 11 anahtar + hash + e-posta | Redactor·31, 49, 56 | aktif |
| S-04 | Geçmiş loglar temizlenmez | Redactor·22 | bilinçli sınır |
| S-05 | .env okunmaz/yazılmaz | Guardrail | kural |
| S-06 | kodda yedek sır YASAK | OWASP Secrets | ihlal var (Z-1) |
| S-07 | Rotasyon/revokasyon yok | ADR yok | UNKNOWN |
| S-08 | Sır depoda asla tam yazılmaz | bu dosya kuralı | kural |

## §12 SECRET YAŞAM DÖNGÜSÜ VE SIZINTI YÜZEYLERİ (2026-10-18 — APPEND-ONLY)

> §1-§11'i silmez; S-01..S-08 karar tablosunu (§11) tamamlar. Yeni kod kanıtı yok — sentez.

### 12.1 Secret Yaşam Döngüsü Matrisi

| Aşama | Kural | Sınıf (kapsam §14) | S-01..S-08 karşılığı |
|---|---|---|---|
| Üretim | `random_bytes` / CSPRNG; elle yazılmış sabit ("secret123") YASAK | S3 | S-06 |
| Dağıtım | yalnız env / secrets manager; YAML'a gömme yasak | S3 | S-01 |
| Kullanım | yalnızca ilgili bileşen okur; başka katmana kopyalanmaz (en az yetki) | S3 | S-01 |
| Log/redaksiyon | tek maskeleme noktası; secret değeri log'a GİRMEZ | S3 | S-02, S-03 |
| Rotasyon | periyodik + olay tetikli; yeni üret → dağıt → eskisini iptal | S3 | S-07 (yok → UNKNOWN) |
| İptal (revocation) | eski secret anında reddedilir; ikili yayında grace period | S3 | S-07 (yok) |
| Saklama süresi | git history'ye girdiyse rotasyon ZORUNLU | S3 | S-06 (ihlal Z-1) |
| Sızıntı müdahalesi | tespit → REDDET → rotasyon → etki analizi (kapsam §20 protokolü) | S3 | S-08 |

### 12.2 Sızıntı Yüzeyleri (hangi kanaldan secret çıkar)

| # | Yüzey | Savunma | Durum |
|---|---|---|---|
| 1 | Git geçmişi (commit edilmiş .env/sür) | .gitignore + CI taraması (gitleaks) | S-06 ihlali Z-1 açık |
| 2 | Log dosyaları (debug dump, exception) | Redactor + [REDACTED] | S-02/S-03 aktif |
| 3 | Hata yanıt gövdesi (stack trace) | genel mesaj + RFC 9457 | master §23 |
| 4 | CI değişken sızıntısı (echo $SECRET) | min permission + maskeleme | security-kod-ornekleri §20.16 |
| 5 | Bellek dump / swap | S1 bellek temizliği kuralı | kısmi |
| 6 | Yedek / eski kopya | yedek şifreleme | K17-devops |
| 7 | İnsan hatası (screenshot, e-posta) | politika | K020-policies |

### 12.3 Rotasyon Protokolü (S-07 kapandığında uygulanacak)

| Adım | Aksiyon | Onay |
|---|---|---|
| 1 | Tetikleyici: periyot veya olay (sızıntı, personel değişimi) | Vault Steward |
| 2 | Yeni secret üret (CSPRNG) + secrets manager'a yaz | DevOps |
| 3 | Uygulamaya dağıt; ikili yayında eski+yeni geçerli | DevOps |
| 4 | Eskisini İPTAL et | DevOps |
| 5 | Etki analizi: eski secret'ın eriştiği kayıtlar | Security |
| 6 | Kapatma kanıtı: tarih + sahip + yöntem; kanıtsız kapatma YASAK | Vault Steward |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
