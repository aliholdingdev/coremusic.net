---
title: "K072 Neva Engine Core — Klasör Dizini (index)"
type: architecture-index
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K072 — Neva Engine Core Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K072 |
| **Ad** | Neva Engine Core (C++20 / Real-Time Safe / Lock-Free Çekirdek) |
| **Amaç** | Neva Engine çekirdeğinin yaşam döngüsünü, thread/bellek disiplinini ve K3 katmanının üst
düzey mimarisini tek klasörde toplamak; DSP zincirine ve alt aşamalara sınır çizmek |
| **Bağımlılık** | Yukarı: K2 sürücü katmanı (kapsam dışı) · Aşağı: `[[../k073-dsp-chain/index]]` · Yanda: `[[../k077-stream-buffer/index]]`, `[[../k081-format-decoder/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `performance-engineer` (latency/CPU metrikleri) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` (salt-okunur yedek: `neva-engine-core.md` 371, `index.md` 131, `README.md` 664, `CLAUDE.md` 69 satır) |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Satır (hedef) |
|---|---|---|---|---|
| 1 | `[[neva-engine-core]]` | Neva Engine Core | Motor çekirdeği: durum makinesi, thread modeli, buffer pool, RT kuralları | ≥501 |
| 2 | `[[index]]` | Bu dizin | Klasör özeti, kaynak envanteri, komşu bağlantılar, kanıt | ≥501 |

**Okuma sırası:** `index` → `neva-engine-core` → (zincir içeriği için) `[[../k073-dsp-chain/index]]`.

## 2. Komşu Klasör Bağlantıları

| # | Klasör | İlişki | Wiki-link |
|---|---|---|---|
| 1 | k073-dsp-chain | Motorun yürüttüğü 15 aşamalı zincir | `[[../k073-dsp-chain/index]]` |
| 2 | k074-eq-parametric | 5. aşama | `[[../k074-eq-parametric/index]]` |
| 3 | k075-sample-rate-conversion | 4. aşama | `[[../k075-sample-rate-conversion/index]]` |
| 4 | k076-mixer-routing | 10. aşama | `[[../k076-mixer-routing/index]]` |
| 5 | k077-stream-buffer | Motoru besleyen kuyruk/IO | `[[../k077-stream-buffer/index]]` |
| 6 | k078-playback-gapless | Parça geçişi ve motor durumu | `[[../k078-playback-gapless/index]]` |
| 7 | k079-bit-depth-conversion | 13. aşama | `[[../k079-bit-depth-conversion/index]]` |
| 8 | k080-channel-processing | 2. aşama | `[[../k080-channel-processing/index]]` |
| 9 | k081-format-decoder | Motoru besleyen çözümleyici | `[[../k081-format-decoder/index]]` |
| 10 | k082-dynamics-compressor | 6. aşama | `[[../k082-dynamics-compressor/index]]` |
| 11 | k083-effects-reverb | 7-8. aşamalar (reverb/chorus/delay) | `[[../k083-effects-reverb/index]]` |
| 12 | k072 (bu klasör) | Çekirdek | `[[index]]` |

## 3. Bağımlılık Matrisi (K072 → D02)

| # | Hedef | Tür | Güç | Not |
|---|---|---|---|---|
| 1 | `[[../k073-dsp-chain/index]]` | zorunlu | Yüksek | Motor zincir yürütmeden işlevsiz |
| 2 | `[[../k081-format-decoder/index]]` | besleyici | Yüksek | Ham veri kaynağı |
| 3 | `[[../k077-stream-buffer/index]]` | besleyici | Orta | Kuyruk/IO dengesi |
| 4 | `[[../k076-mixer-routing/index]]` | aşağı | Orta | 10. aşama |
| 5 | `[[../k074-eq-parametric/index]]` | aşağı | Orta | 5. aşama |
| 6 | `[[../k082-dynamics-compressor/index]]` | aşağı | Orta | 6. aşama |
| 7 | `[[../k083-effects-reverb/index]]` | aşağı | Orta | 7-8. aşama |
| 8 | `[[../k078-playback-gapless/index]]` | yandan | Düşük | Zamanlama senkronu |
| 9 | `[[../k075-sample-rate-conversion/index]]` | aşağı | Orta | 4. aşama |
| 10 | `[[../k079-bit-depth-conversion/index]]` | aşağı | Düşük | 13. aşama |
| 11 | `[[../k080-channel-processing/index]]` | aşağı | Orta | 2. aşama |
| 12 | K2 sürücü katmanı | yukarı | Yüksek | Kapsam dışı, bu vault diliminde kanıt yok |

## 4. Sinyal Akışı Özeti (Katman)

```
K0 Donanım --> K1 OS --> K2 Surucu --> [ K3 SES MOTORU ] --> K4 Uygulama
                                             |
                 +---------------------------+---------------------------+
                 |                           |                           |
        15 asamali DSP zinciri        analiz (FFT)              format/IO decode
        (k073 -> k074..k083)          (k073 analysis)           (k081, k077)
```

K3, K2'den ham ses verisini alır, DSP uygular ve işlenmiş sinyali K2'ye geri iletir (kaynak:
`k3 index.md` §Mimari Konum).

## 5. Kaynak Envanteri (salt-okunur backup)

| # | Dosya | Satır | İçerik | Kullanım |
|---|---|---:|---|---|
| 1 | `_backup/.../k3-ses-motoru/README.md` | 664 | Katman master dokümanı, alt katman şeması K3.1-K3.9, ADR'ler | §11 verbatim |
| 2 | `_backup/.../k3-ses-motoru/index.md` | 131 | K3 genel bakış, 15 aşamalı şema, performans hedefleri, dosya haritası | §11 verbatim |
| 3 | `_backup/.../k3-ses-motoru/neva-engine-core.md` | 371 | Motor çekirdeği detayı | `[[neva-engine-core]]` §4 |
| 4 | `_backup/.../k3-ses-motoru/CLAUDE.md` | 69 | Hard guardrail'ler, yasaklı örüntüler, DSP sırası, frekans bantları | Her iki dosyada §10 |
| 5 | `_backup/.../k3-ses-motoru/dsp-chain.md` | 398 | 15 aşama, biquad, ring buffer | `[[../k073-dsp-chain/dsp-chain]]` |
| 6 | `_backup/.../k3-ses-motoru/` (diğer 11 dosya) | 290-423 | Efekt, analiz, format, kanal | İlgili K074-K083 klasörleri |
| 7 | **TOPLAM** | 18 dosya | README §"Dosya Haritası" 15 içerik + index + README + CLAUDE | — |

## 6. Kenar Durumları (Katman)

| # | Senaryo | Etkilenen K | Davranış |
|---|---|---|---|
| 1 | Motor başlatılmadan callback gelmesi | K072 | Red + sayaç |
| 2 | Örnekleme hızı değişimi | K072, K075 | Yeniden prepare |
| 3 | Kuyruk taşması | K072, K077 | Drop |
| 4 | Kuyruk boşluğu | K072, K077 | Mute/repeat |
| 5 | Parça geçişi anında zincir durumu | K078, K072 | Crossfade kesişimi |
| 6 | Kanal konfigürasyonu değişimi | K080, K076 | Matris yeniden kurulumu |
| 7 | Format algılama başarısızlığı | K081 | Hata → kuyruk boşaltma |
| 8 | Bit derinliği uyuşmazlığı | K079, K076 | Dither uygulanır |
| 9 | FFT bloğu taşması (pencere) | K073 (analysis) | Pencere kaydırma |
| 10 | Limiter eşiği aşımı | K082 | Gain reduction |

## 7. Hata Modları (Katman)

| # | Belirti | Kök Neden | Klasör |
|---|---|---|---|
| 1 | Glitch/tıklama | RT ihlali (alloc/mutex) | `[[../k072-neva-engine-core/index]]` |
| 2 | Ölü ses (silence) | Kuyruk boş / decode hatası | `[[../k077-stream-buffer/index]]`, `[[../k081-format-decoder/index]]` |
| 3 | Frekans yanıltması | Yanlış biquad katsayısı | `[[../k074-eq-parametric/index]]` |
| 4 | Alias (titreşim) | SRC anti-alias eksikliği | `[[../k075-sample-rate-conversion/index]]` |
| 5 | Ölçme hatası (dither yok) | Truncation | `[[../k079-bit-depth-conversion/index]]` |
| 6 | Kanal kayması | Yanlış downmix matrisi | `[[../k080-channel-processing/index]]` |
| 7 | Çarpma/clip | Master gain + limiter yok | `[[../k082-dynamics-compressor/index]]` |
| 8 | Reverb metallic/boş | Comb/allpass gecikme | `[[../k083-effects-reverb/index]]` |
| 9 | Crossfade klik | Sert kenar | `[[../k078-playback-gapless/index]]` |
| 10 | CPU aşımı | Zincir maliyeti | `[[../k073-dsp-chain/index]]` |

## 8. Performans ve Gereksinimler (Katman Hedefleri)

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | İşleme latency | < 0.1 ms | `k3 index.md` §Performans Metrikleri |
| 2 | CPU kullanımı | < %10 | `k3 index.md` |
| 3 | Maksimum kanal | 128 | `k3 index.md` |
| 4 | Maksimum örnekleme hızı | 384 kHz | `k3 index.md` |
| 5 | Bellek kullanımı | < 100 MB | `k3 index.md` |
| 6 | RT tahsis | 0 (yasak) | `CLAUDE.md` §1 |
| 7 | Alignment | `alignas(64)` | `CLAUDE.md` §1 |
| 8 | Alt katman yaprak sayısı | 200 kanıtlı yaprak (K3 geneli) | `README.md` §Alt Katman Şeması |
| 9 | K3.1 yaprak sayısı | 12 | `README.md` §K3.1 |
| 10 | K3.2 yaprak sayısı | 11 | `README.md` §K3.2 |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir, diskte ölçüm kanıtı yoktur.

## 9. Test ve Doğrulama Stratejisi (Katman)

| # | Test | Kapsadığı K |
|---|---|---|
| 1 | Bit-perfect uçtan uca karşılaştırma | K072, K073, K079 |
| 2 | RT tahsis/metrik denetimi | K072 |
| 3 | Kuyruk taşma/boşluk testi | K072, K077 |
| 4 | EQ düzlem ölçümü (magnitude) | K074 |
| 5 | SRC alias ölçümü | K075 |
| 6 | Downmix enerji testi | K080 |
| 7 | Gapless boşluk ölçümü | K078 |
| 8 | Limiter true-peak testi | K082 |
| 9 | Reverb RT60 kontrolü | K083 |
| 10 | FFT pencere/akış testi | K073 (analysis) |
| 11 | Decoder bit-identical test | K081 |
| 12 | Mixer matris doğruluğu | K076 |
| 13 | Dither SNR testi | K079 |
| 14 | Uzun süreli bellek testi | K072 geneli |

## 10. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok (hedefler hedef olarak kaldı) | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | `README.md` §9 listesinde backup'ta olmayan 6 dosya adı (`31-band-eq.md`, `reverb-modes.md`, `mixer-architecture.md`, `ring-buffer.md`, `zero-allocation.md`, `bit-perfect.md`) | Orta | K072 K satırı yapılmadı (kanıt yok) |
| 3 | `README.md` §9'da `architecture/k3-ses-motoru/31-band-eq.md` yazıyor; diskte `eq-parametric.md` var | Orta | Ad uyuşmazlığı |
| 4 | ADR-062 dondurulmuş aralık (001-037) dışında | Orta | `.ai/log.md` üst karar kaydı |
| 5 | K2/K4 sınırı bu dilimde kanıtlanamıyor | Düşük | Kapsam dışı işaretlendi |
| 6 | K3.1-K3.9 ↔ K072-K083 birebir eşlemesi vault'ta yok | Orta | ⚠️ VERIFICATION REQUIRED |

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


### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (20 satır · 351-370. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| K3.1.c.1 | API / Arayüz | neva-engine-core.md · L278 |
| K3.1.c.2 | Performans Metrikleri | neva-engine-core.md · L346 |
| K3.1.c.3 | Bağımlılıklar | neva-engine-core.md · L356 |

###### K3.2 — DSP Zinciri

*Zincir sırası: EQ (K3.3) → Compressor (K3.4.a) → Reverb (K3.4.b) → Limiter (K3.4.a · dynamics-compressor.md L148); Crossover kardeş bileşendir.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.2.a** | **Pipeline Tasarımı** | dsp-chain.md · L10–L68 |
| K3.2.a.1 | Genel Bakış | dsp-chain.md · L10 |
| K3.2.a.2 | Teknik Detaylar | dsp-chain.md · L14 |
| K3.2.a.3 | 15-Aşamalı Pipeline | dsp-chain.md · L16 |
| K3.2.a.4 | DSP Stage Arabirimi | dsp-chain.md · L41 |
| K3.2.a.5 | Biquad Filtre Yapısı | dsp-chain.md · L68 |
| **K3.2.b** | **Aşamalar & Yönetim** | dsp-chain.md · L106–L283 |
| K3.2.b.1 | Stage Implementasyonları | dsp-chain.md · L106 |
| K3.2.b.2 | Ring Buffer (Inter-Stage) | dsp-chain.md · L242 |
| K3.2.b.3 | Pipeline Yönetimi | dsp-chain.md · L283 |

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



## 12. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | K3 katmanı tanımı ve mimari konum | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/index.md` | Kanıtlı |
| 2 | K3.1 Neva Engine Çekirdeği (3. katman, 12 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 3 | ADR-025 / ADR-062 | `_backup/.../k3-ses-motoru/README.md` §10 · `CLAUDE.md` §5 | Kanıtlı |
| 4 | Kanıt kataloğu (K3) | `_backup/.../k3-ses-motoru/README.md` §Kanıt Kataloğu | Kanıtlı |
| 5 | §11 verbatim blokları | Aynı kaynakların ilgili satır aralıkları | Kanıtlı |
| 6 | Ölçüm değerleri | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` (salt-okunur yedek;
`index.md` 131 satır, `README.md` 664 satır, `neva-engine-core.md` 371 satır, `CLAUDE.md` 69 satır).
