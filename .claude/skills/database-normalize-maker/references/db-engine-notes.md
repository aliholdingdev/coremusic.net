# Motor & Versiyon Notları

> MySQL sürüm gerçeğini tek yerde toplar. Zero-hallucination: doğrulanamayan sürüm iddiası
> `VERIFICATION REQUIRED`.

## Hedef

Bu skill **MySQL 8.0 (LTS)** hedefler. 8.0, uzun dönem destek (LTS) hattıdır ve üretim için
yaygın tercihtir.

## LTS (8.0) vs Innovation (9.x) farkı

| | 8.0 (LTS) | 9.x (Innovation) |
|--|-----------|------------------|
| Karakter | Uzun dönem destek | Yeni feature akışı |
| Bu skill | Hedef | Vault metinlerinde geçebilir |

CoreMusic vault'unda (`.ai/` ve eski skill metinlerinde) “MySQL 9” ifadesi geçebilir.
**Deploy edilen tam sürüm (major.minor.patch) `VERIFICATION REQUIRED`** — `.ai/.sql/mysql/`
veya ortam üzerinden doğrulanmalı.

## 8.0 feature min-sürüm tablosu (dikkat)

| Feature | Min 8.0 sürümü |
|---------|----------------|
| CHECK constraint enforce | 8.0.16+ |
| Çok-değerli (multi-valued) JSON index | 8.0.17+ |
| `EXPLAIN ANALYZE` | 8.0.18+ |
| DESC index | 8.0.0+ |
| Functional index | 8.0.0+ |
| CTE, window functions | 8.0.0+ |
| Invisible index | 8.0.0+ |
| `utf8mb4_0900_ai_ci` collation | 8.0.1+ |

> Production'a feature eklemeden önce patch sürümünü doğrula; “8.0” yetmez, 8.0.16/17/18+
> şart olanlar var.

## Collation notu

- 8.0 varsayılan collation `utf8mb4_0900_ai_ci` (8.0.1+); `utf8mb4_unicode_ci` da geçerli.
- Join/unique kolonlar **aynı** collation olmalı; karışık collation → implicit conversion
  ve index kullanımı kaybı.
- CoreMusic'in kullandığı tek collation vault'ta; karışık ise `VERIFICATION REQUIRED`.

## Partial index gerçeği

MySQL, PostgreSQL tarzı partial/filtered index (`CREATE INDEX ... WHERE`) **desteklemez**.
Yerine: composite index + predicate eşleme, generated kolon + index, veya covering index.
Bu, eski skill içeriğindeki bir hatanın düzeltilmiş halidir (bkz. 03-provider-dialects.md).

## TIMESTAMP 2038

TIMESTAMP 2038-01-19'da taşar. Uzun vadeli/kritik tarihler için DATETIME. Kesin proje
tercihi `VERIFICATION REQUIRED`.

## InnoDB

Varsayılan ve tek hedef engine. FK, crash recovery ve MVCC (REPEATABLE READ) sağlar.
Row-level locking; uzun transaction yasak (lock wait).
