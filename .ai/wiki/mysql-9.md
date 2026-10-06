---
title: "MySQL 9 (18 BCNF)"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-readme-vizyon]
tags: [veritabanı, mysql, bcnf]
---

# MySQL 9

Platformun ilişkisel veritabanı — README: **18 BCNF normalleştirilmiş veritabanı, 156 tablo**.

- Mimari yerleştirme: K5 Veri Yönetimi — 55 bileşen (MySQL 18 DB, 156 tablo, [[redis]])
- Normalizasyon: 18 şema **BCNF** → bkz. [[bcnf]]
- Eşlik eden önbellek: [[redis]] + `ext-apcu` (bkz. [[apcu]])
- Erişim katmanı: `ext-pdo` zorunlu (bkz. [[pdo]])
- Modül ilişkisi: [[api-coremusic-net]], [[auth-coremusic-net]] → [[shared-infrastructure]] database katmanı

Kaynak: `.ai/raw/not-readme-vizyon.md`

## İlgili Sayfalar
- [[bcnf]] · [[redis]] — şema normalizasyonu ve önbellek
- [[pdo]] · [[apcu]] — erişim ve önbellek
- [[shared-infrastructure]] — database/config katmanının sahibi
- [[neva-engine]] · [[vanilla-javascript]] — yığının diğer katmanları
