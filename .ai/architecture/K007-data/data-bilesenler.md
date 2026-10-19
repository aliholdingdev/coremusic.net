---
node_id: "K007-data/bilesenler"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-kapsam"
  - "K007-data/data-master"
evidence:
  - "shared/src/Database/DatabaseManager.php:14-67 — PDO bağlantı fabrikası"
  - "shared/src/Database/DatabaseRegistry.php — çoklu-DB kayıt defteri"
  - "shared/src/Cache/CacheManager.php:32-99 · ApcuAdapter.php · PageCacheAdapter.php"
  - "shared/src/Repository/ApiKeyRepository.php — repository örneği"
  - ".ai/.sql/CLAUDE.md §1 — 18 BCNF DB / 171 tablo dökümü (2026-10-07)"
validation: "2026-10-12 — 9 bileşen disk kanıtıyla (dosya yolu + satır) envanterlendi; sahiplik matrisi K007/K018/K020 arasında 3 açık sınır ile işaretli."
open_questions:
  - "S-6 dış arama motoru seçilirse `search/` çocuğuna bileşen (motor-istemci adaptörü) eklenir mi?"
  - "S-9 CDC/outbox bileşeni (K-07) K007'de mi yoksa K013-integraion'da mı tanımlanacak?"
  - "Sayfalama önbelleği (PageCacheAdapter) K007'nin mi K006/Presentation'ın mı bileşeni olarak sınıflanacak?"
group: mimari
---

# K007-data / bilesenler — Bileşen Envanteri ve Sahiplik Matrisi

> Bu dosya K007'nin **çalışan bileşenlerini** (kodda var olan) ve **planlanan bileşenlerini**
> (karar bekleyen) tek tabloda toplar. Her çalışan bileşen için dosya yolu + satır kanıtı
> verilir. Sınıflandırma: `ÇALIŞIR` (depoda kanıtlanmış) · `TASARIM` (kararlı, henüz kod yok) ·
> `UNKNOWN` (karar açık).

---

## 1. Çalışan bileşenler (disk kanıtı)

### B-01 · DatabaseManager — PDO bağlantı fabrikası

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Database/DatabaseManager.php` |
| Kanıt | satır 14-67 (DSN kurulumu, PDO option'ları) |
| Sorumluluk | Her `coremusic_*` DB için tek PDO bağlantısı üretir; option'lar sabit: `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`, `PERSISTENT=true` |
| Kural | `SELECT *` yasak (ADR-002); caller sütun listesi zorunlu |
| Çocuk ilişkisi | `schema` (DSN/charset), `transactions` (bağlantı üzerinden transaction) |

### B-02 · DatabaseRegistry — çoklu-DB kayıt defteri

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Database/DatabaseRegistry.php` |
| Kanıt | sınıf tanımı — DB adı → yapılandırma eşlemesi |
| Sorumluluk | 18 `coremusic_*` veritabanının ad–tanım kayıtını tutar; `DatabaseManager`'a giden isim çözümleme katmanı |
| Kural | Kayıt defteri tek noktadır; yeni DB eklemek kayıt girdisi + config gerektirir |
| Çocuk ilişkisi | `schema` (DB envanteri), `migrations` (hangi DB'ye iner) |

### B-03 · DatabaseConfig — yapılandırma kaynağı

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Config/DatabaseConfig.php` |
| Sorumluluk | Host/port/dbname/charset (utf8mb4) ve kimlik bilgilerini sağlar; sabit anahtar içermez, config/`.env` akışından okur |
| Güvenlik | Parola/DSN asla `.md`/`.log`'a yazılmaz (AGENTS.md §16.3 ruhu) |
| Çocuk ilişkisi | `schema`, `backup` (hangi DB'nin döküm alınacağı listesi buradan gelir) |

### B-04 · CacheManager + adaptörler — önbellek katmanı

| Alan | Değer |
|---|---|
| Dosyalar | `shared/src/Cache/CacheManager.php:32-99` · `ApcuAdapter.php` · `PageCacheAdapter.php` |
| Kanıt | adaptör sıralaması APCu → (varsa Redis) → Memory; `CacheManager.php:32-40` |
| Sayfalama önbelleği | anahtarlar `page_pub_` / `page_auth_` + `md5(uri)`, varsayılan TTL 600 sn (`PageCacheAdapter`) |
| Karar bağları | K-03 (APCu), K-04 (Redis — UNKNOWN), K-05 (invalidasyon sözleşmesi) |
| Çocuk ilişkisi | `cache` |

### B-05 · Repository örnekleri — erişim katmanı

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Repository/ApiKeyRepository.php` |
| Test | `shared/tests/Repository/ApiKeyRepositoryTest.php` |
| Sorumluluk | Tek varlık için parametreli sorgu, dönüş eşlemesi ve yazma işlemleri; iş mantığı içermez |
| Kural | Sorgular hazırlanmış-ifade (prepared statement) tabanlı; `SELECT *` yok |
| Çocuk ilişkisi | `repositories`, `transactions` (yazma yolları transaction sarmalar) |

### B-06 · Migration dosyaları — şema değişim yüzeyi

| Alan | Değer |
|---|---|
| Dizin | `shared/database/migrations/` |
| Kanıt örnekleri | `oauth_connections_migration.php:16-22` (`return $queries;` deseni) · `2026-10_07_drop_coremusic_api_api_keys_migration.php` (yıkıcı migration + öntest yönergesi) |
| Desen | Her migration SQL dizgesi listesi döndürür; yürütme çağıran katmandadır |
| Çocuk ilişkisi | `migrations` |

### B-07 · Şema varlıkları — 18 BCNF DB / 171 tablo

| Alan | Değer |
|---|---|
| Dizin | `.ai/.sql/mysql/` (ör. `coremusic_musics.sql`, `novasearch.sql`) |
| Kanıt | `.ai/.sql/CLAUDE.md §1` — 2026-10-07 canlı döküm: 18 DB, 171 tablo |
| Ayırt edici | PK'lar `binary(16)` UUID v7; soft delete `is_deleted` + `deleted_at` |
| Çocuk ilişkisi | `schema`, `data-quality` (döküm-bazlı nitelik kontrolleri) |

### B-08 · Sayfa çıktısı önbelleği — sunum sınırındaki bileşen

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Cache/PageCacheAdapter.php:11-34` · `PageCacheInterface.php:7-10` |
| Kanıt | `pageKey()` `page_auth_` / `page_pub_` öneki + URI (satır 28-31); `remember($key,$ttl,$callback)` callback sonucunu TTL ile saklar (satır 34); varsayılan `ttlPage=600` (satır 11) |
| Sınıf | Çalışır–sayılan, ancak **sunum katmanıyla ortak** — sahiplik sınırında: anahtarı URI olduğu için bileşen fiziksel olarak Cache dizininde, işi sayfa sunumu (K006) |
| Bilinmeyen | Cache'lenen değerin tam içeriği (HTML gövde mi, veri paketi mi) kodda okunmadan yazılmaz — `⚠️ VERIFICATION REQUIRED` |
| Çocuk ilişkisi | `cache` |

---

## 2. Planlanan / karar bekleyen bileşenler

| # | Bileşen | Durum | Karar | Hangi çocuk |
|---|---|---|---|---|
| P-01 | Redis istemcisi (K-04) | `UNKNOWN` | Redis'e geçilip geçilmeyeceği | cache |
| P-02 | Dış arama motoru istemcisi (K-06) | `UNKNOWN` | Meilisearch/Solr/Elasticsearch seçimi | search |
| P-03 | CDC/outbox yayıncısı (K-07) | `UNKNOWN` | Debezium benzeri akış gerekli mi | synchronization |
| P-04 | Periyodik yedek görevi | `TASARIM` | K007 mi K17-devops mu sahiplenecek (S-2) | backup |
| P-05 | Veri kalitesi iş kuralı paketi | `TASARIM` | Great Expectations benzeri araç mı, uygulama-içi kontrol mü | data-quality |
| P-06 | İndeks/baby-plan gözden geçirme görevi | `TASARIM` | 171 tabloda `EXPLAIN` tabanlı indeks denetimi | indexing |
| P-07 | Silme-talebi iş akışı (KVKK md.17) | `UNKNOWN` | Soft delete'e fiziksel silme eklenecek mi (S-7/R-04) | data-quality, schema |

---

## 3. Sahiplik matrisi (K007 ↔ komşular)

```
Bileşen             K007-data   K018-devops   K020-discovery   K013-integraion
─────────────────────────────────────────────────────────────────────────────
DatabaseManager         ●
DatabaseRegistry        ●
CacheManager            ●
PageCacheAdapter        ◐ (K006 ile ortak bakış)
Yedek görevi (cron)     ◐           ●
Arama motoru runtime    ◐                        ● (sahiplik tartışması S-1)
CDC/outbox                                          ◐ (S-9)
Import → DB kalite kapısı ●
```

`●` = sahip · `◦` = yok · `◐` = **açık sınır** (K007'de kayıtlı, komşuya bağlı).

---

## 4. Bileşen → çocuk izleme

| Çocuk | Birincil bileşen(ler) | İkincil |
|---|---|---|
| schema | B-07, B-02 | B-03 |
| migrations | B-06 | B-02 |
| repositories | B-05 | B-01 |
| transactions | B-01, B-05 | B-06 |
| indexing | B-07 | — |
| cache | B-04, B-08 | B-01 (DB'den gelen veri) |
| search | P-02 | B-07 (kaynak veri) |
| synchronization | P-03 | B-01 (çoklu-DB bağlantıları) |
| backup | P-04 | B-03 (DB listesi) |
| data-quality | P-05, B-07 | B-06 (migration veri taşımaları) |

---

## 5. Bileşenler için geçerli küresel kurallar (özet)

1. **ORM yok** — tüm erişim PDO; repository'ler prepared statement kullanır (K-02).
2. **`SELECT *` yasak** — sütun listesi açık yazılır (ADR-002, `.ai/AGENTS.md` §18).
3. **Domain katmanına harici bağımlılık yok** — repository arayüzleri domain'de, somut
   uygulaması Infrastructure'ta (§5 katman şeması).
4. **Anahtar/parola asla belgeye yazılmaz** — config/`.env` akışı (AGENTS.md §16.3).
5. **Hata yönetimi Result/Either** — PDO exception'ı repository sınırında yapısal hataya
   çevrilir; üst katmana çıplak `PDOException` sızmaz.

---

**Doğrulama notu:** Çalışan bileşen satırları satır numarasıyla `data-dogrulama.md`
depo-kanıtı tablosuna bağlıdır; planlanan satırlar `data-kararlar.md` K-numaralarına bağlıdır.

---

## 6. Bileşen etkileşim grafiği (çalışan bileşenler arası akış)

Aşağıdaki tablo, §1'de envanterlenen sekiz çalışan bileşenin birbirini nasıl
çağırdığını **çağrı yönü** ile gösterir. Okuma kuralı: satır başı bileşen, sütun
hücreleri onu çağıran bileşendir. Bu tablo `data-kararlar.md` K-01/K-02/K-03
kararlarıyla ve `.ai/.sql/CLAUDE.md §1` dökümüyle uyumludur; yeni bir çağrı yönü
eklendiğinde önce bu tablo güncellenir.

| Çağrılan ↓ / Çağrıyan → | B-01 DatabaseManager | B-02 Registry | B-04 CacheManager | B-05 Repository | B-06 Migration |
|---|---|---|---|---|---|
| B-01 DatabaseManager | — | isim çözümleme | — | bağlantı sağlar | şema uygular |
| B-02 DatabaseRegistry | DB adı → tanıma | — | — | — | hedef DB listesi |
| B-03 DatabaseConfig | host/port/charset | — | — | — | — |
| B-04 CacheManager | — | — | — | önbellekli okuma | — |
| B-05 Repository | prepared sorgu | — | cache-aside | — | — |
| B-06 Migration | tekil bağlantı | — | — | — | — |
| B-07 Şema varlıkları | hedef tablolar | envanter kaynağı | — | — | DDL hedefi |
| B-08 PageCacheAdapter | — | — | cache-aside | — | — |

**Çağrı yönü kuralları:**

1. B-05 → B-01 tek yönlüdür; B-01 asla B-05'i geri çağırmaz (bağımlılık döngüsü yasak).
2. B-04 ile B-08 aynı `CacheManager` arayüzünü paylaşır; B-08 yalnız sunum verisini
   önbellekler, B-05 yalnız varlık verisini — çakışma `page_pub_`/`page_auth_` önekiyle
   ayrılır.
3. B-06 → B-01 bağlantısı tekil (tek PDO) olur; migration çoklu-DB'ye yayılırsa her DB
   için ayrı B-01 örneği açılır (B-02 kayıt defterinden çözülür).
4. B-03 → B-01 yönü **dolaylıdır**: B-03 config'i üretir, B-01 onu okur; ikisi arasında
   doğrudan metod çağrısı değil, yapılandırma paylaşımı vardır.

Bu dört kural §5'teki küresel kurallarla (ORM yok · prepared · `SELECT *` yasak)
çelişmez; yalnız çağrı grafiğini netleştirir.

---

## 7. Hata modu ve yayılım analizi (FMEA özeti)

Her çalışan bileşen için **hata modu**, **etkilenen çocuk** ve **yayılım** tek
satırda özetlenir. Bu analiz `data-riskler.md` R-01…R-12 matrisine bağlanır; her
satır bir R-numarasına veya `—` (açık risk yok) işaretine sahiptir. Analiz kod
değil, tasarım düzeyindedir — somut istisna kodları `data-kod-ornekleri.md` §3'tedir.

| Bileşen | Hata modu | Etkilenen çocuk | Yayılım | Bağlı risk |
|---|---|---|---|---|
| B-01 DatabaseManager | bağlantı havuzu tükenmesi | transactions · repositories | üst katman `PDOException` alır | R-07 |
| B-01 DatabaseManager | charset uyuşmazlığı | schema | Unicode veri bozulması | — |
| B-02 DatabaseRegistry | kayıtta olmayan DB adı | schema · migrations | çözümleme başarısız, çağrı iptal | — |
| B-03 DatabaseConfig | `.env` okunamaması | tümü | bağlantı kurulamaz, sistem açılmaz | — |
| B-04 CacheManager | APCu segment doluluğu | cache | önbellek ıskalama oranı yükselir | R-05 |
| B-04 CacheManager | yanlış TTL | cache | bayat veri sunulması | R-05 |
| B-05 Repository | prepared yerine string birleştirme | repositories | SQL enjeksiyon yüzeyi | R-03 |
| B-05 Repository | sütun listesi eksik (`SELECT *` ihlali) | repositories | gereksiz veri + bağ kırılganlığı | R-03 |
| B-06 Migration | yıkıcı DDL ohne onay | migrations · schema | veri kaybı | R-12 |
| B-07 Şema | PK' binary(16) yerine metin | schema | indeks verimsizliği, JOIN yavaşlığı | R-10 |
| B-08 PageCache | auth verisinin pub anahtarla önbelleklenmesi | cache · (K006) | yetkisiz veri sızmışı | R-06 |

**Yayılım ilkesi:** Bir bileşen hata verdiğinde etki **aşağı katmana** (çocuklara)
yayılır; yukarı katman (çağıran) hata sınırında yakalar. B-05, PDO exception'ı
yakalayıp yapısal hataya çevirdiği için (§5 kural 5) B-01'inham istisnası
B-05 üstüne sızmaz — bu sınır, §6 kural 1'in (tek yönlü çağrı) doğal sonucudur.

---

## 8. Bileşen arayüz sözleşmeleri (girdi · çıktı · yan etki)

Her çalışan bileşenin **sözleşme özeti** aşağıdadır. Bu tablo kod tekrarı değil,
sorumluluk sınırıdır: hangi bileşen neyi garanti eder, neyi garanti etmez. Sütun
`Garanti etmez` bilinçli olarak boş bırakılmaz — her satırda bileşenin
dışarıda bıraktığı şey yazılır.

| Bileşen | Girdi | Çıtı | Yan etki | Garanti etmez |
|---|---|---|---|---|
| B-01 | DB adı + config | PDO bağlantısı | bağlantı havuzuna kayıt | transaction bütünlüğü |
| B-02 | — | DB adı → tanım | kayıt defteri değiştirilmez (salt-okunur okuma) | config doğruluğu |
| B-03 | `.env` / config | yapılandırma nesnesi | dosya/ortam okuma | değerin anlamı, birim |
| B-04 | anahtar + TTL + callback | önbellekli değer | önbelleğe yazma | callback'in yan etkisi |
| B-05 | prepared parametre | dönüş eşlemesi | DB'ye yazma | iş kuralı doğruluğu |
| B-06 | migration dosyası | SQL dizgisi listesi | uygulama çağıran katmanda | yürütme sırası garantisi |
| B-07 | — | tablo/sütun envanteri | yok (salt-okunur döküm) | canlı DB ile eşitlik |
| B-08 | URI + auth durumu | önbellekli sayfa çıktısı | önbelleğe yazma | çıktının güncelliği |

**Sözleşme kuralı:** Bir bileşenin `Garanti etmez` sütunu, o bileşeni çağıran
katmanın sorumluluğudur. Örneğin B-05 transaction bütünlüğünü garanti etmez — bu
`transactions` çocuğunun işidir; B-08 çıktının güncelliğini garanti etmez — bu
TTL (B-04) ve invalidasyon (K-05) sözleşmesinin işidir.

---

## 9. Sahiplik sınırı karar defteri (§3 açık sınırlar)

§3'teki `◐` işaretli üç açık sınır (PageCacheAdapter · yedek görevi · arama motoru
runtime) burada tek tek karara bağlama **çerçevesiyle** listelenir. Nihai karar
`data-kararlar.md`'de alınır; bu tablo yalnız hangi soruların sorulduğunu ve hangi
kanıtın yeteceğini tanımlar. Karar alınana kadar satır `UNKNOWN` kalır —
uydurularak kapatılmaz.

| Açık sınır | Soru | Karar için gereken kanıt | Sahip olacak K | Durum |
|---|---|---|---|---|
| PageCacheAdapter | Sayfa önbelleği K007'nin mi K006/Presentation'ın mı? | anahtarın URI olmasının sunum katmanına ait olduğu argümanı | K007-cache ↔ K006 | UNKNOWN |
| Yedek görevi (cron) | Periyodik yedek K007 mi K17-devops mu sahiplenecek (S-2)? | RPO/RTO hedefleri ve cron altyapısının hangi katmanda olduğu | K007-backup ↔ K018 | UNKNOWN |
| Arama motoru runtime | Dış motor istemcisi hangi çocuğa ait (S-1)? | motor seçimi (K-06) ve istemcinin K020-discovery ile ilişkisi | K007-search ↔ K020 | UNKNOWN |

**Karar defteri kuralı:** Her açık sınır için yalnız **bir** K sahibi olur; ortak
sahiplik (iki K'nin birlikte sahiplenmesi) sonuç üretmez. Sınır kapanınca §3'teki
`◐` `●`'a döner ve bu tablodaki `Durum` satırı tarihle `UNKNOWN`'dan karara çevrilir;
eski `UNKNOWN` satırı silinmez, üstü çizilmez (append-only).

---

## 10. Bileşen olgunluk ve kanıt matrisi

§1'deki `ÇALIŞIR` / `TASARIM` / `UNKNOWN` sınıflandırmasının kanıtla ilişkisi
aşağıdadır. Bu matris, bir bileşenin hangi olgunlukta olduğunu **neye göre**
söylediğimizi gösterir: kanıt türü (dosya yolu · ADR · web · yok) olgunluğu
belirler, takvim değil.

| Bileşen | Sınıf | Kanıt türü | Kanıt gücü | Olgunluk notu |
|---|---|---|---|---|
| B-01 | ÇALIŞIR | dosya yolu + satır | yüksek | opsiyonlar sabit, testi mevcut |
| B-02 | ÇALIŞIR | dosya yolu | orta | kayıt defteri var, 18 DB ile tutarlı |
| B-03 | ÇALIŞIR | dosya yolu | orta | `.env` akışı kodda, değerler kodda değil |
| B-04 | ÇALIŞIR | dosya yolu + satır | yüksek | adaptör sırası kodda sabitlenmiş |
| B-05 | ÇALIŞIR | dosya yolu + test | yüksek | repository + testi birlikte |
| B-06 | ÇALIŞIR | dosya yolu + satır | yüksek | iki desen kodda (düz + anahtarlı) |
| B-07 | ÇALIŞIR | döküm (`.ai/.sql`) | orta | canlı döküm, anlık görüntü |
| B-08 | ÇALIŞIR (sınırlı) | dosya yolu + satır | orta | içerik bilinmiyor → `⚠️` |
| P-01…P-07 | TASARIM/UNKNOWN | ADR/karar yok | — | §2 karar bekliyor |

**Olgunluk kuralı:** `ÇALIŞIR` olmak için dosya yolu + satır kanıtı zorunludur;
yalnız dosya varlığı yetmez (satır aralığı gösterilmelidir). `TASARIM` olmak için
kararın verilmiş olması (K-numarası) gerekir; `UNKNOWN` ise kararın henüz
verilmediğini ve kanıtın bulunmadığını işaretler. Bu üç sınıf dışına çıkılmaz
(ör. "kısmen çalışır" denenmez); B-08'in "sınırlı" eki, kanıtın eksik kısmını
açıklamak içindir, dördüncü bir sınıf değildir.

---

## 11. Bileşen → risk → karar izleme matrisi (§7 + §9 birleşimi)

Bu matris §7'deki risk bağları ile §9'daki sahiplik çerçevesini tek satırda
birleştirir: hangi bileşenin hangi riski taşıdığı ve hangi kararla kapanacağını
gösterir. Amaç, üç ayrı tabloya (§7 · §9 · `data-riskler.md`) tek bakışta erişmektir;
çelişki olursa `data-riskler.md` kazanır (orada R-numaraları kaynaklıdır).

| Bileşen | Bağlı risk | Karar dosyası | Kapanış ölçütü |
|---|---|---|---|
| B-01 | R-07 (kalıcı bağlantı) | data-kararlar K-02 | havuz/timeout kararı verildi |
| B-04 · B-08 | R-05 · R-06 (önbellek) | data-kararlar K-03/K-05 | TTL + invalidasyon sözleşmesi yazıldı |
| B-05 | R-03 (`SELECT *` · enjeksiyon) | ADR-002 + code-review | kod incelemesi 0 ihlal |
| B-06 | R-12 (migration deseni) | data-kararlar K-10 | tek desen seçildi (R-12 kararı) |
| B-07 | R-10 (indeks sürüklenmesi) | indexing çocuğunda | `EXPLAIN` denetimi tanımlandı |
| P-02 | R-11 (arama sahipliği) | §9 + K020 | tek K sahibi atandı |
| P-04 | R-08 (RPO/RTO) | §9 + K018 | sahiplik sınırı karara bağlandı |

**İzleme kuralı:** Bir satır yalnız üç durumdan birinde olabilir: (a) karar verildi
→ `Kapanış ölçütü` tamam, (b) karar açık → ölçüt yazılmamış, (c) risk yeni
eklendi → hem §7'ye hem bu tabloya birlikte satır eklenir. Tek tarafa eklemek
§7 ile bu matris arasında senkron bozukluğu yaratır; iki tablo her zaman birlikte
güncellenir.

---

## 12. Bileşen envanteri yenileme yöntemi (§1'in korunması)

§1'deki envanter statik değildir; depo değiştikçe yenilenir. Bu bölüm, yenilemenin
**nasıl** yapılacağını (hangi adımla, hangi kanıtla) tanımlar ki envanter zamanla
gerçekle sapmasın. Yenileme, §1'i yeniden yazmak değil, ek satır eklemek veya
`ÇALIŞIR`→`UNKNOWN` geri alımıdır.

| Adım | İş | Kanıt | Sapma türü |
|---|---|---|---|
| 1 | Yeni `shared/src/**` dosyaları listelenir | `Get-ChildItem` | yeni bileşen |
| 2 | Her yeni dosya §1'e eşlenir (var mı bakılır) | satır aralığı | ekleme / yok sayma |
| 3 | Eski bileşenlerin dosyası hâlâ var mı | dosya yolu kontrolü | silinme → geri alım |
| 4 | Satır aralıkları değişmiş mi | satır okuma | künye güncelleme |
| 5 | §7 · §9 · §11 senkron mu | çapraz okuma | tablo uyumsuzluğu |

**Geri alım kuralı:** Bir bileşenin dosyası silinmiş veya taşınmışsa, envanterden
silinmez — sınıfı `UNKNOWN`'a döner ve gerekçesi (hangi dosyanın nereye gittiği)
§1 satırına ek not olarak yazılır. Silme, append-only ilkeyle çelişir; yalnız
sınıf geçişi yapılır. Yenileme sonucu `data-dogrulama.md`'ye bir satır olarak
eklenir (tarih + ne değişti).

---

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## 13. Bileşen başına test yüzeyi (ne test edilebilir, ne edilemez)

§1'deki her çalışan bileşenin **test edilebilirlik sınırı** aşağıdadır. Bu tablo
test yazmak için değil, hangi bileşenin test edilebilir olduğunu **bilmek** içindir;
somut test dosyaları `data-test-stratejisi.md` T-01…T-08'de tanımlıdır. `Test var mı`
sütunu yalnız depoda kanıtlanmış testi gösterir — planlanan test "var" sayılmaz.

| Bileşen | Test edilebilir yüzey | Test edilemez yüzey | Test var mı |
|---|---|---|---|
| B-01 DatabaseManager | DSN üretimi, option sabitleri | canlı MySQL bağlantısı (entegrasyon) | — (yalnız örtük) |
| B-02 DatabaseRegistry | ad → tanım eşlemesi | — (saf veri) | — |
| B-03 DatabaseConfig | `.env` ayrıştırma | dosya/ortam erişimi (sarmalanmalı) | — |
| B-04 CacheManager | adaptör sırası, anahtar biçimi | APCu segment davranışı (ortam) | — |
| B-05 Repository | prepared sorgu, dönüş eşlemesi | — (PDO sahte ile) | ✅ `ApiKeyRepositoryTest` |
| B-06 Migration | dönen SQL dizgisi listesi | yürütme sırası (çağıran katman) | — |
| B-07 Şema | döküm okuma, kolon adları | canlı DB eşitliği | — |
| B-08 PageCacheAdapter | `pageKey()` biçimi, TTL varsayılanı | cache'lenen değerin içeriği | — |

**Test yüzeyi kuralı:** Bir bileşenin "test edilemez yüzey" sütunu, o yüzeyin
**entegrasyon testi** veya **ortam bağımlılığı** olduğunu gösterir; birim testiyle
kapatılamaz. B-05'in tek somut testi (`ApiKeyRepositoryTest`) depoda kanıtlanmıştır;
diğer bileşenlerde birim testi kaydı **yoktur** — bu, testin yazılmadığı anlamına
gelir, testin başarısız olduğu anlamına gelmez. Bu ayrım `data-test-stratejisi.md`
§6 kabul kriterleriyle同步 (senkron) tutulur.

---

## 14. K007 dışına taşan bileşen yüzeyleri (bilinen sınırlar)

§3'teki `◐` işaretleri ile §9'daki karar çerçevesi, bileşenlerin bir kısmının
K007 sınırlarını aştığını gösterir. Bu bölüm o sınırları **tek yerde** toplar:
hangi bileşen, hangi K'ya taşıyor, neden. Bu tablo sahiplik kararı **vermez** —
yalnız sınırı görünür kılar; karar §9'dadır.

| Bileşen | Taştığı K | Neden sınır dışarıda | K007'de kalan pay |
|---|---|---|---|
| B-08 PageCacheAdapter | K006 (Presentation) | anahtar URI olduğu için iş sayfa sunumu | fiziksel dosya + TTL |
| P-04 Yedek görevi | K018 (devops) | cron/altyapı K018'in sorumluluğu | DB listesi (B-03) |
| P-02 Arama motoru runtime | K020 (discovery) | motor-istemci sahipliği tartışması (S-1) | kaynak veri (B-07) |
| P-03 CDC/outbox | K013 (integration) | akış taşıma katmanı K013'ün yüzeyi (S-9) | olay kaynağı (B-01) |

**Sınır kuralı:** Bir bileşen sınır dışına taşsa bile, K007'deki **payı** (son
sütun) envanterden çıkarılmaz — §1'de kalır. Çıkarma, sahiplik kararına bağlıdır
ve §12'deki geri alım kuralıyla yürütülür. Sınır dışına taşan pay için "tamamen
K007 dışı" denmez; "K007 ile ortak" veya "K007 payı sınırlı" denir.

---

## 15. Yeni bileşen ekleme kontrolü (genişletme kapısı)

§12 yenileme yöntemi **mevcut** envanteri korur; bu bölüm ise **yeni** bir
bileşen eklenirken geçilmesi gereken kapıyı tanımlar. Kapı, bileşenin K007'ye
ait olduğunun ve §5 küresel kurallarına uyduğunun doğrulanmasıdır. Kapı
geçilmeden §1'e satır eklenmez.

| # | Kapı | Soru | Geçiş kanıtı |
|---|---|---|---|
| 1 | Kapsam | Bu bileşen K007'nin 10 çocuğundan birine mi bağlı? | çocuk adı (§4 sütunu) |
| 2 | ORM | PDO dışında erişim katmanı var mı? | `new \PDO` / repository kanıtı |
| 3 | `SELECT *` | Sütun listesi açık mı? | kod künyesi (§8 sözleşmesi) |
| 4 | Katman | Domain'e harici bağımlılık eklendi mi? | bağımlılık listesi (§6) |
| 5 | Sırd | Anahtar/parola belgeye yazıldı mı? | `.env` akışı (§5 kural 4) |
| 6 | Hata | Çıplak `PDOException` yukarı çıkıyor mu? | Result/Either sınırı (§5 kural 5) |
| 7 | Kanıt | Dosya yolu + satır aralığı var mı? | §1 satır formatı |

**Kapı kuralı:** Yedi kapının hepsi "hayır"/"evet" ile tek tek cevaplanır; tek
"bilinmiyor" kalırsa bileşen §1'e `UNKNOWN` sınıfıyla girer ve §9'daki karar
çerçevesine bağlanır. Kapı, bileşeni **reddetmez** — yalnız hangi sınıfla
gireceğini belirler. Kapı sonucu `data-dogrulama.md`'ye bir satır olarak eklenir
(tarih + kapı sonucu).

---

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.
