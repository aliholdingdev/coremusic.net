---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K19 PCB Tasarım Layer"
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

# K19: PCB Tasarım Layer

**Katman:** K19 (PCB Tasarım)
**Kapsam:** 6-layer stackup, bileşen yerleşimi, kontrollü empedans, sinyal bütünlüğü, yıldız topraklama, PDN, termal via, EMC ve üretim
**Sorumlu Agent:** Audio Hardware Engineer / PCB Designer
**Bileşen Sayısı:** 9 kanonik katman MD'si
**Bağımlılık:** K19 ↔ K1 — bağımsız üretim katmanı (matris §2.2)

---

## 1. Genel Bakış

K19, donanım bileşenlerinin fiziksel kart tasarımından sorumludur: 6 katmanlı stackup, kontrollü empedans ve sinyal bütünlüğü kuralları, yıldız topraklama, güç dağıtım ağı (PDN), termal via'lar ve EMC/üretim spesifikasyonları bu katmanda tanımlanır. Stackup diyagramı ve tasarım kuralları tablosu `index.md` dosyasındadır.

---

## 2. Kaynak Dökümanlar

- `index.md` — katman ana sayfası (mimari diyagram, tablolar, bağımlılıklar)
- `CLAUDE.md` — katman kural ve kapsam notları
- Alt katman şeması ve kanıt kataloğu bu README'ye 2026-09-24'te eklenmiştir (son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması).


---

## Alt Katman Şeması (K19.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K19 katmanını onaylı şema biçiminde (K19 → K19.a → K19.a.b → K19.a.b.c) belgeler. Alanlar (a) PCB tasarım disiplinleri, alt alanlar (b) dosyalardaki H2 bölüm başlıkları, yapraklar (c) k19-pcb/ kanonik MD dosyalarındaki gerçek H2/H3 (+H4 kanıt havuzu) bölüm başlıklarından türetilmiştir; her yaprak kanıt satırıyla kaynak dosya ve bölümünü gösterir. Uydurma düğüm yoktur.

**Şema kuralları:**

1. Zorunlu şema: `K19` → `K19.a` → `K19.a.b`; seviye-4 (`K19.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K16–K20 beş BAĞIMSIZ üretim katmanıdır; hiyerarşi yok, birbirine alt değildir (matris §1.1, §1.3 K3, §5.2 #21).
5. Bağımlılık bağlamı: K19 ↔ K1 (çift yönlü; K19 bağımsız üretim katmanı — matris §2.2). K19 → K0 yasaktır (matris §5.1 #12); K19'un diğer üretim katmanlarına bağımlılığı YOKTUR (matris §5.2 #21).
6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K19` (a alan) | 8 | 8 | 8 | +0 |
| `K19.a.b` (a×b alt alan) | 40 | 40 | — | 0 |
| `K19.a.b.c` (c yaprak) | 120 | 120 | 157 | +37 |
| **Toplam düğüm** | **160** | **160** | **197** | **+37** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K19.1` | 6-Layer Stackup | 5 | 21 | `6-layer-stackup.md` |
| `K19.2` | Bileşen Yerleşimi | 5 | 11 | `component-placement.md` |
| `K19.3` | Kontrollü Empedans | 5 | 12 | `controlled-impedance.md` |
| `K19.4` | Sinyal Bütünlüğü | 5 | 12 | `signal-integrity.md` |
| `K19.5` | Yıldız Topraklama | 5 | 11 | `star-grounding.md` |
| `K19.6` | Güç Dağıtımı (PDN) | 5 | 11 | `power-distribution.md` |
| `K19.7` | Termal Via'lar | 5 | 15 | `thermal-vias.md` |
| `K19.8` | EMC Uyumluluğu & Üretim | 5 | 27 | `emc-compliance.md`, `pcb-fabrication.md` |

### K19.1 — 6-Layer Stackup

**Sorumluluk:** L1–L6 katman görevleri, FR-4/copper/solder mask spesifikasyonu, via yapıları ve EMC kuralları.
**Kanıt dosyaları:** `6-layer-stackup.md` — havuz 28 başlık, kullanıldı 21, seçim dışı 7.

#### K19.1.1 — Genel Bakış / Katman Yapısı

- **Sorumluluk:** 6 katmanlı PCB stackup, COREMUSIC audio platformu için optimize edilmiş bir kart yapısı sunar. Her katman belirli bir fonksiyona hizmet eder: sinyal…
- **Kanıt:** `6-layer-stackup.md` § Genel Bakış / Katman Yapısı (2 bölüm başlığı)

- **K19.1.1.1 — Genel Bakış**
  - Sorumluluk: 6 katmanlı PCB stackup, COREMUSIC audio platformu için optimize edilmiş bir kart yapısı sunar. Her katman belirli bir fonksiyona hizmet eder: sinyal yönlendirme, güç dağıtımı ve EMC kontrolü. Toplam…
  - Kanıt: `6-layer-stackup.md` § Genel Bakış
- **K19.1.1.2 — Katman Yapısı**
  - Sorumluluk: «Katman Yapısı» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Katman Yapısı

#### K19.1.2 — Katman Fonksiyonları

- **Sorumluluk:** Amaç: Ana bileşen yerleşimi ve sinyal yönlendirme Bileşenler: DSP, ADC, DAC, OP-AMP, konnektörler
- **Kanıt:** `6-layer-stackup.md` § Katman Fonksiyonları (7 bölüm başlığı)

- **K19.1.2.3 — Katman Fonksiyonları**
  - Sorumluluk: Amaç: Ana bileşen yerleşimi ve sinyal yönlendirme Bileşenler: DSP, ADC, DAC, OP-AMP, konnektörler
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları
- **K19.1.2.4 — L1: Top Signal Layer**
  - Sorumluluk: Amaç: Ana bileşen yerleşimi ve sinyal yönlendirme Bileşenler: DSP, ADC, DAC, OP-AMP, konnektörler
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları > L1: Top Signal Layer
- **K19.1.2.5 — L2: Ground Plane (Inner 1)**
  - Sorumluluk: Amaç: Sürekli referans düzlemi, düşük empedans dönüş yolu Özellik: Delik openings minimize edilmeli
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları > L2: Ground Plane (Inner 1)
- **K19.1.2.6 — L3: Inner Signal Layer (Inner 2)**
  - Sorumluluk: Amaç: Yüksek hızlı sinyaller için izole edilmiş katman Sinyaller: I2S bus, USB differential pairs, SPI
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları > L3: Inner Signal Layer (Inner 2)
- **K19.1.2.7 — L4: Power Plane (Inner 3)**
  - Sorumluluk: Amaç: Güç dağıtımı için split plane yapısı Planes: Kurallar:
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları > L4: Power Plane (Inner 3)
- **K19.1.2.8 — L5: Ground Plane (Inner 4)**
  - Sorumluluk: Amaç: L6 sinyalleri için dönüş yolu Özellik: L2 ile via stitching ile bağlanmalı Kurallar:
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları > L5: Ground Plane (Inner 4)
- **K19.1.2.9 — L6: Bottom Signal Layer**
  - Sorumluluk: Amaç: SMD bileşenler ve ek routing Bileşenler: Pasifler, bypass kapasitörleri, konnektörler
  - Kanıt: `6-layer-stackup.md` § Katman Fonksiyonları > L6: Bottom Signal Layer

#### K19.1.3 — Malzeme Spesifikasyonları

- **Sorumluluk:** «Malzeme Spesifikasyonları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
- **Kanıt:** `6-layer-stackup.md` § Malzeme Spesifikasyonları (4 bölüm başlığı)

- **K19.1.3.10 — Malzeme Spesifikasyonları**
  - Sorumluluk: «Malzeme Spesifikasyonları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Malzeme Spesifikasyonları
- **K19.1.3.11 — FR-4 Dielectric**
  - Sorumluluk: «FR-4 Dielectric» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Malzeme Spesifikasyonları > FR-4 Dielectric
- **K19.1.3.12 — Copper Foil**
  - Sorumluluk: «Copper Foil» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Malzeme Spesifikasyonları > Copper Foil
- **K19.1.3.13 — Solder Mask**
  - Sorumluluk: «Solder Mask» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Malzeme Spesifikasyonları > Solder Mask

#### K19.1.4 — Impedans Hesaplamaları

- **Sorumluluk:** «Impedans Hesaplamaları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
- **Kanıt:** `6-layer-stackup.md` § Impedans Hesaplamaları (3 bölüm başlığı)

- **K19.1.4.14 — Impedans Hesaplamaları**
  - Sorumluluk: «Impedans Hesaplamaları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Impedans Hesaplamaları
- **K19.1.4.15 — Microstrip (L1, L6)**
  - Sorumluluk: «Microstrip (L1, L6)» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Impedans Hesaplamaları > Microstrip (L1, L6)
- **K19.1.4.16 — Stripline (L3)**
  - Sorumluluk: «Stripline (L3)» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Impedans Hesaplamaları > Stripline (L3)

#### K19.1.5 — Via Yapıları / Stitching Via Kuralları / EMC Tasarım Kuralları

- **Sorumluluk:** «Via Yapıları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
- **Kanıt:** `6-layer-stackup.md` § Via Yapıları / Stitching Via Kuralları / EMC Tasarım Kuralları (5 bölüm başlığı)

- **K19.1.5.17 — Via Yapıları**
  - Sorumluluk: «Via Yapıları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Via Yapıları
- **K19.1.5.18 — Through-Hole Via**
  - Sorumluluk: «Through-Hole Via» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Via Yapıları > Through-Hole Via
- **K19.1.5.19 — Via-in-Pad (BGA)**
  - Sorumluluk: «Via-in-Pad (BGA)» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Via Yapıları > Via-in-Pad (BGA)
- **K19.1.5.20 — Stitching Via Kuralları**
  - Sorumluluk: «Stitching Via Kuralları» — 6-layer-stackup.md dosyasında belgelenen bölüm.
  - Kanıt: `6-layer-stackup.md` § Stitching Via Kuralları
- **K19.1.5.21 — EMC Tasarım Kuralları**
  - Sorumluluk: L2 ve L5 sürekli copper pour olmalı Slot opening: max 2mm Split plane: sadece L4 (power)
  - Kanıt: `6-layer-stackup.md` § EMC Tasarım Kuralları

### K19.2 — Bileşen Yerleşimi

**Sorumluluk:** Fonksiyonel bölge haritası, sinyal akışı prensibi, bileşen gruplama ve DFM yerleşim kuralları.
**Kanıt dosyaları:** `component-placement.md` — havuz 14 başlık, kullanıldı 11, seçim dışı 3.

#### K19.2.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC PCB tasarımında bileşen yerleşimi, sinyal akışı, termal yönetim ve üretim kolaylığı için kritik öneme sahiptir. Bileşenler fonksiyonel gruplara…
- **Kanıt:** `component-placement.md` § Genel Bakış (1 bölüm başlığı)

- **K19.2.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC PCB tasarımında bileşen yerleşimi, sinyal akışı, termal yönetim ve üretim kolaylığı için kritik öneme sahiptir. Bileşenler fonksiyonel gruplara ayrılarak, sinyal yolunu kısaltacak ve…
  - Kanıt: `component-placement.md` § Genel Bakış

#### K19.2.2 — Tasarım Kuralları

- **Sorumluluk:** «Tasarım Kuralları» — component-placement.md dosyasında belgelenen bölüm.
- **Kanıt:** `component-placement.md` § Tasarım Kuralları (1 bölüm başlığı)

- **K19.2.2.2 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Tasarım Kuralları

#### K19.2.3 — Teknik Detaylar (I)

- **Sorumluluk:** «Teknik Detaylar» — component-placement.md dosyasında belgelenen bölüm.
- **Kanıt:** `component-placement.md` § Teknik Detaylar (I) (4 bölüm başlığı)

- **K19.2.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar
- **K19.2.3.4 — Fonksiyonel Bölge Haritası**
  - Sorumluluk: «Fonksiyonel Bölge Haritası» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar > Fonksiyonel Bölge Haritası
- **K19.2.3.5 — Sinyal Akışı Prensibi**
  - Sorumluluk: «Sinyal Akışı Prensibi» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar > Sinyal Akışı Prensibi
- **K19.2.3.6 — Bileşen Gruplama Stratejisi**
  - Sorumluluk: «Bileşen Gruplama Stratejisi» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar > Bileşen Gruplama Stratejisi

#### K19.2.4 — Teknik Detaylar (II)

- **Sorumluluk:** «Bileşen Yerleşim Kuralları» — component-placement.md dosyasında belgelenen bölüm.
- **Kanıt:** `component-placement.md` § Teknik Detaylar (II) (3 bölüm başlığı)

- **K19.2.4.7 — Bileşen Yerleşim Kuralları**
  - Sorumluluk: «Bileşen Yerleşim Kuralları» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar > Bileşen Yerleşim Kuralları
- **K19.2.4.8 — Floorplan Detayları**
  - Sorumluluk: «Floorplan Detayları» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar > Floorplan Detayları
- **K19.2.4.9 — Design for Manufacturing (DFM)**
  - Sorumluluk: «Design for Manufacturing (DFM)» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § Teknik Detaylar > Design for Manufacturing (DFM)

#### K19.2.5 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — component-placement.md dosyasında belgelenen bölüm.
- **Kanıt:** `component-placement.md` § KiCad/Altium Ayarları (2 bölüm başlığı)

- **K19.2.5.10 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § KiCad/Altium Ayarları
- **K19.2.5.11 — KiCad 8.0**
  - Sorumluluk: «KiCad 8.0» — component-placement.md dosyasında belgelenen bölüm.
  - Kanıt: `component-placement.md` § KiCad/Altium Ayarları > KiCad 8.0

### K19.3 — Kontrollü Empedans

**Sorumluluk:** Microstrip/stripline modelleri, I2S/USB sinyal sınıflandırma, length matching ve terminasyon.
**Kanıt dosyaları:** `controlled-impedance.md` — havuz 16 başlık, kullanıldı 12, seçim dışı 4.

#### K19.3.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC platformunda I2S, USB ve SPI yüksek hızlı dijital sinyaller için kontrollü empedans yönlendirme gereklidir. 50Ω tek uçlu (single-ended) ve 90Ω…
- **Kanıt:** `controlled-impedance.md` § Genel Bakış (1 bölüm başlığı)

- **K19.3.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC platformunda I2S, USB ve SPI yüksek hızlı dijital sinyaller için kontrollü empedans yönlendirme gereklidir. 50Ω tek uçlu (single-ended) ve 90Ω diferansiyel empedans hedefleri ile sinyal…
  - Kanıt: `controlled-impedance.md` § Genel Bakış

#### K19.3.2 — Tasarım Kuralları

- **Sorumluluk:** «Tasarım Kuralları» — controlled-impedance.md dosyasında belgelenen bölüm.
- **Kanıt:** `controlled-impedance.md` § Tasarım Kuralları (1 bölüm başlığı)

- **K19.3.2.2 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Tasarım Kuralları

#### K19.3.3 — Teknik Detaylar

- **Sorumluluk:** Microstrip yapısında sinyal hattı bir dielektrik tabakası üzerindedir ve alt tarafta tek bir referans düzlemi bulunur.
- **Kanıt:** `controlled-impedance.md` § Teknik Detaylar (7 bölüm başlığı)

- **K19.3.3.3 — Teknik Detaylar**
  - Sorumluluk: Microstrip yapısında sinyal hattı bir dielektrik tabakası üzerindedir ve alt tarafta tek bir referans düzlemi bulunur.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar
- **K19.3.3.4 — Empedans Modelleri**
  - Sorumluluk: Microstrip yapısında sinyal hattı bir dielektrik tabakası üzerindedir ve alt tarafta tek bir referans düzlemi bulunur.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar > Empedans Modelleri
- **K19.3.3.5 — Sinyal Sınıflandırması**
  - Sorumluluk: «Sinyal Sınıflandırması» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar > Sinyal Sınıflandırması
- **K19.3.3.6 — Length Matching Kuralları**
  - Sorumluluk: «Length Matching Kuralları» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar > Length Matching Kuralları
- **K19.3.3.7 — Crosstalk Analizi**
  - Sorumluluk: «Crosstalk Analizi» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar > Crosstalk Analizi
- **K19.3.3.8 — Termination Stratejileri**
  - Sorumluluk: «Termination Stratejileri» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar > Termination Stratejileri
- **K19.3.3.9 — Via Geçiş Etkileri**
  - Sorumluluk: «Via Geçiş Etkileri» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Teknik Detaylar > Via Geçiş Etkileri

#### K19.3.4 — Simülasyon Sonuçları

- **Sorumluluk:** «Simülasyon Sonuçları» — controlled-impedance.md dosyasında belgelenen bölüm.
- **Kanıt:** `controlled-impedance.md` § Simülasyon Sonuçları (2 bölüm başlığı)

- **K19.3.4.10 — Simülasyon Sonuçları**
  - Sorumluluk: «Simülasyon Sonuçları» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Simülasyon Sonuçları
- **K19.3.4.11 — CST Microwave Studio Results**
  - Sorumluluk: «CST Microwave Studio Results» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § Simülasyon Sonuçları > CST Microwave Studio Results

#### K19.3.5 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — controlled-impedance.md dosyasında belgelenen bölüm.
- **Kanıt:** `controlled-impedance.md` § KiCad/Altium Ayarları (1 bölüm başlığı)

- **K19.3.5.12 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — controlled-impedance.md dosyasında belgelenen bölüm.
  - Kanıt: `controlled-impedance.md` § KiCad/Altium Ayarları

### K19.4 — Sinyal Bütünlüğü

**Sorumluluk:** Yansıma, crosstalk, jitter, insertion loss; empedans simülasyonu, eye diagram ve düzeltme stratejileri.
**Kanıt dosyaları:** `signal-integrity.md` — havuz 15 başlık, kullanıldı 12, seçim dışı 3.

#### K19.4.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC platformunda sinyal bütünlüğü (Signal Integrity - SI), yüksek hızlı dijital sinyallerin bozulmadan iletilmesini sağlar. I2S, USB ve SPI hatlarında…
- **Kanıt:** `signal-integrity.md` § Genel Bakış (1 bölüm başlığı)

- **K19.4.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC platformunda sinyal bütünlüğü (Signal Integrity - SI), yüksek hızlı dijital sinyallerin bozulmadan iletilmesini sağlar. I2S, USB ve SPI hatlarında yansıma, crosstalk, jitter ve insertion…
  - Kanıt: `signal-integrity.md` § Genel Bakış

#### K19.4.2 — Tasarım Kuralları

- **Sorumluluk:** «Tasarım Kuralları» — signal-integrity.md dosyasında belgelenen bölüm.
- **Kanıt:** `signal-integrity.md` § Tasarım Kuralları (1 bölüm başlığı)

- **K19.4.2.2 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Tasarım Kuralları

#### K19.4.3 — Teknik Detaylar (I)

- **Sorumluluk:** «Teknik Detaylar» — signal-integrity.md dosyasında belgelenen bölüm.
- **Kanıt:** `signal-integrity.md` § Teknik Detaylar (I) (4 bölüm başlığı)

- **K19.4.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar
- **K19.4.3.4 — Sinyal Bozulma Kaynakları**
  - Sorumluluk: «Sinyal Bozulma Kaynakları» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Sinyal Bozulma Kaynakları
- **K19.4.3.5 — Empedans Simülasyonu**
  - Sorumluluk: «Empedans Simülasyonu» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Empedans Simülasyonu
- **K19.4.3.6 — Crosstalk Analizi**
  - Sorumluluk: «Crosstalk Analizi» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Crosstalk Analizi

#### K19.4.4 — Teknik Detaylar (II)

- **Sorumluluk:** «Crosstalk Önleme Stratejileri» — signal-integrity.md dosyasında belgelenen bölüm.
- **Kanıt:** `signal-integrity.md` § Teknik Detaylar (II) (4 bölüm başlığı)

- **K19.4.4.7 — Crosstalk Önleme Stratejileri**
  - Sorumluluk: «Crosstalk Önleme Stratejileri» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Crosstalk Önleme Stratejileri
- **K19.4.4.8 — Impedans Uyumsuzluğu Düzeltmeleri**
  - Sorumluluk: «Impedans Uyumsuzluğu Düzeltmeleri» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Impedans Uyumsuzluğu Düzeltmeleri
- **K19.4.4.9 — Eye Diagram Analizi**
  - Sorumluluk: «Eye Diagram Analizi» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Eye Diagram Analizi
- **K19.4.4.10 — Sinyal Bütünlüğü Testleri**
  - Sorumluluk: «Sinyal Bütünlüğü Testleri» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § Teknik Detaylar > Sinyal Bütünlüğü Testleri

#### K19.4.5 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — signal-integrity.md dosyasında belgelenen bölüm.
- **Kanıt:** `signal-integrity.md` § KiCad/Altium Ayarları (2 bölüm başlığı)

- **K19.4.5.11 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § KiCad/Altium Ayarları
- **K19.4.5.12 — KiCad 8.0**
  - Sorumluluk: «KiCad 8.0» — signal-integrity.md dosyasında belgelenen bölüm.
  - Kanıt: `signal-integrity.md` § KiCad/Altium Ayarları > KiCad 8.0

### K19.5 — Yıldız Topraklama

**Sorumluluk:** Analog/dijital/power GND bölümleri, star point topolojisi, ferrite bead seçimi ve toprak döngüsü önleme.
**Kanıt dosyaları:** `star-grounding.md` — havuz 15 başlık, kullanıldı 11, seçim dışı 4.

#### K19.5.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC audio platformunda analog ve dijital devrelerin bir arada bulunması, toprak döngüsü (ground loop) ve gürültü kepçeleme (noise coupling) sorunlarını…
- **Kanıt:** `star-grounding.md` § Genel Bakış (1 bölüm başlığı)

- **K19.5.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC audio platformunda analog ve dijital devrelerin bir arada bulunması, toprak döngüsü (ground loop) ve gürültü kepçeleme (noise coupling) sorunlarını beraberinde getirir. Star grounding…
  - Kanıt: `star-grounding.md` § Genel Bakış

#### K19.5.2 — Tasarım Kuralları

- **Sorumluluk:** «Tasarım Kuralları» — star-grounding.md dosyasında belgelenen bölüm.
- **Kanıt:** `star-grounding.md` § Tasarım Kuralları (1 bölüm başlığı)

- **K19.5.2.2 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Tasarım Kuralları

#### K19.5.3 — Teknik Detaylar (I)

- **Sorumluluk:** «Teknik Detaylar» — star-grounding.md dosyasında belgelenen bölüm.
- **Kanıt:** `star-grounding.md` § Teknik Detaylar (I) (4 bölüm başlığı)

- **K19.5.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar
- **K19.5.3.4 — Ground Plane Bölümleri**
  - Sorumluluk: «Ground Plane Bölümleri» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > Ground Plane Bölümleri
- **K19.5.3.5 — Star Point Topolojisi**
  - Sorumluluk: «Star Point Topolojisi» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > Star Point Topolojisi
- **K19.5.3.6 — Ground Plane Bölümleme Stratejisi**
  - Sorumluluk: «Ground Plane Bölümleme Stratejisi» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > Ground Plane Bölümleme Stratejisi

#### K19.5.4 — Teknik Detaylar (II)

- **Sorumluluk:** «Ferrite Bead Seçimi» — star-grounding.md dosyasında belgelenen bölüm.
- **Kanıt:** `star-grounding.md` § Teknik Detaylar (II) (4 bölüm başlığı)

- **K19.5.4.7 — Ferrite Bead Seçimi**
  - Sorumluluk: «Ferrite Bead Seçimi» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > Ferrite Bead Seçimi
- **K19.5.4.8 — Ground Loop Önleme**
  - Sorumluluk: «Ground Loop Önleme» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > Ground Loop Önleme
- **K19.5.4.9 — Toprak Plane Bölümleme Kuralları**
  - Sorumluluk: «Toprak Plane Bölümleme Kuralları» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > Toprak Plane Bölümleme Kuralları
- **K19.5.4.10 — EMC Etki Analizi**
  - Sorumluluk: «EMC Etki Analizi» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § Teknik Detaylar > EMC Etki Analizi

#### K19.5.5 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — star-grounding.md dosyasında belgelenen bölüm.
- **Kanıt:** `star-grounding.md` § KiCad/Altium Ayarları (1 bölüm başlığı)

- **K19.5.5.11 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — star-grounding.md dosyasında belgelenen bölüm.
  - Kanıt: `star-grounding.md` § KiCad/Altium Ayarları

### K19.6 — Güç Dağıtımı (PDN)

**Sorumluluk:** L4 güç plane'i, PDN impedans hedefleri, bypass stratejisi, LDO rail'leri ve iz/akım kapasitesi.
**Kanıt dosyaları:** `power-distribution.md` — havuz 15 başlık, kullanıldı 11, seçim dışı 4.

#### K19.6.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC platformunda güç dağıtımı, hassas analog devreler için temiz ve kararlı besleme sağlar. 12V DC girişten alınan güç, birden fazla LDO regülatör ile…
- **Kanıt:** `power-distribution.md` § Genel Bakış (1 bölüm başlığı)

- **K19.6.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC platformunda güç dağıtımı, hassas analog devreler için temiz ve kararlı besleme sağlar. 12V DC girişten alınan güç, birden fazla LDO regülatör ile 3.3V, 1.8V ve ±5V seviyelerine…
  - Kanıt: `power-distribution.md` § Genel Bakış

#### K19.6.2 — Tasarım Kuralları

- **Sorumluluk:** «Tasarım Kuralları» — power-distribution.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-distribution.md` § Tasarım Kuralları (1 bölüm başlığı)

- **K19.6.2.2 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Tasarım Kuralları

#### K19.6.3 — Teknik Detaylar (I)

- **Sorumluluk:** «Teknik Detaylar» — power-distribution.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-distribution.md` § Teknik Detaylar (I) (4 bölüm başlığı)

- **K19.6.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar
- **K19.6.3.4 — Güç Mimarisi Şeması**
  - Sorumluluk: «Güç Mimarisi Şeması» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Güç Mimarisi Şeması
- **K19.6.3.5 — Güç Plane Tasarımı (L4)**
  - Sorumluluk: «Güç Plane Tasarımı (L4)» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Güç Plane Tasarımı (L4)
- **K19.6.3.6 — Güç Dağıtım Ağı (PDN) Analizi**
  - Sorumluluk: «Güç Dağıtım Ağı (PDN) Analizi» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Güç Dağıtım Ağı (PDN) Analizi

#### K19.6.4 — Teknik Detaylar (II)

- **Sorumluluk:** «Yüksek Akım Yolları» — power-distribution.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-distribution.md` § Teknik Detaylar (II) (4 bölüm başlığı)

- **K19.6.4.7 — Yüksek Akım Yolları**
  - Sorumluluk: «Yüksek Akım Yolları» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Yüksek Akım Yolları
- **K19.6.4.8 — Regülatör Detayları**
  - Sorumluluk: «Regülatör Detayları» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Regülatör Detayları
- **K19.6.4.9 — Copper Weight ve Trace Hesapları**
  - Sorumluluk: «Copper Weight ve Trace Hesapları» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Copper Weight ve Trace Hesapları
- **K19.6.4.10 — Power Sequencing**
  - Sorumluluk: «Power Sequencing» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § Teknik Detaylar > Power Sequencing

#### K19.6.5 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — power-distribution.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-distribution.md` § KiCad/Altium Ayarları (1 bölüm başlığı)

- **K19.6.5.11 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — power-distribution.md dosyasında belgelenen bölüm.
  - Kanıt: `power-distribution.md` § KiCad/Altium Ayarları

### K19.7 — Termal Via'lar

**Sorumluluk:** Through-hole ve via-in-pad termal via matrisi, pattern tipleri, copper pour ve fanout stratejileri.
**Kanıt dosyaları:** `thermal-vias.md` — havuz 19 başlık, kullanıldı 15, seçim dışı 4.

#### K19.7.1 — Genel Bakış / Tasarım Kuralları

- **Sorumluluk:** COREMUSIC PCB tasarımında termal yönetimi kritik öneme sahiptir. Güç amplifikatörleri, DSP ve voltage regülatörleri yüksek ısı üretir. Termal via'lar bu ısıyı…
- **Kanıt:** `thermal-vias.md` § Genel Bakış / Tasarım Kuralları (2 bölüm başlığı)

- **K19.7.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC PCB tasarımında termal yönetimi kritik öneme sahiptir. Güç amplifikatörleri, DSP ve voltage regülatörleri yüksek ısı üretir. Termal via'lar bu ısıyı board'un iç katmanlarına ve alt yüzeyine…
  - Kanıt: `thermal-vias.md` § Genel Bakış
- **K19.7.1.2 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Tasarım Kuralları

#### K19.7.2 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — thermal-vias.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-vias.md` § Teknik Detaylar (6 bölüm başlığı)

- **K19.7.2.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Teknik Detaylar
- **K19.7.2.4 — Termal Via Tipleri**
  - Sorumluluk: «Termal Via Tipleri» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Teknik Detaylar > Termal Via Tipleri
- **K19.7.2.5 — Termal Via Matris Tasarımı**
  - Sorumluluk: «Termal Via Matris Tasarımı» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Teknik Detaylar > Termal Via Matris Tasarımı
- **K19.7.2.6 — Termal Via Pattern Tipleri**
  - Sorumluluk: «Termal Via Pattern Tipleri» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Teknik Detaylar > Termal Via Pattern Tipleri
- **K19.7.2.7 — Copper Pour Stratejisi**
  - Sorumluluk: «Copper Pour Stratejisi» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Teknik Detaylar > Copper Pour Stratejisi
- **K19.7.2.8 — Fanout Stratejileri**
  - Sorumluluk: «Fanout Stratejileri» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Teknik Detaylar > Fanout Stratejileri

#### K19.7.3 — Simülasyon Verileri

- **Sorumluluk:** «Simülasyon Verileri» — thermal-vias.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-vias.md` § Simülasyon Verileri (3 bölüm başlığı)

- **K19.7.3.9 — Simülasyon Verileri**
  - Sorumluluk: «Simülasyon Verileri» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Simülasyon Verileri
- **K19.7.3.10 — Termal Analiz (Ansys Icepak)**
  - Sorumluluk: «Termal Analiz (Ansys Icepak)» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Simülasyon Verileri > Termal Analiz (Ansys Icepak)
- **K19.7.3.11 — Termal Direnç Karşılaştırması**
  - Sorumluluk: «Termal Direnç Karşılaştırması» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Simülasyon Verileri > Termal Direnç Karşılaştırması

#### K19.7.4 — Üretim Notları

- **Sorumluluk:** «Üretim Notları» — thermal-vias.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-vias.md` § Üretim Notları (3 bölüm başlığı)

- **K19.7.4.12 — Üretim Notları**
  - Sorumluluk: «Üretim Notları» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Üretim Notları
- **K19.7.4.13 — Via-in-Pad İşlemi**
  - Sorumluluk: «Via-in-Pad İşlemi» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Üretim Notları > Via-in-Pad İşlemi
- **K19.7.4.14 — Via Fill Malzemeleri**
  - Sorumluluk: «Via Fill Malzemeleri» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § Üretim Notları > Via Fill Malzemeleri

#### K19.7.5 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — thermal-vias.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-vias.md` § KiCad/Altium Ayarları (1 bölüm başlığı)

- **K19.7.5.15 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — thermal-vias.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-vias.md` § KiCad/Altium Ayarları

### K19.8 — EMC Uyumluluğu & Üretim

**Sorumluluk:** EMC test düzeneği ve limit margin'i, kalkanlama/filtreleme; FR-4/ENIG üretim, test ve kalite gereksinimleri.
**Kanıt dosyaları:** `emc-compliance.md`, `pcb-fabrication.md` — havuz 35 başlık, kullanıldı 27, seçim dışı 8.

#### K19.8.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC platformu, Avrupa ve Kuzey Amerika pazarlarında satılacak şekilde tasarlanmıştır. EMC (Electromagnetic Compatibility) uyumluluğu, cihazın…
- **Kanıt:** `emc-compliance.md`, `pcb-fabrication.md` § Genel Bakış (2 bölüm başlığı)

- **K19.8.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC platformu, Avrupa ve Kuzey Amerika pazarlarında satılacak şekilde tasarlanmıştır. EMC (Electromagnetic Compatibility) uyumluluğu, cihazın electromagnetic interference (EMI) yaratmamasını ve…
  - Kanıt: `emc-compliance.md` § Genel Bakış
- **K19.8.1.2 — Genel Bakış**
  - Sorumluluk: COREMUSIC PCB'si, yüksek ses kalitesi ve güvenilirlik için ENIG (Electroless Nickel Immersion Gold) yüzey bitişi ve 2oz bakır ağırlığı ile üretilir. Bu belge, fabrikasyon parametrelerini, yüzey bitiş…
  - Kanıt: `pcb-fabrication.md` § Genel Bakış

#### K19.8.2 — Tasarım Kuralları

- **Sorumluluk:** «Tasarım Kuralları» — emc-compliance.md dosyasında belgelenen bölüm.
- **Kanıt:** `emc-compliance.md`, `pcb-fabrication.md` § Tasarım Kuralları (2 bölüm başlığı)

- **K19.8.2.3 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Tasarım Kuralları
- **K19.8.2.4 — Tasarım Kuralları**
  - Sorumluluk: «Tasarım Kuralları» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Tasarım Kuralları

#### K19.8.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — emc-compliance.md dosyasında belgelenen bölüm.
- **Kanıt:** `emc-compliance.md`, `pcb-fabrication.md` § Teknik Detaylar (18 bölüm başlığı)

- **K19.8.3.5 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar
- **K19.8.3.6 — EMC Test Düzeneği**
  - Sorumluluk: «EMC Test Düzeneği» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > EMC Test Düzeneği
- **K19.8.3.7 — EMC Tasarım Stratejileri**
  - Sorumluluk: «EMC Tasarım Stratejileri» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > EMC Tasarım Stratejileri
- **K19.8.3.8 — Frekans Spektrumu Analizi**
  - Sorumluluk: «Frekans Spektrumu Analizi» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > Frekans Spektrumu Analizi
- **K19.8.3.9 — Topraklama ve Kalkanlama Stratejisi**
  - Sorumluluk: «Topraklama ve Kalkanlama Stratejisi» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > Topraklama ve Kalkanlama Stratejisi
- **K19.8.3.10 — EMC Bileşen Seçimi**
  - Sorumluluk: «EMC Bileşen Seçimi» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > EMC Bileşen Seçimi
- **K19.8.3.11 — EMC Layout Kuralları**
  - Sorumluluk: «EMC Layout Kuralları» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > EMC Layout Kuralları
- **K19.8.3.12 — EMC Doğrulama Testleri**
  - Sorumluluk: «EMC Doğrulama Testleri» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Teknik Detaylar > EMC Doğrulama Testleri
- **K19.8.3.13 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar
- **K19.8.3.14 — Malzeme Spesifikasyonları**
  - Sorumluluk: «Malzeme Spesifikasyonları» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Malzeme Spesifikasyonları
- **K19.8.3.15 — ENIG Yüzey Bitişi**
  - Sorumluluk: «ENIG Yüzey Bitişi» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > ENIG Yüzey Bitişi
- **K19.8.3.16 — Bakır Ağırlığı ve Kapasite**
  - Sorumluluk: «Bakır Ağırlığı ve Kapasite» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Bakır Ağırlığı ve Kapasite
- **K19.8.3.17 — Delik ve Via Spesifikasyonları**
  - Sorumluluk: «Delik ve Via Spesifikasyonları» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Delik ve Via Spesifikasyonları
- **K19.8.3.18 — Solder Mask Spesifikasyonları**
  - Sorumluluk: «Solder Mask Spesifikasyonları» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Solder Mask Spesifikasyonları
- **K19.8.3.19 — Silkscreen Spesifikasyonları**
  - Sorumluluk: «Silkscreen Spesifikasyonları» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Silkscreen Spesifikasyonları
- **K19.8.3.20 — Board Outline ve Routing**
  - Sorumluluk: «Board Outline ve Routing» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Board Outline ve Routing
- **K19.8.3.21 — Test Gereksinimleri**
  - Sorumluluk: «Test Gereksinimleri» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Test Gereksinimleri
- **K19.8.3.22 — Kalite Kontrol Prosedürleri**
  - Sorumluluk: «Kalite Kontrol Prosedürleri» — pcb-fabrication.md dosyasında belgelenen bölüm.
  - Kanıt: `pcb-fabrication.md` § Teknik Detaylar > Kalite Kontrol Prosedürleri

#### K19.8.4 — KiCad/Altium Ayarları

- **Sorumluluk:** «KiCad/Altium Ayarları» — emc-compliance.md dosyasında belgelenen bölüm.
- **Kanıt:** `emc-compliance.md` § KiCad/Altium Ayarları (3 bölüm başlığı)

- **K19.8.4.23 — KiCad/Altium Ayarları**
  - Sorumluluk: «KiCad/Altium Ayarları» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § KiCad/Altium Ayarları
- **K19.8.4.24 — KiCad 8.0**
  - Sorumluluk: «KiCad 8.0» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § KiCad/Altium Ayarları > KiCad 8.0
- **K19.8.4.25 — Altium Designer**
  - Sorumluluk: «Altium Designer» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § KiCad/Altium Ayarları > Altium Designer

#### K19.8.5 — Bağımlılıklar / Durum: Implementasyon

- **Sorumluluk:** «Bağımlılıklar» — emc-compliance.md dosyasında belgelenen bölüm.
- **Kanıt:** `emc-compliance.md` § Bağımlılıklar / Durum: Implementasyon (2 bölüm başlığı)

- **K19.8.5.26 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — emc-compliance.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-compliance.md` § Bağımlılıklar
- **K19.8.5.27 — Durum: Implementasyon**
  - Sorumluluk: EMC limitleri belirlendi Test düzeneği tanımlandı Tasarım stratejileri yazıldı
  - Kanıt: `emc-compliance.md` § Durum: Implementasyon

---

## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k19-pcb/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayımın hangi kısmını desteklediğini listeler. 9 kanonik katman MD'si + index.md + README.md + CLAUDE.md.

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `6-layer-stackup.md` | 6 Katmanlı PCB Stackup Tasarımı | `K19.1` alanı (1), 5 alt alan, 21 yaprak → `K19` toplam 160 içine katkı |
| 2 | `component-placement.md` | Bileşen Yerleşimi Stratejisi | `K19.2` alanı (1), 5 alt alan, 11 yaprak → `K19` toplam 160 içine katkı |
| 3 | `controlled-impedance.md` | Kontrollü Empedans Yönlendirme | `K19.3` alanı (1), 5 alt alan, 12 yaprak → `K19` toplam 160 içine katkı |
| 4 | `signal-integrity.md` | Sinyal Bütünlüğü Analizi | `K19.4` alanı (1), 5 alt alan, 12 yaprak → `K19` toplam 160 içine katkı |
| 5 | `star-grounding.md` | Star Topolojisi ve Ground Plane Bölme | `K19.5` alanı (1), 5 alt alan, 11 yaprak → `K19` toplam 160 içine katkı |
| 6 | `power-distribution.md` | Güç Dağıtımı ve Güç Düzlemi Tasarımı | `K19.6` alanı (1), 5 alt alan, 11 yaprak → `K19` toplam 160 içine katkı |
| 7 | `thermal-vias.md` | Termal Via Yerleşimi ve Via-in-Pad | `K19.7` alanı (1), 5 alt alan, 15 yaprak → `K19` toplam 160 içine katkı |
| 8 | `emc-compliance.md` | EMC Uyumluluğu ve EMC Test Prosedürleri | `K19.8` alanı (1), 5 alt alan, 15 yaprak → `K19` toplam 160 içine katkı |
| 9 | `pcb-fabrication.md` | PCB Fabrikasyon Spesifikasyonları | `K19.8` alanı (1), 3 alt alan, 12 yaprak → `K19` toplam 160 içine katkı |
| 10 | `index.md` | Katman ana sayfası: diyagram, tablolar, bağımlılıklar | `K19.a) alan tanımı bağlamı |
| 11 | `README.md` | Katman künyesi ve bu şema/katalog | `K19.a`/`K19.a.b` doğrulaması |
| 12 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |

**Sayım dayanağı:** 9 dosyadan çıkarılan 157 H2/H3 başlığı içinden hedef c=120 için 37 başlık orantılı olarak seçimin dışında bırakıldı (aşağıda); alan/alt alan sayıları a=8, b=5 ile kilitlidir.

### Seçim Dışı Kanıtlar

| Başlık | Dosya | Neden |
|--------|-------|-------|
| Ground Plane Bütünlüğü | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Sinyal Koridorları | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad/Altium Ayarları | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad 8.0 - Layer Setup | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `6-layer-stackup.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `component-placement.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `component-placement.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `component-placement.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad 8.0 | `controlled-impedance.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `controlled-impedance.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `controlled-impedance.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `controlled-impedance.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `signal-integrity.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `signal-integrity.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `signal-integrity.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad 8.0 | `star-grounding.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `star-grounding.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `star-grounding.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `star-grounding.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad 8.0 | `power-distribution.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `power-distribution.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `power-distribution.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `power-distribution.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad 8.0 | `thermal-vias.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `thermal-vias.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `thermal-vias.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `thermal-vias.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Panelizasyon | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Dosya Formatları | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Fabrikasyon Notları | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad/Altium Ayarları | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| KiCad 8.0 | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Altium Designer | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `pcb-fabrication.md` | hedef c=120 aşıldı; havuz 157 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |

*K19 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
