---
title: "K078 Gapless Playback — Klasör Dizini (index)"
type: architecture-index
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K078 — Gapless Playback Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K078 |
| **Ad** | Gapless Playback (Pre-decode / Crossfade / Manager) |
| **Amaç** | Boşluksuz çalma ve crossfade mekanizmasını, pre-decode zamanlamasını ve geçiş manager'ını tek klasörde toplamak |
| **Bağımlılık** | Besleyen: `[[../k081-format-decoder/index]]` · Tampon: `[[../k077-stream-buffer/index]]` · Yanda: `[[../k072-neva-engine-core/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `qa-engineer` (geçiş testleri), `performance-engineer` (pre-decode maliyeti) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/` — `playback-gapless.md` 383, `index.md` 131, `README.md` §Alt Katman K3.5 |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Kaynak |
|---|---|---|---|---|
| 1 | `[[playback-gapless]]` | Gapless Playback | Yapı, pre-decode, crossfade, manager | `playback-gapless.md` (383) |
| 2 | `[[index]]` | Bu dizin | Klasör özeti, envanter, komşu bağlantılar, kanıt | `index.md` (131) + `README.md` |

**Okuma sırası:** `index` → `playback-gapless` → girdi için `[[../k077-stream-buffer/index]]` → decode için `[[../k081-format-decoder/index]]`.

## 2. Komşu Klasör Bağlantıları

| # | Klasör | İlişki | Wiki-link |
|---|---|---|---|
| 1 | k072-neva-engine-core | Motor durumu | `[[../k072-neva-engine-core/index]]` |
| 2 | k073-dsp-chain | Zincir state geçişi | `[[../k073-dsp-chain/index]]` |
| 3 | k073 (analiz) | Geçiş ölçümü | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 4 | k074-eq-parametric | EQ state sıfırlama | `[[../k074-eq-parametric/index]]` |
| 5 | k075-sample-rate-conversion | Hız farklılığı | `[[../k075-sample-rate-conversion/index]]` |
| 6 | k076-mixer-routing | Bus çıkışı | `[[../k076-mixer-routing/index]]` |
| 7 | k077-stream-buffer | Tampon doldurma | `[[../k077-stream-buffer/index]]` |
| 8 | k079-bit-depth-conversion | Çıkış dönüşümü | `[[../k079-bit-depth-conversion/index]]` |
| 9 | k080-channel-processing | Kanal konfigürasyonu | `[[../k080-channel-processing/index]]` |
| 10 | k081-format-decoder | Pre-decode kaynağı | `[[../k081-format-decoder/index]]` |
| 11 | k082-dynamics-compressor | Release state | `[[../k082-dynamics-compressor/index]]` |
| 12 | k083-effects-reverb | Reverb tail | `[[../k083-effects-reverb/index]]` |

## 3. Bağımlılık Matrisi

| # | Hedef | Tür | Not |
|---|---|---|---|
| 1 | `[[../k081-format-decoder/index]]` | yukarı | Pre-decode |
| 2 | `[[../k077-stream-buffer/index]]` | aşağı | Doldurma/underrun |
| 3 | `[[../k072-neva-engine-core/index]]` | yukarı | Durum makinesi |
| 4 | `[[../k073-dsp-chain/index]]` | yandan | State geçişi |
| 5 | `[[../k074-eq-parametric/index]]` | yandan | EQ state |
| 6 | `[[../k082-dynamics-compressor/index]]` | yandan | Release |
| 7 | `[[../k083-effects-reverb/index]]` | yandan | Tail |
| 8 | `[[../k076-mixer-routing/index]]` | aşağı | Bus |
| 9 | `[[../k075-sample-rate-conversion/index]]` | yandan | Yeniden plan |
| 10 | `[[../k080-channel-processing/index]]` | yandan | Konfigürasyon |
| 11 | `[[../k079-bit-depth-conversion/index]]` | aşağı | Çıkış |
| 12 | `[[../k073-dsp-chain/analysis-spectrum]]` | yandan | Ölçüm |

## 4. Sinyal Akışı Özeti

```
parca A (caliyor) --> [zincir] --> cikis
parca B (pre-decode) --> [decode k081] --> [buffer k077] --|
                                                          +--> [crossfade / gapless kesme] --> [mixer k076]
[gapless manager] --gecis karari + esik-->                            |
                                                              state temizligi (EQ/dyn/reverb)
```

## 5. Kaynak Envanteri

| # | Dosya | Satır | İçerik | Kullanım |
|---|---|---:|---|---|
| 1 | `_backup/.../k3-ses-motoru/playback-gapless.md` | 383 | Yapı, pre-decode, crossfade, manager | `[[playback-gapless]]` §4 |
| 2 | `_backup/.../k3-ses-motoru/index.md` | 131 | Pipeline + hedefler | §11 verbatim |
| 3 | `_backup/.../k3-ses-motoru/README.md` | 664 | §Alt Katman K3.5, §9-§10 | §11 verbatim |
| 4 | `_backup/.../k3-ses-motoru/CLAUDE.md` | 69 | Guardrail, sıralar | §11 verbatim |
| 5 | `_backup/.../k3-ses-motoru/stream-buffer.md` | 378 | Doldurma | Komşu |
| 6 | `_backup/.../k3-ses-motoru/format-decoder.md` | 384 | Pre-decode kaynağı | Komşu |

## 6. Kenar Durumları

| # | Senaryo | Davranış |
|---|---|---|
| 1 | Gapless parça | Boşluksuz kesme |
| 2 | Crossfade devrede | Üst üste bindirme |
| 3 | Son parça | Geçiş yok → dur |
| 4 | Pre-decode geç biterse | Underrun riski |
| 5 | Parça < crossfade süresi | Kısaltma ⚠️ |
| 6 | Hız farklılığı | Yeniden plan |
| 7 | Kanal konfigürasyonu farklı | Uyarlama |
| 8 | Reverb tail | Politika ⚠️ |
| 9 | Compressor release | Politika ⚠️ |
| 10 | Duraklat/ileri-geri | Manager state |
| 11 | Aynı anda geçiş + duraklat | Sıralı ejecyon |
| 12 | Dosya sonu hatası | Geçiş iptali |

## 7. Hata Modları

| # | Belirti | Kök Neden | Klasör |
|---|---|---|---|
| 1 | Parçalar arası boşluk | Pre-decode geç | `[[playback-gapless]]` |
| 2 | Tıklama | Sert kenar | `[[playback-gapless]]` |
| 3 | CPU sıçraması | 2× işleme | `[[playback-gapless]]` |
| 4 | Bellek artışı | Çift tampon | `[[../k077-stream-buffer/index]]` |
| 5 | Kalıcı efekt | State temizliği yok | `[[../k083-effects-reverb/index]]` |
| 6 | Yarış | Eşzamanlı çağrı | `[[playback-gapless]]` |
| 7 | Bozuk decode | Dosya hatası | `[[../k081-format-decoder/index]]` |
| 8 | Underrun | Yetersiz doldurma | `[[../k077-stream-buffer/index]]` |
| 9 | Hız uyuşmazlığı | Farklı örnekleme | `[[../k075-sample-rate-conversion/index]]` |
| 10 | Ölçüm belirsiz | Süre kanıtsız | `[[playback-gapless]]` |

## 8. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | K3.5 yaprak | 27 | `README.md` §Alt Katman Şeması |
| 7 | Geçiş metrikleri | `playback-gapless.md` §Performans Metrikleri | Kaynak |
| 8 | Crossfade süresi | ⚠️ VERIFICATION REQUIRED | Kanıt yok |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 9. Test ve Doğrulama Stratejisi

| # | Test | Kabul |
|---|---|---|
| 1 | Gapless kesintisizlik | Boşluk ≈ 0 (eşik ⚠️) |
| 2 | Crossfade eğrisi | Beklenen kazanç |
| 3 | CPU açık/kapalı | Fark ölçümlü ⚠️ |
| 4 | Pre-decode zamanlama | Underrun yok |
| 5 | Kısa parça | Kısaltma |
| 6 | Hız farklılığı | Yeniden plan |
| 7 | Reverb tail | Politikaya uyar |
| 8 | Compressor release | Yeni state |
| 9 | Yarış testi | Yarış yok |
| 10 | Dosya sonu hatası | Tanımlı iptal |
| 11 | 128 kanal / 384 kHz | Kabul |
| 12 | Soak | Bellek sabit |

## 10. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Crossfade süresi/eğrileri kanıtsız | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Efekt state politikası kaynakta yok | Orta | `[[playback-gapless]]` §7 |
| 3 | Ölçüm kanıtı yok | Yüksek | §8 |
| 4 | Ayrı ADR yok | Orta | ⚠️ VERIFICATION REQUIRED |
| 5 | Liste politikası kapsam dışı | Düşük | §2.2 |

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

### Kaynak: `README.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/README.md` (35 satır · 449-483. satırlar) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)

| K3.5.b.2 | Teknik Detaylar | playback-gapless.md · L14 |
| K3.5.b.3 | Gapless Playback Yapısı | playback-gapless.md · L16 |
| K3.5.b.4 | Pre-decode Mekanizması | playback-gapless.md · L32 |
| K3.5.b.5 | Crossfade Implementasyonu | playback-gapless.md · L88 |
| K3.5.b.6 | Gapless Playback Manager | playback-gapless.md · L152 |
| K3.5.b.7 | API / Arayüz | playback-gapless.md · L314 |
| K3.5.b.8 | Performans Metrikleri | playback-gapless.md · L359 |
| K3.5.b.9 | Bağımlılıklar | playback-gapless.md · L369 |
| **K3.5.c** | **Stream Tampon** | stream-buffer.md · L10–L364 |
| K3.5.c.1 | Genel Bakış | stream-buffer.md · L10 |
| K3.5.c.2 | Teknik Detaylar | stream-buffer.md · L14 |
| K3.5.c.3 | Jitter Buffer Yapısı | stream-buffer.md · L16 |
| K3.5.c.4 | Adaptif Jitter Buffer | stream-buffer.md · L32 |
| K3.5.c.5 | Network Stream Handler | stream-buffer.md · L185 |
| K3.5.c.6 | Buffer Pool | stream-buffer.md · L252 |
| K3.5.c.7 | API / Arayüz | stream-buffer.md · L315 |
| K3.5.c.8 | Performans Metrikleri | stream-buffer.md · L354 |
| K3.5.c.9 | Bağımlılıklar | stream-buffer.md · L364 |

###### K3.6 — Format & Dönüşüm

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.6.a** | **Codec Çözücüleri** | format-decoder.md · L10–L368 |
| K3.6.a.1 | Genel Bakış | format-decoder.md · L10 |
| K3.6.a.2 | Teknik Detaylar | format-decoder.md · L14 |
| K3.6.a.3 | Format Karşılaştırması | format-decoder.md · L16 |
| K3.6.a.4 | FLAC Decoder | format-decoder.md · L32 |
| K3.6.a.5 | MP3 Decoder | format-decoder.md · L146 |
| K3.6.a.6 | AAC Decoder | format-decoder.md · L199 |
| K3.6.a.7 | DSD Decoder | format-decoder.md · L241 |
| K3.6.a.8 | Format Otomatik Algılama | format-decoder.md · L282 |
| K3.6.a.9 | API / Arayüz | format-decoder.md · L315 |
| K3.6.a.10 | Performans Metrikleri | format-decoder.md · L360 |
| K3.6.a.11 | Bağımlılıklar | format-decoder.md · L368 |

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
| 1 | Gapless/pre-decode/crossfade/manager | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/playback-gapless.md` (383 satır) | Kanıtlı |
| 2 | K3.5 (27 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 3 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` | Kanıtlı (hedef) |
| 4 | RT kuralları | `_backup/.../k3-ses-motoru/CLAUDE.md` §1 | Kanıtlı |
| 5 | Crossfade süresi, efekt state politikası | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/playback-gapless.md` +
`index.md` + `README.md` §Alt Katman K3.5 + `CLAUDE.md`.
