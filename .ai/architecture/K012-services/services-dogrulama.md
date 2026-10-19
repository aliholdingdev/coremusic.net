---
node_id: K012-services
node_type: module
role: dogrulama
status: draft
parent_id: K012-services
children: []
depends_on:
  - "services-master.md"
  - "services-bagimliliklar.md"
  - "index.md"
evidence:
  - "S1 — repo kod kanıtı (19 satır, Dosya+Satır, 2026-10-13)"
  - "S3 — 15 resmi URL HTTP 200 (2026-10-13)"
  - "S5 — yokluk kanıtı (4 iddia, §7)"
validation:
  - "tur-1 — 2026-10-12 iskelet kontrolü"
  - "tur-2 — 2026-10-13 derinleştirme + URL/kod kanıtı (§17)"
open_questions:
  - "Q1 ai-service bağlılığı K006 vs K016 — UNKNOWN"
  - "Q2 playback-service sahipliği — UNKNOWN"
  - "Q3 audio-service sınırı (servis mi motor mu) — UNKNOWN"
  - "Q4 service-contracts kapsamı — UNKNOWN"
  - "Q5 background-jobs iş motoru — UNKNOWN"
  - "Q6 device-identity sahipliği — UNKNOWN"
  - "Q7 BC/DB ilişkisi — tasarım-seviyesi"
  - "Q8 swagger/PSR-7/PSR-15 otomasyonu — tasarım-seviyesi"
  - "Q9 servisler arası çağrının gerçek taşıyıcısı — UNKNOWN"
group: kanit
---

# K012-services — services-dogrulama.md

> Bu belge **kanıt defteridir**: neyin doğrulandığı, neyin beklemede olduğu.
> Doğrulanmayan hiçbir iddia "verified" sayılmaz. Sürüm v2.0.0 · 2026-10-13.

## §1 Amaç ve Kapsam

| Madde | Değer |
|---|---|
| Dosya | `K012-services/services-dogrulama.md` |
| Rol | kanıt defteri — doğrulama sonuçlarının tek kayıt yeri |
| Kapsam | K012-services kök dosyaları + 10 alt alan kanıt durumu |
| Kapsam dışı | K010-network, K014-media (sıradaki kökler), kaynak kod değişikliği |
| Yöntem | kanıt sınıfı S1–S5 (§2) + kriter tablosu (§3) + açık sorular (§4) |
| Tarih | 2026-10-13 (tur-2) |
| Otorite | Vault Steward — Red Team · Human Mode · Truth Mode |

Doğrulama iki düzlemde yürür: (a) **repo kanıtı** — gerçek dosya + satır numarası;
(b) **kaynak kanıtı** — resmi kurum URL'si + HTTP 200 + tarih. İkisi de olmayan
her iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` olarak kalır; tahmin yazılmaz.

## §2 Kanıt Sınıfları (S1–S5)

| Sınıf | Ad | Ne demek | Güç |
|---|---|---|---|
| S1 | repo kod kanıtı | dosya yolu + satır numarası gerçek ve okunmuş | en güçlü |
| S2 | vault iddia kanıtı | vault'ta yazılmış iddia; `TASARIM İDDİASI` etiketli olabilir | orta |
| S3 | URL 200 kanıtı | resmi kaynak URL'si HTTP 200 döndü (tarihli) | güçlü |
| S4 | mantıksal türetme | dayanaktan tek adımda türetilen sonuç | yardımcı |
| S5 | yokluk kanıtı | arama 0 isabet → "repo'da YOK" | sınırlı |

Kural: **S2 tek başına asla `doğrulanmış` sonucu üretmez**; S4 dayanaksız yazılmaz;
S5 yalnız "repo'da yok" der, "olmamalı" demez. Her dosyadaki her iddia bir sınıfa
oturtulur; sınıfı belirsiz iddia `UNKNOWN` sayılır.

## §3 15 Mimari Kriter — Doğrulama Durumu

| # | Kriter | Dayanak | Durum |
|---|---|---|---|
| K1 | sürücü veri modeli | S1 `ServiceDefinition.php:79` | ✅ doğrulanmış |
| K2 | otonomi / bölmeler | S2 §kapsam dosyaları | 🟡 tasarım-seviyesi |
| K3 | BC/DB ilişkisi | S5 repo araması | ⚠️ UNKNOWN (Q7) |
| K4 | Service/API sözleşmeleri | S1 `ServiceRegistry.php` + `VersionResolver.php` | ✅ doğrulanmış |
| K5 | tek uç nokta / router yönlendirme | S1 `.htaccess:26` + `web.config:14` | ✅ doğrulanmış |
| K6 | RESTful'ı önceliklendirme | S1 `.htaccess:26` + `web.config:14` | ✅ doğrulanmış |
| K7 | JSON kabuk + idempotent | S5 | ⚠️ UNKNOWN (kanıt yok) |
| K8 | Stream/Video API | S1 `MusicStreamHandler.php:24/46/91` | ✅ doğrulanmış |
| K9 | WebSocket / real-time | S3 `w3.org/TR/websockets/` + `rfc8441` | 🟡 tasarım-seviyesi (repo kodu YOK) |
| K10 | ECID / oturum anahtarı | S5 | ⚠️ UNKNOWN (kanıt yok) |
| K11 | cihaz servisleri dikey kestirim | S2 `device-service/` | 🟡 tasarım-seviyesi |
| K12 | ayrık ortam/kimlik/izleme | S1 `RateLimitMiddleware.php:34` + `ServiceHealth.php:44` | 🟡 kısmen-doğrulanmış |
| K13 | sürümler + bağımlılık kesme | S1 `VersionResolver.php:57` + `VersionRegistry.php:96` | ✅ doğrulanmış |
| K14 | API ağ geçidi yönlendirmesi | S1 `.htaccess:26` + `web.config:14` | ✅ doğrulanmış |
| K15 | kirli iş dış kaynak | S2 `background-jobs/` | 🟡 tasarım-seviyesi (motor Q5) |

Özet: 6 ✅ · 5 🟡 · 4 ⚠️ — kriter tablosu 15 satır, §21'de prosedürleri vardır.

## §4 Açık Sorular (open_questions — 9)

| # | Soru | Etkilenen alan | Durum |
|---|---|---|---|
| Q1 | ai-service bağlılığı K006-yapay-zeka mı K015-AI mı | alt alan sahipliği | UNKNOWN |
| Q2 | playback-service sahipliği (K015 playback ile sınır) | playback odak kapsamı | UNKNOWN |
| Q3 | audio-service sınırı — servis mi, motor mu (K003-audio) | sınır iddiaları | UNKNOWN |
| Q4 | service-contracts kapsamı (tüm K'lar mı, K012 mi) | sözleşme kapsamı | UNKNOWN |
| Q5 | background-jobs iş motoru (queue kütüphanesi bilinmiyor) | iş motoru iddiası | UNKNOWN |
| Q6 | device-identity sahipliği (K0100-identitiy yazımı korunur) | cihaz kimlik iddiası | UNKNOWN |
| Q7 | BC/DB ilişkisi — hangi BC hangi DB'ye bağlı | DB iddiaları | tasarım-seviyesi |
| Q8 | swagger / PSR-7 / PSR-15 otomasyonu repo'da var mı | sözleşme otomasyonu | tasarım-seviyesi |
| Q9 | servisler arası çağrının gerçek taşıyıcısı (HTTP? CLI? queue?) | transport iddiası | UNKNOWN |

Bu 9 soru kapanana kadar ilgili tüm satırlar `UNKNOWN` tutulur; cevap uydurulmaz.

## §5 Kullanılan 15 Resmi URL (HTTP 200 — 2026-10-13)

| # | URL | Kurum | Durum |
|---|---|---|---|
| 1 | `https://12factor.net/` | 12factor | 200 |
| 2 | `https://www.cncf.io/` | CNCF | 200 |
| 3 | `https://www.rfc-editor.org/rfc/rfc9110` | RFC Editor | 200 |
| 4 | `https://www.rfc-editor.org/rfc/rfc9112` | RFC Editor | 200 |
| 5 | `https://www.rfc-editor.org/rfc/rfc8446` | RFC Editor | 200 |
| 6 | `https://www.rfc-editor.org/rfc/rfc7838` | RFC Editor | 200 |
| 7 | `https://www.rfc-editor.org/rfc/rfc8441` | RFC Editor | 200 |
| 8 | `https://www.rfc-editor.org/rfc/rfc6585` | RFC Editor | 200 |
| 9 | `https://www.rfc-editor.org/rfc/rfc7540` | RFC Editor | 200 |
| 10 | `https://www.rfc-editor.org/rfc/rfc7230` | RFC Editor | 200 |
| 11 | `https://www.rfc-editor.org/rfc/rfc5321` | RFC Editor | 200 |
| 12 | `https://datatracker.ietf.org/doc/html/rfc9110` | IETF | 200 |
| 13 | `https://www.w3.org/TR/websockets/` | W3C | 200 |
| 14 | `https://www.w3.org/TR/media-capabilities/` | W3C | 200 |
| 15 | `https://www.ietf.org/` | IETF | 200 |

Kayıt biçimi: `| İddia | URL | 2026-10-13 | Durum |`. Bu 15 URL'nin 2.929 URL'lik
envanterle kesişimi **0**'dır (§16). Başarısız 7 URL §10'da listelenir.

## §6 Repo Kod Kanıtı — 19 Satır (S1, 2026-10-13)

| # | Dosya | Satır | İddia |
|---|---|---|---|
| 1 | `shared/src/Api/Registry/ServiceRegistry.php` | 19 | kayıt defteri bellek-içi diziler |
| 2 | `shared/src/Api/Registry/ServiceRegistry.php` | 26 | `register()` servis kaydı |
| 3 | `shared/src/Api/Registry/ServiceRegistry.php` | 38 | `discover()` ad ile servis bulma |
| 4 | `shared/src/Api/Registry/ServiceRegistry.php` | 52 | `healthCheck()` sağlık sorgusu |
| 5 | `shared/src/Api/Registry/ServiceRegistry.php` | 97 | `clear()` defterin boşaltılması |
| 6 | `shared/src/Api/Registry/ServiceDefinition.php` | 19 | VO tanımı |
| 7 | `shared/src/Api/Registry/ServiceDefinition.php` | 79 | `timeout=5`, `version='1.0.0'`, `healthy=true` |
| 8 | `shared/src/Api/Registry/ServiceHealth.php` | 34 | `overallStatus` toplu sağlık |
| 9 | `shared/src/Api/Registry/ServiceHealth.php` | 44 | `hasFailures()` hata varlığı |
| 10 | `shared/src/Api/Versioning/VersionResolver.php` | 24 | sınıf tanımı |
| 11 | `shared/src/Api/Versioning/VersionResolver.php` | 57 | `X-API-Version` başlığından sürüm |
| 12 | `shared/src/Api/Versioning/VersionRegistry.php` | 24 | sınıf tanımı |
| 13 | `shared/src/Api/Versioning/VersionRegistry.php` | 96 | `add()` sürüm kaydı |
| 14 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 22 | sınıf tanımı |
| 15 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 34 | 60 istek / 60 saniye penceresi |
| 16 | `home.coremusic.net/.htaccess` | 26 | Apache rewrite yönlendirme |
| 17 | `home.coremusic.net/web.config` | 14 | IIS rewrite karşılığı |
| 18 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 46 | 1 MiB chunk |
| 19 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 91 | Range → 206/416 |

Tüm satırlar 2026-10-13'te dosya üzerinden okunarak teyit edildi (S1).

## §7 Yokluk İddiaları (S5 — 4 satır)

| # | İddia | Arama kapsamı | Sonuç |
|---|---|---|---|
| 1 | `*Service.php` alt alan implementasyonları K012'de YOK | `.ai/architecture/K012-services/` | 0 isabet — "repo'da kod YOK" |
| 2 | WebSocket taşıma kodu repo'da YOK | repo geneli | 0 isabet — K9 tasarım-seviyesi |
| 3 | background-jobs iş motoru kaynağı YOK | `.ai/architecture/K012-services/background-jobs/` | 0 isabet — Q5 `UNKNOWN` |
| 4 | ai-service implementasyon dosyası YOK | `.ai/architecture/K012-services/ai-service/` | 0 isabet — Q1 `UNKNOWN` |

S5 kuralı: yokluk "yanlıştır" anlamı taşımaz; yalnızca kanıtın bulunmadığını söyler.

## §8 Doğrulama Geçmişi

| Tur | Tarih | Kapsam | Sonuç |
|---|---|---|---|
| tur-1 | 2026-10-12 | ilk iskelet (v1.0.0, 87 satır) | iskelet kabul; S3 kanıtı yok |
| tur-2 | 2026-10-13 | derinleştirme (v2.0.0) — 19 S1 + 15 S3 + 4 S5 eklendi | bu dosya |
| tur-2b | 2026-10-16 | web araştırması 4 dosya (kök Q1–Q9 + playback/background-jobs/ai) · services-ornekler §28 (ServiceRegistry verbatim) · C4 kardeş URL tekilliği taraması (132 URL → çakışma 0) | 4 dosya status: draft; Q1–Q9 **hâlâ açık** — yalnız seçenek matrisi sunuldu |
| tur-3 | planlanmadı | açık sorular Q1–Q9 kapanışı | bekliyor |

## §9 Doğrulama Yöntemi — Kanıt Sınıfları Ayrıntısı

### §9.1 S1 — Repo Kod Kanıtı (en güçlü)

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | Dosya yolu repo içinde bulunur | gerçek path |
| 2 | Satır numarası `read` ile teyit edilir | Satır: N |
| 3 | İçerik iddiayla birebir eşleşir | MATCH |
| 4 | Tabloya `Dosya:` + `Satır:` ile yazılır | S1 |

S1 kanıtı tek başına "bu davranış repo'da mevcuttur" der; "doğru çalışır" demez — çalışırlık S3'tür.

### §9.2 S2 — Vault İddia Kanıtı

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | Vault dosyasında iddia satırı bulunur | Dosya + § |
| 2 | İddia `TASARIM İDDİASI` etiketli mi kontrol | etiket |
| 3 | Karşı kanıt (S1/S4) aranır | var/yok |
| 4 | Karşı kanıt yoksa durum `tasarım-seviyesi` | S2 |

S2 tek başına asla `doğrulanmış` sonucu üretmez.

### §9.3 S3 — HTTP 200 URL Kanıtı

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | URL envanteri (2.929 adet) dışındakiler denenir | yeni URL |
| 2 | GET isteği atılır | status code |
| 3 | 200 ise tarih + URL kaydedilir | `| İddia | URL | 2026-10-13 | 200 |` |
| 4 | 200 değilse tabloya yazılmaz; 9 başarısız kayıt §10'da | — |

S3 kaynağı resmi kurum olmak zorundadır (RFC Editor, W3C, IETF, CNCF, 12factor).

### §9.4 S4 — Mantıksal Türetme Kanıtı

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | dayanak S1/S2/S3 seçilir | dayanak |
| 2 | türetme adımı açıkça yazılır | "…çünkü…" |
| 3 | türetme tek adımda mı kontrol | tek/adım |
| 4 | sonuç `türetme` sınıfıyla etiketlenir | S4 |

S4 çok adımlıysa ara adımlar tek tek satır yapılır; zincir koparsa sonuç `UNKNOWN`.

### §9.5 S5 — Yokluk Kanıtı

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | iddia edilen şey aranır (glob/grep) | 0 isabet |
| 2 | arama kapsamı yazılır (hangi dizin) | kapsam |
| 3 | `Repo'da … YOK` cümlesi kurulur | yokluk |
| 4 | "demek ki yanlış" sonucu ÇIKARILMAZ | — |

S5 yalnızca "repo'da yok" der; "olmamalı" demez.

## §10 Başarısız URL Kayıtları (2026-10-13)

| URL | Amaç | Sonuç | Karar |
|---|---|---|---|
| `https://12factor.net/setup/` | 12factor setup bölümü | başarısız | kullanılmadı |
| `https://12factor.net/build/` | 12factor build bölümü | başarısız | kullanılmadı |
| `https://12factor.net/dependencies/` | bağımlılık bölümü | başarısız | kullanılmadı |
| `https://12factor.net/disposability/` | açılabilirlik bölümü | başarısız | kullanılmadı |
| `https://tech.ebu.ch/publications/r128` | EBU R128 loudness | başarısız | kullanılmadı |
| `https://www.ozoneproject.io/` | Ozone proje sayfası | başarısız | kullanılmadı |
| `https://ozoneproject.io/` | Ozone proje sayfası (www'sız) | başarısız | kullanılmadı |

Başarısız URL'ler hiçbir dosyada `200` olarak gösterilmez. EBU R128 ve Ozone iddiaları bu nedenle `⚠️ VERIFICATION REQUIRED` statüsündedir.

## §11 Kullanılan 15 Resmi Kaynak — Notlar

### §11.1 IETF / RFC Editor

| # | URL | K012'deki rolü | Not |
|---|---|---|---|
| 1 | `https://www.rfc-editor.org/rfc/rfc5321` | SMTP — e-posta servis protokolü | SMTP submit/relay kuralları |
| 2 | `https://www.rfc-editor.org/rfc/rfc9110` | HTTP semantiği — servis arayüzü | method/status genel çerçevesi |
| 3 | `https://www.rfc-editor.org/rfc/rfc9112` | HTTP/1.1 mesaj formatı | keep-alive, chunked |
| 4 | `https://www.rfc-editor.org/rfc/rfc8446` | TLS 1.3 — servisler arası kanal güvenliği | handshake |
| 5 | `https://www.rfc-editor.org/rfc/rfc7838` | HTTP Alternative Services | servis taşıma katmanı alternatifi |
| 6 | `https://www.rfc-editor.org/rfc/rfc8441` | HTTP/2 Extended CONNECT (WebSocket) | push/değişken kanal |
| 7 | `https://www.rfc-editor.org/rfc/rfc6585` | 429/503 gibi ek status kodları | RateLimit yanıt kodu dayanağı |
| 8 | `https://www.rfc-editor.org/rfc/rfc7540` | HTTP/2 | akış çoğullama |
| 9 | `https://www.rfc-editor.org/rfc/rfc7230` | HTTP/1.1 (eski) — yalnız tarihsel referans | RFC9112 öncesi |
| 10 | `https://datatracker.ietf.org/doc/html/rfc9110` | RFC9110 HTML sürümü | HTML okuma alternatifi |

### §11.2 W3C

| # | URL | K012'deki rolü | Not |
|---|---|---|---|
| 11 | `https://www.w3.org/TR/websockets/` | WebSocket API — real-time servis kanalı | Browser API spekülatifi |
| 12 | `https://www.w3.org/TR/media-capabilities/` | medya yetenek sorgusu — playback servisi | bilgi amaçlı; asıl sahibi K015 |

### §11.3 Diğer Resmi Kurumlar

| # | URL | K012'deki rolü | Not |
|---|---|---|---|
| 13 | `https://12factor.net/` | servis mimarisi 12 kural | yalnız ana sayfa 200 |
| 14 | `https://www.cncf.io/` | cloud-native servis ekosistemi | çatı organizasyon |
| 15 | `https://www.ietf.org/` | IETF ana sayfa | kurum referansı |

## §12 Repo Kod Kanıtı — Dosya Bazlı Gezinti

### §12.1 ServiceRegistry.php

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `shared/src/Api/Registry/ServiceRegistry.php` | 19 | `$services` + `$health` dizileri — kayıt defteri belleği | S1 |
| `shared/src/Api/Registry/ServiceRegistry.php` | 26 | `register()` — servis kaydı | S1 |
| `shared/src/Api/Registry/ServiceRegistry.php` | 38 | `discover()` — ad ile servis bulma | S1 |
| `shared/src/Api/Registry/ServiceRegistry.php` | 52 | `healthCheck()` — sağlık sorgusu delegasyonu | S1 |
| `shared/src/Api/Registry/ServiceRegistry.php` | 97 | `clear()` — defterin boşaltılması | S1 |

### §12.2 ServiceDefinition.php

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `shared/src/Api/Registry/ServiceDefinition.php` | 19 | sınıf tanımı | S1 |
| `shared/src/Api/Registry/ServiceDefinition.php` | 79 | `timeout=5`, `version='1.0.0'`, `healthy=true` varsayılanları | S1 |

### §12.3 ServiceHealth.php

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `shared/src/Api/Registry/ServiceHealth.php` | 34 | `overallStatus` alanı — toplu sağlık | S1 |
| `shared/src/Api/Registry/ServiceHealth.php` | 44 | `hasFailures()` — hata varlığı sorgusu | S1 |

### §12.4 VersionResolver.php ve VersionRegistry.php

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `shared/src/Api/Versioning/VersionResolver.php` | 24 | sınıf tanımı | S1 |
| `shared/src/Api/Versioning/VersionResolver.php` | 57 | `HEADER='X-API-Version'` — başlıktan sürüm çözümleme | S1 |
| `shared/src/Api/Versioning/VersionRegistry.php` | 24 | sınıf tanımı | S1 |
| `shared/src/Api/Versioning/VersionRegistry.php` | 96 | `add()` — sürüm kaydı | S1 |

### §12.5 RateLimitMiddleware.php

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | 22 | sınıf tanımı | S1 |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | 34 | `60 istek / 60 saniye` — pencere kuralı | S1 |

### §12.6 Yönlendirme Kanıtları (Apache / IIS)

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `home.coremusic.net/.htaccess` | 26 | Apache rewrite — servis yönlendirme | S1 |
| `home.coremusic.net/web.config` | 14 | IIS rewrite — karşılık gelen kural | S1 |

### §12.7 MusicStreamHandler.php (K015 ile ortak kanıt)

| Dosya | Satır | Ne görülüyor | Sınıf |
|---|---|---|---|
| `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 24 | MIME tablosu | S1 |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 46 | `1 MiB` chunk | S1 |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 91 | `Range → 206/416` yanıt mantığı | S1 |

## §13 K012 Alt Alan Kanıt Durumu

| Alt alan (klasör adı yazımıyla) | S1 | S2 | S3 | S4 | S5 | Baskın durum |
|---|---|---|---|---|---|---|
| `ai-service` | var | var | — | — | — | tasarım-seviyesi |
| `audio-service` | yok | var | — | var | var | kanıt-bekliyor |
| `background-jobs` | yok | var | — | var | var | kanıt-bekliyor |
| `device-service` | kısmi | var | — | var | var | kanıt-bekliyor |
| `integration-service` | yok | var | — | var | var | kanıt-bekliyor |
| `media-service` | yok | var | — | var | var | kanıt-bekliyor |
| `music-service` | kısmi | var | — | var | var | kanıt-bekliyor |
| `playback-service` | yok | var | — | var | var | kanıt-bekliyor |
| `service-contracts` | var | var | — | var | var | tasarım-seviyesi |
| `user-service` | kısmi | var | — | var | var | kanıt-bekliyor |

Kısmi S1: registry/versioning kanıtı ilgili alt alanın kendi kodunu değil, ortak `shared/src/Api` katmanını gösterir — alt alanın kendi implementasyonu hâlâ `⚠️ VERIFICATION REQUIRED`.

## §14 İddia Türü Dağılımı (K012 kök dosyaları)

| İddia Türü | Tahmini adet (kök 5 dosya) | Dayanak |
|---|---|---|
| Protokol kuralı | 40+ | S3 (RFC/W3C) |
| Repo davranışı | 19 | S1 (§12) |
| Mimari öneri | 60+ | S2 (tasarım-seviyesi) |
| Türetme | 25+ | S4 |
| Yokluk | 4 | S5 (§7) |
| Bilinmeyen | 9 açık soru | açık-soru |

## §15 Açık Soruların Doğrulama Etkisi

| Açık soru | Etkilediği doğrulama sonucu | Kapanana kadar durum |
|---|---|---|
| Q1 ai-service → K006 vs K016 | alt alan sahipliği | `UNKNOWN` |
| Q2 playback sahipliği | playback odak kapsamı | `UNKNOWN` |
| Q3 audio-service sınırı | sınır iddiaları | `UNKNOWN` |
| Q4 service-contracts kapsamı | sözleşme kapsamı | `UNKNOWN` |
| Q5 background-jobs motoru | iş motoru iddiası | `UNKNOWN` |
| Q6 device-identity sahipliği | cihaz kimlik iddiası | `UNKNOWN` |
| Q7 BC/DB ilişkisi | DB iddiaları | `tasarım-seviyesi` |
| Q8 swagger/PSR-7/PSR-15 | sözleşme otomasyonu | `tasarım-seviyesi` |
| Q9 servisler arası çağrının gerçek taşıyıcısı | transport iddiası | `⚠️ UNKNOWN` |

## §16 Doğrulama Sonuç Sayıları (2026-10-13)

| Metrik | Değer |
|---|---|
| Repo S1 kanıtı (K012) | 19 satır |
| Kullanılan resmi URL | 15 |
| Envantere göre yeni URL | 15 (2.929 ile kesişim 0) |
| Başarısız URL | 7 (§10) |
| Açık soru | 9 |
| `UNKNOWN` kapsamı | 9 açık soru + transport |
| Sınıf dışı iddia | 0 |

## §17 Geçmiş Doğrulama Turları

| Tur | Tarih | Kapsam | Sonuç |
|---|---|---|---|
| tur-1 | 2026-10-12 | ilk iskelet | iskelet kabul |
| tur-2 | 2026-10-13 | derinleştirme (bu tur) | 19 S1 + 15 S3 kanıtı eklendi |
| tur-3 | planlanmadı | — | — |

## §18 Kapsam Dışı Yüzeyler

| Yüzey | Neden kapsam dışı |
|---|---|
| `*.php` / `*.js` / `*.css` / `*.sql` kaynak değişikliği | salt-okunur; bu dosya yalnız kanıt okur |
| `.ai/electronic/**` | yazma yasağı |
| `.ai/architecture/index.md` | yazma yasağı |
| K000–K009, K013, K016, K018, K020 | yazma yasağı |
| K014 / K015 | bu görevin sıradaki kökleri |

## §19 Sonraki Doğrulama Turu İçin Hazır Listesi

| # | Yapılacak | Koşul |
|---|---|---|
| 1 | 9 açık soruyu ilgili vault dosyalarında ara | Q1–Q9 |
| 2 | `background-jobs` iş motorunu repo'da ara | Q5 |
| 3 | servisler arası transport'u ağ trafiğinde doğrula | Q9 |
| 4 | EBU R128 + Ozone URL'lerini yeniden dene | başarısız URL'ler |
| 5 | 12factor alt sayfalarını yeniden dene | başarısız URL'ler |

## §20 Ekler — Doğrulama Sözlüğü

| Terim | Bu dosyadaki anlamı |
|---|---|
| iddia | bir dosyada doğrulanması gereken tek bir önerme |
| kanıt sınıfı | S1–S5 |
| dayanak | kanıtın geldiği kaynak (repo / vault / URL) |
| durum | doğrulanmış / doğrulanmadı / tasarım-seviyesi / UNKNOWN / kanıt-bekliyor / kısmen-doğrulanmış / çelişkili / reddedildi |
| envanter | 2.929 URL listesi |
| resmi kaynak | RFC Editor / W3C / IETF / CNCF / 12factor |
| açık soru | kanıt kapanana kadar `UNKNOWN` tutan madde |
| kesişim | bu dosyadaki URL ∩ envanter = 0 olmalı |

## §21 15 Kriterin Tek Tek Doğrulama Prosedürü

### §21.1 K1–K5 prosedürleri

| Kriter | Prosedür | Kanıt sınıfı | Başarı ölçütü |
|---|---|---|---|
| K1 sürücü veri modeli | `.md` içindeki alan tablosu ile `ServiceDefinition.php` alanları karşılaştırılır | S1 | her alan eşleşiyor |
| K2 Otonomi/Bölmeler | `.md` §kapsam ile dosya listesi karşılaştırılır | S2 | §kapsam gerçek klasörle aynı |
| K3 BC/DB ilişkisi | BC iddiası repo'da `composer.json`/namespace ile aranır | S1/S5 | ya kod var ya `UNKNOWN` |
| K4 Service/API sözleşmeleri | sözleşmelerin repo'daki karşılığı (`shared/src/Api`) aranır | S1 | registry + versioning kodu bulundu |
| K5 Tek uç nokta / router | `.htaccess` 26 ve `web.config` 14 okunur | S1 | iki platformda da rewrite var |

### §21.2 K6–K10 prosedürleri

| Kriter | Prosedür | Kanıt sınıfı | Başarı ölçütü |
|---|---|---|---|
| K6 RESTful'ı önceliklendirme | repo'da REST yönlendirme kanıtı aranır | S1 | `.htaccess`/`web.config` |
| K7 JSON kabuk ve idempotent | kod içi `json_encode`/idempotency araştırması | S5 | yoksa `UNKNOWN` yazılır |
| K8 Stream/Video API | `MusicStreamHandler.php` okunur | S1 | MIME/Range/206 kanıtı |
| K9 WebSocket/real-time | `w3.org/TR/websockets/` + `rfc8441` | S3 | iki URL 200 |
| K10 ECID / oturum anahtarı | repo'da ECID aranır | S5 | yoksa `UNKNOWN` |

### §21.3 K11–K15 prosedürleri

| Kriter | Prosedür | Kanıt sınıfı | Başarı ölçütü |
|---|---|---|---|
| K11 cihaz servisleri dikey kestirim | `device-service` klasör içeriği + cihaz kayıt kodu | S2/S5 | tasarım-seviyesi |
| K12 Ayrık ortam/kimlik/izleme | `RateLimitMiddleware` + registry sağlık kodu | S1 | 60/60s + healthCheck |
| K13 sürümler ve bağımlılık kesme | `VersionResolver`/`VersionRegistry` | S1 | X-API-Version başlığı |
| K14 API ağ geçidi yönlendirmesi | `.htaccess`/`web.config` yönlendirme | S1 | rewrite kanıtı |
| K15 kirli iş dış kaynak | `background-jobs` alt alanı | S2 | motor `UNKNOWN` |

### §21.4 Kriter doğrulama özet sayıları

| Sayı | Değer |
|---|---|
| Kriter tablosu satırı | 15 |
| S1 ile kapanan kriter | 6 (K1, K4, K5, K6, K8, K13) |
| S2 ile kalan kriter | 4 (K2, K4-pasif, K11, K15) |
| S5/`UNKNOWN` ile kalan kriter | 5 (K3, K7, K9-tam, K10, K12-eksik) |

## §22 Çelişki Avcısı — İddia Eşleştirme Tablosu

| İddia A | İddia B | Çelişki? | Karar |
|---|---|---|---|
| registry bellek-içi (`ServiceRegistry` dizileri) | "dağıtık servis kayıt" (vault) | evet-imsal | repo'da tek süreçli defter; dağıtık iddia `⚠️ VERIFICATION REQUIRED` |
| `version='1.0.0'` tek sürüm | "çoklu API sürümü" (vault) | kısmi | kayıt tek sürüm; çoklu sürüm `UNKNOWN` |
| rate limit 60/60s | "ağ geçidinde global limit" (vault) | kısmi | repo kanıtı middleware düzeyinde; global ağ geçidi `UNKNOWN` |
| tek `.htaccess`/`web.config` yönlendirme | "tek uç nokta ilkesi" (12factor) | hayır | uyumlu |
| WebSocket kanalı (vault) | repo'da WebSocket kodu YOK (S5) | evet | tasarım-seviyesi; `kanıt-bekliyor` |

## §23 Doğrulama Sonucu İddiaları

| İddia | Sonuç | Gün |
|---|---|---|
| K012 kök dosyalarında uyduruk URL yok | doğrulanmış (15 URL 200) | 2026-10-13 |
| Repo kod kanıtı gerçek dosya+satır | doğrulanmış (19 satır) | 2026-10-13 |
| Yokluk iddiaları (`*Service.php` YOK) | doğrulanmış (§7, 4 satır) | 2026-10-13 |
| Servisler arası transport | `UNKNOWN` | 2026-10-13 |
| Gerçek runtime davranışı | `⚠️ VERIFICATION REQUIRED` | 2026-10-13 |
| Alt alan implementasyonları | `kanıt-bekliyor` | 2026-10-13 |

## §24 Sorumluluk ve Tekrar Zamanlaması

| Rol | Görev | Sıklık |
|---|---|---|
| Vault Steward | §17 tur-3 açılışı | yeni kanıt çıktıkça |
| Vault Steward | başarısız URL'leri yeniden dene | en geç sonraki görevde |
| Vault Steward | açık soru Q1–Q9 kapanış takibi | her derinleştirme turunda |
| Automation | envanter (2.929 URL) yenilemesi | mimari dosya değişiminde |

## §25 Uyarı İşaretleri Sözlüğü

| İşaret | Anlamı |
|---|---|
| `⚠️ VERIFICATION REQUIRED` | kanıt yok; iddia yazılmamalı |
| `⚠️ UNKNOWN` | bilgi hiç yok; tahmin yasak |
| `⚠️ ÇELİŞKİ` | iki kaynak farklı diyor |
| `⚠️ KISMEN` | bir kısmı kanıtlanmış |
| `⚠️ GEÇMİŞ TUR` | önceki oturumdan kaldı |

Bu dosyada yer tutucu desenleri, boş hücre ve mojibake kullanılmaz; bilinmeyen değer `UNKNOWN`, kanıtsız iddia `⚠️ VERIFICATION REQUIRED` olarak yazılır.

## §26 Tur-2 Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 15 URL 200 doğrulandı mı | evet (§11) |
| 2 | 7 başarısız URL listelendi mi | evet (§10) |
| 3 | 19 repo satır kanıtı gerçek mi | evet (§12) |
| 4 | 9 açık soru taşındı mı | evet (§15) |
| 5 | Envanter kesişimi 0 mı | evet (§16) |
| 6 | Yokluk iddiaları geçerli mi | evet (§7) |
| 7 | Çelişki tablosu var mı | evet (§22) |
| 8 | Son satır authority satırı mı | evet (§27) |

## §27 Authority

Bu doğrulama dosyasının otoritesi Vault Steward'dır; iddialar yalnız buradaki
kanıt sınıflarıyla desteklenir. Kanıtsız hiçbir satır `doğrulanmış` sayılmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
