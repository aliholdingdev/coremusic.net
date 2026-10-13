---
type: electronic
category: amplifier-pcb
title: "CoreMusic — Amplifier PCB Layout"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Amplifier PCB Layout

**See also:** [[electronic/hardware/index]] · [[electronic/amplifier/index]]

---

## 1. Amaç

Amplifier PCB Layout, CoreMusic amfi PCB yerleşim kurallarını tanımlar.

---

## 2. Yerleşim Stratejisi

```
[Giriş] → [Diferansiyel] → [VAS] → [Driver] → [Output] → [Filtre] → [Çıkış]
    │                                                              │
[Bias]                                                          [Koruma]
    │                                                              │
[Güç]                                                          [Röle]
```

---

## 3. Güç Yolu Kuralları

| Kural | Açıklama |
|-------|----------|
| Wide traces | ±42V için 100mil+ |
| Short paths | Güç kaynağı → transistör |
| Kelvin connection | Sensing için |
| Star ground | Tek toprak noktası |

---

## 4. Termal Yerleşim

| Kural | Açıklama |
|-------|----------|
| Output transistors | Heatsink kenarında |
| Thermal vias | Isı dağıtımı için |
| Air gap | Sıcak → soğuk akış |
| Sensor placement | En sıcak nokta |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

## 6. Amplifier PCB Yerleşim Detayları

### 6.1 Modüler Yerleşim (7.1 Sistem)

7.1 surround sistemi için modüler PCB yapısı:

| Modül | Kapsam | Kanal |
|-------|--------|-------|
| Preamp / input stage | Diferansiyel giriş, ses kontrolü | Tüm kanallar |
| Class AB output (×7) | 100W çıkış stage | FL/FR/C/SL/SR/SBL/SBR |
| Subwoofer output | 200W Class D (TPA3255) | Sub |
| Power supply | ±42V boost, 51V PVDD | Tüm |
| Protection | Röle, DC offset, termal | Tüm |

### 6.2 Analog Yerleşim Kuralları

| Kural | Açıklama |
|-------|----------|
| Giriş → çıkış akış | Sinyal yolu düz (L-shape yok) |
| Dijital uzak | XMOS/PCM3168A giriş stage'den ≥20mm |
| Topraklama | Star ground, tek nokta |
| Guard ring | Diferansiyel giriş etrafında |
| Kablo yolu | XLR/TRS giriş → kısa, ekranlı |

### 6.3 Güç Yerleşimi

| Bölge | Gerilim | Akım | Trace genişliği |
|-------|---------|------|-----------------|
| Boost converter | 24V→51V | 10A | 200mil+ veya plane |
| ±42V rail | ±42V | 5A | 100mil+ |
| PVDD (sub) | 51V | 15A | 300mil+ veya plane |
| 5V preamp | 5V | 1A | 25mil |
| 3.3V logic | 3.3V | 0.5A | 15mil |

### 6.4 Sinyal Yerleşimi (Analog)

| Sinyal | Trace genişliği | Clearance |
|--------|-----------------|-----------|
| Giriş (diferansiyel) | 10mil差分 | 15mil (pair içi), 20mil (diğer) |
| Feedback ağı | 8mil | 15mil |
| Bias devresi | 8mil | 10mil |
| Çıkış (100W) | 100mil+ | 25mil (50V) |
| Zobel ağı | 20mil | 15mil |

---

## 7. Termal Yerleşim (Amplifier PCB)

### 7.1 Output Transistör Yerleşimi

| Kural | Değer | Gerekçe |
|-------|-------|---------|
| Heatsink kenarı | TO-247/TO-220 heatsink'e doğrudan | RθCS minimal (thermal §17) |
| Hava akışı yönü | Transistörler → heatsink kanatları | Sıcak hava dışarı |
| NTC konumu | En sıcak transistöre yakın | Erken tespit (thermal §8.1) |
| Röle uzaklığı | Output transistörlerden ≥20mm | Röle ısı dayanımı |
| Kapasitör uzaklığı | Isı kaynağı ≥10mm | Elektrolitik ömür (thermal §9.3) |

### 7.2 Heatsink Monteaj PCB Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Heatsink montaj delikleri | TO-220/TO-247 hizalı, ±0.5mm tolerans |
| 2 | Thermal pad alanı | Her transistör için ≥100mm² copper |
| 3 | Thermal via | TO-247 altı 4×4 grid (16 via) |
| 4 | Bottom-side pour | Heatsink altında geniş copper |
| 5 | Elektriksel izolasyon | Mika/pad (gövde↔ısıtıcı >1MΩ) |

### 7.3 Termal Bölme (PCB)

| Bölme | Amaç |
|-------|------|
| PSU ↔ amplifikatör | 41W PSU ısıtması amfi stage'i etkilemesin |
| Boost ↔ output | Boost converter ısıtması output'a geçmesin |
| Analog ↔ dijital | Hem termal hem EMI |

---

## 8. Amplifier PCB Güç Yolu (Power Path)

### 8.1 Boost Converter Çıkışı → Output Stage

```
24V DC input → Boost converter (51V) → Bulk cap (470µF/63V) → ±42V rail
    │                                                              │
    └── PVDD (51V, subwoofer)                              Output transistörler
```

### 8.2 Güç Yolu Kuralları

| # | Kural | Değer |
|---|-------|-------|
| 1 | Bulk cap çıkışı kısa | ≤10mm (transistöre) |
| 2 | Rail trace genişliği | 100mil+ (5A) veya plane |
| 3 | Decoupling | 100nF + 10µF her rail'de |
| 4 | Fuse | 5A hızlı patlama (her rail) |
| 5 | Kelvin connection | Sensing (voltage feedback) |

### 8.3 Topraklama (Amplifier PCB)

| Toprak | Konum | Amaç |
|--------|-------|------|
| Star ground (GND1) | Power supply çıkışı | Tüm güç toprakları burada birleşir |
| Signal ground (GND2) | Giriş stage | Analog sinyal referansı |
| Output ground (GND3) | Çıkış terminali | Hoparlör toprak dönüşü |
| Chassis ground | Metal kasa | EMI shield, güvenlik |

**Star ground bağlantısı:** GND1, GND2, GND3 → tek noktada birleşir (power supply
girişi); ground loop önleme (pcb-design §9.1).

---

## 9. Amplifier PCB Bileşen Yerleşimi

### 9.1 Sıralama (Öncelik)

| # | Bileşen | Yerleşim |
|---|---------|----------|
| 1 | XLR/TRS giriş connector | PCB kenarı, kısa yol |
| 2 | Diferansiyel giriş stage | Guard ring, dijital uzak |
| 3 | VAS (gerilim amplifikasyonu) | PCB merkezi |
| 4 | Driver stage (MJE15030/31) | Output'a yakın |
| 5 | Output transistör (TO-247) | Heatsink kenarı (§7.1) |
| 6 | Zobel ağı | Çıkış terminaline yakın |
| 7 | Output relay | Çıkış terminali, transistör uzak |
| 8 | Bias devresi (Vbe-multiplier) | Output transistöre termal bağlı |

### 9.2 Bileşen Aralık Kuralları

| Bileşen çifti | Min aralık | Gerekçe |
|---------------|-----------|---------|
| Output transistör ↔ heatsink | 0mm (doğrudan temas) | RθCS minimal |
| Transistör ↔ elektrolitik | 10mm+ | Kapasitör ömür |
| Boost ↔ analog stage | 20mm+ | EMI + termal |
| XLR ↔ dijital | 20mm+ | EMI |
| Röle ↔ heatsink | 20mm+ | Röle ısı dayanımı |

---

## 10. Amplifier PCB EMC Kuralları

### 10.1 EMI Azaltma (Amplifier)

| # | Teknik | Uygulama |
|---|--------|----------|
| 1 | Zobel ağı | Çıkışta 10Ω + 100nF (indüktif yük stabilite) |
| 2 | Ferrite bead | PVDD girişi, çıkış hattı |
| 3 | Shielding | Metal kasa (EMI + radyasyon) |
| 4 | Star ground | Ground loop önleme |
| 5 | Twisted pair | XLR giriş, çıkış kabloları |

### 10.2 EMI Test (Amplifier)

| Test | Sınır | Yöntem |
|------|-------|--------|
| Conducted emission | CISPR 11 Sınıf B | LISN + EMC receiver |
| Radiated emission | CISPR 11 Sınıf B | Absorbing clamp |
| Conducted immunity | IEC 61000-4-6 (3V) | Injection probe |
| Radiated immunity | IEC 61000-4-3 (3V/m) | Anechoic chamber |

---

## 11. Amplifier PCB Ölçüm Noktaları (Test Points)

| # | Nokta | Sinyal | Amaç |
|---|-------|--------|------|
| 1 | TP1 | +42V rail | Voltaj doğrulama |
| 2 | TP2 | -42V rail | Voltaj doğrulama |
| 3 | TP3 | PVDD 51V | Boost çıkışı |
| 4 | TP4 | GND (star) | Toprak eşpotansiyeli |
| 5 | TP5 | Bias (Vbe) | Bias doğrulama |
| 6 | TP6 | Output DC offset | Koruma testi (protection §5) |
| 7 | TP7 | NTC (sıcaklık) | Termal izleme |
| 8 | TP8 | Feedback point | Kazanç doğrulama |

---

## 12. Repo / Kaynak Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda amplifier PCB tasarım dosyası (Altium/KiCad) bulunmaz; tasarım
yalnızca bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI
orkestrasyon katmanının placeholder alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder (PCB verisi YOK) | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** Amplifier PCB kuralları vault tarafında tanımlıdır (§6-§11); gerçek PCB
üretimi (Altium/KiCad dosyası) PLANNED.

---

## 13. Risk Matrisi (Amplifier PCB)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Ground loop (hum) | Orta | MEDIUM | Star ground (§8.3) |
| 2 | Output transistör termal | Orta | HIGH | Thermal via (§7.2) |
| 3 | EMI (AM band) | Düşük | HIGH | Zobel + shield (§10.1) |
| 4 | Röle termal | Düşük | MEDIUM | Röle↔heatsink 20mm (§9.2) |
| 5 | Boost EMI | Orta | MEDIUM | Ferrite bead, star ground |
| 6 | Zobel yetersiz | Düşük | HIGH | 10Ω + 100nF (§10.1-1) |
| 7 | Bias termal kaçak | Orta | HIGH | NTC tracking (class-ab §3.2) |

---

## 14. Terimler (Glossary)

| Terim | Tanım |
|-------|-------|
| Star ground | Toprakların tek noktada birleşmesi (ground loop önleme) |
| Zobel ağı | Çıkışta 10Ω + 100nF (indüktif yük stabilite) |
| Kelvin connection | Dirençsel sensing (4-wire, akım paylaşımı doğrulama) |
| Thermal via | Isı dağıtan via (transistör → heatsink) |
| Guard ring | Diferansiyel giriş etrafında toprak halkası |
| Bulk cap | Yüksek kapasitanslı güç kapasitörü (470µF+) |
| Star point | Star ground'ın birleştiği nokta |
| PVDD | Class D güç kaynağı (51V boost çıkışı) |

---

## 15. Web Kaynakları (Doğrulanmış URL Tablosu)

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| ON Semiconductor MJL3281A — output transistör datasheet (SOA, RθJC) | https://www.onsemi.com/pdf/datasheet/mjl3281a-d.pdf | 2026-10-13 | HTTP 200 doğrulandı |
| TI TPA3255 — Class D amfi datasheet (PVDD, BTL/PBTL) | https://www.ti.com/lit/ds/symlink/tpa3255.pdf | 2026-10-13 | HTTP 200 doğrulandı |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

---

## 16. Amplifier PCB Kural Matrisi (Hızlı Referans)

### 16.1 Trace Genişlik Matrisi

| Sinyal | Genişlik | Clearance | Layer |
|--------|----------|-----------|-------|
| ±42V rail | 100mil | 25mil | L3 (power plane) |
| PVDD 51V | 300mil+ | 30mil | L3 (power plane) |
| Output (5A) | 100mil | 25mil | L1 |
| Output (10A, sub) | 200mil+ | 30mil | L1 |
| Analog giriş (diff) | 10mil | 15mil | L1 |
| Feedback | 8mil | 10mil | L1 |
| Bias | 8mil | 10mil | L1 |
| I2S / clock | 8mil | 8mil | L1 |
| 5V / 3.3V | 15-25mil | 8mil | L3 |

### 16.2 Clearance Matrisi (Gerilim Bazlı)

| Gerilim farkı | Min clearance | Öneri (CoreMusic) |
|---------------|---------------|-------------------|
| 0-15V | 6mil | 10mil |
| 15-30V | 10mil | 15mil |
| 30-50V | 15mil | 20mil |
| 50-60V (PVDD) | 25mil | 30mil |

### 16.3 Via Sayısı Matrisi

| Bileşen | Via tipi | Sayı | Grid |
|---------|----------|------|------|
| TO-220 (output) | Thermal | ≥9 | 3×3 |
| TO-247 (output) | Thermal | ≥16 | 4×4 |
| Power entry | Power | 4-8 | Line |
| Star ground | Signal | 1 | — |
| Stitching (sınır) | Signal | Her 5mm'de 1 | Line |

---

## 17. Amplifier PCB Sıcak Nokta Analizi

### 17.1 Isı Kaynakları ve Etki

| Kaynak | Isı | PCB etkisi | Önlem |
|--------|-----|-----------|-------|
| Output transistör | 20-50W | Board ısınma (L1) | Thermal via, heatsink (§7) |
| Boost converter | 10-20W | PSU bölgesi | Termal bölme (§7.3) |
| Driver (MJE15030/31) | 3-5W | PCB ısınma | Hava akışı |
| LDO (preamp) | 1-3W | Analog bölge | Hava akışı, ama hassas uzak |
| Röle bobin | 0.5W | Röle bölgesi | Yeterli aralık (§9.2) |

### 17.2 PCB Isı Dağılım Kuralları

| Kural | Uygulama |
|-------|----------|
| Copper pour geniş | Isı kaynağı etrafında ≥100mm² |
| Thermal relief | Bileşen pinlerinde 4 spoke, 20mil |
| Isı akış yönü | Sıcak → soğuk, hava akışı yönü |
| Bileşen aralığı | Sıcak bileşenler arası ≥5mm |
| Board edge | Sıcak kaynaklar kenara yakın (dışarı atım) |

### 17.3 Termal Simülasyon (PCB)

| Parametre | Değer | Hedef |
|-----------|-------|-------|
| Board sıcaklığı (output stage) | <80°C @ Ta=40°C | Kapalı kasa |
| Board sıcaklığı (PSU) | <85°C @ Ta=40°C | Kapalı kasa |
| Component sıcaklık farkı | <20°C (board üzerinde) | Eşit dağılım |
| Hava akış hızı | 0.5-1 m/s (fan) | Zorlanmış hava |

---

## 18. Amplifier PCB Üretim Kuralları

### 18.1 Prototip vs Üretim

| Özellik | Prototip | Üretim |
|---------|----------|--------|
| PCB tipi | 2 katman (basit) veya 4 katman | 4 katman (§8 pcb-design) |
| Component | Through-hole + SMD | SMD tercih |
| Test points | Tümü (§11) | Kritik olanlar |
| BOM | Elle derleme | Pick & place |
| Revizyon | A (ilk) | B/C (revize) |

### 18.2 Assembly Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Sıralama | Düşük → yüksek profil (resistors → capacitors → heatsink) |
| 2 | Transistör eşleştirme | Output transistörler Vbe eşleşmeli (±10mV) |
| 3 | Kapasitör polarity | Elektrolitik + işaretli (ters → patlama) |
| 4 | Heatsink son | Bileşen lehimleme sonrası heatsink montaj |
| 5 | Temizlik | Flux artığı temizleme (IPA) |

### 18.3 Kalite Kontrol

| # | Test | Kabul |
|---|------|-------|
| 1 | Görsel (solder joint) | Parlak, concave (yeterli ıslanma) |
| 2 | Continuity (toprak) | Star ground → tüm GND noktaları |
| 3 | Isolation (gövde↔ısıtıcı) | >1MΩ (thermal §18.2-1) |
| 4 | İlk güçleme (idle) | Tcase <45°C, rail ±0.5V |
| 5 | DC offset | <±50mV (protection §5) |
| 6 | Bias | 50-100mA (class-ab §3.1) |

---

## 19. Amplifier PCB Ölçüm Protokolü (Kabul Testi)

### 19.1 Test Setupı

| Adım | Aksiyon | Beklenen |
|------|---------|----------|
| 1 | Continuity testi | Star ground tam |
| 2 | Isolation (gövde↔ısıtıcı) | >1MΩ |
| 3 | İlk güçleme (düşük güç) | Rail ±0.5V, Tcase <45°C |
| 4 | DC offset (sessiz) | <±50mV |
| 5 | Bias akımı | 50-100mA |
| 6 | 1kHz sine, 1W | THD+N <0.01% |
| 7 | Güç artırma | %1 THD+N → 100W @ 8Ω |
| 8 | Termal (1 saat sürekli) | Tcase <80°C, Tj <120°C |

### 19.2 Kabul Kriterleri

| Kriter | Hedef | Red |
|--------|-------|-----|
| Rail voltajı | ±42V ±0.5V | ±1V sapma |
| DC offset (sessiz) | <±50mV | >±100mV |
| Bias | 50-100mA | <30mA veya >150mA |
| THD+N @ 1W | <0.01% | >0.02% |
| SNR (A-wtd) | >100dB | <95dB |
| Tcase (1 saat) | <80°C | >90°C |

### 19.3 Kayıt Tablosu

| Alan | Değer |
|------|-------|
| PCB revizyon | A/B/C |
| Ölçüm tarihi | YYYY-MM-DD |
| Ekipman (multimeter, scope) | Model |
| Rail voltajı | +42V, -42V, PVDD |
| DC offset | mV |
| Bias | mA |
| THD+N @ 1W | % |
| SNR | dB |
| Tcase (1 saat) | °C |
| Durum | PASS/FAIL |

---

## 20. Amplifier PCB Kabul Kontrol Listesi (Üretim Öncesi)

Kart üretimine/montaja verilmeden önce adımlar sırayla tamamlanır; atlanan adım varsa kart **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | Güç yolu trace genişliği matrisini doğrula | §16.1 | genişlik denetimi |
| 2 | Clearance matrisini gerilim bazlı doğrula | §16.2 | clearance denetimi |
| 3 | Star ground ve topraklama kurgusunu doğrula | §8.3 · §3 | toprak şeması |
| 4 | Termal yerleşim/boşlukları doğrula (heatsink montaj) | §7.2 · §4 | termal şema |
| 5 | Analog/dijital ayırma ve EMC kurallarını doğrula | §10.1 | EMC denetimi |
| 6 | Ölçüm noktalarını (test points) yerleştir | §11 | test point listesi |
| 7 | Ölçüm protokolünü çalıştır (§19.1 setup) | §19.1 | ölçüm oturumu |
| 8 | Kabul kriterlerini karşılaştır (§19.2) | §19.2 | PASS/FAIL kaydı |
| 9 | Kayıt tablosunu doldur (§19.3) | §19.3 | dolu kayıt |

### 20.1 Sık Yapılan Amplifier PCB Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Güç yolunun dar trace'den geçmesi | Isı, voltaj düşümü, erime riski | §16.1 matrisi zorunlu |
| 2 | Analog/dijital toprağın tek noktada birleşmemesi | Ground loop, gürültü | §8.3 star ground |
| 3 | Bias ölçümü olmadan kartın çalıştırılması | Termal kaçak, transistör hasarı | §19.2 bias 50-100 mA |
| 4 | Heatsink montaj boşluğu ihlali | Tcase > 90°C → koruma | §7.2 · §19.2 Tcase < 80°C |
| 5 | DC-offset kontrolü atlanarak röle kapatılması | Hoparlör hasarı riski | §19.2 DC < ±50mV (elektronik kural 10) |
| 6 | Test points'siz üretime gitme | Sahada teşhis imkânsız | §11 zorunlu |
| 7 | EMC/EMI testi olmadan sertifikasyon | CE/RoHS uyumsuzluğu | §10.2 EMI testi |

**Not:** Bu bölümdeki toleranslar (±42V ±0.5V · DC < ±50mV · bias 50-100 mA · THD+N <0.01% · SNR >100dB · Tcase <80°C) mevcut §19.2 kabul kriterleriyle **aynı** değerlerdir; yeni tolerans **tanımlanmaz**. Ölçümü olmayan iddia `⚠️ VERIFICATION REQUIRED` işaretlenir.

### 20.2 Amplifier PCB Değişiklik Sonrası Yeniden Ölçüm Kapıları

Aşağıdaki değişikliklerden **herhangi biri** yapıldığında §19 ölçüm protokolü baştan çalıştırılır; eski ölçüm geçersiz sayılır:

| # | Değişiklik | Zorunlu yeniden ölçüm | Kaynak |
|---|------------|------------------------|--------|
| 1 | Güç yolu genişliği/geçit değişikliği | Rail voltajı + THD+N + SNR | §16.1 · §19.2 |
| 2 | Topraklama kurgusu değişikliği | SNR + DC offset | §8.3 · §19.2 |
| 3 | Termal yerleşim/heatsink değişikliği | Tcase (1 saat) | §7.2 · §19.2 |
| 4 | Bias devresi/ayar değişikliği | Bias + THD+N + Tcase | §19.2 |
| 5 | Bileşen değişikliği (transistör/kondansatör) | THD+N + SNR + DC offset | §9 · §19.2 |
| 6 | EMC filtresi/şield değişikliği | EMI testi + SNR | §10.2 · §19.2 |
| 7 | PCB katman/stack-up değişikliği | Tam protokol (§19.1–§19.3) | §6 · §19 |

**Kural:** Eski ölçüm yeni karta **taşınmaz** (revizyon farkı ölçümü geçersiz kılar); ölçüm tarihi ve PCB revizyonu §19.3 kayıt tablosunda birlikte tutulur. Bu kapı, donanım kuralı 10 (DC offset > 0.5V → koruma rölesi zorunlu) ile birlikte çalışır: DC ölçümü temiz olmadan röle kapatılmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
