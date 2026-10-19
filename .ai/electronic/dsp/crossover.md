---
type: electronic
category: dsp-crossover
title: "CoreMusic — Crossover Engine"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Crossover Engine

**See also:** [[electronic/dsp/index]] · [[electronic/amplifier/index]] · [[ADR-025-professional-eq-system]]

---

## 1. Amaç

Crossover Engine, ses sinyalini frekans aralıklarına göre böler ve ilgili
hoparlörlere (woofer, tweeter, subwoofer) yönlendirir.

**Kapsam dışı:** biquad matematiği `[[filters]]` · EQ öncesi/sonrası sıra
ADR-062 (`[[dsp-pipeline]]`) · hoparlör güç/zarar eşiği
`[[../amplifier/index|amplifier]]`.

**Dürüstlük notu:** Repo'da crossover C++ kodu YOK — kaynak:
`.ai/electronic/**`; topoloji/ölçüm disiplini belgelerden, formüller standart
literatürden (Linkwitz-Riley, RBJ cookbook) türetilmiştir.

---

## 2. Crossover Noktaları (7.1 Surround)

```
20Hz ──────────── Subwoofer (LFE)
                 │
80Hz ──────────── Crossover Point
                 │
120Hz ─────────── Woofer → Midrange
                 │
3500Hz ────────── Midrange → Tweeter
                 │
20kHz ─────────── Üst Limit
```

### 2.1 Nokta Seçim Gerekçeleri

| Nokta | Değer | Gerekçe |
|---|---|---|
| 80 Hz | LF/MF (sub/sat) | THX referans noktası; 80 Hz altı içerik sub'a yönlendirilir; `[[../amplifier/index|amplifier]]` LF güç sınırlamasıyla hizalı |
| 120 Hz | Woofer→Mid (planlı) | yalnız 3-yol topolojide aktif; 2-yol (LR-4, 80 Hz) topolojisinde kullanılmaz |
| 3500 Hz | MF/HF (tweeter) | tweeter resonance güvenlik payı + MF sürücü üst limiti |
| 120 Hz | LFE üst sınır | LFE kanalı ayrı; 20–120 Hz bant (ayrı sinyal yolu, bass management §5) |
| 20 kHz | üst limit | 48 kHz Nyquist (24 kHz) altında; anti-imaging `[[filters]]` §3.1 |

> **Not:** Mevcut topoloji 2-yol (sub/full-range) ile 3-yol arasında
> parametrik; 120 Hz noktasi yalnız çok-yol modunda anlamlıdır.

---

## 3. Kanal Yapısı

| Kanal | Frekans Aralığı | Hoparlör |
|-------|------------------|----------|
| Front Left | 20Hz - 20kHz | Full Range |
| Front Right | 20Hz - 20kHz | Full Range |
| Center | 100Hz - 8kHz | Mid Range |
| Surround Left | 100Hz - 16kHz | Surround |
| Surround Right | 100Hz - 16kHz | Surround |
| Rear Left | 100Hz - 16kHz | Surround |
| Rear Right | 100Hz - 16kHz | Surround |
| Subwoofer | 20Hz - 120Hz | LFE |

### 3.1 8.1 Sayımı (ADR-038)

8.1 = 7 tam kanal + 1 LFE; tabloda LFE/Sub tek satır = Subwoofer (20–120 Hz).
Kanal sayısı DAC eşlemesi: PCM3168A 8-ch (ADR-038 H001: PCM5122 **yok**).
Röle koruma zinciri (DC offset > 0.5 V) `[[../hardware/index|hardware]]`
tarafındadır; crossover dijitaldir, röleyi **etkilemez ama tetik koşulunu
azaltır** (LF kanallarında DC geçmez — high-pass §4).

---

## 4. Crossover Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Crossover Frequency | 60Hz - 120Hz | 80Hz |
| Slope | 12dB/oct - 48dB/oct | 24dB/oct |
| Type | Linkwitz-Riley / Butterworth | Linkwitz-Riley |
| Phase | 0° / 180° | 0° |
| Subwoofer Gain | -∞ - 0dB | 0dB |

