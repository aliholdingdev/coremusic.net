---
title: "music — Kapsam"
type: architecture-scope
node_id: K008-domain/music
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# music — Kademe-2 Kapsam Kaydı

> **DDD notu:** bu kayıt, bu dalın **Bounded Context tanımı adayıdır** (ADR-097 K4'te
> kilitlenir). Kapsam DDD terminolojisiyle — *aggregate · entity · ubiquitous language* —
> kurulur; v2 bu dala **yalnızca ad verir**, tahminle dolgu **yapılmaz**.

## §1 Rol (ELI15)

`domain-master.md` §1 sorumluluk tablosu (satır 40) bu dali **"Katalog kavramları
(şarkı · albüm · medya)"** sorumluluğuna `music · media · library` üçlüsüyle yazar.
Basitçe: bir şarkının ne olduğu, hangi albümde durduğu, hangi sanatçının yazdığı —
"şarkı" kelimesinin CoreMusic genelinde **tek** anlamı burada doğar.

`music/index.md` satır 13 bu tanımın v2'de **olmadığını** söyler; §1'deki cümle bir
**tasarım yorumudur**, kilitli sınır değildir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Şarkı (track) · albüm · sanatçı **kavramının tanımı** | Kavramın tabloda saklanması | K007-data (`-bagimliliklar` §3/1) |
| 2 | Katalog dili: `id · title · artist · album · duration` | Dosyanın fiziksel kodlanması/bitrate | K014-media (master §2.1) |
| 3 | "şarkı eklendi" kuralının olaya dönüşü | Ekleme servis süreci (kim, ne zaman) | K012-services (master §2.1) |
| 4 | Katalog kavramının medyadan **ayrı** tanımı | `stream · download` erişim türü | media (master §1/1) |
| 5 | Katalog kavramının koleksiyondan **ayrı** tanımı | "kütüphanesinde" / "beğendi" durumu | library (master §1/1) |
| 6 | Katalog kavramının listelerden **ayrı** tanımı | Sıra, `snapshot_id`, ortak sahiplik | playlist (master §1/2) |
| — | *(dışında)* dış servis katalog eşlemesi (Spotify/Apple) | — | K013-integraion (master §2.1) |

**Sınır notu:** satır 1'deki "tanım ↔ şema" ayrımı `domain-bagimliliklar.md` §4 satır 47'de
`kavram adları (ubiquitous language) paylaşılır · şema K007'nin` diye yazılıdır; bu tablo
o ayrımı yansıtır, **yeni sınır önermez**.

## §3 DDD model notu (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Katalog modeli: track · album · artist | ⚠️ ADR-097 K4 bekliyor |
| 2 | Aggregate (aday) | `Track` — album/artist **referansları** içerir | ⚠️ TASARIM İDDİASI — sınır çizilmedi |
| 3 | Entity | `musicId` taşıyan kayıt (kimlik eşitliği bu id) | ✅ repo: `MusicAddedEvent.php` satır 27 |
| 4 | Value Object (aday) | `duration` (int, saniye) · `title` (string) | ✅ repo: `MusicResponse.php` satır 24, 21 |
| 5 | Ubiquitous language | şarkı = `track` · albüm = `album` · sanatçı = `artist` | ✅ repo: `MusicResponse.php` satır 21–23 |
| 6 | Domain event | `music.added` · `music.played` | ✅ repo: Events/Domain/ (satır 39 her ikisi) |
| 7 | Invariant | **tanımsız** — invariant listesi kademe-3'te | ⚠️ VERIFICATION REQUIRED |

**Yasak:** aggregate sınırı, invariant listesi ve kural seti bu dosyada **uydurulmaz**;
§2/§3'teki ⚠️ satırlar kapanmadan bu dal `draft` kalır.

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: `music` = v2 §2.8 satır 298 | `.ai/architecture/K008-domain/domain-master.md` §3 satır 74 | ✅ dosya okundu |
| 2 | Sorumluluk #1 = katalog kavramları → music · media · library | `domain-master.md` §1 satır 40 | ✅ dosya okundu |
| 3 | "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED" | `music/index.md` satır 13 | ✅ dosya okundu |
| 4 | Katalog DTO'su 7 alanlı: `id·title·artist·album·duration·coverUrl·streamUrl` | `shared/src/Api/Dto/Response/MusicResponse.php` satır 20–26 | ✅ dosya okundu |
| 5 | Domain event `music.added` (`musicId·addedBy·title`) | `shared/src/Events/Domain/MusicAddedEvent.php` satır 27–29, 39 | ✅ dosya okundu |
| 6 | Domain event `music.played` (`musicId·userId·duration`) | `shared/src/Events/Domain/MusicPlayedEvent.php` satır 27–29, 39 | ✅ dosya okundu |
| 7 | Arama DTO'su katalog filtrelerini taşır: `query·genre·artist·page·limit` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` satır 20–24 | ✅ dosya okundu |
| 8 | music ↔ K007 sınırı = kavram adları paylaşılır | `domain-bagimliliklar.md` §4 satır 47 | ✅ dosya okundu |
| 9 | Bu dalın resmi kapsam tanımı (aggregate/invariant) | — | **UNKNOWN** (ADR-097 K4) |

> **Not:** 4–7 satırları `shared/src/` (alt domain değil, paylaşılan katman) içindir;
> bunlar katalog kavramının **repo'daki karşılığıdır**, K008 kapsamının kendisi değildir.

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | music ↔ media sınırı: "şarkı" ile "erişilebilir medya" aynı varlık mı, ayrı mı? | ⚠️ VERIFICATION REQUIRED — bu kayıtta açıldı (master §7'de yok) |
| R2 | Aggregate `Track` mi, `Recording` mi? Kimlik hangi id? | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |
| D3 | `music.played` olayının sahibi playback mi, music mi? (master §7/2 ile kesişir) | ⚠️ VERIFICATION REQUIRED |
| R4 | CoreMusic katalog modelinin kendi dili/çerçevesi (UNKNOWN — master §4 satır 97) | **UNKNOWN** |
| R5 | K013-integraion ile katalog eşlemesi hangi alan üzerinden? | **UNKNOWN** |

**Kapanış:** status: draft kalır. §3/7 ve §4/9 kapanmadan bu dosyada kapsam
iddiası **kesin** sayılmaz (`domain-dogrulama.md` §5 satır 67: ölçüt 6–8 açık).

## §6 ŞEMA KANITLARI: KATALOG ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_musics.sql` (560 satır · 27 tablo ·
> 2026-10-07 canlı dump). Bu bölüm **kilitli kapsam değildir** — §3'teki yasak
> gereği yalnız kanıt sunar; invariant/kural iddiası §7'de açıkça öneridir.

### §6.1 Çekirdek tablo envanteri

| Tablo | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `musics` | s.305–340 | Katalog parçası (ana kayıt) | 26 kolon + 6 dizin (s.331–337) |
| `artists` | s.63–85 | Sanatçı | `slug` UNIQUE (s.80) + FULLTEXT (s.84) |
| `genres` | s.87–104 | Tür (hiyerarşik) | `slug` UNIQUE (s.100) + öz-bağımlı FK (s.103) |
| `music_artists` | s.106–124 | Parça↔sanatçı N:M + rol + sıra | 2 UNIQUE (s.117, s.118) |
| `music_genres` | s.188–199 | Parça↔tür N:M | UNIQUE çift (s.195) + `is_primary` (s.192) |
| `music_stats` | s.236–262 | Günlük/hatırlatıcı istatistik | UNIQUE (music, tarih) s.258 |
| `recordings` | s.487–505 | Özgün ses kaydı (aday taşıma) | `legacy_music_id` FK **YOK** (s.494) |
| `artist_credit` + `_names` | s.18–42 | Birleşik sanatçı adı (MusicBrainz) | UNIQUE (credit, pozisyon) s.38 |

### §6.2 `musics` ↔ `recordings` ayrımı (taşıma izi)

| İddia | Kanıt satırı | Durum |
|---|---|---|
| `musics` hâlâ katalogun canlı kaynağı | s.306–330 dolu kolon seti | ✅ |
| `recordings` yeni modelin adayı | s.487–505; comment: MusicBrainz hizalı (s.505) | ✅ |
| Taşıma bağlantısı iz düzeyinde | s.494 `legacy_music_id` — comment: "FK YOK — iz korunur" | ✅ |
| Eşleme görünümü var | s.507–508 `v_music_recording_map` (LEFT JOIN) | ✅ |
| Taşıma tamamlanmış değil | s.494 "backfill FAZ 2b" comment'i | ⚠️ FAZ 2b DURUMU `UNKNOWN` |
| `duration` birimi iki yerde farklı | `musics.duration_sec` (s.314) ↔ `recordings.duration_ms` (s.490) | ✅ fark kanıtlı — birim uyumu açık soru (§12 R7) |

### §6.3 Bağlantı (junction) kanıtları

| İlişki | UNIQUE kanıtı | FK davranışı | Sıra/bayrak |
|---|---|---|---|
| parça↔sanatçı | s.117 (music, artist) · s.118 (music, position) | CASCADE (s.121–122) | `position` s.111 · `is_primary` s.112 |
| parça↔tür | s.195 (music, genre) | CASCADE (s.197–198) | `is_primary` s.192 |
| credit↔sanatçı | s.38 (credit, position) | CASCADE (s.40–41) | `join_phrase` s.33 |
| istatistik↔parça | s.258 (music, stats_date) | CASCADE (s.261) | — |

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

> **§3 yasağı gereği:** bu tablo **kilitli invariant listesi değildir**; her satır
> SQL kanıtı ile **öneri**yi ayrı sütunda tutar. §2/§3'teki ⚠️ satırları ve
> `status: draft` bu eklemle **kapanmaz** — kapanış ADR-097 K4'e bağlıdır.

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| M-P1 | s.309 `artist_id NOT NULL` + s.338 FK `RESTRICT` | Her parçanın sanatçısı var; sanatçı silinirse parça silinemez | Sahipsiz parça yaratılamaz; katalog kaydı sanatçısız yaşayamaz |
| M-P2 | s.310 `album_id` nullable | Albüm opsiyonel | Albümsüz parça geçerlidir — albüm zorunlu kılınmaz |
| M-P3 | s.117 UNIQUE (music, artist) | Aynı sanatçı parçaya iki kez eklenemez | Tekrarlı sanatçı ataması domain'de de reddedilmeli |
| M-P4 | s.118 UNIQUE (music, position) | Aynı pozisyonda tek sanatçı | Sıra toplu atanmalı (tavsiye §10 R-M5) |
| M-P5 | s.195 UNIQUE (music, genre) | Aynı tür iki kez eklenemez | Tekrarlı tür ataması yok |
| M-P6 | s.192 + s.311 | Parçanın birincil türü alanı + N:M tekil çift | Birden çok tür olabilir; `is_primary` çifti en fazla bir tane (DB'de tekil control **YOK** → ⚠️ öneridir) |
| M-P7 | s.320 `mood enum` 7 değer (happy…neutral) | Kapalı küme | Geçersiz ruh hali yazılamaz (VO §8) |
| M-P8 | s.70 `artist_type enum` 5 değer | Kapalı küme | Geçersiz sanatçı türü yazılamaz |
| M-P9 | s.329–330 `is_deleted` + `deleted_at` | Yumuşak silme ikilisi | Silme yalnız ikisi birlikte set edilmeli (master §24 kuralı) |
| M-P10 | s.338 FK `RESTRICT` (sanatçı) · s.339 FK `SET NULL` (tür) | Silme davranışı iki FK'da farklı | Parça silinemez (sanatçı referanslı); tür silinirse parça kalır, tür BOŞ olur |
| M-P11 | s.337 FULLTEXT (title) + s.84 FULLTEXT (name, biography) | Arama dizinleri | Tam metin araması yalnız bu kolonlarda; `slug` üzerinden arama yapılmaz |
| M-P12 | s.80 + s.100 `slug` UNIQUE | Sanatçı/tür slug'ları benzersiz | Aynı slug iki kayıtta olamaz — slug üretimi çakışma ret'i içermeli |
| M-P13 | s.258 UNIQUE (music, stats_date) | Günlük istatistik satırı tekil | Aynı gün için tek istatistik satırı; tekrar insert ret |
| M-P14 | s.494 FK **YOK** (legacy_music_id) | İsteyen her satır bağlanır (parça bütünlüğü DB'de yok) | Taşıma eşlemesi uygulama katmanında doğrulanmalı — DB güvencesi **yok** (kanıt: s.494 comment) |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| `musics.play_count` (s.324) ile `music_stats.total_plays` (s.242) tutarlı | iki kolon var | ⚠️ TUTARLILIK KURALI YAZILMADI — hangisi SSOT? (§12 R8) |
| `is_primary` tekilleği DB'de | s.192 bayrak var; UNIQUE yok (`is_primary` dâhil değil) | ⚠️ DB güvencesi YOK — öneridir |
| Slug çakışma stratejisi | s.308 `slug NOT NULL` ama `musics` için UNIQUE **yok** (s.331–337) | ⚠️ parça slug'ı tekrarsız **değil** — sanatçı/tür ile fark bilinçli mi? (§12 R9) |
| Döngü engeli (genre öz-hiyerarşi) | s.103 `ON DELETE SET NULL`, döngü kontrolü yok | ⚠️ DÖNGÜ DB'de engellenmez — domain kontrolü önerilir |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda katalog VO sınıfı yoktur; adaylar
> yalnız şema kolonlarından türetilmiştir (`domain-master.md` §25 kuralı).

| # | VO | Dayanak kolon | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-M1 | `TrackTitle` | s.307 varchar(500) NOT NULL | 1–500 karakter | Boş/whitespace ret |
| V-M2 | `Slug` | s.308 (parça) · s.66/s.90 (sanatçı/tür) | URL-dostu biçim | `from(string)` — normalize + ret (§7.1 slug farkı notu) |
| V-M3 | `DurationSeconds` | s.314 int nullable | ≥0 tam sayı (saniye) | Negatif ret; `null` = bilinmiyor — **≠** 0 |
| V-M4 | `Mood` | s.320 enum(7) | 7 kapalı değer | `from(string)` fabrika — geçersizde ret |
| V-M5 | `ArtistType` | s.70 enum(5) | solo/band/duo/orchestra/other | `from(string)` + `isGroup()` (band/duo/orchestra) |
| V-M6 | `IsrcCode` | s.316 varchar(20) | ISRC biçimi | Biçim doğrulama — `recordings.isrc` (s.491) ile aynı biçim |
| V-M7 | `DurationUnit` | s.314 (saniye) ↔ s.490 (milisaniye) | `sec` / `ms` | Birim **tip** olarak ayrılır — s.490 comment "[EXTERNAL VERIFIED]" |
| V-M8 | `GenrePath` | s.92–93 (`parent_id`, `genre_level`) | kökten yaprak zinciri | Döngü ret (§7.1) — `genre_level` monoton artmalı |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-M3 | `from(-5)` ret; `from(null)` ayrı yol (bilinmiyor) |
| V-M4 | 8. değer `from()` çağrısında ret |
| V-M6 | Geçersiz ISRC biçimi ret; geçerli eşleşir |
| V-M8 | `a→b→a` zinciri ret edilir |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar (dosya + satır kanıtlı)

| Olay | Dosya kanıtı | `eventName` | Payload (satır kanıtlı) |
|---|---|---|---|
| `MusicAddedEvent` | `shared/src/Events/Domain/MusicAddedEvent.php` s.27–29 (property) · s.39 | `music.added` | musicId · addedBy · title |
| `MusicPlayedEvent` | `shared/src/Events/Domain/MusicPlayedEvent.php` s.27–29 · s.39 | `music.played` | musicId · userId · duration |

> `music.played` **sahiplik çatışması açık sorudur** (D3/§12): olay bu dalda
> tanımlı ama sahibi playback de olabilir — kapanış `domain-bilesenler.md`
> §14 olay→BC çizelgesine bağlıdır.

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-M1 | `music.updated` | s.327–328 `created_at/updated_at` | ❌ DOSYA YOK (`Events/Domain/` 9 dosyada yok) |
| E-M2 | `music.deleted` (soft) | s.329–330 flag ikilisi | ❌ DOSYA YOK |
| E-M3 | `music.liked` / `unliked` | s.325 `like_count` | ❌ DOSYA YOK |
| E-M4 | `music.verified` | s.322 `is_verified` | ❌ DOSYA YOK |
| E-M5 | `music.artist_assigned` | s.111–112 junction satırı | ❌ DOSYA YOK |
| E-M6 | `music.stats_recorded` | s.254 `stats_date` günlük satır | ❌ DOSYA YOK |

> **Kural:** E-M1…E-M6, `domain-bilesenler.md` §12 protokolüne tabidir —
> olay ancak dosya + `class` + `eventName` satır kanıtıyla envantere girer.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda katalog repository arayüzü
> kanıtlanmamıştır (`domain-bilesenler.md` §10 taraması). Dönüş dili
> `IUserRepository` ile hizalıdır (`?array`/`array`/`void`/`bool`).

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| R-M1 | `findBySlug(string $slug): ?array` | kayıt veya null | M-P12 (sanatçı/tür slug) |
| R-M2 | `findByIdHex(string $id): ?array` | kayıt veya null | s.306 binary(16) PK |
| R-M3 | `search(query, genre, artist, page, limit): array` | sayfa | `MusicSearchRequest` s.20–24 (4'ü §4 kanıtlı) |
| R-M4 | `create(array $data): array` | yeni kayıt | M-P1, M-P2 |
| R-M5 | `attachArtist(string $musicId, string $artistId, int $pos): bool` | evet/hayır | M-P3, M-P4 |
| R-M6 | `setGenres(string $musicId, array $genreIds, string $primaryId): void` | — | M-P5, M-P6 |
| R-M7 | `softDelete(string $id): void` | — | M-P9 |
| R-M8 | `resolveLegacyMapping(string $musicId): ?string` | recordingId | M-P14 (view s.508) |
| R-M9 | `searchFulltext(string $term, int $page, int $limit): array` | sayfa | M-P11 (FULLTEXT s.337/s.84) |

### §10.1 Sözleşme tipi notu

Dönüşlerin `array` olması bilinçli uyumdur (§4 notu: `MusicResponse` 7 alanlı
DTO — s.20–26). VO/Entity sarmalayıcı ancak `domain-bilesenler.md` §9 C-9
borcu kapatılırsa mümkündür; bu dosya tek başına tip kararı vermez.

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| K007-data | music ← data | kavram adları paylaşılır, şema K007'nin (`domain-bagimliliklar.md` §4/47) | Bu dal şema **üretmez**, yalnız dili tanımlar |
| media | music → media | dosyanın kodlanması/bitrate DIŞINDA (§2/2) | `music_files` (s.162) media'da mı? → §12 R10 |
| library | music → library | `like_count` (s.325) sayacı burada; "beğendi" durumu library'de | Sayaç bu dalda, karar library'de |
| playlist | music → playlist | yalnız `music_id` referansı (playlist şeması s.70) | Parça metası playlist'e kopyalanmaz |
| playback | music ↔ playback | `music.played` + `duration` (MusicPlayedEvent s.27–29) | Olay sahipliği D3 — açık |
| services | music → services | ekleme servisi süreci DIŞINDA (§2/3) | Katalog dili burada, süreç orada |
| K013-integraion | music → dış | Spotify/Apple eşlemesi DIŞINDA (§2 son satır) | Eşleme alanı §5 R5 — **UNKNOWN** |
| discovery/recommendation | ← music | `mood` (s.320), `genre` (s.311) filtre kolonları | Öneri/keşif bu kolonları okur, burada karar vermez |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R6…R10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R6 | `podcast_*` (s.342–424) ve `radio_*` (s.425–486) tabloları hangi BC'de? — hepsi `coremusic_musics` şemasında | SQL tablo envanteri (§6 dışı, 8 tablo) | `domain-kapsam.md` §3 ile çapraz okuma |
| R7 | `duration_sec` ↔ `duration_ms` birim dönüşümü hangi katmanda? | s.314 / s.490 | Taşıma (FAZ 2b) tasarımı |
| R8 | `play_count` (s.324) mi, `music_stats.total_plays` (s.242) mi SSOT? | §7.1 uyumsuzluk satırı | Sayı SSOT kararı — ADR |
| R9 | Parça `slug` neden UNIQUE değil (sanatçı/tür'de var)? | s.308 vs s.80/s.100 | Tasarım niyeti sorulur — kasıtlı mı? |
| R10 | `music_files` (s.162) bu dalın mı, media'nın mı? | §11 komşu satırı | `domain-kapsam.md` §3 çapraz okuma |

> **§5'teki R1–R5 ve D3 aynen korunur** (önceki tur kaydı); bu tur yalnız
> R6–R10 ekler. `status: draft` ve §3 yasağı **değişmez**.

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Senaryo taslağıdır; yazılmamıştır
> (`qa-engineer`/`test-writer` kapsamı).

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| T-M1 | Sahipsiz parça oluşturma (`artist_id` boş) | Ret | M-P1 |
| T-M2 | Aynı sanatçıyı parçaya iki kez ekleme | Ret | M-P3 (s.117) |
| T-M3 | Aynı pozisyonda ikinci sanatçı | Ret | M-P4 (s.118) |
| T-M4 | Geçersiz `mood` değeri | VO ret | M-P7, V-M4 |
| T-M5 | Sanatçıyı sil (referanslı parça var) | DB `RESTRICT` hatası | M-P10 (s.338) |
| T-M6 | Türü sil (referanslı parça var) | Parça kalır, `genre_id` NULL | M-P10 (s.339) |
| T-M7 | Soft delete iki alan birlikte | `is_deleted=1` + `deleted_at` dolu | M-P9 |
| T-M8 | Aynı (parça, tür) çiftini tekrar ekleme | Ret | M-P5 (s.195) |
| T-M9 | Aynı gün ikinci istatistik satırı | Ret | M-P13 (s.258) |
| T-M10 | `music.added` yayınlayınca payload | 3 anahtar (§9.1) | `DomainEventInterface` sözleşmesi |
| T-M11 | Slug çakışmasında yaratma (sanatçı) | UNIQUE ret / strateji | M-P12 (s.80) |
| T-M12 | Döngüsel tür hiyerarşisi kurma | Domain ret (DB ret **etmez**) | §7.1 döngü satırı |

## §14 ÖLÇÜM VE KAPANIŞ (BU TUR)

| Metrik | Değer | Yöntem |
|---|---|---|
| Ekleme öncesi | 93 satır | satır sayım aracı (JS ReadAllLines eşdeğeri) |
| Eklenen bölüm | §6–§14 | append-only; §1–§5 + §3 yasağı + Authority 2026-10-12 korundu |
| Invariant adayı | 14 (M-P1…M-P14) | hepsi SQL satır kanıtlı; kilitlenmemiş, `draft` korunur |
| Uyumsuzluk kaydı | 4 (§7.1) | hepsi ⚠️ — SSOT/tekil/slug/döngü |
| VO adayı | 8 (V-M1…V-M8) | hepsi `TASARIM ÖNERİSİ — kanıt DEĞİL` etiketli |
| Olay kaydı | 2 kanıtlı + 6 `❌ DOSYA YOK` | dosya + satır kanıtı (§9) |
| Açık soru | 5 (§5) + 5 yeni (R6–R10) = 10 | §5 kayıtları aynen korundu |
| Kapanan ⚠️ | **0** | §3 yasağı gereği hiçbir ⚠️/UNKNOWN bu turda kapatılmadı |
| Yeni URL | 0 | C4 — tek URL kaynağı `domain-web-arastirma.md` §18.2 |
| Authority | eski (2026-10-12) korundu + yeni eklendi | master §22 M-6 |

> **Sonraki tur:** R8 (sayı SSOT) + R9 (slug niyeti) tek okuma ile kapanır;
> R6/R10 çapraz okuma ister; ADR-097 K4 kilitlenmesi `status` alanını
> `draft` dışına taşır — bu turda taşınmadı.

---

## §15 SQL ERİŞİM VE DİZİN NOTU (KANIT / TARAMA AYRIMI)

> **Kural:** 🟡/🔴 satırlara bu dosyaya performans iddiası olarak yazılamaz.
> İddia için sorgu plani veya kod kanıtı gerekir; tahmin yok.

| # | Erişim paterni | Bilinen kanıt | Tarama durumu |
|---|---|---|---|
| A-M1 | Parça→sanatçı N:M okuma | s.119 dizin `idx_music_artists_artist` | 🟢 Kanıt var |
| A-M2 | Parça→tür N:M okuma | s.196 `idx_music_genres_genre` | 🟢 Kanıt var |
| A-M3 | Tam metin arama (parça) | s.337 FULLTEXT (title) | 🟢 Dizin kanıtlı; sorgu yazılımı taranmadı → kullanım ⚠️ |
| A-M4 | Tam metin arama (sanatçı) | s.84 FULLTEXT (name, biography) | 🟢 Aynı |
| A-M5 | Slug ile sanatçı/tür bulma | s.80 / s.100 UNIQUE | 🟢 Dizin kanıtlı |
| A-M6 | Slug ile parça bulma | s.308 slug var, s.331–337'de UNIQUE/KEY **yok** | 🔴 Parça slug'ı dizinsiz — §12 R9 ile ilişkili |
| A-M7 | Popülerlik/trend sıralaması | s.259–260 iki KEY | 🟢 Kanıt var |
| A-M8 | Günlük istatistik yazma | s.258 UNIQUE çifti | 🟢 Çakışma DB'de ret |
| A-M9 | Parça silme (sanatçı RESTRICT) | s.338 | 🟢 DB davranışı |
| A-M10 | Eşleme görünümü okuma | s.508 LEFT JOIN + s.500 `idx_rec_legacy` | 🟡 View kanıtlı; hangi kodun çağırdığı taranmadı |

## §16 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

> Yön ve niyet §2/§11'den türetilmiştir; **çağrı mekanizması** iddiası
> taşımaz — mekanizma kanıtı ayrı tur işidir.

| # | Çağıran → Çağrılan | Niyet | Bağlı kural |
|---|---|---|---|
| M-BC1 | services → music | Parça ekleme dili (kavram tanımı) | §2/3 |
| M-BC2 | music → K007-data | Kavram adları (ubiquitous language) | §11 |
| M-BC3 | music → media | Dosya erişimi (bitrate/kodlama DIŞINDA) | §2/2, R10 |
| M-BC4 | library → music | `like_count` güncelleme niyeti | §11 |
| M-BC5 | playlist → music | `music_id` geçerliliği + süre okuma | §11 |
| M-BC6 | playback → music | `music.played` + duration | D3 (sahiplik açık) |
| M-BC7 | discovery/recommendation → music | `mood`/genre filtre kolonları | §11 |
| M-BC8 | music → K013-integraion | Dış katalog eşlemesi (alanı UNKNOWN) | §5 R5 |

## §17 ROL VE SORUMLULUK MATRİSİ (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda rollerin dağılımına dair kanıt yoktur.

| Karar alanı | Önerilen sahip | Neden | Ataç |
|---|---|---|---|
| Invariant bütünlüğü (M-P1…M-P14) | music domain sahibi | Şema↔domain farkı §7.1'de | §7 |
| Sayı SSOT kararı (`play_count` ↔ `total_plays`) | `data-engineer` + `architect` | İki tablo, kural yazılmadı | R8 |
| Slug UNIQUE farkı (parça vs sanatçı/tür) | `architect` | Kasıtlı tasarım mı, eksik mi? | R9 |
| Döngü engeli (genre hiyerarşi) | music domain sahibi | DB ret etmez — domain korumalı | §7.1 |
| Taşıma (musics→recordings) | `legacy-modernizer` | FAZ 2b izi s.494; birim farkı R7 | §6.2 |
| Olay envanteri genişlemesi | `domain-bilesenler.md` protokolü | Uydurma olay yasağı merkezi | §9.2 |
| Podcast/radio/video tablo sahipliği | `domain-kapsam.md` sahibi | 8 tablo çekirdek dışı, şemada | R6 |

## §18 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Bir parça kataloğa girerken zincir şöyledir: ekleme süreci services sınırında
durur, music dalı yalnız **dili** tanımlar (§2/3); kayıt `artist_id` olmadan
yaratılamaz (M-P1, s.309 + s.338 RESTRICT), albüm opsiyoneldir (M-P2, s.310).
Sanatçı atamaları N:M ile tutulur: aynı sanatçı ikinci kez eklenemez (M-P3,
s.117), sıra tekildir (M-P4, s.118). Türler ikili düzende durur: hem tekil
`genre_id` (s.311, nullable) hem N:M çifti (s.195) — birincil tür bayrağı
s.192'dedir ama tekilleği DB'de güvence **altında değildir** (§7.1). Ruh hali
kapalı kümededir (M-P7, s.320 enum 7). Silme yumuşaktır (M-P9, s.329–330);
sanatçı silinirse parçalar silinmez çünkü FK RESTRICT'tir (s.338), tür
silinirse parçalar kalır ve tür bağlantısı NULL düşer (s.339) — bu iki
davranış farkı bilinçli okunmalıdır. Yeni model `recordings` tarafında
taşıma izi `legacy_music_id` (s.494) ile korunur; FK olmadığı için eşlemenin
doğruluğu uygulama katmanına düşer (M-P14). Zincirin en zayıf halkası,
`play_count` ile `music_stats.total_plays` arasındaki SSOT belirsizliğidir
(R8) — olay yayını (`music.added`/`music.played`) ise dosya kanıtlı tek
iyi halkadır (§9.1).

## §19 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §19.1 Bu dosyadan okuma sırası

| Amac | Sıra |
|---|---|
| Katalog dilini anlamak | §1 → §2 → §6.1 |
| Şema↔domain farkını görmek | §7 → §7.1 (öneri ve ⚠️ ayrımı) |
| Taşıma durumunu okumak | §6.2 → §12 R7 |
| Olay envanteri | §9.1 (kanıt) → §9.2 (`❌`) → `domain-bilesenler.md` §12 |
| Kapanış | §12 (R1–R10) → §14 (ölçüm) → §20 (yöntem) |

### §19.2 Çağrılan komşu dosyalar

| Dosya | Neden |
|---|---|
| `domain-master.md` §24/§25 | Invariant/VO üretim kuralı (§7/§8) |
| `domain-kapsam.md` §3 | BC envanteri — R6/R10 çapraz okuma |
| `domain-bilesenler.md` §7–§12 | Olay protokolü, T-1…T-12 eşleme |
| `domain-bagimliliklar.md` §4/47 | K007 kavram/şema ayrımı |
| `domain-web-arastirma.md` §18.2 | Tek URL kaynağı (C4) |
| `domain-dogrulama.md` §13 | Tur sonu raporu |
| `music/index.md` satır 13 | v2'de rol tanımı yok kanıtı |

## §20 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

### §20.1 Sınırlar

1. **Bu dosya kapsam + kanıt dosyasıdır, kod değildir.** §8 VO'ları, §10
   repository sözleşmesi depoda yoktur — okuyucu "var" sanıp çağıramaz.
2. **URL aramayın.** C4 gereği web kanıtı `domain-web-arastirma.md` §18.2'dedir.
3. **§13 testleri yazılmamıştır** — taslaktır (`qa-engineer` kapsamı).
4. **`❌ DOSYA YOK` satırları kasıtlı kayıttır** — uydurma olay eklemek §9.2
   protokolünü ihlal eder.
5. **`status: draft` bu turda korunmuştur.** §2/§3'teki ⚠️ satırları ve
   ADR-097 K4 beklentisi aynen durur (§3 yasağı).

### §20.2 Ölçüm yöntemi

| Soru | Cevap |
|---|---|
| Satır nasıl sayılır? | Dosya okunur, satır sonları `\r\n` / `\n` / `\r` ile bölünür — JS `ReadAllLines` eşdeğeri |
| Tahmin var mı? | Hayır — §14 tablosu araç ölçümüdür; tahmin doldurma yasak (master §9-C5) |
| Mojibake nasıl taranır? | U+4E00–U+9FFF aralığı tüm dosyada taranır; isabet 0 beklenir |
| URL ihlali nasıl taranır? | Sıkı desen: `https?://` takibi veya `www.` + alfanümerik; tek başına `http` desen açıklamasıdır, sayılmaz |
| Authority nasıl korunur? | 2026-10-12 satırı aynen kalır, 2026-10-17 satırı eklenir (master §22 M-6) |

---

## §21 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Seçilen | Reddedilen | Neden |
|---|---|---|---|---|
| D-M1 | Bu turda kapsam iddiası | Kanıt sunma + öneri etiketleme (§6–§13) | Invariant/kural seti kilitleme | §3 yasağı + ADR-097 K4 beklemesi |
| D-M2 | Taşıma modeli okuması | `recordings` = aday, `legacy_music_id` = iz (§6.2) | Taşıma tamamlandı saymak | s.494 comment "backfill FAZ 2b" + FK yok |
| D-M3 | Sayı SSOT'u | Açık soru (R8) + uyumsuzluk kaydı (§7.1) | Tahmini SSOT atamak | İki kolon kanıtlı (s.324/s.242), kural kanıtsız |
| D-M4 | Slug farkı | Açık soru (R9) + erişim notu (A-M6 🔴) | Parça slug'ını UNIQUE varsaymak | s.80/s.100'de UNIQUE var, s.331–337'de yok — fark gerçek |
| D-M5 | `music.played` sahipliği | Sahiplik açık kaydı (D3, §9.1 notu) | Sahiplik kararı vermek | `domain-bilesenler.md` §14 çizelgesi merkezi |
| D-M6 | Podcast/radio/video tabloları | Açık soru R6 + çapraz okuma daveti | Bu dala dahil saymak | Şemada (s.342–543) ama §2 kapsam tablosunda yok |
| D-M7 | Olay envanteri | 2 kanıtlı + 6 `❌ DOSYA YOK` | Uydurma olay yazmak | Zero-hallucination + §9.2 protokolü |

## §22 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

> Senaryolar §7 invariant adaylarından türetilmiştir; implementasyon yoktur.

### §22.1 Parça ekleme (services çağrısı varsayımı)

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | services | Ekleme dili katalog dilinden gelir | §2/3, M-BC1 |
| 2 | music | `artist_id` olmadan kayıt ret | M-P1 |
| 3 | music | Albüm yoksa kayıt geçerli | M-P2 |
| 4 | music | `mood` kapalı kümeyi aşarsa ret | M-P7, V-M4 |
| 5 | music | `music.added` yayılır — 3 anahtar | §9.1 |

### §22.2 Sanatçı atama

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | music | Aynı (parça, sanatçı) ikinci kez ret | M-P3 |
| 2 | music | Pozisyon toplu/tekil atanır | M-P4 |
| 3 | music | Birincil sanatçı bayrağı en fazla bir | §7.1 (DB güvencesi YOK — domain) |

### §22.3 Tür atama

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | music | (parça, tür) çifti tekil | M-P5 |
| 2 | music | Birincil tür alanı güncellenir | M-P6 |
| 3 | music | Hiyerarşi döngüsü ret | §7.1 (DB ret etmez — T-M12) |

### §22.4 Silme

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | music | Yumuşak silme ikilisi birlikte | M-P9 |
| 2 | DB | Sanatçı silme RESTRICT | M-P10 (s.338) |
| 3 | DB | Tür silme → parça kalır, `genre_id` NULL | M-P10 (s.339) |

## §23 SONRAKİ TUR İŞ KALEMLERİ

| # | İş | Bağlı soru/borç | Çıktı |
|---|---|---|---|
| J-M1 | `play_count` yazım noktaları taraması | R8 | Ya SSOT kanıtlanır ya §7.1 satırı güncellenir |
| J-M2 | Parça slug üretimi okuması | R9, A-M6 | UNIQUE farkının niyeti belgelenir |
| J-M3 | `domain-kapsam.md` §3 çapraz okuma | R6, R10 | Podcast/radio/video + `music_files` sahipliği |
| J-M4 | FAZ 2b durum taraması | §6.2 ⚠️ | Taşıma ilerlemesi kanıtlanır veya `UNKNOWN` kalır |
| J-M5 | `domain-dogrulama.md` tur sonu raporu | §14 | Ölçüm (a–d) — URL yok, sayı var |

> **Kapanış bu turda:** §1–§5 aynen korundu · §3 yasağı işlemede ·
> `status: draft` · hiçbir ⚠️/UNKNOWN kapatılmadı · URL 0 · Authority 2 satır.

---

## §24 TUR SONU ÖLÇÜM DÜZELTMESİ

§14 tablosu ilk ekleme anının ölçümüdür; bu tur ikinci eklemeyi de kapsar:

| Metrik | İlk ekleme (§6–§14) | İkinci ekleme (§15–§23) | Nihai |
|---|---|---|---|
| Satır | 93 → 308 | 308 → 424 → 489 → 505 | bu dosyanın son hâli (nihai sayı raporu: `domain-dogrulama.md` §13) |
| Eklenen bölüm sayısı | 9 (§6–§14) | 9 (§15–§23) + §24 | 19 (§6–§24) |
| Düzeltme | — | V-M7 tablo hücresi (fazladan `|` → birleşik hücre) | markdown tablo bütünlüğü ✅ |
| URL / CJK / Authority | 0 / 0 / 2 | 0 / 0 / 2 | 0 / 0 / 2 |

> §3 yasağı, `status: draft` ve §1–§5 içeriği **hiçbir düzeltmede
> değiştirilmedi** — yalnız tablo hücre biçimi onarıldı (satır sayısı değişmez).

---

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Authority:** Bayram Ali / Vault Steward · K008-music-kapsam · 2026-10-17 · §6–§14 eklendi: şema kanıtları · M-P1…M-P14 invariant adayı (kilitlenmemiş) · V-M1…V-M8 VO · E-M1…E-M6 eksik olay · R-M1…R-M9 repository · R6–R10 açık soru · 12 test senaryosu · status: draft korundu
