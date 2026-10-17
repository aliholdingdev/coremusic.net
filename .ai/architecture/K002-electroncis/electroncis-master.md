---
node_id: K002-electroncis/electroncis-master
node_type: module
status: draft
parent_id: K002-electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - ".ai/electronic/core-music-electronics-overview.md (279 satır — ham içerik kaynağı)"
  - ".ai/electronic/amplifier/class-ab.md satır 21-22 (100W @ 8Ω, THD+N <0.01% hedefi)"
  - ".ai/electronic/hardware/pcb-design.md satır 24-30 (4 katman PCB yapısı)"
validation: "draft — bu turda yazıldı; K001-donanım anayasa ile çapraz kontrol bekliyor"
open_questions:
  - "Q-M1: XMOS XU316 + RPi5 hedef donanımı bu domainde mi tanımlanacak?"
  - "Q-M2: 8.1 surround kanal topolojisi hangi katmanda?"
group: mimari
---

# electroncis-master — Domain Anayasası

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya K002 domain'inin
> ana omurgasıdır; 5 alt-konuyu birleştiren tek katmanlı referanstır.

## §0 ELI15

Elektronik, **elektriği hesaplanabilir bir sinyale dönüştürme** bilimidir. Bu domain
beş soruyu sırayla yanıtlar: (1) hangi bileşenler hangi devrede? (devre-ve-analog),
(2) sinyal hangi işlemciyle hesaplanır? (dijital-ve-mikroislemci), (3) sistem nereden
güç alır ve nasıl korunur? (guc-yonetimi), (4) sistem dünya ile nasıl konuşur?
(sensor-ve-akteyici), (5) her şey nasıl üretilir ve test edilir? (imalat-ve-test).
CoreMusic bağlamında hedef: **PCM3168A DAC + Class AB/Class D amfi** tabanlı ses
donanımı, SNR >100dB, THD+N <0.01% @1kHz ölçüm kapılarıyla.

## §1 Kimlik

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis` |
| 2 | Kademe | 1 — domain (Band-1) |
| 3 | Alt-konu | 5 (Kademe-2 subdomain) |
| 4 | Toplam dosya | 26 (kök 6 + 5×4) |
| 5 | Durum | `draft` (A1 tur-1) |
| 6 | Yazım | `electroncis` — dokunulmaz |
| 7 | Ham kaynak | `.ai/electronic/**` (60 dosya) |
| 8 | OS komşusu | `K000-isletim-sistemi/electroncis` (2 alt-dal) |
| 9 | Onay | Kullanıcı 2026-10-13 |
| 10 | Kapı | ADR-098 AÇILMAZ (Band-1) |

## §2 Beş Katman (devre → kart → cihaz → ekosistem → üretim)

CoreMusic elektronik domain'i beş fiziksel/mantıksal katman üzerinde kurulmuştur.
Katmanlar aşağıdan yukarıya doğru bağımlıdır: üst katman alt katman olmadan
var olamaz, alt katman üst katmandan bağımsızdır.

### §2.1 Katman 1 — Devre (Circuit)

| # | Öğe | Tanım | Alt-konu |
|---|---|---|---|
| 1 | Pasif bileşenler | R, C, L, ferrit — tolerans, ESR, SRF | devre-ve-analog |
| 2 | Aktif bileşenler | BJT, MOSFET, op-amp, referans | devre-ve-analog |
| 3 | Topoloji | Class AB push-pull, Class D PWM | devre-ve-analog |
| 4 | Filtre | Sallen-Key, LC, Bessel/Butterworth | devre-ve-analog |
| 5 | Osilatör | Kristal, RC, PLL kaynağı | devre-ve-analog + dijital |
| 6 | ADC/DAC ön zincir | Diferansiyel sürücü, referans, anti-aliasing | devre-ve-analog |

**Kanıt (vault):** `.ai/electronic/amplifier/class-ab.md` satır 38-60 — sinyal
zinciri 4 aşamalı: differential pair → VAS → driver → push-pull output.

### §2.2 Katman 2 — Kart (PCB)

| # | Öğe | Tanım | Alt-konu |
|---|---|---|---|
| 1 | Katman yığını | 4 katman: Top sinyal / Inner1 GND / Inner2 güç / Bottom sinyal | imalat-ve-test |
| 2 | Analog/dijital ayırma | Ayrı plane, tek nokta birleşim | imalat-ve-test |
| 3 | Trace kuralları | Güç ±42V 50mil · analog 10mil · I2S 8mil · clock 8mil | imalat-ve-test |
| 4 | Decoupling | Her IC pinine 100nF + bulk 10µF | devre-ve-analog |
| 5 | EMC | Ground pour, stitching via, giriş filtresi | imalat-ve-test |
| 6 | Termal | Heatsink >500cm², θ<1°C/W | guc-yonetimi |

**Kanıt (vault):** `.ai/electronic/hardware/pcb-design.md` satır 24-30 — katman
yapısı; satır 44-52 — trace genişlik tablosu.

### §2.3 Katman 3 — Cihaz (Device)

| # | Öğe | Tanım | Alt-konu |
|---|---|---|---|
| 1 | DAC | PCM3168A (8 kanal) — ADR-038 H001 | devre-ve-analog + dijital |
| 2 | Amplifier | TPA3255 (Class D) veya discrete Class AB | devre-ve-analog |
| 3 | Güç kaynağı | Boost converter 12V→±42V / PVDD 18-53.5V | guc-yonetimi |
| 4 | Koruma | DC offset rölesi, over-current, termal | guc-yonetimi |
| 5 | MCU/DSP | XMOS XU316 (PLANNED) | dijital-ve-mikroislemci |
| 6 | Bağlantı | USB 2.0/3.0, I2S, TDM | dijital-ve-mikroislemci |

**Kanıt (vault):** `.ai/electronic/firmware/boot-sequence.md` satır 27-43 — boot
zinciri: ROM Bootloader → XMOS firmware → DSP chain → I2S → PCM3168A → USB Audio.

### §2.4 Katman 4 — Ekosistem

| # | Öğe | Tanım | Alt-konu |
|---|---|---|---|
| 1 | Cihaz türleri | USB DAC, BT kulaklık, WiFi receiver, Roon endpoint | sensor-ve-akteyici |
| 2 | Hot-plug | Cihaz algılama, eşleştirme, devre dışı bırakma | sensor-ve-akteyici |
| 3 | Sürücü katmanı | Uygulama → HAL → OS ses → kernel → donanım | K000/electroncis |
| 4 | Platform | Windows (WASAPI/ASIO), Linux (ALSA), macOS (CoreAudio) | K000 |
| 5 | Akış protokolleri | DLNA/AirPlay/Roon | K013-integraion |
| 6 | Cihaz kimliği | USB descriptor, BT MAC | K0100-identitiy |

**Kanıt (vault):** `.ai/electronic/drivers/device-integration.md` satır 27-34 —
cihaz türleri tablosu (USB DAC, BT, WiFi, Roon, araç sistemi).

### §2.5 Katman 5 — Üretim (Manufacturing)

| # | Öğe | Tanım | Alt-konu |
|---|---|---|---|
| 1 | PCB üretimi | Gerber, panel, reflow profil | imalat-ve-test |
| 2 | DFT | Test noktaları, boundary scan, ICT/FCT | imalat-ve-test |
| 3 | AOI/X-ray | Optik + BGA X-ray muayene | imalat-ve-test |
| 4 | Kalibrasyon testi | SNR, THD+N, frekans yanıt ölçümü | imalat-ve-test |
| 5 | EMC ön hazırlık | Pre-scan, filtreleme, kalkanlama | imalat-ve-test |
| 6 | Yaşlanma/burn-in | 8 saat sürekli çalıştırma | imalat-ve-test |

**Kanıt (vault):** `.ai/electronic/development-workflow.md` satır 416-434 — Faz 15
Test: unit/integration/E2E/HIL testleri; satır 232 — EMI/EMC considerations.

### §2.6 Katman Matrisi (özet)

| Katman | Girdi | Çıktı | Ölçüm | Dosya grubu |
|---|---|---|---|---|
| 1 Devre | Şema | BOM + netlist | THD, gain, bandwidth | devre-ve-analog |
| 2 Kart | Şema + BOM | Gerber + stackup | Impedance, DRC | imalat-ve-test |
| 3 Cihaz | Kart + firmware | Çalışan donanım | SNR, output power | dijital + guc |
| 4 Ekosistem | Cihaz + OS | Kullanılabilir sistem | Latency, hot-plug | sensor + K000 |
| 5 Üretim | Cihaz | Seri ürün | Yield, AOI pass | imalat-ve-test |

## §3 Alt-Bileşenler

### §3.1 Bileşen Sınıflandırması

| # | Sınıf | Örnek | Kritik parametre | Alt-konu |
|---|---|---|---|---|
| 1 | Direnç | metal film 1% | TCR, gürültü, parasitik L | devre-ve-analog |
| 2 | Kondansatör | MLCC X7R | ESR, DC bias kaybı, piezo | devre-ve-analog |
| 3 | Bobin | toroid choke | SRF, doygunluk akımı | guc-yonetimi |
| 4 | Op-amp | OPA1612, NE5532 | GBW, gürültü, THD+N | devre-ve-analog |
| 5 | Güç transistörü | NPN/PNP çift | SOA, hFE, termal | devre-ve-analog |
| 6 | PWM kontrolcü | UC3843, TPS40210 | Anahtarlama frekansı | guc-yonetimi |
| 7 | LDO | LM1085, TPS7A24 | Dropout, PSRR, gürültü | guc-yonetimi |
| 8 | Sensör arayüzü | INA219, ADS1115 | Doğruluk, drift | sensor-ve-akteyici |
| 9 | Aktüatör sürücü | DRV8825, DRV8874 | Akım sınırı, mikro adım | sensor-ve-akteyici |
| 10 | Referans | shunt, bandgap | ppm/°C drift | devre-ve-analog |

### §3.2 Ses Sinyal Zinciri Bileşenleri

| # | Aşama | Bileşen | Vault kanıtı |
|---|---|---|---|
| 1 | Girişi | RCA/XLR, 47kΩ empedans | class-ab.md satır 42 |
| 2 | Differential pair | BJT çift, CMRR yüksek | class-ab.md satır 40 |
| 3 | VAS | Voltage Amplifier Stage | class-ab.md satır 44 |
| 4 | Driver | Darlington/bootstrap | class-ab.md satır 47 |
| 5 | Çıkış | Complementary push-pull | class-ab.md satır 50 |
| 6 | Geri besleme | Negatif feedback → girişi | class-ab.md satır 57 |
| 7 | Koruma | DC offset rölesi | protection.md satır 37 |

### §3.3 Güç Zinciri Bileşenleri

| # | Aşama | Bileşen | Parametre |
|---|---|---|---|
| 1 | DC giriş | Adaptör/batarya | 12-24V DC |
| 2 | Boost converter | Step-up | 12V→51V (Class D) / ±42V (Class AB) |
| 3 | EMI filtre | LC | Anahtarlama gürültüsü bastırma |
| 4 | Gate drive | GVDD 12V | RC filtre ile ayrıştırma |
| 5 | Smoothing | Bulk kapasitör | 10,000µF (trafolu alternatif) |
| 6 | Koruma | Over-voltage >±48V | Güç azaltma |

**Kanıt (vault):** `.ai/electronic/amplifier/power-supply.md` satır 24-80 — boost
topolojisi diyagramları (Class D / Class AB / toroidal alternatif).

## §4 Arayüzler

### §4.1 Dahili Arayüzler (kart içi)

| # | Arayüz | Hız | Sinyal | Kural |
|---|---|---|---|---|
| 1 | I2S | 3.072MHz BCLK | PCM ses | 8-kanal TX/RX (vault: audio-interface.md satır 32) |
| 2 | TDM | slot bazlı | Çoklu PCM | DAC init (boot-sequence.md satır 36) |
| 3 | SPI | ≥1MHz | DAC config | PCM3168A register yazımı |
| 4 | I2C | 100/400kHz | Sensör/regülatör | Address conflict denetimi |
| 5 | GPIO | — | Kontrol/reset | Enable, mute, röle |
| 6 | PLL clock | MCLK | Saat kaynağı | Jitter <1ps hedef |

### §4.2 Dış Arayüzler (cihaz-ösü)

| # | Arayüz | Standart | Kullanım | Komşu |
|---|---|---|---|---|
| 1 | USB Audio | USB 2.0 (480Mbps) | Stüdyo DAC | K000 |
| 2 | Bluetooth | 5.0+ (A2DP/LDAC) | Mobil | K010-network |
| 3 | WiFi | DLNA/AirPlay | Ev medya | K014 |
| 4 | Ethernet | 100/1000BASE-T | Roon endpoint | K014 |
| 5 | analog giriş | RCA/XLR 47kΩ | Kaynak | devre |
| 6 | analog çıkış | Hoparlör 4-8Ω | Yük | devre + guc |

### §4.3 Yazılım Arayüzleri (bu domainin SINIRI — kod K005'te)

| # | Arayüz | Sahip | K002 rolü |
|---|---|---|---|
| 1 | Register map | donanım | Şartname (K002) |
| 2 | Boot sequence | firmware | Zamanlama şartnamesi (K002), kod (K005) |
| 3 | HAL API | K000 | Sadece referans |
| 4 | DSP zinciri | K004 | Sadece referans |

## §5 Akışlar

### §5.1 Sinyal Akışı (giriş → çıkış)

```text
Kaynak (RCA/XLR)
  → Giriş tamponu (op-amp buffer, 47kΩ)
  → Filtre (anti-aliasing / tone)
  → DAC (PCM3168A — 8 kanal, 32-bit)
  → Amfi girişi (Class AB: diff pair / Class D: PWM komparatör)
  → Amfi çıkışı (push-pull / LC filtre 50kHz)
  → Koruma rölesi (DC offset >0.5V → açma)
  → Hoparlör (4-8Ω)
```

### §5.2 Güç Akışı

```text
AC mains 115/230V  ─┐
                    ├→ Toroidal 500VA → Bridge KBPC5010 → 10,000µF → ±42V DC
DC 12-24V (adaptör) ─┘                                            (Class AB)

DC 12-24V → Boost Converter (12V→51V) → EMI filtre → PVDD 18-53.5V (Class D)
         └→ GVDD 12V (gate drive, RC filtreli)
```

### §5.3 Boot Akışı (vault kanıtlı)

| # | Aşama | Süre | Kanıt |
|---|---|---|---|
| 1 | Power On | — | boot-sequence.md satır 28 |
| 2 | ROM Bootloader | 100ms | boot-sequence.md satır 30, 51 |
| 3 | XMOS Firmware Load | 500ms | boot-sequence.md satır 32, 53 |
| 4 | DSP Chain Init | 200ms | boot-sequence.md satır 34, 55 |
| 5 | I2S Configure | 100ms | boot-sequence.md satır 36, 57 |
| 6 | PCM3168A Init | 200ms | boot-sequence.md satır 38 |
| 7 | USB Audio Start | — | boot-sequence.md satır 40 |
| 8 | System Ready | toplam ~1.1s | boot-sequence.md satır 42 |

### §5.4 Koruma Akışı (öncelik matrisi — vault kanıtlı)

| # | Koruma | Tepki | Kanıt |
|---|---|---|---|
| 1 | Short-Circuit | µs-ms (en hızlı) | protection.md satır 48 |
| 2 | DC Offset >±0.5V | 1s gecikme → röle açma | protection.md satır 37, 49 |
| 3 | Over-Current >5A | ms sınıfı güç azaltma | protection.md satır 38, 50 |
| 4 | Over-Voltage >±48V | Boost kontrol | protection.md satır 41, 51 |
| 5 | Over-Temp >80°C | sn-dk, kapanma | protection.md satır 39, 52 |
| 6 | Soft-Start | 2s rush akım sınırlama | protection.md satır 42, 53 |

## §6 Ölçüm

### §6.1 Ses Performans Eşikleri

| # | Parametre | Hedef | Kaynak |
|---|---|---|---|
| 1 | SNR (A-wtd) | >100dB | snr-thd-measurement.md satır 29 |
| 2 | THD+N @1kHz 1W | <0.01% | snr-thd-measurement.md satır 45 |
| 3 | THD+N @1kHz 10W | <0.001% | snr-thd-measurement.md satır 46 |
| 4 | Noise floor | <-100dB | snr-thd-measurement.md satır 48 |
| 5 | Frekans yanıt 20Hz-20kHz | ±0.5dB (uçlar ±1dB) | frequency-response.md satır 50-56 |
| 6 | Damping factor | >200 @8Ω | class-d.md satır 47 |
| 7 | Çıkış gücü | 100W @8Ω (Class AB) | class-ab.md satır 21 |
| 8 | Verimlilik (Class D) | >90% | class-d.md satır 42 |

### §6.2 Ölçüm Kurulumu

| # | Bileşen | Rol | Kanıt |
|---|---|---|---|
| 1 | PC (Signal Generator) | Sinyal üretimi | snr-thd-measurement.md satır 55 |
| 2 | DAC | Dijital→analog | snr-thd-measurement.md satır 55 |
| 3 | Amplifier | Test edilen | snr-thd-measurement.md satır 55 |
| 4 | Load 8Ω | Dirençsel yük | snr-thd-measurement.md satır 55 |
| 5 | Audio Interface | Geri ölçüm | snr-thd-measurement.md satır 57 |
| 6 | PC (Analyzer) | FFT/analiz | snr-thd-measurement.md satır 59 |

### §6.3 Termal Eşikler

| # | Sıcaklık | Aksiyon | Kanıt |
|---|---|---|---|
| 1 | <50°C | Normal | thermal.md satır 52 |
| 2 | 50-60°C | Uyarı | thermal.md satır 53 |
| 3 | 60-70°C | Fan hızı artır | thermal.md satır 54 |
| 4 | >70°C | Güç azaltma | thermal.md satır 55 |
| 5 | >80°C | Kapanma | thermal.md satır 56 |

## §7 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| R1 | DC offset hoparlör bobinini yakar | Yüksek | Orta | Röle koruması (>0.5V) — kural 10 |
| R2 | Boost converter PVDD aşımı | Yüksek | Düşük | Over-voltage >±48V izleme |
| R3 | Termal kaçış (thermal runaway) | Yüksek | Düşük | θ<1°C/W heatsink + 80°C kapanma |
| R4 | EMC uyumsuzluk — sertifikasyon reddi | Yüksek | Orta | Pre-scan + stitching via + filtre |
| R5 | MLCC DC bias kaybı — filtre kayması | Orta | Orta | X7R yerine C0G kritik noktalar |
| R6 | PLL jitter — SNR bozulması | Orta | Düşük | Düşük-jitter kristal, PLL bypass |
| R7 | PCM5122 kullanımı (YASAK) | Kritik | Düşük | ADR-038 H001: yalnız PCM3168A |
| R8 | Analog/dijital plane karışımı — gürültü | Orta | Orta | Tek nokta birleşim, ayırma |
| R9 | Boot zinciri kopması — sistem açılmaz | Yüksek | Düşük | Aşama bazlı süre bütçesi |
| R10 | Tedarik riski (EOL bileşen) | Orta | Orta | Çoklu kaynak, drop-in alternatif |

## §8 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | SOA | Safe Operating Area — transistörün güvenli çalışma alanı |
| 2 | GBW | Gain-Bandwidth Product — op-amp kazanç-bant genişliği çarpımı |
| 3 | CMRR | Common Mode Rejection Ratio — ortak mod bastırma oranı |
| 4 | ESR | Equivalent Series Resistance — kondansatör eşdeğer seri direnci |
| 5 | SRF | Self-Resonant Frequency — bobin kendi rezonans frekansı |
| 6 | PSRR | Power Supply Rejection Ratio — besleme reddetme oranı |
| 7 | THD+N | Total Harmonic Distortion + Noise — toplam bozulma + gürültü |
| 8 | SNR | Signal-to-Noise Ratio — sinyal/gürültü oranı |
| 9 | PVDD | Power-stage drain supply — güç aşaması besleme gerilimi |
| 10 | GVDD | Gate drive supply — kapı sürme beslemesi |
| 11 | VAS | Voltage Amplifier Stage — gerilim yükselteç aşaması |
| 12 | PLL | Phase-Locked Loop — faz kilitli döngü |
| 13 | MCLK | Master Clock — ana saat |
| 14 | BCLK | Bit Clock — bit saati |
| 15 | LRCLK | Left-Right Clock — kanal seçim saati |
| 16 | DFT | Design for Test — test edilebilirlik tasarımı |
| 17 | AOI | Automated Optical Inspection — otomatik optik muayene |
| 18 | ICT | In-Circuit Test — devre içi test |
| 19 | FCT | Functional Test — işlevsel test |
| 20 | HIL | Hardware-in-the-Loop — donanım döngüde test |
| 21 | DRC | Design Rule Check — tasarım kural denetimi |
| 22 | BGA | Ball Grid Array — bilye dizisi paket |
| 23 | MLCC | Multilayer Ceramic Capacitor — çok katmanlı seramik kondansatör |
| 24 | TCR | Temperature Coefficient of Resistance — sıcaklık katsayısı |
| 25 | Class AB | Yarı iletken çalışma sınıfı — bias ile crossover distorsiyonu azaltılır |
| 26 | Class D | PWM anahtarlamalı amfi — >90% verimlilik |
| 27 | Push-pull | NPN+PNP tamamlayıcı çıkış aşaması |
| 28 | Soft-start | Açılışta rush akımını sınırlayan gecikme |
| 29 | Burn-in | Yaşlanma testi — erken hata yakalama |
| 30 | Yield | Üretim verimi — geçerli ürün oranı |

## §9 Kapsam Dışı

| # | Konu | Sahip | Ayrım |
|---|---|---|---|
| 1 | İşletim sistemi çekirdeği | `K000-isletim-sistemi` | OS ≠ elektronik |
| 2 | OS ↔ donanım kesişimi | `K000-isletim-sistemi/electroncis` | 2 alt-dal — ayrı ajan |
| 3 | Ham içerik (60 dosya) | `.ai/electronic/**` | Kaynak, işlenmemiş |
| 4 | DSP algoritması | `K004-dsp` | Algoritma ≠ devre |
| 5 | Firmware kodu | `K005-firmware` | Kod ≠ donanım |
| 6 | Fiziksel parça envanteri | `K001-donanım` | Var olan ≠ tasarlanan |
| 7 | Web/PHP/JS yüzey | Diğer K-nodes | Elektronik kod repo'da YOK |

## §10 Kaynakça

| # | Kaynak | Tür | Kanıt |
|---|---|---|---|
| 1 | `.ai/electronic/**` (60 dosya) | Vault ham içerik | 2026-10-13 ölçüm |
| 2 | `K000-isletim-sistemi/electroncis/**` (4 dosya) | OS perspektifi | 2026-10-13 ölçüm |
| 3 | `K001-donanım/donanım-anayasa.md` | Komşu anayasa | Band-1 kayıt |
| 4 | ADR-038 | Ses kartı çip seçimi | PCM3168A H001 |
| 5 | ADR-017 | DSP donanım modu | 32-bit float |
| 6 | `.ai/architecture/index.md` §8.1 | Registry | K002 GAP kaydı |
| 7 | TI/ADI/ST/IEEE literatürü | Web doğrulama | dogrulama dosyaları §5 |

### §3.4 Bileşen Seçim Kriterleri

| # | Kriter | Devre | Güç | Sensör |
|---|---|---|---|---|
| 1 | Doğruluk | %1 tolerans | %2 regülasyon | ±%1 kalibrasyon |
| 2 | Sıcaklık | 0-70°C (endüstriyel -40-85°C) | 85°C derating | Sıcaklık kompanzasyonu |
| 3 | Gürültü | <5nV/√Hz op-amp | Ripple <10mV | SNR >80dB |
| 4 | Ömür | 10 yıl hedef | 5 yıl kapasitör | Drift <%/yıl |
| 5 | Tedarik | Çoklu kaynak | Çoklu kaynak | Tek kaynak riski |
| 6 | Paket | 0603/0805 SMD | TO-247/D2PAK | SOP/TSSOP |

### §3.5 Kapasitör Teknolojisi Karşılaştırması

| # | Teknoloji | Avantaj | Dezavantaj | Kullanım |
|---|---|---|---|---|
| 1 | C0G/NP0 | Sıfır piezo, stabil | Düşük yoğunluk | Filtre, osilatör, referans |
| 2 | X7R | Yüksek yoğunluk | DC bias kaybı | Decoupling, bulk |
| 3 | X5R | Ucuz | Sıcaklık kayması | Düşük hassasiyet |
| 4 | Elektrolitik | 10,000µF'e kadar | ESR, ömür | Smoothing, bulk |
| 5 | Tantal | Düşük ESR | Ateşleme modu | RF decoupling |
| 6 | Film | Düşük gürültü | Hacim | Ses yolu, ölçüm |

### §4.4 Arayüz Eşikleri

| # | Arayüz | Maks. uzunluk | Empedans | Eşik |
|---|---|---|---|---|
| 1 | I2S (kart içi) | <10cm | 50Ω trace | BCLK 3.072MHz |
| 2 | SPI | <30cm | 50Ω | 10MHz üst sınır |
| 3 | I2C | <1m | pull-up R | 400kHz fast-mode |
| 4 | USB 2.0 | <5m | 90Ω diferansiyel | 480Mbps |
| 5 | XLR (diferansiyel) | <10m | 600Ω | +4dBu nominal |
| 6 | Hoparlör | <3m | 4-8Ω | ≤500W amfi çıkışı |

## §5.5 Zamanlama Bütçesi (48kHz / 512 örnek — sartname)

| # | Bileşen | Gecikme | Not |
|---|---|---|---|
| 1 | ADC örnekleme | 1/fs = 20.83µs | 48kHz |
| 2 | DSP işleme | ≤10.67ms (512 örnek) | Buffer bütçesi |
| 3 | DAC çıkış | ~1/fs | PCM3168A |
| 4 | Analog filtre | <100µs | Grup gecikmesi |
| 5 | Amfi | <10µs | Slew rate bağlı |
| 6 | Toplam hedef | ≤25ms (dijital+analog) | Sartname (ölçüm PLANNED) |

## §6.4 Ölçüm Yöntemleri

| # | Parametre | Yöntem | Ekipman |
|---|---|---|---|
| 1 | THD+N | FFT analiz, 1kHz sinüs | Audio analyzer (AP) |
| 2 | SNR | A-weighting, 0dBFS referans | Audio analyzer |
| 3 | Frekans yanıt | Sweep 20Hz-20kHz, 1/3 oktav | Sweep jeneratör + analyzer |
| 4 | Çıkış gücü | 8Ω/4Ω yük, %1 THD+N'ye kadar | Grafit yük, osiloskop |
| 5 | Verimlilik | Girdi/güç ölçümü, tam yük | Power analyzer |
| 6 | Termal | Termokupl + thermal kamera | IR kamera |
| 7 | EMC pre-scan | LISI/hızlı tarama | EMC anechoic + receiver |

## §11 Değişiklik Geçmişi

| # | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 1 | 2026-10-13 | Domain kurulumu — 26 dosya (kök 6 + 5×4) | Vault Steward |
| 2 | 2026-10-13 | `.ai/electronic/**` envanteri referans alındı (60 dosya) | Vault Steward |
| 3 | — | Sonraki tur: 3 ajan çıktısıyla derinleştirme | BEKLEMEDE |

## §12 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `electroncis-kapsam.md` §B | Kapsam dışı detayları |
| 2 | `electroncis-bagimliliklar.md` §2 | Bağımlılık matrisi |
| 3 | `electroncis-dogrulama.md` §5-§6 | Web + repo kanıtları |
| 4 | `devre-ve-analog/*` | Katman 1 derinliği |
| 5 | `dijital-ve-mikroislemci/*` | Katman 3 derinliği |
| 6 | `guc-yonetimi/*` | Katman 3 güç derinliği |
| 7 | `sensor-ve-akteyici/*` | Katman 4 derinliği |
| 8 | `imalat-ve-test/*` | Katman 2+5 derinliği |

---

## §13 Utility / Parasitik Topolojiler

### §13.1 Sıralı Seviye ve Kademeli Başlangıç

Sistemde birden çok besleme alanı varsa tek MASTER_Enable kullanmak yerine her seviyenin kendi ENABLE'ını üretip seviyeleri sabit RC gecikmesiyle kademeli açmak yaygın ve doğru çözümdür. Bu, anahtarlama anındaki toplam giriş akımını sınırlar ve inrush'u kontrol eder. Vadatech uygulama notu, kademeli başlangıç topolojisinin hem ortak hem de online modellerde analojik ve dijital seviyelerin ENABLE gecikmesini RC ile belirlediğini, böylece ilk seviye tam güçte çalışırken sonrakinin bir öncekini başlatması gerektiğini anlatır.

### §13.2 Süperkapasitör Eşitleme / Balans

Birbirine seri bağlı süperkapasitör hücrelerinin eşitlenmesi, HER hücreye 100 kOhm gibi eşit bir direnç koymakla olur — pasif eşitleme dirençleri, seri süperkapasitör bankalarında hücreler arası gerilim farkını düzeltir ve en zayıf hücrenin (düşük eşik gerilimli olanın) erken aşırı yüklenmesini önler. Bu, sıradan ama kritik bir uygulama kuralıdır.

### §13.3 Back-Feed / Vida Besleme (Back-Power)

Standby gücünü çıkarıcı devreden İLERİ doğru (back-feed) vererek C6/pilot devreyi ilk uyandırmak, ayrı bir pilot devre gerektirmeden kapsamlı bir boot yaklaşımı sağlar; çıkarıcı devre kademeli olarak devreye girerek BOOST aşamasına kadar ilerler. Bu topoloji boot-sequence.md:35-43'in ayrı pilot devre kuralının alternatifidir; iki yaklaşım birlikte kullanılmaz, sistem mimarisi karar verir. Vicor dokümanı back-feed'i başlangıç senaryosunun öncül koşulu olarak verir, tek başına bir kural olarak değil.

## §14 Ölçüm / Test Eşikleri Özeti

### §14.1 Elektriksel Test Eşikleri (kart seviyesi)

| # | Test | Yöntem | Kabul | Kanıt |
|---|---|---|---|---|
| 1 | Continuity | multimetre, ohm | kısa yok, net yok | pcb-design.md test bölüm satır 55+ |
| 2 | Power-rail short | güç vermeden önce ohm | VCC-GND kısa yok | boot-sequence.md satır 28 öncesi |
| 3 | Ripple ölçümü | osiloskop, AC coupling | <10mV pk-pk | power-supply.md satır 70+ |
| 4 | DC offset | DC coupled, çıkış | <0.5V (röle eşiği) | protection.md satır 37 |
| 5 | Bias akımı | seri ampermetre | Sınıf-A tasarım değeri | class-ab.md satır 55+ |

### §14.2 Ses Performans Test Sırası (üretim hattı)

| # | Test | Ekipman | Sınır | Karar |
|---|---|---|---|---|
| 1 | Frekans yanıt | sweep + analyzer | 20Hz-20kHz ±0.5dB | frequency-response.md satır 50-56 |
| 2 | THD+N @1W | 1kHz sinüs | <0.01% | snr-thd-measurement.md satır 45 |
| 3 | THD+N @10W | 1kHz sinüs | <0.001% | snr-thd-measurement.md satır 46 |
| 4 | SNR | A-wtd, 0dBFS | >100dB | snr-thd-measurement.md satır 29 |
| 5 | Noise floor | girişi kısa devre | <-100dB | snr-thd-measurement.md satır 48 |
| 6 | Çıkış gücü | 8Ω, %1 THD+N'ye kadar | 100W (Class AB) | class-ab.md satır 21 |
| 7 | Damping factor | 8Ω @1kHz | >200 | class-d.md satır 47 |

### §14.3 EMC Test Aşamaları

| # | Aşama | Yer | Amaç | Durum |
|---|---|---|---|---|
| 1 | Pre-scan | İç laboratuvar | Yayılım haritası | development-workflow.md satır 232 |
| 2 | Conducted emissions | Akredite lab | CISPR sınırları | ⚠️ VERIFICATION REQUIRED (standart noyu) |
| 3 | Radiated emissions | Akredite lab | CISPR sınırları | ⚠️ VERIFICATION REQUIRED (standart noyu) |
| 4 | Immunity | Akredite lab | IEC 61000 serisi | ⚠️ VERIFICATION REQUIRED (standart noyu) |

**Not:** EMC standart numaraları (CISPR 32, IEC 61000-4-x vb.) bu turda diskten
doğrulanmadı — ⚠️ VERIFICATION REQUIRED olarak işaretlidir, uydurulmadı.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
