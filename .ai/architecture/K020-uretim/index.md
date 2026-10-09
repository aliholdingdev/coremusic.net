---
type: architecture
category: layer
title: "K020 — Üretim"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K020 — Üretim

## §1 Kimlik
- Katman: K020 · Alan: **A5** (K16-K20).
- Kapsam: elektronik platform üretimi — seri üretim mimarisi ve SKU yönetimi.

## §2 Sorumluluk
1. Elektronik platform üretim mimarisi (ADR-064).
2. Kanal varyant SKU'larının üretim planına taşınması (ADR-090).
3. BOM/imalat dosyalarının toplanması (K019 PCB çıktısı).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K016 · K017 · K018 · K019 (donanım bileşenleri) → A0 zemini.
- **Üst:** yok — A5 içindeki en üst katman.
- A5 içi kenarların tamamı plan §3'te tanımlanmadı → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-064 | Electronics Platform Architecture |
| ADR-090 | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) |

## §5 Durum
**PLANNED** — ADR-064 dosyası diskte (`.ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md`); üretim süreci/üretici kanıtı yok.

## §6 Risk / Not
- Üretim agent'ı AGENTS §4 registry'sinde yok → sahiplik **UNKNOWN** (geçici: audio-hardware-engineer).
