---
title: "Termal Simülasyon Metodolojisi"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# Termal Simülasyon Metodolojisi

## Genel Bakış

Termal simülasyon, COREMUSIC donanım tasarımının fiziksel test öncesi dijital ortamda doğrulanmasını sağlar. CFD (Computational Fluid Dynamics) analizi ile hava akışı, sıcaklık dağılımı ve termal direnç hesaplamaları gerçekleştirilir. Bu metodoloji, tasarım hatalarını erken aşamada tespit ederek maliyet ve zaman tasarrufu sağlar.

## Termal Hesaplamalar

### CFD Temel Denklemleri

```
Navier-Stokes Denklemleri:
ρ(∂v/∂t + v·∇v) = -∇p + μ∇²v + ρg

Enerji Denklemi:
ρc_p(∂T/∂t + v·∇T) = k∇²T + Φ

Süreklilik Denklemi:
∇·v = 0 (sıkıştırılamaz akışkan)
```

### Termal Direnç Ağ Modeli

```
T_junction → Rθ_jc → T_case → Rθ_cs → T_sink → Rθ_sa → T_ambient

Rθ_jc: Junction-to-case (0.5°C/W)
Rθ_cs: Case-to-sink (0.8°C/W - termal ped)
Rθ_sa: Sink-to-ambient (2.2°C/W - aktif soğutma)
```

### Hava Akışı Hesabı

```
Reynolds Sayısı: Re = (v × D) / ν

v: Hava hızı (m/s)
D: Karakteristik uzunluk (m)
ν: Kinematik viskozite (1.5×10⁻⁵ m²/s)

Re < 2300: Laminar akış
Re > 4000: Turbulan akış
2300 < Re < 4000: Geçiş bölgesi
```

## Teknik Spesifikasyonlar

### CFD Yazılım ve Araçlar

| Yazılım | Amaç | Lisans |
|---------|------|--------|
| OpenFOAM | Ana CFDsolver | Açık kaynak |
| FreeCAD + CfdOF | Geometri modelleme | Açık kaynak |
| ParaView | Sonuç görselleştirme | Açık kaynak |
| MATLAB | Veri analizi | Ticari |
| Python (NumPy/SciPy) | Script otomasyonu | Açık kaynak |

### Simülasyon Parametreleri

| Parametre | Değer |
|-----------|-------|
| Hacim grid boyutu | 0.5mm-2mm (adaptive) |
| Mesh türü | Unstructured tetrahedral |
| Toplam hücre sayısı | ~500,000-1,000,000 |
| Akışkan modeli | Hava (ideal gas) |
| Turbulans modeli | k-ε (RANS) |
| Sınır koşulları | Velocity inlet, Pressure outlet |
| Sıcaklık koşulu | Wall (fixed T veya heat flux) |
| Çözücü | SIMPLEC (basınç-bağlama) |
| Adım boyutu | 0.001-0.01 sn |
| Toplam simülasyon süresi | 300-600 sn |

### Simülasyon Senaryoları

| Senaryo | Sıcaklık | Fan Durumu | Amaç |
|---------|----------|------------|------|
| Idle | 25°C ambient | Fan %20 | Pasif soğutma doğrulama |
| Normal yük | 30°C ambient | Fan %50 | Çalışma sıcaklığı |
| Yüksek yük | 35°C ambient | Fan %80 | Kritik sıcaklık |
| Fan arızası | 30°C ambient | Fan kapalı | Acil durum senaryosu |
| Yaz sıcaklığı | 40°C ambient | Fan %100 | Extreme ortam |

## Seçim Kriterleri

### CFD Yazılım Seçimi: OpenFOAM
1. **Açık Kaynak**: Lisans maliyeti yok
2. **Geniş Modül Yelpazesi**: fluidHeatTransfer, buoyantBoussinesqSimpleFoam
3. **Özelleştirme**: C++ ile solver özelleştirme
4. **Topluluk Desteği**: Büyük kullanıcı topluluğu
5. **Geçmiş Başarı**: Endüstri standardı CFD aracı

