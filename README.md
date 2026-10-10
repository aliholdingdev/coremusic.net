# 🎵 CoreMusic

> *"Hayatın ritmi sende gizli, müziğinle parla!"*  
> **"Aynı Müzik Her Yerde Seninle" — Sınırların ötesinde bir müzik deneyimi.**  
> *Software · Audio · Hardware · AI — Version 1.0*

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES2022%20Vanilla-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://tc39.es/ecma262/) [![C++](https://img.shields.io/badge/C++-20%20NevaEngine-00599C?style=flat&logo=cplusplus&logoColor=white)](https://isocpp.org/) [![MySQL](https://img.shields.io/badge/MySQL-9%20(18%20BCNF)-4479A1?style=flat&logo=mysql&logoColor=white)](https://dev.mysql.com/)
[![Architecture](https://img.shields.io/badge/Architecture-21%20Layers%20(K000-K020)-blue)](.ai/architecture/00-master-index.md) [![License](https://img.shields.io/badge/License-Proprietary-red)](#lisans) [![Status](https://img.shields.io/badge/Status-Aktif%20Geli%C5%9Ftirme-brightgreen)](#proje-durumu)

**🔍 Web Verification**: PHP 8.4 · C++20 · MySQL 9 · JUCE 9 · ASIO SDK 2.3.4 · PCM3168A · AK4458 · LM5122 — all verified 2026-10-07.

## 📋 İçindekiler

- [1. Proje Tanımı ve Vizyon](#1-proje-tanımı-ve-vizyon)
- [2. Hangi Sorunları Çözer? (Pazar Çözüm Matrisi)](#2-hangi-sorunları-çözer-pazar-çözüm-matrisi)
- [3. Temel Yetenekler (10 Ana Başlık)](#3-temel-yetenekler-10-ana-başlık)
- [4. Sektörel Çözümler ve Subdomain Ağı](#4-sektörel-çözümler-ve-subdomain-ağı)
- [5. Hedef Kullanıcı Kitleleri](#5-hedef-kullanıcı-kitleleri)
- [6. Mimari Yapı (21 Katman — K000-K020)](#6-mimari-yapı-21-katman--k000-k020)
- [7. C++20 Neva Engine ve Ses Donanımı](#7-c20-neva-engine-ve-ses-donanımı)
- [8. Teknoloji Yığını](#8-teknoloji-yığını)
- [9. Kurulum ve Geliştirme](#9-kurulum-ve-geliştirme)
- [10. Single Source of Truth (Vault .ai/)](#10-single-source-of-truth-vault-ai)
- [11. 🔗 .ai/ Vault Dosyaları Bağlantıları](#11--ai-vault-dosyaları-bağlantıları)
- [12. AI Agent & Skill Ekosistemi](#12-ai-agent--skill-ekosistemi)
- [AI Agent Boot Özeti (Master Engineering System)](#ai-agent-boot-özeti-master-engineering-system)
- [Harici Kaynaklar (Ek — harici linkler)](#harici-kaynaklar-harici-linkler--kurulu-skill-değil-indirme-kaynağıdır)

## 1. Proje Tanımı ve Vizyon

### 1.1 CoreMusic Nedir?
**CoreMusic**, müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya dağıtımı süreçlerini tek bir platform altında birleştiren **kurumsal dijital ses ve medya ekosistemidir**.

Trilyon dolarlık küresel dijital medya, otomotiv ses sistemleri ve tüketici elektroniği pazarındaki yapısal açıkları kapatmak ve doğrudan yüksek kârlılığa dönüştürmek amacıyla geliştirilmiş kurumsal seviyede bir **Ticari Dijital Medya Ekosistemi ve Gelir Platformudur**.

Sıradan müzik çalarların sunduğu basit dosya oynatma deneyiminin ötesine geçerek; çevrim içi bulut akışı ve internet bağlantısı olmadan çalışabilen **Offline-First** mimarisi sayesinde kullanıcının FLAC, WAV ve MP3 formatındaki ses koleksiyonunu tam mülkiyet altında tutmasını sağlar.

### 1.2 Temel Felsefemiz
* **Mülkiyet ve Özgürlük:** Kiralama modellerini ortadan kaldırır. Kullanıcının sahip olduğu kayıpsız ses koleksiyonu kalıcı, bağımsız ve ilişkisel bir dijital varlık olarak korunur; telif veya lisans iptalleriyle arşivden asla silinmez.
* **Kesintisiz Bütünleşik Yaşam Deneyimi (Handoff):** Salonda başlatılan bir parça, arabaya binildiğinde (`car.coremusic.net`) veya iş istasyonuna geçildiğinde (`studio.coremusic.net`) tek bir milisaniye dahi duraksamadan, aynı akustik profille devam eder.

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

## 5. Hedef Kullanıcı Kitleleri

1. **🎧 Bireysel Kullanıcılar:** Kolay kullanımlı şık arayüz, kişisel arşiv yönetimi, AI destekli müzik keşfi.
2. **🎵 Hi-Fi / Audiophile:** 32-bit float kayıpsız ses (FLAC/WAV), ASIO/WASAPI desteği, 31-band EQ, saf analog amfi çıkışı.
3. **🎛️ Profesyonel Stüdyo:** 8.1 surround ses izleme, <10ms sinyal gecikmesi, EBU R128 ses şiddeti ölçümü, mastering araçları.
4. **🚗 Araç Kullanıcıları:** Güvenli sürüş için optimize edilmiş dokunmatik arayüz, tünellerde kesilmeyen offline yerel akış.
5. **🏠 Ev Medya Kullanıcıları:** Raspberry Pi 5 ev sunucusu, odalar arası senkronize ses (Multi-Room), Smart TV Ambient Aura.
6. **💻 Geliştirici / Freelancer:** Açık mimari dokümantasyonu, REST/WebSocket API'ler, modüler sürücü şablonları.

---

## 6. Mimari Yapı (21 Katman — K000-K020)

CoreMusic, **K000 (İşletim Sistemi)'den K020 (Üretim)'e** uzanan 21 dikey katman ve 6 alan etiketi (A0–A5) üzerine inşa edilmiştir.
**SSOT:** [`.ai/architecture/00-master-index.md`](.ai/architecture/00-master-index.md) (v2.0.0, 2026-10-09) — katman tablosu §1 · topoloji §2 · durum §3 · makro eşleme §8.

```text
===========================================================================
|                    COREMUSIC 21 KATMANLI MİMARİ                          |
|                    K000 - K020  |  6 ALAN ETİKETİ (A0-A5)                 |
|~ A5 — HEDEF DONANIM (K016-K020, makro K0-K4'e girmez) ~~~~~~~~~~~~~~~~~~|
|  K020: ÜRETİM        -- ADR-064                              [PLANNED]   |
|  K019: PCB           -- ADR-063                              [PLANNED]   |
|  K018: TERMAL        -- (başlıklı ADR YOK)                  [PLANNED]   |
|  K017: GÜÇ KAYNAĞI   -- ADR-089 (±35V LM5122)               [PLANNED]   |
|  K016: AMPLİFİKATÖR  -- ADR-089/090 (Class AB 8x50W)        [PLANNED]   |
|~ A4 — VERİ / ENTEGRASYON ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|
|  K015: MEDYA         -- ADR-026/027/028/092                 [IMPLEMENTED]|
|  K014: AĞ            -- ADR-004/009/012/016/021/043/047     [IMPLEMENTED]|
|  K013: CI/CD         -- ADR-082                             [IMPLEMENTED]|
|  K012: İZLEME        -- ADR-006                             [IMPLEMENTED]|
|~ A3 — SUNUM ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|
|  K011: UX            -- ADR-001/004/018/044/045/046/048/093 [IMPLEMENTED]|
|  K010: UYGULAMA      -- ADR-056/085/086                     [IMPLEMENTED]|
|~ A2 — SERVİS & API ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|
|  K009: API           -- ADR-004/020/084/009/016/021         [IMPL (OpenAPI PLANNED)]|
|  K008: SERVİSLER     -- ADR-039/085/086                     [IMPL (kısmi)]|
|~ A1 — GÜVENLİK ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|
|  K007: MIDDLEWARE    -- ADR-008/010/012/013/020/056/094     [IMPLEMENTED]|
|  K006: GÜVENLİK      -- ADR-008/010/011/012/013/020/022/034 |
|                        /043/047/052/056/058/059/094/095     [IMPLEMENTED]|
|~ A0 — TEMEL (OS · DONANIM · SÜRÜCÜ · SES · YZ · VERİ) ~~~~~~~~~~~~~~~~~~|
|  K005: VERİ YÖNETİMİ -- ADR-002/003/014/033/040/041/050/081 [IMPLEMENTED]|
|  K004: YAPAY ZEKA   -- ADR-030/035/036/049/075             [PLANNED]   |
|  K003: SES MOTORU    -- ADR-017/019/025/062/037             [PLANNED]   |
|  K002: SÜRÜCÜ        -- ADR-032/038/017                     [PLANNED]   |
|  K001: DONANIM       -- ADR-061/038                         [PLANNED]   |
|  K000: İŞLETİM SİSTEMİ -- ADR-015/082/085                  [IMPLEMENTED]|
|  Durum: IMPLEMENTED 12 · PLANNED 9                          (2026-10-09)|
===========================================================================
```

> **⚠️ Çelişki kaydı (2026-10-10):** Eski "1095 Bileşen" sayımı bu yapının yerini almıştır ve
> `.ai/architecture/00-master-index.md` §1 ile hizalanmıştır. ADR-096/097'nin "6.000 katman K000-K5999"
> modeli **hedef**tir, diskte uygulanmamıştır (21 K-dizini / 125 md ölçüldü).


## 7. C++20 Neva Engine ve Ses Donanımı

CoreMusic'in kalbinde, işletim sisteminin sesi bozan katmanları baypas eden C++20 Neva Engine ve ayrık analog donanım amfisi yer alır:

* **32-Bit Float DSP:** 15 aşamalı filtre zinciri (InputGain → Gate → HPF → LPF → 31-Band EQ → Dynamics → Delay → Reverb → True Peak Limiter).
* **Discrete Class AB Amplifikatör:** 8 kanal bağımsız modüler yapı, MJL21194/MJL21193 tamamlayıcı çıkış çifti, 50W RMS @ 8Ω (80W @ 4Ω), THD+N <%0.005.
* **±35V LM5122 Dual Boost Güç Kaynağı:** 6S LiPo (22.2V) veya 19-24V DC laptop adaptörü girişi; %96 tepe verimlilik, sıfır 50Hz şebeke gürültüsü sağlayan **DC-ONLY** güç mimarisi.
* **Ses Kartı Köprüsü:** XMOS XU316 USB Audio Class 2.0 işlemcisi, PCM3168A 8-kanal DAC, AK4458 8-kanal DAC.

## 8. Teknoloji Yığıdı

* **Backend:** PHP 8.4+ (Strict types, PDO, PSR-15 Middleware), Node.js / TypeScript (Download microservice)
* **Frontend:** Vanilla JavaScript (ES2022 SPA Router), ITCSS 9-Layer + BEM CSS, Glassmorphism UI
* **Ses Motoru:** Modern C++20, JUCE 9 AudioProcessor, Steinberg ASIO SDK, Windows WASAPI Exclusive, Linux ALSA
* **Veritabanı & Önbellek:** MySQL 9 (18 BCNF Normalleştirilmiş Veritabanı, 156 Tablo), Redis, APCu
* **Güvenlik & Kriptografi:** Argon2id parola özeti, AES-256-GCM Credential Vault, CSP Nonce, CSRF koruması

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

## 10. Single Source of Truth (Vault .ai/)

CoreMusic projesinin tüm mimari kararları, anayasası, kuralları ve detaylı dokümanları `.ai/` dizinindeki **Vault** içerisinde toplanmıştır:

| Doküman | Yol | Açıklama |
|:---|:---|:---|
| **Vizyon Belgesi** | [`.ai/VISION.md`](.ai/VISION.md) | Proje vizyonu, pazar krizi, mülkiyet felsefesi ve stratejik hedefler |
| **Proje Tanımı** | [`.ai/PROJECTS.md`](.ai/PROJECTS.md) | 10 temel yetenek, sektörler, kullanıcı profilleri ve pazar çözümleri |
| **AI Anayasası** | [`.ai/CLAUDE.md`](.ai/CLAUDE.md) | 16 Hard Guardrail, mühendislik standartları ve kurallar |
| **Master İndeks** | [`.ai/index.md`](.ai/index.md) | Vault navigasyon — 14 kök boot + 5 alt sayfa (`index/01-05`) |
| **Katman Master İndeksi** | [`.ai/architecture/00-master-index.md`](.ai/architecture/00-master-index.md) | 21 katman K000-K020, durum (12 IMPLEMENTED / 9 PLANNED), ADR eşlemesi |
| **Bağılılık Matrisi** | [`.ai/architecture/katman-baglilik-matrisi.md`](.ai/architecture/katman-baglilik-matrisi.md) | Katmanlar arası bağımlılık kenarları |
| **Mimari Plan** | [`.ai/architecture/coremusic-mimari-plani.md`](.ai/architecture/coremusic-mimari-plani.md) | 50 yıllık mimari ilkelere dayalı sıfırdan plan |
| **Devre Şeması** | [`.ai/architecture/electronics/amfii/amplifier-classab-circuit.md`](.ai/architecture/electronics/amfii/amplifier-classab-circuit.md) | Class AB 50W amfi devresi, Mermaid şeması ve test noktaları |

## 11. 🔗 .ai/ Vault Dosyaları Bağlantıları

> **Veri Doğrulama**: Tüm teknoloji iddiaları aşağıda listelenen kaynaklara göre 2026-10-07'de doğrulanmıştır. ✅ = web ile doğrulanmış.

| Bu Dosya | Anlamı | 🔗 Bağlantı |
|:---|:---|:---|
| **`.ai/CLAUDE.md`** | AI Anayasası — 16 Hard Guardrail, 34 bölüm | `@.ai/CLAUDE.md` |
| **`.ai/AGENTS.md`** | Agent Registry SSOT — 11 agent, routing, handover | `@.ai/AGENTS.md` |
| **`.ai/WORKFLOW.md`** | Vault süreçleri — fazlar, ADR lifecycle | [`.ai/WORKFLOW.md`](.ai/WORKFLOW.md) |
| **`.ai/CONTEXT.md`** | Vault klasör yapısı, envanter, bağlam | [`.ai/CONTEXT.md`](.ai/CONTEXT.md) |
| **`.ai/SYNC.md`** | Sürekli güncelleme döngüsü SSOT — baş/orta/kapanış | [`.ai/SYNC.md`](.ai/SYNC.md) |
| **`.ai/CHECKLIST.md`** | Session checklist — §A/§B/§C + hedef set 26 | [`.ai/CHECKLIST.md`](.ai/CHECKLIST.md) |
| **`.ai/VISION.md`** | Vizyon ve yol haritası | [`.ai/VISION.md`](.ai/VISION.md) |
| **`.ai/PROJECTS.md`** | Proje envanteri, 10 yetenek | [`.ai/PROJECTS.md`](.ai/PROJECTS.md) |
| **`.ai/brain.md`** | Mimari kararlar, ADR'ler | [`.ai/brain.md`](.ai/brain.md) |
| **`.ai/MEMORY.md`** | Session hafızası | [`.ai/MEMORY.md`](.ai/MEMORY.md) |
| **`.ai/log.md`** | Audit trail (append-only) | [`.ai/log.md`](.ai/log.md) |
| **`.ai/engine.md`** | Orkestrasyon motoru | [`.ai/engine.md`](.ai/engine.md) |
| **`.ai/ULTRA-THINKING.md`** | Ultra düşünme protokolü | [`.ai/ULTRA-THINKING.md`](.ai/ULTRA-THINKING.md) |
| **`.ai/glossary.md`** | Terim sözlüğü | [`.ai/glossary.md`](.ai/glossary.md) |
| **`.ai/index.md`** | Master katalog | [`.ai/index.md`](.ai/index.md) |
| **`.ai/keys.md`** | Keyword haritası | [`.ai/keys.md`](.ai/keys.md) |
| **`.ai/ROLE.md`** | Rol tanımı | [`.ai/ROLE.md`](.ai/ROLE.md) |
| **`.ai/.templates/`** | Şablon registry | [`.ai/.templates/index.md`](.ai/.templates/index.md) |

> **Boot Protocol**: AI agent'ları root `CLAUDE.md` → `AGENTS.md` → `README.md` → `WORKFLOW.md` okur, sonra ihtiyaç anında `@.ai/` dosyalarını okur (Guardrail #2: Vault First).

---

## 12. AI Agent & Skill Ekosistemi

CoreMusic'te AI ajanları **Skill Usage Mandate** ile çalışır. Mandate özeti (5 madde):

1. Her görev başında `CLAUDE.md` §Skill Registry + available-skills listesi taranır, eşleşme kontrol edilir.
2. Eşleşme varsa **ilk işlem** Skill tool ile o skill'i yüklemektir; eşleşme varken skill'siz işlem başlatmak yasaktır.
3. Birden fazla eşleşen varsa en dar (domain-specific) skill önce yüklenir.
4. İstisna: kullanıcı açıkça "skill kullanma" derse veya görev skill'lerle ilgisizse (sohbet, aritmetik); belirsizse 1 kısa soru sorulur.
5. Yetenek yoksa kopyala-yapıştır yapılmaz; `skill-maker` ile skill üretilir (Guardrail #16 + v3.0 şeması).

* **Kayıt defteri (SSOT):** [`CLAUDE.md`](CLAUDE.md) §Skill Registry — **13 proje skill'i** (`.claude/skills/`, 2026-10-10 disk ölçümü). ⚠️ Eski "12 proje + 5 global = 17" kaydı düzeltildi: `C:\.claude\skills\` **diskte YOK** (Test-Path = False) → 0 global skill. `learning-prompts` kayıtsızdı, Registry'ye eklendi. Bu dosyada liste tekrarlanmaz; ayrıca ~340 üçüncü-parti global skill grubu harness available-skills listesiyle gelir.
* **Uygulama (2 katman):** (1) yazılı mandate — kök `CLAUDE.md` §Skill Kullanım Zorunluluğu; (2) hook — `.claude/settings.json → UserPromptSubmit` her prompt'ta kısa hatırlatma + proje skill isimlerini enjekte eder.
* **Yeni skill ekleme yolu:** `skill-maker` çalıştırılır → v3.0 şeması + format otoritesi `.claude/skills/skill-maker/` (şablon + kurallar) → üretilen skill `CLAUDE.md` §Skill Registry'ye satır olarak eklenir.

---

## AI Agent Boot Özeti (Master Engineering System)

1. **Boot okuma sırası:** root `CLAUDE.md` + `AGENTS.md` + `README.md` + `WORKFLOW.md` (ve `.ai/` karşılıkları) → `.ai/.rules/**` → ilgili `.ai/**` & `architecture/**` → mevcut dosya → kod → `.ai/.rules/**` çalıştır (hata: error-recovery → dosyayı sil + yeniden yaz → tekrar; temiz: UI değişikliği var mı → evet: browser test → hayır: commit). Ayrıntı: [`WORKFLOW.md`](WORKFLOW.md) §16 · [`.ai/WORKFLOW.md`](.ai/WORKFLOW.md) §8.10.
2. **Yaşam döngüsü (15 aşama):** Understand → Discover → Research → Verify → Resolve Context → Analyze → Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document — analiz tamamlanmadan implementation başlatılmaz.
3. **Zero-Hallucination:** repository/URL/API/class/method/dependency/version/config/skill/agent/dosya/mimari kural/benchmark/güvenlik iddiası asla uydurulmaz; doğrulanamayan = `⚠️ VERIFICATION REQUIRED`, bilinmeyen = `UNKNOWN`.
4. **Final rule:** Understand → Research → Resolve → Decide → Implement → Track → Verify — riskli işlemede dur, onaysız büyük source-code değişikliği yok.
5. **MAX THINKING (anti-overthink):** [`AGENTS.md`](AGENTS.md) §5 — 7 madde; skill dosyalarında kısaltılmış 5 madde.
6. **Skill Usage Mandate:** her görev başında [`CLAUDE.md`](CLAUDE.md) §Skill Registry + available-skills taranır; eşleşen skill varsa **ilk işlem** Skill tool ile yüklemektir (Skill Usage Mandate §2 — madde 5'teki "gereksiz skill üretme" yasağıyla çelişmez).

## Harici Kaynaklar (Harici Linkler — kurulu skill değildir, indirme kaynağıdır)

Aşağıdaki depolar harici referans/kaynak listesidir; proje tarafından kurulu skill değildir (kayıt defteri: [`CLAUDE.md`](CLAUDE.md) §Skill Registry):

- https://github.com/NacioFelix/awesome-opencode
- https://github.com/weisser-dev/awesome-opencode
- https://github.com/jamait/awesome-opencode-skills
- https://github.com/agentskills/agentskills
- https://github.com/j4flmao/agent-skills
- https://github.com/JazzaAI/agent-skills
- https://github.com/iannil/skills
- https://github.com/luckys/agent-skills
- https://github.com/tars-agentic/agent-skills
- https://github.com/dotnet/skills
- https://github.com/DevExpress/agent-skills
- https://github.com/DenisSergeevitch/agents-best-practices
- https://github.com/obra/superpowers
- https://github.com/vercel-labs/agent-skills
- https://github.com/lumpinif/frontend-ui-engineering
- https://github.com/hueyexe/frontend-agent-skills
- https://github.com/Junaid-PK/frontend-design-skill
- https://github.com/shanraisshan/claude-code-best-practice
- https://github.com/davila7/claude-code-templates
- https://github.com/ykdojo/claude-code-tips
- https://github.com/subinium/awesome-claude-code
- https://github.com/dazuiba/awesome-claude-code-1
- https://github.com/jqueryscript/awesome-claude-code
- https://github.com/itgoyo/awesome-claude-code
- https://github.com/mctrinh/awesome-mcp-servers
- https://github.com/modelcontextprotocol/servers
- https://github.com/vakra-dev/awesome-ai-agents
- https://github.com/hammond01/CleanArchitecture

**Session Lifecycle:** Bu depoda her AI session'ı `.ai/CHECKLIST.md` §A/§B/§C ile yürütülür; hedef dosya seti **26'dır** (6 kök `.md` + 20 `.ai/` kök `.md` — sınıflandırma [[CHECKLIST]] §A0: CRITICAL 18 / ON-DEMAND 6 / LOG 2). Kapanışta `.workflows/vault-sync.md` Aşama 8 satır 5 ile değişen dosyalar güçlendirilir; sürekli güncelleme döngüsü `.ai/SYNC.md` (SSOT) ile yönetilir.

**Authority:** Bayram Ali / Vault Steward  
**Kaynak Doküman:** Freelancer Technical Documentation v1.0 (CoreMusic: Software Audio Hardware AI)  
**Last Updated:** 2026-10-10  
**Version:** 3.1.0  
**Mode:** Red Team · Human Mode · Truth Mode