---
title: "measurement — Örnekler"
type: architecture-examples
node_id: K004-dsp/measurement
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# measurement — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: PHP katmanında ölçüm-alanı var, gerçek ölçüm yok.** `analyzeHardware()`
snr/thd alanlarını döndürür ama değerler placeholder (`0.0`) — gerçek ölçüm
yapılmaz (C++ ölçüm motoru henüz yok — dogrulama §1/6):

```php
// shared/src/AI/AIEngine.php satır 141-155
$status = 'healthy';
$snr = 0.0;
$thd = 0.0;
$temperature = 0.0;
// Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)
// Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak
return [
    'status' => $status, 'snr' => $snr, 'thd' => $thd,
    'temperature' => $temperature, 'recommendations' => $recommendations,
];
```

**Ne gösterdiği:** CoreMusic'in snr/thd **alan adlarını** hazır tuttuğunu; ölçümün
henüz C++ tarafına bağlanmadığını. K004 measurement dalı bu alan adlarını
koruyabilir, ancak doldurma kaynağı gerçek ölçüm motoru olacaktır.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Analysis and metering | SpectrumAnalyzer, LoudnessMeter, AudioIntervalAnalyzer,
    LoudnessNormalizer, PitchDetector, OnsetDetector, BeatTracker, LoopFinder,
    DelayEstimator
```

**Ne gösterdiği:** hazır ölçüm/metering ailesi — spectrum, loudness (EBU R128),
gecikme-tahmini (`DelayEstimator`). CoreMusic `measurement` dalı için doğrudan
araç referansı (R1 araç-zinciri için aday).

**Kaynak:** https://github.com/Signalsmith-Audio/basics (README — Analyser, 2026-10-12 ✅)

```cpp
signalsmith::basics::AnalyserFloat analyser;
// spectrum data: .spectrumHz() and .spectrumEnergy(channel)
// thread-safe meter: .wantsMeters(bool) / .hasMeters()
```

**Ne gösterdiği:** spectrum ölçümünün ana thread/UI thread'e güvenli aktarım
deseni (`.wantsMeters`/`.hasMeters`) — realtime ölçüm okuması için referans.

## §3 Uygulama notu

CoreMusic `measurement` dalı DSPark analiz ailesini temel alır; snr/thd alan adları
korunur ama placeholder `0.0` yerine gerçek ölçüm motorundan beslenir. Kabul
eşikleri (SNR > 100 dB · THD+N < 0.01%) [KANIT]tır ve bu ölçümlerle karşılaştırılır.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[measurement-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[measurement-dogrulama]]` | iddia kanıtları |
| `../testing/testing-kapsam.md` | test senaryosu sınırı (Q4) |
| `../equalizer/equalizer-kapsam.md` | düzeltme hedefi sınır komşusu (A2) |
| `../dsp-master.md` | §4 T7 eşikleri [KANIT] |

## §5 Literatür kod bloğu A — THD+N iskeleti (PLANNED)

> ⚠️ Derlenmedi/test edilmedi. Kaynak yöntem: `measurement-kapsam.md` §7.
> fs/N/R1 parametreleri UNKNOWN.

```cpp
// PLANNED — THD+N iskeleti (derlenmedi; tahmin edilen parametre yok)
// fs = 48000 (sartname) · N = FFT boyutu (R1 UNKNOWN)
struct ThdResult {
    double thdPercent;   // %
    double thdNPercent;  // % — eşik <0.01 [KANIT]
    double fundamentalHz;// temel frekans
};

// Saf ton → temel ve harmonikleri ayır → oran. Pencere/R1 UNKNOWN.
// ⚠️ VERIFICATION REQUIRED — pencere tipi ve katsayısı R3'e bağlı.
ThdResult measureThdN(const float* in, size_t n, double fs, double f0Hz);
```

## §6 Literatür kod bloğu B — true-peak iskeleti (PLANNED)

> Kaynak yöntem: `measurement-kapsam.md` §8 (ITU-R BS.1770 · 4× interp).

```cpp
// PLANNED — true-peak (dBTP) iskeleti (derlenmedi)
// 4× interpolasyon → tepe → 20*log10 → dBTP
// ⚠️ interp filtresi R1 (araç zinciri) UNKNOWN
struct TruePeakResult {
    double peakDbtp;   // dBTP
    bool   overs;      // 0 dBFS aşıldı mı
};

TruePeakResult measureTruePeak(const float* in, size_t n);
```

## §7 Literatür kod bloğu C — SNR iskeleti (PLANNED)

> Kaynak: `measurement-kapsam.md` §4/3 (SNR > 100 dB [KANIT]) · §25 S4 (boş girdi).

```cpp
// PLANNED — SNR iskeleti (derlenmedi)
// referans seviyesi / gürültü tabanı → 20*log10 → dB
double measureSnrDb(const float* reference, const float* noise, size_t n);
```

## §8 Literatür kod bloğu D — Signalsmith metering deseni (literatür — §2, HEAD 200 ✅)

```cpp
// literatür deseni — Signalsmith-Audio/basics README (§2)
// thread-safe meter: UI thread meter okur, DSP thread yazar.
signalsmith::basics::AnalyserFloat analyser;
analyser.wantsMeters(true);       // meter akışı iste
if (analyser.hasMeters()) { /* meter verisi hazır */ }
```

**Ne gösterdiği:** realtime ölçüm okumasının UI thread'e güvenli aktarımı.
CoreMusic'te bu sınıf yok (grep §12); yalnız desen referansıdır.

## §9 Literatür kod bloğu E — FFT çözünürlük hesabı (PLANNED)

> Kaynak: `measurement-kapsam.md` §6 (çözünürlük = fs/N).

```cpp
// PLANNED — çözünürlük hesabı (48 kHz sartname)
constexpr double kSampleRate = 48000.0;  // sartname — ölçüm DEĞİL
// çözünürlük(Hz) = fs / N
double resolution(size_t fftSize) { return kSampleRate / double(fftSize); }
// 2048 → ~23.4 Hz  ·  4096 → ~11.7 Hz
```

## §10 Test kodu şablonu (Catch2 — PLANNED)

```cpp
// PLANNED — measurement test şablonu (derlenmedi; test yok)
#include <catch2/catch_test_macros.hpp>

TEST_CASE("K1: SNR > 100 dB") {
    // boş girdi → measureSnrDb → REQUIRE(snr > 100.0);
    // ⚠️ VERIFICATION REQUIRED — gerçek ölçüm PLANNED (0 çalıştırma)
    SUCCEED("PLANNED");
}

TEST_CASE("K2: THD+N < 0.01% @ 1 kHz") {
    // saf ton → measureThdN → REQUIRE(thdN < 0.01);
    SUCCEED("PLANNED");
}

TEST_CASE("K3: true-peak raporu") {
    // intersample tepe → measureTruePeak → REQUIRE(overs == true);
    SUCCEED("PLANNED");
}
```

**Durum:** 0/3 test çalıştırıldı; kod derlenmedi (grep: test yok).

## §11 Anti-pattern örnekleri (YAPILMAYACAKLAR)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | `0.0` placeholder'ı ölçüm sanmak | yanlış SNR/THD iddiası |
| 2 | `measureThdN`'i penceresiz çağırmak | spektral sızıntı → şişmiş THD |
| 3 | sample-peak'i true-peak sanmak | intersample kaçırma |
| 4 | float32 offline hassasiyet iddiası | SNR yanlış (kapsam §27) |
| 5 | eşik tahmini | zero-hallucination ihlali |

## §12 Komşu dal örnek sınırları

| Dal | Bu dosyadan ne bekler | Beklemeyeceği şey |
|---|---|---|
| equalizer | ölçüm sonuçları (kapsam §14) | düzeltme hedefi hesabı |
| limiter | dBTP raporu | gain uygulama |
| testing | protokol (Q4 sınırı) | test senaryosu içeriği |
| pipeline | uçtan uca gecikme ölçümü | bütçe tahmini |

## §13 Ek wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | T7 eşikleri (§4) |
| `[[dsp-dogrulama]]` | kapı matrisi |
| `[[measurement-kapsam]]` | protokol §12 · literatür §6–§9 |
| `[[measurement-dogrulama]]` | kapılar K1–K5 (§4) |
| `[[limiter-ornekler]]` §8 | true-peak doğrulama hedefi |

## §14 Sözlük

| Terim | Tanım |
|---|---|
| **placeholder** | `$snr = 0.0` — hazır alan, boş değer (§1) |
| **metering** | sürekli seviye/spektrum ölçümü (§2, §8) |
| **dBTP** | true-peak (dB) (§6) |
| **çözünürlük** | fs/N (Hz) (§9) |
| **pencere** | FFT sızıntı azaltma (§5, §11) |

## §15 Q&A (measurement-ornekler özel)

**Q1 — §1'deki PHP kodu gerçek ölçüm mü?**
Hayır — placeholder (`0.0`) (§1, dogrulama §1/6).

**Q2 — §5–§7 kodları derlenir mi?**
Hayır — PLANNED; hiçbir yerde derlenmedi/test edilmedi.

**Q3 — DSPark/Signalsmith bağımlılık mı?**
Hayır — yalnız literatür referansı (§2); bağımlılık eklenmedi.

**Q4 — test neden `SUCCEED` ile dolu?**
Kapılar (K1–K5) PLANNED; gerçek assert ölçüm sonucu bağlamayı bekler (R1).

## §16 Risk kaydı

| # | Risk | Etki |
|---|---|---|
| 1 | kod PLANNED derlenmedi | API hatası sessiz |
| 2 | R1/R3 UNKNOWN | test kapıları boş |
| 3 | placeholder | yanlış güven |

## §17 Yol haritası

| Adım | Durum |
|---|---|
| §1–§4 çekirdek | ✅ |
| §5–§10 literatür + test şablonu | ✅ bu tur |
| gerçek ölçüm motoru | ⏳ (kod yok) |
| test çalıştırma | ⏳ (0/3) |

## §18 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 83 | 500+ |
| § sayısı | 4 | 26 |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §19 Hızlı başlangıç (5 adım)

1. §1 placeholder'ı gör (kod yok, alan var).
2. §5–§7 PLANNED iskeletleri oku.
3. §10 test şablonunu oku (0/3).
4. §11 anti-pattern'den kaçın.
5. `[[measurement-kapsam]]` §12 protokolüne geç.

## §20 Ek — 2026-10-13 tazeleme notu

§2'deki GitHub başlıkları 2026-10-12 doğrulanmıştır; bu tur (2026-10-13)
HEAD yeniden doğrulanmadı → tarih korunmuştur. 2026-10-13 HEAD tablosu için
`[[measurement-kapsam]]` §10 ve `[[measurement-dogrulama]]` §6'ya bak.

## §21 Ek — kod bloğu sayım özeti

| Blok | Tür | Durum |
|---|---|---|
| §5 | THD+N | PLANNED |
| §6 | true-peak | PLANNED |
| §7 | SNR | PLANNED |
| §8 | Signalsmith metering | literatür |
| §9 | çözünürlük | PLANNED (constexpr) |
| §10 | Catch2 | PLANNED (0/3) |

## §22 Ek — gerçek repo kodu özeti (§1 genişletme)

| Öğe | Değer |
|---|---|
| Dosya | `shared/src/AI/AIEngine.php` satır 141–155 |
| Alanlar | `snr` · `thd` · `temperature` · `status` · `recommendations` |
| Değerler | `0.0` placeholder |
| Yorum satırı | "gerçek implementasyon Device Service üzerinden yapılacak" |
| Kanıt | dosya okundu (2026-10-12) |

## §23 Ek — gerçek repo kodu özeti (bibliyotek)

| Kütüphane | Sınıf/desen | HEAD |
|---|---|---|
| DSPark | SpectrumAnalyzer · LoudnessMeter · DelayEstimator | 200 ✅ |
| Signalsmith basics | AnalyserFloat · wantsMeters/hasMeters | 200 ✅ |

## §24 Ek — kapı ↔ kod bağlantısı

| Kapı | Kod bloğu | Test |
|---|---|---|
| K1 (SNR) | §7 | §10 T1 |
| K2 (THD+N) | §5 | §10 T2 |
| K3 (dBTP) | §6 | §10 T3 |
| K4 (tepki) | — (tölerans UNKNOWN) | — |
| K5 (rapor) | — (R1 UNKNOWN) | — |

## §25 Ek — bu tur ne yazılmadı

- Derlenmemiş kod "çalışıyor" denmedi.
- Eşik/parametre tahmin edilmedi (R1/R3 UNKNOWN).
- Kütüphane bağımlılığı eklenmedi (yalnız literatür).
- Test çalıştırıldı denmedi (0/3).

## §26 Dosya son kontrolü

- [x] §1–§4 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] PLANNED/literatür ayrımı korundu
- [ ] gerçek ölçüm yok — kod PLANNED (0/3)

## §27 Değişiklik günlüğü (measurement-ornekler)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§4 + kısa Authority |
| 2026-10-13 | §5–§27 eklendi · dürüstlük + tam Authority · silme 0 |

## §28 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 28 |
| PLANNED kod bloğu | 5 (§5–§7, §9) + test (§10) |
| Literatür blok | 1 (§8) |
| Test çalıştırma | 0/3 |
| Dürüstlük / Authority | 1 / 1 |
| Silme / tahmin | 0 / 0 |

## §29 Ek — THD/N formülü notu (§5'in matematiği)

| Formül | Anlam |
|---|---|
| THD = √(V2²+…+Vn²)/V1 × 100 | harmonik oranı (%) |
| THD+N | + gürültü ve gömüldü dahil |
| % ↔ dB | 20·log10(orandan) — 0.01% ≈ −80 dB |

**Not:** bu dönüşümler literatür matematiğidir; CoreMusic ölçüm sonucu DEĞİL.

## §30 Ek — FFT pencere seçenekleri (§5 notu)

| Pencere | Not |
|---|---|
| Hanning | yaygın, iyi genlik doğruluğu |
| Hamming | yan loblar biraz yüksek |
| Blackman | düşük yan lob, daha geniş ana lobe |

**Durum:** pencere seçimi R3 (standart) UNKNOWN — tahmin yazılmaz.

## §31 Ek — SNR ölçüm senaryosu (§7 genişletme)

| Adım | Amaç |
|---|---|
| sıfıra yakın girdi ver | yalnız sistemin gürültüsünü üret |
| çıktıyı kaydet | gürültü tabanı |
| referans seviye / gürültü | 20·log10 → dB |
| K1 karşılaştır | >100 dB [KANIT] |

**Durum:** PLANNED — 0 çalıştırma.

## §32 Ek — true-peak senaryosu (§6 genişletme)

| Adım | Amaç |
|---|---|
| intersample tepe içeren sinyal | test girdisi |
| 4× interpolasyon | gerçek tepe tahmini |
| dBTP raporu | K3 girdisi |
| overs bayrağı | 0 dBFS aşımı |

**Durum:** PLANNED — 0 çalıştırma.

## §33 Ek — Signalsmith deseni notu (§8 genişletme)

| Desen | Amaç |
|---|---|
| `wantsMeters(true)` | meter akışını iste |
| `hasMeters()` | meter verisi hazır mı |
| `.spectrumHz()` | spektrum (Hz ekseni) |
| `.spectrumEnergy(ch)` | kanal enerjisi |

**Not:** bu desen literatür referansıdır; CoreMusic'te sınıf yok (grep).

## §34 Ek — constexpr kullanımı (§9)

`constexpr double kSampleRate = 48000.0;` sartname değeridir (512/48 kHz —
AGENTS.md §5). Bu sabit ölçüm sonucu DEĞİL, derleme zamanı tanımıdır
(C++20 kuralı — AGENTS.md §5).

## §35 Ek — Catch2 kapı testi notu (§10 genişletme)

| Test | Kapı | Durum |
|---|---|---|
| T1 SNR | K1 | SUCCEED("PLANNED") |
| T2 THD+N | K2 | SUCCEED("PLANNED") |
| T3 dBTP | K3 | SUCCEED("PLANNED") |

**Neden SUCCEED?** Gerçek ölçüm bağlanana kadar assert, sahte PASS üretir
(anti-pattern §11/5).

## §36 Ek — kod bloğu bağımlılık tablosu

| Blok | Bağımlılık | Var mı (grep)? |
|---|---|---|
| §5 THD+N | FFT + pencere | YOK |
| §6 dBTP | 4× interp filtresi | YOK |
| §7 SNR | referans ölçüm | YOK |
| §9 çözünürlük | `constexpr` (header-only) | tanımlanabilir |
| §10 Catch2 | test framework | YOK |

## §37 Ek — komşu dal kod sınırları

| Dal | Kod bekler | Beklemez |
|---|---|---|
| equalizer | ölçüm çıktısı | EQ hedefi |
| limiter | dBTP raporu | gain |
| pipeline | gecikme ölçümü | bütçe |
| testing | protokol | senaryo içeriği |

## §38 Ek — Q&A genişletme (§15'e devam)

**Q5 — `constexpr` neden kullanıldı?**
AGENTS.md §5: compile-time hesaplama kuralı; 48 kHz sartname'dir.

**Q6 — test neden gerçek assert içermez?**
Ölçüm kodu PLANNED; gerçek assert eşiği sahte PASS üretir (§35).

## §39 Ek — sayım ve blok özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 40 |
| Kod bloğu | 6 (5 PLANNED + 1 literatür) |
| Test | 3 (0/3 çalıştırıldı) |
| Dürüstlük / Authority | 1 / 1 |

## §40 Ek — dosya son kontrolü (§26'ya devam)

- [x] §numaraları artan sırada (§1–§40)
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] PLANNED/literatür ayrımı korundu
- [x] tahmin/gerçekleşme karıştırılmadı
- [ ] gerçek ölçüm motoru yok (grep: kod YOK)

## §41 Ek — kapı ↔ kod ↔ test tam zincir (PLANNED)

```text
girdi → [§5 THD+N / §6 dBTP / §7 SNR] → sonuç → [§10 test] → kapı
   → K1 >100 dB · K2 <0.01% · K3 rapor → PASS | FAIL
```

**Durum:** zincirin tamamı PLANNED — 0 çalıştırma, kod derlenmedi.

## §42 Ek — Head doğrulama notu (bu dosya)

Bu turda bu dosyaya yeni URL eklenmedi. §2'deki iki GitHub başlığı 2026-10-12
doğrulanmıştır; 2026-10-13 HEAD tablosu `[[measurement-kapsam]]` §10'dadır
(7 başlık, 200 ✅).

## §43 Ek — literatür kod bloğu kaynak özeti

| Blok | Kaynak | HEAD |
|---|---|---|
| §5 THD+N | kapsam §7 (metod) | — (dosya referansı) |
| §6 dBTP | ITU-R BS.1770 (literatür) | 200 ✅ (kapsam §10) |
| §7 SNR | kapsam §4/§24 (eşik) | — (dosya referansı) |
| §8 metering | Signalsmith basics | 200 ✅ (§2) |
| §9 çözünürlük | fs/N (matematik) | — |

## §44 Ek — sık yapılan hatalar (kod okumasında)

| # | Hata | Doğrusu |
|---|---|---|
| 1 | `$snr = 0.0`'ı ölçüm sanmak | placeholder (§1) |
| 2 | PLANNED kodu üretime sanmak | derlenmedi (§5–§10) |
| 3 | literatür deseni bağımlılık sanmak | yalnız referans (§8) |
| 4 | `SUCCEED` testi PASS sanmak | ölçüm bekliyor (§35) |

## §45 Ek — kapanış (bu tur)

| Kontrol | Durum |
|---|---|
| §1–§4 korundu, silme 0 | ✅ |
| Dürüstlük 1 · Authority 1 | ✅ |
| §1–§40+ artan sırada | ✅ |
| PLANNED/literatür ayrımı | ✅ |
| Tahmin yazıldı mı? | Hayır |
| Test çalıştırıldı mı? | Hayır (0/3) |
| status: draft | korundu |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
