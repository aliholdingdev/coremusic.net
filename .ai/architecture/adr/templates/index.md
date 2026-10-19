---
title: "Mimari ADR Şablonları"
type: index
category: architecture
date: 2026-10-12
status: active
version: 1.0.0

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

group: mimari
---

authority: "Şablon SSOT: .ai/.templates/adr/ (Guardrail #16)"
---

# Mimari ADR Şablonları

Bu dizin, `.ai/architecture/adr/decisions/` altındaki mimari seri ADR'leri için şablon tutar.

- Şablon kaynağı (kopya DEĞİL, oraya bakılır): `.ai/.templates/adr/adr-nygard-template.md` (Nygard: Status/Context/Decision/Consequences) · `adr-template.md` (house) · `adr-index.md`.
- **İki ADR serisi ayrıdır** (ADR-026 §3.4): `.ai/.decisions/` = karar serisi (ADR-090+) · `.ai/architecture/adr/decisions/` = mimari seri.
- Mimari seri numarası: `ADR-0NN-mimari-<konu>.md` (ilk kayıt henüz yok).

## §A Şablon eşlemesi (hangi şablon ne zaman)

| # | Şablon | Kullanım anı | Erişim |
|---|---|---|---|
| 1 | `adr-nygard-template.md` | mimari seri ADR (bu dizin) | okunur · kopyalanıp dosyaya gömülür |
| 2 | `adr-template.md` (house) | house biçimi karar kaydı | `.ai/.templates/adr/` içinden |
| 3 | `adr-index.md` | ADR dizin/katalog iskeleti | `.ai/.templates/adr/` içinden |

**Kural (Guardrail #16):** şablon **kaynaktan okunur**, bu dizinde şablonun kopyası
tutulmaz — buradaki `index.md` yalnız eşleme ve serinin künyesidir. Şablon
değişirse tek kaynak güncellenir; bu dosyadaki satırlar yeniden sayılır.

**Authority:** Bayram Ali / Vault Steward
