---
title: "K17 Güç Kaynağı Katmanı"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# K17 Güç Kaynağı Katmanı

## Genel Bakış

K17, COREMUSIC platformunun tüm donanım katmanlarını besleyen merkezi güç yönetim sistemini tanımlar. 6S LiPo bataryadan (22.2V nominal) başlayarak, ±35V, ±15V, ±12V, +5V ve +3.3V rail'lerine kadar çoklu çıkış sağlar. Sistem, yüksek verimlilik (>%92), düşük EMI ve askeri standartlarda koruma seviyeleri sunar.

## Güç Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        K17 GÜÇ KAYNAĞI MİMARİSİ                        │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────────────────┐  │
│  │  6S LiPo     │───▶│  OR-ing      │───▶│  LM5122 Dual Boost      │  │
│  │  22.2V       │    │  Circuit     │    │  Converter              │  │
│  │  2200mAh     │    │  (Rv. Pol.)  │    │  ±35V @ 2A              │  │
│  └──────────────┘    └──────────────┘    └──────────┬───────────────┘  │
│         │                                           │                  │
│         │              ┌──────────────┐             │                  │
│         └─────────────▶│  BMS         │             │                  │
│                        │  (Cell       │             │                  │
│                        │  Balancing)  │             ▼                  │
│                        └──────────────┘    ┌──────────────────────────┐│
│                                            │  Güç Rail'leri           ││
│                                            │  ┌────────────────────┐ ││
│                                            │  │ +35V / -35V        │ ││
│                                            │  │ (Analog Synth)     │ ││
│                                            │  ├────────────────────┤ ││
│                                            │  │ +15V / -15V        │ ││
│                                            │  │ (Op-Amp, DAC)      │ ││
│                                            │  ├────────────────────┤ ││
│                                            │  │ +12V / -12V        │ ││
│                                            │  │ (Audio Preamp)     │ ││
│                                            │  ├────────────────────┤ ││
│                                            │  │ +5V (Logic, USB)   │ ││
│                                            │  ├────────────────────┤ ││
│                                            │  │ +3.3V (MCU, FPGA)  │ ││
│                                            │  └────────────────────┘ ││
│                                            └──────────────────────────┘│
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  KORUMA SİSTEMİ                                                  │  │
│  │  • Soft Start (Inrush Limiting)                                  │  │
│  │  • Overcurrent Protection (ACS711)                               │  │
│  │  • Thermal Shutdown (NTC + Comparator)                           │  │
│  │  • Reverse Polarity (MOSFET OR-ing)                              │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

## Güç Rail'leri ve Yük Dağılımı

| Rail | Gerilim | Maks Akım | Güç | Kullanım |
|------|---------|-----------|-----|----------|
| +35V | +35V ±0.5% | 2A | 70W | Analog synth oscilatörleri, VCF |
| -35V | -35V ±0.5% | 2A | 70W | Negatif analog rail |
| +15V | +15V ±1% | 500mA | 7.5W | Op-amp, DAC, buffer |
| -15V | -15V ±1% | 500mA | 7.5W | Negatif op-amp rail |
| +12V | +12V ±2% | 1A | 12W | Audio preamp, motor |
| -12V | -12V ±2% | 500mA | 6W | Negatif preamp |
| +5V | +5V ±2% | 3A | 15W | USB, logik, LED |
| +3.3V | +3.3V ±3% | 2A | 6.6W | MCU, FPGA, SRAM |

**Toplam Maksimum Güç:** ~194W (pik), ortalama 85W

## Topoloji Seçimi

### Neden Dual Boost (LM5122)?

1. **Yüksek Verimlilik:** %94'e varan verim, lineer regülatörlere kıyasla %60 tasarruf
2. **İkiz Çıkış:** Tek entegre ile +35V ve -35V üretimi
3. **Geniş Giriş Aralığı:** 4.5V-60V giriş, 6S LiPo için ideal
4. **Düşük Ripple:** 50mVp-p altında çıkış ripple
5. **Synchronous Rectification:** Düşük iletim kayıpları

### Rail Sıralaması

```
Batarya Bağlantısı
    │
    ▼
┌─────────────┐
│ Soft Start  │  t = 0ms → 500ms (ramp)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ +5V Rail    │  t = 100ms (ilk açılan)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ +3.3V Rail  │  t = 200ms
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ ±15V Rail   │  t = 300ms
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ ±12V Rail   │  t = 400ms
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ ±35V Rail   │  t = 500ms (son açılan)
└─────────────┘
```

## Koruma Katmanları

### 1. Giriş Koruması
- **Reverse Polarity:** P-MOSFET OR-ing, RDS(on) < 10mΩ
- **TVS Diode:** 30V standoff, 1.5kW pul power
- **Fuse:** 10APTC, otomatik reset

### 2. Çıkış Koruması
- **Overcurrent:** ACS711, ±31A aralık, 125mV/A sensitivity
- **Short Circuit:** <10μs response time
- **Overvoltage:** Zener clamp + crowbar SCR

### 3. Termal Koruma
- **NTC Thermistor:** 10kΩ @ 25°C, B=3950
- **Shutdown Threshold:** 85°C board, 105°C junction
- **Hysteresis:** 10°C cooldown

## Verimlilik Hedefleri

| Durum | Verimlilik |power_loss |
|-------|-----------|-----------|
| Nominal (85W) | %92 | 7.3W |
| Düşük Yük (20W) | %88 | 2.7W |
| Pik (194W) | %90 | 21.6W |

## EMC Spesifikasyonları

- **Conducted Emissions:** CISPR 32 Class B (<30MHz)
- **Radiated Emissions:** CISPR 32 Class B (>30MHz)
- **ESD Immunity:** IEC 61000-4-2, ±8kV contact, ±15kV air
- **Surge Immunity:** IEC 61000-4-5, ±2kV line-to-line

## Bağımlılıklar

| Katman | Bağımlılık Tipi | Açıklama |
|--------|----------------|----------|
| K0 | Donanım platformu | PCB montaj, termal tasarım |
| K1 | Donanım arayüzü | ADC/DAC referans gerilimleri |
| K3 | Ses motoru | ±35V analog synth besleme |
| K4 | Yapay zeka | +3.3V FPGA/MCU besleme |
| K16 | Test altyapısı | Güç test noktaları, kalibrasyon |

## Durum: Implementasyon

✅ LM5122 dual boost converter devre tasarımı tamamlandı  
✅ 6S LiPo batarya management sistemi entegre edildi  
✅ ±35V, ±15V, ±12V, +5V, +3.3V rail'ler aktif  
✅ Soft start ve inrush limiting çalışıyor  
✅ Overcurrent koruma test edildi  
✅ EMC filtreleme doğrulandı  
⚠️ Termal testler devam ediyor (Junction sıcaklık optimizasyonu)  
⚠️ Efficiency sweep ölçümü bekleniyor
