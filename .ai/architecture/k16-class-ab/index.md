---
title: "K16 Class AB Amplifikatör Genel Bakış"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# K16 Class AB Amplifikatör

## Genel Bakış

K16 katmanı, COREMUSIC çok kanallı ses sistemi için **Class AB** tamamlayıcı çıkış transistörlü güç amplifikatörü tasarımı kapsar. Class AB topolojisi, Class B'nin crossover distorsiyonunu azaltırken Class A'nın verimsizliğini ortadan kaldırır. Her bir kanal ≥100W @ 8Ω THD < %0.01 hedefinde tasarlanmıştır.

## Devre Blok Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│  GND Ref ──┐                                                    │
│            │   ┌──────────┐   ┌──────────┐   ┌──────────┐      │
│  Input ────┼──▶│ Diff Pair├──▶│ VAS Stage├──▶│  Output  ├──▶──┤ Output
│  (SE/Bal)  │   │  Input   │   │ (Miller) │   │ Push-Pull│      │  ±35V PSU
│  10kΩ      │   │ Long-Tail│   │ Vbe Mult │   │ MJL21194 │      │
│            │   │  Pair    │   │ Bias     │   │ /21193   │      │
│  Feedback ◀┼───┤          │   │          │   │ Darlington      │
│  Network   │   └──────────┘   └──────────┘   └──────────┘      │
│  (Closed  │        ▲               ▲               ▲            │
│   Loop)   │   Current Mirror   Thermal Track   Short Circuit   │
│            │        │               │               │            │
│            │        ▼               ▼               ▼            │
│            │   ┌──────────────────────────────────────────┐     │
│            └───┤          Koruma Devreleri                │     │
│                │  DC Offset │ Overcurrent │ Thermal │ SC │     │
│                └──────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
```

## Ana Topoloji Özellikleri

| Parametre | Değer |
|---|---|
| Topoloji | Class AB, tamamlayıcı çift |
| Giriş katı | Differential pair, long-tail |
| Gerilim kazancı katı | VAS + Miller kompanzasyonu |
| Çıkış katı | Push-pull, Darlington empedans dönüşümü |
| Geri besleme | Negatif, kapalı döngü ≤0.01% THD |
| Besleme gerilimi | ±35V DC (8Ω için) |
| Çıkış gücü | 100W RMS @ 8Ω, 160W RMS @ 4Ω |
| Frekans tepkisi | 20Hz – 80kHz (±0.5dB) |
| Giriş empedansı | 47kΩ (single-ended) |
| Çıkış empedansı | ≤0.01Ω (döngü içi) |
| Koruma | DC offset, overcurrent, termal, kısa devre |

## Sinyal Akışı

1. **Giriş**: Sinyal, 47kΩ giriş empedansıyla differansiyel giriş katına ulaşır
2. **Diff Pair**: Uzun kuyruklu differansiyel çift, geri besleme ile karşılaştırma yapar
3. **Current Mirror**: Aktif yük olarak gerilim kazancını artırır
4. **VAS**: Tek-ended çıkış sinyali, Miller kompanzasyonu ile bant genişliği kontrolü
5. **Vbe Multiplier**: Çıkış transistörleri için Class AB bias noktası ayarlar
6. **Darlington**: Akım kazancı ve empedans dönüşümü sağlar
7. **Push-Pull Çıkış**: MJL21194 (NPN) / MJL21193 (PNP) tamamlayıcı çift
8. **Geri Besleme**: Çıkıştan girişe negatif geri besleme, distorsiyonu azaltır
9. **Koruma**: DC offset, akım sınırı, termal ve kısa devre koruması devrede kalır

## K16 Modül Bağımlılıkları

| Katman | Bağımlılık | Açıklama |
|---|---|---|
| K0 | İşletim Sistemi | DSP zamanlama, gerçek zamanlı kontrol |
| K1 | Donanım | PCB layout, heatsink montajı |
| K2 | Sürücü | I2C/SPI bias kontrol, dijital pot |
| K3 | Ses Motoru | Analog sinyal girişi, balanced input |
| K15 | Medya | Kaynak sinyal (PCM/DAC çıkışı) |
| K17 | (Varsa) Dijital Sınıflandırma | D amplifikatör hibrit topoloji |

## Tasarım Kısıtlamalari

- **RMS Güç**: 8Ω'da minimum 100W continuous
- **Damping Faktörü**: ≥200 (8Ω yük)
- **THD+N**: %0.01 @ 1kHz, 1W
- **Sinyal/Gürültü**: ≥110dB (A-Weighted)
- **Gecikme Süresi**: ≤1μs (classed-in gecikme)
- **Isı Sınıfı**: AB, idle akımı 50-100mA @ ±35V
- **Koruma**: <100ms tepki süresi

## Durum: Implementasyon
- 20 teknik doküman hazırlandı
- Devre topolojisi ve hesaplamalar tamamlandı
- B bileşen seçimi ve SPICE modelleri belirlendi
