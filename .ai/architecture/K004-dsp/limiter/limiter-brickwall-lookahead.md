---
title: "limiter-brickwall-lookahead — K004-dsp Derin Dal"
type: architecture-deep-dive
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K004-dsp/dsp-master.md §3 · limiter dalı"
updated: 2026-10-12
parent_id: K004-dsp/limiter
group: mimari
node_id: K004-dsp/limiter/limiter-brickwall-lookahead.md
---

# limiter-brickwall-lookahead — K004-dsp Derin Dal

## §1 Rol ve sınır

Bu dal, sınırlayıcının (limiter) **tavan garantisi** kararlarını belgeler: brickwall
davranışının nasıl kurulacağı, look-ahead tamponunun gecikme bütçesini nasıl
etkilediği, gerçek tepe (true peak) riskinin nasıl yönetileceği ve kazanç
yumuşatmasının tıkırtı üretmeden nasıl uygulanacağı.

`compressor` politika ortağıdır; limiter ondan ayrı olarak **kesin tavan** garantisi
verir. Ölçüm (loudness/true peak) sahibi `measurement` dalıdır; bu dosya yalnızca
sınırı yazar.

| # | Bu dalın içinde | Dışında | Komşu |
|---|---|---|---|
| 1 | Brickwall tanımı ve davranışı | GR politikası genişletmesi | compressor |
| 2 | Look-ahead tamponu ve gecikme | Ölçüm standardı metni | measurement |
| 3 | Release stratejileri | Zarf matematiği soyut tarafı | dynamics |
| 4 | Oversampling ve true peak ölçümü | Ölçüm sahipliği | measurement |
| 5 | Kazanç yumuşatma (smoothing) | Bütçe planı | dynamics |
| 6 | Sıra kararı (en son mu, çıkışta mı) | Zincir sahipliği | pipeline |
| 7 | Gerçek zamanlı kısıtlar | C++ uygulaması (motor yok) | build |
| 8 | Bypass/AB güveni | Test kapsamı | testing |

## §2 Brickwall davranışı

Brickwall, hiçbir örneğin belirlenen tavanı aşmaması garantisidir. K004 kapsamında
tavan değeri (dB) ve gerçek tepe toleransı için karar metni **yoktur**.

| # | Tavan tipi | Tanım | Risk | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Örnekleme tepe (sample peak) | Her örnek ≤ tavan | Örnekleme-arası aşım | Tepki ölçümü | Değer yok |
| 2 | Gerçek tepe (true peak) | Süreklileştirilmiş tepe ≤ tavan | Ölçüm gerektirir | Oversampling ölçüm | Değer yok |
| 3 | Sessiz mutlak tavan | Hiçbir aşım yok | Sert karakter | Tepki ölçümü | Değer yok |
| 4 | Yumuşak tavan | Eşik üstünde sınırlı artış | Aşım riski | Tepki ölçümü | Değer yok |
| 5 | Bant bazlı tavan | Bant başına sınır | Toplam aşım | Toplam ölçüm | UNKNOWN |
| 6 | Kanal bazlı tavan | Kanal başına sınır | Kanal farkı | Kanal ölçümü | UNKNOWN |
| 7 | Ortak tavan (link) | Tüm kanallarda ortak | Tutarlılık | Kanal ölçümü | UNKNOWN |
| 8 | Girdi bazlı tavan | Girdiye göre | Karakter değişimi | Ölçüm | UNKNOWN |
| 9 | Ölçekli tavan | fs'e bağlı | fs değişimi | fs testi | Karar yok |
| 10 | Preset tavanı | Profil ile | Taşınabilirlik | Preset testi | Biçim yok |
| 11 | Ölçüm referans tavanı | Standart ile | Bağımlılık | Ölçüm | measurement |
| 12 | Donanım tavanı | DAC/çıkış ile | Donanım | Donanım ölçüm | audio-hw |

## §3 Look-ahead ve gecikme bütçesi

Look-ahead, tavan aşımını önceden görerek kazancı zamanında düşürmek için kullanılan
ileri bakış tamponudur. Bedeli gecikmedir; K004'ün 512 örnek hedefi bu bütçeyi
sınırlar.

| # | Look-ahead senaryosu | Ek gecikme | Tavan güveni | Maliyet | Durum |
|---|---|---|---|---|---|
| 1 | Look-ahead yok | 0 | Düşük (zirve kaçışı) | Düşük | Karar yok |
| 2 | Kısa look-ahead | Az | Orta | Düşük | Değer yok |
| 3 | Orta look-ahead | Orta | Yüksek | Orta | Değer yok |
| 4 | Uzun look-ahead | Çok | Çok yüksek | Yüksek | Bütçe aşımı riski |
| 5 | Blok boyuna eşit | 512 örnek | Yüksek | Orta | sartname ile sınırlı |
| 6 | Blok boyundan büyük | >512 | Çok yüksek | Yüksek | Blok dışı, riskli |
| 7 | Ölçüm pencereli | Pencere kadar | Ölçüme bağlı | Orta | measurement |
| 8 | Bant bazlı | Bant başına | Değişken | Yüksek | UNKNOWN |
| 9 | Kanal bazlı | Kanal başına | Değişken | Orta | UNKNOWN |
| 10 | Bypass eşitliği için | Ek | — | Orta | testing |
| 11 | Preset ile | Profil | Değişken | Düşük | Biçim yok |
| 12 | fs bağımsız | — | — | — | Karar yok |

## §4 Release stratejileri

| # | Release stratejisi | Davranış | Pompalama riski | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Sabit release | Zaman sabitli | Orta | Zaman ölçümü | Değer yok |
| 2 | Program-bağımlı | Sinyale göre | Düşük | Ölçüm | Değer yok |
| 3 | Otomatik | Sinyale göre | Düşük | Ölçüm | Değer yok |
| 4 | Üstel | Yavaş kuyruk | Orta | Ölçüm | Değer yok |
| 5 | Doğrusal | Sabit eğim | Yüksek | Ölçüm | Değer yok |
| 6 | Çoklu kademeli | Aşamalı | Düşük | Ölçüm | Değer yok |
| 7 | Hold + release | Bekleme sonrası | Düşük | Ölçüm | Değer yok |
| 8 | Serbest bırakma | Bağımsız | Orta | Ölçüm | Değer yok |
| 9 | Frekans bağımlı | Bant bazlı | Düşük | Ölçüm | UNKNOWN |
| 10 | Kanal bağımsız | Ayrık | Orta | Kanal ölçümü | UNKNOWN |
| 11 | Kanal bağlı | Ortak | Düşük | Kanal ölçümü | UNKNOWN |
| 12 | Blok-içi güncelleme | 512 örnek | Blok sınırı | Blok testi | sartname |
| 13 | fs duyarlı | fs ile değişir | fs testi | fs testi | Karar yok |
| 14 | Ölçüm referanslı | Standart ile | Ölçüm | Ölçüm | measurement |

## §5 Oversampling ve gerçek tepe

Örnekleme-arası tepe (true peak), örnek değerlerin arasına düşen gizli zirveleri
ifade eder. Aşırı örneklemek (oversampling) bu zirveleri görünür kılar; bedeli
işlem maliyeti ve ek gecikmedir.

| # | Oversampling senaryosu | Fark | true peak doğruluğu | Maliyet | Durum |
|---|---|---|---|---|---|
| 1 | Oversampling yok | 1x | Düşük | Düşük | Ölçüm yok |
| 2 | 2x | 2x | Orta | Orta | Değer yok |
| 3 | 4x | 4x | Yüksek | Yüksek | Değer yok |
| 4 | 8x | 8x | Çok yüksek | Çok yüksek | Değer yok |
| 5 | Ölçüm için ayrı yol | Ölçüm yolu | Yüksek | Orta | measurement |
| 6 | Yazma için ayrı yol | Çıkış yolu | Yüksek | Yüksek | pipeline |
| 7 | Bölünmüş (partition) | Blok-içi | Yüksek | Orta | reverb sınırı |
| 8 | Kanal başına | Kanal | Yüksek | Kanal kadar | UNKNOWN |
| 9 | Ortak | Tek yol | Yüksek | Orta | UNKNOWN |
| 10 | Ölçüm penceresine bağlı | Pencere | Değişken | Orta | Q4 |
| 11 | Preset ile | Profil | Değişken | Düşük | Biçim yok |
| 12 | fs bağımsız | — | — | — | Karar yok |

## §6 Kazanç yumuşatma (smoothing)

| # | Yumuşatma konusu | Tıkırtı riski | Yöntem | Sahip |
|---|---|---|---|---|
| 1 | Tavan değişimi | Yüksek | Yavaş geçiş | dynamics |
| 2 | Release çıkışı | Orta | Ballistics | dynamics |
| 3 | Bypass geçişi | Yüksek | Eşitleme + geçiş | testing |
| 4 | Preset değişimi | Yüksek | Yumuşatma | dynamics |
| 5 | Ölçek değişimi | Orta | Yavaş geçiş | dynamics |
| 6 | Kanal linki değişimi | Orta | Yavaş geçiş | dynamics |
| 7 | Look-ahead değişimi | Yüksek | Sıfırlama | UNKNOWN |
| 8 | Oversampling değişimi | Yüksek | Sıfırlama | UNKNOWN |
| 9 | Durum taşıma | Orta | Blok sınırı | pipeline |
| 10 | Sıfırlama | Orta | Reset politikası | UNKNOWN |
| 11 | Ölçüm sondası | Yok | — | measurement |
| 12 | Yazma | — | Sıfır tahsis | L0 |

## §7 Repo kanıtı

Depoda brickwall uygulaması **yoktur**; en yakın gerçek kod, sayısal bir sınırlama
(clamp) desenidir. Bu, bir analogidir ve dürüstçe çerçevelenir.

| # | İddia | Dosya | Satır | Gözlem |
|---|---|---|---|---|
| 1 | Clamp deseni gerçek | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 54 | `return Math.min(1, Math.max(0, value));` |
| 2 | Clamp metodu tanımı | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 53 | `clamp(value) {` |
| 3 | Sezgisel arayüz | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 63 | `fromClientX(clientX, trackLeft, trackWidth) {` |
| 4 | Tekerlek girdisi | assets.coremusic.net/js/coreplayer/coreplayer.volume.js | 68 | `fromWheel(deltaY, current) {` |
| 5 | Seeker da clamp kullanır | assets.coremusic.net/js/coreplayer/coreplayer.seekbar.js | 33 | `clamp(value) {` |
| 6 | Zaman sınırı clamp | assets.coremusic.net/js/coreplayer/coreplayer.seekbar.js | 45 | `return Math.min(duration, Math.max(0, currentTime + step));` |
| 7 | Brickwall C++ kodu yok | engine/ | 0 | 2026-10-12 envanter ölçümü |
| 8 | `.cpp/.h` dosyası yok | Tüm depo | 0 | 2026-10-12 envanter ölçümü |

> **Sınır notu:** JS clamp, tek örneklik bir sayısal sınırlamadır; DSP brickwall
> bir **zaman serisi** garantisidir. Bu dosyada yalnızca "sınır uygulama" deseninin
> canlı örneği olarak anılır; brickwall garantisi için repo kanıtı **yoktur**.

## §8 Gerçek zamanlı kısıtlar

| # | Kural | limiter karşılığı | İhlal belirtisi | Denetim |
|---|---|---|---|---|
| 1 | Tahsis yasak | Look-ahead tamponu baştan allocate | Kopma | Kod denetimi |
| 2 | Kilit yasak | Parametre yazımı lock-free | Gecikme | Kod denetimi |
| 3 | noexcept | Callback istisna atmaz | Kapanma | Derleme |
| 4 | float32 (ADR-017) | Tavan ve kazanç tipi | Gereksiz çiftlik | Kod denetimi |
| 5 | 512 örnek | Blok-içi tavan kontrolü | Blok dışı | sartname |
| 6 | 48 kHz | Süre sabitleri fs bağlı | Yanlış ms | sartname |
| 7 | Denormal | Sessizlikte CPU | Süre artışı | L0 kuralı |
| 8 | alignas(64) | Tampon hizası | Önbellek ezilmesi | Ölçüm |
| 9 | ASIO Exclusive | Cihaz sahipliği | Çakışma | Çalışma anı |
| 10 | PCM3168A | Çıkış DAC'u | PCM5122 | BOM |
| 11 | DC relö | DC koruması | Eşik aşımı | Ölçüm |
| 12 | ADR-019 | Per-OS player değişmez | İhlal | Denetim |
| 13 | Sürüm kanıtı | JUCE/ASIO | İddia | VERIFICATION REQUIRED |

## §9 Ölçüm etkileşimi

| # | Ölçüm konusu | Yön | Sahip | Belirsizlik |
|---|---|---|---|---|
| 1 | Gerçek tepe ölçümü | limiter ↔ measurement | Ölçüm | Oversampling katsayısı |
| 2 | Loudness hedefi | measurement → limiter | Ölçüm | Standart metni measurement'ta |
| 3 | Tavan doğrulama | limiter → measurement | Ölçüm | Pencere Q4 |
| 4 | GR raporu | limiter → measurement | Ölçüm | Arayüz yok |
| 5 | Eşik seçimi | limiter | Politika | UNKNOWN |
| 6 | Release seçimi | limiter | Politika | UNKNOWN |
| 7 | Look-ahead bütçesi | limiter | Tasarım | UNKNOWN |
| 8 | Test kapsamı | testing | Test | Q4 |

## §10 Komşu sınırlar

| # | Komşu | Sınır | Çelişki halinde | Durum |
|---|---|---|---|---|
| 1 | compressor | GR politikası | Sıra | compressor sahibi |
| 2 | dynamics | Zarf matematiği | Smoothing | Q3 |
| 3 | measurement | Ölçüm sahipliği | Q4 | Bekliyor |
| 4 | pipeline | Sıra | Çıkış yeri | pipeline |
| 5 | equalizer | Bant seviyesi | Sıra | pipeline |
| 6 | delay | Gecikme bütçesi | Look-ahead | delay |
| 7 | reverb | Konvolüsyon | Sıra | reverb |
| 8 | spatial-audio | Kanal tavanı | 8.1 | PLANNED |
| 9 | crossover | Bant tavanı | Bant bölme | crossover |
| 10 | testing | Bypass/AB | Kapsam | testing |

## §11 Riskler ve açık sorular

| # | Kayıt | Tip | Etki | Durum |
|---|---|---|---|---|
| 1 | Tavan değeri (dB) | Tasarım | Davranış | UNKNOWN |
| 2 | Gerçek tepe toleransı | Tasarım | Güven | UNKNOWN |
| 3 | Look-ahead süresi | Tasarım | Gecikme | UNKNOWN |
| 4 | Release aralığı | Tasarım | Pompalama | UNKNOWN |
| 5 | Oversampling katsayısı | Tasarım | Doğruluk/maliyet | UNKNOWN |
| 6 | Yumuşatma aralığı | Tasarım | Tıkırtı | UNKNOWN |
| 7 | Kanal link politikası | Tasarım | Tutarlılık | UNKNOWN |
| 8 | Bypass eşitliği | Test | Güven | UNKNOWN |
| 9 | Ölçüm penceresi | Ölçüm | Doğruluk | Q4 |
| 10 | Test kapsamı | Test | Güven | Q4 |
| 11 | JUCE sürüm kanıtı | Bağımlılık | Derleme | VERIFICATION REQUIRED |
| 12 | ASIO SDK sürüm kanıtı | Bağımlılık | Derleme | VERIFICATION REQUIRED |
| 13 | Ölçüm tekrarı | Ölçüm | Güven | Ölçülmedi |
| 14 | true peak ölçüm yolu | Ölçüm | Güven | Yok |
| 15 | Donanım tavanı | Donanım | Güven | audio-hw |
| 16 | Sıra kararı | Mimari | Davranış | pipeline |

## §12 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp modülü ses sinyaline uygulanan limiter sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1Limiter.html | 2026-10-12 | 200 OK |
| JUCE dsp modülü oversampling (aşırı örnekleyici) sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1Oversampling.html | 2026-10-12 | 200 OK |
| JUCE `juce_dsp` kaynak ağacında limiter başlık dosyası bulunur | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/widgets/juce_Limiter.h | 2026-10-12 | 200 OK |
| JUCE `juce_dsp` kaynak ağacında oversampling başlık dosyası bulunur | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_Oversampling.h | 2026-10-12 | 200 OK |
| EBU, "EBU mode" loudness ölçümü için teknik doküman yayımlamıştır | https://tech.ebu.ch/docs/tech/tech3341.pdf | 2026-10-12 | 200 OK |

## §13 Kanıt özeti

| # | Kanıt | Tür | Konum |
|---|---|---|---|
| 1 | Clamp deseni (volume) | Repo kodu | assets.coremusic.net/js/coreplayer/coreplayer.volume.js satır 54 |
| 2 | Clamp metodu | Repo kodu | coreplayer.volume.js satır 53 |
| 3 | Seeker clamp | Repo kodu | coreplayer.seekbar.js satır 33 ve 45 |
| 4 | Brickwall kodu yok | Envanter | Ölçüm 2026-10-12 |
| 5 | 5 URL HTTP 200 | Web | §12 |
| 6 | Kademe-2 kapsam kaydı | Vault | limiter/limiter-kapsam.md |
| 7 | Kademe-2 örnek dosyası | Vault | limiter/limiter-ornekler.md |
| 8 | EBU dokümanı | Web | tech3341.pdf |
| 9 | Ölçüm sahipliği | Vault | measurement |
| 10 | Tavan değeri kararı | Eksik | UNKNOWN |

## §14 Terim sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Limiter | Tavan garantisi veren dinamik işlem |
| 2 | Brickwall | Aşım olmayan kesin tavan |
| 3 | Look-ahead | İleri bakış tamponu |
| 4 | true peak | Örnekleme-arası gerçek tepe |
| 5 | sample peak | Örnekleme tepe değeri |
| 6 | Oversampling | Aşırı örnekleyici |
| 7 | Interpolation | Ara değer hesaplama |
| 8 | Alias | Örnekleme yan ürünü |
| 9 | Release | Toparlanma hızı |
| 10 | Pompalama | Seviye salınımı etkisi |
| 11 | Smoothing | Yumuşatma |
| 12 | Clamp | Sayısal sınırlama |
| 13 | Headroom | Seviye payı |
| 14 | GR | Kazanç azaltımı |
| 15 | Loudness | Algılanan şiddet |
| 16 | LUFS | Loudness birimi |
| 17 | EBU mode | EBU ölçüm kipi |
| 18 | Bypass | Düz geçiş |
| 19 | Block | 512 örnek |
| 20 | fs | Örnekleme hızı |
| 21 | float32 | ADR-017 veri tipi |
| 22 | Denormal | CPU riski |
| 23 | noexcept | İstisna atmayan imza |
| 24 | lock-free | Kilit kullanmayan yazım |
| 25 | UNKNOWN | Karar metni olmayan değer |

## §15 Tavan bazlı karar matrisi

| # | Tavan senaryosu | Örnek tepe | true peak | Look-ahead | Oversampling | Durum |
|---|---|---|---|---|---|---|
| 1 | Düşük çıkış (kulaklık) | Sınır | Sınır | Bütçe içi | Ölçüm | UNKNOWN |
| 2 | Orta çıkış (hoparlör) | Sınır | Sınır | Bütçe içi | Ölçüm | UNKNOWN |
| 3 | Yüksek çıkış (ampli) | Sınır | Sınır | Bütçe içi | Ölçüm | UNKNOWN |
| 4 | Yayın yolu | Sınır | Sınır | Orta | Ölçüm | UNKNOWN |
| 5 | Streaming yolu | Sınır | Sınır | Orta | Ölçüm | UNKNOWN |
| 6 | Stüdyo master | Sınır | Sınır | Yüksek | Ölçüm | UNKNOWN |
| 7 | Ölçüm referansı | Sınır | Sınır | Ölçüm | Ölçüm | measurement |
| 8 | 8.1 çıkış | Kanal | Kanal | Bütçe içi | Ölçüm | PLANNED |
| 9 | Spatial çıkış | Kanal grup | Kanal grup | Bütçe içi | Ölçüm | PLANNED |
| 10 | Bypass | Yok | Yok | Yok | Yok | testing |
| 11 | Test vektörü | Bilinen | Bilinen | Bilinen | Bilinen | testing |
| 12 | Ölçüm tekrarı | Ölçülen | Ölçülen | Ölçülen | Ölçülen | measurement |

## §16 Ölçüm gereksinim matrisi

| # | Gereksinim | Amaç | Sahip | Durum |
|---|---|---|---|---|
| 1 | true peak ölçüm yolu | Aşım doğrulama | measurement | Yok |
| 2 | Oversampling ölçümü | Doğruluk | measurement | Yok |
| 3 | Tavan doğrulama testi | Garanti | testing | Tanım yok |
| 4 | Bypass eşitliği testi | Karşılaştırma | testing | Tanım yok |
| 5 | Release zaman testi | Pompalama | testing | Tanım yok |
| 6 | Look-ahead bütçe testi | Gecikme | testing | Tanım yok |
| 7 | Ölçüm penceresi sabitliği | Tekrar | measurement | Q4 |
| 8 | Ölçüm tekrarlanabilirliği | Güven | measurement | Yok |
| 9 | Ölçüm standart metni | Referans | measurement | §12 URL |
| 10 | Donanım tapan ölçümü | Donanım | audio-hw | Yok |
| 11 | SNR etkisi | Gürültü | testing | Hedef yok |
| 12 | DC eşiği | Koruma | audio-hw | Relö kuralı |

## §17 Karar günlüğü

| # | Kayıt | Tarih | Etkilenen | Durum |
|---|---|---|---|---|
| 1 | Kademe-2 kapsam kaydı | 2026-10-12 | limiter | Mevcut |
| 2 | Derin dal (bu dosya) | 2026-10-12 | limiter | Bu dosya |
| 3 | Tavan değeri yazılmadı | 2026-10-12 | limiter | UNKNOWN |
| 4 | true peak toleransı yazılmadı | 2026-10-12 | limiter | UNKNOWN |
| 5 | Look-ahead süresi yazılmadı | 2026-10-12 | limiter | UNKNOWN |
| 6 | Release aralığı yazılmadı | 2026-10-12 | limiter | UNKNOWN |
| 7 | Oversampling katsayısı yazılmadı | 2026-10-12 | limiter | UNKNOWN |
| 8 | Yumuşatma aralığı yazılmadı | 2026-10-12 | dynamics | UNKNOWN |
| 9 | Ölçüm yolu tanımı yok | 2026-10-12 | measurement | Yok |
| 10 | Test kapsamı tanımı yok | 2026-10-12 | testing | Q4 |
| 11 | Q4 bekliyor | 2026-10-12 | architect | Bekliyor |
| 12 | EBU dokümanı doğrulandı | 2026-10-12 | research | Ölçüldü |
| 13 | Tahsis yasak L0'da | 2026-10-12 | L0 | Mevcut |
| 14 | Kilit yasak L0'da | 2026-10-12 | L0 | Mevcut |
| 15 | noexcept kuralı L0'da | 2026-10-12 | L0 | Mevcut |
| 16 | float32 kararı ADR-017'de | 2026-10-12 | ADR-017 | Mevcut |
| 17 | 512/48k sartname PLANNED | 2026-10-12 | sartname | PLANNED |
| 18 | Ölçüm testleri PLANNED | 2026-10-12 | measurement | PLANNED |
| 19 | 8.1 tavan PLANNED | 2026-10-12 | spatial-audio | PLANNED |
| 20 | C++ motor envanteri 0 | 2026-10-12 | build | Ölçüldü |
| 21 | 5 URL doğrulandı | 2026-10-12 | research | Ölçüldü |
| 22 | JUCE sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| 23 | ASIO SDK sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| 24 | true peak ölçümü yok | 2026-10-12 | measurement | Ölçülmedi |
| 25 | Bypass testi yok | 2026-10-12 | testing | Yok |
| 26 | Release testi yok | 2026-10-12 | testing | Yok |
| 27 | Look-ahead bütçe testi yok | 2026-10-12 | testing | Yok |
| 28 | Ölçüm tekrarı yok | 2026-10-12 | measurement | Ölçülmedi |
| 29 | Donanım tavanı ölçülmedi | 2026-10-12 | audio-hw | Ölçülmedi |
| 30 | Üst karar bekleniyor | 2026-10-12 | architect | Bekliyor |

## §18 Denetim matrisi

| # | Denetim | Yöntem | Sıklık | Sorumlu | Durum |
|---|---|---|---|---|---|
| 1 | Tavan kararı mevcut mu | Vault araması | Sürüm | architect | Yok |
| 2 | true peak toleransı tanımlı mı | Tasarım okuma | Sürüm | embedded | UNKNOWN |
| 3 | Look-ahead bütçesi tanımlı mı | Tasarım okuma | Sürüm | embedded | UNKNOWN |
| 4 | Tahsis yok mu | Kod denetimi | Her PR | embedded | Motor yok |
| 5 | Mutex yok mu | Kod denetimi | Her PR | embedded | Motor yok |
| 6 | noexcept tam mı | Derleme | Her derleme | build | Motor yok |
| 7 | float32 tutarlı mı | Tip denetimi | Her PR | embedded | ADR-017 |
| 8 | 512 blok doğrulandı mı | Sartname ölçümü | Sürüm | testing | PLANNED |
| 9 | 48 kHz doğrulandı mı | Sartname ölçümü | Sürüm | testing | PLANNED |
| 10 | Tavan doğrulandı mı | Tepki ölçümü | Sürüm | testing | Yok |
| 11 | true peak doğrulandı mı | Oversampling ölçüm | Sürüm | measurement | Yok |
| 12 | Bypass eşitliği korundu mu | Seviye ölçümü | Sürüm | testing | Tanım yok |
| 13 | Release davranışı doğrulandı mı | Zaman ölçümü | Sürüm | testing | Yok |
| 14 | Pompalama ölçüldü mü | Dinleme/ölçüm | Sürüm | testing | Yok |
| 15 | Gecikme ölçüldü mü | Zaman ölçümü | Sürüm | testing | Yok |
| 16 | Oversampling doğrulandı mı | Karşılaştırma | Sürüm | measurement | Yok |
| 17 | Kanal tutarlılığı test edildi mi | Kanal ölçümü | Sürüm | testing | UNKNOWN |
| 18 | 8.1 tavan test edildi mi | Kanal ölçümü | Sürüm | K003-audio | PLANNED |
| 19 | Ölçüm penceresi sabit mi | Tekrar ölçümü | Sürüm | measurement | Q4 |
| 20 | Ölçüm tekrarlanabilirliği | Tekrar testi | Sürüm | measurement | Yok |
| 21 | DC eşiği korundu mu | DC ölçümü | Donanım | audio-hw | Relö kuralı |
| 22 | SNR etkisi ölçüldü mü | SNR ölçümü | Sürüm | testing | Yok |
| 23 | Sürüm kanıtı mevcut mu | Derleme kaydı | Sürüm | build | VERIFICATION REQUIRED |
| 24 | Lisans denetimi yapıldı mı | Lisans taraması | Sürüm | legal | Yok |
| 25 | URL benzersizliği korundu mu | Vault ölçümü | Dosya | vault | Ölçüm betiği |
| 26 | Mojibake yok mu | Byte ölçümü | Dosya | vault | Ölçüm betiği |
| 27 | Dolgu dizgisi yok mu | Metin taraması | Dosya | vault | Ölçüm betiği |
| 28 | Frontmatter tam mı | Alan sayımı | Dosya | vault | 10 alan |
| 29 | node_id yolu doğru mu | Dosya yolu | Dosya | vault | Ölçüm betiği |
| 30 | Satır sayısı 500 üstü mü | Satır sayımı | Dosya | vault | Ölçülecek |

## §19 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | limiter/limiter-kapsam.md | Kademe-2 |
| 3 | compressor sınırı | compressor/compressor-kapsam.md | Sıra |
| 4 | dynamics sınırı | dynamics/dynamics-kapsam.md | Smoothing |
| 5 | measurement kapsamı | measurement/measurement-kapsam.md | Q4 |
| 6 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 7 | delay kapsamı | delay/delay-kapsam.md | Gecikme bütçesi |
| 8 | spatial kapsamı | spatial-audio/spatial-audio-kapsam.md | 8.1 tavanı |
| 9 | testing örneği | testing/testing-ornekler.md | Test |
| 10 | crossover kapsamı | crossover/crossover-kapsam.md | Bant tavanı |
| 11 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 12 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 13 | Kök indeks | index.md | Katalog |
| 14 | ADR-017 | .ai/.decisions | float32 |
| 15 | ADR-019 | .ai/.decisions | Per-OS FROZEN |
| 16 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 17 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 18 | Clamp kodu | coreplayer.volume.js | Analoji |
| 19 | Seeker clamp kodu | coreplayer.seekbar.js | Analoji |
| 20 | C++ motor | engine/ (yok) | Eksik |

## §20 Limit-algoritma karşılaştırma matrisi

| # | Algoritma | Gecikme | Tepe garantisi | Renk riski | İşlem maliyeti | OS gereği | Durum |
|---|---|---|---|---|---|---|---|
| 1 | Basit clamp | 0 örnek | Sert | Şiddetli parazit | Düşük | Hayır | Kıyas |
| 2 | Hard-knee attack | 0 örnek | Sert | Orta | Düşük | Hayır | Kıyas |
| 3 | Smoothed gain (tek kutup) | Kalıcı | Sert (clamp sonrası) | Düşük | Düşük | Hayır | K004-vurgu |
| 4 | Look-ahead + envelope | Look-ahead kadar | Sert | Düşük | Orta | Hayır | Sınıf 1 |
| 5 | Look-ahead + smoothed release | Look-ahead kadar | Sert | Düşük | Orta | Hayır | Sınıf 1 |
| 6 | Multi-stage (önce yavaş) | 2× look-ahead | Sert | Düşük | Yüksek | Opsiyonel | Sınıf 2 |
| 7 | Oversampled limiter | Look-ahead + OS | OS sonrası sert | En düşük | Yüksek | Zorunlu | Sınıf 2 |
| 8 | Kombi envelope + OS | Bakınız §5 | OS sonrası sert | En düşük | Çok yüksek | Zorunlu | Sınıf 3 |
| 9 | Adaptive lookahead | Değişken | Sert | Orta | Orta | Opsiyonel | Araştırma |
| 10 | ML tahmin release | Değişken | Garanti yok | Bilinmiyor | Yüksek | Bilinmiyor | Kapsam dışı |
| 11 | Wave shaping (soft clip) | 0 örnek | Sert kademede | Orta (renk) | Düşük | Hayır | §2 komşu |
| 12 | Ceiling-ekleme (ganh headroom) | 0 örnek | Sert | Düşük | Düşük | Hayır | Yaygın |
| 13 | Delta / UPWAS türevi | Değişken | Sert | Düşük | Orta | Hayır | Literatür |
| 14 | True-peak (OS 4×) | OS kadar | Gerçek tepe | Düşük | Yüksek | Öneri | §5 |
| 15 | Dither sonrası tepe | 0 örnek | dither üstü | Bayes-noise | Düşük | Hayır | Ölçüm |

## §21 Look-ahead gecikme bütçesi tablosu

| # | Girdi | Değer | Kaynak | Bütçe etkisi | Ölçüm | Not |
|---|---|---|---|---|---|---|
| 1 | Buffer boyu | 512 örnek | sartname | Ana dilim | PLANNED | 48 kHz |
| 2 | Örnekleme | 48 kHz | sartname | Zaman = örnek/48000 | PLANNED | — |
| 3 | 512 örnek süresi | 10.67 ms | 512/48000 | Tam dilim | Hesap | Tipik üst sınır |
| 4 | Look-ahead tipik | 1-5 ms | Literatür | 48-240 örnek | VERIFICATION REQUIRED | Sınıf 1 |
| 5 | Look-ahead uzun | 10 ms | Tasarım | 480 örnek | Tasarım | Bütçeyi doldurur |
| 6 | Attack (compressor) | 0.1-10 ms | §4 | Komşu | Ölçüm PLANNED | Sıra etkisi |
| 7 | Release | 10-1000 ms | §4 | Gecikme değil | Ölçüm PLANNED | Envelope |
| 8 | OS 2× gecikme | Filtre grubu | Literatür | +0.5-2 ms | VERIFICATION REQUIRED | §5 |
| 9 | OS 4× gecikme | Filtre grubu | Literatür | +1-4 ms | VERIFICATION REQUIRED | §5 |
| 10 | LPF faz | Filtre derecesi | Tasarım | +N grup | Tasarım | Linear-phase artı |
| 11 | DelayLine kapasitesi | Tasarım | JUCE DelayLine | Tahsis baştan | Kod PLANNED | Zero-alloc |
| 12 | Convolution IR | IR uzunluğu | JUCE Convolution | IR kadar | Ölçüm | Bölüm 11 |
| 13 | Waveform gecikme | UI | progressbar.js | Görsel | Kod | Ölçüm değil |
| 14 | Pipeline toplam | Σ bloklar | pipeline | Zincir | Ölçüm PLANNED | Q-bütçe |
| 15 | Round-trip (ASIO) | Donanım | ASIO sürücüsü | Dış | Ölçüm PLANNED | K003 |
| 16 | Bütçe kararı | K004 | Tasarım dokümanı | Karar yok | UNKNOWN | §8 kısıt |

## §22 Ceiling ve headroom karar tablosu

| # | Karar | Seçenek A | Seçenek B | Seçenek C | Durum |
|---|---|---|---|---|---|
| 1 | Dijital ceiling | -0.1 dBFS | -1.0 dBFS | -0.3 dBFS | Karar yok |
| 2 | True-peak ceiling | -1.0 dBTP | -2.0 dBTP | Ölçüm yok | Q-karar |
| 3 | EBU r128 hedefi | -23 LUFS | Ölçüm yok | Ölçülecek | measurement |
| 4 | BS.1770 ölçüm | Gerekiyor | Yok | Kısmi | measurement |
| 5 | Dither | Açılır | Kapalı | 24-bit'te gerekli | Bilinmiyor |
| 6 | Yazılım çıkış | volume.js clamp | float32 devam | Koruma yok | Kod kanıtı |
| 7 | Donanım kaçış | PCM3168A | ADR-038 H001 | Koruma yok | FROZEN |
| 8 | Relay koruma | >0.5V DC | Zorunlu | Donanım | Kök kural |
| 9 | ASIO exclusive | Tek uygulama | Kilit | K003 | Kök kural |
| 10 | Buffer | 512 | Ölçüm PLANNED | sartname | Sabit |
| 11 | Oversampling oranı | 2× | 4× | 1× (hızlı) | Karar yok |
| 12 | OS filtresi | Kaiser | Butterworth | Sinc | Karar yok |
| 13 | Release şekli | Sabit | Program-bağımlı | adaptif | Karar yok |
| 14 | Look-ahead açma | Var | Yok | Kısmi | Karar yok |
| 15 | Mekanik kazanç | Kapalı | Açık (filtre) | Ölçüm yok | Karar yok |

## §23 Ölçüm ve kabul kriteri matrisi

| # | Kriter | Hedef | Yöntem | Araç | Durum |
|---|---|---|---|---|---|
| 1 | Tepe aşımı | 0 örnek > ceiling | Blok tarama | Ölçüm PLANNED | Q-ölçüm |
| 2 | True-peak | ≤ ceiling dBTP | 4× OS ölçüm | Ölçüm PLANNED | BS.1770 |
| 3 | THD+N | <0.01% @1 kHz | Saf dalga | Ölçüm PLANNED | Kök hedef |
| 4 | SNR | >100 dB | Susma girdisi | Ölçüm PLANNED | Kök hedef |
| 5 | Gecikme | ≤ bütçe | Impuls yanıtı | Ölçüm PLANNED | §21 |
| 6 | Distorsiyon (kulaç) | Ölçülecek | BS.1116 | Ölçüm PLANNED | testing |
| 7 | Alias (OS) | ≤ -90 dB | Swept-sine | Ölçüm PLANNED | §5 |
| 8 | Kod kapağı | Kare dalga korunur | Unit test | PHPUnit/Vitest | testing |
| 9 | Clamp anolojisi | Kapsanan | volume.js | Kod | §7 |
| 10 | Log-kayıp yok | Doğrulanacak | Kod incelemesi | Repo | §7 |
| 11 | Tahsis yok | Doğulanacak | audio thread profil | Profil PLANNED | L0 |
| 12 | Lock yok | Doğulanacak | Kod denetimi | Repo | L0 |
| 13 | noexcept | Zorunlu | Kod denetimi | Repo | L0 |
| 14 | Buffer 512 | Sabit | Konfigürasyon | sartname | Sabit |
| 15 | Ceiling kararı | Karar gerekli | Tasarım | K004 | UNKNOWN |

## §24 Komşu blok sırası kontrol tablosu

| # | Sıra | Blok | Gerekçe | Risk | Durum |
|---|---|---|---|---|---|
| 1 | Önce | compressor | Dinamik kısma | Çifte GR | Sıra A |
| 2 | Sonra | limiter | Tavan kilit | Gecikme | Sıra A |
| 3 | Ölçüm | measurement | Kapalı çevrim | Q4 | Ayrım yok |
| 4 | Son | pipeline | Zincir kapanışı | Bütçe | §8 |
| 5 | Eşzamanlı | dynamics | Smoothing ortak | Çakışma | dynamics |
| 6 | Öncesi | crossover | Bant SC | karma | §11 |
| 7 | Sonrası | spatial | Uzamsal tavan | Kanal | §11 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
