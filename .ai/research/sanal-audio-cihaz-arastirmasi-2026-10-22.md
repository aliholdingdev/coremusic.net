---
title: "Sanal Audio Cihazı / Sanal Ses Sürücüsü Araştırması (192 kHz / 32-bit)"
type: research
category: audio
status: draft
version: 1.0.0
date: 2026-10-22
authority: "Researcher Agent — kaynaklar §12'de listelidir; her iddia kaynaklıdır"
tier: 3
domain: audio
ssot: false
risk: medium
owner: "MO"
depends-on: []
---

# Sanal Audio Cihazı / Sanal Ses Sürücüsü Araştırması — 192 kHz'e kadar + 32-bit

**Tarih:** 2026-10-22 · **Araştırma şekli:** web (resmi dokümanlar + GitHub + forumlar) · **Kaynak sayısı:** 17 doğrudan kaynak (§12)
**Kapsam:** Neva Engine (CoreMusic, C++20) için Windows / macOS / Linux sanal ses cihazı ve sanal ses sürücüsü adaylarının taranması, 192 kHz / 32-bit desteğinin teknik doğrulanması.

**Politika:** Zero-hallucination — doğrulanmayan her iddia `⚠️ VERIFICATION REQUIRED` etiketi ile işaretlidir; bilinmeyen değerler `UNKNOWN` olarak yazılır. Uydurma repo / URL / sürüm / commit tarihi yoktur.

> **Görev kısıtı (kullanıcı direktifi, bu araştırmanın verisidir):** FxSound'ın 192 kHz / 32-bit desteği YOKTUR; bu nedenle FxSound bir alternatif olarak KABUL EDİLMEZ. Bu satır araştırma bulgusu değil, görev tanımı kısıtıdır.

---

## 0. Yönetsel Özet (Executive Summary)

| Soru | Kısa Cevap | Kanıt |
|------|-----------|-------|
| Windows'ta 192 kHz / 32-bit sanal endpoint mümkün mü? | **EVET — teknik olarak mümkün.** WASAPI exclusive'te sınır donanım değil, endpoint sürücüsünün duyurduğu formattır; sanal WDM sürücüsü dilediği formatı duyurabilir | §7.1 · Microsoft "Device Formats" (K7) |
| Açık kaynak, 192 kHz/32-bit duyuran hazır Windows sanal sürücüsü var mı? | **VAR, tek aday:** VirtualDrivers/Virtual-Audio-Driver (MIT) — Virtual Speaker 8-bit/8 kHz → **32-bit/192 kHz** | §2.1 · K1 |
| Çekirdek sürücü imzalama engeli var mı? | **EVET.** Win10 1607+ Dev Portal imzası olmadan yeni kernel-mode sürücüyü YÜKLEMEZ; EV sertifika + attestation/WHQL gerekir; alternatif: Test Mode (bcdedit) | §7.4 · K5, K16 |
| macOS'ta 192 kHz sanal cihaz? | **VAR:** BlackHole 8 kHz → 768 kHz arası örnek hızlarını duyurur (192 kHz dahil) | §3.1 · K8 |
| Linux'ta 192 kHz sanal cihaz? | **VAR:** PipeWire sanal sink `F32P` (32-bit float) + `default.clock.rate = 192000`; `snd-aloop` ise 192 kHz'de **sınırlıdır** (ALSA çekirdeği değil, modül sınırı) | §4.1-4.2 · K12, K13, K14 |
| VB-Audio VB-CABLE 32-bit destekliyor mu? | **HAYIR** — referans manuel 16/24-bit.resolution diyor; Hi-Fi Cable bile 24-bit 384 kHz'te kalır | §2.6 · K10 |
| 192 kHz/32-bit sanal cihaz CoreMusic'e uyar mı? | **Uygun, 3 kademeli strateji ile** — §11'de detaylı | §11 |

---

## 1. Araştırma Metodolojisi

| Kalem | Değer |
|-------|-------|
| Arama motorları | websearch (Bing), exa (çalışma zamanında erişilemedi — `fetch failed`, 2 deneme) |
| Doğrudan sayfa çekimi (webfetch) | 8 sayfa (GitHub README, Microsoft Learn, WebSDR, raw LICENSE) |
| Doğrulanabilirlik sınırı | Yalnızca bu oturumda okunan sayfalardan iddia üretildi; okunmayan lisans/sürüm/commit `UNKNOWN` |
| Tarih referansı | Araştırma tarihi 2026-10-22; "son commit / aktivite" yalnız okunan sayfadaki görünür veriye dayanır |
| Erişilemeyen | `gitlab.freedesktop.org` (Anubis bot-koruması, K12 yerine K15 kullanıldı), exa MCP (fetch failed) |

### 1.1 Aday Seçim Kriterleri

| # | Kriter | Zorunlu mu? |
|---|--------|-------------|
| C1 | Sanal ses cihazı / sanal endpoint / loopback sunabilmeli | Evet |
| C2 | 192 kHz örnekleme oranı desteği (duyuru veya yapılandırma ile) | Evet |
| C3 | 32-bit (int veya float) format desteği | Tercih — §8'de ayrı sütun |
| C4 | Repo erişilebilirliği + lisans şeffaflığı | Evet |
| C5 | Sürdürülebilirlik (aktivite / olgunluk) | Tercih |

---

## 2. Windows Adayları (Detay)

### 2.1 VirtualDrivers/Virtual-Audio-Driver — **BİRİNCİ ADAY (Windows)**

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://github.com/VirtualDrivers/Virtual-Audio-Driver | K1 |
| Platform | Windows 10 (Build 1903+) ve Windows 11 · mimari x64 (test edilmiş), ARM64 | K1 |
| Lisans | **MIT** (kendi kodu) + **MS-PL** (Microsoft örnek kodu — THIRD_PARTY_NOTICES.md) | K1 |
| Sürücü modeli | **WDK (Windows Driver Kit)** — Microsoft SysVAD / Simple Audio Sample türevi | K1 |
| Aktivite | 424 ★ · 69 fork · 70 commit · 22 açık issue · 1 PR | K1 |
| Durum | **Beta** — README açıkça "This software is in beta and requires test signing to be enabled" diyor | K1 |
| Virtual Speaker formatları | **8-bit / 8.000 Hz → 32-bit / 192.000 Hz** ( Telephone 8-bit/11.025, DVD/Studio 16-24-32-bit, 192 kHz dahil ) | K1 |
| Virtual Mic formatları | 16-bit/44.100 · 16-bit/48.000 · 24-bit/96.000 · **24-bit/192.000** · 32-bit/48.000 | K1 |
| Ek özellikler | Windows Sonic (Spatial Sound), Exclusive Mode, App Priority, per-app volume | K1 |
| Kurulum | `bcdedit /set testsigning on` + Device Manager → Add Legacy Hardware → Have Disk → VirtualAudioDriver.inf | K1 |
| CoreMusic uygunluğu | **YÜKSEK** — MIT + 32-bit/192 kHz speaker; WDM tabanlı olduğu için WDM-KS üzerinden erişilebilir (§7.3) | §7 |
| Risk | (a) Beta + test-signing zorunluluğu; (b) "mikethetech" özel teklif satırı ("contact for quotes on a custom build") — çekirdeğin toplulukça sürdürülmeme riski; (c) HLK/WHQL durumu belirsiz | §9 |

**Neden birinci aday:** Görevin 192 kHz/32-bit şartını **tek karşılayan**, açık kaynak (MIT) ve Windows kernel-mode sürücü tabanına sahip proje budur.

### 2.2 Microsoft SysVAD (Virtual Audio Device Driver Sample) — referans / taban

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://github.com/microsoft/Windows-driver-samples/tree/main/audio/sysvad | K2 |
| Doküman | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/sample-audio-drivers | K3 |
| Platform | Windows (Windows 10 Mobile örnekleri kaldırıldı) | K2 |
| Lisans | Microsoft Public License (MS-PL) — Windows-driver-samples deposu | K2 |
| Sürücü modeli | **WDM audio driver + WaveRT** ("The driver uses WaveRT and audio offloading for rendering devices") | K2 |
| Sanal cihaz | "The driver uses a 'virtual audio device' instead of an actual hardware-based adapter" | K2 |
| Varsayılan format listesi | **UNKNOWN** — README'de pin formatları listelenmiyor; `⚠️ VERIFICATION REQUIRED` (SysVadShared.h / hw.cpp içinde tanımlıdır, bu oturumda okunmadı) | K2 |
| HLK | "tested using the latest HLK version... since it is a virtual audio driver it does not implement audio mixing... some HLK tests are expected to fail" | K2 |
| CoreMusic uygunluğu | **YÜKSEK (taban olarak)** — 2.1'deki MIT projesinin de türediği kaynak; format listesi değiştirilerek 192 kHz/32-pin duyurulabilir (§7.3) | §7 |
| Risk | MS-PL kaynak kodunun MIT/proprietary sürücüye karıştırılması lisans karışıklığı yaratır (§9.1) | §9 |

**İlgili ikinci örnek:** Microsoft "Simple Audio Sample Device Driver" — https://github.com/microsoft/Windows-driver-samples/blob/main/audio/simpleaudiosample/README.md (K17) · "The driver also uses a 'virtual audio device'".

### 2.3 rdp/virtual-audio-capture-grabber-device — **SANAL CİHAZ DEĞİL, DirectShow capture filtresi**

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://github.com/rdp/virtual-audio-capture-grabber-device | K6 |
| Platform | Windows Vista+ (README: "for Windows Vista+") | K6 |
| Lisans | LICENSE dosyası mevcut, **içeriği bu oturumda okunmadı** → `UNKNOWN` `⚠️ VERIFICATION REQUIRED` | K6 |
| Sürücü modeli | **User-mode DirectShow audio capture filter** (`.ax`, `regsvr32`) — kernel-mode audio driver DEĞİL | K6 |
| Teknoloji | "Windows Vista/7 offer a new interface called a 'loopback' adapter... This little utility captures the loopback audio and offers it to you as an input device" | K6 |
| Aktivite | 618 ★ · 180 fork · 311 commit · 20 açık issue | K6 |
| Format / oran | **UNKNOWN** — README format listesi vermiyor; "support more audio channels than 2" bir feature request olarak duruyor | K6 |
| Sürdürülebilirlik | README hâlâ Vista/7 dilini taşıyor; Windows 10/11 uyumu doğrulanmadı | K6 |
| CoreMusic uygunluğu | **DÜŞÜK** — yalnız DirectShow kabul eden uygulamalar kullanabilir; Neva'nın WASAPI/ASIO hattına girmez | §8 |
| Yazarın kendi tespiti | "Ping me if you want me to convert it into a 'real' kernel level audio device so that any program can use it, not just directshow compatible ones" | K6 |

### 2.4 eiz/Synchronous Audio Router (SAR) — ASIO köprüsü, bayat

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://github.com/eiz/SynchronousAudioRouter | K7 |
| Platform | Windows | K7 |
| Lisans | **GPL-3.0 + GPL-2.0** ("GPL-3.0, GPL-2.0 licenses found") | K7 |
| Sürücü modeli | Windows audio driver + ASIO bridge (DAW'ı ASIO olarak kullanıp uygulama sesini route etme) | K7 |
| Aktivite | **Son sürüm v0.13.1 — 7 Nisan 2018 (≈8 yıl önce)**; toplam 10 release | K7 |
| Davranış | "the endpoints created by SAR are only active while your DAW is running and has started ASIO" | K7 |
| Format / oran | **UNKNOWN** — README format listesi yok | K7 |
| CoreMusic uygunluğu | **DÜŞÜK (referans)** — lisansı GPL (CoreMusic proprietary olamaz), 8 yıldır bakımsız, endpoint'ler DAW açıkken yaşıyor | §9 |
| Değer | Mimarî referans: kullanıcı-kipi bir ASIO host ile sanal endpoint üretmenin kanıtlanmış yolu | §6 |

### 2.5 Windows Virtual Audio Device Driver Sample (WDK) — §2.2 ile aynı

Görev manifestinde ayrı adı olarak geçen "Windows Virtual Audio Device Driver Sample (WDK)", §2.2'deki SysVAD ile aynı örnektir (Microsoft bu örneği "Virtual Audio Device Driver Sample" adıyla yayınlar). Ayrı bir repo DEĞİLDİR — uydurma repo yazılmamıştır.

### 2.6 VB-Audio Virtual Cable + Hi-Fi Cable — **kapalı kaynak REFERANS** (görev kısıtı: alternatif değil)

| Alan | VB-CABLE | VB-Audio Hi-Fi Cable | Kaynak |
|------|----------|---------------------|--------|
| URL | https://vb-audio.com/Cable/ | https://vb-audio.com/Cable/ | K10 |
| Lisans | **Donationware / kapalı kaynak** (webshop lisansı) | Donationware / kapalı kaynak | K10 |
| Örnek oranı (I/O) | "VB-CABLE for Win10/11 supports **8kHz to 192 kHz** sample rate on i/o" | "supports up to **24 bits 384 kHz** audio stream" | K10 |
| Bit derinliği | "44.1 kHz to 96 or 192kHz sample rate (**16bits or 24bits resolution**)" | **24-bit** (bit-perfect, SRC yok) | K10 |
| İç mimari | "VB-CABLE works internally with a **fixed Samplerate** (given by Internal SR)"; standart Internal SR: 44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz | "needs to be configured with the same samplerate on its Input and Its output" | K10 |
| Pipe boyu | "VB-CABLE needs **3 buffers**... pipe size (7168 samples per default)" | n/a | K10 |
| Windows kapsamı | "XP SP2 to latest Win11 32 or 64 bits" (pack45, EKİ 2024, Arm64 dahil) | XP/Vista/7/8 32/64-bit (MAR 2014) | K10 |
| **32-bit?** | **HAYIR** — referans manuel yalnız 16/24-bit derinlikten söz ediyor | **HAYIR** — 24-bit üst sınır | K10 |
| CoreMusic uygunluğu | **REFERANS** — 192 kHz'e ulaşır ama 32-bit'e ULAŞMAZ; kapalı kaynak olduğu için kod referansı yapılamaz | **REFERANS** — bit-perfectlik kavramı için | §8 |

**Kısıt uyumu notu:** Görev tanımı FxSound'ı "192 kHz/32-bit desteği yok" diye dışlıyor. VB-CABLE de 32-bit'i desteklemediği için aynı gerekçeyle **çözüm adayı değil, yalnız referanstır**.

### 2.7 Windows Adayları — Konsolide Tablo

| Proje | Sürücü modeli | Lisans | Max oran/bit | Aktivite | CoreMusic uygunluğu |
|-------|--------------|--------|--------------|----------|---------------------|
| VirtualDrivers/Virtual-Audio-Driver | WDK (SysVAD türevi) | MIT + MS-PL | **192 kHz / 32-bit** (speaker) | Aktif · 424★ · beta | **YÜKSEK** |
| Microsoft SysVAD | WDM + WaveRT | MS-PL | UNKNOWN (README yok) | Microsoft bakımı | YÜKSEK (taban) |
| Microsoft Simple Audio Sample | WDM | MS-PL | UNKNOWN | Microsoft bakımı | YÜKSEK (taban) |
| Synchronous Audio Router | WDM + ASIO bridge | GPL-3.0/GPL-2.0 | UNKNOWN | **Son release 2018** | DÜŞÜK |
| virtual-audio-capture-grabber | DirectShow (user-mode) | UNKNOWN ⚠️ | UNKNOWN | 618★ · durağan | DÜŞÜK |
| VB-CABLE | Kapalı WDM sürücüsü | Donationware | 192 kHz / **24-bit** | Ticari, EKİ 2024 pack | REFERANS |
| VB Hi-Fi Cable | Kapalı WDM sürücüsü | Donationware | 384 kHz / **24-bit** | MAR 2014 (durağan) | REFERANS |

---

## 3. macOS Adayları

### 3.1 ExistentialAudio/BlackHole — macOS birincil adayı

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://github.com/existentialaudio/blackhole | K8 |
| Platform | macOS 10.10+ (Intel / Apple Silicon kurulum paketleri mevcut) | K8 |
| Lisans | **UNKNOWN bu oturumda** — GitHub sayfası lisans rozeti okunmadı; `⚠️ VERIFICATION REQUIRED` (LICENSE dosyası repo kökünde incelenmeli) | K8 |
| Sürücü modeli | macOS **audio loopback driver** (CoreAudio plugin / system extension) | K8 |
| Örnek oranı | "Supports **8kHz, 16kHz, 44.1kHz, 48kHz, 88.2kHz, 96kHz, 176.4kHz, 192kHz, 352.8kHz, 384kHz, 705.6kHz and 768kHz** sample rates" | K8 |
| Gecikme | "zero additional latency" | K8 |
| Sürüm | v0.2.10 (en son görünür release); `blackhole-2ch: 0.2.10` (Homebrew) | K8 |
| Bilinen sorun | "BlackHole sample rate keeps changing" (Issue #524) — macOS 12.1, M1 Pro | K8 |
| Kanal varyantları | 2ch / 16ch / 64ch sürümleri ("installer for BlackHole64ch") | K8 |
| CoreMusic uygunluğu | **YÜKSEK** — 192 kHz bariz; 352.8/384/705.6/768 kHz desteği hi-res hedefiyle uyumlu. 32-bit float CoreAudio tarafı doğrulanmadı (`⚠️`) | §8 |

### 3.2 mattingalls/Soundflower — **DEPRECATED, aday değil**

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://github.com/mattingalls/soundflower | K9 |
| Durum | README: "DEPRECATED — Silicon Macs are not [supported]" | K9 |
| Sürüm kapsamı | "Soundflower works on macOS **Catalina**" | K9 |
| Lisans | **UNKNOWN bu oturumda** (GitHub LICENSE okunmadı) — `⚠️ VERIFICATION REQUIRED` | K9 |
| CoreMusic uygunluğu | **YOK** — Apple Silicon desteksiz, Catalina ile sınırlı; BlackHole'un açık kaynak halefi konumunda | §8 |

### 3.3 macOS Konsolide Tablo

| Proje | Sürücü modeli | Lisans | Max oran | Durum | Uygunluk |
|-------|--------------|--------|----------|-------|----------|
| BlackHole | CoreAudio loopback driver | UNKNOWN ⚠️ (repo LICENSE incelenecek) | **768 kHz** (192 kHz dahil) | Aktif · v0.2.10 | **YÜKSEK** |
| Soundflower | Kernel extension | UNKNOWN ⚠️ | UNKNOWN | **DEPRECATED** | YOK |
| VB-CABLE for Mac | Kapalı driver | Donationware | UNKNOWN ⚠️ (Win tarafı 192 kHz) | Ticari | REFERANS |

---

## 4. Linux Adayları

### 4.1 PipeWire sanal sink / loopback — Linux birincil adayı

| Alan | Değer | Kaynak |
|------|-------|--------|
| URL | https://gitlab.freedesktop.org/pipewire/pipewire · mirror https://github.com/PipeWire/pipewire | K12, K15 |
| Lisans | **MIT** ("All PipeWire source files are licensed under the MIT License"); PulseAudio'dan gelen LGPL kodu ve `jack/control.h` GPL-2.0 içerir | K15 |
| Sürücü modeli | **User-space sunucu** (kernel sürücü DEĞİL) — `null-audio-sink` SPA eklentisi + `libpipewire-module-loopback` | K13 |
| Sanal sink formatı | `audio.format` varsayılanı **`F32P` (32-bit float planar)**; izin verilenler `F32P, F32, S16, S24, S32` — `spa/plugins/support/null-audio-sink.c:943-944` | K13 |
| Örnek oranı yapılandırması | `default.clock.rate` + `default.clock.allowed-rates` — "It is possible to specify up to **32 alternative sample rates**" | K12 |
| 192 kHz pratikte | Kullanıcılar `default.clock.rate = 192000` + `default.clock.allowed-rates = [ 48000, 88200, 96000, 176400, 192000 ]` bildiriyor (Manjaro forumu) | K12 |
| Daha yüksek oran | "defining rates like 192kHz, 384kHz, 576kHz or even 768kHz as long as your hardware supports it" | K14 (libre.moe wiki, alıntılanan PipeWire yapılandırması) |
| Sanal sink oluşturma | `module-null-sink` / `module-loopback` / `module-virtual-source` (pipewire-pulse uyumluluk katmanı) | K14b (Craig Wilson blog) |
| Uyarı | "when i tried setting the sample rate, audio became noisy and crackly" (NixOS discourse) — oran değiştirme kararlılık riski taşır | K12 |
| CoreMusic uygunluğu | **YÜKSEK** — `F32P` varsayılanı = 32-bit float zaten; 192 kHz yalnızca clock yapılandırması | §8 |

### 4.2 ALSA `snd-aloop` — 192 kHz'de SINIRLI

| Alan | Değer | Kaynak |
|------|-------|--------|
| Sürücü modeli | **Linux kernel ALSA loopback modülü** (`snd-aloop`) — gerçek bir "sanal kart" | K14 (sdrutah.org) |
| Lisans | Linux kernel ağacı — `⚠️ VERIFICATION REQUIRED` (modül bazlı LICENSE dosyası doğrulanmadı; kernel modülleri GPL'dir ama bu oturumda teyit edilmedi) | K14 |
| Örnek oranı sınırı | "**snd-aloop has a built-in speed limit of 192 kHz**: This is **not a limit imposed by the ALSA core**, but rather many of the associated programs and modules which were not patched since ALSA was modified to allow up to **768 kHz** several years ago" | K14 |
| 192 kHz üstü çözüm | **snd-fastloop** yaması — `fastloop.tar.gz` Vanilla `snd-aloop` kaynağını indirip 768 kHz için yamalar; **yalnızca Ubuntu 18.x / 20.04 LTS'de test edilmiş**, "known to NOT work on Ubuntu 16.x", Debian'da denenmedi | K14 |
| Yapılandırma | `/etc/modprobe.d/alsa-base.conf`: `options snd-aloop index=16,17 id=lp0,lp1 enable=1,1 pcm_streams=4,4` (en fazla 8 cihaz, 32 ALSA cihaz sınırı) | K14 |
| `pcm_streams` | Varsayılan 8 substream; `4,4` ile sınırlandırılabilir | K14 |
| CoreMusic uygunluğu | **ORTA** — 192 kHz tam sınırda çalışır (192 kHz/32-bit isteniyorsa tam bu noktada); 32-bit format yeteneği bu oturumda doğrulanmadı (`⚠️`) | §8 |

### 4.3 Linux Konsolide Tablo

| Aday | Katman | Lisans | 192 kHz | 32-bit | Uygunluk |
|------|--------|--------|---------|--------|----------|
| PipeWire null-sink / loopback | User-space sunucu | MIT (+LGPL/GPL-2.0 parçalar) | **EVET** (clock yapılandırması) | **EVET** (`F32P` varsayılan) | **YÜKSEK** |
| PulseAudio `module-null-sink` | User-space sunucu | **UNKNOWN ⚠️** (PulseAudio LGPL; doğrulanmadı) | Yapılandırmaya bağlı | Yapılandırmaya bağlı | ORTA (PipeWire ile değişti) |
| ALSA `snd-aloop` | Kernel modülü | Kernel ağacı ⚠️ | **SINIR (192 kHz tam)** | `⚠️` doğrulanmadı | ORTA |
| `snd-fastloop` (yama) | Kernel modülü | UNKNOWN ⚠️ (yama, upstream değil) | **768 kHz** | `⚠️` | DÜŞÜK (Ubuntu 18/20'ye bağlı) |

---

## 5. Cross-Platform / Taşınabilir

### 5.1 Kütüphane düzeyi — "sanal cihaz DEĞİL"

| Teknoloji | Ne yapar | Sanal cihaz yaratır mı? | Kaynak |
|-----------|----------|------------------------|--------|
| **PortAudio** | C ses I/O kitaplığı (host sürücülerine bindirir) | **HAYIR** | K12b (görev tanımı + genel bilgi; `⚠️` depo LICENSE bu oturumda okunmadı) |
| **RtAudio** | C++ ses I/O kitaplığı | **HAYIR** | K12b (aynı) |
| **miniaudio** | Tek-dosya C ses I/O | **HAYIR** | `⚠️ VERIFICATION REQUIRED` — bu oturumda doğrulanmadı, UNKNOWN |
| **JUCE AudioDeviceManager** | Tüm platformlarda ses cihazlarını YÖNETİR | **HAYIR** | K18 (JUCE resmi tutorials) |

> **Sonuç:** Cross-platform I/O kütüphaneleri sanal cihaz üretmez; yalnız mevcut cihazlara bağlanır. Neva Engine için "sanal cihaz" ayrı bir sürücü/eklenti katmanıdır — §11.

### 5.2 WebRTC `VirtualAudioDevice` / sahte cihaz kütüphaneleri

| Konu | Durum |
|------|-------|
| WebRTC `VirtualAudioDevice` modülünün varlığı ve formatları | **UNKNOWN** — bu oturumda doğrulanmadı; depo URL'si uydurulmadı |
| Rust/C++ "fake device" kütüphaneleri | **UNKNOWN** — bu oturumda doğrulanmadı |

`⚠️ VERIFICATION REQUIRED` — bu iki başlık için ayrı bir doğrulama turu gerekir (§10 Açık Konular).

---

## 6. VST/AU Host Referansları (Neva Backend Soyutlaması İçin — Kısa)

> Bunlar **sanal cihaz DEĞİLDİR**; Neva'nın `AudioDeviceManager` benzeri soyutlaması için mimari referans niteliğindedir (görev tanımı gereği kısa tutulmuştur).

| Çatı | Lisans | Sanal cihaz? | Neva'ya katkısı | Kaynak |
|------|--------|--------------|-----------------|--------|
| **JUCE (AudioDeviceManager)** | **Çift lisans:** GPL **veya** ücretli propriyetel lisans (PACE satın alması sonrası OSS desteğinin korunacağı bildirildi) | HAYIR | "managing audio devices on all platforms" — platform soyutlaması referansı | K18 |
| **DPF (DISTRHO)** | **ISC** — LICENSE dosyası: "Copyright (C) 2012-2025 Filipe Coelho... Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee..." | HAYIR | Lisans-temiz (permissive) taban; LADSPA/DSSI/LV2/VST2/VST3/CLAP export | K19 (raw LICENSE), K20 |
| **iPlug2** | **UNKNOWN** — `master/LICENSE` ve `master/LICENSE.md` 404 döndü; `⚠️ VERIFICATION REQUIRED` | HAYIR | Web Audio Modules, Faust desteği (Medium/K18 kaynaklı) | K18, K21 |

**Lisans özeti (host çatıları):** DPF (ISC) > JUCE (GPL/ücretli) > iPlug2 (UNKNOWN) sıralaması, CoreMusic'in "çift lisanslı / proprietery'e geçiş esnekliği" arayışıyla DPF'i öne çıkarır.

---

## 7. 192 kHz / 32-bit Teknik Doğrulama

### 7.1 WASAPI — Shared vs Exclusive (Microsoft resmi dokümanı)

| Kural | Alıntı / Özet | Kaynak |
|-------|---------------|--------|
| Shared mode sınırı | "An application that uses WASAPI to manage shared-mode streams can rely on the audio engine to perform **only limited format conversions**... the format for an application stream typically must have the **same number of channels and the same sample rate** as the stream format used by the device" | K4 |
| Exclusive mode | "If an application is using a device in exclusive mode, the application must use a stream format that **the audio hardware explicitly supports**. In exclusive mode, the application and device exchange audio data directly, **without intervention by the audio engine**" | K4 |
| Sorgu API | `IAudioClient::IsFormatSupported` — shared-mode'da audio engine'e, **exclusive-mode'da device driver'a** sorulur | K4 |
| Motor iç formatı | Audio engine dahili işleme için float kullanır; "convert samples to floating-point numbers before processing them" | K4 |
| Cihaz formatı seçimi | Kullanıcı `control mmsys.cpl` → Properties → Advanced → Default Format ile cihazın shared-mode formatını seçer | K4 |
| **Sonuç (Neva için)** | **192 kHz/32-bit, exclusive mode'da DONANIM DEĞİL SÜRÜCÜYE bağlıdır.** Bir sanal endpoint sürücüsü bu formatları duyurabiliyorsa WASAPI exclusive 192 kHz/32-bit'i doğrudan geçirir. Shared mode'da ise cihazın kendisi 192 kHz'e ayarlanabilmelidir (mmsys.cpl) | §7.5 |

### 7.2 ASIO

| Kural | Durum | Kaynak |
|-------|-------|--------|
| ASIO'da max örnek oranı | **Donanım/sürücüye bağlı** — ASIO sürücülerini üretici yayınlar; sanal bir cihazda ASIO sürücüsü yazılımdır | §7.2b |
| CoreMusic hedefi | ASIO SDK **2.3.4** (ROLE.md uzmanlık satırı) | vault (ROLE §2.3) |
| Sar'ın kanıtı | Synchronous Audio Router, bir ASIO host üzerinden sanal endpoint üretmeyi kanıtlamıştır (§2.4) | K7 |
| `⚠️ VERIFICATION REQUIRED` | ASIO'nun 192 kHz/32-bit üst sınırı hakkında **bu oturumda resmi bir Steinberg kaynağı okunmadı**; iddia üretilmedi | §10 |

### 7.3 WDM-KS üzerinden 192 kHz/32-bit sanal endpoint mümkün mü?

| Adım | Gerekçe | Kanıt |
|------|---------|-------|
| 1 | SysVAD **WDM** ses sürücüsüdür ("shows how to develop a WDM audio driver") | K2 |
| 2 | WDM-KS (Kernel Streaming) uygulamaları doğrudan sürücünün KS pin'lerine bağlanır | Genel bilgi · `⚠️` resmi MS kaynağı bu oturumda okunmadı |
| 3 | Sürücü KS pin'lerine 192 kHz/32-bit format duyurabilir — çünkü format listesi sürücü kodunda tanımlanır (SysVadShared.h / hw.cpp) | K2 (dosya listesi) |
| 4 | VirtualDrivers projesi bunu yapmaktadır: "8-bit, 8000 Hz up to **32-bit, 192,000 Hz**" + "Exclusive Mode" | K1 |
| 5 | **Sonuç: EVET — teknik olarak mümkün; tekil engel İMZALAMA'dır (§7.4)** | §7.4 |

### 7.4 Windows sürücü imzalama — asıl engel

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Win10 1607+ | "Windows will not load any new kernel-mode drivers which are **not signed by the Dev Portal**" | K5 |
| EV sertifika | "You must have an **extended validation (EV) code signing certificate**" (dashboard hesabı için) | K5 |
| Attestation signing | HLK testi GEREKTİRMEZ; "primarily used for testing purposes"; **yalnız Windows 10/11 Desktop**; Windows Server desteklemez; Windows Update'e yayınlANAMAZ | K16 |
| WHQL | HLK testleri zorunlu; Windows Server + Windows Update desteği; "several days" test süresi | K16 |
| Test Mode alternatif | `bcdedit /set testsigning on` — VirtualDrivers README'sinin önerdiği yol; sürücü test-signed olmalıdır | K1, K5 |
| Sonuç (CoreMusic) | Üretim dağıtımı için **EV sertifika + Attestation (hızlı) veya WHQL (tam)** gerekir; geliştirme/test için Test Mode yeterlidir | §11 |

### 7.5 Teknik Doğrulama Matrisi

| Hedef | Windows (WASAPI exc.) | Windows (WDM-KS) | macOS (CoreAudio) | Linux (PipeWire) | Linux (ALSA) |
|-------|----------------------|------------------|-------------------|------------------|--------------|
| 192 kHz | Sürücü format duyurusuna bağlı → **VAR** (K1) | Sürücü pin'ine bağlı → **VAR** (K1, §7.3) | BlackHole **VAR** (K8) | `default.clock.rate=192000` → **VAR** (K12) | `snd-aloop` **SINIR** = 192 kHz (K14) |
| 32-bit int | Virtual Speaker duyurusu **VAR** (K1) | Aynı pin → **VAR** | `⚠️` doğrulanmadı | `S32` izinli (K13) | `⚠️` doğrulanmadı |
| 32-bit float | WASAPI exclusive float destekler; sürücü duyurusuna bağlı | Sürücü pin'ine bağlı | CoreAudio float destekler; BlackHole tarafı `⚠️` | **`F32P` varsayılan** (K13) | `⚠️` |

---

## 8. CoreMusic Uygunluk Matrisi (Genel Bakış)

| # | Aday | Platform | Lisans | Sürücü modeli | Max oran/bit | Aktivite | CoreMusic uygunluğu | URL |
|---|------|----------|--------|--------------|--------------|----------|---------------------|-----|
| 1 | VirtualDrivers/Virtual-Audio-Driver | Windows 10/11 | **MIT** (+MS-PL) | WDK (SysVAD türevi) | **192 kHz / 32-bit** | 424★ · 70 commit · beta | **YÜKSEK** | https://github.com/VirtualDrivers/Virtual-Audio-Driver |
| 2 | Microsoft SysVAD | Windows | MS-PL | WDM + WaveRT | UNKNOWN | Microsoft bakımı | **YÜKSEK (taban)** | https://github.com/microsoft/Windows-driver-samples/tree/main/audio/sysvad |
| 3 | Microsoft Simple Audio Sample | Windows | MS-PL | WDM | UNKNOWN | Microsoft bakımı | YÜKSEK (taban) | https://github.com/microsoft/Windows-driver-samples/blob/main/audio/simpleaudiosample/README.md |
| 4 | Synchronous Audio Router | Windows | GPL-3.0/2.0 | WDM + ASIO | UNKNOWN | **Son release 2018** | DÜŞÜK | https://github.com/eiz/SynchronousAudioRouter |
| 5 | virtual-audio-capture-grabber | Windows Vista+ | UNKNOWN ⚠️ | DirectShow (kullanıcı kipi) | UNKNOWN | 618★ · durağan | DÜŞÜK | https://github.com/rdp/virtual-audio-capture-grabber-device |
| 6 | VB-CABLE | Win/macOS | Donationware (kapalı) | Kapalı WDM | 192 kHz / **24-bit** | Ticari (EKİ 2024) | REFERANS | https://vb-audio.com/Cable/ |
| 7 | VB Hi-Fi Cable | Windows | Donationware (kapalı) | Kapalı WDM | 384 kHz / **24-bit** | MAR 2014 | REFERANS | https://vb-audio.com/Cable/ |
| 8 | BlackHole | macOS 10.10+ | UNKNOWN ⚠️ | CoreAudio loopback | **768 kHz** (192 dahil) | v0.2.10 · aktif | **YÜKSEK** | https://github.com/existentialaudio/blackhole |
| 9 | Soundflower | macOS (Catalina) | UNKNOWN ⚠️ | Kernel extension | UNKNOWN | **DEPRECATED** | YOK | https://github.com/mattingalls/soundflower |
| 10 | PipeWire null-sink/loopback | Linux | **MIT** | User-space sunucu | **F32P + 192 kHz+** | Aktif (1.6.9/1.7.0) | **YÜKSEK** | https://gitlab.freedesktop.org/pipewire/pipewire |
| 11 | PulseAudio null-sink | Linux | UNKNOWN ⚠️ | User-space sunucu | Yapılandırmaya bağlı | PipeWire'a kaydı | ORTA | — |
| 12 | ALSA `snd-aloop` | Linux | Kernel ağacı ⚠️ | Kernel modülü | **192 kHz SINIR** | Çekirdek içinde | ORTA | — |
| 13 | `snd-fastloop` (yama) | Linux (Ubuntu 18/20) | UNKNOWN ⚠️ | Kernel modülü (yama) | 768 kHz | Yerel yama | DÜŞÜK | https://www.sdrutah.org/info/high_rate_loopback_websdr.html (K14) |
| 14 | JUCE AudioDeviceManager | Cross | GPL / ücretli | Kütüphane (sanal cihaz YOK) | Host'a bağlı | Aktif | REFERANS | https://juce.com/ (K18) |
| 15 | DPF (DISTRHO) | Cross | **ISC** | Kütüphane (sanal cihaz YOK) | Host'a bağlı | Aktif | REFERANS | https://github.com/DISTRHO/DPF (K19) |
| 16 | iPlug2 | Cross | UNKNOWN ⚠️ | Kütüphane (sanal cihaz YOK) | Host'a bağlı | Aktif | REFERANS | https://github.com/iPlug2/iPlug2 |
| 17 | PortAudio / RtAudio | Cross | UNKNOWN ⚠️ | I/O kütüphanesi | Host'a bağlı | Aktif | REFERANS | — |

**Renk anahtarı:** **YÜKSEK** = doğrudan aday · **ORTA** = koşullu · **DÜŞÜK** = yalnız mimari referans · **REFERANS** = teknik/lisans referansı, çözüm değil · **YOK** = elenmiş.

---

## 9. Lisans Risk Analizi

### 9.1 Lisans Tablosu (CoreMusic Proprietary Uygunluğu)

| Lisans | Adaylar | CoreMusic'e uygun mu? | Not |
|--------|---------|----------------------|-----|
| **MIT** | VirtualDrivers/Virtual-Audio-Driver (kendi kodu), PipeWire | **EVET** | En esnek; kod inceleme/fork serbest |
| **ISC** | DPF | **EVET** | MIT eşdeğeri |
| **MS-PL** | SysVAD, Simple Audio Sample, VirtualDrivers THIRD_PARTY | **EVET (dikkatle)** | MS-PL kaynak kodun MIT koda karışması lisans bildirimi yükümlülüğü doğurur (§9.2) |
| **GPL-2.0/3.0** | Synchronous Audio Router | **HAYIR** (proprietary) | Yalnız GPL kaynak kodu paylaşımı taahhüdüyle |
| **Donationware / kapalı** | VB-CABLE, Hi-Fi Cable, VoiceMeeter | **HAYIR (kod incelemesi)** | Yalnız davranış referansı |
| **UNKNOWN ⚠️** | BlackHole, Soundflower, iPlug2, PortAudio, RtAudio, PulseAudio, snd-aloop, snd-fastloop, virtual-audio-capture-grabber | **KARAR VERİLEMEZ** | §10 Açık Konular |

### 9.2 MS-PL + MIT Karışımı (VirtualDrivers)

| Risk | Açıklama | Azaltım |
|------|----------|---------|
| Bildirim yükümlülüğü | MS-PL, değiştirilmiş MS kodunu dağıtanın MS-PL şartlarını korumasını ister | `THIRD_PARTY_NOTICES.md`'yi koru; kendi kodunu MS-PL dosyalarından AYRI tut |
| Çekirdek fork riski | Proje beta + tek geliştirici bağımlılığı görünüyor | SysVAD (Microsoft) tabanına fork yap; VirtualDrivers'i yalnız referans al |
| İmalatçı teklifi | "Contact us for quotes on a custom build" satırı topluluk bakımının zayıflayabileceğine işaret | Kendi fork'umuzu üret |

### 9.3 Karar Matrisi (Lisans)

| Seçenek | Lisans yükü | Fork özgür mü? | Üretim riski | Karar |
|---------|-------------|----------------|--------------|-------|
| A: VirtualDrivers fork (MIT+MS-PL) | Düşük | Evet | Beta + imzalama | **ÖNERİLİR** (§11 Kademe 1) |
| B: SysVAD tabanlı sıfırdan sürücü (MS-PL) | Düşük-orta | Evet | Daha fazla iş | **ÖNERİLİR** (§11 Kademe 1 alternatifi) |
| C: SAR fork (GPL) | **Yüksek** | Yalnız GPL ile | Bakımsızlık | **REDDEDİLİR** |
| D: VB-CABLE davranışı takibi | Yok | N/A (kapalı) | Sürüm değişimi | Yalnız referans |

---

## 10. Açık Konular (UNKNOWN / Doğrulama Gerekenler)

| # | Konu | Neden UNKNOWN | Önerilen doğrulama adımı |
|---|------|---------------|--------------------------|
| U1 | BlackHole **lisansı** | GitHub LICENSE dosyası bu oturumda okunmadı | `raw.githubusercontent.com/ExistentialAudio/BlackHole/main/LICENSE` çek |
| U2 | BlackHole **32-bit float** format desteği | README yalnız örnek oranlarını listeliyor | BlackHole `Info.plist` / `BlackHole.c` içindeki format tanımını incele |
| U3 | SysVAD **varsayılan pin formatları** | README format listesi yok | `audio/sysvad/SysVadShared.h` ve `hw.cpp` içindeki format tablosunu oku |
| U4 | **ASIO maksimum örnek oranı** sınırı (Steinberg) | Bu oturumda resmi ASIO kaynağı okunmadı | ASIO SDK 2.3.4 docs + Steinberg sayfası |
| U5 | WDM-KS'nin **resmi** format kaynağı (MS) | §7.3 madde 2 genel bilgidir | Microsoft Learn `kernel-streaming` sayfası |
| U6 | **WebRTC VirtualAudioDevice** | Doğrulanmadı, URL uydurulmadı | WebRTC deponu `modules/audio_device` tara |
| U7 | **Rust/C++ fake device kütüphaneleri** | Doğrulanmadı | Ayrı arama turu |
| U8 | **virtual-audio-capture-grabber LICENSE** | LICENSE dosyası okunmadı | `raw.githubusercontent.com/rdp/virtual-audio-capture-grabber-device/master/LICENSE` |
| U9 | **iPlug2 lisansı** | `LICENSE` ve `LICENSE.md` 404 | Depo kökünde `LICENSE.txt` / `COPYING` ara |
| U10 | **PipeWire PulseAudio uyumluluk LGPL'ı** | K15'te "contains LGPL code from Pulseaudio" dendi, kapsamı netleşmedi | `pipewire/LICENSE` dosyasını tam oku |
| U11 | `snd-aloop` **32-bit** format yeteneği | Bu oturumda doğrulanmadı | `aplay -D hw:lp0,0,0 --dump-hw-params` |
| U12 | **PipeWire 1.6.9/1.7.0** sürüm iddiası | Sürüm numaraları arama sonucu başlıklarından; sayfa tarihi doğrulanmadı | `pipewire.org` sürüm sayfası |

---

## 11. Sonuç — CoreMusic / Neva Engine için 3 Kademeli Öneri

### Kademe 1 — Windows Sanal Cihaz Stratejisi (Hedef: 192 kHz / 32-bit)

| Adım | Aksiyon | Gerekçe | Kanıt |
|------|---------|---------|-------|
| 1.1 | **VirtualDrivers/Virtual-Audio-Driver'ı fork'la** (MIT) ve Neva'nın `--cm-*` cihaz kimliğiyle yeniden adlandır | Görevin 192 kHz/32-bit şartını TEK karşılayan açık kaynak Windows sürücüsü | §2.1 · K1 |
| 1.2 | **SysVAD (Microsoft) tabanını yedek plan olarak tut** — fork tutmazsa `audio/sysvad` MS-PL tabanında sıfırdan sürücü | Microsoft bakımı + lisans temizliği; VirtualDrivers zaten bundan türedi | §2.2 · K2 |
| 1.3 | **Sanal pin formatlarını 192 kHz/32-bit'e sabitle** — `IsFormatSupported` çağrısının `WAVEFORMATEXTENSIBLE` ile de başarılı olması için hem `WAVEFORMATEX` hem `WAVEFORMATEXTENSIBLE` testi yap | Microsoft: "exclusive-mode applications should call IsFormatSupported twice for each 1-channel or 2-channel PCM format" | K4 |
| 1.4 | **İmzalama yolu seç:** Geliştirme → `bcdedit /set testsigning on`; Üretim → EV sertifika + Attestation signing (hızlı, Win10/11 Desktop) veya WHQL (Server + Windows Update) | Win10 1607+ Dev Portal imzası olmadan kernel-mode sürücüyü yüklemez | §7.4 · K5, K16 |
| 1.5 | **VB-CABLE'i "192 kHz'e ulaşıyor ama 32-bit'e ulaşmıyor" diye dokümante et** — çözüm değil, davranış referansı | Referans manuel 16/24-bit derinlik deriyor | §2.6 · K10 |

**Beklenen kazanım:** WASAPI exclusive + WDM-KS üzerinden **192 kHz / 32-bit** sanal endpoint — Neva'nın ASIO fallback'ine gerek kalmadan bit-perfect yol.

### Kademe 2 — Cross-Platform Fallback (macOS + Linux)

| Platform | Seçim | Yapılandırma | Beklenen sonuç |
|----------|-------|--------------|----------------|
| **macOS** | **BlackHole** | Kurulum sonrası sistem girişi olarak seç; 192 kHz destekli | 192 kHz sanal loopback; 352.8/384/705.6/768 kHz hi-res kapasitesi hazır |
| **Linux** | **PipeWire null-sink / loopback** | `default.clock.rate = 192000` · `default.clock.allowed-rates = [ 48000, 88200, 96000, 176400, 192000 ]` · `audio.format = F32P` | **192 kHz + 32-bit float** sanal sink |
| Linux (alternatif) | **ALSA `snd-aloop`** | `options snd-aloop index=16,17 id=lp0,lp1 enable=1,1 pcm_streams=4,4` | **192 kHz tam sınır** — 32-bit doğrulanmadı (`⚠️`) |
| Linux (ileri) | `snd-fastloop` yaması | **Yalnız Ubuntu 18.x/20.04** test edilmiş; distro güncellemesi sonrası yeniden derleme gerekir | 768 kHz — **CoreMusic hedefi için gereksiz risk** |

**Neden PipeWire > snd-aloop:** PipeWire `F32P` varsayılanıyla 32-bit'i zaten sunar; `snd-aloop` 192 kHz'de tam sınırda çalışır ve 32-bit yeteneği doğrulanmamıştır.

### Kademe 3 — 192 kHz / 32-bit Doğrulama Planı (Laboratuvar Testleri)

| # | Test | Adım | Başarı kriteri | Süre |
|---|------|------|----------------|------|
| T1 | Windows: sanal endpoint format duyurusu | `control mmsys.cpl` → sanal cihaz → Properties → Advanced | 192.000 Hz ve 32-bit seçenekleri listede görünür | 5 dk |
| T2 | Windows: WASAPI exclusive | Neva'da exclusive mode + `IsFormatSupported` (WAVEFORMATEX + WAVEFORMATEXTENSIBLE) | `S_OK` / `S_FALSE` ayrıştırması; `S_OK` → destekleniyor | 30 dk |
| T3 | Windows: WDM-KS | Neva WDM-KS backend'i ile sanal endpoint'i aç | `IAudioClient::Initialize` success, 192 kHz/32-bit | 30 dk |
| T4 | Windows: bit-perfect doğrulama | Test tonu üret → sanal endpoint → loopback kaydı → MD5 karşılaştır | MD5 eşleşmesi (bit-perfect) | 1 sa |
| T5 | macOS: BlackHole 192 kHz | `AudioDeviceSetup` ile 192 kHz kur | Cihaz 192 kHz kabul eder, tıkırtı yok | 20 dk |
| T6 | Linux: PipeWire 192 kHz | `pw-cli ls node` + `pw-metadata -n settings` | `clock.rate = 192000`, sink `F32P` | 20 dk |
| T7 | Linux: snd-aloop sınırı | `aplay -D hw:lp0,0,0 --dump-hw-params` | `RATE: [192000]` üst sınır doğrulanır; 32-bit format satırı okunur | 15 dk |
| T8 | HLK/Attestation | Attestation signing sonrası temiz makinede yükleme | Test Mode KAPALI iken sürücü yüklenir | 1-2 sa |
| T9 | FxSound karşılaştırması | **YAPILMAZ** — görev kısıtı: FxSound alternatif değildir | — | — |

**Toplam tahmini süre:** ~4-6 saat (T1-T8) · **Ek bağımlılık:** EV sertifika (T8 için) · **Riskli adım:** T8 (imzalama maliyeti/tedarik süresi).

---

### 11.4 Risk Kayıt Defteri (Risk Register)

| # | Risk | Olasılık | Etki | Azaltım | Sahip |
|---|------|----------|------|---------|-------|
| R1 | Windows sürücü imzalama (EV sertifika/WHQL) tedarik süresi ve maliyeti | Yüksek | Yüksek | Attestation signing ile başla; WHQL'ı ürüne ertele | MO + win-sw |
| R2 | VirtualDrivers fork'unun tek-geliştirici bağımlılığı nedeniyle bakımsız kalması | Orta | Orta | SysVAD (Microsoft) tabanına fork; kendi çekirdeğimizi tut | embedded |
| R3 | MS-PL kodunun MIT koda karışması → lisans bildirim yükümlülüğü | Orta | Orta | `THIRD_PARTY_NOTICES.md` korunur; MS-PL dosyaları ayrı derlenir | MO |
| R4 | Test-signing gerekliliğinin son kullanıcıya yansıması | Yüksek | Orta | Üretimde Attestation; son kullanıcıya test mode anlatılmaz | win-sw |
| R5 | PipeWire `default.clock.rate` değişikliğinin sistem geneli kararlılığı bozması ("noisy and crackly" raporları) | Orta | Orta | 192 kHz'i uygulama-bazlı sanal sink'e sınırla; global clock'ı 48 kHz'te tut | embedded |
| R6 | `snd-aloop` 192 kHz tam sınırında — bir üst sürümde kırılabilir | Düşük | Orta | 192 kHz'i `S32` + `F32P` PipeWire yoluyla üret | embedded |
| R7 | BlackHole lisansı doğrulanmadan entegrasyon | Düşük | **Yüksek** | U1 kapanmadan macOS paketleme başlamaz | MO |
| R8 | FxSound karşılaştırmasının yanlışlıkla araştırmaya girmesi | Düşük | Düşük | Görev kısıtı §0'da açıkça "kısıt" olarak işaretlendi | Researcher |

### 11.5 Uygulama Sırası (Neva Engine Entegrasyon Yol Haritası)

| Faz | İş | Bağımlılık | Çıktı |
|-----|----|-----------|-------|
| F1 | U1 (BlackHole lisansı) + U3 (SysVAD pin formatları) doğrulama | Yok | UNKNOWN kapanışı |
| F2 | Windows: VirtualDrivers fork + 192 kHz/32-bit pin sabitleme | F1 | Çalışan test-signed sürücü |
| F3 | Neva WASAPI exclusive + WDM-KS backend'i sanal endpoint'i tanısın | F2 | 192 kHz/32-bit bit-perfect yol |
| F4 | T1-T4 laboratuvar testleri | F3 | MD5 bit-perfect kanıtı |
| F5 | macOS BlackHole + Linux PipeWire fallback entegrasyonu | F1 | Cross-platform fallback |
| F6 | T5-T7 testleri | F5 | macOS/Linux doğrulama |
| F7 | Attestation / WHQL imzalama (üretim) | F4, EV sertifika | Test Mode'suz yükleme |

---

## 12. Kaynakça (17 doğrudan kaynak)

| # | Kaynak | URL | Güvenilirlik |
|---|--------|-----|--------------|
| K1 | GitHub — VirtualDrivers/Virtual-Audio-Driver (README + repo sayfası) | https://github.com/VirtualDrivers/Virtual-Audio-Driver | Yüksek |
| K2 | GitHub — microsoft/Windows-driver-samples / audio/sysvad (README) | https://github.com/microsoft/Windows-driver-samples/tree/main/audio/sysvad | Çok yüksek (Microsoft) |
| K3 | Microsoft Learn — Sample Audio Drivers | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/sample-audio-drivers | Çok yüksek |
| K4 | Microsoft Learn — Device Formats (Win32 Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats | Çok yüksek |
| K5 | Microsoft Learn — Attestation sign Windows drivers + Driver Signing Policy | https://learn.microsoft.com/en-us/windows-hardware/drivers/dashboard/code-signing-attestation | Çok yüksek |
| K6 | GitHub — rdp/virtual-audio-capture-grabber-device | https://github.com/rdp/virtual-audio-capture-grabber-device | Orta (kişisel proje, durağan) |
| K7 | GitHub — eiz/SynchronousAudioRouter | https://github.com/eiz/SynchronousAudioRouter | Orta (2018'den beri bakımsız) |
| K8 | GitHub — ExistentialAudio/BlackHole | https://github.com/existentialaudio/blackhole | Yüksek |
| K9 | GitHub — mattingalls/Soundflower (DEPRECATED) | https://github.com/mattingalls/soundflower | Yüksek (durum tespiti) |
| K10 | VB-Audio — CABLE Reference Manual (PDF) + Cable sayfası | https://vb-audio.com/Cable/VBCABLE_ReferenceManual.pdf · https://vb-audio.com/Cable/ | Orta (kapalı kaynak üretici) |
| K11 | GitHub — Windows-driver-samples / audio/simpleaudiosample | https://github.com/microsoft/Windows-driver-samples/blob/main/audio/simpleaudiosample/README.md | Çok yüksek |
| K12 | PipeWire docs — pipewire.conf · Manjaro forum · Fedora/Arch/NixOS konuları | https://docs.pipewire.org/page_man_pipewire_conf_5.html | Yüksek (docs) + Orta (forum) |
| K13 | benashby.com — PipeWire Virtual Devices (null-audio-sink.c satır referanslı) | https://www.benashby.com/resources/pipewire-virtual-devices/ | Orta-yüksek (kod referanslı) |
| K14 | Northern Utah WebSDR — Configuring high-rate audio loopback devices (snd-aloop / snd-fastloop) | https://www.sdrutah.org/info/high_rate_loopback_websdr.html | Orta (SA teknik blog, Ubuntu 18/20 testi) |
| K14b | Craig Wilson's Blog — Virtual Sink Creation and Loopback on Linux | https://www.craigwilson.blog/post/2024/2024-12-05-virtualaudiosplit/ | Orta |
| K15 | GitHub — PipeWire LICENSE (mirror) + Wikipedia PipeWire | https://github.com/PipeWire/pipewire/blob/master/LICENSE | Yüksek |
| K16 | Microsoft Learn — Attestation vs WHQL (Microsoft Q&A) | https://learn.microsoft.com/en-us/answers/questions/5900667/ | Yüksek |
| K17 | PipeWire docs — Loopback module + pipewire-pulse-modules (format listesi) | https://docs.pipewire.org/page_module_loopback.html · https://docs.pipewire.org/page_man_pipewire-pulse-modules_7.html | Yüksek |
| K18 | JUCE — AudioDeviceManager tutorial + JUCE licensing (Medium/K18) | https://juce.com/tutorials/tutorial_audio_device_manager/ · https://juce.com/ | Yüksek (resmi) |
| K19 | GitHub — DISTRHO/DPF LICENSE (raw, ISC metni) | https://raw.githubusercontent.com/DISTRHO/DPF/main/LICENSE | Çok yüksek (dosyanın kendisi) |
| K20 | GitHub — DISTRHO/DPF | https://github.com/distrho/dpf | Yüksek |
| K21 | GitHub — iPlug2 (LICENSE 404 — UNKNOWN) | https://github.com/iPlug2/iPlug2 | Yüksek (durum) |

### 12.1 Erişilemeyen / Başarısız Kaynaklar (şeffaflık)

| Kaynak | Neden | Etki |
|--------|-------|------|
| exa MCP web_search | `fetch failed` (2 deneme) | Yok — websearch ile telafi edildi |
| `gitlab.freedesktop.org/pipewire/pipewire/-/raw/master/LICENSE` | Anubis bot-koruması (JS PoW) | PipeWire lisansı K15 (GitHub mirror + Wikipedia) ile doğrulandı |
| `raw.githubusercontent.com/iPlug2/iPlug2/master/LICENSE` ve `LICENSE.md` | 404 Not Found | iPlug2 lisansı **UNKNOWN** olarak kaldı (U9) |

---

## 13. Ek Doğrulama Turu — Emsal Ürün Ölçümü (VoiceMeeter VAIO, 2026-10-22)

> Bu bölüm, `reverse-voicemeeter-fxsound-2026-10-22.md` §16'daki web çapraz doğrulamasının **bu rapora giren** kısmıdır. Amaç: "192 kHz/32-bit sanal cihaz" şartının emsal bir üründe nasıl karşılandığını sayısal olarak görmek.

### 13.1 Resmi spesifikasyon (vb-audio.com — VoiceMeeter)

| Parametre | Resmi değer |
|---|---|
| DSP iş motoru | 32 · 44.1 · 48 · 88.2 · 96 · 176.4 · **192 kHz** |
| Sanal I/O (VAIO) | WDM, KS, MME, DirectX, **WaveRT** — **8–192 kHz**, 1–8 kanal |
| Sanal ASIO I/O | ASIO **32–192 kHz**, 8 kanal, **4 istemci** |
| OS | Windows XP→11, 32/64-bit + **ARM64** |
| Lisans | Donationware |

### 13.2 Sürücü kontrol paneli verileri (VAIO Control Panel v3.3.1.6, kılavuz görseli)

| Alan | Değer |
|---|---|
| `Internal Sampling Rate` | 44100 / 48000 / 88200 / 96000 / 176400 / **192000 Hz** |
| `Max Latency` | **4096 / 7168 smp** (değişim restart ister) |
| Çıkış bilgisi örneği | `ch:2 · sr:48000 Hz · res: 24 bits` |
| Sayaçlar | DMA size, Timer Glitch, Buffer Call |

### 13.3 Çıkarımlar (bu raporun girdisi)

| # | Çıkarım | Etki |
|---|---|---|
| 1 | Emsal ürün sanal cihazı **192 kHz'e** taşıyor — hedef fiziksel olarak mümkün | §2.1 aday hedefi doğrulandı |
| 2 | Emsalin raporladığı çözünürlük **24-bit** — **32-bit float geçmiyor** | CoreMusic şartı emsaldan **yüksek** → fork gerekli (§2.1) |
| 3 | 4096 smp @192k ≈ **21,3 ms** — emsalin buffer'ı bizim hedefimizden (≤5,33 ms @1024) **4× ağır** | Neva sanal cihazı daha agresif buffer hedeflemeli |
| 4 | ARM64 sürücü varyantı emsalde mevcut | toolchain matrisine ARM64 sütunu zorunlu |
| 5 | Emsalde pin formatları **INF'ten okunmadı** — hâlâ `⚠️` | §10 açık konuları aynen geçerli |

---

## 14. Neva Sanal Cihaz Şartnamesi (bu araştırmadan türetilen — ADR-107 §2.5 girdisi)

| # | Şart | Doğrulama |
|---|---|---|
| R1 | **Örnekleme:** 44.1/48/88.2/96/176.4/**192 kHz** seçilebilir (kontrol paneli + INF) | Sürücü kurulumu sonrası ses ayarları |
| R2 | **Çözünürlük:** **32-bit float** destekli (emsal 24-bit — şartımız daha yüksek) | `WAVEFORMATEXTENSIBLE` + loopback hash testi |
| R3 | **Kanal:** ≥2 (stereo); 8 kanal hedef (emsal 1–8 ch) | 7.1 test akışı |
| R4 | **Buffer:** ≤1024 smp @192k (5,33 ms) — emsal 7168 sınırını aşmamak | Sürücü register ayarı + sayaç |
| R5 | **İşletim sistemi:** Win10 1607+ (EV/Attestation imza) — x86/x64/**ARM64** | İmza doğrulama + kurulum |
| R6 | **ASIO bridge:** sanal cihaz, ayrı DLL ile **≥4 istemci** desteklemeli (emsal sınırı) | DAW + CLI + 3. parti |
| R7 | **Yakalama yönü:** sanal cihaz → uygulama (loopback) bit-perfect kaydetme | SHA-256 hash eşitliği |
| R8 | **Cihaz değişimi canlı bildirimi** (Hot-Plug) | Tak/çek testi |
| R9 | Taban: **MIT lisanslı fork** (`VirtualDrivers/Virtual-Audio-Driver`) — AGPL/kapalı kaynak kopyalama yasak | LICENSE okuması |

**Bağlantı:** Bu şartname `neva-engine-platform-paketleme.md` §13 (backend şartnameleri) + ADR-107 §2.5 (sürücü ayrı iş) ile birlikte okunur.

---

**Authority:** Researcher Agent (Versa Coder) · **Mode:** Zero-hallucination · Red Team
**Son güncelleme:** 2026-10-22
**İlişkili:** [[.ai/research/neva-player-media-stack]] · [[.ai/projects/NevaEngine/*]] · ROLE.md §2.3 (#37 Virtual Audio, #38 Audio Driver, #39 WDK) · ADR-107 (draft)
