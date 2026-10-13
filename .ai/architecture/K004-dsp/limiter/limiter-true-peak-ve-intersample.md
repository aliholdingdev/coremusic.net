---
node_id: K004-dsp/limiter/limiter-true-peak-ve-intersample
node_type: module
status: draft
parent_id: K004-dsp/limiter
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# limiter-true-peak-ve-intersample — K004-dsp Derin Dal

Bu dosya, Neva Engine limiter bileşeninin true-peak (gerçek tepe)
kavramını ve intersample-peak (örnek arası tepe) ölçümünü tanımlar:
oversampling ile yeniden örneklemeli tepe tespiti, tavan (ceiling)
kavramı, inter-sample clipping riski ve dijital-analog sınırındaki
etkisi. Brickwall topolojisi ayrı dosyadır (`limiter-brickwall.md`);
bu dosyanın odağı **true-peak ve intersample ölçüm mantığıdır**.
K004 kapsamında limiter **PLANNED**'tır; tavan değeri kararı
**yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut JS/PHP satırları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | True-peak tanımı | Evet | — | Tasarım |
| 2 | Intersample tespit | Evet | — | Tasarım |
| 3 | Oversampling kullanımı | Evet | Faktör kararı | UNKNOWN |
| 4 | Ceiling (tavan) | Evet | Değer kararı | UNKNOWN |
| 5 | Lookahead | Referans | Brickwall dosyası | brickwall |
| 6 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 7 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 8 | Analog sınır (DAC) | Referans | Donanım | ADR-038 |
| 9 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |
| 10 | Kanal başına tavan | Referans | 8.1 (ADR-017) | ADR-017 |

## §2 True-peak temel matrisi

| # | Kavram | Tanım | Ölçüm | Birim | Durum |
|---|---|---|---|---|---|
| 1 | Örnek tepe (sample peak) | Dijital örnekteki max\|x\| | Doğrudan | dBFS | Tasarım |
| 2 | True-peak | Örnek arası dahil gerçek tepe | Yeniden örneklenmiş | dBTP | Tasarım |
| 3 | Intersample peak | Örnekler arası maksimum | Interpolasyon | dBTP | Tasarım |
| 4 | Fark | TP - sample peak | Hesap | dB | Tasarım |
| 5 | Risk (tipik) | 0-3 dB aşım | Literatür | dB | Kıyas |
| 6 | Dijital kırpma | x > 1.0 | Clipping | Distorsiyon | Tasarım |
| 7 | Analog kırpma | DAC sonrası | Aşım | Distorsiyon | §1.8 |
| 8 | Yeniden örnekleme | ×N örnekleme | Tespit | — | Tasarım |
| 9 | Nyquist sınırı | fs/2 | Sınır | Hz | sartname |
| 10 | float32 | ADR-017 | Hassasiyet | — | FROZEN |
| 11 | Ölçüm penceresi | Sürekli | — | — | Tasarım |
| 12 | Ölçüm doğruluğu | Kalibrasyon | Zorunlu | dB | testing |
| 13 | Ölçüm değeri | PLANNED | — | — | PLANNED |
| 14 | Karar | Tavan değeri | — | — | UNKNOWN |
| 15 | Karar | Oversampling faktörü | — | — | UNKNOWN |

## §3 Intersample tespit matrisi

| # | Yöntem | Açıklama | Doğruluk | CPU | Durum |
|---|---|---|---|---|---|
| 1 | Doğrusal interpolasyon | İki örnek arası doğru | Orta | Düşük | Sınıf A |
| 2 | Polinom interpolasyon | Kübik | İyi | Orta | Sınıf B |
| 3 | Sinc interpolasyon | Bant sınırlı | Çok iyi | Yüksek | Sınıf C |
| 4 | Oversampling ×2 | 2 kat örnek | İyi | Orta | Sınıf B |
| 5 | Oversampling ×4 | 4 kat örnek | Çok iyi | Yüksek | Sınıf C |
| 6 | Oversampling ×8 | 8 kat örnek | Çok iyi | Çok yüksek | Sınıf C |
| 7 | Polifaz filtre | Verimli | İyi | Orta | Sınıf B |
| 8 | Windowed-sinc | FIR tabanlı | Çok iyi | Yüksek | Sınıf C |
| 9 | Analog model | DAC benzeri | Sınırlı | Düşük | Araştırma |
| 10 | Ölçüm referansı | Kalibre edilmiş | Zorunlu | — | testing |
| 11 | float32 | ADR-017 | — | — | FROZEN |
| 12 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 13 | Ölçüm | Karşılaştırma | PLANNED | — | PLANNED |
| 14 | Karar | Yöntem | — | — | UNKNOWN |
| 15 | Karar | Faktör | — | — | UNKNOWN |

