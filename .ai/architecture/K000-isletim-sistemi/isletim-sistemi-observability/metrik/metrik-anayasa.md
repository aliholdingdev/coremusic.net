---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik/metrik-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik
children: []
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
  - https://man7.org/linux/man-pages/man1/sar.1.html
  - https://man7.org/linux/man-pages/man1/mpstat.1.html
  - https://man7.org/linux/man-pages/man1/pidstat.1.html
  - https://man7.org/linux/man-pages/man1/top.1.html
  - https://man7.org/linux/man-pages/man1/free.1.html
  - https://man7.org/linux/man-pages/man1/uname.1.html
  - https://man7.org/linux/man-pages/man8/ethtool.8.html
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - https://docs.kernel.org/admin-guide/mm/concepts.html
  - https://man7.org/linux/man-pages/man7/signal.7.html
validation: "2026-10-13 — M-W1…M-W16 kök -dogrulama.md §2'de HTTP 200 doğrulanmış; M-W17 (uptime.1) ve M-W18 (perf_event_open.2) bu turda webfetch ile HTTP 200 doğrulandı (envanter kesişimi 0); ETW performans sayacı/PDH adayı HTTP 404 (kök -dogrulama.md §4 X6) → ⚠️ VERIFICATION REQUIRED; sayısal eşik/hacim değeri yazılmadı; 2. tur: M-W19/M-W20/M-W21 webfetch HTTP 200 (sysinfo.2, getloadavg.3, perf-top.1); sınıf bazlı soru envanteri ve kayıp envanteri §23–§24 ile eklendi; dosya 404 non-blank satır"
open_questions:
  - "O1: OS-level metrik toplama (perf, ETW, /proc okuyucu) hedef mimaride var mı, yoksa yalnız uygulama süresi mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık metrik (Prometheus/OTel) hedef mimarida mı? [UNKNOWN]"
group: mimari
---

# metrik-anayasa — OS Metrik/Sayaç Anayasası

## §0 ELI15

Metrik, bir sayacın **zaman içinde tekrar okunması**yla elde edilen değerdir.
İşletim sistemi üç yerde sayaç üretir: (1) **/proc ve /sys sanal dosyaları** —
sayaçların en ilkel ve en taşınabilir yüzeyi; (2) **PMU/`perf_event_open`** —
donanım sayaç sayaçları (cycle, cache miss); (3) **platform sayaç sistemleri** —
Windows performans sayacı (PDH/ETW) ve Apple karşıtları. Bu anayasa OS düzeyi
metrik **tanımını** bağlar: sayaç türleri, kaynak yüzeyleri, zaman kaynakları,
platform karşılaştırması. Uygulama/dağıtık metrik (Prometheus metriği, OTel
metrik adı, APM paneli) `K017-observability` dalının sorumluluğundadır.

Değişmez üç madde:

1. Ölçülmemiş sayı yazılmaz — eşik, periyot, hacim değeri envanterde/mevcut
   ölçümdе yoksa `UNKNOWN` yazılır.
2. Repo'daki `hrtime`/`microtime` süresi uygulama içi ölçümdür; OS latency
   metriği **sayılmaz**.
3. Doğrulanmayan URL envantere girmez; bu dosyadaki her URL ya kök
   `-dogrulama.md` §2'de HTTP 200 doğrulanmıştır ya da bu turda webfetch ile
   doğrulanmıştır (§12, tarih 2026-10-13).

## §1 Sayaç türleri — OS gözünden iki tür

| # | Tür | Tanım | OS örneği | Tekrar okuma davranışı |
|---|---|---|---|---|
| 1 | Sayaç (counter) | monotonik artar, sıfırlanması olaydır | toplam CPU zamanı, toplam paket sayısı | iki okuma farkı = artış hızı |
| 2 | Anlık değer (gauge) | her an her değeri alabilir | bellek kullanımı, yük ortalaması, süreç sayısı | tek okuma okunur, fark anlamsızdır |

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Sayaç farkı alınırken sayaç sıfırlandıysa (servis yeniden başladı) negatif fark **raporlanır**, 0'a yuvarlanmaz | yanlış negatif hız gizlenmez |
| 2 | Gauge'a oran/limit uygulanır (ör. kullanım %), sayaca uygulanmaz | monotonik değer oranı anlamı yok |
| 3 | Karşılaştırma yalnız aynı kaynaktan okunan değerlerle yapılır | kaynak değişiminde ölçek değişir |

> `cgroup v2` (M-W13) sayaçları her ikisini de karıştırır: `cpu.stat` sayaçtur
> (monotonik), `memory.current` gauge'tır — okuyucu bu ayrımı bilmek zorundadır.

