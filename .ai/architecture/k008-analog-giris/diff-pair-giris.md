---
title: "K008 — Diff-Pair Giriş"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K008 — Diff-Pair Giriş

**K numarası:** K008 · **Klasör:** `k008-analog-giris` · **Dosya:** `diff-pair-giris`
**Üst katman:** [[index.md]] (K008 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `audio-hardware-engineer` · `embedded-engineer`

## Amaç

Class AB amplifikatörün diferansiyel giriş evresini belgelemek: BC560C eşli PNP
transistör topolojisini, tail current çalışma noktasını, CMRR ve gürültü
analizlerini, giriş empedansı/seviye şartlarını tek düğümde toplamak. Yolun
konnektörden bu evreye kadar olan kısmi [[analog-sinyal-yolu]] düğümündedir; bu
dosya **evrenin kendisini** sahiplenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Diff-pair topolojisi, çalışma noktası (bias) hesabı | XLR giriş filtresi + EMI (→ [[analog-sinyal-yolu]]) |
| CMRR formülü ve hesaplanan değer | VAS evresi ve Miller kompanzasyonu (→ [[../k009-vas-feedback/index]]) |
| Gürültü kaynakları tablosu, transistör eşleme şartları | Çıkış evresi (→ [[../k010-class-ab-cikis/index]]) |
| Giriş empedansı / hassasiyet değerleri | Geri besleme ağı (→ [[../k009-vas-feedback/index]]) |

## Arayüz

- **Giriş:** diferansiyel sinyal (XLR Hot/Cold veya DAC diferansiyel çıkış) →
  `Input(+)` / `Input(-)`, giriş dirençleri `R3/R4 = 47kΩ`.
- **Çıkış:** diff-pair kolektör çıkışı → VAS (`Base ← From Differential Pair Output`).
- **Güç:** `+35V` / `-35V` besleme; tail direnci `R5 = 100Ω`.
- **Bileşen:** `BC560C` (PNP, matched pair), eşleme şartları kaynak L126–L127.

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
> hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` - L10-L26

## Genel Bakış

Differential pair input stage, Class AB amplifikatörün giriş evresidir. Diferansiyel sinyalleri alır,(Common Mode Rejection Ratio) yüksek CMRR ile gürültü bastırması sağlar ve VAS (Voltage Amplification Stage)'a düşük distorsiyonlu sinyal iletir. BC560C low-noise PNP transistörleri kullanılır.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| CMRR | > 100dB @ 1kHz |
| THD | < %0.0005 (1kHz, 1Vrms) |
| Giriş Gürültüsü | < 1nV/√Hz |
| Bias Akımı | 1mA (tail current) |
| Tail Direnci | 100Ω |
| Transistör | BC560C (PNP, matched pair) |
| RθJC (BC560C) | 200°C/W |

### 4.2 — Devre Şeması

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` - L28-L57

## Devre Şeması

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R5   │ 100Ω (Tail Direnci)
                        └───┬───┘
                            │
                     ┌──────┴──────┐
                     │  Tail Node   │
                     │              │
                  ┌──┴──┐       ┌──┴──┐
                  │ Q1  │       │ Q2  │  BC560C (PNP)
                  │NPN  │       │PNP  │  Matched Pair
                  └──┬──┘       └──┬──┘
                     │             │
                  ┌──┴──┐       ┌──┴──┐
                  │  R1 │       │  R2 │  100Ω (Emitter)
                  └──┬──┘       └──┬──┘
                     │             │
                     │             │
                  ┌──┴──┐       ┌──┴──┐
                  │  R3 │       │  R4 │  47kΩ (Input)
                  └──┬──┘       └──┬──┘
                     │             │
                     ▼             ▼
                  Input(+)     Input(-)
                  (Non-Inv)    (Inverting)
```

### 4.3 — Bias Current Hesaplaması

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` - L59-L78

## Bias Current Hesaplaması

### Tail Current

```
ITail = (V+ - VBE - V-) / RTail
ITail = (35V - 0.7V - (-35V)) / 100Ω
ITail = 70V / 100Ω = 700mA (maksimum)
```

### Operating Point

| Parametre | Değer |
|-----------|-------|
| ITail | 1mA (tasarım değeri) |
| IC1 = IC2 | 0.5mA (her biri) |
| VCE | ~35V (her transistör) |
| gm | 19.2 mA/V (IC/VT, VT=26mV) |
| rπ | 5.2kΩ (β/gm, β=100) |
| r0 | 100kΩ (Early voltage consideration) |

### 4.4 — CMRR Analizi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` - L80-L100

## CMRR Analizi

### CMRR Formülü

```
CMRR = Ad / Acm

Ad = Differential Gain = gm × RC
Acm = Common-Mode Gain = gm × RC / (1 + 2 × gm × RE)

CMRR = 1 + 2 × gm × RE
```

### hesaplama

| Parametre | Değer |
|-----------|-------|
| gm | 19.2 mA/V |
| RE (tail) | 100Ω |
| CMRR (hesaplanan) | 100.8 dB |
| CMRR (hedef) | > 100dB |

### 4.5 — Gürültü Analizi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` - L102-L111

## Gürültü Analizi

### Giriş Gürültüsü Kaynakları

| Kaynak | Değer | Etki |
|--------|-------|------|
| Thermal (RTail) | 1.29 nV/√Hz | Düşük |
| Shot (IC) | 0.28 nV/√Hz | Düşük |
| Flicker (1/f) | ~5 nV/√Hz @ 10Hz | Orta |
| Toplam Giriş | < 1 nV/√Hz @ 1kHz | Kabul edilebilir |

### 4.6 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` - L113-L130

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 VAS Stage | Çıkış | VAS girişine sinyal iletir |
| K1 Feedback | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V besleme |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- BC560C seçimi: VBE eşleme < 2mV, hFE eşleme < %5
- Matching fixture: Test düzeneği hazır
- Input coupling: DC coupled (no coupling capacitor)
- Input impedance: 47kΩ differential (standart audio)
- PCB placement: Symmetrical layout, short traces

## Kurallar

1. **Simetri zorunlu:** PCB yerleşimi simetrik, izler kısa (kaynak L130); asimetrik
   yerleşim CMRR'yi düşürür ve onaylanmaz.
2. **Transistör eşleme:** VBE eşleme < 2mV, hFE eşleme < %5 (kaynak L126) — eşleşmemiş
   çift kullanılmaz.
3. **DC bağlı giriş:** coupling kondansatörü yoktur (kaynak L128); kondansatör eklemek
   yalnız ADR ile mümkündür.
4. **Tail current tasarım değeri 1mA** (kaynak L73); kaynakta ayrıca 700mA'lık bir
   "maksimum" hesabı vardır (L66) — ikisi çelişkili görünür, `⚠️ VERIFICATION REQUIRED`.
5. **Kısıt:** kaynakta olmayan ölçüm değeri `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K008 → K009 | **gösterim** (VAS çıkışı) | bu dosya §4.6 (VAS Stage'e çıkış) |
| K008 → K006 | **gösterim** (DAC diferansiyel giriş) | bu dosya §4.6 (AK4458 girişi) |
| K008 → K011 | **gösterim** (±35V besleme) | bu dosya §4.6 (güç kaynağı) |

> Bu dosya yeni bağımlılık oku **eklemez** (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[analog-sinyal-yolu]] · [[../k009-vas-feedback/index]] · [[../k006-dac-adc-zinciri/index]] · [[../k010-class-ab-cikis/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` | 130 | L10–L130 (§4.1–§4.6) |
