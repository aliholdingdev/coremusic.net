---
type: electronic
category: embedded-drivers
title: "CoreMusic — Embedded Platform Drivers (RPi5, ARM64)"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Embedded Platform Drivers

**See also:** [[electronic/drivers/index]] · [[electronic/hardware/index]] · [[electronic/drivers/audio-drivers]] · [[electronic/drivers/device-integration]]

> **Repo gerçeği:** Bu depoda gömülü platform (RPi5/ARM64) üzerinde çalışan gerçek bir C/C++ sürücü kodu **YOK** (`.cpp`/`.c`/`.h` taraması 0 sonuç). Bu dosya, `.ai/electronic/**` vault mimarisinin gömülü sürücü tasarımını ve donanım arayüz şartnamelerini tanımlar — çalışan kod kanıtı değil, **tasarım şartnamesidir**.

---

## 1. Amaç & Gömülü Katman

Embedded Platform Drivers, CoreMusic platformunun gömülü sistemlerdeki (Raspberry Pi 5, ARM64) donanım sürücülerini tanımlar. Bu katman, SoC peripherals (I2S, I2C, SPI, UART) ile üst işletim sistemi arasındaki arayüzü taşır.

### 1.1 Katman Konumu

```
┌─────────────────────────────────────────────┐
│  Uygulama (Neva Engine / Player)            │
├─────────────────────────────────────────────┤
│  OS Ses Katmanı (ALSA / PulseAudio)         │
├─────────────────────────────────────────────┤
│  I2S Sürücüsü (BU DOSYA)                    │
├─────────────────────────────────────────────┤
│  DMA (I2S ↔ Bellek)                         │
├─────────────────────────────────────────────┤
│  SoC Peripheral (BCM2712)                   │
├─────────────────────────────────────────────┤
│  DAC (PCM3168A — ADR-038 H001)              │
└─────────────────────────────────────────────┘
```

### 1.2 Raspberry Pi 5 Destek

| Özellik | Değer |
|---------|-------|
| CPU | BCM2712, Quad-core 2.4GHz Cortex-A76 |
| RAM | 4GB/8GB LPDDR4X |
| GPIO | 40-pin header |
| I2S | PCM5102A, PCM3168A |
| USB | USB 3.0 x2, USB 2.0 x2 |
| WiFi | 802.11ac |
| Bluetooth | 5.0, BLE |

> **ADR-038 H001 (bağlayıcı):** PCM5122 **ABSOLUTELY FORBIDDEN** — yalnızca PCM3168A. PCM5102A, vault'ta referans edilen bir DAC'dır; mevcut RPi5 destek tablosunda listelenmiştir. Yeni DAC seçiminde ADR-038 geçerlidir.

### 1.3 Donanım Arayüzleri (Peripheral Interfaces)

| Arayüz | Görev | Hız | Kullanım |
|--------|-------|-----|----------|
| I2S | Ses verisi | ≤3.072 MHz (48kHz/64slot) | DAC/ADC |
| I2C | Control/register | ≤400 kHz (Fast) | DAC konfigürasyonu |
| SPI | Yüksek hızlı register | ≤50 MHz | Display/sensor |
| UART | Seri konsol | ≤921600 baud | Debug/boot |
| GPIO | Genel amaçlı | — | Reset, IRQ, LED |

### 1.4 GPIO Pin Atamaları

| Pin | İşlev | Not |
|-----|-------|-----|
| 18 | I2S BCLK | Bit Clock |
| 19 | I2S LRCLK | Word Select |
| 21 | I2S DIN | Data In (TX) |
| 20 | I2S DOUT | Data Out (RX) |
| 3 | SDA | I2C Data |
| 5 | SCL | I2C Clock |
| 25 | Reset | Hardware Reset |

**GPIO pin notları:**

1. I2S BCLK/LRCLK, DAC'ın slave mode'unda SoC'tan üretilir (master).
2. I2C SDA/SCL, PCM3168A register erişimi için kullanılır (ADR-038).
3. Hardware Reset (pin 25), boot sırasında DAC'ı sıfırlar.

### 1.5 I2S Driver

| Özellik | Değer |
|---------|-------|
| Format | I2S, Left-justified |
| Bit | 24-bit, 32-bit |
| Örnekleme | 44.1kHz, 48kHz, 96kHz |
| DMA Buffer | 4096 byte |
| Latency | <5ms |

**I2S pin tanımı:**

| Pin | Sinyal | Yön | Frekans |
|-----|--------|-----|---------|
| BCLK | Bit clock | SoC → DAC | fs × 2 × 64 |
| LRCLK (WS) | Word select | SoC → DAC | fs (44.1/48/96 kHz) |
| DIN | Serial data in | SoC → DAC | — |
| DOUT | Serial data out | DAC → SoC | — (ADC) |

**I2S kuralı:** BCLK = fs × bitderinliği × kanal; 48kHz/32-bit/stereo = 3.072 MHz. 8.1 surround (9 kanal, ADR-038) TDM slotları gerektirir (§1.6).

### 1.6 TDM (Time-Division Multiplexing) — 8.1 Surround

| Parametre | Değer (8.1) | Not |
|-----------|-------------|-----|
| Kanal | 9 (ADR-038) | TDM slot |
| Slot başına bit | 32 | 24-bit + padding |
| BCLK | fs × 32 × 9 | 48kHz → 13.824 MHz |
| FS (frame sync) | fs | LRCLK = slot 0 |

**TDM kuralı:** 8.1 surround (9 kanal) TDM modu gerektirir; I2S modu tek stereo (2 kanal) taşır. ADR-038 H001: PCM3168A TDM destekler.

---

## 2. Kesme (IRQ) ve DMA

### 2.1 DMA Akışı (I2S ↔ Bellek)

```
Uygulama (Player)
  │ write() → ring buffer
  ▼
OS Ses Katmanı (ALSA)
  │ mmap → DMA buffer
  ▼
DMA motoru
  │ DMA channel (I2S TX)
  ▼
I2S Peripheral
  │ BCLK/LRCLK/DIN
  ▼
DAC (PCM3168A) → Hoparlör
```

### 2.2 DMA Kanal Planlaması

| Kanal | Kullanım | Öncelik | Not |
|-------|----------|---------|-----|
| 0 | I2S TX | Yüksek | Ses verisi |
| 1 | I2S RX | Yüksek | ADC (geri besleme) |
| 2 | SPI (display) | Orta | |
| 3 | I2C (yedek) | Düşük | |
| 4-7 | Serbest | — | |

### 2.3 IRQ (Interrupt) Yönetimi

| IRQ | Kaynak | Öncelik | Gecikme Hedefi |
|-----|--------|---------|----------------|
| I2S TX | DMA buffer empty | En yüksek | ≤10 µs |
| I2S RX | DMA buffer full | Yüksek | ≤10 µs |
| I2C | Transfer complete | Orta | ≤100 µs |
| GPIO (reset) | Hardware event | Düşük | ≤1 ms |

**IRQ kuralları:**

1. I2S TX IRQ, DMA buffer underrun'u önler; handler en kısa sürede buffer doldurur (§2.4).
2. IRQ handler, sıfır tahsis (zero-allocation, kural 1) ve kilit-dışı (lock-free, kural 2) çalışır.
3. IRQ öncelik sırası: I2S > SPI > I2C > GPIO.

### 2.4 DMA Buffer Hesabı

| Parametre | Değer (48kHz/32-bit/stereo) | Not |
|-----------|-----------------------------|-----|
| Buffer boyutu | 4096 byte (§1.5) | Mevcut tasarım |
| Buffer süresi | 4096 / (48000 × 4 × 2) ≈ 10.67 ms | 2 tampon (ADR-017: 512 sample) |
| DMA channel | I2S TX (§2.2) | |
| Underrun riski | Buffer süresi ≤ IRQ gecikmesi | §2.3 |

**DMA buffer kuralı:** 4096 bayt buffer ≈ 10.67 ms (48kHz/32-bit/stereo); ADR-017 (512 sample) ile uyumlu. Underrun, DMA buffer boşaldığında oluşur — IRQ handler zamanında doldurmalıdır.

---

## 3. Düşük Güç (Power Management)

### 3.1 Düşük Güç Modları

| Mod | Açıklama | Ses Etkisi |
|-----|----------|------------|
| Active | Tüm peripheral aktif | En düşük latency |
| Idle | CPU clock gating | Orta |
| Sleep (suspend-to-RAM) | RAM korunur, peripheral kapalı | Akış kesilir |
| Hibernate (suspend-to-disk) | Disk'e yazılır | Sıfırdan boot |

**Tasarım kuralı:** Ses akışı sırasında RPi5 **Active/Idle** modunda kalmalıdır; Sleep/Hibernate akışı kesintiye uğratır.

### 3.2 DAC Güç Yönetimi (PCM3168A)

| Durum | I2C Register | Akım | Not |
|-------|--------------|------|-----|
| Normal | 0x00 (Mode 1) | ~30 mA (typik) | Çalışma |
| Mute | 0x00 (mute bit) | ~30 mA | Çıkış susturulur |
| Power-down | 0x00 (PD bit) | ~1 µa | Düşük güç |

> ⚠️ VERIFICATION REQUIRED: PCM3168A register değerleri TI datasheet'ten doğrulanmalıdır; bu tablo yalnızca tasarım niyetini gösterir.

### 3.3 Isı (Thermal) Yönetimi

| Parametre | Değer | Not |
|-----------|-------|-----|
| BCM2712 throttle | 80°C (tipik) | CPU clock ↓ |
| DAC sıcaklık | Datasheet sınırı | PCM3168A |
| PCB ısı dağıtımı | Pasif soğutucu | Isı yayılımı |

**Isı kuralı:** Uzun süreli ses akışında BCM2712 throttle'a uğramamalıdır (§7.3); throttle, DMA/IRQ zamanlamasını bozar, dropout riski ↑.

---

## 4. RTOS / Zaman Kritik Sürücü

### 4.1 RTOS Gereksinimleri (Real-Time OS)

| Gereksinim | Değer | Not |
|------------|-------|-----|
| Task priority (I2S) | En yüksek | §2.3 IRQ |
| Context switch | ≤10 µs | Hedef |
| Timer resolution | ≤1 µs | fs senkronu |
| Jitter | ≤10 µs | DMA underrun önleme |

> ⚠️ VERIFICATION REQUIRED: CoreMusic'in RPi5'te gerçek zamanlı bir RTOS kullanıp kullanmayacağı vault'ta tanımlı değil; bu tablo yalnızca gereksinimleri tanımlar.

### 4.2 Zaman Kritik Görevler (Time-Critical Tasks)

| Görev | Periyot | Deadline | Not |
|-------|---------|----------|-----|
| I2S DMA refill | 10.67 ms (§2.4) | ≤1 ms | Underrun önleme |
| IRQ handler | IRQ tetiklenmesi | ≤10 µs | §2.3 |
| I2C register (mute) | Olay bazlı | ≤1 ms | DAC kontrolü |
| Watchdog | 1 saniye | — | Sistem kurtarma |

### 4.3 Gömülü Sürücü Akışı

```
RPi5 Boot ──▶ GPIO Init ──▶ I2S Init ──▶ DAC Bağla ──▶ Stream Başlat ──▶ İzleme
```

**Boot akışı adımları:**

| Adım | İşlem | Hata Modu |
|------|-------|-----------|
| 1 | RPi5 Boot | Boot hatası |
| 2 | GPIO Init (§1.4) | Pin atama çakışması |
| 3 | I2S Init (§1.5) | BCLK/LRCLK senkron hatası |
| 4 | DAC Bağla (PCM3168A, I2C) | I2C ACK yok |
| 5 | Stream Başlat (DMA §2.1) | Underrun |
| 6 | İzleme (heartbeat/watchdog) | Bağlantı timeout |

---

## 5. Gömülü Hata Modları

| Hata | Belirti | Kök Neden | Çözüm |
|------|---------|-----------|-------|
| I2S underrun | Dropout / clicking | DMA buffer boş | Buffer büyütme (§2.4), IRQ öncelik |
| I2C ACK yok | DAC register yazılamıyor | Kablo/pin | §1.4 pin doğrulama |
| BCLK senkron hatası | Ses bozuk | fs uyuşmazlığı | §1.5 BCLK hesabı |
| Throttle | Latency ↑ | Isı (§3.3) | Soğutma, clock throttle |
| Boot hatası | Sistem açılmıyor | Boot partition | Boot sıfırlama |
| DAC reset | Çıkış yok | GPIO reset (§1.4) | Hardware reset |

---

## 6. Ölçüm (Measurement)

### 6.1 I2S Sinyal Ölçümü

| Metod | Araç | Kabul |
|-------|------|-------|
| BCLK frekansı | Osiloskop | fs × 64 (±%0.1) |
| LRCLK senkronu | Osiloskop | fs (44.1/48/96 kHz) |
| DIN verisi | Logic analyzer | I2S format (§1.5) |
| DMA underrun | IRQ sayacı | 0 underrun / 1 saat |

### 6.2 DMA/IRQ Ölçümü

| Metod | Araç | Kabul |
|-------|------|-------|
| DMA buffer süresi | Timer | 10.67 ms (§2.4) |
| IRQ gecikmesi | Timestamp | ≤10 µs (§2.3) |
| Context switch | RTOS profiling | ≤10 µs (§4.1) |
| Jitter | IRQ timestamp analizi | ≤10 µs (p95) |

### 6.3 Sistem Ölçümü

| Metod | Araç | Kabul |
|-------|------|-------|
| CPU kullanımı | `top`/`htop` | ≤%25 (48kHz/32-bit/stereo) |
| Isı (thermal) | `vcgencmd measure_temp` | <80°C (§3.3) |
| Bellek | `free` | Tahsis yok (kural 1) |
| Underrun (ALSA) | `/proc/asound/card0/stat` | 0 underrun |

### 6.4 Ölçüm Adımları

| Adım | İşlem | Çıktı |
|------|-------|-------|
| 1 | Boot (§4.3) | Sistem aktif |
| 2 | GPIO/I2S init (§1.4/§1.5) | Pin/sinyal aktif |
| 3 | DAC bağla (PCM3168A, §3.2) | Register OK |
| 4 | DMA başlat (§2.1) | Buffer akıyor |
| 5 | Akış başlat (§4.3) | Play |
| 6 | IRQ/jitter ölç (§6.2) | ≤10 µs |
| 7 | Underrun/ısıl ölç (§6.3) | 0 underrun, <80°C |
| 8 | Raporla | Ölçüm kaydı |

### 6.5 Kabul Kriterleri

| Kriter | Eşik | Test |
|--------|------|------|
| BCLK frekansı | fs × 64 (±%0.1) | §6.1 |
| IRQ gecikmesi | ≤10 µs | §6.2 |
| DMA buffer | 10.67 ms (§2.4) | §6.2 |
| Underrun (1 saat) | 0 | §6.3 |
| CPU kullanımı | ≤%25 | §6.3 |
| Isı | <80°C | §6.3 |
| I2C register | ACK (PCM3168A) | §6.1 |

---

## 7. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A |

**ADR-038 H001 hatırlatma:** PCM5122 **ABSOLUTELY FORBIDDEN** — yalnızca PCM3168A (§1.2). 8.1 surround = 9 kanal TDM (§1.6); 512 sample/48kHz/32-bit float (ADR-017, §2.4).

---

## 8. Kaynakça (Sources)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Linux kernel driver-model platform doc | https://www.kernel.org/doc/html/latest/driver-api/driver-model/platform.html | 2026-10-13 | HTTP 200 ✓ |
| Linux kernel driver-api basics doc | https://www.kernel.org/doc/html/latest/driver-api/basics.html | 2026-10-13 | HTTP 200 ✓ |

**Vault içi referanslar:** [[electronic/drivers/index]] · [[electronic/drivers/audio-drivers]] · [[electronic/drivers/device-integration]] · [[electronic/hardware/index]] · [[electronic/firmware/index]] · [[electronic/drivers/usb-drivers]]

---

## 9. I2S/TDM Clock Matematiği ve DMA Buffer Derinlemesi

### 9.1 BCLK Hesabı (Stereo vs TDM)

```
Stereo I2S (2 kanal):  BCLK = fs × bit × 2
  48 kHz / 32-bit     → 48000 × 32 × 2 = 3.072 MHz
TDM (9 kanal, 8.1 — ADR-038): BCLK = fs × slot × N
  48 kHz / 32-slot / 9 kanal → 48000 × 32 × 9 = 13.824 MHz
Frame sync (LRCLK/FS) = fs (48 kHz), slot 0 = sol kanal işareti
```

### 9.2 DMA Buffer Süresi Hesabı (Genel)

```
T_buffer (bayt) = N_tampon × N_kanal × bayt/sample × period
48 kHz / 32-bit (4 bayt) / stereo:
  2 tampon × 2 × 4 × 512 = 8192 bayt ≈ 21.33 ms   (ADR-017: 512 sample)
8.1 TDM (9 kanal):
  2 tampon × 9 × 4 × 512 = 36864 bayt ≈ 21.33 ms  (aynı süre, 9 kanal)
```

### 9.3 MCLK (Master Clock) İlişkisi

```
MCLK = fs × PLL katsayısı (genelde 256×fs veya 128×fs)
  48 kHz × 256 = 12.288 MHz (tipik MCLK → DAC PLL girdisi)
PCM3168A (ADR-038 H001): MCLK/LRCLK oranı register ile ayarlanır (I2C §1.4)
```

### 9.4 Kanal Başı DMA ve 8.1 TDM Slotları

| Kanal (8.1) | TDM Slot | Not |
|-------------|----------|-----|
| 0 | Slot 0 | Front-Left |
| 1 | Slot 1 | Front-Right |
| 2 | Slot 2 | Center |
| 3 | Slot 3 | LFE (subwoofer) |
| 4 | Slot 4 | Surround-Left |
| 5 | Slot 5 | Surround-Right |
| 6 | Slot 6 | Back-Left |
| 7 | Slot 7 | Back-Right |
| 8 | Slot 8 | Height/Atmos (9. kanal) |

### 9.5 IRQ ↔ DMA Zamanlama Bütçesi

```
Periyot (T_period) = 512 / 48000 = 10.667 ms
DMA refill deadline = T_period − iş gecikmesi
Kabul: IRQ gecikmesi ≤10 µs (§2.3) → kalan 10.657 ms DSP için
Underrun oluşmaz: IRQ handler periyot dolmadan buffer doldurur
```

### 9.6 Ölçüm Kaydı Şeması (Embedded)

| Alan | Tip | Örnek |
|------|-----|-------|
| timestamp | ISO 8601 | 2026-10-13T12:00:00Z |
| bclk_mhz | float | 3.072 (stereo) / 13.824 (TDM) |
| mclk_mhz | float | 12.288 |
| dma_period_ms | float | 10.667 |
| irq_latency_us | float | 8.0 |
| underrun_count | int | 0 |
| cpu_pct | float | 22.0 |
| temp_c | float | 62.0 |
| verdict | enum | PASS / RED |

---

## 10. PLL/Klok Drift ve Jitter Derinlemesi

### 10.1 Klok Kaynağı ↔ Drift İlişkisi

| Klok Kaynağı | Tipik Drift | Kullanım |
|--------------|-------------|----------|
| Kristal osilatör (XTAL) | ±20–50 ppm | Standart |
| Gömülü RC osilatör | ±%1–5 (≈%50000 ppm) | Yetersiz (ses için) |
| TCXO (sıcaklık kompanzasyonlu) | ±0.5–2 ppm | Yüksek kalite |
| NTP/PTP senkron (§10.1 net) | µs (ağ) | Multi-room |

**Kural:** Ses klok kaynağı (MCLK/BCLK §9.3) kristal (XTAL) tabanlı olmalıdır; RC osilatör ±%1–5 drift → ses.dropout/artifacts.

### 10.2 Sample Drift ↔ Buffer Hesabı

```
Drift emme = jitter tamponu / drift farkı
İki cihaz ±20 ppm (§5.2) → fark ≤40 ppm = 40 µs/sn
  512-sample buffer (10.667 ms) → emme süresi = 10.667 ms / (40 µs/sn) ≈ 267 sn
  Çift tampon (21.33 ms) → ≈533 sn ≈ 9 dk
Kabul: emme süresi > oturum süresi → drift dropout üretmez
```

### 10.3 BCLK Jitter Ölçümü (RMS)

```
BCLK jitter (RMS) = σ(t_edge − t_nominal)
Hedef: jitter ≤ 1 ns RMS (ses kalitesi için)
MCL/k jitter ilişkisi: DAC jitter tolerance (PCM3168A datasheet) > ölçilen jitter
Ölçüm: osiloskop time-interval error (TIE) veya faz noise analyzer
```

### 10.4 DMA Jitter Ölçümü (IRQ Timestamp)

```
IRQ gecikmesi jitter'ı = σ(IRQ_timestamp − expected_period)
Örnek (§2.3 hedef ≤10 µs):
  1000 IRQ ölç → p50/p95/p99 hesapla
Kabul: p99 ≤ 10 µs (§2.3); p99 > deadline → underrun riski (§9.5)
```

### 10.5 Sıcaklık ↔ Drift (Thermal Drift)

| Sıcaklık Değişimi | Kristal Drift (tipik) | Etki |
|-------------------|------------------------|------|
| ±10°C | ±1 ppm | Minimal |
| ±25°C | ±2.5 ppm | §10.2 emilim |
| ±50°C (throttle §3.3) | ±5 ppm | Drift ↑, emilim ↓ |

**Termal kural:** Throttle (§3.3, 80°C) yalnızca CPU clock'u düşürür; kristal drift'i artmaz — ancak DMA/IRQ zamanlaması (§9.5) bozulabilir → dropout riski.

### 10.6 Klok Drift Ölçüm Adımları

| Adım | İşlem | Çıktı |
|------|-------|-------|
| 1 | XTAL drift ölç (frekans sayacı) | ±ppm |
| 2 | İki cihaz drift farkı | ≤40 ppm (§10.2) |
| 3 | Buffer emme süresi hesapla | ≥ oturum süresi |
| 4 | BCLK jitter ölç (§10.3) | ≤1 ns RMS |
| 5 | IRQ jitter ölç (§10.4) | p99 ≤10 µs |
| 6 | Raporla | §9.6 şeması |

---

## 11. I2C Register Erişimi ve Boot Zamanlama Bütçesi

### 11.1 I2C Register Yazma/Okuma Sırası (PCM3168A — ADR-038 H001)

```
Boot → GPIO reset (pin 25 §1.4) → I2C bus init (§1.5, ≤400 kHz Fast)
  → DAC register yaz (mode/mute/PLL §3.2) → ACK bekle (§6.1)
  → I2S clock init (BCLK/LRCLK/MCLK §9.1/§9.3) → DMA başlat (§2.1)
  → Akış başlat (§4.3)
```

**I2C kuralları:**

1. Register yazımı, I2S clock'tan ÖNCE yapılır — DAC PLL kilitlenmesi (§9.3) için.
2. ACK yoksa (§5 hata tablosu) I2C bus reset → 3 retry → escalate.
3. Mute register'ı, akış bitiminde set edilir (graceful stop §9.4 device-int).

### 11.2 Boot→Hazır Süre Bütçesi

| Aşama | Hedef | Not |
|-------|-------|-----|
| RPi5 boot (kernel→user) | ≤10 sn (tipik) | OS bağımlı |
| GPIO/I2S init (§1.4/§1.5) | ≤10 ms | |
| DAC register (I2C §11.1) | ≤5 ms | ACK |
| DMA warm-up (2 tampon) | 21.33 ms | §2.4 |
| **Ses-çıktı- hazıra toplam** | **boot + ~50 ms** | Akışa hazır |

**Kural:** Boot sonrası ilk ses, DMA warm-up (2 tampon, §2.4) dolmadan çıkmaz — underrun önleme (§9.5).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
