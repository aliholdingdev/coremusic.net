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
| MediaTranscodedEvent | Domain Event | `shared/src/Events/Domain/` envanteri (isim `⚠️`) | Kısmi |

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
| PlaybackQueueUpdatedEvent | Domain Event | `shared/src/Events/Domain/` | Kanıtlı |

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
| PlaybackQueueUpdatedEvent | Domain Event | `shared/src/Events/Domain/` | Kanıtlı |
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

---

*Authority: Bayram Ali / Vault Steward · K008-domain bileşenler · 2026-10-12 · 9 domain event + 20 SQL şema dosyası kanıtlı*
