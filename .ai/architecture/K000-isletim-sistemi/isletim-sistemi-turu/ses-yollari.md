---
title: "K000-isletim-sistemi-turu/ses-yollari"
type: architecture
category: layer-detail
date: 2026-10-10
status: planned
version: 1.0.0
authority: SSOT
---

# Ses Yolları (Platform Özeti)

> **PLANNED — içerik YOK (2026-10-10)** · eşik/sayı/ölçüm bu dosyada **uydurulmaz** (şablon §7.4).

## §1 Kimlik

- Dosya: `ses-yollari.md` · Klasör: `isletim-sistemi-turu/` · Statü: **planned** · Tarih: 2026-10-10.
- Üst: `[[index]]` · Kardeş: `[[platform-karsilastirma]]` · `[[windows]]` · `[[linux]]` · `[[macos]]` · `[[web]]`.

## §2 Amaç

Bu dosya kapandığında hangi soruyu cevaplayacaktır:

- Her platformun ses yolu zinciri (uygulama → API → donanım) **tek tabloda** nasıl özetlenir?
- Platformlar arası ortak/paylaşılan nokta (`IAudioBackend` — ADR-019) nerede kesişir?
- Hangi platformda hangi fallback zinciri devrededir?

## §3 Planlanan İçerik Dalları

| # | Dal | Kanıt kaynağı |
|---|---|---|
| 1 | Masaüstü zincirleri (Win/macOS/Linux) | ADR-019 §1 — dosya VAR |
| 2 | Tarayıcı zinciri | `[[web]]` §4 · MDN kaynakları |
| 3 | Mobil/TV zincirleri (native karar YOK) | `[[ios]]` · `[[andorid]]` · `[[tizen]]` §8 |
| 4 | Fallback/kill-switch zinciri | `brain.md:862` · ADR-019 · ADR-017 |
| 5 | Eşik/ölçüm kapısı | ADR-006 — **sayı bu stub'a yazılmaz** |

## §4 Bağlantılar

`[[index]]` · `[[platform-karsilastirma]]` · `[[../isletim-sistemi-plan/adr]]`

**Doldurma koşulu:** ADR-019 adapter kapsamı + gerçek ölçüm (K3 kanıtı) olmadan sayı/eşik girilmez.
