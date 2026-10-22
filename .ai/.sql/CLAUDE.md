# CoreMusic — .sql Bağlam

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

---

## 1. Bağlam

SQL dump klasörü. MySQL altında **20 dump dosyası** (2026-10-22 ölçümü): 18 BCNF `coremusic_*` + `media_catalog` (CANLI — 2026-10-22 doğrulandı) + `novasearch` (**AYRI PROJE** — nova-search-engine Node.js; CoreMusic şema senkronu/rebuild döngüsü DIŞI; canlıya 2026-10-22 kuruldu, `v_search_log_singular` köprü view'ı dahil). Otorite: ADR-003 + ADR-040.
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
| | **TOPLAM (18 coremusic DB)** | **172 tablo + 1 view** — 2026-10-22 canlı dump ölçümü (eski: 171 @2026-10-07; 2026-10-21 migration'ları sonrası) |

**2026-10-22 dump senkronu:** `mysql/*.sql` = 20 dosya, **birebir canlı MySQL dump'ı** (üreteç: `mysqldump` 9.7 — `full_dump.php` diskte YOK, git geçmişine düştü; betik: `C:\temp\opencode\regen_dumps_20261022.ps1`). **Doğrulama: 19/19 DB yapısal eşit** (`verify_<db>` import → `information_schema` tablo|view|sütun|index canlı ile birebir); FAIL görünen 6 satır yalnız `TABLE_ROWS` farkıdır = canlıda veri var, dump'a KVKK gereği yazılmadı. `fix_constraints_20261021.sql` migration'ının tüm index'leri dump'a dahil (kanıt: `coremusic_logs.sql:181,207,293,466`). Dosyalarda `DROP TABLE IF EXISTS` var → **idempotent rebuild**. ŞEMA: TAM (tablo/view/routine/trigger). VERİ: yalnız onaylı seed — `coremusic_system.system_services` (7) + `coremusic_system.i18n_languages` (12) + `coremusic_neva.eq_presets` (7) + `media_catalog.taxonomy` (98) — **kullanıcı onayı 2026-10-22** (ilk üçü yeni whitelist genişlemesi; PII/kullanıcı verisi YOK). `novasearch` **AYRI PROJE** — CoreMusic regen/rebuild döngüsüne DAHİL DEĞİL (kullanıcı kuralı 2026-10-22); dump dosyası korunur, canlıya 2026-10-22 kuruldu (7 tablo + `v_search_log_singular` view — bcnf köprüsü). `media_catalog` **9 tablo + 1 view = CANLI** (eski "PLANNED" kaydı geçersiz). CoreMusic toplamı: **181 tablo + 2 view** (18 DB + media_catalog; novasearch hariç). Yeniden kurulum: `mysql/rebuild-all.ps1` (sıralı import + rapor). FAZ1-3 migration kayıtları: `migration/2026-10-07_faz{1,2,3}_*.sql` (up/down çiftleri).

---

## 4. Protokol

- Boot protocol uygulanır ([[../CLAUDE.md]] §16)
- Değişiklikler [[../log.md]] audit izine yazılır
- Schema değişikliği → Data Engineer sorumluluğu
- **Migration yazma yasağı (kullanıcı kuralı — 2026-10-22):** `.ai/.sql/` altında ve bu iş akışında **migration dosyası üretilmez/uygulanmaz**. Eksik veya eklenecek her şema parçası (tablo, sütun, index, view, seed) **doğrudan mevcut `mysql/*.sql` dump dosyasına gömülür** ve şema doğrudan canlı MySQL'e uygulanır. Tek kanal: dump dosyaları (`rebuild-all.ps1` = kurulum, regen betiği = senkron). `shared/database/migrations/` bu akışta referans okunur, yeni hedef değildir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
