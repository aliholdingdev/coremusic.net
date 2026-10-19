---
type: system
category: electronics-ecosystem
title: "CoreMusic Electronics Device Ecosystem"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Device Ecosystem

**Zorunlu Bağlantılar:** [[electronic/device-architecture]] · [[electronic/platform-architecture]] · [[electronic/software-architecture]] · [[electronic/service-architecture]] · [[ADR-061-electronics-architecture]]

---

## 1. Amaç

CoreMusic ELECTRONICS tek bir cihaz değil, **bir ekosistemdir**. Cihazlar ortak standartlar üzerinden birbirleriyle iletişim kurar, senkronize çalışır ve merkezi olarak yönetilir. CoreMusic Cloud → API → Home/Car/Studio Audio → Embedded/Mobile → Web Management zinciri üzerinden koordine edilir.

---

## 2. Ekosistem Mimarisi

```
┌─────────────────────────────────────────────────────────┐
│  CoreMusic Cloud                                       │
│  Remote Management, OTA, Analytics, AI                  │
├─────────────────────────────────────────────────────────┤
│  API Gateway                                           │
│  Auth, Rate Limit, Routing, Service Discovery          │
├──────────────┬──────────────┬───────────────────────────┤
│  Home Audio  │  Car Audio   │  Professional Studio     │
│  7 cihaz     │  5 cihaz     │  6 cihaz                 │
├──────────────┴──────────────┴───────────────────────────┤
│  Embedded / Mobile                                     │
│  RPi, ARM Board, Phone, Tablet                        │
├─────────────────────────────────────────────────────────┤
│  Web Management                                        │
│  Admin Dashboard, Config, Monitoring                  │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Cihaz Aileleri (4 Aile, 22 Cihaz)

### 3.1 Ev Sesi (Home Audio) — 7 Cihaz

| # | Cihaz | Açıklama | İletişim | Öncelik |
|---|-------|----------|----------|---------|
| 1 | Smart Amplifier | Akıllı amplifikatör | Ethernet, Wi-Fi, USB | Yüksek |
| 2 | Smart Receiver | Akıllı alıcı | Ethernet, Wi-Fi, HDMI ARC | Yüksek |
| 3 | Network Audio Player | Ağ ses oynatıcısı | Ethernet, Wi-Fi | Yüksek |
| 4 | Multi Room Controller | Çoklu oda kontrolcüsü | Ethernet, Wi-Fi, BLE | Yüksek |
| 5 | Streaming Player | Streaming oynatıcı | Wi-Fi, Bluetooth | Orta |
| 6 | Wireless Speaker | Kablosuz hoparlör | Wi-Fi, Bluetooth | Orta |
| 7 | Subwoofer Controller | Subwoofer kontrolcüsü | Wi-Fi, I2S | Düşük |

### 3.2 Araç Sesi (Car Audio) — 5 Cihaz

| # | Cihaz | Açıklama | İletişim | Öncelik |
|---|-------|----------|----------|---------|
| 1 | DSP Amplifier | DSP amplifikatör | CAN Bus, USB, I2S | Yüksek |
| 2 | Android Multimedia Unit | Android bilgi-eğlence | USB, HDMI, Wi-Fi | Yüksek |
| 3 | OEM Audio Processor | OEM ses işlemcisi | CAN Bus, I2S | Orta |
| 4 | Navigation Audio Controller | Navigasyon ses kontrolcüsü | CAN Bus | Orta |
| 5 | CAN Bus Audio Controller | CAN Bus ses kontrolcüsü | CAN Bus | Yüksek |

### 3.3 Profesyonel Ses (Professional Audio) — 6 Cihaz

| # | Cihaz | Açıklama | İletişim | Öncelik |
|---|-------|----------|----------|---------|
| 1 | Studio Audio Interface | Stüdyo ses arayüzü | USB, Thunderbolt | Yüksek |
| 2 | Rack DSP Processor | Raf tipi DSP | Ethernet, USB | Yüksek |
| 3 | Digital Mixer | Dijital mikser | Ethernet, USB, Dante | Yüksek |
| 4 | Audio Matrix | Ses matrisi | AES-EBU, Dante | Orta |
| 5 | Monitor Controller | Monitör kontrolcüsü | USB, AES-EBU | Orta |
| 6 | Recording Interface | Kayıt arayüzü | USB, Thunderbolt | Yüksek |

### 3.4 Gömülü Ses (Embedded Audio) — 4 Cihaz

| # | Cihaz | Açıklama | İletişim | Öncelik |
|---|-------|----------|----------|---------|
| 1 | Raspberry Pi Audio Player | RPi ses oynatıcı | Ethernet, Wi-Fi, I2S | Yüksek |
| 2 | ARM Audio Controller | ARM ses kontrolcüsü | Ethernet, SPI, I2C | Orta |
| 3 | Embedded Linux Audio Device | Gömülü Linux ses | Ethernet, USB | Orta |
| 4 | Industrial Audio Gateway | Endüstriyel ses gateway | Ethernet, RS-485 | Düşük |

---

## 4. İletişim Protokolleri

### 4.1 Yerel İletişim (Local)

| Protokol | Hız | Mesafe | Kullanım |
|----------|-----|--------|----------|
| USB 2.0 | 480 Mbps | 5m | Audio interface, firmware update |
| USB 3.0 | 5 Gbps | 3m | Yüksek hız veri |
| UART | 115200 baud | 15m | Debug, seri iletişim |
| SPI | 10 MHz | 1m | Yüksek hız sensör/EEPROM |
| I2C | 400 kHz | 1m | Düşük hız cihaz yönetimi |
| CAN Bus | 1 Mbps | 40m | Araç içi iletişim |
| RS-485 | 10 Mbps | 1200m | Endüstriyel |
| I2S | 3.072 MHz | Board-level | Ses verisi transferi |

### 4.2 Ağ İletişimi (Network)

| Protokol | Hız | Menzil | Kullanım |
|----------|-----|--------|----------|
| Ethernet 100M | 100 Mbps | 100m | Güvenilir bağlantı |
| Ethernet 1G | 1 Gbps | 100m | Yüksek hız |
| Ethernet 10G | 10 Gbps | 100m | Profesyonel |
| Wi-Fi 6 | 2.4 Gbps | 50m | Kablosuz yüksek hız |
| Bluetooth 5.3 | 3 Mbps | 10m | Kısa menzil kablosuz |
| BLE | 1 Mbps | 30m | Düşük güç |

### 4.3 Uygulama İletişimi (Application)

| Protokol | Tür | Kullanım |
|----------|-----|----------|
| HTTP/HTTPS | REST | CRUD işlemleri |
| WebSocket | Real-time | Canlı güncelleme |
| MQTT | Pub/Sub | IoT, hafif iletişim |
| gRPC | RPC | Yüksek performans |
| Dante/AES67 | Audio-over-IP | Profesyonel ses |
| AirPlay | Streaming | Apple ekosistemi |
| Chromecast | Streaming | Google ekosistemi |

---

## 5. Cihaz Kimliği ve Kaydı

### 5.1 Cihaz Kimliği

Her cihaz benzersiz bir kimliğe sahiptir:

| Alan | Format | Açıklama |
|------|--------|----------|
| Device ID | UUID v4 | Benzersiz cihaz tanımlayıcı |
| Device Name | String | Kullanıcı tanımlı ad |
| Device Type | Enum | amplifier, receiver, interface, etc. |
| Hardware Rev | `Rev X.Y` | Donanım revisyonu |
| Firmware Version | `vX.Y.Z` | Mevcut firmware |
| Driver Version | `vX.Y.Z` | Mevcut sürücü |
| Production Date | ISO 8601 | Üretim tarihi |
| Serial Number | `CM-XXXX-XXXX` | Üretim seri numarası |

### 5.2 Kayıt Akışı (7 Adım)

```
New Device ──▶ Local Network ──▶ API Gateway ──▶ CoreMusic Server ──▶ Device DB
    │               │                │                │                │
    │        Broadcast      Device Discovery   Register Device  Store Record
    │        (mDNS/Bonjour)                    Device Info +    Initial Config
    │                                        Certificate
```

### 5.3 Cihaz Profili

| Alan | Tür | Açıklama |
|------|-----|----------|
| audio_config | Object | Sample rate, bit depth, channels, outputs |
| dsp_config | Object | EQ bands, compressor, limiter, crossover |
| eq_config | Object | 31-band parametric EQ settings |
| crossover_config | Object | Frequency splits, slopes, phases |
| network_config | Object | IP, DNS, proxy, static/DHCP |
| permissions | Array | Device capabilities and restrictions |
| ai_config | Object | AI features, auto-EQ, recommendations |

---

## 6. Merkezi Yönetim

| Yönetim | Açıklama |
|---------|----------|
| Remote Restart | Uzaktan yeniden başlatma |
| Remote Update | Uzaktan firmware/driver güncelleme |
| Configuration | Uzaktan konfigürasyon yönetimi |
| Health Monitoring | Sağlık durumu izleme |
| Performance Monitoring | Performans metrikleri |
| Diagnostics | Tanılama ve hata analizi |
| Log Collection | Uzaktan log toplama |
| Backup/Restore | Konfigürasyon yedekleme |

---

## 7. OTA Güncelleme Desteği

### 7.1 Güncelleme Türleri

| Tür | Kapsam |
|-----|--------|
| Firmware | Ana yazılım güncellemesi |
| Driver | Sürücü güncellemesi |
| DSP | DSP preset/profil güncellemesi |
| Config | Konfigürasyon güncellemesi |
| AI Models | AI model güncellemesi |
| UI Assets | UI varlık güncellemesi |

### 7.2 Güncelleme Kanalları

| Kanal | Kullanım | Sıklık |
|-------|----------|--------|
| Stable | Üretim cihazları | Aylık |
| Beta | Test cihazları | Haftalık |
| Nightly | Geliştirme | Günlük |
| Security | Güvenlik yamaları | Acil |

### 7.3 OTA Akışı

```
Device ──▶ Update Server ──▶ CDN ──▶ Verification
    │              │            │           │
    │      Check Update   Compare Versions  Firmware Binary
    │      (current ver)  Update Available  Verify Signature
    │                      User Approval    Verification Result
    │
    ├──▶ Başarılı ──▶ Flash Firmware ──▶ Reboot ──▶ Report Success
    └──▶ Başarısız ──▶ Report Failure ──▶ Rollback to Previous
```

### 7.4 Güncelleme Gereksinimleri

| Gereksinim | Açıklama |
|------------|----------|
| Signed Firmware | Tüm güncellemeler imzalı olmalı |
| Rollback Support | Güncelleme başarısız olursa geri dönüş |
| Delta Update | Sadece değişiklikler indirilmeli |
| Interrupted Recovery | Kesinti durumunda kurtarma |
| Bandwidth Management | Bant genişliği yönetimi |
| Scheduled Update | Zamanlanmış güncelleme |

---

## 8. Senkronizasyon

### 8.1 Multi-Room Senkronizasyonu

```
Audio Source (Streaming, Local File)
        │
        ▼
Multi-Room Manager (Sync, Zone, Volume)
        │
        ├──▶ Zone 1 (Living Room)
        ├──▶ Zone 2 (Bedroom)
        ├──▶ Zone 3 (Kitchen)
        └──▶ Zone 4 (Garden)
```

### 8.2 Senkronizasyon Türleri

| Tür | Açıklama |
|-----|----------|
| Multi-Room | Ev içi çoklu oda senkronizasyonu |
| Studio Setup | Profesyonel stüdyo kurulumu |
| Car Audio | Araç içi çoklu zone |

### 8.3 Senkronizasyon Gereksinimleri

| Gereksinim | Değer |
|------------|-------|
| Clock Sync | PTP/NTP ile <1ms |
| Latency | Zone'lar arası <50ms |
| Jitter | <10ms |
| Sample Rate | 48kHz senkron |
| Bit Depth | 24-bit |

---

## 9. Cihaz Sağlık İzleme

### 9.1 Sağlık Metrikleri

| Metrik | Eşik | Aksiyon |
|--------|------|---------|
| CPU Kullanımı | >90% | Warn |
| RAM Kullanımı | >85% | Warn |
| DSP Yükü | >80% | Warn |
| Sıcaklık | >80°C | Critical |
| Güç Voltajı | ±%10 | Critical |
| Ağ Durumu | Packet loss >1% | Error |
| Gecikme | >20ms | Warn |
| Hatalar | >0 | Critical |
| Ses Buffer | Underrun | Critical |

### 9.2 Sağlık İzleme Akışı

```
[Her 30 saniye]
Device ──▶ Health Monitor ──▶ Metrics DB
    │              │                │
    │       Collect Metrics    Store Metrics
    │              │
    │       Check Thresholds
    │              │
    └──▶ Threshold Exceeded ──▶ AI Analyzer ──▶ Notification Service
                    │                │                    │
              Analyze Issue   Root Cause         Notify User
                              Analysis
```

---

## 10. AI Cihaz İzleme

| AI Yeteneği | Açıklama | Kullanım |
|-------------|----------|----------|
| Performans Analizi | CPU, RAM, DSP kullanım analizi | Optimizasyon |
| Donanım Analizi | PCB, termal, sinyal kalitesi | Bakım |
| Ses Analizi | Frekans yanıtı, THD, SNR | Kalite |
| Anomaly Detection | Anormal durum tespiti | Erken uyarı |
| Predictive Maintenance | Öngörücü bakım | Arıza önleme |
| Usage Analytics | Kullanım analizi | Optimizasyon |
| Sound Quality AI | Ses kalitesi AI | Otomatik EQ |
| Room Correction | Oda düzeltme | Akustik optimizasyon |

Detaylar: [[architecture/ai/ai-engine]], [[architecture/ai/ai-workflow]]

---

## 11. Güvenlik Mimarisi

| Güvenlik Katmanı | Açıklama | Standart |
|------------------|----------|----------|
| Device Authentication | Cihaz kimlik doğrulama | X.509 Certificate |
| Encrypted Communication | Şifreli iletişim | TLS 1.3 |
| Secure Boot | Güvenli başlatma | UEFI Secure Boot |
| Signed Firmware | İmzalı firmware | RSA/ECDSA |
| Rollback Protection | Geri dönüş koruması | Secure Boot chain |
| Secure OTA | Güvenli güncelleme | Code signing |
| Access Control | Erişim kontrolü | RBAC |
| Audit Logging | Denetim günlüğü | OWASP |
| Zero Trust | Sıfır güven | NIST 800-207 |

Detaylar: [[architecture/l1-security]], [[ADR-022-database-hardened-security]]

---

## 12. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/platform-architecture]] | Platform mimarisi |
| [[electronic/software-architecture]] | Yazılım mimarisi |
| [[electronic/service-architecture]] | Servis mimarisi |
| [[electronic/operating-system-architecture]] | OS mimarisi |
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | Health check |
| [[ADR-061-electronics-architecture]] | Electronics ADR |

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Device Families | 4 |
| Total Devices | 22 |
| Local Protocols | 8 |
| Network Protocols | 6 |
| Application Protocols | 7 |
| OTA Update Types | 6 |
| Security Layers | 9 |
| AI Capabilities | 8 |
| Cross References | 8 |

---

## 14. Cihaz Sayımı ve Tutarlılık Denetimi

`§3` başlığındaki "22 Cihaz" iddiasının ailelerle **aritmetik** karşılaştırması.

| Aile | Dosyadaki Sayı | Tablodaki Satır Sayısı | Sonuç |
|------|----------------|------------------------|-------|
| Home Audio | 7 | 7 | ✅ Eşleşiyor |
| Car Audio | 5 | 5 | ✅ Eşleşiyor |
| Professional Audio | 6 | 6 | ✅ Eşleşiyor |
| Embedded Audio | 4 | 4 | ✅ Eşleşiyor |
| **Toplam** | **22** | **22** | ✅ 7+5+6+4 = 22 |

**Ekosistem diyagramı (`§2`) kontrolü:** Diyagramda Home `7`, Car `5`, Professional `6` yazılıdır; Embedded/Mobile satırı **sayı vermez**. Toplam 22 çıkması için Embedded'in 4 olması gerekir → `§3.4` ile tutarlı ✅.

**Öncelik dağılımı (toplama):**

| Öncelik | Home | Car | Professional | Embedded | Toplam |
|---------|------|-----|--------------|----------|--------|
| Yüksek | 4 | 3 | 4 | 1 | 12 |
| Orta | 2 | 2 | 2 | 2 | 8 |
| Düşük | 1 | 0 | 0 | 1 | 2 |
| **Satır** | **7** | **5** | **6** | **4** | **22** |

**Sonuç:** Üç bağımsız toplam (aile sayısı, satır sayısı, öncelik dağılımı) da **22** ile örtüşür; bu dosyada sayım tutarsızlığı **yoktur**.

**Sınır:** Bu denetim yalnız **iç tutarlılığı** sınar; 22 cihazın gerçekte var olup olmadığını **kanıtlamaz** — o ayrı bir sorudur (`## 20`, satır 2).

---

## 15. Senkronizasyon Gereksinimlerinin Tutarlılığı

`§8.3` tablosundaki değerlerin vault geneliyle ve kendi aralarındaki karşılaştırması.

| Gereksinim | Bu Dosyadaki Değer | Karşılaştırma | Sonuç |
|------------|--------------------|---------------|-------|
| Clock Sync | PTP/NTP · <1ms | `[[electronic/platform-architecture]]` §11 (IEEE 1588) | ✅ Yönelim aynı |
| Zone arası gecikme | <50ms | `§9.1` uyarı eşiği >20ms | ✅ Uyarı eşiği gecikme sınırının altında |
| Jitter | <10ms | `§8.3` clock sync <1ms ile aynı ölçekte değil | ⚠️ Gerekçe yazılmamış → `⚠️ VERIFICATION REQUIRED` |
| Sample rate | 48kHz senkron | Şartname (48 kHz) | ✅ Tutarlı |
| Bit depth | **24-bit** | ADR-017: ses verisi **32-bit float** | ⚠️ Çelişki görüntüsü → aşağıdaki not |

**Bit depth notu (dürüstlük):** `§8.3`'teki `24-bit` bir **aktarım/örnekleme** değeri, ADR-017'deki `32-bit float` ise **iç işleme** tipidir. İkisi farklı katmanlara ait olabilir; ancak dosyada bu ayrım **açıkça yazılmamıştır**. Ayrım yazılmadan iki değer birlikte "doğru" sayılmaz → `## 20`, satır 3.

**Kural:** Bu tabloya yeni satır eklenirken değerler **kopyalanmaz**, ilgili bölüm tekrar okunur; kopyalanan değer eskirse bu dosya yanlış kalır.

---

## 16. Uyumluluk ve Sertifikasyon Yüzeyi

Bu ekosistemin hedef pazarlarda taşıması gereken uyumluluk başlıkları. **Bu dosyada sertifika numarası, test raporu veya belge yoktur.**

| Başlık | Kapsam | İlgili Cihazlar | Bu Dosyadaki Durum |
|--------|--------|-----------------|--------------------|
| CE işareti | Avrupa Birliği pazarı | Tüm cihazlar | `UNKNOWN` — belge yok |
| RoHS | Tehlikeli madde kısıtı | Tüm cihazlar | `UNKNOWN` — belge yok |
| EMC (EMI/EMC) | Elektromanyetik uyum | Tüm cihazlar | `⚠️ VERIFICATION REQUIRED` |
| RED (radyo ekipmanı) | Wi-Fi / Bluetooth içeren cihazlar | Home, Car, Embedded | `UNKNOWN` |
| Otomotiv uyumluluğu | Araç içi ortam koşulları | Car Audio ailesi | `UNKNOWN` |
| Endüstriyel uyumluluk | Endüstriyel ortam | Industrial Audio Gateway | `UNKNOWN` |
| Güvenlik (gizlilik) | Kullanıcı verisi | Tüm cihazlar | `[[architecture/l1-security]]` |

**Kural:** Uyumluluk satırı, ilgili **belge** eklenmeden "tamamlandı" yapılamaz; test raporu olmadan işaretleme yapılmaz.

---

## 17. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden alınmıştır; satır numarası `Select-Object -Index (N-1)` ile doğrulanmıştır.

**Kanıt 1 — Elektronik alan adı repo katman etiketinde geçiyor**

`Dosya: shared/src/AI/AIOrchestrator.php` · `Satır: 32`

```php
'embedded' => ['domain' => 'audio-dsp', 'layer' => 'l0-electronics'],
```

**Kanıt 2 — Donanım ajanı önceliği repo konfigürasyonunda**

`Dosya: shared/src/AI/AIOrchestrator.php` · `Satır: 44`

```php
'hardware' => ['agent' => 'audio-hw', 'priority' => 'high'],
```

**Kanıt 3 — Çalar durumu tarayıcı tarafında ayrı depoda tutuluyor**

`Dosya: assets.coremusic.net/js/coreplayer/coreplayer.controls.js` · `Satır: 7`

```javascript
const PlaybackRepository = {
```

**Yorum:** Kanıtlar repo'nun elektronik/ekosistem kavramını **meta veri** olarak taşıdığını gösterir. `§5`'teki cihaz kaydı, `§7`'deki OTA kanalları, `§8`'deki çoklu oda senkronizasyonu ve `§11`'deki güvenlik katmanları için repo'da **kod karşılığı yoktur**.

---

## 18. Kapsam Sınırı ve Dürüstlük Beyanı

| İddia | Durum | Dayanak |
|-------|-------|---------|
| 4 aile · 22 cihaz | İç tutarlılık **doğrulandı** (`§14`) — varlık doğrulanmadı | `§3` |
| Protokol hızları/menzilleri (`§4`) | Tasarım listesi — ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| Senkronizasyon değerleri (`§8.3`) | Ölçülmemiş gereksinim | `§15` |
| Sağlık eşikleri (`§9.1`) | Tasarım eşiği — izleme uygulaması yok | `⚠️ VERIFICATION REQUIRED` |
| AI cihaz izleme (`§10`) | Model/veri yok | `UNKNOWN` |
| X.509 · TLS 1.3 · UEFI · RSA/ECDSA (`§11`) | Gereksinim — uygulama kanıtı yok | `⚠️ VERIFICATION REQUIRED` |
| NIST 800-207 (Zero Trust) | Standart atfı — uygulanmadı | `⚠️ VERIFICATION REQUIRED` |
| Uyumluluk belgeleri (`§16`) | **YOK** | `UNKNOWN` |
| **PCM5122** | **KATI YASAK** | ADR-038 H001 |
| Repo'da cihaz/OTA/kayıt kaynak kodu | **YOK** | Dizin taraması |

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

---

## 19. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| AB'nin CE işareti için resmi rehber sayfası | https://single-market-economy.ec.europa.eu/single-market/ce-marking_en | 2026-10-13 | HTTP 200 ✓ |
| Avrupa Telekomünikasyon Standartları Enstitüsü (ETSI) | https://www.etsi.org/ | 2026-10-13 | HTTP 200 ✓ |

**Sınır:** Bu kaynaklar uyumluluk çerçevesinin **varlığını** destekler; CoreMusic cihazlarının CE/ETSI uyumlu olduğunu desteklemez — bunun için belge gerekir (`## 16`).

---

## 20. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | 22 cihazdan kaç tanesi gerçekte tanımlı (fiziksel/tasarım)? | `UNKNOWN` | Cihaz kayıt defteri |
| 2 | `§8.3` 24-bit ile ADR-017 32-bit float ayrımı yazılmamış | `⚠️ VERIFICATION REQUIRED` (`§15`) | Ayrım cümlesi + ADR okuması |
| 3 | Jitter <10ms sınırının gerekçesi ne? | `⚠️ VERIFICATION REQUIRED` | Ölçüm + gerekçe |
| 4 | `§16` uyumluluk belgeleri mevcut mu? | `UNKNOWN` — belge yok | Belge talebi |
| 5 | `§4.1` mesafe değerleri (5m, 1200m) hangi ortamda? | `⚠️ VERIFICATION REQUIRED` | Test ortamı tanımı |
| 6 | `[[ecosystem/7-service-integration]]` ve `[[ecosystem/service-health-check]]` diskte var mı? | `⚠️ VERIFICATION REQUIRED` | Dizin doğrulaması |
| 7 | `§7.2` güncelleme kanalları (Aylık/Haftalık/Günlük) hangi SLA'ya bağlı? | `UNKNOWN` | Yayın politikası |

---

## 21. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| Ekosistem | Ortak standartlarla bağlı cihaz kümesi | `§1` |
| Zone | Çoklu oda senkronizasyonundaki coğrafi grup | `§8.1` |
| mDNS/Bonjour | Yerel ağda cihaz keşfi protokolü | `§5.2` |
| UUID v4 | Rastgele üretilen 128-bit tanıtıcı | `§5.1` |
| ISO 8601 | Tarih/saat yazım standardı | `§5.1` |
| Delta update | Yalnız değişen bölümü indiren güncelleme | `§7.4` |
| Rollback | Başarısız güncellemeden önceki sürüme dönüş | `§7.3` |
| Kanal (yayın kanalı) | Stable/Beta/Nightly/Security dağıtım yolu | `§7.2` |
| PTP / NTP | Ağ üzerinden saat senkronizasyonu protokolleri | `§8.3` |
| Jitter | Zaman aralığındaki rastgele sapma | `§8.3` |
| Underrun | Tamponun zamanında doldurulamaması | `§9.1` |
| X.509 | Dijital sertifika biçimi | `§11` |
| RBAC | Rol tabanlı erişim kontrolü | `§11` |
| Zero Trust | Varsayılan güvenmeyen güvenlik modeli | `§11` |
| İç tutarlılık | Bir dosyanın kendi içindeki sayım/uyum denetimi | `§14` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
