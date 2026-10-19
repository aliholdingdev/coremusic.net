---
type: electronic
category: amplifier-class-d
title: "CoreMusic — Class D Amplifier Topology"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Class D Amplifier

**See also:** [[electronic/amplifier/index]] · [[class-ab]] · [[power-supply]] · [[hardware/amplifier-pcb]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

Class D Amplifier, CoreMusic ELECTRONICS platformunun yüksek verimli, düşük güç tüketimli amfi topolojisini tanımlar. Ev cihazı amfi devresi, oda amfi imalatı ve test/ölçüm amaçlı CM-D serisi modellerin tasarım omurgasıdır.

Bu dosya:

- PWM/PCM sinyal işleme prensibini (sigma-delta modülasyon)
- Karşılaştırma osilatörü (comparator) ve geri beslemeli/geri beslemesiz farkını
- Çıkış LC filtresi hesabını (50kHz kesme)
- Geri besleme (feedback) topolojilerini (closed-loop vs open-loop)
- EMI yönetimi ve AM avoidance stratejisini
- Verimlilik ve termal hesabı
- THD+N/SNR ölçüm planını

taşır. İlgili dosyalar: [[class-ab]] (alternatif sınıf), [[power-supply]] (PVDD boost), [[protection]] (koruma), [[thermal]] (soğutma).

---

## 2. Temel Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class D (PWM) |
| Verimlilik | >90% |
| Çıkış Gücü | 70W–185W @ 8Ω (çipe bağlı) |
| THD+N | <0.01% (1kHz, 1W) |
| SNR | >100dB (A-wtd) |
| Frekans Yanıtı | 20Hz - 20kHz (±0.5dB) |
| Damping Factor | >200 (8Ω) |
| Anahtarlama Frekansı | 300kHz-600kHz (çipe bağlı) |
| Çıkış Filtresi | LC low-pass, 50kHz |

### 2.1 Sınıf Seçiminde Yer

| Sınıf | Dosya | Kullanım |
|-------|-------|----------|
| Class AB | [[class-ab]] | CoreMusic ana amfi (stüdyo, high-end) |
| **Class D** | **Bu dosya** | Ev cihazı, oda imalatı, test/ölçüm |

### 2.2 Class D Amplifikatör Çip Seçenekleri

| # | Çip (TI) | Güç@8Ω | Güç@4Ω | THD+N | SNR | Supply | Fiyat (1ku) | Fiyat (1-99) | Stok | Kullanım |
|---|----------|--------|--------|-------|-----|--------|-------------|--------------|------|----------|
| 1 | **TPA3255** | 185W | 315W | 0.006% | 112dB | 18-53.5V | ~$4.13 | ~$8.22 | ⚠️ Stok dışı olabilir | **Profesyonel** |
| 2 | **TPA3251** | 150W | 175W | 0.005% | 111dB | 12-38V | ~$3.35 | ~$7.41 | ✅ Mevcut | **Orta güç, en sessiz** |
| 3 | **TPA3250** | 70W | 130W | 0.005% | 110dB | 12-36V | ~$2.35 | ~$5.47 | ✅ Mevcut | **Bütçe, en ucuz** |
| 4 | **TPA3116D2** | — | 50W | 0.1% | >100dB | 4.5-26V | ~$0.70 | ~$1.50 | ✅ Mevcut | **35-50W ev** |
| 5 | **TPA3118D2** | 30W | 60W | 0.1% | >100dB | 4.5-26V | ~$0.60 | ~$1.20 | ✅ Mevcut | **15-30W ev** |
| 6 | **TPA3130D2** | 15W | 30W | 0.1% | >100dB | 4.5-26V | ~$0.50 | ~$1.00 | ✅ Mevcut | **5-15W masaüstü** |

### 2.3 Düşük Güç Class D Detayları

| Çip | Güç@8Ω | Güç@4Ω | Supply | Rds(on) | Fiyat | Özellik |
|-----|--------|--------|--------|---------|-------|---------|
| TPA3130D2 | 15W | 30W | 4.5-26V | 0.12Ω | ~$0.50 | Isıatsız çalışabilir (2 katman PCB) |
| TPA3118D2 | 30W | 60W | 4.5-26V | 0.12Ω | ~$0.60 | Isıatsız çalışabilir (2 katman PCB) |
| TPA3116D2 | — | 50W | 4.5-26V | 0.12Ω | ~$0.70 | Küçük heatsink gerekli |

**Ortak Özellikler (TPA31xx serisi):**
- Same footprint (aynı PCB)
- >90% verimlilik
- AM avoidance
- Master/Slave senkronizasyon
- Self-protection (UV, OT, short)
- Click-and-pop free

### 2.4 Seçim Kılavuzu

| Uygulama | Öneri | Gerekçe |
|----------|-------|---------|
| 100W+ güç gerekli | **TPA3255** | 185W@8Ω, profesyonel |
| 36V PSU ile çalışma | **TPA3251** | En sessiz (THD 0.005%) |
| Düşük bütçe | **TPA3250** | En ucuz TPA32xx |
| Battery operation | **TPA3250** | En düşük idle loss |
| 35-50W ev | **TPA3116D2** | 50W@4Ω |
| 15-30W ev | **TPA3118D2** | Isıatsız çalışabilir |
| 5-15W masaüstü | **TPA3130D2** | Mini, ısıatsız |

### 2.5 BTL / PBTL / SE Modları

TPA32xx serisi üç farklı konfigürasyonda çalışabilir:

| Mod | Tam Adı | Çıkış | Kullanım |
|-----|---------|-------|----------|
| SE | Single-Ended | Tek uçlu, topraklı | Düşük güç, basit |
| BTL | Bridge-Tied Load | Differential (2× V) | **Class D ana mod** — 4× güç |
| PBTL | Parallel BTL | İki BTL paralel | Daha düşük Rds(on), daha yüksek akım |

**Hesap:** BTL'de yük, iki half-bridge arasında bağlıdır → yük üzerinde 2× Vpeak
görür → P = (2V)²/R = 4 × V²/R → **4× güç** (SE'ye göre). PBTL, akımı iki köprü
arasında paylaştırır → Rds(on) yarıya iner → daha yüksek sürekli güç.

| Mod | TPA3255gücü@8Ω | Uygulama |
|-----|----------------|----------|
| SE | 46W | Tek uçlu çıkış |
| BTL | **185W** | CoreMusic standart |
| PBTL | 300W+ (4Ω) | Subwoofer, mono |

---

## 3. PWM/PCM Prensibi

### 3.1 Sinyal Zinciri

```
Analog Input → Sigma-Delta Modulator → PWM → Output Filter → Hoparlör
                              │
                         Self-Oscillating
                         or External Clock
```

| Aşama | Görev | Parametre |
|-------|-------|-----------|
| Analog/Differential Input | Sinyal kabulü | 2 VRMS (TPA32xx) |
| Sigma-Delta Modulator | PCM/PWM dönüşümü | 3. mertebe sigma-delta |
| PWM Output | Half-bridge anahtarlama | 300-600kHz |
| LC Output Filter | PWM → analog | 50kHz kesme |
| Speaker | Yük | 4Ω/8Ω |

### 3.2 PWM Dalga Formu

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Duty cycle (0 sinyal) | %50 | Sessizlik |
| Duty cycle (+ tepe) | ~%90 | Pozitif maksimum |
| Duty cycle (- tepe) | ~%10 | Negatif maksimum |
| Anahtarlama frekansı | 300-600kHz | Sinyal bandının >> üstü |
| Sinyal bandı | 20Hz-20kHz | LC filtresi sonrası |

**Prensip:** Analog sinyalin PWM duty cycle'ı ile orantılı kodlanması; LC filtre PWM taşıyıcısını (300kHz+) geride bırakarak orijinal sinyali geri üretir.

### 3.3 Sigma-Delta Modülasyon

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Mertebe | 3. | Gürültü shaping |
| Gürültü bandı | 20Hz-20kHz | Düşük gürültü |
| Out-of-band gürültü | 20kHz üstü | LC filtre ile bastırılır |
| Oversampling | ≥10× | 480kHz+ (48kHz için) |

---

## 4. Karşılaştırma Osilatörü

### 4.1 Osilatör Topolojisi

Class D amplifikatörlerde modülatör şu bileşenlerden oluşur:

| Bileşen | Görev | Çip İçi/Harici |
|---------|-------|----------------|
| Triangle/sawtooth generator | Karşılaştırma referansı | Çip içi |
| Comparator | Sinyal + tri karşılaştırma | Çip içi |
| Latch | Kararlı PWM çıkışı | Çip içi |
| Dead-time control | Shoot-through önleme | Çip içi |

### 4.2 Self-Oscillating vs Fixed-Frequency

| Özellik | Self-Oscillating (ADI/feedback) | Fixed-Frequency (TI TPA32xx) |
|---------|--------------------------------|------------------------------|
| Frekans | Sinyale bağlı değişken | Sabit (300-600kHz) |
| Jitter | Düşük | Düşük |
| AM avoidance | Zor | Kolay (frekans sabit) |
| Senkronizasyon | Zor (kanallar arası) | Kolay (master/slave) |
| Multi-channel | Karmaşık | TPA3255: 8 kanal BTL/PBTL |
| CoreMusic kullanımı | — | **✅ TPA32xx tercih** |

**CoreMusic kararı:** Sabit frekanslı (fixed-frequency) modülatör; 7.1 surround'da kanallar arası senkronizasyon kritik.

### 4.3 Dead-Time

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Dead-time | 10-50ns | High/low side arası bekleme |
| Amaç | Shoot-through önleme | High+low aynı anda açık → kısa devre |
| THD etkisi | Dead-time ↑ → THD ↑ | Minimum dead-time (çip içi fabrika) |

---

## 5. Çıkış LC Filtresi

### 5.1 Filtre Parametreleri

| Parametre | Değer |
|-----------|-------|
| Filtre Tipi | 2. basamak LC low-pass |
| Kesme Frekansı | 50kHz |
| Indüktör | 10µH, high-current |
| Kapasitör | 0.47µF, film |
| Damping | Q ≈ 0.7 (Butterworth) |

### 5.2 LC Hesabı

| Formül | Değer | Açıklama |
|--------|-------|----------|
| fc = 1 / (2π√(LC)) | 1 / (2π√(10µ × 0.47µ)) = 73kHz | Kesme (±3dB) |
| Hedef fc | 50kHz | Sinyal (20kHz) ile taşıyıcı (300kHz+) arası |
| Indüktör akımı | Ipeak = 3.54A (100W@8Ω) | High-current indüktör |
| Kapasitör voltaj | PVDD (51V max) | Film kapasitör |

### 5.3 Filtre Bileşen Seçimi

| Bileşen | Seçim | Neden |
|---------|-------|-------|
| Indüktör | 10µH, 5A+, low-DCR | Düşük kayıp, doygunluk yok |
| Kapasitör | 0.47µF film (polypropylene) | Düşük DF, düşük gürültü |
| Indüktör tipi | Toroidal / drum core | Kapalı manyetik yol, düşük EMI |
| Kapasitör tipi | Film (NOT ceramic) | Piezoelectric distortion önleme |

### 5.4 Damping / Zobel Ağı

| Parametre | Değer | Amaç |
|-----------|-------|------|
| Zobel R | 10Ω + 0.1µF | Indüktif yük stabilite |
| Konum | Çıkış → toprak | Hoparlör kablosu indüktansı |

---

## 6. Geri Besleme

### 6.1 Closed-Loop vs Open-Loop

| Özellik | Closed-Loop (PurePath Ultra-HD) | Open-Loop |
|---------|--------------------------------|-----------|
| THD+N | 0.005-0.006% | 0.01-0.1% |
| PSRR | Yüksek (güç kaynağı reddi) | Düşük |
| Damping factor | >200 | <100 |
| LC filtre etkisi | Geri besleme telafi eder | Doğrudan yansır |
| Kararlılık | Reaktif yükte dikkat | Kolay |
| CoreMusic kullanımı | **✅ TPA3255/3251/3250** | TPA31xx (düşük güç) |

### 6.2 TPA32xx Closed-Loop Özellikleri

| Özellik | Değer |
|---------|-------|
| Closed-loop | PurePath Ultra-HD |
| Differential analog input | 2 VRMS |
| PCM5242 uyumlu | ✅ |
| Click-and-pop free startup | ✅ |
| Self-protection | UV, OT, clip, short |
| Verimlilik (4Ω) | >90% |
| Idle losses | <2.5W (TPA3255), <1W (TPA3251/3250) |

### 6.3 Geri Besleme Avantajları

| Avantaj | Mekanizma |
|---------|-----------|
| LC filtre telafisi | Filtre insertion loss geri besleme ile düzeltilir |
| PSRR ↑ | Power supply ripple modülasyonu bastırılır |
| Damping ↑ | Çıkış empedansı LC'ye rağmen <0.01Ω |
| THD ↓ | Modülatör nonlinearity geri besleme ile azaltılır |

---

## 7. EMI

### 7.1 EMI Kaynakları

| Kaynak | Frekans | Seviye | Önlem |
|--------|---------|--------|-------|
| PWM anahtarlama | 300-600kHz | Yüksek | LC filtre, shielding |
| Half-bridge shoot-through | ns-region transients | Orta | Dead-time, snubber |
| Bootstrap switching | 300-600kHz | Orta | Bootstrap kapasitörü yerleşimi |
| Cable radiation | 300-600kHz (kablo anten) | Orta | Twisted pair, ferrite bead |

### 7.2 EMI Önleme

| # | Önlem | Uygulama | Etki |
|---|-------|----------|------|
| 1 | **LC filtre** | 50kHz kesme | Taşıyıcıyı 40dB+ bastırır |
| 2 | **4 katman PCB** | GND plane | Loop alanı ↓, EMI ↓ |
| 3 | **SW node kısa tut** | Layout | Anten etkisi ↓ |
| 4 | **Ferrite bead** | PVDD girişi | High-frequency bypass |
| 5 | **Shielding** | Metal kasa | Radiated EMI ↓ |
| 6 | **AM avoidance** | Frekans ayarı | AM band interference ↓ |

### 7.3 AM Avoidance

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| AM band | 530-1700kHz | Radyo algılama bandı |
| PWM frekansı | 300-600kHz | AM bandına düşebilir |
| Çözüm 1 | Frekansı AM bandı dışına kaydır | 600kHz+ (TPA32xx) |
| Çözüm 2 | Spreading spectrum | Jitter ile yayma |
| Çözüm 3 | LC filtre + shielding | Pasif bastırma |

**CoreMusic kuralı:** TPA32xx 600kHz sabit frekans + LC filtre + 4 katman PCB → AM interference önlenir.

### 7.4 Kablolama ve Bağlantı EMI'si

Ses kablosu, yüksek frekanslı anahtarlama enerjisini anten gibi yayınlayan en kritik
EMI kaynağıdır:

| Problem | Mekanizma | Önlem |
|---------|-----------|-------|
| Differential mode | LC çıkışına kadar PWM | Twisted pair, kısa kablo (<2m) |
| Common mode | Output↔GND asimetri | Common-mode choke, ferrite bead |
| Kablo anteni | 600kHz harmonikleri | Ekranlı kablo, kasa içi yönlendirme |
| Hoparlör empedans uyumsuzluğu | Rezonans | Zobel ağı (§5.4) |

**Ölçüm:** LISN (Line Impedance Stabilization Network) + EMC receiver ile conducted
emiSSION ölçümü (150kHz–30MHz, CISPR 11 B sınıfı sınırı).

### 7.5 EMI Ölçüm Setupı

| Ekipman | Amaç | Standart |
|---------|------|----------|
| LISN (50µH/50Ω) | Hat empedansı sabitleme | CISPR 16 |
| EMC receiver (150kHz-30MHz) | Conducted emission | CISPR 11 Grup 1 Sınıf B |
| Absorbing clamp | Radiated emission (kablo) | CISPR 11 |
| Anodize alüminyum kasa | Shielding | IEC 60204-1 |

---

## 8. Verimlilik & Termal

### 8.1 Verimlilik Hesabı

| Parametre | Değer (TPA3255, 185W@8Ω) | Açıklama |
|-----------|---------------------------|----------|
| PVDD | 51V | Boost converter çıkışı |
| Pout | 185W | Çıkış gücü |
| η (verimlilik) | >90% | 4Ω yükte |
| Pdiss (chip) | ~20W | 185W × (1-0.90) / 0.90 ≈ 20.5W |
| Idle loss | <2.5W | Sessizlikte |
| Thermal pad | PowerPAD gerekli | PCB heatsink |

### 8.2 Class AB vs Class D Verimlilik

| Parametre | Class AB (100W) | Class D (185W, TPA3255) |
|-----------|-----------------|--------------------------|
| Verimlilik | %53 | %90+ |
| Pdiss | ~58W | ~20W |
| Heatsink | 500cm², <1°C/W | PCB pad + küçük heatsink |
| Güç kaynağı | 500VA toroidal/boost | 250W boost yeterli |
| Toplam sistem ağırlığı | Ağır | Hafif |

### 8.3 Termal Tasarım (Class D)

| Bileşen | Isı | Soğutma |
|---------|-----|---------|
| TPA3255 (185W) | ~20W | PowerPAD + PCB copper |
| Boost converter | ~10W (500W @ %96) | heatsink |
| LC filtre (indüktör) | ~3W | Doğal konveksiyon |
| PVDD ripple akımı | ~2W | Kapasitör ESR |

**Fark:** Class AB'de output transistör ısıyı doğrudan heatsink'e verir (500cm² gerekli); Class D'de ısı çipin PowerPAD'ı ile PCB copper'a dağılır (daha küçük yüzey yeterli).

Detay: [[thermal]] §1-§2.

### 8.4 Boost Converter ve PVDD

TPA3255'in 51V PVDD'ini üretmek için boost converter gerekir:

| Parametre | Değer (500W boost) | Açıklama |
|-----------|---------------------|----------|
| Giriş | 24V DC (DC kaynak) | Adaptör / batarya |
| Çıkış (PVDD) | 51V | TPA3255 maks supply (§2.2) |
| Verimlilik | %96 | Kayıp: 500W × %4 = 20W |
| Anahtarlama frekansı | 100-500kHz | PWM boost controller |
| Indüktör | 33-47µH, high-current | Doygunluk marjı |
| PVDD ripple | <100mV | SNR için kritik (§6.2, PSRR) |

**PVDD ripple → THD ilişkisi:** Ripple, modülatöre besleme olarak girer; PSRR
(besleme reddi) yüksek olsa da, ripple genliği THD'yi doğrudan artırır. PVDD ripple
<100mV hedefi, 112dB SNR için gereklidir.

---

## 9. Ölçüm (THD+N · SNR)

### 9.1 Ölçüm Setupı

```
PC (Signal Generator) → [[hardware/audio-interface]] DAC → TPA3255 → LC Filter → Load (8Ω)
                                          │
                              [[hardware/snr-thd-measurement]] Analyzer
                                          │
                                    PC (FFT + THD+N)
```

### 9.2 Ölçüm Parametreleri

| Ölçüm | Frekans | Yük | Hedef | Protokol |
|-------|---------|-----|-------|----------|
| THD+N | 1kHz | 8Ω, 1W | <0.01% (TPA3255: 0.006%) | [[hardware/snr-thd-measurement]] §6 |
| SNR | 1kHz | 8Ω | >100dB (TPA3255: 112dB A-wtd) | [[hardware/snr-thd-measurement]] §1 |
| Frekans yanıtı | 20Hz-20kHz | 8Ω | ±0.5dB | [[hardware/frequency-response]] §5 |
| Damping factor | 100Hz | 8Ω | >200 (closed-loop) | Çıkış empedansı <0.01Ω |
| Güç çıkışı | 1kHz | 8Ω/4Ω | 185W/315W (%1 THD+N) | Artırma yöntemi |
| PWM taşıyıcı | 600kHz | Boş | LC sonrası <1mV | Oscilloscope |
| Verimlilik | 1kHz | 8Ω, 100W | >90% | Pin/Pout ölçümü |
| EMI | 150kHz-300MHz | — | CISPR 11 | EMC receiver |

### 9.3 Ölçüm Adımları

| Adım | Aksiyon | Beklenen |
|------|---------|----------|
| 1 | PVDD ayarı (51V boost) | 51V ±0.5V |
| 2 | LC filtre çıkışında PWM taşıyıcı | <1mV @ 600kHz |
| 3 | 1kHz sine, 1W → THD+N | <0.006% (TPA3255) |
| 4 | Güç artırma → %1 THD+N | 185W @ 8Ω |
| 5 | SNR (A-wtd) | 112dB |
| 6 | Frekans sweep | ±0.5dB |
| 7 | Verimlilik: Pin/Pout @ 100W | >90% |

### 9.4 Class D'ye Özel Ölçüm Farkları

Class AB ölçüm protokolüne ([[class-ab]] §8) ek olarak Class D'de şu farklar vardır:

| Ölçüm | Class AB Yöntemi | Class D Yöntemi | Neden |
|-------|------------------|-----------------|-------|
| PWM taşıyıcı | Yok | Oscilloscope, LC çıkışında <1mV | Taşıyıcı THD'ye karışır |
| Gürültü bandı | 20Hz-20kHz | 20Hz-20kHz + out-of-band | Sigma-delta gürültü shaping |
| EMI | Yok (lineer) | LISN + EMC receiver | Anahtarlama EMI |
| Damping factor | Çıkış empedansı ölçümü | LC geri besleme ile <0.01Ω | Closed-loop damping |
| Idle loss | ~3.4W (Iq × Vcc) | <2.5W (TPA3255) | Anahtarlama kaybı |

---

## 10. Repo Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda Class D amplifikatör devresine karşılık gelen C/C++/PCB kodu bulunmaz; tasarım yalnızca bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon katmanının placeholder ölçüm alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` — cihaz analizi | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder (gerçek ölçüm YOK) | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** `analyzeHardware()` snr/thd değerlerini her zaman `0.0` döndürür. Class D ölçüm zinciri vault tarafında tanımlıdır (§9); gerçek cihaz entegrasyonu PLANNED.

### 10.1 CoreMusic Entegrasyon Durumu

| Bileşen | Durum | Kanıt |
|---------|-------|-------|
| CM-D serisi amfi | **PLANNED** | Vault şeması var (§2, §2.4); PCB/schematic YOK |
| Çip seçimi | **PLANNED** — TPA3255 (185W) / TPA3116D2 (50W) | §2.2 çip tablosu |
| Boost converter | **PLANNED** — 24V→51V, %96 verim | §8.4 |
| LC çıkışı | **PLANNED** — 10µH + 0.47µF, 73kHz | §5.2 |
| Ölçüm zinciri | **PLANNED** — [[hardware/snr-thd-measurement]] §6 | Vault protokolü |
| Yazılım entegrasyonu | **PLANNED** — `analyzeHardware()` placeholder | §10 repo kanıtı |

---

## 11. Riskler

| # | Risk | Olasılık | Etki | Önlem | İlgili Dosya |
|---|------|----------|------|-------|--------------|
| 1 | **EMI (AM band)** | Orta | HIGH (regülasyon) | AM avoidance, LC filtre, shielding | §7 |
| 2 | **LC filtre rezonansı** | Düşük | MEDIUM (THD) | Q ≈ 0.7 damping | §5 |
| 3 | **Shoot-through** | Düşük | CRITICAL (MOSFET) | Dead-time (çip içi) | §4.3 |
| 4 | **Bootstrap kaybı** | Orta | HIGH (high-side driver) | 33nF bootstrap kapasitörü | [[power-supply]] §3.3 |
| 5 | **PVDD ripple** | Orta | MEDIUM (SNR) | [[power-supply]] §4 | [[power-supply]] |
| 6 | **Termal (PowerPAD)** | Orta | HIGH (termal kapanma) | PCB copper + heatsink | §8.3 |
| 7 | **Reaktif yük kararsızlığı** | Düşük | HIGH | Closed-loop, Zobel | §6.1 |
| 8 | **Kısa devre** | Orta | CRITICAL | Self-protection (çip içi) + [[protection]] | [[protection]] |
| 9 | **DC offset** | Düşük | CRITICAL (hoparlör) | [[protection]] §4 röle | [[protection]] |
| 10 | **Click/pop** | Orta | MEDIUM (ses) | Click-and-pop free startup | §6.2 |

---

## 12. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

## 13. Kaynakça (Web Araştırması)

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| TI, TPA3116D2 50W Class D audio amplifier ürün sayfasını resmi alan adında yayımlar (düşük güç Class D referansı) | https://www.ti.com/product/TPA3116D2 | 2026-10-13 | HTTP 200 doğrulandı |
| TI, TAS5805M akıllı Class D amplifier ürün sayfasını resmi alan adında yayımlar (akıllı Class D referansı) | https://www.ti.com/product/TAS5805M | 2026-10-13 | HTTP 200 doğrulandı |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode