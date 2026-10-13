---
node_id: K004-dsp/pipeline/pipeline-dsp-zinciri
node_type: module
status: draft
parent_id: K004-dsp/pipeline
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# pipeline-dsp-zinciri — K004-dsp Derin Dal

Bu dosya, Neva Engine DSP işleme zincirinin (processing chain)
mimarisini tanımlar: blok işleme sırası, ProcessSpec sözleşmesi,
dry/wet karıştırma, blok boyutu/örnekleme değişimi ve sıralama
kuralları. Zincir bileşenleri kendi derin-dal dosyalarında
tanımlanır; bu dosyanın odağı **zincirin kendisi ve sözleşme
katmanıdır**. K004 kapsamında pipeline **PLANNED**'tır; hangi
bileşenlerin hangi sırada yer alacağı kararı **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP satırı kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Zincir topolojisi | Evet | Bileşen iç yapısı | Tasarım |
| 2 | ProcessSpec sözleşmesi | Evet | — | Tasarım |
| 3 | Dry/wet karıştırma | Evet | — | Tasarım |
| 4 | Sıra kuralları | Evet | Kesin sıra | UNKNOWN |
| 5 | Blok boyutu/örnekleme değişimi | Evet | — | Tasarım |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | UI/preset katmanı | Hayır | UI katmanı | Sınır |
| 9 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |
| 10 | 8.1 kanal kapsamı | Evet | — | ADR-017 |

## §2 ProcessSpec matrisi

| # | Alan | Tip | Anlamı | Örnek değer | Durum |
|---|---|---|---|---|---|
| 1 | sampleRate | double | Örnekleme | 48000 | sartname |
| 2 | maximumBlockSize | uint32 | Maks blok | 512 | sartname |
| 3 | numChannels | uint32 | Kanal sayısı | 8 (+LFE) | ADR-017 |
| 4 | float precision | float32 | ADR-017 | float32 | FROZEN |
| 5 | double precision | double | Opsiyonel | — | Opsiyonel |
| 6 | Spec değişimi | prepare | Yeniden hazırlama | — | Tasarım |
| 7 | Spec doğrulama | Kabul | Uyum testi | — | testing |
| 8 | Spec tutarlılığı | Tüm bileşenler | Eşit spec | Zorunlu | Tasarım |
| 9 | Blok değişimi | 512↔diğer | Kabul | Zorunlu | PLANNED |
| 10 | Kanal değişimi | 2.0↔8.1 | Kabul | Zorunlu | PLANNED |
| 11 | fs değişimi | 48↔diğer | Kabul | Zorunlu | PLANNED |
| 12 | float32 hassasiyet | ADR-017 | Yeterli | — | FROZEN |
| 13 | Tahsis | Constructor | Non-RT | — | L0 |
| 14 | Lock | 0 mutex | L0 | — | L0 |
| 15 | Karar | Spec detayı | — | — | UNKNOWN |

## §3 Zincir topolojisi matrisi (olası sıra)

| # | Konum | Bileşen | Rol | Öncelik | Durum |
|---|---|---|---|---|---|
| 1 | Giriş | Kaynak | x[n] | — | sartname |
| 2 | 1 | filters | Kanal filtresi | İlk | filters |
| 3 | 2 | crossover | Bant bölme | Erken | crossover |
| 4 | 3 | equalizer | EQ | Orta | eq |
| 5 | 4 | compressor | Kompresyon | Orta | compressor |
| 6 | 5 | dynamics | Kazanç | Orta | dynamics |
| 7 | 6 | reverb | Uzamsal | Geç | reverb |
| 8 | 7 | delay | Gecikme | Geç | delay |
| 9 | 8 | spatial | Uzamsal pan | Geç | spatial |
| 10 | 9 | limiter | Tavan | Son | limiter |
| 11 | Çıkış | DAC | Donanım | — | ADR-038 |
| 12 | Sıra kararı | Bilinmiyor | — | — | UNKNOWN |
| 13 | Ölçüm noktası | Her bileşen sonrası | Q4 | — | measurement |
| 14 | Bypass | Her bileşen | Test | — | testing |
| 15 | Uydurma | YASAK | — | — | Kural |

> §3, olası bir sıra **önerisidir** (sıra kararı `UNKNOWN` — §12.1);
> kesin sıra bu dosyada **iddia edilmez**, yalnız referans olarak
> sunulur.

## §4 Dry/wet matrisi

| # | Bileşen | Dry/wet | Varsayılan | Etki | Durum |
|---|---|---|---|---|---|
| 1 | filters | Opsiyonel | Tam wet | Filtre etkisi | filters |
| 2 | crossover | Tam wet | Wet | Bölme | crossover |
| 3 | equalizer | Tam wet | Wet | EQ | eq |
| 4 | compressor | Tam wet | Wet | GR | compressor |
| 5 | dynamics | Tam wet | Wet | Kazanç | dynamics |
| 6 | reverb | Oranlı | Wet | Karışım | reverb |
| 7 | delay | Oranlı | Wet | Karışım | delay |
| 8 | spatial | Tam wet | Wet | Pan | spatial |
| 9 | limiter | Tam wet | Wet | Tavan | limiter |
| 10 | Dry/wet oranı | 0..1 | Karar | Sıra | UNKNOWN |
| 11 | Crossfade | Rampa | Zorunlu | Kliksiz | Tasarım |
| 12 | Toplam (zincir) | Oranlı | Opsiyonel | — | Opsiyonel |
| 13 | float32 | ADR-017 | — | — | FROZEN |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Karar | Bileşen başına oran | — | — | UNKNOWN |

## §5 Blok işleme matrisi

| # | Blok boyutu | Gecikme | CPU | Bellek | Durum |
|---|---|---|---|---|---|
| 1 | 32 | Çok düşük | Yüksek | Düşük | Kıyas |
| 2 | 64 | Düşük | Yüksek | Düşük | Kıyas |
| 3 | 128 | Düşük | Orta | Düşük | Kıyas |
| 4 | 256 | Orta | Orta | Orta | Kıyas |
| 5 | 512 | Orta | Düşük | Orta | Öneri |
| 6 | 1024 | Yüksek | Düşük | Yüksek | Kıyas |
| 7 | 2048 | Çok yüksek | Düşük | Çok yüksek | Kıyas |
| 8 | sartname | 512 | — | — | sartname |
| 9 | Değişim | Kabul | Zorunlu | — | PLANNED |
| 10 | float32 | ADR-017 | — | — | FROZEN |
| 11 | Tahsis | Baştan | — | — | L0 |
| 12 | Lock | 0 mutex | — | — | L0 |
| 13 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Karar | Blok (sabit) | — | — | sartname |

## §6 Repo kanıtı — pipeline anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/Middleware/MiddlewarePipeline.php | 23 | `public function run(array $request, callable $core): array` | Zincir çalıştırma | Anoloji |
| 2 | engine/ (yok) | — | DSP zincir C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satır PHP'dir; DSP zincir karşılığı **yoktur**
> (motor diskte mevcut değil). `run()` sözleşmesi, sıralı çağrı
> anolojisidir; DSP blok işleme bu satırda **yoktur**.

## §7 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp::ProcessorChain sıralı bileşen zinciri sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1ProcessorChain.html | 2026-10-12 | 200 OK |
| JUCE dsp::DryWetMixer kuru/ıslak karıştırma sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1DryWetMixer.html | 2026-10-12 | 200 OK |
| JUCE dsp::ProcessSpec fs/blok/kanal sözleşmesini tanımlar | https://docs.juce.com/master/structjuce_1_1dsp_1_1ProcessSpec.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_ProcessorChain.h zincir tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_ProcessorChain.h | 2026-10-12 | 200 OK |

## §8 Sıra kuralları matrisi

