---
title: "K016-observability/correlation — Subdomain Kaydı"
node_id: K016-observability/correlation
node_type: subdomain
status: draft
parent_id: K016-observability
children:
  - K016-observability/correlation/correlation-request-id-ve-korelasyon   # 2026-10-13 child doldurma
depends_on:
  - K016-observability   # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# bu turda disk kanıtı TOPLANMADI — kanıt bekliyor"
validation:
  - "okuma/gözlem YOK — verify-required"
open_questions:
  - "correlation/ için disk kanıtı sonraki turda (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-observability/correlation

## 1. Rol (ELI15)

Korelasyon kimliği (request-id): aynı isteğin tüm loglarını birbirine bağlama.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K016-observability (sistem gözetimi) altındadır.
- Test stratejisi (K019) ve deploy kapısı (K018) kapsamı DEĞİLDİR — yalnız entegrasyon kuralları bağlayıcıdır.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain Kökü | [[index]] (K016-observability) · master: observability-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
