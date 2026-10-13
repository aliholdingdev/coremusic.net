---
node_id: K004-dsp/dynamics/dynamics-dinamik-aralik-ve-lra
node_type: module
status: draft
parent_id: K004-dsp/dynamics
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# dynamics-dinamik-aralik-ve-lra — K004-dsp Derin Dal

Bu dosya, Neva Engine dynamics bileşeninin dinamik aralık (dynamic range)
kavramını ve LRA (Loudness Range) ölçüm yöntemini tanımlar: seviye
ölçümünün istatistiksel işlenmesi (blok Loudness, kısa süreli Loudness,
gerekli-sessizlik eşiği), aralık hesabı ve zincir üzerindeki etkisi.
Kazanç zarfı ayrı dosyadır (`dynamics-gain-zarfi.md`); bu dosyanın
odağı **dinamik aralık ve LRA ölçüm mantığıdır**. K004 kapsamında
ölçüm **PLANNED**'tır; hedef LRA/kalite eşikleri **yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut PHP/JS satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Dinamik aralık tanımı | Evet | — | Tasarım |
| 2 | LRA ölçüm yöntemi | Evet | Yayın kararı | Tasarım |
| 3 | Loudness blokları | Evet | Standart kalibrasyon doğrulaması | Ölçüm PLANNED |
| 4 | Gerekli-sessizlik eşiği | Evet | — | Tasarım |
| 5 | Kazanç zarfı | Referans | Ayrı dosya | gain-zarfi |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |
| 9 | loudness standardı | Referans | Uygulama kararı | UNKNOWN |
| 10 | Hedef LRA (içerik) | Hayır | Ürün kararı | UNKNOWN |

## §2 Dinamik aralık matrisi

| # | Kavram | Tanım | Birim | Hesap | Durum |
|---|---|---|---|---|---|
| 1 | Dinamik aralık | En sessiz ile en yüksek anlık seviye farkı | dB | max-min | Tasarım |
| 2 | Anlık dinamik | Kısa pencere (1-40 ms) | dB | Pencere | Tasarım |
| 3 | Sınırlı dinamik | Ölçüm penceresi (sabit) | dB | Pencere | Tasarım |
| 4 | LRA | Loudness aralığı (istatistiksel) | LU | Q10-Q95 | Tasarım |
| 5 | Peak-to-RMS | Tepe ile ortalama farkı | dB | peak-rms | Tasarım |
| 6 | Crest faktörü | Tepe/RMS oranı | dB | 20log | Tasarım |
| 7 | Gereksinim oranı | Sinyal/gürültü | dB | — | sartname |
| 8 | Eşik (noise floor) | Gerekli-sessizlik | LUFS | Blok filtresi | Tasarım |
| 9 | Blok Loudness | Kısa blok değeri | LUFS | Kilo-gram | Tasarım |
| 10 | Ölçüm penceresi | 400 ms blok (referans) | — | Referans | Tasarım |
| 11 | Geçiş (overlap) | Bloklar arası | %75 | Referans | Tasarım |
| 12 | Referans | 0 LUFS = referans | — | Standart | Tasarım |
| 13 | float32 | ADR-017 | — | — | FROZEN |
| 14 | Ölçüm değeri | PLANNED | — | — | PLANNED |
| 15 | Karar | Hedef aralık | — | — | UNKNOWN |

## §3 Loudness ölçüm zinciri matrisi

| # | Adım | İşlem | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | Kanal ayrımı | Per-kanal | 8.1 | L(i) | Tasarım |
| 2 | Filtreleme | Ön-filtre (yüksek geçiren) | Kanal | Filtreli | Tasarım |
| 3 | Ağırlıklandırma | Kilo-gram filtresi | Filtreli | Ağırlıklı | Tasarım |
| 4 | Kareleme | x² | Ağırlıklı | Güç | Tasarım |
| 5 | Kanal toplamı | Σ L(i) (ağırlıklı) | Güç | Toplam | Tasarım |
| 6 | Bloklama | 400 ms blok, %75 örtüşme | Toplam | Blok listesi | Tasarım |
| 7 | Logaritma | -0.691 + 10·log10(Σ güç) | Blok | LUFS blok | Tasarım |
| 8 | Gerekli-sessizlik | Eşik altı bloklar atılır | Blok listesi | Filtreli | Tasarım |
| 9 | İstatistik | Q10-Q95 | Filtreli | LRA | Tasarım |
| 10 | Entegre | Tüm (gerekli-sessizlikli) | Filtreli | LUFS | Tasarım |
| 11 | Kısa süreli | 3 s pencere | Filtreli | LUFS | Tasarım |
| 12 | Momentane | 400 ms anlık | Filtreli | LUFS | Tasarım |
| 13 | float32 | ADR-017 | — | — | FROZEN |
| 14 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 15 | Karar | Hesap detayı | — | — | UNKNOWN |

