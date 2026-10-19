---
title: "K18-testing/unit — Subdomain Kaydı"
node_id: K18-testing/unit
node_type: subdomain
status: draft
parent_id: K18-testing
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K18-testing    # domain kökü
evidence:
  - "shared/tests/Unit = 15 *Test.php — glob 2026-10-12 (Theme 1 · Log 2 · Device 3 · Security 2 · PageRouter 6 · Config 1)"
  - "auth.coremusic.net/tests/Unit = 6 · api.coremusic.net/tests/Unit = 3 · home.coremusic.net/tests/Unit = 6 — glob 2026-10-12 (toplam alt alan Unit 15)"
  - "runner: phpunit ^10.5 (4 composer.json) + vitest ^5.0.3 (assets.coremusic.net 9 .spec.js + 1 .spec.ts) — grep 2026-10-12"
validation:
  - "glob + grep ölçümü 2026-10-12 — master §4.1 envanterinde home.coremusic.net/tests görünmüyor (çelişki: testing-dogrulama.md §3-ç1)"
open_questions:
  - "media.coremusic.net neden Unit test'e sahip değil (tests/ = 0)? kapsam dışı mı? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K019-testing/unit

## 1. Rol (ELI15)

Tek bir birimin (sınıf/metot) izole davranışı — dış bağımlılıklar mock/fixture ile.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K18-testing (neyi nasıl ölçtüğümüz) altındadır.
- Testlerin CI'da koşumu K17-devops'tur; burada yalnız senaryo/ölçüm konuşulur.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da — en zengin kanıtlı çocuk) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
