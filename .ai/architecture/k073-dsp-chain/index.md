---
title: "K073 DSP Chain — Klasör Dizini (index)"
type: architecture-index
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K073 — DSP Chain Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K073 |
| **Ad** | DSP Chain (15 Aşamalı Pipeline + Analiz Dalları) |
| **Amaç** | 15 aşamalı DSP pipeline'ını, aşama arası ring buffer'ı ve paralel analiz dallarını (FFT spectrum / frequency response / waterfall) tek klasörde toplamak |
| **Bağımlılık** | Yukarı: `[[../k072-neva-engine-core/index]]` (motor yürütücü) · Aşağı: `[[../k074-eq-parametric/index]]`, `[[../k082-dynamics-compressor/index]]`, `[[../k083-effects-reverb/index]]` · Yanda: `[[../k076-mixer-routing/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `performance-engineer` (CPU/latency), `qa-engineer` (sıra doğruluğu) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` — `dsp-chain.md` 398, `analysis-spectrum.md` 423, `index.md` 131, `README.md` 664, `CLAUDE.md` 69 satır |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Kaynak |
|---|---|---|---|---|
| 1 | `[[dsp-chain]]` | DSP Chain | 15 aşama, stage arayüzü, biquad, ring buffer, pipeline yönetimi | `dsp-chain.md` (398) |
| 2 | `[[analysis-spectrum]]` | Analiz & Spectrum | FFT algoritması, spectrum analyzer, frequency response, waterfall | `analysis-spectrum.md` (423) |
| 3 | `[[index]]` | Bu dizin | Klasör özeti, envanter, komşu bağlantılar, kanıt | `index.md` (131) + `README.md` |

**Okuma sırası:** `index` → `dsp-chain` → `analysis-spectrum` → aşama detayları için `[[../k074-eq-parametric/index]]` vb.

## 2. Komşu Klasör Bağlantıları

| # | Klasör | İlişki | Wiki-link |
|---|---|---|---|
| 1 | k072-neva-engine-core | Zinciri yürüten motor | `[[../k072-neva-engine-core/index]]` |
| 2 | k074-eq-parametric | 5. ve 11. aşama | `[[../k074-eq-parametric/index]]` |
| 3 | k075-sample-rate-conversion | 4. aşama | `[[../k075-sample-rate-conversion/index]]` |
| 4 | k076-mixer-routing | 10. aşama | `[[../k076-mixer-routing/index]]` |
| 5 | k077-stream-buffer | Girdi kaynağı | `[[../k077-stream-buffer/index]]` |
| 6 | k078-playback-gapless | Kesme/crossfade zamanı | `[[../k078-playback-gapless/index]]` |
| 7 | k079-bit-depth-conversion | 3. ve 13. aşama | `[[../k079-bit-depth-conversion/index]]` |
| 8 | k080-channel-processing | 2. ve 9. aşama | `[[../k080-channel-processing/index]]` |
| 9 | k081-format-decoder | 3. ve 15. aşama | `[[../k081-format-decoder/index]]` |
| 10 | k082-dynamics-compressor | 6. ve 12. aşama | `[[../k082-dynamics-compressor/index]]` |
| 11 | k083-effects-reverb | 7. ve 8. aşama | `[[../k083-effects-reverb/index]]` |
| 12 | k073 (bu klasör) | Zincir + analiz | `[[index]]` |

## 3. Bağımlılık Matrisi

| # | Hedef | Tür | Aşama(lar) |
|---|---|---|---|
| 1 | `[[../k074-eq-parametric/index]]` | aşağı | 5, 11 |
| 2 | `[[../k082-dynamics-compressor/index]]` | aşağı | 6, 12 |
| 3 | `[[../k083-effects-reverb/index]]` | aşağı | 7, 8 |
| 4 | `[[../k075-sample-rate-conversion/index]]` | aşağı | 4 |
| 5 | `[[../k080-channel-processing/index]]` | aşağı | 2, 9 |
| 6 | `[[../k076-mixer-routing/index]]` | aşağı | 10 |
| 7 | `[[../k079-bit-depth-conversion/index]]` | aşağı | 3, 13 |
| 8 | `[[../k081-format-decoder/index]]` | aşağı | 3, 15 |
| 9 | `[[../k072-neva-engine-core/index]]` | yukarı | yürütücü |
| 10 | `[[../k077-stream-buffer/index]]` | besleyici | girdi |
| 11 | `[[../k078-playback-gapless/index]]` | yandan | geçiş |
| 12 | Analiz tap → FFT | paralel | `[[analysis-spectrum]]` |

## 4. Sinyal Akışı Özeti

```
girdi(k077/k081) --> [2 k080] --> [3 k079/k081] --> [4 k075] --> [5 k074] --> [6 k082]
        |
   [7 k083 reverb] --> [8 k083 chorus/delay] --> [9 k080 surround] --> [10 k076 mixer]
        |
   [11 k074 master EQ] --> [12 k082 limiter] --> [13 k079 dither] --> [14 gain] --> [15 k081 output]
        |
   paralel tap --> FFT analyzer (k073 analysis-spectrum)
```

## 5. Kaynak Envanteri

| # | Dosya | Satır | İçerik | Kullanım |
|---|---|---:|---|---|
| 1 | `_backup/.../k3-ses-motoru/dsp-chain.md` | 398 | Pipeline, stage, biquad, ring buffer | `[[dsp-chain]]` §4 |
| 2 | `_backup/.../k3-ses-motoru/analysis-spectrum.md` | 423 | FFT, spectrum, freq response, waterfall | `[[analysis-spectrum]]` §4 |
| 3 | `_backup/.../k3-ses-motoru/index.md` | 131 | 15 aşama şeması + performans hedefleri | §11 verbatim |
| 4 | `_backup/.../k3-ses-motoru/README.md` | 664 | §2.2 zincir kuralı, Alt Katman K3.2 | §11 verbatim |
| 5 | `_backup/.../k3-ses-motoru/CLAUDE.md` | 69 | Guardrail + DSP sırası + frekans bantları | §11 verbatim |
| 6 | `_backup/.../k3-ses-motoru/` (kalan 13 dosya) | — | Diğer K072-K083 klasörlerinin kaynağı | İlgili dizinler |

## 6. Kenar Durumları

| # | Senaryo | Davranış |
|---|---|---|
| 1 | Aşama bypass | Passthrough (politika ⚠️ VERIFICATION REQUIRED) |
| 2 | Ring buffer dolu | Drop / üretici bekleyemez |
| 3 | Ring buffer boş | Mute/repeat |
| 4 | Analiz dalı RT yolunu engellemez | Paralel yürütme |
| 5 | Efekt kapalıyken bit-perfect | Bayt eşitliği korunmalı |
| 6 | 8.1 girdi | 9. aşama aktif |
| 7 | 48 kHz → 384 kHz | 4. aşama (SRC) zorunlu |
| 8 | 16-bit → 24-bit | 3. aşama format convert |
| 9 | Limiter eşiği aşım | Gain reduction |
| 10 | Master EQ kapatılırsa | 5. aşama tek EQ olarak kalır |

## 7. Hata Modları

