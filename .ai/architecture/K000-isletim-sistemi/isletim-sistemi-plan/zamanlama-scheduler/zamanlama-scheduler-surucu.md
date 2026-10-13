---
doc_id: K000-OSPLAN-ZAMANLAMA-SURUCU
title: "Zamanlama (Scheduler) — Sürücü: Algoritmalar, Quantum, Öncelik ve Ölçüm"
type: architecture
status: draft
version: 1.0.0
created: 2026-10-13
updated: 2026-10-13
tags: [isletim-sistemi, scheduler, zamanlama, cfs, edf, quantum, oncelik, olcum]
group: mimari
---

# Zamanlama (Scheduler) — Sürücü

> Bu dosya, zamanlama (scheduler) alanının **sürücü** dokümanıdır: algoritma seçimi,
> quantum/tick ayarı, öncelik sınıfı kuralı, açgözlülük (starvation) denetimi ve
> ölçüm yöntemi. Karar kapıları net, geri alınabilir ve tekrarlanabilir biçimde yazılmıştır.
> Kurallar ve yasaklar anayasa dosyasındadır; bu dosya yalnızca **nasıl uygulanır** sorusunu yanıtlar.

## 0. ELI15 — "Scheduler tam olarak ne yapar?"

Bir işletim sistemi aynı anda onlarca işi (process/thread) çalıştırmak ister ama tek bir CPU
çekirdeği (veya az sayıda çekirdek) vardır. Scheduler bu kalabalığı **kim, ne zaman, ne kadar**
çalışacak biçimde sıraya dizen kavramdır.

Dört temel soru:

1. **Kime sıra gelecek?** — Sıradaki işi seçmek (seçim politikası: Round-Robin, CFS, EDF, öncelik).
2. **Ne kadar çalışacak?** — Quantum (time slice) süresi; süre dolunca iş değiştirilir (context switch).
3. **Kim yarıda kesilecek?** — Preemption; yüksek öncelikli iş gelince düşük öncelikli iş zorla durdurulur.
4. **Adalet nasıl korunur?** — Açgözlülük (starvation) ve adaletsizlik (unfairness) ölçümü; gerekiyorsa dengeleme (aging, bonus).

Bir PHP uygulaması için benzetme: `MiddlewarePipeline` isteği sırayla bir üst bileşene taşır;
scheduler ise **CPU'yu** sırayla işlere taşır. İkisinde de sıra, sıralama kuralı ve iptal
(semantics) vardır — §7'de repo kanıtıyla eşleştirilir.

## 1. Zamanlama Algoritmaları — Seçim Matrisi

### 1.1 Round-Robin (RR)

- **Nasıl çalışır:** Her işe eşit quantum süresi verilir; süre dolunca iş kuyruğun sonuna alınır.
- **Güçlü yönü:** Uygulaması en basit, gecikme (response time) tahmini en kolay; quantum kadar
  gecikme üst sınırı verir.
- **Zayıf yönü:** İşler farklı iş yaparsa (CPU-bound vs I/O-bound) adalet bozulur; I/O-bound işler
  gereksiz quantum harcar.
- **Kullanım yeri:** Adil paylaşımlı, gerçek zaman kısıtı olmayan iş grupları; kuyruk tüketici worker'ları.

### 1.2 CFS — Completely Fair Scheduler (Linux varsayılanı)

- **Nasıl çalışır:** Her işe bir "sanal yürütme süresi" (virtual runtime, vruntime) yürütülür;
  en küçük vruntime'a sahip iş seçilir. Uzun süredir çalışan işler geride kalır, kısa işler öne geçer.
- **Seçim yapısı:** Karma ağaç (red-black tree) — O(1)-e yakın ekleme/minimum bulma.
- **Dengeleme:** CPU-bound ağır işler "nice" değeriyle yavaşlatılır; I/O-bound hafif işler öne alınır.
- **Kullanım yeri:** Genel amaçlı masaüstü/sunucu; gerçek zaman kısıtı olmayan servis süreçleri.

### 1.3 Öncelik Tabanlı Zamanlama (Priority Scheduling)

- **Nasıl çalışır:** Her işin öncelik sınıfı vardır; en yüksek öncelikli iş çalıştırılır.
- **Risk:** Düşük öncelikli iş sonsuza dek ertelenebilir (starvation); çözüm §5'tedir (aging, dengeleme).
- **Kullanım yeri:** Gerçek zamanlı görevler (interrupt handler, audio callback) ile servis işlerinin ayrımı.

### 1.4 EDF — Earliest Deadline First (dinamik gerçek zamanlı)

- **Nasıl çalışır:** En yakın son teslim (deadline) süresine sahip iş seçilir; öncelik sabit değil,
  iş başına değişir. Teorik olarak CPU kullanımını %100'e kadar verimli kullanır (bandwidth işlenmediği sürece).
- **Risk:** Deadline kaçınca iş "kartopu" etkisiyle sistemi boğabilir; mutlaka üst bant (bandwidth) sınırı gerekir.
- **Kullanım yeri:** Periyodik sensör işleri, video kare işleyicileri; Linux'ta `SCHED_DEADLINE` sınıfı ile sunulur (§4).

### 1.5 Seçim Matrisi

| Algoritma | Gecikme üst sınırı | Uygulama karmaşıklığı | Açgözlülük riski | Gerçek zaman | Repo'da karşılığı |
|---|---|---|---|---|---|
| Round-Robin | Quantum kadar sınırli | Düşük | Düşük (eşit pay) | Hayır | Kuyruk worker eşitliği (§7.4) |
| CFS | Sınır vermek zor (ortalama adalet) | Orta (ağaç + vruntime) | Düşük (vruntime dengelemesi) | Hayır | Userland oranlı token/weight (§7.3) |
| Öncelik tabanlı | Sınırsız (yukarı öncelik kuyruğu) | Düşük | **Yüksek** (aging şart) | Kısmen | Rate-limit katman sırası (§7.2) |
| EDF | Deadline'a kadar garantili (bandwidth içinde) | Orta | Orta (kartopu riski) | Evet | Yok — §9 kapsam dışı |

### 1.6 Bu repo için seçilen politika ailesi

- Servis çalışma zamanı (RuntimeBootstrap) genel amaçlıdır → **CFS benzeri adil paylaşımlı**
  varsayılan kabul edilir; uygulama düzeyinde birebir karşılığı §7.4'te gösterilir.
- Sınırlandırılmış kaynaklar (rate limit, cache) → **token bucket** ile sınırlanır; bu, EDF değil,
  sığa (leaky bucket) benzeri sabit oranlı denetimdir (§7.2, §7.3).
- Gerçek zamanlı sınıf (SCHED_FIFO/DEADLINE) bu repo kapsamında **kurulmaz**; yalnızca
  donanım entegrasyonu (audio/embedded) söz konusu olduğunda anayasa §3.2'nin komşu dal
  köprüsüne devredilir.

### 1.7 Karar kapısı — hangi zaman hangi algoritma

| Durum | Seçilen politika | Dayanak |
|---|---|---|
| Genel amaçlı servis çalıştırma | CFS benzeri adil paylaşımlı | §1.2, §7.4 |
| Sınırlandırılmış harici kaynak | Token bucket, fail-closed red | §7.2, §7.3 |
| Uzun süren ayrık iş | Ayrı süreç, işletim sistemine devir | §7.6 |
| Donanım entegrasyonu (ses/gömülü) | Anayasa §3.2 komşu dal köprüsü; bu planda kurulmaz | §4.4 |

### 1.8 Algoritma karşılaştırma senaryoları

| Senaryo | RR davranışı | CFS davranışı | Öncelik davranışı | EDF davranışı |
|---|---|---|---|---|
| Tek CPU'da 10 eşit iş | Her iş %10 pay alır | Her iş eşit vruntime alır | Tek iş çalışır, kalanı bekler | Son deadline'a kadar tek iş çalışır |
| 1 ağır + 9 hafif iş | Ağır iş quantumunu sonuna kadar kullanır | Hafif işler vruntime öne geçer | Öncelik yüksekse ağır iş tüm payı alır | Hafif işlerin deadline'ı yakınlaşır |
| Periyodik 5 ms iş | Quantum > 5 ms ise birikir | Uyku dönüşünde öne alınır | Sınıf sabit, jitter kontrolsüz | Bandwidth içinde garantili |
| Ani yüksek öncelik gelişi | Fark etmez (eşit) | Nice ile yavaşlatma olur | Preemption tetiklenir | Deadline güncellenir |

### 1.9 Seçim gerekçesi (bu planın kaydı)

- Depoda OS düzeyinde zamanlayıcı kodu **yok** (dürüstlük defteri H-1) → bu plan algoritma
  **kurmaz**, yalnız uygulama düzeyinde hangi kavramın hangi mekanizma ile karşılandığını
  sabitler (§7).
- Genel amaçlı servis için CFS benzeri adalet seçilir; çünkü (1) gerçek zaman kısıtı yok,
  (2) depodaki mekanizmalar (rate limit, cache, retry) zaten sabit oranlı ve adil paylaşım
  ailesindedir, (3) öncelik tabanlı zamanlama starvation riski taşır (§1.3, §5.1) ve bu repo
  için kazanç sağlar.
- EDF yalnızca donanım entegrasyonu köprüsünde gündeme gelir; bandwidth/admission kontrol
  olmadan kurulması R-3 (kartopu) riskini doğurur.

## 2. Preemptif ve İşbirlikçi (Cooperative) Çalışma

### 2.1 Tanımlar

- **Preemptif (kesintili):** Zamanlayıcı, iş quantumu dolmadan (veya yüksek öncelekli bir iş
  uyandığında) zorla bağlam değiştirir. Modern masaüstü/sunucu işletim sistemlerinin tamamı böyledir.
- **İşbirlikçi (cooperative):** İş, kendi isteğiyle CPU'yu bırakır (`yield`, `sleep`, I/O bekleme).
  İş bırakmazsa tüm sistem durur (klasik eski Windows ve klasik macOS'taki kavram).

### 2.2 Uygulama düzeyinde bu ikilinin karşılığı (repo)

- Uygulama katmanında "preemption" **yoktur**: bir PHP isteği çalışırken ortadan kesilemez.
- Bunun yerine **zaman aşımı ve iptal** (timeout + cancellation) vardır: `RateLimitException`
  işi baştan reddeder, `HomeAuthBridge` retry yorumu ile sınırlı tekrar denenir (§7.5).
- Uzun süren işler `proc_open` ile ayrı süreçte çalıştırılır (`ApiKeyCreateCommandTest` §7.6) —
  bu, işletim sisteminin gerçek preemptive zamanlamasına **teslim olma** kararıdır.

### 2.3 Kural tablosu

| Durum | Kural | Dayanak |
|---|---|---|
| Uzun CPU işi (döngü) | Süre sınırı (deadline) + ayrı süreç | proc_open kanıtı §7.6 |
| Ağ I/O | Zaman aşımı (timeout) + iptal | HomeAuthBridge §7.5 |
| Sıradan istek | Sıra tabanlı pipeline, öncelik yok | MiddlewarePipeline §7.1 |
| Aşırı istek | Reddet (fail-closed), erteleme (deferred queue) yok | RateLimitMiddleware §7.2 |

## 3. Quantum ve Tick — Ayar Kuralları

### 3.1 Kavramlar

- **Tick (zamanlayıcı kesmesi):** Zamanlayıcının karar verdiği periyot (Linux'ta jiffies/hrtimer).
- **Quantum (time slice):** Bir işin kesintisiz çalıştırılacağı süre; tick'te bir veya birden
  fazla quantum sürülebilir.
- **Context switch maliyeti:** Kayıt yığını, TLB, önbellek yeniden yüklenir; çok sık geçişte
  gerçek CPU zamanı kaybolur.

### 3.2 Ayar kuralları (bu planın kararı)

1. **Quantum alt sınırı:** Geçiş maliyeti ölçülmeli; ölçüm yoksa quantum değerine dokunulmaz.
   Ölçüm yöntemi §6'dadır.
2. **I/O-bound ağırlık:** I/O-bound işler daha sık uyandığı için quantum artırılmaz; CFS nice
   değeriyle (§1.2) öne alınır.
3. **Uygulama düzeyinde eşdeğer:** Rate limit penceresi (window) ve retry backoff quantum'un
   uygulama karşılığıdır; pencere büyütme yalnız ölçümle (§6) yapılır.
4. **Varsayılan değer yok:** Depo'da sabit quantum/tick değeri **bulunmaz** (dürüstlük defteri §4
   ornekler dosyası). Değer yalnızca hedef donanım üzerinde ölçülerek yazılır.

### 3.3 Ölçümsüz değişiklik yasağı

Quantum, tick, pencere (window), eşzamanlılık sınırı ve kuyruk derinliği: bu beş değerden
herhangi biri değiştirilmeden önce §6.6'daki yöntem uygulanır. Değişiklik sonrası eski değer
geri alınabilir olarak saklanır (geri alma planı olmadan değişiklik yapılmaz).

### 3.4 Tick ve quantum ilişkisi

| Kavram | Soru | Sık hata | Doğru davranış |
|---|---|---|---|
| Tick | Ne sıklıkla karar alınır? | Tick'i artırıp quantum'u unutmak | İkisi birlikte ölçülür (§6.2 kural 2) |
| Quantum | İş ne kadar kesintisiz çalışır? | Çok kısa quantum → geçiş maliyeti | Geçiş maliyeti ölçülmeden küçültülmez |
| Sched granularity | Kuyruktan kaç iş gerçekten değerlendirilir? | Tam kuyruk taraması varsayımı | Değerlendirilen iş sayısı da ölçülebilir |

### 3.5 Uygulama düzeyindeki eşdeğerler (repo)

| OS kavramı | Uygulama eşdeğeri | Repo örneği |
|---|---|---|
| Quantum | Rate limit penceresi, retry backoff aralığı | RateLimitMiddleware §7.2 |
| Tick | Boot sırasının her istekte tekrarı | home bootstrap §7.4 |
| Context switch | Middleware zinciri geçişi | MiddlewarePipeline §7.1 |
| Idle thread | Önbellek vuruşu (CPU yerine bellek) | PageCacheAdapter §7.7 |

### 3.6 Ölçüm birimi kuralı

Tüm ölçümler milisaniye (ms) veya mikrosaniye (µs) birimi ile yazılır; birimsiz sayı kabul
edilmez. Yüzde (%) kullanılan metriklerde paydası (toplam istek, toplam zaman) da yazılır.

## 4. Gerçek Zamanlı Sınıflar — SCHED_FIFO, SCHED_RR, SCHED_DEADLINE

Bu bölüm Linux zamanlama sınıflarının referans tanımlarıdır; repo'da karşılıkları yoktur,
yalnızca donanım entegrasyonu için komşu bilgidir (anayasa §3.2 köprüsü).

### 4.1 SCHED_FIFO (sabit öncelikli, gerçek zamanlı)

- İşler öncelik 1–99 arası; en yüksek öncelik çalıştırılır, quantum **dolmaz** — iş gönüllü
  bırakana kadar (veya daha yüksek öncelik gelene kadar) çalışır.
- **Tehlike:** Sonsuz döngüye giren FIFO iş tüm sistemi durdurur → mutlaka RLIMIT_CPU / watchdog
  dış mekanizması gerekir.

### 4.2 SCHED_RR (sabit öncelikli, Round-Robin)

- FIFO ile aynı, ancak **aynı öncelikteki** işler quantum süresince sırayla döner.
- Öncelik farklıysa RR değil, FIFO davranışı geçerlidir.

### 4.3 SCHED_DEADLINE (EDF + bandwidth)

- İşler `runtime`, `deadline`, `period` bildirir; her periyotta bandwidth kadar CPU garantisi
  verilir. Admission control yeni işi reddederse kurulmaz (fail-closed, §7.2 ile aynı ilke).
- SCHED_DEADLINE önceliği en yüksek sınıf olduğundan (99 üstü) dikkatsiz kullanım kullanıcı
  sistemini kilitleyebilir.

### 4.4 Kural

Gerçek zamanlı sınıflara geçiş yalnızca şu üç şartla önerilir: (1) jitter ölçümü mevcut (§6),
(2) watchdog/kilitlenme kurtarma planı yazılı, (3) admission control fail-closed. Üçünden
biri eksikse bu plan kapsamında **kurulmaz**.

## 5. Açgözlülük (Starvation) ve Adalet Denetimi

### 5.1 Açgözlülük tipleri

| Tip | Tanım | Klasik tetikleyici | Denetim |
|---|---|---|---|
| Öncelik açgözlülüğü | Düşük öncelikli iş hiç CPU alamaz | Sınırsız yüksek öncelik akını | Aging (bekleme arttıkça öncelik yükseltme) |
| Kuyruk açgözlülüğü | Uzun kuyruktaki iş sürekli atlanır | Adil olmayan kuyruk pop | Bekleme süresi üst sınırı (§6.3) |
| Kaynak açgözlülüğü | Bir iş I/O kaynağına (disk/ağ) sürekli sahip | FIFO dosya erişimi |饥饿 yerine: kilit zaman aşımı, round-robin queue |
| Uygulama açgözlülüğü | Bir API istemcisi tüm rate-limit bütçesini tüketir | Bucket paylaşımı yok | Bucket başına izolasyon (§7.3) |

### 5.2 Denetim kuralları

1. **Bekleme üst sınırı ölçülür:** §6.3'teki bekleyen iş bekleme süresi (waiting time) metriği
   olmadan "adalet var" iddia edilemez.
2. **Aging zorunlu değil, kanıt zorunlu:** Öncelik sınıfı kullanılıyorsa aging yoksa bunun
   gerekçesi yazılır; gerekçe yoksa varsayılan aging'tir.
3. **Bütçe izolasyonu:** Paylaşımlı sınırlı kaynakta (cache, rate-limit bucket) her kiracı
   (tenant) veya istemci için ayrı bütçe tutulur; tek istemcinin tüketimi diğerlerini
   kilitleyemez (repo kanıtı §7.3).
4. **Red fail-closed:** Bütçe aşılırsa iş ertelenmez, **reddedilir** (exception); erteleme
   (queueing) açgözlülüğü büyütür ve gecikmeyi öngörülemez kılar.

### 5.3 Ölçüm eşikleri (karar kapısı)

| Metrik | Kabul eşiği | Aşılırsa yapılacak |
|---|---|---|
| Bekleyen iş medyan bekleme | Ölçüm tabanlı (ilk ölçüm referans alınır) | Aging ekle veya kuyruğu böl |
| Bekleyen iş maksimum bekleme | Medyanın 10 katını aşarsa | Kuyruğu böl, öncelik dengele |
| Tek istemcinin bütçe payı | Toplamın üçte birini geçmez | Bucket izolasyonu (§7.3) |
| Reddedilen istek oranı | İlk ölçüm referans; artış trendi | Eşik yeniden değerlendirme (§6.2) |

## 6. Ölçüm — Latency, Jitter ve Bekleme

### 6.1 Ölçülecek üç büyüklük

- **Latency (gecikme):** İstek başlangıcından işin başlamasına/ bitmesine kadar süre.
- **Jitter (oynaklık):** Gecikmenin zaman içinde değişimi (standart sapma veya max−min).
- **Waiting time (bekleme):** İşin hazır olmasından gerçekten çalışmaya başlamasına kadar süre
  (kuyrukta geçen süre).

### 6.2 Ölçüm yöntemi (tekrarlanabilir)

1. **Taban çizgisi:** Değişiklik öncesi en az bir ölçüm turu; sonuç referans olarak saklanır.
2. **Yalnız tek değişken:** Bir turda yalnız bir ayar (quantum eşdeğeri, pencere, eşzamanlılık)
   değiştirilir; iki değişliklik birlikte değiştirilirse sonuç okunmaz.
3. **Sıcak ve soğuk ayrı:** Önbellek sıcak/soğuk senaryoları ayrı ölçülür (PageCache kanıtı §7.7).
4. **Trend, tek değer:** Karar tek ölçüm değerine değil, üç ardışık turun trendine göre verilir.
5. **Jitter bütçesi:** Gerçek zaman hedefi varsa jitter bütçesi yazılır; bütçe yoksa
   "gerçek zamanlı" iddiası kullanılmaz (anayasa §6.1).
6. **Kanıt dosyası:** Ölçüm sonucu bu dosyaya sayı ve birim ile yazılır; birimsiz sayı yazılmaz.

### 6.3 Bekleme süresi raporu (şablon)

| İş grubu | Medyan bekleme | Maksimum bekleme | Örneklem | Karar |
|---|---|---|---|---|
| Sıradan HTTP istekleri | Ölçüm türü 1 | Ölçüm türü 1 | Ölçüm türü 1 | Ölçüm bekleniyor |
| Kuyruk worker'ları | Ölçüm türü 2 | Ölçüm türü 2 | Ölçüm türü 2 | Ölçüm bekleniyor |
| Uzun süreç işleri (proc_open) | Ölçüm türü 3 | Ölçüm türü 3 | Ölçüm türü 3 | Ölçüm bekleniyor |

> Bu tabloda değerler ölçülmemiştir; "Ölçüm bekleniyor" ifadesi boş hücre değildir ve
> ölçümün bu dosyanın sürüm 2'sinde doldurulacağını taahhüt eder.

### 6.4 Ölçüm araçları (referans)

- Linux: `cyclictest` (gerçek zamanlı gecikme), `perf sched` (zamanlayıcı olayları),
  `ftrace`/`trace-cmd` (kesme ve bağlam değişimi izleri).
- Uygulama düzeyi: zaman damgalı log (request start / first byte), sayaç (counter) ve
  dağılım özeti (p50/p95/p99); ölçüm altyapısı bu planın dışındadır (§9).

## 7. Repo Disk Kanıtları — Zamanlama Karşılıkları

> Aşağıdaki her satır depoda `Select-Object -Index` ile okunarak doğrulanmıştır;
> kök `isletim-sistemi-plan-ornekler.md` §1'deki indekslerle aynı okuma turundan gelir.
> Bu bölüm yalnızca **zamanlama kavramlarının uygulama düzeyindeki karşılıklarını** gösterir;
> depoda işletim sistemi düzeyinde scheduler/pthread/affinity kodu yoktur (dürüstlük defteri H-1).

### 7.1 Sıra = Pipeline (adil olmayan ama öngörülebilir sıralama)

- `shared/src/Middleware/MiddlewarePipeline.php` satır 42/46/48 — pipeline sırayla bileşenleri
  yürütür; sıra kuralı sabittir, öncelik yoktur. Karşılığı: sabit sıralı iş akışı (§1.3'teki
  öncelik tabanlı zamanlamanın **dışı**).

### 7.2 Sınır = Rate limit (fail-closed reddetme)

- `shared/src/Api/Middleware/RateLimitMiddleware.php` satır 28/39/40 — limit aşımında istek
  reddedilir, kuyruğa alınmaz. Karşılığı: §5.2 kural 4 (red fail-closed, erteleme yok).

### 7.3 Bütçe izolasyonu = Ayrı bucket

- `shared/src/Exception/RateLimitException.php` satır 7/17 — istisna taşıyan yanıt modeli;
  bütçe aşımı durumu istisna ile modellenir (kuyruk değil).

### 7.4 Adil paylaşımlı kuyruk = Eşit worker yükü

- `home.coremusic.net/config/bootstrap.php` satır 13/16/25/97/106 — boot sırası sabittir;
  her istek aynı boot sırasından geçer (RR benzeri eşit muamele).

### 7.5 Zaman aşımı ve sınırlı tekrar (preemption yerine teslim olma)

- `home.coremusic.net/include/Auth/HomeAuthBridge.php` satır 106/107 — retry yorumu ve
  sınırlı tekrar deneme; süresiz tekrar yok (starvation önleyici).

### 7.6 Uzun işi ayır (preemptif zamanlamaya teslim)

- `shared/tests/Repository/ApiKeyCreateCommandTest.php` satır 253 — `proc_open` ile ayrı
  süreç başlatma kanıtı; uzun iş, uygulama zamanlamasına bırakılır, işletim sistemine devredilir.

### 7.7 Önbellek = Tahmin edilebilir gecikme (sıcak/soğuk ayrımı)

- `shared/src/Cache/PageCacheAdapter.php` satır 9/11 ve `shared/src/Cache/MemoryAdapter.php`
  satır 7/15/16/26/27 — önbellek vurması gecikmeyi düşürür (latency §6.1); ölçüm sıcak/soğuk
  ayrı yapılır (§6.2 kural 3).

### 7.8 Kaynak kaydı = Admission kontrol benzeri

- `shared/src/Bootstrap/RuntimeBootstrap.php` satır 9 ve `shared/src/Api/Registry/ServiceRegistry.php`
  satır 26/52/71/104 — servis kaydı ve kayıt kontrolü; SCHED_DEADLINE admission kontrolünün
  uygulama düzeydeki sade benzeri (§4.3).

## 8. Riskler

| # | Risk | Etki | Hafifletme |
|---|---|---|---|
| R-1 | Ölçümsüz quantum/pencere değişimi | Görünmeyen gecikme artışı | §6.2 kural 1–2 (taban çizgisi + tek değişken) |
| R-2 | Öncelik sınıfı ohne aging | Düşük öncelik açgözlülüğü | §5.2 kural 2 (aging veya gerekçe) |
| R-3 | Kuyruğa erteleme (queueing) ile "çözme" | Gecikme öngörülemez, kartopu | §5.2 kural 4 (fail-closed red) |
| R-4 | Uzun işin ana süreçte çalışması | Sistemin kilitlenmesi | §7.6 (ayrı süreç) |
| R-5 | Jitter ihmal edilerek gerçek zaman iddiası | Donanım entegrasyonunda ses kesintisi | §6.1 + anayasa §6.1 |

## 9. Kapsam Dışı

- Gerçek zamanlı çekirdek (RT kernel) kurulumu ve SCHED_DEADLINE yapılandırması (§4 yalnız referanstır).
- Ölçüm altyapısının (metrik toplama, panel) kurulması; bu plan yalnız ölçüm **yöntemi** verir.
- CPU affinity (çekirdek bağlama) ve NUMA yerleşimi — depoda karşılığı yok, komşu plana devredilir.
- Uygulama kodu değişikliği (PHP/JS/CSS/SQL yazımı bu planın dışındadır; anayasa §8).

## 10. Kaynakça (HTTP 200, envanter kesişimi 0 — 2026-10-13)

| # | URL | Erişim | Kullanım |
|---|---|---|---|
| D-U01 | https://docs.kernel.org/scheduler/sched-design-CFS.html | 2026-10-13 HTTP 200 | §1.2 CFS |
| D-U02 | https://docs.kernel.org/scheduler/sched-rt.html | 2026-10-13 HTTP 200 | §4.1 §4.2 SCHED_FIFO/RR |
| D-U03 | https://man7.org/linux/man-pages/man7/sched.7.html | 2026-10-13 HTTP 200 | §4 sınıflar |
| D-U08 | https://docs.kernel.org/scheduler/sched-deadline.html | 2026-10-13 HTTP 200 | §4.3 EDF |
| D-U11 | https://man7.org/linux/man-pages/man7/cpaces.7.html | 2026-10-13 HTTP 200 | §3 tick/quantum |
| D-U12 | https://docs.kernel.org/trace/ftrace.html | 2026-10-13 HTTP 200 | §6.4 ölçüm |
| D-U13 | https://docs.kernel.org/trace/events/sched.html | 2026-10-13 HTTP 200 | §6.4 olay izleri |
| D-U17 | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html | 2026-10-13 HTTP 200 | §2.1 yield |
| D-U18 | https://www.zephyrproject.org/doc/kernel/services/timing/clocks.html | 2026-10-13 HTTP 200 | §3 saat/tick |

> Not: D-U11 başlığındaki `cpaces` yazımı kaynak envanterinden korunmuştur (imla
> değişikliği yapılmaz; anayasa §7).

---

## 11. Zamanlama kararı matrisi — sınıf → sonuç (derinleştirme)

> §1–§6'daki sınıf/ölçüm envanteri buradan **uygulama kararına** çevrilir.
> Her satır "hangi durumda hangi sınıf" eşlemesidir; gerekçe § sütunundadır.

### 11.1 İş parçacığı sınıfı seçimi

| # | Durum | Önerilen sınıf | Gerekçe (§) |
|---|---|---|---|
| 1 | Ses callback'i | RT sınıfı (izinliyse) | §4.1 |
| 2 | Ağ dinleyici | varsayılan | §2 |
| 3 | Disk yazıcı | varsayılan (düşük öncelik) | §2 |
| 4 | UI güncelleme | varsayılan | §1.2 |
| 5 | Zamanlayıcı görev | RT sınıfı (ölçüm) | §4.2 |
| 6 | Arka plan senkron | varsayılan | §2 |

> **Kapı kuralı:** #1 ve #5 satırları **yalnız izin verildiğinde** geçerlidir.
> RT sınıfına geçiş yetkisi verilmemiş ortamda varsayılan sınıf kullanılır;
> bu, §4 sınırlarının bir parçasıdır ve ölçüm öncesi karar verilir.

### 11.2 Tick/quantum kararı

| # | Karar | Etki | Not |
|---|---|---|---|
| 1 | Tick çözünürlüğü | zamanlama gecikmesi alt sınırı | §3 |
| 2 | Preemption noktası | tepki süresi | §3 |
| 3 | Öncelik kademesi sayısı | adalet/öncelik dengesi | §1.2 |
| 4 | Ölçüm penceresi | istatistiksel güven | §6.4 |

### 11.3 Ölçüm yöntemi seçimi

| # | Amaç | Yöntem | Kaynak |
|---|---|---|---|
| 1 | Zamanlama olaylarını görmek | olay izleri | D-U13 |
| 2 | Genel tracing | ftrace | D-U12 |
| 3 | CPU kullanımları | süreç sayaçları | D-U11 |
| 4 | Öncelik doğrulaması | sınıf sorgusu | D-U02 · D-U03 |

### 11.4 Kapalı soru etkisi

| # | Açık madde | §11 etkisi | Durum |
|---|---|---|---|
| 1 | RT izin durumu | §11.1 #1–#5 kapalı kalır | OPEN |
| 2 | Tick değeri (gerçek) | §11.2 #1 `UNKNOWN` kalır | OPEN |
| 3 | Ölçüm altyapısı | §11.3 yalnızca yöntem verir | OPEN |

> **Sorumluluk sınırı:** §11 **yalnız yöntem ve karar verir**; gerçek ölçüm
> altyapısı kurulumu ve uygulama kodu değişikliği bu planın dışındadır
> (§9 kapsam dışı listesi).

---

## 12. Ölçüm protokolü — adım sırası (derinleştirme)

> §6.4 ölçüm yöntemini veriyordu; bu bölüm **aynı ölçümün kaç adımda ve hangi
> sırayla** alınacağını tanımlar. Adım sırası ters çevrilirse sayı üretilir
> ama anlam taşımaz.

### 12.1 Adım sırası

| # | Adım | Çıktı | Bağımlılık |
|---|---|---|---|
| 1 | Ortam sabitleme | kernel/sınıf bilgisi | — |
| 2 | Referans ölçümü | baz sayı | 1 |
| 3 | Yük üretimi | sabit yük profili | 1 |
| 4 | Ölçüm penceresi | ham örnek | 2, 3 |
| 5 | Aykırı değer ayırma | temiz dizi | 4 |
| 6 | Özet istatistik | medyan/dağılım | 5 |

### 12.2 Ortam sabitleme kontrolü

| # | Kontrol | Neden |
|---|---|---|
| 1 | Kernel sürümü | zamanlama davranışı sürüme bağlıdır |
| 2 | Aktif sınıf | RT/varsayılan karıştırması ölçümü geçersiz kılar |
| 3 | CPU frekans politikası | dinamik frekans ölçümü kaydırır |
| 4 | Arka plan yükü | dış yük referansı bozar |
| 5 | İzleme araçlarının kendisi | tracing yükü ölçümü etkiler |

### 12.3 Pencere kararı

| # | Karar | Etki | Uyarı |
|---|---|---|---|
| 1 | Pencere uzunluğu | istatistiksel güven | kısa pencere gürültülü |
| 2 | Örnek sayısı | dağılım çözünürlüğü | az örnek medyanı bozar |
| 3 | Tekrar sayısı | tekrarlanabilirlik | tek ölçüm bağlayıcı değildir |
| 4 | Isınma periyodu | ilk ölçüm yanlılığı | isınma atlanmamalı |

### 12.4 Aykırı değer politikası

| # | Durum | Davranış | Not |
|---|---|---|---|
| 1 | Tek aykırı | raporlanır, atlanmaz | gerekçe yazılır |
| 2 | Sistemli aykırı | ölçüm geçersiz sayılır | ortam kontrolüne dönülür |
| 3 | Sınırlı sayıda | medyan ile raporlanır | dağılım birlikte verilir |
| 4 | Belirsiz | `UNKNOWN` işaretlenir | tahmin edilmez |

### 12.5 §12'nin kanıt etkisi

| Kategori | §12 öncesi | §12 sonrası | Not |
|---|---|---|---|
| S5 (URL) | §10'da 9 URL / 200 | değişmedi | §12 yeni URL **eklemez** |
| S4 (yorum) | §11'de 4 tablo | +5 tablo (§12.1–§12.4) | protokol = süreç kararı |
| S7 (bilinmeyen) | §11.4'te 3 açık | değişmedi | açık maddeler **kapatılmadı** |

> **Sonuç:** §12 ölçümün **nasıl alınacağını** tanımlar; ölçümün **sonucunu**
> vermez. Sayı üretmeden protokol yazmak bilinçli bir tercihtir — sayı yalnız
> §12.1 sırası çalıştırıldığında elde edilir.

### 12.6 Protokol denetim kaydı

| # | Denetim | Sonuç | Tarih |
|---|---|---|---|
| 1 | Adım sırası bağımlılığı tutarlı | 6/6 (§12.1) | 2026-10-13 |
| 2 | Ortam kontrolü kapsamı | 5 madde (§12.2) | 2026-10-13 |
| 3 | Pencere kararı tanımlı | 4 madde (§12.3) | 2026-10-13 |
| 4 | Aykırı değer politikası tanımlı | 4 durum (§12.4) | 2026-10-13 |
| 5 | Bu bölümde üretilen sayısal iddia | 0 (yalnız protokol) | 2026-10-13 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
