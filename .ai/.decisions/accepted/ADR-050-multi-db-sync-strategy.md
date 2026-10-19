---
title: "CoreMusic — ADR-050: Multi-DB Sync Strategy (hangi veri · hangi kanal/API · sync tipi · çakışma çözümü · izleme/kurtarma · zamanlama)"
type: "architecture-decision"
category: "database"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic multi-DB senkronizasyon stratejisi: (a) kapsam = ADR-040 yetki matrisi içindeki veriler, kanal = outbox olayı (ADR-081) veya rapor/analitik ETL, servis↔servis HTTP yasak (ADR-039), geri yazım yok (tek yazar), (b) tip = olay/outbox BİRİNCİL, batch ETL İKİNCİL, gerçek zamanlı çapraz-DB replikasyon YOK, (c) çakışma = LWW + sunucu SSOT (ADR-027) + idempotent tüketici, (d) izleme/kurtarma = üstel backoff retry (max 10) → DLQ (ADR-081) + lag bütçesi alarmı + replay, (e) zamanlama = olaylar anlık relay, ETL gece penceresi, manuel CLI korunur"
kaynak: "Disk/kod kanıt taraması (2026-09-29; tek kapsam: vendor/node_modules/test hariç 248 PHP + .ai/.sql/mysql 19 SQL: outbox 0 · replica/gtid/binlog 0 · CREATE EVENT/ON SCHEDULE/ETL/schedule 0 · cron 4 (yalnız coremusic_logs.sql yorumu) · .github/workflows schedule 0/2 · dispatch(new = 0 · shared/src/Events = 1 dispatcher + 9 domain + 3 integration, testte 2 örneklenme · beginTransaction 3 çağrı sitesi · cross-DB şema referansı 2 migration · index.md:91 satırı var) + web araştırması (6 sorgu / 31 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-050: Multi-DB Sync Strategy (Çoklu-Veritabanı Senkronizasyon Stratejisi)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-09-29 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL — §7.1 · bağlayıcı şartlar §5.4)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-050-multi-db-sync-strategy`
> **İlgili kararlar:** [[ADR-081-multi-provider-data-sync]] (outbox + WAL + DLQ + lag bütçesi — karar (b)/(d)/(e) zemini) · [[ADR-040-database-authority]] (18 DB yetki matrisi + tek yazar + cross-DB FK politikası — karar (a) zemini) · [[ADR-039-7-service-platform-architecture]] (11 servis + PSR-14 + servis↔servis HTTP yasağı — karar (a)) · [[ADR-027-dual-mode-storage-strategy]] (LWW + sunucu SSOT — karar (c)) · [[ADR-003-multi-db-bcnf]] (çoklu-DB BCNF + "DB arası FK YOK" kuralı) · [[ADR-033-sql-normalization-strategy]] (şema/normalizasyon denetimi) · [[ADR-049-startup-prompt-loader]] (önceki ADR — biçim/kanıt etiketi referansı) · [[../index.md]] · [[../../brain.md]]
> **Ad gerekçesi:** slug `ADR-050-multi-db-sync-strategy` **disk kanıtından** alınmıştır — `.ai/.decisions/index.md:91` bu adı taşır (`| [[../brain.md]] ADR-050-multi-db-sync-strategy | Multi-DB Sync Strategy | Database |`); bu dosya o boşluğu doldurur. **Numara istisnası:** güncelleme kuralı yeni ADR'lerin ADR-088+ aralığından başlamasını söyler; 050 slotu `index.md:91`'da **rezerve edilmiş** olduğu için doldurulur (ADR-049'un `index.md:90` örneğiyle aynı istisna) — yeni numara tüketilmez.
> **Index durumu:** `.ai/.decisions/index.md:91` satırı **vardır** ve 88–90 (ADR-046/048/049) satırlarıyla **aynı biçimdedir**; `[[../brain.md]]` bağlantısının slug'ı doğru hedefe bağlayan düzeltmesi **bir sonraki vault reset'ine ertelenmiştir** (bu işlemde index.md'ye dokunulmadı — report-only).
> **Frozen notu:** ADR-001–037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.
> **Kapanan bayraklar (rapor):** [[ADR-040-database-authority]] `:129,360` ile [[ADR-041-database-normalization-supplementary]] `:362` "ADR-050 dosyası YOK → ⚠️ VERIFICATION REQUIRED" kaydını taşıyordu; bu dosyanın yazımıyla kayıt **gerçekleşti**. Bu üç satır bu işlemde **düzeltilmedi** (report-only — SRP + In-Place Refactoring; reset'te gözden geçirilir).

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıt — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **STUB** = kod var ama gövde boş · **ÇELİŞKİ** = iki kayıt uyuşmuyor · **KAPSAM FARKI** = ölçüm kapsamı/tarih farklılığı (çelişki değil). Ortak ölçüm kapsamı: `vendor`/`node_modules`/`tests` hariç **248 PHP dosyası** + `.ai/.sql/mysql/` **19 SQL dosyası** (18 `coremusic_*` + `media_catalog.sql` — ADR-092 Faz 2 eki, KAPSAM NOTU).

#### A) Outbox — şemada ve kodda 0 (PLANNED)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `.ai/.sql/mysql/*.sql` (19 dosya) | `outbox` **0 isabet** — `outbox_events` tablosu şemada YOK | **PLANNED** (ADR-081 kararı yazılı, şema ayağı yok) |
| 248 PHP (vendor/test hariç) | `outbox` **0 isabet** | **PLANNED** |
| [[ADR-081-multi-provider-data-sync]] `:92` | "her iş yazımı, MySQL'in **aynı** yerel transaksiyonu içinde `outbox_events` satırıyla birlikte atılır; ayrı bir async yayıncı (relay) outbox'ı okur" | DOĞRULANDI (karar metni var — uygulanmamış) |

#### B) Batch / cron / zamanlayıcı — kodda 0, yalnız niyet yorumları

| Kanıt | İçerik | Etiket |
|---|---|---|
| 248 PHP + 19 SQL | `CREATE EVENT` **0** · `ON SCHEDULE` **0** · `\bETL\b` **0** · `\bschedule\b` **0** · `\bcron\b` **4** — dördü de `coremusic_logs.sql:411,441,578,626` **yorum satırları** ("Günlük cron job ile doldurulur") | **PLANNED** (zamanlanmış iş **0**; yorum = niyet) |
| `.github/workflows/` (2 dosya: `ci.yml`, `secret-scan.yml`) | `schedule:` / `cron:` trigger **0/2** | **PLANNED** (CI'da zamanlayıcı yok) |
| Manuel CLI (çalıştırılabilir, zamanlanmamış) | `home.coremusic.net/scripts/import-music-folder.php` · `media.coremusic.net/bin/scan.php`, `bin/ingest.php`, `bin/audit.php` | **IMPLEMENTED** (manuel tetikleme) |

#### C) Replikasyon / read-replica — kodda 0, vault'ta PLANNED

| Kanıt | İçerik | Etiket |
|---|---|---|
| 248 PHP + 19 SQL | `replica` **0** · `gtid` **0** · `binlog` **0** | kodda beklenti **0** |
| `.ai/architecture/k5-veri-yonetimi/backup-strategy.md:330,339` | `SHOW SLAVE STATUS` · `Executed_Gtid_Set` (yedekleme/DR betiği) | vault dokümanı (kod değil) |
| `.ai/architecture/k5-veri-yonetimi/mysql-18-database.md:318-336` | `replication: primary/...` konfigürasyon şablonu | vault dokümanı (uygulanmamış) |
| `.ai/.templates/adr/adr-database-template.md:248` | `Replica | 1 read-replica (PLANNED) | ... | replica gecikme < 5s` | **PLANNED** (şablonda hedef) |

#### D) Olay altyapısı, transaksiyonlar, çapraz-DB referanslar

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Events/` | 1 dispatcher (`EventDispatcher.php`) + 1 trait (`StoppableEventTrait.php`) + **9 domain** event (GenderSet, MediaAccessed, MusicAdded, MusicPlayed, PasswordResetRequested, PlaylistCreated, UserLoggedIn, UserLoggedOut, UserRegistered) + **3 integration** event (AuthValidated, Notification, SessionCreated) | **IMPLEMENTED** (altyapı) |
| `dispatch(new ` (248 PHP) | **0 isabet** — üretim kodunda olay **yayınlanmıyor** | **PLANNED** (yayınlama yok) |
| `new *Event(` (248 PHP) | üretim **0**; yalnız `shared/tests/Events/DomainEventTest.php:39,49` (testte 2 örneklenme, `shared/tests` genelinde 11 `Event(` çağrısı) | **IMPLEMENTED (yalnız test)** |
| `beginTransaction` | 3 üretim **çağrı sitesi**: `media.coremusic.net/src/Media/CatalogWriter.php:183` · `auth.coremusic.net/include/Repository/UserRepository.php:111` · `home.coremusic.net/scripts/import-music-folder.php:241` (+ `shared/src/Database/DatabaseManager.php:49,51` implementasyon, `IDatabaseManager.php:10` arayüz) | **IMPLEMENTED** (yerel transaksiyon var) |
| Çapraz-DB şema referansı (gerçek kod) | 2 migration dosyası: `shared/database/migrations/oauth_states_migration.php:31` (`coremusic_user.users`) · `oauth_connections_migration.php:41` (`coremusic_user.users`) — 3. isabet yalnız docblock yorumu (`import-music-folder.php:12`) | **IMPLEMENTED** (istisna; ADR-040 matrisine tabi) |

#### E) Vault kayıtları (bu ADR'den önceki iddialar)

| Kayıt | İçerik | Etiket |
|---|---|---|
| `.ai/.decisions/index.md:91` | `| [[../brain.md]] ADR-050-multi-db-sync-strategy | Multi-DB Sync Strategy | Database |` | DOĞRULANDI (satır VAR — bu dosya boşluğu doldurur) |
| [[ADR-040-database-authority]] `:129,360` | "ADR-050 ... dosya **YOK** → ⚠️ VERIFICATION REQUIRED" | Bu dosya ile **kapanır** (satırlar düzenlenmedi — report-only) |
| [[ADR-041-database-normalization-supplementary]] `:362` | "ADR-050 ... dosya YOK → ⚠️ VERIFICATION REQUIRED (bu ADR'de referanslanmaz)" | Aynı — report-only |
| [[ADR-081-multi-provider-data-sync]] `:30` | "ADR-003 ... ADR-040 ... **ADR-050 multi-db sync stratejisi** ... kayıtlar [[../index]] §3-§4'te" (kavramsal referans — dosya değil indeks kaydı) | İddia artık **doğrulandı** |
| [[ADR-040-database-authority]] `:36-40,88,98` | 82 FK = **54 DB-içi + 28 cross-DB**; **15** yorum satırı "cross-database FK not supported in MySQL"; C1: resmi MySQL 8.4 Ref §15.1.20.5 "foreign_key_checks disabled → It is permitted to drop a database that contains tables with foreign keys that are referenced by tables outside the database" notu sunucu-içi cross-DB referansını **ima** ediyor → ⚠️ VERIFICATION REQUIRED (canlı MySQL'de test edilmedi) | DOĞRULANDI (bu ADR o teknik iddiaya **bağlanmaz**; politika ADR-003/040'tır) |

> **Bulgu özeti:** CoreMusic'te **çapraz-DB senkronizasyon altyapısının hiçbiri yok** — outbox tablosu 0, zamanlanmış iş 0, replikasyon 0, olay yayımı üretimde 0 (yalnız test). Senkronizasyon bugün ya **manuel CLI** ile ya da **hiç** yapılır. Bu ADR bu boşluğu karar (a)–(e) kalemleriyle sabitler; uygulama adımları §5.1'dedir ve ADR-081'in CoreMusic şema ayağını açar.

### §1.2 Sorun Tanımı (Problem)

Beş sorun üst üste biniyor. **(1) Kapsam belirsiz:** 18+1 veritabanı var (ADR-040 yetki matrisi) ama "hangi veri hangi yolla paylaşılır" sorusunun tek kaydı yok — her geliştirici kendi yolunu seçebilir. **(2) Tip seçilmedi:** olay tabanlı senkronizasyon mu, batch ETL mi, gerçek zamanlı replikasyon mu; ADR-081 outbox'ı kararlaştırmış ama ADR-050 slotu (senkronizasyon **stratejisi**) boş olduğu için "senkronizasyon nedir" sorusu cevapsız. **(3) Çakışma kuralı yok:** ADR-027 LWW + sunucu SSOT'u depolama için yazıyor; veritabanları arası projeksiyon güncellemelerinde hangi yazının kazanacağı, çift uygulamanın (at-least-once) nasıl emileceği yazılmamış. **(4) İzleme/kurtarma boş:** retry/DLQ/lag bütçesi ADR-081'de var ama CoreMusic'e özgü eşikler, metrikler ve akış (tespit → retry → DLQ → replay) bu ADR'de sabitlenmedi. **(5) Zamanlama politikası yok:** olaylar anlık mı, ETL hangi pencerede çalışır, mevcut manuel CLI'ların yeri nedir — belirsiz. Ek çelişki: şemada "cron job ile doldurulur" yorumları var ama cron implementasyonu 0 (§1.1-B) — yani **niyet kayıtlı, iş yok**.

### §1.3 Web'den Araştırma Raporu & Sonuçları

Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (birincil/resmî kaynak öncelikli; her iddia çapraz kaynaklı). Tarih: 2026-09-29.

| Alan | Değer |
|------|-------|
| Web Search **Query** | 6 sorgu: (1) "cross-database synchronization patterns shared database microservices outbox pattern comparison" (2) "transactional outbox pattern pitfalls polling publisher idempotency dead letter queue production" (3) "CDC vs batch ETL replication data synchronization trade-offs latency window scheduled jobs" (4) "eventual consistency monitoring replication lag SLA dead letter queue retry recovery patterns distributed systems" (5) "MySQL 8 cross-database foreign key constraint not supported same schema requirement" (6) "last-writer-wins conflict resolution server as source of truth idempotency event ordering pitfalls" |
| Web Search **Konusu** | Çoklu-veritabanı senkronizasyon kalıpları (database-per-service vs shared DB, dual-write problemi), transactional outbox'un operasyonel tuzakları (polling relay, CDC relay, idempotent tüketici, relay izleme), CDC ↔ batch ETL takası (pencere gecikmesi vs anlık değişim yakalama), eventual consistency izleme (lag bütçesi, DLQ, retry/replay), MySQL 8 çapraz-DB FK'nın statüsü ve motor kısıtları, LWW çakışma çözümü + sunucu SSOT + idempotency |
| Web Search **Bağlam** | Karar disk gerçeğiyle yüzleşiyor: outbox 0, zamanlanmış iş 0, replikasyon 0, olay yayımı üretimde 0 (§1.1-A/B/C/D). Araştırma 2026-09-29'da yapıldı; 6 sorgu / 31 adlandırılmış kaynak; resmi MySQL 8.4 dokümanı bizzat okundu (§1.1-E C1 teyidi). CoreMusic'e taşınan sonuçlar §2 (a)–(e) kalemlerine bağlanmıştır |
| Web Search **Kısa Açıklama** | Senkronizasyon üç ailede toplanıyor: **olay yayımı** (outbox = veri + olay aynı yerel transaksiyonda → dual-write kapanır, 2PC gerekmez, at-least-once), **batch ETL** (planlı pencere; dakika–gün gecikmesi kabul edilir, rapor/analitik için yeterli) ve **replikasyon/CDC** (log tabanlı anlık akış). Dual-write (DB commit + mesaj gönderimi ayrımı) tek başına **kanıtlanmış hiçbir çözümle** kapanmıyor; outbox bunun standardı. DLQ, tekrarlı başarısız mesajı ana hattan **izole eder**; lag ölçülmeden eventual consistency "umarak" yürütülür. MySQL 8.4 FK kısıtları: aynı motor, geçici olmayan tablo, benzer tipler — resmi doküman çapraz-DB'yi açıkça yasaklamıyor, sınır **aynı sunucu instance'ı**; çapraz-instance FK yok. LWW ucuzdur ama **sessiz veri yer** (saat senkronuna bağlı) — sunucu SSOT + tek yazar ile çerçevelenmezse güvensizdir |
| Web Search **Uzun Açıklama** | Kaynaklar altı eksende buluşuyor. (i) **Senkronizasyon kalıbı:** microservices.io "Database per service" shared DB'yi anti-pattern ilan eder; Confluent dual-write'ı tanımlar (commit ile yayın ayrımı = tutarsızlık penceresi) ve transactional outbox'ın bu pencereyi atomiklikle kapattığını; Conduktor outbox'ın CDC ile beslenmesini (Debezium relay) anlatır. (ii) **Outbox operasyonel gerçekleri:** appscale.blog polling relay + CDC + tüketici idempotency + **operasyonel izleme** üçlüsünü şart koşar; systemoverflow "relay defalarca çökse bile commit edilen olay sonunda yayılır" (at-least-once); developers.dev relay süreç-gecikme izlemesini "SRE odağı" diye niteleyerek **izlenmeyen outbox'ın üretimde kör nokta** olduğunu vurgular; rafeuddaraj.me try-catch/retry'ın büyük ölçekte neden yetmediğini, consumer-side dedup'un zorunlu olduğunu gösterir. (iii) **ETL ↔ CDC:** molo17 batch'in "dakika–gün" gecikme penceresini, CDC'nin commit edilen her değişimi yakaladığını; streamkap/Databricks toplu çekim ile anlık change yakalamanın farklı operasyonel maliyet taşıdığını yazıyor — yani **ikisi aynı iş için değil, farklı gecikme iştahı için** var. (iv) **İzleme/kurtarma:** Temporal DLQ'nun "işlenemeyen mesajı tıkanmadan ayıracağını"; ByteByteGo DLQ + retry ile sağlıklı trafiği korumayı; AWS DLQ'yu "yeniden işleme hazır tutma alanı" olarak tanımlar; Aerospike eventual consistency'nin "yeterli zaman + yeni güncelleme yok" sözleşmesini hatırlatır — ölçüm yoksa sözleşme ihlal edilir. (v) **MySQL FK:** resmi 8.4 Ref §15.1.20.5 kısıtları listeler (aynı motor, temporary olmamak, benzer tipler, partition'lı InnoDB FK'siz, `foreign_key_checks` kapatıldığında bile tip/eksik indeks hataları sürer) ve "başka veritabanınca referans edilen tabloların bulunduğu DB'nin düşürülmesine izin verilir" notuyla sunucu-içi çapraz-DB referansının **var olduğunu** ima eder — StackOverflow 4452132 ve knex #5056 topluluk yanıtları da sınırın motor/instance olduğunu doğrular (knex: "cross-database foreign keys are constrained by the database engine, not Knex"). Yani şemadaki 15 "desteklemiyor" yorumu **teknik olarak tartışmalı**, ama CoreMusic politikası (ADR-003/040) yine de yasak; senkronizasyon FK'ye dayandırılmaz. (vi) **LWW:** Microsoft Learn peer-to-peer replikasyonda LWW tespit+çözümü konfigürasyonunu sunar (LWW meşru bir stratejidir); buna karşılık Yalovoy "LWW silently eats data" ve programmingappliedai "LWW keyfi güncellemeleri atar, saat senkronuna bağlı" uyarısı, LWW'yi **tek yazar + sunucu SSOT + idempotency** olmadan kullanmanın veri kaybı ürettiğini gösterir; Ably CRDT'leri alternatif olarak işaret eder (CoreMusic'te kapsam dışı — CRDT getirilmez). |
| Web Search **Paragraf Veri Uzun** | CoreMusic'in senkronizasyon sorunu "senkron yok" derinliğinde değil, **"üç soru cevapsız"** derinliğinde: hangi veri (kapsam), hangi tip (yol), nasıl kurtuluruz (dayanıklılık). Web bu boşluğa dört dersle giriyor. Birincisi **dual-write bir mimari karar değil, bir hatadır**: commit ile yayın arasındaki pencere er ya geç veri kaybı üretir; outbox bu pencereyi "veri + olay tek transaksiyon" ile kapatır ve CoreMusic'in 3 gerçek transaksiyon sitesi (§1.1-D) zaten bu atomikliği taşıyacak yerel transaction altyapısına sahiptir — yani outbox için yeni bir dağıtık mimari gerekmez, mevcut `DatabaseManager` yeterlidir. İkincisi **üç tip birbirinin yerine konmaz**: outbox olayların **anlık** yoludur, ETL **penceresel** yoludur (rapor/analitik), replikasyon **devamlı** yoludur; CoreMusic'in 0 zamanlanmış iş + 0 replikasyon kanıtıyla (§1.1-B/C) devamlı yol sıfırdan kurulmak zorunda değildir — bu nedenle karar (b) "replikasyon YOK" der ve ETL'yi ikincil tutar. Üçüncüsü **izlemesiz eventual consistency kumarıdır**: DLQ retry'yi kurtarır ama lag bütçesi olmadan sistem "çalışıyor" görünürken veri dakikalarca/eski kalır; ADR-081'in lag bütçesi fikri bu ADR'de CoreMusic metriklerine (outbox derinliği, ETL pencere süresi, DLQ sayısı, en eski iş yaşı) bağlanır. Dördüncüsü **çakışma kuralı sessiz olamaz**: LWW meşrudur ama tek başına veri yer (Medium, programmingappliedai); ADR-040'ın tek-yazar matrisi ve ADR-027'nin sunucu SSOT'su olmadan LWW yalnızca kazayı gizler — bu yüzden karar (c) LWW'yi **ihlal sensörüyle** birlikte koşullandırır: çakışma nadiren olur (tek yazar), olduysa önce politika ihlali sayılır, değilse LWW + sunucu önceliği uygulanır ve olay idempotent tüketilir. Sonuç olarak web, "hepsini gerçek zamanlı bağlayalım" reçetesini reddediyor; karar **olay birincil + ETL ikincil + replikasyon yok + izlemeli eventual** ekseninde kapanıyor. |
| Web Search **Sonucu** | 6 sorgu / **31 adlandırılmış kaynak**: (1) microservices.io — Pattern: Database per service (shared DB anti-pattern), (2) Confluent — Understanding the Dual-Write Problem and Its Solutions (outbox atomikliği), (3) Conduktor — CDC for Microservices (outbox + Debezium), (4) GeeksforGeeks — Database Per Service Pattern, (5) dev.to/Gajus — Designing microservices with shared databases, (6) appscale.blog — Microservices Outbox Pattern (polling relay, CDC, idempotency, Saga, operational monitoring), (7) singhajit.com — Transactional Outbox Pattern: Never Lose an Event Again (polling + CDC/Kafka), (8) systemoverflow — Transactional Outbox for Reliable Publishing (at-least-once, relay crash), (9) developers.dev — Outbox Pattern (relay/latency monitoring, SRE odağı), (10) rafeuddaraj.me — Outbox + Idempotency deep dive (retry tek başına yetmez), (11) CodeWithVenu — Transactional Outbox Pattern (dual-write standardı), (12) molo17 — Batch ETL vs real-time replication (dakika–gün pencere), (13) streamkap — CDC vs ETL (toplu çekim vs her değişim), (14) Databricks — What is Change Data Capture?, (15) Striim — CDC Tools 2025 (takaslar), (16) StackSync — Master Real-Time CDC (operasyonel darboğaz), (17) Temporal — Error handling in distributed systems (DLQ ayırır), (18) ByteByteGo — Eventual Consistency in Practice (DLQ + retry), (19) AWS — Dead-Letter Queue (DLQ) Explained, (20) Kestra — Dead-Letter Queue Pattern, (21) Aerospike — What is eventual consistency?, (22) **MySQL 8.4 Ref §15.1.20.5 FOREIGN KEY Constraints (resmi, sayfa bizzat okundu)**, (23) StackOverflow 4452132 — Add FK relationship between two Databases, (24) GitHub knex #5056 — cross-DB FK engine'e bağlı, (25) dba.stackexchange — Cross-schema Foreign Keys, (26) HN — Things that don't work well with MySQL's FOREIGN KEY, (27) Microsoft Learn — Configure Last Writer Conflict Detection & Resolution, (28) oneuptime — How to Implement Last-Write-Wins, (29) **Medium/Yalovoy — Why Last-Writer-Wins Silently Eats Data (OLUMSUZ bulgu)**, (30) Ably — CRDTs solve distributed data consistency challenges, (31) programmingappliedai — write conflict resolution (LWW veri kaybı + saat senkronu). **Olumsuz/negatif bulgular:** LWW sessiz veri yer ve saate bağımlıdır; izlenmeyen outbox/relay kör noktadır; batch ETL dakika–gün gecikme üretir; şemadaki 15 "cross-DB FK desteklemiyor" yorumu resmi dokümanla **örtüşmüyor** (doküman sunucu-içi çapraz-DB referansını ima eder — ADR-040 C1 ile aynı sonuç, bu ADR'de raporlanır); `foreign_key_checks` kapatma tip/eksik indeks hatalarını **gidermez**. |
| Web Search **Alınan Karar** | Karar a-e kalemleri bu bulgularla sabitlendi: (a) **Kapsam + kanal:** senkronize edilecek veri ADR-040 yetki matrisindeki (18+1 DB) sahiplik sınırlarıdır; taşıma kanalı yalnız **outbox olayı** (ADR-081) veya **rapor/analitik ETL**tir; servis↔servis HTTP ile veri çekme yasaktır (ADR-039); geri yazım yok — her tablonun tek yazarı vardır (ADR-040) (Confluent/Conduktor dual-write çözümü + microservices.io database-per-service). (b) **Tip:** olay/outbox **birincil** (anlık, at-least-once), batch ETL **ikincil** (penceresel rapor/analitik — molo17/streamkap gecikme iştahı ayrımı), gerçek zamanlı çapraz-DB replikasyon **YOK** (kodda 0 kanıt, sıfırdan kurulum maliyeti; FK'ye dayalı bütünlük yok — ADR-003/040 + MySQL §15.1.20.5 instance sınırı). (c) **Çakışma:** LWW + sunucu SSOT (ADR-027) + **idempotent tüketici** (benzersiz olay kimliği, at-least-once dedup); çakışma nadiren olmalı (tek yazar), sıklıkla olursa politika ihlali sayılır (Microsoft Learn LWW meşruiyeti + Yalovoy/programmingappliedai veri-kayıp uyarısı + rafeuddaraj.me dedup zorunluluğu). (d) **İzleme/kurtarma:** üstel backoff retry (1 sn → 5 dk, max 10) → `FAILED` → **DLQ** (ADR-081 `:127`), **lag bütçesi** ölçülür ve aşılırsa alarm (ADR-081 `:82`); metrikler: outbox derinliği, en eski iş yaşı, ETL pencere süresi, DLQ sayısı; akış = tespit → retry → DLQ → manuel inceleme → replay (Temporal/AWS/ByteByteGo DLQ + developers.dev relay izleme). (e) **Zamanlama:** olaylar **anlık** (relay kısa döngü), ETL **gece penceresi** (şemadaki "gece yarısı cron" niyetiyle hizalı — ama zamanlayıcı 0, §1.1-B), mevcut manuel CLI'lar korunur; `.github/workflows`'a `schedule` **konmaz** (CI ≠ veri zamanlayıcısı). Harici CDC/replikasyon servisi (Debezium/replica tarzı) **getirilmez** — CoreMusic tek instance PHP+MySQL'dir, relay ADR-081'deki uygulama-içi yayıncı ile karşılanır (YAGNI). |
| Web Search **Sonuç** | Araştırma kararı **destekledi ve üç şart netleştirdi**: (1) **tek reçete yok** — "her şeyi anlık bağlayalım" da "yalnız gece batch" da tek başına yanlış; doğru kalıp **olay birincil + ETL ikincil + replikasyon yok**; (2) **izleme zorunlu** — DLQ ve lag bütçesi olmadan outbox, kör nokta haline gelir (developers.dev + ADR-081 `:82` aynı sonuca varır); (3) **LWW tek başına yasak** — sunucu SSOT + tek yazar + idempotency üçlüsü olmadan LWW sessiz veri kaybıdır (Yalovoy). Ek iki doğrulama: MySQL §15.1.20.5 resmi okuması ADR-040 C1'i teyit etti (teknik iddia ⚠️ — politika ADR-003/040'tır, bu ADR'ye bağlanmaz); outbox şemasız çalışmadığından bu karar **ADR-081'in CoreMusic şema ayağını** (§5.1 adım 2) açar. Harici servis (Debezium/Kafka/managed CDC) **getirilmedi** — CoreMusic ölçekte uygulama-içi relay yeterlidir (Aerospike/Ably ölçek önerileri kapsam dışı). |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-040-database-authority]] yetki matrisi + tek yazar | Senkronizasyon **kapsamı** bu matristen çıkar; her tablonun tek yazarı vardır — senkron akış **geri yazım yapamaz**; çapraz-DB FK politikası (varsayılan yasak + 28 istisna) bu ADR'ye bağlanmaz, politika ADR-003/040'ın tekelindedir |
| [[ADR-081-multi-provider-data-sync]] outbox + DLQ + lag bütçesi | Sync tipi (b) ve izleme (d) bu ADR'nin **öncülüdür**; `outbox_events`, retry (max 10), `FAILED` → DLQ davranışları ADR-081'den **ayrı yeniden tanımlanmaz** (SRP) — bu ADR yalnız CoreMusic uygulama eşiğini yazar |
| [[ADR-039-7-service-platform-architecture]] servis sınırları | Servis↔servis HTTP **yasak**; servisler arası tek kanal PSR-14 event — sync kanalı bu sınırın dışına **çıkamaz** |
| [[ADR-027-dual-mode-storage-strategy]] LWW + sunucu SSOT | Çakışma çözümü (c) bu şemanın dışına çıkmaz; CRDT/replika-başına-lider **getirilmez** (kapsam dışı — §3-5) |
| ADR-001–037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz |
| In-Place Refactoring | Dosya adları onaysız **değiştirilmez**; `index.md:91` slug wiki-link düzeltmesi bu işlemde **eklenmez** (reset'e erteli — report-only) |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs` (log.md = append-only); PowerShell write cmdlet'leri yasak |
| REDACTED | Token, anahtar, API key, connection string, kullanıcı verisi bu ADR'ye yazılmaz (MySQL replikasyon şablonundaki host/parola alanları **alıntılanmaz**) |
| Hallucination disiplini | Diskte olmayan hedefe wiki-link **yazılmaz**; kanıtsız satır `⚠️ VERIFICATION REQUIRED` ile işaretlenir |
| Tek instance gerçekliği | CoreMusic tek PHP+MySQL instance'ında çalışır; çapraz-instance/çok-ortam senkronu **kapsam dışı** |

---

## §2 Karar (Decision)

CoreMusic, 18+1 veritabanı arasındaki senkronizasyonu **olay-birincil, ETL-ikincil, replikasyonsuz** bir stratejiyle yürütür: kapsam ADR-040 yetki matrisiyle sınırlıdır, taşıma outbox olayı veya rapor ETL'isidir, çakışmalar sunucu SSOT'lu LWW ile ve idempotent tüketimle çözülür, dayanıklılık DLQ + lag bütçesiyle izlenir, zamanlama olaylarda anlık / ETL'de gecedir.

### §2.1 (a) Senkronize Edilen Veri ve Kanal (API)

| Kalem | Karar |
|---|---|
| **Kapsam** | Yalnız ADR-040 yetki matrisindeki DB'lerdeki **sahiplik sınırları içindeki** veriler; sahiplik dışı veri **senkronize edilmez, okunur** (tek yazar prensibi) |
| **Kanal 1 — olay** | `outbox_events` üzerinden **olay yayımı** (ADR-081): üretici işini + olayı aynı yerel transaksiyonda yazar (`beginTransaction` altyapısı §1.1-D'de hazır), relay yayınlar |
| **Kanal 2 — ETL okuma** | Rapor/analitik için **okunacak verinin tanımı** (tablolar + pencere filtresi) — API değil, tanımlı sorgu |
| **Yasak kanal** | Servis↔servis HTTP ile veri çekme (ADR-039); olay akışının tersine (geri) yazım; başka DB'ye doğrudan `UPDATE/INSERT` |
| **Olay sözleşmesi** | Her olayda: benzersiz `event_id` (idempotency), `occurred_at`, `source_db`, `schema_version` — payload REDACTED (içerik veri değil, olay kimliği + şema) |
| **API ne demek?** | Bu ADR'de "API" = **olay şeması + ETL okuma tanımı**dır; HTTP senkron endpoint'i **yoktur** |

### §2.2 (b) Sync Tipi — Olay/Outbox Birincil, Batch ETL İkincil, Replikasyon Yok

| Tip | Statü | Kullanım alanı | Gerekçe |
|---|---|---|---|
| **Olay / outbox (ADR-081)** | **BİRİNCİL** | anlık bildirim, servisler arası olay, durum yayını | Dual-write'ı atomik kapatır (Confluent/Conduktor); at-least-once; mevcut transaksiyon altyapısı yeterli |
| **Batch ETL** | **İKİNCİL** | rapor, analitik, tarihsel yüklemeler (gece penceresi) | Gecikme iştahı yüksek işler; dakika–gün gecikmesi kabul (molo17/streamkap); zamanlayıcı bugün 0 → §5.1 adım 4 |
| **Gerçek zamanlı çapraz-DB replikasyon** | **YOK** | — | Kodda 0 kanıt (§1.1-C), sıfırdan kurulum + operasyon maliyeti; FK'ye dayalı bütünlük yok (ADR-003/040; MySQL §15.1.20.5 instance sınırı); anlık ihtiyacı outbox zaten karşılar |
| **Harici CDC/replikasyon servisi (Debezium/replica)** | **YOK (kapsam dışı)** | — | Tek instance ölçekte gereksiz bağımlılık (YAGNI); ADR-081 relay'ı yeterli |

### §2.3 (c) Çakışma Çözümü — LWW + Sunucu SSOT + Idempotent Tüketim

| Kural | Ayrıntı |
|---|---|
| **Varsayılan** | **LWW** (son yazan kazanır) + **sunucu SSOT** (ADR-027): projeksiyon tablolarında geçerli değer her zaman sunucu kaydıdır |
| **Önkoşul** | Tek yazar (ADR-040) — çakışma **nadir** olmalıdır; birden fazla yazıcı aynı satırı yazıyorsa bu LWW sorunu değil **politika ihlalidir** → MO'ya eskalasyon (§2.4 akış) |
| **Tüketim** | **Idempotent**: aynı `event_id` ikinci kez uygulanmaz (unique constraint/dedup) — at-least-once teslimatın çift yazımı emilir |
| **Saat** | LWW `occurred_at` + sunucu saatine göre; istemci saati **kabul edilmez** (Yalovoy/programmingappliedai uyarısı) |
| **Reddedilen** | CRDT, vektör saati, uzlaştırıcı servis — kapsam dışı (§3-5) |

### §2.4 (d) İzleme ve Kurtarma (Retry → DLQ → Replay)

| Katman | Karar |
|---|---|
| **Retry** | Üstel backoff 1 sn → 2 sn → … → 5 dk, `attempts` artar; **max 10 deneme** (ADR-081 `:127` ile aynı) |
| **DLQ** | 10 denemeden sonra satır `FAILED` → **dead-letter** kuyruğu; ana akış **tıkanmaz** (Temporal/AWS/ByteByteGo) |
| **Replay** | DLQ kaydı manuel inceleme sonrası **yeniden oynatılabilir** (replay); inceleme olmadan otomatik silme **yasak** (kanıt kaybı) |
| **Metrikler (CoreMusic eşikleri)** | ① outbox derinliği (işlenmemiş satır) ② en eski iş yaşı = **lag** ③ ETL pencere süresi ④ DLQ/adet ⑤ retry başarı oranı |
| **Lag bütçesi** | Kritik akış **≤ 60 sn**, rapor/analitik **≤ 10 dk** (ADR-081 bütçe ailesiyle hizalı); bütçe aşımı → **alarm** (MO/DevOps) — "umarak" eventual yasak (ADR-081 `:82`) |
| **Başarısızlık akışı** | tespit (metrik/eşik) → retry (üstel) → DLQ (`FAILED`) → manuel inceleme → replay veya düzelt-üret → `log.md` append |

### §2.5 (e) Zamanlama Politikası

| İş | Zamanlama | Kanıt/durum |
|---|---|---|
| **Olay yayımı (relay)** | **Anlık** — kısa döngü (saniyeler) ile outbox taranır | §5.1 adım 3 (bugün 0) |
| **Rapor/analitik ETL** | **Gece penceresi** (düşük trafik saati; şemadaki "gece yarısı cron job" niyetiyle hizalı — `coremusic_logs.sql:411,441`) | §5.1 adım 4 (bugün 0 — zamanlayıcı kurdulacak) |
| **Manuel CLI** | Değişmez: `import-music-folder.php`, `media/bin/{scan,ingest,audit}.php` elle çalıştırılır | IMPLEMENTED — korunur, otomatikleştirilmez (bu ADR'nin işi değil) |
| **CI workflow schedule** | `.github/workflows`'a `schedule` **konmaz** | CI ≠ veri zamanlayıcısı (workflow 0/2 kanıtı) |
| **Retry/DLQ işleri** | Anlık relay içindedir (ayrı cron değil) | §2.4 |

### §2.6 Neden Bu Seçenek?

Zemin zaten dört yerde hazır ve tek yöne işaret ediyor: **transaksiyon altyapısı var** (3 çağrı sitesi — outbox bunun üzerine oturur, yeni dağıtık gereksinim yok), **olay sınıfları var** ama **yayım yok** (üretimde 0), **zamanlayıcı yok** (0), **replikasyon yok** (0). Yani strateji, mevcut durumun **en küçük tamamlayıcısıdır**: outbox = mevcut `beginTransaction` + mevcut event sınıflarının bağlanması; ETL = mevcut manuel CLI mantığının pencereye alınması; replikasyon = sıfırdan en pahalı yol olduğu ve anlık ihtiyacı outbox'ın zaten karşıladığı için reddedilir. Çakışmada LWW seçilir çünkü ADR-040 tek-yazar matrisi çakışmayı nadir kılar; ama LWW tek başına sessiz veri yediği için (§1.3) sunucu SSOT + idempotency + ihlal sensörü ile koşullanır. DLQ/lag bütçesi seçilir çünkü izlenmeyen eventual consistency, veri kaybını "çalışıyor" maskesiyle sakar (developers.dev + ADR-081 `:82`).

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Status quo (hiç senkron yok / yalnız manuel CLI)** | Sıfır iş, sıfır risk | §1.2'deki beş sorun çözülmez; "hangi veri paylaşılır" cevapsız kalır; niyet yorumları (cron) gerçeklikle çelişmeye devam eder | Bu ADR'nin tam gerekçesi — boşluk bırakılamaz |
| 2 | **Gerçek zamanlı çapraz-DB replikasyon** (MySQL replica/binlog ile devamlı tutma) | Anlık tutarlılık hissi, okuma ölçeği | Kodda **0** kanıt (§1.1-C) → sıfırdan kurulum + operasyon; tek instance'ta anlamlı değil; FK/şema sürümü kırılganlığı; outbox zaten anlık ihtiyacı karşılar | §2.2: maliyet/ölçek uyumsuz; ADR-081 relay yolu tercih edilir; vault'taki read-replica hedefi **PLANNED** olarak kalır (bu ADR'yi bekler, bu ADR onu bağlamaz) |
| 3 | **2PC / XA dağıtık transaksiyon** | Güçlü atomiklik | Yayılı kilit/koordinatör maliyeti; başarısızlıkta blokaj; outbox bunu **2PC olmadan** çözer (Confluent, ADR-081) | Web araştırması outbox'ı kanıtlanmış standart, 2PC'yi ağır alternatif gösterir; ADR-081 zaten kararlı |
| 4 | **Servis↔servis HTTP sync API** (pull tabanlı veri çekme) | Basit görünen istek/yanıt | ADR-039 servis↔servis HTTP **yasak**; ağ hatası hot path'e girer; çift yazım yüzeyi (dual-write) açılır | Frozen değil ama **aktif** ADR ihlali → doğrudan reddedildi (§1.4 kısıt 3) |
| 5 | **Shared database** (tüm servisler aynı DB'yi okur/yazar) | Tek şema, join kolaylığı | ADR-003/040 sahiplik + tek yazar ihlali; 18 BCNF sınırı aşılır; değişiklik tüm servisleri kırar | microservices.io anti-pattern (§1.3); ADR-040 matrisi bunu yasaklar |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Senkronizasyon tek cümlede tanımlanır:** kapsam (ADR-040 matrisi), tip (olay birincil + ETL ikincil), çakışma (LWW + SSOT), kurtarma (retry→DLQ→replay), zamanlama (anlık/Gece) — §1.2'deki beş sorunun beşi de kapanır.
- **Outbox yol haritası netleşir:** ADR-081 kararı bu ADR ile CoreMusic şema ayağına bağlanır (`outbox_events` + relay), "nereden başlıyoruz" sorusunun cevabı §5.1'dedir.
- **Mevcut altyapı yeniden kullanılır:** 3 transaksiyon sitesi + 12 event sınıfı + `EventDispatcher` zaten vardır — senkronizasyon yeni bir alt sistem değil, mevcut parçaların **bağlanmasıdır**.
- **İzlemeli eventual:** lag bütçesi + DLQ ile "umarak" tutarlılık engellenir; başarısızlık görünür ve geri oynatılabilir.
- **Yeni bağımlılık yok (ADR-001):** harici CDC/servis getirilmez; CI'a gizli zamanlayıcı eklenmez.

### 4.2 Olumsuz Sonuçlar

- **Tutarlılık gecikmeli kalır:** outbox/ETL yolu eventual'dir — okuyucular ≤60 sn / ≤10 dk eski veri görebilir (bilinçli kabul).
- **Uygulama yükü bu ADR'den sonra başlar:** karar bugün **kağıtta** tamdır, altyapı 0'dır (outbox 0, zamanlayıcı 0, relay 0) — §5.1 adımları kapanmadan "senkronizasyon var" denemez.
- **Relay kendi başına bir süreçtir:** outbox tablosu büyürse (retansiyon/temizlik gerekir) — §5.1 adım 3'te bakımı yazılmalıdır.
- **LWW nadiren veri kaybı üretir:** istemci saati kabul edilmezse dahi çakışma penceresi vardır (İstemci modu ADR-027 kapsamındaki senkronlar) — ihlal sensörüyle azaltılır, sıfırlanmaz.
- **`media_catalog.sql` kapsam notu:** 19. dosya (ADR-092 Faz 2) ADR-040'ın "18 DB" sayısına **ektir** — yetki matrisi güncellenene kadar 18+1 olarak okunur (KAPSAM FARKI, bu ADR'de düzeltilmez).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **Karar kağıtta kalır:** outbox/relay/zamanlayıcı 0 → §2 maddeleri uygulanmaz | 4 (Çok olası — zaten 0) | Yüksek (strateji etkisiz) | §5.1 adımlar 2–4 kapı adımı; debate 3 şartı (§5.3) olmadan kapanmaz |
| 2 **İkili yazım (dual-write) sızıntısı:** birisi olayı transaksiyon dışına yazar | 3 (Olası) | Yüksek (sessiz veri kaybı) | Kural: olay **yalnız** iş yazımıyla aynı transaksiyonda (ADR-081); kod incelemesi kapısı |
| 3 **Idempotency unutulursa** at-least-once çift uygulama üretir | 3 (Olası) | Orta-Yüksek (çift satır) | §2.3 `event_id` unique dedup + test (§5.1 adım 6) |
| 4 **LWW sessiz veri yer** (saat/çakışma) | 2 (Mümkün — tek yazar nadir kılıyor) | Orta | Sunucu SSOT + istemci saati reddi + çakışma = ihlal sensörü (§2.3) |
| 5 **Lag bütçesizliği:** alarm yokken sistem "çalışıyor" görünür | 3 (Olası) | Orta (eski veri) | §2.4 metrik + eşik alarmı (§5.1 adım 5); ölçülmeden adım kapanmaz |
| 6 **Vault drift:** `index.md:91` `[[../brain.md]]` biçim hatası + ADR-040/041 "dosya YOK" satırları artık stale | 4 (Çok olası — zaten mevcut) | Düşük (link/katalog kirliliği) | Report-only kayıt (§5.1 adım 8) + bir sonraki vault reset'i; bu ADR'de **düzeltilmez** |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **Outbox hiç kurulmazsa** sistem bugünkü durumunda kalır: olaylar yine de yayınlanmaz (zaten 0), ETL yok, manuel CLI çalışır — **davranış değişikliği yok** (§2.1 kanal 1 eklenmedi).
2. **Relay kapatılırsa** outbox satırları birikir (kayıp yok); tekrar açılınca arkadan işlenir — temizlik manuel ve `log.md`'ye kayıtlıdır.
3. **ETL penceresi iptal edilirse** raporlar manuel CLI ile üretilir (bugünkü davranış).
4. **DLQ/lag gözlemlenemezlik geri alınamaz:** metrik ve alarm adımları yalnız geçici pasifleştirilebilir, **kalıcı silinemez** (kanıt kaybı yasak).
5. **Debate reddederse** karar Draft/Review'a döner: (a)–(e) uygulanmaz sayılır, adımlar 2–6 durdurulur (dosya adı değişmez — In-Place Refactoring).
6. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; frozen ADR'ler (001–037) etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Kanıt envanteri:** §1.1-A/B/C/D tabloları tek kapsamda (248 PHP + 19 SQL) teyit edilir — outbox 0, zamanlayıcı 0, replikasyon 0, üretim olay yayımı 0, transaction 3, event 9+3 | Vault Steward | 0.25 gün ✅ (2026-09-29 taraması yapıldı) |
| 2 | **Outbox şeması:** `outbox_events` tablosu (ADR-081 sözleşmesi: `event_id` unique, `source_db`, `schema_version`, `occurred_at`, `attempts`, `status`) `.ai/.sql/mysql/` şemasına eklenir; **REDACTED** (payload içeriği veri taşımaz) | Data Engineer | 1 gün |
| 3 | **Relay + olay yayıımı:** iş yazımı `beginTransaction` içinde outbox satırı atar (`CatalogWriter`/`UserRepository`/import betiği örneği); relay kısa döngü tarar → `EventDispatcher` ile yayınlar; **üretim `dispatch(new …)` 0 → 1 kanıtlanır** | Backend Architect | 2 gün |
| 4 | **ETL penceresi + zamanlayıcı:** rapor/analitik ETL gece penceresinde çalışır; zamanlayıcı uygulama/sunucu tarafında (`.github/workflows`'a `schedule` **konmaz**); `coremusic_logs.sql` "gece yarısı" niyetiyle hizalı | Backend + DevOps | 1.5 gün |
| 5 | **İzleme:** metrik ①–⑤ (§2.4) + lag bütçesi alarmı (kritik ≤60 sn, rapor ≤10 dk); DLQ listeleme/raporlama | DevOps Engineer | 1 gün |
| 6 | **Test kapıları:** idempotent tüketim (`event_id` ikinci kez → 1 uygulama), retry→DLQ senaryosu, LWW + sunucu SSOT çakışma senaryosu, çapraz-DB geri yazım **0** | QA Engineer | 1 gün |
| 7 | **Debate 3 tur** tamamlanır → §7 Debate/Tech Lead satırları güncellenir (⏳ → ✅) — ✅ **tamamlandı (2026-09-29: 3 tur / 20 persona, 19/1/0 KABUL → §5.4 + §7.1)** | Vault Steward | 0.5 gün ✅ |
| 8 | **Ertelemeler (report-only):** (i) `index.md:91` slug wiki-link düzeltmesi → bir sonraki vault reset'i; (ii) ADR-040 `:129,360` + ADR-041 `:362` "ADR-050 dosya YOK" satırları → bu dosya yazıldığı için stale, reset'te gözden geçirilir (bu ADR'de düzenlenmez); (iii) `media_catalog.sql` yetki matrisi kaydı (18+1) → ADR-040 kapsamı | Vault Steward + Tech Lead | 0.2 gün |

**Toplam ≈ 7.45 gün** (adım 3 + 5 + 6 kapı — bu üçü olmadan "senkronizasyon var" denemez; adım 7 debate'i bekler).

### §5.2 Geri Dönüş Planı

1. **Adım 2 tersi:** `outbox_events` tablosu şemadan düşürülür → sistem bugünkü (outbox'sız) durumuna döner; **davranış farkı yok** (o zaten 0'dı).
2. **Adım 3 tersi:** relay tek bayrakla kapatılır → outbox satırları birikir, kayıp yok; olaylar yayılmaz (bugünkü davranış); tekrar açma arkadan işler.
3. **Adım 4 tersi:** ETL penceresi iptal → raporlar manuel CLI ile (bugünkü davranış).
4. **Adım 5 (geri alınamaz şart):** metrik/DLQ gözetimi **kaldırılamaz** — yalnız eşik gevşetilebilir; kalıcı kapatma yasak (§4.4-4).
5. **Adım 6 geri alma yoktur:** testler yalnız denetim içerir; kalıcı silme yasak (kanıt kaybı).
6. **Adım 7 debate reddederse:** karar Draft'a döner, adımlar 2–6 durdurulur (§4.4-5).
7. **Adım 8 geri alma yoktur:** ertelenen işler henüz yapılmadı (index.md/ADR-040/041'e dokunulmadı).
8. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; frozen ADR'ler (001–037) etkilenmez.

### §5.3 Debate Şartları (Kabul Koşulu — 3 şart, debate ⏳ PENDING)

| Şart | İçerik | Bağlantı |
|---|---|---|
| **1** | **Outbox kapısı:** `outbox_events` şeması + relay kurulmadan senkronizasyon "var" sayılmaz; üretim `dispatch(new …)` 0 → ≥1 kanıtlanır | §5.1 adım 2–3 · §2.1 |
| **2** | **İzleme kapısı:** metrik ①–⑤ + lag bütçe alarmı + DLQ listeleme çalışır durumda gösterilir; ölçülmeden adım kapanmaz | §2.4 · §5.1 adım 5 |
| **3** | **Idempotency + LWW testi:** `event_id` ikinci uygulamada 1 satır; çakışmada sunucu SSOT kazanır; çapraz-DB geri yazım testi 0 | §2.3 · §5.1 adım 6 |

> **Kabul koşulu:** 3 şart kapanmadan senkronizasyon stratejisi yayına alınmaz; debate **⏳ PENDING** (bu işlemde tur başlatılmadı — §7).

> **Durum güncelleme (2026-09-29):** Debate ✅ **tamamlandı** — 3 tur / 20 persona, **19 kabul / 1 çekimser / 0 red → KABUL** (kayıt §7.1). Bu tabloda 3 taslak şart debate ile **bağlayıcı** hâle geldi; bağlayıcı 3 şart **§5.4**'te maddeleştirildi. §5.3 + §5.4 şartları **bağlayıcıdır** — oynanmadan karar (a)–(e) kalemleri uygulanmış sayılmaz.

### §5.4 Debate Bağlayıcı Şartları (3 şart — ✅ KABUL, 2026-09-29)

> **Durum:** ✅ Debate **tamamlandı** (3 tur / 20 persona — Tur 1: 16 kabul/neutral + 3 uyarı · Tur 2: 4 itiraz→çözüm · Tur 3: 19 kabul / 1 çekimser / 0 red → **KABUL**; kayıt §7.1). Aşağıdaki 3 şart **bağlayıcıdır**; §5.3'teki 3 taslak şart bu maddelerde toplanır ve eşlenir. Şartlar karşılanmadan senkronizasyon stratejisi yayına alınmaz (§5.3 kabul koşulu korunur).

| # | Şart | Kapsam | Kabul ölçütü | §5.3 eşlemesi |
|---|---|---|---|---|
| **1a** | **Olay yayımı fazı — production publish + outbox şeması** | Üretim `dispatch(new …)` **0** (yalnız testte 2 — §1.1-D); `outbox_events` şeması + relay kurulmadan senkronizasyon "var" sayılmaz — üretim yayımı **0 → ≥1** kanıtlanır | §5.1 adım 2–3: `outbox_events` şemada + en az 1 üretim olay yayımı kod kanıtıyla açık | §5.3 şart 1 (outbox kapısı) |
| **1b** | **İzleme — lag metrikleri + DLQ drenaj alarmı** | İzleme **0** (§1.1-A/B): metrik ①–⑤ (§2.4) + lag bütçe alarmı (kritik ≤60 sn, rapor ≤10 dk) + **DLQ drenaj alarmı** (retry max 10 → `FAILED` → DLQ) çalışır durumda gösterilir | §5.1 adım 5: metrik + eşik alarmı + DLQ listeleme/raporlama canlı; ölçülmeden adım kapanmaz | §5.3 şart 2 (izleme kapısı) |
| **2** | **Idempotent relay + LWW ihlal sensörü testi** | relay idempotent + `event_id` ikinci uygulamada **1 satır** + LWW **ihlal sensörü** (birden fazla yazıcı = politika ihlali) test edilir; çapraz-DB geri yazım **0** | §5.1 adım 6 test seti yeşil — bu testler olmadan yayın yok | §5.3 şart 3 (idempotency + LWW testi) |
| **3** | **Gece ETL penceresi + retry max 10 → DLQ maddelesi** | ETL yalnız **gece penceresinde** çalışır (`coremusic_logs.sql:411,441` "gece yarısı" niyetiyle hizalı — §2.5); retry **max 10 → DLQ** akışı açık madde olarak §2.4 + §5.1 adım 4'te yazılır ve uygulanır | §5.1 adım 4: pencere + retry max 10 → DLQ akışı yazılı ve çalışır gösterilir | Yeni madde (debate Tur 2.4 — §5.3'te yok) |

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../AGENTS.md]] | Agent registry — §5 domain sınırları (SQL = Data Engineer), §25.3 frozen/append kuralları |
| [[../../WORKFLOW.md]] | Süreçler, fazlar |
| [[../../brain.md]] | Mimari karar özeti |
| [[../../index.md]] | Master katalog |
| [[../../keys.md]] | Keyword haritası |
| [[../../MEMORY.md]] | Session hafızası |
| [[../../log.md]] | Audit trail (append-only — bu ADR için tek satır append) |
| [[../../glossary.md]] | Terimler (outbox, ETL, DLQ, LWW, eventual consistency, idempotency) |
| [[../index.md]] | Karar dizini — **ADR-050 satırı VAR (satır 91); slug wiki-link düzeltmesi reset'e ertelendi (§5.1 adım 8-i)** |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[ADR-003-multi-db-bcnf]] | Çoklu-DB BCNF + "DB arası FK YOK" — senkron FK'ye dayandırılmaz (§2.2) |
| [[ADR-033-sql-normalization-strategy]] | Şema/normalizasyon denetimi — outbox tablosu şema eklerken denetim kapısı |
| [[ADR-027-dual-mode-storage-strategy]] | Karar (c): LWW + sunucu SSOT |
| [[ADR-039-7-service-platform-architecture]] | Karar (a): servis↔servis HTTP yasağı + PSR-14 tek kanal |
| [[ADR-040-database-authority]] | Karar (a): 18 DB yetki matrisi + tek yazar + cross-DB FK politikası (§1.1-E bayrakları) |
| [[ADR-041-database-normalization-supplementary]] | §1.1-E bayrağı: "ADR-050 dosya YOK" satırı bu dosya ile stale (report-only) |
| [[ADR-081-multi-provider-data-sync]] | Karar (b)/(d)/(e) öncülü: outbox + retry max 10 + DLQ + lag bütçesi (19 kaynak) |
| [[ADR-049-startup-prompt-loader]] | Önceki ADR — biçim/kanıt etiketi + index.md satır formatı referansı |
| §5.3 Debate Şartları (3) | **KABUL koşulu (debate öncesi taslak):** (1) outbox kapısı, (2) izleme kapısı, (3) idempotency + LWW testi |
| §5.4 Debate Bağlayıcı Şartları (3) | **✅ KABUL bağlayıcı (3 tur / 20 persona, 19/1/0):** (1a) olay yayımı fazı — production publish + outbox şeması, (1b) izleme — lag metrikleri + DLQ drenaj alarmı, (2) idempotent relay + LWW ihlal sensörü testi, (3) gece ETL penceresi + retry max 10 → DLQ maddelesi |
| `media.coremusic.net/src/Media/CatalogWriter.php:183` · `auth.coremusic.net/include/Repository/UserRepository.php:111` · `home.coremusic.net/scripts/import-music-folder.php:241` | 3 üretim transaksiyon sitesi — outbox'ın bağlanacağı yer — **kod kanıtı (düz metin, wiki-link değil)** |
| `shared/src/Events/` (1 dispatcher + 9 domain + 3 integration) · `shared/tests/Events/DomainEventTest.php:39,49` | Olay altyapısı = IMPLEMENTED, üretim yayımı = 0 — **kod kanıtı (düz metin)** |
| `.ai/.sql/mysql/*.sql` (19) · `.github/workflows/{ci,secret-scan}.yml` · `media.coremusic.net/bin/{scan,ingest,audit}.php` | outbox 0 · schedule 0/2 · manuel CLI — **kod kanıtı (düz metin)** |
| `.ai/architecture/k5-veri-yonetimi/backup-strategy.md:330,339` · `mysql-18-database.md:318-336` · `.ai/.templates/adr/adr-database-template.md:248` | Replikasyon vault kayıtları (PLANNED) — **düz metin** |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü — **düz metin** (vault dışı) |

> **Wiki-link doğrulaması:** Yazımdan önce `Test-Path` ile diskte doğrulandı (2026-09-29) — kod bloğu/inline-code/alıntı içi occurrence'lar hariç **31 bağlantı örneği / 22 benzersiz hedef: 22/22 diskte mevcut, eksik 0** (ham metinde 47 örnek görünür; fark, index.md:91 satırının alıntısı olan [[../brain.md]] ve künye alıntılarındır — bu dosyanın kendi bağlantısı [[../../brain.md]]'dir, [[../brain.md]] bu dosyadan çözülmez ve biçim hatası zaten report-only'dir). Diskte **olmayan** hedefe wiki-link **yazılmamış**; doğrulanamayan iddialar `⚠️ VERIFICATION REQUIRED` ile işaretli veya düz metin bırakılmıştır (ör. MySQL FK teknik iddiası — ADR-040 C1'e bağlanmaz). `ADR-050-*` hedefi bu dosyanın kendisidir. `index.md:91` içindeki `[[../brain.md]]` biçim hatası **raporlanır, düzeltilmez** (report-only — §5.1 adım 8-i).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI (2026-09-29) — 3 tur / 20 persona · sonuç: 19 kabul / 1 çekimser / 0 red → KABUL · bağlayıcı 3 şart → §5.4.**

| Tur | Tür | Sonuç |
|---|---|---|
| 1 | **Bulgu turu (20 persona)** | outbox **0/19 SQL + 0/248 PHP** → PLANNED · zamanlayıcı **0** (CREATE EVENT/ON SCHEDULE/ETL = 0; `.github/workflows` schedule 0/2) · replikasyon **0 kodda** (yalnız vault PLANNED: `backup-strategy.md:330,339`, `mysql-18-database.md:318-336`) · üretim `dispatch(new ` = **0** (testte 2); Events = 1 dispatcher + 9 domain + 3 integration · manuel CLI **4 script IMPLEMENTED** · transaction **3 çağrı sitesi** · çapraz-DB ref = **2 migration** · MySQL §15.1.20.5 instance sınırı **C1 teyidi** · **31 kaynak / 6 sorgu** · `index.md:91` report-only + ADR-040/041 "ADR-050 yok" satırları **stale** (reset'e erteli) | **16 kabul/neutral · 3 uyarı** (Backend: üretim yayımı 0 şart · QA: idempotency/DLQ · Critic: izleme şart) |
| 2.1 | **İtiraz → çözüm (1/4)** | `dispatch(new ` üretimde **0** → olay yayımı fazı: **production publish + outbox şeması** | **Şart 1a** |
| 2.2 | **İtiraz → çözüm (2/4)** | İzleme **0** → **lag metrikleri + DLQ drenaj alarmı** | **Şart 1b** |
| 2.3 | **İtiraz → çözüm (3/4)** | Idempotency testi **yok** → **relay idempotent + LWW ihlal sensörü testi** | **Şart 2** |
| 2.4 | **İtiraz → çözüm (4/4)** | ETL penceresi belirsiz → **gece ETL penceresi + retry max 10 → DLQ maddeleştirilir** | **Şart 3** |
| 3 | **Oy (3. tur)** | **19 kabul / 1 çekimser / 0 red → KABUL** · 3 şart §5.4'te bağlayıcı hâle geldi | §5.4 |

- **Bağlayıcı 3 şart:** (1) yayım fazı + izleme — **1a** production publish + outbox şeması, **1b** lag metrikleri + DLQ drenaj alarmı · **(2)** idempotent relay + LWW ihlal sensörü testi · **(3)** gece ETL penceresi + retry max 10 → DLQ maddelesi → **§5.4**.
- **Tech Lead:** ✅ **KABUL** (2026-09-29 — 3 şart §5.4 bağlayıcı) · **Arch Lead:** ⏳ (beklemede — bu işlemde güncellenmedi).
- **Wiki-link teyidi:** eklenen §5.4 / §6 / §7.1 metinlerinde **yeni wiki-link yok**; mevcut bağlantılar yazımdan sonra yeniden doğrulandı (§6 dipnotu — report-only, index.md'ye dokunulmadı).

---

*1.0.0 | 2026-09-29 | Created*
*Authority: SSOT — CoreMusic Multi-DB Sync Strategy (ADR-050)*
*Mode: Red Team · Human Mode · Truth Mode*
