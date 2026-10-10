---
title: "K000-isletim-sistemi-turu/donanim-uyumluluk"
type: architecture
category: layer-detail
date: 2026-10-10
status: planned
version: 1.0.0
authority: SSOT
---

# Donanım Uyumluluk

> **PLANNED — içerik YOK (2026-10-10)** · eşik/sayı/ölçüm bu dosyada **uydurulmaz** (şablon §7.4).

## §1 Kimlik

- Dosya: `donanim-uyumluluk.md` · Klasör: `isletim-sistemi-turu/` · Statü: **planned** · Tarih: 2026-10-10.
- Üst: `[[index]]` · Kardeş: `[[platform-karsilastirma]]` · sınır komşusu: `[[../electroncis/index]]` (içerik K001'e ait).

## §2 Amaç

Bu dosya kapandığında hangi soruyu cevaplayacaktır:

- Hangi platform → hangi donanım hedefi (ses kartı/DAC/I2S) eşleşir?
- `electroncis/` sınırı ile platform notu arasındaki ayrım nettir (donanım ≠ OS notu)?
- Donanım bulunamadığında platform davranışı nedir?

## §3 Planlanan İçerik Dalları

| # | Dal | Kanıt kaynağı |
|---|---|---|
| 1 | Deployment ↔ donanım eşlemesi | `.ai/CLAUDE.md` §14 (satır 366-370) |
| 2 | I2S / USB / BT / DLNA yolları | `.ai/CLAUDE.md` §13 K2 satırı (satır 119) |
| 3 | Tier ↔ ses sürücüsü eşlemesi | `.ai/CLAUDE.md` §13 (satır 352-358) |
| 4 | Donanım kararları (PCM3168A/XMOS) | ADR-038 · ADR-061 — sınır notu, tekrar karar DEĞİL |

## §4 Bağlantılar

`[[index]]` · `[[platform-karsilastirma]]` · `[[../electroncis/index]]` · `[[../../K001-donanim/index]]`

**Doldurma koşulu:** K001 içerik kanıtı (cpp/h, PCB) glob = 0 → donanım iddiası `PLANNED` kalır.
