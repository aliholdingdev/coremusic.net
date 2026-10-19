---
node_id: K002-electroncis/sensor-ve-akteyici/sensor-ve-akteyici-ornekler
node_type: module
status: draft
parent_id: K002-electroncis/sensor-ve-akteyici
children: []
depends_on: [sensor-ve-akteyici-anayasa, ADR-017, ADR-038, L0-10]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-ornekler.md §7 (dürüstlük kaydı) + §15.1 (iskelet)"
  - ".ai/architecture/K002-electroncis/sensor-ve-akteyici/sensor-ve-akteyici-anayasa.md §2-§11 (kurallar)"
validation: "draft — 2026-10-14 yazıldı · ≥500 ölçümü yazım sonrası · tüm bloklar PSEUDOCODE"
open_questions:
  - "Q-O1: Elektronik kod örneği hangi repo'da doğulanacak"
  - "Q-SN3: Sensör vault kanıtı satır bazlı yok"
group: kod
---

# sensor-ve-akteyici — Kod Örnekleri ve OS/Dil Kapsamı (Kademe-4)

> **Kapsam:** Çalışır durumda kod **SAĞLANMAZ** (repo'da elektronik kodu YOK — L0 kaydı:
> `*.cpp/*.h` = 0). Her blok **pseudocode / arayüz iskeletidir**; register adresi,
> ölçülmüş değer ve URL **uydurulmaz**. Kapsam sınırları: füzyon K004, sürücü kodu
> K005, kalibrasyon UI K0xx (`⚠️`).

## §0 ELI15

Bu dosya, sensör/aktüatör alt-konusunun **örnek yüzeyidir**: şunt hesabı iskeleti,
ADC okuma akışı, kalibrasyon tablosu formatı, sürücü pin yazma ve hot-plug okuma —
hepsi pseudocode. Her örnek bir kurala bağlidır (anayasa §2-§8) ve her OS/dil
satırı `⚠️ VERIFICATION REQUIRED` taşır: bunlar **mühendislik önerisidir, ölçüm değildir**.

## §1 Dürüstlük Kaydı (şablon §7)

| Madde | Kayıt | Kaynak |
|---|---|---|
| Repo'da elektronik kodu | **YOK** (`*.cpp`/`*.h` = 0) | L0 kök turu |
| Bloklar çalışır mı | **HAYIR** — pseudocode | `electroncis-ornekler.md` §7 |
| Ölçülmüş sayı | **YOK** — yalnız ADR/L0 kısıtları | ADR-017 · ADR-038 · L0 |
| Register adresi | **YOK** — datasheet okunmadı | `⚠️` |
| Satır kanıtı | `device-integration.md:27-34` okunmadı | §H.4 `⚠️` |
| Commit | ATILMADI | kural |

## §2 Bağlayıcı Kısıtlar

| # | Kısıt | Değer | Kaynak |
|---|---|---|---|
| 1 | Veri formatı | 32-bit float | ADR-017 |
| 2 | DMA bloğu | 512 örnek / 48 kHz | ADR-017 · L0 |
| 3 | DC koruma | >0.5 V → röle (ölçüm kaynağı burada) | L0-10 |
| 4 | DAC | tek PCM3168A (bu dosya DAC yazmaz) | ADR-038 |
| 5 | Sürücü kodu | K005-firmware (yazılmaz) | §B.4(4) |
| 6 | Füzyon | K004-dsp (yazılmaz) | §B.4(1)-(2) |

## §3 Örnek A — Shunt Ölçüm Hesabı İskeleti (pseudocode)

```text
// PSEUDOCODE — şunt gerilimi -> akım (hesap iskeleti, değer YOK)
GIRIS := adc_oku()                  // front-end çıkışı
AKIM  := GIRIS / SHUNT_DIRENCI      // SHUNT_DIRENCI: datasheet/ölçüm -> `⚠️`
GUC   := AKIM^2 * SHUNT_DIRENCI     // P = I^2 R — sınırla
eğer GUC > SINIR ise UYARI()        // SINIR: hesapla, uydurma -> `⚠️`
```

| # | Satır | Kural | Not |
|---|---|---|---|
| 1 | `adc_oku()` | anayasa §3 | register yok |
| 2 | `SHUNT_DIRENCI` | anayasa §2(1) | `⚠️` bilinmiyor |
| 3 | `SINIR` | anayasa §2(1) | `⚠️` hesaplanacak |
| 4 | Çıktı birimi | float (ADR-017) | 32-bit |

## §4 Örnek B — ADC Okuma Akışı (pseudocode)

```text
// PSEUDOCODE — SPI ile blok okuma (512/48k)
DMA_baslat(512)                     // L0: tampon bashtan allocate
BLOK := dma_bekle()                 // Sleep YOK — L0
her i icin 0..511:
    HAM[i]   := BLOK[i]
    DZELTMIŞ[i] := tablo_uygula(HAM[i])   // §5
yayinla(DZELTMIŞ)                   // 32-bit float — ADR-017
```

| # | Aşama | Kural | Kaçınılan |
|---|---|---|---|
| 1 | DMA 512 | ADR-017 | dinamik tahsis |
| 2 | `dma_bekle` | L0 `Sleep` yasağı | `Sleep(1)` |
| 3 | düzeltme | §5 tablosu | uydurma offset |
| 4 | yayın | float yolu | 16/24-bit |

## §5 Örnek C — Kalibrasyon Tablosu Formatı (JSON)

```json
{
  "record_type": "sensor_calibration_table",
  "channels": ["UNKNOWN"],
  "entries": [
    { "raw": "UNKNOWN", "corrected": "UNKNOWN", "unit": "V" }
  ],
  "offset": "UNKNOWN",
  "gain": "UNKNOWN",
  "measured": false,
  "version": "UNKNOWN",
  "date": "2026-10-14"
}
```

> Tablo **boş/UNKNOWN** doldurulur — ölçüm yok. `measured: false` iken değer sayıya
> çevrilmez (Zero-Hallucination §3).

## §6 Örnek D — Sürücü Pin Yazma İskeleti (K005 sınırı)

```text
// PSEUDOCODE — GPIO pin (register YOK, dijital alt-konu siniri)
MOD_PIN := DUSUK                   // uykuya alma isteği
AKIM_SINIRI := DEVRE_AYARI         // donanım ayarı -> `⚠️`
// mikro-kod YAZMA: K005-firmware (§B.4(4))
```

| # | Satır | Sorumlu | Not |
|---|---|---|---|
| 1 | `MOD_PIN` GPIO | `dijital` (§N-11) | register orada |
| 2 | `AKIM_SINIRI` | devre düzeyi (anayasa §6) | `⚠️` |
| 3 | mikro-kod | **K005** | yazılmaz |

## §7 Örnek E — Hot-Plug Okuma (devre düzeyi)

```text
// PSEUDOCODE — hot-plug pin (device-integration.md:27-34 okunmadı `⚠️`)
SEKME_SAYACI := 0
PIN := gpio_oku(HOTPLUG_PIN)       // RC suzulmus
eğer PIN != ONCEKI ise SEKME_SAYACI := SEKME_SAYACI + 1
eğer SEKME_SAYACI >= ESIK ise      // ESIK: kararli sayi -> `⚠️`
    OLAY_URET()                    // OS mantigi K000 (§B.4(6))
```

## §8 OS Kapsamı — her işletim sistemi (öneri · `⚠️`)

