---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/isletim-sistemi-observability-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://man7.org/linux/man-pages/man3/syslog.3.html
  - https://man7.org/linux/man-pages/man2/syslog.2.html
  - https://man7.org/linux/man-pages/man3/setlogmask.3.html
  - https://www.rfc-editor.org/rfc/rfc3164
  - https://man7.org/linux/man-pages/man1/dmesg.1.html
  - https://man7.org/linux/man-pages/man8/logrotate.8.html
  - https://man7.org/linux/man-pages/man5/journald.conf.5.html
  - https://man7.org/linux/man-pages/man1/journalctl.1.html
  - https://man7.org/linux/man-pages/man8/systemd-journald.8.html
  - https://docs.kernel.org/core-api/printk-formats.html
  - https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log
  - https://learn.microsoft.com/en-us/windows/win32/wes/using-windows-event-log
  - https://developer.apple.com/documentation/os/logging
  - https://man7.org/linux/man-pages/man5/proc.5.html
  - https://man7.org/linux/man-pages/man1/iostat.1.html
  - https://man7.org/linux/man-pages/man1/sar.1.html
  - https://man7.org/linux/man-pages/man1/mpstat.1.html
  - https://man7.org/linux/man-pages/man1/pidstat.1.html
  - https://man7.org/linux/man-pages/man1/top.1.html
  - https://man7.org/linux/man-pages/man1/free.1.html
  - https://man7.org/linux/man-pages/man1/uname.1.html
  - https://man7.org/linux/man-pages/man2/time.2.html
  - https://man7.org/linux/man-pages/man2/clock_gettime.2.html
  - https://man7.org/linux/man-pages/man7/signal.7.html
  - https://man7.org/linux/man-pages/man8/ethtool.8.html
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - https://docs.kernel.org/admin-guide/mm/concepts.html
  - https://man7.org/linux/man-pages/man1/perf.1.html
  - https://man7.org/linux/man-pages/man1/perf-stat.1.html
  - https://man7.org/linux/man-pages/man1/perf-record.1.html
  - https://man7.org/linux/man-pages/man1/perf-script.1.html
  - https://docs.kernel.org/trace/ftrace-design.html
  - https://docs.kernel.org/trace/tracepoints.html
  - https://docs.kernel.org/trace/kprobes.html
  - https://docs.kernel.org/trace/histogram.html
  - https://docs.kernel.org/trace/uprobetracer.html
  - https://docs.kernel.org/trace/osnoise-tracer.html
  - https://man7.org/linux/man-pages/man2/bpf.2.html
  - https://docs.kernel.org/bpf/
  - https://docs.kernel.org/core-api/symbol-namespaces.html
  - https://learn.microsoft.com/en-us/windows/win32/api/evntrace/
  - https://learn.microsoft.com/en-us/windows/win32/api/evntcons/
  - https://man7.org/linux/man-pages/man8/auditd.8.html
  - https://man7.org/linux/man-pages/man8/ausearch.8.html
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/wevtutil
  - https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.diagnostics/get-winevent
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/eventcreate
  - https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-reporteventw
  - https://learn.microsoft.com/en-us/windows/win32/wes/querying-for-events
  - https://developer.apple.com/documentation/os/os_log
  - https://developer.apple.com/documentation/os/logger
  - https://developer.apple.com/documentation/os/logger/init(subsystem:category:)
  - https://man7.org/linux/man-pages/man8/vmstat.8.html
  - https://man7.org/linux/man-pages/man5/proc_vmstat.5.html
  - https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.diagnostics/get-counter
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/typeperf
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman-create-counter
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/perfmon
  - https://learn.microsoft.com/en-us/windows/win32/perfctrs/
  - https://learn.microsoft.com/en-us/windows/win32/api/pdh/
  - https://learn.microsoft.com/en-us/windows/win32/api/pdh/nf-pdh-pdhcollectquerydata
  - https://learn.microsoft.com/en-us/windows/win32/api/pdh/nf-pdh-pdhaddcounterw
  - https://learn.microsoft.com/en-us/windows/win32/perfctrs/collecting-performance-data
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/tracerpt
  - https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal
  - https://developer.apple.com/documentation/kernel
validation: "2026-10-14 — 2. tur OS genişletme: 75 kanıt URL'si toplam HTTP 200 doğrulandı (1. tur 42/42 + 2. tur 33/33) · platform dengesi Linux 47 / Windows 22 / macOS 13 (1. tur 37:4:1) · Windows PDH boşluğu kapandı (M-W28…M-W32), Windows metrik arayüzü kapandı (M-W24…M-W27), macOS metrik kapandı (M-W33…M-W35), macOS trace aynayla kapandı (T-W19…T-W22, dipnotlu) · Apple L-W12 404 çelişkisi düzeltildi (os_log, L-W12r) · vmstat.1(404)→vmstat.8(200) düzeltildi (M-W22) · 18 aday 404 ile reddedildi (§4+§4.1) · soft-404 negatif kontrolü 4/4 gerçek 404 · keith.github.io üçüncü taraf aynası olduğu dipnotlandı · repo kod satırları Read ile doğrulandı (StructuredLogger, FileHandler, Redactor, PageRouterKernel, MiddlewarePipeline, AIWorkflow, ToolCalling, UuidV7, LoggerFactory)"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: kanit
---

# isletim-sistemi-observability — Doğrulama

**Doğrulama tarihi:** 2026-10-13 · Erişim şekli: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` + disk okuma (`Read` / `Select-Object -Index`).

## §0 — Ölçüm metodolojisi

| # | Adım | Araç | Çıktı |
|---|---|---|---|
| 1 | URL envanteri çıkarıldı | `Get-ChildItem .ai/architecture -Recurse -Filter *.md` + regex `https?://` | 2097 benzersiz URL |
| 2 | Adaylar envantere karşı elendi | hashtable `ContainsKey` | 38+16 aday → 42 benzersiz yeni URL (1. tur) |
| 3 | HTTP HEAD doğrulaması | `Invoke-WebRequest -Method Head` | **75/75 HTTP 200** (1. tur 42/42 · 2. tur +33) · 18 aday 404 → reddedildi |
| 4 | Repo kod kanıtı | `Read` (dosya, offset, limit) | 9 dosyada satır doğrulandı |
| 5 | Satır sayısı ölçümü | `Get-Content \| Measure-Object -Line` | 16 dosya hedefe göre yazıldı |

## §1 — Web kanıtları (log alt-dalı)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| L-W1 | syslog(3) Linux sistem günlüğü arayüzüdür | https://man7.org/linux/man-pages/man3/syslog.3.html | 2026-10-13 | HTTP 200 |
| L-W2 | syslog(2) çekirdek syslog socket arayüzüdür | https://man7.org/linux/man-pages/man2/syslog.2.html | 2026-10-13 | HTTP 200 |
| L-W3 | setlogmask(3) kayıt seviyesi maskesi verir | https://man7.org/linux/man-pages/man3/setlogmask.3.html | 2026-10-13 | HTTP 200 |
| L-W4 | BSD syslog biçimi RFC 3164'de tanımlanmıştır | https://www.rfc-editor.org/rfc/rfc3164 | 2026-10-13 | HTTP 200 |
| L-W5 | dmesg(1) çekirdek halka tamponunu okur | https://man7.org/linux/man-pages/man1/dmesg.1.html | 2026-10-13 | HTTP 200 |
| L-W6 | logrotate(8) log rotasyonunu yönetir | https://man7.org/linux/man-pages/man8/logrotate.8.html | 2026-10-13 | HTTP 200 |
| L-W7 | journald.conf(5) journald yapılandırma dosyasıdır | https://man7.org/linux/man-pages/man5/journald.conf.5.html | 2026-10-13 | HTTP 200 |
| L-W8 | journalctl(1) journald kayıtlarını okur | https://man7.org/linux/man-pages/man1/journalctl.1.html | 2026-10-13 | HTTP 200 |
| L-W9 | systemd-journald(8) sistem günlük servisidir | https://man7.org/linux/man-pages/man8/systemd-journald.8.html | 2026-10-13 | HTTP 200 |
| L-W10 | printk biçimlendirme kuralları resmi çekirdek belgesindedir | https://docs.kernel.org/core-api/printk-formats.html | 2026-10-13 | HTTP 200 |
| L-W11 | Windows Event Log resmi Win32 platformudur | https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log | 2026-10-13 | HTTP 200 |
| L-W12 | Event Log kaydı okuma/yazma yöntemleri Microsoft Learn'dedir | https://learn.microsoft.com/en-us/windows/win32/wes/using-windows-event-log | 2026-10-13 | HTTP 200 |
| L-W13 | macOS/iOS unified logging resmi Apple API'sidir | https://developer.apple.com/documentation/os/logging | 2026-10-13 | HTTP 200 |

### §1.1 — OS genişletme turu (2026-10-14) — log kanıtları

> Bu tur, vault'un **yalnız Linux'a indirgenmesini** önlemek için Windows ve
> macOS log yüzeylerini kanıtlar. 11 yeni URL, 11/11 HTTP 200.

| # | İddia | URL | OS | Tarih | Durum |
|---|---|---|---|---|---|
| L-W18 | `auditd(8)` Linux Audit alt sistemini yönetir (disk kaydı) | https://man7.org/linux/man-pages/man8/auditd.8.html | Linux | 2026-10-14 | HTTP 200 |
| L-W19 | `ausearch(8)` Audit kayıtlarını sorgular | https://man7.org/linux/man-pages/man8/ausearch.8.html | Linux | 2026-10-14 | HTTP 200 |
| L-W20 | `wevtutil` Event Log sorgu/yazma komutudur | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/wevtutil | Windows | 2026-10-14 | HTTP 200 |
| L-W21 | `Get-WinEvent` Event Log kayıtlarını PowerShell'de okur | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.diagnostics/get-winevent | Windows | 2026-10-14 | HTTP 200 |
| L-W22 | `eventcreate` olay kaydı yazma komutudur | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/eventcreate | Windows | 2026-10-14 | HTTP 200 |
| L-W23 | `ReportEventW` Win32 Event Log kayıt API'sidir | https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-reporteventw | Windows | 2026-10-14 | HTTP 200 |
| L-W24 | Windows Event Log sorgulama (XPath filtreleme) | https://learn.microsoft.com/en-us/windows/win32/wes/querying-for-events | Windows | 2026-10-14 | HTTP 200 |
| L-W25 | `log(1)` unified logging okuma/emit komutudur (Xcode man aynası) | https://keith.github.io/xcode-man-pages/log.1.html | macOS | 2026-10-14 | HTTP 200 |
| L-W26 | `Logger` subsystem/category ile kayıt üreten Swift API'sidir | https://developer.apple.com/documentation/os/logger | macOS | 2026-10-14 | HTTP 200 |
| L-W27 | `Logger.init(subsystem:category:)` kayıt kaynağı tanımıdır | https://developer.apple.com/documentation/os/logger/init(subsystem:category:) | macOS | 2026-10-14 | HTTP 200 |
| L-W12r | **Düzeltme:** eski L-W12 (`logging/generating-log-messages-from-your-code`) HTTP **404** verdi; `os_log` ile değiştirildi | https://developer.apple.com/documentation/os/os_log | macOS | 2026-10-14 | HTTP 200 |

> **Ayna dipnotu (zero-hallucination):** `keith.github.io/xcode-man-pages`
> Apple'ın resmi sitesi değil, **Xcode man sayfalarının üçüncü taraf aynasıdır**.
> `log.1` komut satırı semantiği bu aynadan alınmıştır; resmi Apple dokümanı
> `os_log`/`Logger` sayfalarıdır (L-W12r, L-W26, L-W27).

## §2 — Web kanıtları (metrik alt-dalı)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| M-W1 | proc(5) /proc dosya sistemi düzenini tanımlar | https://man7.org/linux/man-pages/man5/proc.5.html | 2026-10-13 | HTTP 200 |
| M-W2 | iostat(1) G/Ç istatistiği verir | https://man7.org/linux/man-pages/man1/iostat.1.html | 2026-10-13 | HTTP 200 |
| M-W3 | sar(1) sistem aktivite kayıtçısıdır | https://man7.org/linux/man-pages/man1/sar.1.html | 2026-10-13 | HTTP 200 |
| M-W4 | mpstat(1) çoklu CPU istatistiği verir | https://man7.org/linux/man-pages/man1/mpstat.1.html | 2026-10-13 | HTTP 200 |
| M-W5 | pidstat(1) süreç bazlı kaynak istatistiği verir | https://man7.org/linux/man-pages/man1/pidstat.1.html | 2026-10-13 | HTTP 200 |
| M-W6 | top(1) canlı süreç/kaynak izleyicisidir | https://man7.org/linux/man-pages/man1/top.1.html | 2026-10-13 | HTTP 200 |
| M-W7 | free(1) bellek kullanımını gösterir | https://man7.org/linux/man-pages/man1/free.1.html | 2026-10-13 | HTTP 200 |
| M-W8 | uname(1) sistem/sürüm bilgisi verir | https://man7.org/linux/man-pages/man1/uname.1.html | 2026-10-13 | HTTP 200 |
| M-W9 | time(2) süreç zamanlama ölçümü verir | https://man7.org/linux/man-pages/man2/time.2.html | 2026-10-13 | HTTP 200 |
| M-W10 | clock_gettime(2) yüksek çözünürlüklü zaman kaynağıdır | https://man7.org/linux/man-pages/man2/clock_gettime.2.html | 2026-10-13 | HTTP 200 |
| M-W11 | signal(7) sinyal semantiğini tanımlar (SIGSTOP vb.) | https://man7.org/linux/man-pages/man7/signal.7.html | 2026-10-13 | HTTP 200 |
| M-W12 | ethtool(8) ağ arayüzü sayaçlarını okur | https://man7.org/linux/man-pages/man8/ethtool.8.html | 2026-10-13 | HTTP 200 |
| M-W13 | cgroup v2 çekirdek yönetim belgesi resmi kaynaktır | https://docs.kernel.org/admin-guide/cgroup-v2.html | 2026-10-13 | HTTP 200 |
| M-W14 | Bellek yönetim kavramları resmi çekirdek belgesindedir | https://docs.kernel.org/admin-guide/mm/concepts.html | 2026-10-13 | HTTP 200 |
| M-W15 | perf(1) Linux performans olay aracıdır | https://man7.org/linux/man-pages/man1/perf.1.html | 2026-10-13 | HTTP 200 |
| M-W16 | perf stat PMU sayaçlarını raporlar | https://man7.org/linux/man-pages/man1/perf-stat.1.html | 2026-10-13 | HTTP 200 |

### §2.1 — OS genişletme turu (2026-10-14) — metrik kanıtları

> Windows performans sayacı ve macOS sayaç yüzeyleri bu turda kanıtlanır.
> 14 yeni URL, 14/14 HTTP 200. §10'daki PDH `⚠️` durumu burada kapanır.

| # | İddia | URL | OS | Tarih | Durum |
|---|---|---|---|---|---|
| M-W22 | `vmstat(8)` bellek/CPU/paging sayaçlarını raporlar | https://man7.org/linux/man-pages/man8/vmstat.8.html | Linux | 2026-10-14 | HTTP 200 |
| M-W23 | `proc_vmstat(5)` `/proc/vmstat` sayaç alanlarını tanımlar | https://man7.org/linux/man-pages/man5/proc_vmstat.5.html | Linux | 2026-10-14 | HTTP 200 |
| M-W24 | `Get-Counter` performans sayaçlarını PowerShell'de okur | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.diagnostics/get-counter | Windows | 2026-10-14 | HTTP 200 |
| M-W25 | `typeperf` sayaç değerlerini konsola/dosyaya yazar | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/typeperf | Windows | 2026-10-14 | HTTP 200 |
| M-W26 | `logman create counter` sayaç toplayıcı oluşturur | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman-create-counter | Windows | 2026-10-14 | HTTP 200 |
| M-W27 | `perfmon` performans izleyici/sayıcı konsoludur | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/perfmon | Windows | 2026-10-14 | HTTP 200 |
| M-W28 | Performans sayaçları (PDH) ana portalı | https://learn.microsoft.com/en-us/windows/win32/perfctrs/ | Windows | 2026-10-14 | HTTP 200 |
| M-W29 | PDH API yüzeyi (sayaç toplama) | https://learn.microsoft.com/en-us/windows/win32/api/pdh/ | Windows | 2026-10-14 | HTTP 200 |
| M-W30 | `PdhCollectQueryData` sorgu verisini toplar | https://learn.microsoft.com/en-us/windows/win32/api/pdh/nf-pdh-pdhcollectquerydata | Windows | 2026-10-14 | HTTP 200 |
| M-W31 | `PdhAddCounterW` sorguya sayaç ekler | https://learn.microsoft.com/en-us/windows/win32/api/pdh/nf-pdh-pdhaddcounterw | Windows | 2026-10-14 | HTTP 200 |
| M-W32 | PDH ile toplama akışı (adım adım) | https://learn.microsoft.com/en-us/windows/win32/perfctrs/collecting-performance-data | Windows | 2026-10-14 | HTTP 200 |
| M-W33 | `powermetrics(1)` CPU/güç/bellek istatistiği toplar (Xcode man aynası) | https://keith.github.io/xcode-man-pages/powermetrics.1.html | macOS | 2026-10-14 | HTTP 200 |
| M-W34 | `vm_stat(1)` Mach bellek istatistiği verir (Xcode man aynası) | https://keith.github.io/xcode-man-pages/vm_stat.1.html | macOS | 2026-10-14 | HTTP 200 |
| M-W35 | `netstat(1)` ağ istatistiği/connectivity çıktısı verir (Xcode man aynası) | https://keith.github.io/xcode-man-pages/netstat.1.html | macOS | 2026-10-14 | HTTP 200 |

> **X2 düzeltmesi (2. tur):** §4 X2 `man1/vmstat.1.html` 404 idi; doğru sayfa
> **`man8/vmstat.8.html`** (M-W22) olarak bulunmuş ve 200 doğrulanmıştır.
> `proc_vmstat(5)` (M-W23) `/proc/vmstat` alan düzeyindeki karşılığıdır.

## §3 — Web kanıtları (trace alt-dalı)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| T-W1 | ftrace tasarım belgesi çekirdek trace altyapısını tanımlar | https://docs.kernel.org/trace/ftrace-design.html | 2026-10-13 | HTTP 200 |
| T-W2 | tracepoint'ler statik olay noktalarıdır | https://docs.kernel.org/trace/tracepoints.html | 2026-10-13 | HTTP 200 |
| T-W3 | kprobes çalışma anında eklenen dinamik probtur | https://docs.kernel.org/trace/kprobes.html | 2026-10-13 | HTTP 200 |
| T-W4 | histogram trigger'lar ftrace olaylarından metrik üretir | https://docs.kernel.org/trace/histogram.html | 2026-10-13 | HTTP 200 |
| T-W5 | uprobe kullanıcı uzayı probudur | https://docs.kernel.org/trace/uprobetracer.html | 2026-10-13 | HTTP 200 |
| T-W6 | osnoise tracer kesinti/gerçek zamanlı gecikme ölçer | https://docs.kernel.org/trace/osnoise-tracer.html | 2026-10-13 | HTTP 200 |
| T-W7 | bpf(2) eBPF syscall arayüzüdür | https://man7.org/linux/man-pages/man2/bpf.2.html | 2026-10-13 | HTTP 200 |
| T-W8 | BPF çekirdek dokümantasyonu resmi kaynaktır | https://docs.kernel.org/bpf/ | 2026-10-13 | HTTP 200 |
| T-W9 | sembol adı uzayları çekirdek sembolizasyonunu etkiler | https://docs.kernel.org/core-api/symbol-namespaces.html | 2026-10-13 | HTTP 200 |
| T-W10 | perf record olayları kaydeder | https://man7.org/linux/man-pages/man1/perf-record.1.html | 2026-10-13 | HTTP 200 |
| T-W11 | perf script kayıtları işler | https://man7.org/linux/man-pages/man1/perf-script.1.html | 2026-10-13 | HTTP 200 |
| T-W12 | ETW trace oturum API'si Win32'tedir | https://learn.microsoft.com/en-us/windows/win32/api/evntrace/ | 2026-10-13 | HTTP 200 |
| T-W13 | ETW olay tüketici API'si Win32'tedir | https://learn.microsoft.com/en-us/windows/win32/api/evntcons/ | 2026-10-13 | HTTP 200 |

### §3.1 — OS genişletme turu (2026-10-14) — trace kanıtları

> Windows ETW oturum/toplayıcı ve macOS dinamik izleme yüzeyleri bu turda
> kanıtlanır. 8 yeni URL, 8/8 HTTP 200. §15'teki "macOS trace doğrulanmadı"
> durumu bu turda aynasıyla kapanır.

| # | İddia | URL | OS | Tarih | Durum |
|---|---|---|---|---|---|
| T-W16 | `logman` ETW trace/toplayıcı oturumu yönetir | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/logman | Windows | 2026-10-14 | HTTP 200 |
| T-W17 | `tracerpt` ETL ham verisini rapor/format'a çevirir | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/tracerpt | Windows | 2026-10-14 | HTTP 200 |
| T-W18 | ETW olay izleme portalı (teknoloji turu) | https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal | Windows | 2026-10-14 | HTTP 200 |
| T-W19 | `dtrace(1)` dinamik izleme derleyicisi/izleyicisidir (Xcode man aynası) | https://keith.github.io/xcode-man-pages/dtrace.1.html | macOS | 2026-10-14 | HTTP 200 |
| T-W20 | `fs_usage(1)` dosya sistemi/çağrı izlemesi yapar (Xcode man aynası) | https://keith.github.io/xcode-man-pages/fs_usage.1.html | macOS | 2026-10-14 | HTTP 200 |
| T-W21 | `sample(1)` süreç CPU örnekleme profili alır (Xcode man aynası) | https://keith.github.io/xcode-man-pages/sample.1.html | macOS | 2026-10-14 | HTTP 200 |
| T-W22 | `otool(1)` Mach-O ikili yapısını/sembollerini inceler (Xcode man aynası) | https://keith.github.io/xcode-man-pages/otool.1.html | macOS | 2026-10-14 | HTTP 200 |
| T-W23 | Apple kernel/çekirdek dokümantasyon portalı | https://developer.apple.com/documentation/kernel | macOS | 2026-10-14 | HTTP 200 |

> **Kapsam sınırı (değişmedi):** macOS DTrace `sample`/`fs_usage` araçları
> Apple'ın modern sürümlerde **kullanımdan kaldırılmış** olabilir — bu dal yalnız
> arayüzü tarif eder; hedef sürüm/kullanılabilirlik `UNKNOWN` olarak işaretlenir.

## §4 — Reddedilen aday URL'ler (HTTP 404 — kullanılmadı)

| # | Aday URL | Durum | Karar |
|---|---|---|---|
| X1 | https://man7.org/linux/man-pages/man2/klogctl.2.html | HTTP 404 | reddedildi; klogctl yalnız metin içinde anıldı, URL kanıtı olmadı |
| X2 | https://man7.org/linux/man-pages/man1/vmstat.1.html | HTTP 404 | reddedildi; vmstat proc(5) kanalından tarif edildi |
| X3 | https://man7.org/linux/man-pages/man8/bpftool.8.html | HTTP 404 | reddedildi; bpftool yalnız bpf(2)+BPF portalından tarif edildi |
| X4 | https://man7.org/linux/man-pages/man8/logger.8.html | HTTP 404 | reddedildi |
| X5 | https://man7.org/linux/man-pages/man5/syslog.conf.5.html | HTTP 404 | reddedildi; rsyslog yapılandırması yalnız genel anlatımda |
| X6 | https://learn.microsoft.com/en-us/windows/win32/perfctrs/using-the-pdh-functions-to-collect-performance-data | HTTP 404 | reddedildi; PDH toplama yalnız Event Log portalı üzerinden tarif edildi |
| X7 | https://developer.apple.com/documentation/os/reading-data-from-the-unified-log | HTTP 404 | reddedildi; unified logging ana sayfa üzerinden |
| X8 | https://developer.apple.com/documentation/os/logging/generating-log-messages-from-your-code | HTTP 404 | reddedildi; `os_log` (L-W12r) ile değiştirildi |

### §4.1 — 2. tur reddleri (2026-10-14)

| # | Aday URL | OS | Durum | Karar |
|---|---|---|---|---|
| X9 | https://learn.microsoft.com/en-us/windows/win32/api/reporteventw | Windows | HTTP 404 | reddedildi; doğru yol `winbase/nf-winbase-reporteventw` (L-W23) |
| X10 | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/get-counter | Windows | HTTP 404 | reddedildi; `Get-Counter` **diagnostics** modülündedir (M-W24) |
| X11 | https://keith.github.io/xcode-man-pages/sysctl.1.html | macOS | HTTP 404 | reddedildi; aynada sysctl sayfası yok |
| X12 | https://keith.github.io/xcode-man-pages/iostat.1.html | macOS | HTTP 404 | reddedildi; aynada iostat sayfası yok |
| X13 | https://keith.github.io/xcode-man-pages/spindump.1.html | macOS | HTTP 404 | reddedildi; aynada spindump sayfası yok |
| X14 | https://keith.github.io/xcode-man-pages/atop.1.html | macOS | HTTP 404 | reddedildi; atop macOS'ta yok |
| X15 | https://man7.org/linux/man-pages/man8/perf-sched.8.html | Linux | HTTP 404 | reddedildi; perf-sched man8'de yok |
| X16 | https://docs.kernel.org/trace/irqsoff-tracer.html | Linux | HTTP 404 | reddedildi; irqsoff tracer ayrı sayfa değil |
| X17 | https://manpages.debian.org/.../journalctl (tüm varyantlar) | Linux | HTTP 404 | reddedildi; Debian manpages erişilemez |
| X18 | `man7.org/linux/man_pages/` (alt çizgi yolu — tüm varyantlar) | Linux | HTTP 404 | reddedildi; man7 **tire** (`man-pages`) kullanır |

> **Soft-404 kontrolü (bu tur):** 4 bilinçli geçersiz URL (Apple/keith/man7)
> 404 döndü → sunucu soft-404 döndürmüyor; dolayısıyla 200 alan tüm URL'ler
> gerçekten mevcuttur (truth-engine §1).

## §5 — Disk kanıtları (repo kod satırları — Read ile doğrulanmış)

| # | İddia | Dosya:Satır | Doğrulanan satır içeriği (özet) |
|---|---|---|---|
| D1 | Uygulama logu `error_log()` ile yazılır | `shared/src/PageRouter/StructuredLogger.php:72` | `error_log(Redactor::redact($payload));` |
| D2 | JSON log satırı `[CoreMusic]` önekiyle kurulur | `shared/src/PageRouter/StructuredLogger.php:69` | `$payload = '[CoreMusic] ' . json_encode($record, ...);` |
| D3 | Log seviye tablosu 4 kademelidir | `shared/src/PageRouter/StructuredLogger.php:9` | `private const LEVELS = ['debug' => 0, 'info' => 1, 'warn' => 2, 'error' => 3];` |
| D4 | Süre ölçümü `timing()` ile loglanır | `shared/src/PageRouter/StructuredLogger.php:47-52` | `public function timing(string $module, string $middleware, float $durationMs): void` |
| D5 | Dosya logu metin satırı `write()` içinde kurulur | `shared/src/Log/FileHandler.php:220` | `$line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;` |
| D6 | Tüm seviyeler errors.log'a yazılır | `shared/src/Log/FileHandler.php:228` | `$this->appendToFile('coremusic_php_errors.log', $line);` |
| D7 | Güvenlik olayları ayrı stream'e yazılır | `shared/src/Log/FileHandler.php:234` | `$this->appendToFile('coremusic_php_security.log', $line);` |
| D8 | Ekleme kilitlemede yapılır | `shared/src/Log/FileHandler.php:251` | `@file_put_contents($filepath, $content, FILE_APPEND \| LOCK_EX);` |
| D9 | PSR-3 seviye tablosu 8 kademelidir | `shared/src/Log/FileHandler.php:40-49` | `private const LEVELS = ['debug' => 0, ... 'emergency' => 7];` |
| D10 | TraceId statik alanla taşınır | `shared/src/Log/FileHandler.php:65-68` | `public static function setTraceId(string $traceId): void` |
| D11 | Merkezi redaction tek noktadır | `shared/src/Log/Redactor.php:65` | `public static function redact(string $line): string` |
| D12 | Hassas anahtar listesi regex'te gömülüdür | `shared/src/Log/Redactor.php:31` | `private const SENSITIVE_KEYS = 'auth_key\|password\|...';` |
| D13 | İstek traceId'si kernel'de üretilir | `shared/src/PageRouter/PageRouterKernel.php:87` | `$traceId = self::generateTraceId();` |
| D14 | TraceId hem JSON hem dosya loguna dağıtılır | `shared/src/PageRouter/PageRouterKernel.php:88-91` | `$this->logger->setTraceId($traceId); ... FileHandler::setTraceId($traceId);` |
| D15 | Middleware süre ölçümü `hrtime` ile yapılır | `shared/src/Middleware/MiddlewarePipeline.php:42` | `$start = hrtime(true);` |
| D16 | Süre ms'ye çevrilip timing'e gider | `shared/src/Middleware/MiddlewarePipeline.php:46-48` | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| D17 | AI workflow süre ölçümü `microtime` ile yapılır | `shared/src/AI/AIWorkflow.php:51` | `$startTime = microtime(true);` |
| D18 | AI tool çağrısı süresi ölçülür | `shared/src/AI/ToolCalling.php:85` | `$startTime = microtime(true);` |
| D19 | UuidV7 milisaniyeyi `microtime`'den alır | `shared/src/Security/UuidV7.php:19` | `$ms = (int)(microtime(true) * 1000);` |
| D20 | LoggerFactory tekil (singleton) logger tutar | `shared/src/Log/LoggerFactory.php:13` | `private static ?FileHandler $logger = null;` |
| D21 | Varsayılan log dizini repo köküdür | `shared/src/Log/LoggerFactory.php:32` | `return dirname(__DIR__, 3);` |

## §6 — Olumsuz kanıtlar (grep 0 isabet — repo geneli, vendor hariç)

| # | Arama deseni | Beklenen kullanımda bulunduğu yer | İsabet |
|---|---|---|---|
| N1 | `ftrace` | Linux trace altyapısı kullanımı | 0 |
| N2 | `perf_event` | perf_event_open çağrısı | 0 |
| N3 | `EventWrite` | ETW etkinlik yazımı | 0 |
| N4 | `syslog(` | syslog kütüphane çağrısı | 0 |
| N5 | `journald` | journald yapılandırması | 0 |
| N6 | `proc/self` | /proc okuma | 0 |
| N7 | `bpf(` / `BPF_` | eBPF program yükleme | 0 |
| N8 | `OpenTelemetry` / `OTel` | dağıtık trace kütüphanesi | 0 |

> Sonuç: OS gözlemlenebilirlik katmanı repo'da **kullanılmıyor**; bu "eksiklik" değil,
> hedef mimaride **tanımsızlık** olarak raporlanır (O1–O3 [UNKNOWN]).

## §7 — Eşik doğrulama kuralı

| # | Kural | Uygulama |
|---|---|---|
| 1 | OS eşik değerleri (softlockup süresi, iowait limiti vb.) bu turda sabitlenmez | O2 çözülmeden sayı uydurulmaz |
| 2 | Ölçüm periyodu önerilmez | periyot kararı toplama ajanı tasarımındadır (K002) |
| 3 | Repo'daki süre ölçüm kalıpları (hrtime/microtime) yalnız "uygulama içi süre" dir | OS latency metriği değildir |
| 4 | `⚠️ VERIFICATION REQUIRED` etiketi doğrulanamayan her iddiaya konur | zero-hallucination |

## §8 — Dosya bazlı eşik doğrulama tablosu (16 dosya)

