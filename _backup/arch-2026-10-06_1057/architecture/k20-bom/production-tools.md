---
title: "Üretim Araçları"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Üretim Araçları

## Genel Bakış

Bu doküman COREMUSIC donanımının seri üretimi için gerekli üretim araçlarını, ekipmanları ve süreçlerini tanımlar. Pick-and-place, reflow lehimleme, test ve kalite kontrol prosedürleri kapsamlı olarak ele alınmıştır.

## Üretim Hattı Ekipmanları

### Pick & Place Makineleri

| Ekipman | Model | Kapasite | Maliyet | Tedarikçi |
|---------|-------|----------|---------|-----------|
| Yarı Otomatik | Yamaha YSM20 | 15,000 CPH | $45,000 | Yamaha |
| Tam Otomatik | JUKI RS-1R | 42,000 CPH | $85,000 | JUKI |
| Prototype | LitePlacer | 1,000 CPH | $3,500 | LitePlacer |
| Backup | Manncorp 7100A | 5,000 CPH | $12,000 | Manncorp |

### Reflow Lehimleme

| Ekipman | Model | Sıcaklık | Maliyet | Tedarikçi |
|---------|-------|----------|---------|-----------|
| Conveyor | Heller 1913 MK5 | 350°C | $35,000 | Heller |
| Conveyor Alt | Vitronics XPM820 | 320°C | $28,000 | Vitronics |
| Prototype | T-962 | 300°C | $350 | SainSmart |
|selective | Electrovert EconoPak | 350°C | $18,000 | Electrovert |

###₺ Test Ekipmanları

| Ekipman | Model | Kullanım | Maliyet | Tedarikçi |
|---------|-------|----------|---------|-----------|
| ICT | Keysight 3070 | In-Circuit | $85,000 | Keysight |
| ICT Alt | Teradyne UltraFLEX | In-Circuit | $65,000 | Teradyne |
| Fonksiyonel | NI PXI | Fonksiyonel | $12,000 | NI |
| Oscilloscope | Keysight DSOX3024 | Sinyal | $4,500 | Keysight |
| Audio Analyzer | Audio Precision APx555 | Ses | $15,000 | Audio Precision |
| Multimeter | Keysight 34461A | Genel | $1,800 | Keysight |

### Lehimleme İstasyonları

| Ekipman | Model | Güç | Maliyet | Tedarikçi |
|---------|-------|-----|---------|-----------|
| Hot Air | Weller WXA 2050 | 200W | $850 | Weller |
| Soldering Iron | Hakko FX-951 | 70W | $280 | Hakko |
| Desoldering | Weller WRN 400 | 150W | $650 | Weller |
| Rework | Pace MW 2200 | 220W | $1,200 | Pace |

## Üretim Süreci

### Adım 1: PCB Hazırlık

```
Süre: 30 dk/adet
1. PCB insensitive
2. Stencil yükleme
3. Fixture montaj
4. Kamera kalibrasyonu
5. Malzeme yükleme
```

### Adım 2: SMD Lehimleme

```
Süre: 15 dk/adet
1. Solder paste uygulama
2. Pick & place
3. Paste inspection (SPI)
4. Reflow oven
5. AOI kontrolü
```

### Adım 3: Through-Hole

```
Süre: 20 dk/adet
1. Bileşen Insertion
2. Dipping solder
3. Wave soldering (opsiyonel)
4. Touch-up soldering
5. Visual inspection
```

### Adım 4: Test

```
Süre: 15 dk/adet
1. ICT test
2. Fonksiyonel test
3. Ses kalitesi testi
4. Güvenlik testi
5. Burn-in testi
```

### Adım 5: Montaj

```
Süre: 20 dk/adet
1. Heatsink montajı
2. Konnektör montajı
3. Kasa montajı
4. Kablo bağlantıları
5. Son kontrol
```

## Kalite Kontrol Prosedürleri

### First Article Inspection (FAI)

| Test | Kriter | Geçme |
|------|--------|-------|
| Boyut kontrolü | ±0.1mm | %100 |
| Lehim kalitesi | IPC-A-610 Class 2 | %100 |
| Electrical test | schematic'e uygunluk | %100 |
| Fonksiyonel test | Tüm parametreler | %100 |
| Görünüm kontrolü | Kozmetik standartlar | %100 |

### SPC (Statistical Process Control)

| Parametre | hedef | Alt Limit | Üst Limit |
|-----------|-------|-----------|-----------|
| Placement accuracy | ±0.05mm | -0.08mm | +0.08mm |
| Solder volume | ±15% | -20% | +20% |
| Reflow peak temp | 245°C | 235°C | 255°C |
| ICT pass rate | %99.5 | %99.0 | - |
| First pass yield | %98.0 | %95.0 | - |

### İstatistiksel Analiz

| Metrik | Hedef | Gerçek | Durum |
|--------|-------|--------|-------|
| DPMO | <100 | 75 | Geçti |
| Sigma Level | >5.0 | 5.2 | Geçti |
| Cpk | >1.33 | 1.45 | Geçti |
| FPY | >98% | 98.5% | Geçti |

## Üretim Takip Sistemi

### PCB Seri Numaralandırma

```
Format: CM-YYYYMMDD-XXXX
Örnek: CM-20260920-0001
CM: COREMUSIC
YYYYMMDD: Tarih
XXXX: Sıra numarası
```

### Izlenebilirlik Matrisi

| Veri | Kaynak | Saklama |
|------|--------|---------|
| BOM | ERP sistemi | 10 yıl |
| Lot numarası | Üretim hattı | 10 yıl |
| Test sonuçları | Test istasyonu | 5 yıl |
| Operatör bilgisi | Personel sistemi | 3 yıl |
| Tedarikçi bilgisi | Tedarikçi sistemi | 10 yıl |

## Üretim Maliyeti Dağılımı

| Kalem | Maliyet/Adet | Yüzde |
|-------|--------------|-------|
| BOM | $197.00 | %56.3 |
| PCB | $8.50 | %2.4 |
| İşçilik | $45.00 | %12.9 |
| Test | $33.00 | %9.4 |
| Montaj | $22.00 | %6.3 |
| Paketleme | $5.00 | %1.4 |
| Kargo | $8.00 | %2.3 |
| Kalite | $3.00 | %0.9 |
| Gider | $8.00 | %2.3 |
| **Toplam** | **$339.50** | **%100** |

## Bakım ve Kalibrasyon

| Ekipman | Bakım Periyodu | Kalibrasyon |
|---------|----------------|-------------|
| Pick & Place | Günlük | Aylık |
| Reflow Oven | Haftalık | Aylık |
| ICT Tester | Günlük | Haftalık |
| AOI | Günlük | Aylık |
| Hot Air Station | Aylık | 3 Ayda bir |

## Durum: Implementasyon

Üretim hattı ekipmanları belirlenmiş ve tedarik süreçleri başlatılmıştır. İlk prototip üretimi için gerekli araçlar temin edilmiştir.
