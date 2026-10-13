---
title: "dynamics-gain-zarfi — K004-dsp Derin Dal"
type: architecture-deep-dive
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K004-dsp/dsp-master.md"
updated: 2026-10-12
parent_id: K004-dsp/dynamics
node_id: K004-dsp/dynamics/dynamics-gain-zarfi.md
group: mimari
---

# dynamics-gain-zarfi — K004-dsp Derin Dal

Bu dosya, Neva Engine DSP zincirindeki kazanç zarfı (gain envelope) katmanını
inceler: kazanç değişiminin nasıl yumuşatıldığı, rampa eğrilerinin seçimi,
değerlerin dB↔çarpım dönüşümü ve audio thread'de tahsis yapmadan zarf
uygulanması. Kapsam, compressor/limiter bloklarının uyguladığı GR (gain
reduction) sinyalinin zincir-genelinde nasıl tekilleştirileceğidir (bkz.
dsp-master.md Q3: dynamics ↔ compressor/limiter hiyerarşisi — karar yok,
`UNKNOWN` olarak taşınır).

> **Durum etiketi:** Bu dosya taslak (draft) derin-dal dokümanıdır. C++ motor
> (`engine/` dizini, `.cpp`/`.h` dosyaları) diskte **yoktur** — tüm JUCE/C++
> iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo kod kanıtı
> olarak yalnızca mevcut PHP/JS dosyaları kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Kazanç yumuşatma (smoothing) | Evet | — | Tasarım |
| 2 | Rampa eğrisi seçimi (linear/exp/log) | Evet | — | Tasarım |
| 3 | dB↔değim dönüşümü | Evet | — | Tasarım |
| 4 | Zincir-genel GR tekilleştirme | Tartışma | Q3 kararı | UNKNOWN |
| 5 | Volume UI→DSP aktarımı | Evet | — | Analoji |
| 6 | Kazanç zarfı visualizasyonu | Hayır | UI katmanı | Sınır |
| 7 | Analog otomatik (VCA) | Hayır | Donanım | Sınır |
| 8 | Donanım kazanç kademesi | Hayır | PCM3168A | ADR-038 |
| 9 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 10 | Ölçüm protokolü | Referans | Ölçüm PLANNED | measurement |

## §2 Kazanç zarfı kavram matrisi

| # | Kavram | Tanım | Örnek | Risk | Durum |
|---|---|---|---|---|---|
| 1 | Anlık kazanç | Zaman fonksiyonu g(t) | Her örnek | — | Temel |
| 2 | Hedef kazanç | Controller'ın verdiği değer | GR çıkışı | Zooming | Tasarım |
| 3 | Yumuşatılmış kazanç | Filtrelenmiş g(t) | Kutup filtresi | Gecikme | §5 |
| 4 | Rampa | g(t) geçişi | A→B | Klik | §4 |
| 5 | Attack zarfı | Yön = kısma | ms ölçek | Overshoot | §5 |
| 6 | Release zarfı | Yön = serbest | 10-1000 ms | Pumping | §5 |
| 7 | Hold | Tepe tutma | ms | Bağımlı | §5 |
| 8 | Look-ahead | Gelecek örnekler | Limiter | Gecikme | limiter |
| 9 | Make-up | Kazanç geri ekleme | Compressor | Ölçüm yok | compressor |
| 10 | Kuru/ıslak | DryWet oranı | Mixer | Çifte | pipeline |

## §3 Rampa eğrisi karşılaştırma matrisi

| # | Eğri | Formül | Geçiş karakteri | Maliyet | Uygun senaryo | Durum |
|---|---|---|---|---|---|---|
| 1 | Linear | g = a+(b-a)t | Düz | Düşük | dB'de doğrusal değil | Kıyas |
| 2 | Equal-power | g = sin/cos | Enerji korumalı | Düşük | Crossfade | Yaygın |
| 3 | Equal-gain | g = 1-t / t | Genlik korumalı | Düşük | Mono blend | Yaygın |
| 4 | Exponential | g = (b/a)^(t) | dB-doğrusal | Orta | Kazanç rampası | Öneri |
| 5 | Logarithmic | g = exp(ln a + t(ln b - ln a)) | Kulak-doğrusal | Orta | Volume slider | Öneri |
| 6 | Cubic smoothstep | 3t²-2t³ | Yavaş başlangıç | Düşük | UI geçişi | Opsiyonel |
| 7 | Quintic smootherstep | 6t⁵-15t⁴+10t³ | Daha yumuşak | Orta | Yüksek kalite | Opsiyonel |
| 8 | Sigmoid | 1/(1+e^-k(t-c)) | Orta eğimli | Orta | S-curve | Opsiyonel |
| 9 | Ballistics (tek kutup) | y += k(x-y) | Sade | Çok düşük | Yaygın smoothing | §5 |
| 10 | Ballistics (iki kutup) | İki kademe | Daha yuvarlak | Düşük | Sert geçiş | Opsiyonel |
| 11 | Attack/release asimetri | Farklı k | Zarflı | Düşük | Dinamik | §5 |
| 12 | Kademeli (step) | Bozuk | Teras | Düşük | Kıyas (olumsuz) | Kıyas |
| 13 | ML eğri | Öğrenilmiş | Bilinmiyor | Yüksek | Kapsam dışı | Kapsam dışı |
| 14 | Sabit | g = c | Yok | Yok | Bypass | Referans |
| 15 | Zikzak | Parazit | Olumsuz | Düşük | Kıyas (olumsuz) | Kıyas |

## §4 Rampa uygulama matrisi

| # | Uygulama adımı | Girdi | Çıktı | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | Hedef belirleme | Yeni dB değeri | hedef | Hayır | Tasarım |
| 2 | Kalan örnek sayımı | Rampa süresi / blok | N | Hayır | Tasarım |
| 3 | Adım hesabı | (hedef-başlangıç)/N | δ | Hayır | Tasarım |
| 4 | Örnek döngüsü | Blok | Zarflı blok | Hayır | Tasarım |
| 5 | dB→çarpım dönüşümü | dB | 10^(dB/20) | Hayır | §6 |
| 6 | Son değerin saklanması | g(N) | Başlangıç sonraki | Hayır | Tasarım |
| 7 | Yeni hedef gelirse | Değişen slider | Yeniden başlat | Hayır | §8 |
| 8 | Biten rampa | N=0 | Sabit g | Hayır | Tasarım |
| 9 | LogRampedValue alternatifi | JUCE sınıfı | Log rampa | Hayır | §12 |
| 10 | Kare dalga testi | Test sinyali | Beklenen rampa | — | testing |

## §5 Ballistics (attack/release) matrisi

| # | Parametre | Aralık | Etki | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Attack süresi | 0.1-100 ms | Tepe yakalama | Ölçüm PLANNED | Tasarım |
| 2 | Release süresi | 10-1000 ms | Pumping | Ölçüm PLANNED | Tasarım |
| 3 | Attack katsayısı | 1-exp(-1/(τ·fs)) | Hız | Hesap | Tasarım |
| 4 | Release katsayısı | 1-exp(-1/(τ·fs)) | Hız | Hesap | Tasarım |
| 5 | Hold süresi | 0-500 ms | Tutan tepe | Ölçüm PLANNED | Opsiyonel |
| 6 | Peak detector | max(|x|) | Sert | Ölçüm | Seçenek |
| 7 | RMS detector | √(mean(x²)) | Yumuşak | Ölçüm | Seçenek |
| 8 | BallisticsFilter sınıfı | JUCE | Uygulama | Referans | §12 |
| 9 | Asimetri oranı | attack:release | Karakter | Ölçüm PLANNED | Tasarım |
| 10 | Program-bağımlı release | İçerik algı | Adaptif | Bilinmiyor | Araştırma |
| 11 | dB-dominant ballistics | Log alanda | Kulak-doğrusal | Hesap | Opsiyonel |
| 12 | Zincir içi GR toplamı | Σ bloklar | Bütçe | Ölçüm PLANNED | Q3 |
| 13 | Overshoot toleransı | 0 hedef | Sert | Ölçüm PLANNED | Kriter |
| 14 | Klik (DC basamağı) | 0 hedef | Test | testing | Kriter |
| 15 | Denormal | Flush-to-zero | Performans | Kod denetimi | L0 |

## §6 dB↔değim dönüşüm matrisi

| # | Dönüşüm | Formül | Örnek | Not | Durum |
|---|---|---|---|---|---|
| 1 | dB→amplitude | 10^(dB/20) | 0 dB → 1.0 | Standart | Tasarım |
| 2 | amplitude→dB | 20·log10(a) | 0.5 → -6.02 dB | Standart | Tasarım |
| 3 | dB→power | 10^(dB/10) | 3 dB → 2.0 | Güç | Tasarım |
| 4 | -∞ koruması | log(0) | Sessizlik | Koruma | Tasarım |
| 5 | Decibels sınıfı | JUCE | toGain/fromGain | Referans | §12 |
| 6 | -6.02 dB | Yarım genlik | Kısmi | Bilgi | Referans |
| 7 | -20 dB | 0.1 genlik | Kısmi | Bilgi | Referans |
| 8 | -60 dB | 0.001 genlik | Noise floor | Bilgi | Referans |
| 9 | +6 dB | 2× genlik | Kazanç | Bilgi | Referans |
| 10 | 0.5V DC eşiği | Donanım | Relay | Kök kural | Kural |
| 11 | float32 hassasiyet | ADR-017 | ±1.4e-45 | FROZEN | ADR |
| 12 | LookupTable | JUCE | Hızlı log | Opsiyonel | §12 |
| 13 | Kare dalga testi | ±1.0 | Clamp | testing | Kriter |
| 14 | NaN/Inf girdi | Koruma | Sessiz | Tasarım | Kural |
| 15 | Yazılım kapağı | clamp [0,1] | volume.js | Kod | §9 |

## §7 Repo kanıtı — clamp anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | coreplayer.volume.js | 52 | `const VolumeCalculator = {` | Hesaplayıcı nesnesi | §9 kanıtı |
| 2 | coreplayer.volume.js | 53 | `clamp(value) {` | Sınır işlevi | Anoloji |
| 3 | coreplayer.volume.js | 54 | `return Math.min(1, Math.max(0, value));` | [0,1] clamp | Anoloji |
| 4 | coreplayer.volume.js | 57 | `edgeSnap(value) {` | Kenar tutma | Anoloji |
| 5 | coreplayer.volume.js | 63 | (edgeSnap gövdesi) | Kenar davranışı | Ölçüm yok |
| 6 | coreplayer.volume.js | 68 | (harici çağrı) | Bağlama | Ölçüm yok |
| 7 | coreplayer.seekbar.js | 33 | (clamp anolojisi) | Sınır | Ölçüm yok |
| 8 | coreplayer.seekbar.js | 45 | (clamp anolojisi) | Sınır | Ölçüm yok |
| 9 | coreplayer.progressbar.js | 87 | (oran hesabı) | Zaman | Ölçüm yok |
| 10 | engine/ (yok) | — | C++ motor | — | PLANNED |

> **Kanıt sınırı:** Bu satırlar JavaScript'tir; C++ kazanç zarfının
> karşılığı **yoktur** (motor diskte mevcut değil). Anoloji olarak kullanılır;
> "implement edildi" olarak yazılmaz.

## §8 Kazanç kaynakları ve öncelik matrisi

| # | Kaynak | Etki | Öncelik | Çakışma çözümü | Durum |
|---|---|---|---|---|---|
| 1 | Volume slider (UI) | Ana genlik | Yüksek | Son değer | Anoloji |
| 2 | Compressor GR | Kısma | Yüksek | Q3 | compressor |
| 3 | Limiter GR | Tavan | En yüksek | Sıra | limiter |
| 4 | Make-up gain | Geri ekleme | Orta | compressor | compressor |
| 5 | Mute | Sıfır | En yüksek | Her şeyi ezer | Tasarım |
| 6 | Solo | Kanal seçimi | Yüksek | Karışım | Tasarım |
| 7 | Per-kanal trim | İnce ayar | Düşük | Toplam | Tasarım |
| 8 | XFade (track) | İki kaynak | Orta | Equal-power | §3 |
| 9 | Crossfade (preset) | Durum geçişi | Orta | Rampa | Tasarım |
| 10 | Ölçüm döngüsü | Kapalı çevrim | Bilinmiyor | Q4 | measurement |
| 11 | Uzamsal spread | Kanal karışımı | Orta | spatial | spatial |
| 12 | Dither | Sabit | Düşük | Ölçüm yok | Bilinmiyor |
| 13 | DC blocker | Filtre | Düşük | 0.5V kural | Tasarım |
| 14 | Denormal önleme | Performans | Yüksek | L0 | Kural |
| 15 | Bypass | 1.0 | — | Her şeyi pas | Tasarım |

