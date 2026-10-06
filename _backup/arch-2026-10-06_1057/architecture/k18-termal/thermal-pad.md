---
title: "Termal Ped Seçimi ve Spesifikasyonları"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# Termal Ped Seçimi ve Spesifikasyonları

## Genel Bakış

Termal ped (thermal pad), ısı kaynağı ile soğutucu arasında termal arayüz malzemesi (TIM) olarak görev yapar. COREMUSIC platformunda, CPU, GPU ve amplifikatör bileşenleri için seçilen termal pedler, yüksek iletim katsayısı ve düşük thermal direnç ile optimizasyon sağlanır. Ped seçimi, heatsink performansını doğrudan etkileyen kritik bir karardır.

## Termal Hesaplamalar

### Temel Formül

```
Rθ_thermal_pad = L / (k × A)

Rθ: Thermal direnç (°C/W)
L: Ped kalınlığı (m)
k: Termal iletim katsayısı (W/m·K)
A: Temas alanı (m²)
```

### Pratik Hesaplama

```
Ped Kalınlığı: 0.5mm = 0.0005m
Termal İletim: 6 W/m·K
Temas Alanı: 25mm × 25mm = 0.000625 m²

Rθ_pad = 0.0005 / (6 × 0.000625)
Rθ_pad = 0.0005 / 0.00375
Rθ_pad = 0.133°C/W
```

### Sıcaklık Düşüşü

```
CPU Gücü: 15W
Rθ_pad: 0.133°C/W
ΔT_pad = 15 × 0.133 = 2.0°C

Sonuç: Termal ped sadece 2°C sıcaklık düşüşü sağlar
```

## Teknik Spesifikasyonlar

### Seçilen Termal Ped: Thermal GrizzlyMinus Pad 8

| Özellik | Değer |
|---------|-------|
| Malzeme | Silicone + ceramic filler |
| Termal İletim (k) | 6 W/m·K |
| Kalınlık | 0.5mm (±0.05mm) |
| Boyut | 25mm × 25mm (CPU) |
| Sıcaklık Aralığı | -50°C ile +200°C |
| Yoğunluk | 3.2 g/cm³ |
| Sertlik | Shore 00 40-50 |
| Yonga Basınç Dayanımı | ≥ 100 N/cm² |
| Elektriksel İletkenlik | 1.2 × 10⁻¹³ S/cm ( yalıtkan) |
| Renk | Gri |

### Karşılaştırma Tablosu

| Ped Model | k (W/m·K) | Kalınlık | Sertlik | Maliyet |
|-----------|-----------|----------|---------|---------|
| Thermal GrizzlyMinus 8 | 6.0 | 0.5mm | Shore 00 45 | Yüksek |
| Noctua NT-H1 | 8.5 | 0.1mm | Sıvı (paste) | Yüksek |
| Arctic MX-6 | 6.0 | 0.2mm | Sıvı (paste) | Orta |
| generic silicone pad | 1.5 | 1.0mm | Shore 00 30 | Düşük |
| **Seçim** | **✓** | **✓** | **✓** | - |

### Ped Kalınlığı Seçimi

| Uygulama | Önerilen Kalınlık | Gerekçe |
|----------|-------------------|---------|
| CPU ↔ Heatsink | 0.5mm | Düşük boşluk, yüksek basınc |
| RAM ↔ Heatsink | 1.0mm | Yükseltilmiş bileşen |
| Güç Transistörü ↔ Kasa | 2.0mm | Büyük boşluk, düşük basınc |
| SSD ↔ Kasa | 1.5mm | Orta boşluk |

## Seçim Kriterleri

### Termal Performans
1. **Yüksek Termal İletim**: ≥ 5 W/m·K (minimum), ≥ 8 W/m·K (tercih)
2. **Düşük Thermal Direnç**: ≤ 0.2°C/W (25mm × 25mm alan için)
3. **Uzun Vadeli Kararlılık**: ≥ 10 yıl çalışma ömrü

### Mekanik Özellikler
1. **Düşük Sertlik**: Yüzey bozulması olmadan sıkıştırma
2. **Esneklik**: Yüzey pürüzlülüğünü doldurma yeteneği
3. **Basınç Dayanımı**: Montaj basıncına karşı direnç

### Çevresel Dayanım
1. **Sıcaklık Aralığı**: -50°C ile +200°C
2. **Nem Dayanımı**: %100 RH (condensation olmayan)
3. **Yaşlanma Direnci**: Termal döngü testi (1000 döngü)

### Elektriksel Özellikler
1. **Yalıtkanlık**: ≥ 10¹² Ω·cm (kısa devre önleme)
2. **Dielektrik Güçlü**: ≥ 10 kV/mm (high-voltage uygulamalar)

## Bağımlılıklar

### Girişler
- **K01 Donanım**: Bileşen yüzey boyutları ve boşlukları
- **K18 Heatsink**: Heatsink taban düzgünlüğü ve montaj basıncı
- **K17 Güç Yönetimi**: Bileşen güç tüketimi profilleri

### Çıktılar
- **K18 Thermal Resistance**: Ped thermal direnç hesapları
- **K18 Termal Simülasyon**: CFD modeli için malzeme özellikleri
- **K18 Enclosure**: Kasa içi termal harita oluşturma

### Entegrasyon Noktaları
```
CPU Yüzey ←→ Termal Ped ←→ Heatsink Tabanı
    │              │              │
    └── Rθ_case-pad ── Rθ_pad-heatsink ──┘
         (0.133°C/W)
```

## Uygulama Notları

### Montaj Prosedürü
1. CPU yüzeyini isopropyl alkol ile temizle
2. Ped kalınlığını doğrula (0.5mm ± 0.05mm)
3. Ped'i CPU üzerine merkezi olarak yerleştir
4. Heatsink'i ped üzerine hizala
5. Montaj basıncını uygula (15-25N)
6. Sızıntı olmadığını doğrula

### Bakım ve Değişim
- **Periyot**: 24 ayda bir ped yenileme
- **Belirti**: Sıcaklık artışı (>5°C yükselme)
- **Prosedür**: Heatsink kaldır → Ped temizle → Yeni ped yerleştir

### Yaygın Hatalar
| Hata | Sonuç | Önleme |
|------|-------|--------|
| Ped boyutu yanlış | Termal köprü oluşumu | Doğru boyut ölçümü |
| Yetersiz basınc | Yüksek thermal direnç | Montaj basıncı kontrolü |
| Kirlenmiş yüzey | Hava boşlukları | Temizlik prosedürü |
| Uyumsuz malzeme | Kimyasal reaksiyon | Malzeme uyumluluk tablosu |

## Durum: Implementasyon

Termal ped seçimi, COREMUSIC platformunda termal performans için kritik bir bileşendir. Thermal GrizzlyMinus Pad 8, 6 W/m·K termal iletim ile 0.5mm kalınlıkta seçilmiş ve CPU uygulaması için onaylanmıştır. Ped, heatsink montajı ile entegre edilmiş ve termal direnç hesapları doğrulanmıştır.
