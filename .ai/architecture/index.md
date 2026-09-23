---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Master Architecture Index"
type: architecture-index
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Master Architecture Index

**Toplam Katman:** 21 (K0-K20)
**Toplam Bileşen:** 1,130+
**Toplam Dosya:** 200+ MD

---

## 1. Katman Haritası

```
┌──────────────────────────────────────────────────────────────────────────────┐
│  K13: CI/CD          │  K12: İZLEME       │  K15: MEDYA & STREAMING          │
│  GitHub Actions      │  App Logs          │  FFmpeg • FLAC • MP3 • HLS       │
│  Docker • K8s        │  Prometheus        │  DASH • Podcast • Radio          │
├──────────────────────┴────────────────────┴──────────────────────────────────┤
│  K11: KULLANICI DENEYİMİ                                                     │
│  ITCSS 9-layer • BEM • Design Tokens • Theme Engine • PWA • A11y             │
├──────────────────────────────────────────────────────────────────────────────┤
│  K10: UYGULAMA                                                               │
│  Music • Home • Car • Studio • Admin • Download • Landing • Pro • Media      │
├──────────────────────────────────────────────────────────────────────────────┤
│  K14: AĞ & İLETİŞİM                                                          │
│  HTTP/2 • WebSocket • mDNS • DLNA • AirPlay • WebRTC • DNS • VPN             │
├──────────────────────────────────────────────────────────────────────────────┤
│  K9: API & ROUTING                                                           │
│  Gateway • BFF • CQRS • Event Bus • SPA Router • OpenAPI • Versioning        │
├──────────────────────────────────────────────────────────────────────────────┤
│  K8: SERVİS                                                                  │
│  Control • Media • Audio • Device • Network • AI • Download • Health         │
├──────────────────────────────────────────────────────────────────────────────┤
│  K7: MIDDLEWARE                                                              │
│  OriginCheck • CORS • RateLimit • SecurityHeaders • Session • CSRF           │
├──────────────────────────────────────────────────────────────────────────────┤
│  K6: GÜVENLİK                                                                │
│  Auth • RBAC • CSRF • CSP • RateLimit • Encryption • Vault • Audit           │
├──────────────────────────────────────────────────────────────────────────────┤
│  K5: VERİ YÖNETİMİ                                                           │
│  MySQL 18 DB • Redis • APCu • File System • SQLite • Backup • Archive        │
├──────────────────────────────────────────────────────────────────────────────┤
│  K4: YAPAY ZEKA                                                              │
│  Analysis • Recommendation • Auto EQ • Voice • AI Gen • ML • Edge AI         │
├──────────────────────────────────────────────────────────────────────────────┤
│  K3: SES İŞLEME MOTORU                                                       │
│  Neva Engine • DSP Chain • Mixer • EQ • Reverb • Compressor • Analyzer       │
├──────────────────────────────────────────────────────────────────────────────┤
│  K2: SÜRÜCÜ                                                                  │
│  ASIO • WASAPI • ALSA • PipeWire • CoreAudio • I2S • USB • MIDI              │
├──────────────────────────────────────────────────────────────────────────────┤
│  K1: DONANIM                                                                 │
│  XMOS XU316 • PCM3168A • AK4458 • Class AB ×8 • Speakers ×9 • Power          │
├──────────────────────────────────────────────────────────────────────────────┤
│  K0: İŞLETİM SİSTEMİ                                                         │
│  Windows • Linux • macOS • RPi5 • ReactOS • Docker • IPC • Memory • Threading│
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Katman Detayları

| Katman | İsim | Bileşen | Dosya | ADR |
|--------|------|---------|-------|-----|
| K0 | İşletim Sistemi | 50 | 6 | ADR-017, ADR-019 |
| K1 | Donanım | 120 | 7 | ADR-038, ADR-089 |
| K2 | Sürücü | 40 | 1 | ADR-017 |
| K3 | Ses Motoru | 50 | 8 | ADR-025, ADR-062 |
| K4 | Yapay Zeka | 50 | 1 | ADR-030 |
| K5 | Veri Yönetimi | 55 | 1 | ADR-040 |
| K6 | Güvenlik | 40 | 1 | ADR-010/011/012/013/022/034 |
| K7 | Middleware | 35 | 1 | ADR-010/011/012/013/022 |
| K8 | Servis | 55 | 1 | ADR-039, ADR-086 |
| K9 | API & Routing | 30 | 1 | ADR-083, ADR-084 |
| K10 | Uygulama | 45 | 1 | ADR-039, ADR-045 |
| K11 | UX | 40 | 1 | ADR-001, ADR-044 |
| K12 | İzleme | 35 | 1 | ADR-006 |
| K13 | CI/CD | 35 | 1 | — |
| K14 | Ağ & İletişim | 40 | 1 | — |
| K15 | Medya & Streaming | 35 | 1 | — |
| K16 | Class AB | 120 | 1 | ADR-089 |
| K17 | Güç Kaynağı | 85 | 1 | ADR-089 |
| K18 | Termal | 45 | 1 | ADR-089 |
| K19 | PCB | 50 | 1 | ADR-089 |
| K20 | BOM & Üretim | 40 | 1 | ADR-089 |
| | **TOPLAM** | **1,130+** | **40+** | **30+** |

---

## 3. Katman Bağımlılık Matrisi

| Kaynak → Hedef | İzinli? |
|-----------------|---------|
| K0 → K1 | ✅ |
| K1 → K2 | ✅ |
| K2 → K3 | ✅ |
| K3 → K4 | ✅ |
| K4 → K5 | ✅ |
| K5 → K6 | ✅ |
| K6 → K7 | ✅ |
| K7 → K8 | ✅ |
| K8 → K9 | ✅ |
| K9 → K10 | ✅ |
| K10 → K11 | ✅ |
| K11 → K12 | ✅ |
| K12 → K13 | ✅ |
| K13 → K14 | ✅ |
| K14 → K15 | ✅ |
| K15 → K16-K20 | ✅ |
| **K0 → K3** | ❌ Layer Violation |
| **K0 → K10** | ❌ Layer Violation |
| **K1 → K5** | ❌ Layer Violation |
| **K3 → K1** | ❌ Layer Violation |
| **K10 → K0** | ❌ Layer Violation |

---

## 4. Dosya Yapısı

```
.ai/architecture/
├── index.md                    ← Bu dosya
├── katman-baglilik-matrisi.md  ← Layer dependency matrix
├── github-referanslari.md      ← GitHub reference table
│
├── k0-isletim-sistemi/
│   ├── README.md              ← K0 genel bakış
│   └── windows-api.md         ← Windows detay
│
├── k1-donanim/
│   └── README.md              ← K1 genel bakış
│
├── k2-surucu/
│   └── README.md              ← K2 genel bakış
│
├── k3-ses-motoru/
│   └── README.md              ← K3 genel bakış
│
├── k4-yapay-zeka/
│   └── README.md              ← K4 genel bakış
│
├── k5-veri-yonetimi/
│   └── README.md              ← K5 genel bakış
│
├── k6-guvenlik/
│   └── README.md              ← K6 genel bakış
│
├── k7-middleware/
│   └── README.md              ← K7 genel bakış
│
├── k8-servis/
│   └── README.md              ← K8 genel bakış
│
├── k9-api-routing/
│   └── README.md              ← K9 genel bakış
│
├── k10-uygulama/
│   └── README.md              ← K10 genel bakış
│
├── k11-ux/
│   └── README.md              ← K11 genel bakış
│
├── k12-izleme/
│   └── README.md              ← K12 genel bakış
│
├── k13-cicd/
│   └── README.md              ← K13 genel bakış
│
├── k14-ag/
│   └── README.md              ← K14 genel bakış
│
├── k15-medya-streaming/
│   └── README.md              ← K15 genel bakış
│
├── k16-class-ab/
│   └── README.md              ← K16-K20 genel bakış
│
├── k17-guc-kaynagi/          ← (K16 içinde)
├── k18-termal/                ← (K16 içinde)
├── k19-pcb/                   ← (K16 içinde)
└── k20-bom/                   ← (K16 içinde)
```

---

*Master Architecture Index v2.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
