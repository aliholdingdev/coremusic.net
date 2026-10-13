---
title: "K018-devops — Domain Kaydı (K-Space V3)"
node_id: K018-devops
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - source-control
  - build
  - ci
  - cd
  - environments
  - deployment
  - rollback
  - release-management
  - artifact-management
  - infrastructure-testing
depends_on:
  - K009-security   # secret-scan / güvenlik kapısı — kanıtlı (secret-scan.yml GitLeaks job)
  - K019-testing    # CI'da koşulan test kapısı — kanıtlı (ci.yml php-test/js-test job'ları)
evidence:
  - ".github/workflows/ = 2 dosya (ci.yml · secret-scan.yml) — glob 2026-10-12 (önceki ölçüm: .ai/AGENTS.md §25.2, 2026-09-27 — tutarlı)"
  - "ci.yml = 6 job: php-lint · php-test (matrix: shared, auth.coremusic.net --testsuite Unit) · composer-audit · js-test (vitest) · js-coverage (soft/A7) · js-e2e (Playwright, soft/A6) — dosya okundu 2026-10-12"
  - "secret-scan.yml = gitleaks job (gitleaks/gitleaks-action@v2, fetch-depth: 0; .gitleaks.toml YOK) — dosya okundu 2026-10-12"
  - ".github/ = 8 dosya (AGENTS.md · CONTEXT.md · CLAUDE.md · WORKFLOW.md · workflows×2 · ISSUE_TEMPLATE×2) — glob 2026-10-12"
validation:
  - "dosya okuma (ci.yml · secret-scan.yml) + glob ölçümü 2026-10-12"
  - "v2 spec §2.18 çocuk listesi birebir alındı (2026-10-12)"
open_questions:
  - "CI'nin gerçekten GEÇTİĞİ doğrulanmadı (yalnız workflow dosyaları var — .ai/AGENTS.md §25.2 uyarısı) (⚠️ VERIFICATION REQUIRED)"
  - "git deposu YOK: .git/HEAD glob = 0 isabet (2026-10-12; env: Is directory a git repo: no) → source-control/ için ne tescillenecek? (⚠️ VERIFICATION REQUIRED)"
  - "ci.yml yorumlarındaki plan referansı .ai/architecture/k13-cicd/ diskte YOK — eski plan klasörü V3 rebuild'de kaldırıldı mı? (⚠️ VERIFICATION REQUIRED)"
  - "cd · environments · deployment · rollback · release-management · artifact-management · infrastructure-testing için disk kanıtı YOK (deploy job'ı workflows'da yok) — kanıt bekliyor"
group: mimari
---


# K018-devops — Kodun Üretilip Yayılma Yolu (Domain)

> **ELI15:** Bu domain, kodun depoya girdiği andan kullanıcının ekranına çıktığı ana kadarki yoldur:
> depo, derleme, CI kapısı, test koşumu, ortamlar, yayın, geri alma (rollback) ve sürüm yönetimi.

## 1. Kapsam Sınırı (kısa)

- **Burada:** YOL (pipeline, ortam, yayın). Testlerin **kendisi** K019-testing'dedir; K018 onları **koşar**.
- **DEĞİL:** ajan boot/orchestration (K016) · log/metrik/alert (K017 — entegrasyon var, sınır: uyarı K017, aksiyon K018) · güvenlik politikası (K009 — secret-scan iş birliği kanıtlı).

## 2. Kademe-2 Çocuk Kataloğu (v2 §2.18 — birebir)

| # | Çocuk | Ne işe yarar (ELI15) | Disk kanıtı (2026-10-12) |
|---|---|---|---|
| 1 | source-control/ | Depo kuralları, branch, commit hijyeni | ⚠️ .git/HEAD = 0 isabet → depo YOK (kayıt: §7 açık soru 2) |
| 2 | build/ | Derleme/adım çalıştırma (composer, npm) | ✅ ci.yml composer install ×3 + npm ci · 4 composer.json |
| 3 | ci/ | Push/PR'da otomatik kapı | ✅ ci.yml (6 job, okundu) |
| 4 | cd/ | Yayına sürme otomasyonu | ⚠️ workflows'da deploy job'ı YOK (kanıtlı negatif) |
| 5 | environments/ | Ortam tanımları (dev/staging/prod) | ⚠️ yml'lerde environment: bloğu YOK (kanıtlı negatif) |
| 6 | deployment/ | Yayın mekaniği ve onay akışı | 🔶 .workflows/deployment.md referansı (.github/AGENTS.md §7) — varlık doğrulanmadı |
| 7 | rollback/ | Geri alma planı | ⚠️ kanıt bekliyor |
| 8 | release-management/ | Sürüm/not yönetimi | ⚠️ kanıt bekliyor |
| 9 | artifact-management/ | Paket/artifact saklama | 🔶 kısmi: composer cache + npm ci var; upload-artifact adımı YOK (ci.yml okundu) |
| 10 | infrastructure-testing/ | Altyapı testi | ⚠️ kanıt bekliyor |

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5) |
| Kanıt dengesi | 2/10 çocuk doğrudan kanıtlı (build · ci) + 2 kısmi/negatif (environments · artifact) + 1 uyaran (source-control: depo yok) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Bağlı dosyalar | devops-master.md · devops-bagimliliklar.md · devops-dogrulama.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `devops-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `devops-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `devops-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
