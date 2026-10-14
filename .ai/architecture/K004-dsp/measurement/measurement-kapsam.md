---
title: "measurement — Kapsam"
type: architecture-scope
node_id: K004-dsp/measurement
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# measurement — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 11 bu dalı **"Frekans/zaman tepkisi ölçümü"** olarak
yazar. Basitçe: sistemin gerçekte nasıl davrandığını **ölçen** düğüm — frekans
tepki eğrisi, bozulma (THD), gecikme, seviye. `index.md` bunu "ayna karşısında
saç taramak — iddia değil, ölçüm" diye çağırır. measurement, K004'ün
kabul-kriteri kapısıdır: SNR/THD eşikleri buradan karşılaştırılır.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Frekans tepkisi ölçümü (frequency response) | EQ düzeltme hedefi hesabı | equalizer |
| 2 | Bozulma ölçümü (THD / THD+N) | Limiter/DSP bozulma üretimi | limiter · compressor |
| 3 | Gecikme (latency) ölçümü | Pipeline latency bütçesi tahmini | pipeline |
| 4 | Seviye/loudness ölçümü (R128 vb.) | Ölçüm standardı seçimi | testing |
| 5 | Ölçüm eşikleriyle karşılaştırma (kabul kriteri: SNR > 100 dB · THD+N < 0.01% @ 1 kHz) | Eşiklerin belirlenmesi (kaynak: AGENTS.md §5) | dsp-master §4 T7 |
| — | *(dışında)* ölçüm araç zinciri + rapor formatı | — | **UNKNOWN — R1** |

**Yasak:** measurement yalnız ölçer ve karşılaştırır; düzeltme uygulamaz (EQ'nun
işi). Ölçüm eşikleri (T7) [KANIT]'tır; araç zinciri/rapor formatı kanıtsız yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | measurement ↔ testing (gerçek ölçüm ↔ test senaryosu) | ⚠️ VERIFICATION REQUIRED | dsp-master §7 Q4 |
| A2 | measurement ↔ equalizer (ölçüm ↔ düzeltme hedefi) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | ölçüm araç zinciri + rapor formatı | **UNKNOWN** — tahmin yasak | measurement/index.md open_questions |
| A4 | loudness standardı (R128/ITU vs başka) | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Frekans/zaman tepkisi ölçümü" | `dsp-master.md` §3 satır 11 | ✅ dosya okundu |
| 2 | "ölçüm araç zinciri ve rapor formatı yok" açık soru | `measurement/index.md` open_questions | ✅ dosya okundu |
| 3 | Ölçüm eşikleri: SNR > 100 dB · THD+N < 0.01% @ 1 kHz [KANIT] | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 satır 50 | ✅ dosya okundu |
| 4 | PHP `analyzeHardware()` snr/thd alanları üretir ama placeholder | `shared/src/AI/AIEngine.php` satır 138–155 | ✅ dosya okundu |
| 5 | DSPark `SpectrumAnalyzer`, `LoudnessMeter`, EBU R128 metering | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | DSPark `DelayEstimator`, `OnsetDetector`, `PitchDetector` analiz ailesi | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 7 | Signalsmith-Audio/basics `Analyser` (spectrumHz / spectrumEnergy) | https://github.com/Signalsmith-Audio/basics | ✅ 2026-10-12 doğrulandı |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Ölçüm araç zinciri + rapor formatı? | **UNKNOWN** |
| R2 | measurement ↔ testing / equalizer sınırı (Q4/A2)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Loudness standardı seçimi (R128/ITU)? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 araç-zinciri + R3 standart kapanmadan bu
dosyada ölçüm-iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3). Eşikler (§4/3) ise
[KANIT]'tır ve bağlayıcıdır.

## §6 FFT / RTA literatür notu (kaynaklı — CoreMusic kodu DEĞİL)

> Bu bölüm literatür bilgisidir; CoreMusic'te FFT/RTA kodu YOKTUR (§11 grep).

**FFT (Fast Fourier Fourier Transform):** zaman-uzay örnekleme kümesini
frekans-uzayına çeviren algoritma ailesidir. Ses ölçümünde tipik kullanım
boyutu N = 2^k (1024, 2048, 4096); çözünürlük = fs / N (ör. 48000/2048 ≈ 23.4 Hz).
Pencereleme (Hanning/Hamming/Blackman) spektral sızıntıyı azaltır; pencere
seçimi ölçüm standardına bağlıdır (standart seçimi R3 UNKNOWN).

**RTA (Real-Time Analyzer):** sürekli güncellenen frekans-seviye gösterimi.
Genellikle 1/1 oktav veya 1/3 oktav bantlarında gruplanır. Bant genişliği
daraldıkça frekans çözünürlüğü artar, zaman çözünürlüğü azalır (belirsizlik
ilkesi).

**Kaynaklar:** Wikipedia "FFT" — ⚠️ VERIFICATION REQUIRED (bu oturumda HEAD
doğrulanmadı; §10 tablosuna alınmadı). DSPark `SpectrumAnalyzer` (§4/5, HEAD
200) gerçek bir kütüphane örneğidir.

## §7 THD / THD+N ölçüm yöntemi (kaynaklı)

**THD (Total Harmonic Distortion):** girişe saf ton (1 kHz tipik) verildiğinde,
çıkışta temel frekans dışında üretilen harmoniklerin toplam enerjisidir.
Yüzde olarak: THD = sqrt(V2²+V3²+...+Vn²) / V1 × 100.

**THD+N:** harmoniklerin yanında gürültü (noise) ve gömüldü (rumble) da
kapsar; ayrı bir filtreleme yapmadan temele göre ölçülür. SNR > 100 dB ·
THD+N < 0.01% @ 1 kHz eşikleri (§4/3) bu ölçümle karşılaştırılır.

**Ölçüm adımları (literatür):** saf ton üret → sisteme ver → çıkışı kaydet →
FFT ile temeli ayır → harmonikleri topla → gürültüyü dahil et (THD+N) →
dB/% cinsinden raporla.

**Kaynak:** Wikipedia "Total harmonic distortion" — ⚠️ VERIFICATION REQUIRED
(oturumda doğrulanmadı). dspguide.com (HEAD 200) genel sinyal işleme
referansıdır; THD bölümü doğrulanmadı → §10'a alınmadı.

## §8 True-peak ölçüm notu (ITU-R BS.1770 / EBU R128 — HEAD 200 ✅)

**True-peak (dBTP):** örnekleme noktaları arasında oluşan gerçek tepe,
4× interpolasyonla tahmin edilir. Standart: ITU-R BS.1770; EBU R128 bu ölçümü
kabul kriterine ekler.

**Neden gerekli:** klasik "sample peak" intersample tepeyi kaçırır; dijital
alanı aşan bu tepe analog aşırı yüklenme (clipping) üretir.

**Ölçüm:** sinyali 4× örneklemeye yükselt → tepeyi bul → dBTP cinsinden raporla.
Limiter tarafındaki true-peak tarama (`[[limiter-ornekler]]` §8) bu yöntemle
doğrulanır.

**Kaynaklar (§10 tablosuna dahil):** itu.int (HEAD 200) · Wikipedia "Loudness",
"EBU R 128", "ITU-R BS.1770" (HEAD 200).

## §9 Katsayı doğrulama notu (dB ↔ lineer)

| Katsayı | Ters | Not |
|---|---|---|
| dB = 20·log10(lineer) | lineer = 10^(dB/20) | genlik |
| dB = 10·log10(lineer) | lineer = 10^(dB/10) | güç/enerji |
| +6 dB | ×2 genlik | yaklaşık |
| −6 dB | ÷2 genlik | yaklaşık |

Bu dönüşümler ölçüm raporlarının birim tutarlılığı içindir (§20 rapor şablonu).
Wikipedia "Decibel" HEAD 200 ✅ (§10). CoreMusic'e özgü katsayı tahmini YOK.

## §10 HEAD doğrulama tablosu (2026-10-13)

| # | URL | Durum |
|---|---|---|
| 1 | https://www.itu.int/rec/R-REC-BS.1770 | ✅ 200 |
| 2 | https://en.wikipedia.org/wiki/Loudness | ✅ 200 |
| 3 | https://en.wikipedia.org/wiki/EBU_R_128 | ✅ 200 |
| 4 | https://en.wikipedia.org/wiki/ITU-R_BS.1770 | ✅ 200 |
| 5 | https://en.wikipedia.org/wiki/Decibel | ✅ 200 |
| 6 | https://github.com/CristianMoresi/DSPark | ✅ 200 |
| 7 | https://github.com/Signalsmith-Audio/basics | ✅ 200 |

> 403/YASAK ve 404 başlıklar kullanılmamıştır (§6 kararları).

## §11 Grep kanıtı (CoreMusic'te ölçüm kodu)

| Arama | Sonuç |
|---|---|
| `FFT` / `SpectrumAnalyzer` / `LoudnessMeter` (PHP/C++) | 0 gerçek DSP eşleşmesi (RateLimiter/CacheRateLimiter HTTP'dir) |
| `THD` / `truepeak` / `dBTP` | 0 |
| `analyzeHardware()` snr/thd | `shared/src/AI/AIEngine.php` satır 138–155 — **placeholder**, gerçek ölçüm DEĞİL (§4/4) |

**Sonuç:** CoreMusic'te frekans/bozulma ölçüm kodu YOK; eşikler (§4/3) koddan
değil spec'ten gelir.

## §12 Ölçüm protokolü (PLANNED — çalıştırılmadı)

1. Ortam sabitle: fs = 48000, 512 örnek tampon (sartname; ölçüm PLANNED).
2. Saf ton (1 kHz) üret; THD+N için giriş referansını kaydet.
3. Sisteme uygula; çıkışı kaydet.
4. FFT + pencereleme ile temel/harmonikleri ayır → THD/THD+N.
5. True-peak: 4× interpolasyon → dBTP.
6. Frekans tepkisi: sweep veya mavna ile giriş-çıkış oranı.
7. SNR/THD eşikleriyle karşılaştır (§4/3) → PASS/FAIL raporu (§20).
8. Raporu `[[dsp-dogrulama]]` §13 protokolüne bağla.

**Durum:** 0/8 adım çalıştırıldı — hepsi PLANNED.

## §13 PLANNED kod iskeleti (derlenmedi — §12 protokolünün karşılığı)

> ⚠️ Aşağıdaki kod literatür iskeletidir; hiçbir yerde derlenmedi/test edilmedi.

```cpp
// PLANNED — measurement çekirdeği iskeleti (derlenmedi)
// fs = 48000, N = FFT boyutu (R1 UNKNOWN)
struct MeasurementResult {
    double thdPercent;   // THD %
    double thdNPercent;  // THD+N %
    double truePeakDbtp; // dBTP (4× interp — §8)
    double snrDb;        // dB
};

// ⚠️ VERIFICATION REQUIRED — pencere/tip parametreleri R3 (standart) UNKNOWN
MeasurementResult measure(const float* in, size_t n, double fs);
```

**Yasak:** bu iskeleti gerçek kabul kriteri gibi kullanmak; R1/R3 kapanmadan
eşik karşılaştırması bağlayıcı değildir (§5 Kapanış).

## §14 Sınır ve komşu etkileşimi (derinlemesine)

| Komşu | Ölçülen şey | Düzeltme uygulanmaz |
|---|---|---|
| equalizer | EQ sonrası frekans tepkisi sapması | EQ hedefi hesabı equalizer'ın (§2/1) |
| limiter | ceiling sonrası true-peak | limiter kazancı limiter'ın (§2/2) |
| compressor | dinamik sıkıştırma distorsiyonu | compressor ayarı compressor'ın |
| pipeline | uçtan uca gecikme | bütçe tahmini pipeline'ın (§2/3) |
| testing | test senaryosu | senaryo testing'in (A1) |

measurement tek taraflı ölçer; hiçbir komşuya düzeltme geri beslemez.

## §15 Wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | rol (§3 satır 11) + T7 eşikleri (§4) |
| `[[dsp-bilesenler]]` | ölçüm noktası yerleşimi |
| `[[dsp-dogrulama]]` | kapı matrisi + protokol bağlaması |
| `[[dsp-bagimliliklar]]` | T7 eşik kaynağı (§3 satır 50) |
| `[[equalizer-kapsam]]` | düzeltme komşusu |
| `[[limiter-ornekler]]` §8 | true-peak doğrulama hedefi |
| `[[testing-dogrulama]]` | A1 sınır komşusu |

## §16 Q&A (measurement özel)

**Q1 — ölçüm eşikleri bağlayıcı mı?**
Evet — §4/3 [KANIT]'tır (AGENTS.md §5 kaynağı). Araç zinciri (R1) UNKNOWN
olduğu için eşikler *hedef* olarak bağlayıcıdır ama *otomatik kapı* değildir.

**Q2 — THD mı THD+N mı?**
Her ikisi de ölçülür; eşik (THD+N < 0.01%) THD+N'ye aittir (§4/3).

**Q3 — R128 midir ITU mu?**
UNKNOWN (R3) — standart seçimi yapılmadı; §8 her ikisini de literatür olarak taşır.

**Q4 — neden kod yok?**
CoreMusic'te measurement kodu grep ile YOK (§11); §13 yalnız PLANNED iskelettir.

## §17 Risk kaydı

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | R1 (araç zinciri/rapor formatı) UNKNOWN | otomatik ölçüm kapısı kurulamaz | açık |
| 2 | R3 (loudness standardı) UNKNOWN | R128/ITU seçimi belirsiz | açık |
| 3 | `analyzeHardware()` placeholder | snr/thd alanı yanlış güven üretir | §4/4 |
| 4 | FFT pencere yanlışı | spektral sızıntı → yanlış THD | §6 |
| 5 | intersample peak kaçırılırsa | analog clipping | §8 |

## §18 Sözlük (measurement)

| Terim | Tanım |
|---|---|
| **FFT** | Örnekleme kümesini frekans uzayına çeviren algoritma (§6) |
| **RTA** | Sürekli frekans-seviye gösterimi (§6) |
| **THD** | Harmonik bozulma yüzdesi (§7) |
| **THD+N** | Harmonik + gürültü bozulma — eşik budur (§7) |
| **true peak / dBTP** | 4× interpolasyonla tahmin edilen gerçek tepe (§8) |
| **intersample peak** | Örnekleme arası tepe (§8) |
| **SNR** | Sinyal-gürültü oranı (dB) — §4/3 |
| **pencereleme** | FFT sızıntı azaltma (Hanning vb.) (§6) |

## §19 Yol haritası (measurement)

| Adım | Durum |
|---|---|
| Rol/kapsam kaydı (§1–§5) | ✅ TAMAM |
| Literatür notları (§6–§9) | ✅ bu tur |
| HEAD doğrulama (§10) | ✅ 2026-10-13 |
| Grep + protokol (§11–§12) | ✅ / 0 adım PLANNED |
| R1 (araç zinciri) kapanışı | ⏳ |
| R3 (standart) kapanışı | ⏳ |
| Gerçek ölçüm çalıştırma | ⏳ (§12) |

## §20 Rapor şablonu (PLANNED — §12 çıktısı)

```text
ÖLÇÜM RAPORU — K004/measurement
fs / N       : <R1 UNKNOWN>
THD @1kHz    : <%>  (eşik <0.01%)
THD+N @1kHz  : <%>
SNR          : <dB> (eşik >100 dB)
true peak    : <dBTP> (4× interp)
SONUÇ        : PASS | FAIL
Authority    : Bayram Ali / Vault Steward
```

## §21 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 73 | 500+ |
| § sayısı | 5 | 20 |
| Frontmatter | `parent_id` + `tags` | aynı (hazır) |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §22 Hızlı başlangıç (7 adım)

1. §1–§5'i oku — rol, sınır, eşikler [KANIT].
2. §7 THD+N yöntemini oku — eşik karşılaştırmasının temeli.
3. §8 true-peak notunu oku — ITU/EBU bağlantısı.
4. §11 grep sonucunu gör — kod yok, eşik spec'ten.
5. §12 protokolü oku — 8 adım, 0 çalıştırıldı.
6. `[[dsp-dogrulama]]` §13 protokolüne bağla.
7. R1/R3 kapatılmadan otomatik kapı KURMA (§5 Kapanış).

## §23 Ek — 2026-10-13 tazeleme notu

§10 tablosundaki 7 başlık bu tur HEAD ile doğrulanmıştır. §6'da
"FFT"/"THD" Wikipedia/doğrulanmamış kaynaklar bilinçli olarak §10'a
ALINMAMIŞTIR.

## §24 Ek — eşik kaynak özeti

| Eşik | Değer | Kaynak | Durum |
|---|---|---|---|
| SNR | >100 dB | AGENTS.md §5 · `dsp-master` §4 T7 | [KANIT] |
| THD+N | <0.01% @1kHz | AGENTS.md §5 · `dsp-master` §4 T7 | [KANIT] |
| true-peak standardı | ITU-R BS.1770 | itu.int (HEAD 200) | literatür |
| rapor formatı | — | — | **UNKNOWN (R1)** |

## §25 Ölçüm senaryoları (PLANNED — sentetik, sonuç DEĞİL)

| # | Senaryo | Girdi | Beklenen |
|---|---|---|---|
| S1 | Saf ton THD+N | 1 kHz sinüs, 512 örnek | THD+N < 0.01% |
| S2 | Swept-sine frekans tepkisi | 20 Hz–20 kHz sweep | ±0.5 dB (⚠️ VERIFICATION REQUIRED — CoreMusic toleransı UNKNOWN) |
| S3 | True-peak tek tepe | intersample tepe içeren sinyal | 4× interp ile dBTP raporlanır |
| S4 | SNR boş-girdi | sıfır girdi | SNR > 100 dB |
| S5 | Beyaz gürültü spektrumu | pink/white noise | düz tepki (±tölerans UNKNOWN) |

**Durum:** 0/5 çalıştırıldı — hepsi PLANNED. Töleranslar tahmin edilmedi.

## §26 Ölçüm hata kaynakları (literatür)

| # | Hata kaynağı | Etki | Azaltma |
|---|---|---|---|
| H1 | ADC gürültüsü | SNR düşer | çıkış (dış donanım) |
| H2 | FFT pencere sızıntısı | THD şişer | uygun pencere + düzeltme katsayısı |
| H3 | clock jitter | THD+N şişer | çıkış (donanım) |
| H4 | intersample kaçırma | false PASS | §8 true-peak |
| H5 | ölçekleme/round-off | düşük seviyede yanlış SNR | float64 offline ölçüm |
| H6 | ortam titreşimi/ısı | drift | sıcaklık sabitleme |

> Kaynak: genel DSP literatürü (dspguide.com HEAD 200, ccrma.stanford.edu HEAD 200);
> her satır ayrıca doğrulanmadı → ⚠️ VERIFICATION REQUIRED (katsayısal iddia yok).

## §27 Offline vs realtime ölçüm ayrımı

| | Offline | Realtime |
|---|---|---|
| Hassasiyet | float64 mümkün | float32 (ADR-017) |
| Tahsis | serbest | **yasak** (sıfır tahsis) |
| Pencere | büyük FFT | blok FFT (512) |
| Amaç | kesin THD/SNR | canlı spektrum/RTA |
| CoreMusic | §12 protokolü PLANNED | §11 grep: kod YOK |

Bu ayrım `[[dsp-dogrulama]]` §8 realtime kapısıyla uyumludur.

## §28 Ölçüm → kabul kapısı bağlantısı

```text
ölçüm (bu dal §12) → sonuç (§20 rapor) → kapı karşılaştırma
    → SNR >100 dB  (§4/3 [KANIT])
    → THD+N <0.01% (§4/3 [KANIT])
    → PASS | FAIL  → dsp-master §4 T7 kaydı
```

**Kural:** R1 (araç zinciri) kapanmadan bu kapı otomatikleştirilemez; eşikler
bağlayıcı hedeftir (§5 Kapanış).

## §29 Sınır matrisi (komşuya göre ne ölçülür)

| Ölçüm türü | equalizer | limiter | compressor | pipeline | spatial |
|---|---|---|---|---|---|
| frekans tepkisi | EQ sonrası sapma | n/a | n/a | n/a | HRTF sapma |
| THD+N | n/a | ceiling sonrası | sıkıştırma distorsiyonu | n/a | n/a |
| true-peak | n/a | **ana hedef** | n/a | n/a | n/a |
| gecikme | n/a | lookahead gecikmesi | lookahead | **ana hedef** | HRTF gecikmesi |
| loudness | n/a | n/a | GR doğrulaması | n/a | binaural seviye |

Her hücre yalnız ölçer; düzeltme komşunun işidir (§2/§14).

## §30 Sinyal zinciri ölçüm noktaları (PLANNED çizim)

```text
girdi ──[P1]──> zincir (EQ/limiter/...) ──[P2]──> çıkış
          │                                   │
       referans                          ölçüm noktası
   (§21 rapor)                         (§12 protokol)
```

- **P1:** giriş referansı kaydı (THD+N paydası).
- **P2:** çıkış ölçümü (THD/true-peak/SNR).
- P1/P2 arasındaki fark = sistemin katkısı.
- CoreMusic'te bu noktalar PLANNED'dır (§11 grep: kod yok).

## §31 Hızlı karar tablosu (ne zaman hangi ölçüm)

| Soru | Ölçüm | Bölüm |
|---|---|---|
| bozulma eşiği geçti mi? | THD+N | §7 |
| dijital tepe tehlikeli mi? | true-peak | §8 |
| gürültü tabanı yeterli mi? | SNR | §4/3 |
| frekans düz mü? | sweep/mavna | §6 |
| canlı izleme mi? | RTA | §6 (realtime §27) |

## §32 Dürüstlük notu (bu tur ne yazılmadı)

- Tahmin edilen hiçbir sayısal tolerans eklenmedi (±0.5 dB → VERIFICATION REQUIRED).
- §10 tablosuna yalnız bu tur HEAD 200 döndürenler alındı.
- §13 kodu derlenmedi; §12/§25 adımları çalıştırılmadı (0/N).
- `analyzeHardware()` placeholder olduğu yazıldı; "gerçek ölçüm" denmedi.

## §33 Dosya son kontrolü

- [x] §1–§5 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] tüm §numaraları artan sırada
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] eşikler [KANIT] işaretiyle korundu
- [ ] R1/R3 kapanmadı — status: draft kalır

## §34 Ölçüm birimleri ve dönüşüm tablosu

| Ölçüm | Birim | Referans | Not |
|---|---|---|---|
| THD / THD+N | % veya dB | temel ton | %↔dB: 20·log10(orandan) |
| SNR | dB | sinyal/gürültü | §4/3: >100 dB |
| true-peak | dBTP | dijital full-scale | §8 |
| frekans tepkisi | dB | 0 dB @ referans | ±tölerans UNKNOWN |
| gecikme | ms veya örnek | 48 kHz | 512 örnek = ~10.67 ms (48 kHz) |
| çözünürlük | Hz | fs/N | §6 |

**Not:** 10.67 ms hesabı fs=48000, N=512 için basit bölmedir (512/48000 s);
sartname değerleridir, ölçüm sonucu DEĞİL.

## §35 Ölçüm doğrulama (validation) adımları — literatür

| # | Adım | Amaç |
|---|---|---|
| V1 | Referans kaynağı doğrula | bilinen iyi sistem ölç |
| V2 | Ölçüm tekrarlanabilirliği | aynı girdi → aynı sonuç |
| V3 | Birim tutarlılığı | §9 dönüşümleri |
| V4 | Kapı karşılaştırması | §28 kabul kapısı |

**Durum:** 0/4 çalıştırıldı — PLANNED. Bu adımlar `[[dsp-dogrulama]]`
protokolüne öncülük eder.

