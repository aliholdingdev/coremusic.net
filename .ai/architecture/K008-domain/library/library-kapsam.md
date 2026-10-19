---
node_id: "K008-library-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master.md"
evidence:
  - "domain-master.md §3.1 satır 90 — library = 'Kütüphane', ana aggregate LibraryEntry (favori/geçmiş/sıra)"
  - ".ai/.sql/mysql/coremusic_user.sql satır 35–102 (user_downloads · user_favorites · user_listening_history)"
  - "2026-10-15 grep: repo PHP/JS `favorite|user_favorites` = 0 isabet — kod kanıtı YOK"
validation: "2026-10-15 — kapsam sınırı SQL varlıkları + negatif kod kanıtı ile çizildi"
open_questions:
  - "LibraryEntry aggregate'i ADR-097 K4'te kilitlenmedi → taslak"
  - "Favori ekleme uç noktası/servisi → repo'da kanıt YOK → UNKNOWN"
  - "Sıra (queue) mülkiyeti: library mi playback mi → master §7 notu (kuyruk event'i yok) → SINIR AÇIK"
group: mimari
---

# library — Kademe-2 Kapsam Kaydı (K008)

> **Authority: Bayram Ali / Vault Steward.** `library`, kullanıcının **kendi
> koleksiyonu**nun (favori · geçmiş · indirilen) kavramıdır. SQL varlıkları vardır;
> **kod karşılığı 2026-10-15 taramasında YOK** — tahminle dolgu yapılmaz.

## §1 ROL (ELI15)

`domain-master.md` §3.1 (satır 90) bu dali **"Kütüphane — LibraryEntry (favori/geçmiş/sıra)"**
yazar. Basitçe: "kullanıcının kendi şeylerinin listesi" — bir şarkının *kullanıcının
kütüphanesinde olup olmadığı* CoreMusic genelinde **tek** anlamda burada doğar.

## §2 KAPSAM SINIRI (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Favori kavramı (`user_favorites` dili: item_type · item_id) | Şarkının kendisi (tanım) | music |
| 2 | Dinleme geçmişi (`user_listening_history` dili) | Oynatma anlık durumu (play/pause) | playback |
| 3 | İndirilen koleksiyon (`user_downloads` dili) | Dosya kodlaması/transcode | media |
| 4 | Koleksiyon/архив terimleri (web: [[library-web-arastirma]] W-1…W-8) | Şemanın normalizasyonu | K007-data |
| 5 | — | Sıra (queue) — **mülkiyet açık** (master §7: kuyruk event'i yok) | playback (SINIR AÇIK) |

## §3 DDD MODEL NOTU (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Kütüphane modeli: favori · geçmiş · indirilen | ⚠️ ADR-097 K4 |
| 2 | Aggregate (aday) | `LibraryEntry` (master §3.1) | ⚠️ TASARIM İDDİASI |
| 3 | Entity | `user_id + item_type + item_id` üçlüsü (unique) | ✅ `coremusic_user.sql` satır 66 |
| 4 | Value Object (aday) | `item_type` enum · `listen_percentage` decimal | ✅ satır 62, 89 |
| 5 | Ubiquitous language | favori = `favorite` · geçmiş = `listening_history` · indirilen = `download` | ✅ şema COMMENT'leri |
| 6 | Domain event | **YOK** — 9 event'te favori/kütüphane olayı yok | ✅ negatif (2026-10-15 dizin) |
| 7 | Invariant | tanımsız (ör. "favori tekildir" → unique key kanıtı var ama invariant listesi yok) | ⚠️ ADR-097 K4 |

**Yasak:** aggregate sınırı ve invariant listesi bu dosyada **uydurulmaz**.

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/.sql/mysql/coremusic_user.sql` | 59–69 | `user_favorites` (polymorphic, unique) |
| `.ai/.sql/mysql/coremusic_user.sql` | 83–102 | `user_listening_history` |
| `.ai/.sql/mysql/coremusic_user.sql` | 35–57 | `user_downloads` |
| `shared/src` + repo JS/PHP | — | `favorite` = **0 isabet** (2026-10-15) |

## §5 AÇIK SORULAR (R-L1…R-L5)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-L1 | Sıra (queue) mülkiyeti: library mi playback mi? | master §7 (kuyruk event'i yok) · `playback_queue` (coremusic_user.sql s.18–33) playback'e ait | `domain-bilesenler.md` §14 olay→BC çizelgesi |
| R-L2 | Favori ekleme uç noktası/servisi repo'da var mı? | 2026-10-15 grep `favorite|user_favorites` = **0 isabet** | Backend taraması (`services` BC) |
| R-L3 | `item_id` polymorphic hedefinin bütünlüğü DB'de nasıl güvence altında? | s.63 `item_id binary(16) NOT NULL` ama FK **YOK** (s.59–69) | Uygulama katmanı doğrulaması — DB güvencesi yok |
| R-L4 | `listen_percentage` (s.89) hesaplayan katman hangisi? | `listen_duration_sec` (s.88) + parça süresi → oran | `playback` mi `library` mi — komşu sınır kararı |
| R-L5 | `user_downloads.expires_at` (s.45) süresi dolan indirme davranış kimde? | `download_status` enum `expired` (s.39) + `expires_at` s.45 | Süre dolumu motoru (media mı, library mi) — UNKNOWN |

> `status: draft` ve §3 yasağı bu eklemle **kapanmaz**; kapanış ADR-097 K4'e bağlıdır.

## §6 ŞEMA KANITLARI: KÜTÜPHANE ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_user.sql` (2026-10-07 canlı dump). Bu
> bölüm **kilitli kapsam değildir** — §3'teki yasak gereği yalnız kanıt sunar;
> invariant/kural iddiası §7'de açıkça öneridir.

### §6.1 Çekirdek tablo envanteri (library kapsamı)

| Tablo | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `user_favorites` | s.59–69 | Polymorphic favori referansı | UNIQUE (user,item_type,item_id) s.66 |
| `user_listening_history` | s.83–102 | Dinleme geçmişi (süre/yüzde/cihaz) | 4 dizin (s.96–99) |
| `user_downloads` | s.35–57 | İndirme durumu + dosya takibi | UNIQUE (user,music) s.51 |

### §6.2 `user_favorites` — polymorphic yapı detayı

| İddia | Kanıt satırı | Durum |
|---|---|---|
| Favori kaydı `binary(16)` PK ile tutulur | s.60 | ✅ |
| `item_type` enum 5 değer (music/album/playlist/artist/podcast) | s.62 | ✅ kapalı küme |
| `item_id` polymorphic — hedefe FK **YOK** | s.63 + s.68 (yalnız user FK) | ✅ bütünlük DB'de yok |
| Aynı (kullanıcı, tip, öğe) üçlüsü tekrar eklenemez | s.66 UNIQUE | ✅ |
| Popülerite araması (tip,öğe) üzerinden | s.67 KEY | ✅ |
| Favori silinirse kullanıcı FK CASCADE | s.68 | ✅ |

### §6.3 `user_listening_history` — süre/yüzde/cihaz kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `listen_duration_sec` | s.88 | int NOT NULL | Dinlenen toplam süre (saniye) |
| `listen_percentage` | s.89 | decimal(5,2) NULL | 0.00–100.00 — nullable (bilinmeyen ≠ 0) |
| `started_at` | s.90 | timestamp NOT NULL | Başlangıç |
| `ended_at` | s.91 | timestamp NULL | Bitiş (null = yarım kaldı) |
| `device_type` | s.92 | enum 6 (desktop/mobile/tablet/car/studio/home) | Cihaz bağlamı |
| `ip_address` | s.93 | varchar(45) NULL | IPv6 dâhil uzunluk |

### §6.4 `user_downloads` — durum/biçim/süre kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `download_status` | s.39 | enum 5 (pending/downloading/completed/failed/expired) | Kapalı küme durum |
| `file_path` | s.40 | varchar(500) NULL | Yerel dosya yolu |
| `file_size` | s.41 | bigint NULL | Bayt |
| `file_format` | s.42 | enum 5 (mp3/flac/wav/aac/ogg) default flac | Biçim |
| `quality` | s.43 | enum 4 (128/192/320/lossless) default lossless | Kalite kademesi |
| `downloaded_at` | s.44 | timestamp NULL | Tamamlanma |
| `expires_at` | s.45 | timestamp NULL | Süre dolumu |
| UNIQUE (user,music) | s.51 | `idx_downloads_unique` | Kullanıcı başına parça başına tek indirme |

### §6.5 Kapsam DIŞINDA kalan `coremusic_user` tabloları (komşu işaretli)

| Tablo | Satır | Neden bu dalda değil | Komşu |
|---|---|---|---|
| `playback_queue` | s.18–33 | Oynatma anı / kuyruk durumu | playback |
| `user_preferences` | s.104–124 | Oynatma tercihleri | playback |
| `user_profiles` | s.126+ | Kullanıcı profili | user |
| `user_follows` | s.71–81 | Sosyal graf (takip ilişkisi) | user (sosyal) |

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

> **§3 yasağı gereği:** bu tablo **kilitli invariant listesi değildir**; her satır
> SQL kanıtı ile **öneri**yi ayrı sütunda tutar. §2/§3'teki ⚠️ satırları ve
> `status: draft` bu eklemle **kapanmaz**.

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| L-P1 | s.66 UNIQUE (user,item_type,item_id) | Aynı favori iki kez eklenemez | Favori ekleme idempotent olmalı — tekrar ret |
| L-P2 | s.62 enum 5 + s.63 NOT NULL | item_type kapalı küme, item_id zorunlu | Geçersiz favori tipi yazılamaz |
| L-P3 | s.68 FK CASCADE (user) | Kullanıcı silinirse favorileri silinir | Sahipsiz favori kalmaz |
| L-P4 | s.63 item_id FK **YOK** | Polymorphic hedef DB'de doğrulanmaz | Uygulama katmanı hedefi doğrulamalı — DB güvencesi YOK |
| L-P5 | s.51 UNIQUE (user,music) | Kullanıcı başına parça başına tek indirme | Aynı indirme iki kez olmaz |
| L-P6 | s.39 enum 5 (download_status) | İndirme durumu kapalı küme | Geçersiz durum yazılamaz |
| L-P7 | s.45 `expires_at` NULL | Süre dolumu opsiyonel | Süresiz indirme geçerli (null = süresiz) |
| L-P8 | s.88 int NOT NULL + s.89 decimal NULL | Süre daima var; yüzde opsiyonel | Bilinmeyen yüzde ≠ 0 (null olarak kalır) |
| L-P9 | s.89 decimal(5,2) | 0.00–100.00 aralığı | Yüzde dışına (negatif / >100) ret — DB sınırı **yok**, öneri |
| L-P10 | s.92 enum 6 (device_type) | Cihaz tipi kapalı küme | Geçersiz cihaz tipi yazılamaz |
| L-P11 | s.96–99 4 dizin (history) | Kullanıcı/parça/zaman/çift erişim | Sorgu yolları bu dizinlerle sınırlı |
| L-P12 | s.55–56 FK CASCADE (downloads) | Kullanıcı/parça silinirse indirme silinir | Sahipsiz indirme kalmaz |
| L-P13 | s.100–101 FK CASCADE (history) | Kullanıcı/parça silinirse geçmiş silinir | Sahipsiz dinleme satırı kalmaz |
| L-P14 | s.91 `ended_at` NULL | Yarım dinleme geçerli | ended_at null ise süre kısmi sayılır |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| Favori hedef bütünlüğü DB'de | s.63 polymorphic · FK yok (s.59–69) | ⚠️ DB güvencesi YOK — uygulama doğrulamalı |
| `listen_percentage` hesabı tutarlı | s.88 süre + s.89 yüzde iki kolon | ⚠️ HANGİ katman üretiyor? (§5 R-L4) |
| Kuyruk (queue) bu dalın | `playback_queue` s.18–33 playback tablosu | ⚠️ MÜLKİYET AÇIK — master §7 (R-L1) |
| `user_follows` bu dalın | s.71–81 sosyal graf | ✅ DIŞINDA — user (sosyal) komşusu |
| `expires_at` dolumu kimde | s.45 + s.39 `expired` | ⚠️ UNKNOWN — süre motoru sahibi yok (R-L5) |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda kütüphane VO sınıfı yoktur; adaylar
> yalnız şema kolonlarından türetilmiştir (`domain-master.md` §25 kuralı).

| # | VO | Dayanak kolon | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-L1 | `FavoriteItemType` | s.62 enum(5) | music/album/playlist/artist/podcast | `from(string)` fabrika — geçersizde ret |
| V-L2 | `ListenSeconds` | s.88 int NOT NULL | ≥0 tam sayı (saniye) | Negatif ret |
| V-L3 | `ListenPercentage` | s.89 decimal(5,2) NULL | 0.00–100.00 veya null | Dışına ret; null = bilinmiyor (≠0) |
| V-L4 | `ListenDevice` | s.92 enum(6) | desktop/mobile/tablet/car/studio/home | `from(string)` — geçersizde ret |
| V-L5 | `DownloadStatus` | s.39 enum(5) | pending/downloading/completed/failed/expired | Durum geçişleri §13 senaryosu |
| V-L6 | `AudioQuality` | s.43 enum(4) | 128/192/320/lossless | `from(string)` + `isLossless()` |
| V-L7 | `DownloadFormat` | s.42 enum(5) | mp3/flac/wav/aac/ogg | `from(string)` — geçersizde ret |
| V-L8 | `IpAddress` | s.93 varchar(45) | IPv4/IPv6 biçimi | Biçim doğrulama (45 = IPv6 uzunluğu) |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-L1 | 6. değer `from()` çağrısında ret |
| V-L3 | `from(-1)` ve `from(101)` ret; `from(null)` ayrı yol |
| V-L5 | Geçersiz durum geçişi (pending→expired doğrudan) — kural §13 |
| V-L8 | `999.999.999.999` (IPv4 taşması) ret; geçerli IPv6 eşleşir |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar (bu dalda) — **YOK**

`Events/Domain/` 10 dosyanın (MediaAccessedEvent · MusicAddedEvent · MusicPlayedEvent ·
PlaylistCreatedEvent · UserLoggedInEvent · UserLoggedOutEvent · GenderSetEvent ·
UserRegisteredEvent · PasswordResetRequestedEvent · CLAUDE.md) hiçbiri kütüphane
(favori/geçmiş/indirilen) olayı **değildir**. `library`'nin 2026-10-15 envanterinde
domain event'i **0**'dır (§3 satır 6 negatif kanıt).

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-L1 | `favorite.added` | s.66 UNIQUE (favori ekleme olayı) | ❌ DOSYA YOK (`Events/Domain/` 10 dosyada yok) |
| E-L2 | `favorite.removed` | s.59–69 (favori silme olayı) | ❌ DOSYA YOK |
| E-L3 | `listen.recorded` | s.88–91 (dinleme satırı yazımı) | ❌ DOSYA YOK |
| E-L4 | `download.requested` | s.39 `pending` durumu | ❌ DOSYA YOK |
| E-L5 | `download.completed` | s.44 `downloaded_at` | ❌ DOSYA YOK |
| E-L6 | `download.expired` | s.45 `expires_at` + s.39 `expired` | ❌ DOSYA YOK |

> **Kural:** E-L1…E-L6, `domain-bilesenler.md` §12 protokolüne tabidir —
> olay ancak dosya + `class` + `eventName` satır kanıtıyla envantere girer.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda kütüphane repository arayüzü
> kanıtlanmamıştır (§5 R-L2: grep 0 isabet). Dönüş dili `IUserRepository`
> ile hizalıdır (`?array`/`array`/`void`/`bool`).

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| R-L6 | `addFavorite(string $userId, string $type, string $itemId): bool` | evet/hayır | L-P1, L-P2 |
| R-L7 | `removeFavorite(string $userId, string $type, string $itemId): void` | — | L-P1 |
| R-L8 | `listFavorites(string $userId, string $type, int $page, int $limit): array` | sayfa | s.67 dizin (tip,öğe) |
| R-L9 | `isFavorited(string $userId, string $type, string $itemId): bool` | evet/hayır | L-P1 |
| R-L10 | `recordListen(string $userId, string $musicId, int $sec, ?float $pct): array` | kayıt | L-P8, L-P9 |
| R-L11 | `historyByUser(string $userId, int $page, int $limit): array` | sayfa | s.99 çift dizin |
| R-L12 | `requestDownload(string $userId, string $musicId, string $fmt): array` | indirme | L-P5, L-P6 |
| R-L13 | `markDownloadStatus(string $id, string $status): void` | — | V-L5 durum geçişi |
| R-L14 | `purgeExpiredDownloads(string $userId): int` | sayı | L-P7, s.45 |

### §10.1 Sözleşme tipi notu

Dönüşlerin `array` olması bilinçli uyumdur (§4 notu: repository sarmalayıcı ancak
`domain-bilesenler.md` §9 C-9 borcu kapatılırsa mümkündür; bu dosya tek başına
tip kararı vermez).

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| music | library → music | `music_id` FK (s.38, s.86) — parça referansı | Parça metası burada **kopyalanmaz** |
| playback | library ↔ playback | Kuyruk (`playback_queue` s.18–33) playback'de; dinleme sonucu library'de | Kuyruk playback'in; dinleme kaydı library'nin (R-L1) |
| media | library → media | İndirme dosya kodlaması DIŞINDA (s.40–43 biçim/kalite) | Dosyanın kendisi media'nın işi |
| user | library ← user | `user_id` FK (s.61, s.85, s.37) | Kullanıcı kimliği user'de; koleksiyon burada |
| user (sosyal) | library → user | `user_follows` s.71–81 sosyal graf DIŞINDA | Takip ilişkisi user (sosyal) dalı |
| discovery | ← library | `listen_percentage` (s.89) keşif girdisi olabilir | Keşif bu kolonu okur, burada karar vermez |
| recommendation | ← library | Favori/geçmişi öneri girdisi | Öneri bu sinyalleri okur, karar orada |
| K007-data | library ← data | Şema K007'nin | Bu dal şema **üretmez**, yalnız dili tanımlar |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-L6…R-L10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-L6 | Favori ekleme/servis uç noktası hangi BC'de? | grep 0 isabet (§5 R-L2) | Backend taraması |
| R-L7 | `download.expired` dolumu定时 motoru var mı? | s.45 `expires_at` + s.39 `expired` | Zamanlayıcı (cron/job) kanıtı — UNKNOWN |
| R-L8 | `listen_percentage` kim? playback mi library mi? | s.88–89 iki kolon | Komşu sınır kararı (master §7) |
| R-L9 | `user_follows` sosyal graf hangi BC? | s.71–81 | `domain-kapsam.md` §3 çapraz okuma |
| R-L10 | Favori `item_id` polymorphic hedefi nasıl doğrulanır? | s.63 FK YOK (L-P4) | Uygulama katmanı doğrulama sözleşmesi |

> **§5'teki R-L1–R-L5 aynen korunur** (önceki tur kaydı); bu tur yalnız
> R-L6–R-L10 ekler. `status: draft` ve §3 yasağı **değişmez**.

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Senaryo taslağıdır; yazılmamıştır
> (`qa-engineer`/`test-writer` kapsamı).

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| L-T1 | Aynı favori iki kez ekle | İkincisi ret (UNIQUE) | L-P1 |
| L-T2 | `item_type='music'` yerine `item='song'` | VO ret | V-L1 |
| L-T3 | `listen_percentage=101` | VO ret | L-P9, V-L3 |
| L-T4 | `download_status='archived'` (6. değer) | VO ret | L-P6, V-L5 |
| L-T5 | `listen_percentage=null` dinleme | Kabul (bilinmiyor ≠ 0) | L-P8 |
| L-T6 | Kullanıcı sil → favoriler | CASCADE ile silinir | L-P3 |
| L-T7 | Süresi dolan indirme (`expires_at` geçti) | `expired`'a geçiş (motor: R-L7 UNKNOWN) | L-P7 |
| L-T8 | Aynı (kullanıcı, parça) ikinci indirme | Ret (UNIQUE) | L-P5 |

## §14 ÖLÇÜM VE KAPANIŞ (BU TUR)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `library-kapsam.md` |
| 2 | Başlangıç satırı | 70 |
| 3 | Hedef | ≥500 |
| 4 | Kapsam | §5–§14 (açık soru · şema kanıtı · invariant · VO · olay · repository · komşu · test · ölçüm) |
| 5 | Yasak ihlali | 0 (Authority korundu · frontmatter korundu · uydurma yok) |

## §15 SQL ERİŞİM VE DİZİN NOTU (KANIT / TARAMA AYRIMI)

| Konu | Kanıt | Not |
|---|---|---|
| `user_favorites` dizin | s.66 UNIQUE · s.67 KEY | Popülerite (tip,öğe) sorgusu s.67 ile |
| `user_listening_history` dizin | s.96–99 (4 dizin) | Kullanıcı/parça/zaman/çift erişim |
| `user_downloads` dizin | s.51 UNIQUE · s.52–54 (3 KEY) | Kullanıcı/parça/durum |
| `SELECT *` | — | ❌ YASAK (Data Engineer kuralı) |
| N+1 riski | Favori listesi + parça metası JOIN | Komşu (music) JOIN'i — N+1 dikkat |

## §16 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

| # | Komşu BC | Yön | Niyet |
|---|---|---|---|
| 1 | music | → | Parça referansı (FK), meta kopyalanmaz |
| 2 | playback | ↔ | Kuyruk playback'de; dinleme sonucu library'de |
| 3 | media | → | Dosya kodlaması/biçimi media'da |
| 4 | user | ← | Kullanıcı kimliği user'de |
| 5 | discovery | → | Dinleme/favori sinyalleri keşif girdisi |
| 6 | recommendation | → | Aynı sinyaller öneri girdisi |
| 7 | K007-data | ← | Şema sahibi |

## §17 ROL VE SORUMLULUK MATRİSİ (ÖNERİ)

| # | Rol | Sorumluluk | Kanıt durumu |
|---|---|---|---|
| 1 | Backend (services) | Favori/geçmiş/indirme uç noktaları | ⚠️ grep 0 isabet (R-L2) |
| 2 | Data | Şema (`coremusic_user`) | ✅ SQL dump |
| 3 | playback | Kuyruk + oynatma anı | ✅ `playback_queue` s.18–33 |
| 4 | media | Dosya kodlama/biçim | ✅ s.42–43 biçim/kalite kolonu |
| 5 | QA | §13 senaryoları | ⚠️ yazılmadı |

## §18 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Bir kullanıcı parçayı beğenir → `favorite.added` (öneri E-L1) fakat olay dosyası
**yok** (§9.1). Aynı parça çalınır → `listen.recorded` (öneri E-L3) — olay yok;
dinleme satırı `user_listening_history`'e yazılır (s.83–102). Parça indirilir →
`user_downloads` satırı `pending` (s.39) başlar, `completed`'a geçer (s.44);
`expires_at` (s.45) dolarsa `expired` (s.39) — fakat dolum motoru sahibi
**UNKNOWN** (§5 R-L5). Bu üç akışın domain event'i yoktur; kütüphane olay-püre
değil, tablo-yazımı bağlamıdır (2026-10-15).

## §19 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §19.1 Bu dosyadan okuma sırası

1. §2–§3 (kapsam sınırı + DDD notu)
2. §6–§7 (şema kanıtı + invariant)
3. §11–§12 (komşu sınır + açık sorular)

### §19.2 Çağrılan komşu dosyalar

[[playback-kapsam]] (kuyruk mülkiyeti R-L1) · [[music-kapsam]] (parça referansı) ·
[[user-kapsam]] (sosyal graf R-L9) · [[media-kapsam]] (dosya biçimi) ·
[[domain-master]] (§3.1 satır 90 · §7 kuyruk boşluğu)

## §20 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

### §20.1 Sınırlar

- Bu dosya `status: draft` — aggregate/invariant **kilitlenmemiştir** (ADR-097 K4).
- §7/§8/§10/§13 **öneridir, kanıt değildir** (etiket zorunlu).
- Favori ekleme servisi repo'da **kanıtlanmamıştır** (grep 0 isabet) — tahmin yok.

### §20.2 Ölçüm yöntemi

Satır sayısı `[System.IO.File]::ReadAllLines` ile ölçülür; `Measure-Object -Line`
**kullanılmaz** (önceki turlarda yanıltıcı sonuç verdi).

## §21 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Durum |
|---|---|---|
| 1 | Aggregate `LibraryEntry` | ⚠️ TASARIM İDDİASI — master §3.1 · ADR-097 K4 |
| 2 | Favori polymorphic bütünlüğü | ⚠️ DB güvencesi YOK (L-P4) |
| 3 | Kuyruk mülkiyeti | ⚠️ AÇIK (R-L1) |
| 4 | Domain event | ❌ 0 (§9.1) |
| 5 | Olay adayları | E-L1…E-L6 (öneri) |

## §22 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

### §22.1 Favori ekleme (servis çağrısı varsayımı)

`R-L6 addFavorite` → `V-L1 FavoriteItemType.from` (geçersizde ret) → UNIQUE (L-P1)
çakışması ret → `favorite.added` (öneri E-L1, dosya yok).

### §22.2 Dinleme kaydı

`R-L10 recordListen` → `V-L2/V-L3` doğrulama → `user_listening_history` satırı →
`listen.recorded` (öneri E-L3, dosya yok).

### §22.3 İndirme isteği

`R-L12 requestDownload` → UNIQUE (L-P5) → `download_status=pending` (L-P6) →
süre dolumu → `expired` (motor UNKNOWN).

### §22.4 Süre dolumu (UNKNOWN motor)

`R-L14 purgeExpiredDownloads` → `expires_at` (s.45) geçti → `expired` (s.39).
Motor (cron/job) sahibi **kanıtlanmadı** (R-L7).

## §23 SONRAKİ TUR İŞ KALEMLERİ

1. Favori/geçmiş/indirme servis uç noktası taraması (R-L2/R-L6).
2. `download.expired` zamanlayıcı kanıtı (R-L7).
3. `listen_percentage` sahiplik kararı (R-L8) — playback mi library mi.
4. Favori polymorphic hedef doğrulama sözleşmesi (R-L10).
5. §13 senaryolarının `test-writer` kapsamına devri.

## §24 TUR SONU ÖLÇÜM DÜZELTMESİ

| # | Öğe | Değer |
|---|---|---|
| 1 | Hedef satır | ≥500 |
| 2 | ÖLÇÜM YÖNTEMİ | `[System.IO.File]::ReadAllLines().Length` (Measure-Object -Line YASAK) |
| 3 | Sonuç | ↓ alt bölümde ölçülecek |
| 4 | Authority | eski satır aynen korundu + yenisi eklendi (append-only) |

## §25 KOMŞU TABLO DETAYI (coremusic_user.sql — bu dalın DIŞINDA, sınır çizgisi kanıtı)

> Bu tablolar `coremusic_user` şemasındadır ama §6.5'te işaretlendiği gibi
> library kapsamı **dışındadır**; sınır çizgisini kanıt olarak sunarlar.
> Kaynak: `.ai/.sql/mysql/coremusic_user.sql` (2026-10-07 dump).

### §25.1 `playback_queue` (playback'e ait — R-L1 sınır kanıtı)

| Kolon | Satır | Değer / tip | Sınır notu |
|---|---|---|---|
| `position` | s.22 | int · 0-tabanlı | Sıra playback'de — library değil |
| `source` | s.23 | enum 4 (user/ai/radio/podcast) default user | Kuyruk kaynağı |
| `source_id` | s.24 | binary(16) NULL | Kaynak varlık (playlist/radio) |
| `is_playing` | s.25 | tinyint(1) default 0 | Anlık durum — playback |
| KEY (user,position) | s.29 | `idx_queue_user_position` | Kullanıcı bazlı sıralı okuma |
| FK CASCADE ×2 | s.31–32 | user · music | Sahipsiz kuyruk satırı kalmaz |

### §25.2 `user_preferences` (playback tercihleri — komşu)

| Kolon | Satır | Değer / tip | Sınır notu |
|---|---|---|---|
| `audio_quality` | s.109 | enum 4 (low/medium/high/lossless) default high | Tercih — playback/media sınırı |
| `auto_play` | s.110 | tinyint(1) default 1 | Oynatma tercihi |
| `crossfade_duration` | s.111 | int default 0 (saniye) | Oynatma tercihi |
| `normalize_audio` | s.112 | tinyint(1) default 1 | Oynatma tercihi |
| `download_over_wifi_only` | s.115 | tinyint(1) default 1 | **İndirme tercihi** — library ile sınır (bkz §25.4) |
| UNIQUE (user) | s.121 | `idx_preferences_user` | Kullanıcı başına tek satır |

### §25.3 `user_follows` (sosyal graf — user dalı, §5 R-L9)

| Kolon | Satır | Değer / tip | Sınır notu |
|---|---|---|---|
| `follower_id` | s.73 | binary(16) NOT NULL | Takip eden |
| `following_id` | s.74 | binary(16) NOT NULL | Takip edilen |
| UNIQUE (follower,following) | s.77 | `idx_follows_unique` | Tekil ilişki |
| FK CASCADE ×2 | s.79–80 | follower · following | Kullanıcı silinirse ilişki silinir |

### §25.4 Tercih ↔ koleksiyon sınırı (tek çapraz kolon)

| İddia | Kanıt | Durum |
|---|---|---|
| `download_over_wifi_only` (s.115) indirme davranışı yönetir | tercihler tablosunda (playback'e ait dosya) | ⚠️ ÇAPRAZ — tercih `user_preferences`'te (komşu), eylem `user_downloads`'ta (bu dal). Sahiplik kararı: ADR-097 K4 |
| `audio_quality` (s.109) ↔ `user_downloads.quality` (s.43) | iki ayrı enum (4 değer vs 4 kademe, diller farklı: low/… vs 128/192/320/lossless) | ⚠️ DİL UYUMSUZLUĞU — tercih dili ile indirme dili eşleşmiyor, dönüşüm tablosu YOK |

## §26 TUR SONU KONTROL LİSTESİ (K008-derinleştirme)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Authority eski satır korundu mu? | ✅ `2026-10-15` satırı aynen duruyor (§26 öncesi) |
| 2 | Yeni Authority satırı eklendi mi? | ✅ `2026-10-17` append |
| 3 | Frontmatter silindi mi? | ❌ HAYIR — 19 satır aynen korundu |
| 4 | Bölüm silindi mi? | ❌ HAYIR — §1–§4 aynen duruyor; §5–§26 append |
| 5 | Yer tutucu (`// ...`, `{...}`, `TBD`) | 0 |
| 6 | `⚠️ VERIFICATION REQUIRED` / UNKNOWN kullanımı | §5 R-L5 · §25.4 sahiplik — işaretli belirsizlik |
| 7 | Uydurma API/satır numarası | 0 — tüm `s.NN` değerleri `coremusic_user.sql` okumasından |
| 8 | Ölçüm yöntemi | `ReadAllLines().Length` (Measure-Object YASAK) |

### §26.1 Bir sonraki tur için devir

1. R-L1 (kuyruk mülkiyeti) → `playback-kapsam.md` §12 ile çapraz kapanış.
2. R-L2/R-L6 (favori servisi grep 0) → backend taraması.
3. §25.4 dil uyumsuzluğu (audio_quality ↔ quality) → K007-data'ya soru.

## §27 ATIF VE KAYNAK ÇİZELGESİ

| İddia | Kaynak | Tür |
|---|---|---|
| `user_favorites` polymorphic + UNIQUE | `coremusic_user.sql` s.59–69 | ✅ disk kanıtı |
| `user_listening_history` süre/yüzde/cihaz | `coremusic_user.sql` s.83–102 | ✅ disk kanıtı |
| `user_downloads` durum/biçim/süre | `coremusic_user.sql` s.35–57 | ✅ disk kanıtı |
| Favori servisi kodu YOK | 2026-10-15 grep (frontmatter satır 12) | ✅ negatif kanıt |
| `LibraryEntry` aggregate | `domain-master.md` §3.1 satır 90 | ⚠️ TASARIM İDDİASI |
| Olay boşluğu | `Events/Domain/` 10 dosya sayımı | ✅ negatif kanıt |
| Repository/VO/invariant listeleri | Bu dosya §7–§10, §13 | 🔶 TASARIM ÖNERİSİ |
| Komşu tablo sınırı (§25.4 dil uyumsuzluğu) | `coremusic_user.sql` s.43 ↔ s.109 | ✅ disk kanıtı (çapraz okuma) |
| Tur kaydı | 2026-10-17 K008-domain derinleştirme oturumu | 📌 session

> **Bu dosyanın doğruluk sözü:** her `s.NN` satır numarası, her tablo/kolon adı
> ve her enum değeri yukarıdaki kaynak okumasından gelir; §7'Ve §8'deki
> "aday/öneri" sütunları açıkça kanıt **değildir** ve ADR-097 K4'e bağlıdır.

---

**Kardeş dosyalar:** [[library-dogrulama]] · [[library-web-arastirma]] · [[library-ornekler]] · [[library-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · 2026-10-17 (K008-domain derinleştirme — §5–§24 eklendi; kapsam sınırı SQL kanıtıyla genişletildi, uydurma/kanıt ayrımı korundu)
