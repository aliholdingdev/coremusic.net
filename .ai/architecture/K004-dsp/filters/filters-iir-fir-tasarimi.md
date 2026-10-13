---
title: "filters-iir-fir-tasarimi — K004-dsp Derin Dal"
type: architecture-deep-dive
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K004-dsp/dsp-master.md §3 · filters dalı"
updated: 2026-10-12
parent_id: K004-dsp/filters
group: mimari
node_id: K004-dsp/filters/filters-iir-fir-tasarimi.md
---

# filters-iir-fir-tasarimi — K004-dsp Derin Dal

## §1 Rol ve sınır

Bu dal, K004 DSP motorunun **filtre çekirdeği** tasarımını belgeler: IIR ve FIR
ailelerinin seçimi, katsayıların üretilmesi, kararlılığın korunması ve gerçek
zamanlı bir blok işleme hattında filtre bloğunun maliyeti. `equalizer` dalı bant
seviyesini (kaç bant, hangi frekans) yönetir; buradaki iş bant **içindeki**
matematiktir.

Sınır kaydı (`equalizer-kapsam.md` §2) bu ayrımı "bant kazancı EQ'nun, filtre
çekirdeği filters'ın" diye yazar. Bu dosya çekirdek tarafını derinleştirir, bant
kazancı politikasına girmez.

| # | Bu dalın içinde | Dışında | Komşu |
|---|---|---|---|
| 1 | IIR/FIR çekirdek seçimi | Bant sayısı ve yerleşimi | equalizer |
| 2 | Katsayı üreteç ailesi | Kullanıcı preset'i | equalizer |
| 3 | Kararlılık ve kuantizasyon | Ölçüm hedef eğrisi | measurement |
| 4 | Gecikme ve faz karakteri | Zincir sırası kararı | pipeline |
| 5 | Gain bütçesi (çekirdek düzeyi) | Dinamik kazanç takibi | dynamics |
| 6 | Çoklu örnek hızı duyarlılığı | Brickwall tavanı | limiter |
| 7 | Denormal ve tahsis kuralları | Test örtüsü | testing |
| 8 | JUCE çekirdek referansları | Donanım EQ'su | K003-audio |

## §2 IIR tarafı: biquad ve üstü yapılar

IIR filtreler geri beslemeli yapılarıdır; az sayıda katsayı ile güçlü tepki
üretirler, ancak kararlılık ve faz davranışı tasarım disiplini ister. K004 kapsamındaki
IIR kararları için bu depoda karar metni yoktur; aşağıdaki tablo seçenekleri ve
bilinmeyenleri ayırır.

| # | Yapı | Durum sayısı | Gecikme | Maliyet | Kararlılık riski | K004 notu |
|---|---|---|---|---|---|---|
| 1 | Tek biquad (2. derece) | 2 | Düşük | Düşük | Orta | Parametrik EQ'nun doğal bloğu |
| 2 | Kaskad biquad (N × 2) | 2N | Düşük | Düşük-N | Orta-yüksek | Yüksek sıra için aday |
| 3 | Paralel biquad | 2N | Düşük | Düşük-N | Orta | Bant EQ'suna doğal |
| 4 | Direkt 4. derece | 4 | Düşük | Düşük | Yüksek | Hassasiyet riski |
| 5 | Direkt 8. derece | 8 | Düşük | Orta | Çok yüksek | Kuantizasyon riski |
| 6 | Lattice/ladder IIR | N | Düşük | Orta | Düşük-yüksek | Parametre duyarlılığı avantajlı |
| 7 | State-variable (tekil) | 2 | Orta | Düşük | Düşük | Çoklu çıkış avantajı |
| 8 | TPT state-variable | 2 | Orta | Düşük | Düşük | Yüksek fs kararlılığı |
| 9 | Ladder (5. dereceye kadar) | 5 | Orta | Orta | Orta | Karakter filtresi |
| 10 | All-pass kaskad | 2N | Değişken | Orta | Orta | Faz düzeltme, kapsam kararı yok |
| 11 | Katsayı yeniden yazımı (transpoz) | 2 | Düşük | Düşük | Orta | Durum değişimi azaltır |
| 12 | float32 katsayı | 2 | Düşük | Düşük | Düşük-yüksek | ADR-017 |
| 13 | float64 katsayı | 2 | Düşük | Orta | Düşük | Gerekliliği ölçülmedi |
| 14 | fs duyarlı yeniden hesap | — | — | Başlangıç | — | fs değişimi kararı yok |
| 15 | Blok boyu duyarlılığı | — | — | Blok içi | — | 512 sartname |
| 16 | Durum sıfırlama (reset) | 2 | Yok | Düşük | — | Bypass geçişlerinde gerekli |

## §3 FIR tarafı: pencereleme ve gecikme

FIR filtreler geri beslemesizdir; kararlılık garantilidir, ancak hedef tepkiye
ulaşmak için uzun katsayı dizileri ve sabit gecikme taşır. K004'ün 512 örnek
tampon hedefi FIR uzunluğunu doğrudan sınırlar.

| # | Özellik | FIR | IIR (karşılaştırma) | K004 etkisi | Durum |
|---|---|---|---|---|---|
| 1 | Kararlılık | Koşulsuz | Koşullu | FIR avantajı | Teorik |
| 2 | Faz | Doğrusal yapılabilir | Doğrusal değil | Gecikme bedeli | Tasarım tercihi |
| 3 | Gecikme | Sabit, uzunluk/2 | Düşük, değişken | Bütçe kısıtı | Bütçe UNKNOWN |
| 4 | Katsayı sayısı | 100-1000 arası tipik | 3-20 | Maliyet farkı | Ölçüm yok |
| 5 | Uygulama maliyeti | Konvolüsyon | Rekürsif | CPU farkı | Ölçüm yok |
| 6 | Tasarım yöntemi | Pencereleme, optimal | Analitik | Araç yok | Araç kararı yok |
| 7 | Pencere türü | Rectangular/Hann/Blackman vb. | Yok | Tercih UNKNOWN | Seçim yok |
| 8 | Bant geçiş düzgünlüğü | Pencereye bağlı | Tasa | Kalite ölçütü | Ölçüm yok |
| 9 | Durum belleği | Katsayı kadar | 2 per bant | RAM etkisi | Ölçüm yok |
| 10 | Adaptasyon | Zor | Kolay | Oda düzeltme | measurement sınırı |
| 11 | Konvolüsyon kullanımı | IR ile doğrudan | — | Reverb/ölçüm | reverb dalı |
| 12 | Blok işleme uyumu | FFT konvolüsyon | Blok-içi | Blok 512 | 512 sartname |
| 13 | Gerçek zamanlı değiştirme | Tıkırtı riski | Yumuşatma kolay | Smoothing | dynamics sınırı |
| 14 | Örnekleme hızı duyarlılığı | Tam yeniden tasarım | Yeniden hesap | fs değişimi | Karar yok |
| 15 | Toplam gain | Normalize edilir | Blok kazancı | Bütçe | dynamics |
| 16 | Kapsam durumu | Alternatif, karar yok | Varsayılan aday | — | UNKNOWN |

## §4 Katsayı üreteç aileleri

Katsayı üretimi, filtre tepkisinin belirlendiği adımdır. JUCE `juce_dsp` ağacında
`filter_design/juce_FilterDesign.h` adlı bir tasarım kaynağı bulunur (§13 URL'si);
CCRMA yayınları temel filtre teorisini anlatır. K004 kapsamında hangi ailenin
seçildiğine dair karar metni **yoktur**.

| # | Aile | Karakter | Tipik kullanım | Hassasiyet | K004 durumu |
|---|---|---|---|---|---|
| 1 | Butterworth benzeri düz tepki | Maksimum düz genlik | Genel amaçlı | Orta | Karar yok |
| 2 | Chebyshev benzeri dalgalı | Daha dik geçiş | Dar geçiş | Yüksek | Karar yok |
| 3 | Eliptik benzeri | En dik geçiş | Sert bastırma | Çok yüksek | Karar yok |
| 4 | Bessel benzeri | Faz düzgünlüğü | Faz hassas | Orta | Karar yok |
| 5 | Raised-cosine pencere | FIR geçiş | Yayılım kontrolü | Düşük | FIR dalında |
| 6 | Kaiser pencere | FIR ayarlanabilir | Genel FIR | Orta | FIR dalında |
| 7 | Optimal (Parks-McClellan benzeri) | Eş dalgalı FIR | Mastering | Düşük | Karar yok |
| 8 | Shelving eşleniği | Uç bant | EQ | Orta | equalizer sınırı |
| 9 | Peaking eşleniği | Tepe bant | EQ | Orta | equalizer sınırı |
| 10 | Notch eşleniği | Dar çentik | Tek frekans | Yüksek | equalizer sınırı |
| 11 | Katsayı yuvarlama | float32 sınırlaması | Tüm aileler | Yüksek | ADR-017 |
| 12 | Normalizasyon | DC/Nyquist ölçeği | Tüm aileler | Orta | Ölçüm yok |
| 13 | Kutup kontrolü | Birim daire içi | IIR | Orta-yüksek | Ölçüm yok |
| 14 | Yeniden tasarım (fs değişimi) | Tüm aileler | Çoklu hız | Yüksek | Karar yok |
| 15 | Lisans/ekipman bağımlılığı | Araç seçimi | Üretim | — | Lisans kontrolü yok |
| 16 | Doğrulama yöntemi | Tepki ölçümü | Tüm aileler | — | testing kapsamında |

## §5 TPT ve birinci derece bloklar

Topology-preserving transform (TPT) tabanlı yapılar, örneklemeye oranla kesim
frekansı yükseldikçe davranışın bozulmasını azaltır. K004 hedefi 48 kHz ve 512
örnek blok olduğundan, bu ailenin avantajı ancak ölçümle doğrulanabilir.

| # | Blok | Sıra | Frekans kontrolü | Kazanç kontrolü | Durum |
|---|---|---|---|---|---|
| 1 | TPT low-pass | 1 | Var | Var | JUCE sınıfı §13'te |
| 2 | TPT high-pass | 1 | Var | Var | JUCE sınıfı §13'te |
| 3 | TPT band-pass | 1 | Var | Var | JUCE sınıfı §13'te |
| 4 | TPT notch | 1 | Var | Var | JUCE sınıfı §13'te |
| 5 | Birinci derece alçak geçiren | 1 | Var | Yok | Ayrı sınıf §13 |
| 6 | Birinci derece yüksek geçiren | 1 | Var | Yok | Ayrı sınıf §13'te |
| 7 | Birinci derece shelf | 1 | Var | Var | Katsayı eşlemesi UNKNOWN |
| 8 | Kaskad birinci derece | N | Her biri ayrı | Her biri ayrı | Toplam gecikme UNKNOWN |
| 9 | Ölçekleme (pre-gain) | 0 | Yok | Var | dynamics sınırı |
| 10 | Ölçüm sondası | 0 | Yok | Yok | measurement sınırı |
| 11 | Durum yazımı (state write) | — | — | — | Sıfır tahsis kuralı |
| 12 | Paralel çıkış (multi-out) | — | Var | Var | Bant EQ için uygun |

## §6 Kararlılık ve kuantizasyon

| # | Risk | Belirti | Ölçüt | Önleme | Sahip |
|---|---|---|---|---|---|
| 1 | Kutup birimi dışına çıkış | Üfürme/titreme | Birim daire testi | Tasarım sınırı | filters |
| 2 | Katsayı yuvarlama kayması | Frekans kayması | Tepki ölçümü | float32 yeterlilik | filters |
| 3 | Yüksek sıra hassasiyeti | Sessizde titreşim | Durum genliği | Sıra sınırı | filters |
| 4 | Denormal yavaşlama | CPU artışı | Süre ölçümü | Denormal temizleme | L0 kuralı |
| 5 | Durum taşması | Kırpılma | Durum genliği | Ön ölçekleme | filters |
| 6 | fs değişimi | Yanlış kesim | Frekans ölçümü | Yeniden hesap | build |
| 7 | Blok boyu değişimi | Durum kayması | Süre testi | Sabit blok | pipeline |
| 8 | Bypass tıklaması | Gürültü | A/B testi | Durum taşıma/ sıfırlama | testing |
| 9 | Kanal ayrışması | Stereo kayması | Kanal karşılaştırma | Eşli durum | pipeline |
| 10 | Tahsis ihlali | Kopma | Kod denetimi | Baştan tahsis | L0 kuralı |
| 11 | Kilit ihlali | Gecikme | Kod denetimi | Lock-free | L0 kuralı |
| 12 | İstisna ihlali | Kapanma | Derleme | noexcept | L0 kuralı |
| 13 | Ölçüm uyuşmazlığı | Sapma | FFT ölçümü | Pencere eşleme | measurement |
| 14 | Gain bütçesi aşımı | Kırpılma | Seviye kaydı | Bütçe tablosu | dynamics |
| 15 | Gürültü tabanı | SNR düşüşü | SNR ölçümü | float32 | ADR-017 |
| 16 | Sıfırlama gereksinimi | Geçiş gürültüsü | Durum testi | Reset politikası | UNKNOWN |

## §7 Ölçek, normalizasyon ve gain bütçesi

| # | Ölçek adımı | Amaç | Risk | Kural | Durum |
|---|---|---|---|---|---|
| 1 | Pre-gain | Girdiyi uygun seviyeye çekmek | Erken kırpılma | Bütçe içinde | Eşik UNKNOWN |
| 2 | Katsayı normalizasyonu | DC/Nyquist ölçeği | Tepki bozulması | Tasarım adımı | Ölçüm yok |
| 3 | Bant kazancı | EQ zirvesi | Toplam aşım | Bant toplamı | equalizer |
| 4 | Zincir kazancı | Blok arası seviye | Aşım | Bütçe kaydı | pipeline |
| 5 | Yazma öncesi ölçek | Çıkış seviyesi | Tavan | brickwall | limiter |
| 6 | float32 aralığı | ±3.4e38 sınırı | Aşım | Nadir | ADR-017 |
| 7 | Denormal eşiği | CPU koruması | Gürültü | Temizleme | L0 kuralı |
| 8 | Kanal dengesi | Stereo tutarlılık | Kayma | Eşli işleme | pipeline |
| 9 | DC bileşeni | Hoparlör koruma | Eşik | DC ölçümü | audio-hw |
| 10 | True peak | Örnekleme-arası tepe | Aşım | Oversampling ölçüm | limiter |
| 11 | Gain smearing | Yumuşak geçiş | Tıkırtı | Smoothing | dynamics |
| 12 | Bypass eşitleme | A/B güvenilirliği | Yanlış karşılaştırma | Eşit gain | testing |

## §8 Repo kanıtı: bant-frekans eşlemesi

Filtre tasarımının K004'teki tek canlı bağlantısı, bant indeksinin frekansa
çevrilmesidir. Bu eşleme, EQ bandının hangi merkez frekansında bir çekirdek
isteyeceğini belirler.

| # | İddia | Dosya | Satır | Gözlem |
|---|---|---|---|---|
| 1 | `bandToFrequency` metodu tanımlı | shared/src/AI/AIEngine.php | 332 | `private function bandToFrequency(int $band): float` |
| 2 | Tablo 20 Hz'ten başlar | shared/src/AI/AIEngine.php | 336 | İlk satır 20, 25, 31.5 değerlerini taşır |
| 3 | Tablo 20000 Hz'te biter | shared/src/AI/AIEngine.php | 338 | Son satır 20000 ile kapanır |
| 4 | Döngü 31 banttır | shared/src/AI/AIEngine.php | 119 | `for ($band = 0; $band < 31; $band++)` |
| 5 | Eşleme çağrısı döngü içinde | shared/src/AI/AIEngine.php | 120 | `$frequency = $this->bandToFrequency($band);` |
| 6 | Q frekansa bağlı hesaplanır | shared/src/AI/AIEngine.php | 122 | `calculateBandQ($frequency, $roomSize)` |
| 7 | Arayüzde sözleşme var | shared/src/Contracts/AI/AIEngineInterface.php | 44 | `optimizeEQ(array $roomProfile): array` |
| 8 | Workflow çağrısı var | shared/src/AI/AIWorkflow.php | 164 | `$eqCurve = $this->engine->optimizeEQ($roomProfile);` |
| 9 | C++ çekirdek dosyası yok | engine/ dizini | 0 | 2026-10-12 envanter ölçümü |
| 10 | `.cpp/.h` dosyası yok | Tüm depo | 0 | 2026-10-12 envanter ölçümü |

## §9 Gerçek zamanlı kısıtlar

| # | Kural | Filtre çekirdeği karşılığı | İhlal belirtisi | Denetim |
|---|---|---|---|---|
| 1 | Tahsis yasak | Katsayı/durum baştan allocate | Kopma | Kod inceleme |
| 2 | Kilit yasak | Parametre geçişi lock-free | Gecikme | Kod inceleme |
| 3 | noexcept | Callback istisna atmaz | Kapanma | Derleme |
| 4 | constexpr | Katsayı tabloları derleme anında | Çalışma anı tahsisi | Kod inceleme |
| 5 | alignas(64) | Durum tamponu hizası | Önbellek ezilmesi | Ölçüm |
| 6 | float32 (ADR-017) | Durum ve katsayı tipi | Gereksiz çiftlik | Kod inceleme |
| 7 | 512 örnek | Blok işleme boyu | Ölçüm dışı | sartname |
| 8 | 48 kHz | Örnekleme hızı sabiti | fs değişimi | sartname |
| 9 | ASIO Exclusive | Cihaz sahipliği | Çakışma | Çalışma anı |
| 10 | PCM3168A | Çıkış DAC'u | PCM5122 kullanımı | BOM |
| 11 | DC offset relö | DC bileşeni koruması | Eşik aşımı | Ölçüm |
| 12 | ADR-019 FROZEN | OS player değişmezliği | İhlal | Denetim |
| 13 | Sürüm kanıtı | JUCE/ASIO sürümü | İddia | VERIFICATION REQUIRED |

## §10 Gecikme ve faz karakteri

| # | Gecikme kaynağı | Miktar | Değişir mi | K004 etkisi | Durum |
|---|---|---|---|---|---|
| 1 | Biquad blok-içi | 0 örnek varsayımı | Hayır | Kabul edilebilir | Ölçülmedi |
| 2 | FIR katsayı gecikmesi | (N-1)/2 örnek | N ile | Büyük | FIR seçili değil |
| 3 | FFT blok gecikmesi | Pencere kadar | Pencere ile | Büyük | Ölçüm dalı |
| 4 | Lookahead tamponu | Ayar kadar | Ayar ile | Orta | limiter |
| 5 | Konvolüsyon partition | Bölme ile | Bölme ile | Orta-yüksek | reverb |
| 6 | Ölçüm penceresi | Pencere kadar | Pencere ile | Ölçüm yolu | measurement |
| 7 | OSCP/örtüşme | Blok-içi | Hayır | Az | pipeline |
| 8 | Grup gecikmesi (f0 çevresi) | Frekans bağımlı | f0 ile | Faz kayması | Ölçüm yok |
| 9 | Kanal farkı | 0 hedefi | — | Tutarlılık | pipeline |
| 10 | Bypass farkı | 0 hedefi | — | A/B güveni | testing |

## §11 Komşu sınırlar

| # | Komşu | Sınır | Çelişki halinde | Sahip |
|---|---|---|---|---|
| 1 | equalizer | Bant seviyesi ↔ çekirdek | Q3 üst karar | architect |
| 2 | pipeline | Sıra ve blok sahibi | Sıra kararı | pipeline |
| 3 | dynamics | Kazanç zarfı | Smoothing kimde | Q3 |
| 4 | compressor | GR politikası | Bütçe paylaşımı | compressor |
| 5 | limiter | Tavan | Ölçüm vs sınır | limiter |
| 6 | reverb | Konvolüsyon | FIR kullanımı | reverb |
| 7 | crossover | Bant bölme | Filtre ailesi | crossover |
| 8 | measurement | Ölçüm hedefi | Pencere | Q4 |
| 9 | testing | Ört testleri | Kapsam | Q4 |
| 10 | spatial-audio | Uzamsal işleme | Filtre kullanımı | spatial |

## §12 Riskler ve açık sorular

| # | Kayıt | Tip | Etki | Durum |
|---|---|---|---|---|
| 1 | Filtre ailesi seçimi | Tasarım | Tepki karakteri | UNKNOWN |
| 2 | Maksimum katsayı uzunluğu (FIR) | Tasarım | Gecikme | UNKNOWN |
| 3 | Pencere türü seçimi | Tasarım | Yayılım | UNKNOWN |
| 4 | Maksimum IIR sırası | Tasarım | Kararlılık | UNKNOWN |
| 5 | float32 katsayı yeterliliği | Ölçüm | Frekans kayması | Ölçülmedi |
| 6 | fs değişimi prosedürü | Uyum | Yanlış kesim | UNKNOWN |
| 7 | Blok boyu değişimi prosedürü | Uyum | Durum kayması | UNKNOWN |
| 8 | Denormal stratejisi detayı | Kalite | CPU | Kural var, detay yok |
| 9 | Reset politikası | Kalite | Geçiş gürültüsü | UNKNOWN |
| 10 | Kanal eşleme politikası | Kalite | Tutarlılık | UNKNOWN |
| 11 | Ölçüm penceresi eşlemesi | Ölçüm | Doğruluk | Q4 |
| 12 | JUCE sürüm kanıtı | Bağımlılık | Derleme | VERIFICATION REQUIRED |
| 13 | ASIO SDK sürüm kanıtı | Bağımlılık | Derleme | VERIFICATION REQUIRED |
| 14 | FIR alternatifinin gerekçesi | Kapsam | Karmaşıklık | UNKNOWN |
| 15 | TPT avantajının ölçümü | Ölçüm | Kararlılık | Ölçülmedi |
| 16 | Katsayı üretim aracı | Üretim | Lisans/çalışma | Araç yok |

## §13 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp modülü FIR filtre uygulaması için `FIR::Filter` sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1FIR_1_1Filter.html | 2026-10-12 | 200 OK |
| JUCE dsp modülü birinci derece TPT filtre sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1FirstOrderTPTFilter.html | 2026-10-12 | 200 OK |
| JUCE çekirdeği IIR katsayı yardımcılarını `IIRCoefficients` sınıfı olarak yayınlar | https://docs.juce.com/master/classjuce_1_1IIRCoefficients.html | 2026-10-12 | 200 OK |
| Stanford CCRMA, dijital filtre konusunda yayımlanmış ücretsiz kitap bölümü erişilebilir durumdadır | https://ccrma.stanford.edu/~jos/filters/ | 2026-10-12 | 200 OK |
| JUCE `juce_dsp` kaynak ağacında FIR filtre başlığı dosyası bulunur | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/processors/juce_FIRFilter.h | 2026-10-12 | 200 OK |

## §14 Kanıt özeti

| # | Kanıt | Tür | Konum |
|---|---|---|---|
| 1 | `bandToFrequency` gerçek metodu | Repo kodu | shared/src/AI/AIEngine.php satır 332 |
| 2 | 31 bant döngüsü | Repo kodu | shared/src/AI/AIEngine.php satır 119 |
| 3 | ISO frekans tablosu satırları | Repo kodu | shared/src/AI/AIEngine.php satır 336-338 |
| 4 | Arayüz sözleşmesi | Repo kodu | shared/src/Contracts/AI/AIEngineInterface.php satır 44 |
| 5 | Workflow çağrısı | Repo kodu | shared/src/AI/AIWorkflow.php satır 164 |
| 6 | 5 URL HTTP 200 | Web | §13 |
| 7 | C++ motor yok | Envanter | 2026-10-12 ölçümü |
| 8 | Kademe-2 kapsam kaydı | Vault | filters/filters-kapsam.md |
| 9 | Sınır kaydı | Vault | equalizer/equalizer-kapsam.md §2 |
| 10 | Filtre ailesi kararı | Eksik | UNKNOWN |

## §15 Terim sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | IIR | Geri beslemeli dijital filtre |
| 2 | FIR | Geri beslemesiz dijital filtre |
| 3 | Biquad | İki kutuplu, iki sıfırlı blok |
| 4 | Kutup | Tepkiyi büyüten frekans konumu |
| 5 | Sıfır | Tepkiyi iptal eden frekans konumu |
| 6 | Katsayı | Filtre denkleminin sayısal değerleri |
| 7 | TPT | Topoloji koruyan dönüşüm |
| 8 | SVF | State-variable filtre |
| 9 | Ladder | Merdiven topolojisi |
| 10 | Pencereleme | FIR katsayı üretme yöntemi |
| 11 | Linear phase | Doğrusal faz, sabit gecikme |
| 12 | Grup gecikmesi | Frekans bağımlı gecikme |
| 13 | Nyquist | fs / 2 sınır frekansı |
| 14 | Alias | Örnekleme yan ürünü |
| 15 | Denormal | CPU riski taşıyan küçük sayı |
| 16 | Konvolüsyon | IR ile filtreleme işlemi |
| 17 | Partition | Konvolüsyonun bloklara bölünmesi |
| 18 | Headroom | Kırpılmadan önceki seviye payı |
| 19 | Gain bütçesi | Zincir boyu kazanç planı |
| 20 | Bypass | Düz geçiş karşılaştırması |
| 21 | Reset | Durum sıfırlama |
| 22 | fs | Örnekleme hızı |
| 23 | Blok | İşlem birimi, 512 örnek |
| 24 | noexcept | İstisna atmayan imza |
| 25 | lock-free | Kilit kullanmayan yazım |

## §16 Uygulama kontrol listesi

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Filtre ailesi kararı yazılı mı | Evet/Hayır | UNKNOWN |
| 2 | Maksimum IIR sırası belirlendi mi | Evet/Hayır | UNKNOWN |
| 3 | FIR alternatifinin gerekçesi yazıldı mı | Evet/Hayır | UNKNOWN |
| 4 | float32 katsayı yeterliliği ölçüldü mü | Evet/Hayır | Ölçülmedi |
| 5 | fs değişimi prosedürü var mı | Evet/Hayır | UNKNOWN |
| 6 | Blok boyu sabitliği doğrulandı mı | Evet/Hayır | sartname |
| 7 | Denormal temizliği uygulandı mı | Evet/Hayır | Kural var |
| 8 | Tahsis denetimi yapıldı mı | Evet/Hayır | Motor yok |
| 9 | Kilit denetimi yapıldı mı | Evet/Hayır | Motor yok |
| 10 | noexcept denetimi yapıldı mı | Evet/Hayır | Motor yok |
| 11 | alignas(64) uygulandı mı | Evet/Hayır | Ölçüm yok |
| 12 | Tepki ölçümü yapıldı mı | Evet/Hayır | Ölçüm yok |
| 13 | Bypass testi tanındı mı | Evet/Hayır | testing |
| 14 | Kanal eşleme politikası var mı | Evet/Hayır | UNKNOWN |
| 15 | Reset politikası var mı | Evet/Hayır | UNKNOWN |
| 16 | Sürüm kanıtı toplandı mı | Evet/Hayır | VERIFICATION REQUIRED |

## §17 Katsayı duyarlılığı ve doğrulama vektörleri

Katsayıların sayısal doğruluğu, üretilen tepkinin hedeften sapmasını belirler. Aşağıdaki
tablo duyarlılık kaynaklarını ve bu depodaki ölçüm durumunu ayırır.

| # | Duyarlılık kaynağı | Etkilenen parametre | Sapma biçimi | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | float32 yuvarlama | Tüm katsayılar | Konum kayması | Tepki ölçümü | Ölçülmedi |
| 2 | f0 yüksekliği | Yukarıdaki bantlar | Oransal kayma | Tablo taraması | Ölçülmedi |
| 3 | Q yüksekliği | Dar bantlar | Zirve derinliği | Tepki ölçümü | Ölçülmedi |
| 4 | Kazanç yüksekliği | Tüm bantlar | Genlik | Seviye ölçümü | Ölçülmedi |
| 5 | fs sapması | Tüm katsayılar | Frekans kayması | fs testi | Karar yok |
| 6 | Blok boyu değişimi | Durum | Durum kayması | Süre testi | sartname |
| 7 | Sıra artışı | Kaskad | Kümülatif sapma | Kaskad ölçümü | Ölçülmedi |
| 8 | Kanal ayrışması | Eşli durum | Stereo kayması | Kanal testi | Ölçülmedi |
| 9 | Bypass geçişi | Durum | Tıkırtı | A/B testi | Tanım yok |
| 10 | Denormal durum | Durum | CPU artışı | Süre ölçümü | Kural var |
| 11 | Durum taşması | Durum | Kırpılma | Genlik testi | Ölçülmedi |
| 12 | Ölçek kaybı | Pre-gain | SNR düşüşü | SNR ölçümü | Ölçülmedi |
| 13 | DC bileşeni | Bant çıkışı | DC ofset | DC ölçümü | Relö kuralı |
| 14 | True peak aşımı | Bant toplamı | Örnekleme-arası tepe | Oversampling ölçüm | Ölçülmedi |
| 15 | Preset dönüşümü | Tüm katsayılar | Yuvarlama | Preset testi | Biçim yok |
| 16 | Yazma anı güncelleme | Parametre | Yarım güncelleme | Eşzamanlılık testi | Lock-free kuralı |
| 17 | Yavaşlatılmış parametre | Zarf | Gecikmeli tepki | Zaman ölçümü | dynamics sınırı |
| 18 | Ölçüm penceresi kayması | Ölçüm | Yanlış referans | Pencere testi | Q4 |
| 19 | Yazılım sürümü kayması | Tüm katsayılar | Davranış farkı | Sürüm testi | VERIFICATION REQUIRED |
| 20 | Donanım farkı | Uygulama | Ölçüm farkı | Donanım testi | Donanım yok |
| 21 | Kanal sayısı farkı | Toplam gain | Seviye farkı | Kanal testi | 8.1 PLANNED |
| 22 | Girdi formatı farkı | Örnekleme | Frekans kayması | Format testi | Bilinmiyor |
| 23 | Gürültü eşiği | Durum | Taban gürültüsü | SNR ölçümü | Ölçülmedi |
| 24 | Ölçüm tekrarı | Ölçüm | İstatistiksel yayılım | Tekrar testi | Yok |
| 25 | Referans eğri farkı | Hedef | Yanlış hedef | Eğri karşılaştırma | measurement |
| 26 | Bypass eşitsizliği | Karşılaştırma | Yanlış A/B | Eşitleme testi | Tanım yok |
| 27 | Yeniden başlatma farkı | Durum | Soğuk/sıcak farkı | Restart testi | Tanım yok |
| 28 | Tahsis tekrarı | Bellek | Kopma | Kod denetimi | L0 kuralı |

## §18 Bant bazlı çekirdek eşlemesi

EQ bandı başına çekirdek seçimi, filters ile equalizer'ın ortak sözleşmesidir. Bu
matris bir **tasarım matrisidir**; karar metni yoktur, değerler `UNKNOWN` işaretlidir.

| Bant | Frekans (Hz) | Önerilen çekirdek | Sıra | Duyarlılık | Durum |
|---|---|---|---|---|---|
| 0 | 20 | Peaking biquad | 2 | Yüksek f0 | UNKNOWN |
| 1 | 25 | Peaking biquad | 2 | Yüksek f0 | UNKNOWN |
| 2 | 31.5 | Peaking biquad | 2 | Yüksek f0 | UNKNOWN |
| 3 | 40 | Peaking biquad | 2 | Yüksek f0 | UNKNOWN |
| 4 | 50 | Peaking biquad | 2 | Orta | UNKNOWN |
| 5 | 63 | Peaking biquad | 2 | Orta | UNKNOWN |
| 6 | 80 | Peaking biquad | 2 | Orta | UNKNOWN |
| 7 | 100 | Peaking biquad | 2 | Orta | UNKNOWN |
| 8 | 125 | Peaking biquad | 2 | Orta | UNKNOWN |
| 9 | 160 | Peaking biquad | 2 | Orta | UNKNOWN |
| 10 | 200 | Peaking biquad | 2 | Orta | UNKNOWN |
| 11 | 250 | Peaking biquad | 2 | Orta | UNKNOWN |
| 12 | 315 | Peaking biquad | 2 | Orta | UNKNOWN |
| 13 | 400 | Peaking biquad | 2 | Orta | UNKNOWN |
| 14 | 500 | Peaking biquad | 2 | Orta | UNKNOWN |
| 15 | 630 | Peaking biquad | 2 | Orta | UNKNOWN |
| 16 | 1000 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 17 | 1250 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 18 | 1600 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 19 | 2000 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 20 | 2500 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 21 | 3150 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 22 | 4000 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 23 | 5000 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 24 | 6300 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 25 | 8000 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 26 | 10000 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 27 | 12500 | Peaking biquad | 2 | Düşük | UNKNOWN |
| 28 | 16000 | Peaking biquad | 2 | Yüksek f0 | UNKNOWN |
| 29 | 20000 | High-shelf biquad | 2 | Nyquist'e yakınlık | UNKNOWN |
| 30 | (ek bant) | High-shelf biquad | 2 | Nyquist'e yakınlık | UNKNOWN |

## §19 Karar günlüğü

| # | Kayıt | Tarih | Etkilenen | Durum |
|---|---|---|---|---|
| 1 | Kademe-2 kapsam kaydı | 2026-10-12 | filters | Mevcut |
| 2 | Derin dal (bu dosya) | 2026-10-12 | filters | Bu dosya |
| 3 | IIR/FIR seçimi yazılmadı | 2026-10-12 | filters | UNKNOWN |
| 4 | Filtre ailesi seçimi yazılmadı | 2026-10-12 | filters | UNKNOWN |
| 5 | Maksimum sıra kararı yok | 2026-10-12 | filters | UNKNOWN |
| 6 | Pencere türü kararı yok | 2026-10-12 | filters | UNKNOWN |
| 7 | float32 yeterlilik ölçülmedi | 2026-10-12 | ADR-017 | Ölçülmedi |
| 8 | fs değişimi prosedürü yok | 2026-10-12 | build | UNKNOWN |
| 9 | Blok boyu sabitliği sartnamede | 2026-10-12 | pipeline | Sartname |
| 10 | Denormal kuralı L0'da | 2026-10-12 | L0 | Mevcut |
| 11 | Tahsis yasak L0'da | 2026-10-12 | L0 | Mevcut |
| 12 | Kilit yasak L0'da | 2026-10-12 | L0 | Mevcut |
| 13 | noexcept kuralı L0'da | 2026-10-12 | L0 | Mevcut |
| 14 | 5 URL doğrulandı | 2026-10-12 | research | Ölçüldü |
| 15 | C++ motor envanteri 0 | 2026-10-12 | build | Ölçüldü |
| 16 | Sınır çelişkisi yok (equalizer) | 2026-10-12 | equalizer | Gözlem |
| 17 | Sınır çelişkisi yok (pipeline) | 2026-10-12 | pipeline | Gözlem |
| 18 | Ölçüm penceresi kararı yok | 2026-10-12 | measurement | Q4 |
| 19 | Test kapsamı tanımlı değil | 2026-10-12 | testing | Q4 |
| 20 | JUCE sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| 21 | ASIO SDK sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| 22 | Kanal eşleme politikası yok | 2026-10-12 | pipeline | UNKNOWN |
| 23 | Reset politikası yok | 2026-10-12 | filters | UNKNOWN |
| 24 | FIR gerekçesi yazılmadı | 2026-10-12 | filters | UNKNOWN |
| 25 | TPT avantajı ölçülmedi | 2026-10-12 | testing | Ölçülmedi |
| 26 | Kazanç bütçesi eşiği yok | 2026-10-12 | dynamics | UNKNOWN |
| 27 | Brickwall ölçümü yok | 2026-10-12 | limiter | Ölçülmedi |
| 28 | Konvolüsyon partition kararı yok | 2026-10-12 | reverb | UNKNOWN |
| 29 | Crossover filtre ailesi kararı yok | 2026-10-12 | crossover | UNKNOWN |
| 30 | Üst karar bekleniyor | 2026-10-12 | architect | Bekliyor |

## §20 Denetim matrisi

| # | Denetim | Yöntem | Sıklık | Sorumlu | Durum |
|---|---|---|---|---|---|
| 1 | Filtre ailesi kararı mevcut mu | Vault araması | Sürüm | architect | Yok |
| 2 | Katsayı üretimi tekrar üretilebilir mi | Yeniden çalıştırma | Sürüm | embedded | Motor yok |
| 3 | float32 katsayı doğrulandı mı | Tepki ölçümü | Sürüm | testing | Ölçülmedi |
| 4 | fs değişimi test edildi mi | Ortam değiştirme | Sürüm | build | Yok |
| 5 | Blok boyu değişimi test edildi mi | Ortam değiştirme | Sürüm | pipeline | sartname |
| 6 | Durum sıfırlama çalışıyor mu | A/B testi | Sürüm | testing | Tanım yok |
| 7 | Bypass eşitliği korundu mu | Seviye ölçümü | Sürüm | testing | Tanım yok |
| 8 | Denormal temizliği aktif mi | Süre ölçümü | Sürüm | performance | Kural var |
| 9 | Tahsis callback dışında mı | Kod denetimi | Her PR | embedded | Motor yok |
| 10 | Mutex yok mu | Kod denetimi | Her PR | embedded | Motor yok |
| 11 | noexcept tam mı | Derleme | Her derleme | build | Motor yok |
| 12 | alignas(64) uygulandı mı | Kod denetimi | Sürüm | embedded | Ölçüm yok |
| 13 | Kanal tutarlılığı test edildi mi | Kanal ölçümü | Sürüm | testing | Yok |
| 14 | True peak korundu mu | Oversampling ölçüm | Sürüm | limiter | Ölçülmedi |
| 15 | DC eşiği korundu mu | DC ölçümü | Donanım | audio-hw | Relö kuralı |
| 16 | SNR hedefi tuttu mu | SNR ölçümü | Sürüm | testing | Hedef yok |
| 17 | Gürültü tabanı ölçüldü mü | Sessizlik ölçümü | Sürüm | measurement | Yok |
| 18 | Preset dönüşümü test edildi mi | Preset yükleme | Sürüm | testing | Biçim yok |
| 19 | Ölçüm penceresi sabit mi | Tekrar ölçümü | Sürüm | measurement | Q4 |
| 20 | Ölçüm tekrarlanabilirliği | Tekrar testi | Sürüm | measurement | Yok |
| 21 | Sürüm kanıtı mevcut mu | Derleme kaydı | Sürüm | build | VERIFICATION REQUIRED |
| 22 | Bağımlılık lisansları denetlendi mi | Lisans taraması | Sürüm | legal | Yok |
| 23 | URL benzersizliği korundu mu | Vault ölçümü | Dosya | vault | Ölçüm betiği |
| 24 | Mojibake yok mu | Byte ölçümü | Dosya | vault | Ölçüm betiği |
| 25 | Dolgu dizgisi yok mu | Metin taraması | Dosya | vault | Ölçüm betiği |
| 26 | Frontmatter tam mı | Alan sayımı | Dosya | vault | 10 alan |
| 27 | node_id yolu doğru mu | Dosya yolu | Dosya | vault | Ölçüm betiği |
| 28 | group eşlemesi doğru mu | Suffix kuralı | Dosya | vault | mimari |
| 29 | Authority kapanışı var mı | Metin araması | Dosya | vault | Mevcut |
| 30 | Satır sayısı 500 üstü mü | Satır sayımı | Dosya | vault | Ölçülecek |

## §21 Bağlantı haritası

| # | Bağlantı | Hedef dosya | İlişki | Not |
|---|---|---|---|---|
| 1 | Master kapsam | dsp-master.md | Üst düğüm | §3 rol tanımı |
| 2 | Dal kapsamı | filters/filters-kapsam.md | Kademe-2 | Sınır kaydı |
| 3 | EQ sınırı | equalizer/equalizer-kapsam.md | Komşu | Bant ↔ çekirdek |
| 4 | EQ derin dalı | equalizer/equalizer-filtre-topolojileri.md | Komşu | Topoloji |
| 5 | Pipeline örneği | pipeline/pipeline-ornekler.md | Komşu | Sıra |
| 6 | Ölçüm kapsamı | measurement/measurement-kapsam.md | Komşu | Hedef eğri |
| 7 | Test örneği | testing/testing-ornekler.md | Komşu | Ört testleri |
| 8 | Reverb örneği | reverb/reverb-ornekler.md | Komşu | Konvolüsyon |
| 9 | Limiter örneği | limiter/limiter-ornekler.md | Komşu | Tavan |
| 10 | Bağımlılıklar | dsp-bagimliliklar.md | Bağımlılık | K003-audio |
| 11 | Doğrulama | dsp-dogrulama.md | Kanıt | D-kriterleri |
| 12 | Kök indeks | index.md | Katalog | Dal listesi |
| 13 | ADR-017 | .ai/.decisions | float32 kararı | Karar metni |
| 14 | ADR-019 | .ai/.decisions | Per-OS FROZEN | Karar metni |
| 15 | ADR-038 H001 | .ai/.decisions | PCM3168A | Karar metni |
| 16 | L0 kuralları | Kök AGENTS.md | Kural seti | Ses iş parçacığı |
| 17 | Repo kod kanıtı | shared/src/AI/AIEngine.php | Eşleme | bandToFrequency |
| 18 | Arayüz sözleşmesi | shared/src/Contracts/AI/AIEngineInterface.php | Eşleme | optimizeEQ |
| 19 | Workflow | shared/src/AI/AIWorkflow.php | Eşleme | eq-optimization |
| 20 | C++ motor | engine/ (yok) | Eksik | 0 dosya |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
