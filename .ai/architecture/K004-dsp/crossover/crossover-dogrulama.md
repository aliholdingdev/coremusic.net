---
node_id: K004-dsp/crossover/crossover-dogrulama
node_type: module
status: draft
parent_id: K004-dsp/crossover
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kanit
---

# crossover — Doğrulama Kaydı

> **Node:** `K004-dsp/crossover/crossover-dogrulama` · **Type:** module · **Status:** draft · **Group:** kanit
> **Üst düğüm:** `K004-dsp/crossover` · **Eşlik:** `crossover-kapsam` (kapsam), `crossover-ornekler` (kod)
> **Disk durumu:** K004-dsp **PLANNED** — C++/DSP motoru repoda yoktur (`engine/` dizini 0). Bu dosya
> **doğrulama planını** taşır; hiçbir ölçüm sonucu, faz eğrisi, kazanç değeri veya pass/fail kaydı
> **gerçekleşmemiştir**. Ölçüm 512 örnek / 48 kHz sartnameye bağlıdır ve durumu PLANNED'dir.

## §A Doğrulama senaryoları

### A.1 Senaryo kayıt tablosu (validation register)

| # | Senaryo | Girdi | Beklenen davranış | Yöntem | Durum |
|---|---|---|---|---|---|
| V-1 | Bölme tepkisi (magnitude) | Süpürme sinyali | Bölme noktasında hedef eğim | frekans ölçümü | PLANNED |
| V-2 | Bant ayrımı | Üst bantta sinyal | Alt bantta hedef sönümleme | frekans ölçümü | PLANNED |
| V-3 | Faz uyumu | İki yol girişi | Birleştirmede faz iptali yok | faz ölçümü | PLANNED |
| V-4 | Kazanç birleşimi | Bölme noktası sinüsü | Düz tepki (toplam ≈ 1.0) | genlik ölçümü | PLANNED |
| V-5 | LPF derece etkisi | Sabit bölme frekansı | Eğim dereceyle artar | eğri ölçümü | PLANNED |
| V-6 | HPF derece etkisi | Sabit bölme frekansı | Eğim dereceyle artar | eğri ölçümü | PLANNED |
| V-7 | 3+ yollu yapı | Orta bant girişi | Band-pass doğru yerde | frekans ölçümü | PLANNED |
| V-8 | Bypass eşitliği | Rastgele blok | Çıkış = giriş, bit-bayar | birim testi | PLANNED |
| V-9 | Bypass geçişi | Kip değişimi | Tıkırtısız yumuşak geçiş | dinleme + ölçüm | PLANNED |
| V-10 | Parametre clamp | Bölme frekansı aralık dışı | Değer sınıra çekilir | birim testi | PLANNED |
| V-11 | Atomik paylaşım | Yoğun UI yazımı | Torn read gözlenmez | stres testi | PLANNED |
| V-12 | Tahsis denetimi | Uzun çalışma | 0 malloc/new | profil | PLANNED |
| V-13 | Kilit denetimi | Uzun çalışma | 0 mutex | kod taraması | PLANNED |
| V-14 | Denormal | Uzun sessizlik | CPU şişmesi yok | profil | PLANNED |
| V-15 | Blok boyu | 512 çağrı | Her blokta tam 512 | birim testi | PLANNED |
| V-16 | Örnekleme frekansı | 48 kHz | Katsayılar bu fs ile üretildi | sartname + log | PLANNED |
| V-17 | Hizalama | Durum yapısı | 64 bayt hizalı | `static_assert` | FROZEN |
| V-18 | Kanal gecikme farkı | Çok kanallı çıkış | Kanallar arası 0 örnek fark | zaman ölçümü | PLANNED |
| V-19 | Bass management | Bass bant | Sub/LFE'e doğru yönlendirme | kanal izleme | PLANNED |
| V-20 | Kanal eşleme | Bant + harita | Doğru çıkış kanalında ses | kanal izleme | PLANNED |
| V-21 | Kutup kararsızlığı | Katsayı kayması testi | Filtre ısınmaz, CPU sabit | profil | PLANNED |
| V-22 | Exclusive Lock | İkinci süreç açar | İkincisi başarısız | bütünleşik | PLANNED |
| V-23 | DC koruma | 0.5 V üstü offset | Röle tetiklenir | donanım testi | PLANNED |
| V-24 | Gecikme bütçesi | Blok işleme | Ölçülmüş ve raporlanan | zaman ölçümü | **UNKNOWN** |

### A.2 Senaryo ayrıntıları

#### A.2.1 V-1 / V-2 — Bölme tepkisi ve bant ayrımı

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Bölme frekansı | **UNKNOWN** | Q-X2 | Süpürme merkezi sabitlenemez |
| Eğim hedefi (dB/oct) | **UNKNOWN** | Q-X3 | Beklenen değer yok |
| Filtre tipi | **UNKNOWN** | Q-X3 | Butterworth / Linkwitz-Riley kararı yok |
| Süpürme aralığı | **UNKNOWN** | — | Alt/üst sınır sabitlenmedi |
| Ölçüm adımı | **UNKNOWN** | — | Çözünürlük sabitlenmedi |
| Bant ayrımı beklentisi | **UNKNOWN** | Q-X1 | Bant sayısı bilinmiyor |
| Tolerans bandı | **UNKNOWN** | Q-V2 | Kabul aralığı yok |
| Örnekleme | 48 kHz | sartname | ölçüm PLANNED |
| Blok | 512 örnek | sartname | ölçüm PLANNED |

#### A.2.2 V-3 / V-4 — Faz uyumu ve kazanç birleşimi

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Faz ölçüm yöntemi | **UNKNOWN** | Q-X7 | Araç seçilmedi |
| Grup gecikmesi düzeltmesi | **UNKNOWN** | Q-X7 | Algoritma belirsiz |
| Birleşim katsayısı | **UNKNOWN** | Q-X3 | Toplam ≈ 1.0 şartı kanıtlanamaz |
| Bölme noktasında genlik | Düz tepki hedefi | mantık | Aşım veya çukur istenmez |
| Faz iptali testi | İki yol aynı girdi | yöntem | İşaret tersse çıkış sıfırlanır |
| Ölçüm frekansı | **UNKNOWN** | — | Sartnamede yok |
| Tolerans | **UNKNOWN** | Q-V2 | — |
| Kritik olgu | 180° fark | mantık | Toplamda delik oluşur |

#### A.2.3 V-5 / V-6 / V-7 — Derece ve çok yollu yapı

| Öğe | Değer | Kaynak | Not |
|---|---|---|---|
| Derece (order) | **UNKNOWN** | Q-X3 | Simülasyon engelli |
| Beklenen eğim | **UNKNOWN** | Q-X3 | Hedef yazım yok |
| Yol sayısı | **UNKNOWN** | Q-X1 | 2 yol mu 3+ yol mu belirsiz |
| Orta bant sınırları | **UNKNOWN** | Q-X2 | Band-pass aralığı yok |
| Katsayı üretim yöntemi | `constexpr` tercih | kök AGENTS.md §5 | Çalışma zamanı maliyeti yasak |
| fs değişikliğinde yeniden türetme | Zorunlu | sartname | 48 kHz dışı katsayı kayması |

### A.3 Kabullenme kriterleri (acceptance)

| # | Kriter | Kanıt türü | Zorunlu mu | Durum |
|---|---|---|---|---|
| AC-1 | 512 örnek blok boyu korunur | birim testi | evet | PLANNED |
| AC-2 | 48 kHz zaman/frekans hesapları | sartname | evet | PLANNED |
| AC-3 | `noexcept` imzası | derleyici | evet | FROZEN |
| AC-4 | 0 tahsis (audio thread) | profil | evet | PLANNED |
| AC-5 | 0 mutex (audio thread) | kod taraması | evet | PLANNED |
| AC-6 | `alignas(64)` doğrulama | `static_assert` | evet | FROZEN |
| AC-7 | Bölme noktasında düz tepki | eğri ölçümü | evet | PLANNED |
| AC-8 | Faz uyumu (iptal yok) | faz ölçümü | evet | PLANNED |
| AC-9 | Bant ayrımı hedefe uyar | frekans süpürme | evet | PLANNED |
| AC-10 | bypass bit-bayar | birim testi | evet | PLANNED |
| AC-11 | Parametre clamp | birim testi | evet | PLANNED |
| AC-12 | Torn read yok | stres testi | evet | PLANNED |
| AC-13 | Denormal temizliği | profil | evet | PLANNED |
| AC-14 | Kanal gecikme farkı 0 | zaman ölçümü | evet | PLANNED |
| AC-15 | Exclusive Lock davranışı | bütünleşik | evet | PLANNED |
| AC-16 | DC koruma (0.5 V) | donanım | evet | PLANNED |
| AC-17 | Sürüm kanıtı (JUCE 9) | belge | evet | **VERIFICATION REQUIRED** |
| AC-18 | Sürüm kanıtı (ASIO SDK 2.3.4) | belge | evet | **VERIFICATION REQUIRED** |
| AC-19 | Ölçüm verisi mevcut | rapor | evet | **UNKNOWN** |
| AC-20 | Ölçüm ortamı kayıtlı | envanter | evet | **UNKNOWN** |

### A.4 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Web Audio API `AudioNode` arayüzü, tüm ses düğümlerinin ortak tabanıdır; `channelCount` / `channelCountMode` / `channelInterpretation` özellikleri kanal davranışını tanımlar | https://developer.mozilla.org/en-US/docs/Web/API/AudioNode | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| W3C Web Audio API çalışma belgesi, çok kanallı ses işlemesinin dilim (channel layout) ve birleştirme (mixing) kurallarını tanımlar | https://www.w3.org/TR/webaudio/ | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

> **Sınır notu:** Bu sayfalar **erişilebilirlik/sürüm doğrulamasıdır**; K004-dsp mimari kanıtı sayılmaz.
> Ölçüm sonucu, bölme frekansı veya eğim değeri **içermez**. Sürüm iddiaları hâlâ **VERIFICATION REQUIRED**.

### A.5 Gerçek repo kanıtları (disk)

| Kaynak | Dosya | Satır | Gözlem |
|---|---|---|---|
| `Dosya:` | `api.coremusic.net/config/routes.php` | `Satır:` 24 | `return [` — rota tablosu başlangıcı (yapılandırma deseni kanıtı) |
| `Dosya:` | `api.coremusic.net/config/routes.php` | `Satır:` 120 | `];` — tablo kapanışı |
| `Dosya:` | `shared/src/AI/AIEngine.php` | `Satır:` 140 | `PCM3168A` / `XMOS` / `AK4458` donanım zinciri anılıyor |
| `Dosya:` | `shared/composer.json` | `Satır:` 24 | `"phpunit/phpunit": "^10.5"` — PHP tarafı test çatısı kanıtı |
| `Dosya:` | `home.coremusic.net/pages/components/widget-grid.php` | `Satır:` 59-60 | EQ widget'ı — bant/kanal sunumunun UI kanıtı |

> Bunlar **gerçek repodan okunan satırlardır**. Crossover'ın C++ karşılığı diskte **mevcut değildir**;
> PHP/JS satırları yalnızca yapısal analog taşır, crossover DSP kodu olarak yorumlanamaz.

### A.6 Açık sorular (open questions)

| Q# | Soru | Doğrulamayı engelleyen | Durum |
|---|---|---|---|
| Q3 | dynamics ↔ compressor ↔ limiter hiyerarşisi | Sınır belirsizliği | **AÇIK** |
| Q4 | measurement ↔ testing sınırı | Bu dosyanın yetki alanı | **AÇIK** |
| Q-X1 | Yol sayısı (2 / 3+ ) nedir? | Bant ayrımı beklentisi | **UNKNOWN** |
| Q-X2 | Bölme frekansı(ları) nedir? | Süpürme merkezi | **UNKNOWN** |
| Q-X3 | Filtre tipi ve derece nedir? | Eğim beklentisi | **UNKNOWN** |
| Q-X4 | Bass management kuralı nedir? | V-19 beklenen davranışı | **UNKNOWN** |
| Q-X5 | Bant–kanal eşleme haritası nedir? | V-20 beklenen davranışı | **UNKNOWN** |
| Q-X6 | Bypass geçiş yumuşatma yöntemi nedir? | V-9 beklenen davranışı | **UNKNOWN** |
| Q-X7 | Faz eşitleme algoritması ve gecikme bütçesi nedir? | V-3 / V-24 beklenen değeri | **UNKNOWN** |
| Q-X8 | 8.1 çıkış düzeni nedir? | Kanal doğrulaması | **UNKNOWN** |

#### A.2.4 V-8 / V-9 — bypass ve kip geçişi

| Öğe | Değer | Beklenen | Ölçüm | Durum |
|---|---|---|---|---|
| bypass girişi | rastgele blok | Çıkış = giriş | bit karşılaştırma | PLANNED |
| bypass gecikmesi | 0 örnek | Ek gecikme yok | zaman ölçümü | PLANNED |
| geçiş (aktif → bypass) | kip değişimi | Tıkırtısız | dinleme | PLANNED |
| geçiş (bypass → aktif) | kip değişimi | Tıkırtısız | dinleme | PLANNED |
| bypass yolunun fazı | — | Aktif yolla aynı | faz ölçümü | **UNKNOWN** |
| Yumuşatma süresi | **UNKNOWN** | Q-X6 | Zaman ölçümü | **UNKNOWN** |

#### A.2.5 V-12 / V-13 / V-14 / V-17 — RT kısıt doğrulaması

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
| DK-3 | Ölçüm aracı seçilmedi | disk ölçümü | Faz/kazanç ölçümü yapılamaz |
| DK-4 | CI hattı tanımsız | disk ölçümü | Otomatik kapı yok |
| DK-5 | Sürüm kanıtı yok | kök AGENTS.md §12 | Karşılaştırma yapılamaz |
| DK-6 | Ölçüm durumu PLANNED | sartname | 512/48 kHz doğrulanmadı |
| DK-7 | ASIO cihaz envanteri yok | disk ölçümü | Sistem testi engelli |
| DK-8 | Dinleme ortamı belgelenmedi | disk ölçümü | Subjectif test engelli |
| DK-9 | ADR yüzeyi yasağı | görev kuralları | Karar kaydı açılamaz |
| DK-10 | Bağımlılık bump yasağı | kök AGENTS.md §13 | Araç eklenemez |
| DK-11 | Bölme frekansı sabitlenmedi | Q-X2 | Süpürme merkezi yok |
| DK-12 | Bant sayısı bilinmiyor | Q-X1 | Ayrım beklentisi yazılamaz |

### A.5.1 Görev kuralları ile doğrulama ilişkisi

| Kural | Doğrulamadaki karşılığı | Sınav |
|---|---|---|
| Zero-allocation | V-12 / TS-11 | Tahsis 0 mı |
| Lock-free | V-13 / TS-12 | Mutex 0 mı |
| `noexcept` | AC-3 / TS-17 | İhlal var mı |
| `constexpr` | Kod incelemesi | Çalışma zamanı katsayı hesabı var mı |
| `alignas(64)` | V-17 / AC-6 | `static_assert` geçti mi |
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
| TS-1 | Katsayı üretimi tutarlılığı | fs + fc + order | deterministik set | yüksek |
| TS-2 | fs duyarlılığı | 48 kHz vs diğer | kutup birimi çemberde | yüksek |
| TS-3 | Bölme tepkisi düz mü | süpürme | hedef eğim içinde | yüksek |
| TS-4 | Faz uyumu | iki yol | iptal yok | yüksek |
| TS-5 | Bant toplamı | bölme noktası | ≈ 1.0 | yüksek |
| TS-6 | Bypass eşitliği | rastgele blok | bit-bayar | yüksek |
| TS-7 | Clamp | aralık dışı fc/order | sınır değeri | yüksek |
| TS-8 | Atomik paylaşım | eşzamanlı yazma | tutarlı okuma | yüksek |
| TS-9 | Tahsis sayacı | 10⁶ blok | 0 tahsis | yüksek |
| TS-10 | Mutex sayacı | kod taraması | 0 mutex | yüksek |
| TS-11 | Denormal | sessiz blok | CPU sabit | orta |
| TS-12 | Blok boyu | değişen çağrı | 512 sabit | yüksek |
| TS-13 | Hizalama | `static_assert` | 64 bayt | orta |
| TS-14 | Kanal gecikme farkı | çok kanallı | 0 örnek | yüksek |
| TS-15 | Kip geçişi | bypass↔aktif | tıkırtı yok | orta |
| TS-16 | Bass management | alt bant | sub kanalı | orta |
| TS-17 | `noexcept` denetimi | derleyici | ihlal yok | yüksek |
| TS-18 | Kararlılık | katsayı kayması | ısınma yok | yüksek |
| TS-19 | 3+ yollu yapı | mid bant | doğru aralık | orta |
| TS-20 | Ölçüm ortamı envanteri | ortam kaydı | kayıt tam | orta |

### B.3 Kapsam ve kapsam dışı

| Alan | Kapsamda mı | Gerekçe |
|---|---|---|
| Bant ayırma mantığı | evet | Bu modülün sorumluluğu |
| Katsayı üretici | evet | Bileşen C-5 |
| Filtre çekirdeği uygulama | kısmen | Matematik filters'e ait, bağlama crossover |
| Faz eşitleme kararı | evet | Bileşen C-9 |
| Frekans düzeltmesi (EQ) | hayır | K004-dsp/equalizer |
| Filtre tasarımı (IIR/FIR) | hayır | K004-dsp/filters |
| Ölçüm altyapısı | kısmen | Q4 **AÇIK** — K004-dsp/measurement |
| Test harness'ı iskeleti | hayır | K004-dsp/testing |
| Zincir sırası | hayır | K004-dsp/pipeline |
| UI gösterge çizimi | hayır | Sunum katmanı |
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
| 2 | Faz iptali | Seste kayıp/delik | TS-4, V-3 |
| 3 | Yanlış bölme frekansı | Sistem tepkisi bozulur | TS-3, V-1 |
| 4 | Kararsız kutup | CPU patlar | TS-2, TS-18 |
| 5 | Parametre bozukluğu | Torn read | TS-8, TS-7 |
| 6 | Bypass tıklaması | Duyulabilir bozulma | TS-15, V-9 |
| 7 | Cihaz kilidi | İkinci açılış | V-22 |
| 8 | Ölçüm altyapısı eksikliği | Doğrulanamaz | Q-V1 |

### B.6 Test verisi tablosu

| Veri seti | İçerik | Kullanım | Durum |
|---|---|---|---|
| DS-1 | Süpürme sinüsü seti | Bölme tepkisi | **UNKNOWN** (üretilmedi) |
| DS-2 | Dar bant gürültü | Bant ayrımı | **UNKNOWN** |
| DS-3 | Ani tepe (impulse benzeri) | Faz/gecikme | **UNKNOWN** |
| DS-4 | Çok kanallı test sinyali | Kanal eşleme | **UNKNOWN** |
| DS-5 | Müzik kaydı | Subjectif | **UNKNOWN** |
| DS-6 | Tam genlik sinüs | Klips kontrolü | **UNKNOWN** |
| DS-7 | Sessizlik | Denormal | **UNKNOWN** |
| DS-8 | Bölme noktası sinüsü | Birleşim kontrolü | **UNKNOWN** |

## §C Ölçüm metodolojisi

### C.1 Ölçüm envanteri

| # | Ölçüm | Büyüklük | Birim | Araç | Durum |
|---|---|---|---|---|---|
| M-1 | Frekans tepkisi | Kazanç vs frekans | dB | süpürme | PLANNED |
| M-2 | Faz tepkisi | Faz vs frekans | derece | faz analizi | PLANNED |
| M-3 | Grup gecikmesi | Gecikme vs frekans | ms | faz türevi | PLANNED |
| M-4 | Bant ayrımı | Sönümleme | dB | frekans ölçümü | PLANNED |
| M-5 | Birleşim düzgünlüğü | Toplam genlik | dB | genlik ölçümü | PLANNED |
| M-6 | Gecikme | Blok gecikmesi | örnek | zaman ölçümü | PLANNED |
| M-7 | CPU kullanımı | İş yükü | % | profil | PLANNED |
| M-8 | Tahsis sayısı | Bellek | adet | sayacı | PLANNED |
| M-9 | Xrun sayısı | Kopma | adet | sürücü günlüğü | PLANNED |
| M-10 | Kanal gecikme farkı | Kanallar arası | örnek | zaman ölçümü | PLANNED |
| M-11 | Distorsiyon | Harmonik bozulma | % | frekans analizi | PLANNED |
| M-12 | Sinyal-gürültü oranı | Noise floor | dB | spektrum | PLANNED |

### C.2 Ölçüm zinciri (setup)

| Aşama | Girdi | İşlem | Çıktı | Not |
|---|---|---|---|---|
| S-1 | Test sinyali | Kaynak üretimi | Referans dosya | Üretim yöntemi **UNKNOWN** |
| S-2 | Referans | SUT'a besleme | İşlenmiş çıkış | 512/48 kHz |
| S-3 | Çıkış | Örnekleme | Ham dizi | 32-bit float (ADR-017) |
| S-4 | Ham dizi | FFT / analiz | Tepki serisi | Pencereleme **UNKNOWN** |
| S-5 | Tepki serisi | Karşılaştırma | Hedef eğri | Hedef eğri yok (Q-X3) |
| S-6 | Sapma | İstatistik | Maks. sapma | Tolerans **UNKNOWN** |
| S-7 | Sonuç | Kayıt | Rapor satırı | Kalıcılık yolu **UNKNOWN** |

### C.3 Ölçüm koşulları

| Koşul | Değer | Kaynak | Durum |
|---|---|---|---|
| Örnekleme frekansı | 48000 Hz | sartname | FROZEN |
| Blok boyu | 512 örnek | sartname | FROZEN |
| Örnek biçimi | 32-bit float | ADR-017 | FROZEN |
| Bölme frekansı | **UNKNOWN** | Q-X2 | Ölçüm merkezi yok |
| Filtre tipi/derece | **UNKNOWN** | Q-X3 | Hedef eğim yok |
| Referans seviyesi | **UNKNOWN** | — | Ölçüm PLANNED |
| Noise floor hedefi | **UNKNOWN** | Q-V5 | — |
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
| R-7 | fs sabitlenir (48 kHz) | Katsayı tutarlılığı | PLANNED |

### C.5 Ölçüm riskleri

| Risk | Etki | Azaltma | Kalan belirsizlik |
|---|---|---|---|
| Ölçüm aracı seçilmedi | Ölçüm yapılamaz | Araç seçimi kararı | **UNKNOWN** |
| Bölme frekansı yok | Süpürme merkezi belirsiz | Q-X2 kararı | **UNKNOWN** |
| Hedef eğim yok | Pass/fail belirsiz | Q-X3 kararı | **UNKNOWN** |
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
| Bölme frekansı | **UNKNOWN** | Hz | Q-X2 | — |
| Filtre tipi | **UNKNOWN** | — | Q-X3 | — |
| Derece (order) | **UNKNOWN** | — | Q-X3 | — |
| Eğim hedefi | **UNKNOWN** | dB/oct | Q-X3 | — |
| Yol sayısı | **UNKNOWN** | adet | Q-X1 | — |
| Birleşim katsayısı | **UNKNOWN** | — | Q-X3 | — |
| Faz düzeltme gecikmesi | **UNKNOWN** | örnek | Q-X7 | — |
| Bypass yumuşatma süresi | **UNKNOWN** | ms | Q-X6 | — |
| Noise floor hedefi | **UNKNOWN** | dB | Q-V5 | — |
| Distorsiyon eşiği | **UNKNOWN** | % | — | — |
| CPU bütçesi | **UNKNOWN** | % | — | — |
| Xrun toleransı | **UNKNOWN** | adet | Q-V6 | — |
| Gecikme üst sınırı | **UNKNOWN** | örnek | Q-X7 | — |

### D.2 Eşik tablosu (thresholds)

| Eşik | Değer | Yön | Tepki | Durum |
|---|---|---|---|---|
| Klips sınırı | 1.0 FS | ≥ | Sınır/limit | FROZEN (mantık) |
| DC koruma | 0.5 V | > | Röle | FROZEN |
| Tahsis bütçesi | 0 | > | Xrun riski | FROZEN (kural) |
| Mutex bütçesi | 0 | > | Prioritevi inversion | FROZEN (kural) |
| Birleşim sapması | **UNKNOWN** | > | Tepede aşım/çukur | — |
| Faz iptali eşiği | **UNKNOWN** | > | Seste kayıp | — |
| Bant ayrımı üstü | **UNKNOWN** | < | Sızma | — |
| CPU üstü | **UNKNOWN** | > | Xrun riski | — |
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
| Bölme frekansı | karar beklenir | yok | **DUR** | Q-X2 **AÇIK** |

### D.4 Kanıt kaynakları

| Kaynak | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| `Dosya:` | `api.coremusic.net/config/routes.php` | `Satır:` 24 | Yapılandırma tablosu başlangıcı |
| `Dosya:` | `api.coremusic.net/config/routes.php` | `Satır:` 120 | Yapılandırma tablosu kapanışı |
| `Dosya:` | `shared/src/AI/AIEngine.php` | `Satır:` 140 | Donanım zinciri referansı |
| `Dosya:` | `shared/composer.json` | `Satır:` 24 | phpunit `^10.5` |
| `Kaynak:` | `home.coremusic.net/pages/components/widget-grid.php` | `Satır:` 59-60 | Bant/kanal UI sunumu |
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
| CL-8 | Bölme frekansı doğrulandı | Karar yok | **UNKNOWN** |

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
| Bölme kararı yok (Q-X2) | hayır | — | — | **DUR** |
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
| 10 | Bölme frekansı | **UNKNOWN** | Q-X2 |
| 11 | Filtre tipi ve derece | **UNKNOWN** | Q-X3 |
| 12 | Ölçüm ortamı | **UNKNOWN** | Q-V1 |
| 13 | Kabul toleransları | **UNKNOWN** | Q-V2 |
| 14 | Bant–kanal eşlemesi | **UNKNOWN** | Q-X5 |

### D.9 Kaynak ve yetki

| Öğe | Değer |
|---|---|
| Sahip | Bayram Ali / Vault Steward |
| Üst düğüm | `K004-dsp/crossover` |
| Eşlik dosyalar | `crossover-kapsam.md`, `crossover-ornekler.md` |
| SSOT | `.ai/architecture/K004-dsp/dsp-master.md` §3 |
| Mod | Red Team · Human Mode · Truth Mode |
| Sürüm kanıtı durumu | **VERIFICATION REQUIRED** |
| Ölçüm durumu | **UNKNOWN** |
| Motor durumu | diskte **YOK** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
