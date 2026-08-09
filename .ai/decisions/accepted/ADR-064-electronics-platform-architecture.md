---
type: adr
category: electronics-architecture
title: "ADR-064: CoreMusic Electronics Platform Architecture"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-064: CoreMusic Electronics Platform Architecture

**Zorunlu Bağlantılar:** [[ADR-038-8.1-sound-card-chip-selection]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-040-database-authority]] · [[ADR-061-electronics-architecture]] · [[ADR-062-dsp-pipeline-architecture]] · [[ADR-063-hardware-design-standards]]

---

## 1. Status

**Active** — 2026-08-09'da kabul edildi.

---

## 2. Context

CoreMusic Electronics ekosistemi, 26 bölümlük kapsamlı bir mimari belge ile desteklenmektedir. Bu belge, 9+ katmanlı platform yapısını, 5 cihaz ailesini, 13 servisi ve ortak mühendislik altyapısını tanımlamaktadır.

**Mevcut Durum:**
- L0-L3 katman yapısı (CoreMusic standardı) mevcut
- L4-L6 electronics genişletmesi gerekli
- 5 cihaz ailesi tanımlı (Desktop, Laptop, RPi, NAS, Car)
- 13 servis tanımlı (Control, Media, Audio, Device, Network, AI, Download, vb.)
- Ortak mühendislik altyapısı (Shared Library) tanımlı

**Sorun:**
- Electronics katmanı L0-L3 ile tutarlı olmalı
- Cihaz aileleri arasındaki farklar belgelenmeli
- Servis mimarisi net tanımlanmalı
- Platform mimarisi tek bir kaynaktan yönetilmeli

---

## 3. Decision

CoreMusic Electronics Platform Architecture aşağıdaki bileşenlerden oluşur:

### 3.1 L0-L6 Katman Yapısı

| Katman | Ad | Kapsam |
|--------|-----|--------|
| **L0** | Infrastructure | DB, cache, filesystem, IPC |
| **L1** | Security | Middleware, session, CSRF, CSP |
| **L2** | Routing | SPA, PageRouter, subdomain |
| **L3** | Presentation | Vanilla JS, ITCSS, Web Audio |
| **L4** | Domain | Business logic, DDD, use cases |
| **L5** | Services | 13 servis, API Gateway, event bus |
| **L6** | Electronics | Hardware, firmware, driver, DSP |

**Bağımlılık:** L6→L5→L4→L3→L2→L1→L0

### 3.2 5 Cihaz Ailesi

| # | Aile | OS | Donanım | Kullanım |
|---|------|-----|---------|----------|
| 1 | **Desktop** | Windows/Linux/macOS | PC/Laptop | Ana geliştirme, stüdyo |
| 2 | **Laptop** | Windows/Linux | Taşınabilir | Mobil üretim |
| 3 | **Raspberry Pi** | Debian ARM64 | RPi 5 | Ev, araç, gömülü |
| 4 | **NAS** | Linux (Docker) | Synology/QNAP | Medya sunucusu |
| 5 | **Car** | Windows/Android Auto | RPi + PCM3168A | Araç içi bilgi-eğlence |

### 3.3 13 Servis

| # | Servis | Port | Protokol | Kapsam |
|---|--------|------|----------|--------|
| 1 | Control Service | 81 | HTTP | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | Streaming, multi-room |
| 6 | AI Service | — | Internal | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Deezer/YouTube indirme |
| 8 | Auth Service | — | HTTP | Kimlik doğrulama |
| 9 | Admin Service | 80 | HTTP | Yönetim paneli |
| 10 | Landing Service | 80 | HTTP | Ana sayfa |
| 11 | Home Service | 81 | HTTP | Ev medya merkezi |
| 12 | Studio Service | 81 | HTTP | Profesyonel stüdyo |
| 13 | Pro Service | 81 | HTTP | Profesyonel panel |

### 3.4 Ortak Mühendislik Altyapısı

| Modül | Kapsam |
|-------|--------|
| Auth | Kimlik doğrulama, yetkilendirme |
| Security | Şifreleme, CSRF, CSP |
| Http | İstek/yanıt yönetimi |
| Router | Yönlendirme |
| Container | Bağımlılık enjeksiyonu |
| Event | Olay yönetimi |

---

## 4. Consequences

### 4.1 Olumlu

| Sonuç | Açıklama |
|-------|----------|
| Modüler Geliştirme | Her cihaz ailesi bağımsız geliştirilebilir |
| Ölçeklenebilirlik | Yeni cihaz aileleri kolayca eklenebilir |
| Bakım Kolaylığı | Ortak altyapı tek yerden yönetilir |
| Tutarlılık | L0-L6 katman yapısı tüm ekosistemde geçerli |
| Güvenlik | Katmanlı güvenlik mimarisi |

### 4.2 Olumsuz

| Sonuç | Açıklama |
|-------|----------|
| Karmaşıklık | 7 katman, 13 servis yönetimi zor |
| Öğrenme Eğrisi | Yeni geliştiriciler için kapsamlı eğitim gerekli |
| Performans | Katmanlar arası geçiş overhead |

### 4.3 Riskler

| Risk | Olasılık | Etki | Azaltma |
|------|----------|------|---------|
| Katman ihlali | Orta | Yüksek | Otomatik testler |
| Servis çökmesi | Düşük | Yüksek | Health check, retry |
| Güvenlik açığı | Düşük | Kritik | OWASP uyumluluğu |

---

## 5. References

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 |
| [[ADR-039-7-service-platform-architecture]] | 7 servis platform mimarisi |
| [[ADR-040-database-authority]] | 9 BCNF veritabanı |
| [[ADR-061-electronics-architecture]] | Electronics Architecture (L6) |
| [[ADR-062-dsp-pipeline-architecture]] | DSP Pipeline Architecture |
| [[ADR-063-hardware-design-standards]] | Hardware Design Standards |

---

## 6. Related Files

| Dosya | Amaç |
|-------|------|
| [[architecture/l6-electronics]] | L6 Electronics katmanı |
| [[architecture/network-architecture]] | Ağ mimarisi |
| [[architecture/database-architecture]] | Veritabanı mimarisi |
| [[architecture/security-architecture]] | Güvenlik mimarisi |
| [[electronic/platform-architecture]] | Platform mimarisi |
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/service-architecture]] | Servis mimarisi |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Active |
| Decision Type | Architecture |
| Impact | High |
| ADR References | 6 |
| Related Files | 7 |
| Platform Layers | 7 (L0-L6) |
| Device Families | 5 |
| Services | 13 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
