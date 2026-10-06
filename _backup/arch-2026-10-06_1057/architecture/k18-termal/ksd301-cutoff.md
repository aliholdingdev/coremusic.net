---
title: "KSD301 Termal Cutoff"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# KSD301 Termal Cutoff

## Genel Bakış

KSD301, COREMUSIC platformunda aşırı sıcaklık koruması için kullanılan mekanik termal cutoff cihazıdır. Bimetal strip yapısı sayesinde belirlenen sıcaklık eşiğine ulaştığında mekanik olarak devreyi keser. Bu cihaz, donanım koruması için son savunma hattı olarak işlev görür ve yazılımsız bağımsız bir koruma mekanizması sağlar.

## Termal Hesaplamalar

### Trip Sıcaklığı Hesabı

```
T_trip = T_nominal ± T_tolerance

KSD301-100: T_trip = 100°C ± 5°C
KSD301-85:  T_trip = 85°C ± 5°C
KSD301-70:  T_trip = 70°C ± 5°C
```

### Isıtma Hızı Etkisi

```
Isıtma Hızı (dT/dt) | Trip Sıcaklığı
< 1°C/s             | Nominal ± 5°C
1-5°C/s             | Nominal + 3-8°C
> 5°C/s             | Nominal + 8-15°C

COREMUSIC Uygulaması: ≤ 2°C/s hedef
```

### Thermal Time Constant

```
τ = (m × c) / (h × A)

m: Cihaz kütlesi (0.5g)
c: Özgül ısı kapasitesi (450 J/kg·K)
h: Isı taşınım katsayısı (50 W/m²·K)
A: Yüzey alanı (50mm²)

τ = (0.0005 × 450) / (50 × 0.00005)
τ = 0.225 / 0.0025
τ = 90 saniye (yaklaşık)
```

## Teknik Spesifikasyonlar

### KSD301 Serisi Karşılaştırma

| Model | Trip Sıcaklığı | Tolerans | Voltaj | Akım | Durum |
|-------|----------------|----------|--------|------|-------|
| KSD301-70 | 70°C | ±5°C | 250V AC | 10A | Seçildi (CPU) |
| KSD301-85 | 85°C | ±5°C | 250V AC | 10A | Yedek |
| KSD301-100 | 100°C | ±5°C | 250V AC | 10A | Seçildi (Amp) |
| KSD301-110 | 110°C | ±5°C | 250V AC | 10A | Kritik koruma |

### Fiziksel Özellikler

| Özellik | Değer |
|---------|-------|
| Gövde Malzemesi | PBT (Polybutylene Terephthalate) |
| Terminal Malzemesi | Bakır + Nikel kaplama |
| Boyut | 15mm × 8mm × 4mm |
| Ağırlık | 0.5g |
| Montaj Tipi | PCB mount (through-hole) |
| IP Derecesi | IP00 (açık) |
| Çalışma Sıcaklığı | -30°C ile +150°C |
| Termal Döngü | 10,000 döngü |

### Elektriksel Özellikler

| Özellik | Değer |
|---------|-------|
| Maks. Çalışma Voltajı | 250V AC / 60V DC |
| Maks. Çalışma Akımı | 10A |
| Direnç (kapalı) | ≤ 0.05Ω |
| İzolasyon Direnci | ≥ 100MΩ (250V DC) |
| Dielektrik Dayanım | 1500V AC (1 dk) |
| Ömür | ≥ 100,000 döngü |

## Seçim Kriterleri

### KSD301-70 (CPU Koruması)
1. **Düşük Trip Sıcaklığı**: 70°C, yazılım throttle başlangıcından önce
2. **Hızlı Tepki**: 90 saniye thermal time constant
3. **Yüksek Akım Kapasitesi**: 10A, CPU güç kaynağı yeterli
4. **Küçük Boyut**: PCB mount, minimum yer kaplama

### KSD301-100 (Amplifikatör Koruması)
1. **Orta Trip Sıcaklığı**: 100°C, Class-D amplifikatör için uygun
2. **Yüksek Güç Dayanımı**: 10A, amplifikatör akım gereksinimleri
3. **Termal Kararlılık**: Bimetal strip stabilitesi

### Karşılaştırma Tablosu

| Kriter | KSD301 | SMD Termistör | Dijital Sensör |
|--------|--------|---------------|----------------|
| Güvenilirlik | Yüksek (mekanik) | Orta (elektronik) | Yüksek (dijital) |
| Hız | Orta (90s) | Yüksek (<1s) | Yüksek (<1s) |
| Maliyet | Düşük | Orta | Yüksek |
| Bağımsızlık | Tamamen bağımsız | MCU gerektirir | MCU gerektirir |
| **Uygunluk** | **Son koruma** | **Aktif kontrol** | **Aktif kontrol** |

## Bağımlılıklar

### Girişler
- **K01 Donanım**: CPU ve amplifikatör yerleşim planı
- **K17 Güç Yönetimi**: Güç kaynağı devre şeması
- **K18 Termal Sensörler**: Sıcaklık ölçüm noktaları

### Çıktılar
- **K20 Güvenlik**: Acil durum prosedürleri
- **K13 CI/CD**: Termal koruma test senaryoları
- **K02 Sürücü**: Fan arıza durumunda koruma

### Entegrasyon Noktaları
```
CPU Sıcaklığı → KSD301-70 → Güç Kaynağı Enable → CPU Güç Kesme
                                                        │
Amplifikatör Sıcaklığı → KSD301-100 → Amplifikatör Enable → Amp Güç Kesme
```

## Uygulama Notları

### Montaj Prosedürü
1. KSD301'i PCB üzerindeki montaj deliklerine yerleştir
2. Terminal lehimleme (lehim sıcaklığı ≤ 300°C, 3 sn)
3. Sıcaklık sensörünü KSD301'e 5mm mesafeye monte et
4. Termal iletken macun ile bağlantıyı güçlendir
5. Test: Isıtma ile trip doğrulama

### Test Prosedürü
```
1. KSD301'i 60°C fırında ısıt
2. Sıcaklığı 1°C/dk artır
3. Trip sıcaklığını kaydet (70°C ± 5°C)
4. Soğuma süresini ölç (10 sn max)
5. Reset mekanizmasını doğrula
```

### Bakım ve Değişim
- **Periyot**: 36 ayda bir veya trip sonrası
- **Belirti**: Erken trip veya trip olmama
- **Prosedür**: KSD301'i sök → Yeni KSD301 lehimle → Test et

### Yaygın Hatalar
| Hata | Sonuç | Önleme |
|------|-------|--------|
| Yanlış trip sıcaklığı | Erken/gecikmiş koruma | Doğru model seçimi |
| Kötü lehim bağlantısı | Yüksek direnç, yanlış okuma | Lehim kalite kontrolü |
| Termal köprü eksikliği | Gecikmiş trip | Termal macun kullanımı |
| Mekanik hasar | Tripsiz çalışma | Dikkatli montaj |

## Durum: Implementasyon

KSD301 termal cutoff, COREMUSIC platformunda son savunma hattı olarak onaylanmıştır. KSD301-70 (CPU) ve KSD301-100 (Amplifikatör) modelleri seçilmiş, montaj prosedürleri tanımlanmış ve test senaryoları hazırlanmıştır. Cihaz, bağımsız mekanik koruma sağlayarak yazılımsız güvenlik garantisi vermektedir.
