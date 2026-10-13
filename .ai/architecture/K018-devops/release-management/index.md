---
title: "K018-devops/release-management — Subdomain Kaydı"
node_id: K018-devops/release-management
node_type: subdomain
status: draft
parent_id: K018-devops
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K018-devops      # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# bu turda disk kanıtı YOK (CHANGELOG/sürüm dosyası ölçülmedi)"
validation:
  - "okuma/gözlem YOK — verify-required"
open_questions:
  - "sürümleme stratejisi (semver?) ve release notu süreci tanımlı mı? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K018-devops/release-management

## 1. Rol (ELI15)

Sürüm ve release notu yönetimi: ne, ne zaman, hangi sürümle çıktı.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K018-devops (kodun yolcuğu) altındadır.
- Testlerin tasarımı K019-testing'dedir; burada yalnız CI entegrasyonu (koşma) konuşulur.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K018-devops) · master: devops-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
