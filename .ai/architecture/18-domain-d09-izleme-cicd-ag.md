---
title: "18-domain-d09-izleme-cicd-ag — Mimari Domain Tablosu d09"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d09 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d09
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 18-domain-d09-izleme-cicd-ag — Domain d09: İzleme, CI/CD & Ağ (K400–K449)

> **Kapsam:** PSR-3 loglama + 5 log akışı, CI/secret-scanning, test altyapısı, ağ protokolleri hedefleri.
> **Eski dizin karşılıkları:** [[architecture/k12-izleme]] · [[architecture/k13-cicd]] · [[architecture/k14-ag]] · **Legacy K12 + K13 + K14** → bu domain.
> **Gerçeklik notu (2026-10-07):** Logger (FileHandler PSR-3, P5 2026-10-05/07) ve 5 log dosyası **gerçek**. Prometheus/Grafana/Sentry **YOK** → PLANNED. Ağ protokolleri (HTTP/3, WebRTC, DLNA …) kod kanıtı yok → PLANNED.

## Katman Tablosu (K400–K449 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K400 | FileHandler (PSR-3 logger) | IMPLEMENTED | shared/src/Log/FileHandler.php · .ai/log.md (P5, 2026-10-07) |
| K401 | LoggerFactory | IMPLEMENTED | shared/src/Log/LoggerFactory.php |
| K402 | Redactor (e-posta maskesi) | IMPLEMENTED | shared/src/Log/Redactor.php |
| K403 | StructuredLogger (PageRouter trace) | IMPLEMENTED | shared/src/PageRouter/StructuredLogger.php |
| K404 | Log akışı — errors | IMPLEMENTED | coremusic_php_errors.log (kök) |
| K405 | Log akışı — warnings | IMPLEMENTED | coremusic_php_warnings.log (kök) |
| K406 | Log akışı — info | IMPLEMENTED | coremusic_php_info.log (kök) |
| K407 | Log akışı — kernel_debug (traceId) | IMPLEMENTED | coremusic_php_kernel_debug.log (kök) |
| K408 | Log akışı — security (securityEvent) | IMPLEMENTED | coremusic_php_security.log (kök) |
| K409 | CI workflow (GitHub Actions) | IMPLEMENTED | .github/workflows/ci.yml |
| K410 | Secret scanning workflow | IMPLEMENTED | .github/workflows/secret-scan.yml · .gitleaks.toml |
| K411 | Vault workflow betikleri (12 dosya) | IMPLEMENTED | .workflows/ (session-init, vault-sync, adr-creation … — ls 2026-10-07) |
| K412 | PHPUnit altyapısı (4 proje) | IMPLEMENTED | shared/phpunit.xml · auth/home/api phpunit.xml · shared/composer.json (phpunit ^10.5) |
| K413 | PHPStan (static analysis) | IMPLEMENTED | shared/composer.json (phpstan ^1.10) · .ai/log.md (P5: "PHPStan OK") |
| K414 | Test dosyaları (shared 44 + panel 18) | IMPLEMENTED | shared/tests/ (44) · auth/home/api tests/ (18 — find 2026-10-07) |
| K415 | Playwright (E2E bağımlılığı) | PARTIAL | package.json (`playwright ^1.62.1`) · ⚠️ test senaryosu kanıtı yok |
| K416 | Audit trail (DB) | IMPLEMENTED | .ai/.sql/mysql/coremusic_logs.sql |
| K417 | Prometheus metrics | PLANNED | ⚠️ VERIFICATION REQUIRED — dosya/konfigürasyon yok |
| K418 | Grafana dashboard | PLANNED | ⚠️ VERIFICATION REQUIRED — yok |
| K419 | Sentry error tracking | PLANNED | ⚠️ VERIFICATION REQUIRED — yok |
| K420 | Matomo analytics | PLANNED | ⚠️ VERIFICATION REQUIRED — yok |
| K421 | HTTP/3 | PLANNED | .claude/CLAUDE.md §5 (K14) · ⚠️ kod yok |
| K422 | WebRTC P2P | PLANNED | .claude/CLAUDE.md §5 (K14) · ⚠️ kod yok |
| K423 | DLNA / UPnP | PLANNED | .claude/CLAUDE.md §5 (K14) · ⚠️ kod yok |
| K424 | AirPlay | PLANNED | .claude/CLAUDE.md §5 (K14) · ⚠️ kod yok |
| K425 | mDNS / service discovery (ağ) | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok |
| K426 | Backup / DR (Restic) | PLANNED | .claude/CLAUDE.md §12 (hedef) · ⚠️ kod yok |
| K427 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K428 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K429 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K430 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K431 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K432 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K433 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K434 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K435 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K436 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K437 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K438 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K439 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K440 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K441 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K442 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K443 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K444 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K445 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K446 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K447 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K448 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K449 | Rezerve — d09 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K400–K449) · IMPLEMENTED 16 · PARTIAL 1 · PLANNED 33 (2026-10-07 disk ölçümü).

**İlgili kayıt:** .ai/log.md (P5 enterprise logger, 2026-10-07) · .gitleaks.toml · ADR-006 (performance target — .ai/.decisions/accepted/ADR-006-performance-targets.md)
