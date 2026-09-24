---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K20 BOM & Üretim Layer"
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

# K20: BOM & Üretim Layer

**Katman:** K20 (BOM & Üretim)
**Kapsam:** Bileşen listeleri (transistör, diyot, direnç, kondansatör, bobin, IC, konnektör), maliyet tahmini, üretim hattı ve kalite kontrol
**Sorumlu Agent:** Audio Hardware Engineer / Manufacturing Engineer
**Bileşen Sayısı:** 9 kanonik katman MD'si
**Bağımlılık:** K20 ↔ K1 — bağımsız üretim katmanı (matris §2.2)

---

## 1. Genel Bakış

K20, donanım tasarımının Bill of Materials (BOM) yapısını ve üretim süreçlerini tanımlar: bileşen ailesi bazlı listeler, tedarikçi stratejisi, maliyet/ROI analizi ve üretim hattı (pick & place, reflow, FAI/SPC) kalite adımları bu katmanda belgelenir. Hiyerarşik BOM yapısı, modül haritası ve toplam bileşen özeti `index.md` dosyasındadır.

---

## 2. Kaynak Dökümanlar

- `index.md` — katman ana sayfası (mimari diyagram, tablolar, bağımlılıklar)
- `CLAUDE.md` — katman kural ve kapsam notları
- Alt katman şeması ve kanıt kataloğu bu README'ye 2026-09-24'te eklenmiştir (son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması).


---

## Alt Katman Şeması (K20.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K20 katmanını onaylı şema biçiminde (K20 → K20.a → K20.a.b → K20.a.b.c) belgeler. Alanlar (a) bileşen aileleri ve üretim/maliyet, alt alanlar (b) dosyalardaki H2 bölüm başlıkları, yapraklar (c) k20-bom/ kanonik MD dosyalarındaki gerçek H2/H3 bölüm başlıklarından türetilmiştir; her yaprak kanıt satırıyla kaynak dosya ve bölümünü gösterir. Uydurma düğüm yoktur.

**Şema kuralları:**

1. Zorunlu şema: `K20` → `K20.a` → `K20.a.b`; seviye-4 (`K20.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K16–K20 beş BAĞIMSIZ üretim katmanıdır; hiyerarşi yok, birbirine alt değildir (matris §1.1, §1.3 K3, §5.2 #21).
5. Bağımlılık bağlamı: K20 ↔ K1 (çift yönlü; K20 bağımsız üretim katmanı — matris §2.2). K20 → K0 yasaktır (matris §5.1 #12); K20'nin diğer üretim katmanlarına bağımlılığı YOKTUR (matris §5.2 #21).
6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K20` (a alan) | 8 | 8 | 8 | +0 |
| `K20.a.b` (a×b alt alan) | 40 | 40 | — | 0 |
| `K20.a.b.c` (c yaprak) | 120 | 120 | 121 | +1 |
| **Toplam düğüm** | **160** | **160** | **161** | **+1** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K20.1` | Transistör BOM'u | 5 | 11 | `transistor-list.md` |
| `K20.2` | Diyot BOM'u | 5 | 13 | `diode-list.md` |
| `K20.3` | Direnç BOM'u | 5 | 12 | `resistor-list.md` |
| `K20.4` | Kondansatör BOM'u | 5 | 11 | `capacitor-list.md` |
| `K20.5` | Bobin BOM'u | 5 | 12 | `inductor-list.md` |
| `K20.6` | IC BOM'u | 5 | 13 | `ic-list.md` |
| `K20.7` | Konnektör BOM'u | 5 | 14 | `connector-list.md` |
| `K20.8` | Maliyet & Üretim | 5 | 34 | `cost-estimation.md`, `production-tools.md` |

### K20.1 — Transistör BOM'u

**Sorumluluk:** MJL21194/93 çıkış, BD139/140 sürücü ve sinyal transistörleri; seçim gerekçeleri ve tedarikçi.
**Kanıt dosyaları:** `transistor-list.md` — havuz 11 başlık, kullanıldı 11.

#### K20.1.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC güç amplifikatöründe kullanılan transistörler,Class AB ve Class D topolojileri için seçilmiştir. Çıkış transistörleri, sürücü transistörleri ve…
- **Kanıt:** `transistor-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.1.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC güç amplifikatöründe kullanılan transistörler,Class AB ve Class D topolojileri için seçilmiştir. Çıkış transistörleri, sürücü transistörleri ve regülatör transistörleri olmak üzere üç ana…
  - Kanıt: `transistor-list.md` § Genel Bakış

#### K20.1.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — transistor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `transistor-list.md` § Bileşen Listesi (4 bölüm başlığı)

- **K20.1.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Bileşen Listesi
- **K20.1.2.3 — Çıkış Transistörleri (MJL21194/93 Serisi)**
  - Sorumluluk: «Çıkış Transistörleri (MJL21194/93 Serisi)» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Bileşen Listesi > Çıkış Transistörleri (MJL21194/93 Serisi)
- **K20.1.2.4 — Sürücü Transistörleri (BD139/140 Serisi)**
  - Sorumluluk: «Sürücü Transistörleri (BD139/140 Serisi)» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Bileşen Listesi > Sürücü Transistörleri (BD139/140 Serisi)
- **K20.1.2.5 — Sinyal Transistörleri**
  - Sorumluluk: «Sinyal Transistörleri» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Bileşen Listesi > Sinyal Transistörleri

#### K20.1.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — transistor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `transistor-list.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K20.1.3.6 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Seçim Kriterleri
- **K20.1.3.7 — MJL21194/93 Seçim Nedenleri**
  - Sorumluluk: «MJL21194/93 Seçim Nedenleri» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Seçim Kriterleri > MJL21194/93 Seçim Nedenleri
- **K20.1.3.8 — BD139/140 Seçim Nedenleri**
  - Sorumluluk: «BD139/140 Seçim Nedenleri» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Seçim Kriterleri > BD139/140 Seçim Nedenleri

#### K20.1.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — transistor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `transistor-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.1.4.9 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — transistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `transistor-list.md` § Tedarikçi Bilgisi
- **K20.1.4.10 — Bağımlılıklar**
  - Sorumluluk: Soğutma sistemi (K6) - TO-264 ve TO-3P paketleri için heatsink Koruma devresi (K9) - Termal koruma devresi
  - Kanıt: `transistor-list.md` § Bağımlılıklar

#### K20.1.5 — Durum: Implementasyon

- **Sorumluluk:** Tüm transistörler seçilmiş ve doğrulanmıştır. İlk prototip üretiminde test edilmiştir.
- **Kanıt:** `transistor-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.1.5.11 — Durum: Implementasyon**
  - Sorumluluk: Tüm transistörler seçilmiş ve doğrulanmıştır. İlk prototip üretiminde test edilmiştir.
  - Kanıt: `transistor-list.md` § Durum: Implementasyon

### K20.2 — Diyot BOM'u

**Sorumluluk:** Sinyal, güç, Schottky, Zener ve LED diyot listeleri; alternatif karşılaştırmaları.
**Kanıt dosyaları:** `diode-list.md` — havuz 13 başlık, kullanıldı 13.

#### K20.2.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC devresinde kullanılan diyotlar, sinyal yönlendirme, koruma, voltaj regülasyonu ve güç düzeltme amaçlıdır. Her diyot tipi için spesifik parametreler…
- **Kanıt:** `diode-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.2.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC devresinde kullanılan diyotlar, sinyal yönlendirme, koruma, voltaj regülasyonu ve güç düzeltme amaçlıdır. Her diyot tipi için spesifik parametreler ve alternatif tedarikçiler belirlenmiştir.
  - Kanıt: `diode-list.md` § Genel Bakış

#### K20.2.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — diode-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `diode-list.md` § Bileşen Listesi (6 bölüm başlığı)

- **K20.2.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Bileşen Listesi
- **K20.2.2.3 — Sinyal Diyotları**
  - Sorumluluk: «Sinyal Diyotları» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Bileşen Listesi > Sinyal Diyotları
- **K20.2.2.4 — Güç Diyotları**
  - Sorumluluk: «Güç Diyotları» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Bileşen Listesi > Güç Diyotları
- **K20.2.2.5 — Schottky Diyotları**
  - Sorumluluk: «Schottky Diyotları» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Bileşen Listesi > Schottky Diyotları
- **K20.2.2.6 — Zener Diyotları**
  - Sorumluluk: «Zener Diyotları» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Bileşen Listesi > Zener Diyotları
- **K20.2.2.7 — LED Diyotları**
  - Sorumluluk: «LED Diyotları» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Bileşen Listesi > LED Diyotları

#### K20.2.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — diode-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `diode-list.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K20.2.3.8 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Seçim Kriterleri
- **K20.2.3.9 — 1N4148 vs Alternatifleri**
  - Sorumluluk: «1N4148 vs Alternatifleri» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Seçim Kriterleri > 1N4148 vs Alternatifleri
- **K20.2.3.10 — 1N4007 vs Schottky Karşılaştırması**
  - Sorumluluk: «1N4007 vs Schottky Karşılaştırması» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Seçim Kriterleri > 1N4007 vs Schottky Karşılaştırması

#### K20.2.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — diode-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `diode-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.2.4.11 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — diode-list.md dosyasında belgelenen bölüm.
  - Kanıt: `diode-list.md` § Tedarikçi Bilgisi
- **K20.2.4.12 — Bağımlılıklar**
  - Sorumluluk: Güç kaynağı (K3) - Rectifier diyotlar için voltaj Sinyal yolu (K4) - Sinyal diyotları için sinyal akışı
  - Kanıt: `diode-list.md` § Bağımlılıklar

#### K20.2.5 — Durum: Implementasyon

- **Sorumluluk:** Tüm diyotlar seçilmiş ve stokta doğrulanmıştır. SMD ve THT varyantları mevcuttur.
- **Kanıt:** `diode-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.2.5.13 — Durum: Implementasyon**
  - Sorumluluk: Tüm diyotlar seçilmiş ve stokta doğrulanmıştır. SMD ve THT varyantları mevcuttur.
  - Kanıt: `diode-list.md` § Durum: Implementasyon

### K20.3 — Direnç BOM'u

**Sorumluluk:** 0805/1206 SMD, THT ve karşılaştırma dirençleri; metal film vs thick film seçimi.
**Kanıt dosyaları:** `resistor-list.md` — havuz 12 başlık, kullanıldı 12.

#### K20.3.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC devresindeki dirençler, metal film ve kalın film teknolojisi ile üretilmiştir. Tüm dirençler %1 tolerans ve düşük gürültü özelliğine sahiptir. SMD ve…
- **Kanıt:** `resistor-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.3.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC devresindeki dirençler, metal film ve kalın film teknolojisi ile üretilmiştir. Tüm dirençler %1 tolerans ve düşük gürültü özelliğine sahiptir. SMD ve THT varyantları ile komple bir BOM…
  - Kanıt: `resistor-list.md` § Genel Bakış

#### K20.3.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — resistor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `resistor-list.md` § Bileşen Listesi (5 bölüm başlığı)

- **K20.3.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Bileşen Listesi
- **K20.3.2.3 — 0805 SMD Dirençler**
  - Sorumluluk: «0805 SMD Dirençler» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Bileşen Listesi > 0805 SMD Dirençler
- **K20.3.2.4 — 1206 SMD Dirençler (Güç)**
  - Sorumluluk: «1206 SMD Dirençler (Güç)» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Bileşen Listesi > 1206 SMD Dirençler (Güç)
- **K20.3.2.5 — THT Dirençler (Power)**
  - Sorumluluk: «THT Dirençler (Power)» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Bileşen Listesi > THT Dirençler (Power)
- **K20.3.2.6 — Karşılaştırma Dirençleri**
  - Sorumluluk: «Karşılaştırma Dirençleri» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Bileşen Listesi > Karşılaştırma Dirençleri

#### K20.3.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — resistor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `resistor-list.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K20.3.3.7 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Seçim Kriterleri
- **K20.3.3.8 — Metal Film vs Thick Film**
  - Sorumluluk: «Metal Film vs Thick Film» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Seçim Kriterleri > Metal Film vs Thick Film
- **K20.3.3.9 — SMD vs THT Seçimi**
  - Sorumluluk: «SMD vs THT Seçimi» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Seçim Kriterleri > SMD vs THT Seçimi

#### K20.3.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — resistor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `resistor-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.3.4.10 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — resistor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `resistor-list.md` § Tedarikçi Bilgisi
- **K20.3.4.11 — Bağımlılıklar**
  - Sorumluluk: Amplifikatör devresi (K4) - Geri besleme ve kazanç dirençleri Güç kaynağı (K3) - Voltaj bölücü dirençler
  - Kanıt: `resistor-list.md` § Bağımlılıklar

#### K20.3.5 — Durum: Implementasyon

- **Sorumluluk:** Tüm dirençler standardize edilmiş ve toplu alım için fiyatlar negotiated edilmiştir.
- **Kanıt:** `resistor-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.3.5.12 — Durum: Implementasyon**
  - Sorumluluk: Tüm dirençler standardize edilmiş ve toplu alım için fiyatlar negotiated edilmiştir.
  - Kanıt: `resistor-list.md` § Durum: Implementasyon

### K20.4 — Kondansatör BOM'u

**Sorumluluk:** Elektrolitik, MLCC ve film kondansatörler; tip ve paket seçimi kriterleri.
**Kanıt dosyaları:** `capacitor-list.md` — havuz 11 başlık, kullanıldı 11.

#### K20.4.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC devresindeki kondansatörler, elektrolitik, seramik ve film tiplerinden oluşmaktadır. Her tip belirli uygulama alanları için seçilmiştir: güç…
- **Kanıt:** `capacitor-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.4.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC devresindeki kondansatörler, elektrolitik, seramik ve film tiplerinden oluşmaktadır. Her tip belirli uygulama alanları için seçilmiştir: güç filtreleme, sinyal yolundersizasyon ve bypass…
  - Kanıt: `capacitor-list.md` § Genel Bakış

#### K20.4.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — capacitor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `capacitor-list.md` § Bileşen Listesi (4 bölüm başlığı)

- **K20.4.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Bileşen Listesi
- **K20.4.2.3 — Elektrolitik Kondansatörler**
  - Sorumluluk: «Elektrolitik Kondansatörler» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Bileşen Listesi > Elektrolitik Kondansatörler
- **K20.4.2.4 — Seramik Kondansatörler (MLCC)**
  - Sorumluluk: «Seramik Kondansatörler (MLCC)» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Bileşen Listesi > Seramik Kondansatörler (MLCC)
- **K20.4.2.5 — Film Kondansatörler**
  - Sorumluluk: «Film Kondansatörler» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Bileşen Listesi > Film Kondansatörler

#### K20.4.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — capacitor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `capacitor-list.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K20.4.3.6 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Seçim Kriterleri
- **K20.4.3.7 — Kondansatör Tipi Karşılaştırması**
  - Sorumluluk: «Kondansatör Tipi Karşılaştırması» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Seçim Kriterleri > Kondansatör Tipi Karşılaştırması
- **K20.4.3.8 — Package Seçimi**
  - Sorumluluk: «Package Seçimi» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Seçim Kriterleri > Package Seçimi

#### K20.4.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — capacitor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `capacitor-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.4.4.9 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — capacitor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `capacitor-list.md` § Tedarikçi Bilgisi
- **K20.4.4.10 — Bağımlılıklar**
  - Sorumluluk: Güç kaynağı (K3) - Büyük kapasiteli filtre kondansatörleri Amplifikatör devresi (K4) - Sinyal yolu kondansatörleri
  - Kanıt: `capacitor-list.md` § Bağımlılıklar

#### K20.4.5 — Durum: Implementasyon

- **Sorumluluk:** Tüm kondansatörlerin seçimi tamamlanmış ve stok doğrulaması yapılmıştır.
- **Kanıt:** `capacitor-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.4.5.11 — Durum: Implementasyon**
  - Sorumluluk: Tüm kondansatörlerin seçimi tamamlanmış ve stok doğrulaması yapılmıştır.
  - Kanıt: `capacitor-list.md` § Durum: Implementasyon

### K20.5 — Bobin BOM'u

**Sorumluluk:** Class D çıkış filtresi, güç choke, EMI ve sinyal bobinleri; malzeme karşılaştırmaları.
**Kanıt dosyaları:** `inductor-list.md` — havuz 12 başlık, kullanıldı 12.

#### K20.5.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC devresindeki bobinler, çıkış filtresi, güç kaynağı choke ve EMI filtreleme uygulamaları için seçilmiştir. Class D amplifikatör çıkış filtresi, en…
- **Kanıt:** `inductor-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.5.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC devresindeki bobinler, çıkış filtresi, güç kaynağı choke ve EMI filtreleme uygulamaları için seçilmiştir. Class D amplifikatör çıkış filtresi, en kritik bobin uygulamasıdır ve ses…
  - Kanıt: `inductor-list.md` § Genel Bakış

#### K20.5.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — inductor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `inductor-list.md` § Bileşen Listesi (5 bölüm başlığı)

- **K20.5.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Bileşen Listesi
- **K20.5.2.3 — Class D Çıkış Filtresi Bobinleri**
  - Sorumluluk: «Class D Çıkış Filtresi Bobinleri» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Bileşen Listesi > Class D Çıkış Filtresi Bobinleri
- **K20.5.2.4 — Güç Choke Bobinleri**
  - Sorumluluk: «Güç Choke Bobinleri» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Bileşen Listesi > Güç Choke Bobinleri
- **K20.5.2.5 — EMI Filtre Bobinleri**
  - Sorumluluk: «EMI Filtre Bobinleri» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Bileşen Listesi > EMI Filtre Bobinleri
- **K20.5.2.6 — Sinyal Yolu Bobinleri**
  - Sorumluluk: «Sinyal Yolu Bobinleri» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Bileşen Listesi > Sinyal Yolu Bobinleri

#### K20.5.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — inductor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `inductor-list.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K20.5.3.7 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Seçim Kriterleri
- **K20.5.3.8 — Class D Çıkış Filtresi Bobin Karşılaştırması**
  - Sorumluluk: «Class D Çıkış Filtresi Bobin Karşılaştırması» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Seçim Kriterleri > Class D Çıkış Filtresi Bobin Karşılaştırması
- **K20.5.3.9 — Bobin Malzemesi Karşılaştırması**
  - Sorumluluk: «Bobin Malzemesi Karşılaştırması» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Seçim Kriterleri > Bobin Malzemesi Karşılaştırması

#### K20.5.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — inductor-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `inductor-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.5.4.10 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — inductor-list.md dosyasında belgelenen bölüm.
  - Kanıt: `inductor-list.md` § Tedarikçi Bilgisi
- **K20.5.4.11 — Bağımlılıklar**
  - Sorumluluk: Class D amplifikatör (K4) - Çıkış filtresi bobinleri Güç kaynağı (K3) - Choke bobinleri EMI koruma (K5) - EMI filtre bobinleri
  - Kanıt: `inductor-list.md` § Bağımlılıklar

#### K20.5.5 — Durum: Implementasyon

- **Sorumluluk:** Class D çıkış filtresi bobinleri prototip aşamasında test edilmiştir. Ses kalitesi ölçümleri yapılmaktadır.
- **Kanıt:** `inductor-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.5.5.12 — Durum: Implementasyon**
  - Sorumluluk: Class D çıkış filtresi bobinleri prototip aşamasında test edilmiştir. Ses kalitesi ölçümleri yapılmaktadır.
  - Kanıt: `inductor-list.md` § Durum: Implementasyon

### K20.6 — IC BOM'u

**Sorumluluk:** Güç yönetimi, sinyal işleme, amplifikatör IC'leri, MCU/iletişim ve karşılaştırma matrisleri.
**Kanıt dosyaları:** `ic-list.md` — havuz 13 başlık, kullanıldı 13.

#### K20.6.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC devresindeki entegre devreler, güç yönetimi, sinyal işleme ve amplifikasyon fonksiyonlarını gerçekleştirir. Her IC için spesifik parametreler ve…
- **Kanıt:** `ic-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.6.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC devresindeki entegre devreler, güç yönetimi, sinyal işleme ve amplifikasyon fonksiyonlarını gerçekleştirir. Her IC için spesifik parametreler ve alternatif tedarikçiler belirlenmiştir.
  - Kanıt: `ic-list.md` § Genel Bakış

#### K20.6.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — ic-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `ic-list.md` § Bileşen Listesi (5 bölüm başlığı)

- **K20.6.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Bileşen Listesi
- **K20.6.2.3 — Güç Yönetimi IC'leri**
  - Sorumluluk: «Güç Yönetimi IC'leri» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Bileşen Listesi > Güç Yönetimi IC'leri
- **K20.6.2.4 — Sinyal İşleme IC'leri**
  - Sorumluluk: «Sinyal İşleme IC'leri» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Bileşen Listesi > Sinyal İşleme IC'leri
- **K20.6.2.5 — Amplifikatör IC'leri**
  - Sorumluluk: «Amplifikatör IC'leri» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Bileşen Listesi > Amplifikatör IC'leri
- **K20.6.2.6 — Mikrodenetleyici ve Haberleşme**
  - Sorumluluk: «Mikrodenetleyici ve Haberleşme» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Bileşen Listesi > Mikrodenetleyici ve Haberleşme

#### K20.6.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — ic-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `ic-list.md` § Seçim Kriterleri (4 bölüm başlığı)

- **K20.6.3.7 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Seçim Kriterleri
- **K20.6.3.8 — Op-Amp Karşılaştırması**
  - Sorumluluk: «Op-Amp Karşılaştırması» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Seçim Kriterleri > Op-Amp Karşılaştırması
- **K20.6.3.9 — Class D Amplifikatör Karşılaştırması**
  - Sorumluluk: «Class D Amplifikatör Karşılaştırması» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Seçim Kriterleri > Class D Amplifikatör Karşılaştırması
- **K20.6.3.10 — DAC Karşılaştırması**
  - Sorumluluk: «DAC Karşılaştırması» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Seçim Kriterleri > DAC Karşılaştırması

#### K20.6.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — ic-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `ic-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.6.4.11 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — ic-list.md dosyasında belgelenen bölüm.
  - Kanıt: `ic-list.md` § Tedarikçi Bilgisi
- **K20.6.4.12 — Bağımlılıklar**
  - Sorumluluk: Güç kaynağı (K3) - Besleme gerilimleri Sinyal yolu (K4) - Girdi/çıktı bağlantıları Konnektörler (K8) - Ses girişi/çıkışı
  - Kanıt: `ic-list.md` § Bağımlılıklar

#### K20.6.5 — Durum: Implementasyon

- **Sorumluluk:** Tüm IC'lerin seçimi tamamlanmış ve ilk prototiplerde test edilmiştir.
- **Kanıt:** `ic-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.6.5.13 — Durum: Implementasyon**
  - Sorumluluk: Tüm IC'lerin seçimi tamamlanmış ve ilk prototiplerde test edilmiştir.
  - Kanıt: `ic-list.md` § Durum: Implementasyon

### K20.7 — Konnektör BOM'u

**Sorumluluk:** Profesyonel/tüketici ses, USB, güç, veri ve mekanik konnektörler; XLR/USB-C karşılaştırmaları.
**Kanıt dosyaları:** `connector-list.md` — havuz 14 başlık, kullanıldı 14.

#### K20.7.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC donanımında kullanılan konnektörler, profesyonel ses, tüketici elektroniği ve veri iletişimi için seçilmiştir. Her konnektör tipi belirli uygulama…
- **Kanıt:** `connector-list.md` § Genel Bakış (1 bölüm başlığı)

- **K20.7.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC donanımında kullanılan konnektörler, profesyonel ses, tüketici elektroniği ve veri iletişimi için seçilmiştir. Her konnektör tipi belirli uygulama alanları için optimize edilmiştir.
  - Kanıt: `connector-list.md` § Genel Bakış

#### K20.7.2 — Bileşen Listesi

- **Sorumluluk:** «Bileşen Listesi» — connector-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `connector-list.md` § Bileşen Listesi (7 bölüm başlığı)

- **K20.7.2.2 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi
- **K20.7.2.3 — Profesyonel Ses Konnektörleri**
  - Sorumluluk: «Profesyonel Ses Konnektörleri» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi > Profesyonel Ses Konnektörleri
- **K20.7.2.4 — Tüketici Ses Konnektörleri**
  - Sorumluluk: «Tüketici Ses Konnektörleri» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi > Tüketici Ses Konnektörleri
- **K20.7.2.5 — USB Konnektörleri**
  - Sorumluluk: «USB Konnektörleri» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi > USB Konnektörleri
- **K20.7.2.6 — Güç Konnektörleri**
  - Sorumluluk: «Güç Konnektörleri» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi > Güç Konnektörleri
- **K20.7.2.7 — Veri Konnektörleri**
  - Sorumluluk: «Veri Konnektörleri» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi > Veri Konnektörleri
- **K20.7.2.8 — Mekanik Konnektörler**
  - Sorumluluk: «Mekanik Konnektörler» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Bileşen Listesi > Mekanik Konnektörler

#### K20.7.3 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — connector-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `connector-list.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K20.7.3.9 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Seçim Kriterleri
- **K20.7.3.10 — XLR Konnektör Karşılaştırması**
  - Sorumluluk: «XLR Konnektör Karşılaştırması» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Seçim Kriterleri > XLR Konnektör Karşılaştırması
- **K20.7.3.11 — USB-C Konnektör Karşılaştırması**
  - Sorumluluk: «USB-C Konnektör Karşılaştırması» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Seçim Kriterleri > USB-C Konnektör Karşılaştırması

#### K20.7.4 — Tedarikçi Bilgisi / Bağımlılıklar

- **Sorumluluk:** «Tedarikçi Bilgisi» — connector-list.md dosyasında belgelenen bölüm.
- **Kanıt:** `connector-list.md` § Tedarikçi Bilgisi / Bağımlılıklar (2 bölüm başlığı)

- **K20.7.4.12 — Tedarikçi Bilgisi**
  - Sorumluluk: «Tedarikçi Bilgisi» — connector-list.md dosyasında belgelenen bölüm.
  - Kanıt: `connector-list.md` § Tedarikçi Bilgisi
- **K20.7.4.13 — Bağımlılıklar**
  - Sorumluluk: PCB tasarımı (K7) - Konnektör footprint'leri Kasa tasarımı (K6) - Panel delikleri Kablo tasarımı (K9) - Kablo demetleri
  - Kanıt: `connector-list.md` § Bağımlılıklar

#### K20.7.5 — Durum: Implementasyon

- **Sorumluluk:** Tüm konnektörlerin seçimi tamamlanmış ve mekanik uyumluluk doğrulanmıştır.
- **Kanıt:** `connector-list.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K20.7.5.14 — Durum: Implementasyon**
  - Sorumluluk: Tüm konnektörlerin seçimi tamamlanmış ve mekanik uyumluluk doğrulanmıştır.
  - Kanıt: `connector-list.md` § Durum: Implementasyon

### K20.8 — Maliyet & Üretim

**Sorumluluk:** BOM maliyet özeti, hacim/risk/ROI analizi; pick & place, reflow, FAI/SPC kalite süreçleri.
**Kanıt dosyaları:** `cost-estimation.md`, `production-tools.md` — havuz 35 başlık, kullanıldı 34, seçim dışı 1.

#### K20.8.1 — Genel Bakış / BOM Maliyet Özeti / Detaylı Maliyet Analizi

- **Sorumluluk:** Bu doküman COREMUSIC projesinin BOM maliyet analizini ve üretim maliyet tahminlerini içerir. Analiz, birim başı maliyet, toplam proje maliyeti ve farklı üretim…
- **Kanıt:** `cost-estimation.md`, `production-tools.md` § Genel Bakış / BOM Maliyet Özeti / Detaylı Maliyet Analizi (9 bölüm başlığı)

- **K20.8.1.1 — Genel Bakış**
  - Sorumluluk: Bu doküman COREMUSIC projesinin BOM maliyet analizini ve üretim maliyet tahminlerini içerir. Analiz, birim başı maliyet, toplam proje maliyeti ve farklı üretim hacimlerine göre maliyet…
  - Kanıt: `cost-estimation.md` § Genel Bakış
- **K20.8.1.2 — Genel Bakış**
  - Sorumluluk: Bu doküman COREMUSIC donanımının seri üretimi için gerekli üretim araçlarını, ekipmanları ve süreçlerini tanımlar. Pick-and-place, reflow lehimleme, test ve kalite kontrol prosedürleri kapsamlı…
  - Kanıt: `production-tools.md` § Genel Bakış
- **K20.8.1.3 — BOM Maliyet Özeti**
  - Sorumluluk: «BOM Maliyet Özeti» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § BOM Maliyet Özeti
- **K20.8.1.4 — Bileşen Kategorisi Bazında Maliyet**
  - Sorumluluk: «Bileşen Kategorisi Bazında Maliyet» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § BOM Maliyet Özeti > Bileşen Kategorisi Bazında Maliyet
- **K20.8.1.5 — Üretim Hacmi Bazında Maliyet**
  - Sorumluluk: «Üretim Hacmi Bazında Maliyet» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § BOM Maliyet Özeti > Üretim Hacmi Bazında Maliyet
- **K20.8.1.6 — Detaylı Maliyet Analizi**
  - Sorumluluk: «Detaylı Maliyet Analizi» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Detaylı Maliyet Analizi
- **K20.8.1.7 — PCB Maliyeti**
  - Sorumluluk: «PCB Maliyeti» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Detaylı Maliyet Analizi > PCB Maliyeti
- **K20.8.1.8 — Üretim İşçilik Maliyeti**
  - Sorumluluk: «Üretim İşçilik Maliyeti» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Detaylı Maliyet Analizi > Üretim İşçilik Maliyeti
- **K20.8.1.9 — Test ve Kalite Maliyeti**
  - Sorumluluk: «Test ve Kalite Maliyeti» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Detaylı Maliyet Analizi > Test ve Kalite Maliyeti

#### K20.8.2 — Maliyet Optimizasyonu / Risk Maliyetleri / ROI Analizi / Durum: Implementasyon

- **Sorumluluk:** «Maliyet Optimizasyonu» — cost-estimation.md dosyasında belgelenen bölüm.
- **Kanıt:** `cost-estimation.md` § Maliyet Optimizasyonu / Risk Maliyetleri / ROI Analizi / Durum: Implementasyon (6 bölüm başlığı)

- **K20.8.2.10 — Maliyet Optimizasyonu**
  - Sorumluluk: «Maliyet Optimizasyonu» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Maliyet Optimizasyonu
- **K20.8.2.11 — Tasarım Optimizasyonları**
  - Sorumluluk: «Tasarım Optimizasyonları» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Maliyet Optimizasyonu > Tasarım Optimizasyonları
- **K20.8.2.12 — Toplam Tasarruf Potansiyeli**
  - Sorumluluk: «Toplam Tasarruf Potansiyeli» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Maliyet Optimizasyonu > Toplam Tasarruf Potansiyeli
- **K20.8.2.13 — Risk Maliyetleri**
  - Sorumluluk: «Risk Maliyetleri» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § Risk Maliyetleri
- **K20.8.2.14 — ROI Analizi**
  - Sorumluluk: «ROI Analizi» — cost-estimation.md dosyasında belgelenen bölüm.
  - Kanıt: `cost-estimation.md` § ROI Analizi
- **K20.8.2.15 — Durum: Implementasyon**
  - Sorumluluk: Maliyet analizi tamamlanmış ve onaylanmıştır. Üretim hacmine göre fiyatlandırma stratejisi belirlenmiştir.
  - Kanıt: `cost-estimation.md` § Durum: Implementasyon

#### K20.8.3 — Üretim Hattı Ekipmanları

- **Sorumluluk:** ###₺ Test Ekipmanları
- **Kanıt:** `production-tools.md` § Üretim Hattı Ekipmanları (4 bölüm başlığı)

- **K20.8.3.16 — Üretim Hattı Ekipmanları**
  - Sorumluluk: ###₺ Test Ekipmanları
  - Kanıt: `production-tools.md` § Üretim Hattı Ekipmanları
- **K20.8.3.17 — Pick & Place Makineleri**
  - Sorumluluk: «Pick & Place Makineleri» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Hattı Ekipmanları > Pick & Place Makineleri
- **K20.8.3.18 — Reflow Lehimleme**
  - Sorumluluk: ###₺ Test Ekipmanları
  - Kanıt: `production-tools.md` § Üretim Hattı Ekipmanları > Reflow Lehimleme
- **K20.8.3.19 — Lehimleme İstasyonları**
  - Sorumluluk: «Lehimleme İstasyonları» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Hattı Ekipmanları > Lehimleme İstasyonları

#### K20.8.4 — Üretim Süreci

- **Sorumluluk:** «Üretim Süreci» — production-tools.md dosyasında belgelenen bölüm.
- **Kanıt:** `production-tools.md` § Üretim Süreci (6 bölüm başlığı)

- **K20.8.4.20 — Üretim Süreci**
  - Sorumluluk: «Üretim Süreci» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Süreci
- **K20.8.4.21 — Adım 1: PCB Hazırlık**
  - Sorumluluk: «Adım 1: PCB Hazırlık» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Süreci > Adım 1: PCB Hazırlık
- **K20.8.4.22 — Adım 2: SMD Lehimleme**
  - Sorumluluk: «Adım 2: SMD Lehimleme» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Süreci > Adım 2: SMD Lehimleme
- **K20.8.4.23 — Adım 3: Through-Hole**
  - Sorumluluk: «Adım 3: Through-Hole» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Süreci > Adım 3: Through-Hole
- **K20.8.4.24 — Adım 4: Test**
  - Sorumluluk: «Adım 4: Test» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Süreci > Adım 4: Test
- **K20.8.4.25 — Adım 5: Montaj**
  - Sorumluluk: «Adım 5: Montaj» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Süreci > Adım 5: Montaj

#### K20.8.5 — Kalite Kontrol Prosedürleri (+3 bölüm)

- **Sorumluluk:** «Kalite Kontrol Prosedürleri» — production-tools.md dosyasında belgelenen bölüm.
- **Kanıt:** `production-tools.md` § Kalite Kontrol Prosedürleri (+3 bölüm) (9 bölüm başlığı)

- **K20.8.5.26 — Kalite Kontrol Prosedürleri**
  - Sorumluluk: «Kalite Kontrol Prosedürleri» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Kalite Kontrol Prosedürleri
- **K20.8.5.27 — First Article Inspection (FAI)**
  - Sorumluluk: «First Article Inspection (FAI)» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Kalite Kontrol Prosedürleri > First Article Inspection (FAI)
- **K20.8.5.28 — SPC (Statistical Process Control)**
  - Sorumluluk: «SPC (Statistical Process Control)» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Kalite Kontrol Prosedürleri > SPC (Statistical Process Control)
- **K20.8.5.29 — İstatistiksel Analiz**
  - Sorumluluk: «İstatistiksel Analiz» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Kalite Kontrol Prosedürleri > İstatistiksel Analiz
- **K20.8.5.30 — Üretim Takip Sistemi**
  - Sorumluluk: «Üretim Takip Sistemi» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Takip Sistemi
- **K20.8.5.31 — PCB Seri Numaralandırma**
  - Sorumluluk: «PCB Seri Numaralandırma» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Takip Sistemi > PCB Seri Numaralandırma
- **K20.8.5.32 — Izlenebilirlik Matrisi**
  - Sorumluluk: «Izlenebilirlik Matrisi» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Takip Sistemi > Izlenebilirlik Matrisi
- **K20.8.5.33 — Üretim Maliyeti Dağılımı**
  - Sorumluluk: «Üretim Maliyeti Dağılımı» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Üretim Maliyeti Dağılımı
- **K20.8.5.34 — Bakım ve Kalibrasyon**
  - Sorumluluk: «Bakım ve Kalibrasyon» — production-tools.md dosyasında belgelenen bölüm.
  - Kanıt: `production-tools.md` § Bakım ve Kalibrasyon

---

## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k20-bom/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayımın hangi kısmını desteklediğini listeler. 9 kanonik katman MD'si + index.md + README.md + CLAUDE.md.

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `transistor-list.md` | Transistör Listesi | `K20.1` alanı (1), 5 alt alan, 11 yaprak → `K20` toplam 160 içine katkı |
| 2 | `diode-list.md` | Diyot Listesi | `K20.2` alanı (1), 5 alt alan, 13 yaprak → `K20` toplam 160 içine katkı |
| 3 | `resistor-list.md` | Direnç Listesi | `K20.3` alanı (1), 5 alt alan, 12 yaprak → `K20` toplam 160 içine katkı |
| 4 | `capacitor-list.md` | Kondansatör Listesi | `K20.4` alanı (1), 5 alt alan, 11 yaprak → `K20` toplam 160 içine katkı |
| 5 | `inductor-list.md` | Bobin Listesi | `K20.5` alanı (1), 5 alt alan, 12 yaprak → `K20` toplam 160 içine katkı |
| 6 | `ic-list.md` | IC Listesi | `K20.6` alanı (1), 5 alt alan, 13 yaprak → `K20` toplam 160 içine katkı |
| 7 | `connector-list.md` | Konnektör Listesi | `K20.7` alanı (1), 5 alt alan, 14 yaprak → `K20` toplam 160 içine katkı |
| 8 | `cost-estimation.md` | Maliyet Tahmini | `K20.8` alanı (1), 2 alt alan, 14 yaprak → `K20` toplam 160 içine katkı |
| 9 | `production-tools.md` | Üretim Araçları | `K20.8` alanı (1), 4 alt alan, 20 yaprak → `K20` toplam 160 içine katkı |
| 10 | `index.md` | Katman ana sayfası: diyagram, tablolar, bağımlılıklar | `K20.a) alan tanımı bağlamı |
| 11 | `README.md` | Katman künyesi ve bu şema/katalog | `K20.a`/`K20.a.b` doğrulaması |
| 12 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |

**Sayım dayanağı:** 9 dosyadan çıkarılan 121 H2/H3 başlığı içinden hedef c=120 için 1 başlık orantılı olarak seçimin dışında bırakıldı (aşağıda); alan/alt alan sayıları a=8, b=5 ile kilitlidir.

### Seçim Dışı Kanıtlar

| Başlık | Dosya | Neden |
|--------|-------|-------|
| Durum: Implementasyon | `production-tools.md` | hedef c=120 aşıldı; havuz 121 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |

*K20 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
