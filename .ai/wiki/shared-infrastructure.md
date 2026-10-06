---
title: "Shared Infrastructure"
type: proje
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
tags: [php, paket, altyapı]
---

# Shared Infrastructure

`shared/` dizinindeki ortak PHP paketi — composer meta verisi:

- name: `coremusic/shared-infrastructure` · version `2.0.0` · type `library` · license `proprietary`
- Kapsam: Cache, Config, Database, Middleware, PageRouter, Security
- PSR-4: `CoreMusic\` → `src/`; namespace altındaki tüm modüller bunu kullanır

## Bağımlılıklar

- Runtime: [[php-8-4]] · [[pdo]] · [[apcu]] · [[composer]]
- Dev: [[phpunit]] (^10.5) · [[phpstan]] (level 5)
- Ek: psr/log, psr/cache, psr/container, symfony/event-dispatcher, respect/validation, nyholm/psr7, php-di/php-di

Kaynak: `.ai/raw/not-repo-envanteri.md`

## İlgili Sayfalar
- [[coremusic-platform]] — çatı proje
- [[php-8-4]] · [[pdo]] · [[apcu]] — runtime
- [[phpunit]] · [[phpstan]] — test ve statik analiz