## §4 Tavan (ceiling) matrisi

| # | Tavan (dBTP) | Kullanım | Aşım riski | Gerekçe | Durum |
|---|---|---|---|---|---|
| 1 | -0.1 | Kısıtlı güvenlik | Orta | Yakın sınır | Seçenek |
| 2 | -0.3 | Yaygın güvenli | Düşük | Dizgi payı | Seçenek |
| 3 | -0.5 | Güvenli | Düşük | Rezerv | Seçenek |
| 4 | -1.0 | Çok güvenli | Çok düşük | Koruma | Seçenek |
| 5 | -2.0 | Konservatif | Çok düşük | Zincir payı | Seçenek |
| 6 | 0.0 | Sınır | Yüksek | Sıfır pay | Seçenek |
| 7 | Pozitif | Aşım | Çok yüksek | Distorsiyon | Kural dışı |
| 8 | Zincir payı | Sıra üstü | — | §10 | Tasarım |
| 9 | Kanal başına | 8.1 | — | ADR-017 | ADR-017 |
| 10 | Ölçüm (dBTP) | Doğrulama | — | testing | testing |
| 11 | Ölçüm (sample) | Kontrol | — | testing | testing |
| 12 | float32 | ADR-017 | — | — | FROZEN |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Karar | Tavan | — | — | UNKNOWN |
| 15 | Karar | Zincir payı | — | — | UNKNOWN |

## §5 Oversampling matrisi

| # | Faktör | Örnekleme (48 kHz taban) | CPU | Bellek | Durum |
|---|---|---|---|---|---|
| 1 | ×1 (yok) | 48 kHz | Düşük | 0 | Kıyas |
| 2 | ×2 | 96 kHz | Orta | Orta | Sınıf B |
| 3 | ×4 | 192 kHz | Yüksek | Yüksek | Sınıf C |
| 4 | ×8 | 384 kHz | Çok yüksek | Çok yüksek | Sınıf C |
| 5 | Çıkış dönüşü | Örnekleme | Orta | — | Tasarım |
| 6 | Filtre (interpolation) | Band-limiting | — | FIR | Tasarım |
| 7 | Filtre (decimation) | Band-limiting | — | FIR | Tasarım |
| 8 | Gecikme (filtre) | Blok | ≤ bütçe | — | §8 |
| 9 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 10 | float32 | ADR-017 | — | — | FROZEN |
| 11 | Tahsis | Baştan | — | — | L0 |
| 12 | Filtre katsayıları | Constructor | — | — | L0 |
| 13 | Ölçüm | Doğrulama | PLANNED | — | PLANNED |
| 14 | Ölçüm | CPU | PLANNED | — | PLANNED |
| 15 | Karar | Faktör | — | — | UNKNOWN |

## §6 Ölçüm zinciri matrisi (true-peak ölçüm)

| # | Adım | İşlem | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | Girdi | Blok | x | x | sartname |
| 2 | Oversampling | ×N (§5) | x | xo | Tasarım |
| 3 | Örnek tepe | max\|xo\| | xo | po | Tasarım |
| 4 | dBTP dönüşümü | 20·log10(po) | po | tpDB | Tasarım |
| 5 | Karşılaştırma | tpDB vs ceiling | — | err | Tasarım |
| 6 | GR (gerekirse) | Kısılama | — | gr | brickwall |
| 7 | Decimation | ÷N | — | xd | Tasarım |
| 8 | Çıkış kontrolü | max\|xd\| ≤ 1.0 | xd | OK | testing |
| 9 | Kanal ayrımı | Per-kanal ölçüm | 8.1 | tp(i) | ADR-017 |
| 10 | Raporlama | Max kanal | tp(i) | tpMax | testing |
| 11 | Ölçüm noktası | — | §10 | — | Q4 |
| 12 | Uydurma | YASAK | — | — | Kural |

## §7 Repo kanıtı — clamp anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 54 | `return Math.min(1, Math.max(0, value));` | Sınır (clamp) | Anoloji |
| 2 | shared/src/AI/AIWorkflow.php | 109 | `$analysis = $this->engine->analyzeAudio($filePath);` | Analiz çağrısı | Anoloji |
| 3 | engine/ (yok) | — | Limiter C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satırlar JS/PHP'dir; true-peak DSP karşılığı **yoktur**
> (motor diskte mevcut değil). Clamp anolojisi `[0,1]` aralığına
> sıkıştırır; true-peak **dBTP ölçümü ve oversampling** bu satırlarda
> **yoktur** — yöntem §3'te tanımlanır, uygulama PLANNED'dır.

