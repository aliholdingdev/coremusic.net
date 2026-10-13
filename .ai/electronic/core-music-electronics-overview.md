---
type: system
category: electronics-overview
title: "CoreMusic Electronics Overview"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Overview

**Zorunlu Bağlantılar:** [[architecture/l6-electronics]] · [[electronic/platform-architecture]] · [[electronic/device-architecture]] · [[electronic/software-architecture]] · [[electronic/service-architecture]] · [[electronic/audio-architecture]] · [[electronic/operating-system-architecture]]

---

## 1. Amaç

CoreMusic ELECTRONICS, CoreMusic platformunun **fiziksel donanım katmanını yöneten elektronik sistem mimarisidir.** Amaç, aynı yazılım mimarisini kullanarak farklı elektronik cihazların geliştirilmesini sağlamaktır.

- Fiziksel donanım ile yazılım arasındaki köprü
- Ortak mühendislik platformu yaklaşımı
- AI destekli elektronik geliştirme
- Çoklu cihaz ailesi desteği

---

## 2. Vizyon

Aynı yazılım mimarisiyle farklı elektronik cihazların geliştirilmesini sağlamak:

| Cihaz Ailesi | Açıklama | Örnekler |
|--------------|----------|----------|
| Professional Audio | Stüdyo ses donanımı | Audio interface, Rack DSP, Mixer |
| Home Audio | Ev ses sistemleri | Amplifikatör, Receiver, Streaming |
| Car Audio | Araç içi ses | DSP Amplifikatör, Head Unit |
| Embedded Audio | Gömülü sistemler | RPi HAT, ARM Board, Linux Device |
| Development Boards | Geliştirme kartları | Eval Board, Prototype Board |

---

## 3. Felsefe

**Tüm katmanlar birlikte geliştirilir** — sadece firmware değil:

| Katman | Kapsam |
|--------|--------|
| **Hardware** | PCB, bileşenler, güç dağıtımı, EMI/EMC |
| **Firmware** | RTOS, boot loader, DSP chain |
| **Driver** | İşletim sistemi sürücüleri (ASIO, WASAPI, ALSA) |
| **Audio Engine** | Mixer, EQ, compressor, limiter, crossover |
| **Middleware** | Auth, logging, caching, rate limiting |
| **Application** | Web UI, mobile app, desktop app |

---

## 4. Mimari Katmanlar (6 Katman)

```
Application Layer ─────── Web UI, Mobile App, Desktop
Middleware Layer ──────── Auth, Logging, Caching
Audio Engine Layer ────── Mixer, EQ, DSP Chain
Driver Layer ──────────── ASIO, WASAPI, ALSA, CoreAudio
Firmware Layer ────────── RTOS, Boot, DSP Chain
Hardware Layer ────────── PCB, Components, Connectors
```

---

## 5. Cihaz Aileleri

| Aile | Sorumluluk | ADR |
|------|------------|-----|
| Professional Audio | Stüdyo ses arayüzü, rack DSP, mixer | [[electronic/device-architecture]] |
| Home Audio | Amplifikatör, receiver, streaming player | [[electronic/device-architecture]] |
| Car Audio | DSP amplifikatör, Android head unit | [[electronic/device-architecture]] |
| Embedded Audio | RPi HAT, ARM board, gömülü Linux | [[electronic/device-architecture]] |
| Development Boards | Eval board, prototip kartı | [[electronic/device-architecture]] |

---

## 6. Ortak Mühendislik Platformu

Her cihaz ailesi aynı altyapıyı paylaşır:

| Ortak Altyapı | Açıklama |
|---------------|----------|
| Mimari | Aynı 6 katmanlı mimari yapı |
| Yazılım Standartları | SOLID, Clean Architecture, DDD |
| İletişim Protokolleri | USB, I2S, TDM, Ethernet, Wireless |
| AI Altyapısı | Otomatik EQ, oda akustiği, bakım tahmini |
| Geliştirme Süreci | 16 fazlı cihaz yaşam döngüsü |

---

## 7. AI Destekli Elektronik Geliştirme

| AI Yeteneği | Açıklama | Kullanım |
|-------------|----------|----------|
| Otomatik EQ | Hoparlör ve oda analizine göre EQ | Tüm cihazlar |
| Oda Akustiği | Oda yanıt analizi ve düzeltme | Home, Studio |
| Hoparlör Yerleşimi | Optimal yerleşim önerisi | Home, Car |
| Frekans Analizi | Spektral analiz ve optimizasyon | Tüm cihazlar |
| Öngörücü Bakım | Donanım arıza tahmini | Tüm cihazlar |
| Performans Optimizasyonu | DSP parametre optimizasyonu | Tüm cihazlar |
| Isı Yönetimi | Termal analiz ve soğutma | Tüm cihazlar |

---

## 8. CoreMusic Electronics Bileşenleri

### 8.1 Donanım Bileşenleri

| Bileşen | Örnekler | ADR |
|---------|----------|-----|
| DAC/ADC | PCM3168A, AK4458, CS4272 | [[ADR-038-8.1-sound-card-chip-selection]] |
| DSP | XMOS XU316, SHARC, TMS320 | [[ADR-017-dsp-hardware-mode]] |
| Amplifikatör | Class AB, Class D, TPA3255 | [[electronic/amplifier-design]] |
| MCU | STM32, ESP32, NXP i.MX | — |
| FPGA | Xilinx, Intel (Altera) | — |

### 8.2 Yazılım Bileşenleri

| Bileşen | Kapsam | Katman |
|---------|--------|--------|
| Firmware | RTOS, boot, DSP chain | Firmware |
| Driver | ASIO, WASAPI, ALSA, CoreAudio | Driver |
| Audio Engine | Mixer, EQ, compressor, limiter | [[electronic/dsp/index]] |
| Middleware | Auth, logging, caching | Middleware |
| Web Management | Dashboard, config, monitoring | Application |

### 8.3 AI Bileşenleri

| Bileşen | Kapsam |
|---------|--------|
| Auto-EQ | Otomatik equalizer ayarlama |
| Room Correction | Oda akustik düzeltmesi |
| Bass Management | Otomatik bas yönetimi |
| DSP Profile | Kişiselleştirilmiş DSP profilleri |
| Predictive Maintenance | Öngörücü bakım |

---

## 9. Geliştirme Felsefesi

| Prensipl | Açıklama | ADR |
|----------|----------|-----|
| SOLID | Tek sorumluluk, açık-kapalı, yerine koyma | [[brain.md]] §3 |
| Clean Architecture | L0-L6 katmanlı yapı | [[architecture/l6-electronics]] |
| Hexagonal Architecture | Adapter/Port pattern | [[brain.md]] §3 |
| DDD | Domain-Driven Design | [[electronic/software-architecture]] |
| CQRS | Command Query Responsibility Segregation | [[electronic/service-architecture]] |
| Event Driven | Asenkron olay tabanlı iletişim | [[electronic/service-architecture]] |
| Zero-Allocation | Audio thread'de heap allocation yasak | [[brain.md]] §7.1 |
| Lock-Free | Audio thread'de mutex yasak | [[brain.md]] §7.1 |

---

## 10. 12 Electronics Alt Sistemi

| # | Alt Sistem | Açıklama |
|---|-----------|----------|
| 1 | Sanal Ses Kartı (Virtual Audio Driver) | Yazılımsal ses kartı |
| 2 | Gerçek Ses Kartı (Donanım) | Fiziksel ses kartı |
| 3 | 8+1 Ses Desteği | 1 sub + 8 speakers |
| 4 | 5+1 Ses Desteği | 1 sub + 5 speakers |
| 5 | 2+1 Ses Desteği | 1 sub + 2 speakers |
| 6 | 2 Kanal Ses Desteği | 2 stereo speakers |
| 7 | Sanal Amfi Desteği | Yazılımsal amplifikatör |
| 8 | Gerçek Amfi Desteği (max 8+1) | Fiziksel amplifikatör |
| 9 | DSP Desteği | Sinyal işleme |
| 10 | Equalizer | Frekans ayarı |
| 11 | Volume | Ses seviyesi |
| 12 | Output + Input (Crossover + Reverb + FX) | Çıkış/giriş işleme |

## 11. 14 Device/OS Desteği

| # | Platform | Tip | Maks. Kanal |
|---|----------|-----|------------|
| 1 | Windows ASIO | Sanal Ses Kartı | 8+1 |
| 2 | Windows WASAPI/WDM | Sanal Ses Kartı | 8+1 |
| 3 | Windows WDM | Sanal Ses Kartı | 8+1 |
| 4 | Windows ASIO | Gerçek Ses Kartı | 8+1 |
| 5 | Windows WASAPI/WDM | Gerçek Ses Kartı | 8+1 |
| 6 | Windows WDM | Gerçek Ses Kartı | 8+1 |
| 7 | macOS CoreAudio ASIO | Sanal Ses Kartı | 8+1 |
| 8 | macOS CoreAudio | Sanal Ses Kartı | 8+1 |
| 9 | macOS CoreAudio | Gerçek Ses Kartı | 8+1 |
| 10 | macOS CoreAudio WDM | Gerçek Ses Kartı | 8+1 |
| 11 | Android DSP | Sanal Ses Kartı | 8+1 |
| 12 | Android DSP | Gerçek Ses Kartı | 8+1 |
| 13 | Linux ALSA | Sanal Ses Kartı | 8+1 |
| 14 | Linux ALSA | Gerçek Ses Kartı | 8+1 |

## 12. 7 Amplifikatör Konfigürasyonu

| # | Konfigürasyon | Maks. Güç | Ohm |
|---|--------------|-----------|-----|
| 1 | 8+1 Amplifikatör | 2000W | 8Ω |
| 2 | 5+1 Amplifikatör | 2000W | 8Ω |
| 3 | 2+1 Amplifikatör | 2000W | 8Ω |
| 4 | 2+1 Amplifikatör (Küçük) | 10W | 8Ω |
| 5 | 2+1 Amplifikatör (Orta) | 35W | 8Ω |
| 6 | 2 Kanal Amplifikatör (Küçük) | 10W | 8Ω |
| 7 | 2 Kanal Amplifikatör (Orta) | 35W | 8Ω |

**Varsayılan CoreMusic Amplifikatörü:** 8+1 (max 2000W)

## 13. Maks. Performans Hedefleri

| Metrik | Hedef |
|--------|-------|
| Bit Depth | 32-bit max audio |
| Sample Rate | 48/96/192 kHz+ |
| Latency | 0.1ms max ultra düşük |
| Ses Gücü | 10W–2000W (8+1) |
| Kanal | 2/2+1/5+1/8+1 |

## 14. Yazılım Katmanları

```
Application
    ↓
REST API
    ↓
Middleware
    ↓
Audio Engine (DSP, EQ, Compressor, Limiter, Crossover, FIR/IIR Filter)
    ↓
Driver (ASIO, WASAPI, CoreAudio, ALSA)
    ↓
Firmware (Bootloader, HAL, GPIO, SPI, I2C, UART, USB)
    ↓
Hardware (PCB, DAC/ADC, DSP, Amplifier, Audio Outputs)
```

## 15. Referanslar

| Dosya | Kapsam |
|-------|--------|
| [[electronic/platform-architecture]] | 9 katmanlı platform mimarisi |
| [[electronic/device-architecture]] | 4 ana cihaz ailesi |
| [[electronic/audio-architecture]] | Ses motoru ve DSP pipeline |
| [[electronic/operating-system-architecture]] | 8 OS desteği ve PAL |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi |
| [[electronic/software-architecture]] | Yazılım mimarisi |
| [[electronic/service-architecture]] | Servis mimarisi |
| [[architecture/l6-electronics]] | L6 Electronics katmanı |
| [[electronic/audio-organization]] | 5 bölüm organizasyonu |
| [[electronic/hardware-roadmap]] | 3 fazlı yol haritası |
| [[ADR-061-electronics-architecture]] | Electronics mimarisi ADR |
| [[ADR-062-dsp-pipeline-architecture]] | DSP pipeline ADR |
| [[ADR-063-hardware-design-standards]] | Hardware design standartları ADR |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 15 |
| Device Families | 5 (Professional, Home, Car, Embedded, Dev Board) |
| Architecture Layers | 7 (HW→FW→Driver→Audio Engine→Middleware→API→Application) |
| AI Capabilities | 10 |
| Electronics Subsystems | 12 |
| Device/OS Support | 14 |
| Amplifier Configs | 7 |
| Son Güncelleme | 2026-09-01 |
| Kaynak | coremusic-electronci-vault-design-plan.md |

---

## 17. Katmanlar Arası Sorumluluk Sınırı

`## 4` ve `## 14` iki farklı katman gösterimidir; bu bölüm ikisini tek bir sorumluluk tablosunda birleştirir. Her satır **tek bir sorumlu katman** ve **yasak komşuluk** bildirir.

| Katman | Sahip olduğu Sorumluluk | Doğrudan Erişemeyeceği | Çıktısı |
|--------|--------------------------|------------------------|---------|
| Application | UI akışı, kullanıcı niyeti | Donanım register'ı | Uygulama komutu |
| REST API | Uç nokta sözleşmesi, DTO | İş mantığı kuralları | İstek/yanıt gövdesi |
| Middleware | Auth, logging, caching, rate limiting | DSP parametreleri | İşlenmiş istek |
| Audio Engine | Mixer, EQ, compressor, limiter, crossover, FIR/IIR | Dosya sistemi, ağ | 32-bit float örnek akışı |
| Driver | ASIO, WASAPI, CoreAudio, ALSA uyarlaması | İş mantığı | Tampon + zaman damgası |
| Firmware | Bootloader, HAL, GPIO, SPI, I2C, UART, USB | Uygulama katmanı | Register yazımı, kesme |
| Hardware | PCB, DAC/ADC, DSP çipi, amplifikatör, çıkışlar | Yazılım katmanı | Analog sinyal |

**Sınır kuralı:** Bir katman, bir üstündeki katmanı `import` edemez. İletişim yalnızca aşağı doğru çağrı veya yayılan olay üzerinden olur.

**Geri besleme:** Yalnızca `Hardware → Firmware → Driver` zinciri telemetri (ısı, seviye, hata) taşır; telemetri yukarı katmanlarda **ölçüm** olarak okunur, **karar** olarak kullanılmaz.

---

## 18. AI Yeteneklerinin Veri Bağımlılığı

`## 7` içindeki AI yeteneklerinin hangi veriye ihtiyacı olduğu ve verinin nereden geldiği:

| AI Yeteneği | Girdi Verisi | Kaynak | Çıktı | Durum |
|-------------|--------------|--------|-------|-------|
| Otomatik EQ | Hoparlör ölçümleri, oda yanıt eğrisi | Ölçüm cihazı / mikrofon | EQ eğrisi (dB, frekans) | Tasarım — `⚠️ VERIFICATION REQUIRED` |
| Oda Akustiği | impulse yanıt (IR) | Mikrofon kaydı | Düzeltme filtresi | Tasarım — `⚠️ VERIFICATION REQUIRED` |
| Hoparlör Yerleşimi | Oda geometrisi, konumlar | Kullanıcı girdisi | Yerleşim önerisi | Tasarım — `⚠️ VERIFICATION REQUIRED` |
| Frekans Analizi | Ham PCM | DSP zinciri | Spektral harita | Tasarım — `⚠️ VERIFICATION REQUIRED` |
| Öngörücü Bakım | Sıcaklık, çalışma saati, akım | Telemetri | Risk skoru | Tasarım — `⚠️ VERIFICATION REQUIRED` |
| Performans Optimizasyonu | DSP parametre geçmişi | Profil deposu | Parametre seti | Tasarım — `⚠️ VERIFICATION REQUIRED` |
| Isı Yönetimi | Termal sensör okumaları | Firmware telemetrisi | Soğutma kararı | Tasarım — `⚠️ VERIFICATION REQUIRED` |

**Ortak payda:** Yedi yeteneğin de **eğitim/çıkarım verisi repoda yoktur**; hepsi vault tasarım iddiasıdır. Gerçek model, ağırlık dosyası veya ölçüm seti `UNKNOWN` işaretlenir — tahmin edilerek yazılmaz.

**Repo'da AI'nın mevcut yüzeyi:** `shared/src/AI/` altında bilgi tabanı ve orkestratör sınıfları bulunur; bunlar elektronik çıkarım değil, **ajan yönlendirme** içindir (`## 20`, Kanıt 3 ve 4).

---

## 19. Kanal · Güç · Konfigürasyon Matrisi

`## 12` ve `## 13` sayılarını tek matriste toplar. **Bu tablodaki güç değerleri tasarım hedefidir, ölçülmüş değer değildir.**

| Konfigürasyon | Kanal | Tasarım Güç | Empedans | Kullanım Alanı |
|---------------|-------|-------------|----------|----------------|
| 8+1 | 9 | 2000W | 8Ω | Stüdyo, salon, professionel |
| 5+1 | 6 | 2000W | 8Ω | Ev sinema |
| 2+1 (tam) | 3 | 2000W | 8Ω | Ev, ofis |
| 2+1 (küçük) | 3 | 10W | 8Ω | Masaüstü, monitör |
| 2+1 (orta) | 3 | 35W | 8Ω | Oda hoparlörü |
| 2 kanal (küçük) | 2 | 10W | 8Ω | Stereo masaüstü |
| 2 kanal (orta) | 2 | 35W | 8Ω | Stereo oda |

**Ölçüm durumu:** Güç, verimlilik, THD+N ve sıcaklık değerlerinin **hiçbiri bu depoda ölçülmemiştir** → `⚠️ VERIFICATION REQUIRED`. Ölçüm için gerekenler: güç kaynağı, osiloskop, duyulabilir bozulma ölçüm cihazı, termal kamera.

**Kanal sayımı kuralı:** `8+1` = 8 full-range kanal + 1 subwoofer kanalıdır; toplam 9 çıkış. `5+1` = 6, `2+1` = 3 çıkış. Sayım toplamları `## 11` destek matrisindeki `Maks. Kanal` sütunuyla çelişmemelidir; çelişki tespit edilirse bu dosya değil, `[[electronic/device-ecosystem]]` yeniden okunur.

---

## 20. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden alınmıştır; satır numarası `Select-Object -Index (N-1)` ile doğrulanmıştır.

**Kanıt 1 — Electronics, repo konfigürasyonunda L0 katmanı olarak geçiyor**

`Dosya: shared/src/AI/AIOrchestrator.php` · `Satır: 32`

```php
'embedded' => ['domain' => 'audio-dsp', 'layer' => 'l0-electronics'],
```

**Kanıt 2 — Donanım ajanı önceliği kod içinde sabitlenmiş**

`Dosya: shared/src/AI/AIOrchestrator.php` · `Satır: 44`

```php
'hardware' => ['agent' => 'audio-hw', 'priority' => 'high'],
```

**Kanıt 3 — Bilgi tabanı kategorileri `audio` ve `hardware` içeriyor**

`Dosya: shared/src/AI/KnowledgeBase.php` · `Satır: 25`

```php
private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];
```

**Kanıt 4 — Ses oynatma elemanı sunum katmanında oluşturuluyor**

`Dosya: shared/src/PageRouter/HtmlShellRenderer.php` · `Satır: 192`

```php
echo '<audio id="audio" class="vdisplay" preload="metadata"></audio>';
```

**Yorum:** Kanıt 1–3, "elektronik" kavramının repo içinde yalnız **meta veri / ajand yönlendirme** düzeyinde yaşadığını gösterir. Donanım register erişimi, DAC kontrolü veya amplifikatör koruma döngüsü içeren **herhangi bir C/C++ kaynağı bu depoda yoktur**.

---

## 21. Kapsam Sınırı ve Dürüstlük Beyanı

| İddia Sınıfı | Durum | Dayanak |
|--------------|-------|---------|
| 6 katman, 12 alt sistem, 14 OS, 7 amplifikatör konfigürasyonu | Vault tasarım tablosu | Bu dosya §4–§13 |
| 2000W · 8Ω · 0.1ms gecikme · 192 kHz | **Tasarım hedefi** — ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| PCM3168A · AK4458 · CS4272 · XMOS XU316 seçimi | Vault ADR iddiası | `[[ADR-038-8.1-sound-card-chip-selection]]` · `[[ADR-017-dsp-hardware-mode]]` |
| `PCM5122` | **KATI YASAK** | ADR-038 H001 |
| Repo'da electronics/devre/firmware kodu | **YOK** | Dizin taraması |
| AI yetenekleri (§18) | Tasarım — veri/model yok | `UNKNOWN` |
| JUCE 9 · ASIO SDK 2.3.4 sürüm kanıtı | Yok | `⚠️ VERIFICATION REQUIRED` |
| Kaynak dosya adı `coremusic-electronci-vault-design-plan.md` | İmla **korunur** (dosya adı) | `## 16` satır 273 |

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

---

## 22. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Ölçüm, ölçüm bilimi ve standartlaştırma için ulusal referans kurum | https://www.nist.gov/ | 2026-10-13 | HTTP 200 ✓ |
| İnternet ve açık standartların teknik yayın organı (RFC serisi) | https://www.ietf.org/ | 2026-10-13 | HTTP 200 ✓ |

**Sınır:** Bu kaynaklar genel referans niteliğindedir; elektronik tasarım değerleri (güç, THD, termal) için **vault ve gelecekteki ölçüm** kaynaktır.

---

## 23. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | §13'teki `Latency 0.1ms` hedefi hangi ölçüm koşulunda geçerli? | `⚠️ VERIFICATION REQUIRED` | Buffer 512 / 48 kHz ile gerçek ölçüm |
| 2 | §12'deki 2000W değeri hangi kanal başına / RMS mi, tepe mi? | `UNKNOWN` | Ölçüm şartnamesi yazımı |
| 3 | `## 10` 12 alt sistemi ile `## 11` 14 platformu arasındaki örtüşme | `⚠️ VERIFICATION REQUIRED` | Çapraz eşleme tablosu |
| 4 | AI yetenekleri (§18) için eğitim verisi var mı? | `UNKNOWN` | `.ai/` içinde veri seti araması |
| 5 | `CS4272` DAC seçimi hangi ADR'de kararlaştırıldı? | `⚠️ VERIFICATION REQUIRED` | `[[ADR-038-8.1-sound-card-chip-selection]]` okuması |
| 6 | `[[electronic/audio-organization]]` ve `[[electronic/hardware-roadmap]]` diskte var mı? | `⚠️ VERIFICATION REQUIRED` | Dizin doğrulaması — yoksa link kırık sayılır |

---

## 24. İlişkili Dosyalar (Wikilink Envanteri)

| Rol | Wikilink |
|-----|----------|
| Üst katman | `[[architecture/l6-electronics]]` |
| Kök navigasyon | `[[electronic/index]]` |
| Platform | `[[electronic/platform-architecture]]` |
| Cihaz | `[[electronic/device-architecture]]` · `[[electronic/device-ecosystem]]` |
| Yazılım | `[[electronic/software-architecture]]` |
| Servis | `[[electronic/service-architecture]]` |
| Ses | `[[electronic/audio-architecture]]` · `[[electronic/dsp/index]]` |
| İşletim sistemi | `[[electronic/operating-system-architecture]]` |
| Amplifikatör | `[[electronic/amplifier-design]]` |
| Alt sistemler | `[[firmware/index]]` · `[[drivers/index]]` · `[[hardware/index]]` · `[[dsp/index]]` |
| Organizasyon | `[[electronic/audio-organization]]` · `[[electronic/hardware-roadmap]]` |
| Prensipler | `[[brain.md]]` §3 · `[[brain.md]]` §7.1 |
| ADR'ler | `[[ADR-017-dsp-hardware-mode]]` · `[[ADR-038-8.1-sound-card-chip-selection]]` · `[[ADR-061-electronics-architecture]]` · `[[ADR-062-dsp-pipeline-architecture]]` · `[[ADR-063-hardware-design-standards]]` |

---

## 25. Tasarım → Ölçüm Yol Haritası

Bu dosyadaki her sayısal iddia, ilgili ölçüm tamamlanana kadar **tasarım hedefi** olarak kalır. Aşağıdaki tablo hangi iddianın hangi yöntemle ölçüleceğini ve ölçüm tamamlanmadan önce hangi etiketi taşıdığını tanımlar.

| # | İddia | Ölçüm Yöntemi | Gereken Ekipman | Kabul Kriteri | Şu Anki Etiket |
|---|-------|---------------|-----------------|---------------|----------------|
| 1 | Latency 0.1ms | ASIO callback süresi ölçümü | Gerçek ASIO aygıtı, ölçüm yazılımı | Hedef ≤0.1ms | `⚠️ VERIFICATION REQUIRED` |
| 2 | Sample rate 48/96/192 kHz | Aygıt modu doğrulaması | ASIO/WASAPI aygıtı | Mod geçerli + kilitsiz | `⚠️ VERIFICATION REQUIRED` |
| 3 | Bit depth 32-bit | Akış biçimi kaydı | Aygıt panosu / log | float32 doğrulandı | `⚠️ VERIFICATION REQUIRED` |
| 4 | 8+1 = 9 kanal | Kanal haritası okuması | Sürücü panosu | 9 çıkış tanımlı | `⚠️ VERIFICATION REQUIRED` |
| 5 | 2000W güç | Kalıcı güç ölçümü | Güç kaynağı, yük, osiloskop | Belirtilen RMS'te tutarlı | `⚠️ VERIFICATION REQUIRED` |
| 6 | 8Ω empedans | Empedans analizi | Empedans analizör | ±%10 tolerans | `⚠️ VERIFICATION REQUIRED` |
| 7 | THD+N <0.01% | Bozulma ölçümü | Analog analizör, 1kHz test tonu | 1kHz'de eşik altında | `⚠️ VERIFICATION REQUIRED` |
| 8 | SNR >100dB | Gürültü tabanı ölçümü | Ses analizör | Eşik üstü | `⚠️ VERIFICATION REQUIRED` |
| 9 | Sıcaklık yönetimi | Termal harita | Termal kamera | Tasarım sınırı altında | `⚠️ VERIFICATION REQUIRED` |
| 10 | DC offset >0.5V koruma | Eşik testi | Osiloskop, sinyal kaynağı | Röle tetikleniyor | `⚠️ VERIFICATION REQUIRED` |
| 11 | Zero-allocation (ses iş parçacığı) | Tahsis sayacı | Profiler | Callback sırasında 0 tahsis | Kural bağlayıcı — ölçülmedi |
| 12 | Lock-free (ses iş parçacığı) | Kilit sayacı | Profiler / kod denetimi | 0 kilit | Kural bağlayıcı — denetlenmedi |
| 13 | AI otomatik EQ başarımı | Referans ölçüm karşılaştırması | Ölçüm mikrofonu, oda | Hedef eğri sapması | `UNKNOWN` (model yok) |
| 14 | Öngörücü bakım doğruluğu | Geriye dönük doğrulama | Arıza geçmişi verisi | Doğruluk oranı | `UNKNOWN` (veri yok) |
| 15 | 16 fazlı cihaz yaşam döngüsü | Süreç denetimi | Proje kayıtları | Faz geçiş kanıtı | `⚠️ VERIFICATION REQUIRED` |

**Kural:** Tabloda etiketi `UNKNOWN` olan satırlar için veri veya model **depoda bulunmadığından** ölçüm planlanamaz; bu satırlar uydurma değerle kapatılamaz. Etiketi `⚠️ VERIFICATION REQUIRED` olan satırlar ise **ölene kadar** raporlarda hedef olarak, gerçekleşmiş sonuç olarak sunulmaz.

**Bağlantı:** Bu yol haritası `[[electronic/development-workflow]]` içindeki faz tanımlarıyla ve L0 ölçüm şartnamesiyle birlikte okunur; ölçüm sonuçları üretildiğinde bu dosyanın `## 16` Quality Report satırları güncellenir.

---

## 26. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| L6 Electronics | Vault'taki elektronik mimari katmanının adı | `[[architecture/l6-electronics]]` |
| Ortak mühendislik platformu | Farklı cihaz ailesinin paylaştığı altyapı | `## 6` |
| 6 katmanlı mimari | Application → Middleware → Audio Engine → Driver → Firmware → Hardware | `## 4` |
| Alt sistem | 12 elektronik işlevden biri (sanal ses kartı, amfi, EQ…) | `## 10` |
| Platform desteği | İşletim sistemi + ses API kombinasyonu | `## 11` |
| Sanal ses kartı | Yazılımla üretilen ses aygıtı | `## 10` (1) |
| Gerçek ses kartı | Fiziksel donanım aygıtı | `## 10` (2) |
| Kanal düzeni | `n+1` → n full-range + 1 subwoofer | `## 19` |
| Crossover | Frekansları alt/üst bantlara ayıran filtre ailesi | `## 14` |
| FIR / IIR | Sonlu ve sonsuz dürtü tepkili filtre türleri | `## 14` |
| HAL | Donanım soyutlama katmanı — firmware ile donanım arasında | `## 14` |
| PCM3168A | ADR-038 gereği tek geçerli DAC çipi | `[[ADR-038-8.1-sound-card-chip-selection]]` |
| XMOS XU316 | DSP işlemcisi seçimi | `[[ADR-017-dsp-hardware-mode]]` |
| Zero-allocation | Ses iş parçacığında tahsis yasağı | `## 9` |
| Lock-Free | Ses iş parçacığında kilit yasağı | `## 9` |
| Ölçüm etiketi | `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` / bağlayıcı kural | `## 25` |
| Tasarım hedefi | Ölçülmemiş, hedef olarak taşıyan sayısal iddia | `## 25` |
| Telemetri | Donanımdan yukarı katmanlara giden ölçüm akışı | `## 17` |
| Çapraz referans | Bu dosya ile başka bir vault dosyası arasındaki bağ | `## 15` |
| Wikilink | `[[hedef]]` biçimindeki vault içi bağlantı | `## 24` |
| Envanter kesişimi | Bu dosyadaki dış kaynağın vault URL listesiyle örtüşmesi | `## 22` |
| Yasak yüzey | Bu revizyonda yazılamayan dosya/dizin kümesi | `## 21` |
| Authority bloğu | Dosyanın sonundaki sorumluluk ve mod satırları | Kapanış |

**Okuma notu:** Bu sözlük yalnız bu dosyadaki terimleri kapsar; genel proje terimleri için vault kökündeki terim dosyası kullanılır. Tersine çevirme (terim → bölüm) aynı tablodan yapılır; ikinci bir sözlük tutulmaz, çünkü bilgi iki yerde yaşarsa biri güncellenmez.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-01
**Mode:** Red Team · Human Mode · Truth Mode
