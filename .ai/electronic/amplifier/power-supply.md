---
type: electronic
category: power-supply
title: "CoreMusic — Amplifier Power Supply Design"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Power Supply Design

**See also:** [[electronic/amplifier/index]] · [[electronic/hardware/index]]

---

## 1. Amaç

Power Supply Design, CoreMusic amfi güç besleme tasarımını tanımlar.

---

## 2. Güç Besleme Topolojisi

### 2.1 Class D İçin (TPA3255/3251/3250)
```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  DC GİRİŞ    │    │  BOOST       │    │  PVDD ÇIKIŞ  │
│  12V–24V DC  │───▶│  CONVERTER   │───▶│  18-53.5V DC │
│  (Adaptör/   │    │  Step-Up     │    │  (Tek Rail)  │
│   Batarya)   │    │  12V→51V     │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
                           │
                    ┌──────┴──────┐
                    │  EMI Filtre │
                    │  LC Filtre  │
                    └─────────────┘

Ayrıca gerekli:
┌──────────────┐    ┌──────────────┐
│  12V DC      │───▶│  GVDD (12V)  │  Gate drive supply
│  (Aynı kaynak)│    │  RC filtre ile│  GVDD_AB, GVDD_CD ayrı
└──────────────┘    └──────────────┘
```

### 2.2 Class AB İçin
```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  DC GİRİŞ    │    │  BOOST       │    │  ±42V DC     │
│  12V–24V DC  │───▶│  CONVERTER   │───▶│  (Çift Rail) │
│  (Adaptör/   │    │  Step-Up     │    │  +42V / -42V │
│   Batarya)   │    │  12V→±42V    │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
```

### 2.3 Alternatif Topoloji (Toroidal Trafo — Sadece AC Giriş)
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

---

## 3. Güç Kaynağı Parametreleri

### 3.1 Class D (TPA3255/3251/3250) — Tek Rail

| Parametre | Değer | Not |
|-----------|-------|-----|
| **PVDD (Power Stage)** | **18V–53.5V DC** | Tek rail, boost converter ile yükseltilir |
| **GVDD (Gate Drive)** | **12V DC** | RC filtre ile PVDD'den |
| **VDD (Internal)** | **12V DC** | Low-noise regülatör |
| **Bootstrap** | **33nF ceramic** | 0603/0805, her half-bridge için |
| **PVDD Decoupling** | **1µF ceramic** | Her PVDD_X node'a yakın |
| **GVDD Decoupling** | **10µF + 0.1µF** | VDD pinine yakın |

### 3.2 Class AB — Çift Rail

| Parametre | Değer | Not |
|-----------|-------|-----|
| **+VCC** | **+42V DC** | Boost converter ile yükseltilir |
| **-VCC** | **-42V DC** | Boost converter ile yükseltilir |
| **GND** | **0V** | Toprak noktası |

### 3.3 TPA3255 İçin Kritik Kurallar (TI Datasheet)

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **GVDD Separation** | GVDD_AB, GVDD_CD ve VDD arasında RC filtre |
| 2 | **Bootstrap** | Her half-bridge için 33nF ceramic (0603/0805) |
| 3 | **PVDD Decoupling** | Her PVDD_X node'a 1µF ceramic, pin'e yakın |
| 4 | **Low-Noise Supply** | 12V ve 51V için low-noise, low-impedance regülatör |
| 5 | **Power-up Sequence** | Kritik değil ama RESET supply yerleşene kadar bekle |
| 6 | **Güç Tüketimi** | Idle: <2.5W (TPA3255), <1W (TPA3251/3250) |

### 3.1 Giriş Voltajı Seçenekleri

| Kaynak | Voltaj | Kullanım |
|--------|--------|----------|
| DC Adaptör | 12V/19V/24V | Ev kullanımı |
| USB-C PD | 20V | Taşınabilir |
| Batarya | 12V (3S LiPo) | Kablosuz |
| Otomotiv | 12V/24V | Araç içi |

---

## 4. Boost Converter Tasarımı

### 4.1 Class D İçin (Tek Rail)

| Parametre | Değer |
|-----------|-------|
| Tip | Synchronous Boost |
| Giriş | 12V–24V DC |
| Çıkış | 18V–53.5V DC (TPA3255 için tipik 51V) |
| Güç | 500W max |
| Verimlilik | >90% |
| Anahtarlama Frekansı | 200kHz–500kHz |
| Indktör | 10µH–22µH, high-current |
| MOSFET | Low-Rds(on) N-ch + P-ch |
| Koruma | Over-current, over-voltage, thermal |

### 4.2 Class AB İçin (Çift Rail)

| Parametre | Değer |
|-----------|-------|
| Tip | Dual-output Boost veya Toroidal |
| Giriş | 12V–24V DC veya 115V/230V AC |
| Çıkış | ±42V DC |
| Güç | 500W max |
| Verimlilik | >90% (boost) veya >85% (toroidal) |

### 4.1 Boost Converter Çip Seçenekleri

| # | Çip | Üretici | Giriş | Çıkış | Güç | Verimlilik | Fiyat | Öneri |
|---|-----|---------|-------|-------|-----|-----------|-------|-------|
| 1 | **LTC3862** | ADI | 12-24V | ±42V | 500W | 95% | ~$5 | ✅ **En iyi** |
| 2 | **LM5122** | TI | 12-24V | 100V | 500W | 94% | ~$3 | ✅ **En ucuz** |
| 3 | **Custom** | — | 12-24V | ±42V | Özelleştirilmiş | 90%+ | Değişken | ⚠️ Tasarım gerektirir |

**Seçim Kılavuzu:**
- Yüksek verimlilik → **LTC3862** (%95)
- Düşük maliyet → **LM5122** (~$3)
- Tam kontrol → **Custom** (MOSFET + Indüktör + PWM controller)

### 4.2 Boost Converter Akışı
```
12V–24V DC ──▶ EMI Filtre ──▶ Boost MOSFET ──▶ Indktör ──▶ Çıkış Kapasitörü ──▶ ±42V DC
                                          │
                                    Gate Driver
                                          │
                                    PWM Controller
                                          │
                                    Feedback (±42V)
```

---

## 5. Toroidal Transformer (Alternatif — AC Giriş)

| Parametre | Değer |
|-----------|-------|
| Tip | Toroidal |
| Güç | 500VA (8+1 kanal için) |
| Çıkış | ±42V AC |
| Manyetik Yayılım | Düşük |
| Verimlilik | >95% |

**Not:** Toroidal trafolu tasarım sadece AC mains gerektiğinde kullanılır. DC giriş (12V-24V) ile çalışan cihazlarda boost converter tercih edilir.

---

## 6. Güç Filtresi

| Aşama | Kapasitör | Amaç |
|-------|-----------|------|
| Smoothing | 10,000µF | Ripple azaltma |
| Decoupling | 100nF | Yüksek frekans temizliği |
| Bypass | 10µF | Orta frekans temizliği |

---

## 7. Koruma Devreleri

| Koruma | Tetikleme | Tepki |
|--------|-----------|-------|
| Over-Voltage | >±48V DC | Boost kapat |
| Under-Voltage | <10V DC | Amplifier durdur |
| Over-Current | >max akım | Boost sınırla |
| Thermal | >80°C | Fan hızı ↑, >100°C kapat |
| Short Circuit | 0Ω çıkış | Anında kapanma |

---

## 7. Boost Converter Sorunları ve Çözümleri

### 7.1 LTC3862 Sorunları

| # | Sorun | Belirti | Çözüm |
|---|-------|---------|-------|
| 1 | **Yüksek giriş voltajında termal** | Yüksek VIN'de iç LDO aşırı ısınır | Harici 5V/12V bias supply kullan |
| 2 | **Multi-phase INTVCC** | INTVCC pinleri birleştirilirse termal yük | Her IC için ayrı bypass capacitor |
| 3 | **Güç açma sırası** | VIN INTVCC'den önce gelirse latchup | Güç sıralaması kontrol et |
| 4 | **Yük altında çıkış düşüşü** | Sense resistor yanlış veya layout kötü | Sense resistor doğrula, layout iyileştir |
| 5 | **Duty cycle >%92** | Yüksek VIN/VOUT oranında kararsızlık | Slope compensation ayarla |

### 7.2 LM5122 Sorunları

| # | Sorun | Belirti | Çözüm |
|---|-------|---------|-------|
| 1 | **Yüksek çıkış gürültüsü** | SW node uzun, anten gibi davranır | SW routing'i kısalt, snubber ekle |
| 2 | **Output kapasitör yerleşimi** | Ripple yüksek | Ceramik + bulk kombinasyonu |
| 3 | **Gate driver gürültüsü** | HO ve HS izleri ayrı | HO ve HS izlerini yakın tut |
| 4 | **Current sense gürültüsü** | Yanlış akım ölçümü | CSP/CSN'e RC filtre ekle |
| 5 | **Input kapasitör yetersiz** | Yük altında voltaj düşüşü | Yeterli input capacitance ekle |

### 7.3 Genel Boost Converter Sorunları

| # | Sorun | Belirti | Çözüm |
|---|-------|---------|-------|
| 1 | **PCB Layout** | Gürültü, verimlilik düşüklüğü | TI layout guidelines takip et |
| 2 | **Output ripple** | Yüksek voltaj dalgalanması | Ceramik + bulk kapasitör kombinasyonu |
| 3 | **Inductor doygunluğu** | Aşırı akım, kararsızlık | Isat > ILIM + %20 margin |
| 4 | **Toprak döngüsü** | Gürültü, kararsızlık | Solid ground plane, star ground |
| 5 | **EMI** | Elektromanyetik girişim | Shield, ferrite bead, EMI filtre |

### 7.4 LTC3862 İçin Önemli Kurallar

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **INTVCC Bağlantısı** | Multi-phase'de INTVCC pinlerini BİRLEŞTİRME |
| 2 | **Bypass Capacitor** | INTVCC'ye minimum 4.7µF (50nC+ MOSFET için 10µF) |
| 3 | **3V8 Capacitor** | 1nF ceramic, SGND'ye yakın |
| 4 | **Thermal** | Yüksek VIN'de harici bias supply kullan |
| 5 | **SLOPE Pin** | Float: 1.0, GND: 0.625, 3V8: 1.66 |
| 6 | **SS Pin** | Multi-phase'de tüm SS pinlerini birleştir |

### 7.5 LM5122 İçin Önemli Kurallar

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **SW Routing** | Kısa tut, anten gibi davranmasını engelle |
| 2 | **Output Caps** | Ceramik (ripple) + bulk (transient) kombinasyonu |
| 3 | **Snubber** | High-side MOSFET'e RC snubber ekle |
| 4 | **Gate Routing** | HO ve HS izlerini yakın tut |
| 5 | **Current Sense** | CSP/CSN'e 100Ω + 100pF filtre |
| 6 | **Ground Plane** | Indüktör altına solid GND plane |
| 7 | **Input Caps** | 150W+ uygulama için minimum 47µF bulk |

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