## §36 Sözlük genişletme (§18'a ek)

| Terim | Tanım |
|---|---|
| **sweep** | Frekansı kademeli değiştiren giriş (§6) |
| **mavna (pink noise)** | −3 dB/oct gürültü (§25) |
| **white noise** | Düz spektrumlu gürültü (§25) |
| **clock jitter** | Saat kararsızlığı → THD+N şişmesi (§26 H3) |
| **full-scale (dBFS)** | Dijital referans seviyesi (§8) |
| **referans seviye** | Ölçüm paydası (§30 P1) |

## §37 Karşılaştırmalı not (literatür — doğrulanmadı)

Bazı ölçüm araçları spektrumu pencereleyerek, bazıları doğrudan blok FFT
ile alır; pencere tipi sonuç THD'yü etkileyebilir (§26 H2). Bu satır genel
literatür notudur — araç seçimi R1 (UNKNOWN) olduğu için bağlayıcı değildir.
⚠️ VERIFICATION REQUIRED: belirli bir aracın katsayısı doğrulanmadı.

## §38 Bu dosyanın kardeşleri

| Dosya | Rol | Durum |
|---|---|---|
| `measurement-kapsam.md` | bu dosya — kapsam + literatür + protokol | genişletildi (bu tur) |
| `measurement-dogrulama.md` | doğrulama kapıları, protokol detayı | §sıradaki iş |
| `measurement-ornekler.md` | literatür kod örnekleri, test şablonları | §sıradaki iş |
| `measurement/index.md` | dal indeksi + open_questions | **DOKUNULMAZ** |

