---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/isletim-sistemi-observability-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://www.rfc-editor.org/rfc/rfc3164
  - https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - shared/src/PageRouter/StructuredLogger.php:72
  - shared/src/Log/FileHandler.php:220
  - shared/src/PageRouter/PageRouterKernel.php:87
validation: "2026-10-13 — 3 kanıt URL'si HTTP HEAD ile doğrulandı (3/3 HTTP 200) · StructuredLogger.php:72, FileHandler.php:220, PageRouterKernel.php:87 satır içeriği diskten Read ile okundu · alt-dal dosya adları görev beyazlistesiyle sabitlendi (log/metrik/trace × anayasa/surucu/ornekler)"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---

# isletim-sistemi-observability — Kapsam

> **Tek cümle:** Bu subdomain, işletim sistemi ve çekirdek düzeyindeki gözlemlenebilirlik
> yüzeylerini (log kanalları, metrik sayaçları, trace olayları) tarif eder; uygulama/dağıtık
> gözlemlenebilirlik `K017-observability` alanıdır ve **kapsam dışıdır**.

## §A — Kapsam

### §A.1 Kapsam içi (bu subdomain neyi taşır)

| # | Kapsam içi konu | Ne anlama gelir | Bu dalın dosyası |
|---|---|---|---|
| A1 | OS sistem günlük mimarisi | journald · syslog (RFC 3164/5424) · Windows Event Log · ETW kanalları | `log/log-anayasa.md` |
| A2 | Log kaynak türleri | kernel (printk/dmesg) · auth (audit) · cihaz (sürücü) kayıtları | `log/log-anayasa.md` |
| A3 | Severity/etiket standardı | syslog severity 0–7, ETW kanal seviyeleri, etiket adlandırma | `log/log-surucu.md` |
| A4 | Halka tamponu & taşma | kernel ring buffer, printk kuyruğu, journald taşma politikası | `log/log-surucu.md` |
| A5 | Kural dosyaları/rotasyon | logrotate, journald.conf, rsyslog filter, Event Log retention | `log/log-surucu.md` |
| A6 | Log ölçümü | log hacmi (satır/sn), disk I/O yükü, drop/taşma sayacı | `log/log-ornekler.md` |
| A7 | perf/PMU sayaçları | perf_event_open, hardware/software event, cpu-clock, task-clock | `metrik/metrik-anayasa.md` |
| A8 | /proc ve /sys metrikleri | vmstat/iostat/sar/procps yüzeyleri, /proc sayaç okuma | `metrik/metrik-surucu.md` |
| A9 | Sayaç vs gauge vs histogram | OS metrik tipleri, monotonik sayaç kuralı, örnekleme periyodu | `metrik/metrik-anayasa.md` |
| A10 | Eşik ve alert | softlockup, hung task, kernel panic, iowait eşiği | `metrik/metrik-surucu.md` |
| A11 | ftrace/kprobes/tracepoint | tracefs, statik olay noktaları, dinamik probe | `trace/trace-anayasa.md` |
| A12 | eBPF/BPF | bpf syscall, harita, ring buffer, bpftool | `trace/trace-anayasa.md` |
| A13 | syscall/kernel latency | ölçüm yüzeyi, gecikme histogramı, osnoise tracer | `trace/trace-surucu.md` |
| A14 | Call stack/dwarf sembolizasyon | stack çözümleme, sembol dosyaları, perf script | `trace/trace-surucu.md` |
| A15 | Overhead kontrolü | trace maliyeti, örnekleme oranı, buffer taşması | `trace/trace-surucu.md` |
| A16 | Ölçüm ve doğrulama | iddia-URL-tarih matrisi, disk kanıtı, eşik tabloları | kök `isletim-sistemi-observability-dogrulama.md` |
| A17 | Örnek yapılandırmalar | gerçek repo kodu, iyi/kötü uygulama karşılaştırması | kök `isletim-sistemi-observability-ornekler.md` |

### §A.2 Kapsam dışı (nereye gider)

| # | Kapsam dışı konu | Nereye gider | Gerekçe |
|---|---|---|---|
| B1 | Uygulama/dağıtık gözlemlenebilirlik (OpenTelemetry, Prometheus, Grafana, alert) | `K017-observability` | K017 uygulama ve dağıtık katmanın sahibidir; çakışma önlemi |
| B2 | Uygulama log şablonu (business event adlandırma, PSR-3 seviye seçimi) | uygulama katmanı (`K011-application` / `K012-services`) | Bu dal yalnız OS kanalını tarif eder |
| B3 | Log metriği (log üzerinden uyarı üretimi) | `K017-observability` | Dağıtık/uygulama tarafı işi |
| B4 | APM, distributed tracing backend, span depolama | `K017-observability` | O3 [UNKNOWN] çözülmeden bu dalda tasarımlanmaz |
| B5 | Ayrıntılı hata yönetimi deseni (ErrorHandler, ErrorResponse) | `isletim-sistemi-core` | İş mantığı hata akışı, gözlemlenebilirlik değil |
| B6 | PHP SAPI log yönlendirme kurulumu (Apache/nginx error_log) | `isletim-sistemi-runtime` | Sunucu yapılandırması bu dalın konusu değil |
| B7 | Güvenlik logu politikası, audit süresi, SIEM entegrasyonu | `isletim-sistemi-security` | Politika sahibi güvenlik alanıdır; burada yalnız kanal adı geçer |
| B8 | Veritabanı sorgu logu / slow query log | `K007-data` | DBMS içi log OS logu değildir |
| B9 | CI/CD log akışı ve arşivleme | `K018-devops` | Boru hattı logu üretim OS logundan ayrıdır |
| B10 | Çekirdek kaynağındaki printk/ftrace iç implementasyonu | `isletim-sistemi-kernel` | Bu dal yalnız kullanıcı arayüzünü (sysfs/tracefs/araç) tarif eder |
| B11 | Kernel event üretiminin kendisi (interrupt, scheduler olayı) | `isletim-sistemi-plan` | Olay üretimi planlama dalının; yakalama/okuma bu dalın |
| B12 | /proc ve /sys dosya sistemlerinin üretilmesi | `isletim-sistemi-core` | procfs/sysfs çekirdekte üretilir; okuma yöntemi bu dalda |