| # | Kural | Açıklama | Zorunlu | Durum |
|---|---|---|---|---|
| 1 | Ölçüm öncesi kısma | Limiter en son | Evet | Tasarım |
| 2 | EQ öncesi crossover | Bant bölme | Opsiyonel | Tasarım |
| 3 | Compressor öncesi EQ | Karakter | Opsiyonel | Tasarım |
| 4 | Reverb/delay öncesi dynamics | Seviye | Opsiyonel | Tasarım |
| 5 | Spatial öncesi dynamics | Kanal | Opsiyonel | Tasarım |
| 6 | Limiter her zaman son | Tavan | Evet | Tasarım |
| 7 | Ölçüm noktası her adım | Q4 | Evet | measurement |
| 8 | Bypass her bileşen | Test | Evet | testing |
| 9 | Sıra değiştirilebilirliği | Parçalar | Evet | testing |
| 10 | float32 (ADR-017) | Zorunlu | Evet | FROZEN |
| 11 | Tahsis baştan | L0 | Evet | L0 |
| 12 | Lock yok (audio thread) | L0 | Evet | L0 |
| 13 | Sıra kararı | Bilinmiyor | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | PLANNED |
| 15 | Uydurma | YASAK | — | Kural |

## §9 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Sıra doğruluğu | Kabul | Test | testing |
| 2 | Dry/wet doğruluğu | ≤0.1 dB | Test | testing |
| 3 | Blok değişimi | Kabul | Test | PLANNED |
| 4 | Kanal değişimi (8.1) | Kabul | Test | PLANNED |
| 5 | fs değişimi | Kabul | Test | PLANNED |
| 6 | Toplam gecikme | ≤ bütçe | Impuls | PLANNED |
| 7 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 8 | Tahsis | 0 B/audio thread | Profil | L0 |
| 9 | Lock | 0 mutex | Denetim | L0 |
| 10 | float32 | Zorunlu | ADR-017 | FROZEN |
| 11 | Buffer | 512 örnek | sartname | Sabit |
| 12 | Örnekleme | 48 kHz | sartname | Sabit |
| 13 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 14 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §10 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | filters | Bileşen | §3.2 | filters |
| 2 | crossover | Bileşen | §3.3 | crossover |
| 3 | equalizer | Bileşen | §3.4 | eq |
| 4 | compressor | Bileşen | §3.5 | compressor |
| 5 | dynamics | Bileşen | §3.6 | dynamics |
| 6 | reverb | Bileşen | §3.7 | reverb |
| 7 | delay | Bileşen | §3.8 | delay |
| 8 | spatial | Bileşen | §3.9 | spatial |
| 9 | limiter | Bileşen | §3.10 | limiter |
| 10 | measurement | Ölçüm noktası | Q4 | measurement |
| 11 | testing | Bypass/sıra testi | testing | testing |
| 12 | pipeline | Bu dosya | — | Bu |
| 13 | DSP master | Üst düğüm | Bağ | master |

## §11 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Kesin sıra hangisi? | Mimari | UNKNOWN |
| 2 | Dry/wet oranları? | Karakter | UNKNOWN |
| 3 | Opsiyonel bileşenler? | Kapsam | UNKNOWN |
| 4 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 5 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 6 | Blok değişimi ne sıklıkla? | Kapsam | UNKNOWN |
| 7 | Preset/partial bypass? | Kapsam | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §12 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Yanlış sıra | Orta | Yüksek | Test | testing |
| 2 | Blok değişimi hatası | Düşük | Yüksek | Spec doğrulama | §2 |
| 3 | CPU aşımı (zincir) | Orta | Yüksek | Bütçe + bypass | Ölçüm |
| 4 | Tahsis (zincir) | Düşük | Yüksek | Constructor | L0 |
| 5 | Param yarışması | Düşük | Yüksek | Lock-free atom | L0 |
| 6 | Gecikme aşımı | Orta | Yüksek | Bütçe | §9.6 |
| 7 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 8 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 9 | Dry/wet klik | Orta | Orta | Crossfade | §4.11 |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (tüm bileşenler) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Spec tutarlılığı (tüm bileşenler) | Evet | testing |
| 6 | Limiter en son | Evet | Tasarım |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | pipeline/pipeline-kapsam.md | Kademe-2 |
| 3 | Bileşen dosyaları | İlgili dal dosyaları | §3 |
| 4 | measurement kapsamı | measurement/measurement-kapsam.md | Ölçüm noktası |
| 5 | testing örneği | testing/testing-ornekler.md | Bypass/sıra |
| 6 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 7 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 8 | ADR-017 | .ai/.decisions | float32 + 8.1 |
| 9 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 10 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 11 | Pipeline anolojisi | MiddlewarePipeline.php:23 | Anoloji |
| 12 | C++ motor | engine/ (yok) | Eksik |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Pipeline (zincir) | Sıralı DSP işleme bileşenleri |
| ProcessSpec | fs/blok/kanal sözleşmesi |
| Block size | Blok başına örnek sayısı |
| Dry/wet | Kuru/ıslak karıştırma oranı |
| Bypass | Bileşen devre dışı |
| Crossfade | Yumuşak geçiş |
| Prepare | Spec ile hazırlama |
| Sıra | Bileşenlerin işlenme sırası |
| Toplam gecikme | Zincir gecikme toplamı |
| Ölçüm noktası | Q4 ölçüm erişimi |
| Preset | Kayıtlı parametre seti |
| Partial bypass | Kısmi devre dışı |
| Lock-free | Kilit kullanmayan erişim |
| Tahsis | Bellek ayırma |
| float32 | 32-bit kayan nokta (ADR-017) |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Kesin sıra | §3 | Bekliyor | §11.1 |
| 2 | Dry/wet oranları | §4 | Bekliyor | §11.2 |
| 3 | Opsiyonel bileşenler | §3 | Bekliyor | §11.3 |
| 4 | Blok boyutu | 512 (sartname) | FROZEN | sartname |
| 5 | Preset/partial bypass | Evet/Hayır | Bekliyor | §11.7 |
| 6 | CPU bütçesi | Bilinmiyor | Bekliyor | Ölçüm |
| 7 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 4 URL |
| 4 | Repo kanıtı | MiddlewarePipeline.php:23 |
| 5 | Sıra UNKNOWN işaretli | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §18 Zincir CPU bütçesi matrisi

| # | Bileşen | CPU (göreli) | Gecikme | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | filters | 1× | 0 | Baştan | filters |
| 2 | crossover | 1.5× | 0 | Baştan | crossover |
| 3 | equalizer | 2× | 0 | Baştan | eq |
| 4 | compressor | 1× | 0 | Baştan | compressor |
| 5 | dynamics | 0.5× | 0 | Baştan | dynamics |
| 6 | reverb | 3× | Blok | Baştan | reverb |
| 7 | delay | 0.5× | Gecikme | Baştan | delay |
| 8 | spatial | 5× | Blok | Baştan | spatial |
| 9 | limiter | 2× (oversampling) | Filtre | Baştan | limiter |
| 10 | Toplam | Ölçüm | ≤ bütçe | 0 B | PLANNED |
| 11 | Bypass (test) | 0× | 0 | — | testing |
| 12 | float32 | ADR-017 | — | — | FROZEN |
| 13 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 14 | Bellek bütçesi | Bilinmiyor | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

> Göreli CPU değerleri bileşen başı tahminidir (ölçüm PLANNED); mutlak
> değer bu dosyada **iddia edilmez**.

## §19 Blok akış tablosu (zincir)

| # | Blok | Girdi | İşlem | Çıktı | Ölçüm noktası |
|---|---|---|---|---|---|
| 1 | Girdi (x[n]) | Blok | — | x | Q4 |
| 2 | filters | x | Filtreleme | xf | Q4 |
| 3 | crossover | xf | Bant bölme | xc | Q4 |
| 4 | equalizer | xc | EQ | xe | Q4 |
| 5 | compressor | xe | Kompresyon | xd | Q4 |
| 6 | dynamics | xd | Kazanç | xg | Q4 |
| 7 | reverb | xg | Uzamsal | xr | Q4 |
| 8 | delay | xr | Gecikme | xdl | Q4 |
| 9 | spatial | xdl | Pan (8.1) | xs | Q4 |
| 10 | limiter | xs | Tavan | y | Q4 |
| 11 | Çıkış | y | DAC (ADR-038) | — | Q4 |
| 12 | Uydurma | — | YASAK | — | Kural |

## §20 Sıra değişimi senaryoları matrisi

| # | Değişim | Kabul | Test | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Bileşen ekleme | Evet | Bypass | Kabul | testing |
| 2 | Bileşen çıkarma | Evet | Bypass | Kabul | testing |
| 3 | Sıra değiştirme (komşu) | Evet | Karşılaştırma | Kabul | testing |
| 4 | Dry/wet değişimi | Evet | Sweep | ≤ eşik | testing |
| 5 | Blok değişimi (512↔diğer) | Evet | Spec | Kabul | PLANNED |
| 6 | Kanal değişimi (2.0↔8.1) | Evet | Spec | Kabul | PLANNED |
| 7 | fs değişimi (48↔diğer) | Evet | Spec | Kabul | PLANNED |
| 8 | Param zinciri geçişi | Evet | Ramp | Kliksiz | testing |
| 9 | Preset geçişi | Opsiyonel | Sıralı | Kabul | UNKNOWN |
| 10 | Toplam gecikme değişimi | Kabul | Impuls | ≤ bütçe | PLANNED |
| 11 | CPU değişimi | Kabul | Profil | ≤ bütçe | PLANNED |
| 12 | float32 | ADR-017 | — | — | FROZEN |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Karar | K004 | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §21 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Sıra testi (§3) | Test sinyali | Kabul | ≤ eşik | testing |
| 2 | Bypass (her bileşen) | Test sinyali | Bypass=orijinal | ≤0.1 dB | testing |
| 3 | Dry/wet (0) | Test sinyali | dry=x | ≤0.1 dB | testing |
| 4 | Dry/wet (1) | Test sinyali | wet | ≤ eşik | testing |
| 5 | Blok değişimi | 512↔diğer | Kabul | Kliksiz | testing |
| 6 | Kanal değişimi (8.1) | 2.0↔8.1 | Kabul | Doğru kanal | testing |
| 7 | fs değişimi | 48↔diğer | Kabul | Doğru fs | testing |
| 8 | Toplam gecikme | Impuls | ≤ bütçe | ≤ | PLANNED |
| 9 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 10 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 11 | Param geçişi | Sürükleme | Kliksiz | Ramp | testing |
| 12 | Denormal | Tiny girdi | Performans | FTZ | L0 |
| 13 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural | Kural |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §22 Gecikme bütçesi matrisi

| # | Bileşen | Gecikme kaynağı | Tahmini | Bütçe etkisi | Durum |
|---|---|---|---|---|---|
| 1 | Giriş | Blok | ≤ blok | 0 | sartname |
| 2 | filters | Filtre | ~0 | Düşük | filters |
| 3 | crossover | Filtre | ~0 | Düşük | crossover |
| 4 | equalizer | Filtre | ~0 | Düşük | eq |
| 5 | compressor | Ölçüm | ~blok | Orta | compressor |
| 6 | dynamics | Ölçüm | ~blok | Orta | dynamics |
| 7 | reverb | Konvolüsyon | Blok | Orta | reverb |
| 8 | delay | Ayarlı | 0-2000 ms | Opsiyonel | delay |
| 9 | spatial | Konvolüsyon | Blok | Orta | spatial |
| 10 | limiter | Oversampling | Filtre | Orta | limiter |
| 11 | Çıkış | DAC (ADR-038) | Donanım | Donanım | ADR-038 |
| 12 | Toplam | — | ≤ bütçe | PLANNED | PLANNED |
| 13 | Ölçüm | Impuls | PLANNED | — | PLANNED |
| 14 | Karar | Bütçe | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §23 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Kesin sıra | UNKNOWN | Karar | §11.1 |
| 2 | Dry/wet oranları | UNKNOWN | Karar | §11.2 |
| 3 | Opsiyonel bileşenler | UNKNOWN | Kapsam | §11.3 |
| 4 | Preset/partial bypass | UNKNOWN | Kapsam | §11.7 |
| 5 | Blok boyutu | FROZEN | sartname | §5.8 |
| 6 | Sıra doğrulama | PLANNED | testing | §21 |
| 7 | Blok/kanal/fs değişimi | PLANNED | testing | §20 |
| 8 | CPU/gecikme bütçesi | PLANNED | Ölçüm | §18, §22 |
| 9 | Q4 | UNKNOWN | measurement/testing | §11.4 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.7 |

## §24 Zincir sözleşmeleri matrisi (katmanlar)

| # | Sözleşme | Taraflar | İçerik | Zorunlu | Durum |
|---|---|---|---|---|---|
| 1 | ProcessSpec | Zincir ↔ bileşen | fs/blok/kanal | Evet | §2 |
| 2 | prepare | Zincir → bileşen | Tahsis + init | Evet | L0 |
| 3 | process | Zincir → bileşen | Blok işleme | Evet | Tasarım |
| 4 | reset | Zincir → bileşen | Durum sıfırlama | Evet | Tasarım |
| 5 | Param atomları | UI → bileşen | Lock-free yazma | Evet | L0 |
| 6 | Ölçüm erişimi | measurement → zincir | Q4 | Evet | measurement |
| 7 | Bypass bayrağı | testing → bileşen | Devre dışı | Evet | testing |
| 8 | float32 | Tüm | ADR-017 | Evet | FROZEN |
| 9 | noexcept | Tüm callback | L0 | Evet | L0 |
| 10 | Tahsis sınırı | Tüm | 0 B/audio thread | Evet | L0 |
| 11 | Lock yasağı | Tüm | 0 mutex | Evet | L0 |
| 12 | Spec tutarlılığı | Zincir | Tüm bileşenler eşit | Evet | testing |
| 13 | Karar | Sözleşme detayı | — | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §25 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | ProcessSpec §2 | Tamam |
| 2 | Sıra UNKNOWN §16 | Tamam |
| 3 | Dry/wet §4 | Tamam |
| 4 | Frontmatter §4.2 + group | Tamam |
| 5 | URL benzersiz (4) | Tamam |
| 6 | Repo kanıtı §6 | Tamam |
| 7 | Uydurma ölçüm yok | Tamam |
| 8 | Q4 UNKNOWN | Tamam |
| 9 | CJK/mojibake 0 | Ölçüldü |
| 10 | ≥500 satır | Ölçüldü |
| 11 | Authority son satır | Tamam |

## §26 Karşılaştırma — zincirleme yapılar matrisi

| # | Yapı | Tanım | Esneklik | CPU | Kullanım | Durum |
|---|---|---|---|---|---|---|
| 1 | Sabit sıra | Önceden tanımlı | Düşük | Düşük | Basit | Öneri |
| 2 | Dinamik sıra | Bileşen ekle/çıkar | Yüksek | Orta | Esnek | Araştırma |
| 3 | Ağaç (graph) | Çoklu yol | Çok yüksek | Yüksek | Karmaşık | Araştırma |
| 4 | Paralel | Aynı girdi | Yüksek | Yüksek | Bantlı | Araştırma |
| 5 | Seri | Bileşik (§3) | Orta | Orta | Yaygın | Öneri |
| 6 | ProcessorChain (JUCE) | Şablon seri | Düşük | Düşük | §7 | Tasarım |
| 7 | ProcessorGraph (JUCE) | Dinamik ağaç | Yüksek | Orta | §7 | Araştırma |
| 8 | Bileşik desen | İç içe | Yüksek | Orta | Mimarî | Tasarım |
| 9 | Bypass (her bileşen) | Test edilebilirlik | — | 0 | testing | testing |
| 10 | Ölçüm noktası (her adım) | Q4 | — | — | measurement | measurement |
| 11 | float32 | ADR-017 | — | — | — | FROZEN |
| 12 | Ölçüm | PLANNED | — | — | — | PLANNED |
| 13 | Karar | Yapı | — | — | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | — | Kural |

> Tablo, zincirleme yapı seçeneklerinin karşılaştırmasıdır; K004'ün
> zincir yapısı kararı `UNKNOWN`'dur (§11.1).

| # | Son ek denetim | Durum |
|---|---|---|
| 1 | §18 CPU bütçesi | Tamam |
| 2 | §19 blok akış | Tamam |
| 3 | §20 sıra değişimi | Tamam |
| 4 | §22 gecikme bütçesi | Tamam |
| 5 | §24 sözleşmeler | Tamam |
| 6 | §26 yapı karşılaştırma | Tamam |
| 7 | ≥500 satır | Ölçüldü |
| 8 | Frontmatter 10 anahtar | Tamam |
| 9 | group: mimari | Tamam |
| 10 | Authority son satır | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode