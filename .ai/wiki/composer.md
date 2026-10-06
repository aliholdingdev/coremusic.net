---
title: "Composer"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
tags: [composer, bağımlılık, php]
---

# Composer

Her modülün bağımlılık yöneticisi; 6 modülün **5'inde `composer.json`** (shared, home, api, auth, media) ve **4'ünde `composer.lock`** var (media'da lock YOK; `assets.coremusic.net`'te hiçbiri YOK).

- Ana paket: `coremusic/shared-infrastructure` v2.0.0 (bkz. [[shared-infrastructure]])
- PSR-4 autoload: `CoreMusic\` → `src/`
- Scriptler: `test` → [[phpunit]], `stan` → [[phpstan]]
- Runtime: [[php-8-4]]

Kaynak: `.ai/raw/not-repo-envanteri.md`

## İlgili Sayfalar
- [[shared-infrastructure]] — tanımlanan paket
- [[php-8-4]] · [[phpunit]] · [[phpstan]] — araç zinciri
- [[coremusic-platform]] — üst proje
