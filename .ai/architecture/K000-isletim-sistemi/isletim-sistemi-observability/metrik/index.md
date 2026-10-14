---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability
children:
  - metrik-anayasa
  - metrik-surucu
  - metrik-ornekler
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://man7.org/linux/man-pages/man5/proc.5.html
  - https://man7.org/linux/man-pages/man1/perf.1.html
  - https://man7.org/linux/man-pages/man1/perf-stat.1.html
  - https://man7.org/linux/man-pages/man2/perf_event_open.2.html
  - https://man7.org/linux/man-pages/man1/uptime.1.html
  - https://man7.org/linux/man-pages/man1/iostat.1.html
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/AI/AIWorkflow.php:51
validation: "2026-10-13 — metrik alt-dalının 16 URL'si (M-W1…M-W16) kök -dogrulama.md §2'de HTTP 200 doğrulanmış; 2 yeni URL (uptime.1, perf_event_open.2) bu turda webfetch ile HTTP 200 doğrulandı (envanter kesişimi 0); ETW performans sayacı/PDH adayı HTTP 404 → ⚠️; repo süre ölçüm satırları Read ile doğrulandı; bu indeks 40–100 satır hedefine göre yazıldı"
open_questions:
  - "O1: OS-level metrik/log toplama (perf, ETW, journald, syslog) hedef mimaride var mı, yoksa yalnız uygulama logu mu? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---

# metrik — OS Metrik/Sayaç Alt-Dalı İndeksi (Kademe-3)

## §0 ELI15

Metrik, bir sayacın **zaman içinde okunarak** izlenmesidir: CPU kullanımı, bellek
basıncı, G/Ç zamanı, yük ortalaması. OS bunu `/proc` ve `/sys` sanal dosyalarından
(sayım) veya PMU/`perf_event_open` sayaçlarından (donanım sayacı) verir. Repo'da
OS metriği okuyan kod **yoktur** (grep 0 isabet); bulunan tek şey uygulama içi
süre ölçümüdür (`hrtime`/`microtime`) — bu, OS latency metriği **değildir**.
Uygulama/dağıtık metrik (Prometheus, OTel metriği, APM) `K017-observability`
alanındadır.

## §1 Amaç

| # | Amaç | Gittiği dosya |
|---|---|---|
| 1 | Sayaç türlerini (sayaç/gauge) ve OS kaynak yüzeylerini (/proc, /sys, PMU, ETW) bağlayıcı tanıma bağlamak | `-anayasa` §1–§6 |
| 2 | Okuma/periyot/eşik mühendisliğini (perf_event_open, periyot, softlockup eşiği) belgelemek | `-surucu` §1–§7 |
| 3 | Repo'nun gerçek süre ölçümü yüzeyini satır kanıtıyla göstermek ve OS metrisi olmadığını kanıtlamak | `-ornekler` §1–§5 |

## §2 Kademe ve rol

| # | Kademe | Dosya | Rol |
|---|---|---|---|
| 1 | Kademe-3 | `metrik/index.md` | bu alt-dalın künyesi (bu dosya) |
| 2 | Kademe-4 | `metrik-anayasa.md` | sayaç türleri, /proc-/sys yüzeyi, PMU, ETW, platform karşılaştırma |
| 3 | Kademe-4 | `metrik-surucu.md` | okuma yolları, perf_event_open, periyot/eşik, uptime/load, olumsuz kanıt |
| 4 | Kademe-4 | `metrik-ornekler.md` | repo disk kanıtları (hrtime/microtime), OS araç tarifi, K017 sınırı |

## §3 Envanter

| # | Dosya | Kapsadığı kavramlar |
|---|---|---|
| 1 | `metrik-anayasa.md` | sayaç/gauge · /proc /sys · perf_event/PMU · ETW performans sayacı ⚠️ · uptime/load · cgroup v2 |
| 2 | `metrik-surucu.md` | perf_event_open (sayma vs örnekleme) · periyot kararı · softlockup/hung task eşikleri · platform farkları |
| 3 | `metrik-ornekler.md` | hrtime/microtime repo kanıtları · procps araç tarifi · OS metriği olmadığına dair olumsuz kanıt |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | alt-dal künyesi | Kademe-3 |
| mimari | `metrik-anayasa.md` | bağlayıcı tanım ve ilke seti | Kademe-4 |
| mimari | `metrik-surucu.md` | mekanizma envanteri ve hata modları | Kademe-4 |
| kod | `metrik-ornekler.md` | repo örnekleri ve olumsuz kanıt | Kademe-4 |

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| /proc /sys üretimi | `isletim-sistemi-core` | okuma yöntemi |
| PMU/sayaç çekirdeği | `isletim-sistemi-kernel` | arayüz tanımı |
| kernel event sayacı etiketleri | `isletim-sistemi-plan` | yalnız sayma |
| eşik alert çıkışı (softlockup logu) | `K017-observability` | OS eşiği kalır, iş alarmı gitmez |
| metrik doğrulama testi | `isletim-sistemi-testing` | gereksinim düzeyi |
| prometheus/OTel metriği, APM | `K017-observability` | yalnız sınır tarifi |

## §6 depends_on

`depends_on: [isletim-sistemi-kernel, isletim-sistemi-core]` — sayaçların
üretimi (PMU, /proc) kernel/core'a aittir; bu dal yalnız okuma ve yorumlama
kadarını taşır (kök `index.md` komşu matrisi).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS metrik toplama (perf, ETW sayacı, /proc okuyucu) hedef mimaride var mı? | UNKNOWN |
| O2 | Gözlemlenebilirlik üçlüsü K002-altyapi içinde nasıl konumlanacak? | UNKNOWN |
| O3 | Dağıtık metrik/trace hedef mimarida mı? | UNKNOWN |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
