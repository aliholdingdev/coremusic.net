---
title: "K17-devops/deployment — Subdomain Kaydı"
node_id: K17-devops/deployment
node_type: subdomain
status: draft
parent_id: K17-devops
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K17-devops      # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# .github/AGENTS.md §7 → ../.workflows/deployment.md referansı var — dosyanın kendisi bu turda OKUNMADI"
validation:
  - "doküman referansı — içerik doğrulanmadı"
open_questions:
  - "deployment.md okunacak; onay akışı fiilen uygulanıyor mu? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K17-devops/deployment

## 1. Rol (ELI15)

Yayın mekaniği ve onay akışı: kim, ne zaman, nasıl yayınlar.

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
