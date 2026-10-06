---
title: "Heat Pipe Tasarımı"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# Heat Pipe Tasarımı

## Genel Bakış

Heat pipe (ısı borusu), faz değiştirme prensibiyle çalışan yüksek etkinlikli pasif ısı iletim cihazıdır. COREMUSIC platformunda, kompakt tasarımlarda ve yüksek güç yoğunluklu bileşenler için ısı yönetimi çözümü olarak değerlendirilir. Heat pipe, bakır gövde içinde suyun buharlaşması ve yoğunlaşması sayesinde çok düşük termal direnç ile ısı transferi sağlar.

## Termal Hesaplamalar

### Heat Pipe Temel Prensibi

```
Evaporatör Bölgesi: Su → Buhar (ısınma, faz değiştirme)
Kondenser Bölgesi: Buhar → Su (soğuma, faz değiştirme)
Kapiler Akış: Sinterlenmiş toz → Buhar dönüşümü

Termal Direnç:
Rθ_hp = L_eff / (k_eff × A_c)

L_eff: Etkin uzunluk (mm)
k_eff: Etkin termal iletim (W/m·K) ~10,000-200,000
A_c: Kesit alanı (mm²)
```

### Heat Pipe Performansı

```
Maksimum Isı Taşıma Kapasitesi:

Q_max = (ρ_l × h_fg × A_c × K_eff) / L_eff

ρ_l: Sıvı yoğunluğu (kg/m³)
h_fg: Gazlaşma ısısı (2257 kJ/kg su için)
A_c: Kesit alanı (m²)
K_eff: Etkin iletim (W/m·K)
L_eff: Etkin uzunluk (m)

Örnek: 6mm çap, 100mm uzunluk
A_c = π × (0.003)² = 2.83 × 10⁻⁵ m²
Q_max = (958 × 2.257×10⁶ × 2.83×10⁻⁵ × 150000) / 0.1
Q_max = ~90W (teorik maksimum)
```

### Termal Direnç Karşılaştırması

```
Bakır Çubuk: Rθ = L / (k × A) = 0.1 / (400 × 2.83×10⁻⁵) = 8.8°C/W
Heat Pipe: Rθ = 0.1 / (150000 × 2.83×10⁻⁵) = 0.024°C/W

Heat Pipe ~ 370x daha etkili!
```

## Teknik Spesifikasyonlar

### Seçilen Heat Pipe: Aavid Thermalright Heat Pipe

| Özellik | Değer |
|---------|-------|
| Model | Thermalright HR-09 |
| Çap | 6 mm |
| Uzunluk | 100 mm |
| Malzeme | Bakır gövde, su buharı |
| Kullanım Sıcaklığı | -40°C ile +200°C |
| Maks. Isı Taşıma | 40W (yatay), 25W (dikey) |
| Termal Direnç | 0.02-0.05°C/W |
| Bükülme Yarıçapı | ≥ 20mm |
| Ağırlık | 15g |
| Ömür | > 100,000 saat |

### Heat Pipe Çap Seçimi

| Çap | Q_max (Yatay) | Q_max (Dikey) | Uygulama |
|-----|---------------|---------------|----------|
| 3mm | 15W | 8W | Düşük güç |
| 4mm | 25W | 15W | Orta güç |
| 6mm | 40W | 25W | Yüksek güç |
| 8mm | 60W | 35W | Çok yüksek güç |
| **6mm** | **✓** | **✓** | **COREMUSIC seçimi** |

### Heat Pipe Konfigürasyonları

| Konfigürasyon | Avantaj | Dezavantaj |
|---------------|---------|------------|
| Düz (straight) | Basit, düşük maliyet | Sınırlı esneklik |
| Bükülmüş (bent) | Esnek yerleşim | Bükülme yarıçapı sınırlı |
| Flattened | Düz yüzey teması | Kapasite azalması |
| Multi-pipe | Yüksek kapasite | Büyük boyut |

## Seçim Kriterleri

### Heat Pipe Seçim Nedenleri

1. **Yüksek Etkin İletim**: 150,000 W/m·K (bakırın 375 katı)
2. **Pasif Çalışma**: Enerji gerektirmez, bakım yok
3. **Hızlı Tepki**: Saniyeler içinde ısı transferi
4. **Uzun Ömür**: 100,000 saat+, sızıntı yok
5. **Kompakt Boyut**: 6mm çap, yer kaplamaz

### Isı Pipe Sayısı Hesabı

```
CPU Gücü: 15W
Güvenli Faktör: 1.5
Gerekli Kapasite: 15 × 1.5 = 22.5W

Heat Pipe Kapasitesi: 40W (yatay)
Gerekli Heat Pipe Sayısı: 22.5 / 40 = 0.56 → 1 adet yeterli
```

### Uygulama Senaryoları

| Senaryo | Heat Pipe | Hedef |
|---------|-----------|-------|
| CPU soğutma | 1 × 6mm | 15W → heatsink |
| Amplifikatör | 2 × 6mm | 25W → kasa |
| Kompakt tasarım | 1 × 4mm | 10W → kasa |

## Bağımlılıklar

### Girişler
- **K01 Donanım**: Bileşen yerleşim planı ve boşluklar
- **K17 Güç Yönetimi**: Bileşen güç tüketimi
- **K18 Heatsink**: Heatsink taban tasarımı

### Çıktılar
- **K18 Termal Resistance**: Heat pipe termal direnç hesapları
- **K18 Enclosure**: Kasa içi yerleştirme planı
- **K18 Termal Simülasyon**: CFD modeli için heat pipe geometrisi

### Entegrasyon Noktaları
```
CPU (Isı Kaynağı)
     │
     ▼
┌──────────────────┐
│  Heat Pipe       │  Evaporatör bölge
│  (6mm, bakır)    │  Sinterlenmiş toz
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Heat Pipe       │  Kondenser bölge
│  (soğuma)        │  Su yoğunlaşması
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Heatsink        │  Fischer SK53
│  (Fischer)       │  Pasif/aktif soğutma
└──────────────────┘
```

## Uygulama Notları

### Heat Pipe Montaj Prosedürü

```
1. Heat Pipe'ı heatsink tabanı ile entegre et
   - Mekanik pres veya lehimleme
   - Temas basıncı: 10-20N
2. Evaporatör bölgesini CPU'ya hizala
   - Termal ped kullanımı
   - Merkezi hizalama
3. Kondenser bölgesini heatsink'e bağla
   - Direkt temas veya macun
4. Sıcaklık testi
   - 15W yük uygula
   - Junction sıcaklığı ölç
```

### Heat Pipe Bakımı

- **Periyot**: 36 ayda bir
- **Kontrol**: Sızıntı, korozyon, hasar
- **Değişim**: Heat pipe çatlamasında veya sızıntında
- **Test**: Termal kamera ile sıcaklık dağılımı

### Yaygın Hatalar

| Hata | Sonuç | Önleme |
|------|-------|--------|
| Aşırı bükme | Kırılma, sızıntı | Bükülme yarıçapına dikkat |
| Yanlış montaj | Düşük termal temas | Doğru hizalama |
| Hasar (çökme) | Kapasite düşüşü | Dikkatli taşıma |
| Korozyon | Ömür kısalması | Pasif koruma |

## Durum: Implementasyon

Heat pipe tasarımı, COREMUSIC platformunda kompakt ve yüksek güç yoğunluklu uygulamalar için onaylanmıştır. 6mm çapında bakır heat pipe, 40W maksimum kapasite ile CPU ve amplifikatör soğutmasında kullanılacaktır. Heat pipe, Fischer SK53 heatsink ile entegre edilecek ve pasif/aktif soğutma kombinasyonu sağlanacaktır.
