---
title: "Kondansatör Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Kondansatör Listesi

## Genel Bakış

COREMUSIC devresindeki kondansatörler, elektrolitik, seramik ve film tiplerinden oluşmaktadır. Her tip belirli uygulama alanları için seçilmiştir: güç filtreleme, sinyal yolundersizasyon ve bypass uygulamaları.

## Bileşen Listesi

### Elektrolitik Kondansatörler

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Elektrolitik | 100uF/63V | 10x16mm | 8 | $0.35 | Nichicon |
| Elektrolitik | 220uF/50V | 10x16mm | 8 | $0.45 | Nichicon |
| Elektrolitik | 470uF/35V | 10x16mm | 8 | $0.55 | Nichicon |
| Elektrolitik | 1000uF/35V | 13x20mm | 8 | $0.85 | Panasonic |
| Elektrolitik | 2200uF/25V | 13x20mm | 4 | $1.20 | Panasonic |
| Elektrolitik | 10uF/25V | 5x11mm | 16 | $0.12 | Nichicon |
| Elektrolitik Low ESR | 100uF/25V | 6x11mm | 8 | $0.25 | Panasonic |

### Seramik Kondansatörler (MLCC)

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| X7R | 100pF | 0402 | 16 | $0.005 | TDK |
| X7R | 1nF | 0402 | 16 | $0.005 | TDK |
| X7R | 10nF | 0603 | 16 | $0.006 | Murata |
| X7R | 100nF | 0603 | 32 | $0.006 | Murata |
| X7R | 1uF | 0805 | 16 | $0.01 | TDK |
| X7R | 4.7uF | 0805 | 8 | $0.02 | Murata |
| X7R | 10uF | 1206 | 8 | $0.04 | TDK |
| X5R | 22uF | 1206 | 4 | $0.06 | Murata |
| C0G/NP0 | 10pF | 0402 | 8 | $0.005 | TDK |
| C0G/NP0 | 100pF | 0402 | 8 | $0.005 | TDK |

### Film Kondansatörler

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Polyester | 1nF | Box | 4 | $0.15 | Wima |
| Polyester | 10nF | Box | 4 | $0.18 | Wima |
| Polyester | 100nF | Box | 4 | $0.22 | Wima |
| Polypropylene | 1nF | Box | 2 | $0.25 | Wima |
| Polypropylene | 10nF | Box | 2 | $0.35 | Wima |
| Polypropylene | 100nF | Box | 2 | $0.50 | Wima |
| MKP | 2.2uF/275V | Box | 4 | $1.20 | Wima |

## Seçim Kriterleri

### Kondansatör Tipi Karşılaştırması

| Kriter | Elektrolitik | MLCC X7R | MLCC C0G | Film |
|--------|--------------|----------|----------|------|
| Yoğunluk | Yüksek | Çok Yüksek | Yüksek | Düşük |
| ESR | Yüksek | Düşük | Düşük | Çok Düşük |
| Gürültü | Yüksek | Orta | Düşük | Düşük |
| DC Bias | Var | Var | Yok | Yok |
| Yaşlanma | Yüksek | Düşük | Çok Düşük | Çok Düşük |
| Maliyet | Düşük | Çok Düşük | Orta | Yüksek |
| Uygulama | Güç filtresi | Bypass | Sinyal | Sinyal |

### Package Seçimi

| Package | Kapasite | Güç | Uygulama |
|---------|----------|-----|----------|
| 0402 | ≤10nF | - | Yüksek yoğunluk |
| 0603 | ≤100nF | - | Genel amaç |
| 0805 | ≤10uF | - | Güç bypass |
| 1206 | ≤22uF | - | Güç filtresi |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Serisi | Stok | Min Sipariş |
|-----------|-------------|------|-------------|
| Mouser | Nichicon UHE | Stokta | 10 |
| DigiKey | Panasonic FM | Stokta | 10 |
| TDK | MLCC Serisi | Stokta | 100 |
| Murata | GRM Serisi | Stokta | 100 |

## Bağımlılıklar

- Güç kaynağı (K3) - Büyük kapasiteli filtre kondansatörleri
- Amplifikatör devresi (K4) - Sinyal yolu kondansatörleri
- Kararlılık (K5) - Bypass kondansatörleri

## Durum: Implementasyon

Tüm kondansatörler选型 tamamlanmış ve stok doğrulaması yapılmıştır.
