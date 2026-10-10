---
title: "K000-isletim-sistemi-turu/kurulum-ortamlari"
type: architecture
category: layer-detail
date: 2026-10-10
status: planned
version: 1.0.0
authority: SSOT
---

# Kurulum Ortamları

> **PLANNED — içerik YOK (2026-10-10)** · eşik/sayı/ölçüm bu dosyada **uydurulmaz** (şablon §7.4).

## §1 Kimlik

- Dosya: `kurulum-ortamlari.md` · Klasör: `isletim-sistemi-turu/` · Statü: **planned** · Tarih: 2026-10-10.
- Üst: `[[index]]` · Kardeş: `[[platform-karsilastirma]]` · sınır komşusu: `[[../isletim-sistemi-plan/index]]`.

## §2 Amaç

Bu dosya kapalandığında hangi soruyu cevaplayacaktır:

- Platform başına kurulum/çalıştırma ortamı notu (web zemini, masaüstü player, gömülü cihaz) nedir?
- Dev/Staging ortamı ile platform notu nasıl ayrışır (ortam ≠ platform)?
- Hangi ortam kanıtı diskte vardır, hangisi yalnız hedeftir?

## §3 Planlanan İçerik Dalları

| # | Dal | Kanıt kaynağı |
|---|---|---|
| 1 | Dev/Staging ortam mimarisi | ADR-082 · ADR-099 — dosyalar VAR, okunacak |
| 2 | Web zemin kurulumu (subdomain + composer) | `../isletim-sistemi-api/index` · `../isletim-sistemi-core/index` |
| 3 | Deployment modları | `.ai/CLAUDE.md` §14 (satır 366-370) |
| 4 | Gömülü/ARM64 kurulumu | `UNKNOWN` — betik diskte YOK |

## §4 Bağlantılar

`[[index]]` · `[[platform-karsilastirma]]` · `[[../isletim-sistemi-plan/index]]` · `[[../isletim-sistemi-api/index]]`

**Doldurma koşulu:** kurulum betiği/komutu diskte doğrulanmadan adım yazılmaz (uydurma komut yasak).
