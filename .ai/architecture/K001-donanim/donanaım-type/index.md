---
type: architecture
category: layer-subindex
title: "K001 · donanaım-type — Donanım Tipi Kırılımı (Dizin)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# donanaım-type — Donanım Tipi Kırılımı (Dizin)

> Üst indeks: `[[../index]]` · Dizin adındaki yazım (`donanaım`) **mevcut yapıdır — değiştirilmedi** (onaysız rename yasak).

## §1 Kapsam
Donanım tiplerinin kırılım dizini. 3 alt dal:

| Alt dal | Konu | Durum |
|---|---|---|
| `[[electroncis-circuits/index|electroncis-circuits]]` | devre tipleri (ses yolu, amplifikatör) | PLANNED (ADR) |
| `[[hdd/index|hdd]]` | HDD — depolama tipi | UNKNOWN (K001 kapsamı kasıtı belirsiz) |
| `[[ssd/index|ssd]]` | SSD — depolama tipi | UNKNOWN (K001 kapsamı kasıtı belirsiz) |

## §2 Bağımlılık
- **Alt:** K000.
- **Üst:** `[[../index]]` (K001) · A5 bileşen grupları (K016-K020).

## §3 Not — kapsam uyarısı
`hdd` / `ssd` dalları ses-müzik donanım mimarisiyle (ADR-061/038/089) doğrudan örtüşmüyor;
medya depolama perspektifinden mi (K015 medya) yoksa fiziksel envanterden mi geldiği
**kanıtlanamadı** → her iki dal dosyalarında kapsam `⚠️ VERIFICATION REQUIRED` işaretlidir.
