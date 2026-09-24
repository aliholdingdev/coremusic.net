---
title: "IC Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# IC Listesi

## Genel Bakış

COREMUSIC devresindeki entegre devreler, güç yönetimi, sinyal işleme ve amplifikasyon fonksiyonlarını gerçekleştirir. Her IC için spesifik parametreler ve alternatif tedarikçiler belirlenmiştir.

## Bileşen Listesi

### Güç Yönetimi IC'leri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Step-Down | LM5122 | QFN-20 | 2 | $4.50 | TI |
| Step-Down | TPS5430 | SOIC-8 | 2 | $1.85 | TI |
| LDO | LM1117-3.3 | SOT-223 | 4 | $0.45 | TI |
| LDO | LM1117-5.0 | SOT-223 | 4 | $0.45 | TI |
| LDO Low Noise | LP5907-3.3 | SOT-23 | 4 | $0.85 | TI |
| PMIC | TPS65988 | QFN-48 | 1 | $6.50 | TI |

### Sinyal İşleme IC'leri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Dual Op-Amp | NE5532 | DIP-8 | 4 | $0.65 | TI |
| Dual Op-Amp | OPA2134 | DIP-8 | 4 | $2.80 | TI |
| Quad Op-Amp | LM4562 | DIP-8 | 2 | $3.50 | TI |
| Low Noise Op-Amp | OPA2277 | SOIC-8 | 4 | $2.20 | TI |
| Audio DAC | PCM1794A | TSSOP-20 | 2 | $8.50 | TI |
| Audio DAC Alt | ES9038Q2M | QFN-28 | 2 | $12.00 | ESS |
| ADC | PCM4222 | TSSOP-20 | 2 | $7.50 | TI |

### Amplifikatör IC'leri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Class D Amp | TPA3255 | TQFP-64 | 2 | $5.50 | TI |
| Class D Amp Alt | TAS5630 | QFN-64 | 2 | $6.80 | TI |
| Headphone Amp | TPA6120 | SOIC-14 | 2 | $3.20 | TI |
| Pre-Amp | OPA2134 | DIP-8 | 2 | $2.80 | TI |

### Mikrodenetleyici ve Haberleşme

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| MCU | STM32F407 | LQFP-100 | 2 | $8.50 | ST |
| WiFi/BT | ESP32-S3 | QFN-48 | 1 | $3.20 | Espressif |
| USB Bridge | FT232R | SSOP-28 | 1 | $2.80 | FTDI |
| Ethernet | W5500 | QFN-48 | 1 | $4.50 | Wiznet |
| Clock Gen | Si5351 | MSOP-10 | 2 | $1.20 | Skyworks |

## Seçim Kriterleri

### Op-Amp Karşılaştırması

| Kriter | NE5532 | OPA2134 | LM4562 |
|--------|--------|---------|--------|
| Input Noise | 5nV/√Hz | 8nV/√Hz | 2.7nV/√Hz |
| THD+N | 0.002% | 0.00008% | 0.000015% |
| Gain Bandwidth | 10MHz | 8MHz | 55MHz |
| Slew Rate | 9V/µs | 20V/µs | 20V/µs |
| Supply | ±2.5V-±18V | ±2.5V-±18V | ±2.5V-±17V |
| Fiyat | $0.65 | $2.80 | $3.50 |
| Uygulama | Genel | Audio | Yüksek Performans |

### Class D Amplifikatör Karşılaştırması

| Kriter | TPA3255 | TAS5630 |
|--------|---------|---------|
| Güç (4Ω) | 300Wx2 | 300Wx2 |
| Güç (8Ω) | 175Wx2 | 175Wx2 |
| THD+N | 0.005% | 0.006% |
| Verimlilikat | 93% | 90% |
| Fiyat | $5.50 | $6.80 |

### DAC Karşılaştırması

| Kriter | PCM1794A | ES9038Q2M |
|--------|----------|-----------|
| Çözünürlük | 24-bit | 32-bit |
| Örnekleme | 192kHz | 768kHz |
| Dynamic Range | 132dB | 140dB |
| THD+N | -116dB | -124dB |
| Fiyat | $8.50 | $12.00 |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Kodu | Stok | Min Sipariş |
|-----------|-----------|------|-------------|
| Mouser | 595-TPA3255D2DCA | Stokta | 1 |
| DigiKey | 296-42986-1-ND | Stokta | 1 |
| TI Store | TPA3255D2DCA | Stokta | 1 |

## Bağımlılıklar

- Güç kaynağı (K3) - Besleme gerilimleri
- Sinyal yolu (K4) - Girdi/çıktı bağlantıları
- Konnektörler (K8) - Ses girişi/çıkışı

## Durum: Implementasyon

Tüm IC'lerin seçimi tamamlanmış ve ilk prototiplerde test edilmiştir.
