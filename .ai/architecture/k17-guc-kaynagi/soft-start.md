---
title: "Soft Start Devresi"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Soft Start Devresi (Inrush Current Limiting)

## Genel Bakış

Soft start devresi, COREMUSIC'in açılış anındaki yüksek giriş akımını (inrush current) sınırlayarak bileşenleri ve güç kaynağını korur. Bulk kapasitörlerin şarj olmasını kontrollü bir şekilde sağlar,++]= MOSFET'lerin güvenli çalışmasını garanti eder. RC zaman sabitli analog soft start ile dijital kontrolcü tabanlı sequenced start kombinasyonu kullanılır.

## Çalışma Prensibi

### Inrush Current Problemi

```
Açılış Anı (Soft Start YOK):
─────────────────────────────
VOUT
  │         ┌─────────────── 35V
  │        /
  │       /
  │      /
  │     /
  │────┘
  │
  │    │← Inrush Region →│
  │    │    (1-10μs)       │
  0────┴─────────────────────▶ Süre
           
IIN
  │
  │    ████
  │   ██████
  │  ████████
  │ ██████████  50A+ (Inrush)
  │████████████
  │████████████
  0───────────────────────────▶ Süre
    │← 10μs →│
```

### Soft Start ile

```
Açılış Anı (Soft Start VAR):
─────────────────────────────
VOUT
  │                    ┌───── 35V
  │                 ╱
  │              ╱
  │           ╱
  │        ╱
  │     ╱
  │  ╱
  │╱
  0───────────────────────────▶ Süre
    │← 500ms →│
           
IIN
  │
  │  ████
  │  ████
  │  ████  5A (Sınırlı)
  │  ████
  │  ████
  0───────────────────────────▶ Süre
    │← 500ms →│
```

## Devre Tasarımı

### Analog Soft Start (RC Tabanlı)

```
           VCC (3.3V)
              │
              R_SS (100kΩ)
              │
              ├────────────▶ Enable Pin (LM5122)
              │
            C_SS (10μF)
              │
             GND

Zaman Sabiti:
τ = R_SS × C_SS
τ = 100kΩ × 10μF
τ = 1.0 saniye

Yükselme Süresi (%10 to %90):
tRISE = 2.2 × τ
tRISE = 2.2 × 1.0s
tRISE = 2.2 saniye
```

### Soft Start Voltaj Profili

```
V_SS (Enable Pin)
  │
3.3V│                    ┌───────────
    │                 ╱
    │              ╱
    │           ╱
    │        ╱
    │     ╱
    │  ╱
    │╱
  0V└──────────────────────────────────▶ Süre
    │← τ=1.0s →│
    │← tRISE=2.2s →│
```

### Dijital Soft Start (MCU Tabanlı)

```
┌──────────────────────────────────────────────────────────────┐
│  STM32L4 Soft Start Kontrol                                   │
├──────────────────────────────────────────────────────────────┤
│                                                                │
│  // PWM ile rampa oluşturma                                   │
│  void soft_start_ramp(void) {                                 │
│    for (int duty = 0; duty <= 100; duty++) {                  │
│      TIM2->CCR1 = duty;  // PWM duty cycle                   │
│      delay_ms(5);          // 5ms adımlar                     │
│    }                                                          │
│    // Toplam süre: 100 × 5ms = 500ms                         │
│  }                                                            │
│                                                                │
│  // Aşırı akım kontrolü                                       │
│  if (ADC_Read(CURRENT_SENSE) > INRUSH_LIMIT) {               │
│    TIM2->CCR1 = 0;  // Acil durdurma                         │
│    fault_handler();                                           │
│  }                                                            │
│                                                                │
└──────────────────────────────────────────────────────────────┘
```

## Rail Sıralaması (Power Sequencing)

```
┌───────────────────────────────────────────────────────────────┐
│  GÜÇ RAIL SIRALAMASI                                          │
├───────────────────────────────────────────────────────────────┤
│                                                                 │
│  Zaman (ms)    Rail         Durum                              │
│  ─────────────────────────────────────────────────────────     │
│     0          Batarya      Bağlandı                            │
│     │                                                           │
│    50          +5V          İlk açılan (USB, MCU)              │
│    │                                                           │
│   100          +3.3V        FPGA ve SRAM                       │
│    │                                                           │
│   200          +15V         Op-amp ve DAC                      │
│    │                                                           │
│   250          -15V         Negatif analog rail                │
│    │                                                           │
│   300          +12V         Audio preamp                       │
│    │                                                           │
│   350          -12V         Negatif preamp                     │
│    │                                                           │
│   400          +35V         Analog synth (son)                 │
│    │                                                           │
│   450          -35V         Negatif synth                      │
│    │                                                           │
│   500          PGOOD        Tüm rail'ler hazır                 │
│    │                                                           │
│   550          MCU START    Sistem başlatılabilir               │
│                                                                 │
└───────────────────────────────────────────────────────────────┘
```

## Sequencing Devresi

### RC Delay Ağı

```
Her rail için ayrı RC delay:

+5V Rail:
R1 = 10kΩ, C1 = 1μF → τ1 = 10ms
Enable after 5×τ1 = 50ms

+3.3V Rail:
R2 = 10kΩ, C2 = 2μF → τ2 = 20ms
Enable after 5×τ2 = 100ms

+15V Rail:
R3 = 10kΩ, C3 = 4μF → τ3 = 40ms
Enable after 5×τ3 = 200ms
```

### Sequencing Şeması

```
        ┌──────────────────────────────────────────────────────────┐
        │  ADC ile rail gerilimleri izleniyor                      │
        │                                                          │
        │  If V_5V > 4.5V AND V_3V3 > 3.0V:                      │
        │    Enable +15V                                           │
        │                                                          │
        │  If V_15V > 13.5V:                                      │
        │    Enable -15V                                           │
        │                                                          │
        │  If V_15V > 13.5V AND V_N15V < -13.5V:                  │
        │    Enable +12V, -12V                                     │
        │                                                          │
        │  If V_12V > 10.8V AND V_N12V < -10.8V:                  │
        │    Enable +35V, -35V                                     │
        │                                                          │
        │  If ALL rails OK:                                        │
        │    PGOOD = HIGH                                          │
        │    MCU can start                                         │
        └──────────────────────────────────────────────────────────┘
```

## Inrush Hesaplamaları

### Bulk Kapasitör Şarj Akımı

```
Toplam Bulk Kapasite:
C_BULK = 470μF × 4 (paralel) = 1880μF

Inrush Akımı (Soft Start YOK):
I_INRUSH = C × dV/dt
I_INRUSH = 1880μF × (35V / 1μs)
I_INRUSH = 65.8A (1μs süresince)

Soft Start ile (tRISE = 500ms):
I_INRUSH = C × dV/dt
I_INRUSH = 1880μF × (35V / 500ms)
I_INRUSH = 131.6mA (çok düşük!)
```

### Enerji Hesabı

```
Depolanan Enerji:
E = 0.5 × C × V²
E = 0.5 × 1880μF × (35V)²
E = 1.15 Joule

Güç Kaynağı Kapasitesi (6S LiPo):
P_MAX = 22.2V × 10A = 222W
E_AVAILABLE = P_MAX × t
E_AVAILABLE = 222W × 500ms
E_AVAILABLE = 111 Joule (yeterli)
```

## Koruma Devresi

### Aşırı Akım Kilidi

```
┌─────────────────────────────────────────────────────────┐
│  Inrush Koruma Devresi                                    │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  Current Sense (ACS711)                                   │
│       │                                                   │
│       ▼                                                   │
│  ┌─────────────┐                                         │
│  │ Comparator   │  LM393                                  │
│  │ V+ = I_SENSE │                                         │
│  │ V- = V_REF   │  (5A threshold)                        │
│  └──────┬──────┘                                         │
│         │                                                 │
│         ▼                                                 │
│  ┌─────────────┐                                         │
│  │ SR Latch     │  74HC74                                 │
│  │ S = COMP_OUT │                                         │
│  │ R = RESET    │                                         │
│  └──────┬──────┘                                         │
│         │                                                 │
│         ▼                                                 │
│  ┌─────────────┐                                         │
│  │ MOSFET Gate  │  Disable soft-start FET                 │
│  │ Drive        │                                         │
│  └─────────────┘                                         │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

## Spesifikasyonlar

| Parametre | Değer | Birim |
|-----------|-------|-------|
| Soft Start Süresi | 500 | ms |
| Inrush Akımı (max) | 5 | A |
| Inrush Süresi | <5 | ms |
| Rail Sequencing | 100 | ms/adım |
| Toplam Başlama Süresi | 550 | ms |
| PGOOD Gecikmesi | 50 | ms |
| Fault Response | <10 | μs |
| Çalışma Sıcaklığı | -40 to +85 | °C |

## Bileşen Seçimi

| Bileşen | Değer | Paket | Kullanım |
|---------|-------|-------|----------|
| R_SS | 100kΩ | 0805 | RC time constant |
| C_SS | 10μF | 0805 | RC time constant |
| R_DELAY | 10kΩ | 0805 | Rail sequencing |
| C_DELAY | 1-10μF | 0805 | Rail sequencing |
| LM393 | - | SOIC-8 | Fault comparator |
| 74HC74 | - | SOIC-16 | SR latch |

## PCB Tasarım Notları

1. **RC Ağı:** Soft-start bileşenleri LM5122 EN pinine yakın
2. **Sequencing:** Her rail için ayrı delay ağı
3. **Current Sense:** Kelvin connection, short traces
4. **Fault Signals:** Away from power traces, shielded
5. **Test Noktaları:** Her rail için test point

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-LM5122 | Enable pin bağlantısı |
| K17-BulkCaps | Şarj akımı hesabı |
| K17-CurrentSensing | Aşırı akım koruması |
| K10-MCU | Dijital kontrol |

## Durum: Implementasyon

✅ Analog soft start devresi (RC) tasarlandı  
✅ Dijital soft start PWM kodu yazıldı  
✅ Rail sequencing mantığı belirlendi  
✅ Inrush akım hesaplamaları yapıldı (5A limit)  
✅ Koruma devresi (comparator + latch) entegre edildi  
✅ PGOOD sinyal mantığı uygulandı  
⚠️ Sıcaklık testi bekleniyor  
⚠️ Tüm rail'ler için sequencing tolerans testi devam ediyor
