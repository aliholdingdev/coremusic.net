---
type: electronic
category: usb-drivers
title: "CoreMusic — USB Audio Drivers (UAC 2.0)"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — USB Audio Drivers

**See also:** [[electronic/drivers/index]] · [[electronic/hardware/index]]

---

## 1. USB Yığını

### 1.1 Katmanlar

```
Uygulama (Neva Engine, PLANNED)
    ↓
HAL (WASAPI / ALSA / CoreAudio)
    ↓
OS USB ses sınıfı sürücüsü (UAC2 class-compliant)
    ↓
USB istemci sürücüsü (host controller sürücüsü)
    ↓
USB host controller donanımı (xHCI/EHCI)
    ↓
USB 2.0/3.0 hat (isochronous endpoint)
    ↓
XMOS XU316 (USB device firmware) → I2S/TDM → PCM3168A DAC
```

### 1.2 Katman Tablosu

| Katman | Sorumluluk | Dosya |
|--------|-----------|-------|
| Uygulama/HAL | Format seçimi, akış | [[audio-drivers]] §5 |
| OS UAC2 sınıfı sürücüsü | Cihaz keşfi, ses akışı | bu dosya §3 |
| USB istemci sürücüsü | Paket zamanlama, hata telafisi | bu dosya §2 |
| Host controller (xHCI) | DMA, mikro kare zamanlaması | bu dosya §2.3 |
| Cihaz firmware'i | USB device stack, I2S master | [[electronic/firmware/index]] |

### 1.3 OS USB Sürücüsü Yerleşimi

| OS | UAC2 sürücüsü | Özel sürücü gerekir mi? |
|----|---------------|-------------------------|
| Windows 10/11 | Yerleşik UAC2 | Hayır (class-compliant) |
| Linux | snd-usb-audio (çekirdek) | Hayır |
| macOS | AppleUSBAudio | Hayır |

Class-compliant tasarım sayesinde XMOS XU316 özel sürücü kurulumu gerektirmez (ADR-038). Sürüm kanıtı olmayan iddialar §6.4'te işaretlenmiştir.

### 1.4 Host Controller ve Zamanlama

USB ses isochronous transferleri gerçek zamanlı zamanlamaya tabidir: host controller her USB mikro karesinde (125 µs / high-speed, 1 ms / full-speed) endpoint'e slot ayırır. Ses paketleri bu slotlara yazılır; slot kaçırılırsa veri kaybı (packet error) oluşur. OS ses sürücüsü kaçırılan kareyi doldurma (implicit feedback) veya yeniden gönderme ile telafi etmez — isochronous kayıp kabul edilir, gecikme telafisi cihaz firmware'inde tamponlanır.

### 1.5 Saat Kaynağı ve Asenkron Transfer

USB ses üç transfer modu sunar:

| Mod | Saat kaynağı | Kullanım |
|-----|--------------|----------|
| Synchronous | Host clock | Nadir (clock drift riski) |
| Asynchronous | Cihaz clock (feedback endpoint) | **CoreMusic tercihi** |
| Adaptive | Host, cihaz arası | Ortak |

Asynchron modda cihaz kendi saatini kullanır, host'a feedback paketiyle “ne kadar tampon doldu” bilgisini iletir; host bu geri bildirime göre hızını ayarlar. 48 kHz sabitliğinde clock drift (ppm) telafisi bu yolla otomatik yapılır.

---

## 2. Desen (Isochronous / Bulk)

### 2.1 USB Transfer Türleri

| Tür | Zamanlama | Hata telafisi | Ses akışı |
|-----|-----------|---------------|-----------|
| Control | Zamanlanmamış | Retry var | Yapılandırma |
| Isochronous | Zaman garantili | Retry yok | **PCM verisi** |
| Bulk | Bandwidth arttığında | Retry var | Firmware/DFU güncelleme |
| Interrupt | Periyodik | Retry yok | HID, feedback |

### 2.2 Isochronous Deseni

Isochronous endpoint her mikro karede sabit byte taşır; paket kaybı olsa bile zamanlama bozulmaz. USB 2.0 high-speed (480 Mbps) için teorik maksimum isochronous bandwidth yeterlidir: 8 kanal × 4 byte × 48 kHz = 1.536 Mbps — hat kapasitesinin çok altındadır. Bant genişliği sorunu değil, zamanlama (jitter) ve packet error sorunudur.

### 2.3 Bulk Deseni

Bulk transferler garantili teslimat ister (retry), gerçek zaman garantisi vermez. CoreMusic'te kullanım alanı: firmware OTA güncelleme kanalı (§2.5) ve cihaz içi telemetri. Ses akışı bulk ile taşınmaz — bulk retry'ları ses akışında gecikme patlaması yaratır.

### 2.4 Isochronous Parametreleri (CoreMusic)

| Parametre | Değer |
|-----------|-------|
| Transfer hızı | USB 2.0 High-Speed (480 Mbps) |
| Endpoint | Isochronous, asynchron (cihaz saati) |
| Paket boyutu | 48 kHz × 4 byte × kanal ÷ kare |
| Kanal (8.1) | 9 kanal × 4 byte = 36 byte/örnek |
| Bandwidth | 36 × 48000 = 1.728 Mbps (8.1 tam) |

### 2.5 Bulk — Firmware/DFU Kanalı

Firmware güncellemesi bulk depoda (DFU sınıfı) taşınır; akış kapatılır, cihaz DFU moduna geçer, bulk ile yazılır, doğrulanır, yeniden başlatılır. OTA ve firmware bump → DUR + handover (kural 13). Ses akışıyla eşzamanlı DFU yasaktır.

### 2.6 Hata Modları (Desen)

| Mod | Hata | Sonuç | Kurtarma |
|-----|------|-------|----------|
| Isochronous | Packet error | Click/boşluk | Cihaz firmware tamponu (§4.5) |
| Isochronous | Schedule overrun (host geç kaldı) | Buffer underrun | [[audio-drivers]] §6.4 |
| Bulk | Pipe error | Güncelleme yarıda | DFU yeniden deneme |

### 2.7 Bandwidth Rezervasyonu

USB 2.0 tek hat üzerinde birden çok cihaz paylaşır (hub). Ses cihazına yeterli bandwidth rezerve edilmezse isochronous paketler düşer. CoreMusic kuralı: ses cihazı doğrudan host portuna bağlanır (hub yasak), hub gerekiyorsa ayrı beslemeli (powered) hub.

### 2.8 Mikro Kare (Microframe) Zamanlaması

USB 2.0 high-speed mikro karesi 125 µs'dir; 48 kHz'te bir örnek süresi 20.83 µs olduğundan her mikro kareye ~6 örnek sığar. Zamanlama hassasiyeti mikro kare granülaritesindedir; jitter bütçesi bu granülaritenin altındadır.

### 2.9 Otomatik Gecikme Ayarı (Implicit Feedback)

| Mod | Feedback | Gecikme davranışı |
|-----|----------|-------------------|
| Asynchronous (tercih) | Cihaz → host feedback endpoint | Host tamponu cihaz hızına göre ayarlar |
| Adaptive | Host clock baskın | Drift kademeli telafi |

### 2.10 Desen Seçim Özeti

| İhtiyaç | Desen | Gerekçe |
|---------|-------|---------|
| PCM ses | Isochronous | Zaman garantisi |
| Yapılandırma | Control | Retry'lı, küçük |
| Firmware/DFU | Bulk | Güvenilir teslimat |
| Feedback/HID | Interrupt | Periyodik, küçük |

---

## 3. Sınıf Kodları (UAC · HID)

### 3.1 USB Audio Class 2.0

| Özellik | Değer |
|---------|-------|
| Sürüm | UAC 2.0 |
| Max Kanal | 8 (7.1 surround; 8.1 için cihaz-yönetimli ek yol — §3.3) |
| Bit Derinliği | 32-bit float (bu yolda; UAC2 s16/s24/s32 taşıyabilir) |
| Örnekleme | 44.1 kHz - 192 kHz |
| Isochronous | Evet |
| Latency | <5 ms (cihaz tamponu hariç; toplam bütçe §5.1) |

### 3.2 UAC2 Tanıtıcıları

| Alan | Değer | Kullanım |
|------|-------|----------|
| Sınıf kodu | 0x01 (Audio) | Sınıf eşlemesi |
| Alt sınıf | 0x01 (Audio Control) / 0x02 (Audio Streaming) | Bileşen ayrımı |
| Sürüm | 2.00 | Özellik seçimi |
| Terminaller | Input/Output/Feature | Sinyal yolu haritası |

### 3.3 8.1 Kanal ve UAC2

UAC2 tek akışta 8 kanalı doğal taşır; 8.1 (9. kanal subwoofer yolu) iki yolla kurulur: (1) iki ayrı UAC2 streaming arayüzü (8+1), (2) cihaz firmware'inde kanal mapping ile tek akışta 9 kanal (XMOS XU316 cihaz-yönetimli, §3.5). Seçim donanım tasarımına bağlıdır: [[electronic/hardware/index]] · ADR-038.

### 3.4 HID Sınıfı

USB HID sınıfı (0x03) ses cihazlarındaki tuş/turnike (knob) denetimleri için kullanılır: play/pause, volume knob. HID raporları interrupt endpoint ile taşınır; CoreMusic HAL'ı HID olaylarını kullanıcı girdisi olarak alır, ses akışına dokunmaz.

### 3.5 XMOS XU316 Sınıf Sunumu

| Özellik | Değer |
|---------|-------|
| Chip | XMOS XU316 |
| USB | USB 2.0 High-Speed |
| Kanal | 8-kanal I2S (cihaz-içi DSP ile 8.1 yönetimi) |
| DSP | Zero-latency DSP (ADR-017) |
| Sürücü | Sınıf-yönetici (class-compliant) |

XU316'nın cihaz-içi DSP'si sinyali I2S'e göndermeden önce işler (crossover, EQ, limiter — bkz. [[electronic/dsp/index]]); sürücü katmanı yalnız USB akışını taşır.

### 3.6 UAC1 vs UAC2

| Kriter | UAC1 | UAC2 |
|--------|------|------|
| Max örnekleme | 96 kHz (pratik) | 768 kHz (spec) |
| Kanal | ≤8 | ≤256 (spec) |
| Alt kare (subframe) | 16/24/32-bit | 16/24/32-bit |
| Windows desteği | Yerleşik | 10+ yerleşik |
| Seçim | Uyumluluk | **CoreMusic: UAC2** |

### 3.7 Terminal Haritası (8.1)

| Terminal | Yön | Kanal |
|----------|-----|-------|
| USB Streaming OUT | Cihaz → host | 8 kanal (ana akış) |
| USB Streaming OUT (sub) | Cihaz → host | 1 kanal (LFE yolu) |
| Feature Unit | — | Mute/Volume |
| Output Terminal | DAC çıkışı | Analog (PCM3168A) |

### 3.8 Sınıf Kodu Hata Modları

| Hata | Belirti | Kurtarma |
|------|---------|----------|
| Sınıf eşleşmedi | Cihaz “tanınmadı” | Firmware sınıf kontrolü ([[electronic/firmware/index]]) |
| Terminal eksik | Kanal sayısı yanlış | Firmware/terminal haritası |
| UAC1 cihaz | 96 kHz üstü reddedilir | UAC1 moduna düş, bildir |

### 3.9 Sürücü Sınıfı Seçim Matrisi

| Cihaz | Sürücü | Özel kurulum |
|-------|--------|--------------|
| XMOS XU316 (class-compliant) | OS yerleşik UAC2 | Yok |
| Özel USB DSP (class dışı) | Üretici sürücüsü | Evet — istisna |
| Sanal USB cihazı | Sanal sürücü | [[audio-drivers]] §5.6 |

### 3.10 Sınıf Kodu Ölçümü

| Metrik | Doğrulama |
|--------|-----------|
| Kanal sayısı | OS cihaz listesi vs spec (§3.1) |
| Örnekleme listesi | 44.1/48/96/192 kHz görünebilir |
| Bit derinliği | 32-bit float yol doğrulaması (loopback, §6.1) |

---

## 4. Cihaz Keşfi

### 4.1 Enumeration Akışı

```
USB Tak ──▶ USB Enumeration ──▶ {Cihaz Tanımla}
                                  │
                     XMOS ▼        ▼ Standart
              XMOS Driver    UAC 2.0 Driver
                     │              │
                     └──────┬───────┘
                            ▼
                   Kanal/Format Ayarla
                            │
                            ▼
                   Stream Başlat
                            │
                            ▼
                   Çıkarma ──▶ Temizle
```

### 4.2 Enumeration Adımları

| # | Adım | Detay |
|---|------|-------|
| 1 | Attach olayı | Hub port durumu değişir |
| 2 | Reset + adres atama | Host cihazı sıfırlar |
| 3 | Descriptor okuma | Device/config/interface descriptor |
| 4 | Sınıf eşleme | UAC2/HID (§3) |
| 5 | Ses kontrol keşfi | Terminal/feature unit (§3.7) |
| 6 | Format listesi | Desteklenen örnekleme/bit (§3.1) |
| 7 | Cihaz kaydı | OS ses cihaz listesine ekle ([[audio-drivers]] §4) |

### 4.3 Descriptor Hata Modları

| Hata | Sonuç | Kurtarma |
|------|-------|----------|
| Device descriptor okunamadı | Cihaz görünmez | Kablo/hat kontrolü, port değişimi |
| Config descriptor tutarsız | Sınıf sürücüsü bağlanmaz | Firmware descriptor düzeltmesi |
| Interface eksik | Kanal eksik görünür | Firmware (terminal haritası §3.7) |

### 4.4 Sıcak Tak/Çıkarma (Hot-Plug)

| Olay | Davranış |
|------|----------|
| Takma | Otomatik algılama, stream başlat (§4.2) |
| Çıkarma | Graceful stop, WASAPI fallback ([[audio-drivers]] §4.5) |
| Yeniden takma | Otomatik reconnect (3 retry — [[audio-drivers]] §4.7) |

### 4.5 Cihaz Tamponu ve Keşif Sırası

Keşif sırasında cihaz firmware'i hazır tampon (ring buffer) sunar; bu tampon boyutu toplam gecikme bütçesine yazılır (§5.1). Keşif tamamlanmadan stream başlatılmaz — format müzakeresi (§3.10) keşif parçasıdır.

### 4.6 Çoklu USB Ses Cihazı

| Senaryo | Davranış |
|---------|----------|
| İki USB ses cihazı | İkisi de listede; kullanıcı seçer / whitelist ([[device-integration]] §4) |
| Aynı model iki cihaz | VID:PID aynı — sıra/seri no ile ayrım |
| Hub üzerinde iki cihaz | §2.7 kuralı: ses doğrudan port (hub yasak) |

### 4.7 Keşif Hızı

| Metrik | Hedef |
|--------|-------|
| Tak → cihaz listesinde | <2 sn |
| Tak → stream başlatılabilir | <3 sn |
| Çıkarma → temiz kapanış | <500 ms (mute ile) |

### 4.8 Keşif Güvenliği

| Kontrol | Değer |
|---------|-------|
| Descriptor fuzzing | USB descriptor doğrulama (kernel seviyesinde) — §6.3 |
| Bilinmeyen sınıf | Kullanıcıya sor, otomatik yükleme yok |
| Driver signing | OS imza denetimi ([[architecture/07-security/driver-signing]]) |

### 4.9 Keşif Telemetrisi

Her enumeration olayı şu alanlarla loglanır: VID/PID, sürüm, interface sayısı, keşif süresi, sonuç (başarılı/hata kodu). Telemetri §6.4 ölçüm protokolünün girdisidir.

### 4.10 Keşif ↔ Hotplug Bileşkesi

Keşif akışı (§4.2) yalnız açılışta değil, her attach olayında çalışır; hotplug olayları [[device-integration]] §2 zincirine bağlanır. Çıkarma olayı keşif kaydını cihaz listesinden siler, fallback zincirini tetikler.

---

## 5. Hata Kurtarma

### 5.1 Hata Modları

| # | Hata | Belirti | Kök Neden | Kurtarma |
|---|------|---------|-----------|----------|
| 1 | Pipe error (stall) | Akış durur | Endpoint stall | Pipe reset, akış yeniden başlat |
| 2 | babble (taşma) | Cihaz yanıtı hatası | Firmware/USB yığını | Cihaz reset (USB port reset) |
| 3 | Packet error | Tıkırtı/boşluk | Hat gürültüsü, bandwidth | Kablo/hub kontrolü (§2.7) |
| 4 | Cihaz çıkarıldı | Akış aniden kesilir | Fiziksel | Graceful stop + fallback (§4.4) |
| 5 | Sürücü INVALIDATED | Akış hatası | OS seviyesi cihaz kaybı | [[audio-drivers]] §6.5 |
| 6 | Format reddi | Açma başarısız | Müzakere başarısız | Fallback format, bildir |

### 5.2 Pipe Reset Akışı

```
Pipe error (stall) tespiti
    ↓
Akışı durdur (mute)
    ↓
snd_usb_endpoint reset / WASAPI stream reset
    ↓
Endpoint'u yeniden yapılandır (§2.4)
    ↓
Akışı başlat, doğrula (§5.6)
```

### 5.3 Cihaz Reset Bedeli

USB port reset cihazı tamamen yeniden enumerate eder (§4.2 — ~2 sn). Bu bedel yüksek olduğundan reset yalnızca kurtarma zincirinin son adımındadır: önce pipe reset, sonra cihaz reset, sonra port/hub değişimi önerisi.

### 5.4 Fallback Zinciri (USB)

```
USB cihaz kaybı
    → mute (DSP'e bayrak)
    → sonraki kullanılabilir cihaz (whitelist sırası)
    → sistem varsayılanı
    → sustur + kullanıcı uyar
```

### 5.5 Yeniden Bağlanma

| Parametre | Değer |
|-----------|-------|
| Retry | 3 (artışlı: 1 sn, 2 sn, 5 sn) |
| Sonuç | Başarısız → fallback kalıcı, kullanıcı uyar |

### 5.6 Kurtarma Doğrulaması

Kurtarma sonrası doğrulama: akış RUNNING, xrun sayacı düzeldi, loopback doğrulaması temiz (§6.1). Doğrulama başarısız → bir üst adım (§5.3).

### 5.7 DFU Kurtarması

Firmware güncellemesi başarısız olursa cihaz DFU modunda kalır; OS ses listesinde görünmez. Kurtarma: DFU yeniden yazma (firmware kanalı — [[electronic/firmware/index]]). OTA/firmware bump → DUR + handover (kural 13).

### 5.8 Hata Sıklığı Hedefleri

| Hata | Hedef |
|------|-------|
| Pipe error | <1/gün (sağlıklı sistem) |
| Packet error | <%0.01 paket |
| Cihaz kaybı | Donanım olayıdır — sürücü kaynaklı 0 |
| Format reddi | 0 (§3.10 format listesi ile) |

### 5.9 Hata Kaydı

Her kurtarma olayı: tür, USB hata kodu (URB status), VID/PID, zaman damgası, kurtarma sonucu. Kayıt §6.4 raporlamasına girer.

### 5.10 Kurtarma Sırası Özeti

| Sıra | Adım | Bedel |
|------|------|-------|
| 1 | Mute | Anlık |
| 2 | Pipe/endpoint reset | ~100 ms |
| 3 | Cihaz reset (port reset) | ~2 sn |
| 4 | Fallback cihaz | Anlık (başka cihaz varsa) |
| 5 | Sustur + uyar | — |

---

## 6. Ölçüm

### 6.1 Loopback Ölçümü

| Adım | İşlem |
|------|-------|
| 1 | USB DAC çıkışını USB girişine fiziksel bağla |
| 2 | Impuls gönder, girişte yakala |
| 3 | Gecikmeyi örnek cinsinden ölç (cross-correlation) |
| 4 | 10 ölçümün medyanını raporla |

Ölçüm ortamı: 48 kHz, 32-bit float, 512 buffer, 8.1 — §5.1 bütçesiyle karşılaştırılır.

### 6.2 Packet Error Oranı

| Metrik | Tanım | Hedef |
|--------|-------|-------|
| PER (packet error rate) | Hatalı paket / toplam | <%0.01 |
| Kaçırılan mikro kare | Lost microframes | 0 |
| Feedback hataları | Hatalı feedback paketi | 0 |

### 6.3 Descriptor Fuzzing

USB descriptor doğrulama (fuzz testleri) keşif katmanının güvenlik ölçümüdür: bozuk descriptor üretimi cihaz sürücüsünün çökmemesini doğrular. Çekirdek USB yığını bu doğrulamaları içerir; cihaz firmware'i ayrıca test edilir ([[electronic/firmware/index]]).

### 6.4 Ölçüm Raporu

| Alan | İçerik |
|------|--------|
| Bağlam | VID/PID, OS, sürücü sürümü |
| Ortam | 48 kHz / 512 / 8.1 / 32-bit float |
| Metrikler | Gecikme (örnek), PER, xrun oranı, enumeration süresi |
| Tarih | 2026-10-13 (ölçüm koşulu) |

### 6.5 Karşılaştırmalı Ölçüm

| Bağlantı | Beklenen |
|----------|----------|
| Doğrudan host portu | En düşük jitter |
| Powered hub | §2.7 uyarısı — ölçülebilir jitter artışı |
| Uzatıcı (extender) | Kaçınılmalı; ölçülebilir PER artışı |

### 6.6 Bant Genişliği Ölçümü

Toplam isochronous bandwidth ölçümü (§2.4 hesabı): hat üzerinden ölçülen byte/saniye ile spec karşılaştırılır; sapma ≥%5 ise hat/hub problemi araştırılır.

### 6.7 Gecikme Bütçesi Dağılımı

| Katman | Katman gecikmesi |
|--------|------------------|
| USB isochronous taşıma | Mikro kare granülaritesi (§2.8) |
| Cihaz firmware tamponu | [[electronic/firmware/index]] |
| OS ses tamponu | [[audio-drivers]] §3 |
| **Toplam hedef** | USB cihaz yolu <5 ms (§3.1; bütçe §5.1) |

### 6.8 Ölçüm Hataları

| Hata | Önlem |
|------|-------|
| Yazılım zamanlayıcı ile ölçüm | Fiziksel loopback (§6.1) |
| Tek ölçüm | Medyan (§6.1) |
| Hub'sız doğrulama atlanması | Powered hub senaryosu da ölçülür (§6.5) |

### 6.9 Ölçüm Tekrarı

3 ortam (soğuk açılış, 1 saat çalışma, sıcak) × 10 ölçüm — [[audio-drivers]] §7.6 ile aynı disiplin.

### 6.10 Ölçümün Karara Girdisi

Ölçüm sonucu: (1) hub yasağı teyidi (§2.7), (2) cihaz tampon boyutu önerisi (firmware'e), (3) fallback eşiği ayarı. Kararlar vault'a ölçüm kaydıyla yazılır.

---

## 7. Kaynakça

### 7.1 Web Araştırma

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| Linux çekirdek USB sürücü API dokümantasyonu §1.1-§1.2 katman tanımının resmi referansıdır | https://www.kernel.org/doc/html/latest/driver-api/usb/index.html | 2026-10-13 | HTTP 200 doğrulandı |
| Linux USB çekirdek (usb.html) §2 transfer türleri/desen anlatımının dayanağıdır | https://docs.kernel.org/driver-api/usb/usb.html | 2026-10-13 | HTTP 200 doğrulandı |
| Microsoft Windows USB sürücü geliştirme kılavuzu §1.3 Windows USB yığını için resmi kaynaktır | https://learn.microsoft.com/en-us/windows-hardware/drivers/usbcon/usb-driver-development-guide | 2026-10-13 | HTTP 200 doğrulandı |

### 7.2 Vault Referansları

| Konu | Dosya |
|------|-------|
| UAC2 cihaz firmware'i (XMOS XU316) | [[electronic/firmware/index]] |
| DAC pin/format (PCM3168A H001) | [[electronic/hardware/index]] · [[ADR-038-8.1-sound-card-chip-selection]] |
| Hotplug zinciri | [[device-integration]] §2 |
| Gecikme bütçesi ve ölçüm protokolü | [[audio-drivers]] §3 · §7 |

### 7.3 Sürüm Kanıtı Durumu

| İddia | Durum |
|-------|-------|
| UAC 2.0 sınıf spesifikasyonu sürümü | ⚠️ VERIFICATION REQUIRED — USB-IF belge sürümü depoda kanıtlanmadı |
| XMOS XU316 firmware sürümü | [[electronic/firmware/index]] — firmware sürümü firmware dosyasının konusudur |

### 7.4 Repo Kod Durumu

**Repo'da sürücü/DSP kodu YOK — kaynak: .ai/electronic/**

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode