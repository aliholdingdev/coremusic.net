---
title: "CoreMusic — ADR-073: Podcast Database Schema (coremusic_musics içinde 4 tablo · shows/episodes/subscriptions/transcripts · 3 iç FK · 13 indeks · transcript LONGTEXT + ertelenmiş partition/LOB stratejisi)"
type: "architecture-decision"
category: "database"
date: "2026-09-30"
updated: "2026-09-30"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic podcast veri şeması kararı: (a) **4 tablo** (`podcast_shows` `:406` · `podcast_episodes` `:444` · `podcast_subscriptions` `:487` · `podcast_transcripts` `:516`) ayrı dosyada DEĞİL, `coremusic_musics.sql` içinde — ayrı bir `podcast*.sql` dosyası **YOK**; (b) **3 iç FK** (`fk_pe_show` `:479` · `fk_psub_show` `:506` · `fk_pt_episode` `:536`, üçü de `ON DELETE CASCADE ON UPDATE CASCADE`) + **0 çapraz-DB FK** — `author_user_id`/`user_id` yalnız yorum satırıdır (ADR-003/ADR-040 kuralına uyumlu, 28 istisna defterinde podcast satırı yok); (c) **13 mevcut indeks** korunur + **2 PLANNED** (I1 feed kompoziti, I2 transcript FULLTEXT) — ikisi de ADR-014 tek kapısından; (d) **transcript `LONGTEXT` → partition/LOB stratejisi ERTELENDİ**: InnoDB'de FK'lı tablo partition taşıyamaz + partition anahtarı tüm unique/PK anahtarında zorunludur (vault'taki tek partition örneği `api_calls` FK'sızdır); (e) **şema IMPLEMENTED / kod PLANNED** (PHP+JS podcast tablo CRUD = 0; tek isabet `DeviceManager::showPodcastWidget()`), (f) **doküman-kod çelişkisi**: `podcast-support.md:212` \"tam olarak implemente edilmiştir\" iddiası kod kanıtı olmadan **⚠️ VERIFICATION REQUIRED**"
kaynak: "Disk kanıtı taraması (2026-09-30: ayrı podcast SQL dosyası **YOK** → `.ai/.sql/mysql/` = 19 dosya / 19 CREATE DATABASE / 165 tablo; podcast 4 tablo `coremusic_musics.sql` = **759 satır / 46.111 bayt**, v8.0.0 · Date 2026-08-09 · `:10` ve `:755` 'Tables: 22 (12 musics + 4 podcast + 3 video + 3 radio)' · `:756` 'BCNF Compliant: Yes' (ADR-040 `:38` = iddia, denetim DEĞİL) · CREATE TABLE `:406, :444, :487, :516` · FK `:479, :506, :536` (3/3 iç · hepsi CASCADE) · çapraz-DB yorum satırları `:408, :489, :507-508` ve constraint'siz `:492` (`last_played_episode_id`) · indeks/unique **13** = `:426, 428, 429, 430, 431, 465, 467, 468, 469, 497, 499, 527, 529` · transcript `content LONGTEXT NOT NULL` `:520` · **LONGTEXT envanteri 6** (cms 2 · musics 2 · patch 1 · system 1), MEDIUMTEXT **0**, BLOB **0**, JSON **115** · PARTITION tek örnek `coremusic_api.sql:128` `PARTITION BY RANGE (TO_DAYS(called_at))` p_2026_01…p_2026_12 + p_future MAXVALUE, `api_calls` PK `(id, called_at)` `:115` ve **FK 0** · PHP/JS grep (`podcast_shows|podcast_episodes|podcast_subscriptions|podcast_transcripts`, node_modules/vendor hariç) = **0**; (`podcast|episode|transcript`) = **2 satır** `shared/src/Device/DeviceManager.php:501,503`; (`itunes:|enclosure|simplexml|RSSFeed`) = **0**; JS (`*.js`) = **0** · doküman `architecture/k5-veri-yonetimi/README.md:196-199` (K5.1.3.13–16) · `architecture/k15-medya-streaming/podcast-support.md:210-218` (uygulama iddiası ⚠️) · `architecture/k5-veri-yonetimi/file-system-storage.md:47-50` · `architecture/k8-servis/README.md:233` (K8.2.5) · slug satırları `.decisions/index.md:97` · `.ai/index.md:698` · `brain.md:1014` · `keys.md:290`) + web araştırması (**8 kullanılabilir sorgu / ~60 kaynak bildirimi** — 2 ek sorgu sözlük/generic döndü, **kullanılmadı ve sayıya dahil edilmedi**)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-073: Podcast Database Schema

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-09-30 — **Debate:** ✅ TAMAMLANDI (3 tur / 20 persona · 18/2/0 KABUL) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-073-podcast-database-schema` (dizin otoritesi: [[../index.md]] satır **97** — dosya adı ile birebir hizalı ✅; satır `[[../../raw/brain.md]]` önekli **kusurlu biçimdedir → `[[../../raw/brain.md]]` hedef düzeltmesi son sıfırlamaya ertelendi, bu işlemde rapor-only**)
> **İlgili kararlar:** [[ADR-072-social-database-schema]] (format/dil referansı + ortak kural seti uygulaması) · [[ADR-033-sql-normalization-strategy]] (BCNF kural seti + denetim PLANNED) · [[ADR-040-database-authority]] (18-DB sahiplik matrisi + 28 FK istisna defteri + `coremusic_musics` sahibi `music`) · [[ADR-041-database-normalization-supplementary]] (adlandırma/veri tipi/audit/N+1 tamamlayıcılar) · [[ADR-003-multi-db-bcnf]] ("DB arası FK YOK" hükmü) · [[ADR-014-multi-db-migration-strategy]] (tek migration kapısı — tüm DDL buradan) · [[ADR-002-pdo-mandatory-no-orm]] (erişim katmanı) · [[ADR-039-7-service-platform-architecture]] (A4 veri domaini sahibi) · [[ADR-081-multi-provider-data-sync]] (outbox + WAL — sınır) · [[ADR-050-multi-db-sync-strategy]] · [[ADR-026-download-service-architecture]] (indirme/ses servisi — sınır) · [[ADR-092-media-dizin-ekseni-ve-ulid]] (medya dizin ekseni — sınır) · karar dizini [[../index.md]] **satır 97**.
> **⚠️ VERIFICATION REQUIRED:** `ADR-074`–`ADR-079` (dizin satırı `:98`–`:103` var, **dosya diskte YOK**) · **`ADR-075` (AI DB şeması — diskte YOK → düz metin + ⚠️, wiki-link KURULMAZ)** · `ADR-082` (ne dosya ne dizin satırı — `:104` = 081, `:105` = 083) · `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK) · `ADR-051/053/054/055/057/060` + `ADR-065`–`ADR-071` + `ADR-080` = **14 atlanan boşluk** (hem dosya hem dizin satırı YOK — §5.1/9 ve §7.1) · BCNF "Yes" beyanı **denetlenmemiştir** (ADR-040 `:38`) · `podcast-support.md:212` implementasyon iddiası **kod kanıtsız** · podcast tablo CRUD kullanan PHP/JS = **0**.
> **Bölüm sınırı (kenetli):** ADR-072 **sosyal** şemayı, ADR-040 **DB sahipliğini + FK istisnasını**, ADR-033/041 **normalizasyon kural setini**, ADR-014 **migration kapısını**, ADR-081 **çoklu sağlayıcı senkronunu**, ADR-026 **indirme/ses servisini** yazdı; **4 podcast tablosunun envanteri, ilişki/indeks/TTL-partition politikası ve kod yüzeyi dürüst etiketi bu ADR'nindir** — hiçbiri yeniden yazılmaz. `ADR-075` (AI DB — transkripsiyon üretim servisi) **diskte YOK → düz metin + ⚠️**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 7'deki "yeni ADR ≥ 088" ile arşivin 073 slotu arasındaki **numara çakışması ADR-061/062/063/064/072 künyelerinden tekrar raporlanır, düzeltilmez**.

---

## 1. Bağlam (Context)

