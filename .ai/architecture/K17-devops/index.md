---
title: "K17-devops — Domain Kaydı (K-Space V3)"
node_id: K17-devops
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
  - K18-testing    # CI'da koşulan test kapısı — kanıtlı (ci.yml php-test/js-test job'ları)
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
updated: 2026-10-18
---


# K17-devops — Kodun Üretilip Yayılma Yolu (Domain)

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K17-devops |
| **K Adı** | DevOps (kodun üretilip yayılma yolu) |
| **Kapsam** | Kodun depoya girdiği andan kullanıcının ekranına çıktığı ana kadarki yol: depo, derleme, CI kapısı, test koşumu, ortamlar, yayın, geri alma (rollback) ve sürüm yönetimi. Testlerin kendisi K18-testing'dedir; K17-devops onları koşar. Kanıt yüzeyi: `.github/workflows/` = 2 dosya (ci.yml 6 job · secret-scan.yml gitleaks). 10 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K009-security · K18-testing (kanıt: FM depends_on — secret-scan.yml GitLeaks job · ci.yml php-test/js-test job'ları) |
| **Durum** | draft (10 kademe-2 çocuk · CI'nin gerçekten geçtiği doğrulanmadı ⚠️) |
| **Son Güncelleme** | 2026-10-12 (ci.yml + secret-scan.yml dosya okuma + glob ölçümü) |

> **ELI15:** Bu domain, kodun depoya girdiği andan kullanıcının ekranına çıktığı ana kadarki yoldur:
> depo, derleme, CI kapısı, test koşumu, ortamlar, yayın, geri alma (rollback) ve sürüm yönetimi.

## 1. Kapsam Sınırı (kısa)

- **Burada:** YOL (pipeline, ortam, yayın). Testlerin **kendisi** K18-testing'dedir; K018 onları **koşar**.
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

## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K17-devops | [[K17-devops]] | ./K17-devops/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | iki yönlü atıf | M→3 · M←2 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | → | bağımlı (depends_on) | M→11 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | → | bu K referanslıyor | M→3 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | → | bağımlı (depends_on) | M→14 · M←0 (kök .md, 2026-10-18) |

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