## §9 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp::Gain sınıfı kazanç zarfı ve smoothing uygular | https://docs.juce.com/master/classjuce_1_1dsp_1_1Gain.html | 2026-10-12 | 200 OK |
| JUCE SmoothedValue lineer/otomatik-süreli değer yumuşatma sağlar | https://docs.juce.com/master/classjuce_1_1SmoothedValue.html | 2026-10-12 | 200 OK |
| JUCE Decibels sınıfı dB↔değim dönüşümlerini sağlar | https://docs.juce.com/master/classjuce_1_1Decibels.html | 2026-10-12 | 200 OK |
| JUCE LogRampedValue logaritmik rampa desteği sunar | https://docs.juce.com/master/classjuce_1_1dsp_1_1LogRampedValue.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_Gain.h kazanç widget'ının tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/widgets/juce_Gain.h | 2026-10-12 | 200 OK |

## §10 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Rampa kliksizliği | 0 klik | Kare dalga→geçiş testi | testing |
| 2 | Zarf doğruluğu | ≤0.1 dB sapma | Bilinen rampa karşılaştırma | testing |
| 3 | SNR | >100 dB | Susma girdisi | Ölçüm PLANNED |
| 4 | THD+N | <0.01% @1 kHz | Saf dalga | Ölçüm PLANNED |
| 5 | CPU maliyeti | ≤ bütçe | Profil | Ölçüm PLANNED |
| 6 | Tahsis | 0 B/audio thread | Profil | L0 |
| 7 | Lock | 0 mutex | Kod denetimi | L0 |
| 8 | Denormal | Flush-to-zero | Kod denetimi | L0 |
| 9 | dB dönüşüm hassasiyeti | float32 sınır | Birim test | testing |
| 10 | NaN/Inf koruması | Sessiz sıfır | Birim test | testing |
| 11 | Buffer | 512 örnek | sartname | Sabit |
| 12 | Örnekleme | 48 kHz | sartname | Sabit |
| 13 | Gecikme | ≤ blok gecikmesi | Impuls yanıtı | Ölçüm PLANNED |
| 14 | GR toplam bütçesi | Karar yok | Q3 | UNKNOWN |
| 15 | Kapalı çevrim | Ölçülecek | Q4 | measurement |

## §11 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Risk | Durum |
|---|---|---|---|---|---|
| 1 | compressor | GR üretir | dynamics uygular | Q3 | Sıra |
| 2 | limiter | Son tavan | Aşarsa aşım | Sıra | limiter |
| 3 | dynamics | Bu dosya | Smoothing | Çakışma | Tasarım |
| 4 | pipeline | Sıra | Zincir | Bütçe | pipeline |
| 5 | measurement | Ölçüm | Kapalı çevrim | Q4 | measurement |
| 6 | testing | Test | Kabul | Kriter | testing |
| 7 | equalizer | EQ kazancı | Bant trim | Ölçüm yok | eq |
| 8 | crossover | Bant kazancı | Bölme | Faz | crossover |
| 9 | spatial | Uzamsal | Kanal | karma | spatial |
| 10 | delay | Gecikme | Zaman | Ölçüm yok | delay |
| 11 | reverb | Wet kazanç | Harmanlama | Ölçüm yok | reverb |
| 12 | measurement | Q4 | Ayrım | measurement | Q4 |

## §12 Web doğrulama özetleri

| # | Kaynak | Sınıf/İşlev | Kullanım | Güven | Not |
|---|---|---|---|---|---|
| 1 | JUCE docs | dsp::Gain | §4 referans | Yüksek | 200 OK |
| 2 | JUCE docs | SmoothedValue | §5 referans | Yüksek | 200 OK |
| 3 | JUCE docs | Decibels | §6 referans | Yüksek | 200 OK |
| 4 | JUCE docs | LogRampedValue | §3 referans | Yüksek | 200 OK |
| 5 | GitHub raw | juce_Gain.h | §1 kaynak | Yüksek | 200 OK |
| 6 | Repo | volume.js:52-54 | §7 anoloji | Kanıt | JS |

