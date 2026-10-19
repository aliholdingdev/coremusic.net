---
title: "equalizer-filtre-topolojileri — K004-dsp Derin Dal"
type: architecture-deep-dive
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K004-dsp/dsp-master.md §3 · equalizer dalı"
updated: 2026-10-12
parent_id: K004-dsp/equalizer
group: mimari
node_id: K004-dsp/equalizer/equalizer-filtre-topolojileri.md
---

# equalizer-filtre-topolojileri — K004-dsp Derin Dal

## §1 Rol ve kapsam sınırı

Bu dal, bir EQ'nun **filtre topolojisi** kararlarını belgeler: bir bant bloğunun hangi
matematiksel yapıda kurulacağı, bantların nasıl yerleşeceği, katsayıların nasıl
korunacağı ve gerçek zamanlı bir zincirde EQ'nun nerede duracağı. Kademe-2 kapsam
kaydı (`equalizer-kapsam.md`) bu dalı "Frekans bantlarına göre seviye ayarı" olarak
tanımlar; buradaki derin dal aynı sınırı matematik düzeyinde açar.

`dsp-master.md` §3 kaydı, EQ'nun yalnızca **bant seviyesi** sahibi olduğunu, bant
içindeki filtre çekirdeğinin `filters` dalına ait olduğunu yazar. Bu dosya iki
sınırları da ziyaret eder: topoloji seçimini (EQ işi) ve çekirdek kararlılığını
(filters işi) ayrı ayrı işaretler, çelişkide üst kararı (Q3) bekler.

| # | Bu dalın içinde | Bu dalın dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Bant topolojisi seçimi (grafik, parametrik, shelving, notch) | Biquad katsayı üreteç formüllerinin türetilmesi | filters |
| 2 | 31-band ISO yerleşimi ve bant frekans tablosu | Oda ölçümü ve hedef eğri üretimi | measurement |
| 3 | Q/genişlik kavramı ve bant çakışması | Kazanç zarfı takibi (gain smoothing) | dynamics |
| 4 | Kararlılık ve kuantizasyon riskleri | Filtre uygulama bloğu kodu (C++ henüz yok) | filters · pipeline |
| 5 | EQ'nun pipeline içindeki sırası | Zincirin sahibi olmak | pipeline |
| 6 | float32 veri yolu (ADR-017) | Bant başına donanım EQ'su | K003-audio |
| 7 | Denormal ve tahsis kuralları | Test stratejisi ve ört testleri | testing |
| 8 | Komşu dal uyarıları | Web araştırmasının tamamı (bkz §12) | — |

**Yasak:** bu dosyada `filters` dalının işi olan katsayı üreteç detayına girmek; eşik
çelişkisi çıkarsa sessizce çözmek yerine `⚠️ VERIFICATION REQUIRED` işaretleyip üst
kararı beklemek.

## §2 EQ topolojileri karşılaştırması

EQ tipi seçimi, hedeflenen kullanım ve işlem maliyetiyle birlikte verilir. Aşağıdaki
tablo K004 kapsamında değerlendirilen topolojileri tek yerde toplar; "durum" sütunu
bu depoda hangi bilginin elde olduğunu gösterir.

| # | Topoloji | Bant başına blok | Frekans çözünürlüğü | Gecikme etkisi | Maliyet | Tipik kullanım | Durum |
|---|---|---|---|---|---|---|---|
| 1 | Grafik EQ (sabit bant) | 1 biquad | Orta (bant genişliği kadar) | Blok-içi, değişmez | Düşük, sabit | Kullanıcı preset'i | AIEngine 31-band kodu ile eşleşiyor |
| 2 | Parametrik EQ (serbest f0/Q) | 1 biquad | Yüksek | Blok-içi, değişmez | Orta | Oda düzeltme, master | f0/Q aralığı UNKNOWN |
| 3 | Shelving (low/high) | 1 biquad | Tek geçiş | Blok-içi | Düşük | Bass/tiz genel seviye | K004 kapsamında, değer yok |
| 4 | Cut (notch) | 1 biquad | Dar | Blok-içi | Düşük | Tek frekans bastırma | Eşik değeri UNKNOWN |
| 5 | Paso bant (band-pass) | 1 biquad | Orta | Blok-içi | Düşük | Bant izolasyonu | K004 kapsamında |
| 6 | Faz invert / all-pass | 1 biquad | Geniş | Blok-içi | Düşük | Faz düzeltme | K004 kapsam dışı olabilir — VERIFICATION REQUIRED |
| 7 | FIR tabanlı EQ | Konvolüsyon | Çok yüksek | Sabit gecikme | Yüksek | Ölü-zaman ölçüm düzeltmesi | Gecikme bütçesi UNKNOWN |
| 8 | FFT tabanlı (blok) EQ | Spektral maske | Frekans bini kadar | Blok penceresi | Orta-yüksek | Rötarlı düzeltme | Ölçüm dalı ile sınır |
| 9 | Analog-emülasyon kaskadı | 2-4 biquad | Yüksek | Blok-içi | Orta-yüksek | Renk/karakter preset | Dijital model doğruluğu UNKNOWN |
| 10 | Crosstalk/kanal EQ | Bant × kanal | Orta | Blok-içi | Kanal kadar | 8.1 çıkış düzeltme | Hedef: 8.1, durum PLANNED |
| 11 | Minimum faz eşleniği | 1 biquad | Yüksek | Faz tepkisi değişir | Düşük | Gecikme hassas yol | Faz toleransı UNKNOWN |
| 12 | Doğrusal faz FIR (linear phase) | Konvolüsyon | Yüksek | Büyük sabit gecikme | Yüksek | Mastering yolu | Gecikme bütçesi ile çatışabilir |
| 13 | Multi-band dinamik EQ | Biquad + zarf | Yüksek | Blok-içi | Orta | Frekans-bağımlı sıkıştırma | compressor dalı ile sınır |
| 14 | Adaptif (ölçüm-kontrollü) EQ | Biquad + kontrol | Yüksek | Kontrol yavaş | Orta | Oda düzeltme kapalı çevrim | measurement sahibi |
| 15 | Üst-bant (high-shelf) sabit | 1 biquad | Geniş | Blok-içi | Düşük | Yayın tiz düzeltme | Ölçüm standardı ile ilişkili |
| 16 | Alt-bant (low-shelf) sabit | 1 biquad | Geniş | Blok-içi | Düşük | Yayın bass düzeltme | Ölçüm standardı ile ilişkili |
| 17 | Preset zinciri (önceden hesaplı) | Önceden üretim | Bant | Yok (statik) | Çalışma anında düşük | Sabit profil | Preset formatı UNKNOWN |
| 18 | Kullanıcı arayüzü bant sürüklemesi | 1 biquad | Yüksek | Anlık değer değişimi | Düşük | Etkileşim | Smoothing dynamics dalında |
| 19 | Bypass (düz geçiş) | Yok | Yok | Yok | Yok | Karşılaştırma | Test gereksinimi: testing |
| 20 | Eşleme/karıştırma (dry-wet) | Karıştırıcı | Yok | Eklenen gecikme yok | Düşük | Kısmi uygulama | pipeline dalı sahibi |

> Satır 12'deki **doğrusal faz FIR (linear phase)** topolojisi büyük sabit gecikme
> taşır. K004 hedefi 512 örnek tampon ile çalıştığı için bu topolojinin gecikme
> bütçesi `UNKNOWN` işaretidir; karar `pipeline` dalının gecikme bütçesine bağlıdır.

## §3 Bant yerleşimi: 31-band ISO tablosu

Repo'daki tek gerçek 31-band uygulaması `AIEngine::optimizeEQ` içindedir. Bant
indeksi 0'dan 30'a kadar döner, her indeks bir frekansa ve bir Q değerine karşılık
gelir. Aşağıdaki tablo, o fonksiyonun döngü düzenini (31 bant) ve frekans tablosunun
sayısal içeriğini (20 Hz'ten 20000 Hz'e) taşır.

| Bant # | Frekans (Hz) | Bant # | Frekans (Hz) | Bant # | Frekans (Hz) | Bant # | Frekans (Hz) |
|---|---|---|---|---|---|---|---|
| 0 | 20 | 8 | 125 | 16 | 1000 | 24 | 6300 |
| 1 | 25 | 9 | 160 | 17 | 1250 | 25 | 8000 |
| 2 | 31.5 | 10 | 200 | 18 | 1600 | 26 | 10000 |
| 3 | 40 | 11 | 250 | 19 | 2000 | 27 | 12500 |
| 4 | 50 | 12 | 315 | 20 | 2500 | 28 | 16000 |
| 5 | 63 | 13 | 400 | 21 | 3150 | 29 | 20000 |
| 6 | 80 | 14 | 500 | 22 | 4000 | 30 | tek ek bant (tablo dışı) |
| 7 | 100 | 15 | 630 | 23 | 5000 | — | — |

Tablodaki 29 değer `AIEngine.php` satır 336-338'de yazılan ISO serisidir; 30. ve
31. değer dosyada `20000` ile biter ve döngü `31` bantta durur. Bant sayısının neden
31 olduğu (10 octave üstel seri) bir tasarım kararıdır; bu depoda karar metni
`UNKNOWN` işaretlidir.

| # | Yerleşim özelliği | Gözlem | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Bant sayısı | 31 | AIEngine optimizeEQ döngüsü | Repo kanıtı |
| 2 | Frekans aralığı | 20 Hz – 20000 Hz | AIEngine satır 336-338 | Repo kanıtı |
| 3 | Aralık mantığı | 1/3 octave benzeri üstel seri | Frekans değerleri 1.26 katına yakın artar | Gözlem |
| 4 | Bant merkezi sabit mi | Evet, tablo sabit | bandToFrequency sabit dizi | Repo kanıtı |
| 5 | Bant genişliği (Q) sabit mi | Hayır, frekans ve oda boyutuna bağlı | calculateBandQ | Repo kanıtı |
| 6 | En düşük bant Q değeri | 0.7 / 1.0 / 1.4 / 1.8 kümelerinden seçilir | calculateBandQ gövdesi | Repo kanıtı |
| 7 | Kullanıcı bant sayısı değiştirilebilir mi | Belirsiz | Karar metni yok | UNKNOWN |
| 8 | Bantlar arası çakışma toleransı | Belirsiz | Eşik yok | UNKNOWN |
| 9 | Nyquist üstü bant | Yok, 20000 Hz ile biter | Tablo sonu | Gözlem |
| 10 | Örnekleme hızı bağımsızlığı | 48 kHz'te 20000 Hz geçerli | sartname 48 kHz | Sartname |
| 11 | 44.1 kHz'te 20000 Hz | Nyquist 22050 Hz, geçerli | Hesap | Gözlem |
| 12 | 96 kHz'te davranış | Tablo aynen kalır, üst bantlar boş | Karar yok | UNKNOWN |
| 13 | Bant başına gecikme | Biquad blok-içi, örnekleme gecikmesi yok varsayımı | K004 tasarım hedefi | VERIFICATION REQUIRED |
| 14 | Ölçek (dB) adımı | UI adımı belirsiz | Karar yok | UNKNOWN |
| 15 | Preset taşınabilirliği | Bant sayısı sabitse taşınabilir | Tasarım varsayımı | VERIFICATION REQUIRED |
| 16 | Ölçümle eşleşme | Hedef eğri measurement dalından gelir | Sınır kaydı | Komşu |

## §4 Parametrik bant matematiği (f0, Q, kazanç)

Parametrik bir bant üç sayıyla tanımlanır: merkez frekansı (f0), genişlik (Q) ve
kazanç (dB). K004 kapsamında bu üçlünün **aralıkları** için karar metni yoktur;
aşağıdaki tablo kavramsal ilişkiyi ve bu depodaki bilinmeyenleri ayırır.

| # | Parametre | Sembol | Birim | Etkisi | Sınır riski | Durum |
|---|---|---|---|---|---|---|
| 1 | Merkez frekansı | f0 | Hz | Zirvenin/b çukurun konumu | Nyquist'e yakınlık | Aralık UNKNOWN |
| 2 | Genişlik | Q | boyutsuz | Dar = spesifik, gen = yumuşak | Yüksek Q = kararlılık riski | Aralık UNKNOWN |
| 3 | Kazanç | G | dB | Zirve derinliği | Headroom erimesi | Aralık UNKNOWN |
| 4 | Bandwidth ilişkisi | BW | oktav | Q ile ters orantılı | Formül seçimi | VERIFICATION REQUIRED |
| 5 | Peaking denklemi | — | — | f0 çevresinde tepe | Katsayı hesap filters'ta | Sınır |
| 6 | Shelving denklemi | — | — | Uç bantta sabit eğim | Katsayı hesap filters'ta | Sınır |
| 7 | Notch derinliği | G | dB | Sıfır noktası derinliği | Tam iptal = kararlılık | UNKNOWN |
| 8 | Filtre sırası | n | — | Bant başına kutup sayısı | Yüksek sıra = gecikme/faz | 2. derece varsayımı |
| 9 | Kazanç toplamı | ΣG | dB | Toplam headroom | Aşım halinde kırpmak gerekir | Eşik UNKNOWN |
| 10 | Bant çakışması | — | dB | Komşu bant tepkisi toplanır | Yanıt hedefi ölçüm işi | measurement |
| 11 | Ölçek geçişi | — | ms | Kullanıcı hareketinde yumuşama | Tıkırtı riski | dynamics sınırı |
| 12 | Bypass farkı | — | dB | Açıp kapama farkı | Fark üstü görünürlük | testing gereksinimi |
| 13 | Sıra değiştirme | — | — | EQ'nun yerinin etkisi | Bozulma öncesi/sonrası | pipeline |
| 14 | Kanal eşlemesi | — | — | Stereo/8.1 bant paylaşımı | Ayrık vs ortak bant | UNKNOWN |
| 15 | Örnekleme hızı | fs | Hz | Katsayıların yeniden hesabı | fs değişimi = yeniden tasarım | VERIFICATION REQUIRED |
| 16 | Nokta doğruluğu | — | bit | float32 (ADR-017) | Kuantizasyon gürültüsü | ADR-017 |

## §5 Biquad çekirdek aileleri ve tercih

Topoloji seçimi, çekirdek (biquad) yapısını da dayatır. Aşağıdaki tablo bilinen
dijital biquad ailelerini ve K004 açısından notlarını listeler; hangisinin seçildiğine
dair bu depoda karar metni **yoktur** (filters dalının açması beklenir).

| # | Çekirdek ailesi | Sıfır/kutup davranışı | Hassasiyet | Uygulama tipi | K004 notu |
|---|---|---|---|---|---|
| 1 | Doğrudan formül (Direct Form I) | Standart | Orta | Genel | Örnekleme, implementasyon kararı yok |
| 2 | Direct Form II | Transpoze, durum az | Orta | Genel | Bellek avantajı iddiası ölçülmedi |
| 3 | Transpoze Direct Form II | Yaygın, durum 2 elemanlı | Orta | Genel | Ölçüm yok |
| 4 | Topology-preserving transform (TPT/SVF) | Yüksek fs'te daha stabil | Düşük | Parametrik | JUCE TPT sınıfı dokümantasyonu §12'de |
| 5 | Ladder (merdiven) | Beşinci dereceye kadar | Orta | Karakter EQ | JUCE Ladder sınıfı §12'de |
| 6 | State-variable | Frekans-bağımlı gecikme | Düşük | Çoklu çıkış | Parametrik EQ için aday |
| 7 | Kutup-yarıçap ölçekleme | Sınırlı genlik | Düşük | Yüksek fs | Ölçüm yok |
| 8 | Katsayı yuvarlama (float32) | Hafif kayma | float32 | Hepsi | ADR-017 |
| 9 | Katsayı çift duyarlılığı | f0/Q hassasiyeti | Yüksek f0'da | Tümü | F0'ın büyük kısmında risk |
| 10 | Denormal bastırma | CPU patlaması önlenir | — | Tümü | L0 kuralı: denormal yok |
| 11 | Sıfır-gecikme varsayımı | Blok-içi işleme | — | Tümü | ÖLÇÜLMEDİ — VERIFICATION REQUIRED |
| 12 | Faz doğrusallığı | Biquad = doğrusal değil | — | Tümü | Minimum faz tercihi §2 satır 11 |
| 13 | Grup gecikmesi | f0 çevresinde değişir | — | Tümü | Ölçüm yok, testing işi |
| 14 | Nokta aşımı (overflow) | float32'de nadir | — | Tümü | Aşım davranışı UNKNOWN |
| 15 | Süreksiz parametre değişimi | Tıkırtı üretir | — | Tümü | Smoothing dynamics'te |
| 16 | Çoklu örnek hızı | fs değişince yeniden tasarım | — | Tümü | 48 kHz sabit varsayımı |

## §6 Kararlılık, kuantizasyon ve float32 (ADR-017)

ADR-017'ye göre ses verisi 32-bit floating point'tir. Bu tercih EQ topolojisi
seçimini doğrudan etkiler: bant sayısı arttıkça toplam kazanç ve katsayı
kuantizasyonu riski büyür. Aşağıdaki tablo riskleri ve bu depodaki bilinmeyenleri
ayrıştırır.

| # | Risk | Belirti | Tetikleyici | Önleme | Sahip dal | Durum |
|---|---|---|---|---|---|---|
| 1 | Katsayı kayması | F0'ın kayması | float32 yuvarlama | Tasarım aralığını dar tut | filters | VERIFICATION REQUIRED |
| 2 | Kutup birimi dışına çıkış | Titreme/üfürme | Yüksek Q + yüksek f0 | Q/f0 üst sınır | equalizer | Sınır değeri UNKNOWN |
| 3 | Denormal yavaşlama | CPU artışı | Sessizlikte durum | Denormal temizleme (L0) | equalizer | Kural var |
| 4 | Toplam kazanç aşımı | Kırpılma | Bantların toplamı | Kazanç bütçesi | dynamics | Eşik UNKNOWN |
| 5 | Ölçek ayrışması | Kanallar arası fark | Ayrı bant state | Kanal eşlemesi | pipeline | Tasarım yok |
| 6 | Bypass tıklaması | Geçişte gürültü | Anlık aç/kapa | Yumuşatma | dynamics | test gereği |
| 7 | fs değişimi | Yanlış frekans | 48 kHz dışı | Yeniden hesap | filters | Karar yok |
| 8 | Blok boyu değişimi | Durum kaybı | 512 dışı | Blok boyu sabitleme | pipeline | sartname 512 |
| 9 | Sıfır-tahsis ihlali | Ses kesilmesi | Tahsis callback içinde | Statik/baştan tahsis | L0 kuralı | Kural var |
| 10 | Kilit ihlali | Gecikme/kopma | Mutex kullanmak | Lock-free yazım | L0 kuralı | Kural var |
| 11 | noexcept ihlali | Uygulama kapanır | İstisna kaçışı | noexcept callback | L0 kuralı | Kural var |
| 12 | Ölçüm uyuşmazlığı | EQ eğrisi ölçümle sapan | Ölçüm penceresi | Ölçüm dalı ile eşleme | measurement | Ayrım Q4 |
| 13 | Yanlış headroom varsayımı | Sessiz kırpılma | Master seviye | Kazanç bütçesi tablosu | dynamics | Eşik yok |
| 14 | Gürültü tabanı yükselmesi | SNR düşüşü | Katsayı gürültüsü | float32 yeterlilik | ADR-017 | Ölçüm yok |

## §7 Q ve oda düzeltme etkileşimi (repo kanıtı)

Repo'daki `calculateRoomCorrection` fonksiyonu, oda profilini EQ kazancına çevirir ve
içinde `(1 - $reverb)` çarpanı taşır. Bu, K004 motorunun kendisi değil bir AI/oda
yardımcı hesaplamasıdır; yine de Q ve kazanç ilişkisini gösteren tek canlı koddur.

| # | Kod göstergesi | Dosya | Satır | Ne yapar | EQ topolojisi etkisi |
|---|---|---|---|---|---|
| 1 | `optimizeEQ` metodu | shared/src/AI/AIEngine.php | 111 | 31 bantlı eğri üretir | Bant yerleşimini doğrular |
| 2 | `for ($band = 0; $band < 31; $band++)` | shared/src/AI/AIEngine.php | 119 | Bant döngüsü | 31 bant kuralını kanıtlar |
| 3 | `bandToFrequency($band)` çağrısı | shared/src/AI/AIEngine.php | 120 | Bant → frekans | ISO tablosuna bağlar |
| 4 | `calculateRoomCorrection` çağrısı | shared/src/AI/AIEngine.php | 121 | Bant kazancı | Kazanç üretimi |
| 5 | `calculateBandQ($frequency, $roomSize)` | shared/src/AI/AIEngine.php | 122 | Bant Q değeri | Q frekansa bağlı |
| 6 | `(1 - $reverb)` çarpanı | shared/src/AI/AIEngine.php | 362 | Reverb arttıkça düzeltme azalır | Ölçümle etkileşim |
| 7 | `calculateRoomCorrection` tanımı | shared/src/AI/AIEngine.php | 347 | Fonksiyon başlığı | Oda profili girdisi |
| 8 | `calculateBandQ` tanımı | shared/src/AI/AIEngine.php | 368 | Q hesabı | 0.7/1.0/1.4/1.8 kümeleri |
| 9 | ISO frekans tablosu | shared/src/AI/AIEngine.php | 336-338 | 20 Hz – 20000 Hz | Bant merkezleri |
| 10 | `optimizeEQ` arayüz bildirimi | shared/src/Contracts/AI/AIEngineInterface.php | 44 | Sözleşme | Katman sınırı |
| 11 | `eq-optimization` görevi | shared/src/AI/AIWorkflow.php | 154 | Görev kuyruğa | Workflow sahipliği |
| 12 | `roomProfile` dizisi | shared/src/AI/AIWorkflow.php | 157-161 | Oda profilini kurar | reverb/boyut girdisi |
| 13 | `optimizeEQ` çağrısı | shared/src/AI/AIWorkflow.php | 164 | Eğriyi alır | Pipeline öncesi adım |
| 14 | `reverb` anahtarı | shared/src/AI/AIWorkflow.php | 159 | Varsayılan 0.5 | Çarpan girdisi |

**Sınır notu:** bu tablodaki kod C++ DSP motoru değildir; motor `engine/` dizininde
ve `.cpp/.h` dosyalarında **bulunmaz** (envanter ölçümü 2026-10-12: 0 dosya).
Bu nedenle topoloji seçimi bir *tasarım kararı* olarak kalır, uygulama kanıtı olarak
değil.

## §8 Gerçek zamanlı kısıtlar (L0 kuralları)

EQ topolojisi, L0 altyapı kurallarının dışına çıkamaz. Aşağıdaki tablo her kuralı
EQ bağlamında çevirir ve ihlal halinde sonucu yazar.

| # | L0 kuralı | EQ bağlamındaki karşılığı | İhlal belirtisi | Denetim | Durum |
|---|---|---|---|---|---|
| 1 | Tahsis yasak (ses iş parçacığı) | Bant state baştan allocate | Kesilme/kopma | Kod denetimi | Kural metni var |
| 2 | Kilit yasak (ses iş parçacığı) | Parametre yazımı lock-free | Gecikme/kopma | Kod denetimi | Kural metni var |
| 3 | `noexcept` zorunlu | Callback hiç istisna atamaz | Uygulama kapanır | Derleme denetimi | Kural metni var |
| 4 | `constexpr` tercihi | Frekans tablosu derleme anında | Çalışma anı tahsisi | Kod denetimi | Aday: ISO tablosu |
| 5 | `alignas(64)` | Bant state cache hizası | Önbellek ezilmesi | Ölçüm | Ölçüm yok |
| 6 | float32 veri (ADR-017) | Bant state float32 | Çiftlik gereksiz | Kod denetimi | ADR-017 |
| 7 | 512 örnek / 48 kHz | Blok başına bant işleme | Ölçüm dışı | sartname | Ölçüm PLANNED |
| 8 | ASIO Exclusive Lock | Tek uygulama sahipliği | Cihaz çakışması | Çalışma anı | Kural var |
| 9 | DC offset koruma | EQ düşük bantta DC geçirebilir | Eşik aşımı | Ölçüm | Relö kuralı var |
| 10 | PCM3168A zorunlu | Çıkış DAC'u sabit | PCM5122 kullanımı | BOM denetimi | ADR-038 H001 |
| 11 | Per-OS player FROZEN | EQ preset'i OS'a taşınmaz | ADR-019 ihlali | Denetim | ADR-019 |
| 12 | Sürüm kanıtı yok | JUCE 9 / ASIO SDK 2.3.4 | Sürüm iddiası | Depo yok | VERIFICATION REQUIRED |

## §9 Sıra ve komşu sınırlar

EQ zincirde nerede durur? Bu karar `pipeline` dalının; burada yalnızca topoloji
çıkarımları listelenir.

| # | Sıra seçeneği | EQ öncesi | EQ sonrası | Avantaj | Risk | Sahip |
|---|---|---|---|---|---|---|
| 1 | En baş (kaynak hemen sonra) | Çözme | Sıkıştırma | Temiz bant kontrolü | Girdi kırpılırsa EQ anlamsız | pipeline |
| 2 | Sıkıştırma sonrası | Dinamik | Limiter | GR ile etkileşim görülür | GR hedefi değişir | compressor |
| 3 | Limiter öncesi | Tüm processing | Brickwall | Headroom görülür | Tavan ihlali | limiter |
| 4 | Çıkışta (monitor yolu) | Mixer | DAC | Kulak düzeltme | Ölçüm yolu bozulur | measurement |
| 5 | Dry-wet kısmi | — | — | Yumuşak etki | Eşitlik hesabı | pipeline |
| 6 | Yan/sidechain yolu | Harici bant | Kontrol | Frekans-bağımlı kontrol | Karmaşıklık | compressor |
| 7 | IR/ölçüm sonrası | Konvolüsyon | EQ | Oda düzeltme sonra | Gecikme artışı | measurement |
| 8 | 8.1 kanal kümeleri | Kanal grubu | Ortak bant | Üretkenlik | Kanal tutarsızlığı | K003-audio |

## §10 Ölçüm etkileşimi

EQ'nun doğruluğu ölçümle sabitlenir; ölçüm sahibi `measurement` dalıdır. Bu bölüm
yalnızca topoloji açısından bağ noktalarını yazar.

| # | Bağ noktası | Yön | Bilgi akışı | Belirsizlik |
|---|---|---|---|---|
| 1 | Hedef eğri | measurement → equalizer | Düzeltme hedefi | Hedef eğri formatı UNKNOWN |
| 2 | Doğrulama ölçümü | equalizer → measurement | Öncesi/sonrası tepki | Pencere seçimi UNKNOWN |
| 3 | Bant sayısı eşlemesi | İkili | 31 bant ↔ ölçüm çözünürlüğü | Uyum UNKNOWN |
| 4 | Frekans aralığı | measurement → equalizer | 20 Hz – 20 kHz | Örtüşme tam, kesin değil |
| 5 | Gürültü tabanı | measurement → equalizer | EQ gürültüsü eşiği | Eşik yok |
| 6 | Gerçek tepe | measurement → limiter | Tavan kontrolü | Ayrım Q3/Q4 |
| 7 | Reverb/oda profili | measurement → AIEngine | `(1-$reverb)` çarpanı | Profil formatı kısmen kodda |
| 8 | Karşılaştırma testi | testing → equalizer | A/B bypass | Test kapsamı testing'te |

## §11 Riskler ve açık sorular

| # | Kayıt | Tip | Etki | Önerilen sahip | Durum |
|---|---|---|---|---|---|
| Q1 | DSPark '90+' iddiası ile 2 yıldız çelişkisi | Kanıt | Bağımlılık kararı | research | dsp-master açık sorusu |
| Q2 | Signalsmith MIT lisans teyidi | Lisans | Bağımlılık | legal | dsp-master açık sorusu |
| Q3 | dynamics ↔ compressor/limiter hiyerarşisi | Mimari | Sınır | architect | dsp-master açık sorusu |
| Q4 | measurement ↔ testing ayrımı | Mimari | Sorumluluk | architect | dsp-master açık sorusu |
| Q5 | DSP hedefi host mu hardware mi | Mimari | Uygulama | architect | dsp-master açık sorusu |
| R1 | Bant sayısı/Q varsayılan karar metni | Tasarım | Preset taşınabilirliği | equalizer | UNKNOWN |
| R2 | EQ topolojisi seçimi (grafik vs parametrik) | Tasarım | Zarf/davranış | equalizer | Karar yok |
| R3 | f0/Q/gain aralık kararları | Tasarım | Kararlılık | equalizer | UNKNOWN |
| R4 | Bloom/jitter etkisi ölçümü | Ölçüm | Gecikme | measurement | Ölçülmedi |
| R5 | 8.1 kanal EQ eşlemesi | Kapsam | Üretkenlik | K003-audio | PLANNED |
| R6 | JUCE sürüm kanıtı (9 / ASIO 2.3.4) | Bağımlılık | Derleme | build | VERIFICATION REQUIRED |
| R7 | float32 kuantizasyon etkisi ölçümü | Ölçüm | SNR | testing | Ölçülmedi |
| R8 | Preset dosya biçimi | Veri | Taşıma | equalizer | UNKNOWN |
| R9 | Bypass/AB test eşdeğerliği | Test | Güven | testing | testing dalında |
| R10 | Ölçüm penceresi ile EQ etkileşimi | Ölçüm | Doğruluk | measurement | Ayrım Q4 |
| R11 | Yazma anı tahsis denetimi | Kalite | Kopma | embedded | Denetim planı yok |
| R12 | DC offset EQ ile tetiklenebilir mi | Güvenlik | Eşik aşımı | audio-hw | Relö kuralı var |
| R13 | Kazanç bütçesi eşik değeri | Tasarım | Kırpılma | dynamics | UNKNOWN |
| R14 | fs değişimi senaryosu | Uyum | Frekans kayması | build | Karar yok |

## §12 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| JUCE dsp modülü, IIR filtre uygulaması için `IIR::Filter` sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1IIR_1_1Filter.html | 2026-10-12 | 200 OK |
| JUCE dsp modülü, topology-preserving transform tabanlı state-variable filtre sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1StateVariableTPTFilter.html | 2026-10-12 | 200 OK |
| JUCE dsp modülü, ladder topolojili filtre sınıfı yayınlar | https://docs.juce.com/master/classjuce_1_1dsp_1_1LadderFilter.html | 2026-10-12 | 200 OK |
| JUCE `juce_dsp` kaynak ağacında filtre tasarım yardımcılarını taşıyan `filter_design/juce_FilterDesign.h` dosyası bulunur | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_dsp/filter_design/juce_FilterDesign.h | 2026-10-12 | 200 OK |
| Biquad hesaplayıcı ve eşdeğerlik tabloları yayımlanmış bir kaynak olarak erişilebilir durumdadır | https://earlevel.com/main/2013/10/13/biquad-calculator/ | 2026-10-12 | 200 OK |

## §13 Kanıt özeti

| # | Kanıt | Tür | Konum | Dayanak |
|---|---|---|---|---|
| 1 | 31 bantlı EQ eğrisi üreten gerçek metot | Repo kodu | shared/src/AI/AIEngine.php satır 111 | Doğrudan okuma |
| 2 | Bant döngüsü 0-30 | Repo kodu | shared/src/AI/AIEngine.php satır 119 | Doğrudan okuma |
| 3 | ISO 20 Hz – 20000 Hz frekans tablosu | Repo kodu | shared/src/AI/AIEngine.php satır 336-338 | Doğrudan okuma |
| 4 | Q değerleri 0.7 / 1.0 / 1.4 / 1.8 kümeleri | Repo kodu | shared/src/AI/AIEngine.php satır 368-381 | Doğrudan okuma |
| 5 | Oda düzeltme çarpanı `(1-$reverb)` | Repo kodu | shared/src/AI/AIEngine.php satır 362 | Doğrudan okuma |
| 6 | 5 URL, HTTP 200 ile doğrulandı | Web | §12 tablosu | 2026-10-12 ölçümü |
| 7 | C++ DSP motoru dosyası yok | Envanter | `engine/` ve `*.cpp/*.h` = 0 | 2026-10-12 ölçümü |
| 8 | Kademe-2 sınır kaydı mevcut | Vault | equalizer/equalizer-kapsam.md | Dosya okundu |
| 9 | Master kapsam kaydı mevcut | Vault | dsp-master.md | Dosya okundu |
| 10 | C++ EQ uygulama kanıtı yok | Eksik | — | UNKNOWN |

## §14 Uygulama kontrol listesi

| # | Kontrol | Beklenen | Yöntem | Engel |
|---|---|---|---|---|
| 1 | Bant sayısı kararı yazılı mı | Evet/Hayır | Vault ADR araması | Karar metni yok |
| 2 | Topoloji seçimi onaylandı mı | Evet/Hayır | Üst karar | Q3 bekliyor |
| 3 | f0/Q/gain aralıkları tanımlı mı | Evet/Hayır | Tasarım belgesi | UNKNOWN |
| 4 | float32 doğrulandı mı | Evet/Hayır | ADR-017 okuma | ADR mevcut |
| 5 | Tahsis denetimi yapıldı mı | Evet/Hayır | Kod incelemesi | Motor yok |
| 6 | Kilit denetimi yapıldı mı | Evet/Hayır | Kod incelemesi | Motor yok |
| 7 | `noexcept` denetimi yapıldı mı | Evet/Hayır | Derleme | Motor yok |
| 8 | Denormal temizliği planlandı mı | Evet/Hayır | Tasarım | Kural var |
| 9 | Ölçüm ile eşleştirme yapıldı mı | Evet/Hayır | Ölçüm dalı | Q4 |
| 10 | 512/48k blok doğrulandı mı | Evet/Hayır | sartname | Ölçüm PLANNED |
| 11 | Bypass testi planlandı mı | Evet/Hayır | testing dalı | Kapsam yok |
| 12 | Preset biçimi kararlaştırıldı mı | Evet/Hayır | Tasarım | UNKNOWN |
| 13 | 8.1 kanal eşlemesi netleşti mi | Evet/Hayır | K003-audio | PLANNED |
| 14 | Ölçüm penceresi kararlaştırıldı mı | Evet/Hayır | measurement | Q4 |
| 15 | Kazanç bütçesi eşiği var mı | Evet/Hayır | dynamics | UNKNOWN |
| 16 | Sürüm kanıtı toplandı mı | Evet/Hayır | Build kanıtı | VERIFICATION REQUIRED |

## §15 Bant bazlı topoloji karar matrisi

Her bant indeksi için topoloji, Q ve karar durumu tek tabloda. "Karar" sütunu bu
depoda yazılı bir kararın olup olmadığını gösterir; `UNKNOWN` değerleri uydurulmaz.

| Bant | Frekans (Hz) | Önerilen blok | Q kümesi | Karar durumu | Not |
|---|---|---|---|---|---|
| 0 | 20 | Low-shelf ya da peaking | 0.7 | UNKNOWN | Sub-bass, ölçülmüş eşik yok |
| 1 | 25 | Peaking | 0.7 | UNKNOWN | Sub-bass |
| 2 | 31.5 | Peaking | 0.7 | UNKNOWN | Sub-bass |
| 3 | 40 | Peaking | 0.7 | UNKNOWN | Bass |
| 4 | 50 | Peaking | 0.7 | UNKNOWN | Bass |
| 5 | 63 | Peaking | 0.7 | UNKNOWN | Bass |
| 6 | 80 | Peaking | 1.0 | UNKNOWN | Bass |
| 7 | 100 | Peaking | 1.0 | UNKNOWN | Bass |
| 8 | 125 | Peaking | 1.0 | UNKNOWN | Alçak orta |
| 9 | 160 | Peaking | 1.0 | UNKNOWN | Alçak orta |
| 10 | 200 | Peaking | 1.0 | UNKNOWN | Alçak orta |
| 11 | 250 | Peaking | 1.0 | UNKNOWN | Alçak orta |
| 12 | 315 | Peaking | 1.0 | UNKNOWN | Orta |
| 13 | 400 | Peaking | 1.0 | UNKNOWN | Orta |
| 14 | 500 | Peaking | 1.0 | UNKNOWN | Orta |
| 15 | 630 | Peaking | 1.0 | UNKNOWN | Orta |
| 16 | 1000 | Peaking | 1.0 | UNKNOWN | Referans bant |
| 17 | 1250 | Peaking | 1.4 | UNKNOWN | Üst orta |
| 18 | 1600 | Peaking | 1.4 | UNKNOWN | Üst orta |
| 19 | 2000 | Peaking | 1.4 | UNKNOWN | Üst orta |
| 20 | 2500 | Peaking | 1.4 | UNKNOWN | presence bölgesi |
| 21 | 3150 | Peaking | 1.4 | UNKNOWN | presence bölgesi |
| 22 | 4000 | Peaking | 1.4 | UNKNOWN | Tiz |
| 23 | 5000 | Peaking | 1.4 | UNKNOWN | Tiz |
| 24 | 6300 | Peaking | 1.8 | UNKNOWN | Tiz |
| 25 | 8000 | Peaking | 1.8 | UNKNOWN | Tiz |
| 26 | 10000 | Peaking | 1.8 | UNKNOWN | Air |
| 27 | 12500 | Peaking | 1.8 | UNKNOWN | Air |
| 28 | 16000 | Peaking | 1.8 | UNKNOWN | Air |
| 29 | 20000 | High-shelf | 1.8 | UNKNOWN | Uç bant |
| 30 | (20000 ile biten ek bant) | High-shelf | 1.8 | UNKNOWN | Tablo dışı ek bant |

Q kümeleri (0.7 / 1.0 / 1.4 / 1.8) repo'daki `calculateBandQ` gövdesinden gelir; bant
başına **hangi** kümenin seçildiği dosyada eşik olarak yazılıdır, ancak bu matristeki
"önerilen blok" sütunu bir tasarım önerisidir, karar metni değildir.

## §16 Terim sözlüğü

| # | Terim | Türkçe karşılık | Kısa tanım | İlgili bölüm |
|---|---|---|---|---|
| 1 | f0 | Merkez frekans | Bant tepkisinin zirve konumu | §4 |
| 2 | Q | Kalite faktörü | Bant genişliğinin ters ölçüsü | §4 |
| 3 | Peaking | Tepe bant | f0 çevresinde yükselme/alçalma | §2 |
| 4 | Shelving | Yarım bant | Uç bantta sabit seviye kayması | §2 |
| 5 | Notch | Çentik | Dar frekans aralığında bastırma | §2 |
| 6 | Band-pass | Paso bant | Tek bant geçiren filtre | §2 |
| 7 | All-pass | Tüm geçiş | Genlik sabit, faz değişir | §2 |
| 8 | Biquad | İki kutuplu filtre | İkinci derece dijital filtre | §5 |
| 9 | TPT | Topoloji koruyan dönüşüm | Yüksek fs'te kararlı yapı | §5 |
| 10 | SVF | State-variable filtre | Çoklu çıkışlı esnek yapı | §5 |
| 11 | Ladder | Merdiven filtre | Kademeli kutup dizisi | §5 |
| 12 | Cutoff | Kesim frekansı | Filtre geçiş konumu | §4 |
| 13 | Denormal | Normal olmayan sayı | Çok küçük sayı, CPU riski | §6 |
| 14 | Headroom | Seviye payı | Kırpılmadan önceki boşluk | §7 |
| 15 | GR | Kazanç azaltımı | Dinamik bölümün uyguladığı düşüş | §7 |
| 16 | Nyquist | Örnekleme yarısı | fs / 2 sınır frekansı | §3 |
| 17 | fs | Örnekleme hızı | 48 kHz sartname değeri | §6 |
| 18 | Block | İşlem bloğu | 512 örnek, sartname | §8 |
| 19 | Dry-Wet | Kuru-ıslak karışım | Kısmi uygulama oranı | §9 |
| 20 | Bypass | Düz geçiş | Eşleme karşılaştırması | §2 |
| 21 | Preset | Önceden ayar | Kayıtlı bant değerleri kümesi | §1 |
| 22 | ISO 1/3 | Üçte bir oktav | Standart bant serisi | §3 |
| 23 | Moiré/alias | Binyapı | Örnekleme kaynaklı yan ürün | §5 |
| 24 | Phase margin | Faz payı | Kararlılık ölçütü | §5 |
| 25 | Group delay | Grup gecikmesi | Frekans-bağımlı gecikme | §5 |
| 26 | Impulse response | Dörtgen tepki | Ölçüm düzlemi | §10 |
| 27 | RTA | Gerçek zamanlı analiz | Spektrum gösterimi | §10 |
| 28 | LUFS | Loudness birimi | Ölçüm standardı birimi | §10 |
| 29 | True peak | Gerçek tepe | Örnekleme-arası tepe | §10 |
| 30 | Lookahead | İleri bakış | Tavan öncesi gecikme | §9 |
| 31 | Sidechain | Yan zincir | Kontrol yolu | §9 |
| 32 | Link | Kanal bağlantısı | Kanalların birlikte davranması | §9 |
| 33 | Smoothing | Yumuşatma | Parametre geçişi | §4 |
| 34 | Artifacts | Yan etki | İşlemden doğan bozulma | §6 |
| 35 | Clamp | Kısıtlama | Değeri sınıra çekmek | §7 |
| 36 | ADR-017 | Karar kaydı | float32 veri yolu kararı | §6 |
| 37 | ADR-019 | Karar kaydı | Per-OS player FROZEN | §8 |
| 38 | ADR-038 H001 | Karar kaydı | PCM3168A zorunluluğu | §8 |
| 39 | L0 | Altyapı katmanı | Motor ve platform kuralları | §8 |
| 40 | PLANNED | Planlandı | Uygulanmadı, sartname var | §8 |
| 41 | UNKNOWN | Bilinmiyor | Karar metni yok | §11 |
| 42 | VERIFICATION REQUIRED | Doğrulama gerekli | İddia kanıtlanmadı | §11 |
| 43 | Q3 | Açık soru | dynamics hiyerarşisi | §11 |
| 44 | Q4 | Açık soru | measurement/testing ayrımı | §11 |
| 45 | sartname | Ölçüm belgesi | 512/48k hedefi | §8 |
| 46 | electroncis | Elektronik (imla) | Vault'ta korunan yazım | §13 |
| 47 | recomenataion | Recommendation (imla) | K020 dizin adı, korunur | §13 |
| 48 | identitiy | Identity (imla) | Vault'ta korunan yazım | §13 |
| 49 | integraion | Integration (imla) | Vault'ta korunan yazım | §13 |
| 50 | comilance | Compliance (imla) | Vault'ta korunan yazım | §13 |

## §17 Karar günlüğü ve bağımlılık kaydı

| # | Kayıt | Tarih | Etkilenen | Bağımlılık | Durum |
|---|---|---|---|---|---|
| 1 | Kademe-2 kapsam kaydı üretildi | 2026-10-12 | equalizer | dsp-master | Mevcut |
| 2 | Derin dal (bu dosya) üretildi | 2026-10-12 | equalizer | equalizer-kapsam | Bu dosya |
| 3 | 31 bantlı EQ kodu envantere alındı | 2026-10-12 | AIEngine | — | Repo kanıtı |
| 4 | C++ motor envanteri 0 dosya | 2026-10-12 | K004 | build | Ölçüldü |
| 5 | JUCE sürüm kanıtı yok | 2026-10-12 | build | — | VERIFICATION REQUIRED |
| 6 | 5 URL HTTP 200 doğrulandı | 2026-10-12 | research | — | Ölçüldü |
| 7 | URL benzersizlik havuzu kuruldu | 2026-10-12 | research | — | 1031 kayıt |
| 8 | f0/Q/gain aralıkları yazılmadı | 2026-10-12 | equalizer | — | UNKNOWN |
| 9 | Topoloji seçimi yazılmadı | 2026-10-12 | equalizer | — | UNKNOWN |
| 10 | Ölçüm penceresi kararı yok | 2026-10-12 | measurement | — | UNKNOWN |
| 11 | Kazanç bütçesi eşiği yok | 2026-10-12 | dynamics | — | UNKNOWN |
| 12 | Preset biçimi kararı yok | 2026-10-12 | equalizer | — | UNKNOWN |
| 13 | 8.1 kanal eşlemesi PLANNED | 2026-10-12 | K003-audio | — | PLANNED |
| 14 | Ölçüm testi PLANNED | 2026-10-12 | measurement | — | PLANNED |
| 15 | EQ bypass testi tanımı yok | 2026-10-12 | testing | — | UNKNOWN |
| 16 | Kararlılık ölçümü yok | 2026-10-12 | testing | — | Ölçülmedi |
| 17 | fs değişimi senaryosu yok | 2026-10-12 | build | — | UNKNOWN |
| 18 | Blok boyu sabitliği sartnamede | 2026-10-12 | pipeline | sartname | Sartname |
| 19 | float32 kararı ADR-017'de | 2026-10-12 | ADR-017 | — | Mevcut |
| 20 | PCM3168A kararı ADR-038 H001'de | 2026-10-12 | ADR-038 | — | Mevcut |
| 21 | ASIO Exclusive Lock kuralı L0'da | 2026-10-12 | L0 | — | Mevcut |
| 22 | DC offset relö kuralı L0'da | 2026-10-12 | L0 | — | Mevcut |
| 23 | Tahsis yasak kuralı L0'da | 2026-10-12 | L0 | — | Mevcut |
| 24 | Kilit yasak kuralı L0'da | 2026-10-12 | L0 | — | Mevcut |
| 25 | noexcept kuralı L0'da | 2026-10-12 | L0 | — | Mevcut |
| 26 | Ölçüm standartları araştırma havuzunda | 2026-10-12 | research | — | Havuzda |
| 27 | Sınır çelişkisi tespit edilmedi | 2026-10-12 | filters | — | Gözlem |
| 28 | Sınır çelişkisi tespit edilmedi | 2026-10-12 | pipeline | — | Gözlem |
| 29 | Üst karar bekleniyor (Q3) | 2026-10-12 | architect | — | Bekliyor |
| 30 | Üst karar bekleniyor (Q4) | 2026-10-12 | architect | — | Bekliyor |

## §18 Denetim matrisi (L0 ↔ EQ topolojisi)

| # | Denetim | Yöntem | Sıklık | Sorumlu | Engel |
|---|---|---|---|---|---|
| 1 | Bant sayısı 31 mi | Kod sayımı | Her sürüm | embedded | Motor yok |
| 2 | Frekans tablosu ISO ile uyumlu mu | Tablo karşılaştırma | Her sürüm | embedded | Motor yok |
| 3 | Q kümeleri korunuyor mu | Kod okuma | Her sürüm | embedded | Motor yok |
| 4 | Tahsis callback içinde mi | Kod inceleme | Her PR | embedded | Motor yok |
| 5 | Mutex çağrısı var mı | Kod inceleme | Her PR | embedded | Motor yok |
| 6 | noexcept imzası tam mı | Derleme | Her derleme | build | Motor yok |
| 7 | Denormal temizliği uygulanmış mı | Performans testi | Sürüm | performance | Ölçüm yok |
| 8 | float32 kullanılıyor mu | Tip denetimi | Her PR | embedded | ADR-017 |
| 9 | alignas(64) uygulanmış mı | Kod inceleme | Sürüm | embedded | Ölçüm yok |
| 10 | 512 örnek blok doğrulandı mı | Sartname ölçümü | Sürüm | testing | Ölçüm PLANNED |
| 11 | 48 kHz doğrulandı mı | Sartname ölçümü | Sürüm | testing | Ölçüm PLANNED |
| 12 | Bypass A/B farkı ölçüldü mü | Listening testi | Sürüm | testing | Test tanımı yok |
| 13 | EQ tepkisi ölçüldü mü | FFT ölçümü | Sürüm | measurement | Ölçüm yok |
| 14 | Gerçek tepe korundu mu | Tavan ölçümü | Sürüm | limiter | Ölçüm yok |
| 15 | DC offset eşiği korundu mu | DC ölçümü | Donanım | audio-hw | Donanım yok |
| 16 | PCM5122 kullanılmıyor mu | BOM denetimi | Üretim | audio-hw | BOM yok |
| 17 | ASIO Exclusive ihlali var mı | Çalışma anı testi | Sürüm | win-sw | Ortam yok |
| 18 | Preset taşınabilirliği test edildi mi | Preset yükleme | Sürüm | testing | Biçim yok |
| 19 | fs değişimi test edildi mi | Ortam değiştirme | Sürüm | build | Karar yok |
| 20 | Blok boyu değişimi test edildi mi | Ortam değiştirme | Sürüm | pipeline | Karar yok |
| 21 | Ölçüm penceresi sabit mi | Ölçüm tekrarı | Sürüm | measurement | Q4 |
| 22 | Kazanç bütçesi aşıldı mı | Seviye kaydı | Sürüm | dynamics | Eşik yok |
| 23 | Reverb çarpanı doğru mu | Kod okuma | Her sürüm | embedded | Repo kanıtı var |
| 24 | 8.1 kanal eşlemesi tutarlı mı | Kanal testi | Sürüm | K003-audio | PLANNED |
| 25 | Sürüm kanıtı toplandı mı | Derleme kaydı | Sürüm | build | VERIFICATION REQUIRED |
| 26 | URL benzersizliği korundu mu | Vault ölçümü | Her dosya | vault | Ölçüm betiği |
| 27 | Mojibake yok mu | Byte ölçümü | Her dosya | vault | Ölçüm betiği |
| 28 | Dolgu dizgisi yok mu | Metin taraması | Her dosya | vault | Ölçüm betiği |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
