---
node_id: K008-domain/dogrulama
node_type: root
status: verified
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
  - K008-domain/domain-web-arastirma.md
evidence:
  - .ai/.sql/mysql/coremusic_user.sql
  - .ai/.sql/mysql/coremusic_musics.sql
  - .ai/.sql/mysql/coremusic_playlist.sql
  - .ai/.sql/mysql/coremusic_albums.sql
  - .ai/.sql/mysql/coremusic_auth.sql
  - .ai/.sql/mysql/coremusic_media.sql
  - .ai/.sql/mysql/coremusic_catalog.sql
  - .ai/.sql/mysql/coremusic_download.sql
  - shared/src/Events/Domain
  - shared/src/Repository/ApiKeyRepository.php
  - shared/src/AI/AIEngine.php
  - shared/config/domain.php
validation: 2026-10-12 — 27 SQL satır + 10 kod satırı doğrudan diskten doğrulandı (PowerShell tarama)
open_questions:
  - "domain.php'deki 7 subdomain satırının 10 çocukla tam eşleşmesi doğrulanmadı — VERIFICATION REQUIRED"
group: kanit
---

# K008-domain — Doğrulama (Verification Ledger)

> **Authority: Bayram Ali / Vault Steward.** K008-domain kök iddialarının
> **kanıt defteri**. Kural: her satırda Dosya + Satır + doğrulama tarihi var;
> iddiasız satır yazılmaz. Bu dosyada en az 25 içerik satırı zorunlu (master §9-C1).

## §1 DOĞRULAMA ÖZETİ

| Alan | Sayı | Tarih |
|---|---|---|
| SQL tablo kanıtı | 27 satır (8 dosya) | 2026-10-12 |
| Kod kanıtı (event/dispatcher/repo/AI) | 10 satır (5 dosya) | 2026-10-12 |
| Web kanıtı (URL) | 76 URL (§ web-arastirma) | 2026-10-12 |
| Config kanıtı | 1 blok (`domain.php` s.7–15) | 2026-10-12 |
| Test kanıtı | 4 test dosyası | 2026-10-12 |
| Doğrulanamayan | 5 satır (`⚠️`) | 2026-10-12 |

## §2 FRONTMATTER DOĞRULAMASI (master §9-C1)

| Kontrol | Beklenen | Sonuç |
|---|---|---|
| 10 alan | `node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions, group` | Kök 12 MD'de mevcut |
| `group` değeri | `mimari|kod|kanit` | Index/master/kapsam/bagimliliklar/bilesenler/kararlar/evrensel-dil → `mimari`; kod-ornekleri/test-stratejisi → `kod`; dogrulama/web-arastirma/riskler → `kanit` |
| `status` değeri | `draft|verified|verify-required|conflict` | master/kapsam/kararlar → `verify-required`; diğerleri → `verified` |
| `parent_id` (kök) | `K000–K5000 Architecture Registry` | 12 kök MD'de aynı |
| Çocuk `parent_id` | `K008-domain` | Çocuk setlerinde |

## §3 SQL TABLO KANITLARI (27 satır — doğrulandı 2026-10-12)

### §3.1 `coremusic_user.sql`

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| S1 | `playback_queue` tablosu (kuyruk) | `.ai/.sql/mysql/coremusic_user.sql` | s.18 | Doğrulandı (`CREATE TABLE`) |
| S2 | `user_favorites` tablosu (favori) | `.ai/.sql/mysql/coremusic_user.sql` | s.59 | Doğrulandı |
| S3 | `user_listening_history` tablosu (geçmiş) | `.ai/.sql/mysql/coremusic_user.sql` | s.83 | Doğrulandı |
| S4 | `user_preferences` tablosu (tercih) | `.ai/.sql/mysql/coremusic_user.sql` | s.104 | Doğrulandı |
| S5 | `user_profiles` tablosu (profil) | `.ai/.sql/mysql/coremusic_user.sql` | s.126 | Doğrulandı |

### §3.2 `coremusic_playlist.sql`

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| S6 | `playlist_collaborators` tablosu (iş birliği) | `.ai/.sql/mysql/coremusic_playlist.sql` | s.18 | Doğrulandı |
| S7 | `playlist_tracks` tablosu (liste içeriği) | `.ai/.sql/mysql/coremusic_playlist.sql` | s.67 | Doğrulandı |
| S8 | `playlists` tablosu (liste) | `.ai/.sql/mysql/coremusic_playlist.sql` | s.84 | Doğrulandı |

### §3.3 `coremusic_musics.sql`

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| S9 | `artists` tablosu (sanatçı) | `.ai/.sql/mysql/coremusic_musics.sql` | s.63 | Doğrulandı |
| S10 | `music_audio_features` tablosu (ses özellikleri) | `.ai/.sql/mysql/coremusic_musics.sql` | s.126 | Doğrulandı |
| S11 | `music_files` tablosu (dosya varlığı) | `.ai/.sql/mysql/coremusic_musics.sql` | s.162 | Doğrulandı |
| S12 | `musics` tablosu (müzik) | `.ai/.sql/mysql/coremusic_musics.sql` | s.305 | Doğrulandı |
| S13 | `podcast_shows` tablosu (podcast) | `.ai/.sql/mysql/coremusic_musics.sql` | s.370 | Doğrulandı |
| S14 | `radio_stations` tablosu (radyo) | `.ai/.sql/mysql/coremusic_musics.sql` | s.460 | Doğrulandı |
| S15 | `recordings` tablosu (kayıt) | `.ai/.sql/mysql/coremusic_musics.sql` | s.487 | Doğrulandı |
| S16 | `works` tablosu (eser) | `.ai/.sql/mysql/coremusic_musics.sql` | s.544 | Doğrulandı |

### §3.4 `coremusic_albums.sql`

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| S17 | `albums` tablosu (albüm) | `.ai/.sql/mysql/coremusic_albums.sql` | s.74 | Doğrulandı |
| S18 | `release_groups` tablosu (sürüm grubu) | `.ai/.sql/mysql/coremusic_albums.sql` | s.127 | Doğrulandı |
| S19 | `releases` tablosu (sürüm) | `.ai/.sql/mysql/coremusic_albums.sql` | s.157 | Doğrulandı |
| S20 | `tracks` tablosu (parça) | `.ai/.sql/mysql/coremusic_albums.sql` | s.174 | Doğrulandı |

### §3.5 `coremusic_auth.sql`

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| S21 | `user_sessions` tablosu (oturum) | `.ai/.sql/mysql/coremusic_auth.sql` | s.227 | Doğrulandı |
| S22 | `users` tablosu (kullanıcı) | `.ai/.sql/mysql/coremusic_auth.sql` | s.270 | Doğrulandı |

### §3.6 `coremusic_media.sql` + `coremusic_download.sql` + `coremusic_catalog.sql`

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| S23 | `devices` tablosu (cihaz) | `.ai/.sql/mysql/coremusic_media.sql` | s.89 | Doğrulandı |
| S24 | `media_metadata` tablosu (medya meta) | `.ai/.sql/mysql/coremusic_media.sql` | s.150 | Doğrulandı |
| S25 | `download_queue` tablosu (indirme kuyruğu) | `.ai/.sql/mysql/coremusic_download.sql` | s.56 | Doğrulandı |
| S26 | `catalog_genres` tablosu (tür kataloğu) | `.ai/.sql/mysql/coremusic_catalog.sql` | s.65 | Doğrulandı |
| S27 | `catalog_moods` tablosu (ruh hali kataloğu) | `.ai/.sql/mysql/coremusic_catalog.sql` | s.119 | Doğrulandı |

## §4 KOD KANITLARI (event/dispatcher/repo/AI)

### §4.1 Domain event envanteri (dizin listesi)

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| E1 | 9 domain event sınıfı | `shared/src/Events/Domain/` | dizin | Doğrulandı (PowerShell `Get-ChildItem`) |
| E1a | `MusicPlayedEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1b | `MusicAddedEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1c | `PlaylistCreatedEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1d | `UserRegisteredEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1e | `UserLoggedInEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1f | `UserLoggedOutEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1g | `PasswordResetRequestedEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1h | `GenderSetEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |
| E1i | `MediaAccessedEvent.php` mevcut | `shared/src/Events/Domain/` | dosya | Doğrulandı |

### §4.2 Dispatcher + sözleşmeler

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| E2 | PSR-14 `EventDispatcherInterface` kullanımı | `shared/src/Events/EventDispatcher.php` | s.14, s.20 | Doğrulandı |
| E3 | `dispatch()` yöntemi | `shared/src/Events/EventDispatcher.php` | s.28 | Doğrulandı |
| E4 | Durdurulabilir olay kontrolü | `shared/src/Events/EventDispatcher.php` | s.37 | Doğrulandı |
| E5 | `MusicPlayedEvent` sınıf + `readonly` tarih alanı | `shared/src/Events/Domain/MusicPlayedEvent.php` | s.20, s.24 | Doğrulandı |
| E6 | `MusicPlayedEvent::eventName()` = `music.played` | `shared/src/Events/Domain/MusicPlayedEvent.php` | s.37–39 | Doğrulandı |

### §4.3 Repository + AIEngine

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| E7 | `ApiKeyRepository` — `IDatabaseRegistry` enjeksiyonu | `shared/src/Repository/ApiKeyRepository.php` | s.12–13, s.26 | Doğrulandı |
| E8 | `ApiKeyRepository` — `RAW_KEY_PATTERN` / `KEY_TYPES` | `shared/src/Repository/ApiKeyRepository.php` | s.32, s.37 | Doğrulandı |
| E9 | `AIEngine` — `MAX_RECOMMENDATIONS=20`, `MIN_SCORE=0.7`, `DIVERSITY_TARGET=0.30` | `shared/src/AI/AIEngine.php` | s.30–32 | Doğrulandı (CONFLICT C2 bağlantılı) |
| E10 | `AIEngine::getRecommendations()` | `shared/src/AI/AIEngine.php` | s.49 | Doğrulandı |

## §5 CONFIG + TEST KANITLARI

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| T1 | 7 subdomain kaydı | `shared/config/domain.php` | s.7–15 | Kısmi — 10 çocukla tam eşleşme doğrulanmadı (`⚠️`) |
| T2 | Domain event testi | `shared/tests/Events/DomainEventTest.php` | dosya | Doğrulandı |
| T3 | Dispatcher testi | `shared/tests/Events/EventDispatcherTest.php` | dosya | Doğrulandı |
| T4 | ApiKeyRepository testi (SQLite double) | `shared/tests/Repository/ApiKeyRepositoryTest.php` | dosya | Doğrulandı |
| T5 | phpunit + phpstan (scripts) | `shared/composer.json` | require-dev | Doğrulandı (`phpunit ^10.5`, `phpstan ^1.10`) |

## §6 UYUM KONTROLLERİ (master §9)

| # | Kontrol | Sonuç |
|---|---|---|
| C1 | Frontmatter 10 alan | §2 |
| C2 | Başlık `## §N` + tablo | Tüm kök dosyalar |
| C3 | Domain purity (Y1–Y6) | `bagimliliklar §4` — kural, ihlal yok |
| C4 | Tekil URL | `web-arastirma §16` dağıtım cetveli |
| C5 | Yer tutucu yasağı | `TBD/TODO/Yakında/daha sonra/...` taraması — temiz |
| C6 | `⚠️` işaretleme | §7 |
| C7 | `Dosya/Satır` biçiminde kod örneği | `kod-ornekleri §2–§6` |
| C8 | Çocuk index uyumu | Çocuk setlerinde kontrol edildi |

## §7 DOĞRULANMAYANLAR (`⚠️ VERIFICATION REQUIRED`)

| # | İddia | Neden | Kapatma yolu |
|---|---|---|---|
| ⚠️1 | `shared/config/domain.php` 7 subdomain ↔ 10 çocuk eşleşmesi | 3 çocuk için config satırı aranmadı | `domain.php` tekrar okunur, §5-T1 kapatılır |
| ⚠️2 | DI container (php-di) domain'de kullanımı | `Psr\` taraması container'ı kapsamaz | `new Container` taraması |
| ⚠️3 | `respect/validation` domain kurallarında | Kod okunmadı | `Respect\` taraması |
| ⚠️4 | `psr/cache` domain kullanımı | Tarama yapılmadı | `CacheItemPoolInterface` araması |
| ⚠️5 | Domain event ↔ integration event ayrımının 3 adedi | Dizin yapısı okunmadı (Events/ altında ayrı dizin) | `shared/src/Events/` alt ağacının listelenmesi |

## §8 DOĞRULAMA DÖNGÜSÜ PROTOKOLÜ

| Adım | Kural |
|---|---|
| 1 | İddia → kanıt satırı → tarih → durum |
| 2 | Kanıt yoksa satır `⚠️` + kapatma yolu |
| 3 | 6 ayda bir §3/§4/§5 yeniden taranır (URL'ler için §17 web-arastirma) |
| 4 | Yeni kanıt = yeni satır; eski satır silinmez, `Durum` güncellenir |
| 5 | §7 kapanışı yalnız kanıtla; `status: verify-required` → `verified` geçişi master §9-C1'e bağlı |

---

*Authority: Bayram Ali / Vault Steward · K008-domain doğrulama · 2026-10-12 · 27 SQL + 10 kod + 76 URL · 5 açık `⚠️`*
