---
node_id: "K007-data/kararlar"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-master"
  - "K007-data/data-kapsam"
  - "K007-data/data-riskler"
evidence:
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §2 (K2/K4) · §5.1-5.2 (adlandırma)"
  - "Depo: shared/src/Database/DatabaseManager.php:14-27 (K-01/K-02) · shared/src/Cache/CacheManager.php:32-40 (K-03) · shared/database/migrations/2026-10_07_drop_*.php:26-51 (K-10)"
  - ".ai/.decisions/index.md + .ai/.decisions/accepted/ (ADR-002, ADR-040, ADR-097 taslak durumu) — 2026-10-12 okundu"
validation: "2026-10-12 — 10 teknik karar + 6 kapsam kararı; her kararda durum (KARARLI/UNKNOWN), 2-3 alternatif ve ADR bağı; uydurma ADR numarası yok, yalnız diskte görülenler."
open_questions:
  - "S-6 dış arama motoru seçimi K-06'nın kapanış koşulu"
  - "S-9 CDC/outbox K-07'nin kapanış koşulu — K013-integraion'a devredildi mi"
  - "K-04 Redis kararı: APCu tek başına yeterli mi — ölçüm yok (⚠️ VERIFICATION REQUIRED)"
  - "K-SCOPE-4 cademe-3 yapısı (ADR-097 K4) K007'nin child listesini değiştirebilir"
group: mimari
---

# K007-data / kararlar — Karar Defteri

> **Durum sözlüğü:** `KARARLI` (depoda kanıt veya ADR ile kesin) · `TASLAK` (ADR henüz
> accepted değil) · `UNKNOWN` (karar açık — seçenekler yazılı, seçim yapılmadı).
> Her karar: bağlam → alternatifler → seçim → dayanak. Uydurma ADR numarası yok:
> yalnız `shared/` ve `.ai/.decisions/` diskte görülen numaralar anılır.

---

## Teknik kararlar (K serisi)

### K-01 · RDBMS = MySQL + InnoDB — `KARARLI`

- **Bağlam:** 18 `coremusic_*` veritabanı, 171 tablo (`.ai/.sql/CLAUDE.md §1`, 2026-10-07 dökümü).
- **Alternatifler:**
  1. PostgreSQL'e geçiş — güçlü JSON/rihtim desteği, ancak tüm DSN/sertifika/operasyon
     yüzeyi yeniden kurulur; depoda tek bir PG kanıtı yok.
  2. Tek-DB mimarisi (multi-DB yerine) — `.ai/.decisions/rejected/R-009-single-database-architecture.md`
     diskte **rejected** klasöründe.
  3. MyISAM — `rejected/R-008-mysql-myisam-engine.md` (rejected).
- **Seçim:** MySQL + InnoDB + `utf8mb4` (DSN `charset` parametresi `DatabaseManager.php:14-20`).
- **Dayanak:** depo kanıtı Ö-1/Ö-8; ADR-097 K2 (tek deploy-unit) ile uyumlu; PG yalnız
  "fallback" olarak askıda (ADR-097 K2 metni).

### K-02 · Erişim katmanı = PDO, ORM yasak — `KARARLI`

- **Bağlam:** Veri erişimi hangi teknolojiyle?
- **Alternatifler:**
  1. Eloquent/Laravel ORM — `rejected/R-006-laravel-eloquent-orm.md` (rejected klasörü).
  2. Doctrine benzeri ActiveRecord/entityManager — depoda iz yok.
  3. Saf PDO (seçildi).
- **Seçim:** `new \PDO` + prepared statement (`DatabaseManager.php:22-42`); kural
  ADR-002 ve `.ai/AGENTS.md` §18'de ("ORM (ADR-002), var, eval" yasak — `shared/AGENTS.md` §4 Yasak-3).
- **Dayanak:** `shared/AGENTS.md` §4 Zorunlu-1 ("PDO prepared statement yalnız; DatabaseManager
  dışında DB bağlantısı açılmaz").

### K-03 · Önbellek = APCu → Memory fallback — `KARARLI`

- **Bağlam:** Sıcak veri/HTML önbelleği nerede tutulur?
- **Alternatifler:**
  1. Redis (bkz. K-04 — açık).
  2. Dosya-tabanlı önbellek — çok-çalışanlı yazmada yarış; depoda kanıt yok.
  3. Önbellek yok — performans riski.
- **Seçim:** Adaptör sıralaması APCu → Memory (`CacheManager.php:32-40`); arayüz
  `CacheInterface` (get/set/delete/increment/clear/has).
- **Dayanak:** `shared/AGENTS.md` §2 envanteri ("src/Cache/ — Apcu, Memory, PageCache
  adapter'ları (ADR-007)").

### K-04 · Redis — `UNKNOWN`

- **Bağlam:** Dağıtık/uzun-ömürlü önbellek gerekli mi?
- **Alternatifler:** (a) Redis ekle, (b) APCu ile devam, (c) memcached.
- **Durum:** Karar **verilmedi**. Redis dokümanı tarandı
  (`https://redis.io/docs/latest/`, `data-web-arastirma.md` §2) — ama sürüm/ürüm/ihtiyaç
  ölçülmedi. **Ölçüm yok:** APCu'nun üretimde yeterliliğine dair benchmark
  `⚠️ VERIFICATION REQUIRED`.
- **Kapanış koşulu:** Bellek/yanıt süresi ölçümü + çok-çalışan senaryosu değerlendirmesi.

### K-05 · Cache invalidasyon sözleşmesi — `KARARLI` (anahtar), `TASARLANACAK` (tetikleyici)

- **Bağlam:** Ne zaman silinecek?
- **Seçim (anahtar):** sayfa anahtarları `page_pub_`/`page_auth_` + `md5(uri)`, TTL 600 sn
  (`PageCacheAdapter.php:11,28-31`).
- **Açık kalan:** veri-değişikliği → cache silme **tetikleyicisi** (hangi event, kim
  çağırır) kodda gösterilemedi — `⚠️ VERIFICATION REQUIRED`. ADR-086 event-driven mimari
  (`.ai/.decisions/accepted/ADR-086-event-driven-architecture.md`) tetikleyici için
  doğal adaydır ama K007'de bağlanmadı.

### K-06 · Dış arama motoru — `UNKNOWN`

- **Alternatifler:** (a) Meilisearch, (b) Apache Solr, (c) Elasticsearch, (d) MySQL
  FULLTEXT ile devam.
- **Durum:** Üçü de tarandı (`data-web-arastirma.md` §3); lisans ve sürüm iddiaları
  ikincil kaynak — kesinleştirilmedi. **Açık soru S-6.**
- **Kapanış koşulu:** kullanıcı onayı + lisans denetimi (K020-licence ile ortak).

### K-07 · CDC / outbox — `UNKNOWN`

- **Bağlam:** 18 DB arasında veri akışı nasıl yayınlanır?
- **Alternatifler:** (a) Debezium benzeri CDC, (b) outbox tablosu + poller, (c) uygulama
  içi doğrudan yazma (mevcut durum).
- **Dayanak:** `.ai/.decisions/accepted/ADR-100-cross-db-outbox-cqrss-semi.md` — başlığı
  "cross-db outbox" taşır (diskte kabul edilmiş ADR). ⚠️ **Metni okunmadı**; K007'ye
  bağlanan somut akış bu ADR'den türetilecek — `⚠️ VERIFICATION REQUIRED`.
- **Sınır:** K013-integraion ile ortak (S-9).

### K-08 · Yedekleme kapsamı (ne yedeklenir) — `TASLAK`

- **Seçim (kapsam):** 18 DB'nin tamamı + `.ai/.sql` dökümleri yedek kapsamındadır;
  3-2-1 kuralı hedef olarak yazılır (`data-web-arastirma.md` §9).
- **Açık:** RPO/RTO hedef değerleri **yok** — sayı yazılmadı (`UNKNOWN`).

### K-09 · Yedekleme/arama sahipliği (K007 mi komşu mu) — `UNKNOWN`

- **Çatışma:** `backup` K007 mi K17-devops mu (S-2); `search` K007 mi K020-discovery mu (S-1).
- **Geçici çözüm:** bu K-space'te her iki çocuk da **K007'de tanımlı**; operasyon/ürün
  sahipliği komşuya devredilebilir. Devir gerçekleşirse children listesi güncellenir —
  bu da **K007'de yapılacak tek yapısal değişiklik** olur.

### K-10 · Migration aracı — `KARARLI` (özel desen), alternatifler reddedilmedi

- **Seçim:** Hazır araç **yok**; kendi deseni: `$queries` dizisi + (yıkıcı durumda)
  `precheck`/`approval`/`rollback` anahtarları
  (`oauth_connections_migration.php:13-46`, `2026-10_07_drop_*.php:43-51`).
- **Alternatifler (araştırıldı, benimsenmedi):** Alembic (SQLAlchemy bağımlı — ORM yok,
  K-02 ile çelişir), Flyway, Liquibase (`data-web-arastirma.md` §4).
- **Açık standart:** iki dönüş deseni (düz liste vs anahtarlı dizi) arasındaki tek
  standart `⚠️ VERIFICATION REQUIRED` (runner K007 dışı).

---

## Kapsam kararları (K-SCOPE serisi — tanımlar `data-kapsam.md` §5'tedir, burada özet + durum)

| # | Karar (özet — tam metin `data-kapsam.md` §5) | Durum | Dayanak |
|---|---|---|---|
| K-SCOPE-1 | "Veri bizim, anlam domain'in" — K007 yalnız nasıl saklandığını tanımlar | `KARARLI` | v2 §2.7/§2.8 cümle ayrımı |
| K-SCOPE-2 | Çoklu-veritabanı (18 `coremusic_*` DB) korunur; tek-DB'ye bölme yok | `KARARLI` | `.ai/.sql/CLAUDE.md:31`; `rejected/R-009-single-database-architecture.md` |
| K-SCOPE-3 | ORM yasak, PDO prepared-only, `SELECT *` yasak | `KARARLI` | ADR-002 · `shared/AGENTS.md` §4 · Ö-1/Ö-3/Ö-5 |
| K-SCOPE-4 | `search` K007'de kalır (geçici) — K020-discovery'e yalnız kurallar ait | `UNKNOWN` (⚠️ S-1) | registry §5 satır 24 çift-kayıt gözlemi |
| K-SCOPE-5 | `data-quality` = veri gözlemi, iş kuralı değil | `KARARLI` (risk notu var) | rol ayrımı — ADR-097 K4'te ayrıştırılabilir |
| K-SCOPE-6 | Backup veri sahipliği K007'de; operasyon K018 tartışmalı | `UNKNOWN` (⚠️ S-2) | migration:14 yedek içeriği vs `.github/workflows/` zamanlama |

**Üretim kuralları (K-SCOPE değildir, bu ağacın yazım sözleşmesi):**

1. `data-master.md` adı v2/registry'den gelir, değiştirilemez (registry §5 satır 7 ·
   ADR-097 §5.4 — `data-master.md`).
2. Çocuk görev cümleleri v2'de yalnız isim olarak vardır → `⚠️ TASARIM` işareti zorunlu.
3. Ağaç yazım yüzeyi `.ai/architecture/K007-data/**` ile sınırlıdır; registry/ADR'ye
   dokunulmaz (görev manifesti).
4. MIT/AGPL referans sınırı K020-licence'ın konusudur (S-5); bu ağaçta lisans metni yazılmaz.

---

## Karar ↔ çocuk izleme

| Karar | Çocuk(lar) |
|---|---|
| K-01, K-02 | schema, repositories, transactions |
| K-03, K-04, K-05 | cache |
| K-06 | search |
| K-07 | synchronization, transactions |
| K-08, K-09 | backup |
| K-10 | migrations |
| (kalite kapıları) | data-quality, indexing (kural olarak: indeks değişikliği migration'dan geçer) |

---

## Karar verme kuralı (bu ağaç için)

1. **Disk kanıtı > ADR metni > web kaynağı > model hafızası** sıralaması bağlayıcıdır.
2. `UNKNOWN` karar **uydurularak kapatılamaz**; yalnız ölçüm/onomayla kapanır.
3. Bir karar kapanırsa ilgili `open_questions` satırı ve `data-dogrulama.md` karşılık
   satırı **birlikte** güncellenir (tek başına değişiklik yasak).
4. Reddedilmiş alternatifler varsa `.ai/.decisions/rejected/` dosya adıyla anılır —
   numara uydurulmaz (R-001..R-010 diskte mevcut).
