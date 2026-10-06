---
title: "Termal Yönetim"
layer: K1
category: "Termal Sistemler"
date: 2026-09-20
---

# Termal Yönetim

## Genel Bakış

Termal yönetim sistemi, COREMUSIC amplifikatörünün tüm bileşenlerini güvenli çalışma sıcaklıklarında tutar. Soğutucu seçimi, termal ped, sıcaklık izleme ve fan kontrolü dahildir. MJL21194/93 transistörleri için en kritik termal tasarım uygulanır.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Maks. Ortam Sıcaklığı | 40°C |
| Maks. Cihaz Sıcaklığı | 85°C (transistör junction) |
| Toplam Isı Dağılımı | 350W (8 kanal tam yükte) |
| Soğutucu Kapasitesi | 0.5°C/W (hedef) |
| Fan Hızı | 0-3000 RPM (PWM kontrollü) |
| Sıcaklık Sensörü | NTC 10kΩ @ 25°C |
| Isı Pedleri | Bergquist Sil-Pad 1500 |

## Isı Dağılım Analizi

###.mjle21194-93 Isı Üretimi

| Durum | IC (A) | VCE (V) | PC (W) | Sıcaklık Yükselmesi |
|-------|--------|---------|--------|---------------------|
| Boşta | 0.05 | 70 | 3.5 | 4.3°C |
| 10W | 0.5 | 35 | 17.5 | 21.6°C |
| 50W | 1.8 | 19.4 | 35 | 42.7°C |
| 100W | 3.5 | 10 | 35 | 42.7°C |
| 250W | 5.6 | 6.25 | 35 | 42.7°C |

### Toplam Isı (8 Kanal)

| Durum | Kanal Başına | Toplam | Fan Hızı |
|-------|-------------|--------|----------|
| Boşta | 7W | 56W | %20 (600 RPM) |
| Orta | 40W | 320W | %60 (1800 RPM) |
| Tam Yük | 45W | 360W | %100 (3000 RPM) |

## Soğutucu Seçimi

### Alüminyum Ekstrüzyon Soğutucu

| Parametre | Değer |
|-----------|-------|
| Malzeme | 6063-T5 Alüminyum |
| Boyut | 300mm × 100mm × 50mm |
| Fin Sayısı | 20 |
| Fin Yüksekliği | 45mm |
| Fin Kalınlığı | 1.5mm |
| Tab Kalınlığı | 5mm |
| Ağırlık | 1.2kg |
| RθSA | 0.45°C/W |

### Termal Ped

| Parametre | Değer |
|-----------|-------|
| Model | Bergquist Sil-Pad 1500 |
| Termal Direnç | 0.35°C/in² |
| Kalınlık | 0.18mm |
| Boyut | 25mm × 25mm (transistör başına) |
| Dielectric Strength | 6000V AC |

## Sıcaklık İzleme

### NTC Sensör Devresi

```
              +3.3V
               │
           ┌───┴───┐
           │  R1   │  10kΩ (Pull-up)
           └───┬───┘
               │
           ┌───┴───┐
           │  NTC  │  10kΩ @ 25°C
           │ Sensor│  β = 3950
           └───┬───┘
               │
               ├──────▶ ADC Input (PCM3168A spare channel)
               │
              GND
```

### Sıcaklık-Hassasiyet Tablosu

| Sıcaklık (°C) | NTC Direnci (kΩ) | ADC Voltajı (V) | ADC Değeri |
|---------------|-------------------|-----------------|------------|
| 25 | 10.0 | 1.65 | 2048 |
| 50 | 3.60 | 0.89 | 1114 |
| 75 | 1.52 | 0.46 | 576 |
| 85 | 1.14 | 0.35 | 437 |
| 100 | 0.87 | 0.28 | 350 |

## Fan Kontrolü

### PWM Fan Driver

```
MCU (XMOS GPIO)
     │
     ├─ PWM Signal ──▶ MOSFET Gate (IRLML6344)
     │                   │
     │                   ▼
     │              Fan Motor
     │                   │
     │                   ▼
     │                  GND
     │
     └─ TACH Signal ◀── Fan Tachometer

Fan Speed Control:
- 0-25°C: Fan kapalı (0% PWM)
- 25-50°C: Lineer artış (0-50% PWM)
- 50-75°C: Lineer artış (50-80% PWM)
- 75-85°C: Maksimum hız (100% PWM)
- >85°C: Sistem kapatma (thermal shutdown)
```

## Termal Pad Uygulaması

```
Transistör Mounting:

     ┌─────────────┐
     │  MJL21194   │  TO-264 Package
     │  (Tab)      │
     └──────┬──────┘
            │
     ┌──────┴──────┐
     │  Sil-Pad    │  0.18mm thermal interface
     │  1500       │
     └──────┬──────┘
            │
     ┌──────┴──────┐
     │  Heatsink   │  Alüminyum ekstrüzyon
     │  (Tab)      │
     └─────────────┘

Mounting Torque: 0.5 Nm (M3 vidalar)
Thermal Resistance: RθCS = 0.35°C/in² × 6.25cm² = 0.22°C/W
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Soğutucu mekanik çizimleri |
| K1 Amplifikatör | Bağlantı | Transistör mounting |
| K1 Koruma | Bağlantı | Thermal shutdown |
| K2 OS/Sürücüler | Üst | Fan PWM control |

## Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- Soğutucu: Alüminyum ekstrüzyon profile seçildi
- Termal simülasyon: ANSYS Icepak ile yapılacak
- Fan: Noctua NF-A12x25 (120mm, PWM)
- Termal pad: Bergquist Sil-Pad 1500 onaylandı
- Mounting hardware: M3×8mm vidalar, termal ped, washer
- Prototype: İlk prototip için termal test planı hazır