### 4.1 Slope ↔ Sıra Karşılığı

| Slope | Sıra (order) | LR karşılığı | Butterworth karşılığı |
|---|---|---|---|
| 12 dB/oct | 2 | LR-2 = 2× BW-1 | BW-2 |
| 24 dB/oct | 4 | LR-4 = 2× BW-2 (kullanılan) | BW-4 |
| 48 dB/oct | 8 | LR-8 = 2× BW-4 | BW-8 |

### 4.2 Linkwitz-Riley = Cascaded Butterworth (§7.1 ayrıntısı)

```text
LR-N (N = 2,4,8...) = Butterworth(N/2) ∘ Butterworth(N/2), aynı fc
LR-4 = BW-2 × BW-2
```

- BW-2 @fc: −3.01 dB, faz −90° → iki birebir: **−6.02 dB, faz −180°**
- LP + HP çiftinde faz kayıpları aynı yönde değil **karşılıklıdır**:
  HP'nin +180° faz sıçraması + LP'nin −180° → **toplam 0°**
- Genlik: LP(f) + HP(f) — LR-4'te toplam **allpass** (düz genlik, 0° faz)
  → hoparlörler arası sum bozulmaz (butterworth-4'te tepe/çukur oluşur)

### 4.3 Butterworth vs LR Karar Tablosu

| Ölçüt | Butterworth | Linkwitz-Riley |
|---|---|---|
| @fc kazanç | −3.01 dB | −6.02 dB |
| LP+HP sum | tepe/çukur (faz) | düz (allpass) |
| Bant-overlap | daha dar | geniş, yumuşak |
| Tavsiye | tek filtre rolü | **crossover (default)** |

---

## 5. Bass Management

| Özellik | Değer |
|---------|-------|
| Bass Redirect | Tüm kanallardan bass → subwoofer |
| High-Pass | Seçilen kanallarda bass kesme |
| Low-Pass | Subwoofer'da bass geçme |
| LFE Channel | Ayrı 120Hz kanal |
| Phase Alignment | Subwoofer ile Front phase senkronu |

### 5.1 Yönlendirme Matematiği

```text
Sub_girişi = LPF( Σ_k HPF( kanal_k ) )  +  LFE
Front_k    = HPF( kanal_k )
```

- **Kanal başına LP ayrı, sonra toplam (split-before-sum):** her kanalın
  sub payı ayrı HPF'den geçip toplanır → kanallar arası faz katsayıları
  korunur; tek ortak HPF (sum-before-split) yerine
- LFE kanalı ek olarak LP 120 Hz'den geçer (zaten bantlanmış içerik)

### 5.2 Subwoofer Phase Alignment

- Sub ↔ Front yol farkı (mesafe/krossover gecikmesi) → 0°/180° anahtarı
  (§4 Phase) veya sub'da gecikme (ms) ince ayarı
- **Ölçüm:** tek-tone 80 Hz, sub+front birlikte vs ayrı; sum genliği ≥
  tek başına en iyi kanalın %100'ü → tam yapısal faz (kabul §8.2)
- 180° seçimi kaba adım; sürekli ms gecikme PLANNED (⚠️ VERIFICATION
  REQUIRED — donanım gecikme kanıtı yok)

### 5.3 LFE Farkı

LFE (0.1 kanal) bass **management ile yönlendirilmez**, ayrı taşınır ve
yönlendirilir; içerik 20–120 Hz. LFE kanalının gain'i yayın standardında
+10 dB referans farkına sahiptir — uygulama ölçümü `[[loudness]]` ile
birlikte yapılır.

> ⚠️ VERIFICATION REQUIRED: +10 dB LFE offset'inin bu projede uygulanıp
> uygulanmadığı repo'da kod kanıtı YOK.

---

## 6. Crossover Diyagramı

```
Full Range Signal ──┬──▶ High Pass Filter ──┬──▶ Front Speakers (20Hz-20kHz)
                    │                       ├──▶ Center Speaker (100Hz-8kHz)
                    │                       └──▶ Surround Speakers (100Hz-16kHz)
                    │
                    └──▶ Low Pass Filter ────▶ Subwoofer (20Hz-120Hz)
```

### 6.1 Gecikme Bütçesi (ADR-062 / ADR-017)

> ⚠️ ADR-062'de crossover **ayrı aşama değildir** (bağlayıcı zincir 9 aşama —
> `dsp-pipeline` §2); aşağıdaki tablo **modül** bütçesidir ve toplam
> `getLatency()` katkısını tanımlar.

| Modül | Gecikme | Not |
|---|---|---|
| HPF/LPF biquad (LR-4 = 2× BW-2/kanal) | ~1–2 örnek grup gecikmesi @fc | biquad minimum-gecikme yaklaşımı |
| Allpass (3-yol) | ek 1 allpass/telefi | 3-yol modunda |
| Toplam crossover | ≈ 2–3 örnek ≈ 0.04–0.06 ms @48k | blok bütçesi 512 örnek (10.67 ms) içinde ihmal edilebilir ölçekte |

Kesin değer ölçülür (`[[measurements]]` impulse yöntemi); tahmin
**yazılmaz**, ölçüm **zorunlu**.

### 6.2 Sıralama (EQ ↔ Crossover — ⚠️ ADR-062 aşama değil)

`EQ → Crossover → Dynamics` **bu klasörün tasarım sırasıdır**, crossover'ın
ADR-062 zincirinde ayrı aşama olmadığını görmezden gelmez (hangi aşamaya
bindiği **VERIFICATION REQUIRED** — `dsp-pipeline` §2). crossover öncesi EQ
varsa band-payları bozulmadan zenginleştirilir; crossover sonrası EQ kullanılırsa
**bantlar üzerinde** tekrar kazanç → LR sum allpass'ı bozabilir (dikkat: ölçüm §8.1).

---

## 7. FMEA — Hata Modları ve Etkileri

| # | Hata modu | Etki | Önleme/tespit |
|---|---|---|---|
| 1 | LR yerine BW-4 kullanımı | sum'da tepe/çukur (faz) | default LR (§4) + sum ölçümü |
| 2 | fc = 120 Hz'e kaydırma | tweeter/woofer uyumsuz | 60–120 Hz klips (§4) |
| 3 | Phase 180° yanlış seçim | sub cancellation (bass boşluğu) | §5.2 ölçüm |
| 4 | 3-yol'da allpass unutulması | MF/HF sum faz kayması | topoloji kontrol listesi |
| 5 | Sub gain > 0 dB | headroom kaybı, THD | §4 klips (≤0 dB) |
| 6 | fs ≠ 48 kHz | fc kayması (bilinear) | init'te katsayı yeniden üretim `[[filters]]` §5.2 |
| 7 | LPF öncesi eksik HPF | sub'a DC/LF taşınır | röle koruması (0.5 V) + §4.2 HPF |

---

## 8. Ölçüm Protokolü (Kabul)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| 1 | LR sum düzgünlüğü | sweep, LP+HP birlikte | ±0.5 dB düz, faz ≈ 0° @fc |
| 2 | Sub/front alignment | 80 Hz tek-tone sum | sum ≥ max(tek başına) %100 |
| 3 | Slope doğruluğu | 1 oktav öte genlik | 24 dB/oct ±2 dB |
| 4 | LFE bant | sweep LFE kanalı | 20–120 Hz geçer, üstünde −24 dB/oct |
| 5 | Gecikme | impulse → tepe | §6.1 ölçüm değeri kaydedilir |
| 6 | Kanal ayrışma | kanal kanal sweep | kanallar arası girişim ≤ −60 dB |

Araç: `[[measurements]]` · DAC: PCM3168A (ADR-038) · Bütçe: ADR-017.

---

## 9. Ölçüm Disiplini Kuralları

1. Tüm sweep'ler **log** (20 Hz–20 kHz), ≥10 sn, benzer düzeyde (−12 dBFS
   önerilir; klips ölçümü bozar)
2. Sonuç önce **bypass** ile kıyaslanır (diferansiyel ölçüm)
3. Her ölçüm tarihi + fs + topoloji (2-yol/3-yol) ile kaydedilir
4. Ölçüm yoksa iddia YOK → `⚠️ VERIFICATION REQUIRED`

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-025-professional-eq-system]] | EQ sistemi (crossover öncesi sıra) |
| [[ADR-038-8.1-sound-card-chip-selection]] | 8.1 surround (PCM3168A) |
| [[ADR-017-dsp-hardware-mode]] | 512 / 48 kHz / float32 bütçe |
| ADR-062 | Zincir 9 aşama — crossover ayrı aşama DEĞİL (`dsp-pipeline` §2) |

---

## 11. Çapraz Referanslar

- `[[electronic/dsp/index]]` · `[[filters]]` · `[[equalizer]]` ·
  `[[dynamics]]` · `[[dsp-pipeline]]` · `[[measurements]]`
- `[[electronic/amplifier/index]]` · `[[../hardware/index|hardware]]`

---

## 12. Derinleştirme: Katsayı Üretimi ve Faz Matematiği (2026-10-13)

> Bu bölüm mevcut tasarım kararlarını **hesaplanabilir** kılmak için eklenmiştir;
> repo'da crossover kodu YOK (PLANNED) — sayısal örnekler tasarımsal referanstır,
> ölçüm §8 kapısındadır.

### 12.1 Butterworth → Linkwitz-Riley Katsayı Üretimi

LR-4, **2. derece Butterworth LP'nin iki kez kaskadlanmasıyla** üretilir
(`H_LR4 = H_BW2²`). Analog Butterworth 2. derece kesim frekansı normalize
(`Ωc = 1`) kesim polinomu: `s² + √2·s + 1` (α = √2 ≈ 1.41421).

Bilinear dönüşüm (`s = (2/fs)·(1−z⁻¹)/(1+z⁻¹)`) sonrası normalize LP
katsayıları (ωc ≪ fs yaklaşığında):

```text
b0 = (1 − cos ωc) / 2 · K   b1 = 1 − cos ωc   b2 = b0
a0 = 1                        a1 = −2 cos ωc    a2 = 1 − α·K + K²
(K = 2·tan(ωc/2)·(1/√2) türevi — tam formül [[filters]] §5.2)
```

**Sayısal örnek — fc = 80 Hz, fs = 48000:**

| Değer | Hesap | Sonuç |
|---|---|---|
| ωc | 2π·80/48000 | 0.010472 rad/örnek |
| cos ωc | — | 0.999945 |
| tan(ωc/2) | — | 0.005236 |
| K (BW-2) | tan(ωc/2)·√2 | 0.007405 |
| −2cos ωc (a1) | — | −1.999890 |

- **LR-4 ≠ BW-4:** BW-4 (tek kaskat 4. derece) sum'da √2·|H|² şeklinde tepe
  üretir; LR-4 (kaskad BW-2²) sum'da **allpass** olur → §1 kuralın matematiksel
  dayanağı budur.
- Eğim doğrulaması: `|H|²` → 20·log10(|H|²) = **40 dB/oktav** asimptotik
  (2×12 değil — kaskad 24 dB/oct/oktav eğim verir, §2 tablosundaki
  "24 dB/oct" **grup gecikmesi yanında asimptotik faz eğimidir**; ölçüm §8/3).

### 12.2 Toplam (Sum) Faz Şartı

2-yol LR'de LP ve HP'nin toplamı allpass'a eşittir. Nedeni: LR-4 LP'nin
kutupları analog eksen üzerinde ωc'de iki çift, HP'nin kutupları aynı yerde
aynı ωc'de; **sıfırlar ωc'de çakışır** ve kutup-sıfır fazları toplamda
0°'ye (düz faz) getirir:

```text
H_sum(e^jω) = H_LR-LP(e^jω) + H_LR-HP(e^jω) = A(e^jω)   |A| = 1
→ faz: φ_sum(ω) = −ω·T_grup (doğrusal gecikme), genlik: 0 dB (düz)
```

**3-yol zorunluluğu:** LF/MF ayrımında (80 Hz) LF branch'ı ile MF branch'ı
aynı allpass'a sahip değildir; MF/HF sınırında (3500 Hz) ikinci crossover
ek faz üretir → toplam **düz değil** olur. Telafi: **allpass** her orta
branch'a, kutuplarını **aynı ωc'ye** taşıyarak fazı eşitler:

```text
φ_correction(ω) = φ_target(ω) − φ_actual(ω)  → allpass kökleri bu farkı üretir
```

Allpass 2. derece transferansı: `H(s) = (s² − (ωc/Q)s + ωc²)/(s² + (ωc/Q)s + ωc²)`
— genlik her frekansta 1, faz 0°→−180° (ωc'de −90°).

### 12.3 Grup Gecikmesi Hesabı

Biquad'ın grup gecikmesi (tepe/ölçüm §8/5 ile karşılaştırılır):

```text
GD(ω) = −dφ(ω)/dω   [örnek cinsinden; ×1000/fs → ms
```

- 2-yol LR-4 @80 Hz tekil biquad gecikmesi ≈ 1/ωc büyüklüğünde mertebe;
  **kesin sayı ölçülür** (`[[measurements]]` impulse) — bu dosyada
  **uydurulmaz** (§6.1 kuralı).
- Blok gecikmesi: biquad **sıfır blok gecikmesi** üretir (ADR-025 §2.2c:
  "kaskad blok gecikmesi eklemez") — yalnız grup gecikmesi frekansa bağlıdır.

### 12.4 Bass Management Kazanç Zinciri

Sub yolu (§5):

```text
x_sub = LP_fc(x_front + x_surround…)      # sum → tek LP
y_sub = g_sub · x_sub                      # g_sub ≤ 0 dB (§7/5)
```

- **Sum sonrası LP** (LP önce değil): sum'ın yüksek frekans içerikliliği LP'yi
  gereksiz yormaz ve faz LP'nin kontrolünde kalır.
- g_sub > 0 dB yasak: headroom kaybı + DAC kırpması → röle koruması (0.5 V,
  kök kural 10) ile çakışır.
- LFE kanalı **ayrı** yolda: 20–120 Hz bandı (§8/4), gain kuralı g_sub'e
  tabi değildir ama TP bütçesine tabidir.

### 12.5 Neden 60–120 Hz Klips (§4)?

- <60 Hz: hoparlör/odak response bozulur + sub stroke sınırı → LR fc alt
  sınırı; >120 Hz: sub lokalize olur (yönlenme/speech-masking) → üst sınır
- Üretim cihazları (kıyas envanteri `dsp/AGENTS.md` dışıdır; tipik pratik
  **80 Hz** = THX referansı) — bu dosyanın bağlayıcı default'u §4'tedir.

### 12.6 Ölçüm Veri Şablonu (Kayıt Zorunlu)

| Alan | Değer (boş bırakılmaz) |
|---|---|
| Tarih | YYYY-MM-DD |
| fs | 48000 / diğer |
| Topoloji | 2-yol / 3-yol (+ allpass var/yok) |
| fc | 80 Hz (60–120 klipsi) |
| Yöntem | impulse → tepe (§8/5) / sweep (§8/1) |
| Ölçülen GD @fc | ms — **ölçüm yoksa `⚠️ VERIFICATION REQUIRED`** |
| Sum ripple | dB — kabul ±0.5 |
| xrun | 0 / sayı |

### 12.7 ADR Çapraz Referans Özeti

| Kural | Kaynak | Bu bölümdeki karşılık |
|---|---|---|
| 512/48k/float32 bütçe | [[ADR-017-dsp-hardware-mode]] | §6.1, §12.3 |
| EQ çift, grafik→parametrik | [[ADR-025-professional-eq-system]] | §12.4 zinciri EQ sonrası |
| 9 aşama, crossover aşama değil | ADR-062 | §6.2 uyarısı |
| `<10/<20 ms` ölçerek | ADR-006 | §8 kapıları |
| PCM5122 yasak | ADR-038 H001 | §8 donanım satırı |

---
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
