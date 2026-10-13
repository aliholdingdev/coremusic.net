---
node_id: K004-dsp/compressor/compressor-dogrulama
node_type: module
status: draft
parent_id: K004-dsp/compressor
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kanit
---

# compressor — Doğrulama Kaydı

> **Node:** `K004-dsp/compressor/compressor-dogrulama` · **Type:** module · **Status:** draft · **Group:** kanit
> **Üst düğüm:** `K004-dsp/compressor` · **Eşlik:** `compressor-kapsam` (kapsam), `compressor-ornekler` (kod)
> **Disk durumu:** K004-dsp **PLANNED** — C++/DSP motoru repoda yoktur. Bu dosya **doğrulama planını** taşır;
> hiçbir ölçüm sonucu, gecikme değeri veya pass/fail kaydı **gerçekleşmemiştir**.

## §A Doğrulama senaryoları

### A.1 Senaryo kayıt tablosu (validation register)

| # | Senaryo | Girdi | Beklenen davranış | Yöntem | Durum |
|---|---|---|---|---|---|
| V-1 | Eşik altı sinyal | −40 dBFS sürekli sinüs | Kazanç değişmez (0 dB GR) | simülasyon | PLANNED |
| V-2 | Eşik üstü sabit sinyal | −6 dBFS sinüs, threshold eşiği üstü | Hedef ratio kadar azalır | simülasyon | PLANNED |
| V-3 | Sınır geçişi | −20 dBFS → −4 dBFS kademe | Kesintisiz, tıkırtısız geçiş | dinleme + ölçüm | PLANNED |
| V-4 | Attack davranışı | Ani tepe (transient) | GR hedefe doğru tek yönde ilerler | zarf ölçümü | PLANNED |
| V-5 | Release davranışı | Tepe sonrası sessizlik | GR sıfıra monoton döner | zarf ölçümü | PLANNED |
| V-6 | Histeresis / chattering | Eşik civarında salınım | Kazanç aç-kapa yapmaz | zaman serisi okuma | PLANNED |
| V-7 | Knee geçişi | soft-knee bölge içinde | Türev süreklidir (sıçrama yok) | eğri ölçümü | PLANNED |
| V-8 | ratio = 1 sınırı | Sınır durumu | Kazanç tam 0 dB | birim testi | PLANNED |
| V-9 | bypass modu | Herhangi bir girdi | Çıkış = giriş, bit-bayar | birim testi | PLANNED |
| V-10 | stereo linked | Farklı kanal seviyeleri | Tek ortak kazanç uygulanır | iki kanal ölçümü | PLANNED |
| V-11 | stereo linked kapalı | Farklı kanal seviyeleri | Her kanal kendi kazancını alır | iki kanal ölçümü | PLANNED |
| V-12 | Makeup kazancı | Sıkıştırılmış çıkış | Ortalama seviye geri gelir | RMS ölçümü | PLANNED |
| V-13 | GR işareti | Azaltım aktifken | GR negatif yön okunur | gösterge testi | PLANNED |
| V-14 | Parametre clamp | Aralık dışı yazım | Değer sınıra çekilir | birim testi | PLANNED |
| V-15 | Atomik paylaşım | Yoğun UI yazımı | Torn read gözlenmez | stres testi | PLANNED |
| V-16 | Tahsis denetimi | Uzun çalışma | 0 malloc/new | profil | PLANNED |
| V-17 | Kilit denetimi | Uzun çalışma | 0 mutex | kod taraması | PLANNED |
| V-18 | Denormal | Uzun sessizlik | CPU şişmesi yok | profil | PLANNED |
| V-19 | Buffer boyutu | 512 örnek | Her blokta tam boy | birim testi | PLANNED |
| V-20 | Örnekleme frekansı | 48 kHz | Zaman sabitleri buna göre | sartname | PLANNED |
| V-21 | Exclusive Lock | İkinci süreç açar | İkincisi başarısız | bütünleşik | PLANNED |
| V-22 | DC koruma | 0.5 V üstü offset | Röle tetiklenir | donanım testi | PLANNED |
| V-23 | Salınım kararlılığı | Yarı periyodik girdi | Pompa sınırlı kalır | ölçüm | PLANNED |
| V-24 | Klips yokluğu | Tam genlik girdi | Çıkış genliği | ≤ 1.0 FS | ölçüm | PLANNED |

### A.2 Senaryo ayrıntıları

