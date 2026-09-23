# CoreMusic — .sql Bağlam

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

---

## 1. Bağlam

SQL dump klasörü. MySQL altında 18 BCNF veritabanı dump dosyaları + destek dosyaları. Otorite: ADR-003 + ADR-040.
Sql Veritabanı dosyaları bauarda yazılır toplanır baurda nromzşie diemiş şekidle yazılır.

---

## 2. SQL Envanteri

| Klasör | İçerik | ADR |
|--------|--------|-----|
| `mysql/` | mysql veritabanı dosyaları | |
| `mssql/` | mssql veritabanı dosyaları  | — |
| `postgresql/` | astegresql veritabanı dosyaları  | — |
| `sqlite/` | sqlite veritabanı dosyaları  | — |

---

## 3. 18 BCNF Veritabanı

| # | Veritabanı | Tablo |
|---|------------|-------|
| | **TOPLAM** | **156** |

---

## 4. Protokol

- Boot protocol uygulanır ([[../CLAUDE.md]] §16)
- Değişiklikler [[../log.md]] audit izine yazılır
- Schema değişikliği → Data Engineer sorumluluğu

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