## §4 LRA hesabı matrisi

| # | Öğe | Değer/akış | Not | Durum |
|---|---|---|---|---|
| 1 | Blok değeri | Kısa süreli LUFS blokları | §3.6-7 | Tasarım |
| 2 | Gerekli-sessizlik filtresi | -70 LUFS altı atılır (referans) | §3.8 | Tasarım |
| 3 | Bağıl eşik | -20 LU blok üstü kabul (referans) | §3.8 | Tasarım |
| 4 | Sıralama | Artan | İstatistik | Tasarım |
| 5 | Q10 (altsonsuz) | %10 yüzdelik | Alt sınır | Tasarım |
| 6 | Q95 (üstsonsuz) | %95 yüzdelik | Üst sınır | Tasarım |
| 7 | LRA | Q95-Q10 | LU | Tasarım |
| 8 | Pencere | 3 s kısa süreli bloklar | Referans | Tasarım |
| 9 | LRA (kısa) | Alternatif | Opsiyonel | Opsiyonel |
| 10 | Stereo/multichannel | Kanal toplamlı | §3.5 | Tasarım |
| 11 | LFE | Dahil edilmez (referans) | §4 kanal | Tasarım |
| 12 | float32 | ADR-017 | — | FROZEN |
| 13 | Ölçüm | Doğrulama | PLANNED | PLANNED |
| 14 | Ölçüm | Hedef LRA | PLANNED | PLANNED |
| 15 | Karar | Hesap parametresi | — | UNKNOWN |

## §5 Gerekli-sessizlik matrisi

| # | Durum | Blok eşiği | Filtreleme | Sonuç | Durum |
|---|---|---|---|---|---|
| 1 | Mutlak sessizlik | < -70 LUFS | Atılır | Temizlenmiş | Tasarım |
| 2 | Bağıl sessizlik | < (entegre - 20 LU) | Atılır | Temizlenmiş | Tasarım |
| 3 | Her ikisi | İki koşul (VE) | Atılır | Temizlenmiş | Tasarım |
| 4 | Sessizlik uzunluğu | Kesinti süresi | Değerlendirme | Opsiyonel | Opsiyonel |
| 5 | Kısa kesinti | < 2 s | Dahil (referans) | — | Tasarım |
| 6 | Uzun kesinti | ≥ 2 s | Bölücü | §5.4 | Tasarım |
| 7 | Bölücü (gap) | ≥ 2 s | Sıfırla | Yeni segment | Tasarım |
| 8 | Gürültü zemini | Sürekli | Kalır | Etki | §13 |
| 9 | Sabit ses | Tek blok | Kalır | LRA ~0 | Tasarım |
| 10 | Giriş | Aralıklı | Temiz | Yüksek LRA | Tasarım |
| 11 | Hatalı eşik | Yanlış | Bozuk | Ölçüm hatası | §13 |
| 12 | float32 | ADR-017 | — | — | FROZEN |
| 13 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 14 | Ölçüm | Referans dosyayla | PLANNED | — | PLANNED |
| 15 | Karar | Eşik değeri | — | — | UNKNOWN |

## §6 Ölçüm penceresi karşılaştırma matrisi

