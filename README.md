# 🎵 CoreMusic

> *"Hayatın ritmi sende gizli, müziğinle parla!"*  
> **"Aynı Müzik Her Yerde Seninle" — Sınırların ötesinde bir müzik deneyimi.**  
> *Software · Audio · Hardware · AI — Version 1.0*

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES2022%20Vanilla-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://tc39.es/ecma262/)
[![C++](https://img.shields.io/badge/C++-20%20NevaEngine-00599C?style=flat&logo=cplusplus&logoColor=white)](https://isocpp.org/)
[![MySQL](https://img.shields.io/badge/MySQL-9%20(18%20BCNF)-4479A1?style=flat&logo=mysql&logoColor=white)](https://dev.mysql.com/)
[![Architecture](https://img.shields.io/badge/Architecture-21%20Layers%20(1095%20Components)-blue)](.ai/architecture/index.md)
[![License](https://img.shields.io/badge/License-Proprietary-red)](#lisans)
[![Status](https://img.shields.io/badge/Status-Aktif%20Geliştirme-brightgreen)](#proje-durumu)

---

## 📋 İçindekiler

- [1. Proje Tanımı ve Vizyon](#1-proje-tanımı-ve-vizyon)
- [2. Hangi Sorunları Çözer? (Pazar Çözüm Matrisi)](#2-hangi-sorunları-çözer-pazar-çözüm-matrisi)
- [3. Temel Yetenekler (10 Ana Başlık)](#3-temel-yetenekler-10-ana-başlık)
- [4. Sektörel Çözümler ve Subdomain Ağı](#4-sektörel-çözümler-ve-subdomain-ağı)
- [5. Hedef Kullanıcı Kitleleri](#5-hedef-kullanıcı-kitleleri)
- [6. Mimari Yapı (21 Katman, 1095 Bileşen)](#6-mimari-yapı-21-katman-1095-bileşen)
- [7. C++20 Neva Engine ve Ses Donanımı](#7-c20-neva-engine-ve-ses-donanımı)
- [8. Teknoloji Yığını](#8-teknoloji-yığını)
- [9. Kurulum ve Geliştirme](#9-kurulum-ve-geliştirme)
- [10. Single Source of Truth (Vault .ai/)](#10-single-source-of-truth-vault-ai)

---

## 1. Proje Tanımı ve Vizyon

### 1.1 CoreMusic Nedir?
**CoreMusic**, müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya dağıtımı süreçlerini tek bir platform altında birleştiren **kurumsal dijital ses ve medya ekosistemidir**.

Trilyon dolarlık küresel dijital medya, otomotiv ses sistemleri ve tüketici elektroniği pazarındaki yapısal açıkları kapatmak ve doğrudan yüksek kârlılığa dönüştürmek amacıyla geliştirilmiş kurumsal seviyede bir **Ticari Dijital Medya Ekosistemi ve Gelir Platformudur**.

Sıradan müzik çalarların sunduğu basit dosya oynatma deneyiminin ötesine geçerek; çevrim içi bulut akışı ve internet bağlantısı olmadan çalışabilen **Offline-First** mimarisi sayesinde kullanıcının FLAC, WAV ve MP3 formatındaki ses koleksiyonunu tam mülkiyet altında tutmasını sağlar.

### 1.2 Temel Felsefemiz
* **Mülkiyet ve Özgürlük:** Kiralama modellerini ortadan kaldırır. Kullanıcının sahip olduğu kayıpsız ses koleksiyonu kalıcı, bağımsız ve ilişkisel bir dijital varlık olarak korunur; telif veya lisans iptalleriyle arşivden asla silinmez.
* **Kesintisiz Bütünleşik Yaşam Deneyimi (Handoff):** Salonda başlatılan bir parça, arabaya binildiğinde (`car.coremusic.net`) veya iş istasyonuna geçildiğinde (`studio.coremusic.net`) tek bir milisaniye dahi duraksamadan, aynı akustik profille devam eder.

---

## 2. Hangi Sorunları Çözer? (Pazar Çözüm Matrisi)

CoreMusic, günümüz müzik ekosistemindeki 6 kronik pazar krizini ortadan kaldırmak üzere tasarlanmıştır:

| # | Mevcut Pazar Sorunları | CoreMusic Mühendislik Çözümleri |
|:---:|:---|:---|
| **01** | **Dağınık Platformlar:** Müzik arşivleri farklı platformlarda, cihazlarda ve uygulamalarda dağınık halde bulunur. | **Tek Ekosistem:** Tüm müzik arşiviniz tek platformda (`api.coremusic.net`), her cihazda senkronize ve anında erişilebilir. |
| **02** | **Kalite Kayıpları:** Streaming servisleri sıkıştırılmış (*lossy*) ses sunar; işletim sistemi mikserleri sesi bozar. | **Yüksek Ses Kalitesi (Hi-Fi):** C++20 Neva Engine ile OS mikserlerini baypas eden bit-perfect aktarım, 32-bit Float DSP ve kayıpsız FLAC/WAV saflığı. |
| **03** | **Platform Bağımlılığı & Mülkiyetsizlik:** Kullanıcılar verilerine sahip değildir; lisans iptalleriyle şarkılar silinir. | **Mutlak Veri Mülkiyeti:** Müzik arşiviniz tamamen sizin kontrolünüzdedir. Offline-First mimariyle internet olmadan da kesintisiz çalışır. |
| **04** | **Sınırlı Kişiselleştirme:** Arayüzler statik ve tekdüzedir; gerçek duygusal bağ ve akustik uyum kurulamaz. | **Gerçek Kişiselleştirme:** 2 katmanlı AI öneri motoru, müziğin enerjisine göre renk alan Ambient Aura ve doğal dille tema üreten AI Theme Maker. |
| **05** | **Cihaz Uyumsuzluğu:** Bir cihazdan diğerine geçerken müzik durur, senkronizasyon kopar. | **Tüm Cihazlarda Kusursuz Uyum:** Handoff (kesintisiz geçiş) ve WebRTC/WebSocket multi-room audio ile her ekranda tek akıcı deneyim. |
| **06** | **Profesyonel Araç Eksikliği:** Tüketici oynatıcılarında stüdyo referansı dinleme ve izleme araçları yoktur. | **Entegre Profesyonel Araçlar:** 8.1 Surround ses, 31-band parametrik EQ, EBU R128 LUFS ölçümü, FFT spektrum analizi, ASIO/WASAPI donanım desteği. |

---

## 3. Temel Yetenekler (10 Ana Başlık)

1. **Hibrit Çalışma Mimarisi (Online Cloud & Offline-First):** İnternet kopsa dahi yerel SSD önbelleğinden duraksamadan çalma; internet geldiğinde çift yönlü sessiz senkronizasyon.
2. **Audio DSP Engine & Canlı Mekân Akustiği:** C++20 Neva Engine ile sıfır bellek tahsisi (*zero-allocation*) ve kilitlenmeyen (*lock-free*) halka kuyruklar; Düğün Salonu, Konser Alanı & Arena, Canlı Stüdyo psikoakustik simülasyonları; 31-Band Parametrik & Grafik EQ; True Peak Brickwall Limiter (THD+N <%0.005, SNR >105dB).
3. **Ses İşleme Hassasiyeti:** Dahili ses boru hattında anlık 1528 dB teorik dinamik tavan sunan 32-Bit Float mimari; 64-Bit Float çift duyarlılık yol haritası.
4. **1.0'dan 8.1 Surround'a (+1 LFE) Hoparlör Matrisi:** 1.0 Mono, 2.0/2.1 Hi-Fi, 4.1 Quadraphonic, 5.1/7.1 Ev Sineması, 8.1 Stüdyo Referansı ve bağımsız aktif subwoofer (+1 LFE) faz hizalaması.
5. **Büyüleyici Canlı Temalar & AI Theme Maker:** Müziğin enerjisine göre nefes alan dinamik Ambient Aura (Glassmorphism) ve doğal dil komutlarıyla çalışan AI Theme Maker Tool.
6. **Çapraz Cihaz Ekosistemi & Handoff:** Mobil, PC, Akıllı TV, araç içi bilgi-eğlence ve ev sunucusu arasında anlık geçiş; odalar arası sıfır faz gecikmeli Multi-Room Audio.
7. **Merkezi Medya Depolama & Otonom İndirme:** `NovaSearchEngine` ile YouTube aramaları; `DeezerDownloader` (Deemix) ile stüdyo kalitesinde FLAC (16/24/32-bit Float) ve WAV indirme; tek tıkla USB/HDD aktarımı (FAT32/exFAT/NTFS, ID3v2); Optik Audio CD (Red Book) ve MP3 CD yazma.
8. **Yerel Ağ & Network Audio:** DLNA/UPnP ve WebRTC/P2P protokolleriyle ev ağındaki tüm cihazlara kayıpsız, ultra düşük gecikmeli medya yayını.
9. **AI Müzik Intelligence:** Collaborative filtering + content-based öneri sistemi; parça analitiği (BPM, Key, Energy, Mood); oda akustiğini analiz eden AI Otomatik EQ.
10. **Sektörel Donanım Entegrasyonu:** XMOS XU316 USB Audio işlemcisi, AK4458 DAC, PCM3168A ADC, 8x50W modüler discrete Class AB amplifikatör ve ±35V LM5122 interleaved boost güç kaynağı.

---

## 4. Sektörel Çözümler ve Subdomain Ağı

CoreMusic ekosistemi, 10 bağımsız uzmanlık paneli üzerinden modüler olarak çalışır:

| Subdomain | Sektörel Kapsam | Öne Çıkan Fonksiyon |
|:---|:---|:---|
| **`music.coremusic.net`** | Son Kullanıcı Müzik Portalı | Hibrit SPA, Ambient Aura, AI öneri listeleri |
| **`home.coremusic.net`** | Akıllı Ev & Medya Merkezi | RPi5 desteği, Multi-Room odalar arası ses, Smart TV modu |
| **`car.coremusic.net`** | Otomotiv Bilgi-Eğlence | 48x48px dev dokunmatik butonlar, gece modu, offline önbellek |
| **`studio.coremusic.net`**| Ses Mühendisliği & Mastering | 8.1 Surround izleme, 31-band EQ, LUFS ölçer, FFT analizi |
| **`download.coremusic.net`**| Otonom İndirme & Arşiv | YouTube/Deezer kuyrukları, FAT32 USB ve CD/DVD yazıcı |
| **`media.coremusic.net`** | Medya Deposu & Dağıtım | Çok kaynaklı kütüphane, FFmpeg transcode, DLNA sunucu |
| **`admin.coremusic.net`** | Sistem Yönetim Konsolu | Kullanıcı, kota, veritabanı, güvenlik ve log yönetimi |
| **`auth.coremusic.net`** | Kimlik ve Yetkilendirme | SSO, oturum yönetimi, RBAC yetki matrisi, Credential Vault |
| **`pro.coremusic.net`** | Donanım & DSP Paneli | Neva Engine DSP denetimi, Class AB amfi telemetrisi |
| **`coremusic.net`** | Ana Tanıtım & Portal | Ekosistem tanıtımı, açık kaynak dokümantasyon, indirme |

---

## 5. Hedef Kullanıcı Kitleleri

1. **🎧 Bireysel Kullanıcılar:** Kolay kullanımlı şık arayüz, kişisel arşiv yönetimi, AI destekli müzik keşfi.
2. **🎵 Hi-Fi / Audiophile:** 32-bit float kayıpsız ses (FLAC/WAV), ASIO/WASAPI desteği, 31-band EQ, saf analog amfi çıkışı.
3. **🎛️ Profesyonel Stüdyo:** 8.1 surround ses izleme, <10ms sinyal gecikmesi, EBU R128 ses şiddeti ölçümü, mastering araçları.
4. **🚗 Araç Kullanıcıları:** Güvenli sürüş için optimize edilmiş dokunmatik arayüz, tünellerde kesilmeyen offline yerel akış.
5. **🏠 Ev Medya Kullanıcıları:** Raspberry Pi 5 ev sunucusu, odalar arası senkronize ses (Multi-Room), Smart TV Ambient Aura.
6. **💻 Geliştirici / Freelancer:** Açık mimari dokümantasyonu, REST/WebSocket API'ler, modüler sürücü şablonları.

---

## 6. Mimari Yapı (21 Katman, 1095 Bileşen)

CoreMusic, **A0 Altyapı'dan A5 Bileşenler'e** uzanan 21 dikey katman (K0-K20, 6 alan etiketi A0-A5) ve 1095 bileşen üzerine inşa edilmiştir:

```
===========================================================================
|                    COREMUSIC 21 KATMANLI MİMARİ                         |
|                    1095 BİLEŞEN | DC-ONLY GÜÇ KAYNAĞI                   |
+-------------------------------------------------------------------------+
|  K13: CI/CD           |  K12: İZLEME           |  K15: MEDYA & STREAMING|
|  GitHub Actions / K8s |  App Logs / Prometheus |  FFmpeg - FLAC - HLS   |
|  Docker / Playwright  |  Grafana / Audit Logs  |  DASH - Podcast - Radio|
+-------------------------------------------------------------------------+
|  ELEKTRONİK & DONANIM ALTYAPISI (K16 - K20)                             |
+-------------------------------------------------------------------------+
|  K20: BOM & ÜRETİM    -- 40 Bileşen: Transistör, Diyot, Direnç, BOM     |
|  K19: PCB TASARIM     -- 50 Bileşen: 6-Layer, Controlled Z, Star GND    |
|  K18: TERMAL TASARIM  -- 45 Bileşen: Fischer Heatsink, KSD301, PWM Fan  |
|  K17: GÜÇ ±35V        -- 85 Bileşen: LM5122 Dual Boost, 6S LiPo, %96    |
|  K16: CLASS AB AMP    -- 120 Bileşen: MJL21194/93, 8x50W, THD <0.005%   |
+-------------------------------------------------------------------------+
|  KULLANICI DENEYİMİ & UYGULAMA (K10, K11, K14)                          |
+-------------------------------------------------------------------------+
|  K11: UX & TASARIM    -- 45 Bileşen: ITCSS, BEM, Tokens, Theme, PWA     |
|  K10: UYGULAMA        -- 50 Bileşen: Music, Home, Car, Studio, Admin    |
|  K14: AĞ & İLETİŞİM   -- 50 Bileşen: HTTP/2/3, WebSocket, AirPlay       |
+-------------------------------------------------------------------------+
|  SERVİS & ROUTING (K8 - K9)                                             |
+-------------------------------------------------------------------------+
|  K9:  API & ROUTING   -- 45 Bileşen: Gateway, BFF, CQRS, SPA Router     |
|  K8:  SERVİS KATMANI  -- 60 Bileşen: Control, Media, Audio, Device, AI  |
+-------------------------------------------------------------------------+
|  GÜVENLİK & MIDDLEWARE PIPELINE (K6 - K7)                               |
+-------------------------------------------------------------------------+
|  K7:  MIDDLEWARE      -- 40 Bileşen: OriginCheck, CORS, RateLimit, CSRF |
|  K6:  GÜVENLİK        -- 45 Bileşen: Auth, RBAC, AES-256, Audit Trail   |
+-------------------------------------------------------------------------+
|  VERİ YÖNETİMİ & YAPAY ZEKA (K4 - K5)                                   |
+-------------------------------------------------------------------------+
|  K5:  VERİ YÖNETİMİ   -- 55 Bileşen: MySQL 18 DB (156 Tablo), Redis     |
|  K4:  YAPAY ZEKA      -- 55 Bileşen: Music Analysis, Rec, Auto EQ, ML   |
+-------------------------------------------------------------------------+
|  SES MOTORU & SÜRÜCÜ ÇEKİRDEĞİ (K2 - K3)                                |
+-------------------------------------------------------------------------+
|  K3:  SES İŞLEM MOTORU-- 55 Bileşen: Neva Engine, DSP, EQ, Crossover    |
|  K2:  SÜRÜCÜ KATMANI  -- 45 Bileşen: ASIO, WASAPI, ALSA, PipeWire       |
+-------------------------------------------------------------------------+
|  TEMEL DONANIM PLATFORMU & OS (K0 - K1)                                 |
+-------------------------------------------------------------------------+
|  K1:  DONANIM ALTYAPI -- 120 Bileşen: XMOS XU316, PCM3168A, AK4458 DAC  |
|  K0:  İŞLETİM SİSTEMİ -- 50 Bileşen: Windows, Linux, macOS, RPi5, Docker|
+-------------------------------------------------------------------------+
|  Toplam: 21 Katman | 1095 Bileşen | DC-ONLY | ~$682 Sistem Maliyeti     |
===========================================================================
```

---

## 7. C++20 Neva Engine ve Ses Donanımı

CoreMusic'in kalbinde, işletim sisteminin sesi bozan katmanlarını baypas eden C++20 Neva Engine ve ayrık analog donanım amfisi yer alır:

* **32-Bit Float DSP:** 15 aşamalı filtre zinciri (InputGain → Gate → HPF → LPF → 31-Band EQ → Dynamics → Delay → Reverb → True Peak Limiter).
* **Discrete Class AB Amplifikatör:** 8 kanal bağımsız modüler yapı, MJL21194/MJL21193 tamamlayıcı çıkış çifti, 50W RMS @ 8Ω (80W @ 4Ω), THD+N <%0.005.
* **±35V LM5122 Dual Boost Güç Kaynağı:** 6S LiPo (22.2V) veya 19-24V DC laptop adaptörü girişi; %96 tepe verimlilik, sıfır 50Hz şebeke gürültüsü sağlayan **DC-ONLY** güç mimarisi.
* **Ses Kartı Köprüsü:** XMOS XU316 USB Audio Class 2.0 işlemcisi, PCM3168A 8-kanal ADC, AK4458 8-kanal DAC.

---

## 8. Teknoloji Yığını

* **Backend:** PHP 8.4+ (Strict types, PDO, PSR-15 Middleware), Node.js / TypeScript (Download microservice)
* **Frontend:** Vanilla JavaScript (ES2022 SPA Router), ITCSS 9-Layer + BEM CSS, Glassmorphism UI
* **Ses Motoru:** Modern C++20, JUCE 9 AudioProcessor, Steinberg ASIO SDK, Windows WASAPI Exclusive, Linux ALSA
* **Veritabanı & Önbellek:** MySQL 9 (18 BCNF Normalleştirilmiş Veritabanı, 156 Tablo), Redis, APCu
* **Güvenlik & Kriptografi:** Argon2id parola özeti, AES-256-GCM Credential Vault, CSP Nonce, CSRF koruması

---

## 9. Kurulum ve Geliştirme

```bash
# 1. Depoyu klonlayın
git clone https://github.com/coremusic/coremusic.net.git
cd coremusic.net

# 2. PHP Servisini Başlatın (Port 81)
php -S localhost:81 -t public/

# 3. İndirme Servisini Başlatın (Port 3001)
cd download-service && npm install && npm run dev

# 4. Testleri Çalıştırın
cd shared && vendor/bin/phpunit
```

---

## 10. Single Source of Truth (Vault .ai/)

CoreMusic projesinin tüm mimari kararları, anayasası, kuralları ve detaylı dokümanları `.ai/` dizinindeki **Vault** içerisinde toplanmıştır:

| Doküman | Yol | Açıklama |
|:---|:---|:---|
| **Vizyon Belgesi** | [`.ai/VISION.md`](.ai/VISION.md) | Proje vizyonu, pazar krizi, mülkiyet felsefesi ve stratejik hedefler |
| **Proje Tanımı** | [`.ai/PROJECTS.md`](.ai/PROJECTS.md) | 10 temel yetenek, sektörler, kullanıcı profilleri ve pazar çözümleri |
| **AI Anayasası** | [`.ai/CLAUDE.md`](.ai/CLAUDE.md) | 16 Hard Guardrail, mühendislik standartları ve kurallar |
| **Master İndeks** | [`.ai/architecture/index.md`](.ai/architecture/index.md) | 21 katman, 1.095 bileşen, 18 BCNF DB ve ADR kayıt defteri |
| **Devre Şeması** | [`.ai/architecture/electronics/amfii/amplifier-classab-circuit.md`](.ai/architecture/electronics/amfii/amplifier-classab-circuit.md) | Class AB 50W amfi devresi, Mermaid şeması ve test noktaları |

---

**Authority:** Bayram Ali / Vault Steward  
**Kaynak Doküman:** Freelancer Technical Documentation v1.0 (CoreMusic: Software Audio Hardware AI)  
**Last Updated:** 2026-09-19  
**Version:** 2.0.1  
**Mode:** Red Team · Human Mode · Truth Mode
