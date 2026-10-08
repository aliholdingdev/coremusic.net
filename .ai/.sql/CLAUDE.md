# CoreMusic — .sql Bağlam

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

---

## 1. Bağlam

SQL dump klasörü. MySQL altında **20 dump dosyası** (2026-10-07 ölçümü): 18 BCNF `coremusic_*` + `media_catalog` (PLANNED) + `novasearch` (canlı dump, knex) + destek dosyaları. Otorite: ADR-003 + ADR-040.
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
| | **TOPLAM (18 coremusic DB)** | **171** — 2026-10-07 canlı dump ölçümü (eski: 156; +15 tablo: musics +5, albums +6, social +1, system +3 + 1 view `v_music_recording_map`) |

**2026-10-07 dump senkronu:** `mysql/*.sql` = 20 dosya, **birebir canlı MySQL dump'ı** (full_dump.php, şema tam; VERİ yalnız `coremusic_catalog` lookup + `system_services` 7 seed satırı — kullanıcı onayı 2026-10-07; PII/kullanıcı verisi YOK). `novasearch` 7 tablo, `media_catalog` 9 tablo (canlıda YOK = PLANNED). Tüm dosya toplamı: **187 tablo + 1 view**. FAZ1-3 migration kayıtları: `migration/2026-10-07_faz{1,2,3}_*.sql` (up/down çiftleri).

---

## 4. Protokol

- Boot protocol uygulanır ([[../CLAUDE.md]] §16)
- Değişiklikler [[../log.md]] audit izine yazılır
- Schema değişikliği → Data Engineer sorumluluğu

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