| # | Pencere | Süre | Amaç | Hassasiyet | Gecikme | Durum |
|---|---|---|---|---|---|---|
| 1 | Anlık (momentane) | 400 ms | Tepe anlık | Yüksek | Düşük | Tasarım |
| 2 | Kısa süreli | 3 s | Dinamik takip | Orta | Orta | Tasarım |
| 3 | Entegre | Tüm içerik | Ortalama | Yüksek | Yok | Tasarım |
| 4 | Blok (LRA) | 3 s bloklar | İstatistik | Orta | Yok | Tasarım |
| 5 | Anlık dinamik | 1-40 ms | Peak | Çok yüksek | Düşük | Tasarım |
| 6 | RMS (tipik) | 100-500 ms | Ortalama | Orta | Orta | Kıyas |
| 7 | Program (uzun) | dakikalar | Genel | Düşük | Yok | Opsiyonel |
| 8 | Pencere şekli | Dikdörtgen/pencereli | Kaçak | — | — | Tasarım |
| 9 | Örtüşme | %75 (referans) | Süreklilik | — | — | Tasarım |
| 10 | Kararlılık | Uzun pencere | Düşük varyans | — | — | Tasarım |
| 11 | Yanıt hızı | Kısa pencere | Hızlı takip | — | — | Tasarım |
| 12 | float32 | ADR-017 | — | — | — | FROZEN |
| 13 | Ölçüm | Doğrulama | PLANNED | — | PLANNED | PLANNED |
| 14 | Ölçüm | Karşılaştırma | PLANNED | — | PLANNED | PLANNED |
| 15 | Karar | Pencere seti | — | — | — | UNKNOWN |

## §7 Repo kanıtı — analiz beslemesi anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIWorkflow.php | 109 | `$analysis = $this->engine->analyzeAudio($filePath);` | Analiz çağrısı | Anoloji |
| 2 | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 63 | `fromClientX(clientX, trackLeft, trackWidth) {` | Değer haritalama | Anoloji |
| 3 | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 54 | `return Math.min(1, Math.max(0, value));` | Aralığa sıkıştırma | Anoloji |
| 4 | engine/ (yok) | — | Loudness C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar PHP/JS'dir; loudness/LRA DSP karşılığı
> **yoktur** (motor diskte mevcut değil). Satırlar analiz çağrısı,
> haritalama ve clamp anolojisidir; LRA hesabı bu dosyada uygulanmış
> olarak **yazılmaz**, yöntem olarak tanımlanır.

## §8 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| EBU Tech 3342 LRA (Loudness Range) ölçüm yöntemini tanımlar | https://tech.ebu.ch/docs/tech/tech3342.pdf | 2026-10-12 | 200 OK |
| EBU R128 yayın loudness hedeflerini tanımlar | https://tech.ebu.ch/docs/r/r128.pdf | 2026-10-12 | 200 OK |
| ITU-R BS.1770 loudness ölçüm algoritmasını tanımlar | https://www.itu.int/rec/R-REC-BS.1770/en | 2026-10-12 | 200 OK |

## §9 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | LRA doğruluk | Referans dosyayla eşleşme | Karşılaştırma | testing |
| 2 | Blok doğruluğu | ≤0.1 LU | Sayısal test | testing |
| 3 | Sessizlik filtresi | Beklenen temizleme | Test | testing |
| 4 | Kanal toplamı | Doğru ağırlık | Test | testing |
| 5 | float32 | Zorunlu | ADR-017 | FROZEN |
| 6 | Buffer | 512 örnek | sartname | Sabit |
| 7 | Örnekleme | 48 kHz | sartname | Sabit |
| 8 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 9 | Tahsis | 0 B/audio thread | Profil | L0 |
| 10 | Lock | 0 mutex | Denetim | L0 |
| 11 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 12 | Hedef LRA | Karar | — | UNKNOWN |
| 13 | Standart kalibrasyon | Referans kaynakla | Doğrulama | testing |
| 14 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §10 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | gain-zarfi dosyası | Kazanç uygulama | Komşu dal | gain-zarfi |
| 2 | compressor | GR istatistiği | Ölçüm ortak | compressor |
| 3 | limiter | Tavan davranışı | Öncesi/sıra | limiter |
| 4 | measurement | Ölçüm altyapısı | Q4 | measurement |
| 5 | testing | Ölçüm testi | testing | testing |
| 6 | pipeline | Sıra | pipeline | pipeline |
| 7 | equalizer | EQ sonrası aralık | eq | eq |
| 8 | crossover | Multiband aralık | crossover | crossover |
| 9 | spatial | Kanal aralığı | spatial | spatial |
| 10 | reverb | Sıra | reverb | reverb |
| 11 | delay | Opsiyonel | delay | delay |
| 12 | dynamics | Bu dosya | — | Bu |

## §11 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Hedef LRA aralığı (ürün)? | Ürün kararı | UNKNOWN |
| 2 | Ölçüm entegre mi, sadece aralık mı? | Kapsam | UNKNOWN |
| 3 | Loudness ölçümü feature olarak mı? | Kapsam | UNKNOWN |
| 4 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 5 | Hangi standarda tam uyum? | Doğrulama | UNKNOWN |
| 6 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 7 | LFE dahil mi (ölçümde)? | Kanal | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §12 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Ölçüm sapması (yanlış filtre) | Orta | Yüksek | Referans karşılaştırma | testing |
| 2 | Sessizlik eşiği yanlışlığı | Orta | Orta | İki koşullu filtre | §5 |
| 3 | Kanal ağırlık hatası | Düşük | Yüksek | Test | testing |
| 4 | CPU aşımı (blok işleme) | Orta | Yüksek | Blok/özet | Ölçüm |
| 5 | Tahsis (blok tamponu) | Düşük | Yüksek | Constructor | L0 |
| 6 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 7 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 8 | Standart sapması | Orta | Orta | Doğrulama | §9.13 |
| 9 | Lock kullanımı | Düşük | Yüksek | Lock-free | L0 |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (blok tamponu) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Sessizlik filtresi iki koşullu | Evet | §5 |
| 6 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 7 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 8 | PCM5122 yasağı | Evet | ADR-038 |
| 9 | Buffer 512 / 48 kHz | Evet | sartname |
| 10 | Referans karşılaştırma zorunlu | Evet | testing |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | dynamics/dynamics-kapsam.md | Kademe-2 |
| 3 | Kazanç zarfı | dynamics/dynamics-gain-zarfi.md | Komşu dal |
| 4 | compressor sınırı | compressor/compressor-kapsam.md | Ölçüm ortak |
| 5 | limiter sınırı | limiter/limiter-kapsam.md | Sıra |
| 6 | measurement kapsamı | measurement/measurement-kapsam.md | Ölçüm |
| 7 | testing örneği | testing/testing-ornekler.md | Doğrulama |
| 8 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 9 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 10 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 11 | ADR-017 | .ai/.decisions | float32 + 8.1 |
| 12 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 13 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 14 | Analiz çağrısı | AIWorkflow.php:109 | Anoloji |
| 15 | Clamp | volume.js:54 | Anoloji |
| 16 | C++ motor | engine/ (yok) | Eksik |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Dinamik aralık | En sessiz-en yüksek seviye farkı |
| LRA | Loudness Range — istatistiksel loudness aralığı |
| Loudness | Algılanan ses şiddeti (LUFS) |
| Gerekli-sessizlik | Ölçümden çıkarılan sessiz bloklar |
| Q10/Q95 | Yüzdelik alt/üst sınırlar |
| Blok Loudness | Kısa pencere loudness değeri |
| Entegre loudness | Tüm içerik ortalaması |
| Momentane | Anlık (400 ms) loudness |
| Crest faktörü | Tepe/RMS oranı |
| Noise floor | Gürültü zemini |
| Kilo-gram filtresi | Loudness ağırlık filtresi |
| Pencere | Ölçüm zaman aralığı |
| Örtüşme | Bloklar arası kaplama |
| LU | Loudness birimi (fark) |
| LUFS | Loudness birimi (mutlak) |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Hedef LRA | Bilinmiyor | Bekliyor | §11.1 |
| 2 | Ölçüm kapsamı | LRA/Loudness/ikisi | Bekliyor | §11.2 |
| 3 | Sessizlik eşikleri | §5 referans | Bekliyor | Ölçüm |
| 4 | Pencere seti | §6 | Bekliyor | Standart |
| 5 | LFE dahil mi | Evet/Hayır | Bekliyor | §11.7 |
| 6 | Standart uyumu | Tam/parçalı | Bekliyor | §11.5 |
| 7 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 3 URL |
| 4 | Repo kanıtı | AIWorkflow.php:109 |
| 5 | Uydurma ölçüm yok | Tamam |
| 6 | Ölçüm PLANNED işaretli | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §18 Dinamik aralık ölçüm senaryoları matrisi

| # | İçerik tipi | Tipik LRA | Ölçüm beklentisi | Risk | Durum |
|---|---|---|---|---|---|
| 1 | Klasik müzik (konser) | 20-30 LU | Yüksek | Ölçüm | Kıyas |
| 2 | Film (sinema) | 15-25 LU | Yüksek | Ölçüm | Kıyas |
| 3 | Podcast (doğal) | 6-12 LU | Orta | — | Kıyas |
| 4 | Radyo (komprime) | 3-6 LU | Düşük | Bilgi kaybı | Kıyas |
| 5 | Pop albüm | 4-8 LU | Orta | Pumping riski | Kıyas |
| 6 | Jazz/dinamik | 10-20 LU | Yüksek | — | Kıyas |
| 7 | Elektronik | 5-10 LU | Orta | — | Kıyas |
| 8 | Konuşma (eğitim) | 5-10 LU | Orta | — | Kıyas |
| 9 | Arka plan (BGM) | 2-5 LU | Düşük | Düz | Kıyas |
| 10 | Test sinyali (sweep) | ~0 LU | Kontrol | Kontrol | testing |
| 11 | Step girdi | Kontrollü | Doğrulama | Kontrol | testing |
| 12 | Referans dosya | Bilinen LRA | Kalibrasyon | Zorunlu | testing |
| 13 | Gerçek içerik | Ölçüm | PLANNED | — | PLANNED |
| 14 | Karar (hedef) | Ürün | — | — | UNKNOWN |
| 15 | Karar (hedef) | Ürün | — | — | UNKNOWN |

> Tipik LRA değerleri literatür karşılaştırmasıdır (EBU R128 çevresi
> yayın pratiği); bu dosyada **ölçüm iddiası olarak sunulmaz** ve K004
> ürün hedefi `UNKNOWN`'dur.

## §19 Loudness istatistik matrisi

| # | İstatistik | Tanım | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | Ortalama (entegre) | Bütün blok ortalama | Blok listesi | LUFS | Tasarım |
| 2 | Medyan | %50 nokta | Blok listesi | LUFS | Opsiyonel |
| 3 | Q10 | Altsonsuz | Sıralı blok | LUFS | Tasarım |
| 4 | Q95 | Üstsonsuz | Sıralı blok | LUFS | Tasarım |
| 5 | LRA | Q95-Q10 | Blok listesi | LU | Tasarım |
| 6 | Standart sapma | Yayılmışlık | Blok listesi | LU | Opsiyonel |
| 7 | Varyans | Yayılmışlık² | Blok listesi | LU² | Opsiyonel |
| 8 | Yüzdelik (diğer) | Q1-Q99 | Sıralı blok | LUFS | Opsiyonel |
| 9 | Maksimum | En yüksek kısa süreli | Blok listesi | LUFS | Tasarım |
| 10 | Minimum | En düşük (gerekli-sessizlik sonrası) | Blok listesi | LUFS | Tasarım |
| 11 | Zaman serisi | Blok zaman damgası | Blok listesi | Dizi | Opsiyonel |
| 12 | Hızlı yeniden hesap | Artımlı | Blok akışı | Dizi | Opsiyonel |
| 13 | float32 | ADR-017 | — | — | FROZEN |
| 14 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 15 | Karar | İstatistik seti | — | — | UNKNOWN |

## §20 Zincir etkisi matrisi (dinamik aralık bakımından)

| # | Zincir öğesi | Aralık etkisi | Yön | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Girdi kaynağı | Kaynak LRA | Başlangıç | measurement | measurement |
| 2 | equalizer | Minimal | ~0 | testing | testing |
| 3 | crossover | Minimal (bant) | ~0 | testing | testing |
| 4 | compressor | Genişliği kısıt | Daraltır | Ölçüm | Ölçüm |
| 5 | dynamics (kazanç) | Sabit | 0 dB | testing | testing |
| 6 | reverb | Gürültü zemini yükselt | Daraltır | Ölçüm | Ölçüm |
| 7 | delay | Minimal | ~0 | testing | testing |
| 8 | limiter | Tepe kısıt | Üst sınır | Ölçüm | Ölçüm |
| 9 | spatial | Kanal başına | Bölünmüş | Ölçüm | Örnek |
| 10 | Ölçüm noktası (çıkış) | Son LRA | Sonuç | measurement | measurement |
| 11 | Ölçüm noktası (girdi) | İlk LRA | Başlangıç | measurement | measurement |
| 12 | Fark (ΔLRA) | Zincir etkisi | Beklenen | testing | testing |
| 13 | Pipeline sırası | — | §19 pipeline | pipeline | pipeline |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §21 CPU ve bellek matrisi

| # | İşlem | Sıklık | CPU | Bellek | Durum |
|---|---|---|---|---|---|
| 1 | Filtre (ön + kilo-gram) | Sürekli | Orta | 2 durum | Tasarım |
| 2 | Kareleme | Sürekli | Düşük | — | Tasarım |
| 3 | Blok toplama | 400 ms'de bir | Düşük | Blok tamponu | Tasarım |
| 4 | Blok listesi tutma | Tam içerik | — | Artan | §13.1 |
| 5 | Sıralama (percentile) | Sonuçta | Düşük (tek) | Blok listesi | Opsiyonel |
| 6 | Artımlı hesap | Her blok | Düşük | Sabit | Opsiyonel |
| 7 | Entegre akış | Her blok | Düşük | Sabit | Tasarım |
| 8 | 8 kanal (spatial) | ×8 | Orta | ×8 | spatial |
| 9 | Zaman damgası | Her blok | Düşük | Dizi | Opsiyonel |
| 10 | Tahsis | Audio thread | 0 B | Baştan | L0 |
| 11 | Lock | Her işlem | 0 mutex | — | L0 |
| 12 | float32 | ADR-017 | — | — | FROZEN |
| 13 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 14 | Bellek bütçesi | Bilinmiyor | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

## §22 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Referans dosya | Bilinen LRA | Eşleşen LRA | ≤0.5 LU | testing |
| 2 | Sessizlik temizliği | Sessiz bloklu | Temizlenmiş | Beklenen | testing |
| 3 | Sabit sinyal | 1 sinyal | LRA ~0 | ≤0.5 LU | testing |
| 4 | Step (kontrollü) | Bilinen aralık | Beklenen Q10/Q95 | ≤0.5 LU | testing |
| 5 | Blok doğruluğu | Sinyal | Blok LUFS | ≤0.1 LU | testing |
| 6 | Kanal toplamı | Multichannel | Doğru toplam | ≤0.1 LU | testing |
| 7 | Pencere örtüşmesi | Blok dizisi | %75 | Doğru | testing |
| 8 | Uzun kesinti | Boşluklu | Bölücü | Beklenen | testing |
| 9 | float32 yuvarlama | Değer | ≤1 LSB | Test | testing |
| 10 | CPU profil | Tam içerik | Bütçe | ≤ | PLANNED |
| 11 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 12 | Gürültü zemini | Noise'lu | Bilinen sapma | ≤ eşik | testing |
| 13 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural | Kural |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §23 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Hedef LRA | UNKNOWN | Ürün kararı | §11.1 |
| 2 | Ölçüm kapsamı | UNKNOWN | Kapsam | §11.2 |
| 3 | Sessizlik eşikleri | UNKNOWN | Karar | §5 |
| 4 | Pencere seti | UNKNOWN | Standart | §6 |
| 5 | Standart uyumu | UNKNOWN | Doğrulama | §11.5 |
| 6 | LRA doğrulama | PLANNED | testing | §22 |
| 7 | Referans dosyalar | PLANNED | Test verisi | §18.12 |
| 8 | CPU/bellek bütçesi | PLANNED | Ölçüm | §21 |
| 9 | Q4 | UNKNOWN | measurement/testing | §11.4 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.7 |

## §24 Ölçüm zinciri blok akış tablosu (loudness)

| # | Blok | Girdi | İşlem | Çıktı | Not |
|---|---|---|---|---|---|
| 1 | Girdi (x[n]) | Blok | — | x | 8 kanal |
| 2 | Kanal ayrımı | x | Kanallara böl | L(i) | ADR-017 |
| 3 | Ön-filtre | L(i) | Yüksek geçiren | F(i) | Standart |
| 4 | Kilo-gram | F(i) | Ağırlık | W(i) | Standart |
| 5 | Kareleme | W(i) | x² | P(i) | Güç |
| 6 | Kanal toplamı | P(i) | Σ (ağırlıklı) | Ptot | §3.5 |
| 7 | Blok | Ptot | 400 ms, %75 | Pb | §3.6 |
| 8 | Logaritma | Pb | -0.691 + 10·log10 | LUFSb | §3.7 |
| 9 | Sessizlik filtresi | LUFSb | §5 koşulları | Fb | §5 |
| 10 | İstatistik | Fb | Q10/Q95 (§19) | LRA | §4 |
| 11 | Ölçüm noktası | — | §9 | — | Q4 |
| 12 | Uydurma | — | YASAK | — | Kural |

> Tablo, loudness ölçüm zincirinin Markdown gösterimidir (K004 standart
> formatı). Blok 3-5 filtre/katsayıları doğrulanmış kaynaktan gelir
> (§8); burada **yöntem sırası** tanımlanır, sayısal katsayılar bu
> dosyada ikinci kez iddia edilmez.

## §25 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | LRA hesabı §4 | Tamam |
| 2 | Gerekli-sessizlik §5 | Tamam |
| 3 | Frontmatter §4.2 + group | Tamam |
| 4 | URL benzersiz (3) | Tamam |
| 5 | Repo kanıtı §7 | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Ellipsis 0 | Ölçüldü |
| 8 | Q4 UNKNOWN | Tamam |
| 9 | CJK/mojibake 0 | Ölçüldü |
| 10 | ≥500 satır | Ölçüldü |
| 11 | Authority son satır | Tamam |

## §26 Karşılaştırma — dinamik zarf matrisi

| # | Ölçüm/paket | Ne ölçer | Pencere | Kullanım | Durum |
|---|---|---|---|---|---|
| 1 | Anlık dinamik aralık | En sessiz-en yüksek fark | Tüm | Genel | Kıyas |
| 2 | LRA | Loudness aralığı (istatistiksel) | 3 s bloklar | Yayın | §4 |
| 3 | Peak-to-RMS | Tepe-ortalama farkı | İçeriğe göre | Basit | Kıyas |
| 4 | Crest faktörü | Tepe/RMS oranı | Sinyal | Analiz | Kıyas |
| 5 | Entegre loudness | Ortalama şiddet | Tüm içerik | Hedef | §3.10 |
| 6 | Kısa süreli loudness | 3 s takip | 3 s | Takip | §3.11 |
| 7 | Momentane loudness | Anlık şiddet | 400 ms | Anlık | §3.12 |
| 8 | RMS zarfı | Enerji takibi | 100-500 ms | DSP | dynamics |
| 9 | Tepe zarfı | Tepe takibi | Anlık | DSP | dynamics |
| 10 | GR istatistiği | Redüksiyon ortalaması | İçeriğe göre | compressor | compressor |
| 11 | Tavan ihlali | 0 dBFS üstü | Anlık | limiter | limiter |
| 12 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 13 | Ölçüm | Hedef eşik | PLANNED | — | PLANNED |
| 14 | Karar | Hangi paket | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

> Tablo, dinamik ölçüm/paket ailesinin karşılaştırmasıdır; hangi
> paketlerin K004'e gireceği kararı `UNKNOWN`'dur (§11).

| # | Son madde | Durum |
|---|---|---|
| 1 | §26 karşılaştırma tablosu | Tamam |
| 2 | §25 öz-kontrol | Tamam |
| 3 | ≥500 satır | Ölçüldü |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode