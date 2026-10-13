---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik/metrik-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/Middleware/MiddlewarePipeline.php:46
  - shared/src/AI/AIWorkflow.php:51
  - shared/src/AI/ToolCalling.php:85
  - shared/src/Security/UuidV7.php:19
  - shared/src/PageRouter/StructuredLogger.php:47
  - https://man7.org/linux/man-pages/man1/perf-stat.1.html
  - https://man7.org/linux/man-pages/man1/iostat.1.html
  - https://man7.org/linux/man-pages/man1/sar.1.html
  - https://man7.org/linux/man-pages/man1/mpstat.1.html
  - https://man7.org/linux/man-pages/man1/pidstat.1.html
  - https://man7.org/linux/man-pages/man1/top.1.html
  - https://man7.org/linux/man-pages/man1/free.1.html
  - https://man7.org/linux/man-pages/man1/uptime.1.html
  - https://man7.org/linux/man-pages/man1/uname.1.html
  - https://man7.org/linux/man-pages/man8/ethtool.8.html
validation: "2026-10-13 — 6 repo kanıt satırı Read ile doğrulandı; her Dosya:Satır referansı gerçek satırla eşleşiyor; OS metrik aracı grep'i 0 isabet; procps/perf komutları yalnız tarif amaçlıdır, hiçbiri repo'da çağrılmıyor; sayısal ölçüm değeri yazılmadı; 2. tur: M-W19/M-W20/M-W21 webfetch HTTP 200 (sysinfo.2, getloadavg.3, perf-top.1); ci.yml tam dosya okundu (6 job envanteri, timeout-minutes 10–20, cancel-in-progress 17–19, PHP 8.4.22, %80 eşiği yorum + continue-on-error: true 165–167); dosya 408 non-blank satır"
open_questions:
  - "O1: OS-level metrik toplama (perf, ETW, /proc okuyucu) hedef mimaride var mı, yoksa yalnız uygulama süresi mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık metrik (Prometheus/OTel) hedef mimarida mı, yalnız uygulama içi süre mi? [UNKNOWN]"
group: kod
---

# metrik-ornekler — Repo Metrik Yüzeyi (gerçek disk kanıtı)

## §0 ELI15

Bu dosya CoreMusic deposunun **bugünkü** metrik yüzeyini gösterir. Deponun tek
ölçüm kalıpları `hrtime` ve `microtime` ile yazılmış **uygulama içi süre**
ölçümleridir — bunlar OS latency metriği **değildir**. Depoda OS metriği okuyan
hiçbir kod yoktur (grep 0 isabet, §5). Dosya iki bölümdür: (1) repo'da gerçekten
bulan şeyler (satır kanıtı), (2) OS tarafında **tarif** edilen ama repo'da hiç
çağrılmayan araçlar (komut kalıbı + kaynak URL). Ölçülmüş hiçbir sayısal değer
yazılmamıştır; her `[UNKNOWN]` işareti gerçek bir boşluktur.

## §1 Örnek okuma kuralı

| # | Kural | Uygulama |
|---|---|---|
| 1 | Her repo örneği `Dosya:Satır` ile gelir | satır Read ile okundu (2026-10-13) |
| 2 | OS araçları yalnız **tarif** amaçlıdır | repo'da hiçbiri çağrılmıyor (§6) |
| 3 | Ölçüm değeri yoksa `[UNKNOWN]` | sayı uydurulmaz |
| 4 | OS yorumu ayrı tablo sütunundadır | kod ile yorum karıştırılmaz |

## §2 Örnek 1 — hrtime: monotonik aralık ölçümü (en doğru kalıp)

**Dosya:** `shared/src/Middleware/MiddlewarePipeline.php` · **Satır:** 38–53

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

| # | Gözlem (satır) | OS yorumu |
|---|---|---|
| 1 | `hrtime(true)` (42) | monotonik nanosecond sayacı — `CLOCK_MONOTONIC` (M-W10) karşılığı |
| 2 | fark / 1_000_000 (46) | ns → ms çevirisi; arithmetic süre = aralık ölçümü |
| 3 | `timing()` çağrısı (48) | süre **log satırına** gider (`log-ornekler.md` §10); metrik deposuna değil |
| 4 | yalnız middleware sarmalama | syscall/kernel gecikmesi **ölçülmüyor** — OS latency metriği değildir |

**Sonuç:** bu kalıp aralık ölçümünün doğru API'sini kullanır; ancak çıktısı
log satırıdır — zaman serisi (OS metriği) oluşmaz.

## §3 Örnek 2 — microtime: duyarlı saat ile aralık ölçümü (API ikiliği)

**Dosya:** `shared/src/AI/AIWorkflow.php` · **Satır:** 51

```php
$startTime = microtime(true);
```

**Dosya:** `shared/src/AI/ToolCalling.php` · **Satır:** 85

```php
$startTime = microtime(true);
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | `microtime(true)` duyarlı saattir (wall clock) | `CLOCK_REALTIME` benzeri; NTP düzeltmesi sıçrayabilir |
| 2 | iki dosyada aynı API | repo içi tutarlılık — `hrtime`'dan farklılık **raporlanır** |
| 3 | aralık ölçümü monotonik kaynakla yapılmalı | anayasa §6 kuralı |

> Bu kalıp bir "hata" değildir; ancak aralık ölçümü için `hrtime` (§2) daha
> güvenlidir. Davranış değişikliği bu turda YOK — yalnız rapor.

## §4 Örnek 3 — UuidV7: zaman damgası üretimi (ölçüm değil)

**Dosya:** `shared/src/Security/UuidV7.php` · **Satır:** 17–23

```php
public static function generateBinary(): string
{
    $ms = (int)(microtime(true) * 1000);

    // 6-byte timestamp (48 bit)
    $time = pack('N', ($ms >> 16) & 0xFFFFFFFF)
          . pack('n', $ms & 0xFFFF);
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | milisaniye üretimi (19) | duyarlı saat OK — zaman damgası aralık ölçümü değildir |
| 2 | 48-bit timestamp (22–23) | kimlik zamanı, metrik zaman damgası değil |
| 3 | ölçüm yok | yalnız okuma/üretim |

## §5 Örnek 4 — StructuredLogger::timing: süre tek satırda loglanır

**Dosya:** `shared/src/PageRouter/StructuredLogger.php` · **Satır:** 47–53

```php
public function timing(string $module, string $middleware, float $durationMs): void
{
    $this->log('debug', $module, 'middleware_timing', [
        'middleware'  => $middleware,
        'durationMs' => round($durationMs, 3),
    ]);
}
```

| # | Gözlem | OS ilişkisi |
|---|---|---|
| 1 | `debug` seviyesi | üretimde (minLevel=info) **yazılmaz** — süre verisi production'da kaybolur |
| 2 | `round(…, 3)` | ms cinsinden 3 hane; uygulama hassasiyeti |
| 3 | metrik deposu yok | aggregation/percentile yok — OS sayaç okuması hiç yok |

## §6 Olumsuz kanıt — repo'da OS metrik okuma yok

| # | Desen | Beklenen kullanım | İsabet |
|---|---|---|---|
| 1 | `perf_event` | perf_event_open çağrısı | 0 |
| 2 | `/proc/stat` | CPU sayaç okuma | 0 |
| 3 | `/proc/loadavg` | load okuma | 0 |
| 4 | `proc/self` | süreç metriği okuma | 0 |
| 5 | `EventWrite` | ETW etkinlik yazımı | 0 |
| 6 | `syslog(` | syslog kütüphane çağrısı | 0 |
| 7 | `OpenTelemetry` / `OTel` | dağıtık metrik kütüphanesi | 0 |

> Repo geneli `*.{php,js,yml,yaml,json}` taraması (vendor hariç), 2026-10-13.

| # | Sonuç | Yorum |
|---|---|---|
| 1 | OS metrik katmanı repo'da **kullanılmıyor** | "eksiklik" değil, **tanımsızlık** (O1 [UNKNOWN]) |
| 2 | tek ölçüm yüzeyi uygulama içi süre | log kanalına gider, metrik kanalı yok |
| 3 | ETW PDH yüzeyi | ⚠️ VERIFICATION REQUIRED (PDH URL 404, kök -dogrulama §4 X6) |

## §7 OS araç referansı — tarif amaçlı (repo'da hiç çağrılmıyor)

> Bu komutlar **hiçbiri** repo'da değildir; yalnız hedef mimari kararı (O1)
> verildiğinde kullanılacak OS arayüzünün kanıtlı tarifidir. Her satırın URL
> kanıtı vardır (§8); HTTP 404 alan adaylar buraya alınmamıştır.

| # | Araç | Komut kalıbı | Ne ölçer | Kanıt |
|---|---|---|---|---|
| 1 | perf stat | `perf stat -e cycles,instructions -- sleep 10` | PMU sayaç toplamları | M-W15, M-W16 |
| 2 | iostat | `iostat -xz 1` | blok G/Ç süresi/istatistiği | M-W2 |
| 3 | sar | `sar -q 1 10` | yük ortalaması serisi | M-W3 |
| 4 | mpstat | `mpstat -P ALL 1` | çekirdek bazlı CPU dağılımı | M-W4 |
| 5 | pidstat | `pidstat -u 1` | süreç bazlı CPU | M-W5 |
| 6 | top | `top -b -n 1` | anlık canlı tablo (gauge) | M-W6 |
| 7 | free | `free -m` | bellek kalemleri | M-W7 |
| 8 | uptime | `uptime` | çalışma süresi + load average | M-W17 |
| 9 | uname | `uname -a` | sistem/sürüm kimliği | M-W8 |
| 10 | ethtool | `ethtool -S eth0` | ağ arayüzü sayaçları | M-W12 |

| # | Kural | Neden |
|---|---|---|
| 1 | Komut satırındaki sayı (`1`, `10`) **periyot argümanıdır, önerilen değer değildir** | periyot O2'de |
| 2 | `sleep 10` örnek süreç ömrüdür, ölçüm periyodu değil | `perf stat` toplamı örnektir |
| 3 | vmstat bu listede YOK — URL kanıtı 404 (kök -dogrulama §4 X2) | zero-hallucination |

## §8 Kaynak envanteri (2026-10-13, HTTP 200)

| # | İddia | URL | Durum |
|---|---|---|---|
| M-W2 | iostat(1) G/Ç istatistiği verir | https://man7.org/linux/man-pages/man1/iostat.1.html | 200 |
| M-W3 | sar(1) sistem aktivite kayıtçısıdır | https://man7.org/linux/man-pages/man1/sar.1.html | 200 |
| M-W4 | mpstat(1) çoklu CPU istatistiği verir | https://man7.org/linux/man-pages/man1/mpstat.1.html | 200 |
| M-W5 | pidstat(1) süreç bazlı kaynak istatistiği verir | https://man7.org/linux/man-pages/man1/pidstat.1.html | 200 |
| M-W6 | top(1) canlı süreç/kaynak izleyicisidir | https://man7.org/linux/man-pages/man1/top.1.html | 200 |
| M-W7 | free(1) bellek kullanımını gösterir | https://man7.org/linux/man-pages/man1/free.1.html | 200 |
| M-W8 | uname(1) sistem/sürüm bilgisi verir | https://man7.org/linux/man-pages/man1/uname.1.html | 200 |
| M-W12 | ethtool(8) ağ arayüzü sayaçlarını okur | https://man7.org/linux/man-pages/man8/ethtool.8.html | 200 |
| M-W15 | perf(1) Linux performans olay aracıdır | https://man7.org/linux/man-pages/man1/perf.1.html | 200 |
| M-W16 | perf stat PMU sayaçlarını raporlar | https://man7.org/linux/man-pages/man1/perf-stat.1.html | 200 |
| M-W17 | uptime(1) çalışma süresi + load average verir | https://man7.org/linux/man-pages/man1/uptime.1.html | 200 (bu tur) |
| M-W18 | perf_event_open(2) PMU sayaç syscall'ıdır | https://man7.org/linux/man-pages/man2/perf_event_open.2.html | 200 (bu tur) |

## §9 Repo↔OS karşılaştırma matrisi

| # | Ölçüm ihtiyacı | Repo karşılığı | OS karşılığı | Fark |
|---|---|---|---|---|
| 1 | aralık ölçümü | `hrtime` (MiddlewarePipeline:42) | clock_gettime MONOTONIC (M-W10) | repo doğru API'yi kullanıyor |
| 2 | duyarlı zaman | `microtime` (AIWorkflow:51) | CLOCK_REALTIME | repo duyarlı saat kullanıyor |
| 3 | CPU kullanımı | yok | perf stat / mpstat (M-W16, M-W4) | OS katmanı tanımsız |
| 4 | bellek kullanımı | yok (PHP memory_get_usage bu dalın dışı) | free / memory.current (M-W7, M-W13) | OS katmanı tanımsız |
| 5 | G/Ç süresi | yok | iostat (M-W2) | OS katmanı tanımsız |
| 6 | yük ortalaması | yok | uptime/sar (M-W17, M-W3) | OS katmanı tanımsız |
| 7 | süre→depo | `timing()` → log satırı | OS sayaç → okuyucu | repo'da metrik deposu yok |

## §10 Ölçüm — repo yüzeyinde de sayı yok

| # | Soru | Durum |
|---|---|---|
| 1 | middleware süresi ortalama/95. percentil kaç? | [UNKNOWN] — zaman serisi toplanmıyor |
| 2 | üretimde timing satırı üretiliyor mu? | `debug` seviyesi + minLevel `info` → üretilmiyor (`shouldLog` kapısı) |
| 3 | OS metrik hacmi (GB/gün vb.) | [UNKNOWN] — ölçüm yok |

> Dürüstlük notu: "timing production'da yazılmıyor" bir **kod okuması** sonucudur
> (StructuredLogger:47 debug çağırır · FileHandler shouldLog minLevel=info) —
> ölçüm değil, akış analizi.

## §11 K017 sınırı — bu dosyanın dokunmadığı alanlar

| # | Konu | Gittiği yer |
|---|---|---|
| 1 | Prometheus metriği / scrape | `K017-observability` |
| 2 | OTel metrik adı/kaydı | `K017-observability` |
| 3 | APM paneli / dashboard | `K017-observability` |
| 4 | alert kuralı (eşik + süre + hedef) | `K017-observability` |
| 5 | örnekleme/profil kaydı | `trace/` alt-dalı |

## §12 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS metrik toplama hedef mimaride var mı? | UNKNOWN |
| O2 | Toplama periyodu ve eşik değerleri ne olacak? | UNKNOWN |
| O3 | Dağıtık metrik hedef mimarida mı? | UNKNOWN |

## §13 CI yüzeyi — `.github/workflows/ci.yml` (tam dosya okundu, 2026-10-13)

**Dosya:** `.github/workflows/ci.yml` · 203 satır · **6 job** (dosyanın tamamı Read ile okundu).

| # | Job | Satır | Ne yapar | Metrik/log ilişkisi |
|---|---|---|---|---|
| 1 | `php-lint` | 25–64 | composer install + PHPStan (`composer stan`) | yalnız statik analiz çıktısı |
| 2 | `php-test` | 66–108 | PHPUnit (matrix: shared, auth `--testsuite Unit`) | test süresi CI loguna akar |
| 3 | `composer-audit` | 110–139 | 3 proje için bağımlılık denetimi | güvenlik çıktısı CI logunda |
| 4 | `js-test` | 142–158 | Vitest unit (`npm run test:js`) | — |
| 5 | `js-coverage` | 160–176 | kapsam raporu (`npm run test:js:coverage`) | eşik yorumu + soft job |
| 6 | `js-e2e` | 178–203 | Playwright e2e (`npm run test:e2e`) | soft job |

| # | Satır kanıtı | İçerik (diskten okundu) | Gözlem |
|---|---|---|---|
| 1 | `ci.yml:104–108` | `name: PHPUnit` / `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` | adım adı + argüman matrisi |
| 2 | `ci.yml:161` | `name: JS Coverage (gate — soft, A7)` | eşik kapısının **adı** soft |
| 3 | `ci.yml:165–166` | `A7 kararı: %80 eşiği şimdilik erişilemez (12.9k satır / <100 test)` | eşik gerekçesi **yorum satırında** yazılıdır |
| 4 | `ci.yml:167` | `continue-on-error: true` | job kırmızı görünür ama işi kırmaz |
| 5 | `ci.yml:175–176` | `name: Vitest coverage` / `run: npm run test:js:coverage` | kapsam çalıştırıcısı |
| 6 | `ci.yml:182–183` | `A6: e2e lock'a alındı; ilk koşular bilinen-riskli → soft job.` + `continue-on-error: true` | ikinci soft job |
| 7 | `ci.yml:17–19` | `concurrency: ... cancel-in-progress: true` | aynı ref'te eski iş iptal edilir |
| 8 | `ci.yml:21–22` | `env: PHP_VERSION: '8.4'` | yapılandırma sabiti — ölçüm değeri değil |
| 9 | `ci.yml:27,70,113,145,164,181` | `timeout-minutes: 15/15/10/10/15/20` | iş emniyet sınırıdır — **ölçülen süre değildir** |

| # | CI'da olan | CI'da olmayan | Kanıt |
|---|---|---|---|
| 1 | build/test adımları (6 job) | `/proc`, perf, cgroup okuma | tam dosya okundu |
| 2 | eşiğin yorum satırında gerekçesi | OS metrik toplama job'u | §6 grep 0 isabet |
| 3 | iki `continue-on-error` (167, 183) | OS log/journald yönlendirmesi | dosya kanıtı |

> CI çıktısı **geçici CI logudur** (GitHub saklar); OS günlüğüne yazmaz, log
> rotasyonu GitHub'ındır — `log/log-ornekler.md` §13 ile aynı kanıt.

## §14 CI ↔ repo ↔ OS sınıflandırma matrisi

| # | Ölçüm ihtiyacı | Repo karşılığı | CI karşılığı | OS karşılığı | Sınıf |
|---|---|---|---|---|---|
| 1 | aralık ölçümü | `hrtime` (MiddlewarePipeline:42) | yok | clock_gettime (M-W10) | uygulama içi |
| 2 | duyarlı zaman | `microtime` (AIWorkflow:51) | yok | CLOCK_REALTIME | uygulama içi |
| 3 | test kapsamı | yok | Vitest coverage (ci.yml:175–176) | — | kalite metriği |
| 4 | bağımlılık riski | yok | `composer audit` (ci.yml:123–127) | — | güvenlik çıktısı |
| 5 | CPU kullanımı | yok | yok | perf stat / mpstat (M-W16, M-W4) | OS — tanımsız |
| 6 | bellek | yok | yok | sysinfo / free (M-W19, M-W7) | OS — tanımsız |
| 7 | yük | yok | yok | uptime / getloadavg (M-W17, M-W20) | OS — tanımsız |
| 8 | G/Ç süresi | yok | yok | iostat (M-W2) | OS — tanımsız |
| 9 | log hacmi metriği | yok | yok | yok (dal sınırı §11) | **hiçbirinde yok** |

| # | Sınıf kuralı | Neden |
|---|---|---|
| 1 | CI eşiği (`%80`) OS eşiği **değildir** | kalite kapısı, sayaç okuması değil |
| 2 | CI süresi ölçümü OS latency **değildir** | runner sanal makinesi ölçülür |
| 3 | `timeout-minutes` ölçüm **değildir** | emniyet sınırı |
| 4 | OS sütunu boşsa "yok" **denmez**; `tanımsız` (O1) denir | yokluk ≠ imkânsızlık |

## §15 Zaman API karşılaştırması — genişletilmiş matris

| # | API | Kaynak türü | Repo kullanımı (disk) | Aralık ölçümü | OS karşılığı |
|---|---|---|---|---|---|
| 1 | `hrtime(true)` | monotonik (ns) | `MiddlewarePipeline.php:42` | uygun | CLOCK_MONOTONIC (M-W10) |
| 2 | `microtime(true)` | duyarlı (s) | `AIWorkflow.php:51`, `ToolCalling.php:85` | uygun değil | CLOCK_REALTIME benzeri |
| 3 | `microtime(true)*1000` | duyarlı → ms | `UuidV7.php:19` | ölçüm **değil** | zaman damgası üretimi |
| 4 | `DateTimeImmutable` ATOM | duyarlı | `StructuredLogger.php:63` | ölçüm **değil** | kayıt zaman damgası |
| 5 | `date('Y-m-d H:i:s')` | duyarlı | `FileHandler.php:209` | ölçüm **değil** | metin zaman damgası |

| # | Karar | Sonuç |
|---|---|---|
| 1 | aralık ölçümü | `hrtime` (§2) — duyarlı saat kullanılmaz |
| 2 | kayıt zamanı | duyarlı saat OK (anayasa §6 kural 2) |
| 3 | davranış değişikliği | bu turda **YOK** — yalnız rapor |
| 4 | OS geçişi | O1 çözülünceye kadar tanımsız |

## §16 Hata kataloğu — repo metrik yüzeyi

| # | Hata/hal | Kanıt | Sonuç | Dürüst tepki |
|---|---|---|---|---|
| 1 | `timing` `debug` seviyesinde | StructuredLogger:47 | üretimde (minLevel=info) yazılmaz | süre verisi production'da yok |
| 2 | `shouldLog` kapısı | FileHandler:75–80 | min seviye altı yutulur | filtre, sessizlik **değildir** raporlanır |
| 3 | `microtime` ile aralık | AIWorkflow:51, ToolCalling:85 | NTP sıçraması bozabilir | yalnız rapor — davranış değişikliği yok |
| 4 | OS metrik desenlerinde 0 isabet | §6 | OS katmanı yok | tanımsızlık (O1 [UNKNOWN]) |
| 5 | ETW/PDH adayı 404 | kök §4 X6 | doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| 6 | vmstat adayı 404 | kök §4 X2 | envantere alınmadı | kullanılmadı |
| 7 | `perf_event_paranoid` değeri okunmadı | envanter yok | — | `[UNKNOWN]` (surucu §2) |
| 8 | toplama periyodu tanımsız | O2 | — | `[UNKNOWN]` |
| 9 | ölçüm değeri üretilmiyor | §10 | — | sayı **uydurulmaz** |

## §17 Senaryo A — "timing'i metrik kanalına taşımak istesek ne gerekir?"

| # | Adım | Ne gerekir | Bu tur durumu |
|---|---|---|---|
| 1 | süre kaynağı | monotonik saat (§15 kural 1) | repo'da `hrtime` **var** (MiddlewarePipeline:42) |
| 2 | seviye kapısı | üretimde yazılacak seviye | `debug` → üretimde kapalı (§16.1) |
| 3 | depo | zaman serisi deposu | repo'da **yok** (§9 kural 2) |
| 4 | kimlik | etiket/dimensiyon kümesi | `traceId` var ama etiket değil (trace dalı) |
| 5 | OS katmanı | sayaç okuyucu | yok (§6) — O1 [UNKNOWN] |
| 6 | eşik | alarm eşiği | `K017-observability` (§11) |

| # | Karar | Gerekçe |
|---|---|---|
| 1 | bu adımların 3–6'sı bu dalın **kapsamı dışındadır** | K017 + K002-altyapi |
| 2 | 1–2. adım repo'da **raporlanır**, değiştirilmez | davranış değişikliği yasak |
| 3 | sonuç cümlesi: "repo'da metrik kanalı yok" | kanıt: §6 + §9 |

## §18 Senaryo B — "repo'da OS metriği olsaydı" ne değişirdi (tarif)

| # | Adım | Yüzey | Kanıt durumu |
|---|---|---|---|
| 1 | okuyucu süreci/izni | CAP_PERFMON / `perf_event_paranoid` | değer okunmadı → `UNKNOWN` |
| 2 | sayaç seçimi | `PERF_TYPE_*` ailesi (M-W18) | doğrulandı |
| 3 | kaynak yüzey | `/proc` (M-W1) / sysinfo (M-W19) | doğrulandı |
| 4 | periyot | toplama ajanı kararı (surucu §5) | `UNKNOWN` |
| 5 | depo/eşik | K017 | bu dalın dışı |
| 6 | CI | OS job'u eklenmez (onaysız yml değişmez) | `.github/AGENTS.md` §3.3 kural 4 |

| # | Bu senaryo neden yazılmadı | Kanıt |
|---|---|---|
| 1 | hedef mimari kararı verilmedi | O1 [UNKNOWN] |
| 2 | ölçüm yapılmadı | sayı yok → `UNKNOWN` |
| 3 | repo'da karşılığı yok | §6 grep 0 isabet |

## §19 Bu tur doğrulanan / reddedilen kaynaklar (2026-10-13)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | M-W19 | sysinfo(2) sistem özetini tek çağrıda verir (NOTES: /proc ile aynı bilgi) | https://man7.org/linux/man-pages/man2/sysinfo.2.html | 200 (bu tur) |
| 2 | M-W20 | getloadavg(3) en fazla 3 örnek verir, hatada `-1` | https://man7.org/linux/man-pages/man3/getloadavg.3.html | 200 (bu tur) |
| 3 | M-W21 | perf top(1) gerçek zamanlı profil + OVERHEAD CALCULATION | https://man7.org/linux/man-pages/man1/perf-top.1.html | 200 (bu tur) |
| 4 | — | `docs.kernel.org/trace/irqsoff-tracer.html` | (aday) | 404 — kullanılmadı |
| 5 | — | `man7.org/.../man8/perf-sched.8.html` | (aday) | 404 — kullanılmadı |

> Yeni URL'ler envanter kesişimi 0'dır (M-W1…M-W18 ile örtüşmez); kök
> `-dogrulama.md` §2–§3'teki 42 HTTP 200 URL bu dosyada §8'de taşınır.

## §20 Sık karıştırılan cümleler — düzeltme matrisi

| # | Yanlış cümle | Doğru cümle | Kanıt |
|---|---|---|---|
| 1 | "hrtime ölçüyor, o halde metrik" | süre **log satırına** gider, deposuna değil | §5 kural 3, §9 kural 2 |
| 2 | "timing production'da da var" | `debug` + minLevel `info` → üretilmiyor | §10 kural 2 |
| 3 | "coverage eşiği OS eşiği" | kalite kapısıdır | §14 kural 1 |
| 4 | "CI job süresi latency" | runner süresidir | §14 kural 2 |
| 5 | "0 isabet = OS metriği gereksiz" | tanımsızlık (O1) | §6 |
| 6 | "uuid üretimi ölçümdür" | kimlik üretimidir | §4 |
| 7 | "free/sysinfo aynı şey" | aynı veri yüzeyi, farklı arayüz (surucu §16) | M-W19 |
| 8 | "getloadavg 5 değer verir" | en fazla 3 | M-W20 |
| 9 | "vmstat kullanılabilir" | aday URL 404 | §19.5 |
| 10 | "periyot önerilebilir" | periyot `UNKNOWN` (O2) | §17.4 |

## §21 Ölçüm ve doğrulama adımları (bu tur eki)

| # | Adım | Araç | Durum |
|---|---|---|---|
| 1 | ci.yml tam okuma | Read (203 satır) | 6 job doğrulandı (§13) |
| 2 | ci.yml satır referansları | Read | 104–108 · 161 · 165–167 · 175–176 · 182–183 · 17–19 · 21–22 doğrulandı |
| 3 | OS metrik desenleri | grep | 0 isabet (§6) |
| 4 | repo satırları | Read | §2–§5 dosya:satır eşleşiyor |
| 5 | yeni URL'ler | webfetch | 3 × HTTP 200 (§19) |
| 6 | aday 404'ler | webfetch | 2 × HTTP 404 (§19) |
| 7 | sayısal ölçüm değeri | — | okunmadı → `UNKNOWN` |

## §22 Dosya:satır kanıt envanteri (bu dosyadaki tüm repo satırları)

| # | Dosya:satır | Ne | § |
|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php:38–53` | `wrap()` tam gövde | §2 |
| 2 | `shared/src/Middleware/MiddlewarePipeline.php:42` | `$start = hrtime(true);` | §2, §9 |
| 3 | `shared/src/Middleware/MiddlewarePipeline.php:46` | süre farkı / 1_000_000 | §2, §9 |
| 4 | `shared/src/Middleware/MiddlewarePipeline.php:48` | `timing()` çağrısı | §2 |
| 5 | `shared/src/AI/AIWorkflow.php:51` | `microtime(true)` başlangıç | §3, §9 |
| 6 | `shared/src/AI/AIWorkflow.php:80` | süre farkı | §3 |
| 7 | `shared/src/AI/ToolCalling.php:85` | `microtime(true)` başlangıç | §3, §9 |
| 8 | `shared/src/AI/ToolCalling.php:93` | süre farkı | §3 |
| 9 | `shared/src/Security/UuidV7.php:19` | ms üretimi (ölçüm değil) | §4 |
| 10 | `shared/src/Security/UuidV7.php:22–23` | 48-bit timestamp | §4 |
| 11 | `shared/src/PageRouter/StructuredLogger.php:9` | `LEVELS` 4 kademe | §15 |
| 12 | `shared/src/PageRouter/StructuredLogger.php:47–53` | `timing()` gövdesi | §5 |
| 13 | `shared/src/PageRouter/StructuredLogger.php:63` | ATOM timestamp | §15 |
| 14 | `shared/src/Log/FileHandler.php:75–80` | `shouldLog` kapısı | §10, §16 |
| 15 | `.github/workflows/ci.yml` (203 satır) | 6 job + satır kanıtları | §13 |

| # | Envanter kuralı | Neden |
|---|---|---|
| 1 | her satır Read ile diskten okundu | hafızadan yazılmaz |
| 2 | satır numarası dosyadaki gerçek numaradır | tahmin yok |
| 3 | kod alıntısı yalnız özet/parça biçiminde | tam dosya kopyalanmaz |
| 4 | yeni satır iddiası eklenmeden önce yeniden okunur | dosya değişebilir |

## §23 Bölüm → kanıt haritası

| # | Bölüm | Kanıt türü | Kaynak | Durum |
|---|---|---|---|---|
| 1 | §2 hrtime | disk | MiddlewarePipeline:38–53 | eşleşti |
| 2 | §3 microtime | disk | AIWorkflow:51 · ToolCalling:85 | eşleşti |
| 3 | §4 UuidV7 | disk | UuidV7:17–23 | eşleşti |
| 4 | §5 timing | disk | StructuredLogger:47–53 | eşleşti |
| 5 | §6 olumsuz | grep | 7 desen | 0 isabet |
| 6 | §7 araç referansı | URL | M-W2…M-W8, M-W12, M-W15…M-W18 | HTTP 200 |
| 7 | §8 kaynak envanteri | URL | 12 kayıt | 200 |
| 8 | §9 karşılaştırma | sentez | §2–§7 | — |
| 9 | §10 ölçüm | akış analizi | shouldLog + minLevel | kod okuması |
| 10 | §11 sınır | — | K017 | devredildi |
| 11 | §12 açık sorular | — | O1–O3 | UNKNOWN |
| 12 | §13 CI | disk | ci.yml (203 satır) | 6 job |
| 13 | §14 sınıflandırma | sentez | §6 + §13 + §7 | — |
| 14 | §15 zaman API | disk | §2–§5 satırları | eşleşti |
| 15 | §16 hata kataloğu | sentez | §5, §6, §10 | — |
| 16 | §17 senaryo A | sentez | §9, §11, kural seti | tarif |
| 17 | §18 senaryo B | açık soru | O1 | UNKNOWN |
| 18 | §19 bu tur kaynak | URL | 3 × 200 + 2 × 404 | webfetch |
| 19 | §20 sık karıştırma | sentez | §1–§19 | — |
| 20 | §21 doğrulama adımları | disk/URL/grep | Read · webfetch | bu tur |

| # | Harita kuralı | Neden |
|---|---|---|
| 1 | `disk` satır kanıtı en güçlüdür; `sentez` yeni kanıt **sayılmaz** | kanıt hiyerarşisi |
| 2 | `UNKNOWN` hücreleri haritada da görünür | gizli belirsizlik yasak |
| 3 | `0 isabet` bir sonuçtur, kanıtsızlık **değildir** | olumsuz kanıt geçerli |

## §24 Ölçülmeyenler envanteri — bu turda okunmayan/ölçülmeyen her şey

| # | Ölçülmeyen | Neden | Etiket |
|---|---|---|---|
| 1 | middleware süresi istatistiği | zaman serisi toplanmıyor | [UNKNOWN] |
| 2 | timing satır hacmi | ölçüm yapılmadı | [UNKNOWN] |
| 3 | OS metrik hacmi (GB/gün) | OS katmanı yok (§6) | [UNKNOWN] |
| 4 | toplama periyodu | O2 | [UNKNOWN] |
| 5 | eşiği (alert/iowait) | O2 | [UNKNOWN] |
| 6 | çekirdek sayısı | envanter okunmadı | [UNKNOWN] |
| 7 | `perf_event_paranoid` değeri | okunmadı | [UNKNOWN] |
| 8 | Windows/macOS metrik arayüzü | URL doğrulanmadı | ⚠️ |

| # | Kural | Neden |
|---|---|---|
| 1 | bu envanterdeki hiçbir sayı **tahmin edilmez** | zero-hallucination |
| 2 | `UNKNOWN` ile `⚠️` ayrımı korunur | bilinmeyen ≠ doğrulanmamış |
| 3 | ölçüm yapıldığında önce kök `-dogrulama.md`'ye, sonra bu dosyaya girer | envanter disiplini |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
