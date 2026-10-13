---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan
children:
  - index.md
  - zamanlama-scheduler-anayasa.md
  - zamanlama-scheduler-surucu.md
  - zamanlama-scheduler-ornekler.md
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "https://docs.kernel.org/scheduler/sched-capacity.html"
  - "https://docs.kernel.org/scheduler/sched-stats.html"
  - "https://docs.kernel.org/scheduler/sched-ext.html"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html"
  - "https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html"
validation:
  - "2026-10-13: 8 URL HTTP 200 · envanter kesişimi 0"
  - "2026-10-13: 3 içerik dosyası ≥500 satır"
open_questions:
  - "Plan1: scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# zamanlama-scheduler

> **Eksen sorusu:** hangi iş, hangi sırayla CPU alır?

## §1 — Dosya indeksi

| Dosya | Bölüm dizisi | Ne verir |
|---|---|---|
| [`zamanlama-scheduler-anayasa.md`](zamanlama-scheduler-anayasa.md) | §0–§9 | ELI15, zamanlamanın amacı, politika ailesi özeti, komşu sınırı, terimler, kapsam dışı, açık sorular, Raspberry Pi 5 notu, kaynakça |
| [`zamanlama-scheduler-surucu.md`](zamanlama-scheduler-surucu.md) | §0–§10 | round-robin · CFS · priority · EDF · preemptif/işbirlikçi · quantum/tick · SCHED_FIFO/RR/DEADLINE · starvation/fairness · ölçüm · repo kanıtları · riskler · kaynakça |
| [`zamanlama-scheduler-ornekler.md`](zamanlama-scheduler-ornekler.md) | §0–§6 | gerçek repo kodu, ölçüm örnekleri, kötü uygulama kataloğu, politika karşılaştırma matrisi, repo'da olmayan dürüstlük kaydı |

## §2 — Komşu sınırı matrisi

| Komşu | Bu alt-dalın ona bıraktığı | Almadığı | Ayırıcı çizgi |
|---|---|---|---|
| `isletim-sistemi-core` | süreç/kavram çerçevesi | süreç mekanizması tanımı | ortak tip kimde tanımlı (core) |
| `isletim-sistemi-kernel` | politika çerçevesi ve ölçütler | kuyruk/wakeup iç mekanizması, kesme, lock | ne **nasıl** çalışır (kernel) vs hangi **kural** (plan) |
| `isletim-sistemi-api` | uygulama katmanı kuyruk/öncelik örneğinin OS olmadığının notu | kullanıcı parçacığı API tasarımı | örnek (plan) vs API (api) |
| `isletim-sistemi-security` | zamanlama politikası güvenliği **ihtiyacının** tanımı | capability/rol/ACL modeli | çerçeve (plan) vs model (security) |
| `isletim-sistemi-observability` | context switch latency ve jitter ölçümünün **adı** | metrik boru hattı, log şeması | okuma (plan) vs toplama (observability) |
| `isletim-sistemi-testing` | ölçüm eşikleri ve doğrulama senaryosu | test kodu, CI kapısı | strateji (plan) vs uygulama (testing) |
| `isletim-sistemi-audio` | RT zamanlama sınıfının ses için anlamı (WASAPI/ASIO deadline) | zincir/ASIO/ring buffer tasarımı | sınıf (plan) vs zincir (audio) |
| `isletim-sistemi-turu` | OS zamanlayıcı farklarının varlığının işaret edilmesi | kurulum yolu, dağıtım yapılandırması | politika (plan) vs kurulum (turu) |

## §3 — `group` dağılımı

| Dosya | `group` | `node_type` | `status` |
|---|---|---|---|
| `index.md` | `mimari` | `subdomain` | `draft` |
| `zamanlama-scheduler-anayasa.md` | `mimari` | `module` | `draft` |
| `zamanlama-scheduler-surucu.md` | `mimari` | `module` | `draft` |
| `zamanlama-scheduler-ornekler.md` | `kod` | `module` | `draft` |

## §4 — Açık soru

| # | Soru | Durum | Kapanış koşulu |
|---|---|---|---|
| Plan1 | Zamanlama içeriği `kernel`/`runtime` içinde mi, burada mı? | **[UNKNOWN]** | komşu dosyaların okunup sınırın teyidi — `zamanlama-scheduler-anayasa.md §7` |
| Q3 | Kapsam = OS planlama mı, uygulama roadmap mi? | **[UNKNOWN]** | üst otorite kararı — `isletim-sistemi-plan-kapsam.md §C.4` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode