---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik
evidence:
  - https://www.php.net/manual/en/book.apcu.php
  - https://man7.org/linux/man-pages/man3/exit.3.html
  - https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_join.html
  - shared/src/Cache/ApcuAdapter.php:7-55
  - api.coremusic.net/index.php:143-145
  - .github/workflows/ci.yml:37-38
validation: "2026-10-13 · 3 URL HTTP 200 (book.apcu bu tur envantere eklendi · exit.3 · pthread_join bu dalın index.md'sinden devralındı) · repo satırları Read ile birebir · içerik 380-460 satır"
open_questions:
  - "R9: APCu per-process bellek modeli — çok-worker topolojide cache tutarlılığı nasıl sağlanıyor? Repo'da invalidation stratejisi görünmüyor. [⚠️ VERIFICATION REQUIRED]"
  - "R2: Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? Uygulama katmanında thread oluşturucu kod yok. [UNKNOWN]"
  - "R10: Süreç kuyruğu (background job / queue) hangi süreç modelinde çalışıyor? Repo'da ayrı worker process'i tanımlı değil. [UNKNOWN]"
group: mimari
---

# surec-ve-parcacik — Sürücü (Kademe-3)

## §0 ELI15

Bu dosya, süreç/parçacık kurallarının **repo'daki yürütme yüzeyini** gösterir: APCu
adaptörü process-başına çalışır, kod eklenti kapalı ortama karşı `function_exists`
ile korunur, süreç oluşturma yalnız test altyapısında `proc_open` ile görülür ve
kapanış `exit` ile biter. Yani anayasada tanımlanan süreç modelinin gerçek kod
karşılıkları burada adım adım izlenir.

## §1 Uygulama Katmanı — süreç kullanım haritası

| Yüzey | Dosya | Süreç ile ilişkisi | Durum |
|---|---|---|---|
| APCu adaptörü | `shared/src/Cache/ApcuAdapter.php` | process-başına önbellek; 6 metod `function_exists` korumalı | ✅ IMPLEMENTED |
| APCu devri | `api.coremusic.net/index.php:143-145` | `apcu_enabled()` kontrolü → ApcuAdapter / MemoryAdapter | ✅ IMPLEMENTED |
| Zorunlu eklenti | `shared/composer.json:8-9` | `php >=8.4` + `ext-apcu` | ✅ IMPLEMENTED |
| CI eklentisi | `.github/workflows/ci.yml:37-38` | `extensions: apcu` + `apcu.enable_cli=1` | ✅ IMPLEMENTED |
| Alt süreç | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | `proc_open` + 3 boru + `proc_close` | ✅ TEST ONLY |
| Ölçüm | `shared/src/Middleware/MiddlewarePipeline.php:38-52` | `hrtime` ile istek-içi süre ölçümü | ✅ IMPLEMENTED |
| İş-parçacığı | — | üretim kodunda thread oluşturucu yok | ❌ YOK (R2 [UNKNOWN]) |
| Kuyruk süreci | — | ayrı background worker process'i yok | ❌ YOK (R10 [UNKNOWN]) |

> Harita kuralı: bu tabloda `✅` yalnız disk satırı okunarak yazıldı; `❌` repo
> taramasının negatif kanıtıdır, tahmin değildir.

### §1.1 Yüzey başına notlar

| Yüzey | Kural | Dikkat |
|---|---|---|
| APCu adaptör | Her metod önce `function_exists`, sonra APCu çağrısı | Eklenti yoksa metod `false` döner, hata fırlatmaz |
| APCu devri | `apcu_enabled()` runtime kontrolü | composer `ext-apcu` zorunlu kılsa da kod ortamı test eder |
| CI | `apcu.enable_cli=1` | CLI (phpunit) altında APCu kapalıysa adaptör testleri MemoryAdapter'a düşer |
| Alt süreç | Borular açık, çıkış kodu `proc_close` ile alınır | Üretim kodunda bu desen yok |
| Ölçüm | `hrtime(true)` nanosaniye; `1_000_000` ile ms | Süre ölçümünün sahibi `observability` dalı |

## §2 Akış — katmanlar

### §2.1 Akış 1 — APCu adaptör yaşam döngüsü (ApcuAdapter.php:7-55)

| # | Adım | Metod | Davranış |
|---|---|---|---|
| 1 | Okuma | `get` (7-15) | `apcu_fetch` yoksa `false`; `$success` bayrağı ile ayrım |
| 2 | Yazma | `set` (17-23) | `apcu_store` yoksa `false`; TTL parametresi geçilir |
| 3 | Silme | `delete` (25-31) | `apcu_delete` yoksa `false` |
| 4 | Artırım | `increment` (33-39) | `apcu_inc` yoksa `false`; atomik sayaç |
| 5 | Temizlik | `clear` (41-47) | `apcu_clear_cache` yoksa `false` |
| 6 | Varlık | `has` (49-55) | `apcu_exists` yoksa `false` |
| 7 | Kapsam | — | **Tüm sonuçlar process-başına** (M4 · R9) |

### §2.2 Akış 2 — API istek zincirinde APCu devri (api/index.php:143-145)

| # | Adım | Satır | Ne olur |
|---|---|---|---|
| 1 | Eklenti kontrolü | 143 | `function_exists('apcu_enabled') && apcu_enabled()` |
| 2 | Devir (var) | 144 | `new ApcuAdapter()` — process-başına önbellek |
| 3 | Devir (yok) | 145 | `new MemoryAdapter()` — aynı process içinde istek-içi |
| 4 | Kullanım | 151 | `RateLimitMiddleware(new CacheRateLimiter($apiCache))` |
| 5 | Sonuç | — | Rate-limit sayaçları APCu varsa process-başına kalıcı |

> R9 boşluğu burada görünür: sayaçlar process-başına ise toplam limit worker
> sayısına göre çoğalır; invalidation stratejisi repo'da görünmüyor.

### §2.3 Akış 3 — CLI alt süreç akışı (ApiKeyCreateCommandTest.php:248-269)

| # | Adım | Satır | Ne olur |
|---|---|---|---|
| 1 | Komut kurulumu | 249-250 | `PHP_BINARY` + betik yolu birleştirilir |
| 2 | `proc_open` | 252-257 | Üç boru: stdin (r), stdout (w), stderr (w) |
| 3 | Kaynak denetimi | 259 | `assertIsResource` — süreç açılamadıysa test durur |
| 4 | stdin kapatma | 261 | Boru 0 kapanır (EOF) |
| 5 | Çıkış okuma | 262-263 | stdout + stderr `stream_get_contents` ile |
| 6 | Boru kapatma | 264-265 | Boru 1 ve 2 kapanır |
| 7 | Kapanış | 267 | `proc_close` → çıkış kodu döner |

### §2.4 Akış 4 — çıkış / erken çıkış (exit(3) semantiği)

| # | Adım | Repo yüzeyi | OS karşılığı |
|---|---|---|---|
| 1 | Erken çıkış | `bootstrap.php:15-26` `/health` → `exit` | süreç kapanış çağrısı |
| 2 | atexit | repo'da `register_shutdown_function` gözlenmedi | `exit(3)` atexit temizleyicilerini çalıştırır |
| 3 | Shutdown evresi | PHP kendi kapanışını yapar | son süreç sonlanması |
| 4 | Sonuç | 200 yanıttan sonra istek biter | exit.3 (§7 № 2) |

### §2.5 Akış 5 — pthread: repo negatifi (R2)

| # | Arama | Sonuç | Yorum |
|---|---|---|---|
| 1 | `proc_open` kullanımı | yalnız `shared/tests` | üretimde süreç oluşturma yok |
| 2 | thread oluşturucu çağrı | gözlenmedi | pthread_create.3'e karşılık kod yok |
| 3 | `pthread_join`/`detach` | gözlenmedi | bekleme/ayırma semantiği uygulanmamış |
| 4 | Sonuç | süreç modeli = sunucu worker'ları | topoloji `web-runtime-topolojisi` dalında |

> Bekleme semantiği (hedef mimaride kullanılacaksa): `pthread_join` parçacık
> bitişini bekleyip kaynakları serbest bırakır — pthread_join (§7 № 3).

## §3 Hata Modları

| # | Hata | Belirti | Kök neden | Sonuç |
|---|---|---|---|---|
| 1 | APCu kapalı | adaptör tüm metodlarda `false` | eklenti yok/devre | `MemoryAdapter` devri çalışır |
| 2 | APCu CLI kapalı | testte beklenmedik `false` | `apcu.enable_cli=1` yok | CI `ci.yml:38` bunu kapatır |
| 3 | Çok-worker cache boşluğu | worker A yazdı, worker B okumadı | per-process bellek (R9) | ⚠️ VERIFICATION REQUIRED |
| 4 | Sayaç sapması | rate-limit toplamı worker × limit | atomik paylaşım yok (§2.2 notu) | R9 ile aynı boşluk |
| 5 | `proc_open` başarısız | `assertIsResource` kırmızı | süreç açılamadı (izin/binary) | test fail — kasıtlı |
| 6 | Boru sızıntısı | test asılı | boru kapanmadı | §2.3 adımları 4-6 zorunlu |
| 7 | Üretimde süreç oluşturma | beklenmeyen çocuk süreç | istenmeyen `proc_open`/`exec` | repo taraması 0 (§2.5) |

### §3.1 Teşhis — hatadan kata (şema)

| Kat | Soru | Araç |
|---|---|---|
| Eklenti | `apcu_enabled()` ne döndürüyor? | çalıştırma zamanı kontrolü |
| Kod | Adaptör devri doğru mu? | `api/index.php:143-145` |
| Topoloji | Kaç worker process var? | sunucu yapılandırması (R7 [⚠️]) |
| Tutarlılık | Invalidation stratejisi nerede? | repo taraması — gözlenmedi (R9) |
| Süreç | Beklenmeyen çocuk var mı? | test/prod ayrımı (§2.5) |

## §4 Test Stratejisi

| # | Katman | Durum | Kanıt |
|---|---|---|---|
| 1 | Adaptör birim testi | bu dalda incelenmedi | ⚠️ kapsam dışı (`isletim-sistemi-testing`) |
| 2 | CLI alt süreç testi | ✅ mevcut | `ApiKeyCreateCommandTest.php:248-269` |
| 3 | CI eklenti kapısı | ✅ mevcut | `ci.yml:37-38` (`extensions: apcu`, `apcu.enable_cli=1`) |
| 4 | Çok-worker tutarlılık testi | yok | R9 — PLANNED |
| 5 | Sinyal/iş-parçacığı testi | yok | R2 — repo'da üretilmiyor |

### §4.1 Prosedür — D-1..D-3 (bu dalın yürütüm notları)

| # | Senaryo | Adım | Beklenen |
|---|---|---|---|
| D-1 | APCu devri | `apcu_enabled()` sahte/devre | `MemoryAdapter` seçilir |
| D-2 | Alt süreç | `runCli` çağrısı | çıkış kodu + stdout/stderr okunur |
| D-3 | Eklenti kapısı | CI php-lint job'ı | `apcu` kurulur, `enable_cli=1` |

## §5 Ölçüm

| # | Ne ölçülür | Araç / yöntem | Eşik | Durum |
|---|---|---|---|---|
| 1 | Middleware süresi | `hrtime` (`MiddlewarePipeline.php:38-52`) | gerçek ölçüm | OKU (2026-10-13) |
| 2 | APCu erişimi | `apcu_enabled()` (`api/index.php:143`) | devre → fallback | OKU (2026-10-13) |
| 3 | Eklenti zorunluluğu | `composer.json:9` | `ext-apcu` | OKU (2026-10-13) |
| 4 | CI eklenti ayarı | `ci.yml:37-38` | `apcu` + `enable_cli=1` | OKU (2026-10-13) |
| 5 | Süreç oluşturma yüzeyi | repo taraması | yalnız testte `proc_open` | OKU (2026-10-13) |
| 6 | Cache tutarlılık sapması | çok-worker gözlem | değer yok | UNKNOWN (R9) |

## §6 Repo Kanıtı

### §6.1 APCu devri (api.coremusic.net/index.php:143-145)

```php
$apiCache       = (function_exists('apcu_enabled') && apcu_enabled())
    ? new ApcuAdapter()
    : new MemoryAdapter();
```

### §6.2 Adaptör çekirdeği (shared/src/Cache/ApcuAdapter.php:7-23)

```php
public function get(string $key): mixed
{
    if (!function_exists('apcu_fetch')) {
        return false;
    }
    $success = false;
    $value = apcu_fetch($key, $success);
    return $success ? $value : false;
}

public function set(string $key, mixed $value, int $ttl = 0): bool
{
    if (!function_exists('apcu_store')) {
        return false;
    }
    return apcu_store($key, $value, $ttl);
}
```

### §6.3 Zorunlu eklenti (shared/composer.json:7-9)

```json
"require": {
    "php": ">=8.4",
    "ext-apcu": "*",
```

### §6.4 CI eklenti kapısı (.github/workflows/ci.yml:34-38)

```yaml
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          tools: composer
          extensions: apcu, mbstring, json, pdo, pdo_mysql
          ini-values: apcu.enable_cli=1
```

### §6.5 Ölçüm sargısı (shared/src/Middleware/MiddlewarePipeline.php:38-46)

```php
private function wrap(IMiddleware $middleware, callable $next): callable
{
    return function (array $request) use ($middleware, $next): array {
        $name  = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
        $start = hrtime(true);

        $response = $middleware->handle($request, $next);

        $durationMs = (hrtime(true) - $start) / 1_000_000;
```

## §7 Kaynakça

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | APCu eklenti el kitabı — süreç-başına önbellek kavramı | https://www.php.net/manual/en/book.apcu.php | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 2 | `exit(3)` — atexit temizleyicileri ve süreç sonlanması | https://man7.org/linux/man-pages/man3/exit.3.html | 2026-10-13 HTTP 200 · bu dalın `index.md`'sinden devralındı |
| 3 | `pthread_join(3)` — parçacık bitişini bekleme semantiği | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_join.html | 2026-10-13 HTTP 200 · bu dalın `index.md`'sinden devralındı |

> Devralınan iki URL bu alt-dalın `index.md`'sindedir; kök ağacın başka bir dosyasında
> tekrarlanmaz. `pthread_detach` kaynağı `index.md` §Web Research bölümündedir.

## §8 — CI Yürütme Topolojisi (`.github/workflows/ci.yml` — 203 satır)

> Kanıt: dosya 2026-10-13'de satır satır okundu; job/adım satır başlangıçları aşağıda.

### §8.1 Workflow düzeyi ayarlar

| # | Ayar | Satır | Değer |
|---|---|---|---|
| 1 | `name` | 8 | `CI Pipeline` |
| 2 | `on` | 10-12 | `push` + `pull_request` |
| 3 | `permissions` | 14-15 | `contents: read` |
| 4 | `concurrency` | 17-19 | `group: ${{ github.workflow }}-${{ github.ref }}` |
| 5 | `env` | 21-22 | `PHP_VERSION` |
| 6 | `jobs` | 24 | 6 job |

### §8.2 Job tablosu (6 job · süreç bağımlılıkları dâhil)

| # | Job | Başlangıç | `runs-on` | `needs` | `continue-on-error` |
|---|---|---|---|---|---|
| 1 | `php-lint` | 25 | 27 `ubuntu-latest` | — | — |
| 2 | `php-test` | 66 | 69 `ubuntu-latest` | 68 `php-lint` | — |
| 3 | `composer-audit` | 110 | 112 `ubuntu-latest` | — | — |
| 4 | `js-test` | 142 | 144 `ubuntu-latest` | — | — |
| 5 | `js-coverage` | 160 | 163 `ubuntu-latest` | 162 `js-test` | 167 `true` |
| 6 | `js-e2e` | 178 | 180 `ubuntu-latest` | — | 183 `true` |

### §8.3 Adım tablosu (job başına yürütülen adımlar)

| Job | Adım | Satır |
|---|---|---|
| `php-lint` | Setup PHP | 32 |
| `php-lint` | Cache Composer | 40 |
| `php-lint` | Composer install (shared) | 48 |
| `php-lint` | Composer install (auth.coremusic.net) | 52 |
| `php-lint` | Composer install (home.coremusic.net) | 56 |
| `php-lint` | PHPStan (composer stan) | 62 |
| `php-test` | Setup PHP | 84 |
| `php-test` | Cache Composer | 92 |
| `php-test` | Composer install | 100 |
| `php-test` | PHPUnit | 104 |
| `composer-audit` | Setup PHP | 117 |
| `composer-audit` | Composer install + audit (shared) | 123 |
| `composer-audit` | Composer install + audit (auth.coremusic.net) | 129 |
| `composer-audit` | Composer install + audit (home.coremusic.net) | 135 |
| `js-test` | npm ci | 154 |
| `js-test` | Vitest (unit) | 157 |
| `js-coverage` | Vitest coverage | 175 |
| `js-e2e` | Setup PHP | 187 |
| `js-e2e` | Playwright install chromium | 199 |
| `js-e2e` | Playwright e2e | 202 |

**Checkout satırları (tüm job'larda aynı adım):** 30 · 82 · 115 · 147 · 169 · 185 — 6 job × 1 checkout.

## §9 — CI'da süreç/durum kodu davranışı

| # | Durum | Ne olur | Repo kanıtı |
|---|---|---|---|
| 1 | `php-lint` başarısız | `needs: php-lint` nedeniyle `php-test` **hiç başlamaz** | satır 68 |
| 2 | `js-test` başarısız | `js-coverage` başlamaz | satır 162 |
| 3 | `js-coverage` başarısız | workflow **yeşil kalır** | satır 167 `continue-on-error: true` |
| 4 | `js-e2e` başarısız | workflow **yeşil kalır** | satır 183 `continue-on-error: true` |
| 5 | eşzamanlı tetiklenme | aynı ref için eski koşu iptal edilir | satır 17-19 `concurrency` |
| 6 | izin | workflow repo içeriğini **salt okur** | satır 14-15 `contents: read` |
| 7 | kuyruk modeli | 6 job = 6 ayrı Linux koşusu (süreç), paralel başlar; `needs` ile sıralanır | §8.2 |

> **Süreç anlamı:** her job, GitHub runner'ında ayrı bir çalışma (process) başlatır; `needs`
> bağımlılığı bir job'un **bitişini** bekler — POSIX `waitpid(2)` semantiğinin CI düzeyindeki
> karşılığıdır (bkz. bu dalın `anayasa.md` §13/5).

## §10 — Alt süreç hata kataloğu (proc_open / shell_exec yüzeyi)

| # | Hata | Belirti | Kod karşılığı | Repo yüzeyi |
|---|---|---|---|---|
| 1 | süreç başlatılamadı | `proc_open` `false` döner | `assertIsResource` kırmızı | `ApiKeyCreateCommandTest.php:260` |
| 2 | binary bulunamadı | 1. hata + stderr boş | `PHP_BINARY` geçersiz | `:250` |
| 3 | çıkış kodu ≠ 0 | `proc_close` değeri | assert karşılaştırması | `:268` |
| 4 | boru okunamadı | `$out`/`$err` boş | `stream_get_contents` `false` | `:263-264` |
| 5 | boru sızıntısı | test asılı kalır | `fclose` çağrılmadı | `:262`, `:265-266` |
| 6 | ffprobe yok | `shell_exec` `null` | sabit varsayılan döner | `AIEngine.php:295-298` |
| 7 | ffprobe hatalı çıktı | `json_decode` istisnası | `JSON_THROW_ON_ERROR` | `AIEngine.php:300` |
| 8 | yol kaçışı | komut dizesi bozulur | `escapeshellarg` | `AIEngine.php:292` |

> **Katman notu:** bu sekiz satır PHP uygulama katmanının hata yüzeyidir; POSIX sinyal/çekirdek
> hata yüzeyi bu dalın `anayasa.md` §13-§16'sındadır. İkisi karıştırılmaz.

## §11 — Ölçüm yüzeyi ayrıntısı

### §11.1 `hrtime` sargısı (`shared/src/Middleware/MiddlewarePipeline.php:38-52`)

| # | Satır | Davranış |
|---|---|---|
| 1 | 38-40 | `wrap(IMiddleware $middleware, callable $next)` sargısı kurulur |
| 2 | 41 | middleware adı sınıf adından ayrılır (`substr(strrchr(...))`) |
| 3 | 42 | `$start = hrtime(true);` — nanosaniye sayacı |
| 4 | 44 | `$response = $middleware->handle($request, $next);` |
| 5 | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| 6 | — | süre **istek-içi**dir; süreçler arası toplam değildir |

### §11.2 Eklenti/zorunluluk üçlüsü

| # | Yüzey | Satır | Değer |
|---|---|---|---|
| 1 | composer | `shared/composer.json:8` | `"php": ">=8.4"` |
| 2 | composer | `shared/composer.json:9` | `"ext-apcu": "*"` |
| 3 | CI | `ci.yml:37` | `extensions: apcu, mbstring, json, pdo, pdo_mysql` |
| 4 | CI | `ci.yml:38` | `ini-values: apcu.enable_cli=1` |
| 5 | çalışma zamanı | `api/index.php:143` | `function_exists('apcu_enabled') && apcu_enabled()` |

## §12 — Negatif Kanıt Defteri (bu tur gözlemi)

| # | Aranan | Yöntem | Sonuç |
|---|---|---|---|
| 1 | `pcntl_*` (PHP sinyal uzantısı) | tüm `*.php` taraması | **0 eşleşme** |
| 2 | `pthread_*` (PHP parçacık uzantısı) | tüm `*.php` taraması | **0 eşleşme** |
| 3 | bağımsız `exec()` | desen `(?<![>:\w])exec\s*\(` | **0** (PDO `->exec(` hariç) |
| 4 | `system()` / `passthru` / `popen` | tarama | **0** |
| 5 | gerçek zamanlı politika kuran kod | `pcntl_`/`posix_` taraması | **0** |
| 6 | kuyruk worker process'i | repo taraması | **0** → R10 [UNKNOWN] |
| 7 | APCu invalidation stratejisi | repo taraması | gözlenmedi → R9 [⚠️ VERIFICATION REQUIRED] |
| 8 | FPM/MPM havuz yapılandırması | repo taraması | dosya yok → `web-runtime-topolojisi` R7 [⚠️] |
| 9 | `register_shutdown_function` | tarama | bu dalın dosyalarında **0** → bkz. `baslangic-ve-bootstrap` §19/1 |
| 10 | sinyal işleyici (`pcntl_signal`) | tarama | **0** |

## §13 — Tekrarlanabilir Ölçüm ve URL Durumu

| # | Amaç | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | job sayısı | `Select-String -Path '.github\workflows\ci.yml' -Pattern '^  [a-z0-9-]+:$'` | 6 |
| 2 | bağımlı job sayısı | desen `'    needs:'` | 2 (satır 68, 162) |
| 3 | hata-yumuşatma | desen `'continue-on-error: true'` | 2 (satır 167, 183) |
| 4 | checkout sayısı | desen `'uses: actions/checkout'` | 6 |
| 5 | süreç oluşturma yüzeyi | desen `'proc_open'` | 1 (test) |
| 6 | shell yürütme yüzeyi | desen `'shell_exec'` | 1 (üretim, yardımcı) |
| 7 | pcntl/pthread | desen `'pcntl_|pthread_'` | 0 |
| 8 | dolu satır | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |
| 9 | mojibake | `[IO.File]::ReadAllLines($f)` UTF-8 ayrıştırma | 0 |

### §13.1 Bu tur eklenen doğrulanmış URL (2026-10-13 · HTTP 200)

| # | URL | Kullanım |
|---|---|---|
| 1 | https://man7.org/linux/man-pages/man2/execve.2.html | §10/6-7 semantiği (program değiştirme) |
| 2 | https://man7.org/linux/man-pages/man2/nanosleep.2.html | §11.1 süre ölçümü karşılaştırma notu |
| 3 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createthread | §9/7 CI-paralelliğinin Windows karşılığı |

> **Bu tur bu dosyaya eklenen yeni doğrulanmış URL sayısı: 3.** `exit.3` ve `pthread_join`
> (opengroup) bu dalın `index.md`'sinden devralınmıştır; `book.apcu.php` bu dosyanın §7'sinde
> kayıtlıdır — üçü de tekrar sayılmaz.

## §14 — Katman Eşlemesi: Repo → POSIX → Windows

### §14.1 Çağrı karşılıkları

| # | Repo yüzeyi | PHP çağrısı | POSIX karşılığı | Windows karşılığı | Doğrulanmış metin |
|---|---|---|---|---|---|
| 1 | `ApiKeyCreateCommandTest.php:253` | `proc_open` | `fork(2)` + `execve(2)` | `CreateProcessW` | "execve() executes the program referred to by path." (execve.2, §13.1/1) |
| 2 | `:263-264` | `stream_get_contents` | `read(2)` boru | `ReadFile` [⚠️ API doğrulanmadı] | — |
| 3 | `:268` | `proc_close` | `waitpid(2)` | süreç handle'ı ile bekleme | "suspends execution ... until a child ... has changed state" (bu dal `anayasa.md` §13/5) |
| 4 | `AIEngine.php:295` | `shell_exec` | `popen`-tipi tek yönlü yürütme | `CreateProcessW` + pipe | "Creates a new process and its primary thread." (§13.1/3) |
| 5 | `:42` (pipeline) | `hrtime(true)` | — (PHP sayaç) | — | istek-içi süre; süreç ölçümü değil |
| 6 | `ci.yml` job'ları | — (runner) | ayrı süreç + `wait` | ayrı iş | `needs` = bekleme bağımlılığı |
| 7 | parçacık yüzeyi | yok (§12) | `pthread_create(3)` | `CreateThread` | "Creates a thread to execute within the virtual address space of the calling process." (§13.1/3) |
| 8 | uyku/bekleme yüzeyi | yok | `nanosleep(2)` | `Sleep` [⚠️ API doğrulanmadı] | "nanosleep() suspends the execution of the calling thread until either at least the time specified in *duration has elapsed, or the delivery of a signal" (§13.1/2) |

### §14.2 ffprobe yardımcı süreci — tam akış (repo)

| # | Adım | Satır | Süreç olayı |
|---|---|---|---|
| 1 | komut inşa | 290-293 | dize + `escapeshellarg` |
| 2 | yürütme | 295 | çocuk süreç başlar, stdout boruya yazılır |
| 3 | başarısızlık | 296-297 | `null` → sabit varsayılan metadata |
| 4 | ayrıştırma | 300 | JSON_THROW_ON_ERROR — hata yükseltilebilir |
| 5 | sonlandırma | — | `shell_exec` dönüşünde çocuk kapanır (PHP sarmalayıcı) |

### §14.3 Test CLI süreci — tam akış (repo)

| # | Adım | Satır | Süreç olayı |
|---|---|---|---|
| 1 | argüman | 250 | `PHP_BINARY` + betik yolu, dizi |
| 2 | başlatma | 253-258 | üç boru, çalışma dizini repo kökü |
| 3 | doğrulama | 260 | `assertIsResource` |
| 4 | stdin EOF | 262 | `fclose($pipes[0])` |
| 5 | okuma | 263-264 | stdout + stderr |
| 6 | kapatma | 265-266 | borular kapanır |
| 7 | bekleme + kod | 268 | `proc_close` → çıkış kodu |

### §14.4 Hata kataloğu ↔ OS karşılığı

| # | PHP belirtisi | OS düzeyinde karşılığı | Bu dosyadaki § |
|---|---|---|---|
| 1 | `proc_open` `false` | `fork`/`execve` başarısız (EAGAIN/ENOENT) | §10/1-2 |
| 2 | çıkış kodu ≠ 0 | `waitpid` ile alınan durum | §10/3 |
| 3 | `shell_exec` `null` | alt süreç yok çıktılı bitti | §10/6 |
| 4 | `JSON_THROW_ON_ERROR` | geçersiz çıktı (çocuğun davranışı) | §10/7 |
| 5 | test asılı | `wait`/boru beklemesi — çocuk kapanmadı | §10/5 |

### §14.5 Ölçüm ↔ sorumluluk ayrımı

| Ölçüm | Sahibi | Buradaki rol |
|---|---|---|
| Middleware süresi | bu dosya §11.1 | yalnız `hrtime` yüzeyi |
| Süreç çıkış kodu | bu dosya §14.3 | `proc_close` |
| Cache tutarlılık sapması | R9 — PLANNED | değer yok, UNKNOWN |
| Worker sayısı | `web-runtime-topolojisi` R7 | ⚠️ VERIFICATION REQUIRED |

## §15 — Dosya Kaynak Özeti (bu tur)

| # | Kaynak | Tür | Bu dosyada |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` (203 satır) | repo | §8-§9 · §13 |
| 2 | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | repo | §10-§11 · §14.3 |
| 3 | `shared/src/AI/AIEngine.php:288-300` | repo | §10 · §12 · §14.2 |
| 4 | `shared/src/Middleware/MiddlewarePipeline.php:38-52` | repo | §11.1 |
| 5 | `shared/composer.json:8-9` | repo | §11.2 |
| 6 | `api.coremusic.net/index.php:143-145` | repo | §11.2/5 |
| 7 | `man7.org` (execve.2 · nanosleep.2) | web | §13.1 |
| 8 | `learn.microsoft.com` (CreateThread) | web | §13.1 · §14.1 |
| 9 | `home.coremusic.net/index.php` (29 satır) | repo | §15.1 |
| 10 | `api.coremusic.net/index.php` (223 satır) | repo | §11.2/5 · §14.2 |

### §15.1 — Bootstrap çağrısı envanteri (repo)

| # | Dosya | Satır | Çağrı |
|---|---|---|---|
| 1 | `home.coremusic.net/index.php` | 23 | `RuntimeBootstrap::boot(DEBUG_MODE)` |
| 2 | `api.coremusic.net/index.php` | 44 | `RuntimeBootstrap::boot(DEBUG_MODE)` |
| 3 | `auth.coremusic.net/index.php` | 32 | `RuntimeBootstrap::boot(DEBUG_MODE)` |
| 4 | diğer `index.php` | — | yok (3 eşleşme) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode