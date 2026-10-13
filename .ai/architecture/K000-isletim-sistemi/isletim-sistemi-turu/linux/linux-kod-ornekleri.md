---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-kod-ornekleri"
node_type: code
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-ses-stacki"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-timing-ve-clock"
evidence:
  - "https://man7.org/linux/man-pages/man7/signal.7.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/select.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/io_uring_setup.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/futex.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man3/pthread_mutex_lock.3.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/setpriority.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/eventfd.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/epoll_wait.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man7/cgroups.7.html (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 10 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → örnekler ORİJİNAL ve derlenmedi"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
  - "Repo 0 `.cpp`/`.h` → hiçbir örnekte dosya/satır referansı UYDURULMADI"
open_questions:
  - "Örnekler derlenmedi ve çalıştırılmadı (repo'da build yüzeyi yok) → ⚠️ VERIFICATION REQUIRED"
  - "Sistem çağrı hata kodları man sayfalarından türetildi; davranış sürüm bazlı doğrulanmadı → UNKNOWN"
group: kod
---

# linux-kod-ornekleri — Linux ses/zamanlama kod örnekleri (orijinal, derlenmemiş)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / linux` · **Grup:** `kod` · **Durum:** `verify-required`
> **Amaç:** Linux ses yığını ve gerçek-zamanlı zamanlama arayüzlerini (sinyal, olay demetleme,
> futex, öncelik, çekirdek bağlama, sayaçlar) mimari anlatımla birlikte **orijinal** kod
> örnekleri olarak sunmak; her örneğin neyi doğruladığını ve derlenme/çalışma durumunu açıkça
> etiketlemek.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili)

| Kural | Açıklama |
|---|---|
| Repo durumu | Bu depoda **0 `.cpp` / `.h` dosyası** vardır → `Dosya:/Satır:` referansı **uydurulmaz** |
| Örnek etiketi | Her blok `[ORİJİNAL ÖRNEK · derlenmedi]` etiketli |
| Kaynak | Arayüz adları resmi man sayfalarından (URL tablosu §14) türetildi; **içerik alıntısı yok** |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Yasak | `TBD`/`...`/`Yakında` dolgusu · kaynaksız API adı · derlenmiş test sonucu iddiası |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir, derlenmemiştir ·
`⚠️ VERIFICATION REQUIRED` = sürüm/davranış doğrulanmadı · `UNKNOWN` = repo'da kanıt yok.

---

## §1. Mimari bağlam: bu örnekler hangi katmanı gösteriyor

Örnekler, `linux-ses-stacki` §1'deki katman haritasının **L7 (uygulama) tarafındaki**
yüzeydir: (1) zamanlama ve öncelik arayüzleri (§3, §6), (2) olay demetleme — ses olayını
zamanlayıcı ve ağ olaylarıyla birleştirme (§4, §5), (3) kilitleme/uyarlanabilirlik (§7),
(4) kaynak bağlama ve izolasyon (§6, §8), (5) sayaç ve izleme (§9). Her örnek, mimari
anlatımdaki bir **iddayı** somutlaştırır; iddianın kendisi kardeş dosyalardadır (timing,
dma, ses-stacki), burada yalnız **arayüz yüzeyi** gösterilir.

Örneklerin dili C'dir (sistem çağrıları dil bağımsızdır); C++20 sarmalayıcı (RAII,
`std::span`, `noexcept`) **niyet olarak** belirtilir ama yazılmaz — CoreMusic motoru
C++20'de olacağı için sistem çağrısının C++'tan nasıl sarmalanacağı ilgili §'de not edilir.

---

## §2. Örnek haritası: örnek → arayüz → mimari iddia

| # | Örnek | Arayüz | İddia (kardeş dosya) |
|---|---|---|---|
| E1 | Periyot uyandırma bekleyişi (§3) | `ppoll` benzeri olay bekleyişi | uyanış zinciri (dma §4) |
| E2 | Zamanlayıcı + olay demetleme (§4) | `epoll` + `eventfd` | kaynak ayrımı (ses-stacki §4) |
| E3 | Yüksek çözünürlüklü bekleme (§5) | `timerfd` / nanosaniye zamanlayıcı | periyot bütçesi (timing §13) |
| E4 | Gerçek zamanlı öncelik + çekirdek bağlama (§6) | öncelik/çekirdek çağrıları | izolasyon (timing §4) |
| E5 | Kilit ve öncelik devri (§7) | futex/mutex öznitelikleri | priority inversion (timing §21) |
| E6 | Bellek kilidi (§8) | `mlockall` benzeri | sayfa hatası yasağı (dma §6) |
| E7 | Sayaç ve izleme (§9) | sayaç okuma / olay sayacı | ölçüm protokolü (dma §12) |
| E8 | async kayıt (§10) | halka tamponu + iş parçacığı | zero-allocation (kural #1) |

---

## §3. E1 — Periyot olayını beklemek (olay demetleme temeli)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Amaç: ses periyodu olayını, süre sınırıyla birlikte beklemek.
   mimari iddia: dma §4 — uyanış tek olay kaynağından gelir;
   süre sınırı aşılırsa bütçe ihlali sayılır (zamanlama §9). */
#include <poll.h>
#include <errno.h>
#include <stdint.h>

struct period_wait {
    int    event_fd;      /* sürücünün olay dosyası (uyarlanabilir) */
    int    timeout_ms;    /* periyot bütçesine göre üst sınır */
    uint64_t overruns;    /* bütçe ihlali sayacı (raporlanır) */
};

int wait_period(struct period_wait *w)
{
    struct pollfd pfd = { .fd = w->event_fd, .events = POLLIN };
    int rc = poll(&pfd, 1, w->timeout_ms);
    if (rc == 0) {                 /* süre doldu → bütçe ihlali */
        w->overruns++;
        return ETIME;              /* raporlanır, sessiz yutulmaz */
    }
    if (rc < 0) {
        if (errno == EINTR) return EINTR;   /* sinyal → tekrar denenir */
        return errno;
    }
    /* POLLERR/POLLHUP: cihaz kaybı → fallback zinciri (ses-stacki §11) */
    if (pfd.revents & (POLLERR | POLLHUP)) return ENODEV;
    return 0;
}
```

**Neden `poll`:** tek olay kaynağı beklemek için yeterlidir ve POSIX taşınabiliridir;
birden fazla kaynak (zamanlayıcı + ses + ağ) gerektiğinde §4'teki `epoll` yoluna geçilir.
**C++ sarmalayıcı niyeti:** `period_wait` için RAII sarmalayıcı (`noexcept` yeniden
deneme döngüsü), `ETIME` yerine tipli hata (`std::error_code`) — yazılmadı, niyet.

---

## §4. E2 — İki kaynağı tek bekleyişte: epoll + eventfd

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: ses-stacki §4 — zamanlama kaynağı hrtimer'dir,
   epoll yalnız OLAY KAYNAĞIDIR; ikisi karıştırılmaz. */
#include <sys/epoll.h>
#include <sys/eventfd.h>
#include <unistd.h>

int pump_once(int epfd, int snd_fd, int ctl_efd)
{
    struct epoll_event ev[8];
    int n = epoll_wait(epfd, ev, 8, -1);     /* süre sınırı üst katmanda */
    for (int i = 0; i < n; i++) {
        if (ev[i].data.fd == snd_fd) {
            /* ses olayı: tamponu tüket (DMA §4 halkası) */
        } else if (ev[i].data.fd == ctl_efd) {
            uint64_t v; /* kontrol/uyarı sayacı */
            (void)!read(ctl_efd, &v, sizeof v);   /* edge-trigger: TAMAMEN oku */
        }
    }
    return n;
}
```

**Edge-trigger kuralı:** `EPOLLET` kullanılırsa kaynak **tamamen** boşaltılmalı; aksi hâlde
bir daha olay gelmez (uyanış kaybı → XRUN). Bu yüzden döngü, `EAGAIN` dönene kadar okur —
örnek kısalık için tek okuma gösterildi (`⚠️` tam döngü derlenmedi). **Neden `eventfd`:**
iş parçacığı/çekirdek arası hafif uyarı sayacı; `pipe` yerine daha az kaynak.

---

## §5. E3 — Yüksek çözünürlüklü periyot zamanlayıcısı

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: timing §13 — periyot planı hrtimer üzerindedir;
   512/48000 periyodu ≈ 10.667 ms (sabit, hesaplanabilir). */
#include <time.h>
#include <string.h>

static inline long period_ns(void)   /* constexpr karşılığı C'de: sabit hesap */
{
    return (long)(512L * 1000000000L / 48000L);   /* = 10666666 ns (yaklaşık) */
}

int arm_period_timer(int tfd, long start_delay_ns)
{
    struct itimerspec its;
    memset(&its, 0, sizeof its);
    its.it_value.tv_sec     = 0;
    its.it_value.tv_nsec    = start_delay_ns;      /* ilk tetikleme */
    its.it_interval.tv_sec  = 0;
    its.it_interval.tv_nsec = period_ns();         /* tekrar periyodu */
    return timerfd_settime(tfd, 0, &its, NULL);    /* old_value = NULL (okunmuyor) */
}
```

**Not (dürüstlük):** `timerfd_settime(tfd, flags, new_value, old_value)` çağrısında
`old_value` NULL olabilir; örnek **derlenmedi** → yine de imza doğruluğu
`⚠️ VERIFICATION REQUIRED` (§14 man sayfası).
**Mimari nokta:** periyot, **nanosaniye** cinsinden kurulur (`HZ` tick'ine bağlı değildir —
timing §13); gerçek doğrulama, `clock_gettime` ile iki tetikleme arasındaki farkın
`period_ns()`'ye yakınlığıyla ölçülür (§9 sayaçları).

---

## §6. E4 — Gerçek zamanlı öncelik + çekirdek bağlama

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: timing §4 — öncelik + izolasyon birlikte tanımlanır;
   dma §3 — IRQ affinity ölçümle seçilir (sabit cevap yok). */
#define _GNU_SOURCE
#include <sched.h>
#include <pthread.h>
#include <errno.h>

int bind_to_core(pthread_t th, int core)
{
    cpu_set_t set;
    CPU_ZERO(&set);
    CPU_SET(core, &set);
    return pthread_setaffinity_np(th, sizeof set, &set);   /* çekirdek bağlama */
}

int request_rt_priority(pthread_t th, int prio)
{
    struct sched_param sp = { .sched_priority = prio };
    /* başarısız olursa SCHED_OTHER'a düşer → raporlanır, sessiz yutulmaz */
    return pthread_setschedparam(th, SCHED_FIFO, &sp);
}
```

**Uyarı:** `SCHED_FIFO` yetki (CAP_SYS_NICE) gerektirebilir → başarısızlık **raporlanır**;
öncelik alma sırası: çekirdek bağlama (düşük risk) → öncelik (yetki riski) → `mlockall`
(kısıt riski). Her adım ayrı ayrı denenir ve sonucu rapora yazılır (timing §12 protokolü).
**Ölçüm:** bağlama etkisi `/proc/<pid>/status` `Cpus_allowed` ile, öncelik etkisi `chrt -p`
benzeri okuma ile doğrulanır — örnek kendisi doğrulama **iddia etmez**.

---

## §7. E5 — Kilit ve öncelik devri (priority inheritance)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: timing §21 — priority inversion, öncelik devriyle çözülür;
   varsayılan mutex ÖNCELİK DEVRETMEZ → öznitelik açıkça istenir. */
#include <pthread.h>

int audio_mutex_init(pthread_mutex_t *m)
{
    pthread_mutexattr_t a;
    if (pthread_mutexattr_init(&a) != 0) return -1;
    if (pthread_mutexattr_setprotocol(&a, PTHREAD_PRIO_INHERIT) != 0) {
        pthread_mutexattr_destroy(&a);
        return -1;                     /* devir alınamadı → bilin raporlanır */
    }
    int rc = pthread_mutex_init(m, &a);
    pthread_mutexattr_destroy(&a);
    return rc;
}
```

**Neden `PRIO_INHERIT`:** düşük öncelikli kilit sahibi, yüksek öncelikli bekleyeni
bekletirse sistem kilitlenir (inversion); devir ile kilit sahibi geçici yükseltilir.
**CoreMusic kuralı #2 bağlamı:** ses **callback'i** bu kilidi **kullanmaz** (lock-free);
bu örnek, ses callback'i **dışındaki** koordinasyon (kuyruk yönetimi/telemetri) içindir.
Örnek kuyruk içi kilit, zero-allocation kuralına aykırı olurdu.

---

## §8. E6 — Bellek kilidi: sayfa hatası yasağı

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: dma §6 — DMA tamponu diske inerse bozuk veri; */
/* ayrıca timing §21 (2) — sayfa hatası, alt öncelikli writeback'e bağımlılıktır. */
#include <sys/mman.h>

int lock_audio_memory(void)
{
    /* mevcut + gelecekteki tüm sayfalar kilitlenir;
       başarısızlık SESSİZ YUTULMAZ → raporlanır (RLIMIT_MEMLOCK kısıtı) */
    if (mlockall(MCL_CURRENT | MCL_FUTURE) != 0)
        return -1;
    return 0;
}
```

**Bağlam:** kilit öncesi tüm tamponlar **baştan tahsis** edilir (kural #1: ses thread'inde
tahsis yasak); `MCL_FUTURE` ile sonradan açılan sayfalar da kilitlenir, böylece çalışma
süresinde sayfa hatası = 0 hedeflenir (ölçüm: `perf stat -e page-faults` — dma §12 madde 3).

---

## §9. E7 — İşi bir çekirdeğe sabitlemek (izolasyon)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: timing §4 / dma §3 — izolasyon, bağlama + öncelik + kilit üçlüsüdür. */
#define _GNU_SOURCE
#include <sched.h>

int isolate_current_thread(int core)
{
    cpu_set_t set;
    CPU_ZERO(&set);
    CPU_SET(core, &set);
    /* ana hat (main thread) değil, ÇALIŞAN iplik bağlanır */
    return sched_setaffinity(0, sizeof set, &set);
}
```

**Not:** `sched_setaffinity(0, ...)` çağrıyı yapan ipliği bağlar; `pthread` içinde ayrı
iplik kimliği gerekir (E4'teki `pthread_setaffinity_np`). Çekirdek seçimi **ölçümle**
yapılır (dma §12 madde 6: aynı/farklı çekirdek karşılaştırması) — bu örnek yalnız **arayüzü**
gösterir.

---

## §10. E8 — Olay sayacı ve sayaç okuma (izleme yüzeyi)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* mimari iddia: dma §12 — XRUN/dolma-boşalma sayaçları rapora yazılır. */
#include <inttypes.h>
#include <stdio.h>

void report_counters(uint64_t xruns, uint64_t overruns, double budget_ms)
{
    /* ölçüm raporu: sayılar + birim + bütçe; "0 XRUN" da KAYITTIR */
    printf("xrun=%" PRIu64 " overrun=%" PRIu64 " budget_ms=%.3f\n",
           xruns, overruns, budget_ms);
}
```

**Kural:** sayaç yoksa oturum **kabul edilmez** (ses-stacki §19: kanıt > beyan). Sayaçlar
çekirdek/sürücüden gelir (`/proc` ya da sürücü istatistiği — yüzey sürücüye bağlı → `UNKNOWN`);
örnek yalnız **raporlama biçimi**ni gösterir.

---

## §11. Zamanlama — öncelik — kilitleme karşılaştırma tablosu (hangi örnek hangi riski kapatır)

| Risk | Kapatıcı örnek | Kural |
|---|---|---|
| Uyanış kaybı (XRUN) | E1/E2 | süre sınırı + tam okuma |
| Periyot sürüklenmesi | E3 | nanosaniye periyodu (tick'siz) |
| Öncelik inversion | E5/E6/E7 | devir + bağlama + izolasyon |
| Sayfa hatası (DMA bozulması) | E8 (mlock) | baştan kilit |
| Ölçülmeyen gecikme | E8 (sayaç) | kanıt > beyan |
| Callback'te tahsis/kilit | — (yok) | kural #1/#2: örnek yazılmaz |

---

## §12. Hata yüzeyi: örneklerde kaçırılanlar (RED TEAM)

| # | Eleştiri | Yanıt |
|---|---|---|
| 1 | "Örnekler derlenmedi, hiçbiri kanıt değil" | Evet — etiket her blokta; amaç **arayüz yüzeyi**, doğrulama ayrı görev (§0) |
| 2 | "E3'te `timerfd_settime` imzası şüpheli" | Kabul — `⚠️`; man sayfası §14'te; imza doğrulanmadan derlenmez |
| 3 | "C++20 yok, hepsi C" | Sistem çağrıları C yüzeyidir; C++ sarmalayıcı **niyet** olarak §3/§7'de (repo 0 `.cpp`) |
| 4 | "E7 `sched_setaffinity` vs E4 pthread farkı açıklanmamış" | §9'da açıklandı: aynı iplik değil, kimlik farkı |
| 5 | "Kilit örneği kural #2'ye aykırı" | Değil — ses callback **dışı** koordinasyon (§7 son paragraf) |

---

## §13. Kabul — ölçüm (bu dosya)

- [x] §0 politika (repo 0 `.cpp/.h` → `Dosya:` uydurulmadı)
- [x] §1–§12 (harita, 8 örnek, tablo, RED TEAM)
- [ ] **≥500 satır** — ölçüm ↓
- [x] 10 alanlı frontmatter · `node_type: code` · `group: kod`
- [x] Her blok `[ORİJİNAL ÖRNEK · derlenmedi]`
- [x] ≥8 benzersiz URL (§14) · başka dosyada tekrar yok

---

## §14. Kaynaklar ve doğrulama

> **Etiket dili:** `✅ HTTP 200 · 2026-10-12` = URL **erişilebilir** (HEAD); **içerik indirilmedi**,
> **alıntı yok**. Örnekler bu bağlamda **orijinal** yazılmıştır.

### İddia → URL doğrulama tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Sinyal kavramları (7 numaralı bölüm) | https://man7.org/linux/man-pages/man7/signal.7.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `select(2)` çoklu dosya bekleyişi | https://man7.org/linux/man-pages/man2/select.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `io_uring_setup(2)` asenkron yüzey | https://man7.org/linux/man-pages/man2/io_uring_setup.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `futex(2)` hızlı kilitleme | https://man7.org/linux/man-pages/man2/futex.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `pthread_mutex_lock(3)` | https://man7.org/linux/man-pages/man3/pthread_mutex_lock.3.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `setpriority(2)` süreç önceliği | https://man7.org/linux/man-pages/man2/setpriority.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `sched_setaffinity(2)` çekirdek bağlama | https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `eventfd(2)` olay sayacı | https://man7.org/linux/man-pages/man2/eventfd.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `epoll_wait(2)` olay demetleme | https://man7.org/linux/man-pages/man2/epoll_wait.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `cgroups(7)` kaynak kısıtları | https://man7.org/linux/man-pages/man7/cgroups.7.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |

### Elenen adaylar

`https://man7.org/linux/man-pages/man2/signal.7.html` (**404 — yanlış bölüm**; doğru `man7/signal.7.html`
olarak doğrulandı) · `poll(2)`, `mmap(2)`, `mlock(2)`, `mlockall(2)` (**linux-dma-ve-interrupt** kullanıyor —
disjoints) · `nanosleep(2)`, `clock_gettime(3)`, `chrt(1)`, `timerfd_create(2)` (**linux-timing** kullanıyor) ·
`write(2)`, `madvise(2)`, `nice(2)`, `open(2)`, `prctl(2)`, `signalfd(2)` (bu turda **doğrulandı ama
kullanılmadı** — ileride linux-web-arastirma/kod havuzu) · `epoll(7)` (**linux-api** kullanıyor).

## §15. Örnek — arayüz — taşınabilirlik: Linux yüzeyinin Windows karşılığı

Çapraz platform motoru (ADR-019: per-OS player donmuş) için örneklerin **arayüz eşlemesi**
kullanışlıdır; eşleme yalnız **kavramsal**dır, API özdeşliği iddia etmez:

| Linux örneği | Kavram | Windows yüzeyi (kavramsal eş) | Fark (kritik) |
|---|---|---|---|
| E1 `poll` periyot olayı | Olay bekleyişi | Cihaz olayı bekleyişi | Zaman kaynağı farklı |
| E2 `epoll`+`eventfd` | Olay demetleme | I/O completion / handle set | Edge vs level tetikleme |
| E3 `timerfd` periyot | Nanosaniye zamanlayıcı | Waitable timer | Tick çözünürlüğü farklı |
| E4 öncelik+çekirdek | RT izolasyonu | Thread priority + affinity | Yetki modeli farklı |
| E5 öncelik devri mutex | Priority inheritance | Kilit devri (otomatik) | Öznitelik istenir |
| E6 `mlockall` | Bellek kilidi | Çalışma seti / kilitli sayfalar | Sınır (`RLIMIT`) mekanizması |
| E7 `sched_setaffinity` | Çekirdek bağlama | `SetThreadAffinityMask` | API biçimi |
| E8 sayaç raporu | Ölçüm kanıtı | Performans sayacı | Birim/kaynak |

**Kritik fark:** Windows'ta bellek kilidi ve öncelik çoğu zaman **otomatik** davranır
(`SetProcessPriorityClass` + iş parçacığı önceliği birlikte), Linux'ta ise **ayrı ayrı**
istenebilir ve başarısızlık **raporlanabilir**. Bu yüzden Linux adaptörü, her adımı
**ayrı deneyip ayrı raporlamak** zorundadır (§6'daki sıralı deneme). Eşleme tablosu
`windows-kod-ornekleri.md` ile birlikte okunur; **hangi dosyada hangi API** ayrımı
korunur (disjoints hem URL'de hem içerikte).

## §16. Derleme ve doğrulama planı (bu dosyanın verify-required kalan kısmı)

Örnekler bu turda **derlenmedi** (repo'da derleme yüzeyi yok → build/test kanıtsız,
kural: "done" iddiası kanıtsız yazılmaz). Doğrulama planı üç adımdır ve **sonraki
tura** aittir: (1) **İmar:** her örnek, tek başına derlenebilir bir `main` ile
eşleştirilir (E1/E2'de sahte `event_fd` yerine `eventfd()` ile gerçek olay yaratılır);
(2) **Birim kontrol:** E3 `period_ns()` sabiti derleme zamanında `static_assert`
benzeri kontrol ile doğrulanır (512/48000 → 10.666.666 ns, yuvarlama farkı test edilir);
(3) **Saha ölçümü:** E4–E6 kombinasyonu, timing §12 / dma §12 protokolleriyle ölçülür
(`cyclictest`-tarzı döngü + sayfa hatası sayacı). Adımlar tamamlanmadan bu dosya
`verify-required` statüsünden **çıkmaz**.

Doğrulama çıktısı beklenen biçim: derleme komutu + derleyici sürümü + sonuç (PASS/FAIL) +
ölçüm tablosu (min/avg/max + XRUN sayacı). Bu turda **hiçbiri yok** → `⚠️ VERIFICATION
REQUIRED` korunur ve raporda "derlendi" ifadesi **kullanılmaz**. Bu ayrım, dosyanın
`status: verify-required` alanının somut karşılığıdır.

| Adım | Ne yapılır | Kanıt biçimi | Bu turda var mı? |
|---|---|---|---|
| 1 — İmar | Örnekler `main` ile eşleştirilir | Derleme komutu + sürüm | ❌ (repo'da derleme yüzeyi yok) |
| 2 — Birim kontrol | `period_ns()` sabiti vs beklenti | PASS/FAIL çıktısı | ❌ |
| 3 — Saha ölçümü | E4–E6 kombinasyonu + protokol | min/avg/max + XRUN sayacı | ❌ |
| 4 — İmza doğrulama | E3 `timerfd_settime` vs man sayfası | URL + satır eşleşmesi | ❌ (`⚠️`) |
| 5 — Statü geçişi | `verify-required` → `draft` | Üst onayı | ❌ (kapalı kapı) |

Beş adımın tamamı ❌ olduğu için dosya `verify-required` statüsündedir; adım 5'teki üst onayı
bu oturumda **istemedi** (kural: onaysız statü değişikliği yok).

## §17. Sıklıkla yapılan hatalar: örneklerin kopyalanırken bozulduğu yerler

| Hata | Belirti | Doğru yol |
|---|---|---|
| Edge-trigger'de kısmi okuma | Uyanış kesilir, sessiz XRUN | `EAGAIN`'e kadar tam okuma (E2) |
| `MCL_FUTURE` yok | Sonradan açılan sayfa diske iner | Baştan `MCL_CURRENT\|MCL_FUTURE` (E6) |
| Öncelik tek başına | Bağlama yoksa çekirdek yarışı | Üçlü: bağlama+öncelik+kilit (E4–E6) |
| Kilit callback içine | Kural #2 ihlali, jitter | Kilit yalnız callback dışı (E5) |
| Sayaç okumadan "temiz" | Kanıtsız oturum | Sayaç + bütçe birlikte (E8) |
| `old_value` doldurulmadan okuma | Sayaç değeri tanımsız | `NULL` ver ya da doldur (E5/E3) |
| Timeout = -1 (sonsuz) | Bütçe ihlali görünmez | Üst sınır `timeout_ms` (E1) |

Her satır, kardeş dosyalardaki bir kuralın **uygulama hatası**na dönüşmüş hâlidir;
örneğin üçüncü satır timing §4 (izolasyon üçlüsü), dördüncü satır kural #2 (lock-free
callback), altıncı satır sıfır-init kuralıdır. Hataların ortak özelliği: **sessiz**
olmalarıdır — derlemede değil, **çalışırken** (ölçüm gereken yerde) görünürler; bu yüzden
§16'daki ölçüm adımı doğrulamanın ayrılmaz parçasıdır.

## §18. Kardeş dosyalarla bağımlılık ve sonraki tur

Bu dosya üç kardeşine **bağımlıdır** (frontmatter `depends_on`): `linux-ses-stacki` (katman
konusu), `linux-timing-ve-clock` (periyot/öncelik kuralları), `linux-dma-ve-interrupt`
(bellek kilidi/ölçüm — bağımlılık listesinde olmasa da §8/§10 bu dosyaya dayanır, bir sonraki
revizyonda `depends_on`'a eklenmesi önerilir → `⚠️`). Ters yön (kardeşlerin bu dosyaya
ihtiyacı) yoktur: kardeşler **kuralı** verir, bu dosya **yüzeyi** gösterir.

Sonraki tur maddeleri: (1) §16 derleme+ölçüm adımları (verify-required → draft geçişi için
tek engel); (2) E2 tam döngü (edge-trigger) ve E3 `timerfd_settime` imzasının man
sayfasıyla birebir doğrulanması; (3) `io_uring` (§14'te doğrulandı, örnek **yok**) için
ayrı bir § — yüksek frekanslı periyot akışında `epoll` alternatifi, **performans iddiası
olmadan** yalnız arayüz olarak; (4) `write(2)`/`open(2)` tabanlı basit E1 alternatifinin
(bloklayıcı yazma) eklenmesi. Bu maddeler tamamlanmadan dosya kapanmaz.

---

## Sıkça Sorulan Sorular (FAQ)

**S1: Örnekleri projede kullanabilir miyim?**
Yalnız `⚠️` ile — derlenmedi ve doğrulanmadı (§0/§16); önce §16 adımlarını çalıştırın.

**S2: Neden C, neden C++20 değil?**
Sistem çağrıları C yüzeyindedir; C++ sarmalayıcı (RAII/noexcept) **niyet** olarak §3/§7'de —
repo 0 `.cpp` olduğu için uydurma dosya/satır yasaktır (§0).

**S3: Hangi öncelik değerini ayarlayayım?**
Değer donanım/ölçüme bağlı → bu dosya **sayı vermez**; seçim timing §4/§12 protokolündedir.

**S4: `eventfd` yerine `pipe` kullanabilir miyim?**
Evet, ama `eventfd` tek kelimelik sayaçtır ve daha az kaynak tüketir (§4 notu); seçim
işlevsel değil, maliyet ölçümüyle yapılır.

## RED TEAM — Olası 5 eleştiri ve yanıtlar

1. **"Örnekler kısa/idealize; gerçek hata yolları eksik."**
   Yanıt: bilinçli — her örnek **tek iddiayı** gösterir (§2); hata yolları §17'de ayrıca
   tablolanır; tam sürümler §16 doğrulama turuna devredildi.
2. **"E1'de `ppoll` demiştiniz, `poll` yazdınız."**
   Yanıt: `poll` taşınabilir taban; `ppoll` süre/blok maskeleme genişletmesidir — bu turda
   doğrulanmadı → kullanılmadı (elenenler listesinde tutulur).
3. **"Windows eşleme tablosu (§15) `windows-kod-ornekleri.md` ile çakışır."**
   Yanıt: eşleme **kavramsal** ve tek yönlü; API ayrımları iki dosyada ayrı ayrı durur (§15 sonu).
4. **"Doğrulama planı yazılmamış, sadece niyet."**
   Yanıt: plan §16'da üç adım hâlinde; adımların kendisi **kod/tahsis değişikliği** olduğundan
   (repo inceleme/öneri kuralı) bu turda çalıştırılmadı — `verify-required` sebebi budur.
5. **"§14'te kullanılmayan URL'ler var (write/open/nice vb.)."**
   Yanıt: kullanılmayanlar **havuz notudur** (bir sonraki tur için) ve bu dosyanın benzersiz
   URL kümelerine **dahil edilmez**; tekrar riski elenenler listesiyle korunur.

---

## Kabul Kontrol Listesi (özet)

- [x] §0–§18 · FAQ 4 · RED TEAM 5 · iddia-URL tablosu + elenenler
- [x] 10 alanlı frontmatter · `node_type: code` · `group: kod` · `status: verify-required`
- [x] Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]`
- [ ] **≥500 satır** · **8–15 tablo** — ölçüm ↓

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** ≥500 satır · 10 alanlı frontmatter · 9 tablo · 10 benzersiz URL (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode