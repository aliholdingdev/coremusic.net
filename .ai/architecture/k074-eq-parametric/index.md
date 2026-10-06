---
title: "K074 Parametrik EQ — Klasör Dizini (index)"
type: architecture-index
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K074 — Parametrik EQ Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K074 |
| **Ad** | Parametrik EQ (31 Bant / Biquad / IIR) |
| **Amaç** | 31 bantlı parametrik EQ'nun bant yapısını, biquad filtre tiplerini, preset sistemini ve frekans/katsayı planını tek klasörde toplamak |
| **Bağımlılık** | Yukarı: `[[../k073-dsp-chain/index]]` (5. ve 11. aşama) · Aşağı: `[[../k082-dynamics-compressor/index]]` · Yanda: `[[../k073-dsp-chain/analysis-spectrum]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `qa-engineer` (tepki eğrisi testi), `performance-engineer` (CPU) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` — `eq-parametric.md` 351, `index.md` 131, `README.md` §3, §10 (ADR-025) |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Kaynak |
|---|---|---|---|---|
| 1 | `[[eq-parametric]]` | Parametrik EQ | Bant yapısı, biquad tipleri, 31-bant implementasyonu, preset | `eq-parametric.md` (351) |
| 2 | `[[index]]` | Bu dizin | Klasör özeti, envanter, komşu bağlantılar, kanıt | `index.md` (131) + `README.md` §3 |

**Okuma sırası:** `index` → `eq-parametric` → tepki ölçümü için `[[../k073-dsp-chain/analysis-spectrum]]` → sıradaki aşama `[[../k082-dynamics-compressor/index]]`.

## 2. Komşu Klasör Bağlantıları

| # | Klasör | İlişki | Wiki-link |
|---|---|---|---|
| 1 | k072-neva-engine-core | Motor (init/prepare) | `[[../k072-neva-engine-core/index]]` |
| 2 | k073-dsp-chain | 5. ve 11. aşama sahibi | `[[../k073-dsp-chain/index]]` |
| 3 | k073 (analiz) | Frequency response ölçümü | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 4 | k075-sample-rate-conversion | EQ öncesi/sonrası hız | `[[../k075-sample-rate-conversion/index]]` |
| 5 | k076-mixer-routing | Bus/master EQ ilişkisi | `[[../k076-mixer-routing/index]]` |
| 6 | k077-stream-buffer | Girdi kaynağı | `[[../k077-stream-buffer/index]]` |
| 7 | k078-playback-gapless | Parça değişiminde EQ durumu | `[[../k078-playback-gapless/index]]` |
| 8 | k079-bit-depth-conversion | 16-bit çıkışta ölçme | `[[../k079-bit-depth-conversion/index]]` |
| 9 | k080-channel-processing | Kanal bazlı EQ | `[[../k080-channel-processing/index]]` |
| 10 | k081-format-decoder | Çözümlenmiş girdi | `[[../k081-format-decoder/index]]` |
| 11 | k082-dynamics-compressor | Sıradaki aşama | `[[../k082-dynamics-compressor/index]]` |
| 12 | k083-effects-reverb | Sıradaki efekt | `[[../k083-effects-reverb/index]]` |

## 3. Bağımlılık Matrisi

| # | Hedef | Tür | Güç | Not |
|---|---|---|---|---|
| 1 | `[[../k073-dsp-chain/index]]` | yukarı | Yüksek | EQ zincirsiz işlevsiz |
| 2 | `[[../k073-dsp-chain/analysis-spectrum]]` | yandan | Yüksek | Doğrulama ölçümü |
| 3 | `[[../k082-dynamics-compressor/index]]` | aşağı | Yüksek | Sıra: EQ → Compressor |
| 4 | `[[../k075-sample-rate-conversion/index]]` | yandan | Orta | Katsayı üretimi hız duyarlı |
| 5 | `[[../k076-mixer-routing/index]]` | aşağı | Orta | Master EQ → mixer |
| 6 | `[[../k080-channel-processing/index]]` | yandan | Orta | Kanal bazlı uygulama |
| 7 | `[[../k079-bit-depth-conversion/index]]` | aşağı | Düşük | Ölçme gürültüsü |
| 8 | `[[../k081-format-decoder/index]]` | besleyici | Orta | Ham PCM |
| 9 | `[[../k072-neva-engine-core/index]]` | yukarı | Yüksek | RT yürütme |
| 10 | `[[../k077-stream-buffer/index]]` | besleyici | Düşük | Akış girdisi |
| 11 | `[[../k078-playback-gapless/index]]` | yandan | Düşük | Preset geçişi riski |
| 12 | `[[../k083-effects-reverb/index]]` | aşağı | Orta | EQ → Reverb sırası |

## 4. Sinyal Akışı Özeti

```
girdi --> [4 SRC] --> [5 EQ: bant1..31] --> [6 Compressor] --> [7 Reverb] --> ... --> [11 Master EQ] --> [12 Limiter]
                                              |                                            |
                                              +---- tap --> FFT (frequency response) ------+
```

Katsayı üretimi double hassasiyetle; uygulama float32 kare üzerinde (`k3 index.md` §Numerik Hassasiyet).

## 5. Kaynak Envanteri

| # | Dosya | Satır | İçerik | Kullanım |
|---|---|---:|---|---|
| 1 | `_backup/.../k3-ses-motoru/eq-parametric.md` | 351 | Bant, biquad, implementasyon, preset | `[[eq-parametric]]` §4 |
| 2 | `_backup/.../k3-ses-motoru/index.md` | 131 | Pipeline + performans hedefleri | §11 verbatim |
| 3 | `_backup/.../k3-ses-motoru/README.md` | 664 | §3 EQ band frekansları, §3.2 katsayılar, §10 ADR | §11 verbatim |
| 4 | `_backup/.../k3-ses-motoru/CLAUDE.md` | 69 | Guardrail + DSP sırası + frekans bantları | §11 verbatim |
| 5 | `_backup/.../k3-ses-motoru/analysis-spectrum.md` | 423 | Tepki ölçümü | Komşu klasör |
| 6 | `_backup/.../k3-ses-motoru/` (kalan 12 dosya) | — | Diğer konular | İlgili dizinler |

## 6. Kenar Durumları

| # | Senaryo | Davranış |
|---|---|---|
| 1 | Tüm bantlar 0 dB | Passthrough |
| 2 | Q sınır değeri | Katsayı doğrulaması |
| 3 | Frekans DC/Nyquist sınırı | Parametre kısıtlanır |
| 4 | 5. + 11. aşama birlikte | Toplam tepki iki kez |
| 5 | Örnekleme hızı değişimi | Katsayı yeniden üretilir |
| 6 | 128 kanal | Maliyet ölçeklenir |
| 7 | Preset değişimi (canlı) | Atomik takas (politika ⚠️) |
| 8 | 16-bit çıkış | Dither gerekli |
| 9 | Denormal girdi | Flush-to-zero |
| 10 | EQ kapalıyken bit-perfect | Bayt eşitliği |

## 7. Hata Modları

| # | Belirti | Kök Neden | Klasör |
|---|---|---|---|
| 1 | Çarpık tepki | Katsayı hatası | `[[eq-parametric]]` |
| 2 | Faz kayması | Ardışık IIR | `[[eq-parametric]]` |
| 3 | CPU aşımı | 31 bant × kanal | `[[eq-parametric]]` |
| 4 | Yanlış ölçüm | Tap EQ öncesi | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 5 | Frekans sarması | SRC yok | `[[../k075-sample-rate-conversion/index]]` |
| 6 | Ölçme gürültüsü | Dither yok | `[[../k079-bit-depth-conversion/index]]` |
| 7 | Tıklama | Preset ani geçiş | `[[eq-parametric]]` |
| 8 | Aşırı sıkıştırma etkileşimi | EQ + compressor kazancı | `[[../k082-dynamics-compressor/index]]` |
| 9 | Kanal dengesizliği | Kanal bazlı EQ farkı | `[[../k080-channel-processing/index]]` |
| 10 | Bus aşımı | Master EQ + mixer | `[[../k076-mixer-routing/index]]` |

## 8. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Bant sayısı | 31 | `eq-parametric.md` · `README.md` §3 |
| 7 | K3.3 yaprak | 9 | `README.md` §Alt Katman Şeması |
| 8 | Frekans planı | `README.md` §3.1 | Kaynak |
| 9 | Katsayı tablosu | `README.md` §3.2 | Kaynak |
| 10 | Katsayı hassasiyeti | double | `k3 index.md` |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 9. Test ve Doğrulama Stratejisi

| # | Test | Kabul |
|---|---|---|
| 1 | Frekans taraması (magnitude) | Beklenen eğri |
| 2 | Tek bant impulse cevabı | Analitik eşleşme |
| 3 | 0 dB passthrough | Bayt eşitliği |
| 4 | Preset eşitliği | Katsayı birebir |
| 5 | NaN/Inf girdi | Bypass |
| 6 | Bit-perfect (EQ kapalı) | Bayt eşitliği |
| 7 | CPU ölçümü | ≤ %10 ⚠️ |
| 8 | Latency ölçümü | < 0.1 ms ⚠️ |
| 9 | 128 kanal / 384 kHz | Kabul |
| 10 | Master EQ ayrı test | Sıralı tutarlılık |
| 11 | Uzun süre | Bellek sabit |
| 12 | 16-bit çıkış + dither | SNR kontrolü |

## 10. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Preset geçiş politikası kanıtsız | Orta | ⚠️ VERIFICATION REQUIRED |
| 3 | `README.md` §9 ad uyuşmazlığı (`31-band-eq.md`) | Düşük | Diskte `eq-parametric.md` |
| 4 | ADR-025'in dondurulmuş aralık içindeliği | Orta | ⚠️ VERIFICATION REQUIRED |
| 5 | 5./11. aşama etkileşimi kaynakta ayrı ele alınmıyor | Orta | Yorum olarak işaretli |

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

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (73 satır · 85-157. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| 11 | 200 Hz | 0.7 | Mid |
| 12 | 250 Hz | 0.7 | Mid |
| 13 | 315 Hz | 0.7 | Mid |
| 14 | 400 Hz | 0.7 | Mid |
| 15 | 500 Hz | 0.7 | Mid |
| 16 | 630 Hz | 0.7 | Upper mid |
| 17 | 800 Hz | 0.7 | Upper mid |
| 18 | 1 kHz | 0.7 | Upper mid |
| 19 | 1.25 kHz | 0.7 | Presence |
| 20 | 1.6 kHz | 0.7 | Presence |
| 21 | 2 kHz | 0.7 | Presence |
| 22 | 2.5 kHz | 0.7 | Presence |
| 23 | 3.15 kHz | 0.7 | Brilliance |
| 24 | 4 kHz | 0.7 | Brilliance |
| 25 | 5 kHz | 0.7 | Brilliance |
| 26 | 6.3 kHz | 0.7 | Brilliance |
| 27 | 8 kHz | 0.7 | Air |
| 28 | 10 kHz | 0.7 | Air |
| 29 | 12.5 kHz | 0.7 | Air |
| 30 | 16 kHz | 0.7 | Air |
| 31 | 20 kHz | 0.7 | Air |

###### 3.2 Biquad Filtre Koefisyonları

```cpp
// peakingEQ coefficients
struct EQCoefficients {
    float b0, b1, b2, a1, a2;
};

EQCoefficients calculatePeakingEQ(
    float sampleRate,
    float frequency,
    float gain,
    float Q
) {
    float A = powf(10.0f, gain / 40.0f);
    float w0 = 2.0f * M_PI * frequency / sampleRate;
    float alpha = sinf(w0) / (2.0f * Q);

    float b0 = 1.0f + alpha * A;
    float b1 = -2.0f * cosf(w0);
    float b2 = 1.0f - alpha * A;
    float a0 = 1.0f + alpha / A;
    float a1 = -2.0f * cosf(w0);
    float a2 = 1.0f - alpha / A;

    // Normalize
    b0 /= a0; b1 /= a0; b2 /= a0;
    a1 /= a0; a2 /= a0;

    return { b0, b1, b2, a1, a2 };
}
```

---

##### 4. Reverb (4 Mod)

| Mod | Algoritma | Kullanım |
|-----|-----------|----------|
| Geniş Konser | FDN 16-line | Büyük mekan |
| Düğün Salonu | Plate reverb | Orta mekan |
| Oda | Room algorithm | Küçük mekan |
| Stüdyo | Algorithmic + convolution | Profesyonel |

###### 4.1 FDN Reverb Yapısı

```cpp
// 16-line FDN Reverb
class FDNReverb {
    static constexpr int NUM_LINES = 16;
    static constexpr int MAX_DELAY = 48000; // 1 second @ 48kHz

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (17 satır · 392-408. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


###### K3.4 — Dinamik & Efektler

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.4.a** | **Dinamik İşlemci** | dynamics-compressor.md · L10–L356 |
| K3.4.a.1 | Genel Bakış | dynamics-compressor.md · L10 |
| K3.4.a.2 | Teknik Detaylar | dynamics-compressor.md · L14 |
| K3.4.a.3 | Dinamik İşlemci Mimarisi | dynamics-compressor.md · L16 |
| K3.4.a.4 | Compressor Implementasyonu | dynamics-compressor.md · L32 |
| K3.4.a.5 | Limiter Implementasyonu | dynamics-compressor.md · L148 |
| K3.4.a.6 | Expander Implementasyonu | dynamics-compressor.md · L199 |
| K3.4.a.7 | Dinamik İşlemci Birleşimi | dynamics-compressor.md · L259 |
| K3.4.a.8 | API / Arayüz | dynamics-compressor.md · L302 |
| K3.4.a.9 | Performans Metrikleri | dynamics-compressor.md · L345 |
| K3.4.a.10 | Bağımlılıklar | dynamics-compressor.md · L356 |
| **K3.4.b** | **Reverb** | effects-reverb.md · L10–L323 |

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
| 1 | Bant/biquad/preset yapıları | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/eq-parametric.md` (351 satır) | Kanıtlı |
| 2 | EQ frekansları ve katsayı tablosu | `_backup/.../k3-ses-motoru/README.md` §3.1, §3.2 | Kanıtlı |
| 3 | K3.3 Parametrik EQ (9 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 4 | ADR-025 | `_backup/.../k3-ses-motoru/README.md` §10 · `CLAUDE.md` §5 | Kanıtlı |
| 5 | Sıra kuralı (EQ → Compressor) | `_backup/.../k3-ses-motoru/README.md` §2.2 L68 | Kanıtlı |
| 6 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` | Kanıtlı (hedef) |
| 7 | Ölçüm değerleri, preset geçiş politikası | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/eq-parametric.md` +
`README.md` §3, §10 + `index.md` §Performans Metrikleri + `CLAUDE.md` §1-§5.