CoreMusic'in podcast veri düzlemi (A4) diskte **çoktan yazılmış**, vault'ta **hiç kararlaştırılmamış** durumda: ayrı bir `podcast*.sql` dosyası yok — dört tablo `coremusic_musics.sql` (759 satır / 46.111 bayt, v8.0.0) içinde, dosya footer'ının "22 tablo = 12 musics + 4 podcast + 3 video + 3 radio" sayımının tam olarak dörtte biri. Buna karşılık hiçbir ADR bu dört tabloyu karar olarak yazmıyor: ADR-040 onu `coremusic_musics` satırında "22 tablo / sahibi `music` / PLANNED (servis)" diye geçiyor, ADR-033 denetlenmemiş BCNF iddialarının arasında sayıyor, ADR-072 yalnızca `entity_type ENUM(...,'podcast',...)` polymorphic sınırını düzenliyor. Aynı anda kod yüzeyinde podcast tablo sorgulaması **0** — yani şema IMPLEMENTED, kod PLANNED; bu ayrımı da kimse yazmadı. İki drift büyüyor: (1) doküman `podcast-support.md:212` "RSS parsing, episode management ve offline support **çalışır durumdadır**" derken kodda tek bir podcast CRUD/RSS sınıfı yok; (2) transcript `LONGTEXT` stratejisi, vault'taki tek partition örneği (`api_calls`) FK'sız kurulduğu için doğrudan kopyalanamaz durumda. Bu ADR beş işi tek kayıtta kapatır: **(1)** 4 tabloyu envanterler, **(2)** ilişki/indeks/TTL-partition politikasını yazar, **(3)** IMPLEMENTED/PLANNED etiketini koyar, **(4)** dört riski (transcript şişmesi, subscription bayatlık, show↔episode cascade, feed alanı) + fallback'i yazar, **(5)** ADR-072/075/081/026 ile sınırı çizer. Kod üretmez, DDL çalıştırmaz, tablo açmaz.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-30 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | ADR-073 slotu kayıtlı mı? | [[../index.md]] `:97` → `\| [[../../raw/brain.md]] ADR-073-podcast-database-schema \| Podcast DB Schema \| Database \|` · [[../../index.md]] `:698` · [[../../raw/brain.md]] `:1014` · [[../../raw/keys.md]] `:290` | ✅ **KAYITLI** (4 indeks satırı) |
| 2 | Bu işlem öncesi dosya var mıydı? | `accepted/` dizin listesi (64 girdi) — `ADR-073*.md` **yok**; `draft/` = CLAUDE.md, `rejected/` = CLAUDE.md + index.md | ❌ **YOKTU** → bu işlemde yazılıyor |
| 3 | **Ayrı podcast SQL dosyası var mı?** | `.ai/.sql/mysql/` = **19 dosya**, `podcast*` = **0** · podcast anahtar kelime taraması yalnız `coremusic_musics.sql` içinde isabet verdi | ❌ **AYRI DOSYA YOK** → podcast `coremusic_musics` içindedir |
| 4 | Tablo envanteri | `podcast_shows` `:406` · `podcast_episodes` `:444` · `podcast_subscriptions` `:487` · `podcast_transcripts` `:516` — footer `:10`/`:755` "4 podcast" ile **4/4 birebir** | ✅ **IMPLEMENTED** (şema) |
| 5 | FK envanteri | 3 `ADD CONSTRAINT` = `fk_pe_show` `:479` · `fk_psub_show` `:506` · `fk_pt_episode` `:536` — **3/3 iç FK**, üçü de `ON DELETE CASCADE ON UPDATE CASCADE` | ✅ IMPLEMENTED · **çapraz-DB FK = 0** |
| 6 | Çapraz-DB referanslar | `author_user_id` `:408` (yorum) · `user_id` `:489` + not `:507-508` "cross-database FK not supported" · `last_played_episode_id` `:492` (yorum "FK → podcast_episodes.id" ama **constraint YOK**) | ✅ ADR-003/040'a uyumlu · ⚠️ `:492` tutarsız |
| 7 | İndeks envanteri | **13** — shows 5 (`uq_ps_slug` `:426` + 4 `idx_` `:428-431`) · episodes 4 (`uq_pe_slug` `:465` + `:467-469`) · subscriptions 2 (`:497`, `:499`) · transcripts 2 (`:527`, `:529`) | ✅ IMPLEMENTED · PK-only tablo **0** |
| 8 | BCNF beyanı | `:756` "BCNF Compliant: Yes" + tablo içi `:403, :441, :484, :513` "Normal Form: BCNF" yorumları · ADR-040 `:38` "iddiadır, denetim DEĞİL → PLANNED" | ⚠️ **denetlenmemiş beyan** |
| 9 | **Transcript yüzeyi** | `content LONGTEXT NOT NULL` `:520` · repo geneli **LONGTEXT 6** (cms `:31,:62` · musics `:176` lyrics + `:520` transcript · patch `:63` · system `:138`) · **MEDIUMTEXT 0 · BLOB 0 · JSON 115** | ✅ LONGTEXT = vault standardı · ⚠️ transcript FULLTEXT **YOK** |
| 10 | Partition örneği | `coremusic_api.sql:128` `PARTITION BY RANGE (TO_DAYS(called_at))` = 12 aylık bölme + `p_future` MAXVALUE `:141` · `api_calls` PK `(id, called_at)` `:115` · o dosyada FK 2, **`api_calls` üzerinde 0** · `coremusic_logs.sql:329` "PARTITION: Yok" | ✅ tek örnek **FK'sız** tabloda kurulmuş |
| 11 | Audit/soft-delete alanları | `is_deleted` **3** (shows/episodes/subscriptions) · `updated_at` **2** (shows/episodes) · `deleted_at` **0** · transcripts'ta `is_deleted`/`updated_at` **0** | ⚠️ ADR-033 kural 5 (created+updated+soft-delete) **eksik** (2 tablo) |
| 12 | **Kod yüzeyi (tablo CRUD)** | PHP `podcast_shows|episodes|subscriptions|transcripts` = **0** · JS = **0** · PHP `podcast|episode|transcript` = **2 satır** `shared/src/Device/DeviceManager.php:501,503` (`showPodcastWidget()` — yalnız docblock + method) | ⏳ şema IMPLEMENTED / **kod PLANNED** |
| 13 | Kod yüzeyi (RSS/feed) | PHP `itunes:|enclosure|simplexml|RSSFeed` = **0** | ⏳ **PLANNED** |
| 14 | Doküman yüzeyi | `k5-veri-yonetimi/README.md:196-199` (K5.1.3.13–16, satır numaraları `L406/L444/L487/L516` ile **birebir**) · `k15-medya-streaming/podcast-support.md:210-218` · `k5-veri-yonetimi/file-system-storage.md:47-50` (`podcasts/{podcast_id}/episodes/{episode_id}.mp3`) · `k8-servis/README.md:233` (K8.2.5) | ✅ IMPLEMENTED (doküman) |
| 15 | Doküman ↔ kod çelişkisi | `podcast-support.md:212` "tam olarak implemente edilmiştir. RSS parsing, episode management ve offline support çalışır durumdadır" ↔ kod **0** | ⚠️ **çelişki — rapor-only, doküman düzeltilmedi** |
| 16 | ADR-075 (AI DB) | `accepted/` listesinde **yok** · `draft/`/`rejected/` içinde **yok** | ❌ **diskte YOK → düz metin + ⚠️** |
| 17 | DB envanteri | `.sql/mysql/` = **19 dosya / 19 CREATE DATABASE / 165 tablo** · `coremusic_musics` CREATE TABLE = **22** (footer ile birebir) · ADR-040 `:34` = 18 dosya / 156 tablo | ⚠️ **envanter drift'i** (ADR-072 §5.1/9 ile aynı — rapor) |
| 18 | Referans formatı | [[../../.templates/adr/adr-template.md]] (Guardrail #16) · format referansı [[ADR-072-social-database-schema]] | ✅ okundu |

> **Yol notu:** görev metnindeki `.ai/templates/adr/adr-template.md` yolu **bulunamadı**; gerçek şablon `**.ai/.templates/**adr/adr-template.md` (nokta-önekli `.templates`). Düzeltme rapor-only.

### 1.2 Sorun Tanımı

1. **Şema var, karar yok.** Dört tablo diskte, dosya "22 tablo / 4 podcast" diye sayıyor, ama hangi grubun ne iş gördüğü, hangi FK'nın neden cascade olduğu ve hangi politikanın bağlayıcı olduğu hiçbir ADR'de yazılmıyor → yeni geliştirici "podcast tabloları nerede, nasıl açılır?" sorusuna vault'tan cevap bulamıyor (k5 README satır veriyor, ADR vermiyor).
2. **IMPLEMENTED/PLANNED ayrımı yok.** Şema + 4 doküman var, kod 0 → "podcast hazır" yanılgısı doğabilir; `podcast-support.md:212` bu yanılgıyı besliyor (ADR-040 `:156` servis satırı zaten PLANNED).
3. **Transcript stratejisi yazılmamış.** `LONGTEXT NOT NULL` tek başına 4GB'a kadar satır taşır; arama için FULLTEXT yok, TTL/partition yok, segment-alt-bölme yok → ilk geliştirici ya tabloyu okuyarak şişirir ya da ADR-014'ü bypass edip elle DDL yazar.
4. **Partition/LOB engeli önceden yazılmamış.** Vault'taki tek örnekte (`api_calls`) partition anahtarı PK içinde ve FK **0**; podcast'te ise 3 FK var ve `uq_pt_pair (episode_id, language)` unique key'i partition anahtarını içermek zorunda → "transcript'i partition'layalım" diyen ilk kişi kırık DDL yazar.
5. **İki veri bütünlüğü açığı:** `last_played_episode_id` `:492` FK **iddiasında** ama constraint yok; `podcast_subscriptions`/`podcast_transcripts` `updated_at` (ve transcripts `is_deleted`) taşımıyor → ADR-033 kural 5 ihlali kayıtsız.
6. **Feed/import yüzeyi yok.** Şemada `feed_url`, dış `guid`, enclosure `length/type` alanı bulunmuyor (`website_url` `:416` yalnız show web sitesi) → RSS içe aktarma/dışa yayım Apple gereksinimleriyle (benzersiz enclosure + değişmeyen GUID) eşleşmiyor.
7. **Sınır yazılı değil.** ADR-075 (AI/transkripsiyon), ADR-081 (senkron), ADR-026 (indirme), ADR-072 (sosyal `entity_type`) bu şemaya dokunabilir; sahiplik söylenmezse dördü de şemayı yeniden yazma riski taşır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "podcast database schema design shows episodes subscriptions tables best practice" · (2) "MySQL LONGTEXT large text column best practice separate table row size limit 64KB offload storage" · (3) "podcast RSS feed import database design episode GUID duplicate detection enclosure audio metadata" · (4) "user listen history playback position resume table design UNIQUE key user_id episode_id denormalized counter" · (5) "InnoDB FULLTEXT index large documents transcript search limitations vs external search engine Elasticsearch" · (6) "ON DELETE CASCADE best practices at scale risk self-referencing foreign key delete orphan cleanup" · (7) "store speech transcript in database one document per utterance segment rows versus single JSON text column tradeoffs" · (8) "Boyce Codd normal form at scale practical over-normalization junction table composite unique key performance" |
| Web Search **Konusu** | **(1)** podcast şema modelleme (show/episode/subscription tablo biçimleri) · **(2)** büyük metin kolonu tipi ve satır boyutu sınırı (TEXT vs LONGTEXT, off-page saklama) · **(3)** RSS feed içe/dışa aktarım: episode GUID, enclosure URL/length/type, tekrar tespiti · **(4)** dinleme geçmişi / kaldığı yer modeli: `(user, episode)` birleşik unique + denormalize sayaç · **(5)** transcript araması: InnoDB FULLTEXT kapasitesi ve dış arama motoruna geçiş eşiği · **(6)** `ON DELETE CASCADE` ölçek riski ve silme alternatifleri · **(7)** transcript'i tek LONGTEXT belge olarak mı, segment satırları olarak mı saklama · **(8)** BCNF/ aşırı normalizasyon dengesi + bileşik unique key'li ara tablo. |
| Web Search **Bağlamı** | CoreMusic: podcast 4 tablo `coremusic_musics.sql` içinde IMPLEMENTED, kod 0; ADR-033/040/041 kural seti var ama podcast'e uygulanmış politika yok; `content LONGTEXT` yazmış ama arama/TTL kararı yok; vault'ta tek partition örneği FK'sız. Araştırma bu dört boşluğu hedefliyor — **tablo sayısı/satır numaraları iç karar** olduğu için web yalnız *yapı, kolon tipi, GUID, cascade ve arama kuralı* için okundu (ADR-033'ün sorgularının tekrarı değil). |
| Web Search **Kısa Açıklama** | **(1)** Show ↔ episode (1:N) + episode ↔ subscription (N:M junction, birleşik unique) klasik ilişkisel kalıptır; sayım sütunları (`episode_count`, `subscriber_count`) bilinçli denormalizasyon olarak kabul edilir ama kaynak "ölçülmüş sorgu talebi yoksa ekleme" der. **(2)** `TEXT` 64KB, `MEDIUMTEXT` 16MB, `LONGTEXT` 4GB'tır; büyük değerler satır tamponundan ayrı yerde tutulur (9–12 bayt girdi), dolayısıyla satır boyutu sınırı LONGTEXT'i etkilemez — ama `max_allowed_packet` ve tam-okuma maliyeti etkilenir. **(3)** Apple her bölüm için **değişmeyen benzersiz GUID** ve benzersiz enclosure (URL + length + type) şartı koyar; GUID değişirse bölüm tekrarlanır/yanlış "yeni" işaretlenir. **(4)** `(user_id, episode_id)` birleşik unique anahtar + oyun sayacı/kaldığı yer ayrı sütun veya ayrı geçmiş tablosu; sayaçlar aggregate ile güncellenir (döngüde `COUNT` yasak). **(5)** InnoDB FULLTEXT yalnız CHAR/VARCHAR/TEXT üzerinde çalışır, `MATCH ... AGAINST` gerektirir, token uzunluğu sınırları (`innodb_ft_min_token_size`) vardır; büyük ölçek/alan-sıra sıralama gerekiyorsa Elasticsearch/Lucene tarzı dış motor önerilir. **(6)** `ON DELETE CASCADE` çocuk veri ebeveynsiz anlamsızsa doğrudur; "core business entities üzerinde cascade kullanma", veri kaybı riski ve ölçek sorunu üretir — hafif alternatif `RESTRICT`/`SET NULL` veya uygulama seviyesi temizliktir. **(7)** Tek LONGTEXT belge okuma/yazma için ucuzdur ama alan-bazlı indeks/arama ve parça parça güncelleme için pahalıdır; segment satırları (utterance/segment başına bir kayıt) arama ve zaman damgası sorgularını kolaylaştırır, JOIN maliyeti ekler. **(8)** BCNF her bağımlılığı superkey'e zorlar; 3NF yeterliyken aşırı normalizasyon join maliyeti ve okunabilirlik düşürür; junction tablo birleşik unique ile mükerrer satırı engeller. |
| Web Search **Uzun Açıklama** | **(i)** databasesample "Podcast Episode Management" şeması show/episode/subscription/season ayrımını 32 tabloya kadar genişletir; Fivetran ve DbSchema 2025/2026 listeleri aynı ilkeleri verir: önce veri modeli, sonra tablo; her tabloda PK, her ilişki için FK (veya uygulama seviyesi referans); Redgate/Medium ise adlandırma ve `created_at`/`updated_at` sözleşmesini şart koşar — CoreMusic'te bu sözleşmeler ADR-041 §2.2-a/b'de zaten yazılıdır, bu ADR kural **üretmez**, uygular. **(ii)** MySQL 9.7 Reference Manual §13.7 "Data Type Storage Requirements" açıkça der ki: satır tamponu için maksimum satır boyutu 65.535 bayttır, **BLOB/TEXT bu hesaba yalnız 9–12 bayt katkıcı olarak girer**, veri ayrı alanda saklanır; Atlassian TEXT tipi rehberi `LONGTEXT`'i 4.294.967.295 karakter / 4GB olarak verir — yani transcript'i LONGTEXT'e koymak satır boyutunu kırmaz, asıl maliyet okuma/paket boyutudur (AWS DMS örneğinde LONGTEXT → `TEXT`/`MEDIUMTEXT` küçültme tavsiyesi). **(iii)** Apple Podcasts "Podcast RSS feed requirements" sayfası her bölüm için `<enclosure>` üçlüsünün (URL, length, type) zorunlu ve **tekrarlanan enclosure URL'lerinin yok sayılacağını**, her bölümde **değişmeyen globally unique GUID** bulunmasını şart koşar; Podcasting 2.0 namespace önerileri aynıdır (GUID asla değişmemeli, yoksa bölüm aşağı akışta çoğalır) — CoreMusic şemasında buna karşılık alan **yok** (`episode_number`/`season_number` var, feed GUID yok). **(iv)** Spotify/GeeksforGeeks müzik servisi modellerinde dinleme geçmişi ayrı ilişki tablosu, "kaldığı yer" ise episode seviyesinde bir alan ya da `(user, episode)` unique satırıdır; dev.to/history-tablo tartışmasında FK'li geçmiş tablosunun orijinal satır silinince anlamı yitirdiği, sayaç yerine aggregate kullanılması gerektiği vurgulanır. **(v)** MySQL InnoDB FULLTEXT dokümantasyonu ve Severalnines/Releem rehberleri: FULLTEXT yalnız metin kolonlarında kurulur, ters indeks + gizli FTS tabloları kullanır, `innodb_ft_min_token_size` (varsayılan 3) gibi eşikler vardır; Elastic Docs ve dev.to/lobsters tartışması büyük metin araması ve relevance sıralaması büyüyünce dış motorun tercih edildiğini, ama küçük/orta ölçekte MySQL FULLTEXT'in bağımlılık eklemeden yeterli olduğunu söyler. **(vi)** HackerOne + oneuptime + StackSync + dba.stackexchange cascade yazıları aynı uyarıyı verir: cascade "çocuğun anlamı yoksa" doğrudur, **core business entities'te veri kaybı üretir**, çok seviyeli yayılım beklenmedik satırları siler ve büyük silimlerde performans maliyeti vardır; Postgres.FM 177 (schema design checklist) "at scale `ON ... CASCADE` varsayılanı tehlikeli olabilir" der. **(vii)** JSON/doküman karşılaştırmaları (redis.io, medium, pipeline2insights, stackoverflow 15367696) tek belge saklamanın kod-uyumunu ama kolon-bazlı indeks, sıkıştırma ve alan-sorgusu için pahalı olduğunu; kolon-bazlı şemanın ise alan başına migration istediğini — dengenin "aranan alan kolon, aranmayan alan belge" olduğunu yazar. **(viii)** BCNF kaynakları (kevsrobots, GeeksforGeeks, ITU Online, vldb.org 1982 PDF) superkey şartını ve **aşırı normalizasyonun join/okunabilirlik bedelini**, ITU'nun "OLTP'de 3NF sonra gerekçeli istisna" kuralını verir; junction tablo birleşik unique ile mükerrer satırı engeller. |
| Web Search **Paragraf Veri Uzun** | **8 sorgu / ~60 kaynak bildirimi** (2 ek sorgu — "storing transcript…" ve sözlük çıkışlı "storing…" — sonuçsuz/generic kaldı, **kullanılmadı**): **(1, 8)** databasesample.com podcast episode database · Postgres.FM #177 schema design checklist (YouTube) · reddit r/Database subscription schema · fivetran.com schema best practices · dbschema.com design best practices 2025 · red-gate.com top 11 · medium Database Schema Design Principles · support.microsoft.com database design basics. **(2, 7)** dev.mysql.com 9.7 Ref Manual §13.7 Storage Requirements · atlassian.com MySQL TEXT types size guide · forums.percona.com varchar/text row size limit · stackoverflow 2023481 MySQL Large VARCHAR vs TEXT · codemia.io MySQL Large VARCHAR vs TEXT · dba.stackexchange 137285 TEXT/BLOB kolon limiti · aws re:post AWS DMS LONGTEXT. **(3, 7)** podcasters.apple.com/support/823 RSS requirements · podcasting2.org namespace "Episode GUID" · tyxstudios.com what is a podcast RSS feed · sparkpod.ai podcast RSS feed 2026 · podderapp.com RSS feed podcast · podigee.com episodes GUID · quasa.io preserve GUID. **(4, 6)** dev.to design a table for historical changes · medium Design the Database for Spotify · datalemur Spotify streaming history · geeksforgeeks music streaming DB · developer.spotify.com get-recently-played · reddit r/Database history table. **(5, 8)** dev.mysql.com 17.6.2.4 InnoDB Full-Text Indexes · elastic.co full-text search docs · severalnines MySQL full-text good/bad/ugly · oneuptime MySQL full-text search 2026 · releem.com MySQL full-text indexing · lobste.rs "Don't Waste Your Time With MySQL FTS" · reddit r/devops MySQL FTS vs Elasticsearch · news.ycombinator.com 12621950. **(6, 8)** dba.stackexchange 44956 cascade explanation · hackerone.com FK ON UPDATE/ON DELETE · oneuptime MySQL ON DELETE CASCADE 2026 · stacksync.com MySQL cascading best practices · learn.microsoft.com EF Core cascade delete · stackoverflow 42228082 self-referencing cascade · datacamp.com SQL ON DELETE CASCADE · medium 10 techniques cascading deletes. **(7, 8)** stackoverflow 15367696 JSON vs column per key · pipeline2insights serialisation formats · redis.io JSON databases · medium JSON in databases hidden cost · reddit r/node JSON file vs DB · glareb.com data formats · couchbase.com JSON database · anteru.net data formats. **(8, 8)** kevsrobots BCNF · geeksforgeeks BCNF · ituonline normalized databases for scalability · blog.dataengineerthings.org BCNF · vldb.org 1982 "Reflections on Boyce-Codd Normal Form" (PDF) · oneuptime BCNF in MySQL 2026 · YouTube "Database design made easy part 5" · rmarcus.info parameterizing BCNF. |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** show/episode 1:N + subscription junction + birleşik unique endüstri kalıbidır → mevcut 4 tablo yapısı korunur, yeniden modellenmez; `episode_count`/`subscriber_count`/`play_count` **ölçülmüş talep yoksa** denormalizasyon defterine kaydedilir (ADR-041). **(2) Karar destekleniyor:** `LONGTEXT` transcript için teknik olarak uygundur (satır boyutu 65.535 hesabına girmez) → kolon tipi **değiştirilmez**; asıl risk okuma/paket boyutudur, o da §4.3/R1 ile yönetilir. **(3) Karar destekleniyor (boşluk doğrulandı):** feed GUID + enclosure üçlüsü zorunlu; şemada karşılığı **yok** → **I3/PLANNED feed alanı** kapısı açılır. **(4) Karar destekleniyor:** `(user_id, show_id)` zaten `uq_ps_pair` ile var; `last_played_episode_id` bayat/dangling riski doğrulanır → FK kararı + uygulama seviyesi doğrulama. **(5) Karar destekleniyor (iki yol):** küçük/orta ölçekte InnoDB FULLTEXT yeterli, büyüyünce dış arama motoru → **I2 FULLTEXT PLANNED** + dış motor ADR-075/AI sınırına devredilir. **(6) Karar destekleniyor (engel de doğrulandı):** üç FK'nın da `ON DELETE CASCADE` olması kaynakça "core entities'te cascade" uyarısıyla çelişir → **silme politikası ayrı kapıya bağlanır**; soft-delete zaten şemada mevcut, cascade yalnız fiziksel DELETE'te tetiklenir. **(7) Karar destekleniyor (dönüştürme):** arama/segment zaman damgası gerekirse tek belge → segment satırı; bu ADR'de **dönüşüm yapılmaz**, yalnız kapı yazılır. **(8) Karar destekleniyor:** BCNF iddiası korunur ama denetim ADR-033'te PLANNED; aşırı normalizasyon (transcript'i cümle/tablonun satırına bölmek) bu ölçekte **ret**. **İtiraz/karşıt bulgu:** dış kaynaklar CoreMusic'in tablo sayısını/sahipliğini doğrulayamaz (iç karar); "podcast tam implemente" doküman iddiası hiçbir dış kaynakla doğrulanamaz → ⚠️. |
| Web Search **Alınan Karar** | **(a)** 4 tablo envanteri yazılır (şema korunur, yeniden modellenmez; ayrı `podcast*.sql` **yok**). **(b)** İlişki: 3 iç FK `ON DELETE CASCADE` **korunur** + fiziksel silme politikası §5.1/4'e bağlanır; `last_played_episode_id` FK'sı için uygulama seviyesi doğrulama kararı (yeni FK ekleme ADR-014 kapısından, **PLANNED**); çapraz-DB FK **eklenmez** (ADR-003/040). **(c)** İndeks: mevcut **13** korunur + **I1** `podcast_episodes (show_id, status, publish_date)` feed kompoziti + **I2** `podcast_transcripts` FULLTEXT `content` — ikisi de ADR-014 + `EXPLAIN` şartıyla PLANNED. **(d)** Transcript: `LONGTEXT` **korunur**; **partition/LOB stratejisi ERTELENDİ** (FK yasağı + unique-key içeriği zorunluluğu; vault örneği `api_calls` FK'sız) → kısa vade: okuma disiplini + `max_allowed_packet` denetimi, uzun vade: segment-alt-bölme veya dış depo **yeni ADR** (kapı §5.1/5-6). **(e)** Feed alanları (`feed_url`, dış `guid`, enclosure length/type) **PLANNED** (§5.1/7). **(f)** Dürüst etiket: şema+doküman IMPLEMENTED / kod PLANNED; `podcast-support.md:212` iddiası ⚠️. **(g)** Sınır = ADR-072 · ADR-075 (düz metin+⚠️) · ADR-081 · ADR-026. |
| Web Search **Sonuç** | **8/8 araştırmada karar destekleniyor** (4 yapı + 2 politika + 2 engel): show/episode/subscription kalıbı (1) · `LONGTEXT` transcript uygunluğu (2) · feed GUID boşluğu (3) · `(user, show)` unique + bayatlık riski (4) · FULLTEXT vs dış motor iki yolu (5) · cascade riski (6) · tek belge ↔ segment dengesi (7) · BCNF/bileşik key (8). **Dört gerilim açıkça kabul edildi:** (1) sayım sütunları denetimsiz denormalizasyondur → ADR-041 defteri; (2) 3 FK'nın tamamı CASCADE → silme politikası §5.1/4'te ayrı kapı; (3) `podcast-support.md` implementasyon iddiası kod 0 ile çelişir → ⚠️ rapor; (4) TTL/partition süresi ve segment-eşiği **bu ADR'de sayılmadı** → debate kapısı. **⚠️ VERIFICATION REQUIRED:** TTL saklama süresi · segment-alt-bölme eşiği · dış arama motoru seçimi (ADR-075) · `ADR-074`–`ADR-079`/`ADR-082`–`ADR-088` dosyaları. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar (ADR-002 / ADR-003 / ADR-014 / ADR-026 dahil) |
| ADR-040 sınırı | 18-DB sahiplik matrisi, tek yazar servisi ve 28 FK istisna defteri bağlayıcıdır; `coremusic_musics` sahibi `music` (`:156`), **podcast çapraz FK istisnası defterde yoktur** → yenisi eklenemez |
| ADR-033 / ADR-041 sınırı | Normalizasyon kural seti + tamamlayıcı 6 kural bağlayıcıdır; bu ADR kural **üretmez**, şemaya uygular ve açığı defterine yazar (ör. `updated_at`/`is_deleted` eksikliği) |
| ADR-072 sınırı | Sosyal şema + `entity_type ENUM(...,'podcast',...)` polymorphic sınırı bu ADR'de **yeniden yazılmaz**; podcast sadece o ENUM'un bir değeri olarak kalır |
| ADR-075 sınırı | AI DB / transkripsiyon üretim servisi **diskte YOK → düz metin + ⚠️**; bu ADR transcript üretimini, model versiyonlamasını ve AI maliyetini **kararlaştırmaz** |
| ADR-081 / ADR-026 sınırı | Outbox+WAL senkronu (ADR-081) ve indirme/ses servisi (ADR-026) kapsam dışı; bu ADR çapraz-DB tutarlılık ve indirme akışı önermez |
| Migration tek kapısı | Her şema değişikliği yalnız ADR-014 özel PHP runner'ından geçer; bu ADR elle DDL yazmaz |
| Kod kanıtı | Podcast tablo sorgulayan PHP/JS = **0** → "podcast servisi çalışıyor / özellik hazır" iddiası yazılmaz |
| Vault'a yazma yetkisi | Bu işlem yalnız bu ADR dosyasını + `log.md` append'ini yazar; `index.md`/`brain.md`/`keys.md`/`podcast-support.md`/`ADR-040` düzeltmeleri **sonraki vault reset'ine ertelenir** (In-Place Refactoring + rapor-only) |
| Numara çakışması | Kural 7 "yeni ADR ≥ 088" ↔ arşiv 073 slotu — ADR-061/062/063/064/072 künyelerinden **aynı çakışma tekrar raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

CoreMusic'in podcast veri şeması **bu ADR'de tek kayıtta** sabitlenir: **(a)** **4 tablo** (`podcast_shows` · `podcast_episodes` · `podcast_subscriptions` · `podcast_transcripts`) ayrı dosyada değil `coremusic_musics.sql` içindedir — BCNF + ADR-033/040/041 + ADR-072 ile **aynı ortak kural seti** uygulanır, tablo/kolon adları değişmez; **(b)** **mevcut SQL envanteri** yazılır (dosya: 19 dosyalık klasörde podcast dosyası YOK; tablo 4, iç FK 3, çapraz FK 0, indeks 13); **(c)** **ilişki + indeks + TTL/partition** politikası: 3 iç FK cascade korunur, 2 yeni indeks (I1/I2) ADR-014 kapısından PLANNED, **transcript `LONGTEXT` → partition/LOB stratejisi gerekçeli olarak ertelenir** (FK yasağı + unique-key içeriği + vault örneğinin FK'sız olması); **(d)** **dürüst etiket**: şema + doküman IMPLEMENTED, kod PLANNED, BCNF beyanı denetlenmemiş, doküman iddiası ⚠️; **(e)** **sınır** = ADR-072 (sosyal) · ADR-075 (AI — düz metin + ⚠️) · ADR-081 (sync) · ADR-026 (indirme) — tekrar yok. Karar kod üretmez, tablo açmaz, indeks eklemez.

### 2.1 Neden Bu Seçenek?

Sorun *eksik şema değil, eksik karar*: dört tablo zaten diskte ve k5 veri dokümanında satır satır kayıtlı. Bu ADR sıfırdan podcast DB çizmek yerine mevcut şemayı **envanterler, politika yazar ve dürüst etiketler**: (1) SSOT korunur — dosya adı, tablo adları ve kolon adları değişmez (In-Place Refactoring), (2) "ayrı podcast dosyası var mı?" sorusunun cevabı (**yok**) tek yerde bağlanır, kimse `podcast.sql` aramaya devam etmez, (3) `LongTEXT` + cascade + feed alanları üç boşluğu ilk kez gerekçeli hale getirir — ikisi dış kaynaklarla destekli (MySQL storage requirements + Apple RSS şartları), (4) partition'ı **bilerek ertelemek** teknik imkânsızlıkla gerekçelendirilir: InnoDB'de FK'lı tabloya kullanıcı partition'ı konamaz ve partition anahtarı unique/PK içeri zorunludur; vault'taki tek örnek (`api_calls`) FK'sızdır, dolayısıyla "orada oldu, burada da olur" çıkarımı yanlıştır — bu, ilk geliştiricinin kırık DDL yazmasını önleyen asıl karardır, (5) `podcast-support.md:212` "tam implemente" iddiası ile kod 0 arasındaki çelişki görünür kılınır, sessizce yutulmaz, (6) ADR-075 diskte olmadığı için ona wiki-link **kurulmaz** — uydurma hedef linklemek yerine düz metin + ⚠️ yazılır.

### 2.2 Teknik Detaylar

**(a) Tablo envanteri — 4 tablo (şema korunur, yeniden modellenmez):**

| # | Tablo | Dosya satırı | Aday anahtar / unique | İndeks | Amaç | Etiket |
|---|-------|--------------|------------------------|--------|------|--------|
| 1 | `podcast_shows` | `:406` | `uq_ps_slug (slug)` `:426` | `idx_ps_author` `:428` · `idx_ps_category` `:429` · `idx_ps_language` `:430` · `idx_ps_status` `:431` | Show künyesi: `title/slug/description/cover_image/author_name/category/language/website_url` + sayaç `episode_count` `:417`, `subscriber_count` `:418` + `status CHECK ('draft','active','paused','archived')` `:433` | ✅ IMPLEMENTED (şema) |
| 2 | `podcast_episodes` | `:444` | `uq_pe_slug (show_id, slug)` `:465` | `idx_pe_show` `:467` · `idx_pe_publish (publish_date DESC)` `:468` · `idx_pe_status` `:469` | Bölüm: `audio_url` `:450` (NOT NULL), `audio_format CHECK (mp3/aac/ogg/flac/wav)` `:472`, `audio_size_bytes` `:452`, `duration_seconds` `:453`, `episode_number`/`season_number` `:454-455`, `publish_date` `:456`, `play_count` `:458` | ✅ IMPLEMENTED (şema) |
| 3 | `podcast_subscriptions` | `:487` | `uq_ps_pair (user_id, show_id)` `:497` | `idx_ps_sub_show` `:499` | Kullanıcı↔show aboneliği: `notify_new_episode` `:491`, `last_played_episode_id` `:492` (**constraint'siz**), `is_deleted` `:493`, `created_at` `:494` — **`updated_at` YOK** | ✅ şema / ⚠️ audit eksik |
| 4 | `podcast_transcripts` | `:516` | `uq_pt_pair (episode_id, language)` `:527` | `idx_pt_language` `:529` | Transkript: `content LONGTEXT NOT NULL` `:520`, `format` `:521`, `model_version` `:522`, `confidence DECIMAL(3,2)` `:523`, `generated_at` `:524` — **`is_deleted`/`updated_at` YOK** | ✅ şema / ⚠️ audit eksik |

- **Toplam 4** — dosya footer'ı `:10`/`:755` "12 musics + 4 podcast + 3 video + 3 radio = 22" içinde podcast dilimi **4/4 birebir**; dosyadaki toplam CREATE TABLE = 22 (sayı doğrulandı).
- **İsimlendirme (ADR-041 §2.2-a ile hizalı):** tablo `podcast_` **alan öneki** sabit ✅ · PK `id INT UNSIGNED` (`INT UNSIGNED` = ADR-041 veri tipi standardı ✅) · FK `<nesne>_id` ✅ · `idx_`/`uq_` önekleri tutarlı ✅ · **tek sapma:** ADR-041 "PK `BINARY(16)` UUIDv7" standardı — podcast/video/radio grubu dosya başlığında (`:11`) **açıkça `INT UNSIGNED` ilan edilmiştir** → mevcut durum grandfathered, **yeni podcast tablosunda UUID'ye geçiş ayrı kapı** (§5.1/8).
- **Audit açığı (ADR-033 kural 5):** `deleted_at` podcast bölgesinde **0** (dosya başlığı `:8` "Soft Delete: is_deleted + deleted_at" iddiasıyla çelişir) · `updated_at` **2/4** · `is_deleted` **3/4** (transcripts'ta yok) → defter kaydı §5.1/3.
- **Denormalize sayaçlar (ADR-041 defterine girmelidir):** `episode_count` `:417` · `subscriber_count` `:418` · `play_count` `:458` — üçü de **denetimsiz denormalizasyon**.

**(b) İlişki politikası — 3 iç FK + 0 çapraz-DB FK:**

| FK | Dosya satırı | Hedef | Davranış | Durum |
|----|-------------|-------|----------|-------|
| `fk_pe_show` | `:478-479` | `podcast_shows(id)` | `ON DELETE CASCADE ON UPDATE CASCADE` | ✅ iç — korunur |
| `fk_psub_show` | `:505-506` | `podcast_shows(id)` | `ON DELETE CASCADE ON UPDATE CASCADE` | ✅ iç — korunur |
| `fk_pt_episode` | `:535-536` | `podcast_episodes(id)` | `ON DELETE CASCADE ON UPDATE CASCADE` | ✅ iç — korunur |
| `author_user_id` (show) | `:408` | `coremusic_auth.users.id` (yorum) | **constraint YOK** | ✅ ADR-003/040 uyumlu |
| `user_id` (subscription) | `:489` + not `:507-508` | `coremusic_auth.users.id` (yorum) | **constraint YOK** — "cross-database FK not supported" | ✅ ADR-003/040 uyumlu |
| `last_played_episode_id` | `:492` | `podcast_episodes.id` (yorum "FK") | **constraint YOK — tutarsız** | ⚠️ karar §2.2/b-devam |

- **Kural:** podcast grubu **ADR-003'ün "DB arası FK YOK" hükmüne uyan az sayıdaki gruptur** — ADR-040 `:49` 28 istisna defterinde podcast satırı **yoktur**, yenisi eklenemez (CI statik kapısı ADR-040'ın işidir).
- **Zincir etkisi:** show fiziksel olarak silinirse `episodes → transcripts` ve `subscriptions` CASCADE ile silinir (3 seviye) → risk R3, politika §5.1/4.
- **`last_played_episode_id` kararı:** (i) uygulama seviyesi doğrulama (varsayılan — FK **eklenmez**, çapraz/uzak referans maliyeti yok), (ii) `podcast_episodes` aynı DB olduğu için **iç FK eklenebilir** → ancak bu DDL'dir = ADR-014 kapısı + `ON DELETE SET NULL` tercih edilir (kullanıcı ilerisi kaybolsun istemez). Her iki yol da **PLANNED**, bu ADR'de DDL yok.

**(c) İndeks politikası — mevcut 13 + 2 PLANNED iyileştirme:**

*Mevcut (IMPLEMENTED):* toplam **13** girdi — shows 5 · episodes 4 · subscriptions 2 · transcripts 2 (satır listesi §1.1/7). PK hariç indekssiz tablo **0** → ADR-033 kural 3'ün "PK-only tablo kabul edilmez" eşiği karşılanıyor. `idx_pe_publish (publish_date DESC)` `:468` MySQL 8 descending index destekli şekilde yazılmış ✅.

| # | İndeks (PLANNED) | Neden | Kanıt | Kapı |
|---|------------------|-------|-------|------|
| **I1** | `podcast_episodes (show_id, status, publish_date)` | Show feed sorgusu `WHERE show_id=? AND status='published' ORDER BY publish_date DESC`; mevcut `idx_pe_show` + `idx_pe_status` + `idx_pe_publish` üç ayrı tek sütunlu indeks sıralamayı birlikte taşıyamaz | §1.3/1 + §1.3/8 (indirilen kolon/sıra indeksi) · ADR-041 indeks kuralı | ADR-014 runner + `EXPLAIN` |
| **I2** | `podcast_transcripts` FULLTEXT `ft_pt_content (content)` | Transkript araması (`MATCH ... AGAINST`); bugün FULLTEXT **0** → arama tablo taraması | §1.3/5 (InnoDB FULLTEXT yalnız TEXT kolonunda çalışır, token eşikleri vardır) | ADR-014 runner + `EXPLAIN` |

- **I2 alternatifi (dış motor):** alan-sıra sıralaması / relevance gerekiyorsa Elasticsearch benzeri dış indeks → **ADR-075 (AI DB) sınırı = diskte YOK → düz metin + ⚠️**, bu ADR'de karar alınmaz.
- **`uq_ps_pair` zaten kullanıcı sorgusunu karşılıyor:** abonelik listesi `WHERE user_id=?` için birleşik unique `(user_id, show_id)` prefix'i yeterli → **I3 gereksiz sayıldı** (yeni indeks ekleme "her kolona index" yasağına takılmasın).
- **Ölçüm şartı:** I1/I2 de `EXPLAIN` ile teyit edilmeden "hızlandırdı" denmez (ADR-033 workload denetimi PLANNED ile aynı kapı).

**(d) Transcript için büyük metin / TTL / partition stratejisi — gerekçeli erteleme:**

| Faz | Mekanizma | Durum | Gerekçe / Engel |
|-----|-----------|-------|-----------------|
| **Şimdiki (korunan)** | `content LONGTEXT NOT NULL` — tek satır = bir `(episode, language)` transkripti; segment/olgu **satıra bölünmez** | ✅ IMPLEMENTED | MySQL 9.7 §13.7: LONGTEXT satır tamponu 65.535 bayt hesabına yalnız 9–12 bayt girer → satır boyutu kırılmaz; 4GB tavanı mevcut içerik için fazlasıyla yeterli |
| **Kısa vade (okuma disiplini)** | Transkript **parça parça okunur** (sayfa/konum aralığı), tam `content` listeleme sorgularında **seçilmez**; `max_allowed_packet` denetimi; ADR-041 `SELECT *` yasağı ile tutarlı | ⏳ PLANNED (kod katmanı) | §1.3/2 (asıl maliyet okuma + paket boyutu) · §1.3/7 (tek belge = ucuz yazım, pahalı alan-sorgusu) |
| **Arama** | **I2** FULLTEXT (§2.2/c) — küçük/orta ölçekte yeterli | ⏳ PLANNED | §1.3/5 |
| **Uzun vade — partition** | Transkript tablosunda aylık/üretim tarihine göre RANGE partition + `DROP PARTITION` TTL | ⏳ **ERTELENDİ (ön koşul bağlı)** | **Engel 1:** InnoDB'de kullanıcı partition'lı tablo **FK taşıyamaz** → `fk_pt_episode` `:536` buna engel. **Engel 2:** partition anahtarı **tüm PK/unique key'de** bulunmalı → `PRIMARY KEY (id)` + `uq_pt_pair (episode_id, language)` ile `created/generated_at`'e partition **yapılamaz**; `episode_id` üzerinden partition ise yeniden bölmeyi anlamsız kılar. **Kanıt:** vault'taki tek örnekte `coremusic_api.sql:115` PK `(id, called_at)` + **FK 0** — kural ihlalsiz kurulabilmiştir. |
| **Uzun vade — LOB/segment** | (i) transcript'i segment/ cümle satırlarına bölme, (ii) `LONGTEXT` yerine dış depo + DB'de yalnız `content_uri`, (iii) `TEXT`/`MEDIUMTEXT`'e küçültme | ⏳ **YENİ ADR KAPISI** | §1.3/2 (DMS örneği LONGTEXT → TEXT/MEDIUMTEXT) + §1.3/7 (segment satırı arama/timestamp için avantajlı) — **eşik rakamı bu ADR'de yazılmadı** (⚠️ debate) |

- **TTL süresi bu ADR'de sayılmadı** → ⚠️ debate kapısı (ADR-072 ile aynı disiplin).
- Bu ADR **hiçbir DDL içermez**; tüm değişiklikler ADR-014 tek kapısından yürür.

**(e) Feed / import yüzeyi (PLANNED — boşluk):**

| Alan | Durum | Dayanak |
|------|-------|---------|
| Show `feed_url` | ❌ şemada YOK (`website_url` `:416` yalnız show web sitesi) | §1.3/3 |
| Bölüm dış `guid` | ❌ YOK — `episode_number`/`season_number` var | §1.3/3 (Apple: değişmeyen globally unique GUID) |
| Enclosure `length` / `type` | ⚠️ kısmi — `audio_size_bytes` `:452` + `audio_format` `:472` var, **tekilleştirilmiş enclosure URL doğrulaması yok** | §1.3/3 (Apple: tekrarlanan enclosure URL yok sayılır) |
| Feed içe/dışa aktarım servisi | ⏳ **PLANNED** (kod 0) | §1.1/12-13 |

→ Üç alan da **§5.1/7** kapısına yazılır; bu ADR şemaya kolon **eklemez**.

**(f) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

| Katman | Öğe | Durum | Kanıt |
|--------|-----|-------|-------|
| Şema dosyası | 4 tablo + 13 indeks + 3 FK | ✅ **IMPLEMENTED** | `coremusic_musics.sql` 759 satır / 46.111 bayt |
| Veri dokümanı | K5.1.3.13–16 tablo kayıtları | ✅ **IMPLEMENTED** (doküman) | `architecture/k5-veri-yonetimi/README.md:196-199` (L406/L444/L487/L516 birebir) |
| Akış/dizilim dokümanı | Podcast support + dosya ağacı + servis satırı | ✅ IMPLEMENTED (doküman) | `k15-medya-streaming/podcast-support.md` · `k5-.../file-system-storage.md:47-50` · `k8-servis/README.md:233` |
| Widget | `showPodcastWidget()` | ✅ sınırlı (yalnız görünürlük) | `shared/src/Device/DeviceManager.php:501,503` |
| **Kod (okuma/yazma)** | show/episode/subscription/transcript **CRUD** | ⏳ **PLANNED** | PHP+JS grep = **0** sorgulama |
| **Kod (RSS/feed)** | feed parse + episode import | ⏳ **PLANNED** | `itunes:|enclosure|simplexml` = **0** |
| **Servis iddiası** | "tam implemente" | ⚠️ **VERIFICATION REQUIRED** | `podcast-support.md:212` ↔ kod 0 |
| BCNF denetimi | 4 tablo determinant denetimi | ⏳ **PLANNED** | ADR-040 `:38` (beyan ≠ denetim) |
| Servis sahibi | `music` (ADR-040 `:156`) | ⏳ PLANNED (servis) | ADR-040 |

**(g) Sınır (tekrar yok):** sosyal şema + polymorphic `entity_type` → [[ADR-072-social-database-schema]] · **AI DB / transkripsiyon üretimi → `ADR-075` (diskte YOK → düz metin + ⚠️, wiki-link kurulmaz)** · çoklu sağlayıcı senkronu (outbox + WAL) → [[ADR-081-multi-provider-data-sync]] · indirme/ses servisi → [[ADR-026-download-service-architecture]] · DB sahipliği + FK istisna defteri → [[ADR-040-database-authority]] · normalizasyon kural seti → [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]] · migration tek kapısı → [[ADR-014-multi-db-migration-strategy]] · medya dizin ekseni → [[ADR-092-media-dizin-ekseni-ve-ulid]] · ADR-026'nın "`podcast_subscriptions` abonelik değil içerik listesi" ifadesi (`:63`) **şema ile çelişir** (tabloda `user_id + show_id + notify_new_episode` = abonelik) → ADR-026 frozen olduğundan **düzeltilmez, rapor-only** · `ADR-074`–`ADR-079` (radio/AI/video/studio/CMS/i18n şemaları, başlıklar `index.md:98-103`'tedir) ve `ADR-083`–`ADR-088` (başlıklar `index.md:105-110`'dadır) **dosyalar diskte YOK → düz metin + ⚠️** (`ADR-082` ne dosya ne dizin satırı: `:104` = 081, `:105` = 083).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Podcast için ayrı `coremusic_podcast.sql` dosyası aç** (19.→20. dosya) | Podcast sahipliği görünür olur | `coremusic_musics` footer sayımı (22 = 12+4+3+3) kırılır, 4 tablo dosyalar arası taşınır = In-Place Refactoring ihlali + k5 README `L406/L444/L487/L516` satır referansları + ADR-040 `:156` sahiplik satırı bozulur; envanter drift'i (18↔19) 20'ye çıkar | Dosya adı/değişimi onaysız yapılamaz (kural 18) + tek seferde 3 vault kaydı kırılır → **ret**; ayrı dosya kararı **yeni ADR + onay** ister |
| 2 | **Şemayı bu ADR'de baştan modelle** (4 tablo → BCNF denetimi + yeniden yazım) | Denetimsiz "BCNF Yes" beyanı kapanırdı | ADR-033'ün tablo denetimi hiç çalıştırılmadan şema değişimi **kör** olur; kod 0 iken değişimin doğrulanacak kanıtı yok; tablo/kolon adları kutsal | Denetim ayrı PLANNED iş (ADR-033) → **ret**; bu ADR yalnız politika + etiket yazar |
| 3 | **Transcript'i hemen partition'lı aç / LOB'a taşı** | TTL baştan hazır olurdu | InnoDB'de FK'lı tabloya kullanıcı partition'ı **konamaz** (`fk_pt_episode`) + partition anahtarı PK/unique içeri zorunlu → mevcut yapıda **imkânsız**; LOB/dış depo seçimi ADR-075 (AI DB) alanıdır ve o dosya **diskte YOK**; elle DDL ADR-014 tek kapısını bypass eder | Teknik engel + süreç ihlali + kanıtsız eşik → **ret**; iki fazlı erteleme §2.2/d'ye yazıldı |
| 4 | **Transkripti cümle/segment satırlarına böl (1 satır → N satır)** | Alan-sıra araması + zaman damgası sorgusu kolaylaşır | Şema değişimi + mevcut tek-satır okuma yolunun kırılması; kod 0 iken iki okuma yolu birden desteklenir; BCNF dışı bir "segment" tablosu için iş kuralı (eşik, dil, model versiyonu) henüz yok | Eşik rakamı kanıtlanmadı (⚠️ debate) → **§2.2/d "YENİ ADR KAPISI"** olarak bırakıldı, bu ADR'de **ret** |
| 5 | **Kodu bu ADR'ye bağla** (şema + podcast servisi tek adımda) | Tek seferde biter | Şablon §2 gereği ADR **kod üretmez**; `podcast-support.md:212` zaten kanıtsız bir "hazır" iddiası taşırken servis kodunu sahipliğe önceden bağlamak aynı yanılgıyı büyütür | Yetki/süreç ihlali → **ret**; kod ayrı adım §5.1/6 |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Podcast şeması ilk kez karar olarak kayıtlı:** 4 tablo / 3 FK / 13 indeks / 2 PLANNED indeks / ertelenmiş partition gerekçesi tek dosyada; "podcast tabloları nerede?" sorusunun cevabı (**ayrı dosya yok → `coremusic_musics.sql`**) ilk satırda.
- **Çapraz-DB FK yokluğu bir avantaj olarak yazıldı:** podcast, ADR-003'ün "DB arası FK YOK" hükmüne uyan grup; ADR-040 istisna defterine **eklenecek 29. satır yok** — kimse "podcast de exception ekle" diye düşünmez.
- **Partition engeli önceden yazıldı:** FK yasağı + unique-key içeriği + vault örneğinin FK'sız olması üçü birlikte → "orada partition var, burada da yapalım" diyen ilk geliştirici kırık DDL yazmaz.
- **Feed boşluğu görünür:** Apple gereksinimlerinin (değişmeyen GUID + enclosure üçlüsü) şemadaki karşılığı yoktu → I3/PLANNED kapıya bağlandı.
- **Dürüst etiket:** şema+doküman IMPLEMENTED / kod PLANNED ayrımı, `podcast-support.md:212` çelişkisi ve ADR-026 `:63` ifadesinin şema ile uyumsuzluğu hem §1.1'de hem §5.1'de rapor-only kayıtlı.
- **Sınır net:** ADR-072/081/026 bu şemaya dokunmaz; ADR-075'e wiki-link **kurulmadı** (diskte yok) → kırık link üretilmedi.

### 4.2 Olumsuz Sonuçlar

- **Dört drift düzeltilmedi** (rapor-only): `podcast-support.md:212` implementasyon iddiası · `deleted_at` iddiası ↔ 0 kullanım · ADR-026 `:63` "abonelik değil içerik listesi" · envanter 18 ↔ 19 dosya — reset'e ertelendi; arada vault'ta çelişkili satırlar okunmaya devam eder.
- **TTL süresi + segment eşiği yazılmadı:** saklama süresi ve "kaç kelimeden sonra bölünür" rakamları kanıtlanmadı → ⚠️; debate öncesi "transcript'i ne kadar tutuyoruz?" cevapsız kalır.
- **BCNF hâlâ beyan düzeyinde:** 4 tablo için determinant denetimi çalıştırılmadı (ADR-033/040 PLANNED) → bu ADR şemanın doğru olduğunu **iddia etmez**, yalnız kaydeder.
- **Kod PLANNED olarak kaldı:** I1/I2 indeksleri ve okuma disiplini kod olmadan doğrulanamaz (`EXPLAIN` için üretim sorgusu gerekir).
- **Bir ADR daha:** vault'a tek dosya eklendi; indeks/brain/keys satırları zaten vardı ama `[[../../raw/brain.md]]` hedef düzeltmesi bu işlemde yapılmadı (§5.1/10).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **Transcript şişmesi** — `LONGTEXT` satırı sınırsız büyüyünce tam-okuma + `max_allowed_packet` maliyeti; FULLTEXT yokken arama = tablo taraması | 4 (çok olası) | 3 (orta) | §2.2/d kısa vade okuma disiplini + I2 FULLTEXT (§5.1/2) + uzun vade segment/LOB **yeni ADR** (§5.1/5); `EXPLAIN` şartı |
| R2 | **Subscription bayatlık** — `subscriber_count`/`episode_count`/`play_count` denetimsiz sayaçlar + `last_played_episode_id` constraint'siz → silinmiş bölüm referansı/yanlış sayaç | 4 (çok olası) | 3 (orta) | Üç sayaç ADR-041 denormalizasyon defterine kaydedilir (§5.1/3) + `last_played_episode_id` uygulama seviyesi doğrulama / `SET NULL` FK (§5.1/4) + sayaç aggregate ile yenileme (ADR-041 §2.2-f/6) |
| R3 | **show↔episode cascade** — 3 seviyeli `ON DELETE CASCADE` show fiziksel silinince `episodes → transcripts` ve `subscriptions`'ı da siler; transkript silimi **geri alınamaz** (AI üretim maliyeti) | 3 (olası) | 4 (yüksek) | Şemada `is_deleted` zaten var → **soft-delete varsayılan**; fiziksel show silimi yalnız ADR-014 + onayla; cascade `RESTRICT/SET NULL`'e çevirme kararı §5.1/4 (kaynak: §1.3/6 "core entities'te cascade" uyarısı) |
| R4 | **Feed alanı boşluğu** — dışa/içe aktarımda bölüm tekrarı / "yeni" yanlış işaretlenmesi (`guid` yok, enclosure doğrulaması yok) | 3 (olası) | 3 (orta) | §2.2/e + §5.1/7: `feed_url` / dış `guid` / enclosure doğrulaması ADR-014 kapısından PLANNED; Apple şartları §1.3/3 |
| R5 | **"Podcast hazır" yanılgısı** — `podcast-support.md:212` "tam implemente" diyor, kod 0 | 4 (çok olası) | 3 (orta) | §1.1/15 + §2.2/f tablosu: kod satırı **0**; doküman düzeltmesi §5.1/9 (reset); ADR-040 servis satırı PLANNED kalır |
| R6 | **Yeni çapraz FK sızması** — podcast tabloya bakan geliştirici `coremusic_auth.users`'a FK ekler | 3 (olası) | 4 (yüksek) | ADR-040 CI statik kapısı + bu ADR §2.2/b "yenisi eklenemez" satırı; ihlal → revert + log ERROR |

### 4.4 Fallback (geri birleşim / geri dönüş)

Debate **RED** çıkarsa ya da karar değiştirilirse: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `index.md:97`/`index.md:698`/`brain.md:1014`/`keys.md:290` satırları **silinmez** (değişmez kalır), `log.md`'ye `ADR-073 RED (debate …)` append edilir. Bu durumda (i) 4 tablo şeması **dosyada kalmaya devam eder** (SQL dosyası bu ADR'den bağımsızdır, dokunulmaz), (ii) I1/I2 indeksleri, feed alanları ve partition fazları **kural olmaktan çıkar** (öneri statüsüne döner), (iii) IMPLEMENTED/PLANNED etiketi kalksa bile §1.1'deki 0/0/2/0 kod sayımları **bağımsız bulgu olarak geçerlidir**, (iv) `podcast-support.md:212` çelişkisi yine raporlanır. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

### 4.5 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| Format/dil referansı + ortak kural uygulaması | [[ADR-072-social-database-schema]] | ✅ aynı iskelet + aynı etiket disiplini |
| BCNF kural seti + denetim PLANNED | [[ADR-033-sql-normalization-strategy]] | ✅ uygulandı (kural 3 indeks eşiği karşılanır; kural 5 audit açığı §5.1/3) |
| DB sahipliği + 28 FK istisna defteri | [[ADR-040-database-authority]] (`:34` · `:38` · `:49` · `:156`) | ✅ podcast = `music` sahibi · **çapraz FK istisnası YOK** (uyumlu) |
| Adlandırma/veri tipi/audit/N+1 | [[ADR-041-database-normalization-supplementary]] §2.2 (a–f) | ✅ uygulandı · sayaçlar deftere §5.1/3 |
| "DB arası FK YOK" hükmü | [[ADR-003-multi-db-bcnf]] §2.2 | ✅ podcast'te ihlal **0** |
| Migration tek kapısı | [[ADR-014-multi-db-migration-strategy]] | ✅ I1/I2 + feed + FK tüm DDL buradan |
| Erişim katmanı | [[ADR-002-pdo-mandatory-no-orm]] | ✅ kod PLANNED iken de bağlayıcı |
| Servis sahipliği | [[ADR-039-7-service-platform-architecture]] (A4) | ⚠️ podcast servisi PLANNED |
| Outbox + WAL senkronu | [[ADR-081-multi-provider-data-sync]] | ✅ sınır (kapsam dışı) |
| İndirme/ses servisi | [[ADR-026-download-service-architecture]] `:63` | ⚠️ ifade şema ile çelişir → frozen, rapor-only |
| Medya dizin ekseni | [[ADR-092-media-dizin-ekseni-ve-ulid]] | ✅ sınır (`podcasts/{id}/episodes/{id}.mp3` dizin kuralı ile ilişkili) |
| Sosyal polymorphic `entity_type` | [[ADR-072-social-database-schema]] | ✅ sınır (`'podcast'` yalnız ENUM değeri) |
| **AI DB / transkripsiyon üretimi** | **`ADR-075`** | ⚠️ **diskte YOK → düz metin + ⚠️ (wiki-link kurulmadı)** |
| Dizin slug satırı | [[../index.md]] satır **97** | ✅ hizalı; `[[../../raw/brain.md]]` hedef düzeltmesi §5.1/10'da ertelendi |
| Eksik numaralar (dosya + dizin satırı yok) | `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` | ⚠️ **14 numara** → §5.1/9 + §7.1 (rapor-only) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Debate (3 tur / 20 persona) + Tech Lead onayı — TTL süresi, segment eşiği ve cascade politikası kapıları dahil | MO + persona | 1 gün | ✅ **TAMAMLANDI** — 18/2/0 KABUL (§7.2) |
| 2 | **I1** feed kompoziti (`podcast_episodes (show_id, status, publish_date)`) + **I2** transcript FULLTEXT (`content`) — ADR-014 tek kapı + `EXPLAIN` teyidi | Data Engineer | 0.5 gün | ⏳ PLANNED |
| 3 | Audit açığı + sayaç defteri: `updated_at`/`is_deleted` eksikliğini (subscriptions, transcripts) ve `episode_count`/`subscriber_count`/`play_count` sayaçlarını ADR-033/041 defterlerine **kayıt** (DDL değil, kayıt) | Data Engineer | 0.5 gün | ⏳ PLANNED |
| 4 | **Silme politikası:** (4a) `last_played_episode_id` → uygulama seviyesi doğrulama **ya da** iç FK `ON DELETE SET NULL` (ADR-014) · (4b) 3 seviyeli cascade için **soft-delete varsayılan** + fiziksel show silimi için onay kapısı; `RESTRICT/SET NULL`'e çevirme ayrı DDL kararı | Data + Backend | 1 gün | ⏳ PLANNED |
| 5 | **Uzun vade TTL/LOB:** FK fazı + partition ön koşulu sonrası (i) aylık RANGE + `DROP PARTITION` **ya da** (ii) segment-alt-bölme / dış depo — her ikisi için **eşik rakamı ölçülür ve YENİ ADR** açılır | Data Engineer + MO | 3 gün | ⏳ PLANNED (**⚠️ eşik rakamı bu ADR'de yok**) |
| 6 | Podcast kod yüzeyi: repository/servis katmanı (show/episode/subscription/transcript CRUD + RSS parse/episode import) + entegrasyon testi (show → episode → subscription akışı) | Backend + QA | 5 gün | ⏳ PLANNED |
| 7 | **Feed alanları:** `feed_url` · dış `guid` · enclosure `length/type` doğrulaması (Apple şartları §1.3/3) — ADR-014 kapısı | Data + Backend | 1 gün | ⏳ PLANNED |
| 8 | **Kimlik tipi kararı:** podcast/video/radio grubunun `INT UNSIGNED` PK'ları (dosya `:11`) ↔ ADR-041 `BINARY(16)` UUIDv7 standardı — mevcut grandfathered, **yeni tabloda UUID için kapı** | Data Engineer | sonraki vault reset | ⏳ rapor-only |
| 9 | **Raporlar (düzeltilmedi):** **(9a)** 14 atlanan boşluk `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` (dosya YOK **ve** `.decisions/index.md` satırı YOK; `ADR-082` de satırsız — `:104` = 081, `:105` = 083) · **(9b)** `podcast-support.md:212` "tam implemente" ↔ kod 0 · **(9c)** `deleted_at` iddiası (`:8`) ↔ 0 kullanım · **(9d)** ADR-026 `:63` "abonelik değil içerik listesi" ↔ şema · **(9e)** envanter 18 ↔ 19 dosya · **(9f)** şablon yolu `.ai/templates/…` ↔ gerçek `.ai/.templates/…` | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 10 | `index.md:97` `[[../../raw/brain.md]]` → gerçek ADR hedefine düzeltmesi + `brain.md:1014`/`keys.md:290`/`index.md:698` satır metinlerinin bu ADR'ye bağlanması | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi** (rapor-only) |
| 11 | **Şart 1 (debate)** — debate 3 tur + KABUL/RED kararı; RED ise §5.2 + §4.4 (fallback) uygulanır | MO + persona | 1 gün | ✅ **TAMAMLANDI** (3 tur / 20 persona → 18/2/0 KABUL) |
| 12 | **Şart 2 (debate)** — TTL süresi + segment-alt-bölme eşiği rakamlarının ölçülmesi ve §2.2/d'deki "YENİ ADR KAPISI"na bağlanması | Data Engineer | aşama 2 | ⏳ debate şartı (→ **Debate Şart 3**, satır 16) |
| 13 | **Şart 3 (debate)** — şema IMPLEMENTED / **kod PLANNED** etiketinin + `podcast-support.md:212` ⚠️ işaretinin korunması (kod yazılana kadar "hazır" dendiği yerde bu ADR'ye atıf zorunlu) | MO + Backend | aşama 2 | ⏳ debate şartı (→ **Debate Şart 1a**, satır 14) |
| 14 | **Debate Şart 1 — doküman + FK düzeltmesi (1a-1b):** (1a) `podcast-support.md:212` "tam implemente" iddiasının kod kanıtıyla hizalanması (§5.1/9b) · (1b) `last_played_episode_id` `:492` constraint'siz FK iddiasının ya ADR-014 kapısından gerçek constraint (`ON DELETE SET NULL`) ya da yorum düzeltmesiyle hizalanması | MO + Data | aşama 2 | ⏳ **debate şartı** — KABUL (1a/1b) |
| 15 | **Debate Şart 2 — indeks fazı + FULLTEXT ölçümü:** I1 (`show_id, status, publish_date`) + I2 (`ft_pt_content`) uygulama fazına alınır; FULLTEXT ölçümü + `EXPLAIN` teyidi olmadan "hızlandırdı" denmez (§2.2/c) | Data Engineer | aşama 2 | ⏳ **debate şartı** — KABUL (2) |
| 16 | **Debate Şart 3 — TTL/arama kararı:** `LONGTEXT` transcript TTL süresi + arama kararı (segment-alt-bölme eşiği dahil) aşama 2'de ölçülür ve §2.2/d "YENİ ADR KAPISI"na bağlanır | Data + MO | aşama 2 | ⏳ **debate şartı** — KABUL (3) |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez**), `index.md:97` / `brain.md:1014` / `keys.md:290` / `index.md:698` satırları **silinmez**, `log.md`'ye `ADR-073 RED (debate …)` append edilir; bu durumda (i) 4 tablo şeması `coremusic_musics.sql` içinde **kalmaya devam eder** (SQL dosyası bu ADR'den bağımsızdır, dokunulmaz), (ii) I1/I2 indeksleri, feed alanları, silme politikası ve TTL/LOB fazları **kural olmaktan çıkar** (öneriye döner), (iii) çapraz FK yokluğu zaten ADR-003/040'ta durduğu için otorite kaybı olmaz, (iv) §1.1'deki 0/0/2/0 kod sayımları ve `podcast-support.md` çelişkisi **bağımsız bulgu olarak geçerlidir**. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks/brain/keys satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

---

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır | Ne | Etiket |
|-------|-------|----|--------|
| `.ai/.decisions/index.md` | `:97` | slug `ADR-073-podcast-database-schema` | ✅ hizalı (dosya adı ile birebir) · satır `[[../../raw/brain.md]]` önekli kusurlu → §5.1/10 |
| `.ai/index.md` | `:698` | "ADR-073 … Podcast DB Schema (shows, episodes, subscriptions, transcripts)" | ✅ kayıtlı |
| `.ai/raw/brain.md` | `:1014` | aynı özet satırı | ✅ kayıtlı (metin bu ADR ile hizalanacak → §5.1/10) |
| `.ai/raw/keys.md` | `:290` | "ADR-073 \| podcast database, shows, episodes, transcripts" | ✅ kayıtlı |
| `.ai/sources/.sql/mysql/coremusic_musics.sql` | `:10` · `:398-536` · `:755-756` | 22 tablo sayımı · **4 podcast tablosu + 3 FK + 13 indeks** · footer + BCNF beyanı | ✅ **birincil kanıt** (IMPLEMENTED) |
| `.ai/sources/.sql/mysql/coremusic_api.sql` | `:115` · `:128-141` | `api_calls` PK `(id, called_at)` + aylık RANGE partition + MAXVALUE — **FK 0** | ✅ vault'un tek partition örneği |
| `.ai/.decisions/accepted/ADR-040-database-authority.md` | `:34` · `:38` · `:49` · `:156` · `:194-197` | 18/156 envanter · BCNF iddia ≠ denetim · 28 FK (podcast satırı YOK) · `coremusic_musics` sahibi `music` · X serisi | ✅ dosya var — **bağlayıcı otorite** |
| `.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md` | `:146-150` · `:262-265` | 5 kural (normalizasyon/PK-FK/index/ENUM/audit) + denetim adımları PLANNED | ✅ dosya var — **kural seti** |
| `.ai/.decisions/accepted/ADR-041-database-normalization-supplementary.md` | §2.2 (a–f) | adlandırma · veri tipi · view · trigger · audit · N+1 | ✅ dosya var — **tamamlayıcı kurallar** |
| `.ai/.decisions/accepted/ADR-072-social-database-schema.md` | künye · §1.1 · §2.2 | format/dil referansı + aynı etiket disiplini + `entity_type` sınırı | ✅ dosya var — **format referansı** |
| `.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md` | §2.2 | "DB arası FK YOK" hükmü | ✅ dosya var (frozen — yalnız okunur) |
| `.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md` | künye | tek PHP runner kapısı | ✅ dosya var — **tüm DDL kapısı** |
| `.ai/.decisions/accepted/ADR-081-multi-provider-data-sync.md` | künye | outbox + WAL | ✅ dosya var — **sınır** |
| `.ai/.decisions/accepted/ADR-026-download-service-architecture.md` | `:63` | `podcast_subscriptions` ifadesi (şema ile çelişen) | ✅ dosya var (frozen) — **sınır + rapor** |
| `.ai/architecture/k5-veri-yonetimi/README.md` | `:196-199` | K5.1.3.13–16: `podcast_shows L406` · `episodes L444` · `subscriptions L487` · `transcripts L516` | ✅ IMPLEMENTED (doküman, satırlar birebir) |
| `.ai/architecture/k15-medya-streaming/podcast-support.md` | `:12` · `:72-92` · `:210-218` | RSS/episode metadata dokümanı · **`:212` "tam implemente" iddiası** | ⚠️ **çelişki (kod 0)** |
| `.ai/architecture/k5-veri-yonetimi/file-system-storage.md` | `:47-50` | `podcasts/{podcast_id}/episodes/{episode_id}.mp3` | ✅ IMPLEMENTED (doküman) / ⏳ dizin PLANNED |
| `.ai/architecture/k8-servis/README.md` | `:93` · `:233` | K8.2.5 podcast-video servisi satırı | ✅ IMPLEMENTED (doküman) / ⏳ servis PLANNED |
| `shared/src/Device/DeviceManager.php` | `:501-503` | `showPodcastWidget()` — kod yüzeyindeki tek podcast isabeti | ✅ sınırlı / ⏳ CRUD PLANNED |
| `.ai/.templates/adr/adr-template.md` | §3 · §4 · §6 | 7 bölüm iskeleti + 19 doğrulama (Guardrail #16) | ✅ şablon (görevdeki `.ai/templates/…` yolu **YOK** → §5.1/9f) |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-072-social-database-schema]] · otorite: [[ADR-040-database-authority]] · kural seti: [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]]
- İlgili ADR'ler: [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-003-multi-db-bcnf]] · [[ADR-014-multi-db-migration-strategy]] · [[ADR-026-download-service-architecture]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-050-multi-db-sync-strategy]] · [[ADR-072-social-database-schema]] · [[ADR-081-multi-provider-data-sync]] · [[ADR-092-media-dizin-ekseni-ve-ulid]]
- Şema/kod kanıtları: [[../../sources/.sql/mysql/coremusic_musics.sql]] · [[../../sources/.sql/mysql/coremusic_api.sql]] · [[../../architecture/k5-veri-yonetimi/README.md]] · [[../../architecture/k15-medya-streaming/podcast-support.md]] · [[../../architecture/k5-veri-yonetimi/file-system-storage.md]] · [[../../architecture/k8-servis/README.md]]
- Vault kökü: [[../index.md]] · [[../../index.md]] · [[../../raw/brain.md]] · [[../../raw/keys.md]] · [[../../log.md]] · [[../../CLAUDE.md]]
- Dizin kayıtları (düz metin — dizin hedefidir, .md olmadığı için wiki-link değil): `.ai/.sql/mysql/` (19 dosya) · `shared/src/Device/` · `shared/database/migrations/` · `.ai/.decisions/draft/` · `.ai/.decisions/rejected/`
- Diskte **olmayan** (düz metin + ⚠️, linklenmez): **`ADR-075`** · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` · `ADR-074` · `ADR-076`–`ADR-079` · `ADR-080` · `ADR-082`–`ADR-088`

### 6.3 Wiki-Link Sayımı

| Öğe | Değer |
|------|-------|
| Wiki-link toplamı (bu dosya) | **67** occurrence / **25** benzersiz hedef (kod içi alıntılar hariç; tümü bu dosyada sayım ile doğrulandı) |
| Diskte olan hedef | **25 / 25** ✅ — path-form (`../index.md`, `../../brain.md`, `../../.sql/mysql/coremusic_musics.sql` …) diskte mevcut; slug-form (`[[ADR-040-database-authority]]` …) ADR-072 house biçimi, `accepted/` dizininde `<slug>.md` ile basename çözülüyor |
| Kod içi alıntılar (link değil) | `[[../../raw/brain.md]]` ×8 — index.md:97 kusurlu satırının alıntısı, canlı link sayılmadı |
| Düz metin + ⚠️ (linklenmeyen) | **`ADR-075`** · `ADR-074` · `ADR-076`–`ADR-079` · `ADR-082`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` · `ADR-080` |

### 6.4 Debate Notu (✅ TAMAMLANDI)

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona · sonuç **KABUL** (18 kabul / 2 çekimser / 0 red) |
| Beklenen format | ADR-072 ile aynı: 3 tur / 20 persona · KABUL/RED sayımı §7.2'ye işlendi ✅ |
| Debate öncesi açık kapılar → şartlar | TTL süresi rakamı (§2.2/d) → **Debate Şart 3** (§5.1/16) · segment-alt-bölme eşiği (§5.1/5) → **Debate Şart 3** (§5.1/16) · `last_played_episode_id` FK kararı (§2.2/b) → **Debate Şart 1b** (§5.1/14) · cascade → `RESTRICT/SET NULL` geçişi (§5.1/4) debate kaydında **itiraz yok** → PLANNED olarak kaldı |
| Debate RED ise | §5.2 geri dönüş + §4.4 fallback birlikte uygulanır — sonuç **KABUL** olduğundan uygulanmadı |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-09-30 | ✅ |
| Tech Lead | — | 2026-09-30 | ✅ (debate 3/20 · 18/2/0 KABUL — 3 şart §5.1/14-16) |
| Arch Lead | — | — | ⏳ |

**Numara boşlukları raporu (§5.1/9 ile aynı — rapor-only, düzeltilmedi):** `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065` · `ADR-066` · `ADR-067` · `ADR-068` · `ADR-069` · `ADR-070` · `ADR-071` · `ADR-080` = **14 numara** (dosya YOK **+** `.decisions/index.md` satırı YOK). Ayrıca `ADR-082` (dosya YOK, satır YOK — dizin `:104` = ADR-081, `:105` = ADR-083) ve `ADR-074`–`ADR-079` / `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK — **`ADR-075` dahil**) ayrı ⚠️ kümesidir. Bu ADR numara serisine **dokunmaz**, yalnızca raporlar.

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | 3 / 3 |
| Tur 1 (20 persona) | Bulgu: ayrı podcast SQL **YOK** · 4 tablo `coremusic_musics.sql` içinde (`:406` shows · `:444` episodes · `:487` subscriptions · `:516` transcripts) · FK 3/3 iç hepsi CASCADE (`:479`/`:506`/`:536`), çapraz-DB FK 0, 13 indeks · `last_played_episode_id` FK iddiasında constraint yok → ⚠️ · BCNF "Yes" denetlenmemiş beyan (ADR-040 `:38`) → V.R. · `content LONGTEXT NOT NULL` `:520`, partition tek örnek `coremusic_api.sql:128` (FK'sız) → ertelendi (InnoDB FK yasağı + partition anahtarı unique key zorunluluğu) · kod yüzeyi 0 (PHP/JS CRUD 0; `podcast|episode|transcript` yalnız `DeviceManager.php:501,503`; RSS/enclosure/iTunes 0) → şema IMPLEMENTED / kod PLANNED · doküman çelişkisi `podcast-support.md:212` "tam implemente" ↔ 0 → ⚠️ · ~60 kaynak / 8 sorgu · 14 atlanan boşluk + `ADR-082` index satırı yok (§5.1/9 + §7.1) · `index.md:97` slug hizalı. **Sonuç: 16 kabul/neutral + 4 uyarı** (Critic: doküman+FK şart · QA: I1/I2 şart · DB: TTL) |
| Tur 2 (itiraz → çözüm) | **4 itiraz → 4 çözüm:** (1) doküman-kod çelişkisi (`podcast-support.md:212` "tam implemente" ↔ 0) → doküman düzeltmesi → **şart 1a**; (2) `last_played_episode_id` constraint'siz FK iddiası → constraint ekle veya yorumu düzelt → **şart 1b**; (3) I1/I2 indeks PLANNED → indeks uygulama fazı + FULLTEXT ölçümü → **şart 2**; (4) TTL/segment eşiği sayılmadı → LONGTEXT TTL + arama kararı (aşama 2) → **şart 3** |
| Tur 3 (oy) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | ✅ **KABUL** — **3 şart:** (1) doküman + FK düzeltmesi (1a-1b) → §5.1/14 · (2) indeks fazı + FULLTEXT ölçümü → §5.1/15 · (3) TTL/arama kararı aşama 2 → §5.1/16 |
| Debate sonrası gerekenler | ✅ (1) bu tablo dolduruldu + §7.1 Tech Lead satırı ✅, (2) §6.4 açık kapılar §5.1/14-16 şartlarına bağlandı, (3) `log.md`'ye debate sonucu append edildi |
