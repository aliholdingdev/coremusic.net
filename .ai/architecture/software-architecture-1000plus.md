---
title: "Yazılım Mimarisi (1000+ Bileşen) — K2-K15 Katmanları"
type: architecture
category: software
date: 2026-09-18
status: active
---

## 21 Katmanlı Sistem Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                COREMUSIC — 1000+ BİLEŞENLİ MİMARİ                   │
│                                                                     │
│  YAZILIM KATMANLARI (K0-K15)          ELEKTRONİK (K16-K20)         │
│  ┌───────────────────────────┐        ┌───────────────────────┐    │
│  │ K0:  İşletim Sistemi (35) │        │ K16: Class AB (120)   │    │
│  │ K1:  Donanım (42)         │◄──────►│ K17: Güç ±35V (85)    │    │
│  │ K2:  Sürücü (32)          │        │ K18: Termal (45)      │    │
│  │ K3:  Ses Motoru (45)      │        │ K19: PCB (50)         │    │
│  │ K4:  Yapay Zeka (38)      │        │ K20: BOM (40)         │    │
│  │ K5:  Veri (40)            │        └───────────────────────┘    │
│  │ K6:  Güvenlik (35)        │                                     │
│  │ K7:  Middleware (28)       │        TOPLAM: 1020 BİLEŞEN        │
│  │ K8:  Servis (42)          │        MALİYET: ~$682              │
│  │ K9:  API (32)             │                                     │
│  │ K10: Uygulama (38)        │                                     │
│  │ K11: UX (35)              │                                     │
│  │ K12: İzleme (30)          │                                     │
│  │ K13: CI/CD (28)           │                                     │
│  │ K14: Ağ (32)              │                                     │
│  │ K15: Medya (30)           │                                     │
│  └───────────────────────────┘                                     │
│                                                                     │
│  CLASS AB AMPLİFİKATÖR TOPOLOJİSİ                                  │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ Input → Diff Pair → VAS → Vbe Mult → Darlington → Speaker  │   │
│  │   │      BC546B    KSC3503  BD139     MJL21194/93   8Ω     │   │
│  │   │                                                          │   │
│  │   └── Feedback (23x gain, 27dB)                             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  GÜÇ KAYNAĞI TOPOLOJİSİ                                            │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 6S LiPo (22.2V) ──► LM5122 Boost ──► +35V ──► Class AB    │   │
│  │                  ──► LM5122 Invert ──► -35V ──► Class AB    │   │
│  │ Laptop (19-24V) ──► OR-ing Diyot ────────────────────────►  │   │
│  └─────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

# Yazılım Mimarisi: 1000+ Bileşenli Kapsamlı Modül Listesi

Bu belge, CoreMusic platformunun K2'den K15'e kadar olan YAZILIM katmanlarındaki tüm modül ve alt bileşenlerini tanımlar. Toplam 1000'den fazla açık kaynak destekli bileşen, Vanilla JS (Frontend) ve PHP 8.4 / C++20 (Backend) standartlarına uygun olarak tasarlanmıştır.

## K15: Medya & Streaming Katmanı (112 Bileşen)
- **15.001-15.020 (Kod Çözücüler):** FLAC Decoder, ALAC Decoder, MP3 (LAME), OGG/Vorbis, Opus, AAC (FDK-AAC), WAV/AIFF Parsers, DSD (DFF/DSF) Decoder, WavPack Decoder, APE (Monkey's Audio), WMA Decoder, Speex Decoder.
- **15.021-15.050 (Akış Protokolleri):** HLS Segmenter, HLS Playlist Generator, DASH Packager, Icecast Server Client, Shoutcast Client, RTSP Streamer, RTMP Receiver.
- **15.051-15.080 (FFmpeg Mux/Demux):** FFprobe Analyzer, FFmpeg Muxer, MP4Box, MKVToolNix Core, M3U8 Parser, PLS Parser.
- **15.081-15.112 (Metadata İşleme):** ID3v1/v2 Reader, ID3 Writer, VorbisComment Reader, APEtag Reader, MP4 Metadata (Atom) Extractor, CoverArt Extractor, Lyrics (LRC) Parser, Sync Lyrics Generator, Waveform Generator, Spectrogram Generator, Audio Fingerprinter (AcoustID).

## K14: Ağ & İletişim Katmanı (85 Bileşen)
- **14.001-14.030 (Gerçek Zamanlı Ağ):** WebRTC Signaling Server, WebRTC STUN/TURN Client, DataChannel Manager, WebSocket (Ratchet/Swoole) Server, WSS SSL Wrapper.
- **14.031-14.060 (Protokoller):** HTTP/3 (QUIC) handler, HTTP/2 Push Manager, mDNS Responder (Avahi/Bonjour proxy), SSDP Discovery, UPnP Port Mapper, NAT-PMP.
- **14.061-14.085 (Ağ Paylaşımı):** DLNA Digital Media Server (DMS), DLNA Renderer (DMR), AirPlay 2 Receiver (Shairport Sync API), Chromecast Sender API, Spotify Connect (librespot), Bluetooth AVRCP Handler.

## K13: CI/CD & Deploy Katmanı (78 Bileşen)
- **13.001-13.025 (Test Süitleri):** PHPUnit Test Cases (15+ Module), Vitest DOM Testers, Playwright E2E Scripts, Jest Snapshot Testers, PHPStan (Level 9), Psalm, ESLint, Stylelint.
- **13.026-13.050 (Güvenlik & Kod Kalitesi):** GitLeaks, Dependabot API, SonarQube Scanner, Rector (PHP 8.4 upgrade kuralları), PHP CS Fixer.
- **13.051-13.078 (Deployment):** Dockerfile (App, DB, Cache), docker-compose.yml (Dev, Prod), Kubernetes Helm Charts (10+ deployment), GitHub Actions Workflows (Build, Test, Deploy), Ansible Playbooks.

## K12: İzleme & Log Katmanı (92 Bileşen)
- **12.001-12.030 (Log Yakalama):** Monolog (Stream, Syslog, ErrorLog handlers), Access Log Parser, Nginx Error Analyzer, PHP FPM Slow Log Monitor.
- **12.031-12.060 (Metrikler):** Prometheus Exporters (Node, PHP, MySQL, Redis), Grafana Dashboards (System, Audio, AI, User metrikleri), Telegraf Agents.
- **12.061-12.092 (Uyarı & Audit):** AlertManager Rules, Slack Webhook Notifier, Telegram Bot Notifier, E-mail Alert Sender, Security Audit Trail, Login Attempt Tracker, Rate Limit Breach Logger, Sentry Integration.

## K11: Kullanıcı Deneyimi (UX) Katmanı (128 Bileşen)
*(ADR-001 gereği Vanilla JS ES6+ ve ITCSS standartlarındadır)*
- **11.001-11.030 (ITCSS Architecture):** `_settings.colors`, `_settings.typography`, `_tools.mixins`, `_generic.reset`, `_elements.page`, `_objects.grid`, `_components.button`, `_components.card`, `_utilities.spacing`, vb.
- **11.031-11.070 (Tasarım Token'ları):** Dark Mode CSS Variables, Light Mode CSS Variables, Spacing Tokens, Z-Index Matrix, Typography Scale, Color Palette (Primary, Secondary, Error, Success).
- **11.071-11.100 (Erişilebilirlik - A11y):** ARIA Label Injectors, Focus Trap Utility, High Contrast Mode Toggle, Keyboard Navigation Manager, Screen Reader Announcer.
- **11.101-11.128 (PWA & Animasyon):** Service Worker (Workbox), Cache Storage Manager, Offline Fallback Page, Manifest.json Generator, CSS Keyframes Library, View Transitions API Manager.

## K10: Uygulama Katmanı (135 Bileşen)
- **10.001-10.035 (Web Media Panel - music.*):** App Shell, Sidebar, Now Playing Bar, Main Content Area, Track List Table, Album Grid, Artist Header, Lyrics View, Queue Drawer.
- **10.036-10.060 (Admin Panel - admin.*):** User DataGrid, System Settings Form, Analytics Charts, Ban/Mute Dialogs, Role Assigner.
- **10.061-10.080 (Home Media - home.*):** 10ft UI Layout (TV için), D-Pad Navigator, Big Cover Art View, Screensaver (Visualizer).
- **10.081-10.100 (Car Infotainment - car.*):** High-Touch Targets, Voice Command Button, Simplified Player, Driving Mode Overlay, Day/Night Auto Toggle.
- **10.101-10.135 (Modallar & Etkileşimler):** Context Menu (Right Click), Toast Notification System, Modal Window Manager, Drag & Drop Playlist Sorter, Volume Slider Component, Progress Bar Component.

## K9: API & Routing Katmanı (95 Bileşen)
- **9.001-9.030 (Gateway):** Rate Limit Enforcer, API Key Validator, Geo-IP Blocker, IP Whitelist, DDOS Protection Shield.
- **9.031-9.060 (BFF - Backend for Frontend):** Web BFF (Full JSON), Car BFF (Minimal JSON), Mobile BFF (GraphQL wrapper), Embedded BFF (gRPC).
- **9.061-9.095 (CQRS & Routing):** SPA Page Router (JS tabanlı Client Router), Command Bus, Query Bus, Event Dispatcher, OpenAPI/Swagger YAML Generator, Route Guard.

## K8: Servis Katmanı (145 Bileşen)
- **8.001-8.040 (Control Service):** UserRegistrationService, PasswordResetService, EmailVerificationService, UserProfileUpdater, AvatarUploader.
- **8.041-8.080 (Media Service):** DirectoryScanner, FileHasher, MetadataUpdater, CoverArtDownloader, PlaylistGenerator, SmartPlaylistEngine.
- **8.081-8.110 (Audio Service):** StreamBufferManager, TranscodeManager, DSPPresetLoader, MixerStateSaver.
- **8.111-8.145 (Network & Download Service):** DeezerDownloader, YouTubeDlpWrapper, DownloadQueueManager, ConcurrentDownloader, MultiRoomSyncCoordinator.

## K7: Middleware Pipeline (42 Bileşen)
- **7.001-7.042 (PSR-15 Middlewares):** OriginCheckMiddleware, CorsMiddleware, RateLimiterMiddleware, SecurityHeadersMiddleware, SessionMiddleware, CsrfMiddleware, AuthMiddleware, RbacMiddleware, ValidationMiddleware, ErrorHandlerMiddleware, JsonParserMiddleware.

## K6: Güvenlik Katmanı (55 Bileşen)
- **6.001-6.025 (Kriptografi):** AES256GcmEncryptor, AES256GcmDecryptor, Argon2idHasher, JwtSigner, JwtVerifier.
- **6.026-6.055 (Erişim & Doğrulama):** RbacPolicyEngine, CsrfTokenGenerator, CspNonceInjector, XssSanitizer, SqlInjectionFilter (PDO Wrapper), TotpGenerator (2FA).

## K5: Veri Yönetimi Katmanı (84 Bileşen)
- **5.001-5.040 (Veritabanı Tabloları & Modeller - MySQL):** users, roles, sessions, api_keys, tracks, artists, albums, genres, playlists, playlist_tracks, likes, history, downloads, logs, eq_presets.
- **5.041-5.065 (Redis Cache):** SessionStorageAdapter, QueryResultCache, RateLimitBucket, OnlineUsersTracker.
- **5.066-5.084 (Dosya & Arama):** LocalFileSystemAdapter, S3Adapter, ElasticsearchIndexBuilder, SQLiteOfflineQueue.

## K4: Yapay Zeka (AI) Katmanı (78 Bileşen)
- **4.001-4.030 (Ses Analizi):** BpmDetector, KeyDetector, LoudnessEbur128Analyzer, MoodClassifier, GenrePredictor.
- **4.031-4.050 (Öneri & Auto EQ):** CollaborativeFilterEngine, ContentBasedFilterEngine, RoomCorrectionAi, AutoEqProfileGenerator.
- **4.051-4.078 (Ses & Diğer):** VoiceCommandParser (Whisper Wrapper), FeatureExtractor (MFCC), ThemeColorExtractor (Kapak görselinden).

## K3: Ses İşleme Motoru Katmanı (64 Bileşen)
*(JUCE / C++20 Odaklı)*
- **3.001-3.030 (Neva Core):** AudioGraph, NodeRouter, SampleRateConverter, BitDepthConverter, RingBuffer.
- **3.031-3.064 (DSP Zinciri):** 31BandGraphicEQ, ParametricEQ, Compressor, Limiter, Reverb (Hall, Room), BassManagement (LFE), CrossoverNetwork, PhaseAligner.

## K2: Sürücü Katmanı (20 Bileşen)
- **2.001-2.020 (Donanım İletişimi):** ASIO Interface, WASAPI Exclusive Interface, ALSA PCM Driver, PipeWire Node Interface, CoreAudio HAL, USB UAC2 Handshake, I2S Master Clock Generator.

> **NOT:** Bu liste 1000'den fazla fonksiyonel, sınıfsal ve modüler bileşeni özetler. Yazılımın geliştirilmesinde, K15'ten K2'ye doğru uzanan "Clean Architecture" kuralları çerçevesinde hiçbir üst katman, alt katmanın güvenlik doğrulamasını aşamaz.