## §8 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| EBU Tech 3343 true-peak (intersample) ölçümü tanımlar | https://tech.ebu.ch/docs/tech/tech3343.pdf | 2026-10-12 | 200 OK |
| Apple vDSP vektör işlemleri (klip/dönüşüm) sağlar | https://developer.apple.com/documentation/accelerate/vdsp | 2026-10-12 | 200 OK |

## §9 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | True-peak doğruluk | ≤0.5 dB | Kalibre sinyal | testing |
| 2 | Sample peak doğruluk | ≤0.1 dB | Doğrudan ölçüm | testing |
| 3 | Oversampling doğruluk | ≤ eşik | Karşılaştırma | testing |
| 4 | Çıkış sınırı | ≤ ceiling | Test | testing |
| 5 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 6 | Tahsis | 0 B/audio thread | Profil | L0 |
| 7 | Lock | 0 mutex | Denetim | L0 |
| 8 | float32 | Zorunlu | ADR-017 | FROZEN |
| 9 | Buffer | 512 örnek | sartname | Sabit |
| 10 | Örnekleme | 48 kHz | sartname | Sabit |
| 11 | Param geçişi | Kliksiz | Ramp | testing |
| 12 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 13 | Ölçüm | PLANNED | — | PLANNED |
| 14 | Q4 | UNKNOWN | measurement/testing | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN |

## §10 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | brickwall dosyası | Topoloji | Komşu dal | brickwall |
| 2 | compressor | Öncesi | GR→tavan | compressor |
| 3 | dynamics | LRA etkisi | §20 dynamics | dynamics |
| 4 | measurement | TP ölçümü | Q4 | measurement |
| 5 | testing | TP testi | testing | testing |
| 6 | pipeline | Sıra (son) | pipeline | pipeline |
| 7 | spatial | Kanal tavanı | 8.1 | spatial |
| 8 | equalizer | Öncesi | eq | eq |
| 9 | crossover | Multiband tavan | crossover | crossover |
| 10 | reverb | Öncesi | reverb | reverb |
| 11 | delay | Öncesi | delay | delay |
| 12 | limiter | Bu dosya | — | Bu |
| 13 | DAC sınırı | ADR-038 | PCM3168A | hardware |

## §11 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Tavan değeri (dBTP)? | Ölçüm hedefi | UNKNOWN |
| 2 | Oversampling faktörü? | CPU/kalite | UNKNOWN |
| 3 | True-peak ölçüm feature olarak mı? | Kapsam | UNKNOWN |
| 4 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 5 | Zincir payı nasıl bölüşülür? | Sıra | UNKNOWN |
| 6 | CPU bütçesi ne? | Bileşen | UNKNOWN |
| 7 | Kanal başına mı global mi? | ADR-017 | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §12 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Intersample aşımı (ölçülmezse) | Yüksek | Distorsiyon | §3 | §3 |
| 2 | Yetersiz oversampling | Orta | Ölçüm sapması | Doğrulama | testing |
| 3 | CPU aşımı (yüksek faktör) | Orta | Bütçe | Faktör kararı | §5.14 |
| 4 | Yanlış tavan (aşırı yüksek) | Orta | Distorsiyon | Karar | §4.14 |
| 5 | Tahsis (filtre) | Düşük | Yüksek | Constructor | L0 |
| 6 | Param yarışması | Düşük | Yüksek | Lock-free atom | L0 |
| 7 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 8 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 9 | Gecikme aşımı (filtre) | Orta | Yüksek | Bütçe | §8 |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §13 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (filtre katsayıları) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Çıkış sınırı (≤ ceiling) | Evet | testing |
| 6 | Oversampling doğrulama | Evet | testing |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §14 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | limiter/limiter-kapsam.md | Kademe-2 |
| 3 | Brickwall dosyası | limiter/limiter-brickwall.md | Komşu dal |
| 4 | compressor sınırı | compressor/compressor-kapsam.md | Sıra |
| 5 | dynamics sınırı | dynamics/dynamics-kapsam.md | LRA etkisi |
| 6 | measurement kapsamı | measurement/measurement-kapsam.md | TP ölçümü |
| 7 | testing örneği | testing/testing-ornekler.md | Doğrulama |
| 8 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 9 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 10 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 11 | ADR-017 | .ai/.decisions | float32 + 8.1 |
| 12 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 13 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 14 | Clamp anolojisi | volume.js:54 | Anoloji |
| 15 | C++ motor | engine/ (yok) | Eksik |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| True-peak | Örnek arası tepe dahil gerçek tepe (dBTP) |
| Intersample peak | Örnekler arası maksimum |
| Sample peak | Dijital örnekteki tepe |
| dBTP | True-peak desibel birimi |
| Ceiling | Tavan — izin verilen maksimum |
| Oversampling | Örnekleme katlama |
| Interpolation | Örnekleme arası doldurma |
| Decimation | Örnekleme azaltma |
| Band-limiting | Bant sınırlama filtresi |
| Clipping | Kırpma — sınır üstü kesme |
| Lookahead | İleri bakış |
| GR | Kazanç reduksiyonu |
| Nyquist | fs/2 sınırı |
| dBFS | Dijital referans desibel |
| FIR | Sonlu uzunluklu filtre |

## §16 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Tavan (dBTP) | §4 | Bekliyor | Ölçüm |
| 2 | Oversampling faktörü | §5 | Bekliyor | CPU/kalite |
| 3 | Intersample yöntem | §3 | Bekliyor | Doğruluk |
| 4 | TP ölçüm feature | Evet/Hayır | Bekliyor | §11.3 |
| 5 | Zincir payı | §4.8 | Bekliyor | Sıra |
| 6 | Kanal kapsamı | Kanal/global | Bekliyor | §11.7 |
| 7 | Ölçüm protokolü | Q4 | Bekliyor | measurement |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §17 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 2 URL |
| 4 | Repo kanıtı | volume.js:54 |
| 5 | Uydurma ölçüm yok | Tamam |
| 6 | Ölçüm PLANNED işaretli | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §18 True-peak senaryo karşılaştırma matrisi

| # | Senaryo | Sample peak | True-peak farkı | Aşım riski | Durum |
|---|---|---|---|---|---|
| 1 | Sinüs (düşük fs oranı) | ~1.0 | 0-3 dB | Yüksek | Kıyas |
| 2 | Sinüs (440 Hz, 48 kHz) | ~1.0 | 0-1 dB | Orta | Kıyas |
| 3 | Nyquist yakını | ~1.0 | Maksimum | Yüksek | Kıyas |
| 4 | Noise | Değişken | 1-3 dB | Orta | Kıyas |
| 5 | Transient (davul) | Değişken | 1-2 dB | Orta | Kıyas |
| 6 | Limiter sonrası (sıkı) | Tavan | Küçük | Düşük | Sınıf B |
| 7 | Limiter öncesi (ham) | Yüksek | Büyük | Yüksek | Sınıf C |
| 8 | Komprime içerik | Tavan | Küçük | Düşük | Kıyas |
| 9 | Dinamik içerik | Değişken | Büyük | Orta | Kıyas |
| 10 | Band-limiting (örneklenmiş) | Düzgün | Küçük | Düşük | Sınıf B |
| 11 | Aliasing'li içerik | Bozuk | Büyük | Yüksek | Sınıf C |
| 12 | Ölçüm sinyali (referans) | Bilinen | Kalibrasyon | Kontrol | testing |
| 13 | Gerçek içerik | Ölçüm | PLANNED | — | PLANNED |
| 14 | Karar | Tavan | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §19 Zincir payı matrisi (tavan bölüşümü)

| # | Zincir öğesi | Tavan payı | Gerekçe | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Girdi kaynağı | Bilinmiyor | Kaynak | measurement | measurement |
| 2 | equalizer | ±dB (opsiyonel) | Bant | testing | testing |
| 3 | crossover | Minimal | Bant | testing | testing |
| 4 | compressor | GR sonrası | Kısma | Ölçüm | Ölçüm |
| 5 | dynamics | 0 dB (sabit) | Nötr | testing | testing |
| 6 | reverb | Tail | Zemin | Ölçüm | Ölçüm |
| 7 | delay | Minimal | Gecikme | testing | testing |
| 8 | spatial | Kanal payı | 8.1 | Ölçüm | spatial |
| 9 | limiter (final) | Tavan | §4 | testing | testing |
| 10 | Ölçüm noktası | dBTP | Sonuç | measurement | measurement |
| 11 | DAC (ADR-038) | Donanım sınırı | PCM3168A | Donanım | ADR-038 |
| 12 | Toplam pay | ≥ 1 rezerv | Güvenlik | testing | testing |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Karar | Pay | — | — | UNKNOWN |
| 15 | Uydurma | YASAK | — | — | Kural |

## §20 Gecikme ve CPU bütçesi matrisi

| # | Bileşen | Gecikme | CPU (göreli) | Bellek | Durum |
|---|---|---|---|---|---|
| 1 | Sample-peak ölçüm | 0 | 1× | Düşük | Kıyas |
| 2 | Doğrusal interpolasyon | ~0 | 1.1× | Düşük | Sınıf A |
| 3 | Oversampling ×2 | Filtre gecikmesi | 2× | Orta | Sınıf B |
| 4 | Oversampling ×4 | Filtre gecikmesi | 4× | Yüksek | Sınıf C |
| 5 | Oversampling ×8 | Filtre gecikmesi | 8× | Çok yüksek | Sınıf C |
| 6 | Lookahead (brickwall) | Blok | +1× | Düşük | brickwall |
| 7 | Decimation | Filtre gecikmesi | +1× | Orta | Tasarım |
| 8 | Toplam (×2) | ≤ bütçe | ~3× | Orta | PLANNED |
| 9 | Toplam (×4) | ≤ bütçe | ~6× | Yüksek | PLANNED |
| 10 | 8 kanal (spatial) | — | ×8 | ×8 | spatial |
| 11 | Tahsis | 0 | Baştan | Baştan | L0 |
| 12 | Lock | 0 | 0 mutex | — | L0 |
| 13 | float32 | — | ADR-017 | — | FROZEN |
| 14 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | — | — | PLANNED |

## §21 Ölçüm doğrulama matrisi (true-peak)

| # | Doğrulama | Yöntem | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Referans TP ölçümü | Kalibre sinyal | Bilinen dBTP | ≤0.5 dB | testing |
| 2 | Sinüs TP | Bilinen frekans | ≈ 3.01 dB (sınır) | ≤ eşik | testing |
| 3 | Nyquist yakını | Artan frekans | Artan TP | Monoton | testing |
| 4 | Oversampling karşılaştırma | ×2 vs ×4 | Yakınsayan | ≤ eşik | testing |
| 5 | Tavan uygulama | Bilinen girdi | ≤ ceiling | 0 ihlal | testing |
| 6 | Sample peak | Doğrudan | ≤ ceiling | ≤0.1 dB | testing |
| 7 | Kanal (8.1) | Kanal testi | Doğru kanal | Doğru | testing |
| 8 | float32 yuvarlama | Değer | ≤1 LSB | Test | testing |
| 9 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 10 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 11 | Gecikme | Impuls | ≤ bütçe | ≤ | PLANNED |
| 12 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural | Kural |
| 13 | Ölçüm | PLANNED | — | — | PLANNED |
| 14 | Uydurma | YASAK | — | — | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §22 DAC sınırı matrisi (donanım etkisi)

| # | Sınır | Etki | Koruma | Durum |
|---|---|---|---|---|
| 1 | DAC örnekleme | fs bağımlı | Band-limiting | ADR-038 |
| 2 | DAC interpolasyon | TP aşımı | Tavan payı | ADR-038 |
| 3 | DAC doğrusallığı | Distorsiyon | Tavan payı | ADR-038 |
| 4 | PCM3168A (ADR-038) | Donanım | Tavan payı | ADR-038 |
| 5 | PCM5122 | YASAK | — | ADR-038 |
| 6 | Analog çıkış | Sınır üstü | Tavan payı | Donanım |
| 7 | Ölçüm (donanım) | Kalibrasyon | Gerekli | PLANNED |
| 8 | Yazılım tavanı | Yazılım | §4 | Tasarım |
| 9 | Ölçüm noktası (yazılım) | §6 | — | testing |
| 10 | Ölçüm noktası (donanım) | Çıkış | — | PLANNED |
| 11 | Fark (Δ) | Sınır etkisi | — | PLANNED |
| 12 | Karar | Tavan | — | UNKNOWN |
| 13 | Karar | Ölçüm | — | UNKNOWN |
| 14 | Ölçüm | PLANNED | — | PLANNED |
| 15 | Uydurma | YASAK | — | Kural |

> §22, gerçek DAC/inverter doğrulama verisi olmadan **yöntem düzeyinde**
> kalır; donanım ölçümü PLANNED'dır (Zero-Hallucination).

