---
title: "CoreMusic — Freelancer Teknik Dokümantasyon Kitabı"
type: technical-documentation
version: 1.0.0
status: active
authority: SSOT
date: 2026-09-15
author: Bayram Ali — Vault Steward & Project Owner
audience: Freelancer Developers (Backend, Frontend, Embedded, DevOps)
language: tr-TR
---

# CoreMusic — Freelancer Teknik Dokümantasyon

---

<div align="center">

**Software · Hardware · AI**

# CoreMusic

### Freelancer Technical Documentation

*Hayal etmenin ötesinde gezen Müzikle paralel.*

---

**Versiyon:** 1.0.0
**Tarih:** Eylül 2026
**Gizlilik:** CoreMusic Confidential

---

*Music Connects Everywhere — Music Beyond Limits*

</div>

---

<div style="page-break-after: always;"></div>

## İçindekiler

| Bölüm | Başlık | Sayfa |
|:---:|:---|:---:|
| **01** | Giriş — CoreMusic Nedir? | 1 |
| | 1.1 Core Music Tanımı | 1 |
| | 1.2 Temel Özellikler | 2 |
| | 1.3 Proje Tanıtımı & Kapsamı | 3 |
| | 1.4 Sektörel Görünüm & Pazar Analizi | 3 |
| | 1.5 Hangi Sorunun Çözümü? | 4 |
| **02** | Sistem Mimarisi | 5 |
| | 2.1 Genel Bakış (Clean Architecture + DDD) | 5 |
| | 2.2 Temel Bileşenler — L0-L6 Katmanları | 5 |
| | 2.3 Sistem İşletme Akışı | 7 |
| | 2.4 Middleware Pipeline (10 Adım) | 7 |
| | 2.5 Servis Haritası — 10 Panel & 7 Backend Servis | 8 |
| **03** | Teknoloji Yığını & Platform Desteği | 9 |
| | 3.1 Teknoloji Yığını Tablosu | 9 |
| | 3.2 Platform Tier Desteği | 10 |
| | 3.3 Deployment Modları | 10 |
| **04** | Kurulum & Geliştirme Ortamı | 11 |
| | 4.1 Sistem Gereksinimleri | 11 |
| | 4.2 Adım Adım Kurulum (6 Adım) | 11 |
| | 4.3 Geliştirme Ortamı Kurulumu | 12 |
| | 4.4 Ortam Değişkenleri (.env) | 13 |
| **05** | API Dokümantasyonu | 14 |
| | 5.1 API-First Mimari & Gateway | 14 |
| | 5.2 Kimlik Doğrulama (Hybrid JWT + Session) | 14 |
| | 5.3 Temel Endpoint'ler | 15 |
| | 5.4 Hata Kodları & Yanıt Formatları | 16 |
| **06** | Ses Motoru & Donanım Entegrasyonu | 17 |
| | 6.1 Neva Engine C++20 Çekirdek | 17 |
| | 6.2 Gerçek Zamanlı DSP Zinciri | 17 |
| | 6.3 Ses Donanımı & Güç Katı | 18 |
| | 6.4 Ses Formatları & Codec Desteği | 18 |
| **07** | Arayüz & Tasarım Sistemi | 19 |
| | 7.1 Kanonik Ekranlar & 19 PNG Mockup | 19 |
| | 7.2 Bileşen Envanteri (C01-C16) | 19 |
| | 7.3 Tasarım Tokenları & ITCSS/BEM | 20 |
| | 7.4 Responsive Tier Kuralları | 21 |
| | 7.5 Tema Motoru (ADR-044) | 21 |
| **08** | Güvenlik & Uyumluluk | 22 |
| | 8.1 Değişmez 10 Adımlı Güvenlik Hattı | 22 |
| | 8.2 Şifreleme (AES-256-GCM + Argon2id) | 22 |
| | 8.3 CSRF, CSP & Rate Limiting | 23 |
| | 8.4 Session Yönetimi (ADR-011) | 23 |
| **09** | Veritabanı Mimarisi | 24 |
| | 9.1 18 BCNF Veritabanı (156 Tablo) | 24 |
| | 9.2 SQL Kuralları & Yasaklar | 25 |
| | 9.3 Veritabanı Güvenliği (ADR-022) | 25 |
| **10** | Dağıtım & Operasyonlar | 26 |
| | 10.1 Raspberry Pi 5 Ev Medya Merkezi | 26 |
| | 10.2 Docker & Mikro Servis Mimarisi | 26 |
| | 10.3 CI/CD Pipeline & GitHub Actions | 27 |
| | 10.4 Monitoring & Health Check | 27 |
| **11** | Freelancer Geliştirme Kuralları | 28 |
| | 11.1 Hard Guardrails (17 Kural) | 28 |
| | 11.2 PHP 8.4+ Kuralları | 29 |
| | 11.3 Frontend Kuralları (No-Framework) | 29 |
| | 11.4 C++20 Ses Motoru Kuralları | 30 |
| | 11.5 Git & ADR Protokolü | 30 |
| | 11.6 Kod Örnekleri — Her Katman İçin | 31 |
| **12** | Proje Yönetimi & İletişim Protokolü | 33 |
| | 12.1 Proje Yapısı & Sprint Döngüsü | 33 |
| | 12.2 İletişim Kanalları & Workflow | 33 |
| | 12.3 Görev Dağıtımı & Onay Süreci | 34 |
| | 12.4 Freelancer Entegrasyon Adımları | 34 |
| **13** | Test Rehberi & Kalite Standartları | 35 |
| | 13.1 Test Stratejisi & Kapsama Hedefleri | 35 |
| | 13.2 PHPUnit ile Backend Testi | 35 |
| | 13.3 Vitest ile Frontend Testi | 36 |
| | 13.4 E2E Test (Playwright) | 36 |
| **14** | Sözlük & Kısaltmalar | 37 |
| **15** | Ekler | 38 |
| | 15.1 Dizin Yapısı | 38 |
| | 15.2 ADR Listesi (79 Karar) | 38 |
| | 15.3 Port Haritası | 39 |
| | 15.4 Veritabanı Tablo Envanteri | 39 |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 01
# Giriş — CoreMusic Nedir?

## 1.1 Core Music Tanımı

**CoreMusic**; müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya kontrol süreçlerini tek bir merkezi platform altında birleştirmek amacıyla geliştirilen **yeni nesil kurumsal dijital medya yönetim sistemi ve ticari ses ekosistemidir**.

CoreMusic, klasik bir müzik oynatıcısının basit dosya çalma yaklaşımını kökten reddeder. Kullanıcının sahip olduğu yerel müzik arşivlerini, yüksek çözünürlüklü dijital ses dosyalarını, fiziksel ses dönüştürücü ve amplifikatör donanımlarını, mikro servisleri ve kişisel akustik tercihlerini **tek bir ekosistem içerisinde uçtan uca yönetmesini sağlayan büyük ölçekli bir teknoloji omurgası** olarak tasarlanmıştır.

```
       [ Her Cihaz Her Zaman ]              [ Müziğin Her Yerde ]
                  \                                    /
                   ───>  COREMUSIC EKOSİSTEMİ  <───
                  /                                    \
       [ Yapay Zeka ile Akıllı ]           [ Daha Müziksel Bir Yaşam İçin ]
```

### Temel Amaç

CoreMusic'in temel amacı; farklı cihazlarda ve farklı uygulamalarda dağınık halde bulunan müzik deneyimini tek bir merkezi yapı (`api.coremusic.net`) altında toplamak; daha kontrollü, kişisel, stüdyo referansında ve kesintisiz bir ses yaşam biçimi oluşturmaktır.

### Platform Tanımı

| Özellik | Değer |
|---------|-------|
| Platform Adı | CoreMusic |
| Platform Türü | Dijital Medya Yönetim Platformu |
| Hedef Kullanıcılar | Bireysel, Profesyonel, Stüdyo, Araç İçi, Ev Medya |
| Temel Teknoloji | PHP 8.4, C++20, Vanilla JS, MySQL 9 |
| Lisans | Kapalı Kaynak |
| Versiyon | 1.0.0 |

---

## 1.2 Temel Özellikler

CoreMusic;?";
- **Hibrit Çalışma Mimarisi (Offline Cloud + Offline First):** Çevrimdışı çalışabilen, internet bağlantısı gerektiğinde sunucu ile senkronize olan modüler bir yapı.
- **Audio DSP Engine & Çekirdek Akustik:** Gerçek zamanlı ses işleme motoru (Zero-Allocation, Lock-Free). 31-Band Parametrik EQ, Reverb, Compressor, Limiter.
- **Ses İşleme Hızlılığı & Gecikme:** ASIO SDK 2.3.4 ile <10ms, WASAPI Exclusive ile <15ms gecikme.
- **7.1 Surround & LFE Hoparlör Matrisi:** 8 kanallı PCM3168A DAC, Class AB amplifikatör, LFE subwoofer yönetimi.
- **Biyoteknolojik Temalı UI & AI Destekli Theme Maker:** Cinsiyete göre dinamik tema (female→pink, male→blue, neutral→default).
- **Çapraz Cihaz Senkronizasyonu & Kesintisiz Geçiş (Handoff):** Evde başlattığın müziği arabada devam ettir, oturma odasındaki ses ayarlarını tüm cihazlara aktar.
- **Kullanıcıya Ait Emerald Uninterrupted Denetimi:** Offline-first mimari ile internet bağlantısı olmadan bile kesintisiz müzik deneyimi.
- **Merkezi Medya Deposu & Çok Kaynaklı Otomatik İndirme:** Deezer, YouTube ve özel kaynaklardan otomatik indirme, otomatik metadata çıkarma.
- **İçerik Dağıtımı (Driver + License Worker + CD/Yazıcı Entegrasyonu):** Donanım sürücü dağıtımı, lisans yönetimi, optik medya yazma.
- **Sabit Disk & Network Buçuklama (DLNA/UPnP & WebRTC):** Ev ağındaki tüm cihazlara medya dağıtımı, WebRTC ile düşük gecikmeli streaming.

---

## 1.3 Proje Tanıtımı & Kapsamı

### Bu Dokümanın Amacı

Bu belge, CoreMusic projesinde görev alacak **freelancer geliştiriciler** için hazırlanmış kapsamlı bir teknik rehberdir. Bu kitabı okuyarak:

- Projenin **vizyonunu, hedeflerini ve pazar konumunu** anlayacaksınız
- Sistem **mimarisini** (L0-L6 katmanları) ve bileşenler之间的 ilişkileri kavrayacaksınız
- **Geliştirme ortamını** nasıl kuracağınızı ve çalıştıracağınızı öğreneceksiniz
- **Kodlama standartları** ve hard guardrails'ları içselleştireceksiniz
- **API yapısını**, endpoint'leri ve veri formatlarını bileceksiniz
- **Test, deployment ve güvenlik** süreçlerine hakim olacaksınız
- **Proje yönetim** ve iletişim protokollerini bileceksiniz

### Proje Kapsamı

| Alan | Kapsam |
|------|--------|
| Backend | PHP 8.4 API, routing, middleware, servis mimarisi |
| Frontend | Vanilla JS, ITCSS, BEM, responsive, 19 PNG mockup |
| Ses Motoru | C++20 Neva Engine, DSP, ASIO, WASAPI |
| Donanım | XMOS XU316, PCM3168A, Class AB amplifikatör |
| Veritabanı | 18 BCNF MySQL veritabanı, 156 tablo |
| Güvenlik | OWASP, CSRF, CSP, AES-256-GCM, Argon2id |
| Test | PHPUnit, Vitest, Playwright, ≥80% coverage |
| DevOps | Docker, GitHub Actions, CI/CD |

---

## 1.4 Sektörel Görünüm & Pazar Analizi

### Pazar Problemi

Modern dijital ses endüstrisi, kullanıcıları mülkiyetsiz kiralama modellerine, platform tekellerine ve kayıplı sıkıştırma standartlarına mahkûm eden derin bir parçalanmışlık içindedir:

1. **Platform Bağımlılığı ve Veri Mülkiyeti Kaybı:** Kullanıcıların müzikleri farklı bilgisayarlarda, telefonlarda, taşınabilir disklerde ve bulut sürücülerinde dağınık haldedir.
2. **Kiralama Tuzağı ve Hak Sınırlamaları:** Streaming platformlarında kullanıcılar her ay abonelik öder ancak hiçbir parçanın kalıcı mülkiyetine sahip olamaz.
3. **İşletim Sistemi Ses Bozulması:** Standart işletim sistemi mikserleri ses verisini mecburi yeniden örneklemeye sokarak dinamik aralığı daraltır.
4. **Donanım ve Protokol Uyumsuzluğu:** Evdeki hi-fi sistemi, arabadaki bilgi-eğlence ekranı ve stüdyodaki referans monitörleri birbirinden bağımsız çalışır.

### CoreMusic'in Çözümü

CoreMusic, **Offline-First** mimarisi ve **Kullanıcı Veri Mülkiyeti** felsefesiyle bu pazar krizini doğrudan ölçeklenebilir bir gelir modeline dönüştürür:

- **Uçtan Uca Dikey Entegrasyon:** Veritabanı şemasından C++ ses çekirdeğine, web arayüzünden özel tasarlanmış ses kartına kadar tüm katmanlar entegre.
- **Hibrit İstemci-Sunucu:** 10 bağımsız panel ve 7 backend mikro servisi ile ölçeklenebilir yapı.
- **Tavizsiz Güvenlik:** Katı katman bağımlılıkları, 10 adımlı middleware hattı, sıfır ORM yaklaşımı.

### 4 Ayaklı Gelir Stratejisi

| # | Gelir Kanalı | Açıklama |
|---|-------------|----------|
| 1 | **B2C Donanım Satışı** | XMOS XU316 ses kartı, PCM3168A DAC, RPi5 sunucu, Class AB amfi |
| 2 | **Otomotiv OEM Lisanslama** | Araç başına lisanslı gömülü I2S/TDM yazılım çözümleri |
| 3 | **B2B Profesyonel Stüdyo Paketleri** | 8.1 surround, parametrik EQ, stüdyo iş istasyonu lisansları |
| 4 | **B2C Ekosistem Abonelikleri** | Bulut senkronizasyonu, AI analitiği, gelişmiş medya servisleri |

---

## 1.5 Hangi Sorunun Çözümü?

| Problem | CoreMusic Çözümü |
|---------|-----------------|
| Müzikler dağınık platformlarda | Merkezi kütüphane, 18 BCNF veritabanı |
| Streaming'te mülkiyet yok | Offline-First, FLAC 24/32-bit arşiv |
| İşletim sistemi ses bozulması | ASIO/WASAPI Exclusive, C++ motoru |
| Ev-araba-stüdyo uyumsuzluğu | Tek platform, multi-cihaz senkronizasyonu |
| Profesyonel ses ihtiyacı | 8.1 surround, 31-band EQ, <10ms gecikme |
| Donanım-yazılım kopukluğu | XMOS+PCM3168A entegre donanım |
| Güvenlik açıkları | OWASP, AES-256-GCM, Argon2id |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 02
# Sistem Mimarisi

## 2.1 Genel Bakış (Clean Architecture + DDD)

CoreMusic; yazılım, ses işleme, gömülü donanım ve kullanıcı arayüzü bileşenlerinin birbirinden bağımsız geliştirilebilmesini, test edilebilmesini ve ölçeklenebilmesini garanti altına alan **modüler, katmanlı bir mimari** (L0-L6) kullanır.

Sistemde sorumlulukların ayrılığı (Separation of Concerns) katı kurallarla uygulanır. Bağımlılıklar daima soyutlama yönünde tek taraflı akar:

```
L6 → L5 → L4 → L3 → L2 → L1 → L0
```

> **⚠️ Katman İhlali (Layer Violation) Yasağı:** Ters yönlü bağımlılıklar ve katman atlamalar kesinlikle yasaktır. CI/CD testlerinde otomatik olarak reddedilir.

### Mimari Prensipler

| Prensip | Açıklama |
|---------|----------|
| **Clean Architecture** | İç içe geçen daireler; bağımlılıklar içeriye doğru akar |
| **Domain-Driven Design** | İş mantığı domain modelleri etrafında şekillenir |
| **Hexagonal Architecture** | Giriş/çıkış noktaları bağımsız; iş mantığı soketlerden izole |
| **SOLID Prensipleri** | Tek sorumluluk, açık/kapalı, Liskov, arayüz ayrımı, bağımlılık tersine çevirme |
| **CQRS** | Okuma ve yazma işlemleri tamamen ayrılır |

---

## 2.2 Temel Bileşenler — L0-L6 Katmanları

```
┌────────────────────────────────────────────────────────────────────────┐
│  L6: Electronics & Hardware                                            │
│  Hardware, Firmware, Driver, DSP (XMOS XU316, TI PCM3168A, Class AB)   │
├────────────────────────────────────────────────────────────────────────┤
│  L5: Services                                                          │
│  Application Services, Real-Time Audio Engine, Use Cases               │
├────────────────────────────────────────────────────────────────────────┤
│  L4: Domain                                                            │
│  Business Rules, Core Entities, Value Objects, Domain Events           │
├────────────────────────────────────────────────────────────────────────┤
│  L3: Presentation                                                      │
│  Frontend UI, Vanilla JS, ITCSS 9-Layer, Web & Embedded Interfaces    │
├────────────────────────────────────────────────────────────────────────┤
│  L2: Routing & Gateway                                                 │
│  SPA Router, API Gateway, Controller, DTO Validation                   │
├────────────────────────────────────────────────────────────────────────┤
│  L1: Security                                                          │
│  Authentication, RBAC Session, CSRF, CSP Nonce, AES-256-GCM Vault      │
├────────────────────────────────────────────────────────────────────────┤
│  L0: Infrastructure                                                    │
│  18 BCNF Databases, Raw PDO Engine, Cache, Local Filesystem, UUID v7   │
└────────────────────────────────────────────────────────────────────────┘
```

### Katman Detayları

| Katman | Kapsam | Teknolojiler | Bağımlılık |
|--------|--------|-------------|------------|
| **L6 Electronics** | Donanım, firmware, sürücü, DSP | XMOS XU316, PCM3168A, C++20 | L6→L5 |
| **L5 Services** | Uygulama servisleri, use case'ler | CQRS, Event Bus (PSR-14) | L5→L4 |
| **L4 Domain** | İş kuralları, varlık nesneleri | DDD Entities, Value Objects | L4→L3 |
| **L3 Presentation** | Frontend, UI, DOM | Vanilla JS ES6+, ITCSS, BEM | L3→L2 |
| **L2 Routing** | SPA router, middleware, controller | PHP 8.4 PageRouter, DTO | L2→L1 |
| **L1 Security** | Session, auth, CSRF, CSP | Argon2id, AES-256-GCM | L1→L0 |
| **L0 Infrastructure** | Database, cache, filesystem | PDO MySQL 9, APCu, UUID v7 | — |

### Katman Bağımlılık Matrisi

| Kaynak → Hedef | İzinli mi? |
|-----------------|------------|
| L6 → L5 | ✅ Evet |
| L5 → L4 | ✅ Evet |
| L4 → L3 | ✅ Evet |
| L3 → L2 | ✅ Evet |
| L2 → L1 | ✅ Evet |
| L1 → L0 | ✅ Evet |
| L0 → L2/L3 | ❌ HAYIR |
| L1 → L3 | ❌ HAYIR |
| L3 → L0 | ❌ HAYIR |

> **Layer Violation İhlali:** Tespit edilirse derhal revert + log CRITICAL.

---

## 2.3 Sistem İşletme Akışı

```
Kullanıcı İsteği (Web/Mobil/Gömülü)
    │
    ▼
[L3] Frontend — Vanilla JS + ITCSS
    │  API Client aracılığıyla HTTP isteği
    ▼
[L2] API Gateway — api.coremusic.net
    │  Routing, DTO validation, rate limit
    ▼
[L1] Middleware Pipeline (10 adım)
    │  OriginCheck → Cors → RateLimiter → SecurityHeaders
    │  → SessionManager → Csrf → BypassAuth → Auth
    │  → Permission → Validation
    ▼
[L2] Controller → Use Case → Repository
    │
    ├──→ [L0] MySQL 9 (PDO Prepared Statement)
    │
    ├──→ [L0] APCu Cache
    │
    └──→ [L5] Neva Engine (C++20 Audio)
         │
         └──→ [L6] XMOS XU316 → PCM3168A → Class AB Amp
```

---

## 2.4 Middleware Pipeline (Değişmez — ADR-010/011/012/013/022)

```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS) | — |
| 2 | **Cors** | CORS header yönetimi | — |
| 3 | **RateLimiter** | APCu tabanlı, 60 req/60s | 60s |
| 4 | **SecurityHeaders** | CSP strict-dynamic, X-Frame-Options, HSTS | — |
| 5 | **SessionManager** | Session başlatır, CSP nonce'u session'a kaydeder | 3600s |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) | — |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), prod'da devre dışı | — |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session) | — |
| 9 | **Permission** | RBAC yetki kontrolü (regular/premium/studio/car/admin) | — |
| 10 | **Validation** | Request/DTO validasyonu | — |

> **⚠️ Kritik Not:** CSP nonce üretimi SecurityHeaders (#4) içindedir. SessionManager (#5) bu nonce'u session'a kaydeder. Sıra değiştirilirse CSP bozulur. **Middleware sırası DEĞİŞTİRİLEMEZ.**

---

## 2.5 Servis Haritası — 10 Panel & 7 Backend Servis

### 10 Frontend Panel

| # | Panel | Subdomain | Port | Stack |
|---|-------|-----------|------|-------|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 |
| 7 | Home | `home.coremusic.net` | 81 | Vanilla JS |
| 8 | Car | `car.coremusic.net` | — | Vanilla JS |
| 9 | Studio | `studio.coremusic.net` | 81 | Vanilla JS |
| 10 | Pro | `pro.coremusic.net` | 81 | Vanilla JS |

### 7 Backend Servis

| # | Servis | Port | Protocol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 03
# Teknoloji Yığını & Platform Desteği

## 3.1 Teknoloji Yığını Tablosu

| Katman | Teknoloji | Versiyon | Durum |
|--------|-----------|---------|-------|
| Backend | PHP (strict_types) | 8.4+ | ✅ IMPLEMENTED |
| Frontend | Vanilla JS ES6+ | ES2022 | ✅ IMPLEMENTED (spec) |
| CSS | ITCSS + BEM | 9-layer | ✅ IMPLEMENTED (spec) |
| Database | MySQL / MariaDB (PDO) | 9.x | ✅ IMPLEMENTED (şema) |
| Audio Engine | C++20, JUCE 9, ASIO SDK | 2.3.4 | PLANNED |
| Hardware | XMOS XU316, PCM3168A | — | PLANNED |
| Cache | APCu | — | ✅ IMPLEMENTED |
| Encryption | AES-256-GCM, Argon2id | NIST SP 800-38D | ✅ IMPLEMENTED |
| Package Manager | Composer | 2.7+ | ✅ IMPLEMENTED |
| Download Service | Node.js | LTS | PLANNED |
| Testing | PHPUnit 11, Vitest, Playwright | — | PLANNED |
| CI/CD | GitHub Actions | — | PLANNED |

### Teknoloji Seçim Gerekçeleri

| Teknoloji | Neden Seçildi? | Reddedilen Alternatif |
|-----------|----------------|----------------------|
| PHP 8.4 | strict_types, performans, ekosistem | Node.js (ana backend için) |
| Vanilla JS | Framework bağımsızlığı, hafiflik | React, Vue, Angular (yasak) |
| PDO | Doğrudan SQL kontrolü, performans | ORM (Eloquent, Doctrine — yasak) |
| MySQL 9 | BCNF, olgunluk, ölçeklenebilirlik | MongoDB, PostgreSQL |
| C++20 | Gerçek zamanlı ses işleme | Rust, Java |
| ITCSS | Katmanlı CSS mimarisi | Tailwind, SASS (vanilla tercih) |

---

## 3.2 Platform Tier Desteği

| Tier | OS | Durum | Ses Sürücüsü |
|------|-----|-------|-------------|
| **Tier 1 (Primary)** | Windows (XP-11, Server 2012 R2+) | ✅ Ana geliştirme | ASIO, WASAPI |
| **Tier 2** | Linux (Ubuntu, Debian, Fedora) | ✅ Destekli | ALSA, PipeWire |
| **Tier 3** | macOS (Monterey-Sonoma) | ✅ Destekli | CoreAudio |
| **Tier 4** | Raspberry Pi (ARM64) | ✅ Destekli | I2S |
| **Tier 5** | ReactOS | ⚠️ Experimental | Sınırlı |

---

## 3.3 Deployment Modları

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 04
# Kurulum & Geliştirme Ortamı

## 4.1 Sistem Gereksinimleri

### Sunucu & Altyapı

| Bileşen | Minimum | Önerilen |
|---------|---------|----------|
| **İşletim Sistemi** | Linux (Debian 12) / Windows 11 | Ubuntu 24.04 LTS / Windows 11 |
| **PHP** | 8.4 (strict_types) | 8.4+ |
| **MySQL** | 9.x | 9.x (InnoDB, utf8mb4_unicode_ci) |
| **Web Sunucusu** | Nginx 1.26+ | Nginx 1.26+ |
| **Composer** | 2.7+ | 2.7+ |
| **Node.js** | LTS (Download Service için) | LTS |
| **RAM** | 2 GB | 4 GB+ |
| **Disk** | 20 GB SSD | 50 GB+ NVMe |

### Gerekli PHP Uzantıları

```
pdo_mysql, sodium, mbstring, curl, json, opcache, xml, gd, zip
```

### Ses ve Donanım Bileşenleri

| Bileşen | Gereksinim |
|---------|-----------|
| **Masaüstü** | Windows 10/11 x64 (ASIO SDK 2.3.4 veya WASAPI Exclusive) |
| **Gömülü** | Raspberry Pi 5 (4/8GB RAM) + NVMe SSD |
| **Özel Ses** | CoreMusic XMOS XU316 USB + PCM3168A 8-kanal DAC + 100W Class AB |

---

## 4.2 Adım Adım Kurulum (6 Adım)

### Adım 1: İndirme

```bash
wget https://www.coremusic.net/download/coremusic-latest.tar.gz
```

### Adım 2: Arşivi Açma

```bash
tar -xvf coremusic-latest.tar.gz
cd coremusic
```

### Adım 3: Bağımlılıkları Yükleme

```bash
composer install --no-dev --optimize-autoloader
```

### Adım 4: Yapılandırma

```bash
cp env.example .env
nano .env
```

### Adım 5: Veritabanını Başlatma

```bash
php bin/console migrate
php bin/console db:seed
```

### Adım 6: Servisleri Başlatma

```bash
# Nginx yapılandırmasını kontrol edin
sudo nginx -t
sudo systemctl restart nginx

# Veya IIS için
iisreset
```

Tarayıcınızdan `http://localhost` adresine giderek kurulumu test edin.

---

## 4.3 Geliştirme Ortamı Kurumu

### VS Code Önerilen Eklentileri

| Eklenti | Amaç |
|---------|------|
| PHP Intelephense | PHP autocomplete ve hata tespiti |
| PHPStan | Statik analiz |
| ESLint | JavaScript linting |
| GitLens | Git entegrasyonu |
| Docker | Container yönetimi |

### IDE Ayarları

```json
{
  "php.validate.executablePath": "C:/php/php.exe",
  "editor.formatOnSave": true,
  "editor.tabSize": 4,
  "files.insertFinalNewline": true,
  "files.trimTrailingWhitespace": true
}
```

---

## 4.4 Ortam Değişkenleri (.env)

```ini
# ==============================================================================
# COREMUSIC ENVIRONMENT CONFIGURATION
# ==============================================================================
APP_ENV=development
APP_DEBUG=true
APP_KEY=base64:your_master_key_here==
APP_URL=http://localhost

# Subdomain Routing
DOMAIN_API=http://localhost:81
DOMAIN_AUTH=http://auth.coremusic.local
DOMAIN_MEDIA=http://media.coremusic.local
DOMAIN_HOME=http://home.coremusic.local

# Veritabanı
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=coremusic_dev
DB_PASS=dev_password
DB_NAME=coremusic_auth
DB_CHARSET=utf8mb4

# Kriptografi
SECURITY_AES_KEY=your_64_char_hex_key_here
JWT_SECRET=your_jwt_secret_here
SESSION_LIFETIME=3600

# Ses Motoru
AUDIO_DRIVER=ASIO
AUDIO_BUFFER_SIZE=256
AUDIO_SAMPLE_RATE=48000
AUDIO_CHANNELS=2
```

> **⚠️ Güvenlik Uyarısı:** `.env` dosyası Git'e EKLENMEZ. `.gitignore` dosyasında mutlaka yer almalıdır.

---

<div style="page-break-after: always;"></div>

# BÖLÜM 05
# API Dokümantasyonu

## 5.1 API-First Mimari & Gateway

CoreMusic'te **hiçbir endpoint doğrudan kodlanmaz.** Önce OpenAPI sözleşmesi hazırlanır:

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

### API Gateway

Tüm istemcilerin tek giriş noktası `api.coremusic.net`'tir. Gateway; routing, auth, rate limit, validation, logging, correlation ID görevini üstlenir.

### BFF (Backend for Frontend)

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

---

## 5.2 Kimlik Doğrulama (Hybrid JWT + Session)

CoreMusic, **JWT (JSON Web Token)** ve oturum tabanlı güvenliği birleştiren hibrit bir kimlik doğrulama mimarisi kullanır:

1. İstekler `auth.coremusic.net` üzerinden doğrulanır
2. Başarılı giriş sonrasında üretilen JWT belirteci `Authorization: Bearer <token>` ile taşınır
3. RBAC katmanları: `regular`, `premium`, `studio`, `car`, `admin`

### Login İsteği

**`POST /api/auth/login`**

```http
Content-Type: application/json
X-CSRF-Token: d41d8cd98f00b204e9800998ecf8427e
```

```json
{
  "username": "user@example.com",
  "password": "your_password"
}
```

### Login Yanıtı (HTTP 200)

```json
{
  "status": "success",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "username": "user@example.com",
    "role": "regular",
    "theme_gender": "female"
  }
}
```

### Register İsteği (3 Adım)

**Adım 1 — Cinsiyet Seçimi:**
```json
{
  "gender": "female"
}
```

**Adım 2 — Temel Bilgiler:**
```json
{
  "username": "newuser",
  "email": "user@example.com",
  "password": "SecurePass123!"
}
```

**Adım 3 — Profil Tamamlama:**
```json
{
  "display_name": "Kullanıcı Adı",
  "avatar_url": null,
  "preferences": {
    "language": "tr",
    "theme": "auto"
  }
}
```

---

## 5.3 Temel Endpoint'ler

