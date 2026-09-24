---
reference_doc: CoreMusic Architecture Restructuring Plan
title: "CoreMusic — 1000+ Katmanlı Mimari Yeniden Yapılandırma Planı"
type: architecture
category: frontend-architecture
date: 2026-09-23
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — 1000+ Katmanlı Mimari Yeniden Yapılandırma Planı

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]] · [[ui-design/01-mockup-index]]

---

## 0. Proje Bağlamı & Hedef

### 0.1 Mevcut Durum
- **Frontend:** Vanilla JS ES6+ + ITCSS 9-layer + BEM
- **Backend:** PHP 8.4 + DeviceManager + PSR-4 autoloading
- **Design System:** Figma SSOT (19 PNG mockup, C01-C16 component envanteri)
- **Mevcut Dosyalar:** 45+ CSS dosyası, 30+ JS dosyası, 12+ PHP view

### 0.2 Hedef
Figma tasarımından production-ready, pixel-perfect, erişilebilir ve performanslı bir UI sistemi oluşturmak.

### 0.3 Figma Kaynakları
| Kaynak | Node ID | boyut |
|--------|---------|-------|
| Footer 1024 | `1639-9773` | 1025×90 |
| Footer 1920 | `2831-13747` child[1] | 1923×92 |
| Header | PNG reference | 60px height |
| Media Controls | PNG reference | Circular buttons |

---

## 1. Figma→Kod Dönüşüm Stratejisi

### 1.1 Figma MCP Entegrasyonu

Figma MCP API ile tasarım değerleri otomatik olarak çekilebilir:

```
Figma URL → file_key + node_id → MCP Tools → Design Tokens → CSS Variables
```

**Kullanılacak MCP Tool'ları:**
| Tool | Amaç | Kullanım Anı |
|------|------|-------------|
| `get_design_context` | Tam tasarım bağlamı (layout, renk, tipografi) | Component kodlamadan önce |
| `get_variable_defs` | Design token değişkenleri | CSS token oluştururken |
| `get_metadata` | Node yapısı (child ID'ler) | Büyük tasarımları parçalarken |
| `get_screenshot` | Görsel referans | Pixel-perfect doğrulama |
| `get_node_styles` | CSS özelliklerini çek | Inline style→token dönüşümü |

### 1.2 Dönüşüm Pipeline'ı

```
┌─────────────────────────────────────────────────────────────┐
│                  FİGMA→KOD DÖNÜŞÜM PİPELİNE'I               │
├─────────────────────────────────────────────────────────────┤
│  ADIM 1: Figma URL Parse                                   │
│    → file_key: NFpX9bq58oApWJPgBK5Heo                     │
│    → node_id: 1639-9773 (footer)                           │
│                                                             │
│  ADIM 2: Design Context Çekme                              │
│    → get_design_context(fileKey, nodeId)                    │
│    → Layout, renk, tipografi, spacing, effects              │
│                                                             │
│  ADIM 3: Token Haritalama                                  │
│    → Figma token → CSS custom property                      │
│    → #ff4fd8 → var(--cm-primary)                            │
│    → 12px → var(--cm-spacing-3)                             │
│                                                             │
│  ADIM 4: Component Oluşturma                               │
│    → BEM class: .footer-player__btn                         │
│    → CSS: var() token'ları ile                              │
│    → JS: ComponentBase.js extend                            │
│                                                             │
│  ADIM 5: Pixel-Perfect Doğrulama                           │
│    → get_screenshot() ile karşılaştırma                     │
│    → Chrome DevTools ile measure                            │
│                                                             │
│  ADIM 6: Responsive Genişletme                             │
│    → 1024→1920→3840 breakpoint'lerde test                   │
│    → Media query ile token override                         │
└─────────────────────────────────────────────────────────────┘
```

### 1.3 Figma→CSS Token Haritası

Figma'dan çekilen değerler CSS custom property'lerine dönüştürülür:

| Figma Değeri | CSS Token | Kullanım |
|--------------|-----------|----------|
| `#ff4fd8` | `--cm-primary` | Ana vurgu rengi |
| `rgba(255,255,255,0.88)` | `--cm-text-primary` | Ana metin |
| `rgba(255,255,255,0.06)` | `--cm-surface` | Yüzey rengi |
| `Avalon 10px w500` | `--cm-font-body` | Body font |
| `85×85` | `--cm-cover-size` | Albüm kapağı |
| `1025×4.8` | `--cm-progress-h` | İlerleme çubuğu |
| `297×62` | `--cm-controls-size` | Kontrol butonları |
| `186×14` | `--cm-volume-size` | Ses slider'ı |
| `radius: 50px` | `--cm-pill-radius` | Kapsül şekli |
| `blur(20px)` | `--cm-glass-blur` | Cam efekti |

---

## 2. Katmanlı Mimari — 1000+ Katman

### 2.1 Üst Katman Haritası (L0-L20)

```
┌─────────────────────────────────────────────────────────────────┐
│                    COREMUSIC 1000+ KATMANLI MİMARİ              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  L0  İşletim Sistemi Katmanı                                    │
│  ├── L0.1  Windows (WASAPI, ASIO, COM)                         │
│  ├── L0.2  Linux (ALSA, PipeWire, D-Bus)                       │
│  ├── L0.3  macOS (CoreAudio, AVFoundation)                     │
│  ├── L0.4  Raspberry Pi (ARM64, I2S, GPIO)                     │
│  └── L0.5  ReactOS (Experimental)                              │
│                                                                 │
│  L1  Donanım Altyapısı Katmanı                                  │
│  ├── L1.1  XMOS XU316 (USB Audio Class 2.0)                   │
│  ├── L1.2  PCM3168A (6-in/8-out Codec)                         │
│  ├── L1.3  AK4458 (8-kanal High-End DAC)                       │
│  ├── L1.4  Class AB Amplifikatör (MJL21194/93)                │
│  ├── L1.5  ±35V Güç Kaynağı (LM5122 Boost)                    │
│  ├── L1.6  Termal Yönetim (Fischer heatsink)                   │
│  ├── L1.7  PCB Tasarımı (6-layer, ENIG)                        │
│  └── L1.8  BOM & Üretim (1130 bileşen)                         │
│                                                                 │
│  L2  Sürücü Katmanı (K2)                                       │
│  ├── L2.1  ASIO Driver (Steinberg SDK 2.3.4)                   │
│  ├── L2.2  WASAPI Driver (Windows Audio Session)               │
│  ├── L2.3  ALSA Driver (Linux)                                 │
│  ├── L2.4  PipeWire Driver (Linux Modern)                      │
│  ├── L2.5  CoreAudio Driver (macOS)                            │
│  ├── L2.6  I2S Driver (RPi5)                                   │
│  ├── L2.7  USB Audio Driver                                    │
│  ├── L2.8  Bluetooth Driver (A2DP, LDAC, aptX)                 │
│  └── L2.9  DLNA/UPnP Driver                                    │
│                                                                 │
│  L3  Ses İşleme Motoru Katmanı (K3)                            │
│  ├── L3.1  Neva Engine Core                                    │
│  ├── L3.2  DSP Chain (31-band EQ)                              │
│  ├── L3.3  Reverb Engine (4 mod)                               │
│  ├── L3.4  Compressor/Limiter                                  │
│  ├── L3.5  Crossover (Linkwitz-Riley 4.nesil)                 │
│  ├── L3.6  Spatial Audio (Dolby Atmos, DTS:X)                  │
│  ├── L3.7  Ring Buffer (lock-free, zero-alloc)                 │
│  ├── L3.8  Thread Manager (time-critical priority)             │
│  └── L3.9  Buffer Manager (64-byte aligned)                    │
│                                                                 │
│  L4  Yapay Zeka Katmanı (K4)                                   │
│  ├── L4.1  Music Analysis (10 modül)                           │
│  ├── L4.2  Recommendation Engine (5 modül)                     │
│  ├── L4.3  Voice Recognition (5 modül)                         │
│  ├── L4.4  Edge AI (RPi5 optimization)                         │
│  ├── L4.5  ML Infrastructure                                   │
│  └── L4.6  Auto EQ/DSP (AI-powered)                            │
│                                                                 │
│  L5  Veri Yönetimi Katmanı (K5)                                │
│  ├── L5.1  MySQL 9 (18 BCNF DB, 156 tablo)                    │
│  ├── L5.2  Redis Cache                                         │
│  ├── L5.3  APCu Cache                                          │
│  ├── L5.4  SQLite (Offline Queue)                              │
│  └── L5.5  Restic Backup                                       │
│                                                                 │
│  L6  Güvenlik Katmanı (K6)                                     │
│  ├── L6.1  JWT (RS256, lcobucci/jwt)                           │
│  ├── L6.2  RBAC (regular/premium/studio/car/admin/system)      │
│  ├── L6.3  CSRF (csrf_token, hash_equals)                      │
│  ├── L6.4  CSP (nonce-based, strict-dynamic)                   │
│  ├── L6.5  Rate Limiting (APCu, 60 req/60s)                    │
│  ├── L6.6  AES-256-GCM Encryption                              │
│  ├── L6.7  Argon2id Password Hashing                           │
│  ├── L6.8  Credential Vault                                    │
│  └── L6.9  Audit Trail                                         │
│                                                                 │
│  L7  Middleware Katmanı (K7)                                    │
│  ├── L7.1  OriginCheckMiddleware                               │
│  ├── L7.2  CorsMiddleware                                      │
│  ├── L7.3  RateLimiterMiddleware                               │
│  ├── L7.4  SecurityHeadersMiddleware                           │
│  ├── L7.5  SessionManagerMiddleware                            │
│  ├── L7.6  CsrfMiddleware                                      │
│  ├── L7.7  BypassAuthMiddleware                                │
│  ├── L7.8  AuthMiddleware                                      │
│  ├── L7.9  PermissionMiddleware                                │
│  └── L7.10 ValidationMiddleware                                │
│                                                                 │
│  L8  Servis Katmanı (K8)                                       │
│  ├── L8.1  Control Service (PHP 8.4)                           │
│  ├── L8.2  Media Service (PHP + FFmpeg)                         │
│  ├── L8.3  Audio Service (C++20 JUCE)                          │
│  ├── L8.4  Device Service (C++20)                              │
│  ├── L8.5  Network Audio (WebRTC/P2P)                          │
│  ├── L8.6  AI Service (PHP + Python)                           │
│  └── L8.7  Download Service (Node.js + TS)                     │
│                                                                 │
│  L9  API & Routing Katmanı (K9)                                │
│  ├── L9.1  API Gateway (api.coremusic.net)                     │
│  ├── L9.2  SPA BFF                                             │
│  ├── L9.3  Mobile BFF                                          │
│  ├── L9.4  Embedded BFF (RPi5)                                 │
│  ├── L9.5  Desktop BFF                                         │
│  ├── L9.6  Admin BFF                                           │
│  ├── L9.7  Car BFF                                             │
│  ├── L9.8  CQRS (Command/Query Separation)                     │
│  ├── L9.9  Event Bus (PSR-14)                                  │
│  └── L9.10 SPA Router (PHP+JS Hybrid)                          │
│                                                                 │
│  L10 Uygulama Katmanı (K10)                                    │
│  ├── L10.1 Music Panel (music.coremusic.net)                   │
│  ├── L10.2 Admin Panel (admin.coremusic.net)                   │
│  ├── L10.3 Home Panel (home.coremusic.net)                     │
│  ├── L10.4 Car Panel (car.coremusic.net)                       │
│  ├── L10.5 Studio Panel (studio.coremusic.net)                 │
│  ├── L10.6 Download Panel (download.coremusic.net)             │
│  ├── L10.7 Landing Panel (coremusic.net)                       │
│  ├── L10.8 Pro Panel (pro.coremusic.net)                       │
│  ├── L10.9 Media Panel (media.coremusic.net)                   │
│  └── L10.10 Auth Panel (auth.coremusic.net)                    │
│                                                                 │
│  L11 Kullanıcı Deneyimi Katmanı (K11)                          │
│  ├── L11.1  ITCSS 9-Layer CSS Architecture                     │
│  ├── L11.2  BEM Naming Convention                               │
│  ├── L11.3  Design Tokens (3-tier)                             │
│  ├── L11.4  PWA Support                                        │
│  ├── L11.5  WCAG 2.2 AA Accessibility                          │
│  ├── L11.6  Responsive System (4-tier)                         │
│  ├── L11.7  Theme Engine (ADR-044)                             │
│  ├── L11.8  View Mode System (ADR-045)                         │
│  ├── L11.9  Device-Aware Rendering                             │
│  └── L11.10 Component System (PHP + JS)                        │
│                                                                 │
│  L12 İzleme & Log Katmanı (K12)                                │
│  ├── L12.1  Prometheus Metrics                                 │
│  ├── L12.2  Grafana Dashboard                                  │
│  ├── L12.3  Sentry Error Tracking                              │
│  ├── L12.4  Matomo Analytics                                   │
│  └── L12.5  Audit Trail                                        │
│                                                                 │
│  L13 CI/CD & Deploy Katmanı (K13)                              │
│  ├── L13.1  GitHub Actions                                     │
│  ├── L13.2  Playwright E2E                                     │
│  ├── L13.3  Vitest Unit                                        │
│  ├── L13.4  Docker Containers                                  │
│  └── L13.5  Kubernetes Orchestration                           │
│                                                                 │
│  L14 Ağ & İletişim Katmanı (K14)                               │
│  ├── L14.1  HTTP/3                                             │
│  ├── L14.2  WebRTC P2P                                         │
│  ├── L14.3  DLNA/UPnP                                         │
│  ├── L14.4  AirPlay                                            │
│  └── L14.5  mDNS Discovery                                     │
│                                                                 │
│  L15 Medya & Streaming Katmanı (K15)                           │
│  ├── L15.1  FFmpeg Processing                                  │
│  ├── L15.2  FLAC Decoder                                       │
│  ├── L15.3  HLS Streaming                                      │
│  ├── L15.4  DASH Streaming                                     │
│  ├── L15.5  ID3 Metadata Parser                                │
│  └── L15.6  Radio Stream Manager                               │
│                                                                 │
│  L16-L20 Elektronik Katmanları                                 │
│  ├── L16  Class AB Amplifikatör (50W/kanal)                    │
│  ├── L17  ±35V Güç Kaynağı (LM5122)                           │
│  ├── L18  Termal Tasarım (heatsink + fan)                      │
│  ├── L19  PCB Tasarımı (6-layer)                               │
│  └── L20  BOM & Üretim                                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Frontend Alt Katman Detayı (L11)

```
L11 Kullanıcı Deneyimi — Alt Katmanlar (200+ katman)
├── L11.1 CSS Architecture (ITCSS 9-Layer)
│   ├── L11.1.1  01_Abstracts/
│   │   ├── L11.1.1.1  a-breakpoint-tokens.css (4 breakpoint)
│   │   ├── L11.1.1.2  a-colors-token.css (renk paleti)
│   │   ├── L11.1.1.3  a-design-tokens.css (genel token'lar)
│   │   ├── L11.1.1.4  a-fonts-token.css (tipografi)
│   │   ├── L11.1.1.5  a-layout-tokens-1024.css
│   │   ├── L11.1.1.6  a-layout-tokens-1920.css
│   │   ├── L11.1.1.7  a-layout-tokens-3540.css
│   │   ├── L11.1.1.8  a-layout-tokens-3840.css
│   │   ├── L11.1.1.9  a-login-tokens.css
│   │   ├── L11.1.1.10 a-scale-hybrid.css
│   │   ├── L11.1.1.11 a-theme-config.css
│   │   ├── L11.1.1.12 a-widget-grid-tokens.css
│   │   └── L11.1.1.13 a-layout-tokens.css (yeni: konsolide)
│   ├── L11.1.2  02_Base/
│   │   ├── L11.1.2.1  b-base-core.css (reset, base)
│   │   └── L11.1.2.2  l-main-structural.css
│   ├── L11.1.3  03_Layout/
│   │   ├── L11.1.3.1  _header.css
│   │   ├── L11.1.3.2  _footer.css
│   │   └── L11.1.3.3  _sidebar.css
│   ├── L11.1.4  04_Components/ (16 dosya → 40+ dosya)
│   │   ├── L11.1.4.1  c-badge.css
│   │   ├── L11.1.4.2  c-buttons.css
│   │   ├── L11.1.4.3  c-card.css
│   │   ├── L11.1.4.4  c-component.css (base tokens)
│   │   ├── L11.1.4.5  c-footer-seek.css
│   │   ├── L11.1.4.6  c-footer-volume.css
│   │   ├── L11.1.4.7  c-forms.css
│   │   ├── L11.1.4.8  c-modal.css
│   │   ├── L11.1.4.9  c-progress.css
│   │   ├── L11.1.4.10 c-scrollbar-accent.css
│   │   ├── L11.1.4.11 c-toast.css
│   │   ├── L11.1.4.12 c-toggle.css
│   │   ├── L11.1.4.13 c-player.css (YENİ)
│   │   ├── L11.1.4.14 c-toolbar.css (YENİ)
│   │   ├── L11.1.4.15 c-media-controls.css (YENİ)
│   │   ├── L11.1.4.16 c-volume-slider.css (YENİ)
│   │   ├── L11.1.4.17 c-equalizer.css (YENİ)
│   │   ├── L11.1.4.18 c-playlist.css (YENİ)
│   │   ├── L11.1.4.19 c-nav.css (YENİ)
│   │   ├── L11.1.4.20 c-search.css (YENİ)
│   │   ├── L11.1.4.21 c-album-art.css (YENİ)
│   │   ├── L11.1.4.22 c-waveform.css (YENİ)
│   │   ├── L11.1.4.23 c-spectrum.css (YENİ)
│   │   └── L11.1.4.24 c-glassmorphism.css (YENİ)
│   ├── L11.1.5  05_Pages/
│   │   ├── L11.1.5.1  _home-layout.css
│   │   ├── L11.1.5.2  _home-components.css
│   │   └── L11.1.5.3  _player-layout.css (YENİ)
│   ├── L11.1.6  06_Utilities/
│   │   └── L11.1.6.1  u-helpers-utility.css
│   ├── L11.1.7  07_Vendors/
│   │   └── L11.1.7.1  v-bootstrap-lib.css
│   ├── L11.1.8  08_Devices/ (13 dosya)
│   │   ├── L11.1.8.1  d-embedded.css
│   │   ├── L11.1.8.2  d-desktop.css
│   │   ├── L11.1.8.3  d-phone.css
│   │   ├── L11.1.8.4  d-tablet.css
│   │   ├── L11.1.8.5  d-laptop.css
│   │   ├── L11.1.8.6  d-4k-tv.css
│   │   ├── L11.1.8.7  d-4k-monitor.css
│   │   └── ... (6 auth varyantı)
│   └── L11.1.9  09_ViewModes/
│       ├── L11.1.9.1  v-home.css
│       ├── L11.1.9.2  v-pro.css
│       ├── L11.1.9.3  v-studio.css
│       └── L11.1.9.4  v-car.css
│
├── L11.2 JS Component System (ES2022+)
│   ├── L11.2.1  Core/
│   │   ├── L11.2.1.1  CoreMusicApp.js (lifecycle manager)
│   │   ├── L11.2.1.2  EventBus.js (pub/sub)
│   │   ├── L11.2.1.3  Router.js (SPA router)
│   │   ├── L11.2.1.4  ApiClient.js (HTTP client)
│   │   └── L11.2.1.5  StateManager.js (global state)
│   ├── L11.2.2  Base/
│   │   ├── L11.2.2.1  ComponentBase.js (abstract base)
│   │   ├── L11.2.2.2  ComponentRegistry.js (singleton)
│   │   ├── L11.2.2.3  ComponentLoader.js (auto-discovery)
│   │   ├── L11.2.2.4  DataBinder.js (state→DOM)
│   │   └── L11.2.2.5  TemplateEngine.js (safe HTML)
│   ├── L11.2.3  Components/
│   │   ├── L11.2.3.1  TabsComponent.js
│   │   ├── L11.2.3.2  DropdownComponent.js
│   │   ├── L11.2.3.3  AccordionComponent.js
│   │   ├── L11.2.3.4  ToastComponent.js
│   │   ├── L11.2.3.5  InfiniteScroll.js
│   │   ├── L11.2.3.6  PlayerComponent.js (YENİ)
│   │   ├── L11.2.3.7  ToolbarComponent.js (YENİ)
│   │   ├── L11.2.3.8  VolumeSlider.js (YENİ)
│   │   ├── L11.2.3.9  ProgressBar.js (YENİ)
│   │   └── L11.2.3.10 EqualizerComponent.js (YENİ)
│   ├── L11.2.4  Managers/
│   │   ├── L11.2.4.1  DeviceManager.js
│   │   ├── L11.2.4.2  ThemeManager.js
│   │   ├── L11.2.4.3  ViewModeManager.js
│   │   ├── L11.2.4.4  ScaleManager.js
│   │   └── L11.2.4.5  SidebarManager.js
│   └── L11.2.5  Features/
│       ├── L11.2.5.1  PlayerController.js
│       ├── L11.2.5.2  CardManager.js
│       ├── L11.2.5.3  ScrollManager.js
│       ├── L11.2.5.4  TouchManager.js
│       └── L11.2.5.5  WidgetManager.js
│
├── L11.3 PHP Component System
│   ├── L11.3.1  Shared Infrastructure
│   │   ├── L11.3.1.1  ComponentInterface.php
│   │   ├── L11.3.1.2  AbstractComponent.php
│   │   ├── L11.3.1.3  ComponentRegistry.php
│   │   ├── L11.3.1.4  TemplateEngine.php
│   │   ├── L11.3.1.5  DataBinder.php
│   │   └── L11.3.1.6  ComponentRenderer.php
│   ├── L11.3.2  Concrete Components
│   │   ├── L11.3.2.1  ListComponent.php
│   │   ├── L11.3.2.2  FormComponent.php
│   │   ├── L11.3.2.3  ModalComponent.php
│   │   ├── L11.3.2.4  PlayerComponent.php (YENİ)
│   │   └── L11.3.2.5  ToolbarComponent.php (YENİ)
│   └── L11.3.3  View Partials
│       ├── L11.3.3.1  header.php
│       ├── L11.3.3.2  footer.php
│       ├── L11.3.3.3  sidebar.php
│       └── L11.3.3.4  pages/*.php
│
└── L11.4 Device-Aware Rendering
    ├── L11.4.1  4-Tier Layout System
    │   ├── L11.4.1.1  Phone (≤767px)
    │   ├── L11.4.1.2  Embedded (≤1024px)
    │   ├── L11.4.1.3  Wide (1025-2560px)
    │   └── L11.4.1.4  4K (≥2561px)
    ├── L11.4.2  DeviceManager.php (backend)
    ├── L11.4.3  DeviceManager.js (frontend)
    └── L11.4.4  device-loader.js (cookie bridge)
```

---

## 3. Component Detayı — Figma Görsellerinden

### 3.1 Header Toolbar (Image 1 & 2)

Figma görselinden analiz edilen yapı:

```
┌─────────────────────────────────────────────────────────────────┐
│  HEADER TOOLBAR — Figma Analizi                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Yapı: Tek satır, yatay flex layout                             │
│  Yükseklik: 40-50px (tahmini)                                   │
│  Arka plan: Glassmorphism (blur + semi-transparent)             │
│  Border: Alt kenarda ince çizgi (#ffffff30)                     │
│                                                                 │
│  Sol taraf (ikonlar):                                          │
│  ┌──────┬──────┬──────┬──────┬──────┬──────┬──────┬──────┬──────┐│
│  │ ⏮  │ 🔀  │ ⏸  │ 🔁  │ 🎛  │ 📶  │ 📻  │ 🎵  │ ⚙️  ││
│  │ prev │shuffl│ pause│ repea│ equal│ wifi │ radio│ music│ setti││
│  └──────┴──────┴──────┴──────┴──────┴──────┴──────┴──────┴──────┘│
│                                                                 │
│  Sağ taraf (volume):                                           │
│  ┌────────────────────────────────────────────┬──────────┐     │
│  │ 🔊 ████████████████████████████████░░░░░ │ %100     │     │
│  │ vol  ←── volume slider (pink) ──→          │          │     │
│  └────────────────────────────────────────────┴──────────┘     │
│                                                                 │
│  CSS Token'ları:                                               │
│  --toolbar-h: 48px                                             │
│  --toolbar-bg: rgba(255, 255, 255, 0.06)                       │
│  --toolbar-blur: blur(20px)                                     │
│  --toolbar-border: solid 0.1px #ffffff30                        │
│  --toolbar-icon-size: 20px                                      │
│  --toolbar-icon-gap: 8px                                        │
│  --toolbar-accent: #ff4fd8                                      │
│  --toolbar-slider-w: 186px (embedded) / 353px (desktop)        │
│                                                                 │
│  BEM: .header-toolbar, .header-toolbar__icon,                   │
│       .header-toolbar__volume, .header-toolbar__slider          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**HTML Yapısı:**
```html
<header class="header-toolbar" role="banner">
  <div class="header-toolbar__inner">
    <!-- Sol: İkonlar -->
    <nav class="header-toolbar__icons" aria-label="Hızlı erişim">
      <button class="header-toolbar__icon" data-action="prev" aria-label="Önceki">
        <svg><!-- prev icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="shuffle" aria-label="Karıştır">
        <svg><!-- shuffle icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="pause" aria-label="Duraklat">
        <svg><!-- pause icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="repeat" aria-label="Tekrarla">
        <svg><!-- repeat icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="equalizer" aria-label="Ekolayzır">
        <svg><!-- equalizer icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="wifi" aria-label="Wi-Fi">
        <svg><!-- wifi icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="radio" aria-label="Radyo">
        <svg><!-- radio icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="music" aria-label="Müzik">
        <svg><!-- music icon --></svg>
      </button>
      <button class="header-toolbar__icon" data-action="settings" aria-label="Ayarlar">
        <svg><!-- settings icon --></svg>
      </button>
    </nav>

    <!-- Sağ: Volume -->
    <div class="header-toolbar__volume">
      <svg class="header-toolbar__vol-icon"><!-- speaker icon --></svg>
      <div class="header-toolbar__slider-track">
        <input type="range" class="header-toolbar__slider" min="0" max="100" value="100">
        <div class="header-toolbar__slider-fill" style="width: 100%"></div>
      </div>
      <span class="header-toolbar__vol-text">%100</span>
    </div>
  </div>
</header>
```

### 3.2 Media Controls (Image 3)

```
┌─────────────────────────────────────────────────────────────────┐
│  MEDIA CONTROLS — Figma Analizi                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Yapı: Yatay flex, center-aligned                               │
│  Butonlar: Dairesel, semi-transparent background               │
│                                                                 │
│  ┌──────────┬──────────┬──────────┬──────────┐                  │
│  │   ⏮    │    ▶     │    ■     │    ⏭    │                  │
│  │  prev   │  play    │  stop    │  next    │                  │
│  │ ○○○○○○○ │ ○○○○○○○○ │ ○○○○○○○ │ ○○○○○○○ │                  │
│  └──────────┴──────────┴──────────┴──────────┘                  │
│                                                                 │
│  Buton boyutu: ~50-60px çap (tahmini)                           │
│  İkon boyutu: ~24-28px                                          │
│  Arka plan: rgba(255, 255, 255, 0.1)                           │
│  Border: None                                                   │
│  Border-radius: 50% (dairesel)                                  │
│  Hover: rgba(255, 255, 255, 0.2)                               │
│  Transition: 200ms ease                                         │
│                                                                 │
│  Play butonu: Biraz daha büyük (primary action)                 │
│                                                                 │
│  CSS Token'ları:                                               │
│  --mc-btn-size: 56px                                            │
│  --mc-btn-size--play: 64px                                      │
│  --mc-icon-size: 24px                                           │
│  --mc-icon-size--play: 28px                                     │
│  --mc-bg: rgba(255, 255, 255, 0.1)                             │
│  --mc-bg-hover: rgba(255, 255, 255, 0.2)                       │
│  --mc-radius: 50%                                               │
│  --mc-gap: 16px                                                 │
│                                                                 │
│  BEM: .media-controls, .media-controls__btn,                    │
│       .media-controls__btn--play, .media-controls__icon         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**HTML Yapısı:**
```html
<section class="media-controls" aria-label="Oynatma kontrolleri">
  <button class="media-controls__btn" data-action="prev" aria-label="Önceki">
    <svg class="media-controls__icon" width="24" height="24"><!-- prev --></svg>
  </button>
  <button class="media-controls__btn media-controls__btn--play" data-action="play" aria-label="Oynat">
    <svg class="media-controls__icon media-controls__icon--play" width="28" height="28"><!-- play --></svg>
  </button>
  <button class="media-controls__btn" data-action="stop" aria-label="Durdur">
    <svg class="media-controls__icon" width="24" height="24"><!-- stop --></svg>
  </button>
  <button class="media-controls__btn" data-action="next" aria-label="Sonraki">
    <svg class="media-controls__icon" width="24" height="24"><!-- next --></svg>
  </button>
</section>
```

---

## 4. Design Token Sistemi (3-Tier)

### 4.1 Tier 1: Primitive Token'lar (Raw Values)

```css
:root {
  /* ─── Renk Paleti (Figma'dan çekilen) ─── */
  --p-color-pink-50: #fef0f9;
  --p-color-pink-100: #fde1f3;
  --p-color-pink-200: #fcc3e8;
  --p-color-pink-300: #fb94d8;
  --p-color-pink-400: #ff4fd8;  /* Primary accent */
  --p-color-pink-500: #e91d9e;
  --p-color-pink-600: #d4128a;
  --p-color-pink-700: #a80d6c;
  --p-color-pink-800: #7d0a50;
  --p-color-pink-900: #520734;

  --p-color-white: #ffffff;
  --p-color-black: #000000;
  --p-color-gray-50: #f8f9fa;
  --p-color-gray-100: #f1f3f5;
  --p-color-gray-200: #e9ecef;
  --p-color-gray-300: #dee2e6;
  --p-color-gray-400: #ced4da;
  --p-color-gray-500: #adb5bd;
  --p-color-gray-600: #868e96;
  --p-color-gray-700: #495057;
  --p-color-gray-800: #343a40;
  --p-color-gray-900: #212529;

  /* ─── Spacing Scale ─── */
  --p-space-0: 0;
  --p-space-1: 4px;
  --p-space-2: 8px;
  --p-space-3: 12px;
  --p-space-4: 16px;
  --p-space-5: 20px;
  --p-space-6: 24px;
  --p-space-8: 32px;
  --p-space-10: 40px;
  --p-space-12: 48px;
  --p-space-16: 64px;

  /* ─── Font Scale ─── */
  --p-font-size-xs: 0.75rem;    /* 12px */
  --p-font-size-sm: 0.875rem;   /* 14px */
  --p-font-size-base: 1rem;     /* 16px */
  --p-font-size-lg: 1.125rem;   /* 18px */
  --p-font-size-xl: 1.25rem;    /* 20px */
  --p-font-size-2xl: 1.5rem;    /* 24px */
  --p-font-size-3xl: 1.875rem;  /* 30px */

  /* ─── Border Radius ─── */
  --p-radius-sm: 4px;
  --p-radius-md: 8px;
  --p-radius-lg: 12px;
  --p-radius-xl: 16px;
  --p-radius-2xl: 20px;
  --p-radius-full: 9999px;

  /* ─── Shadow ─── */
  --p-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --p-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --p-shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
  --p-shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);

  /* ─── Z-Index Scale ─── */
  --p-z-dropdown: 200;
  --p-z-sticky: 300;
  --p-z-modal: 400;
  --p-z-toast: 500;
  --p-z-tooltip: 600;
}
```

### 4.2 Tier 2: Semantic Token'lar (Theme-Aware)

```css
:root {
  /* ─── Renk Semantic ─── */
  --s-color-primary: var(--p-color-pink-400);
  --s-color-primary-hover: var(--p-color-pink-500);
  --s-color-primary-active: var(--p-color-pink-600);
  --s-color-primary-light: var(--p-color-pink-100);

  --s-color-text-primary: rgba(255, 255, 255, 0.88);
  --s-color-text-secondary: rgba(255, 255, 255, 0.60);
  --s-color-text-muted: rgba(255, 255, 255, 0.40);

  --s-color-surface: rgba(255, 255, 255, 0.06);
  --s-color-surface-hover: rgba(255, 255, 255, 0.10);
  --s-color-surface-active: rgba(255, 255, 255, 0.14);

  --s-color-border: rgba(255, 255, 255, 0.08);
  --s-color-border-strong: rgba(255, 255, 255, 0.16);

  /* ─── Typography Semantic ─── */
  --s-font-body: 'Avalon', 'DMSans', sans-serif;
  --s-font-heading: 'Avalon', 'DMSans', sans-serif;
  --s-font-mono: 'JetBrains Mono', monospace;

  /* ─── Spacing Semantic ─── */
  --s-spacing-xs: var(--p-space-1);
  --s-spacing-sm: var(--p-space-2);
  --s-spacing-md: var(--p-space-4);
  --s-spacing-lg: var(--p-space-6);
  --s-spacing-xl: var(--p-space-8);

  /* ─── Border Radius Semantic ─── */
  --s-radius-sm: var(--p-radius-sm);
  --s-radius-md: var(--p-radius-md);
  --s-radius-lg: var(--p-radius-lg);
  --s-radius-pill: var(--p-radius-full);

  /* ─── Glass Effect ─── */
  --s-glass-bg: rgba(255, 255, 255, 0.06);
  --s-glass-blur: blur(20px);
  --s-glass-border: 1px solid rgba(255, 255, 255, 0.08);
  --s-glass-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
```

### 4.3 Tier 3: Component Token'lar

```css
:root {
  /* ─── Header Toolbar ─── */
  --c-toolbar-h: 48px;
  --c-toolbar-bg: var(--s-glass-bg);
  --c-toolbar-blur: var(--s-glass-blur);
  --c-toolbar-border: var(--s-glass-border);
  --c-toolbar-icon-size: 20px;
  --c-toolbar-icon-gap: 8px;
  --c-toolbar-icon-color: var(--s-color-text-secondary);
  --c-toolbar-icon-hover: var(--s-color-text-primary);

  /* ─── Volume Slider ─── */
  --c-volume-track-h: 4px;
  --c-volume-track-bg: rgba(255, 255, 255, 0.2);
  --c-volume-fill-bg: var(--s-color-primary);
  --c-volume-fill-radius: 2px;
  --c-volume-text-size: 9px;

  /* ─── Media Controls ─── */
  --c-mc-btn-size: 56px;
  --c-mc-btn-size-play: 64px;
  --c-mc-icon-size: 24px;
  --c-mc-icon-size-play: 28px;
  --c-mc-bg: rgba(255, 255, 255, 0.1);
  --c-mc-bg-hover: rgba(255, 255, 255, 0.2);
  --c-mc-radius: 50%;
  --c-mc-gap: 16px;

  /* ─── Footer Player ─── */
  --c-footer-h: 90px;
  --c-footer-cover: 85px;
  --c-footer-cover-h: 85px;
  --c-footer-text-size: 10px;
  --c-footer-media-w: 367px;
  --c-footer-icon: 13px;

  /* ─── Progress Bar ─── */
  --c-progress-h: 4.8px;
  --c-progress-bg: rgba(255, 255, 255, 0.2);
  --c-progress-fill: var(--s-color-primary);
}
```

---

## 5. CSS Cascade Layers (@layer) Entegrasyonu

### 5.1 Layer Sıralaması

```css
/* ═══ CoreMusic CSS Architecture ═══ */
@layer reset, tokens, base, layout, components, utilities;
```

### 5.2 Uygulama

```css
/* main.css — Master Entry Point */

/* 1. Layer sırasını tanımla */
@layer reset, tokens, base, layout, components, utilities;

/* 2. Reset (ITCSS: Generic) */
@layer reset {
  @import url('02_Base/b-base-core.css');
}

/* 3. Token'lar (ITCSS: Settings) */
@layer tokens {
  @import url('01_Abstracts/a-design-tokens.css');
  @import url('01_Abstracts/a-colors-token.css');
  @import url('01_Abstracts/a-fonts-token.css');
  @import url('01_Abstracts/a-layout-tokens.css');
  @import url('04_Components/c-component.css');
}

/* 4. Layout (ITCSS: Objects) */
@layer layout {
  @import url('03_Layout/_header.css');
  @import url('03_Layout/_footer.css');
  @import url('03_Layout/_sidebar.css');
  @import url('05_Pages/_home-layout.css');
}

/* 5. Components (ITCSS: Components) */
@layer components {
  @import url('04_Components/c-buttons.css');
  @import url('04_Components/c-card.css');
  @import url('04_Components/c-modal.css');
  @import url('04_Components/c-player.css');
  @import url('04_Components/c-toolbar.css');
  @import url('04_Components/c-media-controls.css');
  @import url('04_Components/c-volume-slider.css');
  /* ... diğer component dosyaları */
}

/* 6. Utilities (ITCSS: Trumps) */
@layer utilities {
  @import url('06_Utilities/u-helpers-utility.css');
}

/* 7. Device Overrides (layer dışında) */
@import url('08_Devices/d-embedded.css');
@import url('08_Devices/d-desktop.css');

/* 8. View Modes (layer dışında) */
@import url('09_ViewModes/v-home.css');
```

---

## 6. JS ES2022+ Component Mimarisi

### 6.1 ComponentBase.js (Abstract Base Class)

```javascript
/**
 * ComponentBase — Abstract Base Class for All CoreMusic Components
 * ES2022+ | Vanilla JS | No Framework
 *
 * Lifecycle:
 *   constructor → init() → connectedCallback() → render() → disconnectedCallback()
 *
 * @package CoreMusic\JS\Components\Base
 */
export class ComponentBase extends HTMLElement {
  #state;
  #shadow;
  #eventListeners;

  constructor() {
    super();
    this.#state = new Proxy({}, {
      set: (target, prop, value) => {
        const old = target[prop];
        target[prop] = value;
        if (old !== value) this.#onStateChange(prop, value, old);
        return true;
      }
    });
    this.#eventListeners = new Map();
  }

  /** State erişimi */
  get state() { return this.#state; }
  set state(newState) {
    Object.assign(this.#state, newState);
  }

  /** Lifecycle: connected */
  connectedCallback() {
    this.init();
    this.render();
    this.bindEvents();
  }

  /** Lifecycle: disconnected */
  disconnectedCallback() {
    this.unbindEvents();
    this.onDestroy();
  }

  /** Override: başlatma */
  init() {}

  /** Override: Render HTML */
  render() {}

  /** Override: Event binding */
  bindEvents() {}

  /** Override: Event unbinding */
  unbindEvents() {}

  /** Override: Cleanup */
  onDestroy() {}

  /** Override: State change handler */
  onStateChange(key, newVal, oldVal) {
    this.render();
  }

  /** Event delegation helper */
  on(selector, event, handler) {
    const el = this.matches(selector) ? this : this.querySelector(selector);
    if (!el) return;
    el.addEventListener(event, handler);
    const key = `${selector}:${event}`;
    if (!this.#eventListeners.has(key)) {
      this.#eventListeners.set(key, []);
    }
    this.#eventListeners.get(key).push({ el, event, handler });
  }

  /** Cleanup all event listeners */
  #unbindEvents() {
    for (const [, listeners] of this.#eventListeners) {
      for (const { el, event, handler } of listeners) {
        el.removeEventListener(event, handler);
      }
    }
    this.#eventListeners.clear();
  }

  /** Safe HTML rendering (no innerHTML) */
  html(strings, ...values) {
    const template = document.createElement('template');
    template.innerHTML = strings.reduce((result, str, i) =>
      result + str + (values[i] ?? ''), '');
    return template.content.cloneNode(true);
  }

  /** Dispatch custom event */
  emit(eventName, detail = {}) {
    this.dispatchEvent(new CustomEvent(eventName, {
      detail,
      bubbles: true,
      composed: true
    }));
  }
}
```

### 6.2 ToolbarComponent.js

```javascript
/**
 * ToolbarComponent — Header Toolbar with Icons and Volume
 * Figma: Header toolbar with glassmorphism effect
 *
 * @package CoreMusic\JS\Components
 */
