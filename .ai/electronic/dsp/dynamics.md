---
type: electronic
category: dsp-dynamics
title: "CoreMusic — Dynamics Processing"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Dynamics Processing

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Dynamics Processing, ses sinyalinin dinamik aralığını yöneten Compressor,
Limiter, Gate ve Expander modüllerini tanımlar.

**Kapsam dışı:** EQ/crossover `[[equalizer]]` / `[[crossover]]` · loudness
ölçüm çerçevesi `[[loudness]]` (BS.1770) · zincir sırası ADR-062
(`[[dsp-pipeline]]`).

**Dürüstlük notu:** Repo'da dynamics C++ kodu YOK — kaynak:
`.ai/electronic/**`; zarf/toran matematiği standart literatür (feed-forward
compressor, RC zarf izleyici) ile çapraz doğrulanmıştır.

---

## 2. Compressor

Sinyalin dinamik aralığını sıkıştırır.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Threshold | -60dB - 0dB | -20dB |
| Ratio | 1:1 - 20:1 | 4:1 |
| Attack | 0.1ms - 100ms | 10ms |
| Release | 10ms - 1000ms | 100ms |
| Knee | 0dB - 30dB | 6dB |
| Makeup Gain | 0dB - +24dB | 0dB |

### 2.1 Statik Transfer Eğrisi (dB düzlemi)

```text
x = giriş seviyesi (dBFS)   y = çıkış seviyesi (dBFS)
T = threshold   R = ratio

x ≤ T :              y = x                        (bölge 1)
x > T (hard knee) :  y = T + (x − T)/R            (bölge 2)
```

Örnek (T = −20 dB, R = 4:1, hard):

| x (dBFS) | y (dBFS) | gain (dB) |
|---|---|---|
| −40 | −40 | 0 |
| −20 | −20 | 0 (eşik) |
| −8 | −17 | −9 |
| 0 | −15 | −15 |
| +6 | −13.5 | −19.5 |

### 2.2 Soft Knee (knee = 6 dB)

Knee genişliği `W` (dB) içinde gain geçiş yapar (knee/2 üstü+altı):

```text
W = knee; bölge −W/2 ≤ x−T ≤ +W/2:
y = x + (1/R − 1)·(x − T + W/2)² / (2·W)
```

- Çıkışı `C¹` sürekli yapar → eşiğinde "köşe" yok, sezişsel daha yumuşak
- Knee = 0 → §2.1 hard knee'e iner (limit)

### 2.3 Zarf (Envelope) — Attack/Release

Gecikme zarfı genlik-domain'de (gecikmeli gain):

```text
g_dB(n) = hedef gain (§2.1/2.2, mevcut |x[n]| üzerinden)
her örnek:
  eğer g_dB(n) < g(n) (daha çok sıkıştırma) → g += (g_dB − g)·(1 − e^(−1/(τ_a·fs)))
  eğer g_dB(n) > g(n) (bırakma)            → g += (g_dB − g)·(1 − e^(−1/(τ_r·fs)))
τ_a = attack τ   τ_r = release τ
```

**RC τ ↔ süre kavramı:** verilen süre `T_ms` için τ = T_ms / ln(1000/…);
pratikte zarfın ~%99'u için `4.6·τ` süre → attack 10 ms ≈ τ 2.2 ms.
(Üretimde τ sabitleri parametre dönüşümünde hesaplanır.)

**Yarım-düzlem kural:** attack yalnız **daha çok** kazanç azaltırken
koşar; release yalnız geri verirken → iki ayrı katsayı, tek zarf
(yaygın feed-forward tasarım).

### 2.4 Makeup Gain Hesabı

- Basit: en yüksek beklenen tepe için `makeup = −max_gain_reduction` (örn.
  −15 dB GR → +15 dB makeup, §2.1 tablosu)
- Kademeli: yavaş automatik makeup (loudness-mantığı) `[[loudness]]` hedefi
  (−23 LUFS) ile zincirlenir
- **Kural:** makeup > 0 ise zincir sonunda headroom korunur → limiter
  (§3) son savunma

### 2.5 Kullanım Senaryoları

| Senaryo | Ratio | Attack | Release |
|---------|-------|--------|---------|
| Soft | 2:1 | 20ms | 200ms |
| Medium | 4:1 | 10ms | 100ms |
| Hard | 8:1 | 5ms | 50ms |
| Brick Wall | ∞:1 | 0.1ms | 10ms |

### 2.6 Red-Gain Zarf Örneği (Doğrulama)

T = −20, R = 4:1, x adım = −10 dBFS → hedef GR = −(10 − 0)/4 = −2.5 dB;
attack τ_a ile GR −2.5'e **üstten** yaklaşır (sıkıştırma yönü);
sinyal bitince release ile 0'a **alttan** yaklaşır. Ölçüm: step-response
(`[[measurements]]`) → GR zarfı 10%/90% süreleri tabloya girer.

---

## 3. Limiter

Sinyalin maksimum seviyesini sınırlar.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Ceiling | -10dB - 0dB | -0.3dB |
| Release | Auto - 1000ms | Auto |
| Attack | 0.01ms - 10ms | 0.1ms |

### 3.1 Lookahead Brickwall

```text
lookahead = L örnek (ör. 1–5 ms @48k)
x[n] okunur → x[n−L] uygulanır
her örnek: GR = min(1, ceiling / max(|x[n−L+1 … n]|))  (sliding-window peak)
→ kaçırma yok: pik gelmeden L örnek önce GR inebilir
```

- **Maliyet:** L örnek ek gecikme (ADR-017 bütçesi içinden): L = 48 örnek
  = 1 ms @48k → blok 512'nin ~%1'i
- **Kaçınılan risk:** attack-only (lookaheadsız) limiter'da ilk 1–2 örnek
  `ceiling` üstü kaçar (dijital) ve DAC'ta **inter-sample** kırpmaya yol açar

### 3.2 True Peak Limiting

| Özellik | Değer |
|---------|-------|
| Oversampling | 4x |
| True Peak Detection | ITU-R BS.1770-4 |
| Maximum Level | 0 dBTP |

- Sample-peak ceiling ≠ true-peak: 4× oversample ile **inter-sample** tepe
  ölçülür ve sınırlanır (`[[loudness]]` §true-peak ile aynı ölçüm mantığı)
- Yaygın pratik teslimat eşiği **−1 dBTP** (EBU R128 / yayın); bu dosyanın
  §tablosu "maksimum 0 dBTP" **donanım kırpması**dır; teslimat hedefi
  `[[loudness]]` hedefleriyle (−1 dBTP) çelişirse **ölçüm raporu** esastır
  → `⚠️ VERIFICATION REQUIRED` (hangi eşiğin üretimde kullanıldığı kod kanıtı
  olmadan yazılmaz)

### 3.3 Limiter vs Compressor

| Ölçüt | Compressor | Limiter |
|---|---|---|
| Oran | 2–20:1 | pratikte ∞:1 (ceiling) |
| Hedef | dinamik yoğunlaştırma | tepe koruma (brickwall) |
| Gecikme | opsiyonel lookahead | **zorunlu** lookahead |
| Yer | §6 zincir ortası | zincir sonu |

---

## 4. Noise Gate

Düşük seviyeli gürültüyü temizler.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Threshold | -80dB - -20dB | -60dB |
| Attack | 0.1ms - 10ms | 1ms |
| Hold | 0ms - 500ms | 50ms |
| Release | 10ms - 1000ms | 100ms |

### 4.1 Durum Makinesi

```text
CLOSED: |x| < T → gain 0 (kapanık, floor)
  |x| ≥ T → OPEN, attack ramp
OPEN:   |x| < T → HOLD sayacı başlat (parametre Hold)
  Hold bitti ve hâlâ < T → CLOSE, release ramp
```

- **Hold** chattering'ı önler (kısa sessizliklerde aç-kapa titremesi)
- Ramp'ler click önler (attack/release §2.3 mantığı, ama gain hedefi
  sadece {0, 1} bandı)
- Kapalıyken **tam sessizlik yerine floor** (örn. −90 dB) tercih edilebilir
  (geometrik click yok, gürültü zaten eşik altında)

---

## 5. Expander

Sinyalin dinamik aralığını genişletir.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Threshold | -60dB - 0dB | -30dB |
| Ratio | 1:1 - 1:10 | 1:2 |
| Attack | 0.1ms - 100ms | 10ms |
| Release | 10ms - 1000ms | 100ms |

### 5.1 Transfer Eğrisi (Downward Expansion)

```text
x ≤ T:  y = x
x > T:  y = T + (x −T)·R    ,  R = ratio (örn. 1:2 → 0.5 → 3 dB düşüş/3 dB artış)
```

- **Downward expander** = soft gate; ratio 1:2 → eşiğin üstünde her +3 dB
  giriş için +1.5 dB çıkış (genişleme)
- Compressor'ın aynası: §2.1'deki `1/R` yerine `R`
- Durum çizelgesi: `R > 1` sıkıştırma · `R = 1` pasif · `R < 1` genişletme

---

## 6. Dynamics Pipeline

```
Input Signal
    ↓
Noise Gate (gürültü temizliği)
    ↓
Compressor (dinamik sıkıştırma)
    ↓
Limiter (maksimum sınır)
    ↓
Output Signal
```

### 6.1 Neden Bu Sıra (ADR-062 ile)

1. **Gate önce:** gürültü, compressor'ı gereksiz GR'ya iter; önce temizle
2. **Compressor ortada:** yoğunlaştırma
3. **Limiter son:** makeup/headroom'un son koruması — asla limiter
   **öncesinde** başka gain değişikliği yok (brickwall bozulur)

ADR-062 genel zinciri: `Giriş → Ön-hazırlık → EQ Grafik → EQ Parametrik →
**Dynamics (aşama 5)** → Efekt (6) → Mix/Route (7) → Master (8) → Çıkış (9)`
— §6 alt zinciri bu aşamanın içyapısıdır (crossover/loudness ADR-062'de
**ayrı aşama değildir**, bkz. `dsp-pipeline` §2 notu).

### 6.2 Gecikme Bütçesi

| Modül | Gecikme |
|---|---|
| Gate | ramp süresi (tesirsiz, sinyal gecikmesiz) |
| Compressor (lookaheadsız) | ~0 (zarf gecikmesi kazançtadır, sinyal yolunda değil) |
| Limiter lookahead | L = 1–5 ms (§3.1) |
| Toplam dynamics | ≈ 1–5 ms — 512/48k bütçesinde (10.67 ms) ölçülmeli |

### 6.3 Zero-Allocation Uygulaması

- Zarf durumları (gain, hold sayacı), limiter ring buffer'ı (peak penceresi)
  **init'te allocate**; callback'te `new` YASAK
- Zarf: tek float state/modül; limiter: L-örnek buffer (alignas 64)
- GR göstergesi (UI) → atomik publish, ses thread'de string/hesap yok
- Callback `noexcept` (AGENTS §zero-alloc)

---

## 7. Hata Modları (FMEA)

| # | Hata modu | Etki | Önlem |
|---|---|---|---|
| 1 | Limiter lookahead'siz | tepe kaçışı (dijital+TP) | §3.1 zorunlu |
| 2 | Release çok hızlı | pompa/distortion (pumping) | §2.5 senaryo tablosu + ölçüm |
| 3 | Makeup sonrası limiter bypass | DAC kırpması, THD | zincir kuralı §6.1 |
| 4 | Gate attack 0 | click | §4 ramp zorunlu |
| 5 | Zarf τ hatası (ms↔örnek) | beklenenden yavaş/sert GR | §2.3 dönüşüm formülü |
| 6 | GR ölçümü olmadan iddia | yanlış "brickwall" | `[[measurements]]` step + sweep |

---

## 8. Ölçüm Protokolü (Kabul)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| 1 | Statik eğri | statik ton sweep (level staircase) | §2.1/§2.2 eğrisi ±0.3 dB |
| 2 | Attack | −10 dBFS adım | GR 10→90% süre parametre τ ile uyumlu |
| 3 | Release | adım sonu | GR 90→10% süre uyumlu |
| 4 | Brickwall | +6 dBFS sinüs | sample-peak ≤ ceiling **ve** TP ≤ hedef |
| 5 | TP doğruluğu | 4× oversample referans ölçüm | sapma ≤ 0.1 dB |
| 6 | Pumping/gating duyusal | pink noise + level modülasyon | artifacts yok (dinleme + GR grafiği) |
| 7 | Gate chatter | −65 dBFS kesikli ton | aç-kapa ≤ 1/s (hold ile) |

---

## 9. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware (512 / 48 kHz / float32) |
| ADR-062 | Zincir sırası — dynamics, effects öncesi |
| ADR-038 H001 | DAC PCM3168A — output headroom doğrulaması |

---

## 10. Çapraz Referanslar

- `[[electronic/dsp/index]]` · `[[dsp-pipeline]]` · `[[equalizer]]` ·
  `[[crossover]]` · `[[loudness]]` · `[[effects]]` · `[[measurements]]`
- `[[../hardware/index|hardware]]` (röle/DC koruma 0.5 V — analog son katman)

---

## 11. Derinleştirme: Transfer Eğrisi ve Zarf Hesapları (2026-10-13)

> §2–§6'nın formüllerini sayısal örneklerle sabitler. Repo'da dynamics kodu
> YOK (PLANNED) — doğrulama §8 kapısındadır.

### 11.1 Statik Transfer Eğrisi Sayısal Tablosu

**Eşik T = −18 dBFS · Oran r = 4:1 · soft knee w = 6 dB · makeup = açik
(§2.4 kuralı):**

| Giriş (dBFS) | Kural | Kazanç GR | Çıkış (dBFS) |
|---|---|---|---|
| −40 | T altında | 0 dB | −40 |
| −21 | knee içinde ((x−T+w/2)²/(2w) kuralı) | ≈ −0.375 | ≈ −21.4 |
| −18 | T = eşik | 0 dB | −18 |
| −12 | üstünde: T + (x−T)/r | −1.5 | −13.5 |
| −6 | üstünde | −3.0 | −9.0 |
| 0 | üstünde | −4.5 | −4.5 |

**Soft knee (w = 6 dB) orta nokta kontrolü (§2.2):** x = T − w/2 = −21 dBFS'de
knee başlar; x = −18'de GR = −0.375 dB (formül: `GR = (1/r − 1)·(x−T+w/2)²/(2w) =
(0.25−1)·(9/2)/... = −0.375`) — **continuity**: knee bitişi (x = T + w/2 = −15'te)
sert kural GR = −0.75 ile birleşir (±0.01 dB içeride kalan formül, tam sayı
ölçüm §8/1 ile doğrulanır).

### 11.2 RC Zarf — τ ↔ ms Dönüşümü (§2.3)

İlk derece zarf için %10→%90 (veya tersi) süreleri:

| Yön | Süre | τ cinsinden |
|---|---|---|
| Attack (10→90%) | t_a | τ_a · ln(9) ≈ **2.197 · τ_a** |
| Release (90→10%) | t_r | τ_r · ln(9) ≈ **2.197 · τ_r** |

**Sayısal:** attack = 10 ms isteniyorsa `τ_a = 10/2.197 ≈ 4.55 ms`;
katsayı per-örnek: `α = exp(−1/(τ_a·fs)) = exp(−1/(4.55·48000)) ≈
exp(−4.57e-6) ≈ 0.9999954`.

**Hata modu 5 (§7/5):** ms↔örnek karışıklığı — fs=48000'te τ'nın örnek cinsinden
τ·fs ile (ör. 4.55 ms → 218 örnek) yazılmazsa zarf 48× yavaş/sert olur.

### 11.3 Makeup Gain Hesabı (§2.4)

Ortalama seviye için tipik (kaba) tahmin:

```text
makeup_dB ≈ x_typical_dBFS · (1 − 1/r)   (x = tipik sinyal, T üstünde)
```

**Örnek:** tipik −12 dBFS, r = 4 → makeup ≈ −12·0.75 = **+9 dB**.
⚠️ Bu **tahmin formülüdür** — gerçek makeup, ölçümle (§8/1) veya dinamik
makeup (GR ile) uygulanır; zincir sonu **TP/limiter kontrolü zorunlu**
(§6.1 kuralı: makeup sonrası limiter bypass FMEA §7/3).

### 11.4 Lookahead Limiter — Ring Buffer Şeması (§3.1)

```text
Gecikme L örnek (1–5 ms), buffer: x_ring[N], yazma/okuma imleçleri atomik
Her blokta:
  1) x_ring'e yaz (L-örnek geri tampon)
  2) pencere max: m = max(x_ring[i])   → sample-peak hedefi
  3) TP kontrolü: 4× OS tahmini ≥ m ise hedefi ona göre düşür  (§3.2)
  4) g = min(1, ceiling/m); attack anlık, release §2.5 zarfı
  5) g'yi blok içine uygula (one-pole/linear ramp §7.4)
```

- Pencere max **L-örnek offset** ile okunur → tepe tam geçmeden kısılmaz
  (**aşırı kısılmadan kaçınmak** lookahead'in amacıdır).
- Bellek: 5 ms × 48000 × 4 B ≈ **960 B/kanal** (init'te, alignas 64).

### 11.5 Gate Durum Makinesi Sayısal Örnek (§4)

Parametreler: T = −60 dBFS · attack 5 ms · hold 100 ms · release 200 ms:

| Zaman | Sinyal | Durum | Kazanç |
|---|---|---|---|
| 0 ms | −50 dBFS (T üstünde) | AÇIK | 1.0 (ramp 5 ms bitti varsayımı) |
| 10 ms | −70 dBFS | HOLD'a geç | 1.0 |
| 110 ms | −70 (hâlâ altında) | HOLD bitti → KAPANIŞ (release 200 ms) | 1.0 → 0.0 eğrisi |
| 150 ms | −50 (tekrar üstünde) | attack ile AÇILIŞ (5 ms) | 0.2 → 1.0 |
| chatter | −65 dBFS kesikli | hold sayacı sıfırlanmazsa 1/s altında | §8/7 kabul |

### 11.6 Ölçüm Veri Şablonu (Kayıt Zorunlu)

| Alan | Değer (boş bırakılmaz) |
|---|---|
| Tarih / fs | YYYY-MM-DD / 48000 |
| T / r / knee / attack / release | sayılar (§8/1–2 kabulüne göre) |
| Statik eğri sapması | dB ≤ ±0.3 |
| Brickwall | sample-peak ≤ ceiling **ve** TP ≤ hedef |
| TP sapma (4× OS) | dB ≤ 0.1 |
| xrun | 0 / sayı |

### 11.7 ADR Çapraz Referans Özeti

| Kural | Kaynak | Bu bölümdeki karşılık |
|---|---|---|
| 9 aşama: dynamics = 5, efekt öncesi | ADR-062 | §6.1 |
| Zero-alloc / float32 / 512 | [[ADR-017-dsp-hardware-mode]] | §6.3, §11.4 bellek |
| EQ boost → limiter kapısı | [[ADR-025-professional-eq-system]] §2.2d/e | §11.3 |
| `<10/<20 ms` ölçerek | ADR-006 | §6.2 bütçe |
| PCM3168A (DAC TP doğrulaması) | ADR-038 H001 | §9 |

---

## 12. Dynamics Kabul Kontrol Listesi (Uygulama Öncesi)

Dynamics zinciri (compressor → limiter, ADR-062 aşama 5) devreye alınmadan önce adımlar sırayla tamamlanır; atlanan adım varsa **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | Pipeline sırasını doğrula (compressor → limiter; gate/expander§5-§6) | §6.1 · ADR-062 | zincir şeması |
| 2 | Parametreleri aralıkta sabitle (ratio, attack, release, knee) | §2 · §8/1–2 | parametre listesi |
| 3 | Statik transfer eğrisini doğrula | §2.1 · §11.1 | eğri sapması dB |
| 4 | Soft-knee uygulamasını doğrula (knee = 6 dB) | §2.2 | knee kaydı |
| 5 | Zarf sabitlerini RC'den hesapla (attack/release) | §2.3 · §11.2 | τ değerleri |
| 6 | Makeup gain'i hesapla ve uygula | §2.4 · §11.3 | gain dB |
| 7 | Lookahead limiter ring-buffer şemasını kur (init'te tahsis) | §3.1 · §11.4 · §6.3 | buffer kaydı |
| 8 | True-peak ölçümü yap (4× OS) | §3.2 · §11.6 | dBTP |
| 9 | Latency bütçesini hesapla (lookahead + blok) | §6.2 · ADR-006 | latency ms |
| 10 | xrun soak (60 dk) | §8 | xrun = 0 |

### 12.1 Sık Yapılan Dynamics Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Ring buffer/lookahead tahsisinin runtime'da yapılması | Audio thread'inde tahsis → kural 1 ihlali | §6.3 · §11.4 — init'te |
| 2 | Brickwall'un sample peak ile doğrulanması | Inter-sample klip | §3.2 — 4× OS TP ölçümü |
| 3 | Makeup gain'in unutulması | Sistem sessizleşir | §2.4 zorunlu |
| 4 | Attack/release'in τ hesabı olmadan girilmesi | Öngörülemeyen zarf yanıtı | §11.2 — RC dönüşümü |
| 5 | Lookahead gecikmesinin bütçeye işlenmemesi | ADR-006 kapısı ihlali | §6.2 — ölçülerek |
| 6 | Ölçümsüz "limiter uygun" kararı | Tekrarlanamayan iddia | §11.6 şablonu zorunlu |
| 7 | Dynamics'ın efekt öncesi sırasının değiştirilmesi | ADR-062 ihlali | §6.1 — sıra bağlayıcı |

### 12.2 Dynamics DUR Kapıları

| Kapı | Koşul | Sonuç |
|---|---|---|
| Sıra kapısı | dynamics → efekt sırası bozuldu (aşama 5 → 6) | DUR — yeni ADR |
| Ölçüm kapısı | statik eğri/TP ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| Tahsis kapısı | init dışında buffer üretimi | DUR — kural 1 ihlali |
| Bütçe kapısı | latency <10/<20 ms aşıldı (ölçümerek) | DUR — ADR-006 |
| TP kapısı | TP > hedef (−1 dBTP release gate) | DUR — [[electronic/dsp/index]] §8 |

**Not:** Bu bölümdeki değerler (knee 6 dB · eğri sapması ≤ ±0.3 dB · TP sapma ≤ 0.1 dB · xrun 0) mevcut §8/§11 kapılarıyla **aynı** değerlerdir; yeni tolerans **tanımlanmaz**.

### 12.3 Dynamics Ölçüm Ortamı ve Tekrarlanabilirlik (§8 tamamlayıcı)

Tekrarlanabilir bir dynamics ölçümü için ön koşullar:

| # | Koşul | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | fs = 48 kHz / 512 blok / float32 sabit | Zincir ADR-017 ile aynı yapıda | [[ADR-017-dsp-hardware-mode]] |
| 2 | Test sinyali ve seviyesi kayıtta belirtilir | Aynı sinyal ≠ aynı red-gain | §8 · §11.6 |
| 3 | Statik eğri ölçümü bypass↔aktif sırayla | Baseline olmadan sapma ölçülemez | §8/1 |
| 4 | TP ölçümü 4× oversampling ile | Inter-sample klip gizlenir | §3.2 · §11.6 |
| 5 | Latency ölçümü ayrı oturumda (lookahead + blok) | ADR-006 ölçümü | §6.2 · ADR-006 |
| 6 | Ölçüm donanımı PCM3168A üzerinden | ADR-038 H001 | §9 |

**Tekrar kuralı:** Aynı sinyal + aynı parametre → eğri sapması ±0.3 dB ve TP sapması 0.1 dB içinde tekrarlanır; aşarsa oturum geçersizdir ve §11.6 şablonuyla yeniden ölçülür. Kayıtsız ölçüm kanıt sayılmaz; ölçümü olmayan satır `⚠️ VERIFICATION REQUIRED` işaretlenir.

### 12.4 Parametre Aralığı Hızlı Referans (§2–§5 — Değiştirilemez)

| Blok | Parametre | Aralık (mevcut §) |
|---|---|---|
| Compressor | ratio | 2:1 – 4:1 (§2) |
| Compressor | attack | 1 – 30 ms (§2) |
| Compressor | release | 50 – 500 ms (§2) |
| Compressor | knee | soft, 6 dB (§2.2) |
| Limiter | lookahead | 1 – 5 ms brickwall (§3.1) |
| Limiter | TP hedef | −1 dBTP (§3.2 · release gate) |
| Gate/expander | durum makinesi | §4.1 · §5.1 (sayısal örnek §11.5) |

Bu tablo **yeni değer tanımlamaz** — mevcut §2–§5 aralıklarını tek yerde toplar; değişiklik yalnız ilgili bölümde ve ADR üzerinden yapılır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
