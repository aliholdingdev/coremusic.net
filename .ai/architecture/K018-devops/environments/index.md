---
title: "K018-devops/environments — Subdomain Kaydı"
node_id: K018-devops/environments
node_type: subdomain
status: draft
parent_id: K018-devops
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K018-devops      # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# yml'lerde environment: bloğu YOK (on: push/pull_request only) — okundu 2026-10-12 (kanıtlı negatif)"
validation:
  - "dosya okuma — negatif kanıt"
open_questions:
  - "GitHub arayüzünde environment tanımı var mı? (diskte değil — ⚠️ VERIFICATION REQUIRED)"
---

# K018-devops/environments

## 1. Rol (ELI15)

Ortam tanımları: dev/staging/prod ve ortam değişkenleri.

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

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
