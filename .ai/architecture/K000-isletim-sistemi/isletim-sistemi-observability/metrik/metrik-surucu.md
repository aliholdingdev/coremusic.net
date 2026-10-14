---
node_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik/metrik-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-observability/metrik
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - https://man7.org/linux/man-pages/man2/perf_event_open.2.html
  - https://man7.org/linux/man-pages/man1/perf-stat.1.html
  - https://man7.org/linux/man-pages/man1/perf-record.1.html
  - https://man7.org/linux/man-pages/man1/uptime.1.html
  - https://man7.org/linux/man-pages/man1/iostat.1.html
  - https://man7.org/linux/man-pages/man1/sar.1.html
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/AI/AIWorkflow.php:51
validation: "2026-10-13 — M-W17 (uptime.1) ve M-W18 (perf_event_open.2) bu turda webfetch ile HTTP 200 doğrulandı; diğer URL'ler kök -dogrulama.md §2-§3'te HTTP 200; perf_event_open arayüz ayrıntıları (pid/cpu, sayma vs örnekleme, CAP_PERFMON, perf_event_paranoid) doğrulanmış sayfadan okundu; repo satırları Read ile doğrulandı; periyot/eşik değeri yazılmadı; 2. tur: M-W19 (sysinfo.2 — uptime/loads[3]/ram/swap/procs, mem_unit, /proc/meminfo ve /proc/loadavg NOTS notu), M-W20 (getloadavg.3 — en fazla 3 örnek, hata -1), M-W21 (perf-top.1 — OVERHEAD CALCULATION self/children ayrı tanımı) webfetch ile HTTP 200 doğrulandı; ci.yml 6 job tam dosya okundu (php-lint/php-test/composer-audit/js-test/js-coverage/js-e2e, timeout-minutes 10–20, cancel-in-progress, %80 eşiği yorum satırında continue-on-error: true); dosya 397 non-blank satır"
open_questions:
  - "O1: OS-level metrik toplama (perf, ETW, /proc okuyucu) hedef mimaride var mı, yoksa yalnız uygulama süresi mi? [UNKNOWN]"
  - "O2: Gözlemlenebilirlik üçlüsü (log/metric/trace) K002-altyapi içinde nasıl konumlanacak? [UNKNOWN]"
  - "O3: Dağıtık metrik (Prometheus/OTel) hedef mimarida mı? [UNKNOWN]"
group: mimari
---

# metrik-surucu — OS Metrik Okuma, Periyot ve Eşik Mühendisliği

## §0 ELI15

Anayasa sayaç **tanımını** kurar; bu dosya sayacın **nasıl okunacağını** kurar:
`perf_event_open` ile sayaç açmanın iki modu (sayma vs örnekleme), yetki
sınırları, periyot kararının nerede verildiği, OS eşik/log olaylarının
(softlockup, hung task, OOM) metriğe nasıl dokunduğu ve repo'nun bu yüzeyde
**hiç bulunmadığının** olumsuz kanıtı. Hepsi OS düzeyindedir — Prometheus
scrape, alert kuralı, dashboard `K017-observability` sınırının ötesindedir.

Üç kural burada bağlayıcı:

1. Periyot ve eşik değeri envanterde/mevcut ölçümdе yoksa **uydurulmaz** —
   `UNKNOWN` yazılır (O2).
2. Sayma modu ile örnekleme modu karıştırılmaz: sayma **değer** verir,
   örnekleme **olay** verir (olay trace alt-dalındadır).
3. OS eşik olayı (softlockup) bir **log kaydı**dır; metrik değeri değil —
   log↔metrik kesişimi burada çizilir.

## §1 perf_event_open — sayaç açmanın iki modu

**Kanıt:** perf_event_open(2) (M-W18, 2026-10-13 HTTP 200).

| # | Mod | Ne yapar | Çıktı | Hangi soruyu yanıtlar |
|---|---|---|---|---|
| 1 | Sayma (counting) | sayaç artırılır; süreç bitiminde `read()` | tek değer (toplam) | "bu süreç kaç cycle yedi?" |
| 2 | Örnekleme (sampling) | eşik taşınca kesinti → örnek | olay akışı | "nereye zaman harcadı?" (trace alt-dalı) |

| # | Arayüz ayrıntısı | Değer | Kanıt |
|---|---|---|---|
| 1 | hedef pid | tek süreç / `pid=-1` → tüm sistem | M-W18 |
| 2 | hedef cpu | tek çekirdek / `-1` → makinedeki tüm çekirdekler | M-W18 |
| 3 | sayma tipi ailesi | `PERF_TYPE_HARDWARE` · `PERF_TYPE_SOFTWARE` · `PERF_TYPE_TRACEPOINT` · `PERF_TYPE_RAW` | M-W18 |
| 4 | gruplandırma | `group_fd` ile sayaçlar birlikte atomik okunur | M-W18 |

```text
perf_event_open(pid, cpu, group_fd, flags)
  ├─ sayma modu    → read(fd)            → toplam değer (bu dosya §2)
  └─ örnekleme modu → kesinti + kayıt    → olay akışı (trace/ §3, T-W10)
```

## §2 Yetki sınırı — CAP_PERFMON ve perf_event_paranoid

**Kanıt:** perf_event_open(2) (M-W18).

| # | Mekanizma | Ne yapar | Etki |
|---|---|---|---|
| 1 | `CAP_PERFMON` | sistem/süreç dışı sayaç açma yetkisi | yetki yoksa yalnız kendi süreci |
| 2 | `perf_event_paranoid` | izinsiz açmayı syscall düzeyinde sınırlar | değer yükseldikçe erişim daralır |
| 3 | `perf` sarmalayıcısı | aracı süreç yetkisizse uyarır, açmaz | sessiz başarısızlık yok |

| # | Kural | Neden |
|---|---|---|
| 1 | Toplama ajanı çalıştırılacaksa yetki **baştan** planlanır — çalışırken yetki sürprizi olmaz | O1 henüz bilinmiyor |
| 2 | `perf_event_paranoid` değeri envanterde `[UNKNOWN]` — hedef sunucuda okunmadan varsayılmaz | zero-hallucination |
| 3 | Yetkisiz sayaç açma **hata sayılır**, sessiz 0'a düşürülmez | yanlış "sessiz" ölçüm |

## §3 procps sarmalayıcıları — okuma akışları

**Kanıt:** M-W2 iostat · M-W3 sar · M-W5 pidstat · M-W16 perf stat · M-W17 uptime.

| # | Araç | Komut kalıbı (tarif) | Kaynak yüzeyi |
|---|---|---|---|
| 1 | perf stat | `perf stat -e <sayaç> -- <süreç>` | PMU (M-W18) |
| 2 | iostat | `iostat -xz 1` | /proc/diskstats |
| 3 | sar | `sar -q 1 10` (yük) · `sar -b` (G/Ç) | /proc/* + sar arşivi |
| 4 | pidstat | `pidstat -u 1` | /proc/[pid]/* |
| 5 | uptime | `uptime` | /proc/uptime + /proc/loadavg |

| # | Akış kuralı | Neden |
|---|---|---|
| 1 | Aracın çıktısı **okunur**; CoreMusic bu komutları çağıran kod yazmaz (grep 0 isabet, §8) | tanımsızlık (O1) |
| 2 | İki okuma arasındaki fark = artış; tek okuma = gauge | anayasa §1 tür ayrımı |
| 3 | `perf stat` toplam raporlar; anlık hız için iki okuma gerekir | sarmalayıcı modu |

## §4 uptime/load okuma akışı

**Kanıt:** uptime(1) (M-W17, bu turda HTTP 200).

| # | Adım | Ne okunur | Yorum |
|---|---|---|---|
| 1 | load average (1/5/15 dk) | `/proc/loadavg` üzerinden | hazır + D-durumundaki süreçler |
| 2 | CPU çekirdek sayısıyla karşılaştır | çekirdek sayısı bilinmiyor → oran `[UNKNOWN]` | O2 |
| 3 | şüpheli load yükselmesinde ayrım | pidstat (CPU mu, I/O mu) | load tek başına suçlu bulmaz |

| # | Dürüstlük kuralı | Uygulama |
|---|---|---|
| 1 | "load X'in üstünde alarm" eşiği yazılmaz | hedef çekirdek sayısı/envanter yok |
| 2 | Load yalnız CPU yükü **değildir** | I/O bekleme (D durumu) da sayar |
| 3 | 1/5/15 dk penceresi üç ayrı ölçüm penceresidir | biri diğerinin ortalaması değildir |

## §5 Periyot kararı — nerede verilir, neyle verilmez

| # | Soru | Karar durumu | Kanıt |
|---|---|---|---|
| 1 | toplama periyodu kaç saniye? | [UNKNOWN] — envanterde yok | O2 |
| 2 | periyot kim verir? | toplama ajanı tasarımı (K002-altyapi) — bu dal yalnız kuralı tarif eder | O2 |
| 3 | periyot sabit mi dinamik mi? | [UNKNOWN] | O2 |

| # | Periyot kuralı (sayısal değer olmadan) | Neden |
|---|---|---|
| 1 | Periyot, I/O bütçesiyle birlikte seçilir — /proc okuması syscall'dır | sık okuma I/O yükü üretir |
| 2 | Sayma modunda periyot, okuma sıklığıdır; örnekleme modunda periyot **kaydedicinin** işidir | modlar farklıdır |
| 3 | Periyot değişirse önceki seri ile karşılaştırma işaretlenir | ölçek kayması |

## §6 OS eşik olayları — metrik↔log kesişimi

| # | Olay | OS kaynağı | Ürettiği şey | Kanıt durumu |
|---|---|---|---|---|
| 1 | softlockup | çekirdek lockup detector | printk mesajı (log) | log alt-dalında tarif (L-W9 printk) |
| 2 | hung task | çekirdek hung_task | printk mesajı (log) | ⚠️ VERIFICATION REQUIRED — resmi URL bu turda doğrulanmadı |
| 3 | OOM kill | bellek denetleyicisi | dmesg kaydı + `memory.events` sayacı | M-W13, M-W14 |
| 4 | throttling | cgroup v2 `cpu.stat` | sayaç artışı | M-W13 |

| # | Kural | Neden |
|---|---|---|
| 1 | Eşik olayı **log kaydı** üretir; eşik değeri sabitlenmez | O2 çözülmeden sayı uydurulmaz |
| 2 | OOM tek olay, iki kanal: journald kaydı + `memory.events` sayacı | iki kanal aynı olayı gösterir, toplanmaz |
| 3 | softlockup süresi gibi çekirdek eşikleri bu turda **yazılmaz** | doğrulanmış eşik değeri yok |

## §7 cgroup v2 okuma akışı

**Kanıt:** cgroup v2 (M-W13).

| # | Adım | Dosya | Tür |
|---|---|---|---|
| 1 | anlık kullanım | `memory.current` | gauge |
| 2 | sınır ile birlikte oku | `memory.max` | yapılandırma |
| 3 | olay sayaçları | `memory.events` (oom, oom_kill) | sayaç |
| 4 | CPU kullanımı | `cpu.stat` (usage_usec) | sayaç |

| # | Kural | Neden |
|---|---|---|
| 1 | gauge + sınır tek raporda | sınır dışı kullanım yanıltıcıdır (anayasa §7) |
| 2 | `memory.events` farkı olay sayısıdır | monotonik sayaç |
| 3 | cihaz grubu (cgroup kökü) bilinmiyor → hedef yol `[UNKNOWN]` | envanter yok |

## §8 Olumsuz kanıt — repo'da OS metrik okuma yok

| # | Desen | Beklenen kullanım | İsabet |
|---|---|---|---|
| 1 | `perf_event` | perf_event_open çağrısı | 0 |
| 2 | `/proc/stat` | CPU sayaç okuma | 0 |
| 3 | `/proc/loadavg` | load okuma | 0 |
| 4 | `proc/self` | süreç metriği okuma | 0 |
| 5 | `EventWrite` | ETW etkinlik yazımı | 0 |
| 6 | `OpenTelemetry` / `OTel` | dağıtık metrik kütüphanesi | 0 |

> Repo geneli `*.{php,js,yml,yaml,json}` taraması (vendor hariç), 2026-10-13.
> Sonuç: OS metrik katmanı repo'da **kullanılmıyor**; bu "eksiklik" değil,
> hedef mimaride **tanımsızlık** olarak raporlanır (O1 [UNKNOWN]).

## §9 Repo süresi — OS metriği olmayan tek ölçüm yüzeyi

| # | Dosya:Satır | Kod | OS yorumu |
|---|---|---|---|
| 1 | `MiddlewarePipeline.php:42` | `$start = hrtime(true);` | monotonik aralık ölçümü (M-W10 karşılığı) |
| 2 | `MiddlewarePipeline.php:46` | `$durationMs = (hrtime(true) - $start) / 1_000_000;` | uygulama içi latency |
| 3 | `AIWorkflow.php:51` | `$startTime = microtime(true);` | duyarlı saat — aralık için uygun değil |
| 4 | `ToolCalling.php:85` | `$startTime = microtime(true);` | aynı API ikiliği |
| 5 | `UuidV7.php:19` | `$ms = (int)(microtime(true) * 1000);` | zaman damgası üretimi, ölçüm değil |

| # | Kural | Neden |
|---|---|---|
| 1 | Bu süreler **OS latency metriği değildir** | syscall/kernel gecikmesi ölçülmüyor |
| 2 | Süreler log satırına düşer (`timing()`), metrik deposuna değil | zaman serisi toplanmıyor |
| 3 | `hrtime` vs `microtime` ikiliği yalnız raporlanır | davranış değişikliği bu turda YOK |

## §10 Platform surucu karşılaştırması

| # | Konu | Linux | Windows | macOS |
|---|---|---|---|---|
| 1 | sayaç syscall'ı | perf_event_open (M-W18) | ⚠️ VERIFICATION REQUIRED (PDH 404) | ⚠️ VERIFICATION REQUIRED |
| 2 | sarmalayıcı | perf stat, procps (M-W2…M-M8, M-W16, M-W17) | ⚠️ | ⚠️ |
| 3 | yetki modeli | CAP_PERFMON + perf_event_paranoid (M-W18) | ⚠️ | ⚠️ |
| 4 | eşik olayı kanalı | printk → dmesg (L-W9) | Event Log (L-W10) ⚠️ düzey eşlemesi | ⚠️ |
| 5 | kaynak limiti | cgroup v2 (M-W13) | ⚠️ | ⚠️ |

## §11 Ölçüm ve doğrulama adımları (bu dal için)

| # | Adım | Araç | Durum |
|---|---|---|---|
| 1 | hedef sunucu envanteri (çekirdek sayısı, cgroup kökü) | okunmadı | [UNKNOWN] |
| 2 | perf_event_paranoid değeri | okunmadı | [UNKNOWN] |
| 3 | log hacmi / metrik hacmi ölçümü | ölçülmedi | [UNKNOWN] |
| 4 | repo grep (OS metrik araçları) | grep 0 isabet | §8 |
| 5 | repo satır doğrulama | Read | §9 |

## §12 K017 sınırı ve komşu haritalar

| # | Konu | Bu dosyada | Gittiği yer |
|---|---|---|---|
| 1 | scrape periyodu / scrape_timeout | yok | `K017-observability` |
| 2 | alert kuralı (eşik + süre + hedef) | yok — yalnız OS olayı tarif | `K017-observability` |
| 3 | metrik depolama/downsampling | yok | `K017-observability` |
| 4 | örnekleme modu / profil kaydı | devredildi | `trace/` alt-dalı |
| 5 | log hacmi metriği | devredildi | `log/log-ornekler.md` §11 |

## §13 Platform surucu kararları — özet

| # | Karar | Linux | Windows | Apple |
|---|---|---|---|---|
| 1 | sayaç yüzeyi | /proc + PMU (doğrulanmış) | ⚠️ doğrulanmadı | ⚠️ doğrulanmadı |
| 2 | okuma modu | sayma (değer) | ⚠️ | ⚠️ |
| 3 | yetki | CAP_PERFMON / paranoid | ⚠️ | ⚠️ |
| 4 | eşik olayı | printk → dmesg | Event Log kanalı | os_log |

## §14 Kaynakça (envanter dışı, HTTP 200 — 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| M-W17 | uptime(1) çalışma süresi + load average verir | https://man7.org/linux/man-pages/man1/uptime.1.html | 200 (bu tur) |
| M-W18 | perf_event_open(2) PMU sayaç syscall'ıdır | https://man7.org/linux/man-pages/man2/perf_event_open.2.html | 200 (bu tur) |
| M-W16 | perf stat PMU sayaçlarını raporlar | https://man7.org/linux/man-pages/man1/perf-stat.1.html | 200 |
| M-W10 | clock_gettime(2) monotonik zaman kaynağı verir | https://man7.org/linux/man-pages/man2/clock_gettime.2.html | 200 |

**Reddedilen adaylar:** PDH toplama sayfası (HTTP 404, kök `-dogrulama.md` §4
X6) ve `vmstat.1` (HTTP 404, X2) bu dosyaya alınmamıştır.

## §15 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | OS metrik toplama hedef mimaride var mı? | UNKNOWN |
| O2 | Toplama periyodu ve eşik değerleri ne olacak? | UNKNOWN |
| O3 | Dağıtık metrik hedef mimarida mı? | UNKNOWN |

## §16 sysinfo(2) — tek syscall'da sistem özeti

**Kanıt:** sysinfo(2) (M-W19, 2026-10-13 webfetch HTTP 200).

| # | Alan | Ne verir | Tür |
|---|---|---|---|
| 1 | `uptime` | kesintisiz çalışma süresi (saniye) | sayaç türevi |
| 2 | `loads[3]` | 1/5/15 dakika yük ortalaması | gauge |
| 3 | `totalram` | toplam fiziksel bellek | gauge |
| 4 | `freeram` | boş fiziksel bellek | gauge |
| 5 | `totalswap` / `freeswap` | toplam / boş takas alanı | gauge |
| 6 | `procs` | o andaki süreç sayısı | gauge |
| 7 | `mem_unit` | bayt/birim çarpanı | çarpan — ölçüm değeri değildir |

| # | sysinfo okuma akışı | Ne yapılır | Dikkat |
|---|---|---|---|
| 1 | çağrı | tek syscall ile özet okunur | /proc'dan farkı: metin ayrıştırma yoktur |
| 2 | birim | `mem_unit` bayta çevrilir | çarpan uygulanmazsa değer şişer |
| 3 | tekrar okuma | iki okuma farkı = artış | gauge kalemlerinde fark anlamsızdır (anayasa §1) |
| 4 | yedek kaynak | aynı bilgi `/proc/meminfo` ve `/proc/loadavg`'den de alınır (M-W19, NOTES) | iki kaynak **toplanmaz** — aynı verinin iki yüzeyidir |
| 5 | repo durumu | `sysinfo` deseni repo'da 0 isabet (§8 ile aynı tarama) | yalnız tarif |

| # | sysinfo ↔ /proc yüzeyi | Ortak veri | Fark |
|---|---|---|---|
| 1 | yük | `loads[3]` ↔ `/proc/loadavg` | /proc ek olarak hazır + D-durum kırılımını verir |
| 2 | bellek | `freeram` ↔ `/proc/meminfo` | /proc ayrıntılı kalem listesidir |
| 3 | süreç | `procs` ↔ `/proc/loadavg` süreç alanı | ikisi de anlık gauge |
| 4 | seçim | ikisi birlikte **değil**, biri seçilir | iki kaynak çelişirse okuma koşulu raporlanır |

## §17 getloadavg(3) — üç örnekle yük okuma

**Kanıt:** getloadavg(3) (M-W20, 2026-10-13 webfetch HTTP 200).

| # | Davranış | Kural | Kaynak |
|---|---|---|---|
| 1 | örnek sayısı | en fazla 3 örnek okunur (1/5/15 dk) | M-W20 |
| 2 | istek 3'ü aşarsa | yine en fazla 3 örnek yazılır — 4. değer **uydurulmaz** | M-W20 |
| 3 | okuma başarısız | `-1` döner | M-W20 |
| 4 | `-1` sonucu | hata sayılır; yük "0" **yazılmaz** | §2 kural 3 |

| # | getloadavg(3) ↔ uptime(1) | getloadavg(3) | uptime(1) |
|---|---|---|---|
| 1 | arayüz | kütüphane çağrısı (program içi) | komut çıktısı (okunur) |
| 2 | veri | aynı 1/5/15 dk ortalaması | aynı 1/5/15 dk ortalaması |
| 3 | kullanım anı | kod içinde ölçüm | insan/otomasyon okuması |
| 4 | hata yüzeyi | `-1` dönüşü | çıkış/metin biçiminde hata |
| 5 | repo durumu | repo'da yok (§8) | repo'da yok (§3) |

| # | Yükleme kuralı | Neden |
|---|---|---|
| 1 | Yük yalnız CPU **değildir** (D durumu dahil) — §4 kural 2 | I/O bekleme yükü şişirir |
| 2 | 1/5/15 dk üç ayrı pencere; biri diğerinin ortalaması **değildir** | §4 kural 3 |
| 3 | Çekirdek sayısı okunmadan load eşiği yazılmaz | O2 [UNKNOWN] |
| 4 | Üç pencere tek raporda verilir; yalnız biri seçip genelleme yapılmaz | dar pencere yanıltıcıdır |

## §18 perf top — gerçek zamanlı profil okuması

**Kanıt:** perf top(1) (M-W21, 2026-10-13 webfetch HTTP 200).

| # | Özellik | Ne yapar | Not |
|---|---|---|---|
| 1 | gerçek zamanlı | sürekli güncellenen profil tablosu | `perf record` gibi dosyaya yazma zorunluluğu yoktur |
| 2 | OVERHEAD CALCULATION | bölüm adıyla kaynakta tanımlanır | self/children overhead ayrımı |
| 3 | self overhead | olayın doğrudan bulunduğu yerin payı | kaynaktaki tanıma göre yorumlanır |
| 4 | children overhead | çağrılan alt süreç payı | self ile **toplanmaz** (ayrı tanımlar) |

| # | perf top ↔ perf stat | perf top | perf stat (M-W16) |
|---|---|---|---|
| 1 | çıktı | anlık profil tablosu | süreç bitiminde toplam rapor |
| 2 | mod | okuma/yorumlama yüzeyi | sayma modu sarmalayıcısı (§3) |
| 3 | dosya | dosyaya yazma zorunlu değil | — |
| 4 | kullanım anı | teşhis anı | ölçüm oturumu |
| 5 | repo durumu | çağrılmıyor (§8) | çağrılmıyor (§3) |

| # | Dürüstlük kuralı | Uygulama |
|---|---|---|
| 1 | overhead yüzdesi bu dosyada **yazılmaz** | okunmuş değer yok → `UNKNOWN` |
| 2 | "overhead düşüktür/yüksektir" iddiası kurulmaz | kaynak okundu ama sayı envantere girmedi |
| 3 | tablo eşiği/sütun genişliği gibi kozmetik ayrıntılar taşınmaz | anlama katkı yok |
| 4 | `perf top` çıktısı ölçüm **değildir**, yorum yüzeyidir | süreklilik ≠ doğruluk |

## §19 Hata kataloğu — okuma hatası ve dürüst tepki

| # | Hata/hal | Belirti | Dürüst tepki |
|---|---|---|---|
| 1 | yetki reddi (CAP_PERFMON / perf_event_paranoid) | sayaç açılamaz (§2) | hata sayılır, 0'a düşürülmez |
| 2 | syscall dönüş değeri başarısız (sysinfo) | veri yok | `UNKNOWN` — varsayılan yazılmaz |
| 3 | getloadavg `-1` | okuma başarısız | hata raporlanır; yük "0" **denmez** |
| 4 | `/proc` dosyası yok / izin yok | okuma boş | dosya yolu envanterde `[UNKNOWN]` |
| 5 | alan sırası kayması | yanlış alan okunur | ayrıştırıcı dosya adına bağlanır (anayasa §2 kural 2) |
| 6 | `mem_unit` unutulursa | birim hatası | çarpan bayt-değerin tamamına uygulanır |
| 7 | sayaç sıfırlanması (servis yeniden start) | negatif fark | negatif fark **raporlanır**, 0'a yuvarlanmaz (anayasa §1) |
| 8 | cgroup kökü bilinmiyor | yol bulunamaz | yol `[UNKNOWN]` (§7 kural 3) |
| 9 | iki kaynaktan çelişen okuma (sysinfo ↔ /proc) | değer farklı | koşul raporlanır; ortalaması **alınmaz** |
| 10 | log eşik olayı metrik sanılırsa | olay sayısı ile değer karışır | olay log kaydıdır (§6) |
| 11 | örnek süreç ömrü (sleep 10) periyot sanılır | ölçüm penceresi şaşar | örnek değerdir, periyot değil (anayasa §4) |

## §20 Senaryo A — "sunucu neden yavaş?" okuma akışı (tarif)

| # | Adım | Araç/kavram | Soru | Kanıt |
|---|---|---|---|---|
| 1 | yük penceresi | uptime / getloadavg | yük 1/5/15 dk ne? | M-W17, M-W20 |
| 2 | CPU mu I/O mu | pidstat (§3) | süreç ne bekliyor? | M-W5 |
| 3 | disk G/Ç | iostat (§3) | G/Ç gecikmesi var mı? | M-W2 |
| 4 | bellek | sysinfo / free (§16, anayasa §7) | baskı altında mı? | M-W19, M-W7 |
| 5 | süreç sayısı | sysinfo `procs` | süreç şişmesi var mı? | M-W19 |
| 6 | sınır | cgroup v2 (§7) | limit altında mı? | M-W13 |
| 7 | eşik olayı | dmesg/journald (§6) | OOM/softlockup kaydı var mı? | L-W5, M-W13 |
| 8 | profil | perf top (§18) | nereye zaman gidiyor? | M-W21 |
| 9 | sonucu | — | — | — |

| # | Senaryo kuralı | Neden |
|---|---|---|
| 1 | her adım tek başına **sonuç** vermez; sıra izlenir | yük tek başına suçlu bulmaz (§4) |
| 2 | adımlarda sayısal eşik **okunmaz/yazılmaz** | O2 [UNKNOWN] |
| 3 | repo bu akışın **hiçbir adımını** çağırmaz (§8) | tanımsızlık (O1) |
| 4 | sonuç log/olay ise `log` dalına, sayı ise `metrik` dalına yazılır | dal sınırı (§12) |
| 5 | ölçüm yapılmadan "şüpheli" etiketi konmaz | tahmin ≠ kanıt |

## §21 Senaryo B — periyot kararı (sayısız çerçeve)

| # | Soru | Ne ölçülür | Sonucu |
|---|---|---|---|
| 1 | okuma maliyeti | /proc okuması syscall I/O'dur (anayasa §2 kural 3) | sık okuma I/O üretir |
| 2 | veri hızı | sayaç artış hızı okunmadan bilinmez | `UNKNOWN` |
| 3 | hedef tazelik | ne kadar güncellik gerekli? | O2 [UNKNOWN] |
| 4 | karar sahibi | toplama ajanı tasarımı (K002-altyapi) | bu dal yalnız kuralı tarif eder |
| 5 | ölçüm | periyot alternatifleri okunmadan denenmez | bu turda ölçüm YOK |

| # | Bağlayıcı çerçeve | Değer |
|---|---|---|
| 1 | periyot sayı olarak bu dosyaya **yazılmaz** | ölçüm yok → `UNKNOWN` |
| 2 | periyot değişince seri işaretsiz karşılaştırılmaz | ölçek kayması (§5 kural 3) |
| 3 | periyot ≠ `sleep 10` örnek değeri (anayasa §4 kural 2) | örnek süreç ömrüdür |
| 4 | sayma modunda periyot = okuma sıklığı; örnekleme modunda kaydedicinin işidir | modlar farklı (§5) |
| 5 | periyot kararı tek başına değil, I/O bütçesiyle verilir | §5 kural 1 |

## §22 Repo ↔ CI ↔ OS metrik sınıflandırma matrisi

**Kanıt:** `.github/workflows/ci.yml` tam dosya okundu (2026-10-13, 203 satır / 6 job).

| # | Yüzey | Repo (disk) | CI (ci.yml disk) | OS (doğrulanmış) |
|---|---|---|---|---|
| 1 | süre ölçümü | `hrtime`/`microtime` (§9) | yok | clock_gettime (M-W10) |
| 2 | sayaç okuma | yok (§8) | yok | perf_event_open (M-W18) |
| 3 | yük okuma | yok | yok | uptime / getloadavg (M-W17, M-W20) |
| 4 | test çıktısı | — | `php-test` job (ci.yml:66–108) | — |
| 5 | kapsam eşiği | — | `%80` yorumu (ci.yml:165–166) | — |
| 6 | OS araç çağrısı | 0 isabet (§8) | 0 isabet (6 job tam okundu) | — |
| 7 | job soft davranışı | — | `continue-on-error` (ci.yml:167, 183) | — |

| # | CI metriği **değildir** | Neden |
|---|---|---|
| 1 | GitHub Actions çalışma süresi | CI servisi verisidir; OS metriği değil |
| 2 | job başına süre | aynı gerekçe — `K017`/DevOps kapsamı |
| 3 | `%80` kapsam eşiği | kalite eşiği, OS sayaç değeri değil |
| 4 | `continue-on-error` durumu | CI kapı davranışı, ölçüm sonucu değil |
| 5 | `timeout-minutes` değeri (15/10/20) | güvenlik sınırıdır, ölçülen süre **değildir** |

## §23 Bu tur ek doğrulanan kaynaklar (2026-10-13)

| # | ID | İddia | URL | Durum |
|---|---|---|---|---|
| 1 | M-W19 | sysinfo(2) sistem özetini tek çağrıda verir | https://man7.org/linux/man-pages/man2/sysinfo.2.html | 200 (bu tur) |
| 2 | M-W20 | getloadavg(3) en fazla 3 yük örneği verir, hatada `-1` | https://man7.org/linux/man-pages/man3/getloadavg.3.html | 200 (bu tur) |
| 3 | M-W21 | perf top(1) gerçek zamanlı profil gösterir (OVERHEAD CALCULATION) | https://man7.org/linux/man-pages/man1/perf-top.1.html | 200 (bu tur) |

## §24 Reddedilen adaylar (bu tur)

| # | Aday URL | Sonuç | Karar |
|---|---|---|---|
| 1 | https://docs.kernel.org/trace/irqsoff-tracer.html | HTTP 404 | kullanılmadı |
| 2 | https://man7.org/linux/man-pages/man8/perf-sched.8.html | HTTP 404 | kullanılmadı |
| 3 | https://man7.org/linux/man-pages/man8/logger.8.html | HTTP 404 | log dalına alınmadı |
| 4 | PDH toplama sayfası (kök `-dogrulama.md` §4 X6) | HTTP 404 | ⚠️ korunur |
| 5 | `vmstat.1` (kök `-dogrulama.md` §4 X2) | HTTP 404 | kullanılmadı |

## §25 Sık karıştırılan cümleler — düzeltme matrisi

| # | Yanlış cümle | Doğru cümle | Kanıt |
|---|---|---|---|
| 1 | "uptime verdiği için metriktir" | load okumak **gauge** okumadır; artış hızı üretmez | §4, anayasa §1 |
| 2 | "perf stat ile örnekleme yapılır" | perf stat sayma modudur; örnekleme `perf record` (trace) | §1, §3 |
| 3 | "sysinfo /proc okumaktır" | ayrı syscall'dır; veri yüzeyi kesişir | §16 kural 4 |
| 4 | "getloadavg beş örnek döner" | en fazla 3 örnek; hatada `-1` | M-W20 |
| 5 | "load 2.0 = %200 CPU" | load CPU oranı **değildir**; D durumu da sayar | §4 kural 2 |
| 6 | "CI job süresi OS metriğidir" | CI servisi verisidir | §22 |
| 7 | "hrtime latency metriğidir" | uygulama içi aralıktır | §9 |
| 8 | "cgroup cpu.stat gauge'tır" | monotonik sayaçtır | §7 |
| 9 | "periyot 10 saniye olmalı" | periyot envanterde yok → `UNKNOWN` | §5 |
| 10 | "repo'da OS metriği yok = eksiklik" | tanımsızlıktır (O1) | §8 |
| 11 | "0 isabet = sistem temiz" | yalnız repo yüzeyinde yoktur; OS tarafı ölçülmedi | §8 |
| 12 | "yumuşak job sessizce geçer" | `continue-on-error` işi kırmaz, logda görünür kalır | §22, ci.yml:167,183 |

## §26 Bölüm → kanıt haritası (bu dosya için)

| # | Bölüm | Birincil kanıt | Kanıt türü | Durum |
|---|---|---|---|---|
| 1 | §0 ELI15 | bu dosyanın tamamı | sentez | — |
| 2 | §1 perf_event_open iki mod | perf_event_open(2) (M-W18) | URL | HTTP 200 |
| 3 | §2 yetki sınırı | perf_event_open(2) (M-W18) | URL | HTTP 200 |
| 4 | §3 procps sarmalayıcıları | M-W2, M-W3, M-W5, M-W16, M-W17 | URL | HTTP 200 |
| 5 | §4 uptime/load | uptime(1) (M-W17) | URL | HTTP 200 |
| 6 | §5 periyot kararı | — (karar O2'de) | açık soru | UNKNOWN |
| 7 | §6 OS eşik olayları | M-W13, M-W14, L-W9 | URL + ⚠️ | kısmi ⚠️ (hung task) |
| 8 | §7 cgroup v2 | cgroup-v2 (M-W13) | URL | HTTP 200 |
| 9 | §8 olumsuz kanıt | grep (6 desen) | disk | 0 isabet |
| 10 | §9 repo süresi | Read (5 dosya:satır) | disk | eşleşti |
| 11 | §10–§13 platform | ⚠️ (macOS, Windows) | doğrulanmadı | ⚠️ |
| 12 | §14 kaynakça | webfetch | URL | 4 × 200 |
| 13 | §15 açık sorular | — | açık soru | O1–O3 |
| 14 | §16 sysinfo(2) | sysinfo(2) (M-W19) | URL | 200 (bu tur) |
| 15 | §17 getloadavg(3) | getloadavg(3) (M-W20) | URL | 200 (bu tur) |
| 16 | §18 perf top | perf top(1) (M-W21) | URL | 200 (bu tur) |
| 17 | §19 hata kataloğu | §1–§18 sentezi | sentez | — |
| 20 | §20 senaryo A | M-W2, M-W5, M-W13, M-W17, M-W19, M-W21 | sentez | tarif |
| 21 | §21 senaryo B | — (periyot O2) | açık soru | UNKNOWN |
| 22 | §22 CI/OS matrisi | ci.yml tam okuma (203 satır) | disk | 6 job |
| 23 | §23 bu tur kaynaklar | webfetch | URL | 3 × 200 |
| 24 | §24 red adaylar | webfetch | URL | 404 kaydı |
| 25 | §25 sık karıştırma | §1–§24 sentezi | sentez | — |

| # | Harita kuralı | Neden |
|---|---|---|
| 1 | her § en az bir kanıt türüne bağlanır | kanıtsız bölüm sayılmaz |
| 2 | `⚠️` ve `UNKNOWN` hücreleri haritada da görünür | gizli belirsizlik yasak |
| 3 | bu tablo **kaynak özetidir**, yeni iddia **taşımez** | mükerrer kanıt üretmez |
| 4 | §5/§13/§15 hücrelerinin boşluğu bilinçlidir | karar henüz verilmedi (O1–O3) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
