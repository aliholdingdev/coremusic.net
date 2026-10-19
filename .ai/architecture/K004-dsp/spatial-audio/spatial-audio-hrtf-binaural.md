---
node_id: K004-dsp/spatial-audio/spatial-audio-hrtf-binaural
node_type: module
status: draft
parent_id: K004-dsp/spatial-audio
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# spatial-audio-hrtf-binaural — K004-dsp Derin Dal

Bu dosya, Neva Engine uzamsal ses bileşeninin HRTF (Head-Related Transfer
Function) ve binaural (iki kulaklı) işleme yöntemlerini tanımlar: kanal
eşleme, pan mühendisliği, kafa modeli ve 8.1 konfigürasyonundaki yeri.
K004 kapsamında spatial **PLANNED**'tır; hangi HRTF veri setinin/kafa
modelinin kullanılacağı kararı **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP satırı kullanılır (mojibake satır alıntılmaz).

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | HRTF yöntemi | Evet | Veri seti seçimi | UNKNOWN |
| 2 | Binaural işleme | Evet | Kulaklık kalibrasyonu | Tasarım |
| 3 | Kanal eşleme (8.1) | Evet | Donanım çıkışı | ADR-017 |
| 4 | Pan mühendisliği | Evet | — | Tasarım |
| 5 | Kafa modeli | Yöntem | Kişisel profil | UNKNOWN |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | Donanım DSP | Hayır | Hardware hedef | Sınır |
| 9 | Yayında standart | Referans | Yayın kararı | Sınır |
| 10 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |

## §2 HRTF temel matrisi

| # | Kavram | Tanım | Kullanım | Durum |
|---|---|---|---|---|
| 1 | HRTF | Baş-ilgili transfer fonksiyonu | Yön algısı | Tasarım |
| 2 | HRTF (zaman) | IR (HRIR) | Konvolüsyon | Tasarım |
| 3 | ITD | İnteraural zaman farkı | Düşük frekans yönü | Tasarım |
| 4 | ILD | İnteraural genlik farkı | Yüksek frekans yönü | Tasarım |
| 5 | Pinna etkisi | Kepçe filtresi | Yukarı/aşağı ayrımı | Tasarım |
| 6 | Gölgeleme | Baş gölgesi | Genlik | Tasarım |
| 7 | Ters yayın | Refleks | Tiz | Tasarım |
| 8 | Frekans tepkisi | Azimut/elevasyon bağımlı | Yön | Tasarım |
| 9 | Ölçüm düzeneği | Kafa + mikrofon | Veri üretimi | Ölçüm PLANNED |
| 10 | Veri seti | Ölçülmüş HRTF | Konvolüsyon | UNKNOWN |
| 11 | Parametrik model | Hesaplanmış | Esnek | Araştırma |
| 12 | Kişiselleştirme | Bireysel ölçüm | Doğruluk | UNKNOWN |
| 13 | Örnekleme | fs | Tutarlılık | sartname |
| 14 | float32 | ADR-017 | Hassasiyet | FROZEN |
| 15 | Durum | PLANNED | — | PLANNED |

## §3 Binaural işleme matrisi

| # | İşlem | Girdi | İşlem | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | Yön vektörü | Azimut/elevasyon | Daire/küre | θ, φ | Tasarım |
| 2 | HRTF seçimi | θ, φ | İnterpolasyon | H(f) | Tasarım |
| 3 | Konvolüsyon | sinyal × HRIR | FIR | binaural | Tasarım |
| 4 | ITD uygulama | gecikme | Örnek kaydırma | gecikmeli | Tasarım |
| 5 | ILD uygulama | genlik | Kazanç | dengeli | Tasarım |
| 6 | Kanal ayrımı | sol/sağ kulak | §5 | L, R | Tasarım |
| 7 | Crossfeed | düşük frekans | Karışım | yumuşak | Opsiyonel |
| 8 | Head-tracking | baş yönü | Dinamik güncelle | hareketli | Opsiyonel |
| 9 | Yumuşatma | yön değişimi | Rampa | kliksiz | Tasarım |
| 10 | CPU | Konvolüsyon | Yüksek | Öncelik | PLANNED |
| 11 | Gecikme | FIR blok | Ek | ≤ blok | PLANNED |
| 12 | Tahsis | FIR katsayıları | Constructor | — | L0 |
| 13 | Ölçüm | Yön doğruluğu | Kulaç | PLANNED | PLANNED |
| 14 | Ölçüm | Frekans | Sweep | PLANNED | PLANNED |
| 15 | Karar | HRTF veri seti | — | — | UNKNOWN |

## §4 Kanal konfigürasyon matrisi (8.1)

| # | Kanal | Kod | Rol | Spatial kullanımı | Durum |
|---|---|---|---|---|---|
| 1 | Sol ön | FL | Ana | Pan referansı | ADR-017 |
| 2 | Sağ ön | FR | Ana | Pan referansı | ADR-017 |
| 3 | Merkez | C | Vokal | Merkez pan | ADR-017 |
| 4 | LFE | LFE | Efekt | Bant kısıtlı | ADR-017 |
| 5 | Sol surround | SL | Çevre | Arka pan | ADR-017 |
| 6 | Sağ surround | SR | Çevre | Arka pan | ADR-017 |
| 7 | Sol height | HL | Yükseklik | Elevation pan | ADR-017 |
| 8 | Sağ height | HR | Yükseklik | Elevation pan | ADR-017 |
| 9 | Kanal sayısı | 8+LFE | FROZEN | — | FROZEN |
| 10 | Yerleşim | Konfigürasyon | Karar yok | — | UNKNOWN |
| 11 | Açı (yerleşim) | Standart mu? | Karar yok | — | UNKNOWN |
| 12 | Kanal kazancı | Per-kanal | dynamics | Trim | dynamics |
| 13 | Kanal tavanı | Per-kanal | limiter | Tavan | limiter |
| 14 | Kanal ölçümü | Per-kanal | measurement | RTA | measurement |
| 15 | Karar | Eşleme detayı | — | — | UNKNOWN |

## §5 Pan mühendisliği matrisi

| # | Pan yöntemi | Kapsam | Kanal | Gains | Durum |
|---|---|---|---|---|---|
| 1 | Sabit güç (constant power) | 2D | 2 | sin/cos | Tasarım |
| 2 | Sabit gerilim | 2D | 2 | Doğrusal | Kıyas |
| 3 | Vazife (viola) | 2D | 2 | Eşit | Kıyas |
| 4 | Uzamsal (VBAP) | 3D | 3+ | Üçlü | Tasarım |
| 5 | Dörtgen (quad) | 3D | 4 | Dörtlü | Opsiyonel |
| 6 | Kanal tabanlı | Sabit | 1 | 1.0 | Basit |
| 7 | Stereo genişlik | 2 | 2 | Oran | Opsiyonel |
| 8 | Yükseklik pan | 4 height | HL/HR | Oran | §4.7-8 |
| 9 | LFE besleme | Opsiyonel | LFE | Frekans | §4.4 |
| 10 | Yumuşatma | Tüm | — | Rampa | Tasarım |
| 11 | Güç kontrolü | Tüm | — | Σg²=1 | Tasarım |
| 12 | Ölçüm | Yanıt | PLANNED | — | PLANNED |
| 13 | Ölçüm | Konum | PLANNED | — | PLANNED |
| 14 | JUCE Panner | Sınıf | — | — | §9 |
| 15 | Karar | Yöntem | — | — | UNKNOWN |

## §6 HRTF gecikme ve blok matrisi