import { ComponentBase } from './base/ComponentBase.js';

export class ToolbarComponent extends ComponentBase {
  static get observedAttributes() {
    return ['volume', 'muted'];
  }

  init() {
    this.state = {
      volume: 100,
      muted: false,
      icons: [
        { action: 'prev', label: 'Önceki', icon: 'prev' },
        { action: 'shuffle', label: 'Karıştır', icon: 'shuffle' },
        { action: 'pause', label: 'Duraklat', icon: 'pause' },
        { action: 'repeat', label: 'Tekrarla', icon: 'repeat' },
        { action: 'equalizer', label: 'Ekolayzır', icon: 'equalizer' },
        { action: 'wifi', label: 'Wi-Fi', icon: 'wifi' },
        { action: 'radio', label: 'Radyo', icon: 'radio' },
        { action: 'music', label: 'Müzik', icon: 'music' },
        { action: 'settings', label: 'Ayarlar', icon: 'settings' },
      ]
    };
  }

  render() {
    const { volume, muted, icons } = this.state;

    this.innerHTML = `
      <div class="header-toolbar__inner">
        <nav class="header-toolbar__icons" aria-label="Hızlı erişim">
          ${icons.map(icon => `
            <button class="header-toolbar__icon"
                    data-action="${icon.action}"
                    aria-label="${icon.label}"
                    title="${icon.label}">
              <svg width="20" height="20" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round">
                <use href="#icon-${icon.icon}"/>
              </svg>
            </button>
          `).join('')}
        </nav>
        <div class="header-toolbar__volume">
          <svg class="header-toolbar__vol-icon" width="20" height="20">
            <use href="#icon-volume"/>
          </svg>
          <div class="header-toolbar__slider-track">
            <input type="range" class="header-toolbar__slider"
                   min="0" max="100" value="${volume}"
                   aria-label="Ses seviyesi">
            <div class="header-toolbar__slider-fill"
                 style="width: ${volume}%"></div>
          </div>
          <span class="header-toolbar__vol-text">% ${volume}</span>
        </div>
      </div>
    `;
  }

  bindEvents() {
    this.on('.header-toolbar__slider', 'input', (e) => {
      this.state.volume = parseInt(e.target.value);
      this.emit('volume:change', { volume: this.state.volume });
    });

    this.on('.header-toolbar__icon', 'click', (e) => {
      const action = e.currentTarget.dataset.action;
      this.emit('toolbar:action', { action });
    });
  }
}

// Register
customElements.define('cm-toolbar', ToolbarComponent);
```

### 6.3 MediaControlsComponent.js

```javascript
/**
 * MediaControlsComponent — Circular Play/Stop/Prev/Next Buttons
 * Figma: Circular media control buttons
 *
 * @package CoreMusic\JS\Components
 */
import { ComponentBase } from './base/ComponentBase.js';

export class MediaControlsComponent extends ComponentBase {
  static get observedAttributes() {
    return ['playing'];
  }

  init() {
    this.state = {
      playing: false,
      controls: [
        { action: 'prev', label: 'Önceki', icon: 'prev', size: 24 },
        { action: 'play', label: 'Oynat', icon: 'play', size: 28, primary: true },
        { action: 'stop', label: 'Durdur', icon: 'stop', size: 24 },
        { action: 'next', label: 'Sonraki', icon: 'next', size: 24 },
      ]
    };
  }

  render() {
    const { playing, controls } = this.state;

    this.innerHTML = `
      <section class="media-controls" aria-label="Oynatma kontrolleri">
        ${controls.map(ctrl => `
          <button class="media-controls__btn${ctrl.primary ? ' media-controls__btn--play' : ''}"
                  data-action="${ctrl.action}"
                  aria-label="${ctrl.label}"
                  aria-pressed="${ctrl.action === 'play' ? playing : false}">
            <svg class="media-controls__icon${ctrl.primary ? ' media-controls__icon--play' : ''}"
                 width="${ctrl.size}" height="${ctrl.size}"
                 viewBox="0 0 24 24" fill="currentColor">
              <use href="#icon-${ctrl.icon}"/>
            </svg>
          </button>
        `).join('')}
      </section>
    `;
  }

  bindEvents() {
    this.on('.media-controls__btn', 'click', (e) => {
      const action = e.currentTarget.dataset.action;

      if (action === 'play') {
        this.state.playing = !this.state.playing;
      }

      this.emit('player:control', { action, playing: this.state.playing });
    });
  }

  attributeChangedCallback(name, oldVal, newVal) {
    if (name === 'playing') {
      this.state.playing = newVal === 'true';
    }
  }
}

// Register
customElements.define('cm-media-controls', MediaControlsComponent);
```

---

## 7. Glassmorphism Efekt Sistemi

### 7.1 CSS Glassmorphism Token'ları

```css
:root {
  /* ─── Glass Effect Tokens ─── */
  --glass-bg: rgba(255, 255, 255, 0.06);
  --glass-bg-strong: rgba(255, 255, 255, 0.12);
  --glass-blur: blur(20px);
  --glass-blur-strong: blur(40px);
  --glass-border: 1px solid rgba(255, 255, 255, 0.08);
  --glass-border-strong: 1px solid rgba(255, 255, 255, 0.16);
  --glass-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  --glass-shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.3);
}
```

### 7.2 Glassmorphism Mixin (CSS)

```css
/* ═══ Glass Panel ═══ */
.glass-panel {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
  border: var(--glass-border);
  border-radius: var(--s-radius-lg);
  box-shadow: var(--glass-shadow);
}

/* ═══ Glass Card ═══ */
.glass-card {
  background: var(--glass-bg-strong);
  backdrop-filter: var(--glass-blur-strong);
  -webkit-backdrop-filter: var(--glass-blur-strong);
  border: var(--glass-border-strong);
  border-radius: var(--s-radius-xl);
  box-shadow: var(--glass-shadow-lg);
}

/* ═══ Glass Toolbar ═══ */
.glass-toolbar {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur);
  -webkit-backdrop-filter: var(--glass-blur);
  border-bottom: var(--glass-border);
}
```

---

## 8. SVG Icon Sistemi

### 8.1 SVG Sprite Sheet

Tüm ikonlar tek bir SVG sprite'ta toplanır:

```html
<!-- icons.svg — SVG Sprite (footer.php'ye ekle) -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="icon-prev" viewBox="0 0 24 24">
    <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
  </symbol>
  <symbol id="icon-play" viewBox="0 0 24 24">
    <path d="M8 5v14l11-7z"/>
  </symbol>
  <symbol id="icon-pause" viewBox="0 0 24 24">
    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
  </symbol>
  <symbol id="icon-stop" viewBox="0 0 24 24">
    <path d="M6 6h12v12H6z"/>
  </symbol>
  <symbol id="icon-next" viewBox="0 0 24 24">
    <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
  </symbol>
  <symbol id="icon-shuffle" viewBox="0 0 24 24">
    <path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/>
  </symbol>
  <symbol id="icon-repeat" viewBox="0 0 24 24">
    <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
  </symbol>
  <symbol id="icon-equalizer" viewBox="0 0 24 24">
    <path d="M10 20h4V4h-4v16zm-6 0h4v-8H4v8zM16 9v11h4V9h-4z"/>
  </symbol>
  <symbol id="icon-wifi" viewBox="0 0 24 24">
    <path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/>
  </symbol>
  <symbol id="icon-radio" viewBox="0 0 24 24">
    <path d="M3.24 6.15C2.51 6.43 2 7.17 2 8v12c0 1.1.89 2 2 2h16c1.11 0 2-.9 2-2V8c0-1.11-.9-2-2-2H8.3l8.26-3.34-.37-.91L3.24 6.15zM7 20c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm13-8h-2v-2h2v2z"/>
  </symbol>
  <symbol id="icon-music" viewBox="0 0 24 24">
    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
  </symbol>
  <symbol id="icon-settings" viewBox="0 0 24 24">
    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
  </symbol>
  <symbol id="icon-volume" viewBox="0 0 24 24">
    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
  </symbol>
</svg>
```

---

## 9. Responsive Breakpoint Sistemi

### 9.1 4-Tier Media Queries

```css
/* ═══ Default: 1024px (Embedded/RPi5) ═══ */
:root {
  --header-h: 60px;
  --footer-h: 90px;
  --content-h: 450px;
  --toolbar-h: 48px;
  --mc-btn-size: 56px;
  --volume-w: 186px;
}

/* ═══ Tablet: 768-1024px ═══ */
@media (min-width: 768px) and (max-width: 1024px) {
  :root {
    --header-h: 60px;
    --footer-h: 90px;
    --toolbar-h: 48px;
  }
}

/* ═══ Desktop/Wide: 1920px ═══ */
@media (min-width: 1920px) {
  :root {
    --header-h: 70px;
    --footer-h: 104px;
    --content-h: 906px;
    --toolbar-h: 52px;
    --mc-btn-size: 64px;
    --volume-w: 353px;
  }
}

/* ═══ 4K TV: 3840px ═══ */
@media (min-width: 3840px) {
  :root {
    --header-h: 80px;
    --footer-h: 120px;
    --content-h: 1960px;
    --toolbar-h: 56px;
    --mc-btn-size: 72px;
    --volume-w: 500px;
    --spacing-scale: 1.5;
  }
}

/* ═══ Mobile: ≤767px ═══ */
@media (max-width: 767px) {
  :root {
    --header-h: 0; /* bottom tab nav */
    --footer-h: 80px;
    --toolbar-h: 44px;
    --mc-btn-size: 48px;
    --volume-w: 120px;
  }
}
```

---

## 10. Uygulama Planı — Fazlar

### Faz 1: Temel Altyapı (1-2 gün)
1. ✅ Design token dosyası oluştur (`a-design-tokens-v2.css`)
2. ✅ @layer yapısını kur (`main.css` yeniden yaz)
3. ✅ SVG sprite sheet oluştur
4. ✅ ComponentBase.js ES2022+ versiyonunu yaz

### Faz 2: Header Toolbar (1 gün)
1. Figma'dan pixel-perfect değerleri çek
2. `c-toolbar.css` oluştur
3. `ToolbarComponent.js` yaz
4. `header.php`'yi yeniden yapılandır

### Faz 3: Media Controls (1 gün)
1. Figma'dan dairesel buton analizini yap
2. `c-media-controls.css` oluştur
3. `MediaControlsComponent.js` yaz
4. `footer.php`'deki kontrolleri güncelle

### Faz 4: Volume Slider (0.5 gün)
1. `c-volume-slider.css` oluştur
2. `VolumeSlider.js` yaz
3. Custom range input styling

### Faz 5: Glassmorphism & Efektler (0.5 gün)
1. `c-glassmorphism.css` oluştur
2. Backdrop-filter optimizations
3. Fallback for older browsers

### Faz 6: Responsive Testing (1 gün)
1. 4 tier'da test (phone→embedded→desktop→4K)
2. Device CSS override'ları
3. Pixel-perfect doğrulama

### Faz 7: Integration & Testing (1 gün)
1. PHP view entegrasyonu
2. Event bus bağlantısı
3. Cross-browser testing

---

## 11. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `innerHTML` ile güvenli olmayan HTML | `DOMParser` + `TrustedTypes` |
| `var` kullanımı | `const` / `let` |
| Framework (React, Vue, Angular) | Vanilla JS ES2022+ |
| `eval()` / `Function()` | Safe alternatives |
| Hardcoded renk değerleri | CSS custom properties |
| `!important` kullanımı | @layer cascade |
| BEM iç içe seçiciler | Düz BEM seçiciler |
| Inline style | CSS classes + tokens |
| `localStorage` auth token | Session-based auth |

---

## 12. Kalite Kontrol Listesi

- [ ] Design token'lar 3-tier yapısına uygun mu?
- [ ] @layer sırası doğru mu? (reset→tokens→base→layout→components→utilities)
- [ ] BEM isimlendiresi tutarlı mı?
- [ ] Tüm component'ler `ComponentBase` extend ediyor mu?
- [ ] SVG icon'lar sprite'ta mı?
- [ ] Responsive breakpoint'ler test edildi mi?
- [ ] Glassmorphism fallback var mı?
- [ ] WCAG 2.2 AA uyumlu mu? (touch target, contrast, focus)
- [ ] CSP nonce uyumlu mu?
- [ ] Pixel-perfect doğrulama yapıldı mı?

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode
