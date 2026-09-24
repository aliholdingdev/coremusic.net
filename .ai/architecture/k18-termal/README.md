---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K18 Termal Tasarım Layer"
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

# K18: Termal Tasarım Layer

**Katman:** K18 (Termal Tasarım)
**Kapsam:** Heatsink, heat pipe, termal ped, PWM fan, kasa havalandırma, ortam sıcaklığı, KSD301 cutoff, CFD simülasyon
**Sorumlu Agent:** Audio Hardware Engineer / Thermal Engineer
**Bileşen Sayısı:** 9 kanonik katman MD'si
**Bağımlılık:** K18 ↔ K1 — bağımsız üretim katmanı (matris §2.2)

---

## 1. Genel Bakış

K18, donanım platformlarının sıcaklık yönetimi için pasif soğutma (heatsink, termal ped, heat pipe, kasa), aktif soğutma (PWM fan), KSD301 donanım koruması ve CFD tabanlı simülasyon/doğrulama çerçevesini bir arada tanımlar. Hedef, 0–40°C çalışma aralığında bileşenleri güvenli sıcaklıkta tutmaktır. Isı kaynakları ve akış diyagramı `index.md` dosyasındadır.

---

## 2. Kaynak Dökümanlar

- `index.md` — katman ana sayfası (mimari diyagram, tablolar, bağımlılıklar)
- `CLAUDE.md` — katman kural ve kapsam notları
- Alt katman şeması ve kanıt kataloğu bu README'ye 2026-09-24'te eklenmiştir (son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması).


---

## Alt Katman Şeması (K18.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K18 katmanını onaylı şema biçiminde (K18 → K18.a → K18.a.b → K18.a.b.c) belgeler. Alanlar (a) soğutma/koruma bileşen aileleri, alt alanlar (b) dosyalardaki H2 bölüm başlıkları, yapraklar (c) k18-termal/ kanonik MD dosyalarındaki gerçek H2/H3 bölüm başlıklarından türetilmiştir; her yaprak kanıt satırıyla kaynak dosya ve bölümünü gösterir. Uydurma düğüm yoktur.

**Şema kuralları:**

1. Zorunlu şema: `K18` → `K18.a` → `K18.a.b`; seviye-4 (`K18.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K16–K20 beş BAĞIMSIZ üretim katmanıdır; hiyerarşi yok, birbirine alt değildir (matris §1.1, §1.3 K3, §5.2 #21).
5. Bağımlılık bağlamı: K18 ↔ K1 (çift yönlü; K18 bağımsız üretim katmanı — matris §2.2). K18 → K0 yasaktır (matris §5.1 #12); K18'in diğer üretim katmanlarına bağımlılığı YOKTUR (matris §5.2 #21).
6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K18` (a alan) | 8 | 8 | 8 | +0 |
| `K18.a.b` (a×b alt alan) | 40 | 40 | — | 0 |
| `K18.a.b.c` (c yaprak) | 130 | 130 | 202 | +72 |
| **Toplam düğüm** | **170** | **170** | **242** | **+72** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K18.1` | Fischer Heatsink | 5 | 14 | `fischer-heatsink.md` |
| `K18.2` | Heat Pipe Tasarımı | 5 | 14 | `heat-pipe-design.md` |
| `K18.3` | Termal Ped (TIM) | 5 | 15 | `thermal-pad.md` |
| `K18.4` | PWM Fan Kontrolü | 5 | 14 | `pwm-fan-control.md` |
| `K18.5` | Kasa & Havalandırma | 5 | 15 | `enclosure-thermal.md` |
| `K18.6` | Ortam Sıcaklığı | 5 | 14 | `ambient-temperature.md` |
| `K18.7` | KSD301 Termal Cutoff | 5 | 15 | `ksd301-cutoff.md` |
| `K18.8` | Hesaplama & Simülasyon | 5 | 29 | `thermal-resistance.md`, `thermal-simulation.md` |

### K18.1 — Fischer Heatsink

**Sorumluluk:** Fischer SK53 pasif soğutma: termal direnç hesapları, fiziksel/termal özellikler, montaj ve alternatifler.
**Kanıt dosyaları:** `fischer-heatsink.md` — havuz 22 başlık, kullanıldı 14, seçim dışı 8.

#### K18.1.1 — Genel Bakış

- **Sorumluluk:** Fischer SK53 serisi heatsink, COREMUSIC platformunda yüksek güçlü CPU ve amplifikatör bileşenleri için pasif soğutma çözümü olarak seçilmiştir. Alüminyum…
- **Kanıt:** `fischer-heatsink.md` § Genel Bakış (1 bölüm başlığı)

- **K18.1.1.1 — Genel Bakış**
  - Sorumluluk: Fischer SK53 serisi heatsink, COREMUSIC platformunda yüksek güçlü CPU ve amplifikatör bileşenleri için pasif soğutma çözümü olarak seçilmiştir. Alüminyum ekstrüzyon yapısı ve optimize edilmiş kanat…
  - Kanıt: `fischer-heatsink.md` § Genel Bakış

#### K18.1.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — fischer-heatsink.md dosyasında belgelenen bölüm.
- **Kanıt:** `fischer-heatsink.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.1.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Termal Hesaplamalar
- **K18.1.2.3 — Temel Formüller**
  - Sorumluluk: «Temel Formüller» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Termal Hesaplamalar > Temel Formüller
- **K18.1.2.4 — SK53 Thermal Resistance Değerleri**
  - Sorumluluk: «SK53 Thermal Resistance Değerleri» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Termal Hesaplamalar > SK53 Thermal Resistance Değerleri
- **K18.1.2.5 — Sıcaklık Hesap Örneği**
  - Sorumluluk: «Sıcaklık Hesap Örneği» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Termal Hesaplamalar > Sıcaklık Hesap Örneği

#### K18.1.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — fischer-heatsink.md dosyasında belgelenen bölüm.
- **Kanıt:** `fischer-heatsink.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K18.1.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Teknik Spesifikasyonlar
- **K18.1.3.7 — Fiziksel Özellikler**
  - Sorumluluk: «Fiziksel Özellikler» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Teknik Spesifikasyonlar > Fiziksel Özellikler
- **K18.1.3.8 — Termal Özellikler**
  - Sorumluluk: «Termal Özellikler» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Teknik Spesifikasyonlar > Termal Özellikler
- **K18.1.3.9 — Montaj Özellikleri**
  - Sorumluluk: «Montaj Özellikleri» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Teknik Spesifikasyonlar > Montaj Özellikleri

#### K18.1.4 — Seçim Kriterleri

- **Sorumluluk:** Alüminyum 6063-T5: Yüksek iletim katsayısı, düşük maliyet, kolay işlenebilirlik Optimize Kanat Geometrysi: Doğal konveksiyon için ideal kanat aralığı (3.5mm)
- **Kanıt:** `fischer-heatsink.md` § Seçim Kriterleri (4 bölüm başlığı)

- **K18.1.4.10 — Seçim Kriterleri**
  - Sorumluluk: Alüminyum 6063-T5: Yüksek iletim katsayısı, düşük maliyet, kolay işlenebilirlik Optimize Kanat Geometrysi: Doğal konveksiyon için ideal kanat aralığı (3.5mm)
  - Kanıt: `fischer-heatsink.md` § Seçim Kriterleri
- **K18.1.4.11 — Fischer SK53 Seçim Nedenleri**
  - Sorumluluk: Alüminyum 6063-T5: Yüksek iletim katsayısı, düşük maliyet, kolay işlenebilirlik Optimize Kanat Geometrysi: Doğal konveksiyon için ideal kanat aralığı (3.5mm)
  - Kanıt: `fischer-heatsink.md` § Seçim Kriterleri > Fischer SK53 Seçim Nedenleri
- **K18.1.4.12 — Alternatif Karşılaştırma**
  - Sorumluluk: «Alternatif Karşılaştırma» — fischer-heatsink.md dosyasında belgelenen bölüm.
  - Kanıt: `fischer-heatsink.md` § Seçim Kriterleri > Alternatif Karşılaştırma
- **K18.1.4.13 — Sınır Koşulları**
  - Sorumluluk: Maksimum çalışma sıcaklığı: 150°C (alüminyum erime: 660°C) Minimum sıcaklık: -40°C (malzeme dayanımı)
  - Kanıt: `fischer-heatsink.md` § Seçim Kriterleri > Sınır Koşulları

#### K18.1.5 — Bağımlılıklar

- **Sorumluluk:** K01 Donanım: CPU ve amplifikatör yerleşim planı K17 Güç Yönetimi: Bileşen güç tüketimi profilleri
- **Kanıt:** `fischer-heatsink.md` § Bağımlılıklar (1 bölüm başlığı)

- **K18.1.5.14 — Bağımlılıklar**
  - Sorumluluk: K01 Donanım: CPU ve amplifikatör yerleşim planı K17 Güç Yönetimi: Bileşen güç tüketimi profilleri
  - Kanıt: `fischer-heatsink.md` § Bağımlılıklar

### K18.2 — Heat Pipe Tasarımı

**Sorumluluk:** Isı borusu prensibi, çap/konfigürasyon seçimi, ısı pipe sayısı hesabı ve montaj/bakım prosedürü.
**Kanıt dosyaları:** `heat-pipe-design.md` — havuz 22 başlık, kullanıldı 14, seçim dışı 8.

#### K18.2.1 — Genel Bakış

- **Sorumluluk:** Heat pipe (ısı borusu), faz değiştirme prensibiyle çalışan yüksek etkinlikli pasif ısı iletim cihazıdır. COREMUSIC platformunda, kompakt tasarımlarda ve yüksek…
- **Kanıt:** `heat-pipe-design.md` § Genel Bakış (1 bölüm başlığı)

- **K18.2.1.1 — Genel Bakış**
  - Sorumluluk: Heat pipe (ısı borusu), faz değiştirme prensibiyle çalışan yüksek etkinlikli pasif ısı iletim cihazıdır. COREMUSIC platformunda, kompakt tasarımlarda ve yüksek güç yoğunluklu bileşenler için ısı…
  - Kanıt: `heat-pipe-design.md` § Genel Bakış

#### K18.2.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — heat-pipe-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `heat-pipe-design.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.2.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Termal Hesaplamalar
- **K18.2.2.3 — Heat Pipe Temel Prensibi**
  - Sorumluluk: «Heat Pipe Temel Prensibi» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Termal Hesaplamalar > Heat Pipe Temel Prensibi
- **K18.2.2.4 — Heat Pipe Performansı**
  - Sorumluluk: «Heat Pipe Performansı» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Termal Hesaplamalar > Heat Pipe Performansı
- **K18.2.2.5 — Termal Direnç Karşılaştırması**
  - Sorumluluk: «Termal Direnç Karşılaştırması» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Termal Hesaplamalar > Termal Direnç Karşılaştırması

#### K18.2.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — heat-pipe-design.md dosyasında belgelenen bölüm.
- **Kanıt:** `heat-pipe-design.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K18.2.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Teknik Spesifikasyonlar
- **K18.2.3.7 — Seçilen Heat Pipe: Aavid Thermalright Heat Pipe**
  - Sorumluluk: «Seçilen Heat Pipe: Aavid Thermalright Heat Pipe» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Teknik Spesifikasyonlar > Seçilen Heat Pipe: Aavid Thermalright Heat Pipe
- **K18.2.3.8 — Heat Pipe Çap Seçimi**
  - Sorumluluk: «Heat Pipe Çap Seçimi» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Teknik Spesifikasyonlar > Heat Pipe Çap Seçimi
- **K18.2.3.9 — Heat Pipe Konfigürasyonları**
  - Sorumluluk: «Heat Pipe Konfigürasyonları» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Teknik Spesifikasyonlar > Heat Pipe Konfigürasyonları

#### K18.2.4 — Seçim Kriterleri

- **Sorumluluk:** Yüksek Etkin İletim: 150,000 W/m·K (bakırın 375 katı) Pasif Çalışma: Enerji gerektirmez, bakım yok
- **Kanıt:** `heat-pipe-design.md` § Seçim Kriterleri (4 bölüm başlığı)

- **K18.2.4.10 — Seçim Kriterleri**
  - Sorumluluk: Yüksek Etkin İletim: 150,000 W/m·K (bakırın 375 katı) Pasif Çalışma: Enerji gerektirmez, bakım yok
  - Kanıt: `heat-pipe-design.md` § Seçim Kriterleri
- **K18.2.4.11 — Heat Pipe Seçim Nedenleri**
  - Sorumluluk: Yüksek Etkin İletim: 150,000 W/m·K (bakırın 375 katı) Pasif Çalışma: Enerji gerektirmez, bakım yok
  - Kanıt: `heat-pipe-design.md` § Seçim Kriterleri > Heat Pipe Seçim Nedenleri
- **K18.2.4.12 — Isı Pipe Sayısı Hesabı**
  - Sorumluluk: «Isı Pipe Sayısı Hesabı» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Seçim Kriterleri > Isı Pipe Sayısı Hesabı
- **K18.2.4.13 — Uygulama Senaryoları**
  - Sorumluluk: «Uygulama Senaryoları» — heat-pipe-design.md dosyasında belgelenen bölüm.
  - Kanıt: `heat-pipe-design.md` § Seçim Kriterleri > Uygulama Senaryoları

#### K18.2.5 — Bağımlılıklar

- **Sorumluluk:** K01 Donanım: Bileşen yerleşim planı ve boşluklar K17 Güç Yönetimi: Bileşen güç tüketimi K18 Heatsink: Heatsink taban tasarımı
- **Kanıt:** `heat-pipe-design.md` § Bağımlılıklar (1 bölüm başlığı)

- **K18.2.5.14 — Bağımlılıklar**
  - Sorumluluk: K01 Donanım: Bileşen yerleşim planı ve boşluklar K17 Güç Yönetimi: Bileşen güç tüketimi K18 Heatsink: Heatsink taban tasarımı
  - Kanıt: `heat-pipe-design.md` § Bağımlılıklar

### K18.3 — Termal Ped (TIM)

**Sorumluluk:** Termal ped kalınlığı ve iletkenliği; mekanik, çevresel ve elektriksel dayanım seçim kriterleri.
**Kanıt dosyaları:** `thermal-pad.md` — havuz 23 başlık, kullanıldı 15, seçim dışı 8.

#### K18.3.1 — Genel Bakış

- **Sorumluluk:** Termal ped (thermal pad), ısı kaynağı ile soğutucu arasında termal arayüz malzemesi (TIM) olarak görev yapar. COREMUSIC platformunda, CPU, GPU ve amplifikatör…
- **Kanıt:** `thermal-pad.md` § Genel Bakış (1 bölüm başlığı)

- **K18.3.1.1 — Genel Bakış**
  - Sorumluluk: Termal ped (thermal pad), ısı kaynağı ile soğutucu arasında termal arayüz malzemesi (TIM) olarak görev yapar. COREMUSIC platformunda, CPU, GPU ve amplifikatör bileşenleri için seçilen termal pedler,…
  - Kanıt: `thermal-pad.md` § Genel Bakış

#### K18.3.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — thermal-pad.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-pad.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.3.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Termal Hesaplamalar
- **K18.3.2.3 — Temel Formül**
  - Sorumluluk: «Temel Formül» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Termal Hesaplamalar > Temel Formül
- **K18.3.2.4 — Pratik Hesaplama**
  - Sorumluluk: «Pratik Hesaplama» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Termal Hesaplamalar > Pratik Hesaplama
- **K18.3.2.5 — Sıcaklık Düşüşü**
  - Sorumluluk: «Sıcaklık Düşüşü» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Termal Hesaplamalar > Sıcaklık Düşüşü

#### K18.3.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — thermal-pad.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-pad.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K18.3.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Teknik Spesifikasyonlar
- **K18.3.3.7 — Seçilen Termal Ped: Thermal GrizzlyMinus Pad 8**
  - Sorumluluk: «Seçilen Termal Ped: Thermal GrizzlyMinus Pad 8» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Teknik Spesifikasyonlar > Seçilen Termal Ped: Thermal GrizzlyMinus Pad 8
- **K18.3.3.8 — Karşılaştırma Tablosu**
  - Sorumluluk: «Karşılaştırma Tablosu» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Teknik Spesifikasyonlar > Karşılaştırma Tablosu
- **K18.3.3.9 — Ped Kalınlığı Seçimi**
  - Sorumluluk: «Ped Kalınlığı Seçimi» — thermal-pad.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-pad.md` § Teknik Spesifikasyonlar > Ped Kalınlığı Seçimi

#### K18.3.4 — Seçim Kriterleri

- **Sorumluluk:** Yüksek Termal İletim: ≥ 5 W/m·K (minimum), ≥ 8 W/m·K (tercih) Düşük Thermal Direnç: ≤ 0.2°C/W (25mm × 25mm alan için)
- **Kanıt:** `thermal-pad.md` § Seçim Kriterleri (5 bölüm başlığı)

- **K18.3.4.10 — Seçim Kriterleri**
  - Sorumluluk: Yüksek Termal İletim: ≥ 5 W/m·K (minimum), ≥ 8 W/m·K (tercih) Düşük Thermal Direnç: ≤ 0.2°C/W (25mm × 25mm alan için)
  - Kanıt: `thermal-pad.md` § Seçim Kriterleri
- **K18.3.4.11 — Termal Performans**
  - Sorumluluk: Yüksek Termal İletim: ≥ 5 W/m·K (minimum), ≥ 8 W/m·K (tercih) Düşük Thermal Direnç: ≤ 0.2°C/W (25mm × 25mm alan için)
  - Kanıt: `thermal-pad.md` § Seçim Kriterleri > Termal Performans
- **K18.3.4.12 — Mekanik Özellikler**
  - Sorumluluk: Düşük Sertlik: Yüzey bozulması olmadan sıkıştırma Esneklik: Yüzey pürüzlülüğünü doldurma yeteneği
  - Kanıt: `thermal-pad.md` § Seçim Kriterleri > Mekanik Özellikler
- **K18.3.4.13 — Çevresel Dayanım**
  - Sorumluluk: Sıcaklık Aralığı: -50°C ile +200°C Nem Dayanımı: %100 RH (condensation olmayan) Yaşlanma Direnci: Termal döngü testi (1000 döngü)
  - Kanıt: `thermal-pad.md` § Seçim Kriterleri > Çevresel Dayanım
- **K18.3.4.14 — Elektriksel Özellikler**
  - Sorumluluk: Yalıtkanlık: ≥ 10¹² Ω·cm (kısa devre önleme) Dielektrik Güçlü: ≥ 10 kV/mm (high-voltage uygulamalar)
  - Kanıt: `thermal-pad.md` § Seçim Kriterleri > Elektriksel Özellikler

#### K18.3.5 — Bağımlılıklar

- **Sorumluluk:** K01 Donanım: Bileşen yüzey boyutları ve boşlukları K18 Heatsink: Heatsink taban düzgünlüğü ve montaj basıncı
- **Kanıt:** `thermal-pad.md` § Bağımlılıklar (1 bölüm başlığı)

- **K18.3.5.15 — Bağımlılıklar**
  - Sorumluluk: K01 Donanım: Bileşen yüzey boyutları ve boşlukları K18 Heatsink: Heatsink taban düzgünlüğü ve montaj basıncı
  - Kanıt: `thermal-pad.md` § Bağımlılıklar

### K18.4 — PWM Fan Kontrolü

**Sorumluluk:** Noctua PWM fan duty-cycle hesabı, sıcaklık-hız eğrisi, fan arıza durumları ve yazılım kontrolü.
**Kanıt dosyaları:** `pwm-fan-control.md` — havuz 22 başlık, kullanıldı 14, seçim dışı 8.

#### K18.4.1 — Genel Bakış

- **Sorumluluk:** PWM (Pulse Width Modulation) fan kontrolü, COREMUSIC platformunda aktif soğutma için kullanılan dinamik hız kontrol yöntemidir. Sıcaklık sensörlerinden gelen…
- **Kanıt:** `pwm-fan-control.md` § Genel Bakış (1 bölüm başlığı)

- **K18.4.1.1 — Genel Bakış**
  - Sorumluluk: PWM (Pulse Width Modulation) fan kontrolü, COREMUSIC platformunda aktif soğutma için kullanılan dinamik hız kontrol yöntemidir. Sıcaklık sensörlerinden gelen geri beslemeye göre fan hızı otomatik…
  - Kanıt: `pwm-fan-control.md` § Genel Bakış

#### K18.4.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — pwm-fan-control.md dosyasında belgelenen bölüm.
- **Kanıt:** `pwm-fan-control.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.4.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Termal Hesaplamalar
- **K18.4.2.3 — PWM Duty Cycle Hesabı**
  - Sorumluluk: «PWM Duty Cycle Hesabı» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Termal Hesaplamalar > PWM Duty Cycle Hesabı
- **K18.4.2.4 — Sıcaklık-Fan Hızı Eğrisi**
  - Sorumluluk: «Sıcaklık-Fan Hızı Eğrisi» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Termal Hesaplamalar > Sıcaklık-Fan Hızı Eğrisi
- **K18.4.2.5 — Soğutma Kapasitesi**
  - Sorumluluk: «Soğutma Kapasitesi» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Termal Hesaplamalar > Soğutma Kapasitesi

#### K18.4.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — pwm-fan-control.md dosyasında belgelenen bölüm.
- **Kanıt:** `pwm-fan-control.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K18.4.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Teknik Spesifikasyonlar
- **K18.4.3.7 — Seçilen Fan: Noctua NF-A12x25 PWM**
  - Sorumluluk: «Seçilen Fan: Noctua NF-A12x25 PWM» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Teknik Spesifikasyonlar > Seçilen Fan: Noctua NF-A12x25 PWM
- **K18.4.3.8 — PWM Sinyal Özellikleri**
  - Sorumluluk: «PWM Sinyal Özellikleri» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Teknik Spesifikasyonlar > PWM Sinyal Özellikleri
- **K18.4.3.9 — Fan Hız Haritası**
  - Sorumluluk: «Fan Hız Haritası» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Teknik Spesifikasyonlar > Fan Hız Haritası

#### K18.4.4 — Seçim Kriterleri

- **Sorumluluk:** PWM Kontrol Desteği: 4-pin PWM, hassas hız kontrolü Düşük Gürültü: 22.6 dB(A) minimum, sessiz çalışma
- **Kanıt:** `pwm-fan-control.md` § Seçim Kriterleri (3 bölüm başlığı)

- **K18.4.4.10 — Seçim Kriterleri**
  - Sorumluluk: PWM Kontrol Desteği: 4-pin PWM, hassas hız kontrolü Düşük Gürültü: 22.6 dB(A) minimum, sessiz çalışma
  - Kanıt: `pwm-fan-control.md` § Seçim Kriterleri
- **K18.4.4.11 — Fan Seçim Nedenleri**
  - Sorumluluk: PWM Kontrol Desteği: 4-pin PWM, hassas hız kontrolü Düşük Gürültü: 22.6 dB(A) minimum, sessiz çalışma
  - Kanıt: `pwm-fan-control.md` § Seçim Kriterleri > Fan Seçim Nedenleri
- **K18.4.4.12 — Alternatif Karşılaştırma**
  - Sorumluluk: «Alternatif Karşılaştırma» — pwm-fan-control.md dosyasında belgelenen bölüm.
  - Kanıt: `pwm-fan-control.md` § Seçim Kriterleri > Alternatif Karşılaştırma

#### K18.4.5 — Bağımlılıklar

- **Sorumluluk:** K19 Sensörler: CPU ve ambientsıcaklık sensörleri K02 Sürücü: Fan PWM sürücü donanımı K18 Termal Simülasyon: Fan hız eğrisi optimizasyonu
- **Kanıt:** `pwm-fan-control.md` § Bağımlılıklar (2 bölüm başlığı)

- **K18.4.5.13 — Bağımlılıklar**
  - Sorumluluk: K19 Sensörler: CPU ve ambientsıcaklık sensörleri K02 Sürücü: Fan PWM sürücü donanımı K18 Termal Simülasyon: Fan hız eğrisi optimizasyonu
  - Kanıt: `pwm-fan-control.md` § Bağımlılıklar
- **K18.4.5.14 — Girişler**
  - Sorumluluk: K19 Sensörler: CPU ve ambientsıcaklık sensörleri K02 Sürücü: Fan PWM sürücü donanımı K18 Termal Simülasyon: Fan hız eğrisi optimizasyonu
  - Kanıt: `pwm-fan-control.md` § Bağımlılıklar > Girişler

### K18.5 — Kasa & Havalandırma

**Sorumluluk:** Alüminyum 6061-T6 kasa hava akışı, havalandırma delik tasarımı ve kasa termal direnci hedefi.
**Kanıt dosyaları:** `enclosure-thermal.md` — havuz 23 başlık, kullanıldı 15, seçim dışı 8.

#### K18.5.1 — Genel Bakış

- **Sorumluluk:** Kasa termal tasarımı, COREMUSIC cihazlarının iç mekan sıcaklık yönetimini sağlamak için havalandırma, hava akışı ve ısı yayılımı stratejilerini kapsar. Kasa,…
- **Kanıt:** `enclosure-thermal.md` § Genel Bakış (1 bölüm başlığı)

- **K18.5.1.1 — Genel Bakış**
  - Sorumluluk: Kasa termal tasarımı, COREMUSIC cihazlarının iç mekan sıcaklık yönetimini sağlamak için havalandırma, hava akışı ve ısı yayılımı stratejilerini kapsar. Kasa, iç bileşenleri dış etkenlerden korurken…
  - Kanıt: `enclosure-thermal.md` § Genel Bakış

#### K18.5.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — enclosure-thermal.md dosyasında belgelenen bölüm.
- **Kanıt:** `enclosure-thermal.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.5.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Termal Hesaplamalar
- **K18.5.2.3 — Kasa İçi Hava Akışı**
  - Sorumluluk: «Kasa İçi Hava Akışı» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Termal Hesaplamalar > Kasa İçi Hava Akışı
- **K18.5.2.4 — Havalandırma Delik Boyutu**
  - Sorumluluk: «Havalandırma Delik Boyutu» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Termal Hesaplamalar > Havalandırma Delik Boyutu
- **K18.5.2.5 — Kasa Termal Direnci**
  - Sorumluluk: «Kasa Termal Direnci» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Termal Hesaplamalar > Kasa Termal Direnci

#### K18.5.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — enclosure-thermal.md dosyasında belgelenen bölüm.
- **Kanıt:** `enclosure-thermal.md` § Teknik Spesifikasyonlar (5 bölüm başlığı)

- **K18.5.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Teknik Spesifikasyonlar
- **K18.5.3.7 — Kasa Malzemesi: Alüminyum 6061-T6**
  - Sorumluluk: «Kasa Malzemesi: Alüminyum 6061-T6» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Teknik Spesifikasyonlar > Kasa Malzemesi: Alüminyum 6061-T6
- **K18.5.3.8 — Kasa Boyutları ve Yüzey Alanı**
  - Sorumluluk: «Kasa Boyutları ve Yüzey Alanı» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Teknik Spesifikasyonlar > Kasa Boyutları ve Yüzey Alanı
- **K18.5.3.9 — Havalandırma Delik Dizaynı**
  - Sorumluluk: «Havalandırma Delik Dizaynı» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Teknik Spesifikasyonlar > Havalandırma Delik Dizaynı
- **K18.5.3.10 — Hava Akışı Yolları**
  - Sorumluluk: «Hava Akışı Yolları» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Teknik Spesifikasyonlar > Hava Akışı Yolları

#### K18.5.4 — Seçim Kriterleri

- **Sorumluluk:** Alüminyum 6061-T6: Yüksek termal iletim (167 W/m·K) Düşük ağırlık
- **Kanıt:** `enclosure-thermal.md` § Seçim Kriterleri (4 bölüm başlığı)

- **K18.5.4.11 — Seçim Kriterleri**
  - Sorumluluk: Alüminyum 6061-T6: Yüksek termal iletim (167 W/m·K) Düşük ağırlık
  - Kanıt: `enclosure-thermal.md` § Seçim Kriterleri
- **K18.5.4.12 — Kasa Malzemesi Seçimi**
  - Sorumluluk: Alüminyum 6061-T6: Yüksek termal iletim (167 W/m·K) Düşük ağırlık
  - Kanıt: `enclosure-thermal.md` § Seçim Kriterleri > Kasa Malzemesi Seçimi
- **K18.5.4.13 — Havalandırma Tasarım Kriterleri**
  - Sorumluluk: Giriş Delikleri: Alt kısım, temiz hava girişi Çıkış Delikleri: Üst kısım, sıcak hava çıkışı (natural convection)
  - Kanıt: `enclosure-thermal.md` § Seçim Kriterleri > Havalandırma Tasarım Kriterleri
- **K18.5.4.14 — Enclosure Termal Direnç Hedefi**
  - Sorumluluk: «Enclosure Termal Direnç Hedefi» — enclosure-thermal.md dosyasında belgelenen bölüm.
  - Kanıt: `enclosure-thermal.md` § Seçim Kriterleri > Enclosure Termal Direnç Hedefi

#### K18.5.5 — Bağımlılıklar

- **Sorumluluk:** K01 Donanım: İç bileşen yerleşim planı K18 Heatsink: Heatsink boyutları ve konumu K18 Fan: Fan boyutu ve hava debisi
- **Kanıt:** `enclosure-thermal.md` § Bağımlılıklar (1 bölüm başlığı)

- **K18.5.5.15 — Bağımlılıklar**
  - Sorumluluk: K01 Donanım: İç bileşen yerleşim planı K18 Heatsink: Heatsink boyutları ve konumu K18 Fan: Fan boyutu ve hava debisi
  - Kanıt: `enclosure-thermal.md` § Bağımlılıklar

### K18.6 — Ortam Sıcaklığı

**Sorumluluk:** 0–40°C çalışma aralığı, ortam senaryoları (araç/stüdyo), sıcaklık-eşik aksiyonları ve sensör yerleşimi.
**Kanıt dosyaları:** `ambient-temperature.md` — havuz 22 başlık, kullanıldı 14, seçim dışı 8.

#### K18.6.1 — Genel Bakış

- **Sorumluluk:** Ortam sıcaklığı (ambient temperature), COREMUSIC cihazlarının çalıştığı çevre sıcaklığıdır ve termal tasarımın sınırlayıcı faktörlerinden biridir. Tüm termal…
- **Kanıt:** `ambient-temperature.md` § Genel Bakış (1 bölüm başlığı)

- **K18.6.1.1 — Genel Bakış**
  - Sorumluluk: Ortam sıcaklığı (ambient temperature), COREMUSIC cihazlarının çalıştığı çevre sıcaklığıdır ve termal tasarımın sınırlayıcı faktörlerinden biridir. Tüm termal hesaplamalar referans ortam sıcaklığına…
  - Kanıt: `ambient-temperature.md` § Genel Bakış

#### K18.6.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — ambient-temperature.md dosyasında belgelenen bölüm.
- **Kanıt:** `ambient-temperature.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.6.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Termal Hesaplamalar
- **K18.6.2.3 — Ortam Sıcaklığı Etkisi**
  - Sorumluluk: «Ortam Sıcaklığı Etkisi» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Termal Hesaplamalar > Ortam Sıcaklığı Etkisi
- **K18.6.2.4 — Farklı Ortam Senaryoları**
  - Sorumluluk: «Farklı Ortam Senaryoları» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Termal Hesaplamalar > Farklı Ortam Senaryoları
- **K18.6.2.5 — Güvenli Çalışma Aralığı**
  - Sorumluluk: «Güvenli Çalışma Aralığı» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Termal Hesaplamalar > Güvenli Çalışma Aralığı

#### K18.6.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — ambient-temperature.md dosyasında belgelenen bölüm.
- **Kanıt:** `ambient-temperature.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K18.6.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Teknik Spesifikasyonlar
- **K18.6.3.7 — Çalışma Ortamı Kategorileri**
  - Sorumluluk: «Çalışma Ortamı Kategorileri» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Teknik Spesifikasyonlar > Çalışma Ortamı Kategorileri
- **K18.6.3.8 — Sıcaklık Dalgalanmaları**
  - Sorumluluk: «Sıcaklık Dalgalanmaları» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Teknik Spesifikasyonlar > Sıcaklık Dalgalanmaları
- **K18.6.3.9 — Araç Ortamı Detayları**
  - Sorumluluk: «Araç Ortamı Detayları» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Teknik Spesifikasyonlar > Araç Ortamı Detayları

#### K18.6.4 — Seçim Kriterleri

- **Sorumluluk:** «Seçim Kriterleri» — ambient-temperature.md dosyasında belgelenen bölüm.
- **Kanıt:** `ambient-temperature.md` § Seçim Kriterleri (4 bölüm başlığı)

- **K18.6.4.10 — Seçim Kriterleri**
  - Sorumluluk: «Seçim Kriterleri» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Seçim Kriterleri
- **K18.6.4.11 — Ortam Sıcaklığı Sensörleri**
  - Sorumluluk: «Ortam Sıcaklığı Sensörleri» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Seçim Kriterleri > Ortam Sıcaklığı Sensörleri
- **K18.6.4.12 — Ortam Sensör Yerleşimi**
  - Sorumluluk: «Ortam Sensör Yerleşimi» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Seçim Kriterleri > Ortam Sensör Yerleşimi
- **K18.6.4.13 — Sıcaklık-Aksiyon Eşikleri**
  - Sorumluluk: «Sıcaklık-Aksiyon Eşikleri» — ambient-temperature.md dosyasında belgelenen bölüm.
  - Kanıt: `ambient-temperature.md` § Seçim Kriterleri > Sıcaklık-Aksiyon Eşikleri

#### K18.6.5 — Bağımlılıklarlar

- **Sorumluluk:** K19 Sensörler: Ortam sıcaklık sensörleri K01 Donanım: Sensör yerleşim planı K18 Termal Simülasyon: Ortam sıcaklık etkisi analizi
- **Kanıt:** `ambient-temperature.md` § Bağımlılıklarlar (1 bölüm başlığı)

- **K18.6.5.14 — Bağımlılıklarlar**
  - Sorumluluk: K19 Sensörler: Ortam sıcaklık sensörleri K01 Donanım: Sensör yerleşim planı K18 Termal Simülasyon: Ortam sıcaklık etkisi analizi
  - Kanıt: `ambient-temperature.md` § Bağımlılıklarlar

### K18.7 — KSD301 Termal Cutoff

**Sorumluluk:** Donanım sıcaklık tripsi: trip hesabı, zaman sabiti, KSD301 seçimi, montaj/test/bakım prosedürleri.
**Kanıt dosyaları:** `ksd301-cutoff.md` — havuz 23 başlık, kullanıldı 15, seçim dışı 8.

#### K18.7.1 — Genel Bakış

- **Sorumluluk:** KSD301, COREMUSIC platformunda aşırı sıcaklık koruması için kullanılan mekanik termal cutoff cihazıdır. Bimetal strip yapısı sayesinde belirlenen sıcaklık…
- **Kanıt:** `ksd301-cutoff.md` § Genel Bakış (1 bölüm başlığı)

- **K18.7.1.1 — Genel Bakış**
  - Sorumluluk: KSD301, COREMUSIC platformunda aşırı sıcaklık koruması için kullanılan mekanik termal cutoff cihazıdır. Bimetal strip yapısı sayesinde belirlenen sıcaklık eşiğine ulaştığında mekanik olarak devreyi…
  - Kanıt: `ksd301-cutoff.md` § Genel Bakış

#### K18.7.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — ksd301-cutoff.md dosyasında belgelenen bölüm.
- **Kanıt:** `ksd301-cutoff.md` § Termal Hesaplamalar (4 bölüm başlığı)

- **K18.7.2.2 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Termal Hesaplamalar
- **K18.7.2.3 — Trip Sıcaklığı Hesabı**
  - Sorumluluk: «Trip Sıcaklığı Hesabı» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Termal Hesaplamalar > Trip Sıcaklığı Hesabı
- **K18.7.2.4 — Isıtma Hızı Etkisi**
  - Sorumluluk: «Isıtma Hızı Etkisi» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Termal Hesaplamalar > Isıtma Hızı Etkisi
- **K18.7.2.5 — Thermal Time Constant**
  - Sorumluluk: «Thermal Time Constant» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Termal Hesaplamalar > Thermal Time Constant

#### K18.7.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — ksd301-cutoff.md dosyasında belgelenen bölüm.
- **Kanıt:** `ksd301-cutoff.md` § Teknik Spesifikasyonlar (4 bölüm başlığı)

- **K18.7.3.6 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Teknik Spesifikasyonlar
- **K18.7.3.7 — KSD301 Serisi Karşılaştırma**
  - Sorumluluk: «KSD301 Serisi Karşılaştırma» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Teknik Spesifikasyonlar > KSD301 Serisi Karşılaştırma
- **K18.7.3.8 — Fiziksel Özellikler**
  - Sorumluluk: «Fiziksel Özellikler» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Teknik Spesifikasyonlar > Fiziksel Özellikler
- **K18.7.3.9 — Elektriksel Özellikler**
  - Sorumluluk: «Elektriksel Özellikler» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Teknik Spesifikasyonlar > Elektriksel Özellikler

#### K18.7.4 — Seçim Kriterleri

- **Sorumluluk:** Düşük Trip Sıcaklığı: 70°C, yazılım throttle başlangıcından önce Hızlı Tepki: 90 saniye thermal time constant
- **Kanıt:** `ksd301-cutoff.md` § Seçim Kriterleri (4 bölüm başlığı)

- **K18.7.4.10 — Seçim Kriterleri**
  - Sorumluluk: Düşük Trip Sıcaklığı: 70°C, yazılım throttle başlangıcından önce Hızlı Tepki: 90 saniye thermal time constant
  - Kanıt: `ksd301-cutoff.md` § Seçim Kriterleri
- **K18.7.4.11 — KSD301-70 (CPU Koruması)**
  - Sorumluluk: Düşük Trip Sıcaklığı: 70°C, yazılım throttle başlangıcından önce Hızlı Tepki: 90 saniye thermal time constant
  - Kanıt: `ksd301-cutoff.md` § Seçim Kriterleri > KSD301-70 (CPU Koruması)
- **K18.7.4.12 — KSD301-100 (Amplifikatör Koruması)**
  - Sorumluluk: Orta Trip Sıcaklığı: 100°C, Class-D amplifikatör için uygun Yüksek Güç Dayanımı: 10A, amplifikatör akım gereksinimleri
  - Kanıt: `ksd301-cutoff.md` § Seçim Kriterleri > KSD301-100 (Amplifikatör Koruması)
- **K18.7.4.13 — Karşılaştırma Tablosu**
  - Sorumluluk: «Karşılaştırma Tablosu» — ksd301-cutoff.md dosyasında belgelenen bölüm.
  - Kanıt: `ksd301-cutoff.md` § Seçim Kriterleri > Karşılaştırma Tablosu

#### K18.7.5 — Bağımlılıklar

- **Sorumluluk:** K01 Donanım: CPU ve amplifikatör yerleşim planı K17 Güç Yönetimi: Güç kaynağı devre şeması
- **Kanıt:** `ksd301-cutoff.md` § Bağımlılıklar (2 bölüm başlığı)

- **K18.7.5.14 — Bağımlılıklar**
  - Sorumluluk: K01 Donanım: CPU ve amplifikatör yerleşim planı K17 Güç Yönetimi: Güç kaynağı devre şeması
  - Kanıt: `ksd301-cutoff.md` § Bağımlılıklar
- **K18.7.5.15 — Girişler**
  - Sorumluluk: K01 Donanım: CPU ve amplifikatör yerleşim planı K17 Güç Yönetimi: Güç kaynağı devre şeması
  - Kanıt: `ksd301-cutoff.md` § Bağımlılıklar > Girişler

### K18.8 — Hesaplama & Simülasyon

**Sorumluluk:** Termal direnç zinciri ve değerleri, CFD/OpenFOAM simülasyon parametreleri ve doğrulama prosedürü.
**Kanıt dosyaları:** `thermal-resistance.md`, `thermal-simulation.md` — havuz 45 başlık, kullanıldı 29, seçim dışı 16.

#### K18.8.1 — Genel Bakış

- **Sorumluluk:** Termal direnç (Rθ), ısı akışına karşı gösterilen dirençtir ve elektriksel dirence benzer şekilde hesaplanır. COREMUSIC platformunda, junction-to-ambient termal…
- **Kanıt:** `thermal-resistance.md`, `thermal-simulation.md` § Genel Bakış (2 bölüm başlığı)

- **K18.8.1.1 — Genel Bakış**
  - Sorumluluk: Termal direnç (Rθ), ısı akışına karşı gösterilen dirençtir ve elektriksel dirence benzer şekilde hesaplanır. COREMUSIC platformunda, junction-to-ambient termal direnç zinciri, bileşenlerin sıcaklık…
  - Kanıt: `thermal-resistance.md` § Genel Bakış
- **K18.8.1.2 — Genel Bakış**
  - Sorumluluk: Termal simülasyon, COREMUSIC donanım tasarımının fiziksel test öncesi dijital ortamda doğrulanmasını sağlar. CFD (Computational Fluid Dynamics) analizi ile hava akışı, sıcaklık dağılımı ve termal…
  - Kanıt: `thermal-simulation.md` § Genel Bakış

#### K18.8.2 — Termal Hesaplamalar

- **Sorumluluk:** «Termal Hesaplamalar» — thermal-resistance.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-resistance.md`, `thermal-simulation.md` § Termal Hesaplamalar (9 bölüm başlığı)

- **K18.8.2.3 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Termal Hesaplamalar
- **K18.8.2.4 — Temel Formül**
  - Sorumluluk: «Temel Formül» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Termal Hesaplamalar > Temel Formül
- **K18.8.2.5 — Termal Direnç Zinciri**
  - Sorumluluk: «Termal Direnç Zinciri» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Termal Hesaplamalar > Termal Direnç Zinciri
- **K18.8.2.6 — COREMUSIC Bileşen Dirençleri**
  - Sorumluluk: «COREMUSIC Bileşen Dirençleri» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Termal Hesaplamalar > COREMUSIC Bileşen Dirençleri
- **K18.8.2.7 — Sıcaklık Hesapları**
  - Sorumluluk: «Sıcaklık Hesapları» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Termal Hesaplamalar > Sıcaklık Hesapları
- **K18.8.2.8 — Termal Hesaplamalar**
  - Sorumluluk: «Termal Hesaplamalar» — thermal-simulation.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-simulation.md` § Termal Hesaplamalar
- **K18.8.2.9 — CFD Temel Denklemleri**
  - Sorumluluk: «CFD Temel Denklemleri» — thermal-simulation.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-simulation.md` § Termal Hesaplamalar > CFD Temel Denklemleri
- **K18.8.2.10 — Termal Direnç Ağ Modeli**
  - Sorumluluk: «Termal Direnç Ağ Modeli» — thermal-simulation.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-simulation.md` § Termal Hesaplamalar > Termal Direnç Ağ Modeli
- **K18.8.2.11 — Hava Akışı Hesabı**
  - Sorumluluk: «Hava Akışı Hesabı» — thermal-simulation.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-simulation.md` § Termal Hesaplamalar > Hava Akışı Hesabı

#### K18.8.3 — Teknik Spesifikasyonlar

- **Sorumluluk:** «Teknik Spesifikasyonlar» — thermal-resistance.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-resistance.md`, `thermal-simulation.md` § Teknik Spesifikasyonlar (5 bölüm başlığı)

- **K18.8.3.12 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Teknik Spesifikasyonlar
- **K18.8.3.13 — TERMAL DİREnç DEĞERLERİ**
  - Sorumluluk: «TERMAL DİREnç DEĞERLERİ» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Teknik Spesifikasyonlar > TERMAL DİREnç DEĞERLERİ
- **K18.8.3.14 — Termal Direnç bileşenleri**
  - Sorumluluk: «Termal Direnç bileşenleri» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Teknik Spesifikasyonlar > Termal Direnç bileşenleri
- **K18.8.3.15 — Temas Alanı ve Direnç İlişkisi**
  - Sorumluluk: «Temas Alanı ve Direnç İlişkisi» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Teknik Spesifikasyonlar > Temas Alanı ve Direnç İlişkisi
- **K18.8.3.16 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — thermal-simulation.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-simulation.md` § Teknik Spesifikasyonlar

#### K18.8.4 — Seçim Kriterleri / Bağımlılıklar

- **Sorumluluk:** Düşük Rθ_jc: Yüksek performanslı bileşen seçimi Düşük Rθ_cs: İyi TIM seçimi, yüksek basınc
- **Kanıt:** `thermal-resistance.md` § Seçim Kriterleri / Bağımlılıklar (8 bölüm başlığı)

- **K18.8.4.17 — Seçim Kriterleri**
  - Sorumluluk: Düşük Rθ_jc: Yüksek performanslı bileşen seçimi Düşük Rθ_cs: İyi TIM seçimi, yüksek basınc
  - Kanıt: `thermal-resistance.md` § Seçim Kriterleri
- **K18.8.4.18 — Termal Direnç Hedefleri**
  - Sorumluluk: «Termal Direnç Hedefleri» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Seçim Kriterleri > Termal Direnç Hedefleri
- **K18.8.4.19 — Direnç Azaltma Stratejileri**
  - Sorumluluk: Düşük Rθ_jc: Yüksek performanslı bileşen seçimi Düşük Rθ_cs: İyi TIM seçimi, yüksek basınc
  - Kanıt: `thermal-resistance.md` § Seçim Kriterleri > Direnç Azaltma Stratejileri
- **K18.8.4.20 — Malzeme Seçim Kriterleri**
  - Sorumluluk: «Malzeme Seçim Kriterleri» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Seçim Kriterleri > Malzeme Seçim Kriterleri
- **K18.8.4.21 — Bağımlılıklar**
  - Sorumluluk: K01 Donanım: Bileşen datasheet'leri ve termal özellikleri K18 Heatsink: Heatsink geometrisi ve malzeme
  - Kanıt: `thermal-resistance.md` § Bağımlılıklar
- **K18.8.4.22 — Girişler**
  - Sorumluluk: K01 Donanım: Bileşen datasheet'leri ve termal özellikleri K18 Heatsink: Heatsink geometrisi ve malzeme
  - Kanıt: `thermal-resistance.md` § Bağımlılıklar > Girişler
- **K18.8.4.23 — Çıktılar**
  - Sorumluluk: K18 Termal Simülasyon: CFD modeli için direnç değerleri K18 PWM Fan: Fan hız eğrisi referansları
  - Kanıt: `thermal-resistance.md` § Bağımlılıklar > Çıktılar
- **K18.8.4.24 — Entegrasyon Noktaları**
  - Sorumluluk: «Entegrasyon Noktaları» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Bağımlılıklar > Entegrasyon Noktaları

#### K18.8.5 — Uygulama Notları / Durum: Implementasyon

- **Sorumluluk:** En büyük direnci azalt: En yüksek Rθ bileşenini iyileştir Temas alanını artır: Daha geniş heatsink tabanı
- **Kanıt:** `thermal-resistance.md` § Uygulama Notları / Durum: Implementasyon (5 bölüm başlığı)

- **K18.8.5.25 — Uygulama Notları**
  - Sorumluluk: En büyük direnci azalt: En yüksek Rθ bileşenini iyileştir Temas alanını artır: Daha geniş heatsink tabanı
  - Kanıt: `thermal-resistance.md` § Uygulama Notları
- **K18.8.5.26 — Direnç Ölçüm Prosedürü**
  - Sorumluluk: «Direnç Ölçüm Prosedürü» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Uygulama Notları > Direnç Ölçüm Prosedürü
- **K18.8.5.27 — Yaygın Hatalar**
  - Sorumluluk: «Yaygın Hatalar» — thermal-resistance.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-resistance.md` § Uygulama Notları > Yaygın Hatalar
- **K18.8.5.28 — Optimizasyon İpuçları**
  - Sorumluluk: En büyük direnci azalt: En yüksek Rθ bileşenini iyileştir Temas alanını artır: Daha geniş heatsink tabanı
  - Kanıt: `thermal-resistance.md` § Uygulama Notları > Optimizasyon İpuçları
- **K18.8.5.29 — Durum: Implementasyon**
  - Sorumluluk: Termal direnç hesaplamaları, COREMUSIC platformunun termal tasarım temelini oluşturmaktadır. Junction-to-ambient direnç zinciri tüm bileşenler için hesaplanmış ve hedef değerler belirlenmiştir.…
  - Kanıt: `thermal-resistance.md` § Durum: Implementasyon

---

## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k18-termal/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayımın hangi kısmını desteklediğini listeler. 9 kanonik katman MD'si + index.md + README.md + CLAUDE.md.

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `fischer-heatsink.md` | Fischer SK53 Heatsink | `K18.1` alanı (1), 5 alt alan, 14 yaprak → `K18` toplam 170 içine katkı |
| 2 | `heat-pipe-design.md` | Heat Pipe Tasarımı | `K18.2` alanı (1), 5 alt alan, 14 yaprak → `K18` toplam 170 içine katkı |
| 3 | `thermal-pad.md` | Termal Ped Seçimi ve Spesifikasyonları | `K18.3` alanı (1), 5 alt alan, 15 yaprak → `K18` toplam 170 içine katkı |
| 4 | `pwm-fan-control.md` | PWM Fan Hız Kontrolü | `K18.4` alanı (1), 5 alt alan, 14 yaprak → `K18` toplam 170 içine katkı |
| 5 | `enclosure-thermal.md` | Kasa Termal Tasarımı | `K18.5` alanı (1), 5 alt alan, 15 yaprak → `K18` toplam 170 içine katkı |
| 6 | `ambient-temperature.md` | Ortam Sıcaklığı Değerlendirmesi | `K18.6` alanı (1), 5 alt alan, 14 yaprak → `K18` toplam 170 içine katkı |
| 7 | `ksd301-cutoff.md` | KSD301 Termal Cutoff | `K18.7` alanı (1), 5 alt alan, 15 yaprak → `K18` toplam 170 içine katkı |
| 8 | `thermal-resistance.md` | Termal Direnç Hesaplamaları | `K18.8` alanı (1), 5 alt alan, 23 yaprak → `K18` toplam 170 içine katkı |
| 9 | `thermal-simulation.md` | Termal Simülasyon Metodolojisi | `K18.8` alanı (1), 3 alt alan, 6 yaprak → `K18` toplam 170 içine katkı |
| 10 | `index.md` | Katman ana sayfası: diyagram, tablolar, bağımlılıklar | `K18.a) alan tanımı bağlamı |
| 11 | `README.md` | Katman künyesi ve bu şema/katalog | `K18.a`/`K18.a.b` doğrulaması |
| 12 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |

**Sayım dayanağı:** 9 dosyadan çıkarılan 202 H2/H3 başlığı içinden hedef c=130 için 72 başlık orantılı olarak seçimin dışında bırakıldı (aşağıda); alan/alt alan sayıları a=8, b=5 ile kilitlidir.

### Seçim Dışı Kanıtlar

| Başlık | Dosya | Neden |
|--------|-------|-------|
| Girişler | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Termal Ped Kullanımı | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Montaj Prosedürü | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bakım | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `fischer-heatsink.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Girişler | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Heat Pipe Montaj Prosedürü | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Heat Pipe Bakımı | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Yaygın Hatalar | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `heat-pipe-design.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Girişler | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Montaj Prosedürü | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bakım ve Değişim | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Yaygın Hatalar | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `thermal-pad.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| PWM Fan Kontrol Yazılımı | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Fan Hız Eğrisi Optimizasyonu | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Fan Arıza Durumları | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bakım | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `pwm-fan-control.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Girişler | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Kasa Havalandırma Tasarım Prosedürü | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Kasa Termal Performans Testi | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Yaygın Hatalar | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `enclosure-thermal.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Girişler | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Ortam Sensörü Kalibrasyonu | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Araç Ortamı İçin Özel Önlemler | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Ofis/Stüdyo Ortamı İçin | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `ambient-temperature.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Montaj Prosedürü | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Test Prosedürü | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bakım ve Değişim | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Yaygın Hatalar | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `ksd301-cutoff.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| CFD Yazılım ve Araçlar | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Simülasyon Parametreleri | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Simülasyon Senaryoları | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Seçim Kriterleri | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| CFD Yazılım Seçimi: OpenFOAM | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Grid Kalite Kriterleri | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Doğrulama Kriterleri | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Girişler | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Çıktılar | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Entegrasyon Noktaları | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Uygulama Notları | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Simülasyon Workflow | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Sık Karşılaşılan Sorunlar | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Doğrulama Prosedürü | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `thermal-simulation.md` | hedef c=130 aşıldı; havuz 202 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |

*K18 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
