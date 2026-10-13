---
type: system
category: electronics-platform
title: "CoreMusic Electronics Platform Architecture"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Platform Architecture

**Zorunlu Bağlantılar:** [[electronic/core-music-electronics-overview]] · [[electronic/device-architecture]] · [[electronic/software-architecture]] · [[architecture/l6-electronics]] · [[ADR-061-electronics-architecture]] · [[electronic/audio-architecture]] · [[electronic/operating-system-architecture]]

---

## 1. Amaç

CoreMusic ELECTRONICS platformu, çoklu cihaz ailelerini destekleyen **9 katmanlı bir mimariye** sahiptir. Her katman belirli bir sorumluluk alanı tanımlar ve katmanlar arası bağımlılıklar sıkı şekilde kontrol edilir. Ortak yazılım altyapısı ile tüm cihaz aileleri aynı geliştirme sürecini paylaşır.

---

## 2. Platform Katmanları (9 Katman)

```
Katman 9: AI Layer ──────────── Auto-EQ, Room Correction, Prediction
Katman 8: Application Layer ─── Web UI, Mobile App, Desktop
Katman 7: API Layer ─────────── REST, WebSocket, gRPC
Katman 6: Middleware Layer ──── Auth, Logging, Caching
Katman 5: Audio Engine Layer ── Mixer, EQ, DSP Chain
Katman 4: Driver Layer ──────── ASIO, WASAPI, ALSA
Katman 3: Firmware Layer ────── RTOS, Boot, DSP Chain
Katman 2: Hardware Layer ────── PCB, Components, Connectors
Katman 1: Device Family Layer ─ Pro, Home, Car, Embedded
```

---

## 3. Ürün Aileleri

### 3.1 Professional Audio

| Cihaz | Açıklama | Öncelik |
|-------|----------|---------|
| Studio Audio Interface | Stüdyo ses arayüzü (USB/Thunderbolt) | Yüksek |
| Rack DSP Processor | Raf tipi DSP işlemcisi | Yüksek |
| Professional DAC/ADC | Profesyonel dönüşürücü | Orta |
| Professional Mixer | Profesyonel mikser | Orta |
| Monitor Controller | Monitör kontrolcüsü | Düşük |

### 3.2 Home Audio

| Cihaz | Açıklama | Öncelik |
|-------|----------|---------|
| Smart Amplifier | Akıllı amplifikatör | Yüksek |
| Home Audio Receiver | Ev ses alıcısı | Yüksek |
| Multi Room Controller | Çoklu oda kontrolcüsü | Yüksek |
| Streaming Audio Player | Streaming ses oynatıcı | Orta |
| Smart Speaker | Akıllı hoparlör | Orta |

### 3.3 Car Audio

| Cihaz | Açıklama | Öncelik |
|-------|----------|---------|
| DSP Amplifier | DSP amplifikatör | Yüksek |
| Digital Crossover | Dijital crossover | Orta |
| OEM Audio Processor | OEM ses işlemcisi | Orta |
| Android Head Unit | Android bilgi-eğlence | Yüksek |

### 3.4 Embedded Audio

| Cihaz | Açıklama | Öncelik |
|-------|----------|---------|
| Raspberry Pi Audio HAT | RPi ses genişletme kartı | Yüksek |
| ARM Audio Board | ARM ses kartı | Orta |
| Embedded Linux Audio Device | Gömülü Linux ses cihazı | Orta |

### 3.5 Development Boards

| Cihaz | Açıklama | Öncelik |
|-------|----------|---------|
| Audio Development Board | Ses geliştirme kartı | Yüksek |
| DSP Evaluation Board | DSP değerlendirme kartı | Yüksek |
| Driver Test Board | Sürücü test kartı | Orta |

---

## 4. Cihaz Standartları

Her cihaz aşağıdaki standart yaşam döngüsüne uyar:

| Standart Katman | Açıklama |
|-----------------|----------|
| Device | Cihaz tanımı ve konfigürasyonu |
| Hardware | Fiziksel bileşen ve devre tasarımı |
| Firmware | Gömülü yazılım ve RTOS |
| Driver | İşletim sistemi sürücüsü |
| DSP | Dijital sinyal işleme zinciri |
| API | Dış erişim arayüzü |
| Application | Kullanıcı arayüzü |
| Cloud | Uzaktan yönetim ve güncelleme |
| AI | Akıllı özellikler ve otomasyon |

---

## 5. Ortak Yazılım Altyapısı

| Altyapı | Açıklama |
|---------|----------|
| Kimlik Yönetimi | Cihaz kimlik doğrulama, token yönetimi |
| Güncelleme (OTA) | Over-the-air firmware güncellemesi |
| Telemetri | Cihaz durumu izleme, loglama |
| AI Servisleri | Otomatik EQ, oda akustiği, bakım tahmini |
| Güvenlik | Secure boot, imzalı firmware, şifreleme |

---

## 6. Katman Detayları

### 6.1 Device Family Layer

Her cihaz ailesi ortak mimariyi paylaşır:

| Cihaz Ailesi | Ortak Altyapı |
|--------------|---------------|
| Professional Audio | Aynı mimari, yazılım standartları, protokoller |
| Home Audio | Aynı mimari, yazılım standartları, protokoller |
| Car Audio | Aynı mimari, yazılım standartları, protokoller |
| Embedded Audio | Aynı mimari, yazılım standartları, protokoller |
| Development Boards | Aynı mimari, yazılım standartları, protokoller |

### 6.2 Hardware Layer

Fiziksel bileşenler ve devre tasarımı:

- **PCB Tasarımı:** 4-16 katmanlı PCB, high-speed routing
- **Bileşen Seçimi:** endüstriyel/otomotiv/askeri sınıf
- **Güç Dağıtımı:** multi-rail, low-noise, EMI filtering
- **Termal Yönetim:** heatsink, fan, thermal pad
- **EMI/EMC:** uyumluluk testleri, sertifikasyon

Detaylar: [[electronic/hardware/index]], [[ADR-063-hardware-design-standards]]

### 6.3 Firmware Layer

Gömülü yazılım katmanı:

- **RTOS:** FreeRTOS, Zephyr, bare-metal
- **Boot Loader:** secure boot, OTA update
- **DSP Chain:** real-time audio processing
- **Communication:** USB, Ethernet, I2S, TDM

Detaylar: [[electronic/firmware/index]], [[ADR-062-dsp-pipeline-architecture]]

### 6.4 Driver Layer

İşletim sistemi sürücüleri:

- **Windows:** ASIO, WASAPI, WDM, Kernel Streaming
- **Linux:** ALSA, PipeWire, JACK, PulseAudio
- **macOS:** CoreAudio, AudioUnit

Detaylar: [[electronic/drivers/index]], [[electronic/asio-driver-design]]

### 6.5 Audio Engine Layer

Ses işleme motoru:

- **Mixer:** çoklu kanal karıştırma
- **EQ:** 31-bant parametrik equalizer
- **DSP Chain:** EQ → compressor → limiter → crossover
- **Spatial Audio:** surround, atmos

Detaylar: [[electronic/audio-architecture]], [[electronic/dsp/index]]

### 6.6 Middleware Layer

Ortak katman hizmetleri:

- **Authentication:** cihaz kimlik doğrulama
- **Logging:** olay günlüğü
- **Caching:** performans önbellekleme
- **Rate Limiting:** istek sınırlandırma

### 6.7 API Layer

Dış erişim arayüzü:

- **REST API:** CRUD işlemleri
- **WebSocket:** real-time iletişim
- **gRPC:** yüksek performanslı iletişim

### 6.8 Application Layer

Kullanıcı arayüzü:

- **Web UI:** tarayıcı tabanlı yönetim
- **Mobile App:** iOS/Android kumanda
- **Desktop App:** yerel uygulama

### 6.9 AI Layer

Akıllı özellikler:

- **Auto-EQ:** Otomatik equalizer ayarlama
- **Room Correction:** Oda akustik düzeltmesi
- **Predictive Maintenance:** Öngörücü bakım
- **Performance Optimization:** DSP parametre optimizasyonu

---

## 7. Cihaz Yaşam Döngüsü (16 Faz)

```
Concept ──▶ Design ──▶ Prototype ──▶ Testing ──▶ Certification ──▶ Manufacturing
                                                                        │
Shipping ◀─────────────────────────────────────────────────────────────┘
    │
    ▼
Installation ──▶ Configuration ──▶ Activation ──▶ Operation ──▶ Maintenance
                                                          │           │
                                                          │      Update
                                                          │           │
                                                          │      Operation
                                                          │           │
                                                          └──▶ Decommission
```

| Faz | Açıklama |
|-----|----------|
| 1. Concept | Konsept geliştirme |
| 2. Design | Donanım/yazılım tasarımı |
| 3. Prototype | Prototip üretimi |
| 4. Testing | Test ve doğrulama |
| 5. Certification | Sertifikasyon (CE, FCC) |
| 6. Manufacturing | Üretim |
| 7. Shipping | Sevkiyat |
| 8. Installation | Kurulum |
| 9. Configuration | Konfigürasyon |
| 10. Activation | Aktivasyon |
| 11. Operation | Normal kullanım |
| 12. Maintenance | Bakım |
| 13. Update | Güncelleme (OTA) |
| 14. Upgrade | Yükseltme |
| 15. Support | Teknik destek |
| 16. Decommission | Emekliye ayırma |

---

## 8. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/core-music-electronics-overview]] | Genel bakış |
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/audio-architecture]] | Ses mimarisi |
| [[electronic/operating-system-architecture]] | OS mimarisi |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi |
| [[electronic/software-architecture]] | Yazılım mimarisi |
| [[electronic/service-architecture]] | Servis mimarisi |
| [[architecture/l6-electronics]] | L6 Electronics katmanı |
| [[ADR-061-electronics-architecture]] | Electronics ADR |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Platform Layers | 9 |
| Device Families | 5 |
| Lifecycle Phases | 16 |
| Cross References | 9 |

---

## 10. Katmanlar Arası İzinli / Yasak Bağımlılık Matrisi

`## 2`'deki 9 katmanın her çifti için izin durumu. ✅ = doğrudan çağrı izinli · 🔄 = yalnızca olay/telemetri üzerinden · ❌ = yasak.

| Üst \ Alt | 9 AI | 8 App | 7 API | 6 MW | 5 Audio | 4 Driver | 3 FW | 2 HW | 1 Family |
|-----------|------|-------|-------|------|---------|----------|------|------|----------|
| 9 AI Layer | — | ✅ | ✅ | ✅ | 🔄 | ❌ | ❌ | ❌ | ✅ |
| 8 Application | ❌ | — | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ |
| 7 API | ❌ | ✅ | — | ✅ | 🔄 | ❌ | ❌ | ❌ | ✅ |
| 6 Middleware | ❌ | ✅ | ✅ | — | ❌ | ❌ | ❌ | ❌ | ✅ |
| 5 Audio Engine | ❌ | ❌ | ✅ | ✅ | — | ✅ | ❌ | ❌ | ✅ |
| 4 Driver | ❌ | ❌ | ❌ | ❌ | ❌ | — | ✅ | 🔄 | ✅ |
| 3 Firmware | ❌ | ❌ | ❌ | ❌ | 🔄 | ❌ | — | ✅ | ✅ |
| 2 Hardware | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | 🔄 | — | ✅ |
| 1 Device Family | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | — |

**Okuma kuralı:** Sütun = çağrı edilen katman, satır = çağıran katman. `❌` olan her hücre, kod denetiminde **katman ihlali** sayılır ve revert edilir.

**Geri besleme yolu:** Ölçüm ve hata yalnız `2 → 3 → 4 → 5` (telemetri) ve `7 → 8` (durum) yönünde akar; yukarı katmanlar aşağı katmanın iç durumunu **yazamaz**, yalnız okuyabilir.

**AI katmanının sınırı:** Katman 9 yalnız `8` ve `7` üzerinden eylem üretir; `4`, `3`, `2`'ye **asla** doğrudan yazmaz. AI bir komut üretir, katman 7 onu sıradan bir komut gibi işler — böylece AI'nın çıktısı denetlenebilir kalır.

---

## 11. Zaman Senkronizasyonu ve Saat Mimarisi

Çoklu cihaz ve çoklu oda senaryosunda ortak zaman eksikliği ses bozulmasının (jitter, faz kayması) birincil kaynağıdır. Bu bölüm platformun saat katmanını tanımlar.

| Gereksinim | Yaklaşım | Kapsam | Durum |
|------------|----------|--------|-------|
| Cihazlar arası ortak zaman | IEEE 1588 (Precision Time Protocol) | Ağ üzerinden senkron | `⚠️ VERIFICATION REQUIRED` — uygulanmadı |
| Tek cihaz içinde örnek hizası | Donanım saatini besleyen I2S/TDM bit clock | Tek PCB | `⚠️ VERIFICATION REQUIRED` — ölçülmedi |
| Uygulama düzeyinde olay sırası | Monotonik sayaç + sıra numarası | Yazılım | Tasarım |
| Telemetri zaman damgası | Kayıt anında alınan mutlak zaman | Loglama | Tasarım |
| OTA paketi zaman geçerliliği | Paket üzerinde geçerlilik aralığı | `[[firmware/ota-update]]` | Tasarım |
| Ses tamponu hizası | Callback başına sayaç (underrun/overrun sayacı) | Sürücü katmanı | Tasarım — ölçülmedi |

**Kural:** İki ses çıkışının birlikte çalması gereken her senaryoda (çoklu oda, surround) **tek bir referans saat** tanımlanır; referans saat yoksa o senaryo `UNKNOWN` olarak işaretlenir ve "senkron" iddiası kullanılamaz.

**Ölçüm gereksinimi:** Jitter hedefi bu dosyada sayısal olarak **verilmemiştir** — verilirse uydurma olur. Ölçüm, ağ üzerinden ve tek cihaz içinde ayrı ayrı yapılır.

---

## 12. Araç Zinciri ve Derleme Stratejisi

Platformun firmware tarafı için derleme/yayın zinciri. Bu bölüm **yapısal** tanımdır; kullanılan araç sürümleri `## 15` kapsamındaki doğrulamaya tabidir.

| Aşama | Araç Sınıfı | Girdi | Çıktı | Sorumluluk Katmanı |
|--------|-------------|-------|-------|--------------------|
| Kaynak derleme | C/C++ derleyicisi (GCC sınıfı) | Kaynak dosyalar | Nesne dosyaları | 3 Firmware |
| Bağlama | Bağlayıcı | Nesne dosyaları | ELF / imzalanmamış görüntü | 3 Firmware |
| Görüntü üretimi | Görüntü aracı | ELF | Flash görüntüsü | 3 Firmware |
| İmzalama | İmzalama aracı + özel anahtar | Flash görüntüsü | İmzalı paket | 5 Ortak altyapı (`## 5`) |
| Paketleme | Bölüm/paket tablosu oluşturucu | Görüntü + meta | OTA paketi | `[[firmware/ota-update]]` |
| Kurulum | Bootloader + OTA istemcisi | OTA paketi | Çalışan firmware | 3 Firmware |
| Doğrulama | Bootloader imza denetimi | İmzalı paket | Kabul / redd | 3 Firmware (güvenli boot) |

**Sürüm kanıtı uyarısı:** Bu depoda C/C++ firmware kaynağı **bulunmadığından** derleyici sürümü, bayrakları ve yeniden üretilebilirlik durumu `⚠️ VERIFICATION REQUIRED` olarak kalır; araç adı dahi tahmin edilerek sabitlenmez.

**İmzalama zorunluluğu:** `## 5`'teki "imzalı firmware" maddesi bu tablonun 4. ve 7. satırlarında somutlaşır; imzasız görüntü hiçbir aşamada cihaza kabul ettirilemez.

---

## 13. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden alınmıştır; satır numarası `Select-Object -Index (N-1)` ile doğrulanmıştır.

**Kanıt 1 — API katmanının arkasındaki DI sözleşmesi**

`Dosya: shared/src/Api/Gateway.php` · `Satır: 30`

```php
private readonly ServiceRegistryInterface $serviceRegistry,
```

**Kanıt 2 — Middleware katmanı ayrı bir pipeline sınıfında**

`Dosya: shared/src/Api/Middleware/ApiMiddlewarePipeline.php` · `Satır: 17`

```php
final class ApiMiddlewarePipeline
```

**Kanıt 3 — BFF, API ile Application arasında katman olarak duruyor**

`Dosya: shared/src/Api/Bff/BffLayer.php` · `Satır: 19`

```php
final class BffLayer
```

**Kanıt 4 — Uygulama katmanı ses elemanını kendisi render ediyor**

`Dosya: shared/src/PageRouter/HtmlShellRenderer.php` · `Satır: 192`

```php
echo '<audio id="audio" class="vdisplay" preload="metadata"></audio>';
```

**Yorum:** Kanıt 1–3, `## 2`'deki 7 (API) ve 6 (Middleware) katmanlarının repo karşılığını gösterir; Kanıt 4, 8 (Application) katmanının varlığını. **Buna karşılık 5 (Audio Engine), 4 (Driver), 3 (Firmware), 2 (Hardware) katmanlarının bu depoda karşılığı YOKTUR** — bu dört katman yalnız vault'ta yaşar.

---

## 14. Kapsam Sınırı ve Dürüstlük Beyanı

| İddia | Durum | Dayanak |
|-------|-------|---------|
| 9 katmanlı mimari | Vault tasarım tanımı | Bu dosya §2 |
| 5 ürün ailesi · 16 fazlı yaşam döngüsü | Vault tasarım tanımı | Bu dosya §3, §7 |
| IEEE 1588 tabanlı senkronizasyon | **Uygulanmadı** — gereksinim olarak yazılı | `⚠️ VERIFICATION REQUIRED` |
| 4–16 katman PCB, endüstriyel/otomotiv/askeri sınıf bileşen | Tasarım hedefi — ölçülmedi | `[[ADR-063-hardware-design-standards]]` |
| Secure boot · imzalı firmware | Tasarım gereksinimi — uygulama yok | `⚠️ VERIFICATION REQUIRED` |
| CE / FCC sertifikasyonu (§7 faz 5) | Alınmış sertifika **YOK** | `⚠️ VERIFICATION REQUIRED` |
| gRPC ve WebSocket API'leri | Tasarım — repo'da varlığı doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| `PCM5122` | **KATI YASAK** | ADR-038 H001 |
| Repo'da firmware/hardware/driver kaynak kodu | **YOK** | Dizin taraması |
| Araç sürümleri (derleyici, paketleyici) | `UNKNOWN` | Doğrulanmadı |

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

**İmla koruması:** Vault içindeki bilinen yazım kaymaları dosya/dizin adıdır, düzeltilmesi wiki-link'leri kırar → `[[electronic/index]]` §15.

---

## 15. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Ağ üzerinden hassas zaman senkronizasyonu standardı (IEEE 1588) | https://standards.ieee.org/ieee/1588/6959/ | 2026-10-13 | HTTP 200 ✓ |
| Gömülü sistemler için serbest/kullanım-başımsız C/C++ derleyici projesi | https://www.gnu.org/software/gcc/ | 2026-10-13 | HTTP 200 ✓ |

**Sınır:** Bu kaynaklar ilgili teknolojinin varlığını ve kapsamını destekler; CoreMusic'in bu teknolojiyi **kullandığı** iddiasını desteklemez — kullanım için repo/disk kanıtı gerekir.

---

## 16. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | 9 katmanın repo'daki karşılığı yalnız 3 tanesi (§13) — kalan 6'sı nerede? | `UNKNOWN` | Katman → dizin eşlemesi |
| 2 | IEEE 1588 gereksinimi hangi cihaz ailesi için zorunlu? | `⚠️ VERIFICATION REQUIRED` | Cihaz bazlı gereksinim matrisi |
| 3 | `## 7` 16 fazından hangileri gerçekten yürütüldü? | `UNKNOWN` | Proje kaydı okuması |
| 4 | CE/FCC sertifikasyonu için test raporu var mı? | `UNKNOWN` (rapor yok) | Sertifika dosyası talebi |
| 5 | `[[electronic/asio-driver-design]]` dosyası diskte var mı? | `⚠️ VERIFICATION REQUIRED` | Dizin doğrulaması |
| 6 | Bölüm/paket tablosu (§12) hangi biçimde? | `UNKNOWN` | `[[firmware/ota-update]]` okuması |
| 7 | AI katmanının eylem denetimi (§10) nasıl uygulanacak? | `⚠️ VERIFICATION REQUIRED` | Tasarım kararı + ADR |

---

## 17. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| Platform katmanı | Sorumluluğu tanımlanmış, bağımlılığı sınırlanmış mimari seviye | `## 2` |
| Device Family Layer | Cihaz ailesi soyutlaması — en alt katman | `## 6.1` |
| Telemetri | Donanımdan yukarı akan ölçüm/hata akışı | `## 10` |
| Referans saat | Tüm cihazların hizalandığı tek zaman kaynağı | `## 11` |
| PTP (IEEE 1588) | Ağ üzerinden mikro-saniye düzeyi zaman senkronizasyonu | `## 11` |
| Bit clock | I2S/TDM hattını taşıyan donanım saati | `## 11` |
| Jitter | Zaman aralığındaki rastgele sapma | `## 11` |
| Underrun | Tamponun zamanında doldurulamaması | `## 11` |
| Görüntü (image) | Flash'a yazılabilir firmware paketi | `## 12` |
| Güvenli boot | Açılışta imza denetimi yapan bootloader davranışı | `## 12` |
| OTA | Cihaza kablosuz/kablolu uzaktan güncelleme | `[[firmware/ota-update]]` |
| Bölüm tablosu | Flash'ın bölgelere ayrıldığını anlatan yapı | `## 12` |
| Katman ihlali | ❌ olan bir hücrede çağrı yapılması | `## 10` |
| Ölçülmemiş iddia | Etiketi `⚠️ VERIFICATION REQUIRED` olan satır | `## 14` |

---

## 18. Ürün Ailesi → Katman Kapsamı

Her cihaz ailesinin `## 2`'deki katmanları ne ölçüde kullandığını gösterir. ✅ = tam kapsam · ◐ = kısmi · — = kullanılmaz.

| Cihaz Ailesi | 9 AI | 8 App | 7 API | 6 MW | 5 Audio | 4 Driver | 3 FW | 2 HW | 1 Family |
|--------------|------|-------|-------|------|---------|----------|------|------|----------|
| Professional Audio | ◐ | ◐ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Home Audio | ✅ | ✅ | ✅ | ✅ | ✅ | ◐ | ✅ | ✅ | ✅ |
| Car Audio | ◐ | ◐ | ◐ | ✅ | ✅ | ◐ | ✅ | ✅ | ✅ |
| Embedded Audio | — | ◐ | ◐ | ◐ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Development Boards | — | — | ◐ | — | ✅ | ✅ | ✅ | ✅ | ✅ |

**Okuma kuralı:** `—` olan hücre, o ailede katmanın bulunmadığı anlamına gelir; `◐` ise katmanın var olduğu ama kapsamının daraltıldığı (örneğin mobil uygulamasız) anlamına gelir.

**Öncelik bağlantısı:** `## 3`'teki öncelik sütunu (Yüksek/Orta/Düşük) ile bu tablo birlikte okunur; öncelik yüksek olan aile, `—` hücresi taşıyamaz.

**Doğrulama durumu:** Bu matris vault tasarım iddiasıdır; her hücrenin cihaz bazlı gereksinim belgesiyle desteklenmesi gerekir → `⚠️ VERIFICATION REQUIRED`.

---

## 19. Yaşam Döngüsü Fazlarının Çıktıları

`## 7`'deki 16 fazın her birinin **zorunlu çıktısı**. Çıktı yoksa faz kapatılamaz.

| Faz | Zorunlu Çıktı | Kabul Eden | Şu Anki Durum |
|-----|---------------|------------|---------------|
| 1. Concept | Konsept notu, hedef pazar | Ürün sorumlusu | `UNKNOWN` |
| 2. Design | Şema, BOM, mimari karar (ADR) | Tasarım gözden geçirme | Kısmi — ADR'ler vault'ta |
| 3. Prototype | Fiziksel prototip, ilk ölçüm | Test ekibi | `UNKNOWN` |
| 4. Testing | Test raporu, ölçüm verisi | Test ekibi | `⚠️ VERIFICATION REQUIRED` |
| 5. Certification | Sertifika belgesi (CE/FCC) | Uyumluluk | `UNKNOWN` — belge yok |
| 6. Manufacturing | Üretim dosyası, kalite planı | Üretim | `UNKNOWN` |
| 7. Shipping | Sevkiyat kaydı | Lojistik | `UNKNOWN` |
| 8. Installation | Kurulum kontrol listesi | Saha ekibi | `UNKNOWN` |
| 9. Configuration | Cihaz konfigürasyonu | Kurulum ekibi | `UNKNOWN` |
| 10. Activation | Aktivasyon kaydı, anahtar | Lisans | `UNKNOWN` |
| 11. Operation | Çalışma telemetrisi | İzleme | `UNKNOWN` |
| 12. Maintenance | Bakım kaydı, parça değişimi | Bakım | `UNKNOWN` |
| 13. Update | İmzalı OTA paketi + rollback planı | `[[firmware/ota-update]]` | Tasarım |
| 14. Upgrade | Sürüm geçiş notu | Sürüm yönetimi | `UNKNOWN` |
| 15. Support | Destek kaydı, çözüm veritabanı | Destek | `UNKNOWN` |
| 16. Decommission | Veri silme, geri dönüşüm kaydı | Uyumluluk | `UNKNOWN` |

**Durum dağılımı:** 16 fazın **0'ı** bu depoda kanıtlanmış çıktıya sahiptir; 1'i (Design) kısmen vault ADR'leriyle, 1'i (Update) tasarımsal olarak tanımlıdır. Kalanı `UNKNOWN` işaretlenmiştir — tahmin edilerek "tamamlandı" yazılmaz.

**Bağlantı:** Faz çıktılarının üretim akışı `[[electronic/development-workflow]]` içinde tanımlıdır; bu dosya yalnız **ne üretileceğini** tanımlar, **nasıl üretileceğini** değil.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