#### A.2.1 V-1 / V-2 — Statik kazanç karakteristiği

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Girdi frekansı | 1000 Hz | sektör uygulaması | Ölçüm frekansı **UNKNOWN** (sartnamede yok) |
| Girdi genliği (V-1) | −40 dBFS | varsayım | Sabitlenmedi |
| Girdi genliği (V-2) | −6 dBFS | varsayım | Sabitlenmedi |
| Threshold | **UNKNOWN** | Q-C1 | Beklenen GR hesaplanamaz |
| Ratio | **UNKNOWN** | Q-C1 | Beklenen eğim hesaplanamaz |
| Beklenen GR (V-1) | 0 dB | mantıksal çıkarım | Eşik altı |
| Beklenen GR (V-2) | **UNKNOWN** | Q-C1 | Eşik + ratio gerekli |
| Tolerans | **UNKNOWN** | — | Kabul bandı sabitlenmedi |
| Örnekleme | 48 kHz | sartname | ölçüm PLANNED |
| Blok | 512 örnek | sartname | ölçüm PLANNED |

#### A.2.2 V-4 / V-5 — Attack ve release zarfı

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Attack süresi | **UNKNOWN** | Q-C1 | Hedef süre yok |
| Release süresi | **UNKNOWN** | Q-C1 | Hedef süre yok |
| Zarf ölçüm penceresi | **UNKNOWN** | Q-C6 | Pencere sabitlenmedi |
| Kabul kriteri (monotonluk) | Tek yönlü değişim | mantık | Aşım yönünde ters gitme yasak |
| Kabuk (overshoot) | İzin verilmez | mantık | Hedefi aşan GR bozulmadır |
| Tıklama testi | 0 duyulabilir pop | dinleme | Dinleyici sayısı **UNKNOWN** |

#### A.2.3 V-6 / V-7 — Karar bölgesi sürekliliği

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Knee genişliği | **UNKNOWN** | Q-C3 | Simülasyon engelli |
| Chattering tanımı | Kazançta aç-kapa | tanım | Ölçüt: ardışık ters yön sayısı |
| Türev sürekliliği | Sıçrama yok | tanım | soft-knee varsayımı |
| Ölçüm çözünürlüğü | **UNKNOWN** | — | Örnekleme adımı sabitlenmedi |

### A.3 Kabullenme kriterleri (acceptance)

| # | Kriter | Kanıt türü | Zorunlu mu | Durum |
|---|---|---|---|---|
| AC-1 | 512 örnek blok boyu korunur | birim testi | evet | PLANNED |
| AC-2 | 48 kHz zaman sabitleri | sartname | evet | PLANNED |
| AC-3 | `noexcept` imzası | derleyici | evet | FROZEN |
| AC-4 | 0 tahsis (audio thread) | profil | evet | PLANNED |
| AC-5 | 0 mutex (audio thread) | kod taraması | evet | PLANNED |
| AC-6 | `alignas(64)` doğrulama | `static_assert` | evet | FROZEN |
| AC-7 | Tıkırtısız geçiş | dinleme | evet | PLANNED |
| AC-8 | Kararlı zarf (salınım yok) | ölçüm | evet | PLANNED |
| AC-9 | Eğim hedefe uyar | eğri ölçümü | evet | PLANNED |
| AC-10 | GR raporlanır | gösterge | evet | PLANNED |
| AC-11 | bypass bit-bayar | birim testi | evet | PLANNED |
| AC-12 | Parametre clamp | birim testi | evet | PLANNED |
| AC-13 | Torn read yok | stres testi | evet | PLANNED |
| AC-14 | Denormal temizliği | profil | evet | PLANNED |
| AC-15 | Exclusive Lock davranışı | bütünleşik | evet | PLANNED |
| AC-16 | DC koruma (0.5 V) | donanım | evet | PLANNED |
| AC-17 | Sürüm kanıtı (JUCE 9) | belge | evet | **VERIFICATION REQUIRED** |
| AC-18 | Sürüm kanıtı (ASIO SDK 2.3.4) | belge | evet | **VERIFICATION REQUIRED** |
| AC-19 | Ölçüm verisi mevcut | rapor | evet | **UNKNOWN** |
| AC-20 | Ölçüm ortamı kayıtlı | envanter | evet | **UNKNOWN** |

### A.4 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Web Audio API `DynamicsCompressorNode.reduction` özelliği, uygulanan gain reduction değerini saniyede 20 kez günceller | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode/reduction | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| IEEE 260, ölçüm birimi için standart harf sembollerini tanımlar | https://standards.ieee.org/ieee/260/ | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

> **Sınır notu:** Bu sayfalar **erişilebilirlik/sürüm doğrulamasıdır**; K004-dsp mimari kanıtı sayılmaz.
> Ölçüm sonucu içermaz. Sürüm iddiaları hâlâ **VERIFICATION REQUIRED**.

### A.5 Gerçek repo kanıtları (disk)

| Kaynak | Dosya | Satır | Gözlem |
|---|---|---|---|
| `Dosya:` | `shared/tests/Middleware/MiddlewarePipelineTest.php` | `Satır:` 1 | `declare(strict_types=1)` — test dosyası strict modda |
| `Dosya:` | `shared/tests/Middleware/MiddlewarePipelineTest.php` | `Satır:` 6 | `use CoreMusic\Middleware\MiddlewarePipeline;` — testin hedefi |
| `Dosya:` | `shared/composer.json` | `Satır:` 24 | `"phpunit/phpunit": "^10.5"` — test çatısının sürüm aralığı |
| `Dosya:` | `shared/composer.json` | `Satır:` 8 | `"php": ">=8.4"` — çalışma zamanı alt sınırı |
| `Dosya:` | `shared/composer.json` | `Satır:` 6 | `"2.0.0"` — paket sürümü |

> Bunlar **PHP tarafındaki gerçek test altyapısı** kanıtlarıdır. Compressor'ın C++ testleri repoda **yoktur**.

### A.6 Açık sorular (open questions)

| Q# | Soru | Doğrulamayı engelleyen | Durum |
|---|---|---|---|
| Q3 | dynamics ↔ compressor ↔ limiter hiyerarşisi | Hangi sınırların kime ait olduğu | **AÇIK** |
| Q4 | measurement ↔ testing sınırı | Bu dosyanın yetki alanı | **AÇIK** |
| Q-V1 | Ölçüm ortamı (donanım + yazılım) nedir? | Tekrarlanabilirlik | **UNKNOWN** |
| Q-V2 | Kabul tolerans bandı nedir? | Pass/fail kararı | **UNKNOWN** |
| Q-V3 | Dinleme paneli büyüklüğü? | Subjectif test | **UNKNOWN** |
| Q-V4 | Test sinyali seti (sinüs/kayıt/narrowband)? | Senaryo girdileri | **UNKNOWN** |
| Q-V5 | Gürültü eşiği (noise floor) hedefi? | SNR ölçümü | **UNKNOWN** |
| Q-V6 | Kaç xrun toleransı? | Kararlılık ölçütü | **UNKNOWN** |
| Q-V7 | GR ölçüm penceresi ve birimi? | V-13 doğrulaması | **UNKNOWN** |

#### A.2.4 V-9 / V-10 / V-11 — bypass ve stereo linked

| Öğe | Değer | Beklenen | Ölçüm | Durum |
|---|---|---|---|---|
| bypass girişi | rastgele blok | Çıkış = giriş | bit karşılaştırma | PLANNED |
| bypass gecikmesi | 0 örnek | Ek gecikme yok | zaman ölçümü | PLANNED |
| linked açık: sol kanal | −6 dBFS | Ortak kazanç | iki kanal ölçümü | PLANNED |
| linked açık: sağ kanal | −20 dBFS | Aynı kazanç | iki kanal ölçümü | PLANNED |
| linked kapalı: sol kanal | −6 dBFS | Farklı kazanç | iki kanal ölçümü | PLANNED |
| linked kapalı: sağ kanal | −20 dBFS | Farklı kazanç | iki kanal ölçümü | PLANNED |
| Geçiş (linked ↔ bağımsız) | kip değişimi | Tıkırtısız | dinleme | PLANNED |
| Kanal gecikme farkı | — | 0 örnek | zaman ölçümü | PLANNED |

#### A.2.5 V-16 / V-17 / V-18 — RT kısıt doğrulaması

| Öğe | Yöntem | Eşik | Araç | Durum |
|---|---|---|---|---|
| Tahsis sayacı | ses thread'inde sayma | 0 | hook / sayacı | PLANNED |
| Mutex taraması | kaynak kod taraması | 0 | statik analiz | PLANNED |
| Kilitli bölge | sinyal-izleme | yok | kod incelemesi | PLANNED |
| Denormal | CPU profili | sabit CPU | profiler | PLANNED |
| `noexcept` | derleyici denetimi | ihlal yok | derleyici | FROZEN |
| Hizalama | `static_assert` | 64 bayt | derleyici | FROZEN |
| Blok taşması | sayaç | 0 | runtime bayrağı | PLANNED |
| Zaman aşımı (deadline) | callback süresi | < blok süresi | kronometre | PLANNED |

### A.4.1 Doğrulama ortamı kısıtları

| # | Kısıt | Kaynak | Etki |
|---|---|---|---|
| DK-1 | Engine dizini yok | disk ölçümü | C++ testi çalıştırılamaz |
| DK-2 | Test verisi üretilmedi | disk ölçümü | Senaryolar bekliyor |
| DK-3 | Ölçüm aracı seçilmedi | disk ölçümü | Ölçüm yapılamaz |
| DK-4 | CI hattı tanımsız | disk ölçümü | Otomatik kapı yok |
| DK-5 | Sürüm kanıtı yok | kök AGENTS.md §12 | Karşılaştırma yapılamaz |
| DK-6 | Ölçüm durumu PLANNED | sartname | 512/48 kHz doğrulanmadı |
| DK-7 | ASIO cihaz envanteri yok | disk ölçümü | Sistem testi engelli |
| DK-8 | Dinleme ortamı belgelenmedi | disk ölçümü | Subjectif test engelli |
| DK-9 | ADR yüzeyi yasağı | görev kuralları | Karar kaydı açılamaz |
| DK-10 | Bağımlılık bump yasağı | kök AGENTS.md §13 | Araç eklenemez |

### A.5.1 Görev kuralları ile doğrulama ilişkisi

| Kural | Doğrulamadaki karşılığı | Sınav |
|---|---|---|
| Zero-allocation | TS-12 / M-7 | Tahsis 0 mı |
| Lock-free | TS-13 | Mutex 0 mı |
| `noexcept` | AC-3 / TS-19 | İhlal var mı |
| `constexpr` | Kod incelemesi | Çalışma zamanı hesabı var mı |
| `alignas(64)` | AC-6 | `static_assert` geçti mi |
| float32 (ADR-017) | C.3 tablosu | Biçim doğru mu |
| 512 / 48 kHz | AC-1 / AC-2 | Sartnameye uygun mu |
| Exclusive Lock | V-21 | İkinci açılış reddedildi mi |
| DC 0.5 V | V-22 / AC-16 | Röle tetiklendi mi |
| PCM3168A (ADR-038 H001) | Donanım testi | DAC doğrulandı mı |

## §B Test stratejisi

### B.1 Test piramidi

| Katman | Tür | Kapsam | Araç | Hedef |
|---|---|---|---|---|
| T-1 | Birim | Her bileşen izole | framework | yüksek kapsam |
| T-2 | Bileşen | Bileşenler arası | framework | sözleşmeler |
| T-3 | Bütünleşik | Zincir + bypass | harness | uçtan uca |
| T-4 | Sistem | Gerçek ASIO cihazı | manuel | cihaz davranışı |
| T-5 | Regresyon | Bilinen durumlar | CI | geriye dönük güvenlik |
| T-6 | Performans | CPU / tahsis | profil | RT uygunluğu |
| T-7 | Duyusal | Subjectif dinleme | panel | algısal kalite |

### B.2 Test türü envanteri

| # | Test | Girdi | Assert | Öncelik |
|---|---|---|---|---|
| TS-1 | Detector doğrusallığı | rampa | Ölçülen = beklenen | yüksek |
| TS-2 | dB dönüşümü | bilinen genlik | bilinen dB değeri | yüksek |
| TS-3 | Eşik karşılaştırma | tablo girdileri | doğru aşım | yüksek |
| TS-4 | Eğim hesabı | ratio tablosu | doğru kazanç | yüksek |
| TS-5 | Attack monotonluğu | kademe | tek yönlü GR | yüksek |
| TS-6 | Release monotonluğu | kademe | tek yönlü GR | yüksek |
| TS-7 | ratio = 1 | sınır | 0 dB kazanç | yüksek |
| TS-8 | ratio → ∞ | sınır | limiter davranışı | orta |
| TS-9 | bypass eşitliği | rastgele blok | bit-bayar | yüksek |
| TS-10 | Clamp | aralık dışı | sınır değeri | yüksek |
| TS-11 | Atomik paylaşım | eşzamanlı yazma | tutarlı okuma | yüksek |
| TS-12 | Tahsis sayacı | 10⁶ blok | 0 tahsis | yüksek |
| TS-13 | Mutex sayacı | kod taraması | 0 mutex | yüksek |
| TS-14 | Denormal | sessiz blok | CPU sabit | orta |
| TS-15 | Stereo linked | asimetrik giriş | tek kazanç | orta |
| TS-16 | GR işareti | azaltım | doğru yön | orta |
| TS-17 | Statik eğri | süpürme | hedef eğim | yüksek |
| TS-18 | Kip geçişi | bypass↔aktif | tıkırtı yok | orta |
| TS-19 | Blok boyu | değişen çağrı | 512 sabit | yüksek |
| TS-20 | Hizalama | `static_assert` | 64 bayt | orta |

