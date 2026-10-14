---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/index
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/index
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/anayasa
evidence: https://www.php.net/manual/en/function.ini-set.php · https://www.php.net/manual/en/function.error-reporting.php · https://www.php.net/manual/en/function.register-shutdown-function.php · api.coremusic.net/index.php:10-29 · api.coremusic.net/constants.php:10-25 · api.coremusic.net/Services/RuntimeBootstrap.php:1-21 · api.coremusic.net/bootstrap.php:97-106
validation: 2026-10-13 · 3 URL HTTP 200 (ini_set · error_reporting · register_shutdown_function) · repo satırları Read ile birebir · içerik 380-460 satır
open_questions: R5 opcache warm-up · R10 shutdown çalışma zamanı
group: mimari
---

# baslangic-ve-bootstrap-anayasa — Başlangıç ve Bootstrap Anayasası (Kademe-3)

## §0 ELI15

Uygulama, PHP isteğinin ilk anından ilk HTTP cevabına kadar **tek bir sırayla**
başlar: autoload → ortam değişkenleri → opcache → yapılandırma → bootstrap sınıfı
→ hata yönetimi → yönlendirici. Bu dosya o sıranın **anayasasını** yazar: hangi
adım hangi dosyadan beslenir, hangi adım başarısızsa uygulama neden
**fail-fast** ile durur ve hangi kural asla bozulmaz. Bootstrap = "uygulama
ayakta kalmadan önce yapılan her şey".

## §1 — Kimlik

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/anayasa` |
| node_type | `module` |
| status | `draft` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap/index` |
| children | (boş — yaprak) |
| depends_on | `baslangic-ve-bootstrap/index` · `web-runtime-topolojisi/anayasa` |
| group | `mimari` |
| Kademe | Kademe-3 (alt-dal içerik) |
| Kapsam | entry script sırası · env/constants · hata yönetimi · shutdown · fail-fast |

## §2 — Kavramlar ve Kurallar

### §2.1 — Adım sırası (A1-A7): sıra bozulmaz

| # | Adım | Dosya | Ne yapar |
|---|---|---|---|
| A1 | Autoload | `index.php:10` | `require vendor/autoload.php` — sınıf haritası |
| A2 | Ortam yükleme | `index.php:12-19` | `.env` okunur; `APP_ENV_MODE` yoksa 500 + exit |
| A3 | Sabitler | `constants.php:10-25` | `EnvParser::loadIntoEnv` → `DEBUG_MODE` türetilir |
| A4 | OPcache | `index.php:21-25` | `opcache_reset()` yalnız `DEBUG_MODE` true iken |
| A5 | Yapılandırma | `index.php:27` | `require app.php` (PDO bağları, servisler) |
| A6 | Bootstrap sınıfı | `index.php:29` | `RuntimeBootstrap::boot(DEBUG_MODE)` — timezone + hata |
| A7 | Kernel | `bootstrap.php:97-106` | `PageRouterKernel` yürütülür; Throwable → 500 |

**Kural A:** A1→A7 sırası **kutsaldır**; bir adımın çıktısı bir sonrakinin
girdisidir (ör. A3'te üretilen `DEBUG_MODE` A4 ve A6'yı belirler). Sıra değişirse
bootstrap sessizce yanlış davranır.

### §2.2 — EnvParser (M1): .env tek kaynak, iki yol

| Kural | İfade | Kaynak |
|---|---|---|
| Ayırıcı | Yalnız ilk `=` bölünür | `EnvParser.php:15-17` |
| Yorum | Satır `#` ile başlarsa atlanır | `EnvParser.php:13-14` |
| Eksik `=` | Satır atlanır (yoksayılır) | `EnvParser.php:17-18` |
| Tırnak | Sarmalayıcı tırnaklar kesilir | `EnvParser.php:20-25` |
| Dosya yok | Boş dizi döner; `putenv` çağrılmaz | `EnvParser.php:7-8, 37-40` |
| Ortam | `getenv()` çağrılmadan önce `loadIntoEnv` | `constants.php:10` |

**Kural M1:** `.env` dosyası sunucuda **repo kökünde, git dışında** yaşar
(gitignore'da); kod asla `.env` değerini literal gömmez.

### §2.3 — RuntimeBootstrap (M2): iki mod, tek geçiş

| Ayar | DEBUG true | DEBUG false | Kaynak |
|---|---|---|---|
| `date_default_timezone_set` | UTC | UTC | `RuntimeBootstrap.php:10-12` |
| `error_reporting` | `E_ALL` | `E_ERROR \| E_WARNING \| E_PARSE` | `RuntimeBootstrap.php:14-17` |
| `display_errors` | `1` | `0` | `RuntimeBootstrap.php:19` |
| `log_errors` | `1` | `1` | `RuntimeBootstrap.php:20` |

**Kural M2:** loglama **her iki modda da açıktır**; yalnız gösterim kapanır.
Üretimde `display_errors=0` + `log_errors=1` ihlal edilemez — hata,
isteyene değil, log'a gider.

### §2.4 — Hata yönetimi (M3): iki perde

| Perde | Fonksiyon | Kapsam | Sınır |
|---|---|---|---|
| Perde 1 | `set_error_handler` | `E_WARNING`/`E_NOTICE`/`E_USER_*` sınıfı | `E_ERROR`/`E_PARSE`/`E_COMPILE_*` **yakalanamaz** (PHP 8+) |
| Perde 2 | `set_exception_handler` | `Throwable` (Error + Exception) | Son çare; sonra süreç biter |
| Perde 3 | `register_shutdown_function` | `fatal error` + `exit` | `error_get_last()` son hatayı raporlar |

**Kural M3:** handler zinciri **ilk boot'ta bir kez** kurulur; her istekte tekrar
kayıt (`SessionBootstrapper` idempotentlik örneği gibi) yasaktır.

### §2.5 — Fail-fast (M4): bilinmeyen ortam = 500

| Koşul | Sonuç |
|---|---|
| `APP_ENV_MODE` `.env`'de yok | HTTP 500 + `exit` (çalışma devam etmez) |
| `.env` dosyası hiç yok | `EnvParser` boş dizi → M4 tetiklenir |
| `APP_ENV_MODE` bilinmeyen değer | Uygulama **varsayılan moda düşmez**, 500 |
| PDO dsn eksik | `app.php` içinde `LogicException` fırlatılır |

**Kural M4:** belirsizlik sessizce yutulmaz; ya açık değer ya 500. Bu, anayasanın
en yüksek öncelikli kuralıdır (zero-hallucination'ın çalışma zamanı karşılığı).

### §2.6 — Shutdown (M5): kapanış evresi

PHP, istek sonunda **shutdown fonksiyonlarını** çalıştırır (`exit` dâhil);
burada kayıtlı fonksiyonlar son kez çağrılır. Bootstrap üç kaydı bu evreye
yazarsa: hata loglama, oturum kapanışı, sızıntı sayımı. **Kural M5:** shutdown
fonksiyonu **asla** yeni HTTP cevabı üretmez; yalnız kaydeder/temizler.

### §2.7 — Composer autoload (M6): sınıf haritası

| Alan | Kural | Kaynak |
|---|---|---|
| Giriş | `require vendor/autoload.php` boot'un **ilk satırıdır** (A1) | `index.php:10` |
| Ne sağlar | PSR-4 sınıf→dosya eşlemesi; `class_exists` lazy yükleme | Composer dokümantasyonu |
| Başarısızlık | `vendor/` yoksa (kurulmamışsa) PHP fatal: dosya bulunamadı | PHP `require` semantiği |
| Kural | `vendor/` commit edilmez; CI'da `composer install` ile üretilir | `.github/workflows/ci.yml` |
| Sınır | Autoload **yalnız sınıf yükler**; sabit/env/bootstrap yapmaz | anayasa A1 |

**Kural M6:** autoload boot'un altyapısıdır; uygulama mantığı autoload sonrası
adımlara (A2-A7) aittir — `vendor/autoload.php` içine ya da `composer.json`
`scripts`'ına uygulama mantığı yazılmaz.

### §2.8 — Uygulama bağlayıcıları (M7): app.php · bootstrap.php

| Dosya | Görev | İçerik | Başarısızlık |
|---|---|---|---|
| `app.php` | Servisler | PDO bağlantısı (dsn zorunlu), konteyner, adaptörler | dsn eksik → `LogicException` |
| `bootstrap.php` | Kernel + rota | `/health` erken çıkışı (§3.2 E-4), route yükleme, `handle` | Throwable → 500 (§3.2 E-7) |
| `config.php` | Yapılandırma | Ayar tabloları | tanımsız anahtar → sonraki adımda hata |

**Kural M7:** `app.php` **boot'ta bir kez** çağrılır (istek başına değil —
A5, opcache ile önbelleklenir); `bootstrap.php` her istekte çalışır çünkü
kernel çağrısı istek-bağımlıdır. Bu ayrım, "boot = process-öncesi/ilk, kernel
= istek" çizgisidir (A4 koruması ile aynı disiplin).

### §2.9 — Bootstrap karar ağacı (tek bakışta)

```
index.php giriş
  ├─ vendor/autoload.php yok? → fatal (require) — CI "composer install" hatası
  ├─ APP_ENV_MODE tanımsız?  → 500 + exit (M4)
  ├─ DEBUG_MODE türetme      → üretim dışı = true (§3.2 E-2)
  ├─ opcache_reset           → yalnız DEBUG_MODE (A4)
  ├─ app.php PDO dsn eksik?  → LogicException → perde 2 → 500
  ├─ RuntimeBootstrap::boot  → M2 + M3 (A6)
  └─ bootstrap.php kernel    → Throwable → perde 2 → 500 (§3.2 E-7)
```

### §2.10 — .env güvenlik kuralları (M1'in güvenlik yüzeyi)

| # | Kural | Neden | Kanıt |
|---|---|---|---|
| 1 | `.env` `.gitignore`'dadır | Secret repo'ya girmez | `.gitignore` |
| 2 | Kod `.env` değerini literal gömmez | Ortam değişiminde kırılganlık (AP-7) | M1 |
| 3 | `.env` kök dizindedir; kopya okunmaz | Tek kaynak | `constants.php:10` |
| 4 | `.env` yüklenemezse boot **durmaz** (yalnız `APP_ENV_MODE` zorunlu) | M4 yalnız bilinmeyen **modu** durdurur | `EnvParser.php:37-40` |
| 5 | `.env` log'a yazılmaz | Secret sızıntısı | OWASP kuralı |
| 6 | Üretimde `display_errors=0` (M2) | Yol/SQL sızıntısı | `RuntimeBootstrap.php:19` |
| 7 | Hata mesajı cevaba yansımaz; loga gider | M2 + perde 2/3 | `bootstrap.php:106` |

### §2.11 — PHP eklenti/enstrüman bağımlılıkları

| Eklenti | Boot'taki rolü | Koruma | Kanıt |
|---|---|---|---|
| `opcache` | A4 (`opcache_reset`) | `function_exists` koruması | `index.php:22-23` |
| `apcu` | Rate-limit önbelleği (komşu dal) | `apcu_enabled()` devri | `api/index.php:143-145` |
| `pdo_mysql` | A5 veritabanı | `app.php` dsn kontrolü | `app.php` |
| `mbstring` | UTF-8 işleme | runtime zorunlu | `composer.json` |
| `json` | `E-4` health cevabı | runtime zorunlu | `bootstrap.php:17` |

**Kural:** boot kodu her eklentiyi `function_exists` ile **korur**; eklenti
yoksa uygulama sessizce düşmez, açık hata üretir (M4 ruhu).

### §3 — Akış ve Anti-Pattern

### §3.1 — Başlangıç akışı (tek bakışta)

```
HTTP isteği → web sunucusu (FPM: FastCGI :9000)
  → index.php
     [A1] vendor/autoload.php
     [A2] EnvParser::loadIntoEnv(.env)
            └─ APP_ENV_MODE yoksa → 500 + exit  (M4)
     [A3] define(DEBUG_MODE, ...)  (constants.php:22-25)
     [A4] opcache_reset()  yalnız DEBUG_MODE  (index.php:21-25)
     [A5] require app.php  (PDO prepare zorunlu)
     [A6] RuntimeBootstrap::boot(DEBUG_MODE)  (M2 + M3)
     [A7] require bootstrap.php → PageRouterKernel (bootstrap.php:97)
            └─ Throwable catch → log + HTTP 500 (bootstrap.php:106)
  → cevap → shutdown evresi (M5: register_shutdown_function)
```

### §3.2 — Anti-pattern tablosu

| ID | Anti-pattern | Sonuç | Doğrusu | Kanıt |
|---|---|---|---|---|
| AP-1 | `display_errors=1` üretimde | Kod sızıntısı (SQL, yol) | M2 tablosu: üretim `0` | `RuntimeBootstrap.php:19` |
| AP-2 | `error_reporting(E_ALL)` üretimde kapatma | Uyarılar yutulur, hata birikir | Üretimde `E_ERROR\|E_WARNING\|E_PARSE` | `RuntimeBootstrap.php:16-17` |
| AP-3 | `APP_ENV_MODE` yokken `null` kabul | Bilinmeyen ortamda yanlış davranış | 500 + exit (M4) | `constants.php:14-17` |
| AP-4 | `set_error_handler` ile `E_ERROR` yakalama | Hiç çağrılmaz (PHP 8+ sınırı) | Fatal için shutdown + exception handler | PHP docs: `set_error_handler` |
| AP-5 | Bootstrap'te `header()` sonrası output | "headers already sent" | Output tamponu; cevabı kernel'e bırak | PHP header() docs |
| AP-6 | `opcache_reset()` her istekte | Üretimde tam yeniden derleme | Yalnız `DEBUG_MODE` (A4) | `index.php:21-25` |
| AP-7 | `.env` değerini koda literal gömmek | Secret sızıntısı; ortam değişiminde kırılganlık | M1: `.env` tek kaynak, `EnvParser` | `EnvParser.php:7-40` |
| AP-8 | Shutdown'da `header()`/`echo` | "headers already sent" + shutdown kaybı | M5: shutdown yalnız kaydeder | register-shutdown docs |
| AP-9 | `require` yerine `require_once` zorunluluğu sanmak | İlk `require` fatal; `once` yalnız tekrarı önler | A1: `require` yeter, `once` gereksiz | `index.php:10` |
| AP-10 | `APP_ENV_MODE` değerini string vs int karıştırmak | `===` karşılaştırmasında fail-fast tetiklenmez | M4: tek kaynak `getenv` + trim | `constants.php:14-17` |

### §3.3 — Bootstrap zaman çizelgesi (bağlam içi)

| An | Ne olur | Kalıcılık |
|---|---|---|
| İlk istek (cold) | A1-A7 tamamı; opcache boş → derleme | kod önbelleği dolar |
| Sonraki istek (warm) | A1-A5 (opcache reset yok); A6-A7 | hızlı boot |
| Debug isteği | `opcache_reset()` → her istek cold | kasıtlı yavaşlık |
| Fatal | Perde 2/3 → 500; shutdown evresi | log kalıcı |
| `exit` (erken) | Shutdown evresi; kernel'e ulaşılmaz | `E-4` örneği |

## §4 — Ölçüm ve Test

| Kapı | Test | Beklenen | Sıklık |
|---|---|---|---|
| Sıra | `index.php` Read: A1-A7 satır sırası | Tablo ile birebir | her commit |
| Fail-fast | `.env`'den `APP_ENV_MODE` çıkarıp boot | HTTP 500, süreç durur | CI |
| Debug modu | `DEBUG_MODE=false` boot | `display_errors` = 0 (ini kontrolü) | CI |
| Log | Üretimde uyarı tetikle | Log dosyasında kayıt, cevapta yok | CI |
| Shutdown | `exit` sonrası shutdown sayacı | Kayıt bir kez çalışır | CI |
| Kernel catch | Bilinmeyen route | 500 + `throwable` log | CI |
| Autoload | `vendor/` olmadan boot | Fatal (require) — CI'da `composer install` | CI |
| PDO | Boş dsn ile `app.php` | `LogicException` | CI |
| Idempotent | `RuntimeBootstrap::boot` ×2 | İkinci çağrı zararsız (guard) | CI |
| Warm boot | Aynı istek ×2 süre karşılaştırma | İkinci istek daha hızlı (opcache) | deploy |

### §4.1 — Prosedür detayları (D-1..D-5)

| # | Senaryo | Adım | Beklenen | Kanıt |
|---|---|---|---|---|
| D-1 | Fail-fast | `.env`'den `APP_ENV_MODE` satırını sil | 500 + exit | `constants.php:14-17` |
| D-2 | Debug | `DEBUG_MODE=true` boot | `display_errors=1` + `opcache_reset` | `RuntimeBootstrap.php:19` |
| D-3 | Üretim | `DEBUG_MODE=false` boot | `display_errors=0`, `log_errors=1` | `RuntimeBootstrap.php:19-20` |
| D-4 | Fatal | `throw new Error('test')` | 500 + `[kernel]` logu | `bootstrap.php:106` |
| D-5 | Shutdown | `exit(0)` çağrısı | Shutdown kaydı çalışır | `register_shutdown_function` docs |

## §5 — Komşu Sınırı

- **Web topolojisi** (FastCGI :9000, `SCRIPT_FILENAME`) → `web-runtime-topolojisi/` — burada yalnız "FPM'e düşünce A1 başlar" denir.
- **Süreç/parçacık** (FPM worker ömrü, fork) → `surec-ve-parcacik/` — bootstrap istek-ömrü içindedir, süreç yaşam döngüsü değildir.
- **OPcache ısıtma** → `surucu.md` R5 (açık).
- **Çekirdek PHP kavramları** → `isletim-sistemi-core/` ağacı.

## §6 — Terimler ve Kanıt Cümleleri

| Terim | Anlam | Kanıt cümlesi |
|---|---|---|
| Bootstrap | İlk HTTP cevabına kadar her şey | "A1→A7 sırası kutsaldır" (§2.1) |
| Fail-fast | Bilinmeyen = dur, yutma | "`APP_ENV_MODE` yoksa 500 + exit" (`constants.php:14-17`) |
| Shutdown evresi | `exit` sonrası son fonksiyonlar | "PHP, istek sonunda kayıtlı fonksiyonları çağırır" (register-shutdown docs) |
| Idempotent boot | İkinci kez çağırmak zararsız | `SessionBootstrapper.php:19-28` |
| Fail-open | Hatayı gizleme (yasak) | M4: "sessizce düşme yok" |
| Cold/warm | İlk istek (derleme) vs sonraki (önbellekli) | §3.3 zaman çizelgesi |
| Literal gömme | `.env` değerinin koda yazılması | AP-7: yasak, M1 tek kaynak |

### §6.1 — Repo kanıt blokları

**Kanıt-1 — Entry script sırası (`api.coremusic.net/index.php:10-29`)**

```php
require vendor/autoload.php;               // A1
require constants.php;                     // A2-A3 (EnvParser içeride)
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    if (function_exists('opcache_reset')) {
        opcache_reset();                   // A4 — yalnız debug
    }
}
require app.php;                           // A5 — PDO dsn zorunlu
RuntimeBootstrap::boot(DEBUG_MODE);        // A6 — M2 + M3
require bootstrap.php;                     // A7 — kernel (E-7)
```

**Kanıt-2 — Fail-fast koruması (`api.coremusic.net/constants.php:14-17`)**

```php
$mode = $env('APP_ENV_MODE');
if ($mode === null) {
    http_response_code(500);
    exit;                                  // M4 — bilinmeyen ortam = dur
}
```

**Kanıt-3 — Hata perdesi (`Services/RuntimeBootstrap.php:14-20`)**

```php
error_reporting($debug ? E_ALL : (E_ERROR | E_WARNING | E_PARSE));
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');                // M2 — log her iki modda açık
```

**Kanıt-4 — Kernel catch (`bootstrap.php:97-106`)**

```php
$kernel = new PageRouterKernel($router, $container);
try {
    $kernel->handle($method, $path, $query, $body);
} catch (Throwable $e) {
    error_log('[kernel] ' . $e->getMessage());
    http_response_code(500);               // perde 2 — son çare
}
```

## §7 — Kapsam Dışı

Composer kurulumu · Docker/CI boot (`ci.yml` yalnız test kapısı) ·
PHP derleme/kurulum · sertifika/TLS boot · oturum içeriği ayrıntısı
(`SessionBootstrapper` yalnız idempotentlik örneği) · kuyruk/worker boot.

## §8 — Açık Sorular

| # | Soru | Durum |
|---|---|---|
| R5 | OPcache warm-up (`opcache_compile_file` ön ısıtma) uygulanacak mı? | `[UNKNOWN]` — repo'da yalnız `opcache_reset` (A4) var |
| R10 | Shutdown'da bekleyen iş/kuyruk drenacı | `[⚠️ VERIFICATION REQUIRED]` — shutdown kaydı repo'da tanımlı değil |

## §9 — Kaynakça

1. PHP Manual — ini_set
   https://www.php.net/manual/en/function.ini-set.php
   (Runtime yapılandırması: `display_errors`, `log_errors`, `max_execution_time`;
   `open_basedir`/`disable_functions` runtime değiştirilemez → §3.2 AP-5.)
2. PHP Manual — error_reporting
   https://www.php.net/manual/en/function.error-reporting.php
   (`E_ALL` PHP 8+ varsayılanı; `E_STRICT` PHP 8.4'te deprecated → M2 mod
   tablosu bu dokümantasyonla hizalı.)
3. PHP Manual — register_shutdown_function
   https://www.php.net/manual/en/function.register-shutdown-function.php
   (Kapanış evresine kayıt; `exit` dâhil her sonlandırmada çalışır → M5.)
   (Bu üç URL bu dalın `index.md`'sinden devralınmıştır; tekrar yazılmamıştır.)

## §10 — Disk Doğrulaması: `evidence:` yolları ↔ 2026-10-13 disk durumu

> Bu bölüm yalnız **okuma** sonucudur; §1-§9'daki hiçbir satır değiştirilmemiştir.
> Amaç: frontmatter'daki yol dizgesinin depoda hangi dosyayla eşleştiğini kaydetmek.

### §10.1 — Yol eşleşme tablosu

| # | `evidence:` dizgesindeki yol | Diskte var mı | Eşleşen gerçek dosya | Gözlem |
|---|---|---|---|---|
| 1 | `api.coremusic.net/index.php:10-29` | dosya var (223 satır) | `home.coremusic.net/index.php:10-29` (29 satır) | ⚠️ satır aralığı `home` giriş noktası ile birebir |
| 2 | `api.coremusic.net/constants.php:10-25` | dosya YOK | `home.coremusic.net/config/constants.php:10-25` (117 satır) | ⚠️ fail-fast bloğu bu aralıkta |
| 3 | `api.coremusic.net/Services/RuntimeBootstrap.php:1-21` | `Services/` dizini YOK | `shared/src/Bootstrap/RuntimeBootstrap.php:1-21` (21 satır) | ⚠️ satır sayısı 21 = 21 |
| 4 | `api.coremusic.net/bootstrap.php:97-106` | dosya YOK | `home.coremusic.net/config/bootstrap.php:97-106` (115 satır) | ⚠️ kernel bloğu bu aralıkta |
| 5 | `api.coremusic.net/` giriş noktası | var | `api.coremusic.net/index.php:14` `require_once __DIR__ . '/autoload.php';` | ilk `require` 14. satır |
| 6 | `api.coremusic.net/Services/` dizini | YOK | — | `[UNKNOWN]` — bu önekle eşleşen dizin 2026-10-13 diskte yok |
| 7 | `home.coremusic.net/config/` | var | `app.php` · `bootstrap.php` · `config.php` · `constants.php` | 4 dosya okundu |
| 8 | `shared/src/Bootstrap/` | var | `RuntimeBootstrap.php` tek dosya | sınıf `CoreMusic\Bootstrap` namespace'inde |

**Okuma notu:** satır aralıkları depodaki `home` + `shared` dosyalarıyla, **yol öneki**
ise diskte olmayan bir `api.coremusic.net/Services/` yapısıyla eşleşmektedir. Bu
çelişki **raporlanır, düzeltilmez** (mevcut §'ler değiştirilmez) → R11.

### §10.2 — Bootstrap zinciri sorumluluk matrisi (adım → dosya → satır)

| # | Adım | Dosya | Satır | Disk gözlemi |
|---|---|---|---|---|
| A1 | Autoload | `home.coremusic.net/index.php` | 10 | `require_once __DIR__ . '/autoload.php';` |
| A2 | `.env` yükleme | `home.coremusic.net/config/constants.php` | 10-15 | `APP_ENV_MODE` tanımsızsa `EnvParser::loadIntoEnv` |
| A3 | Sabit üretimi | `home.coremusic.net/config/constants.php` | 21-32 | `APP_ENV_MODE` + `DEBUG_MODE` + `APP_VERSION` + `APP_TIMEZONE` |
| A4 | OPcache koruması | `home.coremusic.net/index.php` | 14-16 | `DEBUG_MODE && function_exists('opcache_reset')` |
| A5 | Uygulama config | `home.coremusic.net/index.php` | 21 | `require __DIR__ . '/config/app.php';` |
| A6 | Hata perdesi | `home.coremusic.net/index.php` | 23 | `RuntimeBootstrap::boot(DEBUG_MODE);` |
| A6a | Perde gövdesi | `shared/src/Bootstrap/RuntimeBootstrap.php` | 7-20 | `date_default_timezone_set` · `error_reporting` · `ini_set` |
| A7a | Config nesneleri | `home.coremusic.net/index.php` | 26 | `require_once __DIR__ . '/config/config.php';` |
| A7b | Uygulama açılışı | `home.coremusic.net/index.php` | 29 | `require_once __DIR__ . '/config/bootstrap.php';` |
| A7c | Kernel kurulumu | `home.coremusic.net/config/bootstrap.php` | 97 | `new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);` |
| A7d | Rota çözücü | `home.coremusic.net/config/bootstrap.php` | 100-104 | `shared-infrastructure/config/routes.php` → `shared/config/routes.php` fallback |
| A7e | Kernel çağrısı | `home.coremusic.net/config/bootstrap.php` | 106 | `$kernel->handle($_SERVER, $_GET, $_POST, $routesFile);` |

## §11 — Giriş noktası satır haritası (`home.coremusic.net/index.php:1-29`)

| # | Satır | İçerik (birebir) | Karşılık |
|---|---|---|---|
| 1 | 1 | `<?php declare(strict_types=1);` | dil katmanı |
| 2 | 3-8 | dosya docblock (SPA router + auth include notu) | — |
| 3 | 10 | `require_once __DIR__ . '/autoload.php';` | A1 |
| 4 | 11 | `require_once __DIR__ . '/config/constants.php';` | A2-A3 |
| 5 | 13 | `/* --- Opcache temizleme (sadece geliştirme ortamı için) --- */` | A4 başlığı |
| 6 | 14 | `if (defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')) {` | A4 kapısı (üçlü koşul) |
| 7 | 15 | `opcache_reset();` | A4 eylemi |
| 8 | 16 | `}` | A4 kapanışı |
| 9 | 18 | `use CoreMusic\Bootstrap\RuntimeBootstrap;` | import |
| 10 | 21 | `$appConfig = require __DIR__ . '/config/app.php';` | A5 |
| 11 | 23 | `RuntimeBootstrap::boot(DEBUG_MODE);` | A6 |
| 12 | 26 | `require_once __DIR__ . '/config/config.php';` | A7a |
| 13 | 29 | `require_once __DIR__ . '/config/bootstrap.php';` | A7b — dosyanın son satırı |

**Sıra kanıtı:** A4 (satır 14-16), A6 (satır 23) ve A7b (satır 29) arasındaki
sıra dosya boyunca tekdüzedir; A6'dan **önce** `header()`/`echo` çağrısı dosyada yoktur.

## §12 — `constants.php` fail-fast ve türetme kuralları (satır kanıtlı)

| # | Satır | Kural | Bilinmeyen davranış |
|---|---|---|---|
| 1 | 10-15 | `APP_ENV_MODE` tanımsızsa `.env` bir kez yüklenir | ikinci `require` guard ile atlanır |
| 2 | 17-18 | `$env = static fn(...) => $_ENV[$key] ?? getenv($key) ?: $default` | boş dize `?:` ile default'a düşer |
| 3 | 21 | `if (!defined('APP_ENV_MODE'))` | tekrar tanımlama hatası önlenir |
| 4 | 22 | `$envMode = $env('APP_ENV_MODE', '');` | boş string default |
| 5 | 23 | beyazliste: `development` · `production` · `test` (`in_array(..., true)`) | başka değer = bilinmeyen ortam |
| 6 | 24-25 | `http_response_code(500); exit('Configuration error: APP_ENV_MODE not set');` | fail-fast — M4 |
| 7 | 27 | `define('APP_ENV_MODE', $envMode);` | tek define |
| 8 | 28 | `define('DEBUG_MODE', APP_ENV_MODE !== 'production');` | türetme kuralı (E-2) |
| 9 | 30 | `APP_VERSION` default `'2.3.0'` | env ile ezilir |
| 10 | 31 | `APP_TIMEZONE` default `'Europe/Istanbul'` | RuntimeBootstrap bu değeri okur |
| 11 | 40 | `$isProdEnv = ... APP_ENV_MODE === 'production';` | bypass bayrağı gate |
| 12 | 45-50 | bypass değişkenleri **yalnız üretim-dışı** okunur | üretimde hamilere hiç dokunulmaz |
| 13 | 51 | `define('FORCE_AUTH_BYPASS', !$isProdEnv && in_array(...))` | iki koşullu AND |
| 14 | 61-62 | `HEADER_PATH` · `FOOTER_PATH` tanımları | kernel kurulumunda kullanılır |
| 15 | 70-71 | `CSRF_TOKEN_LENGTH` 32 · `RATE_LIMIT_MAX/WINDOW` 60 default | env ile ezilir |
| 16 | 75-80 | `session.save_path` yoksa `@mkdir(..., 0750, true)` + `session_save_path()` | oturum dizini boot'ta açılır |
| 17 | 84 | `TRUSTED_PROXIES = ['127.0.0.1', '::1']` | sabit liste |
| 18 | 90-94 | `COREMUSIC_SCHEME` = HTTPS / `X-Forwarded-Proto` / port 443 tespiti | üç koşul OR |
| 19 | 98-100 | `AUTH_URL` · `MUSIC_URL` · `ASSETS_URL` default'ları | `COREMUSIC_SCHEME`'e bağlı |
| 20 | 105-111 | `DB_HOST` · `DB_HOME_NAME` · `DB_MUSIC_NAME` · `DB_PORT` 3306 · `DB_CHARSET` `utf8mb4` | dosya 117 satırda `APP_PEPPER` ile biter |

## §13 — `EnvParser` disk davranışı (`shared/src/Config/EnvParser.php:7-50`)

| # | Satır | Davranış | M1 ile ilişki |
|---|---|---|---|
| 1 | 9-11 | `is_file($envFile)` değilse `[]` döner | dosya yok ≠ hata |
| 2 | 14 | `file(..., FILE_IGNORE_NEW_LINES \| FILE_SKIP_EMPTY_LINES)` | boş satırlar baştan elenir |
| 3 | 15-17 | `file()` `false` dönerse `[]` | I/O hatası yutulur |
| 4 | 19-23 | `trim` + `''` veya `#` ile başlayan satır `continue` | yorum satırı veri sayılmaz |
| 5 | 24-26 | `str_contains($line, '=')` yoksa `continue` | `=` yoksa satır sessizce atlanır |
| 6 | 27 | `explode('=', $line, 2)` | değer içindeki `=` korunur |
| 7 | 28-29 | `$key`/`$value` trim edilir | boşluk temizliği |
| 8 | 30-35 | 2+ karakterde eşleşen `"` veya `'` çiftleri `substr(...,1,-1)` ile kesilir | tırnak sarmalayıcı kaldırılır |
| 9 | 36 | `$env[$key] = $value` | **son satır kazanır** (aynı anahtar tekrar) |
| 10 | 42-50 | `loadIntoEnv`: yalnız `!isset($_ENV[$key])` iken yazar | mevcut ortam değeri ezilmez |
| 11 | 7-40 | `load()` döner, yazmaz | ayrıştırma/surma ayrımı |
| 12 | 44-49 | döngü tek tek `$_ENV` yazar | `putenv` çağrılmaz |

**Çıkarım:** `.env` içindeki **hatalı satır yutulur** (satır 5) — bu nedenle M4
fail-fast'i `APP_ENV_MODE` anahtarını **açıkça** doğrular (`constants.php:23`).

## §14 — Hata perdesi: diskteki tam dosya (`shared/src/Bootstrap/RuntimeBootstrap.php:1-21`)

```php
<?php declare(strict_types=1);

namespace CoreMusic\Bootstrap;

final class RuntimeBootstrap
{
    public static function boot(bool $debugMode = false): void
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');

        if ($debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            ini_set('display_errors', '0');
        }

        ini_set('log_errors', '1');
    }
}
```

| # | Satır | Gözlem | §2.3 (M2) ile karşılaştırma |
|---|---|---|---|
| 1 | 8 | tek `public static function boot(bool $debugMode = false): void` | tek geçiş ✓ |
| 2 | 10 | `$_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul'` | `constants.php:31` default'u ile aynı zone |
| 3 | 12-20 | iki mod: `E_ALL`/`'1'` ↔ `E_ERROR \| E_WARNING \| E_PARSE`/`'0'` | M2 mod tablosu birebir ✓ |
| 4 | 19 | `ini_set('log_errors', '1');` — if bloğu **dışında** | M2: loglama her iki modda ✓ |
| 5 | 5 | `final class RuntimeBootstrap` | genişletilemez |
| 6 | 1-2 | `declare(strict_types=1)` + namespace | dil standardı §5 |

## §15 — Bu turda eklenen/durumlanan URL'ler

| # | URL | Kaynak | 2026-10-13 durumu | Kullanım |
|---|---|---|---|---|
| 1 | https://www.php.net/manual/en/function.ini-set.php | dosya `index.md`'sinden devralınan | bu dosyanın `evidence:` alanında | §3.2 AP-5 |
| 2 | https://www.php.net/manual/en/function.error-reporting.php | dosya `index.md`'sinden devralınan | bu dosyanın `evidence:` alanında | M2 mod tablosu |
| 3 | https://www.php.net/manual/en/function.register-shutdown-function.php | dosya `index.md`'sinden devralınan | bu dosyanın `evidence:` alanında | M5 |
| 4 | Yeni URL eklenmedi | — | — | bu turda konuya uyan **izinli 4 domain** (docs.kernel.org · man7.org · learn.microsoft.com · rfc-editor.org) içinden doğrulanmış kaynak bulunmadı → `0` |

**Eklenen yeni URL sayısı: 0** (mevcut 3 URL korunmuştur; uydurma URL yazılmamıştır.)

## §16 — Açık soru eki

| # | Soru | Durum |
|---|---|---|
| R11 | `evidence:` dizgesindeki `api.coremusic.net/Services/` öneki diskte neden yok? | `[UNKNOWN]` — taşınma/silme geçmişi bu dosyada araştırılmadı |
| R12 | `home.coremusic.net/config/app.php` içindeki PDO `dsn` zorunluluğu | `[⚠️ VERIFICATION REQUIRED]` — bu turda `app.php` okunmadı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
