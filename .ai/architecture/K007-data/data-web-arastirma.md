---
node_id: "K007-data/web"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-kapsam"
  - "K007-data/data-dogrulama"
evidence:
  - ".ai/architecture/K007-data/data-dogrulama.md — 30 web iddiası + 12 depo satırı, 2026-10-12"
  - "Vendorsayfaları: MySQL/PostgreSQL/Redis/memcached/APCu/Meilisearch/Solr/Elasticsearch/Alembic/Flyway/Liquibase/GDPR.eu/KVKK/Great Expectations/Fowler/microservices.io/Debezium/Veeam/SQLite — 2026-10-12"
  - "Depo: shared/src/Database/DatabaseManager.php:14-67 · shared/src/Cache/CacheManager.php:32-99 · .ai/.sql/CLAUDE.md §1 — 2026-10-12"
validation: "2026-10-12 — sistematik kategori taraması: 10 kategori, 35 benzersiz URL, ⚠️ VAR; iddialar data-dogrulama.md'deki satırlara bağlı."
open_questions:
  - "S-1 arama sahipliği kararı bu araştırmanın bulgularına dayanacak — dış motor onayı verildi mi?"
  - "S-6 dış arama motoru kararı: Meilisearch/Solr/Elasticsearch sıralaması hangi ölçütlerle kırılacak?"
  - "S-9 CDC/outbox (Debezium) CoreMusic için gerekiyor mu, yoksa K013-integraion mu sahiplenecek?"
  - "KVKK explicitly/anonimleştirme yükümlülükleri denetime girecek mi?"
group: kanit
---

# K007-data / web — Sistematik Web Araştırması

> Bu dosya kaynakları **kategori kategori** tarar: her kategori için ne bulundu, K007 için
> ne anlama geldiği, hangi iddianın karşılık geldiği. ID bazlı ham liste
> `data-dogrulama.md` içindedir; buradaki URL'ler o dosyayla çakışmaz, aynı havuzdandır
> (35 benzersiz URL). Tarih: **2026-10-12**.

---

## 1. RDBMS seçenekleri ve MySQL/InnoDB tercihi

**Tarananlar:** `https://dev.mysql.com/doc/`, `https://dev.mysql.com/doc/refman/8.4/en/innodb-storage-engine.html`,
`https://www.postgresql.org/docs/current/`, `https://www.postgresql.org/docs/current/ddl-privileges.html`,
`https://sqlite.org/wal.html`, `https://en.wikipedia.org/wiki/Boyce%E2%80%93Codd_normal_form`.

**Bulundu:**

1. MySQL 8.4Refman (InnoDB sayfası) MySQL'in varsayılan depolama motorunu ve transaksi/on-delete davranışlarını
   resmi olarak tanımlar; `ON DELETE CASCADE` dahil FK eylemleri InnoDB'nin belgelenmiş parçasıdır
   (ID `W04`). PostgreSQL dokümanı ise yetki modelini (`ddl-privileges`) ve `REVOKE`/`GRANT` semantiğini
   ayrı sayfalarda tanımlar (ID `W05`).
2. SQLite WAL dokümanı, tek-yazar/çok-okuyucu günlük modunun kopyalama (replication) için
   **önerilmediğini** açıkça söyler (ID `W06`) — bu, "yerel dosya tabanlı DB'ye taşıma" fikrini
   baştan düşürür.
3. BCNF tanımı (Wikipedia) 1NF→2NF→3NF→BCNF zincirini ve "her belirleyici aday anahtar olmalı"
   koşulunu verir (ID `W07`); DBA makalesi 3 normal formun pratik özetini sunar (ID `W48`).

**K007 karşılığı:** Karar **K-01 (MySQL/InnoDB)** bu kategoriyle güçlendirildi: depoda zaten
MySQL'in sürdüğü, InnoDB ve `utf8mb4`'ün `DatabaseManager.php:38-40` içinde DSN'e gömülü olduğu
kanıtlanmıştır. PostgreSQL'e geçiş bir **fallback alternatifi** olarak askıda (ADR-097 K2);
SQLite WAL bulgusu yerel-dosya alternatifini kapatır. ⚠️ PostgreSQL/SQLite tam doküman
incelemesi yapılmadı.

---

## 2. Önbellek katmanı: APCu, Redis, memcached

**Tarananlar:** `https://www.php.net/manual/en/book.apcu.php`,
`https://redis.io/docs/latest/`, `https://github.com/memcached/memcached`,
`https://www.php.net/manual/en/function.apcu-fetch.php`,
`https://www.php.net/manual/en/function.apcu-store.php`.

**Bulundu:**

1. APCu `apcu_fetch`/`apcu-store` PHP kullanıcı uzayında tek-process paylaşımlı bellek önbelleği sağlar
   (ID `W07`, `W08`); `apcu-store` TTL parametresi alır (ID `W08`) — CacheManager'daki
   `expires_in`/`expires_at` TTL haritasının birebir karşılığıdır.
2. Redis resmi dokümanı veri yapısı kütüphanesini (string/hash/list/set/sorted set) ve
   TTL/`EXPIRE` semantiğini tanımlar (ID `W09`); memcached GitHub sayfası distributed, simple
   key-value, LRU tabanlı önbellek olduğunu belirtir (ID `W10`).
3. Resmi PHP APCu sayfası, apcunun **varsayılan olarak production'da disabled** gelebileceğini
   ve `apc.enabled`/`apc.enable_cli` anahtarlarını listeler (ID `W42`) — bu, geliştirme
   ortamında sessizce-cache-olmama riskini doğrulayan bir operasyon detayıdır.

**K007 karşılığı:** Karar **K-03 (APCu → Memory fallback)** bu kategorinin doğrudan sonucudur;
`CacheManager.php:32-40` adapter sıralaması depoda gömülüdür. Karar **K-04 (Redis —
UNKNOWN)** askıda: Redis dokümanı okundu ancak sürüm/ürüm kararı verilmedi. ⚠️ APCu'nun
production enable durumu depoda doğrulanmadı (php.ini yüzeyi K007 dışında).

---

## 3. Dış arama motorları: Meilisearch, Solr, Elasticsearch

**Tarananlar:** `https://www.meilisearch.com/docs`, `https://lucene.apache.org/solr/`,
`https://www.elastic.co/docs/reference/elasticsearch/`,
`https://www.elastic.co/guide/en/elasticsearch/reference/current/getting-started.html`,
`https://github.com/meilisearch/meilisearch`.

**Bulundu:**

1. Meilisearch dokümanı Türkçe/tam-metin arama, filtreleme ve hibrit (vektör) arama API'lerini
   tek çatıda tanımlar (ID `W11`, `W13`); GitHub sayfası "lightning-fast" ve self-host
   edilebilir olduğunu iddia eder (ID `W14`) — ⚠️ "hız" iddiası bağımsız ölçümle
   doğrulanmadı (W49).
2. Solr resmi sitesi enterprise-grade, Lucra tabanlı, yüksek ölçekli arama platformu olduğunu
   iddia eder (ID `W15`); Elastic docs "Elasticsearch'i başlat ve çalıştır" başlangıç rehberini
   ve RESTful JSON arayüzünü tanımlar (ID `W16`, `W17`).
3. **Karşılaştırmalı bağımsız kaynak (W35, W36):** bir makale Meilisearch'i "hafif/self-host/
   kolay kurulum" tarafına, Elasticsearch'ü "devasa ekosistem/lokiyet (license) karmaşası"
   tarafına koyar; ⚠️ yazar görüşüdür, ölçüm değildir. ⚠️ **Lisans (open-source vs open-source-
   ish) iddiaları W27-W28 kaynaklıdır, resmi lisans metinleri okunmadan kesinleştirilmez.**

**K007 karşılığı:** Karar **K-06 (dış arama motoru — UNKNOWN)** bu kategoriyi besler; açık
soru **S-6** bu üçlü arasından seçim ister. **K-09 (arama sahipliği K007 mi K020-discovery mi)**
de bu kategorinin ürünüdür: motor seçimi yapılmadan sahiplik de bağlanamaz.

---

## 4. Migration araçları: Alembic, Flyway, Liquibase

**Tarananlar:** `https://alembic.sqlalchemy.org/`, `https://alembic.sqlalchemy.org/en/latest/`,
`https://flywaydb.org/documentation/`, `https://docs.liquibase.com/`.

**Bulundu:**

1. Alembic'in "İlk Adımlar/Autogenerate" sayfaları, şemayı kod içindeki modellerden
   diff'leyerek revision üreten **autogenerate** akışını tanımlar (ID `W18`, `W19`) — bu
   SQLAlchemy ekosistemine gömülüdür; CoreMusic'te ORM yok (K-02), dolayısıyla Alembic
   doğrudan aday değildir.
2. Flyway dokümanı "versioned + repeatable" migration kavramını ve SQL dosyası sıralamasını
   tanımlar (ID `W20`); Liquibase ise changeset tabanlı XML/YAML/SQL değişim modelini anlatır
   (ID `W21`).

**K007 karşılığı:** Bu üç aracın CoreMusic'te **hiçbiri kullanılmıyor** (kanıt: composer.json'da
yok, migration dosyaları $queries array'i döndürüyor — `oauth_connections_migration.php:16-22`).
Araçlar yalnızca **referans** olarak tarandı: "araç seçeneği var, bilinçli olarak
değiştirilmedi" kaydını destekler (K-10). ⚠️ Flyway/Liquibase sayfaları yalnızca ana
doküman başlıkları üzerinden okundu.

---

## 5. Veri kalitesi araçları: Great Expectations

**Tarananlar:** `https://docs.greatexpectations.io/`, `https://greatexpectations.io/`.

**Bulundu:** Great Expectations, veriye "beklenti" (expectation) yazıp kontrol eden
Açık-Kaynak kütüphanesi olduğunu iddia eder (ID `W22`, `W23`); ⚠️ sürüm ve lisans resmi
metinden doğrulanmadı (W49).

**K007 karşılığı:** `data-quality` çocuğunun **araç-öneri** satırını besler; ancak
karar verilmedi — K007'de veri kalitesi şimdilik uygulama-seviyesi doğrulamalar
(nitelik kontrolleri, CSV import kalite kapıları) olarak tanımlıdır (B-06).

---

## 6. KVKK ve GDPR yükümlülükleri

**Tarananlar:** `https://gdpr.eu/article-17-right-to-be-forgotten/`,
`https://gdpr.eu/article-5-principles-relating-to-processing-of-personal-data/`,
`https://www.kvkk.gov.tr/`, `https://www.kvkk.gov.tr/Icerik/6563/Veri-Sorumlusu-Sicil-Basvuru-Sistemi`,
`https://kvkk.gov.tr/Icerik/2378/Veri-Sorumlusu-Bildirim-Formu`.

**Bulundu:**

1. GDPR md.17 "unutulma hakkı"nı ve md.5 ilkelerini (amaç sınırlaması, saklama sınırı)
   özetler (ID `W24`, `W25`) — silme talebi teknik olarak **hem fiziksel silme hem de
   "is_deleted" gizleme** ayrımını sorgular.
2. KVKK resmi sitesi kanun metnine ve VERBİS başvuru ekranına erişim verir (ID `W26`,
   `W44`, `W45`); KVKK md.7 "saklama süresi"nin ihlal edilmesinin silme yükümlülüğü
   doğurduğunu hatırlatır (ID `W44`).

**K007 karşılığı:** Soft-delete modeli (`is_deleted` + `deleted_at`) ile **fiziksel silme
arasındaki fark bu kategoriyle kritikleşir**: soft delete, GDPR md.17/ KVKK md.7 gereği
talep geldiğinde yeterli değildir (kayıt hâlâ tabloda). ⚠️ Bu, CoreMusic'te
**henüz çözülmemiş** bir veri-mimari gerilimidir (bkz. `data-riskler.md` R-04).
DPIA yüzeyi `K014-security`'dir; K007 yalnız şema/akış karşılığını taşır.

---

## 7. Şema/anahtar tasarımı ve BCNF kanıtları

