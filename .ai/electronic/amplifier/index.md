---
type: system
category: amplifier-architecture
title: "CoreMusic Electronics — Amplifier Architecture Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Amplifier Architecture

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[electronic/dsp/index]]

---

## 1. Amaç

Amplifier Architecture, CoreMusic ELECTRONICS platformunun tüm amplifikatör tasarımını, koruma sistemlerini, güç yönetimini ve soğutma altyapısını kapsar.

---

## 2. Amplifier Tipleri

| Tip | Dosya | Kullanım |
|-----|-------|----------|
| Class AB | [[class-ab]] | **CoreMusic Ana Amplifikatör** — high-end ses, stüdyo |
| Class D | [[class-d]] | Ev cihazı amfi devresi, oda amfi imalatı, test/ölçüm |

---

## 3. CoreMusic Amplifier Ailesi

### 3.1 Class AB — Ana Amplifikatör Serisi (CoreMusic)

| Model | Kanal | Güç (8Ω) | Çip | Fiyat/ch | Stok | Kullanım |
|-------|-------|----------|-----|----------|------|----------|
| CM-71-AB | 7+1 (7.1) | 100W × 7 + 200W Sub | Discrete MJL3281A | ~$15 | ✅ | **CoreMusic Ana Amfi** |
| CM-51-AB | 5+1 (5.1) | 100W × 5 + 200W Sub | Discrete MJL3281A | ~$15 | ✅ | Orta segment |
| CM-21-AB | 2+1 (2.1) | 100W × 2 + 200W Sub | Discrete MJL3281A | ~$15 | ✅ | Stereo + Sub |
| CM-20-AB | 2 (Stereo) | 100W × 2 | Discrete MJL3281A | ~$15 | ✅ | Stereo |
| CM-10-AB | 1 (Mono) | 100W | Discrete MJL3281A | ~$15 | ✅ | Mono test |
| CM-07-AB | 1 (Mono) | 50W | TDA7294 | ~$2.14 | ✅ | Orta güç |
| CM-06-AB | 1 (Mono) | 38W | LM3886 | ~$4.34 | ✅ | Orta güç |
| CM-05-AB | 1 (Mono) | 20W | LM1875 | ~$4.22 | ✅ | Düşük güç |

### 3.2 Class D — Ev Cihazı / Oda İmalatı / Test Serisi

| Model | Kanal | Güç (8Ω) | Çip | Fiyat/ch | Stok | Kullanım |
|-------|-------|----------|-----|----------|------|----------|
| CM-71-D | 7+1 (7.1) | 50W × 7 + 100W Sub | TPA3255 | ~$4.13 | ⚠️ | Ev cihazı, oda |
| CM-51-D | 5+1 (5.1) | 50W × 5 + 100W Sub | TPA3255 | ~$4.13 | ⚠️ | Ev cihazı, oda |
| CM-21-D | 2+1 (2.1) | 50W × 2 + 100W Sub | TPA3255 | ~$4.13 | ⚠️ | Ev cihazı, test |
| CM-20-D | 2 (Stereo) | 50W × 2 | TPA3255 | ~$4.13 | ⚠️ | Ev cihazı, test |
| CM-10-D | 1 (Mono) | 50W | TPA3250 | ~$2.35 | ✅ | Tek kanal test |
| CM-05-D | 2 (Stereo) | 30W × 2 | TPA3118D2 | ~$0.60 | ✅ | Düşük güç ev |
| CM-03-D | 2 (Stereo) | 15W × 2 | TPA3130D2 | ~$0.50 | ✅ | Masaüstü, kulaklık |
| CM-01-D | 1 (Mono) | 5W | TPA3110D2 | ~$0.50 | ✅ | Mini amp |

### 3.3 Class D Chip Seçim Matrisi

| Çip | Güç@8Ω | Güç@4Ω | Supply | Fiyat (1ku) | Stok | Kullanım |
|-----|--------|--------|--------|-------------|------|----------|
| **TPA3255** | 185W | 315W | 18-53.5V | ~$4.13 | ⚠️ | Profesyonel |
| **TPA3251** | 150W | 175W | 12-38V | ~$3.35 | ✅ | Orta güç |
| **TPA3250** | 70W | 130W | 12-36V | ~$2.35 | ✅ | Bütçe |
| **TPA3116D2** | — | 50W | 4.5-26V | ~$0.70 | ✅ | 35-50W ev |
| **TPA3118D2** | 30W | 60W | 4.5-26V | ~$0.60 | ✅ | 15-30W ev |
| **TPA3130D2** | 15W | 30W | 4.5-26V | ~$0.50 | ✅ | 5-15W masaüstü |
| **TPA3110D2** | 10W | 15W | 4.5-26V | ~$0.50 | ✅ | Mini amp |

### 3.4 Class AB Chip Seçim Matrisi

