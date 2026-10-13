---
type: electronic
category: power-supply
title: "CoreMusic — Amplifier Power Supply Design"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Power Supply Design

**See also:** [[electronic/amplifier/index]] · [[electronic/hardware/index]]

**Zorunlu Bağlantılar:** [[electronic/amplifier/class-ab]] · [[electronic/amplifier/class-d]] · [[electronic/amplifier/protection]] · [[electronic/hardware/pcb-design]]

---

## 1. Amaç

Power Supply Design, CoreMusic amfi güç besleme tasarımını tanımlar: AC→DC dönüşümü (§2), filtre/kondansatör hesabı (§3), regülatörler (§4), ripple & dinamik (§5), koruma (§6), termal (§7), ölçüm (§8), boost sorunları (§9), boost tasarım hesapları (§14), web kaynakları (§15) ve açık konular (§16).

Bu dosya şu soruları yanıtlar:

- 12-24V DC girişi ±42V'a (Class AB) veya 51V'a (Class D) nasıl yükseltirim?
- Filtre kondansatörü boyutu nasıl hesaplanır?
- Ripple ve dinamik yük davranışını nasıl ölçerim?
- Koruma devreleri (over-voltage, under-voltage, over-current) nasıl kurulur?

İlgili dosyalar: [[class-ab]] (±42V rail), [[class-d]] (PVDD boost), [[protection]] (koruma eşikleri), [[thermal]] (soğutma).

---

## 2. AC→DC Dönüşümü

### 2.1 Topoloji Seçimi

| Girdi | Çıktı | Topoloji | Kullanım |
|-------|-------|----------|----------|
| 12-24V DC adaptör | PVDD 51V (Class D) | Boost (step-up) | Ev cihazı, taşınabilir |
| 12-24V DC adaptör | ±42V (Class AB) | Dual boost veya inverting | Ana amfi |
| 115/230V AC mains | ±42V (Class AB) | Toroidal + bridge rectifier | Stüdyo, sabit kurulum |
| USB-C PD 20V | PVDD 51V | Boost | Taşınabilir |
| 12V batarya (3S LiPo) | PVDD 36V | Boost | Kablosuz |

### 2.2 AC→DC Zinciri (Toroidal — AC Giriş)

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  AC MAINS    │    │  TOROIDAL    │    │  BRIDGE      │    │  SMOOTHING   │
│  115V/230V   │───▶│  TRANSFORMER │───▶│  RECTIFIER   │───▶│  CAPACITOR   │
│              │    │  500VA       │    │  KBPC5010    │    │  10,000µF    │
└──────────────┘    └──────────────┘    └──────────────┘    └──────┬───────┘
                                                                   │
                                                                   ▼
                                                            ┌──────────────┐
                                                            │  ±42V DC     │
                                                            │  (Class AB)  │
                                                            └──────────────┘
```

| Aşama | Bileşen | Parametre | Gerekçe |
|-------|---------|-----------|---------|
| 1. Transformatör | Toroidal 500VA | ±42V AC çıkış, %95 verim | Düşük manyetik yayılım |
| 2. Rectifier | KBPC5010 bridge | 50A, 100V | Yeterli akım kapasitesi |
| 3. Smoothing | 2 × 10,000µF elektrolitik | Ripple azaltma | §2 hesap |
| 4. Post-filter | LC + 100nF ceramic | High-frequency temizlik | §4 |

### 2.3 AC→DC Zinciri (Boost — DC Giriş, Class D)

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  DC GİRİŞ    │    │  BOOST       │    │  PVDD ÇIKIŞ  │    │  LC FİLTRE   │
│  12V–24V DC  │───▶│  CONVERTER   │───▶│  18-53.5V DC │───▶│  50kHz       │
│  (Adaptör/   │    │  Step-Up     │    │  (Tek Rail)  │    │  low-pass    │
│   Batarya)   │    │  12V→51V     │    │              │    │              │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
```

**Neden boost?** DC giriş (12-24V) Class D'nin PVDD ihtiyacını (18-53.5V) karşılamaz; boost converter gerilimi yükseltir. Class AB için çift rail (±42V) gerekir → dual boost veya inverting topoloji.

---

## 3. Filtre / Kondansatör Hesabı

### 3.1 Ripple Hesabı (Smoothing Kondansatörü)

Full-wave rectifier sonrası ripple voltajı:

| Formül | Değer | Açıklama |
|--------|-------|----------|
| Vripple(pp) = Iload / (2 × f × C) | 3.54A / (2 × 100Hz × 10,000µF) = 1.77Vpp | 50/100Hz ripple |
| C = Iload / (2 × f × Vripple) | 3.54 / (2 × 100 × 0.5) = 35,400µF | 0.5Vpp hedef için |
| Vripple(rms) | ≈ Vripple(pp) / (2√3) = 0.51Vrms | RMS karşılık |

**Seçim tablosu:**

| Hedef Ripple | Gerekli C (3.54A yük) | Adet × Değer | Maliyet |
|--------------|----------------------|--------------|---------|
| 1.0Vpp | ~17,700µF | 2 × 10,000µF | Düşük |
| **0.5Vpp** | **~35,400µF** | **4 × 10,000µF** | **Orta (CoreMusic)** |
| 0.1Vpp | ~177,000µF | 18 × 10,000µF | Yüksek |

### 3.2 Kondansatör Hesap Kuralları

| Kural | Değer | Gerekçe |
|-------|-------|---------|
| Minimum per rail | 10,000µF | Ripple + transient |
| Voltaj rating | ≥1.5 × Vrail (±42V → 63V) | Güvenlik marjı |
| ESR | Low-ESR elektrolitik | Ripple akımı ısıtma |
| Ömür (ripple akımı) | 20,000 saat @ 105°C | Ripple current rating |
| Ceramic bypass | 100nF per IC | High-frequency |
| Bulk + ceramic | 10µF + 100nF kombinasyonu | Geniş bant empedans |

### 3.3 Boost Converter Çıkış Kondansatörü

| Çip | Çıkış C | ESR | Ripple | Not |
|-----|---------|-----|--------|-----|
| LTC3862 | 2 × 100µF ceramic + 1 × 470µF bulk | <10mΩ | <50mV | Multi-phase |
| LM5122 | 100µF ceramic + 220µF bulk | <10mΩ | <50mV | Single-phase |

### 3.4 Kondansatör Ömürü ve Ripple Current

Elektrolitik kondansatör ömrü büyük ölçüde sıcaklık ve iç dirence (ESR) bağlıdır; ömür hesabı olmadan 10,000µF bankası sessizce erir.

| Parametre | Formül / Kural | Örnek Hesap |
|-----------|----------------|-------------|
| Arpman (Arrhenius) kuralı | Ömür her 10°C düşüşte 2× artar | 105°C'de 20,000 saat → 85°C'de 40,000 saat → 65°C'de 80,000 saat |
| Ripple ısıtması | P = I²ripple × ESR | 1.2A × 25mΩ → 36mW/kondansatör (ihmal edilebilir) |
| Toplam ripple current | Iripple(total) = Iload / (2 × f × C) | 3.54A / (2 × 100Hz × 40,000µF) = 0.44App |
| Rating karşılaştırması | İbrelenen ripple ≥ hesaplanan ripple | 10,000µF/63V tipik rating 1.8A @ 105°C ≥ 0.44A ✅ |
| Nem kaybı (dry-out) | Kapalı devre, sıcak nokta | 65°C üstü kabuk sıcaklığında ömür hızla düşer |

**Yaşam ömrü kuralları:**

| # | Kural | Kriter |
|---|-------|--------|
| 1 | Kabuk sıcaklığını ölç | Termokupl ile 1 saat tam yük |
| 2 | Ömür bütçesi ≥ 20,000 saat | Tasarım sıcaklığında hesapla |
| 3 | 105°C rating zorunlu | 85°C rating kabul edilmez |
| 4 | Yılda 1 gözden geçirme | ESR ölçümü (LCR meter), ±%20 sapmada değiştir |
| 5 | Vibration (taşınabilir) | Polimer/kapak menteşeli tip tercih et |

**Ölçüm notu:** ESR ölçümü DC kaynağı kesilmiş, boşaltılmış kondansatörde yapılır; şarjlı kondansatöre LCR probe bağlamak ölçüm cihazını bozar (güvenlik: §6).

---

## 4. Regülatörler

### 4.1 Rail Gereksinimleri

| Rail | Gerilim | Kullanım | Gürültü Hassasiyeti |
|------|---------|----------|---------------------|
| PVDD (Class D) | 18-53.5V (tipik 51V) | Power stage | Orta |
| ±VCC (Class AB) | ±42V | Output transistör | Düşük (SNR) |
| GVDD | 12V | Gate drive | Orta |
| VDD (analog) | 12V low-noise | Input/VAS | **Yüksek** |
| 3.3V | 3.3V | Dijital lojik | Orta |
| 5V | 5V | USB, MCU | Düşük |

### 4.2 Regülatör Seçimi

| Rail | Regülatör | Tip | Gürültü | Verim | Not |
|------|-----------|-----|---------|-------|-----|
| VDD (analog) | LT3045 (ADI) | LDO | 0.8µVrms | Düşük | Ultra-low-noise |
| GVDD | LM7812 veya LDO | LDO | Orta | Orta | Gate drive yeterli |
| 3.3V | AMS1117-3.3 | LDO | Orta | Orta | Dijital lojik |
| 5V | LM2596 | Buck | Yüksek | %90 | USB/MCU |

**Kural:** Analog VDD için her zaman low-noise LDO kullan; switching regülatör (buck/boost) doğrudan analog rale verilmez.

### 4.3 Boost Converter Çip Seçenekleri

| # | Çip | Üretici | Giriş | Çıkış | Güç | Verimlilik | Fiyat | Öneri |
|---|-----|---------|-------|-------|-----|-----------|-------|-------|
| 1 | **LTC3862** | ADI | 12-24V | ±42V | 500W | 95% | ~$5 | ✅ **En iyi** |
| 2 | **LM5122** | TI | 12-24V | 100V | 500W | 94% | ~$3 | ✅ **En ucuz** |
| 3 | **Custom** | — | 12-24V | ±42V | Özelleştirilmiş | 90%+ | Değişken | ⚠️ Tasarım gerektirir |

**Seçim Kılavuzu:**
- Yüksek verimlilik → **LTC3862** (%95)
- Düşük maliyet → **LM5122** (~$3)
- Tam kontrol → **Custom** (MOSFET + Indüktör + PWM controller)

### 4.4 LTC3862 / LM5122 Kuralları

| Çip | Kritik Kurallar |
|-----|-----------------|
| LTC3862 | INTVCC pinlerini multi-phase'de BİRLEŞTİRME; 3V8'ye 1nF ceramic; SLOPE pin: float=1.0, GND=0.625, 3V8=1.66 |
| LM5122 | SW routing kısa; HO/HS izlerini yakın tut; CSP/CSN'e 100Ω+100pF filtre; indüktör altına solid GND plane |

Detaylı sorun-çözüm tabloları: [[class-d]] §7 (boost converter sorunları).

---

## 5. Ripple & Dinamik

### 5.1 Ripple Ölçümü

| Parametre | Hedef | Yöntem |
|-----------|-------|--------|
| Ripple (rail DC) | <50mVpp | Oscilloscope, AC coupling |
| Ripple (analog VDD) | <1mVrms | Ripple meter, A-weighted |
| Switching frekansı | 200-500kHz | FFT, LC filtre sonrası |
| High-frequency noise | <10mVpp | 20MHz bandwidth limit |

### 5.2 Dinamik Yük Davranışı

| Test | Yük Değişimi | Beklenen | Kabul |
|------|-------------|----------|-------|
| Step load (10%-90%) | 0.35A → 3.2A | Vdrop < 1V, recovery < 1ms | ✅ |
| Transient (100W pulse) | Sessizlik → 100W | PVDD dip < 2V | ✅ |
| Music crest factor | 4× RMS | Rail stabil | ✅ |

### 5.3 PSRR (Power Supply Rejection Ratio)

| Rail | PSRR @ 100Hz | PSRR @ 1kHz | Gereksinim |
|------|-------------|-------------|-----------|
| Class AB ±VCC | >60dB | >40dB | SNR >100dB |
| Class D PVDD | >40dB | >20dB | THD <0.01% |
| Analog VDD | >80dB | >60dB | Input stage |

**Not:** PSRR, amplifikatörün power supply ripple'ını ne kadar reddettiğidir; yüksek PSRR → düşük SNR etkisi.

---

## 6. Koruma

### 6.1 Koruma Devreleri

| Koruma | Tetikleme | Tepki | Süre |
|--------|-----------|-------|------|
| Over-Voltage | >±48V DC | Boost kapat | Anında |
| Under-Voltage | <10V DC | Amplifier durdur | Anında |
| Over-Current | >max akım | Boost sınırla | <100µs |
| Thermal | >80°C | Fan hızı ↑, >100°C kapat | Sürekli |
| Short Circuit | 0Ω çıkış | Anında kapanma | <1µs |

### 6.2 Eşik Hesapları

| Koruma | Eşik | Hesap | Aksiyon |
|--------|------|-------|---------|
| Over-voltage | 48V | PVDD max (53.5V) + marj | Boost disable |
| Under-voltage | 10V | Boost min giriş | Amplifier mute |
| Over-current | 5A | Ipeak (100W@8Ω) + %40 marj | Current limit |
| Thermal | 80°C/100°C | Tj limit - 50°C/0°C | Fan/kapanma |

Detay: [[protection]] §7-§8 (over-current, over-temperature).

### 6.3 Soft-Start

| Parametre | Değer | Gerekçe |
|-----------|-------|---------|
| Gecikme | 2 saniye | Rush current sınırlama |
| Rush current | <2× Ipeak | Kondansatör şarj akımı |
| Kondansatör şarj | Yavaş (soft-start RC) | Inrush önleme |
| Röle kapanma | Gecikmeli | Hoparlör koruması |

---

## 7. Termal

### 7.1 Isı Kaynakları

| Bileşen | Isı | Kaynak | Soğutma |
|---------|-----|--------|---------|
| Boost converter | ~25W (500W @ %95) | MOSFET + indüktör | heatsink |
| Bridge rectifier | ~10W (3.54A × 2V drop) | KBPC5010 | heatsink |
| Kondansatör ESR | ~5W (ripple akımı) | 10,000µF ESR | Doğal konveksiyon |
| LDO (analog VDD) | ~1W | Dropout × I | Doğal konveksiyon |

### 7.2 Termal Hesap

| Parametre | Değer |
|-----------|-------|
| Toplam ısı | ~41W (boost + rectifier + cap) |
| Heatsink | 200cm², <2°C/W |
| Tj (MOSFET) | Ta + Pdiss × RθJC = 25 + 15 × 1.5 = 47.5°C ✅ |
| Kondansatör ömür | 105°C rating, 20,000 saat |

Detay: [[thermal]] §2 (ısıtıcı hesabı).

---

## 8. Ölçüm

### 8.1 Ölçüm Setupı

```
AC/DC Kaynak → [Power Supply] → ±42V / PVDD 51V → Load (Amplifier simulator)
                    │
              Oscilloscope (ripple)
                    │
              DMM (DC voltaj)
                    │
              Load tester (dinamik)
```

### 8.2 Ölçüm Parametreleri

| Ölçüm | Hedef | Yöntem | Sıklık |
|-------|-------|--------|--------|
| DC voltaj (rail) | ±42V / 51V ±0.5V | DMM | Her açılış |
| Ripple | <50mVpp | Oscilloscope, AC coupling | Tasarım doğrulama |
| Under/over-voltage | Eşik ±1V | Function generator tetikleme | Üretim testi |
| Over-current | 5A limit | Electronic load | Üretim testi |
| Verimlilik | >90% (boost) | Pin/Pout ölçümü | Tasarım doğrulama |
| Dinamik | Vdrop <1V | Step load | Tasarım doğrulama |
| Termal | Tj <120°C | Termokupl | 1 saat süreli |

### 8.3 Ölçüm Adımları

| Adım | Aksiyon | Beklenen |
|------|---------|----------|
| 1 | Boşta DC voltaj ölç | ±42V / 51V ±0.5V |
| 2 | Ripple ölç (AC coupling) | <50mVpp |
| 3 | Step load testi (10%-90%) | Vdrop <1V, recovery <1ms |
| 4 | Over-voltage tetikle | Boost kapat @ 48V |
| 5 | Over-current tetikle | Limit @ 5A |
| 6 | Verimlilik: Pin/Pout | >90% |
| 7 | Termal: 1 saat tam yük | Tj stabil <120°C |

---

## 9. Boost Converter Sorunları ve Çözümleri

### 9.1 LTC3862 Sorunları

| # | Sorun | Belirti | Çözüm |
|---|-------|---------|-------|
| 1 | Yüksek giriş voltajında termal | Yüksek VIN'de iç LDO aşırı ısınır | Harici 5V/12V bias supply kullan |
| 2 | Multi-phase INTVCC | INTVCC pinleri birleştirilirse termal yük | Her IC için ayrı bypass capacitor |
| 3 | Güç açma sırası | VIN INTVCC'den önce gelirse latchup | Güç sıralaması kontrol et |
| 4 | Yük altında çıkış düşüşü | Sense resistor yanlış veya layout kötü | Sense resistor doğrula, layout iyileştir |
| 5 | Duty cycle >%92 | Yüksek VIN/VOUT oranında kararsızlık | Slope compensation ayarla |

### 9.2 LM5122 Sorunları

| # | Sorun | Belirti | Çözüm |
|---|-------|---------|-------|
| 1 | Yüksek çıkış gürültüsü | SW node uzun, anten gibi davranır | SW routing'i kısalt, snubber ekle |
| 2 | Output kapasitör yerleşimi | Ripple yüksek | Ceramik + bulk kombinasyonu |
| 3 | Gate driver gürültüsü | HO ve HS izleri ayrı | HO ve HS izlerini yakın tut |
| 4 | Current sense gürültüsü | Yanlış akım ölçümü | CSP/CSN'e RC filtre ekle |
| 5 | Input kapasitör yetersiz | Yük altında voltaj düşüşü | Yeterli input capacitance ekle |

### 9.3 Genel Boost Converter Sorunları

| # | Sorun | Belirti | Çözüm |
|---|-------|---------|-------|
| 1 | PCB Layout | Gürültü, verimlilik düşüklüğü | TI layout guidelines takip et |
| 2 | Output ripple | Yüksek voltaj dalgalanması | Ceramik + bulk kapasitör kombinasyonu |
| 3 | Inductor doygunluğu | Aşırı akım, kararsızlık | Isat > ILIM + %20 margin |
| 4 | Toprak döngüsü | Gürültü, kararsızlık | Solid ground plane, star ground |
| 5 | EMI | Elektromanyetik girişim | Shield, ferrite bead, EMI filtre |

---

## 10. Repo / Kaynak Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda güç kaynağı devresine karşılık gelen C/C++/PCB kodu bulunmaz; tasarım yalnızca bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon katmanının placeholder ölçüm alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` — cihaz analizi | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder (gerçek ölçüm YOK) | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** `analyzeHardware()` snr/thd değerlerini her zaman `0.0` döndürür. Güç kaynağı ölçüm zinciri vault tarafında tanımlıdır (§8); gerçek cihaz entegrasyonu PLANNED.

---

## 11. Risk Matrisi

| # | Risk | Olasılık | Etki | Önlem | İlgili |
|---|------|----------|------|-------|--------|
| 1 | **Over-voltage (boost kaçak)** | Düşük | CRITICAL | 48V eşiğinde boost kapat | §6 |
| 2 | **Under-voltage (düşük giriş)** | Orta | HIGH | 10V eşiğinde amplifier mute | §6 |
| 3 | **Over-current (kısa devre)** | Düşük | CRITICAL | 5A current limit, <100µs | §6 |
| 4 | **Ripple yüksek** | Orta | MEDIUM (SNR) | Low-ESR, yeterli C | §3 |
| 5 | **Termal (boost MOSFET)** | Orta | HIGH | heatsink, Tj <120°C | §7 |
| 6 | **Kondansatör ömrü** | Orta | HIGH | 105°C rating, ripple current | §3 |
| 7 | **EMI (switching)** | Orta | HIGH | LC filtre, shielding, ferrite | §6 |
| 8 | **Ground loop** | Orta | MEDIUM (hum) | Star ground, tek nokta | §4 |
| 9 | **Soft-start yetersiz** | Düşük | MEDIUM (rush) | 2s gecikme, inrush limit | §6.3 |
| 10 | **PSRR düşük** | Düşük | MEDIUM (SNR) | Low-noise LDO | §5.3 |

---

## 12. Terimler (Glossary)

| Terim | Tanım |
|-------|-------|
| Ripple | DC rail üzerindeki AC dalgalanma (Vpp) |
| ESR | Equivalent Series Resistance — kondansatör iç direnci |
| PSRR | Power Supply Rejection Ratio (dB) |
| Boost converter | Step-up DC-DC (giriş < çıkış) |
| Soft-start | Açılışta rush current sınırlama |
| Over-voltage | Rail'in güvenli sınırı aşması |
| Under-voltage | Rail'in minimum sınırın altına düşmes |
| Toroidal | Halka çekirdekli transformatör (düşük yayılım) |

---

## 13. ADR ve Çapraz Referanslar

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

| Hedef | İlişki |
|-------|--------|
| [[electronic/amplifier/class-ab]] | ±42V rail tüketimi |
| [[electronic/amplifier/class-d]] | PVDD boost tüketimi |
| [[electronic/amplifier/protection]] | Koruma eşikleri bütünlüğü |
| [[electronic/amplifier/thermal]] | Boost termal bütünlüğü |
| [[electronic/hardware/pcb-design]] | Toprak dözelzi, SW routing, EMI yerleşimi |

---

## 14. Boost Converter Tasarım Hesapları (Adım Adım)

§4.3'te seçilen boost converter için indüktör ve duty cycle hesabı; tasarımcının chip seçimi öncesi yaptığı ön hesap akışıdır.

### 14.1 Duty Cycle Hesabı

| Parametre | Formül | Örnek (24V → 51V, Class D) |
|-----------|--------|---------------------------|
| Duty cycle | D = 1 − (Vin × η) / Vout | 1 − (24 × 0.94) / 51 = 0.556 → %55.6 |
| Min duty | Dmin (Vin=28V max adaptör) | 1 − (28 × 0.94) / 51 = 0.488 |
| Max duty | Dmax (Vin=12V min) | 1 − (12 × 0.94) / 51 = 0.777 — LTC3862 %92 sınırının altında ✅ |

### 14.2 Indüktör Seçimi

| Parametre | Formül | Değer (100W çıkış) |
|-----------|--------|--------------------|
| Çıkış akımı | Iout = Pout / Vout | 100 / 51 = 1.96A |
| Indüktör ripple | ΔIL = Vin × D / (fsw × L) | 24 × 0.556 / (400kHz × 10µH) = 0.33A |
| Peak akım | Ipeak = Iout + ΔIL / 2 | 1.96 + 0.17 = 2.13A |
| Isat kuralı | Isat ≥ Ipeak × 1.2 | ≥ 2.56A → 3A indüktör seç |
| Çekirdek kaybı | Datasheet loss curve @ 400kHz | Düşük-tozlu ferrite (sendust tercih) |

**Kural:** İndüktör alternatif direnci (DCR) düşük seçilmeli — DCR × I²ripple ısı olarak kaybolur; 20mΩ DCR ile 13mW, bu ölçekte ihmal edilebilir.

### 14.3 Giriş / Çıkış Kondansatör Seçimi

| Node | Kural | Değer |
|------|-------|-------|
| Giriş (24V) | Cin ≥ Iout × Dmax / (ΔVin × fsw) | 1.96 × 0.777 / (0.5 × 400kHz) ≈ 7.6µF → 2 × 10µF ceramic X7R |
| Çıkış (51V) | Cout ≥ Iout × D / (ΔVout × fsw) | 1.96 × 0.556 / (0.05 × 400kHz) ≈ 54µF → 4 × 22µF ceramic (100V rating) |
| Bulk | Yük transiyenti için | 1 × 220µF elektrolitik 63V |
| DC bias kaybı | X7R 22µF/100V @ 51V DC | Kapasitans %40-60 düşebilir → ceramic sayısını bu kaybı hesaba katarak seç |

### 14.4 Geri Besleme Dirençleri

| Parametre | Formül | Değer (51V hedef, 0.8V ref) |
|-----------|--------|------------------------------|
| Rtop / Rbot | Vout = 0.8 × (1 + Rtop/Rbot) | Rtop = 634kΩ, Rbot = 10kΩ → 51.2V |
| Tolerans | Direnç %1 dar (1%) | Seri 63.4kΩ × 10 düzeni de kullanılabilir |
| Tolerans etkisi | Vout sapması | %1 Rtop sapması → ±0.5V (PVDD 53.5V limitinin altında ✅) |

**Not:** Geri besleme hattı PCB'de anahtarlayan akım yolundan uzak tutulur; §9.3-3 (PCB layout) ve [[electronic/hardware/pcb-design]].

---

## 15. Web Kaynakları (Doğrulanmış URL Tablosu)

Aşağıdaki URL'ler 2026-10-13 tarihinde HTTP HEAD isteği ile doğrulanmıştır; her URL bu dosyada tektir (diğer vault dosyalarıyla kesişim yok).

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| Boost converter TPS61020 datasheet — 20V'tan yüksek çıkışlı boost ailesi referansı | https://www.ti.com/lit/ds/symlink/tps61020.pdf | Doğrulandı | HTTP 200 |
| BoostLED/boost converter TPS61170 — yüksek voltaj boost topolojisi | https://www.ti.com/lit/ds/symlink/tps61170.pdf | Doğrulandı | HTTP 200 |
| Buck converter TPS54560 — 60V giriş, yüksek voltaj rail regülatörü | https://www.ti.com/lit/ds/symlink/tps54560.pdf | Doğrulandı | HTTP 200 |
| Buck converter TPS5430 — klasik 3A buck, LM2596 alternatifi | https://www.ti.com/lit/ds/symlink/tps5430.pdf | Doğrulandı | HTTP 200 |
| LM2596 datasheet — §4.2 tablosundaki 5V buck kaynak (TI arşivi) | https://www.ti.com/lit/ds/symlink/lm2596.pdf | Doğrulandı | HTTP 200 |
| Ultra-low-noise LDO TPS7A47 — analog VDD rail seçimi için gürültü verisi | https://www.ti.com/lit/ds/symlink/tps7a4700.pdf | Doğrulandı | HTTP 200 |

**Kapsam dışı (⚠️ VERIFICATION REQUIRED):** LTC3862 ve LM5122'nin üretici sayfaları (analog.com / ti.com ürün sayfaları) bu oturumda erişilemez durumdaydı (bağlantı hatası); §4.3-§4.4'teki LTC3862/LM5122 parametreleri vault içi tasarımdan gelir, canlı üretici kaynağıyla çapraz doğrulanmadı. Bkz. §16.

---

## 16. Açık Konular (VERIFICATION REQUIRED)

Truth Mode gereği eşik çelişkileri silinmez; açık madde olarak kaydedilir:

| # | Konu | Çelişki | Durum |
|---|------|---------|-------|
| 1 | Class AB rail gerilimi | Bu dosya §2 ve §4.1 **±42V** kullanır; class-ab.md hedefi **±45V** olarak tanımlanmış olabilir (dosyalar arası eşik farkı) | ⚠️ VERIFICATION REQUIRED — class-ab.md §2 ile birlikte okunmalı; tasarım marjı (42V tipik, 45V max) mi yoksa gerçek çelişki mi olduğu doğrulanmalı |
| 2 | LTC3862 / LM5122 üretici kaynağı | §4.3 parametreleri (verim %95/%94, fiyat ~$5/~$3) canlı datasheet ile çapraz doğrulanmadı | ⚠️ VERIFICATION REQUIRED — üretici erişimi engellendi; fiyatlar tasarım tarihi tahmini |
| 3 | Boost topolojisi seçimi | §2.1 tablosu hem toroidal (AC) hem boost (DC) senaryosu taşır; nihai topoloji (AC mains vs DC boost) mimari kararla kilitlenmedi | ⚠️ VERIFICATION REQUIRED — ADR ile karar bağlanmalı |
| 4 | Kondansatör bankası maliyeti | §3.1 "4 × 10,000µF = Orta maliyet" iddiası tedarikçi fiyatıyla doğrulanmadı | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode