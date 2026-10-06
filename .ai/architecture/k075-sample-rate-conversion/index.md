---
title: "K075 Sample Rate Conversion — Klasör Dizini (index)"
type: architecture-index
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K075 — Sample Rate Conversion Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K075 |
| **Ad** | Sample Rate Conversion (Interpolasyon / Decimation / Anti-Aliasing) |
| **Amaç** | Örnekleme hızı dönüşümünün (asenkron SRC, çok kademeli zincir, kalite preset'leri) tek klasörde toplanması ve 4. aşamanın sınırının tanımlanması |
| **Bağımlılık** | Yukarı: `[[../k073-dsp-chain/index]]` (4. aşama) · Aşağı: `[[../k074-eq-parametric/index]]`, `[[../k079-bit-depth-conversion/index]]` · Yanda: `[[../k081-format-decoder/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `qa-engineer` (alias ölçümü), `performance-engineer` (CPU) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` — `sample-rate-conversion.md` 290, `index.md` 131, `README.md` §Alt Katman K3.6 |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Kaynak |
|---|---|---|---|---|
| 1 | `[[sample-rate-conversion]]` | SRC | Algoritma, asenkron SRC, zincir SRC, preset'ler | `sample-rate-conversion.md` (290) |
| 2 | `[[index]]` | Bu dizin | Klasör özeti, envanter, komşu bağlantılar, kanıt | `index.md` (131) + `README.md` |

**Okuma sırası:** `index` → `sample-rate-conversion` → alias ölçümü için `[[../k073-dsp-chain/analysis-spectrum]]` → bit dönüşümü için `[[../k079-bit-depth-conversion/index]]`.

## 2. Komşu Klasör Bağlantıları

| # | Klasör | İlişki | Wiki-link |
|---|---|---|---|
| 1 | k072-neva-engine-core | Motor (hız yapılandırması) | `[[../k072-neva-engine-core/index]]` |
| 2 | k073-dsp-chain | 4. aşama sahibi | `[[../k073-dsp-chain/index]]` |
| 3 | k073 (analiz) | Alias/frekans ölçümü | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 4 | k074-eq-parametric | SRC sonrası aşama | `[[../k074-eq-parametric/index]]` |
| 5 | k076-mixer-routing | Bus hedef hızı | `[[../k076-mixer-routing/index]]` |
| 6 | k077-stream-buffer | Akış hızı girdisi | `[[../k077-stream-buffer/index]]` |
| 7 | k078-playback-gapless | Hız değişimi anı | `[[../k078-playback-gapless/index]]` |
| 8 | k079-bit-depth-conversion | Ardışık dönüşüm | `[[../k079-bit-depth-conversion/index]]` |
| 9 | k080-channel-processing | Kanal başına dönüşüm | `[[../k080-channel-processing/index]]` |
| 10 | k081-format-decoder | Kaynak hız (decode) | `[[../k081-format-decoder/index]]` |
| 11 | k082-dynamics-compressor | Sonraki dinamik | `[[../k082-dynamics-compressor/index]]` |
| 12 | k083-effects-reverb | Efekt öncesi hız sabitleme | `[[../k083-effects-reverb/index]]` |

## 3. Bağımlılık Matrisi

| # | Hedef | Tür | Not |
|---|---|---|---|
| 1 | `[[../k073-dsp-chain/index]]` | yukarı | 4. aşama |
| 2 | `[[../k081-format-decoder/index]]` | besleyici | Çözümlenen hız |
| 3 | `[[../k077-stream-buffer/index]]` | besleyici | Akış hızı/taşma |
| 4 | `[[../k074-eq-parametric/index]]` | aşağı | Katsayılar hedef hıza göre |
| 5 | `[[../k079-bit-depth-conversion/index]]` | aşağı | Dönüşüm çifti |
| 6 | `[[../k080-channel-processing/index]]` | yandan | Kanal başına |
| 7 | `[[../k076-mixer-routing/index]]` | aşağı | Bus hızı |
| 8 | `[[../k082-dynamics-compressor/index]]` | aşağı | Dinamik öncesi sabit hız |
| 9 | `[[../k083-effects-reverb/index]]` | aşağı | Reverb gecikme çizgileri hıza duyarlı |
| 10 | `[[../k072-neva-engine-core/index]]` | yukarı | RT yürütme |
| 11 | `[[../k073-dsp-chain/analysis-spectrum]]` | yandan | Alias doğrulama |
| 12 | `[[../k078-playback-gapless/index]]` | yandan | Yeniden plan tetikleyicisi |

## 4. Sinyal Akışı Özeti

```
decode/akis (44.1k/48k/...) --> [3 format] --> [4 SRC] --> [5 EQ] --> [6 Dynamics] ... --> [15 format output]
                                          |
                        anti-alias + interpolasyon (asenkron veya zincir SRC)
                                          |
                              alias olcumu: [[../k073-dsp-chain/analysis-spectrum]]
```

## 5. Kaynak Envanteri

| # | Dosya | Satır | İçerik | Kullanım |
|---|---|---:|---|---|
| 1 | `_backup/.../k3-ses-motoru/sample-rate-conversion.md` | 290 | Algoritma, asenkron, zincir, preset | `[[sample-rate-conversion]]` §4 |
| 2 | `_backup/.../k3-ses-motoru/index.md` | 131 | 15 aşama + performans hedefleri | §11 verbatim |
| 3 | `_backup/.../k3-ses-motoru/README.md` | 664 | §Alt Katman K3.6, §9-§10 | §11 verbatim |
| 4 | `_backup/.../k3-ses-motoru/CLAUDE.md` | 69 | Guardrail, sıralar, bantlar | §11 verbatim |
| 5 | `_backup/.../k3-ses-motoru/bit-depth-conversion.md` | 335 | Ardışık dönüşüm | Komşu klasör |
| 6 | `_backup/.../k3-ses-motoru/format-decoder.md` | 384 | Çözümlenen hız | Komşu klasör |

## 6. Kenar Durumları

| # | Senaryo | Davranış |
|---|---|---|
| 1 | Hız eşitliği | Passthrough |
| 2 | 44.1 → 48 kHz | Asenkron/çok kademeli |
| 3 | 48 → 384 kHz | Yüksek maliyet |
| 4 | Saat drift (asenkron) | Sürekli faz düzeltmesi |
| 5 | 128 kanal | Maliyet ölçeklenir |
| 6 | Düşük kalite preset | Daha yüksek alias |
| 7 | Sessiz girdi | Sessiz çıkış (durulma) |
| 8 | Parça geçişi | Yeniden plan |
| 9 | 16-bit bit derinliği | Dither ayrıca gerekli |
| 10 | Denormal girdi | Flush-to-zero |

## 7. Hata Modları

| # | Belirti | Kök Neden | Klasör |
|---|---|---|---|
| 1 | Frekans sarması | Anti-alias yok | `[[sample-rate-conversion]]` |
| 2 | Dropouts | Tampon yetersiz | `[[../k077-stream-buffer/index]]` |
| 3 | CPU aşımı | Tek kademeli yüksek oran | `[[sample-rate-conversion]]` |
| 4 | Ton kayması | Faz/DC kayması | `[[sample-rate-conversion]]` |
| 5 | Ölçme gürültüsü | Dither yok | `[[../k079-bit-depth-conversion/index]]` |
| 6 | EQ bozuk | Hız uyuşmazlığı | `[[../k074-eq-parametric/index]]` |
| 7 | Reverb doğal olmayan | Gecikme çizgisi hıza bağlı | `[[../k083-effects-reverb/index]]` |
| 8 | Kanal dengesizliği | Kanal bazlı dönüşüm farkı | `[[../k080-channel-processing/index]]` |
| 9 | Bozuk bit-perfect | Dönüşüm devrede | `[[../k079-bit-depth-conversion/index]]` |
| 10 | Ölçüm belirsiz | Test vektörü yok | `[[../k073-dsp-chain/analysis-spectrum]]` |

## 8. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | K3.6 yaprak | 30 | `README.md` §Alt Katman Şeması |
| 7 | SRC kalite preset'leri | Kaynakta tanımlı | `sample-rate-conversion.md` §Quality Presetleri |
| 8 | SRC gecikme/CPU | Kaynakta §Performans Metrikleri | `sample-rate-conversion.md` |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 9. Test ve Doğrulama Stratejisi

| # | Test | Kabul |
|---|---|---|
| 1 | Sinüs → SRC → spektrum | Alias eşiği altında (eşik ⚠️) |
| 2 | 44.1 → 48 kHz | Artefakt yok |
| 3 | 48 → 384 kHz | Çalışır |
| 4 | Hız eşit passthrough | Bayt eşitliği |
| 5 | Asenkron drift | Sürekli çalışma |
| 6 | Zincir vs tek kademe | CPU karşılaştırma ⚠️ |
| 7 | Preset farkı | Alias değişimi |
| 8 | Bit-perfect (dönüşüm kapalı) | Bayt eşitliği |
| 9 | CPU | ≤ %10 ⚠️ |
| 10 | Latency | < 0.1 ms ⚠️ |
| 11 | 128 kanal | Kabul |
| 12 | Sessiz girdi | Sessiz çıkış |

## 10. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Alias eşiği sayısal değeri yok | Yüksek | Uydurulmadı |
| 3 | Saat kaynağı vault'ta tanımsız | Orta | Donanım katmanı (kapsam dışı) |
| 4 | Ayrı ADR yok | Orta | ⚠️ VERIFICATION REQUIRED |
| 5 | Bypass politikası kanıtsız | Orta | §6-1 varsayımı |

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


### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (38 satır · 484-521. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| **K3.6.b** | **Örnekleme Dönüşümü** | sample-rate-conversion.md · L10–L276 |
| K3.6.b.1 | Genel Bakış | sample-rate-conversion.md · L10 |
| K3.6.b.2 | Teknik Detaylar | sample-rate-conversion.md · L14 |
| K3.6.b.3 | SRC Algoritması | sample-rate-conversion.md · L16 |
| K3.6.b.4 | Asenkron SRC | sample-rate-conversion.md · L32 |
| K3.6.b.5 | Zincir SRC (Multi-stage) | sample-rate-conversion.md · L152 |
| K3.6.b.6 | Quality Presetleri | sample-rate-conversion.md · L200 |
| K3.6.b.7 | API / Arayüz | sample-rate-conversion.md · L231 |
| K3.6.b.8 | Performans Metrikleri | sample-rate-conversion.md · L267 |
| K3.6.b.9 | Bağımlılıklar | sample-rate-conversion.md · L276 |
| **K3.6.c** | **Bit Derinliği** | bit-depth-conversion.md · L10–L321 |
| K3.6.c.1 | Genel Bakış | bit-depth-conversion.md · L10 |
| K3.6.c.2 | Teknik Detaylar | bit-depth-conversion.md · L14 |
| K3.6.c.3 | Bit Derinliği Dönüşüm Tablosu | bit-depth-conversion.md · L16 |
| K3.6.c.4 | Dithering Implementasyonu | bit-depth-conversion.md · L32 |
| K3.6.c.5 | Noise Shaping | bit-depth-conversion.md · L107 |
| K3.6.c.6 | Bit Depth Converter | bit-depth-conversion.md · L159 |
| K3.6.c.7 | Truncation vs Rounding | bit-depth-conversion.md · L258 |
| K3.6.c.8 | API / Arayüz | bit-depth-conversion.md · L280 |
| K3.6.c.9 | Performans Metrikleri | bit-depth-conversion.md · L311 |
| K3.6.c.10 | Bağımlılıklar | bit-depth-conversion.md · L321 |

###### K3.7 — Kanal & Mixer

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.7.a** | **Kanal İşleme** | channel-processing.md · L10–L328 |
| K3.7.a.1 | Genel Bakış | channel-processing.md · L10 |
| K3.7.a.2 | Teknik Detaylar | channel-processing.md · L14 |
| K3.7.a.3 | Kanal Haritalama Tablosu | channel-processing.md · L16 |
| K3.7.a.4 | Mono/Stereo Dönüşümü | channel-processing.md · L37 |
| K3.7.a.5 | Kanal Eşleme Matrisi | channel-processing.md · L85 |
| K3.7.a.6 | Downmix Implementasyonu | channel-processing.md · L158 |
| K3.7.a.7 | Upmix Implementasyonu | channel-processing.md · L189 |
| K3.7.a.8 | API / Arayüz | channel-processing.md · L278 |
| K3.7.a.9 | Performans Metrikleri | channel-processing.md · L318 |
| K3.7.a.10 | Bağımlılıklar | channel-processing.md · L328 |
| **K3.7.b** | **Mixer & Routing** | mixer-routing.md · L10–L368 |

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (42 satır · 43-84. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

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
| 1 | SRC algoritmaları + preset'ler | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/sample-rate-conversion.md` (290 satır) | Kanıtlı |
| 2 | 4. aşama sırası | `_backup/.../k3-ses-motoru/index.md` §DSP Pipeline | Kanıtlı |
| 3 | K3.6 Format & Dönüşüm (30 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 4 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` | Kanıtlı (hedef) |
| 5 | Numerik hassasiyet | `_backup/.../k3-ses-motoru/index.md` §Temel İlkeller | Kanıtlı |
| 6 | Ölçüm değerleri, alias eşiği | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/sample-rate-conversion.md`
+ `index.md` §DSP Pipeline, §Performans Metrikleri + `README.md` §Alt Katman K3.6.
