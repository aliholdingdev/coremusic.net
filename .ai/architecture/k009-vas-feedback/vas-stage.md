---
title: "K009 — VAS Stage"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K009 — VAS Stage

**K numarası:** K009 · **Klasör:** `k009-vas-feedback` · **Dosya:** `vas-stage`
**Üst katman:** [[index.md]] (K009 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `audio-hardware-engineer` · `embedded-engineer`

## Amaç

Voltage Amplification Stage (VAS) — Class AB amplifikatörün orta evresini
belgelemek: MPSA06 NPN ile yüksek gerilim kazancı, Miller kompanzasyonu, dominant
kutup/ikutup ve stabilite (phase margin) hesabı, bileşen değerleri ve frekans
tepkisini tek düğümde toplamak. Giriş evresi [[../k008-analog-giris/index]] içinde,
geri besleme ağı [[feedback-ve-kararlilik]] içindedir; bu dosya **yalnız VAS'ı**
sahiplenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| VAS topolojisi, kolektör/emitter dirençleri | Diff-pair giriş evresi (→ [[../k008-analog-giris/index]]) |
| Miller Cm = 10pF, dominant pole 15.9kHz | Çıkış evresi / emitter follower (→ [[../k010-class-ab-cikis/index]]) |
| Phase/gain margin hesabı, UGF | Geri besleme faktörü β ve kapalı devre kazancı (→ [[feedback-ve-kararlilik]]) |
| MPSA06 sınırları (VCEO/IC/hFE) | Güç kaynağı tasarımı (→ [[../k011-guc-koruma-termal/index]]) |

## Arayüz

- **Giriş:** `Base ← From Differential Pair Output` (kaynak L51) — diff-pair kolektör
  çıkışı.
- **Çıkış:** `Collector` → push-pull çıktı evresi girişi (kaynak L44–L48).
- **Güç:** `+35V` kolektör yükü üzerinden · `-35V` emitter (kaynak L31, L59).
- **Sınırlayıcı:** `D1 = BAT54S (Clamp)` opsiyonel diyot (kaynak L40, L112).

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
> hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L10-L26

## Genel Bakış

Voltage Amplification Stage (VAS), Class AB amplifikatörün orta evresidir. Differential pair input'tan gelen sinyali yüksek kazançla yükseltir ve output stage'a iletir. Miller compensation technique kullanılarak stabilite sağlanır. MPSA06 NPN transistörleri tercih edilir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Gerilim Kazancı | 40-60dB (100x-316x) |
| Frekans Bant Genişliği | DC – 1MHz |
| Miller Kondansatörü | 10pF C0G |
| Gain-Bandwidth Product | 40MHz |
| Çıkış Empedansı | > 10kΩ |
| THD Katkısı | < %0.0001 |
| MPSA06 VCEO | 80V |
| MPSA06 IC | 500mA |
| MPSA06 hFE | 30-300 |

### 4.2 — Devre Şeması

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L28-L60

## Devre Şeması

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R1   │ 1kΩ (Collector Load)
                        └───┬───┘
                            │
                    ┌───────┴───────┐
                    │               │
                 ┌──┴──┐         ┌──┴──┐
                 │  Cm │  10pF   │  D1 │  BAT54S (Clamp)
                 │Miller│        └──┬──┘
                 └──┬──┘            │
                    │               │
                    │   Collector   │
                    │               │
                 ┌──┴──┐            │
                 │ Q3  │            │  MPSA06 (NPN)
                 │NPN  │◀───────────┘
                 └──┬──┘
                    │
                 Base ←── From Differential Pair Output
                    │
                 Emitter
                    │
                 ┌──┴──┐
                 │  R2 │  100Ω (Emitter Degeneration)
                 └──┬──┘
                    │
                   -35V
```

### 4.3 — Miller Compensation Analizi + Dominant Pole

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L62-L102

## Miller Compensation Analizi

### Neden Miller Compensation?

```
Açık devre kazancı (Av) çok yüksek olduğunda,
transistörün iç kapasitansı (Cob) Miller etkisi ile
büyür ve bant genişliğini daraltır.

Miller Kondansatörü (Cm) kontrollü bir şekilde
polarite splitsiyon stabilized ederek,
transistörün DC kazancını yüksek tutar,
AC kazancını ise istenen frecuency'de düşürür.
```

### Miller Etkisi Formülü

```
AvMiller = Av_open × Cm / (Cm + 1)
AvMiller ≈ Av_open (eğer Cm >> 1)

f-3dB = 1 / (2π × Av × Rc × Cm)

Örnek:
Av = 1000 (60dB)
Rc = 1kΩ
Cm = 10pF

f-3dB = 1 / (2π × 1000 × 1000 × 10×10⁻¹²)
f-3dB = 15.9 kHz (input-referred)
```

### Dominant Pole

| Parametre | Değer |
|-----------|-------|
| Dominant Pole | 15.9 kHz |
| Second Pole | 1.2 MHz |
| Phase Margin | 65° |
| Gain Margin | 20dB |
| UGF (Unity Gain) | 40MHz |

### 4.4 — Bileşen Değerleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L104-L112

## Bileşen Değerleri

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q3 | MPSA06 | NPN | Ana VAS transistörü |
| R1 | 1kΩ 1/4W | Metal Film | Collector load |
| R2 | 100Ω 1/4W | Metal Film | Emitter degeneration |
| Cm | 10pF | C0G/NP0 | Miller compensation |
| D1 | BAT54S | Schottky | Output clamp (optional) |

### 4.5 — Frekans Tepkisi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L114-L134

## Frekans Tepkisi

```
Kazanç (dB)
    │
 60 ┤──────────────────┐
    │                  │
 40 ┤                  │  -20dB/decade
    │                  │
 20 ┤                  │
    │                  │
  0 ┤                  └──────────────────── Frekans
    │
    └───┬────┬────┬────┬────┬────┬────┬───
       10Hz 100Hz 1kHz 10kHz 100kHz 1MHz

    ├─ DC Kazanç: 60dB (1000x)
    ├─ -3dB Noktası: 15.9kHz
    ├─ 0dB Noktası: 40MHz
    └─ Phase Margin: 65°
```

### 4.6 — Stabilite Analizi (Phase Margin Hesabı)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L136-L149

## Stabilite Analizi

### Phase Margin Hesabı

```
Dominant pole: fp1 = 15.9kHz
Second pole: fp2 = 1.2MHz

@ UGF (40MHz):
Phase = -90° (fp1) - arctan(40/1200) = -90° - 1.9° = -91.9°
Phase Margin = 180° - 91.9° = 88.1°

Sonuç: Sistem kararlı (PM > 45° gerekli)
```

### 4.7 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` - L151-L168

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Differential pair çıkışı |
| K1 Output Stage | Çıkış | Push-pull output'a sinyal |
| K1 Feedback | Geri besleme | Geri besleme noktası |
| K1 Güç Kaynağı | Alt | ±35V besleme |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı
- AC analysis: 60dB DC kazanç, 40MHz UGF doğrulandı
- Transient analysis: Slew rate > 50V/µs
- DC operating point: IC = 5mA, VCE = 30V
- PCB placement: Short traces, close to differential pair

## Kurallar

1. **Miller kondansatörü 10pF C0G/NP0** (kaynak L20, L111) — değer değişikliği stabilite
   hesabını bozar, yalnız ADR ile değiştirilir.
2. **Sıcak yerleşim:** izler kısa, diff-pair'e yakın (kaynak L168).
3. **Phase margin şartı > 45°** (kaynak L148); kaynakta hem tabloda 65° (L100) hem
   hesapta 88.1° (L146) geçmektedir — iki değer `⚠️ VERIFICATION REQUIRED` olarak
   korunur, tek değere indirgenmez.
4. **Simülasyon etiketi:** durum "🟡 Simülasyon Aşamasında" (kaynak L162) — hiçbir
   satır ölçülmüş donanım kanıtı olarak sunulmaz.
5. **Kısıt:** kaynakta olmayan ölçüm değeri `⚠️ VERIFICATION REQUIRED` ile işaretlenir
   (ZERO-HALLUCINATION).

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K009 (VAS) → K008 | **çağrı** (diff-pair çıkışı) | bu dosya §4.2 (Base ← Differential Pair) |
| K009 (VAS) → K010 | **gösterim** (push-pull girişi) | bu dosya §4.7 (Output Stage çıkışı) |
| K009 (VAS) ↔ K009 (feedback) | **gösterim** (geri besleme noktası) | bu dosya §4.7 — ayrıntı [[feedback-ve-kararlilik]] |

> Bu dosya yeni bağımlılık oku **eklemez** (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[feedback-ve-kararlilik]] · [[../k008-analog-giris/index]] · [[../k010-class-ab-cikis/index]] · [[../k011-guc-koruma-termal/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` | 168 | L10–L168 (§4.1–§4.7) |
