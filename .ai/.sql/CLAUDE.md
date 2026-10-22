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

## 3. 19 BCNF Veritabanı

| # | Veritabanı | Tablo |
|---|------------|-------|
| | **TOPLAM (19 coremusic DB)** | **185 tablo + 1 view** — 2026-10-22 Enterprise genişletme (+8 `coremusic_billing`, +2 `coremusic_musics`: `podcast_transcript_segments`, `radio_play_history`, +1 `coremusic_cms`: `cms_page_revisions`) |

**2026-10-22 dump senkronu, BCNF normalizasyon & Enterprise genişletme:** `mysql/*.sql` = 21 dosya, **birebir canlı MySQL dump'ı + BCNF normalizasyon + Enterprise modülleri** (üreteç: `mysqldump` 9.7; yeniden kurulum: `rebuild-all.ps1`). Kullanıcı kuralı gereği (2026-10-22) **ayrı migration dosyası üretilmemiş, tüm eksik tablo/kolon/indeksler doğrudan `.sql` şemalarına gömülmüştür**:
1. `coremusic_billing.sql` (YENİ - 8 tablo): Enterprise faturalandırma ve gelir paylaşımı — `subscription_plans`, `payment_methods`, `user_subscriptions`, `payment_transactions`, `invoices`, `device_playback_sessions` (Spotify eşzamanlı cihaz akış kilidi), `content_licenses` (PRO/ISRC lisansları), `royalty_payout_ledger` (sanatçı streaming havuzu hakediş defteri).
2. `coremusic_musics.sql` (+2 tablo, 29 tablo + 1 view): `podcast_transcript_segments` (karaoke/zaman damgalı seeking) ve `radio_play_history` (zaman serisi radyo çalma geçmişi) tabloları eklendi; `podcast_shows.feed_url`, `podcast_episodes.guid`, `enclosure_url`, `enclosure_length`, `enclosure_type` eklendi.
3. `coremusic_cms.sql` (+1 tablo, 9 tablo): `cms_page_revisions` (sayfa sürüm geçmişi, rollback ve denetim tablosu) eklendi; `cms_pages.published_at` + `idx_cp_published` eklendi.
4. `coremusic_social.sql` (+2 tablo, +3 kompozit indeks): `oauth_connections` ve `oauth_states` eklendi; `user_id` `BINARY(16)` olarak `coremusic_auth.users.id`'ye bağlandı (ADR-072 / C5 tip uyumu).
5. `coremusic_studio.sql`: `studio_equipment.idx_se_serial` eklendi (ADR-077).
6. `coremusic_system.sql`: `i18n_languages` tablosuna `updated_at`, `is_deleted`, `deleted_at` audit & soft-delete kolonları eklendi (ADR-079 / ADR-033).
7. `coremusic_user.sql`: `user_favorites.item_type` ENUM'ına `'radio'` ve `'video'` eklendi (ADR-074 §1.1/15b / polymorphic favorite uyumu).

Dosyalarda `DROP TABLE IF EXISTS` var → **idempotent rebuild**. ŞEMA: TAM (tablo/view/routine/trigger). VERİ: yalnız onaylı seed — `coremusic_system.system_services` (7) + `coremusic_system.i18n_languages` (12) + `coremusic_neva.eq_presets` (7) + `media_catalog.taxonomy` (98) — **kullanıcı onayı 2026-10-22** (PII/kullanıcı verisi YOK). `novasearch` **AYRI PROJE** — CoreMusic regen/rebuild döngüsüne DAHİL DEĞİL (kullanıcı kuralı 2026-10-22; 7 tablo + `v_search_log_singular` view). `media_catalog` **9 tablo + 1 view = CANLI**. CoreMusic platform toplamı: **194 tablo + 2 view** (19 CoreMusic DB + media_catalog; novasearch hariç). Genel toplam: **201 tablo + 2 view** (21 SQL dosyası). Yeniden kurulum: `mysql/rebuild-all.ps1`. FAZ1-3 migration kayıtları: `migration/2026-10-07_faz{1,2,3}_*.sql`.

---

## 4. Protokol

- Boot protocol uygulanır ([[../CLAUDE.md]] §16)
- Değişiklikler [[../log.md]] audit izine yazılır
- Schema değişikliği → Data Engineer sorumluluğu
- **Migration yazma yasağı (kullanıcı kuralı — 2026-10-22):** `.ai/.sql/` altında ve bu iş akışında **migration dosyası üretilmez/uygulanmaz**. Eksik veya eklenecek her şema parçası (tablo, sütun, index, view, seed) **doğrudan mevcut `mysql/*.sql` dump dosyasına gömülür** ve şema doğrudan canlı MySQL'e uygulanır. Tek kanal: dump dosyaları (`rebuild-all.ps1` = kurulum, regen betiği = senkron). `shared/database/migrations/` bu akışta referans okunur, yeni hedef değildir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-22