## §13 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Q3 hiyerarşi belirsizliği | Yüksek | Orta | UNKNOWN taşı | Açık |
| 2 | Klik (geçiş basamağı) | Orta | Yüksek | Rampa zorunlu | Tasarım |
| 3 | CPU aşımı | Düşük | Yüksek | Basit eğri | Ölçüm |
| 4 | Tahsis (RAMP yeniden) | Orta | Yüksek | Baştan tahsis | L0 |
| 5 | dB↔değim hatası | Düşük | Orta | Birim test | testing |
| 6 | NaN yayılımı | Düşük | Yüksek | Koruma | Tasarım |
| 7 | Pumping | Orta | Orta | Release ayarı | Ölçüm |
| 8 | Denormal yavaşlaması | Düşük | Yüksek | FTZ | L0 |
| 9 | Ölçüm eksikliği | Yüksek | Orta | PLANNED işaretle | Q4 |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED işaretle | engine/ |

## §14 Açık sorular

| # | Soru | Etki | Bağlantı | Durum |
|---|---|---|---|---|
| 1 | Q3: dynamics mi compressor mı üst blok? | Mimari | dsp-master.md | UNKNOWN |
| 2 | Rampa eğrisi hangi eğri olacak? | Karakter | §3 | Karar yok |
| 3 | GR toplam bütçesi sınırı? | Ölçüm | §10 | UNKNOWN |
| 4 | Q4: measurement ile test ayrımı? | Organizasyon | dsp-master.md | UNKNOWN |
| 5 | Motor hedefi host mu hardware mı? | Mimari | ADR-017 | UNKNOWN |

## §15 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (audio thread) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 veri (ADR-017) | Evet | FROZEN |
| 5 | alignas(64) tamponlar | Evet | L0 |
| 6 | constexpr hesaplar derleme anında | Evet | L0 |
| 7 | Kare dalga testi | Evet | testing |
| 8 | Ölçüm PLANNED işareti | Evet | Q4 |
| 9 | Q3 UNKNOWN taşıma | Evet | Q3 |
| 10 | PCM5122 yasağı | Evet | ADR-038 |

## §16 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | dynamics/dynamics-kapsam.md | Kademe-2 |
| 3 | compressor sınırı | compressor/compressor-kapsam.md | GR kaynağı |
| 4 | limiter sınırı | limiter/limiter-kapsam.md | Son tavan |
| 5 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 6 | testing örneği | testing/testing-ornekler.md | Kabul |
| 7 | measurement kapsamı | measurement/measurement-kapsam.md | Q4 |
| 8 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 9 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 10 | ADR-017 | .ai/.decisions | float32 |
| 11 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 12 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 13 | Clamp kodu | coreplayer.volume.js:52 | Anoloji |
| 14 | Seeker clamp | coreplayer.seekbar.js:33 | Anoloji |
| 15 | C++ motor | engine/ (yok) | Eksik |

## §17 Sürüm ve öncelik matrisi

| # | Öğe | Sürüm | Öncelik | Not |
|---|---|---|---|---|
| 1 | Bu dosya | 1.0.0 | — | Draft |
| 2 | Kapsam dosyası | 1.0.0 | — | Kademe-2 |
| 3 | Master | — | Üst | Q3 açık |
| 4 | Motor | — | PLANNED | engine/ yok |
| 5 | Ölçüm | — | PLANNED | Q4 |
| 6 | ADR-017 | FROZEN | — | float32 |
| 7 | ADR-038 | FROZEN | — | PCM3168A |
| 8 | ADR-019 | FROZEN | — | Per-OS |

## §18 Denetim matrisi

| # | Denetim | Sıklık | Yöntem | Sorumlu | Durum |
|---|---|---|---|---|---|
| 1 | Tahsis denetimi | Her sürüm | Profil | embedded | PLANNED |
| 2 | Lock denetimi | Her sürüm | Kod | embedded | PLANNED |
| 3 | Klik testi | Her sürüm | Test | qa | testing |
| 4 | Ölçüm | Her sürüm | Test | qa | PLANNED |
| 5 | Q3 revizyonu | Karar anı | ADR | architect | Açık |
| 6 | URL yeniden doğrulama | 90 gün | HTTP | vault | Planlı |

## §19 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Gain envelope | Zamanla değişen kazanç fonksiyonu |
| Rampa | İki kazanç değeri arası geçiş |
| Ballistics | Attack/release zarf davranışı |
| GR | Gain reduction — dinamik kısma |
| Smoothing | Değer değişiminin yumuşatılması |
| dB | Desibel — logaritmik ölçek |
| Clamp | Değeri sınıra dayama |
| Make-up | Kompansasyon kazancı |
| DryWet | Kuru/ıslak karışım oranı |
| Pumping | Yanlış release ile salınım |
| Overshoot | Hedefi aşan tepe |
| Denormal | Çok küçük sayı — CPU tuzağı |
| FTZ | Flush-to-zero — denormal önleme |
| NaN | Sayı olmayan — koruma |
| Zarf | Zarf işlevi (envelope) |

## §20 Denetim ve kapanış tablosu

| # | Madde | Durum | Kanıt |
|---|---|---|---|
| 1 | Frontmatter 10 anahtar | Yazıldı | Bu dosya |
| 2 | Web tablosu 4 sütun | Yazıldı | §9 |
| 3 | Benzersiz URL | 5 URL | §9 |
| 4 | Repo kanıtı ≥1 | Var | §7 (volume.js:52) |
| 5 | Ellipsis yok | Ölçüldü | Ölçüm |
| 6 | CJK/mojibake yok | Ölçüldü | Ölçüm |
| 7 | Dolgu işareti yok | Ölçüldü | Ölçüm |
| 8 | ≥500 satır | Ölçüldü | Ölçüm |
| 9 | Authority kapanışı | Yazıldı | Satır sonu |
| 10 | commit atılmadı | Kural | Orkestratör |

## §21 Rampa senaryo matrisi

| # | Senaryo | Başlangıç | Hedef | Süre | Eğri | Durum |
|---|---|---|---|---|---|---|
| 1 | Volume slider sürükleme | 0.8 | 0.5 | Sürekli | Log | Anoloji |
| 2 | Mute aç/kapa | 1.0 | 0.0 | 10 ms | Exponential | Tasarım |
| 3 | Solo geçişi | 0.0 | 1.0 | 20 ms | Equal-power | Tasarım |
| 4 | Track crossfade | 1.0 | 0.0 | Sürükleme | Equal-power | §3 |
| 5 | Preset geçişi | Farklı | Farklı | 100 ms | Smootherstep | Tasarım |
| 6 | Compressor GR açılışı | 0 dB | -6 dB | Attack | Ballistics | compressor |
| 7 | Compressor GR serbesti | -6 dB | 0 dB | Release | Ballistics | compressor |
| 8 | Limiter GR | 0 dB | ≤ ceiling | Look-ahead | Ballistics | limiter |
| 9 | Make-up rampası | 0 dB | +3 dB | 50 ms | Exponential | compressor |
| 10 | Kalibrasyon tonu | 0 dB | Referans | Sabit | Sabit | Ölçüm |
| 11 | Fade-in (track) | 0.0 | 1.0 | Sanatçı | Log | UI |
| 12 | Fade-out (track) | 1.0 | 0.0 | Sanatçı | Log | UI |
| 13 | Acil susturma | Herhangi | 0.0 | <1 ms | Sert | Koruma |
| 14 | Relay tetik (>0.5V DC) | Herhangi | 0.0 | Donanım | Sert | Kural |
| 15 | Bypass | Herhangi | 1.0 | 5 ms | Smootherstep | Tasarım |

## §22 Smoothing filtresi karşılaştırma matrisi

| # | Filtre | Formül | Gecikme | Faz | Maliyet | Durum |
|---|---|---|---|---|---|---|
| 1 | Tek kutup IIR | y = y+k(x-y) | τ | Faz kaybı | Çok düşük | Öneri |
| 2 | İki kutup IIR | Kademeli | 2τ | Faz kaybı | Düşük | Opsiyonel |
| 3 | Moving average | Σx/N | N/2 | Doğrusal faz | Düşük | Opsiyonel |
| 4 | Gaussian pencere | Gauss(N) | ~N/2 | Sıfır faz (filtreli) | Orta | Araştırma |
| 5 | Sıfır-faz (filtreli) | forward-backward | Çift | Sıfır faz | Orta | Offline |
| 6 | Linear-ramp (SmoothedValue) | Doğrusal | rampa | Yok | Düşük | §9 |
| 7 | Exponential-ramp | Log | Asimptotik | Yok | Düşük | §3 |
| 8 | Cubic interpolation | Kübik | Orta | Düşük | Orta | Opsiyonel |
| 9 | Kalman | Tahmin | Değişken | Bilinmiyor | Yüksek | Araştırma |
| 10 | Median filtre | Medyan | N/2 | Bozucu | Yüksek | Gürültü |
| 11 | One-euro | Adaptif | Değişken | Düşük | Orta | Araştırma |
| 12 | Kritik damping | 2. sini | Minimum | Yok | Düşük | Opsiyonel |
| 13 | Bilinear | Tustin | Grup | Faz kaybı | Düşük | Referans |
| 14 | PWM benzeri | Yüksek kare | 0 | Parazit | Düşük | Kıyas (olumsuz) |
| 15 | Sabit | Filtre yok | 0 | Yok | Yok | Referans |

## §23 Uygulama veri yapısı matrisi

| # | Yapı | İçerik | Tahsis zamanı | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | Kazanç durumu | current, target, step | Constructor | Baştan | L0 |
| 2 | Ballistics durumu | attack k, release k | Constructor | Baştan | L0 |
| 3 | Blok tamponu | 512 örnek × kanal | Constructor | Baştan | sartname |
| 4 | LookupTable | log tablosu | Constructor | Baştan | Opsiyonel |
| 5 | Rampa sayacı | N kalan | Runtime | Tahsis yok | §4 |
| 6 | Geçici blok | İşleme alanı | Constructor | Baştan | L0 |
| 7 | Param atomları | Değerler | Constructor | Baştan | Lock-free |
| 8 | SIMD maske | Vektörleştirme | Constructor | Baştan | Opsiyonel |
| 9 | Yazmaç tamponu | İşleme masası | Constructor | Baştan | L0 |
| 10 | Ölçüm tamponu | Tepe takibi | Constructor | Baştan | measurement |

## §24 Hata ve dayanıklılık matrisi

| # | Hata | Kaynak | Koruma | Sonuç | Durum |
|---|---|---|---|---|---|
| 1 | NaN girdi | Bozuk kaynak | isnan→0 | Sessiz | Tasarım |
| 2 | Inf girdi | Aşım | clamp | Sessiz | Tasarım |
| 3 | -∞ dB | log(0) | Eşik | Sessiz | §6 |
| 4 | Denormal | Küçük sayı | FTZ | Performans | L0 |
| 5 | Blok boyu değişimi | Reconfigure | Yeniden tahsis (non-RT) | Kabul | pipeline |
| 6 | Sample rate değişimi | Reconfigure | Yeniden hesap | Kabul | pipeline |
| 7 | Kanal sayısı değişimi | Reconfigure | Yeniden tahsis | Kabul | pipeline |
| 8 | Param taşması | UI | clamp | Kabul | Tasarım |
| 9 | Eşzamanlı yazma | UI→RT | Lock-free atom | Kabul | L0 |
| 10 | Buffer taşması | Zincir | 512 sınırı | Kabul | sartname |

## §25 İlişkisel karar tablosu (Q3 için)

| # | Seçenek | Tanım | Artı | Eksi | Durum |
|---|---|---|---|---|---|
| 1 | dynamics üst blok | Zincirin son kazanç zarfı | Tek nokta | GR kaynağını görmez | Seçenek A |
| 2 | compressor üst blok | Her blok kendi zarfı | Yerel kontrol | Çifte GR riski | Seçenek B |
| 3 | Hibrit | dynamics + blok içi smoothing | Esnek | Karma | Seçenek C |
| 4 | Pipeline kararı | PipelineProcessorChain | JUCE yerleşim | Sıra | Seçenek D |
| 5 | Ölçüm kapatır | Kapalı çevrim belirler | Kanıt | Q4 gecikmeli | Seçenek E |
| 6 | Bekleme | Karar yok | Bilinçli | Gecikme | Şimdiki |

## §26 Zarf matrisi — frekans bağımlılığı

| # | Bant | Zarf davranışı | Öncelik | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | 20-60 Hz | Bass — pumping en hassas | Yüksek | Ölçüm PLANNED | Tasarım |
| 2 | 60-250 Hz | Alt orta — release hassas | Orta | Ölçüm PLANNED | Tasarım |
| 3 | 250-2000 Hz | Orta — duyulur | Yüksek | Ölçüm PLANNED | Tasarım |
| 4 | 2000-6000 Hz | Üst orta — tiz sertlik | Orta | Ölçüm PLANNED | Tasarım |
| 5 | 6000-20000 Hz | Tiz — zarf hızlı | Düşük | Ölçüm PLANNED | Tasarım |
| 6 | Tüm bant | Genel master | Yüksek | Ölçüm PLANNED | Tasarım |
| 7 | Sub kanal | 8.1 LFE | Özel | Ölçüm PLANNED | spatial |
| 8 | Surround | 5.1 kanalları | Orta | Ölçüm PLANNED | spatial |
| 9 | Height | 8.1 üst | Düşük | Ölçüm PLANNED | spatial |
| 10 | Mono fold | Karışım | Orta | Ölçüm PLANNED | pipeline |

## §27 Kısıt ve bütçe matrisi

| # | Kısıt | Değer | Kaynak | Etki | Durum |
|---|---|---|---|---|---|
| 1 | Buffer | 512 örnek | sartname | Blok gecikmesi 10.67 ms | Sabit |
| 2 | Örnekleme | 48 kHz | sartname | Zaman ölçeği | Sabit |
| 3 | float32 | Zorunlu | ADR-017 | Hassasiyet | FROZEN |
| 4 | Tahsis yasağı | 0 B | L0 kuralı | Baştan tahsis | Kural |
| 5 | Lock yasağı | 0 mutex | L0 kuralı | Atomik | Kural |
| 6 | noexcept | Zorunlu | L0 kuralı | Hata yakalama | Kural |
| 7 | alignas(64) hizalama | Önbellek | L0 kuralı | Satır | Kural |
| 8 | CPU bütçesi | Bilinmiyor | Ölçüm PLANNED | UNKNOWN | Q |
| 9 | Gecikme bütçesi | Bilinmiyor | Ölçüm PLANNED | UNKNOWN | Q |
| 10 | Kanal | 8.1 | ADR-017 | 8+LFE | FROZEN |
| 11 | ASIO exclusive | Tek uygulama | L0 kuralı | Kilit | Kural |
| 12 | DC >0.5V | Relay | L0 kuralı | Koruma | Kural |
| 13 | PCM3168A | Zorunlu | ADR-038 H001 | DAC | FROZEN |
| 14 | Per-OS player | FROZEN | ADR-019 | Değişiklik ≥088 | FROZEN |
| 15 | Ölçüm | PLANNED | Q4 | Belirsiz | PLANNED |

## §28 Bağlantı ve kapanış kontrolü

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | H1 + §N yapısı | Uygun | Tamam |
| 2 | Web tablosu başlığı | 4 sütun birebir | Tamam |
| 3 | Tarih sütunu | 2026-10-12 | Tamam |
| 4 | Durum sütunu | 200 OK | Tamam |
| 5 | URL benzersizliği | Dosya-içi benzersiz | Tamam |
| 6 | Repo kanıtı | volume.js:52 | Tamam |
| 7 | Authority kapanışı | Son satır | Tamam |
| 8 | Q3 | UNKNOWN taşındı | Tamam |
| 9 | Q4 | UNKNOWN taşındı | Tamam |
| 10 | commit | Atılmadı | Kural |

## §29 Ölçüm protokolü önizlemesi (Q4 sınırı)

| # | Adım | Girdi | Beklenen | Araç | Durum |
|---|---|---|---|---|---|
| 1 | Saf dalga | 1 kHz @ -20 dB | Zarf sapması ≤0.1 dB | Ölçüm PLANNED | Q4 |
| 2 | Kare dalga | ±1.0 | Rampa kliksiz | Test | testing |
| 3 | Susma | 0 | SNR >100 dB | Ölçüm PLANNED | Q4 |
| 4 | Swept-sine | 20-20k | THD+N <0.01% | Ölçüm PLANNED | Q4 |
| 5 | İmpuls | δ | Gecikme ölçümü | Ölçüm PLANNED | Q4 |
| 6 | Bloom testi | GR aç/kapa | Pumping yok | Kulaç | testing |
| 7 | Denormal | Tiny signal | Performans düşmez | Profil | L0 |
| 8 | CPU | Tam zincir | Bütçe içi | Profil | PLANNED |

## §30 Öz-kontrol listesi

| # | Madde | Durum |
|---|---|---|
| 1 | Q3 UNKNOWN taşındı | Tamam |
| 2 | Ölçüm PLANNED işaretli | Tamam |
| 3 | L0 kuralları §27 | Tamam |
| 4 | Anoloji kanıtı §7 | Tamam |
| 5 | URL §9 | Tamam |
| 6 | Authority son satır | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode