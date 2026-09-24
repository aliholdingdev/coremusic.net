---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K10 Uygulama Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
source: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K10: Uygulama Layer

**Katman:** K10 (Uygulama)
**Kapsam:** 10 Panel: Music, Home, Car, Studio, Admin, Download, Landing, Pro, Media, Auth
**Sorumlu Agent:** UI Designer
**Bileşen Sayısı:** 45

---

## 1. Genel Bakış

K10 katmanı, CoreMusic'in 10 web panelini içerir. Her panel bağımsız bir subdomain üzerinde çalışır.

---

## 2. 10 Panel Haritası

| # | Panel | Subdomain | Port | Stack | Durum |
|---|-------|-----------|------|-------|-------|
| 1 | Landing | coremusic.net | 80 | Vanilla JS | ✅ |
| 2 | Music | music.coremusic.net | 81 | PHP 8.4 + JS | ✅ |
| 3 | Admin | admin.coremusic.net | 80 | PHP 8.4 | ✅ |
| 4 | Download | download.coremusic.net | 3001 | Node.js + TS | ✅ |
| 5 | Media | media.coremusic.net | 5000/6000 | PHP + FFmpeg | ✅ |
| 6 | Auth | auth.coremusic.net | — | PHP 8.4 | ✅ |
| 7 | Home | home.coremusic.net | 81 | Vanilla JS | ✅ |
| 8 | Car | car.coremusic.net | — | Vanilla JS | ✅ |
| 9 | Studio | studio.coremusic.net | 81 | Vanilla JS | ✅ |
| 10 | Pro | pro.coremusic.net | 81 | Vanilla JS | ✅ |

---

## 3. Panel Detayları

### 3.1 Music Panel (music.coremusic.net:81)

```
Ana medya paneli. Kütüphane, albüm, sanatçı yönetimi.
  - Library view
  - Album browser
  - Artist browser
  - Playlist management
  - Search
  - Now playing
  - Footer player
```

### 3.2 Home Panel (home.coremusic.net:81)

```
Ev medya merkezi. RPi5 optimized.
  - Dashboard widgets
  - Recent tracks
  - Recommendations
  - Radio
  - Podcast
  - Multi-room control
```

### 3.3 Car Panel (car.coremusic.net)

```
Araç içi bilgi-eğlence. Touch-optimized.
  - Large touch targets
  - Minimal UI
  - Voice control
  - Navigation
  - Hands-free
```

### 3.4 Studio Panel (studio.coremusic.net:81)

```
Profesyonel stüdyo.
  - Multi-track view
  - Mixer
  - EQ visualization
  - Metering
  - Reference tracks
```

### 3.5 Admin Panel (admin.coremusic.net:80)

```
Yönetim paneli.
  - User management
  - System settings
  - Analytics
  - Logs
  - Backup management
```

### 3.6 Download Panel (download.coremusic.net:3001)

```
İndirme yönetimi.
  - Download queue
  - Source selection (Deezer/YouTube)
  - Quality settings
  - Cache management
```

---

## 4. 4-Tier Device Manager

| Tier | Cihazlar | Viewport | Layout |
|------|----------|----------|--------|
| Tier 1: Phone | PHONE | ≤767px | Tek sütun |
| Tier 2: Embedded | EMBEDDED, TABLET | ≤1024px | 42/58 split |
| Tier 3: Wide | LAPTOP, DESKTOP | 1025-2560px | 3-sütun |
| Tier 4: 4K | FOUR_K_TV, FOUR_K_MON | ≥2561px | 4K ölçekli |

---

## 5. SPA → ApiClient Kuralı

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure

SPA asla PDO, MySQL, Repository, Entity, Infrastructure, Filesystem,
FFmpeg, Redis, Cache veya SQL GÖRMEZ.
```

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-039 | 7-servis platform mimarisi |
| ADR-045 | Multi-domain view mode |
| ADR-046 | Cross-view state koruma |

---

*K10 Uygulama Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Alt Katman Şeması (K10.a.b.c)

> **Revizyon (2026-09-24):** Bu bölüm 3 turlu agent tartışması sonucu eklenmiştir. §1–§6 (mevcut içerik) silinmemiştir. Adlandırma: adlandirma-kurali.md (K{n} → K{n}.a → K{n}.a.b). Düzey-2 sırası plan §2.1 L10.1–L10.10 ile birebir aynıdır (CLAUDE §9 ile de uyumlu).

### 1. Kaynak Tablosu

| # | Kaynak | Kullanım |
|---|--------|----------|
| 1 | .ai/CLAUDE.md §5 (K10 satırı: 10 Panel music, admin, home, car, studio, download, landing, pro, media, auth · 45 bileşen · sınır: yalnızca K9 API) | Düzey-2 kapsamı + iletişim sınırı (düzenleyici kaynak) |
| 2 | .ai/CLAUDE.md §9 (Service Map — 10 Panels + Faz 1 gerçeklik notu + ADR-043 referansı) | Panel subdomain/port/stack/durum tablosu + fiziksel gerçeklik (auth ✅, home ✅) |
| 3 | .ai/CLAUDE.md §5 (K11, K12, K15 satırları) | Sınırlar: K10 → K11 tetikleme · K12 K10'u okumaz · FFmpeg K15 |
| 4 | .ai/architecture/frontend-restructuring-plan.md §2.1 L10.1–L10.10 (10 satır) | Düzey-2 sıra kanıtı |
| 5 | k10-uygulama/README.md §2–§6 | 10 panel haritası, 6 panel detay bloğu, 4-Tier Device Manager, SPA→ApiClient kuralı, ADR-039/045/046 |
| 6 | k10-uygulama/index.md (Uygulama Haritası 14 madde · Routing 16 route · State Slices · Bileşen Hiyerarşisi · performans · güvenlik · bağımlılıklar · Durum 🟡) | K10.7–K10.9 children + kök kanıtlar + çelişki C1–C5 |
| 7 | .ai/AGENTS.md §25.2 (UI/Embedded/... PLANNED — spec mevcut) | Durum çelişkisinin çözümü (C5) |
| 8 | k10-uygulama/*.md (disk glob: 17 dosya) | 9 panel MD + 5 cross-cutting MD + README + index + CLAUDE |

### 2. Şema Kuralları

| Kural | Uygulama |
|-------|----------|
| Kök | K10 (plan §2.1 L10 kökü) |
| Düzey-2 | K10.a — küçük harf-tire; sıra: L10.1–L10.10 (music → auth) |
| Düzey-3 | K10.a.b — yalnızca kanıt varsa (README detay bloğu / index harita satırı) |
| Düzey-4 | K10.a.b.c — bu katmanda YOK (tek gerçek L4 = K11.1.4.13) |
| .0. yasak | Hiçbir düğümde kullanılmadı |
| K numarası | Belgede her düğüm K10 önekiyle anıldı |

### 3. Düzey-2 Tablosu (K10.a — 10 düğüm)

| # | Düğüm | Panel / subdomain | plan §2.1 | Disk MD |
|---|-------|-------------------|:---------:|---------|
| K10.1 | music | music.coremusic.net:81 | L10.1 | music.md |
| K10.2 | admin | admin.coremusic.net:80 | L10.2 | admin.md |
| K10.3 | home | home.coremusic.net:81 | L10.3 | home.md |
| K10.4 | car | car.coremusic.net | L10.4 | car.md |
| K10.5 | studio | studio.coremusic.net:81 | L10.5 | studio.md |
| K10.6 | download | download.coremusic.net:3001 | L10.6 | download.md |
| K10.7 | landing | coremusic.net:80 | L10.7 | landing.md |
| K10.8 | pro | pro.coremusic.net:81 | L10.8 | pro.md |
| K10.9 | media | media.coremusic.net:5000/6000 | L10.9 | media.md |
| K10.10 | auth | auth.coremusic.net | L10.10 | YOK (auth-panel.md yok — dürüstlük kaydı) |

### 4. Düzey-3 Düğümleri (K10.a.b — 38 düğüm)

#### K10.1 music — 7 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.1.1 library-view | Kütüphane görünümü | README §3.1 |
| K10.1.2 album-browser | Albüm gezgini | README §3.1 |
| K10.1.3 artist-browser | Sanatçı gezgini | README §3.1 |
| K10.1.4 playlist-management | Playlist yönetimi | README §3.1 |
| K10.1.5 search | Panel içi arama | README §3.1 |
| K10.1.6 now-playing | Çalan parça görünümü | README §3.1 |
| K10.1.7 footer-player | Alt bar oynatıcı | README §3.1 |

#### K10.2 admin — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.2.1 user-management | Kullanıcı yönetimi | README §3.5 · index routing /admin/users |
| K10.2.2 system-settings | Sistem ayarları | README §3.5 |
| K10.2.3 analytics | Analitik | README §3.5 |
| K10.2.4 logs | Log görüntüleme | README §3.5 |
| K10.2.5 backup-management | Yedek yönetimi | README §3.5 |

#### K10.3 home — 6 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.3.1 dashboard-widgets | Gösterge paneli widget'ları | README §3.2 |
| K10.3.2 recent-tracks | Son parçalar | README §3.2 |
| K10.3.3 recommendations | Öneriler (K8/K4 bağımlılığı — C2) | README §3.2 · index Bağımlılıklar K4 |
| K10.3.4 radio | Radyo | README §3.2 |
| K10.3.5 podcast | Podcast | README §3.2 |
| K10.3.6 multi-room-control | Çoklu oda kontrolü | README §3.2 · index Harita 02 |

#### K10.4 car — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.4.1 large-touch-targets | Büyük dokunma hedefleri | README §3.3 |
| K10.4.2 minimal-ui | Minimal arayüz | README §3.3 |
| K10.4.3 voice-control | Sesli kontrol | README §3.3 |
| K10.4.4 navigation | Navigasyon entegrasyonu | README §3.3 · index Harita 03 |
| K10.4.5 hands-free | Serbest ellerle kullanım | README §3.3 |

#### K10.5 studio — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.5.1 multi-track-view | Çoklu track görünümü | README §3.4 |
| K10.5.2 mixer | Mixer görünümü | README §3.4 |
| K10.5.3 eq-visualization | EQ görselleştirme | README §3.4 |
| K10.5.4 metering | Seviye ölçümü | README §3.4 |
| K10.5.5 reference-tracks | Referans parçalar | README §3.4 |

#### K10.6 download — 4 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.6.1 download-queue | İndirme kuyruğu | README §3.6 · index Harita 06 |
| K10.6.2 source-selection | Deezer/YouTube kaynak seçimi | README §3.6 |
| K10.6.3 quality-settings | Kalite ayarları | README §3.6 |
| K10.6.4 cache-management | Cache yönetimi | README §3.6 |

#### K10.7 landing — 2 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.7.1 product-showcase | Ürün vitrini | index Uygulama Haritası 07 |
| K10.7.2 marketing | Pazarlama içeriği | index Uygulama Haritası 07 |

#### K10.8 pro — 2 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.8.1 advanced-features | Gelişmiş özellikler | index Uygulama Haritası 08 |
| K10.8.2 power-user | Power kullanıcı modu | index Uygulama Haritası 08 |

#### K10.9 media — 2 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K10.9.1 video-player | Video oynatıcı | index Uygulama Haritası 09 |
| K10.9.2 medya-oynatici | Medya oynatıcı görünümü | index Uygulama Haritası 09 |

#### K10.10 auth — 0 çocuk (dürüstlük)

Kanıt yok: auth-panel.md diskte YOK, README §3'te detay bloğu YOK, index Haritası'nda satır YOK. Düzey-3 uydurulmadı (Guardrail #3). Düzey-2 kanıtı: plan L10.10 + CLAUDE §9 satır 6 + CLAUDE §9 gerçeklik notu (auth fiziksel mevcut ✅) + ADR-043 (auth-subdomain-consolidation).

### 5. Çelişki Kayıt Defteri

| # | Çelişki | Kazanan | Gerekçe |
|---|---------|---------|---------|
| C1 | index: React 19 + Next.js 15 + TypeScript + Tailwind + Zustand/Redux + React.lazy/next/image ↔ CLAUDE §9 stack (Vanilla JS, PHP 8.4 + JS, Node + TS) + README §2 | CLAUDE §9 | SSOT §2.1; katman geneli React/Next/Tailwind kanıtlanmadı, reddedildi (AGENTS §25.2: UI = PLANNED, Vanilla JS) |
| C2 | index: "Her panel K8 ile HTTP/WS iletişim kurar" + Bağımlılıklar K8/K5/K4/K7/K0 ↔ CLAUDE §5 K10 sınırı: yalnızca K9 API | CLAUDE §5 | Panel → K9 → (middleware) → K8 zinciri; K8'e doğrudan erişim ihlaldir |
| C3 | index routing: /downloads (çoğul), /settings/theme, /settings/accessibility, auth route YOK ↔ K9 README §5.1: /download, /auth/login (14 route) | K9 README §5.1 | Routing sahibi K9.4 spa-router (ADR-083); index route satırları K9.4'e taşınmalı |
| C4 | index Bileşen Hiyerarşisi Panel Router = 8 panel (landing, auth eksik) ↔ CLAUDE §5/§9 + plan = 10 panel | 10 panel | Çoğunluk + düzenleyici kaynak |
| C5 | Durum sütunu ✅ (README §2 + CLAUDE §9) ↔ CLAUDE §9 gerçeklik notu: fiziksel olarak yalnız auth ✅ + home ✅, diğerleri kod ağacında YOK ↔ index 🟡 Planlama ↔ AGENTS §25.2 UI = PLANNED | Gerçeklik notu + AGENTS §25.2 | ✅ = hedef sütunu; uygulama durumu PLANNED (2/10 fiziksel mevcut) |

### 6. Matrisler

**4-Tier Device Manager (README §4) — panel bağımsız katman kökü:**

| Tier | Cihazlar | Viewport | Layout |
|------|----------|----------|--------|
| 1 Phone | PHONE | ≤767px | Tek sütun |
| 2 Embedded | EMBEDDED, TABLET | ≤1024px | 42/58 split |
| 3 Wide | LAPTOP, DESKTOP | 1025–2560px | 3-sütun |
| 4 4K | FOUR_K_TV, FOUR_K_MON | ≥2561px | 4K ölçekli |

**State Slice matrisi (index §State Management — React varsayımı reddedildi, parçalanma kavramı korunur):** MusicSlice (şarkı listesi, current track, playback) · HomeSlice (oda durumları, cihaz listesi, senaryolar) · UserSlice (kimlik, tercih, roller) · NotificationSlice (bildirim listesi, badge).

**Güvenlik kalemleri (index §Güvenlik Katmanı):** DOMPurify XSS sanitization · Token-based CSRF (K7 ile uyumlu) · Strict CSP (K7/K6 ile uyumlu) · JWT + refresh token rotasyonu · RBAC panel erişimi.

**İlgili ADR'ler (README §6 + CLAUDE §9 çapraz referans):** ADR-039 7-servis platform (→ panel-servis zemini) · ADR-045 multi-domain view mode (→ Home, Pro, Studio görünüm modları) · ADR-046 cross-view state koruma · ADR-043 auth-subdomain-consolidation (→ K10.10).

**Bağımlılıklar (index §Bağımlılıklar — C2 düzeltmesiyle okunur):** K9 API Gateway (doğru kanal) · K8 Service Layer (yalnız K9 üzerinden) · K5 veri · K4 ML/öneri · K7 middleware (auth/rate-limit/session — K9 zinciri içinde) · K0 dosya sistemi/process.

### 7. Katman Sınırları (K9 ↔ K10 ↔ K11 · SPA yasağı)

| Sınır | Kural | Kanıt |
|-------|-------|-------|
| K10 → K9 | K10 yalnızca K9 API üzerinden iletişim kurabilir | CLAUDE §5 K10 sınırı |
| K10 → K11 | K10 K11'i tetikleyebilir (K11 = yalnız K10 tarafından tetiklenebilir) | CLAUDE §5 K11 sınırı |
| K10 → K8/K5/K4/K7 | Doğrudan erişim YASAK (C2); tüm çağrılar K9 üzerinden | CLAUDE §5 K10 · index reddedildi |
| SPA → ApiClient | SPA asla PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis, Cache, SQL GÖRMEZ | README §5 |
| FFmpeg | Panel FFmpeg'e dokunmaz; FFmpeg sahibi K15 (CLAUDE §5 K15) | CLAUDE §5 · README §5 yasağı |
| K12 → K10 | Yok: K12 yalnızca K8 servislerinden okuma yapar | CLAUDE §5 K12 |

### 8. Kök Kanıtlar (tek panellik değil — düzey-2 yapılamaz)

| Kanıt (disk/belge) | Neden kök? |
|--------------------|------------|
| theme-customization.md (index Harita 13: Dark/Light, Custom Themes) | Tüm panelleri kapsar |
| pwa-features.md (index Harita 11: Offline Support, Service Worker) | Tüm panelleri kapsar |
| notification-panel.md (index Harita 14: In-App Alerts, Badges) | Panel bağımsız overlay |
| mobile-responsive.md (index Harita 10: Responsive Design, Touch Gestures) | Tüm panelleri kapsar |
| accessibility-panel.md (index Harita 12: WCAG 2.2 AA, Screen Reader) | Tüm panelleri kapsar |
| README §4 4-Tier Device Manager | Katman geneli viewport politikası |
| README §5 SPA → ApiClient zinciri | Katman geneli veri erişim yasağı |
| index §Bileşen Hiyerarşisi (App Shell, Sidebar, Top Bar, Panel Router, Footer, Global Overlays) | Panel dışı iskelet |

### 9. Düzey-4 Durumu

**Bu katmanda düzey-4 düğüm YOKTUR (0 adet).** Doğrulama: şemada benimsenen tek gerçek düzey-4 düğüm K11.1.4.13'tür (plan §2.2, c-player.css). K10 için düzey-4 talebine ek kanıt zorunludur; uydurma düğüm eklenmez (Guardrail #3).

### 10. Sayım Özeti (K10)

| Seviye | Onaylı hedef (X/Y/Z = T) | Gerçek (2026-09-24 sayımı) | Durum |
|--------|:-------------------------:|:---------------------------:|-------|
| Düzey-2 (K10.a) | 10 | 10 (L10.1–L10.10 sırasıyla) | ✅ |
| Düzey-3 (K10.a.b) | 6 * | 38 (auth: 0 — kanıt yok) | * tanım belirsiz — gerçek şemanın kendisinden sayıldı |
| Düzey-4 (K10.a.b.c) | 200 * | 0 | * tanım belirsiz — kanıt yok, uydurulmadı |
| Toplam T | 260 (X·Y+Z=10·6+200) | — | * bkz. not |

> **Not (Truth Mode):** Y ve Z'nin tanımı paylaşılmadı; X·Y+Z denklemi K10'da tutuyor (10·6+200=260). "Her dosya ≥500 satır" ile "7 dosya toplamı 1.235" birlikte sağlanamaz (7×500=3.500). Öncelik: (1) gerçek kanıt, (2) düzey-2 = onaylı X, (3) dosya başı ≥500 satır. Sapmalar raporlanmıştır.

### 11. Düzey-2 → Kaynak Çapraz Referans Matrisi

| Düğüm | plan §2.1 | CLAUDE §9 satır | Disk MD | README §3 detay |
|-------|:---------:|:---------------:|---------|:---------------:|
| K10.1 music | L10.1 | 2 ✔ | music.md | ✔ §3.1 |
| K10.2 admin | L10.2 | 3 ✔ | admin.md | ✔ §3.5 |
| K10.3 home | L10.3 | 7 ✔ | home.md | ✔ §3.2 |
| K10.4 car | L10.4 | 8 ✔ | car.md | ✔ §3.3 |
| K10.5 studio | L10.5 | 9 ✔ | studio.md | ✔ §3.4 |
| K10.6 download | L10.6 | 4 ✔ | download.md | ✔ §3.6 |
| K10.7 landing | L10.7 | 1 ✔ | landing.md | YOK |
| K10.8 pro | L10.8 | 10 ✔ | pro.md | YOK |
| K10.9 media | L10.9 | 5 ✔ | media.md | YOK |
| K10.10 auth | L10.10 | 6 ✔ (+ ADR-043) | YOK | YOK |

**Doğrulama:** 10/10 düğüm plan L10.x + CLAUDE §9 satırıyla eşleşiyor (sıra farklıdır: plan/CLAUDE §5 sırası esas alındı). 9/10 düğümün disk MD'si var; K10.10 auth-md YOK (kanıt: plan + CLAUDE §9 + ADR-043). 6/10 düğümün README §3 detay bloğu var; 4'ü children'ını index haritasından aldı.

### 12. Şema Kuralı Uyum Matrisi (adlandirma-kurali.md #1–#5)

| Kural | K10 Uygulaması | Durum |
|-------|----------------|-------|
| #1 Kök K{n} | K10 (plan §2.1 L10 kökü) | ✅ |
| #2 Düzey-2 K{n}.a | K10.1–K10.10 (küçük harf-tire) | ✅ |
| #3 Düzey-3 K{n}.a.b | K10.a.b — 38 düğüm (K10.10: 0) | ✅ |
| #4 Düzey-4 K{n}.a.b.c | K10'da 0 (yalnız gerçek L4 = K11.1.4.13) | ✅ |
| #5 ".0." yasak | Hiçbir düğümde ".0." yok | ✅ |

### 13. Kapsam Dışı ve Bilinen Boşluklar

| # | Boşluk | Durum | Sonraki Eylem |
|---|--------|-------|---------------|
| 1 | auth-panel.md diskte yok | Açık — K10.10 children'sız bırakıldı | Dosya eklenirse §4 K10.10 alt bölümü doldurulur |
| 2 | React/Next/TS/Tailwind/Zustand stack (index) | Reddedildi (C1) | index.md revizyonu: CLAUDE §9 + AGENTS §25.2 stack'ine geç |
| 3 | Panel → K8 doğrudan HTTP/WS (index) | Reddedildi (C2) | index.md revizyonu: kanal = K9 API |
| 4 | Panel Router 8 panel (index) | Reddedildi (C4) | index.md hiyerarşiye landing + auth eklenmeli |
| 5 | CLAUDE §9 Media satırı "PHP + FFmpeg" ↔ §5 K15 FFmpeg sahipliği | Açık — CLAUDE içi çapraz not | §9 satırı panel backend ayağı olarak notlanmalı; işlevsel sahiplik K15 |

### 14. Revizyon ve Denetim Notu

| Öğe | Değer |
|-----|-------|
| Bu revizyon | v1.1.0 — 2026-09-24 (frontmatter updated + source; footer 2026-09-24) |
| Önceki durum | v1.0.0 — 157 satır, §1–§6 (içerik korundu, silme yok) |
| Eklenen H2 | ## Alt Katman Şeması (K10.a.b.c) · ## Kanıt Kataloğu |
| Denetim izi | CLAUDE.md §5 (K10/K11/K12/K15) · CLAUDE.md §9 (10 panel + reality) · plan §2.1 L10.1–L10.10 · k10-uygulama/ (17 dosya) · AGENTS.md §25.2 |
| Sonraki tetik | Panel ekleme/çıkarma veya durum değişimi (PLANNED → IMPLEMENTED) §3/§5/§10/§11 + Kanıt Kataloğu ile yeniden sayılır |

### 15. index Routing → Panel Eşlemesi (index §Routing Stratejisi · sahibi K9.4)

| Route | Panel | Düğüm |
|-------|-------|-------|
| / | Landing Page | K10.7 |
| /music | Music Panel | K10.1 |
| /music/playlist/:id | Playlist Detail | K10.1.4 |
| /music/equalizer | Equalizer | K10.1 (K11.1.4 ile ilişkili — c-player) |
| /home | Home Panel | K10.3 |
| /home/room/:id | Room Control | K10.3.6 |
| /car | Car Panel | K10.4 |
| /studio | Studio Panel | K10.5 |
| /studio/recording | Recording View | K10.5 (index Harita 04: Recording) |
| /admin | Admin Panel | K10.2 |
| /admin/users | User Management | K10.2.1 |
| /downloads | Download Panel | K10.6 (C3: K9.4 canonical = /download) |
| /pro | Pro Panel | K10.8 |
| /media | Media Panel | K10.9 |
| /settings/theme | Theme Customization | kök: theme-customization.md |
| /settings/accessibility | Accessibility Settings | kök: accessibility-panel.md |

**Not:** /auth/login route'u index'te YOKTUR — K9 README §5.1'dedir (C3). Routing kararı K10'da değil, K9.4 spa-router'dadır (ADR-083).

### 16. Çelişki Eylem Listesi (index.md + README §2 Revizyon Kuyruğu)

| # | Eylem | Hedef dosya | Öncelik |
|---|-------|-------------|:-------:|
| 1 | Stack satırını CLAUDE §9'a göre düzelt (React/Next/Tailwind/Zustand kaldır) | k10-uygulama/index.md (Genel Bakış + Durum) | P0 (C1) |
| 2 | Panel iletişim kanalını K9 API yap (K8 direkt çağrı kaldır) | index.md (API Entegrasyonu + Bağımlılıklar) | P0 (C2) |
| 3 | Panel Router listesine landing + auth ekle (8 → 10) | index.md (Bileşen Hiyerarşisi) | P1 (C4) |
| 4 | Route'ları K9.4 tablosuyla hizala (/downloads → /download, /auth/login ekle) | index.md (Routing Stratejisi) | P1 (C3) |
| 5 | Durum sütununa PLANNED etiketini yaz (AGENTS §25.2) | README §2 + bu belge §11 | P2 (C5) |

### 17. Fiziksel Envanter Notu (CLAUDE §9 Faz 1 Gerçeklik Notu)

| Panel | Kod ağacında dizin | Not |
|-------|:------------------:|-----|
| auth | ✅ mevcut | Faz 0 Test-Path ile doğrulandı (CLAUDE §9) |
| home | ✅ mevcut | + assets statik servisi (CLAUDE §9) |
| music, admin, download, media, car, studio, pro, landing | YOK | Dizin kod ağacında yok — hedef mimari (CLAUDE §9 reality note) |
| Durum sütunu anlamı | ✅ = hedef | Uygulama etiketi: PLANNED (AGENTS §25.2) — C5 |

**Sonuç:** K10 şeması hedef mimariyi (10 panel) anlatır; 2/10 panel fiziksel mevcuttur. Şemaya "mevcut/planlı" ikili etiketi eklenmeden üretim sayımı yapılmaz (Truth Mode).

---

## Kanıt Kataloğu

> Bu katmanın diskteki TÜM .md dosyaları (glob: 17 adet) ve hangi sayımın kanıtını taşıdıkları. Dosya başına 2 satır: açıklama + desteklediği sayaç.

- **README.md** (CoreMusic — K10 Uygulama Layer) — §2 10 panel haritası (CLAUDE §9 kopyası), §3.1–§3.6 altı panel detayı, §4 4-Tier, §5 SPA yasağı, §6 ADR-039/045/046.
  → Destek: K10.1–K10.6 düzey-3 (32), §6/§8 kök kanıtlar (2), C5, tier matrisi.
- **index.md** (K10 Uygulama Katmanı - Genel Bakış) — Uygulama Haritası 14 madde, Routing 16 route, State Slices, Bileşen Hiyerarşisi, performans, güvenlik, bağımlılıklar, Durum 🟡.
  → Destek: K10.7–K10.9 düzey-3 (6), kök kanıtlar §8 (harita 10–14 → 5 cross-cutting MD), C1–C4, §15 eşleme.
- **CLAUDE.md** (CoreMusic — K10 Uygulama CLAUDE.md) — katmana özgü CLAUDE kestirmesi/şablon girişi.
  → Destek: kanıt kaynakları tablosu (kural #6), katalog bütünlüğü (17 dosya sayımı).
- **music.md** (Music Panel) — K10.1'in disk kanıtı.
  → Destek: K10.1, K10.1.1–K10.1.7 (düzey-2/3: 8).
- **admin.md** (Admin Panel) — K10.2'nin disk kanıtı.
  → Destek: K10.2, K10.2.1–K10.2.5 (6).
- **home.md** (Home Panel) — K10.3'ün disk kanıtı (fiziksel mevcut ✅ — CLAUDE §9 reality).
  → Destek: K10.3, K10.3.1–K10.3.6 (7).
- **car.md** (Car Panel) — K10.4'ün disk kanıtı.
  → Destek: K10.4, K10.4.1–K10.4.5 (6).
- **studio.md** (Studio Panel) — K10.5'in disk kanıtı.
  → Destek: K10.5, K10.5.1–K10.5.5 (6).
- **download.md** (Download Panel) — K10.6'nın disk kanıtı.
  → Destek: K10.6, K10.6.1–K10.6.4 (5).
- **landing.md** (Landing Page) — K10.7'nin disk kanıtı.
  → Destek: K10.7, K10.7.1–K10.7.2 (3).
- **pro.md** (Pro Panel) — K10.8'in disk kanıtı.
  → Destek: K10.8, K10.8.1–K10.8.2 (3).
- **media.md** (Media Panel) — K10.9'un disk kanıtı.
  → Destek: K10.9, K10.9.1–K10.9.2 (3).
- **theme-customization.md** (Dark/Light + Custom Themes) — kök kanıt (§8 satır 1).
  → Destek: kök sayımı (5 kökten 1), route /settings/theme — düzey-2 DEĞİL.
- **pwa-features.md** (Offline Support, Service Worker) — kök kanıt (§8 satır 2).
  → Destek: kök sayımı (2/5), index Harita 11 — düzey-2 DEĞİL.
- **notification-panel.md** (In-App Alerts, Badges) — kök kanıt (§8 satır 3).
  → Destek: kök sayımı (3/5), index Harita 14 — düzey-2 DEĞİL.
- **mobile-responsive.md** (Responsive Design, Touch Gestures) — kök kanıt (§8 satır 4).
  → Destek: kök sayımı (4/5), index Harita 10 + README §4 tier — düzey-2 DEĞİL.
- **accessibility-panel.md** (WCAG 2.2 AA, Screen Reader) — kök kanıt (§8 satır 5).
  → Destek: kök sayımı (5/5), route /settings/accessibility — düzey-2 DEĞİL.

**Dürüstlük kaydı:** auth-panel.md diskte YOKTUR — K10.10'ın disk kanıtı yoktur (9/10 panel MD mevcut). K10.10'ın düzey-2 kanıtı plan L10.10 + CLAUDE §9 satır 6 + ADR-043'tür; children uydurulmadı. React/Next/Tailwind/Zustand iddiası bu klasörde yalnız index.md'dedir ve reddedilmiştir (C1).

**Harici kanıt kaynakları (katalog kapsamı):**

| Kaynak | Kullanım |
|--------|----------|
| .ai/CLAUDE.md §5 (K10, K11, K12, K15 satırları) | Kapsam (10 panel, 45 bileşen), sınır (yalnız K9, K11 tetikleme, FFmpeg K15) |
| .ai/CLAUDE.md §9 (Service Map — 10 Panels + reality note) | Subdomain/port/stack/durum + fiziksel gerçeklik (2/10) |
| .ai/architecture/frontend-restructuring-plan.md §2.1 L10.1–L10.10 | Düzey-2 sıra kanıtı (10 satır) |
| .ai/AGENTS.md §25.2 (UI = PLANNED) | Durum çelişkisi çözümü (C5) |
| disk glob (k10-uygulama/*.md) | 17 dosya: 9 panel + 5 kök + 3 bağlam |

*K10 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*
