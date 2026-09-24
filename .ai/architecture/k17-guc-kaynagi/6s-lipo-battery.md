---
title: "6S LiPo Batarya"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# 6S LiPo Batarya

## Genel Bakış

6S LiPo (6 hücre seri, Lithium Polymer) batarya, COREMUSIC platformunun mobil güç kaynağıdır. 22.2V nominal gerilim, 2200mAh kapasite ile 48.8Wh enerji depolar. Yüksek enerji yoğunluğu, düşük iç direnç ve yüksek deşarj oranı (C-rate) ile profesyonel ses ekipmanları için idealdir. BMS entegrasyonu ile hücre dengesi ve koruma sağlanır.

## Pil Konfigürasyonu

```
┌─────────────────────────────────────────────────────────────────┐
│                    6S LiPo Batarya Paketi                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Hücre 1 (3.7V)  ────┐                                        │
│  Hücre 2 (3.7V)  ────┼─── Seri Bağlantı                      │
│  Hücre 3 (3.7V)  ────┤    Toplam: 6 × 3.7V = 22.2V           │
│  Hücre 4 (3.7V)  ────┤                                        │
│  Hücre 5 (3.7V)  ────┤                                        │
│  Hücre 6 (3.7V)  ────┘                                        │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  BMS (Battery Management System)                         │  │
│  │  • Hücre dengesi (balance charging)                      │  │
│  │  • Aşırı deşarj koruması (< 3.0V/hücre)                 │  │
│  │  • Aşırı şarj koruması (> 4.2V/hücre)                   │  │
│  │  • Kısa devre koruması                                    │  │
│  │  • Sıcaklık koruması (NTC)                               │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Hücre Durum Diyagramı

```
Voltaj (V)
  4.2 │ ████████████████████████████████████████████  %100 Şarj
      │ ████████████████████████████████████████████
  4.0 │ ████████████████████████████████████████████  %80
      │ ████████████████████████████████████████████
  3.8 │ ████████████████████████████████████████████  %60
      │ ████████████████████████████████████████████
  3.7 │ ████████████████████████████████████████████  Nominal
      │ ████████████████████████████████████████████
  3.5 │ ████████████████████████████████████████████  %20
      │ ████████████████████████████████████████████
  3.3 │ ████████████████████████████████████████████  Minimum
      │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  Deşarj Limiti
  3.0 │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓
      │                                              Süre (dk)
      0    30    60    90    120   150   180   210
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|-----------|-------|-----|
| Hücre Sayısı | 6S (6 seri) | Lithium Polymer |
| Nominal Gerilim | 22.2V | 3.7V × 6 |
| Tam Şarj Gerilimi | 25.2V | 4.2V × 6 |
| Deşarj Limiti | 18.0V | 3.0V × 6 |
| Kapasite | 2200mAh | 48.8Wh |
| Maks Deşarj Akımı | 44A (20C) | Sürekli |
| Pik Deşarj Akımı | 110A (50C) | 10 saniye |
| Şarj Akımı | 2.2A (1C) | Standart |
| Hızlı Şarj | 4.4A (2C) | Opsiyonel |
| İç Direnç | <25mΩ | Hücre başına |
| Ağırlık | 380g | ±20g |
| Boyutlar | 135×45×25mm | Yaklaşık |
| Çalışma Sıcakığı | -20°C to +60°C | |
| Depolama Sıcakığı | -10°C to +45°C | |

## Şarj Profili

### CC-CV (Constant Current - Constant Voltage) Şarj

```
Akım (A)
  4.4 │ ████████████████████████████░░░░░░░░░░░░░░░░░  2C Hızlı Şarj
      │ ████████████████████████████░░░░░░░░░░░░░░░░░
  2.2 │ ████████████████████████████░░░░░░░░░░░░░░░░░  1C Standart
      │ ████████████████████████████░░░░░░░░░░░░░░░░░
  1.0 │ ████████████████████████████░░░░░░░░░░░░░░░░░  0.5C Yavaş
      │ ████████████████████████████░░░░░░░░░░░░░░░░░
      │                                              
  0.0 │─────────────────────────────────────────────── cv
      │                                              
      │  CC Aşaması  │     CV Aşaması               │
      0    30    60    90    120   150   180   210  Süre (dk)
      
Voltaj (V)
 25.2 │                ████████████████████████████████
      │            ████
 22.2 │        ████
      │    ████
 18.0 │████
      │
      0    30    60    90    120   150   180   210  Süre (dk)
```

### Şarj Aşamaları

1. **Pre-charge (Ön Şarj):** < 3.0V/hücre → 0.1C akım
2. **Constant Current (CC):** 3.0V-4.2V → Sabit akım (1C veya 2C)
3. **Constant Voltage (CV):** 4.2V sabit → Akım azalır
4. **Charge Complete:** Akım < 0.05C → Şarj tamamlandı

## Hücre Dengesi (Cell Balancing)

### Pasif Dengeleme

```
Hücre 1: 4.18V  ──┐
Hücre 2: 4.20V  ──┤  ═══  Direnç ile fazla enerjiyi boşalt
Hücre 3: 4.15V  ──┤      (100Ω, 100mW)
Hücre 4: 4.19V  ──┤
Hücre 5: 4.16V  ──┤
Hücre 6: 4.21V  ──┘
         │
         ▼
    Hepsini 4.18V'a getir
```

### Aktif Dengeleme (Opsiyonel)

```
Hücre Yüksek (4.21V)  ────┐
                           │  ═══  Energy transfer
Hücre Düşük (4.15V)   ────┘      (inductor-based)
         │
         ▼
    Hepsini 4.18V'a getir
    Verimlilik: %85-90
```

## Koruma Devresi

### Aşırı Deşarj Koruması

```
Hücre Voltajı < 3.0V
       │
       ▼
┌──────────────┐
│ Comparator   │  LM393
│ (Ref: 3.0V)  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ MOSFET Gate  │  P-MOSFET
│ Drive        │  IRLML6402
└──────┬───────┘
       │
       ▼
    KESME       Load disconnected
```

### Aşırı Şarj Koruması

```
Hücre Voltajı > 4.2V
       │
       ▼
┌──────────────┐
│ Comparator   │  LM393
│ (Ref: 4.2V)  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Charge FET   │  P-MOSFET
│ Disable      │  IRLML6402
└──────┬───────┘
       │
       ▼
    ŞARJ DURDU    Charger disconnected
```

## Sıcaklık Etkisi

| Sıcaklık | Kapasite | İç Direnç | Şarj Akımı |
|----------|----------|-----------|------------|
| -20°C | %70 | 150% | Yasak |
| 0°C | %85 | 120% | 0.5C max |
| 25°C | %100 | 100% | 1C/2C |
| 45°C | %95 | 90% | 1C |
| 60°C | %80 | 80% | 0.5C |

## Güç Hesaplamaları

```
Toplam Enerji:
E = VNominal × Capacity
E = 22.2V × 2.2Ah
E = 48.8Wh

Ortalama Tüketim (85W):
t = E / P
t = 48.8Wh / 85W
t = 0.574 saat ≈ 34 dakika

Düşük Tüketim (20W):
t = 48.8Wh / 20W
t = 2.44 saat ≈ 146 dakika
```

## Depolama Kuralları

| Durum | Gerilim | Süre | Not |
|-------|---------|------|-----|
| Kısa süreli | 3.8V/hücre | < 1 ay | Oda sıcaklığı |
| Uzun süreli | 3.8V/hücre | < 6 ay | +4°C buzdolabı |
| Depolama | 3.6V/hücre | < 1 yıl | Özel depolama |
| Asla | < 3.0V | - | Kalıcı hasar |

## Bağımlılıklar

| Bileşen | Kullanım |
|---------|----------|
| BMS Kartı | Hücre dengesi, koruma |
| Balance Connector | 7-pin JST-XH |
| Charger (CC-CV) | 25.2V/2.2A |
| Power Connector | XT60 (4-pin) |
| Voltage Monitor | Buzzer/alarm |

## Durum: Implementasyon

✅ 6S LiPo batarya konfigürasyonu belirlendi  
✅ Şarj profili (CC-CV) hesaplandı  
✅ Hücre dengesi devresi tasarlandı  
✅ Koruma eşikleri belirlendi (3.0V-4.2V)  
✅ Sıcaklık etkisi analiz edildi  
✅ Güç hesaplamaları yapıldı (48.8Wh)  
⚠️ Batarya mounting ve vibrasyon testi bekleniyor  
⚠️ Cycle life testi devam ediyor (hedef: 500+ döngü)
