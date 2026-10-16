---
node_id: K2-GUC-ORNEK
node_type: kod
status: active
parent_id: K2-GUC
children: []
depends_on: [K2-GUC, ADR-038, ADR-017, L0-10]
evidence: .ai/architecture/K002-electroncis/electroncis-ornekler.md#§7 · .ai/architecture/K002-electroncis/electroncis-ornekler.md#§15.1 · .ai/architecture/K002-electroncis/guc-yonetimi/guc-yonetimi-anayasa.md#§T-K000
validation: BBC-1 · BBC-2 · LINE-500
open_questions: Q-GYA1 · Q-O1
group: kod
---

# K2-GUC-ORNEK — Güç Yönetimi Kod Örnekleri ve OS/Dil Kapsamı

> **Kapsam:** Bu dosya güç yönetimi konusunda **çalışır durumda kod sağlamaz**. Repo'da
> elektronik kodu yoktur (L0 kaydı: `*.cpp/*.h` = 0). Buradaki her blok **pseudocode /
> arayüz iskeleti** olarak işaretlenir; her tablo kaynak § ve satır atıflı `⚠️ VERIFICATION
> REQUIRED` taşır. Uydurma register adresi, uydurma ölçülmüş sayı, uydurma URL yazılmaz.

## §1 Dürüstlük Kaydı (şablon §7)

| Madde | Kayıt | Kaynak |
|---|---|---|
| Repo'da elektronik kodu var mı | **YOK** — `*.cpp` / `*.h` = 0 | `.ai/electronic/` L0 kök turu (2026-10-14) |
| Bu dosyanın blokları çalışır mı | **HAYIR** — pseudocode / iskelet | `electroncis-ornekler.md` §7 |
| Ölçülmüş sayı içeriyor mu | **HAYIR** — yalnız ADR/L0 verilen kısıtlar | ADR-038 · ADR-017 · L0-10 |
| Register adresi içeriyor mu | **HAYIR** — yok; varsa `⚠️` | PCM3168A datasheet okunmadı |
| OS/dil verisi kaynağı | K000-isletim-sistemi vault + standart arayüz | §T-K000 taşıma deseni |
| Tahmini değer işareti | `⚠️ VERIFICATION REQUIRED` | `.ai/architecture/K002-electroncis/electroncis-dogrulama.md` §2 |

## §2 Bağlayıcı Kısıtlar (kod bloklarının üstü kural)

| Kısıt | Değer | ADR/L0 |
|---|---|---|
| DAC yongası | **PCM3168A tek DAC** — PCM5122 yasak | ADR-038 · H001 |
| Kaynak format | **32-bit float** | ADR-017 |
| DMA/çözünürlük | **512 örnek / 48 kHz** | L0 · ADR-017 |
| DC offset koruması | **>0.5 V → röle aç** | L0 kural 10 |
| SNR hedefi | **>100 dB** | proje kısıtı |
| THD+N hedefi | **<0.01 % @1 kHz** | proje kısıtı |
| Q-K2-1 sınırı | Konversiyon/matris anahtarı K001'de | `index.md` Q-K2-1 |

## §3 OS Kapsamı — Güç Yönetimi Arayüzleri (her işletim sistemi)

> **Kaynak not:** OS satırları K000-isletim-sistemi vault'undaki domainsel kapsamla
> hizalanır (`isletim-sistemi-turu/{windows,macos,ios,android}` · `os-guc-elektronigi` ·
> `donanim-arayuz-protokolleri`); teknik doğrulama **`⚠️ VERIFICATION REQUIRED`**
> olarak kalır — bu dosya ölçü **ÜRETMEZ**.

| # | OS | Güç yönetimi arayüzü (öneri) | K000 kapsama kanıtı | Doğrulama |
|---|---|---|---|---|
| 1 | Windows | ACPI/uyku olayları + `SetThreadPriority` ile render-ses kritik görev ayırma | `isletim-sistemi-turu/windows/` | `⚠️ VERIFICATION REQUIRED` |
| 2 | macOS | Power Manager / IOKit güç olayları; RealTime işlem sınıfı | `isletim-sistemi-turu/macos/` | `⚠️ VERIFICATION REQUIRED` |
| 3 | Linux | `sysfs`/`upower` olayları; `SCHED_FIFO` gerçek zamanlı kuyruk | `os-guc-elektronigi/` (K000 domainsel) | `⚠️ VERIFICATION REQUIRED` |
| 4 | iOS | UIApplication güç olayları; ses oturumu kesintisi | `isletim-sistemi-turu/ios/` | `⚠️ VERIFICATION REQUIRED` |
| 5 | Android | PowerManager/Doze; ses oturumu önceliği | `isletim-sistemi-turu/android/` | `⚠️ VERIFICATION REQUIRED` |
| 6 | Gömülü/RTOS | DMA tampon = 512 örnek, Sleep yok, lock-free | L0 · ADR-017 · `os-guc-elektronigi/` | `⚠️ VERIFICATION REQUIRED` |

**Zorunlu kural (tüm OS'lerde ortak):** röle açma kararı işletim sisteminden **bağımsız**,
DSP çekirdeğinde ve donanım eşiğinde (`>0.5 V`) uygulanır — L0 kural 10.

## §4 Dil Kapsamı — Güç/Oturum Kod Önerileri (her programlama dili)

| # | Dil | Öneri | Uygun katman | Doğrulama |
|---|---|---|---|---|
| 1 | C | Sabit boy DMA tamponu; global durum; `volatile` kesme bayrağı | DSP çekirdek | `⚠️ VERIFICATION REQUIRED` |
| 2 | C++ | RAII ses oturumu; `std::atomic` durum bayrağı; kilit yok | çekirdek + taşıyıcı | `⚠️ VERIFICATION REQUIRED` |
| 3 | Rust | `Send + Sync` ses iş parçacığı; `unsafe` yalnız DMA sınırı | çekirdek | `⚠️ VERIFICATION REQUIRED` |
| 4 | Python | Güç telemetrisi/diagnostik scripti; gerçek zamanlı yol **değil** | test/imalat | `⚠️ VERIFICATION REQUIRED` |
| 5 | C#/.NET | WinForms/CLI telemetri ve fabrika test istemcisi | test/imalat | `⚠️ VERIFICATION REQUIRED` |
| 6 | PHP 8.x | Sunucu tarafında güç/kalite raporu saklama (PDO) | uygulama | `⚠️ VERIFICATION REQUIRED` |
| 7 | JS/TS | Web arayüzünde canlı güç göstergesi (WebSocket) | uygulama | `⚠️ VERIFICATION REQUIRED` |
| 8 | Assembly | Yalnız boot/ISR kritik yol; geri kalan C | DSP çekirdek | `⚠️ VERIFICATION REQUIRED` |

**Dil-OS matrisi (örnek yoğunluk önerisi — tahmini):**

| Dil | Windows | macOS | Linux | iOS | Android | Gömülü |
|---|---|---|---|---|---|---|
| C | 1 | 1 | 1 | 0 | 0 | **3** |
| C++ | **3** | **3** | **3** | 2 | 2 | **3** |
| Rust | 2 | 2 | 2 | 1 | 1 | 2 |
| Python | 3 | 3 | 3 | 0 | 1 | 1 |
| C# | **3** | 1 | 1 | 0 | 0 | 0 |
| PHP 8.x | 2 | 2 | 2 | 1 | 1 | 0 |
| JS/TS | 3 | 3 | 3 | 2 | 2 | 0 |
| Assembly | 0 | 0 | 0 | 0 | 0 | 2 |

> Şiddet tablosu: 0 = önerilmez · 1 = sınırlı · 2 = uygun · 3 = tercih edilen.
> Bu matris **mühendislik önerisidir, ölçüm değildir** — `⚠️ VERIFICATION REQUIRED`.

## §5 Örnek A — Röle Eşiği Pseudocode (L0 kural 10)

```text
// PSEUDOCODE — çalışır değildir; 512 örnek/48 kHz blok üzerinde
ALGILAMA(dc_offset) := |ortalama(son blok)|      // blok = 512 örnek
EIK(ters): dc_offset > 0.5 V                     // sabit: L0 kural 10
eğer EIK ise
    RÖLE_ac()                                    // donanım güvenlik yolü
    DURUM := "koruma"                            // uygulama/OS'tan bağımsız
değilse
    RÖLE_kapa()                                  // `⚠️ VERIFICATION REQUIRED`
```

| Satır aşaması | Kontrol | Kural kaynağı |
|---|---|---|
| Blok ölçümü | 512 örnek tek DMA bloğu | ADR-017 · L0 |
| Eşik | 0.5 V sabit — **değiştirilemez** | L0 kural 10 |
| Eylem | röle = fiziksel kesme | L0 kural 10 |
| Kapanış | PowerShell gate (BBC-1/BBC-2) doğrulama | `electroncis-dogrulama.md` §5 |

## §6 Örnek B — Güç Olayı Durum Makinesi (OS bağımsız)

| Durum | Tetik | Çıkış | OS bağımlı mı |
|---|---|---|---|
| STANDBY | açılış / uyku | röle kapalı, DMA durdu | Hayır |
| RUN | güç olayı = aktif | DMA 512/48k başlat | Olay kaynağı evet, davranış hayır |
| PROTECT | `dc > 0.5 V` | röle aç, oturumu kapat | Hayır |
| FAULT | SNR/THD kapısı düşerse | röle aç, rapor yaz | Hayır |
| IDLE | kullanıcıcı gecikme | oturum serbest bırak | **Evet** (OS API) |

> OS bağımlı tek satır = `IDLE` → oturum bırakma çağrısı; L0 yasağı `Sleep(1)` ve
> lock-free ihlali bu çağrıda **geçersiz** kılınmaz (`Sleep yok` L0).

## §7 Örnek C — DAC Register İskeleti (PCM3168A)

| Adım | İşlem | Doğrulama |
|---|---|---|
| 1 | DAC = **tek PCM3168A** (PCM5122 yok) | ADR-038 · H001 |
| 2 | Format = **32-bit float** | ADR-017 |
| 3 | Örnek = **512 / 48 kHz** | ADR-017 · L0 |
| 4 | Register adresleri | **`⚠️ VERIFICATION REQUIRED` — datasheet okunmadı** |
| 5 | Reset/miks sırası | `⚠️ VERIFICATION REQUIRED` |
| 6 | GAIN/HP amp ayarı | `⚠️ VERIFICATION REQUIRED` |

**Dürüstlük:** Register değeri bu dosyada **yoktur ve uydurulmayacaktır.**

## §8 Örnek D — Ölçüm/Telemetri Çıktı Sözleşmesi

```json
{
  "record_type": "power_telemetry",
  "unit": "V",
  "dc_offset": "UNKNOWN",
  "relay_state": "UNKNOWN",
  "sample_block": 512,
  "sample_rate_hz": 48000,
  "snr_target_db": 100,
  "thd_n_target_percent": 0.01,
  "measured": false
}
```

> `dc_offset` / `relay_state` **bilinmeyen** — ölçüm yapılmadı (`measured: false`).
> Uydurma ölçüm değeri yazılmaz (Zero-Hallucination §3).

## §9 OS/Dil Örnek Eşleme Matrisi

| Senaryo | Tercih edilen OS | Tercih edilen dil | Alternatif | Atıf |
|---|---|---|---|---|
| DSP çekirdek + DMA | Gömülü/RTOS | C/C++ | Rust | L0 · `os-guc-elektronigi/` |
| Güç olayı yakalama | Windows | C# | C++ | `windows/` |
| Güç olayı yakalama | macOS | C++/Obj-C | Swift | `macos/` |
| Güç olayı yakalama | Linux | C | Python (diagnostik) | `os-guc-elektronigi/` |
| Fabrika telemetri | Windows/Python | Python 3 | C# CLI | `imalat-ve-test/` (K2) |
| Sunucu raporu | Linux/PHP 8.x | PHP 8.x | Node.js | `isletim-sistemi-turu/` (indirekt) |
| Mobil izleme | Android | Kotlin/Java | — | `android/` |
| Mobil izleme | iOS | Swift | — | `ios/` |

## §10 Yasaklar ve Kaçınılan Örnekler

| # | Yasak | Neden |
|---|---|---|
| 1 | PCM5122 register iskeleti | ADR-038 yasağı |
| 2 | `Sleep(1)` içeren güç döngüsü | L0 yasağı |
| 3 | Non-lock-free tampon döngüsü | L0 yasağı |
| 4 | 16/24-bit kaynak iskeleti | ADR-017 ihlali |
| 5 | Eşik yerine UI ayarı | L0 kural 10 |
| 6 | Ölçülmüş `snr_meas_db` değeri | ölçüm yok → `UNKNOWN` |
| 7 | Uydurma URL/register | Zero-Hallucination §3 |

## §11 OS Bazlı Ayrıntı — Windows

| # | Bileşen | Öneri | Doğrulama |
|---|---|---|---|
| 1 | Güç olayı | ACPI/seviye olayları; kuyruğa alıp durum makinesine ilet | `⚠️` |
| 2 | Öncelik | Ses kritik iş parçacığı yüksek öncelik; güç işi normal | `⚠️` |
| 3 | Telemetri | C# CLI/WinForms istemcisi; JSON satır kaydı | `⚠️` |
| 4 | Kapı | Röle kararı WinAPI'den **bağımsız** çekirdekte | L0-10 |
| 5 | Yasak | `Sleep` ile bekleyen güç döngüsü | L0 |
| 6 | Uyku/uyanma | Uyanışta DMA bloğu yeniden kur (512/48k) | ADR-017 · `⚠️` |

## §12 OS Bazlı Ayrıntı — macOS

| # | Bileşen | Öneri | Doğrulama |
|---|---|---|---|
| 1 | Güç olayı | Power Manager olaylarını dinle; durum makinesine ilet | `⚠️` |
| 2 | Öncelik | Gerçek zamanlı ses iş parçacığı sınıfı | `⚠️` |
| 3 | Telemetri | CLI aracı; JSON satır kaydı | `⚠️` |
| 4 | Kapı | Röle kararı çekirdekte | L0-10 |
| 5 | Uyku | Uyanışta ses oturumu + DMA yeniden kurma | `⚠️` |
| 6 | Dil | C/C++ çekirdek; Swift yalnız taşıyıcı | `⚠️` |

## §13 OS Bazlı Ayrıntı — Linux

| # | Bileşen | Öneri | Doğrulama |
|---|---|---|---|
| 1 | Güç olayı | sysfs/upower olayları; systemd uyanma tetikleyici | `⚠️` |
| 2 | Öncelik | `SCHED_FIFO` gerçek zamanlı kuyruk | `⚠️` |
| 3 | Telemetri | Python/CLI; systemd servis kaydı | `⚠️` |
| 4 | Kapı | Çekirdek; udev kuralı yalnız röle cihazını bağlar | `⚠️` |
| 5 | Sunucu | PHP 8.x rapor katmanı (PDO, parametreli) | `php-backend-standards` |
| 6 | Yasak | Kullanıcı uzayında eşik kararı | L0-10 ihlali |

## §14 OS Bazlı Ayrıntı — iOS / Android

| # | Bileşen | iOS önerisi | Android önerisi | Doğrulama |
|---|---|---|---|---|
| 1 | Güç olayı | UIApplication seviye olayları | PowerManager/Doze geçişleri | `⚠️` |
| 2 | Ses oturumu | Kesinti olayında DMA bloğu korunur | Odak/kayıp olayı aynı | `⚠️` |
| 3 | Telemetri | CLI çıktısı alınır (mobil UI **değil**) | CLI/logcat çıktısı | `⚠️` |
| 4 | Kapı | **Cihazda uygulanmaz** — kapı donanım/DSP'de | aynı | L0-10 |
| 5 | Dil | Swift yalnız izleme katmanı | Kotlin yalnız izleme katmanı | `⚠️` |
| 6 | Kapsam | Gömülü hedefte **birincil** değildir | aynı | `electroncis-kapsam.md` §B.4/B.5 |

## §15 OS Bazlı Ayrıntı — Gömülü / RTOS (birincil hedef)

| # | Bileşen | Öneri | Kural |
|---|---|---|---|
| 1 | DMA tamponu | Baştan allocate; 512 örnek; çalışma anında yok | L0 |
| 2 | Döngü | Lock-free; kesme yolu yalnız bayrak | L0 |
| 3 | Bekleme | `Sleep` **yok**; bayrak/yordam bekleme | L0 |
| 4 | Kapı | Röle ≥0.5 V → çekirdek içinde açılır | L0-10 |
| 5 | Kaynak | 32-bit float yolu (ADR-017); 16/24 yasak | ADR-017 |
| 6 | DAC | Tek PCM3168A; PCM5122 yok | ADR-038 |
| 7 | Kapı ölçümü | SNR>100 dB · THD+N<0.01% @1 kHz | proje kısıtı |

## §16 Dil Bazlı Ayrıntı — C / C++ / Assembly

| # | Dil | Örnek blok türü | Kaçınılan | Doğrulama |
|---|---|---|---|---|
| 1 | C | DMA tamponu, durum makinesi, bayrak | dinamik tahsis sıcak yolda | `⚠️` |
| 2 | C++ | RAII oturumu, `stdatomic` bayrak | kilitli sıcak yol | `⚠️` |
| 3 | Assembly | Yalnız ISR/boot kritik yol | genel iş mantığı | `⚠️` |
| 4 | C/C++ | Ölçüm `float` köprüsü | `double` sıcak yolda varsayımı | `⚠️` |
| 5 | C | Kaynak = 32-bit float | 16/24-bit karışımı | ADR-017 |
| 6 | C++ | DAC = tek PCM3168A sarmalayıcı | ikinci DAC sarmalayıcı | ADR-038 |

## §17 Dil Bazlı Ayrıntı — Python / C# / PHP / JS

| # | Dil | Örnek blok türü | Katman | Doğrulama |
|---|---|---|---|---|
| 1 | Python | Fabrika telemetrisi; seri port okuma; CSV rapor | test/imalat | `⚠️` |
| 2 | Python | Eşik **kontrolü** (`>0.5`) — uygulayıcı **değil** | test | L0-10 |
| 3 | C#/.NET | CLI/WinForms telemetri, JSONL yazma | test/imalat | `⚠️` |
| 4 | C#/.NET | Ölçüm yok → `measured:false` | test | §8 |
| 5 | PHP 8.x | Rapor saklama; PDO parametreli sorgu | uygulama | `php-backend-standards` |
| 6 | JS/TS | Canlı gösterge; WebSocket | uygulama | `⚠️` |
| 7 | JS/TS | Karar vermeyi sunucuya taşıma **yasak** | uygulama | L0-10 |
| 8 | Python | `Sleep` ile senkron üretme | test | L0 |

## §18 OS×Dil Uygunluk Ana Matrisi (tümü öneridir)

| OS ↓ / Dil → | C | C++ | Rust | Python | C# | PHP | JS/TS | Asm |
|---|---|---|---|---|---|---|---|---|
| Windows | 2 | 3 | 2 | 3 | **3** | 2 | 3 | 0 |
| macOS | 2 | **3** | 2 | 3 | 1 | 2 | 3 | 0 |
| Linux | **3** | **3** | 2 | **3** | 1 | **3** | 3 | 0 |
| iOS | 1 | 2 | 1 | 0 | 0 | 1 | 2 | 0 |
| Android | 1 | 2 | 1 | 1 | 0 | 1 | 2 | 0 |
| Gömülü/RTOS | **3** | **3** | 2 | 1 | 0 | 0 | 0 | 2 |

> 0 = önerilmez · 1 = sınırlı · 2 = uygun · 3 = tercih edilen ·
> **`⚠️ VERIFICATION REQUIRED` — ölçüm değildir.**

## §19 OS/Dil Örnek Girdi Tabloları (veri yüzeyi)

| Girdi | Kaynak | Biçim | Doğrulama |
|---|---|---|---|
| DC offset örneği | DSP bloğu (512/48k) | `float` volt | `⚠️` ölçülmedi |
| Röle durumu | Çekirdek durumu | `enum {RUN,PROTECT,FAULT}` | L0-10 |
| Güç olayı | OS olay katmanı | `enum {RUN,IDLE,SLEEP}` | `⚠️` |
| SNR/THD raporu | Test istasyonu | `dB` / `%` | ölçü → sonra yaz |
| DAC register | datasheet | `(addr,value)` | `⚠️` datasheet yok |
| Sıcaklık (ops.) | sensör | `°C` | `⚠️` kapsam dışı |

## §19.1 OS İskelet Blokları (pseudocode — çalışır değildir)

### Windows

```text
// PSEUDOCODE — Windows güç olayı akışı
OLAY := güç_olayi_dinle()                 // ACPI/seviye olayı — `⚠️`
eğer OLAY = "uyan" ise
    DMA_kur(512, 48000)                   // ADR-017
    RÖLE_degerlendir(dc)                  // L0-10 — çekirdek
```

### macOS

```text
// PSEUDOCODE — macOS güç olayı akışı
OLAY := guc_olay_dinle()                  // Power Manager — `⚠️`
eğer OLAY = "uyan" ise
    oturum_kur()                          // gerçek zamanlı sınıf — `⚠️`
    DMA_kur(512, 48000)                   // ADR-017
```

### Linux

```text
// PSEUDOCODE — Linux güç olayı akışı
OLAY := sysfs_olay_dinle()                // upower/sysfs — `⚠️`
görev := SCHED_FIFO(öncelik)              // `⚠️`
eğer OLAY = "uyan" ise
    DMA_kur(512, 48000)                   // ADR-017
```

### iOS / Android

```text
// PSEUDOCODE — mobil izleme (birincil hedef DEĞİL)
SES_OTURUMU := kesinti_olayi()            // `⚠️`
eğer kesinti ise
    DMA_korunur()                         // donanım/DSP tarafı
    rapor_uret()                          // CLI/logcat — `⚠️`
```

### Gömülü / RTOS

```text
// PSEUDOCODE — gömülü ana yol (L0)
döngü:
    blok := DMA_bekle(512)                // Sleep YOK — L0
    dc := ortalama(blok)                  // 32-bit float — ADR-017
    eğer dc > 0.5 V ise RÖLE_ac()         // L0-10
    değilse           RÖLE_kapa()
    bayrak_yayinla()                      // lock-free — L0
```

## §19.2 Dil İskelet Blokları (pseudocode — çalışır değildir)

### C

```c
/* PSEUDOCODE — C DMA tamponu */
/* 512 örnek / 48 kHz — ADR-017 */
static float dma_buf[512];   /* baştan allocate — L0 */
static volatile int done;    /* kesme bayrağı */
/* Sleep yok, lock-free — L0 */
```

### C++

```cpp
// PSEUDOCODE — C++ RAII oturumu
// oturum ac -> DMA kur; oturum kapan -> DMA durdur
// std::atomic<bool> done;  // kilit yok — L0
// DAC = tek PCM3168A       // ADR-038
```

### Rust

```rust
// PSEUDOCODE — Rust ses iş parçacığı
// let done = AtomicBool::new(false);   // Send + Sync
// unsafe { /* yalnız DMA sınırı */ }   // `⚠️`
// 512 / 48000 — ADR-017
```

### Python

```python
# PSEUDOCODE — fabrika telemetri (test katmanı)
# esik = 0.5            # L0 kural 10 — kontrol, uygulayıcı DEGIL
# if dc_offset > esik: report("PROTECT")
# measured = False      # olcum yapilmadi -> UNKNOWN
```

### C#/.NET

```csharp
// PSEUDOCODE — telemetri istemcisi
// record PowerTelemetry { double DcOffset; bool Measured = false; }
// JSONL yaz; olcum yoksa "UNKNOWN" — Zero-Hallucination §3
```

### PHP 8.x

```php
<?php
// PSEUDOCODE — rapor saklama (PDO, parametreli)
// $st = $pdo->prepare('INSERT INTO power_report (dc_offset) VALUES (:dc)');
// Karar vermeyi sunucuya TAŞIMA — L0-10
```

### JS/TS

```javascript
// PSEUDOCODE — canlı gösterge (WebSocket)
// ws.onmessage = (m) => renderGuc(JSON.parse(m.data));
// esik karari SUNUCU/CEKİRDEKTE — L0-10
```

### Assembly

```asm
; PSEUDOCODE — yalniz ISR/boot kritik yolu
; bayrak yaz -> ana dongu C'de devralir
; DMA 512 ornek — ADR-017
```

## §19.3 OS/Dil Karar Ağacı

| Soru | Evet | Hayır |
|---|---|---|
| Hedef gömülü donanım mı | C/C++ çekirdek → §15 | OS katmanına geç |
| Telemetri gerekli mi | Python/C# CLI → §17 | doğrudan rapor yok |
| Web gösterge gerekli mi | JS/TS okuma → §17 | atla |
| Sunucu raporu var mı | PHP 8.x PDO → §17 | atla |
| Röle kararı nerede | **her zaman çekirdek** — L0-10 | — |
| Kaynak formatı | **32-bit float** — ADR-017 | 16/24 = yasak |
| DAC | **tek PCM3168A** — ADR-038 | PCM5122 = yasak |

## §19.4 Ölçüm/Biçim Sözlüğü

| Terim | Birim | Kaynak | Durum |
|---|---|---|---|
| DC offset | V | DSP bloğu | `UNKNOWN` (ölçülmedi) |
| Röle | enum | çekirdek | RUN/PROTECT/FAULT |
| Örnek bloğu | 512 | ADR-017 | sabit |
| Örnekleme | 48 kHz | ADR-017 | sabit |
| SNR | dB | test | hedef >100 — ölçüm yok |
| THD+N | % | test | hedef <0.01 @1kHz — ölçüm yok |
| Eşik | 0.5 V | L0-10 | değiştirilemez |

## §20 Kapı ve Doğrulama

| Kapı | Aşama | Durum |
|---|---|---|
| Mockup | Kod görseli yok | N/A |
| BBC-1/BBC-2 | PowerShell kök ölçüm | `electroncis-dogrulama.md` §5 |
| LINE-500 | Bu dosya ≥500 satır | ölçüm: §23 |
| Commit | subagent ATMAZ | orkestratöre |

## §21 Açık Sorular (uydurulmaz)

| ID | Soru | Etki |
|---|---|---|
| Q-O1 | Elektronik kod örneği hangi repo'da doğulanacak | gerçek kod bloğu |
| Q-GYA1 | 8.1 güç bütçesi nerede | eşiğin fiziksel gerekçesi |
| Q-K2-1 | Sınır kararı (konversiyon/matris) K001'de | bu dosyanın kesişimi |
| Q-D1 | 47 dosya derinleştirme | OS/dil bloklarının kapsamı |

## §22 Sürüm ve Değişiklik Kaydı

| Sürüm | Tarih | Değişiklik | Kaynak |
|---|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu | index.md §children |

## §22.1 OS Yüzey Özeti (her işletim sistemi)

| OS | Güç olayı | RT ses | Telemetri | Röle kapısı | Birincil mi |
|---|---|---|---|---|---|
| Windows | var `⚠️` | öncelik `⚠️` | C#/Python | çekirdek | Hayır (test) |
| macOS | var `⚠️` | RT sınıfı `⚠️` | CLI | çekirdek | Hayır (test) |
| Linux | var `⚠️` | SCHED_FIFO `⚠️` | Python/PHP | çekirdek | Hayır (test) |
| iOS | var `⚠️` | oturum kesintisi `⚠️` | CLI | donanım | Hayır |
| Android | var `⚠️` | odak kaybı `⚠️` | logcat | donanım | Hayır |
| Gömülü/RTOS | yok (döngü) | ana yol | yok/seri | **çekirdek** | **EVET** |

## §22.2 Dil Yüzey Özeti (her programlama dili)

| Dil | Katman | Sıcak yol uygun mu | Röle kararı | Not |
|---|---|---|---|---|
| C | çekirdek | EVET | verir | DMA/bayrak |
| C++ | çekirdek | EVET | verir | RAII/atomic |
| Rust | çekirdek | EVET | verir | unsafe sınırı `⚠️` |
| Python | test | HAYIR | yalnız okur | fabrika |
| C#/.NET | test | HAYIR | yalnız okur | telemetri |
| PHP 8.x | uygulama | HAYIR | yasak | PDO rapor |
| JS/TS | uygulama | HAYIR | yasak | gösterge |
| Assembly | çekirdek | EVET | verir | ISR/boot |

## §22.3 Uygunluk Sınırı Notları

| # | Not | Kural |
|---|---|---|
| 1 | Tüm OS satırları `⚠️` işaretli | ölçülmüş değer yok |
| 2 | Dil-OS matrisleri öneridir | ölçüm değildir |
| 3 | Röle kararı tek yerde | L0-10 |
| 4 | 16/24-bit karışımı yasak | ADR-017 |
| 5 | İkinci DAC yasak | ADR-038 |

## §22.4 Sonraki Adım

| Adım | Sahip | Durum |
|---|---|---|
| Q-O1 kod örneği | orkestratör | açık |
| Gerçek ölçüm | test istasyonu | yapılmadı |
| Commit | orkestratör | ATILMADI |

## §23 Ölçüm Tablosu (LINE-500)

| Dosya | Hedef satır | Durum |
|---|---|---|
| `guc-yonetimi/guc-yonetimi-ornekler.md` | ≥500 | ölçülür (yazım sonrası) |

---

**Authority:** `electroncis-ornekler.md` §7 · `.ai/architecture/K002-electroncis/electroncis-dogrulama.md` §5 · `.ai/ARCHITECTURE.md` · ADR-038 · ADR-017
