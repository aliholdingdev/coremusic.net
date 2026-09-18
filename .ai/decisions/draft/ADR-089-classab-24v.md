# ADR-089: Class AB Amplifier + 6S LiPo Power Architecture

## Status
Draft

## Date
2026-09-18

## Decision Makers
- Bayram Ali (Vault Steward)

## Context

CoreMusic'in ses sistemi için yüksek kaliteli amplifikatör gereksinimi. PDF Technical Documentation v1.0'a uygun 7 katmanlı sistem mimarisi genişletilerek 21 katmanlı (K0-K20) 1020 bileşenli kapsamlı mimari yapı oluşturulmuştur.

Mevcut durum:
- ADR-038: 8.1 Surround ses kartı seçimi (PCM3168A + XMOS XU316)
- ADR-061: Electronics Architecture (Genişletiliyor)
- ADR-039: 7-servis platform (Kapsanıyor)

Gereksinimler:
- Yüksek ses kalitesi (THD <0.005%)
- Pil ile çalışabilme (6S LiPo, 22.2V)
- Laptop adaptörü ile çalışabilme (19-24V DC)
- Modüler yapı (1-8 kanal)
- Sınıf AB topolojisi (Class D değil)

## Decision

### 1. Amplifikatör Topolojisi: Class AB Darlington

**Seçilen Topoloji:** Class AB Darlington Output Stage

**Gerekçe:**
- Class D'den üstün ses kalitesi
- Daha düşük THD (<0.005% @ 1W)
- Daha doğal, sıcak ses profili
- Profesyonel stüdyo uygulamaları için uygun

**Seçilen Bileşenler:**
- Output Transistör: MJL21194 (NPN) + MJL21193 (PNP) — TO-264
- VAS Transistör: KSC3503 — TO-126
- Driver: BD139/BD140 — TO-126
- Diff Pair: BC546B/BC556B — TO-92

**Özellikler:**
- Güç: 50W/kanal @ 8Ω
- THD: <0.005% @ 1W
- Slew Rate: >40V/µs
- Bandwidth: 10Hz-150kHz
- Gain: 23x (27dB)

### 2. Güç Kaynağı: ±35V Boost Converter

**Seçilen Converter:** LM5122 (Texas Instruments)

**Gerekçe:**
- Geniş giriş voltajı aralığı (4.5V-60V)
- Yüksek verimlilik (%96'ya kadar)
- Düşük gürültü (1.2MHz switching)
- Hem boost hem inverting konfigürasyon desteği

**Konfigürasyon:**
- Giriş: 6S LiPo (22.2V nominal) veya Laptop adaptörü (19-24V DC)
- Çıkış: ±35V simetrik
- Koruma: BMS, Overcurrent, Undervoltage, Thermal

### 3. Pil Seçimi: 6S LiPo

**Seçilen Pil:** 6S LiPo (22.2V nominal)

**Gerekçe:**
- Maksimum 24V sınırına uygun
- Yeterli voltaj boost için (22.2V → ±35V)
- Yaygın kullanım, kolay temin
- BMS entegre

**Özellikler:**
- Nominal: 22.2V
- Min: 18V (6S boş)
- Max: 25.2V (6S şarj)
- Kapasite: 10000-20000mAh önerilen

### 4. Mimari Genişletme: 21 Katman (K0-K20)

**Yeni Katmanlar:**
- K16: Class AB Amplifikatör (120 bileşen)
- K17: Güç Kaynağı ±35V (85 bileşen)
- K18: Termal Tasarım (45 bileşen)
- K19: PCB Tasarım (50 bileşen)
- K20: BOM & Üretim (40 bileşen)

**Toplam:** 1020 bileşen, 21 katman

## Consequences

### Olumlu
- Yüksek ses kalitesi (Class AB)
- Pil ile çalışabilme esnekliği
- Modüler 1-8 kanal desteği
- Kapsamlı mimari dokümantasyon

### Olumsuz
- Class AB düşük verimlilik (%55-60)
- Yüksek ısı üretimi (41W/kanal)
- Büyük heatsink gereksinimi
- Yüksek bileşen maliyeti

### Riskler
- Frozen ADR'lerle çelişme riski (DÜŞÜK)
- Isı yönetimi karmaşıklığı (ORTA)
- Üretim karmaşıklığı (ORTA)

## Related ADRs

| ADR | Konu | İlişki |
|-----|------|--------|
| ADR-001 | Vanilla JS + ITCSS | Korunuyor |
| ADR-002 | PDO Mandatory | Korunuyor |
| ADR-004 | Multi-Domain SPA | Korunuyor |
| ADR-038 | 8.1 Sound Card | Korunuyor |
| ADR-061 | Electronics | Genişletiliyor |

## GitHub Referansları

| # | Proje | Stars | Link |
|---|-------|-------|------|
| 1 | classab-amp | — | https://github.com/prydin/classab-amp |
| 2 | classab-amp-mosfet | — | https://github.com/prydin/classab-amp-mosfet |
| 3 | opamp-frontend-class-ab | — | https://github.com/prydin/opamp-frontend-class-ab |
| 4 | Elektor-Fortissimo-100 | — | https://github.com/analoghifi/Elektor-Fortissimo-100 |
| 5 | classab-motorola-an-422 | — | https://github.com/ufelectronics/classab-based-on-motorola-an-422 |

## Review

- [ ] Security Engineer review
- [ ] Embedded Engineer review
- [ ] Vault Steward approval

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode
