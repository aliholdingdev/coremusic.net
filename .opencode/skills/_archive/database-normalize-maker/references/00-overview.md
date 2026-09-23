# 00. Overview: Veritabanı Oluşturma & Normalizasyon Motoru

## Genel Bakış

`database-normalize-maker`, CoreMusic projesinde veritabanı şeması tasarlayan, normalizasyon yapan ve MySQL 9 uyumlu SQL komutları üreten bir skill'dir.

## Bu Skill Ne Yapar?

1. **Veritabanı Oluşturma:** `CREATE DATABASE` ve `CREATE TABLE` komutlarını üretir.
2. **Tablo Tasarımı:** BCNF kurallarına uygun tablolar tasarlar.
3. **SQL Üretimi:** CREATE, ALTER, INSERT, UPDATE, DELETE, SELECT, JOIN, TRANSACTION, VIEW, TRIGGER komutlarını üretir.
4. **Normalizasyon:** 1NF → 2NF → 3NF → BCNF kontrollerini yapar.
5. **İndeks Stratejisi:** Sorgu kalıplarına göre index tasarlar.
6. **Migration:** Expand-contract zero-downtime migration scriptleri oluşturur.
7. **Güvenlik:** PII şifreleme, audit trail ve soft delete uygular.
8. **ER Diyagramı:** Mermaid.js formatında entity-relationship diyagramı üretir.

## Kullanmaz

- ORM (ADR-021: ORM yasak — sadece PDO prepared statements)
- Framework (ADR-001: Framework yasak)
- `SELECT *` (yasak — açık kolon listesi)
- `FLOAT` para birimi (yasak — `DECIMAL(10,2)`)

## 6 Adımlı İş Akışı (Model → Migrate → Validate)

```
1. MODEL    — Gereksinimleri anla, şemayı tasarla
2. DESIGN   — Kolonları, tipleri, ilişkileri belirle
3. NORMALIZE — 1NF/2NF/3NF/BCNF kontrolü yap
4. MIGRATE  — SQL komutlarını üret (up + down)
5. DOCUMENT — schema.sql, seed.sql, ER diyagramı oluştur
6. VALIDATE — 25 maddelik kontrol listesinden geçir
```

## Beklenen Davranış

Sistemi çağırdığınızda:
1. Önce gereksinimleri sorar (tablo, ilişki, ölçek, sorgu kalıpları)
2. Şemayı tasarlar ve normalizasyon kontrolünden geçirir
3. Tüm SQL komutlarını üretir (CREATE, ALTER, INDEX, FK, INSERT, SELECT, vb.)
4. Seed verisi, ER diyagramı ve migration dosyaları oluşturur
5. 25 maddelik validation checklist'ten geçirir

## MySQL 9 Zorunlu Kuralları

| Kural | Detay |
|-------|-------|
| Motor | `ENGINE=InnoDB` |
| Charset | `utf8mb4_unicode_ci` |
| Primary Key | `id BIGINT UNSIGNED AUTO_INCREMENT` |
| Para | `DECIMAL(10,2)` — asla `FLOAT` |
| ORM | Yasak — PDO prepared statements only |
| SELECT * | Yasak — açık kolon listesi |
| Timestamp | `created_at`, `updated_at`, `deleted_at` (her tabloda) |

## CoreMusic Veritabanı Sayısı

11 BCNF veritabanı: auth, users, musics, albums, playlist, catalog, logs, media, system, social, wireless.

## Referans

- Ana dosya: `SKILL.md` (v5.0)
- Alt dosyalar: `references/`, `scripts/`, `templates/`
