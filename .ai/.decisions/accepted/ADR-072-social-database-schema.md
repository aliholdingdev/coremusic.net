---
title: "CoreMusic — ADR-072: Social Database Schema (coremusic_social · 9 tablo · 5 tablo grubu · 13 çapraz-DB FK grandfathered · indeks + TTL-partition politikası)"
type: "architecture-decision"
category: "database"
date: "2026-09-30"
updated: "2026-09-30"
version: "1.0.0"
status: "accepted"
authority: "SSOT — coremusic_social sosyal şema kararı: (a) **5 tablo grubu** (yorum · paylaşım · aktivite · dinleme odası · bildirim) + 1 bağlı tablo = **9 tablo** (dosya footer'ı ile birebir), (b) **13 çapraz-DB FK** ADR-040 istisna defterinde X-12…X-24 olarak grandfathered kalır — yenisi eklenmez, (c) **indeks politikası**: bildirim okuma yolu için birleşik indeks şemada YOK → 3 iyileştirme ADR-014 kapısından PLANNED, (d) **TTL/aylık RANGE partition ERTELENDİ**: InnoDB'de partition'lı tabloda FK taşınamaz (mevcut 17 FK ile çakışma) → kısa vade arşiv job'u, uzun vade FK fazı sonrası partition, (e) **şema IMPLEMENTED / kod PLANNED** (PHP+JS sosyal tablo sorgulaması = 0) ve **SSOT drift'i**: `oauth_connections` + `oauth_states` yalnız PHP migration'larında, `.sql` dosyasında YOK (9 ↔ 11 tablo)"
kaynak: "Disk kanıtı taraması (2026-09-30: `.ai/sources/.sql/mysql/coremusic_social.sql` = **277 satır / 16.975 bayt**, v7.0.0 · Date 2026-08-09 · `:273` 'Tables: 9' · `:274` 'BCNF Compliant: Yes' · 9 CREATE TABLE (`:23, :58, :77, :101, :129, :161, :190, :215, :245`) · 17 FK = 13 çapraz + 4 iç · çapraz satırlar `:47, 67, 91, 118, 119, 150, 151, 180, 204, 205, 235, 266, 267` = ADR-040 `:195` X-12…X-24 listesi ile **birebir** · `.ai/.sql/mysql/` = **19 dosya / 19 CREATE DATABASE / 165 tablo** (ADR-040 `:34` = 18 dosya / 156 tablo; fark `media_catalog.sql` 478 satır / 9 tablo · ADR-092 §5.2 · commit `50f8734` · 2026-09-29) · PHP/JS grep (`social_notifications|activity_feed|listening_room|comment_likes|coremusic_social`, node_modules/vendor hariç) = **1 dosya** `shared/database/migrations/oauth_connections_migration.php:4,6,7` (docblock) · `oauth_connections|oauth_states` `.ai/.sql/mysql/*.sql` = **0 isabet** · doküman `architecture/k10-uygulama/notification-panel.md` + `architecture/k8-servis/notification-service.md` MEVCUT · slug satırları `.decisions/index.md:96` · `.ai/index.md:697` · `brain.md:1013` · `keys.md:289` · ADR-040 `:163` satır 10 (9 tablo / main / ⚠️ debate) · ADR-033 `:67` BCNF beyanı denetlenmemiş) + web araştırması (**5 sorgu / 37 kaynak bildirimi** — benzersiz ~36, tekrar tespit edilmedi)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-072: Social Database Schema

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-09-30 — **Debate:** ✅ TAMAMLANDI (3 tur / 20 persona · 18/2/0 KABUL) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-072-social-database-schema` (dizin otoritesi: [[../index.md]] satır **96** — dosya adı ile birebir hizalı ✅)
> **İlgili kararlar:** [[ADR-040-database-authority]] (18-DB sahiplik matrisi + 28 FK istisna defteri — 13 çapraz FK burada bağlayıcı) · [[ADR-033-sql-normalization-strategy]] (BCNF kural seti + 156 tablo denetimi) · [[ADR-041-database-normalization-supplementary]] (indeks/naming/audit/N+1 tamamlayıcı kurallar) · [[ADR-003-multi-db-bcnf]] (18 BCNF + "DB arası FK YOK" hükmü) · [[ADR-014-multi-db-migration-strategy]] (tek migration kapısı) · [[ADR-029-listening-rooms-social]] (oda gerçek zamanlı mimarisi + kapasite 50) · [[ADR-081-multi-provider-data-sync]] (outbox + WAL) · [[ADR-050-multi-db-sync-strategy]] · [[ADR-092-media-dizin-ekseni-ve-ulid]] (media_catalog.sql üreticisi) · [[ADR-039-7-service-platform-architecture]] (A4 veri domaini sahibi) · [[ADR-064-electronics-platform-architecture]] (numara çakışması raporu) · karar dizini [[../index.md]] **satır 96**.
> **⚠️ VERIFICATION REQUIRED:** `ADR-073`–`ADR-079` (dizin satırı `:97`–`:103` var, **dosya diskte YOK**) · `ADR-075` (AI DB şeması) · `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK) · `ADR-082` (ne dosya ne dizin satırı) — hepsi **düz metin + ⚠️**, wiki-link yok · `oauth_connections`/`oauth_states` runtime'da var ama SSOT `.sql` dosyasında yok (9 ↔ 11 tablo) · BCNF "Yes" beyanı **denetlenmemiştir** (ADR-033) · sosyal tablo kullanan PHP/JS sınıfı **0**.
> **Bölüm sınırı (kenetli):** ADR-029 oda **gerçek zamanlı katmanını** (WebSocket + Redis pub/sub, sunucu saati, SSE fallback, kapasite 50), ADR-081 **çoklu sağlayıcı senkronunu** (outbox + WAL), ADR-040 **DB sahipliğini + FK istisnasını**, ADR-033/041 **normalizasyon kural setini** yazdı; **9 tablonun şeması, ilişki/indeks/TTL-partition politikası ve kod yüzeyi dürüst etiketi bu ADR'nindir** — dördü de yeniden yazılmaz.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 7'deki "yeni ADR ≥ 088" ile arşivin 072 slotu arasındaki **numara çakışması ADR-061/062/063/064 künyelerinden tekrar raporlanır, düzeltilmez**.

---

## 1. Bağlam (Context)

CoreMusic'in sosyal veri düzlemi (A4) diskte **çoktan yazılmış**, vault'ta **hiç kararlaştırılmamış** durumda: `.ai/sources/.sql/mysql/coremusic_social.sql` (v7.0.0, 2026-08-09) dokuz tabloyu, indeksleri ve 17 FK'nın 13'ünü içeri alıyor; buna karşılık hiçbir ADR bu şemayı karar olarak yazmıyor. ADR-040 onu sahiplik matrisinde "⚠️ debate" satırı olarak tutuyor, ADR-033 onu denetlenmemiş 156 tablonun parçası sayıyor, ADR-029 yalnız oda **gerçek zamanlı** katmanını tanımlıyor. Aynı anda kod yüzeyinde sosyal tablo sorgulaması **0** — yani şema IMPLEMENTED, kod PLANNED; bu ayrımı da kimse yazmadı. Üç drift büyüyor: envanter 18 → 19 dosya, SSOT `.sql` 9 tablo ↔ runtime 11 tablo (iki PHP migration'ı), bildirim okuma yolu için kaynakların hemfikir olduğu birleşik indeks şemada yok. Bu ADR beş işi tek kayıtta kapatır: **(1)** 9 tabloyu 5 gruba ayırır, **(2)** ilişki/indeks/TTL-partition politikasını yazar, **(3)** IMPLEMENTED/PLANNED etiketini koyar, **(4)** üç drift'i §5.1 kapılarına bağlar, **(5)** ADR-029/081/040/033-041 ile sınırı çizer. Kod üretmez, DDL çalıştırmaz, tablo açmaz.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-30 taraması)

| İddia | Kanıt | Etiket |
|-------|-------|--------|
| ADR-072 slotu kayıtlı mı? | [[../index.md]] `:96` → `\| [[../../raw/brain.md]] ADR-072-social-database-schema \| Social DB Schema \| Database \|` · [[../../index.md]] `:697` · [[../../raw/brain.md]] `:1013` · [[../../raw/keys.md]] `:289` | ✅ **KAYITLI** (4 indeks satırı) |
| Bu işlem öncesi dosya var mıydı? | `accepted/` dizin listesi (63 girdi) — `ADR-072*.md` **yok** | ❌ **YOKTU** → bu işlemde yazılıyor |
| Şema dosyası | [[../../sources/.sql/mysql/coremusic_social.sql]] = **277 satır / 16.975 bayt** · `:2` v7.0.0 · `:5` Date 2026-08-09 · `:273` "Tables: 9" · `:274` "BCNF Compliant: Yes" | ✅ **IMPLEMENTED** (dosya) |
| 9 tablo (5 grup) | `comments` `:23` · `comment_likes` `:58` · `shares` `:77` · `activity_feed` `:101` · `listening_rooms` `:129` · `listening_room_members` `:161` · `listening_room_queue` `:190` · `user_achievements` `:215` · `social_notifications` `:245` | ✅ **9/9 CREATE TABLE** (footer ile birebir) |
| 13 çapraz-DB FK | `:47, 67, 91, 118, 119, 150, 151, 180, 204, 205, 235, 266, 267` → `coremusic_auth.users` **(11)** + `coremusic_musics.musics` **(2)** · ADR-040 `:195` = **aynı satır listesi** (X-12…X-24) | ✅ **KABUL (grandfathered)** |
| ADR-040 sahiplik satırı | ADR-040 `:163` → `coremusic_social \| 9 \| main \| ATAMA — entity_type polymorphic \| ⚠️ debate` · `:174` "social … debate çıkışıdır" | ⚠️ **geçici sahiplik** |
| DB envanteri | `.sql/mysql/` = **19 dosya / 19 CREATE DATABASE / 165 tablo** vs ADR-040 `:34` = **18 dosya / 156 tablo**; fark = `media_catalog.sql` (478 satır / 9 tablo · ADR-092 §5.2 · commit `50f8734` · 2026-09-29) | ⚠️ **envanter drift'i** (rapor — düzeltilmez) |
| Kod yüzeyi | PHP+JS grep (`social_notifications\|activity_feed\|listening_room\|comment_likes\|coremusic_social`, node_modules/vendor hariç) = **1 dosya**: `shared/database/migrations/oauth_connections_migration.php:4,6,7` (yalnız docblock) · sosyal tablo sorgulayan class/servis = **0** | ⏳ şema IMPLEMENTED / **kod PLANNED** |
| Migration ↔ SSOT drift | `oauth_connections\|oauth_states` → `.ai/.sql/mysql/*.sql` **0 isabet**; iki dosya `shared/database/migrations/` içinde (`oauth_connections_migration.php:7` "coremusic_social veritabanına eklenir") | ⚠️ runtime **11** ↔ SSOT **9** |
| Sosyal olay sınıfı | `shared/src/Events/Integration/NotificationEvent.php` MEVCUT (kod yüzeyindeki tek sosyal-tarafı sınıf) | ✅ sınırlı / ⏳ entegrasyon PLANNED |
| Bildirim dokümanları | `architecture/k10-uygulama/notification-panel.md` (In-App Alerts/Badges) · `architecture/k8-servis/notification-service.md` MEVCUT | ✅ **IMPLEMENTED** (doküman) |
| BCNF beyanı | ADR-033 `:67` → "bunlar **iddiadır, denetim değildir** … PLANNED" | ⚠️ **denetlenmemiş** |
| Oda kapasitesi | `:136` `max_members INT NOT NULL DEFAULT 50` + satır içi yorum "ADR-029 kapasite 50" | ✅ ADR-029 yansıması (yeniden alınmaz) |
| Eksik ADR dosyaları | `accepted/` = 001–050 · 052 · 056 · 058 · 059 · 061–064 · 081 · 089 · 090 · 092 (+ kökte `ADR-091-…`) | ⚠️ **14 numara** dizinde de yok → §5.1/9 |
| Referans formatı | [[../../.templates/adr/adr-template.md]] (Guardrail #16) · format referansı [[ADR-064-electronics-platform-architecture]] | ✅ okundu |

### 1.2 Sorun Tanımı

1. **Şema var, karar yok.** 9 tablo diskte ama hangi grubun ne iş gördüğü, hangi FK'nın neden tolerated olduğu ve hangi politikanın bağlayıcı olduğu hiçbir ADR'de yazılmıyor → yeni bir geliştirici "sosyal tablo nasıl açılır?" sorusuna vault'tan cevap bulamıyor.
2. **IMPLEMENTED/PLANNED ayrımı yok.** Şema dosyası + iki doküman var, kod 0; "sosyal özellik hazır" yanılgısı doğabilir (ADR-039'un 11 servisi içinde sosyal servis de PLANNED).
3. **İndeks politikası okuma yoruyla hizalı değil.** `social_notifications` üç ayrı tek sütunlu indeks taşıyor (`:261, :264, :265`); okuma yolu `(user_id, is_read, created_at)` birleşik indeksini istiyor — kaynaklarda hemfikir olan kalıp şemada yok.
4. **TTL/partition politikası yok.** `activity_feed` + `social_notifications` zaman serisidir, büyüme sınırsızdır; ama InnoDB partition kuralı (FK yasağı + partition anahtarı tüm unique key'de) mevcut 17 FK ile doğrudan çakışıyor → "partition yapalım" diyen ilk kişi DDL'i kırar.
5. **Üç drift:** envanter 18 ↔ 19 dosya · SSOT 9 ↔ runtime 11 tablo · sahiplik "main" ⚠️ debate — hiçbiri raporlanmamış.
6. **Sınır yazılı değil.** ADR-029 (gerçek zamanlı) ve ADR-081 (senkron) bu şemaya dokunabilir; hangi katmanın kime ait olduğu söylenmezse ikisi de şemayı yeniden yazma riski taşır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "social network database schema design 2025 2026 comments shares activity feed tables" · (2) "fan-out on write vs read hybrid feed architecture write amplification celebrity problem 2025" · (3) "comment threading database design adjacency list vs materialized path vs closure table deep replies" · (4) "notification table schema design read unread composite index counter watermark TTL retention 2026" · (5) "MySQL range partitioning monthly activity feed table drop partition TTL foreign key restriction" |
| Web Search **Konusu** | **(1)** sosyal şema modelleme (yorum/paylaşım/aktivite tablo biçimleri, polymorphic varlık) · **(2)** akış yayılımı: push (fan-out on write) / pull (fan-out on read) / hibrit + yazma şişirme · **(3)** yorum ipliği: adjacency list ↔ materialized path ↔ closure table (derin cevaplar) · **(4)** bildirim tablosu: okundu/okunmadı indeksi, sayaç/watermark, TTL saklama · **(5)** MySQL aylık RANGE partition + DROP PARTITION TTL + FK kısıtı. |
| Web Search **Bağlamı** | CoreMusic: `coremusic_social.sql` 9 tablo IMPLEMENTED ama kod 0; ADR-033/041 kural seti var, indeks/TTL politikası yok; ADR-040 13 FK'yı grandfathered tutuyor; ADR-029 oda kapasitesini 50'ye sabitlemiş. Araştırma bu dört boşluğu hedefliyor — **sayılar iç karar** olduğu için web yalnızca *yapı, indeks ve TTL kuralı* için okundu (ADR-033'ün 5 sorgusunun tekrarı değil). |
| Web Search **Kısa Açıklama** | **(1)** Sosyal şema tipik olarak polymorphic `entity_type + entity_id` ile tek yorum/aktivite tablosunda toplanır; beğeni ve paylaşım ayrı ilişki tablolarıdır. **(2)** Fan-out tercihi trafik ve ünlü oranına göre değişir: küçük ölçek push, ünlü varlık çakışmasında hibrit (ön sayfa materialize + kuyruk pull); yazma şişirme push'un bedelidir. **(3)** Derin olmayan ipliklerde adjacency list (`parent_id`) en kaba ve en dayanıklısıdır; derin/çok okunan ağaçlarda closure table veya materialized path ek tablo/alan ister. **(4)** Bildirim okuma yolu birleşik indeks ister `(recipient, is_read, created_at)`; okunmamış sayımı sayaç/watermark ile O(1) tutulur; TTL için `expires_at` + temizlik işi. **(5)** Aylık RANGE partition + `DROP PARTITION` TTL'i DELETE'ten hızlı ve kilitlenmesiz yapar — ama InnoDB'de partition'lı tablo FK taşıyamaz ve partition anahtarı tüm unique key'de olmalıdır. |
| Web Search **Uzun Açıklama** | **(i)** AWS DynamoDB sosyal modelleme rehberi + twitterdesign (2025) + getstream (2026) aynı kalıbı verir: yorum tablosu polymorphic varlık anahtarı, beğeni için `(comment_id, user_id)` benzersizliği, akış için `created_at` sıralı indeks. **(ii)** hellointerview (Facebook news feed), firstprinciplesengineering, theaugmenteddev (2026), systeminternals.dev (2026) ve Goodspeed "your social feed is not like Twitter's" yazısı: push okumayı ucuzlatır ama yazmayı şişirir (1M takipçi → 1M satır), pull yazımı tek yerde tutar ama okumayı pahalılaştırır; büyük oyuncular hibrit kullanır — akış ön-üretimi ADR-029'un gerçek zamanlı katmanının işidir, **şema kararının değil**. **(iii)** dba.stackexchange + stackoverflow 28783967 + fraiseql/zenn (2026): adjacency list tek sorguda derinliği çözer, ek JOIN ister ama tutarlıdır; closure table 10× satır üretir, materialized path derinlik/çıkarma maliyeti yükler — CoreMusic `parent_id` FK (`:48`, `ON DELETE SET NULL`) zaten adjacency list kurmuştur. **(iv)** techinterview.org LLD (2026) + oneuptime (2026) + dev.to (2024): `notifications (recipient, …, is_read, created_at)` + okunmamış için `(recipient, is_read, created_at)` birleşik indeks + `notification_counts` sayaç/timestamp watermark + Redis TTL cache; 90 gün saklama + soğuk katman önerisi. **(v)** MySQL 26.7 RANGE/Partitioning Types + 9.1 partition management + keboola (aylık `UNIX_TIMESTAMP` RANGE + cron ile `REORGANIZE`/`DROP PARTITION`) + khimananda (2026): partition anahtarı PK/unique key'de zorunlu, FK'li InnoDB tabloda kullanıcı partition'ı olamaz, `MAXVALUE` yakalama bölmesi şart, günlük partition pratik değildir (aylık/çeyreklik yeter). |
| Web Search **Paragraf Veri Uzun** | **5 sorgu / 37 kaynak bildirimi** (benzersiz ~36; tekrar tespit edilmedi — techinterview.org iki sorguda geçiyor): **(1–3, 21)** AWS DynamoDB "social app modeling" · twitterdesign.substack 2025 · dba.stackexchange (threading) · stackoverflow 28783967 · Goodspeed (feed != Twitter) · mintlify activity-feed DB design · VULK 2025 · GeeksforGeeks 2025 · hld.handbook.academy · hellointerview Facebook news feed · firstprinciplesengineering fan-out · getstream.io 2026 · theaugmenteddev 2026 · systeminternals.dev 2026 · scalablesystem.dev · fraiseql.dev · techinterview.org 2026 · zenn.dev 2026 · dangvngiang.dev 2026 · illuminatedcomputing · peterspython. **(4, 8)** techinterview.org LLD notification center 2026 · oneuptime "MySQL notification system" 2026 · oneuptime "Use MySQL for notification systems" 2026 · stackguides 7506068 · dev.to nikl 2024 · stackoverflow 29831367 · stackguides 17059558 · github khodakivskyi notification-service docs. **(5, 8)** dev.mysql.com 26.7 RANGE Partitioning · dev.mysql.com 26.7 Partitioning Types · dev.mysql.com 9.1 RANGE/LIST management · vitess.io INSTANT DDL partition rotation · oneuptime "MySQL Partitioning" 2026 · stackoverflow 70586843 · keboola 500 (aylık RANGE + cron) · khimananda MySQL partitioning 2026. |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** polymorphic `entity_type/entity_id` + ayrı beğeni/paylaşım tabloları endüstri kalıbıdır → mevcut 9 tablo yapısı korunur. **(2) Karar destekleniyor (sınır da doğrulandı):** fan-out stratejisi **veri şeması değil**, akış üretimi kararıdır → CoreMusic'te yeri ADR-029/gerçek zamanlı katmandır; bu ADR'de "push mu pull mu" kararı **üretilmez**. **(3) Karar destekleniyor:** adjacency list en kaba çözümdür; `parent_id` FK + `reply_count` sayaç mevcut yapıyla tutarlı (sayaç ADR-041 denormalizasyon defterine kayıt ister). **(4) Karar destekleniyor (boşluk doğrulandı):** birleşik `(user_id, is_read, created_at)` indeksi + sayaç/watermark kalıbı üç ayrı bağımsız kaynakta aynı; şemadaki tek sütunlu üç indeks bunu karşılamıyor → **I1 iyileştirmesi**. **(5) Karar destekleniyor (engel de doğrulandı):** aylık RANGE + `DROP PARTITION` TTL için doğru araçtır, ama FK yasağı + PK içeriği zorunluluğu mevcut 17 FK ile çakışır → **partition ertelenir**, kısa vade temizlik işi. **İtiraz/karşıt bulgu:** dış kaynaklar CoreMusic'in tablo sayısını/sahipliğini doğrulayamaz (iç karar); ayrıca `user_achievements` 5 grubun dışındadır — kaynaklarda achievement'lar genelde bildirim/aktivite ile ilişkili tutulur, şema değişmez. |
| Web Search **Alınan Karar** | **(a)** 5 grup + 1 bağlı tablo = 9 tablo envanteri yazılır (şema korunur, yeniden modellenmez). **(b)** 13 çapraz FK = X-12…X-24 grandfathered; yenisi ADR-003/040 gereği yasak. **(c)** İndeks politikası: **I1** `social_notifications (user_id, is_read, created_at)` · **I2** `activity_feed (user_id, visibility, created_at)` · **I3** `comments (entity_type, entity_id, created_at, is_pinned)` — üçü de ADR-014 tek kapısından **PLANNED**. **(d)** Fan-out stratejisi bu ADR'ye alınmaz → sınır ADR-029. **(e)** TTL = kısa vade `is_deleted`/`deleted_at` üzerine temizlik işi, uzun vade FK fazı sonrası **aylık RANGE + DROP PARTITION** — ikisi de PLANNED, bu ADR'de DDL yok. **(f)** Kod yüzeyi dürüst etiketi: şema IMPLEMENTED / kod PLANNED. |
| Web Search **Sonuç** | **5/5 araştırmada karar destekleniyor:** polymorphic varlık anahtarı + ayrı beğeni tablosu (1) · fan-out ADR-029'un işi (2) · adjacency list kalır (3) · birleşik bildirim indeksi + sayaç (4) · partition aracının doğru ama FK ön koşulsuz uygulanamaz (5). **Dört gerilim açıkça kabul edildi:** (1) `user_achievements` 5 grup dışında → ayrı satır; (2) sayaç sütunları (`like_count`, `reply_count`, `member_count`) denetimsiz denormalizasyondur → ADR-041 defterine kayıt; (3) TTL hedefi rakamı (30/90 gün) **bu ADR'de sayılmadı** → ⚠️, tartışma/debate kapısında; (4) partition ön koşulu nedeniyle TTL süresiz ertelenemez → §5.1/5-6 kapıları. **⚠️ VERIFICATION REQUIRED:** TTL saklama süresi · `oauth_connections`/`oauth_states` SSOT kararı · BCNF denetimi · fan-out stratejisi (ADR-029) · `ADR-073`–`ADR-079`/`ADR-082`–`ADR-088` dosyaları. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar (ADR-003 / ADR-014 / ADR-029 dahil) |
| ADR-040 sınırı | 18-DB sahiplik matrisi, tek yazar servisi ve **28 FK istisna defteri (X-12…X-24)** bağlayıcıdır; bu ADR listeyi değiştirmez, yalnız sosyal satırının içeriğini yazar |
| ADR-033 / ADR-041 sınırı | Normalizasyon kural seti + tamamlayıcı 6 kural bağlayıcıdır; bu ADR kural **üretmez**, şemaya uygular ve istisnayı defterine yazar |
| ADR-029 sınırı | Oda gerçek zamanlı katmanı (WS/Redis/sunucu saati/SSE/kapasite 50) kapsam dışı; `max_members DEFAULT 50` yalnız yansımadır |
| ADR-081 sınırı | Outbox + WAL senkronu kapsam dışı; bu ADR çapraz-DB tutarlılık mekanizması önermez |
| Migration tek kapısı | Her şema değişikliği yalnız ADR-014 özel PHP runner'ından geçer; bu ADR elle DDL yazmaz |
| Kod kanıtı | Sosyal tablo sorgulayan PHP/JS = **0** → "servis çalışıyor / özellik hazır" iddiası yazılmaz |
| Vault'a yazma yetkisi | Bu işlem yalnız bu ADR dosyasını + `log.md` append'ini yazar; `index.md`/`brain.md`/`keys.md`/`ADR-040` düzeltmeleri **sonraki vault reset'ine ertelenir** (In-Place Refactoring + rapor-only) |
| Numara çakışması | Kural 7 "yeni ADR ≥ 088" ↔ arşiv 072 slotu — ADR-061/062/063/064 künyelerinden **aynı çakışma tekrar raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

CoreMusic'in sosyal veri şeması **bu ADR'de tek kayıtta** sabitlenir: **(a)** 9 tablo **5 tablo grubuna** bağlanır (yorum · paylaşım · aktivite · dinleme odası · bildirim) + 1 bağlı tablo (`user_achievements`); **(b)** **13 çapraz-DB FK** ADR-040'ın X-12…X-24 istisnası olarak grandfathered kalır, yenisi eklenmez, iç FK'lar (4 adet) korunur; **(c)** **indeks politikası** üç iyileştirmeyle (I1/I2/I3) ADR-014 kapısından PLANNED yazılır; **(d)** **TTL/partition politikası** iki fazda ertelenir (kısa vade temizlik işi, uzun vade FK fazı sonrası aylık RANGE + DROP PARTITION) — bu ADR'de DDL yoktur; **(e)** **dürüst etiket**: şema + doküman IMPLEMENTED, kod PLANNED, BCNF beyanı denetlenmemiş; **(f)** **sınır** = ADR-029 / ADR-081 / ADR-040 / ADR-033-041 yeniden alınmaz. Karar kod üretmez, tablo açmaz, indeks eklemez.

### 2.1 Neden Bu Seçenek?

Sorun *eksik şema değil, eksik karar*: dokuz tablo zaten diskte ve ADR-040'ın istisna defterinde kayıtlı. Bu ADR sıfırdan sosyal DB çizmek yerine mevcut şemayı **sınıflandırır, politika yazar ve dürüst etiketler**: (1) SSOT korunur — dosya adı, tablo adları ve FK'lar değişmez (In-Place Refactoring), (2) 13 FK'nın "neden ihlal" olduğu tek yerde bağlanır (ADR-003'ün "DB arası FK YOK" hükmü ile çelişiyor, grandfathered), (3) indeks/TTL politikası dış kaynaklarla destekli ilk kez gerekçeli hale gelir, (4) fan-out gibi şema-olmayan bir konu **bilerek alınmaz** (ADR-029'a devredilir) — bu, ADR-029/081 ile çakışmayı önleyen asıl karardır, (5) 9 ↔ 11 ve 18 ↔ 19 drift'leri görünür kılınır, sessizce yutulmaz. Web araştırması beş maddede de kararı destekledi (§1.3 Sonuç); karşıt bulgular (sayaç sütunları denetimsiz, TTL süresi rakamı kanıtsız) risklere ve §5.1 kapılarına yazıldı.

### 2.2 Teknik Detaylar

**(a) 5 tablo grubu — 9 tablo envanteri (şema korunur, yeniden modellenmez):**

| # | Grup | Tablo (dosya satırı) | Adet | Amaç / anahtar | Etiket |
|---|------|----------------------|------|----------------|--------|
| 1 | **Yorum** | `comments` `:23` · `comment_likes` `:58` | 2 | Polymorphic yorum (`entity_type` 6 ENUM) + iplik (`parent_id` → adjacency list, `:48` `ON DELETE SET NULL`) + beğeni (`uk (comment_id, user_id)` `:64`) | ✅ IMPLEMENTED (şema) |
| 2 | **Paylaşım** | `shares` `:77` | 1 | `entity_type` + `share_platform` (BCNF aday anahtar yorumu `:74`) + `idx_shares_entity` `:88` | ✅ IMPLEMENTED (şema) |
| 3 | **Aktivite** | `activity_feed` `:101` | 1 | 11 ENUM tipi, `visibility` (public/friends/private), `metadata JSON`, `idx_activity_user_created` `:117` | ✅ IMPLEMENTED (şema) |
| 4 | **Dinleme odası** | `listening_rooms` `:129` · `listening_room_members` `:161` · `listening_room_queue` `:190` | 3 | Oda (`uk room_code` `:146`, `max_members DEFAULT 50` `:136`) + üye (`uk (room_id, user_id)` `:175`) + kuyruk | ✅ şema / ⏳ kod PLANNED |
| 5 | **Bildirim** | `social_notifications` `:245` | 1 | 8 ENUM tipi, `is_read`/`read_at`/`is_dismissed`, `from_user_id` (sistem için NULL) | ✅ IMPLEMENTED (şema) |
| — | **Bağlı (grup dışı)** | `user_achievements` `:215` | 1 | Rozet (`uk (user_id, achievement_type)` `:234`); `activity_feed.activity_type='achievement'` ile ilişkili — 5 grubun parçası değildir, sayımı etkilemez | ✅ IMPLEMENTED (şema) |

- **Toplam 9** — dosya footer'ı `:273` "Tables: 9" ile birebir; grup sayımı 2+1+1+3+1 = 8 + 1 bağlı = 9.
- **Polymorphic sınır:** `entity_type` ENUM('music','album','playlist','podcast','radio','video') üç tabloda tekrar eder (`:26`, `:80` ve aktivite/bildirimde VARCHAR) → ADR-040'ın "tek alana sığmaz, platform sahibi" gerekçesi (`:163`) bu ADR'de de geçerlidir.
- **Sayaç alanları (denetimsiz denormalizasyon):** `comments.like_count/reply_count` `:30-31`, `listening_rooms.member_count` `:140` → ADR-041 kayıt defterine **girilmelidir** (§5.1/4).

**(b) İlişki politikası — 13 çapraz-DB FK grandfathered + 4 iç FK:**

| FK grubu | Dosya satırları | Hedef | Adet | Durum |
|----------|-----------------|-------|------|-------|
| Kullanıcı (yorum, beğeni, paylaşım, aktivite ×2, oda sahibi, üye, kuyruk ekleyen, achievement, bildirim ×2) | `:47, 67, 91, 118, 119, 150, 180, 205, 235, 266, 267` | `coremusic_auth.users(id)` | **11** | ✅ KABUL — ADR-040 X serisi (grandfathered) |
| Müzik (oda current music, kuyruk music) | `:151, 204` | `coremusic_musics.musics(id)` | **2** | ✅ KABUL — aynı defter |
| **Çapraz toplam** | 13 satır — ADR-040 `:195` listesi ile **birebir** | — | **13** | ✅ X-12…X-24 |
| **İç FK (çapraz değil)** | `comments.parent_id` `:48` · `comment_likes.comment_id` `:66` · `members.room_id` `:179` · `queue.room_id` `:203` | aynı DB içindeki tablolar | **4** | ✅ korunur |

- **Kural:** yeni çapraz-DB FK **YASAK** (ADR-003 §2.2 + ADR-040 (c)); 13'ü kaldırılmaz, defterde kalır. CI statik kapısı ADR-040'ın işidir — bu ADR yalnız sosyal dilimin içeriğini yazar.
- **Sahiplik:** tek yazar = `main` (ADR-040 `:163`), **⚠️ debate** → servis eşleşmesi ADR-039 §5.1 adım 5 ile kapanır (§5.1/8).

**(c) İndeks politikası — mevcut + 3 PLANNED iyileştirme:**

*Mevcut (IMPLEMENTED):* `comments` 5 B-tree + `ftx_comments_text` FULLTEXT `:46` · `comment_likes` unique `:64` · `shares` entity `:88` · `activity_feed` 6 indeks (kompozit `:117` dahil) · `listening_rooms` unique + 3 · `members` unique `:175` · `achievements` unique `:234` · `social_notifications` **3 ayrı tek sütunlu** (`user_id` `:261`, `is_read` `:264`, `created_at` `:265`).

| # | İndeks (PLANNED) | Neden | Kanıt | Kapı |
|---|------------------|-------|-------|------|
| **I1** | `social_notifications (user_id, is_read, created_at)` | Bildirim listesi + okunmamış sayımı tek indeksle çözülür; mevcut üç tek sütunlu indeks sıralama/filtreyi birlikte taşıyamaz | techinterview.org LLD 2026 + oneuptime 2026 (hemfikir) · §1.3/4 | ADR-014 runner |
| **I2** | `activity_feed (user_id, visibility, created_at)` | Akış sorgusu `WHERE user_id=? AND visibility IN(…) ORDER BY created_at`; `:117` görünürlüğü kapsamıyor | §1.3/1 · ADR-041 indeks kuralı | ADR-014 runner |
| **I3** | `comments (entity_type, entity_id, created_at, is_pinned)` | Sıralı yorum listesi + sabit yorum ön sıralama; mevcut `:42` yalnız iki sütun | §1.3/3 · ADR-041 | ADR-014 runner |

- **Sayım kuralı:** okunmamış bildirim sayımı `COUNT(*)` ile her istekte **çalıştırılmaz** — sayaç/timestamp watermark (§1.3/4) opsiyonel PLANNED; bu, ADR-041'in counter/N+1 kuralına bağlıdır.
- **Ölçüm şartı:** üç indeks de `EXPLAIN` ile teyit edilmeden "hızlandırdı" denmez (ADR-033 workload denetimi PLANNED ile aynı kapı).

**(d) TTL / partition politikası — iki fazlı erteleme (gerekçeli):**

| Faz | Mekanizma | Durum | Gerekçe |
|-----|-----------|-------|---------|
| **Kısa vade** | Zamanlanmış temizlik işi: `is_deleted`/`deleted_at` (soft delete zaten şemada `:38-39`, `:258-259`) + `created_at` üst sınırına göre toplu silme/arşivleme | ⏳ PLANNED | FK'ları bozmaz, ADR-014 runner'ını beklemez |
| **Uzun vade** | `activity_feed` + `social_notifications` için **aylık RANGE (veya RANGE COLUMNS) partition + `DROP PARTITION` ile TTL** | ⏳ PLANNED (**ön koşul bağlı**) | MySQL 26.7 + keboola + khimananda: DROP PARTITION DELETE'ten hızlı, kilitlenmesiz; `MAXVALUE` yakalama bölmesi şart |

- **Ön koşul (engel):** InnoDB'de kullanıcı tanımlı partition'lı tablo **FK taşıyamaz** ve partition anahtarı **tüm unique/PK anahtarlarında** bulunmalıdır → mevcut 17 FK + `BINARY(16) PK` yapısıyla doğrudan uygulanamaz. Uzun vade, ADR-040'ın FK temizlik fazlarının (uygulama katmanına geçiş) **bitmesine** bağlıdır.
- **Pratik limit:** günlük partition yok — aylık/çeyreklik (khimananda: 8192 teknik sınır, pratik çok altında); TTL süresi (30/90 gün) **bu ADR'de sayılmadı** → ⚠️ debate kapısı.
- Bu ADR **hiçbir DDL içermez**; tüm değişiklikler ADR-014 tek kapısından yürür.

**(e) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

| Katman | Öğe | Durum | Kanıt |
|--------|-----|-------|-------|
| Şema dosyası | 9 tablo + indeks + 17 FK | ✅ **IMPLEMENTED** | `coremusic_social.sql` 277 satır / 16.975 bayt |
| Migration | `oauth_connections` + `oauth_states` (içerik) | ✅ IMPLEMENTED içerik / ⏳ runner PLANNED | ADR-040 `:67` · iki PHP dosyası |
| Doküman | bildirim paneli + bildirim servisi | ✅ **IMPLEMENTED** (doküman) | `k10-uygulama/notification-panel.md` · `k8-servis/notification-service.md` |
| Olay sınıfı | `NotificationEvent` | ✅ sınırlı / ⏳ entegrasyon PLANNED | `shared/src/Events/Integration/NotificationEvent.php` |
| **Kod (okuma/yazma)** | yorum/beğeni/paylaşım/aktivite/oda/bildirim CRUD | ⏳ **PLANNED** | PHP+JS grep = 0 sorgulama (yalnız 1 migration docblock) |
| BCNF denetimi | 9 tablo determinant denetimi | ⏳ **PLANNED** | ADR-033 `:67` (beyan ≠ denetim) |
| Servis sahibi | `main` (ADR-040 `:163`) | ⚠️ debate | ADR-039 §5.1/5 bekliyor |

**(f) Sınır (tekrar yok):** oda gerçek zamanlı katmanı + kapasite 50 → [[ADR-029-listening-rooms-social]] · çoklu sağlayıcı senkronu (outbox + WAL) → [[ADR-081-multi-provider-data-sync]] · DB sahipliği + FK istisna defteri → [[ADR-040-database-authority]] · normalizasyon kural seti → [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]] · migration tek kapısı → [[ADR-014-multi-db-migration-strategy]] · medya arşivi şeması (media_catalog) → [[ADR-092-media-dizin-ekseni-ve-ulid]] (kapsam dışı) · fan-out stratejisi (push/pull/hibrit) → ADR-029'un gerçek zamanlı katmanına **devredilir, bu ADR'de karar alınmaz** · `ADR-073`–`ADR-079` (podcast/radio/AI/video/studio/CMS/i18n şemaları, başlıklar `index.md:97-103`'tedir) ve `ADR-083`–`ADR-088` (SPA router, gateway, shared, event, plan, gender-OAuth, başlıklar `index.md:105-110`'dadır) **dosyalar diskte YOK → düz metin + ⚠️** (`ADR-082` ne dosya ne dizin satırı — ayrıca `ADR-088` yalnız `oauth_connections_migration.php:9` docblock'unda geçer).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Şemayı bu ADR'de baştan modelle** (9 tablo → BCNF denetimi + yeniden yazım) | Denetimsiz "BCNF Yes" beyanı kapanırdı | ADR-033'ün 156 tablo denetimi (determinant taraması) hiç çalıştırılmadan şema değişimi **kör** olur; kod 0 iken değişimin doğrulanacak kanıtı yok; dosya adı/tablo adları In-Place Refactoring ile kutsal | Denetim ayrı PLANNED iş (ADR-033) → **ret**; bu ADR yalnız politika + etiket yazar |
| 2 | **Bildirim/aktivite tablolarını baştan partition'lı aç** | TTL baştan hazır olurdu | InnoDB'de FK'lı tabloya kullanıcı partition'ı **konamaz** (17 FK) + partition anahtarı PK/unique içeri zorunlu → mevcut yapıda imkânsız; ayrıca elle DDL ADR-014 tek kapısını bypass eder | Teknik engel + süreç ihlali → **ret**; iki fazlı erteleme §2.2/d'ye yazıldı |
| 3 | **Sosyal veriyi `coremusic_user` DB'sine taşı** (kullanıcı verisi tek yerde) | Tek kullanıcı DB'si, tek FK hedefi | ADR-040 sahiplik matrisi + 13 FK hedefi `coremusic_auth.users`; taşıma 28 FK istisna defterini ve X-12…X-24 satırlarını bozar; polymorphic `entity_type` tek alana yine sığmaz (ADR-040 `:163` gerekçesi) | Otorite + istisna defteri çökertirdi → **ret** |
| 4 | **Polymorphic'i FK'li ayrı tablolara böl** (`comment_music`, `comment_album`, …) | Her ilişki tip-güvenli FK alırdı | 6 varlık tipi × (yorum + paylaşım + aktivite) ≈ 18+ tablo; şema şişer, ENUM↔FK tutarsızlığı büyür (ADR-041ENUM/kapsam kuralı), yeni tip = yeni tablo + migration | Tablo patlaması + ADR-041 çelişkisi → **ret**; polymorphic korunur |
| 5 | **Kodu bu ADR'ye bağla** (şema + sosyal servis tek adımda) | Tek seferde biter | Şablon §2 gereği ADR **kod üretmez**; 8 servis PLANNED iken (ADR-039) sosyal kodu sahiplik debate'ine önceden bağlanmış olurdu | Yetki/süreç ihlali → **ret**; kod ayrı adım §5.1/7 |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Şema ilk kez karar olarak kayıtlı:** 9 tablo / 5 grup / 13 FK / indeks + TTL politikası tek dosyada; yeni geliştirici "sosyal tablo nasıl açılır?" sorusuna vault'tan cevap bulur.
- **13 FK'nın statüsü tek yerde:** ADR-003'ün "DB arası FK YOK" hükmü ile çelişen 13 satır grandfathered olarak gerekçeli — kimse "ihlali düzeltmeye" kalkmaz, yenisi eklenmez (kapı ADR-040 CI).
- **İndeks boşluğu görünür:** I1 birleşik bildirim indeksi dış kaynaklarla hemfikir, şemada yoktu → PLANNED kapıya bağlandı (EXPLAIN şartıyla).
- **Partition engeli önceden yazıldı:** "Neden partition yok?" sorusunun cevabı (FK yasağı + PK içeriği) hazır → ilk geliştirici kırık DDL yazmaz.
- **Dürüst etiket:** şema IMPLEMENTED / kod PLANNED ayrımı + 9 ↔ 11 ve 18 ↔ 19 drift'leri hem §1.1'de hem §5.1'de kayıtlı.
- **Sınır net:** ADR-029 (gerçek zamanlı) ve ADR-081 (senkron) bu şemaya dokunmaz; fan-out kararı bilerek alınmadı.

### 4.2 Olumsuz Sonuçlar

- **Üç drift düzeltilmedi** (rapor-only): `media_catalog.sql` envanterin dışında (18 ↔ 19), `oauth_connections`/`oauth_states` SSOT'ta yok (9 ↔ 11), ADR-040 `:163` satırı "⚠️ debate" — reset'e ertelendi; arada bu sürüklenmeler vault'ta okunmaya devam eder.
- **TTL rakamı yazılmadı:** saklama süresi (30/90 gün) kanıtlanmadı → ⚠️; tartışma öncesi "ne kadar süre saklıyoruz?" cevapsız kalır.
- **BCNF hâlâ beyan düzeyinde:** 9 tablo için determinant denetimi çalıştırılmadı (ADR-033 PLANNED) → bu ADR şemanın doğru olduğunu **iddia etmez**, yalnız kaydeder.
- **Kod PLANNED olarak kaldı:** indeks/TTL iyileştirmeleri kod olmadan doğrulanamaz (EXPLAIN için üretim sorgusu gerekir).
- **Bir ADR daha:** vault'a tek dosya eklendi; indeks/brain/keys satırları zaten vardı ama `[[../../raw/brain.md]]` hedef düzeltmesi bu işlemde yapılmadı.

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **SSOT ↔ runtime tablo drift'i** — migration'lar `.sql` dosyasında olmadığı için ikinci yazan tabloyu göremez (9 ↔ 11) | 4 (çok olası) | 3 (orta) | §5.1/2 kararı: iki tablo SSOT'a taşınır **ya da** migration SSOT ilan edilir; ADR-014 runner bu tabloları da kapsar |
| R2 | **Bildirim okuma yolu yavaş** — `(user_id, is_read, created_at)` yokken okunmamış sayaçı tablo taraması | 3 (olası) | 3 (orta) | I1 birleşik indeks (§5.1/3) + sayaç/watermark (§5.1/4) + EXPLAIN teyidi |
| R3 | **TTL süresiz ertelenir** — FK ön koşulu beklerken `activity_feed`/`social_notifications` sınırsız büyür | 4 (çok olası) | 3 (orta) | Kısa vade temizlik işi FK'sız çalışır (§5.1/5); uzun vade §5.1/6 ayrı faz |
| R4 | **Yeni çapraz FK sızması** — sosyal tabloya bakan geliştirici `coremusic_auth.users`'a FK ekler | 3 (olası) | 4 (yüksek) | ADR-040 CI statik kapısı + bu ADR §2.2/b "yenisi YASAK" satırı; ihlal tespit → revert + log ERROR |
| R5 | **"Sosyal özellik hazır" yanılgısı** — şema/doküman IMPLEMENTED diye kod var sanılır | 4 (çok olası) | 3 (orta) | §1.1/8 + §2.2/e tablosu: kod satırı **0**; ADR-039 sosyal servis satırı PLANNED kalır |

### 4.4 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| 13 FK istisna defteri (X-12…X-24) | [[ADR-040-database-authority]] `:47` · `:195` · `:350` | ✅ satır listesi birebir doğrulandı |
| Normalizasyon kural seti | [[ADR-033-sql-normalization-strategy]] · [[ADR-041-database-normalization-supplementary]] | ✅ uygulandı (istisna defteri §2.2/c) |
| Migration tek kapısı | [[ADR-014-multi-db-migration-strategy]] | ✅ I1/I2/I3 + TTL buradan |
| Oda gerçek zamanlı + kapasite 50 | [[ADR-029-listening-rooms-social]] (`:136` yansıması) | ✅ sınır (kapsam dışı) |
| Outbox + WAL senkronu | [[ADR-081-multi-provider-data-sync]] | ✅ sınır (kapsam dışı) |
| DB arası FK hükmü | [[ADR-003-multi-db-bcnf]] §2.2 | ⚠️ çelişki grandfathered ile kapatıldı |
| Servis sahipliği (main, debate) | [[ADR-039-7-service-platform-architecture]] §5.1/5 | ⚠️ açık → §5.1/8 |
| media_catalog envanter drift'i | [[ADR-092-media-dizin-ekseni-ve-ulid]] §5.2 (`media_catalog.sql`) | ⚠️ 18 ↔ 19 → §5.1/9 |
| Dizin slug satırı | [[../index.md]] satır **96** | ✅ hizalı; `[[../../raw/brain.md]]` hedef düzeltmesi §5.1/10'da ertelendi |
| AI/podcast/radio/video/studio/CMS/i18n şemaları | `ADR-073` … `ADR-079` (dizin `:97`–`:103`) | ⚠️ dosyalar diskte YOK → düz metin |
| Eksik numaralar (dosya + dizin satırı yok) | `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` | ⚠️ **14 numara** → §5.1/9 (rapor-only) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Debate 3 tur + Tech Lead onayı (TTL süresi rakamı dahil karar kapıları) | MO + persona | 1 gün | ✅ **TAMAMLANDI** (3 tur / 20 persona · 18/2/0 KABUL — §7.2) |
| 2 | `oauth_connections` + `oauth_states` → SSOT `.sql` dosyasına taşınması **ya da** migration'ların SSOT ilanı (9 ↔ 11 tablo drift'i) | Data Engineer + ADR-040 sahibi | 1 gün | ⏳ PLANNED (bu işlemde rapor-only) |
| 3 | **I1** birleşik indeks migration'ı (`social_notifications (user_id, is_read, created_at)`) — ADR-014 tek kapı + EXPLAIN teyidi | Data Engineer | 0.5 gün | ⏳ PLANNED |
| 4 | **I2/I3** indeksleri + sayaç/watermark kararı + `like_count`/`reply_count`/`member_count` satırlarının ADR-041 denormalizasyon defterine kaydı | Data + Backend | 1 gün | ⏳ PLANNED |
| 5 | Kısa vade TTL: `created_at`/`deleted_at` üstünlüne göre zamanlanmış temizlik işi (FK'sız, arşiv tablosuna yazma opsiyonu) | Backend + DevOps | 1 gün | ⏳ PLANNED |
| 6 | Uzun vade TTL: FK'leri uygulama katmanına taşıma fazı (ADR-040 temizlik fazları) → `activity_feed` + `social_notifications` aylık RANGE/RANGE COLUMNS + `MAXVALUE` + `DROP PARTITION` | Data Engineer | 3 gün | ⏳ PLANNED (⚠️ ön koşul — §2.2/d) |
| 7 | Sosyal kod yüzeyi: repository/servis katmanı (şema hazır, kod 0) + entegrasyon testi (yorum → bildirim akışı) | Backend + QA | 5 gün | ⏳ PLANNED |
| 8 | ADR-040 `:163` "main / ⚠️ debate" satırının ADR-039 §5.1/5 ile kapanması | MO + Data | sonraki vault reset | ⏳ rapor-only (bu işlemde düzeltilmedi) |
| 9 | **Numara boşlukları raporu (14):** `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` — dosya YOK **ve** `.decisions/index.md` satırı YOK (dizin 91/92'de 050 → 061, 95/96'da 064 → 072, 103/104'te 079 → 081 zıplar; ayrıca `ADR-082` ne dosya ne satır) + `media_catalog.sql` envanter drift'i (18 ↔ 19) | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 10 | `index.md:96` `[[../../raw/brain.md]]` → gerçek ADR hedefine düzeltmesi + `brain.md:1013`/`keys.md:289` satır metinlerinin bu ADR'ye bağlanması | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi** (rapor-only) |
| 11 | **Şart 1 (debate)** — envanter + SSOT düzeltmesi: **(1a)** `.sql/mysql/` envanterinin SSOT sayımıyla ADR-040 `:34` satırına hizalanması (19 dosya / 165 tablo ↔ 18/156 — `media_catalog.sql` dahil, sonraki vault reset) + **(1b)** `oauth_connections`/`oauth_states` için **tek kaynak kararı** (`.sql` SSOT'a taşınır **ya da** migration SSOT ilan edilir — §5.1/2 ile aynı kapı) | MO + Data Engineer + ADR-040 sahibi | sonraki vault reset | ⏳ debate şartı (2026-09-30) |
| 12 | **Şart 2 (debate)** — **I1** bildirim birleşik (composite) indeks fazı: `social_notifications (user_id, is_read, created_at)` migration'ı — ADR-014 tek kapı + EXPLAIN teyidi (§5.1/3) | Data Engineer | 0.5 gün | ⏳ debate şartı (2026-09-30) |
| 13 | **Şart 3 (debate)** — şema IMPLEMENTED / **kod PLANNED** etiketinin korunması + **TTL/partition kararının (aşama 2)** FK fazı sonrası partition için alınması ve debate kaydına işlenmesi (§2.2/d-e · §5.1/5-6) | MO + Data + Backend | aşama 2 | ⏳ debate şartı (2026-09-30) |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `index.md:96`/`brain.md:1013`/`keys.md:289` satırları **silinmez** (değişmez kalır), `log.md`'ye `ADR-072 RED (debate …)` append edilir; bu durumda (i) 9 tablo şeması **dosyada kalmaya devam eder** (SQL dosyası bu ADR'den bağımsızdır, dokunulmaz), (ii) I1/I2/I3 indeksleri ve TTL fazları **kural olmaktan çıkar** (öneri statüsüne döner), (iii) 13 FK "grandfathered" etiketi ADR-040'ta zaten durduğu için otorite kaybı olmaz, (iv) §1.1'deki 9 ↔ 11 ve 18 ↔ 19 drift raporları **yine de geçerlidir** (bağımsız bulgu). Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks/brain/keys satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

---

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır | Ne | Etiket |
|-------|-------|----|--------|
| `.ai/.decisions/index.md` | `:96` | slug `ADR-072-social-database-schema` | ✅ hizalı (dosya adı ile birebir) |
| `.ai/index.md` | `:697` | "ADR-072 … (comments, shares, activity, rooms, notifications)" | ✅ kayıtlı |
| `.ai/raw/brain.md` | `:1013` | aynı özet satırı | ✅ kayıtlı (metin bu ADR ile hizalanacak → §5.1/10) |
| `.ai/raw/keys.md` | `:289` | "ADR-072 \| social database, comments, shares, activity, notifications" | ✅ kayıtlı |
| `.ai/sources/.sql/mysql/coremusic_social.sql` | `:23–269` | 9 tablo + 17 FK + indeksler · `:273-274` footer | ✅ **birincil kanıt** (IMPLEMENTED) |
| `.ai/.decisions/accepted/ADR-040-database-authority.md` | `:34` · `:47` · `:163` · `:195` · `:350` · `:67` | 18/156 envanter · social 13 FK · satır 10 (main/⚠️ debate) · X-12…X-24 · migration notu | ✅ dosya var — **bağlayıcı otorite** |
| `.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md` | `:67` · `:71-72` | "BCNF = iddia, denetim PLANNED" · 156 PK / 82 FK / 550 index · 28 cross-DB | ✅ dosya var — **kural seti** |
| `.ai/.decisions/accepted/ADR-041-database-normalization-supplementary.md` | §2 (a–f) | naming/datatype/view/trigger/audit/N+1 tamamlayıcılar | ✅ dosya var |
| `.ai/.decisions/accepted/ADR-029-listening-rooms-social.md` | künye | WS + Redis pub/sub, sunucu saati, SSE, kapasite 50 | ✅ dosya var — **sınır** |
| `.ai/.decisions/accepted/ADR-081-multi-provider-data-sync.md` | künye | outbox + WAL | ✅ dosya var — **sınır** |
| `.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md` | künye | tek PHP runner kapısı | ✅ dosya var — **tüm DDL kapısı** |
| `shared/database/migrations/oauth_connections_migration.php` · `oauth_states_migration.php` | `:4,6,7` · dosya | coremusic_social'e `oauth_connections` ekler (docblock: ADR-088 + ADR-072) | ✅ IMPLEMENTED içerik / ⏳ runner |
| `shared/src/Events/Integration/NotificationEvent.php` | dosya | tek sosyal-tarafı olay sınıfı | ✅ sınırlı |
| `.ai/architecture/k10-uygulama/notification-panel.md` · `.ai/architecture/k8-servis/notification-service.md` | dosya | In-App Alerts/Badges · bildirim servisi | ✅ IMPLEMENTED (doküman) |
| `.ai/sources/.sql/mysql/media_catalog.sql` | 478 satır / 9 tablo | ADR-092 §5.2 üretimi (commit `50f8734`) — ADR-040 envanterinin dışında | ⚠️ 18 ↔ 19 drift |
| `.ai/.templates/adr/adr-template.md` | §3 · §4 · §6 | 7 bölüm iskeleti + 19 doğrulama (Guardrail #16) | ✅ şablon |
| `.ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md` | künye | format/biçim referansı + numara çakışması raporu | ✅ format referansı |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-064-electronics-platform-architecture]] · otorite: [[ADR-040-database-authority]] · kural seti: [[ADR-033-sql-normalization-strategy]] · [[ADR-041-database-normalization-supplementary]]
- İlgili ADR'ler: [[ADR-003-multi-db-bcnf]] · [[ADR-014-multi-db-migration-strategy]] · [[ADR-029-listening-rooms-social]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-050-multi-db-sync-strategy]] · [[ADR-081-multi-provider-data-sync]] · [[ADR-092-media-dizin-ekseni-ve-ulid]]
- Şema/kod kanıtları: [[../../sources/.sql/mysql/coremusic_social.sql]] · [[../../architecture/k10-uygulama/notification-panel.md]] · [[../../architecture/k8-servis/notification-service.md]]
- Vault kökü: [[../index.md]] · [[../../index.md]] · [[../../raw/brain.md]] · [[../../raw/keys.md]] · [[../../log.md]] · [[../../CLAUDE.md]]
- Dizin kayıtları (düz metin — dizin hedefidir, .md değildir → wiki-link değil): `.sql/mysql/` (19 dosya) · `shared/database/migrations/` · `shared/src/Events/Integration/`
- Diskte **olmayan** (düz metin + ⚠️): `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` · `ADR-080` (14 numara) · `ADR-073`–`ADR-079` · `ADR-082`–`ADR-088`

### 6.3 Wiki-Link Sayımı

| Öğe | Değer |
|------|-------|
| Wiki-link toplamı (bu dosya) | **58** occurrence / **21** benzersiz hedef (kod blokları hariç) |
| Diskte olan hedef | **21 / 21** ✅ — 10 path-form (`../index.md`, `../../brain.md`, `../../.sql/mysql/coremusic_social.sql` …) diskte mevcut; 11 slug-form (`[[ADR-040-database-authority]]` …) ADR-064/040 house biçimi, `accepted/` dizininde `<slug>.md` ile basename çözülüyor (11/11 doğrulandı) |
| Kod içi alıntılar (link değil) | `[[../../raw/brain.md]]` ×3 + `[[…]]` ×1 — index.md:96 kusurlu satırının alıntısı, bunlar canlı link sayılmadı |
| Düz metin + ⚠️ (linklenmeyen) | `ADR-051/053/054/055/057/060` · `ADR-065`–`071` · `ADR-073`–`079` · `ADR-080` · `ADR-082`–`088` |

### 6.4 Debate Şartları (3 — 2026-09-30 KABUL)

| # | Şart | Karşılık | Durum |
|---|------|----------|-------|
| 1 | Envanter + SSOT düzeltmesi (**1a** envanter 19/165 ↔ 18/156 · **1b** `oauth_*` tek kaynak) | §5.1/11 · §1.1/7 · §1.1/9 · §5.1/2 | ⏳ açık |
| 2 | Bildirim birleşik (composite) indeks — **I1** fazı | §5.1/12 · §2.2/c · §5.1/3 | ⏳ açık |
| 3 | Şema IMPLEMENTED / **kod PLANNED** etiketi + TTL/partition kararı (aşama 2) | §5.1/13 · §2.2/d-e · §5.1/5-6 | ⏳ açık |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-09-30 | ✅ |
| Tech Lead | — | 2026-09-30 | ✅ (debate 18/2/0 KABUL sonrası) |
| Arch Lead | — | — | ⏳ |

**Numara boşlukları raporu (§5.1/9 ile aynı — rapor-only, düzeltilmedi):** `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065` · `ADR-066` · `ADR-067` · `ADR-068` · `ADR-069` · `ADR-070` · `ADR-071` · `ADR-080` = **14 numara** (dosya YOK + `.decisions/index.md` satırı YOK). Ayrıca `ADR-082` (dosya YOK, satır YOK) ve `ADR-073`–`ADR-079`/`ADR-083`–`ADR-088` (satır var, dosya YOK) ayrı ⚠️ kümesidir. Bu ADR numara serisine **dokunmaz**, yalnızca raporlar.

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** (3 tur / 20 persona — 2026-09-30) |
| Tur sayısı | 3 / 3 |
| Sonuç | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tur 1 — bulgu (20 persona) | `coremusic_social.sql` **277 satır / 9 tablo / 17 FK = 13 çapraz (X-12…X-24 birebir) + 4 internal** · bildirim indeksleri **3 tek sütunlu, composite YOK** → I1 PLANNED (I2/I3 mevcut) · partition **iki aşamalı erteleme** (InnoDB FK-on-partitioned ban) · TTL sayısı **bilinçli yazılmadı** (debate kapısı) · envanter drifti **19 .sql / 165 tablo ↔ ADR-040 "18/156"** (`media_catalog.sql`) rapor-only · SSOT drift **`oauth_connections`/`oauth_states` .sql'te 0**, yalnız PHP migrasyonlarında (**9 ↔ 11** tablo) · PHP/JS CRUD **0** → şema IMPLEMENTED / kod PLANNED · **14 atlanan boşluk notu** (051/053-055/057/060 + 065-071 + 080) §5.1/9 + §7.1'de · ADR-082 index satırı yok (104=081, 105=083) raporlandı · **37 kaynak / 5 sorgu** · `index.md:96` slug hizalı · ADR-082/075 diskte yok → düz metin + V.R. · **16 kabul/neutral, 4 uyarı** (DevOps: drift şart · QA: I1 şart · Critic: PLANNED+TTL şart · DB: oauth SSOT) |
| Tur 2 — itiraz → çözüm | **(1)** 19/165 ↔ 18/156 envanter drifti → envanter düzeltme + SSOT sayımı (reset) → **şart 1a** · **(2)** `oauth_*` PHP↔SQL SSOT uyuşmazlığı → tek kaynak kararı + düzeltme → **şart 1b** · **(3)** bildirim composite indeks yok → I1 indeks fazı → **şart 2** · **(4)** kod 0 + TTL/partition belirsiz → PLANNED etiketi + TTL/partition kararı (aşama 2) → **şart 3** |
| Tur 3 — oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Şartlar (3) | **(1)** envanter + SSOT düzeltmesi (1a-1b) → §5.1/11 · **(2)** bildirim composite indeks (I1) → §5.1/12 · **(3)** PLANNED etiketi + TTL/partition kararı → §5.1/13 (hepsi §6.4) |
| Beklenen kapılar | TTL saklama süresi rakamı · I1-I3 indeks önceliği · `oauth_connections` SSOT kararı · fan-out devri (ADR-029) teyidi → **3 şart ile bağlandı** (TTL/partition kararı aşama 2 — §5.1/13) |
| Tech Lead | ✅ (2026-09-30 — debate 18/2/0 KABUL sonrası) |
| Arch Lead | ⏳ (onay akışı §7.1'de açık) |

> **Kural:** Debate ✅ TAMAMLANDI (3 tur / 20 persona · 18/2/0 KABUL) ve Tech Lead ✅ — buna rağmen TTL/İndeks DDL'leri yalnız **3 şart** (§5.1/11-13) ve §5.1 kapıları kapatıldıktan sonra ADR-014 runner'ından geçer; debate bu ADR'yi `accepted` **öneri + kayıt** statüsünden çıkarmaz, DDL yetkisi vermez. Frozen'a geçiş yoktur (ADR-001–037 dışı).

---

*ADR-072 v1.0.0 — 2026-09-30 — Created (debate PENDING)*
*Authority: CoreMusic Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
*ADR-072 v1.0.0 — 2026-09-30 — Debate ✅ TAMAMLANDI (3 tur / 20 persona · 18/2/0 KABUL) · Tech Lead ✅ · +3 şart (§5.1/11-13, §6.4)*
