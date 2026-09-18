---
title: "K10 — Uygulama Katmanı (Application Layer)"
type: architecture
category: layer-definition
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k10-application.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K10
  component_count: 45
  adr:
    - "[[ADR-039-7-service-platform-architecture]]"
    - "[[ADR-044-dynamic-user-theme-engine]]"
    - "[[ADR-045-multi-domain-view-mode-architecture]]"
  github:
    - name: "Leafplayer"
      url: "https://github.com/paulschwoerer/leafplayer"
    - name: "Musable"
      url: "https://github.com/musable/musable"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K10 — Uygulama Katmanı (Application Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic ekosistemi için 10 web paneli ve alt sayfa bileşenleri. Her panel bağımsız bir subdomain'de çalışır. 45 bileşen.

---

## 1. Genel Bakış

K10 katmanı, CoreMusic'in kullanıcıya sunduğu tüm web panellerini ve sayfa bileşenlerini tanımlar. Her panel kendi subdomain'inde çalışır ve K8 (Services) katmanındaki ilgili servisleri kullanır.

### 1.1 Panel Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    K10 — APPLICATION LAYER (45)                     │
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│  MUSIC (8)   │  ADMIN (4)   │  HOME (3)    │  CAR (2)               │
│              │              │              │                        │
│  Library     │  Dashboard   │  Dashboard   │  Dashboard             │
│  Search      │  User Mgmt   │  Player      │  Player                │
│  Player      │  Content Mgmt│  Settings    │  Navigation            │
│  Playlist    │  System Set  │              │                        │
│  Artist      │              │              │                        │
│  Album       │              │              │                        │
│  Settings    │              │              │                        │
│  Home Page   │              │              │                        │
├──────────────┴──────────────┴──────────────┴────────────────────────┤
│  STUDIO (3)  │  DOWNLOAD (3)│  LANDING (3) │  PRO (2)   │ MEDIA(2) │
│              │              │              │            │          │
│  Mixer       │  Queue       │  Hero        │  Dashboard │ Process  │
│  EQ          │  History     │  Features    │  Analytics │ Convert  │
│  Monitor     │  Settings    │  Pricing     │            │          │
└──────────────┴─────────────┴──────────────┴────────────┴──────────┘
```

---

## 2. music.coremusic.net — Ana Medya Paneli (8 Bileşen)

CoreMusic'in ana medya yönetim paneli. Port: 81. Stack: PHP 8.4 + Vanilla JS.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 1 | Home Page | Ana sayfa (hoş geldin, öneriler, son dinlenenler) | Hero, Widget Grid, Now Playing |
| 2 | Library | Müzik kütüphanesi (tüm şarkılar, filtreleme) | Track List, Filters, Sort |
| 3 | Search | Gelişmiş arama (sanatçı, albüm, şarkı,歌词) | Search Bar, Results, Suggestions |
| 4 | Player | Tam ekran oynatıcı (kontrol,歌词,liste) | Controls, Progress, Lyrics, Queue |
| 5 | Playlist | Çalma listesi yönetimi (oluştur, düzenle, paylaş) | List, Drag-Drop, Collaborative |
| 6 | Artist | Sanatçı sayfası (biyografi, discography) | Bio, Albums, Top Tracks |
| 7 | Album | Albüm sayfası (kapak, şarkı listesi, bilgi) | Cover, Track List, Credits |
| 8 | Settings | Kullanıcı ayarları (tema, dil, gizlilik) | Forms, Toggles, Preferences |

### 2.1 Music Panel Sayfa Akışı

```
Home Page → Library → Search → Artist → Album → Player
                  ↕                ↕          ↕
              Playlist          Artist      Playlist
```

---

## 3. admin.coremusic.net — Yönetim Paneli (4 Bileşen)

Sistem yönetimi ve içerik kontrolü. Port: 80. Stack: PHP 8.4.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 9 | Admin Dashboard | Yönetim ana sayfası (istatistikler, uyarılar) | Stats Cards, Charts, Alerts |
| 10 | User Management | Kullanıcı yönetimi (CRUD, roller, engelleme) | User List, Edit Form, Role Assign |
| 11 | Content Management | İçerik yönetimi (şarkılar, albümler, sanatçılar) | Content List, Upload, Edit |
| 12 | System Settings | Sistem ayarları (genel, e-posta, güvenlik) | Config Forms, Feature Toggles |

---

## 4. home.coremusic.net — Ev Medya Merkezi (3 Bileşen)

RPi5 optimize ev medya paneli. Port: 81. Stack: Vanilla JS + PHP 8.4.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 13 | Home Dashboard | Ev medya ana sayfası (hızlı erişim, öneriler) | Quick Access, Recommendations |
| 14 | Player | Ev oynatıcı (tema bazlı, large UI) | Large Controls, Theme Integration |
| 15 | Settings | Ev ayarları (cihaz, tema, multi-room) | Device Settings, Room Config |

### 4.1 Home Panel Cihaz Desteği

| Cihaz | Viewport | Layout | Mockup |
|-------|----------|--------|--------|
| RPi5 7" | 1024×600 | Embedded (42/58 split) | home-1024 |
| Desktop | 1920×1080 | Wide (3-sütun) | home-1920 |
| Phone | ≤767px | Phone (tek sütun) | — |
| 4K TV | ≥3840px | 4K (ölçekli) | — |

---

## 5. car.coremusic.net — Araç İçi Panel (2 Bileşen)

Araç içi bilgi-eğlence sistemi. Stack: Vanilla JS.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 16 | Car Dashboard | Araç ana sayfası (hızlı erişim, navigasyon) | Touch Cards, Quick Play |
| 17 | Car Player | Araç oynatıcı (büyük buton, sesli komut) | Large Controls, Voice Input |

### 5.1 Car Panel Özellikleri

| Özellik | Değer |
|---------|-------|
| Touch Target | Min 48×48px (WCAG 2.2 AA) |
| Font Size | Min 16px (okunabilirlik) |
| Distracted Driving | Minimal animasyon, sesli geri bildirim |
| Background | Koyu tema (gece sürüşü) |

---

## 6. studio.coremusic.net — Stüdyo Paneli (3 Bileşen)

Profesyonel stüdyo ses yönetimi. Stack: Vanilla JS.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 18 | Studio Mixer | Stüdyo mikseri (8 kanal, fader, pan) | Channel Strips, Faders, Meters |
| 19 | Studio EQ | Stüdyo equalizer (31-band, parametrik) | EQ Graph, Band Controls |
| 20 | Studio Monitor | Stüdyo monitoring (source select, volume) | Source Matrix, Level Meters |

---

## 7. download.coremusic.net — İndirme Paneli (3 Bileşen)

Müzik indirme yönetimi. Port: 3001. Stack: Node.js + TypeScript.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 21 | Download Queue | İndirme kuyruğu (bekleyen, devam eden, tamamlanan) | Queue List, Progress Bars |
| 22 | Download History | İndirme geçmişi (tüm indirmeler, filtreleme) | History List, Search, Filters |
| 23 | Download Settings | İndirme ayarları (kalite, klasör, anti-ban) | Quality Select, Path Config |

---

## 8. coremusic.net — Landing Page (3 Bileşen)

Tanıtım ve pazarlama sayfası. Stack: Vanilla JS.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 24 | Landing Hero | Ana tanıtım (video, CTA, özellikler) | Hero Section, Video, CTA |
| 25 | Landing Features | Özellikler sayfası (detaylı açıklama) | Feature Cards, Comparison |
| 26 | Landing Pricing | Fiyatlandırma sayfası (planlar, karşılaştırma) | Pricing Table, FAQ |

---

## 9. pro.coremusic.net — Profesyonel Panel (2 Bileşen)

Profesyonel kullanıcılar için analitik ve raporlama. Stack: Vanilla JS.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 27 | Pro Dashboard | Profesyonel ana sayfa (dinleme istatistikleri) | Analytics Charts, Insights |
| 28 | Pro Analytics | Detaylı analitik (trend, karşılaştırma, dışa aktarma) | Reports, Export, Filters |

---

## 10. media.coremusic.net — Medya İşleme (2 Bileşen)

Medya dönüştürme ve işleme servisi. Port: 5000/6000. Stack: PHP + FFmpeg.

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 29 | Media Process | Medya işleme durumu (dönüştürme, metadata) | Progress, Queue, Logs |
| 30 | Media Convert | Format dönüştürme (FLAC→MP3, bitrate seçimi) | Format Select, Quality |

---

## 11. auth.coremusic.net — Kimlik Doğrulama (12 Bileşen)

Merkezi kimlik doğrulama servisi (tek panel, 12 akış sayfası).

| # | Sayfa | Tanım | Bileşenler |
|---|-------|-------|------------|
| 31-42 | Auth Pages | Login, Register, Forgot Password, Reset, Email Verify, OAuth Callback, MFA Setup, MFA Verify, Passkey Register, Passkey Login, Session Manage, Logout | Forms, Validation, Redirect |

### 11.1 Auth Sayfa Akışı

```
Login → Dashboard
Register → Email Verify → Login
Forgot Password → Reset → Login
OAuth → Callback → Dashboard
MFA Setup → MFA Verify → Dashboard
Passkey Register → Passkey Login → Dashboard
```

---

## 12. Panel Teknoloji Matrisi

| Panel | Subdomain | Port | Backend | Frontend | Durum |
|-------|-----------|------|---------|----------|-------|
| Music | music.coremusic.net | 81 | PHP 8.4 | Vanilla JS | ✅ |
| Admin | admin.coremusic.net | 80 | PHP 8.4 | PHP | ✅ |
| Home | home.coremusic.net | 81 | PHP 8.4 | Vanilla JS | ✅ |
| Car | car.coremusic.net | — | PHP 8.4 | Vanilla JS | PLANNED |
| Studio | studio.coremusic.net | 81 | PHP 8.4 | Vanilla JS | PLANNED |
| Download | download.coremusic.net | 3001 | Node.js | Vanilla JS | PLANNED |
| Landing | coremusic.net | 80 | Static | Vanilla JS | PLANNED |
| Pro | pro.coremusic.net | 81 | PHP 8.4 | Vanilla JS | PLANNED |
| Media | media.coremusic.net | 5000/6000 | PHP + FFmpeg | Vanilla JS | PLANNED |
| Auth | auth.coremusic.net | — | PHP 8.4 | PHP | ✅ |

---

## 13. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| K10 Application | K8 Services | Backend servisleri |
| K10 Application | K9 API Routing | SPA routing, API calls |
| K10 Application | K11 UX Layer | UI bileşenleri, CSS |
| K10 → ADR-044 | Theme Engine | Dinamik tema |
| K10 → ADR-045 | View Mode | Multi-domain view |
| K10 → Leafplayer | GitHub | Açık kaynak müzik player |
| K10 → Musable | GitHub | Açık kaynak medya |

---

## 14. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | draft |
| Total Components | 45 |
| Panels | 10 |
| Music Pages | 8 |
| Admin Pages | 4 |
| Home Pages | 3 |
| Car Pages | 2 |
| Studio Pages | 3 |
| Download Pages | 3 |
| Landing Pages | 3 |
| Pro Pages | 2 |
| Media Pages | 2 |
| Auth Pages | 12+ |
| ADR Coverage | 3 ADR referansı |
| GitHub References | Leafplayer, Musable |

---

## 15. Class AB Uygulama Entegrasyonu

Tum paneller Class AB amplifikator kontrolune sahiptir:
- [[electronics/amplifier-classab-circuit]] — Amplifikator kontrol paneli
- [[electronics/power-supply-classab]] — Guç durumu gostergesi
- [[electronics/thermal-design-classab]] — Sicaklik gostergesi
- [[electronics/bom-classab]] — BOM yonetimi

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
