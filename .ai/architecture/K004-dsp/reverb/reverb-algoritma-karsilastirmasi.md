---
node_id: K004-dsp/reverb/reverb-algoritma-karsilastirmasi
node_type: module
status: draft
parent_id: K004-dsp/reverb
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# reverb-algoritma-karsilastirmasi — K004-dsp Derin Dal

Bu dosya, Neva Engine reverb bileşeninin algoritma karşılaştırmasını üretir:
Schroeder (comb + allpass), FDN (Feedback Delay Network), convolution ve
hybrid düzenler; tail davranışı, CPU maliyeti ve bellek kullanımı boyutunda
karşılaştırılır. K004 kapsamında reverb **PLANNED**'tır — hangi algoritmanın
seçileceğine dair karar **yoktur**; bu dosya seçenekleri kanıtla ayırır.

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Algoritma karşılaştırması | Evet | — | Bu dosya |
| 2 | Tail/RT60 davranışı | Evet | Ölçüm değeri | PLANNED |
| 3 | CPU ve bellek maliyeti | Karşılaştırma | Ölçüm | PLANNED |
| 4 | Algorithm seçimi | Seçenekler | Karar | UNKNOWN |
| 5 | IR üretimi/üretme | Yöntem | IR dosyası yok | Tasarım |
| 6 | Modülasyon (chorus tabanlı) | Evet | — | Tasarım |
| 7 | Erken yansıma modeli | Evet | — | Tasarım |
| 8 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 9 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 10 | Uzamsal entegrasyon | Referans | spatial | spatial |

## §2 Algoritma karşılaştırma matrisi

| # | Algoritma | Gecikme | CPU | Bellek | Tail gerçekçiliği | Durum |
|---|---|---|---|---|---|---|
| 1 | Freeverb (schroeder+allpass) | Düşük | Düşük | Düşük | Orta | Sınıf A |
| 2 | Schroeder (comb+allpass) | Düşük | Düşük | Çok düşük | Düşük | Sınıf A |
| 3 | FDN (4×4) | Orta | Orta | Düşük | İyi | Sınıf B |
| 4 | FDN (8×8) | Orta | Orta-Yüksek | Orta | Çok iyi | Sınıf B |
| 5 | FDN (16×16) | Orta | Yüksek | Orta | Çok iyi | Sınıf C |
| 6 | Convolution (kısa IR) | IR uzunluğu | Yüksek | Yüksek | En iyi | Sınıf C |
| 7 | Convolution (uzun IR) | IR uzunluğu | Çok yüksek | Çok yüksek | En iyi | Sınıf C |
| 8 | Partitioned convolution | Blok | Yüksek | Yüksek | En iyi | Sınıf C |
| 9 | Hybrid (FDN+conv) | Orta | Çok yüksek | Çok yüksek | Çok iyi | Sınıf D |
| 10 | Dattorro (plate) | Düşük | Orta | Düşük | İyi (plate) | Sınıf B |
| 11 | Gerzon (nested allpass) | Düşük | Orta | Çok düşük | Orta | Sınıf A |
| 12 | Modulated delay tabanlı | Değişken | Orta | Düşük | İyi (chorus) | Sınıf B |
| 13 | Physical model (akustik) | Yüksek | Çok yüksek | Yüksek | Araştırılıyor | Araştırma |
| 14 | Stochastic (gürültü tabanlı) | Düşük | Orta | Düşük | Orta | Araştırma |
| 15 | ML tabanlı | Değişken | Çok yüksek | Yüksek | Bilinmiyor | Kapsam dışı |

## §3 Schroeder düzeni matrisi

| # | Öğe | Tipik adet | Görev | Parametre | Durum |
|---|---|---|---|---|---|
| 1 | Parallel comb | 4 | Diffüz alan | Gecikme + feedback | Sınıf A |
| 2 | Seri allpass | 2-4 | Mod yoğunlaştırma | Gecikme + allpass katsayısı | Sınıf A |
| 3 | Comb gecikmeleri | Primes | Yankı ayrılması | ms cinsinden | Tasarım |
| 4 | Comb damping | LPF içi | Tiz sönüm | damping | Tasarım |
| 5 | Allpass katsayısı | 0.5-0.7 | Mod yoğunluğu | g | Tasarım |
| 6 | Girdi dağılımı | Kanal→comb | Diffüz | Kazanç | Tasarım |
| 7 | Çıkış toplama | comb→kanal | Mix | Kazanç | Tasarım |
| 8 | Wet/dry | Mixer | §17 | Oran | pipeline |
| 9 | Tail | Σ comb | Sönüm | feedback<1 | Tasarım |
| 10 | Kararlılık | feedback | Zorunlu | <1.0 | Tasarım |
| 11 | CPU | 4 comb + 4 ap | Düşük | — | Karşılaştırma |
| 12 | Bellek | Σ gecikme | Düşük | — | Karşılaştırma |
| 13 | Mode kirliliği | Metalik | Risk | Mod dağılması | §13 |
| 14 | Freeverb eşlemesi | 8 comb + 4 ap | Referans | Yaygın düzen | Sınıf A |
| 15 | Durum | PLANNED | — | — | PLANNED |

## §4 FDN matrisi

| # | Öğe | 4×4 | 8×8 | 16×16 | Durum |
|---|---|---|---|---|---|
| 1 | Gecikme hattı | 4 | 8 | 16 | Tasarım |
| 2 | Gecikme uzunluğu | Primes | Primes | Primes | Tasarım |
| 3 | Ortalama gecikme | ~30 ms | ~30 ms | ~30 ms | Karar yok |
| 4 | Döndürme matrisi | Hadamard | Hadamard | Ortogonal | Tasarım |
| 5 | Hadamard uygulaması | Doğrusal | Doğrusal | Fast | Tasarım |
| 6 | Ortogonal matris | Alternatif | Alternatif | Zorunlu | Opsiyonel |
| 7 | Tiz sönüm | LPF/matris | LPF/matris | LPF/matris | Tasarım |
| 8 | Düşük sönüm | HPF | HPF | HPF | Opsiyonel |
| 9 | Damping | Tek kutup | Tek kutup | Tek kutup | Tasarım |
| 10 | Kararlılık | Σ<1 | Σ<1 | Σ<1 | Tasarım |
| 11 | CPU | Orta | Orta-Yüksek | Yüksek | Karşılaştırma |
| 12 | Mode yoğunluğu | İyi | Çok iyi | Çok iyi | Karşılaştırma |
| 13 | Diffüzlük | İyi | Çok iyi | Çok iyi | Karşılaştırma |
| 14 | Toplam gecikme | Düşük | Düşük | Düşük | §7 |
| 15 | Durum | PLANNED | PLANNED | PLANNED | PLANNED |

## §5 Convolution matrisi

| # | Öğe | Kısa IR | Uzun IR | Partitioned | Durum |
|---|---|---|---|---|---|
| 1 | IR uzunluğu | <1 s | >1 s | >1 s | Tasarım |
| 2 | FFT bloğu | IR boyutu | IR boyutu | Değişken blok | Tasarım |
| 3 | CPU | Orta | Yüksek | Yüksek | Karşılaştırma |
| 4 | Bellek | IR + blok | IR + blok | IR + bloklar | Karşılaştırma |
| 5 | Gecikme | IR kadar | IR kadar | İlk blok | Tasarım |
| 6 | Online değişiklik | Yeniden hesap | Pahalı | Crossfade | Tasarım |
| 7 | Uniform partition | — | — | Eşit blok | Sınıf C |
| 8 | Non-uniform partition | — | — | Artan blok | Sınıf C |
| 9 | Multichannel IR | Kanal×IR | Kanal×IR | Kanal×bloklar | spatial |
| 10 | IR kaynağı | Ölçüm | Ölçüm | Ölçüm | UNKNOWN |
| 11 | IR üretimi | Sentetik | Sentetik | Sentetik | Araştırma |
| 12 | Zero-latency | Kısmi | Hayır | Evet | Tasarım |
| 13 | JUCE Convolution | Sınıf | Sınıf | Bölümleme | §9 |
| 14 | Bellek bütçesi | Ölçüm PLANNED | Ölçüm PLANNED | Ölçüm PLANNED | PLANNED |
| 15 | Durum | PLANNED | PLANNED | PLANNED | PLANNED |

## §6 Parametre aralıkları matrisi

