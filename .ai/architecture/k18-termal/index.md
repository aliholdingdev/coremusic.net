---
title: "K18 Termal Tasarım Katmanı"
layer: K18
category: "Termal Tasarım"
date: 2026-09-20
---

# K18 Termal Tasarım Katmanı

## Genel Bakış

K18 Termal Tasarım katmanı, COREMUSIC donanım platformlarının sıcaklık yönetimi için kapsamlı bir mühendislik çerçevesi sunar. Bu katman, passive ve active soğutma stratejilerini, termal koruma mekanizmalarını ve termal simülasyon metodolojilerini entegre ederek cihazların güvenilir çalışma sıcaklıklarında kalmasını sağlar. Termal tasarım, notamment yüksek güçlü amplifikatörler ve CPU/GPU bileşenleri için kritik öneme sahiptir.

## Mimari Yapı

```
┌─────────────────────────────────────────────────────┐
│              K18 Termal Tasarım Katmanı              │
├─────────────┬──────────────┬────────────────────────┤
│  Passive    │   Active     │    Koruma              │
│  Soğutma    │   Soğutma    │    Mekanizmaları       │
├─────────────┼──────────────┼────────────────────────┤
│ • Heatsink  │ • PWM Fan    │ • KSD301 Cutoff        │
│ • Thermal   │ • Fan        │ • Termal Throttle      │
│   Pad       │   Controller │ • Acil Kapatma         │
│ • Heat Pipe │ • Hava       │ • Software Limit       │
│ • Enclosure │   Akışı      │                        │
│   Tasarımı  │              │                        │
└─────────────┴──────────────┴────────────────────────┘
         │              │                │
         ▼              ▼                ▼
┌─────────────────────────────────────────────────────┐
│         Termal Simülasyon & Analiz                  │
│   • CFD Analizi  • Termal Direnç Hesapları          │
│   • Ambient Analizi  • Isı Haritası                 │
└─────────────────────────────────────────────────────┘
```

## Bileşen Katalogu

| Dosya | Bileşen | Açıklama |
|-------|---------|----------|
| `fischer-heatsink.md` | Fischer SK53 Heatsink | Passive soğutma için heatsink spesifikasyonları |
| `thermal-pad.md` | Termal Ped | Isı iletim pedi seçimi ve spesifikasyonları |
| `ksd301-cutoff.md` | KSD301 Termal Cutoff | Aşırı sıcaklık koruma mekanizması |
| `pwm-fan-control.md` | PWM Fan Kontrolü | Aktif fan hızı kontrolü ve sıcaklık eğrisi |
| `thermal-simulation.md` | Termal Simülasyon | CFD analiz metodolojisi |
| `ambient-temperature.md` | Ortam Sıcaklığı | Çalışma ortamı sıcaklık değerlendirmesi |
| `thermal-resistance.md` | Termal Direnç | Rθ hesaplamaları ve analizi |
| `heat-pipe-design.md` | Heat Pipe Tasarımı | Kompakt tasarımlar için ısı borusu seçenekleri |
| `enclosure-thermal.md` | Kasa Termal Tasarımı | Kasa havalandırma ve akış tasarımı |

## Termal Hedefler

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Maksimum CPU Sıcaklığı | 85°C | Throttle başlangıcı |
| KSD301 Trip Sıcaklığı | 100°C | Donanım koruma eşiği |
| Çalışma Ortamı | 0°C - 40°C | Tasarım sıcaklık aralığı |
| Depolama Sıcaklığı | -20°C - 70°C | Non-operational aralık |
| Heatsink Thermal Resistance | ≤ 2.5°C/W | Aktif soğutma ile |
| Fan Ömrü | ≥ 50,000 saat | MTBF garantisi |

## Isı Kaynakları

| Kaynak | Güç (W) | Öncelik |
|--------|---------|---------|
| Ana İşlemci (ARM Cortex-A72) | 15 | Yüksek |
| Audio DAC (ESS Sabre) | 2 | Düşük |
| Güç Amplifikatörü (Class-D) | 25 | Yüksek |
| HDMI Transceiver | 3 | Orta |
| WiFi/BT Modülü | 1.5 | Düşük |
| Güç Kaynağı (PSU) | 8 | Orta |
| **Toplam** | **54.5** | - |

## Termal Akış Diyagramı

```
Isı Kaynağı (CPU, Amp, DAC)
        │
        ▼
┌──────────────────┐
│  Termal Ped /    │  Interface Malzemesi
│  TIM Seçimi     │  (Thermal Interface Material)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Heatsink /      │  Passive Soğutma
│  Heat Pipe       │  (Fischer SK53 / Copper Heat Pipe)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Fan / Konveksiyon│  Active Soğutma
│  Hava Akışı      │  (PWM Fan,自然 hava akımı)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Enclosure /     │  Kasa Tasarımı
│  Dış Yüzey       │  (Alüminyum, havalandırma delikleri)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Ortam Sıcaklığı │  Sınırlayıcı Faktör
│  (Ambient)       │  (0-40°C çalışma aralığı)
└──────────────────┘
```

## Bağımlılıklar

### Üst Katmanlar
- **K06 Ses Motoru**: Ses processorü için termal gereksinimler
- **K17 Güç Yönetimi**: Güç tüketimi profili ve termal korelasyon
- **K02 Sürücü Katmanı**: Fan sürücü ve sensör entegrasyonu

### Yan Katmanlar
- **K01 Donanım**: Fiziksel bileşen yerleşimi ve malzeme özellikleri
- **K03 Çekirdek**: CPU/GPU termal profili ve throttle davranışları
- **K13 CI/CD**: Termal test otomasyonu ve kalite kapıları

### Alt Katmanlar
- **K19 Sensörler**: Sıcaklık sensörleri (NTC, thermistor, dijital sensörler)
- **K20 Güvenlik**: Termal koruma mekanizmaları ve acil durum prosedürleri

## Termal Yönetim Stratejisi

### Zones (Bölge) Yaklaşımı
```
Zone 1: CPU + RAM → Yüksek öncelikli soğutma
Zone 2: Audio Amplifikatör → Orta öncelikli soğutma
Zone 3: Güç Kaynağı → Orta öncelikli soğutma
Zone 4: RF Modülleri → Düşük öncelikli soğutma
Zone 5: Depolama (SSD/eMMC) → Düşük öncelikli soğutma
```

### Hiyerarşik Koruma
1. **Yazılım Throttle**: CPU frekans düşürme (85°C'de)
2. **PWM Fan Hızlandırma**: Fan RPM artırımı (75°C'de)
3. **KSD301 Cutoff**: Donanım koruma (100°C'de)
4. **Acil Kapatma**: Sistem kapatma (105°C'de)

## Doğrulama ve Test

| Test | Metod | Kriter |
|------|-------|--------|
| Thermal Runaway | Aşırı yük testi | KSD301 tetikleme |
| Fan Arızası | Fan kapatma testi | Throttle başlangıcı |
| Ambient Extremes | İklim odası testi | 0-40°C çalışma |
| Long-term Burn-in | 72 saat sürekli çalışma | Sıcaklık kararlılığı |
| Heat Pipe Performansı | Isı iletim testi | ≥ 80°C verimlilik |

## Durum: Implementasyon

K18 Termal Tasarım katmanı, COREMUSIC platformunun güvenilirliği için temel bir mühendislik katmanıdır. Pasif ve aktif soğutma stratejileri entegre edilmiş, termal koruma mekanizmaları tanımlanmış ve simülasyon metodolojisi belirlenmiştir. Tüm termal bileşenler, başlangıç tasarım aşamasından itibaren sistem mimarisine entegre edilmiştir.
