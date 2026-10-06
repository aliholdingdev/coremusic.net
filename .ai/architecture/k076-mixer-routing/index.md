---
title: "K076 Mixer / Routing — Klasör Dizini (index)"
type: architecture-index
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K076 — Mixer / Routing Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K076 |
| **Ad** | Mixer / Routing (Bus, Channel Strip, Yönlendirme Matrisi) |
| **Amaç** | Çoklu kanalın bus'lara yönlendirilip toplanmasını, 8.1 surround routing'ini ve bass management'i tek klasörde toplamak |
| **Bağımlılık** | Yukarı: `[[../k073-dsp-chain/index]]` (10. aşama) · Besleyen: `[[../k080-channel-processing/index]]` · Aşağı: `[[../k074-eq-parametric/index]]`, `[[../k082-dynamics-compressor/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `performance-engineer` (toplama maliyeti), `qa-engineer` (matris testi) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` — `mixer-routing.md` 382, `index.md` 131, `README.md` §7, §Alt Katman K3.7 |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Kaynak |
|---|---|---|---|---|
| 1 | `[[mixer-routing]]` | Mixer / Routing | Bus mimarisi, channel strip, matris, manager, 8.1 routing, bass management | `mixer-routing.md` (382) |
| 2 | `[[index]]` | Bu dizin | Klasör özeti, envanter, komşu bağlantılar, kanıt | `index.md` (131) + `README.md` §7 |

**Okuma sırası:** `index` → `mixer-routing` → kanal girdileri için `[[../k080-channel-processing/index]]` → seviye güvenliği için `[[../k082-dynamics-compressor/index]]`.

## 2. Komşu Klasör Bağlantıları

| # | Klasör | İlişki | Wiki-link |
|---|---|---|---|
| 1 | k072-neva-engine-core | Motor | `[[../k072-neva-engine-core/index]]` |
| 2 | k073-dsp-chain | 10. aşama | `[[../k073-dsp-chain/index]]` |
| 3 | k073 (analiz) | Bus tap ölçümü | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 4 | k074-eq-parametric | 11. master EQ | `[[../k074-eq-parametric/index]]` |
| 5 | k075-sample-rate-conversion | Bus hedef hızı | `[[../k075-sample-rate-conversion/index]]` |
| 6 | k077-stream-buffer | Akış kaynağı | `[[../k077-stream-buffer/index]]` |
| 7 | k078-playback-gapless | Parça geçişinde bus durumu | `[[../k078-playback-gapless/index]]` |
| 8 | k079-bit-depth-conversion | Çıkış dönüşümü | `[[../k079-bit-depth-conversion/index]]` |
| 9 | k080-channel-processing | Kanal girdisi/matris | `[[../k080-channel-processing/index]]` |
| 10 | k081-format-decoder | Çözümlenmiş kaynak | `[[../k081-format-decoder/index]]` |
| 11 | k082-dynamics-compressor | 12. limiter | `[[../k082-dynamics-compressor/index]]` |
| 12 | k083-effects-reverb | Reverb send bus'ı | `[[../k083-effects-reverb/index]]` |

## 3. Bağımlılık Matrisi

| # | Hedef | Tür | Not |
|---|---|---|---|
| 1 | `[[../k073-dsp-chain/index]]` | yukarı | 10. aşama |
| 2 | `[[../k080-channel-processing/index]]` | besleyici | Kanal eşleme |
| 3 | `[[../k080-channel-processing/surround-decoder]]` | besleyici | 5.1/7.1/8.1 girdi |
| 4 | `[[../k074-eq-parametric/index]]` | aşağı | Master EQ |
| 5 | `[[../k082-dynamics-compressor/index]]` | aşağı | Limiter |
| 6 | `[[../k083-effects-reverb/index]]` | aşağı | Send bus |
| 7 | `[[../k079-bit-depth-conversion/index]]` | aşağı | Çıkış dither |
| 8 | `[[../k077-stream-buffer/index]]` | besleyici | Akış |
| 9 | `[[../k081-format-decoder/index]]` | besleyici | Kaynak |
| 10 | `[[../k078-playback-gapless/index]]` | yandan | Geçiş |
| 11 | `[[../k075-sample-rate-conversion/index]]` | yandan | Hız |
| 12 | `[[../k072-neva-engine-core/index]]` | yukarı | RT yürütme |

## 4. Sinyal Akışı Özeti

```
kaynaklar --> [channel strip xN] --> [routing matrix] --> [bus summing] --> [11 master EQ] --> [12 limiter] --> cikis
                     |                     |                    |
                 gain/pan/mute       8.1 matris (k073/k080)   bass management (README §7.2)
```

Toplama sonrası seviye artışı limiter ile korunur; analiz tap'ı bus'tan okur.

## 5. Kaynak Envanteri

| # | Dosya | Satır | İçerik | Kullanım |
|---|---|---:|---|---|
| 1 | `_backup/.../k3-ses-motoru/mixer-routing.md` | 382 | Bus, strip, matris, manager | `[[mixer-routing]]` §4 |
| 2 | `_backup/.../k3-ses-motoru/index.md` | 131 | Pipeline + hedefler | §11 verbatim |
| 3 | `_backup/.../k3-ses-motoru/README.md` | 664 | §7 mixer (8.1), §Alt Katman K3.7 | §11 verbatim |
| 4 | `_backup/.../k3-ses-motoru/CLAUDE.md` | 69 | Guardrail, sıralar | §11 verbatim |
| 5 | `_backup/.../k3-ses-motoru/channel-processing.md` | 342 | Kanal girdisi | Komşu |
| 6 | `_backup/.../k3-ses-motoru/surround-decoder.md` | 305 | Surround girdi | Komşu |

## 6. Kenar Durumları

| # | Senaryo | Davranış |
|---|---|---|
| 1 | Tek kaynak | Normal akış |
| 2 | Sıfır kaynak | Sessiz bus |
| 3 | Tam kazanç toplama | Clip riski → limiter |
| 4 | 8.1 çıkış | Kanal yerleşimi doğru |
| 5 | Bass management kapalı | Sub yok |
| 6 | Matris hücresi 0 | Bağlantı yok |
| 7 | Pan orta | Eşit dağılım (katsayı ⚠️) |
| 8 | Kanal mute | Sessiz |
| 9 | Reverb send açık | Islak bus toplanır |
| 10 | Parça geçişi | Bus durumu korunur |
| 11 | 128 kanal | Maliyet ölçeklenir |
| 12 | Denormal | Flush-to-zero |

## 7. Hata Modları

| # | Belirti | Kök Neden | Klasör |
|---|---|---|---|
| 1 | Clip | Toplama kazancı | `[[mixer-routing]]` |
| 2 | Kanal kayması | Matris hatası | `[[mixer-routing]]` |
| 3 | Bozuk sub | Bass management ihlali | `[[mixer-routing]]` |
| 4 | Sessiz bus | Matris boş | `[[mixer-routing]]` |
| 5 | CPU aşımı | Kanal × bus | `[[mixer-routing]]` |
| 6 | Sönümleme | Faz/pan hatası | `[[../k080-channel-processing/index]]` |
| 7 | Yankılı ıslak sinyal | Send seviyesi | `[[../k083-effects-reverb/index]]` |
| 8 | Ölçme gürültüsü | Dither yok | `[[../k079-bit-depth-conversion/index]]` |
| 9 | Kuyruk taşması | Akış | `[[../k077-stream-buffer/index]]` |
| 10 | Bozuk teslim | Çıkış formatı | `[[../k081-format-decoder/index]]` |

## 8. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Kanal routing matrisi | `README.md` §7.1 | Kaynak |
| 7 | Bass management | `README.md` §7.2 | Kaynak |
| 8 | K3.7 yaprak | 29 | `README.md` §Alt Katman Şeması |
| 9 | Bus/strip metrikleri | `mixer-routing.md` §Performans Metrikleri | Kaynak |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 9. Test ve Doğrulama Stratejisi

| # | Test | Kabul |
|---|---|---|
| 1 | Tek kanal → tek bus | Birebir |
| 2 | Eşit kazanç toplama | Beklenen seviye |
| 3 | Birim matris passthrough | Bayt eşitliği |
| 4 | Pan uç değerleri | Beklenen dağılım |
| 5 | Bass management | Sub yalnız LF |
| 6 | 8.1 routing | Yerleşim doğru |
| 7 | Clip testi | Limiter korur |
| 8 | Bit-perfect | Bayt eşitliği |
| 9 | CPU | ≤ %10 ⚠️ |
| 10 | Latency | < 0.1 ms ⚠️ |
| 11 | 128 kanal | Kabul |
| 12 | Sessiz bus | Sessizlik |

## 10. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Pan katsayıları kanıtsız | Orta | Uydurulmadı |
| 3 | Sparse matris optimizasyonu kanıtsız | Orta | `[[mixer-routing]]` §8 |
| 4 | Ayrı ADR yok | Orta | ⚠️ VERIFICATION REQUIRED |
| 5 | 8.1 matrisin k080 ile sınırı belirsiz | Orta | Bölüm 2.2 |

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

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (28 satır · 267-294. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

###### 7.2 Bass Management

```
Crossover: Linkwitz-Riley 4th Order
Frequency: 80Hz

Low-pass → Subwoofer (LFE)
High-pass → Main speakers (Front, Center, Surround, Rear)
```

---

##### 8. Analyzer

###### 8.1 Spectrum Analyzer

| Parametre | Değer |
|-----------|-------|
| FFT Size | 4096 |
| Window | Hann |
| Overlap | 50% |
| Bands | 31 (1/3 octave) |
| Update Rate | 30 fps |

---

##### 9. İlgili Dosyalar


### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (37 satır · 522-558. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| K3.7.b.1 | Genel Bakış | mixer-routing.md · L10 |
| K3.7.b.2 | Teknik Detaylar | mixer-routing.md · L14 |
| K3.7.b.3 | Bus Mimarisi | mixer-routing.md · L16 |
| K3.7.b.4 | Channel Strip | mixer-routing.md · L34 |
| K3.7.b.5 | Bus Implementasyonu | mixer-routing.md · L111 |
| K3.7.b.6 | Routing Matrix | mixer-routing.md · L174 |
| K3.7.b.7 | Mixer Manager | mixer-routing.md · L244 |
| K3.7.b.8 | API / Arayüz | mixer-routing.md · L316 |
| K3.7.b.9 | Performans Metrikleri | mixer-routing.md · L358 |
| K3.7.b.10 | Bağımlılıklar | mixer-routing.md · L368 |
| **K3.7.c** | **Surround Çözücü** | surround-decoder.md · L10–L291 |
| K3.7.c.1 | Genel Bakış | surround-decoder.md · L10 |
| K3.7.c.2 | Teknik Detaylar | surround-decoder.md · L14 |
| K3.7.c.3 | Surround Formatları | surround-decoder.md · L16 |
| K3.7.c.4 | Downmix Matrisi | surround-decoder.md · L47 |
| K3.7.c.5 | Upmix Matrisi | surround-decoder.md · L107 |
| K3.7.c.6 | Surround Decoder Implementasyonu | surround-decoder.md · L160 |
| K3.7.c.7 | API / Arayüz | surround-decoder.md · L245 |
| K3.7.c.8 | Performans Metrikleri | surround-decoder.md · L281 |
| K3.7.c.9 | Bağımlılıklar | surround-decoder.md · L291 |

###### K3.8 — Katman İndeksi

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.8.a** | **Yapı & Kapsam** | index.md · L10–L100 |
| K3.8.a.1 | Genel Bakış | index.md · L10 |
| K3.8.a.2 | Mimari Konum | index.md · L14 |
| K3.8.a.3 | Kapsam ve Kategoriler | index.md · L22 |
| K3.8.a.4 | Temel Motor | index.md · L24 |
| K3.8.a.5 | Efektler | index.md · L29 |
| K3.8.a.6 | Analiz | index.md · L35 |
| K3.8.a.7 | Format Desteği | index.md · L39 |
| K3.8.a.8 | İşleme | index.md · L44 |
| K3.8.a.9 | Temel İlkeler | index.md · L49 |
| K3.8.a.10 | DSP Pipeline | index.md · L73 |
| K3.8.a.11 | Performans Metrikleri | index.md · L90 |

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
| 1 | Bus/strip/matrix/manager | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/mixer-routing.md` (382 satır) | Kanıtlı |
| 2 | 8.1 routing + bass management | `_backup/.../k3-ses-motoru/README.md` §7.1, §7.2 | Kanıtlı |
| 3 | K3.7 (29 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 4 | 10. aşama sırası | `_backup/.../k3-ses-motoru/index.md` §DSP Pipeline | Kanıtlı |
| 5 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` | Kanıtlı (hedef) |
| 6 | Pan katsayıları, ölçüm | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/mixer-routing.md` +
`README.md` §7, §Alt Katman Şeması + `index.md`.
