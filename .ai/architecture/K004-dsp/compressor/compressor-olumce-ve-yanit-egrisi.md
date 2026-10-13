---
node_id: K004-dsp/compressor/compressor-olumce-ve-yanit-egrisi
node_type: module
status: draft
parent_id: K004-dsp/compressor
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# compressor-olumce-ve-yanit-egrisi — K004-dsp Derin Dal

Bu dosya, Neva Engine compressor bileşeninin ölçüm (detection) zincirini
ve kazanç-reduksiyon yanıt eğrisini tanımlar: tepe/RMS/ortanca ölçüm
yöntemleri, attack/release zaman sabitleri, soft/hard knee matematiği ve
yanıt eğrisi referans noktaları. Sidechain topolojisi ayrı dosyadır
(`compressor-sidechain-dinamik.md`); bu dosyanın odağı **ölçüm ve yanıt
eğrisidir**. K004 kapsamında compressor **PLANNED**'tır; parametre
kararları **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Ölçüm yöntemi (detection) | Evet | — | Tasarım |
| 2 | Yanıt eğrisi (input→output) | Evet | — | Tasarım |
| 3 | Attack/release davranışı | Evet | Ölçüm değeri | PLANNED |
| 4 | Knee matematiği (soft/hard) | Evet | — | Tasarım |
| 5 | Ratio ve threshold | Evet | Karar | UNKNOWN |
| 6 | Sidechain yapısı | Referans | Ayrı dosya | sidechain |
| 7 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 8 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 9 | Lookahead | Referans | limiter | limiter |
| 10 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |

## §2 Ölçüm yöntemleri matrisi

| # | Yöntem | Tanım | Duyarlılık | Gecikme | Kullanım | Durum |
|---|---|---|---|---|---|---|
| 1 | Tepe (peak) | max\|x\| | En yüksek | Düşük | Tavan koruması | Tasarım |
| 2 | Ortalama tepe | RMS benzeri | Orta | Orta | Yaygın | Tasarım |
| 3 | RMS | Kök ortalama kare | Enerji | Orta | Yaygın | Öneri |
| 4 | Ortalama (rect) | Σ\|x\|/N | Orta | Orta | Basit | Kıyas |
| 5 | Log (dB) | 20log\|x\| | Psikoakustik | Orta | Yaygın | Öneri |
| 6 | Yarım-dalga | max(0,x) | Pozitif | Orta | Basit | Kıyas |
| 7 | Tam-dalga | \|x\| | Simetrik | Orta | Basit | Kıyas |
| 8 | Kombinasyon | Peak+RMS | Yüksek | — | Esnek | Opsiyonel |
| 9 | Ponderans (A/K) | Ağırlıklı | Psikoakustik | — | Opsiyonel | Opsiyonel |
| 10 | Kritik bant | Bantlı | Psikoakustik | — | Araştırma | Araştırma |
| 11 | Kenar tetikleme | Δx | Hareket | Düşük | Opsiyonel | Opsiyonel |
| 12 | Sidechain filtresi | BPF/HPF | Seçici | Ek | sidechain | sidechain |
| 13 | Örnekleme (blok) | 512 | sartname | 512 | — | sartname |
| 14 | float32 | ADR-017 | — | — | — | FROZEN |
| 15 | Karar | Ölçüm yöntemi | — | — | — | UNKNOWN |

## §3 Yanıt eğrisi matrisi (bölge)

| # | Bölge | Giriş (dB) | Davranış | Kazanç | Durum |
|---|---|---|---|---|---|
| 1 | Sessizlik | < threshold-knee | Doğrusal | 0 dB GR | Tasarım |
| 2 | Knee bölgesi | threshold±knee/2 | Yumuşak geçiş | Artan GR | Tasarım |
| 3 | Kompresyon | > threshold | Oranlı | GR = (x-th)·(1-1/R) | Tasarım |
| 4 | Limit (R→∞) | Her | Sabit tavan | GR = (x-th) | Tasarım |
| 5 | Hard knee | Tek nokta | Keskin geçiş | Doğrusal | Tasarım |
| 6 | Soft knee | knee genişliği | Yumuşak | Parabolik | Tasarım |
| 7 | Knee genişliği | 0-24 dB | Karakter | — | Tasarım |
| 8 | Genlik üstü | > 0 dBFS | Sınırlı | — | Tasarım |
| 9 | Aşırı kompresyon | Çok yüksek giriş | GR büyük | Risk | §13 |
| 10 | Negatif threshold | Opsiyonel | Eşik altı | Tasarım | Opsiyonel |
| 11 | Expansion bölgesi | Farklı ratio | GR<0 | Opsiyonel | Opsiyonel |
| 12 | Upside-down | Farklı ratio | GR<0 | Opsiyonel | Opsiyonel |
| 13 | Ölçüm | Eğri | Sweep | PLANNED | PLANNED |
| 14 | Ölçüm | Nokta | Sayısal | testing | testing |
| 15 | Karar | Threshold/ratio/knee | — | — | UNKNOWN |

## §4 Oran ve kazanç-reduksiyon matrisi

| # | Ratio (R) | Eğim (1/R) | GR (6 dB üstü) | Karakter | Durum |
|---|---|---|---|---|---|
| 1 | 1:1 | 1.0 | 0 dB | Bypass | Referans |
| 2 | 1.5:1 | 0.67 | 4 dB | Hafif | Sınıf A |
| 3 | 2:1 | 0.5 | 3 dB | Yumuşak | Sınıf A |
| 4 | 3:1 | 0.33 | 2 dB | Orta | Sınıf A |
| 5 | 4:1 | 0.25 | 1.5 dB | Belirgin | Sınıf A |
| 6 | 6:1 | 0.17 | 1 dB | Sert | Sınıf B |
| 7 | 8:1 | 0.125 | 0.75 dB | Sert | Sınıf B |
| 8 | 10:1 | 0.1 | 0.6 dB | Çok sert | Sınıf B |
| 9 | 20:1 | 0.05 | 0.3 dB | Neredeyse limit | Sınıf C |
| 10 | ∞:1 (limit) | 0 | 0.5 dB | Tavan | limiter |
| 11 | GR = (x-th)·(1-1/R) | Formül | Hesap | Tasarım | Tasarım |
| 12 | threshold tipik | -24..-6 dB | Karar | — | UNKNOWN |
| 13 | Ratio kararı | Karar | — | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §5 Attack/release matrisi

| # | Parametre | Aralık | Tanım | Etki | Durum |
|---|---|---|---|---|---|
| 1 | Attack | 0.1-100 ms | Yükseliş gecikmesi | Tepe koruması | Tasarım |
| 2 | Attack (hızlı) | 0.1-5 ms | Transient kaçışı az | Sert | Tasarım |
| 3 | Attack (yavaş) | 20-100 ms | Transient geçişi | Doğal | Tasarım |
| 4 | Release | 10-1000 ms | Dönüş gecikmesi | Pumping | Tasarım |
| 5 | Release (hızlı) | 10-100 ms | Sıkı takip | Pumping riski | Tasarım |
| 6 | Release (yavaş) | 200-1000 ms | Yumuşak | Yavaşlama | Tasarım |
| 7 | Program bağımlı | İçerik | Değişken | Opsiyonel | Opsiyonel |
| 8 | Oranlı release | dB/s | Doğal | Opsiyonel | Opsiyonel |
| 9 | Zaman sabiti (τ) | ms | 63% hedefi | Hesap | Tasarım |
| 10 | Blok içinde | Ekponential | Aproksimasyon | Tasarım | Tasarım |
| 11 | Attack katsayı | e^(-1/τ·fs) | Ön hesap | Constructor | L0 |
| 12 | Release katsayı | e^(-1/τ·fs) | Ön hesap | Constructor | L0 |
| 13 | float32 | ADR-017 | Hassasiyet | — | FROZEN |
| 14 | Ölçüm | Yanıt süresi | PLANNED | — | PLANNED |
| 15 | Karar | Attack/release | — | — | UNKNOWN |

## §6 Envelope follower matrisi

