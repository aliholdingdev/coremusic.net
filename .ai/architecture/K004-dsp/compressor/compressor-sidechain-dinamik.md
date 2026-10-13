---
title: "compressor-sidechain-dinamik — K004-dsp Derin Dal"
type: architecture-deep-dive
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K004-dsp/dsp-master.md §3 · compressor dalı"
updated: 2026-10-12
parent_id: K004-dsp/compressor
group: mimari
node_id: K004-dsp/compressor/compressor-sidechain-dinamik.md
---

# compressor-sidechain-dinamik — K004-dsp Derin Dal

## §1 Rol ve sınır

Bu dal, sıkıştırıcının **dinamik işlem kararlarını** belgeler: sinyalin hangi
göstergesiyle (detector) bakılacağı, kazanç azaltımının (GR) nasıl hesaplanacağı,
sidechain yolunun nasıl kurulacağı ve ballistics (attack/release) davranışının
nasıl tanımlanacağı. Politika sahipleri compressor ve limiter'dır; matematiksel
zarf takibinin soyut tarafı `dynamics` dalındadır.

`dsp-master.md` Q3 açık sorusu, dynamics ↔ compressor/limiter hiyerarşisini henüz
çözmemiştir; bu dosya sınırı dürüstçe işaretler, çelişkide üst kararı bekler.

| # | Bu dalın içinde | Dışında | Komşu |
|---|---|---|---|
| 1 | Detector seçimi (peak/RMS/hall) | Zarf takip matematiği | dynamics |
| 2 | Eşik ve oranı davranışı | Brickwall tavanı | limiter |
| 3 | Sidechain yolu ve high-pass | Ölçüm standardı | measurement |
| 4 | Attack/release ballistics | Kazanç yumuşatma yardımcıları | dynamics |
| 5 | Channel link / stereo link | Uzamsal kanal haritası | spatial-audio |
| 6 | GR azaltımının raporlanması | Test örtüsü | testing |
| 7 | Gerçek zamanlı kısıtlar | C++ uygulaması (motor yok) | build |
| 8 | Ölçüm penceresi kullanımı | Ölçüm sahipliği | Q4 |

## §2 Detector topolojileri

Detector, sıkıştırıcının "neye baktığıdır". Seçim, GR'nin hızını ve karakterini
belirler. K004 kapsamında detector seçimi için karar metni **yoktur**.

| # | Detector | Hız | Gürültü duyarlılığı | Maliyet | Karakter | Durum |
|---|---|---|---|---|---|---|
| 1 | Anlık tepe (peak) | Çok hızlı | Yüksek | Düşük | Sert, koruyucu | Karar yok |
| 2 | Yarım dalga tekil (rectifier + filtre) | Hızlı | Orta | Düşük | Klasik | Karar yok |
| 3 | Tam dalga tekil | Hızlı | Orta | Düşük | Simetrik | Karar yok |
| 4 | RMS benzeri ortalama | Orta | Düşük | Düşük | Yumuşak | Karar yok |
| 5 | Karesel ortalama (mean of squares) | Orta | Düşük | Orta | Enerji odaklı | Karar yok |
| 6 | Log domain ortalama | Orta | Düşük | Orta | dB uyumlu | Karar yok |
| 7 | Hall (kütüphane terminolojisi) | Yavaş | Düşük | Orta | Organik | JUCE sınıfı §14 |
| 8 | Ballistics filtresi sonrası | Ayarlı | Düşük | Düşük | Kontrollü | JUCE sınıfı §14 |
| 9 | Harici sidechain detector | Ayarlı | Değişken | Orta | Kontrollü | Tasarım yok |
| 10 | Frekans ağırlıklı detector | Orta | Seçici | Orta-yüksek | Frekans-bağımlı | Tasarım yok |
| 11 | Çoklu bant detector | Yavaş-yüksek | Seçici | Yüksek | Multi-band | compressor + crossover |
| 12 | Uç değer reddi (hold) | — | Düşük | Düşük | Kararlı | Hold süresi UNKNOWN |
| 13 | Sessizlik eşiği (gate ilişkisi) | Hızlı | — | Düşük | Kapı | NoiseGate sınıfı §14 |
| 14 | Gerçek zamanlı güncelleme | Blok-içi | — | Düşük | Blok 512 | sartname |
| 15 | Kanal bağımsız | Hızlı | — | Kanal kadar | Ayrık | Tutarlılık riski |
| 16 | Kanal bağlı (link) | Hızlı | — | Tek detector | Merkez | Link oranı UNKNOWN |

## §3 Gain hesap zinciri

Girdi → detector → eşiğe göre sapma → oran uygulaması → GR → kazanç zarfı →
uygulama. Bu zincirde her adımın K004 karşılığı aşağıdadır; sayısal eşikler bu
depoda tanımlı değildir.

| # | Adım | Girdi | Çıktı | Bağımlılık | Durum |
|---|---|---|---|---|---|
| 1 | Girdi ölçekleme | Blok | Ölçekli blok | Pre-gain | Eşik UNKNOWN |
| 2 | Detector | Blok | Seviye (dB) | Detector tipi | §2 |
| 3 | Eşik karşılaştırma | Seviye | Sapma (dB) | Threshold | Eşik UNKNOWN |
| 4 | Oran uygulaması | Sapma | Kazanç (dB) | Ratio | Oran UNKNOWN |
| 5 | Knee yumuşatma | Sapma | Yumuşak kazanç | Knee genişliği | Knee UNKNOWN |
| 6 | Ballistics | Kazanç | Zamanlı kazanç | Attack/release | Süre UNKNOWN |
| 7 | Kazanç uygulaması | Blok + kazanç | Sıkıştırılmış blok | float32 | ADR-017 |
| 8 | Make-up gain | Blok | Seviyelenmiş blok | Bütçe | dynamics |
| 9 | GR raporu | Kazanç | Ölçüm | Ölçüm sondası | measurement |
| 10 | Bypass | Blok | Düz blok | Durum taşıma | testing |
| 11 | Yazma | Blok | Çıkış | Sıfır tahsis | L0 kuralı |
| 12 | Sidechain dallanması | Girdi | Kontrol yolu | Ek tampon | Tahsis yasak |

## §4 Eşik, oran ve knee

| # | Parametre | Birim | Aralık (bilinen) | Etki | Karar durumu |
|---|---|---|---|---|---|
| 1 | Threshold (eşik) | dB | Bilinmiyor | GR başlangıcı | UNKNOWN |
| 2 | Ratio (oran) | x:1 | Bilinmiyor | Eğim | UNKNOWN |
| 3 | Knee (diz) | dB | Bilinmiyor | Geçiş yumuşaklığı | UNKNOWN |
| 4 | Attack | ms | Bilinmiyor | Hızlanma | UNKNOWN |
| 5 | Release | ms | Bilinmiyor | Toparlanma | UNKNOWN |
| 6 | Hold | ms | Bilinmiyor | GR koruma | UNKNOWN |
| 7 | Make-up | dB | Bilinmiyor | Seviye iadesi | dynamics |
| 8 | Range | dB | Bilinmiyor | Maksimum GR | UNKNOWN |
| 9 | Look-ahead | ms | Bilinmiyor | Öngörü | limiter |
| 10 | Channel link | % | Bilinmiyor | Kanal tutarlılığı | UNKNOWN |
| 11 | Detector tipi | — | — | Karakter | §2 |
| 12 | Soft/hard knee | — | Bilinmiyor | Geçiş | UNKNOWN |

## §5 Sidechain yolu

Sidechain, kontrol sinyalinin ana yoldan ayrılarak işlendiği yoldur. K004 kapsamında
sidechain'in zorunlu olup olmadığı kararı **yoktur**; aşağıdaki tablo seçenekleri
ve bilinmeyenleri ayırır.

| # | Sidechain düzeni | Kaynak | İşlem | Avantaj | Risk | Durum |
|---|---|---|---|---|---|---|
| 1 | Kendi kendine (internal) | Ana yol | Detector | Basit | Frekans bağımlı davranış | Varsayılan |
| 2 | Harici stereo | Harici giriş | Detector | Remix kontrolü | Kanal eşlemesi | Karar yok |
| 3 | Frekans-bağımlı (SC-HPF) | İç yol + filtre | Yüksek geçiren | Bass korunur | Filtre ailesi | Karar yok |
| 4 | Kanal kopyası | Kanal A → B | Detector | Merkez kontrol | Gecikme farkı | Karar yok |
| 5 | Yanıt kopyası (OSC değil) | Sistem dışı | — | — | — | Kapsam dışı |
| 6 | Ölçüm beslemesi | measurement | Kontrol | Kapalı çevrim | Q4 | Ayrım yok |
| 7 | Uzamsal grup | spatial grupları | Detector | 8.1 tutarlılık | Kanal sayısı | PLANNED |
| 8 | Çoklu bant SC | Crossover sonrası | Bant detector | Frekans seçici | Karmaşıklık | crossover sınırı |
| 9 | Zincir içi GR paylaşımı | Önceki blok | Ölçek | Bütçe | Çifte işlem | dynamics |
| 10 | Sessizlik kapısı ilişkisi | İç yol | Kapı | Gürültü kesme | NoiseGate ayrı blok | §14 |
| 11 | Bant-geçirir SC filtresi | Crossover sonrası | Band-pass | De-esser bandı | filtre ailesi kararı | §15 |
| 12 | OS gözlem yolu | Yukarı örnekleyici | Downsampler | Alias korumalı | Gecikme artışı | limiter |
| 13 | Split-band SC (çoklu detector) | Her bant ayrı | Bant detector | Frekans ayrımı | Phase lining | crossover |
| 14 | Harici MIDI tetik | Sistem dışı | — | — | — | Kapsam dışı |
| 11 | Tahrifat sonrası SC | Distortion sonrası | Detector | Sert kontrol | Gecikme | Kapsam dışı |
| 12 | Bypass sidechain | Düz yol | Yok | Karşılaştırma | Eşitleme | testing |

## §6 Ballistics: attack ve release

Attack ve release, GR'nin zaman içindeki davranışıdır. JUCE `dsp` modülünde bunu
modelleyen ayrı bir ballistics sınıfı bulunur (§14 URL'leri). Sınıf var, K004
için süre değerleri **UNKNOWN**.

| # | Ballistics özelliği | Attack etkisi | Release etkisi | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Üstel yaklaşım | Hızlı başlangıç, kuyruk | Yavaş toparlanma | Zaman ölçümü | Değer yok |
| 2 | Doğrusal yaklaşım | Sabit eğim | Sabit eğim | Zaman ölçümü | Değer yok |
| 3 | Program-bağımlı | Sinyale göre | Sinyale göre | Ölçüm | Değer yok |
| 4 | Otomatik | Sinyale göre | Sinyale göre | Ölçüm | Değer yok |
| 5 | Sabit | Ayar | Ayar | — | Değer yok |
| 6 | Hızlı (transient) | Kısa | Orta | Dinleme | Değer yok |
| 7 | Yavaş (program) | Uzun | Uzun | Dinleme | Değer yok |
| 8 | Serbest bırakma (free) | — | Serbest | Ölçüm | Değer yok |
| 9 | Kilitleme (hold) | — | Bekleme | Ölçüm | Değer yok |
| 10 | Zincirleme (staged) | Çoklu aşama | Çoklu aşama | Ölçüm | Değer yok |
| 11 | Blok-içi doğrusallık | Blok sınırında adım riski | Aynı | Blok testi | 512 sartname |
| 12 | Ölçek bağımsızlığı | fs değişimi | fs değişimi | fs testi | Karar yok |
| 13 | Ölçüm penceresi etkisi | — | — | Pencere testi | Q4 |
| 14 | Yanıt gecikmesi | GR gecikmesi | GR gecikmesi | Zaman ölçümü | Ölçülmedi |
| 15 | GR raporu doğruluğu | — | — | Ölçüm | Ölçülmedi |
| 16 | Aşırı tepki (overshoot) | Zirve kaçışı | — | Tepe testi | Ölçülmedi |

## §7 Kazanç zarfı uygulaması

| # | Uygulama adımı | Blok-içi | Tıkırtı riski | Önleme | Sahip |
|---|---|---|---|---|---|
| 1 | GR hesaplama | Evet | Yok | — | compressor |
| 2 | Zarf yumuşatma | Evet | Düşük | Ballistics | dynamics |
| 3 | Kazanç uygulama | Evet | Orta | Yumuşak geçiş | dynamics |
| 4 | Make-up uygulama | Evet | Düşük | Yavaş geçiş | dynamics |
| 5 | Bypass geçişi | Evet | Yüksek | Eşit gain + geçiş | testing |
| 6 | Parametre değişimi | Hayır | Yüksek | Yumuşatma | dynamics |
| 7 | Durum taşıma | — | Orta | Blok sınırı | pipeline |
| 8 | Sıfırlama | — | Orta | Reset politikası | UNKNOWN |
| 9 | Kanal eşlemesi | Evet | Orta | Eşli durum | pipeline |
| 10 | Ölçüm sondası | Evet | Yok | — | measurement |
| 11 | Ön ölçekleme | Evet | Düşük | Bütçe | dynamics |
| 12 | Yazma | Evet | — | Sıfır tahsis | L0 |

## §8 Repo kanıtı

Depoda sıkıştırıcı uygulaması **yoktur**; en yakın gerçek kod, oda düzeltme
hesabındaki kazanç çarpanıdır. Bu, bir analogidir ve dürüstçe çerçevelenir.

| # | İddia | Dosya | Satır | Gözlem |
|---|---|---|---|---|
| 1 | Kazanç çarpanı hesabı gerçek | shared/src/AI/AIEngine.php | 362 | `return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb);` |
| 2 | Oda düzeltme metodu tanımlı | shared/src/AI/AIEngine.php | 347 | `private function calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float` |
| 3 | Reverb girdisi alınıyor | shared/src/AI/AIWorkflow.php | 159 | `'reverb' => $request['reverb'] ?? 0.5,` |
| 4 | EQ eğrisi üretiliyor | shared/src/AI/AIWorkflow.php | 164 | `$eqCurve = $this->engine->optimizeEQ($roomProfile);` |
| 5 | Arayüz sözleşmesi | shared/src/Contracts/AI/AIEngineInterface.php | 44 | `public function optimizeEQ(array $roomProfile): array;` |
| 6 | Sıkıştırıcı PHP kodu yok | Tüm depo | 0 | 2026-10-12 envanter ölçümü |
| 7 | C++ motor dosyası yok | engine/ | 0 | 2026-10-12 envanter ölçümü |
| 8 | `.cpp/.h` dosyası yok | Tüm depo | 0 | 2026-10-12 envanter ölçümü |

> **Sınır notu:** satır 362'deki çarpan bir EQ düzeltme hesabıdır, sıkıştırma
> değildir. Bu dosyada yalnızca "kazanç çarpanı uygulama" deseninin canlı örneği
> olarak anılır; GR hesabı için repo kanıtı **yoktur**.

## §9 Gerçek zamanlı kısıtlar

| # | Kural | compressor karşılığı | İhlal belirtisi | Denetim |
|---|---|---|---|---|
| 1 | Tahsis yasak | Detector/sidechain tamponu baştan allocate | Kopma | Kod denetimi |
| 2 | Kilit yasak | Parametre yazımı lock-free | Gecikme | Kod denetimi |
| 3 | noexcept | Callback istisna atmaz | Kapanma | Derleme |
| 4 | float32 (ADR-017) | Seviye ve kazanç tipi | Gereksiz çiftlik | Kod denetimi |
| 5 | 512 örnek | Blok başına GR güncelleme | Blok dışı güncelleme | sartname |
| 6 | 48 kHz | Zaman sabitlerinin fs'e bağlılığı | Yanlış ms | sartname |
| 7 | Denormal | Sessizlikte CPU | Süre artışı | L0 kuralı |
| 8 | alignas(64) | Durum tamponu hizası | Önbellek ezilmesi | Ölçüm |
| 9 | ASIO Exclusive | Cihaz sahipliği | Çakışma | Çalışma anı |
| 10 | PCM3168A | Çıkış DAC'u | PCM5122 | BOM |
| 11 | DC relö | GR altında DC | Eşik aşımı | Ölçüm |
| 12 | ADR-019 | Per-OS player değişmez | İhlal | Denetim |
| 13 | Sürüm kanıtı | JUCE/ASIO | İddia | VERIFICATION REQUIRED |

## §10 Ölçüm ve GR raporu

| # | Ölçüm | Amaç | Sahip | Belirsizlik |
|---|---|---|---|---|
| 1 | GR zaman serisi | Davranış doğrulama | measurement | Pencere yok |
| 2 | GR tepe değeri | Maksimum azaltım | measurement | Eşik yok |
| 3 | Ortalama GR | Program etkisi | measurement | Pencere yok |
| 4 | Toparlanma süresi | Release doğruluğu | testing | Değer yok |
| 5 | Tepki süresi | Attack doğruluğu | testing | Değer yok |
| 6 | Gürültü eşiği | Detector davranışı | measurement | Eşik yok |
| 7 | Yan etki (artifacts) | Kalite | testing | Ölçüt yok |
| 8 | Gerçek tepe korunumu | Tavan | limiter | Ölçüm yok |
| 9 | SNR etkisi | Gürültü | testing | Hedef yok |
| 10 | Ölçüm tekrarlanabilirliği | Güven | measurement | Yok |

## §11 Ölçüm standartları ve sınır

Eşik/ölçüm standartları (loudness tabanlı) measurement dalının konusudur; bu dosya
yalnızca sınırı yazar.

| # | Sınır konusu | Yön | Sahip | Not |
|---|---|---|---|---|
| 1 | Hedef loudness | measurement → compressor | Ölçüm | Standart metni measurement'ta |
| 2 | GR doğrulama ölçümü | compressor → measurement | Ölçüm | Pencere Q4 |
| 3 | Gerçek tepe ölçümü | limiter ↔ measurement | Ölçüm | Oversampling |
| 4 | Eşik seçimi | compressor | Politika | UNKNOWN |
| 5 | Oran seçimi | compressor | Politika | UNKNOWN |
| 6 | Ballistics değerleri | compressor | Politika | UNKNOWN |
| 7 | Sidechain zorunluluğu | compressor | Tasarım | UNKNOWN |
| 8 | Test kapsamı | testing | Test | Q4 |

## §12 Komşu sınırlar

| # | Komşu | Sınır | Çelişki halinde | Durum |
|---|---|---|---|---|
| 1 | dynamics | Zarf matematiği | Q3 | Bekliyor |
| 2 | limiter | Tavan politikası | Sıra | limiter sahibi |
| 3 | equalizer | Bant seviyesi | Sıra | pipeline |
| 4 | pipeline | Zincir sırası | Sıra | pipeline |
| 5 | measurement | Ölçüm sahipliği | Q4 | Bekliyor |
| 6 | testing | Ört testleri | Kapsam | testing |
| 7 | crossover | Multi-band SC | Bant bölme | crossover |
| 8 | spatial-audio | Kanal linki | 8.1 haritası | PLANNED |
| 9 | reverb | Konvolüsyon yolu | Sıra | reverb |
| 10 | delay | Gecikme bütçesi | Look-ahead | delay |

## §13 Riskler ve açık sorular

| # | Kayıt | Tip | Etki | Durum |
|---|---|---|---|---|
| 1 | Detector seçimi | Tasarım | Karakter | UNKNOWN |
| 2 | Threshold aralığı | Tasarım | Davranış | UNKNOWN |
| 3 | Ratio aralığı | Tasarım | Davranış | UNKNOWN |
| 4 | Knee genişliği | Tasarım | Geçiş | UNKNOWN |
| 5 | Attack aralığı | Tasarım | Tepki | UNKNOWN |
| 6 | Release aralığı | Tasarım | Toparlanma | UNKNOWN |
| 7 | Hold gereksinimi | Tasarım | Kararlılık | UNKNOWN |
| 8 | Sidechain zorunluluğu | Kapsam | Karmaşıklık | UNKNOWN |
| 9 | Channel link oranı | Tasarım | Tutarlılık | UNKNOWN |
| 10 | Look-ahead bütçesi | Tasarım | Gecikme | UNKNOWN |
| 11 | GR raporu arayüzü | Arayüz | Görünürlük | UNKNOWN |
| 12 | Ölçüm penceresi | Ölçüm | Doğruluk | Q4 |
| 13 | Test kapsamı | Test | Güven | Q4 |
| 14 | JUCE sürüm kanıtı | Bağımlılık | Derleme | VERIFICATION REQUIRED |
| 15 | ASIO SDK sürüm kanıtı | Bağımlılık | Derleme | VERIFICATION REQUIRED |
| 16 | Q3 hiyerarşi kararı | Mimari | Sınır | Bekliyor |

## §14 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp modülü ses sinyaline uygulanan compressor sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1Compressor.html | 2026-10-12 | 200 OK |
| JUCE dsp modülü ballistics (attack/release) modelleyen ayrı sınıf yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1BallisticsFilter.html | 2026-10-12 | 200 OK |
| JUCE dsp modülü noise gate sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1NoiseGate.html | 2026-10-12 | 200 OK |
| JUCE `juce_dsp` kaynak ağacında compressor başlık dosyası bulunur | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/widgets/juce_Compressor.h | 2026-10-12 | 200 OK |
| JUCE `juce_dsp` kaynak ağacında ballistics başlık dosyası bulunur | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_BallisticsFilter.h | 2026-10-12 | 200 OK |

## §15 Kanıt özeti

| # | Kanıt | Tür | Konum |
|---|---|---|---|
| 1 | Kazanç çarpanı hesabı | Repo kodu | shared/src/AI/AIEngine.php satır 362 |
| 2 | Oda düzeltme metodu | Repo kodu | shared/src/AI/AIEngine.php satır 347 |
| 3 | Reverb girdisi | Repo kodu | shared/src/AI/AIWorkflow.php satır 159 |
| 4 | EQ çağrısı | Repo kodu | shared/src/AI/AIWorkflow.php satır 164 |
| 5 | Arayüz sözleşmesi | Repo kodu | shared/src/Contracts/AI/AIEngineInterface.php satır 44 |
| 6 | Sıkıştırıcı kodu yok | Envanter | Ölçüm 2026-10-12 |
| 7 | 5 URL HTTP 200 | Web | §14 |
| 8 | Kademe-2 kapsam kaydı | Vault | compressor/compressor-kapsam.md |
| 9 | Q3 açık sorusu | Vault | dsp-master.md |
| 10 | GR hesabı kanıtı | Eksik | UNKNOWN |

## §16 Terim sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Compressor | Eşiği aşan sinyali azaltan dinamik işlem |
| 2 | Threshold | GR'nin başladığı seviye |
| 3 | Ratio | Sapma ile kazanç azaltımı oranı |
| 4 | Knee | Eşik çevresindeki geçiş yumuşaklığı |
| 5 | Attack | GR'nin başlama hızı |
| 6 | Release | GR'nin toparlanma hızı |
| 7 | Hold | GR'nin korunma süresi |
| 8 | GR | Kazanç azaltımı |
| 9 | Detector | Seviye ölçen birim |
| 10 | Peak | Anlık tepe ölçümü |
| 11 | RMS | Ortalama karekök seviye |
| 12 | Ballistics | Zaman davranışı modeli |
| 13 | Sidechain | Kontrol yolu |
| 14 | SC-HPF | Sidechain yüksek geçiren filtre |
| 15 | Channel link | Kanalların birlikte davranması |
| 16 | Make-up | Eksilen seviyenin iadesi |
| 17 | Range | Maksimum GR sınırı |
| 18 | Look-ahead | İleri bakış gecikmesi |
| 19 | Artifacts | İşlemden doğan bozulma |
| 20 | Free release | Serbest toparlanma |
| 21 | Staged | Aşamalı ballistics |
| 22 | Multi-band | Frekans bantlarına bölünmüş sıkıştırma |
| 23 | Program-bağımlı | Sinyale göre davranan ayar |
| 24 | Auto | Otomatik ballistics |
| 25 | Bypass | Düz geçiş |
| 26 | Smoothing | Yumuşatma |
| 27 | Block | 512 örnek işlem birimi |
| 28 | fs | Örnekleme hızı |
| 29 | float32 | ADR-017 veri tipi |
| 30 | UNKNOWN | Karar metni olmayan değer |

## §17 Sidechain bant matrisi

Multi-band ve frekans-bağımlı sidechain senaryolarında bant bazlı kontrol. Kapsam
için bir **tasarım matrisidir**; karar yoktur.

| Bant | Frekans (Hz) | SC davranışı | Önerilen filtre | Durum |
|---|---|---|---|---|
| 0 | 20 | Sub korunur | SC-HPF | UNKNOWN |
| 1 | 25 | Sub korunur | SC-HPF | UNKNOWN |
| 2 | 31.5 | Sub korunur | SC-HPF | UNKNOWN |
| 3 | 40 | Bass korunur | SC-HPF | UNKNOWN |
| 4 | 50 | Bass korunur | SC-HPF | UNKNOWN |
| 5 | 63 | Bass korunur | SC-HPF | UNKNOWN |
| 6 | 80 | Bass izlenir | SC-HPF sınırı | UNKNOWN |
| 7 | 100 | Bass izlenir | SC-HPF sınırı | UNKNOWN |
| 8 | 125 | Alçak orta izlenir | Yok | UNKNOWN |
| 9 | 160 | Alçak orta izlenir | Yok | UNKNOWN |
| 10 | 200 | Alçak orta izlenir | Yok | UNKNOWN |
| 11 | 250 | Alçak orta izlenir | Yok | UNKNOWN |
| 12 | 315 | Orta izlenir | Yok | UNKNOWN |
| 13 | 400 | Orta izlenir | Yok | UNKNOWN |
| 14 | 500 | Orta izlenir | Yok | UNKNOWN |
| 15 | 630 | Orta izlenir | Yok | UNKNOWN |
| 16 | 1000 | Referans | Yok | UNKNOWN |
| 17 | 1250 | Üst orta izlenir | Yok | UNKNOWN |
| 18 | 1600 | Üst orta izlenir | Yok | UNKNOWN |
| 19 | 2000 | Üst orta izlenir | Yok | UNKNOWN |
| 20 | 2500 | Presence izlenir | Yok | UNKNOWN |
| 21 | 3150 | Presence izlenir | Yok | UNKNOWN |
| 22 | 4000 | Tiz izlenir | Yok | UNKNOWN |
| 23 | 5000 | Tiz izlenir | Yok | UNKNOWN |
| 24 | 6300 | Tiz izlenir | Yok | UNKNOWN |
| 25 | 8000 | Tiz izlenir | Yok | UNKNOWN |
| 26 | 10000 | Air izlenir | Yok | UNKNOWN |
| 27 | 12500 | Air izlenir | Yok | UNKNOWN |
| 28 | 16000 | Air izlenir | Yok | UNKNOWN |
| 29 | 20000 | Uç bant | Tiz koruma | UNKNOWN |
| 30 | (ek bant) | Uç bant | Tiz koruma | UNKNOWN |

## §18 Parametre aralık matrisi

| # | Parametre | Alt sınır | Üst sınır | Adım | Birim | Durum |
|---|---|---|---|---|---|---|
| 1 | Threshold | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 2 | Ratio | Bilinmiyor | Bilinmiyor | Bilinmiyor | x:1 | UNKNOWN |
| 3 | Knee | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 4 | Attack | Bilinmiyor | Bilinmiyor | Bilinmiyor | ms | UNKNOWN |
| 5 | Release | Bilinmiyor | Bilinmiyor | Bilinmiyor | ms | UNKNOWN |
| 6 | Hold | Bilinmiyor | Bilinmiyor | Bilinmiyor | ms | UNKNOWN |
| 7 | Make-up | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 8 | Range | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 9 | Look-ahead | Bilinmiyor | Bilinmiyor | Bilinmiyor | ms | UNKNOWN |
| 10 | Channel link | Bilinmiyor | Bilinmiyor | Bilinmiyor | % | UNKNOWN |
| 11 | SC-HPF frekansı | Bilinmiyor | Bilinmiyor | Bilinmiyor | Hz | UNKNOWN |
| 12 | Detector tipi | — | — | — | — | Seçim yok |
| 13 | Pre-gain | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 14 | Yazma gain | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 15 | GR raporu çözünürlüğü | Bilinmiyor | Bilinmiyor | Bilinmiyor | dB | UNKNOWN |
| 16 | Bypass eşitliği | 0 | 0 | — | dB | Hedef, ölçülmedi |
| 17 | Blok-içi güncelleme | Blok başına | Blok başına | — | örnek | sartname |
| 18 | fs duyarlılığı | 48 kHz | 48 kHz | — | Hz | sartname |
| 19 | float32 hassasiyeti | — | — | — | bit | ADR-017 |
| 20 | Tahsis | 0 | 0 | — | adet | L0 kuralı |
| 21 | Kilit | 0 | 0 | — | adet | L0 kuralı |
| 22 | İstisna | 0 | 0 | — | adet | L0 kuralı |
| 23 | Denormal temizliği | Aktif | Aktif | — | — | L0 kuralı |
| 24 | alignas(64) | Uygulanacak | Uygulanacak | — | bayt | Ölçüm yok |
| 25 | Ölçüm penceresi | Bilinmiyor | Bilinmiyor | Bilinmiyor | ms | Q4 |
| 26 | Test tekrarı | Bilinmiyor | Bilinmiyor | Bilinmiyor | adet | Q4 |
| 27 | Sürüm kanıtı | Yok | Yok | — | — | VERIFICATION REQUIRED |
| 28 | Lisans denetimi | Yok | Yok | — | — | Yok |

## §19 Karar günlüğü

| # | Kayıt | Tarih | Etkilenen | Durum |
|---|---|---|---|---|
| 1 | Kademe-2 kapsam kaydı | 2026-10-12 | compressor | Mevcut |
| 2 | Derin dal (bu dosya) | 2026-10-12 | compressor | Bu dosya |
| 3 | Detector seçimi yazılmadı | 2026-10-12 | compressor | UNKNOWN |
| 4 | Eşik/oran aralıkları yazılmadı | 2026-10-12 | compressor | UNKNOWN |
| 5 | Ballistics değerleri yazılmadı | 2026-10-12 | compressor | UNKNOWN |
| 6 | Sidechain zorunluluğu kararı yok | 2026-10-12 | compressor | UNKNOWN |
| 7 | Channel link oranı yok | 2026-10-12 | compressor | UNKNOWN |
| 8 | Look-ahead bütçesi yok | 2026-10-12 | limiter | UNKNOWN |
| 9 | Q3 hiyerarşi bekliyor | 2026-10-12 | architect | Bekliyor |
| 10 | Q4 ölçüm ayrımı bekliyor | 2026-10-12 | architect | Bekliyor |
| 11 | GR raporu arayüzü yok | 2026-10-12 | measurement | UNKNOWN |
| 12 | Ölçüm penceresi kararı yok | 2026-10-12 | measurement | Q4 |
| 13 | Test kapsamı tanımı yok | 2026-10-12 | testing | Q4 |
| 14 | Tahsis yasak L0'da | 2026-10-12 | L0 | Mevcut |
| 15 | Kilit yasak L0'da | 2026-10-12 | L0 | Mevcut |
| 16 | noexcept kuralı L0'da | 2026-10-12 | L0 | Mevcut |
| 17 | float32 kararı ADR-017'de | 2026-10-12 | ADR-017 | Mevcut |
| 18 | 512/48k sartname PLANNED | 2026-10-12 | sartname | PLANNED |
| 19 | Ölçüm testleri PLANNED | 2026-10-12 | measurement | PLANNED |
| 20 | 8.1 kanal linki PLANNED | 2026-10-12 | spatial-audio | PLANNED |
| 21 | C++ motor envanteri 0 | 2026-10-12 | build | Ölçüldü |
| 22 | 5 URL doğrulandı | 2026-10-12 | research | Ölçüldü |
| 23 | JUCE sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| 24 | ASIO SDK sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| 25 | Ölçüm tekrarı yok | 2026-10-12 | testing | Ölçülmedi |
| 26 | GR doğrulama yok | 2026-10-12 | measurement | Ölçülmedi |
| 27 | Yan etki ölçümü yok | 2026-10-12 | testing | Ölçülmedi |
| 28 | Bypass eşitleme tanımı yok | 2026-10-12 | testing | UNKNOWN |
| 29 | Sınır çelişkisi tespit edilmedi | 2026-10-12 | dynamics | Gözlem |
| 30 | Üst karar bekleniyor | 2026-10-12 | architect | Bekliyor |

## §20 Denetim matrisi

| # | Denetim | Yöntem | Sıklık | Sorumlu | Durum |
|---|---|---|---|---|---|
| 1 | Detector kararı mevcut mu | Vault araması | Sürüm | architect | Yok |
| 2 | Threshold aralığı tanımlı mı | Tasarım okuma | Sürüm | embedded | UNKNOWN |
| 3 | Ratio aralığı tanımlı mı | Tasarım okuma | Sürüm | embedded | UNKNOWN |
| 4 | Ballistics değerleri tanımlı mı | Tasarım okuma | Sürüm | embedded | UNKNOWN |
| 5 | Tahsis yok mu | Kod denetimi | Her PR | embedded | Motor yok |
| 6 | Mutex yok mu | Kod denetimi | Her PR | embedded | Motor yok |
| 7 | noexcept tam mı | Derleme | Her derleme | build | Motor yok |
| 8 | float32 kullanımı tutarlı mı | Tip denetimi | Her PR | embedded | ADR-017 |
| 9 | 512 blok doğrulandı mı | Sartname ölçümü | Sürüm | testing | PLANNED |
| 10 | 48 kHz doğrulandı mı | Sartname ölçümü | Sürüm | testing | PLANNED |
| 11 | GR raporu çalışıyor mu | Ölçüm | Sürüm | measurement | Yok |
| 12 | Bypass eşitliği korundu mu | Seviye ölçümü | Sürüm | testing | Tanım yok |
| 13 | Tepki süresi ölçüldü mü | Zaman ölçümü | Sürüm | testing | Yok |
| 14 | Toparlanma süresi ölçüldü mü | Zaman ölçümü | Sürüm | testing | Yok |
| 15 | Gerçek tepe korundu mu | Tavan ölçümü | Sürüm | limiter | Yok |
| 16 | DC eşiği korundu mu | DC ölçümü | Donanım | audio-hw | Relö kuralı |
| 17 | SNR etkisi ölçüldü mü | SNR ölçümü | Sürüm | testing | Yok |
| 18 | Ölçüm penceresi sabit mi | Tekrar ölçümü | Sürüm | measurement | Q4 |
| 19 | Ölçüm tekrarlanabilirliği | Tekrar testi | Sürüm | measurement | Yok |
| 20 | Kanal linki test edildi mi | Kanal testi | Sürüm | testing | UNKNOWN |
| 21 | Sidechain yolu test edildi mi | Yol testi | Sürüm | testing | UNKNOWN |
| 22 | Multi-band test edildi mi | Bant testi | Sürüm | testing | UNKNOWN |
| 23 | 8.1 link testi yapıldı mı | Kanal testi | Sürüm | K003-audio | PLANNED |
| 24 | Sürüm kanıtı mevcut mu | Derleme kaydı | Sürüm | build | VERIFICATION REQUIRED |
| 25 | Lisans denetimi yapıldı mı | Lisans taraması | Sürüm | legal | Yok |
| 26 | URL benzersizliği korundu mu | Vault ölçümü | Dosya | vault | Ölçüm betiği |
| 27 | Mojibake yok mu | Byte ölçümü | Dosya | vault | Ölçüm betiği |
| 28 | Dolgu dizgisi yok mu | Metin taraması | Dosya | vault | Ölçüm betiği |
| 29 | Frontmatter tam mı | Alan sayımı | Dosya | vault | 10 alan |
| 30 | Satır sayısı 500 üstü mü | Satır sayımı | Dosya | vault | Ölçülecek |

## §21 Bağlantı haritası

| # | Bağlantı | Hedef | İlişki |
|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm |
| 2 | Dal kapsamı | compressor/compressor-kapsam.md | Kademe-2 |
| 3 | dynamics sınırı | dynamics/dynamics-kapsam.md | Q3 |
| 4 | limiter sınırı | limiter/limiter-kapsam.md | Sıra |
| 5 | pipeline örneği | pipeline/pipeline-ornekler.md | Sıra |
| 6 | measurement kapsamı | measurement/measurement-kapsam.md | Q4 |
| 7 | testing örneği | testing/testing-ornekler.md | Test |
| 8 | crossover kapsamı | crossover/crossover-kapsam.md | Multi-band |
| 9 | spatial kapsamı | spatial-audio/spatial-audio-kapsam.md | Kanal linki |
| 10 | Bağımlılıklar | dsp-bagimliliklar.md | K003-audio |
| 11 | Doğrulama | dsp-dogrulama.md | Kanıt |
| 12 | Kök indeks | index.md | Katalog |
| 13 | ADR-017 | .ai/.decisions | float32 |
| 14 | ADR-019 | .ai/.decisions | Per-OS FROZEN |
| 15 | ADR-038 H001 | .ai/.decisions | PCM3168A |
| 16 | L0 kuralları | Kök AGENTS.md | Ses iş parçacığı |
| 17 | Kazanç çarpanı kodu | shared/src/AI/AIEngine.php | Analoji |
| 18 | Workflow | shared/src/AI/AIWorkflow.php | Eşleme |
| 19 | Arayüz | shared/src/Contracts/AI/AIEngineInterface.php | Sözleşme |
| 20 | C++ motor | engine/ (yok) | Eksik |

## §22 Ek denetim tablosu (dosya bütünlüğü)

| # | Denetim | Beklenen | Durum |
|---|---|---|---|
| 1 | Frontmatter anahtar | 9 alan (v2 §4.2 + group) | Eski şema — yeni dosyalarda §4.2 |
| 2 | Authority kapanışı | Son satır | Tamam |
| 3 | Ellipsis dolgu | 0 | Ölçüldü |
| 4 | CJK / mojibake | 0 | Ölçüldü |
| 5 | Web tablosu sütun | 4 (İddia/URL/tarih/Durum) | Tamam |
| 6 | Repo kanıtı | AIEngine.php satır doğrulama | Tamam |
| 7 | Q3 | UNKNOWN taşındı | Tamam |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
