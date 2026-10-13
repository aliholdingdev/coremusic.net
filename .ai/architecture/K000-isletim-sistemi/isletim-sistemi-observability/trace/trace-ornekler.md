---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/trace/trace-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/trace
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - shared/src/PageRouter/PageRouterKernel.php:87
  - shared/src/PageRouter/PageRouterKernel.php:88
  - shared/src/PageRouter/PageRouterKernel.php:91
  - shared/src/PageRouter/StructuredLogger.php:66
  - shared/src/PageRouter/StructuredLogger.php:72
  - shared/src/Log/FileHandler.php:65
  - shared/src/Log/FileHandler.php:213
  - shared/src/PageRouter/ResponseEmitter.php:97
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/Middleware/MiddlewarePipeline.php:46
  - shared/src/AI/ToolCalling.php:85
  - shared/src/Security/UuidV7.php:19
  - .github/workflows/ci.yml
validation: "2026-10-13 — tüm repo satırları Read/grep ile diskten okundu; OS trace aracı deseni (ftrace|tracefs|kprobe|tracepoint|perf_event|bpftool|bpf(|EventWrite|wevtutil|logman|dtrace|ktrace|OpenTelemetry|otlp) php/js/yml/json/c/cpp/h yüzeyinde 0 isabet; ci.yml trace aracı yok (grep 0 isabet + tam dosya okundu); macOS DTrace/ktrace ve PDH resmi URL doğrulanmadı → ⚠️; sayısal süre/overhead değeri yazılmadı; bu dosya 380–460 satır hedefine göre yazıldı; 2. tur: T-W14 (evntprov EventWrite ailesi) ve T-W15 (docs.kernel.org/trace index) webfetch HTTP 200; ci.yml tam dosya envanteri §24'e eklendi (6 job, trace aracı yok, timeout/cancel-in-progress satır numaralarıyla); 404 redleri: trace/irqsoff-tracer.html, perf-sched.8, logger.8, openlog.3.html içerik yanıltma (syslog(3) sayfası döndü — L-W1 olarak sayılmadı); dosya 419 non-blank satır"
open_questions:
  - "O1: OS-level trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı, yoksa yalnız uygulama traceId'si mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: kod
---

# trace-ornekler — Repo Örnekleri ve Olumsuz Kanıt

## §0 ELI15

Bu dosya repo'nun **gerçek satırlarını** gösterir ve **göstermez**:
repo'da `traceId` üretilir, dağıtılır, loga ve HTTP başlığına yazılır — ama
bunların **hiçbiri OS trace değildir** (ftrace/ETW/eBPF çağıran tek bir satır
yoktur: grep 0 isabet). Süre ölçümü `hrtime`/`microtime` ile yapılır — bu
uygulama içi süredir, syscall latency **değildir**. Tüm satırlar Read/grep ile
diskten okunmuştur; hiçbir satır hafızadan yazılmamıştır.

## §1 Örnek okuma kuralı

| # | Kural | Uygulama |
|---|---|---|
| 1 | Her satır kanıtı dosya:satır biçimindedir ve diskten okunmuştur | Read/grep |
| 2 | "yok" iddiası olumsuz grep ile kanıtlanır (desen + yüzey + sonuç) | §7 |
| 3 | traceId ≠ OS trace ayrımı her örnekte tekrarlanır | §10 |
| 4 | Doğrulanamayan platform iddiası `⚠️` kalır | §11 |
| 5 | Süre değeri okunmadan yazılmaz; yalnız ölçüm yöntemi tarif edilir | §5 |

## §2 Örnek 1 — traceId üretimi ve dağıtımı (tek istek, tek kimlik)

| # | Dosya:satır | İçerik (diskten okundu) | Rol |
|---|---|---|---|
| 1 | `shared/src/PageRouter/PageRouterKernel.php:87` | `$traceId = self::generateTraceId();` | istek başına kimlik üretimi |
| 2 | `shared/src/PageRouter/PageRouterKernel.php:88` | `$this->logger->setTraceId($traceId);` | JSON logger'a kimlik |
| 3 | `shared/src/PageRouter/PageRouterKernel.php:89` | `// P5: FileHandler satırları da aynı traceId ile damgalansın —` | yorum: iki logger'ı bağlama niyeti |
| 4 | `shared/src/PageRouter/PageRouterKernel.php:91` | `\CoreMusic\Log\FileHandler::setTraceId($traceId);` | metin logger'a aynı kimlik |

| # | Bu örnek trace mi? | Gerekçe |
|---|---|---|
| 1 | **hayır** | kimlik üretildi; olay **akısı** kaydedilmedi (zaman damgası sıralı olaylar yok) |
| 2 | **hayır** | çıktı log satırı etiketidir (`StructuredLogger` §3) |
| 3 | **hayır** | dosya I/O üzerinden yürür; OS trace hattı çekirdek halkasıdır |

| # | OS trace karşılığı (tarif) | Ne yapardı | Repo durumu |
|---|---|---|---|
| 1 | tracepoint/eBPF olay kaydı | isteğin çekirdek içindeki yolunu sırayla yakalardı | yok (grep 0 isabet) |
| 2 | ETW oturumu | Windows'ta aynı işi yapardı | yok |

## §3 Örnek 2 — StructuredLogger: traceId alanının loga düşmesi

| # | Dosya:satır | İçerik (diskten okundu) | Rol |
|---|---|---|---|
| 1 | `shared/src/PageRouter/StructuredLogger.php:11` | `private string $traceId = '';` | alan tanımı |
| 2 | `shared/src/PageRouter/StructuredLogger.php:17` | `public function setTraceId(string $traceId): void` | setter |
| 3 | `shared/src/PageRouter/StructuredLogger.php:66` | `'traceId'   => $this->traceId,` | JSON satırına alan |
| 4 | `shared/src/PageRouter/StructuredLogger.php:72` | `error_log(Redactor::redact($payload));` | tek satır JSON'un yazıldığı yer |

| # | Zincir | Katman |
|---|---|---|
| 1 | `PageRouterKernel:87` → `setTraceId` → `:66` alan → `:72` `error_log` | uygulama logu |
| 2 | OS trace hattı (tracefs/ETW) | **bu zincirin hiçbir adımında yok** |

> `:72`'deki `error_log()` uygulama katmanının **tek** JSON yazıcısıdır — bu,
> log–trace köprüsünün uygulama ucudur (OS tarafı: `log-anayasa` §1).

## §4 Örnek 3 — FileHandler: metin satırına `[trace=…]` eki

| # | Dosya:satır | İçerik (diskten okundu) | Rol |
|---|---|---|---|
| 1 | `shared/src/Log/FileHandler.php:37` | `private static string $traceId = '';` | statik alan |
| 2 | `shared/src/Log/FileHandler.php:65` | `public static function setTraceId(string $traceId): void` | setter |
| 3 | `shared/src/Log/FileHandler.php:213` | `$trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';` | satır eki |
| 4 | `shared/src/Log/FileHandler.php:15` | `*    (StructuredLogger traceId'i ile AYNI değer → istek stitching).` | yorum: stitching niyeti |

| # | Ne yapar | Ne yapmaz |
|---|---|---|
| 1 | metin satırına istek kimliği ekler | çekirdek olayı kaydetmez |
| 2 | iki logger'ı **aynı değerle** diker (stitching) | sıra/zaman damgası akısı üretmez |
| 3 | boşsa ek **koymaz** (dürüst boşluk) | "trace yok" olayını **uydurmaz** |

## §5 Örnek 4 — hrtime: uygulama içi süre (syscall latency değil)

| # | Dosya:satır | İçerik (diskten okundu) | Rol |
|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php:42` | `$start = hrtime(true);` | monotonik başlangıç |
| 2 | `shared/src/Middleware/MiddlewarePipeline.php:46` | `$durationMs = (hrtime(true) - $start) / 1_000_000;` | ms süresi |

| # | Bu süre nedir? | Değildir |
|---|---|---|
| 1 | PHP süreci içinde bir middleware adımının süresi | syscall latency (OS ölçümü) |
| 2 | monotonik saat (`hrtime`) | perf/osnoise ölçümü |
| 3 | uygulama katmanı | OS trace/metrik katmanı |

## §6 Örnek 5 — microtime: duyarlı saat ile süre ve kimlik

| # | Dosya:satır | İçerik (diskten okundu) | Kullanım |
|---|---|---|---|
| 1 | `shared/src/AI/ToolCalling.php:85` | `$startTime = microtime(true);` | araç çağrısı başlangıcı |
| 2 | `shared/src/AI/ToolCalling.php:93` | `$executionTime = microtime(true) - $startTime;` | süre farkı |
| 3 | `shared/src/AI/AIWorkflow.php:51` | `$startTime = microtime(true);` | iş akımı başlangıcı |
| 4 | `shared/src/AI/AIWorkflow.php:80` | `$executionTime = microtime(true) - $startTime;` | süre farkı |
| 5 | `shared/src/Security/UuidV7.php:19` | `$ms = (int)(microtime(true) * 1000);` | UUID zaman damgası (ölçüm **değil**) |

| # | `microtime` ile trace arasındaki fark |
|---|---|
| 1 | **aralık** ölçer (başlangıç→bitiş), olay **akısı** kaydetmez |
| 2 | saat `microtime` duyarlıdır, `hrtime` monotoniktir — ikisi de uygulama içidir |
| 3 | OS trace olayları `CLOCK_MONOTONIC` benzeri çekirdek kaynağıyla damgalanır (kaynak: `-anayasa` §7; sayısal sapma `UNKNOWN`) |

## §7 Örnek 6 — X-Trace-Id: HTTP başlığına kimlik taşımak

| # | Dosya:satır | İçerik (diskten okundu) | Rol |
|---|---|---|---|
| 1 | `shared/src/PageRouter/ResponseEmitter.php:97` | `header('X-Trace-Id: ' . $traceId);` | yanıt başlığı |
| 2 | `shared/src/PageRouter/ResponseEmitter.php:16` | `public function emit(array $response, ?string $traceId = null, ...)` | parametre |

| # | Başlık ne taşır | Ne taşımaz |
|---|---|---|
| 1 | isteğin kimliğini istemciye | olay akısını |
| 2 | log–istek korelasyonu | OS trace verisi |
| 3 | uygulama katmanı sözleşmesi | tracefs/ETW veri modeli (`-anayasa` §7) |

## §8 Örnek 7 — CI: trace aracı yok (dosya kanıtı)

| # | Kanıt | Sonuç |
|---|---|---|
| 1 | `.github/workflows/ci.yml` grep (`trace\|perf\|ftrace\|strace\|bpf`) | **0 isabet** |
| 2 | `.github/workflows/ci.yml` tam dosya okundu | trace aracı adı geçmiyor |
| 3 | CI yalnız build/test yüzeyi | OS trace işi CI'da tanımsız |

| # | CI'da OLAN | CI'da OLMAYAN |
|---|---|---|
| 1 | build/test adımları (workflow dosyası mevcut) | perf/ftrace/eBPF çağrısı |
| 2 | — | ETW/kprobe/ktrace çağrısı |
| 3 | — | OpenTelemetry/OTLP aktarımı |

## §9 Olumsuz kanıt — grep matrisi (2026-10-13)

| # | Desen | Yüzey | Sonuç |
|---|---|---|---|
| 1 | `ftrace` | `*.php` `*.js` `*.yml` `*.json` `*.c` `*.cpp` `*.h` | 0 isabet |
| 2 | `tracefs` | aynı | 0 isabet |
| 3 | `kprobe` | aynı | 0 isabet |
| 4 | `tracepoint` | aynı | 0 isabet |
| 5 | `perf_event` | aynı | 0 isabet |
| 6 | `bpftool` / `bpf(` | aynı | 0 isabet |
| 7 | `EventWrite` / `wevtutil` / `logman` | aynı | 0 isabet |
| 8 | `dtrace` / `ktrace` | aynı | 0 isabet |
| 9 | `OpenTelemetry` / `otlp` | aynı | 0 isabet |

| # | Sonucun anlamı | Değil |
|---|---|---|
| 1 | repo OS trace **çağırmıyor** | "trace gereksiz" **değil** (O1 [UNKNOWN]) |
| 2 | repo uygulama katmanında kalıyor | OS trace'in imkânsız olduğu **değil** |
| 3 | traceId bu boşluğu **doldurmuyor** | traceId'nin anlamsız olduğu **değil** |

## §10 traceId ≠ OS trace — ayrım matrisi

| # | Kriter | Repo traceId | OS trace (ftrace/ETW) |
|---|---|---|---|
| 1 | Üreten | PHP kodu (`PageRouterKernel.php:87`) | çekirdek (tracepoint/kprobe/eBPF · ETW sağlayıcısı) |
| 2 | Birim | log satırı/HTTP yanıtı etiketi | olay (zaman damgası + pid/tid/cpu) |
| 3 | Sıra | dosyaya ekleme sırası + istek sırası | oturum/hat içi tam sıra |
| 4 | Taşıma | `error_log` dosya I/O · `header()` | çekirdek halka tamponu · ETW oturum tamponu |
| 5 | Kaldırma | kod kararı | tracer/oturum kapama |
| 6 | Boşluk davranışı | alan yoksa eklenmez (`FileHandler.php:213`) | trace kapalıysa **hiç olay yok** ("0 olay" değil) |
| 7 | Kapsam | tek istek (uygulama) | sistem/çekirdek (OS) |
| 8 | Bu dalın kapsamında | yalnız ayrım | **evet** |

| # | Sık karıştırma | Doğru cümle |
|---|---|---|
| 1 | "traceId var, trace'imiz var" | traceId korelasyon kimliğidir; olay izi yoktur |
| 2 | "hrtime latency ölçüyor" | hrtime uygulama içi aralıktır; OS latency ölçümü değildir |
| 3 | "CI'da strace var" | grep 0 isabet (§8) |

## §11 ⚠️ VERIFICATION REQUIRED kayıtları

| # | İddia | Neden ⚠️ | Kaynak |
|---|---|---|---|
| 1 | macOS trace (DTrace/ktrace) | resmi URL doğrulanmadı | kök `-dogrulama.md` §15.1 (§474 kaydı da aynı) |
| 2 | Windows PDH toplama | aday URL HTTP 404 | kök `-dogrulama.md` §4 X6 · §15.2 |
| 3 | `trace-cmd` aracının sürüm/komut yüzeyi | bu turda doğrulanamadı | kullanılmadı (`-anayasa` §12 reddedilenler) |
| 4 | ETW'de kprobe eşleniği | doğrulanmış eşlenik yok | `⚠️` (`-anayasa` §8) |

> Bu iddialar bu dosyada **tarif bile edilmez**; yalnızca varlıkları ve
> doğrulanmamış oldukları kaydedilir. Doğrulanana kadar URL **yazılmaz**.

## §12 Ölçüm — repo yüzeyinde de sayı yok

| # | Ölçmek istenen | Repo'da var mı? | Kanıt |
|---|---|---|---|
| 1 | OS trace olay hacmi | hayır | grep 0 isabet (§9) |
| 2 | trace açma/kapama maliyeti | hayır | tracer çağıran kod yok |
| 3 | taşma/kayıp | hayır | halka tamponu yok |
| 4 | uygulama süresi | var (yöntem) | `hrtime`/`microtime` (§5, §6) — **değer okunmadı, yazılmadı** |
| 5 | istek başına kimlik | var | `PageRouterKernel.php:87` |

| # | Dürüstlük kuralı | Uygulama |
|---|---|---|
| 1 | kod satırı okunmadan alıntılanmaz | tüm §2–§8 satırları Read/grep |
| 2 | sayısal süre değeri okunmadan yazılmaz | yalnız yöntem (`hrtime`, `microtime`) |
| 3 | "yok" deseni belirtilir | §9 desen+Yüzey+Sonuç |
| 4 | doğrulanmayan URL yazılmaz | §11 |

## §13 OS araç referansı — tarif amaçlı (repo'da hiç çağrılmıyor)

| # | Araç | Platform | Ne yapar | Kaynak durumu |
|---|---|---|---|---|
| 1 | ftrace/tracefs | Linux | tracepoint/olay izi | T-W1, T-W2 ✅ |
| 2 | kprobe/uprobe | Linux | dinamik prob | T-W3, T-W5 ✅ |
| 3 | eBPF (`bpf(2)`) | Linux | programlanabilir olay mantığı | T-W7, T-W8, verifier/helpers/ringbuf ✅ (yeni) |
| 4 | `perf record`/`perf script` | Linux | örnekleme kaydı + çözümleme | T-W10, T-W11 ✅ |
| 5 | `evntrace`/`evntcons` | Windows | ETW oturumu + tüketici | T-W12, T-W13 ✅ |
| 6 | DTrace/ktrace | macOS | ⚠️ VERIFICATION REQUIRED | resmi URL doğrulanmadı |
| 7 | PDH | Windows | ⚠️ (metrik dalı) | aday URL 404 |

> Hiçbiri repo'da çağrılmıyor (§9); tablo **tariftir**, repo davranışı
> **değildir**.

## §14 K017 sınırı — bu dosyanın dokunmadığı alanlar

| # | Alan | Gittiği dal | Bu dosyanın yaptığı |
|---|---|---|---|
| 1 | OTel span üretimi, korelasyon servisi | `K017-observability` | yalnız traceId ile ayrım (§10) |
| 2 | alarm/alert kuralları | `K017-observability` | yok |
| 3 | sayaç okuma/periyot | `metrik` alt-dalı | yalnız perf_event ortaklığına atıf |
| 4 | log formatı/rotasyon | `log` alt-dalı | yalnız `:72`/`:213` köprü kanıtı |
| 5 | tracepoint kaynağı | `isletim-sistemi-kernel` | yok |
| 6 | OS araç testi stratejisi | `isletim-sistemi-testing` | yok |

## §15 Kaynakça (2026-10-13)

| # | Tür | Kaynak | Durum |
|---|---|---|---|
| 1 | repo satır kanıtı | §2–§8 dosya:satırları | Read/grep ile diskten okundu |
| 2 | olumsuz kanıt | §9 grep matrisi | 0 isabet (2026-10-13) |
| 3 | CI kanıtı | `.github/workflows/ci.yml` | tam dosya okundu + grep 0 |
| 4 | OS trace URL'leri | kök `-dogrulama.md` T-W1…T-W13 | HTTP 200 |
| 5 | yeni URL'ler | `bpf/verifier.html` · `bpf/helpers.html` · `bpf/ringbuf.html` | HTTP 200 (2026-10-13) |
| 6 | ⚠️ kayıtları | macOS DTrace/ktrace · PDH | kök `-dogrulama.md` §15.1, §15.2, §4 X6 |

## §16 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı, yoksa yalnız uygulama traceId'si mi? | UNKNOWN |
| O2 | Gözlemlenebilirlik üçlüsü K002-altyapi içinde nasıl konumlanacak? | UNKNOWN |
| O3 | Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? | UNKNOWN |

## §17 CI yüzeyi — `.github/workflows/ci.yml` (tam dosya okundu, 2026-10-13)

**Dosya:** `.github/workflows/ci.yml` · 203 satır · 6 job — trace aracı **0 isabet** (§8.4).

| # | Job | Satır | Ne yapar | Trace ilişkisi |
|---|---|---|---|---|
| 1 | `php-lint` | 25–64 | composer install + PHPStan | yok |
| 2 | `php-test` | 66–108 | PHPUnit (matrix shared/auth) | yok |
| 3 | `composer-audit` | 110–139 | bağımlılık denetimi (3 proje) | yok |
| 4 | `js-test` | 142–158 | Vitest unit | yok |
| 5 | `js-coverage` | 160–176 | kapsam (`test:js:coverage`) — `continue-on-error: true` (167) | yok |
| 6 | `js-e2e` | 178–203 | Playwright e2e — `continue-on-error: true` (183) | yok |

| # | Satır kanıtı | İçerik | Trace yorumu |
|---|---|---|---|
| 1 | `ci.yml:104–108` | `PHPUnit` adımı | test zamanı ölçümleri CI logunadır — trace değil |
| 2 | `ci.yml:165–167` | `%80 eşiği erişilemez` yorumu + `continue-on-error: true` | kalite eşiği — OS eşiği değil |
| 3 | `ci.yml:182–183` | `e2e lock'a alındı` + `continue-on-error: true` | soft job — trace eşiği **değildir** |
| 4 | `ci.yml:27,70,113,145,164,181` | `timeout-minutes` 15/15/10/10/15/20 | emniyet sınırıdır — süre ölçümü değil |
| 5 | `ci.yml:17–19` | `cancel-in-progress: true` | eski iş iptali — olay izi değildir |
| 6 | `ci.yml:21–22` | `PHP_VERSION: '8.4'` | yapılandırma — olay kimliği değil |

| # | CI'da OLAN | CI'da OLMAYAN | Kanıt |
|---|---|---|---|
| 1 | build/test adımları | perf/ftrace/eBPF çağrısı | tam dosya okundu (§8.4) |
| 2 | iki soft job (167, 183) | ETW/kprobe/ktrace çağrısı | dosya kanıtı |
| 3 | eşik yorumları | OTLP aktarımı | dosya kanıtı |
| 4 | PHPUnit/Vitest/Playwright çıktıları | tracepoint seçimi/kapatma | dosya kanıtı |

> CI çıktısı **geçici CI logudur** (GitHub saklar); OS trace hattına **yazmaz**.
> `traceId` da CI'a taşınmaz (workflow dosyasında `trace` deseni 0 isabet).

## §18 traceId sınıflandırma — repo traceId yüzeylerinin envanteri

| # | Yüzey | Dosya:satır | Ne yapar | OS trace mi? |
|---|---|---|---|---|
| 1 | üretim | `PageRouterKernel.php:87` | istek başına kimlik | hayır |
| 2 | JSON logger dağıtımı | `PageRouterKernel.php:88` | `setTraceId` | hayır |
| 3 | metin logger dağıtımı | `PageRouterKernel.php:91` | statik `setTraceId` | hayır |
| 4 | JSON alanı | `StructuredLogger.php:11,17,66` | alan + setter + satır alanı | hayır |
| 5 | JSON yazımı | `StructuredLogger.php:72` | `error_log` | hayır |
| 6 | metin satırı eki | `FileHandler.php:37,65,213` | `[trace=…]` eki | hayır |
| 7 | HTTP başlığı | `ResponseEmitter.php:97` | `X-Trace-Id` | hayır |
| 8 | CI | `.github/workflows/ci.yml` | traceId geçmiyor | — |

| # | traceId'nin taşıdığı | Taşımadığı |
|---|---|---|
| 1 | tek isteğin log satırlarını birleştirme anahtarı | olay akısı |
| 2 | istemci↔sunucu korelasyonu (HTTP başlığı) | çekirdek olayı |
| 3 | iki logger'ı aynı değerde diken etiket | zaman damgası sırası |
| 4 | — | pid/tid/cpu bağlamı |
| 5 | — | OS zaman kaynağı |

| # | traceId üretim kuralı | Kanıt |
|---|---|---|
| 1 | istek başına **bir kez** üretilir | `PageRouterKernel.php:87` |
| 2 | iki logger'a **aynı değer** verilir | `:88`, `:91` |
| 3 | boşsa metin satırına ek **konmaz** | `FileHandler.php:213` |
| 4 | biçimi/uzunluğu bu dosyada **yazılmaz** | üretici kod okundu; şema `UNKNOWN` |

## §19 Hata kataloğu — repo trace yüzeyi

| # | Hata/hal | Kanıt | Sonuç | Dürüst tepki |
|---|---|---|---|---|
| 1 | OS trace desenlerinde 0 isabet | §9 | OS trace yok | tanımsızlık (O1) |
| 2 | CI'da trace aracı yok | §8, §17 | CI trace'i tanımaz | raporlanır |
| 3 | `traceId` alanı boşsa | `FileHandler.php:213` | ek konmaz | "trace yok" **uydurulmaz** |
| 4 | `hrtime` latency sanılırsa | §5 | uygulama içi süre | yanlış sınıflandırma önlenir |
| 5 | `microtime` aralığı NTP ile sıçrarsa | §6 | aralık bozulabilir | yalnız rapor |
| 6 | macOS trace iddiası | §11.1 | resmi URL yok | `⚠️` |
| 7 | PDH adayı 404 | §11.2 | metrik dalı | `⚠️` |
| 8 | `trace-cmd` doğrulanamadı | §11.3 | kullanılmadı | kanıtsız geçmez |
| 9 | ETW kprobe eşleniği doğrulanmadı | §11.4 | — | `⚠️` |
| 10 | trace kapalıyken "olay yok" denmesi | anayasa §9.4 | yanıltıcı | "iz yok" denir |
| 11 | ölçüm okunmadı | §12 | — | sayı **uydurulmaz** |
| 12 | aday URL 404 (irqsoff, perf-sched) | §22 | envantere alınmadı | reddedildi |

## §20 Senaryo — traceId ile stitching (istek birleştirme) akışı

| # | Adım | Ne olur | Dosya:satır |
|---|---|---|---|
| 1 | istek gelir | `generateTraceId()` çağrılır | `PageRouterKernel.php:87` |
| 2 | JSON logger bağlanır | `setTraceId($traceId)` | `:88` |
| 3 | metin logger bağlanır | statik `setTraceId` | `:91` |
| 4 | iş akar | middleware süreleri ölçülür (hrtime) | `MiddlewarePipeline.php:42,46` |
| 5 | JSON satır yazılır | alan `traceId` dolu → satırda | `StructuredLogger.php:66` |
| 6 | metin satır yazılır | `[trace=…]` eki (boşsa eklenmez) | `FileHandler.php:213` |
| 7 | yanıt döner | `X-Trace-Id` başlığı | `ResponseEmitter.php:97` |
| 8 | sorgulama | aynı traceId'li satırlar dosyada birleşir | uygulama katmanı |

| # | Bu senaryonun **olmadığı** yer | Neden |
|---|---|---|
| 1 | çekirdek olay akısı | ftrace/ETW yok (§9) |
| 2 | zaman damgası sırası garantisi | dosyaya ekleme sırası yalnız |
| 3 | pid/tid/cpu bağlamı | olay veri modeli yok (anayasa §7) |
| 4 | CI trace akışı | ci.yml'de yok (§17) |

| # | Stitching kuralı | Neden |
|---|---|---|
| 1 | stitching = aynı etiketle dosyada arama | olay izi **değildir** |
| 2 | iki logger farklı biçimde yazar (JSON / metin) | birleştirme yalnız traceId ile |
| 3 | OS trace istenirse §13'teki araçlar gerekir | repo'da hiçbiri yok |

## §21 Sık karıştırılan cümleler — düzeltme matrisi (ek)

| # | Yanlış cümle | Doğru cümle | Kanıt |
|---|---|---|---|
| 1 | "X-Trace-Id trace verisi taşır" | kimlik taşır | §7 |
| 2 | "CI'da coverage soft job trace soft job'dur" | kalite eşiğidir | §17 |
| 3 | "hrtime trace kaynağı" | uygulama içi aralıktır | §5 |
| 4 | "traceId boşsa olay yoktur" | ek konmaz; iz yoktur | §4 |
| 5 | "timeout-minutes trace eşiğidir" | emniyet sınırıdır | §17 |
| 6 | "repo'da trace yok = gereksiz" | tanımsızlık (O1) | §9 |
| 7 | "OS araçları tarif ediliyorsa repo'da var" | tablo tariftir, repo **değildir** | §13 |
| 8 | "ETW yazımı = PDH okuması" | farklı API ailesi | §13, surucu §15 |
| 9 | "macOS'ta da aynı" | doğrulanmadı → `⚠️` | §11 |
| 10 | "perf record = perf stat" | örnekleme kaydı vs sayma toplamı | §13.4 |

## §22 Bu tur ek doğrulanan / reddedilen kaynaklar (2026-10-13)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | T-W14 | evntprov.h ETW sağlayıcı API'sidir | https://learn.microsoft.com/en-us/windows/win32/api/evntprov/ | 200 (bu tur) |
| 2 | T-W15 | Linux Tracing Technologies rehberi konu katalogudur | https://docs.kernel.org/trace/index.html | 200 (bu tur) |
| 3 | — | `docs.kernel.org/trace/irqsoff-tracer.html` | (aday) | 404 — kullanılmadı |
| 4 | — | `man7.org/.../man8/perf-sched.8.html` | (aday) | 404 — kullanılmadı |

> Bu dosyadaki yeni URL'ler **repo iddiasını** değil, **OS arayüzünün
> varlığını** doğrular; repo yüzeyi §9'daki grep ile değişmez.

## §23 Ölçüm ve doğrulama adımları (bu tur eki)

| # | Adım | Araç | Durum |
|---|---|---|---|
| 1 | ci.yml tam okuma | Read (203 satır) | 6 job + satır referansları doğrulandı (§17) |
| 2 | ci.yml trace desenleri | grep | 0 isabet (§8.4) |
| 3 | traceId yüzeyleri | Read | §18 dosya:satırları diskten okundu |
| 4 | OS trace desenleri | grep | 0 isabet (§9) |
| 5 | yeni URL'ler | webfetch | 2 × HTTP 200 (§22) |
| 6 | aday 404'ler | webfetch | 2 × HTTP 404 (§22) |
| 7 | sayısal süre/overhead | — | okunmadı → `UNKNOWN` |
| 8 | stitching senaryosu | Read | §20 satırları mevcut kodla eşleşiyor |

## §24 Dosya:satır kanıt envanteri (bu dosyadaki tüm repo satırları)

| # | Dosya:satır | Ne | § |
|---|---|---|---|
| 1 | `PageRouterKernel.php:87` | `generateTraceId()` | §2, §18 |
| 2 | `PageRouterKernel.php:88` | JSON logger `setTraceId` | §2, §18 |
| 3 | `PageRouterKernel.php:89` | P5 yorumu (stitching niyeti) | §2 |
| 4 | `PageRouterKernel.php:91` | metin logger statik `setTraceId` | §2, §18 |
| 5 | `StructuredLogger.php:11` | `$traceId` alan tanımı | §3, §18 |
| 6 | `StructuredLogger.php:17` | `setTraceId()` setter | §3 |
| 7 | `StructuredLogger.php:66` | JSON satırına `traceId` alanı | §3, §18 |
| 8 | `StructuredLogger.php:72` | `error_log(Redactor::redact(...))` | §3, §18 |
| 9 | `FileHandler.php:37` | statik `$traceId` alanı | §4, §18 |
| 10 | `FileHandler.php:65` | statik setter | §4 |
| 11 | `FileHandler.php:15` | stitching yorumu | §4 |
| 12 | `FileHandler.php:213` | `[trace=…]` satır eki | §4, §18 |
| 13 | `MiddlewarePipeline.php:42` | `hrtime(true)` | §5, §18 |
| 14 | `MiddlewarePipeline.php:46` | süre farkı | §5 |
| 15 | `ToolCalling.php:85` · `:93` | `microtime` başlangıç/fark | §6 |
| 16 | `AIWorkflow.php:51` · `:80` | `microtime` başlangıç/fark | §6 |
| 17 | `UuidV7.php:19` | UUID zaman damgası | §6 |
| 18 | `ResponseEmitter.php:16` · `:97` | `X-Trace-Id` başlığı | §7, §18 |
| 19 | `.github/workflows/ci.yml` | 203 satır / 6 job | §8, §17 |

| # | Envanter kuralı | Neden |
|---|---|---|
| 1 | her satır Read ile diskten okundu | hafızadan yazılmaz |
| 2 | satır numarası gerçek dosya numarasıdır | tahmin yok |
| 3 | kod alıntısı özet/parçadır | tam dosya kopyalanmaz |
| 4 | yeniden okunmadan iddia tazelenmez | dosya değişebilir |

## §25 Bölüm → kanıt haritası

| # | Bölüm | Kanıt türü | Durum |
|---|---|---|---|
| 1 | §2 traceId üretimi | disk | eşleşti |
| 2 | §3 JSON logger | disk | eşleşti |
| 3 | §4 metin logger | disk | eşleşti |
| 4 | §5 hrtime | disk | eşleşti |
| 5 | §6 microtime | disk | eşleşti |
| 6 | §7 X-Trace-Id | disk | eşleşti |
| 7 | §8 CI | disk + grep | 0 isabet |
| 8 | §9 grep matrisi | grep | 0 isabet (9 desen) |
| 9 | §10 ayrım matrisi | sentez | — |
| 10 | §11 ⚠️ kayıtları | — | 4 kayıt ⚠️ |
| 11 | §12 ölçüm | sentez | sayı `UNKNOWN` |
| 12 | §13 OS araç referansı | URL | T-W serisi 200 |
| 13 | §14 K017 sınırı | — | devredildi |
| 14 | §15 kaynakça | URL | 200 |
| 15 | §16 açık sorular | — | O1–O3 |
| 16 | §17 CI sınıflandırma | disk | ci.yml 203 satır |
| 17 | §18 traceId sınıflandırma | disk | 8 yüzey |
| 18 | §19 hata kataloğu | sentez | — |
| 19 | §20 stitching senaryosu | disk | §24 satırları |
| 20 | §21 sık karıştırma | sentez | — |
| 21 | §22 bu tur kaynak/red | webfetch | 2 × 200 + 2 × 404 |
| 22 | §23 doğrulama adımları | disk/URL/grep | bu tur |

| # | Harita kuralı | Neden |
|---|---|---|
| 1 | `disk` kanıtı `URL` kanıtından **bağımsızdır** | repo gerçeği ≠ OS arayüzü |
| 2 | `0 isabet` bir sonuçtur | olumsuz kanıt geçerli |
| 3 | ⚠️ hücreleri haritada da görünür | gizli belirsizlik yasak |
| 4 | bu tablo yeni iddia **taşımez** | mükerrer kanıt üretilmez |

## §26 Ölçülmeyenler envanteri — bu tur okunmayan/ölçülmeyen her şey

| # | Ölçülmeyen | Neden | Etiket |
|---|---|---|---|
| 1 | OS trace olay hacmi | OS trace repo'da yok (§9) | [UNKNOWN] |
| 2 | iz açma/kapama maliyeti | tracer çağıran kod yok | [UNKNOWN] |
| 3 | halka tamponu taşması/kaybı | halka tamponu yok | [UNKNOWN] |
| 4 | `hrtime` süre **değeri** | değer okunmadı, yalnız yöntem | [UNKNOWN] |
| 5 | traceId biçimi/uzunluğu | üretici kod okundu; şema yok | [UNKNOWN] |
| 6 | macOS trace arayüzü | resmi URL yok | ⚠️ |
| 7 | Windows PDH | aday 404 | ⚠️ |
| 8 | ETW kprobe eşleniği | doğrulanmadı | ⚠️ |

| # | Kural | Neden |
|---|---|---|
| 1 | tahmini sayı yazılmaz | zero-hallucination |
| 2 | `UNKNOWN` ≠ `⚠️` | bilinmeyen ≠ doğrulanmamış |
| 3 | ölçüm önce kök `-dogrulama.md`'ye girer | envanter disiplini |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
