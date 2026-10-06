---
title: "PWM Fan Hız Kontrolü"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# PWM Fan Hız Kontrolü

## Genel Bakış

PWM (Pulse Width Modulation) fan kontrolü, COREMUSIC platformunda aktif soğutma için kullanılan dinamik hız kontrol yöntemidir. Sıcaklık sensörlerinden gelen geri beslemeye göre fan hızı otomatik olarak ayarlanır. Bu sayede düşük sıcaklıklarda sessiz çalışma, yüksek sıcaklıklarda maksimum soğutma sağlanır.

## Termal Hesaplamalar

### PWM Duty Cycle Hesabı

```
Duty Cycle (%) = (On_Time / Period_Time) × 100

Period Time: 25 kHz (40μs periyot)
Min Duty: %20 (minimum fan hızı)
Max Duty: %100 (maksimum fan hızı)
```

### Sıcaklık-Fan Hızı Eğrisi

```
Sıcaklık (°C)    Duty Cycle (%)    Fan RPM
   < 50              %20            800
   50-60             %30           1200
   60-70             %50           2000
   70-80             %70           2800
   80-85             %90           3600
   > 85              %100          4000
```

### Soğutma Kapasitesi

```
Q_fan = ṁ × c_p × ΔT

ṁ: Hava kütle debisi (g/s)
c_p: Özgül ısı (1.005 J/g·K)
ΔT: Fan öncesi/sonrası sıcaklık farkı

4000 RPM'de: ṁ = 15 g/s
ΔT = 2°C (fan çıkışı sıcaklık düşüşü)
Q_fan = 15 × 1.005 × 2 = 30.15W soğutma kapasitesi
```

## Teknik Spesifikasyonlar

### Seçilen Fan: Noctua NF-A12x25 PWM

| Özellik | Değer |
|---------|-------|
| Model | Noctua NF-A12x25 PWM |
| Boyut | 120mm × 120mm × 25mm |
| PWM Sinyali | 4-pin (PWM, +12V, GND, Tach) |
| PWM Frekansı | 25 kHz |
| Çalışma Voltajı | 12V DC |
| Çalışma Akımı | 0.13A (max) |
| Güç Tüketimi | 1.56W (max) |
| Hız Aralığı | 450-2000 RPM |
| Hava Debisi | 102.1 m³/h (max) |
| Statik Basınç | 2.34 mmH₂O |
| Gürültü | 22.6 dB(A) (min) |
| Ömür | > 150,000 saat (MTBF) |
| Rulman | SSO2 (Self-Stabilising Oil-Pressure) |

### PWM Sinyal Özellikleri

| Özellik | Değer |
|---------|-------|
| PWM Frekansı | 25 kHz ± 5% |
| Min Duty Cycle | %0 (kapatma) |
| Max Duty Cycle | %100 (tam hız) |
| Tachometer Çıkışı | 2 pulse/rev |
| Tachometer Voltajı | 5V (open collector) |
| Start Voltage | 5V DC (minimum) |
| Lock Protection | Otomatik restart |

### Fan Hız Haritası

| Duty (%) | RPM | Hava Debisi | Gürültü | Uygulama |
|----------|-----|-------------|---------|----------|
| 0% | 0 | 0 | 0 dB | Fan kapatma |
| 20% | 450 | 25 m³/h | 12 dB | Idle mode |
| 30% | 670 | 38 m³/h | 15 dB | Düşük yük |
| 50% | 1000 | 56 m³/h | 19 dB | Orta yük |
| 70% | 1400 | 78 m³/h | 22 dB | Yüksek yük |
| 90% | 1800 | 95 m³/h | 25 dB | Kritik yük |
| 100% | 2000 | 102 m³/h | 26 dB | Maksimum |

## Seçim Kriterleri

### Fan Seçim Nedenleri
1. **PWM Kontrol Desteği**: 4-pin PWM, hassas hız kontrolü
2. **Düşük Gürültü**: 22.6 dB(A) minimum, sessiz çalışma
3. **Yüksek Ömür**: 150,000 saat MTBF, güvenilirlik
4. **SSO2 Rulman**: Yağlama gerektirmez, bakım yok
5. **Statik Basınç**: 2.34 mmH₂O, heatsink üzerinden hava akışı

### Alternatif Karşılaştırma

| Kriter | Noctua NF-A12x25 | Arctic P12 PWM | Be Quiet! Shadow Wings 2 |
|--------|------------------|----------------|--------------------------|
| RPM | 450-2000 | 200-1800 | 300-1500 |
| Hava Debisi | 102 m³/h | 56 m³/h | 67 m³/h |
| Gürültü | 22.6 dB(A) | 22.5 dB(A) | 15.8 dB(A) |
| Ömür | 150k saat | 40k saat | 80k saat |
| **Seçim** | **✓** | - | - |

## Bağımlılıklar

### Girişler
- **K19 Sensörler**: CPU ve ambientsıcaklık sensörleri
- **K02 Sürücü**: Fan PWM sürücü donanımı
- **K18 Termal Simülasyon**: Fan hız eğrisi optimizasyonu

### Çıktılar
- **K18 Enclosure**: Kasa içi hava akışı tasarımı
- **K18 Ambient Temperature**: Fan ile ortam sıcaklığı etkileşimi
- **K20 Güvenlik**: Fan arıza durumunda koruma prosedürleri

### Entegrasyon Noktaları
```
Sıcaklık Sensörleri (K19)
        │
        ▼
┌──────────────────┐
│  Fan Controller  │  PWM Duty Cycle Hesaplama
│  (Software)      │  (Linux/ALSA integration)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  PWM Signal      │  25 kHz sinyal
│  Generator       │  GPIO pin çıkış
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Fan Motor       │  Noctua NF-A12x25
│  (4-pin PWM)     │  450-2000 RPM
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Tachometer      │  RPM ölçümü
│  Feedback        │  2 pulse/rev
└──────────────────┘
```

## Uygulama Notları

### PWM Fan Kontrol Yazılımı

```bash
# Linux PWM fan kontrol (sysfs)
echo 1 > /sys/class/hwmon/hwmon0/pwm1_enable  # PWM modu
echo 128 > /sys/class/hwmon/hwmon0/pwm1        # %50 duty cycle
cat /sys/class/hwmon/hwmon0/fan1_input          # RPM okuma
```

### Fan Hız Eğrisi Optimizasyonu

```
Hedef: Gürültü ve soğutma dengesi

1. Idle modda sessizlik: ≤ 20 dB(A)
2. Orta yükte denge: ≤ 25 dB(A)
3. Kritik yükte soğutma: ≤ 30 dB(A)
4. Acil durum: Tam hız (30 dB(A))
```

### Fan Arıza Durumları

| Durum | Belirti | Müdahale |
|-------|---------|----------|
| Fan durdu | 0 RPM tachometer | Hızlı restart denemesi |
| Fan yavaş | RPM duty'den düşük | PWM sinyal kontrolü |
| Fan titreşim | Anormal titreşim | Rulman kontrolü, değiştirme |
| Fan gürültülü | Anormal ses | Rulman yağlama/değiştirme |

### Bakım
- **Periyot**: 12 ayda bir toz temizliği
- **Yağlama**: SSO2 rulman yağlama gerektirmez
- **Değişim**: Fan arızasında veya >30dB gürültüde

## Durum: Implementasyon

PWM fan kontrolü, COREMUSIC platformunda aktif soğutma için onaylanmıştır. Noctua NF-A12x25 PWM fan, 450-2000 RPM aralığında sessiz ve etkili soğutma sağlamaktadır. Sıcaklık-fan hızı eğrisi optimize edilmiş ve fan arıza durumları için koruma mekanizmaları tanımlanmıştır.