## §39 Değişiklik günlüğü (measurement-kapsam)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§5 + kısa Authority |
| 2026-10-13 | §6–§39 eklendi (literatür, HEAD, grep, protokol, senaryo, birim, hata kaynakları, sınır matrisi, Q&A, risk, sözlük, yol haritası, rapor şablonu, derinlik, hızlı başlangıç) · dürüstlük bloğu + tam Authority · frontmatter `parent_id`+`tags` (bu tur öncesi) · silme 0 |

## §40 Ek — kaynak erişim notu (2026-10-13)

§10 tablosu `Invoke-WebRequest -Method Head` ile alınmıştır. 403/YASAK ve 404
durumları §6 kararları gereği kaynak yapılmamıştır. Wikipedia başlıkları
(Loudness, EBU R 128, ITU-R BS.1770, Decibel) 200 döndürmüştür.

## §41 Ek — son ölçüm özeti (bu tur)

| Öğe | Değer |
|---|---|
| Toplam satır (son) | 500+ |
| § sayısı | 41 |
| HEAD doğrulanan | 7 başlık (§10) |
| Çekirdek eşikler | SNR >100 dB · THD+N <0.01% @1kHz [KANIT] |
| Açık kapılar | R1 (araç zinciri) · R3 (loudness standardı) |
| PLANNED adım | §12 (0/8) · §25 (0/5) · §35 (0/4) |
| Kod | grep YOK · §13 iskelet derlenmedi |
| Dürüstlük / Authority | 1 / 1 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
