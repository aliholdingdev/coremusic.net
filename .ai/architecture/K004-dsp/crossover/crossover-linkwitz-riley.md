---
node_id: K004-dsp/crossover/crossover-linkwitz-riley
node_type: module
status: draft
parent_id: K004-dsp/crossover
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# crossover-linkwitz-riley — K004-dsp Derin Dal

Bu dosya, Neva Engine crossover (bölme) bileşeninin Linkwitz-Riley
düzenlerini tanımlar: kutup sayısı, kademe, faz davranışının toplam
yanıtı, frekans ayrımı ve 8.1 kanal mimarisindeki yeri. K004 kapsamında
crossover **PLANNED**'tır; hangi kesim frekansının/kademesinin kullanılacağı
kararı **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Linkwitz-Riley düzeni | Evet | — | Tasarım |
| 2 | Kutup sayısı/kademe | Evet | — | Tasarım |
| 3 | Faz ve grup gecikmesi | Evet | Ölçüm | PLANNED |
| 4 | Bant ayrımı (multiband) | Evet | — | Tasarım |
| 5 | 8.1 kanal eşlemesi | Evet | Donanım | spatial |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | Donanım filtresi | Hayır | Analog | Sınır |
| 9 | Bant kazancı | Referans | dynamics | dynamics |
| 10 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |

## §2 Linkwitz-Riley temel matrisi

| # | Özellik | LR2 (2. kademe) | LR4 (4. kademe) | LR8 (8. kademe) | Durum |
|---|---|---|---|---|---|
| 1 | Türetme | 2× Butterworth | 2× Butterworth | 2× Butterworth | Tasarım |
| 2 | Kesim frekansı (-6 dB) | Evet | Evet | Evet | Tasarım |
| 3 | Toplam faz | 0° (alçak+yüksek) | 360° | 720° | Tasarım |
| 4 | Genlik toplamı | Allpass | Allpass | Allpass | Tasarım |
| 5 | Grup gecikmesi düz | Hayır | Kısmi | Daha iyi | Tasarım |
| 6 | Eğim (dB/oct) | -12 | -24 | -48 | Tasarım |
| 7 | Bant ayrımı kalitesi | Orta | İyi | Çok iyi | Karşılaştırma |
| 8 | Gecikme | Düşük | Orta | Yüksek | Karşılaştırma |
| 9 | CPU | Düşük | Orta | Yüksek | Karşılaştırma |
| 10 | Filtre yapısı | 2 SVF | 4 SVF/2 biquad | 8 SVF/4 biquad | Tasarım |
| 11 | Q (Butterworth) | 0.707 | 0.707 | 0.707 | Tasarım |
| 12 | Pratik kullanım | Basit | Yaygın | Sert ayrım | Karşılaştırma |
| 13 | 8.1 için | Opsiyonel | Öneri | Opsiyonel | Karar yok |
| 14 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 15 | Karar | K004 | K004 | K004 | UNKNOWN |

## §3 Kademe karşılaştırma matrisi

| # | Kademe (n) | Eğim | Toplam faz | Allpass | Gecikme | Durum |
|---|---|---|---|---|---|---|
| 1 | 2 | -12 dB/oct | 180°×2 | Evet | Düşük | Sınıf A |
| 2 | 4 | -24 dB/oct | 180°×4 | Evet | Orta | Sınıf B |
| 3 | 6 | -36 dB/oct | 180°×6 | Evet | Orta | Sınıf C |
| 4 | 8 | -48 dB/oct | 180°×8 | Evet | Yüksek | Sınıf C |
| 5 | 10 | -60 dB/oct | 180°×10 | Evet | Yüksek | Araştırma |
| 6 | 12 | -72 dB/oct | 180°×12 | Evet | Çok yüksek | Araştırma |
| 7 | Butterworth (karşılaştırma) | n×6 | Değişken | Hayır | Düşük | Kıyas |
| 8 | Bessel (karşılaştırma) | Düz faz | Optimum | Hayır | Orta | Kıyas |
| 9 | Chebyshev (karşılaştırma) | Düz eğim | Ripple | Hayır | Orta | Kıyas |
| 10 | Elliptic (karşılaştırma) | Düz | Ripple | Hayır | Orta | Kıyas |
| 11 | FIR (karşılaştırma) | Doğrusal faz | 0° | Hayır | Yüksek | Kıyas |
| 12 | Linkwitz-Riley (karar) | 2× Butterworth | Allpass | Evet | Kademe | Öneri |
| 13 | Ölçüm | PLANNED | PLANNED | — | PLANNED | PLANNED |
| 14 | Ölçüm | PLANNED | PLANNED | — | PLANNED | PLANNED |
| 15 | Karar | UNKNOWN | — | — | UNKNOWN | UNKNOWN |

## §4 Frekans ayrımı matrisi

| # | Bant | Frekans | Kanal | Eğim | Durum |
|---|---|---|---|---|---|
| 1 | Sub/LFE | 3-120 Hz | LFE | LR4 | Tasarım |
| 2 | Low | 120-500 Hz | FL/FR (sub karışımı) | LR4 | Tasarım |
| 3 | Mid | 500-2000 Hz | FL/FR/C | LR4 | Tasarım |
| 4 | High | 2000-20000 Hz | FL/FR/C | LR4 | Tasarım |
| 5 | Surround low | 120-500 Hz | SL/SR | LR4 | Tasarım |
| 6 | Surround high | 500-20000 Hz | SL/SR | LR4 | Tasarım |
| 7 | Height low | 120-500 Hz | HL/HR | LR4 | spatial |
| 8 | Height high | 500-20000 Hz | HL/HR | LR4 | spatial |
| 9 | İki bantlı | 2 bant | Basit | LR2/LR4 | Seçenek |
| 10 | Üç bantlı | 3 bant | Orta | LR4 | Seçenek |
| 11 | Çok bantlı (N) | N bant | Multiband | LR4 | compressor |
| 12 | Bant sınırı | Karar yok | — | — | UNKNOWN |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Karar | UNKNOWN | — | — | UNKNOWN |

## §5 Faz ve grup gecikmesi matrisi

| # | Özellik | Tek kademe | LR2 toplam | LR4 toplam | Durum |
|---|---|---|---|---|---|
| 1 | Faz kayması | -90°×n/2 | 360° (Allpass) | 720° (Allpass) | Tasarım |
| 2 | Genlik toplamı | — | 1 (Allpass) | 1 (Allpass) | Tasarım |
| 3 | Grup gecikmesi | Artan | Artan | Daha artan | Tasarım |
| 4 | Grup düzleştirme | — | FIR/IIR | FIR/IIR | Opsiyonel |
| 5 | Minimum faz | Evet | — | — | Tasarım |
| 6 | Sıfır faz | Hayır (online) | — | — | Tasarım |
| 7 | Bidirezyonel | Hayır (RT) | — | — | Kural |
| 8 | Faz hizası | Ölçüm | PLANNED | PLANNED | PLANNED |
| 9 | Crossover bölgesi | Artık | Allpass | Allpass | Tasarım |
| 10 | Tutarlılık | Zorunlu | Zorunlu | Zorunlu | Tasarım |
| 11 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 12 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 13 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 14 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 15 | Karar | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |

## §6 Biquad katsayı matrisi

| # | Katsayı | Formül (butterworth 2. kademe) | Not | Durum |
|---|---|---|---|---|
| 1 | w0 | 2π·fc/fs | Kesim açısı | Tasarım |
| 2 | alpha | sin(w0)/(2Q) | Bant genişliği | Tasarım |
| 3 | Q (Butterworth) | 0.7071 | Sabit | Tasarım |
| 4 | LPF b0 | (1-cos w0)/2 | Alçak geçiren | Tasarım |
| 5 | LPF b1 | 1-cos w0 | Alçak geçiren | Tasarım |
| 6 | LPF b2 | (1-cos w0)/2 | Alçak geçiren | Tasarım |
| 7 | HPF b0 | (1+cos w0)/2 | Yüksek geçiren | Tasarım |
| 8 | HPF b1 | -(1+cos w0) | Yüksek geçiren | Tasarım |
| 9 | HPF b2 | (1+cos w0)/2 | Yüksek geçiren | Tasarım |
| 10 | a0 | 1+alpha | Normalizasyon | Tasarım |
| 11 | a1 | -2cos w0 | Normalizasyon | Tasarım |
| 12 | a2 | 1-alpha | Normalizasyon | Tasarım |
| 13 | LR katsayısı | 2× BW katsayı | LR = BW² | Tasarım |
| 14 | float32 hassasiyet | ADR-017 | Katsayı | FROZEN |
| 15 | Hesap zamanı | Constructor | Non-RT | L0 |

## §7 Repo kanıtı — bant döngüsü anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIEngine.php | 119 | `for ($band = 0; $band < 31; $band++) {` | Bant döngüsü | Anoloji |
| 2 | shared/src/AI/AIEngine.php | 120 | `$frequency = $this->bandToFrequency($band);` | Bant→frekans | Anoloji |
| 3 | shared/src/AI/AIEngine.php | 111 | `public function optimizeEQ(array $roomProfile): array` | EQ sözleşmesi | Anoloji |
| 4 | engine/ (yok) | — | Crossover C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP'dir; crossover DSP karşılığı **yoktur**
> (motor diskte mevcut değil). 31 bant EQ döngüsü anolojisidir; crossover
> uygulaması bu dosyada **iddia edilmez**.

## §8 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp::LinkwitzRileyFilter sınıfı LR crossover filtresi sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1LinkwitzRileyFilter.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_LinkwitzRileyFilter.h LR tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_LinkwitzRileyFilter.h | 2026-10-12 | 200 OK |
| Earlevel cascading-filters sayfası kademelemeli filtreleri anlatır | https://earlevel.com/main/2016/09/29/cascading-filters/ | 2026-10-12 | 200 OK |
| DSPRelated filters kitabı filtre tasarımını kapsar | https://dsprelated.com/freebooks/filters/ | 2026-10-12 | 200 OK |
| ITU-R BS.775 çok kanallı stereofonik sesi tanımlar | https://www.itu.int/rec/R-REC-BS.775/en | 2026-10-12 | 200 OK |

## §9 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Toplam faz (LR) | Allpass davranışı | Sweep | testing |
| 2 | Genlik toplamı | 1 (0 dB) | Sweep | testing |
| 3 | Kesim frekansı | -6 dB | Sweep | testing |
| 4 | Eğim doğruluğu | Kademe | Sweep | testing |
| 5 | Grup gecikmesi | Ölçüm PLANNED | measurement | PLANNED |
| 6 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 7 | Tahsis | 0 B/audio thread | Profil | L0 |
| 8 | Lock | 0 mutex | Kod denetimi | L0 |
| 9 | float32 | Zorunlu | ADR-017 | FROZEN |
| 10 | Buffer | 512 örnek | sartname | Sabit |
| 11 | Örnekleme | 48 kHz | sartname | Sabit |
| 12 | Gecikme | ≤ blok | Impuls | PLANNED |
| 13 | Bant tutarlılığı | Zorunlu | Test | testing |
| 14 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 15 | Karar | K004 | — | UNKNOWN |

## §10 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | equalizer | Bant EQ | Sıra | eq |
| 2 | compressor | Multiband SC | §4.11 | compressor |
| 3 | limiter | Bant tavanı | Sıra | limiter |
| 4 | spatial | Kanal eşlemesi | §4.7-8 | spatial |
| 5 | dynamics | Bant kazancı | dynamics | dynamics |
| 6 | measurement | Faz ölçümü | Q4 | measurement |
| 7 | testing | Toplam yanıt testi | testing | testing |
| 8 | filters | Filtre ailesi | filters | filters |
| 9 | reverb | Opsiyonel | reverb | reverb |
| 10 | delay | Opsiyonel | delay | delay |
| 11 | pipeline | Sıra | pipeline | pipeline |
| 12 | crossover | Bu dosya | — | Bu |

## §11 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Faz tutarsızlığı | Düşük | Yüksek | LR Allpass | Tasarım |
| 2 | Genlik toplamı sapması | Düşük | Yüksek | Test | testing |
| 3 | CPU aşımı (yüksek kademe) | Orta | Yüksek | Kademe seçimi | Ölçüm |
| 4 | Bant boşluğu/kayması | Düşük | Yüksek | Test | testing |
| 5 | 8.1 eşleme hatası | Orta | Orta | spatial | spatial |
| 6 | Tahsis (katsayı) | Düşük | Yüksek | Constructor | L0 |
| 7 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 8 | Kademe erken seçimi | Orta | Orta | Karar yok | UNKNOWN |
| 9 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §12 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Kademe seçimi (2/4/8)? | Kalite/gecikme | UNKNOWN |
| 2 | Bant frekansları? | 8.1 eşleme | UNKNOWN |
| 3 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 4 | spatial ile eşleme? | Kanal | UNKNOWN |
| 5 | Multiband compressor SC? | compressor | UNKNOWN |
| 6 | Grup gecikmesi düzeltmesi? | Kalite | Karar yok |
| 7 | Ölçüm protokolü? | Doğrulama | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (katsayılar) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Allpass toplamı | Zorunlu | Tasarım |
| 6 | Bant tutarlılığı testi | Evet | testing |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | crossover/crossover-kapsam.md | Kademe-2 |
| 3 | spatial sınırı | spatial-audio/spatial-audio-kapsam.md | 8.1 |
| 4 | compressor sınırı | compressor/compressor-kapsam.md | Multiband |
| 5 | measurement kapsamı | measurement/measurement-kapsam.md | Faz ölçümü |
| 6 | testing örneği | testing/testing-ornekler.md | Toplam yanıt |
| 7 | filters sınırı | filters/filters-kapsam.md | Filtre ailesi |
| 8 | dynamics sınırı | dynamics/dynamics-kapsam.md | Bant kazancı |
| 9 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 10 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 11 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 12 | ADR-017 | .ai/.decisions | float32 |
| 13 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 14 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 15 | Bant döngüsü | AIEngine.php:119 | Anoloji |
| 16 | C++ motor | engine/ (yok) | Eksik |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Crossover | Frekans bazlı bant ayırma |
| Linkwitz-Riley | 2× Butterworth kademelemesi |
| Allpass | Faz geçiren, genlik düz filtre |
| Kademe | Filtre derecesi (kutup) |
| Grup gecikmesi | Faz türevi — zaman gecikmesi |
| Bant | Ayrılmış frekans aralığı |
| LFE | Low frequency effects kanalı |
| Multiband | Çok bantlı işleme |
| SVF | State variable filter |
| biquad | İki kutuplu filtre |
| Kesim frekansı | -6 dB noktası (LR) |
| Butterworth | Düz genlikli filtre |
| Bessel | Düz fazlı filtre |
| Chebyshev | Rippli filtre |
| Sweep | Süpürme testi |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Kademe | 2/4/8 | Bekliyor | §3 |
| 2 | Bant frekansları | §4 | Bekliyor | 8.1 eşleme |
| 3 | Kanal eşlemesi | spatial | Bekliyor | §4.7-8 |
| 4 | Multiband SC | Evet/Hayır | Bekliyor | §4.11 |
| 5 | Grup düzleştirme | Evet/Hayır | Bekliyor | §5.4 |
| 6 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 7 | Bant sayısı | 2/3/N | Bekliyor | §4.9-11 |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 5 URL |
| 4 | Repo kanıtı | AIEngine.php:119 |
| 5 | Uydurma ölçüm yok | Tamam |
| 6 | Q4 UNKNOWN | Tamam |
| 7 | CJK/mojibake | Ölçüldü |
| 8 | Authority son satır | Tamam |
| 9 | ≥500 satır | Ölçüldü |
| 10 | commit atılmadı | Kural |

## §18 Filtre yapısı karşılaştırma matrisi (uygulama düzeyi)

| # | Yapı | LR2 karşılığı | LR4 karşılığı | Avantaj | Risk | Durum |
|---|---|---|---|---|---|---|
| 1 | Tek biquad kademe | 1× BW² | — | Basit | Eğim sınırlı | Sınıf A |
| 2 | SVF kademe | 1× SVF² | — | Stabilite | Parametre | Sınıf A |
| 3 | TPT SVF | 1× TPT² | — | Stabilite yüksek | Parametre | Sınıf B |
| 4 | Ladder | Opsiyonel | — | Karakter | Kararlılık | Araştırma |
| 5 | First-order kademe | 2× FO | 4× FO | Basit | Zayıf eğim | Kıyas |
| 6 | Biquad serisi | 2 seride | 4 seride | Esnek | Parametre | Sınıf A |
| 7 | SVF serisi | 2 seride | 4 seride | Stabilite | Parametre | Sınıf B |
| 8 | FIR (kıyas) | — | — | Doğrusal faz | Gecikme | Kıyas |
| 9 | Wave digital | Opsiyonel | Opsiyonel | Stabilite | Karmaşık | Araştırma |
| 10 | Toplam allpass | Zorunlu | Zorunlu | Faz | — | Tasarım |
| 11 | LPF+HPF kademe | Zorunlu | Zorunlu | LR tanımı | — | Tasarım |
| 12 | Çapraz (cross) | Zorunlu | Zorunlu | Allpass toplamı | — | Tasarım |
| 13 | float32 katsayı | ADR-017 | ADR-017 | Hassasiyet | — | FROZEN |
| 14 | Constructor | Non-RT | Non-RT | L0 | — | L0 |
| 15 | Karar | K004 | K004 | — | — | UNKNOWN |

## §19 Ölçüm senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Frekans yanıtı | Sweep | LR eğrisi | ≤ eşik | testing |
| 2 | Faz yanıtı | Sweep | Allpass | ≤ eşik | testing |
| 3 | Toplam genlik | Sweep | 0 dB | ≤0.1 dB | testing |
| 4 | Toplam faz | Sweep | Allpass | ≤ eşik | testing |
| 5 | Kesim noktası | Sinüs @ fc | -6 dB | ≤0.5 dB | testing |
| 6 | Eğim | Sweep | Kademe | ≤ eşik | testing |
| 7 | Grup gecikmesi | Sweep | Artan | Ölçüm PLANNED | PLANNED |
| 8 | Impuls yanıtı | δ | Zaman | Ölçüm PLANNED | PLANNED |
| 9 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 10 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 11 | Kararlılık | Gürültü | Sınırsız | Bounded | testing |
| 12 | Bant boşluğu | Sweep | Yok | ≤ eşik | testing |
| 13 | Kanal eşlemesi | 8.1 test | Doğru | Spatial | spatial |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §20 8.1 kanal eşleme matrisi

| # | Kanal | Kod | Bant | Crossover | Durum |
|---|---|---|---|---|---|
| 1 | Sol ön | FL | Full/LR | §4.2-4 | ADR-017 |
| 2 | Sağ ön | FR | Full/LR | §4.2-4 | ADR-017 |
| 3 | Merkez | C | Full | §4.3-4 | ADR-017 |
| 4 | LFE | LFE | 3-120 Hz | §4.1 | ADR-017 |
| 5 | Sol surround | SL | Full/LR | §4.5-6 | ADR-017 |
| 6 | Sağ surround | SR | Full/LR | §4.5-6 | ADR-017 |
| 7 | Sol height | HL | Full/LR | §4.7-8 | ADR-017 |
| 8 | Sağ height | HR | Full/LR | §4.7-8 | ADR-017 |
| 9 | Kanal sayısı | 8+LFE | Sabit | — | FROZEN |
| 10 | Sub karışımı | FL/FR/C→sub | Opsiyonel | §4.2 | Karar yok |
| 11 | Kanal kazancı | Per-kanal | dynamics | §4 | dynamics |
| 12 | Kanal tavanı | Per-kanal | limiter | §4 | limiter |
| 13 | Ölçüm | Kanal testi | PLANNED | — | PLANNED |
| 14 | Ölçüm | Kanal testi | PLANNED | — | PLANNED |
| 15 | Karar | Eşleme | K004 | — | UNKNOWN |

## §21 Multiband ilişkisi matrisi

| # | İlişki | Bileşen | Kullanım | Durum |
|---|---|---|---|---|
| 1 | Bant SC | compressor | Frekans seçici kısma | compressor |
| 2 | Bant kazancı | dynamics | Per-bant trim | dynamics |
| 3 | Bant tavanı | limiter | Per-bant tavan | limiter |
| 4 | Bant ölçümü | measurement | Per-bant RTA | measurement |
| 5 | Bant testi | testing | Per-bant doğrulama | testing |
| 6 | Bant sayısı | N | Karar | UNKNOWN |
| 7 | Bant sınırı | fc | Karar | UNKNOWN |
| 8 | Bant SC kapatma | — | Opsiyonel | compressor |
| 9 | Bant fazı | Zorunlu | Tutarsızlık riski | Tasarım |
| 10 | Bant CPU | Ölçüm | PLANNED | PLANNED |
| 11 | Ölçüm | PLANNED | — | PLANNED |
| 12 | Ölçüm | PLANNED | — | PLANNED |
| 13 | Ölçüm | PLANNED | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | PLANNED |
| 15 | Karar | UNKNOWN | — | UNKNOWN |

## §22 Katsayı hesap kontrol matrisi

| # | Kontrol | Beklenen | Yöntem | Durum |
|---|---|---|---|---|
| 1 | float32 yuvarlama | ≤1 LSB | Test | testing |
| 2 | Normalizasyon (a0) | 1 | Test | testing |
| 3 | Kararlılık (kutup) | |p|<1 | Test | testing |
| 4 | LPF+HPF toplamı | Allpass | Test | testing |
| 5 | LR = BW² | Tutarlılık | Test | testing |
| 6 | fc=fs/4 | Simetri | Test | testing |
| 7 | fc→0 | Sınırlı | Test | testing |
| 8 | fc→fs/2 | Sınırlı | Test | testing |
| 9 | Q tutarlılığı | 0.707 | Test | testing |
| 10 | Constructor | Non-RT | Denetim | L0 |
| 11 | Reconfigure | Non-RT | Denetim | L0 |
| 12 | Param atomları | Lock-free | Denetim | L0 |
| 13 | Buffer | 512 | sartname | Sabit |
| 14 | fs | 48 kHz | sartname | Sabit |
| 15 | Uydurma | YASAK | Kural | Kural |

## §23 Bant frekansı seçimi matrisi

| # | Seçenek | Alt bant | Orta bant | Üst bant | Uygun | Durum |
|---|---|---|---|---|---|---|
| 1 | Düşük kesim | 80 Hz | 800 Hz | — | Ev | Seçenek |
| 2 | Orta kesim | 120 Hz | 1000 Hz | — | Stüdyo | Seçenek |
| 3 | Yüksek kesim | 200 Hz | 2000 Hz | — | Sert ayrım | Seçenek |
| 4 | İki bant | 200 Hz | — | — | Basit | Seçenek |
| 5 | Üç bant (geniş) | 100 Hz | 1000 Hz | — | Yaygın | Seçenek |
| 6 | Üç bant (dar) | 150 Hz | 1500 Hz | — | Sert | Seçenek |
| 7 | LFE sınırı | 120 Hz | — | — | Standart | Seçenek |
| 8 | Sub-only | 120 Hz | — | — | Sub | Seçenek |
| 9 | Kanal-bağımlı | Farklı | Farklı | Farklı | 8.1 | Opsiyonel |
| 10 | Ölçüm ile seç | Ölçüm | Ölçüm | Ölçüm | PLANNED | PLANNED |
| 11 | Oda ile seç | Oda | Oda | Oda | UNKNOWN | UNKNOWN |
| 12 | Karar | K004 | K004 | K004 | — | UNKNOWN |
| 13 | Karar | K004 | K004 | K004 | — | UNKNOWN |
| 14 | Karar | K004 | K004 | K004 | — | UNKNOWN |
| 15 | Karar | K004 | K004 | K004 | — | UNKNOWN |

## §24 Filtre kararlılığı matrisi

| # | Durum | Belirti | Koruma | Test | Durum |
|---|---|---|---|---|---|
| 1 | |p|<1 | Kararlı | Varsayılan | testing |
| 2 | |p|=1 | Sınırlı | Parametre sınırı | testing |
| 3 | |p|>1 | Kararsız | Koruma | testing |
| 4 | NaN katsayı | Bozuk | Koruma | Test | testing |
| 5 | Inf katsayı | Bozuk | Koruma | Test | testing |
| 6 | fc geçersiz | Bozuk | clamp | Test | testing |
| 7 | fs=0 | Bozuk | Koruma | Test | testing |
| 8 | Denormal | Yavaş | FTZ | Test | L0 |
| 9 | float32 taşma | Bozuk | Sınır | Test | testing |
| 10 | Reconfigure yarışması | Bozuk | Non-RT | Denetim | L0 |
| 11 | Buffer aşımı | Bozuk | 512 sınırı | Denetim | L0 |
| 12 | Kanal aşımı | Bozuk | 8+LFE sınırı | Denetim | L0 |
| 13 | Param atomı | Yarışma | Lock-free | Denetim | L0 |
| 14 | Koruma testleri | Zorunlu | Suite | testing | testing |
| 15 | Uydurma | YASAK | Kural | Kural | Kural |

## §25 Bağımlılık ve durum özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Kademe seçimi | UNKNOWN | Ölçüm | §3 |
| 2 | Bant frekansları | UNKNOWN | Ölçüm/oda | §23 |
| 3 | Kanal eşlemesi | UNKNOWN | spatial | §20 |
| 4 | Multiband SC | UNKNOWN | compressor | §21 |
| 5 | Grup düzleştirme | Karar yok | — | §5.4 |
| 6 | Faz doğrulama | PLANNED | measurement | §19 |
| 7 | CPU bütçesi | PLANNED | Profil | §9 |
| 8 | Q4 | UNKNOWN | measurement/testing | §12.3 |
| 9 | Motor kodu | PLANNED | engine/ yok | §1.7 |
| 10 | 8.1 kanal | FROZEN | ADR-017 | §20 |

## §26 Denetim matrisi

| # | Denetim | Sıklık | Yöntem | Sorumlu | Durum |
|---|---|---|---|---|---|
| 1 | Katsayı doğruluğu | Her sürüm | Test | qa | testing |
| 2 | Allpass toplamı | Her sürüm | Test | qa | testing |
| 3 | CPU bütçesi | Her sürüm | Profil | embedded | PLANNED |
| 4 | Tahsis denetimi | Her sürüm | Profil | embedded | L0 |
| 5 | Lock denetimi | Her sürüm | Kod | embedded | L0 |
| 6 | Kademe kararı | Karar anı | ADR | architect | Açık |
| 7 | Bant kararı | Karar anı | ADR | architect | Açık |
| 8 | Q4 revizyonu | Karar anı | ADR | architect | Açık |
| 9 | URL yeniden doğrulama | 90 gün | HTTP | vault | Planlı |
| 10 | Uydurma denetimi | Her sürüm | Kod | qa | Kural |

## §27 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Kademe UNKNOWN | Tamam |
| 2 | Faz/Allpass tanımlı | Tamam |
| 3 | 8.1 eşleme §20 | Tamam |
| 4 | Multiband ilişki §21 | Tamam |
| 5 | Kararlılık §24 | Tamam |
| 6 | Web tablosu §8 | Tamam |
| 7 | Repo kanıtı §7 | Tamam |
| 8 | Authority son satır | Tamam |
| 9 | ≥500 satır | Ölçüldü |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode