---
title: "Direnç Listesi"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Direnç Listesi

## Genel Bakış

COREMUSIC devresindeki dirençler, metal film ve kalın film teknolojisi ile üretilmiştir. Tüm dirençler %1 tolerans ve düşük gürültü özelliğine sahiptir. SMD ve THT varyantları ile komple bir BOM listesi sunulmuştur.

## Bileşen Listesi

### 0805 SMD Dirençler

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Metal Film | 100R | 0805 | 16 | $0.003 | Yageo |
| Metal Film | 220R | 0805 | 12 | $0.003 | Yageo |
| Metal Film | 470R | 0805 | 8 | $0.003 | Yageo |
| Metal Film | 1K | 0805 | 24 | $0.003 | Yageo |
| Metal Film | 2.2K | 0805 | 16 | $0.003 | Yageo |
| Metal Film | 4.7K | 0805 | 12 | $0.003 | Yageo |
| Metal Film | 10K | 0805 | 32 | $0.003 | Yageo |
| Metal Film | 22K | 0805 | 8 | $0.003 | Yageo |
| Metal Film | 47K | 0805 | 4 | $0.003 | Yageo |
| Metal Film | 100K | 0805 | 4 | $0.003 | Yageo |

### 1206 SMD Dirençler (Güç)

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Thick Film | 0.1R | 1206 | 4 | $0.008 | Yageo |
| Thick Film | 0.22R | 1206 | 4 | $0.008 | Vishay |
| Thick Film | 0.47R | 1206 | 4 | $0.008 | Yageo |
| Thick Film | 1R | 1206 | 8 | $0.008 | Yageo |
| Thick Film | 2.2R | 1206 | 4 | $0.008 | Vishay |
| Thick Film | 10R | 1206 | 8 | $0.008 | Yageo |
| Thick Film | 22R | 1206 | 4 | $0.008 | Yageo |

### THT Dirençler (Power)

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Metal Film 1W | 0.47R | Axial | 4 | $0.04 | Vishay |
| Metal Film 1W | 1R | Axial | 4 | $0.04 | Vishay |
| Metal Film 2W | 0.22R | Axial | 2 | $0.08 | Vishay |
| Wirewound 5W | 10R | Cement | 2 | $0.25 | TE |

### Karşılaştırma Dirençleri

| Tip | Değer | Package | Adet | Fiyat (USD) | Tedarikçi |
|-----|-------|---------|------|-------------|-----------|
| Precision | 10K 0.1% | 0805 | 8 | $0.02 | Vishay |
| Precision | 47K 0.1% | 0805 | 4 | $0.02 | Vishay |
| Precision | 100K 0.1% | 0805 | 4 | $0.02 | Vishay |
| Trim | 10K | SOT-23 | 8 | $0.05 | Bourns |

## Seçim Kriterleri

### Metal Film vs Thick Film

| Kriter | Metal Film | Thick Film |
|--------|------------|------------|
| Tolerans | %0.1-%1 | %1-%5 |
| TCR | ±50ppm/°C | ±200ppm/°C |
| Gürültü | Düşük | Orta |
| Güç | 0.125W-0.5W | 0.1W-1W |
| Fiyat | $0.003 | $0.002 |
| Uygulama | Sinyal yolu | Güç yolu |

### SMD vs THT Seçimi

| Kriter | SMD (0805) | THT |
|--------|------------|-----|
| Boyut | 2.0x1.25mm | 6.5x2.5mm |
| Montaj | Otomatik | Manuel |
| Güç | 0.125W | 0.25W-5W |
| Yoğunluk | Yüksek | Düşük |
| Bakım | Zor | Kolay |

## Tedarikçi Bilgisi

| Tedarikçi | Ürün Serisi | Stok | Min Sipariş |
|-----------|-------------|------|-------------|
| Mouser | RC0805 serisi | Stokta | 100 |
| DigiKey | CRCW0805 serisi | Stokta | 100 |
| Yageo | RC serisi | Stokta | 5000 |
| LCSC | CR serisi | Stokta | 5000 |

## Bağımlılıklar

- Amplifikatör devresi (K4) - Geri besleme ve kazanç dirençleri
- Güç kaynağı (K3) - Voltaj bölücü dirençler
- Filtre devresi (K4) - RC filtre dirençleri

## Durum: Implementasyon

Tüm dirençler standardize edilmiş ve toplu alım için fiyatlar negotiated edilmiştir.
