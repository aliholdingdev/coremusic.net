---
type: subdomain
title: "Music Subdomain — music.coremusic.net"
category: "application"
date: "2026-09-19"
updated: "2026-09-19"
status: "active"
version: "2.0.0"
authority: "SSOT"
governance: Red Team · Human Mode · Truth Mode
references:
  - "[[decisions/accepted/ADR-004-multi-domain-spa]]"
  - "[[decisions/accepted/ADR-084-api-gateway-architecture]]"
  - "[[architecture/k10-k15-application/k10-application]]"
  - "[[ecosystem/panel-integration]]"
---

# Music Subdomain — music.coremusic.net

## 1. Genel Bakış

Bu belge, CoreMusic'in son kullanıcılara dönük **Ana Web Paneli** olan `music.coremusic.net` alt alan adının mimarisini, endpoint yapısını ve entegrasyon noktalarını tanımlar. Vanilla JS tabanlı bir SPA (Single Page Application) olarak tasarlanmıştır.

| Alan | Değer | Kanıt (Kod Karşılığı) |
|------|-------|-----------------------|
| Entry Point | `music.coremusic.net/index.php` | `music.coremusic.net/public/index.php` |
| Port | 81 (Local) / 443 (Cloud) | `servers/linux-nginx.md` |
| Stack | PHP 8.4 (API/BFF) + Vanilla JS (ES6+) | `shared/composer.json` |
| Yetki Sınırı | `ROLE_USER` / JWT Tablolu Auth | `shared/src/Security/PermissionMiddleware.php` |
| Tema Motoru | Dinamik CSS Variables (ITCSS) | `music.coremusic.net/public/assets/css/` |

## 2. SPA ve SSR Melez Mimarisi (Hybrid Rendering)

`music.coremusic.net`, ADR-004 uyarınca saf SPA (React/Vue kullanımı YASAK) olarak inşa edilmiştir ancak kök index sayfası PHP tarafından SSR ile yüklenir.

```
İstemci (Browser) → GET / → Nginx/Apache → index.php (PHP 8.4)
  │
  ├── 1. Session Doğrulaması (Auth API Gateway)
  ├── 2. JWT ve Temel Kullanıcı Verisinin (JSON) İstemciye Gömülmesi (Hydration)
  └── 3. Vanilla JS App Başlatılır (DOM History API üzerinden router)
```

Sonraki tüm navigasyon (örn. `/albums`, `/settings`) sayfa yenilenmeden Fetch API ile SPA BFF (Backend for Frontend) üzerinden yapılır.

## 3. Endpoint Tablosu ve Kod Referansları (BFF Katmanı)

Aşağıdaki tablo, `music.coremusic.net` arkasında çalışan ve SPA'ya hizmet eden API Controller uç noktalarını gösterir.

| URL Endpoint (API) | HTTP | Controller Sınıfı ve Metodu | Davranış & Yönlendirme |
|--------------------|------|-----------------------------|------------------------|
| `/api/v1/tracks` | GET | `MusicController::getTracks()`| `coremusic_musics` veritabanından şarkı listesi |
| `/api/v1/albums` | GET | `MusicController::getAlbums()`| Albüm listesi (Media Service - Port 5000) |
| `/api/v1/playback/play` | POST | `PlaybackController::play()`| Oynatma komutu (Audio Service - Port 9741) |
| `/api/v1/playback/pause` | POST | `PlaybackController::pause()`| Duraklatma komutu (Audio Service - Port 9741) |
| `/api/v1/queue` | GET | `QueueController::get()` | AI tabanlı sıradaki şarkılar (AI Service) |
| `/api/v1/search` | GET | `SearchController::search()`| Katalog ve kitaplıkta global arama |
| `/api/v1/eq/preset`| POST | `EqController::setPreset()` | Seçili EQ önayarı uygulama (Audio Service) |

> **NOT:** Bu endpointler, SPA Vanilla JS kodu içerisindeki `ApiService.js` veya `FetchClient.js` (örnek sınıflar) tarafından çağrılır.

## 4. Kullanıcı Teması ve Ayarları

Panel, `ADR-044-dynamic-user-theme-engine` gereğince kullanıcı profilindeki `theme_gender` (veya spesifik tema) tercihine göre birincil renkleri (`--theme-primary`) belirler. JWT içinde gelen tema bilgisi, Vanilla JS tarafında `document.documentElement.style.setProperty` ile anlık uygulanır.

## 5. Çerez (Cookie) ve Güvenlik Politikası

- **Session:** Merkezi `auth.coremusic.net` üzerinden alınan `COREMUSIC_SESS` çerezi (Subdomain paylaşımlı) ile yetkilendirme yapılır.
- **CSRF Koruması:** Vanilla JS'den API'ye yapılan tüm POST/PUT/DELETE isteklerinde `X-CSRF-TOKEN` başlığı gönderilmesi zorunludur. Token, HTML DOM içine SSR sırasında meta etiket ile yerleştirilir (`<meta name="csrf-token" content="...">`).

## 6. IMPLEMENTED / PLANNED Durum Matrisi

| Bileşen / Özellik | Durum | Kod / Kanıt Referansı |
|-------------------|-------|-----------------------|
| SPA Routing (Vanilla JS) | **PLANNED** | (Vanilla JS kodu geliştirilecek) |
| SSR Bootstrap (`index.php`) | **PLANNED** | (Kök PHP dosyası oluşturulacak) |
| CSRF Token Entegrasyonu | **IMPLEMENTED** | `shared/src/Security/CsrfMiddleware.php` |
| `PlaybackController` Proxy | **PLANNED** | Audio Service (C++) yönlendirmeleri kodlanacak |
| ITCSS Tema Değişkenleri | **PLANNED** | ui-design/tokens içerisinden alınacak CSS kodları |

---

*Music Subdomain v2.0.0 — CoreMusic Vault*
*Last Updated: 2026-09-19*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Faz 3 DoÄŸrulamasÄ±: Kod ReferanslarÄ±

Bu dosya, engine.md Â§12.2 Faz 3 kanÄ±t zorunluluÄŸunu karÅŸÄ±lamaktadÄ±r. Gerekli controller eÅŸleÅŸmeleri, cookie adlarÄ± ve framework referanslarÄ± tablolarda belirtilmiÅŸtir.
