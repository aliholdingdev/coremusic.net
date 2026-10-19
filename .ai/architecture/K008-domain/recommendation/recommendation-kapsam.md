---
node_id: "K008-recommendation-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 94 — Öneri | recommendation | RecommendationSet | AIEngine çıktısı (integration)"
  - "domain-master.md §4.3 satır 158–166 — K008 bu kodun sahibi değildir; sınır ADR olarak domain-kararlar.md §5"
  - "shared/src/AI/AIEngine.php satır 28–77 · shared/src/AI/AIWorkflow.php satır 49–90"
  - ".ai/.sql/mysql/coremusic_ai.sql satır 75–77 · 79–98"
validation: "2026-10-15 — kapsam sınırı kod + şema + master kanıtıyla çizildi; sahiplik çelişkisi açık (C2)"
open_questions:
  - "RecommendationSet aggregate → ADR-097 K4'te kilitlenmedi → taslak"
  - "öneri bağlamının sahibi: K008 mi K006/K020 mi → master §6 C2 (satır 188) → ORKESTRATÖR KARARI"
  - "öneri PHP sunucusu/controller → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# recommendation — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `recommendation`, **ne önerileceğini üretme** anıdır: işbirlikçi skor + içerik
> skoru → hibrit → çeşitlilik → eşik → sıralama → limit. Master §4.3 (satır
> 158–166) bu davranışın K008'de **yaşadığını ama K008'in kodun sahibi olmadığını**
> söyler — uygulama `shared/src/AI/` katmanındadır.

## §1 ROL (ELI15)

İki skor kaynağı vardır: benzer kullanıcıların tercihleri (işbirlikçi) ve şarkı
özellikleri (içerik tabanlı). `AIEngine` bunları **0.6 / 0.4 ağırlığıyla** birleştirir
(satır 65), **%30 farklı tür garantisi** uygular (satır 67–68), **0.7 altı skorları
eler** (satır 71), sıralar ve **en çok 20** sonuç döner (satır 51, 76). `AIWorkflow`
bunu orkestre eder: oturum tercihlerini okur (satır 55), görevi dağıtır (satır 59),
bilgi tabanında arar (satır 62–63), prompt üretir (satır 66–72), motoru çağırır
(satır 75) ve sonucu 3600 sn cache'ler (satır 78). `recommendation_history` tablosu
geri bildirim döngüsünün kaydıdır (coremusic_ai.sql satır 79–98).

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Öneri skorlama zinciri (`AIEngine.php` satır 49–77) | Arama sorgusu + sonuç metrikleri (`search_logs`) | `discovery` |
| 2 | Öneri tarihi + geri bildirim (`recommendation_history` satır 79–98) | Benzerlik çiftleri şeması (`music_similar`, coremusic_musics.sql 221–234) | `music` |
| 3 | Workflow orkestrasyonu + cache (`AIWorkflow.php` satır 49–90) | Oynatma anının durumu | `playback` |
| 4 | `feedback` enum'unda `saved` sinyali (satır 97) | Koleksiyon CRUD | `library` |
| 5 | — | **Öneri davranışının UYGULAMA kodu sahipliği** | master §4.3 (satır 163–165): K008 kodun sahibi DEĞİL → K016/teknoloji katmanı |
| 6 | — | **Bağlamın sahibi: K008 mi, K006/K020 mi?** (recommendation + discovery) | SINIR AÇIK — master §6 C2 (satır 188) |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Öneri (`recommendation`) | ✅ domain-master §3.1 satır 94 |
| 2 | Aggregate (aday) | RecommendationSet — "AIEngine çıktısı (integration)" | ⚠️ TASARIM İDDİASI — ADR-097 K4 |
| 3 | Entity / kayıt | `recommendation_history` satırı (algoritma · güven · geri bildirim · pozisyon) | ✅ coremusic_ai.sql satır 79–98 |
| 4 | Value Object (aday) | `confidence` decimal(3,2) (satır 85) · `position` tinyint (satır 87) · `feedback` enum (satır 97) | ✅ şema |
| 5 | Ubiquitous language | öneriler = AIEngine çıktısı · geri bildirim = `feedback` (none/liked/disliked/skipped/saved) · eşik = `MIN_SCORE` | ✅ kod + şema |
| 6 | Domain event | — (kanıtlanmadı; workflow sonucu memory cache'e yazar, satır 78) | ⚠️ tarama yok |
| 7 | Invariant | limit ≤ 20 (satır 51) · score ≥ 0.7 (satır 71) | ✅ KOD KANITLI |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `shared/src/AI/AIEngine.php` | 28 · 30–32 · 49–77 | sınıf · üç sabit · skorlama zinciri (hibrit 0.6/0.4) |
| `shared/src/AI/AIWorkflow.php` | 49–90 | recommendationWorkflow — memory → orkestratör → bilgi → prompt → engine → cache |
| `.ai/.sql/mysql/coremusic_ai.sql` | 75–77 | `model_versions` — model_type enum'u `recommendation` içerir |
| `.ai/.sql/mysql/coremusic_ai.sql` | 79–98 | `recommendation_history` — güven · geri bildirim · pozisyon · CHECK'ler |
| `.ai/architecture/K008-domain/domain-master.md` | 94 · 158–166 · 232 · 188 | RecommendationSet · §4.3 sahiplik sınırı · §8 bağımlılık · §6 C2 |

## §5 AÇIK SORULAR (R-R1…R-R5)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-R1 | `recommendation_history.user_id`/`music_id` `int unsigned` (s.81–82) — geri kalan sistem `binary(16)` UUID | coremusic_ai.sql s.81–82 ↔ discovery/library/playback/device (hepsi binary(16)) | K007-data + ADR — **kritik kimlik dili çelişkisi** |
| R-R2 | `recommendation_history` hangi kod tarafından yazılır? | AIWorkflow s.49–90 sonucu **memory cache**'e yazar (satır 78); DB INSERT kanıtı YOK | Backend taraması — yazıcı kanıtlanmadı |
| R-R3 | `algorithm` dili: `varchar(30)` + CHECK 4 değer (s.83, s.96) ↔ `music_similar.algorithm` enum 3 (discovery s.226) | iki ayrı küme (ai_curated fazladan) | Domain dili birleştirme — ADR |
| R-R4 | `feedback` geri bildirimi (`liked/disliked/...`) kim işler? | s.97 CHECK 5 değer · işleyen kod kanıtlanmadı | Backend taraması |
| R-R5 | `model_versions` (s.72–77) ile AIEngine bağlantısı? | `model_name+version` UNIQUE · `model_type` CHECK 4 | Model kayıt↔çıkış eşlemesi — tarama |

> `status: draft` ve §3 yasağı bu eklemle **kapanmaz**; kapanış ADR-097 K4'e bağlıdır.

## §6 ŞEMA/KOD KANITLARI: ÖNERİ ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_ai.sql` (model_versions ·
> recommendation_history) + `shared/src/AI/AIEngine.php` + `shared/src/AI/AIWorkflow.php`.

### §6.1 Çekirdek envanter

| Varlık | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `recommendation_history` | s.79–98 | Öneri geçmişi | 2 CHECK (s.96–97) |
| `model_versions` | s.72–77 | Model envanteri | 2 CHECK + `uq_mv_pair` (s.72) |
| `AIEngine::getRecommendations` | s.49–77 | Skorlama zinciri | 6 adımlı pipeline |
| `AIWorkflow::recommendationWorkflow` | s.49–90 | Workflow | memory → orkestratör → engine → cache |

### §6.2 `recommendation_history` — kolon kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `id` | s.80 | **int unsigned AUTO_INCREMENT** | ⚠️ binary(16) PK dilinden farklı (R-R1) |
| `user_id` | s.81 | **int unsigned NOT NULL** | ⚠️ R-R1 — UUID çelişkisi |
| `music_id` | s.82 | **int unsigned NOT NULL** | ⚠️ R-R1 |
| `algorithm` | s.83 | varchar(30) NOT NULL | Serbest metin — CHECK s.96 kısıtlar |
| `model_version` | s.84 | varchar(50) NULL | Model izi |
| `confidence` | s.85 | decimal(3,2) NULL | 0.00–1.00 (yorum — CHECK YOK) |
| `feedback` | s.86 | varchar(20) default 'none' | CHECK s.97 |
| `position` | s.87 | tinyint unsigned NULL | Sıra |
| `is_deleted` | s.88 | tinyint(1) default 0 | Soft delete |
| CHECK `algorithm` | s.96 | 4 değer: collaborative · content_based · hybrid · **ai_curated** | `music_similar` enum 3'ten fazladan: ai_curated |
| CHECK `feedback` | s.97 | 5 değer: none · liked · disliked · skipped · saved | Kapalı küme |

### §6.3 `model_versions` — CHECK kanıtları

| Kolon | Satır | Değer | Kanıt notu |
|---|---|---|---|
| UNIQUE (name,version) | s.72 | `uq_mv_pair` | Model çifti tekil |
| CHECK `model_type` | s.75 | recommendation · audio_analysis · nlp · classification | 4 değer |
| CHECK `status` | s.76 | training · active · deprecated · archived | 4 değer |

### §6.4 AIEngine skorlama zinciri (kod kanıtları)

| # | Adım | Satır | Kanıt |
|---|---|---|---|
| 1 | `MAX_RECOMMENDATIONS=20` | s.30 | limit sabiti |
| 2 | `MIN_SCORE=0.7` | s.31 | eşik sabiti |
| 3 | `DIVERSITY_TARGET=0.30` | s.32 | çeşitlilik hedefi |
| 4 | Context okuma (user_id/session/mood/genre/history) | s.52–56 | 5 alan |
| 5 | Collaborative filtering | s.59 | adım 1 |
| 6 | Content-based | s.62 | adım 2 |
| 7 | Hybrid ağırlık **0.6/0.4** | s.65 | ağırlık kanıtı |
| 8 | Çeşitlilik filtresi | s.68 | %30 farklı tür |
| 9 | MIN_SCORE filtresi | s.71 | `>= 0.7` |
| 10 | Sıralama + limit | s.74–76 | azalan skor |

## §7 INVARIANT ADAYLARI (KOD/SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

| # | Kanıt (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| R-P1 | AIEngine s.51 `min($limit, 20)` | Limit ≤ 20 | İstek 20'yi aşamaz |
| R-P2 | AIEngine s.71 `score >= 0.7` | Minimum skor eşiği | 0.7 altı öneri dömez |
| R-P3 | AIEngine s.65 ağırlık 0.6/0.4 | Hybrid oran sabit | Oran değişirse davranış farkı — konfigürasyon değil sabit |
| R-P4 | s.96 CHECK 4 değer | algorithm kapalı küme | ai_curated dâhil 4 dışı ret |
| R-P5 | s.97 CHECK 5 değer | feedback kapalı küme | 5 dışı ret |
| R-P6 | s.72 `uq_mv_pair` | Model çifti tekil | Aynı (isim,versiyon) iki kez eklenemez |
| R-P7 | s.76 CHECK 4 durum | Model durum kapalı küme | Geçersiz durum ret |
| R-P8 | s.81–82 int unsigned | Kimlik tam sayı | ⚠️ UUID diline ÇELİŞKİLİ — R-R1 |
| R-P9 | s.88 is_deleted | Soft delete | Silme ikili (flag + tarih YOK — s.88'de deleted_at **YOK**) |
| R-P10 | s.85 decimal(3,2) NULL | Confidence opsiyonel | 0.00–1.00 dışına DB'de ret YOK — domain kontrolü önerisi (discovery D-P7 ile aynı sınıf) |

### §7.1 Uyumsuzluk / açık noktalar

| İddia | Kanıt | Durum |
|---|---|---|
| Kimlik dili tutarlı | s.81–82 int ↔ binary(16) (diğer tüm tablolar) | ⚠️ **KRİTİK ÇELİŞKİ** (R-R1) |
| `algorithm` tek dil | s.96 CHECK 4 ↔ music_similar enum 3 (discovery s.226) | ⚠️ ÇELİŞKİLİ (R-R3) |
| DB'ye yazım var | AIWorkflow s.78 → memory cache; INSERT kanıtı YOK | ⚠️ YAZICI KANITSIZ (R-R2) |
| `deleted_at` soft-delete ikilisi | s.88'de yalnız flag — tarih kolonu YOK | ⚠️ tekil flag (master §24 ikilisiyle uyumsuz) |
| `confidence` sınırı | s.85 NULL · CHECK YOK | ⚠️ DB güvencesi YOK (R-P10) |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.**

| # | VO | Dayanak | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-R1 | `Algorithm` | s.96 CHECK(4) | collaborative/content_based/hybrid/ai_curated | `from(string)` — 4 dışı ret |
| V-R2 | `Feedback` | s.97 CHECK(5) | none/liked/disliked/skipped/saved | `from(string)` + `isPositive()` (liked/saved) |
| V-R3 | `Confidence` | s.85 decimal(3,2) NULL | 0.00–1.00 veya null | Dışına ret; null = bilinmiyor |
| V-R4 | `RecommendationPosition` | s.87 tinyint unsigned NULL | ≥0 veya null | Negatif ret |
| V-R5 | `ModelVersionId` | s.84 varchar(50) NULL | serbest metin | null = sürüm izlenmiyor |
| V-R6 | `RecommendationLimit` | AIEngine s.30/s.51 | 1–20 | ≤20 domain kuralı (R-P1) |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-R1 | `from('random_forest')` (5. değer) ret |
| V-R2 | `from('meh')` ret; `isPositive('saved')` true |
| V-R3 | `from(1.5)` ret; `from(null)` ayrı yol |
| V-R6 | `from(25)` → 20'ye kırpılır (min() davranışı, s.51) |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar — **YOK** (kanıtlanmadı)

`Events/Domain/` 10 dosyanın hiçbiri öneri olayı değildir. AIWorkflow
sonucu **memory cache**'e yazar (s.78 — §4 kanıtı); DB'ye yazım ve olay
yayını **kanıtlanmadı** (R-R2). §3 satır 6 tarama yok kaydı.

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-R1 | `recommendation.generated` | getRecommendations dönüşü (s.76) | ❌ DOSYA YOK |
| E-R2 | `recommendation.feedback_given` | `feedback` değişimi (s.86–97) | ❌ DOSYA YOK |
| E-R3 | `model.activated` | `model_versions.status='active'` (s.76) | ❌ DOSYA YOK |
| E-R4 | `model.deprecated` | s.76 `deprecated` | ❌ DOSYA YOK |

> **Kural:** E-R1…E-R4, `domain-bilesenler.md` §12 protokolüne tabidir.

## §10 SERVİS SÖZLEŞMESİ ÖNERİSİ (TASARIM ÖNERİSİ — kanıt DEĞİL)

> AIEngine **arayüz kanıtlı** (`AIEngineInterface`, s.28) — repository değil,
> engine sözleşmesidir; repository önerisi ayrıdır.

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| RM-R1 | `getRecommendations(array $context, int $limit): array` | sıralı liste | ✅ **MEVCUT** — s.49 (kanıtlı imza) |
| RM-R2 | `recordFeedback(string $userId, int $musicId, string $feedback): void` | — | V-R2, R-R4 |
| RM-R3 | `historyByUser(int $userId, int $page): array` | sayfa | s.91 dizin |
| RM-R4 | `activeModel(string $type): ?array` | model veya null | s.73 dizin, s.76 durum |
| RM-R5 | `purgeDeleted(int $days): int` | sayı | R-P9 (flag-only soft delete) |

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| discovery | recommendation ↔ discovery | `algorithm` dili (R-R3) — benzerlik (çift) vs öneri (sıralı) | Benzerlik keşifte; öneri burada |
| playback | recommendation → playback | `autoplay_recommendations` (playback s.114) | Tercih playback'de; motor burada |
| music | recommendation ← music | `music_id` (s.82 — int!) · mood/genre (context s.54–55) | Kimlik çelişkisi R-R1 |
| library | recommendation ← library | `history` context (s.56) | Sinyal orada; karar burada |
| user | recommendation ← user | `user_id` (s.81 — int!) | Kimlik çelişkisi R-R1 |
| K007-data | recommendation ← data | Şema `coremusic_ai` | Bu dal şema üretmez |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-R6…R-R10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-R6 | `recommendation_history` kim, ne zaman yazıyor? (R-R2 genişletme) | INSERT kanıtsız | Backend taraması |
| R-R7 | `ai_curated` (s.96) hangi motor? | 4. CHECK değeri — AIEngine zincirinde yok (s.58–76 yalnız collaborative/content/hybrid) | Motor kanıtı — `ai_curated` üreten kod |
| R-R8 | `confidence` (s.85) hangi adımda set edilir? | zincirde `score` var (s.71–74), `confidence` yok | Skor↔confidence eşlemesi |
| R-R9 | `DIVERSITY_TARGET=0.30` (s.32) neden konfigürasyon değil? | sabit const | ADR — esneklik kararı |
| R-R10 | `model_version` (s.84) dolduruluyor mu? | NULL default | İzlenebilirlik — tarama |

> **§5'teki R-R1–R-R5 aynen korunur**; bu tur yalnız R-R6–R-R10 ekler.

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| R-T1 | `algorithm='random_forest'` (5. değer) | CHECK ret (DB) + VO ret | R-P4, V-R1 |
| R-T2 | `feedback='meh'` (6. değer) | CHECK ret | R-P5, V-R2 |
| R-T3 | `getRecommendations(limit=50)` | 20'ye kırpılır | R-P1, V-R6 |
| R-T4 | Skor 0.65 olan öğe | Listeye girmez | R-P2 |
| R-T5 | `confidence=1.05` | VO ret (domain) — DB retmez | R-P10, V-R3 |
| R-T6 | Aynı (model_name,version) ikinci INSERT | UNIQUE ret | R-P6 |
| R-T7 | `user_id` UUID binary(16) verisi | int unsigned'a **uyuşmaz** — R-R1 senaryosu | §7.1 |

## §14 ÖLÇÜM VE KAPANIŞ (BU TUR)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `recommendation-kapsam.md` |
| 2 | Başlangıç satırı | 78 |
| 3 | Hedef | ≥500 |
| 4 | Kapsam | §5–§24 |
| 5 | Yasak ihlali | 0 |

## §15 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

| # | Komşu BC | Yön | Niyet |
|---|---|---|---|
| 1 | discovery | ↔ | algorithm dili + benzerlik/öneri sınırı |
| 2 | playback | → | autoplay_recommendations tetiklemesi |
| 3 | music | ← | mood/genre/history sinyalleri |
| 4 | library | ← | dinleme geçmişi sinyali |
| 5 | user | ← | kimlik (R-R1 çelişkisi dâhil) |

## §16 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Kullanıcı uygulamayı açar → `recommendationWorkflow` (AIWorkflow s.49–90)
memory'den bağlam toplar, orkestratöre sorar, `AIEngine.getRecommendations`
çağırır → 6 adımlı zincir (§6.4) → 0.7 altı elenir → en fazla 20 öneri döner.
Sonuç **memory cache**'e yazılır (s.78); `recommendation_history`'ye INSERT
kanıtı **YOK** (R-R2). Kullanıcı 3. öneriyi beğenir → `feedback='liked'`
(s.97) — işleyen kod kanıtsız (R-R4). `ai_curated` (s.96) bu zincirde **geçmiyor**
(R-R7). `recommendation.generated`/`feedback_given` olayları **yok** (E-R1/E-R2).

## §17 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §17.1 Bu dosyadan okuma sırası

1. §2–§3 · 2. §6–§7 (kanıt + invariant) · 3. §7.1 + §12 (çelişkiler + açık sorular)

### §17.2 Çağrılan komşu dosyalar

[[discovery-kapsam]] §27–§28 (R-R1/R-R3 çelişki kaydı) · [[playback-kapsam]] (R-PL10) ·
[[music-kapsam]] (mood/genre) · [[library-kapsam]] (history sinyali) ·
[[domain-master]] (§3.1 satır 94 · §8)

## §18 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

- `status: draft` — RecommendationSet aggregate'i kilitlenmemiştir (ADR-097 K4).
- §7/§8/§10/§13 **öneridir, kanıt değildir** (RM-R1 hariç — imza s.49 kanıtlı).
- Ölçüm `[System.IO.File]::ReadAllLines` ile; `Measure-Object -Line` YASAK.

## §19 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Durum |
|---|---|---|
| 1 | Aggregate `RecommendationSet` | ⚠️ TASARIM İDDİASI — ADR-097 K4 |
| 2 | Kimlik dili (int ↔ binary(16)) | ⚠️ **KRİTİK** — R-R1 |
| 3 | DB yazıcı | ⚠️ KANITSIZ — R-R2 |
| 4 | `algorithm` dili | ⚠️ ÇELİŞKİLİ — R-R3 |
| 5 | `ai_curated` motoru | ⚠️ YOK — R-R7 |

## §20 ATIF VE KAYNAK ÇİZELGESİ

| İddia | Kaynak | Tür |
|---|---|---|
| `recommendation_history` kolon + 2 CHECK | `coremusic_ai.sql` s.79–98 | ✅ disk kanıtı |
| `model_versions` 2 CHECK + UNIQUE | `coremusic_ai.sql` s.72–77 | ✅ disk kanıtı |
| 6 adımlı skorlama zinciri | `AIEngine.php` s.49–77 | ✅ disk kanıtı |
| 3 sabit (20/0.7/0.30) | `AIEngine.php` s.30–32 | ✅ disk kanıtı |
| Workflow → memory cache | `AIWorkflow.php` s.49–90 (s.78) | ✅ disk kanıtı |
| Kimlik çelişkisi | discovery-kapsam §27.1 (çapraz okuma) | ✅ çapraz kanıt |
| RecommendationSet | `domain-master.md` satır 94 | ⚠️ TASARIM İDDİASI |
| Repository/VO/invariant | Bu dosya §7–§10, §13 | 🔶 TASARIM ÖNERİSİ |

## §21 TUR KAYDI (2026-10-17)

| # | Öğe | Değer |
|---|---|---|
| 1 | Eklenen bölümler | §5–§21 |
| 2 | Invariant adayı | R-P1…R-P10 |
| 3 | VO adayı | V-R1…V-R6 |
| 4 | Olay adayı | E-R1…E-R4 |
| 5 | Açık soru | R-R1…R-R10 |
| 6 | En kritik bulgu | `user_id`/`music_id` int ↔ binary(16) kimlik dili çelişkisi (R-R1) + DB yazıcı kanıtsız (R-R2) |

## §22 TERİM SÖZLÜĞÜ (BU DALIN UBİQUITOUS DİLİ)

| Terim | Şema/kod karşılığı | Kanıt |
|---|---|---|
| Öneri (geçmiş) | `recommendation_history` satırı | s.79–98 |
| Algoritma | `algorithm` CHECK 4 | s.96 |
| Güven | `confidence` decimal(3,2) | s.85 |
| Geri bildirim | `feedback` CHECK 5 | s.97 |
| Sıra | `position` tinyint | s.87 |
| Model | `model_versions` satırı | s.72–77 |
| Model tipi | `model_type` CHECK 4 | s.75 |
| Model durumu | `status` CHECK 4 | s.76 |
| Skor eşiği | `MIN_SCORE` 0.7 | AIEngine s.31 |
| Limit | `MAX_RECOMMENDATIONS` 20 | AIEngine s.30 |
| Çeşitlilik | `DIVERSITY_TARGET` 0.30 | AIEngine s.32 |
| Hibrit ağırlık | 0.6 collaborative / 0.4 content | AIEngine s.65 |

## §23 SONRAKİ TUR İŞ KALEMLERİ

1. R-R1 (kimlik dili) — **en yüksek öncelik**, K007-data + ADR.
2. R-R2/R-R6 (DB yazıcı taraması).
3. R-R3 (`algorithm` dili birleştirme).
4. R-R7 (`ai_curated` motoru kanıtı).
5. §13 senaryolarının `test-writer` kapsamına devri.

## §24 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

### §24.1 Öneri üretimi

Context (user_id · mood · genre · history — s.52–56) → collaborative (s.59) →
content-based (s.62) → hybrid 0.6/0.4 (s.65) → çeşitlilik %30 (s.68) →
MIN_SCORE 0.7 (s.71) → sıralama + ≤20 (s.74–76) → **memory cache** (workflow
s.78) → `recommendation.generated` olayı **yok** (E-R1) → `recommendation_history`
yazımı **kanıtsız** (R-R2).

### §24.2 Geri bildirim

Kullanıcı beğenir → `RM-R2 recordFeedback` (V-R2 doğrulama) → `feedback='liked'`
(s.97 CHECK geçerli) → `recommendation.feedback_given` olayı **yok** (E-R2) →
gelecek önerilere etkisi kanıtsız (kapalı döngü tasarımı **UNKNOWN**).

### §24.3 Model yaşam döngüsü

`RM-R4 activeModel` → `model_versions` (s.76 `active`) → öneri üretimi bu
sürümle (s.84 `model_version` — dolduruluyor mu, R-R10) → `status='deprecated'`
→ `model.deprecated` olayı **yok** (E-R4).

## §25 ÇAPRAZ REFERANS VE BAĞIMLILIK MATRİSİ

| # | Bu dosya referansı | Hedef | İlişki |
|---|---|---|---|
| 1 | §6.2 `user_id int unsigned` (s.81) | discovery-kapsam §27.1 | Çapraz çelişki kaydı — aynı bulgu iki dosyada |
| 2 | §6.2 CHECK 4 (s.96) | discovery s.226 enum 3 | R-R3 — dil farkı |
| 3 | §6.4 sabitler (s.30–32) | R-P1/R-P2/R-P9 + AIWorkflow s.49–90 | Kod → invariant zinciri |
| 4 | §7 R-P9 (deleted_at YOK) | master §24 soft-delete ikili | ⚠️ uyumsuz |
| 5 | §9.1 olay boşluğu | `Events/Domain/` 10 dosya | negatif kanıt |
| 6 | playback s.114 `autoplay_recommendations` | R-PL10 (playback dosyası) | Çift yönlü soru |
| 7 | AIEngine `AIEngineInterface` (s.28) | Contracts/AI | Arayüz kanıtlı — tek exception (§10.1) |
| 8 | master §3.1 satır 94 | RecommendationSet | ⚠️ ADR-097 K4 |
| 9 | `domain-bilesenler.md` §14 | E-R1…E-R4 | Olay→BC çizelgesi |
| 10 | K007-data | R-R1 kimlik dili | Şema sahibi — karar |

## §26 TUR SONU KONTROL LİSTESİ

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Authority eski satır | ✅ `2026-10-15` aynen duruyor |
| 2 | Yeni Authority | ✅ `2026-10-17` append |
| 3 | Frontmatter | ✅ korundu |
| 4 | Bölüm silme | ❌ YOK — §1–§4 duruyor, §5–§26 append |
| 5 | Yer tutucu | 0 |
| 6 | Uydurma satır numarası | 0 — tüm `s.NN` okunan AI SQL/PHP'den |
| 7 | Kanıtlı istisna | RM-R1 (`getRecommendations`) — imza AIEngine s.49'da mevcut |
| 8 | Ölçüm | `ReadAllLines().Length` |

## §27 SONRAKİ TUR DEVİR KARTI

| # | Devredilen iş | Hedef dosya | Öncelik |
|---|---|---|---|
| 1 | `user_id`/`music_id` int ↔ binary(16) (R-R1) | K007-data + ADR | **KRİTİK** |
| 2 | `recommendation_history` yazıcı taraması (R-R2/R-R6) | Backend | YÜKSEK |
| 3 | `algorithm` dili (R-R3) | ADR-097 K4 | YÜKSEK |
| 4 | `ai_curated` motoru (R-R7) | AI taraması | ORTA |
| 5 | Geri bildirim döngüsü (§24.2) | Backend + product | ORTA |
| 6 | `deleted_at` eksikliği (§7.1) | K007-data | DÜŞÜK |

> **Tur durumu (2026-10-17):** bu dosya §5–§27 ile derinleştirildi;
> `status: draft` değişmedi, §1–§4 aynen korundu, tüm ekler append-only.

## §28 SKORLAMA ZİNCİRİ DAVRANIŞ ÇİZELGESİ (AIEngine §6.4 GENİŞLETME)

> **Kaynak:** `shared/src/AI/AIEngine.php` s.49–77 (2026-10-17 okuması). Her satır
> kod kanıtıdır; yorumlar açıkça işaretlidir.

| # | Giriş → Çıkış | Satır | Kanıt / yorum |
|---|---|---|---|
| 1 | `limit` → `min(limit, 20)` | s.51 | ✅ kod |
| 2 | `context['user_id']` (nullable) | s.52 | ✅ anonim öneri mümkün |
| 3 | `context['session_id']` | s.53 | ✅ oturum bağlamı |
| 4 | `context['mood']` · `context['genre']` | s.54–55 | ✅ music mood enum 7 ile (s.320) — dönüşüm kanıtsız |
| 5 | `context['history']` (array) | s.56 | ✅ library sinyali (komşu) |
| 6 | collaborative skorlar | s.59 | ✅ `getCollaborativeScores` — **içeriği bu turda okunmadı** ⚠️ |
| 7 | content-based skorlar | s.62 | ✅ `getContentBasedScores` — içeriği ⚠️ |
| 8 | `combineScores(..., 0.6, 0.4)` | s.65 | ✅ ağırlık sabitleri — fonksiyon gövdesi ⚠️ |
| 9 | `applyDiversityFilter(hybrid, genre)` | s.68 | ✅ fonksiyon çağrısı — içeriği ⚠️ |
| 10 | `score >= MIN_SCORE` (0.7) | s.71 | ✅ filtre |
| 11 | `usort` azalan skor | s.74 | ✅ sıralama |
| 12 | `array_slice(0, limit)` | s.76 | ✅ kesme |

### §28.1 Okunmamış iç fonksiyonlar (⚠️ VERIFICATION REQUIRED)

| Fonksiyon | Çağrı satırı | Durum |
|---|---|---|
| `getCollaborativeScores` | s.59 | ⚠️ gövde okunmadı — kullanıcı benzerliği hesabı **UNKNOWN** |
| `getContentBasedScores` | s.62 | ⚠️ gövde okunmadı — özellik çıkarımı **UNKNOWN** |
| `combineScores` | s.65 | ⚠️ gövde okunmadı — lineer mi ağırlıklı ort. mi **UNKNOWN** |
| `applyDiversityFilter` | s.68 | ⚠️ gövde okunmadı — %30 uygulaması **UNKNOWN** |

> **Kural:** bu fonksiyonların davranış iddiaları bu dosyada **yazılmaz**;
> sonraki tur `AIEngine.php` iç gövdelerini okur ve §6.4'e ekler.

## §29 DİZİN VE SORGU YOLU ÇİZELGESİ (KANIT)

| Dizin | Satır | Sorgu yolu |
|---|---|---|
| `idx_rh_user` | s.91 | Kullanıcı öneri geçmişi |
| `idx_rh_music` | s.92 | Parça bazlı öneriler (popülerite) |
| `idx_rh_algorithm` | s.93 | Algoritma karşılaştırma |
| `idx_rh_feedback` | s.94 | Geri bildirim analizi |
| `idx_rh_created DESC` | s.95 | Zaman serisi (son öneriler) |
| `uq_mv_pair` | s.72 | Model çifti tekil |
| `idx_mv_type` · `idx_mv_status` | s.73–74 | Tip/durum filtreleri |

> **N+1 notu:** `historyByUser` (RM-R3) tek dizinle (s.91) sayfalanır; `music_id`
> (s.82 — int!) parça metası ister — komşu JOIN'inde **kimlik dili çelişkisi
> (R-R1) JOIN'i kırar**: `int unsigned` ↔ `binary(16)` doğrudan eşleşmez,
> dönüşüm katmanı **UNKNOWN**. Bu, R-R1'in pratik sonucudur — §7.1.

## §30 KAPSAM DIŞI KAYITLAR (coremusic_ai — bu dalda olmayanlar)

| Tablo | Satır | Neden bu dalda değil | Not |
|---|---|---|---|
| `model_versions` | s.60–77 (dizini §6.3) | Kısmen dâhil — envanter bu dalda, eğitim processi değil | Model yaşam döngüsü sınırlı dahil (§24.3) |
| `training_jobs` | s.100+ | Eğitim işi (MLOps) — öneri üretimi değil | ⚠️ train ↔ serve sınırı ADR-097 K4 |
| AIWorkflow diğer adımları | s.49–90 dışı | Prompt/nöro-zekâ akışı | Orkestrasyon ayrı alan (ADR-030) |
| `training_jobs` kolonları | s.100–dışarı | Eğitim parametreleri | Müfredat dışı — bu tur okunmadı |

### §30.1 Bu turda okunmayan ve okunan kaynak özeti

| Kaynak | Durum |
|---|---|
| `coremusic_ai.sql` s.72–98 | ✅ OKUNDU (bu tur) |
| `AIEngine.php` s.26–77 | ✅ OKUNDU (bu tur) |
| `AIWorkflow.php` s.49–90 | ✅ §4'te okunmuştu (önceki tur) |
| `AIEngine.php` iç fonksiyon gövdeleri | ⚠️ OKUNMADI — §28.1 |
| `training_jobs` tam şema | ⚠️ OKUNMADI — §30 |

> **Sınır notu:** `model_versions` bu dosyada **kapsam içine alındı** çünkü
> `recommendation_history.model_version` (s.84) doğrudan ona bağlanır; fakat
> model eğitim süreci (training_jobs) **dışarıdadır** — öneri = servis,
> eğitim = operasyon. `ai_curated` algoritmasının hangi modelden çıktığı
> (R-R7) bu sınırın tam üzerinde durur — kapanış ADR-097 K4.

---

**Kardeş dosyalar:** [[recommendation-dogrulama]] · [[recommendation-web-arastirma]] · [[recommendation-ornekler]] · [[recommendation-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · 2026-10-17 (K008-domain derinleştirme — §5–§30 eklendi; CHECK/skorlama zinciri kanıtı derinleştirildi, int↔binary(16) kimlik çelişkisi kaydedildi, okunmamış iç fonksiyonlar işaretlendi)
