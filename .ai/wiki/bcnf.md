---
title: "BCNF (Boyce-Codd Normal Formu)"
type: kavram
created: 2026-10-06
updated: 2026-10-06
sources: [not-readme-vizyon]
tags: [veritabanı, normalizasyon, bcnf, şema]
---

# BCNF (Boyce-Codd Normal Formu)

İlişkisel şema normalizasyonu kademesi. Bir ilişki BCNF'dedir; ancak **her çözücü bağımlılığı
nitelik değil, aday anahtarın kendisi** olduğunda. 3NF'den daha sıkı bir koşuldur: 3NF'de aday
anahtarla örtüşmeyen sol taraflara izin verilirken, BCNF o sol tarafları da aday anahtara indirger.

## Neden önemli

- Aşırı normalizasyon yazma maliyetini artırır, eksik normalizasyon güncelleme anomalisisi doğurur.
- 156 tablolarlık bir şemada BCNF, tekrar eden veri ve anomali riskini düşürür.
- Veritabanı denetimlerinde (migration öncesi) BCNF ihlali, şema revizyonu gerektiren bulgu olarak raporlanır.

## CoreMusic bağlamı

- README v2.0.1: **18 BCNF normalleştirilmiş veritabanı, 156 tablo** → bkz. [[mysql-9]].
- Uygulama katmanı: `ext-pdo` zorunlu (bkz. [[pdo]]); ORM yok, ham sorgu.
- Şema sahibi: [[shared-infrastructure]] database katmanı.

Kaynak: `.ai/raw/not-readme-vizyon.md`

## İlgili Sayfalar
- [[mysql-9]] — 18 BCNF veritabanının kendisi
- [[pdo]] — erişim katmanı
- [[shared-infrastructure]] — config/database sahibi
- [[coremusic-platform]] — 21 katmanlık mimaride K5 Veri Yönetimi
