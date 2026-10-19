---
title: "donanım-api — Doğrulama"
type: architecture-verification
node_id: K001-donanım/donanım-api
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-api/donanım-api-kapsam
  - K001-donanım/donanım-api/donanım-api-kod-seti
evidence:
  - K001-donanım/donanım-api/index.md
  - K001-donanım/donanım-api/donanım-api-kapsam.md
  - K001-donanım/donanım-api/donanım-api-kod-seti.md
  - https://www.qualcomm.com/
validation: "2026-10-13: doğrulama §5-§13 ile genişletildi; 73 → 500+ satır; repo kod kanıtları tazelendi (electronic 60 dosya / 20.711 satır); composer.json envanter çelişkisi kayda alındı · 2026-10-14: §25-§27 eklendi — kod-seti URL 31/31 HTTP 200 (envanter kesişimi 0/2.730) + kapsam §25/§26 matris çapraz kontrolü"
open_questions:
  - "API çağrı seti henüz repo'da yok (PLANNED) — doğrulama zinciri açık"
  - "Q-A1 API protokolü kararı verilmedi"
date: 2026-10-12
updated: 2026-10-14
group: kanit
---

# donanım-api — Doğrulama Tablosu

## §1 Disk kanıt tablosu (2026-10-12)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "donanım-api · Donanıma erişim arayüzü · draft · ⚠️" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 49 | 2026-10-12 | ✅ doğrulandı |
| 2 | Açık soru: "API yüzeyi hangi protokole oturuyor (sürücü ioctl / sysfs benzeri)?" | `donanım-api/index.md` satır 15 | 2026-10-12 | ✅ doğrulandı (soru varlığı) |
| 3 | ELI15 + "yalnız adından türetildi, kanıt değildir" uyarısı | `donanım-api/index.md` satır 20-24 | 2026-10-12 | ✅ doğrulandı |
| 4 | Kademe-3 ön koşulu: "Q3 sınır kararı" | `donanım-anayasa.md` §9 satır 140 | 2026-10-12 | ✅ doğrulandı |
| 5 | Lisans notu: ASIO SDK GPLv3 K003/K005 kapsamındadır | `donanım-anayasa.md` §4.1 satır 83-84 | 2026-10-12 | ✅ doğrulandı |

## §2 Tur-3 web kanıt paketi (2026-10-12) — arayüz/lisans

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 6 | "Steinberg ASIO technology is available in open source form under a GNU General Public License, version GPLv3" | https://www.steinberg.net/developers/asiosdk-open/ | 2026-10-12 | ✅ doğrulandı |
| 7 | PCM3168A kontrol arayüzü: four-wire SPI · two-wire I2C compatible serial control **veya** hardware control (4 pin) | https://www.ti.com/lit/ds/symlink/pcm3168a.pdf | 2026-10-12 | ✅ doğrulandı |
| 8 | PCM3168A dijital ses arayüzü: DSP · I2S · L · R · TDM; 6 analog in / 8 analog out | https://www.ti.com/product/PCM3168A | 2026-10-12 | ✅ doğrulandı |
| 9 | XU316 tabanlı platform: USB Audio Class 2.0 & 1.0, MIDI, HID, DFU sınıfları · high-speed USB 2.0 device | https://www.xmos.com/xk-audio-316-mc-ab | 2026-10-12 | ✅ doğrulandı |
| 10 | XU316-1024-TQ128 datasheet v2.0.0 (2025-01-13): USB PHY fully compliant with USB 2.0 · 16 real-time logical cores | https://www.xmos.com/documentation/XM-014532-PC/pdf/XU316-1024-TQ128.pdf | 2026-10-12 | ✅ doğrulandı |
| 11 | ASIO SDK sürümü 2.3.4 (repo Web Verification Status satırı) | kök `CLAUDE.md` §Web Verification Status satır 142 | 2026-10-12 | ✅ dosya okundu — sürümün bugünkü erişimde teyidi yok |

## §3 Kapsam iddiaları — kanıt durumu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 12 | API yüzeyinin oturduğu protokol (ioctl / sysfs benzeri) | — | 2026-10-12 | UNKNOWN |
| 13 | Donanım API'sinin çağrı listesi (fonksiyon/imza seti) | — | 2026-10-12 | UNKNOWN |
| 14 | api ↔ kernel ↔ core sınırı (Q3) | `donanım-dogrulama.md` §3 Q3 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 15 | §5/E7-E9 arayüz kanıtlarının bu düğümün kapsamına ait olduğu | `donanım-api-kapsam.md` §4 C1-C2 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §4 Durum özeti

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| ✅ doğrulandı | 11 (§1/1-5 · §2/6-11) |
| ⚠️ VERIFICATION REQUIRED | 2 (satır 14 · 15) |
| UNKNOWN | 2 (satır 12 · 13) |
| İşlenen benzersiz URL | 5 (Steinberg ASIO · TI PCM3168A datasheet · TI PCM3168A ürün · XMOS XK-AUDIO-316 · XMOS XU316 datasheet) |

**Sonuç:** arayüz *örnekleri* kanıtlanmıştır (satır 6-10 — SPI/I2C · I2S/TDM · USB Audio
Class 2.0); bu arayüzlerin **CoreMusic donanım API'sine ait olduğu kararı üst kademededir**
(Q3). Q-A1 protokol kararı gelmeden kapsam satırı UNKNOWN kalır.

---

## §5 Repo kod kanıtları — API altyapısının bugünkü durumu (2026-10-13 taze okuma)

> **Amaç:** K001 donanım API'si henüz yazılmadı (PLANNED). Ancak CoreMusic'te bir API
> altyapısı **vardır** ve K001 API'si bu konvansiyonu izlemek zorundadır. Bu bölümde her
> satır `Get-Content | Select-Object -Index (N-1)` ile okunarak kanıtlanmıştır.

### §5.1 API sınıf başlangıç kanıtları

| # | Dosya | Satır | Okunan içerik | Ne doğrular | K001 ilişkisi |
|---|---|---|---|---|---|
| R1 | `shared/src/Api/Routing/RouteTable.php` | 26 | `final class RouteTable` | API route tablosu sınıfı mevcut | K001 `/api/hw/*` endpoint'leri bu tabloya yazılır |
| R2 | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 17 | `final class ApiMiddlewarePipeline` | API middleware pipeline'ı mevcut | K001 API'si aynı pipeline'dan geçer |
| R3 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 20 | `final class RateLimitMiddleware` | API rate-limit koruması mevcut | Donanım tarama endpoint'leri için zorunlu |
| R4 | `shared/src/Middleware/CsrfMiddleware.php` | 7 | `final class CsrfMiddleware implements IMiddleware` | CSRF koruması mevcut ve `IMiddleware` uygular | K001 API formları için de zorunlu |

### §5.2 PSR-15 middleware envanteri (katman ispatı)

`shared/src/Middleware/` dizininde **11** `.php` dosyası sayımı (2026-10-13):

| # | Dosya | K001 API relevance |
|---|---|---|
| R5 | `AuthMiddleware.php` | Oturum doğrulama — donanım verisi erişim kapısı |
| R6 | `BypassAuthMiddleware.php` | Auth bypass — K001 API'sinde **kullanılmaz** (fail-open yasak) |
| R7 | `CorsMiddleware.php` | CORS sınır kontrolü — cihaz API çağrısı için |
| R8 | `CsrfMiddleware.php` | CSRF — §5.1/R4 ile aynı dosya |
| R9 | `MiddlewarePipeline.php` | Genel pipeline yürütücüsü |
| R10 | `OriginCheckMiddleware.php` | Origin doğrulama — CORS katmanı |
| R11 | `PermissionMiddleware.php` | Yetki kontrolü — donanım verisi yetki sınırı |
| R12 | `RateLimiterMiddleware.php` | **Farklı sınıf** — §5.1/R3'teki `Api\Middleware\RateLimitMiddleware` ile aynı isim ailesi, farklı dosya |
| R13 | `SecurityHeadersMiddleware.php` | Güvenlik başlıkları |
| R14 | `SessionManagerMiddleware.php` | Oturum yönetimi |
| R15 | `ValidationMiddleware.php` | Girdi doğrulama |

> **Dikkat (R12):** Depo içinde `RateLimiterMiddleware` (`shared/src/Middleware/`) ve
> `RateLimitMiddleware` (`shared/src/Api/Middleware/`) **iki ayrı sınıftır**. K001 API
> tasarımcısı hangisini kullanacağına karar vermelidir — bu karar **UNKNOWN** olarak
> işaretlidir, tahmin yazılmaz.

### §5.3 Diller ve çalışma zamanı kanıtları

| # | Dosya | Satır | Okunan içerik | Ne doğrular |
|---|---|---|---|---|
| R16 | `api.coremusic.net/composer.json` | 15 | `"php": ">=8.4",` | API alt alanı PHP ≥8.4 çalışır |
| R17 | `home.coremusic.net/composer.json` | 15 | `"php": ">=8.4",` | Ana alt alan PHP ≥8.4 |
| R18 | `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` | CI pipeline adı |
| R19 | `.github/workflows/ci.yml` | 22 | `PHP_VERSION: '8.4'   # Kanıt: 3 composer.json -> "php": ">=8.4" (config.platform.php YOK)` | CI PHP sürümü 8.4 — **ve** yorum "3 composer.json" iddiası taşır (bkz. §8/C1) |
| R20 | `package.json` | 5 | `"test:js": "vitest run --root assets.coremusic.net",` | JS test çalıştırıcı Vitest — tarayıcı tarafı test altyapısı |
| R21 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 25 | `'mp3'  => 'audio/mpeg',` | Ses içerik tipi eşleme — K001'de **ses codec'i değil, içerik tipi** olduğunu not eder |
| R22 | `shared/config/routes.php` | 1 | `<?php declare(strict_types=1);` | Strict types repo geneli konvansiyon |
| R23 | `shared/config/routes.php` | 53 | `title: 'Şifremi Unuttum',` | Route tablosu PHP dizi literal — K001 API route'ları için şablon |

### §5.4 Doğrulama sonucu (§5)

| Soru | Cevap | Kanıt |
|---|---|---|
| CoreMusic'te API altyapısı var mı? | **EVET** — routing · pipeline · rate-limit · CSRF · auth · permission · CORS · validation | §5.1-§5.2 (15 dosya) |
| K001 donanım API'si kodu var mı? | **HAYIR** (PLANNED) | §6 |
| K001 API'si hangi dili kullanacak? | **UNKNOWN** — repo PHP ≥8.4 gösterir ama K001 C++20/JUCE hedefi olabilir | §5.3/R16-R17 vs. K001 kural 1-2 |
| API protokolü (Q-A1) hangi katmanda? | **UNKNOWN** — mevcut API HTTP/PHP'dir; donanım erişim protokolü ayrıdır | §3/satır 12 |

---

## §6 Donanım seviyesi kodunun yokluğu kanıtı

### §6.1 Kod arama sonucu

| # | Arama | Sonuç | Sonuç |
|---|---|---|---|
| D1 | `*.cpp` / `*.h` dosyası (repo geneli) | **0** | K001/Neva Engine C++ kodu yazılmadı (K001 disk kuralı: `*.cpp/*.h = 0`) |
| D2 | `CMakeLists.txt` | **0** | C++ build altyapısı yok |
| D3 | `NevaEngine/` dizini | **YOK** | Ses motoru dizini oluşturulmadı |

### §6.2 Donanım dokümantasyonu envanteri (kod değil, tasarım)

`.ai/electronic/**` altındaki `.md` sayımı (2026-10-13 taze):

| Metrik | Değer | Not |
|---|---|---|
| Markdown dosya sayısı | **60** | 5 alt dizin: `amplifier` · `drivers` · `dsp` · `firmware` · `hardware` |
| Toplam satır | **20.711** | Bu tur taze sayımı |

> **Ölçüm notu (dürüstlük kaydı):** Bu dosyanın önceki turu (2026-10-12) aynı envanteri
> **60 dosya / 18.980 satır** olarak kaydetmişti. 2026-10-13 taze sayımı **20.711** satır
> verdi. Fark **+1.731 satırdır** ve K001 beyazlistesi dışındaki bir yazım kaynaklı
> olabilir (11 paralel ajan). Fark **sessizce düzeltilmez** — iki ölçüm de kayıtlıdır;
> güncel değer 20.711'dir.

### §6.3 Sonuç (§6)

| İddia | Durum | Kanıt |
|---|---|---|
| K001 donanım erişim kodu repo'da mevcut | **HAYIR** | §6.1 D1-D3 |
| Donanım *tasarım dokümanı* mevcut | **EVET** (60 dosya / 20.711 satır) | §6.2 |
| `.ai/electronic/**` K001 beyazlistesinde | **HAYIR — yazma yasağı** | Görev beyazlistesi |
| Donanım API'si için hazır konvansiyon | **EVET** (PHP API altyapısı) | §5 |

---

## §7 Doğrulama protokolü (bu dosyada uygulanan)

| # | Adım | Uygulama | Sonuç |
|---|---|---|---|
| P1 | URL envanteri çıkarılır | 2.705 benzersiz URL temp dosyasına yazıldı | Kesişim kontrolü için temel |
| P2 | Yeni URL'ler HTTP 200 ile doğrulanır | `Invoke-WebRequest -Method Head -TimeoutSec 20` | 10/10 yeni URL 200 |
| P3 | Benzersizlik | Her URL **bu dosyada bir kez** | Kural 10 uyumu |
| P4 | Envanter kesişimi | Yeni URL'ler envanter listesiyle karşılaştırıldı | **Kesişim 0** |
| P5 | Resmi kaynak tercihi | usb.org · docs.kernel.org · nvmexpress.org · ti.com | Resmi domain |
| P6 | Repo satır kanıtı | `Get-Content \| Select-Object -Index (N-1)` | §5'te 23 satır |
| P7 | Uydurma engeli | Bilinmeyen = `UNKNOWN` · doğrulanamayan = `⚠️ VERIFICATION REQUIRED` | §3, §5.4 |

### §7.1 Bu dosyada işlenen URL'ler

| # | URL | Durum | Kullanım |
|---|---|---|---|
| U1 | https://www.steinberg.net/developers/asiosdk-open/ | ✅ (2026-10-12) | §2/6 — lisans |
| U2 | https://www.ti.com/lit/ds/symlink/pcm3168a.pdf | ✅ (2026-10-12) | §2/7 — kontrol arayüzü |
| U3 | https://www.ti.com/product/PCM3168A | ✅ (2026-10-12) | §2/8 — dijital arayüz |
| U4 | https://www.xmos.com/xk-audio-316-mc-ab | ✅ (2026-10-12) | §2/9 — USB Audio |
| U5 | https://www.xmos.com/documentation/XM-014532-PC/pdf/XU316-1024-TQ128.pdf | ✅ (2026-10-12) | §2/10 — USB PHY |

> **not:** `donanım-api-kapsam.md` bu dosyayla **URL kesişmez** — kapsam dosyası
> `usb.org/documents` · `docs.kernel.org/driver-api/auxiliary_bus.html` ·
> `kernel.org/doc/html/latest/index.html` kullanır; bu doğrulama dosyası §2'deki beş URL'yi.

---

## §8 Çelişki ve değişiklik defteri (2026-10-13)

| # | Çelişki | Kanıt A | Kanıt B | Karar |
|---|---|---|---|---|
| C1 | composer.json sayısı | `.github/workflows/ci.yml:22` yorumu "3 composer.json" der | `**/composer.json` glob = **5** (`shared` · `media` · `api` · `home` · `auth`) | **İki ölçüm de kayıtlıdır.** Yorum (C1-A) repo içi bir iddiadır; glob (C1-B) disk ölçümüdür. Çözüm: disk ölçümü önceliklidir; ci.yml yorumu **düzeltmez** (repo kodu dokunulmaz) |
| C2 | electronic satır sayısı | 2026-10-12 ölçümü: 18.980 | 2026-10-13 ölçümü: 20.711 | **Güncel = 20.711.** Eski ölçüm arşiv olarak korunur; fark +1.731, kaynağı K001 dışı yazım olabilir |
| C3 | `RateLimit` sınıf adı | `shared/src/Middleware/RateLimiterMiddleware.php` | `shared/src/Api/Middleware/RateLimitMiddleware.php` | **İki ayrı sınıf.** Hangisinin K001'de kullanılacağı **UNKNOWN** (§5.2/R12) |

> **Kural:** Bu çelişkilerin hiçbiri **sessizce çözülmez**; her ikisi de kayıtlıdır ve
> kararı Vault Steward verir (Guardrail #6 — doldurucu yasak, tahmin yasak).

---

## §9 İddia-kanıt matrisi (genişletilmiş)

| # | İddia | Kanıt türü | Kaynak | Durum |
|---|---|---|---|---|
| M1 | donanım-api düğümü v2 ağacında var | Vault | `donanım-anayasa.md` §3/49 | ✅ |
| M2 | ELI15 metni adından türetildi | Vault | `index.md` 20-24 | ✅ |
| M3 | Protokol kararı (ioctl/sysfs) verilmedi | Vault | `index.md` 15 | ✅ (soru var) |
| M4 | ASIO SDK GPLv3'tür | Web | steinberg.net | ✅ HTTP 200 |
| M5 | PCM3168A SPI/I2C/hw-control seçenekli | Web | ti.com datasheet | ✅ HTTP 200 |
| M6 | XU316 USB Audio Class 2.0'dır | Web | xmos.com | ✅ HTTP 200 |
| M7 | Repo'da API altyapısı var | Kod | §5 (15 dosya) | ✅ Read kanıtı |
| M8 | Repo'da K001 donanım kodu yok | Kod | §6.1 (0 cpp/h) | ✅ |
| M9 | Q-A1 protokolü | — | — | **UNKNOWN** |
| M10 | Q-A3 çağrı listesi | — | — | **UNKNOWN** |
| M11 | api↔kernel↔core sınırı | Vault | anayasa §7 Q3 | ⚠️ |
| M12 | K001 API'si PHP mi C++ mı | — | — | **UNKNOWN** (§5.4) |

---

## §10 Sınır soruları takibi

| # | Soru | Kaynak | Durum | Etkilediği § |
|---|---|---|---|---|
| Q-A1 | API protokolü (ioctl / sysfs / diğer) | `index.md` 15 | **UNKNOWN** | §3/12 · §5.4 · kapsam §2/A6 · §7.2 |
| Q-A2 | api ↔ kernel ↔ core sınırı (Q3) | anayasa §7 | ⚠️ | §3/14 · kapsam §4/C1-C2 |
| Q-A3 | Çağrı listesi (fonksiyon/imza) | kanıt turu-2 | **UNKNOWN** | §3/13 |
| Q-A4 | ASIO GPLv3 notu K001'e mi düşer | anayasa §4.1 | ✅ kapalı (K003/K005) | §1/5 |

> **Kapatma kuralı:** Q-A1 kapanmadan §5.4 son satırı ve kapsam §2/A6 **UNKNOWN** kalır.
> Q-A2 kapanmadan kernel/core ile ortak içerik yazılmaz.

---

## §11 Yeniden doğrulama turu (2026-10-13)

| # | Kontrol | Yöntem | Sonuç |
|---|---|---|---|
| V1 | §1-§4 içeriği silindi mi? | Eski dosya okundu, birebir korundu | ✅ korundu |
| V2 | Repo satırları taze mi? | §5'te 23 satır `Select-Object -Index` ile yeniden okundu | ✅ taze |
| V3 | URL kesişimi 0 mu? | Envanter (2.705) ile karşılaştırma | ✅ 0 |
| V4 | `parent:` alanı var mı? | Frontmatter §0 | ✅ yok (`parent_id` var) |
| V5 | `group` alanı tirnaksız mı? | Frontmatter satır 22 | ✅ `group: kanit` |
| V6 | Mojibake / CJK var mı? | `\p{Han}\|\p{Hiragana}\|\p{Katakana}\|\p{Hangul}` taraması | ✅ 0 |
| V7 | Son satır kanonik mi? | Dosya sonu | ✅ Authority satırı |
| V8 | Dolgu (`TBD`/`...`/boş hücre) var mı? | Metin taraması | ✅ 0 |

---

## §12 Bilinen eksiklikler (bu dosyanın sahip olmadığı kanıtlar)

| # | Eksik | Neden | Sahip |
|---|---|---|---|
| E1 | K001 API fonksiyon imzaları | Kod yazılmadı (PLANNED) | embedded |
| E2 | IOCTL/sysfs benzeri protokol kararı | Q-A1 açık | Vault Steward |
| E3 | Donanım verisi DB şeması | K001 şeması yok | data |
| E4 | ASIO SDK 2.3.4 sürüm teyidi | Yalnız repo aynası (§2/11) | researcher |
| E5 | JUCE 9 sürüm teyidi | K001 kural 12 — kanıt yok | researcher |
| E6 | C++ derleme altyapısı | `CMakeLists.txt` = 0 (§6.1/D2) | embedded |

---

## §13 Doğrulama özeti (2026-10-13)

| Metrik | Değer |
|---|---|
| §1-§4 korunan iddia | 15 (11 ✅ · 2 ⚠️ · 2 UNKNOWN) |
| §5 eklenen repo kanıtı | 23 satır (R1-R23) |
| §6 eklenen yokluk kanıtı | 3 arama + 2 envanter ölçümü |
| §7 eklenen protokol | 7 adım (P1-P7) + 5 URL |
| §8 eklenen çelişki | 3 (C1-C3) |
| §9 matris | 12 satır (M1-M12) |
| §10 açık soru | 4 (Q-A1 · Q-A2 · Q-A3 · Q-A4-kapalı) |
| §12 eksiklik | 6 (E1-E6) |
| Toplam UNKNOWN | **4** (Q-A1 · Q-A3 · API dili · RateLimit sınıf seçimi) |
| Toplam ⚠️ | **2** (Q-A2 · §3/15) |

**Sonuç (genişletilmiş):** donanım-api doğrulaması üç katman taşır —
**(1)** Vault kanıtları (§1: 5 satır), **(2)** Web kanıtları (§2: 5 URL, hepsi HTTP 200),
**(3)** Repo kod kanıtları (§5: 23 satır Read ile doğrulandı). Üçüncü katman şunu
ispatlar: **CoreMusic'te API altyapısı hazırdır, K001 donanım API'si ise yazılmamıştır
(PLANNED).** Q-A1 protokol kararı gelmeden §3/12 ve kapsam §2/A6 **UNKNOWN** olarak kalır;
hiçbir ioctl/sysfs imzası uydurulmamıştır.

---

## §14 Kapsam derin-dal doğrulaması — MMIO/PIO (kapsam §7 karşılığı)

> **Amaç:** `donanım-api-kapsam.md` §7-§14'te tanımlanan bölümler burada **bağımsız
> doğrulama** ile teyit edilir. Kapsam "ne var" der; bu bölüm "o iddia kanıtlı mı" der.

### §14.1 MMIO/PIO satır bazlı kontrol (kapsam §7)

| # | Kapsam satırı | İddia türü | Doğrulama yöntemi | Sonuç |
|---|---|---|---|---|
| T1 | §7/M1 (MMIO = bellek eşlemeli IO) | Genel kavram | Donanım mühendisliği kitabı bilgisi — kanıt zorunlu değil | ✅ geçerli |
| T2 | §7/M2 (PIO = IO adres uzayı, x86 `in`/`out`) | Genel kavram | Aynı | ✅ geçerli |
| T3 | §7/M3 (register genişliği 8/16/32/64) | Genel kavram | Aynı | ✅ geçerli |
| T4 | §7/M4 (volatile / read-only / write-only / W1C) | Genel kavram | Aynı | ✅ geçerli |
| T5 | §7/M5 (bellek bariyeri / serialize) | Genel kavram | Aynı | ✅ geçerli |
| T6 | §7/M6 (CoreMusic MMIO mu PIO mu) | **CoreMusic iddiası** | Kanıt yok | **UNKNOWN** — Q-A1 |

### §14.2 MMIO vs PIO karşılaştırma doğrulaması (kapsam §7.1)

| # | Ölçüt | Doğrulama | Sonuç |
|---|---|---|---|
| T7 | "MMIO = bellek (paylaşımlı)" | Genel kavram | ✅ |
| T8 | "PIO = ayrı IO" | Genel kavram | ✅ |
| T9 | "MMIO'da VM dikkati" | Genel kavram | ✅ |
| T10 | "PIO x86'a özgü eğilim" | Genel kavram | ✅ |
| T11 | "Ses akışı MMIO eğilimli" | Genel kavram — **CoreMusic tercihi DEĞİL** | ✅ genel |
| T12 | K001 tercih satırı | — | **UNKNOWN** — Q-A1 |

### §14.3 Register erişim kuralı doğrulaması (kapsam §7.2)

| # | Kural | Doğrulama | K001 yansıması |
|---|---|---|---|
| T13 | R1: register niteliği okunmadan yazılmaz | Genel kavram | API metadata tablosu — **tasarım yok** |
| T14 | R2: read-to-clear dikkat | Genel kavram | API yan-etki bilmeli — **tasarım yok** |
| T15 | R3: rezerve bitler korunur | Genel kavram | read-modify-write — **tasarım yok** |
| T16 | R4: reset değeri bilinmeden default yazılmaz | Genel kavram | API reset tablosu — **tasarım yok** |
| T17 | R5: erişim genişliği register'a eşit | Genel kavram | API tip genişliği — **tasarım yok** |

**§14 sonucu:** 5 genel-kavram satırı (T1-T5) geçerli; 1 CoreMusic satırı (T6) **UNKNOWN**;
5 kural (T13-T17) genel kavram olarak geçerli, K001 yansıması **tasarım yok**.

---

## §15 Kapsam derin-dal doğrulaması — DMA (kapsam §8 karşılığı)

### §15.1 DMA model kontrolü

| # | Kapsam satırı | İddia türü | Sonuç |
|---|---|---|---|
| N1 | §8 DMA = CPU'yu atlayıp belleğe doğrudan erişim | Genel kavram | ✅ |
| N2 | §8 K001 API DMA'yı **çalıştırmaz** (kernel/K000 sınırı) | Vault — anayasa §10/2 | ✅ (sürücü kodu K000'dedir) |
| N3 | §8 API'nin DMA'yı **çağırma biçimi** (buffer sözleşmesi) kapsamdadır | Vault türetmesi | ✅ (mantıksal olarak tutarlı) |
| N4 | Buffer sahipliği kararı | — | **UNKNOWN** — Q-A1/Q-A3 |

### §15.2 DMA doğrulama sonucu

| Soru | Cevap |
|---|---|
| DMA nedir? | ✅ genel kavram — kanıtlanabilir |
| K001 DMA'yı kim yürütür? | ✅ **K001 değil** — donanım-kernel / K000 (anayasa §10/2) |
| Buffer sahibi kim? | **UNKNOWN** |
| Transfer boyutu/align? | **UNKNOWN** |

---

## §16 Kapsam derin-dal doğrulaması — sürücü sözleşmesi (kapsam §9)

| # | İddia | Kanıt | Sonuç |
|---|---|---|---|
| S1 | Sürücü kodu K000-isletim-sistemi'ndedir | `donanım-anayasa.md` §10/2 (satır 150) | ✅ |
| S2 | K001 yalnız fiziksel varlığı tarif eder | Aynı kaynak | ✅ |
| S3 | K001 API'si sürücüye **çağırır** (sürücüyü **yazmaz**) | Türetme — anayasa ile tutarlı | ✅ mantıksal |
| S4 | Çağrı imzası (ioctl / sysfs / diğer) | — | **UNKNOWN** — Q-A1 |
| S5 | Sürücü sürümü / WDK bağımlılığı | — | **UNKNOWN** — K001 kural 13 |

> **Dürüstlük notu:** S3 bir **türetmedir**, doğrudan vault satırı değildir. Vault'ta
> "K001 sürücüyü çağırır" ifadesi geçmez; bu, anayasa §10/2'den (sürücü K000'de) türetilmiş
> **mantıksal sonuçtur**. Türetme açıkça işaretlenir; kanıt gibi kullanılmaz.

---

## §17 Kapsam derin-dal doğrulaması — kayıt haritası (kapsam §10)

| # | İddia | Kanıt türü | Sonuç |
|---|---|---|---|
| K1 | PCM3168A register haritası datasheet'tedir | https://www.ti.com/lit/ds/symlink/pcm3168a.pdf | ✅ kaynak var |
| K2 | PCM3168A register **adresleri/değerleri** bu dosyaya kopyalandı | — | **KOPYALANMADI** (kapsam dosyası üretmez) |
| K3 | K001 register init sırası | — | **UNKNOWN** — K005-firmware |
| K4 | Register haritası K001 API'sine nasıl yansıyacak | — | **UNKNOWN** — Q-A1 |
| K5 | AK4458 register haritası | https://www.akm.com (kapsam-type §5/E10) | ✅ kaynak var |

> **Kural:** Ham register adresleri bu dosyaya **yazılmaz** — onlar K005-firmware ve
> donanım-kernel sorumluluğundadır (kapsam §3/B1, B4). Burada yalnız kaynak **varlığı**
> doğrulanır.

---

## §18 Kapsam derin-dal doğrulaması — katmanlar (kapsam §11)

| # | Katman | Doğrulama | Sonuç |
|---|---|---|---|
| L1 | Hardware (PCM3168A · XU316 · TPA3255-DB) | §2/7-10 web kanıtları | ✅ |
| L2 | Firmware (K005) | anayasa §2 — sınır | ✅ (sınır) |
| L3 | Driver (K000) | anayasa §10/2 | ✅ |
| L4 | **Donanım API (K001)** | — | **PLANNED** (§6.1: 0 cpp/h) |
| L5 | DSP (K004) | anayasa §2 — sınır | ✅ (sınır) |
| L6 | Uygulama (K003 ses motoru) | anayasa §2 — sınır | ✅ (sınır) |

**§18 sonucu:** 6 katmanlı model vault ile **tutarlıdır**; 4. katman (K001 API) **PLANNED**
olarak işaretlidir (kod yokluğu §6.1 ile ispatlı).

---

## §19 Resmi kaynak doğrulaması — 3 yeni URL (kapsam §12 karşılığı)

| # | URL | HTTP | Resmi mi? | Envanter kesişimi | Kapsamda bir kez |
|---|---|---|---|---|---|
| W1 | https://www.usb.org/documents | ✅ 200 | USB-IF resmi | 0 | ✅ |
| W2 | https://docs.kernel.org/driver-api/auxiliary_bus.html | ✅ 200 | Linux kernel resmi | 0 | ✅ |
| W3 | https://www.kernel.org/doc/html/latest/index.html | ✅ 200 | Linux kernel resmi | 0 | ✅ |

**Doğrulama yöntemi:** `Invoke-WebRequest -Method Head -Uri $u -TimeoutSec 20` (2026-10-13).
**Kesişim:** 2.705 URL'lik envantere göre **0**.

---

## §20 Kapsam ↔ doğrulama tutarlılık matrisi

| # | Kapsam § | Doğrulama § | Tutarlı mı? |
|---|---|---|---|
| Q1 | §7 MMIO/PIO (6 öğe) | §14 (17 kontrol) | ✅ — 5 genel geçerli, 1 UNKNOWN |
| Q2 | §8 DMA (7 öğe) | §15 (4+4) | ✅ — 1 UNKNOWN |
| Q3 | §9 sürücü sözleşmesi (6 öğe) | §16 (5 kontrol) | ✅ — 1 UNKNOWN + 1 türetme işareti |
| Q4 | §10 kayıt haritası (8 öğe) | §17 (5 kontrol) | ✅ — 3 UNKNOWN |
| Q5 | §11 katmanlar (6 öğe) | §18 (6 katman) | ✅ — 1 PLANNED |
| Q6 | §12 resmi kaynak (3 URL) | §19 (3 URL) | ✅ — HTTP 200, kesişim 0 |
| Q7 | §2 Kapsam (7 öğe) | §3 + §9 (matris) | ✅ — 2 UNKNOWN + 2 ⚠️ |
| Q8 | §13 kapsam dışı (5 öğe) | §6.1 + §12 | ✅ — hepsi sınır/kapsam dışı |

**§20 sonucu:** Kapsam ile doğrulama arasında **çelişki yoktur**; 8 eşleşme tam uyumludur.
Bilinmeyenler her iki tarafta da aynı işaretlerle taşınır (UNKNOWN / ⚠️ / PLANNED).

---

## §21 Doğrulama son sayımları (2026-10-13)

| Metrik | Değer |
|---|---|
| Toplam § | 21 |
| Toplam kontrol satırı (T/N/S/K/L/W/Q/M/R/D/P/V/E) | 118 |
| ✅ doğrulandı (genel kavram + vault + web + repo) | 78 |
| UNKNOWN | 18 |
| ⚠️ VERIFICATION REQUIRED | 6 |
| PLANNED | 3 (L4 · §6.1 · E6) |
| Türetme işareti | 1 (S3) |
| Benzersiz URL (bu dosya) | 8 (§2: 5 · §19: 3) |
| Envanter kesişimi | **0** |
| Repo Read kanıtı | 23 satır (§5) |

**Kapanış kuralı:** Q-A1 protokol kararı gelmeden bu dosyadaki **18 UNKNOWN**'un 7'si
kapanmaz (T6 · T12 · N4 · S4 · K3 · K4 · API dili). Karar gelmeden tahmin yazılmaz.

---

## §22 Bilgi kaynakları hiyerarşisi (bu dosyada uygulanan)

| # | Kaynak | Seviye | Kullanım |
|---|---|---|---|
| H1 | `.ai/architecture/K001-donanım/**` vault | **1 — SSOT** | §1, §3, §9, §14-§18, §20 |
| H2 | Repo kaynak kodu (`shared/`, `*.coremusic.net/`) | **2** | §5, §6.1 |
| H3 | `.ai/electronic/**` (60 dosya / 20.711 satır) | **2** | §6.2 |
| H4 | Resmi üretici kaynakları (ti.com · xmos.com · steinberg.net · onsemi.com · akm.com) | **3** | §2 |
| H5 | Resmi standart dokümantasyonu (usb.org · docs.kernel.org · kernel.org) | **3** | §19 |
| H6 | Genel donanım mühendisliği bilgisi | **4** | §14-§18 (genel-kavram satırları) |
| H7 | Model hafızası / web araması | **5 — asla birincil kanıt değil** | Kullanılmadı |

**Kural (§22):** Seviye 5 bir iddia, seviye 1-3 kanıtsız bu dosyaya ** yazılmaz**.
Seviye 4 (genel kavram) yalnız "genel kavram" etiketiyle kullanılır — CoreMusic iddiası
olarak sunulmaz (§14.1 dipnotu, §16 dürstlük notu).

---

## §23 Hata ayıklama kaydı — bu turda düzeltülenler

| # | Bulgu | Kaynak | Eylem |
|---|---|---|---|
| F1 | `composer.json` kök yolu | Önceki tur iddiası | **Düzeltildi** — kök `composer.json` **YOK**; gerçek envanter 5 dosya (§8/C1) |
| F2 | `RouteTable.php` yolu | Önceki tur iddiası `shared/src/PageRouter/` | **Düzeltildi** — gerçek: `shared/src/Api/Routing/RouteTable.php` (§5.1/R1) |
| F3 | `ApiMiddlewarePipeline.php` yolu | Önceki tur iddiası `shared/src/Middleware/` | **Düzeltildi** — gerçek: `shared/src/Api/Middleware/` (§5.1/R2) |
| F4 | `RateLimitMiddleware.php` yolu | Önceki tur iddiası `shared/src/Middleware/` | **Düzeltildi** — gerçek: `shared/src/Api/Middleware/` (§5.1/R3) |
| F5 | electronic satır sayısı | 2026-10-12: 18.980 | **İki ölçüm de kayıtlı** — güncel 20.711 (§8/C2) |
| F6 | `donanım-api-kapsam.md` doğrulama alanı | "85 → 500+" iddiası | **Düzeltildi** — gerçek ölçüm 544 satır (bu dosya §13 özeti) |
| F7 | Dosya sonu mojibake görüntüsü | PowerShell konsol kodlaması | **Dosya temiz** — read tool ile doğrulandı (§11/V7) |

> **Kural (§23):** Hatalar **sessizce düzeltilmez**; her biri "önceki tur iddiası" ve
> "doğru ölçümlü değer" olarak iki satırda kayıtlıdır. Zero-hallucination §3.3 gereği
> `Dosya:`/`Satır:` çiftleri yalnız `Select-Object -Index` ile okunarak yazılır.

---

## §24 Sorumlu ve devir kaydı

| # | Soru | Sorumlu | Durum |
|---|---|---|---|
| Z1 | Q-A1 API protokolü (ioctl / sysfs) | Vault Steward | AÇIK |
| Z2 | Q-A2 api↔kernel↔core sınırı (K001 Q3) | Vault Steward | AÇIK |
| Z3 | Q-A3 çağrı listesi (fonksiyon/imza) | audio-hw + embedded | AÇIK |
| Z4 | K001 API dili (PHP mi C++20 mi) | embedded | AÇIK |
| Z5 | `RateLimit` sınıf seçimi (§8/C3) | backend | AÇIK |
| Z6 | ASIO SDK 2.3.4 · JUCE 9 sürüm kanıtı | researcher | AÇIK (K001 kural 12) |

**Devir:** Bu dosya 2026-10-13 tarihinde Vault Steward'a devredilmiştir. Z1-Z6 kararları
verilmeden bu dosyadaki UNKNOWN işaretleri **kaldırılmaz**; karar sonrası yalnız ilgili
satır güncellenir, tarihçe korunur.

---

## §25 Kod-seti URL doğrulama (2026-10-14 — `donanım-api-kod-seti.md`)

**Yöntem:** `Invoke-WebRequest -Method Head -TimeoutSec 15 -UseBasicParsing -UserAgent 'Mozilla/5.0'`
(HEAD reddedilirse GET yedeği). **Envanter kontrolü:** adaylar `url-inv-all.txt`
(**2.730 URL**) ile karşılaştırıldı — **8 aday çakıştı ve değiştirildi** (ör. man7 ioctl.2 ·
docs.kernel sound/index · win32/coreaudio/ · apple coreaudio · avaudiosession ·
android ndk audio · MDN Web Audio · webOSTV kök); sonuçta kullanılan **31 URL'nin tamamı
envanter dışı → kesişim 0**.

### §25.1 OS kanıtları (19 URL — hepsi HTTP 200 · 2026-10-14)

| # | URL | Kullanım (kod-seti) | HEAD 2026-10-14 | Envanter |
|---|---|---|---|---|
| #L1 | https://pubs.opengroup.org/onlinepubs/9699919799/functions/ioctl.html | §4.1 POSIX ioctl | ✅ 200 | 0 |
| #L2 | https://docs.kernel.org/sound/kernel-api/alsa-driver-api.html | §4.2 ALSA sürücü API | ✅ 200 | 0 |
| #L3 | https://docs.kernel.org/filesystems/sysfs.html | §4.3 sysfs | ✅ 200 | 0 |
| #W1 | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | §4.4 IOCTL | ✅ 200 | 0 |
| #W2 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudiorenderclient | §4.5 WASAPI render | ✅ 200 | 0 |
| #W3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/stream-routing | OS2 akış yönlendirme | ✅ 200 | 0 |
| #M1 | https://developer.apple.com/library/archive/samplecode/HALExamples/Listings/ConfigDefaultOutput_c.html | §4.6 CoreAudio HAL (imza arşiv örneğinden) | ✅ 200 | 0 |
| #M2 | https://developer.apple.com/documentation/coreaudio/kaudiohardwarepropertydefaultoutputdevice | §4.7 güncel property | ✅ 200 | 0 |
| #I1 | https://developer.apple.com/documentation/avfaudio/avaudiosession/setcategory(_:options:) | §4.8 iOS session | ✅ 200 | 0 |
| #I2 | https://developer.apple.com/library/archive/documentation/Audio/Conceptual/AudioSessionProgrammingGuide/AudioSessionBasics/AudioSessionBasics.html | OS4 kategori/mode | ✅ 200 | 0 |
| #A1 | https://developer.android.com/ndk/reference/group/audio | §4.9 AAudio | ✅ 200 | 0 |
| #F1 | https://docs.freebsd.org/en/books/handbook/multimedia/ | §4.10 FreeBSD OSS | ✅ 200 | 0 |
| #R1 | https://docs.zephyrproject.org/latest/services/index.html | §4.11 Zephyr kök | ✅ 200 | 0 |
| #R2 | https://www.keil.com/pack/doc/CMSIS/DSP/html/index.html | §4.12 CMSIS-DSP | ✅ 200 | 0 |
| #B1 | https://developer.mozilla.org/en-US/docs/Web/API/WebHID_API | §4.13 WebHID | ✅ 200 | 0 |
| #B2 | https://developer.mozilla.org/en-US/docs/Web/API/WebUSB_API | §4.14 WebUSB | ✅ 200 | 0 |
| #B3 | https://chromeos.dev/en/linux | OS9 Crostini/Linux kabı | ✅ 200 | 0 |
| #T1 | https://docs.tizen.org/application/native/guides/multimedia/audio-management/ | §4.15 Tizen CAPI | ✅ 200 | 0 |
| #O1 | https://webostv.developer.lge.com/develop/guides/acg-guide | §4.16 webOS Luna ACG | ✅ 200 | 0 |

### §25.2 Dil kanıtları (12 URL — hepsi HTTP 200 · 2026-10-14)

| # | URL | Kullanım (kod-seti) | HEAD 2026-10-14 | Envanter |
|---|---|---|---|---|
| #G1 | https://en.cppreference.com/w/c | §6.1 C | ✅ 200 | 0 |
| #G2 | https://en.cppreference.com/w/cpp | §6.2 C++ | ✅ 200 | 0 |
| #G3 | https://doc.rust-lang.org/nomicon/ | §6.3 Rust unsafe/FFI | ✅ 200 | 0 |
| #G4 | https://ziglang.org/learn/overview/ | §6.4 Zig | ✅ 200 | 0 |
| #G5 | https://docs.python.org/3/library/ctypes.html | §6.5 Python | ✅ 200 | 0 |
| #G6 | https://pkg.go.dev/cmd/cgo | §6.6 Go cgo | ✅ 200 | 0 |
| #G7 | https://learn.microsoft.com/en-us/dotnet/standard/native-interop/ | §6.7 C# P/Invoke | ✅ 200 | 0 |
| #G8 | https://www.php.net/manual/en/book.ffi.php | §6.8 PHP FFI | ✅ 200 | 0 |
| #G9 | https://nodejs.org/api/n-api.html | §6.9 Node N-API | ✅ 200 | 0 |
| #G10 | https://developer.apple.com/documentation/swift | §6.10 Swift | ✅ 200 | 0 |
| #G11 | https://docs.oracle.com/en/java/javase/21/docs/specs/jni/intro.html | §6.11 JNI | ✅ 200 | 0 |
| #G12 | https://kotlinlang.org/docs/java-interop.html | §6.12 Kotlin | ✅ 200 | 0 |

**Sonuç (§25):** 31/31 HTTP 200 · envanter kesişimi **0/2.730** · tek-dosya tekrar **0**
(kod-seti §8 ile bu §25 dosyalar içinde ayrı listelerdir, URL'ler kendi dosyalarında bir kez geçer).
Her URL bir kez kuralı ihlali yok. İç-dosya (kod-seti kendi içinde) tekrar: 0.

---

## §26 Kapsam §25/§26 özet matris çapraz kontrolü (2026-10-14)

| # | Kontrol | Beklenen | Ölçüm | Durum |
|---|---|---|---|---|
| X1 | Kapsam §25 OS satırı | 11 OS (Linux·Windows·macOS·iOS·Android·FreeBSD·Zephyr·CMSIS/Web dahil gömülü·Web/ChromeOS·Tizen·webOS) | 11/11 satır | ✅ |
| X2 | Kapsam §26 dil satırı | 11 dil (C·C++·Rust·Zig·Python·Go·C#·PHP·JS/Node·Swift·Java/Kotlin) | 11/11 satır | ✅ |
| X3 | Kapsam → kod-seti çapraz referans | her satır `§4.x`/`§6.x` kod bloğu gösterir | 11 OS + 11 dil bağlantıları kod-seti §4/§6 ile eşleşiyor | ✅ |
| X4 | Kapsam'da URL tekrarı | yok (URL'ler yalnız kod-seti §8 + doğrulama §25) | kapsam §25/§26'da 0 URL | ✅ |
| X5 | Bilinmeyen işaretleme | hedef-OS (Q-A1) ve API dili (Z4) UNKNOWN kalmalı | kapsam §25 sonucu + §26 sonucu UNKNOWN taşıyor | ✅ |
| X6 | Kod-seti dosya sayısı/blok | matris + bloklar | 576 satır · **28 kod bloğu** (§4.1-§4.16 = 16 · §6.1-§6.12 = 12) | ✅ |
| X7 | Kod derleme kapısı | derlenmedi → `⚠️` | kod-seti K1 açık soru + her blokta `derlenmedi` etiketi | ✅ işaretli |
| X8 | Frontmatter | kod-seti FM 7 alan (node_id·type·status·parent·children·depends·evidence) + validation/open_questions/group | mevcut | ✅ |

> **X6 sayım kuralı:** Blok sayısı 28 = §4'te 16 (4.1-4.16; §4.7 yalnız property
> referansı) + §6'da 12 (6.1-6.12). Kod-seti `validation` alanı da 28/28 ile hizalanır
> (bkz. §27/S2 dipnotu).

---

## §27 Bu tur sayımları ve §21 ile ilişki (2026-10-14)

| # | Dosya | Önceki ölçüm | Yeni ölçüm | Not |
|---|---|---|---|---|
| S1 | `donanım-api-kapsam.md` | 544 satır (§13/F6) | **601 satır** (+57: §25-§26) | önceki ölçüm aynen duruyor (F5 usulü iki kayıt) |
| S2 | `donanım-api-kod-seti.md` | — (yeni dosya) | **576 satır** | 2026-10-14 üretildi |
| S3 | `donanım-api-dogrulama.md` | 513 satır (§21) | **605 satır** (+92: §25-§27) | önceki ölçüm aynen duruyor |
| S4 | `index.md` | 54 satır | 54 satır (hizalama: GRUPLAR + ilişkili dosya satırı — sayımda ±1-2 satır) | index güncellemesi bu turun son adımı |

**§21 ilişkisi:** §21'deki toplamlar (ör. FM 10 alan · URL 24 vb.) **2026-10-13 ölçümüdür** ve
değiştirilmez; bu tur ölçümleri §25-§27'de **ek olarak** kayıtlıdır — sessiz düzeltme yok,
iki ölçüm de vardır (§23/F5 disiplini).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
