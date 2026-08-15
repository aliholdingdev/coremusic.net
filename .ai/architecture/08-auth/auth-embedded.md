---
type: architecture
category: auth
title: "Enterprise Auth — Embedded System Authentication (RPi5)"
date: 2026-08-12
updated: 2026-08-12
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Embedded System Authentication (RPi5)

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

**İlgili ADR:** [[ADR-060-rpi5-embedded-auth]]

## 1. Amaç

Raspberry Pi 5 üzerinde çalışan embedded sistemlerin (Home, Pro, Studio, Car) authentication mimarisini tanımlar. Bu sistemler Volumio benzeri yerel medya işletim sistemleridir.

## 2. Embedded Sistem Tanımı

| Mod | Donanım | Amaç | Veritabanı |
|-----|---------|------|------------|
| **Home** | RPi5 + Touch Screen + PCM3168A | Ev teybi medya merkezi | SQLite (local) |
| **Pro** | RPi5 + HDMI Display + Class AB | Profesyonel ses sistemi | SQLite (local) |
| **Studio** | RPi5 + 8.1 Surround | Stüdyo monitoring sistemi | SQLite (local) |
| **Car** | RPi5 + PCM3168A | Araç içi bilgi-eğlence | SQLite (local) |

**Temel Özellikler:**
- Tarayıcı arayüzü tam ekran olarak çalışır
- Dokunmatik ekran desteği (48px minimum touch target)
- Yerel sunucu modunda çalışır (internet gerekmez)
- Browser control panel — tarayıcı bir kontrol paneli gibi davranır
- Volumio benzeri ancak çok daha gelişmiş

*Kaynak: [[ADR-060-rpi5-embedded-auth]]*

## 3. Embedded Auth Akışı

### 3.1 İlk Kurulum (Setup Mode)

```
┌─────────────────────────────────────────────────────────────────┐
│              İLK KURULUM AUTH AKIŞI                              │
│              (Setup Mode — İlk açılış)                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. RPi5 ilk kez açılır                                         │
│     │                                                           │
│     ▼                                                           │
│  2. Setup wizard başlatılır                                     │
│     │                                                           │
│     ▼                                                           │
│  3. Kullanıcı auth.coremusic.net'e bağlanmak ister mi? sorusu   │
│     │                                                           │
│     ├── Evet → auth.coremusic.net/login'e redirect              │
│     │          │                                                │
│     │          ├── İnternet mevcut → Online auth                │
│     │          │   │                                            │
│     │          │   ├── Login başarılı → JWT + Session al       │
│     │          │   │                                            │
│     │          │   └── local DB'ye kaydet                      │
│     │          │                                                │
│     │          └── İnternet yok → Offline mode                 │
│     │               │                                           │
│     │               └── Local admin hesabı oluştur             │
│     │                                                           │
│     └── Hayır → Local-only mode                                │
│                  │                                              │
│                  └── Local admin hesabı oluştur                │
│                                                                 │
│  4. Local DB'ye (SQLite) kaydet                                 │
│     │                                                           │
│     ▼                                                           │
│  5. Auth modu: "local" veya "hybrid" olarak ayarla             │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Normal Çalışma (Runtime Mode)

```
┌─────────────────────────────────────────────────────────────────┐
│              NORMAL ÇALIŞMA AUTH AKIŞI                           │
│              (Runtime Mode — Günlük kullanım)                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → home.coremusic.net'e erişir                     │
│     │                                                           │
│     ▼                                                           │
│  2. Auth modu kontrolü                                          │
│     │                                                           │
│     ├── Mod: "local" → Local SQLite session kontrolü            │
│     │                                                           │
│     ├── Mod: "hybrid" → auth_key cookie (JWT) kontrolü          │
│     │   │                                                       │
│     │   ├── JWT geçerli → Local session'a yaz                   │
│     │   │                                                       │
│     │   └── JWT geçersiz → auth.coremusic.net'e redirect        │
│     │                                                           │
│     └── Mod: "cloud" → auth.coremusic.net API kontrolü          │
│                                                                 │
│  3. Kullanıcı authenticated → Dashboard göster                   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 4. Auth Modları

| Mod | Tanım | İnternet | Kullanım |
|-----|-------|----------|----------|
| **local** | Sadece yerel SQLite, cloud yok | Gerekmez | Tamamen offline |
| **hybrid** | İlk bağlanma cloud, sonrası local | Gerekmez (bağlantı sonrası) | Önerilen |
| **cloud** | Her istekte auth.coremusic.net | Gerekir | Tam online |

### 4.1 Local Mode

```
RPi5 (Local Mode)
├── SQLite DB: /data/coremusic_auth.db
├── Admin hesabı: İlk kurulumda oluşturulur
├── Session: PHP native session (server-side)
├── Cookie: HttpOnly, Secure=false (HTTP)
└── Cloud bağlantısı: Yok
```

### 4.2 Hybrid Mode (Önerilen)

```
RPi5 (Hybrid Mode)
├── İlk bağlanma: auth.coremusic.net'e bağlan
├── JWT token'ı al ve local DB'ye kaydet
├── Offline: Local SQLite session
├── Online: auth.coremusic.net JWT refresh
├── Token yenileme: İnternet varsa otomatik
└── Offline-first: İnternet yoksa local devam
```

### 4.3 Cloud Mode

```
RPi5 (Cloud Mode)
├── Her istek: auth.coremusic.net API çağrısı
├── Session: auth.coremusic.net tarafından yönetilir
├── JWT: Her istekte doğrulanır
├── Cache: APCu/Redis ile token cache
└── Fallback: İnternet yoksa login sayfası göster
```

## 5. Local SQLite Şeması

```sql
-- Embedded auth için minimal SQLite şeması
CREATE TABLE local_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'admin',
    auth_mode TEXT NOT NULL DEFAULT 'local',
    cloud_user_id INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);

CREATE TABLE local_sessions (
    id TEXT PRIMARY KEY,
    user_id INTEGER NOT NULL,
    ip_address TEXT,
    user_agent TEXT,
    last_activity DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES local_users(id)
);

CREATE TABLE local_preferences (
    user_id INTEGER NOT NULL,
    key TEXT NOT NULL,
    value TEXT,
    PRIMARY KEY (user_id, key),
    FOREIGN KEY (user_id) REFERENCES local_users(id)
);
```

## 6. Embedded Security

| KorumA | Yöntem | Not |
|--------|--------|-----|
| **Session Fixation** | session_regenerate_id(true) | Login sonrası |
| **Brute Force** | Rate limiting (APCu) | 5 req/60s |
| **XSS** | HttpOnly cookie | JS erişimi yasak |
| **CSRF** | csrf_token | SameSite=Lax |
| **Offline Security** | Local SQLite encryption | Opsiyonel |
| **Physical Access** | PIN lock | Opsiyonel |

## 7. İlk Kurulum Wizard

```
┌─────────────────────────────────────────────────────────────────┐
│              İLK KURULUM WIZARD                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Adım 1: Hoş geldiniz                                          │
│    └─ CoreMusic'e hoş geldiniz. Kuruluma başlayın.            │
│                                                                 │
│  Adım 2: Auth modu seçin                                       │
│    └─ ○ Hybrid (Önerilen) — Cloud + Local                      │
│    └─ ○ Local — Sadece yerel                                   │
│    └─ ○ Cloud — Sadece online                                  │
│                                                                 │
│  Adım 3: Hybrid mod için auth.coremusic.net'e bağlanın         │
│    └─ Email: [________________]                                │
│    └─ Şifre: [________________]                                │
│    └─ [Bağlan]                                                 │
│                                                                 │
│  Adım 4: Local admin hesabı oluşturun (yedek)                  │
│    └─ Kullanıcı adı: [________________]                        │
│    └─ Şifre: [________________]                                │
│    └─ Şifre tekrar: [________________]                         │
│                                                                 │
│  Adım 5: Medya klasörünü seçin                                 │
│    └─ /home/pi/Music/ (varsayılan)                             │
│    └─ [Değiştir]                                               │
│                                                                 │
│  Adım 6: Kurulum tamamlandı                                    │
│    └─ [Dashboard'a Git]                                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 8. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Embedded'de production key kullanımı | Test key (development) | Güvenlik açığı |
| Offline mode'da cloud auth denemesi | Local fallback | Timeout hatası |
| SQLite'da SELECT * | Explicit columns | SQL injection riski |
| Local admin şifresi hardcoded | İlk kurulumda oluşturma | Güvenlik açığı |
| Embedded'de HTTPS zorunluluğu | HTTP yeterli (local network) | Gereksiz karmaşıklık |

## 9. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | İlk kurulum wizard'ı **zorunlu** | Yapılandırma hatası |
| 2 | Local admin hesabı **zorunlu** (hybrid/cloud modda) | Erişim kaybı |
| 3 | SQLite DB **şifreli** (opsiyonel ama önerilen) | Veri sızıntısı |
| 4 | Session timeout **3600s** | Güvenlik açığı |
| 5 | Rate limiting **devrede** (embedded'de de) | Brute force |

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Auth Modları | 3 (local, hybrid, cloud) |
| Embedded Sistemler | 4 (Home, Pro, Studio, Car) |
| ADR Uyumlu | ✅ 060 |
| Zero Hallucination | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode
