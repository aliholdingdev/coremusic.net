---
node_id: K000-isletim-sistemi/isletim-sistemi-observability
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children:
  - log
  - metrik
  - trace
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://docs.kernel.org/trace/ftrace-design.html
  - https://docs.kernel.org/trace/tracepoints.html
  - https://docs.kernel.org/trace/kprobes.html
  - https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal
  - https://man7.org/linux/man-pages/man1/perf.1.html
  - https://man7.org/linux/man-pages/man3/syslog.3.html
  - https://www.rfc-editor.org/rfc/rfc3164
  - shared/src/PageRouter/StructuredLogger.php:72
  - shared/src/Log/FileHandler.php:220
validation: "2026-10-14 — §GRUPLAR'a OS grupları tablosu eklendi (log/metrik/trace × Linux/Windows/macOS bölüm haritası); Dosya indeksi 42 → 75 URL olarak güncellendi | 2026-10-13 — 42 kanıt URL'si HTTP HEAD ile doğrulandı (42/42 HTTP 200; envanter 2097 ile kesişim 0) · StructuredLogger.php:72 ve FileHandler.php:220 satır içeriği diskten okundu · OS gözlemlenebilirlik aracı grep'i 0 isabet · alt-dal envanteri glob ile sayıldı (log/ metrik/ trace/ = 12 içerik dosyası) | 2026-10-14 — isletim-sistemi-observability-kod-seti.md yazıldı (667 satır · 17 kod bloğu · 19 resmi URL HTTP 200 · spot-check: orkestratör)"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---
# isletim-sistemi-observability

> **Kapsam (tek cümle):** İşletim sistemi düzeyinde gözlemlenebilirlik — `dmesg` · `syslog/journald` ·
> `ETW` · `Tracepoints/kprobes/eBPF` · `ktrace` · `/proc` `/sys` · `perf` · `ethtool` · kernel
> lockup detector; repo bugün yalnız **uygulama katmanı** log üretir, OS gözlemlenebilirliği
> **tanımsızdır** (O1–O3 [UNKNOWN]).

## Dosya indeksi

| Dosya | Ne içerir |
|---|---|
| [`isletim-sistemi-observability-kapsam.md`](isletim-sistemi-observability-kapsam.md) | Kapsam içi/dışı, OS↔uygulama ayrım matrisi, komşu sınırı, açık sorular |
| [`isletim-sistemi-observability-dogrulama.md`](isletim-sistemi-observability-dogrulama.md) | 75 URL (75/75 HTTP 200) + disk kanıtı + olumsuz kanıt matrisi |
| [`isletim-sistemi-observability-ornekler.md`](isletim-sistemi-observability-ornekler.md) | Gerçek repo kodu: StructuredLogger, FileHandler, Redactor, hrtime/microtime |

## K017 ayrımı (bağlayıcı)

Bu dal **OS/çekirdek düzeyi** gözlemlenebilirliği (ftrace, perf, ETW, journald, syslog, /proc
sayaçları) anlatır. Uygulama/dağıtık gözlemlenebilirlik (OpenTelemetry, Prometheus, APM, alert
kuralları) **`K017-observability`** alanındadır — bu dosyalar o alana **dokunmaz**, sınırı
yalnızca tarif eder.

## Alt-dal komşu sınır matrisi

| Komşu | Bu dalın ondan aldığı | Bu dalın ona verdiği |
|---|---|---|
| `K017-observability` | uygulama/dağıtık desenler (OTel, alert, log metriği) | OS kaynağı listesi — uygulama katmanına girmez |
| `isletim-sistemi-kernel` | dmesg/printk kaynağı, tracepoint tanımı | okuma/yorumlama arayüzü |
| `isletim-sistemi-core` | /proc /sys üretimi, süreç/bellek kavramı | ölçüm yöntemi |
| `isletim-sistemi-plan` | kernel event üretimi | event yakalama (tracepoint/kprobe) |
| `isletim-sistemi-security` | audit trail politikası | OS log kanalı adları |
| `isletim-sistemi-testing` | gözlem testi stratejisi | OS kaynağı tanımı |
| `isletim-sistemi-api` | syslog uçları (socket/API yüzeyi) | log kanalı modeli |
| `isletim-sistemi-turu` | OS araç farkları (journalctl vs wevtutil vs log) | gözlemlenebilirlik kavramı |

## Mevcut durum (repo kanıtı, 2026-10-13)

| Katman | Repo durumu | Kanıt |
|---|---|---|
| Uygulama logu (JSON) | var | `shared/src/PageRouter/StructuredLogger.php:72` |
| Uygulama logu (metin, 5 dosya) | var | `shared/src/Log/FileHandler.php:220` |
| OS sistem logu (syslog/journald/Event Log) | yok | grep 0 isabet |
| Metrik toplama (perf, /proc okuma) | yok | grep 0 isabet |
| Trace (ftrace, ETW oturumu, eBPF) | yok | grep 0 isabet |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `log/index.md` | OS günlük mimarisi alt-dalı künyesi | Kademe-3 |
| mimari | `metrik/index.md` | OS metrik/sayaç alt-dalı künyesi | Kademe-3 |
| mimari | `trace/index.md` | OS trace/eBPF alt-dalı künyesi | Kademe-3 |
| kod | `isletim-sistemi-observability-kod-seti.md` | Cross-platform log hattı / monotonic saat / ölçümler kod seti — 667 satır · 17 kod bloğu · 19 resmi URL | Kademe-2 (kök) |

### OS grupları (2. tur genişletme — 2026-10-14)

> Her üç alt-dal da **Linux · Windows · macOS** ekseninde genişletildi;
> kanıt dengesi 1. tur 37:4:1 → **47:22:13** (toplam 75 × HTTP 200).

| OS | Log (anayasa/sürücü/örnek) | Metrik (anayasa/sürücü/örnek) | Trace (anayasa/sürücü/örnek) |
|---|---|---|---|
| Linux | §8.1 journald/syslog/auditd · §17.1 · §21.1 | §10–§11 /proc,PMU · §10 · §7.1 | §2–§5 ftrace/eBPF · §1–§5 · §13 |
| Windows | §8.1 Event Log/ReportEventW · §17.2 · §21.2 | §10 PDH (M-W28–32) · §10/§13 · §7.1.1 | §6/§6.1 ETW · §6.1 · §13.1 |
| macOS | §8.1 os_log/Logger · §17.3 · §21.3 | §10.1 powermetrics/vm_stat · §10/§13 · §7.1.2 | §8 DTrace (ayna) · §10 · §13.1 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
