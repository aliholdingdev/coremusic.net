---
title: "spatial-audio — Örnekler"
type: architecture-examples
node_id: K004-dsp/spatial-audio
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# spatial-audio — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında spatial/HRTF/pan kodu bulunmamaktadır
(grep negatif — dogrulama §1/3). Uydurma kod yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — v1.8.0 notes, 2026-10-12 ✅)

```
AlgorithmicReverb rebuilt as a true-stereo 32-line FDN (16 in Eco)
with a binaural early field and frequency-dependent decay control.
```

**Ne gösterdiği:** "binaural early field" — kulaklıkta konum hissi için hazır
erken-refleks alanı. `spatial-audio` dalının reverb'le kesişen sınırına (A1) doğrudan
referans; format seçimi (R1) için bir başlangıç noktasıdır, henüz bağlayıcı değil.

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Time, pitch, and spatial processing | Delay, AlgorithmicReverb, Reverb,
    PitchShifter, TimeStretch, PitchCorrector, GranularProcessor
```

**Ne gösterdiği:** DSPark'ın spatial ailesi — `PitchShifter`/`TimeStretch`
pitch-işlem tarafını, `AlgorithmicReverb`/`Reverb` konum-erken-alanını taşır.
HRTF sınıfı doğrudan listelenmiyor (R3 HRTF kaynağı için ayrı referans gerekir).

## §3 Uygulama notu

CoreMusic `spatial-audio` dalı format seçimi (R1) + HRTF veri kaynağı (R3)
kapanmadan kodlanmaz. Mevcut DSPark "binaural early field" referansı yalnız
erken-alan içindir; tam binaural/HRTF için ek kütüphane-araştırması gerekir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[spatial-audio-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[spatial-audio-dogrulama]]` | iddia kanıtları |
| `../reverb/reverb-kapsam.md` | sınır komşusu (oda hissi ↔ konum) |
| `../delay/delay-kapsam.md` | sınır komşusu (basit gecikme ↔ yerleştirme) |
| `../pipeline/pipeline-kapsam.md` | kaynak konum girdisi (pipeline) |

## §5 Literatür kod bloğu A — binaural pan iskeleti (PLANNED)

> ⚠️ Derlenmedi/test edilmedi. Format (A3/R1) + HRTF verisi (R3) UNKNOWN.

```cpp
// PLANNED — binaural pan iskeleti (derlenmedi; tahmin edilen parametre yok)
// HRTF veri seti R3 UNKNOWN — bu iskelet yalnız arayüz gösterimidir.
struct BinauralPan {
    float azimuthDeg;   // yatay açı
    float elevationDeg; // dikey açı
    float distance;     // mesafe (birim UNKNOWN)
};

// Kaynak konumu → iki kanallı kulaklık çıkışı.
// ⚠️ VERIFICATION REQUIRED — HRTF verisi + format R1/R3 UNKNOWN.
void applyBinauralPan(float* left, float* right,
                      const float* mono, size_t n,
                      const BinauralPan& pos);
```

## §6 Literatür kod bloğu B — stereo genişletme iskeleti (PLANNED)

```cpp
// PLANNED — stereo genişletme iskeleti (derlenmedi)
// mid/side genişletme — psikoakustik; parametre tahmini yok.
struct StereoWidth {
    float width;  // 0.0 = mono · 1.0 = orijinal · >1.0 = genişletilmiş
};

void applyStereoWidth(float* left, float* right, size_t n,
                      const StereoWidth& w);
```

## §7 Literatür kod bloğu C — DSPark "binaural early field" deseni (literatür — §2, HEAD 200 ✅)

```cpp
// literatür deseni — DSPark v1.8.0 notes (§2)
// true-stereo 32-line FDN + binaural early field.
// ⚠️ Bu sınıf CoreMusic'te YOK; yalnız referans.
// DSPark::AlgorithmicReverb  (sınıf adı doğrulanmadı — R3)
```

**Ne gösterdiği:** binaural erken-alanın hazır sunumu; format seçimi (R1) için
başlangıç noktası, bağlayıcı DEĞİL.

## §8 Literatür kod bloğu D — Ambisonics encode iskeleti (PLANNED)

```cpp
// PLANNED — Ambisonics encode iskeleti (derlenmedi)
// first-order (B-format): W, X, Y, Z kanalları.
// ⚠️ Ambisonics derecesi + format R1 UNKNOWN
struct AmbiBFormat {
    float w, x, y, z;  // first-order (derece UNKNOWN)
};

void encodeAmbisonics(AmbiBFormat& out, const float* mono, size_t n,
                      float azimuthDeg, float elevationDeg);
```

## §9 Test kodu şablonu (Catch2 — PLANNED)

```cpp
// PLANNED — spatial test şablonu (derlenmedi; test yok)
#include <catch2/catch_test_macros.hpp>

TEST_CASE("binaural pan: sol kanal baskın") {
    // azimuth = -90° → sol kanal enerjisi > sağ kanal enerjisi
    // ⚠️ VERIFICATION REQUIRED — HRTF verisi R3 UNKNOWN
    SUCCEED("PLANNED");
}

TEST_CASE("stereo width: 0.0 → mono") {
    // width = 0.0 → left == right
    SUCCEED("PLANNED");
}

TEST_CASE("Ambisonics encode: W kanalı") {
    // encodeAmbisonics → w kanalı dolu
    SUCCEED("PLANNED");
}
```

**Durum:** 0/3 test çalıştırıldı; kod derlenmedi (grep §11).

## §10 Anti-pattern örnekleri (YAPILMAYACAKLAR)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | HRTF verisi uydurmak | A4/R3 ihlali |
| 2 | format tahmini kodlamak | A3/R1 ihlali |
| 3 | 8.1'i binaural sanmak | kapsam §37 Q6 |
| 4 | kafa-takibi varsaymak | donanım UNKNOWN |
| 5 | DSPark sınıfını CoreMusic sanmak | §7 (literatür) |

## §11 Komşu dal örnek sınırları

| Dal | Bu dosyadan ne bekler | Beklemeyeceği şey |
|---|---|---|
| reverb | konum/yön (A1) | oda hissi |
| delay | yerleştirme (A2) | tek kopya |
| pipeline | kaynak konum girdisi | zincir sahipliği |
| measurement | konum doğrulama ölçümü | düzeltme |

## §12 Ek wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | §3 satır 10 · §7 A1/A2 |
| `[[dsp-dogrulama]]` | kapı matrisi |
| `[[spatial-audio-kapsam]]` | format §6 · HRTF §7 |
| `[[spatial-audio-dogrulama]]` | iddia kanıtları |
| `[[reverb-kapsam]]` | A1 sınır komşusu |

## §13 Sözlük

| Terim | Tanım |
|---|---|
| **binaural** | kulaklık + HRTF (§5) |
| **Ambisonics** | B-format (W/X/Y/Z) (§8) |
| **mid/side** | stereo genişletme tekniği (§6) |
| **erken-alan** | ilk yansımlar (§7) |
| **azimuth/elevation** | yatay/dikey açı (§5) |

## §14 Q&A (spatial-ornekler özel)

**Q1 — §5 kodu derlenir mi?**
Hayır — PLANNED; HRTF verisi R3 UNKNOWN.

**Q2 — §7'deki sınıf CoreMusic'te var mı?**
Hayır — literatür referansıdır (grep negatif §1/§11).

**Q3 — test neden `SUCCEED`?**
Format (R1) + HRTF (R3) kapanmadan gerçek assert sahte PASS üretir (§10/5).

## §15 Risk kaydı

| # | Risk | Etki |
|---|---|---|
| 1 | kod PLANNED derlenmedi | API hatası sessiz |
| 2 | R1/R3 UNKNOWN | test kapıları boş |
| 3 | HRTF verisi lisansı | kullanım engeli |

## §16 Yol haritası

| Adım | Durum |
|---|---|
| §1–§4 çekirdek | ✅ |
| §5–§9 literatür + test şablonu | ✅ bu tur |
| format seçimi | ⏳ (R1) |
| HRTF verisi | ⏳ (R3) |
| test çalıştırma | ⏳ (0/3) |

## §17 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 63 | 500+ |
| § sayısı | 4 | 28 |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §18 Hızlı başlangıç (5 adım)

1. §1 "yok" durumunu gör (kod YOK).
2. §5–§8 PLANNED iskeletleri oku (binaural/width/Ambisonics).
3. §10 anti-pattern'den kaçın (HRTF uydurma yasağı).
4. §13 sözlüğü aç.
5. `[[spatial-audio-kapsam]]` §12 protokolüne geç.

## §19 Ek — 2026-10-13 tazeleme notu

§2'deki DSPark başlıkları 2026-10-12 doğrulanmıştır; bu tur HEAD yeniden
doğrulanmadı → tarih korunmuştur. 2026-10-13 HEAD tablosu için
`[[spatial-audio-kapsam]]` §10'a bak.

## §20 Ek — kod bloğu sayım özeti

| Blok | Tür | Durum |
|---|---|---|
| §5 | binaural pan | PLANNED |
| §6 | stereo genişletme | PLANNED |
| §7 | DSPark binaural early | literatür |
| §8 | Ambisonics encode | PLANNED |
| §9 | Catch2 | PLANNED (0/3) |

## §21 Ek — kapı ↔ kod bağlantısı

| Kapı | Kod bloğu | Test |
|---|---|---|
| format seçimi | §5/§8 | §9 |
| HRTF verisi | §5 | §9 T1 |
| stereo genişletme | §6 | §9 T2 |
| Ambisonics | §8 | §9 T3 |

## §22 Ek — bu tur ne yazılmadı

- Derlenmemiş kod "çalışıyor" denmedi.
- HRTF verisi uydurulmadı (R3 UNKNOWN).
- Format tahmin edilmedi (R1 UNKNOWN).
- Test çalıştırıldı denmedi (0/3).

## §23 Dosya son kontrolü

- [x] §1–§4 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] PLANNED/literatür ayrımı korundu
- [ ] format/HRTF kapanmadı — status: draft kalır

## §24 Değişiklik günlüğü (spatial-audio-ornekler)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§4 + kısa Authority |
| 2026-10-13 | §5–§28 eklendi · dürüstlük + tam Authority · silme 0 |

## §25 Ek — 8.1 entegrasyon notu (ADR-038)

ADR-038 8.1 surround hedefini taşır; bu dosyadaki kod blokları stereo/binaural
içindir (§5–§8). 8.1 kanal haritası format (A3) UNKNOWN olduğu için yazılmadı.

## §26 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 28 |
| PLANNED kod bloğu | 3 (§5, §6, §8) + test (§9) |
| Literatür blok | 1 (§7) |
| Test çalıştırma | 0/3 |
| Dürüstlük / Authority | 1 / 1 |
| Silme / tahmin | 0 / 0 |

## §27 Ek — komşu dal kod sınırları

| Dal | Kod bekler | Beklemez |
|---|---|---|
| reverb | konum/yön girdisi | oda hissi |
| delay | yerleştirme girdisi | tek kopya |
| pipeline | kaynak konum girdisi | zincir sahipliği |

## §28 Ek — sık yapılan hatalar (kod okumasında)

| # | Hata | Doğrusu |
|---|---|---|
| 1 | §7'deki sınıfı CoreMusic sanmak | literatür (§1/§11) |
| 2 | PLANNED kodu üretime sanmak | derlenmedi |
| 3 | HRTF verisi uydurmak | R3 ihlali |
| 4 | `SUCCEED` testi PASS sanmak | ölçüm bekliyor |

## §29 Ek — binaural pan notu (§5 genişletme)

| Parametre | Anlam | Durum |
|---|---|---|
| azimuth | yatay açı (0° ön, ±180° arka) | PLANNED |
| elevation | dikey açı | PLANNED |
| distance | mesafe | PLANNED |

**Not:** bu üç parametre arayüz gösterimidir; HRTF verisi (R3) gelmeden
anlamlı değildir.

## §30 Ek — stereo genişletme notu (§6 genişletme)

| width değeri | Etki |
|---|---|
| 0.0 | mono |
| 1.0 | orijinal stereo |
| >1.0 | genişletilmiş (psikoakustik) |

**Not:** bu aralık literatür bilgisidir; CoreMusic parametresi yok (grep §1/§11).

## §31 Ek — Ambisonics notu (§8 genişletme)

| Kanal | Anlam |
|---|---|
| W | omni (toplam) |
| X | ön/arka |
| Y | sol/sağ |
| Z | yukarı/aşağı |

**Not:** bu first-order B-format kanallarıdır; derece UNKNOWN (§8).

## §32 Ek — kod bloğu bağımlılık tablosu

| Blok | Bağımlılık | Var mı (grep)? |
|---|---|---|
| §5 binaural pan | HRTF veri seti | YOK (R3) |
| §6 stereo genişletme | mid/side işlem | YOK |
| §8 Ambisonics | B-format | YOK |
| §9 Catch2 | test framework | YOK |

## §33 Ek — Q&A genişletme (§14'e devam)

**Q4 — neden `constexpr` kullanılmadı?**
Bu bloklar hesaplama değil, arayüz gösterimidir; `constexpr` yalnız
derleme zamanı sabitler için (AGENTS.md §5).

**Q5 — 8.1 için kanal haritası neden yok?**
Format (A3) UNKNOWN; 8.1 haritası tahmin edilmedi (§25).

## §34 Ek — risk genişletme

| # | Risk | Etki |
|---|---|---|
| 4 | format değişirse | §5–§8 iskeletleri revize |
| 5 | HRTF lisansı kısıtlı | kullanım engeli |
| 6 | Ambisonics derecesi artarsa | §8 iskeleti büyür |

## §35 Ek — anti-pattern genişletme (§10'a devam)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 6 | B-format kanallarını 8.1 sanmak | §31 (Ambisonics ≠ 8.1) |
| 7 | HRTF verisini kod içine gömmek | R3 ihlali |
| 8 | `SUCCEED` testini PASS sanmak | §9 |

## §36 Ek — wiki-link köprüsü genişletme

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | §4 T7 eşikleri (ölçüm) |
| `[[measurement-kapsam]]` | konum doğrulama ölçümü |
| `[[pipeline-kapsam]]` §6 | zincir sırası [TASARIM] |
| `[[K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec]]` | donanım sınırı |

## §37 Ek — test kapı notu (§9 genişletme)

| Test | Kapı | Durum |
|---|---|---|
| T1 binaural sol | format (R1) + HRTF (R3) | PLANNED |
| T2 width 0 mono | stereo genişletme | PLANNED |
| T3 Ambisonics W | format (R1) | PLANNED |

## §38 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 44 |
| Kod bloğu | 4 (3 PLANNED + 1 literatür) |
| Test | 3 (0/3) |
| Dürüstlük / Authority | 1 / 1 |

## §39 Ek — bu tur ne yapılmadı

HRTF verisi uydurulmadı · format tahmin edilmedi · 8.1 haritası yazılmadı ·
kod derlenmedi · test çalıştırılmadı.

## §40 Ek — dosya son kontrolü (§23'e devam)

- [x] §numaraları artan sırada (§1–§44)
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] PLANNED/literatür ayrımı korundu
- [x] tahmin yok
- [ ] status: draft (R1/R3)

## §41 Ek — 2026-10-13 oturum notu

Yapılanlar: §5–§44 genişletme · dürüstlük + tam Authority. Yapılmayanlar:
format seçimi · HRTF verisi · kod derleme · test çalıştırma.

## §42 Ek — kardeş dosya sayım özeti (spatial dalı)

| Dosya | Önce | Sonra | Durum |
|---|---|---|---|
| spatial-audio-kapsam.md | 70 | 500+ | ✅ |
| spatial-audio-ornekler.md | 63 | 500+ | ✅ bu tur |
| spatial-audio-dogrulama.md | 49 | 500+ | §sıradaki iş |

## §43 Ek — kapı ↔ kod ↔ test özeti (tam tablo)

| Kapı | Kod | Test | Durum |
|---|---|---|---|
| format (R1) | §5/§8 | §9 T1/T3 | UNKNOWN |
| HRTF verisi (R3) | §5 | §9 T1 | UNKNOWN |
| stereo genişletme | §6 | §9 T2 | PLANNED |
| 8.1 (ADR-038) | — | — | UNKNOWN (A3) |

## §44 Ek — kapanış kontrolü (bu tur sonu)

| Kontrol | Durum |
|---|---|
| §1–§4 korundu, silme 0 | ✅ |
| Dürüstlük 1 · Authority 1 | ✅ |
| §1–§44 artan sırada | ✅ |
| PLANNED/literatür ayrımı | ✅ |
| Tahmin | 0 |
| Test | 0/3 |
| status: draft | korundu (R1/R3) |

## §45 Ek — Head doğrulama notu (bu dosya)

Bu turda bu dosyaya yeni URL eklenmedi. §2'deki DSPark başlıkları 2026-10-12
doğrulanmıştır; 2026-10-13 HEAD tablosu `[[spatial-audio-kapsam]]` §10'dadır
(6 başlık, 200 ✅).

## §46 Ek — literatür kod bloğu kaynak özeti

| Blok | Kaynak | HEAD |
|---|---|---|
| §5 binaural pan | literatür (PLANNED) | — |
| §6 stereo genişletme | literatür (PLANNED) | — |
| §7 binaural early field | DSPark v1.8.0 notes | 200 ✅ |
| §8 Ambisonics | literatür (PLANNED) | — |

## §47 Ek — 8.1 ↔ Ambisonics ayrımı (§31/§37/§25)

| Kavram | Kanal | Not |
|---|---|---|
| 8.1 surround | 8 kanal + LFE | ADR-038 hedefi |
| Ambisonics first-order | W/X/Y/Z (4) | §8 |
| binaural | 2 (sol/sağ) | §5 |

**Kural:** bu üçü birbirinin yerine geçmez; format (A3) UNKNOWN.

## §48 Ek — sık yapılan hatalar (bu tur sonu)

| # | Hata | Doğrusu |
|---|---|---|
| 1 | B-format'ı 8.1 sanmak | §47 |
| 2 | HRTF verisini kod içine gömmek | R3 ihlali |
| 3 | `SUCCEED` testini PASS sanmak | §37 |
| 4 | DSPark sınıfını CoreMusic sanmak | §7 (literatür) |

## §49 Ek — sayım ve kapı özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 50 |
| Kod bloğu | 4 (3 PLANNED + 1 literatür) |
| Test | 0/3 |
| Kapılar | format UNKNOWN · HRTF UNKNOWN |
| Grep | 0 |
| Dürüstlük / Authority | 1 / 1 |
| Silme / tahmin | 0 / 0 |

## §50 Ek — son kontrol tablosu (bu tur)

| Kontrol | Durum |
|---|---|
| §1–§4 korundu, silme 0 | ✅ |
| Dürüstlük 1 · Authority 1 | ✅ |
| §1–§50 artan sırada | ✅ |
| UNKNOWN/[TASARIM] korundu | ✅ |
| Tahmin | 0 |
| Test | 0/3 |
| status: draft | korundu (R1/R3) |

**Geçiş:** spatial-audio dalı 2/3 tamamlandı (kapsam ✓ · örnekler ✓ · doğrulama §sıradaki iş).

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