| # | OS | Sensör/ölçüm arayüzü | Sürücü/hot-plug | K000 atıfı |
|---|---|---|---|---|
| 1 | Windows | Seri/USB test istemcisi | GPIO masaüstü sınırlı `⚠️` | `windows/` |
| 2 | macOS | Seri/USB + donanım olayı `⚠️` | olay dinleme | `macos/` |
| 3 | Linux | `iio`/`i2c-dev`/`sysfs` `⚠️` | udev + GPIO `⚠️` | `os-guc-elektronigi/` |
| 4 | iOS | Sensör izleme (yalnız okuma) | hot-plug **YOK** | `ios/` |
| 5 | Android | SensorManager izleme | USB OTG olayı | `android/` |
| 6 | Gömülü/RTOS | SPI/I²C + DMA 512/48k | GPIO/PWM doğrudan | `donanim-arayuz-protokolleri/` |

## §9 Dil Kapsamı — her programlama dili (öneri · `⚠️`)

| # | Dil | Sensör bloğu | Sürücü bloğu | Kaçınılan |
|---|---|---|---|---|
| 1 | C | SPI/I²C okuma + tampon | GPIO yazma | dinamik tahsis |
| 2 | C++ | `Sensor` sınıfı (RAII) | `Driver` sınıfı | kilitli yol |
| 3 | Rust | sürücü trait'i | register trait'i | aşırı `unsafe` |
| 4 | Python | ADC okuma scripti | **YOK** (test) | gerçek zamanlı yol |
| 5 | C#/.NET | kalibrasyon istemcisi | **YOK** (test) | gerçek zamanlı yol |
| 6 | PHP 8.x | kalibrasyon kaydı (PDO) | **YOK** | karar verme |
| 7 | JS/TS | gösterge (okuma) | **YOK** | karar verme |
| 8 | Assembly | örnek ISR | PWM ISR | genel iş mantığı |

## §10 OS Bazlı İskelet Blokları

### Windows

```text
// PSEUDOCODE — test istemcisi
VERI := seri_oku(port)             // ADC telemetrisi
kaydet_jsonl(VERI)                 // measured=false
```

### macOS

```text
// PSEUDOCODE — test istemcisi (ayni akis, fark olay katmani)
OLAY := donanim_olayi_dinle()       // `⚠️`
eğer OLAY = "bagla" ise telemetri_baslat()
```

### Linux

```text
// PSEUDOCODE — sysfs/iio okuma
HAM := sysfs_oku(sensor_yolu)       // `⚠️` yol dogrulanmadi
kaydet(HAM, "measured=false")
```

### iOS

```text
// PSEUDOCODE — izleme (kontrol YOK)
DEGER := sensör_oku()               // yalniz izleme
// surucu/hot-plug: kapsam disi
```

### Android

```text
// PSEUDOCODE — izleme (kontrol YOK)
DEGER := sensor_manager_oku()       // `⚠️`
// surucu: kapsam disi
```

### Gömülü / RTOS

```text
// PSEUDOCODE — ana yol (L0 baglari tam)
döngü:
    BLOK := dma_bekle(512)          // Sleep YOK
    HAM  := spi_oku(BLOK)
    DZ   := tablo_uygula(HAM)       // float — ADR-017
    eğer DC(DZ) > 0.5 V ise röle_ac()   // L0-10 (olcum kaynagi burada)
    bayrak_yayinla()                // lock-free
```

## §11 Dil Bazlı İskelet Blokları

### C

```c
/* PSEUDOCODE — SPI okuma + sabit tampon */
/* static float tampon[512];   bashtan allocate (L0) */
/* spi_oku(ham);               register YOK         */
/* cikti = tablo_uygula(ham);  float — ADR-017       */
```

### C++

```cpp
// PSEUDOCODE — Sensor/Driver siniflari (surucu kodu K005'te)
// class Sensor { void oku(float* cikti); };   // cikti[512]
// class Driver { void mod(bool uyku); };      // GPIO — dijital siniri
```

### Rust

```rust
// PSEUDOCODE — surucu trait'i
// trait Sensor { fn oku(&mut self, buf: &mut [f32; 512]); }
// unsafe { /* yalniz DMA/oturken siniri */ }  // `⚠️`
```

### Python

```python
# PSEUDOCODE — kalibrasyon toplama (test)
# ham = adc_oku()
# sapma = ham - referans          # referans bilinen deger
# kayit = {"sapma": sapma, "measured": True}   # OLCUM ISE
```

### C#/.NET

```csharp
// PSEUDOCODE — telemetri kaydi
// record KalibrasyonKaydi(double Sapma, bool Measured = false);
// olcum yoksa "UNKNOWN" — Zero-Hallucination §3
```

### PHP 8.x

```php
<?php
// PSEUDOCODE — kalibrasyon kaydi (PDO parametreli)
// $st = $pdo->prepare('INSERT INTO cal_log (kanal, sapma) VALUES (:k,:s)');
// surucu/karar YOK — yalniz saklama
```

### JS/TS

```javascript
// PSEUDOCODE — canli gosterge (WebSocket okuma)
// ws.onmessage = (m) => renderSensor(JSON.parse(m.data));
// karar/uygulama YOK — L0-10 cekirdekte
```

### Assembly

```asm
; PSEUDOCODE — ornekleme ISR (yalniz kritik yol)
; dma_tampon_doldur   ; 512 ornek — ADR-017
; bayrak_kur          ; ana dongu C'de devralir
```

## §12 OS × Dil Uygunluk Matrisi (öneridir · `⚠️`)

| OS ↓ / Dil → | C | C++ | Rust | Python | C# | PHP | JS/TS | Asm |
|---|---|---|---|---|---|---|---|---|
| Windows | 2 | 2 | 2 | **3** | **3** | 2 | 3 | 0 |
| macOS | 2 | **3** | 2 | **3** | 1 | 2 | 3 | 0 |
| Linux | **3** | **3** | 2 | **3** | 1 | **3** | 3 | 0 |
| iOS | 1 | 2 | 1 | 0 | 0 | 1 | 2 | 0 |
| Android | 1 | 2 | 1 | 1 | 0 | 1 | 2 | 0 |
| Gömülü/RTOS | **3** | **3** | 2 | 1 | 0 | 0 | 0 | 2 |

> 0 = önerilmez · 1 = sınırlı · 2 = uygun · 3 = tercih edilen · ölçüm **DEĞİLDİR**.

## §13 Örnek ↔ Kural Eşlemesi

| # | Örnek | Anayasa kuralı | Kısıt |
|---|---|---|---|
| 1 | §3 shunt | §2(1) P=I²R | `⚠️` hesap |
| 2 | §4 ADC | §3 · §4 | 512/48k · float |
| 3 | §5 kalibrasyon | §5 | UNKNOWN korunur |
| 4 | §6 sürücü | §6 | K005 sınırı |
| 5 | §7 hot-plug | §8 | device-integration `⚠️` |
| 6 | §10 gömülü | §16 anayasa | L0-10 · L0 |

## §14 Yasaklar

| # | Yasak | Neden |
|---|---|---|
| 1 | Füzyon algoritması | K004-dsp |
| 2 | Sürücü mikro-kodu | K005-firmware |
| 3 | Kalibrasyon UI | K0xx `⚠️` |
| 4 | Register/URL uydurma | Zero-Hallucination |
| 5 | Ölçülmüş değer yazma | ölçüm yok |
| 6 | 16/24-bit kaynak | ADR-017 |
| 7 | `Sleep` + lock-free ihlali | L0 |

## §15 Ölçüm Girdi Tablosu

| Girdi | Birim | Durum | Kaynak |
|---|---|---|---|
| Shunt gerilimi | mV | `UNKNOWN` | front-end |
| ADC ham | code | `UNKNOWN` | ADS serisi `⚠️` |
| Akım | mA | `UNKNOWN` | hesap (§3) |
| Sürücü akımı | mA | `⚠️` | devre ayarı |
| Hot-plug | bool | `UNKNOWN` | GPIO |
| DC offset | V | `UNKNOWN` | L0-10 eşiği bağlar |

