---
title: "10-domain-d01-isletim-sistemi-platform — Mimari Domain Tablosu d01"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d01 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d01
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 10-domain-d01-isletim-sistemi-platform — Domain d01: İşletim Sistemi & Platform (K000–K049)

> **Kapsam:** Platform katmanları (Tier 1-5), PHP 8.4 çalışma zamanı, yapılandırma/bootstrap, CI/CI altyapısının OS tarafı, container/hedef platformlar.
> **Eski dizin karşılığı:** [[architecture/k0-isletim-sistemi]] (geriye dönük uyum) · **Legacy K0** → bu domain.
> **Durum efsanesi:** 00-master-index §Durum Efsanesi (IMPLEMENTED / PARTIAL / PLANNED / DESIGN / DEPRECATED).
> **Kural:** Her satırın kanıtı diskte doğrulanmıştır; doğrulanamayan satır `⚠️ VERIFICATION REQUIRED` taşır (Zero-Hallucination).

## Katman Tablosu (K000–K049 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K000 | Windows platform katmanı (Tier 1 — ana geliştirme) | IMPLEMENTED | .claude/CLAUDE.md §13 |
| K001 | Linux platform katmanı (Tier 2) | PLANNED | .claude/CLAUDE.md §13 (hedef; çalıştırma kanıtı yok) |
| K002 | macOS platform katmanı (Tier 3) | PLANNED | .claude/CLAUDE.md §13 (hedef; çalıştırma kanıtı yok) |
| K003 | Raspberry Pi / ARM64 (Tier 4) | PLANNED | .claude/CLAUDE.md §13 (hedef; çalıştırma kanıtı yok) |
| K004 | ReactOS (Tier 5 — experimental) | PLANNED | .claude/CLAUDE.md §13 |
| K005 | PHP 8.4 çalışma zamanı | IMPLEMENTED | shared/composer.json (`"php": ">=8.4"`) |
| K006 | Apache yapılandırması (.htaccess) | IMPLEMENTED | auth.coremusic.net/.htaccess · assets.coremusic.net/.htaccess · home.coremusic.net/.htaccess |
| K007 | Ortam yapılandırması (.env) | IMPLEMENTED | ADR-015 · auth.coremusic.net/config/.env.example |
| K008 | Bootstrap / DI (php-di) | IMPLEMENTED | shared/src/Bootstrap/ · shared/composer.json (php-di/php-di ^7.0) |
| K009 | APCu uzantısı (zorunlu bağımlılık) | IMPLEMENTED | shared/composer.json (`"ext-apcu": "*"`) |
| K010 | PSR-7 istek/yanıt (nyholm/psr7) | IMPLEMENTED | shared/composer.json · shared/src/Api/ApiRequest.php |
| K011 | CI — GitHub Actions | IMPLEMENTED | .github/workflows/ci.yml |
| K012 | Secret scanning (gitleaks) | IMPLEMENTED | .gitleaks.toml · .github/workflows/secret-scan.yml |
| K013 | Vault validator (Control Plane) | IMPLEMENTED | .ai/scripts/validate.mjs |
| K014 | Vault script seti (yardımcı betikler) | IMPLEMENTED | .ai/scripts/ (10 dosya — 2026-10-07 ls) |
| K015 | Otomatik context yükleme (instructions[]) | IMPLEMENTED | .claude/settings.json · .opencode/opencode.json |
| K016 | Container (Docker) | PLANNED | ⚠️ VERIFICATION REQUIRED — Dockerfile diskte YOK (glob 2026-10-07) |
| K017 | Kubernetes orkestrasyon | PLANNED | ⚠️ VERIFICATION REQUIRED — dosya yok |
| K018 | Deployment pipeline (uygulama) | PLANNED | .workflows/deployment.md (yalnız doküman; uygulama kanıtı yok) |
| K019 | Cross-platform API (Win/Lin/Mac/RPi) | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok |
| K020 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K021 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K022 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K023 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K024 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K025 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K026 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K027 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K028 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K029 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K030 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K031 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K032 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K033 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K034 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K035 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K036 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K037 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K038 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K039 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K040 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K041 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K042 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K043 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K044 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K045 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K046 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K047 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K048 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K049 | Rezerve — d01 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K000–K049) · IMPLEMENTED 15 · PLANNED 35 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-015 (.env) · ADR-082 (dev environment — .ai/.decisions/accepted/ADR-082-dev-environment.md)
