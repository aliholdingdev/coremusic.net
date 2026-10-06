---
title: "Termal Direnç Hesaplamaları"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# Termal Direnç Hesaplamaları

## Genel Bakış

Termal direnç (Rθ), ısı akışına karşı gösterilen dirençtir ve elektriksel dirence benzer şekilde hesaplanır. COREMUSIC platformunda, junction-to-ambient termal direnç zinciri, bileşenlerin sıcaklık yönetimini belirler. Tüm termal hesaplamalar, bu direnç ağ modeline dayanır.

## Termal Hesaplamalar

### Temel Formül

```
Rθ = ΔT / Q

Rθ: Termal direnç (°C/W veya K/W)
ΔT: Sıcaklık farkı (°C veya K)
Q: Isı akışı (W)
```

### Termal Direnç Zinciri

```
Rθ_ja = Rθ_jc + Rθ_cs + Rθ_sa

Rθ_ja: Junction-to-Ambient (toplam)
Rθ_jc: Junction-to-Case (bileşen içi)
Rθ_cs: Case-to-Sink ( TIM + montaj)
Rθ_sa: Sink-to-Ambient (soğutucu + hava)
```

### COREMUSIC Bileşen Dirençleri

```
CPU (ARM Cortex-A72):
Rθ_jc = 0.5°C/W (datasheet)
Rθ_cs = 0.8°C/W (termal ped)
Rθ_sa = 2.2°C/W (Fischer SK53 + fan)
Rθ_ja = 0.5 + 0.8 + 2.2 = 3.5°C/W

Amplifikatör (Class-D):
Rθ_jc = 1.0°C/W (datasheet)
Rθ_cs = 0.5°C/W (termal ped)
Rθ_sa = 1.8°C/W (heatsink + fan)
Rθ_ja = 1.0 + 0.5 + 1.8 = 3.3°C/W

Audio DAC (ESS Sabre):
Rθ_jc = 2.0°C/W (datasheet)
Rθ_cs = 1.0°C/W (termal ped)
Rθ_sa = 3.0°C/W (pasif soğutma)
Rθ_ja = 2.0 + 1.0 + 3.0 = 6.0°C/W
```

### Sıcaklık Hesapları

```
CPU (15W, 25°C ambient):
T_junction = 25 + (15 × 3.5) = 25 + 52.5 = 77.5°C ✓

Amplifikatör (25W, 25°C ambient):
T_junction = 25 + (25 × 3.3) = 25 + 82.5 = 107.5°C ⚠️

DAC (2W, 25°C ambient):
T_junction = 25 + (2 × 6.0) = 25 + 12 = 37°C ✓
```

## Teknik Spesifikasyonlar

### TERMAL DİREnç DEĞERLERİ

| Bileşen | Rθ_jc | Rθ_cs | Rθ_sa | Rθ_ja | Durum |
|---------|-------|-------|-------|-------|-------|
| CPU | 0.5 | 0.8 | 2.2 | 3.5 | ✓ |
| Amplifikatör | 1.0 | 0.5 | 1.8 | 3.3 | ⚠️ |
| DAC | 2.0 | 1.0 | 3.0 | 6.0 | ✓ |
| HDMI | 1.5 | 0.8 | 2.5 | 4.8 | ✓ |
| WiFi/BT | 3.0 | 1.2 | 4.0 | 8.2 | ✓ |
| PSU | 0.3 | 0.2 | 1.5 | 2.0 | ✓ |

### Termal Direnç bileşenleri

| Direnç | Kaynak | Değer Aralığı |
|--------|--------|----------------|
| Rθ_jc | Bileşen içi | 0.1-5.0°C/W |
| Rθ_cs | Termal ped | 0.1-2.0°C/W |
| Rθ_cs | Termal macun | 0.05-0.5°C/W |
| Rθ_sa | Heatsink (pasif) | 2.0-10.0°C/W |
| Rθ_sa | Heatsink (aktif) | 0.5-3.0°C/W |
| Rθ_sa | Heat pipe | 0.1-1.0°C/W |

### Temas Alanı ve Direnç İlişkisi

```
Rθ_cs = L / (k × A)

L: Kalınlık (m)
k: Termal iletim (W/m·K)
A: Temas alanı (m²)

Örnek: 0.5mm ped, 6 W/m·K, 25×25mm
Rθ_cs = 0.0005 / (6 × 0.000625) = 0.133°C/W
```

## Seçim Kriterleri

### Termal Direnç Hedefleri

| Bileşen | Hedef Rθ_ja | Gerekçe |
|---------|-------------|---------|
| CPU | ≤ 4.0°C/W | 85°C altında çalışma |
| Amplifikatör | ≤ 2.5°C/W | 100°C altında çalışma |
| DAC | ≤ 7.0°C/W | 60°C altında çalışma |
| HDMI | ≤ 5.0°C/W | 70°C altında çalışma |

### Direnç Azaltma Stratejileri

1. **Düşük Rθ_jc**: Yüksek performanslı bileşen seçimi
2. **Düşük Rθ_cs**: İyi TIM seçimi, yüksek basınc
3. **Düşük Rθ_sa**: Büyük heatsink, fan kullanımı

### Malzeme Seçim Kriterleri

| Malzeme | k (W/m·K) | Uygulama |
|---------|-----------|----------|
| Bakır | 400 | Heatsink tabanı |
| Alüminyum | 201 | Heatsink kanatları |
| Grafen | 5000 | High-end TIM |
| Diamond | 2000 | Extreme uygulama |
| Silicone pad | 1-6 | Genel TIM |

## Bağımlılıklar

### Girişler
- **K01 Donanım**: Bileşen datasheet'leri ve termal özellikleri
- **K18 Heatsink**: Heatsink geometrisi ve malzeme
- **K18 Termal Ped**: TIM özellikleri
- **K17 Güç Yönetimi**: Bileşen güç tüketimi

### Çıktılar
- **K18 Termal Simülasyon**: CFD modeli için direnç değerleri
- **K18 PWM Fan**: Fan hız eğrisi referansları
- **K20 Güvenlik**: Kritik sıcaklık eşikleri

### Entegrasyon Noktaları
```
Bileşen Datasheet → Rθ_jc → Direnç Ağ Modeli → Sıcaklık Hesabı
        │                                    │
        └── Termal Ped → Rθ_cs ──────────────┘
                │
                └── Heatsink → Rθ_sa ────────┘
```

## Uygulama Notları

### Direnç Ölçüm Prosedürü

```
1. Termal çift sensörleri yerleştir (junction, case, sink)
2. Sabit güç uygula (P = 10W)
3. Sıcaklık kararlılığını bekle (10 dk)
4. Sıcaklık değerlerini kaydet
5. Rθ hesapla: Rθ = ΔT / P
6. Karşılaştırma: Ölçülen vs hesaplanan
```

### Yaygın Hatalar

| Hata | Sonuç | Önleme |
|------|-------|--------|
| Yanlış datasheet değeri | Hatalı hesaplama | Doğrulama ölçümü |
| Temas alanı yanlış | Yüksek Rθ | Gerçekçi alan hesabı |
| TIM kalınlığı yanlış | Rθ sapması | Kalınlık ölçümü |
| Eksik direnç zinciri | Yanlış toplam | Tüm zinciri dahil et |

### Optimizasyon İpuçları

1. **En büyük direnci azalt**: En yüksek Rθ bileşenini iyileştir
2. **Temas alanını artır**: Daha geniş heatsink tabanı
3. **Basıncı artır**: Daha güçlü montaj (sınırda)
4. **TIM'i iyileştir**: Daha yüksek k TIM

## Durum: Implementasyon

Termal direnç hesaplamaları, COREMUSIC platformunun termal tasarım temelini oluşturmaktadır. Junction-to-ambient direnç zinciri tüm bileşenler için hesaplanmış ve hedef değerler belirlenmiştir. Amplifikatör bileşeni için ek soğutma önlemi gerekmektedir. Direnç ölçüm prosedürleri ve optimizasyon stratejileri tanımlanmıştır.
