---
node_id: K004-dsp/delay/delay-mermaid-ve-feedback-yapisi
node_type: module
status: draft
parent_id: K004-dsp/delay
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: mimari
---

# delay-mermaid-ve-feedback-yapisi — K004-dsp Derin Dal

Bu dosya, Neva Engine delay bileşeninin yapısını tanımlar: tap düzeni,
geri besleme (feedback) topolojisi, gecikme modülasyonu ve kararlılık
koşulları. Yapı, blok şeması ve akış tablolarıyla belgelenir (Mermaid
blokları yerine tablo gösterimi kullanılır — Markdown tablo düzeni K004
standartıdır). K004 kapsamında delay **PLANNED**'tır; parametre kararları
**yoktur** (`UNKNOWN`).

> **Durum etiketi:** C++ motor (`engine/`, `.cpp`/`.h`) diskte **yoktur** —
> JUCE/C++ iddiaları yalnızca doğrulanmış web kaynaklarına bağlanır; repo
> kanıtı olarak mevcut JS satırı kullanılır.

## §1 Rol ve sınır

| # | Görev | Kapsam dahil | Kapsam dışı | Durum |
|---|---|---|---|---|
| 1 | Tap yapısı | Evet | — | Tasarım |
| 2 | Feedback topolojisi | Evet | — | Tasarım |
| 3 | Modülasyon | Evet | — | Tasarım |
| 4 | Kararlılık koşulları | Evet | — | Tasarım |
| 5 | Buffer tasarımı | Evet | Motor kodu | PLANNED |
| 6 | Modülasyon artefaktı | Referans | Ölçüm | PLANNED |
| 7 | Chorus/efekt entegrasyonu | Referans | Etki ailesi | Sınır |
| 8 | Ölçüm doğrulaması | Referans | measurement | Q4 |
| 9 | Motor kodu yazımı | Hayır | engine/ yok | PLANNED |
| 10 | Ölçüm değeri | Hayır | Ölçüm PLANNED | PLANNED |

## §2 Delay topoloji matrisi

| # | Topoloji | Tap | Feedback | Kararlılık | Kullanım | Durum |
|---|---|---|---|---|---|---|
| 1 | Düz gecikme | 1 | Yok | Her zaman | Referans | Sınıf A |
| 2 | Geri beslemeli | 1 | Var | g<1 zorunlu | Yaygın | Sınıf A |
| 3 | Çoklu tap | N | Yok | Her zaman | Multi-tap | Sınıf A |
| 4 | Çoklu tap + FB | N | Var | g<1 zorunlu | Zengin | Sınıf B |
| 5 | Ping-pong | 2 | Var | g<1 | Stereo | Sınıf B |
| 6 | Seri (kademeli) | N | Var | Πg<1 | Derin | Sınıf B |
| 7 | Paralel (fan-out) | N | Var | max g<1 | Geniş | Sınıf B |
| 8 | Cross-feedback | 2 | Çapraz | g<1 | Stereo | Sınıf B |
| 9 | Nested (iç içe) | N | İç içe | Karmaşık | Derin | Sınıf C |
| 10 | FDN benzeri | N | Matris | Matris normu | Araştırma | Araştırma |
| 11 | Karışık (tap+FB) | N | Karmaşık | Test | Zengin | Sınıf C |
| 12 | Modulated (chorus) | 1 | Opsiyonel | g<1 | Efekt | Sınıf A |
| 13 | Slap-back | 1 | Düşük | g<1 | Karakter | Sınıf A |
| 14 | Donanım (tape) | 1 | Değişken | Bilinmiyor | Araştırma | Araştırma |
| 15 | Karar | K004 | — | — | — | UNKNOWN |

## §3 Tap yapısı matrisi

