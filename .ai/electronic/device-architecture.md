---
type: system
category: electronics-device
title: "CoreMusic Electronics Device Architecture"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Device Architecture

**Zorunlu Bağlantılar:** [[electronic/platform-architecture]] · [[electronic/software-architecture]] · [[electronic/device-ecosystem]] · [[electronic/operating-system-architecture]] · [[electronic/audio-architecture]] · [[ADR-063-hardware-design-standards]]

---

## 1. Amaç

CoreMusic ELECTRONICS platformu, **birden fazla cihaz ailesini** desteklemek için tasarlanmıştır. Her cihaz ailesi ortak mühendislik altyapısını paylaşır: aynı mimari yapı, aynı yazılım standartları, aynı iletişim protokolleri, aynı AI altyapısı ve aynı geliştirme süreci.

---

## 2. Cihaz Ailesi Mimarisi

```
┌──────────────┐
│ Professional │──▶ Same Architecture
│    Audio     │──▶ Same Software Standards
├──────────────┤──▶ Same Communication Protocols
│   Home Audio │──▶ Same AI Infrastructure
├──────────────┤──▶ Same Development Process
│   Car Audio  │
├──────────────┤
│  Embedded    │
│    Audio     │
└──────────────┘
```

### 2.1 Cihaz Aileleri

| Aile | Cihazlar | Öncelik |
|------|----------|---------|
| Professional Audio | Studio Interface, Rack DSP, Mixer, Monitor Controller | Yüksek |
| Home Audio | Smart Amplifier, Receiver, Multi-Room, Streaming Player | Yüksek |
| Car Audio | DSP Amplifier, Head Unit, Digital Crossover | Yüksek |
| Embedded Audio | RPi HAT, ARM Board, Embedded Linux Device | Orta |

---

## 3. Donanım Mimarisi

### 3.1 Güç → CPU → RAM → Flash → Audio Codec → DAC/ADC → DSP → Amplifier → Output

```
Power Supply ──▶ CPU/MCU ──▶ RAM
                       │       └──▶ Flash Storage
                       ├──▶ Audio Codec ──▶ DAC
                       │                 └──▶ ADC
                       ├──▶ DSP ──▶ Amplifier ──▶ Output
```

### 3.2 Güç Yönetimi

| Güç Kaynağı | Voltaj | Kullanım |
|-------------|--------|----------|
| USB-C PD | 5V/9V/12V/15V/20V | Taşınabilir cihazlar |
| PoE | 48V | Ağ cihazları |
| DC Jack | 12V–24V DC | Sabit cihazlar (Boost converter ile ±42V) |
| Batarya | 3.7V-12V | Kablosuz cihazlar |
| Automotive | 12V/24V | Araç içi |

### 3.3 İşlemci Katmanı

| İşlemci | Mimarisi | Kullanım |
|---------|----------|----------|
| STM32H7 | ARM Cortex-M7 | DSP amplifikatör |
| ESP32-S3 | Xtensa LX7 | Kablosuz cihazlar |
| NXP i.MX8 | ARM Cortex-A53 | Android head unit |
| XMOS XU316 | xCORE-200 | USB audio interface |
| Raspberry Pi 4/5 | ARM Cortex-A72/A76 | Gömülü Linux |
| Intel NUC | x86_64 | Desktop/studio |
| Xilinx Zynq | ARM+FPGA | Yüksek performans |
| SHARC ADSP-21489 | SHARC | Profesyonel DSP |

### 3.4 Bellek Yapısı

| Bellek Türü | Kapasite | Kullanım |
|-------------|----------|----------|
| SRAM | 256KB-1MB | Hızlı erişim, buffer |
| DDR4/DDR5 | 4GB-64GB | Ana bellek |
| LPDDR4/5 | 1GB-8GB | Düşük güç |
| SPI Flash | 4MB-128MB | Firmware, config |
| EEPROM | 2KB-64KB | Kalıcı ayarlar |
| eMMC | 8GB-128GB | Depolama |
| NVMe SSD | 256GB-4TB | Yüksek hız depolama |

---

## 4. Haberleşme Protokolleri

### 4.1 Yerel İletişim

| Protokol | Hız | Kullanım |
|----------|-----|----------|
| USB 2.0 | 480 Mbps | Audio interface |
| USB 3.0 | 5 Gbps | Yüksek hız |
| UART | 115200 baud | Debug, seri |
| SPI | 10 MHz | Yüksek hız |
| I2C | 400 kHz | Düşük hız |
| I2S | 3.072 MHz | Ses verisi |
| TDM | 8.192 MHz | Çoklu kanal ses |
| CAN Bus | 1 Mbps | Araç içi |

### 4.2 Ağ İletişimi

| Protokol | Hız | Kullanım |
|----------|-----|----------|
| Ethernet | 100M/1G/10G | Ağ bağlantısı |
| Wi-Fi 6 | 2.4/5/6 GHz | Kablosuz |
| Bluetooth 5.3 | 1-3 Mbps | Kablosuz ses |

### 4.3 I2S/TDM İletişim Diyagramı

```
PC / Raspberry Pi ──USB 2.0 (480 Mbit/s)──▶ XMOS XU316
                                                │
                                    ┌───────────┴───────────┐
                                    │                       │
                              USB RX (Isochronous)    USB TX (Isochronous)
                                    │                       │
                                    └───────────┬───────────┘
                                                │
                                           DSP Engine
                                                │
                              ┌─────────────────┼─────────────────┐
                              │                 │                 │
                    I2S/TDM Bus          I2S/TDM Bus        I2S/TDM Bus
                    MCLK 24.576MHz      BCLK 3.072MHz      LRCLK 48kHz
                    SDOUT Ch 1-8        SDIN Ch 1-8
                              │                 │
                              └────────┬────────┘
                                       │
                              PCM3168A Codec
                              DAC (8 Kanal) + ADC (6 Kanal)
                                       │
                              Balanced XLR/RCA
                                       │
                              Class AB Amplifier
                                       │
                              Speakers (7.1 Surround)
```

### 4.4 USB Audio Sinyal Yolu Diyagramı

```
USB Host ──Isochronous 48kHz/24bit──▶ USB Audio Class 2.0 ──▶ Stream Decode
                                                                  │
                                                        DSP Processing
                                                        Volume ──▶ Mute ──▶ EQ 31-Band
                                                                  │
                                                          Output Path
                                                          Channel Router
                                                                  │
                                                      PCM3168A DAC × 8
                                                                  │
                                                      Class AB Amplifier
                                                                  │
                                                          Speakers
```

---

## 5. Ses Çıkışları

| Çıkış | Kullanım | Seviye |
|-------|----------|--------|
| RCA | Consumer | 2Vrms |
| TRS (6.35mm) | Prosumer | +4dBu |
| XLR | Profesyonel | +4dBu (dengeli) |
| Optical (TOSLINK) | Dijital | — |
| SPDIF (Coaxial) | Dijital | — |
| AES-EBU | Profesyonel dijital | 110Ω |
| HDMI ARC/eARC | TV entegrasyonu | — |
| Speaker Terminals | Amplifikatör çıkışı | — |

---

## 6. Ses Donanımı

| Bileşen | Model | Özellik | ADR |
|---------|-------|---------|-----|
| DAC | PCM3168A | 6-in/8-out, 24-bit | [[ADR-038-8.1-sound-card-chip-selection]] |
| DAC | AK4458 | 8-kanal, 32-bit, 768kHz | [[electronic/audio-interface-design]] |
| ADC | PCM3168A | 6-in, 24-bit, 96kHz | [[ADR-038-8.1-sound-card-chip-selection]] |
| DSP | XMOS XU316 | USB Audio Class 2.0 | [[ADR-017-dsp-hardware-mode]] |
| Codec | CS4272 | 24-bit, 192kHz | — |

**⚠️ PCM5122 REDDEDİLMİŞTİR (H001):** Sadece 2 kanal, 7.1 surround için yetersiz.

---

## 7. Yazılım Mimarisi

```
User Interface (Web, Mobile, Desktop)
        │
        ▼
REST API (JSON, WebSocket)
        │
        ▼
Device Manager (HW Detection, Driver Load)
        │
        ▼
DSP Engine (EQ, Compressor, Limiter)
        │
        ▼
Driver Layer (ASIO, WASAPI, ALSA)
        │
        ▼
Firmware (RTOS, Boot)
        │
        ▼
Hardware (PCB, Components)
```

### 7.1 Device Manager Sorumlulukları

| Sorumluluk | Açıklama |
|------------|----------|
| Hardware Detection | Donanım otomatik algılama |
| Driver Loading | Sürücü otomatik yükleme |
| Firmware Control | Firmware yönetimi |
| Update Management | Güncelleme yönetimi |
| Health Check | Sağlık kontrolü |
| Error Reporting | Hata raporlama |
| AI Analysis | AI analizi |

---

## 8. AI Device Layer

| AI Yeteneği | Açıklama | Kullanım |
|-------------|----------|----------|
| Donanım Analizi | Donanım durumu ve performans analizi | Tüm cihazlar |
| Performans İzleme | CPU, bellek, disk kullanımı | Tüm cihazlar |
| Isı Yönetimi | Termal analiz ve soğutma optimizasyonu | Tüm cihazlar |
| Arıza Tahmini | Donanım arıza olasılığı tahmini | Tüm cihazlar |
| Otomatik Konfigürasyon | Donanım parametre otomatik ayarı | Tüm cihazlar |

---

## 9. Ortak Standartlar

### 9.1 Yazılım Standartları

| Standart | Açıklama |
|----------|----------|
| SOLID | Tek sorumluluk, açık-kapalı, yerine koyma |
| Clean Architecture | Katmanlı yapı |
| Hexagonal Architecture | Adapter/Port pattern |
| DDD | Domain-Driven Design |
| CQRS | Command Query Segregation |
| Event Driven | Asenkron olay tabanlı |

Detaylar: [[brain.md]] §3, [[electronic/software-architecture]]

### 9.2 Donanım Standartları

| Standart | Açıklama |
|----------|----------|
| EMI/EMC | Elektromanyetik uyumluluk |
| CE | Avrupa uyumluluk |
| RoHS | Zararlı madde kısıtlaması |
| Modular PCB | Modüler kart tasarımı |
| IPC Standards | PCB üretim standartları |

Detaylar: [[ADR-063-hardware-design-standards]], [[electronic/test-protocols]]

### 9.3 Güvenlik Standartları

| Standart | Açıklama |
|----------|----------|
| Secure Boot | Güvenli başlatma |
| Signed Firmware | İmzalı firmware |
| Secure Update | Güvenli güncelleme |
| Encrypted Communication | Şifreli iletişim |
| Device Authentication | Cihaz kimlik doğrulama |

Detaylar: [[architecture/l1-security]], [[ADR-022-database-hardened-security]]

---

## 10. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/platform-architecture]] | 9 katmanlı platform mimarisi |
| [[electronic/audio-architecture]] | Ses mimarisi ve DSP pipeline |
| [[electronic/software-architecture]] | Yazılım mimarisi |
| [[electronic/operating-system-architecture]] | OS mimarisi ve PAL |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi |
| [[electronic/service-architecture]] | Servis mimarisi |
| [[electronic/hardware-roadmap]] | Hardware yol haritası |
| [[ADR-038-8.1-sound-card-chip-selection]] | Ses kartı seçimi |
| [[ADR-063-hardware-design-standards]] | Hardware standartları |

---

## 11. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Device Families | 4 (Professional, Home, Car, Embedded) |
| Hardware Components | 30+ |
| Communication Protocols | 12 |
| Audio Outputs | 8 |
| AI Capabilities | 5 |
| Cross References | 9 |

---

## 12. Sinyal Yolu Aşama Aşama

`§4.3` ve `§4.4` diyagramlarının metinsel karşılığı; her aşama ne üretir, ne tüketir.

| # | Aşama | Giriş | Çıkış | Sorumlu Bileşen | Kayıp/Gecikme Türü |
|---|-------|-------|-------|-----------------|--------------------|
| 1 | Kaynak | Ağ / dosya | PCM akış | Host | Paket kaybı |
| 2 | USB taşıma | PCM paketleri | İzokron çerçeve | USB Audio Class 2.0 | Underrun |
| 3 | Çözme / yönlendirme | Çerçeve | Kanal başına akış | DSP Engine | Yeniden senkron |
| 4 | Hacim + susturma | Akış | Akış (kazanç uygulanmış) | DSP Engine | Kuantalama |
| 5 | EQ 31-bant | Akış | Akış | DSP Engine | Faz kayması |
| 6 | Kompresör / limiter | Akış | Akış | DSP Engine | Gecikme (grup) |
| 7 | Crossover | Akış | Alt/üst bantlar | DSP Engine | Faz kayması |
| 8 | Kanal yönlendirici | Bantlar | Kanal haritası | DSP Engine | — |
| 9 | DAC dönüşümü | Dijital akış | Analog çıkış | PCM3168A | SNR / THD |
| 10 | Amplifikasyon | Analog seviye | Güç çıkışı | Class AB | Bozulma / ısı |
| 11 | Çıkış bağlayıcı | Güç | Hoparlör | XLR / RCA / terminal | Empedans kaybı |

**Zorunlu kayıt:** Her aşamanın **grup gecikmesi** ayrı ölçülür; toplam, aşamaların toplamıdır — tek başına "uçtan uca" ölçümü aşamaları ayırt etmez.

**Bu depoda karşılığı olan aşamalar:** Yalnız 1. aşama (uygulama tarafı). 2–11. aşamaların tamamı donanım/firmware alanıdır ve repo'da karşılığı **YOK**.

---

## 13. Saat ve Frekans Hesapları (Tutarlılık Denetimi)

Dosyanın `§4.1` ve `§4.3`ündeki frekans değerleri, 48 kHz örnekleme hızı varsayımıyla **aritmetik olarak** karşılaştırılmıştır. Bu denetim sabit değer üretmez; yalnız dosyadaki değerlerin birbiriyle uyumunu sınar.

| Büyükleme | Hesap | Bulunan Değer | Dosyadaki Değer | Sonuç |
|-----------|-------|---------------|-----------------|-------|
| MCLK | 48000 × 512 = 24.576.000 Hz | 24.576 MHz | 24.576 MHz (`§4.3`) | ✅ Tutarlı |
| BCLK (I2S) | 48000 × 64 = 3.072.000 Hz | 3.072 MHz | 3.072 MHz (`§4.1`, `§4.3`) | ✅ Tutarlı |
| LRCLK | doğrudan = 48.000 Hz | 48 kHz | 48 kHz (`§4.3`) | ✅ Tutarlı |
| TDM bit clock | 8.192.000 ÷ 48.000 = 170,666… | tam bölen **yok** | 8.192 MHz (`§4.1`) | ⚠️ Tutarsız — `⚠️ VERIFICATION REQUIRED` |
| 8 kanal / tek hat (256 bit) | 48000 × 256 = 12.288.000 Hz | 12.288 MHz | dosyada **yok** | `UNKNOWN` |
| 512 örnek tampon süresi | 512 ÷ 48000 = 0,010666… sn | 10.667 ms | şartname hedefi | Hesaplanan değer |

**Tutarlılık uyarısı:** `§4.1`'deki `TDM 8.192 MHz` değeri 48 kHz ile tam bölünmediğinden ya farklı bir örnekleme hızına aittir ya da yazım hatasıdır. **Düzeltme YAPILMADI** (silme/değiştirme yetkisi kapsam dışı) → `## 18` risk listesine kaydedildi.

**Kural:** Bu tabloya yeni satır eklenirken hesap **yeniden yazılır**, sonuç kopyalanmaz; kopyalanan sonuç hata taşır.

---

## 14. Güç, Besleme ve Termal Kısıtlar

`§3.2`'deki besleme kaynakları ile `§3.1`'deki sinyal zinciri arasındaki kısıtlar.

| Kısıt | İfade | Dayanak | Durum |
|-------|-------|---------|-------|
| Amplifikatör besleme | DC Jack 12V–24V → boost ile ±42V | `§3.2` | Tasarım — ölçülmedi |
| Taşınabilir besleme | USB-C PD 5V/9V/12V/15V/20V | `§3.2` | USB PD kapsamı |
| Ağ besleme | PoE 48V | `§3.2` | IEEE PoE sınıfı |
| Otomotiv besleme | 12V / 24V | `§3.2` | Araç içi |
| Batarya | 3.7V–12V | `§3.2` | Tasarım |
| Gürültü | multi-rail, low-noise, EMI filtreleme | `[[ADR-063-hardware-design-standards]]` | `⚠️ VERIFICATION REQUIRED` |
| Termal | heatsink, fan, thermal pad | `[[ADR-063-hardware-design-standards]]` | `⚠️ VERIFICATION REQUIRED` |
| Kasa sıcaklık aralığı | **Bu dosyada verilmemiş** | — | `UNKNOWN` |

**Yasak:** Termal sınır, verimlilik yüzdesi, akım değeri veya sıcaklık artışı gibi sayısal değerler **bu dosyada yoktur ve tahmin edilerek yazılmaz**; üretilirse ölçümden gelmek zorundadır.

**Koruma bağlantısı:** DC offset eşiği (0.5V üzeri → koruma rölesi) L0 kuralıdır; bu dosyada DC offset ile ilgili bir tanım yoktur → ilgili yüzey `[[electronic/amplifier-architecture]]` ve `[[dsp/index]]` kapsamındadır.

---

## 15. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden alınmıştır; satır numarası `Select-Object -Index (N-1)` ile doğrulanmıştır.

**Kanıt 1 — Donanım ajanı önceliği repo konfigürasyonunda**

`Dosya: shared/src/AI/AIOrchestrator.php` · `Satır: 44`

```php
'hardware' => ['agent' => 'audio-hw', 'priority' => 'high'],
```

**Kanıt 2 — Elektronik alan adı repo katman etiketinde geçiyor**

`Dosya: shared/src/AI/AIOrchestrator.php` · `Satır: 32`

```php
'embedded' => ['domain' => 'audio-dsp', 'layer' => 'l0-electronics'],
```

**Kanıt 3 — Oynatma durumu tarayıcı tarafında tutuluyor (cihaz durumu değil)**

`Dosya: assets.coremusic.net/js/coreplayer/coreplayer.controls.js` · `Satır: 7`

```javascript
const PlaybackRepository = {
```

**Yorum:** Kanıt 1–2, donanım/elektronik kavramının repo'da **meta veri** olarak yaşadığını gösterir. `§3.3`'teki işlemciler, `§4`'teki protokoller ve `§6`'daki codec seçimi için repo'da **kod karşılığı yoktur** — bu satırlar yalnız vault iddiasıdır.

---

## 16. Kapsam Sınırı ve Dürüstlük Beyanı

| İddia | Durum | Dayanak |
|-------|-------|---------|
| 4 cihaz ailesi | Vault tasarım listesi | `§2.1` |
| 12 haberleşme protokolü | Vault tasarım listesi | `§4` |
| Frekans değerleri (MCLK/BCLK/TDM) | `§13` denetiminde 3/4 tutarlı, 1'i değil | `⚠️ VERIFICATION REQUIRED` |
| ±42V boost, 2000W sınıfı güç | Tasarım — ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| Çıkış seviyeleri (2Vrms, +4dBu) | Tasarım — ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| CE / RoHS / IPC uygunluğu | Gereksinim — sertifika belgesi yok | `⚠️ VERIFICATION REQUIRED` |
| PCM3168A · AK4458 · XMOS XU316 · CS4272 | Vault ADR seçimi | `§6` |
| **PCM5122** | **KATI YASAK — reddedildi (H001)** | `§6` + ADR-038 |
| Repo'da donanım/firmware kaynak kodu | **YOK** | Dizin taraması |
| `§8` AI yetenekleri (arıza tahmini vb.) | Model/veri yok | `UNKNOWN` |

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

---

## 17. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Bluetooth spesifikasyonlarının resmi yayını | https://www.bluetooth.com/specifications/ | 2026-10-13 | HTTP 200 ✓ |
| İnternet medya tipi atama sicili (IANA) | https://www.iana.org/assignments/media-types/media-types.xml | 2026-10-13 | HTTP 200 ✓ |

**Sınır:** Bu kaynaklar `§4.2`'deki Bluetooth ve içerik/medya tipi iddialarının **genel çerçevesini** destekler; cihazın bu sürümleri kullandığı iddiasını desteklemez.

---

## 18. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | `§4.1` TDM 8.192 MHz değeri hangi örnekleme hızına ait? | `⚠️ VERIFICATION REQUIRED` (`§13`) | Tasarım kaydı okuması |
| 2 | `§5` çıkış seviyeleri (2Vrms, +4dBu) ölçüldü mü? | `⚠️ VERIFICATION REQUIRED` | Ses analizör ölçümü |
| 3 | `§3.4` bellek kapasite aralıkları hangi cihaz için? | `UNKNOWN` | Cihaz başına bellek matrisi |
| 4 | `[[electronic/audio-interface-design]]` dosyası diskte var mı? | `⚠️ VERIFICATION REQUIRED` | Dizin doğrulaması |
| 5 | `§8` AI yetenekleri için ölçüm verisi var mı? | `UNKNOWN` | Telemetri veri seti araması |
| 6 | Bluetooth sürümü (5.3) hangi cihaz ailesinde zorunlu? | `⚠️ VERIFICATION REQUIRED` | Gereksinim matrisi |
| 7 | `§3.2` ±42V boost hangi amplifikatör sınıfı için? | `⚠️ VERIFICATION REQUIRED` | `[[electronic/amplifier-architecture]]` okuması |

---

## 19. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| Cihaz ailesi | Ortak mimariyi paylaşan ürün grubu | `§2.1` |
| Codec | Sesin dijital↔analog dönüşümünü yapan çip | `§6` |
| MCLK | Ana saat — codec'i besleyen yüksek frekanslı saat | `§4.3`, `§13` |
| BCLK | Bit saatı — her örnek bitinin taşındığı saat | `§4.1`, `§13` |
| LRCLK | Sol/sağ (kanal) çerçeve saatı = örnekleme hızı | `§4.3` |
| TDM | Zaman bölmeli çoklu kanal taşıma | `§4.1` |
| İzokron | Zamanı garanti altına alan USB aktarım türü | `§4.3` |
| Boost converter | Girdi gerilimini yükselten dönüştürücü | `§3.2` |
| Dengeli çıkış | XLR ile taşınan diferansiyel sinyal | `§5` |
| +4dBu | Profesyonel seviye referansı | `§5` |
| 2Vrms | Tüketici seviyesi referansı | `§5` |
| Crossover | Frekansları bantlara ayıran filtre ailesi | `§4.4` |
| Grup gecikmesi | Bileşenin sinyali geciktirme süresi | `§12` |
| Toplam gecikme | Aşama gecikmelerinin toplamı | `§12` |
| Tutarsızlık | İki hesabın birbiriyle örtüşmemesi | `§13` |

---

## 20. Doğrulama Kontrol Listesi (Bu Dosya İçin)

Bu dosyadaki her iddia sınıfı için kapanış ölçütü. Satır, kanıt eklenmeden kapatılamaz.

| # | İddia Sınıfı | Kapanış Kanıtı | Mevcut Durum |
|---|--------------|----------------|--------------|
| 1 | Saat/frekans değerleri | `§13` aritmetik denetimi | 3/4 tutarlı · 1'i `⚠️` |
| 2 | Çıkış seviyeleri | Ses analizör ölçüm kaydı | `⚠️ VERIFICATION REQUIRED` |
| 3 | Güç/termal değerler | Termal kamera + güç kaynağı kaydı | `⚠️ VERIFICATION REQUIRED` |
| 4 | Protokol sürümleri | Cihaz pano görüntüsü | `⚠️ VERIFICATION REQUIRED` |
| 5 | Uyumluluk (CE/RoHS/IPC) | Sertifika belgesi | `UNKNOWN` |
| 6 | AI yetenekleri | Model + ölçüm veri seti | `UNKNOWN` |
| 7 | Repo karşılığı | Dosya + satır kanıtı | 3 kanıt eklendi (`§15`) |
| 8 | Vault dosyası varlığı | Dizin okuması | `§18` satır 4 — bekliyor |

**Kapanış kuralı:** Yukarıdaki satırlardan `UNKNOWN` durumda olanlar, sayısal değer üretilmeden "tamamlandı" olarak işaretlenemez. Kanıt geldiğinde yalnız ilgili satır güncellenir; diğer satırlara dokunulmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
