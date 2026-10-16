---
node_id: K013-integraion/synchronization/sync-dogrulama
node_type: technology-deepdive
title: "Sync Doğrulama — Kapılar, Test Senaryoları, Ölçüm, CI"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/synchronization
children: []
depends_on:
  - K013-integraion/synchronization/sync-sozlesme
  - K013-integraion/synchronization/sync-anayasa
  - K013-integraion/synchronization
evidence:
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://docs.stripe.com/idempotency (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/delta-query-overview (2026-10-16)"
  - "https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html (2026-10-16)"
  - "https://docs.cloud.google.com/spanner/docs/change-streams (2026-10-16)"
  - "https://cloud.google.com/pubsub/docs/ordering (2026-10-16)"
  - "https://www.postgresql.org/docs/current/logical-replication.html (2026-10-16)"
  - "https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html (2026-10-16)"
  - "https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html (2026-10-16)"
  - "https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ (2026-10-16)"
  - "https://cloud.google.com/storage/docs/hashes-etags (2026-10-16)"
validation:
  - "Her test kapısı §Kaynak Tablosu'ndaki fetch edilmiş URL ile eşleştirilmiştir (2026-10-16)"
  - "Test/CI altyapısının CoreMusic'te mevcudiyeti bu turda ÖLÇÜLMEDİ → durum satırları ⚠️ ile işaretlidir"
  - "Kabul kriteri sayıları uydurulmaz; eşik değerleri yalnız kaynakta okunan değerlerdir"
  - "K-7/K-8/K-9 kapıları (§7A–§7C) DMS validation · DMS CLI sayaçları · Argo CD dry-run · GCS checksum kaynaklarıyla 2026-10-16'da eklendi"
open_questions:
  - "CoreMusic senkron kodu var mı → ⚠️ VERIFICATION REQUIRED (bu turda tespit edilmedi)"
  - "Senkron testleri hangi test projesinde çalışacak → UNKNOWN"
  - "Eşik (ör. %100 idempotent apply) kabul kriteri onayı → ⚠️ VERIFICATION REQUIRED (ust onay bekliyor)"
group: kod
---

# Sync Doğrulama — Kapılar · Test Senaryoları · Ölçüm

> **Bu dosya ne değildir:** Test framework kurulum rehberi veya pipeline
> dosyası değildir. Bu dosya **senkronun nasıl kanıtlanacağını** tanımlar:
> her iddia için bir test senaryosu, her senaryo için bir gözlenebilir
> sonuç, her sonuç için bir kapı (GEÇTİ/KALDI/ATLANDI).
>
> **Durum etiketi:** TEST PLANI IMPLEMENTED (kaynak kanıtlı senaryolar) ·
> CoreMusic testleri ⚠️ PLANNED (senkron kodu bu turda tespit edilmedi).

---

## §0 ELI15 — Doğrulama neden ayrı dosya?

Senkron "çalışıyor" demek **iki şeyin birden** doğru olması demektir:
(1) veri kaybı yok (aynı kayıt iki kez silinmedi, kayıp olmadı), (2)
yeniden denemeler zarar vermedi (idempotency). Bunu "bir kez denedim,
gördü" ile **kanıtlayamazsın** → tekrarlanabilir kapılar gerekir. Bu dosya
o kapıları yazar. (Kaynak: §Kaynak Tablosu [V1]–[V11])

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `sync-anayasa.md` | Eksenler, sınır, sahiplik tartışması | Kademe-3 |
| mimari | `sync-sozlesme.md` | Token/hata/kavram sözleşmesi | Kademe-3 |
| **kod** | **`sync-dogrulama.md`** | **Doğrulama kapıları ve test senaryoları** | Kademe-3 |
| kod | `sync-kod-ornekleri.md` | 13 dilde artımlı senkron örneği | Kademe-3 |
| kanit | `sync-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Kapı Mimarisi — 9 Kapı

| # | Kapı | Ne ölçer | Çıkış |
|---|---|---|---|
| K-1 | **Sözleşme kapısı** | Token/alan adları `sync-sozlesme` §2–§4 ile birebir mi? | GEÇTİ / KALDI |
| K-2 | **Idempotency kapısı** | Aynı istek/anahtar ikinci kez → tek işlem mi? | GEÇTİ / KALDI |
| K-3 | **Sıra kapısı** | Aynı anahtarlı olaylar sırayla uygulandı mı? | GEÇTİ / KALDI |
| K-4 | **Checkpoint kapısı** | Hata sonrası retry doğru checkpoint'ten mi devam etti? | GEÇTİ / KALDI |
| K-5 | **Tombstone/silme kapısı** | Silme istemci cache'inden temizledi mi? TTL aşımında catch-up mı çalıştı? | GEÇTİ / KALDI |
| K-6 | **Rate-limit kapısı** | 429/Retry-After/backoff+jitter davranışı doğru mu? | GEÇTİ / KALDI |
| K-7 | **Reconciliation kapısı** | Kaynak↔hedef satır karşılaştırması + sayaç sayımı tutuyor mu? (`ValidationFailed` = 0?) | GEÇTİ / KALDI |
| K-8 | **Checksum/değişim kapısı** | Fark tespiti checksum ile yapılıyor mu; uyuşmazlıkta ne oluyor? | GEÇTİ / KALDI |
| K-9 | **Dry-run kapısı** | Yazmadan önce ön-eşitleme/doğrulama çalışıyor mu? | GEÇTİ / KALDI |

**Durum satırı (bu tur ölçümü):** CoreMusic'te bu kapıların
**çalıştırılabilir karşılığı** bu turda **tespit edilmedi** → tüm kapılar
`⚠️ PLANNED` (kod yok). Kapı **tanımları** geçerlidir; çalıştırma sonucu
**uydurulmaz**. K-7/K-8/K-9, 2026-10-16'da eklenen **kaynak-kanıtlı**
kapılardır (§7A–§7C).

---

## §2 K-1 — Sözleşme Kapısı

### §2.1 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-1.1 | Cursor'suz tam istek | Tüm kayıt döner + başlangıç jetonu (`startedAt` benzeri) | [V1] |
| K-1.2 | Cursor'lu artımlı istek | Yalnız değişiklik döner; `nextToken`/deltaLink ayrımı doğru | [V1] [V2] |
| K-1.3 | Aynı sayfada hem nextLink hem deltaLink | **Yasak** — ikisi birlikte gelmemeli | [V2] |
| K-1.4 | Opak token'a dokunma | Token geri verilir; içi parse edilip değiştirilmez | [V2] |
| K-1.5 | Sayfalama sonu | `rel="next"` yoksa döngü biter | [V3] |
| K-1.6 | `per_page` > maks | Sessiz kırpma (hata değil) bekleniyorsa davranış kaynakla eşleşmeli | [V3] |

### §2.2 Kabul kriteri

- Token adları kaynaktaki adlarla **eşleşiyorsa** GEÇTİ; CoreMusic'e özel
  adlandırma `UNKNOWN` olduğundan **eşleşme testi kaynağı esas alır**.
- K-1.6'da CoreMusic'in "hata döndürme" kararı varsa bu **bilinçli
  sapmadır** ve `sync-anayasa` §14'e iş kalemi olarak yazılır.

---

## §3 K-2 — Idempotency Kapısı (kritik)

### §3.1 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-2.1 | Aynı anahtarla aynı istek ×2 | **Tek** işlem; ikinci yanıt ilkinin kopyası (kod+gövde) | [V4] |
| K-2.2 | Aynı anahtar, **farklı** parametre | **Hata** (parametre uyuşmazlığı) | [V4] |
| K-2.3 | Aynı anahtar, ≥24 saat sonra (v1 davranışı) | Pruning sonrası **yeni** istek sayılabilir → davranış bilinçli seçilmeli | [V4] |
| K-2.4 | `GET`/`DELETE`'te anahtar gönderilmedi | Sorun yok (idempotent by definition) | [V4] |
| K-2.5 | Ağ hatası sonrası tekrar (idempotent method) | Etki ilk ile **aynı** | [V5] (RFC 9110 §9.2.2) |
| K-2.6 | Artımlı yanıtta aynı kayıt **iki kez** (replay) | Uygulama ikinci kezde **yeni kayıt açmaz** | [V2] |

### §3.2 Kabul kriteri

- K-2.1 ve K-2.6: **0 mükerrer kayıt** → GEÇTİ; ≥1 mükerrer → KALDI.
- K-2.2'nin CoreMusic'te nasıl davranacağı (hata mı?) →
  `⚠️ VERIFICATION REQUIRED` (tasarım kararı yok).

---

## §4 K-3 — Sıra Kapısı

### §4.1 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-3.1 | Aynı anahtar (ör. `entity_id`) ile 3 olay (create→update→delete) | Sırayla uygulanır; son durum = delete | [V6] [V7] |
| K-3.2 | Farklı anahtarlar paralel işlendi | Sıra garantisi **beklenmez** (kaynak: farklı key arası sıra yok) | [V6] |
| K-3.3 | Orta mesajın teslimi başarısız → yeniden | Sonraki mesajlar da yeniden gelir (at-least-once) → idempotent apply şart | [V6] |
| K-3.4 | Abonelik sıralama **kapalı** | Sıra bozulabilir → sistem davranışı bilinçli olmalı | [V6] |
| K-3.5 | Aynı anahtar çok yüksek hızda (hot key) | Gecikme/artış → izleme (`oldest_unacked_message_age` benzeri metrik) | [V6] |
| K-3.6 | Postgres benzeri snapshot+changes zinciri | Değişiklikler **yayıncı sırasıyla** uygulanır | [V7] |

### §4.2 Kabul kriteri

- K-3.1: sıra **%100** korunmalı (anahtar başına) → GEÇTİ/KALDI.
- K-3.3: yeniden teslim durumunda mükerrer uygulama **0** olmalı (K-2 ile
  birlikte ölçülür).

---

## §5 K-4 — Checkpoint Kapısı

### §5.1 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-5a.1 | Apply sırasında çökme (checkpoint yazılmadı) | Retry aynı batch'i **yeniden** uygular; veri kaybı yok, mükerrer yok (idempotency) | [V1] [V5] |
| K-5a.2 | Başarılı apply sonrası checkpoint | Bir sonraki istek **yeni** checkpoint'le başlar | [V1] |
| K-5a.3 | Checkpoint kaybı (dosya/DB silindi) | Sistem **full sync'e** düşer (delta'ya güvenmez) | [V1] [V2] |
| K-5a.4 | 410 Gone / `syncStateNotFound` | Full sync restart (cursor **fırlatılmaz**, yeniden alınır) | [V2] |
| K-5a.5 | Partition/bölünmüş okuma (Spanner benzeri) | Her partition kendi checkpoint'iyle retry edilir | [V1] (Spanner: partition token) |

### §5.2 Kabul kriteri

- K-5a.1: veri kaybı **0**, mükerrer **0** → GEÇTİ.
- K-5a.3: otomatik full-fallback **gözlemlenmeli**; "sessizce eski cursor'la
  devam" → KALDI (güvenli olmayan davranış).

---

## §6 K-5 — Tombstone / Silme Kapısı

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-5b.1 | İstemci çevrimdışıyken kayıt silindi | İstemci delta'da `_deleted`/`@removed` alır → yerel kopya **silinir** | [V1] [V2] |
| K-5b.2 | `reason: changed` vs `deleted` (Graph) | İkisi **ayrı** işlenir (ilki geri alınabilir) | [V2] |
| K-5b.3 | TTL aşıldı, tombstone kayboldu | Periyodik **base query (catch-up)** kaydı cache'ten temizler | [V1] |
| K-5b.4 | İlk delta'da eski silmeler görünmeyebilir | Sistem "ilk delta tam sayım" **iddiasında bulunmaz** | [V2] |

**Kabul kriteri:** K-5b.1 + K-5b.3'te ghost kayıt (cache'te kalan silinmiş
kayıt) = **0** → GEÇTİ.

---

## §7 K-6 — Rate-Limit Kapısı

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-6.1 | `429` + `Retry-After: 10` | Yaklaşık **10 sn** bekleme, sonra tek tekrar | [V6] |
| K-6.2 | `429`, `Retry-After` yok | Exponential backoff | [V6] |
| K-6.3 | Backoff | **Jitter** içerir (deterministik değil) | [V7] |
| K-6.4 | GitHub kaynaklı 403/429 + `x-ratelimit-remaining: 0` | `x-ratelimit-reset`'e kadar bekleme | [V6] |
| K-6.5 | Stripe 429 `Stripe-Rate-Limited-Reason` | Neden türüne göre aksiyon ayrımı | [V7] |
| K-6.6 | `lock_timeout` (429 ama rate limit değil) | Farklı kısa-retry davranışı | [V7] |
| K-6.7 | 429 sırasında | Cursor **ilerletilmez** | [V6] [V5] |
| K-6.8 | Sınamada limit aşımı | Retry sonrası **sonuç** yine de başarılı ve idempotent | [V5] |

**Kabul kriteri:** K-6.7 zorunlu (ilerletilen cursor = veri kaybı riski);
K-6.3'te jitter **yoksa** KALDI (thundering herd riski, kaynak [V7]).

---

## §7A K-7 — Reconciliation Kapısı (satır karşılaştırma + sayaç sayımı)

### §7A.1 Kaynak kanıtı

AWS DMS validation: doğrulama, **kaynaktaki her satırı hedefteki
karşılığıyla** karşılaştırır, aynı veriyi doğrular, uyuşmazlığı raporlar;
full load sonrası hemen başlar, CDC'de değişiklikler **oluşurken**
karşılaştırır. [V8] Sayaç/sayı sayımı `describe-table-statistics` ile
okunur: `Inserts`/`Deletes`/`Updates` ↔ `Applied*` ↔ `FullLoadRows` ↔
`Validation*Records`. [V9]

### §7A.2 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-7.1 | Full load sonrası reconciliation | Tüm satırlar `ValidationState = Validated` | [V8] |
| K-7.2 | Hedefte eksik satır | `FAILURE_TYPE = MISSING_TARGET` + `ValidationFailedRecords` artar | [V8] |
| K-7.3 | Kaynakta eksik satır | `FAILURE_TYPE = MISSING_SOURCE` | [V8] |
| K-7.4 | Aynı PK, farklı alan değeri | `FAILURE_TYPE = RECORD_DIFF`; `DETAILS` JSON'unda uyuşmayan kolonlar listelenir | [V8] |
| K-7.5 | Tabloda PK/unique index yok | `ValidationState = No primary key` → doğrulama **yapılamaz** (kapı **ATLANDI** olarak **değil**, **KALDI** sayılır çünkü kapsam kanıtlanamaz) | [V8] |
| K-7.6 | Sürekli değişen kaynak satır | `ValidationSuspendedRecords` artar → sistem "eşit" **iddiasında bulunamaz** | [V8] |
| K-7.7 | Başarısız/suspended kayıt **10.000'i** aşarsa | Doğrulama **durur** → kapı **KALDI**; altta yatan sorun çözülmeden "geçti" **denemez** | [V8] |
| K-7.8 | Olay sayaçları karşılaştırması | Kaynak `Inserts` = hedef `AppliedInserts` (ve Deletes/Updates için de) | [V9] |
| K-7.9 | Sayaç sayımı paginated | Tek çağrıda tüm tablolar gelmez → `Marker`/`NextToken` ile **tümü** toplanır; eksik sayım **KALDI** | [V9] |
| K-7.10 | Doğrulama sırasında hedefe 3. taraf yazımı | Farklar yanlış raporlanabilir → kapı sonucu **güvensiz** olarak işaretlenmeli | [V8] |

### §7A.3 Kabul kriteri

- **K-7.1:** `ValidationFailedRecords = 0` **ve** `ValidationPendingRecords = 0`
  → GEÇTİ; aksi KALDI (kaynak [V8] [V9] sayaçları).
- **K-7.8:** sayaç farkı **≠ 0** → KALDI (veri kaybı/uygulanmamış olay).
- **K-7.5:** `No primary key` → kapının **kapsam dışı** olduğu **yazılarak**
  bildirilir; sessizce atlanmaz.
- Uyuşmazlık kaydı nerede saklanıyor (DMS'te `awsdms_control.*` kontrol
  tablosu) → CoreMusic karşılığı `UNKNOWN` (şema K007'ye ait).

---

## §7B K-8 — Checksum / Değişim Tespiti Kapısı

### §7B.1 Kaynak kanıtı

GCS: CRC32C (önerilen) + MD5; parçalı yüklemede MD5 **desteklenmez**;
yazmada uyuşmazlık → **400**; `gcloud rsync` farkı **checksum → boyut+`mtime`
→ yalnız boyut** kademesiyle bulur; kopya sonrası otomatik doğrulama
uyuşmazlıkta **geçersiz kopyayı siler**. [V11]

### §7B.2 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-8.1 | Aynı içerik, farklı yol | Checksum eşleşir → transfer **atlanır** | [V11] |
| K-8.2 | Bozuk kopya (içerik farkı) | Checksum uyuşmaz → değişiklik **tespit edilir**; geçersiz kopya silinir/uyarı basılır | [V11] |
| K-8.3 | Checksum yok, `mtime` var | Boyut + `mtime` kademesine düşer (kademe 2) | [V11] |
| K-8.4 | Ne checksum ne `mtime` | Yalnız boyut kıyaslanır (kademe 3) → **aynı boyutta farklı içerik** **görünmez** → kapı bu kademede **düşük güven** raporlar | [V11] |
| K-8.5 | Parçalı yüklemede MD5 bekleniyor | MD5 **desteklenmez** → CRC32C eşdeğeri yoksa kapı `⚠️` | [V11] |
| K-8.6 | İndirme sonrası bütünlük | Sunucu checksum'ı ile istemci hesabı uyuşmaz → veri bozulmuş say, **yeniden dene** | [V11] |

### §7B.3 Kabul kriteri

- K-8.2: bozuk kopya **tespitsiz geçemez** → GEÇTİ.
- K-8.4: yalnız boyut kademesinde çalışan bir senkron, "eşitlendi" beyanını
  **güven notuyla** vermek zorunda (aksi KALDI).
- CoreMusic checksum algoritması → `UNKNOWN` (seçim yok).

---

## §7C K-9 — Dry-Run / Validation-Only Kapısı

### §7C.1 Kaynak kanıtı

- AWS DMS **validation only task**: "preview and validate data **without
  performing any migration or data replication**" — yazma yapmadan
  karşılaştırma (`EnableValidation` + `ValidationOnly` = true). [V8]
- Argo CD: sync **dry-run** ile çalışır; `SkipDryRunOnMissingResource=true`
  yalnızca **henüz bulunmayan** kaynak türü için dry-run'ı atlar; CRD zaten
  varsa **dry-run yine çalışır**. [V10]
- Argo CD yıkıcı adımlar için **manuel onay kapıları**: `Prune=confirm` ·
  `Delete=confirm` (onaylanana kadar işlem `Syncing` kalır). [V10]

### §7C.2 Senaryolar

| # | Senaryo | Beklenen gözlem | Kanıt |
|---|---|---|---|
| K-9.1 | Yazmadan önce ön-denklem | Dry-run çalışır, **hiçbir yazma** olmaz; rapor döner | [V8] [V10] |
| K-9.2 | Dry-run'da bilinmeyen tür | Hata → `SkipDryRunOnMissingResource=true` ile atlanabilir; **var olan** tür yine dry-run'dan geçer | [V10] |
| K-9.3 | Yıkıcı aksiyon (silme/prune) | Onay **zorunlu** (`Prune=confirm`/`Delete=confirm`) → onaysız yürütülmez | [V10] |
| K-9.4 | Sadece değişenler uygulanıyor | `ApplyOutOfSyncOnly=true` benzeri modda yalnız out-of-sync öğeler işlenir | [V10] |
| K-9.5 | Paylaşılan kaynak | `FailOnSharedResource=true` → senkron **düşer** (sessiz çakışma yasak) | [V10] |

### §7C.3 Kabul kriteri

- K-9.1: dry-run sonucunda **0 yazma** → GEÇTİ; tek bir bileşenin bile
  yazılması KALDI.
- K-9.3: onaysız yıkıcı işlem **gözlemlenirse** KALDI (geri alınamaz hasar).
- CoreMusic'te dry-run'ın karşılığı var mı → `⚠️ VERIFICATION REQUIRED`
  (kod tespit edilmedi).

---

## §8 Entegrasyon Senaryoları (uçtan uca)

| # | Senaryo | Kapılar | Kanıt |
|---|---|---|---|
| E-1 | İlk kurulum: full → 3 değişiklik → delta → doğrula | K-1, K-4 | [V1] |
| E-2 | Çevrimdışı 1 gün: dönüşte delta + tombstone | K-5 | [V1] [V2] |
| E-3 | Aynı anda 2 istemci aynı kaydı günceller | K-2 (+ çakışma) | [V4] |
| E-4 | Sıra altında 100 olay, 1'i red | K-3, K-2 | [V6] |
| E-5 | Limit'e takılan uzun çekim | K-6, K-4 | [V7] [V6] |
| E-6 | Cursor süresi doldu → otomatik full | K-4, K-1 | [V2] |
| E-7 | Journal TTL'si aşıldı → catch-up | K-5, K-4 | [V1] |
| E-8 | Push bildirimi + delta çekimi (hibrit) | K-1, K-6 | [V2] |
| E-9 | Sync bitti → reconciliation: satır karşılaştır + sayaç say | K-7, K-2 | [V8] [V9] |
| E-10 | Bozuk kopya tespiti → checksum ile yakala/yeniden dene | K-8, K-4 | [V11] |
| E-11 | Yazmadan önce dry-run + yıkıcı adımda onay | K-9 | [V8] [V10] |

---

## §9 Ölçüm Tablosu (ne sayılır?)

| Metrik | Tanım | Hedef | Kanıt |
|---|---|---|---|
| M1 Kayıp olay | Beklenen – uygulanan | **0** | [V1] [V7] |
| M2 Mükerrer apply | Aynı olay iki kez işlendi | **0** | [V4] [V2] |
| M3 Sıra ihlali | Anahtar başına sıra bozulması | **0** | [V6] |
| M4 Checkpoint uyumsuzluğu | Başarılı apply'de ilerlemeyen checkpoint | **0** | [V1] |
| M5 Ghost kayıt | Cache'te kalan silinmiş kayıt | **0** | [V1] [V2] |
| M6 429 kurtarma başarısı | Backoff sonrası başarılı istek oranı | ⚠️ eşik `UNKNOWN` | [V6] [V7] |
| M7 Full-fallback sıklığı | Otomatik full senkron tetiklenme | ⚠️ eşik `UNKNOWN` | [V2] |
| M8 Reconciliation başarısı | `ValidationFailed` + `ValidationPending` | **0** (kaynakta sayaç bu iki alan) | [V8] [V9] |
| M9 Sayaç farkı | Kaynak olay sayısı − hedefe uygulanan (`Inserts` − `AppliedInserts` vb.) | **0** | [V9] |
| M10 Checksum uyuşmazlığı | Doğrulamada uyuşmayan nesne/anahtar sayısı | **0** | [V11] |

> **Eşik kuralı:** M1–M5, M8–M10 hedefleri **sıfırdır** ve doğası gereği
> tartışmasızdır (sıfır-uyuşmazlık); M6/M7 eşikleri CoreMusic'te
> **ölçülmemiştir** → `⚠️ VERIFICATION REQUIRED` (uydurma sayı yazılmaz).

---

## §10 CI Kapısı — Nerede çalışır?

| Öğe | Durum | Kanıt |
|---|---|---|
| CoreMusic CI dosyaları `.github/workflows/` = 2 dosya (`ci.yml`, `secret-scan.yml`) | ✅ envanter (2026-10-27 ölçümü `.ai/AGENTS.md` §25.2) | vault envanteri |
| `ci.yml` içinde senkron testi var mı | ⚠️ VERIFICATION REQUIRED (bu turda okunmadı) | — |
| Senkron kodu (`sync` geçen sınıf/dosya) | ⚠️ VERIFICATION REQUIRED (bu turda tespit edilmedi) | — |
| Test projesi/seçici (filter) | `UNKNOWN` | — |

**Kapı kuralı (tasarım iddiası):** Senkron kodu ortaya çıktığında CI
zinciri: **K-1 → K-2 → K-3 → K-4 → K-5 → K-6 → K-7 → K-8 → K-9 →
E-1…E-11** sırasıyla çalışır; **K-2 veya K-3 başarısızsa** sonrakiler
**koşmaz** (yanlış sonuç üretmesin); **K-9 (dry-run)** her yıkıcı yazmadan
**önce** çalışır ve **K-7 (reconciliation)** senkron "bitti" beyanının
**ön koşuludur**. Bu sıralamanın mevcut `ci.yml`'e eklenmesi → iş kalemi
(§11/1).

---

## §11 İş Kalemleri (bu dosya için)

| # | İş | Öncelik |
|---|---|---|
| 1 | `ci.yml` içine senkron kapıları (K-1…K-9) eklenmesi — **onay + kod** gerektirir | P0 |
| 2 | Senkron kodunun depoda varlığının ölçülmesi (grep) | P0 |
| 3 | M6/M7 eşik değerlerinin üst kararla belirlenmesi | P1 |
| 4 | E-4 (100 olay) test verisi üretimi | P2 |
| 5 | Reconciliation sayaçlarının (M8/M9) CoreMusic metriklerine eşlenmesi — metrik adı `UNKNOWN` | P1 |
| 6 | Checksum kademesinin (K-8.3/K-8.4) CoreMusic'te hangi güven düzeyinde çalıştığının belirlenmesi | P1 |

---

## §12 Yasaklar

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | "Testler geçti" **demek** (çalıştırılmadan) | Kanıtsız başarı yasak — kapılar yalnız çalıştırılınca geçilir |
| 2 | M6/M7 için eşik **uydurmak** | Ölçüm yok → `⚠️` |
| 3 | `ci.yml`'i bu dosyadan **değiştirmek** | Doğrulama dosyası kod **yazmaz**; iş kalemi P0 |
| 4 | `git commit` **atmak** | Orkestratöre ait |

---

## §13 Red Listesi

| # | Yazılmayan iddia | Neden |
|---|---|---|
| 1 | "CoreMusic senkron testleri %100 geçiyor" | Test **çalıştırılmadı** → `⚠️` |
| 2 | "ci.yml senkron kapılarını içeriyor" | Dosya bu turda **okunmadı** → `⚠️` |
| 3 | "9 kapı da uygulandı" | Kod tespit **edilmedi** → PLANNED |
| 4 | "429 eşik değeri X/sn'dir" | CoreMusic ölçümü yok → `UNKNOWN` |
| 5 | "Reconciliation periyodu X dakikadır" | Periyot kararı yok → `UNKNOWN` (§7A) |
| 6 | "Dry-run CoreMusic'te mevcuttur" | Kod tespit edilmedi → `⚠️` (§7C) |

---

## §Kaynak Tablosu

| # | Kaynak | Kanıt verdiği iddia | Tarih | Durum |
|---|---|---|---|---|
| V1 | https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html | base/delta, `startedAt` checkpoint, TTL catch-up, `_deleted` cache temizliği | 2026-10-16 | ✅ fetch |
| V2 | https://learn.microsoft.com/en-us/graph/delta-query-overview | replay toleransı, 410 → full restart, jeton süresi, ilk delta'da eksik silmeler, gecikme | 2026-10-16 | ✅ fetch |
| V3 | https://docs.github.com/en/rest/using-the-rest-api/using-pagination-in-the-rest-api | `rel="next"` döngü sonu, `per_page` sessiz kırpma | 2026-10-16 | ✅ fetch |
| V4 | https://docs.stripe.com/idempotency | Aynı anahtar → aynı yanıt, parametre uyuşmazlığı hatası, 24 saat pruning, GET/DELETE hariç | 2026-10-16 | ✅ fetch |
| V5 | https://www.rfc-editor.org/rfc/rfc9110.html | İdempotent method (§9.2.2), koşullu istek (§13), 412, Retry-After (§10.2.3) | 2026-10-16 | ✅ fetch |
| V6 | https://cloud.google.com/pubsub/docs/ordering | Sıra anahtarı, at-least-once + yeniden teslim zinciri, kapalı abonelikte sıra yok, hot key metriği | 2026-10-16 | ✅ fetch |
| V7 | https://docs.stripe.com/rate-limits · https://docs.cloud.google.com/spanner/docs/change-streams · https://www.postgresql.org/docs/current/logical-replication.html | jitter+backoff, `Stripe-Rate-Limited-Reason`, `lock_timeout` · checkpoint/partition sorumluluğu · sıra ile apply | 2026-10-16 | ✅ fetch (**üç ayrı** URL) |
| V8 | https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html | Satır-bazlı karşılaştırma; `ValidationState` enum; `ValidationPending`/`ValidationSuspended`/`ValidationFailed`; kontrol tablosu `awsdms_control.awsdms_validation_failures_v1` (`RECORD_DIFF`/`MISSING_SOURCE`/`MISSING_TARGET`/`TABLE_WARNING`); PK önkoşulu; 10.000 hata durağı; validation-only task ("preview and validate … without performing any migration") | 2026-10-16 | ✅ fetch |
| V9 | https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html | `Inserts`/`Deletes`/`Updates`/`Ddls` · `Applied*` · `FullLoadRows`/`FullLoadErrorRows` · `Validation*Records` · `ValidationState` · `Resync*` · `TableState` · sayfalama (`Marker`/`NextToken`) | 2026-10-16 | ✅ fetch |
| V10 | https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ | Dry-run senkron; `SkipDryRunOnMissingResource` (CRD varsa dry-run yine çalışır); `Prune=confirm`/`Delete=confirm` manuel onay; `ApplyOutOfSyncOnly`; `FailOnSharedResource` | 2026-10-16 | ✅ fetch |
| V11 | https://cloud.google.com/storage/docs/hashes-etags | CRC32C (önerilen) + MD5; MD5 parçalı yüklemede desteklenmez; yazmada uyuşmazlık → 400; gcloud `rsync` kademeleri checksum → size+mtime → yalnız size; kopya sonrası otomatik doğrulama, uyuşmazlıkta geçersiz kopya silinir | 2026-10-16 | ✅ fetch |

> **Kapsam beyanı:** Dayanak kümesi yalnız bu tablodur. `⚠️`/`UNKNOWN`
> satırlarının dayanağı yoktur.

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | K-1…K-9 kapıları **tanımlı**, çalıştırma sonucu yok | ⚠️ PLANNED (kod tespit edilmedi) |
| 2 | `ci.yml` senkron testi içeriyor mu → okunmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | M6/M7 eşikleri → üst karar | `⚠️` |
| 4 | Senkron kodu var mı → glob/grep ölçümü yapılmadı | `⚠️` (iş kalemi P0) |
| 5 | Kapı önceliği: K-2/K-3 başarısızsa sonrası koşmaz; K-9 yazmadan önce; K-7 "bitti" ön koşulu | §10 tasarım iddiası |
| 6 | K-7 reconciliation kaynağı çekildi (DMS validation + CLI sayaçları) — CoreMetrics eşlemesi `UNKNOWN` | ✅ kaynak · `⚠️` eşleme (§7A) |
| 7 | K-8 checksum kaynağı çekildi (GCS) — algoritma seçimi `UNKNOWN` | ✅ kaynak · `⚠️` eşleme (§7B) |
| 8 | K-9 dry-run kaynağı çekildi (DMS validation-only + Argo CD) — CoreMusic karşılığı `⚠️` | ✅ kaynak · `⚠️` kod (§7C) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** AWS AppSync · AWS DMS (validation + CLI table statistics) · Argo CD (sync-options) · Microsoft Graph · GitHub REST Docs · Stripe (idempotency/rate-limits) · IETF RFC 9110 · Google Cloud (Pub/Sub/Spanner/Storage checksum) · PostgreSQL · **Tarih: 2026-10-16**
