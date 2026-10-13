---
title: "K017-observability/logging — Subdomain Kaydı"
node_id: K017-observability/logging
node_type: subdomain
status: draft
parent_id: K017-observability
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K017-observability   # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "shared/src/Log/LoggerFactory.php · FileHandler.php · Redactor.php — glob 2026-10-12"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · LoggerRedactionTest.php — glob 2026-10-12"
validation:
  - "dosya varlıkları glob ile doğrulandı 2026-10-12"
open_questions:
  - "PSR-3 uyumu test içeriği OKUNMADI — doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
  - "redaction davranışının kendisi test edilmedi, yalnız dosya var (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K017-observability/logging

## 1. Rol (ELI15)

Logların yazımı, formatı ve redaction'ı: ne loglanır, nasıl loglanır, hassas veri nasıl maskelenir.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K017-observability (sistem gözetimi) altındadır.
- Test stratejisi (K019) ve deploy kapısı (K018) kapsamı DEĞİLDİR — yalnız entegrasyon kuralları bağlayıcıdır.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain Kökü | [[index]] (K017-observability) · master: observability-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
