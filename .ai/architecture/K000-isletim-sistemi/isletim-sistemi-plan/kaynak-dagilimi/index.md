---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan
children:
  - K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-anayasa
  - K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-surucu
  - K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-ornekler
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - "https://docs.kernel.org/admin-guide/cgroup-v2.html"
  - "https://man7.org/linux/man-pages/man2/getrlimit.2.html"
  - "shared/src/Security/CacheRateLimiter.php:16"
  - "shared/src/Api/Middleware/RateLimitMiddleware.php:22"
  - "shared/src/Cache/PageCacheAdapter.php:11"
validation:
  - "2026-10-13: 15 URL HTTP 200 (8 yeni + 7 kardeş-teyidi) · envanter kesişimi 0"
  - "2026-10-13: 4 alt-alan .htaccess/web.config = 8 dosya sayıldı"
open_questions:
  - "Q3 — kapsam OS kaynak dağıtımı mı, uygulama kotası mı? [UNKNOWN]"
  - "Plan1 — scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# kaynak-dagilimi

> **Eksen sorusu:** kaynak kime, ne kadar, ne zaman verilir?

## §0 ELI15

İşletim sistemi kaynakları **sınırlıdır** ve bir planlayıcı tarafından dağıtılır:
CPU çekirdeği/öncelik (nice, cgroup), bellek kotası (NUMA atribütleri, cgroup
memory), disk G/Ç kuyrukları ve RLIMIT. Bu alt-dal "kaynak kime, ne kadar, ne
zaman verilir?" sorusunun **kural yüzeyini** taşır — mekanizmanın *nasıl*
çalıştığı kernel dalının, uygulamadaki yansıması runtime dalının konusudur.

## §1 — Dosya indeksi

| Dosya | Bölüm dizisi | Ne verir |
|---|---|---|
| [`kaynak-dagilimi-anayasa.md`](kaynak-dagilimi-anayasa.md) | §0–§9 | ELI15, CPU çekirdeği/paylaşım, bellek atribütleri (NUMA · cgroup), G/Ç kuyrukları, öncelik devralma, ölçüm, komşu sınırı, açık sorular, kaynakça |
| [`kaynak-dagilimi-surucu.md`](kaynak-dagilimi-surucu.md) | §0–§7 | cgroup kota ayrıntısı, NUMA atribüt/yerleşim, IO kuyruğu disiplinleri, priority inheritance zinciri, ölçüm, repo disk kanıtları, kaynakça |
| [`kaynak-dagilimi-ornekler.md`](kaynak-dagilimi-ornekler.md) | §0–§6 | gerçek repo kodu (cache TTL · rate limit · retryAfter), kota örneği, kötü uygulama kataloğu, aşım davranışı matrisi, dürüstlük defteri |

## §2 — Komşu sınırı matrisi

| Komşu | Bu alt-dalın ona bıraktığı | Almadığı | Ayırıcı çizgi |
|---|---|---|---|
| `isletim-sistemi-kernel` | kota ve öncelik **kuralı** | kuyruk iç mekanizması, wakeup, kesme | kural (plan) vs **nasıl** (kernel) |
| `isletim-sistemi-core` | süreç/kavram çerçevesinin kaynak okuması | süreç mekanizması tanımı | ortak tip kimde tanımlı (core) |
| `isletim-sistemi-runtime` | ölçüm adının ve eşiğinin tanımı | çalışma zamanı kaynak izleme boru hattı | okuma (plan) vs toplama (runtime) |
| `zamanlama-scheduler` | kota, politika **girdisidir** | hangi işin sırayla CPU alacağı | ne **kadar** (burası) vs hangi **sıra** (zamanlama) |
| `is-parcacigi-planlamasi` | kota, iş **birimi başına** tanımlanır | parçacık durum makinesi | kota (burası) vs birim (parçacık) |
| `isletim-sistemi-security` | limit/inflate ihtiyacının tanımı | capability/rol/ACL modeli | kota (plan) vs yetki (security) |
| `isletim-sistemi-observability` | utilization/run queue ölçümünün **adı** | metrik toplama, log şeması | okuma (plan) vs toplama (observability) |
| `isletim-sistemi-api` | uygulama katmanı kota örneğinin OS katmanı olmadığının notu | API tasarımı, rate-limit şeması | örnek (plan) vs sözleşme (api) |

> Çıkış: `depends_on` **2 alt-dal**; komşu sınırı **8 satır**; ters bağımlılık yok.

## §3 — `group` dağılımı

| Dosya | `group` | `node_type` | `status` |
|---|---|---|---|
| `index.md` | `mimari` | `subdomain` | `draft` |
| `kaynak-dagilimi-anayasa.md` | `mimari` | `module` | `draft` |
| `kaynak-dagilimi-surucu.md` | `mimari` | `module` | `draft` |
| `kaynak-dagilimi-ornekler.md` | `kod` | `module` | `draft` |

## §4 — Ölçüm / envanter (disk · 2026-10-13)

| Sayım | Değer | Yöntem |
|---|---|---|
| Alt-dal dosyası | 4 (1 index + 3 içerik) | disk enumerasyonu |
| Çift yazılım dosyası (alt-alan × 4) | 8 (`.htaccess` × 4 + `web.config` × 4) | `Get-ChildItem -Include` |
| CI koşu ortamı | tek platform — `ubuntu-latest` (6 job) | `ci.yml` `runs-on` sayımı |
| OS düzeyi kota API'si (repo) | 0 eşleşme | `pcntl_|pthread_|sched_|affinity` grep → 0 |

## §5 — Açık sorular

| # | Soru | Durum | Kapanış koşulu |
|---|---|---|---|
| Q3 | Kapsam = OS kaynak dağıtımı mı, uygulama kotası mı? | **[UNKNOWN]** | üst otorite kararı — `isletim-sistemi-plan-kapsam.md §C.4` |
| Plan1 | Scheduling içeriği `kernel`/`runtime` içinde mi, burada mı? | **[UNKNOWN]** | komşu dosyaların okunup sınırın teyidi — `zamanlama-scheduler-anayasa.md §7` |
| Q-kd-1 | cgroup/NUMA kotası üretimde uygulanıyor mu? | **⚠️ VERIFICATION REQUIRED** | `kaynak-dagilimi-ornekler.md §5` dürüstlük defteri |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