## §2 /proc ve /sys — en ilkel metrik yüzeyi

**Kanıt:** proc(5) (M-W1) `/proc` dosya sistemi düzenini tanımlar.

| # | Dosya | Ne verir | Tür |
|---|---|---|---|
| 1 | `/proc/uptime` | sistem çalışma süresi + idle süresi | sayaç türevi |
| 2 | `/proc/loadavg` | 1/5/15 dk yük ortalaması + çalışan süreçler | gauge |
| 3 | `/proc/stat` | CPU zamanları, kesinti, context switch | sayaç |
| 4 | `/proc/meminfo` | bellek kalemleri (toplam, boş, buffer/cache) | gauge |
| 5 | `/proc/self/stat` | sürecin kendi CPU/zaman bilgisi | sayaç |
| 6 | `/proc/diskstats` | blok cihaz G/Ç sayaçları | sayaç |
| 7 | `/sys/class/net/<if>/statistics` | ağ arayüzü sayaçları | sayaç |

| # | Kural | Neden |
|---|---|---|
| 1 | /proc dosyaları **metindir**; ayrıştırma okuyucunun sorumluluğudur | şema sabiti değildir |
| 2 | Alan sırası sürümle değişebilir → ayrıştırıcı dosya adını sabit tutar, alanı indekse bağlar | sessiz kayma riski |
| 3 | /proc okuması syscall I/O'dur; sık periyot yüksek I/O üretir | periyot kararı `-surucu` §5'te |

> `/proc/stat` CPU alanlarının yorumu (user/system/idle/iowait ayrımı) bu dalın
> **kavramsal** sınırındadır; alan-okuma kodu yazımı O1 çözülünceye kadar
> tanımsızdır.

## §3 PMU ve perf_event_open — donanım sayaçları

**Kanıt:** perf(1) (M-W15) · perf stat(1) (M-W16) · perf_event_open(2) (M-W18, bu turda HTTP 200 doğrulandı).

| # | Kavram | Tanım | Kanıt |
|---|---|---|---|
| 1 | PMU | işlemci performans birimi — cycle, instruction, cache miss sayar | M-W15 |
| 2 | `perf_event_open` | PMU sayaçlarını süreç/çekirdek düzeyinde açan syscall | M-W18 |
| 3 | Sayaç tipi ailesi | `PERF_TYPE_HARDWARE` · `PERF_TYPE_SOFTWARE` · `PERF_TYPE_TRACEPOINT` · `PERF_TYPE_RAW` | M-W18 |
| 4 | Sayma (counting) modu | sayaç artırılır, kullanıcı `read()` ile değeri okur | M-W18 |
| 5 | Örnekleme (sampling) modu | sayaç eşiğinde kesinti → örnek kaydedilir (profil/trace) | M-W18 |

| # | Arayüz kuralı | Kanıt |
|---|---|---|
| 1 | `pid` + `cpu` argümanları hedefi belirler: tek süreç / tek çekirdek / tüm sistem (`pid = -1`) | M-W18 |
| 2 | Yetki: `CAP_PERFMON` gerekir; `perf_event_paranoid` syscall düzeyini sınırlar | M-W18 |
| 3 | Sayma ve örnekleme aynı mekanizmanın iki modudur — örnekleme trace alt-dalına (`trace/`) devredilir | T-W10, T-W11 |

```text
perf_event_open(pid, cpu, group_fd, flags)
  → sayma modu: read(fd) → tek değer
  → örnekleme modu: eşik taşınca kesinti → kayıt (trace/trace-ornekler.md §3)
```

> `perf stat` (M-W16) sayma modunun sarmalayıcısıdır: `-e` ile sayaç seçimi,
> süreç bitiminde toplam raporlar. Örnekleme (`perf record`) bu dosyanın
> kapsamı dışındadır — `trace/` alt-dalındadır.

## §4 /proc dışı procps sayaç araçları — yüzeyin sarmalayıcıları

**Kanıt:** M-W2 iostat(1) · M-W3 sar(1) · M-W4 mpstat(1) · M-W5 pidstat(1) · M-W6 top(1) · M-W7 free(1) · M-W8 uname(1).

| # | Araç | Sorduğu soru | Kaynak yüzeyi | Tür |
|---|---|---|---|---|
| 1 | iostat | G/Ç ne kadar zaman aldı? | /proc/diskstats | sayaç |
| 2 | sar | geçmişte ne oldu? (kayıt/geri okuma) | /proc/* + kendi arşivi | her ikisi |
| 3 | mpstat | CPU'lar arası dağılım? | /proc/stat | sayaç |
| 4 | pidstat | süreç bazlı kaynak? | /proc/[pid]/* | sayaç + gauge |
| 5 | top | şu an ne oluyor? (canlı gauge paneli) | /proc/* | gauge |
| 6 | free | bellek ne durumda? | /proc/meminfo | gauge |
| 7 | uname | sistem/sürüm kimliği? | uname syscall | sabit bilgi |

| # | Kural | Neden |
|---|---|---|
| 1 | Araç çıktısı **okunur, yeniden yazılmaz** — CoreMusic bu araçları çağıran kod yazmaz (O1) | tanımsızlık |
| 2 | sar'ın kendi arşivi OS düzeyi saklama değildir, uygulama arşividir — journald ile karıştırılmaz | sorumluluk ayrımı |
| 3 | vmstat bu dosyada **URL kanıtı olmadan** geçmez — aday URL HTTP 404 vermiştir (kök `-dogrulama.md` §4 X2) | zero-hallucination |

## §5 uptime ve load — "sistem ne kadar dolu?"

**Kanıt:** uptime(1) (M-W17, bu turda webfetch ile HTTP 200 doğrulandı).

| # | Alan | Anlam | Tür |
|---|---|---|---|
| 1 | şu anki saat | okuma anı | bilgi |
| 2 | uptime | kesintisiz çalışma süresi | sayaç türevi |
| 3 | kullanıcı sayısı | oturum sayısı | gauge |
| 4 | load average (1/5/15 dk) | hazır + I/O bekleyen süreç sayısı ortalaması | gauge |

| # | Load yorumlama kuralı | Not |
|---|---|---|
| 1 | Load, çalışan **ve** durdurulamaz (D durumunda) süreçleri sayar — yalnız CPU değil | I/O sıkışması yükü şişirir |
| 2 | Load tek başına suçlu bulmaz; CPU bekleyen mi, I/O bekleyen mi pidstat/diskstats ile ayrılır | ayrım zorunlu |
| 3 | Load eşiği çekirdek sayısıyla ilişkilidir; sabit evrensel eşik **uydurulmaz** — hedef çekirdek sayısı bilinmiyor | [UNKNOWN] (O2) |

## §6 Zaman kaynakları — metriğin taşıyıcısı

**Kanıt:** time(2) (M-W9) · clock_gettime(2) (M-W10).

| # | Kaynak | Tür | Metrik kullanımı |
|---|---|---|---|
| 1 | `CLOCK_REALTIME` | duyarlı (wall clock) | kayıt zaman damgası; NTP düzeltmesiyle sıçrayabilir |
| 2 | `CLOCK_MONOTONIC` | monotonik | aralık ölçümü (süre farkı) — **aralık ölçümünün tek doğru kaynağı** |
| 3 | `hrtime` (repo) | PHP sarmalayıcısı | `MiddlewarePipeline.php:42` — `CLOCK_MONOTONIC` karşılığı |
| 4 | `microtime(true)` (repo) | PHP duyarlı saat | `AIWorkflow.php:51` — REALTIME benzeri, aralık için uygun değil |

| # | Kural | Neden |
|---|---|---|
| 1 | Aralık ölçümü monotonik kaynakla yapılır | duyarlı saat sıçraması negatif/arızalı süre üretir |
| 2 | Zaman damgası duyarlı kaynakla yazılır | kaydın insan-okunur zamanı |
| 3 | İki repo API'si (`hrtime` vs `microtime`) birlikte raporlanır, birleştirilmez | davranış değişikliği bu turda YOK |

## §7 cgroup v2 ve bellek metrikleri

**Kanıt:** cgroup-v2 (M-W13) · mm/concepts (M-W14).

| # | Dosya | Ne verir | Tür |
|---|---|---|---|
| 1 | `cpu.stat` | kullanım süresi, throttle olayları | sayaç |
| 2 | `memory.current` | anlık bellek kullanımı | gauge |
| 3 | `memory.max` | sınır (limit) | yapılandırma |
| 4 | `memory.events` | OOM vb. olay sayaçları | sayaç |

| # | Kural | Neden |
|---|---|---|
| 1 | Sınır (`memory.max`) ile kullanım (`memory.current`) aynı okumada raporlanır | limit dışında kullanım değeri yanıltıcıdır |
| 2 | Bellek kavramları (anonim sayfa, buffer, cache) mm/concepts (M-W14) üzerinden okunur | uydurma tanımsız |
| 3 | OOM olayı hem `memory.events` sayacı hem journald kaydı üretir — log↔metrik kesişimi buradadır | iki kanal aynı olay |

## §8 Ağ sayaçları

**Kanıt:** ethtool(8) (M-W12).

| # | Yüzey | Ne verir | Kanıt |
|---|---|---|---|
| 1 | `ethtool -S <if>` | arayüz özel sayaçlar (hata, düşen paket) | M-W12 |
| 2 | `/sys/class/net/<if>/statistics` | taşınabilir sayaç seti | M-W1 (proc/sys yüzeyi) |

| # | Kural | Neden |
|---|---|---|
| 1 | Sayım hataları (error/drop) her zaman **sayı** olarak raporlanır, yüzde olarak değil | oran için toplam yük gerekir |
| 2 | Sayaçlar arayüz başınadır; toplam alınırken arayüz seti sabitlenir | arayüz ekleme/çıkarma farkı bozar |

## §9 Sinyaller — metrik üretmeyen ama durumu değiştiren olaylar

**Kanıt:** signal(7) (M-W11).

| # | Sinyal | Metrik ilişkisi |
|---|---|---|
| 1 | `SIGSTOP`/`SIGCONT` | süreç durdurulduğunda sayaç okumaları askıdadır — durum bilgisi metrik değildir, olaydır |
| 2 | `SIGKILL` | süreç sonlanır; son okunan sayaç değerleri donar (son değer ≠ 0) |
| 3 | `SIGTERM` | kapanış; son ölçüm anı kapanış zamanıdır |

> Sinyal olayları log alt-dalına (`log/`) aittir; bu dosyada yalnız metrik
> yorumunu etkileyen yönüyle geçer.

## §10 ETW performans sayacı (Windows) — ⚠️ VERIFICATION REQUIRED

| # | İddia | Kanıt durumu |
|---|---|---|
| 1 | Windows performans sayaçları PDH API üzerinden okunur | ⚠️ VERIFICATION REQUIRED — aday URL HTTP 404 (kök `-dogrulama.md` §4 X6) |
| 2 | Performans sayacı verisi ETW üzerinden taşınabilir | ⚠️ VERIFICATION REQUIRED — resmi kaynak bu turda doğrulanmadı |
| 3 | Sayaç adları ("Processor", "Memory" nesneleri) resmi registry'dedir | ⚠️ VERIFICATION REQUIRED — registry URL'si doğrulanmadı |

| # | Karar | Gerekçe |
|---|---|---|
| 1 | Windows metrik **arayüzü** bu dosyada tarif edilmez | zero-hallucination: doğrulanmış URL yok |
| 2 | Yalnız varlığı işaretlenir; detay yeni doğrulama turuna devredilir | 404 aday kayıt altındadır |
| 3 | Apple metrik yüzeyi (mach host_statistics vb.) | ⚠️ VERIFICATION REQUIRED — bu turda aday doğrulanmadı |

## §11 Platform karşılaştırması — tek tablo

| # | Konu | Linux | Windows | macOS |
|---|---|---|---|---|
| 1 | Birincil sayaç yüzeyi | /proc, /sys (M-W1) | performans sayacı ⚠️ (§10) | ⚠️ doğrulanmadı |
| 2 | Donanım sayaçları | perf_event_open / PMU (M-W15, M-W18) | ⚠️ | ⚠️ |
| 3 | Sarmalayıcı araç | perf stat, procps (M-W2…M-W8) | ⚠️ | ⚠️ |
| 4 | Yük/kullanım gösterimi | top/free/uptime (M-W6, M-W7, M-W17) | ⚠️ | ⚠️ |
| 5 | Kaynak limiti | cgroup v2 (M-W13) | ⚠️ | ⚠️ |
| 6 | Sistem kimliği | uname (M-W8) | ⚠️ | ⚠️ |

> Tablonun ⚠️ hücreleri **bilinmeyen değil, doğrulanmamıştır** — aradaki fark
> bu dosyanın kanıt disiplininin konusudur: doğrulanmayan iddia yazılmaz.

## §12 Resmi yapı taşları — URL envanteri (2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| M-W1 | proc(5) /proc dosya sistemi düzenini tanımlar | https://man7.org/linux/man-pages/man5/proc.5.html | 200 |
| M-W2 | iostat(1) G/Ç istatistiği verir | https://man7.org/linux/man-pages/man1/iostat.1.html | 200 |
| M-W3 | sar(1) sistem aktivite kayıtçısıdır | https://man7.org/linux/man-pages/man1/sar.1.html | 200 |
| M-W4 | mpstat(1) çoklu CPU istatistiği verir | https://man7.org/linux/man-pages/man1/mpstat.1.html | 200 |
| M-W5 | pidstat(1) süreç bazlı kaynak istatistiği verir | https://man7.org/linux/man-pages/man1/pidstat.1.html | 200 |
| M-W6 | top(1) canlı süreç/kaynak izleyicisidir | https://man7.org/linux/man-pages/man1/top.1.html | 200 |
| M-W7 | free(1) bellek kullanımını gösterir | https://man7.org/linux/man-pages/man1/free.1.html | 200 |
| M-W8 | uname(1) sistem/sürüm bilgisi verir | https://man7.org/linux/man-pages/man1/uname.1.html | 200 |
| M-W9 | time(2) süreç zamanlama ölçümü verir | https://man7.org/linux/man-pages/man2/time.2.html | 200 |
| M-W10 | clock_gettime(2) yüksek çözünürlüklü zaman kaynağıdır | https://man7.org/linux/man-pages/man2/clock_gettime.2.html | 200 |
| M-W11 | signal(7) sinyal semantiğini tanımlar | https://man7.org/linux/man-pages/man7/signal.7.html | 200 |
| M-W12 | ethtool(8) ağ arayüzü sayaçlarını okur | https://man7.org/linux/man-pages/man8/ethtool.8.html | 200 |
| M-W13 | cgroup v2 çekirdek yönetim belgesi resmi kaynaktır | https://docs.kernel.org/admin-guide/cgroup-v2.html | 200 |
| M-W14 | Bellek yönetim kavramları resmi çekirdek belgesindedir | https://docs.kernel.org/admin-guide/mm/concepts.html | 200 |
| M-W15 | perf(1) Linux performans olay aracıdır | https://man7.org/linux/man-pages/man1/perf.1.html | 200 |
| M-W16 | perf stat PMU sayaçlarını raporlar | https://man7.org/linux/man-pages/man1/perf-stat.1.html | 200 |
| M-W17 | uptime(1) çalışma süresi + load average verir | https://man7.org/linux/man-pages/man1/uptime.1.html | 200 (bu tur) |
| M-W18 | perf_event_open(2) PMU sayaç syscall'ıdır | https://man7.org/linux/man-pages/man2/perf_event_open.2.html | 200 (bu tur) |

**Dipnot (red kayıtları):** `vmstat.1` ve PDH toplama sayfası HTTP 404 aldığı
için bu dosyaya alınmamıştır (kök `-dogrulama.md` §4 X2, X6);
`getrusage.2`/`gettimeofday.2`/`vdso.7` envanter kesişimi nedeniyle elenmiştir
(kök `-dogrulama.md` §17.1).

## §13 K017 sınırı ve komşu matrisi

| # | Konu | Bu dosyada | Gittiği yer |
|---|---|---|---|
| 1 | Prometheus metriği / scrape | yok | `K017-observability` |
| 2 | OTel metrik adı/kaydı | yok | `K017-observability` |
| 3 | APM paneli / dashboard | yok | `K017-observability` |
| 4 | İş alarmı (alert kuralı) | yok — yalnız OS eşiği (softlockup) tarif | `K017-observability` |
| 5 | Sayaç üretimi (PMU, /proc) | yalnız okuma | `isletim-sistemi-kernel`, `isletim-sistemi-core` |
| 6 | Örnekleme/profil kaydı | yok | `trace/` alt-dalı |

## §14 Ölçüm ve dürüstlük kuralları

| # | Kural | Uygulama |
|---|---|---|
| 1 | Eşik değeri (softlockup süresi, iowait limiti vb.) bu turda sabitlenmez | O2 çözülmeden sayı uydurulmaz |
| 2 | Ölçüm periyodu önerilmez | periyot kararı toplama ajanı tasarımındadır (`-surucu` §5) |
| 3 | repo süre ölçümleri OS latency **sayılmaz** | `metrik-ornekler.md` §2–§4 |
| 4 | `⚠️ VERIFICATION REQUIRED` etiketi doğrulanamayan her iddiaya konur | §10, §11 |

## §15 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS metrik toplama (perf, ETW, /proc okuyucu) hedef mimaride var mı? | UNKNOWN |
| O2 | Toplama periyodu ve eşik değerleri ne olacak? | UNKNOWN |
| O3 | Dağıtık metrik (Prometheus/OTel) hedef mimarida mı? | UNKNOWN |

## §16 sysinfo(2) ve getloadavg(3) — /proc dışı iki okuma yüzeyi (bu tur)

**Kanıt:** sysinfo(2) (M-W19) · getloadavg(3) (M-W20) — her ikisi de 2026-10-13 webfetch HTTP 200.

| # | Yüzey | Tür | Ne verir | Sınıf |
|---|---|---|---|---|
| 1 | `sysinfo(2)` | syscall | uptime, loads[3], totalram, freeram, totalswap, freeswap, procs, mem_unit | karışık (gauge + çarpan) |
| 2 | `getloadavg(3)` | kütüphane | 1/5/15 dk yük ortalaması, en fazla 3 örnek | gauge |
| 3 | `/proc` (M-W1) | sanal dosya | metin akışı — ayrıştırma okuyucunun işi | karışık |
| 4 | perf_event_open (M-W18) | syscall | PMU sayaç fd'si | sayaç |

| # | sysinfo alanları | Tür |
|---|---|---|
| 1 | `uptime` | sayaç türevi |
| 2 | `loads[3]` | gauge |
| 3 | `totalram` / `freeram` | gauge |
| 4 | `totalswap` / `freeswap` | gauge |
| 5 | `procs` | gauge |
| 6 | `mem_unit` | çarpan (ölçüm değildir) |

| # | Yüzeyler arası kural | Gerekçe |
|---|---|---|
| 1 | sysinfo ile `/proc/meminfo` **aynı bilgiyi** verir (M-W19 NOTES) — ikisi birden toplanmaz | aynı veri, iki arayüz |
| 2 | `mem_unit` uygulanmadan bayt karşılaştırması yapılmaz | birim hatası değeri şişirir |
| 3 | getloadavg en fazla 3 örnek verir; 4. değer **uydurulmaz** | API sınırı (M-W20) |
| 4 | getloadavg hatada `-1` döner — "yük 0" **denmez** | hata ≠ sıfır |
| 5 | `/proc` metindir, sysinfo yapı dönüşümüdür | ayrıştırma riski yalnız /proc'da |
| 6 | repo'da üç yüzey de **yok** (grep 0 isabet) | tanımsızlık (O1) |

## §17 Kaynak ↔ iddia çapraz matrisi (kanıt disiplini)

| # | İddia | Birincil kaynak | İkincil kaynak | Durum |
|---|---|---|---|---|
| 1 | /proc düzeni proc(5) ile tanımlıdır | M-W1 | — | doğrulandı |
| 2 | PMU sayaçları perf_event_open ile açılır | M-W18 | M-W15, M-W16 | doğrulandı |
| 3 | yük 1/5/15 dk üç örnektir | M-W17 | M-W20, M-W19 | doğrulandı |
| 4 | sistem özeti tek syscall ile okunur | M-W19 | — | doğrulandı (bu tur) |
| 5 | yük kütüphane ile okunabilir | M-W20 | — | doğrulandı (bu tur) |
| 6 | cgroup limit + kullanım birlikte okunur | M-W13 | — | doğrulandı |
| 7 | Windows sayacı PDH ile okunur | — | — | **404 → ⚠️** (kök §4 X6) |
| 8 | macOS metrik arayüzü | — | — | **doğrulanmadı → ⚠️** |
| 9 | vmstat sayacı | — | — | **404 → kullanılmadı** |
| 10 | irqsoff tracer sayfası | — | — | **404 → kullanılmadı** (bu tur) |
| 11 | perf sched sayfası | — | — | **404 → kullanılmadı** (bu tur) |

## §18 Hata/anti-pattern kataloğu — metrik tanımı

| # | Anti-pattern | Neden yanlış | Doğru davranış |
|---|---|---|---|
| 1 | gauge'a fark almak | anlık değer farkı anlamsız | fark yalnız sayaçta |
| 2 | sayaç sıfırlanmasını 0'a yuvarlamak | gizlenen veri kaybı | negatif fark **raporlanır** |
| 3 | sınırı okumadan kullanım raporlamak | limit dışı kullanım yanıltıcı | `memory.max` + `memory.current` birlikte |
| 4 | log olayını sayaç saymak | iki kanal aynı olayı gösterir | OOM: journald kaydı + `memory.events` ayrı |
| 5 | duyarlı saatle aralık ölçmek | NTP sıçraması bozar | monotonik saat |
| 6 | alan indeksine sabit bağlamak | /proc sırası değişebilir | dosya adına bağla, indeksi ayır |
| 7 | ölçmeden eşik yazmak | uydurma sayı | `UNKNOWN` (O2) |
| 8 | "0 isabet = eksiklik" | yokluk ≠ imkânsızlık | tanımsızlık raporu |
| 9 | CI eşiğini OS eşiği sanmak | kalite kapısıdır | sınıf ayrımı (§17-§18 surucu) |
| 10 | örnek süreç ömrünü periyot sanmak | `sleep 10` örnektir | periyot ayrı karar |
| 11 | vmstat'ı kanıtsız eklemek | aday 404 | zero-hallucination |
| 12 | PDH'yi ETW ile karıştırmak | farklı katman (metrik vs trace) | dal ayrımı |

## §19 Senaryo — sayaç seçimi karar akışı (tarif)

| # | Soru | Evet → | Hayır → |
|---|---|---|---|
| 1 | soru "ne kadar birikti?" mu? | sayaç (§1) | §1 gauge satırı |
| 2 | soru "şimdi ne kadar?" mu? | gauge (§1) | §1 sayaç satırı |
| 3 | okuma /proc'dan mı? | metin ayrıştırma (§2) | §3 |
| 4 | donanım sayacı mı gerek? | perf_event_open (§3) | /proc / sysinfo (§2, §16) |
| 5 | tek çağrıda özet mi lazım? | sysinfo(2) (§16) | /proc ayrıntısı |
| 6 | yalnız yük mü? | getloadavg(3) (§16) | sysinfo / /proc |
| 7 | Windows yüzeyi mi? | ⚠️ doğrulanmadı (§10) | Linux kaynakları |

| # | Seçim sonrası zorunlu kayıt | Neden |
|---|---|---|
| 1 | hangi yüzeyden okunduğu (/proc, sysinfo, PMU) | karşılaştırma yalnız aynı kaynaktan |
| 2 | okuma anı ve periyot | tekrarlanabilirlik |
| 3 | birim çarpanı (mem_unit) | birim hatası |
| 4 | hata durumu (`-1`, yetki reddi) | hata ≠ 0 |

## §20 Sık karıştırılan cümleler — düzeltme matrisi

| # | Yanlış cümle | Doğru cümle | Kanıt |
|---|---|---|---|
| 1 | "sayaç ile gauge farkı önemsizdir" | fark yalnız sayaçda anlam taşır | §1 |
| 2 | "sysinfo /proc okur" | ayrı syscall; veri yüzeyi kesişir | §16 |
| 3 | "getloadavg 5 değer döndürür" | en fazla 3; hatada `-1` | M-W20 |
| 4 | "load = CPU kullanımı" | CPU + I/O bekleyen | §5 |
| 5 | "VM ile ölçülür" | vmstat adayı 404 | §17.9 |
| 6 | "Windows sayacı doğrulandı" | PDH adayı 404 → ⚠️ | §10 |
| 7 | "repo'da metrik yoksa eksiktir" | tanımsızlıktır (O1) | §14 |
| 8 | "periyot varsayılan 10 sn" | periyot `UNKNOWN` (O2) | §14 |
| 9 | "cgroup cpu.stat anlık değer" | monotonik sayaçtır | §7 |
| 10 | "OOM tek kanaldır" | iki kanal: log + sayacı | §7 |

## §21 Bu tur ek doğrulanan / reddedilen kaynaklar (2026-10-13)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | M-W19 | sysinfo(2) sistem özetini tek çağrıda verir | https://man7.org/linux/man-pages/man2/sysinfo.2.html | 200 (bu tur) |
| 2 | M-W20 | getloadavg(3) en fazla 3 yük örneği verir | https://man7.org/linux/man-pages/man3/getloadavg.3.html | 200 (bu tur) |
| 3 | M-W21 | perf top(1) gerçek zamanlı profil verir | https://man7.org/linux/man-pages/man1/perf-top.1.html | 200 (bu tur) |
| 4 | — | `docs.kernel.org/trace/irqsoff-tracer.html` | (aday) | 404 — kullanılmadı |
| 5 | — | `man7.org/.../man8/perf-sched.8.html` | (aday) | 404 — kullanılmadı |

> Envater kesişimi 0 (M-W1…M-W18 ile örtüşmez). `openlog.3` adayı 200 döndü
> ancak içerik **syslog(3)** sayfasıdır (L-W1 ile aynı sayfa) — mükerrer
> sayıldı, bu dosyaya yeni ID olarak **alınmadı** (dürüstlük kaydı).

## §22 Ölçüm ve dürüstlük kuralları — bu tur eki

| # | Kural | Uygulama |
|---|---|---|
| 1 | `⚠️` yalnız **doğrulanmamış** iddiaya konur | §10, §11 |
| 2 | `UNKNOWN` yalnız **bilinmeyen** değere konur | O1–O3 |
| 3 | 404 aday envantere **alınmaz**, reddedilenler listesinde kalır | §21.4–§21.5 |
| 4 | 200 dönen ama içerik farklı sayfa olan URL mükerrer sayılır | §21 dipnotu |
| 5 | bu turda ölçülen hiçbir sayısal metrik değeri yoktur | `[UNKNOWN]` |
| 6 | repo iddiası yalnız Read/grep ile okunan satırla yazılır | `metrik-ornekler.md` |
| 7 | CI iddiası yalnız tam dosya okumasıyla yazılır | ci.yml 203 satır / 6 job |

## §23 Bölüm → kaynak haritası

| # | Bölüm | Kaynak | Kanıt türü | Durum |
|---|---|---|---|---|
| 1 | §1 sayaç türleri | cgroup-v2 (M-W13) | URL | 200 |
| 2 | §2 /proc ve /sys | proc(5) (M-W1) | URL | 200 |
| 3 | §3 PMU/perf_event_open | M-W15, M-W16, M-W18 | URL | 200 |
| 4 | §4 procps araçları | M-W2…M-W8 | URL | 200 |
| 5 | §5 uptime/load | M-W17 (+ M-W19, M-W20 bu tur) | URL | 200 |
| 6 | §6 zaman kaynakları | M-W9, M-W10 | URL | 200 |
| 7 | §7 cgroup/bellek | M-W13, M-W14 | URL | 200 |
| 8 | §8 ağ sayaçları | M-W12, M-W1 | URL | 200 |
| 9 | §9 sinyaller | M-W11 | URL | 200 |
| 10 | §10 ETW/PDH | — | 404 aday | ⚠️ |
| 11 | §11 platform matrisi | §1–§10 | sentez | kısmi ⚠️ |
| 12 | §12 URL envanteri | 18 kayıt | URL | 200 |
| 13 | §13 K017 sınırı | — | sınır | devredildi |
| 14 | §14 dürüstlük | — | kural | — |
| 15 | §15 açık sorular | — | O1–O3 | UNKNOWN |
| 16 | §16 sysinfo/getloadavg | M-W19, M-W20 | URL | 200 (bu tur) |
| 17 | §17 çapraz matris | §1–§16 | sentez | — |
| 18 | §18 anti-pattern | §1–§17 | sentez | — |
| 19 | §19 sayaç seçimi | §1–§3, §16 | sentez | tarif |
| 20 | §20 sık karıştırma | sentez | sentez | — |
| 21 | §21 bu tur kaynaklar | webfetch | URL | 3 × 200 + 2 × 404 |
| 22 | §22 dürüstlük eki | — | kural | — |

| # | Harita kuralı | Neden |
|---|---|---|
| 1 | her § bir kaynağa bağlanır | kanıtsız bölüm sayılmaz |
| 2 | §10'un ⚠️ kaydı korunur | 404 aday envantere girmez |
| 3 | bu tablo yeni iddia **taşımez** | mükerrer kanıt üretilmez |

## §24 Sınıf ayrım özeti — tek sayfada

| # | Nesne | Sınıf | Tekrar okuma | Karşılığı |
|---|---|---|---|---|
| 1 | `/proc/stat` CPU zamanları | sayaç | fark = artış | iostat/mpstat |
| 2 | `/proc/meminfo` kalemleri | gauge | tek okuma | free |
| 3 | `/proc/loadavg` | gauge | tek okuma | uptime, getloadavg |
| 4 | `cpu.stat` (cgroup) | sayaç | fark = kullanım | — |
| 5 | `memory.current` (cgroup) | gauge | tek okuma | — |
| 6 | `memory.events` (cgroup) | sayaç | fark = olay sayısı | journald kaydı (iki kanal) |
| 7 | PMU sayaçları | sayaç | `read(fd)` toplam | perf stat |
| 8 | örnekleme modu | olay akısı | — | trace dalı |
| 9 | `sysinfo.uptime` | sayaç türevi | fark | uptime(1) |
| 10 | `sysinfo.procs` | gauge | tek okuma | — |
| 11 | softlockup kaydı | olay (log) | — | log dalı |
| 12 | CI job süresi | CI verisi | — | OS metriği **değil** |
| 13 | repo `hrtime` farkı | uygulama içi | — | OS latency **değil** |
| 14 | getloadavg örnekleri | gauge (×3) | tek okuma | 1/5/15 dk |

| # | Ayrım kuralı | Neden |
|---|---|---|
| 1 | sayaçta fark alınır, gauge'ta alınmaz | anayasa §1 |
| 2 | olay log kaydıdır, sayı **değildir** | log↔metrik kesişimi (surucu §6) |
| 3 | iki kanal aynı olayı gösterir, toplanmaz | OOM (§7) |
| 4 | CI/uygulama verisi OS sınıfına **geçirilmez** | sınıf kayması yanıt üretir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
