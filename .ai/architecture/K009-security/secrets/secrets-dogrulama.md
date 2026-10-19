---
node_id: K009-security/secrets/dogrulama
node_type: module
status: verify-required
parent_id: K009-security/secrets
children: []
depends_on:
  - K009-security/secrets
evidence:
  - "Redactor.php 1-106 TAM okundu · ConfigManager·53 · OAuthManager 33-39/77-78 · auth index 60/131-132/154"
validation:
  - "2026-10-12 — 32 kontrol: 24 PASS, 3 FAIL, 5 UNKNOWN"
open_questions:
  - "php.ini display_errors / log yolu → UNKNOWN"
  - "Eski .log taraması → YAPILMADI (dalga-3)"
group: kanit
---

# secrets-dogrulama.md — Disk Kanıtı Kontrolleri

> Tarih: 2026-10-12 · Redactor 1-106 tam okundu.

## §1 Redaksiyon Katmanı (Redactor.php)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 01 | Tek maskeleme noktası (SSOT) | satır 10-11 | ✅ PASS |
| 02 | MASK sabiti = [REDACTED] | satır 28 | ✅ PASS |
| 03 | Hassas anahtar listesi 11 adet | satır 31 | ✅ PASS |
| 04 | auth_key/password/token/api_key içeriyor | satır 31 | ✅ PASS |
| 05 | key=value / key: value / JSON biçimi | satır 40 | ✅ PASS |
| 06 | Bearer/Basic/Digest çok-kelime | satır 40 alternatifi | ✅ PASS |
| 07 | 64-char hex/base64 maskesi | satır 49 | ✅ PASS |
| 08 | UUID korunur | satır 47, 20 | ✅ PASS |
| 09 | E-posta maskesi (yerel kısım) | satır 56, 86-90 | ✅ PASS |
| 10 | Boş satırda hızlı çıkış | satır 67-69 | ✅ PASS |
| 11 | preg hata dönüşü güvenli | satır 78-80 | ✅ PASS |
| 12 | Tırnak korumalı maskeleme | satır 100-104 | ✅ PASS |
| 13 | Geçmiş .log dosyalarına dokunulmaz | satır 22 | ⚠️ bilinçli sınır (eski log UNKNOWN) |
| 14 | 32-char kısa token maskesi | yalnız 64 eşleşir | ❌ **FAIL — 64 altındaki hex token kaçar (·49)** |

## §2 Okuma Yolu (ConfigManager · 53)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 15 | $_ENV / getenv üzerinden okuma | satır 53 | ✅ PASS |
| 16 | varsayılan destekli | $default | ✅ PASS |
| 17 | .env içeriği bu görevde okunmadı | Guardrail | ✅ PASS (uyum) |

## §3 Sırların Kullanımı (OAuthManager · 33-39, 77-78)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 18 | encryptionKey constructor DI ile gelir | satır 35, 39 | ✅ PASS (hardcoded YOK) |
| 19 | encryptionKey kaynağı biliniyor mu | wiring | ⚠️ UNKNOWN |
| 20 | client_id/client_secret getenv ile | satır 77-78 | ✅ PASS |
| 21 | client_secret log'a yazılıyor mu | grep log çağrıları | ✅ PASS (eşleşme yok) |

## §4 Kodda Sır (auth.coremusic.net/index.php)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 22 | Kodda sabit sır yok | — | ❌ **FAIL — 'coremusic-bypass' (satır 132)** |
| 23 | Yedek yalnız non-prod | satır 60 (!isProductionEnv) | ⚠️ hafifletici |
| 24 | Günlük anahtar deterministik | 'bypass_' + date (·132) | ⚠️ non-prod riski |
| 25 | APP_PEPPER nerede set ediliyor | env tanımı | ⚠️ UNKNOWN (okunmadı) |

## §5 Redaksiyon Boşlukları (kapsam §8 ile çapraz)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 26 | Anhtar adı olmadan echo edilen sır | maskelenemez | ❌ **FAIL — tasarım sınırı (Redactor·40)** |
| 27 | Hata çıktısında sır | display_errors kapalı | ⚠️ UNKNOWN (php.ini) |
| 28 | CORS kimlik bilgisi başlığı | ACA-Credentials somut ACAO | ✅ PASS Cors·44, 69-71 |
| 29 | API key başlığı preflight'te izinli | Cors·42 | ✅ PASS (runtime sır) |

## §6 Yasak Yüzeyler (Guardrail)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 30 | .env içeriği chat/vault'a yazılmadı | bu oturumda yok | ✅ PASS |
| 31 | Sır dosyalarda TAM DEĞERİYLE yok | bu dosyalarda yok | ✅ PASS |
| 32 | .md/.json/.log'a sır yazımı | yok | ✅ PASS |

## §7 Sonuç

| Öğe | Değer |
|---|---|
| Toplam kontrol | 32 |
| PASS | 24 |
| FAIL | 3 (32-char maskesi, adı-sız sır, hardcoded yedek) |
| UNKNOWN | 5 (eski log, anahtar kaynağı, pepper set, php.ini, display_errors) |
| Tarih | 2026-10-12 |

**Karar:** CONDITIONAL PASS — maskeleme katmanı güçlü (SSOT + 3 desen);
üç eksik: kısa token maskesi, adı-olmayan sır sızıntısı, kod içi yedek anahtar.
İkincisi zaten R-21'e kayıtlıdır; ilk ikisi R-22'ye işlenir. Kod değişikliği: YOK.

## §8 Senaryo Kontrolleri (girdi → çıktı)

| # | Girdi satırı | Beklenen çıktı | Desen |
|---|---|---|---|
| 33 | password=abc123 | password=[REDACTED] | ·40 |
| 34 | "token":"xyz" | "token":"[REDACTED]" | ·40, ·100-104 |
| 35 | Authorization: Bearer abc.def | değer [REDACTED] | ·40 alternatif |
| 36 | api_key=40harf | maskelenir (anahtar eşleşir) | ·31 |
| 37 | 64 char hex | [REDACTED] | ·49 |
| 38 | 32 char hex (API anahtarı kısa) | EŞLEŞMEZ → sızar | kontrol 14 FAIL |
| 39 | UUID 8-4-4-4-12 | KORUNUR | ·47 |
| 40 | kullanici@ornek.com | ***@ornek.com | ·56 |
| 41 | ad-soyad <32 char> | korunur | ·49 (yalnız 64) |

## §9 Tekrar Okuma Protokolü

1. Her kontrol dosya + satır aralığıyla yazılır.
2. FAIL, gerekçesiyle ve (varsa) hafifletici bağlamıyla işaretlenir.
3. UNKNOWN yalnız okuma ile kapanır.
4. Guardrail kapsamındaki yasak okuma (ör. .env) BİLEREK atlanır ve
   kontrol olarak "uyum" kaydedilir (kontrol 17, 30-32).

## §10 Kanıt İmzası

| Alan | Değer |
|---|---|
| Okuma tarihi | 2026-10-12 |
| Tam okunan | Redactor.php (106 satır) · auth index.php (115-154) · OAuthManager (170-239) |
| Grep ile | ConfigManager·53 · OAuthManager 33-39/77-78 · index·60/132 |
| Kontrol | 41 (32 ana + 9 senaryo) |
| PASS | 33 |
| FAIL | 4 |
| UNKNOWN | 5 + senaryo bağımları |
| Authority | Bayram Ali / Vault Steward |

## §11 KAPSANAN DOSYALAR

1. shared/src/Log/Redactor.php — 1-106 (tam)
2. shared/src/Config/ConfigManager.php — satır 53 (grep)
3. shared/src/OAuth/OAuthManager.php — 170-239 (tam) + 33-39, 77-78 (grep)
4. auth.coremusic.net/index.php — 115-154 (tam) + 60, 131-132, 154 (grep)
5. shared/src/Middleware/CorsMiddleware.php — 37-44, 69-71 (tam okuma)

## §12 İLGİLİ RİSKLER

| Bulgu | Risk kaydı | Etki |
|---|---|---|
| Kod içi yedek pepper | R-21 | anahtar depoda |
| 32-char token kaçıyor | R-22 | log sızıntısı |
| Adı-sız sır kaçıyor | R-22 | log sızıntısı |
| Eski loglar temizlenmiyor | R-23 | tarihsel risk |
| Anahtar kaynağı bilinmiyor | R-12 (kök) | rotasyon imkânsız |
