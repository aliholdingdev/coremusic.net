---
title: "Bobin Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Bobin Listesi

## Genel Bakış

COREMUSIC devresindeki bobinler, çıkış filtresi, güç kaynağı choke ve EMI filtreleme uygulamaları için seçilmiştir. Class D amplifikatör çıkış filtresi, en kritik bobin uygulamasıdır ve ses kalitesini doğrudan etkiler.

## Bileşen Listesi

### Class D Çıkış Filtresi Bobinleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Toroid Core | 10uH/10A | 25x15mm | 4 | $3.50 | Bourns |
| Toroid Core | 22uH/6A | 20x12mm | 4 | $2.80 | Bourns |
| Send Core | 10uH/8A | 20x10mm | 4 | $2.20 | Coilcraft |
| Iron Powder | 4.7uH/15A | 30x20mm | 2 | $4.50 | Micrometals |

### Güç Choke Bobinleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| SMD Choke | 100uH/1A | 12x12mm | 4 | $0.85 | Coilcraft |
| SMD Choke | 47uH/2A | 12x12mm | 4 | $0.90 | Bourns |
| THT Choke | 10mH/0.5A | 20x15mm | 2 | $1.20 | Murata |
| Common Mode | 10mH | 22x15mm | 2 | $1.80 | TDK |

### EMI Filtre Bobinleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Ferrite Bead | 120R@100MHz | 0805 | 16 | $0.02 | TDK |
| Ferrite Bead | 600R@100MHz | 0805 | 8 | $0.03 | Murata |
| Ferrite Bead | 1000R@100MHz | 0603 | 8 | $0.025 | TDK |
| Common Mode Choke | 5mH | SMD | 4 | $0.15 | Wurth |

### Sinyal Yolu Bobinleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Air Core | 1uH | Radial | 4 | $0.15 | Coilcraft |
| Ferrite Core | 10uH | Axial | 4 | $0.20 | Bourns |
| Audio Choke | 100mH | THT | 2 | $3.50 | Lundahl |

## Seçim Kriterleri

### Class D Çıkış Filtresi Bobin Karşılaştırması

| Kriter | Toroid 10uH | Send 10uH | Iron Powder 4.7uH |
|--------|-------------|-----------|-------------------|
| İndüktans | 10uH | 10uH | 4.7uH |
| Maks Akım | 10A | 8A | 15A |
| DC Resistance | 20mΩ | 25mΩ | 15mΩ |
| Satürasyon | İyi | Orta | Yüksek |
| Core Loss | Düşük | Orta | Yüksek |
| Maliyet | $3.50 | $2.20 | $4.50 |
| Ses Kalitesi | Yüksek | İyi | Orta |

### Bobin Malzemesi Karşılaştırması

| Kriter | Toroid | Sendust | Iron Powder | Ferrite |
|--------|--------|---------|-------------|---------|
| Permeability | 60-90 | 60-90 | 10-75 | 200-10000 |
| Core Loss | Düşük | Orta | Yüksek | Yüksek |
| Satürasyon | 1.2T | 1.0T | 1.4T | 0.3-0.5T |
| Boyut | Büyük | Orta | Orta | Küçük |
| Maliyet | Yüksek | Orta | Düşük | Düşük |
| Uygulama | Audio filtresi | Güç choke | EMI | Data hattı |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Serisi | Stok | Min Sipariş |
|-----------|-------------|------|-------------|
| Mouser | Bourns SRP | Stokta | 1 |
| DigiKey | Coilcraft SER | Stokta | 1 |
| Coilcraft | XAL Serisi | Stokta | 1 |
| Micrometals | T130 Serisi | Stokta | 10 |

## Bağımlılıklar

- Class D amplifikatör (K4) - Çıkış filtresi bobinleri
- Güç kaynağı (K3) - Choke bobinleri
- EMI koruma (K5) - EMI filtre bobinleri

## Durum: Implementasyon

Class D çıkış filtresi bobinleri prototip aşamasında test edilmiştir. Ses kalitesi ölçümleri yapılmaktadır.