| Metot | Endpoint | Açıklama | Yetki |
|-------|----------|----------|-------|
| `POST` | `/api/auth/login` | Kimlik doğrulama | Public |
| `POST` | `/api/auth/register` | Kullanıcı kaydı | Public |
| `POST` | `/api/auth/logout` | Oturum kapatma | Auth |
| `GET` | `/api/tracks` | Müzik kütüphanesi listeleme | regular+ |
| `GET` | `/api/tracks/{uuid}` | Parça detayı + metadata | regular+ |
| `POST` | `/api/playlists` | Çalma listesi oluşturma | regular+ |
| `GET` | `/api/playlists/{id}` | Çalma listesi detayı | regular+ |
| `PUT` | `/api/playlists/{id}` | Çalma listesi güncelleme | regular+ |
| `DELETE` | `/api/playlists/{id}` | Çalma listesi silme | regular+ |
| `GET` | `/api/albums` | Albüm listesi | regular+ |
| `GET` | `/api/artists` | Sanatçı listesi | regular+ |
| `GET` | `/api/hardware/status` | Donanım durumu (XMOS, PCM3168A) | premium+ |
| `POST` | `/api/dsp/profile` | EQ/DSP profili aktarma | studio+ |
| `GET` | `/api/system/health` | Servis sağlık kontrolü | admin |

### Sayfalama

Tüm listeleme endpoint'leri sayfalama destekler:

```
GET /api/tracks?page=1&pageSize=20&sortBy=title&sortOrder=asc
```

Yanıt:
```json
{
  "data": [...],
  "pagination": {
    "page": 1,
    "pageSize": 20,
    "totalItems": 142,
    "totalPages": 8
  }
}
```

---

## 5.4 Hata Kodları & Yanıt Formatları

### Standart Hata Formatı

```json
{
  "status": "error",
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "E-posta adresi gerekli",
    "details": {
      "field": "email",
      "rule": "required"
    }
  }
}
```

### HTTP Durum Kodları

| Kod | Anlam | Kullanım |
|-----|-------|----------|
| `200` | Başarılı | Normal yanıt |
| `201` | Oluşturuldu | Kayıt, oluşturma |
| `400` | Hatalı İstek | Validation hatası |
| `401` | Yetkisiz | Token yok veya geçersiz |
| `403` | Yasaklı | Yetki yetersiz |
| `404` | Bulunamadı | Kayıt yok |
| `409` | Çelişki | Duplicate kayıt |
| `422` | İşlenemedi | Semantic validation |
| `429` | Çok Fazla İstek | Rate limit aşıldı |
| `500` | Sunucu Hatası | Internal error |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 06
# Ses Motoru & Donanım Entegrasyonu

## 6.1 Neva Engine C++20 Çekirdek

**Neva Engine**, Windows, Linux ve gömülü platformlarda işletim sistemi mikserlerini baypas ederek doğrudan ses donanımını süren tescilli C++20 ses çekirdeğidir.

### Temel Kurallar

| Kural | Açıklama |
|-------|----------|
| **Zero-Allocation** | Ses döngüsü içinde `malloc`, `free`, `new`, `delete` kesinlikle yasak |
| **Lock-Free** | Tek yazıcı/tek okuyucu ring buffer (SPSC) ile kilitlenmesiz iletişim |
| **64-Bayt Hizalama** | `alignas(64)` ile false sharing engelleme |
| **32-Bit Float** | Tüm hesaplamalar Float32 PCM'de çalışır |
| **Ultra Düşük Gecikme** | ASIO ile <10ms, WASAPI Exclusive ile <15ms |

### C++ Guardrails

```cpp
// ✅ Doğru — Pre-allocated buffer
class AudioProcessor {
    alignas(64) float buffer[4096];  // Compile-time allocation
    void process(float* input, float* output, size_t frames) noexcept;
};

// ❌ YASAK — Runtime allocation
void process(float* input, float* output, size_t frames) {
    float* temp = new float[frames];  // YASAK!
    // ...
    delete[] temp;  // YASAK!
}
```

---

## 6.2 Gerçek Zamanlı DSP Zinciri

```
[Kayıpsız FLAC/WAV]
   ──> [31-Band Parametrik EQ]
   ──> [Reverb Akustik Oda Simülatörü]
   ──> [Dinamik Kompresör]
   ──> [True Peak Brickwall Limiter]
   ──> [8.1 Surround Yönlendirme Matrisi]
   ──> [TI PCM3168A 8-Kanal DAC]
   ──> [100W Class AB Amfi]
```

| Bileşen | Özellik |
|---------|---------|
| **31-Band EQ** | 20Hz-20kHz, 1/3 oktav ISO, ±18dB kazanç, Q: 0.1-10.0 |
| **Reverb** | Geniş Konser, Düğün Salonu, Oda, Stüdyo modları |
| **Kompresör** | Threshold, Ratio, Attack, Release ayarlanabilir |
| **Limiter** | True Peak brickwall, inter-sample peak koruması |
| **8.1 Surround** | 7.1 + LFE, her kanal için bağımsız time alignment |

---

## 6.3 Ses Donanımı & Güç Katı (ADR-038)

| Bileşen | Model | Özellik |
|---------|-------|---------|
| **USB Audio İşlemcisi** | XMOS XU316 | 16 çekirdekli xCORE-200, USB Audio Class 2.0 |
| **DAC** | TI PCM3168A | 24-bit 192kHz, 112dB SNR, 8 kanal diferansiyel |
| **Amplifikatör** | Class AB | Kanal başına 100W @ 8Ω, THD+N <%0.01, SNR >100dB |
| **Koruma** | Mikrodenetleyici | >0.5V DC offset'te röle devresi ile hoparlör koruması |

---

## 6.4 Ses Formatları & Codec Desteği

| Format | Bit Derinliği | Örnekleme Hızı | Durum |
|--------|---------------|----------------|-------|
| FLAC | 16/24/32-bit | 44.1-192kHz | ✅ Tam destek |
| WAV/PCM | 16/24/32-bit | 44.1-192kHz | ✅ Tam destek |
| MP3 | 320kbps CBR | 44.1kHz | ✅ Decode |
| AAC | 256kbps | 44.1kHz | ✅ Decode |
| OGG Vorbis | — | — | ✅ Decode |
| DSD64/128 | 1-bit | 2.8/5.6MHz | ⚠️ Planlanan |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 07
# Arayüz & Tasarım Sistemi

## 7.1 Kanonik Ekranlar & 19 PNG Mockup

CoreMusic arayüzü, `.ai/ui-design/00-mockup-index.md` altındaki **19 kanonik PNG mockup** tasarımına birebir piksel sadakatiyle üretilmiştir.

### PNG Mockup Envanteri

| # | Kategori | Ekran | Çözünürlük |
|---|----------|-------|-----------|
| 1-12 | Home (1024) | Ana Sayfa, Hoş Geldin, Albums, Album Detayı, Sanatçılar, Playlist, Video, Göz At | 1024×600 |
| 13 | Home (1920) | Desktop Ana Sayfa | 1920×1080 |
| 14-19 | Auth | Select Gender, Login, Register (3 adım) | 1024×600 |

### Ana Ekran Bileşenleri

- **Sol Kenar Çubuğu (Sidebar):** Logo, Home, Search, Library, Playlists, Artists, Settings
- **Gövde Alanı:** Selamlama, Son Çalınanlar, Kişiselleştirilmiş Listeler
- **Sabit Alt Çalar (Player Bar):** Kapak, şarkı bilgisi, süre, kontroller, ses

---

## 7.2 Bileşen Envanteri (C01-C16)

| Kod | Bileşen | İşlev |
|-----|---------|-------|
| **C01** | `SidebarNavigation` | Masaüstü ve tablet dikey gezinme |
| **C02** | `TopHeaderBar` | Arama, bildirimler, avatar |
| **C03** | `PlayerBarBottom` | Kesintisiz sabit alt oynatıcı |
| **C04** | `TrackRowItem` | Parça listesi satırı |
| **C05** | `AlbumCard` | Albüm grid kapak kartı |
| **C06** | `VolumeControlSlider` | Logaritmik ses kaydırıcısı |
| **C07** | `Equalizer31Band` | 31-band EQ matrisi |
| **C08** | `SpectrumAnalyzerFFT` | Gerçek zamanlı FFT spektrum |
| **C09** | `MultiRoomSelector` | Oda hoparlör seçimi |
| **C10** | `LyricsViewModal` | Senkronize şarkı sözü |
| **C11** | `CarTouchButton` | Min 48×48px dokunma butonu |
| **C12** | `SurroundMatrixView` | 8.1 kanal atama ızgarası |
| **C13** | `NotificationToast` | Bildirim balonu |
| **C14** | `OfflineStatusBadge` | Çevrimdışı durum rozeti |
| **C15** | `DevicePickerMenu` | Çıkış cihazı seçim menüsü |
| **C16** | `CredentialVaultKey` | Şifreli anahtar kasası |

---

## 7.3 Tasarım Tokenları & ITCSS/BEM

### ITCSS 9 Katman

```
1. Settings    — Değişkenler, token'lar
2. Tools       — Mixin'ler, fonksiyonlar
3. Generic     — Reset, normalize
4. Elements    — HTML element stilleri
5. Objects     — Layout pattern'leri
6. Components  — B bileşenleri (BEM namespace)
7. Utilities   — Yardımcı sınıflar
8. Overrides   — Kural dışı durumlar
9. Themes      — Tema değişkenleri
```

### BEM Namespace

```css
/* ✅ Doğru — BEM ile namespace */
.c-player-bar { }
.c-player-bar__title { }
.c-player-bar--active { }

/* ❌ YASAK — Global namespace */
.player-bar { }
.player-title { }
```

### Design Tokens (Örnek)

```css
:root {
  /* Renkler */
  --cm-color-primary: #ff4fd8;
  --cm-color-secondary: #4f9fff;
  --cm-color-bg: #0a0a12;
  --cm-color-surface: #1a1a2e;
  --cm-color-text: #ffffff;
  
  /* Boşluk */
  --cm-space-xs: 4px;
  --cm-space-sm: 8px;
  --cm-space-md: 16px;
  --cm-space-lg: 24px;
  --cm-space-xl: 32px;
  
  /* Tipografi */
  --cm-font-family: 'Inter', sans-serif;
  --cm-font-size-sm: 12px;
  --cm-font-size-md: 14px;
  --cm-font-size-lg: 18px;
  --cm-font-size-xl: 24px;
  
  /* Köşe Yuvarlaklığı */
  --cm-radius-sm: 4px;
  --cm-radius-md: 8px;
  --cm-radius-lg: 12px;
}
```

---

## 7.4 Responsive Tier Kuralları

| Cihaz | Çözünürlük | Breakpoint | Touch Target |
|-------|-----------|-----------|--------------|
| **Desktop** | 1920×1080 | >1024px | — |
| **Tablet** | 1024×768 | 768-1024px | 44px min |
| **Embedded (RPi5)** | 1024×600 | ≤1024px | 48px min |
| **Car** | 800×480 | ≤800px | 48px min (WCAG) |

### 4K Kuralı (§7.4)

4K ekranlarda merkezleme YASAKTIR — sol üst köşeden hizalama yapılır.

### Geriye Dönük Uyumluluk (§12)

Eski cihazlar için fallback zorunludur. CSS `@supports` ile feature detection yapılır.

---

## 7.5 Tema Motoru (ADR-044)

| Özellik | Detay |
|---------|-------|
| **Cinsiyet Bazlı** | female→pink (#ff4fd8), male→blue (#4f9fff), neutral→default |
| **PHP Katmanı** | `ThemeEngine.php` — DB + user gender çözümleme |
| **JS Katmanı** | `ThemeManager.js` — CSS custom properties ile anında geçiş |
| **Veritabanı** | `user_preferences` tablosu — `user_id`, `device_type`, `theme_gender` |
| **Admin** | Bağımsız tema sistemi (kullanıcı temalarından ayrı) |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 08
# Güvenlik & Uyumluluk

## 8.1 Değişmez 10 Adımlı Güvenlik Hattı

Her HTTP isteği aşağıdaki kontrollerden geçer:

```
IP Filtresi
  → Rate Limiter (60 req/60s, APCu)
    → CORS Headers
      → CSRF Doğrulama (csrf_token)
        → CSP Nonce Üretimi
          → Security Headers (HSTS, X-Frame-Options)
            → JWT/Session Auth
              → RBAC Doğrulama
                → Input Sanitization
                  → Strict Output Encoding
```

> **⚠️ Middleware sırası DEĞİŞTİRİLEmez.** CSP nonce üretimi SecurityHeaders (#4) içindedir; SessionManager (#5) bu nonce'u session'a kaydeder.

---

## 8.2 Şifreleme (AES-256-GCM + Argon2id)

### Parola Hashing — Argon2id

```php
// ✅ Doğru — Argon2id
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,  // 64 MB
    'time_cost' => 4,        // 4 iterasyon
    'threads' => 3,          // 3 iş parçacığı
]);

// Doğrulama
if (password_verify($input, $storedHash)) {
    // Giriş başarılı
}
```

### Veri Şifreleme — AES-256-GCM

```php
// ✅ Doğru — AES-256-GCM
$ciphertext = sodium_crypto_aead_aes256gcm_encrypt(
    $plaintext,
    $additionalData,
    $nonce,
    $key
);

$decrypted = sodium_crypto_aead_aes256gcm_decrypt(
    $ciphertext,
    $additionalData,
    $nonce,
    $key
);
```

> **⚠️** `sodium` PHP uzantısı zorunludur. `mcrypt` veya `openssl` eski yöntemleri KULLANILMAZ.

---

## 8.3 CSRF, CSP & Rate Limiting

### CSRF Token

| Kural | Değer |
|-------|-------|
| Token adı | `csrf_token` (kesinlikle `_csrf_token` DEĞİL) |
| Saklama yeri | Session (HTTPOnly cookie) |
| Gönderim | `X-CSRF-Token` header veya form field |
| Geçerlilik | Tek kullanımlık, her istek sonrası yenilenir |

### CSP Nonce

```
Content-Security-Policy:
  default-src 'self';
  script-src 'nonce-{RANDOM}' 'strict-dynamic';
  style-src 'self' 'nonce-{RANDOM}';
  img-src 'self' data: https:;
  connect-src 'self' https://api.coremusic.net;
  frame-ancestors 'self';
  base-uri 'self';
  form-action 'self';
```

### Rate Limiting

| Parametre | Değer |
|-----------|-------|
| Mekanizma | APCu tabanlı |
| Limit | 60 istek / 60 saniye |
| Pencere | Kaydırma penceresi (sliding window) |
| Aşımda | HTTP 429 Too Many Requests |

---

## 8.4 Session Yönetimi (ADR-011)

| Parametre | Değer |
|-----------|-------|
| Saklama | HTTPOnly Secure Cookie |
| Ömür | 3600 saniye (1 saat) idle timeout |
| Yenileme | Hareket sonrasında otomatik yenileme |
| Session ID | 32-byte rastgele, kriptografik olarak güvenli |
| Rolling | Her 5 dakikada bir session ID rotasyonu |

### Yasak Örüntüler

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `localStorage` ile auth token saklama | HTTPOnly cookie |
| `sessionStorage` ile auth token saklama | Session-based auth |
| `_csrf_token` | `csrf_token` |
| `eval()` / `Function()` | Safe alternatives |
| `innerHTML` | `DOMParser` + `TrustedTypes` |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 09
# Veritabanı Mimarisi

## 9.1 18 BCNF Veritabanı (156 Tablo)

| # | Veritabanı | Amaç | Tablo |
|---|------------|------|-------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens, API keys | 13 |
| 2 | `coremusic_user` | Profiles, preferences, history | 7 |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics, podcasts, videos | 22 |
| 4 | `coremusic_albums` | Album collections, discs, stats | 5 |
| 5 | `coremusic_playlist` | Playlists, collaborators, followers | 5 |
| 6 | `coremusic_catalog` | Reference data (genres, instruments, moods) | 8 |
| 7 | `coremusic_logs` | Audit trail, analytics, performance | 22 |
| 8 | `coremusic_media` | Device sync, media metadata | 8 |
| 9 | `coremusic_system` | Settings, config, cache, EQ, notifications | 17 |
| 10 | `coremusic_social` | Comments, shares, listening rooms | 9 |
| 11 | `coremusic_wireless` | WiFi + Bluetooth networks | 5 |
| 12 | `coremusic_ai` | Preference profiles, recommendations | 6 |
| 13 | `coremusic_api` | API keys, rate limits, webhooks | 4 |
| 14 | `coremusic_cms` | Pages, blog, tags, FAQs, banners | 8 |
| 15 | `coremusic_download` | Download queue, history, cache | 4 |
| 16 | `coremusic_neva` | EQ presets, DSP settings, routing matrix | 4 |
| 17 | `coremusic_studio` | Studio sessions, tracks, presets | 6 |
| 18 | `coremusic_patch` | Schema versions, migration logs | 3 |
| | **TOPLAM** | | **156** |

### ID Standardı: UUID v7

Tüm tablolarda birincil anahtar olarak **UUID v7** kullanılır. UUID v7 zaman damgalıdır ve sıralama dostudur.

```php
// ✅ Doğru — UUID v7
$uuid = Uuid::v7();  // Zaman damgalı, sıralanabilir

// ❌ YASAK — Auto-increment
$id = $pdo->lastInsertId();  // YASAK
```

---

## 9.2 SQL Kuralları & Yasaklar

| Kural | Açıklama |
|-------|----------|
| **`SELECT *` YASAKTIR** | Sadece ihtiyaç duyulan sütunlar yazılır |
| **Parametrik Sorgu Zorunlu** | Değişkenler SQL dizesine birleştirilemez |
| **N+1 Sorgu Yasağı** | Döngü içinde SQL atılamaz |
| **ORM YASAKTIR** | Sadece ham PDO prepared statement |
| **BCNF Zorunlu** | Tüm tablolar BCNF formatında olmalı |

### Örnek — Doğru PDO Kullanımı

```php
// ✅ Doğru — Parametrik sorgu, explicit columns, prepared statement
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.email, u.created_at
    FROM coremusic_auth.users u
    WHERE u.id = :user_id
    AND u.status = :status
");
$stmt->execute([
    ':user_id' => $userId,
    ':status' => 'active'
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ❌ YASAK — String concatenation, SELECT *
$stmt = $pdo->query("SELECT * FROM users WHERE id = " . $userId);  // YASAK!
```

### Örnek — N+1 Engelleme

```php
// ✅ Doğru — JOIN ile toplu veri çekme
$stmt = $pdo->prepare("
    SELECT a.id, a.title, ar.name AS artist_name
    FROM coremusic_albums.albums a
    JOIN coremusic_musics.artists ar ON a.artist_id = ar.id
    WHERE a.user_id = :user_id
    ORDER BY a.created_at DESC
    LIMIT :limit OFFSET :offset
");

// ❌ YASAK — Döngü içinde sorgu
foreach ($albumIds as $id) {
    $stmt = $pdo->query("SELECT * FROM albums WHERE id = $id");  // YASAK!
}
```

---

## 9.3 Veritabanı Güvenliği (ADR-022)

| Güvenlik Önlemi | Uygulama |
|----------------|----------|
| **最小 privilege** | Uygulama kullanıcısı sadece SELECT/INSERT/UPDATE, DROP yasak |
| **Encryption at rest** | Hassas alanlar AES-256-GCM ile şifreli |
| **Backup** | Günlük otomatik yedekleme, 30 gün saklama |
| **Audit trail** | Tüm write操作ları `coremusic_logs`'a kaydedilir |
| **Connection pool** | PDO persistent connection, max 20 |
| **Charset** | utf8mb4_unicode_ci (emoji ve unicode desteği) |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 10
# Dağıtım & Operasyonlar

## 10.1 Raspberry Pi 5 Ev Medya Merkezi

### Kurulum Adımları

1. **İmaj Yazma:** CoreMusic Home OS görüntüsünü RPi Imager ile NVMe SSD'ye yazdırın
2. **Ağ Yapılandırması:** Ethernet veya 5GHz Wi-Fi → `http://home.coremusic.local`
3. **Multi-Room Dağıtımı:** WebRTC ile çok odalı ses yayını başlatma

### Minimum Donanım

| Bileşen | Minimum | Önerilen |
|---------|---------|----------|
| Raspberry Pi | 5 (4GB) | 5 (8GB) |
| Depolama | 64GB MicroSD | 256GB NVMe SSD |
| Ağ | 100Mbps Ethernet | Gigabit Ethernet |
| Ses Çıkışı | 3.5mm jack | I2S → PCM3168A |

---

## 10.2 Docker & Mikro Servis Mimarisi

### docker-compose.prod.yml (Örnek)

```yaml
version: '3.8'

services:
  api:
    build: ./api.coremusic.net
    ports:
      - "81:81"
    environment:
      - APP_ENV=production
      - DB_HOST=mysql
    depends_on:
      - mysql
      - redis

  auth:
    build: ./auth.coremusic.net
    ports:
      - "82:81"
    environment:
      - APP_ENV=production

  download:
    build: ./download.coremusic.net
    ports:
      - "3001:3001"
    environment:
      - NODE_ENV=production

  mysql:
    image: mysql:9
    environment:
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASS}
      - MYSQL_DATABASE=coremusic_auth
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

volumes:
  mysql_data:
```

### Başlatma

```bash
docker compose -f docker-compose.prod.yml up -d
```

---

## 10.3 CI/CD Pipeline & GitHub Actions

### Pipeline Yapısı

```
Push to main
  → Lint (PHP, JS)
    → Unit Test (PHPUnit, Vitest)
      → Integration Test
        → Security Scan (GitLeaks, OWASP)
          → Build Docker Image
            → Deploy to Staging
              → Smoke Test
                → Deploy to Production
```

### GitHub Actions Workflow (Örnek)

```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo_mysql, sodium, mbstring
      - name: Install Dependencies
        run: composer install --no-progress
      - name: Run Tests
        run: vendor/bin/phpunit --coverage-text
      - name: Security Audit
        run: composer audit
```

---

## 10.4 Monitoring & Health Check

### Health Check Endpoint

```
GET /api/system/health
```

Yanıt:
```json
{
  "status": "healthy",
  "timestamp": "2026-09-15T10:30:00Z",
  "services": {
    "database": { "status": "up", "latency_ms": 2 },
    "cache": { "status": "up", "latency_ms": 1 },
    "audio_engine": { "status": "up", "latency_ms": 5 }
  },
  "version": "1.0.0"
}
```

### Monitoring Araçları

| Araç | Amaç |
|------|------|
| Prometheus | Metrik toplama |
| Grafana | Dashboard ve görselleştirme |
| UptimeRobot | Dış servis izleme |
| Sentry | Hata takibi |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 11
# Freelancer Geliştirme Kuralları

## 11.1 Hard Guardrails (17 Kural)

Bu kurallar **kesinlikle ihlal edilemez.** İhlal eden kod derhal revert edilir.

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Zero Code Before Plan** | Plan onayı olmadan kod yazma |
| 2 | **Vault First** | Kod yazmadan önce AI vault'u oku |
| 3 | **Zero Hallucination** | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` |
| 4 | **In-Place Refactoring** | Dosya adı/yolu değişmez |
| 5 | **Single Source of Truth** | Bilgi sadece `.ai/` vault'tan |
| 6 | **CSRF Token = `csrf_token`** | `_csrf_token` yasak |
| 7 | **Middleware Order Immutable** | Sıra değişmez |
| 8 | **Port 81 = music.coremusic.net** | PHP 8.4 |
| 9 | **No ORM** | Raw PDO only |
| 10 | **No Frameworks** | Vanilla JS + ITCSS |
| 11 | **Mockup Before Frontend** | Mockup okunmadan frontend kodu yasak |
| 12 | **Contradiction Gate** | Vault'ta çelişki varsa DUR ve sor |
| 13 | **Session Continuity** | Her oturum başında geçmişten devam |
| 14 | **Human Approval Gate** | Mimari karar öncesi onay zorunlu |
| 15 | **Vault-First Mandatory** | Vault okumadan plan/kod/faaliyet başlatılamaz |
| 16 | **Template Mandatory** | Yeni dosya için template zorunlu |
| 17 | **Single Component Responsive** | Tek component + responsive CSS; ayrı HTML/branch yasak |

---

## 11.2 PHP 8.4+ Kuralları

| Kural | Açıklama |
|-------|----------|
| `declare(strict_types=1);` | Her PHP dosyasının en başında |
| Tip-specific parametreler | `function foo(string $bar, int $baz): bool` |
| `mixed` yasak | Açık tiplendirme zorunlu |
| PSR-12 kodlama standartları | Opening braces, spacing, naming |
| `readonly` properties | Yapısal olarak mümkünse `readonly` kullan |
| Named arguments | Uzun parametre listelerinde named arguments |
| Match expression | `switch` yerine `match` tercih |

### PHP Kod Örneği — Controller

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Controller;

use CoreMusic\Http\Request;
use CoreMusic\Http\Response;
use CoreMusic\Service\TrackService;
use CoreMusic\DTO\TrackListRequest;

final class TrackController
{
    public function __construct(
        private readonly TrackService $trackService,
    ) {}

    public function index(Request $request): Response
    {
        $dto = TrackListRequest::fromRequest($request);
        $tracks = $this->trackService->list($dto);

        return Response::json([
            'status' => 'success',
            'data' => $tracks->toArray(),
            'pagination' => $tracks->pagination()->toArray(),
        ]);
    }

    public function show(Request $request, string $uuid): Response
    {
        $track = $this->trackService->findByUuid($uuid);

        if ($track === null) {
            return Response::json([
                'status' => 'error',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Parça bulunamadı',
                ],
            ], 404);
        }

        return Response::json([
            'status' => 'success',
            'data' => $track->toArray(),
        ]);
    }
}
```

---

## 11.3 Frontend Kuralları (No-Framework)

| Yasak | Doğru |
|-------|-------|
| React, Vue, Angular, Svelte | Vanilla JS ES6+ |
| jQuery, TailwindCSS | ITCSS + BEM |
| `var` | `const` / `let` |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| `eval()` / `Function()` | Safe alternatives |
| Global namespace | BEM namespace |

### JS Kod Örneği — API Client

```javascript
// ✅ Doğru — Vanilla JS ES6+, modüler
const ApiClient = {
    baseUrl: window.location.origin,
    
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': this.getCsrfToken(),
            },
            ...options,
        };

        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            throw new ApiError(data.error.code, data.error.message);
        }

        return data;
    },

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? '';
    },

    async getTracks(page = 1) {
        return this.request(`/api/tracks?page=${page}`);
    },

    async createPlaylist(name) {
        return this.request('/api/playlists', {
            method: 'POST',
            body: JSON.stringify({ name }),
        });
    },
};

// Kullanım
const tracks = await ApiClient.getTracks(1);
console.log(tracks.data);
```

---

## 11.4 C++20 Ses Motoru Kuralları

| Yasak | Doğru |
|-------|-------|
| `new` / `delete` (Audio Thread) | Pre-allocated buffers |
| `std::mutex` (Audio Thread) | Lock-free ring buffer |
| `std::cout` / `printf` (Audio Thread) |(lock-free logging) |
| `sleep` (Audio Thread) | Non-blocking wait |
| Heap allocation | `alignas(64)` stack/pool |

### C++ Kod Örneği — Ring Buffer

```cpp
// ✅ Doğru — Lock-free SPSC ring buffer
template<typename T, size_t Capacity>
class alignas(64) LockFreeRingBuffer {
    static_assert((Capacity & (Capacity - 1)) == 0, "Capacity must be power of 2");
    
private:
    alignas(64) std::atomic<size_t> writeIndex_{0};
    alignas(64) std::atomic<size_t> readIndex_{0};
    alignas(64) T buffer_[Capacity];

public:
    bool push(const T& item) noexcept {
        const size_t currentWrite = writeIndex_.load(std::memory_order_relaxed);
        const size_t nextWrite = (currentWrite + 1) & (Capacity - 1);
        
        if (nextWrite == readIndex_.load(std::memory_order_acquire)) {
            return false;  // Buffer dolu
        }
        
        buffer_[currentWrite] = item;
        writeIndex_.store(nextWrite, std::memory_order_release);
        return true;
    }
    
    bool pop(T& item) noexcept {
        const size_t currentRead = readIndex_.load(std::memory_order_relaxed);
        
        if (currentRead == writeIndex_.load(std::memory_order_acquire)) {
            return false;  // Buffer boş
        }
        
        item = buffer_[currentRead];
        readIndex_.store((currentRead + 1) & (Capacity - 1), std::memory_order_release);
        return true;
    }
};
```

---

## 11.5 Git & ADR Protokolü

### Commit Mesajı Formatı

```
<type>(<scope>): <description>

<optional body>

<optional footer>
```

| Type | Kullanım |
|------|----------|
| `feat` | Yeni özellik |
| `fix` | Hata düzeltme |
| `refactor` | Kod yeniden yapılandırma |
| `docs` | Dokümantasyon |
| `test` | Test ekleme/düzeltme |
| `chore` | Bakım görevi |

### Örnek

```
feat(api): add playlist CRUD endpoints

- Implemented POST /api/playlists
- Implemented GET /api/playlists/{id}
- Implemented PUT /api/playlists/{id}
- Implemented DELETE /api/playlists/{id}

Closes #42
```

### ADR Oluşturma

Her mimari karar için `.ai/decisions/` klasörüne ADR eklenir:

```
decisions/
├── accepted/
│   ├── ADR-001-vanilla-js-itcss.md
│   ├── ADR-002-pdo-mandatory-no-orm.md
│   └── ...
├── draft/
│   └── ADR-089-new-feature.md
└── rejected/
    └── R-001-redux-style-state-management.md
```

---

## 11.6 Kod Örnekleri — Her Katman İçin

### Repository (L0)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Repository;

use CoreMusic\Database\Connection;
use CoreMusic\Entity\Track;

final class TrackRepository
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    public function findByUuid(string $uuid): ?Track
    {
        $stmt = $this->connection->prepare("
            SELECT id, uuid, title, artist, album, duration, file_path
            FROM coremusic_musics.tracks
            WHERE uuid = :uuid
        ");
        $stmt->execute([':uuid' => $uuid]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? Track::fromRow($row) : null;
    }

    public function findByUserId(int $userId, int $page = 1, int $pageSize = 20): array
    {
        $offset = ($page - 1) * $pageSize;
        $stmt = $this->connection->prepare("
            SELECT id, uuid, title, artist, album, duration
            FROM coremusic_musics.tracks
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

### Service (L4-L5)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Service;

use CoreMusic\Repository\TrackRepository;
use CoreMusic\DTO\TrackListRequest;
use CoreMusic\Pagination\PaginatedResult;

final class TrackService
{
    public function __construct(
        private readonly TrackRepository $trackRepository,
    ) {}

    public function list(TrackListRequest $request): PaginatedResult
    {
        $tracks = $this->trackRepository->findByUserId(
            $request->userId,
            $request->page,
            $request->pageSize
        );

        $total = $this->trackRepository->countByUserId($request->userId);

        return new PaginatedResult(
            data: $tracks,
            page: $request->page,
            pageSize: $request->pageSize,
            totalItems: $total,
        );
    }

    public function findByUuid(string $uuid): ?array
    {
        $track = $this->trackRepository->findByUuid($uuid);
        return $track?->toArray();
    }
}
```

### Middleware (L1)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Http\Request;
use CoreMusic\Http\Response;
use CoreMusic\Session\SessionManager;

final class CsrfMiddleware
{
    public function __construct(
        private readonly SessionManager $sessionManager,
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'], true)) {
            $token = $request->header('X-CSRF-Token')
                ?? $request->input('csrf_token');

            if ($token === null || !hash_equals(
                $this->sessionManager->get('csrf_token'),
                $token
            )) {
                return Response::json([
                    'status' => 'error',
                    'error' => [
                        'code' => 'CSRF_TOKEN_MISMATCH',
                        'message' => 'Geçersiz CSRF token',
                    ],
                ], 403);
            }
        }

        return $next($request);
    }
}
```

---

<div style="page-break-after: always;"></div>

# BÖLÜM 12
# Proje Yönetimi & İletişim Protokolü

## 12.1 Proje Yapısı & Sprint Döngüsü

### Sprint Yapısı

| Parametre | Değer |
|-----------|-------|
| Sprint süresi | 2 hafta |
| Planning | Pazartesi sabahı (ilk gün) |
| Daily standup | Her gün 09:30 (15 dk) |
| Review | Cuma öğleden sonra (son gün) |
| Retro | Cuma öğleden sonra (Review sonrası) |

### Görev Durumları

| Durum | Renk | Açıklama |
|-------|------|----------|
| **Backlog** | Gri | Henüz planlanmamış görev |
| **To Do** | Sarı | Planlandı, başlanacak |
| **In Progress** | Mavi | Devam ediyor |
| **Review** | Turuncu | İnceleme bekliyor |
| **Testing** | Mor | Test ediliyor |
| **Done** | Yeşil | Tamamlandı |

### Öncelik Seviyeleri

| Öncelik | Renk | Süre | Örnek |
|---------|------|------|-------|
| **P0 — CRITICAL** | Kırmızı | 1-4 saat | Auth bypass, veri sızıntısı |
| **P1 — HIGH** | Turuncu | 1-2 gün | Kritik işlev kaybı |
| **P2 — MEDIUM** | Sarı | 3-5 gün | Normal özellik geliştirme |
| **P3 — LOW** | Mavi | 1-2 hafta | İyileştirme, optimizasyon |

---

## 12.2 İletişim Kanalları & Workflow

### İletişim Araçları

| Araç | Amaç | Yanıt Süresi |
|------|------|-------------|
| **GitHub Issues** | Görev takibi, bug raporlama | 24 saat |
| **GitHub Pull Request** | Kod inceleme, merge | 48 saat |
| **Slack / Discord** | Günlük iletişim, hızlı sorular | 4 saat (iş saatleri) |
| **Email** | Resmi iletişim, kontrat | 24 saat |
| **Video Call** | Sprint planning, complex discussions | Randevu ile |

### Workflow Adımları

```
1. Görev Oluşturma (GitHub Issue)
   → Etiketleme (bug, feature, enhancement)
   → Öncelik atama (P0-P3)
   → Assign (sorumlu belirleme)
   
2. Geliştirme
   → Branch oluşturma (feature/issue-42)
   → Kod yazma (guardrails'a uygun)
   → Commit (conventional commits)
   → PR oluşturma
   
3. Code Review
   → En az 1 onay gerektirir
   → CI/CD pipeline geçmeli
   → Test coverage ≥80%
   
4. Merge & Deploy
   → Merge (squash or rebase)
   → Otomatik deploy (staging)
   → Smoke test
   
5. Done
   → Issue kapatma
   → Changelog güncelleme
   → Dokümantasyon güncelleme
```

---

## 12.3 Görev Dağıtımı & Onay Süreci

### Görev Dağıtımı Matrisi

| Görev Tipi | Sorumlu Agent | Onaylayıcı |
|------------|---------------|-----------|
| Backend API | Backend Architect | Tech Lead |
| Frontend UI | UI Designer | Tech Lead |
| Güvenlik | Security Engineer | Arch Lead |
| Veritabanı | Data Engineer | Tech Lead |
| Test | QA Engineer | Tech Lead |
| DevOps/Deploy | DevOps Engineer | Arch Lead |
| Donanım | Audio HW Engineer | Arch Lead |
| ADR | İlgili Architect | Vault Steward |

### Onay Süreci

| Değişiklik Türü | Onay Gereksinimi |
|-----------------|-----------------|
| Bug fix (küçük) | 1 onay (PR review) |
| Yeni özellik | 2 onay (PR review + Tech Lead) |
| Mimari değişiklik | 3 onay (PR + Tech Lead + Arch Lead + ADR) |
| Security fix | 2 onay (PR + Security Engineer) |
| Deployment | 1 onay (Arch Lead) |
| Vault değişikliği | Vault Steward |

---

## 12.4 Freelancer Entegrasyon Adımları

### 1. Hafta — Hazırlık

| # | Adım | Süre |
|---|------|------|
| 1 | Bu dokümanın tamamını oku | 4-6 saat |
| 2 | `.ai/CLAUDE.md` oku (guardrails) | 1 saat |
| 3 | `.ai/AGENTS.md` oku (agent kuralları) | 30 dk |
| 4 | Geliştirme ortamını kur (Bölüm 4) | 2-3 saat |
| 5 | İlk test PR'ı oluştur ( Küçük bir bug fix) | 2-3 saat |

### 2. Hafta — Entegrasyon

| # | Adım | Süre |
|---|------|------|
| 6 | Sprint planning toplantısına katıl | 2 saat |
| 7 | İlk görevi al ve tamamla | 3-5 gün |
| 8 | Code review sürecine katıl | Sürekli |
| 9 | Günlük standup'a katıl | 15 dk/gün |

### Başarılı Entegrasyon Kriterleri

| Kriter | Hedef |
|--------|-------|
| Guardrails uyumu | %100 (17/17 kural) |
| Test coverage | ≥%80 |
| Code review onayı | İlk PR'da 1 onay |
| Sprint velocity | 2. sprint'te ortalamaya ulaşma |
| Communication | Günlük standup katılımı |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 13
# Test Rehberi & Kalite Standartları

## 13.1 Test Stratejisi & Kapsama Hedefleri

| Modül | Minimum | Hedef | Framework |
|-------|---------|-------|-----------|
| Backend (PHP) | ≥%80 | ≥%90 | PHPUnit 11 |
| Frontend (JS) | ≥%80 | ≥%90 | Vitest |
| Audio Engine (C++) | ≥%80 | ≥%90 | Google Test |
| Download Service | ≥%80 | ≥%90 | Vitest |

### Test Pyramid

```
        /  E2E Tests  \          (Playwright — az, yavaş, pahalı)
       /  Integration   \        (PHPUnit/Vitest — orta)
      /   Unit Tests     \       (PHPUnit/Vitest — çok, hızlı, ucuz)
```

---

## 13.2 PHPUnit ile Backend Testi

### Kurulum

```bash
composer require --dev phpunit/phpunit:^11.0
```

### Test Dosyası Örneği

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use CoreMusic\Service\TrackService;
use CoreMusic\Repository\TrackRepository;

class TrackServiceTest extends TestCase
{
    private TrackService $trackService;
    private $mockRepository;

    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(TrackRepository::class);
        $this->trackService = new TrackService($this->mockRepository);
    }

    public function test_list_returns_paginated_result(): void
    {
        // Arrange
        $this->mockRepository
            ->method('findByUserId')
            ->willReturn([
                ['id' => 1, 'title' => 'Test Track'],
            ]);

        $this->mockRepository
            ->method('countByUserId')
            ->willReturn(1);

        // Act
        $result = $this->trackService->list(
            new TrackListRequest(userId: 1, page: 1, pageSize: 20)
        );

        // Assert
        $this->assertCount(1, $result->data);
        $this->assertEquals(1, $result->totalItems);
    }

    public function test_findByUuid_returns_track_when_exists(): void
    {
        $this->mockRepository
            ->method('findByUuid')
            ->willReturn(['uuid' => 'test-uuid', 'title' => 'Test']);

        $result = $this->trackService->findByUuid('test-uuid');

        $this->assertNotNull($result);
        $this->assertEquals('Test', $result['title']);
    }

    public function test_findByUuid_returns_null_when_not_exists(): void
    {
        $this->mockRepository
            ->method('findByUuid')
            ->willReturn(null);

        $result = $this->trackService->findByUuid('nonexistent');

        $this->assertNull($result);
    }
}
```

### Çalıştırma

```bash
vendor/bin/phpunit
vendor/bin/phpunit --coverage-text
vendor/bin/phpunit --filter test_list
```

---

## 13.3 Vitest ile Frontend Testi

### Kurulum

```bash
npm install --save-dev vitest @testing-library/dom
```

### Test Dosyası Örneği

```javascript
import { describe, it, expect, vi } from 'vitest';
import { ApiClient } from '../src/api-client.js';

describe('ApiClient', () => {
    it('getTracks calls correct endpoint', async () => {
        const mockFetch = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({
                status: 'success',
                data: [{ id: 1, title: 'Test Track' }],
                pagination: { page: 1, totalItems: 1 },
            }),
        });

        globalThis.fetch = mockFetch;

        const result = await ApiClient.getTracks(1);

        expect(mockFetch).toHaveBeenCalledWith(
            expect.stringContaining('/api/tracks?page=1'),
            expect.any(Object)
        );
        expect(result.data).toHaveLength(1);
    });

    it('throws error on non-ok response', async () => {
        globalThis.fetch = vi.fn().mockResolvedValue({
            ok: false,
            json: async () => ({
                status: 'error',
                error: { code: 'UNAUTHORIZED', message: 'Yetkisiz' },
            }),
        });

        await expect(ApiClient.getTracks(1)).rejects.toThrow('Yetkisiz');
    });
});
```

### Çalıştırma

```bash
npx vitest
npx vitest run
npx vitest --coverage
```

---

## 13.4 E2E Test (Playwright)

### Kurulum

```bash
npm install --save-dev @playwright/test
npx playwright install
```

### Test Dosyası Örneği

```javascript
import { test, expect } from '@playwright/test';

test.describe('Login Flow', () => {
    test('user can login with valid credentials', async ({ page }) => {
        await page.goto('http://localhost/auth');
        
        // Gender select
        await page.click('[data-testid="gender-female"]');
        
        // Login form
        await page.fill('[data-testid="email"]', 'test@example.com');
        await page.fill('[data-testid="password"]', 'password123');
        await page.click('[data-testid="login-button"]');
        
        // Verify redirect to home
        await expect(page).toHaveURL(/.*home/);
        await expect(page.locator('[data-testid="welcome-message"]')).toBeVisible();
    });

    test('user sees error with invalid credentials', async ({ page }) => {
        await page.goto('http://localhost/auth');
        
        await page.click('[data-testid="gender-female"]');
        await page.fill('[data-testid="email"]', 'wrong@example.com');
        await page.fill('[data-testid="password"]', 'wrongpass');
        await page.click('[data-testid="login-button"]');
        
        await expect(page.locator('[data-testid="error-message"]')).toBeVisible();
    });
});
```

### Çalıştırma

```bash
npx playwright test
npx playwright test --ui
npx playwright show-report
```

---

<div style="page-break-after: always;"></div>

# BÖLÜM 14
# Sözlük & Kısaltmalar

| Terim | Kısaltma | Tanım |
|-------|----------|-------|
| **Architecture Decision Record** | ADR | Mimari karar kaydı |
| **Audio Stream Input/Output** | ASIO | Düşük gecikmeli ses protokolü |
| **Backend for Frontend** | BFF | İstemciye özel backend |
| **Boyce-Codd Normal Form** | BCNF | Veritabanı normalizasyonu |
| **Content Security Policy** | CSP | İçerik güvenliği politikası |
| **Command Query Responsibility Segregation** | CQRS | Okuma/yazma ayrımı |
| **Cross-Site Request Forgery** | CSRF | Siteler arası sahte istek |
| **Digital Signal Processing** | DSP | Dijital sinyal işleme |
| **Domain-Driven Design** | DDD | Alan odaklı tasarım |
| **Data Transfer Object** | DTO | Veri taşıma nesnesi |
| **Free Lossless Audio Codec** | FLAC | Kayıpsız ses formatı |
| **Hardware Abstraction Layer** | HAL | Donanım soyutlama katmanı |
| **Information Technology CSS** | ITCSS | Katmanlı CSS mimarisi |
| **JSON Web Token** | JWT | Yetki belirteci |
| **Low Frequency Effects** | LFE | Subwoofer kanalı |
| **Object-Relational Mapping** | ORM | Nesne-ilişkisel eşleme (YASAK) |
| **Open Web Application Security Project** | OWASP | Güvenlik standartları |
| **Pulse-Code Modulation** | PCM | Ham ses verisi |
| **Platform Interface** | PCI | Donanım arayüzü |
| **Role-Based Access Control** | RBAC | Rol bazlı erişim |
| **Single Source of Truth** | SSOT | Tek doğruluk kaynağı |
| **Structured Query Language** | SQL | Veritabanı sorgu dili |
| **WebSocket** | WS | Gerçek zamanlı iletişim |

---

<div style="page-break-after: always;"></div>

# BÖLÜM 15
# Ekler

## 15.1 Dizin Yapısı

```
coremusic.net/
├── .ai/                          # Vault (SSOT)
│   ├── CLAUDE.md                 # AI anayasası
│   ├── AGENTS.md                 # Agent kayıt defteri
│   ├── WORKFLOW.md               # Süreçler
│   ├── brain.md                  # Mimari kararlar
│   ├── index.md                  # Master katalog
│   ├── keys.md                   # Keyword haritası
│   ├── MEMORY.md                 # Session hafızası
│   ├── log.md                    # Audit trail
│   ├── engine.md                 # Orkestrasyon motoru
│   ├── ROLE.md                   # Rol tanımı
│   ├── VISION.md                 # Ürün vizyonu
│   ├── TECHNICAL_DOCUMENTATION.md # Teknik doküman
│   ├── FREELANCER_TECHNICAL_DOCUMENTATION.md # Bu dosya
│   ├── architecture/             # Mimari dokümanlar
│   ├── decisions/                # ADR'ler
│   ├── ui-design/                # UI tasarım sistemi
│   ├── electronic/               # Donanım/firmware
│   ├── projects/                 # Proje planları
│   ├── .sql/mysql/               # DB şemaları
│   └── .templates/               # Şablonlar
├── shared/                       # Ortak PHP altyapısı
│   ├── composer.json
│   ├── src/
│   │   ├── Controller/
│   │   ├── Service/
│   │   ├── Repository/
│   │   ├── Entity/
│   │   ├── Middleware/
│   │   ├── Database/
│   │   ├── Cache/
│   │   └── Http/
│   └── tests/
├── api.coremusic.net/            # API Gateway (L2)
├── auth.coremusic.net/           # Auth servisi (L1)
├── music.coremusic.net/          # Ana medya paneli (L3)
├── admin.coremusic.net/          # Yönetim paneli (L3)
├── home.coremusic.net/           # Ev medya merkezi (L3)
├── car.coremusic.net/            # Araç içi (L3)
├── studio.coremusic.net/         # Stüdyo paneli (L3)
├── pro.coremusic.net/            # Profesyonel panel (L3)
├── media.coremusic.net/          # Medya servisi (L0/L2)
├── download.coremusic.net/       # İndirme servisi (L2)
├── assets.coremusic.net/         # Statik dosyalar
├── .opencode/                    # OpenCode yapılandırması
├── .github/                      # GitHub Actions CI/CD
├── docker-compose.yml
├── .gitignore
└── README.md
```

---

## 15.2 ADR Listesi (79 Karar)

### Frozen ADR'ler (001-037) — Değiştirilemez

| ADR | Konu | Kategori |
|-----|------|----------|
| 001 | Vanilla JS + ITCSS, Framework Yasak | Frontend |
| 002 | PDO Mandatory, ORM Yasak | Database |
| 003 | Multi-DB 9 BCNF Veritabanı | Database |
| 004 | Multi-Domain SPA Mimarisi | Architecture |
| 005 | Ultrathink Protocol (Zero Hallucination) | Quality |
| 006 | Performance Targets | Performance |
| 007 | Cache Namespace Standardı | Infrastructure |
| 008 | Auth Bypass Middleware | Security |
| 009 | Clean URL Redirect | Routing |
| 010 | CSRF Protection Strategy | Security |
| 011 | Session Management | Security |
| 012 | CSP Nonce Strict-Dynamic | Security |
| 013 | Rate Limiting APCu | Security |
| 014-037 | Diğer frozen kararlar | Çeşitli |

### Active ADR'ler (038-088) — Güncellenebilir

| ADR | Konu | Kategori |
|-----|------|----------|
| 038 | 8.1 Ses Donanımı (PCM3168A + XMOS) | Audio |
| 039 | 7-Servis Platform Mimarisi | Architecture |
| 040 | 18 BCNF DB Otoritesi | Database |
| 042 | Vault Restructuring | Vault |
| 044 | Dynamic Theme Engine | Frontend |
| 083 | SPA Router Architecture | Routing |
| 084 | API Gateway Architecture | Architecture |
| 085 | Shared Library Hybrid | Architecture |
| 086 | Event Driven Architecture | Architecture |
| 087 | Master Implementation Plan | Architecture |

---

## 15.3 Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net (Control) | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 18 BCNF DB | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |
| 9743 | Neva Player | WS |

---

## 15.4 Veritabanı Tablo Envanteri (Özet)

| Veritabanı | Ana Tablolar | Toplam |
|------------|-------------|--------|
| coremusic_auth | users, roles, sessions, tokens, api_keys, credential_vault | 13 |
| coremusic_user | profiles, preferences, history, favorites | 7 |
| coremusic_musics | tracks, artists, genres, lyrics, files, podcasts, videos, radio | 22 |
| coremusic_albums | albums, discs, album_stats | 5 |
| coremusic_playlist | playlists, playlist_tracks, collaborators | 5 |
| coremusic_catalog | genres, artist_roles, instruments, moods | 8 |
| coremusic_logs | audit_trail, analytics, error_logs, performance_metrics | 22 |
| coremusic_media | device_sync, media_metadata, access_control | 8 |
| coremusic_system | settings, config, cache, eq_presets, notifications | 17 |
| coremusic_social | comments, shares, activity, listening_rooms | 9 |
| coremusic_wireless | wifi_networks, bluetooth_devices | 5 |
| coremusic_ai | preference_profiles, features, recommendations | 6 |
| coremusic_api | api_keys, rate_limits, api_logs | 4 |
| coremusic_cms | pages, blog, tags, media_assets, faqs | 8 |
| coremusic_download | queue, history, cache, sources | 4 |
| coremusic_neva | eq_presets, dsp_settings, routing_matrix | 4 |
| coremusic_studio | sessions, tracks, presets, equipment | 6 |
| coremusic_patch | schema_versions, migration_logs | 3 |
| | **TOPLAM** | **156** |

---

<div style="page-break-after: always;"></div>

## Son Notlar

### Doküman Geçmişi

| Versiyon | Tarih | Değişiklik |
|----------|-------|------------|
| 1.0.0 | Eylül 2026 | İlk yayınlanma |

### Sorumluluk Reddi

Bu belge "olduğu gibi" sunulmaktadır. CoreMusic ekibi, bu belgedeki bilgilerin doğruluğu veya eksiksizliği konusunda garanti vermez. Geliştiriciler, kendi sorumlulukları dahilinde çalışmalıdır.

### İletişim

| Kanal | Adres |
|-------|-------|
| **Proje Sahibi** | Bayram Ali — Vault Steward |
| **GitHub** | https://github.com/coremusic |
| **Web** | https://coremusic.net |

---

*CoreMusic Freelancer Teknik Dokümantasyon v1.0.0*
*Bu belge CoreMusic Confidential kapsamındadır.*
*İzinsiz kopyalanması veya dağıtılması yasaktır.*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-15
**Mode:** Red Team · Human Mode · Truth Mode
