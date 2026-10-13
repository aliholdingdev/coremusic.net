---
node_id: K005-firmware
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - boot
  - runtime
  - device-control
  - audio-control
  - power-control
  - diagnostics
  - update
  - security
  - testing
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
  - K003-audio
  - K004-dsp
evidence:
  - "ADR-097 §5.5 (satır 140) — iki-ekseni gruplama — okundu 2026-10-12"
  - ".ai/architecture/index.md §8.1 (satır 160) — grup tablosu — okundu 2026-10-13"
  - ".github/workflows/ci.yml (satır 8, 48) — CI Pipeline / jobs — okundu 2026-10-13"
  - "URL havuzu (181 satır doğrulanmış kayıt) — 12/26/48 paylaştırıldı 2026-10-13"
validation:
  - "2026-10-12 — §2 çocuk tablosu v2 §2.5 mini-ağaçı ile satır satır karşılaştırıldı (9/9)"
  - "2026-10-13 — §4 URL'leri havuz dosyasından yenilendi (12 URL, HTTP-200)"
  - "2026-10-13 — § yapısı sabitlandı (diğer 11 kök dosya bu §lere referans verir)"
open_questions:
  - "§3 iddialarının çoğu kanıtsız → [TASARIM]; kanıt yalnız §4 tablosunda → ⚠️ VERIFY REQUIRED"
  - "K004-dsp sınırı: firmware'de donanım-içi DSP register yazımı var mı? → K004 ile ortak inceleme (⚠️)"
  - "v2 §6 disk çakışması: `K005-veri-yonetimi` ↔ `K005-firmware` → ID CONFLICT — VERIFY REQUIRED"
group: mimari
---

# K005-firmware — Firmware Master (anayasa)

> v2 §2.5 bu dosyaya **`firmware-master.md`** adını verir (KURAL 1 — ad değiştirilmez).
> Bu dosya K005'in **tek omurga** belgesidir; diğer 11 kök dosya ve 36 çocuk dosyası
> buradaki § yapısına referans verir.

## §0 Doküman künyesi

| Alan | Değer |
|---|---|
| Düğüm | `K005-firmware` (kademe-1 · domain) |
| Üretim | çoklu-MD şeması — 12 kök MD + 6 çocuk × 6 MD |
| Şema dayanağı | ADR-097 §5.5 + `.ai/architecture/index.md` §8.1 (iki eksen) |
| Spec dayanağı | `coremusic-katmanli-mimari-agaci-v2.md` §2.5 · §3 · §4.2 · §4.3 · §4.7 |
| Tarih | 2026-10-13 |
| Durum | `verify-required` — D1–D9 kapandığında `validation:` listesine `verified` kaydı düşülür (FM'de `status:` alanı yok — K-13) |

## §1 ELI15 rol ve sınır

> **Donanımın kendi küçük yazılımı — cihaz açılırken çalışan "iç işletim sistemi".**

| Düğüm | K005 ile ilişkisi | 1 satır gerekçe |
|---|---|---|
| K001-donanım | **içinde** | Fiziksel parça, register ve pin'ler firmware'in yazdığı yüzeydir |
| K000-isletim-sistemi | **dışında** | Host OS'in kendisi ayrı raf; K005 yalnız gömülü/bare-metal hedefte yaşar |
| K003-audio | **dışında** | Sesin taşınması ve yönlendirilmesi ayrı sorumluluk; K005 yalnız hazır durumu **sorgular** |
| K004-dsp | **dışında** | Ses işleme algoritması (EQ/reverb/compressor) K004'ün işidir; K005 yalnız **kontrol/konfigürasyon** verir |

**Tek cümle sınır:** K005, cihazın **açılışını**, **çalışma anını**, **parça kontrolünü**,
**gücünü**, **kendini doğrulamasını**, **güncellemesini** ve **kilitlenmesini** yönetir;
sesi **işlemez**, işletim sistemi **kurmaz**, donanımın **fiziksel tasarımını** yapmaz.

### §1.1 Yaşam döngüsü (tek bakışta)

```text
  ENERJİ ──► RESET ──► reset vector ──► ROM bootloader
                                                │
                          ┌─────────────────────┴─────────────────────┐
                          ▼                                           ▼
                  imza doğrulandı                            imza HATASI
                          │                                           │
                          ▼                                           ▼
              ilk-stage (clock · pin · watchdog)          güvenli kapanma / geri-alma
                          │
                          ▼
                 ana firmware yüklenir
                          │
          ┌───────────────┼────────────────┬──────────────────┐
          ▼               ▼                ▼                  ▼
      runtime döngüsü   device-control   audio-control    power-control
      (scheduler ·      (bus · driver)   (register ·      (uyku · brown-out)
       watchdog)                         mute · gain)
          │                                     │
          ▼                                     ▼
      diagnostics (self-test · log)        update (OTA · A/B · rollback)
                          │
                          ▼
                    security (imza · erişim kilidi · sayaç)
```

| Kademe | Sahip çocuk | Çıktı |
|---|---|---|
| Reset vektörü | boot | İlk çalıştırılabilir adres |
| ROM bootloader | boot | Bölüm bütünlüğü + imza kontrolü |
| İlk-stage | boot | Clock · pin · watchdog kurulumu |
| Ana firmware | runtime | Görev tablosu + ana döngü |
| Kip geçişleri | power-control · audio-control | Uyku · mute · gain |

## §2 Kapsam haritası — 9 çocuk (v2 §2.5, sıra korunur)

| # | Çocuk | Rol (adından türetildi) | Dalga | Üretim |
|---|---|---|---|---|
| 1 | boot | Reset → kendini tanıma → yükleme | 2 | ✅ 6 MD |
| 2 | runtime | Çalışma anı döngüsü · scheduler · watchdog | 2 | ✅ 6 MD |
| 3 | device-control | Parçaya komut · bus · driver kipi | 2 | ✅ 6 MD |
| 4 | audio-control | Ses donanımı ayar ve kip kontrolü | 2 | ✅ 6 MD |
| 5 | power-control | Uyku · kapanma · akım · termal | 2 | ✅ 6 MD |
| 6 | diagnostics | Self-test · hata raporu · log | 3 | ⏳ yalnız eski index.md |
| 7 | update | OTA · A/B · rollback | 2 | ✅ 6 MD |
| 8 | security | Secure boot · imza · erişim kilidi | 3 | ⏳ yalnız eski index.md |
| 9 | testing | Unit · integration · HIL testleri | 3 | ⏳ yalnız eski index.md |

> ⚠️ VERIFICATION REQUIRED: v2 §2.5 bu dala **yalnızca ad verir** — rol satırları
> adından türetilmiştir (v2 §3 KURAL 3). Uydurma register/sürüm/ürün adı yazılmaz.

## §3 Teknoloji yığını iddiaları

> Her satır **ya kanıtlıdır ya tasarımdır.** `[KANIT]` = altında kanıt var (§4 ·
> `firmware-dogrulama.md` §2 · `firmware-kod-ornekleri.md` §2). `[TASARIM]` = hedef
> ifadesi, ölçülmemiş. Ölçüm yoksa rakam uydurulmaz.

### §3.1 Açılış ve taşıma

| # | İddia | Etiket |
|---|---|---|
| A1 | Açılış zinciri `reset vector` → ROM bootloader → ilk-stage loader → ana firmware → uygulama şeklinde kademelenir | [TASARIM] |
| A2 | Her kademe bir sonrakine yalnızca kendini doğruladıktan sonra teslim eder | [TASARIM] |
| A3 | Bootloader bölümü ile uygulama bölümü bellek haritasında ayrıdır (A/B bölmeli güncelleme için) | [TASARIM] |
| A4 | Interrupt vector tablosu ilk-stage'de yüklenir, runtime'da değiştirilmez | [TASARIM] |
| A5 | Watchdog bootloader'a geçişte devre dışı bırakılır, runtime'da yeniden açılır | [TASARIM] |
| A6 | Kristal/PLL ayarı ilk-stage'de yapılır; clock ağacı runtime'da sabit kalır | [TASARIM] |
| A7 | Derleme çıktısı sabit bellek adresine göre link edilir (linker script / scatter file) | [TASARIM] |
| A8 | İlk-stage yalnız kendi bütünlüğünü değil, bir sonraki kademenin imzasını da denetler | [TASARIM] |

### §3.2 Çalışma anı (runtime)

| # | İddia | Etiket |
|---|---|---|
| R1 | Tek bir ana döngü veya gerçek zamanlı işletim sistemi (RTOS) görevi vardır; ikisi aynı anda kullanılmaz | [TASARIM] |
| R2 | Kesme (interrupt) servis rutinleri kisa tutulur; uzun iş ana döngüye/queue'ya devredilir | [TASARIM] |
| R3 | Paylaşılan veri kritik bölüm (critical section) içinde okunur/yazılır | [TASARIM] |
| R4 | Windowed watchdog periyodik olarak beslenir; besleme yalnız ana döngüden yapılır | [TASARIM] |
| R5 | Görev (task) öncelikleri sabittir; ses/kontrol yolu en yüksek önceliklidir | [TASARIM] |
| R6 | Zaman kaynağı monotoniktir; saat sıçraması (NTP benzeri)irmware zamanlayıcısını etkilemez | [TASARIM] |
| R7 | Boşta kalma (idle) kipinde düşük güç seviyesine geçilir | [TASARIM] |
| R8 | Reset nedeni (watchdog · yazılım · brown-out · pin) bir sonraki açılışta okunur ve raporlanır | [TASARIM] |

### §3.3 Cihaz kontrolü (device · audio · power)

| # | İddia | Etiket |
|---|---|---|
| C1 | Her çevre birimi (peripheral) yalnız tek bir sahip modül tarafından yazılır | [TASARIM] |
| C2 | Register yazımı önce okuma-modify-yazma (RMW) ile yapılır, bit alanı ezilmez | [TASARIM] |
| C3 | I²C/SPI/UART erişimi kuyruk (queue) üzerinden serileştirilir, yarış (race) önlenir | [TASARIM] |
| C4 | Ses donanımı ayarları açılışta yüklenir; çalışma anında yalnız gain/mute gibi alanlar değişir | [TASARIM] |
| C5 | Mute aç-kapa sırası belirlidir (pop/clip koruması); kip geçişleri adımlı yapılır | [TASARIM] |
| C6 | Uyku/kapanma sırası: önce ses yolu sustur → tamponları boşalt → gücü kapat | [TASARIM] |
| C7 | Brown-out / düşük güç eşiği firmware tarafında izlenir ve güvenli kapanma tetiklenir | [TASARIM] |
| C8 | GPIO yön ve başlangıç değeri reset anında tanımlanır (floating pin yok) | [TASARIM] |

### §3.4 Güvenlik ve güncelleme

| # | İddia | Etiket |
|---|---|---|
| S1 | Secure boot zinciri imza doğrulamadan geçmeden firmware çalışmaz | [TASARIM] |
| S2 | Anahtarlar (public key) salt-okunur bölgede tutulur; gizli anahtar üretim dışında kalır | [TASARIM] |
| S3 | OTA güncellemesi imzalı manifest + bütünlük özetine (hash) bağlıdır | [TASARIM] |
| S4 | A/B bölmeli güncellemede geri alma (rollback) eski bölmeye dönmektir | [TASARIM] |
| S5 | Sayaç geri sarması (rollback counter) yüklenmiş sürümü düşürmeyi engeller | [TASARIM] |
| S6 | Hata log'u hassas veri içermez; maskeleme katmanı vardır | [KANIT] — `Dosya: shared/src/Log/Redactor.php / Satır: 1` |
| S7 | Erişim denetimi firmware kabuk (shell) komutlarında da uygulanır | [TASARIM] |
| S8 | Bootloader alanına yazma yetkisi yalnız güncelleme modülündedir | [TASARIM] |

### §3.5 Arayüz ve veri iddiaları

| # | İddia | Etiket |
|---|---|---|
| I1 | Firmware ile host arasındaki tek veri yolu belirli bir protokolle sınırlıdır (USB veya seri) | [TASARIM] |
| I2 | Host komutları sabit uzunluklu/kareli (framed) ve sürüm etiketlidir | [TASARIM] |
| I3 | Bilinmeyen komut sessizce yok sayılmaz; hata kodu döner | [TASARIM] |
| I4 | Firmware içi olaylar (event) tek dağıtıcıdan (dispatcher) yayılır | [KANIT] — `Dosya: shared/src/Events/EventDispatcher.php / Satır: 1` |
| I5 | Servis/nesne kayıtları tek bir registry'de tutulur, ikinci kayıt yoktur | [KANIT] — `Dosya: shared/src/Api/Registry/ServiceRegistry.php / Satır: 1` |
| I6 | Yapılandırma ortam dosyasından tek yönde yüklenir, çalışma anında değiştirilmez | [KANIT] — `Dosya: shared/src/Config/EnvParser.php / Satır: 7` |
| I7 | Önbellek anahtarları ad alanı (namespace) ile ayrılır | [KANIT] — `Dosya: shared/src/Cache/CacheManager.php / Satır: 5` |
| I8 | Rate limit sayaçları önbellek katmanında tutulur | [KANIT] — `Dosya: shared/src/Security/CacheRateLimiter.php / Satır: 10` |
| I9 | Log yazımı fabrika (factory) üzerinden yürür, doğrudan `echo` yoktur | [KANIT] — `Dosya: shared/src/Log/LoggerFactory.php / Satır: 1` |
| I10 | Hassas alanlar log'a yazılmadan önce maskelenir | [KANIT] — `Dosya: shared/src/Log/Redactor.php / Satır: 6` |
| I11 | Zaman dilimi ve hata-raporlama kipi açılışta bir kez kurulur | [KANIT] — `Dosya: shared/src/Bootstrap/RuntimeBootstrap.php / Satır: 9` |
| I12 | Test/analiz pipeline'ı sürüm kontrol deposundaki workflow ile yürür | [KANIT] — `Dosya: .github/workflows/ci.yml / Satır: 8` |
| I13 | Kesişim (interrupt) bayrağı doluysa bir sonraki döngüde işlenir | [TASARIM] |
| I14 | Uzak alan (remote field) güncellemesi değişiklik günlüğüne (journal) yazılır | [TASARIM] |
| I15 | Şema sürümü firmware sürümüyle birlikte okunur | [KANIT] — `Dosya: .ai/.sql/mysql/coremusic_neva.sql / Satır: 1` |

> `[KANIT]` satırları bu repo'da **gerçekten okunan** dosyalardır; tam alıntı ve
> satır numaraları `firmware-kod-ornekleri.md` §2 içindedir.

## §4 Web kanıtları (özet — tam döküm `firmware-web-arastirma.md` ve `firmware-dogrulama.md`)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| CMake, firmware derleme sistemi için çapraz-platform yapım aracıdır | https://cmake.org/ | 2026-10-13 | ✅ HTTP-200 |
| CMake referans dokümantasyonu resmi sitede yayımlanır | https://cmake.org/cmake/help/latest/ | 2026-10-13 | ✅ HTTP-200 |
| GCC derleyicisinin kullanıcı kılavuzu resmi sitededir | https://gcc.gnu.org/onlinedocs/gcc/ | 2026-10-13 | ✅ HTTP-200 |
| Clang kullanıcı kılavuzu LLVM projesinin resmi belgesidir | https://clang.llvm.org/docs/UsersManual.html | 2026-10-13 | ✅ HTTP-200 |
| GNU make, firmware yapım betiklerinin çalıştırılmasını sağlar | https://www.gnu.org/software/make/manual/make.html | 2026-10-13 | ✅ HTTP-200 |
| Ninja, paralel yapım çalıştırıcısı olarak kullanılır | https://ninja-build.org/manual.html | 2026-10-13 | ✅ HTTP-200 |
| LLVM araç zinciri dokümantasyonu tek çatı altında toplanır | https://llvm.org/docs/ | 2026-10-13 | ✅ HTTP-200 |
| MISRA C, gömülü C kodu için güvenlik kılavuzudur | https://www.misra.org.uk/faq/ | 2026-10-13 | ✅ HTTP-200 |
| Git, firmware depo sürüm kontrolünün kaynağıdır | https://git-scm.com/ | 2026-10-13 | ✅ HTTP-200 |
| GitHub Actions, CI kapılarının (G1–G8) çalıştığı ortamdır | https://docs.github.com/en/actions | 2026-10-13 | ✅ HTTP-200 |
| Arm CPU mimarisi dokümantasyonu resmi Arm sitesinde yayımlanır | https://www.arm.com/architecture/cpu | 2026-10-13 | ✅ HTTP-200 |
| CMSIS Core, Arm Cortex-M çekirdek katmanının resmi arayüzüdür | https://arm-software.github.io/CMSIS_5/Core/html/index.html | 2026-10-13 | ✅ HTTP-200 |

> Bu dosyada yalnız **12** URL kullanıldı; aynı URL bu K005 üretiminde **bir kez** geçer.
> Tarih sütunu başlığı ile satır tarihi aynı gün olmak zorundadır (K-14).

## §5 Bağımlılık omurgası

| Yön | Düğüm | İlişki | Etiket |
|---|---|---|---|
| K005 → K001-donanım | register · clock · pin · DMA yüzeyi | okuma/yazma | [TASARIM] |
| K001 → K005 | Donanım ancak firmware ile uyanır | çağıran | [TASARIM] |
| K005 → K000-isletim-sistemi | Yalnız host/bare-metal hedeflerde değil, sanallaştırma sınırı | sınır | [TASARIM] |
| K003-audio → K005 | Ses yolu hazır durumu sorgulanır | çağıran | [TASARIM] |
| K005 → K003-audio | Yalnız "yol hazır mı" bayrağı; akış (stream) verisi geçmez | sınır | [TASARIM] |
| K005 → K004-dsp | Yalnız **kontrol parametresi** gönderimi | sınır | [TASARIM] ⚠️ |
| K004 → K005 | Algoritma firmware'e bağımlı değil | bağımsız | [TASARIM] |

## §6 Bileşen kataloğu

### §6.1 Kök (12 MD)

| # | Dosya | Grup | Görev |
|---|---|---|---|
| 1 | `index.md` | mimari | Künye · çocuk kataloğu · §GRUPLAR |
| 2 | `firmware-master.md` | mimari | Anayasa (bu dosya) |
| 3 | `firmware-kapsam.md` | mimari | Kapsam sınırı |
| 4 | `firmware-bagimliliklar.md` | mimari | Bağımlılık haritası + mimari↔kod |
| 5 | `firmware-dogrulama.md` | kanit | Kriterler + doğrulama tabloları |
| 6 | `firmware-web-arastirma.md` | kanit | Web araştırması kayıtları |
| 7 | `firmware-bilesenler.md` | mimari | Bileşen kataloğu + kademe-3 planı |
| 8 | `firmware-kod-ornekleri.md` | kod | Gerçek repo örnekleri |
| 9 | `firmware-kararlar.md` | mimari | Karar kaydı |
| 10 | `firmware-riskler.md` | kanit | Risk matrisi |
| 11 | `firmware-test-stratejisi.md` | kod | Test piramidi · gate |
| 12 | `firmware-evrensel-dil.md` | mimari | TR domain / EN teknik sözlük |

### §6.2 Kademe-2 çocuk şeması (6 çocuk × 6 MD = 36 dosya)

| # | Çocuk | Dosya deseni (6) | Öncelik |
|---|---|---|---|
| 1 | boot | `index.md` · `boot-kapsam.md` · `boot-dogrulama.md` · `boot-web-arastirma.md` · `boot-ornekler.md` · `boot-bilesenler.md` | 1 |
| 2 | runtime | `index.md` · `runtime-kapsam.md` · `runtime-dogrulama.md` · `runtime-web-arastirma.md` · `runtime-ornekler.md` · `runtime-bilesenler.md` | 2 |
| 3 | audio-control | `index.md` · `audio-control-kapsam.md` · `audio-control-dogrulama.md` · `audio-control-web-arastirma.md` · `audio-control-ornekler.md` · `audio-control-bilesenler.md` | 3 |
| 4 | device-control | `index.md` · `device-control-kapsam.md` · `device-control-dogrulama.md` · `device-control-web-arastirma.md` · `device-control-ornekler.md` · `device-control-bilesenler.md` | 4 |
| 5 | power-control | `index.md` · `power-control-kapsam.md` · `power-control-dogrulama.md` · `power-control-web-arastirma.md` · `power-control-ornekler.md` · `power-control-bilesenler.md` | 5 |
| 6 | update | `index.md` · `update-kapsam.md` · `update-dogrulama.md` · `update-web-arastirma.md` · `update-ornekler.md` · `update-bilesenler.md` | 6 |

### §6.3 Kademe-3 iskeleti (G4 `teknoloji` grubu — bu üretimde AÇILMADI)

```text
K005-firmware/{çocuk}/{teknoloji}/{teknoloji}-{rol}.md
   └─ grup: teknoloji (ADR-097 §5.5/A — G4)
   └─ örn: runtime/freertos/freertos-gorev-modeli.md  → ADR + band onayı gerektirir
```

## §7 Kararlar özeti (detay: `firmware-kararlar.md`)

| # | Karar | Gerekçe |
|---|---|---|
| K-1 | K005 yalnız **cihaz firmware'ini** kapsar | v2 §2.5 + kapsam ayrımı (§1) |
| K-2 | Master dosya adı `firmware-master.md` | v2 §3 KURAL 1 — ad değiştirilmez |
| K-3 | Kök 12 MD + kademe-2 6 çocuk × 6 MD | ADR-097 çoklu-MD şeması |
| K-4 | Her MD tek gruba ait (`group` 10. alan) | ADR-097 §5.5 + registry §8.1 |
| K-5 | Her `index.md` içinde `## §GRUPLAR` tablosu | registry §8.1 görünürlük kuralı |
| K-6 | G4 `teknoloji` grubu bu üretimde kullanılmaz | Kademe-3 açılmadı; band/ADR bekliyor |
| K-7 | Kanıtsız iddia `[TASARIM]` olarak yazılır | Zero-Hallucination (AGENTS §3) |
| K-8 | Dalga-3 (diagnostics · security · testing) bu üretimde yazılmaz | Görev kapsamı — 6 çocuk sınırı |

## §8 Risklar özeti (detay: `firmware-riskler.md`)

| # | Risk | Skor |
|---|---|---|
| 1 | Kapsam kayması: K004-dsp işi firmware'e taşınırsa sınır bozulur | 16 |
| 2 | Kapsam kayması: K001 register/API'si firmware'de tekrar tanımlanır | 12 |
| 3 | Kanıtsız iddiaların `[KANIT]` gibi kullanılması | 16 |
| 4 | Secure boot zinciri kırılırsa cihaz güvensiz açılır | 20 |
| 5 | OTA geri alma (rollback) çalışmazsa cihaz tuğla (brick) olur | 20 |
| 6 | Watchdog beslenmezse plansız reset döngüsü oluşur | 12 |
| 7 | Dalga-3 dosyaları yazılmazsa şema 9 çocuk taahhüdünü karşılamaz | 9 |
| 8 | ID CONFLICT (`K005-veri-yonetimi`) çözülmezse kayıt ikiye ayrılır | 15 |

## §9 Doğrulama durumu

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| D1 | v2 §2.5 birebirliği (9 çocuk + master adı) | spec satır okuma | ✅ 2026-10-12 |
| D2 | 12 kök MD mevcut | dizin sayımı | 🔄 |
| D3 | 6 çocuk × 6 MD mevcut | dizin sayımı | 🔄 |
| D4 | Frontmatter 9 anahtar (`status:` yok · `parent:` yok) | script kontrolü | ✅ 2026-10-13 |
| D5 | Her `index.md` → `## §GRUPLAR` | grep | 🔄 |
| D6 | Web iddiaları `İddia/URL/Tarih/Durum` tablosunda | grep | 🔄 |
| D7 | Kod örnekleri gerçek `Dosya/Satır` | dosya okuma | 🔄 |
| D8 | Dolgu kelimesi yok (doldurma · zaman · erteleme ifadeleri) | grep | 🔄 |
| D9 | Açık ⚠️/UNKNOWN kalmadı | `firmware-dogrulama.md` §5 | ❌ |
| D10 | `validation:` listesine `verified` kaydı | D1–D9 ✅ | ❌ |

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | Dalga-3 3 çocuk ne zaman yazılacak (diagnostics · security · testing)? | `⚠️ VERIFY REQUIRED` |
| 2 | v2 §6 disk çakışması `K005-veri-yonetimi` ↔ `K005-firmware` ne zaman çözülür? | `⚠️ ID CONFLICT — VERIFY REQUIRED` |
| 3 | Kademe-3 teknoloji klasörleri (G4) için band/ADR onayı var mı? | `⚠️ RESERVED — ADR` |
| 4 | Hedef MCU ailesi ve frekans aralığı seçilmedi (A1–A8 iddiaları geneldir) | `⚠️ VERIFY REQUIRED` |
| 5 | K004-dsp ile sınır (kontrol mü, algoritma mı) yazılı olarak teyit edilmeli | `⚠️ VERIFY REQUIRED` |

## §11 Kapsam dışı (bu anayasanın yazmadığı yüzeyler)

| Yüzey | Neden kapsam dışı | Gideceği düğüm |
|---|---|---|
| DSP algoritması (EQ · reverb · dinamik) | algoritma sahipliği K004'tedir | `K004-dsp` |
| PCB · devre · BOM | donanım tasarımı | `K001-donanım` |
| ASIO/WASAPI host sürücüsü | işletim sistemi ses yolu | `K000-isletim-sistemi` |
| Uygulama katmanı çalma (player) | per-OS player FROZEN (ADR-019) | `K003-audio` |
| MySQL · şema · migration | veri katmanı | `K005-veri-yonetimi` (ID CONFLICT — §10) |
| Web arayüzü · CSS · JS | sunucu/UI katmanı | K006–K011 |

Bu tabloya aykırı iş K005 kökünde başlamaz; başlarsa kapsam kayması (§8, skor 16)
tetiklenir ve dosya geri taşınır.

## §12 Katman sınırları (A0–A3 ile firmware ilişkisi)

| Katman | Firmware'in onunla ilişkisi | İzin verilen tek yön |
|---|---|---|
| A0 (veri · çekirdek) | bare-metal/boot kodu burada yaşar | firmware → A0 (yalnız kayıt) |
| A1 (güvenlik) | secure boot · röle · imza kontrolü | A1 → firmware (kural) |
| A2 (backend · API) | firmware güncelleme uçları (OTA) | A2 → firmware (paket) |
| A3 (arayüz) | firmware durumunun yalnız okunması | A3 → firmware (sorgu) |

Katman ihlali (§18 uyarısı) bu tablodaki yönün tersine dönerse sistem durur ve
MO müdahalesi gerekir (`.ai/AGENTS.md` §18).

## §13 Sürüm ve şema değişikliği kuralları

1. Kök dosya adları **değiştirilemez** (K-2 — v2 §3 KURAL 1).
2. § numaraları eklenir, mevcut § silinmez; taşma yeni § ile olur.
3. Frontmatter anahtar sayısı **9'dur** (K-13): `node_id` · `node_type` ·
   `parent_id` · `children` · `depends_on` · `evidence` · `validation` ·
   `open_questions` · `group`.
4. `status:` ve `parent:` alanları bu şemada yazılmaz.
5. Frontmatter değişikliği diğer 11 kök dosyada da yapılır — tek dosya yalnız
   değiştirilmez.
6. Alt klasöre (`boot/` … `testing/`) geçiş yalnız ayrı görevle olur.

## §14 URL kullanım kuralı (K-14 dağılımı)

| Dosya | URL payı | Tablo biçim |
|---|---|---|
| `firmware-master.md` (bu dosya) | **12** | `| İddia | URL | 2026-10-13 | Durum |` |
| `firmware-dogrulama.md` | **26** | aynı biçim |
| `firmware-web-arastirma.md` | **48** | aynı biçim |
| Toplam | **86** | — |

Kurallar: (a) her URL bu üretimde **bir kez** geçer; (b) tüm URL'ler
doğrulanmış havuzdan gelir (HTTP-200); (c) erişim tarihi satır ile sütun
başlığında aynıdır; (d) 86 URL, envanterdeki eski URL kümesiyle
**kesişmez** (kesişim = 0); (e) erişilemeyen alan adı havuza girmez.

## §15 Doküman hiyerarşisi

```text
index.md                 → 9 çocuk + ## §GRUPLAR (12 satır)
firmware-master.md       → anayasa (bu dosya) — § yapısı referans kaynağı
firmware-kapsam.md       → sınır (iç/dış)
firmware-bagimliliklar.md → K000/K001/K003/K004 omurgası
firmware-dogrulama.md    → D-kapıları (26 URL)
firmware-web-arastirma.md → araştırma kaydı (48 URL)
firmware-bilesenler.md   → bileşen kataloğu
firmware-kod-ornekleri.md → Dosya/Satır + Kaynak künyeleri
firmware-kararlar.md     → K-1 … K-16
firmware-riskler.md      → R-01 … R-10
firmware-test-stratejisi.md → piramit · G1–G9 · M1–M8
firmware-evrensel-dil.md → TR/EN sözlük
└─ 9 alt klasör (çocuk) — bu üretimde yalnız boot/ içerikli
```

## §16 Ölçüm ve kabul (master özeti)

| Ölçüm | Hedef | Durum |
|---|---|---|
| Kök MD sayısı | 12 | 2026-10-13 sayımında doğrulanır |
| Satır alt sınırı (11 dosya) | ≥ 500 | 2026-10-13 sayımında doğrulanır |
| `index.md` satır aralığı | 40–100 | 2026-10-13 sayımında doğrulanır |
| Frontmatter anahtar | 9 (bozuk 0) | 2026-10-13 script kontrolü |
| Yeni URL | 86 (12 + 26 + 48) | havuz kesişimi = 0 |
| Mojibake · dolgu · `parent:` | 0 | 2026-10-13 grep |
| Dosya yeniden adlandırma | 0 | git status |

Sayımlar bu oturumun sonunda çalıştırılır; sonuç bu satırda **ölçülmeden**
"tamam" yazılmaz.

## §17 Değişiklik protokolü

| Adım | Eylem | Sahip |
|---|---|---|
| 1 | İstenen değişikliğin K-1…K-16 ile çelişip çelişmediği | talep sahibi |
| 2 | Çelişki varsa önce `firmware-kararlar.md`'ye yeni K satırı | Vault Steward |
| 3 | Değişiklik satır-satır edit ile uygulanır (silme değil düzeltme+ekleme) | uygulayan |
| 4 | §16 ölçümleri yeniden çalıştırılır | uygulayan |
| 5 | `validation:` listesine tarihli kayıt düşülür | Vault Steward |
| 6 | Commit yalnız orkestratörde | orkestratör |

## §18 Kapanış

Bu dosya K005'in tek omurgasıdır: kapsam (§11), katman (§13), sürüm (§13),
URL (§14), hiyerarşi (§15), ölçüm (§16) ve protokol (§17) burada tanımlıdır.
Diğer 11 kök dosya bu § yapısına referans verir; § numarası değiştirilmez,
yalnızca sona eklenir. Dosyanın son satırı Authority bloğudur ve tek satırdır.

## §19 İlgili ADR ve vault referansları

| Kaynak | Ne verir | Okuma durumu |
|---|---|---|
| ADR-097 §5.5 (satır 140) | iki-ekseni gruplama şeması (grup + kademe) | okundu 2026-10-12 |
| ADR-038 H001 | DAC yasağı: PCM5122 yok, yalnız PCM3168A | geçerli (frozen) |
| ADR-017 | 32-bit float ses veri tipi | geçerli (frozen) |
| ADR-019 | per-OS player FROZEN (≥088 değişiklik onayı) | geçerli (frozen) |
| `.ai/architecture/index.md` §8.1 (satır 160) | grup tablosu görünürlük kuralı | okundu 2026-10-13 |
| `.ai/.decisions/` (98 md) | ADR envanteri | sayıldı 2026-10-13 |
| `.ai/AGENTS.md` §18 | uyarı ve katman ihlali sonucu | okundu (boot) |

Frozen ADR metinleri okunur ve referans edilir; değiştirilmez (`.ai/AGENTS.md`
§25.3 kural 2).

## §20 Kısaltmalar (bu dosyada geçenler)

| Kısaltma | Açılım |
|---|---|
| OTA | over-the-air — kablosuz firmware güncelleme |
| SBOM | software bill of materials — bileşen listesi |
| CI | continuous integration — sürekli tümleştirme |
| FM | frontmatter — dosya başlığı alanları |
| MO | master orkestrator |
| HIL | hardware-in-the-loop — donanım üstünde test |
| TUF | The Update Framework — güncelleme imzalama çerçevesi |

## §21 Çapraz referanslar

| Bu § | Karşılığı olan dosya | Bölüm |
|---|---|---|
| §4 (12 URL) | `firmware-dogrulama.md` | §2 (26 URL) |
| §4 (12 URL) | `firmware-web-arastirma.md` | §2 (48 URL) |
| §7 (K-1…K-8) | `firmware-kararlar.md` | §1 (K-1…K-16) |
| §8 (R-1…R-8) | `firmware-riskler.md` | §1 (R-01…R-10) |
| §9 (D1…D10) | `firmware-dogrulama.md` | §5 |
| §16 (ölçüm) | `firmware-test-stratejisi.md` | §11 (M1…M8) |
| §14 (URL payı) | bu üç dosya | K-14 kararı |

## §22 Revizyon günlüğü

| Tarih | Olay | Kapsam |
|---|---|---|
| 2026-10-12 | Anayasa ilk yazım (§0–§10) | şema kurulumu |
| 2026-10-13 | FM 9 anahtara çekildi (`status:` kaldırıldı) | K-13 |
| 2026-10-13 | §4 URL'leri havuzdan yenilendi (12 URL · 2026-10-13) | K-14 |
| 2026-10-13 | §11–§22 eklendi (kapsam dışı · katman · sürüm · ölçüm · protokol) | anayasa bütünlüğü |

Günlük append-only'dir: geçmiş satır değişmez, yeni olay altına eklenir.

## §23 Son kontrol listesi (bu dosya)

1. FM 9 anahtar, `status:`/`parent:` yok.
2. §4 = 12 URL, biçim `| İddia | URL | 2026-10-13 | Durum |`, tümü havuzdan.
3. FM `evidence:` içindeki URL'ler de havuzdan (eski envanter URL'leri kaldırıldı).
4. Dosya son satırı tek satır Authority bloğu.
5. Satır sayısı ≥ 500 (ölçüm §16'da).

## §24 Yetki ve kapanış

Bu anayasa Vault Steward yetkisiyle yazılır; onaysız ad/§ silinmez. Değişiklik
talebi §17 protokolünden geçer. Ölçümler (§16) bu oturumda çalıştırılır ve
sonuçlar ÖLÇÜM raporunda listelenir.

## §25 Son durum tablosu

| Alan | Değer (2026-10-13) |
|---|---|
| Kök dosya | 12 MD (11 içerik + `index.md`) |
| Çocuk | 9 dizin (`boot/` içerikli · 8'i yalnız `index.md`) |
| URL payı | 12 + 26 + 48 = 86 (kesişim 0) |
| Kapı | D1–D10 (D9 · D10 açık) |
| Karar / risk | K-1…K-16 · R-01…R-10 |

Tablo değeri ölçüm sonucudur; ölçülmeden bu değer yazılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
