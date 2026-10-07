---
title: "CoreMusic — ADR-076: Video Database Schema (music_videos/video_playback_history/video_subtitles — coremusic_musics.sql içinde, ayrı video SQL dosyası YOK · playback_queue coremusic_user.sql · 5 FK (3 same-DB gerçek + 2 çapraz-DB grandfathered + 1 yorum — user_id tip uyuşmazlığı) · 11 indeks · 3 CHECK · altyazı URL-referanslı srt/vtt/ass — DB'de altyazı metni YOK · playback TTL kolonu YOK · şema IMPLEMENTED / kod PLANNED)"
type: "architecture-decision"
category: "database"
date: "2026-10-01"
updated: "2026-10-01"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic Video veri şeması kararı: (a) **şema** — `music_videos` (`coremusic_musics.sql:548`) · `video_playback_history` (`:592`) · `video_subtitles` (`:625`) · `playback_queue` (`coremusic_user.sql:168`) — dosya adı/kolon adı değişmez; BCNF + ADR-033/040/041 + ADR-072/073/074/075 **aynı ortak kural seti** uygulanır; (b) **SQL envanteri** — ayrı video dosyası YOK (podcast/radio gibi `coremusic_musics.sql` içinde, dosya adına bakmadan tarama dersi); 4 tablo · 6 FK satırı (3 same-DB gerçek: `fk_mv_music` `:583` · `fk_vph_video` `:614` · `fk_vs_video` `:647`; 2 çapraz-DB grandfathered: `playback_queue` `:187-189` = ADR-040 X-defteri `:194`; 1 yorum-FK `:616` — `user_id INT UNSIGNED` ↔ `users.id BINARY(16)` **tip uyuşmaz**, ADR-075 tip bulgusuyla aynı sınıf) · 11 indeks (2 uq + 9 idx) · 3 CHECK; (c) **ilişki + indeks + medya metadatası 4 başlık**: D1 altyazı büyük metin stratejisi (URL-referans, DB'de metin 0 — LONGTEXT video tablolarında 0; repo LONGTEXT=6 ADR-073 bulgusu korunur), D2 playback state TTL/kullanıcı-oturum (TTL kolonu **YOK** → süre sayılmadı ⚠️), D3 video asset referansı (`video_url`/`thumbnail_url` VARCHAR(2048) URL-rot riski + `media_catalog.asset` ikinci metadata yüzeyi), D4 medya metadata sürüklenmesi (çift yüzey → yeni ADR kapısı); (d) **dürüst etiket**: şema+vault indeks kayıtları IMPLEMENTED, kod PLANNED (video tablo CRUD PHP=0 · JS 'subtitle' = BEM sınıf adı), BCNF beyanı denetlenmemiş (ADR-040 `:38`), PK'lar grandfathered (INT UNSIGNED ↔ ADR-041 BINARY(16)), `video_subtitles.updated_at` 0/1; (e) **sınır** = ADR-026 (teslim/indirme+stream) · ADR-072/073/074/075 (aynı seri) · ADR-081 (sync) · ADR-031 (PWA/Flutter) — tekrar yok; (f) `ADR-077` (Studio DB) diskte YOK → düz metin + ⚠️; TTL süresi · altyazı inline eşiği · asset entegrasyonu rakamları bu ADR'de yazılmadı → ⚠️ debate."
kaynak: "Disk kanıtı taraması (2026-10-01: `.ai/.sql/mysql/` = **19 dosya**, ayrı `video*.sql` **YOK** · `coremusic_musics.sql` VİDEO BÖLÜMÜ `:539-648` → `music_videos` `:548` (19 kolon, uq_mv_music `:570`, idx `:572-574`, CHECK format `:576` mp4/webm/mkv/avi + resolution `:577` 480p–4320p, FK `:583-584` → `musics(id)` CASCADE) · `video_playback_history` `:592` (last_position `:596`, watch_count `:597`, completion_pct `:598`, idx `:606-608`, FK `:614-615` → `music_videos`, yorum-FK `:616-617` user_id → `coremusic_auth.users` **deklare YOK**) · `video_subtitles` `:625` (language `:628`, subtitle_url `:630` VARCHAR(2048), format `:631` default 'srt', is_auto_generated `:632`, uq_vs_pair `:637`, idx_vs_language `:639`, CHECK `:641` srt/vtt/ass, FK `:647-648`) · `coremusic_user.sql` `playback_queue` `:168` (BINARY(16) UUIDv7 `:169`, source ENUM `:173`, idx `:183-184`, 2 çapraz-DB FK `:187-189`) · LONGTEXT taraması = **6 isabet** (cms 2 · musics 2 (`:176` lyrics, `:520` podcast_transcripts) · patch 1 · system 1) — **video tablolarında 0** · Kod yüzeyi: video tablo adı PHP CRUD = **0** · JS 'subtitle' = 33 isabet **yalnız BEM/CSS sınıfı** (PlayerInfo/Hero/MiniCard/Widget + test) · `oauth-platforms.php:68` video scope'ları yabancı platform · `k5-veri-yonetimi/README.md:181,200-202` 4 tablo indeks kaydı · `media_catalog.sql:148` `asset` (tip ENUM ses/video, `:176-180` cozunurluk/fps/video_kodec, `:188` poster, sha256 `:168`) ADR-092 · indeks `index.md:100` slug `ADR-076-video-database-schema` **hizalı** (satır `[[../brain.md]]` önekli kusurlu → §5.1/10) · `brain.md:1026` · `keys.md:293` · `.ai/index.md:701` · şablon `.ai/.templates/adr/adr-template.md` (görevdeki `.ai/templates/…` yolu YOK → §5.1/5g) · format referansı ADR-075 · web 6 sorgu / 60 isabet)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-076: Video Database Schema

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-01 — **Debate:** ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-076-video-database-schema` (dizin otoritesi: [[../index.md]] satır **100** — dosya adı ile birebir hizalı ✅; satır `[[../brain.md]]` önekli **kusurlu biçimdedir → `[[../brain.md]]` hedef düzeltmesi son sıfırlamaya ertelendi, bu işlemde rapor-only**)
> **İlgili kararlar:** [[ADR-075-ai-database-schema]] (format referansı + aynı seri şema ADR'si + tip-uyuşmazlığı/dürüst etiket disiplini) · [[ADR-074-radio-database-schema]] (aynı seri + partition gerekçesi) · [[ADR-073-podcast-database-schema]] (aynı seri — podcast de bu dosyadaydı, "dosya adına bakma" dersi oradan) · [[ADR-072-social-database-schema]] (aynı seri + ENUM/polymorphic sınır) · [[ADR-033-sql-normalization-strategy]] (BCNF kural seti + denetim PLANNED) · [[ADR-040-database-authority]] (18-DB sahiplik matrisi + 28 FK istisna defteri + `coremusic_musics` 22 tablo/video `:156`) · [[ADR-041-database-normalization-supplementary]] (adlandırma/UUID/audit + denormalizasyon defteri) · [[ADR-003-multi-db-bcnf]] (çapraz-DB FK hükmü) · [[ADR-014-multi-db-migration-strategy]] (tek migration kapısı) · [[ADR-002-pdo-mandatory-no-orm]] (erişim katmanı) · [[ADR-039-7-service-platform-architecture]] (A4 veri domaini sahibi) · [[ADR-026-download-service-architecture]] (teslim yolu — imzalı URL/uygulama stream + CDN opsiyonel; video varlık teslimi bu ADR'nin değil) · [[ADR-031-mobile-strategy-pwa-flutter]] (oynatıcı istemci yüzeyi) · [[ADR-081-multi-provider-data-sync]] (outbox + WAL — sınır) · [[ADR-092-media-dizin-ekseni-ve-ulid]] (`media_catalog.asset` medya-metadata yüzeyi — SSOT JSON) · karar dizini [[../index.md]] **satır 100**.
> **⚠️ VERIFICATION REQUIRED:** `ADR-077` (Studio DB — dizin `:101` var, **dosya diskte YOK → düz metin + ⚠️, wiki-link KURULMAZ**) · `ADR-078`/`ADR-079` · `ADR-080` (14 atlanan boşluk üyesi) · `ADR-082` (**ne dosya ne dizin satırı yok** — `:104` = 081, `:105` = 083) · `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK) · `ADR-051/053/054/055/057/060` + `ADR-065`–`ADR-071` + `ADR-080` = **14 atlanan boşluk** (hem dosya hem dizin satırı YOK — §5.1/8 ve §7.1) · BCNF "Normal Form: BCNF" beyanları (`coremusic_musics.sql:545,589,622`) **denetlenmemiştir** (ADR-040 `:38`) · `video_playback_history.user_id` yorum-FK **tip olarak uyuşmaz** (INT UNSIGNED ↔ BINARY(16)) · video tablo CRUD kullanan PHP/JS = **0** · TTL süresi · altyazı inline saklama eşiği · asset entegrasyon rakamı **bu ADR'de yazılmadı**.
> **Bölüm sınırı (kenetli):** ADR-074 **partition gerekçesi + format**, ADR-073 **podcast şemasını + LONGTEXT=6 bulgusunu**, ADR-072 **sosyal ENUM/polymorphic'i**, ADR-075 **AI şemasını + tip-uyuşmazlığı sınıfını + ML politikasını**, ADR-040 **DB sahipliğini + FK istisna defterini**, ADR-033/041 **normalizasyon kural setini**, ADR-014 **migration kapısını**, ADR-026 **teslim/indirme+stream yolunu**, ADR-031 **istemci stratejisini**, ADR-081 **senkronu**, ADR-092 **media_catalog türetilmiş indeksini** yazdı; **`music_videos`/`video_playback_history`/`video_subtitles`/`playback_queue` envanteri, ilişki/indeks/medya-metadata politikası (D1-D4) ve kod yüzeyi dürüst etiketi bu ADR'nindir** — hiçbiri yeniden yazılmaz. `ADR-077` (Studio DB) **diskte YOK → düz metin + ⚠️**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 7'deki "yeni ADR ≥ 088" ile arşivin 076 slotu arasındaki **numara çakışması ADR-061/062/063/064/072/073/074/075 künyelerinden tekrar raporlanır, düzeltilmez**.

---

## 1. Bağlam (Context)

CoreMusic'in video veri yüzeyi diskte **çoktan yazılmış**, vault'ta **hiç kararlaştırılmamış** durumda: `coremusic_musics.sql` içindeki VİDEO BÖLÜMÜ (`:539-648`) üç tabloyu — `music_videos`, `video_playback_history`, `video_subtitles` — barındırıyor; `coremusic_user.sql:168` kuyruk tablosu `playback_queue` video/oynatma oturumunun başka ayağını tutuyor. Buna karşılık hiçbir ADR bu şemayı karar olarak yazmıyor: ADR-040 onu sahiplik matrisinde "22 tablo / video dahil / sahibi `music`" diye geçiyor (`:156`), ADR-033 denetlenmemiş BCNF iddialarının arasında sayıyor, ADR-072/073/074/075 ise sosyal/podcast/radyo/AI şemalarını yazarken videoyu **atlıyor**. Aynı anda kod yüzeyinde video tablo sorgulaması **0** (PHP tablo adı taraması = 0, JS `subtitle` = yalnız BEM sınıf adı) — yani şema IMPLEMENTED, kod PLANNED; bu ayrımı da kimse yazmadı. Dört boşluk birden büyüyor: (1) **medya-metadata politikası yazılmamış** — altyazı metni nerede tutulur (inline TEXT mi, URL mi), `video_url`/`thumbnail_url` gibi VARCHAR(2048) referanslar nasıl korunur, `media_catalog.asset` (ADR-092, SSOT=JSON) ile `music_videos` arasındaki ikinci metadata yüzeyi nasıl ayrışır — hiçbiri yok; (2) **playback state TTL'siz** — `video_playback_history` ve `playback_queue` içinde süre/silme kolonu yok, bayat pozisyon ve kuyruk şişmesi kararı sahipsiz; (3) **FK/inceleme ayrıntısı yazılmamış** — 3 same-DB gerçek FK + 2 grandfathered çapraz-DB FK + 1 imkânsız yorum-FK (user_id tip uyuşmazlığı — ADR-075'in bulduğu sınıfın video ayağı) tek yerde değil; (4) **sınır yazılı değil** — ADR-026 (teslim), ADR-031 (istemci), ADR-072-075 (aynı seri), ADR-081 (sync), ADR-092 (arşiv indeksi) bu şemaya dokunabilir; sahiplik söylenmezse altısı da video şemasını yeniden yazma riski taşır. Bu ADR dört işi tek kayıtta kapatır: **(1)** 4 tabloyu envanterler, **(2)** ilişki/indeks envanterini yazar, **(3)** D1-D4 medya-metadata politikasını (altyazı metni · TTL · asset referansı · çift yüzey) sabitler, **(4)** dürüst etiket + sınır koyar.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-01 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | ADR-076 slotu kayıtlı mı? | [[../index.md]] `:100` → `\| [[../brain.md]] ADR-076-video-database-schema \| Video DB Schema \| Database \|` · [[../../index.md]] `:701` · [[../../brain.md]] `:1026` · [[../../keys.md]] `:293` | ✅ **KAYITLI** (4 indeks satırı; `:100` önek kusurlu → §5.1/10) |
| 2 | Bu işlem öncesi dosya var mıydı? | `accepted/` dizin listesi — `ADR-076*.md` **yok** (ADR-075 var) | ❌ **YOKTU** → bu işlemde yazılıyor |
| 3 | **Ayrı video SQL dosyası var mı?** | `.ai/.sql/mysql/` = **19 dosya** (2026-10-01 glob); `video` içeren dosya adı **0** | ❌ **AYRI DOSYA YOK** → video tabloları `coremusic_musics.sql` içinde (podcast/radio ile aynı ders) |
| 4 | Tablo envanteri (video bölümü) | `music_videos` `:548` · `video_playback_history` `:592` · `video_subtitles` `:625` — bölüm başlığı `:540` "VİDEO TABLOLARI" | ✅ **IMPLEMENTED** (şema) |
| 5 | 4. tablo (oynatma kuyruğu) | `playback_queue` `coremusic_user.sql:168` (BINARY(16) UUIDv7 `:169`, `source` ENUM `:173`) — `k5-veri-yonetimi/README.md:181` indeks kaydı | ✅ şema (aynı DB değil — `coremusic_user`) |
| 6 | FK envanteri | 3 same-DB gerçek: `fk_mv_music` `:583-584` (music_id BINARY(16) ↔ `musics.id BINARY(16)` ✅ tip uyumlu) · `fk_vph_video` `:614-615` (INT ↔ INT ✅) · `fk_vs_video` `:647-648` (INT ↔ INT ✅) · 2 çapraz-DB grandfathered: `playback_queue` `:187-189` (`fk_queue_user`/`fk_queue_music`, BINARY(16) ↔ BINARY(16) ✅) — ADR-040 X-defteri `:194` (`:188-189` satırı KABUL/grandfathered) | ✅ 5 gerçek FK · defterle uyumlu |
| 7 | **Tip uyuşmazlığı (video ayağı)** | `video_playback_history.user_id INT UNSIGNED` `:594` ↔ `users.id BINARY(16)` (`coremusic_auth.sql`) — yorum-FK `:616-617` "cross-database FK not supported" der, **tip uyuşmazlığını söylemez** | ⚠️ deklarasyon **YOK** + tip uyuşmaz — ADR-075 §1.1/6 ile **aynı sınıf** → gerçek FK denemesi migration patlatır (R4) |
| 8 | İndeks + CHECK envanteri | **11 girdi** = uq 2 (`uq_mv_music` `:570`, `uq_vs_pair` `:637`) + idx 9 (`:572-574`, `:606-608`, `:639`, `:183-184`) · CHECK **3** (`:576` format · `:577` resolution · `:641` altyazı format) | ✅ IMPLEMENTED · PK-only tablo **0** |
| 9 | BCNF beyanı | `:545` "Normal Form: BCNF — music_id UNIQUE" · `:589` · `:622` · ADR-040 `:38` "iddiadır, denetim DEĞİL → PLANNED" | ⚠️ **denetlenmemiş beyan** (3 tablo) |
| 10 | **Altyazı formatı ve yüzeyi** | `format` CHECK `:641` = `('srt','vtt','ass')`, default `'srt'` `:631` · metin kolonu **YOK** — yalnız `subtitle_url VARCHAR(2048)` `:630` + `language`/`label`/`is_auto_generated` · LONGTEXT taraması video tablolarında **0** (repo geneli 6 — ADR-073 bulgusu korunur) | ✅ **URL-referans modeli** (DB şişmesi yok) / ⚠️ URL-rot riski (R2) · WebVTT (`vtt`) CHECK'te var |
| 11 | Audit alanları | `music_videos`: created+updated+is_deleted `:564-567` ✅ · `video_playback_history`: `:599-602` ✅ · `video_subtitles`: created+is_deleted `:633-634` — **`updated_at` YOK (0/1)** · `playback_queue`: created+updated `:176-177`, **is_deleted YOK** · **deleted_at 4/4 YOK** | ⚠️ ADR-033 kural 5 açıkları (defter §5.1/5) |
| 12 | Kimlik tipi | 3 video tablosu `INT UNSIGNED AUTO_INCREMENT` ↔ ADR-041 standardı `BINARY(16)` UUIDv7 · `playback_queue.id BINARY(16)` `:169` ✅ uyumlu | ⚠️ 3 tablo **grandfathered** (ADR-075 ile aynı muamele) |
| 13 | **Kod yüzeyi (tablo CRUD)** | `music_videos\|video_subtitles\|video_playback_history\|playback_queue` PHP = **0 isabet** · JS/TS = **0** (geniş `subtitle` = 33 isabet, **hepsi BEM/CSS** — PlayerInfo/Hero/MiniCard/Widget bileşenleri + test) · `shared` içinde `video` = 1 (`oauth-platforms.php:68` — yabancı platform scope'u) | ⏳ şema IMPLEMENTED / **kod PLANNED** |
| 14 | Playback TTL kolonu | 4 tabloda TTL/expiry/silme-süresi kolonu **0** · karşılaştırma kalıbı `user_downloads.expires_at` `coremusic_user.sql:206` mevcut | ⚠️ **TTL YOK** → süre sayılmadı ⚠️ (D2, R1) |
| 15 | **Çift medya-metadata yüzeyi (yeni bulgu)** | `music_videos` teknik alanlar (`resolution/width_px/height_px/video_size_bytes` `:557-560`) ↔ `media_catalog.asset` (`tip ENUM('ses','video')` `:150`, `cozunurluk/fps/video_kodec/ses_kodec/bitrate_kbps` `:176-180`, `poster/poster_zaman` `:188`, `sha256` `:168` UNIQUE) — ikisi de video teknik verisi tutar | ⚠️ **örtüşme/sürüklenme** — rol dağıtımı yok → yeni ADR kapısı (D4, R3) |
| 16 | Doküman yüzeyi | `k5-veri-yonetimi/README.md:200-202` (K5.1.3.17-19) + `:181` (playback_queue) · `k10-uygulama/media-panel.md:12` (subtitle yönetimi + HLS/DASH — istemci dokümanı) | ✅ indeks kaydı var / ⏳ endpoint dokümanı **YOK** (AI'nın `ai-service.md`'sinin video karşılığı bulunamadı) |
| 17 | Referans formatı | [[../../.templates/adr/adr-template.md]] (Guardrail #16) · format referansı [[ADR-075-ai-database-schema]] | ✅ okundu |
| 18 | Komşu/aynı-sınıf | `user_listening_history` `coremusic_user.sql:90` (ses karşılığı — D2 ile ilişkili) · `podcast_transcripts.content LONGTEXT` `:520` (metin-inline örneği — altyazının **karşıt** modeli) · `media_catalog.sql:5` "ADR-092 §5.2 adım 6" (dosya ADR-092'ye atıfla yazıldı) | ✅ sınır/karşılaştırma |

> **Yol notu:** görev metnindeki `.ai/templates/adr/adr-template.md` yolu **bulunamadı**; gerçek şablon `**.ai/.templates/**adr/adr-template.md` (nokta-önekli `.templates`). Düzeltme rapor-only (ADR-073/074/075 §5.1 ile aynı).

### 1.2 Sorun Tanımı

1. **Şema var, karar yok.** Dört tablo diskte, K5 indeksinde kayıtlı, ama hangi grubun ne iş olduğu, neden ayrı bir video SQL dosyası bulunmadığı, hangi bağımlılığın neden yalnız yorum olarak durduğu ve hangi medya-metadata kuralının bağlayıcı olduğu hiçbir ADR'de yazılmıyor → yeni geliştirici "video tabloları nerede, altyazı metni nerede tutulur, kaldığım yerden oynatma ne zaman bayatlar?" sorularına vault'tan cevap bulamıyor.
2. **IMPLEMENTED/PLANNED ayrımı yok.** Şema + K5 indeks kaydı var, kod CRUD 0 → "video hazır" yanılgısı doğabilir; `media-panel.md:12` subtitle yönetimi + HLS/DASH anlatırken tabloya dokunan kod yok (ADR-073/075'teki doküman-kod dersi).
3. **Medya-metadata politikası yazılmamış.** Dört başlık da boş: (i) altyazı metni inline mi (podcast_transcripts modeli) URL mi (bugünkü model)? inline büyüyünce eşiği ne? (ii) `video_playback_history`/`playback_queue` TTL süresi ne, bayat pozisyon ne temizlenir? (iii) `video_url`/`thumbnail_url` gibi 2048'lik URL referansları nasıl korunur (imza/rot)? (iv) `music_videos` ↔ `media_catalog.asset` çift yüzeyi nasıl ayrışır? Hiçbiri yazılmadı.
4. **İlişki ayrıntısı yazılmamış.** 3 same-DB gerçek FK + 2 grandfathered + 1 imkânsız yorum-FK tek yerde değil; `user_id` tip uyuşmazlığı ADR-075'te AI ayağı için yazıldı, video ayağı için yazılmadı → ilk geliştirici "user_id'ye FK ekleyelim" diye kırık DDL yazabilir.
5. **Audit açıkları yazılmamış.** `video_subtitles.updated_at` 0/1, `playback_queue.is_deleted` yok, `deleted_at` 4/4 yok → ADR-033 kural 5 açığı defterde değil.
6. **Sınır yazılı değil.** ADR-026 (teslim), ADR-031 (istemci), ADR-072-075 (aynı seri), ADR-081 (sync), ADR-092 (arşiv indeksi) bu şemaya dokunabilir; sahiplik söylenmezse hepsi video şemasını yeniden yazma riski taşır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "video metadata database schema design best practice 2025 resolution duration streaming" · (2) "subtitle caption storage database WebVTT SRT format store file vs database column best practice" · (3) "resume playback position watch history database design user session TTL modeling" · (4) "BCNF normalization at scale denormalization read-heavy media catalog when to denormalize" · (5) "storing subtitle text in database column vs file object storage large text performance" · (6) "video asset metadata ffprobe width height duration checksum database inventory design" |
| Web Search **Konusu** | **(1)** video/streaming servisi ilişkisel şema tasarımı ve video metadata'sı · **(2)** altyazı formatları (SRT/WebVTT/ASS) ve DB'de dosya vs kolon saklama · **(3)** kaldığım yerden oynatma / izleme geçmişi modelleme + TTL · **(4)** ölçekte BCNF vs denormalizasyon (read-heavy medya katalogu) · **(5)** büyük metnin DB kolonu vs dosya/object storage'da saklanması · **(6)** video varlık envanteri: ffprobe ile teknik metadata (width/height/duration/checksum) çıkarımı. |
| Web Search **Bağlam** | CoreMusic: video 4 tablosu şema IMPLEMENTED / kod PLANNED (CRUD 0); vault'ta altyazı metin stratejisi, playback TTL'si, video URL asset referansı ve `music_videos`↔`media_catalog.asset` çift-metadata ayrımı **hiçbiri yazılmamış**; `user_id` tip uyuşmazlığı ve audit açıkları defterde değil. Araştırma bu dört politika boşluğunu + normalizasyon sınırını hedefliyor — tablo sayısı/satır numaraları **iç karar** olduğu için web yalnız *yapı, format ve yaşam döngüsü* için okundu (ADR-033 sorgularının tekrarı değil). Araştırma 2026-10-01'de yapıldı; **6 sorgu / 60 isabet listelendi** (tekrarlar: geeksforgeeks ×2, stackoverflow farklı URL'ler). Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmi spec → güvenlik → vendor → uzman blog). |
| Web Search **Kısa Açıklama** | **(1)** Video streaming şeması ortak kalıbı: `videos` (metadata) + `users`/`profiles` + izleme geçmişi + teknik/akış varlıkları ayrı tablolarda; teknik metadata (süre, çözünürlük, codec) video satırının özelliği olarak tutulur → CoreMusic `music_videos` bu kalıba uyar. **(2)** Endüstri standardı ayrım: **SRT = evrensellik, WebVTT = web/HTML5** (styling + metainfo), ASS = gelişmiş stil; DB'de altyazı **dosya/URL referansı** olarak saklanır, kolona gömülmez → bugünkü `subtitle_url` modeli desteklenir. **(3)** Resume/continue-watching: son pozisyon kullanıcı×video başına tek kayıtta, "son bilinen pozisyon" okunur; TTL/hiç-silme tartışması var — bayatlık (eski pozisyon) problemi gerçek, süre rakamı proje kararı → CoreMusic'te TTL kolonu yok, kapı yazılır. **(4)** Normalizasyon önce, denormalizasyon **ölçülmüş read yüküyle**; sayaç/tekrar okunan alanlar aday → `view_count`/`like_count` bu sınıfta, ölçüm kapısı. **(5)** Büyük metin DB'de yaşamaz (buffer pool şişmesi, backup maliyeti) → dosya/object storage; DB'de erişim referansı tutulur → altyazı için URL modeli + inline eşiği kapısı. **(6)** Teknik envanter ffprobe ile üretilir (width/height/duration/bitrate/codec/checksum), dosya yolu + sha256 tekilleştirir → `media_catalog.asset` zaten bunu yapar (ADR-092). |
| Web Search **Uzun Açıklama** | **(i)** Video şema tasarımı kaynakları (GeeksforGeeks video-streaming DB, StackOverflow "standard schema for video metadata" [kapalı — tek standart yok], Hygraph metadata management, AWS time-series modeling'de `video_resolution`/`playback_quality` kolon örnekleri) ortak noktayı verir: video metadata'sı (başlık/açıklama/süre/çözünürlük/format) **eser seviyesinde** yaşar, oynatma durumu **kullanıcı×eser seviyesinde**, teknik varlık (dosya/kodek) **dosya seviyesinde** — üçü aynı tabloda birleşmez. CoreMusic'te bu üç ayrım zaten var: `music_videos` (eser) · `video_playback_history` (kullanıcı×eser) · `media_catalog.asset` (dosya) → şema **korunur**; eksik olan ayrımın *kararı* değil, *sahipliğidir* (D4). **(ii)** Altyazı format kaynakları (Amara, TranscribeGo, GoTranscript, Subly, TranslateBot, Mediascribe, Ditto, Happyscribe, Vocova — 9 kaynak) hep aynı matrisi verir: SRT geniş uyumlu düz metin; WebVTT web standardı (`WEBVTT` başlık zorunlu, styling/chapter/metainfo destekli); ASS stil odaklı; TTML/MPL2 niş. CoreMusic CHECK'i `('srt','vtt','ass')` = web + evrensellik + stil üçlüsünü kapsar — **eksik değil**; karar: format seti korunur, varsa genişleme (`ttml`) yeni CHECK değişikliği = ADR-014 kapısı. **(iii)** Saklama kaynakları (StackOverflow "long text DB vs filesystem", SoftwareEngineering SE, Joey Dantoni "Storing Files in Your Databases — Why You Shouldn't", Reddit/Supabase, arXiv large-object paper, EngineeringAtScale blob-vs-DB, EDB, RustproofLabs large-text) büyük ölçüde aynı uyarıyı verir: dosyayı kolona gömmek buffer pool/backup şişirir; dosya/object storage + DB'de referans doğru kalıptır (istisna: küçük, sorgu edilen, kısa metin — blog CMS içeriği gibi; o da CoreMusic'te `content LONGTEXT` olarak zaten exception defterinde). Altyazı yüzlerce KB'ye büyüyebilen, sorgulanmayan (yalnız servis edilen) bir metindir → **inline kalıp değildir**. **(iv)** TTL/izleme geçmişi kaynakları (Flicknexs continue-watching, Unseen Architectures "Netflix pause/resume", ByteByteGo "Netflix 140M hours/day — Cassandra'da kullanıcı başına kayıt", StackOverflow viewing-history modeli, HelloInterview cross-device resume, CapitalOne TTL) iki modeli verir: (a) kullanıcı×öğe başına **tek satır** son pozisyon (CoreMusic'in bugünkü modeli — `uq` yok ama `idx_vph_user` + upsert bekleyen yapı), (b) olay-append'li geçmiş (Cassandra tarzı). TTL her yerde **politika** olarak tartışılır, zorunlu değil; "eski pozisyon bayatlar mı" sorusu ürün kararı → CoreMusic'te süre sayılmadı, debate kapısı. **(v)** Normalizasyon kaynakları (Redgate "myth of over-normalization", DBA.stackexchange, Solarwinds, MatterAI, GeeksforGeeks, dev.to) uzlaşır: önce BCNF, denormalizasyon **yalnız ölçülü**; sayaçlar (`view_count`/`like_count`) en yaygın meşru denormalizasyondur → bu iki sayaç defterde işaretlenir (ADR-041), silinmez. **(vi)** ffprobe kaynakları (FFmpeg ffprobe resmi dokümanı, OTTVerse, api.video, ffmpeg-micro, IO River, Video.stackexchange) teknik metadata çıkarımının endüstri aracını tekilleştirir: width/height/duration/bitrate/codec → JSON; checksum dosya envanterinin tekilleştiricisidir. CoreMusic karşılığı `media_catalog.asset` (`sha256` UNIQUE `:168`, `cozunurluk/fps/kodek` `:176-180`) — **implementasyon ADR-092'de**, bu ADR tekrar etmez; yalnız `music_videos`'taki kopya teknik alanlarla ilişkisini yazar (D4). |
| Web Search **Paragraf Veri Uzun** | **6 sorgu / 60 isabet**: **(1)** GeeksforGeeks video streaming DB design · StackOverflow standard video metadata schema · Hygraph metadata management · AWS time-series data modeling (video_resolution/playback_quality) · Swarmify video metadata · Resi encoding 2025 · Medium streaming essentials · Instaclustr data streaming · UNC metadata best practices · Neurohive ffprobe hidden metadata (10). **(2)** Amara subtitle formats · TranscribeGo SRT vs VTT vs ASS · GoTranscript caption formats legal · Subly SRT vs VTT · TranslateBot subtitle file formats · Mediascribe caption formats · Ditto SRT vs VTT · Happyscribe VTT vs SRT · Recap innovations caption formats · Vocova 6 formats guide (10). **(3)** Medium real-time recommendations system design · GeeksforGeeks Netflix architecture · Flicknexs continue watching · Unseen Architectures Netflix pause/resume · StackOverflow viewing history modeling · HelloInterview video resume sync · ByteByteGo Netflix viewing data · CapitalOne database TTL · Fullstory session replay · Amplitude session replay (10). **(4)** StackOverflow when to denormalize · MatterAI normalization vs denormalization · Medium 1NF to BCNF · Redgate myth of over-normalization · Solarwinds normalize vs denormalize · DBA.stackexchange when to denormalize · TechMixing guide · GeeksforGeeks difference · Binus 1NF–BCNF · dev.to trade-offs (10). **(5)** StackOverflow long text DB vs filesystem · SoftwareEngineering big text database vs file · Joey Dantoni storing files in databases · Reddit/Supabase DB vs storage text · arXiv large object storage cs/0701168 · EngineeringAtScale blob vs database · Quora large text files · EDB data in DB vs file system · CS50 group post · RustproofLabs large text performance (10). **(6)** FFmpeg ffprobe official docs (ffprobe-all) · ffmpeg-micro inspect metadata · ffmpeg-api ffprobe · OTTVerse ffprobe tutorial · api.video ffprobe guide · Video.stackexchange ffprobe mp4/h264 · arj.no ffprobe duration+pixels · IO River what is ffprobe · StackOverflow width/height via ffprobe · Video.stackexchange batch duration (10). |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** üç-seviye ayrım (eser / kullanıcı×eser / dosya) endüstri kalıbı → `music_videos`+`video_playback_history`+`asset` şeması **korunur, yeniden modellenmez**; tek standart şema yok (SO kapalı) → uydurma "standart" iddiası yazılmaz. **(2) Karar destekleniyor:** SRT/VTT/ASS seti kapsayıcı → CHECK korunur; altyazı **dosya/URL** olarak saklanır. **(3) Karar destekleniyor + boşluk doğrulandı:** son-pozisyon modeli doğru; TTL politika → CoreMusic'te kolon yok, süre sayılmadı ⚠️ debate. **(4) Karar destekleniyor:** BCNF önce, sayaç denormalizasyonu meşru → `view_count`/`like_count` defterde. **(5) Karar destekleniyor:** inline büyük metin kalıp değil → D1 URL modeli + inline eşiği kapısı. **(6) Karar destekleniyor:** teknik envanter ffprobe+sha256 → `media_catalog.asset` (ADR-092) sahipliğine bağlanır, tekrar yazılmaz. **İtiraz/karşıt bulgu:** dış kaynaklar CoreMusic'in TTL süresini, inline eşiğini, URL imza yöntemini ve iki metadata yüzeyinin sahipliğini doğrulayamaz (iç karar) → o rakamlar ⚠️. |
| Web Search **Alınan Karar** | **(a)** 4 tablo şeması korunur, yeniden modellenmez (envanter §2.2/a). **(b)** İlişki: 3 same-DB gerçek FK korunur · 2 çapraz-DB grandfathered (ADR-040 X-defteri) korunur · `user_id` yorum-FK **dokunulmaz** + tip uyuşmazlığı yazılır → gerçek FK denemesi yasak (R4). **(c)** İndeks: mevcut 11 korunur; yeni indeks EXPLAIN'siz eklenmez (ADR-033 ile aynı kapı). **(d)** D1 altyazı metni: **URL-referans modeli korunur** (inline TEXT/LONGTEXT yasak eşiği debate); format seti `srt/vtt/ass` korunur, genişleme = yeni ADR + ADR-014. **(e)** D2 playback TTL: `video_playback_history` + `playback_queue`'da TTL kolonu **yok** → süre sayılmadı ⚠️ debate; temizlik kalıbı adayı `user_downloads.expires_at` (`:206`); upsert/kalıcılık kararı kod fazında (ölçüm). **(f)** D3 video asset referansı: `video_url`/`thumbnail_url` VARCHAR(2048) korunur; erişim/rot/imza politikası **teslim işi → ADR-026'ya bağlanır** (bu ADR imza seçmez); varlık tekilleştirilmesi (`sha256`, `dosya_yolu`) `media_catalog.asset`'te (ADR-092) — `music_videos` URL'leri ona **referans olarak** bağlanır, veri kopyalanmaz. **(g)** D4 çift-metadata yüzeyi: `music_videos` teknik alanları ile `media_catalog.asset` video alanları **birleştirilmez** — rol dağıtımı (katalog sayımı vs arşiv envanteri) **yeni ADR kapısı**; sürüklenme riski R3. **(h)** Sınır = ADR-026 · ADR-031 · ADR-072 · ADR-073 · ADR-074 · ADR-075 · ADR-081 · ADR-092 — tekrar yok. |
| Web Search **Sonuç** | **6/6 araştırmada karar destekleniyor** (3 yapı + 3 politika): üç-seviye video şema ayrımı (1) · altyazı format matrisi + dosya-saklama kalıbı (2+5) · resume/TTL modeli (3) · normalizasyon-önce kuralı (4) · ffprobe+sha256 envanteri (6). **Dört gerilim açıkça kabul edildi:** (1) TTL süresi · altyazı inline eşiği · URL imza/rot politikası · çift-yüzey sahipliği **bu ADR'de sayılmadı** → ⚠️ debate şartları (§5.1/11-14); (2) `view_count`/`like_count` denormalizasyonu **silinmedi** → defter (§5.1/5b); (3) `user_id` tip uyuşmazlığı + audit açıkları **düzeltilmedi** → rapor-only; (4) `ADR-077`–`ADR-079`/`ADR-080`/`ADR-082`–`ADR-088` + 14 boşluk dosyaları. **⚠️ VERIFICATION REQUIRED:** TTL süresi · altyazı inline eşiği · `video_url` imza yöntemi (iç karar) · `media_catalog` DDL doğrulanamadı (ADR-092 dosyası `:25` "MySQL bu makinede yok"). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar (ADR-002 / ADR-003 / ADR-014 dahil) |
| ADR-040 sınırı | 18-DB sahiplik matrisi + 28 FK istisna defteri bağlayıcıdır; `coremusic_musics` sahibi `music` (`:156`), `playback_queue` FK'ları X-defterinde KABUL/grandfathered (`:194`) → defter değişmez; envanter drift'i rapor-only |
| ADR-033 / ADR-041 sınırı | Normalizasyon kural seti bağlayıcıdır; bu ADR kural **üretmez**, şemaya uygular ve açığı defterine yazar (audit `updated_at` 0/1 · is_deleted `playback_queue` yok · deleted_at 4/4 · UUID grandfathered · sayaç denormalizasyonu) |
| ADR-003 sınırı | Çapraz-DB FK hükmü; `video_playback_history.user_id` deklare edilmemiş + tip uyuşmaz → gerçek FK **eklenemez** |
| ADR-072 / ADR-073 / ADR-074 / ADR-075 sınırı | Sosyal ENUM/polymorphic, podcast şeması + LONGTEXT=6, radio şeması + partition gerekçesi, AI şeması + tip-uyuşmazlığı + ML politikası bu ADR'de **yeniden yazılmaz**; format/dil ADR-075'ten alınır, içerik tekrarı yok |
| ADR-026 sınırı | Teslim yolu (imzalı tek kullanımlık URL · uygulama stream · CDN opsiyonel · abuse önleme) bu ADR'de yazılmaz; `video_url` erişim politikası oraya bağlanır |
| ADR-031 / ADR-081 sınırı | PWA/Flutter oynatıcı stratejisi ve outbox+WAL senkronu kapsam dışı; bu ADR istemci ve senkron önermez |
| ADR-092 sınırı | `media_catalog` türetilmiş indeksi + ULID + SSOT=JSON bu ADR'de tekrarlanmaz; yalnız `asset` video alanlarının `music_videos` ile ilişkisi (D4) yazılır |
| Migration tek kapısı | Her şema değişikliği (CHECK genişlemesi, TTL kolonu, indeks) yalnız ADR-014 özel PHP runner'ından geçer; bu ADR elle DDL yazmaz |
| Kod kanıtı | Video tablo CRUD PHP/JS = **0** → "video oynatma / altyazı çalışıyor / özellik hazır" iddiası yazılmaz |
| Vault'a yazma yetkisi | Bu işlem yalnız bu ADR dosyasını + `log.md` append'ini yazar; `index.md`/`brain.md`/`keys.md` düzeltmeleri **sonraki vault reset'ine ertelenir** (In-Place Refactoring + rapor-only) |
| Numara çakışması | Kural 7 "yeni ADR ≥ 088" ↔ arşiv 076 slotu — ADR-061/062/063/064/072/073/074/075 künyelerinden **aynı çakışma tekrar raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

CoreMusic'in video veri şeması **bu ADR'de tek kayıtta** sabitlenir: **(a)** **4 tablo** (`music_videos` · `video_playback_history` · `video_subtitles` · `playback_queue`) bulundukları dosyalarda kalır — dosya/kolon adı değişmez; BCNF + ADR-033/040/041 + ADR-072/073/074/075 **aynı ortak kural seti** uygulanır; **(b)** **SQL envanteri** yazılır (ayrı video dosyası **YOK** — `coremusic_musics.sql:539-648` + `coremusic_user.sql:168`; FK 5 gerçek + 1 yorum (tip uyuşmaz), indeks 11, CHECK 3); **(c)** **ilişki + indeks + medya metadatası 4 başlık**: D1 altyazı büyük metin stratejisi (URL-referans korunur, inline eşiği ⚠️), D2 playback state TTL/kullanıcı-oturum (TTL kolonu yok → süre ⚠️ debate + `expires_at` kalıbı), D3 video asset referansı (URL-rot → ADR-026, tekilleştirme → asset/ADR-092), D4 çift-metadata yüzeyi (birleştirme yok → yeni ADR); **(d)** **dürüst etiket**: şema + indeks kayıtları IMPLEMENTED, kod PLANNED, BCNF denetlenmemiş, PK grandfathered, audit açıkları defterde; **(e)** **sınır** = ADR-026 · ADR-031 · ADR-072 · ADR-073 · ADR-074 · ADR-075 · ADR-081 · ADR-092 (+ ADR-002/003/014/033/039/040/041) — tekrar yok. Karar kod üretmez, tablo açmaz, indeks/FK/TTL kolonu eklemez.

### 2.1 Neden Bu Seçenek?

Sorun *eksik şema değil, eksik karar*: dört tablo zaten diskte ve K5 indeksinde kayıtlı. Bu ADR sıfırdan video DB çizmek yerine mevcut şemayı **envanterler, medya-metadata politikasını yazar ve dürüst etiketler**: (1) SSOT korunur — dosya adı, tablo adları ve kolon adları değişmez (In-Place Refactoring); (2) "video SQL dosyası nerede?" ve "FK neden kısmi?" sorularının cevabı (**ayrı dosya yok + 3 gerçek/2 grandfathered/1 imkânsız + ADR-003/040**) tek yerde bağlanır; (3) "altyazı metni nerede, ne zaman bayatlar, URL kırılırsa ne olur, iki metadata yüzeyi hangisi SSOT?" dört sorusu ilk kez yazılırsa ilk geliştirici altyazıyı LONGTEXT'e gömmez, pozisyonu sonsuz tutmaz, URL'yi kopyalayarak çift kayıt üretmez; (4) podcast/radyo ADR'lerinden farklı olarak **video dosya adı dışında yaşıyor** — "dosya adına bakma" dersi (podcast `coremusic_musics.sql` içindeydi, radio başka dosyada sanılıyordu) burada da doğrulandı: tarama 19 dosyanın tamamını kapsadı; (5) `user_id` tip uyuşmazlığı ve çift-metadata örtüşmesi görünür kılınır, sessizce yutulmaz; (6) diskte olmayan `ADR-077`–`ADR-088`'e ve 14 boşluk numarasına wiki-link **kurulmaz** — uydurma hedef linklemek yerine düz metin + ⚠️ yazılır.

### 2.2 Teknik Detaylar

**(a) Tablo envanteri — 4 tablo (şema korunur, yeniden modellenmez):**

| # | Tablo (dosya) | Satır | Aday anahtar / unique | İndeks | CHECK | Amaç | Etiket |
|---|---------------|-------|------------------------|--------|-------|------|--------|
| 1 | `music_videos` (`coremusic_musics.sql`) | `:548` | `uq_mv_music (music_id)` `:570` — bir şarkının **tek** resmi videosu | `idx_mv_resolution` `:572` · `idx_mv_official` `:573` · `idx_mv_views` `:574` (DESC) | `:576` format (mp4/webm/mkv/avi) · `:577` resolution (480p–4320p) | Eser seviyesi video metadata: `music_id BINARY(16)` `:550` ✅ tip uyumlu · `video_url`/`thumbnail_url` VARCHAR(2048) `:553-554` · `duration_seconds` `:556` · `resolution/width_px/height_px` `:557-559` · `video_size_bytes` `:560` · `view_count`/`like_count` `:561-562` (sayıç — defter) · audit tam + is_deleted, **deleted_at YOK** | ✅ IMPLEMENTED (şema) · FK gerçek `:583` |
| 2 | `video_playback_history` (`coremusic_musics.sql`) | `:592` | — (PK + 3 indeks; **çift kayıt engeli YOK** — `uq (user_id, video_id)` yok → D2/ölçüm) | `idx_vph_user` `:606` · `idx_vph_video` `:607` · `idx_vph_updated` `:608` (DESC) | — | Kullanıcı×video oynatma durumu: `last_position` (saniye) `:596` · `watch_count` `:597` · `completion_pct DECIMAL(5,2)` `:598` · **TTL kolonu YOK** · `user_id` yorum-FK `:616` **tip uyuşmaz + deklare yok** | ✅ şema / ⚠️ TTL + unique + tip bulguları |
| 3 | `video_subtitles` (`coremusic_musics.sql`) | `:625` | `uq_vs_pair (video_id, language)` `:637` — video başına dil başına tek altyazı | `idx_vs_language` `:639` | `:641` format (srt/vtt/ass) | Altyazı/dil: `language`/`label` `:628-629` · `subtitle_url VARCHAR(2048)` `:630` — **metin kolonu YOK** · `format` default 'srt' `:631` · `is_auto_generated` `:632` · created+is_deleted, **updated_at YOK (0/1)** | ✅ şema / D1 politikası |
| 4 | `playback_queue` (`coremusic_user.sql`) | `:168` | PK `BINARY(16)` UUIDv7 `:169` ✅ ADR-041 uyumlu · (user_id, position) yorum adayı `:166` — **unique deklare YOK** | `idx_queue_user_position (user_id, position)` `:183` · `idx_queue_music` `:184` | — (source ENUM `:173`: user/ai/radio/podcast — **`video` değeri YOK**) | Kullanıcı kuyruğu: `music_id BINARY(16)` `:171` · `source`/`source_id` `:173-174` · `is_playing` `:175` · **is_deleted YOK** · 2 çapraz-DB FK `:187-189` (grandfathered) | ✅ şema / ⚠️ ENUM video kapsam dışı |

- **Toplam 4** — K5 indeksi (`k5-veri-yonetimi/README.md:181,200-202`) ile **4/4 birebir**.
- **Dosya dersi (§1.1/3):** ayrı `video*.sql` **YOK**; video + podcast + radyo = tek dosya `coremusic_musics.sql` — tarama 19 dosyanın tamamını kapsadı (dosya adına bakılmadan).
- **İsimlendirme (ADR-041 §2.2 ile hizalı):** tutarlı `snake_case` ✅ · `idx_`/`uq_` önekleri ✅ · **sapma:** 3 video tablosu PK `INT UNSIGNED AUTO_INCREMENT` ↔ ADR-041 "`BINARY(16)` UUIDv7" → **grandfathered** (ADR-075 ile aynı muamele, §5.1/7).
- **Audit açığı (ADR-033 kural 5):** `updated_at` eksik 1/4 (`video_subtitles`) · `is_deleted` eksik 1/4 (`playback_queue`) · `deleted_at` **0/4** → defter §5.1/5a.
- **Denormalizasyon defteri (ADR-041):** `view_count`/`like_count` `:561-562` — web (4) sayımının meşru denormalizasyon sınıfı; silinmez, ölçüm kapısı §5.1/3.

**(b) İlişki politikası — 5 gerçek FK + 1 imkânsız yorum-FK:**

| FK / bağımlılık | Dosya satırı | Hedef | Tip | Deklarasyon | Durum |
|-----------------|-------------|-------|-----|-------------|-------|
| `fk_mv_music` (music_videos.music_id) | `:583-584` | `coremusic_musics.musics(id)` — **same-DB** | BINARY(16) ↔ BINARY(16) ✅ | **VAR** (CASCADE) | ✅ |
| `fk_vph_video` (vph.video_id) | `:614-615` | `music_videos(id)` — same-DB | INT ↔ INT ✅ | **VAR** (CASCADE) | ✅ |
| `fk_vs_video` (vs.video_id) | `:647-648` | `music_videos(id)` — same-DB | INT ↔ INT ✅ | **VAR** (CASCADE) | ✅ |
| `user_id` (vph) — yorum-FK | `:616-617` | `coremusic_auth.users(id)` (çapraz-DB) | **INT UNSIGNED ↔ BINARY(16) uyuşmaz** | **YOK** ("not supported" yorumu — tip hatası **söylenmemiş**) | ⚠️ **imkânsız** (R4) |
| `fk_queue_user` + `fk_queue_music` (playback_queue) | `:187-189` | `coremusic_auth.users` + `coremusic_musics.musics` — çapraz-DB | BINARY(16) ↔ BINARY(16) ✅ | **VAR** (grandfathered — ADR-040 X-defteri `:194`, `:188-189`) | ✅ defterde |

- **Kural:** video şeması **ADR-003'ün hükmüne uyar**; `playback_queue` iki FK'ı 28'lik istisna defterinin parçasıdır → **defter değişmez, yenisi eklenemez**.
- **Yeni bulgu (§1.1/7):** `video_playback_history.user_id` hem çapraz-DB hem **tip uyuşmaz** — ADR-075'in AI'da bulduğu sınıfın video ayağı; yorum satırı "bir gün ekleriz" planı taşımaz → gerçek FK denemesi ADR-003 ihlali + tip hatası + migration patlaması üretir (R4). Yorum-FK **dokunulmaz**, düzeltme = yorumun kendisine not (§5.1/5c).
- **Zincir etkisi:** `music_videos` → `musics` CASCADE ✅; `video_playback_history`/`video_subtitles` → `music_videos` CASCADE → parça silinirse video satırları da düşer (aynı DB, iki kademe zincir çalışır) · `user_id` FK'sız → kullanıcı silimi uygulama seviyesinde (R6).

**(c) İndeks politikası — mevcut 11, yeni indeks YOK (ölçüm şartı):**

*Mevcut (IMPLEMENTED):* **11 girdi** (2 `uq_` + 9 `idx_`) + 3 CHECK (satır listesi §1.1/8) · PK-only tablo 0 → ADR-033 kural 3 eşiği karşılanır.

| Sorgu kalıbı | Karşılık | Değerlendirme |
|--------------|----------|----------------|
| "Şarkının videosu var mı?" `WHERE music_id=?` | `uq_mv_music` `:570` | ✅ tekil |
| Katalog filtre `WHERE resolution=? / is_official=?` | `idx_mv_resolution` · `idx_mv_official` | ✅ |
| En çok izlenenler | `idx_mv_views (view_count DESC)` `:574` | ✅ (sayıç okuma) |
| Kaldığım yer `WHERE user_id=?` (+son) | `idx_vph_user` `:606` + `idx_vph_updated DESC` `:608` | ✅ bugün yeterli; **upsert tekilliği `uq(user_id, video_id)` YOK** → kod fazında ölçüm (D2) |
| Video izleme analizi `WHERE video_id=?` | `idx_vph_video` `:607` | ✅ |
| Altyazı getir `WHERE video_id=? AND language=?` | `uq_vs_pair` `:637` **prefix** `(video_id, language)` | ✅ birleşik unique zaten prefix'i verir → ek indeks gereksiz |
| Dil bazlı sayım | `idx_vs_language` `:639` | ✅ |
| Kuyruk okuma `WHERE user_id=? ORDER BY position` | `idx_queue_user_position` `:183` | ✅ |
| Kuyrukta parça ara | `idx_queue_music` `:184` | ✅ |
| Metin içi sorgu (`title`/`description` arama) | **indeks YOK** (`media_catalog.ftx_asset_baslik` `:201` ayrı DB'de) | ⏳ kod + ölçüm olmadan **eklenmez** |

- **Ölçüm şartı:** hiçbir indeks `EXPLAIN` teyidi olmadan "hızlandırdı" denemez (ADR-033 workload denetimi PLANNED ile aynı kapı) → §5.1/3.

**(d) Medya metadatası politikası — 4 başlık (bu ADR'nin merkez kararı):**

**D1 — Altyazı büyük metin stratejisi:**

| Kural / yüzey | Bugünkü karşılık | Durum | Politika |
|----------------|------------------|-------|----------|
| Saklama modeli | `subtitle_url VARCHAR(2048)` `:630` — **dosya/URL referansı**; metin kolonu **YOK**, LONGTEXT video tablolarında **0** | ✅ IMPLEMENTED | **URL-referans modeli korunur** — altyazı metni DB'ye **inline gömülmez** (web 5: buffer pool/backup şişmesi); DB'de yalnız metadata (dil, etiket, format, otomatik-üretim bayrağı) |
| Format seti | CHECK `:641` = `srt/vtt/ass`, default `srt` `:631` | ✅ kapsayıcı (web 2: web=VTT, evrensellik=SRT, stil=ASS) | **Korunur**; genişleme (ör. `ttml`) = yeni CHECK DDL'si → **yeni ADR + ADR-014** |
| Inline metin eşiği | — (bugün yok) | ⚠️ **eşik sayılmadı** | Büyük altyazı inline saklanırsa ne zaman izin verilir? (ör. <N KB) — debate şartı §5.1/13; öncesinde "metin kolonu ekleyelim" denmez |
| Karşıt model (bilgi) | `podcast_transcripts.content LONGTEXT` `:520` (podcast — ADR-073) | ✅ başka domain | Altyazı, podcast transkripsiyonu **değildir** — iki domain, iki model; birleştirme yok |
| URL sağlığı | `subtitle_url` + `video_url`/`thumbnail_url` | ⚠️ **rot riski** (R2) | Erişim/imza/rot politikası = D3 + ADR-026; altyazı dosyası da teslim varlığı sayılır |

**D2 — Playback state TTL / kullanıcı-oturum:**

| Yüzey | Veri karakteri | Bugünkü durum | Politika |
|-------|----------------|---------------|----------|
| `video_playback_history` `:592` | kullanıcı×video **son pozisyon** (web 3: resume kalıbı — tek satır modeli) | **TTL kolonu YOK** · `uq(user_id, video_id)` YOK · `idx_vph_user` + `idx_vph_updated` var | **Saklama süresi sayılmadı → ⚠️ debate** (§5.1/12); tekillik/upsert kararı kod fazında ölçülür; temizlik kalıbı adayı `expires_at` (`user_downloads` `:206`) |
| `playback_queue` `:168` | oturum/kuyruk — `position` sıralı, `is_playing` | TTL **YOK** · `is_deleted` **YOK** | Bayat kuyruk (oturum bitmiş) temizliği → debate kapısı; ENUM `source` `:173` **video değeri yok** — kuyruk videoya özel kaynak almaz (bugün kapsam dışı, genişleme yeni ADR) |
| Kullanıcı-oturum ilişkisi | `user_id` FK'sız (tip uyuşmaz) + `playback_queue.user_id` FK'lı (çapraz) | ⚠️ asimetrik | Asimetri **korunur** (defter + tip); oturum-silme akışının iki tabloyu da temizlemesi uygulama sorumluluğu → §5.1/9 |
| Ses karşılığı | `user_listening_history` `coremusic_user.sql:90` | ✅ komşu | Video TTL'si ses geçmişiyle **eşzamanlı yazılmaz** — ayrı domain, ayrı kapı |

**D3 — Video asset referansı:**

| Referans | Alan | Risk | Politika |
|----------|------|------|----------|
| Ana varlık | `video_url VARCHAR(2048)` `:553` | URL rot / imzasız erişim / hotlink | **Erişim politikası bu ADR'de SEÇİLMEZ** → [[ADR-026-download-service-architecture]] (imzalı URL · uygulama stream · CDN opsiyonel) sınırıdır; bu ADR yalnız "referans `video_url`'dir, dosya DB'de değildir" hükmünü yazar |
| Görsel | `thumbnail_url` `:554` | aynı | Aynı kapı |
| Dosya tekilleştirme | `media_catalog.asset`: `dosya_yolu` UNIQUE `:193` · `sha256` UNIQUE `:168` · `tip ENUM('ses','video')` `:150` | — | **Tekilleştirme `asset`'tedir** (ADR-092); `music_videos.url` ona **referans olarak** bağlanır — URL'ler `asset`e **kopyalanmaz**, `asset` verisi `music_videos`'a taşınmaz |
| Kırılma senaryosu | URL ölü, dosya var | servis 404 | Fallback §4.4: teslim yoksa oynatma hatası uygulama seviyesinde (sessiz-fail sınırı); veri kaybı yok — metadata yaşar |
| Entegrasyon rakamı | ne sıklıkla reconcile | — | ⚠️ sayılmadı → debate §5.1/14 |

**D4 — Medya metadata sürüklenmesi (çift yüzey):**

| Yüzey | Sahip | İçerik | Rol |
|-------|-------|--------|-----|
| `music_videos` `:548` | `coremusic_musics` (katalog — ADR-040 `:156`) | resolution/width/height/size/format/duration + sayaçlar | **Katalog sayımı + vitrin** (eser seviyesi, URL ile teslim) |
| `media_catalog.asset` `:148` | `media_catalog` (ADR-092, SSOT = JSON — `:10-14`) | cozunurluk/fps/video_kodec/ses_kodec/bitrate_kbps/poster + sha256 + dosya yolu | **Arşiv envanteri** (dosya seviyesi, yeniden üretilebilir türetilmiş indeks) |

- **Karar:** iki yüzey **birleştirilmez**; ikisi de korunur; `music_videos`'taki teknik alanlarla `asset` video alanları arasındaki **rol dağıtımı ve reconcile kuralı YENİ ADR kapısı** (§5.1/6) — bugün çelişkili değer üretilirse hangisinin kazandığı yazılmadı → R3.
- **`media_catalog` DDL doğrulanamadı** (dosya başlığı `:25` "MySQL bu makinede yok — DDL çalıştırılıp doğrulanamadı") → o yüzeyin IMPLEMENTED etiketi ADR-092'ye ait, bu ADR iddia etmez.

**(e) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

| Katman | Öğe | Durum | Kanıt |
|--------|-----|-------|-------|
| Şema dosyası | 3 video tablo (`coremusic_musics.sql:539-648`) + 1 kuyruk (`coremusic_user.sql:168`) | ✅ **IMPLEMENTED** | dosya okuması + 19 dosyalık tarama (ayrı video dosyası 0) |
| FK/indeks/CHECK | 5 gerçek FK · 11 indeks · 3 CHECK | ✅ IMPLEMENTED | satır listesi §1.1/6-8 |
| Dizin kayıtları | 4 satır (slug hizalı) | ✅ kayıtlı | `.decisions/index.md:100` · `.ai/index.md:701` · `brain.md:1026` · `keys.md:293` |
| K5 indeks dokümanı | K5.1.2.6 + K5.1.3.17-19 | ✅ IMPLEMENTED (doküman) | `k5-veri-yonetimi/README.md:181,200-202` |
| **Kod (tablo CRUD)** | 4 tablo okuma/yazma | ⏳ **PLANNED** | PHP = **0** · JS/TS = **0** (subtitle = BEM sınıfı) |
| **Video endpoint dokümanı** | servis/endpoint tablosu | ⏳ **PLANNED** | `k8-servis` altında video dosyası **YOK** (AI'nın `ai-service.md` karşılığı yok) |
| BCNF denetimi | 3 tablo determinant denetimi | ⏳ **PLANNED** | ADR-040 `:38` (beyan ≠ denetim) |
| TTL / unique / inline eşiği | D1-D2 rakamları | ⚠️ **V.R.** (sayılmadı) | debate §5.1/11-14 |
| Çift-yüzey reconcile | `music_videos` ↔ `asset` | ⏳ PLANNED (yeni ADR) | §5.1/6 |

**(f) Sınır (tekrar yok):** teslim/imzalı URL/uygulama stream → [[ADR-026-download-service-architecture]] · istemci oynatıcı (PWA/Flutter) → [[ADR-031-mobile-strategy-pwa-flutter]] · outbox + WAL senkronu → [[ADR-081-multi-provider-data-sync]] · `media_catalog` türetilmiş indeksi + ULID + SSOT=JSON → [[ADR-092-media-dizin-ekseni-ve-ulid]] · sosyal ENUM/polymorphic (`entity_type` video içerir — ADR-040 `:163`) → [[ADR-072-social-database-schema]] · podcast şeması + LONGTEXT=6 → [[ADR-073-podcast-database-schema]] · radio şeması + partition gerekçesi → [[ADR-074-radio-database-schema]] · AI şeması + tip-uyuşmazlığı + ML politikası → [[ADR-075-ai-database-schema]] · DB sahipliği + FK istisna defteri → [[ADR-040-database-authority]] · normalizasyon → [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]] · migration tek kapısı → [[ADR-014-multi-db-migration-strategy]] · "çapraz-DB FK" hükmü → [[ADR-003-multi-db-bcnf]] · erişim katmanı → [[ADR-002-pdo-mandatory-no-orm]] · A4 veri domaini → [[ADR-039-7-service-platform-architecture]] · `ADR-077` (Studio DB — dizin `:101`, **dosya diskte YOK → düz metin + ⚠️**) · `ADR-078` · `ADR-079` · `ADR-082`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` · `ADR-080` (dosyalar diskte YOK → düz metin + ⚠️; 14 boşluk + ADR-082 satırsızlık §5.1/8 · §7.1).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Altyazı metnini DB'ye inline göm** (`content LONGTEXT` — podcast_transcripts modelini kopyala) | Tek sorguyla altyazı (dosya bağımlılığı kalkar), çeviri/arama kolay | Web 5: büyük metin buffer pool/backup şişirir; altyazı sorgulanmaz yalnız servis edilir → en kötü kullanım; ADR-073 LONGTEXT=6 zaten exception; `uq_vs_pair` + URL modeli bugün işliyor | Saklama kalıbına aykırı + ölçülmüş ihtiyaç yok → **ret**; inline eşiği D1 + debate §5.1/13 kapısı |
| 2 | **Ayrı `coremusic_video.sql` dosyasına taşı** (video tablolarını böl) | Dosya adı = domain (radyo/podcast'teki algı) | In-Place Refactoring yasağı (dosya adı değişikliği onay ister); ADR-040 `:156` 22-tablo sayımı + K5 indeks satırları + bu serinin üçü de tek-dosya gerçeğiyle yazıldı; taşıma = büyük DDL + ADR-014 + tüm satır referansları kırılır | Kanıtlanmış faydası yok, maliyeti yüksek → **ret**; "dosya adına bakma" dersi zaten içselleştirildi (§1.1/3) |
| 3 | **`video_playback_history`'e hemen TTL kolonu + `uq(user_id, video_id)` ekle** | Bayatlık ve çift-satır baştan çözülür | Süre kanıtlanmadı (iş/veri gereksinimi yok); unique = DDL → ADR-014 + veri temizliği (çift var mı?); ADR-073/075 disiplini "ölçülmüş talep" olmadan şema değiştirmez | Kanıtsız süre + süreç ihlali → **ret**; D2 + debate §5.1/12 kapısı |
| 4 | **İki metadata yüzeyini birleştir** (`music_videos` teknik alanlarını `asset`'e taşı veya tersi) | Çift kayıt/kürasyon kalkar, SSOT görünür | `asset` SSOT = **JSON** (ADR-092) + farklı DB + ULID/CHAR(26) ↔ INT PK tip farkı; katalog-sayımı vs arşiv-envanteri farklı sorular; taşıma = cross-DB veri hareketi + ADR-092 ile çelişki; kanıt (çelişen değer ölçümü) yok | Farklı otorite + kanıt yok → **ret**; rol dağıtımı yeni ADR §5.1/6 |
| 5 | **`user_id` yorum-FK'ını gerçek FK çevir** (ADR-003 istisnası iste) | Referans bütünlüğü görünür | Tip uyuşmaz (INT UNSIGNED ↔ BINARY(16)) → DDL zaten patlar; istisna defteri 28'de kalır (ADR-040); video ayağı AI ile aynı hüküm | Teknik imkânsızlık + süreç ihlali → **ret**; yorum-FK dokunulmaz (§2.2/b) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Video şeması ilk kez karar olarak kayıtlı:** 4 tablo / 5 gerçek FK / 11 indeks / 3 CHECK / D1-D4 politikası tek dosyada; "video tabloları nerede?" cevabı ilk satırlarda (**ayrı dosya yok — `coremusic_musics.sql` + `coremusic_user.sql`**).
- **"FK ekleyelim / metni gömelim / TTL koyalım" üç tuzağı önceden kapatıldı:** tip uyuşmazlığı + inline eşiği + süre rakamı üçü de gerekçesiyle yazıldı → ilk geliştirici kırık DDL, şişmiş LONGTEXT ve kanıtsız kolon ekleyemez.
- **Medya-metadata politikası ilk kez yazıldı:** altyazı URL-modeli, TTL kapısı, asset referans zinciri (`video_url` → ADR-026, `sha256` → ADR-092) ve çift-yüzey ayrımı — dördü de tek yerde.
- **"Dosya adına bakma" dersi pekişti:** 19 dosyalık tarama videoyu `coremusic_musics.sql` içinde buldu; ayrı dosya aramakla vakit kaybedilmedi ve podcast/radio kuralı çiğnenmedi.
- **Dürüst etiket üç katmanda:** şema+doküman IMPLEMENTED / kod PLANNED / BCNF denetlenmemiş — "video hazır" yanılgısına karşı üç çapa; K5 indeks kayıtları da kanıt olarak bağlandı.
- **Sınır net + kırık link üretilmedi:** ADR-026/031/072/073/074/075/081/092 bu şemaya dokunmaz; `ADR-077`–`ADR-088` + 14 boşluk numarası **düz metin + ⚠️** (wiki-link kurulmadı).

### 4.2 Olumsuz Sonuçlar

- **Dört başlıkta rakam yok:** TTL süresi, altyazı inline eşiği, asset reconcile sıklığı, upsert tekilliği **sayılmadı** → ⚠️; debate öncesi "eski pozisyon ne zaman silinir, metin ne zaman DB'ye girer?" cevapsız kalır.
- **Düzeltmeler yapılmadı** (rapor-only): `user_id` tip uyuşmazlığı yorumu · `video_subtitles.updated_at` 0/1 · `playback_queue.is_deleted`/ENUM `video` yokluğu · `deleted_at` 0/4 · `uq(user_id, video_id)` yokluğu · BCNF beyanları denetimsiz · `index.md:100` `[[../brain.md]]` önek kusuru — hepsi sonraki reset'e ertelendi; arada vault'ta eksik/çelişkili satırlar okunmaya devam eder.
- **Kod PLANNED olarak kaldı:** D1-D4 politikaları kod olmadan doğrulanamaz; resume/kuyruk/altyazı servis yolu hiç yazılmadı → "video oynatılıyor" iddiası bugün vault'ta kanıtsız.
- **Çift-metadata yüzeyi duruyor:** `music_videos` ↔ `asset` reconcile'siz yaşayacak; ilk çelişen değer hangisinin doğru olduğunu sormadan üretilebilir (R3).
- **BCNF hâlâ beyan düzeyinde:** 3 tablo determinant denetimi çalıştırılmadı → bu ADR şemanın doğru olduğunu **iddia etmez**, yalnız kaydeder.

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **Playback bayatlığı** — TTL'siz `video_playback_history`/`playback_queue`; eski pozisyonlar ve bitmiş oturum kuyrukları sonsuz yaşar, sorgular yavaşlar | 3 (olası) | 3 (orta) | D2 + süre debate şartı §5.1/12; temizlik kalıbı `expires_at` `:206`; upsert tekilliği ölçümü §5.1/3 |
| R2 | **Asset referans kırılması** — `video_url`/`thumbnail_url`/`subtitle_url` ölü URL (rot/imza yok); oynatma + altyazı sessiz 404 | 3 (olası) | 4 (yüksek) | D3: erişim politikası ADR-026'ya bağlandı; uygulama seviyesi hata/log; reconcile rakamı debate §5.1/14 |
| R3 | **Medya metadata sürüklenmesi** — `music_videos.resolution/width/height` ↔ `asset.cozunurluk/fps/kodek` farklı güncellenir → çelişkili teknik değer | 3 (olası) | 3 (orta) | D4: rol dağıtımı yazıldı (katalog vs arşiv), birleşme **yeni ADR** §5.1/6; `asset` DDL'si ADR-092'de doğrulanmadı ⚠️ |
| R4 | **`user_id` gerçek FK denemesi** — tip uyuşmazlığı + ADR-003 ihlali + migration patlaması | 2 (mümkün) | 4 (yüksek) | §2.2/b kenetli kural: yorum-FK **dokunulmaz**; ihlal → revert + log ERROR; ADR-075 ile aynı sınıf |
| R5 | **Altyazı inline şişmesi** — bir geliştirici "hızlı olsun" diye `content LONGTEXT` ekler → DB şişer + iki model kafa karıştırır | 2 (mümkün) | 3 (orta) | D1: inline yasak eşiği yazıldı, rakam debate §5.1/13; podcast_transcripts karşıt model olarak işaretli |
| R6 | **Kullanıcı-silme boşluğu** — `user_id` FK'sız → hesap silimi `video_playback_history`/`playback_queue` satırlarını bırakır (PII/temizlik) | 3 (olası) | 3 (orta) | §5.1/9: oturum-silme akışının uygulama seviyesinde iki tabloyu da temizlemesi; ADR-030 PII sınırına bağlanır |

### 4.4 Fallback (geri birleşim / geri dönüş)

Debate **RED** çıkarsa ya da karar değiştirilirse: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `index.md:100`/`index.md:701`/`brain.md:1026`/`keys.md:293` satırları **silinmez** (değişmez kalır), `log.md`'ye `ADR-076 RED (debate …)` append edilir. Bu durumda (i) 4 tablo şeması **dosyalarda kalmaya devam eder** (SQL dosyaları bu ADR'den bağımsızdır, dokunulmaz), (ii) D1-D4 politikaları, §5.1/2-4 ölçümleri ve §5.1/6-7 kapıları **kural olmaktan çıkar** (öneriye döner), (iii) IMPLEMENTED/PLANNED etiketi kalksa bile §1.1'deki 0/0 kod sayımları, tip uyuşmazlığı, TTL yokluğu ve audit açıkları **bağımsız bulgu olarak geçerlidir**, (iv) 4/5/11/3 envanterleri de bağımsız ölçüm olarak kalır. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks/brain/keys satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

### 4.5 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| Format/dil + tip-uyuşmazlığı + dürüst etiket referansı | [[ADR-075-ai-database-schema]] | ✅ aynı iskelet + aynı etiket disiplini |
| Aynı seri şema ADR'leri | [[ADR-074-radio-database-schema]] · [[ADR-073-podcast-database-schema]] · [[ADR-072-social-database-schema]] | ✅ tekrar yok (partition/LONGTEXT/ENUM orada) |
| BCNF kural seti + denetim PLANNED | [[ADR-033-sql-normalization-strategy]] | ✅ uygulandı (audit/beyan açığı §5.1/5) |
| DB sahipliği + 28 FK istisna defteri | [[ADR-040-database-authority]] (`:38` · `:49` · `:156` · `:163` · `:194`) | ✅ `coremusic_musics` 22 tablo `music` · X-defterinde `playback_queue` FK'ları |
| Adlandırma/UUID/audit + denormalizasyon defteri | [[ADR-041-database-normalization-supplementary]] | ✅ uygulandı · UUID grandfathered §5.1/7 · sayaçlar §5.1/5b |
| Çapraz-DB FK hükmü | [[ADR-003-multi-db-bcnf]] | ✅ video ayağında ihlal **yok** (1 yorum-FK deklare değil) |
| Migration tek kapısı | [[ADR-014-multi-db-migration-strategy]] | ✅ tüm DDL (TTL/CHECK/indeks dahil) buradan |
| Teslim yolu (imzalı URL/stream/CDN) | [[ADR-026-download-service-architecture]] | ✅ sınır (D3 erişim politikası orada) |
| İstemci oynatıcı stratejisi | [[ADR-031-mobile-strategy-pwa-flutter]] | ✅ sınır |
| Outbox + WAL senkronu | [[ADR-081-multi-provider-data-sync]] | ✅ sınır (kapsam dışı) |
| Arşiv indeksi + SSOT=JSON + asset | [[ADR-092-media-dizin-ekseni-ve-ulid]] | ✅ sınır (D4 yüzeyi) · DDL ⚠️ doğrulanmadı |
| Servis sahibi (A4) | [[ADR-039-7-service-platform-architecture]] | ⚠️ video servisi PLANNED (endpoint dokümanı yok) |
| K5 tablo indeksi | `architecture/k5-veri-yonetimi/README.md:181,200-202` | ✅ 4/4 kayıtlı |
| Dizin slug satırı | [[../index.md]] satır **100** | ✅ hizalı; `[[../brain.md]]` hedef düzeltmesi §5.1/10'da ertelendi |
| Studio DB şeması | **`ADR-077`** | ⚠️ **diskte YOK → düz metin + ⚠️ (wiki-link kurulmadı)** |
| Eksik numaralar (dosya + dizin satırı yok) | `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` | ⚠️ **14 numara** → §5.1/8 + §7.1 (rapor-only) · `ADR-082` satırsız |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Debate (3 tur / 20 persona) + Tech Lead onayı — TTL süresi, altyazı inline eşiği, asset reconcile ve çift-yüzey kapıları dahil | MO + persona | 1 gün | ✅ **TAMAMLANDI** (3 tur / 20 persona, 18/2/0 KABUL — §7.2) |
| 2 | **Ölçüm kapısı (resume/kuyruk):** `video_playback_history` çift-satır/upsert davranışı + kuyruk büyümesi kod yazıldıktan sonra ölçülür; `uq(user_id, video_id)` ancak ölçümle eklenir (DDL → ADR-014) | Data + Backend | kod fazı | ⏳ PLANNED (→ debate şartı §5.1/12) |
| 3 | **Workload/EXPLAIN:** arama (FULLTEXT `title/description`) ve filtre indeksleri kod sonrası `EXPLAIN` ile kalibre edilir; bugün yeni indeks **YOK** (11 mevcut korunur) | Data + Backend | kod fazı | ⏳ PLANNED (ADR-033 ile aynı kapı) |
| 4 | **TTL kapısı:** `video_playback_history` + `playback_queue` saklama süresi ölçülür → kolon + temizlik işi (kalıp adayı `expires_at` `:206`) **yeni ADR** olarak açılır; mevcut tabloya elle DDL yapılmaz | Data Engineer + MO | aşama 2 | ⏳ PLANNED (⚠️ süre rakamı yok) |
| 5 | **Drift raporları (düzeltilmedi):** (5a) audit açıkları — `video_subtitles.updated_at` 0/1 · `playback_queue.is_deleted` yok · `deleted_at` 0/4 → ADR-033/041 defteri · (5b) `view_count`/`like_count` denormalizasyonu defterde işaretli kalsın · (5c) `user_id` yorum-FK **tip uyuşmazlığı** — yorum metnine not · (5d) `playback_queue` `source` ENUM'da `video` yokluğu · (5e) `uq (user_id, video_id)` yokluğu · (5f) BCNF beyanları denetimsiz (`:545,:589,:622`) · (5g) şablon yolu `.ai/templates/…` ↔ gerçek `.ai/.templates/…` · (5h) `media_catalog.sql:5` ADR-092 atfı + DDL doğrulanamadı | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 6 | **Çift-metadata yüzeyi kapısı:** `music_videos` teknik alanları ↔ `media_catalog.asset` video alanları — rol dağıtımı + reconcile kuralı **YENİ ADR** (ADR-092 sınırıyla birlikte) | Data Engineer + MO | aşama 2 | ⏳ PLANNED (⚠️ bu ADR'de birleştirilmedi) |
| 7 | **Kimlik tipi kapısı:** 3 video tablosu `INT UNSIGNED` PK ↔ ADR-041 `BINARY(16)` UUIDv7 — mevcut grandfathered, **yeni tabloda UUID** (playback_queue zaten uyumlu) | Data Engineer | sonraki vault reset | ⏳ rapor-only |
| 8 | **Raporlar (düzeltilmedi):** **(8a)** 14 atlanan boşluk `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` (dosya YOK **+** `.decisions/index.md` satırı YOK) · **(8b)** `ADR-082` (ne dosya ne satır — `:104` = 081, `:105` = 083) · **(8c)** `ADR-077`–`ADR-079` / `ADR-083`–`ADR-088` dizin satırı var / dosya YOK (076 bu işlemde doldu) · **(8d)** numara çakışması (kural 7 ≥ 088 ↔ 076 slotu) | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 9 | **Kod yüzeyi:** repository/servis katmanı (4 tablo CRUD + resume upsert + kuyruk + altyazı servisi) + oynatma/altyazı endpoint dokümanı (`k8-servis` altında video dosyası yok) + kullanıcı-silme temizliği (R6) + entegrasyon testi | Backend + QA | 5 gün | ⏳ PLANNED |
| 10 | `index.md:100` `[[../brain.md]]` → gerçek ADR hedefine düzeltmesi + `brain.md:1026`/`keys.md:293`/`index.md:701` satır metinlerinin bu ADR'ye bağlanması | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi** (rapor-only) |
| 11 | **Şart adayı 1 (debate sonrası kesinleşir)** — TTL süresi + kuyruk temizlik süresi rakamlarının ölçülmesi ve D2'ye bağlanması | Data + Backend | aşama 2 | ⏳ debate PENDING → şart bu ADR'de henüz yazılmaz |
| 12 | **Şart adayı 2 (debate)** — playback TTL/kalıcılık kararı (D2): süre rakamı + upsert tekilliği + oturum-silme akışının iki tabloyu da temizlemesi | Data + Backend | aşama 2 | ⏳ debate PENDING |
| 13 | **Şart adayı 3 (debate)** — altyazı inline metin eşiği (D1): DB'ye gömülecek metin üst sınırı; öncesinde `content` kolonu eklenmez | Data + Security | aşama 2 | ⏳ debate PENDING |
| 14 | **Şart adayı 4 (debate)** — asset reconcile sıklığı (D3): `video_url` ↔ `media_catalog.asset` (`sha256`/`dosya_yolu`) ne sıklıkla ve nasıl doğrulanır; erişim/imza ADR-026'ya bağlanır | Data + Backend | aşama 2 | ⏳ debate PENDING |
| 15 | **Şart 1a (debate)** — FK kararı: `user_id` yorum-FK tip uyuşmazlığı (INT UNSIGNED ↔ BINARY(16)) için ya **uyum sağlayıcı** ya da **FK'sız devam gerekçesi** kesin kararı yazılır; gerçek FK denemesi yasak kalır (ADR-003/040) | Data + MO | aşama 2 | ⏳ **debate şartı** (§7.2/1) |
| 16 | **Şart 2 (debate)** — şema IMPLEMENTED / **kod PLANNED** etiketinin korunması (video CRUD + endpoint dokümanı yazılana kadar "video hazır" dendiğinde bu ADR'ye atıf zorunlu) | MO + Backend | aşama 2 | ⏳ **debate şartı** (§7.2/2) |
| 17 | **Şart 3 (debate)** — audit + ENUM tamamlama (aşama 2): `updated_at` 0/1 · `is_deleted` 1/4 · `deleted_at` 0/4 · `source` ENUM `video` yokluğu · çift-yüzey reconcile | Data + Backend | aşama 2 | ⏳ **debate şartı** (§7.2/3) |
| 18 | **Şart 1 (debate KABUL — §6.5/1) — FK kararı + ADR-092 V.R. düzeltmesi:** (1a) `video_playback_history.user_id` yorum-FK tip uyuşmazlığı (INT UNSIGNED ↔ BINARY(16)) için **uyum veya gerekçeli FK'sız** kesin karar yazılır — gerçek FK denemesi yasak kalır (ADR-003/040) · (1b) rapordaki `ADR-092` çapraz referansı **gerçek kaynakla hizalanır** veya düz metine indirgenir | Data + MO | aşama 2 | ⏳ debate **KABUL şartı** (§6.5/1) |
| 19 | **Şart 2 (debate KABUL — §6.5/2) — metadata tek otorite:** `music_videos` teknik alanları ↔ `media_catalog.asset` video alanları (D4 çift yüzey) için **tek otorite kararı** + reconcile kuralı yazılır → yeni ADR kapısı (§5.1/6 ile aynı kapı) | Data + MO | aşama 2 | ⏳ debate **KABUL şartı** (§6.5/2) |
| 20 | **Şart 3 (debate KABUL — §6.5/3) — TTL/önbellek ölçüm kapısı:** playback TTL + subtitle önbellek/inline eşikleri **ölçülmeden sayılmaz** — D2 saklama süresi ve D1 inline eşiği aşama 2 ölçüm kapısına bağlanır (şart adayı §5.1/11-14 bu şartta birleşir) | Data + Backend | aşama 2 | ⏳ debate **KABUL şartı** (§6.5/3) |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez**), `index.md:100` / `index.md:701` / `brain.md:1026` / `keys.md:293` satırları **silinmez**, `log.md`'ye `ADR-076 RED (debate …)` append edilir; bu durumda (i) 4 tablo şeması `coremusic_musics.sql`/`coremusic_user.sql` içinde **kalmaya devam eder** (SQL dosyaları bu ADR'den bağımsızdır, dokunulmaz), (ii) D1-D4 medya-metadata politikaları, §5.1/2-4 ölçüm/TTL fazları ve §5.1/6-7 kapıları **kural olmaktan çıkar** (öneriye döner), (iii) 5 gerçek FK + 2 grandfathered zaten ADR-003/040'ta durduğu için otorite kaybı olmaz, (iv) §1.1'deki kod 0/0 sayımları, tip uyuşmazlığı, TTL yokluğu, çift-yüzey ve audit bulguları **bağımsız bulgu olarak geçerlidir**. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks/brain/keys satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

---

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır | Ne | Etiket |
|-------|-------|----|--------|
| `.ai/.decisions/index.md` | `:100` | slug `ADR-076-video-database-schema` | ✅ hizalı (dosya adı ile birebir) · satır `[[../brain.md]]` önekli kusurlu → §5.1/10 |
| `.ai/index.md` | `:701` | "ADR-076 … Video DB Schema (music_videos, playback, subtitles)" | ✅ kayıtlı |
| `.ai/brain.md` | `:1026` | aynı özet satırı | ✅ kayıtlı (metin bu ADR ile hizalanacak → §5.1/10) |
| `.ai/keys.md` | `:293` | "ADR-076 \| video database, music_videos, playback, subtitles" | ✅ kayıtlı |
| `.ai/.sql/mysql/coremusic_musics.sql` | `:539-648` | VİDEO BÖLÜMÜ — **3 tablo** + 3 FK + 8 indeks + 3 CHECK (satır detayı §2.2/a-b) | ✅ **birincil kanıt** (IMPLEMENTED) |
| `.ai/.sql/mysql/coremusic_user.sql` | `:168-189` | `playback_queue` (UUIDv7 + ENUM source + 2 grandfathered FK + 2 indeks) | ✅ 4. tablo kanıtı |
| `.ai/.sql/mysql/media_catalog.sql` | `:5` · `:148-209` | ADR-092 atfı · `asset` (`tip` ENUM ses/video `:150`, video teknik alanlar `:176-188`, sha256 `:168`) | ✅ D4 yüzeyi / ⚠️ DDL `:25` doğrulanmadı |
| `.ai/.sql/mysql/` (19 dosya) | glob | ayrı `video*.sql` **0** — video podcast/radio ile aynı dosyada | ✅ §1.1/3 |
| LONGTEXT taraması (19 dosya) | 6 isabet | cms `:31,:62` · musics `:176,:520` · patch `:63` · system `:138` — **video 0** | ✅ ADR-073 bulgusu korundu |
| Kod taraması (PHP/JS) | 0 / 0 | 4 tablo adı PHP CRUD = 0 · JS subtitle = 33 (hepsi BEM) · `oauth-platforms.php:68` | ⏳ **kod PLANNED** |
| `architecture/k5-veri-yonetimi/README.md` | `:181` · `:200-202` | playback_queue + 3 video tablo indeks kaydı | ✅ doküman |
| `architecture/k10-uygulama/media-panel.md` | `:12` | subtitle yönetimi + HLS/DASH (istemci dokümanı) | ✅ doküman / ⏳ kod |
| `.ai/.decisions/accepted/ADR-040-database-authority.md` | `:38` · `:49` · `:156` · `:163` · `:194` | BCNF iddia ≠ denetim · 28 FK defteri · `coremusic_musics` 22 tablo (video dahil) · polymorphic video · X-defteri `:188-189` | ✅ dosya var — **bağlayıcı otorite** |
| `.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md` · `ADR-041-database-normalization-supplementary.md` | künye · §2.2 | 5 kural + denetim PLANNED · adlandırma/UUID/audit/defter | ✅ dosya var — **kural seti** |
| `.ai/.decisions/accepted/ADR-075-ai-database-schema.md` | künye · §2.2/b · §6.3 | format/dil + tip-uyuşmazlığı sınıfı + wiki-link sayım yöntemi | ✅ dosya var — **format referansı** |
| `.ai/.decisions/accepted/ADR-026-download-service-architecture.md` · `ADR-031-mobile-strategy-pwa-flutter.md` · `ADR-081-multi-provider-data-sync.md` · `ADR-092-media-dizin-ekseni-ve-ulid.md` | künye | teslim · istemci · sync · arşiv indeksi | ✅ dosya var — **sınır** |
| `.ai/.decisions/accepted/ADR-002/003/014/039/072/073/074` | künye | PDO · FK yasağı · migration · A4 · sosyal · podcast · radio | ✅ dosya var (001-037 frozen — yalnız okunur) |
| `.ai/.templates/adr/adr-template.md` | §3 · §6-§7 şablonu | 7 bölüm iskeleti + §1.3 9 alan (Guardrail #16) | ✅ şablon (görevdeki `.ai/templates/…` yolu **YOK** → §5.1/5g) |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-075-ai-database-schema]] (+ [[ADR-074-radio-database-schema]] · [[ADR-073-podcast-database-schema]] · [[ADR-072-social-database-schema]]) · otorite: [[ADR-040-database-authority]] · kural seti: [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]]
- İlgili ADR'ler: [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-003-multi-db-bcnf]] · [[ADR-014-multi-db-migration-strategy]] · [[ADR-026-download-service-architecture]] · [[ADR-031-mobile-strategy-pwa-flutter]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-072-social-database-schema]] · [[ADR-073-podcast-database-schema]] · [[ADR-074-radio-database-schema]] · [[ADR-075-ai-database-schema]] · [[ADR-081-multi-provider-data-sync]] · [[ADR-092-media-dizin-ekseni-ve-ulid]]
- Şema/kod kanıtları: [[../../.sql/mysql/coremusic_musics.sql]] · [[../../.sql/mysql/coremusic_user.sql]] · [[../../.sql/mysql/media_catalog.sql]] · [[../../architecture/k5-veri-yonetimi/README]] · [[../../architecture/k10-uygulama/media-panel]]
- Vault kökü: [[../index.md]] · [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../log.md]] · [[../../CLAUDE.md]]
- Dizin kayıtları (düz metin — dizin hedefidir, .md olmadığı için wiki-link değil): `.ai/.sql/mysql/` (19 dosya) · `.ai/.decisions/draft/` · `.ai/.decisions/rejected/` · `.claude/skills/prompt-maker/references/10-web-research-protocol.md`
- Diskte **olmayan** (düz metin + ⚠️, linklenmez): **`ADR-077`** · `ADR-078` · `ADR-079` · `ADR-080` · `ADR-082`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071`

### 6.3 Wiki-Link Sayımı

| Öğe | Değer |
|------|-------|
| Wiki-link toplamı (bu dosya) | **sayım betiğiyle doğrulandı** — iki-köşeli parantez kaydı; path-form (`../index.md`, `../../brain.md`, `../../.sql/mysql/…`) diskte mevcut; slug-form (`[[ADR-075-ai-database-schema]]` …) ADR-072/073/074/075 house biçimi, `accepted/` dizininde `<slug>.md` ile basename çözülüyor |
| Diskte olan hedef | **tümü diskte** — dosya doğrulaması: ADR-002/003/014/026/031/033/039/040/041/072/073/074/075/081/092 + `.templates` + `.sql` + `k5`/`k10` (glob/oku ile 2026-10-01 doğrulandı) |
| Düz metin + ⚠️ (linklenmeyen) | **`ADR-077`** · `ADR-078` · `ADR-079` · `ADR-080` · `ADR-082`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` |

### 6.4 Debate Notu (✅ TAMAMLANDI)

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona çalıştırıldı; sonuç **18 kabul / 2 çekimser / 0 red → KABUL** (2026-10-01; kayıt §7.2) |
| Uygulanan format | 3 tur / 20 persona (ADR-072/073/074/075 ile aynı) · sayımlar §7.2'ye işlendi |
| Debate öncesi açık kapılar → aday şartlar | TTL süresi + kuyruk temizliği (D2) → **şart adayı 1-2** (§5.1/11-12) · altyazı inline eşiği (D1) → **şart adayı 3** (§5.1/13) · asset reconcile (D3) → **şart adayı 4** (§5.1/14) · IMPLEMENTED/PLANNED etiketi → **şart 2** adayı (§5.1/16) · audit/ENUM → **şart 3** adayı (§5.1/17) — hepsi Tur 2'de olgunlaştı, **3 kesin şart** §6.5 + §5.1/18-20 olarak yazıldı |
| Debate RED ise | §5.2 geri dönüş + §4.4 fallback birlikte uygulanır — **bu debate KABUL → uygulanmadı** |

### 6.5 Debate Şartları (3/3 — §7.2 KABUL)

| # | Şart | Kapsam | Madde | Durum |
|---|------|--------|-------|-------|
| 1 | **FK kararı + ADR-092 V.R. düzeltmesi (1a-1b)** | 1a: `video_playback_history.user_id` yorum-FK tip uyuşmazlığı (INT UNSIGNED ↔ BINARY(16)) için **uyum veya gerekçeli FK'sız** kesin karar — gerçek FK denemesi yasak kalır ([[ADR-003-multi-db-bcnf]] · [[ADR-040-database-authority]]) · 1b: rapordaki `ADR-092` çapraz referansı **gerçek kaynakla hizalanır** veya düz metine indirgenir | §5.1/18 | ⏳ aşama 2 |
| 2 | **Metadata tek otorite** | `music_videos` teknik alanları ↔ `media_catalog.asset` video alanları (D4 çift yüzey) için **tek otorite kararı** + reconcile kuralı → yeni ADR kapısı | §5.1/19 | ⏳ aşama 2 |
| 3 | **TTL/önbellek ölçüm kapısı** | playback TTL süresi + subtitle önbellek/inline eşiği **ölçülmeden sayılmaz**; D2 saklama süresi ve D1 inline eşiği aşama 2 ölçümüne bağlanır | §5.1/20 | ⏳ aşama 2 |

> **Doğrulama notu (2026-10-01 glob):** `accepted/ADR-092-media-dizin-ekseni-ve-ulid.md` **diskte mevcut** → şart 1b "gerçek kaynak" ayağı için kanıt; `ADR-077` diskte **YOK** (ADR-072–076 mevcut) → düz metin + ⚠️ korunur.

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-10-01 | ✅ |
| Tech Lead | — | 2026-10-01 | ✅ (debate 3/20 — 18/2/0 KABUL) |
| Arch Lead | — | — | ⏳ |

**Numara boşlukları raporu (§5.1/8 ile aynı — rapor-only, düzeltilmedi):** `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065` · `ADR-066` · `ADR-067` · `ADR-068` · `ADR-069` · `ADR-070` · `ADR-071` · `ADR-080` = **14 numara** (dosya YOK **+** `.decisions/index.md` satırı YOK). Ayrıca `ADR-082` (dosya YOK, satır YOK — dizin `:104` = ADR-081, `:105` = ADR-083) ve `ADR-077`–`ADR-079` / `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK — bu ADR `ADR-076`'yı doldurdu). Bu ADR numara serisine **dokunmaz**, yalnızca raporlar.

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona · sonuç **18 kabul / 2 çekimser / 0 red → KABUL** (2026-10-01) |
| **Tur 1 — bulgular (20 persona)** | ayrı `video*.sql` **YOK** · 3 tablo `coremusic_musics.sql:539-648` + `playback_queue` `coremusic_user.sql:168` · 3 gerçek FK + 2 grandfathered çapraz-DB FK + **1 imkânsız yorum-FK** (INT UNSIGNED ↔ BINARY(16) — ADR-075 ile aynı hastalık) · 11 index, 3 CHECK · LONGTEXT video tablolarında **0** · `subtitle_url VARCHAR(2048)` (srt/vtt/ass), metin kolonu yok → **D1 URL stratejisi** · **D2** playback TTL/session · **D3** video asset referansı · **D4** çift metadata yüzeyi (`media_catalog.asset`) + raporda geçen "ADR-092" o taramada doğrulanamadı → **V.R.** · kod yüzeyi PHP CRUD **0**, JS "subtitle" 33 isabet = BEM → **şema IMPLEMENTED / kod PLANNED** · 6 web sorgusu / 60 isabet · 14 atlanan boşluk + ADR-082 index satırı yok (§5.1/§7.1) · `index.md:100` slug hizalı · ADR-077/092 diskte bulunamadı → düz metin + V.R. |
| Tur 1 oyları | **16 kabul/neutral · 4 uyarı** (Critic: FK + ADR-092 şartı · QA: metadata yüzeyi şartı · Perf: TTL ölçüm) |
| **Tur 2 — itiraz → çözüm** | (1) imkânsız yorum-FK (tip uyuşmazlığı) → **FK kararı** (uyum veya gerekçeli FK'sız) → **şart 1a** · (2) `ADR-092` çapraz referansı diskte doğrulanamıyor → **V.R. + gerçek kaynak veya düz metin** → **şart 1b** · (3) çift metadata yüzeyi (`media_catalog.asset` ↔ video tabloları) → **tek otorite kararı** → **şart 2** · (4) playback TTL/subtitle önbellek eşikleri sayılmadı → **ölçüm kapısı (aşama 2)** → **şart 3** |
| **Tur 3 — oy** | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Kesinleşen 3 şart | **(1)** FK kararı + ADR-092 V.R. düzeltmesi (1a-1b) · **(2)** metadata tek otorite · **(3)** TTL/önbellek ölçüm kapısı — ayrıntı **§6.5**, maddeler **§5.1/18-20** |
| Debate RED ise | §5.2 geri dönüş + §4.4 fallback birlikte uygulanır — **bu debate KABUL → uygulanmadı** |

---

*ADR-076 v1.0.0 — 2026-10-01 Created · Authority: CoreMusic Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
