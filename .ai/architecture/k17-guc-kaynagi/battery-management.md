---
title: "Batarya Yönetim Sistemi (BMS)"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Batarya Yönetim Sistemi (BMS)

## Genel Bakış

BMS (Battery Management System), 6S LiPo bataryanın güvenli ve verimli çalışmasını sağlayan akıllı yönetim kartıdır. Hücre gerilimi izleme, aktif/pasif dengeleme, akım sınırlama ve çoklu koruma katmanları ile batarya ömrünü uzatır ve güvenliği sağlar. COREMUSIC'te Texas Instruments BQ76940 chip tabanlı çözüm kullanılır.

## BMS Mimarisi

```
┌─────────────────────────────────────────────────────────────────────┐
│                        BMS MİMARİSİ                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │  6S LiPo Batarya Hücreleri                                   │  │
│  │  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐          │  │
│  │  │Cell1│─│Cell2│─│Cell3│─│Cell4│─│Cell5│─│Cell6│          │  │
│  │  │3.7V │ │3.7V │ │3.7V │ │3.7V │ │3.7V │ │3.7V │          │  │
│  │  └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘          │  │
│  │     │       │       │       │       │       │               │  │
│  └─────┼───────┼───────┼───────┼───────┼───────┼───────────────┘  │
│        │       │       │       │       │       │                   │
│        ▼       ▼       ▼       ▼       ▼       ▼                   │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │  BQ76940 Analog Front-End (AFE)                              │  │
│  │  • 3-5 hücre gerilimi izleme (±2mV hassasiyet)              │  │
│  │  • 14-bit ADC                                                │  │
│  │  • Sıcaklık sensörleri (3 NTC)                               │  │
│  │  • Akım sensörü (shunt resistor)                             │  │
│  └──────────────────────┬───────────────────────────────────────┘  │
│                         │                                          │
│                         ▼ I²C                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │  STM32L4 MCU (Kontrolcü)                                     │  │
│  │  • Hücre dengesi algoritması                                  │  │
│  │  • SOC (State of Charge) hesaplama                            │  │
│  │  • Koruma kararları                                           │  │
│  │  • UART/CAN haberleşme                                        │  │
│  │  • LED durum göstergesi                                       │  │
│  └──────────────────────┬───────────────────────────────────────┘  │
│                         │                                          │
│                         ▼                                          │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │  Güç FET Sürücüleri                                          │  │
│  │  • Charge FET (P-MOSFET): Şarj akımı kontrol               │  │
│  │  • Discharge FET (N-MOSFET): Deşarj akımı kontrol           │  │
│  │  • Pre-charge FET: Soft start için                           │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Hücre Gerilimi İzleme

### ADC Ölçüm Devresi

```
          Rfilt (100Ω)
Cell 1+ ────┤├────┬──── BQ76940 VC1
                   │
               Cfilt (100nF)
                   │
                 GND
```

**Filtre Parametreleri:**
- Kesim Frekansı: fc = 1 / (2π × R × C) = 15.9kHz
- ADC Çözünürlüğü: 14-bit, 384μV/LSB
- Örnekleme Hızı: 500Hz (her hücre için)
- Doğruluk: ±2mV @ 25°C

### Hücre Gerilim Eşikleri

| Durum | Alt Eşik | Üst Eşik | Aksiyon |
|-------|----------|----------|---------|
| Normal | 3.3V | 4.1V | Serbest çalışma |
| Düşük Uyarı | 3.2V | 4.15V | LED uyarı |
| Aşırı Deşarj | 3.0V | - | Load kesme |
| Aşırı Şarj | - | 4.25V | Şarj kesme |
| Kritik Düşük | 2.8V | - | Sistem kapatma |
| Kritik Yüksek | - | 4.3V | Sistem kapatma |

## Hücre Dengeleme

### Pasif Dengeleme Devresi

```
           CBAL (100pF)
              │
Cell i ──┬───┤├───┬── DCHG Pin
         │         │
         RBAL      │  BQ76940 Internal
        (100Ω)     │  Balance MOSFET
         │         │
        GND        │
```

**Dengeleme Akımı:**
```
IBAL = (VCELL_HIGH - VCELL_LOW) / RBAL
IBAL = (4.20V - 4.15V) / 100Ω
IBAL = 0.5A (maksimum)
```

### Dengeleme Algoritması

```
┌─────────────────────────────────────────────────┐
│  Hücre Dengeleme Algoritması                     │
├─────────────────────────────────────────────────┤
│                                                   │
│  1. Tüm hücre gerilimlerini oku                  │
│     V[6] = {V1, V2, V3, V4, V5, V6}            │
│                                                   │
│  2. Ortalama gerilimi hesapla                     │
│     VAVG = ΣV[i] / 6                             │
│                                                   │
│  3. Sapmaları hesapla                             │
│     ΔV[i] = V[i] - VAVG                          │
│                                                   │
│  4. Eşik kontrolü                                 │
│     If |ΔV[i]| > 20mV:                           │
│       If V[i] > VAVG:                            │
│         Hücre i'yi deşarj et (RBAL üzerinden)    │
│       End If                                      │
│     End If                                        │
│                                                   │
│  5. 100ms bekle, tekrarla                         │
│                                                   │
└─────────────────────────────────────────────────┘
```

## SOC (State of Charge) Hesaplama

### Coulomb Counting

```
SOC(t) = SOC(t₀) - (1/CAPACITY) × ∫ I(t) dt

Örnekleme: 1kHz
İntegrasyon: Trapezoidal method
Hata: ±3% (kalibrasyon ile ±1%)
```

### OCV (Open Circuit Voltage) Kalibrasyonu

| SOC (%) | OCV (V) | Not |
|---------|---------|-----|
| 100 | 25.2V | Tam şarj, 1 saat bekleme |
| 90 | 24.9V | |
| 80 | 24.6V | |
| 70 | 24.3V | |
| 60 | 24.0V | |
| 50 | 23.7V | |
| 40 | 23.3V | |
| 30 | 22.8V | |
| 20 | 22.2V | Nominal |
| 10 | 21.6V | |
| 5 | 21.0V | Minimum kullanım |

## Koruma Fonksiyonları

### Aşırı Akım Koruması

```
Akım Ölçümü (Shunt: 1mΩ)
       │
       ▼
┌──────────────────┐
│ ADC Okuma        │  14-bit, 125μV/LSB
│ (BQ76940)        │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Karşılaştırma    │
│ I > 10A → UYARI  │  500ms gecikme
│ I > 15A → KES    │  10ms gecikme
│ I > 20A → SHORT  │  <1ms gecikme
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ FET Kontrol      │
│ Discharge FET OFF│  Güvenli kapatma
└──────────────────┘
```

### Sıcaklık Koruması

```
NTC Sensörleri (3 adet)
       │
       ▼
┌──────────────────┐
│ ADC Okuma        │  B = 3950, R25 = 10kΩ
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Sıcaklık Hesapı  │
│ Steinhart-Hart   │
│ veya lookup table│
└────────┬─────────┘
         │
         ▼
┌──────────────────────────────────────────┐
│ Koruma Eşikleri                           │
├──────────────────────────────────────────┤
│ Şarj: -10°C to +45°C (dar aralık)       │
│ Deşarj: -20°C to +60°C (geniş aralık)   │
│ Depolama: 10°C to +25°C (ideal)          │
│ Kritik: +80°C → acil kapatma             │
└──────────────────────────────────────────┘
```

## Güç Modları

### Çalışma Modları

```
┌───────────────────────────────────────────────────────────┐
│                    BMS GÜÇ MODLERİ                         │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  NORMAL MODE                                              │
│  • Tüm hücreler izleniyor                                │
│  • Dengeleme aktif                                        │
│  • MCU tam güçte çalışıyor                               │
│  • Akım消耗: 5mA                                          │
│                                                           │
│  STANDBY MODE                                             │
│  • Hücreler 10sn'de bir okunuyor                         │
│  • Dengeleme pasif                                        │
│  • MCU uyku modunda                                       │
│  • Akımconsumption: 50μA                                 │
│                                                           │
│  SHUTDOWN MODE                                            │
│  • Tüm FET'ler kapalı                                    │
│  • Sadece wake-up devresi aktif                           │
│  • Akımconsumption: 5μA                                  │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

## Haberleşme Arayüzü

### I²C Protokolü (BQ76940 ↔ MCU)

```
STM32L4                        BQ76940
   │                              │
   │  START                       │
   │  0x08 (Write)                │
   │  Register: 0x00              │
   │  ───────────────────────────▶│
   │                              │
   │  ACK                         │
   │  ◀───────────────────────────│
   │                              │
   │  Data Request                │
   │  ───────────────────────────▶│
   │                              │
   │  Cell Voltage Data (6×2byte) │
   │  ◀───────────────────────────│
   │                              │
   │  STOP                        │
   │  ───────────────────────────▶│
```

**I²C Parametreleri:**
- Hız: 400kHz (Fast Mode)
- Adres: 0x08 (7-bit)
- Register Haritası: 80+ register
- Güvenlik: CRC checksum (opsiyonel)

## PCB Tasarım Kuralları

1. **Hücre Bağlantıları:** Kelvin connection, DRC trace width
2. **Shunt Resistor:** 4-terminal (force/sense), low inductance
3. **NTC Yerleşimi:** Hücre paketine termal olarak bağlı
4. **FET Yerleşimi:** Thermal pad, copper pour for heatsink
5. **I²C Traces:** 100mil spacing, ground guard
6. **Power Path:** Wide copper, minimal resistance

## Spesifikasyonlar

| Parametre | Değer | Birim |
|-----------|-------|-------|
| Hücre Sayısı | 3-6 | seri |
| Gerilim Doğruluğu | ±2 | mV |
| Akım Doğruluğu | ±100 | mA |
| Dengeleme Akımı | 50-200 | mA |
| Çalışma Akımı | 5 | mA |
| Uyku Akımı | 50 | μA |
| Shutdown Akımı | 5 | μA |
| I²C Hızı | 400 | kHz |
| Çalışma Sıcaklığı | -40 to +85 | °C |

## Bağımlılıklar

| Bileşen | Adet | Kullanım |
|---------|------|----------|
| BQ76940 | 1 | Analog front-end |
| STM32L4 | 1 | Kontrol MCU |
| IRLML6402 | 2 | Charge/Discharge FET |
| 1mΩ Shunt | 1 | Akım ölçüm |
| NTC 10kΩ | 3 | Sıcaklık izleme |
| 3.3V LDO | 1 | MCU besleme |

## Durum: Implementasyon

✅ BQ76940 AFE seçimi ve pinout doğrulandı  
✅ Hücre gerilimi izleme devresi tasarlandı  
✅ Pasif dengeleme algoritması kodlandı  
✅ SOC hesaplama (coulomb counting + OCV) entegre edildi  
✅ Koruma eşikleri belirlendi ve test edildi  
✅ I²C haberleşme protokolü uygulandı  
✅ Güç modları (Normal/Standby/Shutdown) aktif  
⚠️ Hücre dengesi uzun vadeli testi devam ediyor  
⚠️ Kalibrasyon prosedürü dokümante ediliyor