### §A.3 Kapsam kararının dayanağı

| # | Karar | Dayanak | Etiket |
|---|---|---|---|
| C1 | Repo'da OS gözlemlenebilirlik aracı kullanımı yok | grep `ftrace\|perf_event\|EventWrite` → 0 isabet | [KANIT] |
| C2 | Repo'da uygulama logu var | `shared/src/PageRouter/StructuredLogger.php:72` | [KANIT] |
| C3 | OS katmanı "eksik" değil "tanımsız" olarak raporlanır | O1–O3 açık soru durumu | [BİLİNMEYEN] |
| C4 | Hedef mimari taslağı `draft` statüsünde kalır | kanıtsız iddia `draft` | [TASARIM] |
| C5 | K017 ile sınır dosyada açıkça yazılır | paralel ajan çakışma kuralı | [TASARIM] |
| C6 | ftrace/perf/ETW yalnız "aday" olarak listelenir | hiçbiri repo'da kullanılmıyor | [TASARIM] |
| C7 | Ölçüm değerleri (eşik, periyot) bu dalda sabitlenmez | O2 çözülmeden eşik uydurulmaz | [BİLİNMEYEN] |
| C8 | Uygulama logu örneği disk kanıtıyla verilir | satır numarası Read ile doğrulanır | [KANIT] |
| C9 | Alt-dal dosya adları `anayasa/surucu/ornekler` üçlüsüdür | görev beyazlistesi (2026-10-13) — adlar değiştirilemez | [TASARIM] |

### §A.4 Bu subdomainin dosya envanteri (disk, 2026-10-13)

| # | Dosya | Satır (öncesi) | Satır (bu tur) | group |
|---|---|---|---|---|
| 1 | `index.md` | 79 | 40–100 | mimari |
| 2 | `isletim-sistemi-observability-kapsam.md` | 261 | ≥500 | mimari |
| 3 | `isletim-sistemi-observability-dogrulama.md` | 31 | ≥500 | kanit |
| 4 | `isletim-sistemi-observability-ornekler.md` | 22 | ≥500 | kod |
| 5 | `log/index.md` | — | 40–100 | mimari |
| 6 | `log/log-anayasa.md` | — | ≥500 | mimari |
| 7 | `log/log-surucu.md` | — | ≥500 | mimari |
| 8 | `log/log-ornekler.md` | — | ≥500 | kod |
| 9 | `metrik/index.md` | — | 40–100 | mimari |
| 10 | `metrik/metrik-anayasa.md` | — | ≥500 | mimari |
| 11 | `metrik/metrik-surucu.md` | — | ≥500 | mimari |
| 12 | `metrik/metrik-ornekler.md` | — | ≥500 | kod |
| 13 | `trace/index.md` | — | 40–100 | mimari |
| 14 | `trace/trace-anayasa.md` | — | ≥500 | mimari |
| 15 | `trace/trace-surucu.md` | — | ≥500 | mimari |
| 16 | `trace/trace-ornekler.md` | — | ≥500 | kod |

> Eski alt-dal dosya adları (`kernel-ve-sistem-logu.md`, `yapısal-log-ve-baglam.md`,
> `kernel-metrik-yuzeyleri.md`, `sistem-kaynak-olcumleri.md`, `ftrace-ve-kernel-eventleri.md`,
> `etw-ve-olay-izleme.md`) bu turda **yeniden adlandırıldı** — diskte yoklardı, adlar
> görev beyazlistesindeki `anayasa/surucu/ornekler` üçlüsüne sabitlendi.

### §A.5 OS log kanalları detay matrisi (kapsam içi A1–A6)

| # | Kanal | Platform | Biçim | Kim okur | Bu dalın dosyası |
|---|---|---|---|---|---|
| D1 | printk → ring buffer → dmesg | Linux | serbest metin | dmesg/klogctl | `log/log-anayasa.md` |
| D2 | syslog socket (/dev/log) | Linux/UNIX | RFC 3164/5424 | rsyslog/journald | `log/log-anayasa.md` |
| D3 | journald native | Linux (systemd) | yapısal binary | journalctl | `log/log-anayasa.md` |
| D4 | Windows Event Log | Windows | EVTX biner | wevtutil/Event Viewer | `log/log-anayasa.md` |
| D5 | ETW olay kanalı | Windows | binary etkinlik | logman/WPR | `log/log-anayasa.md` |
| D6 | unified logging | macOS/iOS | binary | log show | `log/log-anayasa.md` |
| D7 | audit subsystem | Linux | audit record | auditd | `log/log-ornekler.md` |
| D8 | auth log (secure/audit) | Linux/Windows | metin/EVTX | SIEM | `log/log-ornekler.md` |