## §16 Açık Sorular

| ID | Soru | Etiket |
|---|---|---|
| Q-O1 | Elektronik kod örneği hangi repo'da | açık |
| Q-SN1 | Füzyon sınırı kesin mi | UNKNOWN |
| Q-SN2 | Kalibrasyon UI sahibi | `⚠️` |
| Q-SN3 | Sensör vault kanıtı satır bazlı yok | `⚠️` |

## §17 Kapı ve Doğrulama

| Kapı | Aşama | Durum |
|---|---|---|
| Mockup | kod görseli yok | N/A |
| LINE-500 | bu dosya ≥500 | ölçülür |
| Mojibake-0 | UTF-8 regex | 0 ✓ |
| FORMAT | frontmatter 9 alan | ✓ |
| Commit | subagent ATMAZ | ATILMADI |

## §18 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu |

## §19 OS Ayrıntı Tabloları (her OS — öneri · `⚠️`)

### Windows

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Telemetri | Seri/USB JSONL | test |
| 2 | GPIO | Masaüstü kütüphanesi sınırlı `⚠️` | test |
| 3 | Hot-plug | Olay K000, devre K002 | §7 |
| 4 | Kalibrasyon | C# istemcisi | test |
| 5 | Karar | Yok (cekirdek/OS disi) | L0-10 |

### macOS

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Telemetri | Seri/USB JSONL | test |
| 2 | Donanım olayı | IOKit `⚠️` | test |
| 3 | Hot-plug | Olay K000, devre K002 | §7 |
| 4 | Kalibrasyon | Python/C++ CLI | test |
| 5 | Karar | Yok | L0-10 |

### Linux

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Sensör | `iio`/`i2c-dev`/`sysfs` `⚠️` | test |
| 2 | GPIO | `gpiod` benzeri `⚠️` | test |
| 3 | Hot-plug | udev (OS) + devre (K002) | §7 |
| 4 | Kayıt | CSV/JSONL | dürüstlük |
| 5 | Karar | Yok | L0-10 |

### iOS

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Sensör | izleme (yalnız okuma) `⚠️` | izleme |
| 2 | Hot-plug | **YOK** | — |
| 3 | Sürücü | **YOK** | kapsam dışı |
| 4 | Kayıt | CLI çıktısı | dürüstlük |
| 5 | Karar | Yok | L0-10 |

### Android

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Sensör | SensorManager izleme `⚠️` | izleme |
| 2 | Hot-plug | USB OTG olayı (OS) | §7 |
| 3 | Sürücü | **YOK** | kapsam dışı |
| 4 | Kayıt | logcat | dürüstlük |
| 5 | Karar | Yok | L0-10 |

### Gömülü / RTOS

| # | Bileşen | Öneri | Kural |
|---|---|---|---|
| 1 | ADC yolu | SPI/I²C + DMA 512/48k | ADR-017 · L0 |
| 2 | Sürücü | GPIO/PWM + freewheel devrede | §6 |
| 3 | Hot-plug | GPIO + RC + kararlılık sayacı | §7 |
| 4 | Bekleme | `Sleep` YOK | L0 |
| 5 | Veri | 32-bit float | ADR-017 |
| 6 | Röle | >0.5 V → röle | L0-10 |

## §21 Dil Ayrıntı Tabloları (her dil)

| # | Dil | Sensör | Sürücü | Test | Kaçınılan |
|---|---|---|---|---|---|
| 1 | C | tampon + SPI | GPIO | — | tahsis sıcak yol |
| 2 | C++ | RAII sınıf | sınıf | — | kilit |
| 3 | Rust | trait | trait | — | aşırı `unsafe` |
| 4 | Python | script | YOK | **EVET** | gerçek zamanlı |
| 5 | C#/.NET | istemci | YOK | **EVET** | gerçek zamanlı |
| 6 | PHP 8.x | YOK | YOK | kayıt | karar |
| 7 | JS/TS | gösterge | YOK | gösterge | karar |
| 8 | Assembly | ISR | PWM ISR | — | genel mantık |

## §22 Karar Ağacı (örnek seçimi)

| Soru | Evet | Hayır |
|---|---|---|
| Hedef test mi | Python/C# (§11) → çekirdeğe geçme | çekirdeğe geç |
| Hedef gömülü mü | C/C++ (§10) | OS katmanı |
| Sensör okuma gerekli mi | §4 akışı | kalibrasyon atla |
| Sürücü kontrolü gerekli mi | GPIO iskeleti (§6) — kod K005 | atla |
| Hot-plug gerekli mi | §7 iskeleti | atla |
| Sunucuya kayıt var mı | PHP PDO (§11) | atla |
| Gösterge gerekli mi | JS/TS okuma | atla |

## §23 Biçim Sözlüğü (veri yüzeyi)

| Alan | Biçim | Değer | Not |
|---|---|---|---|
| `record_type` | string | `sensor_*` | kayıt türü |
| `raw` | code/V | `UNKNOWN` | ölçülmedi |
| `corrected` | V | `UNKNOWN` | tablo yok |
| `measured` | bool | `false` | dürüstlük |
| `unit` | string | `V`/`mA`/`bool` | birim |
| `block` | int | 512 | ADR-017 sabit |
| `rate_hz` | int | 48000 | ADR-017 sabit |

## §24 Kısıt ↔ Örnek Çaprazı

| # | Kısıt | Örnekteki karşılığı |
|---|---|---|
| 1 | ADR-017 float | §3-§7 çıktı tipi |
| 2 | 512/48k | §4 · §10 blok |
| 3 | L0-10 0.5 V | §10 gömülü döngü |
| 4 | L0 `Sleep` yasağı | §4 · §10 `dma_bekle` |
| 5 | ADR-038 | bu dosya DAC yazmaz |
| 6 | K005 sınırı | §6 |
| 7 | K004 sınırı | §14 yasak 1 |

## §25 Kaynak Atıf Sözlüğü

| Atıf | Ne verir |
|---|---|
| `electroncis-ornekler.md` §7 | dürüstlük şablonu |
| `electroncis-ornekler.md` §15.1 | iskelet |
| anayasa §2-§11 | kural bağları |
| `electroncis-kapsam.md` §B.4 | kapsam içi/dışı |
| `electroncis-kapsam.md` §H.4 | envanter `⚠️` |
| K000 isletim-sistemi | OS atıfları (salt-okunur) |
| ADR-017 · ADR-038 · L0 | kısıtlar |

## §26 Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | `electroncis` yazım | ✓ |
| 2 | PSEUDOCODE etiketli bloklar | ✓ |
| 3 | Register/ölçüm yok | ✓ |
| 4 | OS 6 · dil 8 | ✓ |
| 5 | Mojibake 0 | ✓ |
| 6 | Frontmatter 9 alan | ✓ |
| 7 | K000 salt-okunur | ✓ |
| 8 | Commit | ATILMADI ✓ |

## §27 Ölçüm Tablosu (LINE-500 — nihai)

| # | Dosya | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 1 | `sensor-ve-akteyici-ornekler.md` | ≥500 | ölçülür | — |
| 2 | Mojibake | 0 | 0 | ✓ |
| 3 | `parent:` alan hatası | 0 | 0 | ✓ |
| 4 | Tırnaklı `group` | 0 | 0 | ✓ |
| 5 | Commit | — | ATILMADI | ✓ |

---

**Authority:** `electroncis-ornekler.md` §7 · §15.1 · `.ai/architecture/K002-electroncis/sensor-ve-akteyici/sensor-ve-akteyici-anayasa.md` §2-§11 · ADR-017 · ADR-038
