---
type: electronic
category: test-protocol
title: "CoreMusic — 8-Channel Class AB Amplifier Test Protocol"
date: 2026-09-18
updated: 2026-09-18
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/test-protocol.md"
  source_of_truth:
    - ".ai/architecture/8ch-integration.md"
    - ".ai/architecture/test-fixture.md"
    - ".ai/architecture/amplifier-classab-circuit.md"
    - ".ai/brain.md"
    - "ADR-061-electronics-architecture"
  related:
    - ".ai/architecture/test-fixture.md"
---

# CoreMusic — 8-Channel Class AB Amplifier Test Protocol

**Zorunlu Bağlantılar:** [[8ch-integration]] · [[test-fixture]] · [[amplifier-classab-circuit]] · [[brain.md]]

---

## 1. Genel Bilgiler

| Parametre | Değer |
|-----------|-------|
| DUT | 8-Channel Class AB Amplifier (50W × 8) |
| Toplam Test Sayısı | 10 |
| Tahmini Test Süresi | 8-10 saat (tüm testler) |
| Geçme Kriteri | Tüm 10 test PAS olmalı |
| Tekrarlanabilirlik | Her test 3 kez tekrarlanmalı |
| Ortam Sıcaklığı | 25°C ± 2°C |
| Warm-up Süresi | 15 dakika (test öncesi) |

---

## 2. Test 1: DC Offset Ölçümü

### 2.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | < ±50mV DC @ 0V giriş, 8Ω yük |
| Ölçüm Aracı | 6.5-digit multimetre (Keysight 34465A) |
| Yük | 8Ω dummy load (her kanal) |
| Giriş | GND (0V, kısa devre) |

### 2.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | 6.5-digit multimetre | Hassas DC voltaj ölçümü |
| 2 | 8Ω/100W dummy load × 8 | Çıkış yükü |
| 3 | BNC short cable | Giriş GND'ye kısa devre |

### 2.3 Prosedür

```
ADIM 1: Hazırlık
├── DUT'ı test fixture'a bağla
├── Tüm dummy load'ları bağla (8Ω)
├── Girişlerini GND'ye kısa devre et (BNC short)
├── Multimetreyi DC 100mV aralığına ayarla
└── 15 dakika warm-up bekle

ADIM 2: Ölçüm (her kanal için)
├── Multimetre HI → DUT CH1 output (binding post)
├── Multimetre LO → DUT GND (chassis)
├── DC offset değerini oku
├── 10 saniye bekle, stabil okuma al
├── Değeri kaydet
└── Adımı CH2-CH8 için tekrarla

ADIM 3: Değerlendirme
├── Tüm kanallar < ±50mV ise PAS
├── Herhangi bir kanal ≥ ±50mV ise BAŞARISIZ
└── Başarısız kanalı tekrar test et (3 deneme)

ADIM 4: Kayıt
├── Sonuçları test raporuna yaz
├── Her kanal için ortalama ve maks değer
└── Sıcaklık notu (test sıcaklığı)
```

### 2.4 Geçme Kriterleri

| Kanal | Min | Max | Birim |
|-------|-----|-----|-------|
| CH1 | -50 | +50 | mV DC |
| CH2 | -50 | +50 | mV DC |
| CH3 | -50 | +50 | mV DC |
| CH4 | -50 | +50 | mV DC |
| CH5 | -50 | +50 | mV DC |
| CH6 | -50 | +50 | mV DC |
| CH7 | -50 | +50 | mV DC |
| CH8 | -50 | +50 | mV DC |

### 2.5 Hata Analizi

| Belirti | Muhtemel Neden | Çözüm |
|---------|----------------|-------|
| >100mV offset | Diff pair eşleşmemiş | BC546B'leri yeniden eşle |
| >50mV offset (tek kanal) | Vbe multiplier ayarsız | Trimpot ile ayarla |
| Tüm kanallarda yüksek | Besleme dengesiz | PSU ripple kontrol |
| Kararlı olmayan | Termal drift | Warm-up süresini uzat |

---

## 3. Test 2: THD+N Ölçümü

### 3.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | < 0.005% @ 1W/8Ω, 1kHz |
| Ölçüm Aracı | Audio analyzer (miniDSP UMIK-2 + REW) |
| Sinyal | 1kHz sine, 1W output (2.83Vrms @ 8Ω) |
| Yük | 8Ω dummy load |
| Bant genişliği | 20Hz – 20kHz (THD+N bandwidth) |

### 3.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | Audio analyzer (UMIK-2) | THD+N ölçümü |
| 2 | AD2 (sinyal üretici) | 1kHz sine girişi |
| 3 | 8Ω/100W dummy load | Çıkış yükü |
| 4 | Attenuatör (47:1) | Çıkış sinyalini küçültme |
| 5 | BNC kablolar | Bağlantı |

### 3.3 Prosedür

```
ADIM 1: Hazırlık
├── AD2'yi 1kHz sine, 0.35Vrms çıkışına ayarla
├── Attenuatör'ü bağla (47:1 → analyzer'a 0.6Vrms)
├── Audio analyzer'ı REW ile kalibre et
├── DUT'ı warm-up'a bırak (15dk)
└── Dummy load'ları bağla

ADIM 2: Ölçüm (her kanal için)
├── AD2 output → DUT CH1 input
├── DUT CH1 output → Dummy Load CH1
├── DUT CH1 output → Attenuatör → Audio Analyzer
├── REW'de "THD+N" testini başlat
├── Frekans aralığı: 20Hz – 20kHz
├── Ölçüm süresi: 30 saniye
├── THD+N değerini kaydet
└── Adımı CH2-CH8 için tekrarla

ADIM 3: Değerlendirme
├── Tüm kanallar < 0.005% ise PAS
├── Herhangi bir kanal ≥ 0.005% ise BAŞARISIZ
└── Başarısız kanalı 3 kez tekrar test et

ADIM 4: Kayıt
├── THD+N vs. frekans grafiğini kaydet
├── Her kanal için ortalama THD+N
└── Harmonik spektrum (2., 3., 5., 7. harmonik)
```

### 3.4 Geçme Kriterleri

| Kanal | Max THD+N | Frekans | Çıkış |
|-------|-----------|---------|-------|
| CH1-CH8 | < 0.005% | 1kHz | 1W (2.83Vrms) |

### 3.5 Hata Analizi

| Belirti | Muhtemel Neden | Çözüm |
|---------|----------------|-------|
| 3. harmonik baskın | VAS lineeritesi düşük | KSC3503 swap veya bias artır |
| 2. harmonik baskın | Push-pull dengesiz | MJL21194/93 eşleme kontrol |
| Tüm harmonikler yüksek | Global feedback yetersiz | Feedback ağı incele |
| THD 1W'de düşük, 50W'de yüksek | Clipping başlangıcı | PSU voltaj kontrol |

---

## 4. Test 3: SNR Ölçümü

### 4.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | > 100dB (A-weighted, 50W referans) |
| Ölçüm Aracı | Audio analyzer (UMIK-2 + REW) |
| Sinyal | 50W output (28.3Vrms @ 8Ω) → referans |
| Giriş | GND (0V, kısa devre) |
| Weighting | A-weighted |

### 4.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | Audio analyzer (UMIK-2) | SNR ölçümü |
| 2 | AD2 (sinyal üretici) | Giriş GND'ye kısa devre |
| 3 | 8Ω/100W dummy load | Çıkış yükü |
| 4 | Attenuatör (47:1) | Çıkış sinyalini küçültme |

### 4.3 Prosedür

```
ADIM 1: Hazırlık
├── AD2'yi 0V (GND) çıkışına ayarla
├── DUT girişini GND'ye kısa devre et
├── Audio analyzer'ı kalibre et
├── Warm-up: 15 dakika
└── Dummy load'ları bağla

ADIM 2: Ölçüm (her kanal için)
├── DUT CH1 input → GND
├── DUT CH1 output → Dummy Load CH1
├── DUT CH1 output → Attenuatör → Audio Analyzer
├── REW'de "SNR" testini başlat
├── A-weighted filtre uygula
├── SNR değerini kaydet (50W referans)
└── Adımı CH2-CH8 için tekrarla

ADIM 3: Değerlendirme
├── Tüm kanallar > 100dB ise PAS
├── Herhangi bir kanal ≤ 100dB ise BAŞARISIZ

ADIM 4: Kayıt
├── SNR value (dB, A-weighted)
├── Noise floor level (dBV)
└── Spektral gürültü profili
```

### 4.4 Hata Analizi

| Belirti | Muhtemel Neden | Çözüm |
|---------|----------------|-------|
| SNR < 90dB | PSU ripple yüksek | Çıkış filtre kapasitörlerini artır |
| SNR 90-100dB | Toprak döngüsü | Star ground kontrol |
|窄band noise | 50/60Hz hum | GND conexión noktası kontrol |
| High-frequency noise | EMI/RFI | Shielding ve bypass capacitor |

---

## 5. Test 4: Frekans Yanıtı (Bandwidth)

### 5.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | 10Hz – 150kHz (-3dB) |
| Ölçüm Aracı | AD2 (sweep) + Oscilloscope veya Audio Analyzer |
| Sinyal | Sine sweep, 10Hz – 200kHz, 1Vrms giriş |
| Çıkış | 8Ω dummy load |
| -3dB noktası | Alt: ≤10Hz, Üst: ≥150kHz |

### 5.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | AD2 (sinyal üretici) | Sweep sinyali |
| 2 | Osiloskop (100MHz) | Çıkış dalgaformu |
| 3 | Audio analyzer | Frekans yanıtı |
| 4 | 8Ω/100W dummy load | Çıkış yükü |

### 5.3 Prosedür

```
ADIM 1: Hazırlık
├── AD2'yi sine sweep moduna ayarla
├── Sweep aralığı: 10Hz – 200kHz
├── Sweep süresi: 10 saniye
├── Osiloskobu trigger moduna al
└── Warm-up: 15 dakika

ADIM 2: Ölçüm (her kanal için)
├── AD2 output → DUT CH1 input (1Vrms)
├── DUT CH1 output → Dummy Load CH1
├── DUT CH1 output → Osiloskop CH2
├── AD2 W1 (trigger) → Osiloskop EXT TRIG
├── Sweep'i başlat
├── Osiloskopta -3dB noktasını bul
│   ├── Alt kesim: Çıkış < 0.707 × referans
│   └── Üst kesim: Çıkış < 0.707 × referans
├── Alt ve üst -3dB frekanslarını kaydet
└── Adımı CH2-CH8 için tekrarla

ADIM 3: Değerlendirme
├── Alt -3dB ≤ 10Hz VE Üst -3dB ≥ 150kHz ise PAS
├── Aksi halde BAŞARISIZ

ADIM 4: Kayıt
├── Bode plot (gain vs. frequency)
├── -3dB alt frekans
├── -3dB üst frekans
└── Passband gain ripple (dB)
```

### 5.4 Hata Analizi

| Belirti | Muhtemel Neden | Çözüm |
|---------|----------------|-------|
| Alt -3dB > 50Hz | Giriş kapasitör küçük | C1 (100pF) artır veya coupling cap ekle |
| Üst -3dB < 100kHz | VAS bandwidth düşük | KSC3503 Cob kontrol |
| Passband ripple | Feedback ağı sorunlu | R3, R4, C2 incele |

---

## 6. Test 5: Slew Rate Ölçümü

### 6.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | > 40V/µs |
| Ölçüm Aracı | Osiloskop (100MHz, 1GS/s) |
| Sinyal | 100kHz square wave, 20Vpp |
| Çıkış | 8Ω dummy load |
| Ölçüm | Rise time (10%-90%) → SR = ΔV/Δt |

### 6.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | Osiloskop (100MHz) | Rise time ölçümü |
| 2 | AD2 (sinyal üretici) | 100kHz square wave |
| 3 | 8Ω/100W dummy load | Çıkış yükü |
| 4 | 10:1 pasif probe | Osiloskop probu |

### 6.3 Prosedür

```
ADIM 1: Hazırlık
├── AD2'yi 100kHz square wave, 20Vpp çıkışına ayarla
├── Osiloskobu 1µs/div timebase'e ayarla
├── Vertical: 5V/div
├── Trigger: CH1 (input square wave)
└── Warm-up: 15 dakika

ADIM 2: Ölçüm (her kanal için)
├── AD2 output → DUT CH1 input
├── DUT CH1 output → Dummy Load CH1
├── DUT CH1 output → Osiloskop CH2 (10:1 probe)
├── Osiloskopta rise time ölç
│   ├── 10% noktası: V1
│   ├── 90% noktası: V2
│   ├── ΔV = V2 - V1
│   └── Δt = rise time (10%-90%)
├── Slew Rate hesapla: SR = ΔV / Δt
├── Değeri kaydet (V/µs)
└── Adımı CH2-CH8 için tekrarla

ADIM 3: Değerlendirme
├── Tüm kanallar > 40V/µs ise PAS
├── Herhangi bir kanal ≤ 40V/µs ise BAŞARISIZ

ADIM 4: Kayıt
├── Rise time (ns)
├── Slew rate (V/µs) — pozitif ve negatif
├── Overshoot (%) ve Ringing
└── Dalgaformu screenshot
```

