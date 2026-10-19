---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler/zamanlama-scheduler-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "https://docs.kernel.org/scheduler/sched-capacity.html"
  - "https://docs.kernel.org/scheduler/sched-ext.html"
  - "https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_get_priority_max.html"
validation:
  - "2026-10-13: 4 URL HTTP 200 · envanter kesişimi 0"
  - "2026-10-13: komşu sınırı §4 — 8 komşu, ihlal 0"
open_questions:
  - "Plan1: scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: mimari
---

# zamanlama-scheduler — Anayasa

> **Tek cümle:** Bu dosya `zamanlama-scheduler` alt-dalının **anayasasıdır** — zamanlamanın ne olduğunu, ne amaçladığını, hangi politika ailesini taşıdığını, hangi komşuya ne bıraktığını ve hangi soruyu **kapatmadan** bıraktığını tanımlar; ayrıntılı mekanizma `-surucu.md`'dedir.

## §0 — ELI15: zamanlama tek cümlede

> **Bir mutfak düşün:** tek ocak (CPU), önünde bekleyen bir sürü tabak (işler). Zamanlayıcı (şef), **hangi tabağın önce ocakta duracağına, ne kadar sürece orada kalacağına ve sıradaki kim olduğuna** karar verir. Bu kararın adı **zamanlamadır** (scheduling).

| Kavram | Mutfak karşılığı | OS karşılığı |
|---|---|---|
| Ocak | CPU çekirdeği | işlem çekirdeği |
| Tabağı bekleyen | hazır iş | çalışır durumdaki süreç/iş parçası |
| Şefin kararı | sıralama kuralı | zamanlama politikası |
| Ocakta geçen süre | yemek pişirme süresi | CPU zamanı (quantum) |
| "Şimdi sıra sende" | servis çağrısı | dispatch / context switch |
| Ocaktan kalk | yemek bitti / ara verildi | preemption / block |

**Önemli ayrım:** Zamanlama **mühendislik kararıdır**, hukuk değil. Şefin kuralı ne adil ne adaletsizdir — **amacına uygun** ya da **uygunsuz**dur. Aynı şekilde bir zamanlama politikası "kötü" değil; **gereksinime uymuyordur**.

## §1 — Kimlik ve konum

| Alan | Değer |
|---|---|
| `node_id` | `K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler/zamanlama-scheduler-anayasa` |
| `node_type` | `module` |
| `parent_id` | `K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler` |
| `status` | `draft` |
| `group` | `mimari` |
| `depends_on` | `isletim-sistemi-kernel` · `isletim-sistemi-runtime` |
| Kademe | Kademe-4 (dosya) · ebeveyn Kademe-3 (klasör) · kök Kademe-3 |

### §1.1 Bu dosyanın neyi taşımıyor

| # | Taşımıyor | Nerede |
|---|---|---|
| 1 | Algoritma ayrıntıları (RR · CFS · EDF hesapları) | `zamanlama-scheduler-surucu.md §1` |
| 2 | Gerçek zamanlı sınıfı çağrısı (`SCHED_FIFO` / `RR` / `DEADLINE`) | `zamanlama-scheduler-surucu.md §4` |
| 3 | Ölçüm prosedürü (context switch latency · jitter) | `zamanlama-scheduler-surucu.md §6` |
| 4 | Gerçek repo kodu | `zamanlama-scheduler-ornekler.md §1` |
| 5 | Kuyruk/wakeup iç mekanizması | `isletim-sistemi-kernel` |

## §2 — Zamanlamanın amacı

### §2.1 Dört temel amaç

| # | Amaç | Tanım | Ölçü birimi | Ağırlık |
|---|---|---|---|---|
| A-1 | **Adalet (fairness)** | her iş, hakkaniyetli bir CPU payı alır | CPU payı oranı | yüksek |
| A-2 | **Düşük gecikme (latency)** | etkileşimli iş, kullanıcıya hızlı yanıt verir | ms | yüksek (etkileşimli) |
| A-3 | **Yüksek verim (throughput)** | birim zamanda en çok iş biter | iş/sn | yüksek (toplu) |
| A-4 | **Öngörülebilirlik (predictability)** | iş, tahmin edilebilir bir zamanda tamamlanır | gecikme değişkenliği (jitter) | kritik (RT) |

### §2.2 Amaç çatışması

| Çatışma | Açıklama | Sonuç |
|---|---|---|
| Adalet ↔ Düşük gecikme | adalet, uzun işlere de CPU verir; düşük gecikme, kısa işlere öncelik ister | ikisi aynı anda **tam** sağlanamaz |
| Verim ↔ Öngörülebilirlik | verim, toplu işleri doldurur; öngörülebilirlik, RT işlere rezervasyon ister | iki politika ailesi gerekir |
| Adalet ↔ Verim | adalet, küçük işleri sık çağırır (bağlam değiştirme maliyeti); verim, büyük dilimler ister | quantum seçimi burada belirlenir |

### §2.3 Ölçüt tablosu (bu alt-dalın başarı tanımı)

| Ölçüt | Tanım | Aşağıda iyi | Yukarıda iyi | Birim |
|---|---|---|---|---|
| Context switch latency | iki iş arasında geçiş maliyeti | EVET | — | µs |
| Jitter (değişkenlik) | aynı işin gecikmesinin saçılımı | EVET | — | µs |
| Throughput | birim zamanda biten iş | — | EVET | iş/sn |
| CPU kullanımı | CPU'nun dolu olma oranı | — | EVET | % |
| Bekleme kuyruğu (run queue) | hazır bekleyen iş sayısı | EVET | — | adet |
| RT kaçırma oranı (deadline miss) | zamanında bitmeyen RT iş oranı | EVET | — | % |
| Starvation süresi | bir işin CPU alamadan geçirdiği en uzun süre | EVET | — | ms |

## §3 — Politika ailesi özeti

### §3.1 Dört aile

| # | Aile | Temel kural | Ne zaman seçilir | Örnek OS |
|---|---|---|---|---|
| P-1 | **Round Robin (RR)** | hazır işler döngüsel sırayla, eşit dilimlerde CPU alır | etkileşimli, eşit öncelikli işler | POSIX `SCHED_RR` |
| P-2 | **CFS (Completely Fair Scheduler)** | her işın "bekleme süresi" hesaplanır, en az bekleyen önce çalışır | genel amaçlı, dinamik yük | Linux varsayılanı |
| P-3 | **Öncelik tabanlı (priority)** | yüksek öncelik her zaman düşük öncelikliyi baskınlar | farklı kritiklikte işler | POSIX `SCHED_FIFO` |
| P-4 | **Gerçek zamanlı (deadline)** | iş, son teslim (deadline) bildirir; sistem uygunluğu kabul eder | katı gecikme gereksinimi | Linux `SCHED_DEADLINE` |

### §3.2 Seçim matrisi

| Gereksinim | RR | CFS | Priority | Deadline |
|---|---|---|---|---|
| Etkileşimli masaüstü | uygun | **en iyi** | uygun | gereksiz |
| Toplu iş (batch) | zayıf | uygun | zayıf | **en iyi** |
| Katı gecikme (RT) | yetersiz | yetersiz | uygun | **en iyi** |
| Eşit öncelikli iş kümeleri | **en iyi** | uygun | yetersiz | gereksiz |
| Öngörülebilir tamamlanma | zayıf | zayıf | uygun | **en iyi** |
| Yıldızlanma riski | düşük | **çok düşük** | yüksek | düşük |

### §3.3 `sched-ext` notu (yeni politika ailesi)

| Alan | Değer | Kaynak |
|---|---|---|
| Ad | `sched-ext` | https://docs.kernel.org/scheduler/sched-ext.html (HTTP 200, 2026-10-13) |
| Ne | BPF ile kullanıcı uzayında politika yazmaya izin veren çekirdek altyapısı | aynı kaynak |
| Bu alt-daldaki yeri | P-1..P-4 **kod değildir**; `sched-ext` ile değiştirilebilir politika arayüzüdür | bu dosya §3.1 |
| Sınır | BPF politikasının kendisi `isletim-sistemi-kernel`e aittir | bu dosya §4 |

### §3.4 Kapasite ve öncelik baskını

| Kavram | Tanım | Kaynak |
|---|---|---|
| Kapasite (capacity) | bir çekirdeğin bir birim işi ne kadar sürede bitirebileceğinin göstergesi | https://docs.kernel.org/scheduler/sched-capacity.html (HTTP 200, 2026-10-13) |
| Öncelik baskını (priority boost) | düşük öncelikli iş, kaynak beklerken öncelik kazanabilir | aynı kaynak |
| Bu alt-daldaki anlamı | politika **girdisi** olarak kapasite kavramı geçer; hesabı `-kernel`e aittir | bu dosya §4 |

### §3.5 Zamanlama istatistikleri

| Kavram | Tanım | Kaynak |
|---|---|---|
| `sched-stats` | çekirdeğin ürettiği zamanlama istatistikleri | https://docs.kernel.org/scheduler/sched-stats.html (HTTP 200, 2026-10-13) |
| Bu alt-daldaki yeri | §6'daki ölçüm adımlarının **kaynağı** | `zamanlama-scheduler-surucu.md §6` |
| Sınır | istatistik toplama hattı `isletim-sistemi-observability`a aittir | bu dosya §4 |

### §3.6 RTOS zamanlaması (Zephyr örneği)

| Kavram | Tanım | Kaynak |
|---|---|---|
| Zephyr zamanlama hizmetleri | gömülü RTOS'ta iş parçası zamanlama hizmetleri | https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html (HTTP 200, 2026-10-13) |
| Bu alt-daldaki anlamı | zamanlama **kavramının** masaüstü OS'tan bağımsız olduğu; aynı model gömülüde de geçerlidir | bu dosya §3.7 |
| İlişki | CoreMusic hedefi Raspberry Pi 5 → gömülü-kökenli bir OS'ta çalışabilir | `isletim-sistemi-plan-kapsam.md §A.3 A5` |

### §3.7 Zamanlama modeli ortaklığı

| Model | Masaüstü/Linux | Zephyr (RTOS) | Fark |
|---|---|---|---|
| Öncelik tabanlı | `SCHED_FIFO` / `SCHED_RR` | öncelikli iş parçacığı | yok |
| Zaman dilimi (quantum) | tick tabanlı | sayaç tabanlı | çözünürlük |
| Öncelik sayısı | 0–99 (RT) | üreticiye bağlı | aralık |
| Gerçek zamanlı sınıfı | `SCHED_DEADLINE` | deadline'lı iş parçacığı | API adı |
| Adillik (fairness) | CFS | yok (RTOS'ta adillik zorunlu değil) | varlık |

## §4 — Komşu sınırı

### §4.1 Komşu → bırakılan / alınan

| # | Komşu | Bıraktığımız | Aldığımız | Ayırıcı çizgi |
|---|---|---|---|---|
| C-1 | `isletim-sistemi-core` | süreç/kavram çerçevesi | çekirdek tipler | ortak tip kimde (core) |
| C-2 | `isletim-sistemi-kernel` | politika çerçevesi ve ölçütler | kuyruk/wakeup mekanizması | kural (plan) vs mekanizma (kernel) |
| C-3 | `isletim-sistemi-runtime` | öncelik/paralelilik kararlarının yürütme etkisi | süreç/iş parçası ömrü, kilit, çağrı bağlamı | planlanır (plan) vs çağrılır (runtime) |
| C-4 | `isletim-sistemi-api` | uygulama katmanı kuyruğunun OS olmadığının notu | endpoint/sözleşme tasarımı | örnek (plan) vs API (api) |
| C-5 | `isletim-sistemi-security` | zamanlama politikası güvenliği **ihtiyacının** tanımı | capability/rol/ACL modeli | çerçeve (plan) vs model (security) |
| C-6 | `isletim-sistemi-observability` | ölçüm adılarının listesi | metrik boru hattı, log şeması | okuma (plan) vs toplama (observability) |
| C-7 | `isletim-sistemi-testing` | ölçüm eşikleri ve doğrulama senaryosu | test kodu, CI kapısı | strateji (plan) vs uygulama (testing) |
| C-8 | `isletim-sistemi-audio` | RT zamanlama sınıfının ses için anlamı | zincir/ASIO/ring buffer tasarımı | sınıf (plan) vs zincir (audio) |
| C-9 | `isletim-sistemi-turu` | OS zamanlayıcı farklarının varlığının işaret edilmesi | kurulum yolu, dağıtım yapılandırması | politika (plan) vs kurulum (turu) |

### §4.2 Sınır ihlali kataloğu

| # | İhlal | Belirti | Düzeltme |
|---|---|---|---|
| I-1 | Kuyruk veri yapısı tarif ediliyor | "rb-tree", "red-black", "runqueue node" | `-kernel`e bırak |
| I-2 | `pthread_*` / `SCHED_*` çağrı sözleşmesi yazılıyor | parametre listesi, dönüş tipi | `-runtime`e bırak |
| I-3 | Yetki modeli tanımlanıyor | capability seti, rol matrisi | `-security`e bırak |
| I-4 | Metrik toplama hattı yazılıyor | metric name, log schema, alarm | `-observability`e bırak |
| I-5 | Test kodu yazılıyor | fixture, CI gate | `-testing`e bırak |
| I-6 | ASIO/ring buffer tasarımı yazılıyor | buffer size, callback | `-audio`e bırak |
| I-7 | OS kurulum yolu yazılıyor | systemd unit, init script | `-turu`a bırak |
| I-8 | Kullanıcı parçacığı API tasarımı yazılıyor | endpoint, request/response | `-api`ye bırak |

### §4.3 Sınır kontrol listesi (yazar için)

| # | Kontrol | Evet | Hayır |
|---|---|---|---|
| 1 | Cümle "hangi kural" sorusunu mu yanıtlıyor? | yaz | bırak |
| 2 | Cümle "nasıl çalışır" sorusunu mu yanıtlıyor? | `-kernel`e bırak | devam |
| 3 | Cümle "neyi çağırırım" sorusunu mu yanıtlıyor? | `-runtime`e bırak | devam |
| 4 | İçerik uygulama yol haritası içeriyor mu? | `isletim-sistemi-plan-kapsam.md §A.2 B1` ihlali | devam |
| 5 | Cümle `[UNKNOWN]`/`⚠️` gerektiriyor mu? | işaretle | işaretleme |
| 6 | Aynı tablo başka dosyada var mı? | sahibine bırak, link ver | yaz |
| 7 | Son satır Authority satırı mı? | koru | ekle |

## §5 — Terim sözlüğü

| # | Terim | Tanım | Bu alt-daldaki kullanımı |
|---|---|---|---|
| T-1 | Zamanlama (scheduling) | hangi işin, hangi sırayla, ne kadar CPU alacağına karar verme | bu alt-dalın tamamı |
| T-2 | Politika (policy) | zamanlama kararının kuralı | §3 |
| T-3 | Mekanizma (mechanism) | politikanın çalıştırıldığı çekirdek altyapısı | `-kernel`e bırakılır |
| T-4 | İş (job/task) | çalıştırılmayı bekleyen birim | genel kullanım |
| T-5 | Preemption | bir işin, kendi isteğiyle olmaksızın CPU'dan alınması | `surucu §2` |
| T-6 | İşbirlikçi (cooperative) | yalnız işin kendi bırakmasıyla CPU'nun el değiştirmesi | `surucu §2` |
| T-7 | Quantum / dilim | bir işın CPU'da kalabileceği azami süre | `surucu §3` |
| T-8 | Tick | çekirdeğin düzenli "zamanlayıcı uyanışı" periyodu | `surucu §3` |
| T-9 | Context switch | iki iş arasında CPU geçişi | `surucu §6` |
| T-10 | Jitter | aynı işin gecikmesinin değişkenliği | `surucu §6` |
| T-11 | Starvation (açlık) | bir işın CPU alamadan kalması | `surucu §5` |
| T-12 | Adillik (fairness) | işlerin hakkaniyetli pay alması | `surucu §5` |
| T-13 | Öncelik (priority) | bir işın diğerlerine göre sırası | `surucu §4` |
| T-14 | Deadline | işın tamamlanması gereken an | `surucu §4` |
| T-15 | Run queue | hazır bekleyen iş listesi | `surucu §6` |
| T-16 | Baskın (preempt) | yüksek önceliklinin düşük öncelikliyiCPU'dan alması | `surucu §4` |
| T-17 | Kapasite (capacity) | çekirdeğin iş bitirme hızı göstergesi | §3.4 |
| T-18 | `sched-ext` | BPF ile kullanıcı uzayında politika yazma altyapısı | §3.3 |
| T-19 | `SCHED_FIFO` | gerçek zamanlı, ilk-giren-önce (FIFO) öncelikli politika | `surucu §4` |
| T-20 | `SCHED_RR` | gerçek zamanlı, round-robin öncelikli politika | `surucu §4` |
| T-21 | `SCHED_DEADLINE` | son teslim bildiren gerçek zamanlı politika | `surucu §4` |
| T-22 | Nice | POSIX süreç öncelik değeri (düşük = yüksek öncelik) | `surucu §1` |
| T-23 | `sched_yield` | işın gönüllü CPU bırakması | `surucu §2` |
| T-24 | RT kaçırma (deadline miss) | RT işının zamanında bitmemesi | `surucu §6` |

## §6 — Kapsam dışı

| # | Kapsam dışı madde | Nereye gider | Gerekçe |
|---|---|---|---|
| S-1 | Kuyruk veri yapısı (rb-tree, linked list) | `isletim-sistemi-kernel` | mekanizma, politika değil |
| S-2 | Wakeup yolu (interrupt → scheduler → dispatch) | `isletim-sistemi-kernel` | mekanizma |
| S-3 | `pthread_*` / `SCHED_*` çağrı sözleşmesi | `isletim-sistemi-runtime` | yürütme |
| S-4 | Yetki modeli (capability, rol, ACL) | `isletim-sistemi-core` · `isletim-sistemi-security` | çerçeve vs model |
| S-5 | Metrik toplama, log şeması, alarm eşiği | `isletim-sistemi-observability` | ölçüm adı ≠ toplama hattı |
| S-6 | Test kodu, fixture, CI kapısı | `isletim-sistemi-testing` | strateji ≠ uygulama |
| S-7 | Ses zinciri (ASIO, ring buffer, callback) | `isletim-sistemi-audio` | sınıf ≠ zincir |
| S-8 | OS kurulum yolu (systemd, init, bootloader) | `isletim-sistemi-turu` | politika ≠ kurulum |
| S-9 | Donanım topolojisi (çekirdek sayısı, big.LITTLE) | `K001-donanım` (vault) | ipucu ≠ tasarım |
| S-10 | Uygulama yol haritası (faz, sprint, milestone) | `.ai/PROJECTS.md` · `.ai/WORKFLOW.md` | `isletim-sistemi-plan-kapsam.md §A.2 B1` |
| S-11 | Uygulama katmanı kuyruk/öncelik örneği | `isletim-sistemi-api` | OS ≠ uygulama (bkz. `zamanlama-scheduler-ornekler.md §5`) |

## §7 — Açık sorular

### §7.1 Plan1 — scheduling içeriği ayrı yerde mi?

| Alan | Değer |
|---|---|
| Soru | Zamanlama içeriği `isletim-sistemi-kernel` / `isletim-sistemi-runtime` içinde mi, burada mı? |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Bu dosyada yapılan | Sınır çizildi (§4): mekanizma → `-kernel`, yürütme → `-runtime`, politika çerçevesi → burası |
| Kapanış koşulu | `isletim-sistemi-kernel` ve `isletim-sistemi-runtime` dosyalarının okunup sınırın teyidi |
| Kapanmadığında ne olur | bu alt-dal `draft` kalır; çakışma tespit edilirse `isletim-sistemi-plan-kapsam.md §D.5` kataloğu işletilir |

### §7.2 Q-ek — hedef mimaride gerçek zamanlı politika var mı?

| Alan | Değer |
|---|---|
| Soru | CoreMusic hedefinde (Raspberry Pi 5) gerçek zamanlı politika (`SCHED_FIFO`/`RR`/`DEADLINE`) kullanılacak mı? |
| Durum | **[UNKNOWN] — KAPATILMAZ** |
| Mevcut kanıt | `home.coremusic.net/AGENTS.md §1` → "Raspberry Pi 5 üzerinde çalışan ana medya paneli" |
| Eksik kanıt | hedef mimari dokümanında RT politika kararı |
| Kapanış koşulu | ses motoru (Neva Engine) zamanlama gereksiniminin yazılması |

### §7.3 Açık soru defteri kuralları

| Kural | Metin |
|---|---|
| F1 | Açık soru, kanıtla cevaplanamıyorsa `[UNKNOWN]` etiketiyle listede kalır |
| F2 | Cevap uydurulmaz; `⚠️ VERIFICATION REQUIRED` yalnız doğrulanamayan iddia için kullanılır |
| F3 | Soru kapanırsa hem bu dosya hem `-surucu.md §8` hem kök `index.md` eşzamanlı güncellenir |
| F4 | Kapatma kararı subagent tarafından verilmez — orkestratöre/üst otoriteye aittir |

## §8 — Raspberry Pi 5 hedefi notu

### §8.1 Hedef kanıtı

| Alan | Değer | Kaynak |
|---|---|---|
| Cihaz | Raspberry Pi 5 | `home.coremusic.net/AGENTS.md §1` (disk okuma, 2026-10-13) |
| İşlev | "Raspberry Pi 5 üzerinde çalışan ana medya paneli" | aynı kaynak |
| Çekirdek mimarisi | ⚠️ VERIFICATION REQUIRED — bu turda doğrulanmadı | — |
| Çekirdek sayısı | ⚠️ VERIFICATION REQUIRED — bu turda doğrulanmadı | — |
| OS seçimi | ⚠️ VERIFICATION REQUIRED — bu turda doğrulanmadı | — |

### §8.2 Hedefin zamanlamaya etkisi (kapsam, kesinlik değil)

| # | Etki | Neden | Kanıt durumu |
|---|---|---|---|
| E-1 | Düşük çekirdek sayısı olası | Raspberry Pi 5'in çekirdek sayısı bilinmiyor, ama sınıfı 4 çekirdek civarıdır | ⚠️ VERIFICATION REQUIRED |
| E-2 | RT politikası gerekebilir | medya paneli = ses/DSP → düşük gecikme gereksinimi | `home.coremusic.net/AGENTS.md §1` |
| E-3 | Kapasite farkı olabilir | tek tip çekirdek mi, heterogeneous mi bilinmiyor | ⚠️ VERIFICATION REQUIRED |
| E-4 | Bellek sınırlı olabilir | Raspberry Pi sınıfı bellek sınırı | ⚠️ VERIFICATION REQUIRED |

### §8.3 Hedef → alt-dal etkisi tablosu

| Alt-dal | Hedefin etkisi | Aksiyon |
|---|---|---|
| `zamanlama-scheduler` | RT politikası gerekebilir | §4 (`surucu`) RT sınıfı çerçevesi hazır tutulur |
| `is-parcacigi-planlamasi` | çekirdek bağışıklama (affinity) önem kazanır | `is-parcacigi-planlamasi-surucu.md §3` |
| `kaynak-dagilimi` | bellek/CPU kotası sıkılaşır | `kaynak-dagilimi-surucu.md §1` |

## §9 — Kaynakça

| # | İddia | URL | 2026-10-13 |
|---|---|---|---|
| 1 | Linux çekirdek dokümantasyonu, işlem öncelik/baskın (capacity) kavramını taşır | https://docs.kernel.org/scheduler/sched-capacity.html | HTTP 200 |
| 2 | Linux çekirdek dokümantasyonu, `sched-ext` (BPF tabanlı zamanlayıcı) sayfasını taşır | https://docs.kernel.org/scheduler/sched-ext.html | HTTP 200 |
| 3 | Linux çekirdek dokümantasyonu, zamanlama istatistikleri (`sched-stats`) sayfasını taşır | https://docs.kernel.org/scheduler/sched-stats.html | HTTP 200 |
| 4 | POSIX `sched_get_priority_max(3p)`, politika başına öncelik üst sınırını verir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_get_priority_max.html | HTTP 200 |
| 5 | POSIX `sched_yield(3p)`, iş parçasının gönüllü olarak CPU'yu bırakmasını sağlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html | HTTP 200 |
| 6 | POSIX `nice(3p)`, süreç öncelik değeri (nice) getirir/ayarlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/nice.html | HTTP 200 |
| 7 | Zephyr dokümantasyonu, zamanlama (scheduling) hizmetlerini tanımlar | https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html | HTTP 200 |
| 8 | Zephyr dokümantasyonu, iş parçası (thread) hizmetlerini tanımlar | https://docs.zephyrproject.org/latest/kernel/services/threads/index.html | HTTP 200 |
| 9 | Microsoft Learn, `SetThreadPriority`, iş parçası öncelik seviyesini ayarlar | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadpriority | HTTP 200 |

> Not: 19 URL'in tamamı ve envanter kesişim denetimi `isletim-sistemi-plan-dogrulama.md §2`dedir.

---

## §10 Öncelik Aralıkları, Ölçüm Protokolü ve RT Kontrol Listesi Derinlemesine

> Bu bölüm §1-§9'u derinleştirir. Yeni tescil: §10.1'de 1 adres
> (envanter kesişimi 0 · HEAD-200 · 2026-10-13). Ölçüm iddiaları PLANNED
> olarak işaretlidir; sayısal zamanlama değeri (kuantum, gecikme) yazılmaz.

### §10.1 POSIX öncelik aralığı — alt sınır işlevi

§9.4'te öncelik **üst** sınırı işlevi tescillidir; bu bölüm **alt** sınır
işlevini ekler — ikisi birlikte politika başına aralığı tanımlar:

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 10.1.1 | POSIX `sched_get_priority_min`, politika başına öncelik alt sınırını verir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_get_priority_min.html | 2026-10-13 | ✅ HEAD 200 (envanter kesişimi 0) |

### §10.2 Politika → aralık sözleşmesi (kavramsal)

| # | Politika | Aritmetik öncelik mi? | Aralık kaynağı | RT sınıfı |
|---|---|---|---|---|
| 10.2.1 | sıradan (zaman paylaşımlı) | hayır (nice kavramı ayrı) | politika-özel | değil |
| 10.2.2 | gerçek zamanlı tur (RR) | evet | alt-üst sınır çifti (§10.1 + §9.4) | evet |
| 10.2.3 | gerçek zamanlı sabit (FIFO) | evet | alt-üst sınır çifti | evet |
| 10.2.4 | diğer politikalar | ⚠️ bu turda tescil edilmedi | — | — |

> §10.2 tablosu aralık **sözleşmesini** verir; sayısal değerler mimariye ve
> politikaya göre değiştiğinden yazılmaz (daraltma kuralı: iddia sınırıyla).

### §10.3 Tur zamanı (kuantum) belirsizliği

| # | Soru | Durum | Davranış |
|---|---|---|---|
| 10.3.1 | RR tur uzunluğu ne kadar? | ⚠️ VERIFICATION REQUIRED | POSIX mikro-saniye sözü vermez; sayı yazılmaz |
| 10.3.2 | tur sonunda öncelik kırılması? | ⚠️ VERIFICATION REQUIRED | kavramsal "eşit öncelik sırası" tarifi dışında iddia yok |
| 10.3.3 | uygulama bu değeri ölçmeli mi? | evet (hedef) | ölçüm PLANNED — §10.5 |

### §10.4 Heterojen çekirdek — §9.1 1 numaralı kaynağa dayalı genişleme

§9.1/1 (zamanlama kapasite kavramı sayfası) ve §8.3/E-3 (kapasite farkı
açığı) birlikte okunduğunda, heterojen sistemlerde her çekirdeğin eşit
olmadığı **işaretilmiş** bir açık konumudur. Bu bölüm açık kuyruğunu
genişletir:

| # | Açık | Soru | Kapanma ölçütü |
|---|---|---|---|
| 10.4.1 | E-3 (kapasite farkı) | hedef çekirdek kümesi hangi kapasitede? | çekirdek başına ölçüm (PLANNED) |
| 10.4.2 | bağışıklık (affinity) | hangi iş hangi çekirdeğe? | §8.3'teki is-parcacigi dosyasına devredildi |
| 10.4.3 | kapasite ağırlıklı dengeleme | kapasite farkı dengelemeyi nasıl bozar? | ⚠️ bu dosyada derinleştirilmez |

### §10.5 Ölçüm protokolü tasarımı (PLANNED — sayı yok, tasarım var)

| # | Adım | Girdi | Beklenen çıktı (kavramsal) |
|---|---|---|---|
| 10.5.1 | hedef periyot hesabı | örnekleme hızı + tampon boyutu | beklenen saniye cinsinden periyot |
| 10.5.2 | gerçekleşme okuması | zaman damgası çifti | gerçekleşen periyot dizisi |
| 10.5.3 | sapma (jitter) | gerçekleşme − beklenen | sapma dizisi |
| 10.5.4 | kesinti sayacı | taşıma/kesinti sayacı okuması | kesinti olay sayısı |
| 10.5.5 | raporlama | sapma özeti + kesinti sayısı | ölçülmüş doğrulama raporu |

> §10.5 tablosu bir **tasarımdır**; hiçbir satırda ölçülmüş sayı yoktur.
> Ölçüm yapılmadan "düşük gecikme" yazmak bu ağacın R1 kuralı gereği yasaktır
> (erken uyarı işaretleri tablosundaki ikinci satır).

### §10.6 Gerçek zamanlı kontrol listesi (kullanıcı uzayında)

| # | Kontrol | Ne zaman | Gerekçe |
|---|---|---|---|
| 10.6.1 | öncelik ayarı | akış başlamadan bir kez | çalışma sırasında ayar öngörülemez |
| 10.6.2 | çekirdek bağışıklığı | başlangıçta | taşıma maliyeti (§8.3 dosyasına atıf) |
| 10.6.3 | bloklama yasağı | çalışma boyunca | periyot ihlali |
| 10.6.4 | tahsis yasağı | çalışma boyunca | öngörülemeyen gecikme |
| 10.6.5 | gecikme ölçümü | doğrulama aşamasında | R1 kuralı |
| 10.6.6 | geri alma planı | ölçüm başarısızsa | öncelikli mod kapatılır, sıradan moda dönülür |

### §10.7 Gözlem kaynakları — §9.1 kaynakça atıfları

| # | §9.1 kaynağı | Bu bölümdeki rolü |
|---|---|---|
| 10.7.1 | kapasite sayfası (kaynak 1) | §10.4 gerekçesi |
| 10.7.2 | BPF-tabanlı zamanlayıcı sayfası (kaynak 2) | §10.8/4 gerekçesi |
| 10.7.3 | zamanlama istatistikleri sayfası (kaynak 3) | §10.5.4-§10.5.5 gözlem kavramının kaynağı |
| 10.7.4 | üst sınır işlevi (kaynak 4) | §10.1 çifti |
| 10.7.5 | gönüllü bırakma işlevi (kaynak 5) | bloklama-yerine-bırakma tekniği (RT'de nadir, bilgi) |
| 10.7.6 | öncelik değeri işlevi (kaynak 6) | sıradan sınıf ayrı konu |
| 10.7.7 | Zephyr zamanlama + iş parçası (kaynaklar 7-8) | §4.4 modeli ile sınır |
| 10.7.8 | Windows iş parçası öncelik işlevi (kaynak 9) | Windows ayağı tek satır — derinlik ayrı dal |

### §10.8 Belirsizlik tablosu (§10 kapsamı)

| # | Konu | Durum | Davranış |
|---|---|---|---|
| 10.8.1 | kuantum sayısal değeri | ⚠️ VERIFICATION REQUIRED | yazılmaz |
| 10.8.2 | politika başına sayısal aralıklar | ⚠️ VERIFICATION REQUIRED | yalnız sözleşme (§10.2) |
| 10.8.3 | hedef donanım çekirdek düzeni | §8.3 E-3'ten devam eden açık | ölçüm PLANNED |
| 10.8.4 | BPF-tabanlı zamanlayıcının pratik etkisi | bilgi düzeyi | CoreMusic hedefi değil |
| 10.8.5 | ölçülmüş jitter değerleri | ölçülmedi | sayı yok |

### §10.9 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10'nın eklediği tek yeni tescil
§10.1'deki tek adres satırıdır; §9.1 kaynakçası aynen korunmuştur (§10.7
yalnız atıf tablosudur, yeniden tescil değil). Yeni ölçüm sonucu 0.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §10.10 Öncelik-aralığı seçim tablosu — ne zaman alt sınır, ne zaman üst sınır (doktrin)

§10.1-§10.2'deki aralık doktrinine bu bölüm seçim kuralı ekler:

| İş tipi | Aday aralık | Seçim | Gerekçe |
|---|---|---|---|
| kesme-benzeri, µs bütçeli | aralığın üst bölgesi | üst sınıra yakın | gecikme duyarlılığı birincil |
| ses-tüketimi, düzenli tampon | orta bölge | tutarlı-orta | hem gecikme hem adalet dengeli |
| toplu-iş, uzun CPU | alt bölge | alt sınıra yakın | adalet birincil, gecikme ikincil |
| servis/işlem (tekrarlayan) | aralığın tamamı | planlayıcıya bırakılır | sabit değer atamak planlamayı yanıltır |

**Kural:** aralık seçimi işin doğasına göre yapılır, sayının çekiciliğine
göre değil — yüksek sayı her iş için doğru değildir. Politika-aralık
sözleşmesi §10.2'de tanımlıdır; bu tablo o sözleşmenin seçim rehberidir.

### §10.11 Ölçüm protokolü — kapıların sırası (tarif, sayı yok)

§10.5'teki protokol-tasarımına bu bölüm kapı sırası ekler; adımlar bu turda
çalıştırılmaz, yalnız tarif edilir:

1. **öncülleri temizle** — arka planda çalışan ağır işleri durdur, sayaçları
   sıfırla (dış etken temizliği);
2. **tek değişkenli ölç** — bir seferde yalnız bir öncelik-aralığını değiştir;
   ikisi birden değişirse sonuç okunamaz;
3. **yeterli örnek topla** — tek ölçüm sonuç değildir; dağılımın kendisi
   ölçümün çıktısıdır;
4. **aynı koşulda tekrarla** — koşul değişmeden tekrar edilmeyen ölçüm
   doğrulanmış sayılmaz;
5. **kaydet** — sonuç dosyaya yazılır; zihinde tutulan ölçüm kayıtlı
   sayılmaz.

**Kapı durumu:** beş adımın tamamı bu turda uygulanmamıştır; kapı kapalı
değil, hiç başlatılmamıştır. Sayısal eşikler §10.5'te PLANNED işaretlidir —
bu ek eşik yazmaz.

### §10.12 RT kontrol listesi — uygulama-uzayı maddeleri (genişletme)

§10.6'daki listeye bu bölüm madde-içi kriter ekler:

| Madde | Kriter (neyle ölçülür) | Kriter dışı |
|---|---|---|
| öncelik | gerçek-öncelik yetkisi alınmış mı | yalnız-aralık-yazılmış olması |
| zamanlayıcı çözünürlüğü | beklenen-kayıp ölçümü | varsayılan-çözünürlük varsayımı |
| tampon | baştan ayrılmış ve sabit-mi | çalışma-anında-üretilen tampon |
| sayfa-hareketi | çalışma seti oturumda-kalmış-mi | ilk-erişim-değiştirme |
| gözlem | ölçüm-kaydı dosyaya-yazılmış-mi | gözle-görülerek-değerlendirme |

**Okuma kuralı:** her satırda ölçüt dışındakiler yasaklayıcı değildir —
"önerilmez" ile "yasak" ayrıdır; yalnız ilk satırda yetki alınmaması
doğrudan-kapalıdır (yetenek yoksa gerçek öncelik atanamaz, atanan değer
sahte kalır).

### §10.13 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10.10-§10.12 ekleri 0 URL, 0
repo iddiası, 0 ölçüm sonucu içerir; yalnız seçim tablosu, protokol tarifi ve
kontrol-listesi kriterlerinden oluşur. §10.1'deki tek adres tescili, §9.1
kaynakçası ve §10.9 dürüstlük kaydı aynen korunur; ölçüm kapıları bu ekle
açılmaz, sayılar uydurulmaz.

### §10.14 Zamanlama adaleti doktrini (derinleştirme eki 3 — tarif)

§10.10 seçim tablosuna bu bölüm adalet (fairness) boyutunu ekler:

| Politika | Adalet biçimi | Açgözlülük riski | Tersine çevirme yükü |
|---|---|---|---|
| round-robin | eşit dilim | yok (döngüsel) | düşük |
| öncelikli | öncelik eşitliği içinde eşit | düşük-öncelik açlığı olası | orta |
| en-uzun-bekleyen | bekleme süresi eşitliği | yok | yüksek (sıra taraması) |
| sabit-oran | oran paylaşımı | oran dışı süreç açlığı | düşük |

**Doktrin (tarif):** hangi politikanın adaletli olduğu, sistemden beklenen
hizmete bağlıdır — gerçek-zamanlı ses yolu için öncelikli politika
"adaletli" sayılmaz, çünkü adalet ölçütü burada gecikme bütçesidir;
genel amaçlı iş yükü için en-uzun-bekleyen adaletli sayılır. İkisini
aynı ölçütle değerlendirmek kategori hatasıdır.

**Tersine-geçiş kuralı:** politika değişimi (§10.11 protokolü) çalışma
anında yalnız sıradaki seçim noktasında uygulanır; o anda zaten atanmış
görev geri alınmaz — geri-alma politika değişikliğini bozar.

### §10.15 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§10.14) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 ölçüm sonucu eklenmiştir; yalnız adalet tablosu ve
geçiş doktrini tarif edilmiştir. §10.1'deki tek adres tescili ve §10.13
dürüstlük kaydı aynen korunur; ölçüm kapıları bu ekle kapanmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode