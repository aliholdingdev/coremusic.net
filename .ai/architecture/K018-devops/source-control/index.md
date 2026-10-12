---
title: "K018-devops/source-control — Subdomain Kaydı"
node_id: K018-devops/source-control
node_type: subdomain
status: draft
parent_id: K018-devops
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K018-devops      # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# .git/HEAD glob = 0 isabet (2026-10-12) + env: Is directory a git repo: no → DEPO YOK"
  - "vault tarihsel commit kayıtları: .ai/AGENTS.md §25.5 (eski dönem arşivi — bağımsız doğrulanmadı)"
validation:
  - "glob ölçümü 2026-10-12 — KRİTİK BULGU: depo yok"
open_questions:
  - "Git deposu bilinçli mi yok? (taşınma/yedek dönemi?) — K-Space V3 kararı gerekli (⚠️ VERIFICATION REQUIRED)"
  - "depo yokken rollback/release children'ları nasıl tescillenecek?"
---

# K018-devops/source-control

## 1. Rol (ELI15)

Depo kuralları: branch yapısı, commit hijyeni, koruma mekanizmaları.

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
