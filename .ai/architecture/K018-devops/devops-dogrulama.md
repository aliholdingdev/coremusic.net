---
title: "K018-devops — Doğrulama"
node_id: K018-devops
node_type: domain-validation
status: draft
updated: 2026-10-12
version: 1.0.0
---

# K018-devops — devops-dogrulama.md

## §1 Amaç

K018 iddialarını dosya okuma + glob ölçümüyle doğrular. Kanıt yoksa UNKNOWN / ⚠️ VERIFICATION REQUIRED.

## §2 Ölçütler → Durum

| # | Ölçüt | Kanıt (gerçek yol / satır) | Durum |
|---|---|---|---|
| 1 | Workflow dosyaları mevcut mu? | .github/workflows/ = ci.yml + secret-scan.yml (glob 2026-10-12) | ✅ verified |
| 2 | CI lint kapısı var mı? | ci.yml php-lint job (PHP 8.4, composer stan: phpstan --level=5) | ✅ verified (dosya) |
| 3 | CI test kapısı var mı? | ci.yml php-test job (matrix: shared · auth --testsuite Unit) + js-test (vitest) | ✅ verified (dosya) |
| 4 | Audit kapısı var mı? | ci.yml composer-audit job (shared · auth · home) | ✅ verified (dosya) |
| 5 | E2E kapısı var mı? | ci.yml js-e2e job (Playwright chromium, continue-on-error, A6) | ✅ verified (dosya) |
| 6 | Secret taraması var mı? | secret-scan.yml gitleaks job (gitleaks-action@v2, fetch-depth: 0) | ✅ verified (dosya) |
| 7 | Şablon zinciri tutarlı mı? | ci.yml satır 2 → .ai/.templates/infrastructure/github-actions-template.md §3.2 (varlık doğrulandı) | ✅ verified |
| 8 | CI GERÇEKTEN geçiyor mu? | GitHub run kanıtı YOK (diskte değil); vault §25.2: "geçtiği doğrulanmadı" | ⚠️ verify-required |
| 9 | Git deposu var mı? | .git/HEAD glob = 0 isabet (2026-10-12) + env "Is directory a git repo: no" | ❌ verified NEGATIF (depo yok) |
| 10 | cd/deploy job'ı var mı? | 2 workflow dosyasının tamamı okundu; deploy job'ı YOK | ❌ verified NEGATIF |
| 11 | environment: bloğu var mı? | on: push/pull_request only — environment tanımı YOK | ❌ verified NEGATIF |
| 12 | upload-artifact adımı var mı? | ci.yml'de upload-artifact YOK (cache var) | ❌ verified NEGATIF |
| 13 | rollback/release/infra-test kanıtı? | ölçüm YAPILMADI (dosya yok) | ⚠️ verify-required |

## §3 Doğrulanmayan Yüzeyler (kanıt bekliyor — sonraki tur)

GitHub arayüz kanıtları (run geçmişi · branch protection · environments/secrets) · rollback/ · release-management/ · infrastructure-testing/ · deployment/ (doküman okuması).

## §4 Web-Kanıt Kayıtları

> **Bu turda web araştırması YAPILMADI** (A4 payı disk-öncelikli kanıt planı). Tablo iskelettir.

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| — | (boş — kanıt bekliyor) | — | — | ⏳ |

**Bekleyen web-iddiaları (sonraki tur):**

1. gitleaks-action@v2 güncel sürümü ve config best-practice (artifact-management/secret-scan revizyonu için).
2. GitHub Actions sürüm pinleme güvenlik önerileri (major-pin vs SHA-pin).
3. Node 26 / PHP 8.4 LTS destek durumu (toolchain kararları için).

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru |
|---|---|
| 1 | Son CI run'ı geçti mi? (arayüz kanıtı gerekli) |
| 2 | Git deposu yokluğu bilinçli karar mı? |
| 3 | .gitleaks.toml Security onayıyla eklenecek mi? |
| 4 | k13-cicd planı arşiv mi, taşınacak mı? |
| 5 | Deployment onayı .workflows/deployment.md ile mi yürüyor? (okunacak) |

## §6 Kapanış Kuralı

Domain verified olmak için §2'deki ⚠️ satırları kapatmalı; ❌ satırlar (depo yok, cd yok) ise
**karar gerektirir** — ADR ile ya tescillenir ya kapatılır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 · Mode: Truth Mode
