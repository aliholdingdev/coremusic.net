---
node_id: "K008-discovery-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 93 — Keşif | discovery | DiscoveryQuery (radar/trend) | MusicPlayedEvent (akış girdisi)"
  - ".ai/.sql/mysql/coremusic_logs.sql satır 398–414 (search_logs) · 416–431 (user_activity_logs)"
  - ".ai/.sql/mysql/coremusic_musics.sql satır 221–234 (music_similar)"
validation: "2026-10-15 — kapsam sınırı 3 SQL tablo + master satır kanıtıyla çizildi"
open_questions:
  - "DiscoveryQuery aggregate → ADR-097 K4'te kilitlenmedi → taslak"
  - "bağlam sahipliği: discovery/recommendation K008 mi K006/K020 mi → master §6 C2 → ORKESTRATÖR KARARI"
  - "Arama PHP servisi/controller → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# discovery — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `discovery`, **kullanıcının arayıp bulma** anıdır: sorgu, sonuç, seçim, tarama
> (radar/trend). "Ne önerilir" sorusu `recommendation`'ın, "ne var" sorusu `music`'in.

## §1 ROL (ELI15)

Kullanıcı arama kutusuna yazar → sistem sorguyu, sonuç sayısını, seçtiği sonucun
pozisyonunu ve yanıt süresini kaydeder (`search_logs`, satır 398–414). Ne tıklandığı
daha geniş aktivite akışına düşer (`user_activity_logs`, satır 416–431) — hem
analiz hem kişiselleştirme besidir. Benzer şarkı eşlemeleri `music_similar`'dadır
(satır 221–234). Master §3.1 satır 93 bu bağlamı `DiscoveryQuery (radar/trend)`
olarak adlandırır ve girdi event'ine `MusicPlayedEvent` der (akış girdisi).

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Arama sorgusu + sonuç metrikleri (`search_logs` satır 401–406) | Öneri skorlaması / AIEngine çıktısı | `recommendation` |
| 2 | Benzerlik çiftleri + benzerlik puanı (`music_similar` satır 225–226) | Öneri listesi üretimi (RecommendationSet) | `recommendation` |
| 3 | Aktivite akışı: arama/çalma/beğenme olayları (activity_type, satır 419) | Oynatma anının durumu | `playback` |
| 4 | `search_type` enum'unda `playlist`/`album`/`artist` sonuç tipleri (satır 402) | Çalma listesi CRUD | `playlist` |
| 5 | Kategori/öne çıkan içerik arayüzü (referans: [[discovery-web-arastirma]] §1 W-1, W-2) | Katalog tanımı (eser/şarkı) | `music` |
| 6 | — | **Bağlamın sahibi: discovery/recommendation K008 mi, K006/K020 mi?** | SINIR AÇIK — master §6 C2 |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Keşif (`discovery`) | ✅ domain-master §3.1 satır 93 |
| 2 | Aggregate (aday) | DiscoveryQuery (radar/trend) | ⚠️ TASARIM İDDİASI — ADR-097 K4 |
| 3 | Entity / kayıt | `search_logs` satırı (sorgu + tip + sonuç metrikleri) | ✅ coremusic_logs.sql satır 398–414 |
| 4 | Value Object (aday) | `search_type` enum: music · artist · album · playlist · podcast · radio · video (7) | ✅ satır 402 |
| 5 | Ubiquitous language | arama = `search_logs` · benzerlik = `music_similar` · aktivite = `user_activity_logs` | ✅ şema adları |
| 6 | Domain event | keşif event'i YOK; girdi = `MusicPlayedEvent` (master satır 93) | ✅ girdi event'i var (bkz [[playback-dogrulama]] D-PL7) |
| 7 | Invariant | tanımsız | ⚠️ ADR-097 K4 |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/.sql/mysql/coremusic_logs.sql` | 398–414 | `search_logs` — sorgu · tip · sonuç · yanıt süresi · FULLTEXT |
| `.ai/.sql/mysql/coremusic_logs.sql` | 416–431 | `user_activity_logs` — 14 değerli aktivite · json metadata · cihaz |
| `.ai/.sql/mysql/coremusic_musics.sql` | 221–234 | `music_similar` — benzerlik puanı · algoritma enum'u |
| `.ai/architecture/K008-domain/domain-master.md` | 93 | DiscoveryQuery + MusicPlayedEvent (akış girdisi) eşlemesi |
| `shared/src/Events/Domain/MusicPlayedEvent.php` | 20 | girdi event'i sınıfı (bağlam girdisi — §2 satır 6'ya bkz) |

## §5 AÇIK SORULAR (R-D1…R-D5)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-D1 | Keşif domain event'i neden yok? | master §3.4 (keşif boşluğu) · §9.1 | Eksiklik bilinçli mi — ADR |
| R-D2 | `search_logs.selected_result_id` (s.404) hangi varlığa işaret eder? | polymorphic — FK YOK | Doğrulama sözleşmesi (L-P4 sınıfı) |
| R-D3 | `music_similar.algorithm` enum (s.226) hangi servis tarafından yazılır? | 3 değer (collaborative/content_based/hybrid) | Backend taraması — üretim motoru |
| R-D4 | `response_time_ms` (s.406) hangi katmanda ölçülür? | NULL · int | Ölçüm noktası — backend mi gateway mi |
| R-D5 | `user_activity_logs.activity_type` 14 değer (s.419) — domain event envanteriyle eşleşiyor mu? | §9'da 10 event var, 14 aktivite tipi | Çapraz okuma — activity↔event eşlemesi |

> `status: draft` ve §3 yasağı bu eklemle **kapanmaz**; kapanış ADR-097 K4'e bağlıdır.

## §6 ŞEMA KANITLARI: KEŞİF ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_logs.sql` (search_logs ·
> user_activity_logs) + `.ai/.sql/mysql/coremusic_musics.sql` (music_similar).
> Bu bölüm **kilitli kapsam değildir** — yalnız kanıt sunar.

### §6.1 Çekirdek tablo envanteri

| Tablo | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `search_logs` | s.398–414 | Arama sorgu günlüğü | FULLTEXT (s.413) + 4 KEY |
| `user_activity_logs` | s.416–431 | Kullanıcı aktivite günlüğü | 14 değerli enum (s.419) |
| `music_similar` | s.221–234 | Benzerlik çifti | UNIQUE (music,similar) s.229 |

### §6.2 `search_logs` — kolon kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `user_id` | s.400 | binary(16) **NULL** | Anonim arama serbest |
| `search_query` | s.401 | varchar(500) NOT NULL | Sorgu metni |
| `search_type` | s.402 | enum 7 (music/artist/album/playlist/podcast/radio/video) default music | Kapalı küme |
| `results_count` | s.403 | int default 0 | Sonuç sayısı |
| `selected_result_id` | s.404 | binary(16) NULL | Polymorphic — FK YOK |
| `selected_result_position` | s.405 | int NULL | Seçilen sıralama |
| `response_time_ms` | s.406 | int NULL | Yanıt süresi |
| FULLTEXT | s.413 | `ft_search_logs_query` | Tam metin dizini |

### §6.3 `user_activity_logs` — kolon kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `activity_type` | s.419 | enum **14** (login/logout/play/pause/skip/like/unlike/follow/unfollow/download/share/search/playlist_add/playlist_remove) NOT NULL | En geniş aktivite kümesi |
| `entity_type` | s.420 | varchar(50) NULL | Serbest metin — enum değil |
| `entity_id` | s.421 | binary(16) NULL | Polymorphic — FK YOK |
| `metadata` | s.422 | json NULL | Bağlamsal ek veri |
| `device_type` | s.424 | enum 6 (desktop/mobile/tablet/car/studio/home) | `user_listening_history` ile (s.92) **aynı küme** ✅ |
| KEY (user,type) | s.430 | `idx_user_activity_user_type` | Kullanıcı+tip sorgusu |

### §6.4 `music_similar` — kolon kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `music_id` | s.223 | binary(16) NOT NULL | FK CASCADE (s.232) |
| `similar_music_id` | s.224 | binary(16) NOT NULL | FK CASCADE (s.233) |
| `similarity_score` | s.225 | decimal(3,2) NOT NULL | 0.00–1.00 |
| `algorithm` | s.226 | enum 3 (collaborative/content_based/hybrid) default hybrid | Algoritma dili |
| UNIQUE (music,similar) | s.229 | `idx_music_similar_unique` | Çift tekil |
| KEY (score) | s.231 | `idx_music_similar_score` | Puan sıralaması |

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| D-P1 | s.401 NOT NULL · s.400 NULL | Sorgu zorunlu, kullanıcı opsiyonel | Boş sorgu yazılamaz; anonim arama geçerli |
| D-P2 | s.402 enum 7 | Arama tipi kapalı küme | Geçersiz tip yazılamaz |
| D-P3 | s.404 polymorphic · FK YOK | Seçim hedefi DB'de doğrulanmaz | Uygulama doğrulamalı (L-P4 sınıfı) |
| D-P4 | s.419 enum 14 NOT NULL | Aktivite tipi kapalı küme | Geçersiz aktivite yazılamaz |
| D-P5 | s.422 json NULL | Metadata opsiyonel + esnek | Metadata şeması DOMAIN'DE TANIMLI DEĞİL — açık alan |
| D-P6 | s.229 UNIQUE (music,similar) | Benzerlik çifti tekil | Aynı çift iki kez eklenemez |
| D-P7 | s.225 decimal(3,2) NOT NULL | Puan zorunlu | 0.00–1.00 dışına ret — DB sınırı YOK (decimal taşabilir) — domain kontrolü önerisi |
| D-P8 | s.226 enum 3 | Algoritma kapalı küme | Geçersiz algoritma yazılamaz |
| D-P9 | s.232–233 FK CASCADE ×2 | Parça silinirse benzerlikler silinir | Sahipsiz benzerlik satırı kalmaz |
| D-P10 | s.413 FULLTEXT | Tam metin yalnız `search_query`'de | FULLTEXT sorguları bu kolonla |
| D-P11 | s.424 enum 6 | Cihaz tipi kümesi | `user_listening_history` s.92 ile **aynı** — tutarlı ✅ |
| D-P12 | s.420–421 (entity_type varchar + entity_id binary) | Aktivite varlık referansı serbest | Varlık tipi enum değil — doğrulama uygulamada |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| `device_type` iki tabloda tutarlı | s.424 (activity) ↔ s.92 (listening, library) — ikisi enum 6, aynı küme | ✅ TUTARLI |
| `entity_type` (s.420) serbest varchar | enum değil · `search_type` (s.402) enum 7 | ⚠️ İKİ DİL — neden farklı? (R-D5 sınıfı) |
| `similarity_score` sınırı | decimal(3,2) NOT NULL · CHECK **YOK** | ⚠️ DB güvencesi YOK (D-P7) |
| `search_logs` anonimlik | s.400 NULL · s.401 NOT NULL | ✅ bilinçli — anonim arama destekli |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Adaylar şema kolonlarından türetilmiştir.

| # | VO | Dayanak | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-D1 | `SearchType` | s.402 enum(7) | music/artist/album/playlist/podcast/radio/video | `from(string)` fabrika |
| V-D2 | `SearchQuery` | s.401 varchar(500) | 1–500 karakter | Boş/whitespace ret |
| V-D3 | `ResponseTimeMs` | s.406 int NULL | ≥0 veya null | Negatif ret; null = ölçülmedi |
| V-D4 | `ActivityType` | s.419 enum(14) | 14 değer | `from(string)` — geçersizde ret |
| V-D5 | `SimilarityScore` | s.225 decimal(3,2) | 0.00–1.00 | Dışına ret (DB sınırı yok — domain'de) |
| V-D6 | `SimilarityAlgorithm` | s.226 enum(3) | collaborative/content_based/hybrid | `from(string)` + `isHybrid()` |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-D1 | 8. değer ret |
| V-D4 | 15. değer ret |
| V-D5 | `from(-0.01)` ve `from(1.01)` ret; `from(0.00)`/`from(1.00)` kabul |
| V-D6 | 4. değer ret |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar — **bu dalda YOK** (girdi olayı var)

`discovery`'nin kendi olayı **0**'dır; girdi = `MusicPlayedEvent`
(`shared/src/Events/Domain/MusicPlayedEvent.php` s.20 · `music.played` s.39) —
playback'den akar (master satır 93 eşlemesi). §3 satır 6 negatif kanıt.

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-D1 | `search.performed` | `search_logs` INSERT (s.398–414) | ❌ DOSYA YOK |
| E-D2 | `search.result_selected` | `selected_result_id` (s.404) | ❌ DOSYA YOK |
| E-D3 | `similar.updated` | `music_similar` yeniden yazımı (s.221–234) | ❌ DOSYA YOK |
| E-D4 | `activity.recorded` | `user_activity_logs` INSERT (s.416–431) | ❌ DOSYA YOK |

> **Kural:** E-D1…E-D4, `domain-bilesenler.md` §12 protokolüne tabidir.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ (TASARIM ÖNERİSİ — kanıt DEĞİL)

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| RM-D1 | `search(string $query, string $type, int $page, int $limit): array` | sayfa | D-P1, D-P2, V-D1/V-D2 |
| RM-D2 | `logSearch(?string $userId, string $query, string $type, int $count): void` | — | D-P1, D-P2 |
| RM-D3 | `recordSelection(string $logId, string $entityType, string $entityId, int $pos): void` | — | D-P3, D-P12 |
| RM-D4 | `logActivity(string $userId, string $type, ?string $entityType, ?string $entityId, ?array $meta): void` | — | D-P4, D-P5, D-P12 |
| RM-D5 | `similarTo(string $musicId, int $limit): array` | benzer listesi | D-P6, D-P7 |
| RM-D6 | `rebuildSimilarity(string $musicId, string $algorithm): int` | satır sayısı | D-P8, V-D6 |

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| music | discovery ← music | `music_similar.music_id` FK (s.232) · mood/genre filtre kolonları | Benzerlik **üretimi** music'te değil, burada karar |
| playback | discovery ← playback | `MusicPlayedEvent` girdisi | Akış girdisi — olay sahibi playback |
| recommendation | discovery ↔ recommendation | `algorithm` enum (s.226) — öneri ile ortak dil | Benzerlik (çift) vs öneri (sıralı liste) — sınır §12 R-D8 |
| library | discovery ← library | `user_activity_logs` sinyalleri (like/play) | Sinyal orada doğar, burada okunur |
| user | discovery ← user | `user_id` (s.400 · s.418) | Kimlik user'de |
| K007-data | discovery ← data | Şema `coremusic_logs` | Bu dal şema üretmez |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-D6…R-D10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-D6 | `search_type` enum 7 (s.402) ile `activity_type` 'search' (s.419) ilişkisi? | iki ayrı kayıt yolu | Tekilleştirme — ikisi de aramayı kaydediyor mu? |
| R-D7 | `entity_type` (s.420) serbest varchar — kapalı küme olmalı mı? | enum değil | Domain dili kararı |
| R-D8 | `music_similar` (çift) vs `recommendation_history` (sıralı liste) sınırı? | s.221–234 vs coremusic_ai.sql s.79–98 | Bağlam ayrımı — ADR-097 K4 |
| R-D9 | `response_time_ms` ölçüm noktası | s.406 NULL | Backend mi gateway mi — R-D4 ile birleşik |
| R-D10 | Anonim arama (`user_id` NULL, s.400) kişiselleştirme girdisi olabilir mi? | s.400 | Gizlilik politikası — security onayı |

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| D-T1 | `search_type='movie'` (8. değer) | VO ret | V-D1 |
| D-T2 | `activity_type='rate'` (15. değer) | VO ret | V-D4 |
| D-T3 | `similarity_score=1.50` | VO ret (domain) — DB retmez | D-P7, V-D5 |
| D-T4 | Boş sorgu (`search_query=''`) | Ret (NOT NULL + domain boş ret) | D-P1 |
| D-T5 | Aynı (music,similar) ikinci INSERT | UNIQUE ret | D-P6 |
| D-T6 | `selected_result_id` var olmayan varlık | DB ret **DEĞİL** (FK yok) — uygulama ret | D-P3 |
| D-T7 | Anonim arama (`user_id=null`) | Kabul | D-P1 |

## §14 ÖLÇÜM VE KAPANIŞ (BU TUR)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `discovery-kapsam.md` |
| 2 | Başlangıç satırı | 73 |
| 3 | Hedef | ≥500 |
| 4 | Kapsam | §5–§25 |
| 5 | Yasak ihlali | 0 |

## §15 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

| # | Komşu BC | Yön | Niyet |
|---|---|---|---|
| 1 | music | ← | Benzerlik + filtre kolonları |
| 2 | playback | ← | `music.played` girdisi |
| 3 | recommendation | ↔ | `algorithm` ortak dili (R-D8) |
| 4 | library | ← | Aktivite sinyalleri |
| 5 | user | ← | Kimlik |

## §16 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Kullanıcı "jazz" arar → `search_logs` INSERT (`search_type='music'`,
`s.402`) — `user_id` null (anonim, s.400). Sonuç sayfası 20 gösterir
(`results_count=20`, s.403). Kullanıcı 3. sıradakini seçer →
`selected_result_id` + `selected_result_position=3` (s.404–405) — hedef varlık
DB'de doğrulanmaz (D-P3). Seçim `user_activity_logs`'a `activity_type='search'`
olarak da yazılabilir (s.419) — **iki kayıt yolu var, birleştirme yok** (R-D6).
Benzerlik tarafında `music_similar` satırı `algorithm='hybrid'`,
`similarity_score=0.87` (s.225–226) — puan sınırı domain'de yok (D-P7).
`similar.updated` olayı **yok** (E-D3).

## §17 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §17.1 Bu dosyadan okuma sırası

1. §2–§3 · 2. §6–§7 (şema + invariant) · 3. §7.1 + §12 (çelişkiler + açık sorular)

### §17.2 Çağrılan komşu dosyalar

[[recommendation-kapsam]] (R-D8 sınır) · [[playback-kapsam]] (girdi olayı) ·
[[library-kapsam]] (aktivite sinyalleri) · [[music-kapsam]] (mood/genre kolonları) ·
[[domain-master]] (§3.1 satır 93 · §3.4)

## §18 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

- `status: draft` — `DiscoveryQuery` aggregate'i kilitlenmemiştir (ADR-097 K4).
- §7/§8/§10/§13 **öneridir, kanıt değildir**.
- Ölçüm `[System.IO.File]::ReadAllLines` ile; `Measure-Object -Line` YASAK.

## §19 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Durum |
|---|---|---|
| 1 | Aggregate `DiscoveryQuery` | ⚠️ TASARIM İDDİASI — master §3.1 satır 93 |
| 2 | Domain event | ❌ 0 (§9.1) — yalnız girdi var |
| 3 | `entity_type` dili | ⚠️ serbest varchar (R-D7) |
| 4 | `similarity_score` sınırı | ⚠️ DB güvencesi YOK (D-P7) |
| 5 | Benzerlik ↔ öneri sınırı | ⚠️ AÇIK (R-D8) |

## §20 ATIF VE KAYNAK ÇİZELGESİ

| İddia | Kaynak | Tür |
|---|---|---|
| `search_logs` kolon/dizin/FULLTEXT | `coremusic_logs.sql` s.398–414 | ✅ disk kanıtı |
| `user_activity_logs` 14 değerli enum | `coremusic_logs.sql` s.416–431 | ✅ disk kanıtı |
| `music_similar` puan/algoritma/UNIQUE | `coremusic_musics.sql` s.221–234 | ✅ disk kanıtı |
| `device_type` tutarlılığı | s.424 ↔ library s.92 (çapraz okuma) | ✅ çapraz kanıt |
| `music.played` girdisi | `MusicPlayedEvent.php` s.20 · s.39 + master s.93 | ✅ disk kanıtı |
| DiscoveryQuery aggregate | `domain-master.md` satır 93 | ⚠️ TASARIM İDDİASI |
| Repository/VO/invariant | Bu dosya §7–§10, §13 | 🔶 TASARIM ÖNERİSİ |

## §21 TUR KAYDI (2026-10-17)

| # | Öğe | Değer |
|---|---|---|
| 1 | Eklenen bölümler | §5–§21 |
| 2 | Invariant adayı | D-P1…D-P12 |
| 3 | VO adayı | V-D1…V-D6 |
| 4 | Olay adayı | E-D1…E-D4 |
| 5 | Açık soru | R-D1…R-D10 |
| 6 | En kritik bulgu | `device_type` enum 6 tutarlılığı ✅ + `entity_type` iki dilli (R-D7) + benzerlik↔öneri sınırı (R-D8) |

## §22 TERİM SÖZLÜĞÜ (BU DALIN UBİQUITOUS DİLİ)

| Terim | Şema karşılığı | Kanıt |
|---|---|---|
| Arama | `search_logs` satırı | s.398–414 |
| Arama tipi | `search_type` enum 7 | s.402 |
| Seçim | `selected_result_id` + `_position` | s.404–405 |
| Yanıt süresi | `response_time_ms` | s.406 |
| Aktivite | `user_activity_logs` satırı | s.416–431 |
| Aktivite tipi | `activity_type` enum 14 | s.419 |
| Bağlam verisi | `metadata` json | s.422 |
| Benzerlik | `music_similar` satırı | s.221–234 |
| Benzerlik puanı | `similarity_score` decimal(3,2) | s.225 |
| Benzerlik algoritması | `algorithm` enum 3 | s.226 |

## §23 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

### §23.1 Arama akışı

`RM-D1 search` (V-D1/V-D2 doğrulama) → FULLTEXT sorgusu (s.413) → sayfa döner
→ `RM-D2 logSearch` (anonim serbest — D-P1) → `search.performed` olayı **yok**
(E-D1). `response_time_ms` (s.406) ölçülür — ölçüm noktası kanıtsız (R-D4).

### §23.2 Seçim akışı

Kullanıcı sonuç seçer → `RM-D3 recordSelection` → `selected_result_id` yazılır
(s.404) — hedef doğrulanmaz (D-P3) → `search.result_selected` olayı **yok**
(E-D2) → `user_activity_logs`'a `activity_type='search'` ikinci kaydı (R-D6
çift yol).

### §23.3 Benzerlik yeniden üretimi

`RM-D6 rebuildSimilarity` (V-D6) → `music_similar` yeniden yazımı → puan
0.00–1.00 domain kontrolü (V-D5 — DB retmez, D-P7) → `similar.updated` olayı
**yok** (E-D3) → recommendation tarafı bu tabloyu okur (R-D8).

## §24 SONRAKİ TUR İŞ KALEMLERİ

1. R-D6 (çift arama kayıt yolu) — birleştirme kararı.
2. R-D7 (`entity_type` kapalı küme?) — domain dili.
3. R-D8 (benzerlik ↔ öneri sınırı) — ADR-097 K4.
4. R-D5 (14 aktivite ↔ 10 event eşlemesi) — çapraz okuma.
5. §13 senaryolarının `test-writer` kapsamına devri.

## §25 TUR SONU KONTROL LİSTESİ

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Authority eski satır | ✅ `2026-10-15` aynen duruyor |
| 2 | Yeni Authority | ✅ `2026-10-17` append |
| 3 | Frontmatter | ✅ korundu |
| 4 | Bölüm silme | ❌ YOK — §1–§4 duruyor, §5–§25 append |
| 5 | Yer tutucu | 0 |
| 6 | Uydurma satır numarası | 0 — tüm `s.NN` okunan SQL'den |
| 7 | Çapraz doğrulama | `device_type` enum 6 = library s.92 ile aynı küme ✅ |
| 8 | Ölçüm | `ReadAllLines().Length` |

## §26 ÇAPRAZ REFERANS VE BAĞIMLILIK MATRİSİ

| # | Bu dosya referansı | Hedef | İlişki |
|---|---|---|---|
| 1 | §6.2 `search_type` enum 7 | RM-D1/RM-D2 · V-D1 | Arama dili |
| 2 | §6.3 `activity_type` enum 14 | RM-D4 · V-D4 · R-D5 | Aktivite dili |
| 3 | §6.4 `similarity_score` decimal(3,2) | D-P7 · V-D5 | Puan sınırı — domain'de |
| 4 | §6.4 `algorithm` enum 3 | V-D6 · R-D8 | Benzerlik ↔ öneri ortak dili |
| 5 | §7.1 `device_type` tutarlılığı | library s.92 (çapraz) | ✅ aynı küme — domain dili hizalı |
| 6 | §9.1 `music.played` girdisi | playback §9.1 · music §9.1 | Olay sahipliği D3 çatışması |
| 7 | §6.2 FULLTEXT (s.413) | RM-D1 | Tam metin arama yolu |
| 8 | master §3.1 satır 93 | `DiscoveryQuery` adayı | ⚠️ ADR-097 K4 |
| 9 | coremusic_ai.sql s.79–98 | `recommendation_history` | R-D8 sınır kanıtı |
| 10 | `domain-bilesenler.md` §14 | E-D1…E-D4 | Olay→BC çizelgesi |

> **Bu dosyanın doğruluk sözü:** §6/§7/§20'deki her `s.NN` okunan SQL'den gelir;
> §7/§8/§10/§13/§23'teki invariant/VO/olay/akış listeleri **öneridir** ve
> ADR-097 K4'e bağlıdır.

## §27 KOMŞU TABLO DETAYI (recommendation — sınır çizgisi kanıtı)

> **Kaynak:** `.ai/.sql/mysql/coremusic_ai.sql` (R-D8 sınır çatışması kanıtı).
> `recommendation_history` bu dalın **dışındadır**; benzerlik↔öneri sınırını
> kanıt olarak sunar.

| Kolon | Satır (coremusic_ai.sql) | Tip / değer | Sınır notu |
|---|---|---|---|
| `algorithm` | s.96 | CHECK 4 değer | `music_similar.algorithm` (s.226, enum 3) ile **farklı küme** |
| `feedback` | s.97 | CHECK 5 değer | Öneri geri bildirimi — keşifte YOK |
| `confidence` | s.85 | decimal | Benzerlikte `similarity_score` (s.225) — iki ayrı puan dili |
| `position` | s.87 | tinyint | Sıralı liste — benzerlikte sıra YOK |
| `user_id` | s.81 | int unsigned | ⚠️ `binary(16)` UUID diliyle **ÇELİŞKİLİ** — bkz §27.1 |

### §27.1 Kritik bulgu: `recommendation_history.user_id` tip çelişkisi

| İddia | Kanıt | Durum |
|---|---|---|
| CoreMusic kimlik dili `binary(16)` UUID v7 | `music_similar.music_id` (s.223) · `search_logs.user_id` (s.400) · `user_activity_logs.user_id` (s.418) · library/playback/device (hepsi binary(16)) | ✅ tutarlı dil |
| `recommendation_history.user_id` `int unsigned` | `coremusic_ai.sql` s.81 | ⚠️ **ÇELİŞKİLİ** — otomatik artan int |

> **Bulgu (2026-10-17):** `recommendation_history.user_id` (int unsigned) ile
> geri kalan sistemin `binary(16)` UUID kimlik dili uyuşmuyor. Bu, izole bir
> şema hatası mı, eski bir tablo mu, bilinçli bir ayrım mı — **UNKNOWN**.
> Sahipli karar: `recommendation-kapsam.md` §12 ve K007-data.

## §28 ALGORİTMA DİLİ KARŞILAŞTIRMASI (çapraz tablo)

| Tablo | Kolon | Küme | Satır |
|---|---|---|---|
| `music_similar` (keşif) | `algorithm` enum **3** | collaborative · content_based · hybrid | coremusic_musics.sql s.226 |
| `recommendation_history` (öneri) | `algorithm` CHECK **4** | (değerler §27 tablosunda — çekirdeği okundu, 4. değer için ⚠️ VERIFICATION REQUIRED) | coremusic_ai.sql s.96 |
| `user_activity_logs` (keşif) | `activity_type` enum **14** | login…playlist_remove | coremusic_logs.sql s.419 |
| `search_logs` (keşif) | `search_type` enum **7** | music…video | coremusic_logs.sql s.402 |

> **Dil birleştirme uyarısı:** `algorithm` iki tabloda iki farklı küme (3 vs 4).
> Domain dili tekilleştirilirse hangi küme kazanır — ADR-097 K4 kapsamı;
> `recommendation_history`'deki 4. CHECK değeri bu turda doğrulanmadı → ⚠️
> VERIFICATION REQUIRED (uydurma yazılmaz).

### §28.1 Çapraz doğrulama özeti

| Kontrol | Sonuç |
|---|---|
| `device_type` enum 6 → library s.92 ile aynı | ✅ TUTARLI |
| `algorithm` enum 3 ↔ CHECK 4 | ⚠️ ÇELİŞKİLİ — §28 |
| Kimlik dili `binary(16)` ↔ `int unsigned` (s.81) | ⚠️ ÇELİŞKİLİ — §27.1 |
| `entity_type` serbest varchar vs enum'lar | ⚠️ İKİ DİL — R-D7 |

## §29 DİZİN VE SORGU YOLU ÇİZELGESİ (KANIT)

| Tablo | Dizin | Satır | Sorgu yolu |
|---|---|---|---|
| `search_logs` | `idx_search_logs_user_id` | s.409 | Kullanıcı bazlı arama geçmişi |
| `search_logs` | `idx_search_logs_query` | s.410 | Birebir sorgu eşleşmesi |
| `search_logs` | `idx_search_logs_type` | s.411 | Tip filtresi |
| `search_logs` | `idx_search_logs_created_at` | s.412 | Zaman serisi (trend analizi) |
| `search_logs` | `ft_search_logs_query` FULLTEXT | s.413 | Tam metin sorgusu |
| `user_activity_logs` | `idx_user_activity_user_id` | s.427 | Kullanıcı aktiviteleri |
| `user_activity_logs` | `idx_user_activity_type` | s.428 | Tip filtresi |
| `user_activity_logs` | `idx_user_activity_created_at` | s.429 | Zaman serisi |
| `user_activity_logs` | `idx_user_activity_user_type` | s.430 | Kullanıcı+tip çifti |
| `music_similar` | `idx_music_similar_unique` | s.229 | UNIQUE çift |
| `music_similar` | `idx_music_similar_target` | s.230 | Ters yön (benzer olanı bul) |
| `music_similar` | `idx_music_similar_score` | s.231 | Puan sıralaması (top-N) |

> **N+1 notu:** `similarTo` (RM-D5) top-N sorgusu tek dizinle (s.231) karşılanır;
> parça metası ayrı sorgu ister (komşu music JOIN'i) — N+1 dikkat (§18 notu
> ile aynı sınıf).

### §29.1 `SELECT *` ve prepared statement denetimi

| Kural | Bu dal uygulaması |
|---|---|
| `SELECT *` yasak | RM-D1…RM-D6 önerileri **kolon listesi** ile kurulmalı (Data Engineer kuralı) |
| Parametreli sorgu zorunlu | `search_query` (s.401, kullanıcı girdisi) → prepared statement **zorunlu** (SQL injection yüzeyi) |
| FULLTEXT parametresi | s.413 sorgusu `MATCH...AGAINST` — parametre bağlanabilir, sabit metin değil |

## §30 SONRAKİ TUR DEVİR KARTI (özet)

| # | Devredilen iş | Hedef dosya | Öncelik |
|---|---|---|---|
| 1 | `recommendation_history.user_id` int ↔ binary(16) çelişkisi (§27.1) | [[recommendation-kapsam]] §12 + K007-data | YÜKSEK |
| 2 | `algorithm` enum 3 ↔ CHECK 4 (§28) | [[recommendation-kapsam]] + ADR-097 K4 | YÜKSEK |
| 3 | Benzerlik ↔ öneri bağlam sınırı (R-D8) | ADR-097 K4 | ORTA |
| 4 | Çift arama kayıt yolu (R-D6) | Backend taraması | ORTA |
| 5 | 14 aktivite ↔ 10 event eşlemesi (R-D5) | [[domain-bilesenler]] §14 | ORTA |
| 6 | `entity_type` kapalı küme kararı (R-D7) | Domain dili ADR | DÜŞÜK |
| 7 | Anonim arama gizlilik onayı (R-D10) | Security Engineer | DÜŞÜK |

> **Tur durumu (2026-10-17):** bu dosya §5–§30 ile derinleştirildi; `status: draft`
> değişmedi, §1–§4 aynen korundu, tüm ekler append-only yapıldı (eklenen bölüm: §5–§30).

---

**Kardeş dosyalar:** [[discovery-dogrulama]] · [[discovery-web-arastirma]] · [[discovery-ornekler]] · [[discovery-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · 2026-10-17 (K008-domain derinleştirme — §5–§30 eklendi; search/activity/similar şema kanıtı derinleştirildi, device_type tutarlılığı doğrulandı, recommendation.user_id tip çelişkisi kaydedildi)
