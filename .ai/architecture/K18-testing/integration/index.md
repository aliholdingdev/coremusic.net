---
title: "K18-testing/integration — Subdomain Kaydı"
node_id: K18-testing/integration
node_type: subdomain
status: draft
parent_id: K18-testing
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K18-testing    # domain kökü
  - K007-data       # DB/Repository katmanına dokunur — kanıtlı (tests/Repository ×2)
  - K009-security   # middleware entegrasyonu güvenlik yüzeyini yoklar — kanıtlı (tests/Middleware ×9)
evidence:
  - "shared/tests/Api = 8 · Middleware = 9 · Repository = 2 · Events = 2 — glob 2026-10-12 (master §2.1 orta kat ile tutarlı)"
  - "ci.yml php-test job: matrix shared · auth --testsuite Unit (L66–L80) — entegrasyon testleri CI'da ayrı koşturulmuyor, Unit suite içinde (⚠️ kısmi)"
validation:
  - "glob 2026-10-12 — entegrasyon testleri var; CI'daki suite ayrımı doğrulanmadı"
open_questions:
  - "ci.yml php-test yalnız --testsuite Unit koşuyor — Api/Middleware/Repository testleri hangi suite'te ve CI'da koşturuluyor mu? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K019-testing/integration

## 1. Rol (ELI15)

Birimler arası / DB / middleware'in **birlikte** davranışı (tek başına doğru olanın bir arada da doğru olması).

## 2. Kapsam Sınırı

- Bu düğüm yalnız K18-testing altındadır.
- Repository'nin kendisi K007-data'nın işi; burada yalnız onun **sınanması**.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıtlı — 21 dosya; CI suite ayrımı ⚠️ açık soru) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
