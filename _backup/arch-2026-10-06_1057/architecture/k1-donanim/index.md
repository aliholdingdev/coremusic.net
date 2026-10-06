---
title: "K1 Donanım Katmanı - Genel Bakış"
layer: K1
category: "Donanım"
date: 2026-09-20
---

# K1 Donanım Katmanı

## Genel Bakış

K1, COREMUSIC'ın fiziksel donanım katmanıdır. Tüm dijital ve analog bileşenlerin, PCB'nin, güç kaynağının ve mekanik yapıların tanımlandığı katmandır. Ses sinyalinin dijital kaynaktan hoparlörlere kadar olan fiziksel yolculuğunun temelini oluşturur.

## Blok Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                        K1 DONANIM KATMANI                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐      │
│  │  USB-C   │───▶│  XMOS    │───▶│  I2S     │───▶│  DAC     │      │
│  │  Girişi  │    │  XU316   │    │  Bus     │    │  AK4458  │      │
│  └──────────┘    └──────────┘    └──────────┘    └─────┬────┘      │
│                                                        │           │
│  ┌──────────┐    ┌──────────┐                          │           │
│  │  Analog  │───▶│  ADC     │◀─────────────────────────┘           │
│  │  Girişi  │    │  PCM3168A│                                      │
│  └──────────┘    └──────────┘                                      │
│                                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐      │
│  │  DAC     │───▶│  Diff    │───▶│   VAS    │───▶│  Output  │      │
│  │  Çıkışı  │    │  Pair    │    │  Stage   │    │  Stage   │      │
│  └──────────┘    └──────────┘    └──────────┘    └─────┬────┘      │
│                                                        │           │
│                                                        ▼           │
│                                                  ┌──────────┐      │
│                                                  │ Hoparlör │      │
│                                                  │ 8.1 Sys  │      │
│                                                  └──────────┘      │
│                                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐                      │
│  │  Güç     │───▶│  ±35V    │───▶│  Tüm     │                      │
│  │  Kaynağı │    │  Analog  │    │  Devreler│                      │
│  └──────────┘    └──────────┘    └──────────┘                      │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Bileşen Listesi

| # | Bileşen | Model | Adet | Kategori |
|---|---------|-------|------|----------|
| 1 | USB Audio Controller | XMOS XU316 | 1 | Dijital |
| 2 | ADC | PCM3168A | 1 | Dijital/Analog |
| 3 | DAC | AK4458 | 1 | Dijital/Analog |
| 4 | Class AB Amplifikatör | Özel Tasarım | 8 | Analog |
| 5 | Çıkış Transistörleri | MJL21194/93 | 16 | Güç |
| 6 | Hoparlör Sistemi | 8.1 Surround | 1 set | Çıkış |
| 7 | Güç Kaynağı | LM5122 Dual | 1 | Güç |
| 8 | Konnektörler | XLR/RCA/USB-C | çoklu | Giriş/Çıkış |
| 9 | PCB | 6 Katmanlı | 1 | Yapı |
| 10 | Soğutucu | Alüminyum Ekstrüzyon | 1 | Termal |

## Katman Bağımlılıkları

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | PCB, BOM, mekanik çizimler |
| K2 OS/Sürücüler | Üst | USB sürücü, ALSA/PulseAudio |
| K3 Temel Yazılım | Üst | XMOS firmware, I2S kontrol |

## Teknik Özet

- **Toplam Bileşen Sayısı**: 1.775 (tüm K1 alt dosyaları)
- **PCB Katman Sayısı**: 6 (4 Signal + 2 Power)
- **Güç Topolojisi**: ±35V analog, +5V/+3.3V dijital
- **Maksimum Çıkış Gücü**: 8 × 250W = 2.000W RMS
- **Frekans Aralığı**: 5Hz – 80kHz (±0.5dB)
- **THD+N**: < %0.001 (1kHz, 1W)
- **Sinyal/Gürültü Oranı**: > 120dB (A-Weighted)

## Durum: Implementasyon

**K1 Katman Durumu**: 🟡 Tasarım Aşamasında

| Alt Modül | Durum | Not |
|-----------|-------|-----|
| XMOS XU316 | 🟢 Hazır | USB Audio Class 2.0 firmare mevcut |
| PCM3168A ADC | 🟢 Hazır | Pin konfigürasyonu belirlendi |
| AK4458 DAC | 🟢 Hazır | DSD modu yapılandırıldı |
| Class AB Amp | 🟡 Devam | Simülasyon aşamasında |
| Güç Kaynağı | 🟡 Devam | LM5122 layout çalışıyor |
| PCB | 🔴 Başlamadı | 6-katman stackup planlandı |
| Soğutma | 🔴 Başlamadı | Termal simülasyon bekliyor |
| Koruma Devreleri | 🔴 Başlamadı | Şematiği hazır, layout yok |
