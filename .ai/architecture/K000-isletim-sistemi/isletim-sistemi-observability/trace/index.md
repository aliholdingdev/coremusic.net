---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/trace
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability
children:
  - trace-anayasa
  - trace-surucu
  - trace-ornekler
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://docs.kernel.org/trace/ftrace-design.html
  - https://docs.kernel.org/trace/tracepoints.html
  - https://man7.org/linux/man-pages/man2/bpf.2.html
  - https://docs.kernel.org/bpf/verifier.html
  - https://learn.microsoft.com/en-us/windows/win32/api/evntrace/
  - shared/src/PageRouter/PageRouterKernel.php:87
  - shared/src/PageRouter/StructuredLogger.php:66
validation: "2026-10-14 — §4 §GRUPLAR'a OS grupları satırı eklendi (trace-anayasa §6.1/§8/§16.1 · trace-surucu §6.1/§10/§20.1 · trace-ornekler §13/§13.1, kanıt T-W16–T-W23); dosya 101 satır (40–100 hedefinden +1 — bilinçli, OS satırı içeriğe değer kattı) | 2026-10-13 — trace alt-dalının 13 URL'si (T-W1…T-W13) kök -dogrulama.md §2'de HTTP 200 doğrulanmış; 3 yeni URL (bpf/verifier.html, bpf/helpers.html, bpf/ringbuf.html) bu turda doğrulandı (envanter kesişimi 0); repo traceId satırları Read/grep ile doğrulandı; OS trace aracı grep'i 0 isabet"
open_questions:
  - "O1: OS-level trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı, yoksa yalnız uygulama traceId'si mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık trace (OTel) hedef mimarida mı, yalnız uygulama içi log correlation mı? [UNKNOWN]"
group: mimari
---

# trace — OS Trace/eBPF Alt-Dalı İndeksi (Kademe-3)

## §0 ELI15

Trace, bir olayın **"ne zaman, hangi sırayla, hangi süreçte"** olduğunun
kaydıdır: metrik "kaç tane?" der, log "ne oldu?" der, trace "hangi yoldan
geçti?" der. OS bunu **tracepoint / kprobe / eBPF** (Linux), **ETW oturumu**
(Windows) ile verir. Repo'da OS trace aracı **yoktur** (grep 0 isabet); bulunan
tek şey uygulama içi **`traceId`**'dir (`PageRouterKernel.php:87`) — istek
korelasyon kimliğidir, OS trace **değildir**. Uygulama/dağıtık trace (OTel,
span) `K017-observability` alanındadır.

## §1 Amaç

| # | Amaç | Gittiği dosya |
|---|---|---|
| 1 | Trace'in üç anlamını (OS olay izi · uygulama traceId · dağıtık span) ayırıcı tabloyla bağlamak ve ftrace/eBPF/ETW anayasasını yazmak | `-anayasa` §1–§9 |
| 2 | Ölçüm ve sürme mekanizmalarını (osnoise, histogram, perf record/script, ETW oturum mühendisliği, sembolizasyon, overhead) belgelemek | `-surucu` §1–§7 |
| 3 | Repo'nun gerçek traceId yüzeyini satır kanıtıyla göstermek ve OS trace olmadığını kanıtlamak | `-ornekler` §1–§10 |

## §2 Kademe ve rol

| # | Kademe | Dosya | Rol |
|---|---|---|---|
| 1 | Kademe-3 | `trace/index.md` | bu alt-dalın künyesi (bu dosya) |
| 2 | Kademe-4 | `trace-anayasa.md` | ftrace/tracepoint/kprobe/eBPF/ETW tanım ve ilke seti |
| 3 | Kademe-4 | `trace-surucu.md` | osnoise/histogram/perf/ETW oturum sürme ve hata modları |
| 4 | Kademe-4 | `trace-ornekler.md` | repo disk kanıtları (traceId), olumsuz kanıt, K017 sınırı |

## §3 Envanter

| # | Dosya | Kapsadığı kavramlar |
|---|---|---|
| 1 | `trace-anayasa.md` | trace türleri · tracefs/ftrace · tracepoint · kprobe/uprobe · eBPF (verifier, harita, ring buffer) · ETW oturumu · platform karşılaştırma · macOS ⚠️ |
| 2 | `trace-surucu.md` | osnoise tracer · histogram trigger · perf record/script · sembolizasyon · overhead/taşma · ETW oturum mühendisliği · olumsuz kanıt |
| 3 | `trace-ornekler.md` | traceId repo kanıtları (PageRouterKernel, StructuredLogger, FileHandler, ResponseEmitter) · grep 0 isabet · ci.yml · traceId ≠ OS trace ayrımı |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | alt-dal künyesi | Kademe-3 |
| mimari | `trace-anayasa.md` | bağlayıcı tanım ve ilke seti | Kademe-4 |
| mimari | `trace-surucu.md` | mekanizma envanteri ve hata modları | Kademe-4 |
| kod | `trace-ornekler.md` | repo örnekleri ve olumsuz kanıt | Kademe-4 |
| mimari | **OS grupları**: `trace-anayasa.md` §6.1/§8 · `trace-surucu.md` §6.1/§10 · `trace-ornekler.md` §13/§13.1 | Linux/Windows/macOS (ETW T-W16–18 · DTrace ayna T-W19–22) haritası — kanıt T-W16–T-W23 | Kademe-4 |

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| tracepoint tanımı (çekirdek kaynağı) | `isletim-sistemi-kernel` | yakalama ve olay kaydı |
| event üretimi/sıra | `isletim-sistemi-plan` | event yakalama (kprobe/eBPF) |
| /proc /sys, süreç/bellek | `isletim-sistemi-core` | ölçüm yöntemi |
| OS eşik/log kesişimi (softlockup izi) | `log` alt-dalı | yalnız trace tarafı |
| sayaç üretimi (PMU/perf_event) | `metrik` alt-dalı | yalnız olay/örnekleme tarafı |
| span/OTel/APM, alert | `K016-observability` | yalnız sınır tarifi |

## §6 depends_on

`depends_on: [isletim-sistemi-kernel, isletim-sistemi-core]` — tracepoint ve
prob'ların kaynağı kernel'e, olayların üretildiği süreç/bellek bağlamı core'a
aittir; bu dal yalnız yakalama, kayıt ve okuma kadarını taşır (kök `index.md`).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS trace toplama (ftrace, ETW oturumu, eBPF) hedef mimaride var mı? | UNKNOWN |
| O2 | Gözlemlenebilirlik üçlüsü K002-altyapi içinde nasıl konumlanacak? | UNKNOWN |
| O3 | Dağıtık trace (OTel) hedef mimarida mı, yalnız traceId correlation mı? | UNKNOWN |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
