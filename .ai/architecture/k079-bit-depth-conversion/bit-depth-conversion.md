---
title: "Bit Derinliği Dönüştürme — 16/24/32-bit PCM, Dither, Yuvarlama ve Bit-Hassas Yol"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# Bit Derinliği Dönüştürme (K079)

> K079 konu belgesi. Kapsam ve belge listesi: [[index]].
> Kapsam: `architecture/k3-ses-motoru/bit-depth-conversion` — 16/24/32-bit PCM
> dönüştürme matematiği, dither, yuvarlama stratejisi ve bit-hassas sinyal yolu.

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


## 2. Bağlam (Context)

Dijital ses, sonsuz çözünürlüklü bir dalga formunu sonlu sayıda adıma
kuantalar. Çözünürlük (bit derinliği) düştükçe kuantalama hatası büyür ve bu
hata sinyale bağımlıdır — sessizlikte duyulur distorsiyon (kmyfile ve "
titreme") üretir. Dönüştürme katmanı, kaynak ve hedef derinlik farklı
olduğunda bu hatayı yönetir: ya gürültü ekleyerek hatayı yayarak (dither) ya
da doğrudan yuvarlayarak. Üç temel kullanım: (1) iç işlemin 32-bit float
olması, çıkışın 16/24-bit olması; (2) 24-bit kaydın 16-bit'e indirgenmesi;
(3) farklı derinlikteki parçaların birleştirilmesi. Bit-hassas yol, dönüşümü
hiç yapmayan ve sinyali bozmadan geçiren özel bir moddur.

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



## 4. Tasarım Kararları

| # | Karar | Gerekçe |
|---|---|---|
| 1 | İç DSP 32-bit float üzerinde çalışır | Headroom ve yuvarlama birikimi riskini ortadan kaldırır |
| 2 | Çıkışta tek seferde kuantalama | Çok adımlı kuantalama birikimli hata üretir |
| 3 | Dither stratejisi | Hatayı yaymak için (tip: `⚠️ VERIFICATION REQUIRED`) |
| 4 | Bit-hassas mod | Dönüştürme olmadan geçiş (donanım/kyz kontrol için) |

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


## 6. Algoritma ve Matematik

**Yalın yuvarlama (dithersiz):**

```
hedef = round(kaynak / kuantalama_adımı) * kuantalama_adımı
16-bit: adım = 1/32768, aralık = [-1, +1)
24-bit: adım = 1/8388608
```

**Dither eklenmiş kuantalama:**

```
çıkış = round((girdi + dither_gürültüsü) / adım) * adım
dither_gürültüsü: [-adım/2, +adım/2] aralığında rastgele (tip: ⚠️ VERIFICATION REQUIRED)
```

**Dönüşüm çarpanı (bit kaydırma ile):**

```
16→24-bit: çarpan = 2^8 = 256 (soldan bit kaydırma)
24→16-bit: bölen = 2^8 = 256 (sağa kaydırma + yuvarlama)
```

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


## 8. Kalite Ölçütleri (Mevcut Kanıt)

| Ölçüt | Değer | Kaynak |
|---|---|---|
| THD+N değişimi (dithersiz) | `⚠️ VERIFICATION REQUIRED` | Ölçüm yok |
| Dither gürültüsü seviyesi | `⚠️ VERIFICATION REQUIRED` | Ölçüm yok |
| Bit-hassas doğruluk | `⚠️ VERIFICATION REQUIRED` | Kanıt yok |

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


## 10. Riskler ve Performans

| Risk/Ölçüt | Durum |
|---|---|
| Dither tipi ve seviyesi bilinmiyor | `⚠️ VERIFICATION REQUIRED` |
| Yuvarlama modu bilinmiyor | `⚠️ VERIFICATION REQUIRED` |
| Bit-hassas yolun gerçekten hassas olduğu | Doğrulanmadı → `⚠️ VERIFICATION REQUIRED` |
| Dönüştürme CPU maliyeti | Hedef var, ölçüm yok → `⚠️ VERIFICATION REQUIRED` |
| 32-bit float'ın [−1, +1) dışına taşması | Sınır bilgisi `⚠️ VERIFICATION REQUIRED` |

## 11. Karar Kayıt Bağlantıları

- **ADR-025 — 31-Band Parametrik EQ**: kuantalama derinliği (EQ merkez
  frekansları / kazanç adımları) için bağlam. `Kanıt: .ai/adr/ADR-025-*.md`.
- **İlişkili klasör:** dönüştürme zinciri sonrası bit derinliği kararı →
  [[../k075-sample-rate-conversion/sample-rate-conversion]] (SR dönüşümünden
  sonra kuantalama sırası `⚠️ VERIFICATION REQUIRED`).

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


## 13. Kaynak ve Doğrulama

- **Kaynak:** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/bit-depth-conversion` (harici içerik `<!--KAYNAK-->` ile yerine yazıldı).
- **Süreç:** WORKFLOW §2.1 D02 · K079 · durum active.
- **Zero-Hallucination:** Uydurulan değer yok; doğrulanamayanlar
  `⚠️ VERIFICATION REQUIRED` ile işaretli.

## Kanıt:

- `Kanıt: .ai/architecture/k079-bit-depth-conversion/bit-depth-conversion.md (bu dosya) — verbatim bloklar k3-ses-motoru/README.md |27-42, |309-332, |449-483, |484-521, |623-664; işaretleme satırları kendi hükmüdür, kaynakla çelişen sayısal iddia yok.`
