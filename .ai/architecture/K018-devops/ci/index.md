---
title: "K018-devops/ci — Subdomain Kaydı"
node_id: K018-devops/ci
node_type: subdomain
status: draft
parent_id: K018-devops
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K018-devops      # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".github/workflows/ci.yml = 6 job (php-lint · php-test · composer-audit · js-test · js-coverage · js-e2e) — okundu 2026-10-12"
  - "şablon: .ai/.templates/infrastructure/github-actions-template.md §3.2 (varlık doğrulandı)"
validation:
  - "dosya okuma 2026-10-12 — CI'nin GEÇTİĞİ doğrulanmadı (vault §25.2 uyarısı)"
open_questions:
  - "son CI run'ı geçti mi? (GitHub arayüz kanıtı gerekli — ⚠️ VERIFICATION REQUIRED)"
  - "ci.yml'deki .ai/architecture/k13-cicd/ referansı diskte YOK — tarihsel referans, temizlenecek mi?"
group: mimari
---


# K018-devops/ci

## 1. Rol (ELI15)

Push/PR'da otomatik kapı: lint, test, audit, e2e koşumu.

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
