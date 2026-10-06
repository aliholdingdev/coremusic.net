---
title: "README Vizyonu ve Teknoloji Yığını (Not)"
type: kaynak
raw_path: raw/not-readme-vizyon.md
created: 2026-10-06
updated: 2026-10-06
sources: [not-readme-vizyon]
ingested: 2026-10-06 18:14
tags: [vizyon, mimari, teknoloji, kişi]
---

# Kaynak Özeti — README Vizyonu ve Teknoloji Yığını

## Genel Özet

`README.md` (v2.0.1) diskten okundu: platform tanımı, 21 katman/1095 bileşen mimarisi, ses motoru (C++20 Neva Engine), veri (MySQL 9 / 18 BCNF / 156 tablo), frontend (Vanilla JS + ITCSS/BEM) ve Authority kişisi (Bayram Ali / Vault Steward).

## Ana Fikirler

1. Authority: **Bayram Ali / Vault Steward** (README:262).
2. C++20 Neva Engine: zero-allocation, lock-free, OS ses katmanını baypas eder.
3. MySQL 9 — 18 BCNF veritabanı, 156 tablo, + Redis + APCu.
4. Frontend: Vanilla JS ES2022 SPA Router, ITCSS 9 katman, BEM, Glassmorphism.
5. ⚠ Çelişki: README `pro.coremusic.net` anlatıyor; diskte bu dizin yok (api, assets, auth, home, media var).
   → **✅ Çözüldü (2026-10-06 18:37):** README alt alan adı tablosu = plan (10 satır), disk = implementasyon (5 dizin); plan≠implementasyon farkı. Kapsam notu: diskteki `api.coremusic.net` ve `assets.coremusic.net` README planında yok. Detay: [[coremusic-platform]].

## Önemli Alıntılar/Veriler

- `**Authority:** Bayram Ali / Vault Steward` (README:262)
- Rozetler: PHP-8.4 · JavaScript-ES2022-Vanilla · C++-20-NevaEngine · MySQL-9-(18-BCNF) · Architecture-21-Layers-(1095-Components) · License-Proprietary
- "K5: 55 bileşen — MySQL 18 DB (156 tablo), Redis" · "K3: 55 bileşen — Neva Engine, DSP, EQ, Crossover" · "K11: 45 bileşen — ITCSS, BEM, Tokens, Theme, PWA"

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

Yeni (4): [[bayram-ali]] · [[neva-engine]] · [[mysql-9]] · [[vanilla-javascript]]
Güncellenen (2): [[coremusic-platform]] (21 katman/1095 bileşen + ⚠ Çelişki pro.coremusic.net) · [[assets-coremusic-net]] (ITCSS/BEM frontend)
