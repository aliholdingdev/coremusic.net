---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K17 Güç Kaynağı Layer"
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

# K17: Güç Kaynağı Layer

**Katman:** K17 (Güç Kaynağı)
**Kapsam:** 6S LiPo, BMS, OR-ing, LM5122 dual boost, LDO/buck rail'leri, soft start, akım ölçüm, EMC, verimlilik
**Sorumlu Agent:** Audio Hardware Engineer / Power Engineer
**Bileşen Sayısı:** 13 kanonik katman MD'si
**Bağımlılık:** K17 ↔ K1 — bağımsız üretim katmanı (matris §2.2)

---

## 1. Genel Bakış

K17, platformun 6S LiPo (22.2V nominal) girişinden ±35V, ±15V, +12V, +5V ve +3.3V rail'lerine kadar çoklu çıkış üreten merkezi güç yönetim sistemini kapsar. Tasarım hedefi >%92 verim, düşük EMI ve UVP/OVP/OCP/OTP koruma seviyeleridir; soft start ve rail sıralaması ile açılış akışı yönetilir. Ayrıntılı diyagram ve rail tablosu `index.md` dosyasındadır.

---

## 2. Kaynak Dökümanlar

- `index.md` — katman ana sayfası (mimari diyagram, tablolar, bağımlılıklar)
- `CLAUDE.md` — katman kural ve kapsam notları
- Alt katman şeması ve kanıt kataloğu bu README'ye 2026-09-24'te eklenmiştir (son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması).


---

## Alt Katman Şeması (K17.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K17 katmanını onaylı şema biçiminde (K17 → K17.a → K17.a.b → K17.a.b.c) belgeler. Alanlar (a) güç mimarisi bileşen aileleri, alt alanlar (b) dosyalardaki H2 bölüm başlıkları, yapraklar (c) k17-guc-kaynagi/ kanonik MD dosyalarındaki gerçek H2/H3 bölüm başlıklarından türetilmiştir; her yaprak kanıt satırıyla kaynak dosya ve bölümünü gösterir. Uydurma düğüm yoktur.

**Şema kuralları:**

1. Zorunlu şema: `K17` → `K17.a` → `K17.a.b`; seviye-4 (`K17.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K16–K20 beş BAĞIMSIZ üretim katmanıdır; hiyerarşi yok, birbirine alt değildir (matris §1.1, §1.3 K3, §5.2 #21).
5. Bağımlılık bağlamı: K17 ↔ K1 (çift yönlü; K17 bağımsız üretim katmanı — matris §2.2). K17 → K0 yasaktır (matris §5.1 #12); K17'nin K16/K18/K19/K20'ye bağımlılığı YOKTUR (matris §5.2 #21).
6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K17` (a alan) | 9 | 9 | 9 | +0 |
| `K17.a.b` (a×b alt alan) | 54 | 54 | — | 0 |
| `K17.a.b.c` (c yaprak) | 190 | 190 | 240 | +50 |
| **Toplam düğüm** | **244** | **244** | **294** | **+50** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K17.1` | 6S LiPo Batarya | 6 | 14 | `6s-lipo-battery.md` |
| `K17.2` | Batarya Yönetim Sistemi (BMS) | 6 | 17 | `battery-management.md` |
| `K17.3` | Güç Girişi & OR-ing | 6 | 17 | `or-ing-circuit.md` |
| `K17.4` | LM5122 Dual Boost | 6 | 12 | `lm5122-dual-boost.md` |
| `K17.5` | Regülasyon Rail'leri | 6 | 13 | `voltage-regulation.md` |
| `K17.6` | Yumuşak Başlangıç & Sıralama | 6 | 27 | `soft-start.md`, `power-sequencing.md` |
| `K17.7` | Akım Ölçüm & Koruma | 6 | 15 | `current-sensing.md` |
| `K17.8` | EMC, Filtreleme & Depolama | 6 | 42 | `emc-filtering.md`, `decoupling-strategy.md`, `bulk-capacitors.md` |
| `K17.9` | Verimlilik & Termal Yönetim | 6 | 33 | `power-efficiency.md`, `thermal-management-power.md` |

### K17.1 — 6S LiPo Batarya

**Sorumluluk:** 22.2V 6S paket: CC-CV şarj profili, hücre dengeleme, aşırı şarj/deşarj koruması ve depolama kuralları.
**Kanıt dosyaları:** `6s-lipo-battery.md` — havuz 18 başlık, kullanıldı 14, seçim dışı 4.

#### K17.1.1 — Genel Bakış / Pil Konfigürasyonu

- **Sorumluluk:** 6S LiPo (6 hücre seri, Lithium Polymer) batarya, COREMUSIC platformunun mobil güç kaynağıdır. 22.2V nominal gerilim, 2200mAh kapasite ile 48.8Wh enerji…
- **Kanıt:** `6s-lipo-battery.md` § Genel Bakış / Pil Konfigürasyonu (2 bölüm başlığı)

- **K17.1.1.1 — Genel Bakış**
  - Sorumluluk: 6S LiPo (6 hücre seri, Lithium Polymer) batarya, COREMUSIC platformunun mobil güç kaynağıdır. 22.2V nominal gerilim, 2200mAh kapasite ile 48.8Wh enerji depolar. Yüksek enerji yoğunluğu, düşük iç…
  - Kanıt: `6s-lipo-battery.md` § Genel Bakış
- **K17.1.1.2 — Pil Konfigürasyonu**
  - Sorumluluk: «Pil Konfigürasyonu» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Pil Konfigürasyonu

#### K17.1.2 — Hücre Durum Diyagramı / Teknik Spesifikasyonlar

- **Sorumluluk:** «Hücre Durum Diyagramı» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
- **Kanıt:** `6s-lipo-battery.md` § Hücre Durum Diyagramı / Teknik Spesifikasyonlar (2 bölüm başlığı)

- **K17.1.2.3 — Hücre Durum Diyagramı**
  - Sorumluluk: «Hücre Durum Diyagramı» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Hücre Durum Diyagramı
- **K17.1.2.4 — Teknik Spesifikasyonlar**
  - Sorumluluk: «Teknik Spesifikasyonlar» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Teknik Spesifikasyonlar

#### K17.1.3 — Şarj Profili

- **Sorumluluk:** Pre-charge (Ön Şarj): < 3.0V/hücre → 0.1C akım Constant Current (CC): 3.0V-4.2V → Sabit akım (1C veya 2C)
- **Kanıt:** `6s-lipo-battery.md` § Şarj Profili (3 bölüm başlığı)

- **K17.1.3.5 — Şarj Profili**
  - Sorumluluk: Pre-charge (Ön Şarj): < 3.0V/hücre → 0.1C akım Constant Current (CC): 3.0V-4.2V → Sabit akım (1C veya 2C)
  - Kanıt: `6s-lipo-battery.md` § Şarj Profili
- **K17.1.3.6 — CC-CV (Constant Current - Constant Voltage) Şarj**
  - Sorumluluk: «CC-CV (Constant Current - Constant Voltage) Şarj» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Şarj Profili > CC-CV (Constant Current - Constant Voltage) Şarj
- **K17.1.3.7 — Şarj Aşamaları**
  - Sorumluluk: Pre-charge (Ön Şarj): < 3.0V/hücre → 0.1C akım Constant Current (CC): 3.0V-4.2V → Sabit akım (1C veya 2C)
  - Kanıt: `6s-lipo-battery.md` § Şarj Profili > Şarj Aşamaları

#### K17.1.4 — Hücre Dengesi (Cell Balancing)

- **Sorumluluk:** «Hücre Dengesi (Cell Balancing)» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
- **Kanıt:** `6s-lipo-battery.md` § Hücre Dengesi (Cell Balancing) (3 bölüm başlığı)

- **K17.1.4.8 — Hücre Dengesi (Cell Balancing)**
  - Sorumluluk: «Hücre Dengesi (Cell Balancing)» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Hücre Dengesi (Cell Balancing)
- **K17.1.4.9 — Pasif Dengeleme**
  - Sorumluluk: «Pasif Dengeleme» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Hücre Dengesi (Cell Balancing) > Pasif Dengeleme
- **K17.1.4.10 — Aktif Dengeleme (Opsiyonel)**
  - Sorumluluk: «Aktif Dengeleme (Opsiyonel)» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Hücre Dengesi (Cell Balancing) > Aktif Dengeleme (Opsiyonel)

#### K17.1.5 — Koruma Devresi

- **Sorumluluk:** «Koruma Devresi» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
- **Kanıt:** `6s-lipo-battery.md` § Koruma Devresi (3 bölüm başlığı)

- **K17.1.5.11 — Koruma Devresi**
  - Sorumluluk: «Koruma Devresi» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Koruma Devresi
- **K17.1.5.12 — Aşırı Deşarj Koruması**
  - Sorumluluk: «Aşırı Deşarj Koruması» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Koruma Devresi > Aşırı Deşarj Koruması
- **K17.1.5.13 — Aşırı Şarj Koruması**
  - Sorumluluk: «Aşırı Şarj Koruması» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Koruma Devresi > Aşırı Şarj Koruması

#### K17.1.6 — Sıcaklık Etkisi

- **Sorumluluk:** «Sıcaklık Etkisi» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
- **Kanıt:** `6s-lipo-battery.md` § Sıcaklık Etkisi (1 bölüm başlığı)

- **K17.1.6.14 — Sıcaklık Etkisi**
  - Sorumluluk: «Sıcaklık Etkisi» — 6s-lipo-battery.md dosyasında belgelenen bölüm.
  - Kanıt: `6s-lipo-battery.md` § Sıcaklık Etkisi

### K17.2 — Batarya Yönetim Sistemi (BMS)

**Sorumluluk:** BQ76940 tabanlı hücre gerilimi izleme, SOC hesaplama, koruma fonksiyonları ve I²C haberleşme.
**Kanıt dosyaları:** `battery-management.md` — havuz 22 başlık, kullanıldı 17, seçim dışı 5.

#### K17.2.1 — Genel Bakış / BMS Mimarisi

- **Sorumluluk:** BMS (Battery Management System), 6S LiPo bataryanın güvenli ve verimli çalışmasını sağlayan akıllı yönetim kartıdır. Hücre gerilimi izleme, aktif/pasif…
- **Kanıt:** `battery-management.md` § Genel Bakış / BMS Mimarisi (2 bölüm başlığı)

- **K17.2.1.1 — Genel Bakış**
  - Sorumluluk: BMS (Battery Management System), 6S LiPo bataryanın güvenli ve verimli çalışmasını sağlayan akıllı yönetim kartıdır. Hücre gerilimi izleme, aktif/pasif dengeleme, akım sınırlama ve çoklu koruma…
  - Kanıt: `battery-management.md` § Genel Bakış
- **K17.2.1.2 — BMS Mimarisi**
  - Sorumluluk: «BMS Mimarisi» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § BMS Mimarisi

#### K17.2.2 — Hücre Gerilimi İzleme

- **Sorumluluk:** Filtre Parametreleri: Kesim Frekansı: fc = 1 / (2π × R × C) = 15.9kHz ADC Çözünürlüğü: 14-bit, 384μV/LSB
- **Kanıt:** `battery-management.md` § Hücre Gerilimi İzleme (3 bölüm başlığı)

- **K17.2.2.3 — Hücre Gerilimi İzleme**
  - Sorumluluk: Filtre Parametreleri: Kesim Frekansı: fc = 1 / (2π × R × C) = 15.9kHz ADC Çözünürlüğü: 14-bit, 384μV/LSB
  - Kanıt: `battery-management.md` § Hücre Gerilimi İzleme
- **K17.2.2.4 — ADC Ölçüm Devresi**
  - Sorumluluk: Filtre Parametreleri: Kesim Frekansı: fc = 1 / (2π × R × C) = 15.9kHz ADC Çözünürlüğü: 14-bit, 384μV/LSB
  - Kanıt: `battery-management.md` § Hücre Gerilimi İzleme > ADC Ölçüm Devresi
- **K17.2.2.5 — Hücre Gerilim Eşikleri**
  - Sorumluluk: «Hücre Gerilim Eşikleri» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Hücre Gerilimi İzleme > Hücre Gerilim Eşikleri

#### K17.2.3 — Hücre Dengeleme

- **Sorumluluk:** Dengeleme Akımı:
- **Kanıt:** `battery-management.md` § Hücre Dengeleme (3 bölüm başlığı)

- **K17.2.3.6 — Hücre Dengeleme**
  - Sorumluluk: Dengeleme Akımı:
  - Kanıt: `battery-management.md` § Hücre Dengeleme
- **K17.2.3.7 — Pasif Dengeleme Devresi**
  - Sorumluluk: Dengeleme Akımı:
  - Kanıt: `battery-management.md` § Hücre Dengeleme > Pasif Dengeleme Devresi
- **K17.2.3.8 — Dengeleme Algoritması**
  - Sorumluluk: «Dengeleme Algoritması» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Hücre Dengeleme > Dengeleme Algoritması

#### K17.2.4 — SOC (State of Charge) Hesaplama

- **Sorumluluk:** «SOC (State of Charge) Hesaplama» — battery-management.md dosyasında belgelenen bölüm.
- **Kanıt:** `battery-management.md` § SOC (State of Charge) Hesaplama (3 bölüm başlığı)

- **K17.2.4.9 — SOC (State of Charge) Hesaplama**
  - Sorumluluk: «SOC (State of Charge) Hesaplama» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § SOC (State of Charge) Hesaplama
- **K17.2.4.10 — Coulomb Counting**
  - Sorumluluk: «Coulomb Counting» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § SOC (State of Charge) Hesaplama > Coulomb Counting
- **K17.2.4.11 — OCV (Open Circuit Voltage) Kalibrasyonu**
  - Sorumluluk: «OCV (Open Circuit Voltage) Kalibrasyonu» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § SOC (State of Charge) Hesaplama > OCV (Open Circuit Voltage) Kalibrasyonu

#### K17.2.5 — Koruma Fonksiyonları

- **Sorumluluk:** «Koruma Fonksiyonları» — battery-management.md dosyasında belgelenen bölüm.
- **Kanıt:** `battery-management.md` § Koruma Fonksiyonları (3 bölüm başlığı)

- **K17.2.5.12 — Koruma Fonksiyonları**
  - Sorumluluk: «Koruma Fonksiyonları» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Koruma Fonksiyonları
- **K17.2.5.13 — Aşırı Akım Koruması**
  - Sorumluluk: «Aşırı Akım Koruması» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Koruma Fonksiyonları > Aşırı Akım Koruması
- **K17.2.5.14 — Sıcaklık Koruması**
  - Sorumluluk: «Sıcaklık Koruması» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Koruma Fonksiyonları > Sıcaklık Koruması

#### K17.2.6 — Güç Modları / Haberleşme Arayüzü

- **Sorumluluk:** «Güç Modları» — battery-management.md dosyasında belgelenen bölüm.
- **Kanıt:** `battery-management.md` § Güç Modları / Haberleşme Arayüzü (3 bölüm başlığı)

- **K17.2.6.15 — Güç Modları**
  - Sorumluluk: «Güç Modları» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Güç Modları
- **K17.2.6.16 — Çalışma Modları**
  - Sorumluluk: «Çalışma Modları» — battery-management.md dosyasında belgelenen bölüm.
  - Kanıt: `battery-management.md` § Güç Modları > Çalışma Modları
- **K17.2.6.17 — Haberleşme Arayüzü**
  - Sorumluluk: I²C Parametreleri: Hız: 400kHz (Fast Mode) Adres: 0x08 (7-bit)
  - Kanıt: `battery-management.md` § Haberleşme Arayüzü

### K17.3 — Güç Girişi & OR-ing

**Sorumluluk:** Aktif MOSFET OR-ing, ters polarite koruması, kaynak öncelik mantığı ve otomatik geçiş.
**Kanıt dosyaları:** `or-ing-circuit.md` — havuz 21 başlık, kullanıldı 17, seçim dışı 4.

#### K17.3.1 — Genel Bakış

- **Sorumluluk:** OR-ing devresi, COREMUSIC'e çoklu güç kaynağı bağlandığında (batarya + şarj cihazı + harici PSU) kaynakların otomatik olarak birleştirilmesini ve ters polarite…
- **Kanıt:** `or-ing-circuit.md` § Genel Bakış (1 bölüm başlığı)

- **K17.3.1.1 — Genel Bakış**
  - Sorumluluk: OR-ing devresi, COREMUSIC'e çoklu güç kaynağı bağlandığında (batarya + şarj cihazı + harici PSU) kaynakların otomatik olarak birleştirilmesini ve ters polarite korumasını sağlar. MOSFET tabanlı aktif…
  - Kanıt: `or-ing-circuit.md` § Genel Bakış

#### K17.3.2 — Devre Topolojisi

- **Sorumluluk:** «Devre Topolojisi» — or-ing-circuit.md dosyasında belgelenen bölüm.
- **Kanıt:** `or-ing-circuit.md` § Devre Topolojisi (3 bölüm başlığı)

- **K17.3.2.2 — Devre Topolojisi**
  - Sorumluluk: «Devre Topolojisi» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Devre Topolojisi
- **K17.3.2.3 — Aktif OR-ing (MOSFET)**
  - Sorumluluk: «Aktif OR-ing (MOSFET)» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Devre Topolojisi > Aktif OR-ing (MOSFET)
- **K17.3.2.4 — Çalışma Prensibi**
  - Sorumluluk: «Çalışma Prensibi» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Devre Topolojisi > Çalışma Prensibi

#### K17.3.3 — Ters Polarite Koruması

- **Sorumluluk:** «Ters Polarite Koruması» — or-ing-circuit.md dosyasında belgelenen bölüm.
- **Kanıt:** `or-ing-circuit.md` § Ters Polarite Koruması (3 bölüm başlığı)

- **K17.3.3.5 — Ters Polarite Koruması**
  - Sorumluluk: «Ters Polarite Koruması» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Ters Polarite Koruması
- **K17.3.3.6 — Koruma Devresi**
  - Sorumluluk: «Koruma Devresi» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Ters Polarite Koruması > Koruma Devresi
- **K17.3.3.7 — Ters Polarite Durumu**
  - Sorumluluk: «Ters Polarite Durumu» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Ters Polarite Koruması > Ters Polarite Durumu

#### K17.3.4 — Schottky Diyot Alternatifi (Pasif OR-ing)

- **Sorumluluk:** Sonuç: Aktif OR-ing, %95 daha düşük güç kaybı → COREMUSIC'te tercih edilen
- **Kanıt:** `or-ing-circuit.md` § Schottky Diyot Alternatifi (Pasif OR-ing) (3 bölüm başlığı)

- **K17.3.4.8 — Schottky Diyot Alternatifi (Pasif OR-ing)**
  - Sorumluluk: Sonuç: Aktif OR-ing, %95 daha düşük güç kaybı → COREMUSIC'te tercih edilen
  - Kanıt: `or-ing-circuit.md` § Schottky Diyot Alternatifi (Pasif OR-ing)
- **K17.3.4.9 — Devre**
  - Sorumluluk: «Devre» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Schottky Diyot Alternatifi (Pasif OR-ing) > Devre
- **K17.3.4.10 — Kayıp Karşılaştırması**
  - Sorumluluk: Sonuç: Aktif OR-ing, %95 daha düşük güç kaybı → COREMUSIC'te tercih edilen
  - Kanıt: `or-ing-circuit.md` § Schottky Diyot Alternatifi (Pasif OR-ing) > Kayıp Karşılaştırması

#### K17.3.5 — Öncelik Mantığı

- **Sorumluluk:** «Öncelik Mantığı» — or-ing-circuit.md dosyasında belgelenen bölüm.
- **Kanıt:** `or-ing-circuit.md` § Öncelik Mantığı (3 bölüm başlığı)

- **K17.3.5.11 — Öncelik Mantığı**
  - Sorumluluk: «Öncelik Mantığı» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Öncelik Mantığı
- **K17.3.5.12 — Kaynak Öncelik Sıralaması**
  - Sorumluluk: «Kaynak Öncelik Sıralaması» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Öncelik Mantığı > Kaynak Öncelik Sıralaması
- **K17.3.5.13 — Otomatik Geçiş Devresi**
  - Sorumluluk: «Otomatik Geçiş Devresi» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Öncelik Mantığı > Otomatik Geçiş Devresi

#### K17.3.6 — Hesaplamalar

- **Sorumluluk:** «Hesaplamalar» — or-ing-circuit.md dosyasında belgelenen bölüm.
- **Kanıt:** `or-ing-circuit.md` § Hesaplamalar (4 bölüm başlığı)

- **K17.3.6.14 — Hesaplamalar**
  - Sorumluluk: «Hesaplamalar» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Hesaplamalar
- **K17.3.6.15 — MOSFET Kayıpları (Aktif OR-ing)**
  - Sorumluluk: «MOSFET Kayıpları (Aktif OR-ing)» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Hesaplamalar > MOSFET Kayıpları (Aktif OR-ing)
- **K17.3.6.16 — Schottky Diyot Kaybı (Referans)**
  - Sorumluluk: «Schottky Diyot Kaybı (Referans)» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Hesaplamalar > Schottky Diyot Kaybı (Referans)
- **K17.3.6.17 — Faisyat Hesabı**
  - Sorumluluk: «Faisyat Hesabı» — or-ing-circuit.md dosyasında belgelenen bölüm.
  - Kanıt: `or-ing-circuit.md` § Hesaplamalar > Faisyat Hesabı

### K17.4 — LM5122 Dual Boost

**Sorumluluk:** ±35V interleaved boost: duty cycle, indüktör/kapasitör boyutlandırma, bootstrap ve kayıp analizi.
**Kanıt dosyaları:** `lm5122-dual-boost.md` — havuz 15 başlık, kullanıldı 12, seçim dışı 3.

#### K17.4.1 — Genel Bakış

- **Sorumluluk:** LM5122, Texas Instruments tarafından üretilen, 4.5V-60V giriş aralığında çalışan, senkron boost converter kontrolcüsüdür. COREMUSIC'te tek entegre ile +35V ve…
- **Kanıt:** `lm5122-dual-boost.md` § Genel Bakış (1 bölüm başlığı)

- **K17.4.1.1 — Genel Bakış**
  - Sorumluluk: LM5122, Texas Instruments tarafından üretilen, 4.5V-60V giriş aralığında çalışan, senkron boost converter kontrolcüsüdür. COREMUSIC'te tek entegre ile +35V ve -35V simetrik çıkışlar üretmek için dual…
  - Kanıt: `lm5122-dual-boost.md` § Genel Bakış

#### K17.4.2 — Devre Tasarımı

- **Sorumluluk:** «Devre Tasarımı» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
- **Kanıt:** `lm5122-dual-boost.md` § Devre Tasarımı (3 bölüm başlığı)

- **K17.4.2.2 — Devre Tasarımı**
  - Sorumluluk: «Devre Tasarımı» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Devre Tasarımı
- **K17.4.2.3 — Temel Bağlantı Şeması**
  - Sorumluluk: «Temel Bağlantı Şeması» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Devre Tasarımı > Temel Bağlantı Şeması
- **K17.4.2.4 — LM5122 Pin Konfigürasyonu**
  - Sorumluluk: «LM5122 Pin Konfigürasyonu» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Devre Tasarımı > LM5122 Pin Konfigürasyonu

#### K17.4.3 — Hesaplamalar (I)

- **Sorumluluk:** Indüktör Özellikleri: Indüktans: 10μH Satürasyon Akımı: 5A minimum
- **Kanıt:** `lm5122-dual-boost.md` § Hesaplamalar (I) (3 bölüm başlığı)

- **K17.4.3.5 — Hesaplamalar**
  - Sorumluluk: Indüktör Özellikleri: Indüktans: 10μH Satürasyon Akımı: 5A minimum
  - Kanıt: `lm5122-dual-boost.md` § Hesaplamalar
- **K17.4.3.6 — Boost Oranı (Duty Cycle)**
  - Sorumluluk: «Boost Oranı (Duty Cycle)» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Hesaplamalar > Boost Oranı (Duty Cycle)
- **K17.4.3.7 — Indüktör Boyutlandırma**
  - Sorumluluk: Indüktör Özellikleri: Indüktans: 10μH Satürasyon Akımı: 5A minimum
  - Kanıt: `lm5122-dual-boost.md` § Hesaplamalar > Indüktör Boyutlandırma

#### K17.4.4 — Hesaplamalar (II)

- **Sorumluluk:** Kapasitör Özellikleri: Kapasite: 470μF (iki paralel) ESR: <20mΩ @ 100kHz
- **Kanıt:** `lm5122-dual-boost.md` § Hesaplamalar (II) (3 bölüm başlığı)

- **K17.4.4.8 — Çıkış Kapasitörü**
  - Sorumluluk: Kapasitör Özellikleri: Kapasite: 470μF (iki paralel) ESR: <20mΩ @ 100kHz
  - Kanıt: `lm5122-dual-boost.md` § Hesaplamalar > Çıkış Kapasitörü
- **K17.4.4.9 — Anahtarlama Kayıpları**
  - Sorumluluk: «Anahtarlama Kayıpları» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Hesaplamalar > Anahtarlama Kayıpları
- **K17.4.4.10 — Bootstrap Kapasitörü**
  - Sorumluluk: «Bootstrap Kapasitörü» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Hesaplamalar > Bootstrap Kapasitörü

#### K17.4.5 — Kayıp Analizi

- **Sorumluluk:** «Kayıp Analizi» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
- **Kanıt:** `lm5122-dual-boost.md` § Kayıp Analizi (1 bölüm başlığı)

- **K17.4.5.11 — Kayıp Analizi**
  - Sorumluluk: «Kayıp Analizi» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Kayıp Analizi

#### K17.4.6 — Spesifikasyonlar

- **Sorumluluk:** «Spesifikasyonlar» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
- **Kanıt:** `lm5122-dual-boost.md` § Spesifikasyonlar (1 bölüm başlığı)

- **K17.4.6.12 — Spesifikasyonlar**
  - Sorumluluk: «Spesifikasyonlar» — lm5122-dual-boost.md dosyasında belgelenen bölüm.
  - Kanıt: `lm5122-dual-boost.md` § Spesifikasyonlar

### K17.5 — Regülasyon Rail'leri

**Sorumluluk:** ±15V LDO (LM317/LM337), +12V, +5V/+3.3V buck rail'leri; ripple ve termal denge.
**Kanıt dosyaları:** `voltage-regulation.md` — havuz 17 başlık, kullanıldı 13, seçim dışı 4.

#### K17.5.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC'in çoklu güç rail'leri için LDO (Low Dropout) ve buck converter kombinasyonu kullanılır. ±15V ve ±12V rail'leri için lineer regülatörler tercih…
- **Kanıt:** `voltage-regulation.md` § Genel Bakış (1 bölüm başlığı)

- **K17.5.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC'in çoklu güç rail'leri için LDO (Low Dropout) ve buck converter kombinasyonu kullanılır. ±15V ve ±12V rail'leri için lineer regülatörler tercih edilirken, +5V ve +3.3V için yüksek verimli…
  - Kanıt: `voltage-regulation.md` § Genel Bakış

#### K17.5.2 — Regülasyon Mimarisi

- **Sorumluluk:** «Regülasyon Mimarisi» — voltage-regulation.md dosyasında belgelenen bölüm.
- **Kanıt:** `voltage-regulation.md` § Regülasyon Mimarisi (1 bölüm başlığı)

- **K17.5.2.2 — Regülasyon Mimarisi**
  - Sorumluluk: «Regülasyon Mimarisi» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Regülasyon Mimarisi

#### K17.5.3 — LDO Regülatör Detayları

- **Sorumluluk:** «LDO Regülatör Detayları» — voltage-regulation.md dosyasında belgelenen bölüm.
- **Kanıt:** `voltage-regulation.md` § LDO Regülatör Detayları (4 bölüm başlığı)

- **K17.5.3.3 — LDO Regülatör Detayları**
  - Sorumluluk: «LDO Regülatör Detayları» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § LDO Regülatör Detayları
- **K17.5.3.4 — +15V LDO (LM317)**
  - Sorumluluk: «+15V LDO (LM317)» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § LDO Regülatör Detayları > +15V LDO (LM317)
- **K17.5.3.5 — -15V LDO (LM337)**
  - Sorumluluk: «-15V LDO (LM337)» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § LDO Regülatör Detayları > -15V LDO (LM337)
- **K17.5.3.6 — +12V LDO (LM7812)**
  - Sorumluluk: «+12V LDO (LM7812)» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § LDO Regülatör Detayları > +12V LDO (LM7812)

#### K17.5.4 — Buck Converter Detayları

- **Sorumluluk:** «Buck Converter Detayları» — voltage-regulation.md dosyasında belgelenen bölüm.
- **Kanıt:** `voltage-regulation.md` § Buck Converter Detayları (3 bölüm başlığı)

- **K17.5.4.7 — Buck Converter Detayları**
  - Sorumluluk: «Buck Converter Detayları» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Buck Converter Detayları
- **K17.5.4.8 — +5V Buck (TPS54331)**
  - Sorumluluk: «+5V Buck (TPS54331)» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Buck Converter Detayları > +5V Buck (TPS54331)
- **K17.5.4.9 — +3.3V Buck (TPS62A01)**
  - Sorumluluk: «+3.3V Buck (TPS62A01)» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Buck Converter Detayları > +3.3V Buck (TPS62A01)

#### K17.5.5 — Çıkış Ripple Analizi

- **Sorumluluk:** «Çıkış Ripple Analizi» — voltage-regulation.md dosyasında belgelenen bölüm.
- **Kanıt:** `voltage-regulation.md` § Çıkış Ripple Analizi (1 bölüm başlığı)

- **K17.5.5.10 — Çıkış Ripple Analizi**
  - Sorumluluk: «Çıkış Ripple Analizi» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Çıkış Ripple Analizi

#### K17.5.6 — Termal Analiz

- **Sorumluluk:** «Termal Analiz» — voltage-regulation.md dosyasında belgelenen bölüm.
- **Kanıt:** `voltage-regulation.md` § Termal Analiz (3 bölüm başlığı)

- **K17.5.6.11 — Termal Analiz**
  - Sorumluluk: «Termal Analiz» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Termal Analiz
- **K17.5.6.12 — LDO Kayıpları**
  - Sorumluluk: «LDO Kayıpları» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Termal Analiz > LDO Kayıpları
- **K17.5.6.13 — Buck Converter Kayıpları**
  - Sorumluluk: «Buck Converter Kayıpları» — voltage-regulation.md dosyasında belgelenen bölüm.
  - Kanıt: `voltage-regulation.md` § Termal Analiz > Buck Converter Kayıpları

### K17.6 — Yumuşak Başlangıç & Sıralama

**Sorumluluk:** Inrush sınırlama, analog/dijital soft start, rail enable/PGOOD mantığı ve kapanma sıralaması.
**Kanıt dosyaları:** `soft-start.md`, `power-sequencing.md` — havuz 34 başlık, kullanıldı 27, seçim dışı 7.

#### K17.6.1 — Genel Bakış / Çalışma Prensibi

- **Sorumluluk:** Soft start devresi, COREMUSIC'in açılış anındaki yüksek giriş akımını (inrush current) sınırlayarak bileşenleri ve güç kaynağını korur. Bulk kapasitörlerin…
- **Kanıt:** `soft-start.md`, `power-sequencing.md` § Genel Bakış / Çalışma Prensibi (5 bölüm başlığı)

- **K17.6.1.1 — Genel Bakış**
  - Sorumluluk: Soft start devresi, COREMUSIC'in açılış anındaki yüksek giriş akımını (inrush current) sınırlayarak bileşenleri ve güç kaynağını korur. Bulk kapasitörlerin şarj olmasını kontrollü bir şekilde…
  - Kanıt: `soft-start.md` § Genel Bakış
- **K17.6.1.2 — Genel Bakış**
  - Sorumluluk: Güç sıralama devresi, COREMUSIC'in tüm güç rail'lerinin doğru zamanda açılmasını ve kapanmasını sağlar. Yanlış sırada açılan rail'ler, latch-up, aşırı akım veya bileşen hasarına neden olabilir.…
  - Kanıt: `power-sequencing.md` § Genel Bakış
- **K17.6.1.3 — Çalışma Prensibi**
  - Sorumluluk: «Çalışma Prensibi» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Çalışma Prensibi
- **K17.6.1.4 — Inrush Current Problemi**
  - Sorumluluk: «Inrush Current Problemi» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Çalışma Prensibi > Inrush Current Problemi
- **K17.6.1.5 — Soft Start ile**
  - Sorumluluk: «Soft Start ile» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Çalışma Prensibi > Soft Start ile

#### K17.6.2 — Devre Tasarımı

- **Sorumluluk:** «Devre Tasarımı» — soft-start.md dosyasında belgelenen bölüm.
- **Kanıt:** `soft-start.md` § Devre Tasarımı (4 bölüm başlığı)

- **K17.6.2.6 — Devre Tasarımı**
  - Sorumluluk: «Devre Tasarımı» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Devre Tasarımı
- **K17.6.2.7 — Analog Soft Start (RC Tabanlı)**
  - Sorumluluk: «Analog Soft Start (RC Tabanlı)» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Devre Tasarımı > Analog Soft Start (RC Tabanlı)
- **K17.6.2.8 — Soft Start Voltaj Profili**
  - Sorumluluk: «Soft Start Voltaj Profili» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Devre Tasarımı > Soft Start Voltaj Profili
- **K17.6.2.9 — Dijital Soft Start (MCU Tabanlı)**
  - Sorumluluk: «Dijital Soft Start (MCU Tabanlı)» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Devre Tasarımı > Dijital Soft Start (MCU Tabanlı)

#### K17.6.3 — Rail Sıralaması (Power Sequencing) / Sequencing Devresi / Inrush Hesaplamaları

- **Sorumluluk:** «Rail Sıralaması (Power Sequencing)» — soft-start.md dosyasında belgelenen bölüm.
- **Kanıt:** `soft-start.md` § Rail Sıralaması (Power Sequencing) / Sequencing Devresi / Inrush Hesaplamaları (7 bölüm başlığı)

- **K17.6.3.10 — Rail Sıralaması (Power Sequencing)**
  - Sorumluluk: «Rail Sıralaması (Power Sequencing)» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Rail Sıralaması (Power Sequencing)
- **K17.6.3.11 — Sequencing Devresi**
  - Sorumluluk: «Sequencing Devresi» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Sequencing Devresi
- **K17.6.3.12 — RC Delay Ağı**
  - Sorumluluk: «RC Delay Ağı» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Sequencing Devresi > RC Delay Ağı
- **K17.6.3.13 — Sequencing Şeması**
  - Sorumluluk: «Sequencing Şeması» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Sequencing Devresi > Sequencing Şeması
- **K17.6.3.14 — Inrush Hesaplamaları**
  - Sorumluluk: «Inrush Hesaplamaları» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Inrush Hesaplamaları
- **K17.6.3.15 — Bulk Kapasitör Şarj Akımı**
  - Sorumluluk: «Bulk Kapasitör Şarj Akımı» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Inrush Hesaplamaları > Bulk Kapasitör Şarj Akımı
- **K17.6.3.16 — Enerji Hesabı**
  - Sorumluluk: «Enerji Hesabı» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Inrush Hesaplamaları > Enerji Hesabı

#### K17.6.4 — Koruma Devresi / Spesifikasyonlar / Bileşen Seçimi

- **Sorumluluk:** «Koruma Devresi» — soft-start.md dosyasında belgelenen bölüm.
- **Kanıt:** `soft-start.md` § Koruma Devresi / Spesifikasyonlar / Bileşen Seçimi (4 bölüm başlığı)

- **K17.6.4.17 — Koruma Devresi**
  - Sorumluluk: «Koruma Devresi» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Koruma Devresi
- **K17.6.4.18 — Aşırı Akım Kilidi**
  - Sorumluluk: «Aşırı Akım Kilidi» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Koruma Devresi > Aşırı Akım Kilidi
- **K17.6.4.19 — Spesifikasyonlar**
  - Sorumluluk: «Spesifikasyonlar» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Spesifikasyonlar
- **K17.6.4.20 — Bileşen Seçimi**
  - Sorumluluk: «Bileşen Seçimi» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Bileşen Seçimi

#### K17.6.5 — PCB Tasarım Notları / Bağımlılıklar / Durum: Implementasyon / Sıralama Mimarisi

- **Sorumluluk:** RC Ağı: Soft-start bileşenleri LM5122 EN pinine yakın Sequencing: Her rail için ayrı delay ağı
- **Kanıt:** `soft-start.md`, `power-sequencing.md` § PCB Tasarım Notları / Bağımlılıklar / Durum: Implementasyon / Sıralama Mimarisi (4 bölüm başlığı)

- **K17.6.5.21 — PCB Tasarım Notları**
  - Sorumluluk: RC Ağı: Soft-start bileşenleri LM5122 EN pinine yakın Sequencing: Her rail için ayrı delay ağı
  - Kanıt: `soft-start.md` § PCB Tasarım Notları
- **K17.6.5.22 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — soft-start.md dosyasında belgelenen bölüm.
  - Kanıt: `soft-start.md` § Bağımlılıklar
- **K17.6.5.23 — Durum: Implementasyon**
  - Sorumluluk: ✅ Analog soft start devresi (RC) tasarlandı ✅ Dijital soft start PWM kodu yazıldı ✅ Rail sequencing mantığı belirlendi
  - Kanıt: `soft-start.md` § Durum: Implementasyon
- **K17.6.5.24 — Sıralama Mimarisi**
  - Sorumluluk: «Sıralama Mimarisi» — power-sequencing.md dosyasında belgelenen bölüm.
  - Kanıt: `power-sequencing.md` § Sıralama Mimarisi

#### K17.6.6 — Enable Devresi

- **Sorumluluk:** «Enable Devresi» — power-sequencing.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-sequencing.md` § Enable Devresi (3 bölüm başlığı)

- **K17.6.6.25 — Enable Devresi**
  - Sorumluluk: «Enable Devresi» — power-sequencing.md dosyasında belgelenen bölüm.
  - Kanıt: `power-sequencing.md` § Enable Devresi
- **K17.6.6.26 — P-MOSFET Enable**
  - Sorumluluk: «P-MOSFET Enable» — power-sequencing.md dosyasında belgelenen bölüm.
  - Kanıt: `power-sequencing.md` § Enable Devresi > P-MOSFET Enable
- **K17.6.6.27 — Optocoupler Enable (Yüksek Gerilim Rail'ler)**
  - Sorumluluk: «Optocoupler Enable (Yüksek Gerilim Rail'ler)» — power-sequencing.md dosyasında belgelenen bölüm.
  - Kanıt: `power-sequencing.md` § Enable Devresi > Optocoupler Enable (Yüksek Gerilim Rail'ler)

### K17.7 — Akım Ölçüm & Koruma

**Sorumluluk:** ACS711/INA219 ölçüm, oversampling + Kalman filtreleme ve aşırı akım koruma eşiği.
**Kanıt dosyaları:** `current-sensing.md` — havuz 19 başlık, kullanıldı 15, seçim dışı 4.

#### K17.7.1 — Genel Bakış

- **Sorumluluk:** Akım ölçme devresi, COREMUSIC'in tüm güç rail'lerindeki akımı izleyerek aşırı akım koruması, güç tüketimi izleme ve batarya SOC hesaplamasını sağlar. ACS711…
- **Kanıt:** `current-sensing.md` § Genel Bakış (1 bölüm başlığı)

- **K17.7.1.1 — Genel Bakış**
  - Sorumluluk: Akım ölçme devresi, COREMUSIC'in tüm güç rail'lerindeki akımı izleyerek aşırı akım koruması, güç tüketimi izleme ve batarya SOC hesaplamasını sağlar. ACS711 hall-effect sensör galvanik izolasyon…
  - Kanıt: `current-sensing.md` § Genel Bakış

#### K17.7.2 — Akım Ölçüm Mimarisi

- **Sorumluluk:** «Akım Ölçüm Mimarisi» — current-sensing.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-sensing.md` § Akım Ölçüm Mimarisi (1 bölüm başlığı)

- **K17.7.2.2 — Akım Ölçüm Mimarisi**
  - Sorumluluk: «Akım Ölçüm Mimarisi» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Akım Ölçüm Mimarisi

#### K17.7.3 — ACS711 Hall-Effect Sensör

- **Sorumluluk:** «ACS711 Hall-Effect Sensör» — current-sensing.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-sensing.md` § ACS711 Hall-Effect Sensör (4 bölüm başlığı)

- **K17.7.3.3 — ACS711 Hall-Effect Sensör**
  - Sorumluluk: «ACS711 Hall-Effect Sensör» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ACS711 Hall-Effect Sensör
- **K17.7.3.4 — Çalışma Prensibi**
  - Sorumluluk: «Çalışma Prensibi» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ACS711 Hall-Effect Sensör > Çalışma Prensibi
- **K17.7.3.5 — ACS711 Devre Bağlantısı**
  - Sorumluluk: «ACS711 Devre Bağlantısı» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ACS711 Hall-Effect Sensör > ACS711 Devre Bağlantısı
- **K17.7.3.6 — ADC Dönüşümü**
  - Sorumluluk: «ADC Dönüşümü» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ACS711 Hall-Effect Sensör > ADC Dönüşümü

#### K17.7.4 — Shunt Resistor Ölçümü

- **Sorumluluk:** «Shunt Resistor Ölçümü» — current-sensing.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-sensing.md` § Shunt Resistor Ölçümü (3 bölüm başlığı)

- **K17.7.4.7 — Shunt Resistor Ölçümü**
  - Sorumluluk: «Shunt Resistor Ölçümü» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Shunt Resistor Ölçümü
- **K17.7.4.8 — INA219 Bazlı Ölçüm**
  - Sorumluluk: «INA219 Bazlı Ölçüm» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Shunt Resistor Ölçümü > INA219 Bazlı Ölçüm
- **K17.7.4.9 — Shunt Hesaplamaları**
  - Sorumluluk: «Shunt Hesaplamaları» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Shunt Resistor Ölçümü > Shunt Hesaplamaları

#### K17.7.5 — Aşırı Akım Koruması

- **Sorumluluk:** «Aşırı Akım Koruması» — current-sensing.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-sensing.md` § Aşırı Akım Koruması (3 bölüm başlığı)

- **K17.7.5.10 — Aşırı Akım Koruması**
  - Sorumluluk: «Aşırı Akım Koruması» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Aşırı Akım Koruması
- **K17.7.5.11 — Koruma Eşikleri**
  - Sorumluluk: «Koruma Eşikleri» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Aşırı Akım Koruması > Koruma Eşikleri
- **K17.7.5.12 — Koruma Devresi Şeması**
  - Sorumluluk: «Koruma Devresi Şeması» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § Aşırı Akım Koruması > Koruma Devresi Şeması

#### K17.7.6 — ADC Örnekleme ve Filtreleme

- **Sorumluluk:** «ADC Örnekleme ve Filtreleme» — current-sensing.md dosyasında belgelenen bölüm.
- **Kanıt:** `current-sensing.md` § ADC Örnekleme ve Filtreleme (3 bölüm başlığı)

- **K17.7.6.13 — ADC Örnekleme ve Filtreleme**
  - Sorumluluk: «ADC Örnekleme ve Filtreleme» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ADC Örnekleme ve Filtreleme
- **K17.7.6.14 — Oversampling**
  - Sorumluluk: «Oversampling» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ADC Örnekleme ve Filtreleme > Oversampling
- **K17.7.6.15 — Kalman Filter Parametreleri**
  - Sorumluluk: «Kalman Filter Parametreleri» — current-sensing.md dosyasında belgelenen bölüm.
  - Kanıt: `current-sensing.md` § ADC Örnekleme ve Filtreleme > Kalman Filter Parametreleri

### K17.8 — EMC, Filtreleme & Depolama

**Sorumluluk:** X/Y-CMC giriş-çıkış filtreleri, ESD/TVS, decoupling stratejisi ve bulk kapasitör ripple hesabı.
**Kanıt dosyaları:** `emc-filtering.md`, `decoupling-strategy.md`, `bulk-capacitors.md` — havuz 53 başlık, kullanıldı 42, seçim dışı 11.

#### K17.8.1 — Genel Bakış / EMC Mimarisi

- **Sorumluluk:** EMC (Electromagnetic Compatibility) filtreleme, COREMUSIC'in hem kendi içinde hem de dış dünya ile uyumlu çalışmasını sağlar. Anahtarlama power supply'lerinden…
- **Kanıt:** `emc-filtering.md`, `decoupling-strategy.md`, `bulk-capacitors.md` § Genel Bakış / EMC Mimarisi (4 bölüm başlığı)

- **K17.8.1.1 — Genel Bakış**
  - Sorumluluk: EMC (Electromagnetic Compatibility) filtreleme, COREMUSIC'in hem kendi içinde hem de dış dünya ile uyumlu çalışmasını sağlar. Anahtarlama power supply'lerinden kaynaklanan EMI'yi (Electromagnetic…
  - Kanıt: `emc-filtering.md` § Genel Bakış
- **K17.8.1.2 — Genel Bakış**
  - Sorumluluk: Decoupling kapasitörleri, COREMUSIC'in tüm aktif bileşenlerinin (MCU, FPGA, Op-Amp, DAC) güç pin'lerindeki anlık akım taleplerini karşılar. Yüksek frekanslı gürültüyü bastırır, güç rail'lerindeki…
  - Kanıt: `decoupling-strategy.md` § Genel Bakış
- **K17.8.1.3 — Genel Bakış**
  - Sorumluluk: Bulk kapasitör bankı, COREMUSIC'in tüm güç rail'lerindeki enerji depolama ve ripple akımı absorpsiyonunu sağlar. Anahtarlama converter'ların çıkış filtrelemesinde, dinamik yük değişimlerinde gerilim…
  - Kanıt: `bulk-capacitors.md` § Genel Bakış
- **K17.8.1.4 — EMC Mimarisi**
  - Sorumluluk: «EMC Mimarisi» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § EMC Mimarisi

#### K17.8.2 — Giriş EMC Filtresi / Çıkış EMC Filtresi

- **Sorumluluk:** «Giriş EMC Filtresi» — emc-filtering.md dosyasında belgelenen bölüm.
- **Kanıt:** `emc-filtering.md` § Giriş EMC Filtresi / Çıkış EMC Filtresi (7 bölüm başlığı)

- **K17.8.2.5 — Giriş EMC Filtresi**
  - Sorumluluk: «Giriş EMC Filtresi» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Giriş EMC Filtresi
- **K17.8.2.6 — X-Capacitor (Differential Mode)**
  - Sorumluluk: «X-Capacitor (Differential Mode)» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Giriş EMC Filtresi > X-Capacitor (Differential Mode)
- **K17.8.2.7 — Common Mode Choke**
  - Sorumluluk: «Common Mode Choke» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Giriş EMC Filtresi > Common Mode Choke
- **K17.8.2.8 — Y-Capacitor (Common Mode)**
  - Sorumluluk: «Y-Capacitor (Common Mode)» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Giriş EMC Filtresi > Y-Capacitor (Common Mode)
- **K17.8.2.9 — Çıkış EMC Filtresi**
  - Sorumluluk: «Çıkış EMC Filtresi» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Çıkış EMC Filtresi
- **K17.8.2.10 — LC Output Filter**
  - Sorumluluk: «LC Output Filter» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Çıkış EMC Filtresi > LC Output Filter
- **K17.8.2.11 — Ferrite Bead**
  - Sorumluluk: «Ferrite Bead» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Çıkış EMC Filtresi > Ferrite Bead

#### K17.8.3 — Frekans Spektrumu Analizi / ESD Koruma (+3 bölüm) (+6 bölüm)

- **Sorumluluk:** «Frekans Spektrumu Analizi» — emc-filtering.md dosyasında belgelenen bölüm.
- **Kanıt:** `emc-filtering.md`, `decoupling-strategy.md` § Frekans Spektrumu Analizi / ESD Koruma (+3 bölüm) (+6 bölüm) (10 bölüm başlığı)

- **K17.8.3.12 — Frekans Spektrumu Analizi**
  - Sorumluluk: «Frekans Spektrumu Analizi» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Frekans Spektrumu Analizi
- **K17.8.3.13 — ESD Koruma**
  - Sorumluluk: «ESD Koruma» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § ESD Koruma
- **K17.8.3.14 — TVS Diyot Ağı**
  - Sorumluluk: «TVS Diyot Ağı» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § ESD Koruma > TVS Diyot Ağı
- **K17.8.3.15 — Ferrite Seçimi**
  - Sorumluluk: «Ferrite Seçimi» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Ferrite Seçimi
- **K17.8.3.16 — PCB Layout EMC Kuralları**
  - Sorumluluk: «PCB Layout EMC Kuralları» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § PCB Layout EMC Kuralları
- **K17.8.3.17 — Spesifikasyonlar**
  - Sorumluluk: «Spesifikasyonlar» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Spesifikasyonlar
- **K17.8.3.18 — Spesifikasyonlar**
  - Sorumluluk: «Spesifikasyonlar» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Spesifikasyonlar
- **K17.8.3.19 — Bileşen Listesi**
  - Sorumluluk: «Bileşen Listesi» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Bileşen Listesi
- **K17.8.3.20 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — emc-filtering.md dosyasında belgelenen bölüm.
  - Kanıt: `emc-filtering.md` § Bağımlılıklar
- **K17.8.3.21 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Bağımlılıklar

#### K17.8.4 — Durum: Implementasyon / Decoupling Mimarisi / Frekans Tepkisi

- **Sorumluluk:** ✅ Giriş EMC filtresi tasarımı tamamlandı (X, CM, Y caps) ✅ Çıkış LC filtreleri hesaplandı ve seçildi
- **Kanıt:** `emc-filtering.md`, `decoupling-strategy.md` § Durum: Implementasyon / Decoupling Mimarisi / Frekans Tepkisi (6 bölüm başlığı)

- **K17.8.4.22 — Durum: Implementasyon**
  - Sorumluluk: ✅ Giriş EMC filtresi tasarımı tamamlandı (X, CM, Y caps) ✅ Çıkış LC filtreleri hesaplandı ve seçildi
  - Kanıt: `emc-filtering.md` § Durum: Implementasyon
- **K17.8.4.23 — Durum: Implementasyon**
  - Sorumluluk: ✅ Decoupling stratejisi (4 katman) belirlendi ✅ Bileşen bazlı decoupling haritası oluşturuldu
  - Kanıt: `decoupling-strategy.md` § Durum: Implementasyon
- **K17.8.4.24 — Decoupling Mimarisi**
  - Sorumluluk: «Decoupling Mimarisi» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Decoupling Mimarisi
- **K17.8.4.25 — Frekans Tepkisi**
  - Sorumluluk: «Frekans Tepkisi» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Frekans Tepkisi
- **K17.8.4.26 — Impedance vs Frekans**
  - Sorumluluk: «Impedance vs Frekans» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Frekans Tepkisi > Impedance vs Frekans
- **K17.8.4.27 — Kapasitör Tepki Süresi**
  - Sorumluluk: «Kapasitör Tepki Süresi» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Frekans Tepkisi > Kapasitör Tepki Süresi

#### K17.8.5 — Bileşen Bazlı Decoupling (+2 bölüm)

- **Sorumluluk:** «Bileşen Bazlı Decoupling» — decoupling-strategy.md dosyasında belgelenen bölüm.
- **Kanıt:** `decoupling-strategy.md` § Bileşen Bazlı Decoupling (+2 bölüm) (8 bölüm başlığı)

- **K17.8.5.28 — Bileşen Bazlı Decoupling**
  - Sorumluluk: «Bileşen Bazlı Decoupling» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Bileşen Bazlı Decoupling
- **K17.8.5.29 — STM32L4 MCU**
  - Sorumluluk: «STM32L4 MCU» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Bileşen Bazlı Decoupling > STM32L4 MCU
- **K17.8.5.30 — FPGA (Xilinx Spartan-7)**
  - Sorumluluk: «FPGA (Xilinx Spartan-7)» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Bileşen Bazlı Decoupling > FPGA (Xilinx Spartan-7)
- **K17.8.5.31 — Op-Amp (LM4562)**
  - Sorumluluk: «Op-Amp (LM4562)» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Bileşen Bazlı Decoupling > Op-Amp (LM4562)
- **K17.8.5.32 — PCB Decoupling Kuralları**
  - Sorumluluk: «PCB Decoupling Kuralları» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § PCB Decoupling Kuralları
- **K17.8.5.33 — DC Bias ve Sıcaklık Düzeltmeleri**
  - Sorumluluk: «DC Bias ve Sıcaklık Düzeltmeleri» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § DC Bias ve Sıcaklık Düzeltmeleri
- **K17.8.5.34 — Ceramic DC Bias Kaybı**
  - Sorumluluk: «Ceramic DC Bias Kaybı» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § DC Bias ve Sıcaklık Düzeltmeleri > Ceramic DC Bias Kaybı
- **K17.8.5.35 — Sıcaklık Katsayısı**
  - Sorumluluk: «Sıcaklık Katsayısı» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § DC Bias ve Sıcaklık Düzeltmeleri > Sıcaklık Katsayısı

#### K17.8.6 — Toplam Decoupling Bileşen Sayısı / Kapasitör Mimarisi (+2 bölüm) (+3 bölüm)

- **Sorumluluk:** «Toplam Decoupling Bileşen Sayısı» — decoupling-strategy.md dosyasında belgelenen bölüm.
- **Kanıt:** `decoupling-strategy.md`, `bulk-capacitors.md` § Toplam Decoupling Bileşen Sayısı / Kapasitör Mimarisi (+2 bölüm) (+3 bölüm) (7 bölüm başlığı)

- **K17.8.6.36 — Toplam Decoupling Bileşen Sayısı**
  - Sorumluluk: «Toplam Decoupling Bileşen Sayısı» — decoupling-strategy.md dosyasında belgelenen bölüm.
  - Kanıt: `decoupling-strategy.md` § Toplam Decoupling Bileşen Sayısı
- **K17.8.6.37 — Kapasitör Mimarisi**
  - Sorumluluk: «Kapasitör Mimarisi» — bulk-capacitors.md dosyasında belgelenen bölüm.
  - Kanıt: `bulk-capacitors.md` § Kapasitör Mimarisi
- **K17.8.6.38 — Kapasitör Seçim Kriterleri**
  - Sorumluluk: «Kapasitör Seçim Kriterleri» — bulk-capacitors.md dosyasında belgelenen bölüm.
  - Kanıt: `bulk-capacitors.md` § Kapasitör Seçim Kriterleri
- **K17.8.6.39 — Elektrolitik vs Ceramic vs Polymer**
  - Sorumluluk: «Elektrolitik vs Ceramic vs Polymer» — bulk-capacitors.md dosyasında belgelenen bölüm.
  - Kanıt: `bulk-capacitors.md` § Kapasitör Seçim Kriterleri > Elektrolitik vs Ceramic vs Polymer
- **K17.8.6.40 — Ripple Akımı Hesaplamaları**
  - Sorumluluk: «Ripple Akımı Hesaplamaları» — bulk-capacitors.md dosyasında belgelenen bölüm.
  - Kanıt: `bulk-capacitors.md` § Ripple Akımı Hesaplamaları
- **K17.8.6.41 — +35V Boost Çıkış Ripple**
  - Sorumluluk: «+35V Boost Çıkış Ripple» — bulk-capacitors.md dosyasında belgelenen bölüm.
  - Kanıt: `bulk-capacitors.md` § Ripple Akımı Hesaplamaları > +35V Boost Çıkış Ripple
- **K17.8.6.42 — +5V Buck Çıkış Ripple**
  - Sorumluluk: «+5V Buck Çıkış Ripple» — bulk-capacitors.md dosyasında belgelenen bölüm.
  - Kanıt: `bulk-capacitors.md` § Ripple Akımı Hesaplamaları > +5V Buck Çıkış Ripple

### K17.9 — Verimlilik & Termal Yönetim

**Sorumluluk:** Boost/LDO kayıp analizi, ölçüm yöntemi, güç tüketim profili, NTC izleme ve güç katı termal tasarımı.
**Kanıt dosyaları:** `power-efficiency.md`, `thermal-management-power.md` — havuz 41 başlık, kullanıldı 33, seçim dışı 8.

#### K17.9.1 — Genel Bakış / Verimlilik Mimarisi (+3 bölüm)

- **Sorumluluk:** Güç verimliliği, COREMUSIC'in toplam güç tüketimini minimize ederek batarya ömrünü uzatır ve termal yönetimi kolaylaştırır. Her güç dönüşüm aşaması (boost,…
- **Kanıt:** `power-efficiency.md`, `thermal-management-power.md` § Genel Bakış / Verimlilik Mimarisi (+3 bölüm) (7 bölüm başlığı)

- **K17.9.1.1 — Genel Bakış**
  - Sorumluluk: Güç verimliliği, COREMUSIC'in toplam güç tüketimini minimize ederek batarya ömrünü uzatır ve termal yönetimi kolaylaştırır. Her güç dönüşüm aşaması (boost, buck, LDO) için kayıp analizi yapılarak…
  - Kanıt: `power-efficiency.md` § Genel Bakış
- **K17.9.1.2 — Genel Bakış**
  - Sorumluluk: Termal yönetim, COREMUSIC'in güç aşama bileşenlerinin (MOSFET, indüktör, LDO, Sense resistor) güvenli sıcaklık aralığında çalışmasını sağlar. Aşırı sıcaklık, bileşen ömrünü kısaltır, verimliliği…
  - Kanıt: `thermal-management-power.md` § Genel Bakış
- **K17.9.1.3 — Verimlilik Mimarisi**
  - Sorumluluk: «Verimlilik Mimarisi» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Verimlilik Mimarisi
- **K17.9.1.4 — Kayıp Analizi**
  - Sorumluluk: «Kayıp Analizi» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Kayıp Analizi
- **K17.9.1.5 — Boost Converter Kayıpları (+35V)**
  - Sorumluluk: «Boost Converter Kayıpları (+35V)» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Kayıp Analizi > Boost Converter Kayıpları (+35V)
- **K17.9.1.6 — LDO Kayıpları**
  - Sorumluluk: «LDO Kayıpları» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Kayıp Analizi > LDO Kayıpları
- **K17.9.1.7 — Ağırlıklı Ortalama Verimlilik**
  - Sorumluluk: «Ağırlıklı Ortalama Verimlilik» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Ağırlıklı Ortalama Verimlilik

#### K17.9.2 — Optimizasyon Stratejileri

- **Sorumluluk:** «Optimizasyon Stratejileri» — power-efficiency.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-efficiency.md` § Optimizasyon Stratejileri (5 bölüm başlığı)

- **K17.9.2.8 — Optimizasyon Stratejileri**
  - Sorumluluk: «Optimizasyon Stratejileri» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Optimizasyon Stratejileri
- **K17.9.2.9 — 1. Pre-Regulator Kullanımı**
  - Sorumluluk: «1. Pre-Regulator Kullanımı» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Optimizasyon Stratejileri > 1. Pre-Regulator Kullanımı
- **K17.9.2.10 — 2. Switching Pre-Regulator**
  - Sorumluluk: «2. Switching Pre-Regulator» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Optimizasyon Stratejileri > 2. Switching Pre-Regulator
- **K17.9.2.11 — 3. Synchronous Rectification**
  - Sorumluluk: «3. Synchronous Rectification» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Optimizasyon Stratejileri > 3. Synchronous Rectification
- **K17.9.2.12 — 4. Düşük DCR Indüktörler**
  - Sorumluluk: «4. Düşük DCR Indüktörler» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Optimizasyon Stratejileri > 4. Düşük DCR Indüktörler

#### K17.9.3 — Verimlilik Ölçüm Methodu (+3 bölüm)

- **Sorumluluk:** «Verimlilik Ölçüm Methodu» — power-efficiency.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-efficiency.md` § Verimlilik Ölçüm Methodu (+3 bölüm) (7 bölüm başlığı)

- **K17.9.3.13 — Verimlilik Ölçüm Methodu**
  - Sorumluluk: «Verimlilik Ölçüm Methodu» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Verimlilik Ölçüm Methodu
- **K17.9.3.14 — Test Setup**
  - Sorumluluk: «Test Setup» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Verimlilik Ölçüm Methodu > Test Setup
- **K17.9.3.15 — Ölçüm Prosedürü**
  - Sorumluluk: «Ölçüm Prosedürü» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Verimlilik Ölçüm Methodu > Ölçüm Prosedürü
- **K17.9.3.16 — Load vs Verimlilik Grafiği**
  - Sorumluluk: «Load vs Verimlilik Grafiği» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Load vs Verimlilik Grafiği
- **K17.9.3.17 — Sıcaklık Etkisi**
  - Sorumluluk: «Sıcaklık Etkisi» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Sıcaklık Etkisi
- **K17.9.3.18 — Güç Tüketim Profili**
  - Sorumluluk: «Güç Tüketim Profili» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Güç Tüketim Profili
- **K17.9.3.19 — Durum Bazlı Tüketim**
  - Sorumluluk: «Durum Bazlı Tüketim» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Güç Tüketim Profili > Durum Bazlı Tüketim

#### K17.9.4 — Batarya Ömür Hesabı / Spesifikasyonlar / Optimizasyon Önerileri / Bağımlılıklar

- **Sorumluluk:** «Batarya Ömür Hesabı» — power-efficiency.md dosyasında belgelenen bölüm.
- **Kanıt:** `power-efficiency.md` § Batarya Ömür Hesabı / Spesifikasyonlar / Optimizasyon Önerileri / Bağımlılıklar (4 bölüm başlığı)

- **K17.9.4.20 — Batarya Ömür Hesabı**
  - Sorumluluk: «Batarya Ömür Hesabı» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Batarya Ömür Hesabı
- **K17.9.4.21 — Spesifikasyonlar**
  - Sorumluluk: «Spesifikasyonlar» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Spesifikasyonlar
- **K17.9.4.22 — Optimizasyon Önerileri**
  - Sorumluluk: Öncelik: +15V/-15V LDO'ları için pre-regulator ekle İkincil: +12V/-12V için pre-regulator optimize et
  - Kanıt: `power-efficiency.md` § Optimizasyon Önerileri
- **K17.9.4.23 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — power-efficiency.md dosyasında belgelenen bölüm.
  - Kanıt: `power-efficiency.md` § Bağımlılıklar

#### K17.9.5 — Durum: Implementasyon / Termal Mimarisi / Termal Model

- **Sorumluluk:** ✅ Boost converter verimlilik analizi tamamlandı (%92) ✅ LDO kayıp analizi yapıldı ve optimizasyon belirlendi
- **Kanıt:** `power-efficiency.md`, `thermal-management-power.md` § Durum: Implementasyon / Termal Mimarisi / Termal Model (5 bölüm başlığı)

- **K17.9.5.24 — Durum: Implementasyon**
  - Sorumluluk: ✅ Boost converter verimlilik analizi tamamlandı (%92) ✅ LDO kayıp analizi yapıldı ve optimizasyon belirlendi
  - Kanıt: `power-efficiency.md` § Durum: Implementasyon
- **K17.9.5.25 — Termal Mimarisi**
  - Sorumluluk: «Termal Mimarisi» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § Termal Mimarisi
- **K17.9.5.26 — Termal Model**
  - Sorumluluk: «Termal Model» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § Termal Model
- **K17.9.5.27 — LDO için Termal Hesap (LM317, +15V)**
  - Sorumluluk: «LDO için Termal Hesap (LM317, +15V)» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § Termal Model > LDO için Termal Hesap (LM317, +15V)
- **K17.9.5.28 — MOSFET için Termal Hesap**
  - Sorumluluk: «MOSFET için Termal Hesap» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § Termal Model > MOSFET için Termal Hesap

#### K17.9.6 — PCB Termal Tasarımı / Heatsink Seçimi

- **Sorumluluk:** «PCB Termal Tasarımı» — thermal-management-power.md dosyasında belgelenen bölüm.
- **Kanıt:** `thermal-management-power.md` § PCB Termal Tasarımı / Heatsink Seçimi (5 bölüm başlığı)

- **K17.9.6.29 — PCB Termal Tasarımı**
  - Sorumluluk: «PCB Termal Tasarımı» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § PCB Termal Tasarımı
- **K17.9.6.30 — Thermal Via Matrisi**
  - Sorumluluk: «Thermal Via Matrisi» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § PCB Termal Tasarımı > Thermal Via Matrisi
- **K17.9.6.31 — Copper Pour Stratejisi**
  - Sorumluluk: «Copper Pour Stratejisi» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § PCB Termal Tasarımı > Copper Pour Stratejisi
- **K17.9.6.32 — Heatsink Seçimi**
  - Sorumluluk: «Heatsink Seçimi» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § Heatsink Seçimi
- **K17.9.6.33 — LDO'lar için Heatsink**
  - Sorumluluk: «LDO'lar için Heatsink» — thermal-management-power.md dosyasında belgelenen bölüm.
  - Kanıt: `thermal-management-power.md` § Heatsink Seçimi > LDO'lar için Heatsink

---

## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k17-guc-kaynagi/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayımın hangi kısmını desteklediğini listeler. 13 kanonik katman MD'si + index.md + README.md + CLAUDE.md.

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `6s-lipo-battery.md` | 6S LiPo Batarya | `K17.1` alanı (1), 6 alt alan, 14 yaprak → `K17` toplam 244 içine katkı |
| 2 | `battery-management.md` | Batarya Yönetim Sistemi (BMS) | `K17.2` alanı (1), 6 alt alan, 17 yaprak → `K17` toplam 244 içine katkı |
| 3 | `or-ing-circuit.md` | OR-ing Devresi | `K17.3` alanı (1), 6 alt alan, 17 yaprak → `K17` toplam 244 içine katkı |
| 4 | `lm5122-dual-boost.md` | LM5122 Dual Boost Converter | `K17.4` alanı (1), 6 alt alan, 12 yaprak → `K17` toplam 244 içine katkı |
| 5 | `voltage-regulation.md` | Gerilim Regülasyonu | `K17.5` alanı (1), 6 alt alan, 13 yaprak → `K17` toplam 244 içine katkı |
| 6 | `soft-start.md` | Soft Start Devresi | `K17.6` alanı (1), 5 alt alan, 22 yaprak → `K17` toplam 244 içine katkı |
| 7 | `power-sequencing.md` | Güç Sıralama Devresi | `K17.6` alanı (1), 3 alt alan, 5 yaprak → `K17` toplam 244 içine katkı |
| 8 | `current-sensing.md` | Akım Ölçme Devresi | `K17.7` alanı (1), 6 alt alan, 15 yaprak → `K17` toplam 244 içine katkı |
| 9 | `emc-filtering.md` | EMC Filtreleme | `K17.8` alanı (1), 4 alt alan, 18 yaprak → `K17` toplam 244 içine katkı |
| 10 | `decoupling-strategy.md` | Decoupling Stratejisi | `K17.8` alanı (1), 5 alt alan, 17 yaprak → `K17` toplam 244 içine katkı |
| 11 | `bulk-capacitors.md` | Bulk Kapasitör Bankı | `K17.8` alanı (1), 2 alt alan, 7 yaprak → `K17` toplam 244 içine katkı |
| 12 | `power-efficiency.md` | Güç Verimliliği | `K17.9` alanı (1), 5 alt alan, 23 yaprak → `K17` toplam 244 içine katkı |
| 13 | `thermal-management-power.md` | Güç Aşaması Termal Yönetimi | `K17.9` alanı (1), 3 alt alan, 10 yaprak → `K17` toplam 244 içine katkı |
| 14 | `index.md` | Katman ana sayfası: diyagram, tablolar, bağımlılıklar | `K17.a) alan tanımı bağlamı |
| 15 | `README.md` | Katman künyesi ve bu şema/katalog | `K17.a`/`K17.a.b` doğrulaması |
| 16 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |

**Sayım dayanağı:** 13 dosyadan çıkarılan 240 H2/H3 başlığı içinden hedef c=190 için 50 başlık orantılı olarak seçimin dışında bırakıldı (aşağıda); alan/alt alan sayıları a=9, b=6 ile kilitlidir.

### Seçim Dışı Kanıtlar

| Başlık | Dosya | Neden |
|--------|-------|-------|
| Güç Hesaplamaları | `6s-lipo-battery.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Depolama Kuralları | `6s-lipo-battery.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `6s-lipo-battery.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `6s-lipo-battery.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| I²C Protokolü (BQ76940 ↔ MCU) | `battery-management.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| PCB Tasarım Kuralları | `battery-management.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `battery-management.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `battery-management.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `battery-management.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `or-ing-circuit.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| PCB Layout | `or-ing-circuit.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `or-ing-circuit.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `or-ing-circuit.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| PCB Layout Kuralları | `lm5122-dual-boost.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `lm5122-dual-boost.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `lm5122-dual-boost.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| LC Filtre Tasarımı | `voltage-regulation.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `voltage-regulation.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `voltage-regulation.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `voltage-regulation.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| PGOOD Mantığı | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| UVLO (Under-Voltage Lock-Out) | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Kapanma Sıralaması | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| MCU State Machine | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `power-sequencing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `current-sensing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bileşen Listesi | `current-sensing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `current-sensing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `current-sensing.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| ESR/ESL Etkisi | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| ESR (Equivalent Series Resistance) | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| ESL (Equivalent Series Inductance) | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Kapasitör Yerleşim Diyagramı | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Sıcaklık ve Ömür Analizi | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Capacitance vs Sıcaklık | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| DC Bias Etkisi (Ceramic) | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bileşen Listesi (Toplam) | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `bulk-capacitors.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Sıcaklık İzleme | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| NTC Thermistor Ağı | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Sıcaklık Alarm Eşikleri | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Sıcaklık Dağılım Diyagramı | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Spesifikasyonlar | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Termal Malzeme | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Bağımlılıklar | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `thermal-management-power.md` | hedef c=190 aşıldı; havuz 240 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |

*K17 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
