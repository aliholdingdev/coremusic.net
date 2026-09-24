---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K16 Class AB Amplifikatör Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
son_guncelleme: "2026-09-24, kaynak: 3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K16: Class AB Amplifikatör Layer

**Katman:** K16 (Class AB Amplifikatör)
**Kapsam:** Class AB Amplifikatör — K17–K20 yalnızca özet referanstır; kanonik dosyalar: `k17-guc-kaynagi/`, `k18-termal/`, `k19-pcb/`, `k20-bom/`
**Sorumlu Agent:** Audio Hardware Engineer
**Bileşen Sayısı:** 370

---

## 1. K16: Class AB Amplifikatör

### 1.1 Teknik Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class AB Darlington |
| Output Transistör | MJL21194 (NPN) / MJL21193 (PNP) |
| Güç | 50W/kanal @ 8Ω |
| THD+N | <0.005% @ 1W |
| SNR | >100dB |
| Kanal | 8 (modüler) |
| Gain | 27dB |
| Input Impedans | 47kΩ |
| Frequency Response | 20Hz-20kHz ±0.5dB |

### 1.2 Devre Topolojisi

```
Input Stage: Diferansiyel çift (BC546B/BC556B)
VAS: Voltage Amplifier Stage (KSC5026)
Output: Darlington (MJL21194/MJL21193)
Bias: Vbe multiplier (BD139)
Protection: DC offset, thermal, short circuit
```

---

## 2. K17: Güç Kaynağı ±35V

### 2.1 Teknik Özellikler

| Parametre | Değer |
|-----------|-------|
| Giriş | 22.2V (6S LiPo) veya 19-24V DC |
| Çıkış | ±35V simetrik |
| Topoloji | Interleaved Dual Boost |
| Controller | LM5122 × 2 |
| Verimlilik | %96 |
| Ripple | <50mV p-p |
| Koruma | UVP, OVP, OCP, OTP |

### 2.2 Güç Akışı

```
6S LiPo (22.2V) → LM5122 Boost → +35V → Class AB (NPN)
                 → LM5122 Invert → -35V → Class AB (PNP)

Battery Management:
  - UVP: 3.0V/cell (18V total)
  - OVP: 4.2V/cell (25.2V total)
  - OCP: 10A per channel
  - OTP: 60°C
```

---

## 3. K18: Termal Tasarım

### 3.1 Termal Parametreler

| Parametre | Değer |
|-----------|-------|
| Max Sıcaklık | 60°C (full load) |
| Heatsink | Fischer SK53-100-SA |
| Heatsink Boyutu | 300×75×49mm |
| Thermal Resistance | 0.3°C/W |
| Fan | 80mm PWM (Noctua NF-A8) |
| Fan Hızı | Sıcaklık kontrollü |
| Thermal Cutoff | KSD301 (72°C) |

### 3.2 Fan Kontrol Profili

```
<40°C: Fan yok (passive)
40-50°C: Fan %25
50-60°C: Fan %50
>60°C: Fan %100
>72°C: Thermal cutoff (KSD301)
```

---

## 4. K19: PCB Tasarımı

### 4.1 Stackup

```
Layer 1: Signal (top) — Components, high-speed traces
Layer 2: Ground — Continuous ground plane
Layer 3: Signal — I2S, control signals
Layer 4: Power — +35V, -35V, +3.3V, +5V
Layer 5: Ground — Continuous ground plane
Layer 6: Signal (bottom) — Components, low-speed traces
```

### 4.2 Impedans Kontrolü

| Sinyal | Impedans | Tolerance |
|--------|----------|-----------|
| USB 2.0 | 90Ω differential | ±10% |
| I2S | 50Ω single-ended | ±10% |
| Clock | 50Ω single-ended | ±10% |
| Power | Low impedance | — |

---

## 5. K20: BOM & Üretim

### 5.1 BOM Özeti

| Kategori | Bileşen | Adet | Birim | Toplam |
|----------|---------|------|-------|--------|
| Amplifikatör | MJL21194 | 8 | $3.50 | $28 |
| Amplifikatör | MJL21193 | 8 | $3.50 | $28 |
| DAC | PCM3168A | 1 | $8.50 | $8.50 |
| USB | XMOS XU316 | 1 | $12.00 | $12 |
| Boost | LM5122 | 2 | $4.50 | $9 |
| Pasif | Çeşitli | 800+ | ~$0.10 | ~$180 |
| PCB | 6-layer | 1 | $50 | $50 |
| **TOPLAM** | | **~1000** | | **~$430** |

### 5.2 Tedarikçiler

| Tedarikçi | Kullanım |
|-----------|----------|
| Mouser | Ana tedarikçi |
| Digikey | Alternatif |
| LCSC | Uygun fiyatlı |
| JLCPCB | PCB üretimi |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

*K16 Class AB Amplifikatör Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*


---

## Alt Katman Şeması (K16.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K16 katmanını onaylı şema biçiminde (K16 → K16.a → K16.a.b → K16.a.b.c) belgeler. Alanlar (a) amplifikatör blokları ve koruma grupları, alt alanlar (b) dosyalardaki H2 bölüm başlıkları, yapraklar (c) k16-class-ab/ kanonik MD başlıklar + index.md/README.md bileşen-tablosu satırları + frontend-restructuring-plan §2.1 L16 satırından türetilmiştir (c=280 kanıt havuzundan seçilmiştir). Uydurma düğüm yoktur.

**Şema kuralları:**

1. Zorunlu şema: `K16` → `K16.a` → `K16.a.b`; seviye-4 (`K16.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K16–K20 beş BAĞIMSIZ üretim katmanıdır; hiyerarşi yok, birbirine alt değildir (matris §1.1, §1.3 K3, §5.2 #21).
5. Bağımlılık bağlamı: K16 ↔ K1 (çift yönlü; K16 bağımsız üretim katmanı — matris §2.2). K16'nın K17–K20'ye bağımlılığı YOKTUR; tüm bağlantılar K1 üzerinden yürür (matris §5.2 #21).
6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

**Kanoniklik notu — K16 ↔ K1 dosya kopyaları:** `diff-pair-input.md`, `feedback-network.md`, `output-stage.md` ve `vas-stage.md` `.ai/architecture/k1-donanim/` altında da bulunur; **kanonik kaynak `k16-class-ab/`'dir**, K1 klasöründeki kopyalar yalnız referans tutar. Bu dört dosyanın yaprak kanıtları bu README'de K16 üzerinden sayılmıştır.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K16` (a alan) | 11 | 11 | 11 | +0 |
| `K16.a.b` (a×b alt alan) | 77 | 77 | — | 0 |
| `K16.a.b.c` (c yaprak) | 280 | 280 | 280 | +0 |
| **Toplam düğüm** | **357** | **357** | **357** | **+0** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K16.1` | Sistem Tasarımı & 8 Kanal | 7 | 44 | `8-channel-design.md` |
| `K16.2` | Diferansiyel Giriş | 7 | 13 | `diff-pair-input.md` |
| `K16.3` | Akım Aynası & Aktif Yük | 7 | 12 | `current-mirror.md` |
| `K16.4` | VAS & Frekans Kompanzasyonu | 7 | 25 | `vas-stage.md`, `frequency-compensation.md` |
| `K16.5` | Bias: Vbe Multiplier | 7 | 13 | `vbe-multiplier.md` |
| `K16.6` | Sürücü: Darlington Çift | 7 | 12 | `darlington-pair.md` |
| `K16.7` | Çıkış Stage (Push-Pull) | 7 | 11 | `output-stage.md` |
| `K16.8` | Geri Besleme Ağı | 7 | 14 | `feedback-network.md` |
| `K16.9` | Güç Transistörleri & Eşleştirme | 7 | 22 | `mjle21194.md`, `mjle21193.md` |
| `K16.10` | Koruma Devreleri | 7 | 57 | `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` |
| `K16.11` | Sistem Entegrasyonu | 7 | 57 | `power-supply-requisite.md`, `thermal-design.md`, `spice-model.md`, `bom-classab.md` |

### K16.1 — Sistem Tasarımı & 8 Kanal

**Sorumluluk:** Katman genel topolojisi ve 8 kanal modüler mimarisi; index/README/plan kanıt satırlarıyla katman parametreleri (topoloji, güç, kazanç, koruma).
**Kanıt dosyaları:** `8-channel-design.md` — havuz 44 başlık, kullanıldı 44.

#### K16.1.1 — Genel Bakış / Sistem Blok Diyagramı

- **Sorumluluk:** COREMUSIC K16, 8 bağımsız kanaldan oluşan çok kanallı Class AB güç amplifikatörüdür. Her kanal 100W @ 8Ω çıkış sağlar. Toplam çıkış gücü 800W continuous'dur.…
- **Kanıt:** `8-channel-design.md` § Genel Bakış / Sistem Blok Diyagramı (2 bölüm başlığı)

- **K16.1.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC K16, 8 bağımsız kanaldan oluşan çok kanallı Class AB güç amplifikatörüdür. Her kanal 100W @ 8Ω çıkış sağlar. Toplam çıkış gücü 800W continuous'dur. Dual-mono güç kaynağı, her 2 kanal için…
  - Kanıt: `8-channel-design.md` § Genel Bakış
- **K16.1.1.2 — Sistem Blok Diyagramı**
  - Sorumluluk: «Sistem Blok Diyagramı» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Sistem Blok Diyagramı

#### K16.1.2 — Kanal Özellikleri / Güç Dağılımı

- **Sorumluluk:** «Kanal Özellikleri» — 8-channel-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `8-channel-design.md` § Kanal Özellikleri / Güç Dağılımı (2 bölüm başlığı)

- **K16.1.2.3 — Kanal Özellikleri**
  - Sorumluluk: «Kanal Özellikleri» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Kanal Özellikleri
- **K16.1.2.4 — Güç Dağılımı**
  - Sorumluluk: «Güç Dağılımı» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Güç Dağılımı

#### K16.1.3 — Kanal Topolojisi (Her Kanal) / Koruma Devresi (Her Kanal)

- **Sorumluluk:** «Kanal Topolojisi (Her Kanal)» — 8-channel-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `8-channel-design.md` § Kanal Topolojisi (Her Kanal) / Koruma Devresi (Her Kanal) (2 bölüm başlığı)

- **K16.1.3.5 — Kanal Topolojisi (Her Kanal)**
  - Sorumluluk: «Kanal Topolojisi (Her Kanal)» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Kanal Topolojisi (Her Kanal)
- **K16.1.3.6 — Koruma Devresi (Her Kanal)**
  - Sorumluluk: «Koruma Devresi (Her Kanal)» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Koruma Devresi (Her Kanal)

#### K16.1.4 — PCB Layout Stratejisi / Termal Tasarım Özeti

- **Sorumluluk:** «PCB Layout Stratejisi» — 8-channel-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `8-channel-design.md` § PCB Layout Stratejisi / Termal Tasarım Özeti (2 bölüm başlığı)

- **K16.1.4.7 — PCB Layout Stratejisi**
  - Sorumluluk: «PCB Layout Stratejisi» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § PCB Layout Stratejisi
- **K16.1.4.8 — Termal Tasarım Özeti**
  - Sorumluluk: «Termal Tasarım Özeti» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Termal Tasarım Özeti

#### K16.1.5 — Liste Fiyat Tahmini

- **Sorumluluk:** «Liste Fiyat Tahmini» — 8-channel-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `8-channel-design.md` § Liste Fiyat Tahmini (1 bölüm başlığı)

- **K16.1.5.9 — Liste Fiyat Tahmini**
  - Sorumluluk: «Liste Fiyat Tahmini» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Liste Fiyat Tahmini

#### K16.1.6 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — 8-channel-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `8-channel-design.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.1.6.10 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — 8-channel-design.md dosyasında belgelenen bölüm.
  - Kanıt: `8-channel-design.md` § Durum: Implementasyon

#### K16.1.7 — Katman Geneli (index/README/plan kanıtı)

- **Sorumluluk:** index.md — Genel Bakış başlığı katman ana sayfasında tanımlanır.
- **Kanıt:** `index.md`, `frontend-restructuring-plan.md`, `README.md` § Katman Geneli (index/README/plan kanıtı) (34 bölüm başlığı)

- **K16.1.7.11 — Genel Bakış**
  - Sorumluluk: index.md — Genel Bakış başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § Genel Bakış
- **K16.1.7.12 — Devre Blok Diyagramı**
  - Sorumluluk: index.md — Devre Blok Diyagramı başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § Devre Blok Diyagramı
- **K16.1.7.13 — Ana Topoloji Özellikleri**
  - Sorumluluk: index.md — Ana Topoloji Özellikleri başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § Ana Topoloji Özellikleri
- **K16.1.7.14 — Sinyal Akışı**
  - Sorumluluk: index.md — Sinyal Akışı başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § Sinyal Akışı
- **K16.1.7.15 — K16 Modül Bağımlılıkları**
  - Sorumluluk: index.md — K16 Modül Bağımlılıkları başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § K16 Modül Bağımlılıkları
- **K16.1.7.16 — Tasarım Kısıtlamalari**
  - Sorumluluk: index.md — Tasarım Kısıtlamalari başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § Tasarım Kısıtlamalari
- **K16.1.7.17 — Durum: Implementasyon**
  - Sorumluluk: index.md — Durum: Implementasyon başlığı katman ana sayfasında tanımlanır.
  - Kanıt: `index.md` § index.md § Durum: Implementasyon
- **K16.1.7.18 — L16 Class AB Amplifikatör (50W/kanal)**
  - Sorumluluk: frontend-restructuring-plan.md §2.1 üst katman haritası L16 satırı — Class AB amplifikatör katman tanımı.
  - Kanıt: `frontend-restructuring-plan.md` § §2.1 Üst Katman Haritası > L16
- **K16.1.7.19 — 1. K16: Class AB Amplifikatör**
  - Sorumluluk: README.md § 1. K16: Class AB Amplifikatör — K16 bölüm başlığı.
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör
- **K16.1.7.20 — 1.1 Teknik Özellikler**
  - Sorumluluk: README.md § 1.1 Teknik Özellikler — K16 bölüm başlığı.
  - Kanıt: `README.md` § README.md § 1.1 Teknik Özellikler
- **K16.1.7.21 — 1.2 Devre Topolojisi**
  - Sorumluluk: README.md § 1.2 Devre Topolojisi — K16 bölüm başlığı.
  - Kanıt: `README.md` § README.md § 1.2 Devre Topolojisi
- **K16.1.7.22 — Parametre**
  - Sorumluluk: README.md bileşen-tablosu satırı: Parametre — Değer
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.23 — Topoloji**
  - Sorumluluk: README.md bileşen-tablosu satırı: Topoloji — Class AB Darlington
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.24 — Output Transistör**
  - Sorumluluk: README.md bileşen-tablosu satırı: Output Transistör — MJL21194 (NPN) / MJL21193 (PNP)
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.25 — Güç**
  - Sorumluluk: README.md bileşen-tablosu satırı: Güç — 50W/kanal @ 8Ω
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.26 — THD+N**
  - Sorumluluk: README.md bileşen-tablosu satırı: THD+N — <0.005% @ 1W
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.27 — SNR**
  - Sorumluluk: README.md bileşen-tablosu satırı: SNR — >100dB
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.28 — Kanal**
  - Sorumluluk: README.md bileşen-tablosu satırı: Kanal — 8 (modüler)
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.29 — Gain**
  - Sorumluluk: README.md bileşen-tablosu satırı: Gain — 27dB
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.30 — Input Impedans**
  - Sorumluluk: README.md bileşen-tablosu satırı: Input Impedans — 47kΩ
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.31 — Frequency Response**
  - Sorumluluk: README.md bileşen-tablosu satırı: Frequency Response — 20Hz-20kHz ±0.5dB
  - Kanıt: `README.md` § README.md § 1. K16: Class AB Amplifikatör tablosu
- **K16.1.7.32 — Parametre**
  - Sorumluluk: README.md bileşen-tablosu satırı: Parametre — Değer
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.33 — Giriş**
  - Sorumluluk: README.md bileşen-tablosu satırı: Giriş — 22.2V (6S LiPo) veya 19-24V DC
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.34 — Çıkış**
  - Sorumluluk: README.md bileşen-tablosu satırı: Çıkış — ±35V simetrik
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.35 — Topoloji**
  - Sorumluluk: README.md bileşen-tablosu satırı: Topoloji — Interleaved Dual Boost
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.36 — Controller**
  - Sorumluluk: README.md bileşen-tablosu satırı: Controller — LM5122 × 2
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.37 — Verimlilik**
  - Sorumluluk: README.md bileşen-tablosu satırı: Verimlilik — %96
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.38 — Ripple**
  - Sorumluluk: README.md bileşen-tablosu satırı: Ripple — <50mV p-p
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.39 — Koruma**
  - Sorumluluk: README.md bileşen-tablosu satırı: Koruma — UVP, OVP, OCP, OTP
  - Kanıt: `README.md` § README.md § 2. K17: Güç Kaynağı ±35V tablosu
- **K16.1.7.40 — Parametre**
  - Sorumluluk: README.md bileşen-tablosu satırı: Parametre — Değer
  - Kanıt: `README.md` § README.md § 3. K18: Termal Tasarım tablosu
- **K16.1.7.41 — Max Sıcaklık**
  - Sorumluluk: README.md bileşen-tablosu satırı: Max Sıcaklık — 60°C (full load)
  - Kanıt: `README.md` § README.md § 3. K18: Termal Tasarım tablosu
- **K16.1.7.42 — Heatsink**
  - Sorumluluk: README.md bileşen-tablosu satırı: Heatsink — Fischer SK53-100-SA
  - Kanıt: `README.md` § README.md § 3. K18: Termal Tasarım tablosu
- **K16.1.7.43 — Heatsink Boyutu**
  - Sorumluluk: README.md bileşen-tablosu satırı: Heatsink Boyutu — 300×75×49mm
  - Kanıt: `README.md` § README.md § 3. K18: Termal Tasarım tablosu
- **K16.1.7.44 — Thermal Resistance**
  - Sorumluluk: README.md bileşen-tablosu satırı: Thermal Resistance — 0.3°C/W
  - Kanıt: `README.md` § README.md § 3. K18: Termal Tasarım tablosu

### K16.2 — Diferansiyel Giriş

**Sorumluluk:** Uzun kuyruklu diff pair giriş katı: tail akımı, transconductance, CMRR ve giriş empedansı hesabı.
**Kanıt dosyaları:** `diff-pair-input.md` — havuz 13 başlık, kullanıldı 13.

#### K16.2.1 — Genel Bakış / Devre Şeması

- **Sorumluluk:** Fark (differansiyel) çift giriş katı, amplifikatörün giriş empedansını belirler, CMRR (Ortak Mod Reddi) sağlar ve geri besleme sinyali ile giriş sinyalini…
- **Kanıt:** `diff-pair-input.md` § Genel Bakış / Devre Şeması (2 bölüm başlığı)

- **K16.2.1.1 — Genel Bakış**
  - Sorumluluk: Fark (differansiyel) çift giriş katı, amplifikatörün giriş empedansını belirler, CMRR (Ortak Mod Reddi) sağlar ve geri besleme sinyali ile giriş sinyalini karşılaştırır. Long-tail pair topolojisi,…
  - Kanıt: `diff-pair-input.md` § Genel Bakış
- **K16.2.1.2 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Devre Şeması

#### K16.2.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — diff-pair-input.md dosyasında belgelenen bölüm.
- **Kanıt:** `diff-pair-input.md` § Teknik Spesifikasyonlar (1 bölüm başlığı)

- **K16.2.2.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Teknik Spesifikasyonlar

#### K16.2.3 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — diff-pair-input.md dosyasında belgelenen bölüm.
- **Kanıt:** `diff-pair-input.md` § Hesaplamalar (6 bölüm başlığı)

- **K16.2.3.4 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Hesaplamalar
- **K16.2.3.5 — Tail Akımı**
  - Sorumluluk: «Tail Akımı» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Hesaplamalar > Tail Akımı
- **K16.2.3.6 — Transconductance (gm)**
  - Sorumluluk: «Transconductance (gm)» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Hesaplamalar > Transconductance (gm)
- **K16.2.3.7 — Diferansiyel Kazanç (Ad)**
  - Sorumluluk: «Diferansiyel Kazanç (Ad)» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Hesaplamalar > Diferansiyel Kazanç (Ad)
- **K16.2.3.8 — CMRR Hesabı**
  - Sorumluluk: «CMRR Hesabı» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Hesaplamalar > CMRR Hesabı
- **K16.2.3.9 — Giriş Empedansı**
  - Sorumluluk: «Giriş Empedansı» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Hesaplamalar > Giriş Empedansı

#### K16.2.4 — Bileşen Seçim Kriterleri

- **Sorumluluk:** «Bileşen Seçim Kriterleri» — diff-pair-input.md dosyasında belgelenen bölüm.
- **Kanıt:** `diff-pair-input.md` § Bileşen Seçim Kriterleri (1 bölüm başlığı)

- **K16.2.4.10 — Bileşen Seçim Kriterleri**
  - Sorumluluk: «Bileşen Seçim Kriterleri» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Bileşen Seçim Kriterleri

#### K16.2.5 — Termal Hassasiyet

- **Sorumluluk:** ΔVbe/ΔT: -2.2mV/°C (her transistör için) Eşleştirme: Q1 ve Q2 sıcaklıkta eşit olmalı (termal layout)
- **Kanıt:** `diff-pair-input.md` § Termal Hassasiyet (1 bölüm başlığı)

- **K16.2.5.11 — Termal Hassasiyet**
  - Sorumluluk: ΔVbe/ΔT: -2.2mV/°C (her transistör için) Eşleştirme: Q1 ve Q2 sıcaklıkta eşit olmalı (termal layout)
  - Kanıt: `diff-pair-input.md` § Termal Hassasiyet

#### K16.2.6 — Layout Kuralları

- **Sorumluluk:** Q1 ve Q2 arasında minimum 2mm mesafe (termal eşitlik) Symetrik traces, eşit uzunluk Tail resistor'a yakın GND plane bağlantısı
- **Kanıt:** `diff-pair-input.md` § Layout Kuralları (1 bölüm başlığı)

- **K16.2.6.12 — Layout Kuralları**
  - Sorumluluk: Q1 ve Q2 arasında minimum 2mm mesafe (termal eşitlik) Symetrik traces, eşit uzunluk Tail resistor'a yakın GND plane bağlantısı
  - Kanıt: `diff-pair-input.md` § Layout Kuralları

#### K16.2.7 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — diff-pair-input.md dosyasında belgelenen bölüm.
- **Kanıt:** `diff-pair-input.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.2.7.13 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — diff-pair-input.md dosyasında belgelenen bölüm.
  - Kanıt: `diff-pair-input.md` § Durum: Implementasyon

### K16.3 — Akım Aynası & Aktif Yük

**Sorumluluk:** Widlar/current mirror aktif yük, çıkış empedansı, transistör eşleşme ve layout kuralları.
**Kanıt dosyaları:** `current-mirror.md` — havuz 12 başlık, kullanıldı 12.

#### K16.3.1 — Genel Bakış / Devre Şeması

- **Sorumluluk:** Akım aynası (current mirror), diferansiyel giriş katının aktif yükü olarak görev yapar. Tek bir referans akımını çoğaltarak her iki kolun akımını eşitler.…
- **Kanıt:** `current-mirror.md` § Genel Bakış / Devre Şeması (2 bölüm başlığı)

- **K16.3.1.1 — Genel Bakış**
  - Sorumluluk: Akım aynası (current mirror), diferansiyel giriş katının aktif yükü olarak görev yapar. Tek bir referans akımını çoğaltarak her iki kolun akımını eşitler. Widlar kaynağı, düşük akım referansı için…
  - Kanıt: `current-mirror.md` § Genel Bakış
- **K16.3.1.2 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Devre Şeması

#### K16.3.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — current-mirror.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-mirror.md` § Teknik Spesifikasyonlar (1 bölüm başlığı)

- **K16.3.2.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Teknik Spesifikasyonlar

#### K16.3.3 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — current-mirror.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-mirror.md` § Hesaplamalar (5 bölüm başlığı)

- **K16.3.3.4 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Hesaplamalar
- **K16.3.3.5 — Referans Akımı**
  - Sorumluluk: «Referans Akımı» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Hesaplamalar > Referans Akımı
- **K16.3.3.6 — Widlar Çıkış Akımı**
  - Sorumluluk: «Widlar Çıkış Akımı» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Hesaplamalar > Widlar Çıkış Akımı
- **K16.3.3.7 — Ayna Hata Analizi**
  - Sorumluluk: «Ayna Hata Analizi» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Hesaplamalar > Ayna Hata Analizi
- **K16.3.3.8 — Çıkış Empedansı (Aktif Yük)**
  - Sorumluluk: «Çıkış Empedansı (Aktif Yük)» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Hesaplamalar > Çıkış Empedansı (Aktif Yük)

#### K16.3.4 — Widlar Kaynağı Detayı

- **Sorumluluk:** Widlar aynası, standart aynaya göre avantajları: Yüksek çıkış empedansı (500kΩ+) Düşük akım (@100μA) doğrudan üretir
- **Kanıt:** `current-mirror.md` § Widlar Kaynağı Detayı (1 bölüm başlığı)

- **K16.3.4.9 — Widlar Kaynağı Detayı**
  - Sorumluluk: Widlar aynası, standart aynaya göre avantajları: Yüksek çıkış empedansı (500kΩ+) Düşük akım (@100μA) doğrudan üretir
  - Kanıt: `current-mirror.md` § Widlar Kaynağı Detayı

#### K16.3.5 — Transistör Eşleştirme

- **Sorumluluk:** «Transistör Eşleştirme» — current-mirror.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-mirror.md` § Transistör Eşleştirme (1 bölüm başlığı)

- **K16.3.5.10 — Transistör Eşleştirme**
  - Sorumluluk: «Transistör Eşleştirme» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Transistör Eşleştirme

#### K16.3.6 — Layout Kuralları

- **Sorumluluk:** Q3 ve Q4 termal olarak yakın (1mm max) R_ref ve R_W düşük tolerance metal film Akım yolları simetrik
- **Kanıt:** `current-mirror.md` § Layout Kuralları (1 bölüm başlığı)

- **K16.3.6.11 — Layout Kuralları**
  - Sorumluluk: Q3 ve Q4 termal olarak yakın (1mm max) R_ref ve R_W düşük tolerance metal film Akım yolları simetrik
  - Kanıt: `current-mirror.md` § Layout Kuralları

#### K16.3.7 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — current-mirror.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-mirror.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.3.7.12 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — current-mirror.md dosyasında belgelenen bölüm.
  - Kanıt: `current-mirror.md` § Durum: Implementasyon

### K16.4 — VAS & Frekans Kompanzasyonu

**Sorumluluk:** Gerilim kazancı katı: Miller pole-splitting, faz marjı, unity-gain frequency ve slew rate.
**Kanıt dosyaları:** `vas-stage.md`, `frequency-compensation.md` — havuz 25 başlık, kullanıldı 25.

#### K16.4.1 — Genel Bakış / Devre Şeması / Teknik Spesifikasyonlar

- **Sorumluluk:** VAS katı (Voltage Amplification Stage), diferansiyel giriş katından gelen düşük genlikli sinyali çıkış katı için yeterli gerilim genliğine yükseltir. Miller…
- **Kanıt:** `vas-stage.md`, `frequency-compensation.md` § Genel Bakış / Devre Şeması / Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K16.4.1.1 — Genel Bakış**
  - Sorumluluk: VAS katı (Voltage Amplification Stage), diferansiyel giriş katından gelen düşük genlikli sinyali çıkış katı için yeterli gerilim genliğine yükseltir. Miller kompanzasyonu ile birlikte, amplifikatörün…
  - Kanıt: `vas-stage.md` § Genel Bakış
- **K16.4.1.2 — Genel Bakış**
  - Sorumluluk: Frekans kompanzasyonu, negatif geri beslemeli amplifikatörün kararlılığını sağlamak için Miller kompanzasyonu ve pole-splitting tekniğini kullanır. Dominant kutup tanımlayarak, geri besleme…
  - Kanıt: `frequency-compensation.md` § Genel Bakış
- **K16.4.1.3 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Devre Şeması
- **K16.4.1.4 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Teknik Spesifikasyonlar

#### K16.4.2 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — vas-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `vas-stage.md`, `frequency-compensation.md` § Hesaplamalar (10 bölüm başlığı)

- **K16.4.2.5 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Hesaplamalar
- **K16.4.2.6 — Kazanç Hesabı**
  - Sorumluluk: «Kazanç Hesabı» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Hesaplamalar > Kazanç Hesabı
- **K16.4.2.7 — Miller Kompanzasyon**
  - Sorumluluk: «Miller Kompanzasyon» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Hesaplamalar > Miller Kompanzasyon
- **K16.4.2.8 — Slew Rate**
  - Sorumluluk: «Slew Rate» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Hesaplamalar > Slew Rate
- **K16.4.2.9 — DC Kazanç (Open-Loop)**
  - Sorumluluk: «DC Kazanç (Open-Loop)» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Hesaplamalar > DC Kazanç (Open-Loop)
- **K16.4.2.10 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Hesaplamalar
- **K16.4.2.11 — Dominant Kutup Frekansı**
  - Sorumluluk: «Dominant Kutup Frekansı» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Hesaplamalar > Dominant Kutup Frekansı
- **K16.4.2.12 — Unity Gain Frequency**
  - Sorumluluk: «Unity Gain Frequency» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Hesaplamalar > Unity Gain Frequency
- **K16.4.2.13 — Faz Marjini**
  - Sorumluluk: «Faz Marjini» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Hesaplamalar > Faz Marjini
- **K16.4.2.14 — Lead Compensation (Faz İyileştirme)**
  - Sorumluluk: «Lead Compensation (Faz İyileştirme)» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Hesaplamalar > Lead Compensation (Faz İyileştirme)

#### K16.4.3 — Pole-Splitting Analizi / Termal Sınırlar / Bileşen Seçimi

- **Sorumluluk:** «Pole-Splitting Analizi» — vas-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `vas-stage.md` § Pole-Splitting Analizi / Termal Sınırlar / Bileşen Seçimi (3 bölüm başlığı)

- **K16.4.3.15 — Pole-Splitting Analizi**
  - Sorumluluk: «Pole-Splitting Analizi» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Pole-Splitting Analizi
- **K16.4.3.16 — Termal Sınırlar**
  - Sorumluluk: Q7 Maksimum Güç: P = (Vcc+Vee) × Ic = 70V × 1mA = 70mW TO-92 Isı Dağılımı: θ_ja = 200°C/W → ΔT = 0.014°C
  - Kanıt: `vas-stage.md` § Termal Sınırlar
- **K16.4.3.17 — Bileşen Seçimi**
  - Sorumluluk: ##/Layout Notları
  - Kanıt: `vas-stage.md` § Bileşen Seçimi

#### K16.4.4 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — vas-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `vas-stage.md`, `frequency-compensation.md` § Durum: Implementasyon (2 bölüm başlığı)

- **K16.4.4.18 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — vas-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `vas-stage.md` § Durum: Implementasyon
- **K16.4.4.19 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Durum: Implementasyon

#### K16.4.5 — Miller Kompanzasyon Topolojisi / Pole-Splitting Mekanizması

- **Sorumluluk:** «Miller Kompanzasyon Topolojisi» — frequency-compensation.md dosyasında belgelenen bölüm.
- **Kanıt:** `frequency-compensation.md` § Miller Kompanzasyon Topolojisi / Pole-Splitting Mekanizması (2 bölüm başlığı)

- **K16.4.5.20 — Miller Kompanzasyon Topolojisi**
  - Sorumluluk: «Miller Kompanzasyon Topolojisi» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Miller Kompanzasyon Topolojisi
- **K16.4.5.21 — Pole-Splitting Mekanizması**
  - Sorumluluk: «Pole-Splitting Mekanizması» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Pole-Splitting Mekanizması

#### K16.4.6 — AC Tepki Analizi / Kararlılık Kriterleri

- **Sorumluluk:** «AC Tepki Analizi» — frequency-compensation.md dosyasında belgelenen bölüm.
- **Kanıt:** `frequency-compensation.md` § AC Tepki Analizi / Kararlılık Kriterleri (2 bölüm başlığı)

- **K16.4.6.22 — AC Tepki Analizi**
  - Sorumluluk: «AC Tepki Analizi» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § AC Tepki Analizi
- **K16.4.6.23 — Kararlılık Kriterleri**
  - Sorumluluk: «Kararlılık Kriterleri» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Kararlılık Kriterleri

#### K16.4.7 — Slew Rate Optimizasyonu / Bileşen Değerleri Özeti

- **Sorumluluk:** «Slew Rate Optimizasyonu» — frequency-compensation.md dosyasında belgelenen bölüm.
- **Kanıt:** `frequency-compensation.md` § Slew Rate Optimizasyonu / Bileşen Değerleri Özeti (2 bölüm başlığı)

- **K16.4.7.24 — Slew Rate Optimizasyonu**
  - Sorumluluk: «Slew Rate Optimizasyonu» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Slew Rate Optimizasyonu
- **K16.4.7.25 — Bileşen Değerleri Özeti**
  - Sorumluluk: «Bileşen Değerleri Özeti» — frequency-compensation.md dosyasında belgelenen bölüm.
  - Kanıt: `frequency-compensation.md` § Bileşen Değerleri Özeti

### K16.5 — Bias: Vbe Multiplier

**Sorumluluk:** Çıkış transistörleri Class AB bias noktası, idle akımı ayarı ve termal izleme/koruma.
**Kanıt dosyaları:** `vbe-multiplier.md` — havuz 13 başlık, kullanıldı 13.

#### K16.5.1 — Genel Bakış / Devre Şeması

- **Sorumluluk:** Vbe çoğaltıcı (Vbe multiplier), çıkış transistörleri için Class AB bias noktasını belirler. Çıkış stage'indeki crossover distorsiyonunu önlemek için gerekli DC…
- **Kanıt:** `vbe-multiplier.md` § Genel Bakış / Devre Şeması (2 bölüm başlığı)

- **K16.5.1.1 — Genel Bakış**
  - Sorumluluk: Vbe çoğaltıcı (Vbe multiplier), çıkış transistörleri için Class AB bias noktasını belirler. Çıkış stage'indeki crossover distorsiyonunu önlemek için gerekli DC offset voltajını üretir. Termal izleme…
  - Kanıt: `vbe-multiplier.md` § Genel Bakış
- **K16.5.1.2 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Devre Şeması

#### K16.5.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — vbe-multiplier.md dosyasında belgelenen bölüm.
- **Kanıt:** `vbe-multiplier.md` § Teknik Spesifikasyonlar (1 bölüm başlığı)

- **K16.5.2.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Teknik Spesifikasyonlar

#### K16.5.3 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — vbe-multiplier.md dosyasında belgelenen bölüm.
- **Kanıt:** `vbe-multiplier.md` § Hesaplamalar (5 bölüm başlığı)

- **K16.5.3.4 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Hesaplamalar
- **K16.5.3.5 — Vbe Multiplier Kazancı**
  - Sorumluluk: «Vbe Multiplier Kazancı» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Hesaplamalar > Vbe Multiplier Kazancı
- **K16.5.3.6 — Idle Akımı Ayarı**
  - Sorumluluk: «Idle Akımı Ayarı» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Hesaplamalar > Idle Akımı Ayarı
- **K16.5.3.7 — Termal İzleme**
  - Sorumluluk: «Termal İzleme» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Hesaplamalar > Termal İzleme
- **K16.5.3.8 — DC Çalışma Noktası**
  - Sorumluluk: «DC Çalışma Noktası» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Hesaplamalar > DC Çalışma Noktası

#### K16.5.4 — Bias Ayar Prosedürü / Termal Koruma Mekanizması

- **Sorumluluk:** Ön koşul: Amplifikatör ısısız (oda sıcaklığı) Giriş: 0V DC, hiçbir sinyal yok Trimpot: Rb'yi saat yönünde döndürerek I_idle'yi artır
- **Kanıt:** `vbe-multiplier.md` § Bias Ayar Prosedürü / Termal Koruma Mekanizması (2 bölüm başlığı)

- **K16.5.4.9 — Bias Ayar Prosedürü**
  - Sorumluluk: Ön koşul: Amplifikatör ısısız (oda sıcaklığı) Giriş: 0V DC, hiçbir sinyal yok Trimpot: Rb'yi saat yönünde döndürerek I_idle'yi artır
  - Kanıt: `vbe-multiplier.md` § Bias Ayar Prosedürü
- **K16.5.4.10 — Termal Koruma Mekanizması**
  - Sorumluluk: «Termal Koruma Mekanizması» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Termal Koruma Mekanizması

#### K16.5.5 — Bileşen Seçimi

- **Sorumluluk:** «Bileşen Seçimi» — vbe-multiplier.md dosyasında belgelenen bölüm.
- **Kanıt:** `vbe-multiplier.md` § Bileşen Seçimi (1 bölüm başlığı)

- **K16.5.5.11 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Bileşen Seçimi

#### K16.5.6 — Layout Kuralları

- **Sorumluluk:** Q8 output transistörleri ile termal temas (thermal compound) Trimpot kolay erişilebilir konumda
- **Kanıt:** `vbe-multiplier.md` § Layout Kuralları (1 bölüm başlığı)

- **K16.5.6.12 — Layout Kuralları**
  - Sorumluluk: Q8 output transistörleri ile termal temas (thermal compound) Trimpot kolay erişilebilir konumda
  - Kanıt: `vbe-multiplier.md` § Layout Kuralları

#### K16.5.7 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — vbe-multiplier.md dosyasında belgelenen bölüm.
- **Kanıt:** `vbe-multiplier.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.5.7.13 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — vbe-multiplier.md dosyasında belgelenen bölüm.
  - Kanıt: `vbe-multiplier.md` § Durum: Implementasyon

### K16.6 — Sürücü: Darlington Çift

**Sorumluluk:** Darlington akım kazancı, empedans dönüşümü, slew rate etkisi ve Baker clamp ek pedal.
**Kanıt dosyaları:** `darlington-pair.md` — havuz 12 başlık, kullanıldı 12.

#### K16.6.1 — Genel Bakış / Devre Şeması

- **Sorumluluk:** Darlington yapılandırması, iki transistörün seri bağlı collector-emitter konfigürasyonuyla yüksek akım kazancı elde eder. COREMUSIC K16'da çıkış…
- **Kanıt:** `darlington-pair.md` § Genel Bakış / Devre Şeması (2 bölüm başlığı)

- **K16.6.1.1 — Genel Bakış**
  - Sorumluluk: Darlington yapılandırması, iki transistörün seri bağlı collector-emitter konfigürasyonuyla yüksek akım kazancı elde eder. COREMUSIC K16'da çıkış transistörlerinin sürücü katı olarak kullanılır.…
  - Kanıt: `darlington-pair.md` § Genel Bakış
- **K16.6.1.2 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Devre Şeması

#### K16.6.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — darlington-pair.md dosyasında belgelenen bölüm.
- **Kanıt:** `darlington-pair.md` § Teknik Spesifikasyonlar (1 bölüm başlığı)

- **K16.6.2.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Teknik Spesifikasyonlar

#### K16.6.3 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — darlington-pair.md dosyasında belgelenen bölüm.
- **Kanıt:** `darlington-pair.md` § Hesaplamalar (5 bölüm başlığı)

- **K16.6.3.4 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Hesaplamalar
- **K16.6.3.5 — Toplam Akım Kazancı**
  - Sorumluluk: «Toplam Akım Kazancı» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Hesaplamalar > Toplam Akım Kazancı
- **K16.6.3.6 — Empedans Dönüşümü**
  - Sorumluluk: «Empedans Dönüşümü» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Hesaplamalar > Empedans Dönüşümü
- **K16.6.3.7 — Slew Rate Etkisi**
  - Sorumluluk: «Slew Rate Etkisi» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Hesaplamalar > Slew Rate Etkisi
- **K16.6.3.8 — Güç Kaybı**
  - Sorumluluk: «Güç Kaybı» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Hesaplamalar > Güç Kaybı

#### K16.6.4 — Alternatif Driver Transistörleri

- **Sorumluluk:** «Alternatif Driver Transistörleri» — darlington-pair.md dosyasında belgelenen bölüm.
- **Kanıt:** `darlington-pair.md` § Alternatif Driver Transistörleri (1 bölüm başlığı)

- **K16.6.4.9 — Alternatif Driver Transistörleri**
  - Sorumluluk: «Alternatif Driver Transistörleri» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Alternatif Driver Transistörleri

#### K16.6.5 — Ek Pedal Transistörleri (Baker Clamp)

- **Sorumluluk:** «Ek Pedal Transistörleri (Baker Clamp)» — darlington-pair.md dosyasında belgelenen bölüm.
- **Kanıt:** `darlington-pair.md` § Ek Pedal Transistörleri (Baker Clamp) (1 bölüm başlığı)

- **K16.6.5.10 — Ek Pedal Transistörleri (Baker Clamp)**
  - Sorumluluk: «Ek Pedal Transistörleri (Baker Clamp)» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Ek Pedal Transistörleri (Baker Clamp)

#### K16.6.6 — Layout Kuralları

- **Sorumluluk:** Q11a ve Q9 arasında kısa collector trace (minimum inductance) Q11a base-emitter parasitic kapasitansını minimize
- **Kanıt:** `darlington-pair.md` § Layout Kuralları (1 bölüm başlığı)

- **K16.6.6.11 — Layout Kuralları**
  - Sorumluluk: Q11a ve Q9 arasında kısa collector trace (minimum inductance) Q11a base-emitter parasitic kapasitansını minimize
  - Kanıt: `darlington-pair.md` § Layout Kuralları

#### K16.6.7 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — darlington-pair.md dosyasında belgelenen bölüm.
- **Kanıt:** `darlington-pair.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.6.7.12 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — darlington-pair.md dosyasında belgelenen bölüm.
  - Kanıt: `darlington-pair.md` § Durum: Implementasyon

### K16.7 — Çıkış Stage (Push-Pull)

**Sorumluluk:** Push-pull çıkış: maksimum gerilim, akım sınırı, damping faktörü ve verimlilik.
**Kanıt dosyaları:** `output-stage.md` — havuz 11 başlık, kullanıldı 11.

#### K16.7.1 — Genel Bakış

- **Sorumluluk:** Push-pull çıkış katı, tamamlayıcı (complementary) NPN/PNP transistör çifti ile Class AB modunda çalışır. MJL21194 (NPN) ve MJL21193 (PNP) güç transistörleri,…
- **Kanıt:** `output-stage.md` § Genel Bakış (1 bölüm başlığı)

- **K16.7.1.1 — Genel Bakış**
  - Sorumluluk: Push-pull çıkış katı, tamamlayıcı (complementary) NPN/PNP transistör çifti ile Class AB modunda çalışır. MJL21194 (NPN) ve MJL21193 (PNP) güç transistörleri, düşük empedanslı (>200 damping factor) ve…
  - Kanıt: `output-stage.md` § Genel Bakış

#### K16.7.2 — Devre Şeması

- **Sorumluluk:** «Devre Şeması» — output-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `output-stage.md` § Devre Şeması (1 bölüm başlığı)

- **K16.7.2.2 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Devre Şeması

#### K16.7.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — output-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `output-stage.md` § Teknik Spesifikasyonlar (1 bölüm başlığı)

- **K16.7.3.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Teknik Spesifikasyonlar

#### K16.7.4 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — output-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `output-stage.md` § Hesaplamalar (5 bölüm başlığı)

- **K16.7.4.4 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Hesaplamalar
- **K16.7.4.5 — Maksimum Çıkış Voltajı**
  - Sorumluluk: «Maksimum Çıkış Voltajı» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Hesaplamalar > Maksimum Çıkış Voltajı
- **K16.7.4.6 — Akım Sınırı**
  - Sorumluluk: «Akım Sınırı» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Hesaplamalar > Akım Sınırı
- **K16.7.4.7 — Empedans Dönüşümü**
  - Sorumluluk: «Empedans Dönüşümü» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Hesaplamalar > Empedans Dönüşümü
- **K16.7.4.8 — Damping Faktörü**
  - Sorumluluk: «Damping Faktörü» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Hesaplamalar > Damping Faktörü

#### K16.7.5 — Class AB Çalışma Modu

- **Sorumluluk:** «Class AB Çalışma Modu» — output-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `output-stage.md` § Class AB Çalışma Modu (1 bölüm başlığı)

- **K16.7.5.9 — Class AB Çalışma Modu**
  - Sorumluluk: «Class AB Çalışma Modu» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Class AB Çalışma Modu

#### K16.7.6 — Güç Kaybı ve Verimlilik

- **Sorumluluk:** «Güç Kaybı ve Verimlilik» — output-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `output-stage.md` § Güç Kaybı ve Verimlilik (1 bölüm başlığı)

- **K16.7.6.10 — Güç Kaybı ve Verimlilik**
  - Sorumluluk: «Güç Kaybı ve Verimlilik» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Güç Kaybı ve Verimlilik

#### K16.7.7 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — output-stage.md dosyasında belgelenen bölüm.
- **Kanıt:** `output-stage.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.7.7.11 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — output-stage.md dosyasında belgelenen bölüm.
  - Kanıt: `output-stage.md` § Durum: Implementasyon

### K16.8 — Geri Besleme Ağı

**Sorumluluk:** Kapalı döngü kazanç, loop gain (Aβ), THD azaltımı, faz marjı ve frekans tepkisi analizi.
**Kanıt dosyaları:** `feedback-network.md` — havuz 14 başlık, kullanıldı 14.

#### K16.8.1 — Genel Bakış / Devre Şeması

- **Sorumluluk:** Negatif geri besleme ağı, amplifikatörün kazancını, distorsiyonunu, bant genişliğini ve empedans özelliklerini kontrol eder. Kapalı döngü kazancı, geri besleme…
- **Kanıt:** `feedback-network.md` § Genel Bakış / Devre Şeması (2 bölüm başlığı)

- **K16.8.1.1 — Genel Bakış**
  - Sorumluluk: Negatif geri besleme ağı, amplifikatörün kazancını, distorsiyonunu, bant genişliğini ve empedans özelliklerini kontrol eder. Kapalı döngü kazancı, geri besleme oranı ile belirlenir. Global negatif…
  - Kanıt: `feedback-network.md` § Genel Bakış
- **K16.8.1.2 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Devre Şeması

#### K16.8.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — feedback-network.md dosyasında belgelenen bölüm.
- **Kanıt:** `feedback-network.md` § Teknik Spesifikasyonlar (1 bölüm başlığı)

- **K16.8.2.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Teknik Spesifikasyonlar

#### K16.8.3 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — feedback-network.md dosyasında belgelenen bölüm.
- **Kanıt:** `feedback-network.md` § Hesaplamalar (6 bölüm başlığı)

- **K16.8.3.4 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Hesaplamalar
- **K16.8.3.5 — Kapalı Döngü Kazançı**
  - Sorumluluk: «Kapalı Döngü Kazançı» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Hesaplamalar > Kapalı Döngü Kazançı
- **K16.8.3.6 — Kazanç Hatası (Gain Error)**
  - Sorumluluk: «Kazanç Hatası (Gain Error)» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Hesaplamalar > Kazanç Hatası (Gain Error)
- **K16.8.3.7 — Loop Gain (Aβ)**
  - Sorumluluk: «Loop Gain (Aβ)» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Hesaplamalar > Loop Gain (Aβ)
- **K16.8.3.8 — Giriş Empedansı (Kapalı Döngü)**
  - Sorumluluk: «Giriş Empedansı (Kapalı Döngü)» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Hesaplamalar > Giriş Empedansı (Kapalı Döngü)
- **K16.8.3.9 — Çıkış Empedansı (Kapalı Döngü)**
  - Sorumluluk: «Çıkış Empedansı (Kapalı Döngü)» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Hesaplamalar > Çıkış Empedansı (Kapalı Döngü)

#### K16.8.4 — THD Azaltma Analizi / Frekans Tepkisi

- **Sorumluluk:** «THD Azaltma Analizi» — feedback-network.md dosyasında belgelenen bölüm.
- **Kanıt:** `feedback-network.md` § THD Azaltma Analizi / Frekans Tepkisi (2 bölüm başlığı)

- **K16.8.4.10 — THD Azaltma Analizi**
  - Sorumluluk: «THD Azaltma Analizi» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § THD Azaltma Analizi
- **K16.8.4.11 — Frekans Tepkisi**
  - Sorumluluk: «Frekans Tepkisi» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Frekans Tepkisi

#### K16.8.5 — Faz Marjini Analizi

- **Sorumluluk:** «Faz Marjini Analizi» — feedback-network.md dosyasında belgelenen bölüm.
- **Kanıt:** `feedback-network.md` § Faz Marjini Analizi (1 bölüm başlığı)

- **K16.8.5.12 — Faz Marjini Analizi**
  - Sorumluluk: «Faz Marjini Analizi» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Faz Marjini Analizi

#### K16.8.6 — Bileşen Seçimi

- **Sorumluluk:** «Bileşen Seçimi» — feedback-network.md dosyasında belgelenen bölüm.
- **Kanıt:** `feedback-network.md` § Bileşen Seçimi (1 bölüm başlığı)

- **K16.8.6.13 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Bileşen Seçimi

#### K16.8.7 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — feedback-network.md dosyasında belgelenen bölüm.
- **Kanıt:** `feedback-network.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K16.8.7.14 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — feedback-network.md dosyasında belgelenen bölüm.
  - Kanıt: `feedback-network.md` § Durum: Implementasyon

### K16.9 — Güç Transistörleri & Eşleştirme

**Sorumluluk:** MJL21194/MJL21193 elektriksel sınırlar, SOA, termal derating ve NPN-PNP tamamlayıcı eşleştirme.
**Kanıt dosyaları:** `mjle21194.md`, `mjle21193.md` — havuz 22 başlık, kullanıldı 22.

#### K16.9.1 — Genel Bakış

- **Sorumluluk:** MJL21194, OnSemi tarafından üretilen yüksek güçlü NPN bipolar güç transistörüdür. 250Wissement güç, 250V Vceo ve 16A ICmax ile Class AB audio amplifikatörleri…
- **Kanıt:** `mjle21194.md`, `mjle21193.md` § Genel Bakış (2 bölüm başlığı)

- **K16.9.1.1 — Genel Bakış**
  - Sorumluluk: MJL21194, OnSemi tarafından üretilen yüksek güçlü NPN bipolar güç transistörüdür. 250Wissement güç, 250V Vceo ve 16A ICmax ile Class AB audio amplifikatörleri için idealdir. MJL21193 (PNP) ile…
  - Kanıt: `mjle21194.md` § Genel Bakış
- **K16.9.1.2 — Genel Bakış**
  - Sorumluluk: MJL21193, OnSami tarafından üretilen yüksek güçlü PNP bipolar güç transistörüdür. MJL21194 (NPN) ile tamamlayıcı çift oluşturarak Class AB push-pull çıkış katında negatif half-cycle'ı sürer.…
  - Kanıt: `mjle21193.md` § Genel Bakış

#### K16.9.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — mjle21194.md dosyasında belgelenen bölüm.
- **Kanıt:** `mjle21194.md`, `mjle21193.md` § Teknik Spesifikasyonlar (7 bölüm başlığı)

- **K16.9.2.3 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Teknik Spesifikasyonlar
- **K16.9.2.4 — Elektriksel Özellikler**
  - Sorumluluk: «Elektriksel Özellikler» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Teknik Spesifikasyonlar > Elektriksel Özellikler
- **K16.9.2.5 — DC Kazanç Özellikleri**
  - Sorumluluk: «DC Kazanç Özellikleri» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Teknik Spesifikasyonlar > DC Kazanç Özellikleri
- **K16.9.2.6 — Anahtarlama Özellikleri**
  - Sorumluluk: «Anahtarlama Özellikleri» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Teknik Spesifikasyonlar > Anahtarlama Özellikleri
- **K16.9.2.7 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Teknik Spesifikasyonlar
- **K16.9.2.8 — Elektriksel Özellikler**
  - Sorumluluk: «Elektriksel Özellikler» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Teknik Spesifikasyonlar > Elektriksel Özellikler
- **K16.9.2.9 — DC Kazanç Özellikleri**
  - Sorumluluk: «DC Kazanç Özellikleri» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Teknik Spesifikasyonlar > DC Kazanç Özellikleri

#### K16.9.3 — output characteristics (I_C vs V_CE) / Thermal Derating (+3 bölüm)

- **Sorumluluk:** «output characteristics (I_C vs V_CE)» — mjle21194.md dosyasında belgelenen bölüm.
- **Kanıt:** `mjle21194.md` § output characteristics (I_C vs V_CE) / Thermal Derating (+3 bölüm) (4 bölüm başlığı)

- **K16.9.3.10 — output characteristics (I_C vs V_CE)**
  - Sorumluluk: «output characteristics (I_C vs V_CE)» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § output characteristics (I_C vs V_CE)
- **K16.9.3.11 — Thermal Derating**
  - Sorumluluk: «Thermal Derating» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Thermal Derating
- **K16.9.3.12 — SOA (Safe Operating Area)**
  - Sorumluluk: «SOA (Safe Operating Area)» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § SOA (Safe Operating Area)
- **K16.9.3.13 — Montaj ve Isı**
  - Sorumluluk: «Montaj ve Isı» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Montaj ve Isı

#### K16.9.4 — Eşleştirme (Matching) / Güvenli Çalışma Sınırları

- **Sorumluluk:** P/N tamamlayıcı çift eşleştirme: hFE eşleştirme: ΔhFE/hFE ≤ %5 @ I_C=4A V_BE eşleştirme: ΔV_BE ≤ 10mV @ I_C=1A
- **Kanıt:** `mjle21194.md` § Eşleştirme (Matching) / Güvenli Çalışma Sınırları (2 bölüm başlığı)

- **K16.9.4.14 — Eşleştirme (Matching)**
  - Sorumluluk: P/N tamamlayıcı çift eşleştirme: hFE eşleştirme: ΔhFE/hFE ≤ %5 @ I_C=4A V_BE eşleştirme: ΔV_BE ≤ 10mV @ I_C=1A
  - Kanıt: `mjle21194.md` § Eşleştirme (Matching)
- **K16.9.4.15 — Güvenli Çalışma Sınırları**
  - Sorumluluk: Vceo: Asla 250V'u aşmamalı (transient dahil) Ic: DC 16A, pulse 30A (10ms) Tj: Maksimum 150°C (derating starts @ 25°C)
  - Kanıt: `mjle21194.md` § Güvenli Çalışma Sınırları

#### K16.9.5 — Durum: Implementasyon

- **Sorumluluk:** «Durum: Implementasyon» — mjle21194.md dosyasında belgelenen bölüm.
- **Kanıt:** `mjle21194.md`, `mjle21193.md` § Durum: Implementasyon (2 bölüm başlığı)

- **K16.9.5.16 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — mjle21194.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21194.md` § Durum: Implementasyon
- **K16.9.5.17 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Durum: Implementasyon

#### K16.9.6 — MJL21193 vs MJL21194 Karşılaştırma / Tamamlayıcı Çift Eşleştirme Kriterleri

- **Sorumluluk:** «MJL21193 vs MJL21194 Karşılaştırma» — mjle21193.md dosyasında belgelenen bölüm.
- **Kanıt:** `mjle21193.md` § MJL21193 vs MJL21194 Karşılaştırma / Tamamlayıcı Çift Eşleştirme Kriterleri (2 bölüm başlığı)

- **K16.9.6.18 — MJL21193 vs MJL21194 Karşılaştırma**
  - Sorumluluk: «MJL21193 vs MJL21194 Karşılaştırma» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § MJL21193 vs MJL21194 Karşılaştırma
- **K16.9.6.19 — Tamamlayıcı Çift Eşleştirme Kriterleri**
  - Sorumluluk: «Tamamlayıcı Çift Eşleştirme Kriterleri» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Tamamlayıcı Çift Eşleştirme Kriterleri

#### K16.9.7 — Push-Pull Simetri Analizi / Termal Eşleme / Güvenlik Sınırları

- **Sorumluluk:** «Push-Pull Simetri Analizi» — mjle21193.md dosyasında belgelenen bölüm.
- **Kanıt:** `mjle21193.md` § Push-Pull Simetri Analizi / Termal Eşleme / Güvenlik Sınırları (3 bölüm başlığı)

- **K16.9.7.20 — Push-Pull Simetri Analizi**
  - Sorumluluk: «Push-Pull Simetri Analizi» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Push-Pull Simetri Analizi
- **K16.9.7.21 — Termal Eşleme**
  - Sorumluluk: «Termal Eşleme» — mjle21193.md dosyasında belgelenen bölüm.
  - Kanıt: `mjle21193.md` § Termal Eşleme
- **K16.9.7.22 — Güvenlik Sınırları**
  - Sorumluluk: Polarity: PNP ters polarma dc voltajına duyarlı V_EBO: -7V'dan fazla reverse bias → junction hasarı
  - Kanıt: `mjle21193.md` § Güvenlik Sınırları

### K16.10 — Koruma Devreleri

**Sorumluluk:** DC offset, overcurrent/foldback, kısa devre ve termal koruma senaryoları ile röle/limit devreleri.
**Kanıt dosyaları:** `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` — havuz 57 başlık, kullanıldı 57.

#### K16.10.1 — Genel Bakış / Devre Şeması

- **Sorumluluk:** DC offset koruma devresi, çıkışta DC voltajı algıladığında hoparlörleri korumak için röleyi devreden çıkarır. ±0.5V DC eşik değeri aşıldığında 100ms içinde…
- **Kanıt:** `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` § Genel Bakış / Devre Şeması (8 bölüm başlığı)

- **K16.10.1.1 — Genel Bakış**
  - Sorumluluk: DC offset koruma devresi, çıkışta DC voltajı algıladığında hoparlörleri korumak için röleyi devreden çıkarır. ±0.5V DC eşik değeri aşıldığında 100ms içinde bağlantı kesilir. Hoparlör voice coil'unu…
  - Kanıt: `protection-dc-offset.md` § Genel Bakış
- **K16.10.1.2 — Genel Bakış**
  - Sorumluluk: Aşırı akım koruması, çıkış transistörlerini ve hoparlörleri kısa devre veya aşırı yükten korur. Current sensing dirençleri üzerinden çıkış akımını izler, eşik değeri aşıldığında akımı sınırlar…
  - Kanıt: `protection-overcurrent.md` § Genel Bakış
- **K16.10.1.3 — Genel Bakış**
  - Sorumluluk: Kısa devre koruması, çıkış doğrudan toprağa veya besleme gerilimine kısa devre edildiğinde output transistörlerini ve besleme kaynaklarını korur. SOA (Safe Operating Area) sınırlaması…
  - Kanıt: `protection-short-circuit.md` § Genel Bakış
- **K16.10.1.4 — Genel Bakış**
  - Sorumluluk: Termal kapatma koruması, heatsink sıcaklığı belirli bir eşiği aştığında amplifikatörü devreden çıkarır. KSD301 termostat sensörü, 85°C'de tetiklenerek röleyi de-enerjize eder. Isı dağılımı design'a…
  - Kanıt: `protection-thermal.md` § Genel Bakış
- **K16.10.1.5 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Devre Şeması
- **K16.10.1.6 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Devre Şeması
- **K16.10.1.7 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Devre Şeması
- **K16.10.1.8 — Devre Şeması**
  - Sorumluluk: «Devre Şeması» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Devre Şeması

#### K16.10.2 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — protection-dc-offset.md dosyasında belgelenen bölüm.
- **Kanıt:** `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K16.10.2.9 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Teknik Spesifikasyonlar
- **K16.10.2.10 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Teknik Spesifikasyonlar
- **K16.10.2.11 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Teknik Spesifikasyonlar
- **K16.10.2.12 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Teknik Spesifikasyonlar

#### K16.10.3 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — protection-dc-offset.md dosyasında belgelenen bölüm.
- **Kanıt:** `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` § Hesaplamalar (19 bölüm başlığı)

- **K16.10.3.13 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Hesaplamalar
- **K16.10.3.14 — DC Algılama Eşiği**
  - Sorumluluk: «DC Algılama Eşiği» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Hesaplamalar > DC Algılama Eşiği
- **K16.10.3.15 — Gecikme Süresi (RC Time Constant)**
  - Sorumluluk: «Gecikme Süresi (RC Time Constant)» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Hesaplamalar > Gecikme Süresi (RC Time Constant)
- **K16.10.3.16 — Röle Gücü**
  - Sorumluluk: «Röle Gücü» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Hesaplamalar > Röle Gücü
- **K16.10.3.17 — Flyback Koruması**
  - Sorumluluk: «Flyback Koruması» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Hesaplamalar > Flyback Koruması
- **K16.10.3.18 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Hesaplamalar
- **K16.10.3.19 — Akım Sınırı (Current Limit)**
  - Sorumluluk: «Akım Sınırı (Current Limit)» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Hesaplamalar > Akım Sınırı (Current Limit)
- **K16.10.3.20 — Foldback Mekanizması**
  - Sorumluluk: «Foldback Mekanizması» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Hesaplamalar > Foldback Mekanizması
- **K16.10.3.21 — Güç Sınırı**
  - Sorumluluk: «Güç Sınırı» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Hesaplamalar > Güç Sınırı
- **K16.10.3.22 — Termal Koruma Entegrasyyonu**
  - Sorumluluk: «Termal Koruma Entegrasyyonu» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Hesaplamalar > Termal Koruma Entegrasyyonu
- **K16.10.3.23 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Hesaplamalar
- **K16.10.3.24 — Kısa Devre Akımı**
  - Sorumluluk: «Kısa Devre Akımı» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Hesaplamalar > Kısa Devre Akımı
- **K16.10.3.25 — SOA Sınırlaması (Safe Operating Area)**
  - Sorumluluk: «SOA Sınırlaması (Safe Operating Area)» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Hesaplamalar > SOA Sınırlaması (Safe Operating Area)
- **K16.10.3.26 — Foldback Dinamiği**
  - Sorumluluk: «Foldback Dinamiği» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Hesaplamalar > Foldback Dinamiği
- **K16.10.3.27 — Güvenli Çalışma Süresi**
  - Sorumluluk: «Güvenli Çalışma Süresi» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Hesaplamalar > Güvenli Çalışma Süresi
- **K16.10.3.28 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Hesaplamalar
- **K16.10.3.29 — Termal Senaryo (100W Continous)**
  - Sorumluluk: «Termal Senaryo (100W Continous)» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Hesaplamalar > Termal Senaryo (100W Continous)
- **K16.10.3.30 — Termal Zaman Sabiti**
  - Sorumluluk: «Termal Zaman Sabiti» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Hesaplamalar > Termal Zaman Sabiti
- **K16.10.3.31 — Koruma Aktifleme Senaryosu**
  - Sorumluluk: «Koruma Aktifleme Senaryosu» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Hesaplamalar > Koruma Aktifleme Senaryosu

#### K16.10.4 — Çalışma Modları

- **Sorumluluk:** «Çalışma Modları» — protection-dc-offset.md dosyasında belgelenen bölüm.
- **Kanıt:** `protection-dc-offset.md` § Çalışma Modları (4 bölüm başlığı)

- **K16.10.4.32 — Çalışma Modları**
  - Sorumluluk: «Çalışma Modları» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Çalışma Modları
- **K16.10.4.33 — Normal Çalışma**
  - Sorumluluk: «Normal Çalışma» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Çalışma Modları > Normal Çalışma
- **K16.10.4.34 — DC Algılama**
  - Sorumluluk: «DC Algılama» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Çalışma Modları > DC Algılama
- **K16.10.4.35 — Otomatik Reset**
  - Sorumluluk: «Otomatik Reset» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Çalışma Modları > Otomatik Reset

#### K16.10.5 — Bileşen Seçimi / Layout Kuralları

- **Sorumluluk:** «Bileşen Seçimi» — protection-dc-offset.md dosyasında belgelenen bölüm.
- **Kanıt:** `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` § Bileşen Seçimi / Layout Kuralları (7 bölüm başlığı)

- **K16.10.5.36 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Bileşen Seçimi
- **K16.10.5.37 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Bileşen Seçimi
- **K16.10.5.38 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Bileşen Seçimi
- **K16.10.5.39 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Bileşen Seçimi
- **K16.10.5.40 — Layout Kuralları**
  - Sorumluluk: Röle kontaktları geniş trace (30A için 5mm) Flyback diode röle bobinine yakın DC sensing trace'i çıkış terminaline yakın
  - Kanıt: `protection-dc-offset.md` § Layout Kuralları
- **K16.10.5.41 — Layout Kuralları**
  - Sorumluluk: R_sense kısa ve geniş trace (5A continuous) Q17 heatsink'te mounting (termal koruma) Zener diodes close to output terminal
  - Kanıt: `protection-short-circuit.md` § Layout Kuralları
- **K16.10.5.42 — Layout Kuralları**
  - Sorumluluk: KSD301 heatsink'in sıcak bölgesine mount Thermal compound uygula (Arctic MX-6) Sensor lead tellleri minimum 5cm (thermal isolation)
  - Kanıt: `protection-thermal.md` § Layout Kuralları

#### K16.10.6 — Durum: Implementasyon / Foldback Karakteristiği / Alternatif Topolojiler

- **Sorumluluk:** «Durum: Implementasyon» — protection-dc-offset.md dosyasında belgelenen bölüm.
- **Kanıt:** `protection-dc-offset.md`, `protection-overcurrent.md`, `protection-short-circuit.md`, `protection-thermal.md` § Durum: Implementasyon / Foldback Karakteristiği / Alternatif Topolojiler (9 bölüm başlığı)

- **K16.10.6.43 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — protection-dc-offset.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-dc-offset.md` § Durum: Implementasyon
- **K16.10.6.44 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Durum: Implementasyon
- **K16.10.6.45 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Durum: Implementasyon
- **K16.10.6.46 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Durum: Implementasyon
- **K16.10.6.47 — Foldback Karakteristiği**
  - Sorumluluk: «Foldback Karakteristiği» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Foldback Karakteristiği
- **K16.10.6.48 — Alternatif Topolojiler**
  - Sorumluluk: «Alternatif Topolojiler» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Alternatif Topolojiler
- **K16.10.6.49 — 1. Simple Current Limit**
  - Sorumluluk: «1. Simple Current Limit» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Alternatif Topolojiler > 1. Simple Current Limit
- **K16.10.6.50 — 2. Foldback (Mevcut)**
  - Sorumluluk: «2. Foldback (Mevcut)» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Alternatif Topolojiler > 2. Foldback (Mevcut)
- **K16.10.6.51 — 3. Electronic Circuit Breaker**
  - Sorumluluk: «3. Electronic Circuit Breaker» — protection-overcurrent.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-overcurrent.md` § Alternatif Topolojiler > 3. Electronic Circuit Breaker

#### K16.10.7 — Koruma Senaryoları / Çalışma Diyagramı / Sensör Alternatifleri

- **Sorumluluk:** «Koruma Senaryoları» — protection-short-circuit.md dosyasında belgelenen bölüm.
- **Kanıt:** `protection-short-circuit.md`, `protection-thermal.md` § Koruma Senaryoları / Çalışma Diyagramı / Sensör Alternatifleri (6 bölüm başlığı)

- **K16.10.7.52 — Koruma Senaryoları**
  - Sorumluluk: «Koruma Senaryoları» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Koruma Senaryoları
- **K16.10.7.53 — Senaryo 1: Çıkış → GND**
  - Sorumluluk: «Senaryo 1: Çıkış → GND» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Koruma Senaryoları > Senaryo 1: Çıkış → GND
- **K16.10.7.54 — Senaryo 2: Çıkış → +Vcc**
  - Sorumluluk: «Senaryo 2: Çıkış → +Vcc» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Koruma Senaryoları > Senaryo 2: Çıkış → +Vcc
- **K16.10.7.55 — Senaryo 3: Çıkış → -Vee**
  - Sorumluluk: «Senaryo 3: Çıkış → -Vee» — protection-short-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-short-circuit.md` § Koruma Senaryoları > Senaryo 3: Çıkış → -Vee
- **K16.10.7.56 — Çalışma Diyagramı**
  - Sorumluluk: «Çalışma Diyagramı» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Çalışma Diyagramı
- **K16.10.7.57 — Sensör Alternatifleri**
  - Sorumluluk: «Sensör Alternatifleri» — protection-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `protection-thermal.md` § Sensör Alternatifleri

### K16.11 — Sistem Entegrasyonu

**Sorumluluk:** Besleme kaynağı gereksinimi, termal tasarım, SPICE modelleri ve sınıf-AB BOM'u/maliyet özeti.
**Kanıt dosyaları:** `power-supply-requisite.md`, `thermal-design.md`, `spice-model.md`, `bom-classab.md` — havuz 57 başlık, kullanıldı 57.

#### K16.11.1 — Genel Bakış / Güç Kaynağı Blok Diyagramı / Teknik Spesifikasyonlar

- **Sorumluluk:** ±35V DC besleme sistemi, K16 Class AB amplifikatörün tüm katmanlarına güç sağlar. Toroid trafolar, aktif regüle ve büyük kapasitörlü filtreleme ile düşük…
- **Kanıt:** `power-supply-requisite.md`, `thermal-design.md`, `spice-model.md`, `bom-classab.md` § Genel Bakış / Güç Kaynağı Blok Diyagramı / Teknik Spesifikasyonlar (6 bölüm başlığı)

- **K16.11.1.1 — Genel Bakış**
  - Sorumluluk: ±35V DC besleme sistemi, K16 Class AB amplifikatörün tüm katmanlarına güç sağlar. Toroid trafolar, aktif regüle ve büyük kapasitörlü filtreleme ile düşük ripple ve yüksek akım kapasitesi sağlanır. 8…
  - Kanıt: `power-supply-requisite.md` § Genel Bakış
- **K16.11.1.2 — Genel Bakış**
  - Sorumluluk: Isı tasarımı, Class AB amplifikatörün sürekli çalışmasını sağlamak için junction sıcaklıklarını 150°C sınırının altında tutar. Heatsink boyutlandırma, termal direnç zinciri analizi ve aktif soğutma…
  - Kanıt: `thermal-design.md` § Genel Bakış
- **K16.11.1.3 — Genel Bakış**
  - Sorumluluk: Bu doküman, K16 Class AB amplifikatörünün LTspice/PSpice simülasyonu için gerekli tüm bileşen modellerini ve simülasyon parametrelerini içerir. Her bir aktif ve pasif bileşen için verified SPICE…
  - Kanıt: `spice-model.md` § Genel Bakış
- **K16.11.1.4 — Genel Bakış**
  - Sorumluluk: Bu doküman, K16 Class AB 8-kanal amplifikatörün tüm bileşenlerini, seçim kriterlerini ve tedarikçi bilgilerini içerir. Her bileşen, ses kalitesi, güvenilirlik ve maliyet dengesi göz önünde…
  - Kanıt: `bom-classab.md` § Genel Bakış
- **K16.11.1.5 — Güç Kaynağı Blok Diyagramı**
  - Sorumluluk: «Güç Kaynağı Blok Diyagramı» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Güç Kaynağı Blok Diyagramı
- **K16.11.1.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Teknik Spesifikasyonlar

#### K16.11.2 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — power-supply-requisite.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-supply-requisite.md`, `thermal-design.md` § Hesaplamalar (11 bölüm başlığı)

- **K16.11.2.7 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Hesaplamalar
- **K16.11.2.8 — DC Çıkış Gerilimi**
  - Sorumluluk: «DC Çıkış Gerilimi» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Hesaplamalar > DC Çıkış Gerilimi
- **K16.11.2.9 — Ripple Hesabı**
  - Sorumluluk: «Ripple Hesabı» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Hesaplamalar > Ripple Hesabı
- **K16.11.2.10 — Toroid Trafo Seçimi**
  - Sorumluluk: «Toroid Trafo Seçimi» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Hesaplamalar > Toroid Trafo Seçimi
- **K16.11.2.11 — Filtre Kapasitörü Boyutlandırma**
  - Sorumluluk: «Filtre Kapasitörü Boyutlandırma» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Hesaplamalar > Filtre Kapasitörü Boyutlandırma
- **K16.11.2.12 — PSU Hız Tepkisi**
  - Sorumluluk: «PSU Hız Tepkisi» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Hesaplamalar > PSU Hız Tepkisi
- **K16.11.2.13 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Hesaplamalar
- **K16.11.2.14 — Isı Yükü**
  - Sorumluluk: «Isı Yükü» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Hesaplamalar > Isı Yükü
- **K16.11.2.15 — Heatsink Sıcaklığı**
  - Sorumluluk: «Heatsink Sıcaklığı» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Hesaplamalar > Heatsink Sıcaklığı
- **K16.11.2.16 — Ayrı Heatsink Tasarımı (Kanat başına)**
  - Sorumluluk: «Ayrı Heatsink Tasarımı (Kanat başına)» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Hesaplamalar > Ayrı Heatsink Tasarımı (Kanat başına)
- **K16.11.2.17 — Fan ile Aktif Soğutma**
  - Sorumluluk: «Fan ile Aktif Soğutma» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Hesaplamalar > Fan ile Aktif Soğutma

#### K16.11.3 — Regüle Devre / Kapasitör Seçimi / Güvenlik / Durum: Implementasyon

- **Sorumluluk:** «Regüle Devre» — power-supply-requisite.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-supply-requisite.md`, `thermal-design.md`, `spice-model.md`, `bom-classab.md` § Regüle Devre / Kapasitör Seçimi / Güvenlik / Durum: Implementasyon (7 bölüm başlığı)

- **K16.11.3.18 — Regüle Devre**
  - Sorumluluk: «Regüle Devre» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Regüle Devre
- **K16.11.3.19 — Kapasitör Seçimi**
  - Sorumluluk: «Kapasitör Seçimi» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Kapasitör Seçimi
- **K16.11.3.20 — Güvenlik**
  - Sorumluluk: «Güvenlik» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Güvenlik
- **K16.11.3.21 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — power-supply-requisite.md dosyasında belgelenen bölüm.
  - Kanıt: `power-supply-requisite.md` § Durum: Implementasyon
- **K16.11.3.22 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Durum: Implementasyon
- **K16.11.3.23 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § Durum: Implementasyon
- **K16.11.3.24 — Durum: Implementasyon**
  - Sorumluluk: «Durum: Implementasyon» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Durum: Implementasyon

#### K16.11.4 — Termal Direnç Zinciri / Heatsink Spesifikasyonları / Termal Ped ve Compound (+6 bölüm)

- **Sorumluluk:** «Termal Direnç Zinciri» — thermal-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-design.md`, `spice-model.md` § Termal Direnç Zinciri / Heatsink Spesifikasyonları / Termal Ped ve Compound (+6 bölüm) (8 bölüm başlığı)

- **K16.11.4.25 — Termal Direnç Zinciri**
  - Sorumluluk: «Termal Direnç Zinciri» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Termal Direnç Zinciri
- **K16.11.4.26 — Heatsink Spesifikasyonları**
  - Sorumluluk: «Heatsink Spesifikasyonları» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Heatsink Spesifikasyonları
- **K16.11.4.27 — Termal Ped ve Compound**
  - Sorumluluk: «Termal Ped ve Compound» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Termal Ped ve Compound
- **K16.11.4.28 — Termal Direnç Hesabı (Compound)**
  - Sorumluluk: «Termal Direnç Hesabı (Compound)» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Termal Ped ve Compound > Termal Direnç Hesabı (Compound)
- **K16.11.4.29 — Isı Dağılım Diyagramı**
  - Sorumluluk: «Isı Dağılım Diyagramı» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Isı Dağılım Diyagramı
- **K16.11.4.30 — Termal Zaman Sabitleri**
  - Sorumluluk: «Termal Zaman Sabitleri» — thermal-design.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-design.md` § Termal Zaman Sabitleri
- **K16.11.4.31 — Montaj Prosedürü**
  - Sorumluluk: Heatsink yüzeyini temizle (isopropil alkol) Transistör pad'lerine ince tabaka MX-6 uygula Transistörleri heatsink'e M3 vidalar ile sabitle
  - Kanıt: `thermal-design.md` § Montaj Prosedürü
- **K16.11.4.32 — Simülasyon Kurulumu**
  - Sorumluluk: «Simülasyon Kurulumu» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § Simülasyon Kurulumu

#### K16.11.5 — MJL21194 NPN Model (OnSemi) / MJL21193 PNP Model (OnSemi) (+3 bölüm) (+7 bölüm)

- **Sorumluluk:** «MJL21194 NPN Model (OnSemi)» — spice-model.md dosyasında belgelenen bölüm.
- **Kanıt:** `spice-model.md` § MJL21194 NPN Model (OnSemi) / MJL21193 PNP Model (OnSemi) (+3 bölüm) (+7 bölüm) (8 bölüm başlığı)

- **K16.11.5.33 — MJL21194 NPN Model (OnSemi)**
  - Sorumluluk: «MJL21194 NPN Model (OnSemi)» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § MJL21194 NPN Model (OnSemi)
- **K16.11.5.34 — MJL21193 PNP Model (OnSemi)**
  - Sorumluluk: «MJL21193 PNP Model (OnSemi)» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § MJL21193 PNP Model (OnSemi)
- **K16.11.5.35 — 2N5551 NPN Small Signal Model**
  - Sorumluluk: «2N5551 NPN Small Signal Model» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § 2N5551 NPN Small Signal Model
- **K16.11.5.36 — 2SA1015 PNP Small Signal Model**
  - Sorumluluk: «2SA1015 PNP Small Signal Model» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § 2SA1015 PNP Small Signal Model
- **K16.11.5.37 — BD139/BD140 Driver Models**
  - Sorumluluk: «BD139/BD140 Driver Models» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § BD139/BD140 Driver Models
- **K16.11.5.38 — Pasif Bileşen Modelleri**
  - Sorumluluk: «Pasif Bileşen Modelleri» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § Pasif Bileşen Modelleri
- **K16.11.5.39 — Simülasyon Komutları**
  - Sorumluluk: «Simülasyon Komutları» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § Simülasyon Komutları
- **K16.11.5.40 — Doğrulama Kriterleri**
  - Sorumluluk: «Doğrulama Kriterleri» — spice-model.md dosyasında belgelenen bölüm.
  - Kanıt: `spice-model.md` § Doğrulama Kriterleri

#### K16.11.6 — Aktif Bileşenler / Pasif Bileşenler

- **Sorumluluk:** «Aktif Bileşenler» — bom-classab.md dosyasında belgelenen bölüm.
- **Kanıt:** `bom-classab.md` § Aktif Bileşenler / Pasif Bileşenler (10 bölüm başlığı)

- **K16.11.6.41 — Aktif Bileşenler**
  - Sorumluluk: «Aktif Bileşenler» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Aktif Bileşenler
- **K16.11.6.42 — Güç Transistörleri (Her Kanal)**
  - Sorumluluk: «Güç Transistörleri (Her Kanal)» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Aktif Bileşenler > Güç Transistörleri (Her Kanal)
- **K16.11.6.43 — Küçük Sinyal Transistörleri**
  - Sorumluluk: «Küçük Sinyal Transistörleri» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Aktif Bileşenler > Küçük Sinyal Transistörleri
- **K16.11.6.44 — Diod**
  - Sorumluluk: «Diod» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Aktif Bileşenler > Diod
- **K16.11.6.45 — Koruma**
  - Sorumluluk: «Koruma» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Aktif Bileşenler > Koruma
- **K16.11.6.46 — Pasif Bileşenler**
  - Sorumluluk: «Pasif Bileşenler» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Pasif Bileşenler
- **K16.11.6.47 — Dirençler (Metal Film %1)**
  - Sorumluluk: «Dirençler (Metal Film %1)» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Pasif Bileşenler > Dirençler (Metal Film %1)
- **K16.11.6.48 — Kapasitörler**
  - Sorumluluk: «Kapasitörler» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Pasif Bileşenler > Kapasitörler
- **K16.11.6.49 — Güç Kaynağı Kapasitörleri**
  - Sorumluluk: «Güç Kaynağı Kapasitörleri» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Pasif Bileşenler > Güç Kaynağı Kapasitörleri
- **K16.11.6.50 — Potansiyometre**
  - Sorumluluk: «Potansiyometre» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Pasif Bileşenler > Potansiyometre

#### K16.11.7 — Mekanik Bileşenler / Maliyet Özeti / Tedarikçi Listesi (+3 bölüm)

- **Sorumluluk:** «Mekanik Bileşenler» — bom-classab.md dosyasında belgelenen bölüm.
- **Kanıt:** `bom-classab.md` § Mekanik Bileşenler / Maliyet Özeti / Tedarikçi Listesi (+3 bölüm) (7 bölüm başlığı)

- **K16.11.7.51 — Mekanik Bileşenler**
  - Sorumluluk: «Mekanik Bileşenler» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Mekanik Bileşenler
- **K16.11.7.52 — Maliyet Özeti**
  - Sorumluluk: «Maliyet Özeti» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Maliyet Özeti
- **K16.11.7.53 — Tedarikçi Listesi**
  - Sorumluluk: «Tedarikçi Listesi» — bom-classab.md dosyasında belgelenen bölüm.
  - Kanıt: `bom-classab.md` § Tedarikçi Listesi
- **K16.11.7.54 — Bileşen Seçim Kriterleri**
  - Sorumluluk: MJL21194/93: OnSemi, TO-264, 250W, audio grade Eşleştirme: hFE %5 tolerans içinde Alternatif: 2SC5200/2SA1943 (Toshiba)
  - Kanıt: `bom-classab.md` § Bileşen Seçim Kriterleri
- **K16.11.7.55 — Transistör Seçimi**
  - Sorumluluk: MJL21194/93: OnSemi, TO-264, 250W, audio grade Eşleştirme: hFE %5 tolerans içinde Alternatif: 2SC5200/2SA1943 (Toshiba)
  - Kanıt: `bom-classab.md` § Bileşen Seçim Kriterleri > Transistör Seçimi
- **K16.11.7.56 — Kapasitör Seçimi**
  - Sorumluluk: Nichicon KG/KZ: Audio grade, low ESR Ripple akımı: ≥5A @ 100kHz Ömür: 2000 saat @ 105°C
  - Kanıt: `bom-classab.md` § Bileşen Seçim Kriterleri > Kapasitör Seçimi
- **K16.11.7.57 — Direnç Seçimi**
  - Sorumluluk: Metal film %1 tolerans Düşük termal drift (50ppm/°C) Axial mount (through-hole)
  - Kanıt: `bom-classab.md` § Bileşen Seçim Kriterleri > Direnç Seçimi

---

## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k16-class-ab/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayımın hangi kısmını desteklediğini listeler. 19 kanonik katman MD'si + index.md + README.md + CLAUDE.md + 34 tamamlayıcı kanıt satırı (index/README/plan → `K16.a.b.c`).

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `8-channel-design.md` | 8 Channel Amplifier Design | `K16.1` alanı (1), 6 alt alan, 10 yaprak → `K16` toplam 357 içine katkı |
| 2 | `diff-pair-input.md` | Differential Pair Input Stage | `K16.2` alanı (1), 7 alt alan, 13 yaprak → `K16` toplam 357 içine katkı |
| 3 | `current-mirror.md` | Current Mirror Bias | `K16.3` alanı (1), 7 alt alan, 12 yaprak → `K16` toplam 357 içine katkı |
| 4 | `vas-stage.md` | Voltage Amplification Stage (VAS) | `K16.4` alanı (1), 4 alt alan, 12 yaprak → `K16` toplam 357 içine katkı |
| 5 | `frequency-compensation.md` | Frequency Compensation | `K16.4` alanı (1), 6 alt alan, 13 yaprak → `K16` toplam 357 içine katkı |
| 6 | `vbe-multiplier.md` | Vbe Multiplier Bias | `K16.5` alanı (1), 7 alt alan, 13 yaprak → `K16` toplam 357 içine katkı |
| 7 | `darlington-pair.md` | Darlington Configuration | `K16.6` alanı (1), 7 alt alan, 12 yaprak → `K16` toplam 357 içine katkı |
| 8 | `output-stage.md` | Push-Pull Output Stage | `K16.7` alanı (1), 7 alt alan, 11 yaprak → `K16` toplam 357 içine katkı |
| 9 | `feedback-network.md` | Negative Feedback Network | `K16.8` alanı (1), 7 alt alan, 14 yaprak → `K16` toplam 357 içine katkı |
| 10 | `mjle21194.md` | MJL21194 NPN Güç Transistörü | `K16.9` alanı (1), 5 alt alan, 12 yaprak → `K16` toplam 357 içine katkı |
| 11 | `mjle21193.md` | MJL21193 PNP Güç Transistörü | `K16.9` alanı (1), 5 alt alan, 10 yaprak → `K16` toplam 357 içine katkı |
| 12 | `protection-dc-offset.md` | DC Offset Koruma | `K16.10` alanı (1), 6 alt alan, 15 yaprak → `K16` toplam 357 içine katkı |
| 13 | `protection-overcurrent.md` | Overcurrent Protection | `K16.10` alanı (1), 5 alt alan, 15 yaprak → `K16` toplam 357 içine katkı |
| 14 | `protection-short-circuit.md` | Short Circuit Protection | `K16.10` alanı (1), 6 alt alan, 15 yaprak → `K16` toplam 357 içine katkı |
| 15 | `protection-thermal.md` | Thermal Shutdown Protection | `K16.10` alanı (1), 6 alt alan, 12 yaprak → `K16` toplam 357 içine katkı |
| 16 | `power-supply-requisite.md` | Power Supply Requirements | `K16.11` alanı (1), 3 alt alan, 13 yaprak → `K16` toplam 357 içine katkı |
| 17 | `thermal-design.md` | Thermal Design & Heatsink | `K16.11` alanı (1), 4 alt alan, 14 yaprak → `K16` toplam 357 içine katkı |
| 18 | `spice-model.md` | SPICE Simulation Parameters | `K16.11` alanı (1), 4 alt alan, 11 yaprak → `K16` toplam 357 içine katkı |
| 19 | `bom-classab.md` | BOM - Bill of Materials | `K16.11` alanı (1), 4 alt alan, 19 yaprak → `K16` toplam 357 içine katkı |
| 20 | `index.md` | Katman ana sayfası: diyagram, tablolar, bağımlılıklar | `K16.a) alan tanımı bağlamı + 34 tamamlayıcı `K16.a.b.c` kanıtı |
| 21 | `README.md` | Katman künyesi, özet tablolar | `K16.a`/`K16.a.b` doğrulaması + bileşen-tablosu yaprak kanıtları |
| 22 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |
| 23 | `frontend-restructuring-plan.md §2.1 (L16 satırı)` | Üst katman haritasında L16 = Class AB Amplifikatör (50W/kanal) | 1 tamamlayıcı `K16.a.b.c` yaprağı + `K16.a` alan tanımı |

**Sayım dayanağı:** 19 dosyadan çıkarılan 280 kanıt havuzu (246 H2/H3 başlık + 34 index/README/plan satırı) içinden hedef c=280 için tamamı kullanıldı; alan/alt alan sayıları a=11, b=7 ile kilitlidir.

*K16 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
