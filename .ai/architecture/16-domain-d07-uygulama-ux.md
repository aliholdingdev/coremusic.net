---
title: "16-domain-d07-uygulama-ux — Mimari Domain Tablosu d07"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d07 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d07
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 16-domain-d07-uygulama-ux — Domain d07: Uygulama & Kullanıcı Deneyimi (K300–K349)

> **Kapsam:** Panel/uygulama katmanı (PHP sayfalar + SPA JS), ITCSS frontend, tema/view-mode, mockup gate.
> **Eski dizin karşılıkları:** [[architecture/k10-uygulama]] · [[architecture/k11-ux]] · **Legacy K10 + K11** → bu domain.
> **Gerçeklik notu (2026-10-07):** Fiziksel paneller = **auth · home · assets (statik) · media (CLI)**. `music/admin/download/car/studio/pro/landing` dizinleri **diskte YOK** (glob: `*/` kök listesi) → PLANNED. JS (vendor hariç) = 94 dosya; ITCSS = **11 dizin** (01_Abstracts … 11_OAuth).

## Katman Tablosu (K300–K349 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K300 | Home panel (ana ekran) | IMPLEMENTED | home.coremusic.net/index.php · home.coremusic.net/pages/home.php |
| K301 | Auth panel (login/logout/forgot) | IMPLEMENTED | auth.coremusic.net/pages/{login,logout,forgot-password}.php |
| K302 | Panel bileşen altyapısı (PHP) | IMPLEMENTED | home.coremusic.net/include/ (Auth, Class, Component, Container, Interfaces, Repository, Session, Stream) |
| K303 | Health check sayfası | IMPLEMENTED | home.coremusic.net/pages/health.php |
| K304 | Assets statik servisi | IMPLEMENTED | assets.coremusic.net/.htaccess · assets.coremusic.net/Css/ · /js/ · /Fonts/ |
| K305 | SPA router çekirdeği | IMPLEMENTED | assets.coremusic.net/js/router/ (23 dosya) · ADR-021 |
| K306 | Router config (route/headers/signal) | IMPLEMENTED | assets.coremusic.net/js/router/config/ (5 dosya) |
| K307 | ContentFetcher + AuthBoundaryDetector | IMPLEMENTED | assets.coremusic.net/js/router/ContentFetcher.js · assets.coremusic.net/js/router/AuthBoundaryDetector.js |
| K308 | CoreMusicApp bootstrap | IMPLEMENTED | assets.coremusic.net/js/core/CoreMusicApp.js |
| K309 | Component sistemi (36 dosya) | IMPLEMENTED | assets.coremusic.net/js/components/ (primitives 12 · composites 13 · interactive 6 · base 5) |
| K310 | Features modülleri | IMPLEMENTED | assets.coremusic.net/js/features/ (6 dosya) |
| K311 | Core JS (footer init vb.) | IMPLEMENTED | assets.coremusic.net/js/core/ (4 dosya) |
| K312 | Auth JS | IMPLEMENTED | assets.coremusic.net/js/auth/ (2 dosya) |
| K313 | Frontend testleri (mocks + component) | IMPLEMENTED | assets.coremusic.net/tests/ (2 dosya) |
| K314 | ITCSS 11 katman CSS | IMPLEMENTED | assets.coremusic.net/Css/ (01_Abstracts … 11_OAuth — ls 2026-10-07) |
| K315 | Design tokens (a-*-token*.css) | IMPLEMENTED | assets.coremusic.net/Css/01_Abstracts/a-design-tokens.css · a-layout-tokens-{mobile,1024,1920,3540,3840}.css |
| K316 | Auth bundled CSS | IMPLEMENTED | assets.coremusic.net/Css/auth-bundled.css |
| K317 | Device CSS override katmanı | IMPLEMENTED | assets.coremusic.net/Css/08_Devices/ (dizin) |
| K318 | ViewMode CSS katmanı | IMPLEMENTED | assets.coremusic.net/Css/09_ViewModes/ (dizin) |
| K319 | Font varlıkları | IMPLEMENTED | assets.coremusic.net/Fonts/ (dizin — ls 2026-10-07) |
| K320 | ViewModeManager (server) | IMPLEMENTED | shared/src/ViewMode/ViewModeManager.php · ADR-093 |
| K321 | ThemeManager (server) | IMPLEMENTED | shared/src/Theme/ThemeManager.php · ADR-044 |
| K322 | ThemeManager (client, anlık geçiş) | IMPLEMENTED | assets.coremusic.net/js/managers/ThemeManager.js · ADR-044 |
| K323 | View-mode tek yükleme yolu | IMPLEMENTED | ADR-093 (.ai/.decisions/accepted/ADR-093-view-modes-single-load-path.md) |
| K324 | Mockup-before-frontend gate (doküman) | IMPLEMENTED | .ai/ui-design/01-mockup-index.md · .ai/ui-design/02-component-inventory.md |
| K325 | Responsive mimari dokümanı | IMPLEMENTED | .ai/ui-design/05-responsive-architecture.md (CLAUDE §7.1 referansı) |
| K326 | Cihaz matrisi (45-tier) | IMPLEMENTED | .ai/ui-design/01-mockup-index.md · .ai/scripts/device-matrix-catid.ps1 |
| K327 | Music panel | PLANNED | ⚠️ VERIFICATION REQUIRED — music.coremusic.net dizini yok (2026-10-07) |
| K328 | Admin panel | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok |
| K329 | Download panel | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok |
| K330 | Car panel | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok |
| K331 | Studio panel | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok |
| K332 | Pro panel | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok |
| K333 | Landing panel | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok |
| K334 | Media panel (web görünümü) | PLANNED | ⚠️ VERIFICATION REQUIRED — media.coremusic.net yalnız CLI/src |
| K335 | PWA (manifest / service worker) | PLANNED | ⚠️ VERIFICATION REQUIRED — manifest/sw dosyası yok |
| K336 | WCAG 2.2 AA doğrulaması | PLANNED | .claude/CLAUDE.md §5 (K11 hedefi) · ⚠️ denetim kanıtı yok |
| K337 | View Transition API | PLANNED | ADR-048 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K338 | Multi-domain view mode | PLANNED | ADR-045 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K339 | Cross-view state preservation | PLANNED | ADR-046 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K340 | Login redirect session bridge | PLANNED | ADR-047 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K341 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K342 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K343 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K344 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K345 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K346 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K347 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K348 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K349 | Rezerve — d07 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K300–K349) · IMPLEMENTED 27 · PLANNED 23 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-001 · ADR-004 · ADR-021 · ADR-044 · ADR-045 · ADR-046 · ADR-047 · ADR-048 · ADR-093 (`.ai/.decisions/accepted/`)
