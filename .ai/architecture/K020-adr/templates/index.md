---
node_id: K020-adr/templates
node_type: subdomain
status: draft
parent_id: K020-adr
children: []
depends_on:
  - K020-adr
evidence:
  - ".ai/.templates/adr/ (7 şablon dosyası — disk ölçümü 2026-10-12)"
  - ".ai/architecture/adr/templates/index.md (mimari seri şablon klasörü — mevcut)"
validation:
  - "2026-10-12 — .ai/.templates/adr/* envanteri alındı (7 dosya)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Mimari seri için adr-nygard şablonunun kopyası mı kullanılacak, ayrı şablon mu yazılacak? → VERIFY REQUIRED"
group: mimari
---


# K020-adr/templates — Şablon Kataloğu İskeleti

**ELI15:** ADR yazacak kişinin **hangi şablonu kopyalayacağını** gösteren raftır.
Şablonun kendisi vault'ta yaşar; bu sayfa yalnız katalogdur.

## Katalog (vault işaretçili — Guardrail #16)

| # | Şablon | Konum | Kullanım alanı | Durum |
|---|---|---|---|---|
| 1 | `adr-nygard-template.md` (v1.0.1) | `@.ai/.templates/adr/` | **varsayılan** — bağlam/karar/alternatif/sonuçlar | mevcut |
| 2 | `adr-security-template.md` | `@.ai/.templates/adr/` | güvenlik kararları (OWASP, auth, crypto) | mevcut |
| 3 | `adr-database-template.md` | `@.ai/.templates/adr/` | BCNF / migration / sorgu kararları | mevcut |
| 4 | `adr-frontend-template.md` | `@.ai/.templates/adr/` | ITCSS/token/UI kararları | mevcut |
| 5 | `adr-audio-template.md` | `@.ai/.templates/adr/` | DSP/ASIO/ses zinciri kararları | mevcut |
| 6 | `adr-template.md` | `@.ai/.templates/adr/` | genel amaçlı eski şablon | mevcut |
| 7 | `adr-index.md` | `@.ai/.templates/adr/` | ADR indeks şablonu | mevcut |
| M | mimari seri şablonu | `[[../../adr/index|architecture/adr/templates/]]` | mimari seri kendi şablonu | iskelet — içerik yazılmadı |

## Durum

- `node_type: subdomain` · `children: []` (v2 §2.20 `templates/` altında alt liste yok).
- **Eksik:** mimari seri için ayrı şablon tanımı → `⚠️ VERIFY REQUIRED` (ADR-026 §3.4 ayrı seri).
- Bu katalog yeni şablon **üretmez**; ek istek onay sonrası Guardrail #16 ile yapılır.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
