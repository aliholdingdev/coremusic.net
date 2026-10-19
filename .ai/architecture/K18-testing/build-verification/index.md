---
title: "K18-testing/build-verification — Subdomain Kaydı"
node_id: K18-testing/build-verification
node_type: subdomain
status: draft
parent_id: K18-testing
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K18-testing    # domain kökü
  - K17-devops     # lint/coverage kapıları ci.yml'de — kanıtlı (php-lint L25 · js-coverage L160/L167)
evidence:
  - "ci.yml php-lint job (L25, sözdizimi) + js-coverage job (L160, continue-on-error soft/A7) — okuma 2026-10-12"
  - "phpstan ^1.10 + script `stan --level=5` — shared/composer.json L25 · L48; auth/api/home composer.json'larında YOK (grep 2026-10-12)"
  - "phpunit ^10.5 (4 composer.json: shared · auth · api · home) + phpunit.xml ×4 — grep/glob 2026-10-12"
  - "coverage hedefi ≥80% — .ai/AGENTS.md §16 (vault beyanı); gate soft → ⚠️ açık soru"
validation:
  - "grep + glob + ci.yml okuma 2026-10-12 — lint/type/coverage YÜZEYİ kanıtlı; coverage eşik GERÇEĞİ doğrulanmadı (soft gate)"
open_questions:
  - "coverage gate (continue-on-error) hard olacak mı? ≥80% bağlayıcı mı? (master §7 açık soru 1 — ⚠️ VERIFICATION REQUIRED)"
  - "phpstan yalnız shared'de — subdomainler static analysis dışında mı? (master §7 açık soru 8 — ⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K019-testing/build-verification

## 1. Rol (ELI15)

Kodun **derlenip temiz geçtiğini** kanıtlar: sözdizimi (lint), tip/statik analiz (phpstan) ve coverage eşiği — çalışan ama bozuk/tipsiz kodun kapıda yakalanması.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K18-testing altındadır.
- Build sisteminin kendisi/kurulumu ve pipeline K17-devops'tur; burada yalnız **doğrulama/kapı sertliği**.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıtlı — lint + phpstan + phpunit; coverage gate soft ⚠️) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
