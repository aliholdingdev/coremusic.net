---
node_id: K004-dsp/measurement/measurement-fft-rta
node_type: module
status: draft
parent_id: K004-dsp/measurement
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# measurement-fft-rta — K004-dsp Derin Dal

Bu dosya, Neva Engine ölçüm katmanının FFT (Fast Fourier Transform) ve RTA
(Real Time Analyzer) bileşenlerini tanımlar. Kapsam: pencereleme, spektrum
türetme, kalibrasyon ve ölçüm sonuçlarının raporlanması. K004 kapsamında
ölçüm altyapısı **PLANNED**'tır; hiçbir ölçüm değeri bu dosyada iddia
edilmez, yalnızca yöntem ve araç tanımlanır. Q4 (measurement ↔ testing
ayrımı) `UNKNOWN` olarak taşınır.

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP/JS satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | FFT ve pencereleme yöntemi | Evet | — | Tasarım |
| 2 | RTA spektrum hesabı | Evet | — | Tasarım |
| 3 | Frekans bant eşlemesi | Evet | — | Tasarım |
| 4 | Ölçüm kalibrasyonu | Evet | Donanım referansı | UNKNOWN |
| 5 | Loudness ölçümü (LUFS) | Yöntem | Karar | measurement |
| 6 | Yazılım ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |
| 7 | Test stratejisi | Hayır | testing dosyası | Q4 |
| 8 | Donanım analiz cihazı | Hayır | Harici ekipman | Sınır |
| 9 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 10 | Kapalı çevrim kontrol | Referans | Q4 kararı | UNKNOWN |

## §2 Ölçüm türleri matrisi

| # | Ölçüm türü | Girdi | Çıktı | Standart | Durum |
|---|---|---|---|---|---|
| 1 | Spektrum (FFT) | Zaman serisi | Frekans bantları | — | Tasarım |
| 2 | RTA | Sürekli akış | Bant seviyeleri | — | Tasarım |
| 3 | Waterfall (CSD) | Blok serisi | Zaman-frekans | — | Opsiyonel |
| 4 | Impulse response | İmpuls | Yanıt | — | Tasarım |
| 5 | THD+N | Saf dalga | Bozulma oranı | IEEE referans | Ölçüm PLANNED |
| 6 | SNR | Susma | Gürültü tabanı | — | Ölçüm PLANNED |
| 7 | Loudness (LUFS) | Program | Sone | BS.1770 | §9 |
| 8 | Loudness Range | Program | LU | tech3342 | §9 |
| 9 | True-peak | OS akış | dBTP | tech3343 | limiter |
| 10 | Faz yanıtı | Süpürme | Faz grafiği | — | Tasarım |
| 11 | Grup gecikmesi | Süpürme | ms | — | filters |
| 12 | Kanal korelasyonu | Çok kanallı | ρ | — | spatial |
| 13 | Distorsiyon (kulaç) | Referans | Puan | BS.1116 | testing |
| 14 | Konum/odak | Çok kanallı | Harita | BS.2051 | spatial |
| 15 | Kapalı çevrim | Zincir | Sapma | Q4 | UNKNOWN |

## §3 FFT matrisi

| # | Parametre | Seçenek | Etki | Maliyet | Durum |
|---|---|---|---|---|---|
| 1 | Boyut (N) | 256-8192 | Frekans çözünürlüğü | N·log N | Tasarım |
| 2 | Frekans çözünürlüğü | fs/N | Bant genişliği | Hesap | Tasarım |
| 3 | Örtüşme (overlap) | %0-75 | Zaman takibi | Orta | Opsiyonel |
| 4 | Toplama (averaging) | Lin/log | Gürültü azaltma | Düşük | Tasarım |
| 5 | Pencere | §4 | Sızıntı | Düşük | §4 |
| 6 | Zero-padding | N ek | Interpolasyon | Düşük | Opsiyonel |
| 7 | Kompleks/gerçek | FFT/rFFT | Bellek | 2× fark | Tasarım |
| 8 | SIMD | Vektör | Hız | Donanım | Opsiyonel |
| 9 | Sabit nokta | Q format | Hız | Hassasiyet | Bilinmiyor |
| 10 | float32 | Zorunlu | ADR-017 | — | FROZEN |
| 11 | Twiddle faktörleri | Önceden | Tahsis | Constructor | L0 |
| 12 | Bit-reversal | Tablo | Hız | Constructor | L0 |
| 13 | Plan oluşturma | Başlangıç | RT olmayan | Non-RT | Tasarım |
| 14 | Blok bağımsızlığı | Streaming | Kalıcı durum | Baştan | Tasarım |
| 15 | Tepe ölçeği | dBFS | Gösterim | — | Tasarım |

