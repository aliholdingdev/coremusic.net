---
node_id: K004-dsp/delay/delay-dogrulama
node_type: module
status: draft
parent_id: K004-dsp/delay
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kanit
---

# delay — Doğrulama Kaydı

> **Node:** `K004-dsp/delay/delay-dogrulama` · **Type:** module · **Status:** draft · **Group:** kanit
> **Üst düğüm:** `K004-dsp/delay` · **Eşlik:** `delay-kapsam` (kapsam), `delay-ornekler` (kod)
> **Disk durumu:** K004-dsp **PLANNED** — C++/DSP motoru repoda yoktur (`engine/` dizini 0). Bu dosya
> **doğrulama planını** taşır; hiçbir gecikme ölçümü, doğruluk değeri veya pass/fail kaydı
> **gerçekleşmemiştir**. Ölçüm 512 örnek / 48 kHz sartnameye bağlıdır ve durumu PLANNED'dir.

## §A Doğrulama senaryoları

### A.1 Senaryo kayıt tablosu (validation register)

| # | Senaryo | Girdi | Beklenen davranış | Yöntem | Durum |
|---|---|---|---|---|---|
| V-1 | Gecikme doğruluğu | İmpuls (tek örnek) | Çıkışta hedef örnek kadar kayma | zaman ölçümü | PLANNED |
| V-2 | Gecikme doğruluğu (süre) | 1000 Hz sinüs | Faz kayması süreyle uyumlu | faz ölçümü | PLANNED |
| V-3 | Tam örnek gecikme | Tam sayı örnek | Kesirli interpolasyon devre dışı | birim testi | PLANNED |
| V-4 | Kesirli gecikme | Ondalık süre | Ara değer üretilir | birim testi | PLANNED |
| V-5 | Frekans düzgünlüğü | Süpürme sinyali | Girdi/çıkış genliği eş | frekans ölçümü | PLANNED |
| V-6 | Sessizlik (taban gürültüsü) | Sessiz girdi | Çıkışta ek gürültü yok | gürültü ölçümü | PLANNED |
| V-7 | Sıfır gecikme | 0 ms | Çıkış ≈ giriş (faz 0) | birim testi | PLANNED |
| V-8 | Maksimum gecikme | Üst sınır | Tampon aşmaz, taşma yok | sınır testi | PLANNED |
| V-9 | Taşma senaryosu | Sınır üstü istek | Clamp uygulanır | birim testi | PLANNED |
| V-10 | Geri besleme kararlılığı | Çarpan < 1.0 | Sönümü azalan yankı | zarf ölçümü | PLANNED |
| V-11 | Geri besleme sınırı | Çarpan = 1.0 | Sınır durum | sınır testi | PLANNED |
| V-12 | Geri besleme kararsızlık | Çarpan > 1.0 | Sınırsız büyüme (beklenen) | sınır testi | PLANNED |
| V-13 | Wet/dry mix | 0 / 0.5 / 1.0 | Karışım doğrusal | genlik ölçümü | PLANNED |
| V-14 | Süre değişimi | Ani istek | Tıklama yok (yumuşatma) | dinleme + ölçüm | PLANNED |
| V-15 | bypass eşitliği | Rastgele blok | Çıkış = giriş, bit-bayar | birim testi | PLANNED |
| V-16 | Parametre clamp | Sınır dışı süre | Değer sınıra çekilir | birim testi | PLANNED |
| V-17 | Atomik paylaşım | Yoğun UI yazımı | Torn read gözlenmez | stres testi | PLANNED |
| V-18 | Tahsis denetimi | Uzun çalışma | 0 malloc/new | profil | PLANNED |
| V-19 | Kilit denetimi | Uzun çalışma | 0 mutex | kod taraması | PLANNED |
| V-20 | Denormal | Uzun sessizlik | CPU şişmesi yok | profil | PLANNED |
| V-21 | Blok boyu | 512 çağrı | Her blokta tam 512 | birim testi | PLANNED |
| V-22 | Exclusive Lock | İkinci süreç açar | İkincisi başarısız | bütünleşik | PLANNED |
| V-23 | DC koruma | 0.5 V üstü offset | Röle tetiklenir | donanım testi | PLANNED |
| V-24 | Kanal izolasyonu | Çok kanallı giriş | Kanallar karışmaz | kanal ölçümü | PLANNED |

### A.2 Senaryo ayrıntıları

#### A.2.1 V-1 / V-2 — Gecikme doğruluğu

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Hedef gecikme | **UNKNOWN** | Q-D1 | Beklenen süre yok |
| İmpuls konumu | blok başı | yöntem | Seçim sabitlenmedi |
| Ölçüm çözünürlüğü | 1 örnek | mantık | Kesirlide ±1 örnek belirsizliği |
| Kabul toleransı | **UNKNOWN** | Q-V2 | Pass/fail bandı yok |
| Örnekleme | 48 kHz | sartname | ölçüm PLANNED |
| ms → örnek dönüşümü | süre × 48 | sartname | fs doğrulanmadı |
| Blok | 512 örnek | sartname | ölçüm PLANNED |
| Faz yöntemi | 1000 Hz sinüs | yöntem | Frekans sabitlenmedi |

#### A.2.2 V-4 / V-5 — Kesirli gecikme ve frekans düzgünlüğü

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Interpolasyon yöntemi | **UNKNOWN** | Q-D4 | Beklenen tepki yok |
| HF kaybı toleransı | **UNKNOWN** | — | Sınır yok |
| Süpürme aralığı | **UNKNOWN** | — | Alt/üst sınır sabitlenmedi |
| Ölçüm adımı | **UNKNOWN** | — | Çözünürlük yok |
| Tolerans bandı | **UNKNOWN** | Q-V2 | Kabul aralığı yok |
| Referans yol | dry | yöntem | Referans seçimi sabitlenmedi |

#### A.2.3 V-10 / V-11 / V-12 — Geri besleme kararlılığı

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Feedback çarpanı aralığı | **UNKNOWN** | Q-D3 | Sınır kararı yok |
| Sönümleme (damping) | **UNKNOWN** | — | Filtre tanımı yok |
| Yankı sayısı (echo count) | **UNKNOWN** | — | Beklenen tap sayısı yok |
| Zarf ölçüm penceresi | **UNKNOWN** | Q-V1 | Pencere sabitlenmedi |
| Kararlılık ölçütü | genlik ≤ başlangıç | mantık | Sınır çarpanı belirsiz |
| Klips kontrolü | ≤ 1.0 FS | mantık | Ölçüm PLANNED |

### A.3 Kabullenme kriterleri (acceptance)

| # | Kriter | Kanıt türü | Zorunlu mu | Durum |
|---|---|---|---|---|
| AC-1 | 512 örnek blok boyu korunur | birim testi | evet | PLANNED |
| AC-2 | 48 kHz süre dönüşümü | sartname | evet | PLANNED |
| AC-3 | `noexcept` imzası | derleyici | evet | FROZEN |
| AC-4 | 0 tahsis (audio thread) | profil | evet | PLANNED |
| AC-5 | 0 mutex (audio thread) | kod taraması | evet | PLANNED |
| AC-6 | `alignas(64)` doğrulama | `static_assert` | evet | FROZEN |
| AC-7 | Gecikme doğruluğu hedefe uyar | impuls ölçümü | evet | **UNKNOWN** (hedef yok) |
| AC-8 | Tıklama yokluğu | dinleme + ölçüm | evet | PLANNED |
| AC-9 | Frekans düzgünlüğü | spektrum ölçümü | evet | PLANNED |
| AC-10 | bypass bit-bayar | birim testi | evet | PLANNED |
| AC-11 | Parametre clamp | birim testi | evet | PLANNED |
| AC-12 | Torn read yok | stres testi | evet | PLANNED |
| AC-13 | Denormal temizliği | profil | evet | PLANNED |
| AC-14 | Kanal izolasyonu | kanal ölçümü | evet | PLANNED |
| AC-15 | Exclusive Lock davranışı | bütünleşik | evet | PLANNED |
| AC-16 | DC koruma (0.5 V) | donanım | evet | PLANNED |
| AC-17 | Sürüm kanıtı (JUCE 9) | belge | evet | **VERIFICATION REQUIRED** |
| AC-18 | Sürüm kanıtı (ASIO SDK 2.3.4) | belge | evet | **VERIFICATION REQUIRED** |
| AC-19 | Ölçüm verisi mevcut | rapor | evet | **UNKNOWN** |
| AC-20 | Kararlılık sınırı belgeli | karar kaydı | evet | **UNKNOWN** (Q-D3) |

### A.4 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Web Audio API `AudioBuffer`, örnekleme frekansı ve kanal verisiyle tanımlı ses örnek deposunu temsil eder | https://developer.mozilla.org/en-US/docs/Web/API/AudioBuffer | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| IETF RFC 3550, RTP için zaman damgası ve senkronizasyon kurallarını tanımlar (RTCP SR/RR ile ölçülen gecikme hesabı) | https://www.ietf.org/rfc/rfc3550.txt | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

> **Sınır notu:** Bu sayfalar **erişilebilirlik/sürüm doğrulamasıdır**; K004-dsp mimari kanıtı sayılmaz.
> Ölçüm sonucu, gecikme değeri veya feedback sınırı **içermez**. Sürüm iddiaları hâlâ
> **VERIFICATION REQUIRED**.

### A.5 Gerçek repo kanıtları (disk)

| Kaynak | Dosya | Satır | Gözlem |
|---|---|---|---|
| `Dosya:` | `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 54 | `clamp` — parametre sınırlama deseni |
| `Dosya:` | `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 57-59 | `edgeSnap` eşiği — sınır davranışı |
| `Dosya:` | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 33 | `process` — sıralı zincir yürütme |
| `Dosya:` | `api.coremusic.net/config/routes.php` | `Satır:` 24 | `return [` — yapılandırma tablosu başlangıcı |
| `Dosya:` | `shared/composer.json` | `Satır:` 24 | `"phpunit/phpunit": "^10.5"` — PHP test çatısı |

> Bunlar **gerçek repodan okunan satırlardır**. Delay'ın C++ karşılığı diskte **mevcut değildir**;
> PHP/JS satırları yalnızca yapısal analog taşır, DSP kodu olarak yorumlanamaz.

### A.6 Açık sorular (open questions)

| Q# | Soru | Doğrulamayı engelleyen | Durum |
|---|---|---|---|
| Q3 | dynamics ↔ compressor ↔ limiter hiyerarşisi | Zincir sırası belirsizliği | **AÇIK** |
| Q4 | measurement ↔ testing sınırı | Gecikme ölçümü kimin? | **AÇIK** |
| Q-D1 | Varsayılan gecikme süresi nedir? | V-1/V-2 hedef değeri | **UNKNOWN** |
| Q-D2 | Maksimum gecikme / tampon boyu nedir? | V-8/V-9 sınırı | **UNKNOWN** |
| Q-D3 | Feedback çarpanı aralığı nedir? | V-10/V-11/V-12 ölçütü | **UNKNOWN** |
| Q-D4 | Interpolasyon yöntemi nedir? | V-4/V-5 beklenen tepkisi | **UNKNOWN** |
| Q-D5 | Wet/dry mix varsayılanı nedir? | V-13 hedefi | **UNKNOWN** |
| Q-D6 | Süre yumuşatma yöntemi nedir? | V-14 kabul ölçütü | **UNKNOWN** |
| Q-D9 | Bypass kipinde tampon korunur mu? | V-15 beklenen davranışı | **UNKNOWN** |
| Q-V1 | Ölçüm ortamı (donanım + yazılım) nedir? | Tekrarlanabilirlik | **UNKNOWN** |
| Q-V2 | Kabul tolerans bandı nedir? | Pass/fail kararı | **UNKNOWN** |
| Q-V5 | Gürültü eşiği (noise floor) hedefi nedir? | V-6 ölçümü | **UNKNOWN** |

#### A.2.4 V-15 / V-16 / V-17 — bypass, clamp, atomik

| Öğe | Değer | Beklenen | Ölçüm | Durum |
|---|---|---|---|---|
| bypass girişi | rastgele blok | Çıkış = giriş | bit karşılaştırma | PLANNED |
| bypass gecikmesi | 0 örnek | Ek gecikme yok | zaman ölçümü | PLANNED |
| süre clamp alt | −100 ms | 0'a çekilir | birim testi | PLANNED |
| süre clamp üst | maksimum üstü | Sınıra çekilir | birim testi | PLANNED |
| feedback clamp | 2.0 | Sınırda durur | birim testi | PLANNED |
| Yoğun yazma | eşzamanlı UI | Torn read yok | stres testi | PLANNED |
| Okuma tutarlılığı | atomik anlık görüntü | Yarım değer yok | stres testi | PLANNED |

#### A.2.5 V-18 / V-19 / V-20 / V-21 — RT kısıt doğrulaması

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
| DK-3 | Ölçüm aracı seçilmedi | disk ölçümü | Gecikme ölçümü yapılamaz |
| DK-4 | CI hattı tanımsız | disk ölçümü | Otomatik kapı yok |
| DK-5 | Sürüm kanıtı yok | kök AGENTS.md §12 | Karşılaştırma yapılamaz |
| DK-6 | Ölçüm durumu PLANNED | sartname | 512/48 kHz doğrulanmadı |
| DK-7 | ASIO cihaz envanteri yok | disk ölçümü | Sistem testi engelli |
| DK-8 | Dinleme ortamı belgelenmedi | disk ölçümü | Subjectif test engelli |
| DK-9 | ADR yüzeyi yasağı | görev kuralları | Karar kaydı açılamaz |
| DK-10 | Bağımlılık bump yasağı | kök AGENTS.md §13 | Araç eklenemez |
| DK-11 | Hedef süre sabitlenmedi | Q-D1 | V-1 hedefi yok |
| DK-12 | Feedback sınırı yok | Q-D3 | Kararlılık ölçütü yok |

### A.5.1 Görev kuralları ile doğrulama ilişkisi

| Kural | Doğrulamadaki karşılığı | Sınav |
|---|---|---|
| Zero-allocation | V-18 / TS-9 | Tahsis 0 mı |
| Lock-free | V-19 / TS-10 | Mutex 0 mı |
| `noexcept` | AC-3 / TS-17 | İhlal var mı |
| `constexpr` | Kod incelemesi | Süre dönüşümü çalışma zamanı mı |
| `alignas(64)` | V-21 / AC-6 | `static_assert` geçti mi |
| float32 (ADR-017) | C.3 tablosu | Biçim doğru mu |
| 512 / 48 kHz | AC-1 / AC-2 | Sartnameye uygun mu |
| Exclusive Lock | V-22 | İkinci açılış reddedildi mi |
| DC 0.5 V | V-23 / AC-16 | Röle tetiklendi mi |
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
| TS-1 | ms → örnek dönüşümü | süre tablosu | doğru örnek sayısı | yüksek |
| TS-2 | Ring buffer sarılması | sınır konumlar | doğru okuma | yüksek |
| TS-3 | Tam örnek gecikme | impuls | hedef konumda tepe | yüksek |
| TS-4 | Kesirli interpolasyon | ara konum | beklenen ara değer | yüksek |
| TS-5 | Sıfır gecikme | giriş bloğu | çıkış ≈ giriş | yüksek |
| TS-6 | Clamp davranışı | sınır dışı süre | sınır değeri | yüksek |
| TS-7 | Feedback sınırı | çarpan tablosu | kararlılık kuralı | yüksek |
| TS-8 | Mix doğrusallığı | mix tablosu | doğrusal toplam | orta |
| TS-9 | Tahsis sayacı | 10⁶ blok | 0 tahsis | yüksek |
| TS-10 | Mutex sayacı | kod taraması | 0 mutex | yüksek |
| TS-11 | Denormal | sessiz blok | CPU sabit | orta |
| TS-12 | Blok boyu | değişen çağrı | 512 sabit | yüksek |
| TS-13 | Hizalama | `static_assert` | 64 bayt | orta |
| TS-14 | Kanal izolasyonu | çok kanallı | çapraz yok | yüksek |
| TS-15 | Kip geçişi | bypass↔aktif | tıkırtı yok | orta |
| TS-16 | Süre yumuşatma | ani istek | sıçrama yok | yüksek |
| TS-17 | `noexcept` denetimi | derleyici | ihlal yok | yüksek |
| TS-18 | Tampon taşması | maksimum süre | okuma geçerli | yüksek |
| TS-19 | Çoklu tap | tap listesi | her tap doğru | orta |
| TS-20 | Ölçüm ortamı envanteri | ortam kaydı | kayıt tam | orta |

### B.3 Kapsam ve kapsam dışı

| Alan | Kapsamda mı | Gerekçe |
|---|---|---|
| Gecikme tamponu mantığı | evet | Bu modülün sorumluluğu |
| Kesirli interpolasyon | evet | Bileşen D-5 |
| Geri besleme ve sönümleme | evet | Bileşen D-6 / D-7 |
| Reverb yansımaları | hayır | K004-dsp/reverb |
| Uzamsal dağıtım | hayır | K004-dsp/spatial-audio |
| Filtre tasarımı | hayır | K004-dsp/filters |
| Ölçüm altyapısı | kısmen | Q4 **AÇIK** — K004-dsp/measurement |
| Test harness'ı iskeleti | hayır | K004-dsp/testing |
| Zincir sırası | hayır | K004-dsp/pipeline |
| UI efekt kontrolü | hayır | Sunum katmanı |
| Donanım DC koruması | hayır | 0.5 V donanım katmanı |
| ASIO sürücü davranışı | kısmen | Dış bağımlılık, gözlem |

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
| 1 | RT ihlali (tahsis/mutex) | Xrun üretir | TS-9, TS-10 |
| 2 | Tıklama (ani süre değişimi) | Duyulabilir bozulma | TS-16, V-14 |
| 3 | Kararsız geri besleme | Klips / ısınma | TS-7, V-12 |
| 4 | Yanlış gecikme doğruluğu | Hedefe uyumsuzluk | TS-1, TS-3 |
| 5 | Parametre bozukluğu | Torn read | TS-6, V-17 |
| 6 | Cihaz kilidi | İkinci açılış | V-22 |
| 7 | Denormal | CPU şişmesi | TS-11 |
| 8 | Ölçüm altyapısı eksikliği | Doğrulanamaz | Q-V1 |

### B.6 Test verisi tablosu

| Veri seti | İçerik | Kullanım | Durum |
|---|---|---|---|
| DS-1 | İmpuls (tek örnek) | Gecikme doğruluğu | **UNKNOWN** (üretilmedi) |
| DS-2 | Süpürme sinüsü seti | Frekans düzgünlüğü | **UNKNOWN** |
| DS-3 | 1000 Hz sinüs | Faz ölçümü | **UNKNOWN** |
| DS-4 | Sessizlik | Denormal / taban gürültü | **UNKNOWN** |
| DS-5 | Müzik kaydı | Subjectif | **UNKNOWN** |
| DS-6 | Tam genlik sinüs | Klips kontrolü | **UNKNOWN** |
| DS-7 | Gölge (kısa kayıt) | Yankı zarfı | **UNKNOWN** |
| DS-8 | Çok kanallı test sinyali | Kanal izolasyonu | **UNKNOWN** |

## §C Ölçüm metodolojisi

### C.1 Ölçüm envanteri

| # | Ölçüm | Büyüklük | Birim | Araç | Durum |
|---|---|---|---|---|---|
| M-1 | Gecikme süresi | Giriş↔çıkış kayması | örnek / ms | zaman ölçümü | PLANNED |
| M-2 | Faz kayması | Faz vs frekans | derece | faz analizi | PLANNED |
| M-3 | Frekans tepkisi | Kazanç vs frekans | dB | süpürme | PLANNED |
| M-4 | Sönüm (echo) | Yankı genliği | dB/tap | zarf ölçümü | PLANNED |
| M-5 | Taban gürültüsü | Noise floor | dB | spektrum | PLANNED |
| M-6 | Distorsiyon | Harmonik bozulma | % | frekans analizi | PLANNED |
| M-7 | CPU kullanımı | İş yükü | % | profil | PLANNED |
| M-8 | Tahsis sayısı | Bellek | adet | sayacı | PLANNED |
| M-9 | Xrun sayısı | Kopma | adet | sürücü günlüğü | PLANNED |
| M-10 | Tıklama genliği | Geçiş sıçraması | dB | zaman serisi | PLANNED |
| M-11 | Kanal izolasyonu | Çapraz konuşma | dB | kanal ölçümü | PLANNED |
| M-12 | Kararlılık | Zarf büyümesi | dB | zarf ölçümü | PLANNED |

### C.2 Ölçüm zinciri (setup)

| Aşama | Girdi | İşlem | Çıktı | Not |
|---|---|---|---|---|
| S-1 | Test sinyali | Kaynak üretimi | Referans dosya | Üretim yöntemi **UNKNOWN** |
| S-2 | Referans | SUT'a besleme | İşlenmiş çıkış | 512/48 kHz |
| S-3 | Çıkış | Örnekleme | Ham dizi | 32-bit float (ADR-017) |
| S-4 | Ham dizi | Zaman/frekans analizi | Örnekleme serisi | Yöntem **UNKNOWN** |
| S-5 | Örnekleme serisi | Karşılaştırma | Hedef süre | Hedef yok (Q-D1) |
| S-6 | Sapma | İstatistik | Maks. sapma | Tolerans **UNKNOWN** |
| S-7 | Sonuç | Kayıt | Rapor satırı | Kalıcılık yolu **UNKNOWN** |

### C.3 Ölçüm koşulları

| Koşul | Değer | Kaynak | Durum |
|---|---|---|---|
| Örnekleme frekansı | 48000 Hz | sartname | FROZEN |
| Blok boyu | 512 örnek | sartname | FROZEN |
| Örnek biçimi | 32-bit float | ADR-017 | FROZEN |
| Hedef gecikme | **UNKNOWN** | Q-D1 | Ölçüm merkezi yok |
| Maksimum gecikme | **UNKNOWN** | Q-D2 | Sınır yok |
| Feedback çarpanı | **UNKNOWN** | Q-D3 | Kararlılık testi engelli |
| Interpolasyon | **UNKNOWN** | Q-D4 | Yöntem belirsiz |
| Referans seviyesi | **UNKNOWN** | — | Ölçüm PLANNED |
| Noise floor hedefi | **UNKNOWN** | Q-V5 | — |
| Cihaz modeli | **UNKNOWN** | — | ASIO cihazı bilinmiyor |
| Sürüm (JUCE) | "9" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |
| Sürüm (ASIO SDK) | "2.3.4" (iddia) | kök AGENTS.md §12 | **VERIFICATION REQUIRED** |

### C.4 Tekrarlanabilirlik (repeatability)

| Kural | Uygulama | Gerekçe | Durum |
|---|---|---|---|
| R-1 | Aynı test seti tekrar edilir | Karşılaştırılabilirlik | PLANNED |
| R-2 | Ortam değişkenleri kaydedilir | Kök neden analizi | PLANNED |
| R-3 | Sürüm bilgisi zorunlu | Regresyon takibi | **VERIFICATION REQUIRED** |
| R-4 | Ölçüm hattı sabit | Ölçüm hatası azaltma | PLANNED |
| R-5 | Ham veri saklanır | Yeniden analiz | PLANNED |
| R-6 | Belirsizlik yazılır | Sahte kesinlik yok | PLANNED |
| R-7 | fs sabitlenir (48 kHz) | Süre dönüşümü tutarlılığı | PLANNED |

### C.5 Ölçüm riskleri

| Risk | Etki | Azaltma | Kalan belirsizlik |
|---|---|---|---|
| Ölçüm aracı seçilmedi | Ölçüm yapılamaz | Araç seçimi kararı | **UNKNOWN** |
| Hedef süre yok | Sapma hesaplanamaz | Q-D1 kararı | **UNKNOWN** |
| Feedback sınırı yok | Kararlılık testi belirsiz | Q-D3 kararı | **UNKNOWN** |
| Ortam belgelenmedi | Sonuç tekrarlanamaz | Envante kaydı | **UNKNOWN** |
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
| Varsayılan gecikme | **UNKNOWN** | ms | Q-D1 | — |
| Maksimum gecikme | **UNKNOWN** | ms | Q-D2 | — |
| Feedback çarpanı | **UNKNOWN** | oran | Q-D3 | — |
| Interpolasyon yöntemi | **UNKNOWN** | enum | Q-D4 | — |
| Wet/dry mix | **UNKNOWN** | oran | Q-D5 | — |
| Süre yumuşatma | **UNKNOWN** | ms | Q-D6 | — |
| Tap sayısı | **UNKNOWN** | adet | Q-D8 | — |
| Gürültü eşiği | **UNKNOWN** | dB | Q-V5 | — |
| Distorsiyon eşiği | **UNKNOWN** | % | — | — |
| CPU bütçesi | **UNKNOWN** | % | — | — |
| Xrun toleransı | **UNKNOWN** | adet | Q-V6 | — |
| Gecikme doğruluk toleransı | **UNKNOWN** | örnek | Q-V2 | — |

### D.2 Eşik tablosu (thresholds)

| Eşik | Değer | Yön | Tepki | Durum |
|---|---|---|---|---|
| Klips sınırı | 1.0 FS | ≥ | Sınır/limit | FROZEN (mantık) |
| DC koruma | 0.5 V | > | Röle | FROZEN |
| Tahsis bütçesi | 0 | > | Xrun riski | FROZEN (kural) |
| Mutex bütçesi | 0 | > | Prioritevi inversion | FROZEN (kural) |
| Feedback kararlılık | **UNKNOWN** | ≥ | Isınma / klips | Q-D3 |
| Tıklama üstü | **UNKNOWN** | > | Duyulabilir pop | — |
| Gürültü üstü | **UNKNOWN** | > | Kalite ihlali | — |
| CPU üstü | **UNKNOWN** | > | Xrun riski | — |
| Gecikme sapması üstü | **UNKNOWN** | > | Sartname ihlali | Q-V2 |

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
| Gecikme süresi | karar beklenir | yok | **DUR** | Q-D1 **AÇIK** |

### D.4 Kanıt kaynakları

| Kaynak | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| `Dosya:` | `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 54 | `clamp` — sınırlama deseni |
| `Dosya:` | `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | `Satır:` 57-59 | `edgeSnap` — sınır eşiği |
| `Dosya:` | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | `Satır:` 33 | Zincir yürütme deseni |
| `Dosya:` | `api.coremusic.net/config/routes.php` | `Satır:` 24 | Yapılandırma tablosu başlangıcı |
| `Dosya:` | `shared/composer.json` | `Satır:` 24 | phpunit `^10.5` |

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
| CL-8 | Gecikme doğruluğu doğrulandı | Ölçüm yok | **UNKNOWN** |

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
| Hedef süre | sayı + birim | evet | boş | **UNKNOWN** (Q-D1) |
| Ölçülen süre | sayı + birim | evet | boş | — |
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
| Hedef süre yok (Q-D1) | hayır | — | — | **DUR** |
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
| 10 | Varsayılan gecikme süresi | **UNKNOWN** | Q-D1 |
| 11 | Feedback sınırı | **UNKNOWN** | Q-D3 |
| 12 | Ölçüm ortamı | **UNKNOWN** | Q-V1 |
| 13 | Kabul toleransları | **UNKNOWN** | Q-V2 |
| 14 | Interpolasyon yöntemi | **UNKNOWN** | Q-D4 |

### D.9 Kaynak ve yetki

| Öğe | Değer |
|---|---|
| Sahip | Bayram Ali / Vault Steward |
| Üst düğüm | `K004-dsp/delay` |
| Eşlik dosyalar | `delay-kapsam.md`, `delay-ornekler.md` |
| SSOT | `.ai/architecture/K004-dsp/dsp-master.md` §3 |
| Mod | Red Team · Human Mode · Truth Mode |
| Sürüm kanıtı durumu | **VERIFICATION REQUIRED** |
| Ölçüm durumu | **UNKNOWN** |
| Motor durumu | diskte **YOK** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
