---
title: "K013-integraion — Integration Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K013-integraion
role: master
authority: "SSOT: v2 §2.13 + .ai/architecture/index.md §5/13"
related: ["integraion-bagimliliklar.md", "integraion-dogrulama.md", "index.md"]
group: mimari
---


# K013-integraion — integration-master.md

> Bu dosya v2 §2.13'de adı geçen **master** dosyasıdır: adı kaynakta
> **integration-master.md** olarak geçer (birebir korunur), klasör adı ise
> **K013-integraion** (imla dokunulmaz — v2 §3 KURAL 1). Yeni rol dosyaları
> düğüm adından türetildi: integraion-bagimliliklar.md · integraion-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.13: *"Dış dünya ile köprü — API konuşmaları, adaptörler, üçüncü taraf sistemler."*

K013, CoreMusic'in **dış dünyaya tek kapısı**dır. Dört iş yapar:

1. **Sözleş** — dış API'nin istek/yanıtını yazar (api-contracts · event-contracts).
2. **Çevir** — iç model ile dış sistem arasında adaptör kurar
   (adapters · protocol-adapters · external-systems).
3. **Eşitle** — dış sistemle veriyi senkronlar (synchronization).
4. **Sınırlandır** — hatayı, tekrar-denemeyi ve kota/rate sınırını yönetir
   (error-handling) · bunu ispatlar (integration-testing).

Ne yapmaz: iç servis işi yapmaz (K012), arayüz göstermez (K011), ağ paketinin
ayrıntısını uydurmaz (K014 yalnız taşır), kimlik bilgisini saklamaz (K007/K010),
politika/kripto üretmez (K009'un işidir).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Dış kontratlar: api-contracts · event-contracts
- Çeviriler: adapters · protocol-adapters
- Dış sistem envanteri: external-systems
- Eşitleme ve hata sınırı: synchronization · error-handling
- Entegrasyon testi: integration-testing

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| Çağıran servis (dış çağrıyı başlatan) | K012-services (integration-service) | K013 yalnız kontrat/çeviri sunar |
| Kullanıcı akışı (dış servisi başlatan ekran) | K011-application | |
| Ağ taşıması, TLS, soket | K010-network | K013 yalnız HTTP vb. kullanır |
| Anahtar/secret saklama, imza | K009-security | Politika orada |
| OAuth/PKCE oturumu, kullanıcı kimliği | K0100-identitiy | |
| Kalıcı veri şeması | K007-data | synchronization sonucu yazar, şema K007'de |
| Medya dosyası/akış | K014-media | Dış katalog senkronu sınırı ⚠️ |
| Servis dağıtım/hat sorumluluğu | K018-devops | |

## §3 Kademe-2 çocuk tablosu (v2 §2.13 birebir — 8 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | api-contracts | Dış API sözleşmesi | İstek/yanıt tipleri, sürüm (sürüm stratejisi ⚠️) | draft |
| 2 | adapters | Üçüncü taraf adaptörleri | Sistem başına bir adaptör (envanter §3'ün adayları) | draft |
| 3 | external-systems | Dış sistem envanteri | Hangi sistem, ne durumda — kayıtlar §4'te | draft |
| 4 | event-contracts | Dış olay sözleşmesi | Dış gelen olayların biçimi (webhook mu ⚠️ UNKNOWN) | draft |
| 5 | protocol-adapters | Protokol dönüşüm köprüleri | Farklı protokol/şema arası çeviriler | draft |
| 6 | synchronization | Dış veri senkronizasyonu | K015/synchronization ile sınır ⚠️ (§7/2) | draft |
| 7 | error-handling | Hata ve sınır yönetimi | Hata sınıflandırma, tekrar-dene, rate limit (biçim ⚠️) | draft |
| 8 | integration-testing | Entegrasyon testleri | Sahte/canlı çevre testi (ortak test altyapısı K019) | draft |

> **Sahiplik notu:** Dış API'yi **çağıran** servis K012'dedir (integration-service);
> bu domain kontrat ve adaptörü tutar. İki tarafın yönü §5.3'te.

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | Spotify Web servisleri Authorization Code + PKCE akışı kullanır; token uç noktası accounts.spotify.com/api/token | https://accounts.spotify.com/api/token | 2026-10-12 | external-systems: Spotify entegrasyonu için token akışı (PKCE = public client) |
| 2 | Apple Music API, ES256 ile imzalanmış ve kid (key id) taşıyan JWT developer token ister | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | external-systems: Apple Music için developer token akışı |
| 3 | YouTube Music için resmi public API yoktur; TOS riski taşır | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | external-systems: kapsam dışı/beklemede kaydı |
| 4 | Evrensel Playlist/Track sözleşmesi hub-and-spoke modeliyle kurulabilir (open-playlist-engine yaklaşımı) | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | api-contracts ile service-contracts (K012) sınırı |

**§4'e girmeyen hiçbir dil, framework, paket veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| HTTP istemci kütüphanesi / dil | ⚠️ UNKNOWN |
| OAuth kütüphanesi (PKCE uygulaması) | ⚠️ UNKNOWN |
| Belge/şema formatı (OpenAPI vb.) | ⚠️ UNKNOWN |
| Olay taşıma biçimi (webhook/akış — hangisi) | ⚠️ UNKNOWN |
| Rate limit / retry politika değerleri | ⚠️ UNKNOWN |

> İddia 2–4 **URL'siz beyan** sınıfındadır (S3): kaydedildi, kanıtlanmadı.
> İddia 3 kesin olumlu/olumsuz **karar değildir** — "resmi API yok" iddiasıdır.

## §5 Bağımlılık özeti (tam metin: integraion-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K007-data | Senkron sonucunun saklanması | TASARIM İDDİASI |
| K008-domain | Kontrat tipleri | TASARIM İDDİASI |
| K009-security | Anahtar/imza/güvenlik | TASARIM İDDİASI |
| K0100-identitiy | OAuth/PKCE kimlik doğrulama | TASARIM İDDİASI |
| K010-network | Taşıma (HTTP vb.) | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Kullanıcı akışının dış çağrısı | TASARIM İDDİASI |
| K012-services | integration-service kontrat tüketimi | TASARIM İDDİASI |
| K014-media | Dış katalog senkronu (sınır ⚠️) | TASARIM İDDİASI |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011-application · K012-services · K014-media
        └─→ K013-integraion (dış kapıyı kullanır)
K013-integraion
├─→ K010-network   (taşıma)  ─→ dış dünya
├─→ K0100-identitiy (OAuth/PKCE) · K009-security (anahtar/imza)
├─→ K008-domain    (kontrat tipleri)
└─→ K007-data      (senkron sonucu)
K013'ün K012'ye geri dönüşü: YOK (yalnız used_by oku) → döngü yok
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.13 ile birebir | metin karşılaştırma (8/8) | ✅ verified |
| 2 | Master dosyası adı registry §5/13 ile birebir (integration-master.md) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırları yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt sütunu | ✅ verified (3 satır URL eksik ⚠️) |
| 5 | 5 dosyalık MD seti + 8 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 6 | YouTube Music iddiası (resmi public API yok) | — | ⚠️ VERIFY REQUIRED (kanıt yok) |
| 7 | Kademe-3 (bounded-context) kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §7 Açık sorular

1. YouTube Music resmi public API yok iddiası doğru mu, TOS riski nasıl yönetilir? → ⚠️ VERIFY REQUIRED
2. synchronization sahipliği: K013 mü, K015 mi? (aynı ad iki domain'de) → ⚠️ VERIFICATION REQUIRED
3. Dış API'yi **çağıran** taraf K012 mi (integration-service), yoksa K013 mü? → ⚠️ VERIFICATION REQUIRED
4. PKCE akışı Spotify'a özel mi, CoreMusic genel politikası mı? → TASARIM İDDİASI
5. Apple Music JWT (ES256+kid) kim üretir — uygulama mı, sunucu mu? → ⚠️ VERIFICATION REQUIRED
6. event-contracts taşıma biçimi (webhook/olay akışı) nedir? → ⚠️ UNKNOWN
7. hub-and-spoke evrensel Playlist/Track spec'i K012 mi K013 mü taşır? → TASARIM İDDİASI
8. external-systems envanterinin güncel kapsamı (hangi sistem dahil)? → ⚠️ VERIFICATION REQUIRED

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.13 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk listesi + zorunlu alanlar |
| Registry | .ai/architecture/index.md §5/13 · §8 | Master adı + isimlendirme |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| Spotify token uç noktası | https://accounts.spotify.com/api/token | 2026-10-12 kanıtı |
| Apple Music JWT (ES256 + kid) | ⚠️ URL yok | 2026-10-12 beyanı |
| YouTube Music (resmi public API yok) | ⚠️ URL yok | 2026-10-12 beyanı |
| hub-and-spoke Playlist/Track (open-playlist-engine) | ⚠️ URL yok | 2026-10-12 beyanı |

---

**Status:** draft · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-12

---

# EK-2026-10-18 — Entegrasyon Katmanı Derinleştirme

> Bu ek, §1–§8'in üzerine **append-only** yazılır; hiçbir mevcut bölüm/başlık
> silinmez veya değiştirilmez. Kanıt sınıfı: **[W1]–[W24] = 2026-10-18 HTTP
> doğrulamalı web kaynağı** (tam listesi §19 · erişim günlüğü
> `event-contracts/event-web-arastirma.md` §10'dadır). Etiket sözlüğü §4'teki
> gibidir: `TASARIM İDDİASI` · `✅ verified` · `⚠️ VERIFICATION REQUIRED` · `UNKNOWN`.

## §9 Entegrasyon Güvenilirlik Mimarisi — üç atom olmama sorunu

K013'ün tek yapısal zorluğu şudur: **iki atom olmayan eylemi eşzamanlı
güvenilir kılmak**. Üç sorun, birbirine karıştırılmamalıdır:

| # | Sorun | Kaynak | Çözüm ailesi |
|---|---|---|---|
| S-1 | DB transaksiyonu **ile** mesaj yayını atomik değil | [W1] [W2] | outbox · event sourcing · CDC |
| S-2 | Gelen isteğin **işlenip işlenmediği** belli değil | [W2] [W3] | inbox · idempotency key · dedup |
| S-3 | Birden fazla servis **arasındaki** tutarlılık | [W4] | saga (+compensating tx) |

**[W1] kanıtı (2026-10-18, HTTP 200):** *"...it is not viable to use a
traditional distributed transaction (2PC) that spans the database and the
message broker... if a service sends a message after committing the
transaction there's no guarantee that it won't crash before sending the
message."* → S-1'in tanımı kaynağında birebir böyledir.

### §9.1 Seçim matrisi (hangi soruna hangi mekanizma)

| Mekanizma | S-1 (yayın) | S-2 (alım) | S-3 (çapraz) | Maliyet | Etiket |
|---|---|---|---|---|---|
| Transactional outbox + relay | ✅ | — | — | 1 tablo + 1 worker | TASARIM İDDİASI |
| Inbox / idempotency key deposu | — | ✅ | — | 1 tablo + UNIQUE | TASARIM İDDİASI |
| Event sourcing | ✅ | — | — | model değişimi | TASARIM İDDİASI |
| Saga (choreography/orchestration) | — | — | ✅ | compensating tx zorunlu | TASARIM İDDİASI |
| 2PC / XA | ✅ | — | ✅ | kilit + kırılgan koordinatör | **[W1]** [W4]: tercih edilmez |
| Exactly-once delivery (broker) | — | kısmi | — | broker'a bağlı | [W7] [W9] |

**Karar (TASARIM İDDİASI, ADR bekliyor):** K013 bu üçünü **birlikte** kurar —
`outbox` (giden) + `inbox` (gelen) + `saga` (çapraz). Tek başına hangisi asla
yeterli değildir: [W2] açıkça der ki outbox *"doesn't prevent duplicate
requests — these still have to be handled on the recipient side"*.

## §10 Transactional Outbox — K008 domain event uyumu

### §10.1 Desenin tanımı (kaynağa sadık)

**[W1] (microservices.io · Chris Richardson · HTTP 200 · 2026-10-18):**
*"The solution is for the service that sends the message to first store the
message in the database as part of the transaction that updates the business
entities. A separate process then sends the messages to the message broker."*

Dört katılımcı (kaynaktaki adlar): **Sender** · **Database** · **Message
outbox** · **Message relay**. Fayda seti [W1]'de üç maddedir: 2PC kullanılmaz;
mesaj DB transaksiyonu commit edildiyse **garantili** gönderilir; sıra korunur.

### §10.2 Outbox tablosu (TASARIM İDDİASI — çekirdek alan seti)

> Şema **tasarım önerisidir**; K007-data'nın gerçek şeması **UNKNOWN** →
> `⚠️ VERIFICATION REQUIRED`. SQL blokları .md içindedir, `*.sql` dosyası değildir.

```sql
CREATE TABLE integration_outbox (
  id            BIGINT       NOT NULL AUTO_INCREMENT,
  aggregate_id  VARCHAR(64)  NOT NULL,   -- K008 aggregate kökü
  event_type    VARCHAR(128) NOT NULL,   -- domain event tipi
  schema_ver    SMALLINT     NOT NULL,   -- §13 sürüm (upcast zinciri)
  payload       JSON         NOT NULL,
  headers       JSON         NULL,       -- trace-id / correlation-id
  occurred_at   DATETIME(6)  NOT NULL,
  status        ENUM('PENDING','PUBLISHED','FAILED') NOT NULL DEFAULT 'PENDING',
  attempts      INT          NOT NULL DEFAULT 0,
  available_at  DATETIME(6)  NOT NULL,   -- backoff hedefi (§17)
  PRIMARY KEY (id),
  KEY idx_outbox_poll (status, available_at, id),
  KEY idx_outbox_agg  (aggregate_id, id)
) ENGINE=InnoDB;
```

**Kritik kural 1 — polling anahtarı:** `[status, available_at, id]` bileşik
anahtarı, relay'in `WHERE status='PENDING' AND available_at <= NOW()` sorgusunun
**index'den** gitmesini sağlar; aksi halde her turda full-scan olur.

**Kritik kural 2 — sıra:** [W1] olay sırasının korunmasını **zorunlu** kılar
(`T1 → E1` önce, `T2 → E2` sonra). Aynı aggregate'ten yayınlanan satırların
**`id` artan sırayla** gönderilmesi gerekir; farklı aggregate'ler arası sıra için
garanti **yoktur** → `⚠️ VERIFICATION REQUIRED` (sıra kapsamı kararı verilmedi).

### §10.3 Relay — üç yayın yöntemi (TASARIM İDDİASI + kaynak)

| Yöntem | Ne yapar | Güçlü | Zayıf | Kaynak |
|---|---|---|---|---|
| **Polling Publisher** | periyodik SELECT + publish + UPDATE | basit | DB'ye yük; en az 1 tur gecikme | [W1] [W2] |
| **Transaction Log Tailing / CDC** | WAL/redo log'unu okur | ek SELECT yok | altyapı olgunluğu şart | [W1] [W2] [W10] |
| **RabbitMQ publisher confirm** | broker teyidi ile "gitti" kesinleşir | teslim teyidi | outbox satırı hâlâ kendi işi | [W11] |

**[W2] (softwaremill · HTTP 200 · 2026-10-18)** polling darboğazını sayısal
olarak tarif eder: sorgu *"usually as plain as a simple SELECT"*, ama *"high
interval (usually below 1s, very often way below)"* çalışır; yükü azaltmak için
**batch büyütmek** ve **`SELECT ... FOR UPDATE SKIP LOCKED`** ile satır kilitleme
kullanılır (kaynakta adı geçen DB'ler: **Postgres ve MySQL**).

**Aynı kaynak ([W2]) outbox'ın sınırlarını da söyler — dürüstlük satırı:**
işaretlenme adımı da bir transaksiyondur; *"After a successful request, the
transaction can fail and the row in the outbox table won't be modified...
the target will get a message twice."* → sonuç **at-least-once**, sıfır-kopya
**değildir**.

### §10.4 K008 domain event ile uyum (sahiplik matrisi)

| Alan | Üreten | Yazan | Okuyan | Etiket |
|---|---|---|---|---|
| `event_type` / payload tipi | K008-domain | K013 (outbox) | K012 / K014 | TASARIM İDDİASI |
| `schema_ver` | K008 | K013 | relay + consumer | TASARIM İDDİASI |
| `aggregate_id` | K008 | K013 | sıralama/parçalama | TASARIM İDDİASI |
| outbox **tablosu** (fiziksel) | — | **K007-data** | relay | `⚠️ VERIFICATION REQUIRED` (şema sahibi K007 mü K013 mü — §7/3 ile aynı açık) |
| relay **çalıştırma** (process) | — | `UNKNOWN` | — | `UNKNOWN` (hangi düğümde) |

**Sınır cümlesi:** K013 outbox **desenini** tanımlar; tablonun fiziksel
şeması ve mülkiyeti K007 ile çözülmemiştir → bu satır §7/3'teki açık soruyla
**aynıdır, ikinci bir soru değildir**.

## §11 Inbox / Idempotency — gelen isteğin akıbeti

### §11.1 Neden inbox ayrıdır

Outbox **giden** tarafı kurtarır; gelen tarafta (webhook, kuyruk tüketimi,
dış API yanıtının tekrar işlenmesi) sorun şudur: aynı istek **iki kez** gelebilir
ve ikisi de geçerli imzaya sahip olabilir. Çözüm, işin **sonucunu** saklamaktır —
işin kendisini değil.

| Yaklaşım | Ne saklar | Sorgu | Risk |
|---|---|---|---|
| **Inbox tablosu** | işlenen olay/komut kimliği | `SELECT ... LIMIT 1` | sadece kendi trafik |
| **Idempotency key deposu** | istek anahtarı **+ yanıt gövdesi** | `INSERT ... ON DUPLICATE` | yanıtı da saklar (yeniden-oyrama) |
| UNIQUE constraint | yalnız varlık | DB hatası ile yakalar | yanıt saklanmaz |

**[W3] (docs.stripe.com/idempotency · HTTP 200 · 2026-10-18)** üçüncü yolu
kullanır: tekrarlanan istekte sunucu **ilk yanıtın kendisini** döndürür; aynı
anahtarla **farklı** gövde göndermek hata verir. `⚠️ VERIFICATION REQUIRED`:
bu davranışın CoreMusic API'sine taşınması için ADR **yoktur**.

### §11.2 Inbox şeması (TASARIM İDDİASI)

```sql
CREATE TABLE integration_inbox (
  idem_key     VARCHAR(191) NOT NULL,     -- dış sağlayıcı + olay id birleşimi
  consumer     VARCHAR(64)  NOT NULL,     -- hangi işleyici
  state        ENUM('PROCESSING','PROCESSED','FAILED') NOT NULL,
  response     JSON         NULL,         -- opsiyonel: yanıt gövdesi (yeniden-oyrama)
  first_seen   DATETIME(6)  NOT NULL,
  last_seen    DATETIME(6)  NOT NULL,
  attempts     INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (consumer, idem_key)
) ENGINE=InnoDB;
```

**Sıra-satır kural (kritik):** `PROCESSING` kaydı **işlemden önce** yazılır
(insert-or-get), `PROCESSED` ise **işlemden sonra** güncellenir. Böylece çökme
olasılığı ikiye ayrılır: önce yazarsan → tek fakat **olabilirli** kayıt; sonra
yazarsan → kopya işlenir. **Doğru olan ilkidir** (kaynak türü: [W2]'nin
"persist intent first" mantığının inbox'a simetriği → `TASARIM İDDİASI`).

### §11.3 Processör sentezi (tek cümlede davranış)

```text
inbox'a PROCESSING kaydı yaz (varsa "zaten çalışıyor" de)
  → işi yap (dış API çağrısı / olay uygulama)
  → PROCESSED'a geç + yanıt gövdesini sakla
  → hatada: attempts++ · available_at = backoff (§17) · FAILED eşikte → DLQ (§17.4)
```

## §12 Exactly-once vs at-least-once — ne iddia edilebilir

### §12.1 Üç semantik (tanım matrisi)

| Semantik | Anlamı | Neyi gerektirir | Kaynak |
|---|---|---|---|
| At-most-once | en fazla bir kez; **kaybetmek mümkün** | fire-and-forget | [W7] |
| At-least-once | hiç **kaybolmaz**; kopya gelebilir | tekrar-dene + kalıcı kuyruk | [W1] [W2] [W7] [W12] |
| Exactly-once (efkâri) | kopya işlenmez **ve** kaybolmaz | broker + **idempotent consumer** | [W8] [W9] |

**[W1] kesin cümle (2026-10-18):** relay çökmeden önce yayını yaparsa satır
hâlâ `PENDING`'dir ve tekrar gönderilir → *"a message consumer must be
idempotent, perhaps by tracking the IDs of the messages that it has already
processed."*

### §12.2 CoreMusic iddia sınırı (Truth Mode)

| İddia | Etiket |
|---|---|
| K013 dış çağrı zincirinde **at-least-once** garantisi hedeflenir | TASARIM İDDİASI |
| "Sıfır kayıp" (outbox sayesinde) | ✅ [W1] koşullu — DB transaksiyonu commit ise |
| "Sıfır kopya" (broker garantisiyle) | **YAZILMAZ** — [W2] bunu açıkça reddeder |
| "Exactly-once" | **YAZILMAZ** — ancak `idempotent consumer` ile *işlem-kez* kurulabilir → [W8] [W9] |
| Hangi broker'ın ne verdiği | `UNKNOWN` (broker seçimi yok) → `⚠️ VERIFICATION REQUIRED` |

> **Yasak cümle:** *"exactly-once delivery sağlanmıştır"* — bu belgede
> **yazılmaz**. Doğru olan: *"at-least-once teslim + idempotent işleyici ile
> exactly-once **etki** hedeflenir"* → o da `TASARIM İDDİASI`dır.

### §12.3 İşlem-kez (exactly-once effect) için dört koşul

1. Teslim **en az bir kez** olmalı (yoksa hiç gelmez) → [W1] [W12]
2. İşleyici **idempotent** olmalı → [W1] [W2] [W16] (EIP Idempotent Receiver)
3. Kopya tespiti için **kalıcı** kimlik deposu gerekir (§11.2) — bellek içi yeterli **değildir**
4. Paralel işleyicilerde aynı anahtar **aynı kuyruğa** gitmeli → aksi halde iki worker eşzamanlı işler → `TASARIM İDDİASI`

## §13 Event Versioning & Upcasting — sürüm kırılmadan yaşam

### §13.1 Üç strateji (anahtar-karşılaştırma)

| Strateji | Ne yapar | Ne zaman güvenli | Maliyet |
|---|---|---|---|
| **Upcasting** (okuma anında) | eski sürüm gövdesini yeniye çevirir | consumer tek sürüm görür | consumer'da dönüşüm kodu |
| **Çoklu-sürüm yayını** (çıkışta) | aynı olayı N sürümle yazar | consumer'lar yavaş güncelleniyor | depolama ×N |
| **Şema alt-kümeleri** (expand/contract) | yalnız **ekleme** yapılır, alan silinmez | alan bazlı uyumluluk | en ucuz |

**Kural (TASARIM İDDİASI, CoreMusic'e bağlanır):** [W1]'in `[schema_ver]`
alanı §10.2'de zorunludur; dönüşüm **consumer tarafında** yapılır, yayınlayan
taraf eski sürümü **asla yeniden yazmaz** (o olay kuyrukta/dış sistemde olabilir).

### §13.2 Sürüm sayacı — ne sayılır, ne sayılmaz

| Değişiklik | Sürüm etkisi | Sınıf |
|---|---|---|
| Yeni **zorunlu olmayan** alan ekleme | **artırmaz** (alt-küme uyumu) | uyumlu |
| Yeni **zorunlu** alan ekleme | **kırar** | breaking |
| Alan **silme / yeniden adlandırma** | **kırar** | breaking |
| Alan tipini daraltma (string→int) | **kırar** | breaking |
| Alan tipini genişletme (int→string? alıcıda) | tartışmalı | ⚠️ `VERIFICATION REQUIRED` |
| Yeni olay **tipi** ekleme | artırmaz; **bilinmeyen tip politikası** gerekir | uyumlu + koşul |
| Enum'a yeni değer ekleme | alıcı tarafı bilmiyorsa **kırabilir** | koşullu |

**Bilinmeyen-tip politikası (TASARIM İDDİASI):** bilinmeyen `event_type`
**sessizce yutulmaz** — `UNKNOWN` olarak kuyruğa/DLQ'ya düşer ve metrik
üretir. (Bu kural `protocol-adapters/proto-sozlesme.md` §7.4 "Bilinmeyen Alan
Politikası" ile **aynı ilkedir**; ikinci bir kural değildir.)

### §13.3 Upcast örneği (şema dönüşümü — TASARIM İDDİASI)

```text
v1  TrackLikedV1 { trackId }
      ↓ upcast (consumer)
v2  TrackLikedV2 { trackId, likedAt, source }
      ↓ upcast
v3  TrackLikedV3 { trackId, likedAt, source, deviceId? }
```

Dönüşüm **zincir halindedir** (v1→v2→v3), her adım **idempotent** ve
**kararlıdır** (aynı girdi → aynı çıktı). Zincirin atlanması yalnız **doğrudan
v1→v3 köprüsü** yazılırsa mümkündür — o da ayrı bir `TASARIM İDDİASI`dır.

**[W5] (learn.microsoft.com/event-sourcing · HTTP 200 · 2026-10-18)** olay
geçmişinin **salt-okunur** olduğunu ve geçmişteki olayların **değiştirilmediğini**
temel alır → upcast, geçmişi değiştirmeyip **yalnızca okuma anında yorumu**
değiştirdiği için bu modele uyar. (`⚠️ VERIFICATION REQUIRED`: CoreMusic'te
event sourcing uygulanıp uygulanmadığı **UNKNOWN**.)

## §14 Saga vs 2PC — çapraz servis tutarlılığı

### §14.1 Kaynak tanımı

**[W4] (microservices.io/patterns/data/saga.html · HTTP 200 · 2026-10-18):**
*"Implement each business transaction that spans multiple services as a saga.
A saga is a reliably coordinated sequence of local transactions...
either all steps complete successfully or, if a local transaction fails...
the changes made by the completed steps are undone by compensating
transactions."* İki koordinasyon biçimi kaynakta adıyla geçer:
**Choreography** (her yerel transaksiyon domain event yayar) ve
**Orchestration** (bir orchestrator katılımcıya ne yapacağını söyler).

### §14.2 Karar matrisi

| Ölçüt | 2PC | Saga (choreo) | Saga (orch) |
|---|---|---|---|
| Atomiklik | tam (eğer destekleniyorsa) | compensating ile | compensating ile |
| İzolasyon (ACID "I") | ✅ | ❌ **yok** [W4] | ❌ **yok** [W4] |
| Geri alma | otomatik | **elle** tasarlanır [W4] | **elle** tasarlanır [W4] |
| Yayılım/kilit | yüksek | düşük | orta |
| Görünürlük | düşük (kara kutu) | dağınık | **yüksek** (tek akış) |
| K013 uyumu | tercih edilmez [W1] [W4] | outbox şart [W1] | outbox + state makinesi |

**[W4]'ün itiraf ettiği iki eksik (birebir):**
*"Lack of automatic rollback"* ve *"Lack of isolation (the 'I' in ACID)"* —
ikincisi için kaynağın önerisi **countermeasure** kavramıdır ve *"careful
analysis is needed to select and correctly implement the countermeasures."*

### §14.3 Compensating transaction kuralı (TASARIM İDDİASI)

1. Her adımın **eşdeğeri geri-alma** adımı önceden tanımlıdır (adım yazılmadan önce).
2. Compensating işlem de **idempotent** olmalıdır (iki kez çalışabilir).
3. Compensating **başarısız** olursa: `⚠️ VERIFICATION REQUIRED` → manuel müdahale
   akışı (K017 gözlem/K018 değil, bu domain'in kapısı) **tanımsızdır**.
4. Saga **adım başarısızlığı** ile **tekrar-dene başarısızlığı** aynı şey
   **değildir**: önce backoff ile denenir (§17), eşik aşılırsa compensating'e geçilir.

**[W18] (learn.microsoft.com/compensating-transaction · HTTP 200 · 2026-10-18)**
saga'nın tamamlayıcı deseni olarak bu kavramı işler → `⚠️ VERIFICATION REQUIRED`:
ayrıntılar bu turda **okunmadı**, yalnız erişilebilirlik doğrulandı.

### §14.4 Çıktının müşteriye dönüşü (saga sonucu) — [W4] üç seçenek

| Seçenek | Ne yapar | Uygulama maliyeti |
|---|---|---|
| Bekleme (sync yanıt) | `OrderApproved`/`OrderRejected` gelince yanıtla | bağlantı açık kalır |
| **Polling** | `orderID` dön, müşteri `GET /orders/{id}` ile sorar | en basit |
| Event/webhook | tamamlanınca istemciye olay gönder | ek kanal |

**CoreMusic seçimi:** `UNKNOWN` (karar yok) → `⚠️ VERIFICATION REQUIRED`.

## §15 DTO / API Versioning — sözleşmenin değişmesi

### §15.1 İki ayrı sürüm yüzeyi (karıştırılmamalı)

| Yüzey | Ne sürümlenir | Sahip | Mekanizma |
|---|---|---|---|
| **DTO / şema** | alan adı, tip, zorunluluk | K008 + K013 kontratı | expand/contract · `schema_ver` |
| **Endpoint** | yol, metod, yanıt kodu | K013 api-contracts | URL/header sürümü |

**Ayrım kuralı (TASARIM İDDİASI):** `schema_ver` (olay) ile `API sürümü`
(HTTP) **bağımsızdır** — biri değişirken diğeri zorunlu olarak değişmez.

### §15.2 Sürüm taşıma yolları (karşılaştırma)

| Yol | Örnek | Artı | Eksi | K013 notu |
|---|---|---|---|---|
| URL sürümü | `/v1/tracks` | açık, proxy'ler kolay | yolu tekrarlar | her ikisi de ⚠️ |
| Header sürümü | `Accept: application/vnd.cm.v2+json` | yol temiz | test/ızgara zor | ⚠️ |
| Query sürümü | `?version=2` | hızlı | cache kırar | ⚠️ |
| **Medya tipi (content negotiation)** | RFC-örtüsü `+json` | ortam-duyarsız | operasyonel yük | [W20] OpenAPI medya tipi |
| Geriye-uyumlu alan | ekleyerek genişlet | **kırılma yok** | alan şişmesi | §13.2 |

**[W20] (spec.openapis.org/oas/latest.html · HTTP 200 · 2026-10-18)** OpenAPI
'nin kontrat dosyasının kendisi için **ana sürüm alanı** `openapi: 3.x.x`
alanıdır → belge sürümü ile **API sürümü** ayrıdır (aynı ayrım §15.1'deki gibi).

### §15.3 Sunucu-üstünü-destekleme kuralı (TASARIM İDDİASI)

1. Sunucu en az **iki API sürümü** aynı anda destekler (eski + yeni).
2. Eski sürüm için **kullanım ölçümü** tutulur; kullanım `0`'a inmedikçe **kaldırılmaz**.
3. Kaldırma = **breaking change** ilanı + süre duyurusu.
4. Dış sağlayıcının sürümü **bizim kontrolümüzde değildir** → onların sürüm
   yükseltmesi **bizim adaptör değişikliğimizdir** (adapters §5'teki sürüm
   köprüsüyle) → `⚠️ VERIFICATION REQUIRED`: CoreMusic'te dış sürüm takip
   sorumlusu **atanmadı**.

## §16 API Gateway Routing & Adaptör

### §16.1 Gateway ne yapar, ne yapmaz (sınır)

| Yapar | Yapmaz |
|---|---|
| Yol → servis eşlemesi (routing) | iş mantığı |
| Kimlik doğrulama başlığı sonlandırma/ilerletme | anahtar **üretimi** (K009) |
| Rate limit / throttling başlangıcı | iş mantığı kotaları |
| Dış istek → iç DTO **çevirmesi tetikleme** | kalıcı şema yazımı (K007) |
| Yanıt kodu/ölçek sınırlama | saga koordinasyonu |

**[W21] (learn.microsoft.com/gateway-routing · HTTP 200 · 2026-10-18)** ve
**[W22] (…/backends-for-frontends · HTTP 200 · 2026-10-18)** bu iki desenin
adlandırılmış karşılığıdır; **[W23] (learn.microsoft.com/api-management-key-concepts
· HTTP 200 · 2026-10-18)** ise üretimde gateway'lerin yönetimsel yüzeyini
işler. `⚠️ VERIFICATION REQUIRED`: CoreMusic'in gateway'si hangi üründe/asla
**UNKNOWN**.

### §16.2 Routing tablosu (TASARIM İDDİASI — çekirdek)

| Dış yol | Yöntem | Hedef iç servis | DTO çevirisi | Kimlik |
|---|---|---|---|---|
| `/ext/v1/tracks/*` | GET | K012 integration-service | `adapters` | Bearer (K009) |
| `/ext/v1/playlists/*` | GET/POST | K012 | `adapters` | Bearer |
| `/ext/v1/webhooks/*` | POST | `UNKNOWN` (K012 mi K013 mü — §7/3) | inbox (§11) | **HMAC** (§17) |

> **Yasak:** bu tabloya `UNKNOWN` sütunu yerine **uydurma** iç servis adı yazılmaz.

### §16.3 Adaptör ile gateway farkı (tek cümle)

**Gateway** isteği **yönlendirir**; **adaptör** isteği **çevirir**. Aynı istek
ikisinden de geçer; sorumluluk dağılımı `adapters/adapters-anayasa.md` §1'dedir.

## §17 Webhook Retry + HMAC İmzalama

### §17.1 İmza (doğrulanmış — iki bağımsız kaynak)

| Kaynak | Başlık | Algoritma | Yapı | HTTP | Tarih |
|---|---|---|---|---|---|
| **[W6]** Stripe | `Stripe-Signature` | **HMAC-SHA256** | `t=<ts>,v1=<hex>` | 200 | 2026-10-18 |
| **[W15]** GitHub | `X-Hub-Signature-256` | **HMAC-SHA256** | `sha256=<hex>` | 200 | 2026-10-18 |
| **[W13]** AWS SigV4 | `Authorization` | **HMAC-SHA256** (imza zinciri) | `AWS4-HMAC-SHA256` | 200 | 2026-10-18 |
| **[W14]** WebSub (W3C) | `X-Hub-Signature` | imza başlığı zorunlu değil ama tanımlı | — | 200 | 2026-10-18 |

**[W6]'dan birebir (2026-10-18):** imza, `signed_payload = <timestamp> + "." + <ham JSON gövde>`
üzerinden hesaplanır; **ham gövde** şarttır (*"Stripe requires the raw body of the
request to perform signature verification"*); karşılaştırma **constant-time**
olmalıdır; zaman aşımı toleransı için kaynak şunu der: *"Our libraries have a
default tolerance of 5 minutes between the timestamp and the current time"* ve
*"Don't use a tolerance value of `0`."*

**[W15]'ten birebir (2026-10-18):** imza **hex** üretilir, her zaman `sha256=`
ile başlar; düz `==` **yasaktır** (*"Never use a plain `==` operator"*), yerine
sabit-zamanlı karşılaştırma (ör. `crypto.timingSafeEqual`) önerilir. Kaynak
ayrıca **resmi test vektörü** yayınlar: `secret = "It's a Secret to Everybody"`,
`payload = "Hello, World!"` → `sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17`.

**K013 sözleşmesi (TASARIM İDDİASI):** `HMAC-SHA256` + `t=` zaman damgası +
sabit-zamanlı karşılaştırma + **zaman penceresi**; pencere değeri `UNKNOWN`
([W6]'nın 5 dakikası **Stripe'a aittir**, CoreMusic'e kopyalanmaz).

### §17.2 Retry politikası (doğrulanmış davranış, uygulama değeri TASARIM İDDİASI)

| Kaynak | Politika | HTTP | Tarih |
|---|---|---|---|
| **[W6]** Stripe | canlı ortamda **3 güne kadar** exponential backoff; sandbox'ta birkaç saat içinde 3 deneme | 200 | 2026-10-18 |
| **[W6]** Stripe | manuel yeniden gönderim: Dashboard **15 gün**, CLI **30 gün** | 200 | 2026-10-18 |
| **[W17]** Google Pub/Sub push | tekrar-dene + ölü mesaj konusu | 200 | 2026-10-18 |
| **[W19]** AWS Lambda + SQS | **kısmi** parti başarısızlığı (yanıtta hangi mesaj başarısız) | 200 | 2026-10-18 |
| **[W11]** RabbitMQ | publisher confirm ile teslim teyidi | 200 | 2026-10-18 |

**[W6] ayrıca sıralama hakkında nettir:** *"Stripe doesn't guarantee the
delivery of events in the order that they're generated"* ve `created`
alanına göre sıra **kurulmaz**; kopya tespiti için **event id** kullanılır.
→ **Dış sistem olayları sıralı değildir**; bu K013'ün senkron katmanında
**zorunlu** bir girdidir (§18).

### §17.3 Backoff formülü

```text
delay(n) = min(cap, base × 2^n)  ±  jitter
jitter:  r = random()             (tam jitter)
         veya  delay × (0.5 + 0.5·r)   (equal jitter)
```

**[W9] (learn.microsoft.com/retry · HTTP 200 · 2026-10-18)** gecikmenin
*"incrementally or exponentially"* artırılabileceğini, ancak **sayısal `base`
değerini sayfada vermediğini** belirtir → `base`/`cap` sayısal değerleri bu
belgede **yazılmaz** (`⚠️ VERIFICATION REQUIRED`, bkz `error-handling/error-anayasa.md` §4).
Aynı kaynak **idempotency uyarısını** da koşul koyar: *"Consider whether the
operation is idempotent. If so, it's inherently safe to retry."*

### §17.4 Sınır: tekrar-dene biterse

```text
en fazla N deneme (N = UNKNOWN → ⚠️)
  → başarisiz: mesaj/outbox satırı FAILED
  → ölü mesaj kuyruğu (dead-letter)  [W16] [W17]
  → gözlem/Alarm  (K017 sahipliği ⚠️ VERIFICATION REQUIRED — error-anayasa §A/3)
```

## §18 Senkronizasyon Çakışma Çözümü

**Bağlam:** dış sistemlerin olayları sırasızdır (§17.2 [W6]) ve senkronlar
artımlıdır (`synchronization/sync-anayasa.md` §2–§9). Çakışma = *aynı kaydın
iki farklı son sürümü*.

### §18.1 Beş çözüm (seçim matrisi)

| # | Yöntem | Kural | Ne zaman | Etiket |
|---|---|---|---|---|
| 1 | **Son-yazan-kazanır (LWW)** | `updated_at` en büyük kazanır | yalnız tek-yazar alanlar | TASARIM İDDİASI |
| 2 | **Sürüm/etag karşılaştırması** | `version` artmadıysa uygula, yoksa çakışma | kaynak destekliyorsa | `synchronization/sync-sozlesme.md` §6 |
| 3 | **Alan-bazlı birleştirme (merge)** | hangi alan değiştiyse o | alanlar bağımsız | TASARIM İDDİASI |
| 4 | **Kaynak-öncelikli (kural tabanlı)** | tek sistem "gerçek sahip" | müzik katalog → dış sistem | `⚠️ VERIFICATION REQUIRED` |
| 5 | **İnsan kararı / log** | çakışma kayda düşer, çözüm bekler | az ve kritik | TASARIM İDDİASI |

### §18.2 Zorunlu üç kural

1. **Sessiz ezme yoktur** — çakışma yakalanırsa ya uygulanır ya kayda düşer;
   sessizce geçilmez.
2. **Tombstone korunur** — dış sistemde silinen kayıt, içeride de silinmelidir
   (`sync-sozlesme.md` §5); aksi halde yeniden-belirir.
3. **Tarih-saati iki taraf için de güvenilmezdir** — [W6] sıralama için
   `created`'ı reddeder → **çakışma kararı `updated_at`'e tek başına
   dayandırılamaz**, `⚠️ VERIFICATION REQUIRED` (hangi alan sahibi henüz yok).

### §18.3 Uzlaşma yoksa

`⚠️ VERIFICATION REQUIRED` → çakışma kaydı **metrik** üretir ve
**Vault Steward** kararı bekler (`synchronization/sync-dogrulama.md` §7A
Reconciliation kapısı ile aynı kapı).

## §19 Kaynak Tablosu (bu ekteki tüm URL'ler — 2026-10-18)

> Tüm satırlar **2026-10-18** tarihinde **gerçekten isteklenmiştir**; HTTP
> durumu PowerShell .NET `HttpClient` (GET, yönlendirme açık) ile ölçülmüştür.
> `⚠️` = iddia taşımaz, yalnız erişilebilirlik kaydıdır.

| # | Kaynak | Kanıtladığı iddia (bu ekte) | HTTP | Tarih |
|---|---|---|---|---|
| W1 | https://microservices.io/patterns/data/transactional-outbox.html | §9 S-1 tanımı · §10.1 · §12.1 · relay+idempotent consumer | 200 | 2026-10-18 |
| W2 | https://softwaremill.com/microservices-101/ | §10.3 polling yükü + `SKIP LOCKED` · §11.1 "kopya engellenmez" · §12.1 | 200 | 2026-10-18 |
| W3 | https://docs.stripe.com/idempotency | §11.1 idempotency key + yanıt saklama | 200 | 2026-10-18 |
| W4 | https://microservices.io/patterns/data/saga.html | §14.1/§14.2/§14.4 saga tanımı · rollback+izolasyon eksikleri · 3 sonuç yolu | 200 | 2026-10-18 |
| W5 | https://learn.microsoft.com/en-us/azure/architecture/patterns/event-sourcing | §13.3 salt-okunur olay geçmişi | 200 | 2026-10-18 |
| W6 | https://stripe.com/docs/webhooks/signatures | §17.1 HMAC-SHA256+ham gövde+5 dk · §17.2 3 gün backoff · §17.2 sıra yok | 200 | 2026-10-18 |
| W7 | https://kafka.apache.org/documentation/#semantics | §12.1 üç teslim semantiği | 200 | 2026-10-18 |
| W8 | https://cloud.google.com/pubsub/docs/exactly-once-delivery | §12.1/§12.2 exactly-once kapsamı | 200 | 2026-10-18 |
| W9 | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | §17.3 backoff+idempotency uyarısı · §14.3 | 200 | 2026-10-18 |
| W10 | https://microservices.io/patterns/data/cqrs.html | §10.3 CDC/CQRS bağlantısı (bilgi) | 200 | 2026-10-18 |
| W11 | https://www.rabbitmq.com/docs/confirms | §10.3/§17.2 publisher confirm | 200 | 2026-10-18 |
| W12 | https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/standard-queues.html | §12.1 at-least-once (standart kuyruk) | 200 | 2026-10-18 |
| W13 | https://docs.aws.amazon.com/general/latest/gr/signature-version-4.html | §17.1 HMAC-SHA256 imza zinciri | 200 | 2026-10-18 |
| W14 | https://www.w3.org/TR/websub/ | §17.1 WebSub imza başlığı | 200 | 2026-10-18 |
| W15 | https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries | §17.1 `sha256=` + sabit-zamanlı karşılaştırma + test vektörü | 200 | 2026-10-18 |
| W16 | https://www.enterpriseintegrationpatterns.com/patterns/messaging/DeadLetterChannel.html | §17.4 dead-letter adı | 200 | 2026-10-18 |
| W17 | https://cloud.google.com/pubsub/docs/push | §17.2 push retry + ölü mesaj | 200 | 2026-10-18 |
| W18 | https://learn.microsoft.com/en-us/azure/architecture/patterns/compensating-transaction | §14.3 compensating transaction | 200 | 2026-10-18 |
| W19 | https://docs.aws.amazon.com/lambda/latest/dg/services-sqs-errorhandling.html | §17.2 kısmi parti başarısızlığı | 200 | 2026-10-18 |
| W20 | https://spec.openapis.org/oas/latest.html | §15.2/§15.3 belge sürümü ≠ API sürümü | 200 | 2026-10-18 |
| W21 | https://learn.microsoft.com/en-us/azure/architecture/patterns/gateway-routing | §16.1 gateway routing deseni | 200 | 2026-10-18 |
| W22 | https://learn.microsoft.com/en-us/azure/architecture/patterns/backends-for-frontends | §16.1 BFF deseni | 200 | 2026-10-18 |
| W23 | https://learn.microsoft.com/en-us/azure/api-management/api-management-key-concepts | §16.1 gateway yönetim yüzeyi | 200 | 2026-10-18 |
| W24 | https://www.enterpriseintegrationpatterns.com/patterns/messaging/IdempotentReceiver.html | §11.1/§12.3 idempotent receiver adı | 200 | 2026-10-18 |

### §19.1 Bu ekte **404 / erişilemeyen** adaylar (dayanak yapılmadı)

| Aday URL | Sonuç | Not |
|---|---|---|
| `https://martinfowler.com/2023/transactional-outbox.html` | **404** | görevde adı geçen Fowler-outbox kaynağı **bu adreste YOK** |
| `https://martinfowler.com/bliki/TransactionalOutbox.html` | **404** | bliki karşılığı yok |
| `https://martinfowler.com/articles/transactional-outbox.html` | **404** | üçüncü aday da yok |
| `https://learn.microsoft.com/en-us/aspnet/core/security/webhooks` | **404** | ASP.NET Core webhook rehberi bu yolda yok |
| `https://www.rabbitmq.com/tutorials/confirms.html` | **404** | eski tutorial yolu → yeni yol [W11] 200 |
| `https://www.enterpriseintegrationpatterns.com/patterns/messaging/GuaranteedDelivery.html` | **404** | bu sayfa bu adreste yok |
| `https://www.enterpriseintegrationpatterns.com/patterns/messaging/TransactionallySharedDatabase.html` | **404** | bu sayfa bu adreste yok |
| `https://temporal.io/blog/how-temporal-works` | **404** | blog yolu yok |
| `https://debezium.io/documentation/reference/stable/connectors/mysql/#mysql-outbox-event-router` | **404** | bu yolda yok |

> **Dürüstlük notu:** Fowler'ın transactional-outbox makalesi beklenen bir
> kaynaktı; **üç ayrı adreste 404** alındı → bu ekte Fowler'a **dayanak
> atfedilmedi**. Doğru kaynak [W1] (microservices.io) ve [W2] (softwaremill) olmuştur.
> `⚠️ VERIFICATION REQUIRED`: Fowler'ın konuya ilişkin yazısı varsa farklı bir
> adreste olmalıdır — sonraki tur işidir.

## §A Kapılar (bu ek için)

1. **§10.4** outbox tablosunun fiziksel sahibi (K007 ↔ K013) → `⚠️ VERIFICATION REQUIRED` (§7/3 ile aynı açık).
2. **§12.2** "exactly-once" kelimesi bu belgede **yazılmaz**; ihlal edilirse ek geçersizdir.
3. **§17.1** HMAC zaman penceresi **Stripe 5 dk değerine kopyalanmaz** — CoreMusic değeri `UNKNOWN`.
4. **§15.3/4** dış sağlayıcının API sürümü takip sorumlusu atanmadı → `⚠️ VERIFICATION REQUIRED`.
5. **§19.1** Fowler-outbox 404 kaydı **silinemez**; "Fowler şöyle der" cümlesi bu belgede **yazılmaz**.
6. Bu ek yeni **dosya/dizin üretmez**; §GRUPLAR tablosu (`index.md`) değiştirilmemiştir → bkz orkestratöre devir DRIFT-1.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Ek Tarih:** 2026-10-18 · **Ölçüm:** append-only (ReadAllLines)
