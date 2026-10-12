---
title: "K017-observability/health-checks — Subdomain Kaydı"
node_id: K017-observability/health-checks
node_type: subdomain
status: draft
parent_id: K017-observability
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K017-observability   # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# bu turda disk kanıtı TOPLANMADI — kanıt bekliyor"
validation:
  - "okuma/gözlem YOK — verify-required"
open_questions:
  - "health-check endpoint hangi servis/rotada? UNKNOWN (⚠️ VERIFICATION REQUIRED)"
---

# K017-observability/health-checks

## 1. Rol (ELI15)

Canlılık ve sağlık uçları: sistem ayakta mı, bağımlılıklar sağlıklı mı?

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

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