## §B — 3 alt-dal envanteri (log/metrik/trace — gözlemlenebilirlik üçlüsü)

### §B.1 Üçlü genel görünüm

| # | Alt-dal | Gözlemlenebilirlik sorusu | Birincil OS yüzeyi | Birincil araç |
|---|---|---|---|---|
| 1 | `log/` | "Ne oldu?" | syslog/journald/Event Log, printk | journalctl, dmesg, wevtutil |
| 2 | `metrik/` | "Kaynak nasıl kullanılıyor?" | /proc, /sys, perf_event_open, performans defteri | perf, procps, PDH |
| 3 | `trace/` | "Hangi sıra/koşulda oldu?" | tracefs, ETW provider/session, BPF | ftrace, logman/WPR, bpftool |

### §B.2 `log/` alt-dalı dosya envanteri

| # | Dosya | İçerik yüzeyi | Ana kaynak |
|---|---|---|---|
| 1 | `log/index.md` | alt-dal künyesi, dosya kataloğu, komşu sınır matrisi | — |
| 2 | `log/log-anayasa.md` | günlük mimarisi (journald · syslog · ETW), kaynak türleri | syslog(3), RFC 3164, Windows Event Log |
| 3 | `log/log-surucu.md` | severity/etiket, halka tamponu & taşma, kural dosyaları/rotasyon | setlogmask(3), logrotate(8), journald.conf(5) |
| 4 | `log/log-ornekler.md` | log ölçümü, repo disk kanıtları, riskler, kapsam dışı | `StructuredLogger.php`, `FileHandler.php`, `Redactor.php` |

### §B.3 `metrik/` alt-dalı dosya envanteri

| # | Dosya | İçerik yüzeyi | Ana kaynak |
|---|---|---|---|
| 1 | `metrik/index.md` | alt-dal künyesi, dosya kataloğu, komşu sınır matrisi | — |
| 2 | `metrik/metrik-anayasa.md` | perf/PMU sayaçları, /proc-/sys yüzeyi, sayaç/gauge/histogram | perf(1), perf-stat(1), cgroup-v2 |
| 3 | `metrik/metrik-surucu.md` | örnekleme/periyot, eşik ve alert (softlockup/hung task/panic) | proc(5), iostat(1), sar(1) |
| 4 | `metrik/metrik-ornekler.md` | ölçüm, repo disk kanıtları, referans | `MiddlewarePipeline.php`, `AIWorkflow.php`, procps araçları |

### §B.4 `trace/` alt-dalı dosya envanteri

| # | Dosya | İçerik yüzeyi | Ana kaynak |
|---|---|---|---|
| 1 | `trace/index.md` | alt-dal künyesi, dosya kataloğu, komşu sınır matrisi | — |
| 2 | `trace/trace-anayasa.md` | ftrace/tracepoint/kprobe mimarisi, eBPF/BPF yüzeyi | ftrace-design, tracepoints, kprobes, bpf(2) |
| 3 | `trace/trace-surucu.md` | syscall/kernel latency, dwarf sembolizasyon, overhead kontrolü | histogram, uprobetracer, osnoise-tracer |
| 4 | `trace/trace-ornekler.md` | ölçüm, repo disk kanıtları, referans | perf-record(1), perf-script(1), bpftool |

### §B.5 Alt-dallar arası bağımlılık

| # | Bağımlılık | Yön | Açıklama |
|---|---|---|---|
| 1 | log → metrik | ters | log hacmi (satır/sn) bir metriktir, metrik dosyası sahiptir |
| 2 | metrik → trace | ters | trace kaydı da örneklem/hız metriği üretir |
| 3 | trace → log | ileri | trace olayları gerektiğinde log satırına düşebilir |
| 4 | metrik → log | ileri | softlockup/hung task eşikleri bir log kaydı üretir (surucu §5) |
| 5 | hepsi → kök kapsam | yukarı | üç alt-dal da kök `isletim-sistemi-observability-kapsam.md` §C ayrım matrisine bağlıdır |
| 6 | kök → K017 | dışarı | uygulama/dağıtık tarafı bu dalın sorumluluğu dışındadır |

### §B.6 Alt-dal dosya adı → soru → araç özeti

| # | Dosya | Ana soru | Birincil araç |
|---|---|---|---|
| 1 | `log/log-anayasa.md` | günlük mimarisi hangi kanalları taşır? | journalctl, wevtutil |
| 2 | `log/log-surucu.md` | kayıt nasıl filtrelenir/taşır/rotasyon yapar? | logrotate, journald.conf |
| 3 | `log/log-ornekler.md` | log hacmi ve I/O yükü nasıl ölçülür? | du, iostat, df |
| 4 | `metrik/metrik-anayasa.md` | hangi sayaçlar PMU'dan okunur? | perf stat |
| 5 | `metrik/metrik-surucu.md` | sayaçlar hangi periyotla okunur/eşiklenir? | vmstat, iostat, sar |
| 6 | `metrik/metrik-ornekler.md` | OS metriği repo'da nasıl temsil edilir? | hrtime, microtime |
| 7 | `trace/trace-anayasa.md` | olaylar tracefs/BPF'de nasıl yakalanır? | ftrace, bpftool |
| 8 | `trace/trace-surucu.md` | latency ve stack nasıl çözümlenir? | perf script, dwarf |
| 9 | `trace/trace-ornekler.md` | trace oturumu nasıl ölçülür? | perf record, osnoise |

## §C — OS düzeyi vs uygulama düzeyi ayrım matrisi

### §C.1 Ana ayrım matrisi

| # | Soru | OS düzeyi (bu dal) | Uygulama düzeyi (bu dalın dışı) |
|---|---|---|---|
| 1 | Kim üretir? | çekirdek, init sistemi, sürücü, OS araçları | PHP işlemi, uygulama kodu |
| 2 | Kanal nerede? | journald, syslog socket, Windows Event Log, tracefs | dosya (`coremusic_php_*.log`), `error_log()` |
| 3 | Biçim | tarihsel metin / CDF / EVTX / ftrace çıktı satırı | JSON satır, PSR-3 metin satırı |
| 4 | Kimlik | PID, birim (unit), kanal adı | traceId, request id, modül adı |
| 5 | Örnekleme | olay/kare bazlı, sık sık buffer | her istekte (uygulama), örnekleme yok |
| 6 | Yetki | root/yönetici (çoğu kaynak) | uygulama kullanıcısı |
| 7 | Kalıcılık | sistem başlangıcından beri | uygulama yaşam döngüsü + log dosyası |
| 8 | Ayrım testi | "uygulama kapansa da kaynak var mı?" | "uygulama kapanınca kayıt biter mi?" |
| 9 | Örnek soru | "disk doluyor mu, iowait yükseldi mi?" | "hangi istek 500 döndü?" |
| 10 | K017 ilişkisi | bu dal taşır | K017 taşır |

### §C.2 Repodaki gerçek karşılık

| # | Katman | Repo durumu | Dosya:Satır | Etiket |
|---|---|---|---|---|
| 1 | Uygulama — JSON yapısal log | var | `shared/src/PageRouter/StructuredLogger.php:72` | [KANIT] |
| 2 | Uygulama — metin log, çoklu dosya | var | `shared/src/Log/FileHandler.php:220` | [KANIT] |
| 3 | Uygulama — istek kimliği (traceId) üretimi | var | `shared/src/PageRouter/PageRouterKernel.php:87` | [KANIT] |
| 4 | Uygulama — maskeleme (redaction) | var | `shared/src/Log/Redactor.php:65` | [KANIT] |
| 5 | Uygulama — süre ölçümü (hrtime) | var | `shared/src/Middleware/MiddlewarePipeline.php:42` | [KANIT] |
| 6 | OS — syslog/journald/Event Log kullanımı | yok | grep 0 isabet | [KANIT] (olumsuz) |
| 7 | OS — perf/ftrace/ETW kullanımı | yok | grep 0 isabet | [KANIT] (olumsuz) |
| 8 | OS — /proc veya /sys okuma | yok | grep 0 isabet | [KANIT] (olumsuz) |
| 9 | OS — metrik toplama ajanı | yok | repo'da ajan yapılandırması bulunmadı | [BİLİNMEYEN] |

### §C.3 Ayrım matrisinin sınırları

| # | Sınır durumu | Davranış | Etiket |
|---|---|---|---|
| 1 | Bir kayıt hem OS hem uygulama katmanına değiyorsa | OS kanalı bu dalda, uygulama işlenmesi K017'de anlatılır | [TASARIM] |
| 2 | uygulama logu dosyası PHP SAPI'nin error_log hedefine yazılıyorsa | kanal = sunucu/uygulama sınırı; bu dal yalnız sınırı tarif eder | [TASARIM] |
| 3 | traceId ile log satırı birleştirilebiliyorsa | bu dal yalnız OS trace kanalını, birleştirmeyi `log/log-ornekler.md` anlatır | [TASARIM] |
| 4 | metrik OS kaynağından okunup uygulama içinde üretiliyorsa | kaynağı bu dal, dışa aktarımı K017 sahiptir | [TASARIM] |
| 5 | Ölçüm değeri doğrulanamıyorsa | `⚠️ VERIFICATION REQUIRED` yazılır, sayı uydurulmaz | [BİLİNMEYEN] |
| 6 | macOS/iOS unified logging geçiyorsa | kaynak bu dalda tarif edilir; Apple API kullanımı `isletim-sistemi-turu` sınırındadır | [TASARIM] |

### §C.4 Korelasyon kimliği matrisi (OS kimliği ↔ uygulama kimliği)

| # | Kimlik | Üreten | Taşıyan | Bu dal mı? |
|---|---|---|---|---|
| 1 | PID / TID | çekirdek | tüm OS kanalları | evet |
| 2 | birim (unit) adı | systemd | journald | evet |
| 3 | ETW provider GUID | provider kaydı | ETW oturumu | evet |
| 4 | traceId | `PageRouterKernel.php:87` | `FileHandler.php:220` satırı | hayır (uygulama) |
| 5 | span kimliği | K017/OTel | K017 | hayır (K017) |

## §D — Bağımlılık grafiği (kernel → uygulama → K017)

### §D.1 Katman sırası

| # | Katman | Bu katmanın ürettiği | Kim okur | K000 içindeki sahibi |
|---|---|---|---|---|
| 1 | Çekirdek (kernel) | printk, tracepoint, sayaç, olay | ftrace, perf, OS araçları | `isletim-sistemi-kernel` |
| 2 | OS servis katmanı | journald, syslog, Event Log, sayaç toplayıcı | sistem yöneticisi, toplama ajanı | **bu subdomain** |
| 3 | Çalışma zamanı (runtime) | PHP SAPI error_log hedefi, süreç çıktıları | dosya okuyucu | `isletim-sistemi-runtime` |
| 4 | Uygulama | JSON log satırı, istek kimliği, redaction | log dosyası okuyucusu | `isletim-sistemi-core` / `K011-application` |
| 5 | Dağıtık/uygulama gözlemlenebilirlik | span, metrik dışa aktarımı, alert | K017 | `K017-observability` |

### §D.2 Bağımlılık okları (bu dalın `depends_on` frontmatter'i)

| # | Bağımlılık | Yön | Neden |
|---|---|---|---|
| 1 | `isletim-sistemi-kernel` | bu dal → kernel | tracepoint, sayaç, printk çekirdekte tanımlıdır |
| 2 | `isletim-sistemi-core` | bu dal → core | çekirdek kavramları (süreç, dosya, bellek) ölçümün konusudur |
| 3 | `isletim-sistemi-runtime` | bu dal → runtime | uygulama logunun OS kanalına çıktığı sınır runtime'dır |
| 4 | `K017-observability` | bu dal → K017 (yalnız sınır) | uygulama/dağıtık yüzey bu dalın dışıdır |
| 5 | `K002-altyapi` | bu dal → altyapı (O2) | toplama ajanı yerleşimi altyapı kararıdır [UNKNOWN] |

### §D.3 Veri akışı (olay → kayıt)

| # | Adım | Katman | Çıktı |
|---|---|---|---|
| 1 | olay oluşur (syscall, sürücü, uygulama) | kernel/uygulama | ham olay |
| 2 | çekirdek yazar (tracepoint/printk/counter) | kernel | buffer içinde kayıt |
| 3 | OS toplar (journald / Event Log / tracefs okuma) | OS servis | kalıcı kanal kaydı |
| 4 | uygulama yazar (JSON satır + traceId) | uygulama | `coremusic_php_*.log` satırı |
| 5 | toplama ajanı okur (O2 [UNKNOWN]) | altyapı | merkezi depo |
| 6 | K017 üretir (metrik, span, alert) | K017 | uyarı/panel |

### §D.4 Redaction/PII sınırı grafiği

| # | Katman | PII sorumluluğu | Örnek |
|---|---|---|---|
| 1 | kernel/OS logu | OS kanalı PII içermez, sistem olayıdır | `/dev/sda: I/O error` |
| 2 | uygulama logu | zorunlu maskeleme | `password=[REDACTED]` |
| 3 | redaction noktası | tek nokta: `Redactor::redact()` | `shared/src/Log/Redactor.php:65` |
| 4 | dışa aktarım | K017'nin kuralı | K017'ye devredildi |

## §E — Sınır komşuları (kernel · core · plan · security · testing · api · turu · K017)

### §E.1 Komşu matrisi

| # | Komşu | Ortak konu | Sınır kararı | Çakışma riski |
|---|---|---|---|---|
| 1 | `isletim-sistemi-kernel` | printk, tracepoint, sayaç çekirdeği, dmesg kaynağı | çekirdek iç implementasyon + kaynak → kernel; okuma/yorumlama → bu dal | orta |
| 2 | `isletim-sistemi-core` | süreç, bellek, dosya kavramları, /proc üretimi | kavram + procfs/sysfs üretimi → core; ölçüm yöntemi → bu dal | orta |
| 3 | `isletim-sistemi-plan` | kernel event üretimi (interrupt, scheduler) | olay üretimi → plan; event yakalama (tracepoint/kprobe) → bu dal | orta |
| 4 | `isletim-sistemi-security` | audit trail, güvenlik logu politikası | politika/süre/audit sahibi → security; kanal adı → bu dal | düşük |
| 5 | `isletim-sistemi-testing` | gözlem testi (log/metrik doğrulama testleri) | test stratejisi → testing; OS kaynağı tanımı → bu dal | düşük |
| 6 | `isletim-sistemi-api` | syslog uçları, socket yüzeyi | syslog soketi bir API uçudur → api; log kanalı modeli → bu dal | düşük |
| 7 | `isletim-sistemi-turu` | OS araç farkları (journalctl vs wevtutil vs log) | platform/tool fark matrisi → turu; OS gözlemlenebilirlik kavramı → bu dal | orta |
| 8 | `isletim-sistemi-runtime` | PHP SAPI log hedefi | yapılandırma → runtime; kanal modeli → bu dal | düşük |
| 9 | `K017-observability` | log/metrik/trace üçlüsü | OS düzeyi → bu dal; uygulama/dağıtık → K017 | **yüksek** |
| 10 | `K002-altyapi` | toplama ajanı, sunucu kurulumu | ajan yerleşimi → K002; kaynak listesi → bu dal | orta |

### §E.2 K017 ile ayrım (her iki dosyada da yazılı olacak)

| # | Boyut | Bu dal (OS/çekirdek) | `K017-observability` (uygulama/dağıtık) |
|---|---|---|---|
| 1 | Örnek araç | ftrace, perf, ETW, journald, wevtutil | OTel collector, Prometheus scrape, alert rule |
| 2 | Örnek metrik | `/proc/stat` CPU jiffy, cgroup `cpu.stat` | iş metriği (`http_requests_total`) |
| 3 | Örnek kayıt | kern.log satırı, Event Log kaydı | uygulama JSON log satırı |
| 4 | Örnek trace | ftrace kare, ETW etkinliği | OpenTelemetry span |
| 5 | Sahip dosya | bu dalın 12 içerik dosyası | K017'nin kendi dosyaları |
| 6 | Birleştirme | yalnız `traceId` benzeri kimlik kavramı ortak | kimlik üretimi uygulamada |

### §E.3 Komşularla bilgi alışverişi

| # | Yön | Ne taşınır | Format |
|---|---|---|---|
| 1 | kernel → bu dal | tracepoint adları, sayaç tanımları, dmesg | tracefs/sysfs dosya listesi |
| 2 | core → bu dal | süreç/dosya/bellek kavramları, /proc düzeni | kavram sözlüğü |
| 3 | plan → bu dal | kernel event adları (sched, irq) | event kataloğu |
| 4 | bu dal → security | OS log kanalı adları (audit, secure) | kanal listesi |
| 5 | bu dal → testing | gözlemlenebilirlik testi gereksinimleri | test matrisi |
| 6 | bu dal → api | syslog uç/socket modeli | soket tarifi |
| 7 | bu dal → turu | OS araç karşılaştırma girdisi | araç matrisi |
| 8 | bu dal → K017 | OS kaynağı listesi ve eşik teklifleri | §E.2 matrisi (O2 ile birlikte) |
| 9 | K002 → bu dal | ajan yerleşim kararı | [UNKNOWN] — O2 açık |

### §E.4 Alt-dal bazlı komşu sınır matrisi — `log/`

| # | Konu | Bu alt-dal | Komşu | Giden alan |
|---|---|---|---|---|
| 1 | uygulama log satırı | sınırı tarif eder | `K017-observability` | içerik/biçim K017 |
| 2 | dmesg kaynağı | okuma | `isletim-sistemi-kernel` | üretilmesi kernel |
| 3 | audit trail güvenliği | kanal adı | `isletim-sistemi-security` | politika security |
| 4 | syslog uçları | model | `isletim-sistemi-api` | socket/API yüzeyi api |
| 5 | OS araç farkları | veri | `isletim-sistemi-turu` | karşılaştırma turu |

### §E.5 Alt-dal bazlı komşu sınır matrisi — `metrik/`

| # | Konu | Bu alt-dal | Komşu | Giden alan |
|---|---|---|---|---|
| 1 | /proc üretimi | okuma yöntemi | `isletim-sistemi-core` | dosya sistemi core |
| 2 | PMU/ sayaç çekirdeği | arayüz | `isletim-sistemi-kernel` | sayaç üretilmesi kernel |
| 3 | kernel event sayacı | etiket | `isletim-sistemi-plan` | olay üretimi plan |
| 4 | eşik alert çıkışı | OS eşiği | `K017-observability` | iş alarmı K017 |
| 5 | metrik doğrulama testi | gereksinim | `isletim-sistemi-testing` | test testing |

### §E.6 Alt-dal bazlı komşu sınır matrisi — `trace/`

| # | Konu | Bu alt-dal | Komşu | Giden alan |
|---|---|---|---|---|
| 1 | tracepoint tanımı | yakalama | `isletim-sistemi-kernel` | tanım kernel |
| 2 | syscall yüzeyi | ölçüm | `isletim-sistemi-api` | syscall semantiği api |
| 3 | ETW provider kaydı | oturum | `isletim-sistemi-turu` | platform farkı turu |
| 4 | audit olayı izleme | kanal | `isletim-sistemi-security` | politika security |
| 5 | OTel span | sınır | `K017-observability` | dağıtık span K017 |

### §E.7 Dokunulmaz yüzeyler (bu tur için)

| # | Yüzey | Durum |
|---|---|---|
| 1 | `K017-observability/**` | **DOKUNULMAZ** — yalnız referans |
| 2 | `K000-isletim-sistemi/index.md`, `os-*.md` | DOKUNULMAZ |
| 3 | `isletim-sistemi-{core,cross,kernel,plan,runtime,security,testing}/**` | DOKUNULMAZ |
| 4 | `isletim-sistemi-{audio,api,turu}/**`, `electroncis/**` | DOKUNULMAZ |
| 5 | `*.php`, `*.js`, `*.css`, `*.sql` | DOKUNULMAZ (yalnız okundu) |
| 6 | `K001..K019/**` (K017 hariç) | DOKUNULMAZ |

## §F — Açık sorular (O1 · O2 · O3)

### §F.1 Açık soru tablosu

| # | Soru | Neden açık | Etkilediği dosya | Durum |
|---|---|---|---|---|
| O1 | OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? | hedef mimari taslağında OS toplama kararı yok; repo'da 0 kullanım | `log/*`, `metrik/*` | [UNKNOWN] |
| O2 | Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? | `K002-altyapi` ajan yerleşimi ve sahipliği tanımsız | tüm alt-dallar | [UNKNOWN] |
| O3 | Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? | repoda yalnız uygulama içi traceId var; OTel kullanımı 0 isabet | `trace/*` | [UNKNOWN] |

### §F.2 O1 — OS düzeyi toplama

| # | Alt soru | Ölçüm | Durum |
|---|---|---|---|
| 1 | Linux tarafında journald/syslog toplayıcısı kurulacak mı? | repo'da journald/syslog yapılandırması yok | [UNKNOWN] |
| 2 | Windows tarafında Event Log/ETW toplayıcısı olacak mı? | repo'da ETW/Event Log kullanımı yok | [UNKNOWN] |
| 3 | perf_event_open ile metrik okunacak mı? | repo'da `perf_event_open` çağrısı yok | [UNKNOWN] |
| 4 | Toplayıcı ajan hangi kullanıcı/yetkiyle çalışacak? | yetki modeli tanımsız | [UNKNOWN] |
| 5 | Toplama periyodu ve örnekleme oranı ne olacak? | ölçüm yok, sayı uydurulmadı | [UNKNOWN] |

### §F.3 O2 — K002-altyapi içindeki konum

| # | Alt soru | Ölçüm | Durum |
|---|---|---|---|
| 1 | log/metrik/trace üçlüsü tek ajan mı, üç ayrı ajan mı? | karar yok | [UNKNOWN] |
| 2 | Kaynak listesi (hangi log dosyası, hangi sayaç) kim onaylar? | onay kaydı yok | [UNKNOWN] |
| 3 | Saklama süresi ve arşiv politikası kimde? | politika security/devops sınırında | [UNKNOWN] |
| 4 | Eşik/alarm değerleri hangi ölçümle belirlenecek? | ölçüm periyodu tanımsız | [UNKNOWN] |
| 5 | Üçlü K002'nin içindeyse bu dalın dosyaları ne rolde? | rol ayrımı (tarif mi, sahiplik mi) çözülmedi | [UNKNOWN] |

### §F.4 O3 — Dağıtık trace vs uygulama içi correlation

| # | Alt soru | Ölçüm | Durum |
|---|---|---|---|
| 1 | Repodaki traceId üretimi dağıtık trace'in başlangıcı mı? | `PageRouterKernel.php:87` yalnız istek kimliği üretir | [UNKNOWN] |
| 2 | OTel/vb. bir kütüphane bağımlılık listesinde mi? | bağımlılık taramasında OTel paketi bulunmadı | [UNKNOWN] |
| 3 | span kavramı OS trace ile mi, uygulama içi mi kurulacak? | tasarım kararı yok | [UNKNOWN] |
| 4 | trace verisi hangi kanala yazılacak? | kanal seçimi O2'ye bağlı | [UNKNOWN] |
| 5 | OS trace'i (ftrace/ETW) kapatılırsa O3 etkilenir mi? | ikisi bağımsız; ayrım `§C.1` satır 10 | [UNKNOWN] |

### §F.5 Açık soru kapanış kriterleri

| # | Soru | Kapanış için gereken kanıt | Sahip |
|---|---|---|---|
| 1 | O1 | hedef mimari kararı ya da repo'da OS toplama yapılandırması | Vault Steward + devops |
| 2 | O2 | `K002-altyapi` dosyasında toplama ajanı kararı | Vault Steward |
| 3 | O3 | ADR ya da bağımlılık listesinde trace kütüphanesi | Vault Steward + architect |

### §F.6 Açık soru etkileşim matrisi

| # | Etkileşim | Yön | Sonuç |
|---|---|---|---|
| 1 | O1 çözülürse O2 | O1 → O2 | kaynak listesi netleşir, ajan kararı kolaylaşır |
| 2 | O2 çözülürse O3 | O2 → O3 | kanal seçimi belli olur |
| 3 | O3 çözülürse O1 | O3 → O1 | OTel varsa OS toplama kapsamı daralır |
| 4 | hepsi çözülmezse | — | bu dal `draft` kalır, eşik sayıları yazılmaz |

## §G — Web araştırma (kısa matris)

| # | İddia | URL | Erişim (2026-10-13) | Durum |
|---|---|---|---|---|
| K-W1 | Syslog biçimi RFC 3164 ile tanımlanmıştır (BSD syslog) | https://www.rfc-editor.org/rfc/rfc3164 | 2026-10-13 | HTTP 200 |
| K-W2 | Windows Event Log resmi Win32 platformudur | https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log | 2026-10-13 | HTTP 200 |
| K-W3 | cgroup v2 çekirdek yönetim belgesi resmi kaynaktır | https://docs.kernel.org/admin-guide/cgroup-v2.html | 2026-10-13 | HTTP 200 |

> Tam matris (42 URL) kök `isletim-sistemi-observability-dogrulama.md` §1'dedir.

## §H — Terim sözlüğü (bu subdomaine özgü)

| # | Terim | Tanım |
|---|---|---|
| 1 | journald | systemd'nin yapısal sistem günlük servisi |
| 2 | syslog | UNIX sistem günlük protokolü/ailesi (RFC 3164/5424) |
| 3 | ETW | Windows Event Tracing for Windows |
| 4 | Event Log | Windows kalıcı olay deposu (EVTX) |
| 5 | printk | çekirdeğin serbest metin günlük çıktısı |
| 6 | ring buffer | dairesel bellek tamponu; taşma eski kaydı siler |
| 7 | severity | kayıt önem seviyesi (syslog 0–7) |
| 8 | rotasyon | log dosyasının dönemsel arşivlenmesi |
| 9 | perf_event_open | Linux performans olayı arayüzü (syscall) |
| 10 | PMU | Performans İzleme Birimi (donanım sayaçları) |
| 11 | sayaç (counter) | monoton artan metrik |
| 12 | gauge | anlık değer metriği (artış/azalış) |
| 13 | histogram | dağılım metriği (gecikme aralıkları) |
| 14 | iowait | CPU'nun disk G/Ç beklemesinde geçirdiği zaman |
| 15 | softlockup | çekirdeğin uzun süre kesintisiz çalışması uyarısı |
| 16 | tracepoint | çekirdeğe yerleştirilmiş statik olay noktası |
| 17 | kprobe | çalışma anında eklenen dinamik çekirdek probu |
| 18 | eBPF | doğrulanan bytecode ile çekirdek içi program yürütme |
| 19 | tracefs | ftrace çıktılarının bağlandığı dosya sistemi |
| 20 | sembolizasyon | ham adresin fonksiyon adına çevrilmesi |

## §I — Risk özeti

| # | Risk | Etki | Azaltma | Etiket |
|---|---|---|---|---|
| 1 | OS toplama kararı olmadan eşik yazmak | uydurma sayı | eşikler O2'ye kadar `UNKNOWN` | [BİLİNMEYEN] |
| 2 | K017 ile sınır karışması | çift sahiplik | §E.2 matrisi her iki tarafta | [TASARIM] |
| 3 | kanıt satır uydurma | vault güvenilirliği | Read ile satır doğrulama | [KANIT] |
| 4 | envanter dışı URL kullanımı | tekrar/çakışma | 2097 URL envanteri elendi | [KANIT] |
| 5 | ring buffer taşmasıyla kanıt kaybı | olay silinmesi | rotasyon/retention surucu §5'te | [TASARIM] |
| 6 | trace overhead'in canlı sistemde ölçülmemesi | gecikme patlaması | örnekleme surucu §4–5 | [TASARIM] |
| 7 | audit trail'in politikasız kalması | güvenlik boşluğu | security dalına devredildi | [TASARIM] |

## §J — Platform yüzeyi karşılaştırma matrisi (bu dalın kapsamındaki OS'ler)

| # | Yüzey | Linux | Windows | macOS/iOS |
|---|---|---|---|---|
| 1 | Sistem günlüğü | journald / syslog | Event Log (EVTX) | unified logging |
| 2 | Çekirdek günlüğü | dmesg / printk | Kernel-PW-Logger / ETW | `log show --predicate` |
| 3 | Metrik kaynağı | /proc, /sys, perf | PDH, performans defteri | `host_statistics` |
| 4 | Trace | ftrace, kprobes, eBPF | ETW provider/session | DTrace / ktrace |
| 5 | Rotasyon | logrotate | Event Log retention | unified log süresi |
| 6 | Yetki modeli | CAP_SYS_ADMIN / root | Yönetici | root / entitlement |

## §K — Kapsam içi karar kaydı (bu tur)

| # | Karar | Gerekçe | Tarih | Etiket |
|---|---|---|---|---|
| K1 | Alt-dal dosya adları `anayasa/surucu/ornekler` üçlüsüne sabitlendi | görev beyazlistesi (değiştirilemez) | 2026-10-13 | [TASARIM] |
| K2 | Eski alt-dal dosya adları (`kernel-ve-sistem-logu.md` vb.) kaldırıldı | diskte yoktu; çakışma önlenmesi | 2026-10-13 | [TASARIM] |
| K3 | `parent:` alanı yerine `parent_id:` kullanıldı | v2 §4.2 8 alan + `group` şeması | 2026-10-13 | [TASARIM] |
| K4 | Root `index.md` 40–100 satır bandına alındı | görev eşik kuralı | 2026-10-13 | [TASARIM] |
| K5 | 3 içerik dosyası ≥500 satıra çıkarıldı | görev eşik kuralı (öncesi 261/31/22) | 2026-10-13 | [TASARIM] |

## §L — Kapsam matrisi özeti (satır bazlı hızlı bakış)

| # | Matris | Satır sayısı | § |
|---|---|---|---|
| 1 | Kapsam içi konu | 17 | §A.1 |
| 2 | Kapsam dışı | 12 | §A.2 |
| 3 | Karar dayanağı | 9 | §A.3 |
| 4 | Dosya envanteri | 16 | §A.4 |
| 5 | OS log kanalları | 8 | §A.5 |
| 6 | Üçlü genel görünüm | 3 | §B.1 |
| 7 | log/ metrik/ trace/ envanter | 4+4+4 | §B.2–§B.4 |
| 8 | Alt-dal bağımlılık | 6 | §B.5 |
| 9 | OS↔uygulama ayrım | 10 | §C.1 |
| 10 | Repodaki karşılık | 9 | §C.2 |
| 11 | Ayrım sınırları | 6 | §C.3 |
| 12 | Komşu matrisi | 10 | §E.1 |
| 13 | K017 ayrım | 6 | §E.2 |
| 14 | Açık sorular | 3+5+5+5 | §F.1–§F.4 |
| 15 | Web kanıtı | 3 (özet; 42 tam) | §G |
| 16 | Terim sözlüğü | 20 | §H |
| 17 | Risk özeti | 7 | §I |
| 18 | Platform yüzeyi | 6 | §J |
| 19 | Karar kaydı | 5 | §K |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
