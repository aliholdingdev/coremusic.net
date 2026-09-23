---
title: "Konnektör Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Konnektör Listesi

## Genel Bakış

COREMUSIC donanımında kullanılan konnektörler, profesyonel ses, tüketici elektroniği ve veri iletişimi için seçilmiştir. Her konnektör tipi belirli uygulama alanları için optimize edilmiştir.

## Bileşen Listesi

### Profesyonel Ses Konnektörleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| XLR Input | XLR 3-pin Male | Panel Mount | 4 | $2.80 | Neutrik |
| XLR Output | XLR 3-pin Female | Panel Mount | 4 | $2.90 | Neutrik |
| XLR PCB | XLR 3-pin | PCB Mount | 4 | $1.80 | Neutrik |
| TRS 6.35mm | 6.35mm TRS | Panel Mount | 4 | $1.50 | Neutrik |
| TRS 3.5mm | 3.5mm TRS | Panel Mount | 2 | $0.80 | Switchcraft |

### Tüketici Ses Konnektörleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| RCA Input | RCA Phono | Panel Mount | 8 | $0.45 | Neutrik |
| RCA Output | RCA Phono | Panel Mount | 4 | $0.45 | Neutrik |
| RCA PCB | RCA Phono | PCB Mount | 8 | $0.25 | CUI |
| Banana Plug | 4mm Banana | Panel Mount | 8 | $0.85 | Neutrik |
| Binding Post | 4mm Binding Post | Panel Mount | 8 | $1.20 | Neutrik |

### USB Konnektörleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| USB-C | USB Type-C | PCB Mount | 2 | $0.85 | TE |
| USB-C Alt | USB Type-C | SMD | 2 | $0.65 | JAE |
| USB-B | USB Type-B | PCB Mount | 2 | $0.45 | Molex |
| USB-A | USB Type-A | PCB Mount | 2 | $0.35 | Molex |

### Güç Konnektörleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| IEC C14 | C14 Inlet | Panel Mount | 1 | $1.50 | Schurter |
| IEC C13 | C13 Outlet | Panel Mount | 1 | $1.20 | Schurter |
| DC Barrel | 5.5x2.1mm | Panel Mount | 2 | $0.35 | CUI |
| Screw Terminal | 5.08mm | PCB Mount | 4 | $0.28 | Phoenix |
| Molex | Molex 4-pin | PCB Mount | 2 | $0.85 | Molex |

### Veri Konnektörleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Ethernet | RJ45 Cat6 | PCB Mount | 1 | $0.85 | TE |
| Ethernet MagJack | RJ45 w/ MagJack | PCB Mount | 1 | $1.50 | Pulse |
| HDMI | HDMI Type-A | Panel Mount | 1 | $2.20 | Molex |
| Optical TOSLINK | TOSLINK | Panel Mount | 1 | $2.50 | Toshiba |
| SPI Header | 2x5 Pin | PCB Header | 2 | $0.35 | Sullins |
| JTAG | 2x10 Pin | PCB Header | 1 | $0.45 | Sullins |

### Mekanik Konnektörler

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Standoff | M3x10mm | Brass | 8 | $0.08 | Essentra |
| Standoff | M3x6mm | Brass | 8 | $0.06 | Essentra |
| PCB Guide | M3x20mm | Nylon | 4 | $0.12 | Essentra |
| Switch | SPDT Toggle | Panel Mount | 2 | $0.85 | C&K |
| Push Button | Momentary | Panel Mount | 4 | $0.45 | C&K |

## Seçim Kriterleri

### XLR Konnektör Karşılaştırması

| Kriter | Neutrik NC3 | Switchcraft 110R | CUI SJ-3 |
|--------|-------------|------------------|----------|
| Pin Sayısı | 3 | 3 | 3 |
| Akım | 10A | 10A | 5A |
| Contact | Gold | Nickel | Nickel |
| Ömür | 5000 cycle | 5000 cycle | 1000 cycle |
| Maliyet | $2.80 | $1.80 | $0.80 |
| Uygulama | Profesyonel | Profesyonel | Tüketici |

### USB-C Konnektör Karşılaştırması

| Kriter | TE 2171730-1 | JAE DX07S024JJ2R1500 |
|--------|--------------|----------------------|
| Tip | USB-C | USB-C |
| Akım | 5A | 5A |
| Speed | USB 3.1 | USB 3.1 |
| Mount | Through-hole | SMD |
| Maliyet | $0.85 | $0.65 |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Serisi | Stok | Min Sipariş |
|-----------|-------------|------|-------------|
| Mouser | Neutrik NC3 serisi | Stokta | 1 |
| DigiKey | Switchcraft 110R | Stokta | 1 |
| Neutrik | XLR serisi | Stokta | 1 |

## Bağımlılıklar

- PCB tasarımı (K7) - Konnektör footprint'leri
- Kasa tasarımı (K6) - Panel delikleri
- Kablo tasarımı (K9) - Kablo demetleri

## Durum: Implementasyon

Tüm konnektörler选型 tamamlanmış ve mekanik uyumluluk doğrulanmıştır.
