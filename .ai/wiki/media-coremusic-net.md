---
title: "media.coremusic.net"
type: proje
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
tags: [medya, gui, php]
---

# media.coremusic.net

Medya/GUI modülü — `media.coremusic.net/`.

- Yapı: `bin/`, `src/`, `config/`, `docs/`, `media/`
- Kendi `composer.json`'u var → [[composer]]; docs/ altında faz dokümantasyonu tutulur
- Omurga: [[php-8-4]] · [[shared-infrastructure]]
- Test altyapısı: **✅ doğrulandı (2026-10-06 18:49): `tests/` YOK, `phpunit.xml` YOK, composer.json'da test scripti YOK** — diğer 4 modülde (shared, home, api, auth) tests/ + phpunit.xml mevcut; `assets.coremusic.net`'te `phpunit.xml` YOK — frontend testleri Vitest/Playwright ile (`assets.coremusic.net/vitest.config.js`, `assets.coremusic.net/playwright.config.ts`). Sonuç: bu modül PHPUnit kapsamı dışında = **test kapsamı boşluğu** (bilgi eksiği değil, disk kanıtlı durum)

Kaynak: `.ai/raw/not-repo-envanteri.md`

## İlgili Sayfalar
- [[coremusic-platform]] — çatı proje
- [[shared-infrastructure]] — ortak paket
- [[api-coremusic-net]] — veri komşusu