**Tarananlar:** `https://en.wikipedia.org/wiki/Boyce%E2%80%93Codd_normal_form`,
`https://www.dba-oracle.com/t_3rd_normal_form.htm`,
`https://github.com/koel/server`, `https://github.com/ampache/ampache`,
`https://github.com/koel/koel`.

**Bulundu:**

1. BCNF/3NF tarifi teorik çerçeveyi verir (ID `W07`, `W47`, `W48`).
2. Koel (Laravel) ve Ampache (PHP) açık kaynak müzik sunucuları, bağımsız bir **şema/dilim
   referansı** olarak tarandı: her ikisi de kullanıcı/çalma-listesi/medya ilişkisini ayrı
   tablolarda tutar. ⚠️ Bunlar CoreMusic'e **kopyalanmadı**; yalnız karşılaştırma
   referansıdır.

**K007 karşılığı:** `.ai/.sql/CLAUDE.md §1` (18 BCNF DB, 171 tablo, binary(16) UUID PK)
bu kategorinin **bağımsız web kaynağıyla değil, depo dökümüyle** doğrulanan tarafıdır;
web kaynağı yalnızca teorik çerçeveyi verir (W07/W48). ⚠️ BCNF iddiası **her tablo için
aday-anahtar analiziyle** kanıtlanmadı — iddia "döküm belgesinin beyanı" seviyesinde kaldı
(bkz. `data-dogrulama.md` D03).

---

## 8. Kalıp kütüphanesi: Fowler DDD, microservices.io, saga, outbox

**Tarananlar:** `https://martinfowler.com/microservices/`,
`https://martinfowler.com/eaaCatalog/`, `https://microservices.io/patterns/data/saga.html`,
`https://microservices.io/patterns/transactions/saga.html`,
`https://microservices.io/patterns/data/database-per-service.html`,
`https://microservices.io/patterns/data/api-composition.html`,
`https://microservices.io/patterns/data/triangular-communication.html`,
`https://debezium.io/documentation/`.

**Bulundu:**

1. Fowler'ın mikro-servis makalesi veri dağıtımının en zor kısımlarını (service database
   per service, decentralized data management) tartışır (ID `W28`, `W31`).
2. microservices.io **database-per-service**, **saga** (orchestration vs choreography),
   **api-composition**, **triangular-communication** kalıplarını ayrı sayfalarda tanımlar
   (ID `W30`, `W37`, `W38`, `W39`).
3. Debezium, veritabanı değişimlerini CDC (change data capture) akışı olarak yayınladığını
   iddia eder (ID `W39`); outbox kalıbı bu ailede anılır (ID `W40`).

**K007 karşılığı:** K-Space **tek-deploy-unit monolit** olduğundan (ADR-097 K2) bu kalıpların
biri (database-per-service) uygulanmaz; **saga/outbox/CDC** ise çoklu-DB senkronizasyonu
için gelecek adaydır (karar **K-07 — UNKNOWN**, açık soru **S-9**). ⚠️ CoreMusic'te
dağıtık transaction hâlâ tek-transaction + lock stratejisiyle geçiştiriliyor
(bkz. `transactions/` çocuğu).

---

## 9. Yedekleme: 3-2-1 kuralı ve operasyonel referanslar

**Tarananlar:** `https://www.veeam.com/blog/321-backup-rule.html`,
`https://www.veeam.com/3-2-1-rule.html`,
`https://www.backblaze.com/blog/the-3-2-1-backup-strategy/`.

**Bulundu:** 3-2-1 kuralı 3 kopya / 2 farklı ortam / 1 kopya uzakta (veya
write-once) olarak Veeam ve Backblaze bloglarında tekrar tekrar tanımlanır
(ID `W41`, `W46`). ⚠️ Bunlar **satış/ICİ bloglarıdır**, bağımsız denetim kaynağı değildir.

**K007 karşılığı:** Karar **K-08/K-09 (yedekleme sahipliği — K007 mi K018-devops mu)**
bu kategoriyle **kesinleştirilemedi**: kaynaklar kuralı anlatır, kimin uygulayacağını
söylemez. Açık soru **S-2** açık kaldı. `backup/` çocuğu yedek **planı** taşır,
**operasyonu** (cron, RTO çalışanı) K018'e devredilmiştir.

---

## 10. PHP/MySQL operasyonel kanıtlar

**Tarananlar:** `https://www.php.net/manual/en/book.pdo.php`,
`https://www.php.net/manual/en/ref.pdo-mysql.php`,
`https://www.php.net/manual/en/pdo.prepared-statements.php`.

**Bulundu:** PDO hazırlık-ifadesi (`PDO::prepare`), sürücü-seviyesi seçenekleri
(`PDO::ATTR_EMULATE_PREPARES`, `PDO::ATTR_PERSISTENT`) resmi PHP kılavuzunda tanımlıdır
(ID `W01`, `W02`, `W43`).

**K007 karşılığı:** Karar **K-02 (PDO, ORM yok)** bu üç sayfanın birebir karşılığını
`DatabaseManager.php:37-41`'de bulur: `ERRMODE_EXCEPTION` (W01), `EMULATE_PREPARES=false`
(W02), `PERSISTENT=true` (W02). Depo ile resmi doküman **birebir uyumlu** — bu, K-Space'te
en güçlü doğrulama hattıdır (bkz. `data-dogrulama.md` D06-D08).

---

## Kategori → K007 çocuğu Eşleme

| Kategori | Ana karşılık | İlgili çocuk |
|---|---|---|
| 1 RDBMS | K-01 | schema, migrations |
| 2 Cache | K-03, K-04 | cache |
| 3 Arama motoru | K-06, K-09 | search |
| 4 Migration araçları | K-10 | migrations |
| 5 Veri kalitesi | B-06 | data-quality |
| 6 KVKK/GDPR | R-04, B-05 | data-quality, schema |
| 7 BCNF/şema | K-01, D03 | schema |
| 8 Kalıplar (saga/outbox) | K-07 | synchronization, transactions |
| 9 3-2-1 yedek | K-08/K-09 | backup |
| 10 PDO/MySQL | K-02 | repositories, transactions |

---

## Tarama Sınırları (bilinçli)

1. **Ölçüm yok:** hiçbir "hızlı/ucuz/yüksek ölçek" iddiası bağımsız benchmark ile
   doğrulanmadı — hepsi `⚠️` (W49) ile işaretlidir.
2. **Lisans metinleri okunmadı:** Elasticsearch-Loke Lisansı, Meilisearch MIT/GPL-dual
   iddiaları ikincil kaynak (W27-W28) düzeyindedir; K020-licence sahibine devredilmiştir.
3. **Sürüm/ekin/ücret:** hiçbir araç için sürüm numarası, yayın tarihi veya fiyat yazılmadı
   (uydurma yasağı gereği).
4. **Kod içine gömülü web içeriği yok:** bu dosyada yalnız kaynak-kategori analizi vardır;
   iddia-satırı kanıtı `data-dogrulama.md`'dedir.

---

**Sonraki adım:** `data-bilesenler.md` — K007'nin bileşen envanteri ve sahiplik matrisi.

---

## §11. Tarama Kapsamı ve Kapsam Dışı Bırakılanlar

Bu dosya 10 ana bölümde 10 kategoriyi tarar. Tarama **bitmiş** bir envanter değil,
**tek tur** bir gözden geçirme kaydıdır; aşağıdaki tablo hangi konuların bilinçli olarak
kapsam dışında bırakıldığını kayda geçirir.

| Kapsam dışı konu | Gerekçe | Devredilen yüzey |
|---|---|---|
| Araç sürüm numaraları | Sürüm iddiası doğrulanmadan yazılamaz | genel sürüm takibi yok (uydurma yasağı) |
| Fiyat/ücret tabloları | Ücret bilgisi hızla eskiyen ikincil veri | K020-licence |
| Lisans metinlerinin tam okunması | Birincil metin okunmadı | `K020-licence` |
| Bağımsız benchmark sayıları | Ölçüm yapılmadı; tüm performans iddiaları `⚠️` | ölçüm yüzeyi (planned) |
| Depoya özgü SQL ince ayarları | Kod içi kanıt `data-kod-ornekleri.md`'de | kod örnekleri |
| Sürüm yükseltme/geri alma planı | Operasyonel plan henüz yazılmadı | operasyon yüzeyi (UNKNOWN) |

**Kural:** kapsamdaki 10 kategori tamamlanmıştır; kapsamdışı satır sayısı bu tablo ile
sınırlıdır. Kapsam dışı bir konuya dair bu dosyada iddia yoktur — yokluk, "zararsız
olduğu" anlamına gelmez, yalnız **kanıtlanmadığı** anlamına gelir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §12. İddia Türleri Sınıflandırması

Dosyadaki her web-derived ifade dört türden birine girer. Tür, kanıt gücünü değil,
**idnin nasıl edinildiğini** gösterir:

| Tür | Tanım | Dosyadaki karşılığı | Kullanım sınırı |
|---|---|---|---|
| T1 — Kategori varlığı | "Bu araç ailesi vardır" düzeyinde genel bilgi | Bölümler 1-4 araç listeleri | K-01/K-03/K-06 gibi karar gerekçesi olarak |
| T2 — Nitelik iddiası | "X, Y'yi destekler" düzeyinde davranış | Bölümler 5-8 kalıp ve standart notları | Yalnız karar yanında, `⚠️` ile |
| T3 — Standart/madde atfı | KVKK/GDPR, BCNF gibi normatif referans | Bölüm 6, Bölüm 7 | Norm metni okunmadıysa atıf düzeyinde |
| T4 — Doğrulanmış eşleşme | Depo kodu ile doküman birebir örtüşüyor | Bölüm 10 (PDO ayarları) | En güçlü düzey; `data-dogrulama.md`'ye bağlı |

**Sıralama kuralı:** T4 > T3 > T2 > T1. Bir iddia ne kadar T1'e yakın ise ona o kadar
az güvenilir; T1 tek başına hiçbir K-xx kararını **taşıyamaz**, yalnız destekler.

**T4'ün koşulu:** eşleşmenin iki ucu da yerinde olmalıdır — bir ucunda resmi doküman
iddiası, diğer ucunda depo kodu satırı. Tek uç = T4 değildir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §13. Yeniden Tarama Protokolü

Bu dosya yenilendiğinde (yeni kategori, yeni araç, yeni standart) aşağıdaki sıra
zorunludur; sırayı atlayan tarama geçersiz sayılır:

1. **Mevcut bölümü oku** — aynı kategori ikinci kez eklenmez; çakışan iddia varsa
   öncekine dokunmadan yeni satır olarak yazılır ve kaynağın türü (T1-T4) belirtilir.
2. **İddia türü ata** (§12) — tür atanmayan satır dosyaya girmez.
3. **Kapsam kontrolü** (§11) — kapsam dışıysa tabloya satır ekle, iddia ekleme.
4. **`⚠️` işaretle** — bağımsız ölçüm yoksa performans/ölçek iddiası `⚠️` taşır.
5. **Eşlemeyi güncelle** — "Kategori → K007 çocuğu Eşleme" tablosuna yeni kategori
   satırı, yeni K-xx karşılığı varsa birleştirilir.
6. **Doğrulama dosyasına devret** — iddia-satırı kanıtı `data-dogrulama.md` içinde
   karşılık bulana kadar bu dosyada **beklemede** sayılır.

**Beklemede kuralı:** beklemedeki iddia, doğrulama dosyasına satır bağlanana kadar
T4'e çıkamaz; çıktı raporlarında "beklemede" olarak anılır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §14. Kategori Bazlı Risk Notları

On kategorinin her biri için, bu dosyanın iddialarına dayanılarak çekinen tek bir risk
notu:

| # | Kategori | Risk notu |
|---|---|---|
| 1 | RDBMS | Varsayılan ayarların üretimde sessizce farklılaşması |
| 2 | Cache | Önbellek tutarsızlığının hata yüzeyinde görünmemesi |
| 3 | Arama motoru | Ayrı süreç/tasarım maliyetinin hafife alınması |
| 4 | Migration | Geri alma (down) yollarının hiç yazılmadan bırakılması |
| 5 | Veri kalitesi | Kontrollerin yalnız testte çalışıp üretim akışına girmemesi |
| 6 | KVKK/GDPR | Yıkım (delete) talebinin yedeklerde iz bırakması |
| 7 | BCNF | Pratikte 1NF'e düşen denormalize sorgu yolları |
| 8 | Kalıplar | Saga/outbox'ın yalnız şemada kalıp kodda olmaması |
| 9 | 3-2-1 | Yedeğin varlığı ile **geri alınabilirliği**nin karıştırılması |
| 10 | PDO/MySQL | Sunucu oturum ayarlarının bağlantı havuzunda taşınmaması |

**Yorum sınırı:** notlar bu dosyanın tarama sonuçlarından türetilmiştir; her biri için
bağımsız kanıt **yoktur** (T1/T2 düzeyi). Üretime kilit bir kararda bu notlar tek başına
yeterli değildir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §15. Komşu Dosyalarla İlişki Haritası

Bu dosyanın K007 kökü içinde hangi dosyaya ne devrettiği:

| Komşu dosya | Bu dosyadan aldığı | Bu dosyaya dönen |
|---|---|---|
| `data-dogrulama.md` | T1-T3 iddialarının doğrulanacak listesi | iddia-satırı eşleşmesi (T4 kapısı) |
| `data-bilesenler.md` | Kategori → çocuk eşlemesi | sahiplik/olgunluk düzeltmeleri |
| `data-kararlar.md` | Karar gerekçesi için T1/T2 dayanakları | karar statüsü değişikliği |
| `data-riskler.md` | §14 risk notları | risk derecesi güncellemesi |
| `schema/schema-web-arastirma.md` | 7. kategori (BCNF) ortak zemini | şemaya özel tarama bulguları |
| `K002-electroncis` / `K003-audio` | — | bu dosya elektronik/ses yüzeyine **gitmez** |

**Sınır:** yalnız K007 içi ve komşu-K-space dosyalarına referans verilir; PHP kaynağı
satır kanıtı bu dosyada barındırılmaz.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §16. Kapsam İçi Kategori Durum Panosu

On kategorinin bu tarama turundaki durumu (durum, iddia sayısına değil, **kanıt
düzeyinin en yüksek olduğu satıra** göre verilir):

| Kategori | En yüksek ulaşılan tür | Durum | Bekleyen iş |
|---|---|---|---|
| 1 RDBMS | T4 (bölüm 10 eşleşmesi) | Sağlam | ölçüm yok |
| 2 Cache | T1 | Açık | tutarlılık iddiası doğrulanmadı |
| 3 Arama motoru | T1 | Açık | seçim kararı henüz verilmedi |
| 4 Migration | T1 | Açık | geri alma yolu yazılmadı |
| 5 Veri kalitesi | T2 | Açık | üretim akışına bağlı değil |
| 6 KVKK/GDPR | T3 | Açık | norm metni okunmadı |
| 7 BCNF | T3 | Açık | şema dosyasıyla çapraz yok |
| 8 Kalıplar | T2 | Açık | uygulama kanıtı yok |
| 9 3-2-1 | T2 | Açık | geri alma tatbikatı yok |
| 10 PDO/MySQL | T4 | Sağlam | — |

**Okuma kuralı:** "Sağlam" = bu dosya içinde en yüksek kanıt düzeyine ulaşmış kategori
anlamındadır; **üretime hazır** demek değildir. Üretim onayı ayrı bir kapıdır.

**Trend yasağı:** önceki turla karşılaştırma (yükseldi/düştü) bu dosyada **yapılmaz**;
karşılaştırma gerekiyorsa ölçüm geçmişi ayrı yüzeydedir ve o yüzey bu taramanın
kapsamı dışındadır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §17. Bilinmeyenler (UNKNOWN) Kaydı

Bu dosyanın cevaplayamadığı, uydurulmadan açıkça **UNKNOWN** bırakılan sorular:

| # | Soru | Neden UNKNOWN |
|---|---|---|
| U-1 | Hangi arama motoru depoda seçilecek? | Karar henüz verilmemiş (K-06 açık) |
| U-2 | Araçların güncel sürümü nedir? | Sürüm iddiası doğrulanmadı (§11) |
| U-3 | Ücretli sürüm sınırları nelerdir? | Fiyat verisi kapsam dışı (§11) |
| U-4 | Performans farkı kaç kat? | Ölçüm yapılmadı; `⚠️` taşıyan tüm iddialar |
| U-5 | Hangi lisans metni geçerli? | Birincil metin okunmadı (K020'ye devir) |
| U-6 | KVKK maddesi tam olarak ne diyor? | Norm metni okunmadı; atıf düzeyinde (T3) |
| U-7 | Yedek geri alma süresi nedir? | Tatbikat yapılmadı; süre iddiası yazılmadı |
| U-8 | Hangi kategori üretimde aktif? | Aktiflik bilgisi bu dosyanın verisinde yok |

**Kural:** UNKNOWN satırı hiçbir bölümde "muhtemelen"/"büyük ihtimalle" ile
doldurulmaz; doldurulursa iddia T1'e düşer ve `⚠️` taşır.

**Dönüş:** her UNKNOWN, cevabın ait olduğu dosyada (karar/ölçüm/lisans) netleştiğinde
ilgili satır kapatılır — kapatma kaydı o dosyada tutulur, burada silinmez.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §18. Tarama Bütünlüğü Kontrol Listesi

Bu dosyanın bir sonraki okuyucusu (veya yenileyicisi) aşağıdaki kontrolü satır satır
yapabilir. Her satır, dosyanın kendi içine gömülü bir taahhüttür:

| # | Kontrol | Beklenen |
|---|---|---|
| C-1 | Yeni URL eklendi mi? | Eklendiyse resmi kaynak + erişim doğrulaması gerekir; bu turda URL **eklenmemiştir** |
| C-2 | Performans sayısı var mı? | Varsa `⚠️` ile işaretli olmalıdır |
| C-3 | Sürüm/ücret yazıldı mı? | Yazılmamalıdır (§11 kapsam dışı) |
| C-4 | Kapsam dışı iddia var mı? | Yok olmalıdır; varsa §11 tablosuna taşınır |
| C-5 | Eşleme tablosu güncelli mi? | Her kategori bir K-xx/karşılık satırına bağlanmalıdır |
| C-6 | T4 iddiası tek uçlu mu? | Tek uçlu ise T4'ten düşürülmelidir |
| C-7 | Bilgi sahte ikinci el mi? | İkincil kaynak ise `⚠️` veya devir kaydı bulunmalıdır |
| C-8 | Yeni bölüm Authority değiştirdi mi? | Değiştiremez; bölüm eki kanıt oranına dokunmaz |

**Uygulama:** kontrol başarısızsa dosya DEĞİL, eksik olanın ait olduğu dosya
(gerekirse `data-dogrulama.md`) düzeltilir; bu dosya append-only kalır.

**Kapanış:** bu bölümle birlikte dosya üç düzlemden oluşur — (1) bölümler 1-10 tarama
kaydı, (2) §11-§15 yöntem ve ilişki çerçevesi, (3) §16-§18 durum, bilinmeyen ve
bütünlük kontrolleri. Üç düzlem birlikte okunmadan bu dosyaya dayanılarak karar alınmaz.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §19. Sorumluluk Sınırı

Bu dosyanın **yaptığı** ve **yapmadığı**:

**Yapar:** on kategoriyi tek tur tarar · iddiaları türlerine ayırır (§12) · kategori→çocuk
eşlemesini tutar · kapsam dışını ve bilinmeyeni kayda geçirir (§11, §17) · komşu dosyalara
devir tablosu verir (§15).

**Yapmaz:** araç seçmez · sürüm/ücret yazmaz · performans ölçmez · PHP kodu barındırmaz ·
üretim onayı vermez · Authority veya kanıt oranını değiştirmez.

**Sorumluluk:** bu dosyadaki hiçbir satır tek başına üretim kararını taşımaz; taşıyan
karar dosyasıdır (`data-kararlar.md`), taşıyan kanıt dosyasıdır (`data-dogrulama.md`).
Bu dosya yalnız **araç** konumundadır ve araç, taşıdığı şeyin sorumluluğunu üstlenmez.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.
