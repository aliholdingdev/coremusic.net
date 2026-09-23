---
title: "CoreMusic — Agent Profiles Index"
type: index
category: agent-registry
date: 2026-09-21
updated: 2026-09-21
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .agents/ — Agent Profilleri İndeksi

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[../CLAUDE.md]] · [[../WORKFLOW.md]]

---

## 1. Amaç

Bu dizin, CoreMusic ekosistemindeki 11 AI ajanının detaylı profil dosyalarını içerir. Her dosya, ajanın amacını,rollerini, domain sınırlarını, teknoloji yığınını, kod standartlarını ve kalite hedeflerini tanımlar.

---

## 2. Agent Profilleri

| # | Agent | Kod Adı | Dosya | Katman |
|---|-------|---------|-------|--------|
| 1 | Master Orchestrator | `mo` | [[./master-orchestrator]] | Koordinasyon |
| 2 | Backend Architect | `backend` | [[./backend-architect]] | L2 (Routing) |
| 3 | UI Designer | `ui` | [[./ui-designer]] | L3 (Presentation) |
| 4 | Security Engineer | `security` | [[./security-engineer]] | L1 (Security) |
| 5 | Data Engineer | `data` | [[./data-engineer]] | L0 (Infrastructure) |
| 6 | Embedded Engineer | `embedded` | [[./embedded-engineer]] | L0 (Hardware) |
| 7 | QA Engineer | `qa` | [[./qa-engineer]] | Cross-cutting |
| 8 | DevOps Engineer | `devops` | [[./devops-engineer]] | CI/CD |
| 9 | Audio HW Engineer | `audio-hw` | [[./audio-hardware-engineer]] | HW |
| 10 | DSP Firmware Engineer | `dsp-fw` | [[./dsp-firmware-engineer]] | FW |
| 11 | Windows SW Engineer | `win-sw` | [[./windows-software-engineer]] | PLAT |

---

## 3. Agent → Stack Eşleştirme

| Agent | Teknoloji | Durum |
|-------|-----------|-------|
| Backend Architect | PHP 8.4, PSR, php-di, fast-route | IMPLEMENTED |
| Security Engineer | Middleware ×4 (PSR-15), Argon2id, AES-256-GCM | IMPLEMENTED |
| Data Engineer | MySQL 9, PDO, BCNF (18 DB, 156 tablo) | IMPLEMENTED (şema) |
| QA Engineer | PHPUnit ^11.0, Vitest, Playwright | IMPLEMENTED |
| UI Designer | Vanilla JS ES6+, ITCSS 9-layer, BEM | PLANNED (spec) |
| Embedded Engineer | C++20, JUCE 9, ASIO SDK 2.3.4 | PLANNED |
| DevOps Engineer | GitHub Actions, Docker, GitLeaks | PLANNED |
| Audio HW Engineer | PCM3168A, AK4458, Class AB | PLANNED |
| DSP Firmware Engineer | XMOS XU316, I2S, TDM | PLANNED |
| Windows SW Engineer | WASAPI, COM, WinRT, WDK | PLANNED |

---

## 4. Domain Sınırları Özeti

| Agent | İzinli Dosyalar | Yasak Dosyalar |
|-------|-----------------|----------------|
| Backend | `*.php`, `shared/src/` | `*.js`, `*.css`, `*.sql` |
| UI | `*.js`, `*.css`, HTML | `*.php`, `*.sql`, `*.cpp` |
| Security | Security middleware, `.env` | Backend logic, Frontend |
| Data | `*.sql`, migration | `*.php`, `*.js`, `*.cpp` |
| Embedded | `*.cpp`, `*.h` | `*.php`, `*.js`, `*.css` |
| QA | `tests/` | `src/` production |
| DevOps | `*.yml`, `Dockerfile` | `*.php`, `*.js`, `*.css` |

---

## 5. Kalite Standartları Özeti

| Agent | Min Coverage | Hedef Coverage | Kritik Kural |
|-------|-------------|----------------|--------------|
| Backend | ≥80% | ≥90% | strict_types %100 |
| UI | ≥80% | ≥90% | Framework %0 |
| Security | ≥80% | ≥90% | OWASP %100 |
| Data | ≥80% | ≥90% | BCNF %100 |
| Embedded | ≥80% | ≥90% | Zero-alloc %100 |
| QA | ≥80% | ≥90% | Flaky test %0 |
| DevOps | — | — | CI/CD success ≥95% |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
