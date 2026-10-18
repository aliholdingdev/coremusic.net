---
node_id: K008-domain/bilesenler
node_type: root
status: verify-required
parent_id: K008-domain
children:
  - music
  - media
  - user
  - library
  - playlist
  - playback
  - discovery
  - recommendation
  - device-management
  - context-mapping
depends_on:
  - K008-domain/domain-kapsam.md
  - K008-domain/domain-bagimliliklar.md
evidence:
  - shared/src/Events/Domain
  - shared/src/AI/AIEngine.php
  - .ai/.sql/mysql
validation: 2026-10-12 — bölüm 3'teki her bileşen satırında en az bir disk kanıtı var
open_questions:
  - "device-management ve context-mapping için repo bileşeni kanıtı yok — kademe-3 planı varsayımsal, VERIFICATION REQUIRED"
group: mimari
---

# K008-domain — Bileşenler (Component Catalogue)

> **Authority: Bayram Ali / Vault Steward.** K008-domain'in yapı taşları:
> her Bounded Context'in (çocuk) **domain bileşenleri** (aggregate, entity,
> value object, event, repository arayüzü). Bileşen = domain modelindeki
> adlandırılmış parça; PHP sınıfı değil **konsepttir**, kod kanıtı ayrı sütunda.

## §1 BİLEŞEN TÜRLERİ

| Tür | Tanım | Kademe |
|---|---|---|
| Aggregate Root | Tutarlılık sınırının girişi; kimliği ile tanınır | 1 (çocuk) |
| Entity | Kimliği olan, değişebilen domain nesnesi | 2 |
| Value Object | Değeri ile tanınan, değişmez nesne | 2 |
| Domain Event | Alanda gerçekleşen, adlandırılmış olay | 2 |
| Repository (arayüz) | Aggregate kalıcılık sözleşmesi | 2 |
| Domain Service | Tek aggregate'e ait olmayan iş kuralı | 2 |
| Subdomain (kademe-3 düğüm) | Çocuk altında ayrılabilecek alt alan | 3 |

## §2 KÖK BİLEŞENLER (K008-domain düzeyi)

| # | Bileşen | Tür | Sahip dosya | Kanıt |
|---|---|---|---|---|
| R1 | Bounded Context envanteri | Domain Service (meta) | `domain-kapsam.md` §3 | v2 spec §2.8 + `shared/config/domain.php` |
| R2 | Domain event sözleşmesi (base class) | Domain Event (meta) | `domain-master.md` §3.3 | `shared/src/Events/Domain/` (9 sınıf) |
| R3 | EventDispatcher yayını | Domain Service (altyapı) | `domain-bagimliliklar.md` §3.2 | `EventDispatcher.php` s.20/s.28 |
| R4 | Ubiquitous dil sözlüğü | Meta | `domain-evrensel-dil.md` | v2 spec §4.2 + MusicBrainz terimleri |
| R5 | Bağlam haritası (context map) | Domain Service (meta) | `context-mapping` çocuğu | `⚠️ VERIFICATION REQUIRED` |

## §3 ÇOCUK BİLEŞEN ENVANTERİ (kademe 1–2)

### §3.1 `music` — Müzik Kataloğu

| Bileşen | Tür | Kanıt (dosya) | Durum |
|---|---|---|---|
| Music (eser/kayıt birleşik görünümü) | Aggregate Root | `.ai/.sql/mysql/coremusic_musics.sql` — `musics` (s.305) | Kanıtlı |
| Artist | Entity | `artists` (s.63) | Kanıtlı |
| Work | Value Object / Entity | `works` (s.544) | Kanıtlı |
| Recording | Entity | `recordings` (s.487) | Kanıtlı |
| Album (release ilişkisi) | Entity (başka BC ile ilişki) | `coremusic_albums.sql` — `releases` (s.157) | Kanıtlı |
| MusicAudioFeature | Value Object | `music_audio_features` (s.126) | Kanıtlı |
| RadioStation | Entity | `radio_stations` (s.460) | Kanıtlı |
| PodcastShow | Entity | `podcast_shows` (s.370) | Kanıtlı |
| MusicPlayedEvent | Domain Event | `shared/src/Events/Domain/MusicPlayedEvent.php` | Kanıtlı |

### §3.2 `media` — Medya Varlıkları

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| MediaFile | Aggregate Root | `coremusic_media.sql` — `media_metadata` (s.150) | Kanıtlı |
| MusicFile | Value Object | `coremusic_musics.sql` — `music_files` (s.162) | Kanıtlı |
| MediaMetadata | Value Object | `media_metadata` (s.150) | Kanıtlı |
| MediaAccessedEvent | Domain Event | `MediaAccessedEvent.php` s.20 (class), s.39 (`media.accessed`) | Kanıtlı |

### §3.3 `user` — Kimlik & Profil

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| User | Aggregate Root | `coremusic_auth.sql` — `users` (s.270) | Kanıtlı |
| UserSession | Entity | `user_sessions` (s.227) | Kanıtlı |
| UserProfile | Value Object | `coremusic_user.sql` — `user_profiles` (s.126) | Kanıtlı |
| UserPreference | Value Object | `user_preferences` (s.104) | Kanıtlı |
| ApiKey | Entity | `shared/src/Repository/ApiKeyRepository.php` (s.31/33/36/40–41) | Kanıtlı |
| UserRegisteredEvent | Domain Event | `shared/src/Events/Domain/` | Kanıtlı |

### §3.4 `library` — Kütüphane

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| UserFavorite | Aggregate parçası | `coremusic_user.sql` — `user_favorites` (s.59) | Kanıtlı |
| ListeningHistory | Aggregate parçası | `user_listening_history` (s.83) | Kanıtlı |
| PlaybackQueue (kuyruk sahipliği) | Aggregate parçası | `playback_queue` (s.18) | Kanıtlı |
| PlaybackQueueUpdatedEvent | Domain Event | 2026-10-15 glob: `shared/src/Events/**` (14 dosya) içinde YOK | ❌ GÖRÜLMEDİ — eski "Kanıtlı" kaydı yanlıştı |

### §3.5 `playlist` — Çalma Listesi

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| Playlist | Aggregate Root | `coremusic_playlist.sql` — `playlists` (s.84) | Kanıtlı |
| PlaylistTrack | Entity | `playlist_tracks` (s.67) | Kanıtlı |
| PlaylistCollaborator | Value Object | `playlist_collaborators` (s.18) | Kanıtlı |
| PlaylistCreatedEvent | Domain Event | `PlaylistCreatedEvent.php` s.20/s.39 | Kanıtlı |

### §3.6 `playback` — Oynatma

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| PlaybackSession | Aggregate Root | `playback_queue` (s.18) + MusicPlayedEvent akışı | Kısmi |
| MusicPlayedEvent | Domain Event | `MusicPlayedEvent.php` s.20, s.37–39 | Kanıtlı |
| PlaybackQueueUpdatedEvent | Domain Event | 2026-10-15 glob: `shared/src/Events/**` (14 dosya) içinde YOK | ❌ GÖRÜLMEDİ — eski "Kanıtlı" kaydı yanlıştı |
| Gapless/kuyruk kuralı | Domain Service | `⚠️ VERIFICATION REQUIRED` (kod kanıtı yok) | Bekliyor |

### §3.7 `discovery` — Keşif (dalga 3)

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| DiscoveryQuery | Aggregate (planlanan) | `⚠️ VERIFICATION REQUIRED` | Bekliyor |
| Trend/Radar kuralı | Domain Service (planlanan) | `⚠️ VERIFICATION REQUIRED` | Bekliyor |
| Katalog girdileri (tür/mood) | Dış kaynak | `coremusic_catalog.sql` — `catalog_genres` (s.65), `catalog_moods` (s.119) | Kanıtlı (girdi) |

### §3.8 `recommendation` — Öneri (dalga 3)

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| RecommendationSet | Aggregate (planlanan) | `⚠️ VERIFICATION REQUIRED` | Bekliyor |
| Öneri eşiği (MIN_SCORE, MAX_RECOMMENDATIONS, DIVERSITY_TARGET) | Domain kuralı | `shared/src/AI/AIEngine.php` s.30–32 | Kanıtlı (uygulama; sahiplik CONFLICT C2) |
| getRecommendations akışı | Domain servis kanıtı | `AIEngine.php` s.49 | Kanıtlı (uygulama) |

### §3.9 `device-management` — Cihaz Yönetimi (dalga 3)

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| Device | Aggregate (planlanan) | `coremusic_media.sql` — `devices` (s.89) | Kanıtlı (şema) |
| Oynatma hedefi ilişkisi | Value Object (planlanan) | `⚠️ VERIFICATION REQUIRED` | Bekliyor |

### §3.10 `context-mapping` — Bağlam Haritası (dalga 3)

| Bileşen | Tür | Kanıt | Durum |
|---|---|---|---|
| ContextMap | Aggregate (meta) | `⚠️ VERIFICATION REQUIRED` | Bekliyor |
| Upstream/Downstream ilişkisi | Value Object (meta) | ADR-097 §5 (yöntem kaynağı) | Yöntem |

## §4 KADEME-3 PLANI (alt düğüm adayları — kademe 3 açılışı)

Aşağıdaki alt alanlar, K008 altında kademe-3 (`Band-3`) düğüm adayıdır. Açılış
`orkestratör` onayı ile olur; bu dosya yalnız planı taşır:

| Çocuk | Kademe-3 adayı | Neden ayrılabilir | Kanıt |
|---|---|---|---|
| `music` | `music-meta` (ISRC/ISWC/MBID kimlikleri) | Kimlik yönetimi ayrı dil gerektirir | MusicBrainz kimlik belgeleri (web-arastirma) |
| `music` | `music-editorial` (derleme/compilation, radyo, podcast) | Editoryal katalog ayrı kaynak | `podcast_shows` (s.370), `radio_stations` (s.460) |
| `media` | `media-transcode` | Çıktı formatları ayrı domain dili | `media_metadata` (s.150) |
| `user` | `user-auth` | Kimlik doğrulama ayrı dil | `coremusic_auth.sql` `users` (s.270) |
| `library` | `library-queue` | Kuyruk oynatma ile iç içe (C2 benzeri sınır) | `playback_queue` (s.18) |
| `playback` | `playback-gain` (ReplayGain/eşik) | DSP-adjacent kural, ayrı uzmanlık | web-arastirma (ReplayGain) |

## §5 BİLEŞEN × GRUP EŞLEŞMESİ (ADR-097 §5.5)

| Grup | Bileşen dosyaları |
|---|---|
| `mimari` | Bu dosya + çocuk `-kapsam.md` + çocuk `-bilesenler.md` |
| `kod` | Çocuk `-ornekler.md` (her bileşenin gerçek PHP karşılığı) |
| `kanit` | Çocuk `-dogrulama.md` (şema/kod satır kanıtları) |

## §6 SONRAKİ ADIM

| # | Adım | Sahip |
|---|---|---|
| 1 | Dalga 1–2 çocuk `-bilesenler.md` dosyaları (kademe-3 planı) | çocuk agent'ı |
| 2 | §3.7–§3.10'daki `⚠️` satırlarının kanıtlanması | orkestratör + keşif |
| 3 | V1/V2 belirsiz bağımlılık taraması (bagimliliklar §5) | kod taraması |

## §7 TÜR ↔ KOD YÜZEYİ EŞLEME KURALLARI

Bir SQL kolonu veya PHP sınıfı, tek başına domain bileşeni **değildir**. Aşağıdaki
eşleme kuralları bu dosyadaki her `Tür` sütunu kararı için bağlayıcıdır; karar
verilirken satır kanıtı zorunludur. Kurallar dil bağımsızdır — PHP, SQL veya
başka bir dilde aynı sonucu verir.

| # | Kanıt yüzeyi | Domain türü eşlemesi | Kural |
|---|---|---|---|
| T-1 | `PRIMARY KEY` tek kolon (`s.30`, `s.102`) | Entity / Aggregate Root | Kimlik kolonu varsa Entity; tutarlılık sınırının girişi ise Aggregate Root |
| T-2 | `UNIQUE` doğal anahtar (`s.31`, `s.78`) | Value Object adayı | Değeri ile tanımlanan çift/üçlü Entity değil, VO olur |
| T-3 | `ENUM(...)` + `CHECK` (`s.90`, `s.91`) | Value Object (kapalı küme) | Kapalı değer kümesi VO fabrika metodu ile temsil edilir |
| T-4 | `FOREIGN KEY ... ON DELETE CASCADE` (`s.34`) | İlişki — **sahiplik kanıtı değildir** | Cascade, aggregate sınırını tek başına kanıtlamaz; sahiplik ayrı kanıtlanır |
| T-5 | `TIMESTAMP` / `DATETIME` kolonu | Entity yaşam döngüsü alanı | VO sayılmaz; domain event `occurredOn`'u bu alanın karşılığı değildir |
| T-6 | `TINYINT(1) is_deleted` (`s.28`, `s.100`) | Repository sorgu filtresi | Soft-delete, repository sözleşmesinde taşınır; aggregate invariant'ı değildir |
| T-7 | `implements DomainEventInterface` | Domain Event | Arayüzü uygulayan ve `eventName()` döndüren sınıf = olay |
| T-8 | `final class ...Repository` | Repository **implementasyonu** | Arayüz domain katmanında; `final` sınıf Infrastructure'dadır |
| T-9 | `FULLTEXT` / `KEY` indeksi (`s.103`–`s.106`) | Sorgu performansı ayrıntısı | Domain modeline yansıtılmaz; bileşen envanterine girmez |
| T-10 | `AIEngine.php` içinde eşik sabitleri (s.30–32) | Domain kuralı (sahiplik açık) | Sabit domain kuralıdır; sahibi BC belirsizse §9 C-2 borcu olarak kaydedilir |
| T-11 | `readonly` property / `private constructor` | VO adayı (dil kalıbı) | Değişmezlik dil kalıbı, VO'nun **kanıtı değil örtüşmesidir** — asıl kanıt T-2/T-3 |
| T-12 | Arayüz (`I*Repository`) | Domain/Adapter sözleşmesi | Sözleşme domain'de; hangi sınıfların uyguladığı ayrı kanıt (§9 C-1) |

### §7.1 Eşleme kararlarının bağlandığı conformance maddeleri

| Karar | Bağlı madde |
|---|---|
| Tür ataması (§3 tabloları) | `domain-master.md` §9 C3 — kanıt zorunluluğu |
| Olay adı / alan adı uydurma yasağı | `domain-master.md` §9 C5 |
| Repository arayüzü ayrımı | `domain-master.md` §28 — R-1…R-7 |
| Value Object kararı | `domain-master.md` §25 — V-1…V-7 |
| Invariant kararı | `domain-master.md` §24 — K-1…K-3, I-1…I-5 |
| Event adlandırma | `domain-master.md` §26 — + bu dosya §8.1 |

> **Not:** Bu eşleme kuralları, `domain-kod-ornekleri.md` §8 dil matrisindeki
> karşılıkların da kuralıdır — dil ne olursa olsun T-1…T-12 değişmez.

## §8 OLAY ENVANTERİ (DOSYA + SATIR KANITLI)

Tüm event dosyaları `shared/src/Events/` altındadır; her satır 2026-10-17
okuması ile doğrulanmıştır. `class` satırı ve `eventName()` dönüş satırı, satır
sayım aracı ile **1-tabanlı**dır. Bu bölümde dosya dışında URL yoktur (C4).

### §8.1 Domain event'leri (9 dosya — `Events/Domain/`)

| # | Dosya | `class` sat. | `eventName()` | ad sat. | Ctor alanları | Payload anahtarları |
|---|---|---|---|---|---|---|
| D-1 | `GenderSetEvent.php` | 20 | `user.gender_set` | 38 | userId, gender | userId, gender, timestamp |
| D-2 | `MediaAccessedEvent.php` | 20 | `media.accessed` | 39 | mediaId, userId, accessType | mediaId, userId, accessType, timestamp |
| D-3 | `MusicAddedEvent.php` | 20 | `music.added` | 39 | musicId, addedBy, title | musicId, addedBy, title, timestamp |
| D-4 | `MusicPlayedEvent.php` | 20 | `music.played` | 39 | musicId, userId, duration | musicId, userId, duration, timestamp |
| D-5 | `PasswordResetRequestedEvent.php` | 20 | `user.password_reset_requested` | 39 | userId, email, token | userId, email, token, timestamp |
| D-6 | `PlaylistCreatedEvent.php` | 20 | `playlist.created` | 39 | playlistId, createdBy, name | playlistId, createdBy, name, timestamp |
| D-7 | `UserLoggedInEvent.php` | 20 | `user.logged_in` | 39 | userId, ipAddress, userAgent | userId, ipAddress, userAgent, timestamp |
| D-8 | `UserLoggedOutEvent.php` | 20 | `user.logged_out` | 38 | userId, sessionId | userId, sessionId, timestamp |
| D-9 | `UserRegisteredEvent.php` | 20 | `user.registered` | 40 | userId, username, email, gender | userId, username, email, gender, timestamp |

### §8.2 Integration event'leri (3 dosya — `Events/Integration/`)

| # | Dosya | `class` sat. | `eventName()` | ad sat. | Ctor alanları | Payload anahtarları |
|---|---|---|---|---|---|---|
| I-1 | `AuthValidatedEvent.php` | 20 | `auth.validated` | 39 | userId, source, validatedAt | userId, source, validatedAt, timestamp |
| I-2 | `NotificationEvent.php` | 20 | `notification.sent` | 40 | userId, type, message, data | userId, type, message, data, timestamp |
| I-3 | `SessionCreatedEvent.php` | 20 | `session.created` | 39 | sessionId, userId, expiresAt | sessionId, userId, expiresAt, timestamp |

### §8.3 Arayüz sözleşmesi (dosya + satır kanıtlı)

| Arayüz | Dosya | Toplam satır | Yöntemler (satır) |
|---|---|---|---|
| `DomainEventInterface` | `shared/src/Contracts/Events/DomainEventInterface.php` | 40 | `eventName(): string` (24) · `occurredOn(): \DateTimeImmutable` (31) · `payload(): array` (38) |
| `IntegrationEventInterface` | `shared/src/Contracts/Events/IntegrationEventInterface.php` | 47 | `eventName(): string` (24) · `source(): string` (31) · `payload(): array` (38) · `occurredOn(): \DateTimeImmutable` (45) |

Sözleşme farkı: integration event ek olarak **`source()`** taşır (kimin
yayinladığı); domain event'te bu alan yoktur. Her iki arayüzde de `payload()`
vardır; payload anahtar seti olay bazında §8.1/§8.2 sütunlarıyla sabittir.

### §8.4 Ortak alan kalıpları (12 olay üzerinden gözlem)

| Kalıp | Görüldüğü yer | Not |
|---|---|---|
| `timestamp` payload anahtarı | 12/12 olay | `occurredOn` ISO-8601 biçiminde üretilir |
| `occurredOn` ctor içinde atanır | 12/12 olay | Saat kaynağı construct anıdır; ayrı saat enjeksiyonu yok (§9 C-4) |
| `StoppableEventTrait` kullanımı | Domain event'ler (ör. `MediaAccessedEvent.php` sınıf satırı altı) | Durdurulabilir akış |
| Kimlik taşıyan ilk alan | 12/12 olay | İlk alan aggregate/entity referansıdır (`<XxxId>`) |
| Kişisel veri alanı | D-5, D-7, D-9 | `email` / `ipAddress` payload'da açık taşınır (§9 C-7) |

### §8.5 Eksik / çakışan olaylar (kanıt yoksa uydurulmaz)

| # | İddia | Durum | Kanıt |
|---|---|---|---|
| X-1 | `PlaybackQueueUpdatedEvent` | ❌ DOSYA YOK | 2026-10-17 `Events/Domain/` listesi 9 dosya + `CLAUDE.md`; bu ad listede yok |
| X-2 | `PlaylistAddedEvent` / `PlaylistRemovedEvent` | ❌ DOSYA YOK | Aynı liste — playlist yalnız `playlist.created` üretir |
| X-3 | `MusicRemovedEvent` | ❌ DOSYA YOK | `music.*` yalnız `added` ve `played` |
| X-4 | 12 olaydan hangileri gerçekten yayılıyor (dispatch) | ⚠️ VERIFICATION REQUIRED | Olay sınıfları var; yayın noktaları (`dispatch` çağrıları) bu turda taranmadı |
| X-5 | Integration event'lerin dış kuyruk çıkışı | ⚠️ VERIFICATION REQUIRED | `EventDispatcher.php` yayını kanıtlı; dış kuyruk entegrasyonu kanıtlanmadı |
| X-6 | Aynı olayın iki BC'de farklı adla var olması | ⚠️ VERIFICATION REQUIRED | 12 ad tek tek benzersiz (§8.1/§8.2); çapraz BC yayılımı taranmadı |

> **Kural:** X-1…X-3 satırları `domain-master.md` §9 C5 ile aynı yasağı
> uygular: var olmayan olay adı bileşen listesine eklenmez; eksiklik `❌ DOSYA
> YOK` olarak kaydedilir ve silinmez (§12 yasakları).

## §9 BİLEŞEN BORÇLARI

Borç = kanıtı olan ama henüz olgunluk eşiğini geçememiş veya sahipliği açık
bileşen durumu. Her satırın kanıt sütunu doludur; kanıtsız satır açılmaz.

| # | Borç | Etkilediği BC | Kanıt | Ağırlık |
|---|---|---|---|---|
| C-1 | Repository arayüzleri yalnız Auth alanında soyutlanmış | user/auth | `shared/src/Contracts/Auth/IUserRepository.php` — 52 satır, 20 method (s.7–s.50); müzik/playlist için `I*Repository` yok | Yüksek |
| C-2 | Öneri eşikleri AIEngine içinde sabit, BC sahipliği belirsiz | recommendation | `shared/src/AI/AIEngine.php` s.30–32 | Orta |
| C-3 | `PlaybackQueueUpdatedEvent` yok — kuyruk değişimi olaylaştırılmamış | playback/library | §8.5 X-1 | Orta |
| C-4 | Saat kaynağı 12 olayda gömülü (`new \DateTimeImmutable()`) | hepsi | §8.4 | Orta |
| C-5 | `final` repository sınıflarının iç DB akışı taranmadı | hepsi | `ApiKeyRepository.php` s.31–41 | ⚠️ VERIFICATION REQUIRED |
| C-6 | `device-management` ve `context-mapping` için bileşen kanıtı yok | bu iki BC | §3.9 / §3.10 `⚠️` satırları | Yüksek |
| C-7 | Event payload'ında kişisel veri açık taşınıyor | user | §8.1 D-5, D-7, D-9 | Güvenlik |
| C-8 | Olay adları iki kalıpta: `<alan>.<olay>` ve `user.*` alt ağacı | hepsi | §8.1/§8.2 `eventName()` sütunu | Düşük |
| C-9 | Domain event'lerde aggregate kimliği `string` — tip sarmalayıcı yok | hepsi | §8.1 ctor alanları | Düşük |

### §9.1 Borç kapanış önceliği

| Sıra | Borç | Neden önce |
|---|---|---|
| 1 | C-6 | Kanıtsız BC'ler §3 tablosunda `Bekliyor` durumunu kilitleyor |
| 2 | C-1 | Repository sözleşmesi olmadan `domain-master.md` §28 R-kuralları uygulanamaz |
| 3 | C-3 | Olay yoksayması playback akışını gözlemez yapıyor |
| 4 | C-7 | Kişisel veri taşıması güvenlik denetimi gerektirir |
| 5 | C-4 | Test edilebilirlik için saat seam'i gerekir |
| 6 | C-2 | Domain kuralı sahipliği conformance C2'ye bağlı |
| 7 | C-5 | İç akış taraması ile doğrulanır |
| 8 | C-9 | Tip sarmalayıcı, sonraki genişleme turunun konusu |
| 9 | C-8 | İsimlendirme tekilleştirmesi düzenleyicidir, kırıcı değil |

## §10 BC × BİLEŞEN MATRİSİ

Satır = `domain-kapsam.md` §3'teki Bounded Context; sütun = §1 türleri.
Değer = §3 alt bölümlerindeki **kanıtlanmış** bileşen sayısı (planlanan hariç).

| BC | Aggregate Root | Entity | Value Object | Domain Event | Repository | Toplam kanıtlı | Bekleyen |
|---|---|---|---|---|---|---|---|
| music | 1 | 3 | 2 | 1 | 0 | 7 | 0 |
| media | 1 | 0 | 2 | 1 | 0 | 4 | 0 |
| user | 1 | 2 | 2 | 1 | 0 (arayüz Auth'ta — C-1) | 6 | 0 |
| library | 0 | 0 | 0 | 0 | 0 | 3 (parçalar) | 1 (❌ X-1) |
| playlist | 1 | 1 | 1 | 1 | 0 | 4 | 0 |
| playback | 1 (kısmi) | 0 | 0 | 1 | 0 | 2 | 1 |
| discovery | 0 | 0 | 0 | 0 | 0 | 1 (girdi) | 3 |
| recommendation | 0 | 0 | 0 | 0 | 0 | 2 (uygulama kanıtı) | 1 |
| device-management | 0 | 0 | 0 | 0 | 0 | 1 (şema) | 2 |
| context-mapping | 0 | 0 | 0 | 0 | 0 | 1 (yöntem) | 2 |
| **Toplam** | **5** | **6** | **7** | **6** | **0** | **31** | **10** |

**Okuma:** kanıtlanmış 31 bileşenin yalnız 6'sı olay, 0'ı repository
arayüzüdür. Eksiklik domain modelinde değil; **kalıcılık sözleşmesinde** (C-1)
ve **olay yayın kanıtında** (X-4) ağırlaşıyor.

## §11 OLGUNLUK KADEMELERİ

| Kademe | Tanım | Geçiş ölçütü |
|---|---|---|
| L0 — Yok | Ne dosya ne şema kanıtı | `⚠️ VERIFICATION REQUIRED` satırı |
| L1 — Şema | SQL kolon/tablo kanıtı var, PHP karşılığı yok | §3 tablosunda `Kanıtlı (şema)` |
| L2 — Uygulama | PHP sınıfı kanıtı var, arayüz sözleşmesi yok | `Kanıtlı (uygulama)` |
| L3 — Sözleşme | Arayüz + implementasyon + örnek kullanım | `I*Repository` + 2+ implementasyon veya çağrı |
| L4 — Olay | Davranış olay ile gözlemlenir + dispatch kanıtı | `eventName()` + `dispatch` satır kanıtı |
| L5 — Test | Yukarıdakilerin hepsi + karakteristik test | test dosyası referansı |

### §11.1 BC olgunluk dağılımı (2026-10-17)

| BC | En yüksek kademe | Engel |
|---|---|---|
| music | L2 (olay sınıfı var) | X-4 — dispatch kanıtsız |
| media | L2 (`MediaAccessedEvent` dolu) | X-4 |
| user | L3 (`IUserRepository` 20 method — en olgun) | X-4 |
| library | L1 | C-3 — olay yok |
| playlist | L2 (`PlaylistCreatedEvent` + şema) | repository arayüzü yok |
| playback | L1 (kısmi) | C-3 |
| discovery | L0–L1 | §3.7 `⚠️` satırları |
| recommendation | L2 (uygulama kanıtı) | C-2 sahiplik |
| device-management | L1 (şema) | C-6 |
| context-mapping | L0 (yöntem notu) | C-6 |

> **Ölçüm notu:** kademe, bu dosyanın kararı değil kanıt durumunun
> yansımasıdır; kademe-3 düğüm açılımı orkestratör onayı gerektirir (§4).

## §12 BİLEŞEN EKLEME / DEĞİŞTİRME PROTOKOLÜ

Bu dosyaya yeni satır ekleyen her agent sırayı izler; sıradan sapma yoktur:

| Adım | İşlem | Zorunlu kanıt |
|---|---|---|
| 1 | Tür belirle (§7 T-1…T-12) | SQL satırı veya PHP `class` satırı |
| 2 | BC sahibini seç (§10 satırı) | `domain-kapsam.md` §3 ile aynı ad |
| 3 | Dosya + satır kanıtı yaz | 1-tabanlı satır, gerçek dosya yolu |
| 4 | Durum işaretle (Kanıtlı / Kısmi / Bekliyor / ❌) | §3'teki mevcut sözlük ile uyum |
| 5 | Olay ise §8.1/§8.2 tablosuna da ekle | `class` + `eventName()` satırları |
| 6 | Kanıt yoksa `⚠️ VERIFICATION REQUIRED` yaz | uydurma ad yasağı (master §9 C5) |
| 7 | Borç doğurduysa §9'a C-kaydı ekle | kanıt sütunu dolu olacak |
| 8 | §10 matrisini güncelle | sayılar §3 toplamıyla eşit olacak |
| 9 | §13 ölçüm tablosunu güncelle | yeni satır sayısı ölçülecek |

**Yasaklar (bu dosyaya özgü):**

- Sahiplik verilmemiş BC'ye `Aggregate Root` atamak;
- `❌ DOSYA YOK` satırını silmek — negatif kanıt da kanıttır;
- URL yazmak — kaynak URL'leri yalnız `domain-web-arastirma.md` §18.2'de (C4);
- §1–§6 mevcut satırlarını silmek veya tablo başlıklarını bozmak;
- Kanıtsız `Kanıtlı` işareti (master §9 C5);
- Yer tutucu metin ile tablo satırı tamamlamak (master §9 C5).

## §13 KAPANIŞ ÖLÇÜMÜ

| Metrik | Değer | Yöntem |
|---|---|---|
| Bu dosya, ekleme öncesi | 185 satır | satır sayım aracı — CRLF dosya sonu |
| Eklenen bölüm | §7–§16 | append-only; §1–§6 korundu |
| Olay satır kanıtı | 12 dosya × (class + eventName) | 2026-10-17 okuma, 1-tabanlı |
| Arayüz kanıtı | 2 dosya (40 / 47 satır) | method satır numaraları ile |
| Domain event sayısı | 9 | `Events/Domain/` dizin listesi |
| Integration event sayısı | 3 | `Events/Integration/` dizin listesi |
| Alan geneli repository arayüzü | 1 (`IUserRepository`, 20 method) | `shared/src/Contracts/**` taraması |
| Yeni URL | 0 | C4 — bu dosyada URL yok |
| Boş içerik satırı | 0 | master §9 C5 taraması |
| Authority | eski satır korundu + yeni satır eklendi | master §22 M-6 |
| Ekleme sonrası toplam | 501 satır (§16.3 ile aynı ölçüm) | aynı araç |

> **Sonraki tur için:** X-4/X-5 dispatch kanıtı ve C-6 iki BC kanıtı, bu
> dosyanın `Bekliyor` satırlarını kanıtlanmış duruma çevirecek tek girdidir.

## §14 OLAY → BC SAHİPLİĞİ EŞLEMESİ

Her olayın hangi BC'den çıktığı, adındaki ilk etiketten ve ctor kimlik alanından
türetilir; kanıt §8.1/§8.2'dir. Bu bir **eşleme önerisidir** — kesin sahiplik
`domain-kapsam.md` §3 ile çapraz doğrulandığında bağlayıcı olur.

| Olay | Kaynak BC (öneri) | Tetikleyici durum | Eşleşen bileşen |
|---|---|---|---|
| `music.added` | music | Eser kataloğa eklendi | Music (Aggregate Root) |
| `music.played` | playback | Oynatma kaydı üretildi | PlaybackSession (kısmi) |
| `media.accessed` | media | Medya varlığı erişildi | MediaFile (Aggregate Root) |
| `playlist.created` | playlist | Liste oluşturuldu | Playlist (Aggregate Root) |
| `user.registered` | user | Yeni kullanıcı kaydı | User (Aggregate Root) |
| `user.logged_in` | user | Oturum açıldı | UserSession (Entity) |
| `user.logged_out` | user | Oturum kapatıldı | UserSession (Entity) |
| `user.gender_set` | user | Profil bilgisi güncellendi | UserProfile (Value Object) |
| `user.password_reset_requested` | user | Sıfırlama isteği | User (Aggregate Root) |
| `auth.validated` | user (integration) | Kimlik doğrulandı | IUserRepository (L3 sözleşme) |
| `session.created` | user (integration) | Oturum üretildi | UserSession (Entity) |
| `notification.sent` | sahiplik açık — ⚠️ VERIFICATION REQUIRED | Bildirim gönderildi | Bileşen kanıtı yok (§3 envanterinde yok) |

**Gözlem:** 12 olayın 9'u `user`/kimlik ekseninde toplanıyor; `library` ve
`discovery` hiç olay üretmiyor (C-3, §10 `Bekleyen` sütunu). `notification.sent`
için BC envanterinde karşılık yok — bu, §9 C-6 benzeri bir sahiplik açıklığıdır
ve kanıtlanana kadar `⚠️` olarak kalır.

## §15 OKUMA YOLU (HANGİ AKTÖR HANGİ BÖLÜMÜ OKUR)

| Aktör / görev | Önce | Sonra | Ne alır |
|---|---|---|---|
| Yeni BC bileşeni ekleyen agent | §12 protokolü | §7 eşleme | Adım sırası + kanıt türü |
| Event ekleyen agent | §8.1/§8.2 | §14 + §9 C-7 | Ad/format + sahiplik + gizlilik borcu |
| Repository soyan agent | §9 C-1 | `domain-master.md` §28 | Sözleşme eksikliği + R-kuralları |
| Conformance denetçisi | §13 ölçüm | `domain-master.md` §9 | C3/C5 kanıt ve yasa kontrolü |
| Olgunluk raporu hazırlayan | §11 | §10 | Kademe + BC kırılımı |
| Kademe-3 açılışı değerlendiren | §4 | §11.1 | Aday listesi + mevcut kademe |
| Öneri/algoritma sahipliği arayan | §9 C-2 | §14 | Sahiplik açıklığı kaydı |

### §15.1 Bu dosyanın komşularıyla ilişkisi

| Komşu dosya | İlişki | Sınır |
|---|---|---|
| `domain-kapsam.md` | BC adları ve §3 envanteri oradan gelir | Bu dosya BC adı **yaratmaz** |
| `domain-master.md` | Kural/conformance oradadır | Bu dosya kural **yaratmaz**, bağlar |
| `domain-kod-ornekleri.md` | Dil karşılıkları oradadır | Bu dosya kod örneği **yazmaz** |
| `domain-web-arastirma.md` | URL'ler oradadır (§18.2) | Bu dosyada URL yok (C4) |
| `domain-dogrulama.md` | Tarama kanıtları oraya yazılır | Buradaki satır numaraları kod okumasından gelir |
| Çocuk `-kapsam.md` dosyaları | Çocuk kendi sınırını anlatır | Çocuk bu dosyayı **ezmez**; çelişki §18'e raporlanır |

## §16 SONRAKİ TUR İŞ KALEMLERİ + ÖLÇÜM

### §16.1 İş kalemleri (öncelik sırası)

| # | İş | Kapanış ölçütü | Bağlı kayıt |
|---|---|---|---|
| J-1 | `dispatch(...)` çağrılarını tara | X-4 `⚠️` satırı kanıtlanır | §8.5 |
| J-2 | `device-management` + `context-mapping` kod kanıtı | §3.9/§3.10 `Bekliyor` → `Kanıtlı` | §9 C-6 |
| J-3 | Alan başına repository arayüzü çıkar | C-1 `I*Repository` sayısı 1'den büyük olur | §9 |
| J-4 | Kuyruk olayını üret veya yokluğu teyit et | C-3 kapanır ya da gerekçeyle dondurulur | §9 |
| J-5 | Payload gizleme kararı | C-7 için security görüşü alınır | §9 |
| J-6 | `notification.sent` sahipliği | §14 `⚠️` satırı kapanır | §14 |

### §16.2 Bu turda yapılanların özeti

- §7: 12 eşleme kuralı (T-1…T-12) + conformance bağları;
- §8: 12 olay × 5 sütun satır kanıtı + 2 arayüz + 6 eksik/çakışma kaydı;
- §9: 9 borç + kapanış önceliği;
- §10: 10 BC × 7 sütun matrisi (31 kanıtlanmış, 10 bekleyen);
- §11: L0–L5 kademeleri + BC dağılımı;
- §12: 9 adımlı ekleme protokolü + 6 yasak;
- §13: ölçüm tablosu;
- §14: 12 olay → BC sahiplik eşlemesi (öneri etiketli);
- §15: aktör→bölüm okuma yolu + komşu dosya sınırları;
- §16: iş kalemleri + ölçüm.

### §16.3 Ölçüm

| Metrik | Değer |
|---|---|
| Ekleme öncesi | 185 satır |
| Ekleme sonrası | 501 satır (2026-10-17 doğrudan ölçüm) |
| Ölçüm aracı | satır sayım aracı — CRLF eşdeğeri (±1: dosya sonu newline) |
| Bölüm sayısı (yeni) | 10 (§7, §8, §9, §10, §11, §12, §13, §14, §15, §16) |
| Alt bölüm sayısı (yeni) | 6 (§7.1, §8.1–§8.5, §9.1, §11.1, §15.1, §16.1–§16.3) |
| Bölüm | §7–§16 |
| Yeni URL | 0 |
| Authority | eski (2026-10-12) + yeni (2026-10-17) satır |

---

*Authority: Bayram Ali / Vault Steward · K008-domain bileşenler · 2026-10-12 · 9 domain event + 20 SQL şema dosyası kanıtlı*
*Authority: Bayram Ali / Vault Steward · K008-domain bileşenler · 2026-10-17 · §7–§16 eklendi: T-1…T-12 eşleme · 12 olay satır kanıtlı · 9 borç · BC×bileşen matrisi · L0–L5 olgunluk · ekleme protokolü · olay→BC eşlemesi · okuma yolu · iş kalemleri*