### B.3 Kapsam ve kapsam dışı

| Alan | Kapsamda mı | Gerekçe |
|---|---|---|
| Compressor çekirdek mantığı | evet | Bu modülün sorumluluğu |
| Detector matematiği | evet | Bileşen B-1 |
| Attack/release zarfı | evet | Bileşen B-5 / B-6 |
| Limiter davranışının sınırı | hayır | Q3 **AÇIK** — K004-dsp/limiter |
| Equalizer bantları | hayır | K004-dsp/equalizer |
| Crossover filtreleri | hayır | K004-dsp/crossover |
| Ölçüm altyapısı | kısmen | Q4 **AÇIK** — K004-dsp/measurement |
| Test harness'ı iskeleti | hayır | K004-dsp/testing |
| UI gösterge çizimi | hayır | Sunum katmanı |
| Donanım DC koruması | hayır | 0.5 V donanım katmanı |
| ASIO sürücü davranışı | kısmen | Dış bağımlılık, gözlem |
| C++ derleme zinciri | evet | Ama proje dosyası **UNKNOWN** |

### B.4 Test ortamı ve araçları

| Öğe | Değer | Kaynak | Durum |
|---|---|---|---|
| Test çatısı (PHP tarafı) | phpunit `^10.5` | `shared/composer.json` `Satır:` 24 | doğrulandı |
| PHP sürüm sınırı | `>=8.4` | `shared/composer.json` `Satır:` 8 | doğrulandı |
| Strict tipler | `declare(strict_types=1)` | `MiddlewarePipelineTest.php` `Satır:` 1 | doğrulandı |
| C++ test çatısı | — | Repoda yok | **UNKNOWN** |
| C++ derleyici | C++20 (kural) | kök AGENTS.md §5 | **VERIFICATION REQUIRED** |
| CI hattı | — | K004 için yok | **UNKNOWN** |
| Profil aracı | — | Seçilmedi | **UNKNOWN** |
| Ses ölçüm aracı | — | Seçilmedi | **UNKNOWN** |
| Dinleme ortamı | — | Belgelenmedi | **UNKNOWN** |
| Sürüm kilidi | — | lockfile yok | **UNKNOWN** |

### B.5 Test öncelik sırası (risk odaklı)

| Sıra | Risk | Neden | İlk test |
|---|---|---|---|
| 1 | RT ihlali (tahsis/mutex) | Xrun üretir | TS-12, TS-13 |
| 2 | Tıkırtı / kopma | Duyulabilir bozulma | TS-18, V-3 |
| 3 | Kararsız zarf | Pompa / salınım | TS-5, TS-6 |
| 4 | Yanlış eğim | Karakter hatası | TS-4, TS-17 |
| 5 | Parametre bozukluğu | Torn read | TS-11, TS-10 |
| 6 | Cihaz kilidi | İkinci açılış | V-21 |
| 7 | Denormal | CPU şişmesi | TS-14 |
| 8 | Ölçüm altyapısı eksikliği | Doğrulanamaz | Q-V1 |

### B.6 Test verisi tablosu

| Veri seti | İçerik | Kullanım | Durum |
|---|---|---|---|
| DS-1 | Tek frekans sinüs seti | Statik karakteristik | **UNKNOWN** (üretilmedi) |
| DS-2 | Kademe (step) sinyali | Attack/release | **UNKNOWN** |
| DS-3 | Ani tepe (impulse benzeri) | Transient davranış | **UNKNOWN** |
| DS-4 | Dar bant gürültü | Kararlılık | **UNKNOWN** |
| DS-5 | Müzik kaydı | Subjectif | **UNKNOWN** |
| DS-6 | Tam genlik sinüs | Klips kontrolü | **UNKNOWN** |
| DS-7 | Sessizlik | Denormal | **UNKNOWN** |
| DS-8 | Asimetrik stereo | Linked testi | **UNKNOWN** |

## §C Ölçüm metodolojisi

### C.1 Ölçüm envanteri

| # | Ölçüm | Büyüklük | Birim | Araç | Durum |
|---|---|---|---|---|---|
| M-1 | Gain reduction | Kazanç azaltımı | dB | zarf analizi | PLANNED |
| M-2 | Giriş/çıkış eğimi | Kazanç karakteristiği | dB/dB | süpürme | PLANNED |
| M-3 | Sinyal-gürültü oranı | Noise floor | dB | spektrum | PLANNED |
| M-4 | Distorsiyon | Harmonik bozulma | % | frekans analizi | PLANNED |
| M-5 | Gecikme | Blok gecikmesi | örnek | zaman ölçümü | PLANNED |
| M-6 | CPU kullanımı | İş yükü | % | profil | PLANNED |
| M-7 | Tahsis sayısı | Bellek | adet | sayacı | PLANNED |
| M-8 | Xrun sayısı | Kopma | adet | sürücü günlüğü | PLANNED |
| M-9 | Zarf tepe değeri | Overshoot | dB | zarf analizi | PLANNED |
| M-10 | Salınım genliği | Pompa | dB | zarf analizi | PLANNED |
| M-11 | Geçiş süresi | Attack/release | ms | kademe ölçümü | PLANNED |
| M-12 | Eşik civarı titreşim | Chattering | adet/s | zaman serisi | PLANNED |

### C.2 Ölçüm zinciri (setup)

| Aşama | Girdi | İşlem | Çıktı | Not |
|---|---|---|---|---|
| S-1 | Test sinyali | Kaynak üretimi | Referans dosya | Üretim yöntemi **UNKNOWN** |
| S-2 | Referans | SUT'a besleme | İşlenmiş çıkış | 512/48 kHz |
| S-3 | Çıkış | Örnekleme | Ham dizi | 32-bit float (ADR-017) |
| S-4 | Ham dizi | Zarf çıkarımı | GR serisi | Yöntem **UNKNOWN** |
| S-5 | GR serisi | İstatistik | Ortalama/tepe/pencere | Pencere **UNKNOWN** |
| S-6 | İstatistik | Karşılaştırma | Kabul/reddet | Tolerans **UNKNOWN** |
| S-7 | Sonuç | Kayıt | Rapor satırı | Kalıcılık yolu **UNKNOWN** |

### C.3 Ölçüm koşulları

| Koşul | Değer | Kaynak | Durum |
|---|---|---|---|
| Örnekleme frekansı | 48000 Hz | sartname | FROZEN |
| Blok boyu | 512 örnek | sartname | FROZEN |
| Örnek biçimi | 32-bit float | ADR-017 | FROZEN |
| Referans seviyesi | **UNKNOWN** | — | Ölçüm PLANNED |
| Noise floor hedefi | **UNKNOWN** | Q-V5 | — |
| Sıcaklık / nem | **UNKNOWN** | — | Donanım testi için |
| Cihaz modeli | **UNKNOWN** | — | ASIO cihazı bilinmiyor |
| Sürüm (JUCE) | "9" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |
| Sürüm (ASIO SDK) | "2.3.4" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |
| Panel büyüklüğü | **UNKNOWN** | Q-V3 | Subjectif test |

### C.4 Tekrarlanabilirlik (repeatability)

| Kural | Uygulama | Gerekçe | Durum |
|---|---|---|---|
| R-1 | Aynı test seti tekrar edilir | Karşılaştırılabilirlik | PLANNED |
| R-2 | Ortam değişkenleri kaydedilir | Kök neden analizi | PLANNED |
| R-3 | Sürüm bilgisi zorunlu | Regresyon takibi | **VERIFICATION REQUIRED** |
| R-4 | Ölçüm hattı sabit | Ölçüm hatası azaltma | PLANNED |
| R-5 | Ham veri saklanır | Yeniden analiz | PLANNED |
| R-6 | Belirsizlik yazılır | Sahte kesinlik yok | PLANNED |

### C.5 Ölçüm riskleri

| Risk | Etki | Azaltma | Kalan belirsizlik |
|---|---|---|---|
| Ölçüm aracı seçilmedi | Ölçüm yapılamaz | Araç seçimi kararı | **UNKNOWN** |
| Ortam belgelenmedi | Sonuç tekrarlanamaz | Envante kaydı | **UNKNOWN** |
| Tolerans yok | Pass/fail belirsiz | Eşik kararı | **UNKNOWN** |
| N = 1 ölçüm | İstatistik yok | Tekrar sayısı | **UNKNOWN** |
| Subjectif test | Önyargılı sonuç | Panel protokolü | **UNKNOWN** |
| Sürüm kanıtsız | Karşılaştırma anlamsız | Belge toplama | **VERIFICATION REQUIRED** |

## §D Referans değerler ve eşikler

### D.1 Referans tablosu (tümü sabitlenmemiş)

| Parametre | Referans | Birim | Kaynak | Durum |
|---|---|---|---|---|
| Örnekleme frekansı | 48000 | Hz | sartname | FROZEN |
| Blok boyu | 512 | örnek | sartname | FROZEN |
| Örnek biçimi | float32 | bit | ADR-017 | FROZEN |
| Hizalama | 64 | bayt | kök AGENTS.md §5 | FROZEN |
| DC koruma eşiği | 0.5 | V | kök AGENTS.md §10 | FROZEN |
| Varsayılan threshold | **UNKNOWN** | dBFS | Q-C1 | — |
| Varsayılan ratio | **UNKNOWN** | N:1 | Q-C1 | — |
| Varsayılan attack | **UNKNOWN** | ms | Q-C1 | — |
| Varsayılan release | **UNKNOWN** | ms | Q-C1 | — |
| Knee genişliği | **UNKNOWN** | dB | Q-C3 | — |
| Makeup kazancı | **UNKNOWN** | dB | Q-C4 | — |
| GR ölçüm penceresi | **UNKNOWN** | ms | Q-C6 | — |
| Maksimum GR | **UNKNOWN** | dB | — | — |
| Noise floor hedefi | **UNKNOWN** | dB | Q-V5 | — |
| Distorsiyon eşiği | **UNKNOWN** | % | — | — |
| CPU bütçesi | **UNKNOWN** | % | — | — |
| Xrun toleransı | **UNKNOWN** | adet | Q-V6 | — |
| Dinleme paneli | **UNKNOWN** | kişi | Q-V3 | — |

### D.2 Eşik tablosu (thresholds)

| Eşik | Değer | Yön | Tepki | Durum |
|---|---|---|---|---|
| Klips sınırı | 1.0 FS | ≥ | Sıkıştırma/limit | FROZEN (mantık) |
| DC koruma | 0.5 V | > | Röle | FROZEN |
| Tahsis bütçesi | 0 | > | Xrun riski | FROZEN (kural) |
| Mutex bütçesi | 0 | > | Prioritevi inversion | FROZEN (kural) |
| GR sunum üstü | **UNKNOWN** | > | Gösterge taşması | — |
| Pompa üstü | **UNKNOWN** | > | Duyulabilir | — |
| CPU üstü | **UNKNOWN** | > | Xrun riski | — |
| Distorsiyon üstü | **UNKNOWN** | > | Kalite ihlali | — |
| Gecikme üstü | **UNKNOWN** | > | Sartname ihlali | — |

### D.3 Şartname ile karşılaştırma

| Şart | Şartname değeri | Ölçülen | Karar | Not |
|---|---|---|---|---|
| Örnekleme | 48 kHz | **UNKNOWN** | bekliyor | Ölçüm PLANNED |
| Blok | 512 | **UNKNOWN** | bekliyor | Ölçüm PLANNED |
| Biçim | float32 | **UNKNOWN** | bekliyor | Statik doğrulanabilir |
| Tahsis | 0 | **UNKNOWN** | bekliyor | Profil gerekli |
| Mutex | 0 | **UNKNOWN** | bekliyor | Tarama gerekli |
| Sürüm kanıtı | JUCE 9 / ASIO 2.3.4 | yok | **DUR** | **VERIFICATION REQUIRED** |
| Engine varlığı | motor beklenir | diskte yok | **DUR** | **UNKNOWN** |

### D.4 Kanıt kaynakları

| Kaynak | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| `Dosya:` | `shared/tests/Middleware/MiddlewarePipelineTest.php` | `Satır:` 1 | strict_types kullanımı |
| `Dosya:` | `shared/tests/Middleware/MiddlewarePipelineTest.php` | `Satır:` 6 | Test hedefi `MiddlewarePipeline` |
| `Dosya:` | `shared/composer.json` | `Satır:` 24 | phpunit `^10.5` |
| `Dosya:` | `shared/composer.json` | `Satır:` 8 | PHP `>=8.4` |
| `Kaynak:` | `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 54 | `clamp` — sınırlama deseni JS'te mevcut |

### D.5 Doğrulama beyanı (claim register)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| CL-1 | Doğrulama planı yazıldı | Bu dosya | evet |
| CL-2 | Ölçüm yapıldı | Yok | **UNKNOWN** |
| CL-3 | Test çalıştırıldı | Yok | **UNKNOWN** |
| CL-4 | C++ kodu derlendi | Motor yok | **UNKNOWN** |
| CL-5 | Sürüm kanıtlandı | Belge yok | **VERIFICATION REQUIRED** |
| CL-6 | 512 / 48 kHz doğrulandı | sartname (ölçüm yok) | **UNKNOWN** |
| CL-7 | 0 tahsis doğrulandı | Yok | **UNKNOWN** |

### D.6 Ölçüm kayıt şablonu (henüz doldurulmadı)

| Alan | Şema | Zorunlu | Durum | Not |
|---|---|---|---|---|
| Kayıt ID | metin | evet | boş | Üretilmedi |
| Tarih | YYYY-MM-DD | evet | boş | Üretilmedi |
| Ortam | donanım + yazılım | evet | boş | Q-V1 |
| Sürüm (JUCE) | metin | evet | boş | **VERIFICATION REQUIRED** |
| Sürüm (ASIO SDK) | metin | evet | boş | **VERIFICATION REQUIRED** |
| Örnekleme | sayı | evet | boş | 48 kHz beklenir |
| Blok boyu | sayı | evet | boş | 512 beklenir |
| Girdi veri seti | DS-x | evet | boş | DS tablosu boş |
| Senaryo | V-x | evet | boş | — |
| Ölçüm | M-x | evet | boş | — |
| Ölçülen değer | sayı + birim | evet | boş | — |
| Beklenen değer | sayı + birim | evet | boş | Çoğu **UNKNOWN** |
| Tolerans | sayı | evet | boş | **UNKNOWN** |
| Karar | PASS / FAIL / BEKLİYOR | evet | boş | — |
| Not | serbest metin | hayır | boş | — |

### D.7 Karar tablosu (decision table)

| Koşul | Kanıt var mı | Tolerans var mı | Sürüm kanıtı | Karar |
|---|---|---|---|---|
| Ölçüm yok | hayır | hayır | hayır | **BEKLİYOR** |
| Ölçüm var, tolerans yok | evet | hayır | evet | **VERIFICATION REQUIRED** |
| Ölçüm var, sürüm yok | evet | evet | hayır | **VERIFICATION REQUIRED** |
| Ölçüm var, tamamı tam | evet | evet | evet | PASS/FAIL yazılabilir |
| Motor yok (engine YOK) | hayır | — | — | **UNKNOWN** |
| Bağımlılık bump isteği | — | — | — | **DUR + handover** |

### D.8 Doğrulama hakkında bilinenler / bilinmeyenler

| # | Bilgi | Durum | Kaynak |
|---|---|---|---|
| 1 | Buffer 512 örnek / 48 kHz sartnamede | biliniyor (sartname) | sartname |
| 2 | Buffer gerçekten 512 / 48 kHz | **UNKNOWN** | ölçüm PLANNED |
| 3 | Ses biçimi float32 | biliniyor (karar) | ADR-017 |
| 4 | Motor repoda mevcut | biliniyor: **YOK** | disk ölçümü |
| 5 | Test altyapısı PHP tarafında var | biliniyor | composer.json `Satır:` 24 |
| 6 | C++ test altyapısı | **UNKNOWN** | dosya yok |
| 7 | JUCE sürümü | **VERIFICATION REQUIRED** | belge yok |
| 8 | ASIO SDK sürümü | **VERIFICATION REQUIRED** | belge yok |
| 9 | Ölçüm verisi | **UNKNOWN** | ölçüm PLANNED |
| 10 | Varsayılan parametreler | **UNKNOWN** | Q-C1 |
| 11 | Kabul toleransları | **UNKNOWN** | Q-V2 |
| 12 | Ölçüm ortamı | **UNKNOWN** | Q-V1 |

### D.9 Kaynak ve yetki

| Öğe | Değer |
|---|---|
| Sahip | Bayram Ali / Vault Steward |
| Üst düğüm | `K004-dsp/compressor` |
| Eşlik dosyalar | `compressor-kapsam.md`, `compressor-ornekler.md` |
| SSOT | `.ai/architecture/K004-dsp/dsp-master.md` §3 |
| Mod | Red Team · Human Mode · Truth Mode |
| Sürüm kanıtı durumu | **VERIFICATION REQUIRED** |
| Ölçüm durumu | **UNKNOWN** |
| Motor durumu | diskte **YOK** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
