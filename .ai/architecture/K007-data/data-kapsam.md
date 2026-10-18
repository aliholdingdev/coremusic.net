---
title: "K007-data — Kapsam Sınırı (K-Space V3)"
type: architecture-scope
category: architecture
date: 2026-10-12
status: draft
version: 1.0.0
node_id: K007-data
node_type: scope
parent_id: "K000–K5000 Architecture Registry"
children:
  - "schema"
  - "migrations"
  - "repositories"
  - "transactions"
  - "indexing"
  - "cache"
  - "search"
  - "synchronization"
  - "backup"
  - "data-quality"
depends_on:
  - "K007-data/data-master"
  - "K008-domain (anlam sınırı — §4/§5)"
  - "K009-security (şifreleme çağrısı sınırı — §4)"
  - "K000-isletim-sistemi (fiziksel saklama sınırı — §4)"
  - "K17-devops (⚠️ yedek sahipliği — S-2)"
  - "K020-discovery (⚠️ arama sahipliği — S-1)"
  - "K013-integraion (⚠️ senkronizasyon sınırı — S-3)"
evidence:
  - "v2 §2.7 (rol cümlesi + 10 çocuk) — 2026-10-12 okundu"
  - ".ai/architecture/index.md §5 satır 7 — 2026-10-12 okundu"
  - "Depo: shared/src/Database/ · shared/src/Cache/ · shared/src/Repository/ · shared/database/migrations/ — 2026-10-12 glob"
validation: "2026-10-12 — kapsam-içi 10 çocuk ve kapsam-dışı 6 sınır kaydedildi; 6 açık soru S-1..S-6 ile master'da birebir aynı."
open_questions:
  - "S-1 arama sahipliği sınırı (K020-discovery ile kesişiyor)"
  - "S-2 yedekleme sahipliği sınırı (K018-devops ile kesişiyor)"
  - "S-3 senkronizasyon sınırı (K013-integraion ile kesişiyor)"
  - "S-4 kademe-3 yapısı (ADR-097 K4) bu kapsamın çocuklarına oturacak"
  - "S-5 MIT/AGPL referans sınırı (K020-licence)"
  - "S-6 dış arama motoru kararı verilirse search kapsamı kesinleşir"
group: mimari
---

# data-kapsam — K007-data

## §1 ELI15 — bu oda ne?

**K007-data, CoreMusic'in kütüphanesinin "kendi kartoteks sistemidir."**

Şarkılar, kullanıcılar, listeler ve yedekler burada durur. Düşün: kütüphane kataloğu
(kim hangi kitabı ne zaman aldı), kitapların kendisi (şarkı künyesi), katalog kartları
(indeks), arama çekmecesi (search), sık bakılanların rafı (cache) ve kütüphanenin
yedeği için alınan mikrofilm (backup). Bu oda **kütüphanenin çalışanının** işidir:
katalog kartını düzenler, doğru rafta tutar, aranabilir kılar, bozulanı onarır.

Ne **kütüphaneci** (K008-domain — hangi kitabın hangi rafta olduğunun **anlamı**), ne de
**kapıdaki bekçi** (K009-security — kilit ve anahtarın sahibi), ne de **bina**
(K000/K001 — fiziksel disk) bu odadır. Fakat üçüyle de konuşur.

## §2 İÇİNDE (bu domainin sahibi olduğu)

| # | Yüzey | Ne demek | Kanıt |
|---|---|---|---|
| 1 | Şema tasarımı | tablo · kolon · tip · CONSTRAINT · COMMENT · BCNF | `.ai/.sql/mysql/*.sql` (20 dosya) |
| 2 | Şema değişimi | versiyonlu, geri alınabilir, kayıtlı migration | `shared/database/migrations/` (3 dosya) |
| 3 | Veri erişimi | repository sözleşmesi — SQL domain'den saklanır | `shared/src/Repository/` |
| 4 | Atomiklik | beginTransaction / commit / rollBack | `DatabaseManager.php:49-62` |
| 5 | İndeksleme | B-tree · prefix · UNIQUE · FULLTEXT tasarımı | dump'larda `KEY`/`UNIQUE KEY` satırları |
| 6 | Önbellek | TTL · kirletme · fallback | `shared/src/Cache/` (6 dosya) |
| 7 | Arama | metinlerde bulma (FULLTEXT/ngram) | `novasearch.sql` (7 tablo) |
| 8 | Senkronizasyon | çok-cihaz/çok-servis tazelik sözleşmesi | ⚠️ repo kanıtı YOK |
| 9 | Yedekleme | dump · PITR · kurtarma testi · KVKK imha | migration:14 (mysqldump talimatı) |
| 10 | Veri kalitesi | beklenti · doğrulama · anomali tespiti | ⚠️ kısmi (PHPStan/PHPUnit) |

## §3 DIŞINDA (komşuya devredilen — dokunulmaz)

| # | Ne | Nereye | Neden bu oda DEĞİL |
|---|---|---|---|
| 1 | "beğen" kelimesinin **anlamı** | K008-domain | veri saklar; kural domain'in |
| 2 | Şifreleme **anahtarı ve politikası** | K009-security | biz yalnız **çağırırız** |
| 3 | Fiziksel disk/RAID/dosya sistemi | K000-isletim-sistemi · K001-donanım | biz mantıksal modeliz |
| 4 | HTTP cache-header / CDN | K010-network · K011-application | sayfa-önbellek **veri** katmanı; transport network'un |
| 5 | Model eğitimi / özellik çıkarımı | K006-yapay-zeka | biz yalnız **girdi verisi**ni veririz |
| 6 | Metrik/telemetri **kaydı** | K017-observability | biz veriyi tutar; olay akışı onun |
| 7 | CI/CD pipeline · deploy zamanlaması | K018-devops | ⚠️ SINIR AÇIK — backup sahipliği bekliyor |
| 8 | Keşif/katalog **kuralları** | K020-discovery | ⚠️ SINIR AÇIK — search sahipliği bekliyor |
| 9 | API sözleşmesi/versiyonlama | K013-integraion | biz repository'yiz; endpoint onun |
| 10 | Kimlik **verimiz** (hesap, rol) | K0100-identitiy | veri tutulur; kimlik **anlamı** orada |

## §4 Komşularla sınır (paylaşım/karşılayıcı tablosu)

| Sınır | Ne PAYLAŞILIR | Ne PAYLAŞILMAZ | Karşılayan |
|---|---|---|---|
| K007 ↔ K008-domain | entity/kavram adları (ubiquitous language) | kural motoru, iş akışı | `data-evrensel-dil.md` |
| K007 ↔ K009-security | şifreleme **çağrısı** (hash/encrypt) | anahtar saklama, politika | security-engineer (§5.4) |
| K007 ↔ K000-isletim-sistemi | saklama yolu, dosya boyutu | disk kuyruğu, fsync politikası | os-master |
| K007 ↔ K006-yapay-zeka | veriye erişim **sözleşmesi** (repo) | eğitim, öznitelik çıkarımı | ai-master |
| K007 ↔ K020-discovery | arama **indeksi verisi** | keşif kuralları/ranking | ⚠️ AÇIK |
| K007 ↔ K018-devops | yedek **artefaktı** | zamanlama/pipeline | ⚠️ AÇIK |
| K007 ↔ K013-integraion | senkron **olay şeması** | taşıma protokolü | ⚠️ AÇIK |
| K007 ↔ K017-observability | sorgu **sayımı/hızı** | metrik deposu/alert | observability-master |

## §5 Sınır kararları (her biri gerekçeli)

### K-SCOPE-1 — "Veri bizim, anlam domain'in" (ayrım çizgisi)
- **Karar:** K007 yalnızca **nasıl saklandığını** tanımlar; **neyin ne anlama geldiğini**
  (örn. `is_active` hangi iş kuralını taşır) K008 tanımlar.
- **Gerekçe:** v2 §2.7/§2.8 cümle ayrımı — "Veritabanı" vs "İşin kuralları".
- **Alternatif:** tek domain — reddedildi, kural değişince şema değişmeye başlardı (bağlılık).
- **Risk:** `data-quality` kuralları anlam-ünitesi olabilir → K-SCOPE-5.

### K-SCOPE-2 — Çoklu-veritabanı (18 `coremusic_*` DB) korunur
- **Karar:** tek DB'ye bölme yok; her subdomain'in kendi DB'si var (canlı dump gerçeği).
- **Gerekçe:** `.ai/.sql/CLAUDE.md:31` — 18 DB · 171 tablo · canlı dump birebir.
  Ayrıca microservices "database per service" ile uyumlu (martinfowler.com/articles/microservices.html).
- **Alternatif:** monolit tek DB — reddedildi, 18 DB canlıda çalışıyor; tek DB **gerçek** olmayan bir hedef.
- **Kanıt:** `.ai/.sql/mysql/coremusic_{auth,musics,api,…}.sql` dosya listesi.

### K-SCOPE-3 — ORM yasak, PDO prepared-only (dokunulmaz)
- **Karar:** veri erişimi yalnız PDO prepared statement; ORM, `SELECT *`, string-interpolated SQL yasak.
- **Gerekçe:** `.ai/AGENTS.md` §18/7 ("ORM kullanımı → SQL injection riski"), ADR-002, `shared/AGENTS.md` §4.
- **Kanıt:** `DatabaseManager.php:32` (`prepare` + `execute`) · `ApiKeyRepository.php:29` (docblock "SELECT * yasak").
- **Alternatif:** Doctrine/Eloquent — reddedildi (güven + bağımlılık yüzeyi + §18/8).

### K-SCOPE-4 — `search` K007'de kalır (geçici)
- **Karar:** `search/` bu domainin altında; K020-discovery'e **yalnız kurallar** ait.
- **Gerekçe:** v2 §2.7 satır 284 adı K007'ye veriyor; K020-discovery `search/` alt-kaydı ayrı
  (registry §5 satır 24). İki kayıtlı `search` var → **ID CONFLICT**, tahmin edilmez.
- **Durum:** ⚠️ VERIFICATION REQUIRED — sahiplik ADR'si bekleniyor (`data-riskler.md` R-11).

### K-SCOPE-5 — Veri kalitesi = veri **gözlemi**, iş **kuralı** değil
- **Karar:** `data-quality` yalnız verinin sağlığına (null/tekrar/aralık/şema uyumu) bakar;
  iş kuralı ihlali domain'e (K008) raporlanır.
- **Gerekçe:** rol ayrımı — "beğenilemez çünkü bu bir podcast" iş kuralıdır, veri kalitesi değildir.
- **Risk:** beklentilerin bir kısmı iş-ünitesi olabilir → ADR-097 K4'te ayrıştırılır.

### K-SCOPE-6 — Backup **veri** sahipliği bizde, **operasyon** tartışmalı
- **Karar:** yedek **artefaktı** ve içeriği K007'nin; zamanlama/pipeline K018'in.
- **Gerekçe:** migration dosyası yedek **içeriğini** tarif eder (`…drop…migration.php:14-16`),
  CI zamanlaması `.github/workflows/` içindedir (devops payı).
- **Durum:** ⚠️ SINIR AÇIK — iki taraflı sahiplik ADR bekliyor.

## §6 Açık sorular (etiketler master §7 ile birebir)

| # | Soru | Etiket |
|---|---|---|
| S-1 | `search` sahipliği K007 mi K020-discovery mi? | ⚠️ VERIFICATION REQUIRED |
| S-2 | `backup` operasyonu K007 mi K018-devops mu? | ⚠️ VERIFICATION REQUIRED |
| S-3 | `synchronization` ↔ `K013-integraion/synchronization/` çift-kaydı nasıl ayrılır? | ⚠️ VERIFICATION REQUIRED |
| S-4 | 18 DB yapısı kademe-3'te bounded-context'e nasıl oturur? | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |
| S-5 | Referans lisans sınırı: MIT (Koel) / AGPL-3.0 (Ampache) — kod kopyalama sınırı? | ⚠️ VERIFICATION REQUIRED (K020-licence) |
| S-6 | Dış arama motoru (Meilisearch/Solr/ES) kullanılırsa lisans + operasyon sorumluluğu? | ⚠️ VERIFICATION REQUIRED (K-06) |

## §7 Durum

| Alan | Değer |
|---|---|
| status | draft |
| Kapsam kararı | 6 karar (K-SCOPE-1…6) — 2'si ⚠️ AÇIK |
| Komşu sınırı | 8 satır — 3'ü ⚠️ AÇIK |
| Açık soru | 6 (S-1…S-6 — master §7 ile aynı) |

---

## §8 Genişletilmiş kapsam matrisi (omurga detayı)

> Bu bölüm K007 omurgasının 10 ayağını **içinde / dışında / sınır** açısından açar.
> Her satır ya repo kanıtı ya resmi doküman (HTTP 200) ya da açıkça `⚠️ VERIFICATION REQUIRED` taşır.

### §8.1 Şema (schema) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| Tablo/kolon tasarımı, PK/FK/UNIQUE | İÇİNDE | `coremusic_catalog.sql:24-40` |
| BCNF normalizasyon denetimi | İÇİNDE (hedef) | `.ai/.sql/CLAUDE.md §1` (iddia) |
| Çok-DB topoloji kaydı (18 DB) | İÇİNDE | `.ai/.sql/mysql/` 20 dump |
| Migration **yürütme** | DIŞINDA → `migrations/` | kardeş sınır |
| İndeks **derinliği** | DIŞINDA → `indexing/` | kardeş sınır |

### §8.2 Migrasyon (migrations) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| Şema sürümleme (up/down) | İÇİNDE | `oauth_connections_migration.php:13,46` (dizi döndürür) |
| Yıkıcı migration ön-kontrol + yedek talimatı | İÇİNDE | `2026-10_07_drop_…migration.php:14-20,26-31` |
| Pipeline/zamanlama çalıştırma | DIŞINDA → K018-devops | ⚠️ sınır |
| Veri taşıma (backfill) iş mantığı | DIŞINDA → K011-application | ⚠️ sınır |

### §8.3 Erişim katmanı (repositories) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| PDO prepared statement sarmalayıcı | İÇİNDE | `DatabaseManager.php:32-43` |
| Repository sözleşmeleri | İÇİNDE | `IUserRepository.php` (20 metot) |
| Domain nesnesi/ValueObject | DIŞINDA → K008-domain | ⚠️ sınır |
| Oturum/identity doğrulama | DIŞINDA → K0100-identitiy | ⚠️ sınır |

### §8.4 Transaction (transactions) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| begin/commit/rollback sarmalayıcı | İÇİNDE | `IDatabaseManager.php:8-14` |
| ACID/MVCC uygulama | İÇİNDE (InnoDB default) | dump `ENGINE=InnoDB` |
| Deadlock **çözüm stratejisi** | İÇİNDE (tasarım) | ⚠️ repo kanıtı YOK |
| İş mantığı gruplama kararı | DIŞINDA → K012-services | ⚠️ sınır |

### §8.5 İndeks (indexing) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| B-tree/prefix/composite indeks tanımı | İÇİNDE | `idx_artist_credit_display(display_name(100))` |
| `KEY`/`UNIQUE KEY` envanteri | İÇİNDE | tüm dump'lar |
| Sorgu **planı** (EXPLAIN) okuma | DIŞINDA → ölçüm/dogrulama | §18 |
| FULLTEXT arama davranışı | DIŞINDA → `search/` | kardeş sınır |

### §8.6 Önbellek (cache) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| APCu + fallback seçimi | İÇİNDE | `CacheManager.php:12-16` |
| Sayfa önbelleği (`page_pub_`/`page_auth_`) | İÇİNDE | `PageCacheAdapter.php:11,30-31` |
| Uygulama-seviyesi cache invalidation kuralları | DIŞINDA → K011-application | ⚠️ sınır |
| Cache ölçüm/telemetri | DIŞINDA → K017-observability | ⚠️ sınır |

### §8.7 Arama (search) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| `novasearch.sql` şeması | İÇİNDE | `.ai/.sql/mysql/novasearch.sql` (147 satır) |
| Metin araması veri-yüzü | İÇİNDE (tanım) | §17 |
| Dış arama motoru operasyonu | DIŞINDA → K-06 kararı | ⚠️ sınır |
| Katalog/keşif kuralları | DIŞINDA → K020-discovery | ⚠️ sınır (S-1) |

### §8.8 Eşitleme (synchronization) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| Tazelik/twin-write tanımı | İÇİNDE (tasarım) | repo kanıtı YOK → ⚠️ |
| CDC/outbox altyapısı | DIŞINDA/UNKNOWN | master §4 satır 14 |
| K013-integraion çift-kaydı | DIŞINDA → sınır | ⚠️ S-3 |

### §8.9 Yedekleme (backup) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| Politika + KVKK soft-delete | İÇİNDE | dump'larda `deleted_at` |
| Yedek **zamanlama/çalıştırma** | DIŞINDA → K018-devops | ⚠️ sınır (S-2) |
| PITR/binlog operasyonu | DIŞINDA → K018-devops | repo kanıtı YOK |

### §8.10 Veri kalitesi (data-quality) — içinde / dışında

| Öğe | Kapsam | Kanıt |
|-----|--------|-------|
| Şema kısıtları (NOT NULL/UNIQUE/FK) | İÇİNDE | dump `CONSTRAINT` |
| Statik denetim (PHPStan) + test | İÇİNDE | `ApiKeyRepositoryTest.php` |
| Veri-beklentisi motoru (Great Expectations vb.) | DIŞINDA/UNKNOWN | repo kanıtı YOK |

---

## §9 Komşu sınır çizelgesi (tam)

| Komşu | Yön | Ne geçer | Ne GEÇMEZ | Etiket |
|-------|-----|----------|-----------|--------|
| K008-domain | → depends_on | entity/kavram tanımı | şifreleme anahtarı | TASARIM İDDİASI |
| K009-security | → depends_on | şifreleme **çağrısı** | anahtarın kendisi | TASARIM İDDİASI |
| K0100-identitiy | ↔ sınır | — | oturum/identity | ⚠️ SINIR AÇIK |
| K011-application | ← used_by | repo üzerinden veri | domain kuralı | TASARIM İDDİASI |
| K012-services | ← used_by | servis kalıcılığı | atomiklik kararı | TASARIM İDDİASI |
| K013-integraion | ↔ sınır | — | synchronization | ⚠️ SINIR AÇIK |
| K017-observability | ← used_by | — | metrik kaydı | TASARIM İDDİASI |
| K018-devops | ↔ sınır | — | yedek operasyonu | ⚠️ SINIR AÇIK |
| K019-testing | ← used_by | test verisi | veri-beklentisi | TASARIM İDDİASI |
| K020-discovery | ↔ sınır | — | arama keşfi | ⚠️ SINIR AÇIK |

---

## §10 İçinde/dışında hızlı karar (ELI15)

> "Bu dosya/plan K007'ye mi ait?" sorusunun 3 soruluk hızlı testi:

| # | Soru | Evet ise |
|---|------|----------|
| 1 | Konu **verinin nasıl durduğu / erişildiği / korunduğu** mu? | K007 İÇİNDE |
| 2 | Konu **iş kuralı / etki tepki / "beğen" anlamı** mı? | K008 (DIŞINDA) |
| 3 | Konu **zamanlama / deploy / keşif kuralı** mı? | K018/K020 (DIŞINDA) |

---

## §11 Kapsam-dışı komşu için AÇIK sınırlar (neden açık)

| Sınır | Neden açık | Kapanış koşulu |
|-------|-----------|----------------|
| S-1 search ↔ K020-discovery | iki düğüm de "arama" diyor | sahiplik ADR'si |
| S-2 backup ↔ K018-devops | politika (K007) vs operasyon (K018) | sahiplik ADR'si |
| S-3 synchronization ↔ K013-integraion | tazelik vs entegrasyon olayı | sınır kaydı |
| S-4 identity ↔ K010 | identity verisi K007'de ama kimlik K010'da | sınır kaydı |

---

## §12 Doğrulanmış resmi referanslar (kapsam kararları destek)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| DDL / şema kapsamı | https://www.postgresql.org/docs/current/ddl.html | 2026-10-13 | HTTP 200 |
| Kısıt tipleri (PK/FK/UNIQUE/CHECK) | https://www.postgresql.org/docs/current/ddl-constraints.html | 2026-10-13 | HTTP 200 |
| Repository / Data Mapper deseni | https://martinfowler.com/eaaCatalog/dataMapper.html | 2026-10-13 | HTTP 200 |
| Active Record (repository'nin karşıtı) | https://martinfowler.com/eaaCatalog/activeRecord.html | 2026-10-13 | HTTP 200 |

---

## §13 Kapsam değişiklik protokolü

| Adım | Aksiyon | Sorumlu |
|------|---------|---------|
| 1 | Yeni öğe "içinde mi dışında mı?" testi (§10) | istek sahibi |
| 2 | Komşu sınırı kontrolü (§9) | K007 sahibi |
| 3 | Kanıt ekle (repo satırı veya HTTP 200 URL) | K007 sahibi |
| 4 | `open_questions` güncelle | K007 sahibi |
| 5 | ADR gerekirse (sınır değişimi) | Vault Steward |

---

## §14 Durum özeti (bu revizyon)

| Alan | Değer |
|---|---|
| Kapsam omurga ayakları | 10 (§8.1-§8.10) |
| Komşu sınır satırı | 10 (§9) |
| Açık sınır (⚠️) | 4 (S-1…S-4) |
| Doğrulanmış URL (bu dosya) | 4 |

---

## §15 Kapsam içi omurga — kavramsal sözlük (TR↔EN)

| TR | EN | K007 karşılığı |
|----|----|----|
| Veri modelleme | Data modeling | schema |
| Normalizasyon | Normalization | schema (1NF→BCNF) |
| İndeks stratejisi | Index strategy | indexing |
| Erişim katmanı | Access layer | repositories |
| Sorgu optimizasyonu | Query optimization | indexing + ölçüm |
| Migrasyon | Migration | migrations |
| Tam/yarım yenileme | Full/incremental refresh | cache + synchronization |
| Yedekleme & kurtarma | Backup & recovery | backup |
| Veri kalitesi | Data quality | data-quality |
| Saklama politikası | Retention policy | backup + data-quality |
| Ölçüm (slow query) | Measurement | data-quality/ölçüm |

---

## §16 Kapsam matrisi — omurga × komşu (özet ızgara)

| ↓ Omurga / Komşu → | K008 | K009 | K010 | K011 | K013 | K018 | K020 |
|---|---|---|---|---|---|---|---|
| schema | veri modeli | — | — | — | — | — | — |
| migrations | — | — | — | — | — | pipeline | — |
| repositories | entity | — | identity | okur | — | — | — |
| transactions | — | — | — | atomiklik | — | — | — |
| indexing | — | — | — | — | — | — | arama |
| cache | — | — | — | invalidation | — | — | — |
| search | — | — | — | — | — | — | ⚠️ sınır |
| synchronization | — | — | — | — | ⚠️ sınır | — | — |
| backup | — | şifreleme | — | — | — | ⚠️ operasyon | — |
| data-quality | — | — | — | — | — | — | — |

---

## §17 Kapsam-dışı "yapma" listesi (bu vault kod üretmez)

| Yapma | Nereye devredilir |
|-------|-------------------|
| PHP kodu yazma | `shared/src/` (K011/K012) |
| SQL migration çalıştırma | K018-devops pipeline |
| Domain kuralı koyma | K008-domain |
| Şifreleme anahtarı üretme | K009-security |
| Arama keşif kuralı yazma | K020-discovery |
| Kimlik doğrulama akışı | K0100-identitiy |

---

## §18 Kapsam kararı geçmişi (append-only)

| Tarih | Karar | Kanıt |
|-------|-------|-------|
| 2026-10-12 | K-SCOPE-1…6 tanımlandı | §6 |
| 2026-10-13 | Omurga 10 ayak kapsam matrisi eklendi (§8) | repo + HTTP 200 |
| 2026-10-13 | Komşu sınır çizelgesi 10 satıra tamamlandı (§9) | master §5 |

---

## §19 Durum (bu genişletme)

| Metrik | Değer |
|--------|-------|
| Omurga kapsam satırı | 10 (§8.1-§8.10) |
| Komşu sınır satırı | 10 (§9) |
| Açık sınır ⚠️ | 4 |
| Doğrulanmış URL | 4 |

---

## §20 Kapsam içi omurga — örnek soru seti (her ayak için)

> Her omurga ayağı için "bu ayağın K007'de ne işi var?" sorusunun cevabı. Bu, yeni kademe-3
> (bounded-context) yazarken kapsam kaçağına karşı referanstır.

| Ayak | Tipik kapsam sorusu | Cevap |
|------|---------------------|-------|
| schema | "Bu alan tipi neden `binary(16)`?" | UUID v7 PK stratejisi |
| migrations | "Bu DDL geri alınabilir mi?" | up/down çifti zorunlu |
| repositories | "SQL neden domain'de değil?" | erişim katmanı soyutlaması |
| transactions | "Bu iki yazma atomik mi?" | begin/commit/rollback |
| indexing | "Bu sorgu fullscan yapıyor mu?" | EXPLAIN + indeks |
| cache | "Bu veri ne kadar taze?" | TTL + invalidation |
| search | "Arama kimin sahipliğinde?" | K020 sınırı (S-1) |
| synchronization | "İki servis aynı veriyi yazarsa?" | K013 sınırı (S-3) |
| backup | "Kaybolursa ne olur?" | RTO/RPO + K018 (S-2) |
| data-quality | "Veri sağlıklı mı?" | kısıt + test |

---

## §21 Kapsam ölçütleri (kabul kriteri)

| Ölçüt | Nasıl ölçülür | Durum |
|-------|---------------|-------|
| 10 omurga ayağı tanımlı | §8.1-§8.10 sayımı | ✅ 10/10 |
| Her ayağın içinde/dışında satırı | §8 tablo hücreleri | ✅ |
| Komşu sınırı 10 satır | §9 | ✅ |
| Açık sınır işaretli (⚠️) | §9 + §11 | ✅ 4 |
| Doğrulanmış URL ≠ 0 | §12 | ✅ 4 |
| Uydurma sınır yok | el ile denetim | ✅ |

---

## §22 İlişkili dosyalar (bu düğüm içi)

| Dosya | İlişki |
|-------|--------|
| `data-master.md` | §5 bağımlılık özeti bu dosyanın girdisi |
| `data-bagimliliklar.md` | yön·kırılganlık — bu dosyanın komşu detayı |
| `data-kararlar.md` | K-SCOPE-1…6 karar defteri |
| `data-riskler.md` | açık sınır → risk kaydı |
| `data-dogrulama.md` | iddia defteri (bu dosyanın ölçütleri) |

---

## §23 Kapsam-dışı açık kapılar (komşu ADR bekliyor)

| Açık kapı | Beklenen ADR | Etki |
|-----------|--------------|------|
| S-1 search sahipliği | arama sahiplik ADR'si | K007↔K020 |
| S-2 backup sahipliği | yedek sahiplik ADR'si | K007↔K018 |
| S-3 synchronization sınırı | entegrasyon sınır ADR'si | K007↔K013 |
| S-4 identity sınırı | kimlik sınır ADR'si | K007↔K010 |

---

## §24 Kapsam omurgası — hızlı referans kartı (tek bakış)

> Bu bölüm, kademe-3 yazılmadan önce **kapsam kaçağı**na karşı tek bakışta referanstır.
> Yeni bir `K007-data/<alt>/` dosyası açılırken önce bu karta bakılır.

### §24.1 Her ayak — bir cümlelik kapsam

| # | Ayak | Bir cümlelik kapsam | Sınır |
|---|------|---------------------|-------|
| 1 | schema | "Veri nasıl durur?" — tablo/kolon/kısıt/ilişki düzeni, BCNF hedefi | migration ayrı |
| 2 | migrations | "Şema nasıl değişir?" — versiyonlu, geri alınabilir, kayıtlı DDL | pipeline ayrı |
| 3 | repositories | "Veriye nasıl erişilir?" — PDO prepared, domain'den SQL saklama | domain ayrı |
| 4 | transactions | "Yazmalar atomik mi?" — begin/commit/rollback, ACID/MVCC | iş mantığı ayrı |
| 5 | indexing | "Sorgu hızlı mı?" — B-tree/prefix/composite indeks tanımı | arama davranışı ayrı |
| 6 | cache | "Sıcak veri taze mi?" — APCu + fallback, TTL, sayfa önbelleği | invalidation kuralı ayrı |
| 7 | search | "Metinde bulma" — novasearch şeması, FULLTEXT veri-yüzü | keşif kuralı ayrı (K020) |
| 8 | synchronization | "Çok servis/cihaz tazelik" — twin-write/CDC tanımı | entegrasyon olayı ayrı (K013) |
| 9 | backup | "Kaybolursa ne olur?" — politika, soft-delete, RTO/RPO | operasyon ayrı (K018) |
| 10 | data-quality | "Veri sağlıklı mı?" — kısıt, test, ölçüm | veri-beklentisi motoru UNKNOWN |

### §24.2 En sık yapılan 5 kapsam hatası (kaçın)

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | schema'ya migration yazmak | migration → `migrations/` |
| 2 | repositories'ye domain kuralı koymak | domain → K008 |
| 3 | cache'ye invalidation iş mantığı yazmak | kural → K011-application |
| 4 | search'i K020 keşfiyle karıştırmak | veri-yüzü K007, keşif K020 (S-1) |
| 5 | backup'ı operasyonel saymak | politika K007, operasyon K018 (S-2) |

### §24.3 Kapsam işaretleme sözlüğü (bu dosyada)

| İşaret | Anlam |
|--------|-------|
| İÇİNDE | K007 sahipliğinde, kanıtla |
| DIŞINDA → X | komşu düğüm X'e devredildi |
| ⚠️ SINIR AÇIK | sahiplik ADR bekliyor |
| UNKNOWN | kanıt yok, tahmin yazılmaz |

---

## §25 Kapsam genişletme günlüğü (append-only)

| Tarih | Değişiklik | Kanıt |
|-------|------------|-------|
| 2026-10-13 | §8-§25 eklendi — omurga 10 ayak kapsam matrisi + komşu sınır + hızlı referans | repo + HTTP 200 |
| 2026-10-13 | 4 doğrulanmış resmi URL eklendi (§12) | HTTP HEAD/GET 200 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
