---
title: "Diyot Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Diyot Listesi

## Genel Bakış

COREMUSIC devresinde kullanılan diyotlar, sinyal yönlendirme, koruma, voltaj regülasyonu ve güç düzeltme amaçlıdır. Her diyot tipi için spesifik parametreler ve alternatif tedarikçiler belirlenmiştir.

## Bileşen Listesi

### Sinyal Diyotları

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Sinyal | 1N4148W | SOD-123 | 24 | $0.012 | Vishay |
| Sinyal THT | 1N4148 | DO-35 | 8 | $0.008 | NXP |
| Sinyal Alt | BAV99 | SOT-23 | 12 | $0.015 | ON Semi |

### Güç Diyotları

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Rectifier | 1N4007 | DO-41 | 8 | $0.02 | Vishay |
| Rectifier | 1N5408 | DO-201 | 4 | $0.04 | ON Semi |
| Fast Recovery | UF4007 | DO-41 | 4 | $0.06 | Vishay |

### Schottky Diyotları

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Schottky | 1N5819 | DO-41 | 8 | $0.04 | ON Semi |
| Schottky | SS34 | SMA | 6 | $0.08 | Vishay |
| Dual Schottky | BAT54S | SOT-23 | 8 | $0.03 | NXP |
| High Power | MBR20100CT | TO-220 | 4 | $0.85 | Vishay |

### Zener Diyotları

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| 5.1V Zener | BZT52C5V1 | SOD-123 | 8 | $0.02 | Vishay |
| 3.3V Zener | BZT52C3V3 | SOD-123 | 4 | $0.02 | NXP |
| 12V Zener | BZT52C12 | SOD-123 | 4 | $0.02 | Rohm |
| 15V Zener | BZX84C15 | SOT-23 | 4 | $0.03 | ON Semi |

### LED Diyotları

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Status LED | Red 0805 | 0805 | 8 | $0.01 | Kingbright |
| Status LED | Green 0805 | 0805 | 8 | $0.01 | Kingbright |
| Status LED | Blue 0805 | 0805 | 4 | $0.015 | Kingbright |

## Seçim Kriterleri

### 1N4148 vs Alternatifleri

| Kriter | 1N4148W | BAV99 | BAT54S |
|--------|---------|-------|--------|
| VRRM | 100V | 75V | 30V |
| IF | 250mA | 200mA | 200mA |
| trr | 4ns | 4ns | 5ns |
| VF | 1V | 0.75V | 0.32V |
| Paket | SOD-123 | SOT-23 | SOT-23 |
| Fiyat | $0.012 | $0.015 | $0.03 |

### 1N4007 vs Schottky Karşılaştırması

| Kriter | 1N4007 | 1N5819 | MBR20100CT |
|--------|--------|--------|------------|
| VRRM | 1000V | 40V | 100V |
| IF | 1A | 1A | 2x10A |
| VF | 1.1V | 0.45V | 0.85V |
| Uygulama | AC rectifier | DC-DC | Kryo |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Kodu | Stok | Min Sipariş |
|-----------|-----------|------|-------------|
| Mouser | 621-1N4148W | Stokta | 100 |
| DigiKey | 1N4148WFSCT-ND | Stokta | 100 |
| RS Components | 225-1738 | Stokta | 100 |
| LCSC | C81598 | Stokta | 1000 |

## Bağımlılıklar

- Güç kaynağı (K3) - Rectifier diyotlar için voltaj
- Sinyal yolu (K4) - Sinyal diyotları için sinyal akışı
- Koruma devresi (K9) - TVS diyotları için koruma

## Durum: Implementasyon

Tüm diyotlar seçilmiş ve stokta doğrulanmıştır. SMD ve THT varyantları mevcuttur.