### 6.4 Hata Analizi

| Belirti | Muhtemel Neden | Çözüm |
|---------|----------------|-------|
| SR < 20V/µs | VAS current limit düşük | Bias akımı artır |
| SR asimetrik (poz./neg.) | Push-pull dengesiz | Driver transistör eşleme |
| Overshoot > 10% | Stability sorun | Compensation ağı incele |
| Ringing | Inductive load etkisi | Output L1 (10µH) değerini kontrol |

---

## 7. Test 6: Sürekli Güç Testi

### 7.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | 50W @ 8Ω, 30 dakika continuous |
| Yük | 8Ω dummy load (fan soğutmalı) |
| Çıkış | 28.3Vrms (50W) |
| Süre | 30 dakika kesintisiz |
| Monitör | Sıcaklık, THD, çıkış voltajı |

### 7.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | AD2 (sinyal üretici) | 1kHz sine, 1Vrms |
| 2 | 8Ω/100W dummy load × 8 | Çıkış yükü |
| 3 | FLIR termal kamera | Sıcaklık izleme |
| 4 | True RMS multimetre | Çıkış voltajı |
| 5 | Osiloskop | Dalgaformu monitör |

### 7.3 Prosedür

```
ADIM 1: Hazırlık
├── Tüm dummy load'ları bağla (8× 8Ω)
├── AD2'yi 1kHz sine, 1Vrms çıkışına ayarla
├── FLIR termal kamerayı tripod'a monte et
├── Multimetreyi True RMS AC moduna al
├── Osiloskobu CH2'ye bağla
└── Warm-up: 15 dakika

ADIM 2: Test Başlatma (tüm 8 kanal)
├── DUT güç ver
├── Tüm kanallar için 1kHz sine giriş uygula
├── Çıkış gücünü 50W'a ayarla (28.3Vrms)
├── FLIR ile ilk termal görüntüyü al (t=0)
├── Multimetreden çıkış voltajını kaydet
├── Osiloskopta dalgaformunu doğrula (clipping yok)
└── Timer başlat (30 dakika)

ADIM 3: İzleme (her 5 dakikada)
├── FLIR ile termal görüntü al
├── Multimetreden çıkış voltajını kaydet
├── Osiloskopta dalgaformunu kontrol
├── Herhangi bir anormallik varsa testi durdur
├── Kayıtları test raporuna yaz
└── 30 dakikaya kadar devam et

ADIM 4: Test Sonu
├── 30 dakika sonra sinyali kes
├── Gücü kapat
├── Son FLIR görüntüsünü al
├── Tüm verileri kaydet
└── Soğuma süresi: 30 dakika

ADIM 5: Değerlendirme
├── 30 dakika boyunca çıkış voltajı稳定 (±0.5V) ise PAS
├── Herhangi bir clipping, distortion veya fault ise BAŞARISIZ
├── Junction sıcaklığı 85°C'yi aşmamalı
└── Fan hızı otomatik artmalı (PID kontrol)
```

### 7.4 Geçme Kriterleri

| Parametre | Min | Max | Birim |
|-----------|-----|-----|-------|
| Çıkış voltajı | 27.8 | 28.8 | Vrms |
| THD+N | — | 0.01 | % |
| Junction sıcaklığı | — | 85 | °C |
| Heatsink sıcaklığı | — | 65 | °C |
| Fan hızı | 800 | 2200 | RPM |
| Test süresi | 30 | — | dakika |

---

## 8. Test 7: Termal Test (8 Saat)

### 8.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | 8 saat continuous, Junction < 85°C |
| Yük | 8Ω dummy load, 30W/ch (70% load) |
| Süre | 8 saat kesintisiz |
| Monitör | Junction sıcaklığı (termal kamera + KSD301) |
| Ortam Sıcaklığı | 25°C ± 2°C |

### 8.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | FLIR termal kamera | Junction sıcaklık ölçümü |
| 2 | AD2 (sinyal üretici) | 1kHz sine |
| 3 | 8Ω/100W dummy load × 8 | Çıkış yükü |
| 4 | Data logger (Laptop) | Sıcaklık verisi kaydı |
| 5 | Ambient termal sensör | Ortam sıcaklığı |

### 8.3 Prosedür

```
ADIM 1: Hazırlık
├── Test odasını 25°C'ye ayarla (klimalı oda)
├── FLIR'ı tripod'a monte et, üstten görünüm
├── Data logger'ı başlat (her 60sn kayıt)
├── DUT'ı warm-up'a bırak (15dk)
└── Ortam sıcaklığını kaydet

ADIM 2: Test Başlatma
├── DUT güç ver
├── 30W/ch çıkış ayarla (22.9Vrms @ 8Ω)
├── FLIR ile ilk termal görüntü (t=0)
├── Timer başlat (8 saat)
└── Fan PWM: otomatik mod

ADIM 3: İzleme (her saat)
├── FLIR ile termal görüntü al
├── Sıcaklık verilerini kaydet
├── Fan hızını kaydet
├── Herhangi bir anormallik varsa testi durdur
└── Klima durumunu kontrol

ADIM 4: Test Sonu
├── 8 saat sonra sinyali kes
├── Gücü kapat
├── Son FLIR görüntüsünü al
├── Tüm verileri kaydet
└── Soğuma süresi: 1 saat

ADIM 5: Değerlendirme
├── Junction sıcaklığı 85°C'yi Hiç aşmamışsa PAS
├── Herhangi bir anda >85°C ise BAŞARISIZ
├── Fan hızı stabil kalmışsa PAS
└── Fan arıza sinyali almamışsa PAS
```

### 8.4 Sıcaklık Kayıt Tablosu

| Saat | Junction CH1 | Junction CH2 | ... | Junction CH8 | Heatsink Ort | Fan RPM | Ortam |
|------|-------------|-------------|-----|-------------|-------------|---------|-------|
| 0 | | | | | | | 25°C |
| 1 | | | | | | | |
| 2 | | | | | | | |
| ... | | | | | | | |
| 8 | | | | | | | |

---

## 9. Test 8: Koruma Sistemi Testi

### 9.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Overcurrent | Trip @ >3A output (0.47Ω sense) |
| Thermal | Trip @ 85°C (KSD301) |
| DC Offset | Trip @ >50mV DC output |
| Speaker Relay | Açma süresi < 100ms |

### 9.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | AD2 (sinyal üretici) | Overcurrent testi için |
| 2 | Osiloskop | Trip süresi ölçümü |
| 3 | FLIR termal kamera | Thermal trip testi |
| 4 | Variable load (0-5Ω) | Overcurrent tetikleme |
| 5 | DC offset source | DC offset tetikleme |

### 9.3 Overcurrent Testi

```
ADIM 1: Overcurrent Tetikleme
├── DUT'ı çalıştır (1kHz sine, 1Vrms)
├── Variable load'u 8Ω'a ayarla
├── Çıkış gücünü 50W'a ayarla
├── Load'u kademeli olarak azalt (8Ω → 4Ω → 2Ω)
├── Koruma tetiklendiğinde osiloskop'ta izle
├── Trip süresini ölç (< 100ms hedef)
└── Trip条件: 0.47Ω sense direnç üzerinde >3A

ADIM 2: Doğrulama
├── Koruma tetiklendikten sonra çıkış 0V olmalı
├── LED kırmızı yanmalı
├── UART fault kodu gönderilmeli
├── Reset butonuna bas → Normal moda dön
└── Tekrar test et (3 deneme)
```

### 9.4 Thermal Testi

```
ADIM 1: Thermal Tetikleme
├── DUT'ı çalıştır (30W/ch, 8Ω)
├── Fan hortumunu tıkayarak sıcaklığı artır
├── FLIR ile Junction sıcaklığını izle
├── 85°C'ye ulaştığında koruma tetiklenmeli
├── Trip条件: KSD301 85°C snap-action

ADIM 2: Doğrulama
├── Koruma tetiklendikten sonra çıkış 0V olmalı
├── LED kırmızı yanmalı
├── Fan maksimum hızda olmalı
├── Soğuduktan sonra (75°C altı) reset
└── Tekrar test et (3 deneme)
```

### 9.5 DC Offset Testi

```
ADIM 1: DC Offset Tetikleme
├── DUT girişini GND'ye kısa devre et
├── Çıkış DC offset'ini osiloskop'ta izle
├── DC offset source ile +60mV DC uygula
├── Koruma tetiklendiğinde izle
├── Trip条件: ±50mV DC çıkış

ADIM 2: Doğrulama
├── Koruma tetiklendikten sonra çıkış 0V olmalı
├── LED kırmızı yanmalı
├── Speaker relay açılmalı
├── Reset → Normal moda dön
└── Tekrar test et (3 deneme)
```

---

## 10. Test 9: Kanal Ayrımı (Channel Separation)

### 10.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | > 80dB |
| Sinyal | 1kHz sine, 50W (28.3Vrms) @ drives kanal |
| Ölçüm | Komşu kanaldaki sinyal seviyesi |
| Kombinasyon | Her kanal çifti (1-2, 2-3, ..., 7-8) |

### 10.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | AD2 (sinyal üretici) | 1kHz sine |
| 2 | Audio analyzer (UMIK-2) | Kannal ölçümü |
| 3 | 8Ω/100W dummy load × 8 | Çıkış yükü |
| 4 | Attenuatör (47:1) | Çıkış sinyalini küçültme |

### 10.3 Prosedür

```
ADIM 1: Hazırlık
├── AD2'yi 1kHz sine, 1Vrms çıkışına ayarla
├── Tüm dummy load'ları bağla
├── Audio analyzer'ı kalibre et
└── Warm-up: 15 dakika

ADIM 2: Ölçüm (her komşu kanal çifti için)
├── AD2 output → DUT CH1 input (drives kanal)
├── DUT CH1 output → Dummy Load CH1 (50W)
├── DUT CH2 output → Dummy Load CH2 (boşta)
├── DUT CH2 output → Attenuatör → Audio Analyzer
├── Audio analyzer'da 1kHz'deki seviyeyi ölç
├── Level dBV cinsinden kaydet
├── Kanal ayrımı = Drive level - Crosstalk level
└── Adımı CH2-CH3, CH3-CH4, ... CH7-CH8 için tekrarla

ADIM 3: Değerlendirme
├── Tüm çiftler > 80dB ise PAS
├── Herhangi bir çift ≤ 80dB ise BAŞARISIZ

ADIM 4: Kayıt
├── Her çift için crosstalk level (dBV)
├── Kanal ayrımı (dB)
└── Crosstalk spektrumu (20Hz-20kHz)
```

### 10.4 Geçme Kriterleri

| Kanal Çifti | Min Ayrım | Birim |
|-------------|-----------|-------|
| CH1-CH2 | > 80 | dB |
| CH2-CH3 | > 80 | dB |
| CH3-CH4 | > 80 | dB |
| CH4-CH5 | > 80 | dB |
| CH5-CH6 | > 80 | dB |
| CH6-CH7 | > 80 | dB |
| CH7-CH8 | > 80 | dB |
| CH1-CH8 | > 80 | dB (uzun mesafe) |

---

## 11. Test 10: Verimlilik Ölçümü

### 11.1 Spesifikasyon

| Parametre | Değer |
|-----------|-------|
| Geçme Kriteri | > 55% @ full power (50W × 8 = 400W) |
| Giriş Gücü | ±35V DC, toplam akım ölçümü |
| Çıkış Gücü | 400W (8 × 50W @ 8Ω) |
| Verimlilik | η = Pout / Pin × 100% |

### 11.2 Gerekli Ekipman

| # | Ekipman | Kullanım |
|---|---------|----------|
| 1 | AD2 (sinyal üretici) | 1kHz sine, 8 kanal |
| 2 | 8Ω/100W dummy load × 8 | Çıkış yükü |
| 3 | True RMS multimetre × 2 | +35V ve -35V akım ölçümü |
| 4 | True RMS multimetre × 2 | +35V ve -35V voltaj ölçümü |
| 5 | Current clamp | Alternatif akım ölçümü |

### 11.3 Prosedür

```
ADIM 1: Hazırlık
├── DUT güç besleme hatlarına current clamp tak
├── Multimetreyi DC voltage moduna al (2 adet)
├── Multimetreyi DC current moduna al (2 adet)
├── AD2'yi 1kHz sine, 1Vrms çıkışına ayarla (8 kanal)
├── Tüm dummy load'ları bağla
└── Warm-up: 15 dakika

ADIM 2: Ölçüm
├── DUT güç ver
├── 8 kanal için 50W çıkış ayarla
├── +35V rail: Voltaj ve akım ölç
│   ├── V_pos = Multimetre 1 (DC V)
│   └── I_pos = Multimetre 2 (DC A)
├── -35V rail: Voltaj ve akım ölç
│   ├── V_neg = Multimetre 3 (DC V)
│   └── I_neg = Multimetre 4 (DC A)
├── Giriş gücünü hesapla:
│   ├── P_in = (V_pos × I_pos) + (|V_neg| × I_neg)
│   └── P_in = (35 × I_pos) + (35 × I_neg)
├── Çıkış gücünü hesapla:
│   └── P_out = 400W (8 × 50W)
├── Verimlilik hesapla:
│   └── η = (P_out / P_in) × 100%
└── Değeri kaydet

ADIM 3: Değerlendirme
├── η > 55% ise PAS
├── η ≤ 55% ise BAŞARISIZ

ADIM 4: Kayıt
├── V_pos, I_pos, V_neg, I_neg
├── P_in (watt)
├── P_out (watt)
├── η (yüzde)
└── ISIP kaybı (P_in - P_out)
```

### 11.4 Hata Analizi

| Belirti | Muhtemel Neden | Çözüm |
|---------|----------------|-------|
| η < 50% | Dropout voltajı yüksek | MJL21194/93 Vce(sat) kontrol |
| η < 55% | Quiescent akım yüksek | Vbe multiplier ayarla |
| η çok yüksek (>70%) | Ölçüm hatası | Multimetre kalibrasyonu |

---

## 12. Test Raporu Şablonu

### 12.1 Genel Bilgiler

```
╔══════════════════════════════════════════════════════════════╗
║  COREMUSIC 8-KANAL CLASS AB AMPLİFİKATÖR TEST RAPORU       ║
╠══════════════════════════════════════════════════════════════╣
║  DUT S/N: ________________                                   ║
║  Test Tarihi: ________________                               ║
║  Test Operator: ________________                             ║
║  Ortam Sıcaklığı: ___________°C                             ║
║  Nem: ___________%                                          ║
║  DUT Warm-up: 15 dakika                                     ║
╚══════════════════════════════════════════════════════════════╝
```

### 12.2 Sonuç Tablosu

| Test # | Test Adı | Kriter | Sonuç | Durum |
|--------|----------|--------|-------|-------|
| 1 | DC Offset | < ±50mV | | |
| 2 | THD+N | < 0.005% | | |
| 3 | SNR | > 100dB | | |
| 4 | Bandwidth | 10Hz-150kHz | | |
| 5 | Slew Rate | > 40V/µs | | |
| 6 | Continuous Power | 30dk @ 50W | | |
| 7 | Thermal | 8 saat, Tj<85°C | | |
| 8 | Protection | OCP, OTP, DC | | |
| 9 | Channel Sep. | > 80dB | | |
| 10 | Efficiency | > 55% | | |

### 12.3 Nihai Karar

```
╔══════════════════════════════════════════════════════╗
║  NİHAİ KARAR:  □ PAS    □ BAŞARISIZ                  ║
╠══════════════════════════════════════════════════════╣
║  Toplam Test: 10                                     ║
║  Geçilen: _______ / 10                              ║
║  Başarısız: _______ / 10                            ║
║                                                      ║
║  Notlar:                                             ║
║  ________________________________________________    ║
║  ________________________________________________    ║
║                                                      ║
║  İmza: ________________  Tarih: ________________    ║
╚══════════════════════════════════════════════════════╝
```

---

## 13. Çapraz Referanslar

| Test | İlgili ADR | İlgili Spesifikasyon |
|------|-----------|---------------------|
| Test 1 (DC Offset) | ADR-061 | amplifier-classab-circuit §1 |
| Test 2 (THD+N) | ADR-061 | amplifier-classab-circuit §1 |
| Test 3 (SNR) | ADR-061 | amplifier-classab-circuit §1 |
| Test 4 (Bandwidth) | ADR-061 | amplifier-classab-circuit §1 |
| Test 5 (Slew Rate) | ADR-061 | amplifier-classab-circuit §1 |
| Test 6 (Power) | ADR-061 | 8ch-integration §1 |
| Test 7 (Thermal) | ADR-061 | thermal-design-classab §1 |
| Test 8 (Protection) | ADR-061 | 8ch-integration §2 |
| Test 9 (Channel Sep.) | ADR-061 | amplifier-classab-circuit §1 |
| Test 10 (Efficiency) | ADR-061 | power-supply-classab §1 |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-09-18  
**Version:** 1.0.0  
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
