---
node_id: K013-integraion/synchronization/sync-web-arastirma
node_type: technology-deepdive
title: "Sync Web Araştırması — ≥12 Benzersiz Resmi Kaynak · 404 Eleme · Çakışma Denetimi"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/synchronization
children: []
depends_on:
  - K013-integraion/synchronization
  - K013-integraion
evidence:
  - "18 benzersiz URL fetch ile doğrulandı (§7 — hepsi 2026-10-16; W1–W14 birinci tur, W15–W18 ikinci tur)"
  - "4 aday URL elendi (§9 — 404/boş yanıt/çekilemedi)"
  - "§8 çakışma denetimi: bu klasörde ilk ölçüm (2026-10-16)"
  - "Tur-2 genişlemesi 4 resmi kaynak: AWS DMS validation · AWS CLI describe-table-statistics · Argo CD sync-options · GCS hashes-etags (§11A–§12)"
validation:
  - "Her iddia §7 tablosunda `| İddia | URL | 2026-10-16 | Durum |` satırı ile taşır"
  - "404/eksik sonuçlanan adaylar §9'da ELLENİR ve elenme nedeni yazılır"
  - "§10 Red Listesi: bu turda çekilmeyen hiçbir iddia dayanak alınmamıştır"
  - "W15–W18'in dördü de 2026-10-16'da tekrar fetch edilerek içerik doğrulandı (§11A)"
open_questions:
  - "Checksum algoritması / reconciliation periyodu: kaynak ✅ çekildi, CoreMusic seçimi → UNKNOWN"
  - "Kafka resmi dokümanı (delivery semantics) → çekilemedi (redirect sayfası, §9)"
  - "MongoDB change streams → URL 404 (§9)"
group: kanit
---

# Sync Web Araştırması — Kaynak Doğrulama Raporu