## §4 Pencereleme matrisi

| # | Pencere | Lobe karakteri | Frekans hassasiyet | Amplitüd hassasiyet | Uygun | Durum |
|---|---|---|---|---|---|---|
| 1 | Rectangular | En dar main | En iyi | En kötü | Referans | Kıyas |
| 2 | Hann | Dengeli | İyi | İyi | Genel | Öneri |
| 3 | Hamming | Yan lobe | İyi | İyi | Genel | Öneri |
| 4 | Blackman-Harris | Düşük lobe | Orta | Çok iyi | Ölçüm | Öneri |
| 5 | Flat-top | Düz tepe | Kötü | En iyi | Amplitüd | Öneri |
| 6 | Kaiser | Parametrik | Ayarlı | Ayarlı | Esnek | Opsiyonel |
| 7 | Gaussian | Yumuşak | Orta | İyi | Zaman | Opsiyonel |
| 8 | Bartlett | Üçgen | İyi | Orta | Basit | Opsiyonel |
| 9 | Welch | Parçalama | Orta | İyi | Gürültü | Opsiyonel |
| 10 | Nuttall | Çok düşük lobe | Orta | Çok iyi | Ölçüm | Opsiyonel |
| 11 | Tukey | Parametrik | Orta | İyi | Esnek | Opsiyonel |
| 12 | Bohman | Düşük lobe | Orta | İyi | Ölçüm | Opsiyonel |
| 13 | Lanczos | Kübik | İyi | İyi | Opsiyonel | Opsiyonel |
| 14 | Pencere düzeltme katsayısı | amplitude | — | Ölçüm | Katsayı | Tasarım |
| 15 | Seçim kararı | K004 | Karar yok | — | UNKNOWN | Q-karar |

## §5 RTA bant eşleme matrisi

| # | Bant genişliği | Bant sayısı (20-20k) | Standart | Kullanım | Durum |
|---|---|---|---|---|---|
| 1 | 1/1 oktav | 10 | Gürültü ölçümü | Genel | Seçenek |
| 2 | 1/3 oktav | 31 | Saha ölçümü | Yaygın | Öneri |
| 3 | 1/6 oktav | 61 | İnce | Araştırma | Seçenek |
| 4 | Sabit FFT | N/2 | Grafik | Yaygın | Öneri |
| 5 | Bark | 24 | Psikoakustik | Araştırma | Opsiyonel |
| 6 | ERB | ~40 | Psikoakustik | Araştırma | Opsiyonel |
| 7 | Mel | 26 | Konuşma | Araştırma | Opsiyonel |
| 8 | 24 bant | Genel | Eşit aralık | Basit | Seçenek |
| 9 | 31 bant | 1/3 oktav | Karşılık | Yaygın | Öneri |
| 10 | Bant merkez frekansı | ISO 3 | Ölçüm | Sabit | Referans |
| 11 | Logaritmik eksen | Gösterim | Grafik | UI | UI |
| 12 | dB ölçeği | Gösterim | Grafik | UI | UI |
| 13 | Pik tutma (peak hold) | RTA | Gösterim | UI | Opsiyonel |
| 14 | Zaman ponderansı | F/S/I | BS.1770 | Loudness | §9 |
| 15 | Bant kararı | K004 | Karar yok | UNKNOWN | Q-karar |

## §6 Ölçüm zinciri matrisi