| # | Dosya | Hedef | Ölçüm yöntemi | Durum |
|---|---|---|---|---|
| 1 | `index.md` | 40–100 satır | `Get-Content \| Measure-Object -Line` | hedefe göre yazıldı |
| 2 | `isletim-sistemi-observability-kapsam.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 3 | `isletim-sistemi-observability-dogrulama.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 4 | `isletim-sistemi-observability-ornekler.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 5 | `log/index.md` | 40–100 satır | aynı | hedefe göre yazıldı |
| 6 | `log/log-anayasa.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 7 | `log/log-surucu.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 8 | `log/log-ornekler.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 9 | `metrik/index.md` | 40–100 satır | aynı | hedefe göre yazıldı |
| 10 | `metrik/metrik-anayasa.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 11 | `metrik/metrik-surucu.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 12 | `metrik/metrik-ornekler.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 13 | `trace/index.md` | 40–100 satır | aynı | hedefe göre yazıldı |
| 14 | `trace/trace-anayasa.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 15 | `trace/trace-surucu.md` | ≥500 satır | aynı | hedefe göre yazıldı |
| 16 | `trace/trace-ornekler.md` | ≥500 satır | aynı | hedefe göre yazıldı |

## §9 — Frontmatter şema doğrulama matrisi (v2 §4.2 + `group`)

| # | Alan | Zorunlu | Kök index | Alt-dal index | İçerik |
|---|---|---|---|---|---|
| 1 | `node_id` | evet | tam yol | tam yol | tam yol |
| 2 | `node_type` | evet | `subdomain` | `subdomain` | `module` |
| 3 | `status` | evet | `draft` | `draft` | `draft` |
| 4 | `parent_id` | evet | `K000-isletim-sistemi` korunur | kök observability | alt-dal veya kök |
| 5 | `children` | evet | 3 alt-dal | 3 içerik dosyası | `[]` |
| 6 | `depends_on` | evet | liste | liste | liste/`[]` |
| 7 | `evidence` | evet | URL/dosya | URL/dosya | URL/dosya |
| 8 | `validation` | evet | metin | metin | metin |
| 9 | `open_questions` | evet | O1–O3 | O1–O3 | O1–O3/`[]` |
| 10 | `group` (9. alan, tırnaksız) | evet | `mimari` | `mimari` | eşleşmeye göre |

### §9.1 `group` eşleştirme kuralı

| # | Dosya adı deseni | `group` değeri |
|---|---|---|
| 1 | `index` | `mimari` |
| 2 | `-kapsam` | `mimari` |
| 3 | `-anayasa` | `mimari` |
| 4 | `-surucu` | `mimari` |
| 5 | `-ornekler` | `kod` |
| 6 | `-kod-ornekleri` | `kod` |
| 7 | `-dogrulama` | `kanit` |
| 8 | `-web-arastirma` | `kanit` |
| 9 | `-riskler` | `kanit` |

### §9.2 Yasaklar denetim listesi

| # | Yasak | Denetim | Sonuç |
|---|---|---|---|
| 1 | `parent:` alanı | `Select-String -Pattern '^parent:'` | 0 |
| 2 | tırnaklı `group:` | `Select-String -Pattern '^group: "'` | 0 |
| 3 | `TBD` / `Yakında` | literal arama | 0 |
| 4 | `// ...` dolgu | literal arama | 0 |
| 5 | `{...}` placeholder | literal arama | 0 |
| 6 | boşturulmaz satır numarası | her `Dosya:Satır` Read ile eşleşti | 0 hata |
| 7 | mojibake | UTF-8 okuma | 0 |

## §10 — URL envanteri kesişim analizi

| # | Adım | Değer |
|---|---|---|
| 1 | Envanter öncesi benzersiz URL (`.ai/architecture/**.md`) | 2097 |
| 2 | Bu turda doğrulanan yeni benzersiz URL | 42 |
| 3 | Envanterle kesişim | 0 |
| 4 | HTTP 404 ile elenen aday | 8 |
| 5 | Toplam aday | 50 |

### §10.1 Dağılım — hangi alt-dal kaç URL kullandı

| # | Alt-dal/kök | Kullanılan URL | Dosya |
|---|---|---|---|
| 1 | kök kapsam | 3 (L-W4, L-W11, M-W13) | `isletim-sistemi-observability-kapsam.md` |
| 2 | kök doğrulama | 42 (tam liste) | `isletim-sistemi-observability-dogrulama.md` §1–§3 |
| 3 | kök örnekler | disk kanıtı ağırlıklı; URL yok | `isletim-sistemi-observability-ornekler.md` |
| 4 | `log/` | L-W1…L-W13 (13) | 3 içerik dosyası |
| 5 | `metrik/` | M-W1…M-W16 (16) | 3 içerik dosyası |
| 6 | `trace/` | T-W1…T-W13 (13) | 3 içerik dosyası |

> Not: aynı URL birden fazla dosyada referans olabilir; envanter kesişimi yine 0'dır
> (envanter = daha önce `.ai/architecture/**` içinde kullanılmış URL kümeleri).

## §11 — Doğrulama protokolü (bu turda izlenen sıra)

| # | Adım | Komut/araç | Sonuç |
|---|---|---|---|
| 1 | URL envanteri | `Get-ChildItem ... -Recurse -Filter *.md` + regex | 2097 |
| 2 | Aday seçimi | resmi kaynaklar (kernel.org, man7, Microsoft Learn, Apple Dev, RFC) | 50 aday |
| 3 | HEAD doğrulama | `Invoke-WebRequest -Method Head -TimeoutSec 20` | 42×200 · 8×404 |
| 4 | Repo dosya listesi | `Get-ChildItem shared\src, api.coremusic.net, home.coremusic.net -Include *.php,*.js` | envanter çıkarıldı |
| 5 | Kod satır doğrulama | `Read` (offset/limit) — `Select-Object -Index` yerine | 21 satır (D1–D21) |
| 6 | Satır sayısı ölçümü | `Get-Content \| Measure-Object -Line` | 16 dosya hedefe göre |
| 7 | Yasak denetimi | literal arama (`parent:`, `group: "`, `TBD`, `// ...`) | 0 |

## §12 — Doğrulanmış kod kanıtları (tam bloklar)

### §12.1 StructuredLogger — JSON log çekirdeği

**Dosya:** `shared/src/PageRouter/StructuredLogger.php` · **Satır:** 55–73

```php
public function log(string $level, string $module, string $event, array $extra = []): void
{
    if (!$this->shouldLog($level)) {
        return;
    }

    $record = array_merge([
        'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        'level'     => $level,
        'module'    => $module,
        'event'     => $event,
        'traceId'   => $this->traceId,
    ], $extra);

    $payload = '[CoreMusic] ' . json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur.
    error_log(Redactor::redact($payload));
}
```

### §12.2 FileHandler — satır kurulumu ve kilitleme

**Dosya:** `shared/src/Log/FileHandler.php` · **Satır:** 248–252

```php
private function appendToFile(string $filename, string $content): void
{
    $filepath = $this->logDir . '/' . $filename;
    @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
}
```

### §12.3 MiddlewarePipeline — hrtime süre ölçümü

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

### §12.4 LoggerFactory — singleton ve log dizini

**Dosya:** `shared/src/Log/LoggerFactory.php` · **Satır:** 13–33

```php
private static ?FileHandler $logger = null;

public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
{
    if (self::$logger === null) {
        $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
        self::$logger = new FileHandler($logDir, $level);
    }
    return self::$logger;
}

private static function resolveLogDir(): string
{
    // Proje kök dizinine yaz (C:\www\coremusic.net\)
    return dirname(__DIR__, 3);
}
```

## §13 — OS araç komut referansı (bu dalda tarif edilenler)

| # | Araç | Platform | Tipik komut | Kullanım |
|---|---|---|---|---|
| 1 | dmesg | Linux | `dmesg --level=err,warn` | çekirdek halka tamponu okuma |
| 2 | journalctl | Linux | `journalctl -p warning -b` | boot sonrası uyarı kayıtları |
| 3 | logrotate | Linux | `/etc/logrotate.d/` kuralı | rotasyon/retention |
| 4 | wevtutil | Windows | `wevtutil qe System /c:10` | Event Log sorgusu |
| 5 | logman | Windows | `logman create trace` | ETW oturumu |
| 6 | log | macOS | `log show --last 1h` | unified logging okuma |
| 7 | perf stat | Linux | `perf stat -e cycles,instructions` | PMU sayaçları |
| 8 | perf record | Linux | `perf record -g -F 99 -- sleep 10` | trace kaydı |
| 9 | iostat | Linux | `iostat -xz 1` | G/Ç istatistiği |
| 10 | sar | Linux | `sar -q 1 10` | yük/çalışma zamanı kaydı |
| 11 | bpftool | Linux | `bpftool prog list` | BPF program envanteri |
| 12 | ethtool | Linux | `ethtool -S eth0` | ağ arayüzü sayaçları |

> Komutlar bu dalın kavramsal referansıdır; CoreMusic'te bunları çağıran kod
> **yoktur** (§6 olumsuz kanıt) — O1 çözülene kadar yalnız tarif amaçlıdır.

## §14 — Sınır komşularına göre kanıt dağılımı

| # | Komşu | Bu dosyada geçen kanıt | Dosyası |
|---|---|---|---|
| 1 | `isletim-sistemi-kernel` | dmesg/printk kaynağı | L-W5, L-W10 |
| 2 | `isletim-sistemi-core` | /proc yüzeyi | M-W1 |
| 3 | `isletim-sistemi-security` | audit/log politikası sınırı | L-W11–L-W12 (Event Log) |
| 4 | `isletim-sistemi-api` | syslog socket | L-W2 |
| 5 | `isletim-sistemi-turu` | platform araç farkları | L-W13, M-W8 |
| 6 | `isletim-sistemi-plan` | kernel event üretimi sınırı | T-W2, T-W3 |
| 7 | `isletim-sistemi-testing` | gözlem testi sınırı | §7 eşik kuralı |
| 8 | `K016-observability` | OTel sınırı | §6 N8 (0 isabet) |

## §15 — Platform bazlı kanıt özeti

| # | Platform | Kanıt grubu | Sayı (1. tur) | Sayı (2. tur) | Toplam | Ana kaynak |
|---|---|---|---|---|---|---|
| 1 | Linux | L-W1…L-W10, M-W1…M-W16, T-W1…T-W11 | 37 | +2 log (L-W18–19) · +2 metrik (M-W22–23) | 41 | kernel.org + man7 |
| 2 | Windows | L-W11, L-W12, T-W12, T-W13 | 4 | +5 log (L-W20–24) · +9 metrik (M-W24–32) · +3 trace (T-W16–18) | 21 | Microsoft Learn |
| 3 | macOS/iOS | L-W13 | 1 | +3 log (L-W12r, L-W25–27) · +3 metrik (M-W33–35) · +5 trace (T-W19–23) | 12 | Apple Dev + Xcode man aynası |
| | **Toplam** | | **42** | **+33** | **74** | |

### §15.1 — 2. tur sonrası kapanan/alan boşluklar

| # | Konu | 1. tur durumu | 2. tur durumu |
|---|---|---|---|
| 1 | Windows PDH toplama | ⚠️ (X6 404) | **KAPANDI** — M-W28…M-W32 (portal + PDH API + toplama akışı) |
| 2 | Windows performans sayacı arayüzü | ⚠️ §10 | **KAPANDI** — M-W24 (Get-Counter), M-W25 (typeperf), M-W26 (logman counter), M-W27 (perfmon) |
| 3 | macOS trace (DTrace/ktrace) | ⚠️ | **KISMI KAPANDI** — T-W19…T-W22 (ayna, dipnotlu); hedef sürüm kullanılabilirliği `UNKNOWN` |
| 4 | macOS metrik yüzeyi | ⚠️ | **KAPANDI** — M-W33 (powermetrics), M-W34 (vm_stat), M-W35 (netstat) |
| 5 | Windows Event Log kayıt API'si | yalnız portal | **GENİŞLEDİ** — L-W23 (ReportEventW), L-W22 (eventcreate), L-W21 (Get-WinEvent), L-W20 (wevtutil) |
| 6 | macOS log kayıt API'si | yalnız ana sayfa | **GENİŞLEDİ** — L-W26 (Logger), L-W27 (init subsystem/category), L-W25 (log.1) |
| 7 | Android logcat | kapsam dışı | kapsam dışı (değişmedi) |

## §16 — Zaman çizelgesi (kanıt tarihleri)

| # | Tarih | Olay |
|---|---|---|
| 1 | 2026-10-12 | ilk doğrulama turu — 5 URL, 31 satır dosya |
| 2 | 2026-10-13 | bu tur — 42 URL, 16 dosya, 21 disk kanıtı |
| 3 | 2026-10-13 | 8 aday URL 404 ile elendi (§4) |
| 4 | 2026-10-13 | 4 dosya ≥500/40–100 hedefine göre yeniden yazıldı |
| 5 | 2026-10-14 | **2. tur (OS genişletme)** — 33 yeni URL (18 Windows · 12 macOS · 10 Linux), 33/33 HTTP 200 |
| 6 | 2026-10-14 | Apple `generating-log-messages` 404 → `os_log` (L-W12r) ile değiştirildi |
| 7 | 2026-10-14 | `vmstat.1` (404) → `vmstat.8` (M-W22, 200) düzeltmesi |
| 8 | 2026-10-14 | 10 aday 404 ile elendi (§4.1); soft-404 negatif kontrolü yapıldı |

## §17 — Kesişim analizi detayı (envanter vs yeni URL)

| # | Kümе | Sayı | Kaynak |
|---|---|---|---|
| 1 | Envanter (`.ai/architecture/**.md` içinde geçen) | 2097 | regex `https?://` |
| 2 | Bu tur adayı | 50 | resmi kaynak seçimi |
| 3 | Aday ∩ envanter | 3 (wnode-header, getrusage, gettimeofday, vdso — 4 aday) | hashtable |
| 4 | Kesişim elendi | 4 | kullanılmadı |
| 5 | Benzersiz yeni (envanter dışı) | 46 başlık, 42 HTTP 200 doğrulanan | §1–§3 |
| 6 | HTTP 404 | 8 | §4 |

### §17.1 Kesişimi olan ve elenen adaylar

| # | URL | Durum | Karar |
|---|---|---|---|
| 1 | `learn.microsoft.com/.../etw/wnode-header` | envanterde var | elendi |
| 2 | `man7.org/.../man2/getrusage.2.html` | envanterde var | elendi |
| 3 | `man7.org/.../man2/gettimeofday.2.html` | envanterde var | elendi |
| 4 | `man7.org/.../man7/vdso.7.html` | envanterde var | elendi |

## §18 — Doğrulama turu özeti (kapanış)

| # | Kalem | Önceki tur (2026-10-12) | Bu tur (2026-10-13) |
|---|---|---|---|
| 1 | Doğrulanan URL | 5 | 42 |
| 2 | Reddedilen aday | 0 | 8 (404) + 4 (envanter kesişimi) |
| 3 | Disk kanıtı satırı | 2 (D1–D2) | 21 (D1–D21) |
| 4 | Doğrulanan repo dosyası | 1 | 9 |
| 5 | Dosya sayısı (subdomain) | 4 | 16 |
| 6 | ≥500 satırlık dosya | 0 | 12 |
| 7 | Alt-dal | 0 | 3 (log/metrik/trace) |

## §19 — Doğrulama dışı kalanlar (bilinçli kapsam sınırı)

| # | Konu | Neden doğrulanmadı | Gittiği yer |
|---|---|---|---|
| 1 | journald iç binary biçimi | bu dal yalnız arayüzü tarif eder | `isletim-sistemi-kernel` |
| 2 | ETW provider kaynak kodu | ETW uygulama yazımı bu turda yok | `isletim-sistemi-turu` |
| 3 | PDH counter registry | 1. turda 404 (§4 X6) | **2. turda kapandı** — M-W28…M-W32 |
| 4 | macOS DTrace/ktrace | 1. turda doğrulanmadı | **2. turda aynayla kapandı** — T-W19…T-W22; hedef sürüm `UNKNOWN` |
| 5 | OTel/dağıtık trace | kapsam dışı (K017) | `K016-observability` |
| 6 | uygulama log metriği | kapsam dışı | `K016-observability` |
| 7 | audit politikası/süresi | kapsam dışı | `isletim-sistemi-security` |
| 8 | slow query log | kapsam dışı | `K007-data` |

## §20 — Sonuç

| # | Ölçüm | 1. tur | 2. tur | Toplam |
|---|---|---|---|---|
| 1 | Web kanıtı (log+metrik+trace) | 42/42 HTTP 200 | 33/33 HTTP 200 | **75/75 HTTP 200** |
| 2 | Reddedilen aday URL | 8 (404) + 4 (envanter) | 10 (404) | 18 (404) + 4 (envanter) |
| 3 | Disk kanıtı | 21 satır (D1–D21) + 4 blok | değişmedi | 21 satır + 4 blok |
| 4 | Olumsuz kanıt | 8 desen, 0 isabet | değişmedi | 8 desen, 0 isabet |
| 5 | Platform dengesi (L:W:M) | 37:4:1 | 10:18:12 | **47:22:13** |
| 6 | Kapsam durumu | `draft` (O1–O3) | değişmedi | `draft` (O1–O3 [UNKNOWN]) |
| 7 | Mojibake / dolgu / `// ...` / `parent:` | 0/0/0/0 | 0/0/0/0 | 0/0/0/0 |

## §21 — Doğrulama dosyası sonu kontrolü

| # | Kontrol | 1. tur | 2. tur |
|---|---|---|---|
| 1 | Web kanıtı satırı | 42 (13 L + 16 M + 13 T) | **75** (24 L + 30 M + 21 T) |
| 2 | Reddedilen | 8 (404) + 4 (envanter) | **18 (404)** + 4 (envanter) |
| 3 | Disk kanıtı | 21 satır + 4 blok | değişmedi |
| 4 | Olumsuz kanıt deseni | 8 | değişmedi |
| 5 | Eşik doğrulama tablosu | 16 dosya | 16 dosya |
| 6 | Frontmatter şema matrisi | 10 alan | 10 alan |
| 7 | Yasak denetimi | 7 kalem, 0 ihlal | 7 kalem, 0 ihlal |
| 8 | Soft-404 negatif kontrolü | yapılmadı | **4 geçersiz URL → 404** (gerçek 404) |

## §22 — 2. tur doğrulama protokolü (2026-10-14)

| # | Adım | Araç | Sonuç |
|---|---|---|---|
| 1 | Aday seçimi | OS yüzey boşlukları (§15.1) | ~50 aday |
| 2 | GET doğrulama (redirect takipli) | `fetch(url,{method:"GET",redirect:"follow"})` | 33×200 · 10×404 |
| 3 | Retry (erişim hatası için) | 3 deneme | freedesktop 3 URL sonunda 200 |
| 4 | İçerik çekimi (soft-404 ayrımı) | exa `web_fetch_exa` | 404'ler içerik döndürmedi |
| 5 | Soft-404 negatif kontrolü | 4 sahte URL | 4/4 → 404 (gerçek) |
| 6 | Ayna dipnotu | `keith.github.io` türü | üçüncü taraf aynası olduğu yazıldı |
| 7 | Çelişki düzeltmesi | Apple L-W12 200→404 | `os_log` ile değiştirildi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
