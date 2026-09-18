# 🎵 CoreMusic

**Kurumsal Seviyede Dijital Medya Yönetim Platformu**

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES2022-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://tc39.es/ecma262/)
[![C++](https://img.shields.io/badge/C++-20-00599C?style=flat&logo=cplusplus&logoColor=white)](https://isocpp.org/)
[![MySQL](https://img.shields.io/badge/MySQL-9-4479A1?style=flat&logo=mysql&logoColor=white)](https://dev.mysql.com/)
[![Architecture](https://img.shields.io/badge/Architecture-K0_to_K20-blue)](#mimari-yapı)
[![License](https://img.shields.io/badge/License-Proprietary-red)](#lisans)
[![Status](https://img.shields.io/badge/Status-Aktif%20Geliştirme-brightgreen)](#proje-durumu)

---

## 📋 İçindekiler

- [Proje Tanımı](#proje-tanımı)
- [Vizyon](#vizyon)
- [Temel Özellikler](#temel-özellikler)
- [Teknoloji Yığını](#teknoloji-yığını)
- [Mimari Yapı (K0-K20)](#mimari-yapı)
- [Servis Haritası](#servis-haritası)
- [Veritabanı Yapısı](#veritabanı-yapısı)
- [Donanım ve Akustik Mimarisi](#donanım-ve-akustik-mimarisi)
- [Kurulum Rehberi](#kurulum-rehberi)
- [Geliştirme Ortamı](#geliştirme-ortamı)
- [Yol Haritası](#yol-haritası)
- [Lisans](#lisans)
- [AI Engineering (Vault)](#ai-engineering)
- [İletişim](#iletişim)

---

## 🎯 Proje Tanımı

**CoreMusic**, bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi bilgi-eğlence ve ev medya merkezleri için tasarlanmış **kurumsal seviyede dijital medya yönetim platformudur**.

Sadece yazılımsal bir platform olmanın ötesinde, entegre C++ DSP motoru, Class AB amplifikatör donanım tasarımları ve 1000'den fazla bağımsız bileşene ev sahipliği yapan tam teşekküllü bir **Ecosystem** olarak inşa edilmiştir.

### Hedef Kullanıcılar

| Kullanıcı Grubu | Kullanım Senaryosu |
|-----------------|-------------------|
| 🎧 Bireysel Kullanıcılar | Kişisel müzik kütüphane yönetimi, çevrimdışı dinleme |
| 🎛️ Profesyonel Üreticiler | Stüdyo kalitesinde ses, 31-band EQ, 64-bit float DSP |
| 🏢 Stüdyolar | 8.1 surround ses (NevaEngine), çoklu oda senkronizasyonu |
| 🚗 Araç İçi | Özel araç paneli (Car UI), düşük gecikmeli ses |
| 🏠 Ev Medya | NAS entegrasyonu, multi-room ağ yayını (WebRTC/P2P) |

---

## 🔭 Vizyon

CoreMusic, müzik dinleme ve yönetme deneyimini **katmanlı, modüler ve uzatılabilir** bir yapıyla yeniden tanımlamayı hedefler:

- **Faz 1 (Yazılım Çekirdeği):** Mevcut PC/laptop'larda temel medya platformu
- **Faz 2 (Donanım/Premium):** CoreMusic Audio donanım entegrasyonu (PCM3168A, XMOS XU316) ve Class AB 100W amplifikatör entegrasyonu (ADR-089).
- **Faz 3 (Professional):** Tam entegre stüdyo (8.1 Surround NevaEngine) ve araç içi sistemler

---

## ✨ Temel Özellikler

### 🎵 Medya Yönetimi
- Otonom müzik indirme servisleri
- Kapsamlı müzik kütüphane yönetimi (sanatçı, albüm, tür, sözler)
- AI destekli akıllı çalma listeleri ve metadata çekimi

### 🔊 Profesyonel Ses (NevaEngine C++20)
- 15 aşamalı DSP pipeline (ADR-062)
- Zero-allocation memory, lock-free ring buffer
- 8.1 surround ses desteği ve ASIO/WASAPI donanım köprüsü
- Sınıfının en iyisi SNR (>112dB) performansı ile High-End ses çıkışı

### 🖥️ Çoklu Panel & Mikroservis Ağı
- 10 bağımsız web paneli (music, admin, download, auth, vb.)
- 7 ana mikroservis mimarisi (Event Driven Architecture)
- SSR + Vanilla JS Hybrid SPA (Frameworksüz saf performans)

### 🔒 Güvenlik
- 10-katmanlı bükülemez güvenlik middleware zinciri (OriginCheck → Validation)
- AES-256-GCM / Argon2id kriptografi omurgası
- BCNF izolasyonlu veritabanı ayrışımı

---

## 🛠️ Teknoloji Yığını

### Yazılım - Backend
- **PHP 8.4+** (API, Middleware, Strict-types)
- **Node.js (TypeScript)** (Download Service / Asenkron Kuyruklar)
- **MySQL 9+** (18 İzole BCNF Şeması)
- **Redis / APCu** (Event bus, Memory Caching)

### Yazılım - Frontend
- **Vanilla JS (ES2022)** (SPA router, DOM manipulation)
- **ITCSS 7-Layer + BEM** (Saf CSS, Frameworksüz)
- **TrustedTypes** (DOMParser ile saf güvenlik)

### Ses ve Elektronik Donanımı
- **C++20 (JUCE, ASIO SDK)** (NevaEngine)
- **XMOS XU316** (USB Audio Class 2.0 / Multichannel)
- **PCM3168A** (6-In / 8-Out 24-bit/192kHz DAC)
- **Class AB 100W** (High-Fidelity Amplifikatör)

---

## 🏗️ Mimari Yapı (Master Vault Architecture)

CoreMusic mimarisi, yazılımdan donanıma uzanan **21 Katmanlı (K0-K20)** ve **1000'den fazla** mikro-bileşen barındıran devasa bir matrise evrimleşmiştir (ADR-042).

```text
============================================================
[ L4 ] K16-K20: DONANIM VE ELEKTRONİK (Electronics / PCB)
       └── Amplifikatör, DSP Chip, DAC, PSU, Sensörler
============================================================
[ L3 ] K12-K15: ENİNE KESEN KATMANLAR (Cross-Cutting)
       └── Loglama, CI/CD, Network Ağ, Medya Streaming
============================================================
[ L2 ] K6-K11: YAZILIM / UYGULAMA (Application Core)
       └── Güvenlik, Middleware, Servisler, API Routing, UX
============================================================
[ L1 ] K0-K5: ALT-SİSTEM & ÇEKİRDEK (OS / Driver / C++)
       └── OS Layer, Drivers, NevaEngine (Audio), AI Engine
============================================================
```

**Kritik Kurallar:**
- **ORM Kullanımı Kesinlikle Yasaktır:** Sadece ham PDO parametreli sorgular kullanılır.
- **Frontend Framework Yasaktır:** React, Vue, Angular kullanılamaz; Vanilla JS standarttır.
- **Güvenlik Pipeline'ı Değiştirilemez:** CSRF, Session, Auth başlıkları donanımsal mantıkla art arda çalışır.

---

## 🗺️ Servis Haritası

CoreMusic, bağımsız ölçeklenebilen 7 mikroservisten oluşur (Faz 3 Entegrasyon standartları ile izlenmektedir):

1. **Control Service:** PHP 8.4 (Auth, Session, Yönetim Paneli)
2. **Media Service:** PHP + FFmpeg (Kütüphane indexleme)
3. **Audio Service:** C++20 NevaEngine (Donanım/Ses işlemleri)
4. **Device Service:** C++ (BLE, USB Cihaz Eşleşmeleri)
5. **Network Audio:** C++ (WebRTC, P2P Multi-room aktarım)
6. **AI Service:** Python/PHP (Öneri ve EQ algoritmaları)
7. **Download Service:** Node.js (Platform dışı medya tedariği)

---

## 🗄️ Veritabanı Yapısı

Sistem, veri izolasyonu ve güvenlik (ADR-040) amacıyla birbirinden kesin çizgilerle ayrılmış **18 BCNF İzole Veritabanı** kullanır:

`coremusic_auth`, `coremusic_user`, `coremusic_musics`, `coremusic_system`, `coremusic_neva` (DSP Config), `coremusic_studio`... (Toplam 150+ Tablo)

---

## 🔧 Donanım ve Akustik Mimarisi

CoreMusic aynı zamanda kapalı-kutu bir ses donanımı projesidir. Devre tasarımları `.ai/electronics/` altında belgelenmiştir.

- **Ses Yongası:** Texas Instruments PCM3168A (ADR-038)
- **İşlemci (DSP):** XMOS XU316
- **Amplifikasyon:** ±24V Dual-Rail Class AB (100W@8Ω) (ADR-089)
- **Akustik Başarı:** >112dB SNR, %0.005 THD+N

---

## 🤖 AI Engineering (Vault SSOT)

CoreMusic, proje hafızasını ve mimari kararları `.ai/` klasöründeki AI Vault (Vault SSOT) üzerinden yürütür. Kök dizindeki dosyalar sadece yönlendiricidir (Pointer). 

> [!WARNING]
> **AI AGENT UYARISI:** Lütfen ortam bağlamını, iş akışlarını ve güvenlik kurallarını okumak için derhal [`.ai/CLAUDE.md`](.ai/CLAUDE.md) anayasasına başvurun. Vault dışında kalıcı mimari karar alınamaz.

### Vault Hızlı Linkleri
- **[AI Anayasası (CLAUDE.md)](.ai/CLAUDE.md)**
- **[Agent Kayıt Defteri (AGENTS.md)](.ai/AGENTS.md)**
- **[İş Akışları (WORKFLOW.md)](.ai/WORKFLOW.md)**
- **[Mimari Kararlar (brain.md)](.ai/brain.md)**
- **[Master İndeks (.ai/index.md)](.ai/index.md)**

---

## 📊 Proje İstatistikleri (Faz 3 Sonrası)

| Metrik | Güncel Değer |
|--------|-------|
| **Mimari Katman** | 21 Katman (K0-K20) |
| **Bileşen Sayısı** | 1000+ Komponent |
| **ADR Kaydı** | 89 Kabul Edilmiş Karar (ADR) |
| **Veritabanı** | 18 İzole Şema (BCNF) |
| **Bağımsız Panel** | 10 (music, admin, download, home...) |
| **Süreç Durumu** | Faz 3 Tamamlandı (Ecosystem / Servers) |

---

## 📜 Lisans

Bu proje **kapalı kaynak** (proprietary) lisansla yayınlanmaktadır. 
Telif hakkı © 2026 CoreMusic. Tüm hakları saklıdır.
