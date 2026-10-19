---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime
children:
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/anayasa
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/surucu
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/ornekler
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - php.net/manual/en/opcache.installation.php yanıt kodu 2026-10-13 (HTTP 200)
  - php.net/manual/en/opcache.setup.php yanıt kodu 2026-10-13 (HTTP 200)
  - php.net/manual/en/install.fpm.php yanıt kodu 2026-10-13 (HTTP 200)
  - shared/src/Bootstrap/RuntimeBootstrap.php (21 satır)
  - home.coremusic.net/index.php:10-29
  - home.coremusic.net/config/constants.php:10-32
  - https://developer.apple.com/documentation/foundation/process
validation: "web: OPcache kurulum/setup ve PHP-FPM kurulum dokümanları HEAD ile 2026-10-13 doğrulandı (HTTP 200). disk: RuntimeBootstrap / index.php / constants.php satırları Read ile birebir okundu."
open_questions:
  - "R5: opcache/APCu warm-up boot'ta yapılıyor mu? Yalnız DEBUG_MODE korumalı opcache_reset görüldü. [UNKNOWN]"
  - "R6: Kapanış drenaç (fastcgi_finish_request) kullanılıyor mu? Repo'da gözlenmedi. [UNKNOWN]"
  - "R3: FPM mi, Apache mod_php mi? Boot sırası SAPI'dan bağımsız yazılmış; php.ini kaynakları SAPI'ya göre ayrışır. [⚠️ VERIFICATION REQUIRED]"
group: mimari
---

# baslangic-ve-bootstrap

> **Kapsam (tek cümle):** CoreMusic PHP uygulamasının **başlangıç (boot) sırası** — autoloader → ortam sabitleri → OPcache kararı → `RuntimeBootstrap` ini/hata raporlama profili → uygulama bootstrap (DI + router) → kapanış (shutdown) evresi.
> Bu alt-dal Kademe-3'tür (kök `isletim-sistemi-runtime` altında); üç içerik dosyası sınır (anayasa), yürütme (surucu) ve kanıt (ornekler) katmanını taşır.

## §0 ELI15

PHP uygulaması her istekte aynı sırayla başlar: autoloader → ortam sabitleri (fail-fast) → OPcache kararı → `RuntimeBootstrap` → DI + router → shutdown; SAPI (mod_php vs FPM) ayrımı belirsizliği `⚠️` ile işaretlidir.

## İçerik Dosyaları

| Dosya | Grup | Ne içerir |
|---|---|---|
| [`anayasa.md`](anayasa.md) | mimari | Boot sırası kuralları, OPcache/ini sınırı, hata raporlama politikası, kapanış evresi |
| [`surucu.md`](surucu.md) | mimari | Boot haritası (dosya → adım), handler eksikliği, hata modları, ölçüm |
| [`ornekler.md`](ornekler.md) | kod | Gerçek repo kodu: `index.php` boot, `constants.php` fail-fast, `RuntimeBootstrap` (21) |

## Katman Konumu

| Katman | Repo'da | OS düzeyi |
|---|---|---|
| Giriş noktası | ✅ `home.coremusic.net/index.php:10-29` | Worker process istek başlangıcı |
| Ortam sabitleri | ✅ `constants.php:10-32` (fail-fast) | `.env` dosya okuması |
| Ini/hata profili | ✅ `RuntimeBootstrap.php:1-21` | php.ini (SAPI'ya göre) |
| OPcache | ⚠️ yalnız `opcache_reset` (R5) | Paylaşımlı bytecode cache |
| Kapanış | ⚠️ `exit` var, shutdown kaydı yok (R6) | `exit(3)` → atexit → son |

## Açık Sorular

R5 warm-up · R6 kapanış drenaç · R3 mod_php-vs-FPM (ini kaynağı) — üçü de [UNKNOWN] / [⚠️ VERIFICATION REQUIRED]; ayrıntı `anayasa.md` §8.

## Komşu Sınır Matrisi

| Komşu | Paylaşılan sınır | Bu alt-dalın sahibi | Komşunun sahibi |
|---|---|---|---|
| `surec-ve-parcacik` | Process başına boot | Boot, process başı bir kez | `surec-ve-parcacik` süreç semantiği |
| `web-runtime-topolojisi` | Sunucu → PHP devri | SAPI bağımsız boot sırası | `web-runtime-topolojisi` topoloji |
| `isletim-sistemi-core` | Autoloader/sınıf yükleme | PSR-4 boot tetikleyicisi | `core` sınıf yaşam döngüsü |
| `isletim-sistemi-api` | Route/kernel boot | `bootstrap.php` kernel kurulumu | `api` route/dispatch |
| `isletim-sistemi-security` | Hata sızıntısı kararı | `display_errors` profili | `security` hata politikası |
| `isletim-sistemi-observability` | Hata günlüğü | `log_errors` + `error_log` | `observability` toplama |
| `isletim-sistemi-testing` | CLI boot ortamı | `apcu.enable_cli` dahil boot | `testing` PHPUnit harness |
| `K011-application` | Uygulama boot bağımlılığı | Boot zinciri son adımı | `K011` uygulama mantığı |

## Web Research

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PHP OPcache kurulum dokümanı `opcache.enable` / JIT bayraklarını tanımlar; `RuntimeBootstrap`'in `ini_set` çağrıları ve `opcache_reset` bloğu bu yapılandırma yüzeyini kullanır (R5 iddiası). | `https://www.php.net/manual/en/opcache.installation.php` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| PHP'nin opcache için ayrı kurulum/setup bölümü resmi el kitabında yer alır; runtime ısınlama (warm-up) dayanağı bu bölümdür (R5 iddiası). | `https://www.php.net/manual/en/opcache.setup.php` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| PHP-FPM kurulumu resmi PHP el kitabı bölümünde anlatılır; FPM SAPI'sında php.ini kaynağı ve boot sırası bu bölüme bağlıdır (R3 iddiası). | `https://www.php.net/manual/en/install.fpm.php` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |

---

## § OS GRUPLAR

> 8 işletim sistemi grubu — boot/runtime başlatma modeli ve bu alt-dalın (PHP bootstrap A1-A7) karşılığı. Tam karşılaştırma, OS.1-OS.3 uzun açıklamalar ve OS bazlı kod blokları içerik dosyalarındadır: `anayasa.md § OS` (PHP kurulum/boot · launchd · fork+exec) · `surucu.md § OS` (systemd · Android) · `ornekler.md § OS` (iOS · Tizen · webOS).

| # | OS | Boot/runtime başlatma modeli | Bu dosyadaki karşılığı | Kaynak |
|---|---|---|---|---|
| 1 | Windows | UEFI → winload → NT kernel → SCM; PHP IIS/FastCGI (`php-cgi`) | § Katman Konumu — worker process boot | `anayasa.md` § OS |
| 2 | macOS | boot.efi → XNU → launchd (PID 1); `php-fpm` LaunchDaemon job'ı | §0 ELI15 — A1-A7 (SAPI bağımsız) | `anayasa.md` § OS |
| 3 | iOS | iBoot → XNU → launchd → SpringBoard; uygulama `UIApplicationDelegate` | kapsam dışı — bu dosya PHP boot'unu kapsar | https://developer.apple.com/documentation/foundation/process |
| 4 | Android | bootloader → Linux kernel → `init` → Zygote; öncelik ActivityManager | § Komşu Sınır — `surec-ve-parcacik` sınırı | `surucu.md` § OS |
| 5 | Linux | bootloader → Linux kernel → systemd (PID 1); `php-fpm.service` | §0 ELI15 — boot sırası A1-A7 | `anayasa.md` § OS |
| 6 | FreeBSD/BSD | loader → kernel → `init` (PID 1) → rc.d; PHP servisi rc.girişi | § Komşu Sınır — POSIX boot semantiği | `anayasa.md` § OS |
| 7 | Tizen | Linux çekirdekli; uygulama süreç yaşam döngüsü (process-model) | kapsam dışı — bu dosya PHP boot'unu kapsar | `ornekler.md` § OS |
| 8 | webOS OSE | Linux çekirdekli; servis katmanı Luna Service 2 (LS2) | kapsam dışı — bu dosya PHP boot'unu kapsar | `ornekler.md` § OS |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
