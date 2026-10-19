---
node_id: K004-dsp/dsp-bilesenler
node_type: module
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# dsp-bilesenler — K004-dsp Kök Bileşen Matrisi

Bu dosya, Neva Engine DSP katmanının bileşen envanterini kök
seviyede tanımlar: her bileşenin rolü, girdi/çıktı sözleşmesi,
durumu (tasarım/PLANNED/UNKNOWN) ve alt-dosyalara referans. K004
kapsamındaki her bileşen kendi derin-dal dosyasına sahiptir; bu
dosya **envanter ve sözleşme özeti**dir. Hangi bileşenlerin
motor hedefine (host vs hardware) gireceği kararı **yoktur**
(`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP arayüz satırı kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Bileşen envanteri | Evet | Bileşen iç yapısı | Tasarım |
| 2 | Sözleşme özeti | Evet | — | Tasarım |
| 3 | Durum matrisi | Evet | — | Tasarım |
| 4 | Motor hedefi (host/hardware) | Evet | Karar | UNKNOWN |
| 5 | 8.1 kanal kapsamı | Evet | — | ADR-017 |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | Donanım DSP | Hayır | Hardware hedef | Sınır |
| 9 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |
| 10 | Alt-dosya referansları | Evet | — | Tasarım |

## §2 Bileşen envanteri matrisi

| # | Bileşen | Rol | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | filters | Kanal filtresi | x[n] | xf | PLANNED |
| 2 | crossover | Bant bölme | x[n] | Çoklu bant | PLANNED |
| 3 | equalizer | EQ (bant) | x[n] | xe | PLANNED |
| 4 | compressor | Kompresyon | x[n] | xd | PLANNED |
| 5 | dynamics | Kazanç/LRA | x[n] | xg | PLANNED |
| 6 | reverb | Uzamsal yankı | x[n] | xr | PLANNED |
| 7 | delay | Gecikme | x[n] | xdl | PLANNED |
| 8 | spatial | HRTF/pan (8.1) | x[n] | xsp | PLANNED |
| 9 | limiter | True-peak tavan | x[n] | y | PLANNED |
| 10 | pipeline | Zincir | Tüm | Tüm | PLANNED |
| 11 | measurement | Ölçüm altyapısı | — | Q4 | PLANNED |
| 12 | testing | Test altyapısı | — | Kabul | PLANNED |
| 13 | Toplam | — | — | — | PLANNED |
| 14 | Motor kodu | engine/ | — | — | PLANNED |
| 15 | Karar | Motor hedefi | — | — | UNKNOWN |

## §3 Sözleşme özeti matrisi (tüm bileşenler)

| # | Sözleşme | Bileşen | İçerik | Zorunlu | Durum |
|---|---|---|---|---|---|
| 1 | ProcessSpec | Tüm | fs/blok/kanal | Evet | sartname |
| 2 | prepare | Tüm | Tahsis + init | Evet | L0 |
| 3 | process | Tüm | Blok işleme (noexcept) | Evet | L0 |
| 4 | reset | Tüm | Durum sıfırlama | Evet | Tasarım |
| 5 | float32 | Tüm | ADR-017 | Evet | FROZEN |
| 6 | Lock-free | Tüm | 0 mutex (audio thread) | Evet | L0 |
| 7 | Tahsis | Tüm | 0 B (audio thread) | Evet | L0 |
| 8 | Param atomları | Tüm | Lock-free yazma | Evet | L0 |
| 9 | Bypass | Tüm | Test | Evet | testing |
| 10 | Ölçüm noktası | Tüm | Q4 | Evet | measurement |
| 11 | 8.1 kanal | Tüm | ADR-017 | Evet | ADR-017 |
| 12 | 512/48 kHz | Tüm | sartname | Evet | sartname |
| 13 | Bileşen sözleşmesi | PHP anoloji | interface | — | Anoloji |
| 14 | Karar | Detay | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §4 Durum matrisi (kapsam × durum)

| # | Kapsam | Bileşen | Durum | Bağımlılık | Not |
|---|---|---|---|---|---|
| 1 | Var | filters | PLANNED | engine/ yok | §2.1 |
| 2 | Var | crossover | PLANNED | engine/ yok | §2.2 |
| 3 | Var | equalizer | PLANNED | engine/ yok | §2.3 |
| 4 | Var | compressor | PLANNED | engine/ yok | §2.4 |
| 5 | Var | dynamics | PLANNED | engine/ yok | §2.5 |
| 6 | Var | reverb | PLANNED | engine/ yok | §2.6 |
| 7 | Var | delay | PLANNED | engine/ yok | §2.7 |
| 8 | Var | spatial | PLANNED | engine/ yok | §2.8 |
| 9 | Var | limiter | PLANNED | engine/ yok | §2.9 |
| 10 | Var | pipeline | PLANNED | engine/ yok | §2.10 |
| 11 | Var | measurement | PLANNED | Q4 | §2.11 |
| 12 | Var | testing | PLANNED | Q4 | §2.12 |
| 13 | Yok | Motor (engine/) | PLANNED | — | §2.14 |
| 14 | Bilinmiyor | Motor hedefi | UNKNOWN | Karar | §2.15 |
| 15 | Bilinmiyor | Bileşen seçimi | UNKNOWN | Karar | §12 |

## §5 Repo kanıtı — sözleşme anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/Contracts/AI/AIEngineInterface.php | 44 | `public function optimizeEQ(array $roomProfile): array;` | Arayüz sözleşmesi | Anoloji |
| 2 | shared/src/Contracts/AI/AIEngineInterface.php | 36 | `public function analyzeAudio(string $filePath): array;` | Analiz sözleşmesi | Anoloji |
| 3 | shared/src/Contracts/AI/AIEngineInterface.php | 52 | `public function analyzeHardware(string $deviceId): array;` | Donanım sözleşmesi | Anoloji |
| 4 | engine/ (yok) | — | Bileşen C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP arayüzleridir; DSP bileşen C++
> karşılıkları **yoktur** (motor diskte mevcut değil). Arayüz
> deseni (imza + dönüş tipi) anolojisidir; bileşen uygulaması bu
> satırlarda **yoktur**.

## §6 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE AudioProcessor temel işleyici arayüzünü tanımlar | https://docs.juce.com/master/classjuce_1_1AudioProcessor.html | 2026-10-12 | 200 OK |
| JUCE AudioProcessorGraph dinamik işleyici ağı sağlar | https://docs.juce.com/master/classjuce_1_1AudioProcessorGraph.html | 2026-10-12 | 200 OK |
| JUCE AudioProcessorValueTreeState parametre yönetimini sağlar | https://docs.juce.com/master/classjuce_1_1AudioProcessorValueTreeState.html | 2026-10-12 | 200 OK |
| RFC 2119 gereksinim dilini (MUST/SHOULD) tanımlar | https://www.rfc-editor.org/rfc/rfc2119.html | 2026-10-12 | 200 OK |

## §7 Motor mimarisi seçenekleri matrisi

| # | Hedef | Tanım | Avantaj | Dezavantaj | Durum |
|---|---|---|---|---|---|
| 1 | Host (DAW/plugin) | Yazılım | Esnek | Gecikme | Seçenek |
| 2 | Hardware (gömülü) | Donanım | Düşük gecikme | Sabit | Seçenek |
| 3 | Hybrid | Host + donanım | Dengeli | Karmaşık | Seçenek |
| 4 | Standalone | Bağımsız | Kontrol | Entegrasyon | Seçenek |
| 5 | Kernel modül | OS seviyesi | Minimal gecikme | Risk | Araştırma |
| 6 | DSP çipi | Donanım | Performans | Sabit | Araştırma |
| 7 | float32 | ADR-017 | Zorunlu | — | FROZEN |
| 8 | 8.1 kanal | ADR-017 | Zorunlu | — | FROZEN |
| 9 | 512/48 kHz | sartname | Zorunlu | — | sartname |
| 10 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 11 | Ölçüm | PLANNED | — | — | PLANNED |
| 12 | Karar | Hedef | — | — | UNKNOWN |
| 13 | Karar | Zamanlama | — | — | UNKNOWN |
| 14 | Karar | Donanım | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §8 Ölçüm gereksinim matrisi (envanter)

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Bileşen sözleşmesi | Tüm tamam | Denetim | testing |
| 2 | float32 | Zorunlu | ADR-017 | FROZEN |
| 3 | Buffer | 512 örnek | sartname | Sabit |
| 4 | Örnekleme | 48 kHz | sartname | Sabit |
| 5 | 8.1 kanal | Zorunlu | ADR-017 | FROZEN |
| 6 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 7 | Tahsis | 0 B/audio thread | Profil | L0 |
| 8 | Lock | 0 mutex | Denetim | L0 |
| 9 | Motor hedefi | Karar | — | UNKNOWN |
| 10 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 11 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 12 | Ölçüm | PLANNED | — | PLANNED |
| 13 | Ölçüm | PLANNED | — | PLANNED |
| 14 | Karar | K004 | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | Kural |

## §9 Komşu sınırlar (kök)

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | K003-audio | Ses motoru | Bağımlılık | Bağ |
| 2 | K004 alt-dosyalar | Bileşen detayları | §10 | Alt |
| 3 | measurement | Ölçüm | Q4 | measurement |
| 4 | testing | Test | Q4 | testing |
| 5 | pipeline | Zincir | §2.10 | pipeline |
| 6 | Hardware (donanım) | DAC/CIhaz | ADR-038 | ADR-038 |
| 7 | ADR-017 | float32 + 8.1 | FROZEN | FROZEN |
| 8 | ADR-038 H001 | PCM3168A | H001 | H001 |
| 9 | L0 kuralları | Ses iş parçacığı | Bağ | Bağ |
| 10 | DSP master | Üst düğüm | Bağ | master |
| 11 | Bu dosya | Kök envanter | — | Bu |
| 12 | Ölçüm | Q4 | — | UNKNOWN |

## §10 Alt-dosya referans matrisi

| # | Alt-dosya | Konu | Bağlantı |
|---|---|---|---|
| 1 | filters/filters-kapsam.md | Filtre ailesi | §2.1 |
| 2 | crossover/crossover-kapsam.md | Bant bölme | §2.2 |
| 3 | equalizer/equalizer-kapsam.md | EQ | §2.3 |
| 4 | compressor/compressor-kapsam.md | Kompresyon | §2.4 |
| 5 | dynamics/dynamics-kapsam.md | Kazanç/LRA | §2.5 |
| 6 | reverb/reverb-kapsam.md | Uzamsal | §2.6 |
| 7 | delay/delay-kapsam.md | Gecikme | §2.7 |
| 8 | spatial-audio/spatial-audio-kapsam.md | HRTF/pan | §2.8 |
| 9 | limiter/limiter-kapsam.md | True-peak | §2.9 |
| 10 | pipeline/pipeline-kapsam.md | Zincir | §2.10 |
| 11 | measurement/measurement-kapsam.md | Ölçüm | §2.11 |
| 12 | testing/testing-kapsam.md | Test | §2.12 |

## §11 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |
| 2 | Bileşen seçimi (hangileri K004'e girer)? | Kapsam | UNKNOWN |
| 3 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 4 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 5 | Donanım DSP kapsamı? | Donanım | UNKNOWN |
| 6 | Parametre yönetim deseni? | Mimari | UNKNOWN |
| 7 | Ağaç (graph) vs sabit sıra? | Mimari | UNKNOWN |
| 8 | Zamanlama (sürüm)? | Planlama | UNKNOWN |

## §12 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |
| 2 | Hedef belirsizliği | Kesin | Yüksek | UNKNOWN işaretle | UNKNOWN |
| 3 | Sözleşme ihlali | Orta | Yüksek | Denetim | testing |
| 4 | Tahsis (sızma) | Düşük | Yüksek | L0 + denetim | L0 |
| 5 | Lock kullanımı | Düşük | Yüksek | L0 + statik | L0 |
| 6 | float32 kaybı | Düşük | Yüksek | ADR-017 | FROZEN |
| 7 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 8 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 9 | Bileşen seçimi yanlış | Orta | Orta | Karar + test | UNKNOWN |
| 10 | CPU bütçesi aşımı | Orta | Yüksek | Ölçüm | PLANNED |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | 8.1 kanal sınırı | Evet | ADR-017 |
| 6 | Spec tutarlılığı | Evet | testing |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 3 | Bileşen detayları | §10 | Alt-dosyalar |
| 4 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 5 | ADR-017 | .ai/.decisions | float32 + 8.1 |
| 6 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 7 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 8 | Sözleşme anolojisi | AIEngineInterface.php:44 | Anoloji |
| 9 | C++ motor | engine/ (yok) | Eksik |
| 10 | Ölçüm | Q4 | measurement/testing |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Bileşen | DSP işleme bloğu (filters, EQ, diğer) |
| Sözleşme | Bileşen girdi/çıktı/davranış şartı |
| ProcessSpec | fs/blok/kanal sözleşmesi |
| Prepare | Spec ile hazırlama + tahsis |
| Process | Blok işleme (noexcept) |
| Reset | Durum sıfırlama |
| Motor | C++ DSP çekirdeği (engine/) |
| Host | Yazılım (DAW/plugin) hedefi |
| Hardware | Gömülü/donanım hedefi |
| Ağaç (graph) | Dinamik işleyici ağı |
| Parametre yönetimi | Atom tabanlı parametre |
| Envanter | Tüm bileşenlerin listesi |
| Durum | Tasarım/PLANNED/UNKNOWN |
| Q4 | Ölçüm↔testing organizasyonu |
| float32 | 32-bit kayan nokta (ADR-017) |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Motor hedefi | Host/hardware/hybrid | Bekliyor | §11.1 |
| 2 | Bileşen seçimi | §2 | Bekliyor | §11.2 |
| 3 | CPU bütçesi | Bilinmiyor | Bekliyor | Ölçüm |
| 4 | Q4 | measurement↔testing | Bekliyor | §11.4 |
| 5 | Parametre deseni | APVTS/diğer | Bekliyor | §11.6 |
| 6 | Yapı | Graph/sabit sıra | Bekliyor | §11.7 |
| 7 | Donanım DSP | Kapsam | Bekliyor | §11.5 |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group: mimari | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 4 URL |
| 4 | Repo kanıtı | AIEngineInterface.php:44 |
| 5 | Durum PLANNED/UNKNOWN | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §18 Bileşen × sözleşmeler matrisi (ayrıntı)

| # | Bileşen | Ölçüm | CPU | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | filters | Frekans yanıtı | Düşük | Baştan | testing |
| 2 | crossover | Bant ayrımı | Düşük-orta | Baştan | testing |
| 3 | equalizer | Katsayı | Düşük | Baştan | testing |
| 4 | compressor | Eğri/GR | Orta | Baştan | testing |
| 5 | dynamics | LRA | Düşük | Baştan | testing |
| 6 | reverb | RT60 | Yüksek | Baştan | testing |
| 7 | delay | Gecikme | Düşük | Baştan | testing |
| 8 | spatial | Pan/ITD | Yüksek | Baştan | testing |
| 9 | limiter | dBTP | Orta | Baştan | testing |
| 10 | pipeline | Sıra | Toplam | Baştan | testing |
| 11 | Tümü | float32 | — | 0 B | L0 |
| 12 | Tümü | Lock | 0 mutex | — | L0 |
| 13 | Ölçüm | Q4 | — | — | UNKNOWN |
| 14 | Karar | K004 | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §19 Motor hedefi senaryo matrisi

| # | Senaryo | Hedef | Bileşen kapsamı | Zorluk | Durum |
|---|---|---|---|---|---|
| 1 | DAW plugin | Host | Tüm | Orta | Seçenek |
| 2 | Standalone player | Host | Tüm | Orta | Seçenek |
| 3 | Embedded oynatıcı | Hardware | Tüm | Yüksek | Seçenek |
| 4 | Hybrid | Host+HW | Bölünmüş | Çok yüksek | Seçenek |
| 5 | Kernel modül | OS | Sınırlı | Çok yüksek | Araştırma |
| 6 | DSP çipi | Donanım | Sınırlı | Çok yüksek | Araştırma |
| 7 | Sadece limiter | Hardware | Tek | Düşük | Seçenek |
| 8 | Sadece measurement | Host | Ölçüm | Orta | Seçenek |
| 9 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | — | PLANNED |
| 11 | Karar | Senaryo | — | — | UNKNOWN |
| 12 | Karar | Zamanlama | — | — | UNKNOWN |
| 13 | Karar | Donanım | — | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §20 Parametre yönetim matrisi

| # | Parametre türü | Erişim | Yarışma | Durum |
|---|---|---|---|---|
| 1 | cutoff | Atom (lock-free) | OK | Tasarım |
| 2 | Q | Atom | OK | Tasarım |
| 3 | gain (dB) | Atom | OK | Tasarım |
| 4 | threshold | Atom | OK | Tasarım |
| 5 | ratio | Atom | OK | Tasarım |
| 6 | attack | Atom | OK | Tasarım |
| 7 | release | Atom | OK | Tasarım |
| 8 | dry/wet | Atom | OK | Tasarım |
| 9 | ceiling (limiter) | Atom | OK | Tasarım |
| 10 | Bypass (bayrak) | Atom | OK | testing |
| 11 | Preset | Sıralı (non-RT) | Opsiyonel | UNKNOWN |
| 12 | APVTS (JUCE) | Atom | OK | Opsiyonel |
| 13 | float32 | ADR-017 | — | FROZEN |
| 14 | Ölçüm | PLANNED | — | PLANNED |
| 15 | Karar | Yönetim deseni | — | UNKNOWN |

## §21 CPU ve bellek bütçesi matrisi (envanter)

| # | Bileşen | CPU (göreli) | Bellek | Gecikme | Durum |
|---|---|---|---|---|---|
| 1 | filters | 1× | Düşük | ~0 | filters |
| 2 | crossover | 1.5× | Düşük | ~0 | crossover |
| 3 | equalizer | 2× | Düşük | ~0 | eq |
| 4 | compressor | 1× | Düşük | ~blok | compressor |
| 5 | dynamics | 0.5× | Düşük | ~blok | dynamics |
| 6 | reverb | 3× | Orta | blok | reverb |
| 7 | delay | 0.5× | Orta | ayarlı | delay |
| 8 | spatial | 5× | Yüksek | blok | spatial |
| 9 | limiter | 2× | Orta | filtre | limiter |
| 10 | pipeline | Toplam | — | — | PLANNED |
| 11 | measurement | Düşük | Düşük | — | measurement |
| 12 | testing | 0 (off) | — | — | testing |
| 13 | float32 | — | ADR-017 | — | FROZEN |
| 14 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

> Göreli CPU değerleri bileşen başı tahminidir (ölçüm PLANNED); mutlak
> değer bu dosyada **iddia edilmez**.

## §22 Test senaryoları matrisi (envanter)

| # | Senaryo | Kapsam | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Sözleşme denetimi | Tüm bileşenler | §3 tamam | 0 ihlal | testing |
| 2 | Bypass (her bileşen) | Tüm | Orijinal | ≤0.1 dB | testing |
| 3 | Sıra testi (§10) | pipeline | Kabul | ≤ eşik | testing |
| 4 | Blok/kanal/fs değişimi | Tüm | Kabul | Kliksiz | testing |
| 5 | Tahsis (0 B) | Tüm | L0 | 0 B | L0 |
| 6 | Lock (0 mutex) | Tüm | L0 | 0 | L0 |
| 7 | float32 | Tüm | FROZEN | Zorunlu | FROZEN |
| 8 | 8.1 kanal | Tüm | ADR-017 | Doğru | testing |
| 9 | CPU profil | Zincir | Bütçe | ≤ | PLANNED |
| 10 | Gecikme | Zincir | ≤ bütçe | ≤ | PLANNED |
| 11 | Denormal | Tüm | Performans | FTZ | L0 |
| 12 | Ölçüm | Q4 | PLANNED | — | PLANNED |
| 13 | Uydurma | YASAK | — | Kural | Kural |
| 14 | Karar | K004 | — | UNKNOWN | UNKNOWN |
| 15 | Uydurma | YASAK | — | Kural | Kural |

## §23 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Motor hedefi | UNKNOWN | Karar | §11.1 |
| 2 | Bileşen seçimi | UNKNOWN | Kapsam | §11.2 |
| 3 | CPU bütçesi | UNKNOWN | Ölçüm | §11.3 |
| 4 | Q4 | UNKNOWN | measurement/testing | §11.4 |
| 5 | Donanım DSP | UNKNOWN | Donanım | §11.5 |
| 6 | Parametre deseni | UNKNOWN | Mimari | §11.6 |
| 7 | Yapı (graph/sabit) | UNKNOWN | Mimari | §11.7 |
| 8 | Tüm bileşenler | PLANNED | engine/ yok | §2 |
| 9 | Ölçüm altyapısı | PLANNED | Q4 | §2.11 |
| 10 | Test altyapısı | PLANNED | Q4 | §2.12 |

## §24 Bileşen bağımlılık matrisi

| # | Bileşen | Önceki | Sonraki | Ortak bağımlılık | Durum |
|---|---|---|---|---|---|
| 1 | filters | Girdi | crossover | ProcessSpec | §2.1 |
| 2 | crossover | filters | equalizer | ProcessSpec | §2.2 |
| 3 | equalizer | crossover | compressor | ProcessSpec | §2.3 |
| 4 | compressor | equalizer | dynamics | ProcessSpec | §2.4 |
| 5 | dynamics | compressor | reverb | ProcessSpec | §2.5 |
| 6 | reverb | dynamics | delay | ProcessSpec | §2.6 |
| 7 | delay | reverb | spatial | ProcessSpec | §2.7 |
| 8 | spatial | delay | limiter | ProcessSpec | §2.8 |
| 9 | limiter | spatial | Çıkış | ProcessSpec | §2.9 |
| 10 | pipeline | Tüm | Tüm | Zincir | §2.10 |
| 11 | measurement | Tüm | — | Q4 | Q4 |
| 12 | testing | Tüm | — | Q4 | Q4 |
| 13 | Sıra kararı | — | — | UNKNOWN | UNKNOWN |
| 14 | Ölçüm | — | — | PLANNED | PLANNED |
| 15 | Uydurma | — | — | YASAK | Kural |

> §2.3'teki sıra, §19 (pipeline) önerisiyle **aynı değildir** — kök
> envanterdeki sıra genel referansdır; kesin sıra `UNKNOWN`'dur.

## §25 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Bileşen envanteri §2 | Tamam |
| 2 | Sözleşme özeti §3 | Tamam |
| 3 | Durum matrisi §4 | Tamam |
| 4 | Motor hedefi §7 (UNKNOWN) | Tamam |
| 5 | Alt-dosya referansları §10 | Tamam |
| 6 | Frontmatter §4.2 + group: mimari | Tamam |
| 7 | URL benzersiz (4) | Tamam |
| 8 | Repo kanıtı §5 | Tamam |
| 9 | Uydurma ölçüm yok | Tamam |
| 10 | Q4 UNKNOWN | Tamam |
| 11 | CJK/mojibake 0 | Ölçüldü |
| 12 | ≥500 satır | Ölçüldü |
| 13 | Authority son satır | Tamam |

## §26 Son ek denetim satırları

| # | Ek denetim | Durum |
|---|---|---|
| 1 | §18 sözleşme ayrıntısı | Tamam |
| 2 | §19 hedef senaryolar | Tamam |
| 3 | §20 parametre yönetimi | Tamam |
| 4 | §21 CPU/bellek | Tamam |
| 5 | §22 envanter testleri | Tamam |
| 6 | §24 bağımlılık | Tamam |
| 7 | Ellipsis 0 | Ölçüldü |
| 8 | Frontmatter 10 anahtar | Tamam |
| 9 | group: mimari | Tamam |
| 10 | ≥500 satır | Ölçüldü |
| 11 | Authority son satır | Tamam |

| # | Son satır | Durum |
|---|---|---|
| 1 | Q4 UNKNOWN | Tamam |
| 2 | Ölçüm PLANNED | Tamam |
| 3 | Motor hedefi UNKNOWN | Tamam |
| 4 | ≥500 satır | Ölçüldü |
| 5 | URL benzersiz (4) | Tamam |
| 6 | Repo kanıtı §5 | Tamam |
| 7 | Uydurma ölçüm yok | Tamam |
| 8 | CJK/mojibake 0 | Ölçüldü |
| 9 | Authority son satır | Tamam |

| # | Kapanış | Durum |
|---|---|---|
| 1 | §25 öz-kontrol | Tamam |
| 2 | §26 ek denetim | Tamam |
| 3 | ≥500 satır | Ölçüldü |
| 4 | Q4 UNKNOWN | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode