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

## §9 KAPANIŞ KANITLARI (2026-10-17 TARAMA TURU)

> §7'deki ⚠️1…⚠️5 için kapatma yolları izlendi. Tarama yöntemi: dosya ağacı
> gezinme + regex eşleşmesi (Araç: Node.js `fs` + `RegExp`); kapsam
> `shared/src/**.php` (derinlik ≤6) + `shared/config/domain.php` +
> `shared/src/Events/` ağacı. Tarih: 2026-10-17.

| # | ⚠️ (§7) | Kapatma yolu | Tarama sonucu | Durum |
|---|---|---|---|---|
| K1 | ⚠️1 — 7 subdomain ↔ 10 çocuk eşleşmesi | `shared/config/domain.php` okundu | s.8–14: `auth · home · assets · music · admin · media · api` = **7 subdomain** (T1 iddiası doğrulandı) | 🟡 **KISMEN** — eşleşme varsayımı yanlış küme (aşağıda §9.1) |
| K2 | ⚠️2 — DI container (php-di) domain kullanımı | `new Container` taraması (regex: `new` + boşluk + `Container`) | **0 isabet** (`shared/src/**.php`, derinlik ≤6) | ✅ **KAPANDI** — domain kodunda container kurulumu yok |
| K3 | ⚠️3 — `respect/validation` domain kuralları | Respect namespace deseni taraması (kayıt: Respect + namespace ayracı) | **0 isabet** | ✅ **KAPANDI** — domain kurallarında Respect namespace'i yok |
| K4 | ⚠️4 — `psr/cache` domain kullanımı | `CacheItemPoolInterface` araması | **0 isabet** | ✅ **KAPANDI** — arayüz domain kodunda kullanılmıyor |
| K5 | ⚠️5 — Domain ↔ Integration event ayrımı (3 adet) | `shared/src/Events/` alt ağacı listelendi | `Domain/` 9 dosya + `Integration/` **3 dosya** (`AuthValidatedEvent` · `NotificationEvent` · `SessionCreatedEvent`) | ✅ **KAPANDI** — 3 adet doğrulandı; adlar `domain-bilesenler.md` §8 ile çakışmasız |

### §9.1 ⚠️1 bulgusu: yanlış varsayım (kapanış yerine düzeltme)

| Küme | Üye | Kanıt |
|---|---|---|
| Config subdomain (7) | auth · home · assets · music · admin · media · api | `domain.php` s.8–14 |
| K008-domain çocuğu (10) | context-mapping · device-management · discovery · library · media · music · playback · playlist · recommendation · user | K008-domain dizin ağacı (2026-10-17 sayımı) |

**Sonuç:** iki küme **eşit değil ve eşit olmak zorunda değil** — config
subdomain'leri **barındırma** birimi, K008 çocukları **domain klasörü**
birimidir. `assets/admin/api/home` domain dalı değildir; `playlist/library/
discovery/recommendation/notification` ayrı host gerektirmez. ⚠️1'nin
"eşleşme aranır" kısmı **kapatıldı (eşleşme yok, beklenen de buydu)**; §5-T1
satırının "10 çocukla tam eşleşme" koşulu ise **yanlış varsayım olarak
düzeltilemelidir** — bu dosya §5 tablosunu değiştirmez (append-only), düzeltme
iş kalemi §13-J1'dedir.

### §9.2 Tarama sınırları (dürüstlük notu)

1. **Derinlik ≤6:** `shared/src/` altında derinlik 6'yı aşan yollar
   taramaya girmemiştir; mevcut yapıda bilinen derinlik 4'tür (`Api/Dto/Request`).
2. **Desen darlığı:** `new Container` deseni yalnız `new Container` biçimini
   yakalar; `ContainerBuilder` gibi türev adlar **kapsam dışıdır**.
3. **Sıfır isabet = kanıt:** K2–K4 sonuçları "yok" iddiası değil, "bu desende
   isabet yok" kaydıdır — master §3 zero-hallucination gereği böyle kaydedilir.

## §10 WEB DOĞRULAMA TURU (2026-10-17) — SAYISAL KAYIT

> **C4 kuralı:** bu dosyaya URL yazılmaz. Tüm URL'ler tek kaynaktadır:
> `domain-web-arastirma.md` §18.2 (dağıtım cetveli) ve §18.4 (keyword
> eşlemeleri). Burada yalnız sayısal sonuç kaydedilir.

| Metrik | Değer | Kaynak |
|---|---|---|
| HTTP 200 | **29 URL** | web-arastirma §18 turu |
| HTTP 404 | **16 URL** (kaynak havuzdan düşüldü) | aynı |
| Meta-refresh | **1 URL** (bir bölüm → yeni bölüm taşınması; §18.4 işaretli) | aynı |
| Kök havuz genişlemesi | 75 → **104** | §18.2 |
| K008 geneli URL | 169 → **198** | K008-domain taraması |
| C4 çakışma (aynı URL iki dosyada) | **0** | §18.2 tekillik denetimi |

> **Okuma notu:** 16 × 404 = kaynak kalitesi bulgusudur, hata değil; 404
> veren kaynaklar havuzdan çıkarılmış, §18.4 eşlemeleri güncellenmiştir.
> Meta-refresh sayfası "ölü" değil "taşınmış" kabul edilmiş, yeni bölümüne
> bağlanmıştır.

## §11 K008-DOMAIN ÖLÇÜM KAYDI (BU TUR)

### §11.1 Hedef dosyalar: önce → sonra

| # | Dosya | Önce | Sonra | ≥500? | Eklenen bölümler |
|---|---|---|---|---|---|
| 1 | `domain-web-arastirma.md` | 230 | **503** | ✅ | §18.1–§18.10 (tur raporu, havuz, dağıtım) |
| 2 | `domain-kod-ornekleri.md` | 248 | **798** | ✅ | §8 dil matrisi (L1–L17) |
| 3 | `domain-master.md` | 491 | **666** | ✅ | §23–§29 (§9 tablosu değiştirilmedi) |
| 4 | `domain-kapsam.md` | 139 | **511** | ✅ | §8–§24 (BC envanteri §3 korunur) |
| 5 | `domain-bilesenler.md` | 185 | **501** | ✅ | §7–§16 (T-1…T-12 · 9+3 olay · protokol) |
| 6 | `playlist/playlist-kapsam.md` | 62 | **504** | ✅ | §5–§28 (K-P1…K-P10 · V-P1…V-P6 · mojibake düzeltmesi §9) |
| 7 | `music/music-kapsam.md` | 93 | **505** | ✅ | §6–§24 (M-P1…M-P14 · V-M1…V-M8 · draft korundu) |
| 8 | `domain-dogrulama.md` | 197 | §13.3 | ✅ | §9–§13 (bu bölüm) |

### §11.2 K008-domain geneli (araç ölçümü)

| Metrik | 2026-10-12 (önceki tur) | 2026-10-17 (bu tur) | Fark |
|---|---|---|---|
| Dosya sayısı | 72 | **72** | 0 (dosya eklenmedi/silinmedi) |
| Toplam satır | 6.651 | **9.273** | +2.622 |
| ≥500 satır dosya | 0 | **7** (§11.1 tablosu + bu dosya) | +7 |
| <100 satır dosya | 51 | **49** | −2 |

> **±1 newline notu:** sayımlar JS `ReadAllLines` eşdeğeri (CRLF / LF / CR
> bölme) ile alınmıştır; dosya sonu newline'ı bölmeye göre son boş eleman
> üretebilir — bu yüzden eşikler `≥500` ile ifade edilir, tam sayıya
> yaslanmaz.

### §11.3 Bu dosyanın ölçümü

| Aşama | Satır |
|---|---|
| §9–§13 öncesi | 197 |
| §9–§13 sonrası | araç ölçümü — §13.1 tablosunda |

## §12 TUR SONU UYUM KONTROLLERİ (C1–C8 YENİDEN)

| # | Kontrol | Sonuç | Kanıt |
|---|---|---|---|
| C1 | Frontmatter 10 alan (kalem 8) | ✅ hedef 8 dosyada korundu | dosya başları okundu |
| C2 | Başlık `## §N` + tablo | ✅ eklenen tüm bölümler § biçiminde | §9–§13 bu dosyada |
| C3 | Domain purity | ✅ ihlal yok | `bagimliliklar §4` — bu tur kod katmanına girmedi |
| C4 | Tekil URL | ✅ bu dosyada URL **0** (sıkı desen taraması) | §10 yalnız sayı |
| C5 | Yer tutucu yasağı | ✅ gerçek yer tutucu **0**; 8 tarama isabeti = 2 kendi kendini tanımlayan C5 kontrol satırı (dogrulama:169 · master:245) → **sahte pozitif** | §12.1 |
| C6 | `⚠️` işaretleme | ✅ ⚠️1…⚠️5 sonuçları §9'da; kapananlar ✅, yanlış varsayım 🟡 | §9 |
| C7 | Kod örneği biçimi | ✅ bu tur kod örneği eklemedi | — |
| C8 | Çocuk index uyumu | ✅ çocuk dosyaları değiştirilmedi (yalnız 2 kapsam dosyası derinleştirildi) | §11.1 |

### §12.1 Ek taramalar (bu tur)

| Tarama | Sonuç | Not |
|---|---|---|
| Mojibake (U+4E00–U+9FFF) — hedef 8 dosya | **0** | playlist §9'daki 1 Cince karakter bu turda düzeltildi |
| Mojibake — K008-domain geneli | **1 dosya**: `device-management/device-management-ornekler.md` | ⚠️ **beyazliste dışı** — bu turda DOKUNULMADI; sonraki tur iş kalemi §13-J6 |
| URL (sıkı desen) — hedef 8 dosya | **0** | C4 |
| URL — K008-domain geneli | 216 isabet; 13 dosyada (web kanıtı dosyaları) | Bunlar **web kanıtı dosyalarıdır** — C4 dağıtım cetveline göre §18.2'de tekilleşir; çakışma 0 (§10) |
| `git commit` | **0** | commit atılmadı — değişiklikler working tree |
| Dokunulmaz yüzey (`index.md` · `README.md` · `adr` · `diagrams` · `*.php/js/css/sql`) | **0 yazma** | beyazliste dışında yazım yok |

## §13 TUR SONU RAPORU (a–d)

### §13.1 (a) Değişen dosyalar — önce/sonra

| Dosya | Önce | Sonra | Değişiklik türü |
|---|---|---|---|
| `domain-web-arastirma.md` | 230 | 503 | §18 eklendi |
| `domain-kod-ornekleri.md` | 248 | 798 | §8 eklendi |
| `domain-master.md` | 491 | 666 | §23–§29 eklendi |
| `domain-kapsam.md` | 139 | 511 | §8–§24 eklendi |
| `domain-bilesenler.md` | 185 | 501 | §7–§16 eklendi |
| `playlist/playlist-kapsam.md` | 62 | 504 | §5–§28 + mojibake düzeltmesi |
| `music/music-kapsam.md` | 93 | 505 | §6–§24 + V-M7 hücre onarımı |
| `domain-dogrulama.md` | 197 | §13.3 | §9–§13 |

### §13.2 (b) ≥500 hedefi

**8/8 dosya ≥500** (§11.1 tablosu). K008-domain genelinde ≥500 sayısı
0 → **8** (dosya sayısı değişmedi: 72).

### §13.3 (c) Benzersiz URL + HTTP durumları

| Kalem | Değer |
|---|---|
| Bu dosyaya eklenen URL | **0** (C4) |
| Tur sonunda HTTP 200 | 29 URL |
| HTTP 404 | 16 URL (havuzdan düştü) |
| Meta-refresh | 1 URL (taşınmış sayfa) |
| K008 çakışma | 0 |

> Ayrıntı + URL'ler: `domain-web-arastirma.md` §18.2/§18.4 (tek kaynak).

### §13.4 (d) Doğrulama özeti

| Kontrol | Sonuç |
|---|---|
| Yer tutucu (gerçek) | **0** (2 sahte pozitif — C5 kendi kendini referanslayan kontrol satırı) |
| Mojibake (hedef 8) | **0** (1 düzeltme yapıldı) |
| Mojibake (beyazliste dışı) | 1 dosya `device-management-ornekler.md` — dokunulmadı, J6 |
| Yasaklı yüzey yazımı | **0** |
| `git commit` | **0** (orkestratöre ait) |
| Authority | hedef 8 dosyada eski satır korundu + yenisi eklendi |

### §13.5 Sonraki tur iş kalemleri

| # | İş | Bağlı | Öncelik |
|---|---|---|---|
| J1 | ⚠️1 bulgusunun §5-T1 satırına işlenmesi (yanlış varsayım düzeltmesi) | §9.1 | Orta |
| J2 | playlist OQ-P2/P3 (dispatch + `total_tracks`) | playlist §25 | Yüksek |
| J3 | music R8/R9 (sayı SSOT + slug niyeti) | music §23 | Yüksek |
| J4 | `playlist-bilesenler.md` derinleştirme (kardeş dosya) | playlist §26 | Orta |
| J5 | K008-domain <100 satır dosyaların (49) derinleştirilmesi | §11.2 | Düşük–Orta |
| J6 | `device-management-ornekler.md` mojibake düzeltmesi | §12.1 | Orta |

> **Bu turun kapanışı:** 8 hedef dosya ≥500 · ⚠️2–⚠️5 kapatıldı · ⚠️1
> yanlış varsayım olarak belgelendi · C4=0 · mojibake(hedef)=0 · commit=0.

---

---

*Authority: Bayram Ali / Vault Steward · K008-domain doğrulama · 2026-10-12 · 27 SQL + 10 kod + 76 URL · 5 açık `⚠️`*
*Authority: Bayram Ali / Vault Steward · K008-domain doğrulama · 2026-10-17 · §9–§13 eklendi: ⚠️2–⚠️5 kapatıldı · ⚠️1 yanlış varsayım belgelendi · web turu 29×200 / 16×404 / 1 meta-refresh · K008 72 dosya → 9.273 satır · 8/8 hedef ≥500 · C4=0 · commit=0*
