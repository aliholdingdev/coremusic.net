---
title: "K016-AI/agents — Subdomain Kaydı"
node_id: K016-AI/agents
node_type: subdomain
status: draft
parent_id: K016-AI
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K016-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/.agents/ = 12 .md (11 uzman profili + AGENTS.md indeksi) — glob 2026-10-12"
  - ".claude/agent/ dizini — profil .md'leri mevcut (tam sayı UNKNOWN, liste 100 sonuçta kırptı) — glob 2026-10-12"
validation:
  - "disk sayımı 2026-10-12"
open_questions:
  - ".claude/agent/ TAM profil sayısı (UNKNOWN)"
  - "11 proje ajanı ile 118 global registry ajanının eşleme tablosu vault'ta yok mu? (⚠️ VERIFICATION REQUIRED)"
---

# K016-AI/agents

## 1. Rol (ELI15)

Ajanların kimliği: hangi ajan var, hangi alanda uzman, hangi dosyaya dokunabilir.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K016-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K016-AI) · master: ai-master.md |

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
