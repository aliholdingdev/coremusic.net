---
title: "PHPUnit"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
tags: [test, phpunit, qa]
---

# PHPUnit

Backend test aracı — `phpunit/phpunit ^10.5` (require-dev).

- `shared/tests/` + modül `tests/` dizinleri ve `phpunit.xml` yalnız 4 modülde var: [[home-coremusic-net]], [[api-coremusic-net]], [[auth-coremusic-net]] + `shared/` — `assets.coremusic.net`'te `phpunit.xml` **YOK** (frontend testleri Vitest/Playwright: `assets.coremusic.net/vitest.config.js`, `assets.coremusic.net/playwright.config.ts`)
- Çalıştırma: `composer test` → [[composer]]
- Runtime: [[php-8-4]]; frontend testleri ayrı: [[vitest]] ve [[playwright]]

Kaynak: `.ai/raw/not-repo-envanteri.md`

## İlgili Sayfalar
- [[composer]] — çalıştırma girişi
- [[phpstan]] — statik analiz eşleniği
- [[shared-infrastructure]] — test edilen paket