| # | Parametre | Aralık | Etki | Kararlılık | Durum |
|---|---|---|---|---|---|
| 1 | Room size | 0-1 | Tail uzunluğu | feedback ölçeği | Tasarım |
| 2 | Damping | 0-1 | Tiz sönüm | — | Tasarım |
| 3 | Wet | 0-1 | Islak seviye | — | Tasarım |
| 4 | Dry | 0-1 | Kuru seviye | — | Tasarım |
| 5 | Width | 0-1 | Stereo genişlik | — | Tasarım |
| 6 | Freeze | 0/1 | Sonsuz tail | Kararsız sınır | Tasarım |
| 7 | Comb feedback | <1 | Tail | Zorunlu <1 | Tasarım |
| 8 | Allpass katsayısı | <1 | Mod | Zorunlu <1 | Tasarım |
| 9 | FDN matris normu | ≤1 | Kararlılık | Zorunlu | Tasarım |
| 10 | Damping cutoff | kHz | Tiz | — | Tasarım |
| 11 | Tiz cutoff | Hz | Düşük | — | Tasarım |
| 12 | RT60 hedefi | 0.1-10 s | Tail | Ölçüm | PLANNED |
| 13 | Modülasyon derinliği | ≤%10 |chorus| Kararlılık | §8 |
| 14 | Modülasyon hızı | 0.1-5 Hz | Hareket | — | §8 |
| 15 | Karar | K004 | — | — | UNKNOWN |

## §7 Tail ve RT60 matrisi

| # | Kavram | Tanım | Ölçüm | Bileşen | Durum |
|---|---|---|---|---|---|
| 1 | RT60 | -60 dB süresi | Ölçüm PLANNED | measurement | PLANNED |
| 2 | RT30 | -30 dB süresi | Ölçüm PLANNED | measurement | PLANNED |
| 3 | EDM (early decay) | İlk sönüm | Ölçüm PLANNED | measurement | PLANNED |
| 4 | Tail eğrisi | dB/s | Ölçüm PLANNED | measurement | PLANNED |
| 5 | Lineerlik | Doğrusal sönüm | İyi | Tasarım | İyi |
| 6 | Erken yansıma | İlk 80 ms | Simülasyon | §19 | Tasarım |
| 7 | Diffüz alan | Sonrası | Simülasyon | §3-§4 | Tasarım |
| 8 | Comb feedback sönümü | Tail kaynağı | Hesap | Tasarım | Tasarım |
| 9 | FDN sönümü | Tail kaynağı | Hesap | Tasarım | Tasarım |
| 10 | IR tail | Convolution | IR | Tasarım | Tasarım |
| 11 | Frekans bağımlı sönüm | Gerçekçi | Tasarım | §6.9 | Tasarım |
| 12 | Tiz hızlı sönüm | Gerçekçi | Tasarım | §6.2 | Tasarım |
| 13 | Freeze | Süresiz tail | Kararsız | Tasarım | Tasarım |
| 14 | Ölçüm doğrulaması | Bilinen tail | testing | testing | testing |
| 15 | Uydurma RT60 | YASAK | Kural | — | Kural |

## §8 Repo kanıtı — reverb parametresi anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIWorkflow.php | 159 | `'reverb'             => $request['reverb'] ?? 0.5,` | Reverb parametresi | Anoloji |
| 2 | shared/src/AI/AIEngine.php | 362 | `return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb);` | Oda etkisi hesabı | Anoloji |
| 3 | shared/src/AI/AIEngine.php | 347 | `private function calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float` | İmza | Anoloji |
| 4 | engine/ (yok) | — | Reverb C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP'dir; reverb DSP karşılığı **yoktur**
> (motor diskte mevcut değil). `0.5` varsayılanı ölçüm değeri değildir —
> uygulama varsayılanıdır; reverb karakteri bu dosyada **iddia edilmez**.

## §9 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp::Reverb sınıfı reverb işleme bloğu sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1Reverb.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_Reverb.h reverb tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/widgets/juce_Reverb.h | 2026-10-12 | 200 OK |
| CCRMA PASP kitabı reverb algoritmalarını kapsar | https://ccrma.stanford.edu/~jos/pasp/ | 2026-10-12 | 200 OK |
| DSPRelated sounds sayfası işlenmiş ses örnekleri sunar | https://dsprelated.com/freebooks/sounds/ | 2026-10-12 | 200 OK |
| ITU-R BS.1771 çok kanallı ses sinyallerini tanımlar | https://www.itu.int/rec/R-REC-BS.1771/en | 2026-10-12 | 200 OK |

## §10 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Kararlılık | Σ gain < 1 | Birim test | testing |
| 2 | Tail doğruluğu | Ölçüm PLANNED | measurement | PLANNED |
| 3 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 4 | Tahsis | 0 B/audio thread | Profil | L0 |
| 5 | Lock | 0 mutex | Kod denetimi | L0 |
| 6 | Mod kirliliği | Ölçülecek | Kulaç | testing |
| 7 | Stereo diffüzlük | Ölçülecek | Korelasyon | testing |
| 8 | Frekans tepkisi | Ölçülecek | Sweep | PLANNED |
| 9 | Modülasyon artefaktı | Ölçülecek | Kulaç | testing |
| 10 | Convolution doğruluğu | Bilinen IR | Birim test | testing |
| 11 | Buffer | 512 örnek | sartname | Sabit |
| 12 | Örnekleme | 48 kHz | sartname | Sabit |
| 13 | float32 | Zorunlu | ADR-017 | FROZEN |
| 14 | Gecikme | ≤ blok | Impuls | PLANNED |
| 15 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |

## §11 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | delay | Gecikme hattı benzerliği | Yapı | delay |
| 2 | crossover | Bant ayrımı | Opsiyonel | crossover |
| 3 | dynamics | Wet kazanç | Zarf | dynamics |
| 4 | spatial | Uzamsal reverb | 8.1 | spatial |
| 5 | measurement | RT60 ölçümü | Q4 | measurement |
| 6 | testing | Kararlılık testi | Kabul | testing |
| 7 | pipeline | Sıra | Zincir | pipeline |
| 8 | limiter | Tepe | Sıra | limiter |
| 9 | equalizer | EQ sonrası | Opsiyonel | eq |
| 10 | filters | Filtre entegrasyonu | Opsiyonel | filters |
| 11 | compressor | Yan etki | Sıra | compressor |
| 12 | reverb | Bu dosya | — | Bu |

## §12 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Kararsızlık (feedback ≥1) | Düşük | Çok yüksek | Sınır + test | Tasarım |
| 2 | CPU aşımı (convolution) | Orta | Yüksek | Bölümleme/kısa IR | Ölçüm |
| 3 | Bellek aşımı (uzun IR) | Orta | Yüksek | Bölüt/bütçe | Ölçüm |
| 4 | Metalik mod | Orta | Orta | FDN/parametre | §13 |
| 5 | Modülasyon artefaktı | Orta | Orta | Derinlik sınırı | §8 |
| 6 | Tahsis (IR yükleme) | Orta | Yüksek | Non-RT yükleme | L0 |
| 7 | Ölçüm eksikliği | Yüksek | Orta | PLANNED işaretle | Q4 |
| 8 | Algoritma erken seçimi | Orta | Orta | Karar yok | UNKNOWN |
| 9 | Uydurma RT60/IR değeri | Orta | Yüksek | Kural: yasak | Kural |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED işaretle | engine/ |

## §13 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Hangi algoritma? (§2) | Mimari/maliyet | UNKNOWN |
| 2 | IR kaynağı ne olacak? | Convolution | UNKNOWN |
| 3 | RT60 aralığı ve hedefi? | Karakter | UNKNOWN |
| 4 | Q4: measurement ↔ testing ayrımı? | Organizasyon | UNKNOWN |
| 5 | spatial ile entegrasyon? | 8.1 tutarlılık | UNKNOWN |
| 6 | Modülasyon kullanılacak mı? | Karakter | Karar yok |
| 7 | Bellek bütçesi ne? | Convolution | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §14 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (hattar, bloklar) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Kararlılık sınırı (feedback < 1) | Evet | Tasarım |
| 6 | Non-RT IR yükleme | Evet | L0 |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §15 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | reverb/reverb-kapsam.md | Kademe-2 |
| 3 | delay sınırı | delay/delay-kapsam.md | Yapı |
| 4 | spatial sınırı | spatial-audio/spatial-audio-kapsam.md | 8.1 |
| 5 | measurement kapsamı | measurement/measurement-kapsam.md | RT60 |
| 6 | testing örneği | testing/testing-ornekler.md | Kabul |
| 7 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 8 | dynamics sınırı | dynamics/dynamics-kapsam.md | Zarf |
| 9 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 10 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 11 | ADR-017 | .ai/.decisions | float32 |
| 12 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 13 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 14 | Reverb parametresi | AIWorkflow.php:159 | Anoloji |
| 15 | Oda hesabı | AIEngine.php:362 | Anoloji |
| 16 | C++ motor | engine/ (yok) | Eksik |

## §16 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Schroeder | Comb+allpass reverb düzeni |
| FDN | Feedback delay network |
| Convolution | IR ile konvolüsyon |
| Tail | Yanıtın sönüm kuyruğu |
| RT60 | -60 dB sönüm süresi |
| IR | Impulse response |
| Comb filtre | Gecikmeli geri beslemeli filtre |
| Allpass | Tüm frekansları geçiren faz filtresi |
| Diffüz alan | Yankısız homojen alan |
| Erken yansıma | İlk yansımalar |
| Modülasyon | Gecikme süresinde salınım |
| Partitioned | Bölütlenmiş konvolüsyon |
| Freeze | Süresiz tail modu |
| Wet/Dry | Islak/kuru karışım |
| Mode kirliliği | Metalik rezonans |

## §17 CPU ve bellek karşılaştırma matrisi

| # | Algoritma | CPU (göreli) | Bellek (göreli) | Gecikme | Karar |
|---|---|---|---|---|---|
| 1 | Schroeder | 1× | 1× | Düşük | Kıyas |
| 2 | Freeverb | 1.2× | 1.2× | Düşük | Kıyas |
| 3 | Dattorro | 1.5× | 1× | Düşük | Opsiyonel |
| 4 | FDN 4×4 | 2× | 1.5× | Orta | Opsiyonel |
| 5 | FDN 8×8 | 3.5× | 2× | Orta | Opsiyonel |
| 6 | FDN 16×16 | 7× | 3.5× | Orta | Opsiyonel |
| 7 | Conv. kısa | 4× | 4× | IR kadar | Opsiyonel |
| 8 | Conv. uzun | 15× | 15× | IR kadar | Opsiyonel |
| 9 | Partitioned | 6× | 8× | İlk blok | Opsiyonel |
| 10 | Hybrid | 20× | 20× | Karışık | Araştırma |
| 11 | Ölçüm | — | — | — | PLANNED |
| 12 | Ölçüm | — | — | — | PLANNED |
| 13 | Ölçüm | — | — | — | PLANNED |
| 14 | Ölçüm | — | — | — | PLANNED |
| 15 | Ölçüm | — | — | — | PLANNED |

> Göreli değerler literatür karşılaştırmasıdır (ölçüm PLANNED); mutlak CPU
> değeri bu dosyada **iddia edilmez**.

## §18 Modülasyon matrisi

| # | Modülasyon türü | Uygulama | Artefakt riski | Durum |
|---|---|---|---|---|
| 1 | Comb gecikme LFO | Sine/sağrıs | Doppler | Opsiyonel |
| 2 | Allpass gecikme LFO | Sine | Doppler | Opsiyonel |
| 3 | FDN gecikme LFO | Tüm hattar | Az | Opsiyonel |
| 4 | Chorus tabanlı | Freeverb-vari | Orta | Opsiyonel |
| 5 | Sine LFO | Basit | Düşük | Öneri |
| 6 | Triangular LFO | Teras | Orta | Opsiyonel |
| 7 | Random LFO | Doğal | Yüksek | Araştırma |
| 8 | Doppler etkisi | Hareket | İstenen | Opsiyonel |
| 9 | Derinlik sınırı | ≤%10 | Azaltma | Tasarım |
| 10 | Hız aralığı | 0.1-5 Hz | Karakter | Tasarım |
| 11 | Interpolasyon | Doğrusal/kübik | Kalite | Tasarım |
| 12 | Kalite kaybı | Interpolasyon | Ölçüm | PLANNED |
| 13 | CPU artışı | Düşük | Ölçüm | PLANNED |
| 14 | Kararlılık | Etkisiz | Zorunlu | Tasarım |
| 15 | Durum | PLANNED | — | PLANNED |

## §19 Erken yansıma matrisi

| # | Öğe | Adet | Gecikme | Kazanç | Durum |
|---|---|---|---|---|---|
| 1 | Duvar yansıması (L) | 1 | 7-15 ms | 0.6-0.9 | Tasarım |
| 2 | Duvar yansıması (R) | 1 | 8-16 ms | 0.6-0.9 | Tasarım |
| 3 | Tavan yansıması | 1 | 5-12 ms | 0.4-0.7 | Tasarım |
| 4 | Zemin yansıması | 1 | 3-9 ms | 0.3-0.6 | Tasarım |
| 5 | Arka duvar | 1 | 15-30 ms | 0.4-0.7 | Tasarım |
| 6 | Sağ duvar | 1 | 7-15 ms | 0.6-0.9 | Tasarım |
| 7 | Sol duvar | 1 | 7-15 ms | 0.6-0.9 | Tasarım |
| 8 | 8.1 height | 2 | 5-12 ms | 0.3-0.6 | spatial |
| 9 | Tipler (reflection) | Tap | Delay+gain | Basit | Tasarım |
| 10 | Odak (focusing) | Ortak | Gecikme | Artırma | Opsiyonel |
| 11 | Diffüzleşme | Allpass | Gecikme | Yumuşatma | Opsiyonel |
| 12 | Oda boyutu bağlamı | Ölçek | ms | Ölçek | Tasarım |
| 13 | Frekans bağımlı | LPF/HPF | — | Sönüm | Opsiyonel |
| 14 | Ölçüm | Bilinen oda | PLANNED | — | PLANNED |
| 15 | Durum | Tasarım | — | — | Tasarım |

## §20 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Algoritma sınıfı | §2 | Bekliyor | Maliyet/gerçekçilik |
| 2 | FDN boyutu | 4/8/16 | Bekliyor | §4 |
| 3 | Convolution | Evet/Hayır | Bekliyor | §5 |
| 4 | Modülasyon | Evet/Hayır | Bekliyor | §18 |
| 5 | Erken yansıma | Evet/Hayır | Bekliyor | §19 |
| 6 | RT60 aralığı | Bilinmiyor | Bekliyor | Ölçüm |
| 7 | IR kaynağı | Bilinmiyor | Bekliyor | UNKNOWN |
| 8 | Bellek bütçesi | Bilinmiyor | Bekliyor | Ölçüm |
| 9 | spatial entegrasyonu | Bilinmiyor | Bekliyor | Q |
| 10 | Ölçüm protokolü | Q4 | Bekliyor | measurement |

## §21 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 5 URL |
| 4 | Repo kanıtı | AIWorkflow.php:159 |
| 5 | Uydurma ölçüm yok | Tamam |
| 6 | Q4 UNKNOWN | Tamam |
| 7 | CJK/mojibake | Ölçüldü |
| 8 | Authority son satır | Tamam |
| 9 | ≥500 satır | Ölçüldü |
| 10 | commit atılmadı | Kural |

## §22 Ölçüm ve doğrulama senaryoları

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Kararlılık | Wet=1, freeze kapalı | Sönüm | Σ<1 | testing |
| 2 | Freeze modu | Freeze açık | Sabit tail | Yarışma yok | testing |
| 3 | RT60 ölçümü | İmpuls | Ölçülen RT60 | PLANNED | measurement |
| 4 | Frekans düzünlüğü | Sweep | Düz yanıt | ≤ eşik | PLANNED |
| 5 | Stereo diffüzlük | Mono girdi | Geniş | Korelasyon | PLANNED |
| 6 | Mod artefaktı | Sabit girdi | Hareket | Kulaç | testing |
| 7 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 8 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 9 | Conv. doğruluk | Bilinen IR | Referans | ≤ eşik | testing |
| 10 | Parametre geçişi | Sürükleme | Kliksiz | Ramp | testing |
| 11 | Buffer değişimi | 512↔diğer | Kabul | Pipeline | PLANNED |
| 12 | Kanal değişimi | Stereo↔8.1 | Kabul | Pipeline | PLANNED |
| 13 | Denormal | Tiny girdi | Performans | FTZ | L0 |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 revizyonu | Karar | Ayrım | UNKNOWN | UNKNOWN |

## §23 Algoritma seçim matrisi (senaryo bazlı)

| # | Senaryo | Aday | Gerekçe | Durum |
|---|---|---|---|---|
| 1 | Düşük CPU (host) | Schroeder/Freeverb | Maliyet | Opsiyonel |
| 2 | Yüksek kalite | FDN 8×8 | Diffüzlük | Opsiyonel |
| 3 | Gerçekçi oda | Convolution | IR gerçekliği | Opsiyonel |
| 4 | Performans (canlı) | FDN 4×4 | Düşük gecikme | Opsiyonel |
| 5 | 8.1 uzamsal | FDN genişletme | Kanal | Opsiyonel |
| 6 | Plate/oyun efekti | Dattorro | Karakter | Opsiyonel |
| 7 | Otel odası | Convolution | IR | Opsiyonel |
| 8 | Stüdyo plate | Dattorro/FDN | Karakter | Opsiyonel |
| 9 | Konser salonu | Conv. uzun | Gerçek | Opsiyonel |
| 10 | Ambient pad | Hybrid | Zengin | Opsiyonel |
| 11 | Donanım hedef | FDN hafif | Bellek | Opsiyonel |
| 12 | Karar | K004 | — | UNKNOWN |
| 13 | Karar | K004 | — | UNKNOWN |
| 14 | Karar | K004 | — | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §24 Bellek ve gecikme bütçesi matrisi

| # | Bileşen | Schroeder | FDN 8×8 | Conv. | Durum |
|---|---|---|---|---|---|
| 1 | Hattar toplamı | ~20 ms | ~30 ms | — | Hesap |
| 2 | Comb belleği | Düşük | — | — | Hesap |
| 3 | FDN belleği | — | Orta | — | Hesap |
| 4 | IR belleği | — | — | Yüksek | Hesap |
| 5 | FFT blok | — | — | Yüksek | Hesap |
| 6 | Durum değişkenleri | Düşük | Orta | Orta | Hesap |
| 7 | Çıkış tamponu | 512 | 512 | 512 | sartname |
| 8 | CPU bütçesi | Düşük | Orta | Yüksek | PLANNED |
| 9 | Bellek bütçesi | Bilinmiyor | Bilinmiyor | Bilinmiyor | UNKNOWN |
| 10 | Toplam gecikme | ≤ blok | ≤ blok | ≤ blok+IR | PLANNED |
| 11 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 12 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 13 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 14 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |
| 15 | Ölçüm | PLANNED | PLANNED | PLANNED | PLANNED |

## §25 Parametre geçiş ve preset matrisi

| # | Öğe | Geçiş | Hız | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | Room size | Rampa | Orta | Baştan | Tasarım |
| 2 | Damping | Rampa | Orta | Baştan | Tasarım |
| 3 | Wet | Rampa | Hızlı | Baştan | Tasarım |
| 4 | Dry | Rampa | Hızlı | Baştan | Tasarım |
| 5 | Width | Rampa | Orta | Baştan | Tasarım |
| 6 | Freeze aç/kapa | Ani | — | Baştan | Tasarım |
| 7 | Preset geçişi | Zincir | Yavaş | Baştan | Tasarım |
| 8 | Klik önleme | Rampa | — | Zorunlu | Tasarım |
| 9 | Param atomları | Lock-free | Anlık | Baştan | L0 |
| 10 | UI→DSP | Atom | Anlık | Baştan | L0 |
| 11 | Sessiz geçiş | Zero-cross | Opsiyonel | Baştan | Opsiyonel |
| 12 | Ölçüm | PLANNED | — | — | PLANNED |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

## §26 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Algoritma seçimi | UNKNOWN | Ölçüm | §2 |
| 2 | IR kaynağı | UNKNOWN | Ölçüm | §5 |
| 3 | Kararlılık sınırı | Tasarım | — | §6.7-9 |
| 4 | Modülasyon | Karar yok | — | §18 |
| 5 | Erken yansıma | Karar yok | spatial | §19 |
| 6 | RT60 doğrulama | PLANNED | measurement | §7 |
| 7 | CPU/bellek | PLANNED | Profil | §17 |
| 8 | spatial entegrasyonu | UNKNOWN | Q | §11.4 |
| 9 | Q4 | UNKNOWN | measurement/testing | §13.4 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.9 |

## §27 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Q4 UNKNOWN | Tamam |
| 2 | Ölçüm iddiası yok | Tamam |
| 3 | Kararlılık sınırı §6 | Tamam |
| 4 | Web tablosu §9 | Tamam |
| 5 | Repo kanıtı §8 | Tamam |
| 6 | Authority son satır | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode