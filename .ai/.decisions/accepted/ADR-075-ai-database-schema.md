---
title: "CoreMusic — ADR-075: AI Database Schema (coremusic_ai — 6 tablo — user_preference_profiles/listening_features/recommendation_history/audio_features/model_versions/training_jobs · 0 FK deklarasyonu (7 yorum-FK + 3 çapraz-DB başlık bağımlılığı · INT UNSIGNED ↔ BINARY(16) tip uyuşmazlığı) · 16 indeks · 5 CHECK · ML veri politikası: feature store çift katman + model registry değişmezlik/stage + öneri TTL kapısı + tercih yaşam döngüsü · şema IMPLEMENTED / kod PLANNED)"
type: "architecture-decision"
category: "database"
date: "2026-10-01"
updated: "2026-10-01"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic AI veri şeması kararı: (a) **6 tablo** (`user_preference_profiles` `:31` · `listening_features` `:55` · `recommendation_history` `:80` · `audio_features` `:109` · `model_versions` `:139` · `training_jobs` `:166`) ayrı dosyada DEĞİL, `.ai/.sql/mysql/coremusic_ai.sql` içinde (188 satır / 11.417 bayt, v8.0.0) — şema korunur, yeniden modellenmez; (b) **SQL envanteri**: 6 tablo · **0 FK deklarasyonu** (7 yorum-FK + 3 başlık bağımlılığı; tip uyuşmazlığı dahil — ADR-003/040 kuralına uyumlu, 28 istisna defterinde AI satırı YOK) · **16 indeks** (4 `uq_` + 12 `idx_`) + **5 CHECK**; (c) **ML veri politikası 4 başlık**: feature store çift katman (offline = MySQL IMPLEMENTED / online = ölçüm kapısı PLANNED), model registry (`uq_mv_pair` değişmezlik + `status` 4-stage tek yönlü + rollback = eski sürümü yeniden active), öneri TTL (`recommendation_history` = büyüyen tek tablo → süre sayılmadı ⚠️ + `api_calls` kalıbı partition kapısı), tercih yaşam döngüsü (`user_preferences` = ayar SSOT / `user_preference_profiles` = AI türevi; 6 aşama; silme açığı defterde); (d) **dürüst etiket**: şema+doküman IMPLEMENTED, kod PLANNED (PHP/JS CRUD = 0), AIEngine `return []` STUB (`:212`/`:217`/`:234`), BCNF beyanı denetlenmemiş, başlık `deleted_at`/`BINARY(16)` iddiaları 0/6 ile çelişir; (e) **sınır** = ADR-030 (strateji+sessiz fail) · ADR-049 (prompt loader + stub) · ADR-072/073/074 (aynı seri) · ADR-081 (sync) — tekrar yok; (f) çift-tablo örtüşmeleri, TTL/freshness/retention rakamları ve GDPR silme kapısı = yeni ADR kapıları."
kaynak: "Disk kanıtı taraması (2026-10-01: `.ai/.sql/mysql/coremusic_ai.sql` = **188 satır / 11.417 bayt** v8.0.0 (Date 2026-08-10) · CREATE TABLE `:31, :55, :80, :109, :139, :166` · footer `:187` '6 tablo tamamlandı' · FK deklarasyonu **0** (ADD CONSTRAINT/FOREIGN KEY = 0 isabet) · yorum-FK 7 = `:48, :72-73, :101-102, :132, :183` · başlık bağımlılıkları `:14-17` (user_id→coremusic_auth.users, music_id→coremusic_musics.musics, model_version_id→coremusic_ai.model_versions) · indeks **16** = uq `:45, :68, :127, :154` + idx `:69, :92-96, :128-129, :155-156, :178-179` · CHECK **5** = `:97, :98, :157, :158, :180` · audit: created+updated yalnız upp `:41-43` + lf `:64-66` · created_at 5/6 (audio_features **0** — yalnız `analyzed_at` `:125`) · is_deleted 3/6 (`:89, :151, :175`) · **deleted_at 0/6** (başlık `:12` iddiasıyla çelişir) · başlık `:11` 'BINARY(16)' ↔ PK **6/6 INT UNSIGNED AUTO_INCREMENT** (çelişki) · tip: user_id/music_id INT UNSIGNED (`:33, :57-58, :82-83, :111`) ↔ users.id/musics.id **BINARY(16)** (`coremusic_auth.sql` users · `coremusic_musics.sql:88`) → yorum-FK **tip olarak enforce edilemez** · BCNF beyanı `:4` + `:29, :53, :78, :107, :137, :164` (ADR-040 `:38` = iddia, denetim DEĞİL) · komşu: `coremusic_user.sql:57` user_preferences (BINARY(16) `:58`, `autoplay_recommendations` `:67`, gerçek FK `:83`) + `:90` user_listening_history · `coremusic_musics.sql:344` music_audio_features (BINARY(16), unique `:364`, gerçek FK `:367`) ↔ `coremusic_ai.audio_features` **örtüşme** · partition farkı: gerçek FK 0 → MySQL §26.6 yasağı bu şemada geçerli değil (radyo 2 FK'dan farklı), PK (id) → kolon ekleme DDL kapısı · **Kod yüzeyi**: tablo adı PHP CRUD = **0** · JS/TS = **0** · `user_preferences|user_listening_history|music_audio_features` PHP = **1** (ThemeManager.php:66 yorum — DB yok) · AIEngine stub: `return []` `:212, :217, :234` · placeholder `analyzeHardware` `:149-155` · `extractFeatures` `:319-326` (bpm 120.0, key 'C') · ADR-049 alıntıları `:216-225/:230-242/:145-163/:322-334` ↔ güncel `:209-218/:223-235/:138-156/:315-327` → **satır kayması** (son commit `c208092` AI güncellemeleri içerir — neden kesin değil ⚠️, rapor-only) · ENUM: `coremusic_user.sql:173` kuyruk `source 'ai'` + `:174` source_id (yorum) · ADR-040 `:33` 18 dosya ↔ disk **19 dosya** (envanter drift) · ADR-040 `:165` `coremusic_ai` 6 tablo sahibi `music` ATAMA (LLM kodu 0) · ADR-040 `:49` 28 FK istisnası (user 11 + social 13 + system 4) — **AI satırı YOK** · **Doküman**: `k8-servis/ai-service.md:28-65` 4 endpoint tablosu (analyze/recommendations/voice/learn) + `:45` radio · `k4-yapay-zeka/` = **14 dosya** (recommendation-engine.md, ml-infrastructure.md) · dizin: `.decisions/index.md:99` (slug hizalı, `[[../brain.md]]` önekli kusurlu) · `.ai/index.md:700` · `brain.md:1016` · `keys.md:292` · şablon `.ai/.templates/adr/adr-template.md` (görevdeki `.ai/templates/…` yolu YOK)) + web araştırması (**5 sorgu / 23 benzersiz kaynak** — §1.3)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-075: AI Database Schema

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-01 — **Debate:** ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-075-ai-database-schema` (dizin otoritesi: [[../index.md]] satır **99** — dosya adı ile birebir hizalı ✅; satır `[[../brain.md]]` önekli **kusurlu biçimdedir → `[[../brain.md]]` hedef düzeltmesi son sıfırlamaya ertelendi, bu işlemde rapor-only**)
> **İlgili kararlar:** [[ADR-074-radio-database-schema]] (format referansı + aynı seri şema ADR'si + dürüst etiket disiplini) · [[ADR-073-podcast-database-schema]] (aynı seri) · [[ADR-072-social-database-schema]] (aynı seri + bildirim/ENUM sınırı) · [[ADR-033-sql-normalization-strategy]] (BCNF kural seti + denetim PLANNED) · [[ADR-040-database-authority]] (18-DB sahiplik matrisi + 28 FK istisna defteri + `coremusic_ai` sahibi `music` `:165`) · [[ADR-041-database-normalization-supplementary]] (adlandırma/UUID/audit + denormalizasyon defteri) · [[ADR-003-multi-db-bcnf]] ("DB arası FK YOK" hükmü) · [[ADR-014-multi-db-migration-strategy]] (tek migration kapısı — tüm DDL buradan) · [[ADR-002-pdo-mandatory-no-orm]] (erişim katmanı) · [[ADR-039-7-service-platform-architecture]] (A4 veri domaini sahibi) · [[ADR-030-ai-strategy-core]] (AI stratejisi + PII + sessiz başarısızlık yasağı — sınır) · [[ADR-049-startup-prompt-loader]] (AIEngine stub bulgusu + bu ADR'de satır kayması raporu) · [[ADR-081-multi-provider-data-sync]] (outbox + WAL — sınır) · karar dizini [[../index.md]] **satır 99**.
> **⚠️ VERIFICATION REQUIRED:** `ADR-076`–`ADR-079` (dizin satırı `:100`–`:103` var, **dosya diskte YOK → düz metin + ⚠️, wiki-link KURULMAZ**) · `ADR-082` (ne dosya ne dizin satırı — `:104` = 081, `:105` = 083) · `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK) · `ADR-051/053/054/055/057/060` + `ADR-065`–`ADR-071` + `ADR-080` = **14 atlanan boşluk** (hem dosya hem dizin satırı YOK — §5.1/8 ve §7.1) · BCNF "Yes" beyanı (`coremusic_ai.sql:4`) **denetlenmemiştir** (ADR-040 `:38`) · başlık `deleted_at` (`:12`) ve `BINARY(16)` (`:11`) iddiaları **kullanım 0/6 ile çelişir** · yorum-FK'lar **tip olarak uyuşmaz** (INT UNSIGNED ↔ BINARY(16)) · AI tablo CRUD kullanan PHP/JS = **0** · AIEngine `return []` = **STUB** · TTL / freshness / retention **rakamları bu ADR'de yazılmadı** · ADR-049 AIEngine **satır aralıkları güncel değil** (kayması nedeni kesin değil ⚠️ — rapor-only).
> **Bölüm sınırı (kenetli):** ADR-074 **formatı + radio şeması + partition gerekçesi**, ADR-073 **podcast şemasını**, ADR-072 **sosyal şema/ENUM'u**, ADR-040 **DB sahipliğini + FK istisnasını**, ADR-033/041 **normalizasyon kural setini**, ADR-014 **migration kapısını**, ADR-030 **AI stratejisini (öneri modeli/PII/API politikası)**, ADR-049 **prompt loader'ı + stub etiketini**, ADR-081 **çoklu sağlayıcı senkronunu** yazdı; **`coremusic_ai` 6 tablosunun envanteri, ilişki/indeks/ML veri politikası (feature store · registry · TTL · tercih döngüsü) ve kod yüzeyi dürüst etiketi bu ADR'nindir** — hiçbiri yeniden yazılmaz. `ADR-076` (Video DB) **diskte YOK → düz metin + ⚠️**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 7'deki "yeni ADR ≥ 088" ile arşivin 075 slotu arasındaki **numara çakışması ADR-061/062/063/064/072/073/074 künyelerinden tekrar raporlanır, düzeltilmez**.

---

## 1. Bağlam (Context)

CoreMusic'in AI veri düzlemi (A4) diskte **çoktan yazılmış**, vault'ta **hiç kararlaştırılmamış** durumda: `.ai/.sql/mysql/coremusic_ai.sql` 188 satır / 11.417 bayt, v8.0.0, başlık `:7` "Tables: 6", footer `:187` "6 tablo tamamlandı" — altı tablo da yerinde. Buna karşılık hiçbir ADR bu şemayı karar olarak yazmıyor: ADR-040 onu sahiplik matrisinde "6 tablo / sahibi `music` / ATAMA (LLM kodu 0)" diye geçiyor (`:165`), ADR-033 denetlenmemiş BCNF iddialarının arasında sayıyor, ADR-030 AI stratejisini (API-birincil, PII, human-in-the-loop) yazıyor ama şemayı değil, ADR-049 ise `AIEngine` stub'unu bulup prompt loader kararını veriyor — veri politikası bırakıyor. Aynı anda kod yüzeyinde AI tablo sorgulaması **0** (PHP tablo adı taraması = 0, JS/TS = 0) — yani şema IMPLEMENTED, kod PLANNED; bu ayrımı da kimse yazmadı. Dört boşluk birden büyüyor: (1) **ML veri politikası yazılmamış** — feature store'un çift katmanı (offline batch ↔ online serving) hangi tazelik (staleness) eşiğiyle yaşayacak, `model_versions` hangi değişmezlik/stage kurallarına bağlanacak, `recommendation_history` ne kadar tutulacak (TTL), kullanıcı tercihi nasıl oluşup silinecek — dördü de boş; (2) **çift-tablo örtüşmesi sahipsiz**: `coremusic_ai.audio_features` `:109` ↔ `coremusic_musics.music_audio_features` `:344` aynı ölçütleri (danceability/energy/valence/…/bpm) iki farklı PK tipiyle (INT UNSIGNED ↔ BINARY(16)) tutuyor, `user_preference_profiles` `:31` ↔ `coremusic_user.user_preferences` `:57` iki "tercih" tablosu aynı-domain görünüyor; (3) **başlık iddiaları kullanımla çelişiyor**: `:11` "UUID: BINARY(16)" ↔ 6/6 tablo `INT UNSIGNED AUTO_INCREMENT`, `:12` "Soft Delete: is_deleted + deleted_at" ↔ `deleted_at` 0/6 ve `is_deleted` 3/6; (4) **yorum-FK'lar asla uygulanamaz**: `user_id`/`music_id` `INT UNSIGNED` iken hedef `users.id`/`musics.id` `BINARY(16)` — tip uyuşmazlığı, kimse yazmadı. Bu ADR beş işi tek kayıtta kapatır: **(1)** 6 tabloyu envanterler, **(2)** ilişki/indeks envanterini yazar (FK 0 — ADR-003 uyumu + tip bulgusu), **(3)** ML veri politikasının dört başlığını yazar (feature store · registry · TTL · tercih döngüsü), **(4)** IMPLEMENTED/PLANNED etiketini koyar + yedi drift'i raporlar, **(5)** sınırı kenetler (ADR-030/049/072/073/074/081).

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-01 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | ADR-075 slotu kayıtlı mı? | [[../index.md]] `:99` → `\| [[../brain.md]] ADR-075-ai-database-schema \| AI DB Schema \| Database \|` · [[../../index.md]] `:700` · [[../../brain.md]] `:1016` · [[../../keys.md]] `:292` | ✅ **KAYITLI** (4 indeks satırı; `:99` önek kusurlu → §5.1/10) |
| 2 | Bu işlem öncesi dosya var mıydı? | `accepted/` dizin listesi — `ADR-075*.md` **yok** (ADR-074 var) | ❌ **YOKTU** → bu işlemde yazılıyor |
| 3 | **AI SQL dosyası var mı?** | `.ai/.sql/mysql/coremusic_ai.sql` = **188 satır / 11.417 bayt**, v8.0.0 (Date `:6` = 2026-08-10), başlık `:7` "Tables: 6", footer `:187` "6 tablo tamamlandı" | ✅ **AYRI DOSYA VAR** (radyonun aksine — 19 dosyalık klasörde `coremusic_ai.sql`) |
| 4 | Tablo envanteri | `user_preference_profiles` `:31` · `listening_features` `:55` · `recommendation_history` `:80` · `audio_features` `:109` · `model_versions` `:139` · `training_jobs` `:166` — başlık `:7` ile **6/6 birebir** | ✅ **IMPLEMENTED** (şema) |
| 5 | FK envanteri | `ADD CONSTRAINT`/`FOREIGN KEY` = **0 isabet** · 7 yorum-FK (`:48`, `:72-73`, `:101-102`, `:132`, `:183`) + başlık 3 bağımlılık (`:14-17`) — **model_versions dahil iç FK bile 0** | ⚠️ deklarasyon **0** · ADR-003/040'a uyumlu (istisna defterinde AI satırı YOK — ADR-040 `:49`) |
| 6 | **Tip uyuşmazlığı (yeni bulgu)** | `user_id`/`music_id` = `INT UNSIGNED` (`:33`, `:57-58`, `:82-83`, `:111`) ↔ hedef `users.id`/`musics.id` = `BINARY(16)` (`coremusic_auth.sql` users · `coremusic_musics.sql:88`) | ⚠️ yorum-FK **tip olarak enforce edilemez** — gerçek FK denemesi migration patlatır (R4) |
| 7 | İndeks + CHECK envanteri | **16 girdi** = uq 4 (`:45`, `:68`, `:127`, `:154`) + idx 12 (`:69`, `:92-96`, `:128-129`, `:155-156`, `:178-179`) · CHECK **5** (`:97` algorithm · `:98` feedback · `:157` model_type · `:158` status · `:180` status) | ✅ IMPLEMENTED · PK-only tablo **0** |
| 8 | BCNF beyanı | başlık `:4` "BCNF: Yes" + tablo içi `:29, :53, :78, :107, :137, :164` "Normal Form: BCNF" · ADR-040 `:38` "iddiadır, denetim DEĞİL → PLANNED" | ⚠️ **denetlenmemiş beyan** |
| 9 | Audit/soft-delete alanları | created+updated **2/6** (upp `:41-43`, lf `:64-66`) · created_at 5/6 (**audio_features 0** — yalnız `analyzed_at` `:125`) · updated_at 2/6 · is_deleted 3/6 (`:89`, `:151`, `:175`) · **deleted_at 0/6** (başlık `:12` iddiasıyla çelişir) | ⚠️ ADR-033 kural 5 eksik (4 tablo) + iddia-kullanım boşluğu |
| 10 | Kimlik tipi | başlık `:11` "UUID: BINARY(16)" ↔ PK **6/6 `INT UNSIGNED AUTO_INCREMENT`** · ADR-041 standardı `BINARY(16)` UUIDv7 | ⚠️ **iddia-kullanım çelişkisi**; 6 tablo grandfathered → yeni tabloda UUID (§5.1/7) |
| 11 | **Kod yüzeyi (tablo CRUD)** | tablo adı PHP = **0** · JS/TS = **0** · geniş kalıp (`recommendation|ai_|user_preference|…`) yalnız AI-katmanı sınıfları (AIEngine, AIWorkflow, PromptEngine, AIOrchestrator) + `ThemeManager.php:66` yorumu | ⏳ şema IMPLEMENTED / **kod PLANNED** |
| 12 | AIEngine stub durumu | `return []` `:212` (getCollaborativeScores null), `:217` (gövde), `:234` (getContentBasedScores) · `analyzeHardware` placeholder `:149-155` · `extractFeatures` sabit `:319-326` (bpm 120.0, key 'C') · `predictFault` `:161-202` eşik mantığı gerçek | ⚠️ **STUB** — öneri veri gövdesi boş (ADR-030 sessiz-fail riski R1) |
| 13 | ADR-049 satır kayması | ADR-049 alıntısı `getCollaborativeScores :216-225` / `getContentBasedScores :230-242` / `analyzeHardware :145-163` / `extractFeatures :322-334` ↔ güncel `:209-218` / `:223-235` / `:138-156` / `:315-327` (son commit `c208092` AI güncellemeleri içerir) | ⚠️ **satır kayması** — neden kesin değil; rapor-only (§5.1/5d) |
| 14 | Komşu/çift tablolar | `coremusic_user.sql:57` user_preferences (BINARY(16) `:58`, `autoplay_recommendations` `:67`, gerçek FK `:83`) + `:90` user_listening_history · `coremusic_musics.sql:344` music_audio_features (BINARY(16), unique `:364`, gerçek FK `:367`) ↔ `audio_features` `:109` | ⚠️ **örtüşme riski** — rol dağıtımı yok (R2, §5.1/6) |
| 15 | Doküman yüzeyi | `k8-servis/ai-service.md:28-65` = 4 endpoint tablosu (analyze · recommendations · voice · learn) + `:45` `GET /api/v1/ai/recommendations/radio` · `k4-yapay-zeka/` = **14 dosya** (recommendation-engine.md, ml-infrastructure.md) | ✅ IMPLEMENTED (doküman) / ⏳ kod PLANNED |
| 16 | ENUM/çapraz referanslar | `coremusic_user.sql:173` kuyruk `source ENUM('user','ai','radio','podcast')` + `:174` `source_id` (yorum — constraint yok) · ADR-040 `:165` sahiplik/atama satırı | ✅ `ai` yalnız **ENUM değeri** + atama — FK değil |
| 17 | Partition teknik farkı (radyo ile karşılaştırma) | dosyada gerçek FK constraint **0** → MySQL §26.6 "partition'lı InnoDB'de FK desteklenmez" yasağı bu şemada **tetiklenmez** (ADR-074'ün 2 FK'lı radyosundan farklı); ancak 6 PK da yalnız `(id)` → partition kolonu unique key'e eklenmeli | ✅ teknik engel yok · DDL = PK değişikliği → ADR-014 + yeni ADR (§2.2/d-D3) |
| 18 | DB envanteri | `.sql/mysql/` = **19 dosya** (`coremusic_ai.sql` dahil, 2026-10-01 glob) · ADR-040 `:33` = **18** dosya (`coremusic_ai` → `coremusic_wireless` aralığı) | ⚠️ **envanter drift'i** (ADR-073/074 ile aynı — rapor) |
| 19 | Referans formatı | [[../../.templates/adr/adr-template.md]] (Guardrail #16) · format referansı [[ADR-074-radio-database-schema]] | ✅ okundu |

> **Yol notu:** görev metnindeki `.ai/templates/adr/adr-template.md` yolu **bulunamadı**; gerçek şablon `**.ai/.templates/**adr/adr-template.md` (nokta-önekli `.templates`). Düzeltme rapor-only (ADR-073/074 §5.1 ile aynı).

### 1.2 Sorun Tanımı

1. **Şema var, karar yok.** Altı tablo diskte, dosya "6 tablo" diye sayıyor, ama hangi grubun ne iş yaşadığı, hangi bağımlılığın neden yalnız yorum olarak durduğu ve hangi ML politikasının bağlayıcı olduğu hiçbir ADR'de yazılmıyor → yeni geliştirici "AI tabloları nerede, model sürümü nasıl değiştirilir, öneri geçmişi ne kadar tutulur, tercih nasıl silinir?" sorularına vault'tan cevap bulamıyor.
2. **IMPLEMENTED/PLANNED ayrımı yok.** Şema + 2 doküman yüzeyi var, kod CRUD 0 ve skor gövdesi `return []` → "AI hazır" yanılgısı doğabilir; `ai-service.md` 26 endpoint tablosu ile bu yanılgıyı besliyor (ADR-040 `:165` zaten "ATAMA (LLM kodu 0)" diyor).
3. **ML veri politikası yazılmamış.** Dört başlık da boş: (i) feature store çift katmanında offline = MySQL, online = hangi teknoloji/hız eşiği? tazelik (staleness) süresi ne? (ii) `model_versions` yayınlanmış sürüm değiştirilebilir mi, stage geçişleri nasıl? (iii) `recommendation_history` ne kadar tutulur, ne zaman partition'a geçilir? (iv) kullanıcı tercihi nasıl oluşur/tazelenir/silinir (GDPR)? Hiçbiri yazılmadı.
4. **Çift-tablo örtüşmesi sahipsiz.** İki "ses özelliği" tablosu (farklı PK tipleri, kısmen farklı ölçütler) ve iki "tercih" tablosu (ayar SSOT ↔ AI türevi) yan yana duruyor; rol dağıtılmazsa ilk geliştirici hangisini yazacağını bilemez (SSOT ihlali riski R2).
5. **Başlık iddiaları kullanımla çelişiyor.** `deleted_at` (`:12`) 0/6, `BINARY(16)` (`:11`) 0/6 → soft-delete ve UUID standartları iddia düzeyinde kalıyor; ADR-033 kural 5 (audit) 4 tabloda eksik. Ayrıca yorum-FK tip uyuşmazlığı (INT UNSIGNED ↔ BINARY(16)) — "yorumda yazıyor → bir gün ekleriz" planı **fiziksel olarak imkânsız**, kimse yazmadı.
6. **Sınır yazılı değil.** ADR-030 (strateji/PII), ADR-049 (stub + prompt loader), ADR-072/073/074 (aynı seri şemalar), ADR-081 (sync) bu şemaya dokunabilir; sahiplik söylenmezse altısı da ML politikasını veya tabloları yeniden yazma riski taşır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "feature store online offline store separation staleness freshness best practice" · (2) "recommendation system database schema user interaction history feedback table design best practice" · (3) "model registry metadata immutability version stages promotion rollback best practice" · (4) "database schema design audit columns soft delete created_at updated_at index checklist best practice" · (5) "machine learning data privacy retention lifecycle user preference deletion policy" |
| Web Search **Konusu** | **(1)** çift katmanlı feature store (offline batch ↔ online serving) ve bayat özellik (stale feature) sorunu · **(2)** öneri sistemi etkileşim/geri bildirim veri modeli + kullanıcı geçmiş tablosu · **(3)** model registry metadata'sı, yayınlanmış sürüm değişmezliği ve stage/promotion/rollback · **(4)** şema denetim kuralları: audit kolonları, soft-delete, indeks kontrol listesi · **(5)** ML veri gizliliği, saklama süresi (retention) ve kullanıcı tercih verisi yaşam döngüsü. |
| Web Search **Bağlam** | CoreMusic: `coremusic_ai` 6 tablosu şema IMPLEMENTED / kod PLANNED (CRUD 0 + AIEngine STUB); vault'ta feature-store, registry, TTL ve tercih-silme politikasının **hiçbiri yazılmamış**; iki örtüşen tablo (ses özellikleri, tercihler) sahipsiz; başlık iddiaları (`deleted_at`, `BINARY(16)`) 0/6 ile çelişiyor. Araştırma bu dört politika boşluğu + denetim kurallarını hedefliyor — tablo sayısı/satır numaraları **iç karar** olduğu için web yalnız *yapı, kural ve yaşam döngüsü* için okundu (ADR-033'ün sorgularının tekrarı değil). Araştırma 2026-10-01'de yapıldı; **5 sorgu / 23 benzersiz kaynak**. |
| Web Search **Kısa Açıklama** | **(1)** Online/offline ayrımı endüstri standardıdır; tazelik (staleness) servis kalitesini belirler — CoreMusic'te offline = MySQL (IMPLEMENTED), online = ölçüm kapısı (PLANNED), eşik rakamı yok. **(2)** Etkileşim tablosu (algoritma + geri bildirim + konum + zaman) öneri değerlendirmesinin verisidir; `recommendation_history` bu kalıba uyar → şema korunur. **(3)** Registry'de yayınlanmış sürüm değiştirilmez; stage geçişleri metadata ile izlenir, rollback = eski sürümü yeniden aktifleştirmek — `model_versions` uq + 4-değerli status CHECK bu kalıba birebir uyar. **(4)** Audit kolonları + soft-delete + birleşik unique, şema denetiminin çekirdeğidir; CoreMusic'te 4/6 tabloda eksik → defter. **(5)** Kişisel/tercih verisi için saklama süresi ve silme hakkı yazılı olmalıdır; süre rakamları CoreMusic'te yok → ⚠️ kapı. |
| Web Search **Uzun Açıklama** | **(i)** Feature store literatürü (Databricks, Hopsworks, Aerospike, Qwak, IBM) aynı ayrımı verir: offline store batch eğitimi besler, online store düşük gecikmeli servis eder; tazelik ihlali (stale features) model kalitesini sessizce bozar — bu yüzden tazelik alanı zorunlu, CoreMusic'te alanlar var (`last_analyzed_at` `:40`, `last_played_at` `:63`, `analyzed_at` `:125`) ama **eşik yok**; BigID/Trendyol kaynakları feature tarafının da veri yönetişimi (sahiplik, PII, saklama) konusuna girdiğini gösterir → tercih/profil JSON'ları (`genre_weights` `:34`) kişiseldir, ADR-030 PII sınırına bağlanır. **(ii)** Öneri veri modeli (Recombee, systemdesignhandbook, GeeksforGeeks, StackOverflow) üç katmanı şart koşar: öğe özellikleri (parça/doğa), kullanıcı etkileşimi (izleme/atla/geri bildirim) ve önerinin kendisi (algoritma, sürüm, konum, güven) — CoreMusic üçünü de tutar (`audio_features`, `listening_features`, `recommendation_history`: `algorithm` `:84`, `model_version` `:85`, `confidence` `:86`, `feedback` `:87`, `position` `:88`); "geçmiş tablosu nasıl tasarlanır" yanıtı (StackOverflow) append-only + zamana göre indekslemedir → `idx_rh_created (created_at DESC)` `:96` zaten vardır. **(iii)** Model registry kaynakları (MLflow, Snowflake, Atlan, mlinproduction, JFrog) ortak kural verir: (a) yayınlanmış `(name, version)` **değişmez** — düzeltme yeni sürümdür, (b) stage'ler (Staging/Production/Archived benzeri) metadata ile geçilir ve **tek yönlü** ilerler, (c) rollback = önceki sürümü tekrar aktifleştirmek, (d) eğitim metrikleri (accuracy/f1/veri boyutu/süre) sürümün yanında yaşar; CoreMusic'te bu dördünün karşılığı hazır: `uq_mv_pair` `:154`, status CHECK `:158` (`training/active/deprecated/archived`), `deployed_at` `:150`, metrikler `:145-148`. **(iv)** Şema denetim rehberleri (Exasol, Redgate, Medium, devart) `created_at`/`updated_at` zorunluluğunu, soft-delete için tek kolon sözleşmesini ve "her tablo unique/inceleme indeksi taşır" kuralını şart koşar; CoreMusic ölçümünde bu kural 4/6 tabloda eksik, `deleted_at` 0/6 → denetim PLANNED (ADR-033) kapsamında kayıt. **(v)** Gizlilik/retention kaynakları (NYU JIPEL, Termly, Ultralytics) ML yaşam döngüsünde saklama süresi + silme/deletion-rights gerektiğini, sürelerin ürün-politikası olarak **açıkça yazılmasını** talep eder → CoreMusic'te süre yok → kapı; mekanik olarak da dikkat: AI profili **farklı DB'de** olduğu için `user_preferences` FK CASCADE'i (`:83`) ona ulaşamaz → hesap silme akışının `coremusic_ai`'ı da temizlemesi gerekir (R7). |
| Web Search **Paragraf Veri Uzun** | **5 sorgu / 23 benzersiz kaynak**: **(1)** Databricks feature store (online/offline) · Aerospike feature staleness/freshness · Hopsworks feature view · Qwak staleness · JFrog feature store · Trendyol stale-features (blog) · IBM what-is-feature-store · BigID feature-store governance (8). **(2)** Recombee recommender systems survey · systemdesignhandbook recommendations system · GeeksforGeeks recommendation system · StackOverflow user-history table design practice (4). **(3)** MLflow model registry (stages) · Snowflake model registry docs · Atlan model registry · mlinproduction model registry practices · JFrog model registry (JFrog tekrar → benzersiz +4). **(4)** Exasol database design principles · Redgate schema change/audit · Medium practical schema design · devart soft-delete/FK conventions (4). **(5)** NYU JIPEL LLM lifecycle privacy · Termly data retention · Ultralytics data lifecycle/retention (3). |
| Web Search **Sonucu** | **(1) Karar destekleniyor + boşluk doğrulandı:** çift katman endüstri standardı → offline = MySQL IMPLEMENTED, online = **ölçüm kapısı PLANNED**; tazelik alanları var ama eşik yok → ⚠️. **(2) Karar destekleniyor:** `recommendation_history` üç-katmanlı etkileşim kalıbına uyar → şema **korunur, yeniden modellenmez**. **(3) Karar destekleniyor:** uq + 4-stage CHECK + metrik kolonları registry kalıbını karşılar → değişmezlik/stage/rollback politikası bu ADR'ye yazılır. **(4) Karar destekleniyor + eksik doğrulandı:** audit/soft-delete 4/6 eksik, `deleted_at`/UUID iddiaları 0/6 → defter kaydı (düzeltilmedi). **(5) Karar destekleniyor (boşluk doğrulandı):** retention/silme politikası vault'ta yok → kapı; CASCADE'in AI profiline ulaşamadığı mekanik sınır yazılır. **İtiraz/karşıt bulgu:** dış kaynaklar CoreMusic'in TTL süresini, freshness eşiğini, retention süresini ve tablo sahipliğini doğrulayamaz (iç karar) → o rakamlar ⚠️. |
| Web Search **Alınan Karar** | **(a)** 6 tablo şeması korunur, yeniden modellenmez (envanter §2.2/a). **(b)** İlişki: 0 FK deklarasyonu + 7 yorum-FK **korunur**; çapraz-DB FK eklenmez (ADR-003/040); tip uyuşmazlığı bulgusu yazılır → gerçek FK denemesi yasak (R4). **(c)** Feature store: offline = MySQL iki tablo (IMPLEMENTED), online serving store = **aşama 2 ölçüm kapısı** (bu ADR teknoloji seçmez); freshness eşiği sayılmadı ⚠️. **(d)** Registry: yayınlanmış `(model_name, version)` **değişmez**; `status` tek yönlü (training→active→deprecated→archived); rollback = eski sürümü yeniden active; silme = `is_deleted` soft. **(e)** Öneri TTL: `recommendation_history` = büyüyen tek tablo → saklama süresi **sayılmadı** ⚠️ debate; büyüme ölçülünce `api_calls` kalıbı partition (PK `(id, created_at)` + RANGE/`TO_DAYS` + MAXVALUE + `DROP PARTITION`) → **yeni ADR + ADR-014**. **(f)** Tercih yaşam döngüsü: `user_preferences` (coremusic_user) = **ayar SSOT**, `user_preference_profiles` = **AI türevi profil**; 6 aşama (oluşum→tazeleme→geri besleme→bayatlık→silme→kullanıcı ayarı) yazılır; `deleted_at` eksikliği + CASCADE sınırı deftere/kapıya. **(g)** Sınır = ADR-030 · ADR-049 · ADR-072 · ADR-073 · ADR-074 · ADR-081 — tekrar yok. |
| Web Search **Sonuç** | **5/5 araştırmada karar destekleniyor** (2 yapı + 3 politika): çift katman feature store (1) · etkileşim veri modeli (2) · registry değişmezlik/stage (3) · audit/soft-delete kuralı (4) · retention/silme yaşam döngüsü (5). **Dört gerilim açıkça kabul edildi:** (1) TTL / freshness / retention **rakamları bu ADR'de yazılmadı** → ⚠️ debate şartları (§5.1/11-12); (2) online feature store **teknolojisi seçilmedi** → ölçüm kapısı; (3) audit eksikliği (4 tablo) + `deleted_at`/UUID iddiaları **düzeltilmedi** → rapor-only; (4) çift-tablo örtüşmeleri **birleştirilmedi** → yeni ADR kapısı. **⚠️ VERIFICATION REQUIRED:** öneri TTL süresi · retention süresi · freshness eşiği · online store eşikleri · `ADR-076`–`ADR-080`/`ADR-082`–`ADR-088` dosyaları. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar (ADR-002 / ADR-003 / ADR-014 dahil) |
| ADR-040 sınırı | 18-DB sahiplik matrisi + 28 FK istisna defteri bağlayıcıdır; `coremusic_ai` sahibi `music` (`:165`), **defterde AI çapraz FK satırı yoktur** → yenisi eklenemez; dosya sayısı iddiası (`:33` = 18) ile disk (19) arasındaki drift rapor-only |
| ADR-033 / ADR-041 sınırı | Normalizasyon kural seti bağlayıcıdır; bu ADR kural **üretmez**, şemaya uygular ve açığı defterine yazar (audit 4/6, UUID grandfathered, sayaç/JSON denormalizasyonu) |
| ADR-003 sınırı | "DB arası FK YOK" hükmü; `coremusic_ai`'ta 0 deklarasyon zaten uyumlu → gerçek FK **eklenemez** (tip uyuşmazlığı ayrıca imkânsız kılar) |
| ADR-072 / ADR-073 / ADR-074 sınırı | Sosyal ENUM/notifications, podcast şeması, radio şeması + partition gerekçesi bu ADR'de **yeniden yazılmaz**; format/dil ADR-074'ten alınır, içerik tekrarı yok |
| ADR-030 / ADR-049 sınırı | AI stratejisi (öneri modeli, PII, API-birincil, sessiz başarısızlık yasağı) ve prompt loader + stub etiketi bu ADR'de tekrarlanmaz; yalnız veri şeması + ML politikası yazılır |
| ADR-081 sınırı | Outbox + WAL senkronu kapsam dışı; bu ADR senkron akışı önermez |
| Migration tek kapısı | Her şema değişikliği yalnız ADR-014 özel PHP runner'ından geçer; bu ADR elle DDL yazmaz (partition dahil — PK değişikliği bile) |
| Kod kanıtı | AI tablo CRUD PHP/JS = **0** → "AI/öneri çalışıyor / özellik hazır" iddiası yazılmaz; AIEngine `return []` = STUB |
| Vault'a yazma yetkisi | Bu işlem yalnız bu ADR dosyasını + `log.md` append'ini yazar; `index.md`/`brain.md`/`keys.md`/`ADR-040`/`ADR-049` düzeltmeleri **sonraki vault reset'ine ertelenir** (In-Place Refactoring + rapor-only) |
| Numara çakışması | Kural 7 "yeni ADR ≥ 088" ↔ arşiv 075 slotu — ADR-061/062/063/064/072/073/074 künyelerinden **aynı çakışma tekrar raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

CoreMusic'in AI veri şeması **bu ADR'de tek kayıtta** sabitlenir: **(a)** **6 tablo** (`user_preference_profiles` · `listening_features` · `recommendation_history` · `audio_features` · `model_versions` · `training_jobs`) `.ai/.sql/mysql/coremusic_ai.sql` içindedir — BCNF + ADR-033/040/041 + ADR-072/073/074 **aynı ortak kural seti** uygulanır, tablo/kolon adları değişmez; **(b)** **SQL envanteri** yazılır (dosya: 19 dosyalık klasörde `coremusic_ai.sql` VAR; tablo 6, FK deklarasyonu 0 — 7 yorum-FK + 3 başlık bağımlılığı, tip uyuşmazlığı bulgusuyla; indeks 16, CHECK 5); **(c)** **ML veri politikası 4 başlık**: D1 feature store çift katmanı (offline IMPLEMENTED / online + tazelik eşiği ölçüm kapısı), D2 model registry (değişmezlik + tek yönlü stage + rollback), D3 öneri TTL (`recommendation_history` süre ⚠️ + `api_calls` kalıbı partition kapısı), D4 tercih yaşam döngüsü (6 aşama + iki tablo rolü + silme açığı); **(d)** **dürüst etiket**: şema + doküman IMPLEMENTED, kod PLANNED, AIEngine STUB, BCNF denetlenmemiş, başlık iddiaları 0/6 çelişkili, yedi drift rapor-only; **(e)** **sınır** = ADR-030 · ADR-049 · ADR-072 · ADR-073 · ADR-074 · ADR-081 (+ ADR-002/003/014/033/039/040/041) — tekrar yok. Karar kod üretmez, tablo açmaz, indeks/FK eklemez.

### 2.1 Neden Bu Seçenek?

Sorun *eksik şema değil, eksik karar*: altı tablo zaten diskte ve dosyanın kendi başlığı/footer'ı sayıyor. Bu ADR sıfırdan AI DB çizmek yerine mevcut şemayı **envanterler, ML politikasını yazar ve dürüst etiketler**: (1) SSOT korunur — dosya adı, tablo adları ve kolon adları değişmez (In-Place Refactoring), (2) "FK neden yok?" sorusunun cevabı (**deklare edilmemiş + tip uyuşmazısı + ADR-003 yasağı**) tek yerde bağlanır, kimse "FK ekleyelim" diye kırık DDL yazmaz, (3) "ML veri politikası" dört başlıkta ilk kez yazılırsa ilk geliştirici model sürümünü overwrite etmeyi, öneriyi sonsuz tutmayı veya hesabı silerken AI profilini unutmayı denemez, (4) radyo ADR'sinden farklı olarak **partition teknik engeli burada yok** (FK 0 → §26.6 tetiklenmez) — ama doğru sıra PK değişikliği + ölçüm + yeni ADR'dir, bu üçü birlikte yazılır, (5) çift-tablo örtüşmeleri ve başlık iddiaları görünür kılınır, sessizce yutulmaz, (6) diskte olmayan `ADR-076`–`ADR-080`'e wiki-link **kurulmaz** — uydurma hedef linklemek yerine düz metin + ⚠️ yazılır.

### 2.2 Teknik Detaylar

**(a) Tablo envanteri — 6 tablo (şema korunur, yeniden modellenmez):**

| # | Tablo | Dosya satırı | Aday anahtar / unique | İndeks | CHECK | Amaç | Etiket |
|---|-------|--------------|------------------------|--------|-------|------|--------|
| 1 | `user_preference_profiles` | `:31` | `uq_upp_user (user_id)` `:45` | — | — | AI tercih profili: `genre_weights JSON` `:34` · `mood_preferences JSON` `:35` · tempo/energy/acoustic/diversity `:36-39` · `last_analyzed_at` `:40` · audit **tam** (`created/updated` `:41-43`), `is_deleted`/`deleted_at` **YOK** | ✅ IMPLEMENTED (şema) |
| 2 | `listening_features` | `:55` | `uq_lf_pair (user_id, music_id)` `:68` | `idx_lf_music` `:69` | — | Kullanıcı×parça etkileşim özeti: `features JSON` `:59` · `skip_rate` `:60` · `listen_ratio` `:61` · `repeat_count` `:62` · `last_played_at` `:63` · audit tam (`:64-66`), soft-delete YOK | ✅ şema |
| 3 | `recommendation_history` | `:80` | — (PK + 5 indeks) | `idx_rh_user` `:92` · `idx_rh_music` `:93` · `idx_rh_algorithm` `:94` · `idx_rh_feedback` `:95` · `idx_rh_created` `:96` (DESC) | `:97` algorithm · `:98` feedback | Öneri etkileşim kaydı: `algorithm` `:84` (collaborative/content_based/hybrid/ai_curated) · `model_version` `:85` · `confidence` `:86` · `feedback` `:87` · `position` `:88` · `is_deleted` `:89` · `created_at` `:90` (**updated_at YOK**) | ✅ şema / ⚠️ audit kısmi · **büyüyen tablo (D3)** |
| 4 | `audio_features` | `:109` | `uq_af_music (music_id)` `:127` | `idx_af_bpm` `:128` · `idx_af_key` `:129` | — | Parça ses analizi: bpm/key/mode/time_signature `:112-115` · 7 ölçüt `:116-122` · `loudness_db` `:123` · `features JSON` `:124` · `analyzed_at` `:125` — **created/updated/is_deleted 0** | ✅ şema / ⚠️ audit **0** · `coremusic_musics.music_audio_features` ile **örtüşme** |
| 5 | `model_versions` | `:139` | `uq_mv_pair (model_name, version)` `:154` | `idx_mv_type` `:155` · `idx_mv_status` `:156` | `:157` model_type · `:158` status | Model registry: `model_name`/`version` `:141-142` · `model_type` `:143` · metrikler `:145-148` · `status` `:149` (training/active/deprecated/archived) · `deployed_at` `:150` · `is_deleted` `:151` · `created_at` `:152` | ✅ şema · **D2 registry çekirdeği** |
| 6 | `training_jobs` | `:166` | — (PK + 2 indeks) | `idx_tj_model` `:178` · `idx_tj_status` `:179` | `:180` status | Eğitim işi: `model_version_id` `:168` (yorum-FK `:183` — **deklare edilmedi**) · `status` `:169` (queued/running/completed/failed/cancelled) · `started/completed` `:170-171` · `error_message` `:172` · `metrics JSON` `:173` · `gpu_hours` `:174` · `is_deleted` `:175` | ✅ şema / ⚠️ iç FK bile 0 |

- **Toplam 6** — başlık `:7` "Tables: 6" + footer `:187` "6 tablo tamamlandı" ile **6/6 birebir**.
- **İsimlendirme (ADR-041 §2.2-a ile hizalı):** tutarlı `snake_case` ✅ · `idx_`/`uq_` önekleri ✅ · **sapma:** PK'lar `INT UNSIGNED AUTO_INCREMENT` ↔ ADR-041 "`BINARY(16)` UUIDv7" standardı → 6 tablo **grandfathered**, yeni tabloda UUID (§5.1/7); başlık `:11` iddiası ise **hiçbiriyle tutmuyor** (§1.1/10).
- **Audit açığı (ADR-033 kural 5):** tam 2/6 · kısmi 3/6 · yok 1/6 (`audio_features`) · `deleted_at` **0/6** → defter §5.1/5a.
- **Denormalizasyon defteri (ADR-041):** `genre_weights`/`metrics`/`features` JSON kolonları (5 adet: `:34`, `:35`, `:59`, `:124`, `:173`) — sorgu/güncelleme deseni kod olmadan doğrulanamaz → ölçüm kapısı §5.1/3.

**(b) İlişki politikası — 0 FK deklarasyonu + 7 yorum-FK (tip uyuşmazlığı dahil):**

| Yorum-FK / bağımlılık | Dosya satırı | Hedef (iddia) | Hedef gerçekte | Deklarasyon | Durum |
|-----------------------|-------------|---------------|----------------|-------------|-------|
| `user_id` (upp) | `:48` | `coremusic_auth.users(id)` | `users.id BINARY(16)` ↔ kolon `INT UNSIGNED` | **YOK** | ⚠️ **tip uyuşmaz** |
| `user_id` (lf) | `:72` | `coremusic_auth.users(id)` | aynı | **YOK** | ⚠️ tip uyuşmaz |
| `music_id` (lf) | `:73` | `coremusic_musics.musics(id)` | `musics.id BINARY(16)` ↔ `INT UNSIGNED` | **YOK** | ⚠️ tip uyuşmaz |
| `user_id` (rh) | `:101` | `coremusic_auth.users(id)` | aynı | **YOK** | ⚠️ tip uyuşmaz |
| `music_id` (rh) | `:102` | `coremusic_musics.musics(id)` | aynı | **YOK** | ⚠️ tip uyuşmaz |
| `music_id` (af) | `:132` | `coremusic_musics.musics(id)` | aynı | **YOK** | ⚠️ tip uyuşmaz |
| `model_version_id` (tj) | `:183` | `coremusic_ai.model_versions(id)` | **aynı DB** — `INT UNSIGNED` ↔ `INT UNSIGNED` ✅ tip uyumlu | **YOK** | ⚠️ **iç FK bile deklare edilmedi** |

- **Kural:** AI şeması **ADR-003'ün "DB arası FK YOK" hükmüne uyar** — ADR-040 `:49` 28 istisna defterinde (user 11 · social 13 · system 4) **AI satırı yoktur**, yenisi eklenemez (CI statik kapısı ADR-040'ın işidir).
- **Yeni bulgu (§1.1/6):** 6 çapraz bağımlılığın **hepsi tip olarak uyuşmaz** (INT UNSIGNED ↔ BINARY(16)) → yorum satırları "bir gün ekleriz" planı taşımaz; gerçek FK denemesi ADR-003 ihlali + tip hatası + migration patlaması üretir (R4). Yorum-FK'lar **dokunulmaz kalır**, düzeltme = yorumun kendisine not (reset işi, §5.1/5c).
- **Zincir etkisi yok:** CASCADE/RESTRICT tanımı imkânsız (constraint yok); kullanıcı/parça silimi uygulama seviyesinde kalır → tercih döngüsü D4/6 ve R7'ye bağlanır.
- **`source_id` gibi ENUM/alan çaprazları** (kuyruk `:173-174` = metin/UUID) constraint değildir → ADR-072/040 sınırı (§1.1/16).

**(c) İndeks politikası — mevcut 16, yeni indeks YOK (ölçüm şartı):**

*Mevcut (IMPLEMENTED):* toplam **16 girdi** (4 `uq_` + 12 `idx_`, satır listesi §1.1/7) + 5 CHECK. PK hariç indekssiz tablo **0** → ADR-033 kural 3 eşiği karşılanır.

| Sorgu kalıbı | Karşılık | Değerlendirme |
|--------------|----------|----------------|
| Tercih profili okuma `WHERE user_id=?` | `uq_upp_user` `:45` | ✅ |
| Etkileşim `WHERE user_id=? AND music_id=?` | `uq_lf_pair` `:68` **prefix** `(user_id, music_id)` | ✅ birleşik unique zaten prefix'i verir → ek indeks gereksiz |
| Parça etkileşimi `WHERE music_id=?` | `idx_lf_music` `:69` | ✅ |
| Öneri feed'i `WHERE user_id=?` + yeni→eski | `idx_rh_user` `:92` + `idx_rh_created` `:96` | ✅ bugün yeterli |
| Öneri analizi `WHERE algorithm=? / feedback=?` | `idx_rh_algorithm` `:94` · `idx_rh_feedback` `:95` | ✅ (offline eval için) |
| Parça özellikleri `WHERE music_id=?` | `uq_af_music` `:127` | ✅ |
| Registry listeleme `WHERE model_type=? / status=?` | `idx_mv_type` `:155` · `idx_mv_status` `:156` | ✅ |
| Eğitim işleri `WHERE model_version_id=? / status=?` | `idx_tj_model` `:178` · `idx_tj_status` `:179` | ✅ |
| JSON içi sorgu (`genre_weights`, `metrics`) | **indeks YOK** (MySQL multi-valued index opsiyonel) | ⏳ kod + ölçüm olmadan **eklenmez** |

- **Ölçüm şartı:** hiçbir indeks `EXPLAIN` teyidi olmadan "hızlandırdı" denemez (ADR-033 workload denetimi PLANNED ile aynı kapı) → §5.1/3.

**(d) ML veri politikası — 4 başlık (bu ADR'nin merkez kararı):**

**D1 — Feature store modeli (çift katman):**

| Katman / kural | Bugünkü karşılık | Durum | Politika |
|----------------|------------------|-------|----------|
| Offline (batch) feature store | `listening_features` `:55` (kullanıcı×parça: JSON + skip_rate/listen_ratio/repeat_count) + `audio_features` `:109` (parça: bpm/energy/…/loudness) | ✅ IMPLEMENTED (şema) | MySQL tek kaynak; tazeleme = analiz job'u (`analyzed_at` `:125`) + etkileşim (`last_played_at` `:63`, `updated_at` `:65-66`) |
| Online (serving) feature store | — (Redis/feature store **YOK**) | ⏳ PLANNED | Aşama 2'de **ölçümle** karar (hız/ölçek eşikleri sayılmadı → bu ADR teknoloji **SEÇMEZ**); bugün "düşük gecikme" iddiası yazılmaz |
| Tazelik (staleness) eşiği | alanlar var: `last_analyzed_at` `:40` · `last_played_at` `:63` · `analyzed_at` `:125` | ⚠️ **eşik YOK** | Bayatlık süresi rakamı bu ADR'de yazılmadı → debate şartı (§5.1/11); kaynak: §1.3/1 (stale features = sessiz kalite düşüşü) |
| Feature sahipliği (örtüşme) | `coremusic_ai.audio_features` ↔ `coremusic_musics.music_audio_features` `:344` (ölçüt kesişimi: danceability…speechiness, loudness, tempo↔bpm) | ⚠️ **örtüşme** | Birleştirme/rol dağıtımı **yeni ADR** (§5.1/6); bugün ikisi de dokunulmaz; PK tip farkı (INT ↔ BINARY(16)) ayrıca rapor |
| Kişisel feature (PII) | `genre_weights` `:34` · `mood_preferences` `:35` (JSON, kullanıcı kişi) | ✅ şema / ⚠️ politika | ADR-030 PII sınırı: silme/erişim bu ADR'de değil → D4/5 + kapı |

**D2 — Model metadata / registry (değişmezlik + stage + rollback):**

| Kural | Karşılık (şema) | Karar |
|-------|-----------------|-------|
| **Değişmezlik (immutability)** | `uq_mv_pair (model_name, version)` `:154` | Yayınlanmış çift **overwrite edilemez**; düzeltme = yeni `version` satırı; silme = `is_deleted` `:151` (soft) — fiziksel silim yasak |
| **Stage geçişi (tek yönlü)** | `status` CHECK `:158` = `training → active → deprecated → archived` | Geçiş yönü tek yönlü; `deployed_at` `:150` yalnız `active`'e geçerken yazılır; geriye dönüş (deprecated→active) **yalnız rollback** (aşağıda) |
| **Metadata zorunlu** | `accuracy_score`/`f1_score` `:145-146` · `training_data_size` `:147` · `training_duration_seconds` `:148` | Metriksiz sürüm `active` yapılamaz (iş kuralı kod fazında — bu ADR DDL üretmez) |
| **Eğitim izi** | `training_jobs` `:166` → `model_version_id` `:168` (yorum-FK `:183`, deklare **YOK**) · `metrics JSON` `:173` · `gpu_hours` `:174` · status CHECK `:180` | Her sürümün eğitim izi satır satır yaşar; başarısız iş (`failed`) sürümü otomatik silmez |
| **Rollback** | mevcut `model_versions` satırı | Rollback = eski sürümü yeniden `active` yapmak (**yeni satır değil**); eskiyi silmek R5 üretir |
| **Bilgi yazarı** | MLflow/Snowflake/Atlan/mlinproduction kalıbı (§1.3/3) | Politika bu ADR'de; **süre/ eşik rakamı yok** → kapı §5.1/12 |

**D3 — Öneri TTL / saklama politikası:**

| Yüzey | Veri karakteri | Büyüme | TTL / partition kararı | Gerekçe / engel |
|-------|----------------|--------|------------------------|-----------------|
| `recommendation_history` `:80` | **Append-only** etkileşim kaydı (`created_at` `:90`, `idx_rh_created DESC` `:96`) | **BÜYÜR** (kullanıcı × öneri × gün) | **Saklama süresi sayılmadı → ⚠️ debate**; büyüme ölçülünce **partition kapısı** | Dosyada gerçek FK **0** → MySQL §26.6 yasağı **tetiklenmez** (radyo 2 FK'dan **farklı**); ancak PK `(id)` → partition kolonu unique key'e eklenmeli = **PK `(id, created_at)` DDL'si** → `api_calls` kalıbı (`coremusic_api.sql:115,128-141`: RANGE/`TO_DAYS` + `p_future MAXVALUE` + `DROP PARTITION`) → **yeni ADR + ADR-014** |
| `listening_features` `:55` | Kullanıcı×parça çifti; `uq_lf_pair` `:68` ile **sınırlı** (üst sınır = kullanıcı × katalog) | sınırlı; kullanıcı artışıyla genişler | **TTL gerekmez** — yeni etkileşim `updated_at` ile **upsert** tazeler | şişme değil **bayatlık** sorunudur → D1 staleness eşiği (⚠️) |
| `user_preference_profiles` `:31` | Kullanıcı başına **1 satır** (`uq_upp_user` `:45`) | sınırlı (kullanıcı sayısı) | **TTL gerekmez**; bayatlık `last_analyzed_at` `:40` ile izlenir | periyodik yeniden analiz job'u (kod PLANNED) |
| `audio_features` `:109` | Parça başına 1 satır (`uq_af_music` `:127`) | katalog boyutu | **TTL gerekmez**; yeniden analiz upsert (`analyzed_at` tazeler) | katalogdan çıkan parça → uygulama temizliği (kapı §5.1/6) |
| `model_versions` / `training_jobs` | Kayıt + iz | modele göre | **TTL yasak** (registry = denetim izi); `is_deleted` soft | D2 değişmezlik |

- **TTL süresi (hangi gün/ay silinir) bu ADR'de sayılmadı** → ⚠️ debate kapısı (ADR-072/073/074 ile aynı disiplin).
- Bu ADR **hiçbir DDL içermez**; PK/parition değişikliği bile ADR-014 tek kapısından yürür.

**D4 — Kullanıcı tercih yaşam döngüsü (detay):**

**İki tablo, iki rol (çakışma değil sınır):**

| Tablo | DB | Kimlik | Rol | Sahiplik |
|-------|----|--------|-----|----------|
| `user_preferences` `:57` | `coremusic_user` | `BINARY(16)` UUIDv7 `:58` + **gerçek FK** `:83` (`ON DELETE CASCADE`) | **Kullanıcı ayarı SSOT**: cihaz/tema/kalite/crossfade/explicit + `autoplay_recommendations` `:67` ("Auto-play AI recommendations" — AI'ya açılan tek bayrak) | kullanıcı ayarı → ADR-003/040 (mevcut sahiplik) |
| `user_preference_profiles` `:31` | `coremusic_ai` | `INT UNSIGNED` (grandfathered) + `uq_upp_user` `:45` | **AI türevi profil**: `genre_weights`/`mood_preferences` JSON + skorlar + `last_analyzed_at` — türetilmiş, ayar değil | AI veri şeması → **bu ADR** |

**Yaşam döngüsü — 6 aşama:**

| # | Aşama | Tetik | Kayıt / alan | Durum |
|---|-------|-------|--------------|-------|
| 1 | **Oluşum** | İlk öneri/analiz işi | `user_preference_profiles` INSERT (`uq_upp_user` ile tek satır) | ⏳ PLANNED (kod 0) |
| 2 | **Tazeleme** | Periyodik analiz job'u | JSON/skor güncelle + `last_analyzed_at` `:40` + `updated_at` `:43` | ⏳ PLANNED |
| 3 | **Geri besleme** | Öneri akışı + kullanıcı tepkisi | `recommendation_history.feedback` CHECK `:98` (`none/liked/disliked/skipped/saved`) → sonraki analize girer | ⏳ PLANNED |
| 4 | **Bayatlık** | `last_analyzed_at` eski | Yeniden analiz; **eşik rakamı YOK** | ⚠️ debate (§5.1/11) |
| 5 | **Silme (hesap/erase)** | Kullanıcı silme akışı | Beklenen soft-delete: profile'da `is_deleted` **YOK**, `deleted_at` dosyada **0/6** ↔ başlık `:12` iddiası → **açık** | ⚠️ eksik → defter §5.1/5a + kapı §5.1/7 |
| 6 | **Kullanıcı ayarı silmesi** | `user_preferences` satırı silinir | FK `:83` CASCADE yalnız `coremusic_user` içinde; **AI profili farklı DB'de → cascade EDAMEZ** → hesap silme akışının `coremusic_ai` profilini de silmesi zorunlu | ⚠️ R7 + ADR-030 PII kapısı |

- **Kural:** `user_preferences` = ne zaman **ayar**, `user_preference_profiles` = ne zaman **türev analiz**; birleştirilmez (farklı DB otoritesi), ikisi de `ai`'a veri **aktarır** (bayrak `:67`, JSON profil) — aktarım akışı kod fazında (§5.1/9).
- `user_listening_history` `:90` (coremusic_user) etkileşim **ham** geçmişi olarak `listening_features`'in (AI özeti) **ham kaynağıdır** → ikisi de korunur, birleştirme yok.

**(e) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

| Katman | Öğe | Durum | Kanıt |
|--------|-----|-------|-------|
| Şema dosyası | 6 tablo + 16 indeks + 5 CHECK + 0 FK | ✅ **IMPLEMENTED** | `coremusic_ai.sql` 188 satır / 11.417 bayt (`:31-181`) |
| Dizin kayıtları | 4 satır (slug hizalı) | ✅ kayıtlı | `.decisions/index.md:99` · `.ai/index.md:700` · `brain.md:1016` · `keys.md:292` |
| Doküman (endpoint/akış) | ai-service 4 tablo + k4 14 dosya | ✅ IMPLEMENTED (doküman) | `ai-service.md:28-65` · `k4-yapay-zeka/` |
| **AIEngine skor gövdesi** | collaborative/content-based `return []` + placeholder | ⚠️ **STUB** | `AIEngine.php:212, 217, 234` · `:149-155` · `:319-326` |
| **Kod (tablo CRUD)** | 6 tablo okuma/yazma | ⏳ **PLANNED** | PHP = **0** · JS/TS = **0** |
| **Kod (ML akışı)** | öneri üretimi + train + feedback + registry geçişi | ⏳ **PLANNED** | `train/feedback` endpoint'leri yalnız doküman |
| **Online feature store** | serving katmanı | ⏳ **PLANNED** (ölçüm) | yok |
| BCNF denetimi | 6 tablo determinant denetimi | ⏳ **PLANNED** | ADR-040 `:38` (beyan ≠ denetim) |
| Servis sahibi | `music` — "ATAMA (LLM kodu 0)" | ⏳ PLANNED (servis) | ADR-040 `:165` |
| **Doküman/iddia drift'leri** | başlık `deleted_at`/`BINARY(16)` · ADR-049 satırları · çift tablo · envanter 18↔19 · şablon yolu | ⚠️ **VERIFICATION REQUIRED** | §1.1/9,10,13,14,18 + §5.1/5 |

**(f) Sınır (tekrar yok):** AI stratejisi + PII + API-birincil + **sessiz başarısızlık yasağı** → [[ADR-030-ai-strategy-core]] · prompt loader + **stub etiketi + hata davranışı** → [[ADR-049-startup-prompt-loader]] (bu ADR yalnız satır kaymasını raporlar, stub kararını tekrarlamaz) · sosyal şema/notifications/ENUM → [[ADR-072-social-database-schema]] · podcast şeması → [[ADR-073-podcast-database-schema]] · radio şeması + partition gerekçesi → [[ADR-074-radio-database-schema]] · outbox + WAL senkronu → [[ADR-081-multi-provider-data-sync]] · DB sahipliği + FK istisna defteri → [[ADR-040-database-authority]] · normalizasyon → [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]] · migration tek kapısı → [[ADR-014-multi-db-migration-strategy]] · "DB arası FK YOK" → [[ADR-003-multi-db-bcnf]] · erişim katmanı → [[ADR-002-pdo-mandatory-no-orm]] · A4 veri domaini → [[ADR-039-7-service-platform-architecture]] · `ADR-076` (Video DB) · `ADR-077`–`ADR-079` · `ADR-080` · `ADR-082`–`ADR-088` (başlıklar `index.md:100-103`, `:105-110`'dadır) **dosyalar diskte YOK → düz metin + ⚠️** (`ADR-082` ne dosya ne dizin satırı: `:104` = 081, `:105` = 083).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Yorum-FK'ları hemen gerçek FK'a çevir** (7 deklarasyon ekle) | Referans bütünlüğü görünür olur | ADR-003/040 **çapraz-DB FK yasağı** + 6 bağımlılık **tip olarak uyuşmaz** (INT ↔ BINARY(16)) → DDL zaten patlar; istisna defteri 28'de kalır; iç FK (`model_version_id`) bile tek başına ADR-014 + test ister | Teknik imkânsızlık + süreç ihlali → **ret**; yorum-FK'lar korunur (§2.2/b) |
| 2 | **Şemayı bu ADR'de baştan modelle** (UUIDv7'ye geç + çift tabloları birleştir + JSON'ları 3NF'e çöz) | Standart tamamlanır, örtüşme kalkar | Kod 0 → değişimin doğrulanacak kanıtı yok; ADR-033 denetimi hiç çalıştırılmadan şema değişimi **kör** olur; tablo/kolon adları kutsal (In-Place Refactoring) + dosyanın başlık/footer sayımı (6) değişir | Denetim ayrı PLANNED iş + kanıt yok → **ret**; birleşme/kimlik geçişi **yeni ADR kapısı** (§5.1/6-7) |
| 3 | **Online feature store / Redis'i hemen ekle** (çift katman tamamlansın) | Servis gecikmesi düşer | Ölçüm yok (kod 0 → bugün hiçbir servis sorgusu yok); ADR-073/041 disiplini "ölçülmüş talep" olmadan bağımlılık şişirmez; cache tutarlılığı ayrıca karar ister | Ölçülmüş gerekçe yok → **ret**; §1.3/1 eşiği + §5.1/2 kapısı |
| 4 | **`recommendation_history`'i hemen TTL/partition'la** | Geçmiş şişmesi baştan önlenir | Saklama süresi **kanıtlanmadı** (iş/veri gereksinimi yok); PK `(id)` → PK `(id, created_at)` **DDL** = ADR-014 bypass; `feedback`/`position` geçmişi henüz kimsenin sorguladığı veri değil (kod 0) | Kanıtsız süre + süreç ihlali → **ret**; kalıp + kapı §2.2/d-D3 |
| 5 | **Kodu bu ADR'ye bağla** (6 tablo CRUD + AIEngine + train akışı tek adımda) | Tek seferde biter | Şablon §2 gereği ADR **kod üretmez**; `ai-service.md` zaten26 endpoint'i "var" gibi sunarken stub `return []` iken kodu sahipliğe önceden bağlamak aynı yanılgıyı büyütür (ADR-073'teki doküman-kod dersi) | Yetki/süreç ihlali + yanılgı riski → **ret**; kod ayrı adım §5.1/9 |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **AI şeması ilk kez karar olarak kayıtlı:** 6 tablo / 0 FK deklarasyonu / 16 indeks / 5 CHECK / ML politikası 4 başlık tek dosyada; "AI tabloları nerede?" sorusunun cevabı (**ayrı dosya var: `coremusic_ai.sql`**) ilk satırlarda.
- **"FK ekleyelim" tuzağı önceden kapatıldı:** 0 deklarasyon hem ADR-003 uyumu hem **tip uyuşmazlığı** gerekçesiyle yazıldı → ilk geliştirici kırık `ADD CONSTRAINT` denemez; `model_version_id` iç FK bile bilinçli olarak işaretli.
- **ML politikası ilk kez yazıldı:** feature store çift katmanı, registry değişmezlik/stage/rollback, TTL kapısı, tercih 6 aşaması — dördü de tek yerde; "model sürümünü overwrite etmek / öneriyi sonsuz tutmak / hesabı silerken AI profilini unutmak" senaryolarının üçü de görünür (R5, R3, R7).
- **Partition farkı radyo ile karşılaştırılıp yazıldı:** FK 0 → §26.6 yasağı tetiklenmez; ama doğru sıra PK + ölçüm + yeni ADR — "radyoda olmadı, burada da olmaz" ve "burada olur, hemen yapalım" iki yanılgısı da reddedildi.
- **Dürüst etiket üç katmanda:** şema+doküman IMPLEMENTED / kod PLANNED / AIEngine STUB — "AI hazır" yanılgısına karşı üç çapa; ADR-049 satır kayması da görünür (kör takip önlenir).
- **Sınır net + kırık link üretilmedi:** ADR-030/049/072/073/074/081 bu şemaya dokunmaz; `ADR-076`–`ADR-088`'e wiki-link **kurulmadı** (diskte yok → düz metin + ⚠️).

### 4.2 Olumsuz Sonuçlar

- **Dört başlıkta rakam yok:** TTL süresi, freshness eşiği, retention süresi, online-store eşikleri **sayılmadı** → ⚠️; debate öncesi "geçmişi ne kadar tutuyoruz, profil ne sıklıkla tazeleniyor?" cevapsız kalır.
- **Yedi drift düzeltilmedi** (rapor-only): `deleted_at` iddiası · `BINARY(16)` iddiası · yorum-FK tip uyuşmazlığı · ADR-049 satır kayması · çift-tablo örtüşmesi ×2 · envanter 18↔19 · şablon yolu — reset'e ertelendi; arada vault'ta çelişkili satırlar okunmaya devam eder.
- **BCNF hâlâ beyan düzeyinde:** 6 tablo için determinant denetimi çalıştırılmadı (ADR-033/040 PLANNED) → bu ADR şemanın doğru olduğunu **iddia etmez**, yalnız kaydeder.
- **Kod PLANNED olarak kaldı:** politikalar (D1-D4) kod olmadan doğrulanamaz; AIEngine stub duruyor → öneri çıktısı boş kalmaya devam eder (ADR-049'un da beklediği dış bağımlılık).
- **Audit açığı 4 tabloda duruyor:** `audio_features` created/updated 0, `is_deleted` 3/6, `deleted_at` 0/6 → GDPR/erase kapısı açık (R7).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **AIEngine sessiz boşluğu** — `return []` (`:212/217/234`) "öneri çalışıyor" yanılgısı besler; ADR-030 sessiz başarısızlık yasağıyla yüzleşir | 4 (çok olası) | 3 (orta) | Etiket §2.2/e (STUB); kod fazında hata/log'a bağlama §5.1/9; ADR-049 şart 2 ile aynı kapı |
| R2 | **Çift-tablo örtüşmesi** — iki ses-özelliği tablosu ayrı güncellenir → çelişkili ölçü; iki tercih tablosu "hangisi doğru?" sorusu üretir | 3 (olası) | 3 (orta) | Rol dağıtımı §2.2/d-D1 + D4 (ayar SSOT vs türev); birleşme **yeni ADR** §5.1/6 |
| R3 | **`recommendation_history` şişmesi** — TTL'siz append; geç sorgular yavaşlar, saklama maliyeti büyür | 3 (olası) | 3 (orta) | Süre debate şartı §5.1/11; büyüme ölçümü → partition kapısı §2.2/d-D3 (PK DDL + ADR-014) |
| R4 | **Yorum-FK'ı gerçek FK yapma denemesi** — tip uyuşmazlığı + ADR-003 ihlali + migration patlaması | 2 (mümkün) | 4 (yüksek) | §2.2/b kenetli kural: yorum-FK'lar **dokunulmaz**; ihlal → revert + log ERROR; istisna defteri değişmez |
| R5 | **Registry overwrite / ters stage** — yayınlanmış sürüm silinir/değiştirir → eğitim izi ve rollback kaybolur | 2 (mümkün) | 4 (yüksek) | D2: uq `:154` + soft-delete `:151` + tek yönlü stage; rollback = eski sürümü yeniden active |
| R6 | **"AI hazır" yanılgısı** — `ai-service.md` 26 endpoint + dokümanlar var, kod 0 + STUB | 4 (çok olası) | 3 (orta) | §2.2/e tablosu: kod satırı **0**; "hazır" dendiğinde bu ADR'ye atıf zorunlu (debate şartı §5.1/12) |
| R7 | **Eksik hesap silme** — FK CASCADE `user_preferences`'te kalır, AI profili `coremusic_ai`'da yaşar (PII/GDPR açık) | 3 (olası) | 4 (yüksek) | D4/5-6 + kapı §5.1/7 (ADR-030 PII'ya bağlanır); `deleted_at` açığı defterde §5.1/5a |

### 4.4 Fallback (geri birleşim / geri dönüş)

Debate **RED** çıkarsa ya da karar değiştirilirse: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `index.md:99`/`index.md:700`/`brain.md:1016`/`keys.md:292` satırları **silinmez** (değişmez kalır), `log.md`'ye `ADR-075 RED (debate …)` append edilir. Bu durumda (i) 6 tablo şeması **dosyada kalmaya devam eder** (SQL dosyası bu ADR'den bağımsızdır, dokunulmaz), (ii) D1–D4 politikaları, §5.1/2-4 ölçümleri ve §5.1/6-7 kapıları **kural olmaktan çıkar** (öneri statüsüne döner), (iii) IMPLEMENTED/PLANNED etiketi kalksa bile §1.1'deki 0/0/0 kod sayımları, stub satırları, tip uyuşmazlığı ve başlık iddiaları **bağımsız bulgu olarak geçerlidir**, (iv) 6/16/5 envanteri de bağımsız ölçüm olarak kalır. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks/brain/keys satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

### 4.5 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| Format/dil referansı + aynı seri | [[ADR-074-radio-database-schema]] | ✅ aynı iskelet + aynı etiket disiplini |
| Aynı seri şema ADR'leri | [[ADR-073-podcast-database-schema]] · [[ADR-072-social-database-schema]] | ✅ tekrar yok (partition gerekçesi/ENUM sınırı orada) |
| BCNF kural seti + denetim PLANNED | [[ADR-033-sql-normalization-strategy]] | ✅ uygulandı (audit açığı §5.1/5a) |
| DB sahipliği + 28 FK istisna defteri | [[ADR-040-database-authority]] (`:33` · `:38` · `:49` · `:165`) | ✅ `coremusic_ai` = `music` ATAMA · **istisnada AI satırı YOK** |
| Adlandırma/UUID/audit + defter | [[ADR-041-database-normalization-supplementary]] | ✅ uygulandı · UUID grandfathered §5.1/7 |
| "DB arası FK YOK" hükmü | [[ADR-003-multi-db-bcnf]] | ✅ AI'da ihlal **0** (0 deklarasyon) |
| Migration tek kapısı | [[ADR-014-multi-db-migration-strategy]] | ✅ tüm DDL (partition PK dahil) buradan |
| Erişim katmanı | [[ADR-002-pdo-mandatory-no-orm]] | ✅ kod PLANNED iken de bağlayıcı |
| Servis sahipliği (A4) | [[ADR-039-7-service-platform-architecture]] | ⚠️ AI servisi PLANNED |
| AI stratejisi + PII + sessiz fail | [[ADR-030-ai-strategy-core]] | ✅ sınır (öneri modeli/PII bu ADR'de yazılmaz) |
| Prompt loader + stub etiketi | [[ADR-049-startup-prompt-loader]] | ✅ sınır + **satır kayması raporu** (§1.1/13) |
| Outbox + WAL senkronu | [[ADR-081-multi-provider-data-sync]] | ✅ sınır (kapsam dışı) |
| AI endpoint/kod dokümanları | `ai-service.md:28-65` · `k4-yapay-zeka/` (14 dosya) | ✅ doküman / ⏳ kod PLANNED |
| Dizin slug satırı | [[../index.md]] satır **99** | ✅ hizalı; `[[../brain.md]]` hedef düzeltmesi §5.1/10'da ertelendi |
| Video DB şeması | **`ADR-076`** | ⚠️ **diskte YOK → düz metin + ⚠️ (wiki-link kurulmadı)** |
| Eksik numaralar (dosya + dizin satırı yok) | `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` | ⚠️ **14 numara** → §5.1/8 + §7.1 (rapor-only) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Debate (3 tur / 20 persona) + Tech Lead onayı — TTL süresi, freshness eşiği, retention ve çift-tablo kapıları dahil | MO + persona | 1 gün | ✅ **TAMAMLANDI** (3/20 · 18/2/0 KABUL — §7.2) |
| 2 | **Ölçüm kapısı (feature store):** online-serving gecikmesi/ölçek eşikleri + tazelik (staleness) süresi aşama 2'de ölçülür; sayılar ⚠️ işaretli kalır, online store teknolojisi ancak ölçümle seçilir | Data + Backend | aşama 2 | ⏳ PLANNED (→ debate şartı §5.1/11) |
| 3 | **Workload/EXPLAIN:** JSON içi sorgu ve feed indeksleri kod yazıldıktan sonra `EXPLAIN` ile kalibre edilir; bugün yeni indeks **YOK** (16 mevcut korunur) | Data + Backend | kod fazı | ⏳ PLANNED (ADR-033 ile aynı kapı) |
| 4 | **Öneri TTL kapısı:** `recommendation_history` saklama süresi ölçülür → PK `(id, created_at)` + RANGE/`TO_DAYS` + `MAXVALUE` + `DROP PARTITION` (`api_calls` kalıbı) **yeni ADR** olarak açılır; mevcut tabloya elle DDL yapılmaz | Data Engineer + MO | aşama 2 | ⏳ PLANNED (⚠️ süre rakamı yok) |
| 5 | **Drift raporları (düzeltilmedi):** (5a) `deleted_at` iddiası (`:12`) ↔ 0/6 + `is_deleted` 3/6 + audit eksik 4 tablo → ADR-033/041 defteri · (5b) `BINARY(16)` iddiası (`:11`) ↔ PK 6/6 `INT UNSIGNED` · (5c) yorum-FK **tip uyuşmazlığı** (INT ↔ BINARY(16)) — yorum metnine not · (5d) ADR-049 satır kayması (`:216-225/:230-242/:145-163/:322-334` ↔ güncel `:209-218/:223-235/:138-156/:315-327`; neden adayı `c208092`) · (5e) çift-tablo örtüşmesi ×2 · (5f) envanter 18 ↔ 19 · (5g) şablon yolu `.ai/templates/…` ↔ gerçek `.ai/.templates/…` | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 6 | **Çift-tablo kapısı:** `audio_features` ↔ `music_audio_features` (rol/PK tip farkı) + `user_preference_profiles` ↔ `user_preferences` (ayar vs türev) — birleştirme ya da rol dağıtımı **YENİ ADR** | Data Engineer + MO | aşama 2 | ⏳ PLANNED (⚠️ bu ADR'de birleştirilmedi) |
| 7 | **Tercih silme / GDPR kapısı:** profile'da `is_deleted`/`deleted_at` kararı + hesap silme akışının `coremusic_ai` profilini temizlemesi (FK CASCADE ulaşamaz → R7); ADR-030 PII politikasına bağlanır | Data + Security | aşama 2 | ⏳ PLANNED (yeni ADR/kod) |
| 8 | **Raporlar (düzeltilmedi):** **(8a)** 14 atlanan boşluk `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` (dosya YOK **+** `.decisions/index.md` satırı YOK; `ADR-082` de satırsız — `:104` = 081, `:105` = 083) · **(8b)** `ADR-076`–`ADR-079` / `ADR-083`–`ADR-088` dizin satırı var / dosya YOK (075 bu işlemde doldu) · **(8c)** numara çakışması (kural 7 ≥ 088 ↔ 075 slotu) | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 9 | **Kod yüzeyi:** repository/servis katmanı (6 tablo CRUD + upsert'ler) + AIEngine stub'unun ADR-030 hata/log davranışına bağlanması + öneri/feedback/train akış entegrasyon testi | Backend + QA | 5 gün | ⏳ PLANNED |
| 10 | `index.md:99` `[[../brain.md]]` → gerçek ADR hedefine düzeltmesi + `brain.md:1016`/`keys.md:292`/`index.md:700` satır metinlerinin bu ADR'ye bağlanması | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi** (rapor-only) |
| 11 | **Şart adayı 1 (debate sonrası kesinleşir)** — TTL süresi + freshness eşiği + retention süresi rakamlarının ölçülmesi ve §2.2/d kapılarına bağlanması | Data + Backend | aşama 2 | ⏳ debate PENDING → şart bu ADR'de henüz yazılmaz |
| 12 | **Şart adayı 2 (debate sonrası kesinleşir)** — şema IMPLEMENTED / **kod PLANNED / STUB** etiketinin korunması (kod yazılana kadar "AI hazır" dendiğinde bu ADR'ye atıf zorunlu) | MO + Backend | aşama 2 | ⏳ debate PENDING |
| 13 | **Kimlik tipi kapısı:** 6 tablo `INT UNSIGNED` PK ↔ ADR-041 `BINARY(16)` UUIDv7 standardı — mevcut grandfathered, **yeni tabloda UUID**; başlık `:11` iddiası reset'te düzeltilir | Data Engineer | sonraki vault reset | ⏳ rapor-only |
| 14 | **Model registry iş kuralı kodu:** stage geçişi tek yönlü + metriksiz active yasağı + rollback akışı (D2) kod fazında uygulanır ve test edilir | Backend + QA | 3 gün | ⏳ PLANNED |
| 15 | **Şart 1a (debate):** FK kararı — 7 yorum-FK + 3 başlık bağımlılığında tip uyuşmazlığı (INT UNSIGNED ↔ BINARY(16)) için ya **uyum sağlayıcı** ya da **FK'sız devam gerekçesi** kesin kararı yazılır; gerçek FK denemesi yasak kalır (ADR-003/040) | Data + MO | aşama 2 | ⏳ **debate şartı** (§7.2/1) |
| 16 | **Şart 1b (debate):** ADR-049 alıntı **satır yeniden hizalama** düzeltmesi — alıntı `:216-225/:230-242/:145-163/:322-334` ↔ güncel `:209-218/:223-235/:138-156/:315-327` (aday neden `c208092`) | MO (vault-updater) | sonraki vault reset | ⏳ **debate şartı** (§7.2/1) |
| 17 | **Şart 2 (debate):** AIEngine uygulama fazı kapısı — stub `return []` `:212/:217/:234` ADR-030 hata/log davranışına (sessiz başarısızlık yasağı) bağlanarak kod fazında kapatılır; öncesinde "AI hazır" denmez | Backend + QA | aşama 2 | ⏳ **debate şartı** (§7.2/2) |
| 18 | **Şart 3 (debate):** audit + TTL tamamlama (aşama 2) — 4/6 tabloda eksik audit kolonları (`deleted_at` 0/6 · `is_deleted` 3/6 · `audio_features` created 0) + `recommendation_history` TTL süresi rakamının ölçülmesi | Data + Backend | aşama 2 | ⏳ **debate şartı** (§7.2/3) |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez**), `index.md:99` / `index.md:700` / `brain.md:1016` / `keys.md:292` satırları **silinmez**, `log.md`'ye `ADR-075 RED (debate …)` append edilir; bu durumda (i) 6 tablo şeması `coremusic_ai.sql` içinde **kalmaya devam eder** (SQL dosyası bu ADR'den bağımsızdır, dokunulmaz), (ii) D1–D4 ML politikaları, §5.1/2-4 ölçüm/TTL fazları ve §5.1/6-7 kapıları **kural olmaktan çıkar** (öneriye döner), (iii) 0 FK deklarasyonu zaten ADR-003/040'ta durduğu için otorite kaybı olmaz, (iv) §1.1'deki kod 0/0 sayımları, stub satırları, tip uyuşmazlığı ve başlık iddiaları **bağımsız bulgu olarak geçerlidir**. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), indeks/brain/keys satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

---

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır | Ne | Etiket |
|-------|-------|----|--------|
| `.ai/.decisions/index.md` | `:99` | slug `ADR-075-ai-database-schema` | ✅ hizalı (dosya adı ile birebir) · satır `[[../brain.md]]` önekli kusurlu → §5.1/10 |
| `.ai/index.md` | `:700` | "ADR-075 … AI DB Schema (preferences, features, recommendations, models)" | ✅ kayıtlı |
| `.ai/brain.md` | `:1016` | aynı özet satırı | ✅ kayıtlı (metin bu ADR ile hizalanacak → §5.1/10) |
| `.ai/keys.md` | `:292` | "ADR-075 \| ai database, preferences, features, recommendations, models" | ✅ kayıtlı |
| `.ai/.sql/mysql/coremusic_ai.sql` | `:1-24` · `:31-181` · `:187` | başlık (7 iddia) + CREATE DATABASE · **6 tablo + 16 indeks + 5 CHECK + 0 FK** · footer | ✅ **birincil kanıt** (IMPLEMENTED) |
| `.ai/.sql/mysql/coremusic_user.sql` | `:57` · `:58` · `:67` · `:83` · `:90` · `:173-174` | user_preferences (UUIDv7 + `autoplay_recommendations` + gerçek FK CASCADE) · user_listening_history · kuyruk `source 'ai'` | ✅ komşu tablolar / sınır |
| `.ai/.sql/mysql/coremusic_musics.sql` | `:88` · `:344` · `:364` · `:367` | `musics.id BINARY(16)` (tip kanıtı) · music_audio_features + unique + gerçek FK | ✅ **örtüşme + tip uyuşmazlığı kanıtı** |
| `.ai/.sql/mysql/coremusic_auth.sql` | users `:CREATE TABLE` | `users.id BINARY(16)` UUIDv7 | ✅ tip uyuşmazlığı kanıtı |
| `.ai/.sql/mysql/coremusic_api.sql` | `:115` · `:128-141` | `api_calls` PK `(id, called_at)` + aylık RANGE partition + MAXVALUE — **FK 0** | ✅ vault'un tek partition örneği (D3 kalıbı) |
| `shared/src/AI/AIEngine.php` | `:138-156` · `:209-218` · `:223-235` · `:315-327` | placeholder · `return []` `:212/:217` · `:234` · sabit değerler (`bpm 120.0`, `key 'C'`) | ⚠️ **STUB** (kod kanıtı) |
| `.ai/.decisions/accepted/ADR-049-startup-prompt-loader.md` | `:10` · `:63` · `:341` | AIEngine alıntı aralıkları `:216-225/:230-242/:145-163/:322-334` | ⚠️ **satır kayması** (§1.1/13 — rapor-only) |
| `.ai/.decisions/accepted/ADR-040-database-authority.md` | `:33` · `:38` · `:49` · `:165` · `:342` | 18 dosya (envanter drift) · BCNF iddia ≠ denetim · 28 FK istisnası (AI satırı YOK) · `coremusic_ai` 6 tablo `music` ATAMA | ✅ dosya var — **bağlayıcı otorite** |
| `.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md` | künye · kural 5 | 5 kural + denetim PLANNED | ✅ dosya var — **kural seti** |
| `.ai/.decisions/accepted/ADR-041-database-normalization-supplementary.md` | §2.2 (a–f) | adlandırma · UUID · audit · N+1 defteri | ✅ dosya var — **tamamlayıcı kurallar** |
| `.ai/.decisions/accepted/ADR-074-radio-database-schema.md` | künye · §2.2 · §6.3 | format/dil/etiket referansı + partition gerekçesi | ✅ dosya var — **format referansı** |
| `.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md` · `ADR-014-multi-db-migration-strategy.md` · `ADR-002-pdo-mandatory-no-orm.md` | künye | FK yasağı · tek PHP runner · PDO | ✅ dosya var (frozen — yalnız okunur) |
| `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md` · `ADR-049-startup-prompt-loader.md` · `ADR-081-multi-provider-data-sync.md` · `ADR-039-7-service-platform-architecture.md` | künye | AI stratejisi · prompt loader · outbox+WAL · A4 | ✅ dosya var — **sınır** |
| `architecture/k8-servis/ai-service.md` | `:28-65` · `:45` | 4 endpoint tablosu (analyze/recommendations/voice/learn) + radio satırı | ✅ doküman / ⏳ kod PLANNED |
| `architecture/k4-yapay-zeka/` | 14 dosya | recommendation-engine.md · ml-infrastructure.md · … | ✅ doküman envanteri |
| `.ai/.templates/adr/adr-template.md` | §3 · §6-§7 şablonu | 7 bölüm iskeleti + §1.3 9 alan (Guardrail #16) | ✅ şablon (görevdeki `.ai/templates/…` yolu **YOK** → §5.1/5g) |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-074-radio-database-schema]] (+ [[ADR-073-podcast-database-schema]] · [[ADR-072-social-database-schema]]) · otorite: [[ADR-040-database-authority]] · kural seti: [[ADR-033-sql-normalization-strategy]] + [[ADR-041-database-normalization-supplementary]]
- İlgili ADR'ler: [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-003-multi-db-bcnf]] · [[ADR-014-multi-db-migration-strategy]] · [[ADR-030-ai-strategy-core]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-049-startup-prompt-loader]] · [[ADR-072-social-database-schema]] · [[ADR-073-podcast-database-schema]] · [[ADR-074-radio-database-schema]] · [[ADR-081-multi-provider-data-sync]]
- Şema/kod kanıtları: [[../../.sql/mysql/coremusic_ai.sql]] · [[../../.sql/mysql/coremusic_user.sql]] · [[../../.sql/mysql/coremusic_musics.sql]] · [[../../.sql/mysql/coremusic_auth.sql]] · [[../../.sql/mysql/coremusic_api.sql]] · [[../../architecture/k8-servis/ai-service.md]] · [[../../architecture/k4-yapay-zeka/recommendation-engine.md]] · [[../../architecture/k4-yapay-zeka/ml-infrastructure.md]]
- Vault kökü: [[../index.md]] · [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../log.md]] · [[../../CLAUDE.md]]
- Dizin kayıtları (düz metin — dizin hedefidir, .md olmadığı için wiki-link değil): `.ai/.sql/mysql/` (19 dosya) · `shared/src/AI/` · `.ai/.decisions/draft/` · `.ai/.decisions/rejected/`
- Diskte **olmayan** (düz metin + ⚠️, linklenmez): **`ADR-076`** · `ADR-077` · `ADR-078` · `ADR-079` · `ADR-080` · `ADR-082`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071`

### 6.3 Wiki-Link Sayımı

| Öğe | Değer |
|------|-------|
| Wiki-link toplamı (bu dosya) | **79** canlı occurrence / **28** benzersiz hedef (tüm iki-köşeli parantez kaydı = **88** = 79 canlı + 9 kod içi alıntı; sayım bu dosyada betikle doğrulandı) |
| Diskte olan hedef | **79 / 79** ✅ (benzersiz 28/28 — eksik 0, betik doğrulaması) — path-form (`../index.md`, `../../brain.md`, `../../.sql/mysql/coremusic_ai.sql` …) diskte mevcut; slug-form (`[[ADR-040-database-authority]]` …) ADR-072/073/074 house biçimi, `accepted/` dizininde `<slug>.md` ile basename çözülüyor |
| Kod içi alıntılar (link değil) | **9 adet**: `[[../brain.md]]` ×8 (frontmatter kaynak ×1 · satır 18 ×2 · 34 · 300 · 321 · 339 · 375 — bu satır dahil; index.md:99 kusurlu satırının alıntısı) + slug-form örnek alıntısı ×1 ("Diskte olan hedef" satırı) — canlı link sayılmadı |
| Düz metin + ⚠️ (linklenmeyen) | **`ADR-076`** · `ADR-077`–`ADR-079` · `ADR-080` · `ADR-082`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` |

### 6.4 Debate Notu (✅ TAMAMLANDI)

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona (ADR-072/073/074 ile aynı format) · sonuç **18/2/0 KABUL** |
| Beklenen format | 3 tur / 20 persona · KABUL/RED sayımı debate sonrası §7.2'ye işlenir |
| Debate sonucu (2026-10-01) | ✅ **KABUL** — 3 tur / 20 persona · **18 kabul / 2 çekimser / 0 red** |
| Kesinleşen 3 şart | **(1)** FK kararı + ADR-049 alıntı düzeltmesi (1a §5.1/15 · 1b §5.1/16) · **(2)** AIEngine uygulama fazı — ADR-030 uyumu (§5.1/17) · **(3)** audit + TTL tamamlama — aşama 2 (§5.1/18) |
| Debate öncesi açık kapılar → aday şartlar | TTL süresi + freshness eşiği + retention süresi rakamları (§2.2/d) → **şart adayı 1** (§5.1/11) · IMPLEMENTED/PLANNED/STUB etiketinin korunması (§2.2/e) → **şart adayı 2** (§5.1/12) · çift-tablo + GDPR kapıları (§5.1/6-7) → debate çıktısıyla kesinleşir |
| Debate RED ise | §5.2 geri dönüş + §4.4 fallback birlikte uygulanır |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-10-01 | ✅ |
| Tech Lead | — | 2026-10-01 | ✅ (debate KABUL 18/2/0) |
| Arch Lead | — | — | ⏳ |

**Numara boşlukları raporu (§5.1/8 ile aynı — rapor-only, düzeltilmedi):** `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065` · `ADR-066` · `ADR-067` · `ADR-068` · `ADR-069` · `ADR-070` · `ADR-071` · `ADR-080` = **14 numara** (dosya YOK **+** `.decisions/index.md` satırı YOK). Ayrıca `ADR-082` (dosya YOK, satır YOK — dizin `:104` = ADR-081, `:105` = ADR-083) ve `ADR-076`–`ADR-079` / `ADR-083`–`ADR-088` (dizin satırı var, dosya YOK — bu ADR `ADR-075`'i doldurdu). Bu ADR numara serisine **dokunmaz**, yalnızca raporlar.

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona · sonuç **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tur sayısı | **3 / 3** (tamamlandı 2026-10-01) |
| Tur 1 — bulgu (20 persona) | `coremusic_ai.sql` **188 satır / 11.417 bayt v8.0.0** · 6 tablo IMPLEMENTED (`:31` upp · `:55` lf · `:80` rh · `:109` af · `:139` mv · `:166` tj) + 16 indeks + 5 CHECK · **FK deklarasyonu 0** (7 yorum-FK + 3 başlık bağımlılığı; tip uyuşmazlığı INT UNSIGNED ↔ BINARY(16) nedeniyle yorum-FK'lar asla enforce edilemez; `model_version_id` iç FK bile 0; ADR-040 istisnasında AI satırı yok → ADR-003'e uyumlu) · AIEngine **STUB** — `return []` `:212/:217/:234`, placeholder `:149-155`, sabit `extractFeatures` `:319-326` · ADR-049 alıntıları (`:216-225/:230-242`) **satır kayması** (aday neden `c208092`) → rapor-only · kod CRUD yüzeyi **0** (PHP tablo adı 0 · JS/TS 0 · geniş tarama tek isabet `ThemeManager.php:66` yorum) → **şema IMPLEMENTED / kod PLANNED** · çift katman feature store (online/offline) · model registry 4-stage · audit kolonları **4/6 eksik** · TTL süresi sayılmadı ⚠️ V.R. · ~kaynak 5 sorgu (feature store staleness, öneri etkileşim, registry stage, audit/soft-delete, privacy retention) · 14 atlanan boşluk + `ADR-082` index satırı yok (§5.1/8 · §7.1) · `index.md:99` slug hizalı · `ADR-076` diskte yok → düz metin + ⚠️ V.R. · **oy: 16 kabul/neutral, 4 uyarı** (Critic: FK + alıntı şart · AI: stub şart · QA: audit) |
| Tur 2 — itiraz → çözüm | (1) FK 0 + tip uyuşmazlığı (BINARY(16) ↔ INT) → **FK uygulama kararı** (uyum sağla veya FK'sız devam gerekçesi) → **şart 1a** · (2) ADR-049 alıntı satır kayması → **alıntı düzeltmesi (satır yeniden hizalama)** → **şart 1b** · (3) AIEngine stub `return []` → **uygulama fazı kapısı (ADR-030 uyumu)** → **şart 2** · (4) audit kolonları 4/6 eksik + TTL sayısı yok → **audit + TTL tamamlama (aşama 2)** → **şart 3** |
| Tur 3 — oylama | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Kesinleşen 3 şart | **(1)** FK kararı + alıntı düzeltmesi (1a §5.1/15 · 1b §5.1/16) · **(2)** AIEngine uygulama fazı (§5.1/17) · **(3)** audit + TTL tamamlama — aşama 2 (§5.1/18) — §6.4'te de kayıtlı |
| Debate öncesi gerekenler | ✅ (1) bu ADR yazıldı, (2) açık kapılar §5.1/11-12'ye **aday** olarak bağlandı (şart değil), (3) `log.md`'ye "ADR-075 yazıldı (debate PENDING)" append edildi |

---

*ADR-075 v1.0.0 — 2026-10-01 Created · Authority: CoreMusic Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