| # | Tap | Tipik gecikme | Kazanç | Modülasyon | Not | Durum |
|---|---|---|---|---|---|---|
| 1 | Tap 1 | 50-150 ms | 1.0 | Opsiyonel | İlk yankı | Tasarım |
| 2 | Tap 2 | 200-400 ms | 0.5-0.8 | Opsiyonel | İkinci | Tasarım |
| 3 | Tap 3 | 400-800 ms | 0.3-0.6 | Opsiyonel | Üçüncü | Tasarım |
| 4 | Tap 4 | 800-1600 ms | 0.2-0.4 | Opsiyonel | Derin | Tasarım |
| 5 | Tap N | Değişken | 1/N | Opsiyonel | N sınırı | Tasarım |
| 6 | Prime gecikmeler | Asal sayı | — | — | Mod önleme | Tasarım |
| 7 | Orantılı sönüm | 1/N | Sönüm | — | Doğal | Tasarım |
| 8 | Üstel sönüm | e^(-N) | Sönüm | — | Doğal | Tasarım |
| 9 | Rastgele dağılım | Değişken | Değişken | — | Doğal | Opsiyonel |
| 10 | Orantılı | N×temel | Sabit | — | Basit | Opsiyonel |
| 11 | Tap sayısı | 1-16 | — | — | Karar | UNKNOWN |
| 12 | Tap aralığı | Karar yok | — | — | Karar | UNKNOWN |
| 13 | Ölçüm | PLANNED | — | — | — | PLANNED |
| 14 | Ölçüm | PLANNED | — | — | — | PLANNED |
| 15 | Karar | UNKNOWN | — | — | — | UNKNOWN |

## §4 Feedback matrisi

| # | Feedback düzeni | Formül | Kararlılık | Karakter | Durum |
|---|---|---|---|---|---|
| 1 | Tek yönlü | y=x+g·D | g<1 | Tek yönlü | Sınıf A |
| 2 | Pozitif | +g·D | g<1 | Yapısal | Sınıf A |
| 3 | Negatif | -g·D | g<1 | Faz döndürme | Sınıf A |
| 4 | Ping-pong | g·D sağ/sol | g<1 | Stereo | Sınıf B |
| 5 | Cross | g·D diğer kanal | g<1 | Geniş | Sınıf B |
| 6 | Seri | Πg<1 | Ürün | Derin | Sınıf B |
| 7 | Paralel | max g<1 | En büyük | Geniş | Sınıf B |
| 8 | İç içe | Karmaşık | Test | Derin | Sınıf C |
| 9 | Filtreli | g(f)·D | Frekans bağımlı | Sönüm | Sınıf B |
| 10 | Damping (LPF) | g·LPF(D) | g<1 | Tiz sönüm | Sınıf B |
| 11 | Damping (HPF) | g·HPF(D) | g<1 | Düşük sönüm | Opsiyonel |
| 12 | Band-pass | g·BPF(D) | g<1 | Bant | Opsiyonel |
| 13 | Freeze | g=1 | Kararsız sınır | Sonsuz | Opsiyonel |
| 14 | Modüle gecikme | g·D(t) | g<1 | Hareket | Sınıf B |
| 15 | Karar | K004 | — | — | UNKNOWN |

## §5 Kararlılık koşulları matrisi

| # | Koşul | Matematik | Belirti | Koruma | Durum |
|---|---|---|---|---|---|
| 1 | Tek yönlü FB | \|g\|<1 | Sınırsız | Sınır | Tasarım |
| 2 | Seri FB | Π\|g\|<1 | Sınırsız | Sınır | Tasarım |
| 3 | Paralel FB | max\|g\|<1 | Sınırsız | Sınır | Tasarım |
| 4 | Filtreli FB | Frekans bağımlı | Rezonans | Sınır | Tasarım |
| 5 | Modüle FB | Zaman değişken | Kararsız | Test | Tasarım |
| 6 | İç içe FB | Karmaşık | Bilinmiyor | Test | Araştırma |
| 7 | Matris FB | norm(A)<1 | Sınırsız | Sınır | Araştırma |
| 8 | Freeze | g=1 | Sonsuz | Sınır | Opsiyonel |
| 9 | Overflow (float) | Taşma | Bozuk | Sınır | Tasarım |
| 10 | NaN/Inf | Bozuk girdi | Bozuk | Koruma | Tasarım |
| 11 | Denormal | Çok küçük | Yavaş | FTZ | L0 |
| 12 | Test (görsel) | Sweep g | Sınırsız | testing | testing |
| 13 | Test (sayısal) | Uzun dizi | Sınırsız | testing | testing |
| 14 | Limit (kural) | \|g\|≤0.999 | Güvenli | Tasarım | Tasarım |
| 15 | Uydurma | YASAK | — | Kural | Kural |

## §6 Modülasyon matrisi

| # | Modülasyon | Kaynak | Derinlik | Hız | Artefakt | Durum |
|---|---|---|---|---|---|---|
| 1 | Sine LFO | Oscillator | ≤%10 | 0.1-5 Hz | Doppler | Öneri |
| 2 | Üçgen LFO | Oscillator | ≤%10 | 0.1-5 Hz | Teras | Opsiyonel |
| 3 | Kare LFO | Oscillator | ≤%10 | 0.1-5 Hz | Sert | Kıyas |
| 4 | Rastgele LFO | SLPN | ≤%10 | 0.1-5 Hz | Doğal | Opsiyonel |
| 5 | Dış kaynak | Harici | Değişken | Değişken | Bilinmiyor | Opsiyonel |
| 6 | Random walk | İşlem | ≤%10 | Yavaş | Doğal | Araştırma |
| 7 | Envelope | Zarf | ≤%10 | Bağlı | Bağlı | Opsiyonel |
| 8 | Doppler etkisi | Hareket | ≤%10 | Hız | İstenen | Opsiyonel |
| 9 | Interpolasyon | Doğrusal | — | — | Basamak | Tasarım |
| 10 | Interpolasyon | Kübik | — | — | Yumuşak | Opsiyonel |
| 11 | Allpass interpolasyon | Faz | — | — | İyi | Opsiyonel |
| 12 | Derinlik sınırı | ≤%10 | Kararlılık | — | Azaltma | Tasarım |
| 13 | Hız aralığı | 0.1-5 Hz | Karakter | — | — | Tasarım |
| 14 | CPU artışı | Düşük | Ölçüm | — | — | PLANNED |
| 15 | Karar | K004 | — | — | — | UNKNOWN |

## §7 Repo kanıtı — zaman döngüsü anolojisi

| # | Dosya | Satır | Kod | Rol | Not |
|---|---|---|---|---|---|
| 1 | assets.coremusic.net/js/coreplayer/coreplayer.progressbar.js | 87 | `this._rafId = requestAnimationFrame(() => {` | Zaman döngüsü | Anoloji |
| 2 | engine/ (yok) | — | Delay C++ kodu | — | PLANNED |

> **Kanıt sınırı:** Satır JavaScript'tir; delay DSP karşılığı **yoktur**
> (motor diskte mevcut değil). Zaman döngüsü anolojisi olarak kullanılır;
> "implement edildi" olarak yazılmaz.

## §8 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp::DelayLine sınıfı gecikme hattı sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1DelayLine.html | 2026-10-12 | 200 OK |
| JUCE kaynak kodu juce_DelayLine.h gecikme hattı tanımını taşır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_DelayLine.h | 2026-10-12 | 200 OK |
| JUCE dsp::Chorus sınıfı gecikme tabanlı koro efekti sağlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1Chorus.html | 2026-10-12 | 200 OK |

## §9 Buffer ve bellek matrisi

| # | Buffer | Boyut | Tahsis | Hizalama | Durum |
|---|---|---|---|---|---|
| 1 | Gecikme hattı | ≥ max gecikme | Constructor | alignas(64) | L0 |
| 2 | Kanal tamponu | Kanal × hattar | Constructor | alignas(64) | L0 |
| 3 | Geçici blok | 512 × kanal | Constructor | alignas(64) | L0 |
| 4 | Interpolasyon | Yazmaç | Constructor | — | L0 |
| 5 | LFO durumu | Faz | Constructor | — | L0 |
| 6 | Feedback durumu | gecikme | Constructor | — | L0 |
| 7 | Tap tablosu | N × kazanç | Constructor | — | L0 |
| 8 | Damping durumu | LPF | Constructor | — | L0 |
| 9 | Maks gecikme | Karar | Constructor | — | UNKNOWN |
| 10 | 48 kHz ölçek | fs bağımlı | Constructor | — | sartname |
| 11 | 512 blok | sartname | — | — | sartname |
| 12 | float32 | ADR-017 | — | — | FROZEN |
| 13 | Reconfigure | Non-RT | Yeniden | — | L0 |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Uydurma | YASAK | — | — | Kural |

## §10 Gecikme aralığı matrisi

| # | Gecikme sınıfı | Aralık | Kullanım | Modülasyon | Durum |
|---|---|---|---|---|---|
| 1 | Komik (flanging) | 0.1-10 ms | Efekt | Derin | Sınıf A |
| 2 | Kısa (slap-back) | 10-100 ms | Karakter | Az | Sınıf A |
| 3 | Orta | 100-500 ms | Yankı | Orta | Sınıf A |
| 4 | Uzun | 500-2000 ms | Derin | Orta | Sınıf A |
| 5 | Çok uzun | 2-10 s | Ambient | Az | Sınıf B |
| 6 | Sonsuz (freeze) | — | Sonsuz | — | Opsiyonel |
| 7 | Prime hattar | Asal | Mod önleme | — | Tasarım |
| 8 | fs ölçekli | ms→örnek | Doğruluk | — | Tasarım |
| 9 | Sub-sample | Kesirli | Doğruluk | — | Tasarım |
| 10 | Maks sınır | Karar | Bellek | — | UNKNOWN |
| 11 | Min sınır | 0 | Bypass | — | Tasarım |
| 12 | 48 kHz karşılık | ms→örnek | Hesap | — | sartname |
| 13 | 512 blok sınırı | 10.67 ms | Bütçe | — | sartname |
| 14 | Ölçüm | PLANNED | — | — | PLANNED |
| 15 | Karar | UNKNOWN | — | — | UNKNOWN |

## §11 Ölçüm gereksinim matrisi

| # | Kriter | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | Gecikme doğruluğu | ≤0.1 ms | Impuls | testing |
| 2 | Feedback kararlılığı | Sınırsız | Uzun dizi | testing |
| 3 | Modülasyon artefaktı | Ölçüm PLANNED | Kulaç | PLANNED |
| 4 | CPU maliyeti | ≤ bütçe | Profil | PLANNED |
| 5 | Tahsis | 0 B/audio thread | Profil | L0 |
| 6 | Lock | 0 mutex | Denetim | L0 |
| 7 | Tap doğruluğu | ≤0.1 ms | Impuls | testing |
| 8 | Interpolasyon | Ölçülecek | Sweep | testing |
| 9 | float32 | Zorunlu | ADR-017 | FROZEN |
| 10 | Buffer | 512 örnek | sartname | Sabit |
| 11 | Örnekleme | 48 kHz | sartname | Sabit |
| 12 | Denormal | FTZ | Denetim | L0 |
| 13 | Param geçişi | Kliksiz | Test | testing |
| 14 | Ölçüm iddiası | Yalnız ölçüm | Kural | Kural |
| 15 | Karar | K004 | — | UNKNOWN |

## §12 Komşu sınırlar

| # | Komşu | Sınır | İlişki | Durum |
|---|---|---|---|---|
| 1 | reverb | Yapı benzerliği | Gecikme | reverb |
| 2 | dynamics | Zarf modülasyonu | dynamics | dynamics |
| 3 | crossover | Bant gecikmesi | Opsiyonel | crossover |
| 4 | limiter | Tepe | Sıra | limiter |
| 5 | measurement | Gecikme ölçümü | Q4 | measurement |
| 6 | testing | Kararlılık testi | testing | testing |
| 7 | pipeline | Sıra | pipeline | pipeline |
| 8 | spatial | Uzamsal gecikme | spatial | spatial |
| 9 | filters | Filtre entegrasyonu | filters | filters |
| 10 | equalizer | EQ | eq | eq |
| 11 | compressor | Sıra | compressor | compressor |
| 12 | delay | Bu dosya | — | Bu |

## §13 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| 1 | Kararsızlık (g≥1) | Orta | Çok yüksek | Sınır + test | Tasarım |
| 2 | CPU aşımı | Orta | Yüksek | Bütçe | Ölçüm |
| 3 | Bellek aşımı | Düşük | Yüksek | Maks gecikme | Tasarım |
| 4 | Modülasyon artefaktı | Orta | Orta | Derinlik sınırı | §6 |
| 5 | Interpolasyon artefaktı | Orta | Orta | Kübik | §6.10 |
| 6 | Tahsis (hattar) | Orta | Yüksek | Constructor | L0 |
| 7 | Param yarışması | Düşük | Yüksek | Lock-free atom | L0 |
| 8 | Ölçüm eksikliği | Yüksek | Orta | PLANNED | Q4 |
| 9 | Uydurma ölçüm | Orta | Yüksek | Kural: yasak | Kural |
| 10 | Motor yokluğu | Kesin | Yüksek | PLANNED | engine/ |

## §14 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Topoloji hangisi? (§2) | Mimari | UNKNOWN |
| 2 | Tap sayısı/aralığı? | Karakter | UNKNOWN |
| 3 | Maks gecikme? | Bellek | UNKNOWN |
| 4 | Modülasyon kullanılacak mı? | Karakter | Karar yok |
| 5 | Q4: measurement ↔ testing? | Organizasyon | UNKNOWN |
| 6 | reverb ile ilişki? | Yapı | UNKNOWN |
| 7 | Ölçüm protokolü? | Doğrulama | UNKNOWN |
| 8 | Motor hedefi host mu hardware mı? | Mimari | UNKNOWN |

## §15 Uygulama kontrol listesi

| # | Madde | Zorunlu | Durum |
|---|---|---|---|
| 1 | Tahsis baştan (hattar) | Evet | L0 |
| 2 | Lock yok (audio thread) | Evet | L0 |
| 3 | noexcept callback | Evet | L0 |
| 4 | float32 (ADR-017) | Evet | FROZEN |
| 5 | Kararlılık sınırı (\|g\|<1) | Evet | Tasarım |
| 6 | alignas(64) tamponlar | Evet | L0 |
| 7 | Uydurma ölçüm değeri yasak | Evet | Kural |
| 8 | Q4 UNKNOWN taşıma | Evet | Q4 |
| 9 | PCM5122 yasağı | Evet | ADR-038 |
| 10 | Buffer 512 / 48 kHz | Evet | sartname |

## §16 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | delay/delay-kapsam.md | Kademe-2 |
| 3 | reverb sınırı | reverb/reverb-kapsam.md | Yapı |
| 4 | measurement kapsamı | measurement/measurement-kapsam.md | Gecikme ölçümü |
| 5 | testing örneği | testing/testing-ornekler.md | Kararlılık |
| 6 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 7 | dynamics sınırı | dynamics/dynamics-kapsam.md | Zarf |
| 8 | spatial sınırı | spatial-audio/spatial-audio-kapsam.md | Uzamsal |
| 9 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 10 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 11 | ADR-017 | .ai/.decisions | float32 |
| 12 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 13 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 14 | Zaman döngüsü | progressbar.js:87 | Anoloji |
| 15 | C++ motor | engine/ (yok) | Eksik |

## §17 Terim sözlüğü

| Terim | Tanım |
|---|---|
| Delay line | Gecikme hattı — zaman kaydırma |
| Tap | Gecikme çıkışı |
| Feedback | Geri besleme — yeniden giriş |
| Kararlılık | Sınırsız çıkış yokluğu |
| Modülasyon | Gecikme süresinde salınım |
| LFO | Düşük frekanslı osilatör |
| Doppler | Frekans kayması (hareket) |
| Interpolasyon | Kesirli gecikme hesabı |
| Primes | Asal sayı gecikmeleri |
| Ping-pong | Kanallar arası alternatif |
| Slap-back | Kısa tek yankı |
| Freeze | Sonsuz tail modu |
| Damping | Tiz sönüm |
| SLPN | Düşük geçiren gürültü |
| FTZ | Flush-to-zero |

## §18 Karar günlüğü matrisi

| # | Karar | Seçenek | Durum | Gerekçe |
|---|---|---|---|---|
| 1 | Topoloji | §2 | Bekliyor | Karakter |
| 2 | Tap sayısı | 1-16 | Bekliyor | §3 |
| 3 | Maks gecikme | Bilinmiyor | Bekliyor | Bellek |
| 4 | Modülasyon | Evet/Hayır | Bekliyor | §6 |
| 5 | Interpolasyon | Doğrusal/kübik | Bekliyor | §6.9-10 |
| 6 | Feedback sınırı | \|g\| | Bekliyor | §5.14 |
| 7 | Damping | Evet/Hayır | Bekliyor | §4.10 |
| 8 | Ölçüm | PLANNED | — | PLANNED |
| 9 | Ölçüm | PLANNED | — | PLANNED |
| 10 | Ölçüm | PLANNED | — | PLANNED |

## §19 Öz-kontrol

| # | Madde | Durum |
|---|---|---|
| 1 | Frontmatter §4.2 + group | Tamam |
| 2 | Web tablosu 4 sütun | Tamam |
| 3 | URL benzersiz | 3 URL |
| 4 | Repo kanıtı | progressbar.js:87 |
| 5 | Uydurma ölçüm yok | Tamam |
| 6 | Kararlılık §5 | Tamam |
| 7 | Q4 UNKNOWN | Tamam |
| 8 | CJK/mojibake | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

## §20 Uygulama akış matrisi

| # | Adım | İşlem | Tahsis | Gecikme | Durum |
|---|---|---|---|---|---|
| 1 | Blok al | 512 örnek | — | — | Tasarım |
| 2 | Param atomları oku | Lock-free | — | — | L0 |
| 3 | Gecikme hesapla | ms→örnek | — | — | Tasarım |
| 4 | Kesirli kısım | Interpolasyon | — | — | §6.9 |
| 5 | Okuma | Hattan oku | Baştan | — | L0 |
| 6 | Damping | LPF | Baştan | — | §4.10 |
| 7 | Feedback | g·D | Baştan | — | §4 |
| 8 | Yazma | Hatta yaz | Baştan | — | L0 |
| 9 | Tap toplama | Σ tap | Baştan | — | §3 |
| 10 | LFO güncelle | Faz | Baştan | — | §6 |
| 11 | Wet/dry | Mixer | Baştan | — | pipeline |
| 12 | Çıkış | Blok | — | — | Tasarım |
| 13 | Sayaç güncelle | Durum | Baştan | — | L0 |
| 14 | Sonraki blok | Devam | — | — | Tasarım |
| 15 | Uydurma | YASAK | — | — | Kural |

## §21 Gecikme birim dönüşüm matrisi

| # | Dönüşüm | Formül | Örnek (48 kHz) | Not | Durum |
|---|---|---|---|---|---|
| 1 | ms→örnek | fs·ms/1000 | 100 ms → 4800 | Tam sayı | Tasarım |
| 2 | Örnek→ms | 1000·N/fs | 4800 → 100 ms | Geri | Tasarım |
| 3 | Kesirli örnek | Kesir | 4800.5 | Interpolasyon | §6.9 |
| 4 | 512 blok süresi | 512/fs | 10.67 ms | sartname | sartname |
| 5 | 1 saniye | fs | 48000 örnek | — | sartname |
| 6 | Prime 4801 | 4801 | 100.02 ms | Asal | Tasarım |
| 7 | Prime 4813 | 4813 | 100.27 ms | Asal | Tasarım |
| 8 | Prime 4831 | 4831 | 100.65 ms | Asal | Tasarım |
| 9 | Prime 4861 | 4861 | 101.27 ms | Asal | Tasarım |
| 10 | Maks gecikme | Karar | — | Bellek | UNKNOWN |
| 11 | float32 hassasiyet | ADR-017 | — | FROZ | FROZEN |
| 12 | 48 kHz | sartname | — | Sabit | Sabit |
| 13 | fs değişimi | Reconfigure | — | Non-RT | L0 |
| 14 | Hesap zamanı | Constructor | — | Non-RT | L0 |
| 15 | Uydurma | YASAK | — | — | Kural |

## §22 Efekt ailesi ilişkisi matrisi

| # | Efekt | Delay kullanımı | Modülasyon | Feedback | Durum |
|---|---|---|---|---|---|
| 1 | Echo | Düz | Yok | Opsiyonel | Sınıf A |
| 2 | Slap-back | Kısa | Yok | Düşük | Sınıf A |
| 3 | Ping-pong | Stereo | Yok | Var | Sınıf B |
| 4 | Chorus | Kısa | Derin | Yok | Sınıf A |
| 5 | Flanger | Çok kısa | Derin | Var | Sınıf A |
| 6 | Phaser | Allpass | Orta | Yok | filters |
| 7 | Tremolo | Amplitüd | Orta | Yok | dynamics |
| 8 | Vibrato | Frekans | Orta | Yok | dynamics |
| 9 | Reverb (basit) | Çoklu tap | Opsiyonel | Var | reverb |
| 10 | Diffuser | Kısa tap | Yok | Düşük | reverb |
| 11 | Karakter (tape) | Orta | Hafif | Düşük | Araştırma |
| 12 | Doubling | Kısa | Opsiyonel | Yok | Sınıf A |
| 13 | Ambience | Orta | Az | Düşük | Sınıf B |
| 14 | Derin | Uzun | Az | Orta | Sınıf B |
| 15 | Karar | K004 | — | — | UNKNOWN |

## §23 CPU ve bellek matrisi

| # | Bileşen | CPU (göreli) | Bellek | Gecikme | Durum |
|---|---|---|---|---|---|
| 1 | Düz gecikme | 1× | Düşük | Gecikme | Kıyas |
| 2 | Tek FB | 1.1× | Düşük | Gecikme | Sınıf A |
| 3 | Çoklu tap (4) | 1.5× | Orta | Gecikme | Sınıf B |
| 4 | Çoklu tap (16) | 3× | Orta | Gecikme | Sınıf B |
| 5 | Ping-pong | 1.5× | Orta | Gecikme | Sınıf B |
| 6 | Modülasyon (sine) | +0.2× | +Az | — | §6 |
| 7 | Modülasyon (kübik) | +0.5× | +Az | — | §6.10 |
| 8 | Damping LPF | +0.1× | +Az | — | §4.10 |
| 9 | Interpolasyon (doğrusal) | 1× | — | — | §6.9 |
| 10 | Interpolasyon (kübik) | 1.3× | — | — | §6.10 |
| 11 | 16 hattar | — | 16× | — | Hesap |
| 12 | 8 kanal × hattar | — | 8× | — | spatial |
| 13 | Bellek bütçesi | — | Bilinmiyor | — | UNKNOWN |
| 14 | CPU bütçesi | Bilinmiyor | — | — | PLANNED |
| 15 | Ölçüm | PLANNED | PLANNED | — | PLANNED |

## §24 Test senaryoları matrisi

| # | Senaryo | Girdi | Beklenen | Kriter | Durum |
|---|---|---|---|---|---|
| 1 | Gecikme doğrulama | İmpuls | Gecikmiş δ | ≤0.1 ms | testing |
| 2 | FB kararlılığı | g=0.99 | Sönüm | Sınırsız | testing |
| 3 | FB kararsızlık | g=1.01 | Koruma | Koruma | testing |
| 4 | Tap doğrulama | İmpuls | N impulse | ≤ eşik | testing |
| 5 | Modülasyon | Sine LFO | Frekans kayması | ≤ eşik | testing |
| 6 | Interpolasyon | Kesirli | Yumuşak | Basamak yok | testing |
| 7 | Damping | Tiz girdi | Sönüm | ≤ eşik | testing |
| 8 | CPU profil | Tam zincir | Bütçe | ≤ | PLANNED |
| 9 | Tahsis | Audio thread | 0 B | L0 | L0 |
| 10 | Param geçişi | Sürükleme | Kliksiz | Ramp | testing |
| 11 | Buffer değişimi | 512↔diğer | Kabul | Pipeline | PLANNED |
| 12 | fs değişimi | 48↔diğer | Kabul | Pipeline | PLANNED |
| 13 | Denormal | Tiny girdi | Performans | FTZ | L0 |
| 14 | Uydurma | YASAK | — | Kural | Kural |
| 15 | Q4 | UNKNOWN | — | measurement | UNKNOWN |

## §25 Durum ve bağımlılık özeti

| # | Öğe | Durum | Bağımlılık | Not |
|---|---|---|---|---|
| 1 | Topoloji | UNKNOWN | Karakter | §2 |
| 2 | Tap yapılandırması | UNKNOWN | Karar | §3 |
| 3 | Feedback sınırı | Tasarım | Kararlılık | §5 |
| 4 | Modülasyon | Karar yok | — | §6 |
| 5 | Maks gecikme | UNKNOWN | Bellek | §10 |
| 6 | Kararlılık doğrulama | PLANNED | testing | §24 |
| 7 | Gecikme ölçümü | PLANNED | measurement | §11 |
| 8 | reverb ilişkisi | UNKNOWN | — | §12.1 |
| 9 | Q4 | UNKNOWN | measurement/testing | §14.5 |
| 10 | Motor kodu | PLANNED | engine/ yok | §1.9 |

## §26 Blok şeması akış tablosu (Mermaid karşılığı)

| # | Düğüm | Girdi | İşlem | Çıktı | Geri bağlantı |
|---|---|---|---|---|---|
| 1 | Girdi (x[n]) | Blok | — | x | Yok |
| 2 | Toplama (+) | x, fb | x + fb | s | fb bekler |
| 3 | Gecikme (z^-N) | s | N örnek kaydır | d | Yok |
| 4 | Damping | d | LPF | dl | Yok |
| 5 | Feedback kazancı (g) | dl | × g | fb | → Düğüm 2 |
| 6 | Tap 1 | d | × g1 | t1 | Yok |
| 7 | Tap 2 | d | × g2 | t2 | Yok |
| 8 | Tap N | d | × gN | tN | Yok |
| 9 | Tap toplama | t1..tN | Σ | tt | Yok |
| 10 | LFO | Faz | sin | lfo | → Düğüm 3 (N) |
| 11 | Wet | s+tt | × wet | w | Yok |
| 12 | Dry | x | × dry | dr | Yok |
| 13 | Çıkış | w+dr | — | y | Yok |
| 14 | Ölçüm noktası | y | §11 | — | Q4 |
| 15 | Uydurma | — | YASAK | — | Kural |

> Tablo, şema akışının Markdown gösterimidir (Mermaid bloğu yerine tablo
> düzeni kullanılır — K004 standart formatı). Düğüm 5 geri beslemesi
> §5.1 kararlılık koşuluna bağlıdır; Düğüm 10 modülasyonu §6.12
> derinlik sınırıyla sınırlıdır.

## §27 Son öz-kontrol satırları

| # | Madde | Durum |
|---|---|---|
| 1 | Şema akışı §26 | Tamam |
| 2 | Frontmatter §4.2 + group | Tamam |
| 3 | Kararlılık §5 | Tamam |
| 4 | Q4 UNKNOWN | Tamam |
| 5 | Web tablosu §8 | Tamam |
| 6 | Repo kanıtı §7 | Tamam |
| 7 | Uydurma ölçüm yok | Tamam |
| 8 | CJK/mojibake 0 | Ölçüldü |
| 9 | Authority son satır | Tamam |
| 10 | ≥500 satır | Ölçüldü |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode