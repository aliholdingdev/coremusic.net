---
type: electronic
category: rtos
title: "CoreMusic — RTOS Integration"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — RTOS Integration

**See also:** [[electronic/firmware/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

RTOS Integration, CoreMusic ELECTRONICS platformunun gerçek zamanlı işletim sistemi entegrasyonunu tanımlar.

---

## 2. RTOS Seçenekleri

| RTOS | Platform | Kullanım |
|------|----------|----------|
| FreeRTOS | RPi5 | Embedded Linux |
| Xenomai | Linux | Real-time patch |
| bare-metal | XMOS | Zero-overhead |

---

## 3. Görev Öncelikleri

| Görev | Öncelik | Periyot |
|-------|---------|---------|
| Audio DSP | En yüksek | 10µs |
| I2S Transfer | Yüksek | 20µs |
| USB Audio | Yüksek | 100µs |
| Control | Orta | 1ms |
| Monitoring | Düşük | 100ms |

---

## 4. Real-Time Kısıtları

| Kural | Açıklama |
|-------|----------|
| No blocking | Audio thread'de I/O yasak |
| No malloc | Heap allocation yasak |
| Lock-free | Atomic operations |
| Priority inversion | Priority ceiling |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |

---

## 6. RTOS Seçim Kararı ve Gerekçesi

CoreMusic ELECTRONICS platformu tek bir RTOS'a kilitlenmez; hedef donanım ailesine göre üç farklı real-time strateji tanımlar. Seçim kararının arkasındaki gerekçe aşağıdaki matriste taşınır.

| Karar Kriteri | FreeRTOS (RPi5 / MCU) | Xenomai (Linux dual-kernel) | bare-metal (XMOS) |
|---------------|------------------------|------------------------------|--------------------|
| Zamanlama garantisi | Hard real-time (scheduler) | Hard real-time (deterministic shadow) | Tam deterministik (cycle-accurate) |
| Öğrenme eğrisi | Düşük | Yüksek (kernel patch) | Orta (ISA specific) |
| Bellek ayak izi | ~6–10 KB tipik çekirdek | Yüksek (yan Linux kernel) | Çok düşük (sıfır overhead) |
| Erişilebilirlik | Çok yaygın | Niş (endüstriyel) | Vendor lock-in (XMOS xCORE) |
| Sürücü ekosistemi | Zengin (middleware) | Linux sürücüleri paylaşılır | Vendor kütüphaneleri |
| CoreMusic kullanımı | Cihaz kontrol / yardımcı işler | Oda cihazı low-latency yol | DSP çekirdek (audio) |

### 6.1 Neden Tek RTOS Değil

Audio DSP yolu cycle-accurate determinizm ister (XMOS bare-metal); kontrol/yönetim yolu ise zengin sürücü ve ağ yığını ister (Linux + Xenomai patch). FreeRTOS ise gömülü yardımcı MCU'lar (güç kaynağı kontrolü, koruma röle kontrolü, termal izleme) için en düşük maliyetli real-time seçenektir. Bu üçleme, [[electronic/firmware/index]] içindeki katman sınırlarıyla uyumludur.

### 6.2 Karar Ağacı

```
Audio DSP yolu (I2S/TDM, <10µs jitter)
  └─> bare-metal / cycle-accurate (XMOS xCORE)

Oda cihazı, low-latency Linux uygulaması
  └─> Linux + Xenomai dual-kernel (COBALT)

Yardımcı kontrol MCU'su (güç, koruma, termal)
  └─> FreeRTOS (static allocation, priority ceiling)
```

---

## 7. Görev Zamanlama ve Scheduler Derinliği

Bölüm 3'teki öncelik tablosunun arkasındaki zamanlama modeli burada tanımlanır. CoreMusic real-time yığınında her görev için süreklilik (period), son teslim (deadline) ve en kötü durum yürütme süresi (WCET) baştan tanımlanır.

| Görev | Periyot | Deadline | Öncelik (0=en yüksek) | Bütçe (CPU %) | Yol |
|-------|---------|----------|------------------------|---------------|-----|
| Audio DSP | 10µs | 10µs | 0 | ~40 | bare-metal |
| I2S/TDM DMA | 20µs | 20µs | 1 | ~15 | bare-metal |
| USB Audio | 100µs | 125µs (1ms microframe) | 2 | ~10 | RTOS/Linux |
| Control (I²C/SPI) | 1ms | 2ms | 3 | ~5 | FreeRTOS |
| Monitoring (termal/log) | 100ms | 200ms | 4 | ~2 | FreeRTOS |

### 7.1 Rate-Monotonic Analiz (RMA)

Sabit periyotlu görevler için rate-monotonic zamanlama, en yüksek frekanslı göreve en yüksek öncelik verir. CoreMusic görev kümesinde toplam CPU yükü (U) hesabının, Liu-Layland sınırı olan `U ≤ n(2^(1/n) − 1)` (n→∞ için ~%69) altında kalması beklenir; n=5 için sınır ≈%74'tür. Ölçüm §12'de tartılır.

| Görev | Periyot T (ms) | WCET C (ms) | C/T |
|-------|----------------|-------------|-----|
| Audio DSP | 0.01 | 0.004 | 0.40 |
| I2S DMA | 0.02 | 0.003 | 0.15 |
| USB Audio | 0.1 | 0.01 | 0.10 |
| Control | 1.0 | 0.05 | 0.05 |
| Monitoring | 100 | 2.0 | 0.02 |
| **Toplam U** | — | — | **0.72** |

U = 0.72 < 0.74 sınırı; görev kümesi rate-monotonic altında zamanlanabilir (şart: her görevin en kötü durumunda son teslimine yetişmesi).

### 7.2 EDF Karşılaştırması

Earliest-Deadline-First (EDF) teorik olarak `U ≤ 1`'e kadar yükü taşıyabilir, ancak dinamik kuyruk yönetimi real-time öngörülebilirliği düşürür. CoreMusic, öncelik-sabit rate-monotonic modelini tercih eder (daha deterministik, daha az overhead).

---

## 8. Zaman Ayrılmış Bellek ve Sıfır-Allocasyon Kuralı

Audio thread'de heap allocation yasaktır (bkz. Bölüm 4). Bunun uygulanabilirliği, tüm görev ve tamponların başlatma (init) anında statik olarak ayrılmasına dayanır.

| Teknik | Açıklama | CoreMusic Kullanımı |
|--------|----------|---------------------|
| Static task allocation | `xTaskCreateStatic` benzeri statik görev yığını | Tüm görevler init'te |
| Static queue/buffer | Queue ve ring buffer önceden allocate | DMA tamponları |
| Memory pool | Sabit boyutlu blok havuzu | Mesaj bus kareleri |
| No dynamic new/delete | Çalışma zamanı allocation yasak | Audio yolu |
| Cache-line align | `alignas(64)` ile hizalama | Paylaşılan atomik sayaçlar |

### 8.1 Neden Heap Yasak

Dinamik allocation, real-time sistemde iki sorun üretir: (1) allocation süresi belirsizdir (fragmentasyon), (2) determinizm bozulur. Bu yüzden tüm tamponlar baştan allocate edilir; audio callback içinde `malloc`/`new` çağrısı yasaktır — bu kural kök `AGENTS.md` §ZERO-HALLUCINATION değil, mühendislik kuralıdır ve elektronik L6 hattının temelidir.

### 8.2 Bellek Bölme (Partitioning)

| Bölge | Kullanım | Erişim |
|-------|----------|--------|
| Audio-tight | DSP tamponları, coefficient | Sadece audio thread (tek yazar) |
| Control | Parametre, cihaz durumu | Lock-free atomik paylaşım |
| Monitoring | Log, ölçüm | Düşük öncelik, best-effort |

---

## 9. Priority Inversion ve Priority Ceiling

Düşük öncelikli bir görev, yüksek öncelikli bir görevi bir kilit (mutex) üzerinden bekletebilir; bu da medium öncelikli bir üçüncü görevin araya girmesine ve dolaylı (indirect) gecikmeye yol açar. CoreMusic bu durumu priority-ceiling protokolüyle önler.

| Kavram | Tanım | Önlem |
|--------|-------|-------|
| Priority inversion | Düşük öncelik kilit tutar, yüksek bekler | Priority ceiling |
| Priority ceiling | Kilit, en yüksek kullanıcının öncelikine "tavanlanır" | Mutex ceiling |
| Deadlock | Karşılıklı kilit bekleme | Tutarlı kilit sırası |
| Livelock | Sürekli geri çekilen ilerleme | Backoff / ceiling |

### 9.1 CoreMusic Kuralı

Audio thread hiçbir mutex beklemez. Kontrol ve audio arasındaki durum paylaşımı lock-free atomiklerle (§10) yapılır; dolayısıyla priority inversion audio yolu üzerinde yapısal olarak imkânsızdır.

---

## 10. Lock-Free Desenler ve Atomik İşlemler

Bölüm 4'teki "Lock-free: Atomic operations" maddesinin uygulanışı. Paylaşılan durum, tek yazar–çok okuyucu ya da kuyruk tabanlı desenlerle taşınır.

| Desen | Veri Akışı | Senkronizasyon |
|-------|------------|----------------|
| Single-producer single-consumer (SPSC) ring | Audio → Control | Cache-line atomik head/tail |
| Seqlock | Parametre güncelleme | Sayaç (sequence) çift okuma |
| Atomic flag/counter | Sayaç, durum | `memory_order_acquire/release` |
| RCU-like | Nadir okunan tablo | Epoch-based reclamation |
| Double-buffer | Coefficient yükleme | Toggle atomik |

### 10.1 Bellek Sıralama (Memory Ordering)

| Sıralama | Kullanım | Etki |
|----------|----------|------|
| relaxed | Sayaç artırma | Sıra garantisi yok |
| acquire | Okuyucu bayrak | Sonraki okuma bayrağa kadar bekler |
| release | Yazar bayrak | Önceki yazma bayrağa kadar görünür |
| acq_rel | Karşılıklı kilit | Her iki yön |

### 10.2 Cache-Coherency Uyarısı

False sharing, iki çekirdeğin aynı cache satırındaki farklı değişkenleri yazmasıyla performans düşürür. Bu yüzden atomik head/tail sayaçları `alignas(64)` ile ayrı satırlara yerleştirilir (kök `AGENTS.md` §alignas(64) kuralı).

---

## 11. Kesme (Interrupt) ve ISR Tasarımı

Gerçek zamanlı ses yolunda kesme yönetimi, gecikme bütçesinin kritik parçasıdır.

| Kural | Açıklama |
|-------|----------|
| Kısa ISR | ISR yalnız bayrak/kuyruk işler; hesap bekletilir |
| Deferred work | Uzun iş ISR sonrası "bottom-half" işleyiciye |
| Zero-copy | DMA tamponuna doğrudan erişim |
| Latency budget | ISR giriş→çıkış jitter'i < periyot |
| Preemption control | Kritik bölgede kesme kapatma (kısa) |

### 11.1 ISR → Görev Elçi (Handoff)

DMA tamponu dolduğunda ISR bir bayrak set eder ve hazır bir görevi `notify` eder; görev kuyruktan tamponu alır. Bu desen, ISR'ın kısa kalmasını ve hesabın öncelikli görevde yapılmasını sağlar.

---

## 12. Zamanlama Doğrulama ve WCET Ölçüm

Real-time iddiası, ölçülmüş en kötü durum yürütme süresi (WCET) ile desteklenir.

| Ölçüm | Yöntem | Hedef |
|-------|--------|-------|
| WCET | Cycle sayaçlı Profiler | Görev başına sabit üst sınır |
| Jitter | ISR giriş zaman damgası | Audio periyodundan düşük |
| CPU yükü | Idle oranından türetme | < %80 (başlık payı) |
| Deadline kaçışı | Bekleme süresi > deadline sayacı | 0 |
| Priority inversion | Lock bekleme kaydı | 0 (yapısal olarak imkânsız) |

### 12.1 Ölçüm Protokolü

1. Tüm görevler statik allocate edilir ve WCET'leri cycle sayaçlarıyla kaydedilir.
2. En kötü durum senaryosu (tüm kesmeler ve yüksek öncelikli görevler eşzamanlı) zorlanır.
3. Toplam yük §7.1'deki `U ≤ n(2^(1/n) − 1)` sınırı altında ve deadline kaçışı sıfır olmalıdır.
4. Ölçüm sonuçları `[[electronic/firmware/index]]` altındaki ilgili dosyalara işlenir.

> ⚠️ VERIFICATION REQUIRED: Gerçek donanım üzerinde cycle-accurate WCET ölçümü henüz yapılmamıştır; değerler tasarım hedefleridir, ölçülmüş sonuç değildir.

---

## 13. Repo Kanıtı

Repo'da RTOS/firmware (C++/C) kodu YOK — 2026-10-13 ölçümü: `0` adet `.cpp/.h/.c` dosyası, `0` adet `CMakeLists.txt`, `NevaEngine` dizini mevcut değil. Bu dosya salt spesifikasyon/PPROT seviyesindedir; kaynak `.ai/electronic/**`. Uydurma `Dosya:/Satır:` referansı yazılmaz.

---

## 14. Web Araştırması

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| FreeRTOS gerçek zamanlı işletim sistemi; statik görev/çizelgeleme desteği sağlar | https://www.freertos.org/ | 2026-10-13 | HTTP 200 doğrulandı |
| Xenomai, Linux üzerinde dual-kernel (COBALT) gerçek zamanlı ekler | https://xenomai.org/ | 2026-10-13 | HTTP 200 doğrulandı |
| Zephyr RTOS, ARM board'lar için donanım destek katmanı sağlar | https://docs.zephyrproject.org/latest/boards/arm/index.html | 2026-10-13 | HTTP 200 doğrulandı |

---

## 15. Kaynakça

1. FreeRTOS — resmi dokümantasyon: https://www.freertos.org/
2. Xenomai — real-time framework for Linux: https://xenomai.org/
3. Zephyr Project — ARM board documentation: https://docs.zephyrproject.org/latest/boards/arm/index.html
4. Liu & Layland, "Scheduling Algorithms for Multiprogramming in a Real-Time Environment" (rate-monotonic / EDF) — akademik referans; DOI/URL ⚠️ VERIFICATION REQUIRED.
5. CoreMusic Vault — `.ai/electronic/firmware/index.md`, `.ai/electronic/dsp/index.md`

---

## 16. RTOS Görev Durum Makinesi

Her görev, dört durum arasında geçiş yapar. CoreMusic zamanlama modeli (§7) bu durum makinesinin üzerine oturur.

| Durum | Açıklama | Geçiş Tetikleyicisi |
|-------|----------|---------------------|
| Ready | Çalışmaya hazır, CPU bekliyor | Zamanlayıcı seçimi |
| Running | CPU üzerinde çalışıyor | Zaman dilimi bitişi / preempt |
| Blocked | Bir olay (event/queue) bekliyor | Olay gerçekleşmesi |
| Suspended | Bilinçli olarak durdurulmuş | `Suspend` çağrısı |

### 16.1 Durum Geçiş Tablosu

| Kaynak → Hedef | Tetikleyici | CoreMusic Örneği |
|----------------|-------------|------------------|
| Ready → Running | Zamanlayıcı ataması | En yüksek öncelikli hazır görev |
| Running → Ready | Zaman dilimi / preempt | Daha yüksek öncelikli görev hazır oldu |
| Running → Blocked | Kuyruk/semaphore bekleyişi | Control görevi mesaj bekler |
| Blocked → Ready | Olay set edildi | ISR bir bayrak set etti |
| Running → Suspended | Bilinçli askıya alma | Monitoring görevi askıda |
| Suspended → Ready | Resume çağrısı | Test / bakım modu |

### 16.2 Bloklanma Yasağı (Audio Yolu)

Audio DSP ve I2S/DMA görevleri asla Blocked durumuna geçmez; her zaman Ready↔Running arasında döner. Bu, §4'teki "No blocking" kuralının durum-makinesi karşılığıdır.

---

## 17. Çekirdek API Eşleme Tablosu

Farklı RTOS sağlayıcıları (FreeRTOS / Zephyr / Xenomai / bare-metal) arasında kavramsal API eşleme. CoreMusic, platform-bağımsız bir soyutlama katmanı (HAL) üzerinden bu API'leri kullanır.

| Kavram | FreeRTOS | Zephyr | Xenomai (RTAI/Cobalt) | bare-metal (XMOS) |
|--------|----------|--------|------------------------|-------------------|
| Görev oluşturma | `xTaskCreate(Static)` | `k_thread_create` | `rt_task_create` | `run()` / core start |
| Bekleme | `vTaskDelay` | `k_sleep` | `rt_task_sleep` | Timer/loop |
| Kuyruk | `xQueueSend/Receive` | `k_fifo_put/get` | `rt_queue_send` | Channel/communication |
| Semaför | `xSemaphoreTake/Give` | `k_sem_take/give` | `rt_sem_p/v` | Flag/atomic |
| Mutex (ceiling) | `xSemaphoreTake` (priority inherit) | `k_mutex_lock` | N/A (lock-free tercih) | N/A |
| Zamanlayıcı | `xTimerCreate` | `k_timer_start` | `rt_timer_start` | Hardware timer |
| Atomik | `std::atomic` / port atomics | `atomic_*` | `std::atomic` | `std::atomic` / HW |
| Kesme yönetimi | `taskENTER_CRITICAL` | `irq_lock` | `rt_spin_lock` | `disable_irq` |

### 17.1 Soyutlama Katmanı Prensibi

Uygulama kodu doğrudan sağlayıcı API'sine bağlı değildir; `CoreMusic::rtos::*` soyutlama katmanı üzerinden çağırır. Bu, sağlayıcı değişiminde (örn. FreeRTOS → Zephyr) uygulama kodunun değişmemesini sağlar. Şimdilik bu soyutlama katmanının implementasyonu repo'da YOK (§13); tasarım seviyesindedir.

---

## 18. Zamanlayıcı Zaman Dilimi ve Tick Ayarları

Zamanlayıcı tick çözünürlüğü, jitter ve tepki süresini belirler.

| Ayar | Değer | Etki |
|------|-------|------|
| Tick periyodu | 1ms (tipik) / 100µs (hızlı) | Coarse scheduling çözünürlüğü |
| Tickless idle | Etkin | Düşük güç; tick kesmesini azaltır |
| Preemptive vs cooperative | Preemptive (CoreMusic) | Yüksek öncelik anında devralır |
| Time slicing | Eşit öncelikli görevler arasında round-robin | CPU paylaşımı |
| High-resolution timer | Audio jitter için gerekli | Sub-tick hassasiyet |

### 18.1 Tickless Idle ve Güç Yönetimi

Uygun olmayan (idle) durumda sabit tick kesmeleri gereksiz güç tüketir ve jitter yaratır. Tickless idle, en yakın görevin uyandırma zamanına kadar tick'i askıya alır. CoreMusic yardımcı MCU'larında (güç/termal) tickless idle; audio yolunda ise sabit yüksek çözünürlüklü tick tercih edilir (determinizm).

---

## 19. Çok-Çekirdek ve SMP Considerations

Hedef platformların bir kısmı çok-çekirdeklidir (ör. RPi5'in Cortex-A76 çiftleri, XMOS'un çok-çekirdekli xCORE). SMP altında zamanlama, paylaşılan kuyruklar ve çekirdek bağlama (affinity) gerektirir.

| Konu | Açıklama | CoreMusic Yaklaşımı |
|------|----------|---------------------|
| Çekirdek bağlama (affinity) | Görev sabit çekirdeğe sabitlenir | Audio yolu sabit çekirdek |
| NUMA / cache locality | Veriye en yakın çekirdek | Tampon çekirdek-yerel |
| False sharing | Aynı satırda paylaşılan atomikler | `alignas(64)` (§10.2) |
| Cross-core ISR | IRQ çekirdeklere dağıtılır | Audio IRQ sabit çekirdek |
| Lock-free SPSC | Çekirdekler arası kuyruk | Atomik head/tail (§10) |

### 19.1 Çekirdek Ayrıştırma (Partitioning) Stratejisi

| Çekirdek | Rol | Öncelik |
|----------|-----|---------|
| Çekirdek 0 | Audio DSP + I2S/DMA (sıcak yol) | 0–1 |
| Çekirdek 1 | USB Audio + Control | 2–3 |
| Çekirdek 2 (varsa) | Monitoring + Log | 4 |

Bu ayrıştırma, sıcak audio yolunun diğer işlerden kaynaklanan önbellek/veri yolu rekabetinden izole edilmesini sağlar.

---

## 20. Güç Yönetimi ve Uyku Modları

Yardımcı MCU'larda güç tüketimi kritiktir; audio yolunda ise düşük gecikme önceliklidir.

| Mod | CPU Durumu | Uyanma | Kullanım |
|-----|------------|--------|----------|
| Active | Tam çalışır | — | Audio yol |
| Idle | WFI (wait-for-interrupt) | Kesme | Kısa boşluk |
| Sleep | Clock gated | Timer/kesme | Control görevleri |
| Deep sleep | Bellek korunur, clock kapalı | Uzak olay | Bekleme modu |
| Shutdown | Bellek kaybolur | Reset | Kritik hata |

### 20.1 Uyanma Gecikmesi ve Bütçe

Uyku modundan tam çalışma durumuna geçiş, saat (clock) yeniden senkronizasyonu ve PLL kilitlenmesi gerektirir. Bu gecikme, gerçek zamanlı görevlerin periyoduna eklenmelidir. CoreMusic kuralı: audio yolu asla derin uykuya girmez; yalnız yardımcı MCU'lar uyur.

---

## 21. Hata Kurtarma ve Sağlık İzleme (Watchdog)

Gerçek zamanlı sistemde geri döndürülemez durumlar için watchdog ve sağlık izleme şarttır.

| Mekanizma | Açıklama | CoreMusic Kullanımı |
|-----------|----------|---------------------|
| Software watchdog | Görev canlılık (liveness) bayrağı | Her görev periyodik "kick" |
| Hardware watchdog | Donanım zamanlayıcısı, CPU kilitlenirse reset | Sistem geneli son çare |
| Task health monitor | Görev başına son-çalışma zaman damgası | Monitoring görevi kontrol |
| Deadline monitor | Deadline kaçışı sayacı | Alarm / log |
| Graceful degradation | Hata durumunda küçülme | Audio bypass / safe-mode |

### 21.1 Watchdog Hiyerarşisi

| Seviye | Kapsam | Müdahale |
|--------|--------|----------|
| Görev watchdog | Tek görev | Görev yeniden başlatma |
| Sistem watchdog | Tüm görevler | Sistem reset |
| Kriz kurtarma | Donanım kilidi | Güç döngüsü (relay) |

### 21.2 Audio Yolunda Hata Davranışı

Hata durumunda audio yolu susturulur (mute) ve safe-mode'a geçer; kontrol yolu log tutar ve kurtarma dener. DC offset koruması (§ elektronik kural 10) ile koordine olur: DC offset > 0.5V → koruma rölesi.

---

## 22. Zaman Kısıtlı Test ve Doğrulama Matrisi

Real-time davranış, test edilmeden iddia edilemez. CoreMusic doğrulama matrisi:

| Test | Yöntem | Kabul Kriteri |
|------|--------|----------------|
| Deadline testi | Yük altında stres | 0 deadline kaçışı |
| Jitter testi | ISR/çıkış zaman damgası | Jitter < periyot |
| WCET testi | Cycle sayaçlı profil | Ölçülen ≤ tahmin |
| Priority inversion testi | Kilit senaryosu | 0 inversion (yapısal) |
| Bellek testi | Allocation izleme | Audio yolunda 0 alloc |
| Watchdog testi | Görev askıya alma | Zamanında kurtarma |
| Uzun süre stabilite | 72h soak | Sıfır kilitlenme |

### 22.1 Test Ortamı

Doğrulama, hedef donanım + simülasyon (QEMU/XMOS simulator) kombinasyonuyla yapılır. Simülasyon, WCET ve durum-geçişlerini hızlı doğrular; nihai jitter/jitter ölçümü gerçek donanımda yapılır. ⚠️ VERIFICATION REQUIRED: Bu testler henüz repo'da otomatikleştirilmemiştir; tasarım seviyesindedir.

---

## 23. Terimler ve Kısaltmalar

| Terim | Açıklama |
|-------|----------|
| RTOS | Real-Time Operating System |
| WCET | Worst-Case Execution Time (en kötü durum yürütme süresi) |
| ISR | Interrupt Service Routine |
| RMA | Rate-Monotonic Analysis |
| EDF | Earliest-Deadline-First |
| SMP | Symmetric Multi-Processing |
| SPSC | Single-Producer Single-Consumer |
| TDM | Time-Division Multiplexing (ses veri yolu) |
| jitter | Zamanlama dalgalanması |
| deadline | Görevin tamamlanması gereken son an |
| ceiling | Priority ceiling (öncelik tavanı) |

---

## 24. Cross-Referanslar ve Yol Haritası

| Konu | İlgili Dosya |
|------|--------------|
| Firmware genel bakış | [[electronic/firmware/index]] |
| Boot sırası | [[boot-sequence]] |
| DSP firmware & clocking | [[dsp-firmware]] |
| OTA güncelleme | [[ota-update]] |
| DSP zinciri | [[electronic/dsp/index]] |
| Donanım arayüzü | [[electronic/hardware/index]] |
| ADR-017 (DSP hardware mode) | [[ADR-017-dsp-hardware-mode]] |

### 24.1 Yol Haritası

| Aşama | İçerik | Durum |
|-------|--------|-------|
| Faz 1 | RTOS seçim matrisi + zamanlama tasarımı | Tasarım (bu dosya) |
| Faz 2 | Soyutlama katmanı (HAL) API sözleşmesi | PLANNED (repo kodu YOK) |
| Faz 3 | WCET/jitter ölçüm altyapısı | PLANNED (ölçüm PLANNED) |
| Faz 4 | Donanım üzerinde doğrulama | PLANNED |

---

## 25. Başlangıç (Boot) → RTOS Devir Sekansı

Sistem açılışında RTOS'un devraldığı nokta, §11'deki ISR tasarımı ve §7'deki zamanlama modelinin geçerlilik kazandığı andır.

```
Reset / Bootloader (bkz. [[boot-sequence]])
    ↓
Statik bellek init (bölüm 8 — tüm tamponlar allocate)
    ↓
Sabit veri & coefficient yükleme (audio DSP için)
    ↓
Kesme vektörleri & timer yapılandırması (bölüm 11, 18)
    ↓
Görev oluşturma (statik, §7 öncelik sırasıyla)
    ↓
Zamanlayıcı başlat (scheduler start — asla geri dönmez)
    ↓
Audio yolu aktif (DSP + I2S/DMA) · Control · Monitoring
```

### 25.1 Devir Sırası Kuralı

Görevler, öncelik sırasıyla (en yüksek önce) oluşturulur; zamanlayıcı yalnızca tüm görevler hazırlandıktan sonra başlatılır. Böylece ilk tick'te en yüksek öncelikli (Audio DSP) görev ilk çalışır.

---

## 26. Mesaj Bus ve Olay Kuyruğu Tasarımı

Servisler ve görevler arası iletişim, gerçek zamanlı kısıtları koruyarak message-bus üzerinden yürütülür.

| Katman | Mekanizma | Real-Time Etkisi |
|--------|-----------|------------------|
| Görev-içi (same core) | SPSC ring / atomik | Sıfır kilit, deterministic |
| Görevler-arası (cross core) | Lock-free kuyruk | Atomik head/tail |
| Servisler-arası (system) | Event-driven message bus | Best-effort, non-real-time |
| Log/monitoring | Async ring → disk | Düşük öncelik, asla bloklamaz |

### 26.1 Real-Time Olmayan Yolun İzolasyonu

Mesaj bus üzerindeki servisler-arası iletişim (system message bus) real-time audio yolundan **fiziksel olarak ayrılır**; audio yolu yalnız SPSC ring ve atomik sayaçlar kullanır. Bu, §9'daki priority-inversion'ın yapısal olarak imkânsız kalmasını güvence altına alır.

---

## 27. Yapılandırma Parametreleri (Özet)

| Parametre | Önerilen Değer | Not |
|-----------|----------------|-----|
| Tick periyodu | 100µs (audio-yakın) / 1ms (yardımcı) | §18 |
| Zamanlayıcı tipi | Preemptive rate-monotonic | §7 |
| Bellek modeli | Statik / zero-alloc (audio) | §8 |
| Bellek sıralaması | acq/rel atomik | §10.1 |
| Watchdog timeout | Görev periyotunun ~10×'i | §21 |
| Çekirdek bağlama | Audio sabit çekirdek | §19 |
| CPU yükü hedefi | < %80 (başlık payı) | §12 |

---

## 28. İzleme (Monitoring) Metrikleri

| Metrik | Kaynağı | Eşiği |
|--------|---------|-------|
| CPU yükü | Idle oranından | < %80 |
| Jitter (audio) | ISR/çıkış damgası | < periyot |
| Deadline kaçışı | Bekleme sayacı | 0 |
| Queue doluluk | Kuyruk seviyesi | < %80 (taşma yok) |
| Bellek (yardımcı) | Heap watermark | Sabit (statik) |
| Watchdog kick | Canlılık damgası | Zamanında |
| Görev-üstü CPU | Görev başına sayaç | WCET ≤ tahmin |

### 28.1 Metrik Toplama Prensibi

İzleme, audio yolunu bloklamayan atomik sayaçlarla yapılır (§10); ayrıştırma ve log yazımı düşük öncelikli Monitoring görevinde gerçekleşir (§7). Böylece gözlemlenebilirlik, real-time garantileri bozmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
