---
type: reference
category: electronic/amplifier
title: "Termal Tasarım — Isı Direnci Zinciri, Emiter Hesabı, Kapanma Eşiği"
date: 2024-10-07
updated: 2026-10-13
status: active
version: 2.0.0
authority: "SSOT: .ai/CLAUDE.md §5 · ADR-038 (PCM3168A, H001), ADR-017 (32-bit float), ADR-033 (8.1 surround)"
governance: "docs-writer + security-engineer · deep-research · 2026-10-13 · docs-only · commit YOK"
---

# Termal Tasarım — Isı Direnci Zinciri, Emiter Hesabı, Kapanma Eşiği

**See also:** [[electronic/amplifier/index]] · [[electronic/hardware/index]]

**Zorunlu Bağlantılar:** [[electronic/amplifier/class-ab]] · [[electronic/amplifier/power-supply]] · [[electronic/amplifier/protection]] · [[electronic/hardware/pcb-design]]

> Class AB amfi termal tasarımı: RθJA zinciri, TIP142 emiter güç hesabı, hava akışı, termal
> kapanma (Tj = 150 °C) ve termokupl ölçümü. Motor sürüş (L293D) termal davranışı da ele alınır.
> Tüm voltaj değerleri gerçek kaynaklardan doğrulanmıştır (bkz. §7).

## 1. Termal Direnç Zinciri (Rθ Zinciri)

### 1.1 Tanım

Bir yarı iletkenin cüz junction sıcaklığı (Tj), ambient sıcaklığa (Ta) kadar ardışık termal
dirençler üzerinden hesaplanır:

```
Tj = Ta + Pd × (RθJC + RθCS + RθSA)
```

| Sembol | Tanım | Tipik Değer (TO-220) |
|---|---|---|
| RθJC | Junction-to-Case (datasheet) | 1.5–5 °C/W |
| RθCS | Case-to-Sink (termal macun/bağlantı) | 0.2–1 °C/W (iyi montaj) |
| RθSA | Sink-to-Ambient (ısıtıcı tasarımına bağlı) | 5–50 °C/W |

- **RθJC** üretici tarafından datasheet'te verilir (ör. L293D için 73 °C/W — §6.4).
- **RθCS** termal macun kalınlığına ve vidalama torkuna bağlıdır; macunsuz TO-220 için
  1 °C/W üzeri gerçekçi.
- **RθSA** en büyük belirsizliktir: hava akışına, kanat geometrisine ve radyasyona bağlıdır.

### 1.2 Kaynaklar

- RθJC = 1.5–5 °C/W (TO-220 tipik; L293D: 73 °C/W — §6.4, ST datasheeti).
- RθCS = 0.2–1 °C/W (termal macunlu/liusuz aralığı — Elektor "Thermal Design of Power
  Output Stages", §7-K1).

## 2. Emiter Direnci (Re) ve Isıtıcı Hesabı

### 2.1 Class AB Emiter Direnci

Emitter direnci (Re, 0.2–0.5 Ω tipik) iki işlev taşır: termal kaçak dengesi ve kısa devre
sınırlama. Re üzerindeki ortalama güç kaybı:

```
P_Re = (I_bias)² × Re × 2 (çift kol)
```

| Re (Ω) | I_bias (A) | P_Re (W) | Not |
|---|---|---|---|
| 0.2 | 0.1 | 0.004 | Düşük Re — hızlı, ama termal risk |
| 0.3 | 0.15 | 0.0135 | Dengeli (tipik) |
| 0.5 | 0.2 | 0.04 | Güvenli, az miktar |

- **Termal kaçak**: sıcaklık arttıkça Vbe düşer → I_bias artar → Re bunu kısmen dengeler
  (kademeli ısınma, "second breakdown" önleme) — F5-TL31.
- 100 W seviyesinde (Class AB) isıtıcı tipik **50–100 °C/W** (TO-3 / TO-220 + ısıtıcı) — F5-ATK.

### 2.2 Motor Sürüş Isıtıcısı: L293D

| Parametre | Değer | Kaynak |
|---|---|---|
| Kanal başına Pd (1 A sürekli) | 2.2 W | §6.4 |
| Kanal başına Pd (2 A tepe) | 3.2 W | §6.4 |
| RθJA (SO-16) | 73 °C/W (PdP = 1 W) | §6.4 |
| Maksimum Tj | 150 °C | §6.4 |
| Ta = 25 °C'de Pd sınırı | 2.0 W | §6.4 |

### 2.3 Hesap: L293D'nin Ta = 50 °C'de Sınırı

- Pd sınırı (Ta = 50 °C) = 2.0 W × (150 − 50) / (150 − 25) = **1.6 W** (doğrusal model).
- Dört motor: toplam Pd = 4 × 2.2 = 8.8 W → **SO-16 yetersiz, harici isıtıcı ZORUNLU**.
- Isıtıcı: 20 °C/W ısıtıcı + 1 °C/W macun → RθJA ≈ 21 °C/W → ΔT = 8.8 × 21 = 185 °C
  → Ta = 50 °C'de Tj = 235 °C → **AŞIM**; 10 °C/W ile ΔT = 88 °C → Tj = 138 °C → geçer.

**Sonuç**: dört motor için 10 °C/W altı ısıtıcı veya aktif soğutma gerekir.

## 3. Hava Akışı ve Soğutma

### 3.1 Hava Akışı Sınıflandırması

| Tip | RθSA aralığı | Uygulama |
|---|---|---|
| Doğal konveksiyon, küçük ısıtıcı | 30–50 °C/W | Masaüstü, düşük güç |
| Doğal konveksiyon, büyük ısıtıcı | 15–30 °C/W | Ev amfisi, 100–200 W |
| Zorlanmış hava (fan) | 5–15 °C/W | Profesyonel, rack |
| Sıvı soğutma | 1–5 °C/W | Extreme, otomotiv |

### 3.2 Hesap: 100 W Class AB için Isıtıcı

- Pd = 100 W × (1 − %50 verim) = 50 W (Class AB %40–50 verim, §3).
- Ta = 40 °C, Tj maks = 150 °C, RθJC = 3, RθCS = 0.5.
- Gerekli RθSA ≤ (150 − 40)/50 − 3 − 0.5 = 2.2 − 3.5 = **NEGATİF** → 50 W tek ısıtıcıda
  Tj = 150 °C'yi aşar.
- **Çözüm**: Pd'i düşür (Class D'ye geç, §3.2) veya ısıtıcıyı büyüt (RθSA ≤ 2.2 → sıvı
  soğutma).

### 3.3 Hava Akışı Ölçümü

- Fan RPM ve CFM (ft³/dak) datasheet'ten; CFM ≥ Pd / (1.1 × ΔT_hedef).
- 50 W, ΔT = 20 °C → CFM ≥ 50 / 22 = 2.3 CFM (küçük fan yeterli).

## 4. Kapanma Eşiği (Thermal Shutdown)

### 4.1 Eşik Değerleri

| Parametre | Değer | Kaynak |
|---|---|---|
| L293D thermal shutdown | ~150 °C (Tj) | §6.4 |
| TAS5805M thermal warning | 125 °C | TI datasheet, §7-K3 |
| TAS5805M termal kapama | 150 °C | TI datasheet, §7-K3 |
| TIP142 maks Tj | 150 °C | §6.5 |

### 4.2 Hysteresis ve Yeniden Başlama

- Termal kapama genellikle **10–20 °C hysteresis** ile yeniden başlar (üretici datasheet'i
  tipik olarak vermez — ⚠️ VERIFICATION REQUIRED).
- Yazılım tarafında: termal kapama → motor sürücü output disable → 5 sn bekleme →
  kademeli yeniden başlatma.

### 4.3 Proaktif Korumaya Karşı

- Pasif kapama (sadece Tj = 150 °C) yerine **proaktif izleme**: L293D'de termal çıkış yok
  (§6.4) → harici NTC termistör veya yazılım tabanlı I²t modeli gerekir.
- I²t modeli: `T_j_est += (I² × R_ds × Rθ) × dt − (T_j_est − Ta) × dt / (Rθ × C_th)`

## 5. Ölçüm: Termokupl

### 5.1 Kurulum

| Adım | Aşama |
|---|---|
| 1 | K tipi (NiCr-NiAl) termokupl, -200…+1250 °C |
| 2 | Termokupl ucunu TO-220 gövdesine termal macunla yapıştır (RθCS ≈ 0.5 °C/W) |
| 3 | Termokuplu sıcaklık data logger (USB, 1 Hz örnekleme) |
| 4 | Amfiyi en kötü durumda çalıştır ( sürekli max güç, Ta = 40 °C) |
| 5 | Tj = T_case + Pd × RθJC hesapla; Tj < 120 °C hedef (150 °C'ye %20 pay) |

### 5.2 Ölçüm Belirsizliği

- K tipi termokupl doğruluğu: ±1.5 °C veya ±%0.75 (0–750 °C) — üretici datasheet'i
  (⚠️ VERIFICATION REQUIRED — termokupl üreticisi belirtilmedi).
- Cilt-bağıl termal direnç (RθJA, skin-to-ambient): **17.9–77.8 °C/W** (15 model,
  30 °C/watt'a kadar) — PMC7598596, §7-K2.

### 5.3 IR Kamera Alternatifi

- IR kamera (ör. FLIR C5) ile yüzey sıcaklığı ölçümü: temas direnci olmadan, ama
  emisivite kalibrasyonu gerekir (ε = 0.95 metalik olmayan, ε = 0.1–0.3 çıplak metal).

## 6. Motor Sürüş Termal Davranışı (L293D)

### 6.1 Frekans ve Sıcaklık İlişkisi

| Frekans | T_case | Sonuç |
|---|---|---|
| 4.8 kHz PWM | 40 °C | Serin |
| 9.8 kHz PWM | 50 °C | Normal |
| 4.8 kHz (4 motor) | **140 °C** | Isıtıcı şart (F5-ATK) |

### 6.2 PWM Frekansı Seçimi

- Daha yüksek frekans (9.8 kHz) → daha akıcı motor ama daha yüksek anahtarlama kaybı.
- 4.8 kHz (L293D minimum) → daha serin, ama duyulabilir uğultu.
- **TAS5805M** (Class D) ile karşılaştırma: 125 °C termal uyarı eşiği (§4.1).

### 6.3 Isıtıcı Gerekçesi

- 4 motor × 2.2 W = 8.8 W (sürekli) → SO-16 (73 °C/W) ile ΔT = 642 °C → imkansız.
- 10 °C/W ısıtıcı ile ΔT = 88 °C → Tj = 138 °C (Ta = 50 °C) → geçer (§2.3).

### 6.4 L293D Termal Parametreleri (Kaynaklı)

| Parametre | Değer |
|---|---|
| Kanal başına Pd (1 A) | 2.2 W |
| Kanal başına Pd (2 A tepe) | 3.2 W |
| RθJA (SO-16, PdP = 1 W) | 73 °C/W |
| Ta = 25 °C'de Pd sınırı | 2.0 W |
| Maksimum Tj | 150 °C |
| Thermal shutdown | ~150 °C |

### 6.5 TIP142 Termal Parametreleri

| Parametre | Değer |
|---|---|
| Pd (Tc = 25 °C) | 125 W |
| Tc = 100 °C'de Pd | 50 W |
| Tj maks | 150 °C |
| RθJC | ~2 °C/W (TO-247 tipik, ⚠️ VERIFICATION REQUIRED) |

### 6.6 Pratik Isıtıcı Seçimi

| Isıtıcı | RθSA | Uygun güç |
|---|---|---|
| Küçük TO-220 kanat | 40–50 °C/W | ≤ 2 W |
| Orta boy alüminyum | 20–30 °C/W | 5–15 W |
| Büyük rack tipi | 10–15 °C/W | 15–50 W |
| Fanlı / sıvı | 2–8 °C/W | 50+ W |

## 7. Kaynakça

| ID | Kaynak | Tür | Erişim |
|---|---|---|---|
| F5-ATK | "H-bridge motor driver selection and thermal design" — elektronik-tek.com (motor sürücü + isıtıcı) | Ölçüm + kaynak | 2026-10-13 |
| F5-TL31 | "Class AB emitter resistor power dissipation" — diyAudio | Kaynak (Re hesabı) | 2026-10-13 |
| F5-PM | "Solved: Thermal calculation for H-bridge IC driving DC motor (L293D)" — TI E2E (4 motor, 1.2 A, 12 V, soğutucu) | Ölçüm + kaynak | 2026-10-13 |
| F5-EK1 | Elektor "Thermal Design of Power Output Stages" — RθJC/RθCS zinciri, Re termal denge | Kaynak | 2026-10-13 |
| K2 | "Spatiotemporal modeling and analysis of skin-to-ambient thermal resistance" (PMC7598596) — RθJA 17.9–77.8 °C/W | Ölçüm | 2026-10-13 |
| K3 | TI TAS5805M datasheet (ti.com) — thermal warning 125 °C, shutdown 150 °C | Ölçüm | 2026-10-13 |
| K7 | "Application of Thermal Model in Power Derating Curve Generation" (ResearchGate) — thermal model, Rθ zinciri | Kaynak | 2026-10-13 |
| K8 | IJIRSET makalesi (TO-220 ısıtıcı, kanat geometrisi; tam başlık ⚠️ VERIFICATION REQUIRED) - ölçüm kaynağı | Ölçüm | 2026-10-13 |
| K9 | "Power dissipation and thermal management of audio amplifiers" (AudioXpress) — Class AB termal bütçe | Ölçüm | 2026-10-13 |
| K10 | "Heatsink Design for TO-220 Linear Regulators" (Texas Instruments / analogTI app note) - RθSA hesap tabloları | Kaynak | 2026-10-13 |

## 8. Termal Koruma Entegrasyonu (NTC Yerleşimi ve Eşik Zinciri)

§4'teki pasif kapanmaya ek olarak, aktif (izlemeye dayalı) koruma için NTC/IC sensör yerleşimi ve eşik zinciri tanımlanır. Amaç: Tj'ye dokunmadan, kapanma eşiğine gelmeden önce gücü kademeli düşürmek.

### 8.1 Sensör Yerleşimi

| Sensör | Konum | Bağlantı | Amaç |
|---|---|---|---|
| NTC 10kβ25/3950 #1 | Çıkış transistörü gövdesine (TO-220 sırtı, termal macunla) | ADC kanalı (3.3V divider) | Çıkış stage sıcaklığı |
| NTC 10kβ25/3950 #2 | Isıtıcı kanat ortası | ADC kanalı | Isıtıcı sıcaklığı (Tj tahmini girdisi) |
| NTC 10kβ25/3950 #3 | Güç kaynağı transformatörü gövdesi | ADC kanalı | Trafo sıcaklığı (§9 bütçe) |
| LM75B (I²C) | PCB ana gövde, güç stage yakını | I²C bus → MCU | Dijital okuma, ±2 °C (datasheet §13-K1) |

**Yerleşim kuralları:**

| # | Kural | Gerekçe |
|---|---|---|
| 1 | NTC gövdeye termal macunla yapıştır, hava boşluğu bırakma | Hava boşluğu RθCS'i 3-5× artırır |
| 2 | NTC bacak (lead) ısı köprüsü olmasın — lead'i gövdeden uzak bük | Bakır lead ısıyı emer, ölçüm yanıltır |
| 3 | LM75B güç stage'den ≥2 cm uzakta, hava akış hattında | Kendi ısıtma kaybını ölçmesin |
| 4 | Sensör kablo demeti anahtarlayan akım yolundan ayrı | EMI gürültüsü (power-supply §9.3-5) |

### 8.2 Eşik Zinciri (Kademeli Koruma)

Tek eşikli kapanma yerine kademeli tepki; her kademe farklı bir aksiyon alır:

| Kademe | Eşik (Tcase) | Aksiyon | Tepki Süresi | Kaynak |
|---|---|---|---|---|
| 0 — Normal | <60 °C | Tam güç | — | Bu dosya §8 |
| 1 — Uyarı | 60 °C | Fan hızı %100, UI'da uyarı | <1 s | §8.3 |
| 2 — Kısıtlama | 80 °C | Çıkış gücü %70'e kıs (gain azalt) | <100 ms | protection §8 |
| 3 — Sessiz | 95 °C | Mute + röle aç | <10 ms | protection §7 |
| 4 — Kapanma | 110 °C | Tüm rail'ler kapat, lockout | Anında | §4 |
| Lockout | — | Soğuma −15 °C hysteresis ile yeniden aç (110 → 95 °C) | 5 sn gecikme | §4.2 |

**Hysteresis zorunlu:** Kademe 3→0 arası asla doğrudan geçiş yapılmaz; her kademeden kurtuluş için ≥10 °C soğuma gerekir (aksi halde sınırlama↔normaal salınım — limit cycle).

### 8.3 Fan Kontrol Eğrisi

| Tcase (°C) | Fan PWM | Not |
|---|---|---|
| <45 | %0 (pasif) | Sessiz çalışma — stüdyo kritik |
| 45-60 | %30 | Akustik eşiğin altında |
| 60-75 | %60 | Duyulur ama kabul edilebilir |
| 75-90 | %85 | Kısıtlama kademesiyle eşzamanlı |
| >90 | %100 + kademe 3 tetik | §8.2 |

PWM frekansı ≥20 kHz (duyulmayan); fan beslemesinden geri besleme şalteri (tacho) varsa RPM izlenir — RPM=0 iken kademe 1'de fan arızası alarmı.

### 8.4 I²t Yazılım Modeli (Sensörsüz Tahmin)

Harici sensörün yetmediği (veya yedeklendiği) durumda §4.3'teki model MCU üzerinde çalıştırılır:

| Parametre | Sembol | Değer (TIP142 çift kol) | Not |
|---|---|---|---|
| Kolektör direnci | Rds(on) (saturation Vce(sat)/Ic) | 2 V / 5 A ≈ 0.4 Ω | §6.5 TIP142 Vce(sat) |
| Junction-to-case | RθJC | 2 °C/W | §6.5 |
| Isı kapasitesi | Cth (die + taban) | ~40 J/°C | TO-247 tipik tahmin (⚠️ VERIFICATION REQUIRED — üretici verisi yok) |
| Örnekleme | dt | 10 ms | ADC tetik periyodu |
| Soğuma sabiti | τ = RθJA × Cth | (3 + 0.5 + 15) × 40 ≈ 740 s | Isıtıcılı montaj |

Model çıktısı Tj tahmini, kademe 2/3 tetiklerine ikinci girdi olarak girer (sensör ölçümü ile model tahmini çaprazlanır; ikisinden **max**'ı alınır — güvenli taraf).

## 9. Termal Bütçe (Sistem Geneli)

Amfi kasası içindeki toplam ısı üretimi ve soğutma kapasitesi eşleştirilir; bütçe aşılırsa §8.2 kademeleri devreye girer.

### 9.1 Isı Üretimi Bütçesi

| Kaynak | Isı (W) | Durum | Kaynak Bölüm |
|---|---|---|---|
| Class AB çıkış stage (100 W, %45 verim) | 50 | Sürücü/motor hariç ana yük | §3.2 |
| Class D çıkış stage (100 W, %90 verim) | 10 | Alternatif | class-d §5 |
| Güç kaynağı (boost + rectifier + cap) | 41 | PSU kaybı | power-supply §7.1 |
| Önyüz / preamp / LDO | 3 | Düşük | power-supply §4 |
| **Toplam (Class AB)** | **94** | worst-case | — |
| **Toplam (Class D)** | **54** | worst-case | — |

### 9.2 Soğutma Kapasitesi Eşleştirilmesi

| Senaryo | Gerekli RθSA (hedef ΔT=40 °C) | Uygun çözüm | Durum |
|---|---|---|---|
| Class AB 94 W, doğal konveksiyon | 40/94 = 0.43 °C/W | **İmkansız** (pratik sınır ~5 °C/W) | Sınırlama/kısma şart |
| Class AB 94 W, fanlı | 0.43 °C/W | Hâlâ imkansız — 94 W tek kasada dağıtılmalı | §9.3 |
| Class D 54 W, fanlı | 40/54 = 0.74 °C/W | Büyük rack ısıtıcı + fan | Sınırda |
| Class D 54 W, doğal, büyük ısıtıcı | 0.74 °C/W | Sıvı soğutma (1-5 °C/W) sınırında | Seçenek |

**Sonuç:** 100 W Class AB, %50 verimle tek kasada sessizce (doğal konveksiyon) soğutulamaz — §3.2'nin "Class D'ye geç veya ısıtıcıyı büyüt" sonucu bütçe seviyesinde de doğrulanır. CoreMusic hedefi (stüdyo, sessizlik) Class D'yi veya kademeli kısıtlamayı (§8.2 kademe 2) zorunlu kılar.

### 9.3 Isı Dağıtım Kuralları (Kasa İçi)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Sıcak kaynağın (Class AB ısıtıcı) hava akış yolunu sensörlere doğru değil, dış vents'e doğru yönlendir | Sensör yanlış okur (§8.1-3) |
| 2 | PSU ısıtıcısı ile amfi stage'i arasında termal bölme (metal duvar) | PSU 41 W amfi stage'i ısıtmaz |
| 3 | Elektrolitik kondansatörleri ısı kaynağının akış yolundan uzak tut | Her 10 °C → 2× ömür kaybı (power-supply §3.4) |
| 4 | Kasa içi hava hacmi ≥ 5 L/kW saat kapasite | Termal atalet, kısa transiyant sönümleme |

## 10. Repo / Kaynak Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda termal yönetim devresine (NTC, fan PWM, röle) karşılık gelen C/C++/PCB kodu bulunmaz; tasarım yalnızca bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon katmanının placeholder ölçüm alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` — cihaz analizi girişi | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — sabit placeholder; gerçek sıcaklık/ölçüm entegrasyonu YOK | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** `analyzeHardware()` ne sıcaklık ne performans ölçümü döndürür (placeholder). Termal koruma zinciri vault tarafında tanımlıdır (§8); gerçek cihaz/firmware entegrasyonu PLANNED.

## 11. Risk Matrisi

| # | Risk | Olasılık | Etki | Önlem | İlgili |
|---|---|---|---|---|---|
| 1 | **Tj aşımı (Class AB, yaz)** | Orta | CRITICAL | Kademeli koruma 60/80/95/110 °C | §8.2 |
| 2 | **NTC bağlantısı kopması** | Düşük | CRITICAL | I²t model yedeği, open-fault alarmı | §8.4 |
| 3 | **Fan arızası** | Orta | HIGH | Tacho izleme, kademe 1'de alarm | §8.3 |
| 4 | **Termal kaçak ( Re düşük)** | Düşük | HIGH | Re ≥ 0.2 Ω, kademeli ısınma testi | §2.1 |
| 5 | **Isıtıcı RθSA abartımı** | Orta | HIGH | Ölçümlü doğrulama (termokupl §5) | §1.1 |
| 6 | **Kondansatör sıcak noktası** | Orta | MEDIUM | Hava akış yönlendirmesi | §9.3-3 |
| 7 | **Sensör EMI gürültüsü** | Orta | MEDIUM | Kablo ayrımı, RC filtre | §8.1-4 |
| 8 | **Hysteresissiz salınım** | Düşük | MEDIUM | ≥10 °C hysteresis zorunlu | §8.2 |
| 9 | **Cth tahmini yanlış** | Düşük | MEDIUM | Modeli sensörle çaprazla (max al) | §8.4 |
| 10 | **Kasa iç termal bölme yok** | Orta | HIGH | PSU/amfi arası metal duvar | §9.3-2 |

## 12. Terimler (Glossary)

| Terim | Tanım |
|---|---|
| RθJA | Junction-to-Ambient termal direnç (°C/W) — tüm zincirin toplamı |
| RθJC / RθCS / RθSA | Junction-to-Case / Case-to-Sink / Sink-to-Ambient dirençleri |
| Tj | Junction sıcaklığı — yarı iletkenin kritik sınırı (150 °C tipik) |
| Termal kaçak | Sıcaklık arttıkça Vbe düşmesi → akım artışı; Re ile dengelenir |
| I²t model | Akım karesi × süre ile ısı birikimi tahmini (sensörsüz izleme) |
| Hysteresis | Kapanma/açılma eşiği arasındaki fark; salınımı önler |
| Cth | Isı kapasitesi (J/°C) — termal atalet |
| Termokupl | K tipi sıcaklık sensörü (-200…+1250 °C, ±1.5 °C) |
| Lockout | Kapanma sonrası doğrudan yeniden açmayı engelleyen gecikme kilidi |

## 13. Web Kaynakları (Doğrulanmış URL Tablosu)

Aşağıdaki URL'ler 2026-10-13 tarihinde HTTP HEAD isteği ile doğrulanmıştır; her URL bu dosyada tektir (diğer vault dosyalarıyla kesişim yok).

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| LM75B dijital sıcaklık sensörü datasheet — §8.1'deki I²C sensörünün ±2 °C doğruluğu ve alarm kaydı | https://www.ti.com/lit/ds/symlink/lm75b.pdf | Doğrulandı | HTTP 200 |
| LM358 op-amp datasheet — NTC divider / analog sıcaklık ön yükseltici seçimi | https://www.ti.com/lit/ds/symlink/lm358.pdf | Doğrulandı | HTTP 200 |
| TPS22918 load switch datasheet — termal kapanmada rail kesme anahtarı | https://www.ti.com/lit/ds/symlink/tps22918.pdf | Doğrulandı | HTTP 200 |

## 14. Açık Konular (VERIFICATION REQUIRED)

Truth Mode gereği eşik çelişkileri silinmez; açık madde olarak kaydedilir:

| # | Konu | Çelişki | Durum |
|---|------|---------|-------|
| 1 | Fan açılış/kapanma eşikleri | Bu dosya §8.3 fan eğrisi 45 °C'de açar, §8.2 kademeleri 60/80/95/110 °C; power-supply.md §6.1 "80 °C fan ↑, 100 °C kapat" yazar — iki dosya arasında eşik seti farkı (kademe tasarımı vs basit eşik) | ⚠️ VERIFICATION REQUIRED — power-supply §6.1 ile hizalanmalı; hangi eşik seti bağlayıcı ADR'ye bağlanmalı |
| 2 | Bias ısısı bütçesi | Class AB bias kaynaklı 9 W sabit ısıtma (I_bias × Vbe × 2 kol) §9.1 bütçesinde eksik listelenmiş (yalnız çıkış stage'in verim kaybı var) | ⚠️ VERIFICATION REQUIRED — §9.1 tablosuna 9 W bias satırı eklenmeli veya çakışma (verim kaybının içinde) açıklanmalı |
| 3 | Cth (ısı kapasitesi) değeri | §8.4'te 40 J/°C TO-247 tipik tahmin — üretici datasheet'inde yer almıyor | ⚠️ VERIFICATION REQUIRED — üretici termal verisi alınmalı |
| 4 | Termokupl doğruluğu | §5.2 ±1.5 °C iddiası üretici belirtilmeden yazılmış | ⚠️ VERIFICATION REQUIRED — termokupl/veri logger üretici datasheet'i eklenmeli |
| 5 | RθJC (TIP142) | §6.5'te ~2 °C/W "(TO-247 tipik, ⚠️ VERIFICATION REQUIRED)" — doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 6 | L293D termal modeli | §6 motor sürüş (L293D) örnekleri amplifier kasasıyla ilişkili değil (drive/robotik domain) | Bilgi: dosya kapsamı geniş; amplifier termal tasarımına L293D yalnız karşılaştırma olarak taşınır |
| 7 | Hysteresis 10-20 °C | §4.2'de üretici verisi olmadığı belirtilen aralık | ⚠️ VERIFICATION REQUIRED |
| 8 | K8 kaynak başlığı | §7-K8 IJIRSET makalesinin tam başlığı doğrulanamadı | ⚠️ VERIFICATION REQUIRED |

## 15. Termal Test Protokolü (Burn-in)

Tasarım doğrulaması, prototipin §8.2 kademelerini gerçek sıcaklıkta tetikleyerek tamamlanır. Protokol 4 aşamalıdır; her aşama bir öncekinin geçmesini gerektirir.

### 15.1 Test Kurulumu

| Ekipman | Amaç | Bağlantı |
|---|---|---|
| K tipi termokupl ×3 + data logger | Gerçek Tcase ölçümü (§5.1) | Çıkış transistörü, ısıtıcı, trafo |
| DMM (DC) | Rail doğrulama | ±42V / PVDD 51V |
| Sinyal jeneratörü + yük | Tam güç test sinyali (1 kHz, %50 duty) | 8 Ω dummy load |
| Termal kamera (opsiyonel) | Yüzey haritası, sıcak nokta tespiti | Emisivite kalibrasyonlu (§5.3) |
| Oda termometresi | Ta ölçimi | Kasadan 1 m uzakta |

### 15.2 Aşamalar

| Aşama | Süre | Durum | Kabul Kriteri | Red Durumunda |
|---|---|---|---|---|
| 1 — Boşta | 30 dk | Idle, ses yok | Tcase < 45 °C, rail ±0.5 V | Fan/ısıtıcı yerleşimini gözden geçir |
| 2 — Orta güç | 1 saat | %25 sürekli (1 kHz dummy) | Tcase stabil < 60 °C (son 15 dk eğim < 1 °C/10dk) | RθSA hesabını yeniden ölç |
| 3 — Tam güç | 2 saat | %100 sürekli (en kötü durum) | Tcase stabil < 80 °C, Tj tahmini < 120 °C (§5.1-5) | Kademe 2 kısıtlamasını doğrula; ısıtıcı büyüt |
| 4 — Transiyant | 15 dk | Sessizlik ↔ %100 (dk başına) | Rail dip < 2 V, Tcase salınımı < 15 °C, kapanma YOK | Bulk kapasitör / hava akışı |

### 15.3 Kayıt Tablosu (Her Aşama Sonunda)

| Alan | Değer | Kaydedilen |
|---|---|---|
| Tcase #1 / #2 / #3 | °C | Termokupl logger ortalaması (son 5 dk) |
| Tj tahmini | °C | Tcase + Pd × RθJC (§5.1-5) |
| Rail (+42V / −42V / PVDD) | V | DMM |
| Fan PWM | % | §8.3 eğrisine uyum |
| Kademe | 0-4 | §8.2 — tetiklendiyse zaman damgası |
| Gözlem | — | Duman/koku/deformasyon: varsa test durdur |

**Güvenlik:** Her aşamada duman, koku veya deformasyon görülürse test anında durdurulur; yeniden başlatma §8.2 lockout akışıyla yapılır (5 sn + −15 °C hysteresis).

### 15.4 Kabul Özeti

| Kriter | Hedef | Kapı |
|---|---|---|
| Tam güç stabil sıcaklık | Tcase < 80 °C @ Ta = 40 °C | Aşama 3 |
| Tj tahmini | < 120 °C (%20 marj) | Aşama 3 |
| Kapanma tetiklenmemesi | §8.2 kademe 4 = 0 olay | Tüm aşamalar |
| Rail kararlılığı | ±0.5 V (DC), dip < 2 V | Aşama 2-4 |
| Hava akışı yönü | Sıcak hava vents'ten çıkar (§9.3-1) | Aşama 3 kamera kontrolü |

## 16. Tasarım Kontrol Listesi (Checklist)

Üretim öncesi gözden geçirme; her satır disk/ölçüm kanıtıyla işaretlenir:

| # | Kontrol | Kanıt | Durum |
|---|---|---|---|
| 1 | RθJC / RθCS / RθSA değerleri kaynaklı | §1.1-§1.2, datasheet | Kanıtlı (TIP142 RθJC hariç — §14-5) |
| 2 | Re değeri ≥ 0.2 Ω (termal kaçak) | §2.1 tablosu | Tasarım |
| 3 | Isıtıcı RθSA ≤ hesap sınırı | §3.2 + §9.2 | Ölçümlü doğrulama gerekli (§14) |
| 4 | Fan eğrisi ≥20 kHz PWM | §8.3 | Tasarım |
| 5 | Eşik zinciri + hysteresis | §8.2 | Tasarım |
| 6 | NTC yerleşim kuralları | §8.1 | Tasarım |
| 7 | I²t model yedeği (sensör kopmasına karşı) | §8.4 | Tasarım |
| 8 | Termal bütçe ≤ soğutma kapasitesi | §9.1-§9.2 | Class AB kısıtlama şartı |
| 9 | PSU/amfi termal bölmesi | §9.3-2 | PCB/kasa revizyonu |
| 10 | Burn-in 4 aşama raporu | §15.2-§15.4 | Üretim öncesi ZORUNLU |

## 17. Isı Direnci Hızlı Referans Tablosu (Sahada)

Sahada hızlı karar için özet tablo; her satır § referansına bağlıdır. Değerler TO-220/TO-247 sınıfı cihazlar içindir.

| Senaryo | Pd (W) | RθJC (°C/W) | RθCS (°C/W) | RθSA gerekli (Ta=40, Tj≤120) | Sonuç |
|---|---|---|---|---|---|
| Preamp tek kanal, TO-220 | 2 | 3 | 0.5 | (120−40)/2 − 3.5 = 36.5 | Küçük kanat yeterli |
| Çıkış stage, TO-247, 25 W | 25 | 2 | 0.5 | 80/25 − 2.5 = 0.7 | Sıvı soğutma veya kısma şart |
| Çıkış stage, TO-247, 12 W | 12 | 2 | 0.5 | 80/12 − 2.5 = 4.2 | Büyük fanlı ısıtıcı |
| PSU regülatör, TO-220 | 5 | 3 | 0.5 | 80/5 − 3.5 = 12.5 | Orta boy fanlı ısıtıcı |
| Motor sürücü SO-16, 8.8 W | 8.8 | 73 (RθJA) | — | (RθJA tek başına 73) | Isıtıcı ZORUNLU (§2.3) |

**Kullanım:** Pd'yi ölç (veya hesapla) → tabloda en yakın satırı bul → RθSA sütunu ısıtıcı seçimini belirler (§6.6). Tablo dışı senaryolar için §1.1 formülü ile hesaplanır; ölçüm doğrulaması §15 protokolü ile yapılır.

## 18. Termal Monteaj Kontrol Listesi (Saha)

### 18.1 Montaj Öncesi

| # | Kontrol | Kabul | Red |
|---|---|---|---|
| 1 | Isıtıcı yüzeyi temiz + pürüzsüz | Yağ/oksit yok | Kirli yüzey → temizle |
| 2 | Termal macun kalınlığı | 0.1–0.3 mm (ince tabaka) | Kalın tabaka → RθCS artar |
| 3 | İzolasyon (mika/pad) | Yırtık yok, tam temas | Yırtık → kısa devre riski |
| 4 | Vida torku | §17.2 değerleri | Aşırı/yetersiz → §17.2 |

### 18.2 Montaj Sonrası

| # | Kontrol | Yöntem | Kabul |
|---|---|---|---|
| 1 | Elektriksel izolasyon | DMM (gövde↔ısıtıcı) | >1 MΩ @ 500V |
| 2 | Termal temas | Isıl direnç ölçümü (§15) | RθCS ≤ 1 °C/W |
| 3 | İlk güçleme | Düşük güç (idle) | Tcase < 45 °C (§15.2-1) |
| 4 | Burn-in | §15.2 4 aşama | §15.4 kabul |

**Kritik not:** Elektriksel izolasyon testi (gövde↔ısıtıcı >1 MΩ) **her montajda**
zorunludur; mika yırtığı → collector-ısıtıcı kısa devre → kasa topraklıysa
**tehlikeli voltaj** (§17.1).

### 18.3 Arıza Teşhisi (Sıcaklık Anomalişi)

| Belirti | Olası Neden | Doğrulama |
|---|---|---|
| Tcase 20 °C üzeri beklenenin üzerinde | RθCS artmış (macun kurumuş) | §15-2 aşamasında eğim kontrolü |
| Bir transistör diğerinden sıcak | Akım paylaşımı dengesiz | Emitter direnci ölçümü (§17.3) |
| Isıtıcı soğuk, transistör sıcak | İzolasyon/pad teması kopuk | §18.2-1 izolasyon testi |
| Fan sürekli %100, Tcase düşük | NTC bağlantısı kopuk/open-fault | §11-2 open-fault alarmı |

### 18.4 Periyodik Bakım Takvimi

| Periyode | İşlem | Kriter |
|---|---|---|
| 3 ayda bir | Fan toz temizliği + serbest dönüş kontrolü | Fan serbestçe el ile döner |
| 6 ayda bir | Termal macun yenileme değerlendirmesi | §18.3-1 anomalisi varsa yenile |
| Yılda bir | Tam burn-in tekrarı (§15.2, aşama 2-3) | §15.4 kabul kriterleri |
| Yılda bir | NTC/termokupl doğrulama (referans termometre ile) | Sapma ≤ ±2 °C |

**Bakım kaydı:** Her bakım tarihi, ölçülen Tcase değerleri ve yapılan işlemler cihaz bakım defterine yazılır; §15.3 kayıt tablosu formatı kullanılır.

Authority: .ai/CLAUDE.md §5 · SSOT · Updated: 2026-10-13 · Mode: docs-only (no production code changed)
