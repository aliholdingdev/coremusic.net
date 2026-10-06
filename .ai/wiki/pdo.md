---
title: "PDO (PHP Data Objects)"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
tags: [veritabanı, pdo, php]
---

# PDO

Veritabanı erişim katmanı — `shared/composer.json` içinde zorunlu uzantı: `"ext-pdo": "*"`.

- Tüm modüllerde ortak; `shared/` içindeki database/ yapılandırması bunu kullanır
- Runtime: [[php-8-4]]; birlikte zorunlu: [[apcu]] (önbellek)
- Etkilendiği projeler: [[shared-infrastructure]], [[api-coremusic-net]], [[auth-coremusic-net]]

Kaynak: `.ai/raw/not-repo-envanteri.md`

## İlgili Sayfalar
- [[php-8-4]] — çalışma zamanı
- [[apcu]] — birlikte zorunlu uzantı
- [[shared-infrastructure]] — sarmalayan paket
