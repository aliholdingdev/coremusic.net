---
title: "K17-devops/artifact-management — Subdomain Kaydı"
node_id: K17-devops/artifact-management
node_type: subdomain
status: draft
parent_id: K17-devops
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K17-devops      # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "ci.yml: actions/cache@v4 (composer cache) + npm ci — var"
  - "# upload-artifact adımı YOK (ci.yml okundu 2026-10-12)"
validation:
  - "dosya okuma — kısmi/negatif kanıt"
open_questions:
  - "artifact saklama politikası var mı? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K17-devops/artifact-management

## 1. Rol (ELI15)

Paket/artifact saklama ve paylaşma: ne nerede tutulur.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K17-devops (kodun yolcuğu) altındadır.
- Testlerin tasarımı K18-testing'dedir; burada yalnız CI entegrasyonu (koşma) konuşulur.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K17-devops) · master: devops-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