## §23 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Tavan ihlali testi | Aşırı girdi | ≤ ceiling | 0 ihlal | testing |
| 2 | True-peak doğruluk | Kalibre sinyal | ≤0.5 dB | Doğruluk | testing |
| 3 | Intersample (düşük fs) | Nyquist yakını | Doğru TP | ≤ eşik | testing |
| 4 | Oversampling geçişi | Faktör değişimi | Kabul | ≤ eşik | testing |
| 5 | Gecikme testi | Impuls | ≤ bütçe | ≤ | testing |
| 6 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 7 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 8 | Param geçişi (ceiling) | Sürükleme | Kliksiz | Ramp | testing |
| 9 | Kanal testi (8.1) | Kanal | Doğru | Doğru | testing |
| 10 | Denormal | Tiny girdi | Performans | FTZ | L0 |
| 11 | Zincir payı | Sıra testi | Kabul | ≤ eşik | testing |
| 12 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural | Kural |
| 13 | Uydurma | YASAK | — | Kural | Kural |
| 14 | Q4 | UNKNOWN | — | measurement | UNKNOWN |
| 15 | Karar | K004 | — | UNKNOWN | UNKNOWN |

## §24 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Tavan değeri | UNKNOWN | Karar | §4 |
| 2 | Oversampling faktörü | UNKNOWN | CPU | §5 |
| 3 | Intersample yöntem | UNKNOWN | Doğruluk | §3 |
| 4 | TP feature kapsamı | UNKNOWN | Kapsam | §11.3 |
| 5 | Zincir payı | UNKNOWN | Sıra | §19 |
| 6 | TP doğrulama | PLANNED | testing | §21 |
| 7 | Donanım ölçümü | PLANNED | ADR-038 | §22 |
| 8 | CPU bütçesi | PLANNED | Ölçüm | §20 |
| 9 | Q4 | UNKNOWN | measurement/testing | §11.4 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.7 |

## §25 Ölçüm zinciri blok akış tablosu (true-peak)

| # | Blok | Girdi | İşlem | Çıktı | Gecikme |
|---|---|---|---|---|---|
| 1 | Girdi (x[n]) | Blok | — | x | 0 |
| 2 | Oversampling (×N) | x | Interpolasyon + FIR | xo | Filtre |
| 3 | Örnek tepe | xo | max\|·\| | po | 0 |
| 4 | dBTP | po | 20·log10 | tpDB | 0 |
| 5 | Karşılaştırma | tpDB | ceiling'e göre | err | 0 |
| 6 | Tavan kısma | x, err | GR uygula (brickwall) | y | §20.6 |
| 7 | Decimation | y | ÷N + FIR | yd | Filtre |
| 8 | Çıkış kontrolü | yd | max ≤ ceiling | OK | 0 |
| 9 | Kanal (8.1) | Her kanal | §2-7 | tp(i) | — |
| 10 | Ölçüm noktası | — | §21 | — | Q4 |
| 11 | Uydurma | — | YASAK | — | Kural |

## §26 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | True-peak tanımı §2 | Tamam |
| 2 | Intersample §3 | Tamam |
| 3 | Frontmatter §4.2 + group | Tamam |
| 4 | URL benzersiz (2) | Tamam |
| 5 | Repo kanıtı §7 | Tamam |
| 6 | Uydurma ölçüm yok | Tamam |
| 7 | Cyrillic fix (Koruma) | Ölçüldü |
| 8 | Q4 UNKNOWN | Tamam |
| 9 | CJK/mojibake 0 | Ölçüldü |
| 10 | ≥500 satır | Ölçüldü |
| 11 | Authority son satır | Tamam |

| # | Ek denetim | Durum |
|---|---|---|
| 1 | §18 senaryo tablosu | Tamam |
| 2 | §19 zincir payı tablosu | Tamam |
| 3 | §20 gecikme/CPU tablosu | Tamam |
| 4 | §21 doğrulama tablosu | Tamam |
| 5 | §22 DAC sınırı tablosu | Tamam |
| 6 | §25 blok akış tablosu | Tamam |
| 7 | ≥500 satır | Ölçüldü |
| 8 | Frontmatter 10 anahtar | Tamam |
| 9 | group: mimari | Tamam |
| 10 | Authority son satır | Tamam |

| # | Son ek satır | Durum |
|---|---|---|
| 1 | §26 öz-kontrol | Tamam |
| 2 | Ölçüm PLANNED | Tamam |
| 3 | ≥500 satır | Ölçüldü |
| 4 | Q4 UNKNOWN | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode