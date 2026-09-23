---
title: "Maliyet Tahmini"
layer: K20
category: "BOM & Üretim"
date: 2026-09-20
---

# Maliyet Tahmini

## Genel Bakış

Bu doküman COREMUSIC projesinin BOM maliyet analizini ve üretim maliyet tahminlerini içerir. Analiz, birim başı maliyet, toplam proje maliyeti ve farklı üretim hacimlerine göre maliyet optimizasyonunu kapsar.

## BOM Maliyet Özeti

### Bileşen Kategorisi Bazında Maliyet

| Kategori | Adet | Birim Fiyat (USD) | Toplam (USD) | Yüzde |
|----------|------|-------------------|--------------|-------|
| Transistör | 24 | $0.85 | $20.40 | %8.2 |
| Diyot | 18 | $0.05 | $0.90 | %0.4 |
| Direnç | 156 | $0.005 | $0.78 | %0.3 |
| Kondansatör | 142 | $0.15 | $21.30 | %8.6 |
| Bobin | 12 | $1.80 | $21.60 | %8.7 |
| IC | 16 | $3.50 | $56.00 | %22.6 |
| Konnektör | 28 | $1.20 | $33.60 | %13.6 |
| PCB | 1 | $45.00 | $45.00 | %18.2 |
| Mekanik | 1 | $25.00 | $25.00 | %10.1 |
| Kablo ve Trafo | 1 | $22.00 | $22.00 | %8.9 |
| **Toplam** | - | - | **$246.58** | **%100** |

### Üretim Hacmi Bazında Maliyet

| Hacim | BOM/Adet | Üretim/Adet | Toplam/Adet | Toplam Proje |
|-------|----------|-------------|-------------|--------------|
| Prototip (10 adet) | $246.58 | $85.00 | $331.58 | $3,315.80 |
| Küçük Seri (100 adet) | $220.00 | $45.00 | $265.00 | $26,500.00 |
| Orta Seri (500 adet) | $195.00 | $30.00 | $225.00 | $112,500.00 |
| Büyük Seri (1000 adet) | $175.00 | $22.00 | $197.00 | $197,000.00 |
| Yüksek Hacim (5000 adet) | $155.00 | $15.00 | $170.00 | $850,000.00 |

## Detaylı Maliyet Analizi

### PCB Maliyeti

| Parametre | Prototip | Seri Üretim |
|-----------|----------|-------------|
| Katman Sayısı | 4 | 4 |
| Boyut | 200x150mm | 200x150mm |
| Malzeme | FR-4 TG150 | FR-4 TG170 |
| Kalınlık | 1.6mm | 1.6mm |
| HASL | Evet | Lead-free |
|Mesh | 1oz | 1oz |
| Fiyat/Adet | $45.00 | $8.50 |

### Üretim İşçilik Maliyeti

| İşlem | Süre | Maliyet/Adet |
|-------|------|--------------|
| Pick & Place | 15 dk | $8.00 |
| Reflow Lehimleme | 10 dk | $5.00 |
| Through-hole | 20 dk | $12.00 |
| Test | 15 dk | $8.00 |
| Montaj | 20 dk | $10.00 |
| Paketleme | 5 dk | $2.00 |
| **Toplam** | **85 dk** | **$45.00** |

### Test ve Kalite Maliyeti

| Test | Maliyet/Adet |
|------|--------------|
| ICT (In-Circuit Test) | $5.00 |
| Fonksiyonel Test | $8.00 |
| Ses Kalitesi Testi | $12.00 |
| Güvenlik Testi | $5.00 |
| Yangın Testi | $3.00 |
| **Toplam** | **$33.00** |

## Maliyet Optimizasyonu

### Tasarım Optimizasyonları

| Optimizasyon | Tasarruf | Etki |
|--------------|----------|------|
| Direnç 0805 → 0603 | $0.10/adet | Montaj hızı |
| Kondansatör MLCC sole sourcing | $2.50/adet | Tedarik kolaylığı |
| IC alternatif seçim | $5.00/adet | Performans |
| Bobin toroid → sendust | $1.50/adet | Ses kalitesi |
| Konnektör yerli tedarik | $3.00/adet | Teslimat |

### Toplam Tasarruf Potansiyeli

| Optimizasyon | Prototip | Seri |
|--------------|----------|------|
| B bileşen optimizasyonu | $12.00 | $15.00 |
| C montaj optimizasyonu | $8.00 | $10.00 |
| Test optimizasyonu | $5.00 | $8.00 |
| **Toplam Tasarruf** | **$25.00/adet** | **$33.00/adet** |

## Risk Maliyetleri

| Risk | Olasılık | Etki | Maliyet |
|------|----------|------|---------|
| Tedarik gecikmesi | %20 | Orta | $15.00/adet |
| Kalite sorunu | %10 | Yüksek | $25.00/adet |
| Tasarım hatası | %15 | Yüksek | $50.00/adet |
| Döviz kuru | %30 | Düşük | $5.00/adet |

## ROI Analizi

| Parametre | Değer |
|-----------|-------|
| Toplam Yatırım (1000 adet) | $197,000 |
| Birim Satış Fiyatı | $350.00 |
| Birim Kâr | $153.00 |
| Toplam Kâr | $153,000 |
| ROI | %77.7 |
| Başa Baş Noktası | 636 adet |

## Durum: Implementasyon

Maliyet analizi tamamlanmış ve onaylanmıştır. Üretim hacmine göre fiyatlandırma stratejisi belirlenmiştir.
