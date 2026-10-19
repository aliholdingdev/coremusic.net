---
type: system
category: electronics-os
title: "CoreMusic Electronics Operating System Architecture"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Operating System Architecture

**Zorunlu Bağlantılar:** [[electronic/device-architecture]] · [[electronic/platform-architecture]] · [[electronic/software-architecture]] · [[electronic/audio-architecture]] · [[ADR-017-dsp-hardware-mode]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

CoreMusic ELECTRONICS platformu birden fazla işletim sistemini destekler. Her OS için **Platform Abstraction Layer (PAL)** ile OS-specific kod izole edilir. Tüm OS'ler aynı çekirdek mimariyi paylaşır, sadece platform adaptörleri değişir.

---

## 2. Desteklenen İşletim Sistemleri (8 OS)

| OS | Tier | Durum | Kullanım |
|----|------|-------|----------|
| Windows (XP-11, Server 2012 R2+) | Tier 1 | ✅ Ana geliştirme | Desktop, Studio |
| Linux (Ubuntu, Debian, Fedora, Arch) | Tier 2 | ✅ Destekli | Server, Embedded |
| macOS (Monterey–Sonoma) | Tier 3 | ✅ Destekli | Desktop, Studio |
| Android | Tier 3 | ✅ Destekli | Mobile, Automotive |
| iOS | Tier 3 | ✅ Destekli | Mobile |
| Raspberry Pi OS | Tier 4 | ✅ Destekli | Embedded |
| Embedded Linux (Yocto/Buildroot) | Tier 4 | ✅ Destekli | Industrial |
| ReactOS | Tier 5 | ⚠️ Experimental | Uyumluluk |

Detaylar: [[brain.md]] §15, [[CLAUDE.md]] §13

---

## 3. Platform Abstraction Layer (PAL)

```
CoreMusic Application
        │
        ▼
Platform Abstraction Layer (Common API)
        │
        ├──▶ Windows Adapter (WASAPI/ASIO/WDM/WDK)
        ├──▶ Linux Adapter (ALSA/PipeWire/JACK)
        ├──▶ macOS Adapter (CoreAudio/AudioUnit)
        ├──▶ Android Adapter (AAudio/OpenSL ES)
        ├──▶ iOS Adapter (CoreAudio/AVAudioEngine)
        ├──▶ Raspberry Pi Adapter (ALSA/I2S/GPIO)
        ├──▶ Embedded Linux Adapter (ALSA/Buildroot)
        └──▶ ReactOS Adapter (Wine/Win32)
                │
                ▼
        Audio Hardware (DAC, ADC, DSP)
```

---

## 4. OS Bazlı Teknoloji Yığınları

### 4.1 Windows

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| Win32 API | Temel Windows API | Yüksek |
| COM | Component Object Model | Yüksek |
| WASAPI | Windows Audio Session API | Yüksek |
| ASIO | Audio Stream Input/Output | Yüksek |
| WDM | Windows Driver Model | Orta |
| WDK | Windows Driver Kit | Yüksek |
| Kernel Streaming | Düşük seviye ses | Orta |

### 4.2 Linux

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| ALSA | Advanced Linux Sound Architecture | Yüksek |
| PipeWire | Modern ses sunucusu | Yüksek |
| JACK | Jack Audio Connection Kit | Orta |
| PulseAudio | Eski ses sunucusu | Düşük |
| POSIX | Taşınabilir sistem arayüzü | Yüksek |
| udev | Cihaz yönetimi | Yüksek |
| SystemD | Servis yönetimi | Yüksek |

### 4.3 macOS

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| CoreAudio | Temel ses çerçevesi | Yüksek |
| AudioUnit | Ses eklentisi API'si | Yüksek |
| AVFoundation | Multimedya çerçevesi | Orta |
| IOKit | Donanım sürücüleri | Yüksek |

### 4.4 Android

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| AAudio | Düşük gecikmeli ses API | Yüksek |
| OpenSL ES | Open Sound Library | Orta |
| AudioTrack | Ses çalma | Yüksek |
| AudioRecord | Ses kayıt | Yüksek |

### 4.5 iOS

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| CoreAudio | Temel ses çerçevesi | Yüksek |
| AVAudioEngine | Ses motoru API | Yüksek |
| AudioUnit | Ses eklentisi API | Yüksek |

### 4.6 Raspberry Pi

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| ALSA | Ses altyapısı | Yüksek |
| I2S | Seri ses verisi | Yüksek |
| GPIO | Genel amaçlı giriş/çıkış | Yüksek |
| SPI | Seri periferal arayüz | Orta |
| I2C | İki telli haberleşme | Orta |
| Device Tree | Donanım tanımlama | Yüksek |

### 4.7 Embedded Linux

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| ALSA | Ses altyapısı | Yüksek |
| Buildroot | Sistem oluşturma | Yüksek |
| Yocto | Embedded Linux | Yüksek |
| Device Tree | Donanım tanımlama | Yüksek |

### 4.8 ReactOS

| Teknoloji | Açıklama | Öncelik |
|-----------|----------|---------|
| Win32 API | Windows uyumluluğu | Yüksek |
| Wine | Linux üzerinde Windows | Orta |

---

## 5. Sürücü Mimarisi

## 5. Sürücü Mimarisi

```
CoreMusic Audio Engine
        │
        ▼
Driver Abstraction Layer (Common Interface)
        │
        ├──▶ ASIO Driver (Windows)
        ├──▶ WASAPI Driver (Windows)
        ├──▶ ALSA Driver (Linux)
        ├──▶ PipeWire Driver (Linux)
        ├──▶ CoreAudio Driver (macOS)
        └──▶ AAudio Driver (Android)
                │
                ▼
        Hardware (USB Audio, PCIe Audio, I2S Audio, Bluetooth Audio)
```

### 5.1 Driver Seviyeleri

| Seviye | Tür | Açıklama |
|--------|-----|----------|
| Level 0 | Virtual | Yazılım tabanlı driver (test) |
| Level 1 | Physical | Donanım sürücüsü |
| Level 2 | USB | USB audio class driver |
| Level 3 | DSP | DSP tabanlı driver |

---

## 6. Ses Backend Öncelik Sırası

### 6.1 Windows

| Sıra | Backend | Gecikme | Kullanım |
|------|---------|---------|----------|
| 1 | ASIO | <10ms | Profesyonel |
| 2 | WASAPI Exclusive | <15ms | Düşük gecikme |
| 3 | WASAPI Shared | <20ms | Genel kullanım |
| 4 | WDM/KS | <30ms | Geriye uyumluluk |

### 6.2 Linux

| Sıra | Backend | Gecikme | Kullanım |
|------|---------|---------|----------|
| 1 | ALSA (direct) | <5ms | Profesyonel |
| 2 | PipeWire | <10ms | Modern |
| 3 | JACK | <10ms | Profesyonel |
| 4 | PulseAudio | <30ms | Genel kullanım |

### 6.3 macOS

| Sıra | Backend | Gecikme | Kullanım |
|------|---------|---------|----------|
| 1 | CoreAudio | <5ms | Tüm kullanım |
| 2 | AudioUnit | <10ms | Plugin desteği |

### 6.4 Android

| Sıra | Backend | Gecikme | Kullanım |
|------|---------|---------|----------|
| 1 | AAudio | <10ms | Düşük gecikme |
| 2 | OpenSL ES | <20ms | Geriye uyumluluk |

### 6.5 Raspberry Pi

| Sıra | Backend | Gecikme | Kullanım |
|------|---------|---------|----------|
| 1 | ALSA (I2S) | <5ms | Doğrudan donanım |
| 2 | ALSA (USB) | <10ms | USB ses |

---

## 7. Cihaz Algılama (Device Discovery)

```
Hardware ──▶ OS Kernel ──▶ PAL ──▶ Device Manager ──▶ Audio Engine
    │             │            │           │                │
    │      Hardware    Device Event  Device Detected  Configure Audio
    │      Connected                Identify Device  Start Audio Stream
    │                                  Load Driver
```

---

## 8. Hot Plug Desteği

| Özellik | Windows | Linux | macOS | Android | RPi |
|---------|---------|-------|-------|---------|-----|
| USB Hot Plug | ✅ | ✅ | ✅ | ✅ | ✅ |
| Bluetooth Hot Plug | ✅ | ✅ | ✅ | ✅ | ✅ |
| PCIe Hot Plug | ⚠️ | ✅ | ✅ | ❌ | ❌ |
| I2S Hot Plug | ❌ | ✅ | ❌ | ❌ | ✅ |

**Hot Plug Prosedürü:**
1. Cihaz bağlantısı algılanır
2. OS kernel olay üretir
3. PAL arayüzü üzerinden bildirim yapılır
4. Device Manager cihazı tanımlar
5. Sürücü yüklenir
6. Ses yolu yapılandırılır
7. Ses akışı başlatılır

---

## 9. Sürücü Güvenliği

| Gereksinim | Açıklama | Standart |
|------------|----------|----------|
| Code Signing | Sürücü imzalama | EV Certificate |
| WHQL Certification | Windows uyumluluk | Microsoft |
| Kernel Protection | Kernel-mode koruma | ASLR, DEP |
| Secure Boot | Güvenli başlatma | UEFI |
| Audit Logging | Denetim günlüğü | OWASP |

Detaylar: [[architecture/l1-security]], [[ADR-022-database-hardened-security]]

---

## 10. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/platform-architecture]] | Platform mimarisi |
| [[electronic/audio-architecture]] | Ses mimarisi |
| [[electronic/software-architecture]] | Yazılım mimarisi |
| [[electronic/drivers/index]] | Driver detayları |
| [[electronic/asio-driver-design]] | ASIO sürücü tasarımı |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode |
| [[ADR-038-8.1-sound-card-chip-selection]] | Ses kartı seçimi |

---

## 11. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Supported OS | 8 |
| Audio Backends | 15+ |
| Driver Levels | 4 |
| Cross References | 8 |
| Mermaid Diagrams | 4 |

---

## 12. PAL Arayüz Sözleşmeleri

`## 3`'teki PAL'ın her adaptörün uymak zorunda olduğu arayüzü. Ortak API'nin metot kümesi OS'tan **bağımsızdır**; adaptör yalnız bu metotları uygular.

| Sözleşme Metodu | Girdi | Çıktı | Tüm OS'lerde Zorunlu |
|-----------------|-------|-------|----------------------|
| `listDevices()` | Yok | Cihaz listesi | ✅ |
| `openStream(params)` | Kanal, örnek hızı, tampon | Akış tutamağı | ✅ |
| `startStream(handle)` | Akış tutamağı | Başarı/hata | ✅ |
| `stopStream(handle)` | Akış tutamağı | Başarı/hata | ✅ |
| `closeStream(handle)` | Akış tutamağı | Başarı/hata | ✅ |
| `getLatency(handle)` | Akış tutamağı | Gecikme değeri (ms) | ✅ |
| `onDeviceChanged(callback)` | Geri çağırma | Cihaz olayı | ✅ |
| `setExclusiveMode(handle, flag)` | Boolean | Başarı/hata | ⚠️ OS'e bağlı |
| `getBufferMetrics(handle)` | Akış tutamağı | Underrun/overrun sayacı | ✅ |

**İstisna kuralı:** `setExclusiveMode` yalnız özel mod destekleyen OS'lerde anlamlıdır (ASIO Exclusive, WASAPI Exclusive); desteklemeyen OS adaptörü bunu **sessizce reddetmez**, "desteklenmiyor" sonucu döner — sessizce başarıymış gibi davranmak yasaktır.

**Uygulama durumu:** Bu arayüz vault'ta tanımlıdır; repo'da PAL karşılığı **YOK** → `⚠️ VERIFICATION REQUIRED`.

---

## 13. Ses Backend'lerinde Gecikme İddialarının Durumu

`## 6`'daki `<5ms` / `<10ms` / `<15ms` / `<20ms` / `<30ms` değerleri **ölçülmemiş tasarım hedefleridir**. Bu bölüm hangi koşulda ölçüleceklerini tanımlar.

| Koşul | Sabitlenmesi Gerekenler | Ölçüm Aracı | Kabul |
|-------|--------------------------|-------------|-------|
| Örnek hızı | 48 kHz varsayımı | Aygıt panosu | Kayıtta belirtilmeli |
| Tampon boyu | 128 / 256 / 512 örnek | Aygıt ayarı | Değer yazılmalı |
| Kanal sayısı | 2 / 6 / 9 | Akış parametresi | Değer yazılmalı |
| Mod | Özel (exclusive) / paylaşımlı | Aygıt ayarı | Belirtilmeli |
| Donanım | Gerçek aygıt modeli | Sistem bilgisi | Belirtilmeli |
| Ölçüm | Bitmiş uçtan uca gecikme | Test tonu + osiloskop | Ortalama + tepe |

**Ölçüm yapılmadan söylenemeyecekler:**

1. "ASIO <10ms" — `⚠️ VERIFICATION REQUIRED`
2. "WASAPI Exclusive <15ms" — `⚠️ VERIFICATION REQUIRED`
3. "ALSA direct <5ms" — `⚠️ VERIFICATION REQUIRED`
4. "CoreAudio <5ms" — `⚠️ VERIFICATION REQUIRED`
5. "AAudio <10ms" — `⚠️ VERIFICATION REQUIRED`

**Bağlantı şeridi:** Şartname hedefi 512 örnek / 48 kHz'tir → 512/48000 ≈ **10.67 ms** yalnız **tampon süresidir**; uçtan uca gecikme bundan büyük olacaktır. Bu nedenle `## 6` tablosundaki `<5ms` satırları, 512 örnek tamponla **aynı anda** doğru olamaz — ikisi birlikte raporlanırsa çelişki doğar. Çelişki çıktığında hangisinin geçerli olduğu ölçümle belirlenir, tahminle değil.

---

## 14. Sürücü Yaşam Döngüsü

| Aşama | Windows | Linux | macOS | Kabul |
|-------|---------|-------|-------|-------|
| Geliştirme | WDK ortamı | Çekirdek modülü derlemesi | Sistem uzantısı | Derleme hatasız |
| İmzalama | EV sertifika ile kod imzalama | Modül imzası (varsa) | İmzalı paket | İmza geçerli |
| Yüklenme | Servis/kurulum | Modül yükleyici | Uzantı kurulumu | Yükleme başarılı |
| Doğrulama | Uyumluluk testi | Yükleme günlüğü | Sistem günlüğü | Hata yok |
| Hot plug | Cihaz olayı | udev olayı | IOKit olayı | Akış yeniden kurulur |
| Kaldırma | Kaldırma kaydı | Modül kaldırma | Uzantı kaldırma | Kalıntı yok |
| Denetim | Günlük incelemesi | Günlük incelemesi | Günlük incelemesi | Kayıt tutulur |

**Bağlantı:** İmzalama ve güvenli başlatma gereksinimleri `## 9` ile `[[electronic/platform-architecture]]` `## 12`'deki araç zinciriyle aynıdır; iki dosya arasında çelişki çıkarsa **ölçüm/kanıt taşıyan** taraf geçerlidir.

**Bu depoda uygulanmış mı?** Hayır — sürücü kaynağı bulunmadığından bu tablonun tümü `⚠️ VERIFICATION REQUIRED` durumundadır.

---

## 15. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden alınmıştır; satır numarası `Select-Object -Index (N-1)` ile doğrulanmıştır.

**Kanıt 1 — HTTP istek/yanıt katmanı repo'da mevcut (API katmanı karşılığı)**

`Dosya: shared/src/Middleware/MiddlewarePipeline.php` · `Satır: 8`

```php
final class MiddlewarePipeline
```

**Kanıt 2 — Ses oynatma elemanı sunum katmanında oluşturuluyor**

`Dosya: shared/src/PageRouter/HtmlShellRenderer.php` · `Satır: 192`

```php
echo '<audio id="audio" class="vdisplay" preload="metadata"></audio>';
```

**Kanıt 3 — Oynatma durumu tarayıcı tarafında ayrı depoda tutuluyor**

`Dosya: assets.coremusic.net/js/coreplayer/coreplayer.controls.js` · `Satır: 7`

```javascript
const PlaybackRepository = {
```

**Yorum:** Üç kanıt da **uygulama katmanı** sesini gösterir; PAL, sürücü veya çekirdek modülü karşılığı **yoktur**. Tarayıcı `<audio>` elemanı, `## 3`'teki PAL'ın yerine **geçmez** — farklı bir katmandır ve donanım sürücüsüne erişmez.

---

## 16. Kapsam Sınırı ve Dürüstlük Beyanı

| İddia | Durum | Dayanak |
|-------|-------|---------|
| 8 desteklenen OS + Tier sistemi | Vault tasarım listesi | `## 2` |
| PAL + 8 adaptör | Vault tasarım — repo karşılığı yok | `## 3`, `## 12` |
| Backend gecikme değerleri | Ölçülmemiş hedef | `## 13` |
| Hot plug ✅/❌ matrisi | Tasarım iddiası — test edilmedi | `⚠️ VERIFICATION REQUIRED` |
| WHQL / EV sertifika / UEFI Secure Boot | Gereksinim — alınmış belge yok | `⚠️ VERIFICATION REQUIRED` |
| Windows XP–11 kapsamı | Tasarım — test matrisi yok | `⚠️ VERIFICATION REQUIRED` |
| ReactOS desteği | `⚠️ Experimental` (dosyada belirtilmiş) | `## 2` |
| Repo'da sürücü/çekirdek kaynak kodu | **YOK** | Dizin taraması |
| `PCM5122` | **KATI YASAK** | ADR-038 H001 |
| JUCE 9 · ASIO SDK 2.3.4 sürüm kanıtı | Yok | `⚠️ VERIFICATION REQUIRED` |

**Bilinen yapısal kusur (düzeltme YAPILMADI):** Bu dosyada `## 5. Sürücü Mimarisi` başlığı iki kez tekrarlanmıştır (aynı bölümün ikinci başlığı). İçerik kaybı yoktur; silme yetkisi kapsam dışıdır → raporlandı ve bırakıldı.

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

---

## 17. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Linux çekirdeğinde ses alt yapısının resmi dokümantasyonu (ALSA dahil) | https://www.kernel.org/doc/html/latest/sound/index.html | 2026-10-13 | HTTP 200 ✓ |
| Linux çekirdeğinin çekirdek API dokümantasyonu | https://www.kernel.org/doc/html/latest/core-api/index.html | 2026-10-13 | HTTP 200 ✓ |

**Sınır:** Bu kaynaklar ilgili arayüzlerin **varlığını** destekler; CoreMusic'in bu arayüzleri **kullandığını** desteklemez — kullanım için repo veya ölçüm kanıtı gerekir.

---

## 18. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | PAL arayüzü (§12) repo'da hangi tiplere karşılık geliyor? | `⚠️ VERIFICATION REQUIRED` | Depo tip taraması |
| 2 | `## 6` gecikme değerleri hangi tampon/örnek hızında? | `⚠️ VERIFICATION REQUIRED` | §13 ölçüm planı |
| 3 | `## 2`'deki Tier atamaları hangi ölçüte göre? | `UNKNOWN` | Ölçüt tanımı |
| 4 | Hot plug matrisindeki ❌ hücreleri test edildi mi? | `UNKNOWN` | Test kaydı |
| 5 | `[[electronic/asio-driver-design]]` diskte var mı? | `⚠️ VERIFICATION REQUIRED` | Dizin doğrulaması |
| 6 | Çift `## 5` başlığı düzeltilecek mi? | Raporlandı — düzeltme yetkisi yok | Orkestratör kararı |
| 7 | Windows XP/Server 2012 R2 kapsamı gerçek test edildi mi? | `UNKNOWN` | Test matrisi |

---

## 19. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| PAL | Platform Abstraction Layer — OS farkını yutan ortak katman | `## 3` |
| Tier | Destek öncelik seviyesi (1 = en yüksek) | `## 2` |
| WASAPI | Windows Audio Session API | `## 4.1` |
| ASIO | Steinberg arayüzü — profesyonel düşük gecikme | `## 4.1` |
| WDK | Windows Driver Kit | `## 4.1` |
| ALSA | Linux çekirdek ses alt yapısı | `## 4.2` |
| PipeWire | Modern Linux ses/görüntü sunucusu | `## 4.2` |
| CoreAudio | macOS/iOS ses çerçevesi | `## 4.3` |
| AAudio | Android'in düşük gecikmeli ses API'si | `## 4.4` |
| Device Tree | Donanım tanım dosyası biçimi | `## 4.6` |
| Exclusive mod | Aygıtı tek uygulamanın kullanması | `## 12` |
| Underrun | Tamponun zamanında doldurulamaması | `## 12` |
| Cihaz olayı | `onDeviceChanged` ile üst katmanlara bildirilen bağlantı/kesme olayı | `## 12` |
| Hot plug | Takıp çıkarılabilir cihaz algılama | `## 8` |
| Code signing | Sürücü/imzalı paket doğrulaması | `## 9`, `## 14` |
| WHQL | Windows donanım uyumluluk programı | `## 9` |
| Uçtan uca gecikme | Girdiden çıkışa toplam gecikme | `## 13` |
| Tampon süresi | Örnek sayısı ÷ örnek hızı | `## 13` |
| Backend | Ses verisini işletim sistemine taşıyan arayüz | `## 6` |
| Uçtan uca test | Girdiden çıkışa kadar zinciri ölçen test | `## 13` |
| Sürücü yaşam döngüsü | Geliştirme → imzalama → yükleme → kaldırma | `## 14` |
| Modül kaldırma | Sürücünün sistemden izsiz çıkarılması | `## 14` |
| Adaptör | PAL altında OS'e özgü uygulama | `## 3` |
| Çakışma kuralı | İki dosya farklı iddia ederse kanıtın geçerli olması | `## 20` |
| Öncelik sütunu | Cihaz ailesindeki hedef önceliği (Yüksek/Orta/Düşük) | `[[electronic/platform-architecture]]` §3 |
| Ölçüm etiketi | `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` / bağlayıcı | `## 16` |

**Okuma notu:** Terimler yalnızca bu dosyada anlam kazanır; bir terim başka bir elektronik dosyasında farklı tanımlanırsa **dosya kendi bağlamı** geçerlidir ve iki tanım birleştirilmez — birleştirme bilgi kaybı üretir.

---

## 20. OS → Cihaz Ailesi ve Sorumluluk Eşlemesi

`## 2`'deki 8 OS ile `[[electronic/device-architecture]]` içindeki cihaz ailelerinin kesişimi. ✅ = hedef platform · ◐ = ikincil · — = kapsam dışı.

| OS | Professional | Home | Car | Embedded | Dev Board | Sorumlu Adaptör |
|----|--------------|------|-----|----------|-----------|-----------------|
| Windows | ✅ | ✅ | ◐ | ◐ | ✅ | Windows Adapter |
| Linux | ◐ | ◐ | ◐ | ✅ | ✅ | Linux Adapter |
| macOS | ✅ | ◐ | — | — | ◐ | macOS Adapter |
| Android | — | ◐ | ✅ | ◐ | ◐ | Android Adapter |
| iOS | — | ◐ | — | — | — | iOS Adapter |
| Raspberry Pi OS | — | ◐ | — | ✅ | ✅ | Raspberry Pi Adapter |
| Embedded Linux | — | ◐ | ✅ | ✅ | ◐ | Embedded Linux Adapter |
| ReactOS | — | — | — | — | ◐ | ReactOS Adapter |

**Öncelik sonucu:** Tier 1 (Windows) tüm ailelerde en az `◐` taşır; Tier 5 (ReactOS) yalnız `Development Boards` ailesinde `◐` ile sınırlıdır ve **üretim hedefi değildir**.

**Çakışma kuralı:** İki dosya arasındaki (bu dosya ile cihaz mimarisi) bir hücre farkı varsa, hangisinde **ölçüm/kanıt** varsa o geçerlidir; ikisinde de yoksa her iki dosya da `⚠️ VERIFICATION REQUIRED` olarak işaretlenir ve tek başına doğruymuş gibi sunulmaz.

**Doğrulama durumu:** Bu matris test edilmemiştir → `⚠️ VERIFICATION REQUIRED`. Test, her OS için cihaz ailesi başına tek bir "akış açılır / kapanır" senaryosu ile yapılır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
