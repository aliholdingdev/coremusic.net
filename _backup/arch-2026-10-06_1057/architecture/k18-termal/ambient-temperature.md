---
title: "Ortam Sıcaklığı Değerlendirmesi"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# Ortam Sıcaklığı Değerlendirmesi

## Genel Bakış

Ortam sıcaklığı (ambient temperature), COREMUSIC cihazlarının çalıştığı çevre sıcaklığıdır ve termal tasarımın sınırlayıcı faktörlerinden biridir. Tüm termal hesaplamalar referans ortam sıcaklığına göre yapılır. COREMUSIC platformu, ev, stüdyo ve araç ortamları için farklı sıcaklık aralıklarında çalışacak şekilde tasarlanmıştır.

## Termal Hesaplamalar

### Ortam Sıcaklığı Etkisi

```
T_max = T_ambient + (P × Rθ_total)

T_max: Maksimum bileşen sıcaklığı
T_ambient: Ortam sıcaklığı
P: Bileşen güç tüketimi
Rθ_total: Toplam termal direnç
```

### Farklı Ortam Senaryoları

```
Senaryo 1: Ev Ortamı (20-25°C)
T_max = 25 + (15 × 2.2) = 25 + 33 = 58°C ✓

Senaryo 2: Stüdyo (25-30°C)
T_max = 30 + (15 × 2.2) = 30 + 33 = 63°C ✓

Senaryo 3: Araç İçi (40-60°C)
T_max = 50 + (15 × 2.2) = 50 + 33 = 83°C ⚠️ (kritik)

Senaryo 4: Dış Mekan (35-45°C)
T_max = 40 + (15 × 2.2) = 40 + 33 = 73°C ✓ (fan ile)
```

### Güvenli Çalışma Aralığı

```
Ortam Sıcaklığı    │ Durum              │ Aksiyon
0-10°C            │ Soğuk              │ Fan minimum
10-25°C           │ Ideal              │ Fan optimize
25-40°C           │ Sıcak              │ Fan artır
40-50°C           │ Çok sıcak          │ Throttle başlangıcı
> 50°C            │ Kritik             │ Acil kapatma
```

## Teknik Spesifikasyonlar

### Çalışma Ortamı Kategorileri

| Kategori | Sıcaklık | Nem | Uygulama |
|----------|----------|-----|----------|
| Ev Ortamı | 15-30°C | %30-60 | Living room, ofis |
| Stüdyo | 20-28°C | %40-50 | Profesyonel stüdyo |
| Araç İçi | -20°C - +80°C | %10-90 | Araç infotainment |
| Dış Mekan | -30°C - +50°C | %0-100 | Açık hava hoparlör |
| Endüstriyel | 0°C - +45°C | %20-80 | Endüstriyel kontrol |

### Sıcaklık Dalgalanmaları

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Günlük Dalgalanma | ±10°C | Gündüz/gece farkı |
| Mevsimsel Dalgalanma | ±25°C | Yaz/kış farkı |
| Ani Değişim | 5°C/dk | Kapı açılması, klima |
| Isı Kaynağı Yakınlığı | +5-15°C | Güneş ışığı, cihazlar |

### Araç Ortamı Detayları

| Parametre | Değer |
|-----------|-------|
| Çalışma Sıcaklığı | -40°C ile +85°C |
| Depolama Sıcaklığı | -40°C ile +105°C |
| Isınma Süresi (cold start) | -40°C → 0°C: 5 dk |
| Soğuma Süresi | +85°C → +50°C: 10 dk (fan ile) |
| Güneş Işığı Etkisi | +15-20°C (dashboard) |

## Seçim Kriterleri

### Ortam Sıcaklığı Sensörleri

| Sensör | Aralık | Hassasiyet | Uygulama |
|--------|--------|------------|----------|
| NTC 10kΩ | -40°C - +125°C | ±1°C | Genel amaç |
| DS18B20 | -55°C - +125°C | ±0.5°C | Hassas ölçüm |
| TMP36 | -40°C - +125°C | ±1°C | Düşük maliyet |
| **Seçim** | **✓** | **✓** | COREMUSIC |

### Ortam Sensör Yerleşimi

```
Yerleşim Kuralları:
1. Fan girişi dışarıdan hava almalı
2. Sensör fan girişine yakın olmalı
3. Güneş ışığından korunmalı
4. Isı kaynaklarından uzak olmalı
5. Temiz hava akışı sağlanmalı
```

### Sıcaklık-Aksiyon Eşikleri

| Eşik | Sıcaklık | Aksiyon | Öncelik |
|------|----------|---------|---------|
| Normal | < 35°C | Fan optimize | Düşük |
| Uyarı | 35-45°C | Fan artır | Orta |
| Alarm | 45-55°C | Throttle | Yüksek |
| Kritik | 55-65°C | Acil soğutma | Kritik |
| Acil | > 65°C | Kapatma | Acil |

## Bağımlılıklarlar

### Girişler
- **K19 Sensörler**: Ortam sıcaklık sensörleri
- **K01 Donanım**: Sensör yerleşim planı
- **K18 Termal Simülasyon**: Ortam sıcaklık etkisi analizi

### Çıktılar
- **K18 PWM Fan**: Fan hız kontrolü için referans sıcaklık
- **K18 Termal Resistance**: Ortam sıcaklığına göre Rθ ayarı
- **K20 Güvenlik**: Kritik sıcaklık koruma prosedürleri

### Entegrasyon Noktaları
```
Ortam Sensörü (K19)
        │
        ▼
┌──────────────────┐
│  Sıcaklık Okuma  │  Her 1 sn
│  (Sysfs/ADC)     │  ±0.5°C hassasiyet
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Eşik Kontrolü   │  5 eşik seviyesi
│  (Software)      │  Aksiyon tetikleme
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Aksiyon         │  Fan, throttle, kapatma
│  Uygulama        │  Öncelik sıralaması
└──────────────────┘
```

## Uygulama Notları

### Ortam Sensörü Kalibrasyonu

```
Kalibrasyon Prosedürü:
1. Bilinen sıcaklık kaynağına yerleştir (su banyosu)
2. 25°C, 50°C, 75°C noktalarında ölç
3. Doğrusal regresyon ile kalibrasyon eğrisi oluştur
4. Kalibrasyon sabitlerini EEPROM'a yaz
5. Yılda bir tekrar kalibre et
```

### Araç Ortamı İçin Özel Önlemler

1. **Cold Start**: -40°C'de fan başlatma zorluğu
   - Çözüm: Düşük hızda starter motor
2. **Dashboard Isısı**: Güneş altında +80°C
   - Çözüm: yalıtım malzemesi, reflection
3. **Nem Kondensasyonu**: Ani sıcaklık değişimi
   - Çözüm: Conformal coating, desiccant

### Ofis/Stüdyo Ortamı İçin

1. **Klima Etkisi**: Ani sıcaklık düşüşü
   - Çözüm: Yavaş fan hızı ayarı
2. **Isı Kaynakları**: Yakın monitör/amp
   - Çözüm: Uzaklık planlaması
3. **Hava Dolaşımı**: Stagnant hava
   - Çözüm: Fan yardımı ile sirkülasyon

## Durum: Implementasyon

Ortam sıcaklığı değerlendirmesi, COREMUSIC termal tasarımının temel girdilerinden biridir. Ev, stüdyo ve araç ortamları için sıcaklık aralıkları tanımlanmış, sensör yerleşim kuralları belirlenmiş ve sıcaklık-aksiyon eşikleri oluşturulmuştur. Araç ortamı için ek önlemler planlanmıştır.