### Grid Kalite Kriterleri

| Kriter | Hedef | Minimum |
|--------|-------|---------|
| Skewness | < 0.5 | < 0.85 |
| Orthogonality | > 0.7 | > 0.5 |
| Aspect Ratio | < 5 | < 20 |
| Y+ (duvar) | 30-300 | - |
| Grid Bağımsızlığı | < %2 sonuç farkı | - |

### Doğrulama Kriterleri

| Kriter | Değer | Kaynak |
|--------|-------|--------|
| Sıcaklık farkı (simülasyon vs test) | < %10 | Experimental |
| Hava debisi farkı | < %15 | Anemometre |
| Gürültü seviyesi farkı | < 3 dB | Sound meter |
| Termal direnç farkı | < %12 | Thermal test |

## Bağımlılıklar

### Girişler
- **K01 Donanım**: 3D CAD modelleri (STEP/IGES)
- **K18 Heatsink**: Heatsink geometrisi ve malzeme özellikleri
- **K17 Güç Yönetimi**: Bileşen güç tüketimi verileri
- **K18 Termal Ped**: Termal arayüz malzeme özellikleri
- **K18 Fan**: Fan performans eğrileri (P-Q curves)

### Çıktılar
- **K18 Heatsink**: Heatsink optimizasyon önerileri
- **K18 Enclosure**: Kasa havalandırma tasarımı
- **K18 Ambient Temperature**: Ortam sıcaklık etkisi analizi
- **K13 CI/CD**: Simülasyon otomasyon scriptleri

### Entegrasyon Noktaları
```
CAD Model (K01)
     │
     ▼
┌──────────────────┐
│  Geometri        │  FreeCAD/Salome
│  Hazırlama       │  Mesh oluşturma
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  OpenFOAM        │  CFD solver
│  Simülasyon      │  k-ε turbulans
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  ParaView        │  Sonuç görselleştirme
│  Analiz          │  Sıcaklık/harita
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Rapor           │  Tasarım önerileri
│  Optimizasyon    │  Iteratif geliştirme
└──────────────────┘
```

## Uygulama Notları

### Simülasyon Workflow

```
1. CAD Model İçe Aktarma (.step → .stl)
2. Mesh Oluşturma (snappyHexMesh)
3. Sınır Koşulları Tanımlama
4. Solver Yapılandırma
5. Simülasyon Çalıştırma
6. Sonuç Analizi (ParaView)
7. Rapor Oluşturma
8. Optimizasyon Önerileri
```

### Sık Karşılaşılan Sorunlar

| Sorun | Neden | Çözüm |
|-------|-------|-------|
| Diverjans | Yanlış sınır koşulları | Basınç ayarı, under-relaxation |
| Uzun süren simülasyon | Çok büyük mesh | Adaptive mesh refinement |
| Yanlış sonuçlar | Hatalı malzeme özellikleri | Doğrulama testleri |
| Görselleştirme sorunu | Büyük veri seti | Slice plane, iso-surface |

### Doğrulama Prosedürü

```
1. Prototip üretimi (3D printing)
2. Sıcaklık sensörleri yerleştirme (10 nokta)
3. Fan hız kontrolü
4. Farklı yük senaryolarında ölçüm
5. Simülasyon sonuçlarıyla karşılaştırma
6. Model kalibrasyonu (gerekirse)
7. Nihai rapor
```

## Durum: Implementasyon

Termal simülasyon metodolojisi, COREMUSIC tasarım sürecinin temel bir parçası olarak onaylanmıştır. OpenFOAM tabanlı CFD analizleri, geometri hazırlama ve mesh oluşturma prosedürleri tanımlanmıştır. Doğrulama kriterleri belirlenmiş ve iteratif optimizasyon süreci kurulmuştur.
