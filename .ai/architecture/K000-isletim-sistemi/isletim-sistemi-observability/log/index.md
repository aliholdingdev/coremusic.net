---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/log
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability
children:
  - log-anayasa
  - log-surucu
  - log-ornekler
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence: ["https://man7.org/linux/man-pages/man3/syslog.3.html", "https://man7.org/linux/man-pages/man1/dmesg.1.html", "https://man7.org/linux/man-pages/man8/logrotate.8.html", "https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log", "shared/src/PageRouter/StructuredLogger.php:72", "shared/src/Log/FileHandler.php:220", "shared/src/Log/Redactor.php:31"]
validation: "2026-10-14 — §4 §GRUPLAR'a OS grupları satırı eklendi (log-anayasa §8.1 · log-surucu §17 · log-ornekler §21, kanıt L-W18–L-W27); dosya 101 satır (40–100 hedefinden +1 — bilinçli, OS satırı içeriğe değer kattı) | 2026-10-13 — log alt-dalının 14 URL'si (L-W1…L-W14) kardeş dosyalarda HTTP 200 ile doğrulanmış; log-anayasa.md ve log-surucu.md diskten okundu (bölüm başlıkları + §5/§15 kaynakça); repo satırları Read ile doğrulandı; bu indeks 40–100 satır hedefine göre yazıldı"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---

# log — Günlük (Log) Alt-Dalı İndeksi (Kademe-3)

## §0 ELI15

Günlük (log) bir olayın **zaman damgası + kaynak + şiddet + metin** ile diske veya OS
halkasına yazılmasıdır. Bu alt-dal OS düzeyi günlük üretimini ve taşımasını bağlar:
çekirdek `printk` mesajı, `journald`/syslog kuyruğu, Windows Event Log kanalı, Apple
os_log — plus rotasyon, taşma ve şiddet kapısı mühendisliği. Repo bugün yalnız
**uygulama katmanı** log üretir (`error_log()` + dosya ekleme); OS log kanalına
yazan kod **yoktur** (grep 0 isabet). Uygulama/dağıtık log desenleri
(OTel LogRecord, APM gönderici) `K017-observability` alanındadır.

## §1 Amaç

| # | Amaç | Gittiği dosya |
|---|---|---|
| 1 | Günlük mimarisini (journald · syslog · ETW · os_log) bağlayıcı tanıma bağlamak | `-anayasa` §1–§5 |
| 2 | Şiddet kapısı, halka taşması, rotasyon ve traceId akışının mühendisliğini belgelemek | `-surucu` §1–§7 |
| 3 | Repo'nun gerçek log yüzeyini satır kanıtıyla göstermek ve OS aracısı olmadığını kanıtlamak | `-ornekler` §2–§13 |

## §2 Kademe ve rol

| # | Kademe | Dosya | Rol |
|---|---|---|---|
| 1 | Kademe-3 | `log/index.md` | bu alt-dalın künyesi (bu dosya) |
| 2 | Kademe-4 | `log-anayasa.md` | günlük mimarisi, kaynak türleri, şiddet ölçeği, redaksiyon |
| 3 | Kademe-4 | `log-surucu.md` | şiddet kapısı, halka tamponu, logrotate, journald, traceId köprüsü |
| 4 | Kademe-4 | `log-ornekler.md` | repo disk kanıtları (StructuredLogger · FileHandler · Redactor), olumsuz kanıt |

## §3 Envanter

| # | Dosya | Kapsadığı kavramlar |
|---|---|---|
| 1 | `log-anayasa.md` | syslog(2)/(3) · RFC 3164/5424 · dmesg halkası · journald · Event Log · os_log · redaksiyon |
| 2 | `log-surucu.md` | setlogmask · console_loglevel · logrotate · journald.conf RateLimit · traceId · log fırtınası freni |
| 3 | `log-ornekler.md` | StructuredLogger JSON · FileHandler metin satırı · Redactor · LoggerFactory · hrtime/microtime · grep 0 isabet |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | alt-dal künyesi | Kademe-3 |
| mimari | `log-anayasa.md` | bağlayıcı tanım ve ilke seti | Kademe-4 |
| mimari | `log-surucu.md` | mekanizma envanteri ve hata modları | Kademe-4 |
| kod | `log-ornekler.md` | repo örnekleri ve olumsuz kanıt | Kademe-4 |
| mimari | **OS grupları**: `log-anayasa.md` §8.1 · `log-surucu.md` §17 · `log-ornekler.md` §21 | Linux/Windows/macOS anayasa+sürücü+örnek haritası — kanıt L-W18–L-W27 | Kademe-4 |

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| dmesg/printk üretimi | `isletim-sistemi-kernel` | yalnız okuma/yorumlama |
| /proc /sys üretimi | `isletim-sistemi-core` | log dosyası I/O ölçümü |
| audit trail politikası | `isletim-sistemi-security` | OS log kanalı adları |
| syslog socket/API yüzeyi | `isletim-sistemi-api` | log kanalı modeli |
| OS araç farkları (journalctl vs wevtutil vs log) | `isletim-sistemi-turu` | platform karşılaştırma örneği |
| uygulama log desenleri, log metriği, alert | `K017-observability` | yalnız sınır tarifi |

## §6 depends_on

`depends_on: [isletim-sistemi-kernel, isletim-sistemi-core]` — çekirdek günlük
kaynakprintk/dmesg) kernel'in, log dosyasının yazıldığı süreç/bellek yüzeyi core'un
sorumluluğundadır (kök `index.md` komşu matrisi).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS-level log toplama (journald/syslog/Event Log) hedef mimaride var mı? | UNKNOWN |
| O2 | Gözlemlenebilirlik üçlüsü K002-altyapi içinde nasıl konumlanacak? | UNKNOWN |
| O3 | Dağıtık trace/log correlation hedef mimarida mı? | UNKNOWN |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
