---
title: "Redis"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-readme-vizyon]
tags: [önbellek, redis, cache, veritabanı]
---

# Redis

Platformun uçtan uca önbellek / anahtar-değer katmanı. README mimari envanterinde K5 Veri
Yönetimi bileşenleri arasında sayılır (MySQL 18 DB · 156 tablo · Redis).

## Konumu

- **Seviye:** Uygulama-uzamsal önbellek; PHP process önbelleği `ext-apcu`'dan ayrıdır.
- **İlişki:** `ext-apcu` ([[apcu]]) process-içi, Redis süreçler-arası ortak önbellektir.
- **Erişim:** `ext-pdo` üzerinden ([[pdo]]); ORM kullanılmaz.
- **Sahip:** [[shared-infrastructure]] cache katmanı (paket tanımı: "Cache, Config, Database,
  Middleware, PageRouter, Security").
- **Veri kaynağı:** [[mysql-9]] — 18 BCNF veritabanının ön önbelleği.

> ⚠ VERIFICATION REQUIRED: sürüm, port, eviction policy ve hangi cache namespace'lerinin
> Redis'e yazıldığı `raw/` kaynaklarında **yok**. Bu değerler yazılmadı.

Kaynak: `.ai/raw/not-readme-vizyon.md`

## İlgili Sayfalar
- [[apcu]] — süreç-içi karşılığı
- [[mysql-9]] — önbelleklenen veritabanı
- [[pdo]] — erişim katmanı
- [[shared-infrastructure]] — cache katmanının sahibi
- [[bcnf]] — önbelleklenen şemanın normalizasyon kademesi
