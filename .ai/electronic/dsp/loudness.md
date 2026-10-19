---
type: electronic
category: dsp-loudness
title: "CoreMusic — Loudness & ReplayGain"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Loudness & ReplayGain

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Loudness & ReplayGain, ses seviyesi yönetimini ve parçalar arası dengeyi
sağlayan mekanizmaları tanımlar. Ölçüm çerçevesi **ITU-R BS.1770-4** +
**EBU R128**; uygulama `[[dsp-pipeline]]`'da. ⚠️ ADR-062'nin 9 aşamasında
Loudness **ayrı aşama değildir** (bkz. `dsp-pipeline` §2 notu); hangi aşamaya
bindiği **VERIFICATION REQUIRED** — varsayılan: Çıkış (aşama 9) öncesi
master/çıkış gain zinciri içinde.

**Kapsam dışı:** limiter mekanizması `[[dynamics]]` · EQ `[[equalizer]]`
· ölçümler `[[measurements]]`.

**Dürüstlük notu:** Repo'da loudness C++ kodu YOK — kaynak:
`.ai/electronic/**`; K-weighting/gating matematiği ITU-R BS.1770-4 ve
EBU Tech 3341/3342 çerçevesiyle çapraz doğrulanmıştır. §2'deki platform
hedefleri (Spotify/YouTube/Apple) kamuya açık değerlerdir; **bu projede
kodlanmış olduğu iddia edilmez**.

---

## 2. Loudness Standards

| Standart | Hedef | Kullanım |
|----------|-------|----------|
| EBU R128 | -23 LUFS | Yayın |
| ITU-R BS.1770-4 | -24 LUFS | Genel |
| Spotify | -14 LUFS | Streaming |
| YouTube | -14 LUFS | Video |
| Apple Music | -16 LUFS | Streaming |
| CoreMusic Default | -23 LUFS | Varsayılan |

> **Çelişki notu:** ITU-R BS.1770-4 tek başına "hedef" tanımlamaz —
> hedefi yayın standardı verir (ABD ATSC A/85 → −24 LKFS; EBU R128 →
> −23 LUFS). Tablo "Genel −24" satırı bu projede kabul edilen **ATSC
> referansıdır**; iki değer birbirinin yerine geçmez, kullanım alanına
> göre seçilir. CoreMusic default **−23 LUFS (EBU R128)**.

---

## 3. Loudness Measurement

### LUFS (Loudness Units Full Scale)

| Metrik | Açıklama |
|--------|----------|
| Integrated | Parça boyunca ortalama |
| Short-term | 3 saniyelik ortalama |
| Momentary | 400ms ortalama |
| True Peak | Maksimum true peak |

### 3.1 K-Weighting (BS.1770-4)

Ölçüm zinciri: **pre-filter (yüksek shelf) → RLB high-pass → blok enerji →
gate → log toplam**.

```text
Stage 1 (pre-filter, high-shelf "kabartma"):
f0 = 1681.97 Hz, G = +3.9999 dB, Q = 0.7071
(48 kHz için bilinen katsayılar — pre-filter)
Stage 2 (RLB high-pass):
f0 = 38.13 Hz, Q = 0.5003
(iki biquad; 48 kHz'e özel sabitler üretimde double ile üretilir)
```

- K-weighting, insan kulak frekans-bağımlı hassasiyetini (ISO 226 eğrisi
  yaklaşık) örnekleme-örnek modeller
- Sonrası: 400 ms bloklar (Momentary) — her blokta **iki kanal enerji
  toplamı**: `z_i = (1/T)·Σ y²[ ]` (kanal ağırlıkları: L=R=1.0;
  C/LFE ağırlıkları standartta tanımlıdır — 5.1+ için LFE **hariç**)

### 3.2 Blok Zamanlamaları

| Metrik | Blok | Overlap | Kullanım |
|---|---|---|---|
| Momentary | 400 ms | 75% (100 ms adım) | anlık göstergesi |
| Short-term | 3 s | 100 ms adım | LRA girdisi |
| Integrated | 400 ms (momentary üzerinden) | — | gated ortalama |

### 3.3 Gating (EBU R128 / BS.1770-4)

```text
1) Momentary blok LUFS'ları: L_j = −0.691 + 10·log10( Σ z_j )
2) Absolute gate: L_j ≥ −70 LUFS  olan bloklar aday
3) Adayların ortalması: Γ_r = −0.691 + 10·log10( mean(z_aday) ) − 10
   (relative gate, 10 LU aşağı)
4) Final: Γ_r üzeri bloklarla Integrated:
   L = −0.691 + 10·log10( mean( z_j , L_j ≥ Γ_r ve ≥ −70 ) )
```

- **Neden gate:** sessizlikler Integrated'ı düşürür → parça sonu
  sessizlikleri ölçüme girmez; −70 LUFS absolute, −10 LU relative
- Eşikler §tablo (index §7 ile aynı): −70 abs + −10 rel relative

### 3.4 LU vs LKFS

- **LUFS** = LKFS (Loudness Units relative to Full Scale) — iki ad, aynı
  ölçüm; fark yalnız yazım geleneği (EBU: LUFS, ATSC: LKFS)
- **LU** (Loudness Unit) = iki ölçülmüş seviye arasındaki **fark**:
  `Δ = L1 − L2` (LU cinsinden; dB ile aynı ölçek, ses niteliği taşır)

---

## 4. ReplayGain

Her parçanın algılanan ses seviyesini normalize eder.

### ReplayGain Parametreleri

| Parametre | Değer |
|-----------|-------|
| Standard | ReplayGain 2.0 |
| Target | -23 LUFS (EBU R128) |
| Pre-amp | 0dB |
| Clip Prevention | Aktif |
| Track Gain | Parça bazlı |
| Album Gain | Albüm bazlı |

### 4.1 Track vs Album Gain

```text
Track gain: parça Integrated → hedef fark:  G_t = Target − L_paranin
Album gain: albüm boyunca tutarlılık — en yüksek track gain'e kilitli
            (dinleme sırası değişse de albüm içi dinamik korunur)
```

- **Clip prevention:** `G > 0` ise tepe tahmini → gain kırpılır; kalan
  fark limiter'a (`[[dynamics]]`) bırakılmaz, önceden klipslenir
- **Pre-amp:** kullanıcı ek düzeltmesi (0 dB default) → toplam gain =
  `min(G, Clip-prevention sınırı) + Pre-amp`

### 4.2 ReplayGain 2.0 Ölçümü

- RG 2.0; ölçüm kemiği bu projede **BS.1770-4** ile uyumludur
  (parça Integrated → LUFS tabanlı); eski RG1 (dBFS RMS) **kullanılmaz**
- Etiket/akış: dosya başına gain değeri (metadata) — kod tarafı PLANNED
  (repo'da uygulama YOK)

---

## 5. Loudness Normalization Akışı

```
Audio Input ──▶ Loudness Ölç ──▶ {Hedef ile Karşılaştır}
                                    │
                    Düşük ▼         │         ▼ Yüksek
                  Gain Artır       │       Gain Azalt
                       │           │           │
                       │    Uygunlu ▼           │
                       │      Geç              │
                       │       │               │
                       └───────┴───────┬───────┘
                                       ▼
                                Limiter Kontrol
                                       │
                                       ▼
                                Normal Çıkış
```

### 5.1 Uygulama Modları

| Mod | Mekanizma | Gecikme | Kullanım |
|---|---|---|---|
| **İki geçiş (offline)** | parça tam dinlenir, gain yazılır | 0 (güncelleme anında) | dosya/metadata |
| **Canlı (gated sliding)** | short-term zarf → yumuşak gain | zarf süresi | streaming/çalma |

- Canlı modda gain **anlık** atlanmaz (pumping); zarf `[[dynamics]]`
  release mantığıyla sınırlanır
- Zincirde (bkz. `dsp-pipeline` §2) Loudness **ayrı aşama değil**; ölçüm/azaltma
  işlevi — **gain artırma sonrası limiter kontrolü zorunlu** (§şema: Limiter
  Kontrol düğümü); aşamaya bindirme kararı VERIFICATION REQUIRED

### 5.2 Ölçüm–Uygulama Uyumu

- Ölçüm **K-weighted**; uygulanan gain **lineer** → sapma yok
- Zincir sonu true-peak kontrolü §6 ile çaprazlanır (gain → TP → gerekiyorsa
  azalt)

---

## 6. True Peak Clipping

| Özellik | Değer |
|---------|-------|
| True Peak Limit | -0.3 dBTP |
| Oversampling | 4x |
| Standard | ITU-R BS.1770-4 |
| Clipping | Engellenir |

### 6.1 Neden Sample Peak Yetmez

```text
Örneklere uyan sinüs: tepe = örnekte görünür
Örneklere uymayan sinüs: tepe iki örnek ARASINDA → inter-sample peak
Dijital zincir → DAC (reconstruction) → analogda bu tepe GERÇEK olur →
analog kırpmada/DAC headroom aşımında → TP ölçümü (4× oversample)
```

### 6.2 Ölçüm/Ayar Çelişkileri

- Bu dosya §tablosu: **−0.3 dBTP** (limit) — `[[dynamics]]` §3.2'de
  "maksimum 0 dBTP (donanım)" ve teslimat pratiğinde **−1 dBTP** (EBU
  R128 prz) bulunur. Üç farklı değer **kasten** farklı katmanları
  ifade eder:
  - `−1 dBTP` = yayın **teslimat** hedefi (gerekirse `[[loudness]]` hedefi
    olarak seçilir)
  - `−0.3 dBTP` = bu dosyanın **limit ayarı** (varsayılan)
  - `0 dBTP` = donanım/dijital **mutlak üst**
- Üretim değeri hangisi: `⚠️ VERIFICATION REQUIRED` (repo'da kod kanıtı yok)
- Ölçüm sapması kabulü: 4× oversample referans ile ≤ 0.1 dB
  (`[[measurements]]`)

---

## 7. Loudness Range (LRA)

| Aralık | Kullanım |
|--------|----------|
| <5 LU | Yayın (sıkıştırılmış) |
| 5-15 LU | Müzik (standart) |
| >15 LU | Klasik müzik (geniş dinamik) |

### 7.1 LRA Hesabı (EBU Tech 3342)

```text
Short-term (3 s) LUFS değerlerinden:
1) absolute gate −70 LUFS
2) relative gate: ortalama − 20 LU (LRA için 20 LU eşik, Integrated'ın
   10 LU'sundan farklı)
3) kalan short-term değerlerin 10. persentil ↔ 95. persentil farkı = LRA (LU)
```

- LRA, **dinamik dağılım** özeti; Integrated ile birlikte raporlanır
- Yayın standardında tipik LRA dar (§tablo <5 LU); master'da LRA
  kasıtlı korunur (klasik: >15 LU → limiter baskı uygulanmaz)

---

## 8. Ölçüm Protokolü (Kabul)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| 1 | K-weighting doğruluğu | sinus sweep, weight'li/ağırlıksız | sapma eğrisi ≤ 0.5 dB |
| 2 | Integrated doğruluğu | referans test sinyali (R128 testi) | ≤ 0.5 LU sapma |
| 3 | Gate davranışı | sonunda 2 s sessizlik | Integrated değişmez (sessizlik dahil değil) |
| 4 | TP ölçümü | 4× oversample referans | ≤ 0.1 dB sapma |
| 5 | Normalizasyon hedefi | hedefe getirme → yeniden ölçüm | hedef ±0.5 LU |
| 6 | Canlı zarf | seviye rampası | pumping yok (ΔGain ≤ 3 LU/s — parametrik) |
| 7 | LRA | persentil hesabı | EBU 3342 ile uyumlu (referans içerikle) |

---

## 9. Hata Modları (FMEA)

| # | Hata modu | Etki | Önlem |
|---|---|---|---|
| 1 | K-weighting uygulanmadan "LUFS" | yanlış seviye (≥2 dB tipik sapma) | §3.1 zincir zorunlu |
| 2 | Gate'siz Integrated | sessizlikler yutar → abartılı sonuç | §3.3 |
| 3 | Gain sonrası TP kontrolsüz | inter-sample kırpmа | §6 zincir sonu |
| 4 | Short-term ile Integrated karıştırma | yanlış hedef (−14 vs −23) | §3.2/§tablo ayrımı |
| 5 | LFE ağırlığı unutulması (5.1+) | abartılı loudness | §3.1 kanal ağırlıkları |
| 6 | Ölçüm bloğu ≠ 400 ms | standart dışı sonuç | §3.2 zamanlama |

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-006-performance-targets]] | Performans hedefleri |
| ADR-062 | Zincirde 9 aşama — Loudness ayrı aşama DEĞİL (`dsp-pipeline` §2) |

---

## 11. Çapraz Referanslar

- `[[electronic/dsp/index]]` · `[[dynamics]]` · `[[dsp-pipeline]]` ·
  `[[crossover]]` (LFE) · `[[measurements]]`
- Çerçeve kaynakları: ITU-R BS.1770-4 · EBU R128 (Tech 3341/3342/3344) —
  `[[electronic/dsp/index]]` §10 ölçüm disipliniyle birlikte uygulanır

---

## 12. Derinleştirme: Hesap Örnekleri ve Veri Şablonu (2026-10-13)

> Bölüm §3/§7'nin prosedürünü **çalıştırılabilir örneklerle** sabitler. Repo'da
> loudness kodu YOK (PLANNED) — sayısal örnekler standart formülünün
> tasarımsal uygulamasıdır; gerçek ölçüm §8 kapısındadır.

### 12.1 Blok Loudness Hesabı (ITU-R BS.1770-4, Adım Adım)

**Parametreler:** 400 ms blok · %75 overlap (adım 100 ms) · K-weighting
(§3) · kanal ağırlıkları §12.3.

```text
Adım 1 — K-weighting uygula:          y_k(t) = H_pre(s) · H_RLB(s) · x_k(t)
Adım 2 — blok enerjisi (kanal ağırlıklı):
        z_i = Σ_k G_k · (1/400ms) ∫_blok y_k²(t) dt     [ms/örnek cinsinden ort.]
Adım 3 — blok loudness:
        l_i = −0.691 + 10·log10(z_i)     [LUFS]
Adım 4 — kapılardan geçir (§12.2) → entegre:
        L_KG = −0.691 + 10·log10( ort_i∈{geçen} z_i )
```

**Sayısal örnek — tek kanal (L), 1 kHz sinüs, tepe = 0.5 (≈ −6.02 dBFS):**

| Adım | Hesap | Sonuç |
|---|---|---|
| DC/ortalama (sin²) | a²/2 = 0.25/2 | 0.125 |
| G_L | — | 1.00 |
| z | 1.00 × 0.125 | 0.125 |
| 10·log10(z) | 10·log10(0.125) | −9.03 dB |
| l | −0.691 − 9.03 | **−9.72 LUFS** |

> K-weighting 1 kHz'de ≈ 0 dB (pre-filter shelf'i 1.5 kHz'de açılır) → bu
> frekansta ölçüm ≈ ağırlıksız enerjiye eşittir; sapma uçlarda görünür (§3).

### 12.2 Kapıların Sayısal Hesabı

Bir ölçüm seti için (6 blok, LUFS): `−68, −64, −61, −62, −65, −72`:

| Adım | Kural | Sonuç |
|---|---|---|
| 1 Mutlak kapı | `l_i ≥ −70` | −72 elenir → kalan 5 blok |
| 2 Ortalama | aritmetik ortalama kalanların | (−68−64−61−62−65)/5 = **−64.0** |
| 3 Bağıl kapı eşiği | Γ_r = −64.0 − 10 | **−74.0 LUFS** |
| 4 Bağıl kapı | `l_i ≥ −74` (mutlaktan geçenler) | hepsi kalır |
| 5 Entegre | kalan z'lerin ortalaması → formül | bu örnekte −64.0'ın z-mukabili ≈ **−64.0 LUFS** |

- Kapılar **iki aşamalıdır**: mutlak (−70) → bağıl (Γ_r = µ − 10). Bağıl kapı
  **mutlak kapıdan geçen bloklar** üzerinde hesaplanır (µ onların ortalamasıdır).
- ≥2 blok gerekliliği pratik şartı §8'dedir; daha az blok `UNKNOWN` sayılır.

### 12.3 Kanal Ağırlıkları (BS.1770-4)

| Kanal | G_k | Not |
|---|---|---|
| L, R, C | **1.00** | ağırlıksız güç |
| Ls, Rs (surround) | **1.41** (≈ za = 1.59) | +1.5 dB işitsel katkı |
| Lfe | **0** | loudness hesabına **girmez** (ayrı bant §5.1) |
| Alt/ek kanal (8.1 LFE dışı) | ⚠️ | 8.1 genişletmesinin ağırlık eşlemesi bu metinde **yok** → VERIFICATION REQUIRED (BS.1770 5.1'i tanımlar) |

### 12.4 LRA Hesabı (§7) — Sayısal Örnek

Gated kısa-çaprazlı (short-term, 3 s) serisi: `−34, −32, −30, −27, −24, −21,
−19, −16, −14` (9 değer):

1. §7 kapıları uygulanır (mutlak −70 · bağıl µ−20 · kısa-çaprazlı μ ≥ −70)
2. Kalan değerler **persentil** ile okunur: **10. persentil** alt sınır ·
   **95. persentil** üst sınır
3. LRA = P95 − P10 (dB) — interpolasyon yöntemi ölçüm aracına bağlıdır
   (⚠️ persentil interpolasyon kuralı araç dokümanıyla doğrulanır)

### 12.5 True Peak Sayısal Notu (§6)

- 4× oversample: `y_os = IDFT(4× Z)` → tepe `max|y_os|` vs `max|y_n|`
- Örnek: tepe örneklerde 0.90 iken inter-sample tepe 0.97 bulunursa
  fark ≈ 0.63 dB → **−0.3 dBTP limiti örnek tepeye göre ayarlanamaz**,
  TP ölçümüne göre ayarlanır (bu §6.1'in sayısısal gösterimidir).
- Sapma tipik 0–1 dB aralığındadır **ancak bu dosyada tipik değer
  iddia edilmez** — her parçada ölçülür (§8/4).

### 12.6 Ölçüm Veri Şablonu (Kayıt Zorunlu)

| Alan | Değer (boş bırakılmaz) |
|---|---|
| Tarih / dosya | YYYY-MM-DD · parça kimliği |
| fs / blok / overlap | 48000 · 400 ms · %75 |
| Kapılar | mutlak −70 · bağıl µ−10 · LRA µ−20/P10–P95 |
| Entegre | LUFS — ölçüm yoksa `⚠️ VERIFICATION REQUIRED` |
| LRA | dB |
| TP | dBTP (4× OS) |
| Hedef sapma | hedef −23 / −16 / −14 (§2 seçimi) ± 0.5 dB |
| xrun | 0 / sayı |

### 12.7 ADR Çapraz Referans Özeti

| Kural | Kaynak | Bu bölümdeki karşılık |
|---|---|---|
| float32/48k/512 bütçe | [[ADR-017-dsp-hardware-mode]] | blok işleme bütçesi |
| Performans kapıları `<10/<20 ms` | ADR-006 | §8, §12.6 xrun |
| Zincir 9 aşama (loudness aşama değil) | ADR-062 | §1, `dsp-pipeline` §2 |
| EQ boost sonrası TP/limiter | [[ADR-025-professional-eq-system]] §2.2d | §5.2/§6 |

---

## 13. ReplayGain ↔ R128 Karşılaştırması ve Kapsam Notları (2026-10-13)

### 13.1 İki Sistem Karşılaştırması

| Ölçüt | ReplayGain 1/2 | EBU R128 / BS.1770 |
|---|---|---|
| Ölçüm filtresi | düz (kaba) / RG2'de iki-katmanlı | K-weighting (§3) |
| Kapı | basit eiksel | mutlak −70 + bağıl µ−10 (§12.2) |
| Hedef | referans ≈ −18 LUFS (aracıya göre) | **−23 LUFS** yayın (§2) |
| True peak | yok (RG2'de kısılabilir) | **zorunlu** (§6, 4× OS) |
| LRA | yok | var (§7) |
| Uygulama alanı | dosya/metadata (§4) | canlı + yayın (§5) |
| Parça/album modu | RG2 track/album (§4.1) | çok-parçalı için **BU METİNDE YOK** ⚠️ |

### 13.2 Eşik Çelişkileri Kaydı (Katman Notu)

- §2 hedef **−23 LUFS** (EBU yayın) · teslimat pratiği **−16 / −14** (streaming)
  · ReplayGain referansı **≈ −18** mertebe → **üç farklı katman**; bu dosyanın
  bağlayıcı varsayılanı §2'dedir, teslimat hedefi seçimi uygulama ayarıdır.
- TP: **−0.3 dBTP** (§6 limit) · teslimat **−1 dBTP** · mutlak **0 dBTP** —
  §6.2'deki üçlü ayrım geçerlidir; biri diğerini **ezmez**.

### 13.3 Track vs Album ReplayGain (§4.1)

- **Track:** her parça bağımsız ölçülür → parçalar arası dinamik **korunur**,
  sıralama değiştikçe gain değişebilir
- **Album:** albüm bütünü ölçülür, tek gain tüm parçalara → içi-dinamik
  korunur ama kısa parçalarda kırpmaya dikkat (§4.1 TP kontrolü)
- İkisi birlikte **çakıştırılmaz**: tek parça üzerinde iki tag varsa
  uygulama hangisini uygulayacağına karar vermelidir — bu metinde seçim
  **yok** → ⚠️ VERIFICATION REQUIRED (uygulama kararı, PLANNED).

### 13.4 Ölçüm Şablonuna Ek Alanlar (Bu Bölüm)

| Alan | Değer |
|---|---|
| Mod | track / album / canlı-sliding |
| Referans hedef | −23 / −16 / −14 (hangisi seçildi) |
| Gain uygulandı mı | evet/hayır + dB |
| TP sonrası | dBTP ≤ seçilen limit |

---

## 14. Loudness Kabul Kontrol Listesi (Uygulama Öncesi)

Loudness zinciri (ölçüm → gating → normalize → TP) devreye alınmadan önce adımlar sırayla tamamlanır; atlanan adım varsa **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | K-weighting filtresini uygula (high-shelf + RLB high-pass) | §3.1 · BS.1770-4 | filtre katsayıları |
| 2 | Blok süreleri ve örtüşmeyi doğrula (400 ms / %75) | §3.2 | blok zamanlaması |
| 3 | Gating'i uygula (−70 LUFS absolute + −10 LU relative) | §3.3 | gate logu |
| 4 | Hedefi seç ve kaydet (−23 LUFS varsayılan) | §2 · §5.1 | seçilen hedef |
| 5 | True-peak ölçümü yap (inter-sample TP) | §6 · §6.1 | dBTP değeri |
| 6 | LRA hesapla (EBU Tech 3342) | §7.1 | LU değeri |
| 7 | Sapma kapısını değerlendir (±1 LU) | §8 | PASS/FAIL |
| 8 | Ölçüm veri şablonunu doldur (§12.6 + §13.4) | §12.6 | dolu kayıt |

### 14.1 Sık Yapılan Loudness Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Sample peak ile true peak'in karıştırılması | Inter-sample klip | §6.1 — TP ölçümü zorunlu |
| 2 | Gating'siz tek-pas loudness hesabı | Sessizlik baskısı → yanlış LUFS | §3.3 iki kademeli gate |
| 3 | K-weighting olmadan ölçüm | Düşük frekans ağırlıklı hatalı okuma | §3.1 filtre zinciri |
| 4 | Track/Album gain'in birlikte uygulanması | Çift gain → kırpma riski | §13.3 — seçim uygulamada, bu metinde yok (`⚠️ VERIFICATION REQUIRED`) |
| 5 | Ölçümsüz "normalize edildi" iddiası | Tekrarlanamayan sonuç | §12.6 şablonu zorunlu |
| 6 | TP limiti seçiliyken sample peak'e güvenilmesi | Yayın kapısında klip | §6.2 · −1 dBTP (release gate) |

**Not:** Bu bölümdeki değerler (−23 LUFS · −1 dBTP · gate −70/−10 LU · 400 ms/%75 · sapma ±1 LU) mevcut §2/§3/§8 kapılarıyla **aynı** değerlerdir; yeni tolerans **tanımlanmaz**. Ölçümsüz satır `⚠️ VERIFICATION REQUIRED` işaretlenir. Hedef seçimi (−23 / −16 / −14) uygulama kararıdır — bu metinde sabitlenmez.

### 14.2 Ölçüm Ortamı ve Tekrarlanabilirlik Koşulları (§8 ile uyum)

Tekrarlanabilir bir loudness ölçümü için koşullar (§8 ölçüm protokolünün ön koşulları):

| # | Koşul | Gerekçe | Kaynak |
|---|-------|---------|--------|
| 1 | Örnekleme hızı sabit: 48 kHz / float32 (512 blok) | Zincir ADR-017 ile aynı yapıda | [[ADR-017-dsp-hardware-mode]] · [[electronic/dsp/index]] §6 |
| 2 | Kanal ağırlıkları BS.1770-4'e birebir (L/R/C = 1.0, Ls/Rs = 1.41) | Yanlış ağırlık → LUFS sapması | §12.3 |
| 3 | Kapı değerleri ölçüm öncesi yazılı (−70 / −10 LU) | Gating sürüklenmesi → tekrarlanamayan sonuç | §3.3 |
| 4 | TP ölçümü ayrı pasoda (inter-sample, ≥4× upsample) | Sample peak TP'yi göstermez | §6.1 · §12.5 |
| 5 | Ölçüm oturumu kaydı (tarih, hedef, mod, sonuç) | Kayıtsız ölçüm kanıt sayılmaz | §12.6 · §13.4 |
| 6 | Ortam/sinyal kaynağı belirtilmiş | Aynı kaynak ≠ aynı loudness | §8 (kayıt zorunlu) |

**Tekrar kuralı:** Aynı dosya + aynı hedef + aynı mod → sapma ±1 LU içinde tekrarlanmalıdır; aşarsa oturum geçersiz sayılır ve §12.6 şablonuyla yeniden ölçülür. Ölçüm ekipmanı/modeli kayıt tablosunda belirtilir; belirtilmeyen ölçüm `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

### 14.3 Loudness Kapıları — DUR Kararları

| # | Kapı | Koşul | Sonuç |
|---|------|-------|-------|
| 1 | Ölçüm kapısı | Ölçüm kaydı yok (§12.6 boş) | DUR — iddia yazılmaz |
| 2 | Sapma kapısı | Hedefe sapma > ±1 LU | DUR — §8 gereğince yeniden ölç |
| 3 | TP kapısı | dBTP > −1 dBTP (limit ihlali) | DUR — release gate |
| 4 | Gate kapısı | −70/−10 LU kapıları uygulanmamış | DUR — LUFS değeri geçersiz |
| 5 | Standart kapısı | Hedef standart (R128/BS.1770-4) belirtilmemiş | DUR — `⚠️ VERIFICATION REQUIRED` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
