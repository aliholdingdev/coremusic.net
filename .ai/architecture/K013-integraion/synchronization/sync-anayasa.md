---
node_id: K013-integraion/synchronization/sync-anayasa
node_type: technology-deepdive
title: "Sync Anayasa — Full/Incremental · Push/Pull · Delta · Çakışma · Idempotency · Kuyruk · Checkpoint"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/synchronization
children: []
depends_on:
  - K013-integraion/synchronization
  - K013-integraion
  - K007-data
evidence:
  - "https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html (2026-10-16)"
  - "https://docs.aws.amazon.com/appsync/latest/devguide/conflict-detection-and-sync.html (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/delta-query-overview (2026-10-16)"
  - "https://docs.cloud.google.com/spanner/docs/change-streams (2026-10-16)"
  - "https://cloud.google.com/pubsub/docs/ordering (2026-10-16)"
  - "https://www.postgresql.org/docs/current/logical-replication.html (2026-10-16)"
  - "https://docs.stripe.com/idempotency (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://crdt.tech/ (2026-10-16)"
  - "https://www.inkandswitch.com/essay/local-first/ (2026-10-16)"
  - "https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html (2026-10-16)"
  - "https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html (2026-10-16)"
  - "https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ (2026-10-16)"
  - "https://cloud.google.com/storage/docs/hashes-etags (2026-10-16)"
validation:
  - "Her iddia §Kaynak Tablosu'nda [S#] ile URL eşlemesine sahiptir (2026-10-16 fetch)"
  - "K013↔K015 sahipliği karar VERİLMEZ — iki ihtimal + sınır tablosu (§11)"
  - "Doğrulanamayan her satır ⚠️ VERIFICATION REQUIRED / UNKNOWN işaretlidir"
  - "§11.1'deki eski 'K015 glob 0 sonuç' satırı DÜZELTİLDİ — K015-media diskte mevcut (15 md); hidden:false tuzağı not edildi (2026-10-16)"
open_questions:
  - "synchronization sahipliği: K013 mü K015 mi? → ⚠️ VERIFICATION REQUIRED (integration-master §7/2) — K015 sibling index sınır beyanıyla daraltıldı, karar yok"
  - "İki index'in sınır beyanı birbirini daraltıyor mu (K013↔K015) → ⚠️ VERIFICATION REQUIRED (§11.2)"
  - "CoreMusic'in senkron yönü (dış → iç mi, çift yönlü mü)? → UNKNOWN (karar yok)"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
group: mimari
---

# Sync Anayasa — Full/Incremental · Push/Pull · Delta · Çakışma · Idempotency · Kuyruk · Checkpoint

> **Bu dosya ne değildir:** Kod, kütüphane seçimi veya satır-içi uygulama
> rehberi değildir. Bu dosya **mimari karar yüzeyini** tanımlar: senkronun
> hangi eksenlerde bölüneceği, her eksenin kanıtlanmış endüstri karşılığı
> ve CoreMusic sınırı. Uygulama örnekleri `sync-kod-ornekleri.md`'dedir;
> doğrulama kapıları `sync-dogrulama.md`'dedir.
>
> **Durum etiketi:** KAVRAM/şartname IMPLEMENTED (**17 resmi kaynak fetch**
> ile doğrulandı — §Kaynak Tablosu; S3 adayı 404 → elendi) · CoreMusic kodu
> ⚠️ PLANNED (bu turda senkron implementasyonu depoda tespit edilmedi →
> `⚠️ VERIFICATION REQUIRED`).

---

## §0 ELI15 — Senkron bir cümlede

İki taraf arasındaki veriyi **bir kez tam al** (full), sonrasında **yalnız
değişeni al** (incremental/delta); değişikliği **ya sen bildir** (push) **ya
da ben sorayım** (pull); aynı işlemi **ikinci kez uygulamak zarar vermesin**
(idempotency); **sıra bozulmasın** (sıralı kuyruk); **nerede kaldığını
kaydet** (checkpoint); **çakışırsa** bir kurala göre birleştir (çakışma
çözümü); **limiti aşarsa** bekle-geri-dön (rate limit).
(Kaynak: §Kaynak Tablosu [S1]–[S18])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| **mimari** | **`sync-anayasa.md`** | **Eksenler, sınır, sahiplik tartışması** | Kademe-3 |
| mimari | `sync-sozlesme.md` | Token/hata/kavram sözleşmesi | Kademe-3 |
| kod | `sync-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `sync-kod-ornekleri.md` | 13 dilde artımlı senkron örneği | Kademe-3 |
| kanit | `sync-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Kavram Sözlüğü — Kanıtlanmış Terimler

| Terim | Anlam | Kanıt |
|---|---|---|
| Full sync (base query) | `lastSync` verilmeden yapılan **tam** sorgu; istemci yerel önbelleğini ilk kez doldurur | [S1] |
| Incremental sync (delta query) | Son senkron anından **sonraki** değişimleri çeken artımlı sorgu | [S1] [S4] |
| Cursor / sync token | Bir sonraki artımlı sorgunun başlangıç noktası (`lastSync`, `$deltatoken`, `startedAt`) | [S1] [S4] |
| Journal / Delta tablo | Değişimlerin ayrı bir "günlük" tabloda saklanması (Base = gerçek, Delta = journal) | [S1] |
| Tombstone | Silinen kaydın **varlığını bildiren** işareti (`_deleted`, `@removed`) | [S1] [S4] |
| Optimistic concurrency | Kayıt sürümü (`_version`) ile yazma; çakışırsa reddet + yeniden dene | [S2] |
| Automerge | Çakışmayı **veri tipine göre otomatik** birleştirme (liste birleşimi, küme birleşimi) | [S2] |
| Lambda/Custom resolution | Karmaşık çakışmada **özel mantık** çağırma | [S2] |
| Idempotency key | Aynı isteğin tekrarını **aynı sonuçla** karşılayan istemci anahtarı | [S8] |
| Ordering key | İlgili mesajların **sırasını koruyan** anahtar | [S6] |
| At-least-once | Mesajın **en az bir kez** gelmesi; tekrar işleme riski taşıdığı için idempotency şart | [S6] |
| Checkpoint | Kalıcılık noktası: hata/tekrar durumunda **nereden devam** edileceğinin kaydı | [S5] |
| Conditional request | `ETag`/`If-Match` ile "değişmediyse 304, çakışırsa 412" | [S9] |
| Delta query vs change notification | Delta = **pull** (ben çekerim), notification = **push** (sen bildirirsin) | [S4] |

---

## §2 Eksen 1 — Full vs Incremental (ne zaman tam?)

### §2.1 ELI15

İlk açılışta tüm katalog gerekir → **full**. Sonrasında saatlerce aynı
kataloğun tamamını çekmek israftır → **delta**. Ama delta'nın güvenilir
olduğu **varsayılamaz**: kayıt silinmiş, jeton süresi dolmuş veya sistem
bakım yapmış olabilir → periyodik bir **global catch-up (full tekrar)**
zorunludur. (Kaynak: [S1] "global catchup process" · [S4] "410 Gone → full
synchronization restart")

### §2.2 Karar Tablosu

| Durum | Yöntem | Kanıt |
|---|---|---|
| İlk kurulum / yeni istemci | Full (base query) | [S1] |
| İstemci tekrar çevrimiçi | Delta (son `lastSync` ile) | [S1] |
| Delta jetonu süresi doldu (Graph: directory nesnelerinde **7 gün**) | Full restart; hata kodu `syncStateNotFound` | [S4] |
| Delta `410 Gone` döndürdü (Graph: bakım/migrasyon) | Full restart (boş `$deltatoken`) | [S4] |
| Delta journal TTL'si aşıldı (AppSync: `DeltaSyncTableTTL`) | Periyodik base query (global catch-up) | [S1] |
| Değişim gecikmesi olabilir (Graph: eventual consistency) | Aynı deltaLink'i **bir süre sonra tekrar** dene | [S4] |
| Aynı kayıt yanıtta **birden fazla** görünebilir (Graph: replay) | İşlem katmanı tekrarı tolere etmeli (idempotent apply) | [S4] |

### §2.3 CoreMusic sınırı

- **Tam katalog/şema tanımı** → bu dosya değil: şema yazımı **K007-data**
  aittir (integration-master §2.2).
- **Senkron stratejisi seçimi** (full/delta periyodu, TTL) → bu dosya (§2.2).
- Periyot değerleri (delta TTL kaç saat?) → `⚠️ VERIFICATION REQUIRED`
  (CoreMusic'te ölçüm/ADR **yok**; uydurulmaz).

---

## §3 Eksen 2 — Push vs Pull (kim başlattı?)

| Model | Mekanizma | Avantaj | Kanıt |
|---|---|---|---|
| **Pull (delta query)** | İstemci periyodik çeker | Basit, istemci kontrolü; gecikme toleransı varsa yeterli | [S4] |
| **Push (change notification)** | Kaynak bildirim gönderir | Düşük gecikme; **polling'i neredeyse sıfırlar** | [S4] |
| **Push + Pull hibrit** | Bildirim gelince delta çek | Graph'ın **önerdiği** kalıp: "nearly eliminate polling" + azalan throttling | [S4] |
| Push (mesaj kuyruğu) | Yayınla/abone ol; sıralı anahtarla | Değişim yakalama + sıra garantisi | [S6] |

**Kanıt (hibrit kalıp):** Microsoft Graph, change notification (push) +
delta query (pull) birleştirmenin "isteği sık sık çektirmeyi büyük ölçüde
ortadan kaldırdığını" ve throttling olasılığını düşürdüğünü yazar. [S4]

**Red:** "CoreMusic push kullanacak" **DENMEZ** — depoda karar yok →
`UNKNOWN` (§14/3).

---

## §4 Eksen 3 — Delta mı, Karşılaştırma mı (diff)?

| Yöntem | Ne yapar | Nerede kanıtlanmıştır | Risk |
|---|---|---|---|
| **Delta/journal** | Yalnız **değişeni** üretir | AppSync Base+Delta tablo [S1] · Spanner change streams [S5] · Postgres logical replication [S7] | Journal TTL aşımında veri boşluğu |
| **Karşılaştırma (checksum/diff)** | İki tarafın anlık görüntüsünü karşılaştırır | AWS DMS validation: kaynak satırı **hedef satır ile tek tek karşılaştırır**, uyuşmazlığı raporlar [S15] · GCS: CRC32C/MD5 checksum ile değişim tespiti [S18] | Büyük veride maliyetli; **PK/unique index zorunlu** [S15]; sürekli değişen satırlar karşılaştırılamaz [S15] |
| **Commit timestamp tarama** | `commit timestamp > checkpoint` taraması | Spanner change streams: commit timestamp + transaction ID + partition token [S5] | Checkpoint yönetimi **API kullanıcısının** sorumluluğunda [S5] |

**Kanıt (Postgres):** Logical replication önce **snapshot** alır (tam
kopya), sonra değişiklikleri **yayıncının işlediği sırayla** gönderir —
"transactional consistency is guaranteed for publications within a single
subscription". Yani full+delta **tek kalıp**tır: snapshot = full, changes =
delta. [S7]

**Kanıt (Spanner):** Change streams `retention` **1–30 gün** tutar; değer
yakalama `OLD_AND_NEW_VALUES`/`NEW_VALUES`/`ROW_ID` olabilir; çıktı
commit timestamp + transaction ID taşır; **partition token** ile okuma
bölünür. [S5]

**Kanıt (AWS DMS — karşılaştırma/karşılaştırma yöntemi):** Validation
etkinse doğrulama **full load biter bitmez** başlar; CDC'li görevde artımlı
değişiklikler **oluşurken** karşılaştırılır. DMS "kaynaktaki her satırı
hedefteki karşılığıyla karşılaştırır", satırların aynı veriyi içerdiğini
doğrular ve uyuşmazlıkları raporlar. Tablo-bazlı sayaçlar: `ValidationPending`
(gönderilmiş ama doğrulanmamış), `ValidationSuspended` (karşılaştırılamayan —
ör. sürekli değişen satır), `ValidationFailed` (geçemeyen); durumlar
`Validated` · `Mismatched records` · `No primary key` · `Pending records`
vb. Uyuşmazlık ayrıntısı hedefteki kontrol tablosuna
(`awsdms_control.awsdms_validation_failures_v1`) yazılır: `FAILURE_TYPE` ∈
`RECORD_DIFF` / `MISSING_SOURCE` / `MISSING_TARGET` / `TABLE_WARNING`. [S15]

**Kanıt (Google Cloud Storage — checksum ile değişim tespiti):** GCS
CRC32C ve MD5 checksum destekler; **CRC32C önerilen doğrulama yöntemidir**;
MD5 parçalı yüklemeler (multipart/composite) için **desteklenmez**. Yazmada
istemci checksum'ı hesaplayıp gönderir, sunucu karşılaştırır; uyuşmazlıkta
istek **400 (BadRequestException)** ile reddedilir. gcloud `cp`/`mv`/`rsync`
kaynak ile hedef arasındaki farkı MD5 veya CRC32C ile belirler; **checksum
yoksa** `mtime`+boyut, **`mtime` de yoksa yalnız boyut** ile yetinir. [S18]

**Sınır (CoreMusic):** Hangi yöntemin (journal/delta mı, karşılaştırma mı)
kullanılacağı → `UNKNOWN` (karar yok). Bu bölüm yalnızca iki yöntemin de
resmi kaynaklarla **mevcut ve kanıtlanmış** olduğunu gösterir.

---

## §5 Eksen 4 — Çakışma Çözümü

### §5.1 Kanıtlanmış üç strateji (AppSync)

| Strateji | Davranış | Ne zaman | Kanıt |
|---|---|---|---|
| **Optimistic concurrency** | Çakışan mutation'ı **reddeder**, istemci güncellenmiş veriyle tekrar dener | Varsayılan; çakışma nadirse | [S2] |
| **Automerge** | Veri tipine göre **otomatik** birleştirir: listeler birleşir, kümeler birleşir, skaler **mevcut değer korunur** | Çakışma sık, alan bazlı kabul edilebilir | [S2] |
| **Lambda-based resolution** | Özel iş mantığı çağrılır | İş kuralı karmaşık (ör. iki taraf farklı para birimi) | [S2] |

### §5.2 Versiyon ve tombstone

- Kayıtlara **versiyon meta verisi** otomatik eklenir; mutation'lar Delta
  tabloya **tombstone** (silme işareti) yazar; silinen kayıt TTL'si ile
  zamanla temizlenir. [S2] [S1]
- Graph tarafında silme `@removed` nesnesiyle bildirilir; `reason: changed`
  (geri alınabilir) vs `reason: deleted` (geri alınamaz) ayrımı vardır. [S4]

### §5.3 CRDT mi?

- `crdt.tech`: CRDT'ler **strongly consistent replication** ve **optimistic
  replication** ailesindedir; **farklı yollarla** değiştirilen veriyi
  **çakışma çözümü olmadan otomatik** birleştirirler. [S10]
- Ink & Switch (local-first): CRDT'ler "çok-kullanıcılı ama aynı anda yerel
  ve özel" veri yapılarıdır; **dosya senkron ürünlerinin** (eşzamanlı
  düzenlemede) zayıf olduğu yerde temel teknoloji adayıdır. [S11]
- **Karar:** CoreMusic'te CRDT **kullanılacak mı?** → `UNKNOWN` (karar
  yok). Bu dosya yalnızca seçenek olduğunu kanıtla sınırlıdır.

### §5.4 Karşılaştırma tablosu (ne zaman hangisi)

| Senaryo | Önerilen | Kanıt |
|---|---|---|
| Aynı kayıt iki yerde değişti, biri kazanmalı | Optimistic + versiyon | [S2] |
| Liste/üye ekleme çakışması | Automerge (liste/küme birleşimi) | [S2] |
| İş kuralı çakışması (indirim, bakiye) | Custom (Lambda eşdeğeri) | [S2] |
| Eşzamanlı yerel düzenleme (local-first) | CRDT (araştırma düzeyi) | [S10] [S11] |
| Salt okunur kopya (read replica) | Çakışma yok; sıra koru | [S7] |

---

## §6 Eksen 5 — Idempotency (ikinci kez uygulanabilir mi?)

**İddia (Stripe, fetch ile doğrulandı):** `Idempotency-Key` ile güvenli
yeniden deneme yapılır; anahtar **istemci üreterek** sunucuya gönderir
(öneri: V4 UUID); anahtar **en fazla 255 karakter**; hassas veri
kullanılmaz; v1'de anahtarlar **en az 24 saat** sonra temizlenebilir;
`GET`/`DELETE` için anahtar gönderilmez (zaten idempotent). [S8]

**İddia (RFC 9110):** `PUT`/`DELETE` idempotent methodlardır; bir methodun
idempotent olması, aynı isteğin **birden fazla kez** yapılması durumunda
etkisinin **ilk kezle aynı** olması demektir — istemci bağlantı hatasında
yeniden deneyebilir. [S9]

**CoreMusic kuralı (tasarım iddiası):**
1. Her yazma isteğine istemci tarafında idempotency anahtarı üretilir (biçim
   → `UNKNOWN`; UUID v4 önerisi [S8]'den alınmıştır, CoreMusic kararı
   değildir).
2. Artımlı sync'te aynı delta kaydı **ikinci kez** uygulanabilir (replay
   kanıtı [S4]) → uygulama **aynı anahtarla** tekrar yazarsa ikinci kayıt
   açılmaz.
3. Depolama süresi (Stripe 24 saat [S8]) CoreMusic için →
   `⚠️ VERIFICATION REQUIRED` (ölçüm/ADR yok).

---

## §7 Eksen 6 — Sıralı Kuyruk

**İddia (Google Cloud Pub/Sub, fetch ile doğrulandı):**
- Sıra, **ordering key** ile korunur (ör. müşteri ID'si veya satırın birincil
  anahtarı; en fazla **1 KB**). [S6]
- Aynı ordering key ile yayınlanan mesajlar **sırayla** teslim edilir;
  **farklı** key'ler arasında sıra garantisi **yoktur**. [S6]
- Abonelikte `enableMessageOrdering` **açılmalıdır** ve oluşturulduktan
  sonra **değiştirilemez**. [S6]
- Teslimat **at-least-once**'tur; bir mesaj yeniden gönderilirse **sonraki
  tüm mesajlar** (onaylanmış olsalar bile) o key için yeniden gönderilir. [S6]
- Sıralı teslim, yayınlama kullanılabilirliğini düşürür ve uçtan uca gecikmeyi
  artırır; **hot key** (tek anahtarda çok yüksek hız) subscriber hızıyla
  sınırlıdır. [S6]

**İddia (Postgres):** Subscriber veriyi **publisher sırasıyla** uygular →
tek abonelik içinde transactional tutarlılık. [S7]

**CoreMusic kuralı (tasarım iddiası):**
- Aynı varlığın (ör. `entity_id`) değişiklikleri **aynı kuyruk
  anahtarıyla** sıralanır; anahtar başına paralellik **1** (sıra için).
- Sıra kırıldığında: versiyon (§5.2) ile en son durum kurtarılır.
- Anahtar seçimi ve kuyruk altyapısı → `UNKNOWN` (karar yok).

---

## §8 Eksen 7 — Checkpoint / Commit

**İddia (Spanner, fetch ile doğrulandı):** Checkpoint/commit kaydı
**verimli retry ve hata kurtarma** için API kullanıcısının sorumluluğundadır;
partition token ile okuma bölünür; **heartbeat** kaydı ilerleme göstergesi
olarak yazılır. [S5]

**İddia (AppSync):** İstemci **başarıyla yaptığı son sorgunun zamanını**
saklar (`startedAt`/`lastSync`) ve bir sonraki delta sorgusunda onu geçirir;
Amplify istemcileri bunu **diske kalıcı** yazar. [S1]

**İddia (Graph):** `@odata.nextLink` (sayfa) vs `@odata.deltaLink`
(bitiş/jeton) ayrımı vardır; bir sayfada **ikisi birden bulunamaz**;
jetonlar istemciye **opaktır** — içine bakılmaz, olduğu gibi geri verilir. [S4]

**Checkpoint kuralı (CoreMusic tasarım iddiası):**

| Kural | Gerekçe | Kanıt |
|---|---|---|
| Checkpoint **yalnız** başarılı apply sonrası yazılır | Yarı-yazma durumunda tekrar güvenli olsun | [S5] |
| Checkpoint kaydı **opak saklanır** (içindekine bakılmaz) | Jeton formatı sürüm değişse bile bozulmasın | [S4] |
| Retry'da checkpoint'ten **aynen** devam edilir | Bölünmüş okuma idempotent kalır | [S5] [S9] |
| Checkpoint kaybı → full sync'e düş | Delta güvenilirliği kanıtlanamaz | [S1] [S4] |

---

## §9 Eksen 8 — Rate Limit Uyumu

| Kural | Kanıt |
|---|---|
| `429` + `Retry-After` geldiğinde **o kadar saniye bekle**, sonra tekrar dene; hâlâ 429 ise aynı gecikmeyi uygula | [S13] |
| `Retry-After` yoksa **üstel geri çekilme (exponential backoff)** uygula | [S13] |
| Geri çekilmeye **rastgelelik (jitter)** ekle → thundering herd önlenir | [S14] |
| Sürekli polling yerine **change tracking (delta)** kullan → throttling olasılığı düşer | [S13] [S4] |
| GitHub: yetkili istek **5.000/saat**, yetkisiz **60/saat**; `x-ratelimit-*` başlıkları otoritedir; aşımda `403`/`429` | [S12] |
| GitHub: **eşzamanlı istek ≤ 100**; endpoint başına dakikada **900 puan** (GET=1, POST/PATCH/PUT/DELETE=5) | [S12] |
| Stripe: global limit canlı **100 istek/sn**, sandbox **25/sn**; endpoint başına **25/sn**; eşzamanlılık **50** | [S14] |
| Stripe: `429` + `Stripe-Rate-Limited-Reason` başlığı; `429` **başlıksız** ise rate limit değil (örn. `lock_timeout`) | [S14] |

**CoreMusic kuralı (tasarım iddiası):** Sync worker'ı `429` aldığında
**cursor'u ilerletmez**, `Retry-After` (varsa) veya jitter'lı exponential
backoff ile dener; limit karakteristiği dış sisteme göre →
`⚠️ VERIFICATION REQUIRED`.

---

## §10 Sınır Tablosu — K013 synchronization ↔ K007-data (şema)

> Kaynak: `integration-master.md` §2.2 (çizelge) — senkron **yazımı**
> şemaya girer, şemanın **sahibi** K007'dir.

| Konu | K013 (synchronization) | K007-data | Kanıt |
|---|---|---|---|
| Senkron stratejisi (full/delta, periyot, yön) | ✅ Bu dosya | ❌ | integration-master §2.2 |
| Cursor/jeton saklama yeri (tablo yapısı) | Tasarım iddiası (nerede?) | ✅ Şema/tablo tanımları | integration-master §2.2 |
| Çakışma stratejisi seçimi | ✅ §5 | ❌ | — |
| Şema/alan tanımı (hangi kolon, tip, indeks) | ❌ | ✅ | integration-master §2.2 |
| Idempotency anahtarının **depolanması** | Anahtar üretimi/kuralı ✅ | Depolama şeması ✅ | §6 + §2.2 |
| Rate limit stratejisi (backoff) | ✅ §9 | ❌ | — |
| Şema migrasyonu | ❌ | ✅ | §2.2 |

**Çelişki kuralı:** Bu dosya ile `.ai/.sql/**` veya K007 arasında çelişki
çıkarsa **şema kazanır** (SSOT: veri modeli K007) — senkron tarafı uyarlar,
şemayı **bu dosyadan** değiştirmez.

---

## §11 Sınır Tablosu — K013 ↔ K015 (medya katalog senkronu) — KARAR VERİLMEZ

> Bu bölüm **karar bölümü değildir.** `integration-master.md` §7/2'deki
> açık soru burada **iki ihtimal** olarak sunulur; hangisinin doğru olduğu
> **kanıtlanmamıştır**.

### §11.1 Kanıt durumu

| Gözlem | Sonuç | Tarih |
|---|---|---|
| `integration-master.md` §7/2: "synchronization sahipliği: K013 mü, K015 mi?" | Açık soru kayıtlı, cevaplanmamış | 2026-10-16 okuma |
| İlk glob `.ai/architecture/K015*/**/*.md` (gizli dizin, `hidden:false`) | **sahte "0 sonuç"** üretti — **geçersiz ölçüm** | 2026-10-16 (ilk tur) |
| Düzeltme: glob `K015-media/**/*.md` (`hidden:true`) | **15 md dosyası** — `K015-media/synchronization/index.md` dâhil | 2026-10-16 (düzeltme) |
| `K015-media/synchronization/index.md` → **Sınır (ne DEĞİL)** satırı | "Dış sistem eşitlemesi **K013/synchronization (aynı ad)** · ağ K014" | 2026-10-16 okuma |
| `K015-media/synchronization/index.md` → open_questions | "Aynı ad iki domain'de: ne ayırıyor → ⚠️ VERIFICATION REQUIRED (§7/3)" | 2026-10-16 okuma |
| `K013-integraion/synchronization/index.md` → **Sınır (ne DEĞİL)** satırı | "Kalıcı veri K007-data · **medya katalog senkronu K015 ile sınır belirsiz**" | 2026-10-16 okuma |
| Bu klasörün (K013/synchronization) varlığı | ✅ mevcut (index.md + 5 içerik dosyası) | 2026-10-16 |

**Ölçüm kuralı (düzeltme kaydı):** `.ai/` gizli (nokta) dizindir;
`hidden:false` ile yapılan glob **sahte 0 sonuç** üretir ve "yok" iddiasına
dönüşür — bu, **zero-hallucination ihlalidir**. Doğru ölçüm `hidden:true`
ile yapılır. İlk turdaki "K015 glob 0" satırı **iptal edilmiştir**; yerine
yukarıdaki disk kanıtı konmuştur.

**Yorum kuralı:** K015 düğümü artık **gözlemlenmiştir** (15 md). Her iki
index de **sınırlarını yazmıştır** ve ikisi birbirini **daraltır**: K015,
"dış sistem eşitlemesi K013'tür" der; K013 ise "medya katalog senkronu
K015 ile sınır belirsiz" der. **Karar yine verilmez** — daraltma bir
sahiplik ataması değildir → `⚠️ VERIFICATION REQUIRED`.

### §11.2 İki ihtimal

| | İhtimal A — Sahip K013 | İhtimal B — Sahip K015 |
|---|---|---|
| Özet | Tüm dış-sistem senkronu (veri + medya) K013-integraion/synchronization'da | Medya/katalog senkronu K015'e ait; K013 yalnızca "genel/dış-sistem" senkron |
| Lehte (kanıt) | Bu klasör `v2 §2.13` ile kayıtlı (index.md FM) · **K015 kendi index'inde sınırını K013'e bırakıyor**: "Dış sistem eşitlemesi K013/synchronization (aynı ad)" · integration-master §1/33 K013'ü "Eşitle" olarak tanımlar | K013 index'i kendi sınırında "medya katalog senkronu K015 ile **sınır belirsiz**" diyor → kapsam ayrımı henüz çizilmemiş · K015'in 11 alt düğümü (artwork/catalog/downloads/library/metadata/…) medya-kapsamı olarak ayrı duruyor |
| Aleyhte (kanıt) | K013 bu konuyu kendisi "belirsiz" ilan ediyor → tek başına sahipliği bağlamıyor | K015 kendi synchronization index'inde **kademe-3 kapsamının ADR-097 faz C'yi beklediğini** yazıyor → kapsam daha yazılmadı · "aynı ad iki domain'de: ne ayırıyor" sorusu **kendi open_questions'unda açık** |
| Sonuç | `⚠️ VERIFICATION REQUIRED` | `⚠️ VERIFICATION REQUIRED` |

### §11.3 Sınır tablosu (iki ihtimal için ortak çizgi)

| Konu | İhtimal A'da | İhtimal B'de |
|---|---|---|
| Full/delta stratejisi (§2) | K013 | K013 (paylaşılır) |
| Çakışma çözümü (§5) | K013 | K013 (paylaşılır) |
| Medya dosyası/katalog eşitleme akışı | K013 | **K015** |
| Medya şeması (tablo/alan) | K007 | K007 (değişmez) |
| **Dış-sistem eşitlemesi** (K015 index "Sınır" satırı) | **K013** (bırakıyor) | **K013** (K015 bunu zaten kendine ait saymıyor) |
| Kapsam dışı beyanı (index.md "Sınır") | Kalıcı veri K007 | Kalıcı veri K007 · medya K015'e bırakılmış |
| Kademe-3 kapsamı (K015 index "Durum" satırı) | K015 kapsamı henüz yazılmadı → çizgi geçici | K015 kapsamı yazılmadan ayrım yapılamaz |

> **Disk kanıtı (2026-10-16):** K015 `synchronization/index.md` satır 23:
> *"Dış sistem eşitlemesi K013/synchronization (aynı ad) · ağ K014."* ·
> K013 `synchronization/index.md` satır 23: *"Kalıcı veri K007-data · medya
> katalog senkronu K015 ile sınır belirsiz."* · Her iki dosyanın da
> open_questions'unda sahiplik **açık** olarak duruyor. Üç disk kanıtı birlikte
> **tek bir sonucu zorlamaz** → karar ADR'ye bırakılır.

> **Bu tablo iki ihtimali de bağlamaz; yalnız sınırı
> `⚠️ VERIFICATION REQUIRED` olarak görünür kılar. Karar mercii:
> ADR/üst karar (integration-master §7/2).**

---

## §12 Yasaklar (bu dosyanın yapmayacakları)

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | K013↔K015 sahibini **seçmek** | Şart: karar verilmez (§11) |
| 2 | Klasör adını "integraion" → "integration" **düzeltmek** | İmla dokunulmaz (kullanıcı kuralı) |
| 3 | Şema/kolon tanımı **yazmak** | K007-data'ya ait (§10) |
| 4 | Periyot/TTL/süre **uydurmak** (ör. "delta TTL 24 saat") | Ölçüm yok → `⚠️` |
| 5 | `git commit` **atmak** | Orkestratöre ait |
| 6 | CoreMusic'in push/pull/CRDT **seçtiğini söylemek** | Karar yok → `UNKNOWN` |

---

## §13 Red Listesi — Bu Dosyada Yazılmayan İddialar

| # | Yazılmayan iddia | Neden yazılmadı |
|---|---|---|
| 1 | "CoreMusic full sync'i X saatte bir yapar" | Periyot kararı/ölçümü **yok** → `UNKNOWN` |
| 2 | "Medya senkronu K015'tir" | Sahiplik açık soru → §11 `⚠️` |
| 3 | "K015-media vault'ta yoktur" | İlk glob **sahte 0** idi; düzeltmede K015 **gözlemlendi** (15 md) → "yok" iddiası hâlâ yazılmaz (§11.1 düzeltme kaydı) |
| 4 | "CRDT kullanılacaktır" | Karar yok → `UNKNOWN` (§5.3) |
| 5 | "Checksum karşılaştırma kullanılacaktır" | Yöntem **seçilmedi**; iki resmi kaynak yalnızca **seçenek olduğunu** kanıtlar (§4: [S15] [S18]) → seçim `UNKNOWN` |
| 6 | "MongoDB change streams ile eşitlenecek" | URL **404** → elendi (`sync-web-arastirma` §9) |
| 7 | "AWS DataSync kullanılacaktır" | URL **404** → elendi (`sync-web-arastirma` §9) |
| 8 | "Idempotency anahtarı 24 saat saklanır (CoreMusic)" | Stripe değeri Stripe'a aittir; CoreMusic için `⚠️` (§6) |

---

## §14 Sonraki Tur İş Kalemleri

| # | İş | Çıkacağı dosya | Öncelik |
|---|---|---|---|
| 1 | K013↔K015 sahiplik kararının ADR'ye bağlanması (§11.1 disk kanıtı hazır) | `integration-master.md` §7/2 | **P0** |
| 2 | ~~Karşılaştırma (checksum) yöntemine resmi kaynak çekimi~~ | ✅ kapatıldı 2026-10-16 ([S15] [S18] + `sync-web-arastirma` W15–W18) | — |
| 3 | CoreMusic senkron yönü (dış→iç / çift yönlü) kararının sorulması | bu dosya §3 | P1 |
| 4 | Periyot/TTL değerleri için ölçüm | `sync-dogrulama.md` | P2 |
| 5 | Reconciliation (satır karşılaştırma) + dry-run kapılarının CoreMusic senkronuna uyarlanması | `sync-dogrulama.md` §7A–§7C | P1 |

---

## §Kaynak Tablosu

| # | Kaynak | Kanıt verdiği iddia | Tarih | Durum |
|---|---|---|---|---|
| S1 | https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html | base/delta tablo, `lastSync`, `startedAt`, `_version`, `_deleted`, TTL, global catch-up | 2026-10-16 | ✅ fetch |
| S2 | https://docs.aws.amazon.com/appsync/latest/devguide/conflict-detection-and-sync.html | Optimistic / Automerge / Lambda çakışma stratejileri, versiyonlu veri kaynağı, tombstone | 2026-10-16 | ✅ fetch |
| S3 | https://docs.aws.amazon.com/dataSync/latest/userguide/what-is.html | — (aday) | 2026-10-16 | ❌ **404 → elendi** |
| S4 | https://learn.microsoft.com/en-us/graph/delta-query-overview | pull delta, nextLink/deltaLink, `@removed`, 410 Gone, 7 gün jeton, replay, push+pull hibrit | 2026-10-16 | ✅ fetch |
| S5 | https://docs.cloud.google.com/spanner/docs/change-streams | retention 1–30 gün, value capture, commit timestamp, partition token, heartbeat/checkpoint sorumluluğu | 2026-10-16 | ✅ fetch |
| S6 | https://cloud.google.com/pubsub/docs/ordering | ordering key, at-least-once, yeniden teslim zinciri, abonelik ayarı sabit, hot key | 2026-10-16 | ✅ fetch |
| S7 | https://www.postgresql.org/docs/current/logical-replication.html | publish/subscribe, snapshot → changes, sıra ile uygulama, transactional consistency | 2026-10-16 | ✅ fetch |
| S8 | https://docs.stripe.com/idempotency | Idempotency-Key, UUID v4 önerisi, 255 karakter, 24 saat, GET/DELETE hariç | 2026-10-16 | ✅ fetch |
| S9 | https://www.rfc-editor.org/rfc/rfc9110.html | §9.2.2 idempotent methods, §13 conditional requests, ETag/If-Match, Retry-After | 2026-10-16 | ✅ fetch |
| S10 | https://crdt.tech/ | strongly vs optimistic replication, otomatik birleştirme | 2026-10-16 | ✅ fetch |
| S11 | https://www.inkandswitch.com/essay/local-first/ | local-first ilkeleri, CRDT temel teknoloji, dosya-sync çakışma zayıflığı | 2026-10-16 | ✅ fetch |
| S12 | https://docs.github.com/en/rest/overview/rate-limits-for-the-rest-api | 5.000/saat, 60/saat, x-ratelimit-*, eşzamanlılık 100, puan tablosu | 2026-10-16 | ✅ fetch |
| S13 | https://learn.microsoft.com/en-us/graph/throttling | 429, Retry-After, polling yerine change tracking | 2026-10-16 | ✅ fetch |
| S14 | https://docs.stripe.com/rate-limits | 100/25 istek-sn, eşzamanlılık 50, jitter, `Stripe-Rate-Limited-Reason` | 2026-10-16 | ✅ fetch |
| S15 | https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html | Satır-bazlı karşılaştırma (kaynak↔hedef), ValidationPending/Suspended/Failed, ValidationState enum, kontrol tablosu `awsdms_control.awsdms_validation_failures_v1`, PK zorunluluğu, >10.000 hatada durma, validation-only görev | 2026-10-16 | ✅ fetch |
| S16 | https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ | Dry-run senkron (`SkipDryRunOnMissingResource` — CRD varsa dry-run yine çalışır), `Prune=confirm`/`Delete=confirm` onay kapıları, `ApplyOutOfSyncOnly`, `FailOnSharedResource`, `RespectIgnoreDifferences` | 2026-10-16 | ✅ fetch |
| S17 | https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html | Sayaçlar: Inserts/Deletes/Updates/Ddls · Applied* · FullLoadRows/ErrorRows · ValidationPending/Failed/SuspendedRecords · ValidationState · ResyncRows*/ResyncProgress · sayfalama (Marker) | 2026-10-16 | ✅ fetch |
| S18 | https://cloud.google.com/storage/docs/hashes-etags | CRC32C (önerilen) + MD5; MD5 parçalı yüklemede desteklenmez; yazmada uyuşmazlık → 400; gcloud cp/mv/rsync değişim tespiti: checksum → size+mtime → yalnız size | 2026-10-16 | ✅ fetch |

> **Kapsam beyanı:** Bu dosyada dayanak alınan **tek** URL kümesi bu
> tablodur. `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretli satırların
> **dayanağı yoktur** ve "kaynak alındı" diye kullanılamaz.

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | K013↔K015 sahipliği → iki ihtimal sunuldu, **karar verilmedi**; K015 disk kanıtı §11.1'e işlendi | ⚠️ VERIFICATION REQUIRED (integration-master §7/2) |
| 2 | CoreMusic senkron yönü (push/pull, çift yönlü) → `UNKNOWN` | FM open_questions |
| 3 | Periyot/TTL/süre değerleri → ölçüm yok | `⚠️` (§12/4) |
| 4 | Karşılaştırma (checksum) yöntemi kaynağı → ✅ çekildi ([S15] [S18]); **seçim** hâlâ `UNKNOWN` | §4 · §14/2 kapatıldı |
| 5 | Şema yüzeyi K007'ye devredildi — bu dosya şema **yazmaz** | §10 sınır tablosu |
| 6 | §11.1 "K015 glob 0" yanlış ölçümü düzeltildi (hidden:false tuzağı) | ✅ düzeltme kaydı (2026-10-16) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** integration-master §7/2 · v2 §2.13 · ADR-097 §5.5 · K015-media/synchronization/index.md (disk kanıtı) · AWS AppSync · AWS DMS (validation + CLI istatistikleri) · Argo CD · Microsoft Graph · Google Cloud (Spanner/Pub/Sub/Storage checksum) · PostgreSQL · Stripe · IETF RFC 9110 · crdt.tech · Ink & Switch · **Tarih: 2026-10-16**
