---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi/is-parcacigi-planlamasi-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "shared/src/Api/Registry/ServiceHealth.php L24-L26 — 'Execute parallel health checks.' yorumu tek süreç içinde"
  - "shared/tests/Repository/ApiKeyCreateCommandTest.php L253 — proc_open ile alt süreç"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html"
  - "https://man7.org/linux/man-pages/man3/pthread_once.3.html"
validation:
  - "2026-10-13: ≥500 satır · 4 URL HTTP 200 · envanter kesişimi 0 · 2 repo satırı Select-Object -Index ile teyit"
open_questions:
  - "P-1: hedef mimaride pthread var mı? [UNKNOWN]"
  - "P-2: kilitlenme tespiti üretime açık mı? [UNKNOWN]"
  - "Plan1: içerik kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# is-parcacigi-planlamasi — Anayasa

> **Tek cümle:** Bu dosya `is-parcacigi-planlamasi` alt-dalının **anayasasıdır** — iş biriminin
> süreç (process) ve iş parçacığı (thread) olarak nasıl kurulduğunu, kilitlenmenin (deadlock)
> hangi koşullarda doğduğunu, senkronizasyonun hangi çerçevede tanımlandığını, kaynak
> kilitlerinin nasıl sıralanacağını ve öncelik devrinin hangi politikaya bağlandığını
> tanımlar; ayrıntılı uygulama `-surucu.md`'dedir.

## §0 — ELI15: bu alt-dal tek cümlede

> **Mutfak düşün:** süreç = **ayrı mutfak** (kendi tezgâhı, kendi dolabı), iş parçacığı =
> **aynı mutfakta çalışan iki aşçı** (tezgâh ortak). İki aşçı aynı tencereye uzanırsa
> **kilitlenme** olur: ikisi de "önce sen" der ve tencereye hiç kimse dokunamaz.

| Kavram | Mutfak karşılığı | OS karşılığı |
|---|---|---|
| Ayrı mutfak | izole tezgâh/dolap | process (adres uzayı) |
| Aynı mutfakta iki aşçı | ortak tezgâh | thread (process içi) |
| Tencereye tek el | tek erişim hakkı | mutual exclusion (mutex) |
| "Önce sen" döngüsü | karşılıklı bekleme | deadlock (circular wait) |
| Ustaya öncelik verme | kıdem devri | priority inheritance |
| Aşçı değiştirmek | iş teslimi | context switch |

**Önemli ayrım:** "Paralel" bir **donanım/başlatma** durumudur; "eşzamanlı" (concurrent) bir
**yürütme** durumudur. Aynı kodun paralel çalıştığı **söylenemez** — yalnızca süreç/thread
sayısı ve CPU sayısı ölçülür; kanıt yoksa `UNKNOWN` yazılır.

## §1 — Kimlik ve konum

| Alan | Değer |
|---|---|
| `node_id` | `K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi/is-parcacigi-planlamasi-anayasa` |
| `node_type` | `module` |
| `parent_id` | `K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi` |
| `status` | `draft` |
| `group` | `mimari` |
| `depends_on` | `isletim-sistemi-kernel` · `isletim-sistemi-runtime` |
| Kademe | Kademe-4 (dosya) · ebeveyn Kademe-3 (klasör) · kök `K000-isletim-sistemi` |

### §1.1 Bu dosyanın neyi taşımıyor

| # | Taşımıyor | Nerede |
|---|---|---|
| 1 | mutex/condvar iç mekanizması (rb-tree, wait queue) | `isletim-sistemi-kernel` |
| 2 | `pthread_create` / `pthread_join` çağrı sözleşmesi (parametre, dönüş tipi) | `isletim-sistemi-runtime` |
| 3 | sıralama algoritması (RR, CFS, quantum) | `zamanlama-scheduler-anayasa.md §3` |
| 4 | bellek/fd kotası | `kaynak-dagilimi-anayasa.md §4` |
| 5 | gerçek repo kodu ve anti-pattern kataloğu | `is-parcacigi-planlamasi-ornekler.md §2-§3` |
| 6 | hata modu ve test stratejisi | `is-parcacigi-planlamasi-surucu.md §7` |

## §2 — Kavram modeli

### §2.1 Süreç (process) ile iş parçacığı (thread) ayrımı

| Özellik | Süreç (process) | İş parçacığı (thread) | Karar |
|---|---|---|---|
| Bellek uzayı (address space) | ayrı, kopyalanmış | aynı, ortak | izolasyon gerekiyorsa süreç |
| Dayanıklılık (crash blast radius) | parçacık çökerse süreç ayakta | parçacık çökerse tüm süreç çöker | kritik hizmette süreç |
| Açılış maliyeti | yüksek (sayfa tablosu kopyası) | düşük (yığın paylaşılır) | yüksek frekansta parçacık |
| Veri paylaşımı | IPC (pipe, socket, paylaşımlı bellek) | doğrudan bellek erişimi | veri yoğun işte parçacık |
| Güvenlik sınırı | ayrı kullanıcı/ yetki bağlamı | aynı yetki bağlamı | yetki ayrımı gerekiyorsa süreç |
| Sıra/öncelik bağımsızlığı | ayrı zamanlama nesnesi | genelde aynı öncelik grubu | bağımsız öncelik gerekiyorsa süreç ayrı |
| POSIX karşılığı | `fork` + `exec` | `pthread_create` | ikisi de bu alt-dalın konusu |
| Windows karşılığı | `CreateProcess` | `CreateThread` | aynı ayrım geçerli |

### §2.2 "Parallel" iddiası ≠ gerçek paralellik

Repo'daki bir yorum "parallel health check" der; bu **iddiadır, kanıt değildir.** Ayrım:

| # | İddia | Kanıt | Ne gösterir | Ne göstermez |
|---|---|---|---|---|
| P-1 | "Execute parallel health checks." | `shared/src/Api/Registry/ServiceHealth.php` **L24** (`      * Execute parallel health checks.`) | kod yazarının **niyeti** | parçacık/fork varlığını |
| P-2 | `public function checkAll(): array` | aynı dosya **L26** (`    public function checkAll(): array`) | tek çağrıda toplu denetim | eşzamanlı yürütme |
| P-3 | Gerçek kullanımda işaretleme | `Get-Content shared/src/Api/Registry/ServiceHealth.php \| Select-Object -Index 25` → L26 | metod imzası | paralel yürütme altyapısı |

**Sonuç (beyan):** repo üretim kodunda `pcntl_*`, `pthread_*`, `parallel`, `Fiber` **gerçek
kullanımı yoktur** (negatif kanıt: `isletim-sistemi-plan-dogrulama.md §3.3 X-1`). "Parallel"
sözcüğü yalnız yorum satırında geçer; bu dosya onu **"eşzamanlı denetim tasarımı"** olarak
yorumlar, paralel donanım iddiası olarak değil.

| # | Terim | Tanım | Kullanım kuralı |
|---|---|---|---|
| T-A | Eşzamanlı (concurrent) | birden çok işin ilerleme durumunun birlikte olması | uygulama için geçerli |
| T-B | Paralel (parallel) | gerçekten aynı anda yürütüm | yalnız ölçümle kanıtlanır |
| T-C | Kritik bölüm (critical section) | yalnız tek süreç/parçacık girebilen kod aralığı | mutex ile korunur |
| T-D | Durumsal paylaşımlı veri (shared state) | birden çok erişilebilir durum | lock veya atomik gerekir |

### §2.3 Senkronizasyon araçları (çerçeve — mekanizma `-kernel`e)

| # | Araç | Temel kural | Ne zaman | Risk |
|---|---|---|---|---|
| S-1 | Mutex | tek sahip, serbest bırakana kadar bloke | kritik bölüm | deadlock, öncelik tersine çevirme |
| S-2 | Recursive mutex | aynı sahip tekrar girebilir | iç içe çağrı | yanıltıcı, genelde kötü tasarım |
| S-3 | Condition variable | "olay beklemek" — mutex ile birlikte | üretici/tüketici | uyanma kaçırması (missed wakeup) |
| S-4 | Semaphore (named/anonim) | sayaç kadar eşzamanlı erişim | kaynak havuzu | aşım (overflow) |
| S-5 | Spinlock | bekleme yerine dönme | çok kısa kritik bölüm, RT | RT olmayanda CPU israfı |
| S-6 | Read-write lock | çok okuma serbest, yazma tek | okuma ağırlıklı | yazıcı açlığı (writer starvation) |
| S-7 | Atomik işlem | kilitsiz tek kelime okuma/yazma | sayaç, bayrak | sıra (ordering) unutulursa hata |
| S-8 | Mesajlaşma (queue) | veri kopyalanarak aktarılır | süreçler arası | kopya maliyeti |

### §2.4 Bellek ve durum ayrımı

| # | Durum | Kime ait | Paylaşım yolu | Kilit gerekir mi |
|---|---|---|---|---|
| M-1 | Statik/yığın (static/stack) | süreç geneli | doğrudan | evet, parçacık paylaşımında |
| M-2 | Parçacık yığını (thread stack) | parçacık | paylaşılmaz | hayır |
| M-3 | Parçacık anahtarı (TLS / `pthread_key_create`) | parçacık | paylaşılmaz | hayır |
| M-4 | Süreç içi önbellek (örn. APCu) | süreç | aynı süreç içindeki parçacıklar | evet |
| M-5 | Paylaşımlı bellek segmenti | birden çok süreç | `shm`/dosya | evet (kilit başka kanaldan) |
| M-6 | Salt okunur yapılandırma | süreç geneli | başlatmada yazılır | hayır |

### §2.5 İlk başlatma ve tek-seferlik kurulum

| # | Gereksinim | Çözüm çerçevesi | Kaynak (2026-10-13, HTTP 200) |
|---|---|---|---|
| I-1 | Parçacık içinde tek seferlik başlangıç | `pthread_once` — bir kez çalışan başlatıcı | https://man7.org/linux/man-pages/man3/pthread_once.3.html |
| I-2 | Parçacığa özel veri (TLS) | `pthread_key_create` + `pthread_setspecific` | https://man7.org/linux/man-pages/man3/pthread_key_create.3.html |
| I-3 | Sinyalin hangi parçacığa gideceğini seçme | `pthread_sigmask` — parçacık bazında maske | https://man7.org/linux/man-pages/man3/pthread_sigmask.3.html |
| I-4 | Sinyal yiyicisinin güvenli olması | async-signal-safe fonksiyon listesi | https://man7.org/linux/man-pages/man7/signal-safety.7.html |
| I-5 | `fork` sonrası kilit durumu | atfork handlers (kilidi fork öncesi/sonrası yönet) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html |

### §2.6 Yaşam döngüsü (özet — ayrıntı §3.1)

| Aşama | Süreç | İş parçacığı | Bu alt-dalın sorumluluğu |
|---|---|---|---|
| Yaratma | `fork`/`exec` veya `CreateProcess` | `pthread_create` | kavram ve sırası |
| Çalıştırma | bağımsız yürütme | ortak adres uzayında yürütme | kritik bölüm tanımı |
| Askıya alma | bekleme/IPC | `cond_wait` / lock bekleme | bekleme koşulu |
| Bitirme | `exit` / sinyal | `pthread_join` / `detach` | kaynak serbest bırakma |
| Toplama | parent `waitpid` | join/detach ile yığın dönüşü | zombi/kaçak önleme |

### §2.7 Süreç mi, parçacık mı? — karar matrisi

| # | Soru | Evet → | Hayır → | Gerekçe |
|---|---|---|---|---|
| M-1 | İş, paylaşılan kilide dokunuyor mu? | parçacık + kilit tasarımı | süreç (fork) | kilitsiz süreç izolasyonu |
| M-2 | İş çökünce diğerleri ölmeli mi? | süreç | parçacık | süreç izolasyonu = dayanıklılık |
| M-3 | İş bellek paylaşıyor mu? | parçacık / paylaşımlı bellek | süreç | parçacık bellek uzayını paylaşır |
| M-4 | İş CPU-bound mu? | süreç | parçacık | GIL yok ama kilit yarışı var |
| M-5 | İş I/O-bound mu? | parçacık + havuz | süreç | bekleme sırasında GIL bırakılır |
| M-6 | İş ayrı kota (limit) altına girmeli mi? | süreç + `RLIMIT` | parçacık | kota süreç düzeyindedir (bkz. `kaynak-dagilimi`) |
| M-7 | İş yetki düşürmesi (drop-privilege) almalı mı? | süreç | parçacık | yetki süreç düzeyindedir |
| M-8 | Repo karşılığı var mı? | — | — | repo'da parçacık **YOK**; tek süreç kanıtı `proc_open` (§4.4 P-8) |

| Sonuç | CoreMusic'te geçerli mi? | Kanıt |
|---|---|---|
| parçacık + kilit | **hayır** — repo'da `pthread_*`/parçacık kilidi yok | §3.3 N-D02 dürüstlük defteri |
| süreç + `RLIMIT` | kısmen — `proc_open` tek kanıt | `ApiKeyCreateCommandTest.php` L253 |
| karar henüz verilmedi | evet — P-1 açık sorusu | §8.1 **[UNKNOWN]** |
| havuz (parçacık → süreç değişimi) | hayır — havuz kütüphanesi composer'da yok | `composer.json` envanteri (§4.4) |
| `RLIMIT`+`setrlimit` | hayır — çağıran kod yok | §4.5 dürüstlük defteri |

> Matris kuralı: satır 1–7 tek başına karar vermez; M-1 ve M-2'nin birlikte okunması gerekir.
> M-8 ise repo gerçeğini matrise bağlayan **dürüstlük satırıdır** — silinmez.

## §3 — Mekanizma akışı

### §3.1 Süreç ömrü: boot → exit → kaynak serbest bırakma

| # | Aşama | Ne olur | Hata olursa | Kanıt/kaynak |
|---|---|---|---|---|
| L-1 | Boot (uygulama girişi) | ortam değişkenleri, zaman dilimi, hata raporlama ayarlanır | fail-fast ile çıkış | `shared/src/Bootstrap/RuntimeBootstrap.php` L9 `date_default_timezone_set(...)` |
| L-2 | Bağımlılık çözümü | autoloader ve konteyner kurulur | sınıf bulunamazsa durdurma | `home.coremusic.net/config/bootstrap.php` L13 |
| L-3 | İstek döngüsüne girme | yönlendirici (router) eliyle iş dağıtılır | istisna yukarı taşınır | `home.coremusic.net/config/bootstrap.php` L97 |
| L-4 | Alt süreç başlatma (varsa) | `proc_open`/`fork` ile ayrı süreç | başarısızsa temizlik | `shared/tests/Repository/ApiKeyCreateCommandTest.php` L253 |
| L-5 | Kapanış (shutdown) | kayıtlı temizleyiciler çalışır | atlanırsa kaynak sızıntısı | POSIX `exit(3)` semantiği — `-runtime` |
| L-6 | Kaynak serbest bırakma | dosya tanıtııcı, kilit, bellek iade edilir | sızıntı birikir | bu dosya §4.3 |
| L-7 | Toplama (reaping) | parent alt süreci toplar | zombi süreç birikir | `-runtime` / `-kernel` |

### §3.2 Kilitlenmenin (deadlock) dört gerekli koşulu

| # | Koşul | Türkçe tanım | Kırma stratejisi | Sahibi |
|---|---|---|---|---|
| K-1 | Mutual exclusion | kaynak aynı anda tek sahipte | hiç kilit kullanmama (atomik) | bu alt-dal §2.3 |
| K-2 | Hold and wait | sahipken bekleme | tüm kaynakları toplayıp alma | bu alt-dal §3.4 |
| K-3 | No preemption | sahip zorla alınamaz | kilidi zorla serbest bırakma (yalnız OS) | `isletim-sistemi-kernel` |
| K-4 | Circular wait | döngüsel bekleme zinciri | küresel sıralama (lock ordering) | bu alt-dal §3.4 |

> Dört koşulun **hepsi aynı anda** olmalıdır; biri kırılırsa kilitlenme olmaz. Bu nedenle
> önleme stratejileri bu dört sütuna yazılır ve her biri için sorumluluk dağıtılır.

### §3.3 Önleme · tespit · kurtarma

| # | Yaklaşım | Nasıl | Maliyet | Uygulanabilirlik (CoreMusic) |
|---|---|---|---|---|
| O-1 | Önleme: küresel sıralama | tüm kod aynı kaynak sırasını kullanır | düşük | **önerilen** |
| O-2 | Önleme: tek kaynak | bölünmüş kaynak sayısını 1'e indir | orta | kuyruk işlerinde |
| O-3 | Önleme: timeout ile alma | `try_lock` + bekleme üst sınırı | düşük | uygulama katmanı |
| O-4 | Tespit: bekleyen graf | bekleme grafiğinde döngü arama | yüksek (ölçüm) | `observability` işi — burada yalnız adı |
| O-5 | Kurtarma: öncelikle kurtarma | düşük önceliklinin kilidini alma | OS desteği gerekir | `-kernel` |
| O-6 | Kurtarma: süreç sonlandırma | kilitlenen parçacıyı öldürme | veri kaybı riski | yalnız yönetimsel |

### §3.4 Kaynak kilitleri ve sıralama (lock ordering)

| # | Kural | Ayrıntı | İhlal belirtisi |
|---|---|---|---|
| R-1 | Her kaynak bir sayı (rank) alır | rank = sabit sıralama numarası | iki yerde farklı sıra |
| R-2 | Artan sırada kilitleme | yalnız küçük → büyük | ters sırada kilitleme |
| R-3 | Aynı yerde serbest bırakma | LIFO değil, açılış sırası | erken bırakma |
| R-4 | Kilit altında sistem çağrısı yok | bloke eden çağrı kilidi uzatır | uzun bekleme |
| R-5 | Kilit altında kilit alma yasağı (kısmi) | tek istisna: belgelenmiş zincir | döngü riski |
| R-6 | Tek kaynaklı işler sırasız | bölünmüş-lock yoksa rank gerekmez | gereksiz karmaşıklık |

### §3.5 Senkronizasyon akışı (üretici–tüketici)

| # | Adım | Durum | Hata modu | Kural |
|---|---|---|---|---|
| F-1 | Mutex kilitle | serbest → sahip | zaten sahipse | recursive kullanma |
| F-2 | Koşulu kontrol et | gerçek → sahip | gerçek değilse | `while` döngüsü kullan (tek kontrol yeterli değil) |
| F-3 | Bekle (`cond_wait`) | kilit bırakılır | uyanma kaçarsa | koŞul yeniden kontrol edilir |
| F-4 | Uyanınca yeniden kontrol | koŞul hâlâ yanlışsa | spurious wakeup | tekrar bekle |
| F-5 | Üret/yaz | kritik bölüm | yarım yazma | tek yazar (single writer) |
| F-6 | Uyanma sinyali | bekleme yoksa | kayıp sinyal | koşul öncesi/sonrası tutarlılık |
| F-7 | Kilidi aç | sahip → serbest | unutulan kilit | `finally`/`defer` benzeri kalıp |

### §3.6 Öncelik devri (priority inheritance) ve tersine çevirme

| # | Kavram | Tanım | Sonuç | Sahip |
|---|---|---|---|---|
| V-1 | Priority inversion | düşük öncelikli kilidi tutuyor, yüksek öncelikli bekliyor | orta öncelikli iş öne geçer, yüksek gecikir | bu alt-dal §2.3 |
| V-2 | Priority inheritance | bekleme süresince kilidi verenin önceliği yükseltilir | gecikme sınırlanır | `-kernel` (kural) + `zamanlama-scheduler` |
| V-3 | Priority ceiling | kilide en yüksek öncelik tanımlanır | döngü önlenir | `-kernel` |
| V-4 | Starvation ilişkisi | devir yoksa düşük öncelikli aç kalır | açlık (starvation) | `zamanlama-scheduler-surucu.md §5` |
| V-5 | Uygulama katmanı karşılığı | uygulamada "öncelik" yalnız kuyruk sırasıdır | OS önceliği değildir | `is-parcacigi-planlamasi-ornekler.md §1` |

### §3.7 İş parçacığı başına akış ve sinyal disiplini

| # | Adım | Kural | Kaynak (2026-10-13, HTTP 200) |
|---|---|---|---|
| W-1 | Parçacık başlatma | yalnız bir yerde başlat, kaynağı dağıt | https://man7.org/linux/man-pages/man3/pthread_once.3.html |
| W-2 | Sinyal maskesi | yalnız ana parçacık sinyal alır | https://man7.org/linux/man-pages/man3/pthread_sigmask.3.html |
| W-3 | Temizleyici | sinyal yiyicide async-signal-safe çağrı | https://man7.org/linux/man-pages/man7/signal-safety.7.html |
| W-4 | Per-thread veri | anahtar (key) ile parçacık verisi | https://man7.org/linux/man-pages/man3/pthread_key_create.3.html |
| W-5 | Fork sonrası durum | kilitli fork edilmez; atfork handler | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html |

### §3.8 Kilitlenme senaryoları (hangi koşul, hangi strateji)

| # | Senaryo | Aktif koşullar (§3.2) | Kırılan koşul | Strateji |
|---|---|---|---|---|
| S-1 | iki iş, iki kilit, ters sıra | K-1 K-2 K-3 K-4 | K-4 | küresel sıralama (O-1) |
| S-2 | tek iş, tek kilit, unutulan açma | K-1 K-2 K-3 | K-2 | `finally` kalıbı (F-7) |
| S-3 | kilit altında bloke eden I/O | K-1 K-2 K-3 K-4 (dolaylı) | K-2 | kilit altında I/O yasağı (R-4) |
| S-4 | spurious wakeup sonrası tek kontrol | K-1 K-2 | K-2 | `while` döngüsü (F-2) |
| S-5 | sinyal yiyicide kilit açma | K-1 | K-1 | async-signal-safe (W-3) |

| Senaryo | Ölçüm karşılığı | Repo kanıtı |
|---|---|---|
| S-1 | bekleme grafiği döngüsü | yok — parçacık kilidi repo'da **YOK** (§3.3) |
| S-2 | kilit süresi ölçümü (P-1 kalıbı) | `MiddlewarePipeline.php` L42 `hrtime` kalıbı |
| S-3 | kilitli bölüm üst sınırı (H-2) | **[UNKNOWN]** — eşik sayısal olarak bilinmiyor |
| S-4 | kaçırılan uyanma sayacı (E-7) | yok — condition variable repo'da **YOK** |
| S-5 | sinyal sayacı | yok — sinyal işleyici repo'da **YOK** |

## §4 — Ölçüm ve eşik

### §4.1 Ölçütler (bu alt-dalın başarı tanımı)

| # | Ölçüt | Tanım | Birim | İyi yön |
|---|---|---|---|---|
| E-1 | Lock contention süresi | kilidin beklenme toplam süresi | ms | düşük |
| E-2 | Deadlock tespit süresi | döngünün görülmesinden tepkiye kadar | sn | düşük |
| E-3 | Priority inversion gecikmesi | yüksek önceliklinin kilitten beklediği süre | ms | düşük |
| E-4 | Kilitli bölüm süresi | tek kritik bölümün iş süresi | µs | düşük |
| E-5 | Parçacık oluşturma süresi | başlatma → çalışır durum | ms | düşük |
| E-6 | Bekleyen parçacık sayısı | lock kuyruğunda bekleyen | adet | düşük |
| E-7 | Uyanma kaçırma (missed wakeup) olayı | koşul değiştiği hâlde bekleyenin uyanmaması | adet | 0 |

### §4.2 Eşikler (kabul kriteri — sayı bilinmiyorsa `UNKNOWN` yazılır)

| # | Eşik | Hedef | Kanıt durumu |
|---|---|---|---|
| H-1 | Kilitlenme olayı sayısı üretimde | 0 | **[UNKNOWN]** — üst otorite kararı |
| H-2 | Kilitli bölüm süresi üst sınırı | **[UNKNOWN]** — ölçüm gerekli | ölçüm `-surucu §7.3` |
| H-3 | Inversion gecikme toleransı | **[UNKNOWN]** — RT sınıfına bağlı | `zamanlama-scheduler-surucu.md §4` |
| H-4 | Parçacık oluşturma maliyeti | **[UNKNOWN]** — hedef donanım bilinmiyor | Raspberry Pi 5 kanıtı yok |
| H-5 | Kaçırılan uyanma olayı | 0 | `-surucu §7.2` test stratejisi |

### §4.3 Ölçüm adımları (adres — toplama `-observability`a)

| # | Adım | Yöntem | Ne kaydedilir |
|---|---|---|---|
| Z-1 | Kilit süresi ölçümü | monotonik saat (`hrtime` benzeri) | kilitle–aç arası süre |
| Z-2 | Bekleme kuyruğu | kilit bekleyen sayısı örnekleme | derinlik, en uzun bekleme |
| Z-3 | Ölçüm noktası ekleme | kritik bölüm giriş/çıkış damgası | başlangıç/bitiş |
| Z-4 | Kilitlenme alarmı | bekleme üst sınırı aşıldığında | uyarı satırı |
| Z-5 | Repo'daki saat kanıtı | `shared/src/Middleware/MiddlewarePipeline.php` L42 `$start = hrtime(true);` | süre ölçüm kalıbı (doğrulama: `Select-Object -Index 41`) |

### §4.4 Ölçüm kalıpları (repo kanıtı — doğrulanmış satır alıntıları)

| # | Kalıp | Dosya | Satır | Ne ölçer |
|---|---|---|---|---|
| P-1 | monotonik süre damgası | `shared/src/Middleware/MiddlewarePipeline.php` | L42 | `$start = hrtime(true);` — kilitli bölüm süresi (E-4) kalıbı |
| P-2 | süre raporu | `shared/src/Middleware/MiddlewarePipeline.php` | L46 | pipeline toplam süresinin log satırına taşınması |
| P-3 | önbellek yazma | `shared/src/Cache/ApcuAdapter.php` | L22 | APCu `apcu_store` sarmalayıcısı — ölçümde OOM etkisi |
| P-4 | bellek okuma | `shared/src/Cache/MemoryAdapter.php` | L16 | PHP-dahili dizi tabanlı önbellek — bellek ölçümü |
| P-5 | sayfa önbelleği | `shared/src/Cache/PageCacheAdapter.php` | L9 | sayfa düzeyi önbellek — kilitlenme dışı paylaşılan durum |
| P-6 | önyükleme saati | `shared/src/Core/RuntimeBootstrap.php` | L9 | `date_default_timezone_set` — tekil süreç başlangıcı |
| P-7 | paralel denetim | `shared/src/Api/Registry/ServiceHealth.php` | L24 | "Execute parallel health checks" yorumu — eşzamanlılık iddiası tek süreç içinde |
| P-8 | alt süreç testi | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | L253 | `$handle = proc_open(` — süreç/fork yüzeyinin tek repo kanıtı |

### §4.5 Ölçülmeyecek yüzeyler (dürüstlük defteri — §3.3 ile aynı kural)

| Yüzey | Neden ölçülmüyor | Durum |
|---|---|---|
| Parçacık oluşturma süresi (E-5) | repo'da parçacık oluşturan kod yok (§3.3 N-D02) | **[UNKNOWN]** |
| Priority inversion gecikmesi (E-3) | öncelikli planlama sınıfı kullanılmıyor | **[UNKNOWN]** |
| Lock contention süresi (E-1) | mutex/parçacık kilidi repo'da yok | **[UNKNOWN]** — ölçüm ancak hedef mimari kararı sonrası anlamlı |
| Kaçırılan uyanma (E-7) | condition variable yok | **[UNKNOWN]** — `E-7 = 0` hedefi kural olarak yazılır, ölçüm yok |
| Bekleyen parçacık sayısı (E-6) | parçacık havuzu yok | **[UNKNOWN]** |

Ölçüm yasağı: bu satırlara sayı uydurulmaz; ölçüm ancak `-surucu §5` havuz kararı
verildikten sonra açılır ve `-observability`a devredilir.

### §4.6 Ölçüm → karar matrisi

| Ölçüm sonucu | Karar | Uygulanan eşik |
|---|---|---|
| Kilitli bölüm > eşik (H-2) | kritik bölüm küçültülür | H-2 **[UNKNOWN]** — sayı yok |
| Bekleme kuyruğu derinliği artıyor | parçacık sayısı değil, kilit sayısı sorgulanır | E-6 |
| Kilitlenme alarmı (Z-4) tetiklendi | üst otorite: kilit kırma yetkisi (P-2) | H-1 = 0 |
| Parçacık oluşturma maliyeti ölçülmeden havuz önerisi | havuz **kurulmaz** — önce E-5 ölçümü | H-4 |

## §5 — Komşu sınırı

### §5.1 Komşu → bırakılan / alınan

| # | Komşu | Bıraktığımız | Aldığımız | Ayırıcı çizgi |
|---|---|---|---|---|
| C-1 | `isletim-sistemi-core` | süreç/parçacık kavram iskeleti | çekirdek tipler | kavram (bu) vs tablo (core) |
| C-2 | `isletim-sistemi-kernel` | kilit/kuyruk **politikası** | mutex ve wakeup mekanizması | ne (bu) vs nasıl (kernel) |
| C-3 | `isletim-sistemi-runtime` | parçacık API'sinin çağrılmayacağı sınır | `pthread_*`/`fork` yürütme bağlamı | çerçeve (bu) vs yürütme (runtime) |
| C-4 | `zamanlama-scheduler/` | öncelik devrinin sıraya bağlanması | sıra politikası | kilit (bu) vs sıra (plan) |
| C-5 | `kaynak-dagilimi/` | kilit bellek/fd kotasına bağlı | kota ve bütçe | sınır (bu) vs kota (kaynak) |
| C-6 | `isletim-sistemi-security` | parçacık yetki ihtiyacı | yetki modeli | ihtiyaç (bu) vs model (security) |
| C-7 | `isletim-sistemi-observability` | ölçüm adlarının listesi | metrik boru hattı | ad (bu) vs boru hattı |
| C-8 | `isletim-sistemi-testing` | kilit senaryosu ve eşikleri | test kodu, CI kapısı | senaryo (bu) vs kod (testing) |
| C-9 | `isletim-sistemi-audio` | RT parçacığında kilit kullanmama ihtiyacı | ring buffer/ASIO tasarımı | ihtiyaç (bu) vs zincir (audio) |

### §5.2 Runtime'a bırakılan çağrı çerçevesi (politika olarak — sözleşme değil)

| # | Çağı | Ne bırakılıyor | Ne bırakılmıyor | Neden |
|---|---|---|---|---|
| Y-1 | `pthread_getschedparam` | parçacığın öncelik/poli̇tika **sorgulanabileceği** kuralı | parametre listesi, dönüş tipi | sözleşme `-runtime`a aittir |
| Y-2 | `pthread_setschedparam` | öncelik ayarının yalnız yetkili süreçte yapılacağı | hata kodu çözümleme | yetki `-security`ye |
| Y-3 | `pthread_create`/`join` | yaşam döngüsü sırası (§2.6) | imza ve bayraklar | yürütme `-runtime`a |
| Y-4 | `fork`/`exec` | süreç zinciri kavramı (§3.1) | sistem çağrısı ayrıntısı | yürütme `-runtime`a |

### §5.3 Sınır ihlali kataloğu

| # | İhlal | Belirti | Düzeltme |
|---|---|---|---|
| I-1 | Mutex iç mekanizması yazılıyor | "wait queue", "rb-tree node" | `-kernel`e bırak |
| I-2 | `pthread_*` imzası yazılıyor | parametre listesi, `errno` | `-runtime`e bırak |
| I-3 | Sıra algoritması yazılıyor | quantum, CFS vruntime | `zamanlama-scheduler`a bırak |
| I-4 | Kilitlenme alarmı kodlanıyor | log schema, alert threshold | `-observability`a bırak |
| I-5 | Kilit test kodu yazılıyor | fixture, CI gate | `-testing`e bırak |
| I-6 | Uygulama yol haritası yazılıyor | faz, sprint, milestone | `isletim-sistemi-plan-kapsam.md §A.2` |

### §5.4 Yazar kontrol listesi

| # | Kontrol | Evet | Hayır |
|---|---|---|---|
| 1 | Cümle "hangi kural" sorusunu yanıtlıyor mu? | yaz | bırak |
| 2 | Cümle "nasıl çalışır" sorusunu yanıtlıyor mu? | `-kernel`e bırak | devam |
| 3 | Cümle "neyi çağırırım" sorusunu yanıtlıyor mu? | `-runtime`e bırak | devam |
| 4 | İddia için disk kanıtı var mı? | `Dosya/Satır` yaz | `UNKNOWN` işaretle |
| 5 | Aynı tablo başka dosyada mı? | sahibine link ver | yaz |
| 6 | Son satır Authority satırı mı? | koru | ekle |

## §6 — Terim sözlüğü

| # | Terim | Tanım | Bu alt-daldaki kullanımı |
|---|---|---|---|
| T-1 | Süreç (process) | kendi adres uzayına sahip yürütme birimi | §2.1 |
| T-2 | İş parçacığı (thread) | süreç içindeki ikincil yürütme birimi | §2.1 |
| T-3 | Kritik bölüm (critical section) | yalnız tek sahibin girebildiği kod | §2.3 |
| T-4 | Karşılıklı dışlama (mutual exclusion) | aynı anda tek erişim | §2.3 S-1 |
| T-5 | Kilitlenme (deadlock) | dört koşulun birlikte olmasıyla döngüsel bekleme | §3.2 |
| T-6 | Livelock | sürekli deneme ama ilerleme yok | `surucu §3` |
| T-7 | Örgü (convoy) | tek yavaş sahipte kuyruk birikmesi | `surucu §3` |
| T-8 | Senkronizasyon | sırayı ve veri tutarlılığını sağlama | §2.3 |
| T-9 | Koşul değişkeni (condition variable) | olay bekleyen yapıştırma | §3.5 |
| T-10 | Semaför | sayaçlı erişim izni | §2.3 S-4 |
| T-11 | Spinlock | dönerek bekleme | §2.3 S-5 |
| T-12 | Atomik işlem | bölünemez tek kelime işlemi | §2.3 S-7 |
| T-13 | Öncelik tersine çevirme (priority inversion) | yüksek önceliklinin düşük öncelikliye mahkûm olması | §3.6 V-1 |
| T-14 | Öncelik devri (priority inheritance) | bekleme boyunca öncelik ödünç verme | §3.6 V-2 |
| T-15 | Öncelik tavanı (priority ceiling) | kilit için en yüksek öncelik sınırı | §3.6 V-3 |
| T-16 | Lock ordering (sıralı kilitleme) | küresel kaynak sırası | §3.4 |
| T-17 | Rank (sıra numarası) | kaynağa atanan sabit sayı | §3.4 R-1 |
| T-18 | Hold and wait | sahipken bekleme | §3.2 K-2 |
| T-19 | Circular wait | döngüsel bekleme | §3.2 K-4 |
| T-20 | Spurious wakeup | koşulsuz uyanma | §3.5 F-4 |
| T-21 | Missed wakeup | kaçan uyanma | §4.1 E-7 |
| T-22 | TLS (`pthread_key_create`) | parçacığa özel veri alanı | §2.4 M-3 |
| T-23 | `pthread_once` | tek-seferlik başlatma | §2.5 I-1 |
| T-24 | `pthread_sigmask` | parçacık bazlı sinyal maskesi | §2.5 I-3 |
| T-25 | Async-signal-safe | sinyal yiyicide güvenli çağrı kümesi | §2.5 I-4 |
| T-26 | Atfork handler | `fork` öncesi/sonrası kilit yönetimi | §2.5 I-5 |
| T-27 | Eşzamanlı (concurrent) | birlikte ilerleme durumu | §2.2 T-A |
| T-28 | Paralel (parallel) | aynı anda yürütüm | §2.2 T-B |
| T-29 | Zombi süreç | bitmiş ama toplanmamış süreç | §3.1 L-7 |
| T-30 | Kaynak sızıntısı (leak) | serbest bırakılmayan kilit/fd | §3.1 L-6 |

## §7 — Kapsam dışı

| # | Kapsam dışı madde | Nereye gider | Gerekçe |
|---|---|---|---|
| S-1 | Mutex/kuyruk veri yapısı | `isletim-sistemi-kernel` | mekanizma |
| S-2 | `pthread_*` çağrı sözleşmesi | `isletim-sistemi-runtime` | yürütme |
| S-3 | Zamanlama algoritması (RR/CFS/EDF) | `zamanlama-scheduler/` | sıra politikası |
| S-4 | Bellek/fd kotası | `kaynak-dagilimi/` | kota |
| S-5 | Yetki modeli (capability, rol) | `isletim-sistemi-security` | model |
| S-6 | Metrik toplama ve alarm | `isletim-sistemi-observability` | ölçüm adı ≠ toplama |
| S-7 | Test kodu ve CI kapısı | `isletim-sistemi-testing` | strateji ≠ kod |
| S-8 | Ses zinciri/RT iş parçacığı tasarımı | `isletim-sistemi-audio` | sınıf ≠ zincir |
| S-9 | Donanım topolojisi (çekirdek sayısı) | `K001-donanım` (vault) | ipucu ≠ tasarım |
| S-10 | Uygulama yol haritası | `.ai/PROJECTS.md` · `.ai/WORKFLOW.md` | kapsam `§A.2 B1` |
| S-11 | Gerçek repo parçacık API'si varsayımı | yok — bkz. `is-parcacigi-planlamasi-ornekler.md §5` | kanıt yoksa iddia yok |

## §8 — Açık sorular

### §8.1 P-1 — hedef mimaride parçacık var mı?

| Alan | Değer |
|---|---|
| Soru | Hedef mimaride çok-iş-parçacıklı çalışma (`pthreads`) kullanılacak mı? |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Bu dosyada yapılan | Ayrım ve çerçeve yazıldı (§2.1-§2.6); varlık varsayılmadı |
| Mevcut kanıt | repo üretim kodunda thread oluşturucu çağrı **yok** (`isletim-sistemi-plan-dogrulama.md §3.3 X-1`) |
| Kapanış koşulu | hedef mimari kararının yazılması veya `-runtime` dosyalarının teyidi |
| Kapanmadığında ne olur | bu alt-dal `draft` kalır |

### §8.2 P-2 — kilitlenme tespiti üretime açık mı?

| Alan | Değer |
|---|---|
| Soru | Üretimde bekleme üst sınırı (timeout) ile kilitlenme tespiti kullanılacak mı? |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Mevcut kanıt | uygulamada `retryAfter`/`timeout` alanları var (`shared/src/Exception/RateLimitException.php` L7) — ama bu **kilit** değil, istek kotasıdır |
| Kapanış koşulu | üst otoritenin `surucu §3` hata modu kararını onaylaması |
| Kapanmadığında ne olur | `surucu §3` "aday" olarak kalır |

### §8.3 Plan1 — içerik nerede?

| Alan | Değer |
|---|---|
| Soru | Parçacık planlaması içeriği `isletim-sistemi-kernel` / `isletim-sistemi-runtime` içinde mi, burada mı? |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Bu dosyada yapılan | Sınır çizildi (§5): mekanizma → `-kernel`, yürütme → `-runtime`, politika çerçevesi → burası |
| Kapanış koşulu | komşu dosyaların okunup sınırın teyidi |
| Kapanmadığında ne olur | çakışma tespit edilirse `isletim-sistemi-plan-kapsam.md §D.5` kataloğu işletilir |

### §8.4 Açık soru defteri kuralları

| Kural | Metin |
|---|---|
| F1 | Kanıtla cevaplanamayan soru `[UNKNOWN]` etiketiyle listede kalır |
| F2 | Cevap uydurulmaz; `⚠️ VERIFICATION REQUIRED` yalnız doğrulanamayan iddia için |
| F3 | Soru kapanırsa bu dosya, `-surucu.md §7.7` (varsa) ve kök `index.md` birlikte güncellenir |
| F4 | Kapatma kararı subagent tarafından verilmez — orkestratöre aittir |

## §9 — Kaynakça (Web Research)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX `pthread_sigmask`, parçacık bazında sinyal maskesini okur/ayarlar; sinyal disiplininin çerçevesidir (§2.5 I-3) | https://man7.org/linux/man-pages/man3/pthread_sigmask.3.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| POSIX/Linux `pthread_once`, çok-parçacıklı başlangıcı tek çalıştırmayı garanti eder (§2.5 I-1, §3.7 W-1) | https://man7.org/linux/man-pages/man3/pthread_once.3.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| POSIX `pthread_key_create`, parçacığa özel veri (TLS) tanımı sağlar; paylaşımlı durudan ayrıştırır (§2.4 M-3) | https://man7.org/linux/man-pages/man3/pthread_key_create.3.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| Linux `signal-safety(7)`, sinyal yiyicide çağrılabilecek güvenli fonksiyon kümesini tanımlar (§3.7 W-3) | https://man7.org/linux/man-pages/man7/signal-safety.7.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| POSIX `pthread_atfork`, `fork` öncesi/sonrası kilit yönetimi için handler kaydı tanır (§2.5 I-5, §3.7 W-5) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi POSIX/IEEE 1003.1) |

> Not: envanter kesişim denetimi — yukarıdaki 5 URL `url-inventory-2026-10-13.txt` (2633 kayıt)
> içinde **yoktur**; kesişim = 0.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
