---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/trace/trace-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/trace
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://docs.kernel.org/trace/osnoise-tracer.html
  - https://docs.kernel.org/trace/histogram.html
  - https://man7.org/linux/man-pages/man1/perf-record.1.html
  - https://man7.org/linux/man-pages/man1/perf-script.1.html
  - https://docs.kernel.org/core-api/symbol-namespaces.html
  - https://learn.microsoft.com/en-us/windows/win32/api/evntrace/
  - https://learn.microsoft.com/en-us/windows/win32/api/evntcons/
  - https://docs.kernel.org/bpf/ringbuf.html
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/PageRouter/PageRouterKernel.php:87
validation: "2026-10-14 — §6.1 ETW toplama/sarma araçları eklendi (T-W16 logman, T-W17 tracerpt, T-W18 portal); §6 hata modu PDH satırı güncellendi (404 → M-W28–32 ile metrik dalında kapandı); §10 platform matrisi 9 satıra genişletildi (macOS: dtrace/fs_usage/sample/otool T-W19–22 ayna dipnotlu; Windows: logman/tracerpt); §12 kararı 5–6 güncellendi; §13 kaynakça +8 satır; §16.11/§18.3/§19.8 eski ⚠️ durumları kapatıldı; §20.1 eklendi; §21 harita §6.1/§10 satırları | 2026-10-13 — T-W4, T-W6, T-W9, T-W10, T-W11, T-W12, T-W13 kök -dogrulama.md §2'de HTTP 200; ringbuf.html bu turda HEAD 200; repo hrtime/traceId satırları Read ile doğrulandı; OS trace aracı grep 0 isabet; overhead/taşma/kayıp sayısal değeri yazılmadı (UNKNOWN); bu dosya 380–460 satır hedefine göre yazıldı; 2. tur: T-W14 (learn.microsoft.com evntprov) ve T-W15 (docs.kernel.org/trace/index.html) webfetch ile HTTP 200 doğrulandı; 3 aday URL HTTP 404 ile reddedildi (trace/irqsoff-tracer.html, perf-sched.8, logger.8); dosya 394 non-blank satır"
open_questions:
  - "O1: OS-level trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı, yoksa yalnız uygulama traceId'si mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---

# trace-surucu — OS Trace Sürme ve Ölçüm Rehberi

## §0 ELI15

Anayasa "trace nedir" der; bu sürücü **"nasıl sürülür ve nasıl ölçülür"**
der: osnoise tracer ile kesinti/gerçek zamanlı gecikme, histogram trigger ile
olay dağılımı, `perf record`/`perf script` ile örnekleme kaydı ve sembolizasyon,
ETW'de oturum başlatma/tüketme, halka tamponu taşması ve overhead ölçümü.
Repo bugün bu araçların **hiçbirini çağırmaz** (grep 0 isabet) — bu dosya
**tarif**tir, repo davranışı **değildir** (`-ornekler` kanıtı taşır).

## §1 osnoise tracer — kesinti ve gerçek zamanlı gecikme ölçümü

| # | Adım | Ne yapılır | Kaynak |
|---|---|---|---|
| 1 | Kaynak | osnoise tracer dokümanı okunur | T-W6 `osnoise-tracer.html` (HTTP 200) |
| 2 | Etkinleştirme | tracefs üzerinden tracer seçilir (dosya yolları katalogdan okunur — sabit yol uydurulmaz) | T-W1 `ftrace-design.html` |
| 3 | Örnekleme periyodu | ölçüm periyodu seçilir; değer kaynaklı yazılır | T-W6 |
| 4 | Okuma | tracer çıktısı okunur ve yorumlanır | T-W6 |
| 5 | Kapatma | tracer kapatılır; kapalıyken kanıt "yoktur" | T-W6 |

| # | osnoise'nin ölçtüğü | Türü | Karşılığı repo'da |
|---|---|---|---|
| 1 | kesinti (interrupt) süresi dağılımı | trace çıktısı | yok (grep 0 isabet) |
| 2 | gerçek zamanlı iş parçacığı gecikmesi | trace çıktısı | yok |
| 3 | çalıştırma/kesinti ayrımı zamanı | trace çıktısı | yok |

| # | osnoise sürme kararları | Değer | Not |
|---|---|---|---|
| 1 | periyot | kaynaklı seçilir | envanterde sayısal değer yok → `UNKNOWN` |
| 2 | hedef çekirdek/CPU | ölçüm kapsamı belirlenir | kapsam kaydı zorunlu |
| 3 | durdurma | tracer kapatılır | kapalıyken "0 olay" **denmez** (anayasa §9.4) |
| 4 | tekrarlanabilirlik | ayarlar kaydedilir | ölçüm-sonrası yorum için |

> Repo'daki `MiddlewarePipeline.php:42-46` (`hrtime` ile ms süresi) **bu
> ölçümün karşılığı değildir** — o uygulama içi istek süresidir (OS latency
> metriği değil; `-ornekler` §5).

| # | osnoise ↔ metrik dalı sınırı | Bu dal | `metrik` dalı |
|---|---|---|---|
| 1 | nesne | kesinti/gerçek zamanlı gecikme izi | CPU/bellek/IO sayaçları |
| 2 | araç ailesi | osnoise tracer | perf_event, /proc |
| 3 | çıktı | zaman aralıkları akısı | birikim/gauge değerleri |

## §2 Histogram trigger — olay dağılımını metriğe çevirmek

| # | Adım | Ne yapılır | Kaynak |
|---|---|---|---|
| 1 | Kaynak | histogram trigger dokümanı | T-W4 `histogram.html` (HTTP 200) |
| 2 | Kaynak olay | tracepoint/olay kataloğu seçilir | `events.html` ✅ |
| 3 | Anahtar | histogram anahtarı (pid, çağırıcı vb.) belirlenir | T-W4 |
| 4 | Değer | toplanan alan (süre, sayaç) belirlenir | T-W4 |
| 5 | Okuma | histogram tablosu okunur | T-W4 |

| # | Histogram ne verir | Ne vermez | Sınır |
|---|---|---|---|
| 1 | olayın dağılımı (anahtar bazında) | tekil olay akısının tam sırası | akım → `-anayasa` §1 |
| 2 | süre/sayı dağılımı | uygulama katmanı anlamı | yorum → `K017` |
| 3 | tetikleme (eşikte aksiyon) | iş alarmı | alarm → `K017` |

| # | histogram ↔ metrik dalı | Bu dal | `metrik` dalı |
|---|---|---|---|
| 1 | üretici | olay akışı + trigger | `/proc`, PMU, ETW sayacı |
| 2 | birim | olay/süre dağılımı | birikim/gauge |
| 3 | yeniden okuma | tracefs okuması | sayaç okuması |

## §3 perf record — örnekleme kaydı

| # | Adım | Ne yapılır | Kaynak |
|---|---|---|---|
| 1 | Olay seçimi | kaydedilecek olay seçimi (olay türü veya genel sayım) | T-W10 `perf-record.1.html` (HTTP 200) |
| 2 | Profil kaynağı | perf_event altyapısı (sayaç/örnekleme modu → `metrik` §1) | os-master §C.6 ✅ `perf_event_open.2.html` |
| 3 | Kayıt | örnekler dosyaya yazılır | T-W10 |
| 4 | Filtre | çekirdek/uygulama/kullanıcı ayrımı | T-W10 |
| 5 | Kapasite | taşma/kesme uyarısı yorumu — sayısal değersiz `UNKNOWN` | T-W10 |

| # | Kayıt modu | Ne yakalar | Kullanım anı |
|---|---|---|---|
| 1 | olay tetiklemeli (tracepoint/kprobe) | belirli olay anları | kesin olay izi |
| 2 | periyodik örnekleme | rastgele anlarda PC (program counter) | CPU profili |
| 3 | sayaç eşiği | birikim aşım anları | metrik eşik → `metrik` dalı |

> `perf record` ile **tracepoint tetiklemeli kayıt** arasındaki fark: birincisi olay
> anını yakalar, ikincisi rastgele örnekler. İkisi aynı altyapıyı (perf_event) kullanır
> — arayüz ortaklığı `metrik`-`trace` sınırındadır (§10).

## §4 perf script ve sembolizasyon

| # | Adım | Ne yapılır | Kaynak |
|---|---|---|---|
| 1 | Çözümleme | `perf script` ham kaydı satırlara çözümler | T-W11 `perf-script.1.html` (HTTP 200) |
| 2 | Sembol adları | çekirdek sembolleri ad uzaylarına göre etiketlenir | T-W9 `symbol-namespaces.html` (HTTP 200) |
| 3 | Hata modu | sembol bulunamazsa yalnız adres görünür — **uydurulmaz** | T-W9, T-W11 |
| 4 | Dışa aktarma | satır biçimi/uygulama işlenmesi | T-W11 |

| # | Sembolizasyon sorunu | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | sembol tablosu yok | ham adres | `UNKNOWN` — ad uydurulmaz |
| 2 | modül/uzay farkı | yanlış etiket | T-W9 ad uzayı notu |
| 3 | derleme bilgisi yok | dosya/satır yok | dosya:satır yazılmaz |

## §5 Halka tamponu ve taşma — kayıp olayların dürüstlüğü

| # | Mekanizma | Taşma davranışı | Kaynak |
|---|---|---|---|
| 1 | ftrace hattı (tek-yazıcı halka) | dolunca eski olay düşer; kayıp sayacı taşmayı gösterir | T-W1 (mekanizma); sayısal değer `UNKNOWN` |
| 2 | BPF ring buffer | üretici-tüketici halkası; taşma halka tarafından işaretlenir | **yeni** `bpf/ringbuf.html` (HEAD 200) |
| 3 | ETW oturum tamponu | oturum belleği dolarsa davranış oturum yapılandırmasına bağlı | T-W12 (sayısal değer `UNKNOWN`) |

Bağlayıcı taşma kuralları:

1. Taşma **vardır ve ölçülür**; ölçülmeden "kayıp yok" **denmez**.
2. Kayıp varsa sonuç **eksik izdir** — "hiç olay yok" ile karıştırılmaz.
3. Buffer boyutu, periyot, eşik değerleri envanterde yoksa `UNKNOWN` yazılır
   (`-anayasa` §11 kural 2).

| # | Taşma ile log halkasının farkı (`log-surucu` §2) | trace hattı | log dosyası |
|---|---|---|---|
| 1 | taşıma | çekirdek halka tamponu | dosya I/O |
| 2 | taşma sonucu | olay kaybı (iz eksilir) | logrotate/dosya (satır eksilir) |
| 3 | geri kazanım | yeniden başlatma gerekir | rotasyon dosyalarından |

| # | Taşma teşhis adımları (tarif) | Beklenen kanıt |
|---|---|---|
| 1 | kayıp sayacı okunur | sayaç arttıysa taşma var |
| 2 | periyot/buffer ayarı gözden geçirilir | ayar kaydı (§1.4) |
| 3 | olay hacmi azaltılır (filtre/hedef daraltma) | hacim tekrar ölçülür |
| 4 | ölçüm tekrarlanır | kayıp sayacı yeniden okunur |

> Adımlar **tekrarlanabilirlik** içindir; her adımda ölçülen sayısal değer
> envanterde yoksa `UNKNOWN` olarak kalır — tahmin yazılmaz.

## §6 ETW oturum mühendisliği (Windows)

| # | Adım | Ne yapılır | Kaynak |
|---|---|---|---|
| 1 | Sağlayıcı seçimi | izlenecek olay sağlayıcısı belirlenir | os-master §C.6 ✅ ETW portal |
| 2 | Oturum başlatma | `evntrace` ile oturum oluşturulur/yapılandırılır | T-W12 `api/evntrace/` (HTTP 200) |
| 3 | Kayıt yolu | canlı dinleme veya dosyaya yazma yapılandırması | T-W12 |
| 4 | Tüketme | `evntcons` ile olaylar okunur | T-W13 `api/evntcons/` (HTTP 200) |
| 5 | Oturum sonlandırma | oturum kapatılır, kaynak serbest bırakılır | T-W12 |
| 6 | WNODE yapıları | başlık/düğüm alanları doğru doldurulur | os-master §C.6 ✅ `wnode-header` |

| # | ETW hata modu | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | sağlayıcı bulunamadı | oturum boş döner | sağlayıcı adı doğrulanmadan yazılmaz |
| 2 | yetki yetersiz | oturum açılamaz | platform yetkisi envanterde yok → `UNKNOWN` |
| 3 | tampon taşması | olay kaybı | §5 kural 1 |
| 4 | PDH ile karıştırma | sayaç toplama sanılır | PDH **metrik** dalıdır; 2. turda M-W28–M-W32 ile kapandı (kök §4 X6 reddi korunur) |

## §6.1 ETW toplama/sarma araçları — 2. turda doğrulandı (2026-10-14)

| # | Araç | İş | Örnek | Kanıt |
|---|---|---|---|---|
| 1 | `logman` | ETW oturumu başlat/sdur/listele | `logman start -ets MyTrace -p "Microsoft-Windows-Kernel-Process" 0xFFFFFFFF` | T-W16 |
| 2 | `tracerpt` | ETL → rapor (CSV/HTML/XML) | `tracerpt C:\traces\MyTrace.etl -o report.html -of HTML` | T-W17 |
| 3 | ETW portal | teknoloji turu (genel portal) | — | T-W18 |

> `logman` aynı zamanda **performans toplayıcısı** da oluşturabilir
> (`logman create counter`, M-W26) — bu sayım **metrik** dalıdır; bu dosya
> yalnız ETW **oturum** tarafını taşır (§6).

## §7 Ölçüm adımları — bu dalın doğrulama protokolü

| # | Ölçüm | Adım | Kanıt türü |
|---|---|---|---|
| 1 | İz açık/kapalı maliyeti | tracer açılıp kapatılır, ölçüm yapılır | ölçüm gerekli → bu dosyada sayısal değer `UNKNOWN` |
| 2 | Taşma/kayıp | kayıp sayacı okunur | okunmadan sayı yazılmaz |
| 3 | Olay hacmi | olay sayacı okunur | `metrik` dalıyla ortak okuma yüzeyi |
| 4 | Repo yüzeyi | grep + Read | `-ornekler` (grep 0 isabet; satır kanıtları) |
| 5 | CI yüzeyi | `.github/workflows/ci.yml` okuması | `-ornekler` §8 |

| # | Ölçüm sonucu nereye yazılır | Nereye yazılmaz |
|---|---|---|
| 1 | bu dosya (mekanizma) | sayısal değer envantersiz → `UNKNOWN` |
| 2 | repo kanıtı → `-ornekler` | anayasaya kopyalanmaz |
| 3 | doğrulama kaydı → kök `-dogrulama.md` | URL doğrulanmadan envantere |

## §8 Olumsuz kanıt — repo'da OS trace yok

| # | Aranan yüzey | Desen (grep) | Sonuç (2026-10-13) |
|---|---|---|---|
| 1 | `*.php` `*.js` `*.yml` `*.json` `*.c` `*.cpp` `*.h` | `ftrace\|tracefs\|kprobe\|tracepoint\|perf_event\|bpftool\|bpf(` | 0 isabet |
| 2 | aynı yüzey | `EventWrite\|wevtutil\|logman\|ETW oturumu` | 0 isabet |
| 3 | aynı yüzey | `dtrace\|ktrace\|OpenTelemetry\|otlp` | 0 isabet |
| 4 | `.github/workflows/ci.yml` | trace aracı (perf, ftrace, ETW) | yok — tam dosya okundu |

| # | Repo'da BULUNAN (ama trace olmayan) | Satır | Neden trace değil |
|---|---|---|---|
| 1 | `traceId` üretimi | `shared/src/PageRouter/PageRouterKernel.php:87` | istek kimliği, olay akısı değil |
| 2 | `traceId` dağıtımı | `PageRouterKernel.php:88,91` | log satırlarına etiket |
| 3 | `hrtime` süresi | `shared/src/Middleware/MiddlewarePipeline.php:42,46` | uygulama içi süre, OS latency değil |
| 4 | `X-Trace-Id` başlığı | `shared/src/PageRouter/ResponseEmitter.php:97` | HTTP başlığı, trace hattı değil |

## §9 Süre ölçümü ile trace'in ayrımı (repo gerçeği)

| # | Yüzey | Ne ölçer | OS trace mi? | OS metrik mü? |
|---|---|---|---|---|
| 1 | `MiddlewarePipeline.php:42,46` | istek başlangıç→işlem süresi (hrtime) | hayır | hayır — uygulama içi süre |
| 2 | `PageRouterKernel.php:87` | istek kimliği üretimi | hayır | hayır |
| 3 | `StructuredLogger.php:66` | `traceId` alanının loga yazımı | hayır | hayır |
| 4 | ftrace/ETW/perf | OS olayları | **evet** | hayır (akım) |
| 5 | `/proc`, PMU sayaçları | OS sayaçları | hayır | **evet** (`metrik` dalı) |

| # | Kafa karışıklığı | Çözüm |
|---|---|---|
| 1 | "`traceId` = trace" | hayır — adı benzer, katmanı farklı (`-ornekler` §10) |
| 2 | "`hrtime` = latency metriği" | hayır — uygulama içi süre (`metrik-surucu` §9) |
| 3 | "`perf` hem metrik hem trace" | doğru ortaklık; sayaç → `metrik`, örnekleme kaydı → bu dal (§3) |

## §10 Platform surucu karşılaştırması (2. tur güncellendi — 2026-10-14)

| # | İş | Linux | Windows | macOS |
|---|---|---|---|---|
| 1 | iz açma/kapama | tracefs tracer seçimi (T-W1, T-W6) | `evntrace` oturumu (T-W12) · `logman start -ets` (T-W16) | DTrace (`dtrace.1`, T-W19, ayna) |
| 2 | dinamik prob | kprobe/uprobe (T-W3, T-W5) | ⚠️ eşlenik doğrulanmadı | DTrace dinamik prob (`dtrace.1`, T-W19, ayna) |
| 3 | programlanabilir mantık | eBPF verifier+ringbuf (§5 `-anayasa`) | ⚠️ doğrulanmadı | DScript (`dtrace.1`, T-W19, ayna) — sürüm `UNKNOWN` |
| 4 | olay okuma | ftrace/perf/ringbuf (T-W10, T-W11) | `evntcons` (T-W13) · `tracerpt` (T-W17) | `log show` / DTrace çıktı (T-W19, ayna) |
| 5 | dağılım toplama | histogram trigger (T-W4) | ⚠️ doğrulanmadı | ⚠️ doğrulanmadı |
| 6 | sembolizasyon | `perf script` + sembol ad uzayları (T-W9, T-W11) | ⚠️ doğrulanmadı | `otool(1)` Mach-O sembol inceleme (T-W22, ayna) |
| 7 | dosya sistemi izleme | `fs` tracepoint + eBPF | ⚠️ doğrulanmadı | `fs_usage(1)` (T-W20, ayna) |
| 8 | CPU örnekleme profili | `perf record` (T-W10) | ⚠️ doğrulanmadı | `sample(1)` (T-W21, ayna) |
| 9 | sayaç toplama | perf_event (→ `metrik`) | PDH → `metrik` dalı (M-W28–32, kapandı) | ⚠️ (→ `metrik`, M-W33–35) |

> **macOS hücre notu:** T-W19–T-W22 keith.github.io **üçüncü taraf
> aynasından** doğrulanmıştır (kök `-dogrulama.md` §3.1); hedef macOS
> sürümünde DTrace/ktrace kullanılabilirliği **`UNKNOWN`**. Kalan `⚠️`
> yalnız **doğrulanmamış** alt konular içindir.

## §11 K017 sınırı ve komşu haritalar

| # | Konu | Bu dal | Karşılığı |
|---|---|---|---|
| 1 | OTel span, korelasyon servisi | yalnız sınır tarifi | `K017-observability` |
| 2 | "gecikme > eşik → alarm" | ölçüm/iz | alarm → `K017-observability` |
| 3 | sayaç okuma/periyot | yalnız perf_event ortaklığı | `metrik` alt-dalı |
| 4 | log yazımı, rotasyon | yalnız trace↔log kesişimi | `log` alt-dalı (`log-surucu` §7 traceId köprüsü) |
| 5 | tracepoint kaynağı | yalnız yakalama | `isletim-sistemi-kernel` |
| 6 | gözlem testi stratejisi | yalnız gereksinim düzeyi | `isletim-sistemi-testing` |

## §12 Platform surucu kararları — özet

| # | Karar | Koşul | Kaynak |
|---|---|---|---|
| 1 | tracepoint/eBPF tercih edilir, kprobe geçicidir | kalıcı iz gerekiyorsa | `-anayasa` §4 |
| 2 | taşma/kayıp ölçülmeden sayı yazılmaz | her platform | §5 |
| 3 | ETW'de oturum + tüketici birlikte sürülür | Windows | T-W12, T-W13 |
| 4 | sembolizasyon yoksa adres olduğu yazılır | Linux | T-W9, T-W11 |
| 5 | macOS iddiaları ayna-dipnotlu ve sürüm-bilinmeyen kalır | 2. turda T-W19–T-W22 ayna ile doğrulandı; hedef sürüm `UNKNOWN` | kök §3.1 · §10 hücre notu |
| 6 | ETW toplama aracı ile performans toplayıcısı karıştırılmaz | `logman` ETW (T-W16) ≠ `logman create counter` (M-W26, metrik) | §6.1 |

## §13 Kaynakça (envanter dışı, HTTP 200 — 2026-10-13)

| # | Kaynak | URL | Doğrulama |
|---|---|---|---|
| 1 | osnoise tracer | https://docs.kernel.org/trace/osnoise-tracer.html | T-W6 HTTP 200 (§2) |
| 2 | histogram trigger | https://docs.kernel.org/trace/histogram.html | T-W4 HTTP 200 (§2) |
| 3 | perf record | https://man7.org/linux/man-pages/man1/perf-record.1.html | T-W10 HTTP 200 (§2) |
| 4 | perf script | https://man7.org/linux/man-pages/man1/perf-script.1.html | T-W11 HTTP 200 (§2) |
| 5 | sembol ad uzayları | https://docs.kernel.org/core-api/symbol-namespaces.html | T-W9 HTTP 200 (§2) |
| 6 | ETW oturum API | https://learn.microsoft.com/en-us/windows/win32/api/evntrace/ | T-W12 HTTP 200 (§2) |
| 7 | ETW tüketici API | https://learn.microsoft.com/en-us/windows/win32/api/evntcons/ | T-W13 HTTP 200 (§2) |
| 8 | BPF ring buffer | https://docs.kernel.org/bpf/ringbuf.html | **yeni** HEAD 200 |
| 9 | perf_event_open (ortak arayüz) | https://man7.org/linux/man-pages/man2/perf_event_open.2.html | os-master §C.6 ✅ yeniden kullanım |

**2. tur genişletme (2026-10-14):**

| # | Kaynak | URL | Doğrulama |
|---|---|---|---|
| 10 | `logman` ETW oturumu yönetir | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman | T-W16 HTTP 200 (§2.1) |
| 11 | `tracerpt` ETL → rapor | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/tracerpt | T-W17 HTTP 200 (§2.1) |
| 12 | ETW olay izleme portalı | https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal | T-W18 HTTP 200 (§2.1) |
| 13 | `dtrace(1)` (ayna) | https://keith.github.io/xcode-man-pages/dtrace.1.html | T-W19 HTTP 200 (§3.1) |
| 14 | `fs_usage(1)` (ayna) | https://keith.github.io/xcode-man-pages/fs_usage.1.html | T-W20 HTTP 200 (§3.1) |
| 15 | `sample(1)` (ayna) | https://keith.github.io/xcode-man-pages/sample.1.html | T-W21 HTTP 200 (§3.1) |
| 16 | `otool(1)` (ayna) | https://keith.github.io/xcode-man-pages/otool.1.html | T-W22 HTTP 200 (§3.1) |
| 17 | Apple kernel portalı | https://developer.apple.com/documentation/kernel | T-W23 HTTP 200 (§3.1) |

Doğrulanamayanlar (korunur): Windows **dinamik prob eşleniği** (⚠️), Windows
**dağılım toplama** (⚠️), Windows **sembolizasyon** (⚠️), macOS **dağılım
toplama** (⚠️) — bu alt konular bu turda aday bile değildi.
`trace-cmd.html` (bu turda doğrulanamadı → kullanılmadı).
**Kapananlar:** macOS DTrace/ktrace 1. turda doğrulanmamıştı → 2. turda
ayna ile kapandı (T-W19–T-W22, dipnotlu; sürüm `UNKNOWN`); Windows PDH
1. turda 404 idi → 2. turda M-W28–M-W32 ile metrik dalında kapandı.

## §14 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı? | UNKNOWN |
| O2 | Gözlemlenebilirlik üçlüsü K002-altyapi içinde nasıl konumlanacak? | UNKNOWN |
| O3 | Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? | UNKNOWN |

## §14 docs.kernel.org/trace/index.html — Linux Tracing Technologies çerçevesi

**Kanıt:** Linux Tracing Technologies rehberi (`https://docs.kernel.org/trace/index.html`, 2026-10-13 webfetch HTTP 200).

| # | Rehber konusu | Ne anlatır | Bu dalda karşılığı |
|---|---|---|---|
| 1 | ftrace | çekirdek iz altyapısı | §1 (anayasa §2) |
| 2 | kprobes | dinamik çekirdek prob | anayasa §4 |
| 3 | fprobe | fonksiyon prob + çağrı bilgisi | anayasa §4 (aynı aile) |
| 4 | eprobes | eBPF tabanlı prob | anayasa §4–§5 sınırı |
| 5 | lockless ring buffer | kilitlenmesiz halka tampon mekanizması | §5 |
| 6 | event tracing | olay takibi | anayasa §3 (`events.html`) |
| 7 | histograms | olay dağılımı toplama | §2 |
| 8 | osnoise | kesinti/gerçek zamanlı gecikme | §1 |
| 9 | timerlat | zamanlayıcı/kesinti gecikmesi ölçümü | §1 (aynı aile) |
| 10 | user_events | kullanıcı uzayı olay üretimi | anayasa §4.5 (kullanıcı uzayı) |
| 11 | uprobes | kullanıcı uzayı prob | anayasa §4 |
| 12 | boot-time tracing | açılışta iz | §17 (kapsam dışı — açılma senaryosu) |
| 13 | tracefs ring-buffer memory mapping | halka tamponunun bellek eşlemesi | §5 |

| # | Rehber kuralı | Neden |
|---|---|---|
| 1 | rehber **katalogdur** — tek tek komut satırı bu dosyaya kopyalanmaz | yanılma riski |
| 2 | konu adları kaynaktaki adlarla yazılır | uydurma ad yok |
| 3 | sayısal değer (buffer boyutu, gecikme) **alınmaz** | okunmadı → `UNKNOWN` |
| 4 | repo'da bu konuların **hiçbiri** çağrılmıyor (§8) | bu dosya tarifir |

## §15 evntprov — ETW sağlayıcı (üretici) API'si

**Kanıt:** `https://learn.microsoft.com/en-us/windows/win32/api/evntprov/` (2026-10-13 webfetch HTTP 200) — evntprov.h.

| # | Sembol | Rol | Akıştaki yeri |
|---|---|---|---|
| 1 | `EventRegister` | sağlayıcı kayıt oturumu açar | 1. adım |
| 2 | `EventWrite` | olayı yazar | 2. adım (tekrarlanır) |
| 3 | `EventWriteEx` | gelişmiş yazım (filtre/kuyruk seçenekleri) | 2. adım alternatifi |
| 4 | `EventWriteTransfer` | olayı belirli oturuma/aktiviteye transfer eder | 2. adım alternatifi |
| 5 | `EventUnregister` | oturumu kapatır, kaynak bırakır | 3. adım |
| 6 | `EVENT_DESCRIPTOR` | olay kimliği/yapısı taşıyıcısı | yapı taşı |
| 7 | `PENABLECALLBACK` | enable/disable geri çağrısı | dinamik aç/kapa |

| # | Sağlayıcı akışı (tarif) | Adım | Kural |
|---|---|---|---|
| 1 | `EventRegister` | oturum aç | açık oturum kaynak bırakmaz → kapatma zorunlu |
| 2 | `EventWrite*` | olay yaz | sağlayıcı adı/olay kimliği doğrulanmadan **yazılmaz** |
| 3 | `PENABLECALLBACK` | aç/kapa dinle | iz kapalıyken "0 olay" **denmez** (§16) |
| 4 | `EventUnregister` | oturum kapat | kapatma olmadan kaynak sızıntısı |

| # | evntprov ↔ evntrace/evntcons | evntprov (bu §) | evntrace/evntcons (§6) |
|---|---|---|---|
| 1 | rol | olay **üretici** API'si | oturum **yönetimi** + **tüketim** API'si |
| 2 | taraflar | sağlayıcı tarafında | kontrol/tüketici tarafında |
| 3 | birlikte kullanımı | evet — sağlayıcı yazımı oturum olmadan görünmez | oturum olmadan toplama çalışmaz |

| # | Hata modu | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | kayıt başarısız (`EventRegister`) | olay üretilmez | hata raporlanır; "olay yok" **denmez** |
| 2 | yetki yetersiz | kayıt açılamaz | platform yetkisi envanterde yok → `UNKNOWN` |
| 3 | unregister unutulursa | kaynak sızıntısı | akışta kapatma zorunlu |
| 4 | sağlayıcı adı uydurulursa | boş oturum | ad doğrulanmadan yazılmaz |
| 5 | PDH ile karıştırma | sayaç toplama sanılır | PDH metrik dalıdır → **M-W28–32 ile kapandı** (kök §4 X6 404'tü) |

> Repo'da `EventWrite`/`EventRegister` deseni **0 isabet** (§8) — bu tablo
> **tariftir**, repo davranışı **değildir**.

## §16 Hata kataloğu — trace sürme hataları (bu tur eki)

| # | Hata/hal | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | tracer kapalıyken okuma | veri yok | "iz yok" der, "olay yok" **demez** |
| 2 | halka tamponu taşması | kayıp sayacı artar | "iz eksik" der; kayıp **uydurulmaz** |
| 3 | sembol tablosu yok | ham adres | ad/satır **uydurulmaz** |
| 4 | prob noktası yok | prob eklenemez | sembol doğrulanır (anayasa §4) |
| 5 | verifier reddi (eBPF) | program yüklenmez | fail-safe — kapıdan geçmedi |
| 6 | ETW oturumu kapatılmaz | kaynak sızıntısı | kapatma zorunlu (§6, §15) |
| 7 | ETW sağlayıcısı bulunamaz | oturum boş döner | ad doğrulanmadan yazılmaz |
| 8 | trace kapalıyken "temiz rapor" | yanıltıcı temizlik | kapalı = kanıt yok (§16 anayasa §9.4) |
| 9 | sayısal ölçüm okunmadı | — | `UNKNOWN` yazılır |
| 10 | 404 aday URL kanıt sayılırsa | uydurma kaynak | reddedilenler listesinde (§20) |
| 11 | macOS iddiası genellenir | "macOS'ta da böyledir" | ayna-kanıtlı + sürüm-bilinmeyen (anayasa §8) |
| 12 | CI'da trace sanılması | yanlış güven | grep 0 isabet (§8, §17) |

## §17 Senaryo A — Linux'ta iz aç/kapa akışı (tarif)

| # | Adım | İşlem | Kaynak |
|---|---|---|---|
| 1 | çerçeve oku | `trace/index.html` konu listesi (§14) | bu tur HTTP 200 |
| 2 | olay noktası seç | `events.html` kataloğu | os-master §C.6 ✅ |
| 3 | nokta yoksa | kprobe/uprobe | anayasa §4 |
| 4 | koşul/filtre gerekirse | eBPF (verifier kapısı) | anayasa §5 |
| 5 | kayıt | ftrace hattı / ring buffer | §5 |
| 6 | okuma | dosya/syscall üzerinden | anayasa §7a |
| 7 | çözümleme | `perf script` / histogram | §3, §2 |
| 8 | kapatma | tracer/prob kapatılır | §1 kural |
| 9 | rapor | sayısal değer okunmadıysa `UNKNOWN` | §16 |

| # | Akış kuralı | Neden |
|---|---|---|
| 1 | her adım **tariftir**; repo'da bu yolu çağıran kod yok | §8 grep 0 isabet |
| 2 | kapatma yapılmadan "ölçüm bitti" **denmez** | kaynak temizliği |
| 3 | adımların 5–6'sı yalnız OS yüzeyi | uygulama katmanına taşınmaz |
| 4 | boot-time tracing (§14.12) bu senaryonun **dışında** | açılma senaryosu ayrı kapsamdır |

## §18 Senaryo B — Windows'ta ETW sağlayıcı akışı (tarif)

| # | Adım | Sembol/API | Kural |
|---|---|---|---|
| 1 | sağlayıcı seç | doğrulanmış ad yok → `UNKNOWN` | ad uydurulmaz |
| 2 | oturum kur | `evntrace` (T-W12) | oturum merkezli (§6) |
| 3 | sağlayıcı kaydı | `EventRegister` (§15) | kayıt hatası hata sayılır |
| 4 | olay yazımı | `EventWrite` / `EventWriteEx` / `EventWriteTransfer` | `EVENT_DESCRIPTOR` ile |
| 5 | aç/kapa dinleme | `PENABLECALLBACK` | kapalıyken "0 olay" denmez |
| 6 | tüketim | `evntcons` (T-W13) | tüketici olmadan yalnız toplama |
| 7 | kapatma | `EventUnregister` + oturum sonu | kaynak bırakılmaz |

| # | Akış kuralı | Neden |
|---|---|---|
| 1 | üretim ve tüketim **aynı API ailesinde değildir** | evntprov vs evntrace/evntcons (§15) |
| 2 | sayısal tampon boyutu yazılmaz | envanterde yok → `UNKNOWN` |
| 3 | PDH bu akışa **eklenmez** | PDH metrik dalıdır; 2. turda M-W28–32 ile kapandı (kök §4 X6 reddi korunur) |
| 4 | repo'da `EventWrite` 0 isabet | §8 |

## §19 Sık karıştırılan cümleler — düzeltme matrisi

| # | Yanlış cümle | Doğru cümle | Kanıt |
|---|---|---|---|
| 1 | "traceId var, trace'imiz var" | traceId korelasyon kimliğidir; olay izi yok | anayasa §1 |
| 2 | "hrtime latency ölçer" | uygulama içi aralıktır | anayasa §9 (§9 bu dosya) |
| 3 | "perf hem metrik hem trace'tir" | ortak arayüz: sayım metrik, kayıt trace | anayasa §3, §3 bu dosya |
| 4 | "ETW = PDH" | ETW olay izidir; PDH sayacı **metrik** dalıdır | §6, §15 |
| 5 | "osal tracer kapalı, olay yok" | kapalıyken kanıt yoktur | §16 |
| 6 | "trace rehberi komut listesidir" | katalogdur; komut kopyalanmaz | §14 kural 1 |
| 7 | "sağlayıcı adı tahmin edilebilir" | doğrulanmadan yazılmaz | §15 |
| 8 | "macOS trace da doğrulandı" | yalnız ayna-kanıtlı (T-W19–22, dipnotlu); hedef sürüm `UNKNOWN` | anayasa §8 |
| 9 | "CI'da strace/perf var" | grep 0 isabet | §8 |
| 10 | "eBPF tracepoint'i değiştirir" | üzerinde çalışır; değiştirmez | anayasa §5 kural 2 |
| 11 | "kprobe üretimdedir" | teşhis amaçlı geçicidir | anayasa §4 |
| 12 | "user_events repo'da kullanılıyor" | rehber konusudur; repo'da yok | §14, §8 |

## §20 Bu tur ek doğrulanan / reddedilen kaynaklar (2026-10-13)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | T-W14 | evntprov.h ETW sağlayıcı API'sidir (EventRegister/EventWrite/EventUnregister) | https://learn.microsoft.com/en-us/windows/win32/api/evntprov/ | 200 (bu tur) |
| 2 | T-W15 | Linux Tracing Technologies rehberi trace konularını kataloglar | https://docs.kernel.org/trace/index.html | 200 (bu tur) |
| 3 | — | `docs.kernel.org/trace/irqsoff-tracer.html` | (aday) | 404 — kullanılmadı |
| 4 | — | `man7.org/.../man8/perf-sched.8.html` | (aday) | 404 — kullanılmadı |

> Doğrulanamayanlar **1. turda** korunmuştu; **2. tur (2026-10-14)** iki
> maddeyi kapattı: macOS DTrace/ktrace → T-W19–T-W22 (ayna, dipnotlu);
> Windows PDH → M-W28–M-W32 (metrik dalı). `trace-cmd.html` hâlâ
> doğrulanamadı → kullanılmadı. T-W14, evntrace/evntcons ile **farklı API
> ailesidir** (üretici vs oturum) — envanter kesişimi 0.

### §20.1 2. tur ek doğrulanan kaynaklar (2026-10-14)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | T-W16 | `logman` ETW trace/toplayıcı oturumu yönetir | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman | 200 |
| 2 | T-W17 | `tracerpt` ETL ham verisini rapor/format'a çevirir | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/tracerpt | 200 |
| 3 | T-W18 | ETW olay izleme portalı (teknoloji turu) | https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal | 200 |
| 4 | T-W19 | `dtrace(1)` dinamik izleme derleyicisi (ayna) | https://keith.github.io/xcode-man-pages/dtrace.1.html | 200 (ayna) |
| 5 | T-W20 | `fs_usage(1)` dosya sistemi/çağrı izlemesi (ayna) | https://keith.github.io/xcode-man-pages/fs_usage.1.html | 200 (ayna) |
| 6 | T-W21 | `sample(1)` süreç CPU örnekleme profili (ayna) | https://keith.github.io/xcode-man-pages/sample.1.html | 200 (ayna) |
| 7 | T-W22 | `otool(1)` Mach-O ikili yapısı/sembolleri (ayna) | https://keith.github.io/xcode-man-pages/otool.1.html | 200 (ayna) |
| 8 | T-W23 | Apple kernel/çekirdek dokümantasyon portalı | https://developer.apple.com/documentation/kernel | 200 |

## §21 Bölüm → kanıt haritası

| # | Bölüm | Kanıt türü | Kaynak | Durum |
|---|---|---|---|---|
| 1 | §1 osnoise | URL | T-W6, T-W1 | 200 |
| 2 | §2 histogram | URL | T-W4, `events.html` | 200 |
| 3 | §3 perf record | URL | T-W10 | 200 |
| 4 | §4 perf script | URL | T-W11, T-W9 | 200 |
| 5 | §5 halka tamponu | URL | T-W1, `bpf/ringbuf.html` | 200/HEAD 200 |
| 6 | §6 ETW oturum | URL | T-W12, T-W13 | 200 |
| 7 | §7 ölçüm adımları | — | protokol | sayı `UNKNOWN` |
| 8 | §8 olumsuz kanıt | grep | 4 desen + 4 repo satır | 0 isabet |
| 9 | §9 süre↔trace ayrımı | disk | Read | eşleşti |
| 10 | §10 platform matrisi | sentez + ⚠️ | §1–§9 + §6.1 | 2. tur: Windows/macOS hücreleri dolduruldu; kalan ⚠️ = doğrulanmamış alt konular |
| 10.1 | §6.1 ETW toplama araçları | URL | T-W16–T-W18 | 200 (2. tur) |
| 11 | §11 K017 sınırı | — | devredildi | — |
| 12 | §12 karar özeti | sentez | §1–§11 | — |
| 13 | §13 kaynakça | URL | 9 kayıt | 200 |
| 14 | §14 açık sorular | — | O1–O3 | UNKNOWN |
| 15 | §15 trace index rehberi | URL | `trace/index.html` | 200 (bu tur) |
| 16 | §16 evntprov | URL | `api/evntprov/` | 200 (bu tur) |
| 17 | §17 hata kataloğu | sentez | §1–§16 | — |
| 18 | §18 senaryo A | sentez | §1–§7 | tarif |
| 19 | §19 senaryo B | sentez | §6, §16 | tarif |
| 20 | §20 sık karıştırma | sentez | §1–§19 | — |
| 21 | §21 bu tur kaynak/red | webfetch | 2 × 200 + 2 × 404 | bu tur |

| # | Harita kuralı | Neden |
|---|---|---|
| 1 | her § kanıt türüne bağlanır | kanıtsız bölüm sayılmaz |
| 2 | §7/§14 hücrelerinde sayı **yoktur** | ölçüm yapılmadı → `UNKNOWN` |
| 3 | 404 adaylar §21'de kalır, §1–§20'ye **sınmaz** | red kaydı korunur |
| 4 | bu tablo yeni iddia **taşımez** | mükerrer kanıt üretilmez |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
