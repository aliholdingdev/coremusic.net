---
title: "Architecture ADR Serisi — İndeks"
type: index
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: active
version: 1.0.0

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |

group: mimari
---

authority: "İki seri kasıtlı ayrıdır — ADR-026 §3.4 (bkz. .ai/.templates/adr/adr-nygard-template.md §1)"
---

# Mimari ADR Serisi (kendi numara uzayı)

> **Seriler ayrım kuralı (ADR-026 §3.4):** Bu klasördeki seriler `.ai/.decisions/` (karar serisi, ≥ADR-090) ile **birleştirilmez**. Mimari seri kendi numarasını taşır.

## Dizin

| Dosya | Seri | Durum |
|---|---|---|
| `templates/` | mimari seri şablonları (`adr-nygard` / `adr-index` kopyalarına bkz. `.ai/.templates/adr/`) | aktif |
| `decisions/` | mimari ADR metinleri (seri no: `ADR-0xx-mimari-…`) | boş — ilk mimari ADR henüz yazılmadı |

## İlişkili karar (karar serisinden)

| Karar | Neden burada değil? |
|---|---|
| `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` | K-Space V3 kurulumu **vault-yönetim kararıdır** → karar serisi (ADR-096 silinmiş numara aralığı). Mimari seri yalnız mimari şablon/katman tasarım kararlarına aittir. |

**Authority:** Bayram Ali / Vault Steward
