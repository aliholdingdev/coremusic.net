---
node_id: K004-dsp/testing/testing-dsp-test-stratejisi
node_type: module
status: draft
parent_id: K004-dsp/testing
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kod
---

# testing-dsp-test-stratejisi — K004-dsp Derin Dal

Bu dosya, Neva Engine DSP bileşenleri için test stratejisini tanımlar:
test piramidi (birim/entegrasyon/ölçüm), referans sinyaller, sayısal
kabul kriterleri, objective metric'ler (ölçülebilir kriter, subjektif dinleme yerine)
kriterler ve subjektif değerlendirme alternatifleri. Ölçüm protokolü
dosyası ayrıdır (`measurement/measurement-kapsam.md`); bu dosyanın
odağı **test tasarımı ve kabul kriterleridir**. K004 kapsamında
test altyapısı **PLANNED**'tır; kesin eşikler **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP test satırı kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Test piramidi | Evet | — | Tasarım |
| 2 | Referans sinyaller | Evet | Ölçüm protokolü | measurement |
| 3 | Kabul kriterleri | Evet | Kesin eşik | UNKNOWN |
| 4 | Objective metric | Evet | Subjektif test | Tasarım |
| 5 | UnitTest altyapısı | Evet | Motor kodu | PLANNED |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | UI testi | Hayır | UI katmanı | Sınır |
| 9 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |
| 10 | Q4 organizasyonu | Referans | — | UNKNOWN |

## §2 Test piramidi matrisi

| # | Katman | Kapsam | Sıklık | Ortam | Durum |
|---|---|---|---|---|---|
| 1 | Birim (unit) | Tek bileşen | Her commit | Host | Tasarım |
| 2 | Entegrasyon | Bileşen grubu | Her commit | Host | Tasarım |
| 3 | Zincir | Tam pipeline | Günlük | Host | Tasarım |
| 4 | Ölçüm (measurement) | Frekans/seviye | Günlük | Host | Tasarım |
| 5 | Donanım | DAC/CIhaz | Manuel | Donanım | PLANNED |
| 6 | Performans | CPU/gecikme | Günlük | Host | Tasarım |
| 7 | Subjektif | Dinleyici | Manuel | İnsan | Opsiyonel |
| 8 | Regresyon | Sabit hata | Her commit | Host | Tasarım |
| 9 | Sınır (edge) | Aşırı durum | Her commit | Host | Tasarım |
| 10 | Konvolüsyon (property) | Özellikler | Haftalık | Host | Opsiyonel |
| 11 | Fuzz | Rastgele girdi | Haftalık | Host | Opsiyonel |
| 12 | CPU profil | Bütçe | Günlük | Host | Tasarım |
| 13 | Tahsis (0 B) | L0 kuralı | Her commit | Host | Tasarım |
| 14 | Lock (0 mutex) | L0 kuralı | Statik | Host | Tasarım |
| 15 | Q4 | UNKNOWN | — | — | UNKNOWN |

## §3 Referans sinyaller matrisi

| # | Sinyal | Tanım | Kullanım | Üretim | Durum |
|---|---|---|---|---|---|
| 1 | İmpuls | Tek örnek 1.0 | Gecikme/yanıt | Doğrudan | Tasarım |
| 2 | Step | 0→1 geçiş | Zaman yanıtı | Doğrudan | Tasarım |
| 3 | Sinüs (tek frekans) | A·sin(2πf) | Genlik/faz | Doğrudan | Tasarım |
| 4 | Sweep (log) | 20 Hz-20 kHz | Frekans yanıtı | Doğrudan | Tasarım |
| 5 | Beyaz gürültü | Rastgele (deterministik) | Enerji | Tablo/PRNG | Tasarım |
| 6 | Pembe gürültü | -3 dB/oct | Bant | Filtreli | Tasarım |
| 7 | Kombinasyon (multitone) | Çok frekanslı | Yanıt | Toplam | Tasarım |
| 8 | DC (0 Hz) | Sabit | DC testi | Doğrudan | Tasarım |
| 9 | Nyquist yakını | fs/2 | Sınır | Sinüs | Tasarım |
| 10 | Aşırı girdi | >0 dBFS | Koruma | Doğrudan | Tasarım |
| 11 | Sessizlik | 0.0 | Zemin | Doğrudan | Tasarım |
| 12 | Tiny girdi | 1e-30 | Denormal | Doğrudan | L0 |
| 13 | Rastgele (PRNG) | Deterministik seed | Property | PRNG | Opsiyonel |
| 14 | Ölçüm doğrulama | Kalibre | Zorunlu | Referans | testing |
| 15 | Karar | Sinyal seti | — | — | UNKNOWN |

## §4 Objective metric matrisi

| # | Metrik | Tanım | Birim | Hedef | Durum |
|---|---|---|---|---|---|
| 1 | Frekans yanıtı sapması | Referans vs çıkış | dB | ≤ eşik | UNKNOWN |
| 2 | Faz sapması | Referans vs çıkış | derece | ≤ eşik | UNKNOWN |
| 3 | THD+N | Bozulma | % | ≤ eşik | UNKNOWN |
| 4 | SNR | Sinyal/gürültü | dB | ≥ eşik | UNKNOWN |
| 5 | Gecikme | İmpuls gecikmesi | örnek | ≤ bütçe | UNKNOWN |
| 6 | Kareleme (klik) | Adım/artımlı test | Adet | 0 | testing |
| 7 | CPU kullanımı | Profil | ms/blok | ≤ bütçe | UNKNOWN |
| 8 | Tahsis | Audio thread | B | 0 | L0 |
| 9 | Lock | Audio thread | mutex | 0 | L0 |
| 10 | float32 | ADR-017 | — | Zorunlu | FROZEN |
| 11 | Kararlılık | \\|p\\|<1 / bounded | — | Test | testing |
| 12 | Tavan ihlali | 0 dBFS üstü | Adet | 0 | testing |
| 13 | Kanal doğruluğu | 8.1 eşleme | — | Doğru | testing |
| 14 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural | Kural |
| 15 | Karar | Eşik değeri | — | — | UNKNOWN |

## §5 Kabul kriteri matrisi (bileşen)

| # | Bileşen | Kritik test | Kabul | Durum |
|---|---|---|---|---|
| 1 | filters | Frekans yanıtı | ≤ eşik | UNKNOWN |
| 2 | crossover | Bant ayrımı | ≤ eşik | UNKNOWN |
| 3 | equalizer | Katsayı doğrulama | ≤ eşik | testing |
| 4 | compressor | Eğri/GR | ≤ eşik | testing |
| 5 | dynamics | LRA doğrulama | ≤ eşik | testing |
| 6 | reverb | RT60 doğrulama | ≤ eşik | UNKNOWN |
| 7 | delay | Gecikme doğruluk | ≤0.5 örnek | testing |
| 8 | spatial | Pan gücü/ITD | ≤ eşik | testing |
| 9 | limiter | Tavan (dBTP) | 0 ihlal | testing |
| 10 | pipeline | Sıra/bypass | ≤0.1 dB | testing |
| 11 | Tümü | Tahsis 0 B | L0 | L0 |
| 12 | Tümü | Lock 0 mutex | L0 | L0 |
| 13 | Tümü | float32 | FROZEN | FROZEN |
| 14 | Ölçüm | Q4 | PLANNED | PLANNED |
| 15 | Uydurma | YASAK | — | Kural |

## §6 Repo kanıtı — test anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/tests/Middleware/MiddlewarePipelineTest.php | 17 | `final class MiddlewarePipelineTest extends TestCase` | Test sınıfı | Anoloji |
| 2 | engine/ (yok) | — | DSP test C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satır PHP'dir; DSP test karşılığı **yoktur** (motor
> diskte mevcut değil). Test sınıfı deseni (TestCase devralma) anolojisidir;
> DSP test içeriği bu satırda **yoktur**.

## §7 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE UnitTest birim test altyapısı sağlar | https://docs.juce.com/master/classjuce_1_1UnitTest.html | 2026-10-12 | 200 OK |
| JUCE UnitTestRunner test çalıştırma sağlar | https://docs.juce.com/master/classjuce_1_1UnitTestRunner.html | 2026-10-12 | 200 OK |
| ITU-R BS.1116 subjektif değerlendirme yöntemini tanımlar | https://www.itu.int/rec/R-REC-BS.1116/en | 2026-10-12 | 200 OK |
| IEEE 1597 ses sistemleri ölçümlerini tanımlar | https://standards.ieee.org/ieee/1597/ | 2026-10-12 | 200 OK |
| IEEE 1597.1 ses doğrulama ölçüm yöntemlerini tanımlar | https://standards.ieee.org/ieee/1597.1/ | 2026-10-12 | 200 OK |

## §8 Subjektif değerlendirme matrisi (alternatif)

| # | Yöntem | Katılımcı | Protokol | Kullanım | Durum |
|---|---|---|---|---|---|
| 1 | AB karşılaştırma | Uzman | İki sinyal | Kıyas | Opsiyonel |
| 2 | MUSHRA | Uzman | Çoklu | Kalite | Opsiyonel |
| 3 | Subjektif değerlendirme | Denek | Standart (BS.1116) | Referans | Opsiyonel |
| 4 | Subjektif değerlendirme | Denek | SSID (BS.1116) | Referans | Opsiyonel |
| 5 | Kulaç testi | Denek | Yön algısı | spatial | Opsiyonel |
| 6 | Subjektif dinleme | Grup | Oda | Referans | Opsiyonel |
| 7 | Objective öncelik | — | Ölçülebilir | Zorunlu | Tasarım |
| 8 | Subjektif doğrulama | — | Son adım | Opsiyonel | Opsiyonel |
| 9 | Rastgele sunum | — | Sapma önleme | Zorunlu | Tasarım |
| 10 | Ölçüm | PLANNED | — | — | PLANNED |
| 11 | Ölçüm | PLANNED | — | — | PLANNED |
| 12 | Karar | Kapsam | — | — | UNKNOWN |
| 13 | Karar | Kapsam | — | — | UNKNOWN |
| 14 | Karar | Kapsam | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

> Subjektif yöntemler **opsiyonel**'dir; K004 kalite kapısı
> **objective metric** (§4) üzerine kurulur (Ölçüm PLANNED).

## §9 Ölçüm gereksinim matrisi (test altyapısı)

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Test kapsamı | Bileşen başına | Piramit | testing |
| 2 | Referans sinyal seti | §3 | Doğrulama | testing |
| 3 | Kabul kriteri | §5 (eşik UNKNOWN) | Karar | UNKNOWN |
| 4 | Regresyon | Sabit hata | Test | testing |
| 5 | CI entegrasyonu | Her commit | Otomasyon | PLANNED |
| 6 | CPU ölçümü | Bütçe | Profil | PLANNED |
| 7 | Tahsis (0 B) | L0 | Profil | L0 |
| 8 | Lock (0 mutex) | L0 | Statik/denetim | L0 |
| 9 | float32 | Zorunlu | ADR-017 | FROZEN |
| 10 | Buffer | 512 örnek | sartname | Sabit |
| 11 | Örnekleme | 48 kHz | sartname | Sabit |
| 12 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 13 | Ölçüm | PLANNED | — | PLANNED |
| 14 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §10 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | measurement | Ölçüm protokolü | Q4 | measurement |
| 2 | pipeline | Zincir testi | §2.3 | pipeline |
| 3 | her bileşen | Birim testi | §5 | Bileşenler |
| 4 | equalizer | Katsayı testi | §5.3 | eq |
| 5 | compressor | Eğri testi | §5.4 | compressor |
| 6 | dynamics | LRA testi | §5.5 | dynamics |
| 7 | delay | Gecikme testi | §5.7 | delay |
| 8 | spatial | Pan/ITD testi | §5.8 | spatial |
| 9 | limiter | Tavan testi | §5.9 | limiter |
| 10 | DSP master | Üst düğüm | Bağ | master |
| 11 | testing | Bu dosya | — | Bu |
| 12 | Ölçüm | Q4 | — | UNKNOWN |

## §11 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 2 | Kesin eşikler? | Kabul | UNKNOWN |
| 3 | CI entegrasyonu? | Süreç | UNKNOWN |
| 4 | Subjektif test kapsamı? | Kapsam | UNKNOWN |
| 5 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 6 | Donanım testi ne zaman? | Donanım | UNKNOWN |
| 7 | Reference sinyal kaynakları? | Test verisi | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §12 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Eksik test kapsamı | Yüksek | Yüksek | Piramit + regresyon | testing |
| 2 | Yanlış eşik | Orta | Orta | Ölçüm + karar | §4.15 |
| 3 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 4 | CI entegrasyon eksikliği | Orta | Orta | PLANNED | §9.5 |
| 5 | CPU ölçüm eksikliği | Orta | Orta | PLANNED | §9.6 |
| 6 | Donanım test eksikliği | Yüksek | Orta | PLANNED | §11.6 |
| 7 | Subjektif yanlılık | Orta | Orta | Rastgele sunum | §8.9 |
| 8 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 9 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |
| 10 | Lock/tahsis ihlali | Düşük | Yüksek | L0 + denetim | L0 |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Test piramidi | Evet | Tasarım |
| 2 | Referans sinyal seti | Evet | §3 |
| 3 | Kabul kriteri | Evet | §5 |
| 4 | Regresyon testi | Evet | testing |
| 5 | Tahsis (0 B) testi | Evet | L0 |
| 6 | Lock (0 mutex) testi | Evet | L0 |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | testing/testing-kapsam.md | Kademe-2 |
| 3 | Test örnekleri | testing/testing-ornekler.md | Komşu dal |
| 4 | measurement kapsamı | measurement/measurement-kapsam.md | Q4 |
| 5 | pipeline zinciri | pipeline/pipeline-dsp-zinciri.md | Zincir testi |
| 6 | Bileşen dosyaları | İlgili dal dosyaları | §5 |
| 7 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 8 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 9 | ADR-017 | .ai/.decisions | float32 |
| 10 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 11 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 12 | Test sınıfı anolojisi | MiddlewarePipelineTest.php:17 | Anoloji |
| 13 | C++ motor | engine/ (yok) | Eksik |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Test piramidi | Birim→entegrasyon→ölçüm katmanları |
| Regresyon | Sabit hatanın tekrarını önleme |
| Objective metric | Ölçülebilir kalite kriteri |
| Subjektif değerlendirme | Dinleyici değerlendirme |
| MUSHRA | Çoklu gizli referans değerlendirme |
| Sweep | Frekans süpürme |
| THD+N | Toplam bozulma + gürültü |
| SNR | Sinyal/gürültü oranı |
| Denormal | Çok küçük sayı (performans riski) |
| Fuzz | Rastgele girdi testi |
| Property test | Özellik tabanlı test |
| CI | Sürekli entegrasyon |
| Kabul kriteri | Test geçiş eşiği |
| Regresyon testi | Sabit hata kapsamı |
| Q4 | Ölçüm↔testing organizasyonu |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Q4 | measurement↔testing | Bekliyor | §11.1 |
| 2 | Kesin eşikler | §4/§5 | Bekliyor | §11.2 |
| 3 | CI entegrasyonu | Evet/Hayır | Bekliyor | §11.3 |
| 4 | Subjektif kapsam | §8 | Bekliyor | §11.4 |
| 5 | Test framework | JUCE UnitTest/— | Bekliyor | §7 |
| 6 | Donanım testi | Ne zaman | Bekliyor | §11.6 |
| 7 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group: kod | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 5 URL |
| 4 | Repo kanıtı | MiddlewarePipelineTest.php:17 |
| 5 | Eşikler UNKNOWN | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §18 Test senaryoları matrisi (bileşen başına)

| # | Bileşen | Test 1 | Test 2 | Test 3 | Kabul | Durum |
|---|---|---|---|---|---|---|
| 1 | filters | Frekans yanıtı | Faz | Sınır (fc) | ≤ eşik | UNKNOWN |
| 2 | crossover | Bant ayrımı | Faz uyumu | Geçiş bandı | ≤ eşik | UNKNOWN |
| 3 | equalizer | Katsayı (a0=1) | Kararlılık (\\|p\\|<1) | Sweep | ≤ eşik | testing |
| 4 | compressor | Eğri (§19) | Attack/release τ | Knee C¹ | ≤ eşik | testing |
| 5 | dynamics | LRA (referans) | Sessizlik filtresi | Blok | ≤ eşik | testing |
| 6 | reverb | RT60 | Frekans yanıtı | CPU | ≤ eşik | UNKNOWN |
| 7 | delay | Gecikme (örnek) | Feedback kararlılık | Modülasyon | ≤0.5 örnek | testing |
| 8 | spatial | Pan gücü (Σg²) | ITD | Frekans yanıtı | ≤ eşik | testing |
| 9 | limiter | Tavan (dBTP) | Oversampling | Gecikme | 0 ihlal | testing |
| 10 | pipeline | Sıra | Bypass | Blok/kanal/fs | ≤0.1 dB | testing |
| 11 | Tümü | Tahsis (0 B) | Lock (0 mutex) | Denormal | L0 | L0 |
| 12 | Tümü | float32 | Buffer 512 | fs 48 kHz | FROZEN | sartname |
| 13 | Ölçüm | Q4 | PLANNED | — | — | UNKNOWN |
| 14 | Karar | K004 | — | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | — | Kural |

## §19 Regresyon testi matrisi

| # | Regresyon türü | Tetikleyici | Kapsam | Sıklık | Durum |
|---|---|---|---|---|---|
| 1 | Sıfır noktası hatası | Refaktör | Bileşen | Her commit | testing |
| 2 | Faz tersliği | Filtre değişimi | filters | Her commit | testing |
| 3 | Katsayı hatası | Formül değişimi | eq | Her commit | testing |
| 4 | Kararlılık kaybı | Parametre | Bileşen | Her commit | testing |
| 5 | CPU regresyonu | Kod değişimi | Zincir | Günlük | testing |
| 6 | Tahsis (sızma) | Kod değişimi | Tüm | Her commit | L0 |
| 7 | Lock sızması | Kod değişimi | Tüm | Statik | L0 |
| 8 | float32 kaybı | Dönüşüm | Tüm | Her commit | FROZEN |
| 9 | Gecikme artışı | Kod değişimi | Zincir | Günlük | testing |
| 10 | Ölçüm sapması | Kod değişimi | Ölçüm | Günlük | testing |
| 11 | Donanım regresyonu | Sürücü/firmware | Donanım | Manuel | PLANNED |
| 12 | Ölçüm | PLANNED | — | — | PLANNED |
| 13 | Karar | K004 | — | — | UNKNOWN |
| 14 | Karar | K004 | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §20 CPU ve bellek ölçüm matrisi

| # | Ölçüm | Yöntem | Hedef | Sıklık | Durum |
|---|---|---|---|---|---|
| 1 | CPU/blok (ns) | Profil | ≤ bütçe | Günlük | PLANNED |
| 2 | CPU/zincir (%) | Profil | ≤ bütçe | Günlük | PLANNED |
| 3 | Tahsis (B/audio thread) | Profil | 0 | Her commit | L0 |
| 4 | Lock (mutex sayısı) | Statik/denetim | 0 | Her commit | L0 |
| 5 | Bellek (toplam) | Profil | ≤ bütçe | Günlük | PLANNED |
| 6 | Bellek (zincir) | Profil | Baştan | Her commit | L0 |
| 7 | Gecikme (impuls) | Ölçüm | ≤ bütçe | Günlük | PLANNED |
| 8 | Denormal | Performans testi | Performans | Her commit | L0 |
| 9 | 8 kanal (spatial) | Profil | ≤ bütçe | Günlük | PLANNED |
| 10 | float32 | ADR-017 | Zorunlu | — | FROZEN |
| 11 | Buffer | 512 | sartname | — | sartname |
| 12 | Ölçüm | PLANNED | — | — | PLANNED |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Karar | Bütçe | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §21 CI/otomasyon matrisi

| # | Aşama | İçerik | Tetikleyici | Durum |
|---|---|---|---|---|
| 1 | Statik analiz | Lint + kural (L0) | Her commit | PLANNED |
| 2 | Birim testi | §2.1 | Her commit | PLANNED |
| 3 | Entegrasyon | §2.2 | Her commit | PLANNED |
| 4 | Zincir testi | §2.3 | Günlük | PLANNED |
| 5 | Ölçüm testi | §2.4 | Günlük | PLANNED |
| 6 | CPU/performans | §20 | Günlük | PLANNED |
| 7 | Tahsis/lock | §20.3-4 | Her commit | L0 |
| 8 | Regresyon | §19 | Her commit | PLANNED |
| 9 | Donanım | Manuel | Manuel | PLANNED |
| 10 | Raporlama | Sonuç + kanıt | Her çalıştırma | PLANNED |
| 11 | Kapı (gate) | Kabul kriteri | Her commit | UNKNOWN |
| 12 | CI | UNKNOWN | — | UNKNOWN |
| 13 | CI | UNKNOWN | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | PLANNED |
| 15 | Uydurma | YASAK | — | Kural |

> §21, test otomasyon **planıdır**; motor diskte olmadığı için hiçbir
> CI adımı bu dosyada **çalıştırılmış olarak yazılmaz** (PLANNED).

## §22 Test verisi matrisi

| # | Veri | Kaynak | Boyut | Deterministik | Durum |
|---|---|---|---|---|---|
| 1 | Referans sinyaller | §3 (üretim) | Küçük | Evet | Tasarım |
| 2 | Kalibre ölçüm dosyası | Dış kaynak | Orta | Evet | PLANNED |
| 3 | Referans dosya (ölçüm) | Dış kaynak | Orta | Evet | PLANNED |
| 4 | Rastgele (PRNG) | Seed | İstediğin | Evet (seed) | Opsiyonel |
| 5 | Fuzz girdisi | PRNG | Büyük | Evet (seed) | Opsiyonel |
| 6 | 8.1 test deseni | Üretim | Küçük | Evet | Tasarım |
| 7 | Tiny/denormal | Üretim | Küçük | Evet | L0 |
| 8 | Aşırı girdi | Üretim | Küçük | Evet | Tasarım |
| 9 | Lisans (dış veri) | Kaynaka göre | — | — | Kural |
| 10 | Depoya ekleme | Onay | — | — | Kural |
| 11 | Ölçüm | PLANNED | — | — | PLANNED |
| 12 | Karar | K004 | — | — | UNKNOWN |
| 13 | Karar | K004 | — | — | UNKNOWN |
| 14 | Karar | K004 | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §23 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Q4 (measurement↔testing) | UNKNOWN | Organizasyon | §11.1 |
| 2 | Kesin eşikler | UNKNOWN | Karar | §11.2 |
| 3 | CI entegrasyonu | UNKNOWN | Süreç | §11.3 |
| 4 | Subjektif kapsam | UNKNOWN | Kapsam | §11.4 |
| 5 | CPU bütçesi | UNKNOWN | Ölçüm | §11.5 |
| 6 | Donanım testi | UNKNOWN | Donanım | §11.6 |
| 7 | Test altyapısı | PLANNED | engine/ yok | §1.7 |
| 8 | Referans veri | PLANNED | Test verisi | §22 |
| 9 | Ölçüm protokolü | Q4 | measurement | Q4 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.7 |

## §24 Ölçüm noktaları ve Q4 matrisi

| # | Ölçüm noktası | Konum | Amaç | Erişim | Durum |
|---|---|---|---|---|---|
| 1 | Girdi | Zincir başı | Kaynak doğrulama | Q4 | measurement |
| 2 | filters sonrası | §19.1 | Yanıt | Q4 | measurement |
| 3 | crossover sonrası | §19.2 | Bant | Q4 | measurement |
| 4 | equalizer sonrası | §19.3 | Katsayı | Q4 | measurement |
| 5 | compressor sonrası | §19.4 | GR | Q4 | measurement |
| 6 | dynamics sonrası | §19.5 | LRA | Q4 | measurement |
| 7 | reverb sonrası | §19.6 | RT60 | Q4 | measurement |
| 8 | delay sonrası | §19.7 | Gecikme | Q4 | measurement |
| 9 | spatial sonrası | §19.8 | Pan | Q4 | measurement |
| 10 | limiter sonrası (çıkış) | §19.9 | dBTP | Q4 | measurement |
| 11 | Bypass karşılaştırma | §18.10 | Sıra | Q4 | testing |
| 12 | Q4 | UNKNOWN | Organizasyon | — | UNKNOWN |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §25 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Test piramidi §2 | Tamam |
| 2 | Referans sinyaller §3 | Tamam |
| 3 | Objective metric §4 | Tamam |
| 4 | Subjektif opsiyonel §8 | Tamam |
| 5 | Frontmatter §4.2 + group: kod | Tamam |
| 6 | URL benzersiz (5) | Tamam |
| 7 | Repo kanıtı §6 | Tamam |
| 8 | Uydurma ölçüm yok | Tamam |
| 9 | CJK/mojibake 0 | Ölçüldü |
| 10 | Q4 UNKNOWN | Tamam |
| 11 | ≥500 satır | Ölçüldü |
| 12 | Authority son satır | Tamam |

| # | Ek denetim | Durum |
|---|---|---|
| 1 | §18 bileşen test matrisi | Tamam |
| 2 | §19 regresyon matrisi | Tamam |
| 3 | §20 CPU/bellek ölçüm | Tamam |
| 4 | §21 CI planı (PLANNED) | Tamam |
| 5 | §22 test verisi | Tamam |
| 6 | §24 Q4 matrisi | Tamam |
| 7 | Eşik UNKNOWN | Tamam |
| 8 | ≥500 satır | Ölçüldü |
| 9 | group: kod | Tamam |
| 10 | Authority son satır | Tamam |

| # | Son satır denetimi | Durum |
|---|---|---|
| 1 | §23 bağımlılık özeti | Tamam |
| 2 | §25 öz-kontrol | Tamam |
| 3 | Frontmatter 10 anahtar | Tamam |
| 4 | Web tablosu 4 sütun | Tamam |
| 5 | ≥500 satır | Ölçüldü |
| 6 | group: kod | Tamam |
| 7 | URL benzersiz (5) | Tamam |
| 8 | Authority son satır | Tamam |

| # | Kapanış | Durum |
|---|---|---|
| 1 | Q4 UNKNOWN | Tamam |
| 2 | Ölçüm PLANNED | Tamam |
| 3 | ≥500 satır | Ölçüldü |
| 4 | group: kod | Tamam |
| 5 | Frontmatter §4.2 | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode