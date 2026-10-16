---
node_id: K013-integraion/synchronization/sync-sozlesme
node_type: technology-deepdive
title: "Sync Sözleşme — Token, Sayfalama, Hata Kodları, Tombstone, Idempotency Anahtarı"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/synchronization
children: []
depends_on:
  - K013-integraion/synchronization/sync-anayasa
  - K013-integraion/synchronization
  - K007-data
evidence:
  - "https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/delta-query-overview (2026-10-16)"
  - "https://docs.github.com/en/rest/using-the-rest-api/using-pagination-in-the-rest-api (2026-10-16)"
  - "https://docs.stripe.com/idempotency (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/throttling (2026-10-16)"
  - "https://docs.stripe.com/rate-limits (2026-10-16)"
  - "https://cloud.google.com/storage/docs/hashes-etags (2026-10-16)"
  - "https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html (2026-10-16)"
  - "https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html (2026-10-16)"
validation:
  - "Her sözleşme satırı §Kaynak Tablosu'ndaki fetch edilmiş URL ile eşleşir (2026-10-16)"
  - "Uç nokta adı/şema kolonu UYDURULMAZ — çekirdek kavramlar kaynaktaki adlarla verilir, CoreMusic karşılıkları ⚠️ ile işaretlenir"
  - "Doğrulanamayan hiçbir değer (süre, limit, biçim) yazılmaz"
  - "K015 sınır beyanı iki index'ten birebir okunarak §10'a işlendi (2026-10-16)"
open_questions:
  - "CoreMusic senkron uç noktası adı ve HTTP metodu → UNKNOWN (API tasarımı yok)"
  - "Token/jeton saklama biçimi (K007 şeması) → ⚠️ VERIFICATION REQUIRED"
  - "Hata gövdesi biçimi (RFC 9457 problem+json mı düzelt mi) → UNKNOWN (bu turda çekilmedi)"
  - "Checksum algoritması (CRC32C eşdeğeri?) ve reconciliation periyodu → UNKNOWN (kaynak var, CoreMusic kararı yok)"
group: mimari
---

# Sync Sözleşme — Token · Sayfalama · Hata · Tombstone · Idempotency

> **Bu dosya ne değildir:** OpenAPI belgesi veya gerçek bir endpoint
> listesi değildir. Bu dosya **senkronun kavramsal sözleşmesini** tanımlar:
> hangi alan zorunludur, hangi hata hangi aksiyonu tetikler, hangi token
> nasıl kullanılır. **Ad/biçim kanıtlıdır** (kaynak sayfalarındaki adlarla
> verilir); CoreMusic'in kendi adlandırması `UNKNOWN` olarak işaretlenir.
>
> **Durum etiketi:** KAVRAM IMPLEMENTED (**11 kaynak fetch**) · CoreMusic API
> yüzeyi ⚠️ PLANNED (bu turda senkron endpoint'i depoda tespit edilmedi).

---

## §0 ELI15 — Bu dosyanın işi

Bir senkron sistemin **iki tarafı arasında** konuşacağı dili yazar: ne
gönderirim (cursor, limit, idempotency anahtarı), ne alırım (kayıt, token,
tombstone), hata gelince ne yaparım (410 → full, 429 → bekle, 412 →
yeniden dene). Kaynakları `sync-anayasa.md` §0'daki eksenlerin
**sözleşmeye dönüşmüş hâlidir**. (Kaynak: §Kaynak Tablosu [R1]–[R11])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `sync-anayasa.md` | Eksenler, sınır, sahiplik tartışması | Kademe-3 |
| **mimari** | **`sync-sozlesme.md`** | **Token/hata/kavram sözleşmesi** | Kademe-3 |
| kod | `sync-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `sync-kod-ornekleri.md` | 13 dilde artımlı senkron örneği | Kademe-3 |
| kanit | `sync-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Sözleşme Katmanları

| Katman | İçerik | Bu dosyada | Kanıt |
|---|---|---|---|
| L1 — İstek | cursor, limit, idempotency anahtarı, condition | §3, §7 | [R1] [R3] [R5] |
| L2 — Yanıt | kayıt listesi, sonraki token, tombstone, versiyon | §4, §5, §6 | [R1] [R2] |
| L3 — Hata | 410/429/412/409 → aksiyon eşlemesi | §8 | [R2] [R6] [R7] |
| L4 — Uygulama | retry, backoff, idempotent apply, checksum, reconciliation | §7, §7A, §9, §9A | [R4] [R5] [R7] [R8] [R9] [R10] [R11] |
| L5 — Sahiplik | kim neyi değiştirir | §10 | integration-master §2.2 |

---

## §2 Temel Kavramlar — Kaynaktaki Adlarla

| Kavram | Kaynaktaki ad | Anlam | Kaynak |
|---|---|---|---|
| Tam sorgu | `base query` (AppSync) / ilk `delta` (Graph) | Önbelleği dolduran ilk çekim; `lastSync` **verilmez** | [R1] [R2] |
| Artımlı sorgu | `delta query` | Son checkpoint'ten sonrası | [R1] [R2] |
| İlerleme jetonu | `lastSync` + `startedAt` (AppSync) · `$deltatoken` (Graph) | Bir sonraki delta'nın başlangıcı | [R1] [R2] |
| Sayfalama jetonu | `nextToken` (AppSync) · `@odata.nextLink` + `$skiptoken` (Graph) | Aynı oturumdaki sonraki sayfa | [R1] [R2] |
| Sayfalama başlığı | `link` başlığı (`rel="next"`) | GitHub tarzı sayfalama | [R3] |
| Bitiş jetonu | `@odata.deltaLink` | Oturum bitti; gelecekteki delta bununla | [R2] |
| Silme bildirimi | `_deleted` (AppSync) · `@removed` (Graph) | İstemci yerel kopyayı **temizler** | [R1] [R2] |
| Versiyon | `_version` | Optimistic concurrency anahtarı | [R1] [R4] |
| Idempotency | `Idempotency-Key` başlığı | Tekrar isteği güvenli kılar | [R5] |

> ⚠️ **CoreMusic karşılıkları:** `lastSync`/`nextToken`/`_version` bu
> isimlerle **CoreMusic API'sinde var mı?** → `UNKNOWN` (API tasarımı yok).
> Tabloda adlar **kaynaktan** alınmıştır; CoreMusic'e taşınması ayrı bir
> karardır.

---

## §3 İstek Sözleşmesi (L1)

### §3.1 Artımlı istek alanları

| Alan | Zorunlu mu? | Anlam | Kanıt |
|---|---|---|---|
| `lastSync` (cursor) | Artımlı istekte **evet**; tam istekte **yok/boş** | "Şu andan sonrasını ver" | [R1] |
| `limit` | Hayır | Sayfa boyutu | [R1] |
| `nextToken` | Aynı oturumda devam ederken | Sonraki sayfa | [R1] [R3] |
| Condition (ETag/If-Match) | Yazmada önerilir | "Şu sürümdeyse uygula" | [R5] |
| `Idempotency-Key` | Yazmalarda (Stripe: tüm `POST`'larda) | Tekrar güvenliği | [R5] |

**Kural (kanıtlı davranış):** Cursor **opaktır** — istemci içeriğine
bakmaz, olduğu gibi geri verir; Graph'a göre jeton, istenilen `$select`
gibi parametreleri **kodlar**, bu yüzden sonraki istekte parametre
**değiştirilmez** (değiştirilirse jeton geçersizleşebilir). [R2]

**Kural (sayfalama):** GitHub: yanıt `link` başlığı döndürür;
`rel="next"` olan URL ile devam edilir; `per_page` maksimumu **100**'dür —
üzeri **hata vermez, sessizce kırpılır**. [R3]

### §3.2 İstek → durum eşlemesi

| İstemci durumu | İstek | Beklenen |
|---|---|---|
| İlk açılış | Cursor'suz (base) | Tüm kayıt + başlangıç jetonu [R1] |
| Sonraki çekim | Cursor'lu (delta) | Yalnız değişim + yeni jeton [R1] |
| Cursor kayboldu | Cursor'suz (base) | Tam yeniden senkron [R1] [R2] |
| `410 Gone` aldı | Cursor'suz (base) | Sıfırdan başlangıç [R2] |
| Süre doldu (`syncStateNotFound`) | Cursor'suz (base) | Sıfırdan başlangıç [R2] |

---

## §4 Yanıt Sözleşmesi (L2)

### §4.1 Alan seti (kanıtlı örnekler)

| Alan | Anlam | Kaynak |
|---|---|---|
| `items[]` | Değişim kayıtları (oluşturma/güncelleme standart temsil, silme `@removed` ile) | [R2] |
| `nextToken` | Daha sayfa var | [R1] |
| `startedAt` | Bu yanıtın itibari zamanı → **gelecek** delta cursor'u | [R1] |
| `_version` | Kaydın sürümü | [R1] |
| `_lastChangedAt` | Son değişiklik zaman damgası | [R1] |
| `_deleted` | Yalnız sync yanıtında döner; istemci cache'i **temizler** | [R1] |

### §4.2 Graph'ın ek davranışları (sözleşmeye girer)

| Davranış | Sözleşme etkisi | Kanıt |
|---|---|---|
| Bir sayfada `@odata.nextLink` **ve** `@odata.deltaLink` **birlikte olamaz** | İstemci ayrımı parse edebilir | [R2] |
| Aynı kayıt yanıtta **birden fazla** gelebilir | Apply idempotent olmalı | [R2] |
| Silmeden önceki kayıtlar ilk delta'da **görünmeyebilir** | İlk delta tam sayım **garanti etmez** | [R2] |
| Değişimlerin görünmesinde **gecikme** olabilir | Aynı jetonla **sonra tekrar** denenebilir | [R2] |
| Silme `reason: changed` (geri alınabilir) / `deleted` (geri alınamaz) | İstemci iki durumu **ayrı** işlemeli | [R2] |

---

## §5 Tombstone Sözleşmesi

| Kural | Ayrıntı | Kanıt |
|---|---|---|
| Silme **eksiltme değildir** | Kayıt silinince istemcideki kopyanın da silinmesi gerekir → tombstone zorunlu | [R1] [R2] |
| Tombstone TTL'li | AppSync: Delta tabloda `_ttl` ile otomatik temizlik; süre veri kaynağı üzerinde yapılandırılır | [R1] |
| TTL aşıldı | İstemci cache'i **base query (global catch-up)** ile temizler | [R1] |
| Versiyonlu silme | `DeletePostInput` gibi silme girdisi `_version` **ister** → optimistic silme | [R1] |

> ⚠️ **CoreMusic TTL değeri:** `UNKNOWN` (ölçüm/ADR yok — uydurulmaz).

---

## §6 Versiyon (Optimistic Concurrency) Sözleşmesi

| Kural | Kanıt |
|---|---|
| Güncelleme/silme isteği **mevcut sürümü** taşır | [R1] (`_version: Int!` zorunlu) |
| Sürüm tutmuyorsa sunucu **reddeder** → istemci güncel veriyle tekrar dener | [R4] |
| Her başarılı mutation sürümü artırır (`1 → 2`) | [R1] |
| Alternatif HTTP karşılığı: `ETag` + `If-Match`; önkoşul sağlanmazsa **412** | [R5] |
| `ETag` zayıf (`W/`) vs güçlü karşılaştırma ayrımları RFC 9110 §8.8.1'dedir | [R5] |

---

## §7 Idempotency Sözleşmesi

**Kaynak davranış (Stripe):**

| Kural | Değer | Kanıt |
|---|---|---|
| Başlık | `Idempotency-Key` | [R5] |
| Anahtar üretimi | **İstemci** üretir; öneri: V4 UUID | [R5] |
| Uzunluk | ≤ **255 karakter** | [R5] |
| Hassas veri | Anahtarda **kullanılmaz** (e-posta/kişisel veri) | [R5] |
| v1 yanıt saklama | Aynı anahtarla gelen tekrar, **ilk** yanıt kodu+gövdesini döner (500'ler dahil) | [R5] |
| Parametre uyuşmazlığı | Anahtar özgün istekle **farklı** parametre içerirse hata | [R5] |
| Pruning | v1'de anahtar **≥24 saat** sonra silinebilir; sonra yeniden kullanılırsa **yeni** istek sayılır | [R5] |
| `GET`/`DELETE` | Anahtar **gönderilmez** (zaten idempotent) | [R5] |
| Doğrulama başarısızlığı/konflik | Sonuç **saklanmaz** → tekrar denenebilir | [R5] |

**Senkron'a etkisi (tasarım iddiası, [R1] [R2] + [R5] dayanak):**
Artımlı çekimde aynı kayıt ikinci kez gelirse (replay) uygulama
**idempotent** olmalıdır: aynı anahtar/sürüm ile ikinci uygulama ikinci kayıt
**açmaz**. Bunun sözleşmedeki karşılığı `sync-dogrulama.md` §5'tedir.

---

## §7A Checksum / Değişim Tespiti Sözleşmesi (L4 eki)

> **Ne için:** Delta/journal **yokken** (veya güvenilirliği
> doğrulanamazken) iki taraf arasındaki farkı **checksum ile** bulmak.
> Kaynak: Google Cloud Storage — "Data validation and change detection
> with checksums" [R8] (fetch 2026-10-16).

### §7A.1 Kanıtlı kurallar (GCS)

| Kural | Ayrıntı | Kanıt |
|---|---|---|
| Desteklenen algoritmalar | **CRC32C** ve **MD5** | [R8] |
| Önerilen yöntem | **CRC32C** ("recommended validation method for performing integrity checks") | [R8] |
| MD5 sınırı | Parçalı yüklemede (multipart / composite) **desteklenmez**; yalnız tek-dosya yüklemelerde geçerli | [R8] |
| Yazmada doğrulama | İstemci checksum'ı hesaplar → isteğe ekler; sunucu kendi checksum'ıyla **karşılaştırır**; uyuşmazlıkta istek **`400 BadRequestException`** ile **reddedilir** | [R8] |
| Kopya/rewrite | Sunucu, kaynak nesnenin **değiştirilemez checksum'ı** ile otomatik doğrulama yapar | [R8] |
| İstemci tarafı doğrulama | Yüklenen nesnenin metadata'sını oku → hash'i karşılaştır → uyuşmazlıkta **sil** (başa sarma) | [R8] |
| İndirmede | Sunucu kayıtlı checksum'ı yanıtla gönderir; istemci kendi hesabını **karşılaştırır**; uyuşmazlık → veri bozulmuş sayılır, **yeniden dene** | [R8] |

### §7A.2 Değişim tespiti kademesi (gcloud `rsync` — kaynak: [R8])

| Kademe | Ne kıyaslanır | Ne zaman |
|---|---|---|
| 1 | **Checksum** (MD5/CRC32C) | İkisi de bulut kovası ve nesnede checksum varsa; **veya** nesnenin hiç `mtime`'ı yoksa |
| 2 | **Boyut + `mtime`** | İkisinde de `mtime` varsa (ör. dosya sistemi ↔ dosya sistemi); kova ↔ yerel dosya sisteminde kova için `time created` `mtime` yerine kullanılır |
| 3 | **Yalnız boyut** | Ne `mtime` ne checksum varsa (ör. composite nesne + CRC32C desteklemeyen sağlayıcı) |

**Sözleşme etkisi (tasarım iddiası):** Bir senkron sistemi "değişti mi?"
kararını **kademe kademe** alır: önce ucuz checksum, yoksa metadata, yoksa
boyut. CoreMusic'te hangi kademenin kullanılacağı → `UNKNOWN` (karar yok;
uydurulmaz).

### §7A.3 Çelişki/divergence davranışı (GCS CLI örneği)

- gcloud `cp`/`mv`/`rsync` kopyalama sonrası **otomatik** doğrulama yapar;
  kaynak ile hedef checksum'ı eşleşmezse **geçersiz kopyayı siler ve uyarı
  basar**. [R8]
- Bu davranış, "senkron **başarılı** sayılma eşiği = checksum eşleşmesi"
  sözleşmesinin kanıtlı bir örneğidir.

> ⚠️ **CoreMusic karşılıklaştırmaları:** checksum algoritması, kademe
> seçimi ve "uyuşmazlıkta sil/say" aksiyonu → **`UNKNOWN`** (tasarım kararı
> yok). Bu bölüm yalnızca **seçenek ve davranış** taşır.

---

## §8 Hata → Aksiyon Eşlemesi (L3)

| HTTP | Anlam (kaynak) | Sync aksiyonu | Kanıt |
|---|---|---|---|
| **410 Gone** | Graph: bakım/migrasyon → delta jetonu geçersiz (yanıtta boş `$deltatoken`) | **Full sync'e dön** | [R2] |
| **4xx + `syncStateNotFound`** | Graph: jeton süresi doldu (directory için **7 gün**) | **Full sync'e dön** | [R2] |
| **429 Too Many Requests** | Throttling / rate limit | `Retry-After` kadar **bekle**, tekrar dene; cursor **ilerletilmez** | [R6] [R7] |
| **429 (Stripe, başlık `Stripe-Rate-Limited-Reason`)** | `global-rate` / `endpoint-rate` / `global-concurrency` / `endpoint-concurrency` / `resource-specific` | Jitter'lı **exponential backoff** | [R7] |
| **429 + `lock_timeout` (Stripe)** | Rate limit **değil**; nesne kilidi | Kısa backoff ile **tekrar dene** (kilit serbest kalınca) | [R7] |
| **412 Precondition Failed** | RFC 9110: önkoşul sağlanmadı (ör. `If-Match` tutmadı) | **Çakışma say** → §6 (yeniden oku-uygula) | [R5] |
| **409 Conflict** | Çakışan istek | Çakışma stratejisine gönder (`sync-anayasa` §5) | ⚠️ eşleme **tasarım iddiası** |
| **403 / 429 (GitHub)** | Aşımda `x-ratelimit-remaining: 0` | `x-ratelimit-reset`'e kadar **bekle** | [R6] |
| **304 Not Modified** | RFC 9110: koşullu GET, içerik değişmedi | Kaynağı **güncelleme**; mevcut kopyayı koru | [R5] |

**GitHub kuralı (ayrıntı):** `x-ratelimit-*` başlıkları **otoritedir**
(`GET /rate_limit` ile çelişirse başlığa güvenilir); aşımda yanıt `403`
veya `429` olur; `retry-after` varsa o kadar saniye, yoksa en az **1 dakika**
beklenir; tekrar tekrar denemek **entegrasyonun banlanmasına** yol açabilir. [R6]

**Graph kuralı (ayrıntı):** `429` + `Retry-After: 10` gibi bir yanıt örneği
dokümante edilmiştir; en hızlı kurtarma `Retry-After`'a uymaktır; header
yoksa exponential backoff önerilir; **sürekli polling → throttling'in baş
nedenidir**, delta/change tracking kullanılması önerilir. [R6]

---

## §9 Retry / Backoff Sözleşmesi (L4)

| Kural | Değer | Kanıt |
|---|---|---|
| 429'da önce `Retry-After` | Sunucu dediği kadar bekle | [R6] [R7] |
| Header yoksa exponential backoff | Üstel büyüme | [R6] [R7] |
| Backoff'a **jitter** ekle | Thundering herd önlenir | [R7] |
| İdempotency anahtarı **yeniden denemelerde aynı** kalır | Farklı anahtar = ikinci işlem | [R5] |
| Cursor, **başarılı** apply'den sonra ilerletilir | Yarı-yazma → tekrar güvenli | [R1] [R5] |
| Eşzamanlı istek sınırları | GitHub ≤100 eşzamanlı · Stripe global 50 | [R6] [R7] |
| İstemci SDK davranışı (Stripe) | SDK `429`'ları otomatik yeniden dener; **lock_timeout** dahil | [R7] |

---

## §9A Reconciliation (Satır Karşılaştırma) Sözleşmesi (L4 eki)

> **Ne için:** Senkron **bitti** demeden önce iki tarafın **aynı** olduğunu
> kanıtlamak — iki biçim: (a) satır bazında karşılaştırma, (b) sayaçla
> sayı sayımı. Kaynak: AWS DMS data validation [R9] · AWS CLI
> `describe-table-statistics` [R10] (fetch 2026-10-16).

### §9A.1 Satır bazlı karşılaştırma (AWS DMS validation — [R9])

| Sözleşme kalemi | Kanıtlı davranış | Kanıt |
|---|---|---|
| Ne karşılaştırılır | **Kaynaktaki her satır** ile hedefteki karşılığı; satırların aynı veriyi içerdiği doğrulanır, uyuşmazlık raporlanır | [R9] |
| Ne zaman başlar | Validation etkinse **full load biter bitmez** | [R9] |
| CDC'de | Artımlı değişiklikler **oluşurken** karşılaştırılır | [R9] |
| Durum (`ValidationState`) | `Not enabled` · `Pending records` · `Mismatched records` · `Suspended records` · `No primary key` · `Table error` · `Validated` · `Error` · `Pending validation` · `Preparing table` · `Pending revalidation` | [R9] [R10] |
| Sayaçlar | `ValidationPending` (gönderilmiş, doğrulanmamış) · `ValidationSuspended` (karşılaştırılamayan) · `ValidationFailed` (geçemeyen) | [R9] [R10] |
| Uyuşmazlık ayrıntısı | Hedefte kontrol tablosu `awsdms_control.awsdms_validation_failures_v1`; `FAILURE_TYPE` ∈ **`RECORD_DIFF`** (veri farkı) / **`MISSING_SOURCE`** / **`MISSING_TARGET`** / **`TABLE_WARNING`**; `DETAILS` JSON'unda uyuşmayan kolon değerleri | [R9] |
| Önkoşul | Tabloda **primary key veya unique index yoksa doğrulama yapılamaz** (`No primary key`); PK kolonları `CLOB/BLOB/BINARY/BYTE` olamaz; `VARCHAR/CHAR` PK uzunluğu **<1024** | [R9] |
| Sürekli değişen satır | Karşılaştırılamaz → `ValidationSuspended` (sürekli güncellenen kaynak satır) | [R9] |
| Durma eşiği | **10.000'i** aşan başarısız/suspended kayıtta doğrulama **durur** (altta yatan sorun çözülmeli) | [R9] |
| Hedef dış müdahale | Doğrulama sırasında hedefe **başka** uygulama yazarsa farklar doğru raporlanmayabilir | [R9] |

### §9A.2 Sayaçla sayı sayımı (`describe-table-statistics` — [R10])

| Sayaç grubu | Alanlar | Sözleşmedeki karşılığı |
|---|---|---|
| Kaynakta yapılan | `Inserts` · `Deletes` · `Updates` · `Ddls` | "beklenen olay sayısı" |
| Hedefe uygulanan | `AppliedInserts` · `AppliedDeletes` · `AppliedUpdates` · `AppliedDdls` | "uygulanan olay sayısı" |
| Full load | `FullLoadRows` · `FullLoadErrorRows` · `FullLoadCondtnlChkFailedRows` · `FullLoadStartTime`/`EndTime` · `FullLoadReloaded` | "ilk yükleme satır sayısı" |
| Validation | `ValidationPendingRecords` · `ValidationFailedRecords` · `ValidationSuspendedRecords` · `ValidationState`(+`Details`) | doğrulama kapı sayaçları |
| Resync | `ResyncRowsAttempted` · `ResyncRowsSucceeded` · `ResyncRowsFailed` · `ResyncProgress` (%) | **telafi** (repair) sayımı |
| Tablo durumu | `TableState`: `Table does not exist` · `Before load` · `Full load` · `Table completed` · `Table cancelled` · `Table error` · `Table is being reloaded` | yaşam döngüsü |
| Sayfalama | İşlem **paginate** edilir (`Marker` / `NextToken`) — tek yanıtta tüm tablolar gelmeyebilir | §3.1 sayfalama sözleşmesiyle aynı kural |

**Sözleşme etkisi (tasarım iddiası):** "Senkron tamamlandı" beyanı
**iki koşula** bağlanır: (1) olay sayaçları kaynakta = hedefte uygulanan
(`Inserts` ≈ `AppliedInserts` vb.), (2) `ValidationFailed` = **0** ve
`ValidationPending` = **0**. Aksi hâlde durum `Validated` **değildir**.
CoreMusic'in bu sayaçlara karşılık gelen metrik adları → `UNKNOWN`.

### §9A.3 Dry-run / validation-only (doğrulamayı yazmadan çalıştırma)

- AWS DMS **validation only task**: "preview and validate data **without
  performing any migration or data replication**" — yazma yapmadan
  karşılaştırma görevi (`EnableValidation` + `ValidationOnly` = true). [R9]
- Argo CD **dry run**: sync apply'dan önce dry-run ile çalışır;
  `SkipDryRunOnMissingResource=true` yalnızca **henüz kümede olmayan** tür
  için dry-run'ı atlar; CRD zaten varsa **dry-run yine çalışır**. [R11]
  (Argo CD sync-options, §12'de R11 olarak kayıtlıdır.)

> ⚠️ **CoreMusic karşılıklaştırmaları:** reconciliation periyodu, sayaç
> eşikleri ve "dry-run önce-sonra" sırası → **`UNKNOWN`/`⚠️`** (ölçüm/ADR
> yok). Bu bölüm yalnız kaynaktaki davranışı taşır.

---

## §10 Sahiplik Sözleşmesi (L5) — K013 ↔ K007 ↔ K015

| Sözleşme kalemi | Sahip | Not |
|---|---|---|
| Token/cursor **alan tanımı ve tipi** (şema) | **K007-data** | integration-master §2.2 — bu dosya şema **yazmaz** |
| İstek/yanıt **kavramsal** akışı | **K013 / bu dosya** | §3–§9A |
| Medya/katalog senkronu kapsamı | **⚠️ K013 ↔ K015 belirsiz** | `sync-anayasa` §11 — **karar verilmez** |
| Dış-sistem eşitlemesi (K015 kendi sınır beyanı) | **K013** | K015 `synchronization/index.md`: "Dış sistem eşitlemesi K013/synchronization (aynı ad) · ağ K014" (disk okuma 2026-10-16) |
| K013'ün kendi sınır beyanı | **belirsiz** | K013 `synchronization/index.md`: "medya katalog senkronu K015 ile sınır belirsiz" (disk okuma 2026-10-16) |
| Doğrulama kapıları | `sync-dogrulama.md` | CI/test (§7A–§7C orada tanımlı) |
| Sahiplik kararı (ADR) | Orkestratör/üst karar | commit subagent'a ait değil |

---

## §11 Yasaklar

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | Uç nokta adı/HTTP metodu **uydurmak** | API tasarımı yok → `UNKNOWN` |
| 2 | `Idempotency-Key` saklama süresini CoreMusic'e **atfetmek** | 24 saat Stripe'a ait [R5]; CoreMusic için `⚠️` |
| 3 | 409/412 eşlemesini **kesin** saymak | 412 RFC kanıtlı, 409 eşlemesi tasarım iddiası (§8) |
| 4 | Hata gövdesi şeması **üretmek** | Bu turda problem-details kaynağı **çekilmedi** → `UNKNOWN` |
| 5 | Checksum algoritması / reconciliation periyodu **seçmek** | Kaynak var, CoreMusic kararı yok → `UNKNOWN` (§7A/§9A) |
| 6 | `git commit` **atmak** | Orkestratöre ait |

---

## §12 Red Listesi

| # | Yazılmayan iddia | Neden |
|---|---|---|
| 1 | "CoreMusic sync endpoint'i `GET /sync`'tir" | Tasarım yok → `UNKNOWN` |
| 2 | "Cursor 30 gün saklanır" | Süre uydurmak yasak → `⚠️` |
| 3 | "Hata gövdesi RFC 9457'dir" | Bu turda çekilmedi → `⚠️` |
| 4 | "Medya senkronu sözleşmesi şudur" | Sahiplik açık → `sync-anayasa` §11 |
| 5 | "Stripe 24 saat anahtar süresi CoreMusic'te de geçerlidir" | Atıf yasak → §7 |

---

## §Kaynak Tablosu

| # | Kaynak | Kanıt verdiği iddia | Tarih | Durum |
|---|---|---|---|---|
| R1 | https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html | base/delta sorgu, `lastSync`, `startedAt`, `nextToken`, `_version`, `_deleted`, `_ttl`, zorunlu `_version`'lu silme | 2026-10-16 | ✅ fetch |
| R2 | https://learn.microsoft.com/en-us/graph/delta-query-overview | opak jetonlar, nextLink/deltaLink ayrımı, `@removed` nedenleri, 410 Gone, 7 gün jeton, `syncStateNotFound`, replay, gecikme | 2026-10-16 | ✅ fetch |
| R3 | https://docs.github.com/en/rest/using-the-rest-api/using-pagination-in-the-rest-api | `link` başlığı, `rel="next"`, `per_page` maks 100 ve sessiz kırpma | 2026-10-16 | ✅ fetch |
| R4 | https://docs.aws.amazon.com/appsync/latest/devguide/conflict-detection-and-sync.html | optimistic/automerge/lambda çakışma stratejileri | 2026-10-16 | ✅ fetch |
| R5 | https://www.rfc-editor.org/rfc/rfc9110.html | idempotent methodlar (§9.2.2), koşullu istekler (§13), ETag/If-Match, 412, 304, Retry-After (§10.2.3) · Stripe `Idempotency-Key` kuralları (§7) | 2026-10-16 | ✅ fetch (Stripe: docs.stripe.com/idempotency) |
| R6 | https://learn.microsoft.com/en-us/graph/throttling · https://docs.github.com/en/rest/overview/rate-limits-for-the-rest-api | 429+Retry-After davranışı, polling → delta önerisi · x-ratelimit-* otoritesi, 403/429, 100 eşzamanlı | 2026-10-16 | ✅ fetch (iki URL tek satırda **iki ayrı** kaynaktır) |
| R7 | https://docs.stripe.com/rate-limits | 429 + `Stripe-Rate-Limited-Reason`, `lock_timeout` ayrımı, jitter + exponential backoff, eşzamanlılık 50 | 2026-10-16 | ✅ fetch |
| R8 | https://cloud.google.com/storage/docs/hashes-etags | CRC32C (önerilen) + MD5; MD5 parçalı yüklemede desteklenmez; yazmada uyuşmazlık → 400; kopya/rewrite otomatik doğrulama; gcloud `rsync` değişim tespiti kademesi: checksum → size+mtime → yalnız size | 2026-10-16 | ✅ fetch |
| R9 | https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html | Satır-bazlı karşılaştırma, ValidationState enum, ValidationPending/Suspended/Failed, kontrol tablosu `awsdms_control.awsdms_validation_failures_v1` (FAILURE_TYPE), PK önkoşulu, 10.000 hata durağı, validation-only task ("preview and validate … without performing any migration") | 2026-10-16 | ✅ fetch |
| R10 | https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html | Inserts/Deletes/Updates/Ddls · Applied* · FullLoadRows/ErrorRows · Validation*Records · ValidationState(+Details) · ResyncRows*/ResyncProgress · TableState · sayfalama (`Marker`) | 2026-10-16 | ✅ fetch |
| R11 | https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ | Dry-run senkron; `SkipDryRunOnMissingResource` (CRD varsa dry-run yine çalışır); `Prune=confirm`/`Delete=confirm` manuel onay kapıları; `ApplyOutOfSyncOnly`; `FailOnSharedResource` | 2026-10-16 | ✅ fetch |

> **Kapsam beyanı:** Bu dosyada dayanak alınan URL kümesi yalnız bu
> tablodur. `⚠️`/`UNKNOWN` satırlarının dayanağı yoktur.

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | CoreMusic uç nokta adı/metodu → `UNKNOWN` | FM open_questions |
| 2 | Token saklama şeması → K007'ye devredildi | ⚠️ VERIFICATION REQUIRED |
| 3 | Hata gövdesi biçimi → `UNKNOWN` (kaynak çekilmedi) | FM open_questions |
| 4 | 409 eşlemesi → tasarım iddiası (412 RFC kanıtlı) | §8 notu |
| 5 | K013↔K015 sahipliği → kararı bu dosyada **verilmez** (iki index sınır beyanı §10'a işlendi) | `sync-anayasa` §11 |
| 6 | Checksum/değişim tespiti sözleşmesi → kaynak ✅ çekildi ([R8]); **algoritma seçimi** `UNKNOWN` | §7A |
| 7 | Reconciliation (satır + sayaç) sözleşmesi → kaynak ✅ çekildi ([R9] [R10]); **periyot/eşik** `UNKNOWN` | §9A |
| 8 | Dry-run/validation-only davranışı → kaynak ✅ çekildi ([R9] [R11]) | §9A.3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** AWS AppSync · AWS DMS (validation + CLI table statistics) · Argo CD (sync-options) · Microsoft Graph (delta/throttling) · GitHub REST Docs · Stripe (idempotency/rate-limits) · IETF RFC 9110 · Google Cloud Storage (hashes-etags) · integration-master §2.2 · K015-media/synchronization/index.md (disk kanıtı) · **Tarih: 2026-10-16**