| Çip | Güç@8Ω | Güç@4Ω | Supply | Fiyat (1ku) | Stok | Kullanım |
|-----|--------|--------|--------|-------------|------|----------|
| **LM3886** | 38W | 68W | ±20-94V | ~$3-5 | ✅ | 35-50W high-end |
| **LM1875** | 20W | 30W | ±16-60V | ~$2-3 | ✅ | 10-20W ev |
| **TDA7294** | 100W | 100W | ±40V | ~$3-5 | ✅ | 50-100W ev |
| **MJL3281A/MJL1302A** | 100W+ | 200W+ | ±42V | ~$2.5/adet | ✅ | Discrete, high-end |

**Not:** CoreMusic'in ana amfisi Class AB CM-71-AB (7.1) modelidir. Class D modelleri ev cihazı devrelerinde, oda amfi imalatında ve test/ölçüm amaçlı kullanılır.

---

## 4. 7.1 Surround Kanal Yapısı

```
Front Left      (20Hz - 20kHz)
Front Right     (20Hz - 20kHz)
Center          (100Hz - 8kHz)
Surround Left   (100Hz - 16kHz)
Surround Right  (100Hz - 16kHz)
Rear Left       (100Hz - 16kHz)
Rear Right      (100Hz - 16kHz)
Subwoofer LFE   (20Hz - 120Hz)
```

Bass Management: Linkwitz-Riley 4. nesil, crossover 80Hz.

---

## 5. Amplifier Bileşenleri

| Bileşen | Dosya | Kapsam |
|---------|-------|--------|
| Class AB Tasarım | [[class-ab]] | Gain stage, bias, thermal |
| Class D Tasarım | [[class-d]] | PWM, MOSFET, filter |
| Koruma Sistemleri | [[protection]] | Kısa devre, termal, DC offset |
| PSU + Soğutma | [[psu-cooling]] | Güç kaynağı, fan, heatsink |

---

## 6. Koruma Sistemleri

| Koruma | Açıklama | Kritiklik |
|--------|----------|-----------|
| Kısa Devre | Çıkış kısa devresi koruması | CRITICAL |
| Aşırı Akım | Maksimum akım sınırı | CRITICAL |
| Aşırı Gerilim | Maksimum gerilim sınırı | HIGH |
| Ters Polarite | Ters bağlanma koruması | HIGH |
| Termal | Isı sensörü + fan kontrolü | HIGH |
| DC Offset | >0.5V DC offset koruma rölesi | CRITICAL |
| Hoparlör Koruma | Soft start/stop | MEDIUM |

Detay: [[protection]]

---

## 7. Güç Seviyeleri ve Chip Eşleşmesi

| Seviye | Class AB Çip | Class D Çip | Kullanım | Ohm |
|--------|-------------|-------------|----------|-----|
| **5W** | — | TPA3110D2 | Mini amp, masaüstü | 8Ω |
| **10W** | LM1875 (30W@4Ω) | TPA3130D2 | Masaüstü, kulaklık | 8Ω |
| **15W** | LM1875 | TPA3130D2 | Küçük oda | 8Ω |
| **20W** | LM1875 | TPA3118D2 | Ev, kitaplık | 8Ω |
| **30W** | LM3886 (68W@4Ω) | TPA3118D2 | Orta oda | 8Ω |
| **35W** | LM3886 | TPA3116D2 | Orta oda | 8Ω |
| **50W** | LM3886 / TDA7294 | TPA3116D2 / TPA3250 | Büyük oda | 8Ω |
| **100W** | Discrete (MJL3281A) | TPA3255 | **CoreMusic standart** | 8Ω |
| **150W** | Discrete | TPA3251 | Salon | 8Ω |
| **185W** | Discrete | TPA3255 | Profesyonel | 8Ω |
| **250W** | Discrete (paralel) | TPA3255 (BTL) | Büyük salon | 4Ω |
| **500W** | Discrete (multi-pair) | TPA3255 (PBTL) | Konser | 2Ω |

---

## 8. Amplifier Tasarım Kuralları

### 8.1 Genel Kurallar

| # | Kural | Açıklama | İhlal Sonucu |
|---|-------|----------|-------------|
| 1 | **Güç Sıralaması** | Önce DSP, sonra analog, en son amplifier | Pop-noise, hasar |
| 2 | **Güç Giriş** | 12V-24V DC + Boost Converter → ±42V | Düşük çıkış gücü |
| 3 | **PCB Katman** | Minimum 4 katman (Class D için zorunlu) | EMI, gürültü |
| 4 | **Topraklama** | Star ground, analog/dijital ayrım | Ground loop |
| 5 | **Koruma** | DC offset, over-current, termal zorunlu | Hoparlör hasarı |
| 6 | **Soğutma** | Heatsink zorunlu (Class AB), opsiyonel (Class D) | Termal kapanma |
| 7 | **Giriş Empedansı** | 10kΩ-47kΩ arası | Empedans uyumsuzluğu |
| 8 | **Çıkış Empedansı** | <0.1Ω (Class AB), <0.01Ω (Class D) | Düşük damping factor |
| 9 | **Filtre** | Class D için LC çıkış filtresi zorunlu | EMI, high-frequency gürültü |
| 10 | **Decoupling** | Her çip yakınına 100nF + 10µF | Besleme gürültüsü |

### 8.2 Class AB Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Bias Ayarı** | Quiescent akım 50-100mA, NTC termistör ile termal tracking |
| 2 | **Output Transistör** | MJL3281A/MJL1302A (en güvenilir) veya 2SC5200/2SA1943 (en ucuz) |
| 3 | **Driver Transistör** | MJE15030/MJE15031 (uyumlu gain linearity) |
| 4 | **Negatif Geri Besleme** | %100 negatif feedback, düşük THD |
| 5 | **Heatsink** | Minimum 500cm², <1°C/W termal direnç |
| 6 | **Güç Transistör Sayısı** | 100W@8Ω için minimum 2 pair (NPN+PNP) |

### 8.3 Class D Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Çıkış Filtresi** | 2. basamak LC, 50kHz kesme, 10µH indüktör |
| 2 | **Diferansiyel Giriş** | TPA32xx serisi için zorunlu |
| 3 | **PCB Yerleşimi** | Güç yolu kısa, sinyal yolu uzun |
| 4 | **Bootstrap** | Her kanal için 220nF bootstrap kapasitörü |
| 5 | **Heat Pad** | TPA3255 için PowerPAD termal bağ |
| 6 | **AM Avoidance** | Switching frequency ayarı ile AM interference önleme |

### 8.4 Güç Besleme Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Giriş Voltajı** | 12V-24V DC (adaptör veya batarya) |
| 2 | **Boost Converter** | LTC3862 (en iyi, %95) veya LM5122 (en ucuz, %94) |
| 3 | **Ripple** | <5mV RMS (LC filtre ile) |
| 4 | **Kapasitör** | Minimum 10,000µF per rail |
| 5 | **Koruma** | Over-voltage, under-voltage, over-current zorunlu |
| 6 | **Soft-Start** | 2 saniye gecikme, rush current sınırlama |
| 7 | **Toroidal** | AC mains gerektiğinde tercih edilir (500VA, %95 verimlilik) |

### 8.5 Koruma Kuralları

| # | Kural | Tetikleme | Aksiyon | Süre |
|---|-------|-----------|---------|------|
| 1 | **DC Offset** | >±0.5V DC | Röle aç (hoparlör ayır) | <10ms |
| 2 | **Over-Current** | >max akım | Güç azalt | <100µs |
| 3 | **Short-Circuit** | 0Ω çıkış | Anında kapanma | <1µs |
| 4 | **Termal** | >80°C | Fan hızı ↑, >100°C kapanma | Sürekli |
| 5 | **Over-Voltage** | >±48V DC | Boost kapat | Anında |
| 6 | **Under-Voltage** | <10V DC | Amplifier durdur | Anında |

### 8.6 Termal Yönetim Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Heatsink** | Class AB için zorunlu, Class D için opsiyonel |
| 2 | **Fan Eşiği** | 60°C'de fan başlar |
| 3 | **Kapanma** | >100°C'de otomatik kapanma |
| 4 | **Termal Sensör** | En sıcak noktaya yerleştir |
| 5 | **Hava Akışı** | Sıcak → soğuk yönünde akış |

### 8.7 Test Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **THD+N Ölçümü** | 1kHz, 1W, 8Ω yük |
| 2 | **SNR Ölçümü** | A-waged, 1kHz referans |
| 3 | **Frekans Yanıtı** | 20Hz-20kHz, ±0.5dB tolerans |
| 4 | **Damping Factor** | 100Hz, 8Ω yük |
| 5 | **Güç Çıkışı** | THD+N=%1 noktasında |
| 6 | **Termal Test** | 1 saat sürekli çalışma |

---

## 9. PCB Katman Önerisi

| Katman | Kullanım | Öneri |
|--------|----------|-------|
| 2 | Düşük-orta güç Class AB | ❌ Yetersiz |
| **4** | **Class D + orta güç** | ✅ **En iyi seçim (TI önerisi)** |
| 6 | Karmaşık mixed-signal | ✅ Profesyonel |
| 8 | Ultra high-end | ⚠️ Gereksiz pahalı |

**4 Katman Stack:**
```
Layer 1: Signal routing (top)
Layer 2: Ground plane (analog)
Layer 3: Power plane (±42V, 5V, 3.3V)
Layer 4: Signal routing (bottom)
```

**Neden 4 katman?**
- TI'ın Class D için önerisi
- Analog/dijital ayrım için yeterli
- EMI containment iyi
- Maliyet uygun (6 katmana göre %40 daha ucuz)

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode |

---

## 10. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Amplifier | [[electronic/dsp/index]] | DSP çıkışı |
| Amplifier | [[electronic/hardware/index]] | PCB tasarımı |
| Amplifier | [[electronic/drivers/index]] | Driver çıkışı |
| Amplifier | [[architecture/07-security/index]] | Koruma sistemleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