| # | Belirti | Kök Neden | Klasör |
|---|---|---|---|
| 1 | Yanlış sıra | connect sırası hatalı | `[[dsp-chain]]` |
| 2 | Frekans sarması | SRC yok/yanlış | `[[../k075-sample-rate-conversion/index]]` |
| 3 | Aşırı şekillendirme | Çift EQ | `[[../k074-eq-parametric/index]]` |
| 4 | Clip | Limiter yok | `[[../k082-dynamics-compressor/index]]` |
| 5 | Ölçme gürültüsü | Dither yok | `[[../k079-bit-depth-conversion/index]]` |
| 6 | Kanal kayması | Matris hatası | `[[../k080-channel-processing/index]]` |
| 7 | Metalik reverb | Comb/allpass gecikme | `[[../k083-effects-reverb/index]]` |
| 8 | Gecikmiş ekran spektrumu | FFT kuyruğu | `[[analysis-spectrum]]` |
| 9 | CPU aşımı | Zincir maliyeti | `[[dsp-chain]]` |
| 10 | Bozuk teslim | Kuyruk/underrun | `[[../k077-stream-buffer/index]]` |

## 8. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | K3.2 yaprak | 11 | `README.md` §Alt Katman Şeması |
| 7 | Zincir kuralı | EQ → Compressor → Reverb → Limiter | `README.md` §2.2 L68 |
| 8 | Frekans bantları | 20-80 / 80-250 / 250-4k / 4k-10k / 10k-20k Hz | `CLAUDE.md` §4 |
| 9 | FFT blok/pencere | Kaynakta tanımlı (bkz. `[[analysis-spectrum]]`) | `analysis-spectrum.md` |
| 10 | Biquad katsayı hassasiyeti | double | `k3 index.md` |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 9. Test ve Doğrulama Stratejisi

| # | Test | Kabul |
|---|---|---|
| 1 | Sıra doğrulama | 15 aşama sırayla |
| 2 | Bit-perfect (efekt kapalı) | Bayt eşit |
| 3 | Bypass eşitliği | Passthrough |
| 4 | Impulse cevabı / aşama kerneli | Beklenen eğri |
| 5 | FFT doğruluğu (sine girdi) | Tepe doğru bin'de |
| 6 | Frequency response ölçümü | 20 Hz-20 kHz ± tolerans |
| 7 | Waterfall kararlılığı | Zaman ekseni tutarlı |
| 8 | Ring buffer taşma/boşluk | Tanımlı davranış |
| 9 | CPU bütçesi | ≤ %10 ⚠️ ölçüm |
| 10 | Latency | < 0.1 ms ⚠️ ölçüm |
| 11 | 128 kanal / 384 kHz | Kabul |
| 12 | Uzun süreli test | Bellek sabit |
| 13 | Denormal girdi | CPU sabit |
| 14 | Analiz tap noktası | Doğru ara nokta |

## 10. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Bypass/hata politikası kaynakta belirsiz | Orta | ⚠️ VERIFICATION REQUIRED |
| 3 | Analiz dalının RT önceliği kanıtsız | Orta | ⚠️ VERIFICATION REQUIRED |
| 4 | 15 aşama ↔ klasör eşlemesi yorum | Orta | `[[dsp-chain]]` §3.2 |
| 5 | ADR-062 dondurulmuş aralık dışı | Orta | `.ai/log.md` üst karar |

## 11. Kaynak Aktarımı (Verbatim)

### Kaynak: `index.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/index.md` (125 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### K3 Ses Motoru Katmanı

##### Genel Bakış

K3 Ses Motoru Katmanı, COREMUSIC'in temel ses işleme bileşenidir. Neva Engine C++20 tabanlı, gerçek zamanlı güvenli (real-time safe) ve kilit-free (lock-free) bir mimari ile tasarlanmıştır. 15 aşamalı DSP zinciri, efektler, analiz ve format desteği sağlar.

##### Mimari Konum

```
K0 (Donanım) → K1 (OS) → K2 (Sürücü) → K3 (Ses Motoru) → K4 (Uygulama)
```

K3, K2'den ham ses verisini alır, dijital sinyal işleme (DSP) uygular ve işlenmiş sinyali K2'ye geri iletir.

##### Kapsam ve Kategoriler

###### Temel Motor
- **Neva Engine Core**: C++20, real-time safe, lock-free
- **DSP Chain**: 15 aşamalı DSP pipeline
- **Mixer/Routing**: Ses karıştırma ve yönlendirme

###### Efektler
- **EQ Parametric**: 31 bantlı parametrik EQ
- **Dynamics**: Compressor, limiter, expander
- **Reverb**: Freeverb algoritması
- **Chorus/Delay**: Chorus, delay, flanger, phaser

###### Analiz
- **Spectrum Analyzer**: FFT tabanlı spectrum analizi
- **Frequency Response**: Frekans tepkisi ölçümü

###### Format Desteği
- **Format Decoder**: FLAC, MP3, AAC, WAV, DSD
- **Stream Buffer**: Jitter buffer, adaptif buffering
- **Playback**: Gapless, crossfade

###### İşleme
- **Channel Processing**: Kanal eşleme, downmix
- **Sample Rate Conversion**: SRC algoritmaları
- **Bit Depth Conversion**: Dithering, noise shaping

##### Temel İlkeller

###### 1. Gerçek Zamanlı Güvenlik
- Bellek ayırma yasak (real-time context'te)
- Kilitlenme (blocking) yasak
- Sistem çağrısı yasak
- Bellek serbest bırakma yasak

###### 2. Lock-Free Tasarım
- Tüm kritik yollar lock-free
- SPSC/MPMC queue'lar
- Atomic operations
- Wait-free algoritmalar

###### 3. Statelessness
- İşleme stateless olmalı
- Her frame bağımsız işlenebilmeli
- Persistent state minimal tutulmalı

###### 4. Numerik Hassasiyet
- Float32/Float64 çift hassasiyet
- Biquad katsayıları double precision
- Denormal sayılar flush-to-zero

##### DSP Pipeline

```
┌─────────────────────────────────────────────────────┐
│              15-Aşamalı DSP Pipeline                │
│                                                     │
│  1. Input Gain      → 2. Channel Mapping            │
│  3. Format Convert  → 4. Sample Rate Convert        │
│  5. EQ Parametric   → 6. Dynamics Process           │
│  7. Reverb          → 8. Chorus/Delay               │
│  9. Surround Decode → 10. Mixer/Routing             │
│  11. Master EQ      → 12. Limiter                   │
│  13. Dithering      → 14. Output Gain               │
│  15. Format Output                                       │
└─────────────────────────────────────────────────────┘
```

##### Performans Metrikleri

| Metrik | Hedef |
|--------|-------|
| İşleme Latency | < 0.1ms |
| CPU Kullanımı | < 10% (tam kapasite) |
| Maksimum Kanal | 128 |
| Maksimum Örnekleme Hızı | 384kHz |
| Bellek Kullanımı | < 100MB |

##### Bağımlılıklar

| Katman | İlişki |
|--------|--------|
| K2 | Ham ses verisini alır/iletir |
| K4 | İşlenmiş sesi sunar |
| K1 | Sistem hizmetlerini kullanır |

##### Dosya Haritası

| Dosya | İçerik |
|-------|--------|
| neva-engine-core.md | Ana motor, C++20, real-time safe |
| dsp-chain.md | 15 aşamalı DSP pipeline |
| eq-parametric.md | 31 bantlı parametrik EQ |
| dynamics-compressor.md | Compressor, limiter, expander |
| effects-reverb.md | Freeverb algoritması |
| effects-chorus-delay.md | Chorus, delay, flanger, phaser |
| analysis-spectrum.md | FFT analizi, spectrum analyzer |
| surround-decoder.md | 5.1/7.1/8.1 surround |
| format-decoder.md | FLAC, MP3, AAC, WAV, DSD |
| stream-buffer.md | Jitter buffer, adaptif buffering |
| playback-gapless.md | Gapless, crossfade |
| mixer-routing.md | Mixer, routing matrix |
| channel-processing.md | Kanal mapping, downmix |
| sample-rate-conversion.md | SRC algoritmaları |
| bit-depth-conversion.md | Dithering, noise shaping |

##### Durum: Implementasyon

K3 Katmanı, K2 sürücüleri tamamlandıktan sonra implemente edilecektir. Önce Neva Engine Core ve DSP Chain, ardından efektler ve analiz araçları yazılacaktır.


### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (16 satır · 27-42. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

##### 2. Neva Engine Mimarisi

###### 2.1 Ana Bileşenler

```
Neva Engine
├── DSP Chain (Kanal başına)
│   ├── 31-Band Parametric EQ
│   ├── Compressor
│   ├── Reverb (4 mod)
│   ├── Limiter
│   └── Analyzer
├── Mixer
│   ├── 8.1 Channel Routing
│   ├── Volume Control
│   ├── Pan Control

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (26 satır · 43-68. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

│   └── Mute/Solo
├── Crossover
│   ├── Linkwitz-Riley 4th Order
│   └── Bass Management
└── Master Output
    ├── Dithering
    ├── Sample Rate Conversion
    └── Bit-Perfect Output
```

###### 2.2 DSP Chain Akışı

```
Input (32-bit float)
    → Gain Staging
    → 31-Band Parametric EQ
    → Compressor
    → Reverb (mix control)
    → Limiter
    → Output Gain
    → Dithering (optional)
Output (32-bit float)
```

---


### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (16 satır · 69-84. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

##### 3. 31-Band Parametric EQ

###### 3.1 EQ Band Frekansları

| Band | Frekans | Q Factor | Aralık |
|------|---------|----------|--------|
| 1 | 20 Hz | 0.7 | Sub-bass |
| 2 | 25 Hz | 0.7 | Sub-bass |
| 3 | 31.5 Hz | 0.7 | Sub-bass |
| 4 | 40 Hz | 0.7 | Bass |
| 5 | 50 Hz | 0.7 | Bass |
| 6 | 63 Hz | 0.7 | Bass |
| 7 | 80 Hz | 0.7 | Bass |
| 8 | 100 Hz | 0.7 | Lower mid |
| 9 | 125 Hz | 0.7 | Lower mid |
| 10 | 160 Hz | 0.7 | Lower mid |

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (21 satır · 371-391. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| **K3.2.c** | **Arayüz & Metrikler** | dsp-chain.md · L336–L383 |
| K3.2.c.1 | API / Arayüz | dsp-chain.md · L336 |
| K3.2.c.2 | Performans Metrikleri | dsp-chain.md · L373 |
| K3.2.c.3 | Bağımlılıklar | dsp-chain.md · L383 |

###### K3.3 — Parametrik EQ

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.3.a** | **Filtre Çekirdeği** | eq-parametric.md · L10–L34 |
| K3.3.a.1 | Genel Bakış | eq-parametric.md · L10 |
| K3.3.a.2 | Teknik Detaylar | eq-parametric.md · L14 |
| K3.3.a.3 | EQ Bant Yapısı | eq-parametric.md · L16 |
| K3.3.a.4 | Biquad Filtre Tipleri | eq-parametric.md · L34 |
| **K3.3.b** | **31-Bant & Preset** | eq-parametric.md · L108–L258 |
| K3.3.b.1 | 31-Bant EQ Implementasyonu | eq-parametric.md · L108 |
| K3.3.b.2 | Preset Sistemi | eq-parametric.md · L258 |
| **K3.3.c** | **Arayüz & Metrikler** | eq-parametric.md · L290–L337 |
| K3.3.c.1 | API / Arayüz | eq-parametric.md · L290 |
| K3.3.c.2 | Performans Metrikleri | eq-parametric.md · L327 |
| K3.3.c.3 | Bağımlılıklar | eq-parametric.md · L337 |

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (24 satır · 309-332. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


| ADR | Konu |
|-----|------|
| ADR-025 | 31-band parametrik EQ |
| ADR-062 | DSP Pipeline Architecture |

---

##### Alt Katman Şeması (K3.a.b.c)

> **Zincir Kuralı (kanıt: dsp-chain.md L16 · README §2.2 L68):** DSP zinciri EQ → Compressor → Reverb → Limiter sırasıyla akar. Crossover (Linkwitz-Riley 4. derece, README §2.1 L59) zincirin kardeş bileşenidir, aşama değildir.
> **Kapsam:** 18 Markdown dosyası · yalnız diskteki H2/H3 başlıkları + index.md Dosya Haritası tablo satırları · 210 başlık − 25 hariç + 15 tablo satırı = 200 kanıtlı yaprak · 0 uydurma değer.

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|-----------|----|-----------|-------------------|----------------|
| K3.1 | Neva Engine Çekirdeği | 3 | 12 | neva-engine-core.md |
| K3.2 | DSP Zinciri | 3 | 11 | dsp-chain.md |
| K3.3 | Parametrik EQ | 3 | 9 | eq-parametric.md |
| K3.4 | Dinamik & Efektler | 3 | 32 | dynamics-compressor.md · effects-reverb.md · effects-chorus-delay.md |
| K3.5 | Analiz & Çalma | 3 | 27 | analysis-spectrum.md · playback-gapless.md · stream-buffer.md |
| K3.6 | Format & Dönüşüm | 3 | 30 | format-decoder.md · sample-rate-conversion.md · bit-depth-conversion.md |
| K3.7 | Kanal & Mixer | 3 | 29 | channel-processing.md · mixer-routing.md · surround-decoder.md |
| K3.8 | Katman İndeksi | 2 | 28 | index.md |
| K3.9 | Taşıyıcı Dokümantasyon | 2 | 22 | README.md |

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (26 satır · 623-664. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| 11 | playback-gapless.md | Gapless çalma | 6 | 4 | 9 | L10–L376 |
| 12 | mixer-routing.md | Mixer / routing | 6 | 5 | 10 | L10–L375 |
| 13 | channel-processing.md | Kanal işleme | 6 | 5 | 10 | L10–L335 |
| 14 | sample-rate-conversion.md | Örnekleme dönüşümü | 6 | 4 | 9 | L10–L283 |
| 15 | bit-depth-conversion.md | Bit derinliği | 6 | 5 | 10 | L10–L328 |
| 16 | index.md | Katman indeksi | 9 | 9 | 28 (13 başlık + 15 satır) | L10–L128 |
| 17 | README.md | Taşıyıcı doküman | 10 | 12 | 22 | L26–L335 |
| 18 | CLAUDE.md | Ajan kural dosyası | 5 | 0 | 0 (5 hariç) | L16–L57 |
| | **TOPLAM** | 18 dosya | **114** | **96** | **200** | |

Katalog notları:

1. **Sayım zinciri:** 210 başlık − 25 hariç + 15 tablo satırı = **200 kanıtlı yaprak**; hiyerarşi 9 × 2. katman · 25 × 3. katman · 200 × 4. katman. Onaylı hedef 9/6/200 karşılandı (3. katman tabanı 6'nın üzerinde).
2. **Hariç tutulan 25:** 16 × "Durum: Implementasyon" (içerik dosyalarının 16'sında birer kez — durum metni, katman kanıtı değil; K2 emsali); 4 × index.md numaralı ilke başlığı (L51, L57, L63, L68 — "Temel İlkeler" L49 altında birleşir, K2 emsali); 5 × CLAUDE.md başlığı (L16, L26, L41, L47, L57 — guardrail/kural dosyası, katman yaprağı değil).
3. **Eklenen 15 yaprak:** index.md Dosya Haritası tablo satırları (L112–L126); 15 satırın tamamı dizinde gerçekten var olan 15 .md dosyasına işaret eder (dosya listesiyle birebir örtüşür) — başlık yerine tablo satırı kanıtı.
4. **Katman kuralı:** DSP zinciri EQ → Compressor → Reverb → Limiter (dsp-chain.md L16 "15-Aşamalı Pipeline" + README §2.2 L68 akışı). Crossover (README §2.1 L59–L61) kardeş bileşendir; zincir aşaması olarak K3.2 altında sayılmadı, mixer/surround tarafında (K3.7) yer alır.
5. **Uydurma koruma:** README §9 tablosundaki diskte olmayan 6 ad (31-band-eq.md, reverb-modes.md, mixer-architecture.md, ring-buffer.md, zero-allocation.md, bit-perfect.md) yaprak sayılmadı; §9/§10 başlıklarının kendisi diskte olduğu için K3.9.b'de sayıldı.
6. **Tutarlılık kararı:** her dosyanın "Teknik Detaylar" H2'si ile altındaki H3 başlıkları ayrı yaprak sayıldı (başlık satırının tamamı disk kanıtıdır); "Bağımlılıklar" H2'leri gerçek katman bağımlılık tabloları taşıdığı için yaprak olarak korundu (K0–K2 ile aynı).

---

*K3 Ses İşleme Motoru Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*


### Kaynak: `CLAUDE.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/CLAUDE.md` (59 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### K3 Ses Motoru — CLAUDE.md

**Bu dosya K3 katmanı için özel AI talimatlarını içerir.**

##### 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Audio thread malloc/free/new/delete yasak | Crash |
| 2 | Audio thread mutex yasak | Deadlock |
| 3 | noexcept zorunlu (callback) | Crash |
| 4 | alignas(64) zorunlu | False sharing |
| 5 | constexpr buffer zorunlu | Runtime alloc |

##### 2. Yasaklı Örüntüler

```cpp
// ❌ YASAK — Audio thread'de
std::vector<float> buf(samples);     // Heap alloc
float* p = new float[samples];       // Heap alloc
std::mutex mtx; mtx.lock();          // Mutex
std::shared_ptr<X> sp;               // Atomic refcount

// ✅ DOĞRU
alignas(64) float buf[4096];         // Stack/member
std::atomic<size_t> head;            // Lock-free
constexpr int MAX = 4096;            // Compile-time
```

##### 3. DSP Zincir Sırası

```
Input → Gain → 31-Band EQ → Compressor → Reverb → Limiter → Output
```

##### 4. Frekans Aralıkları

| Bant | Frekans | Kullanım |
|------|---------|----------|
| Sub-bass | 20-80Hz | Subwoofer |
| Bass | 80-250Hz | Ana bas |
| Mid | 250Hz-4kHz | Vokal, enstrüman |
| Presence | 4kHz-10kHz | Netlik |
| Air | 10kHz-20kHz | Parlaklık |

##### 5. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-025 | 31-band parametrik EQ |
| ADR-062 | DSP Pipeline Architecture |

---

*K3 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*



## 12. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | 15 aşama şeması | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/index.md` | Kanıtlı |
| 2 | Zincir kuralı + crossover kardeş | `_backup/.../k3-ses-motoru/README.md` §2.1-§2.2 | Kanıtlı |
| 3 | K3.2 DSP Zinciri (3 katman, 11 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 4 | Stage/biquad/ring buffer | `_backup/.../k3-ses-motoru/dsp-chain.md` (398 satır) | Kanıtlı |
| 5 | FFT/spectrum/freq response/waterfall | `_backup/.../k3-ses-motoru/analysis-spectrum.md` (423 satır) | Kanıtlı |
| 6 | Frekans bantları + guardrail | `_backup/.../k3-ses-motoru/CLAUDE.md` §1, §4 | Kanıtlı |
| 7 | Ölçüm değerleri | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` — `dsp-chain.md`,
`analysis-spectrum.md`, `index.md`, `README.md`, `CLAUDE.md` (salt-okunur).