| # | Adım | Girdi | İşlem | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | Kaynak seçimi | Zincir çıkışı | Nokta seçimi | Örnek akış | Tasarım |
| 2 | Örnekleme | 48 kHz | Sabit | Zaman serisi | sartname |
| 3 | Pencereleme | Blok | §4 | Pencere blok | Tasarım |
| 4 | FFT | Pencere blok | §3 | Spektrum | Tasarım |
| 5 | Genlik | Kompleks | |x| | Genlik | Tasarım |
| 6 | Bant eşleme | Genlik | §5 | Bant seviyeleri | Tasarım |
| 7 | Ortalama | Blok serisi | Lin/log | Kararlı | Tasarım |
| 8 | Kalibrasyon | Referans | Katsayı | Ölçümlü | UNKNOWN |
| 9 | Gösterim | Bantlar | dB | UI | UI |
| 10 | Kayıt | Ölçüm | Biçim | Rapor | Opsiyonel |
| 11 | Eşik kontrolü | Ölçüm | Kriter | Uyarı | Tasarım |
| 12 | Kapalı çevrim | Sapma | Q4 | Kontrol | UNKNOWN |
| 13 | Tekrarlanabilirlik | Aynı girdi | Aynı sonuç | Kararlılık | testing |
| 14 | Doğrulama | Bilinen sinyal | Bilinen sonuç | Kabul | testing |
| 15 | Raporlama | Tümü | Özet | Tablo | Opsiyonel |

## §7 Repo kanıtı — analiz zinciri anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIEngine.php | 291 | `ffprobe -v quiet -print_format json -show_format -show_streams "%s"` | Medya analiz komutu | Anoloji |
| 2 | shared/src/AI/AIWorkflow.php | 109 | `$analysis = $this->engine->analyzeAudio($filePath);` | Analiz çağrısı | Anoloji |
| 3 | shared/src/Contracts/AI/AIEngineInterface.php | 36 | `public function analyzeAudio(string $filePath): array;` | Analiz sözleşmesi | Anoloji |
| 4 | shared/src/Contracts/AI/AIEngineInterface.php | 52 | `public function analyzeHardware(string $deviceId): array;` | Donanım analizi | Anoloji |
| 5 | engine/ (yok) | — | FFT/RTA C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP'dir; FFT/RTA C++ karşılığı **yoktur**
> (motor diskte mevcut değil). Anoloji olarak kullanılır; "implement edildi"
> olarak yazılmaz.

## §8 Frekans bandı referans matrisi

| # | Bant | Frekans aralığı | Ölçüm odağı | Bileşen | Durum |
|---|---|---|---|---|---|
| 1 | Sub-bass | 20-60 Hz | Enerji | crossover | Tasarım |
| 2 | Bass | 60-250 Hz | Denge | equalizer | Tasarım |
| 3 | Düşük-orta | 250-500 Hz | Bulanıklık | equalizer | Tasarım |
| 4 | Orta | 500-2000 Hz | Vokal | equalizer | Tasarım |
| 5 | Üst-orta | 2000-4000 Hz | Sertlik | equalizer | Tasarım |
| 6 | Tiz | 4000-6000 Hz | Netlik | equalizer | Tasarım |
| 7 | Parlaklık | 6000-10000 Hz | Hava | equalizer | Tasarım |
| 8 | Air | 10000-20000 Hz | Ferahlık | equalizer | Tasarım |
| 9 | LFE | 3-120 Hz | Kanal | spatial | ADR-017 |
| 10 | 8.1 kanallar | Tüm | Uzamsal | spatial | ADR-017 |
| 11 | Örnekleme üst sınır | fs/2 = 24 kHz | Nyquist | sartname | Sabit |
| 12 | Ölçüm penceresi | 20-20 kHz | Kulak | Referans | Tasarım |
| 13 | Alias riski | fs/2 üstü | OS | limiter | §3 |
| 14 | Denormal | Çok küçük | FTZ | L0 | Kural |
| 15 | Kalibrasyon referansı | Bilinmiyor | UNKNOWN | §6 | UNKNOWN |

## §9 Loudness ölçüm matrisi

