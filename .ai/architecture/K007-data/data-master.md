---
title: "K007-data — data-master (K-Space V3)"
type: architecture-master
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 2.0.0
node_id: K007-data
node_type: master
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
  - "K008-domain (entity/kavram tanımı — §5)"
  - "K009-security (şifreleme çağrısı — §5)"
  - "K000-isletim-sistemi (fiziksel disk/dosya — §5)"
  - "K011-application · K012-services (repo üzerinden veri talebi — §5 used_by)"
  - "K013-integraion (⚠️ sınır — synchronization çift kaydı)"
  - "K17-devops (⚠️ sınır — yedek operasyonu)"
  - "K021-recomenataion (⚠️ sınır — arama keşfi)"
evidence:
  - ".ai/architecture/index.md §5 satır 7 (master adı) + §7-§8 — 2026-10-12"
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §2 (K2/K4) + §5.1-5.2 (adlandırma) — 2026-10-12"
  - "Depo: shared/src/Database/DatabaseManager.php:14-67 · shared/src/Cache/CacheManager.php:32-99 · .ai/.sql/CLAUDE.md §1 — 2026-10-12"
validation: "2026-10-12 — v2 §2.7 rol cümlesi + registry §5 + 10 çocuk disk enumerate ✅ · 2026-10-15 — §3 çocuk tablosu 10/10 index.md linklendi; 10 çocuk × 6 MD = 60 dosya disk enumerate ✅"
open_questions:
  - "S-1 arama sahipliği: K007 search mi K020-discovery mi?"
  - "S-2 yedekleme sahipliği: K007 backup mı K17-devops mu?"
  - "S-3 senkronizasyon sınırı: K007 synchronization mı K013-integraion mı?"
  - "S-4 kademe-3 yapısı ADR-097 K4 ile sabitlenecek (TASLAK ADR)"
  - "S-5 MIT/AGPL referans sınırı (K020-licence kaydı bekleniyor)"
  - "S-6 dış arama motoru seçimi (K-06) onaya bağlı"
authority: "SSOT: .ai/architecture/index.md"
related:
  - "index.md"
  - "data-kapsam.md"
  - "data-bagimliliklar.md"
  - "data-dogrulama.md"
  - "data-web-arastirma.md"
  - "data-bilesenler.md"
  - "data-kod-ornekleri.md"
  - "data-kararlar.md"
  - "data-riskler.md"
  - "data-test-stratejisi.md"
  - "data-evrensel-dil.md"
group: mimari
---

# data-master — K007-data

> Master adı **v2'den birebir**: `data-master.md` (ADR-097 §5.4 — değiştirilmez).
> Bu dosya K007-data'nın **anayasası**dır: kimlik · sınırlar · kademe-2 · sorumluluk
> matrisi · teknoloji gerçeği · okuma zinciri.

## §0 Dosya seti ve rolleri

| # | Dosya | İş |
|---|---|---|
| 1 | `index.md` | künye — 8 zorunlu alan (v2 §4.2) + çocuk haritası + gezinme |
| 2 | `data-master.md` | bu dosya — anayasa |
| 3 | `data-kapsam.md` | ne İÇİNDE, ne DIŞINDA, komşularla sınır |
| 4 | `data-bagimliliklar.md` | depends_on / used_by / entegrasyon sınırı / kırılganlık |
| 5 | `data-dogrulama.md` | iddia·URL·tarih·durum kayıtları + ölçütler |
| 6 | `data-web-arastirma.md` | sistemli web taraması (kaynak kategorisi bazlı) |
| 7 | `data-bilesenler.md` | 10 kademe-2 çocuğun rol/sorumluluk/bağımlılık matrisi |
| 8 | `data-kod-ornekleri.md` | gerçek repo kodu — neyin ZATEN var olduğu |
| 9 | `data-kararlar.md` | mimari karar · alternatif · gerekçe · ADR bağlantısı |
| 10 | `data-riskler.md` | risk·etki·olasılık·azaltma + UNKNOWN/⚠️ defteri |
| 11 | `data-test-stratejisi.md` | doğrulama seviyeleri + kabul kriteri |
| 12 | `data-evrensel-dil.md` | TR↔EN terim sözlüğü (DDD ubiquitous language) |
| — | 10 çocuk klasörü | kademe-2 subdomain → her biri 6 MD |

## §1 Rol

**v2 §2.7 (birebir):** "Veritabanı — şarkılar, kullanıcılar, listeler, yedekler."

ELI15: bu oda CoreMusic'in **hafızasıdır**. Uygulama kapandığında silinmeyen her şey burada
yaşar: şarkı künyesi, sanatçı kredisi, kullanıcı hesabı, playlist sırası, cihaz eşlemesi,
API anahtarı hash'i, yedek görüntüsü. K-Space V3'te bu düğüm **kod üretmez** — plan, sınır,
sözleşme ve kayıt üretir; gerçek PHP/SQL `shared/` ve `.ai/.sql/` altındadır.

Rol cümlesinden (v2 tek cümle verir) türeyen **dört sorumluluk** — bu tablo **tasarım
yorumudur**, v2'de karşılığı yoktur:

| # | Sorumluluk | Soru sorar | Bağlı çocuklar |
|---|---|---|---|
| 1 | Kaydı biçimlendirmek | "veri nasıl durur?" | schema · migrations · indexing |
| 2 | Erişimi ve bütünlüğü yönetmek | "kim neyi değiştirebilir, atomik mi?" | repositories · transactions · data-quality |
| 3 | Hızlı ve taze okumak | "nasıl hızlı ve güncel bulunur?" | cache · search · synchronization |
| 4 | Koruma altına almak | "kaybolursa ne olur?" | backup |

### §1.1 Sorumluluk matrisi (kim neye bakar)

| Sorumluluk | Sahip çocuk | Besleyen komşu | Ölçülen şey | Ölçüm aracı (repo) |
|---|---|---|---|---|
| Kayıt biçimi | schema | K008-domain (entity tanımı) | tablo/kolon/sınır sayısı · BCNF | `.ai/.sql/mysql/*.sql` (20 dosya) |
| Şema değişimi | migrations | K17-devops (pipeline) | up/down çifti · geri alınabilirlik | `shared/database/migrations/*.php` |
| Veri erişimi | repositories | K011-application · K012-services | repository başına sorgu sayısı | `shared/src/Repository/` |
| Atomiklik | transactions | K012-services | rollback başarı oranı | `DatabaseManager::rollBack()` |
| Sorgu hızı | indexing | search · cache | EXPLAIN planı · fullscan sayısı | `dev.mysql.com/…/explain` (web) |
| Önbellek | cache | K016-observability (oran) | hit/miss · TTL uyumu | `shared/src/Cache/` |
| Bulma | search | K020-discovery (⚠️ sınır) | precision@k · typo toleransı | MySQL FULLTEXT (web) |
| Tazelik | synchronization | K013-integraion (⚠️ sınır) | gecikme · çakışma sayısı | outbox/CDC (web) |
| Kurtarma | backup | K17-devops (⚠️ sınır) | RTO/RPO · kurtarma testi | mysqldump + binlog (web) |
| Bütünlük | data-quality | K18-testing | expectation geçme oranı | Great Expectations (web) + PHPStan |

## §2 Kapsam / Sınır (özet — tam metin `data-kapsam.md`)

### §2.1 Kapsam DIŞI (komşuya devredilen)

| Kapsam dışı | Nereye | Tür | Dayanak |
|---|---|---|---|
| İş kuralları ("çal", "beğen" anlamı) | K008-domain | yapısal | v2 §2.8 |
| Şifreleme/anahtar sahipliği | K009-security | yapısal | v2 §2.9 |
| Fiziksel disk/donanım | K000-isletim-sistemi · K001-donanım | yapısal | v2 §2.1/§2.2 |
| Deploy/zamanlama/pipeline | K17-devops | ⚠️ SINIR AÇIK | sahiplik ADR'si bekliyor |
| Keşif/katalog kuralları | K020-discovery | ⚠️ SINIR AÇIK | sahiplik ADR'si bekliyor |
| Telemetri/metrik kaydı | K016-observability | TASARIM | v2 §2.17 |
| Öneri modeli eğitimi | K006-yapay-zeka | TASARIM | v2 §2.6 |

### §2.2 Kapsam İÇİ (10 çocuk)

schema · migrations · repositories · transactions · indexing · cache · search ·
synchronization · backup · data-quality — hepsi v2 §2.7'de ad olarak geçer; **rol
tanımları bu üretimde tasarım yorumudur** ve kademe-3'te (ADR-097 K4) kilitlenir.

## §3 Kademe-2 çocuk tablosu (adlar v2 §2.7 · satır 274–288)

| # | Çocuk | v2 satır | Rol (tasarım yorumu — ⚠️) | Kod kanıtı var mı? | Durum |
|---|---|---|---|---|---|
| 1 | [schema](schema/index.md) | 278 | Tablo/kolon/İlişki/CONSTRAINT/COMMENT düzeni — BCNF hedefi | ✅ `.ai/.sql/mysql/*.sql` (18 DB · 171 tablo) | draft |
| 2 | [migrations](migrations/index.md) | 279 | Şema değişiminin versiyonlu, geri alınabilir, kayıtlı hali | ✅ `shared/database/migrations/` (3 dosya) | draft |
| 3 | [repositories](repositories/index.md) | 280 | Domain'in veriye tek kapıdan erişimi — SQL'i domain'den saklar | ✅ `shared/src/Repository/ApiKeyRepository.php` | draft |
| 4 | [transactions](transactions/index.md) | 281 | Atomik gruplama · rollback · izolasyon seviyesi | ✅ `DatabaseManager.php:49-62` (beginTransaction/commit/rollBack) | draft |
| 5 | [indexing](indexing/index.md) | 282 | Sorgu hızını koruyan B-tree/FULLTEXT yapıları | ✅ dump'larda `KEY`/`UNIQUE KEY`/prefix index | draft |
| 6 | [cache](cache/index.md) | 283 | Sıcak verinin hafızada, TTL'li, kirletilebilir kopyası | ✅ `shared/src/Cache/` (6 dosya · APCu + Memory) | draft |
| 7 | [search](search/index.md) | 284 | Şarkı/sanatçı/albüm metinlerinde bulma (FULLTEXT) | ⚠️ kısmi — `novasearch.sql` var; MySQL FULLTEXT kullanımı repo'da YOK | draft |
| 8 | [synchronization](synchronization/index.md) | 285 | Çok-cihaz/çok-servis tazelik: outbox · CDC · sıraya alma | ❌ repo kanıtı YOK — yalnız web | draft |
| 9 | [backup](backup/index.md) | 286 | Yedekleme · PITR · kurtarma testi · KVKK imha | ⚠️ kısmi — migration'da mysqldump talimatı var (`2026-10_07…:14`) | draft |
| 10 | [data-quality](data-quality/index.md) | 287 | Beklenti/test/monitor ile veri sağlığı denetimi | ⚠️ kısmi — PHPStan/PHPUnit var; veri-beklentisi YOK | draft |

> **Kod kanıtı sütunu** bu üretimde yeni eklendi (v1 master'da yoktu): "neyin zaten
> var olduğu" ile "neyin plan olduğu" ayrıştırılır → `data-kod-ornekleri.md` §2.

## §4 Teknoloji gerçeği (repo kanıtlı · 2026-10-12)

> Kural: **yalnız** okunmuş dosya + satır no ile yazılır. Tahmin `UNKNOWN`'dur.

| # | İddia | Kanıt (dosya: satır) | Durum |
|---|---|---|---|
| 1 | DBMS = **MySQL** (InnoDB, `utf8mb4_unicode_ci`) | `shared/src/Database/DatabaseManager.php:14-20` (DSN `mysql:host=…charset=%s`) + tüm dump'larda `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` | ✅ DOĞRULANDI |
| 2 | Bağlantı katmanı = **PDO** (ORM yasak) | `DatabaseManager.php:22-27` (4 PDO attribute) · ADR-002 · `.ai/AGENTS.md` §18 | ✅ DOĞRULANDI |
| 3 | Sertleştirme: `ERRMODE_EXCEPTION` · `FETCH_ASSOC` · `EMULATE_PREPARES=false` · `PERSISTENT=true` | `DatabaseManager.php:23-26` | ✅ DOĞRULANDI |
| 4 | Çoklu-DB registry: `registerMySql(key, host, db, user, pass, port=3306, charset=utf8mb4)` | `shared/src/Database/DatabaseRegistry.php:14-19` | ✅ DOĞRULANDI |
| 5 | 18 BCNF `coremusic_*` veritabanı · 171 tablo (canlı dump) | `.ai/.sql/CLAUDE.md:9,29,31` | ✅ DOĞRULANDI (2026-10-07 ölçümü) |
| 6 | Anahtar tipi: `binary(16)` UUID v7 | `.ai/.sql/mysql/coremusic_musics.sql:19,29,45,64` (`COMMENT 'UUID v7'`) | ✅ DOĞRULANDI |
| 7 | Yumuşak silme: `is_deleted` + `deleted_at` | `coremusic_musics.sql:53-55` · `ApiKeyRepository.php:16` (şema docblock) | ✅ DOĞRULANDI |
| 8 | Önbellek = **APCu** (yoksa bellek-içi fallback) | `shared/src/Cache/CacheManager.php:12-16` · `composer.json:9` (`ext-apcu`) | ✅ DOĞRULANDI |
| 9 | Sayfa önbelleği: `page_pub_`/`page_auth_` + `md5(uri)` · TTL 600 | `shared/src/Cache/PageCacheAdapter.php:11,30-31` | ✅ DOĞRULANDI |
| 10 | Migration PHP-dizisi döndürür (`$queries` + `return $queries`) | `shared/database/migrations/oauth_connections_migration.php:13,46` | ✅ DOĞRULANDI |
| 11 | Yıkıcı migration ön-kontrol + yedek talimatı | `2026-10_07_drop_coremusic_api_api_keys_migration.php:14-20,26-31` | ✅ DOĞRULANDI |
| 12 | Testler SQLite in-memory ile şema sahtesi | `shared/tests/Repository/ApiKeyRepositoryTest.php:6-9,23-30` | ✅ DOĞRULANDI |
| 13 | Redis / memcached / dış arama motoru (Meilisearch·Solr·ES) | repo'da hiçbir referans YOK | **UNKNOWN** (web'de değerlendirildi → K-04/K-06) |
| 14 | synchronization için CDC/outbox altyapısı | repo'da YOK | **UNKNOWN** (yalnız web → K-07) |

### §4.1 Kanıt yokluğunda davranış

| Durum | Davranış |
|---|---|
| Seçim satırı kanıtsızsa | `UNKNOWN` yazılır — tahmin edilmez |
| Lisans iddiası | yalnız LICENSE dosyası ile yazılır |
| Komşu payın kanıtı | bu paya taşınmaz |
| `status` etkisi | açık ölçüt kaldıkça `draft` kalır |

## §5 Bağımlılık özeti (tam tablo: `data-bagimliliklar.md`)

| Yön | Düğüm | Ne | Etiket |
|---|---|---|---|
| → depends_on | K008-domain | entity/kavram tanımı — şemanın dayanağı | TASARIM İDDİASI |
| → depends_on | K009-security | şifreleme **çağrısı** (anahtarın kendisi security'de) | TASARIM İDDİASI |
| → depends_on | K000-isletim-sistemi | fiziksel dosya/disk yazımı | TASARIM İDDİASI |
| ← used_by | K006-yapay-zeka | öneri girdisi (etkileşim + katalog) | TASARIM İDDİASI |
| ← used_by | K011-application | sayfa verisi (repo üzerinden) | TASARIM İDDİASI |
| ← used_by | K012-services | servis kalıcılığı (repo üzerinden) | TASARIM İDDİASI |
| ← used_by | K18-testing | test verisi kurulumu | TASARIM İDDİASI |
| ⚠️↔ | K17-devops | yedek zamanlama/pipeline | SINIR AÇIK |
| ⚠️↔ | K020-discovery | arama keşif kuralları | SINIR AÇIK |
| ⚠️↔ | K013-integraion | `synchronization/` çift-kaydı | SINIR AÇIK |

## §6 Doğrulama özeti (tam liste: `data-dogrulama.md` §2)

| Ölçüt | Nasıl ölçülür | Sonuç |
|---|---|---|
| children = disk enumeration + v2 §2.7 (10/10) | `Get-ChildItem` + satır 274–288 | ✅ PASS · 2026-10-12 |
| master adı = registry §5 satır 7 | `data-master.md` eşleşmesi | ✅ PASS |
| index.md 8 zorunlu alan | v2 §4.2 kontrolü | ✅ PASS (8/8) |
| Dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | ✅ PASS (12/12 · 6/6 çocuk) |
| Web kanıtı | 74 benzersiz URL · tarih + durum | ✅ PASS (`data-dogrulama.md` §4) |
| Repo kod kanıtı | 12 dosya · yolu + satır no | ✅ PASS (`data-kod-ornekleri.md` §2) |
| Kendi DBMS/ORM/önbellek seçimi | karar + kanıt | ✅ PASS → K-01/K-02/K-03 (v1'deki açık kapandı) |
| depends_on karşılıklı kayıt | K008/K009/K000 index'leri | ❌ AÇIK → TASARIM |
| Kademe-3 kapsamı | ADR-097 K4 | ⏳ BEKLİYOR |
| search/backup sahipliği | sahiplik ADR'si | ❌ AÇIK (K-08/K-09) |

## §7 Açık sorular

| # | Soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | `search` (K007) ↔ `K020-discovery/search/` sahipliği kimde? | ⚠️ VERIFICATION REQUIRED | sahiplik ADR'si |
| 2 | `backup` operasyonel sahipliği: K007 ↔ K018-devops? | ⚠️ VERIFICATION REQUIRED | sahiplik ADR'si |
| 3 | `synchronization` ↔ `K013-integraion/synchronization/` sınırı? | ⚠️ VERIFICATION REQUIRED | sınır kaydı |
| 4 | 10 çocuğun kademe-3 (bounded-context) yapısı? | ⚠️ VERIFICATION REQUIRED | ADR-097 K4 |
| 5 | referans lisans etkileşimi: MIT (Koel) / AGPL-3.0 (Ampache) — kod kopyalama sınırı | ⚠️ VERIFICATION REQUIRED | K020-licence kaydı |
| 6 | dış arama motoru (Meilisearch/Solr/ES) kullanılırsa lisans + operasyon sorumluluğu? | ⚠️ VERIFICATION REQUIRED | K-06 kararı onayı |

## §8 Okuma zinciri (bu düğüm içinde)

```text
index.md (künye)
 └─ data-master.md (bu dosya — kimlik + sınırlar + teknoloji gerçeği)
     ├─ data-kapsam.md        → ne DEĞİL (komşularla sınır)
     ├─ data-bilesenler.md    → 10 çocuk = kademe-2
     ├─ data-bagimliliklar.md → yön · neden · kırılganlık
     ├─ data-kararlar.md      → K-01…K-10 kararları
     ├─ data-kod-ornekleri.md → repo gerçeği (12 dosya)
     ├─ data-web-arastirma.md → 74 URL · kategori bazlı
     ├─ data-dogrulama.md     → iddia defteri (≥25 satır)
     ├─ data-riskler.md       → risk + UNKNOWN/⚠️ defteri
     ├─ data-test-stratejisi.md → 5 seviye + kabul kriteri
     └─ data-evrensel-dil.md  → TR↔EN sözlük
```

## §8.1 Dosya bütünlük tablosu (12/12 — 2026-10-12 ölçümü)

| Dosya | Satır bütçesi | Rol (tek cümle) | Birbirine bağlı olduğu dosyalar |
|---|---|---|---|
| `index.md` | 60-120 | künye + çocuk haritası | hepsi (giriş) |
| `data-master.md` | 300-500 | anayasa | kapsam · bağımlılıklar · doğrulama |
| `data-kapsam.md` | 100-600 | sınır | master · kararlar · riskler |
| `data-bagimliliklar.md` | 100-600 | yön·kırılganlık | master · bilesenler |
| `data-dogrulama.md` | 100-600 | iddia defteri | web-arastirma · kod-ornekleri · kararlar |
| `data-web-arastirma.md` | 100-600 | kaynak taraması | dogrulama |
| `data-bilesenler.md` | 100-600 | envanter | kod-ornekleri · çocuklar |
| `data-kod-ornekleri.md` | 100-600 | repo gerçeği | dogrulama · bilesenler |
| `data-kararlar.md` | 100-600 | K-01…K-10 + K-SCOPE | kapsam · riskler |
| `data-riskler.md` | 100-600 | R-01…R-12 | kararlar · dogrulama |
| `data-test-stratejisi.md` | 100-600 | T-01…T-10 planı | kararlar · riskler |
| `data-evrensel-dil.md` | 100-600 | sözlük | tüm dosyalar (terminoloji) |

**Bütünlük kuralı:** bir dosyadaki iddia başka dosyada çelişiyorsa önce `data-dogrulama.md`
ölçütüne bakılır; kanıt çelişkisi `conflict` durumu üretir ve `status` `draft`'a döner.

## §8.2 Etiket sözlüğü (bu ağacın ortak dili)

| Etiket | Ne demek | Kullanım yeri |
|---|---|---|
| `✅ DOĞRULANDI` | dosya yolu + satır no okunmuş, iddia birebir | §4 · dogrulama · ornekler |
| `⚠️ VERIFICATION REQUIRED` | iddia mantıksal olarak tutarlı ama kanıt okunmadı | tüm dosyalar |
| `UNKNOWN` | hiçbir kanıt yok — tahmin yazılmaz | kararlar · riskler |
| `⚠️ TASARIM` | tasarım yorumu (v2'de karşılığı yok) | çocuk rol tanımları |
| `⚠️ SINIR AÇIK` | komşu sahipliği karar bekliyor (S-1/S-2/S-3) | kapsam · bağımlılıklar |
| `REPO` | kod/şema kanıtı (bağımlılık tablosu) | bagimliliklar §1-§2 |
| `TASARIM İDDİASI` | yapısal mantıktan türetildi | bagimliliklar §5 |

## §9 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.7 — `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md` (satır 270–288) | rol cümlesi + 10 çocuk adı |
| v2 §4.2 · §4.4 · §4.5 | 8 zorunlu alan · durum geçişi · path-ID biçimi |
| `.ai/architecture/index.md` §5 satır 7 · §7 · §8 | master adı · sayım · isimlendirme |
| `.ai/.decisions/draft/ADR-097-…md` §2 K2/K4 · §5.1/§5.2 | çoklu-MD şeması · rol sözlüğü |
| `.ai/AGENTS.md` §16 · §18 · §25.2 | Data standartları (BCNF · no ORM · no SELECT *) |
| `shared/AGENTS.md` §4 | PDO prepared-only · ORM yasak · migration şablonu |
| `.ai/.sql/CLAUDE.md` §1–§3 | 20 dump · 18 BCNF DB · 171 tablo |
| `shared/src/**` (12 dosya) | §4'teki 12 repo kanıtı satırı |
| 74 web URL (2026-10-12) | `data-dogrulama.md` §4 · `data-web-arastirma.md` §2 |

---

## §10 Üretim günlüğü (bu revizyon)

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-12 | 12 root MD sıfırdan yazıldı (v2.0.0); 4 eski taslak frontmatter'ı 8 zorunlu alana tamamlandı | dosya başlıkları + bu tablo |
| 2026-10-12 | DBMS/ORM/önbellek iddiaları `UNKNOWN` → `✅ DOĞRULANDI` (K-01/K-02/K-03) | §4 satır 1-9 |
| 2026-10-12 | Açık soru etiketleri kanonik S-1…S-6'ya hizalandı; KVKK sorusu S-7 olarak ayrıldı | §7 · kapsam §6 |
| 2026-10-12 | K-SCOPE-1…6 tanımları `data-kapsam.md` §5 ile tekilleştirildi (çift tanım kaldırıldı) | kararlar K-SCOPE tablosu |
| 2026-10-12 | 10 çocuk × 6 MD üretimi bu dosyanın §3 tablosuna bağlı kalır | §3 |

**Değişiklik kuralı:** bu tabloya satır **eklenir**, geçmiş satır değiştirilmez.

---

## §11 Veri modelleme & normalizasyon (ER/ilişkisel · 1NF→BCNF)

> Bu bölüm K007 omurgasının ilk ayağıdır: veri **nasıl durur**. Kanıt hem repo şeması
> (`.ai/.sql/mysql/*.sql`) hem resmi RDBMS dokümantasyonudur. Uydurma tablo/kolon yazılmaz.

### §11.1 Normalizasyon kademeleri (kısa refresher)

| Kademe | Koşul | CoreMusic karşılığı | Kanıt |
|--------|-------|---------------------|-------|
| 1NF | Alan bölünemez; tekrar eden grup yok | her kolon tek değer (`position int`, `join_phrase varchar`) | `coremusic_musics.sql` `artist_credit_names` |
| 2NF | 1NF + parçalı bağımlılık yok | kompozit PK'da tam fonksiyonel bağımlılık | `uk_acn_credit_pos (artist_credit_id, position)` |
| 3NF | 2NF + geçiş bağımlılığı yok | lookup tabloları (`catalog_album_types`, `catalog_artist_roles`) ayrı | `coremusic_catalog.sql:24-40` |
| BCNF | Her determinant aday anahtar | hedef — `.ai/.sql/CLAUDE.md §1` "18 BCNF DB" iddiası | ⚠️ iddia · tablo-bazlı denetim → VERIFICATION REQUIRED |

> **Dürüstlük notu:** "18 BCNF" iddiası `.ai/.sql/CLAUDE.md`'de geçer; bu vault o iddiayı
> **tekrarlar**, kendisi tablo-bazlı BCNF ispatı **üretmez** (üretmek → `schema-anayasa.md` işi).

### §11.2 Anahtar stratejisi (repo gerçeği)

| Karar | Değer | Kanıt (dosya:satır) |
|-------|-------|---------------------|
| Birincil anahtar tipi | `binary(16)` UUID v7 | `coremusic_musics.sql` `artist_credit` `id binary(16) NOT NULL COMMENT 'UUID v7'` |
| Yumuşak silme | `is_deleted` + `deleted_at` | `coremusic_catalog.sql` her tabloda `is_deleted tinyint(1) DEFAULT '0'`, `deleted_at timestamp NULL` |
| Zaman damgası | `created_at`/`updated_at` `ON UPDATE CURRENT_TIMESTAMP` | `coremusic_catalog.sql` `catalog_album_types` |
| N:M ilişki | ayrı join tablosu + `UNIQUE` sıra | `artist_credit_names` `uk_acn_credit_pos (artist_credit_id, position)` |
| FK davranışı | `ON DELETE CASCADE ON UPDATE CASCADE` | `artist_credit_names` `CONSTRAINT fk_acn_artist ... ON DELETE CASCADE` |

### §11.3 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| BCNF/normalizasyon kademeleri | https://www.postgresql.org/docs/current/ddl.html | 2026-10-13 | HTTP 200 |
| PK/FK/UNIQUE/CHECK kısıtları | https://www.postgresql.org/docs/current/ddl-constraints.html | 2026-10-13 | HTTP 200 |
| CREATE TABLE sözdizimi | https://www.postgresql.org/docs/current/sql-createtable.html | 2026-10-13 | HTTP 200 |
| Oracle CREATE TABLE (tip referansı) | https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/CREATE-TABLE.html | 2026-10-13 | HTTP 200 |

---

## §12 İndeks stratejisi

> İndeks, okuma hızını korur ama yazma/mekân maliyeti taşır. Kanıt: repo dump'larındaki
> `KEY`/`UNIQUE KEY` + resmi indeks dokümanı.

### §12.1 Repo indeks türleri (dump'larda görülen)

| Tür | Repo örneği | Amaç | Kanıt |
|-----|-------------|------|-------|
| `UNIQUE KEY` | `uk_album_types_name (type_name)` | alanı tekilleştir | `coremusic_catalog.sql` |
| kompozit `UNIQUE` | `uk_acn_credit_pos (artist_credit_id, position)` | sıra tekilliği | `coremusic_musics.sql` |
|普通 `KEY` | `idx_album_types_sort_order (sort_order)` | sıralama/tarama | `coremusic_catalog.sql` |
| prefix (dizgi) index | `idx_artist_credit_display (display_name(100))` | uzun `varchar` taraması | `coremusic_musics.sql` `artist_credit` |
| composite normal | `uk_apikeys_hash`, `idx_apikeys_user` | lookup hızı | `ApiKeyRepository.php:1-24` docblock |

### §12.2 İndeks kararı matrisi

| Durum | Karar | Gerekçe |
|-------|-------|---------|
| Sık `WHERE` + `JOIN` kolonu | indeksle | tarama maliyetini düşür |
| Uzun metin araması (`display_name`) | prefix index (`(100)`) | tam kolon indeksleme maliyetli |
| Düşük kardinalite (`is_active`, `sort_order`) | tek başına indeksleme; sorgu planına bırak | seçicilik düşük |
| `SELECT *` | **yasak** (ADR-002) | gereksiz sorgu + transfer |

### §12.3 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| B-tree indeks temelleri | https://www.postgresql.org/docs/current/indexes.html | 2026-10-13 | HTTP 200 |
| Multicolumn (composite) indeks sırası | https://www.postgresql.org/docs/current/indexes-multicolumn.html | 2026-10-13 | HTTP 200 |
| Oracle CREATE INDEX | https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/CREATE-INDEX.html | 2026-10-13 | HTTP 200 |

---

## §13 Erişim katmanı (PDO / prepared)

> CoreMusic'te ORM **yasak** (ADR-002); erişim PDO prepared statement ile yapılır. Kanıt: repo.

### §13.1 PDO sertleştirme (repo gerçeği)

`shared/src/Database/DatabaseManager.php:22-27` dört PDO attribute set eder:

| Attribute | Değer | Neden |
|-----------|-------|-------|
| `ATTR_ERRMODE` | `ERRMODE_EXCEPTION` | sessizce başarısız olma; exception fırlat |
| `ATTR_DEFAULT_FETCH_MODE` | `FETCH_ASSOC` | associative dizi |
| `ATTR_EMULATE_PREPARES` | `false` | gerçek server-side prepared → injection koruması |
| `ATTR_PERSISTENT` | `true` | bağlantı yeniden kullanım (dikkat: pool riski → §13.3) |

`IDatabaseManager` (`shared/src/Contracts/Database/IDatabaseManager.php:8-14`) altı metot
sözleşmesi verir: `execute` · `write` · `lastInsertId` · `beginTransaction` · `commit` · `rollBack`.

`IDatabaseRegistry` (`.../IDatabaseRegistry.php:8-12`) çoklu-DB kaydı: `registerMySql(key, host,
dbName, user, password, port=3306, charset=utf8mb4)` · `get(key)` · `has(key)`.

### §13.2 Prepared statement kuralı

```text
execute(string $sql, array $params = []): array   →  prepare($sql) → execute($params) → fetchAll()
write(string $sql, array $params = []): bool      →  prepare($sql) → execute($params)
```
Değerler **her zaman** `$params` üzerinden bağlanır; string birleştirme (interpolasyon) yasak.

### §13.3 Persistent bağlantı notu (kırmızı bayrak)

`ATTR_PERSISTENT=true` (`DatabaseManager.php:26`) bağlantı yeniden kullanır. Bilinen risk:
session boyunca değişen `SET` değerleri (ör. `sql_mode`, `time_zone`) bir sonraki kullanıcıya
sızabilir. Azaltma: bağlantı açılışında sabit `SET` veya request-scope reset → **⚠️ TESPİT EDİLMEDİ** repo'da
(tasarım önerisi, kanıt yok → VERIFICATION REQUIRED).

### §13.4 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| DML (prepared/parameterized bağlam) | https://www.postgresql.org/docs/current/dml.html | 2026-10-13 | HTTP 200 |
| SQLAlchemy engine bağlam katmanı (referans) | https://docs.sqlalchemy.org/en/20/core/engines.html | 2026-10-13 | HTTP 200 |

---

## §14 İşlemler (transactions · ACID · MVCC)

> Atomiklik repo'da `beginTransaction`/`commit`/`rollBack` ile sağlanır.

### §14.1 ACID ↔ repo

| ACID | Repo karşılığı | Kanıt |
|------|----------------|-------|
| Atomicity | `beginTransaction()` + `rollBack()` | `DatabaseManager.php` (IDatabaseManager sözleşmesi) |
| Consistency | FK + `UNIQUE` kısıtları | dump'larda `CONSTRAINT`/`UNIQUE KEY` |
| Isolation | InnoDB MVCC (varsayılan REPEATABLE READ) | ⚠️ repo'da açık `SET TRANSACTION` **YOK** → default |
| Durability | InnoDB flush + binlog (operasyon) | ⚠️ K17-devops sınırı → VERIFICATION REQUIRED |

### §14.2 İzolasyon seviyesi dürüstlüğü

Repo kodunda izolasyon seviyesi **açıkça ayarlanmamış** (`beginTransaction()` çağrısı dışında
`SET TRANSACTION ISOLATION LEVEL` referansı okunmadı). Bu yüzden seviye **default (InnoDB
REPEATABLE READ)** varsayılır — bu bir **kanıtlanmış seçim değil, varsayılan sonucu**dur.

### §14.3 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Transaction temelleri (ACID) | https://www.postgresql.org/docs/current/tutorial-transactions.html | 2026-10-13 | HTTP 200 |
| MVCC davranış modeli | https://www.postgresql.org/docs/current/mvcc.html | 2026-10-13 | HTTP 200 |
| Explicit locking | https://www.postgresql.org/docs/current/explicit-locking.html | 2026-10-13 | HTTP 200 |

---

## §15 Saklama & yedekleme (backup · kurtarma)

> Yedekleme operasyoneldir; bu vault yalnız **politika + sınır** tutar, zamanlama K17-devops'a aittir.

### §15.1 Politika matrisi

| Katman | Ne | Sahip | Kanıt/Not |
|--------|----|-------|-----------|
| Tam yedek (dump) | `mysqldump` · schema + data | K17-devops (⚠️ sınır) | migration'da yedek talimatı: `2026-10_07_drop_…migration.php:14-20` |
| Artık yedek (binlog/PITR) | point-in-time recovery | K17-devops | repo kod kanıtı **YOK** → VERIFICATION REQUIRED |
| KVKK imha | `is_deleted` + `deleted_at` soft-delete | K007 (şema) | dump'larda `deleted_at timestamp NULL` |
| Kurtarma testi | periyodik restore provası | K17-devops | repo kanıtı YOK → VERIFICATION REQUIRED |

### §15.2 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Backup & dump (pg_dump modeli) | https://www.postgresql.org/docs/current/backup-dump.html | 2026-10-13 | HTTP 200 |
| Continuous archiving / PITR | https://www.postgresql.org/docs/current/continuous-archiving.html | 2026-10-13 | HTTP 200 |
| Hot standby (okuma replikası) | https://www.postgresql.org/docs/current/hot-standby.html | 2026-10-13 | HTTP 200 |

---

## §16 Önbellek (cache)

> CoreMusic önbelleği APCu tabanlıdır; bellek-içi fallback vardır. Kanıt: repo.

### §16.1 Repo önbellek katmanı

| Dosya | Rol | Kanıt |
|-------|-----|-------|
| `shared/src/Cache/CacheInterface.php` | sözleşme | dosya var (boyut ölçüldü) |
| `shared/src/Cache/CacheManager.php` | APCu + fallback seçim | `CacheManager.php:12-16` (master §4 satır 8) |
| `shared/src/Cache/ApcuAdapter.php` | APCu uzantısı | `composer.json` `ext-apcu` |
| `shared/src/Cache/MemoryAdapter.php` | fallback (APCu yoksa) | dosya var |
| `shared/src/Cache/PageCacheAdapter.php` | sayfa önbelleği `page_pub_`/`page_auth_` + `md5(uri)` TTL 600 | `PageCacheAdapter.php:11,30-31` |

### §16.2 Cache strateji matrisi

| Strateji | Kullanım | Invalidation |
|----------|----------|--------------|
| Read-through | repo sorgusu → sıcak veri | TTL + event |
| Page cache | statik/oturumsuz sayfa | `md5(uri)` anahtarı · TTL 600 |
| Write-through | yazma sonrası tazeleme | ⚠️ repo'da kanıt YOK → VERIFICATION REQUIRED |
| Stampede koruması | eşzamanlı yeniden yükleme | ⚠️ repo'da kanıt YOK → VERIFICATION REQUIRED |

### §16.3 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Result cache (Oracle modeli) | https://docs.oracle.com/en/database/oracle/oracle-database/23/tgdba/tuning-result-cache.html | 2026-10-13 | HTTP 200 |
| Runtime query config (cache/plan) | https://www.postgresql.org/docs/current/runtime-config-query.html | 2026-10-13 | HTTP 200 |

---

## §17 Arama (search)

> Arama sahipliği **sınır** (K020-discovery ile çakışma → S-1). Bu bölüm yalnız K007'nin
> veri-yüzü tanımıdır.

### §17.1 Repo arama durumu

| İddia | Kanıt | Durum |
|-------|-------|-------|
| `novasearch.sql` şema dosyası var (147 satır) | `.ai/.sql/mysql/novasearch.sql` | ✅ DOĞRULANDI (dosya var) |
| MySQL FULLTEXT kullanımı repo kodunda | okunan repo dosyalarında `MATCH…AGAINST` **görülmedi** | ⚠️ VERIFICATION REQUIRED |
| Dış arama motoru (Meilisearch/Solr/ES) | repo'da referans YOK | **UNKNOWN** (K-06 kararı onaya bağlı) |

### §17.2 Arama stratejisi (tasarım — kanıt sınırıyla)

| Gereksinim | Yaklaşım | Kanıt durumu |
|------------|----------|--------------|
| Tam-kelime bulma | MySQL FULLTEXT / prefix index | şema var, kullanım kanıtsız |
| Typo toleransı | `novasearch` (ne-vas) | şema var, davranış kanıtsız |
| Semantik/katalog keşfi | K020-discovery | K007 kapsam-dışı |

### §17.3 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Aggregate/fonksiyon referansı (araştırma) | https://www.postgresql.org/docs/current/functions-aggregate.html | 2026-10-13 | HTTP 200 |

---

## §18 Ölçüm (slow query · izleme)

> "Ölçüm" K007'nin son omurga ayağıdır: sorgu hızını **görünür** kılmak.

### §18.1 Ölçüm araçları

| Araç | Ne ölçer | Kanıt/durum |
|------|----------|-------------|
| MySQL slow query log | yavaş sorgular | ⚠️ repo'da yapılandırma **okunmadı** → VERIFICATION REQUIRED |
| `EXPLAIN` planı | indeks kullanımı / fullscan | web kanıtı (§12.3) |
| pg_stat_* | canlı istatistik | https://www.postgresql.org/docs/current/monitoring-stats.html (200) |
| Parallel query | paralel plan eşiği | https://www.postgresql.org/docs/current/parallel-query.html (200) |

### §18.2 Ölçüm matrisi

| Metrik | Hedef | Kaynak |
|--------|-------|--------|
| fullscan sayısı | JOIN/WHERE'de 0 | EXPLAIN |
| slow query eşiği | ⚠️ tanımsız (repo kanıtı yok) | UNKNOWN |
| cache hit oranı | ⚠️ tanımsız (repo'da sayaç yok) | UNKNOWN |

### §18.3 Resmi kaynak (doğrulanmış HTTP 200)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| İstatistik/izleme | https://www.postgresql.org/docs/current/monitoring-stats.html | 2026-10-13 | HTTP 200 |
| Parallel query planlama | https://www.postgresql.org/docs/current/parallel-query.html | 2026-10-13 | HTTP 200 |
| Performans ipuçları | https://www.postgresql.org/docs/current/performance-tips.html | 2026-10-13 | HTTP 200 |

---

## §19 Veri kalitesi (data-quality)

### §19.1 Dürüstlük katmanları

| Katman | Mekanizma | Kanıt |
|--------|-----------|-------|
| Şema kısıtı | `NOT NULL` · `UNIQUE` · `FK` | dump'larda `CONSTRAINT` |
| Uygulama | prepared + DTO doğrulama | K011-application sınırı |
| Denetim | PHPStan/PHPUnit | master §4 satır 12 (`ApiKeyRepositoryTest.php`) |
| Veri-beklentisi (Great Expectations vb.) | repo'da **YOK** | ⚠️ VERIFICATION REQUIRED |

### §19.2 Saklama politikası

| Veri | Politika | Kanıt |
|------|----------|-------|
| Ham API anahtarı | asla DB'ye yazılmaz (yalnız SHA-256 hash + prefix) | `ApiKeyRepository.php:18-22` docblock |
| Kişisel veri (KVKK) | soft-delete `deleted_at` | dump'larda `deleted_at` |
| Log | K016-observability / K009-security | komşu sınırı |

---

## §20 Kapsam dışı (bu bölüm özet — tam: `data-kapsam.md`)

| Kapsam dışı | Nereye | Not |
|-------------|--------|-----|
| Domain kuralları / ValueObject | K008-domain | şemanın dayanağı |
| Şifreleme anahtarı sahipliği | K009-security | K007 yalnız çağırır |
| Deploy/zamanlama/pipeline | K17-devops | yedek operasyonu |
| Keşif/katalog kuralları | K020-discovery | arama sahipliği sınırı (S-1) |
| Telemetri/metrik kaydı | K016-observability | ölçüm veri akışı |
| Kimlik doğrulama/oturum | K0100-identitiy | veri değil, kimlik |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
