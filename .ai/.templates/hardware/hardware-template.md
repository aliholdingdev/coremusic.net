---
title: "CoreMusic — Hardware Design Template"
type: hardware-template
category: hardware
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# CoreMusic — Hardware Design Template

**Katman:** K1 (donanım) / L0 · **Sorumlu Agent:** Audio Hardware Engineer · **İkincil:** Embedded Engineer, DSP Firmware Engineer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[../other/cpp-template]] · [[../infrastructure/migration-template]]

---

## 1. Amaç

Bu şablon, CoreMusic donanım tasarım dokümanını standartlaştırmaktır: bileşen seçimini, devre notlarını (güç kaynağı, sinyal zinciri, amplifikatör), kanal eşlemesini, PCB kurallarını, güç/termal bütçesini, BOM'u ve test protokolünü tek iskelette toplar. **Guardrail #16:** yeni donanım tasarım dokümanı bu şablondan üretilmek ZORUNLUDUR.

| Karar | ADR | Şablona gömülü karşılığı |
|-------|-----|--------------------------|
| DAC seçimi: PCM3168A — PCM5122 REDDEDİLDİ | ADR-038 | §3.1 bileşen tablosu + §4.1 #1 |
| Class AB amplifikatör zorunlu | ADR-089 | §3.3 amplifikatör devresi + §4.1 #3 |
| XMOS XU316 + PCM3168A DSP zinciri | ADR-017 | §3.2.2 sinyal zinciri |
| DSP pipeline mimarisi | ADR-062 | §3.2.2 + `[[../other/cpp-template]]` |
| Edge case: PCM5122 yerine PCM3168A/AK4458 | AGENTS.md §17.8 | §4.2 uyarı satırı |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| DAC/ADC, amplifikatör, güç kaynağı, batarya, PCB tasarım dokümanı | Yazılım kodu (PHP/JS) → ilgili şablonlar |
| Bileşen seçim tablosu, pin/kanal eşlemesi, BOM, test protokolü | Firmware/DSP kodu → `[[../other/cpp-template]]` |
| Güç bütçesi, termal hesap, emniyet kontrol listesi | Veritabanı/migration → `[[../infrastructure/migration-template]]` |
| K1 (donanım) katmanı | CI/CD pipeline → `[[../infrastructure/github-actions-template]]` |

- **Dosya tipi:** Markdown donanım tasarım dokümanı (`electronic/` altına yerleştirilir).
- **Kullanan agent:** Audio Hardware Engineer (sorumlu), Embedded Engineer / DSP Firmware Engineer (ikincil).
- **Doğrulama zorunluluğu:** fiyat, tolerans ve güç değerleri tedarikçi verisine dayanmıyorsa `⚠️ VERIFICATION REQUIRED` ile işaretlenir; üretim kararı bu değerlerle verilemez.

---

## 3. Mimari

Şablonun gövdesi: bileşen seçimi, devre notları, kanal eşlemesi, PCB kuralları, güç/termal bütçe, BOM ve test protokolü.

### 3.1 Bileşen Seçim Tablosu

| Rol | Model | Temel özellik | Neden bu seçim | Durum |
|-----|-------|---------------|----------------|-------|
| USB ses denetleyici | XMOS XU316 | USB Audio Class 2.0, çok çekirdek | Hi-Res + DSP yükü (ADR-017) | Hedef |
| DAC | PCM3168A | 6-in/8-out, 24-bit | 8.1 surround çıkış (ADR-038) | Hedef |
| DAC (alternatif) | AK4458 | 8-kanal, 32-bit | High-end varyant | Alternatif |
| Amplifikatör | MJL21194 (NPN) + MJL21193 (PNP) | Class AB Darlington çifti | 50 W/kanal, düşük bozulma (ADR-089) | Hedef |
| Boost dönüştürücü | LM5122 | Sınırlı-süreklilik boost | ±35 V simetrik rail | Hedef |
| Batarya | 6S LiPo | 22.2 V nominal | Taşınabilir besleme | Hedef |
| DC giriş | 19-24 V adapter | Masaüstü besleme | Sabit kullanım | Alternatif |
| Reddedilen DAC | PCM5122 | Tek kanal stereo | 8.1 yapılamaz (ADR-038) | ❌ RED |

### 3.2 Devre Şeması Notları

#### 3.2.1 Güç Kaynağı

```
6S LiPo (22.2 V) ──veya── 19-24 V DC adapter
        │
        ▼
   LM5122 Boost (+ sırt)
        │
        ├──► +35 V ──► Class AB (NPN tarafı — MJL21194)
        │
        └──► -35 V ──► Class AB (PNP tarafı — MJL21193)

Kural: güç kaynağı yalnızca DC'dir (§4.1 #2).
Simetri: |+35 V| ile |-35 V| farkı ≤ %5 (§3.7 test T1).
```

#### 3.2.2 Sinyal Zinciri (8.1)

```
USB source
   │
   ▼
XMOS XU316 ──I2S/TDM──► PCM3168A ──analog──► Class AB amplifikatör dizisi ──► hoparlörler
                              │
                              └── DSP zinciri (EQ / kompresör / limiter) — ADR-062
                                  kod iskeleti: [[../other/cpp-template]]

PCM3168A kanal → amplifikatör → çıkış:
  CH1 Front Left    → Amp 1 → Hoparlör 1
  CH2 Front Right   → Amp 2 → Hoparlör 2
  CH3 Center        → Amp 3 → Hoparlör 3
  CH4 LFE (sub)     → Amp 4 → Subwoofer
  CH5 Surround Left → Amp 5 → Hoparlör 5
  CH6 Surround Right→ Amp 6 → Hoparlör 6
  CH7 Rear Left     → Amp 7 → Hoparlör 7
  CH8 Rear Right    → Amp 8 → Hoparlör 8
```

#### 3.2.3 Amplifikatör Devresi (tek kanal, ADR-089)

```
                 +35 V rail
                    │
              ┌─────┴─────┐
              │ MJL21194  │  NPN çıkış
   Girdi ─────┤           ├──── Çıkış ──► hoparlör (8 Ω)
   (bias)     │ MJL21193  │  PNP çıkış
              └─────┬─────┘
                    │
                 -35 V rail

Hedef: 50 W/kanal @ 8 Ω · THD+N < 0.005 % @ 1 W · DC offset < 0.5 V
Termal: çıkış transistörlerinde thermal via + 2 oz bakır (§3.4)
```

### 3.3 PCB Tasarım Kuralları

| Parametre | Değer | Gerekçe |
|-----------|-------|---------|
| Katman | 6 katman stackup | Gürültü ayrımı + güç düzlemi |
| Bakır | 2 oz (üst/alt), 1 oz (iç) | Akım taşıma + termal |
| Yüzey | ENIG | Lehim dayanımı, düzlemsellik |
| Empedans | 90 Ω USB, 50 Ω I2S/TDM | Sinyal bütünlüğü |
| Toprak | Star ground topology | Ortak empedans geri beslemesi önlenir |
| Termal | Güç bileşenleri altında thermal via | Aşırı ısınma önlenir |
| Boyut | 200 × 100 mm (azami) | Muhafaza uyumu |
| Ayırma | Analog/dijital bölme, tek nokta birleşim | Çapraz konuşlanma |

### 3.4 Güç ve Termal Bütçe

| Kalem | Hesap | Sonuç | Not |
|-------|-------|-------|-----|
| Kanal gücü | 8 × 50 W | 400 W (tam sürücü) | §3.2.3 |
| Verimlilik (Class AB, %50 ortalama) | 400 W / 0.50 | ~800 W giriş | ⚠️ VERIFICATION REQUIRED (ölçümle doğrulanacak) |
| Boost çıkışı | ≥ 800 W / 0.90 | ~890 W | LM5122 dizimi |
| Batarya ömrü (6S 5000 mAh ≙ 111 Wh) | 111 Wh / 800 W | ~8 dak (tam güç) | Kısmi güçte uzar |
| Termal sınır | termal kamera | < 60 °C tam güçte | §3.7 test T5 |
| Kritik bileşen | çıkış transistörleri | Thermal via + 2 oz | §3.4 |

### 3.5 BOM Maliyet Analizi

| Kategori | Bileşen | Adet | Birim | Toplam |
|----------|---------|------|-------|--------|
| DAC | PCM3168A | 1 | 8.50 $ | 8.50 $ |
| USB | XMOS XU316 | 1 | 12.00 $ | 12.00 $ |
| Amp (çift) | MJL21194 | 8 | 3.50 $ | 28.00 $ |
| Amp (çift) | MJL21193 | 8 | 3.50 $ | 28.00 $ |
| Boost | LM5122 | 2 | 4.50 $ | 9.00 $ |
| Pasif | direnç/kondansatör/indüktör | ~200 | ~0.10 $ | ~20.00 $ |
| PCB | 6 katman, ENIG | 1 | 50.00 $ | 50.00 $ |
| **TOPLAM** | | | | **~155 $** |

> Fiyatlar tasarım öncesi tahmindir; güncellenmiş tedarikçi fiyatı yoksa ⚠️ VERIFICATION REQUIRED.

### 3.6 Test Protokolü

| # | Test | Yöntem | Kriter | Geçme |
|---|------|--------|--------|-------|
| T1 | Rail simetrisi | Multimetre | ±35 V ± %5 | □ |
| T2 | THD+N | Ses analizörü | < 0.005 % @ 1 W | □ |
| T3 | SNR | Ses analizörü | > 100 dB | □ |
| T4 | Frekans tepkisi | Sine sweep | 20 Hz-20 kHz ± 0.5 dB | □ |
| T5 | Termal | Termal kamera | < 60 °C tam güçte | □ |
| T6 | DC offset | Multimetre | < 0.5 V DC | □ |
| T7 | Kanal eşlemesi | Test tonu (sweep CH1→CH8) | Yanlış kanal yok | □ |
| T8 | Koruma | Kısa devre / aşırı sıcaklık | Kapanma + geri dönüş | □ |

### 3.7 Doküman Revizyon Tablosu

| Rev | Tarih | Değişiklik | Onay |
|-----|-------|------------|------|
| A | {{DATE}} | İlk taslak (şablondan türetildi) | {{APPROVER}} |
| B | {{DATE}} | {{CHANGE_NOTES}} | {{APPROVER}} |

### 3.8 Kanal ve Pin Eşlemesi

| Kanal | Çıkış (PCM3168A) | Amplifikatör | Hoparlör rolü | Empedans |
|-------|------------------|--------------|---------------|----------|
| CH1 | OUT1 | Amp 1 (MJL21194/93 çifti) | Front Left | 8 Ω |
| CH2 | OUT2 | Amp 2 | Front Right | 8 Ω |
| CH3 | OUT3 | Amp 3 | Center | 8 Ω |
| CH4 | OUT4 | Amp 4 | LFE (subwoofer) | 4 Ω |
| CH5 | OUT5 | Amp 5 | Surround Left | 8 Ω |
| CH6 | OUT6 | Amp 6 | Surround Right | 8 Ω |
| CH7 | IN1 (loop-out) | Amp 7 | Rear Left | 8 Ω |
| CH8 | IN2 (loop-out) | Amp 8 | Rear Right | 8 Ω |

| Kontrol hedefi | Yön | Not |
|----------------|-----|-----|
| I2S/TDM clock (BCLK/LRCK) | XMOS → PCM3168A | 50 Ω empedans (§3.3) |
| USB D+/D− | Konnektör → XMOS | 90 Ω diferansiyel (§3.3) |
| Amplifikatör girdi | DAC çıkış → bias ağı | AC eşleme kondansatörü |
| Koruma rölesi | Çıkış → hoparlör terminali | Aşırı akım/sıcaklık tetikli |
| Termal izleme | Çıkış transistörü → koruma MCU | §3.4 eşiği |

### 3.9 Koruma Devresi (ASCII)

```
                   ┌─────────────┐
 Çıkış (CH_n) ─────┤ Aşırı akım  ├───── Röle ──── Hoparlör terminali
                   │ sensörü     │                 │
                   └──────┬──────┘                 │
                          │ tetik                  │
                   ┌──────▼──────┐                 │
 Sıcaklık sensörü ──┤ Koruma MCU ├─────────────────┘
 (NTC, güç tran.)  └──────┬──────┘
                          │ kapanma
                   ┌──────▼──────┐
 DC offset mon. ────┤ DC KİLİDİ  ├──> girdi mute
                   └─────────────┘

Eşikler: DC offset > 0.5 V → mute · Sıcaklık > 60 °C → azalt ·
         Aşırı akım → röle aç · Geri dönüş: 10 s bekleme + test tonu
```

### 3.10 PCB Stackup (ASCII)

```
  ┌──────────────────────────────────────────┐
  │ L1  Sinyal (USB/DIFF, 2 oz)             │
  │ L2  GND düzlemi (toprak yıldızı)        │
  │ L3  Sinyal (I2S/TDM, 1 oz)              │
  │ L4  GÜÇ düzlemi (+35 V / -35 V / 5 V)   │
  │ L5  Analog toprak (ayrık, tek nokta)     │
  │ L6  Sinyal + güç çıkış padleri (2 oz)    │
  └──────────────────────────────────────────┘
  Analoge/dijitale bölme: L2 altında kesik, tek nokta birleşim (§3.3)
```

### 3.11 Güç Dağılımı ve Filtreleme

| Aşama | Bileşen | İşlev | Kritik değer |
|-------|---------|-------|--------------|
| Giriş | TVS + sigorta | Aşırı gerilim/akım koruması | 24 V eşik |
| EMI | π filtresi (L + C) | Gürültü bastırma | ⚠️ VERIFICATION REQUIRED |
| Boost | LM5122 + indüktör | ±35 V simetrik | Simetri ≤ %5 (§3.7 T1) |
| Yerel | 100 µF + 100 nF demetleri | Rail soğurma | Her güç pedi yanında |
| Sessiz | LDO (varsa) | Analog referans | Ripple < 10 mV ⚠️ |
| Çıkış | Zobel ağı (R+C) | Yük kararlılığı | Hoparlör başına |

### 3.12 Test Ekipmanı Listesi

| Ekipman | Kullanım | İlgili test |
|---------|----------|-------------|
| Çokmetre | Rail/DC offset/efor sürekliliği | T1, T6 |
| Ses analizörü (THD/SNR) | Bozulma ve gürültü | T2, T3 |
| Fonksiyon üretici + osiloskop | Sine sweep, dalga biçimi | T4, T7 |
| Termal kamera / termokupl | Yüzey sıcaklığı | T5 |
| Elektronik yük | Amplifikatör çıkış yükü | T4, T8 |
| USB protokol analizörü | UAC2 paketleri | T7 (entegrasyon) |

### 3.13 EMC ve Mekanik

| Konu | Kural | Doğrulama |
|------|-------|-----------|
| Ekranlama | Kasa metalik, tek nokta toprak | Ölçüm ⚠️ |
| Kablo | USB ekranlı, I2S kısa ve eşlenik | Görsel kontrol |
| Havalandırma | Güç bölümü üst delikli ızgara | Termal kamera (T5) |
| Montaj | Çıkış transistörleri ısıl pede vidalı | Tork tablosu ⚠️ |
| Titreşim | Batarya sabitleme köşebentli | Mekanik test |
| Boyut | 200 × 100 mm azami (§3.3) | Kalıp kontrolü |

### 3.14 Ses Hedefleri (Kabul Kriterleri)

| Parametre | Hedef | Ölçüm |
|-----------|-------|-------|
| Frekans tepkisi | 20 Hz-20 kHz ± 0.5 dB | T4 |
| THD+N @ 1 W | < 0.005 % | T2 |
| SNR | > 100 dB | T3 |
| Kanal ayrımı | > 80 dB ⚠️ | T7 |
| LFE bant sınırı | 20-120 Hz ⚠️ (firmware DSP) | ADR-062 |
| Gecikme (USB → çıkış) | < 5 ms ⚠️ | T7 |

### 3.15 Gereksinim → Test Matrisi

| # | Gereksinim | Kaynak | Test |
|---|------------|--------|------|
| R1 | 8.1 çoklu kanal çıkış | ADR-038 | T7 |
| R2 | ±35 V simetrik besleme | §3.2.1 | T1 |
| R3 | Class AB amplifikasyon | ADR-089 | T2, T3 |
| R4 | Taşınabilir besleme | §3.4 | T5 (termal) + §3.4 ömür |
| R5 | Koruma devresi | §3.9 | T8 |
| R6 | Dokunmatik/güvenlik sınırları | K1 katmanı | T5, T6 |

### 3.16 Alternatif Tedarik ve Risk Tablosu

| Kritik bileşen | Birincil | Alternatif | Risk | Etki |
|----------------|----------|------------|------|------|
| DAC | PCM3168A | AK4458 | Tedarik kesintisi | Pinout farklı → PCB revizyonu |
| USB denetleyici | XMOS XU316 | XMOS XU208 | Bütçe/performans | DSP çekirdek sayısı |
| Boost | LM5122 | LM25116 | Bulunabilirlik | ±35 V korunmalı |
| Çıkış çifti | MJL21194/93 | MJL3281/1302 | Sınıf A/B bozulması | Termal yeniden hesap |
| Röle | {{RELAY_MODEL}} | {{RELAY_ALT}} | ⚠️ | Koruma zinciri |
| Batarya hücreleri | 6S LiPo | 6S Li-ion | Güvenlik profili | BMS eşiği |

### 3.17 Üretim (Prodüksiyon) Test İstasyonları

| İstasyon | Adım | Kriter | Kayıt |
|----------|------|--------|-------|
| ST1 | Görsel + süreklilik | Kısa/açık yok, dizim doğru | Seri no + foto |
| ST2 | Güç ilk açılış | Rail ±%5, akım sınırı | §3.7 T1 |
| ST3 | Firmware yükleme | XMOS + koruma MCU sürümü | Sürüm logu |
| ST4 | Ses kalibrasyonu | DC offset, gain kalibrasyonu | §3.18 |
| ST5 | Kanal sweep | CH1→CH8 + LFE doğru | §3.7 T7 |
| ST6 | Koruma doğrulama | Kısa devre/kalorilik tetik | §3.7 T8 |
| ST7 | Kutulama | Aksesuar + etiket | Fatura/lot |

### 3.18 Kalibrasyon Kalıbı

```text
{{TITLE}} — KALİBRASYON ADIMLARI

1. Giriş: 1 kHz sine, -20 dBFS, tüm kanallar aktif.
2. Ölçüm: her kanalın çıkış genliği ve fazı okunur.
3. Düzeltme: DAC/analog gain kaydırıcıları ile hedefe getirilir
   (hedef: kanallar arası ≤ 0.5 dB sapma, ⚠️ VERIFICATION REQUIRED).
4. DC offset ayarı: çıkış kapalıyken offset < 0.05 V hedeflenir (§3.6 T6 < 0.5 V sınır).
5. Kayıt: kalibrasyon değerleri cihaz seri numarası ile saklanır.
6. Doğrulama: ADIM 1 tekrarlanır; §3.7 T2/T3 eşikleri karşılanır.
```

### 3.19 Kablolama ve Etiketleme

| Kablo / terminal | Etiket | Not |
|------------------|--------|-----|
| USB giriş | `USB-IN` | Ekranlı, kısa |
| DC/batarya giriş | `PWR-IN 22.2V / 19-24V` | Sigorta dahili |
| Hoparlör çıkışları | `FL FR C LFE SL SR RL RR` | §3.2.2 sırası |
| Sensor/termal | `NTC-1..N` | Koruma MCU |
| Yayılım hattı | `I2S/TDM` | 50 Ω, eşlenik |
| Etiket zorunlu | Giriş gerilimi + uyarı | §4.4 #7 |

### 3.20 Kullanım Senaryoları ve Konumlandırma

| Senaryo | Gereksinim | İlgili bölüm |
|---------|------------|--------------|
| Ev medya (sabit) | 19-24 V DC adapter, 8.1 çıkış | §3.2.1, §3.2.2 |
| Taşınabilir stüdyo | 6S LiPo, düşük gecikme | §3.4, §3.14 |
| Araç içi | Titreşim/dayanım, koruma | §3.13, §4.4 |
| Gömülü ekran (RPi5) | 1024×600 UI hizası | `[[../frontend/css-template]]` §3.5 |
| Geliştirici/entegrasyon | USB UAC2, test istasyonları | §3.12, §3.17 |

### 3.21 Bakım ve Servis Notları

| Öğe | Ömür / Aralık | Servis eylemi |
|-----|---------------|---------------|
| Batarya (6S LiPo) | ~300 döngü ⚠️ | Şarj döngüsü kaydı, değiştirme eşiği |
| Fan/havalandırma filtresi | 6 ay ⚠️ | Temizleme |
| Çıkış rölesi | 100.000 operasyon ⚠️ | Kontak direnci ölçümü |
| Termal ped/transistör bağlantısı | Servis sonrası | Tork + termal macun kontrolü |
| Firmware (XMOS + MCU) | Sürüm takibi | §3.17 ST3 kaydı |
| Kalibrasyon | İlk üretim + onarım | §3.18 |

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | PCM5122 ile 8.1 surround yapılamaz — PCM3168A/AK4458 (ADR-038) | Yanlış donanım |
| 2 | Güç kaynağı yalnızca DC (6S LiPo 22.2 V veya 19-24 V DC) | Sistem/safety hatası |
| 3 | Class AB amplifikatör zorunlu (ADR-089) | Yanlış topoloji |
| 4 | Empedans eşleştirme zorunlu (90 Ω USB, 50 Ω I2S) | Sinyal kaybı |
| 5 | Termal hesap zorunlu (§3.4) + thermal via | Aşırı ısınma |
| 6 | §3.6 test protokolünün tamamı geçmeden tasarım onaylanamaz | Onaysız üretim |
| 7 | BOM/ölçüm değeri doğrulanmadan üretime çıkılmaz | Hatalı tedarik |

### 4.2 Ek Kurallar

- **Zorunlu:** bileşen tablosunda (§3.1) her satırın bir gerekçesi ve ADR bağlantısı vardır; reddedilen seçenek (PCM5122) de görünür kalır.
- **Zorunlu:** kanal eşlemesi (§3.2.2) firmware ile birebir aynıdır; firmware tarafı `[[../other/cpp-template]]` (ADR-017/ADR-062) ile hizalanır.
- **Zorunlu:** her tasarım dokümanı §3.7 revizyon tablosunu taşır.
- **Yasak:** `{{TITLE}}`, `{{HARDWARE_CATEGORY}}`, `{{DATE}}`, `{{APPROVER}}`, `{{CHANGE_NOTES}}` placeholder'ları doldurulmadan commit.
- **Uyarı:** edge case — PCM5122 isteği gelirse PCM3168A/AK4458 önerilir ve `log.md`'ye not düşülür (AGENTS.md §17.8).
- **Uyarı:** doğrulanamayan güç/fiyat/tolerans değeri `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

### 4.3 Yasaklı Seçenekler (Edge Cases)

| # | Yasak / Edge | Sonuç | Yapılacak |
|---|--------------|-------|-----------|
| 1 | PCM5122 kullanımı | 8.1 yapılamaz (ADR-038) | PCM3168A veya AK4458 öner |
| 2 | AC güç girişi | Sistem/safety hatası | 6S LiPo veya 19-24 V DC |
| 3 | Sınıf D amplifikatör varsayımı | Topoloji ihlali (ADR-089) | Class AB korunur |
| 4 | Empedans eşleşmesiz çıkış | Sinyal kaybı + ısınma | §3.3 değerleri uygulanır |
| 5 | Doğrulanmamış fiyat/ölçümle üretim | Yanlış tedarik | ⚠️ VERIFICATION REQUIRED |
| 6 | Firmware kanal sırasıyla çelişen eşleme | Yanlış hoparlör | `[[../other/cpp-template]]` ile hizala |

### 4.4 Güvenlik Kontrol Listesi

| # | Kontrol | Kriter | ✔ |
|---|---------|--------|---|
| 1 | Aşırı akım koruması | Röle/ sigorta tetikli | ☐ |
| 2 | Aşırı sıcaklık | 60 °C eşiğinde azaltma/kapanma | ☐ |
| 3 | DC kilidi | > 0.5 V DC'de mute | ☐ |
| 4 | Pil koruması | Aşırı deşarj/şarj sınırı (6S BMS) | ☐ |
| 5 | İzolasyon | Güç ↔ sinyal ayrımı ölçüldü | ☐ |
| 6 | Topraklama | Star ground, tek nokta | ☐ |
| 7 | Etiketleme | Giriş gerilimi + uyarı yazısı | ☐ |

### 4.5 Ölçüm Raporu Kalıbı

```
{{TITLE}} — ÖLÇÜM RAPORU
Tarih: {{DATE}}   Operatör: {{APPROVER}}   Örnek No: {{SERIAL}}

T1 Rail simetrisi     : +34.8 V / -34.9 V   → [GEÇ]
T2 THD+N @ 1W         : 0.0031 %            → [GEÇ]
T3 SNR                : 104.2 dB            → [GEÇ]
T4 Frekans tepkisi    : 20 Hz-20 kHz ±0.4 dB → [GEÇ]
T5 Termal (tam güç)   : 54.1 °C             → [GEÇ]
T6 DC offset           : 0.08 V              → [GEÇ]
T7 Kanal eşlemesi     : CH1..CH8 doğru      → [GEÇ]
T8 Koruma             : kısa devre → kapanış→ [GEÇ]

Sonuç: [ONAY] / [RED]   İmza: {{APPROVER}}
Not: ölçüm değerleri eksi/kayıpsa §3.6 kriterine göre yeniden ölçülür.
```

### 4.6 Sık Yapılan Hatalar

| # | Hata | Sonuç | Doğrusu |
|---|------|-------|---------|
| 1 | PCM5122 ile 8.1 istemek | Kanal yetmezliği | PCM3168A/AK4458 (ADR-038) |
| 2 | Simetrik rail'i tek taraflı beslemek | DC offset + hoparlör hasarı | §3.2.1 + T1 |
| 3 | Analoge/dijitale bölmesiz PCB | Gürültü | §3.10 stackup |
| 4 | Fiyatsız BOM ile üretim kararı | Bütçe sapması | §3.5 + ⚠️ işareti |
| 5 | Test protokolünü kısaltmak | Sahada arıza | §3.6 tamamı |
| 6 | Firmware kanal sırasını unutmak | Ters surround | §3.8 + `[[../other/cpp-template]]` |

### 4.7 Kabul Matrisi (Özet)

| Alan | Kriter | Test |
|------|--------|------|
| Elektrik | ±35 V ± %5, DC < 0.5 V | T1, T6 |
| Ses | THD < 0.005 %, SNR > 100 dB, ±0.5 dB | T2, T3, T4 |
| Termal | < 60 °C tam güç | T5 |
| İşlevsellik | 8 kanal + LFE doğru, koruma tetikli | T7, T8 |
| Doküman | §3.7 revizyon + §3.15 gereksinim matrisi | §6 |

### 4.8 Terminoloji

| Terim | Tanım |
|-------|-------|
| Class AB | Çift tarafik, ortak emitter çıkış — ADR-089 topolojisi |
| Rail | Amplifikatör besleme sırtı (±35 V) |
| Star ground | Tek noktadan toprak bağlantısı (§3.3) |
| Thermal via | Güç pedi altında ısı iletken via dizisi |
| LFE | Low Frequency Effects (subwoofer kanalı) |
| BCNF | Boyko-Codd normal formu — ADR-040 |
| Forward-only | İleriye dönük, geri alınmaz migration — ADR-014 |

---

## 5. Workflow

```
İHTİYAÇ → ŞABLONU SEÇ → KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16 DOĞRULA → TEST PROTOKOLÜ → COMMIT
```

1. **İHTİYAÇ:** kanal sayısı, güç sınıfı, besleme türü netleştirilir.
2. **ŞABLONU SEÇ:** `.ai/.templates/hardware/hardware-template.md` (Guardrail #16).
3. **KOPYALA:** dokümanı `electronic/` altındaki ilgili tasarım konumuna kopyala.
4. **`{{VARIABLE}}` DOLDUR:** `{{TITLE}}`, `{{HARDWARE_CATEGORY}}`, `{{DATE}}`, `{{APPROVER}}`, `{{CHANGE_NOTES}}`; §3.1/§3.5 gerçek seçim ve fiyatlarla güncellenir.
5. **GUARDRAIL #16 DOĞRULA:** §6 + §4.1 (7 madde; ADR-038/ADR-089 ihlali yok).
6. **TEST PROTOKOLÜ:** §3.6 sekiz test de geçmeden onay yok.
7. **COMMIT:** ADR-038/ADR-089 ile çelişki yoksa onayla; `log.md` append'i parent yapar.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan (title, type, category, date, updated, version, status, authority) |
| 2 | Bölüm yapısı | §1-§7 numaralı, en fazla 3 başlık seviyesi |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı |
| 4 | Guardrails | §4.1 7/7 — PCM5122 yok, DC besleme, Class AB |
| 5 | Kanal eşlemesi | 8 kanal + LFE tanımı firmware ile eşleşiyor |
| 6 | PCB | 8 parametre (§3.3) tam |
| 7 | Güç/termal | §3.4 bütçe dolu; belirsiz değer işaretli |
| 8 | BOM | §3.5 toplam satırı mevcut |
| 9 | Test | §3.6 sekiz satır + geçme kutuları |
| 10 | Revizyon | §3.7 tablosu mevcut |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter (DRY) |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails, ADR-042 |
| Agent registry | [[../../AGENTS.md]] | §6 yönlendirme (hardware → audio-hw), §17.8 edge case |
| C++ DSP şablonu | [[../other/cpp-template]] | Sinyal zinciri firmware karşılığı |
| Migration şablonu | [[../infrastructure/migration-template]] | Cihaz/patch verisi depolanacaksa |
| İlgili ADR'ler | ADR-017 · ADR-038 · ADR-062 · ADR-089 | §1 tablosunda eşleştirilmiştir |
| Kanıt sınırlı | `electronic/` envanteri bu şablon için glob'lanmadı | ⚠️ VERIFICATION REQUIRED |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