| # | Ölçüm | Standart | Parametre | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | Momentary loudness | BS.1770 | 400 ms | LUFS | Yöntem |
| 2 | Short-term loudness | BS.1770 | 3 s | LUFS | Yöntem |
| 3 | Integrated loudness | BS.1770 | Program | LUFS | Yöntem |
| 4 | Loudness Range (LRA) | tech3342 | Kısım LU | LU | Yöntem |
| 5 | True-peak | tech3343 | OS | dBTP | limiter |
| 6 | Gating | BS.1770 | Eşik | Filtreli | Yöntem |
| 7 | K-weighting | BS.1770 | Filtre | Ağırlıklı | Yöntem |
| 8 | Kanal ağırlığı | BS.1770 | Kanal | Ağırlık | Yöntem |
| 9 | Hedef seviye | r128 | -23 LUFS | Hedef | Karar yok |
| 10 | Tolerans | r128 | Ölçüm | Sınır | Karar yok |
| 11 | Ölçüm doğrulaması | Bilinen sinyal | Kabul | testing | testing |
| 12 | Ölçüm aralığı | Kalibrasyon | Bilinmiyor | UNKNOWN | UNKNOWN |
| 13 | Donanım referansı | Bilinmiyor | UNKNOWN | Sınır | UNKNOWN |
| 14 | Yazılım doğruluğu | Ölçüm PLANNED | — | PLANNED | PLANNED |
| 15 | Rapor formatı | Karar yok | — | UNKNOWN | UNKNOWN |

## §10 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp::FFT sınıfı FFT hesaplaması sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1FFT.html | 2026-10-12 | 200 OK |
| JUCE dsp::WindowingFunction pencereleme sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1WindowingFunction.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_FFT.h FFT tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/frequency/juce_FFT.h | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_Windowing.h pencere tanımlar | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/frequency/juce_Windowing.h | 2026-10-12 | 200 OK |
| Apple vDSP kütüphanesi vDSP FFT işlevlerini tanımlar | https://developer.apple.com/documentation/accelerate/vdsp/fft | 2026-10-12 | 200 OK |
| ITU-R BS.1284 genel loudness ölçüm yöntemini tanımlar | https://www.itu.int/rec/R-REC-BS.1284/en | 2026-10-12 | 200 OK |

## §11 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | FFT doğruluğu | Bilinen sinyal testi | testing | testing |
| 2 | Pencere düzeltme | ≤0.1 dB | Katsayı testi | testing |
| 3 | Frekans çözünürlüğü | Tasarım | §3 | Tasarım |
| 4 | Ölçüm tekrarlanabilirliği | ≥%99 | Tekrar testi | testing |
| 5 | SNR ölçüm aralığı | Bilinmiyor | UNKNOWN | UNKNOWN |
| 6 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 7 | Tahsis | 0 B/audio thread | Profil | L0 |
| 8 | Gerçek zamanlı güncelleme | ≤ blok | Zamanlama | Tasarım |
| 9 | Ölçüm gecikmesi | ≤ bütçe | Impuls | PLANNED |
| 10 | Kalibrasyon | Bilinmiyor | UNKNOWN | UNKNOWN |
| 11 | Buffer | 512 örnek | sartname | Sabit |
| 12 | Örnekleme | 48 kHz | sartname | Sabit |
| 13 | Kapalı çevrim | Q4 | UNKNOWN | UNKNOWN |
| 14 | Raporda iddia | Yalnız ölçüm | Kural | Kural |
| 15 | Uydurma değer | Yasak | Kural | Kural |

## §12 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | testing | Test stratejisi | Q4 ayrımı | Q4 |
| 2 | limiter | True-peak ölçümü | tech3343 | §9 |
| 3 | equalizer | EQ ölçümü | Bant §8 | eq |
| 4 | crossover | Bant bölme | Bant §8 | crossover |
| 5 | spatial | Uzamsal ölçüm | §2.11/§2.13 | spatial |
| 6 | filters | Faz/gecikme ölçümü | §2.10/§2.11 | filters |
| 7 | dynamics | GR ölçümü | §2.15 | dynamics |
| 8 | pipeline | Ölçüm noktası | §6.1 | pipeline |
| 9 | reverb | RT60 ölçümü | §2.4 | reverb |
| 10 | measurement | Bu dosya | — | Bu |
| 11 | delay | Gecikme ölçümü | §2.4 | delay |
| 12 | compressor | Ölçüm besleme | §2.15 | compressor |

## §13 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Q4 belirsizliği | Yüksek | Orta | UNKNOWN taşı | Açık |
| 2 | Kalibrasyon eksikliği | Yüksek | Yüksek | PLANNED işaretle | Açık |
| 3 | Pencere hatası | Orta | Orta | Katsayı testi | testing |
| 4 | CPU aşımı | Orta | Yüksek | Bant azaltma | Ölçüm |
| 5 | Gerçek zamanlı tutamama | Orta | Orta | Güncelleme oranı | Ölçüm |
| 6 | Tahsis (plan) | Orta | Yüksek | Constructor plan | L0 |
| 7 | Yanlış ölçüm iddiası | Orta | Yüksek | Yalnız ölçüm yaz | Kural |
| 8 | Kalibrasyon referansı sahtesi | Düşük | Yüksek | UNKNOWN | Zero-Hallucination |
| 9 | Denormal yavaşlaması | Düşük | Yüksek | FTZ | L0 |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED işaretle | engine/ |

## §14 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Q4: measurement ↔ testing ayrımı nasıl? | Organizasyon | UNKNOWN |
| 2 | FFT boyutu ve pencere hangisi? | Doğruluk/performans | Karar yok |
| 3 | Kalibrasyon referansı ne olacak? | Ölçüm güveni | UNKNOWN |
| 4 | LUFS hedefi r128 -23 mü? | Karar | Karar yok |
| 5 | Kapalı çevrim kullanılacak mı? | Mimari | UNKNOWN |

## §15 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (plan, twiddle) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 6 | Kalibrasyon UNKNOWN | Evet | Zero-Hallucination |
| 7 | Ölçüm PLANNED işareti | Evet | PLANNED |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §16 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | measurement/measurement-kapsam.md | Kademe-2 |
| 3 | testing sınırı | testing/testing-kapsam.md | Q4 |
| 4 | limiter sınırı | limiter/limiter-kapsam.md | True-peak |
| 5 | pipeline örneği | pipeline/pipeline-ornekler.md | Ölçüm noktası |
| 6 | equalizer sınırı | equalizer/equalizer-kapsam.md | Bant |
| 7 | spatial sınırı | spatial-audio/spatial-audio-kapsam.md | Uzamsal |
| 8 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 9 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 10 | ADR-017 | .ai/.decisions | float32 |
| 11 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 12 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 13 | Analiz komutu | AIEngine.php:291 | Anoloji |
| 14 | Analiz çağrısı | AIWorkflow.php:109 | Anoloji |
| 15 | Analiz sözleşmesi | AIEngineInterface.php:36 | Anoloji |
| 16 | C++ motor | engine/ (yok) | Eksik |

## §17 Ölçüm protokolü tablosu

| # | Protokol | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | FFT doğrulama | Bilinen sinus | Bilinen bant | Hata ≤ eşik | testing |
| 2 | Pencere doğrulama | Bilinen genlik | Düzeltmeli | Hata ≤ eşik | testing |
| 3 | Bant eşleme | Bilinen frekans | Merkez bant | Doğru bant | testing |
| 4 | Tekrar | Aynı girdi | Aynı sonuç | ≥%99 | testing |
| 5 | THD+N | Saf dalga | Bozulma | <0.01% | PLANNED |
| 6 | SNR | Susma | Gürültü | >100 dB | PLANNED |
| 7 | LUFS | Bilinen program | Bilinen LUFS | Hata ≤ eşik | PLANNED |
| 8 | CPU | Tam zincir | Bütçe | ≤ bütçe | PLANNED |

## §18 Denetim ve rapor matrisi

| # | Denetim | Sıklık | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | Ölçüm değerlerinin kaynağı | Her sürüm | vault | Kural |
| 2 | Kalibrasyon durumu | Her sürüm | embedded | UNKNOWN |
| 3 | Q4 revizyonu | Karar anı | architect | Açık |
| 4 | URL yeniden doğrulama | 90 gün | vault | Planlı |
| 5 | PLANNED işaretleri | Her sürüm | vault | Kural |
| 6 | Uydurma ölçüm denetimi | Her sürüm | qa | Kural |

## §19 Terim sözlüğü

| Terim | Tanım |
|---|---|
| FFT | Hızlı Fourier dönüşümü — zaman→frekans |
| RTA | Gerçek zamanlı analizör — sürekli spektrum |
| Pencere | FFT bloğuna uygulanan zarf |
| Sızıntı | Pencere kenarından spektral yayılım |
| Bant genişliği | RTA bantlarının genişliği |
| Kalibrasyon | Ölçüm zinciri düzeltmesi |
| LUFS | Loudness units full scale |
| LRA | Loudness range — dinamik aralık |
| dBTP | True-peak desibel |
| Gating | Eşik altı reddetme |
| K-weighting | Loudness filtresi |
| Waterfall | Zaman-frekans grafiği |
| IR | Impulse response |
| Nyquist | fs/2 üst sınır |
| FTZ | Flush-to-zero |

## §20 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter 10 anahtar | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 6 URL |
| 4 | Repo kanıtı | AIEngine.php:291 |
| 5 | Ölçüm değeri iddiası yok | Tamam |
| 6 | Q4 UNKNOWN | Tamam |
| 7 | ≥500 satır | Ölçüldü |
| 8 | Authority son satır | Tamam |
| 9 | commit atılmadı | Kural |
| 10 | Mojibake/dolgu | Ölçüldü |

## §21 FFT blok yönetimi matrisi

| # | Blok yönetimi | Açıklama | Tahsis | Gecikme | Durum |
|---|---|---|---|---|---|
| 1 | Sabit blok (512) | sartname ile aynı | Baştan | 512 | Tasarım |
| 2 | 2× blok (1024) | Çift pencere | Baştan | 1024 | Opsiyonel |
| 3 | 4× blok (2048) | İnce çözünürlük | Baştan | 2048 | Opsiyonel |
| 4 | Örtüşmeli (%50) | İki blok kayması | Baştan | 256 | Opsiyonel |
| 5 | Örtüşmeli (%75) | Dört blok kayması | Baştan | 128 | Opsiyonel |
| 6 | İki tampon (ping-pong) | Yazma/okuma ayrımı | Baştan | Blok | L0 |
| 7 | Geçici blok | FFT çalışma alanı | Constructor | — | L0 |
| 8 | Twiddle tablosu | Sin/cos katsayıları | Constructor | — | L0 |
| 9 | Bit-reversal tablosu | İndeks yeniden düzenleme | Constructor | — | L0 |
| 10 | Pencere tablosu | §4 pencere katsayıları | Constructor | — | L0 |
| 11 | Bant eşleme tablosu | §5 indeks haritası | Constructor | — | L0 |
| 12 | Ortalama durumu | Kümülatif ortalama | Constructor | — | L0 |
| 13 | Pik tutma durumu | Peak-hold sönümleme | Constructor | — | L0 |
| 14 | dB dönüşüm tablosu | log yaklaşımı | Constructor | — | Opsiyonel |
| 15 | Çıkış tamponu | Bant seviyeleri | Constructor | — | L0 |

## §22 Ölçüm hatası kaynakları matrisi

| # | Hata kaynağı | Etki | Belirti | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Pencere sızıntısı | Yan lobe | Gürültü bandı | §4 pencere | Tasarım |
| 2 | Spectral leakage | Genlik hatası | Yanasma | Pencere | Tasarım |
| 3 | Scalloping loss | ±3.9 dB (rect) | Tepe hatası | Flat-top | Tasarım |
| 4 | Boyut sınırı | Çözünürlük | Bant birleşimi | N artırma | Tasarım |
| 5 | Nyquist aşımı | Alias | Sahte bant | OS / filtre | limiter |
| 6 | Quantization | Hassasiyet | Gürültü tabanı | float32 | ADR-017 |
| 7 | Denormal | Performans | Yavaşlama | FTZ | L0 |
| 8 | Toplama yetersiz | Salınım | Zıplama | Ortalama artı | Tasarım |
| 9 | Kalibrasyon kaybı | Sistemik | Yanlış seviye | Kalibrasyon | UNKNOWN |
| 10 | Ölçüm noktası yanlış | Bağlam | Beklenmedik | §6.1 | Tasarım |
| 11 | Kanal karışımı | Uzamsal | Yanlış kanal | Kanal seçimi | spatial |
| 12 | Blok kayması | Gecikme | Ölçüm gecikmesi | §21 | Tasarım |
| 13 | Log/lin ortalama | Değer | Farklı sonuç | Standart seç | Tasarım |
| 14 | Gösterim yuvarlama | Görsel | Yanlış okuma | Yuvarlama kuralı | UI |
| 15 | Uydurma değer | Güven | Sahte | Kural: yasak | Kural |

## §23 RTA ve UI gösterim matrisi

| # | Gösterim | Bilgi | Güncelleme | Renk | Durum |
|---|---|---|---|---|---|
| 1 | Bant sütunları | Frekans enerjisi | ≤ blok | dB eksenli | UI |
| 2 | Pik tutma | En yüksek | Kalıcı | İkinci renk | UI |
| 3 | Ortalama eğri | Kararlı | Yavaş | Çizgi | UI |
| 4 | Eşik çizgisi | Limit | Sabit | Kırmızı | UI |
| 5 | Renk gradyanı | Seviye | — | Isı | UI |
| 6 | Logaritmik eksen | Frekans | — | 20-20k | UI |
| 7 | dB ekseni | Seviye | — | -120..0 | UI |
| 8 | Zaman ölçeği | Akış hızı | Tasarım | — | UI |
| 9 | Ekran yenileme | ≤ 60 Hz | UI sınırlı | — | UI |
| 10 | Worker güncelleme | UI dışı | lock-free | — | UI |
| 11 | Canvas/DOM seçimi | Teknoloji | UI | — | UI |
| 12 | Düşük çözünürlük | CPU | Mod | — | UI |
| 13 | Kapsam modu | Tek bant | Mod | — | UI |
| 14 | Waterfall görünüm | Zaman | Opsiyonel | — | Opsiyonel |
| 15 | Rapor görünümü | Tablo | Opsiyonel | — | Opsiyonel |

## §24 Kapalı çevrim (Q4) matrisi

| # | Kapalı çevrim öğesi | Tanım | Risk | Durum |
|---|---|---|---|---|
| 1 | Ölçüm beslemesi | §6 çıktısı | Q4 | UNKNOWN |
| 2 | Referans | Kalibre edilmiş | Bilinmiyor | UNKNOWN |
| 3 | Sapma hesabı | Ölçüm - hedef | Bilinmiyor | UNKNOWN |
| 4 | Düzeltme | EQ/dynamics | Çevrim kararsızlığı | UNKNOWN |
| 5 | Kararlılık | Loop gain < 1 | Bilinmiyor | UNKNOWN |
| 6 | Kullanım amacı | Oda korreksiyon | AIWorkflow anolojisi | Anoloji |
| 7 | Kullanıcı onayı | Zorunlu | Kapalı çevrim otomatik | Kural |
| 8 | Manuel bypass | Zorunlu | Koruma | Kural |
| 9 | Ölçüm tekrarı | Aynı girdi | Tekrarlanabilirlik | testing |
| 10 | Kapatma | Q4 kararı | Mimari | UNKNOWN |

## §25 Standart ve referans haritası

| # | Standart | Kapsam | İlişki | Durum |
|---|---|---|---|---|
| 1 | ITU-R BS.1284 | Loudness ölçüm yöntemi | §10 | 200 OK |
| 2 | ITU-R BS.1770 | Loudness + true-peak | §9 | Yöntem |
| 3 | EBU tech3342 | Loudness Range | §9 | Yöntem |
| 4 | EBU tech3343 | True-peak | limiter | Yöntem |
| 5 | EBU r128 | Yayın hedefi | §9.9 | Karar yok |
| 6 | IEEE 1597 | Ses sistemi doğrulama | testing | testing |
| 7 | BS.1116 | Kulaç yöntemi | testing | testing |
| 8 | BS.2051 | Uzamsal konfigürasyon | spatial | spatial |
| 9 | JUCE docs | FFT/Windowing | §10 | 200 OK |
| 10 | Apple vDSP | FFT referansı | §10 | 200 OK |

## §26 Ölçüm kayıt ve raporlama matrisi

| # | Rapor alanı | İçerik | Biçim | Zorunlu | Durum |
|---|---|---|---|---|---|
| 1 | Test tarihi | YYYY-MM-DD | Metin | Evet | Tasarım |
| 2 | Sürüm | K004 sürümü | Metin | Evet | Tasarım |
| 3 | Konfigürasyon | Buffer, fs, kanal | Tablo | Evet | sartname |
| 4 | FFT ayarları | N, pencere, overlap | Tablo | Evet | §3-§4 |
| 5 | Kalibrasyon | Durum / referans | Metin | Evet | UNKNOWN |
| 6 | Spektrum verisi | Bant seviyeleri | Tablo/dosya | Evet | Tasarım |
| 7 | Loudness | LUFS/LRA (ölçüldüyse) | Sayı | Opsiyonel | PLANNED |
| 8 | True-peak | dBTP (ölçüldüyse) | Sayı | Opsiyonel | PLANNED |
| 9 | SNR/THD | Ölçüldüyse | Sayı | Opsiyonel | PLANNED |
| 10 | Ortam | Oda/sıcaklık | Metin | Opsiyonel | Bilinmiyor |
| 11 | Tekrar sayısı | N | Sayı | Evet | Tasarım |
| 12 | Sapma tablosu | Hedef vs ölçüm | Tablo | Evet | Tasarım |
| 13 | İmza | Ölçümcü | Metin | Evet | Tasarım |
| 14 | Saklama | Rapor arşivi | Depo | Karar yok | UNKNOWN |
| 15 | Tekrar üretme | Aynı girdi | Aynı sonuç | Zorunlu | testing |

## §27 Bant merkez frekansları referansı (1/3 oktav)

| # | Bant | Merkez (Hz) | Bant | Merkez (Hz) | Bant | Merkez (Hz) |
|---|---|---|---|---|---|---|
| 1 | 1 | 25 | 12 | 400 | 23 | 6300 |
| 2 | 2 | 31.5 | 13 | 500 | 24 | 8000 |
| 3 | 3 | 40 | 14 | 630 | 25 | 10000 |
| 4 | 4 | 50 | 15 | 800 | 26 | 12500 |
| 5 | 5 | 63 | 16 | 1000 | 27 | 16000 |
| 6 | 6 | 80 | 17 | 1250 | 28 | 20000 |
| 7 | 7 | 100 | 18 | 1600 | 29 | 25000 |
| 8 | 8 | 125 | 19 | 2000 | 30 | 31500 |
| 9 | 9 | 160 | 20 | 2500 | 31 | 40000 |
| 10 | 10 | 200 | 21 | 3150 | — | — |
| 11 | 11 | 250 | 22 | 4000 | — | — |

> Bu tablo referans bilgisidir; K004 ölçüm aralığı 20-20 kHz'tir (§8.12).
> Ölçüm değerleri bu dosyada **iddia edilmez** — yalnızca bant tanımıdır.

## §28 Ölçüm dosyası öz-kontrol ek satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Q4 UNKNOWN taşındı | Tamam |
| 3 | Uydurma ölçüm yok | Tamam |
| 4 | Kaynak etiketli JUCE referansı | Tamam |
| 5 | CJK/mojibake 0 | Ölçüldü |
| 6 | Authority son satır | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode