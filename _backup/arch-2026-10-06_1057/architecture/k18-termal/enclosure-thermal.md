---
title: "Kasa Termal Tasarımı"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# Kasa Termal Tasarımı

## Genel Bakış

Kasa termal tasarımı, COREMUSIC cihazlarının iç mekan sıcaklık yönetimini sağlamak için havalandırma, hava akışı ve ısı yayılımı stratejilerini kapsar. Kasa, iç bileşenleri dış etkenlerden korurken aynı zamanda ısı dağılımına katkı sağlamalıdır. Doğru kasa tasarımı, termal performansı %20-30'a kadar artırabilir.

## Termal Hesaplamalar

### Kasa İçi Hava Akışı

```
Doğal Konveksiyon:
Q_natural = h × A × ΔT

h: Isı taşınım katsayısı (5-25 W/m²·K)
A: Yüzey alanı (m²)
ΔT: Kasa içi - dış sıcaklık farkı

Zorlanmış Hava (Fan):
Q_forced = ṁ × c_p × ΔT

ṁ: Hava kütle debisi (kg/s)
c_p: Özgül ısı (1005 J/kg·K)
ΔT: Giriş - çıkış sıcaklık farkı
```

### Havalandırma Delik Boyutu

```
Reynolds Sayısı: Re = (v × D) / ν

Hedef: Turbulan akış (Re > 4000) için iyi karıştırma
v: Hava hızı (m/s)
D: Delik çapı (m)
ν: Viskozite (1.5×10⁻⁵ m²/s)

10mm delik, 2 m/s hız:
Re = (2 × 0.01) / 1.5×10⁻⁵ = 1333 (laminar)
```

### Kasa Termal Direnci

```
Rθ_enclosure = 1 / (h_ext × A_ext)

h_ext: Dış yüzey ısı taşınım (10 W/m²·K)
A_ext: Dış yüzey alanı (0.05 m²)

Rθ_enclosure = 1 / (10 × 0.05) = 2.0°C/W
```

## Teknik Spesifikasyonlar

### Kasa Malzemesi: Alüminyum 6061-T6

| Özellik | Değer |
|---------|-------|
| Malzeme | Alüminyum 6061-T6 |
| Isı İletim Katsayısı | 167 W/m·K |
| Yoğunluk | 2700 kg/m³ |
| Özgül Isı | 896 J/kg·K |
| Yüzey İşlemi | Anodize (siyah, ε=0.85) |
| Kalınlık | 2.0 mm |
| Ağırlık | ~350g (standart kasa) |

### Kasa Boyutları ve Yüzey Alanı

| Kasa Tipi | Boyut (mm) | Yüzey Alanı | Hacim |
|-----------|------------|-------------|-------|
| Compact | 150×100×50 | 550 cm² | 750 cm³ |
| Standart | 200×150×60 | 1020 cm² | 1800 cm³ |
| Rack-mount | 430×300×44 | 4100 cm² | 5676 cm³ |
| **Seçim** | **200×150×60** | **1020 cm²** | **1800 cm³** |

### Havalandırma Delik Dizaynı

| Bölge | Delik Çapı | Delik Sayısı | Toplam Alan |
|-------|------------|--------------|-------------|
| Giriş (alt) | 3mm | 50 | 353 mm² |
| Çıkış (üst) | 3mm | 60 | 424 mm² |
| Yan panel | 2mm | 100 | 314 mm² |
| Toplam | - | 210 | 1091 mm² |

### Hava Akışı Yolları

```
Giriş (Alt)          Çıkış (Üst)
    │                    ▲
    ▼                    │
┌───────────────────────────────┐
│  ┌─────────┐  ┌─────────┐    │
│  │  CPU +  │  │  PSU    │    │
│  │ Heatsink│  │         │    │
│  └─────────┘  └─────────┘    │
│        ▲              ▲       │
│        │              │       │
│   Fan Girişi    Fan Çıkışı   │
│   (120mm)       (120mm)      │
└───────────────────────────────┘
```

## Seçim Kriterleri

### Kasa Malzemesi Seçimi

1. **Alüminyum 6061-T6**:
   - Yüksek termal iletim (167 W/m·K)
   - Düşük ağırlık
   - Korozyon direnci (anodize)
   - Elektromanyetik ekranlama

2. **Alternatif Karşılaştırma**:

| Malzeme | k (W/m·K) | Ağırlık | Maliyet | EMK |
|---------|-----------|---------|---------|-----|
| Alüminyum 6061 | 167 | Düşük | Orta | İyi |
| Çelik (SECC) | 50 | Yüksek | Düşük | Mükemmel |
| Plastik (ABS) | 0.2 | Düşük | Düşük | Kötü |
| **Seçim** | **✓** | **✓** | **✓** | - |

### Havalandırma Tasarım Kriterleri

1. **Giriş Delikleri**: Alt kısım, temiz hava girişi
2. **Çıkış Delikleri**: Üst kısım, sıcak hava çıkışı (natural convection)
3. **Delik Boyutu**: 2-3mm (toz girişini minimize)
4. **Toplam Delik Alanı**: ≥ 1000 mm² (yeterli hava akışı)
5. **Toz Filtresi**: Manyetik veya yıkanabilir filtre

### Enclosure Termal Direnç Hedefi

```
Hedef Rθ_enclosure: ≤ 2.5°C/W
Mevcut Rθ_enclosure: 2.0°C/W ✓
Güvenli Pay: 0.5°C/W
```

## Bağımlılıklar

### Girişler
- **K01 Donanım**: İç bileşen yerleşim planı
- **K18 Heatsink**: Heatsink boyutları ve konumu
- **K18 Fan**: Fan boyutu ve hava debisi
- **K18 Termal Simülasyon**: Hava akışı analizleri

### Çıktılar
- **K18 Ambient Temperature**: Kasa içi sıcaklık profili
- **K18 Termal Resistance**: Kasa termal direnç katkısı
- **K20 Güvenlik**: Kasa içi sıcaklık izleme

### Entegrasyon Noktaları
```
Ortam Havası (Alt Giriş)
        │
        ▼
┌──────────────────┐
│  Kasa İçi        │  Hava akışı yolu
│  Hava Akışı      │  Doğal + zorlanmış
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Bileşenler      │  Isı kaynağı
│  (CPU, Amp, DAC) │  Toplam 54.5W
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Heatsink + Fan  │  Isı transferi
│  (Fischer+Noctua)│  Aktif soğutma
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Kasa Dış Yüzey  │  Isı yayılımı
│  (Alüminyum)     │  Radyasyon + konveksiyon
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Çıkış (Üst)     │  Sıcak hava çıkışı
│  Havalandırma    │  Natural convection
└──────────────────┘
```

## Uygulama Notları

### Kasa Havalandırma Tasarım Prosedürü

```
1. İç bileşen yerleşim planını oluştur
2. Isı kaynaklarını belirle (toplam güç)
3. Hava akışı yolunu tasarla (alt → üst)
4. Delik boyutu ve sayısını hesapla
5. Toplam delik alanını doğrula (≥1000mm²)
6. Toz filtresi ekle (opsiyonel)
7. CFD simülasyonu ile doğrula
8. Prototip üret ve test et
```

### Kasa Termal Performans Testi

```
Test Prosedürü:
1. Kasa içine 10 termal çift sensör yerleştir
2. Tüm bileşenleri tam yükte çalıştır
3. 60 dakika bekle (kararlı hal)
4. Sensör değerlerini kaydet
5. Sıcaklık haritası oluştur
6. Kritik noktaları belirle
7. Gerekirse havalandırma iyileştir
```

### Yaygın Hatalar

| Hata | Sonuç | Önleme |
|------|-------|--------|
| Yetersiz havalandırma | Kasa içi sıcaklık artışı | Delik alanı hesabı |
| Yanlış hava akışı | Hot spots oluşumu | Hava akışı simülasyonu |
| Toz birikmesi | Termal direnç artışı | Filtre kullanımı |
| Kasa kapatma sızıntısı | Hava kaçağı | Conta kullanımı |

## Durum: Implementasyon

Kasa termal tasarımı, COREMUSIC platformunda iç mekan sıcaklık yönetimi için onaylanmıştır. Alüminyum 6061-T6 kasa, 1020 cm² yüzey alanı ile yeterli ısı yayılımı sağlamaktadır. Havalandırma delik tasarımı 1091 mm² tolam delik alanı ile optimize edilmiş ve doğal konveksiyon ile zorlanmış hava akışı kombinasyonu sağlanmıştır.