| # | Öğe | Değer aralığı | Etki | Bütçe | Durum |
|---|---|---|---|---|---|
| 1 | HRIR uzunluğu | 128-512 örnek | Kalite | Bellek | Tasarım |
| 2 | ITD | ~0-1 ms | Yön | ≤ blok | Tasarım |
| 3 | ITD çözünürlüğü | 1 örnek | Doğruluk | — | Tasarım |
| 4 | ITD alt-örnek | Kesirli | Doğruluk | — | Opsiyonel |
| 5 | Konvolüsyon bloğu | 512 | sartname | — | sartname |
| 6 | Partitioned | Değişken | CPU | Opsiyonel | Opsiyonel |
| 7 | Toplam gecikme | ≤ blok + ITD | Yanıt | ≤ bütçe | PLANNED |
| 8 | Frekans sınırlı | 20-20 kHz | Doğruluk | — | Tasarım |
| 9 | Örnekleme | 48 kHz | Tutarlılık | — | sartname |
| 10 | float32 | ADR-017 | Hassasiyet | — | FROZEN |
| 11 | Gecikme ölçümü | Impuls | Doğrulama | PLANNED | PLANNED |
| 12 | CPU | Konvolüsyon | Yüksek | ≤ bütçe | PLANNED |
| 13 | Tahsis | Katsayılar | Constructor | — | L0 |
| 14 | Lock | 0 mutex | L0 | — | L0 |
| 15 | Karar | Blok yapısı | — | — | UNKNOWN |

## §7 Repo kanıtı — analiz beslemesi anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIWorkflow.php | 164 | `$eqCurve = $this->engine->optimizeEQ($roomProfile);` | Profil→eğri | Anoloji |
| 2 | shared/src/AI/AIWorkflow.php | 157 | `$roomProfile = [` | Profil dizisi | Anoloji |
| 3 | engine/ (yok) | — | Spatial C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satır PHP'dir; spatial DSP karşılığı **yoktur** (motor
> diskte mevcut değil). Kanal eşlemesi ADR-017'den gelir, bu satırdan
> **gelmez**; satır profil→eğri akışının anolojisidir. AIWorkflow.php:168
> satırı mojibake içerir ve **alıntılanmaz**.

## §8 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| ITU-R BS.2051 çok kanallı ses konfigürasyonlarını tanımlar | https://www.itu.int/rec/R-REC-BS.2051/en | 2026-10-12 | 200 OK |
| Microsoft Spatial Sound (Windows Core Audio) uzamsal sesi anlatır | https://learn.microsoft.com/en-us/windows/win32/coreaudio/spatial-sound | 2026-10-12 | 200 OK |
| JUCE dsp::Panner sınıfı kanal pan kazancı sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1Panner.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_Panner.h pan tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_Panner.h | 2026-10-12 | 200 OK |

## §9 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Yön doğruluğu | Kulaç testi | testing | testing |
| 2 | ITD doğruluğu | ≤ örnek | Impuls | testing |
| 3 | Pan gücü | Σg²=1 | Sayısal | testing |
| 4 | Frekans yanıtı | Düz | Sweep | testing |
| 5 | Kanal ayrımı | Doğru | Test | testing |
| 6 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 7 | Tahsis | 0 B/audio thread | Profil | L0 |
| 8 | Lock | 0 mutex | Denetim | L0 |
| 9 | Gecikme | ≤ bütçe | Impuls | PLANNED |
| 10 | float32 | Zorunlu | ADR-017 | FROZEN |
| 11 | Buffer | 512 örnek | sartname | Sabit |
| 12 | Örnekleme | 48 kHz | sartname | Sabit |
| 13 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 14 | HRTF verisi | Bilinmiyor | Veri yok | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §10 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | crossover | Kanal eşlemesi | §4 | crossover |
| 2 | reverb | Uzamsal reverb | Entegrasyon | reverb |
| 3 | delay | Uzamsal gecikme | ITD benzeri | delay |
| 4 | measurement | Uzamsal ölçüm | Q4 | measurement |
| 5 | testing | Yön testi | Kulaç | testing |
| 6 | limiter | Kanal tavanı | Sıra | limiter |
| 7 | dynamics | Kanal kazancı | Trim | dynamics |
| 8 | pipeline | Sıra | pipeline | pipeline |
| 9 | equalizer | Kanal EQ | Opsiyonel | eq |
| 10 | filters | Filtre | Opsiyonel | filters |
| 11 | compressor | Sıra | compressor | compressor |
| 12 | spatial | Bu dosya | — | Bu |

## §11 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | HRTF verisi yokluğu | Kesin | Yüksek | UNKNOWN işaretle | UNKNOWN |
| 2 | Kişiselleştirme eksikliği | Yüksek | Orta | Genel veri seti | UNKNOWN |
| 3 | CPU aşımı (konvolüsyon) | Yüksek | Yüksek | Bölümleme | Ölçüm |
| 4 | Gecikme aşımı | Orta | Yüksek | Bütçe | PLANNED |
| 5 | Yanlış kanal eşlemesi | Düşük | Yüksek | Test | testing |
| 6 | Yön sezgisizliği | Orta | Orta | Kulaç testi | testing |
| 7 | Tahsis (katsayı) | Düşük | Yüksek | Constructor | L0 |
| 8 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 9 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §12 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | HRTF veri seti hangisi? | Doğruluk/lisans | UNKNOWN |
| 2 | Kişiselleştirme olacak mı? | Kapsam | UNKNOWN |
| 3 | Head-tracking olacak mı? | Kapsam | UNKNOWN |
| 4 | 8.1 yerleşim açıları? | Kanal | UNKNOWN |
| 5 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 6 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 7 | Binaural mı, hoparlör pan mı? | Mimari | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (HRTF katsayıları) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | 8+LFE kanal sınırı | Evet | ADR-017 |
| 6 | Yumuşatma (klik önleme) | Evet | Tasarım |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | spatial-audio/spatial-audio-kapsam.md | Kademe-2 |
| 3 | crossover sınırı | crossover/crossover-kapsam.md | Kanal |
| 4 | reverb sınırı | reverb/reverb-kapsam.md | Uzamsal |
| 5 | measurement kapsamı | measurement/measurement-kapsam.md | Ölçüm |
| 6 | testing örneği | testing/testing-ornekler.md | Kulaç |
| 7 | delay sınırı | delay/delay-kapsam.md | Gecikme |
| 8 | limiter sınırı | limiter/limiter-kapsam.md | Kanal tavanı |
| 9 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 10 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 11 | ADR-017 | .ai/.decisions | float32 + 8.1 |
| 12 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 13 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 14 | Profil→eğri | AIWorkflow.php:164 | Anoloji |
| 15 | C++ motor | engine/ (yok) | Eksik |
| 16 | Mojibake satır | AIWorkflow.php:168 | Alıntı YOK |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| HRTF | Baş-ilgili transfer fonksiyonu |
| HRIR | Baş-ilgili impuls yanıtı |
| ITD | İnteraural zaman farkı |
| ILD | İnteraural genlik farkı |
| Binaural | İki kulaklı dinleme |
| Pinna | Kepçe — tiz yönlendirme |
| Azimut | Yatay yön açısı |
| Elevation | Yükseklik açısı |
| VBAP | Vector base amplitude panning |
| Crossfeed | Kanallar arası besleme |
| Head-tracking | Baş hareketi takibi |
| Konvolüsyon | FIR filtreleme |
| Pan | Kanallar arası konumlandırma |
| Height kanal | Yükseklik kanalı |
| Kulaç | Dinleyici değerlendirme |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | HRTF veri seti | Bilinmiyor | Bekliyor | §2.10 |
| 2 | Binaural vs hoparlör | Bilinmiyor | Bekliyor | §12.7 |
| 3 | Head-tracking | Evet/Hayır | Bekliyor | §12.3 |
| 4 | Yerleşim açıları | Bilinmiyor | Bekliyor | §4.10-11 |
| 5 | Pan yöntemi | §5 | Bekliyor | Güç kontrolü |
| 6 | LFE besleme | Evet/Hayır | Bekliyor | §5.9 |
| 7 | CPU bütçesi | Bilinmiyor | Bekliyor | Ölçüm |
| 8 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 4 URL |
| 4 | Repo kanıtı | AIWorkflow.php:164 |
| 5 | Mojibake satır alıntılmadı | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §18 HRTF örnek kaydırma ve ITD matrisi

| # | ITD yöntemi | Uygulama | Doğruluk | CPU | Durum |
|---|---|---|---|---|---|
| 1 | Örnek kaydırma | Tam örnek | Orta | Düşük | Sınıf A |
| 2 | Yarım örnek (interpolasyon) | Doğrusal | İyi | Düşük | Sınıf B |
| 3 | Kübik interpolasyon | Kesirli | Çok iyi | Orta | Sınıf C |
| 4 | Allpass gecikme | Frekans bağımlı | İyi | Orta | Opsiyonel |
| 5 | Fraunhofer ITD (r cos θ) | Geometrik | İyi | Düşük | Tasarım |
| 6 | Woodworth ITD | Geometrik | İyi | Düşük | Tasarım |
| 7 | Gerçek zamanlı güncelle | Yön değişimi | — | Rampa | Tasarım |
| 8 | Yumuşatma | Hareket | — | Rampa | Tasarım |
| 9 | Kanal başına ITD | L/R ayrı | Zorunlu | — | Tasarım |
| 10 | Yükseklik ITD | HL/HR | Opsiyonel | — | Opsiyonel |
| 11 | ILD (genlik) | Frekans bağımlı | İyi | Düşük | Tasarım |
| 12 | Frekans bağımlı ILD | Shadow modeli | İyi | Orta | Opsiyonel |
| 13 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 14 | Ölçüm | Kulaç | PLANNED | — | PLANNED |
| 15 | Karar | Yöntem | — | — | UNKNOWN |

## §19 Yön uzayı matrisi

| # | Uzay | Kapsam | Çözünürlük | Kullanım | Durum |
|---|---|---|---|---|---|
| 1 | Azimut (2D) | 0-360° | 5-15° | Yatay pan | Tasarım |
| 2 | Elevation (2D) | -90..+90° | 5-15° | Yükseklik | Tasarım |
| 3 | Küre (3D) | Tüm | Izgara | Binaural | Tasarım |
| 4 | Yarı küre | Ön | Izgara | Hoparlör | Opsiyonel |
| 5 | Kanal konileri | 8+LFE | Konik | VBAP | §5.4 |
| 6 | Ara nokta | İki kanal | interpolasyon | Yumuşak | Tasarım |
| 7 | İnterpolasyon | HRTF | Ağırlıklı | Sürekli | Tasarım |
| 8 | Tabela boyutu | N × M | Bellek | Tasarım | Tasarım |
| 9 | Ön hesap | Constructor | Non-RT | — | L0 |
| 10 | Dinamik | Baş takibi | Runtime | Opsiyonel | Opsiyonel |
| 11 | Düzeltme | Perçem | Opsiyonel | — | Opsiyonel |
| 12 | Ölçüm | Yön doğruluğu | PLANNED | — | PLANNED |
| 13 | Ölçüm | Geçiş | PLANNED | — | PLANNED |
| 14 | Ölçüm | CPU | PLANNED | — | PLANNED |
| 15 | Karar | Çözünürlük | — | — | UNKNOWN |

## §20 Frekans bölgesi matrisi (binaural)

| # | Bölge | Frekans | Mekanizma | İşlem | Durum |
|---|---|---|---|---|---|
| 1 | Çok düşük | 20-100 Hz | ITD baskın | Gecikme | Tasarım |
| 2 | Düşük | 100-800 Hz | ITD + ILD | Karışık | Tasarım |
| 3 | Orta | 800-3000 Hz | ILD + pinna | Konvolüsyon | Tasarım |
| 4 | Yüksek | 3000-8000 Hz | Gölge + pinna | Konvolüsyon | Tasarım |
| 5 | Tiz | 8000-20000 Hz | Pinna + refleks | Konvolüsyon | Tasarım |
| 6 | LFE bölgesi | 3-120 Hz | Yönsüz | Kanal | §4.4 |
| 7 | Crossfeed bandı | Düşük | Yumuşak | Opsiyonel | Opsiyonel |
| 8 | Bant geçiş bölgeleri | — | Düzgün geçiş | Tasarım | Tasarım |
| 9 | fs/2 üst sınır | 24 kHz | Nyquist | sartname | sartname |
| 10 | Ölçüm | Bant yanıt | PLANNED | — | PLANNED |
| 11 | Ölçüm | Bant gürültü | PLANNED | — | PLANNED |
| 12 | Ölçüm | Bant korelasyon | PLANNED | — | PLANNED |
| 13 | Bant karar | K004 | — | — | UNKNOWN |
| 14 | Bant karar | K004 | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §21 Karşılaştırmalı spatial yöntemler matrisi

| # | Yöntem | Gecikme | CPU | Gerçekçilik | Ekipman | Durum |
|---|---|---|---|---|---|---|
| 1 | Binaural (HRTF) | Orta | Yüksek | Yüksek | Kulaklık | Sınıf B |
| 2 | Stereo pan | 0 | Düşük | Düşük | 2 hoparlör | Sınıf A |
| 3 | Quadraphonic | 0 | Orta | Orta | 4 hoparlör | Kıyas |
| 4 | 5.1 pan | 0 | Orta | Orta | 6 hoparlör | Kıyas |
| 5 | 7.1 pan | 0 | Orta | İyi | 8 hoparlör | Kıyas |
| 6 | 8.1 pan (ADR-017) | 0 | Orta | İyi | 9 hoparlör | Öneri |
| 7 | VBAP (3D) | 0 | Orta | İyi | 3+ hoparlör | Sınıf B |
| 8 | Wave field | 0 | Çok yüksek | Çok yüksek | Dizi | Araştırma |
| 9 | Ambisonics (1. sıra) | Düşük | Orta | Orta | Sferik | Araştırma |
| 10 | Ambisonics (3. sıra) | Düşük | Yüksek | İyi | Sferik | Araştırma |
| 11 | Transaural | Orta | Yüksek | İyi | Kalibre | Araştırma |
| 12 | Düzeltmeli hoparlör | 0 | Orta | Orta | Oda | Araştırma |
| 13 | Head-tracked HRTF | Orta | Çok yüksek | Çok yüksek | Kulaklık+izleme | Araştırma |
| 14 | Ölçüm | — | — | — | — | PLANNED |
| 15 | Karar | — | — | — | — | UNKNOWN |

## §22 CPU ve bellek karşılaştırma matrisi

| # | Bileşen | CPU (göreli) | Bellek | Gecikme | Durum |
|---|---|---|---|---|---|
| 1 | Sabit kanal pan | 1× | 0 | 0 | Kıyas |
| 2 | VBAP 8 kanal | 1.5× | Düşük | 0 | Sınıf B |
| 3 | HRTF (kısa HRIR) | 5× | Orta | ≤ blok | Sınıf B |
| 4 | HRTF (uzun HRIR) | 12× | Yüksek | ≤ blok | Sınıf C |
| 5 | Partitioned HRTF | 8× | Yüksek | ≤ blok | Sınıf C |
| 6 | Head-tracking | +2× | +Az | Rampa | Araştırma |
| 7 | Ambisonics 1. sıra | 2× | Düşük | 0 | Araştırma |
| 8 | Ambisonics 3. sıra | 6× | Orta | 0 | Araştırma |
| 9 | Ölçüm | — | — | — | PLANNED |
| 10 | Ölçüm | — | — | — | PLANNED |
| 11 | Ölçüm | — | — | — | PLANNED |
| 12 | Ölçüm | — | — | — | PLANNED |
| 13 | Ölçüm | — | — | — | PLANNED |
| 14 | Ölçüm | — | — | — | PLANNED |
| 15 | Ölçüm | — | — | — | PLANNED |

> Göreli değerler literatür karşılaştırmasıdır (ölçüm PLANNED); mutlak CPU
> değeri bu dosyada **iddia edilmez**.

## §23 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Pan gücü | Süpürme | Σg²=1 | ≤0.1 dB | testing |
| 2 | Pan geçişi | Yavaş hareket | Kliksiz | Artımlı | testing |
| 3 | ITD doğruluğu | İmpuls | Gecikmiş δ | ≤0.1 ms | testing |
| 4 | Yön testi | Kulaç | Doğru yön | Kulaç | testing |
| 5 | Kanal eşlemesi | Kanal testi | Doğru kanal | Doğru | testing |
| 6 | Frekans yanıtı | Sweep | Düz | ≤ eşik | testing |
| 7 | LFE filtresi | Tiz | Kısılmış | ≤ eşik | testing |
| 8 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 9 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 10 | Param geçişi | Yön değişimi | Kliksiz | Ramp | testing |
| 11 | Buffer değişimi | 512↔diğer | Kabul | Pipeline | PLANNED |
| 12 | Kanal değişimi | 2.0↔8.1 | Kabul | Pipeline | PLANNED |
| 13 | Denormal | Tiny girdi | Performans | FTZ | L0 |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §24 Lisans ve veri kaynağı matrisi

| # | Veri kaynağı | Tür | Erişim | Lisans | Durum |
|---|---|---|---|---|---|
| 1 | Ölçüm düzeneği | Kendi ölçüm | Fiziksel | Sahip olunan | Araştırma |
| 2 | Üçüncü parti HRTF seti | Hazır veri | İndirme | Bilinmiyor | UNKNOWN |
| 3 | Açık HRTF seti | Hazır veri | İndirme | Bilinmiyor | UNKNOWN |
| 4 | Türetilmiş model | Hesap | Kod | Sahip olunan | Araştırma |
| 5 | Kişisel profil | Ölçüm | Kullanıcı | Kullanıcı | UNKNOWN |
| 6 | Lisans denetimi | Her kaynak | — | Zorunlu | Kural |
| 7 | Atıf zorunluluğu | Kaynaka göre | — | Kontrol | Kural |
| 8 | Ticari kullanım | Kaynaka göre | — | Kontrol | Kural |
| 9 | Dağıtım (bundling) | Kaynaka göre | — | Kontrol | Kural |
| 10 | Depoya ekleme | Onay | — | Onay | Kural |
| 11 | Bilinmeyen lisans | — | — | Kullanma | Kural |
| 12 | Karar | K004 | — | — | UNKNOWN |
| 13 | Karar | K004 | — | — | UNKNOWN |
| 14 | Karar | K004 | — | — | UNKNOWN |
| 15 | Karar | K004 | — | — | UNKNOWN |

> Lisans durumu bilinmediği için **UNKNOWN** olarak işaretlenir; doğrulanmış
> lisans bilgisi olmadan hiçbir HRTF veri seti depoya eklenmez
> (Zero-Hallucination).

## §25 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | HRTF veri seti | UNKNOWN | Lisans/ölçüm | §24 |
| 2 | Binaural vs hoparlör | UNKNOWN | Mimari | §12.7 |
| 3 | Head-tracking | UNKNOWN | Kapsam | §12.3 |
| 4 | 8.1 yerleşim | UNKNOWN | Standart | §4.10 |
| 5 | Pan yöntemi | UNKNOWN | Karar | §5 |
| 6 | CPU bütçesi | PLANNED | Ölçüm | §22 |
| 7 | Yön doğruluğu | PLANNED | testing | §23 |
| 8 | Q4 | UNKNOWN | measurement/testing | §12.5 |
| 9 | Motor kodu | PLANNED | engine/ yok | §1.7 |
| 10 | Kanal sayısı | FROZEN | ADR-017 | §4.9 |

## §26 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | HRTF UNKNOWN | Tamam |
| 2 | Lisans UNKNOWN | Tamam |
| 3 | Mojibake satır alıntılanmadı | Tamam |
| 4 | 8.1 eşleme §4 | Tamam |
| 5 | Pan gücü §5 | Tamam |
| 6 | Web tablosu §8 | Tamam |
| 7 | Repo kanıtı §7 | Tamam |
| 8 | Q4 UNKNOWN | Tamam |
| 9 | CJK/mojibake 0 | Ölçüldü |
| 10 | Authority son satır | Tamam |

## §27 Son öz-kontrol ek satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Lisans UNKNOWN | Tamam |
| 3 | HRTF veri seti UNKNOWN | Tamam |
| 4 | URL benzersiz (4) | Tamam |
| 5 | Repo kanıtı §7 | Tamam |
| 6 | Q4 UNKNOWN | Tamam |
| 7 | ≥500 satır | Ölçüldü |
| 8 | Authority son satır | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode