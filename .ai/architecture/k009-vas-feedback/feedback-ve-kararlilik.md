---
title: "K009 — Feedback ve Kararlılık"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K009 — Feedback ve Kararlılık

**K numarası:** K009 · **Klasör:** `k009-vas-feedback` · **Dosya:** `feedback-ve-kararlilik`
**Üst katman:** [[index.md]] (K009 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `audio-hardware-engineer` · `embedded-engineer`

## Amaç

Negatif geri besleme ağını ve kararlılık şartlarını belgelemek: Rf/Rg direnç
bölücü ile kazanç ayarı, β oranı, kapalı devre kazancı, distorsiyon azaltımı,
frequency compensation (Bode), stabilite kriterleri (phase/gain margin, UGF, peak)
ve empedans eşleşmesini tek düğümde toplamak. VAS'ın kendisi [[vas-stage]]
düğümündedir; bu dosya **geri besleme ağı + kararlılık** sahiplenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Rf = 20kΩ / Rg = 1kΩ bölücü, β = 0.0476 | VAS Miller kompanzasyonu (→ [[vas-stage]]) |
| Kapalı devre kazancı, THD azaltımı (477x) | Diff-pair giriş evresi (→ [[../k008-analog-giris/index]]) |
| Bode plot, stabilite kriterleri tablosu | Çıkış evresi / Zobel (→ [[../k010-class-ab-cikis/index]]) |
| Feedback bileşen toleransları (%0.1 metal film) | Koruma rölesi / DC offset (→ [[../k011-guc-koruma-termal/index]]) |

## Arayüz

- **Giriş (feedback noktası):** çıkış sinyali geri besleme ağına girer; sinyal akışı
  `Input(+) → Differential Pair → VAS → Output → Speaker` ve dönüş
  `Output → Feedback Network → Differential Pair` (kaynak L50–L55).
- **Bölücü:** `R_f = 20kΩ` (feedback) + `R_g = 1kΩ` (ground) → `β = Rg / (Rf + Rg)`.
- **Çıkış:** geri besleme sinyali diff-pair girişine uygulanır (kaynak L140:
  "Feedback giriş noktası").
- **Güç/ referans:** `±35V referans` (kaynak L143).

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
> hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` - L10-L24

## Genel Bakış

Negative feedback network, amplifikatörün çıkış sinyalini girişe geri besleyerek kazanç stabilitesini, distorsiyon azaltmasını ve bant genişliği genişletmesini sağlar. Resistive divider ile kazanç ayarı yapılır, frequency compensation ile stabilite garantilenir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Açık Devre Kazancı | 80dB (10,000x) |
| Geri Besleme Oranı (β) | 0.05 (1/20) |
| Loop Gain | 60dB (1000x) |
| THD Azaltma | 60dB (1000x) |
| Bant Genişliği | DC – 80kHz |
| Sinyal/Gürültü | > 120dB |

### 4.2 — Devre Şeması (Sinyal Akışı)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` - L26-L56

## Devre Şeması

```
                         ┌──────────────────────────────────┐
                         │         FEEDBACK NETWORK         │
                         │                                  │
                         │   ┌───────┐                      │
                         │   │  R_f  │  20kΩ (Feedback)     │
                         │   └───┬───┘                      │
                         │       │                          │
                         │   ┌───┴───┐                      │
                         │   │  R_g  │  1kΩ (Ground)        │
                         │   └───┬───┘                      │
                         │       │                          │
                         │      GND                         │
                         │                                  │
                         │   β = Rg / (Rf + Rg)             │
                         │   β = 1kΩ / (20kΩ + 1kΩ)        │
                         │   β = 0.0476                     │
                         │                                  │
                         │   Av = 1/β = 21 (26.4dB)         │
                         │                                  │
                         └──────────────────────────────────┘

Signal Flow:

Input(+) ──▶ Differential Pair ──▶ VAS ──▶ Output ──▶ Speaker
                  ▲                                     │
                  │                                     │
                  └──────── Feedback Network ◀──────────┘
```

### 4.3 — Kazanç Hesaplaması + Distorsiyon Azaltma

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` - L58-L80

## Kazanç Hesaplaması

### Kapalı Devre Kazancı

```
Av_closed = Av_open / (1 + Av_open × β)

Av_open = 10,000 (80dB)
β = 0.0476

Av_closed = 10,000 / (1 + 10,000 × 0.0476)
Av_closed = 10,000 / 477
Av_closed = 20.96 (26.4dB)
```

### Distorsiyon Azaltma

```
THD_open = %0.1 (açık devre)
THD_closed = THD_open / (1 + Av_open × β)
THD_closed = %0.1 / 477
THD_closed = %0.00021 (hedef: < %0.001)
```

### 4.4 — Frequency Compensation (Bode Plot + Stabilite Kriterleri)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` - L82-L115

## Frequency Compensation

### Bode Plot

```
Kazanç (dB)
    │
 80 ┤─────────────┐
    │             │
 60 ┤             │ -20dB/decade (dominant pole)
    │             │
 40 ┤             │
    │             │
 20 ┤             │
    │             │
  0 ┤             └────────────────── Frekans
    │
    └───┬────┬────┬────┬────┬────┬───
       10Hz 100Hz 1kHz 10kHz 100kHz 1MHz

    ├─ DC Kazanç: 80dB (10,000x)
    ├─ Unity Gain Frequency: 1MHz
    ├─ Phase Margin: > 60°
    └─ Gain Margin: > 20dB
```

### Stabilite Kriterleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| Phase Margin | > 45° | ✅ 65° |
| Gain Margin | > 10dB | ✅ 20dB |
| UGF | 1-10MHz | ✅ 1MHz |
| Peak | < 3dB | ✅ 1.2dB |

### 4.5 — Bileşen Değerleri + Empedans Eşleşme

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` - L117-L134

## Bileşen Değerleri

| Referans | Değer | Tolerans | Tip | Açıklama |
|----------|-------|----------|-----|----------|
| R_f | 20kΩ | %0.1 | Metal Film | Feedback direnci |
| R_g | 1kΩ | %0.1 | Metal Film | Ground reference |
| C_f | 100pF | %5 | C0G/NP0 | HF compensation |
| R_d | 10Ω | %5 | Metal Film | Damping (optional) |

## Empedans Eşleşme

```
Giriş Empedansı: 47kΩ (differential)
Feedback Empedansı: 21kΩ (Rf + Rg paralel)
Çıkış Empedansı: < 0.1Ω (açık devre feedback ile)

Empedans oranı: 47kΩ / 21kΩ = 2.24:1 (iyi eşleşme)
```

### 4.6 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` - L136-L153

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Feedback giriş noktası |
| K1 Output Stage | Çıkış | Feedback çıkış noktası |
| K1 VAS | Bağlantı | Loop gain katkısı |
| K1 Güç Kaynağı | Alt | ±35V referans |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- Kazanç: 20x (26dB) – LTSpice doğrulandı
- THD: < %0.001 @ 1kHz, 1W – Simülasyon ile verified
- Feedback trace: PCB'de short, shielded routing
- Ground connection: Star ground point'e doğrudan bağlantı
- Component tolerance: %0.1 metal film (precision)

## Kurallar

1. **Kazanç dirençleri %0.1 metal film** (kaynak L121–L122) — tolerans değiştirilemez,
   değişim yalnız ADR ile.
2. **Feedback izi kısa ve shielded**, topraklama star ground'a doğrudan
   (kaynak L151–L152).
3. **Stabilite eşiği:** Phase Margin > 45°, Gain Margin > 10dB (kaynak L110–L113);
   eşiğin altına düşen tasarım onaylanmaz.
4. **β çelişkisi korunur:** teknik tabloda β = 0.05 (1/20, kaynak L20) ve şemada
   β = 0.0476 (kaynak L44) birlikte verilmiştir — ikisi de kaynakta olduğu gibi
   taşınır, birine indirgenmez → `⚠️ VERIFICATION REQUIRED`.
5. **Simülasyon etiketi:** durum "🟡 Simülasyon Aşamasında" (kaynak L147) — hiçbir
   değer ölçülmüş donanım kanıtı olarak sunulmaz.
6. **Kısıt:** kaynakta olmayan ölçüm değeri `⚠️ VERIFICATION REQUIRED` ile işaretlenir
   (ZERO-HALLUCINATION).

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K009 (feedback) → K008 | **çağrı** (geri besleme girişi) | bu dosya §4.6 (Diff Pair giriş noktası) |
| K009 (feedback) → K010 | **gösterim** (çıkıştan geri besleme) | bu dosya §4.2 (Signal Flow) |
| K009 (feedback) ↔ K009 (VAS) | **gösterim** (loop gain katkısı) | bu dosya §4.6 — ayrıntı [[vas-stage]] |

> Bu dosya yeni bağımlılık oku **eklemez** (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[vas-stage]] · [[../k008-analog-giris/index]] · [[../k010-class-ab-cikis/index]] · [[../k011-guc-koruma-termal/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` | 153 | L10–L153 (§4.1–§4.6) |
