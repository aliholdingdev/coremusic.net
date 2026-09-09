---
title: "CoreMusic - C:\www\coremusic.net\shared\src\Device Baglam"
type: context
folder: "C:\www\coremusic.net\shared\src\Device"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# Device - CLAUDE.md

**Zorunlu Baglantilar:** [[./AGENTS.md]] . [[../CLAUDE.md]]

## 1. Baglam
Cihaz tespiti ve hybrid rendering sunucu tarafi. HtmlShellRenderer DeviceRenderer uzerinden ID'li CSS link'leri + main[data-tier] + loader data-* uretir; device-loader.js ayni sozlesmeyle hydrate eder.

## 2. Mevcut Durum
| Durum | Deger |
|-------|-------|
| Dosya | 4 |
| Hybrid sozlesme | DeviceRenderer (server) ↔ device-loader.js (client); tier: phone/embedded/wide, 4K→wide |
| Konum | shared/src/Device |

## 3. Komsu Iliskiler
| Yon | Hedef | Iliski |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Ust baglam |
| Talimatlar | [[../AGENTS.md]] | Ust kurallar |

## 4. Degisiklik Protokolu
1. Degisiklik once ust talimatlarla uyum kontrolu
2. Gerekirse ADR + [[../../.ai/log.md]] audit

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
