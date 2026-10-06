---
title: "APCu"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
tags: [önbellek, apcu, php]
---

# APCu

Kullanıcı-uzamsal önbellek uzantısı — `"ext-apcu": "*"` (zorunlu, shared/composer.json).

- Amaç: `shared/` cache katmanını besler (paket tanımı: "Cache, Config, Database, Middleware, PageRouter, Security")
- Runtime: [[php-8-4]]; birlikte zorunlu: [[pdo]]
- Etkilendiği proje: [[shared-infrastructure]]

Kaynak: `.ai/raw/not-repo-envanteri.md`

## İlgili Sayfalar
- [[php-8-4]] — çalışma zamanı
- [[pdo]] — birlikte zorunlu uzantı
- [[shared-infrastructure]] — cache katmanının sahibi
