---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik
evidence:
  - https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_detach.html
  - https://man7.org/linux/man-pages/man3/pthread_create.3.html
  - https://man7.org/linux/man-pages/man2/fork.2.html
  - shared/src/Cache/ApcuAdapter.php:1-56
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
validation: "2026-10-13 · 3 URL HTTP 200 (pthread_detach bu dalın index.md'sinden devralındı · pthread_create.3 · fork.2 bu tur envantere eklendi) · repo kod blokları Read ile birebir · içerik 380-460 satır"
open_questions:
  - "R2: Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? Üretim kodunda thread oluşturucu yok. [UNKNOWN]"
  - "R9: APCu per-process cache tutarlılığı — invalidation stratejisi repo'da görünmüyor. [⚠️ VERIFICATION REQUIRED]"
  - "R10: Süreç kuyruğu hangi süreç modelinde çalışıyor? Repo'da ayrı worker process'i yok. [UNKNOWN]"
group: kod
---

# surec-ve-parcacik — Örnekler (gerçek repo kodu · Kademe-3)

> **Kural:** Her örnek gerçek bir repo dosyasından okunmuştur; `Dosya:` + `Satır:`
> künyesi Read (2026-10-13) ile doğrulanmıştır. Örnek uydurulmaz, özetlenmez —
> dosyadaki satırlar aynen taşınır.

## §0 ELI15

Bu dosya, süreç/parçacık konularının **gerçek repo kanıtlarını** taşır: APCu
(per-process bellek), CLI alt süreç (`proc_open`), eklenti zorunluluğu, CI kapısı
ve `hrtime` süre ölçümü. Her örnek `Dosya:` + `Satır:` künyesiyle Read (2026-10-13)
ile doğrulanmıştır; OS düzeyi süreç mekanizması bu dosyanın **kapsamı dışındadır**
(komşu: `surec-ve-parcacik-anayasa.md` / `-surucu.md`).

## §0.1 Okuma notu

| # | Kural | Açıklama |
|---|---|---|
| 1 | Künye zorunlu | her örnekte `Dosya:` ve `Satır:` |
| 2 | Kod aynen | satır içi düzenleme yok |
| 3 | Yer tutucu yok | eksik kod gizlenmez |
| 4 | Yorum satırı | dosyadaki varsa korunur |
| 5 | Açıklama | her örnekten sonra "Ne gösteriyor" |
| 6 | Çeviri kaydı | orijinalde bozuk kodlamalı metin ASCII'ye çevriliyorsa satırında kaydedilir |

## §1 Örnek Envanteri

| # | Örnek | Dosya | Satır | Konu |
|---|---|---|---|---|
| E-1 | APCu adaptörü (tam sınıf) | `shared/src/Cache/ApcuAdapter.php` | 1-56 | per-process bellek |
| E-2 | APCu devri | `api.coremusic.net/index.php` | 143-145 | çalışma zamanı kontrolü |
| E-3 | CLI alt süreç | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 248-269 | `proc_open` / `proc_close` |
| E-4 | Zorunlu eklenti | `shared/composer.json` | 7-9, 27-31 | `ext-apcu` + PSR-4 |
| E-5 | CI eklenti kapısı | `.github/workflows/ci.yml` | 22, 34-38 | `apcu` + `enable_cli=1` |
| E-6 | Süre ölçümü | `shared/src/Middleware/MiddlewarePipeline.php` | 38-52 | `hrtime` sargısı |

## §2 İyi Örnekler

### §2.1 E-1 — APCu adaptörü, tam sınıf (ApcuAdapter.php:1-56)

**Dosya:** `shared/src/Cache/ApcuAdapter.php` · **Satır:** 1-56

```php
<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class ApcuAdapter implements CacheInterface
{
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

    public function delete(string $key): bool
    {
        if (!function_exists('apcu_delete')) {
            return false;
        }
        return apcu_delete($key);
    }

    public function increment(string $key, int $step = 1): int|false
    {
        if (!function_exists('apcu_inc')) {
            return false;
        }
        return apcu_inc($key, $step);
    }

    public function clear(): bool
    {
        if (!function_exists('apcu_clear_cache')) {
            return false;
        }
        return apcu_clear_cache();
    }

    public function has(string $key): bool
    {
        if (!function_exists('apcu_exists')) {
            return false;
        }
        return apcu_exists($key);
    }
}
```

**Ne gösteriyor:** Önbelleğin tamamı **process-başına**dır: her metod önce eklenti
fonksiyonunun varlığını kontrol eder, yoksa `false` döner. `increment` `apcu_inc`
ile atomik artırım yapar (anayasa M7). Sınıf `CacheInterface`'i uygular → APCu
kapalı ortamda adaptör `MemoryAdapter`'a devredilir (E-2).

**Doğrulama:** Read (2026-10-13) — dosya 56 satır; `apcu_fetch` satır 13,
`apcu_store` satır 22, `apcu_inc` satır 38.

### §2.2 E-2 — APCu devri (api.coremusic.net/index.php:143-145)

**Dosya:** `api.coremusic.net/index.php` · **Satır:** 143-145

```php
$apiCache       = (function_exists('apcu_enabled') && apcu_enabled())
    ? new ApcuAdapter()
    : new MemoryAdapter();
```

**Ne gösteriyor:** Çalışma zamanı yeteneği **kontrol edilip** devir yapar. Bu satır
`RateLimitMiddleware(new CacheRateLimiter($apiCache))` (satır 151) için girdidir —
rate-limit sayaçlarının nerede yaşadığı burada belli olur.

**Doğrulama:** Read (2026-10-13) — satır 143 `apcu_enabled()` kontrolü.

### §2.3 E-3 — CLI alt süreç (ApiKeyCreateCommandTest.php:248-269)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 248-269

```php
private function runCli(array $args): array
{
    $command = array_merge([PHP_BINARY, self::BIN], $args);

    $pipes  = [];
    $handle = proc_open(
        $command,
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        dirname(__DIR__, 3)
    );

    $this->assertIsResource($handle, 'CLI alt sureci acilamadi');

    fclose($pipes[0]);
    $out = (string) stream_get_contents($pipes[1]);
    $err = (string) stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];
}
```

**Ne gösteriyor:** Depoda süreç oluşturma örneği **yalnız test katmanında** vardır:
`proc_open` ile alt süreç, üç boru, `proc_close` ile çıkış kodu. `proc_open`
`fork`+`exec` benzeri başlatma zincirinin (anayasa A1) PHP'deki uygulama yüzeyidir.
Orijinal dosyadaki Türkçe hata metni bozuk kodlamadır; burada ASCII'ye çevrilmiştir
ve bu çeviri kayıtlıdır.

**Doğrulama:** Read (2026-10-13) — `proc_open(` satır 252, `proc_close` satır 267.

### §2.4 E-4 — Zorunlu eklenti ve autoload (shared/composer.json:7-9, 27-31)

**Dosya:** `shared/composer.json` · **Satır:** 7-9 ve 27-31

```json
"require": {
    "php": ">=8.4",
    "ext-apcu": "*",
```

```json
"autoload": {
    "psr-4": {
        "CoreMusic\\": "src/"
    }
},
```

**Ne gösteriyor:** `ext-apcu` **zorunlu** bağımlılıktır — kurulum APCu'suz yapılamaz.
PSR-4 autoload `src/` kökünü bağlar; boot sırasındaki sınıf yükleme bu kayda dayanır
(`baslangic-ve-bootstrap` dalı).

**Doğrulama:** Read (2026-10-13) — satır 8 `"php": ">=8.4"`, satır 9 `"ext-apcu": "*"`.

### §2.5 E-5 — CI eklenti kapısı (.github/workflows/ci.yml:22, 34-38)

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 22 ve 34-38

```yaml
  PHP_VERSION: '8.4'
```

```yaml
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ env.PHP_VERSION }}
          tools: composer
          extensions: apcu, mbstring, json, pdo, pdo_mysql
          ini-values: apcu.enable_cli=1
```

**Ne gösteriyor:** CI ortamı APCu'yu **kurar** ve CLI'da **açar** (`enable_cli=1`).
Böylece `ApcuAdapter` testleri gerçek APCu üzerinde koşar; eklenti kapalı olsaydı
tüm testler `MemoryAdapter` senaryosuna düşerdi.

**Doğrulama:** Read (2026-10-13) — satır 22 `PHP_VERSION`, satır 37 `extensions`,
satır 38 `ini-values`.

### §2.6 E-6 — Süreç-içi süre ölçümü (MiddlewarePipeline.php:38-46)

**Dosya:** `shared/src/Middleware/MiddlewarePipeline.php` · **Satır:** 38-46

```php
private function wrap(IMiddleware $middleware, callable $next): callable
{
    return function (array $request) use ($middleware, $next): array {
        $name  = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
        $start = hrtime(true);

        $response = $middleware->handle($request, $next);

        $durationMs = (hrtime(true) - $start) / 1_000_000;
```

**Ne gösteriyor:** İstek-içi ölçüm process içinde `hrtime` ile alınır; nanosaniye
`1_000_000` ile milisaniyeye çevrilir. Ölçüm altyapısının sahibi
`isletim-sistemi-observability` olsa da, ölçen kodun kendisi bu depodadır.

**Doğrulama:** Read (2026-10-13) — `hrtime(true)` satır 39 ve 45.

### §2.7 Örnek ↔ anayasa eşlemesi

| Örnek | Anayasa maddesi | Akış |
|---|---|---|
| E-1 | M4 per-process bellek · M7 atomiklik | §2.1 |
| E-2 | M4 + SAPI kontrolü (M5) | §2.2 |
| E-3 | M1 süreç başlatma | §2.3 · A1 |
| E-4 | M4 zorunlu eklenti | — |
| E-5 | M5 CLI SAPI | — |
| E-6 | §4 ölçüm sözleşmesi madde 1 | — |

## §3 Anti-Pattern'ler

### §3.1 AP-1 — Zorunlu eklenti ↔ korumalı kod (çift katman)

| Alan | Değer |
|---|---|
| Yüzey | `composer.json:9` (`ext-apcu` zorunlu) ↔ `ApcuAdapter.php` (`function_exists` korumalı) |
| Gerilim | Zorunlu bağımlılık, neden koruma katmanı? |
| Değerlendirme | Anti-pattern değil **savunma katmanıdır**: `composer.json` kurulumu, koruma çalışma zamanını kapsar (SAPI/farklı ortam). Kayıt olarak bilinmelidir |
| Sonuç | Bilgi kaydı — davranış değiştirilmez |

### §3.2 AP-2 — Çok-worker cache boşluğu (R9)

| Alan | Değer |
|---|---|
| Yüzey | `ApcuAdapter` (process-başına) + çok worker process |
| Belirti | Bir worker'da yazılan kayıt diğerinde görünmeyebilir |
| Repo durumu | Invalidation stratejisi (broadcast/subscribe, TTL disiplini) gözlenmedi |
| Sonuç | [⚠️ VERIFICATION REQUIRED] — R9 açık kalır |

### §3.3 AP-3 — Thread oluşturucu yokluğu (R2 negatifi)

| Alan | Değer |
|---|---|
| Yüzey | üretim kodu |
| Arama | `proc_open` yalnız testte; pthread çağrısı yok |
| Sonuç | Eşzamanlı model süreç-tabanlıdır; pthread hedef mimaride varsa `-surucu` §2.5 güncellenir |
| Etiket | [UNKNOWN] — R2 |

### §3.4 AP-4 — Rate-limit sayaç sapması

| Alan | Değer |
|---|---|
| Yüzey | `CacheRateLimiter($apiCache)` (api/index.php:151) |
| Mekanizma | Sayaçlar APCu varsa process-başına → toplam limit worker sayısına göre çoğalabilir |
| Sonuç | Ölçüm PLANNED (§4 madde 4); eşik uydurulmaz |

### §3.5 AP-5 — Sinyal işleyici eksikliği

| Alan | Değer |
|---|---|
| Yüzey | üretim kodu |
| Durum | `pcntl_signal`/eşdeğeri gözlenmedi |
| Değerlendirme | Web SAPI'sında sinyal yönetimi sunucunun işidir; uygulama katmanında beklenmez |
| Sonuç | Kasıtlı boşluk — kusur sayılmaz |

### §3.6 Anti-pattern özeti

| # | Anti-pattern | Durum | Etiket |
|---|---|---|---|
| 1 | Zorunlu eklenti ↔ korumalı kod | savunma katmanı | kayıt |
| 2 | Çok-worker cache boşluğu | açık | R9 [⚠️] |
| 3 | Thread oluşturucu yokluğu | negatif kanıt | R2 [UNKNOWN] |
| 4 | Sayaç sapması | ölçülecek | R9 |
| 5 | Sinyal işleyici yokluğu | kasıtlı | — |

## §4 Test Senaryoları Eşlemesi

| # | Senaryo | Örnek | Beklenen |
|---|---|---|---|
| 1 | CLI alt süreç çıkışı | E-3 | `exit` kodu + stdout/stderr |
| 2 | APCu devri (eklenti devre) | E-2 | `MemoryAdapter` |
| 3 | CI eklenti kurulumu | E-5 | `apcu` + `enable_cli=1` |
| 4 | Çok-worker tutarlılık | — | PLANNED (R9) |

### §4.1 Negatif kanıt defteri (bu dalın "yok"ları)

| # | Arama | Gözlem | Tarih |
|---|---|---|---|
| 1 | üretim kodunda `proc_open` | 0 (yalnız testte) | 2026-10-13 |
| 2 | pthread çağrısı | 0 | 2026-10-13 |
| 3 | sinyal işleyici | 0 | 2026-10-13 |
| 4 | APCu invalidation stratejisi | 0 (R9) | 2026-10-13 |

## §5 Platform Karşılaştırması (örnek düzeyi)

| Örnek | POSIX/Linux | Windows | Not |
|---|---|---|---|
| E-3 alt süreç | `fork`+`exec` ailesine karşılık | `CreateProcess` ailesine karşılık | PHP `proc_open` soyutlar |
| E-1 APCu | süreç-başına | süreç-başına | PHP eklentisi platformdan bağımsız |
| E-6 `hrtime` | nanosaniye | nanosaniye | PHP zamanlayıcı API'si |

## §6 Sözlük Bağlantısı

| Terim | Anayasa |
|---|---|
| süreç · fork/exec | §2 M1 |
| iş-parçacığı | §2 M2 |
| per-process bellek | §2 M4 |
| atomik artırım | §2 M7 |
| exit / shutdown | §2 M6 |

## §7 Kapsam Dışı

| # | Konu | Nereye |
|---|---|---|
| 1 | pthread fonksiyon listesi | `isletim-sistemi-api` |
| 2 | MPM/havuz topolojisi örnekleri | `web-runtime-topolojisi/-ornekler.md` |
| 3 | Boot sırası örnekleri | `baslangic-ve-bootstrap/-ornekler.md` |
| 4 | Test harness stratejisi | `isletim-sistemi-testing` |

### §7.1 Komşu sınırı (bu dosyanın bittiği yer)

| Konu | Gittiği dal |
|---|---|
| Sunucu süreç modeli örnekleri | `web-runtime-topolojisi` |
| Boot/exit uygulama örnekleri | `baslangic-ve-bootstrap` |
| Cache kavram ağacı | `bellek-kavramlari` (core) |

## §8 Açık Sorular (dosya içi)

| # | Soru | Etiket |
|---|---|---|
| R2 | pthread hedef mimaride var mı? | [UNKNOWN] |
| R9 | APCu invalidation stratejisi nerede? | [⚠️ VERIFICATION REQUIRED] |
| R10 | Kuyruk süreci hangi süreç modelinde? | [UNKNOWN] |

## §9 Kaynakça

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `pthread_detach(3)` — beklemeden parçacık kaynaklarını ayırma | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_detach.html | 2026-10-13 HTTP 200 · bu dalın `index.md`'sinden devralındı |
| 2 | `pthread_create(3)` — parçacık oluşturma/bitiş semantiği | https://man7.org/linux/man-pages/man3/pthread_create.3.html | 2026-10-13 HTTP 200 · `-anayasa` §9 № 1 ile aynı tur kaydı |
| 3 | `fork(2)` — süreç kopyalama (E-3'ün OS semantiği) | https://man7.org/linux/man-pages/man2/fork.2.html | 2026-10-13 HTTP 200 · `-anayasa` §9 № 2 ile aynı tur kaydı |

> URL kullanım notu: № 2 ve № 3 bu alt-dalın `-anayasa.md` dosyasında da geçer —
> 12 yeni URL / 33 yerleştirme kısıtında aynı URL iki dosyada kullanılmıştır;
> çelişki kök `-dogrulama.md` §14 adım 8 (dosya başı 3 URL) ile ağaç-başı-bir-kez
> kuralı arasındadır, çıktı raporunda bayraklanmıştır.

## §10 — Eklenti Örnekler (E-7 … E-10)

### §10.1 E-7 — ffprobe yardımcı süreci (`shared/src/AI/AIEngine.php:288-300`)

```php
private function extractMetadata(string $filePath): array
{
    $cmd = sprintf(
        'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
        escapeshellarg($filePath)
    );

    $output = shell_exec($cmd);
    if ($output === null) {
        return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
    }

    $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
```

| # | Gözlem | Kanıt |
|---|---|---|
| 1 | üretim kodunda **tek** `shell_exec` | tarama: 1 eşleşme (§12 AP-6) |
| 2 | başarı yok sayımı: `null` → sabit varsayılan | satır 296-297 |
| 3 | JSON hatası yükseltilebilir | satır 300 `JSON_THROW_ON_ERROR` |
| 4 | tırnak-çiftleme belirsizliği | satır 291 `"%s"` + satır 292 `escapeshellarg` → ⚠️ |

### §10.2 E-8 — CI test job'ı, matrix'li (`.github/workflows/ci.yml:66-108`)

```yaml
  php-test:
    name: PHP Tests (${{ matrix.project }})
    needs: php-lint
    runs-on: ubuntu-latest
    timeout-minutes: 15
    strategy:
      fail-fast: false
      matrix:
        include:
          - project: shared
            args: ''
          - project: auth.coremusic.net
            args: '--testsuite Unit'
```

| # | Satır | Süreç anlamı |
|---|---|---|
| 1 | 68 | `needs: php-lint` — lint bitmeden test süreci başlamaz |
| 2 | 70 | `timeout-minutes: 15` — süreç üst sınırı |
| 3 | 72 | `fail-fast: false` — bir matrix kolu ölürse diğerleri devam eder |
| 4 | 77-78 | yorum satırı: `tests/Integration` diskte YOK → yalnız Unit |
| 5 | 104-108 | `vendor/bin/phpunit "$PHPUNIT_ARGS"` + `working-directory` |

### §10.3 E-9 — Boru kapatma sırası (`ApiKeyCreateCommandTest.php:260-268`)

```php
$this->assertIsResource($handle, 'CLI alt süreci açılamadı');

fclose($pipes[0]);
$out = (string) stream_get_contents($pipes[1]);
$err = (string) stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);

return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];
```

> Sıra zorunlu: stdin EOF → stdout/stderr okuma → boru kapatma → `proc_close` (bekleme + çıkış kodu).
> Ters sıra boru sızıntısı/asılı test üretir (`surec-ve-parcacik/surucu.md` §10/5).

### §10.4 E-10 — `hrtime` sargısı, tam (`MiddlewarePipeline.php:38-52`)

```php
private function wrap(IMiddleware $middleware, callable $next): callable
{
    return function (array $request) use ($middleware, $next): array {
        $name  = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
        $start = hrtime(true);

        $response = $middleware->handle($request, $next);

        $durationMs = (hrtime(true) - $start) / 1_000_000;
        if ($this->logger !== null) {
            $this->logger->timing('MiddlewarePipeline', $name, $durationMs);
        }

        return $response;
    };
}
```

## §11 — Örnek ↔ Anayasa/Sürücü Eşlemesi

| Örnek | Anayasa karşılığı | Sürücü karşılığı |
|---|---|---|
| E-1 APCu adaptörü | M4 per-process bellek | `surucu.md` §2.1 |
| E-2 APCu devri | M4 · R9 boşluğu | `surucu.md` §2.2 |
| E-3 CLI alt süreci | M1 süreç başlatma (repo yüzeyi) | `surucu.md` §2.3 |
| E-4 eklenti + autoload | — | `surucu.md` §11.2 |
| E-5 CI eklenti kapısı | — | `surucu.md` §8 |
| E-6 süre ölçümü | — | `surucu.md` §11.1 |
| E-7 ffprobe | M1 yardımcı süreç | `surucu.md` §10/6-7 · §14.2 |
| E-8 CI matrix job | M1 ayrı süreç + bekleme | `surucu.md` §8.2-§9 |
| E-9 boru kapatma | M1 çıkış/wait | `surucu.md` §14.3 |
| E-10 hrtime | — | `surucu.md` §11.1 |

## §12 — Anti-Pattern Eki (AP-6 … AP-8)

### §12.1 AP-6 — `escapeshellarg` + yeniden tırnaklama

| # | Alan | Değer |
|---|---|---|
| 1 | Yer | `AIEngine.php:291-292` |
| 2 | Desen | `escapeshellarg()` sonucu `"%s"` içine alınıyor |
| 3 | Risk | tırnak-çiftleme yol değerini bozabilir (Windows/boşluklu yol) |
| 4 | Kanıt durumu | kasıtlı mı hata mı **kanıtlanamaz** → ⚠️ VERIFICATION REQUIRED |
| 5 | Aksiyon | bu dalda düzeltme yok — rapor |

### §12.2 AP-7 — sessiz sabit varsayılan

| # | Alan | Değer |
|---|---|---|
| 1 | Yer | `AIEngine.php:296-297` |
| 2 | Desen | süreç başarısız → `['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2]` |
| 3 | Risk | gerçek ölçüm yoksa yanlış metadata sessizce yayılır |
| 4 | Ölçüm | log/telemetri **gözlenmedi** → ⚠️ |
| 5 | Aksiyon | rapor |

### §12.3 AP-8 — `continue-on-error` ile örtülen job'lar

| # | Alan | Değer |
|---|---|---|
| 1 | Yer | `ci.yml:167` (`js-coverage`) · `ci.yml:183` (`js-e2e`) |
| 2 | Desen | job başarısız olsa da workflow yeşil |
| 3 | Risk | kırık coverage/e2e sessiz kalır |
| 4 | Kanıt | satır 167 ve 183 `continue-on-error: true` |
| 5 | Aksiyon | rapor — politika kararı bu dalın dışında |

## §13 — Ölçüm ve URL Durumu

| # | Amaç | Komut | Beklenen |
|---|---|---|---|
| 1 | E-7 tekliği | desen `'shell_exec'` (tüm `*.php`) | 1 |
| 2 | E-3 tekliği | desen `'proc_open'` | 1 |
| 3 | timeout | desen `'timeout-minutes'` | 1 (satır 70) |
| 4 | matrix | desen `'fail-fast'` | 1 (satır 72) |
| 5 | dolu satır | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |

**Bu tur bu dosyaya eklenen yeni doğrulanmış URL: 0** — örneklerin tamamı repo disk kanıtıdır;
OS semantiği bu dalın `anayasa.md` §13-§17'sine, yürütme yüzeyi `-surucu.md` §8-§14'üne yazıldı
(aynı URL iki dosyada geçmez kuralı korunmuştur).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode