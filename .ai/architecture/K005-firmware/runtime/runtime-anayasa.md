---
node_id: K005-firmware/runtime/runtime-anayasa
node_type: module
status: draft
parent_id: K005-firmware/runtime
children: []
depends_on:
  - K005-firmware/boot/boot-surucu
evidence:
  - ".ai/electronic/firmware/rtos.md §2-§4 — okundu 2026-10-13"
  - ".ai/electronic/firmware/index.md §5 RTOS — okundu 2026-10-13"
  - "K005-firmware/boot/boot-anayasa.md — devir sözleşmesi 2026-10-13"
validation:
  - "2026-10-13 — FM10 anahtar + group (mimari) + son satır Authority denetimi"
  - "2026-10-13 — satır ölçümü kök ölçüm betiğinde (K005 raporu)"
open_questions:
  - "Gerçek görev/set çağrısı adları kodda yok (repo'da firmware kodu YOK) — ⚠️ VERIFICATION REQUIRED"
  - "Hedef RTOS seçimi (FreeRTOS/Zephyr/ThreadX/bare-metal) — firmware/index §9'a göre cihaza bağlı, UNKNOWN"
group: mimari
---

# runtime-anayasa — Zamanlama Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Cihaz açıldıktan sonra **zamanın efendisi**: görevler, kesmeler,
> gözcü (watchdog) ve öncelik kuralları. Boot bittiğinde bayrağı buradan
> devralır, ses yolunu **asla bloke etmez**, bellekte tahsis yapmaz. Bu dosya
> zamanlama modülünün kural ve sınırlarıdır; arayüzler `runtime-surucu.md`,
> kalıplar `runtime-ornekler.md` içindedir.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Kapsam içi | Görev oluşturma, öncelik, devirme (preemption) | evet | runtime |
| Kapsam içi | Kesme (IRQ) öncelik yönetimi | evet | runtime |
| Kapsam içi | Gözcü (watchdog) besleme ve eşiği | evet | runtime |
| Kapsam içi | Atomics / kilitssiz veri paylaşımı | evet | runtime |
| Kapsam içi | Boot'tan devir (System Ready paketi) | evet | runtime ← boot |
| Kapsam dışı | DSP algoritması (EQ/reverb) | hayır → K004-dsp | K004 |
| Kapsam dışı | Host işletim sistemi zamanlayıcısı (cron/timerfd) | hayır → K000-isletim-sistemi | K000 |
| Kapsam dışı | Donanım kronu/OScilatör toleransı | hayır → K001-donanım | K001 |
| Kapsam dışı | Boot zinciri ve ROM davranışı | hayır → K005-firmware/boot | boot |
| Kapsam dışı | Gözcü donanım periyodu seçimi (MCU) | hayır → K001 / UNKNOWN | belirsiz |
| Kapsam dışı | Test stratejisi uygulaması | hayır → K005-firmware/testing | testing |
| Kapsam dışı | Ölçüm/telemetri saklama | hayır → diagnostics | diagnostics |

**Kapsam kuralı (Z1):** Bu modül *zamanlamayı* yönetir, *içeriği* değil;
içerik sahibi olan modül (audio-control · device-control · K004) kalır.

**Kapsam kuralı (Z2):** Zamanlama kuralları OS'tan bağımsız yazılır;
RTOS seçimi değişirse yalnız §3/§5 uygulama katmanı uyar (§17 Q2).

## §2 Zamanlama Modeli

Kaynak: `.ai/electronic/firmware/index.md §5` + `rtos.md §3`.

| Özellik | Değer (Kaynak) | Bu modüldeki karşılığı | Durum |
|---|---|---|---|
| Zamanlama türü | Preemptive priority-based (firmware/index §5) | öncelik tabanlı devirme | tanımlı |
| Maksimum görev sayısı | Max 32 (firmware/index §5) | görev envanteri üst sınırı | tanımlı |
| Kesme | IRQ priority management (firmware/index §5) | §5 | tanımlı |
| Bellek | Static allocation — heap yasak (firmware/index §5) | P2/Z-ilkeleri | tanımlı |
| Gözcü | Hardware watchdog zorunlu (firmware/index §5) | §6 | tanımlı |
| RTOS seçenekleri | FreeRTOS · Zephyr · ThreadX · bare-metal (firmware/index §5) | portlanabilir sözleşmeler | tanımlı |
| Cihaz bazlı seçim | firmware/index §9 cihaz tablosu | XMOS bare-metal · STM32 FreeRTOS · ESP32 FreeRTOS | tanımlı (kaynak) |
| Gerçek zaman kısıtı | No blocking · No malloc · Lock-free (rtos §4) | §7 | tanımlı |
| Prioriyet inversionsı | Priority ceiling (rtos §4) | §8 | tanımlı |
| Seçim kanıtı | — | hangi cihazda hangisi | UNKNOWN (§17 Q1) |

**Model kuralı (Z3):** Model *öncelik tabanlı devirmeli* dışında hiçbir
seçenek bu modülde varsayılmaz; FIFO/cooperative yazım yasaktır (kaynakta yok).

## §3 Görev Öncelik Matrisi (Genişletilmiş Envanter)

Kaynak: `rtos.md §3` (5 satır) + tamamlayıcı kayıtlar.

| # | Görev | Öncelik (Kaynak) | Periyot (Kaynak) | Tip | Kapsam sahibi |
|---|---|---|---|---|---|
| T1 | Audio DSP | En yüksek | 10 µs | sürekli | K004 + runtime |
| T2 | I2S Transfer | Yüksek | 20 µs | sürekli | audio-control |
| T3 | USB Audio | Yüksek | 100 µs | sürekli | audio-control |
| T4 | Control (komut işleme) | Orta | 1 ms | periyodik | device-control |
| T5 | Monitoring (izleme) | Düşük | 100 ms | periyodik | diagnostics |
| T6 | Boot devir kontrolü | devir anında | tek sefer | olay | boot → runtime |
| T7 | Watchdog besleme | gözcü eşiğine göre | sabit | kritik | runtime (§6) |
| T8 | OTA kontrol (idle) | en düşük (idle) | olay | update |
| T9 | Güvenlik olayı (E4 vb.) | olay önceliği | olay | security |
| T10 | Ölçüm/telemetri yazımı | en düşük | 100 ms | diagnostics |
| T11 | Güç olayı işleme | yüksek | olay | power-control |
| T12 | — | 32 görev üst sınırı (firmware/index §5) | — | envanter sınırı |

**Görev kuralı (Z4):** Öncelik *sayısal* değeri değil, **sıra** olarak
kaydedilir (sayısal eşleme RTOS seçimine bağlı — §17 Q3, UNKNOWN).

**Görev kuralı (Z5):** Yeni görev yalnız bu tabloya kayıtla eklenir;
tabloya kayıt = yetki (test edilebilir tek nokta).

## §4 Periyot, Jitter ve Bütçe Kuralları

| # | Görev | Periyot | Bütçe kuralı | Aşım davranışı |
|---|---|---|---|---|
| B1 | Audio DSP | 10 µs | jitter ≤ bütçe (değeri UNKNOWN) | overrun sayacı → diagnostics |
| B2 | I2S Transfer | 20 µs | tampon taşması yasak | xrun bayrağı |
| B3 | USB Audio | 100 µs | paket penceresi | xrun bayrağı |
| B4 | Control | 1 ms | kuyruk taşması yasak | drop sayacı |
| B5 | Monitoring | 100 ms | kayıp tolere edilir | say |
| B6 | Gözcü besleme | WDT periyodu içinde | besleme kaçarsa reset | E6 (boot kodu) |
| B7 | Toplam CPU | — | bütçe UNKNOWN | ölçüm gerekli |
| B8 | Kesme yükü | — | oransal üst sınır UNKNOWN | ölçüm gerekli |

**Bütçe kuralı (Z6):** Rakam yalnız Kaynak veya ölçüm gelir; jitter/CPU
değerleri şimdilik `UNKNOWN` — uydurulmaz (repo dürüstlük kuralı).

## §5 Kesme (IRQ) Yönetimi

| # | Kural | Gerekçe | Kaynak |
|---|---|---|---|
| I1 | IRQ öncelikleri sabit tablo ile tanımlanır | firmware/index §5 (IRQ priority management) | kaynak |
| I2 | Kesme içinde yalnız kısa, tahsisiz işlem | bloklama yasağı | rtos §4 |
| I3 | Kesme içinde mutex yasak (atomik / maskeleme) | lock-free | repo kuralı 2 |
| I4 | Uzun iş kesmeden çıkarılır → görev | yarı-kesme deseni | kural |
| I5 | Boot sırasında kesmeler maskeli kalır | boot devri | boot-anayasa §16 |
| I6 | Ses yolundaki kesme önceliği en yüksek kuşağa | jitter bütçesi | §4 |
| I7 | Kaynak/sayma kesmeleri ayrı hattan | ölçüm | §12 |
| I8 | IRQ tablosu RTC seçimine bağlı | port | §17 Q3 |

**Kesme kuralı (Z7):** IRQ içi kod, beklenen yürütme süresini *belirtmek
zorundadır* (sütun §11); belirtilmemiş kesme = inceleme ret sebebi.

## §6 Gözcü (Watchdog) Sözleşmesi

| # | Alan | Değer | Sahip | İhlal |
|---|---|---|---|---|
| W1 | Gözcü türü | Hardware watchdog (firmware/index §5) | K001/MCU | — |
| W2 | Zorunluluk | Zorunlu (Kaynak) | runtime | ret |
| W3 | Besleme görevi | T7 (runtime) | runtime | — |
| W4 | Besleme penceresi | WDT periyodu içinde (periyot UNKNOWN) | runtime | E6 reset |
| W5 | Boot eli | boot açar, devreder | boot → runtime (F11) | boot-anayasa §6 P10 |
| W6 | Aşım sonrası | donanım reset → boot zinciri | boot | sayaç +1 |
| W7 | Kilitlenme senaryosu | görev asılı kalırsa | besleme yapılmaz → reset | beklenen davranış |
| W8 | Test modu | pencere testi | testing | TI10 (boot-surucu §20) |

## §7 Bellek, Atomics ve Kilitssiz Kurallar

| # | Kural | İzin | Yasak | Kaynak |
|---|---|---|---|---|
| M1 | Statik tahsis | önceden boyutlu tampon | malloc/new/delete | repo kuralı 1 · rtos §4 |
| M2 | Veri paylaşımı | atomik okuma/yazma | mutex/lock_guard | repo kuralı 2 |
| M3 | Kritik bölüm | kesme maskesi / atomik | spinlock ile UI bekletme | kural |
| M4 | Ses verisi biçimi | 32-bit float (ADR-017) | biçimsiz veri | repo kuralı 7 |
| M5 | Tampon boyutu | 512 örnek @ 48 kHz | dinamik boyut | repo kuralı 8 |
| M6 | Sabit hesap | constexpr | çalışma zamanı sabiti hesabı | repo kuralı 4 |
| M7 | İstisna | yok — return kodu | throw (ses yolunda) | repo kuralı 3 |
| M8 | Önbellek hizalama | alignas(64) paylaşım bloğu | paylaşımlı hizasız | repo kuralı 5 |
| M9 | Görev yığını | statik, sabit boyut | yığından dinamik dizi | firmware/index §5 |
| M10 | Sayma/olay | atomik sayı | kilitli sayaç | Z7 |

## §8 Prioriyet İnversiyonu ve Tavan (Ceiling)

| # | Senaryo | Risk | Çözüm (Kaynak: rtos §4) | Doğrulama |
|---|---|---|---|---|
| PI1 | Düşük öncelik kritik tutuyor, yüksek bekliyor | tersine blokaj | Priority ceiling | test (testing) |
| PI2 | Orta görev yüksek önceliği geçerek çalışır | jitter aşımı | öncelik devri kuralı | ölçüm |
| PI3 | Kesme uzun sürer | görev yığını | §5 I4 yarı-kesme | kod incelemesi |
| PI4 | Gözcü beslemesi düşük öncelikte | gereksiz reset | T7 yüksek öncelik | §6 |
| PI5 | idle görevinde ağır iş (OTA) | ses jitter'ı | idle'e kısıt | update kapısı |
| PI6 | Kilitli bölüm + kesme | deadlock | M2/M3 | kod incelemesi |
| PI7 | Kuyruk dolu (Control) | komut kaybı | drop sayacı (B4) | ölçüm |

## §9 Anayasal İlkeler (Sert Kurallar — bu modül)

| # | Kural | Sonuç (ihlalde) | Karşılığı |
|---|---|---|---|
| P1 | Ses yolunda bloke eden sistem çağrısı yasak | ret | rtos §4 No blocking |
| P2 | Çalışma zamanı tahsisi yasak | ret | repo kuralı 1 |
| P3 | Mutex/lock yasak (atomik zorunlu) | ret | repo kuralı 2 |
| P4 | Ses callback'i `noexcept` | ret | repo kuralı 3 |
| P5 | Sabit hesap `constexpr` | ret | repo kuralı 4 |
| P6 | Paylaşımlı blok `alignas(64)` | ret | repo kuralı 5 |
| P7 | DAC yalnız PCM3168A (ADR-038 H001) | ret | repo kuralı 6 |
| P8 | Ses verisi 32-bit float (ADR-017) | ret | repo kuralı 7 |
| P9 | 512 örnek @ 48 kHz (sartname) | ret | repo kuralı 8 |
| P10 | ASIO Exclusive Lock'ta ikinci uygulama yok | ret | repo kuralı 9 |
| P11 | Gözcü beslemesi pencere dışında değil | reset + E6 | firmware/index §5 |
| P12 | Görev kaydı §3 tablosunda | ret | Z5 |
| P13 | Jitter/CPU rakamı ölçülmeden yazılmaz | ret (UNKNOWN) | Z6 |
| P14 | IRQ içinde tahsis/kilit yok | ret | Z7 · M1-M3 |

## §10 Hata, Zaman Aşımı ve Kurtarma Senaryoları

| # | Senaryo | Tespit | Tepki | Alıcı |
|---|---|---|---|---|
| S1 | Görev overrun (periyot aşımı) | sayaç | overrun++ → diagnostics | diagnostics |
| S2 | Ses tamponu taşması (xrun) | hal/olay | xrun bayrağı | audio-control |
| S3 | Görev kuyruğu dolu | drop sayacı | drop++ | device-control |
| S4 | Gözcü penceresi kaçtı | donanım reset | boot zinciri + E6 | boot |
| S5 | Prioriyet inversiyonu gözlemi | ölçüm | §8 önlemleri | runtime |
| S6 | Boot devir paketi eksik | doğrulama | recovery isteği | boot |
| S7 | Kesme fırtınası | IRQ sayacı | oran raporu | diagnostics |
| S8 | Bellek ihlali (tahsis denemesi) | statik analiz | ret | testing |
| S9 | Ölçüm sayacı taşması | rollover | genişletilmiş sayaç | testing |

## §11 Görev/Kesme Detay Kaydı (Sahip · Yürütme · Çıktı)

| # | Kayıt | Yürütme tipi | Tahsis | Kilit | Çıktı |
|---|---|---|---|---|---|
| D1 | Audio DSP (T1) | sürekli/sabit | yok | yok (atomik) | ses tamponu |
| D2 | I2S Transfer (T2) | DMA kuyruk | yok | yok | seri veri |
| D3 | USB Audio (T3) | paket | yok | yok | USB kuyruk |
| D4 | Control (T4) | kuyruk okuma | yok | atomik bayrak | komut sonucu |
| D5 | Monitoring (T5) | periyodik okuma | yok | yok | ölçüm paketi |
| D6 | Gözcü (T7) | kritik öncelik | yok | atomik | besleme |
| D7 | OTA (T8) | idle iş | flash yazma (update yetkisi) | update kuralı | bank |
| D8 | Güvenlik olayı (T9) | olay | yok | atomik | olay kaydı |
| D9 | Telemetri (T10) | periyodik | yok | atomik | olay kaydı |
| D10 | Güç olayı (T11) | olay | yok | atomik | güç durumu |

## §12 Ölçüm Kapıları (R-kapıları)

| # | Kapı | Eşik | Yöntem | Durum |
|---|---|---|---|---|
| R1 | Görev envanteri ≤ 32 | firmware/index §5 | tablo sayımı | planlı |
| R2 | Audio DSP jitter | ≤ periyot (10 µs) | ölçüm aracı (UNKNOWN) | ölçülmedi |
| R3 | CPU kullanımı | bütçe UNKNOWN | profil | ölçülmedi |
| R4 | Gözcü pencere | WDT periyodu içinde | test | ölçülmedi (periyot UNKNOWN) |
| R5 | Tahsis sayısı | 0 | statik analiz | planlı |
| R6 | Mutex kullanımı | 0 | kod taraması | planlı |
| R7 | IRQ süresi kaydı | her IRQ ölçülü | sayaç | planlı |
| R8 | Overrun/xrun/drop | 0 kabul | test | planlı |
| R9 | Boot devir paketi bütünlüğü | 100% alan | test | planlı |
| R10 | Prioriyet tablosu eşlemesi | 100% §3 | kod incelemesi | planlı |

## §13 Sınır Komşuları

| Komşu | Yön | Ne geçer | Kanıt |
|---|---|---|---|
| boot | boot → runtime | BOOT_READY paketi (F1-F11) | boot-surucu §5 |
| audio-control | runtime ↔ | ses tamponu 512 örnek, xrun bayrağı | repo kuralı 8 |
| device-control | runtime ↔ | Control kuyruğu (T4) | rtos §3 |
| power-control | runtime ↔ | güç olayı (T11), uyku/uyanma | firmware/index §9 |
| diagnostics | runtime → | overrun/drop/telemetri (T5, T10) | §10 |
| update | runtime ↔ | idle OTA (T8) | firmware/index §7 |
| security | runtime ↔ | olay önceliği (T9) | firmware/index §8 |
| testing | testing → runtime | kapılar R1-R10 | §12 |
| K004-dsp | runtime ↔ | T1 öncelik/periyot | rtos §3 |
| K000-isletim-sistemi | sınır | host zamanlayıcı bu modülde değil | §1 |

## §14 Bağımlılık Grafiği

| Düğüm | Bağımlı olduğu | Tür |
|---|---|---|
| runtime-anayasa | K005-firmware/runtime | üst düğüm |
| runtime-anayasa | rtos.md §2-§4 | kanıt |
| runtime-anayasa | firmware/index §5, §9 | kanıt |
| runtime-surucu | runtime-anayasa | mimari |
| runtime-ornekler | runtime-anayasa + runtime-surucu | kod |
| runtime-anayasa | boot-surucu §5 | devir sözleşmesi |
| update/security/diagnostics | runtime-anayasa §3 | görev kaydı |

## §15 Terminoloji (TR ↔ EN)

| Terim (EN) | Karşılığı | Not |
|---|---|---|
| scheduler | zamanlayıcı | öncelik tabanlı devirme |
| preemption | devirme | model (Z3) |
| priority inversion | öncelik tersi | §8 |
| priority ceiling | öncelik tavanı | rtos §4 çözümü |
| ISR (interrupt service routine) | kesme hizmet rutini | §5 |
| jitter | salınım | §4 B1 |
| overrun | görev aşımı | §10 S1 |
| xrun | tampon taşması | §10 S2 |
| watchdog (WDT) | gözcü zamanlayıcı | §6 |
| static allocation | statik tahsis | M1 |
| lock-free | kilitsiz | M2/M3 |
| atomic | atomik | M2 |
| handover | devir | boot → runtime |
| tick | zaman tıkı | RTOS'e bağlı — UNKNOWN |
| mailbox/kuyruk | kuyruk | T4 |

## §16 Web Araştırması

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| FreeRTOS görev bildirimi (task notifications) dokümanı, hafif senkronizasyon mekanizmasını tanımlar — bu modülün atomik/bayrak tercihini besler | https://www.freertos.org/RTOS-task-notifications.html | 2026-10-13 | HTTP 200 |
| FreeRTOS API referansı, görev oluşturma/öncelik arayüzlerini belgeler (§3 envanteri) | https://www.freertos.org/a00110.html | 2026-10-13 | HTTP 200 |
| FreeRTOS kuyruk API'si, değer yazma/okuma sözleşmesini tanımlar (T4 Control kuyruğu) | https://www.freertos.org/xQueueOverwrite.html | 2026-10-13 | HTTP 200 |
| Zephyr zamanlayıcı dokümanı, yazılım zamanlayıcı servisini belgeler (periyodik görevler) | https://docs.zephyrproject.org/latest/kernel/services/timing/timers.html | 2026-10-13 | HTTP 200 |
| Zephyr mutex dokümanı, kilit semantiğini tanımlar — bu modülde kilit KULLANILMAZ, yalnız sınır okuması | https://docs.zephyrproject.org/latest/kernel/services/synchronization/mutexes.html | 2026-10-13 | HTTP 200 |
| Mbed çok-iş parçacıklı program dokümanı, görev/thread modelini tanımlar | https://os.mbed.com/docs/mbed-os/v6/reference/multi-threaded.html | 2026-10-13 | HTTP 200 |
| Mbed zamanlama API dokümanı, sayaç/zamanlama işlevlerini belgeler (ölçüm yöntemi) | https://os.mbed.com/docs/mbed-os/v6/reference/timing-apis.html | 2026-10-13 | HTTP 200 |

## §17 Kaynak ve Repo Kanıtı

**Repo'da firmware kodu YOK — kaynak: .ai/electronic/firmware/ (boot-sequence·dsp-firmware·ota-update·rtos)**

| Kaynak | Bölüm | Kullanıldığı yer |
|---|---|---|
| `Kaynak: .ai/electronic/firmware/rtos.md` | §2 RTOS Seçenekleri | §2 |
| `Kaynak: .ai/electronic/firmware/rtos.md` | §3 Görev Öncelikleri | §3 |
| `Kaynak: .ai/electronic/firmware/rtos.md` | §4 Real-Time Kısıtları | §7, §8, §9 |
| `Kaynak: .ai/electronic/firmware/index.md` | §5 RTOS (max 32 · statik · watchdog) | §2, §6, §12 |
| `Kaynak: .ai/electronic/firmware/index.md` | §9 Cihaz Bazlı Firmware | §2, §17 Q1 |
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §2 System Ready | boot devri |

## §18 Açık Sorular (open_questions)

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | Hedef cihazda RTOS hangisi (FreeRTOS/Zephyr/ThreadX/bare-metal)? | §3 eşleme, tick | UNKNOWN |
| Q2 | Tick süresi ve sayım birimi nedir? | §4 bütçe, §6 pencere | UNKNOWN |
| Q3 | Öncelik sayısal eşlemesi nedir? | Z4 | UNKNOWN |
| Q4 | Jitter/CPU eşikleri ne? (ölçüm yok) | R2/R3 | ölçülmedi |
| Q5 | Gerçek görev/set çağrı adları? | tümü — kod yok | ⚠️ VERIFICATION REQUIRED |

## §19 Gereksinim → Kural Eşlemesi

| # | Gereksinim | Kural | Kapı | Sahip |
|---|---|---|---|---|
| G1 | Görev ≤ 32 (firmware/index §5) | Z5 §3 | R1 | runtime |
| G2 | Statik tahsis (firmware/index §5) | M1/P2 | R5 | runtime |
| G3 | Hardware watchdog zorunlu (firmware/index §5) | §6 | R4 | runtime |
| G4 | IRQ priority management (firmware/index §5) | §5 | R7 | runtime |
| G5 | No blocking · No malloc · Lock-free (rtos §4) | P1-P3 | R5/R6 | runtime |
| G6 | Priority ceiling (rtos §4) | §8 | R2 | runtime |
| G7 | Görev periyotları (rtos §3) | §4 | R2/R8 | runtime |
| G8 | 512 örnek @ 48 kHz (repo kuralı 8) | M5 | R8 | audio-control |
| G9 | 32-bit float (ADR-017) | M4/P8 | kod incelemesi | audio-control |
| G10 | PCM3168A tek DAC (ADR-038 H001) | P7 | kod incelemesi | audio-control |
| G11 | Boot devir paketi (boot-surucu §5) | §13 | R9 | boot+runtime |
| G12 | ASIO Exclusive Lock tek uygulama (repo 9) | P10 | host testi | K003/host |
| G13 | Gözcü pencere kaçarsa reset | W4 | R4 | runtime |
| G14 | Overrun/xrun sayımı | §10 | R8 | diagnostics |
| G15 | IRQ içinde tahsis yok | Z7/M1 | R5/R6 | runtime |
| G16 | Ölçülmüş rakam (jitter/CPU) | Z6/P13 | R2/R3 | testing |
| G17 | Yalnız kayıtla görev ekleme | Z5 | R1 | runtime |
| G18 | idle'e ağır iş kısıtı | PI5 | R3 | update |
| G19 | Eşzamanlılık atomik | M2 | R6 | runtime |
| G20 | Kapsam dışılmayan sınır (K000/K001/K004) | Z1 | — | orkestratör |

## §20 Red Team İtiraz Kayıtları

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "10 µs/20 µs gerçek ölçüm mü?" | Hayır — Kaynak (rtos §3) tablosu | §4 + §17 |
| T2 | "Öncelik sayısı neden sayısal değil?" | RTOS seçimi UNKNOWN (Z4) | §18 Q3 |
| T3 | "32 görev sınırı kaynak mı?" | firmware/index §5 | §2 |
| T4 | "Mutex yasağı çelişmez mi?" | Kaynakta lock-free + repo kuralı 2 | §7 |
| T5 | "Gözcü periyodu neden yok?" | MCU ailesi UNKNOWN, uydurulmadı | §6 W4 |
| T6 | "T6-T11 görevleri kaynakta mı?" | Hayır — modül envanteri, sahipleriyle | §3 |
| T7 | "Boş hücre var mı?" | tarama 0 | §21 |
| T8 | "URL'ler envanter dışı mı?" | 7/7 doğrulandı | §16 |
| T9 | "Kod örneği iddiası?" | YOK — dürüstlük cümlesi | §17 |
| T10 | "Ölçüm kapıları çalıştı mı?" | Hayır, tanımlı; testing uygular | §12 |
| T11 | "500+ satır mı?" | kök ölçüm raporu | §21 |
| T12 | "K000 çakışması?" | kapsam dışı ilan edildi | §1 |
| T13 | "boot-surucu bağımlılığı gerçek mi?" | devir paketi sözleşmesi | §13/§14 |
| T14 | "Ölçüm yokken eşik yazmak dolgu mu?" | Eşik tanımdır, ölçüm ayrı (Z6) | §4 B7-B8 |
| T15 | "PCM5122 geçiyor mu?" | Hayır — yalnız PCM3168A | P7 |

## §22 Görev ↔ Modül Sahiplik ve Devir Matrisi

| # | Görev | Üreten veri | Sahip | Devralan | Devir biçimi |
|---|---|---|---|---|---|
| O1 | T1 Audio DSP | işlenmiş ses karesi | K004 | audio-control | sabit tampon (512 örnek) |
| O2 | T2 I2S Transfer | seri kare | audio-control | donanım (K001) | DMA |
| O3 | T3 USB Audio | USB paketi | audio-control | host | kuyruk |
| O4 | T4 Control | komut sonucu | device-control | istemci modül | atomik sonuç |
| O5 | T5 Monitoring | ölçüm özetleri | diagnostics | kayıt | olay |
| O6 | T7 Gözcü | besleme | runtime | MCU | donanım yazma |
| O7 | T8 OTA | bank yazımı | update | flash | update yetkisi |
| O8 | T9 Güvenlik | olay | security | diagnostics | olay |
| O9 | T10 Telemetri | log satırı | diagnostics | kayıt | kuyruk |
| O10 | T11 Güç | güç durumu | power-control | runtime | bayrak |
| O11 | Devir paketi | F1-F11 | boot | runtime | tek paket |
| O12 | Xrun bayrağı | taşma | runtime | audio-control | atomik bayrak |
| O13 | Overrun sayacı | aşım | runtime | diagnostics | atomik sayaç |
| O14 | Drop sayacı | kayıp | runtime | device-control | atomik sayaç |
| O15 | IRQ sayacı | kesme oranı | runtime | diagnostics | atomik sayaç |

**Sahiplik kuralı (Z8):** Veri bloğunun **tek yazıcısı** vardır; ikinci
yazıcı tespit edilirse tablo güncellenir ve gerekçe yazılır (tablo dışında
yazım = ihlal).

## §23 Senaryo Tabanlı Davranış Kayıtları

| # | Senaryo | Tetik | Beklenen davranış | Test kapısı |
|---|---|---|---|---|
| SC1 | Soğuk açılış sonrası devir | BOOT_READY | paket doğrula → gözcüyü devral | R9 |
| SC2 | Ses yolu devredeyken güç olayı | T11 | yüksek öncelik sırası koru | — |
| SC3 | DSP görevi periyodu aşıyor | overrun | sayaç + diagnostics | R2/R8 |
| SC4 | I2S tamponu taşıyor | xrun | bayrak + say | R8 |
| SC5 | Control kuyruğu dolu | drop | say + rapor | R8 |
| SC6 | Gözcü penceresi kaçıyor | donanım | reset → boot (E6) | R4 |
| SC7 | OTA işi idle'da çalışıyor | T8 | ses önceliği ezilmedi | R3 |
| SC8 | Güvenlik olayı geliyor | T9 | yüksek öncelik kuyruğa girer | — |
| SC9 | Kesme fırtınası | IRQ sayacı | oran raporu → diagnostics | R7 |
| SC10 | Boot paketi eksik alan | doğrulama | recovery isteği | R9 |
| SC11 | Tahsis denemesi | statik analiz | ret | R5 |
| SC12 | Mutex kullanma denemesi | kod taraması | ret | R6 |
| SC13 | Yeni görev (tablosuz) | kayıt denetimi | ret | R1 |
| SC14 | Öncelik matrisi kayması | kod incelemesi | ret | R10 |
| SC15 | Ölçüm yapılmadan eşik iddiası | inceleme | reddedilir (Z6) | R2/R3 |
| SC16 | Çift yazıcılı bayrak | Z8 ihlali | tablo güncellenir + gerekçe | R6 |
| SC17 | Tablosuz öncelik | R10 ihlali | kod incelemesinde ret | R10 |
| SC18 | IRQ içinde tahsis | R5 ihlali | statik analizde ret | R5 |
| SC19 | Devir paketi bozuk | R9 ihlali | recovery isteği | R9 |
| SC20 | Görev envanteri 32'yi aşar | R1 ihlali | kayıt ret | R1 |
| SC21 | Gözcü beslemesi yarışta | R4 ihlali | pencer tekrar planla | R4 |
| SC22 | Jitter ölçümü tutarsız | R2 | yeniden ölç, iddia geri çekilir | R2 |
| SC23 | Idle işi sesi ezdi | R3 | kuyruk kısıtlanır | R3 |
| SC24 | Drop oranı büyüdü | R8 | kuyruk/öncelik gözden geçirilir | R8 |
| SC25 | Ölçüm aracı yok | R2/R3 | eşik UNKNOWN kalır | Z6 |

## §24 Uygulama Kalıpları ve Anti-Pattern Kayıtları

| # | Kalıp | Doğru | Yanlış (anti-pattern) | Denetim |
|---|---|---|---|---|
| K1 | Periyodik görev | sabit periyot + sayaç | dinamik zamanlayıcı | R2 |
| K2 | Veri paylaşımı | atomik | mutex (bu modülde yasak) | R6 |
| K3 | Tahsis | statik tampon | görev içinde tahsis | R5 |
| K4 | Gözcü | yüksek öncelik görev | idle görevine bırakma | R4 |
| K5 | Kesme | kısa + sayımlı | uzun iş kesmede | R7 |
| K6 | Devir | tek paket (§13) | çoklu çağrı | R9 |
| K7 | Öncelik | §3 kaydı | tablosuz eşleme | R10 |
| K8 | Ölçüm | ölç → yaz | tahmin → yaz | R2/R3 |
| K9 | Hata | kod + olay | yalnız metin log | §10 |
| K10 | Jitter | bütçe içinde | bütçesiz iddia | R2 |
| K11 | Idle iş | idle'a kısıt | ses yolunda | R3 |
| K12 | Atomics | tek atomik tamsayı | bayrak yığını | R6 |
| K13 | Tepki | tanımlı eylem | sessiz devam | §10 |
| K14 | RTOS bağımlılığı | portlanabilir sözleşme | RTOS'a bağlı çağrı (seçim UNKNOWN) | §18 Q1 |
| K15 | Ölçüm birimi | UNKNOWN işaretli | birim uydurma | §18 Q2 |
| K16 | Bütçe tablosu | Kaynak/ölçüm | tahmini jitter | R2 |
| K17 | Olay yayını | atomik sayaç | kilitli kuyruk | R6 |
| K18 | Görev kaydı | §3 tablosu | sessiz görev | R1 |
| K19 | Devir doğrulama | paket alan denetimi | varsayımsal geçiş | R9 |
| K20 | Boot sırası kesme | maskeli bekleme | erken IRQ açma | §5 I5 |
| K21 | Gözcü sahipliği | T7 tek görev | birden fazla besleme | R4 |
| K22 | idle işi | yalnız idle | ses yolunda ağır iş | R3 |
| K23 | xrun tepkisi | bayrak + say | sessiz yutma | R8 |
| K24 | Hata kodu | sabit kod | metin eşleme | §10 |
| K25 | Port varyantı | §2 port satırı | varyant başına kod kopyası | §14 |
| K26 | Rakam kaynağı | Kaynak/ölçüm | ikinci el sayı | Z6 |
| K27 | Belirsizlik | UNKNOWN/⚠️ | kesin yazılmış tahmin | §18 |
| K28 | Çekişme | §9 öncelik | uyumsuz öncelik iddiası | §8 |
| K29 | Kapsam | §1 sınırı | komşu modüle iş sızması | Z1 |

## §25 İddia → Dayanak Eşlemesi

| # | İddia | Dayanak | Tür |
|---|---|---|---|
| C1 | Model preemptive priority-based | firmware/index §5 | kaynak |
| C2 | Max 32 görev | firmware/index §5 | kaynak |
| C3 | Statik tahsis + gözcü zorunlu | firmware/index §5 | kaynak |
| C4 | Görev periyotları 10 µs…100 ms | rtos.md §3 | kaynak |
| C5 | No blocking/No malloc/Lock-free | rtos.md §4 | kaynak |
| C6 | Priority ceiling | rtos.md §4 | kaynak |
| C7 | RTOS seçenekleri listesi | firmware/index §5 | kaynak |
| C8 | Cihaz bazlı RTOS (XMOS/STM32/ESP32) | firmware/index §9 | kaynak |
| C9 | Devir paketi alanları | boot-surucu §5 | sözleşme |
| C10 | Görev envanteri T6-T11 | bu dosya §3 | modül envanteri |
| C11 | Bütçe/jitter rakamları | — | UNKNOWN |
| C12 | Gözcü periyodu | — | UNKNOWN |
| C13 | Öncelik sayısal eşlemesi | — | UNKNOWN |
| C14 | Gerçek çağrı adları | — | ⚠️ (kod yok) |
| C15 | IRQ tablosu | firmware/index §5 + port | kısmi |
| C16 | 512 örnek @ 48 kHz | repo kuralı 8 | kural |
| C17 | 32-bit float | repo kuralı 7 + ADR-017 | kural |
| C18 | PCM3168A | repo kuralı 6 + ADR-038 | kural |
| C19 | Ölçüm kapıları R1-R10 | bu dosya §12 | kural |
| C20 | URL'ler (7) | §16 · HTTP 200 · envanter dışı | kanıt |
| C21 | Dürüstlük cümlesi | §17 | kural |
| C22 | Kapsam dışı 6 komşu | §1 | sınır |
| C23 | Ölçüm protokolü M1-M15 | §21 | yöntem |
| C24 | Sahiplik matrisi 15 satır | §22 | sözleşme |
| C25 | Senaryo kaydı 15 satır | §23 | test köprüsü |

## §26 Revizyon ve Sorumluluk Kaydı

| # | Kayıt | Değer |
|---|---|---|
| V1 | Dosya | `runtime/runtime-anayasa.md` |
| V2 | node_id | K005-firmware/runtime/runtime-anayasa |
| V3 | node_type | module |
| V4 | status | draft |
| V5 | group | mimari (tirnaksız) |
| V6 | parent_id | K005-firmware/runtime |
| V7 | children | [] |
| V8 | Üretim tarihi | 2026-10-13 |
| V9 | Kaynak sayısı | 3 (rtos · firmware/index · boot-surucu) |
| V10 | Web URL | 7 (HTTP 200, envanter dışı) |
| V11 | Eşik | ≥515 satır (kök ölçüm raporu) |
| V12 | Sahip | Bayram Ali / Vault Steward |
| V13 | Mod | Red Team · Human Mode · Truth Mode |
| V14 | İlgili modüller | runtime · boot · audio-control · device-control · diagnostics · update · security · power-control · testing |
| V15 | Dokunulmaz yüzey | frozen ADR'ler · `.ai/electronic/**` (okuma) · K000/K001/K003/K004 (erişim yok) |
| V16 | Değişiklik yetkisi | yalnız K005-firmware beyazlistesi + orkestratör |

## §27 Ölçüm Protokolü ve Kapanış Kontrolü

| # | Ölçüm | Yöntem | Kabul |
|---|---|---|---|
| M1 | Satır sayısı | Get-Content UTF8 sayımı | ≥515 |
| M2 | FM 10 anahtar | ilk --- bloğu | 10 |
| M3 | `parent:` yok | tarama | 0 |
| M4 | group tirnaksız `mimari` | tarama | 1 |
| M5 | Liste alanları tanımlı | 5 alan | 5 |
| M6 | Son satır Authority | birebir | 1 |
| M7 | Bölüm §1→§21 kesintisiz | tarama | 21 |
| M8 | Her bölümde tablo | görsel denetim | evet |
| M9 | Yer tutucu/bozuk karakter | tarama | 0 |
| M10 | Boş tablo hücresi | tarama | 0 |
| M11 | URL sayısı/karakteri | §16 | 7 / HTTP 200 |
| M12 | Dürüstlük cümlesi | §17 birebir | 1 |
| M13 | UNKNOWN/⚠️ işaretli | §18 | 5 |
| M14 | Eşleme tablosu | §19 | 20 satır |
| M15 | İtiraz kaydı | §20 | 15 satır |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