> **Bu dosya ne değildir:** Bilgi özeti veya rehber değildir. Bu dosya
> **kanıt defteridir**: her satır 2026-10-16'da `webfetch` ile çekilmiş,
> **yanıt alınan** URL'leri taşır. Çekilemeyenler §9'da elenir ve nedeni
> yazılır. İddiası olmayan satır yoktur; dayanağı olmayan iddia yazılmaz.
>
> **Ölçüm özeti:** 22 aday URL → **18 doğrulandı** (✅ fetch) · **4 elendi**
> (§9 · 5 satır/4 neden ailesi) · hedef **≥12 benzersiz resmi kaynak →
> KARŞILANDI (18)**.

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `sync-anayasa.md` | Eksenler, sınır, sahiplik tartışması | Kademe-3 |
| mimari | `sync-sozlesme.md` | Token/hata/kavram sözleşmesi | Kademe-3 |
| kod | `sync-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `sync-kod-ornekleri.md` | 13 dilde artımlı senkron örneği | Kademe-3 |
| **kanit** | **`sync-web-arastirma.md`** | **≥12 benzersiz resmi kaynak** | Kademe-3 |

---

## §0 ELI15 — Bu dosyanın işi

Diğer 4 dosyadaki her önemli cümlenin **arkasında bir URL vardır**; bu
dosya o URL'lerin **çekilmiş olduğunu** ispatlar. Bir cümle burada yoksa,
dayanağı **yoktur** ve `⚠️ VERIFICATION REQUIRED` sayılır. Elenenler de
yazılır — "çekmedim" demek, "çekti ama olmadı" demekten farklıdır ve ikisi
de saklanmaz.

---

## §1 Yöntem

| Adım | İş | Kural |
|---|---|---|
| 1 | Aday toplama (websearch + bilinen resmi dokümanlar) | Yalnız **resmi** kaynak: üretici dokümanı, RFC, standart sitesi |
| 2 | Her aday **fetch** ile çekilir | `websearch` tek başına **kanıt sayılmaz** |
| 3 | Yanıt gövdesinde ilgili iddia **okunur** | Gövdede olmayan iddia yazılmaz |
| 4 | 2xx olmayan / boş / yönlendirme kabuğu dönen | §9'a elenir + neden |
| 5 | Kalan benzersiz URL sayısı ≥12 | ✅ (bu tur: 18) |

---

## §2 İddia Kümesi — Bu Turda Kanıtlanan Konular

| # | Konu | İlgili dosya |
|---|---|---|
| C1 | Full (base) vs incremental (delta) + cursor | `sync-anayasa` §2 |
| C2 | Push vs pull (delta query vs change notification) | `sync-anayasa` §3 |
| C3 | Çakışma çözümü stratejileri (optimistic/automerge/custom) | `sync-anayasa` §5 |
| C4 | Idempotency anahtarı kuralları | `sync-anayasa` §6 · `sync-sozlesme` §7 |
| C5 | Sıralı kuyruk (ordering key, at-least-once) | `sync-anayasa` §7 |
| C6 | Checkpoint/partition token sorumluluğu | `sync-anayasa` §8 |
| C7 | Rate limit + Retry-After + jitter | `sync-anayasa` §9 · `sync-sozlesme` §8-§9 |
| C8 | Tombstone/silme (`_deleted`, `@removed`) | `sync-sozlesme` §5 |
| C9 | Sayfalama (`link` başlığı, `nextToken`) | `sync-sozlesme` §3 |
| C10 | Koşullu istek/ETag (RFC 9110 §13) | `sync-sozlesme` §6 |
| C11 | CRDT / local-first (araştırma düzeyi seçenek) | `sync-anayasa` §5.3 |
| C12 | Logical replication: snapshot + changes zinciri | `sync-anayasa` §4 |
| C13 | Reconciliation: satır-bazlı karşılaştırma + hata tablosu | `sync-anayasa` §4 · `sync-sozlesme` §9A · `sync-dogrulama` §7A |
| C14 | Sayaçla sayı sayımı (Inserts↔Applied*, Validation*) | `sync-sozlesme` §9A.2 · `sync-dogrulama` §7A.2 |
| C15 | Checksum / değişim tespiti kademeleri | `sync-anayasa` §4 · `sync-sozlesme` §7A · `sync-dogrulama` §7B |
| C16 | Dry-run / validation-only (yazmadan doğrulama) | `sync-sozlesme` §9A.3 · `sync-dogrulama` §7C |

---

## §3 Ana Doğrulama Tablosu (18 doğrulanmış kaynak)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| W1 | Base query (tam) vs delta query (artımlı); `lastSync`/`startedAt` cursor; `_version`/`_deleted`/`_ttl`; global catch-up | https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html | 2026-10-16 | ✅ fetch |
| W2 | Çakışma stratejileri: Optimistic Concurrency / Automerge / Lambda; versiyonlu veri kaynağı; tombstone | https://docs.aws.amazon.com/appsync/latest/devguide/conflict-detection-and-sync.html | 2026-10-16 | ✅ fetch |
| W3 | Delta query = pull; change notification = push; `@odata.nextLink`/`@odata.deltaLink`; `@removed` nedenleri; 410 Gone; 7 gün jeton; replay; gecikme; push+pull hibrit | https://learn.microsoft.com/en-us/graph/delta-query-overview | 2026-10-16 | ✅ fetch |
| W4 | Change streams: retention 1–30 gün; value capture tipleri; commit timestamp + transaction ID; partition token; **checkpoint sorumluluğu API kullanıcısında**; heartbeat | https://docs.cloud.google.com/spanner/docs/change-streams | 2026-10-16 | ✅ fetch |
| W5 | Ordering key (≤1 KB); aynı key sıralı, farklı key sıra yok; abonelikte açılır ve **sonradan değiştirilemez**; at-least-once + yeniden teslim zinciri; hot key | https://cloud.google.com/pubsub/docs/ordering | 2026-10-16 | ✅ fetch |
| W6 | Publish/subscribe; snapshot → changes; subscriber **publisher sırasıyla** uygular; transactional consistency | https://www.postgresql.org/docs/current/logical-replication.html | 2026-10-16 | ✅ fetch |
| W7 | `Idempotency-Key`: istemci üretir (UUID v4 önerisi), ≤255 karakter, hassas veri yasak, ≥24 saat pruning, GET/DELETE hariç | https://docs.stripe.com/idempotency | 2026-10-16 | ✅ fetch |
| W8 | §9.2.2 idempotent methods → güvenli retry; §13 koşullu istekler; §8.8.3 ETag; 412; 304; §10.2.3 Retry-After | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ fetch |
| W9 | Rate limit: `429` + `Stripe-Rate-Limited-Reason`; global 100/sn (canlı) / 25/sn (sandbox); endpoint 25/sn; eşzamanlılık 50; jitter'lı backoff; `lock_timeout` ayrımı | https://docs.stripe.com/rate-limits | 2026-10-16 | ✅ fetch |
| W10 | Yetkili 5.000/saat, yetkisiz 60/saat; `x-ratelimit-*` otoriter; 403/429; `retry-after` yoksa ≥1 dakika; ≤100 eşzamanlı; GET=1 / yazma=5 puan | https://docs.github.com/en/rest/overview/rate-limits-for-the-rest-api | 2026-10-16 | ✅ fetch |
| W11 | `link` başlığı ile sayfalama; `rel="next"`; `per_page` maks 100 ve **sessiz kırpma**; `octokit.paginate` | https://docs.github.com/en/rest/using-the-rest-api/using-pagination-in-the-rest-api | 2026-10-16 | ✅ fetch |
| W12 | Throttling: `429` + `Retry-After: 10` örneği; header yoksa exponential backoff; **polling yerine change tracking** önerisi; batching'te tekil 429 | https://learn.microsoft.com/en-us/graph/throttling | 2026-10-16 | ✅ fetch |
| W13 | CRDT: strongly consistent vs optimistic replication; farklı yollarla değişimi **otomatik birleştirme** | https://crdt.tech/ | 2026-10-16 | ✅ fetch |
| W14 | Local-first 7 ilke; dosya-sync ürünlerinin eşzamanlı düzenlemede zayıflığı; CRDT temel teknoloji adayı; sunucu-öncelikli vs yerel-öncelikli | https://www.inkandswitch.com/essay/local-first/ | 2026-10-16 | ✅ fetch |
| W15 | Reconciliation: kaynak satırı ↔ hedef satır karşılaştırması; `ValidationState` enum (`Validated`/`Mismatched records`/`No primary key`/…); `ValidationPending`/`ValidationSuspended`/`ValidationFailed`; kontrol tablosu `awsdms_control.awsdms_validation_failures_v1` (`RECORD_DIFF`/`MISSING_SOURCE`/`MISSING_TARGET`/`TABLE_WARNING`); PK/unique index önkoşulu; sürekli değişen satır → suspended; **10.000** aşımında doğrulama durması; **validation-only task** = "preview and validate data without performing any migration or data replication" | https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html | 2026-10-16 | ✅ fetch (tur-2) |
| W16 | Dry-run senkron; `SkipDryRunOnMissingResource=true` yalnız bulunmayan tür için dry-run'ı atlar (**CRD varsa dry-run yine çalışır**); `Prune=confirm`/`Delete=confirm` manuel onay kapıları; `ApplyOutOfSyncOnly`; `FailOnSharedResource`; `RespectIgnoreDifferences` | https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ | 2026-10-16 | ✅ fetch (tur-2) |
| W17 | Sayaçlar: `Inserts`/`Deletes`/`Updates`/`Ddls` · `Applied*` · `FullLoadRows`/`FullLoadErrorRows` · `ValidationPendingRecords`/`ValidationFailedRecords`/`ValidationSuspendedRecords` · `ValidationState`(+`Details`) · `ResyncRowsAttempted/Succeeded/Failed` + `ResyncProgress` · `TableState` · işlem **paginate** (`Marker`/`NextToken`) | https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html | 2026-10-16 | ✅ fetch (tur-2) |
| W18 | Checksum: **CRC32C** (önerilen) + MD5; MD5 parçalı yüklemede **desteklenmez**; yazmada uyuşmazlık → **400**; kopya/rewrite'ta sunucu checksum doğrulaması; gcloud `cp`/`mv`/`rsync` farkı checksum ile bulur; **rsync kademeleri**: checksum → boyut+`mtime` → yalnız boyut; uyuşmazlıkta geçersiz kopya silinir | https://cloud.google.com/storage/docs/hashes-etags | 2026-10-16 | ✅ fetch (tur-2) |

**Benzersiz kaynak sayımı:** W1–W18 = **18 benzersiz URL** (hepsi resmi:
AWS · Microsoft · Google Cloud · PostgreSQL · Stripe · IETF · GitHub ·
Argo CD · crdt.tech · Ink & Switch) → **≥12 hedefi KARŞILANDI (18/12)**.

---

## §4 İddia → Kaynak Eşlemesi (dosyalar arası)

| İddia grubu | Birincil kaynak | Kullanan dosya |
|---|---|---|
| Full/delta/cursor | W1, W3 | anayasa §2 · sözleşme §3 |
| Push/pull | W3, W12 | anayasa §3 |
| Çakışma stratejisi | W2, W13, W14 | anayasa §5 |
| Idempotency | W7, W8 | anayasa §6 · sözleşme §7 · doğrulama §3 · kod §16 |
| Sıralı kuyruk | W5, W6 | anayasa §7 · doğrulama §4 |
| Checkpoint | W1, W4, W3 | anayasa §8 · doğrulama §5 |
| Rate limit | W9, W10, W12 | anayasa §9 · sözleşme §8-§9 · doğrulama §7 |
| Tombstone | W1, W3 | sözleşme §5 · doğrulama §6 |
| Sayfalama | W1, W3, W11 | sözleşme §3-§4 |
| Reconciliation (satır karşılaştırma) | W15, W17 | anayasa §4 · sözleşme §9A · doğrulama §7A |
| Sayaç/sayı sayımı | W17 | sözleşme §9A.2 · doğrulama §7A.2 (M8/M9) |
| Checksum / değişim tespiti | W18 | anayasa §4 · sözleşme §7A · doğrulama §7B |
| Dry-run / validation-only | W15, W16 | sözleşme §9A.3 · doğrulama §7C |

> **Tek-kaynak iddialar** (yalnız bir URL'ye dayanan): 410 → full (W3) ·
> 7 gün jeton süresi (W3) · `per_page` sessiz kırpma (W11) · `lock_timeout`
> (W9) · checksum kademeleri (W18 — DMS ile çapraz **değil**, farklı ürün
> ailesi). Bunlar **tek kaynağa bağımlıdır** ve ikinci kaynakla
> **çaprazlanmamıştır** → kullanım öncesi `⚠️` olarak işaretlenir.
> (W15 ↔ W17 aynı ürün ailesinde **tamamlayıcıdır**; bağımsız çapraz
> sayılmaz — bkz. §6.)

---

## §5 Websearch Kayıtları (ön araştırma — kanıt DEĞİL)

| # | Sorgu | Kullanılan yer | Not |
|---|---|---|---|
| S-1 | AWS AppSync Delta Sync | W1/W2 adaylarını buldu | Kanıt W1/W2 fetch'tir |
| S-2 | Microsoft Graph delta query | W3 adayını buldu | Kanıt W3 fetch'tir |
| S-3 | Google Cloud Spanner change streams | W4 adayını buldu | Kanıt W4 fetch'tir |
| S-4 | CRDT + Ink & Switch local-first | W13/W14 adaylarını buldu | Kanıt W13/W14 fetch'tir |
| S-5 | AWS DMS validation / table comparison | W15 adayını buldu (tur-2) | Kanıt W15 fetch'tir |
| S-6 | Argo CD sync options dry-run | W16 adayını buldu (tur-2) | Kanıt W16 fetch'tir |
| S-7 | GCS checksums etags / rsync comparison | W18 adayını buldu (tur-2) | Kanıt W18 fetch'tir |

> **Kural:** websearch sonuçları **tek başına dayanak sayılmaz**; her iddia
> §3'teki fetch satırıyla eşlenmek zorundadır. W17 (AWS CLI
> `describe-table-statistics`) **websearch ile değil**, W15 sayfasındaki
> AWS doc ağacından **doğrudan bilinen URL** olarak çekildi — "adres çubuğu
> tahmini" değil, doküman içi referanstır.

---

## §6 Çapraz Doğrulama (ikinci kaynakla teyit)

| İddia | Birincil | Çapraz kaynak | Sonuç |
|---|---|---|---|
| 429 + Retry-After davranışı | W12 (Graph) | W9 (Stripe), W10 (GitHub) | ✅ 3 bağımsız kaynak aynı davranışı yazıyor |
| Exponential backoff | W12 | W9 (jitter ile) | ✅ uyumlu (jitter W9'da açık) |
| Idempotent retry güvenliği | W8 (RFC) | W7 (Stripe uygulaması) | ✅ standart + uygulama |
| Full → delta akışı | W1 (AppSync) | W3 (Graph), W6 (Postgres snapshot+changes) | ✅ üç ayrı mimari aynı kalıp |
| Sıra + tekrar işleme | W5 | W6 (sıralı apply) | ✅ uyumlu |
| Silme bildirimi | W1 (`_deleted`) | W3 (`@removed`) | ✅ iki biçim, aynı amaç |
| **Yazmadan doğrulama (dry-run)** | W16 (Argo CD dry-run) | W15 (DMS validation-only: "without performing any migration") | ✅ **iki bağımsız ürün** aynı kalıbı yazıyor |
| Doğrulama sayaçları | W15 (`ValidationFailed` vb.) | W17 (`Validation*Records` CLI alanları) | ✅ aynı ürünün konsol+CLI yüzeyi **birbirini doğrular** (tamamlayıcı, bağımsız değil) |
| Checksum → mükerrer aktarım engelleme | W18 (gcloud `rsync`) | — | ⚠️ **çaprazlanmadı** (tek kaynak — §4 notu) |

---

## §7 Çakışma Denetimi (bu klasör — ilk ölçüm, 2026-10-16)

| # | Denetim | Sonuç |
|---|---|---|
| C-1 | Bu klasörde (6 dosya) kaç benzersiz `https://` kullanılıyor (§3 + §17 tabloları)? | **18** (W1–W18 = K1–K9/S1–S18 kümesi ile **aynı 18 URL**) |
| C-2 | Aynı URL farklı dosyada mı kullanılıyor? | **Evet — kasıtlı.** Örn. W1 (`tutorial-delta-sync`) 5 dosyada dayanaktır. Bu **yanlış pozitif değildir**: tek kaynak, çok iddia taşır. |
| C-3 | Çelişen iddia (iki dosya aynı konuda ters cümle)? | **0** — tüm dosyalar `sync-anayasa` §2-§9 eksenlerine bağlıdır |
| C-4 | Kaynak-tablosu-sınırlı ölçüm (metin içi geçişler hariç)? | anayasa 18 · sözleşme 11 · doğrulama 11 · kod 7 → **toplam 47 satır / 18 benzersiz URL** |
| C-5 | `index.md` `evidence:` alanı URL içeriyor mu? | Hayır — dosya adı içerir (openapi kalıbıyla aynı) → geniş tarama **yanlış pozitif** üretmez |

> **Ölçüm kuralı (openapi kalıbı §19.1 ile aynı):** Çakışma ölçümü
> **yalnızca kaynak tablolarından** yapılır; `evidence:` alanları ve metin
> içi geçişler sayım dışındadır. Çelişki çıkarsa önce **hangi dosyanın
> hangi satırı** yazılır, sonra düzeltme notu düşülür — sessizce
> değiştirilmez.

---

## §8 Eleme Denetimi — URL Çekim Sonuçları (22 aday)

| # | Aday URL | Sonuç | Karar |
|---|---|---|---|
| 1 | https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html | 200 + içerik | ✅ §3/W1 |
| 2 | https://docs.aws.amazon.com/appsync/latest/devguide/conflict-detection-and-sync.html | 200 + içerik | ✅ §3/W2 |
| 3 | https://learn.microsoft.com/en-us/graph/delta-query-overview | 200 + içerik | ✅ §3/W3 |
| 4 | https://docs.cloud.google.com/spanner/docs/change-streams | 200 + içerik | ✅ §3/W4 |
| 5 | https://cloud.google.com/pubsub/docs/ordering | 200 + içerik (last updated 2026-10-13) | ✅ §3/W5 |
| 6 | https://www.postgresql.org/docs/current/logical-replication.html | 200 + içerik | ✅ §3/W6 |
| 7 | https://docs.stripe.com/idempotency | 200 + içerik | ✅ §3/W7 |
| 8 | https://www.rfc-editor.org/rfc/rfc9110.html | 200 + içerik (RFC 9110, June 2022) | ✅ §3/W8 |
| 9 | https://docs.stripe.com/rate-limits | 200 + içerik | ✅ §3/W9 |
| 10 | https://docs.github.com/en/rest/overview/rate-limits-for-the-rest-api | 200 + içerik | ✅ §3/W10 |
| 11 | https://docs.github.com/en/rest/using-the-rest-api/using-pagination-in-the-rest-api | 200 + içerik | ✅ §3/W11 |
| 12 | https://learn.microsoft.com/en-us/graph/throttling | 200 + içerik | ✅ §3/W12 |
| 13 | https://crdt.tech/ | 200 + içerik | ✅ §3/W13 |
| 14 | https://www.inkandswitch.com/essay/local-first/ | 200 + içerik (2019, Onward!) | ✅ §3/W14 |
| 15 | https://docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html | 200 + içerik (tur-2) | ✅ §3/W15 |
| 16 | https://argo-cd.readthedocs.io/en/latest/user-guide/sync-options/ | 200 + içerik (tur-2) | ✅ §3/W16 |
| 17 | https://docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html | 200 + içerik (tur-2) | ✅ §3/W17 |
| 18 | https://cloud.google.com/storage/docs/hashes-etags | 200 + içerik (tur-2) | ✅ §3/W18 |

---

## §9 404 / Elenen Adaylar (4)

| # | Aday URL | HTTP/Sonuç | ELENME NEDENİ |
|---|---|---|---|
| E1 | https://docs.aws.amazon.com/dataSync/latest/userguide/what-is.html | **404** | Büyük harfli `dataSync` segmenti geçersiz (AWS doc segmenti küçüktür) — **kaynak adı yanlış yazılmış**, içerik çekilmedi → iddia üretilemedi |
| E2 | https://docs.aws.amazon.com/datasync/latest/userguide/what-is.html | **Boş yanıt** (gövde dönmedi) | Yönlendirme/boş gövde → içerik okunamadı; "çekildi" **denmez** → elendi |
| E3 | https://www.mongodb.com/docs/manual/change-streams/ | **404** | Uçluksuz varyant da denendi (aşağıda) — iki varyant da 404 |
| E4 | https://www.mongodb.com/docs/manual/change-streams | **404** | Doğru yola görelenmiş sayfa bu turda erişilemedi → "MongoDB change streams şöyledir" **iddiası YAZILMADI** |
| E5 | https://kafka.apache.org/documentation/#maxinflight | **Yönlendirme kabuğu** | Gövde yalnızca "Redirecting..." + navigasyon döndürdü; konfigürasyon metni okunamadı → "Kafka max.in.flight ayarı şudur" **iddiası YAZILMADI** |

> **Eleme kuralı:** Elenen URL'den **hiçbir iddia türetilmez**; konu
> gerekiyorsa `⚠️ VERIFICATION REQUIRED` bırakılır (örn. §10/2-3).
> AWS DataSync adayı ayrıca **yanlış ürün adayıdır** (AWS DataSync, dosya
> taşıma servisidir — bu dosyada konuyla ilişkilendirilmedi ve dayanak
> alınmadı).

---

## §10 Red Listesi — Bu Turda Çekilmeyen / Yazılmayan İddialar

| # | Yazılmayan iddia | Neden yazılmadı |
|---|---|---|
| 1 | "Kafka'da sıralı teslim `max.in.flight.requests.per.connection=X` ile sağlanır" | URL **çekilemedi** (E5) → `⚠️ VERIFICATION REQUIRED` |
| 2 | "MongoDB change streams şu şekilde çalışır" | URL **404** (E3/E4) → `⚠️ VERIFICATION REQUIRED` |
| 3 | "AWS DataSync ile senkron yapılır" | URL **404/boş** (E1/E2) + konu dışı → dayanak yok |
| 4 | "Checksum karşılaştırma yöntemi **CoreMusic'te** şudur" | Resmi kaynak ✅ çekildi (W18 — CRC32C önenilen, MD5 parçalı yüklemede yasak); **ürün seçimi ve CoreMusic eşlemesi yok** → seçim `UNKNOWN` (kaynak ≠ karar) |
| 5 | "CoreMusic'in senkron yönü/periodu şudur" | Depoda karar/ölçüm **yok** → `UNKNOWN` |
| 6 | "Synchronization sahibi K013'tür (veya K015'tir)" | Açık soru → `sync-anayasa` §11'de iki ihtimal + `⚠️` |
| 7 | "S3/Kafka/Debezium vb. ürünler CoreMusic'te kullanılacak" | Ürün seçimi **yok** → `UNKNOWN` |
| 8 | "CRDT kütüphanesi X kullanılacak" | Karar yok → yalnız W13/W14 seçenek düzeyinde |

---

## §11 Sonraki Tur İş Kalemleri

| # | İş | Öncelik |
|---|---|---|
| ~~1~~ | ~~Checksum/karşılaştırma yöntemine resmi kaynak çekimi~~ | ✅ **KAPANDI** (W18 — tur-2; kalan: CoreMusic seçimi → §10/4 `UNKNOWN`) |
| 2 | Kafka delivery semantics sayfasının doğru URL ile çekilmesi (§10/1) | P2 |
| 3 | MongoDB change streams doğru yolu (§10/2) | P2 |
| 4 | Tek-kaynaklı iddiaların (§4 notu) çapraz kaynağı | P2 |
| 5 | K013↔K015 sahiplik kararının ADR'ye bağlanması | **P0** |
| 6 | Reconciliation periyodu / senkron yönü CoreMusic kararının yazılması (W15-W18 kanıtı hazır, karar yok) | **P0** |

---

## §11A Tur-2 Fetch Kayıt Defteri (2026-10-16)

> Tur-2, 4 konu boşluğu için yapıldı: reconciliation (satır karşılaştırma),
> sayaçlar, checksum, dry-run. Her URL **aynı gün tekrar fetch** edilerek
> gövde okundu; §3 satırları bu defterin özetidir.

| # | URL | Çekim nedeni | Gövdede OKUNAN (kanıt) |
|---|---|---|---|
| W15 | `docs.aws.amazon.com/dms/latest/userguide/CHAP_Validating.html` | Reconciliation + validation-only kanıtı | `ValidationState` enum · `awsdms_control.awsdms_validation_failures_v1` kontrol tablosu · `RECORD_DIFF`/`MISSING_SOURCE`/`MISSING_TARGET` hata kodları · PK/unique index önkoşulu · 10.000 hata durağı · "preview and validate data without performing any migration" cümlesi |
| W16 | `argo-cd.readthedocs.io/en/latest/user-guide/sync-options/` | Dry-run senkron kanıtı (DMS dışı bağımsız ürün) | `SkipDryRunOnMissingResource` davranış tanımı · `Prune=confirm`/`Delete=confirm` · `ApplyOutOfSyncOnly` · `FailOnSharedResource` · `RespectIgnoreDifferences` |
| W17 | `docs.aws.amazon.com/cli/latest/reference/dms/describe-table-statistics.html` | Sayaç ↔ CLI alanı eşlemesi | `Inserts`/`Deletes`/`Updates`/`Ddls` · `Applied*` · `FullLoadRows` · `Validation*Records` · `Resync*` · `TableState` · `Marker`/`NextToken` sayfalama |
| W18 | `cloud.google.com/storage/docs/hashes-etags` | Checksum kademeleri kanıtı | CRC32C (önerilen) + MD5 · MD5 parçalı yüklemede desteklenmez · uyuşmazlıkta 400 · rsync kademeleri: checksum → boyut+mtime → yalnız boyut · geçersiz kopya silinir |

**Defter kuralı:** bu tablodaki hiçbir cümle §3 satırından **fazlasını**
iddia etmez; §3'te olmayan hiçbir gövde cümlesi dosyalara **taşınmaz**.

---

## §12 Tur-2 İddia Detayları (W15–W18 — hangi cümle hangi kapıdan geçti)

> Her madde: (a) gövdede okunan kanıt · (b) hangi dosyaya taşındı ·
> (c) **kullanım sınırı** (iddia aşımı yasağı). Kanıt (a) olmadan (b)
> yazılmadı; (c) olmayan madde kapatılmış sayılmaz.

### §12.1 W15 — AWS DMS validation (reconciliation)

- **Kanıt:** doğrulama, hedef satırı kaynak satırıyla karşılaştırır;
  sonuç `ValidationState` enum'una yazılır (`Validated`, `Mismatched
  records`, `No primary key`, …). Detaylar kontrol tablosu
  `awsdms_control.awsdms_validation_failures_v1` içine dökülür —
  kodlar: `RECORD_DIFF` / `MISSING_SOURCE` / `MISSING_TARGET` /
  `TABLE_WARNING`. Doğrulama için PK veya unique index **ön koşuldur**.
  Sürekli değişen satır → `ValidationSuspended`. Hata sayısı **10.000**'i
  aşarsa doğrulama durur. **Validation-only task**: "preview and validate
  data without performing any migration or data replication".
- **Taşındı:** `sync-anayasa` §4 · `sync-sozlesme` §9A · `sync-dogrulama` §7A.
- **Kullanım sınırı:** DMS bir **ürün örneğidir**; CoreMusic'te DMS
  kullanılacağı **iddia edilmez** (ürün seçimi `UNKNOWN` — §10/7).
  Taşınan yalnız **kalıp**: satır karşılaştırma + hata tablosu + durak.

### §12.2 W16 — Argo CD sync-options (dry-run)

- **Kanıt:** senkron işlemleri dry-run ile çalıştırılabilir;
  `SkipDryRunOnMissingResource=true` yalnızca **bulunamayan tür** için
  dry-run'ı atlar — CRD mevcutsa dry-run yine çalışır. `Prune=confirm`
  ve `Delete=confirm` silme işlemlerini **manuel onay** kapısına bağlar.
  `ApplyOutOfSyncOnly` yalnız uyumsuz kaynakları uygular;
  `FailOnSharedResource` paylaşımlı kaynakta hata verir;
  `RespectIgnoreDifferences` tanımlı farkları yok sayar.
- **Taşındı:** `sync-sozlesme` §9A.3 · `sync-dogrulama` §7C.
- **Kullanım sınırı:** "confirm" kapıları Argo CD'ye özgü **sözdizimidir**;
  CoreMusic sözleşmesine taşınan yalnız **manuel onay kapısı kavramıdır**.

### §12.3 W17 — AWS CLI describe-table-statistics (sayaçlar)

- **Kanıt:** tablo istatistikleri şunları taşır: `Inserts` / `Deletes` /
  `Updates` / `Ddls` (kaynak tarafı sayaç), `AppliedInserts` /
  `AppliedDeletes` / `AppliedUpdates` (hedefe uygulanan), `FullLoadRows` /
  `FullLoadErrorRows`, `ValidationPendingRecords` /
  `ValidationFailedRecords` / `ValidationSuspendedRecords`,
  `ValidationState` (+`Details`), `ResyncRowsAttempted` /
  `ResyncRowsSucceeded` / `ResyncRowsFailed` + `ResyncProgress`,
  `TableState`. İstek **sayfalanır** (`Marker` / `NextToken`).
- **Taşındı:** `sync-sozlesme` §9A.2 · `sync-dogrulama` §7A.2 (M8/M9).
- **Kullanım sınırı:** W15 ↔ W17 **aynı ürün ailesinin** konsol+CLI
  yüzeyidir → **tamamlayıcıdır, bağımsız çapraz sayılmaz** (§6).

### §12.4 W18 — GCS hashes-etags (checksum)

- **Kanıt:** nesneler **CRC32C** (önerilen) ve MD5 hash'i taşır; MD5
  **parçalı (multipart) yüklemede desteklenmez**. Yazma sırasında sunucu
  checksum'u istemci değeriyle **uyuşmazsa 400** döner. Kopyalama/
  yeniden yazma sırasında sunucu checksum doğrulaması yapılır. `gcloud
  storage cp` / `mv` / `rsync` içerik farkını checksum ile bulur;
  **rsync kademeleri**: (1) checksum → (2) boyut + `mtime` → (3) yalnız
  boyut; uyuşmazlıkta **geçersiz kopya silinir**.
- **Taşındı:** `sync-anayasa` §4 · `sync-sozlesme` §7A · `sync-dogrulama` §7B.
- **Kullanım sınırı:** bu iddia **tek kaynaktır** (W18) — DMS ile
  çaprazlanmamıştır (§4/§6 notu ⚠️). GCS'nin hash algoritması
  **CoreMusic seçimine çevrilmez** → seçim `UNKNOWN` (§10/4).

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | ≥12 benzersiz resmi kaynak | ✅ **18/12** (§3) |
| 2 | Her iddia URL+2026-10-16+Durum satırı taşır | ✅ §3 (18 satır) |
| 3 | 404/eksik adaylar elendi + nedeni yazıldı | ✅ §9 (5 satır / 4 neden ailesi) |
| 4 | Çakışma denetimi | ✅ §7 (0 çelişki; 18 benzersiz URL) |
| 5 | Çekilmeyen iddialar red listesinde | ✅ §10 (8 satır; #4 kaynak ✅ / seçim UNKNOWN) |
| 6 | Tur-2 fetch kanıtı kayıt defterinde + iddia detayları sınırıyla | ✅ §11A + §12 (W15–W18, aynı gün tekrar fetch; her madde kullanım sınırı taşıyor) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** AWS AppSync · AWS DMS · AWS CLI · Microsoft Graph · Google Cloud (Spanner/Pub/Sub/Storage) · PostgreSQL · Stripe · IETF RFC Editor · GitHub Docs · Argo CD · crdt.tech · Ink & Switch · **Tarih: 2026-10-16**
