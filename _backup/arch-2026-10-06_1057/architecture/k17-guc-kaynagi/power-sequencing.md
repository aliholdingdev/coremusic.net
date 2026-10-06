---
title: "Güç Sıralama Devresi"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Güç Sıralama Devresi (Power Sequencing)

## Genel Bakış

Güç sıralama devresi, COREMUSIC'in tüm güç rail'lerinin doğru zamanda açılmasını ve kapanmasını sağlar. Yanlış sırada açılan rail'ler, latch-up, aşırı akım veya bileşen hasarına neden olabilir. STM32L4 MCU tabanlı aktif sıralama ile her rail için enable/disable zamanlaması kontrol edilir.

## Sıralama Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    GÜÇ SIRALAMA MİMARİSİ                              │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  BAT_EN ────────────────────────────────────────────── HIGH         │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +5V_EN ─────────────────────────────────────── HIGH                │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +3V3_EN ───────────────────────────────── HIGH                    │
│       │                                                              │
│       ▼ +50ms                                                        │
│  MCU_BOOT ──────────────────────────── HIGH                        │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +15V_EN ────────────────────── HIGH                               │
│       │                                                              │
│       ▼ +50ms                                                        │
│  -15V_EN ──────────────── HIGH                                     │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +12V_EN ────────── HIGH                                           │
│       │                                                              │
│       ▼ +50ms                                                        │
│  -12V_EN ──── HIGH                                                 │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +35V_EN ── HIGH                                                   │
│       │                                                              │
│       ▼ +50ms                                                        │
│  -35V_EN HIGH                                                       │
│       │                                                              │
│       ▼ +50ms                                                        │
│  PGOOD ──────── HIGH (tüm rail'ler stabil)                         │
│       │                                                              │
│       ▼ +50ms                                                        │
│  MCU_START HIGH (sistem başlatılabilir)                            │
│                                                                      │
│  Toplam Başlama Süresi: 600ms                                      │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Enable Devresi

### P-MOSFET Enable

```
MCU GPIO (3.3V) ────┬──── Gate
                    │
                  R (10kΩ)
                    │
              VCC (rail)

P-MOSFET (IRLML6402):
• MCU HIGH → VGS = 0V → MOSFET OFF → Rail OFF
• MCU LOW → VGS = -VCC → MOSFET ON → Rail ON
```

### Optocoupler Enable (Yüksek Gerilim Rail'ler)

```
MCU GPIO ──── R ──── Optocoupler LED
                         │
                    Phototransistor
                         │
                    VCC Rail ── Gate

Avantaj: Galvanik izolasyon (MCU vs 35V)
```

## PGOOD Mantığı

```
┌──────────────────────────────────────────────────────┐
│  PGOOD = AND(V_5V_OK, V_3V3_OK, V_15P_OK,          │
│              V_15N_OK, V_12P_OK, V_12N_OK,          │
│              V_35P_OK, V_35N_OK)                    │
│                                                      │
│  V_Rail_OK = (|V measured - V nominal| < 10%)       │
│                                                      │
│  Debounce: 10ms (tüm rail'ler stabil olduktan sonra) │
└──────────────────────────────────────────────────────┘
```

## UVLO (Under-Voltage Lock-Out)

| Rail | Turn-on | Turn-off | Hysteresis |
|------|---------|----------|------------|
| Batarya | 20V | 16.8V | 3.2V |
| +5V | 4.5V | 4.0V | 0.5V |
| +3.3V | 3.0V | 2.7V | 0.3V |
| ±35V | ±32V | ±28V | ±4V |

## Kapanma Sıralaması

```
t=0ms:    +35V, -35V disable (ilk kapanan)
t=50ms:   +12V, -12V disable
t=100ms:  +15V, -15V disable
t=200ms:  +3.3V disable
t=250ms:  +5V disable (son kapanan)
```

## MCU State Machine

```c
typedef enum {
    SEQ_IDLE, SEQ_BAT, SEQ_5V, SEQ_3V3,
    SEQ_MCU_BOOT, SEQ_15V, SEQ_N15V,
    SEQ_12V, SEQ_N12V, SEQ_35V, SEQ_N35V,
    SEQ_PGOOD, SEQ_RUN, SEQ_ERROR
} SeqState;
```

## Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Başlama Süresi | 600ms |
| Kapanma Süresi | 300ms |
| Adım Süresi | 50ms |
| PGOOD Gecikmesi | 10ms |
| Emergency Response | <1μs |

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-LM5122 | ±35V enable |
| K17-VoltageReg | ±15V, ±12V enable |
| K17-BMS | Batarya bağlantısı |
| K1-MCU | GPIO kontrolü |

## Durum: Implementasyon

✅ Sıralama zamanlaması belirlendi (50ms adım)  
✅ Enable lojik devresi tasarlandı  
✅ PGOOD mantığı uygulandı  
✅ UVLO eşikleri ayarlandı  
✅ State machine firmware yazıldı  
✅ Kapanma sıralaması dokümante edildi  
⚠️ Tüm rail sweep testi bekleniyor  
⚠️ Sıcaklık altında timing testi devam ediyor