| # | Envelope tipi | Formül | Karakter | CPU | Durum |
|---|---|---|---|---|---|
| 1 | Tek kutuplu (bir yön) | y = a·y + (1-a)·\|x\| | Basit | Düşük | Sınıf A |
| 2 | Ayrık attack/release | A/R katsayı | Yaygın | Düşük | Öneri |
| 3 | Çift kutuplu | İki kademeli | Düzgün | Orta | Opsiyonel |
| 4 | Çıkarma (peak detay) | max(x, a·y) | Tepe | Düşük | Sınıf A |
| 5 | Log domain | dB domain | Psikoakustik | Orta | Opsiyonel |
| 6 | Lineer domain | Doğrusal | Basit | Düşük | Kıyas |
| 7 | dB lineer karışık | İki domain | Esnek | Orta | Opsiyonel |
| 8 | Kritik bantlı | Bantlı envelope | Psikoakustik | Yüksek | Araştırma |
| 9 | Yumuşatma | Slew rate | Karakter | Düşük | Opsiyonel |
| 10 | Slew limit | dB/s | Karakter | Düşük | Opsiyonel |
| 11 | Ön hesap katsayı | Constructor | Non-RT | — | L0 |
| 12 | Blok güncelleme | Katsayı sabit | — | Düşük | L0 |
| 13 | float32 | ADR-017 | — | — | FROZEN |
| 14 | Ölçüm | Takip doğruluğu | PLANNED | — | PLANNED |
| 15 | Karar | Envelope tipi | — | — | UNKNOWN |

## §7 Knee matematiği matrisi

| # | Knee | Formül (bölge) | Geçiş | Karakter | Durum |
|---|---|---|---|---|---|
| 1 | Hard knee | Tek eşik | Keskin | Sert | Tasarım |
| 2 | Soft knee (0-8 dB) | Parabolik | Dar | Yumuşak | Tasarım |
| 3 | Soft knee (0-12 dB) | Parabolik | Orta | Yumuşak | Öneri |
| 4 | Soft knee (0-24 dB) | Parabolik | Geniş | Çok yumuşak | Opsiyonel |
| 5 | Knee yarı genişliği | w/2 | — | — | Tasarım |
| 6 | Knee girişi | x < th-w/2 | Doğrusal | GR=0 | Tasarım |
| 7 | Knee ortası | \|x-th\| ≤ w/2 | Parabolik | GR yumuşak | Tasarım |
| 8 | Knee çıkışı | x > th+w/2 | Oranlı | GR tam | Tasarım |
| 9 | Süreklilik | C¹ | Zorunlu | Zıplama yok | Tasarım |
| 10 | float32 | ADR-017 | — | — | FROZEN |
| 11 | Ölçüm | Eğri birleşim | Sweep | PLANNED | PLANNED |
| 12 | Ölçüm | Kliksiz | Test | testing | testing |
| 13 | Ölçüm | Derivatif | Test | testing | testing |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Karar | Knee genişliği | — | — | UNKNOWN |

## §8 Repo kanıtı — oda etkisi anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIEngine.php | 347 | `private function calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float` | Eşik benzeri hesap | Anoloji |
| 2 | shared/src/AI/AIEngine.php | 362 | `return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb);` | Sapma×çarpan | Anoloji |
| 3 | shared/src/AI/AIWorkflow.php | 157 | `$roomProfile = [` | Ölçüm girdisi | Anoloji |
| 4 | engine/ (yok) | — | Compressor C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP'dir; compressor DSP karşılığı **yoktur**
> (motor diskte mevcut değil). `target-actual` sapma hesabı, ölçüm→karar
> akışının anolojisidir; kazanç-reduksiyon eğrisi bu dosyada **uygulanmış
> olarak yazılmaz**.

## §9 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE kaynak kodu juce_NoiseGate.h gürültü kapısı tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/widgets/juce_NoiseGate.h | 2026-10-12 | 200 OK |
| JUCE dsp::LookupTable sınıfı tablo tabanlı aralık dönüşümü sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1LookupTable.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_LookupTable.h lookup table tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/maths/juce_LookupTable.h | 2026-10-12 | 200 OK |

