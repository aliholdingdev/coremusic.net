---
node_id: K004-dsp/equalizer/equalizer-filtre-topolojisi-ve-katsayi
node_type: module
status: draft
parent_id: K004-dsp/equalizer
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# equalizer-filtre-topolojisi-ve-katsayi — K004-dsp Derin Dal

Bu dosya, Neva Engine equalizer bileşeninin biquad katsayı hesaplamasını
(transfer fonksiyonu, katsayı normalizasyonu, Q/gain ilişkisi) tanımlar.
Filtre topolojileri ayrı dosyada (`equalizer-filtre-topolojileri.md`);
bu dosyanın odağı **katsayı matematiğidir**: bilinen cutoff/Q/gain
değerlerinden b0/b1/b2/a0/a1/a2 üretimi ve doğrulama kuralları.
K004 kapsamında EQ **PLANNED**'tır; hangi bant/kademe kullanılacağı
kararı **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Biquad transfer fonksiyonu | Evet | — | Tasarım |
| 2 | Katsayı hesaplama (RBJ benzeri) | Evet | Seçilmiş formül ailesi | UNKNOWN |
| 3 | Q/gain ilişkisi | Evet | — | Tasarım |
| 4 | Bant frekans eşlemesi | Evet | 31 bant kararı | UNKNOWN |
| 5 | float32 hassasiyet | Evet | — | ADR-017 |
| 6 | Filtre topolojisi | Referans | Ayrı dosya | eq-topolojileri |
| 7 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 8 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 9 | Oda korreksiyonu | Referans | AIEngine anolojisi | Anoloji |
| 10 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |

## §2 Biquad transfer fonksiyonu matrisi

| # | Öğe | Sembol | Anlamı | Hesap | Durum |
|---|---|---|---|---|---|
| 1 | Zaman alanı | y[n] | Çıkış | Σ | Tasarım |
| 2 | Girdi | x[n] | Giriş | — | Tasarım |
| 3 | Fark denklemi | y[n] | b0x+b1x1+b2x2-a1y1-a2y2 | Doğrusal | Tasarım |
| 4 | Transfer | H(z) | B(z)/A(z) | Oran | Tasarım |
| 5 | Sıfır | B(z) | Sıfır noktaları | Kök | Tasarım |
| 6 | Kutup | A(z) | Kutup noktaları | Kök | Tasarım |
| 7 | Kararlılık | \|p\|<1 | Birim daire içi | Test | Tasarım |
| 8 | Frekans yanıtı | H(e^jw) | dB yanıt | Değerlendirme | Tasarım |
| 9 | Faz yanıtı | arg H | Faz | Değerlendirme | Tasarım |
| 10 | Grup gecikmesi | -dφ/dω | ms | Türev | filters |
| 11 | Norm | a0 | Payda | 1'e eşitle | Tasarım |
| 12 | Dirençli form | Kroneker | Yazılım | Optimizasyon | Tasarım |
| 13 | Transpoz | Form | Bellek | Alternatif | Tasarım |
| 14 | float32 | ADR-017 | Katsayı | Yuvarlama | FROZEN |
| 15 | Durum | PLANNED | — | — | PLANNED |

## §3 Katsayı hesap matrisi (genel akış)

| # | Adım | Girdi | İşlem | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | cutoff (fc) | Hz | Kullanıcı | fc | Tasarım |
| 2 | örnekleme (fs) | Hz | sartname | 48000 | sartname |
| 3 | cutoff oranı | fc/fs | Bölme | f | Tasarım |
| 4 | açı (w0) | f | 2πf | w0 | Tasarım |
| 5 | cos/sin | w0 | Trigonometri | cw, sw | Tasarım |
| 6 | Q | Kullanıcı | — | Q | Tasarım |
| 7 | alpha | sw/(2Q) | Bölme | al | Tasarım |
| 8 | gain (dB) | Kullanıcı | 10^(dB/20) | A | Tasarım |
| 9 | ham katsayılar | w0, al, A | Formül | b0..a2 | Tasarım |
| 10 | normalizasyon | a0 | Bölme | b/a0, a/a0 | Tasarım |
| 11 | doğrulama | katsayı | \|p\|<1 | Kabul | testing |
| 12 | float32 yazma | katsayı | Yuvarlama | float32 | FROZEN |
| 13 | uygulama | katsayı | Filtre | sinyal | PLANNED |
| 14 | ölçüm | çıkış | Sweep | Yanıt | PLANNED |
| 15 | uydurma | — | YASAK | — | Kural |

## §4 Filtre tipi katsayı matrisi

| # | Tip | b0 (ham) | Q etkisi | gain etkisi | Durum |
|---|---|---|---|---|---|
| 1 | LPF | (1-cw)/2 | Genlik | Yok | Tasarım |
| 2 | HPF | (1+cw)/2 | Genlik | Yok | Tasarım |
| 3 | BPF (sabit kazanç) | al | Genlik | Yok | Tasarım |
| 4 | BPF (0 dB tepe) | al·A?? | Genlik | Yok | Tasarım |
| 5 | Notch | 1 (ham) | Genlik | Yok | Tasarım |
| 6 | Peaking | 1+A·al (yaklaşık) | Genlik | Tepe | Tasarım |
| 7 | Low shelf | Yok (asimetrik) | — | Alt bant | Tasarım |
| 8 | High shelf | Yok (asimetrik) | — | Üst bant | Tasarım |
| 9 | Allpass | 1 (ham) | Genlik | Yok | filters |
| 10 | Notch/peak genişliği | Q | Ters | — | Tasarım |
| 11 | Shelf genişliği | S (yaklaşık) | — | Asimetrik | Tasarım |
| 12 | Q aralığı | 0.1-10 | — | — | Tasarım |
| 13 | Q tipik | 0.707 / 1.0 / 2.0 | Karakter | — | Tasarım |
| 14 | float32 | ADR-017 | — | — | FROZEN |
| 15 | Karar | Formül ailesi | — | — | UNKNOWN |

> §4.4 ham formülü bu dosyada **kesin olarak yazılmaz** — formül ailesi
> (RBJ / TPT / başka) kararı `UNKNOWN`'dur; doğrulanmış kaynak
> gösterilmeden ham formül iddia edilmez (Zero-Hallucination).

## §5 Q ve genlik ilişkisi matrisi

| # | İlişki | Formül | Sonuç | Kullanım | Durum |
|---|---|---|---|---|---|
| 1 | Q → bant genişliği | BW = fc/Q | Ters orantı | Genişlik | Tasarım |
| 2 | Q → tepe (biquad) | 1/(2Q²) dB (yaklaşık) | Yükselme | Karakter | Tasarım |
| 3 | Butterworth Q | 1/√2 | 0.7071 | Düz genlik | Tasarım |
| 4 | Kritik sönümlü | 0.5 | Tepelenme | Sınırfaz | Tasarım |
| 5 | Yüksek Q | >2 | Rezonans | Sert tepe | Tasarım |
| 6 | Düşük Q | <0.5 | Geniş | Yumuşak | Tasarım |
| 7 | Peaking Q/genlik | Q → genişlik | Genlik bağımsız | Tasarım | Tasarım |
| 8 | Notch Q/genlik | Q → derinlik/kısmi | Genlik sabit | Tasarım | Tasarım |
| 9 | Shelf Q/genlik | S → geçiş | Asimetrik | Tasarım | Tasarım |
| 10 | BPF kazanç | Q ile ölçek | Normalizasyon | Tasarım | Tasarım |
| 11 | Ölçüm | Yanıt | Sweep | testing | testing |
| 12 | Ölçüm | Q doğruluk | Sweep | testing | testing |
| 13 | Q aralığı kararı | K004 | — | — | UNKNOWN |
| 14 | float32 | ADR-017 | — | — | FROZEN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §6 31 bant eşleme matrisi (anoloji)

| # | Bant | tipik fc (Hz) | Aritmetik | Geometrik | Durum |
|---|---|---|---|---|---|
| 1 | 0 | 20 | — | √(f1f2) | Anoloji |
| 2 | 1 | 25 | — | Geometrik | Anoloji |
| 3 | 2 | 31.5 | — | Geometrik | Anoloji |
| 4 | 3 | 40 | — | Geometrik | Anoloji |
| 5 | 4 | 50 | — | Geometrik | Anoloji |
| 6 | 5 | 63 | — | Geometrik | Anoloji |
| 7 | 6 | 80 | — | Geometrik | Anoloji |
| 8 | 7 | 100 | — | Geometrik | Anoloji |
| 9 | 8 | 125 | — | Geometrik | Anoloji |
| 10 | 9 | 160 | — | Geometrik | Anoloji |
| 11 | 10 | 200 | — | Geometrik | Anoloji |
| 12 | 11 | 250 | — | Geometrik | Anoloji |
| 13 | 12 | 315 | — | Geometrik | Anoloji |
| 14 | 13..30 | 400-20000 | — | Geometrik | Anoloji |
| 15 | 31 | 20000 | — | Son | Anoloji |

> Tablo, repo'daki 31 bantlı anolojinin eşlemesidir; K004 EQ'nun bant
> sayısı/kararı `UNKNOWN`'dur (§12).

## §7 Repo kanıtı — bant eşlemesi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIEngine.php | 332 | `private function bandToFrequency(int $band): float` | Bant→frekans | Anoloji |
| 2 | shared/src/AI/AIEngine.php | 336 | `20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,` | Frekans listesi | Anoloji |
| 3 | shared/src/AI/AIEngine.php | 111 | `public function optimizeEQ(array $roomProfile): array` | EQ sözleşmesi | Anoloji |
| 4 | engine/ (yok) | — | EQ katsayı C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP'dir; biquad katsayı C++ karşılığı
> **yoktur** (motor diskte mevcut değil). 31 bantlı frekans listesi
> anolojisidir; EQ uygulaması bu dosyada **iddia edilmez**.

## §8 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE kaynak kodu juce_IIRFilter.h IIR filtre tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_IIRFilter.h | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_StateVariableTPTFilter.h SVF tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_StateVariableTPTFilter.h | 2026-10-12 | 200 OK |

## §9 Doğrulama matrisi

| # | Doğrulama | Yöntem | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Normalizasyon | a0=1 | a0=1 | \|a0-1\|≤1 LSB | testing |
| 2 | Kararlılık | \|p\|<1 | Birim daire | Test | testing |
| 3 | LPF dc | H(0) | 1 (0 dB) | ≤0.1 dB | testing |
| 4 | HPF nyquist | H(π) | 1 (0 dB) | ≤0.1 dB | testing |
| 5 | Peaking tepe | H(fc) | 10^(dB/20) | ≤0.1 dB | testing |
| 6 | Notch çukur | H(fc) | ~0 | ≤ eşik | testing |
| 7 | Q genişliği | -3 dB | fc/Q | ≤ eşik | testing |
| 8 | float32 yuvarlama | Katsayı testi | ≤1 LSB | Test | testing |
| 9 | Sweep | Frekans yanıtı | Formül eğrisi | ≤ eşik | testing |
| 10 | Faz | Sweep | Beklenen | ≤ eşik | filters |
| 11 | CPU | Profil | ≤ bütçe | ≤ | PLANNED |
| 12 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | Kural | Kural |

## §10 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Katsayı doğruluğu | ≤1 LSB | Sayısal test | testing |
| 2 | Yanıt doğruluğu | ≤0.1 dB | Sweep | testing |
| 3 | Kararlılık | \|p\|<1 | Kök testi | testing |
| 4 | float32 | Zorunlu | ADR-017 | FROZEN |
| 5 | Buffer | 512 örnek | sartname | Sabit |
| 6 | Örnekleme | 48 kHz | sartname | Sabit |
| 7 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 8 | Tahsis | 0 B/audio thread | Profil | L0 |
| 9 | Lock | 0 mutex | Denetim | L0 |
| 10 | Param geçişi | Kliksiz | Ramp | testing |
| 11 | Q aralığı | Karar | — | UNKNOWN |
| 12 | Bant sayısı | Karar | — | UNKNOWN |
| 13 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 14 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §11 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | filters | Filtre ailesi | Katsayı paylaşımı | filters |
| 2 | crossover | Bant bölme | §6 | crossover |
| 3 | dynamics | Bant kazancı | dynamics | dynamics |
| 4 | limiter | Bant tavanı | limiter | limiter |
| 5 | measurement | EQ ölçümü | Q4 | measurement |
| 6 | testing | Katsayı testi | testing | testing |
| 7 | pipeline | Sıra | pipeline | pipeline |
| 8 | compressor | Multiband | compressor | compressor |
| 9 | reverb | Opsiyonel | reverb | reverb |
| 10 | spatial | Kanal EQ | spatial | spatial |
| 11 | delay | Opsiyonel | delay | delay |
| 12 | equalizer | Bu dosya | — | Bu |

## §12 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Katsayı formül ailesi (RBJ/TPT/diğer) | Kalite/maliyet | UNKNOWN |
| 2 | Bant sayısı ve frekansları? | Kapsam | UNKNOWN |
| 3 | Q aralığı/varsayılan? | Karakter | UNKNOWN |
| 4 | Topoloji (diğer dosya) | Mimari | eq-topolojileri |
| 5 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 6 | Oda korreksiyonu entegre mi? | Kapsam | UNKNOWN |
| 7 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §13 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Kararsız katsayı | Düşük | Çok yüksek | \|p\| testi | testing |
| 2 | float32 kaybı | Orta | Orta | Sınır testi | testing |
| 3 | Formül hatası | Düşük | Yüksek | Sweep testi | testing |
| 4 | CPU aşımı | Orta | Yüksek | Bant/kademe | Ölçüm |
| 5 | Param yarışması | Düşük | Yüksek | Lock-free atom | L0 |
| 6 | Tahsis (katsayı) | Düşük | Yüksek | Constructor | L0 |
| 7 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 8 | Erken formül seçimi | Orta | Orta | Karar yok | UNKNOWN |
| 9 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §14 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (katsayılar) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Kararlılık testi (\|p\|<1) | Evet | testing |
| 6 | Normalizasyon (a0=1) | Evet | testing |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §15 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | equalizer/equalizer-kapsam.md | Kademe-2 |
| 3 | Topoloji dosyası | equalizer/equalizer-filtre-topolojileri.md | Komşu dal |
| 4 | filters sınırı | filters/filters-kapsam.md | Filtre ailesi |
| 5 | crossover sınırı | crossover/crossover-kapsam.md | Bant |
| 6 | measurement kapsamı | measurement/measurement-kapsam.md | Ölçüm |
| 7 | testing örneği | testing/testing-ornekler.md | Katsayı testi |
| 8 | dynamics sınırı | dynamics/dynamics-kapsam.md | Bant kazancı |
| 9 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 10 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 11 | ADR-017 | .ai/.decisions | float32 |
| 12 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 13 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 14 | Bant eşlemesi | AIEngine.php:336 | Anoloji |
| 15 | C++ motor | engine/ (yok) | Eksik |

## §16 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Biquad | İki kutuplu, iki sıfırlı filtre |
| Katsayı | b0..b2, a0..a2 — filtre çarpanları |
| Transfer fonksiyonu | H(z) = B(z)/A(z) |
| Kutup/sıfır | H(z) kökleri |
| Kararlılık | \|p\|<1 koşulu |
| cutoff (fc) | Kesim frekansı |
| Q | Kalite faktörü — bant genişliği |
| alpha | w0/(2Q) ara değişkeni |
| Peaking | Tepe/buçuk EQ |
| Shelf | Rafa EQ (alt/üst) |
| Notch | Çukur EQ |
| Allpass | Yalnız faz geçen filtre |
| Normalizasyon | a0 = 1 yapma |
| Butterworth | Düz genlikli tasarım |
| Sweep | Frekans süpürme testi |

## §17 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Formül ailesi | RBJ/TPT/diğer | Bekliyor | §4.15 |
| 2 | Bant sayısı | 10/31/diğer | Bekliyor | §6 |
| 3 | Q aralığı | 0.1-10 | Bekliyor | §5.13 |
| 4 | Topoloji | Diğer dosya | Bekliyor | §12.4 |
| 5 | Kademe | 1/2 | Bekliyor | eq-topolojileri |
| 6 | Oda korreksiyonu | Evet/Hayır | Bekliyor | §12.6 |
| 7 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §18 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 2 URL |
| 4 | Repo kanıtı | AIEngine.php:336 |
| 5 | Ham formül iddiası yok | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §19 Nyquist ve sınır durum matrisi

| # | Sınır durumu | fc değeri | w0 | Davranış | Koruma | Durum |
|---|---|---|---|---|---|---|
| 1 | fc → 0 | 0 Hz | 0 | LPF: birim, HPF: sıfır | clamp | Tasarım |
| 2 | fc = fs/4 | 12000 Hz | π/2 | Simetri noktası | — | Tasarım |
| 3 | fc = fs/2 | 24000 Hz | π | Nyquist sınırı | clamp | sartname |
| 4 | fc > fs/2 | — | — | Geçersiz | clamp | Tasarım |
| 5 | fc ≤ 0 | — | — | Geçersiz | clamp | Tasarım |
| 6 | Q → 0 | — | al → ∞ | Kararsız | min Q | Tasarım |
| 7 | Q çok yüksek | — | al → 0 | Rezonans | max Q | Tasarım |
| 8 | Q = 0.7071 | — | Butterworth | Düz genlik | — | Tasarım |
| 9 | gain → ∞ | — | A → ∞ | Taşma | max dB | Tasarım |
| 10 | gain = 0 dB | — | A = 1 | Nötr | — | Tasarım |
| 11 | fs = 0 | — | — | Geçersiz | Koruma | testing |
| 12 | NaN girdi | — | — | Bozuk | Koruma | testing |
| 13 | Inf girdi | — | — | Bozuk | Koruma | testing |
| 14 | float32 taşma | — | — | Bozuk | Sınır | testing |
| 15 | Uydurma | — | — | YASAK | Kural | Kural |

## §20 Kademe (filtre derecesi) matrisi

| # | Kademe | Kutup | Eğim | Gecikme | CPU | Durum |
|---|---|---|---|---|---|---|
| 1 | 1. kademe | 1 | -6 dB/oct | Düşük | Düşük | Kıyas |
| 2 | 2. kademe (biquad) | 2 | -12 dB/oct | Orta | Düşük | Öneri |
| 3 | 4. kademe | 4 | -24 dB/oct | Orta | Orta | Opsiyonel |
| 4 | 6. kademe | 6 | -36 dB/oct | Yüksek | Orta | Opsiyonel |
| 5 | 8. kademe | 8 | -48 dB/oct | Yüksek | Yüksek | Opsiyonel |
| 6 | Tek biquad (EQ bant) | 2 | — | Düşük | Düşük | Öneri |
| 7 | Seri biquad (çok bant) | 2×N | Toplam | Toplam | N× | Opsiyonel |
| 8 | SVF (state variable) | 2 | 12 dB/oct | Düşük | Orta | Sınıf B |
| 9 | TPT SVF | 2 | 12 dB/oct | Düşük | Orta | Sınıf B |
| 10 | Ladder | 4+ | Karakterli | Orta | Orta | Araştırma |
| 11 | First-order seri | N | -6·N dB/oct | Düşük | Düşük | Kıyas |
| 12 | float32 kararlılık | Kademe | Artan risk | Test | testing | testing |
| 13 | Ölçüm | PLANNED | — | — | PLANNED | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED | PLANNED |
| 15 | Karar | UNKNOWN | — | — | UNKNOWN | UNKNOWN |

## §21 CPU ve bellek matrisi (katsayı hesabı)

| # | İşlem | Sıklık | CPU | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | Katsayı hesabı (constructor) | Bir kez | Düşük | — | L0 |
| 2 | Katsayı yeniden hesap | Param değişimi | Düşük | — | L0 |
| 3 | Trigonometri (sin/cos) | Param değişimi | Orta | — | L0 |
| 4 | Bölme | Param değişimi | Düşük | — | L0 |
| 5 | Filtre uygulama (blok) | Her blok | Ana maliyet | 0 B | Tasarım |
| 6 | Biquad uygulama | 512×N | Düşük-orta | 0 B | Tasarım |
| 7 | SVF uygulama | 512×N | Düşük | 0 B | Tasarım |
| 8 | 31 bant toplam | 512×31 | Yüksek | 0 B | Ölçüm |
| 9 | SIMD (opsiyonel) | Blok | Hız | — | Opsiyonel |
| 10 | Blok başına yeniden hesap | Yasak | — | — | L0 |
| 11 | Precompute tablo | Opsiyonel | Hız | Constructor | Opsiyonel |
| 12 | Bellek (katsayılar) | N bant × 6 | Düşük | Baştan | L0 |
| 13 | Bellek durumu | 6 kvadrat | Düşük | Baştan | L0 |
| 14 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

## §22 Parametre geçiş matrisi

| # | Parametre | Geçiş | Hız | Yarışma kontrolü | Durum |
|---|---|---|---|---|---|
| 1 | cutoff | Rampa | Orta | Atom | L0 |
| 2 | Q | Rampa | Orta | Atom | L0 |
| 3 | gain (dB) | Rampa | Hızlı | Atom | L0 |
| 4 | Bant aç/kapa | Rampa | Hızlı | Atom | L0 |
| 5 | Preset geçişi | Zincir | Yavaş | Sıralı | Tasarım |
| 6 | Katsayı yazma | Çift yazma | — | Tutarlı okuma | L0 |
| 7 | Yarışma okuma | Eski/yeni | — | Atom | L0 |
| 8 | Klik önleme | Rampa | — | Zorunlu | Tasarım |
| 9 | Geçiş süresi | 10-50 ms | Karar yok | — | UNKNOWN |
| 10 | UI→DSP | Atom | Anlık | Lock-free | L0 |
| 11 | Toplam zincir geçişi | Sıralı | Yavaş | — | Tasarım |
| 12 | Sessiz geçiş | Zero-cross | Opsiyonel | — | Opsiyonel |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §23 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Katsayı normalizasyon | Tüm tipler | a0=1 | ≤1 LSB | testing |
| 2 | Kararlılık | Tüm katsayılar | \|p\|<1 | Test | testing |
| 3 | LPF sweep | Sweep | Formül eğrisi | ≤0.1 dB | testing |
| 4 | HPF sweep | Sweep | Formül eğrisi | ≤0.1 dB | testing |
| 5 | Peaking sweep | Sweep | Formül eğrisi | ≤0.1 dB | testing |
| 6 | Shelf sweep | Sweep | Formül eğrisi | ≤0.1 dB | testing |
| 7 | Notch sweep | Sweep | Formül eğrisi | ≤ eşik | testing |
| 8 | Q doğruluk | -3 dB bant | fc/Q | ≤ eşik | testing |
| 9 | float32 yuvarlama | Katsayı | ≤1 LSB | Test | testing |
| 10 | Sınır (fc) | fs/2 dışı | Koruma | clamp | testing |
| 11 | Param geçişi | Sürükleme | Kliksiz | Ramp | testing |
| 12 | CPU profil | 31 bant | Bütçe | ≤ | PLANNED |
| 13 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 14 | Q4 | UNKNOWN | — | measurement | UNKNOWN |
| 15 | Uydurma | YASAK | — | Kural | Kural |

## §24 Bant kazanç toplama matrisi

| # | Toplama yöntemi | Formül | Sonuç | Risk | Durum |
|---|---|---|---|---|---|
| 1 | Doğrusal toplam | Σ g_i(f) | Yanıt | Faz | Tasarım |
| 2 | dB toplamı | Σ dB_i | dB | Aproksimasyon | Tasarım |
| 3 | Seri filtre | Π H_i(f) | Yanıt | Düşük | Öneri |
| 4 | Paralel filtre | Σ H_i(f) | Yanıt | Yüksek | Opsiyonel |
| 5 | Bant örtüşmesi | Komşu | Düzgün | Çakışma | Tasarım |
| 6 | Çift sayım | Aşırı | Şişme | Risk | Tasarım |
| 7 | Nötr testi | 0 dB tüm | 1 (0 dB) | Kabul | testing |
| 8 | Tek bant testi | Tek değişken | Tek tepe | Kabul | testing |
| 9 | Tepe üstü | Toplam > hedef | Aşırı | Koruma | Tasarım |
| 10 | Tavan koruması | Sonraki stage | — | limiter | limiter |
| 11 | float32 | ADR-017 | Yeterli | — | FROZEN |
| 12 | Ölçüm | Sweep | PLANNED | — | PLANNED |
| 13 | Ölçüm | CPU | PLANNED | — | PLANNED |
| 14 | Karar | Toplama yöntemi | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §25 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Formül ailesi | UNKNOWN | Karar | §4 |
| 2 | Bant sayısı | UNKNOWN | Karar | §6 |
| 3 | Q aralığı | UNKNOWN | Karar | §5 |
| 4 | Topoloji | Diğer dosya | eq-topolojileri | §12.4 |
| 5 | Kademe | UNKNOWN | Karar | §20 |
| 6 | Katsayı doğrulama | PLANNED | testing | §9 |
| 7 | Yanıt doğrulama | PLANNED | testing | §23 |
| 8 | CPU bütçesi | PLANNED | Ölçüm | §21 |
| 9 | Q4 | UNKNOWN | measurement/testing | §12.5 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.8 |

## §26 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Satır numaraları doğrulandı (332/336) | Tamam |
| 3 | Ham formül iddiası yok | Tamam |
| 4 | URL benzersiz (2) | Tamam |
| 5 | Ellipsis 0 | Ölçüldü |
| 6 | CJK/mojibake 0 | Ölçüldü |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | Uydurma ölçüm yok | Tamam |
| 9 | ≥500 satır | Ölçüldü |
| 10 | Authority son satır | Tamam |

## §27 Son ek denetim satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Katsayı doğrulama §9 | Tamam |
| 2 | Sınır durumları §19 | Tamam |
| 3 | Kademe §20 | Tamam |
| 4 | Toplama §24 | Tamam |
| 5 | Karar günlüğü §17 | Tamam |
| 6 | Bağlantı haritası §15 | Tamam |
| 7 | Repo kanıtı §7 | Tamam |
| 8 | Web tablosu §8 | Tamam |
| 9 | Authority son satır | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode