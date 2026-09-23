---
reference_doc: Freelancer Technical Documentation v1.0
type: project
category: core
title: "CoreMusic — Proje Tanımı, Yetenekler, Ekosistem Modeli ve Proje Envanteri"
date: 2026-09-19
updated: 2026-09-23
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Proje Tanımı, Yetenekler, Ekosistem Modeli ve Proje Envanteri

> *"Software · Audio · Hardware · AI — One Ecosystem, Limitless Music."*

---

## Zorunlu Bağlantılar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Anayasa, kurallar, guardrail'ler |
| [[VISION]] | Vizyon, pazar analisi, strateji |
| [[brain.md]] | Mimari kararlar, ADR 001-089 |
| [[index.md]] | Vault master kataloğu |
| [[AGENTS.md]] | Agent kayıt defteri |
| [[WORKFLOW.md]] | Süreçler ve fazlar |
| [[architecture/index]] | 21 katmanlı mimari indeks |

---

## 1. CoreMusic Nedir?

**CoreMusic**, müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya dağıtımı süreçlerini tek bir platform altında birleştiren **kurumsal dijital ses ve medya ekosistemidir**.

Geleneksel müzik çalarların sunduğu basit dosya oynatma deneyiminin ötesine geçerek; çevrim içi bulut akışı ve internet bağlantısı olmadan çalışabilen **Offline-First** mimarisi sayesinde kullanıcının FLAC, WAV ve MP3 formatındaki ses koleksiyonunu tam mülkiyet altında tutmasını sağlar.

```
+-------------------------------------------------------------------------+
|                  COREMUSIC EKOSİSTEMİ DİKEY ENTEGRASYONU                 |
+-------------------------------------------------------------------------+
|  10 ÖN YÜZ PANELİ    -> music, home, car, studio, download, admin, ...  |
|  7 MİKROSERVİS       -> Control, Media, Audio, Device, Network, AI, Down |
|  18 BCNF VERİTABANI  -> 156 Tablo, Sıfır Veri Tekrarı, Saf PDO Güvenliği|
|  C++20 NEVA ENGINE   -> 32-bit Float DSP, True Peak Limiter, Reverb      |
|  DONANIM PLATFORMU   -> XMOS XU316, Class AB 8x50W Amfi, ±35V Dual Boost|
|  YAPAY ZEKA MOTORU   -> 2-Katmanlı Öneri, Ses Analizi (BPM/Key), Auto EQ|
+-------------------------------------------------------------------------+
```

### 1.1 Temel Çekirdek Unsurları

* **Yapay Zeka Destekli Müzik Öneri Sistemi:** Kullanıcının dinleme geçmişini, beğenilerini ve tercihlerini analiz ederek **collaborative filtering** ve **content-based** olmak üzere iki katmanlı bir motorla kişiselleştirilmiş öneriler üretir.
* **Ses Analizi AI'ı:** Her parçanın BPM'sini, anahtarını (musical key), enerjisini ve ruh halini (mood) otomatik olarak indeksler; bu sayede müzik kütüphanesi yalnızca sanatçı ve albüm bilgisiyle değil, sesin doğrudan akustik karakteriyle sınıflandırılır.
* **AI Otomatik EQ Sistemi:** Dinlenen odanın akustiğini analiz ederek hoparlör konfigürasyonuna uygun equalizer eğrisini kendisi üretir.
* **Hata Tahmin AI'ı:** Donanım bileşenlerinin performans düşüşünü veya aşırı ısınmasını önceden tespit ederek kullanıcıyı korur.
* **Merkezi Medya Yönetimi:** Ses dosyalarının yanı sıra podcast, radyo kanalları ve video klipleri de aynı kütüphane içinde yönetilir; metadata, şarkı sözleri ve yüksek çözünürlüklü albüm kapakları otomatik indekslenir.
* **Otonom İndirme Sistemi:** YouTube linklerini otomatik arar; Deezer üzerinden stüdyo referansında kayıpsız FLAC (16/24/32-bit Float) formatında edinir ve kütüphaneye metadata zenginleştirmesiyle dahil eder.
* **Canlı Tema Sistemi & AI Theme Maker:** Çalan müziğin enerjisine göre anlık renk değiştiren Glassmorphism arayüzü ve doğal dille ("80'ler retro neon", "Gece mavisi akustik") kişisel temalar üreten Theme Maker Tool.
* **C++20 Neva Engine (Sesin Kalbi):** İşletim sisteminin ses kalitesini bozan mikser katmanlarını baypas eden; 32-bit Float DSP, True Peak Brickwall Limiter ve psikoakustik reverb algoritmalarıyla çalışan tescilli ses motoru.
* **Mono'dan 8.1 Surround'a Akıllı Yönlendirme:** 1.0 Mono'dan 8.1 Surround'a (+1 LFE aktif subwoofer) kadar tüm hoparlör yapılandırmalarını matris düzeyinde denetler; ASIO, WASAPI ve ALSA donanım desteği sunar.
* **11 Uzmanlık Alanına Sahip AI Agent Sistemi:** Ses analizinden donanım optimizasyonuna, bilgi bankası yönetiminden prompt üretimine kadar tüm süreçleri koordine eden agent orkestrasyonu.

---

## 2. Temel Özellikler (10 Ana Başlık)

CoreMusic'in sunduğu yetenekler teknik dokümantasyon uyarınca 10 ana başlık altında toplanmıştır:

| # | Temel Başlık | Kapsam ve Teknik Özellikler |
|:---:|:---|:---|
| **1** | **Hibrit Çalışma Mimarisi (Online Cloud & Offline-First)** | **Kesintisiz Yerel Akış:** Tünellerde veya kırsal yollarda internet kopsa dahi müzik yerel SSD önbelleğinden duraksamadan çalar.<br>**Çift Yönlü Bulut Eşzamanlaması:** Bağlantı sağlandığında listeler ve geçmiş arka planda sessizce eşitlenir.<br>**Kullanıcı Veri Mülkiyeti:** Kayıpsız arşiv (FLAC, WAV, MP3) kullanıcının kalıcı mülkiyetinde kalır. |
| **2** | **Audio DSP Engine & Canlı Mekân Akustiği** | **C++20 Çekirdeği:** Sıfır bellek tahsisi (*zero-allocation*) ve kilitlenmeyen (*lock-free*) halka kuyruklar.<br>**Canlı Mekân Modelleri:** Düğün Salonu, Konser Alanı & Arena, Canlı Stüdyo ve Kulüp psikoakustik simülasyonları.<br>**31-Band Parametrik & Grafik EQ:** 20 Hz – 20 kHz arasında 1/3 oktav stüdyo seviyesi filtreleme.<br>**True Peak Brickwall Limiter:** Ses sonuna kadar açılsa dahi dijital çatlamayı önler (THD+N <%0.005, SNR >105dB). |
| **3** | **Ses İşleme Hassasiyeti & Gelecek Yol Haritası** | **Aktif 32-Bit Float:** Dahili ses boru hattında anlık 1528 dB teorik dinamik tavan.<br>**64-Bit Float Yol Haritası:** Gelecek nesil çift duyarlılıklı (*double precision*) çekirdek AR-GE planlamasında. |
| **4** | **1.0'dan 8.1 Surround'a (+1 LFE) Hoparlör Matrisi** | **Esnek Kanal Mimarisi:** 1.0 Mono, 2.0/2.1 Hi-Fi, 4.1 Quadraphonic, 5.1/7.1 Ev Sineması, 8.1 Stüdyo Referansı.<br>**Aktif Subwoofer Yönetimi:** Bağımsız LFE bas frekans süzme ve faz hizalaması ile kristal netliğinde vuruşlar. |
| **5** | **Büyüleyici Canlı Temalar & AI Theme Maker** | **Ambient Aura:** Çalan albüm kapağının tonlarına ve müziğin enerjisine göre gerçek zamanlı nefes alan akıcı Glassmorphism arayüzü.<br>**Yapay Zeka ile Tema Tasarımı:** Kullanıcıların doğal dil komutlarıyla anında estetik temalar üretebilmesi. |
| **6** | **Çapraz Cihaz Ekosistemi & Handoff** | **Tüm Ekranlarda Tek Deneyim:** Mobil, tablet, PC, akıllı TV, araç içi (`car.coremusic.net`) ve ev medya merkezi (`home.coremusic.net` - RPi5).<br>**Çok Odalı Ses (Multi-Room):** WebRTC ve WebSocket ile farklı odalardaki hoparlörlere sıfır faz gecikmeli ses dağıtımı. |
| **7** | **Merkezi Medya Depolama & Otonom İndirme** | **Modüler İndirme Sürücüleri:** `NovaSearchEngine` (YouTube arama), `DeezerDownloader` (Deemix çekirdeği ile stüdyo kalitesinde FLAC/WAV indirme), `YouTubeDownloader` (1080p/4K video klip).<br>**Admin Otomasyonu:** Sanatçı veya tür bazlı zamanlanmış indirme kuyrukları (`download.coremusic.net`).<br>**Çoklu Dışa Aktarma:** Tek tıkla USB/HDD aktarımı (FAT32/exFAT/NTFS, ID3v2 etiketleme), Optik Audio CD (Red Book) ve MP3 CD yazma desteği. |
| **8** | **Yerel Ağ & Network Audio** | DLNA/UPnP ve WebRTC/P2P protokolleriyle ev ağındaki tüm cihazlara kayıpsız, ultra düşük gecikmeli medya akışı. |
| **9** | **AI Müzik Intelligence** | Kişiselleştirilmiş öneri motoru, akıllı dinamik çalma listeleri (Smart Playlist), ortam akustiğine göre otomatik EQ ayarı, parçaların BPM, Key, Energy ve Mood analitiği. |
| **10** | **Sektörel Donanım Entegrasyonu** | XMOS XU316 USB Audio Class 2.0, AK4458 DAC, PCM3168A ADC, 8x50W Class AB analog amplifikatör ve ±35V senkron boost güç kaynağı entegrasyonu. |

---

## 3. Kullanım Alanları (9 Ana Senaryo)

CoreMusic; farklı kullanım ortamlarına kusursuz adapte olan modüler bir mimariye sahiptir:

1. **Ev Medya Merkezi:** Ev ağında NAS veya Raspberry Pi 5 üzerinde çalışan merkezi kütüphane sunucusu; odalar arası kablosuz çoklu oda (*multi-room*) ses dağıtımı (`home.coremusic.net`).
2. **Araç İçi Bilgi-Eğlence:** Dokunmatik araç ekranlarına özel optimize edilmiş, dikkat dağıtmayan yüksek kontrastlı gece arayüzü; RPi5 + PCM3168A üzerinden araç ses sistemine kayıpsız çıkış (`car.coremusic.net`).
3. **Profesyonel Stüdyo:** ASIO SDK entegrasyonuyla 32-bit float hassasiyetinde <10ms gecikmeli ses akışı, 8.1 surround izleme, 31-band parametrik EQ ve çok kanallı kayıt yönetimi (`studio.coremusic.net`).
4. **Masaüstü İş İstasyonu:** Windows, Linux ve macOS sistemlerinde yerel ses sunucuları; gelişmiş arşivleme, etiketleme ve kütüphane düzenleme araçları.
5. **Kişisel Dinleme & Odyofili:** Yüksek çözünürlüklü Hi-Fi ses dosyaları, bit-perfect aktarım, 31 bant EQ ve kişisel profille sesi stüdyodan çıktığı saf haliyle dinleme.
6. **Kişisel Müzik Koleksiyoneri:** Offline-first mimariyle mutlak veri mülkiyeti; kayıpsız formatlarda (FLAC, WAV, ALAC) bağımsız dijital arşiv inşası.
7. **Geliştirici & Entegrasyon:** REST/WebSocket API erişimi, WebRTC protokolleri ve tak-çıkar (*pluggable*) mimariyle üçüncü parti yazılım ve donanım entegrasyonları.
8. **Kurumsal / Ticari Kullanım:** Otel, restoran, mağaza, AVM, ofis ve spor salonları gibi işletmeler için merkezi müzik yayını ve marka odaklı akustik yönetimi.
9. **Yayın ve İçerik Üretimi:** Podcast, radyo yayıncılığı, canlı yayın ve içerik üreticileri için profesyonel ses işleme, otomatik seviyeleme ve kalite optimizasyonu.

---

## 4. Hedef Kullanıcı Profilleri (6 Kitle)

```
+-------------------------------------------------------------------------+
|                  COREMUSIC 6 TEMEL HEDEF KULLANICI KİTLESİ              |
+-------------------------------------------------------------------------+
|  1. BİREYSEL KULLANICI      -> Kolay arayüz, kişisel arşiv, çoklu cihaz|
|  2. HI-FI / AUDIOPHILE      -> Kayıpsız FLAC/WAV, Bit-Perfect, DSP/EQ   |
|  3. PROFESYONEL STÜDYO      -> Stüdyo referansı, 8.1 Surround, ASIO SDK |
|  4. ARAÇ KULLANICISI        -> CarPlay/Android Auto uyumu, dev butonlar |
|  5. EV MEDYA KULLANICISI    -> Multi-room, Smart TV, NAS/DLNA, Handoff  |
|  6. GELİŞTİRİCİ / FREELANCER-> Açık dokümantasyon, modüler API, şablon  |
+-------------------------------------------------------------------------+
```

1. **Bireysel Kullanıcılar:** Kişisel müzik arşivini yönetmek, dilediği cihazda kesintisiz ve yüksek kalitede dinlemek isteyen son kullanıcılar. (Kolay kullanım, çapraz senkronizasyon, AI öneriler).
2. **Hi-Fi / Audiophile:** Ses kalitesine, donanım kontrolüne ve profesyonel ses işleme detaylarına önem veren üst seviye dinleyiciler. (Kayıpsız FLAC/WAV, gelişmiş DSP/EQ, ASIO/WASAPI, donanım amfisi).
3. **Profesyonel Stüdyo:** Kayıt, miks, mastering ve prodüksiyon süreçlerinde stüdyo referansında ses kontrolü sağlamak isteyen profesyoneller. (8.1 surround izleme, EBU R128 LUFS ölçümü, FFT analizi).
4. **Araç Kullanıcısı:** Araç içi bilgi-eğlence sisteminde güvenli, sürüşü bölmeyen, kesintisiz ve yüksek kaliteli müzik deneyimi yaşamak isteyen sürücüler. (Offline önbellek, 4.1/5.1 akustik ayar).
5. **Ev Medya Kullanıcısı:** Evinde çok odalı, merkezi ve yüksek kaliteli bir müzik ekosistemi oluşturmak isteyen aileler ve teknoloji meraklıları. (Multi-room, Ambient Aura Smart TV, RPi5 ev sunucusu).
6. **Geliştirici / Freelancer:** CoreMusic ekosistemine modül, sürücü veya arayüz geliştirecek yazılım ve donanım mühendisleri. (Açık dokümantasyon, net mimari kurallar, modüler API).

---

## 5. Sektörel Çözümler ve Subdomain Ağı

CoreMusic, her sektörel ihtiyacı ayrı bir alt alan adı (*subdomain*) ile dikey olarak çözer:

| Subdomain | Sektörel Çözüm Alanı | Öne Çıkan Özellikler |
|:---|:---|:---|
| `music.coremusic.net` | **Kişisel Dinleme & Web Player** | Hibrit SPA, Ambient Aura, kişiselleştirilmiş AI çalma listeleri |
| `home.coremusic.net` | **Akıllı Ev & Multi-Room Audio** | RPi5 desteği, odalar arası senkronize ses, Smart TV arayüzü |
| `car.coremusic.net` | **Otomotiv & Araç İçi Bilgi-Eğlence** | 48x48px dev dokunmatik butonlar, gece modu, kesintisiz offline akış |
| `studio.coremusic.net`| **Profesyonel Ses Mühendisliği** | 8.1 Surround izleme, 31-band parametrik EQ, LUFS ölçümü, FFT spektrum |
| `download.coremusic.net`| **Otonom İndirme & Dışa Aktarma** | YouTube/Deezer indirme kuyrukları, FAT32 USB ve CD/DVD yazıcı |
| `media.coremusic.net` | **Merkezi Medya Deposu & Dağıtım** | Çok kaynaklı medya kütüphanesi, transcode, DLNA/UPnP sunucusu |
| `admin.coremusic.net` | **Merkezi Sistem Konsolu** | Kullanıcı, kota, veritabanı, güvenlik ve mikroservis yönetimi |
| `auth.coremusic.net`  | **Merkezi Kimlik ve Yetkilendirme** | SSO, Session yönetimi, RBAC yetkilendirme, Credential Vault |
| `pro.coremusic.net`   | **Ses Mühendisi & Donanım Paneli** | DSP parametre ayarları, Class AB amfi telemetrisi ve test araçları |
| `coremusic.net`       | **Ana Tanıtım ve Karşılama Portalı** | Platform tanıtımı, ekosistem vizyonu ve indirme merkezi |

---

## 6. Hangi Sorunları Çözer? (Pazar Çözüm Matrisi)

| Mevcut Pazar Sorunu | CoreMusic Mühendislik Çözümü |
|:---|:---|
| **Dağınık Platformlar:** Şarkıların farklı uygulamalara bölünmesi | **Merkezi Ekosistem:** Tek omurgada toplanan tam entegre kütüphane |
| **Kayıplı Ses Sıkıştırma (Lossy):** Sıkıştırılmış kalitesiz ses | **Kayıpsız Bit-Perfect:** C++20 Neva Engine ile 32-bit Float FLAC/WAV saflığı |
| **Mülkiyetsiz Kiralama Modeli:** Abonelik bittiğinde her şeyin kaybolması | **Mutlak Veri Mülkiyeti:** Offline-First mimariyle kalıcı bağımsız kütüphane |
| **Statik & Sıkıcı Arayüzler:** Kişiselleştirilemeyen müzik deneyimi | **AI Theme Maker & Ambient Aura:** Çalan müziğin enerjisiyle yaşayan canlı arayüz |
| **Cihaz Uyumsuzluğu:** Cihaz değişiminde müziğin durması | **Kesintisiz Handoff & Multi-Room:** Arabadan eve milisaniyesiz aktarım |
| **Profesyonel Araç Yokluğu:** Tüketici çalarlarında stüdyo aracının olmaması | **Entegre Stüdyo Araçları:** 8.1 surround, 31-band EQ, ASIO, EBU R128 LUFS |

---

## 7. Proje Envanteri — Detaylı Katalog

### 7.1 Yazılım Projeleri

#### 7.1.1 NevaEngine (C++20 Audio Engine)
| Özellik | Değer |
|---------|-------|
| Dil | C++20 |
| Amaç | OS mikserini baypas eden high-performance ses motoru |
| Sample Format | 32-bit Float (64-bit yol haritası) |
| Sample Rate | 48kHz standart, 96/192kHz desteği |
| Kanal Desteği | 1.0 Mono → 8.1 Surround (+1 LFE) |
| Gecikme Hedefi | <10ms (ASIO), <20ms (WASAPI) |
| DSP Efektleri | EQ, Reverb, Compressor, Limiter, Crossover |
| Bellek Modeli | Zero-allocation, lock-free ring buffer |
| Donanım Bağımlılığı | K2 Sürücü katmanına sıkı bağımlı (ASIO/WASAPI/ALSA) |
| Test Framework | Google Test |
| Hedef Coverage | ≥80% (minimum), ≥90% (hedef) |
| Durum | GELİŞTİRMEDE |

#### 7.1.2 NevaPlayer (Cross-Platform Media Player)
| Özellik | Değer |
|---------|-------|
| Dil | PHP 8.4 (backend) + Vanilla JS ES6+ (frontend) |
| Amaç | Tüm cihazlarda müzik oynatma ve yönetim arayüzü |
| Panel Sayısı | 10 web paneli (music, home, car, studio, download, admin, auth, media, pro, landing) |
| Mimari | SPA Router, API Gateway, BFF, CQRS |
| Tema Sistemi | Ambient Aura, AI Theme Maker, Gender-based |
| CSS | ITCSS 9-layer, BEM methodology |
| Offline Desteği | Service Worker, IndexedDB, local cache |
| Durum | GELİŞTİRMEDE |

#### 7.1.3 WirelessConnect (Bluetooth/WiFi Audio)
| Özellik | Değer |
|---------|-------|
| Dil | C++20 |
| Amaç | Kablosuz ses bağlantısı yönetimi |
| Protokoller | BLE 5.0, WiFi Direct, mDNS, DLNA/UPnP |
| Hedef | Cihazlar arası keşif ve otomatik bağlantı |
| Durum | PLANLANMIŞ |

#### 7.1.4 NevaConnect (Multi-Room Audio)
| Özellik | Değer |
|---------|-------|
| Dil | C++20 + JavaScript |
| Amaç | Çok odalı senkronize ses dağıtımı |
| Protokoller | WebRTC, WebSocket, P2P |
| Gecikme Hedefi | <5ms faz farkı (oda arası) |
| Maksimum Oda | 16 oda (hedef) |
| Durum | PLANLANMIŞ |

#### 7.1.5 Download Service (Node.js + TypeScript)
| Özellik | Değer |
|---------|-------|
| Dil | Node.js + TypeScript |
| Port | 3001 |
| Amaç | YouTube/Deezer'dan otonom müzik indirme |
| Kaynaklar | YouTube, YouTube Music, Deezer (FLAC stüdyo kalitesi) |
| Anti-Ban | Proxy rotasyonu, rate limiting, user-agent spoofing |
| Çıkış Formatları | FLAC, WAV, MP3, MP4 |
| API | REST + WebSocket (gerçek zamanlı durum) |
| Durum | AKTİF |

#### 7.1.6 Web Platform (PHP 8.4 SPA)
| Özellik | Değer |
|---------|-------|
| Dil | PHP 8.4 (strict_types) + Vanilla JS ES6+ |
| CSS | ITCSS 9-layer + BEM |
| Veritabanı | MySQL 9 (18 BCNF, 156 tablo) |
| Güvenlik | 10 adımlı middleware, CSRF, CSP, Argon2id |
| API | OpenAPI spec, REST, WebSocket |
| Durum | GELİŞTİRMEDE |

#### 7.1.7 DSP Engine
| Özellik | Değer |
|---------|-------|
| Dil | C++20 |
| Amaç | Dijital sinyal işleme ve efekt yönetimi |
| Efektler | 31-band parametrik EQ, reverb (4 mod), compressor, limiter, crossover |
| Gerçek Zamanlı | Evet (ASIO callback içinde) |
| Durum | PLANLANMIŞ |

#### 7.1.8 AI Recommendation Engine
| Özellik | Değer |
|---------|-------|
| Dil | PHP + Python |
| Amaç | Kişisel müzik önerileri ve ses analizi |
| Model | Collaborative filtering + Content-based (2 katman) |
| Analiz | BPM, Key, Energy, Mood, Spectral features |
| Durum | PLANLANMIŞ |

#### 7.1.9 EQ/DSP Manager
| Özellik | Değer |
|---------|-------|
| Dil | PHP 8.4 + JS |
| Amaç | Equalizer ve DSP parametre yönetimi |
| EQ Türleri | 31-band parametrik, grafik, otomatik (AI) |
| Preset | Kullanıcı presets + AI otomatik |
| Durum | PLANLANMIŞ |

#### 7.1.10 Streaming Service
| Özellik | Değer |
|---------|-------|
| Dil | PHP + FFmpeg |
| Port | 5000/6000 |
| Amaç | Medya transcode ve akış sunucusu |
| Protokoller | HLS, DASH, DLNA/UPnP |
| Durum | PLANLANMIŞ |

#### 7.1.11 Mobile Companion App
| Özellik | Değer |
|---------|-------|
| Dil | TBD (React Native / Flutter) |
| Amaç | Mobil cihazlar için uzaktan kumanda ve dinleme |
| Özellikler | Handoff, multi-room kontrol, AI önerileri |
| Durum | PLANLANMIŞ |

#### 7.1.12 Admin Dashboard
| Özellik | Değer |
|---------|-------|
| Dil | PHP 8.4 + JS |
| Port | 80 |
| Amaç | Merkezi sistem yönetimi |
| Özellikler | Kullanıcı yönetimi, kota, DB, güvenlik, mikroservis izleme |
| Durum | PLANLANMIŞ |

#### 7.1.13 API Gateway
| Özellik | Değer |
|---------|-------|
| Dil | PHP 8.4 |
| Amaç | Tüm istemcilerin tek giriş noktası |
| Protokoller | REST, WebSocket, GraphQL (gelecek) |
| Güvenlik | Rate limiting, auth, validation, logging |
| Durum | PLANLANMIŞ |

---

### 7.2 Donanım Projeleri

#### 7.2.1 Class AB Amplifikatör (K16)
| Özellik | Değer |
|---------|-------|
| Topoloji | Class AB Darlington |
| Output Transistörleri | MJL21194 (NPN) / MJL21193 (PNP) |
| Güç | 50W/kanal @ 8Ω |
| THD | <0.005% @ 1W |
| SNR | >105dB |
| Kanal | 1-8 (modüler, her kanal bağımsız PCB) |
| Heatsink | Fischer SK53-100-SA (300×75×49mm) |
| Fan | Noctua NF-A8 PWM (80mm) |
| Koruma | KSD301 thermal cutoff, DC offset koruma rölesi |
| PCB | 6-layer, 200×100mm, 2oz copper, IPC Class 3 |
| Durum | TASARIM AŞAMASINDA (ADR-089 Draft) |

#### 7.2.2 Güç Kaynağı — ±35V LM5122 Boost (K17)
| Özellik | Değer |
|---------|-------|
| Topoloji | LM5122 Dual Interleaved Boost |
| Giriş | 6S LiPo (22.2V nominal) veya 19-24V DC adapter |
| Çıkış | ±35V simetrik |
| Güç | 800W |
| Verimlilik | %96 |
| Koruma | UVP, OVP, OCP, OTP |
| Kapasitör | 2200μF × 4 (low-ESR) |
| Bobin | 33μH × 2 (toroid) |
| Durum | TASARIM AŞAMASINDA |

#### 7.2.3 Termal Tasarım (K18)
| Özellik | Değer |
|---------|-------|
| Heatsink | Fischer SK53-100-SA (300×75×49mm, alüminyum ekstrüzyon) |
| Termal Direnç | 0.42°C/W (heatsink), toplam <0.8°C/W |
| Fan | Noctua NF-A8 PWM (80mm, 2000rpm max) |
| Thermal Cutoff | KSD301 (97°C) |
| Sıcaklık Hedefi | <85°C (tam yükte) |
| Fan Profili |PWM: 30°C altı sessiz, 30-70°C lineer artış, 70°C+ tam hız |
| Durum | TASARIM AŞAMASINDA |

#### 7.2.4 XMOS XU316 USB Audio
| Özellik | Değer |
|---------|-------|
| Chip | XMOS XU316 |
| Arayüz | USB Audio Class 2.0 |
| Çözünürlük | 32-bit / 384kHz |
| Kanal | 8 out + 8 in |
| Desteği | ASIO, WASAPI, CoreAudio |
| Durum | BİLEŞEN SEÇİMİ TAMAM |

#### 7.2.5 PCM3168A ADC
| Özellik | Değer |
|---------|-------|
| Chip | Texas Instruments PCM3168A |
| Tür | 6ch ADC |
| Çözünürlük | 24-bit / 192kHz |
| SNR | 112dB |
| Kullanım | Stüdyo giriş, enstrüman kaydı |
| Durum | BİLEŞEN SEÇİMİ TAMAM |

#### 7.2.6 AK4458 DAC
| Özellik | Değer |
|---------|-------|
| Chip | Asahi Kasei AK4458 |
| Tür | 8ch DAC |
| Çözünürlük | 32-bit / 768kHz |
| THD+N | -112dB |
| Kullanım | 8.1 Surround çıkış |
| Durum | BİLEŞEN SEÇİMİ TAMAM |

---

### 7.3 Altyapı Projeleri

#### 7.3.1 18 BCNF Veritabanı
| Veritabanı | Amaç | Tablo Sayısı |
|------------|------|-------------|
| coremusic_auth | Users, roles, sessions, tokens | ~12 |
| coremusic_user | Profiles, preferences, history | ~8 |
| coremusic_musics | Songs, artists, genres, lyrics | ~15 |
| coremusic_albums | Album collections, discs | ~6 |
| coremusic_playlist | Playlists, collaborators | ~8 |
| coremusic_catalog | Reference data | ~10 |
| coremusic_logs | Audit trail, analytics | ~8 |
| coremusic_media | Device sync, file metadata | ~7 |
| coremusic_system | Settings, config, cache | ~12 |
| coremusic_social | Comments, shares, activity | ~10 |
| coremusic_wireless | WiFi + Bluetooth networks | ~5 |
| coremusic_ai | Preference profiles, recommendations | ~6 |
| coremusic_api | API keys, rate limits, webhooks | ~7 |
| coremusic_cms | Pages, blog, tags, FAQs | ~9 |
| coremusic_download | Download queue, history | ~6 |
| coremusic_neva | EQ presets, DSP settings | ~8 |
| coremusic_studio | Sessions, tracks, presets | ~7 |
| coremusic_patch | Schema versions, migrations | ~4 |
| **TOPLAM** | | **~156 tablo** |

#### 7.3.2 Middleware Pipeline (10 Adım)
```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

#### 7.3.3 7 Backend Servis
| Servis | Port | Protokol | Amaç |
|--------|------|----------|------|
| Control | 81 | HTTP | Auth, session, RBAC |
| Media | 5000/6000 | HTTP | Library, metadata, streaming |
| Audio | 9741/9742 | REST/WS | Player, DSP, mixer, EQ |
| Device | — | BLE/WiFi/USB | Bluetooth, WiFi, USB |
| Network Audio | — | WebRTC/P2P | Streaming, multi-room |
| AI | — | Internal | Recommendations |
| Download | 3001 | HTTP/WS | Deezer/YouTube indirme |

---

## 8. Proje Bağımlılık Haritası

```
                         ┌─────────────────┐
                         │   NevaEngine     │
                         │   (C++20 Core)   │
                         └────────┬────────┘
                                  │
                    ┌─────────────┼─────────────┐
                    │             │             │
              ┌─────┴─────┐ ┌────┴────┐ ┌─────┴─────┐
              │ DSP Engine│ │Player   │ │Streaming  │
              │           │ │(NevaPl) │ │Service    │
              └─────┬─────┘ └────┬────┘ └─────┬─────┘
                    │             │             │
                    └─────────────┼─────────────┘
                                  │
                    ┌─────────────┼─────────────┐
                    │             │             │
              ┌─────┴─────┐ ┌────┴────┐ ┌─────┴─────┐
              │Download   │ │Web      │ │Mobile     │
              │Service    │ │Platform │ │Companion  │
              └───────────┘ └────┬────┘ └───────────┘
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
              ┌─────┴─────┐ ┌───┴───┐ ┌─────┴─────┐
              │Class AB   │ │XMOS   │ │PCM3168A/  │
              │Amplifier  │ │XU316  │ │AK4458     │
              │(K16)      │ │       │ │           │
              └─────┬─────┘ └───────┘ └───────────┘
                    │
              ┌─────┴─────┐
              │±35V LM5122│
              │Power Sup. │
              │(K17)      │
              └───────────┘
```

---

## 9. Fazlama Stratejisi

### Faz 1 — MVP (Ay 1-12): Yazılım Çekirdeği
**Hedef:** Temel platformun çalışır demo'su
- Auth + Music Paneli (Ay 3)
- SPA Router + API Gateway (Ay 4)
- NevaEngine v1.0 — 2.0 stereo (Ay 6)
- Download Service (Ay 7)
- 18 BCNF DB Migration (Ay 8)
- Multi-Room v1 (Ay 10)
- Car Paneli v1 (Ay 11)
- Public Beta (Ay 12)

### Faz 2 — Premium (Ay 13-24): Donanım + Premium Özellikler
**Hedef:** Donanım entegrasyonu ve premium abonelik
- 8.1 Surround (Ay 14)
- AI Theme Maker (Ay 15)
- Class AB Prototip (Ay 16)
- XMOS Entegrasyonu (Ay 18)
- DSP Engine v1.0 (Ay 20)
- Studio Paneli (Ay 22)
- Premium Abonelik Başlatma (Ay 24)

### Faz 3 — Professional (Ay 25-36): Global Ölçek
**Hedef:** Kurumsal satış ve global dağıtım
- OEM Otomotiv Anlaşmaları (Ay 28)
- Global Donanım Satışı (Ay 30)
- AI Müzik Asistanı (Ay 32)
- Stüdyo Sertifikasyonu (Ay 34)
- IPO Hazırlığı (Ay 36)

---

## 10. Kaynak İhtiyaçları

### 10.1 Donanım Kaynakları

| Kaynak | Adet | Amaç |
|--------|------|------|
| RPi 5 (8GB) | 5 | Car + Home + Test |
| XMOS XU316 Dev Board | 3 | USB Audio prototip |
| Fischer SK53 Heatsink | 8 | Class AB termal test |
| oscilloskop | 1 | Sinyal analizi |
| Multimetre (6.5 digit) | 2 | Hassas ölçüm |
| Lehim istasyonu | 2 | PCB montaj |
| PSPICE / LTSpice | Lisans | Devre simülasyonu |

### 10.2 Yazılım Kaynakları

| Kaynak | Amaç |
|--------|------|
| PHP 8.4 Runtime | Backend geliştirme |
| Node.js LTS | Download service |
| MySQL 9 | Veritabanı |
| Docker | Container deployment |
| GitHub Actions | CI/CD |
| Visual Studio 2022 | C++ geliştirme |
| CLion | C++ IDE |
| PhpStorm | PHP IDE |

### 10.2 İnsan Kaynakları (Hedef)

| Rol | Sayı | Sorumluluk |
|-----|------|------------|
| Full-Stack Developer | 1 | Backend + Frontend + DevOps |
| C++ Audio Engineer | 1 | NevaEngine + DSP |
| Hardware Engineer | 1 | Class AB + Güç kaynağı |
| UI/UX Designer | 1 (yarı zamanlı) | Arayüz tasarımı |
| QA Engineer | 1 (yarı zamanlı) | Test ve kalite |

---

## 11. Test Stratejisi

| Modül | Framework | Minimum Coverage | Hedef Coverage |
|-------|-----------|-----------------|----------------|
| Backend (PHP) | PHPUnit 11 | ≥80% | ≥90% |
| Frontend (JS) | Vitest | ≥80% | ≥90% |
| Audio Engine (C++) | Google Test | ≥80% | ≥90% |
| Download Service | Vitest | ≥80% | ≥90% |
| Donanım | Manual + Automated | — | — |

---

## 12. Deployment Stratejisi

### 12.1 Ortam Mimarisi

| Ortam | Amaç | Altyapı |
|-------|------|---------|
| Development | Yerel geliştirme | PHP built-in server (port 81), Node.js (port 3001) |
| Staging | Entegrasyon testi | Docker Compose, MySQL 9, Redis |
| Production | Canlı ortam | Docker + Nginx, SSL, CDN |
| CI/CD | Otomasyon | GitHub Actions, Playwright, Vitest |

### 12.2 Deployment Pipeline

```
[Git Push] → [GitHub Actions] → [Lint + Test] → [Build] → [Deploy Staging]
                                                          ↓
                                                     [Smoke Test]
                                                          ↓
                                                     [Deploy Production]
                                                          ↓
                                                     [Health Check]
```

### 12.3 rollback Stratejisi

| Senaryo | rollback Adımı |
|---------|----------------|
| Database migration hatası | Migration rollback + eski schema |
| Yeni feature hatası | Feature flag ile devre dışı bırakma |
| Güvenlik açığı | Hotfix branch + acil deployment |
| Performans düşüklüğü | Scaling up + cache temizleme |

---

## 13. Monitoring ve Observability

### 13.1 İzleme Metrikleri

| Metrik | Araç | Eşik Değeri |
|--------|------|-------------|
| Uptime | Prometheus + Grafana | %99.9 altına düşme |
| API yanıt süresi | Application Insights | p95 >200ms |
| Hata oranı | Sentry | %1 üzeri 5xx |
| CPU kullanımı | Node Exporter | %80 üzeri |
| Bellek kullanımı | Node Exporter | %85 üzeri |
| Disk kullanımı | Node Exporter | %90 üzeri |
| Aktif bağlantı | Prometheus | Normalin 3x üzeri |

### 13.2 Alert Kuralları

| Alert | Seviye | Aksiyon |
|-------|--------|---------|
| Uptime %99.9 altına düştü | CRITICAL | Derhal müdahale |
| API p95 >500ms | WARNING | Performans analizi |
| Hata oranı %5 üzeri | CRITICAL | Kod inceleme |
| Disk %95 dolu | WARNING | Temizlik / genişletme |
| Güvenlik açığı tespit | CRITICAL | Hotfix + pentest |

---

## 14. CI/CD Pipeline Detayı

### 14.1 GitHub Actions Workflow

```yaml
# .github/workflows/ci.yml
name: CoreMusic CI/CD
on: [push, pull_request]
jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: PHP Lint
        run: vendor/bin/phpcs
      - name: JS Lint
        run: npx eslint src/
  test:
    needs: lint
    runs-on: ubuntu-latest
    services:
      mysql: image: mysql:9
    steps:
      - name: PHPUnit
        run: vendor/bin/phpunit --coverage-clover
      - name: Vitest
        run: npx vitest run --coverage
  build:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Docker Build
        run: docker build -t coremusic:$GITHUB_SHA .
  deploy-staging:
    needs: build
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Staging
        run: docker compose -f docker-compose.staging.yml up -d
```

### 14.2 Test automation

| Test Türü | Araç | Çalışma Zamanı | Hedef |
|-----------|------|---------------|-------|
| Unit Test | PHPUnit + Vitest | Her push | ≥80% coverage |
| Integration Test | PHPUnit + Vitest | Her PR | ≥70% coverage |
| E2E Test | Playwright | Haftalık | Kritik user journeys |
| Security Scan | SonarQube | Her PR | Sıfır critical |
| Performance Test | k6 | Haftalık | p95 <200ms |

---

## 15. Backup ve Disaster Recovery

### 15.1 Backup Stratejisi

| Veri | Yöntem | Sıklık | Saklama |
|------|--------|--------|---------|
| MySQL DB | mysqldump + incremental | Günlük | 30 gün |
| Media dosyaları | rsync + snapshot | Haftalık | 90 gün |
| Config dosyaları | Git | Her commit | Süresiz |
| Log dosyaları | Log rotation | Günlük | 180 gün |
| Redis dump | RDB + AOF | Saatlik | 7 gün |

### 15.2 Recovery Planı

| Senaryo | RTO | RPO | Adım |
|---------|-----|-----|------|
| DB silinmesi | 1 saat | 24 saat | Backup'tan geri yükle |
| Sunucu çökmesi | 30 dakika | 0 | Docker restart + IP değişikliği |
| Veri bozulması | 2 saat | 24 saat | Point-in-time recovery |
| Güvenlik ihlali | 15 dakika | 0 | Isolate + investigate + restore |
| Doğal afet | 4 saat | 24 saat | Cross-region backup |

---

## 16. Dokümantasyon Stratejisi

| Doküman | Format | Güncelleme | Sorumlu |
|---------|--------|-----------|---------|
| API Dokümantasyonu | OpenAPI 3.1 | Her endpoint değişikliğinde | Backend Architect |
| Mimari Kararlar | ADR | Her mimari kararda | Vault Steward |
| Kullanıcı Kılavuzu | Markdown | Her release'de | UI Designer |
| Geliştirici Kılavuzu | Markdown | Sürekli | Tüm ekip |
| Deployment Kılavuzu | Markdown | Altyapı değişikliğinde | DevOps Engineer |
| Güvenlik Raporu | PDF | Üç aylık | Security Engineer |

---

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 17 |
| Total Lines | 500+ |
| Cross References | 7 (Zorunlu Bağlantılar) |
| Software Projects | 13 (detaylı) |
| Hardware Projects | 6 (detaylı) |
| Infrastructure | 3 (detaylı) |
| Target Users | 6 |
| Sectors | 10 (subdomain) |
| Phase Milestones | 18 (3 faz × 6 milestone) |
| Dependencies | Full map |
| Deployment | 4 ortam + pipeline |
| Monitoring | 7 metrik + 5 alert |
| CI/CD | GitHub Actions workflow |
| Backup | 5 veri türü + 5 senaryo |
| Documentation | 6 doküman türü |

---

**Authority:** Bayram Ali / Vault Steward
**Kaynak Doküman:** Freelancer Technical Documentation v1.0 (CoreMusic: Software Audio Hardware AI)
**Last Updated:** 2026-09-23
**Version:** 3.0.0
**Mode:** Red Team · Human Mode · Truth Mode