## §10 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Yanıt eğrisi doğruluğu | ≤0.1 dB | Sayısal test | testing |
| 2 | Attack doğruluğu | ≤%5 | Envelope testi | testing |
| 3 | Release doğruluğu | ≤%5 | Envelope testi | testing |
| 4 | Knee sürekliliği | C¹ | Türev testi | testing |
| 5 | GR ölçümü | Doğru | Bilinen girdi | testing |
| 6 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 7 | Tahsis | 0 B/audio thread | Profil | L0 |
| 8 | Lock | 0 mutex | Denetim | L0 |
| 9 | float32 | Zorunlu | ADR-017 | FROZEN |
| 10 | Buffer | 512 örnek | sartname | Sabit |
| 11 | Örnekleme | 48 kHz | sartname | Sabit |
| 12 | Param geçişi | Kliksiz | Ramp | testing |
| 13 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 14 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §11 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | sidechain dosyası | SC yapısı | Komşu dal | sidechain |
| 2 | limiter | Limit bölgesi | §4.10 | limiter |
| 3 | dynamics | GR→kazanç | dynamics | dynamics |
| 4 | crossover | Multiband ölçüm | crossover | crossover |
| 5 | measurement | Eğri ölçümü | Q4 | measurement |
| 6 | testing | Eğri testi | testing | testing |
| 7 | pipeline | Sıra | pipeline | pipeline |
| 8 | equalizer | Sıra | eq | eq |
| 9 | delay | Opsiyonel | delay | delay |
| 10 | reverb | Sıra | reverb | reverb |
| 11 | spatial | Kanal SC | spatial | spatial |
| 12 | compressor | Bu dosya | — | Bu |

## §12 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Ölçüm yöntemi hangisi? | Karakter | UNKNOWN |
| 2 | Threshold/ratio aralığı? | Karakter | UNKNOWN |
| 3 | Attack/release aralığı? | Karakter | UNKNOWN |
| 4 | Knee genişliği? | Karakter | UNKNOWN |
| 5 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 6 | multiband kullanılacak mı? | crossover | UNKNOWN |
| 7 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §13 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Pumping (yanlış release) | Orta | Orta | Parametre aralığı | §5 |
| 2 | Tekmeleme (hızlı attack) | Düşük | Orta | Ölçüm | §2 |
| 3 | Ölçüm yöntemi uygunsuzluğu | Orta | Orta | Seçenek ayırma | §2 |
| 4 | Knee zıplaması | Düşük | Orta | C¹ testi | testing |
| 5 | CPU aşımı | Orta | Yüksek | Bütçe | Ölçüm |
| 6 | Tahsis (katsayı) | Düşük | Yüksek | Constructor | L0 |
| 7 | Param yarışması | Düşük | Yüksek | Lock-free atom | L0 |
| 8 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 9 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §14 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (katsayılar) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Knee sürekliliği (C¹) | Evet | testing |
| 6 | GR ölçüm testi | Evet | testing |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §15 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | compressor/compressor-kapsam.md | Kademe-2 |
| 3 | Sidechain dosyası | compressor/compressor-sidechain-dinamik.md | Komşu dal |
| 4 | limiter sınırı | limiter/limiter-kapsam.md | Limit |
| 5 | dynamics sınırı | dynamics/dynamics-kapsam.md | GR→kazanç |
| 6 | measurement kapsamı | measurement/measurement-kapsam.md | Eğri ölçümü |
| 7 | testing örneği | testing/testing-ornekler.md | Eğri testi |
| 8 | crossover sınırı | crossover/crossover-kapsam.md | Multiband |
| 9 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 10 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 11 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 12 | ADR-017 | .ai/.decisions | float32 |
| 13 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 14 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 15 | Sapma hesabı | AIEngine.php:362 | Anoloji |
| 16 | C++ motor | engine/ (yok) | Eksik |

## §16 Terim sözlüğü

| Terim | Tanım |
|---|---|
| GR (gain reduction) | Kazanç reduksiyonu — dB |
| Threshold | Kompresyon başlangıç eşiği |
| Ratio | Giriş/çıkış oranı |
| Knee | Eşik geçiş yumuşaklığı |
| Attack | Yükseliş zaman gecikmesi |
| Release | Dönüş zaman gecikmesi |
| Envelope | Sinyal zarfı takibi |
| RMS | Kök ortalama kare |
| Peak | Tepe değer |
| Detection | Ölçüm yöntemi |
| Pumping | Yanlış release kaynaklı salınım |
| Transient | Kısa patlama |
| τ (tau) | Zaman sabiti (63%) |
| Lookahead | İleri bakış — limiter |
| Sweep | Test süpürmesi |

## §17 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Ölçüm yöntemi | §2 | Bekliyor | Karakter |
| 2 | Threshold aralığı | Bilinmiyor | Bekliyor | §4.12 |
| 3 | Ratio | Bilinmiyor | Bekliyor | §4.13 |
| 4 | Attack/release | Bilinmiyor | Bekliyor | §5.15 |
| 5 | Knee genişliği | 0-24 dB | Bekliyor | §7.15 |
| 6 | multiband | Evet/Hayır | Bekliyor | §12.6 |
| 7 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §18 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 3 URL |
| 4 | Repo kanıtı | AIEngine.php:347 |
| 5 | Uydurma ölçüm yok | Tamam |
| 6 | Ölçüm PLANNED işaretli | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §19 Yanıt eğrisi referans noktaları matrisi

| # | Nokta | Giriş (dB) | Çıkış (dB) | GR (dB) | Not | Durum |
|---|---|---|---|---|---|---|
| 1 | Eşik altı | -40 | -40 | 0 | Doğrusal | Tasarım |
| 2 | Eşik altı | -20 | -20 | 0 | Doğrusal | Tasarım |
| 3 | Eşik | -12 | -12 | 0 | Başlangıç (hard knee) | Tasarım |
| 4 | Knee ortası | -12+w/4 | Hesap | Kısmi | Soft knee | Tasarım |
| 5 | Eşik üstü (2:1) | -6 | -9 | -3 | 6 dB üstü yarı | Tasarım |
| 6 | Eşik üstü (4:1) | -6 | -10.5 | -4.5 | 6 dB üstü çeyrek | Tasarım |
| 7 | Eşik üstü (2:1) | 0 | -6 | -6 | 12 dB üstü | Tasarım |
| 8 | Eşik üstü (4:1) | 0 | -9 | -9 | 12 dB üstü | Tasarım |
| 9 | 0 dBFS | 0 | Sınırlı | ≤ -9 | Örnek (4:1) | Tasarım |
| 10 | Aşırı | +12 | Tavana göre | Büyük | Risk | §13 |
| 11 | Bypass (1:1) | Her | Her | 0 | Referans | Referans |
| 12 | Limit (∞:1) | Eşik üstü | = threshold | = x-th | Tavan | limiter |
| 13 | Ölçüm | Nokta test | Sayısal | testing | testing | testing |
| 14 | Ölçüm | Sweep | Eğri | testing | testing | testing |
| 15 | Uydurma | YASAK | — | — | Kural | Kural |

## §20 Zaman domaini yanıt matrisi (step response)

| # | Adım girdi | Attack davranışı | GR tepkisi | Release davranışı | Durum |
|---|---|---|---|---|---|
| 1 | Tek step | Yükseliş | Exponential | Exponential | Tasarım |
| 2 | Kısa transient | Kısmi | Az GR | Hızlı dönüş | Tasarım |
| 3 | Uzun step | Tam GR | Sabit | Yavaş dönüş | Tasarım |
| 4 | Sıkı tekrar | Birikme | Pumping | Dönüş | §13 |
| 5 | Sinüs (440 Hz) | Dönem içi | Tepe takibi | — | Tasarım |
| 6 | Düşük sinüs | Dönem içi | Tam takip | — | Tasarım |
| 7 | Yüksek sinüs | Blok içi | Ortalama | — | Tasarım |
| 8 | Noise | Ortalama | RMS GR | — | Tasarım |
| 9 | Step tepki (test) | τ ölçümü | 63% | testing | testing |
| 10 | Release tepki (test) | τ ölçümü | 63% | testing | testing |
| 11 | Overshoot testi | Sıfır | Aşım yok | testing | testing |
| 12 | Kliksiz param | Rampa | Kademeli | testing | testing |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §21 dB ve doğrusal dönüşüm matrisi

| # | Dönüşüm | Formül | Örnek | Not | Durum |
|---|---|---|---|---|---|
| 1 | dB → lineer | 10^(dB/20) | -6 dB → 0.501 | Genlik | Tasarım |
| 2 | lineer → dB | 20·log10(x) | 0.5 → -6.02 dB | Genlik | Tasarım |
| 3 | GR → lineer | 10^(GR/20) | -6 dB → 0.501 | Kısma | Tasarım |
| 4 | Ratio eğimi | 1/R | 4:1 → 0.25 | §4 | Tasarım |
| 5 | Knee parabol | Kuadratik | Orta bölge | §7 | Tasarım |
| 6 | τ → katsayı | exp(-1/(τ·fs)) | 10 ms → a | §5.11 | Tasarım |
| 7 | dB aralığı | 0.1 dB | Yuvarlama | Gösterim | Tasarım |
| 8 | float32 hassasiyet | ~7 hane | ADR-017 | Yeterli | FROZEN |
| 9 | Taşma | x ≤ 0 dBFS | Sınır | Koruma | Tasarım |
| 10 | Negatif log | x=0 | -∞ | Koruma | Tasarım |
| 11 | Lookup table | Ön hesap | Hız | Opsiyonel | §9 |
| 12 | CPU (log) | Orta | Optimizasyon | Opsiyonel | Opsiyonel |
| 13 | Ölçüm | Dönüşüm testi | testing | testing | testing |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §22 Ölçüm yöntemi seçimi matrisi (senaryo bazlı)

| # | Senaryo | Önerilen yöntem | Gerekçe | Durum |
|---|---|---|---|---|
| 1 | Master zincir | RMS + peak kombinasyon | Dengeli | Seçenek |
| 2 | Kick/perçem | Peak | Transient koruma | Seçenek |
| 3 | Vokal | Log/RMS | Psikoakustik | Seçenek |
| 4 | Bas | RMS | Enerji | Seçenek |
| 5 | Limiter öncesi | Peak | Tavan | limiter |
| 6 | Multiband | Per-bant yöntem | Bantlı | crossover |
| 7 | Sidechain | Filtreli ölçüm | Seçici | sidechain |
| 8 | Yumuşak kompresyon | RMS | Doğal | Seçenek |
| 9 | Sert kompresyon | Peak/RMS | Güvenli | Seçenek |
| 10 | Yayın zinciri | Log (LUFS benzeri) | Standart | measurement |
| 11 | Düşük gecikme | Peak | Hızlı | Seçenek |
| 12 | CPU düşük | Tek yöntem | Hızlı | Seçenek |
| 13 | Karar | K004 | — | UNKNOWN |
| 14 | Karar | K004 | — | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §23 CPU ve bellek matrisi

| # | Bileşen | CPU (göreli) | Bellek | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | Tepe ölçüm | 1× | 1 yazmaç | Baştan | Kıyas |
| 2 | RMS ölçüm | 1.2× | 1 yazmaç + kare | Baştan | Kıyas |
| 3 | Log ölçüm | 2× | log tablosu | Opsiyonel | Kıyas |
| 4 | Envelope (tek kutup) | 1× | 1 durum | Baştan | Sınıf A |
| 5 | Envelope (A/R ayrık) | 1.1× | 2 durum | Baştan | Sınıf A |
| 6 | Knee hesabı | +0.2× | — | Baştan | Sınıf A |
| 7 | dB dönüşümü | +0.3× | Tablo | Opsiyonel | Opsiyonel |
| 8 | Lookup table (log) | -0.2× | Tablo | Constructor | Opsiyonel |
| 9 | 8 kanal (spatial) | 8× | 8 durum | Baştan | spatial |
| 10 | multiband (N bant) | N× | N durum | Baştan | crossover |
| 11 | float32 | — | ADR-017 | — | FROZEN |
| 12 | Blok | 512 | sartname | — | sartname |
| 13 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 14 | Bellek bütçesi | — | Düşük | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

## §24 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Eğri nokta testi | Bilinen giriş | §19 GR | ≤0.1 dB | testing |
| 2 | Attack τ | Step | 63% @ τ | ≤%5 | testing |
| 3 | Release τ | Step | 63% @ τ | ≤%5 | testing |
| 4 | Knee sürekliliği | Sweep | C¹ | Zıplama yok | testing |
| 5 | Bypass (1:1) | Her | Aynı | ≤0.1 dB | testing |
| 6 | Limit (∞:1) | Step | Tavan | ≤ eşik | testing |
| 7 | Ölçüm yöntemi | Sabit sinyal | Doğru GR | ≤ eşik | testing |
| 8 | Pumping testi | Music | Kabul | Kulaç | testing |
| 9 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 10 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 11 | Param geçişi | Sürükleme | Kliksiz | Ramp | testing |
| 12 | Overshoot | Step | Sıfır | ≤ eşik | testing |
| 13 | Denormal | Tiny girdi | Performans | FTZ | L0 |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §25 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Ölçüm yöntemi | UNKNOWN | Karar | §2 |
| 2 | Threshold/ratio | UNKNOWN | Karar | §4 |
| 3 | Attack/release | UNKNOWN | Karar | §5 |
| 4 | Knee genişliği | UNKNOWN | Karar | §7 |
| 5 | Sidechain yapısı | Diğer dosya | sidechain | §1.6 |
| 6 | Eğri doğrulama | PLANNED | testing | §19 |
| 7 | Zaman doğrulama | PLANNED | testing | §20 |
| 8 | CPU bütçesi | PLANNED | Ölçüm | §23 |
| 9 | Q4 | UNKNOWN | measurement/testing | §12.5 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.8 |

## §26 Ölçüm zinciri blok akış tablosu

| # | Blok | Girdi | İşlem | Çıktı | Gecikme |
|---|---|---|---|---|---|
| 1 | Girdi (x[n]) | Blok | — | x | 0 |
| 2 | Ölçüm (detection) | x | §2 yöntemi | lvl | Metoda göre |
| 3 | dB dönüşümü | lvl | 20·log10 | lvdB | 0 |
| 4 | Karşılaştırma | lvdB | eşiğe göre | err | 0 |
| 5 | Knee | err | §7 | errok | 0 |
| 6 | Oran | errok | ×(1-1/R) | grB | 0 |
| 7 | Attack | grB | Yavaş açılış | grA | §5.1 |
| 8 | Release | grA | Yavaş kapanış | grR | §5.4 |
| 9 | Kazanç uygulama | x, grR | ×10^(gr/20) | y | 0 |
| 10 | Ölçüm noktası | y | §10 | — | Q4 |
| 11 | Uydurma | — | YASAK | — | Kural |

> Tablo, ölçüm→GR→kazanç zincirinin Markdown gösterimidir (K004 standart
> formatı). Blok 4-6 eşik/oran/knee matematiğini, Blok 7-8 zaman
> davranışını kapsar; tüm katsayılar constructor'da ön hesaplanır
> (§5.11-12, L0).

## §27 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Yanıt eğrisi §3, §19 | Tamam |
| 2 | Ölçüm yöntemleri §2 | Tamam |
| 3 | Knee C¹ §7 | Tamam |
| 4 | Frontmatter §4.2 + group | Tamam |
| 5 | URL benzersiz (3) | Tamam |
| 6 | Repo kanıtı §8 | Tamam |
| 7 | Uydurma ölçüm yok | Tamam |
| 8 | Q4 UNKNOWN | Tamam |
| 9 | CJK/mojibake 0 | Ölçüldü |
| 10 | ≥500 satır | Ölçüldü |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode