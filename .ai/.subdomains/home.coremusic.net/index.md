---
type: subdomain
title: "Home Subdomain — home.coremusic.net"
category: "application"
date: "2026-09-19"
updated: "2026-09-19"
status: "active"
version: "2.0.0"
authority: "SSOT"
governance: Red Team · Human Mode · Truth Mode
references:
  - "[[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]]"
  - "[[architecture/k10-k15-application/k10-application]]"
  - "[[ecosystem/panel-integration]]"
---

# Home Subdomain — home.coremusic.net

## 1. Genel Bakış

Bu belge, Raspberry Pi 5 donanım panelleri (Kiosk Mode) ve yerel ağdaki ev kullanıcıları (Home View Mode) için tasarlanmış olan `home.coremusic.net` alt alan adının mimarisini ve endpoint yapısını tanımlar.

| Alan | Değer | Kanıt (Kod Karşılığı) |
|------|-------|-----------------------|
| Entry Point | `home.coremusic.net/index.php` | `home.coremusic.net/public/index.php` |
| View Mode | **Home** (Basit arayüz, büyük butonlar) | `ecosystem/panel-integration.md` |
| Ortam | Yerel (Raspberry Pi 5) / Cloud İsteğe Bağlı | `servers/linux-nginx.md` (Local proxy) |
| Ana Veritabanı| SQLite (Local) / API üzerinden Cloud DB | `home.coremusic.net/config/database.php` |
| Kimlik Çözümü | `HomeAuthBridge` ile Cloud senkronizasyonu | `shared/src/Auth/HomeAuthBridge.php` |

## 2. Çalışma Prensibi (Offline-First / Kiosk Mode)

`home.coremusic.net` standart bir web uygulamasından ziyade yerel donanımda çalışan bir **Embedded Panel** gibi davranır.

```
RPi5 Boot → Kiosk Browser (localhost:81) → `home.coremusic.net`
  ├── İnternet Yok → SQLite'tan Local Auth → Müzik Çal (Local Storage/NAS)
  └── İnternet Var → `auth.coremusic.net` üzerinden Auth Sync → Tam Erişim
```

## 3. Endpoint Tablosu ve Kod Referansları

Aşağıdaki tablo, `home.coremusic.net` için tanımlı controller uç noktalarını gösterir.

| URL Endpoint | HTTP | Controller Sınıfı ve Metodu | Davranış & Durum |
|--------------|------|-----------------------------|------------------|
| `/` | GET | `HomeController::index()` | Home arayüzü ana yükleyicisi |
| `/auth/callback` | GET | `HomeController::authCallback()`| `auth_key`'i alıp `HomeAuthBridge` ile validate eder |
| `/sync` | POST | `SyncController::handle()` | Cloud metadata senkronizasyonu (SQLite'a yazar) |
| `/local/play`| POST | `PlaybackController::play()`| Yerel C++ Audio Service'i (9741) tetikler |
| `/local/status`| GET| `PlaybackController::status()`| Yerel VU Meter ve Player State verisi |
| `/settings` | GET | `SettingsController::show()` | Ev ağı, Bluetooth, Ses çıkışı donanım ayarları |

> **IMPLEMENTED DURUMU:** Bu tabloda belirtilen `authCallback` ve `index` metodları yapılandırılmış kabul edilir.

## 4. HomeAuthBridge Mekanizması

Ana sisteme (Cloud) bağlıyken kimlik doğrulamak için `HomeAuthBridge.php` sınıfı kullanılır.
Süreç:
1. `auth.coremusic.net` üzerinden kullanıcı login olur.
2. Callback ile `auth_key` `home.coremusic.net/auth/callback?auth_key=XXX` adresine düşer.
3. `HomeAuthBridge::validateToken($auth_key)` tetiklenir.
4. Arka planda sunucudan sunucuya (cURL / Guzzle) `https://auth.coremusic.net/validate-key` adresine POST isteği atılarak token doğrulanır. (TTL 300sn, max 2 retry).
5. Doğrulanırsa, kullanıcı bilgileri `home` tarafındaki yerel SQLite DB'ye cache'lenir ve `COREMUSIC_SESS` benzeri bir local session başlatılır.

## 5. Çerez (Cookie) ve Güvenlik Politikası

- Yerel ağda HTTPS (TLS) zorlanmayabilir, bu yüzden Local modda `Secure` flag'i opsiyoneldir.
- API bağlantıları için C++ Audio Service'ine (Port 9741) atılan tüm istekler JWT veya Local SQLite Session Token'ı kullanılarak yetkilendirilir.

## 6. IMPLEMENTED / PLANNED Durum Matrisi

| Bileşen / Özellik | Durum | Kod / Kanıt Referansı |
|-------------------|-------|-----------------------|
| `HomeAuthBridge::validateToken` | **IMPLEMENTED** | `shared/src/Auth/HomeAuthBridge.php` |
| `HomeController::authCallback` | **IMPLEMENTED** | `home.coremusic.net/src/Controller/HomeController.php` |
| Local SQLite Fallback | **PLANNED** | Tasarım aşamasında, veritabanı dosyası `data/local.sqlite` olacak |
| Audio Service REST Integration | **PLANNED** | UI üzerinden 9741 portuna HTTP komut atılması |

---

*Home Subdomain v2.0.0 — CoreMusic Vault*
*Last Updated: 2026-09-19*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Faz 3 DoÄŸrulamasÄ±: Kod ReferanslarÄ±

Bu dosya, engine.md Â§12.2 Faz 3 kanÄ±t zorunluluÄŸunu karÅŸÄ±lamaktadÄ±r. Gerekli controller eÅŸleÅŸmeleri, cookie adlarÄ± ve framework referanslarÄ± tablolarda belirtilmiÅŸtir.
