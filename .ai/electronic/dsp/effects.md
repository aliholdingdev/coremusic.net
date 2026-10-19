---
type: electronic
category: dsp-effects
title: "CoreMusic — Audio Effects"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Audio Effects

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Audio Effects, CoreMusic DSP Engine'deki mekânsal efektleri (reverb, delay,
echo) ve oda düzeltme mekanizmalarını tanımlar.

**Kapsam dışı:** zincir sırası (dynamics sonrası effects) ADR-062
(`[[dsp-pipeline]]`) · FIR matematiği `[[filters]]` · loudness hedefi
`[[loudness]]`.

**Dürüstlük notu:** Repo'da efekt C++ kodu YOK — kaynak: `.ai/electronic/**`;
yankı/zarf matematiği standart literatür (FDN, Schroeder allpass, geometric
echo series) ile çapraz doğrulanmıştır. §7.3'teki ürün-karşılaştırma
satırları **pazarlama-karşılaştırması**dır; bağımsız ölçüm kanıtı yok →
karşılaştırma iddiaları `⚠️ VERIFICATION REQUIRED` işaretlidir.

---

## 2. Reverb

Mekânsal yankı efekti.

### Reverb Modları

| Mod | Kullanım | Room Size |
|-----|----------|-----------|
| Geniş Konser | Büyük mekan | 5000m³ |
| Düğün Salonu | Orta mekan | 2000m³ |
| Oda | Küçük mekan | 100m³ |
| Stüdyo | Profesyonel | 50m³ |
| Custom | Özelleştirilebilir | Ayarlanabilir |

### Reverb Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Room Size | 0 - 100% | 30% |
| Decay Time | 0.1s - 10s | 2.0s |
| Pre-delay | 0ms - 100ms | 20ms |
| Damping | 0 - 100% | 50% |
| Mix (Wet/Dry) | 0% - 100% | 20% |

### 2.1 RT60 ↔ Feedback İlişkisi (Geometric Decay)

Delay/geri beslemeli reverb'de bir yankı hücresinin kazancı `g`, tekrar
aralığı `T_s` (sn) → her tekrar `g` katı:

```text
amplitüd tekrarı: a(t) = g^(t/T_s)
60 dB azalma (RT60) için: g^(RT60/T_s) = 10^(−60/20) = 10^−3
→ g = 10^(−3·T_s/RT60) = 10^(−0.003·T_s/RT60·1000 ms…)  (basitleştirilmiş)
örnek: T_s = 50 ms, RT60 = 2.0 s → g = 10^(−0.003·0.05/2.0·?) — pratik form:
g = 10^( −3·T_s / RT60 )   (T_s saniye cinsinden)
   = 10^( −3·0.05 / 2.0 ) = 10^(−0.075) ≈ 0.841
```

- **Damping**: yüksek frekanslarda g'yi düşürür (bant-geçirir geri besleme
  → doğal oda kaybı) — katsayı frekans bağımlı hale getirilir
- **Room Size**: T_s'yi (gecikme uzunluğu) artırır → aynı RT60'ta daha seyrek
  yankı, "daha büyük" algısı
- `g ≥ 1` → sonsuz salınım (kararsız) → **klips §parametre 95% benzeri sınır**
  (delay §3'te feedback ≤ 95%)

### 2.2 Pre-delay

- Direkt sinyalle yankı arasına `pre-delay` (örn. 20 ms) → frekans yanıtı
  kompresyonu önler, kaynağın netliği korunur (80 Hz dalga boyu ≈ 4.3 m;
  20 ms ≈ 6.8 m gecikme, mikrofon/kaynak ayrımı simülasyonu)

### 2.3 Topoloji Seçimi (PLANNED)

| Topoloji | Artı | Eksi | Durum |
|---|---|---|---|
| Schroeder (comb+allpass) | çok ucuz (4–6 gecikme) | metalik | referans |
| **FDN** (feedback delay network) | doğal, ölçeklenebilir | orta maliyet | PLANNED (öneri) |
| Konvolüsyon (IR) | en gerçekçi | FIR maliyeti → FFT §filters 6.3 | PLANNED (offline IR) |

> ⚠️ VERIFICATION REQUIRED: hangi topolojinin üretime alındığı repo kodu
> olmadan yazılmaz; §tablo yalnız tasarım seçenekleridir.

---

## 3. Delay

Gecikme efekti.

### Delay Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Delay Time | 1ms - 2000ms | 250ms |
| Feedback | 0% - 95% | 40% |
| Mix | 0% - 100% | 20% |
| Ping-Pong | On/Off | Off |
| Sync | BPM-sync | Off |

### 3.1 Yankı Serisi (Impulse Yanıtı)

```text
x(t) impulse → çıkış: δ(t), δ(t−D)·g, δ(t−2D)·g², …   (D = delay time, g = feedback)
genlik toplamı: Σ gⁿ  → |g| < 1'de yakınsar (geometrik dizi)
|g| = 0.95 → 60 dB için n ≈ ln(10^−3)/ln(0.95) ≈ 135 tekrar ≈ 135·D süre
```

- **Kararlılık sınırı:** g < 1.0 (tablo 95% = 0.95 üst sınırı → margin)
- **Dijital sınır:** g ≈ 1.0'a yaklaştıkça yankı süresi çok uzar; noise floor
  (float32 ≈ −140 dBFS) seriyi fiilen keser
- **Ping-pong:** tek tek gecikme yerine L↔R dönüşümlü iki satır (stereo
  genişlik) → §5 stereo genişlikle toplanır

### 3.2 BPM-Sync

```text
D = (60 / BPM) · (4 / note_div)  saniye   (örn. 1/4 not → 4/BPM·60/… sadeleştirilmiş:
BPM 120, 1/4 not = 0.5 s = 500 ms; 1/8 = 250 ms (varsayılan 250 ms → 120 BPM 1/8))
```

- Nota tablosu (üçlü nokta dahil) init'te sabitlenir; **time değişimi
  click'siz crossfade** (§6.3)

### 3.3 Eko vs Delay Ayrımı

- **Delay**: tek gecikme + geri besleme (kontünlü)
- **Echo** (§4): sınırlı sayı yankı (decay adımı) → az sayıda düz tekerrür

---

## 4. Echo

Tekrarlayan yankı efekti.

### Echo Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Echo Time | 50ms - 500ms | 200ms |
| Decay | 1 - 10 | 3 |
| Mix | 0% - 100% | 15% |

### 4.1 Decay Sayısı

- `Decay = 3` → 3 yankı: `g, g², g³` (g tipik 0.5); impulse yanıtı **sonlu**
  → delay'den (sonsuz seri) farkı budur
- Decay adımı amp: `g = 10^(−3/…)` yerine **lineer basamak** (parametre
  1–10 → g ≈ 0.7^(adım/…)) — kesin dönüşüm kod içi, dolgu yok

> ⚠️ VERIFICATION REQUIRED: Decay parametresinin (1–10) tam gain dönüşümü
> repo'da kod kanıtı olmadan yazılmaz; tablo yalnız aralıktır.

---

## 5. Stereo Width

Stereo genişletme/sıkıştırma.

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Width | 0% (mono) - 200% (ultra-wide) | 100% |
| Center | 0% - 100% | 50% |

### 5.1 Mid/Side Matematiği

```text
M = (L + R)/2      S = (L − R)/2
genişletme: L' = M + w·S   R' = M − w·S      (w = width/100%)
w = 0 → mono (S iptal)     w = 1 → orijinal   w > 1 → ultra-wide
```

- **w > 1 mono-uyumluluk riski:** L+R toplamında S **iptal olmaz**
  (L'+R' = 2M, aslında toplam korunur — M/S toplamı mono'da güvenli),
  tehlike **yan steril kanal aşırı genliği** → §6 headroom
- DSP'de genişlik yalnız **yan** bileşeni ölçekler; orta (vokal) sabit

---

## 6. Room Correction

Oda akustik düzeltmesi.

### Room Correction Parametreleri

| Parametre | Kullanım |
|-----------|----------|
| Measurement Mic | Oda ölçümü |
| Frequency Response | Frekans yanıtı |
| Phase Response | Faz yanıtı |
| Correction Filter | FIR tabanlı düzeltme |
| Target Curve | Hedef eğri |

### 6.1 Düzeltme Hattı (Inverse Filtering)

```text
Düzeltme eğrisi = hedef / ölçülen   (dB: H_dB = T_dB − M_dB)
FIR: h_fix = IDFT( 1/H(ω) )  (düzgünleştirme gerekli: |H| < eşitse gürültü amplifikasyonu)
```

- **Regularizasyon:** `1/max(|H|, ε)` — dip noktalarında (null) tersini
  almak gürültüyü uçurur → ε floor zorunlu
- **Kısıt:** düzeltme ≤ ±6 dB önerilir (pratik sınır; büyük düzeltmeler
  headroom ve faz bozar) — kesin sayısal sınır ölçüm raporuyla belirlenir
- FIR maliyeti yüksek → FFT konvolüsyon `[[filters]]` §6.3 · PLANNED
  (repo'da implementasyon YOK)

---

## 7. FX Pipeline

```
Input Signal
    ↓
Reverb (mekânsal)
    ↓
Delay (gecikme)
    ↓
Echo (yankı)
    ↓
Stereo Width (genişlik)
    ↓
Room Correction (oda düzeltme)
    ↓
Output Signal
```

### 7.1 ADR-062 Konumu

Yukarıdaki alt zincir, ADR-062 genel akışının **Efekt (aşama 6)** içinde
çalışır: `... → Dynamics (5) → **Efekt (bu zincir)** → Mix/Route (7) →
Master (8) → Çıkış (9)` (loudness ADR-062'de ayrı aşama DEĞİL —
`dsp-pipeline` §2 notu). Efekt **limiter'dan sonra** gelmez (§zincir kuralı
dynamics 6.1:
brickwall en son savunma; zincirde hâlâ master/çıkış gain kontrolü var →
aşırı tepe üretilirse `[[loudness]]`/limiter kontrolüne
geri dönülür — üretimde **effects output tepe monitorü** zorunlu).

### 7.2 Gecikme Bütçesi (ADR-017)

| Modül | Gecikme |
|---|---|
| Pre-delay (reverb) | 0–100 ms parametrik (bu **yanıt**tır, sinyal zorunlu gecikmesi değil — wet yolu) |
| Delay dry yolu | 0 (dry bypass) |
| Room Correction FIR | taps/2 (`[[filters]]` §4.1) |
| Tüm efekt dry yolu | **0** — efektler wet-yan yola konur, dry gecikmesiz |

**Kural:** dry yolu asla geciktirilmez (PDC — plugin delay compensation
eşdeğeri) → mix gecikmesi wet gecikmesiyle sınırlıdır; bütçe içinde
ölçülür (`[[measurements]]`).

### 7.3 Zero-Allocation

- Delay satırları max 2000 ms × fs × kanal = 96.000 örnek × 4 B × 2 (ping-pong)
  ≈ 768 KB → **init'te allocate** (static pool, alignas 64)
- Reverb hücre gecikmeleri toplam ≤ ~50 ms ≈ 2400 örnek → sabit havuz
- Wet/dry crossfade atomik; callback `noexcept`, `new`/`malloc` YASAK

---

## 8. AUX/USB Çıkışından FX

### 8.1 Çıkış Tipleri

| Çıkış | Protokol | FX Desteği | Kullanım |
|-------|----------|------------|----------|
| **Hoparlör (ASIO)** | ASIO/WASAPI | Tam FX | Ana ses çıkışı |
| **AUX Output** | Analog (3.5mm/6.3mm) | Reverb, Delay, Chorus | Harici amfi/speaker |
| **USB Output** | USB Audio Class 2.0 | Tam FX | USB DAC, harici cihaz |
| **Bluetooth** | BLE/A2DP | Sınırlı FX | Kablosuz hoparlör |
| **WiFi** | mDNS/DLNA | Tam FX | Multi-room |

> Sürüm notu: tarihsel metin §7 numarasını iki kez kullanıyordu (FX
> Pipeline + AUX/USB); bu revizyonda AUX/USB bloğu §8'e numaralandırıldı.
> İçerik korunmuştur.

### 8.2 FX Routing

```
DSP Chain Output
  → Mixer (Volume, Pan)
    → FX Bus (Reverb, Delay, Chorus)
      → AUX Output (analog)
      → USB Output (dijital)
      → Bluetooth (kablosuz)
      → WiFi (multi-room)
```

**Çakışma kuralı (ASIO exclusive):** ASIO Exclusive Lock — aynı anda tek
uygulama (`[[../drivers/index|drivers]]`); AUX/USB/BT **paralel** fiziksel
çıkıştır, ayrı sürücü/kart **almaz**. BT/WiFi gecikmesi DSP zincirinden
**bağımsız**dır (paketleme/kodek gecikmesi), efekt dry yoluna yansımaz.

### 8.3 Samsung Galaxy J7 Tarzı Geniş Konser Efekti

CoreMusic, Samsung Galaxy J7 2016'daki "Geniş Konser Salonu" efektine benzer
mekânsal ses deneyimi sunar.

| Efekt | Samsung J7 | CoreMusic |
|-------|------------|-----------|
| **Geniş Konser** | 3.0s decay | 3.0s decay, 5000m³ room |
| **Düğün Salonu** | 2.0s decay | 2.0s decay, 2000m³ room |
| **Oda** | 0.8s decay | 0.8s decay, 100m³ room |
| **Stüdyo** | 0.5s decay | 0.5s decay, 50m³ room |

> ⚠️ VERIFICATION REQUIRED: J7 sütunu **pazarlama referansı**dır —
> bağımsız ölçüm/analiz kanıtı yoktur; "benzer" iddiası algısal hedeftir,
> ölçülmüş eşdeğerlik **değildir**.

**Kullanım Senaryoları:**
- Müzik dinlerken konser salonu atmosferi hissetme
- Düğün salonu gibi mekanların akustiğini yeniden oluşturma
- Stüdyo kayıtlarında profesyonel reverb ekleme
- AUX/USB çıkışından efektli ses aktarımı

---

## 9. Hata Modları (FMEA)

| # | Hata modu | Etki | Önlem |
|---|---|---|---|
| 1 | Feedback ≥ 1.0 (delay/reverb) | sonsuz salınım → DAC kırpması | §3.1 üst sınır + genlik guard |
| 2 | Delay time canlı değişimi | click | crossfade (§3.2) |
| 3 | Room correction ε'siz tersleme | gürültü amplifikasyonu | §6.1 floor |
| 4 | Ultra-wide (w>2) | kanal headroom aşımı | §5.1 + output monitor |
| 5 | Wet boost sonrası tepe | TP aşımı | FX output tepe monitor (§7.1) |
| 6 | IR/konvolüsyon buffer dinamik | zero-alloc ihlali | §7.3 statik havuz |

---

## 10. Ölçüm Protokolü (Kabul)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| 1 | Reverb RT60 | impulse → −60 dB süresi | parametre Decay ±%10 |
| 2 | Delay aralığı | impulse → tepe konumları | D ± 1 örnek |
| 3 | Feedback kararlılık | g = 0.95, 60 dk soak | salınım yok, xrun 0 |
| 4 | Mono compatibility | w=100% → L+R | mono kayıp ≤ 3 dB (band içi) |
| 5 | Dry yol gecikmesi | impulse dry vs bypass | gecikme 0 örnek |
| 6 | FX output true-peak | 4× oversample | hedef aşılmaz (`[[loudness]]`) |

---

## 11. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-025-professional-eq-system]] | EQ sistemi |
| ADR-062 | Effects aşaması pipeline konumu (§7.1) |
| ADR-038 H001 | DAC PCM3168A — output tepe koruması |

---

## 12. Çapraz Referanslar

- `[[electronic/dsp/index]]` · `[[filters]]` · `[[dynamics]]` ·
  `[[loudness]]` · `[[dsp-pipeline]]` · `[[measurements]]`
- `[[../drivers/index|drivers]]` (ASIO exclusive) · `[[../hardware/index|hardware]]`

---

## 13. Derinleştirme: Efekt Hesap Örnekleri (2026-10-13)

> §2–§8'in formüllerini sayısal örneklerle sabitler. Repo'da efekt kodu YOK
> (PLANNED) — doğrulama §10 kapısındadır.

### 13.1 Reverb — Geri Besleme ve RT60 Dönüşümü (§2)

N tap'lı kaskad/gerbesleme şebekesinde tap gecikmesi `d` (ms), gerbesleme `fb`:

```text
fb = 10^( −3 · d_ms / RT60_ms )        # −60 dB'ye RT60'da ulaşım
```

**Sayısal:** d = 20 ms, hedef RT60 = 1.0 s → `fb = 10^(−3·20/1000) = 10^(−0.06)
≈ 0.871`. Yani **%87.1** — fb ≥ 1.0 kararsızlık (§3 kuralı, FMEA §9).

**Katsayı/bellek:** RT60 = 0.3–2.0 s aralığı (§2), tap başına tek çarpan
(4 B) → havenin tamamı init'te allocate (§7).

### 13.2 Echo — Geometrik Yankı Aralıkları (§4)

Yankı gecikmesi mesafe ile: `d_ms = 2 · 1000 · d_m / 343` (hava, ~20 °C):

| Mesafe | Gecikme | Perdesel algı eşiği |
|---|---|---|
| 1.7 m | ≈ 10 ms | <30 ms → flanger/chorus bölgesi |
| 8.6 m | ≈ 50 ms | 50–120 ms → **echo** bölgesi |
| 34 m | ≈ 200 ms | >125 ms → ayrı olay (yankı) |

- Flanger 0.1–10 ms · chorus 5–30 ms · **echo 50–350 ms** (§3/§4 sınırları
  bu geometriyle uyumlu).
- BPM sync (§3.4): `d = 60000/BPM · n` (n = 1/2 · 1/4 · 1/8 nota).

### 13.3 M/S Genişlik — Sayısal Örnek (§5)

L = 0.9, R = 0.5:

```text
M = (L + R)/2 = 0.70      S = (L − R)/2 = 0.20
genişlik k = 1.5 → S' = 0.30 → L' = M + S' = 1.00 · R' = M − S' = 0.40
```

- k = 1.5 ile tepe 0.90 → 1.00'e çıktı; **k > ~1.11'te** bu örnekte tepe
  sınırı aşabilir → §5 genişlik üst sınırı **TP/limiter ile** kısılır
  (ölçüm §10/2).
- k = 0 → L=R=M (mono) ✓ · k = 1 → orijinal ✓ (kontrol kapıları).

### 13.4 Room Correction — Regularizasyon (§6)

Konvolüsyon hedefi: `H_d(f)` hedef tepki, `H_m(f)` ölçülen:

```text
W(f) = H_d(f) / H_m(f) · [ |H_m|² / (|H_m|² + λ) ]    # Tikhonov-benzeri
```

- `|H_m| ≈ 0` çukurunda `W` → ∞ olurdu (aşırı boost = gürültü amplifikasyonu);
  ikinci çarpan (λ) bunu kısar: λ ↑ → daha muhafazakâr, daha az boost.
- λ seçimi ölçüme bağlıdır (**λ değeri bu metinde sabitlenmez** — kayıt §10/3
  ile ölçüm oturumunda belirlenir; sabitsiz iddia yazılmaz).
- FIR taps/2 grup gecikmesi §6.2'dedir ve dry yola **yansımaz** (yan yol).

### 13.5 Ölçüm Veri Şablonu (Kayıt Zorunlu)

| Alan | Değer (boş bırakılmaz) |
|---|---|
| Tarih / fs | YYYY-MM-DD / 48000 |
| RT60 ölçüm (sweep) | s — hedef 0.3–2.0 sapması |
| Echo/chorus süresi | ms (§3/§4 aralığı) |
| Genişlik tepe kontrolü | dBTP ≤ limit (§13.3 kuralı) |
| Room correction λ | **ölçüm oturumunda** — yoksa `⚠️ VERIFICATION REQUIRED` |
| xrun | 0 / sayı |

### 13.6 ADR Çapraz Referans Özeti

| Kural | Kaynak | Bu bölümdeki karşılık |
|---|---|---|
| Efekt = aşama 6 | ADR-062 | §7.1 |
| Zero-alloc (havuzlar init) | [[ADR-017-dsp-hardware-mode]] | §7.3, §13.1 |
| TP/limiter kapısı | [[ADR-025-professional-eq-system]] §2.2d/e | §13.3 |
| PCM3168A (DAC) | ADR-038 H001 | §10 donanım |

---

## 14. Efekt Bloğu Kabul Kontrol Listesi (Uygulama Öncesi)

Her efekt bloğu (reverb · delay · chorus · convolver) devreye alınmadan önce sırayla tamamlanır; atlanan adım varsa blok **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | Havuz/tamponu init'te allocate et (runtime tahsis yasak) | §7.3 · [[ADR-017-dsp-hardware-mode]] | tahsis logu (yalnız init) |
| 2 | Dry/wet yolu ayır; dry = 0 kontrolü | §7.1 · ADR-062 aşama 6 | bypass↔aktif farkı |
| 3 | RT60/echo süresini aralıkta sabitle (0.3–2.0 s) | §3 · §10/1 | sweep ölçümü |
| 4 | Genişlik/tepe kontrolünü TP ile kıs | §13.3 · [[ADR-025-professional-eq-system]] §2.2d | dBTP ölçümü |
| 5 | Room correction λ'yı ölçüm oturumunda belirle | §13.4 · §10/3 | λ kaydı (yoksa VR) |
| 6 | Grup gecikmesini hesapla ve bütçeye işle | §6.2 · [[electronic/dsp/index]] §6 | latency katkısı |
| 7 | Kararlılık soak (60 dk) + xrun = 0 | §10/5 | soak kaydı |
| 8 | mute/unmute click testi (ramp 5 ms) | [[electronic/dsp/index]] §8 | click ölçümü |

### 14.1 Sık Yapılan Efekt Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Reverb/delay havuzunun runtime'da büyümesi | Audio thread'inde tahsis → kural 1 ihlali | Havuzlar init'te (§7.3) |
| 2 | Feedback ≥ 1.0 bırakılması | Döngü kararsızlığı, take-off | Feedback < 1.0 sınırı (§4) |
| 3 | Wet yola DC/offset taşınması | DC → koruma rölesi (elektronik kural 10) | DC kontrolü çıkışta |
| 4 | Konvolüsyon taps'ının ölçüsüz artırılması | Latency bütçesi ihlali | FIR taps/2 = grup gecikmesi (§6.2) |
| 5 | λ sabitlenerek "ayarlanmış" sayılması | Ölçümsüz iddia | §10/3 kaydı zorunlu, sabit yazılmaz |
| 6 | Bypass'ın sırayı atladığı sanılması | ADR-062 sırası ihlali | Bypass aşamayı atlar, sırayı bozmaz ([[electronic/dsp/index]] §1) |

**Not:** Bu bölümdeki toleranslar (RT60 0.3–2.0 s · feedback < 1.0 · xrun 0 · ramp 5 ms) mevcut §10/§14 kapılarıyla **aynı** değerlerdir; yeni tolerans **tanımlanmaz**. Ölçümsüz satır `⚠️ VERIFICATION REQUIRED` işaretlenir.

### 14.2 Efekt Sırası ve Bypass Kuralları (ADR-062 — Değiştirilemez)

Efekt bloklarının zincir içi sırası: **reverb → chorus/delay → surround decode** (ADR-062 aşama 6, [[electronic/dsp/index]] §1). Kurallar:

- Sıra **bağlayıcıdır**; yeniden sıralama yeni ADR ister.
- **Bypass**, bloğu atlar — sırayı bozmaz; bypass'lı blok latency katkısı 0 sayılır, sıradaki blok aynı yerde kalır.
- Wet yolu dry = 0 durumunda bile kendi gecikmesini taşır; toplam latency = aktif blokların `getLatency()` toplamı.
- Blok eklenmesi (yeni efekt) → gecikme bütçesi yeniden ölçülür ([[electronic/dsp/index]] §9 DUR kapısı).

| Kapı | Koşul | Sonuç |
|---|---|---|
| Sıra kapısı | ADR-062 aşama 6 sırası ihlal | DUR — yeni ADR |
| Ölçüm kapısı | RT60/echo ölçümü yok | `⚠️ VERIFICATION REQUIRED` |
| Tahsis kapısı | Havuz init dışında allocate edildi | DUR — kural 1 ihlali |
| TP kapısı | dBTP ≤ −1 dBTP aşıldı | DUR — [[ADR-025-professional-eq-system]] §2.2e |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
