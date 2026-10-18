---
node_id: "K008-playlist-kapsam"
node_type: "mimari"
status: "active"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
  - "K008-domain/index"
evidence:
  - ".ai/.sql/mysql/coremusic_playlist.sql"
  - "shared/src/Events/Domain/PlaylistCreatedEvent.php"
validation: "2026-10-15 — §1-§3 satırları SQL dump satır aralıklarıyla doğrulandı (5 tablo, 109 satır)"
open_questions: ["OQ-P1: RouteTable'da kayıtlı playlistController implementasyonu 2026-10-15 grep'inde bulunamadı → ⚠️ VERIFICATION REQUIRED"]
group: mimari
---

# playlist — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15

## §1 KAPSAM

| # | Öğe | İçerik | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Tanım | Kullanıcının parçaları derlediği, adlandırdığı, sıraladığı koleksiyon kavramı; sistem genelinde tek şema (`coremusic_playlist` — 5 tablo) | `Dosya: .ai/.sql/mysql/coremusic_playlist.sql · satır 12-16 (CREATE DATABASE) · satır 84-107 (playlists, 17 kolon)` | doğrulandı |
| 2 | Sahip | Her playlist `user_id` zorunlu — sistem (anonim) playlist'i yok | `Satır 86: user_id binary(16) NOT NULL` (UUID v7) | doğrulandı |
| 3 | Ortak playlist (düzenleme) | `playlist_collaborators` tablosu: rol (`viewer`/`editor`/`admin`, varsayılan `viewer`) + davet akışı (`invited_at`/`accepted_at`) | `Satır 18-35 (tablo) · satır 22 (role ENUM)` | doğrulandı |
| 4 | Görünürlük | `visibility enum('public','private','unlisted') NOT NULL DEFAULT 'public'` — varsayılan **açık** | `Satır 90` | doğrulandı |
| 5 | Sıra | `playlist_tracks` ara tablosunda `position int NOT NULL` + `added_by` (nullable) + `added_at` | `Satır 67-82 (tablo) · satır 71 (position)` | doğrulandı |
| 6 | Liste türü | `playlist_type enum('user','ai','auto_generated','radio','curated') DEFAULT 'user'` — AI/otomatik/radyo liste sınıfı | `Satır 91 (ENUM)` | doğrulandı |
| 7 | Sayaç | `total_tracks`, `total_duration_sec`, `follow_count`, `like_count`, `play_count` önbellek kolonları | `Satır 93-97` | doğrulandı |
| 8 | Kapsam dışı | Parça metaverisi (→ [[music-kapsam]]), çalma motoru sırası uygulaması (→ [[playback-kapsam]]), sıradaki önerisi (→ [[recommendation-kapsam]]) | `domain-master.md §2 §3` | doğrulandı |

## §2 SINIR KOMŞULARI

| # | Komşu | Akış | Yön | Kanıt |
|---|---|---|---|---|
| 1 | media | parçaları listeye ekleme (playlist'e medya kimliğiyle referans) | playlist ← media | `Satır 70: music_id binary(16) NOT NULL` (medya parçası) |
| 2 | context-mapping | listeleme olaylarının domain dışına çıkışı | playlist → dış | `Dosya: shared/src/Events/Domain/PlaylistCreatedEvent.php · satır 20 (implements DomainEventInterface) · satır 53-61 (payload)` |
| 3 | discovery | kullanıcı listelerinin keşif akışına girmesi | discovery ← playlist | `Satır 90 (visibility='public' varsayılan)` + keşif `search_logs` — bkz [[discovery-kapsam]] |
| 4 | device-management | cihaza indirilen listeler | device-management ← playlist | `Dosya: .ai/.sql/mysql/coremusic_media.sql · satır 18-31 (device_playlists)` |

## §3 IDIOM (BIC)

| # | İdiom | Karar | Gerekçe |
|---|---|---|---|
| 1 | Sayaç kolonları | `total_tracks`/`total_duration_sec` SQL'de tutulur, JOIN ile sayım yok | `Satır 93-94` — liste başlığı anlık güncellenir |
| 2 | Yumuşak silme | `is_deleted` flag + `deleted_at` nullable birlikte | `Satır 100-101` |
| 3 | Sıralama bütünlüğü | `(playlist_id, music_id, position)` üçlü UNIQUE + `(playlist_id, position)` KEY | `Satır 78-79` — aynı parça-liste çifti teke iner, sıra index'lenir |

## §4 RİSK BAĞLANTILARI

| # | Risk | İlişki |
|---|---|---|
| 1 | R1 | `PlaylistCreatedEvent` tanımlı ama yayını repo'da yok (`->dispatch(` = 1 isabet) — bkz [[domain-riskler]] §R1 |
| 2 | R12 | 7 çocuk yalnız `index.md` ile baştı; playlist bu turda 6 dosyaya tamamlandı — bkz [[domain-riskler]] §R12 |

**Kardeş dosyalar:** [[index]] · [[playlist-dogrulama]] · [[playlist-web-arastirma]] · [[playlist-ornekler]] · [[playlist-bilesenler]]

## §5 AGGREGATE SINIRI ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Bu bölümdeki aggregate sınırları,
> §1–§3'teki SQL kanıtlarından türetilmiş bir **öneridir**; repo'da aggregate
> sınıfı yoktur (kanıt: `domain-bilesenler.md` §10 — playlist repository 0).
> Bağlayıcı hâle gelmesi, `domain-master.md` §24 kurallarıyla çapraz
> doğrulandığı andadır.

### §5.1 Önerilen sınır: Playlist (aggregate root)

| Öğe | Öneri | Dayanak (§1–§3 kanıtı) |
|---|---|---|
| Aggregate Root | `Playlist` — kimliği `playlists.id` (binary(16)) | s.84–87: PK + UUID v7 |
| İçi varlık (entity) | `PlaylistTrack` — sıra satırı | s.67–78: ara tablo + üçlü UNIQUE |
| İçi varlık (entity) | `PlaylistCollaborator` — rol satırı | s.18–35: rol ENUM + davet alanları |
| Dış referans (içi değil) | `music_id` → music BC | s.70 — yalnız kimlik, `music` kolonu yok |
| Dış referans (içi değil) | `user_id` (sahip) ve followers | s.86 sahiplik; s.37–45 takipçiler ayrı tablo |
| Sayaçlar (aggregate içi türetilmiş) | `total_tracks`, `total_duration_sec` | s.93–94 — §3 İdiom 1 |

### §5.2 Neden takipçiler/istatistikler dışarıda (öneri gerekçesi)

| Tablo | Satır | Öneri | Gerekçe |
|---|---|---|---|
| `playlist_followers` | s.37–45 | Aggregate **dışı** | Sayaç güncellemesi yüksek yazma oranı taşır; takip eylemi playlist invariant'ını bozmaz |
| `playlist_stats` | s.48–64 | Aggregate **dışı** | Oynatma/like sayaçları playback/music kaynaklıdır, playlist lifecycle'ına bağlı değildir |
| `playlist_collaborators` | s.18–35 | Aggregate **içi** | Rol, düzenleme yetkisini belirler — aggregate tutarlılık sınırının parçası |
| `playlist_tracks` | s.67–82 | Aggregate **içi** | Sıra bütünlüğü (s.78–79) ancak aggregate içinde korunabilir |

### §5.3 Önerilen ikinci aggregate (aday)

| Aday | Kapsam | Neden ayrı | Engel |
|---|---|---|---|
| `PlaylistStats` | `playlist_stats` (s.48–64) | Farklı yaşam döngüsü (sürekli güncelleme) | Sahiplik: playback mi playlist mi — `⚠️ VERIFICATION REQUIRED` |
| `UserLibraryPlaylist` (izleme/indirme) | `device_playlists` (media şeması, s.18–31) | Başka BC'nin şeması | Sahipliğin media olduğu varsayımı — `domain-kapsam.md` §3 ile çaprazlanmalı |

## §6 INVARIANT TABLOSU (SQL KANITLI)

Aşağıdaki tabloda **kanıt satırı** ile **invariant önerisi** ayrı sütundadır.
SQL satırı kanıttır; invariant cümlesi bu dosyanın türetilmiş önerisidir
(`domain-master.md` §24 bağlamı).

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant önerisi (kanıt DEĞİL) |
|---|---|---|---|
| K-P1 | s.31 `UNIQUE(playlist_id, user_id)` (collaborators) | Bir kullanıcı bir listede tek rolde | Aynı kullanıcı iki rolle eklenemez |
| K-P2 | s.78 `UNIQUE(playlist_id, music_id, position)` | Bir listede aynı pozisyonda tek parça | Aynı posizyon iki parçaya paylaştırılamaz |
| K-P3 | s.79 `(playlist_id, position)` KEY | Sıra çifti dizinli ve tektir | Sıra, aggregate içinde toplu atanmalıdır |
| K-P4 | s.90 `visibility enum('public','private','unlisted')` | Kapalı küme | Geçersiz görünürlük değeri kabul edilemez (VO `from()` önerisi §7) |
| K-P5 | s.91 `playlist_type enum('user','ai','auto_generated','radio','curated')` | Kapalı küme | Liste türü yalnız bu 5 değerden biri olabilir |
| K-P6 | s.93 `total_tracks` | Önbellek sayaç | Sayaç, `playlist_tracks` gerçek sayısıyla tutarlı kalmalı (§6.1 uyumsuzluk notu) |
| K-P7 | s.100 `is_deleted` + s.101 `deleted_at` | Yumuşak silme ikilisi | Silme yalnız ikisi birlikte set edilmeli — tek başına alan kullanımı §3 İdiom 2 |
| K-P8 | s.34 FK `ON DELETE CASCADE` | Ana kayıt silinince altlar iner | Repository seviyesinde `is_deleted` yayılımı ayrıca tanımlanmalı (cascade DB'de, soft-delete domain'de) |
| K-P9 | s.86 `user_id NOT NULL` | Her listenin sahibi var | Sahipsiz liste oluşturulamaz (anonim liste yok — §1/2) |
| K-P10 | s.90 `DEFAULT 'public'` | Yeni liste varsayılan açık | Yaratım akışında gizlilik bilinçli seçilmelidir — varsayılan-güvenlik tartışması §11 OQ-P4 |

### §6.1 Uyumsuzluk riski (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| `total_tracks` yazma anında güncelleniyor | §1/3 idiom satırı (s.93 varlığı) | ⚠️ GÜNCELLEME NOKTASI TARANMADI — repo kodunda `total_tracks` yazımı görülmedi |
| Aynı parça iki kez eklenemez | s.78 üçlü UNIQUE | DB kanıtı var; domain katmanı hatayı önceden yakalamıyor olabilir (öneri: aggregate kontrolü) |
| Sıra boşlukları | s.79 dizini | Boşluk/varlık ikisi de DB'de serbest — sıra yeniden numaralandırma politikası `⚠️ VERIFICATION REQUIRED` |

## §7 VALUE OBJECT ÖNERİLERİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Adaylar `domain-master.md` §25 (V-1…V-7)
> kuralıyla uyumlu üretilmiştir; depoda VO sınıfı yoktur.

| # | VO | Dayanak kolon | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-P1 | `PlaylistVisibility` | s.90 | `public` / `private` / `unlisted` | `from(string)` fabrika — geçersizde ret; `isPublic()` sorgu yardımcısı |
| V-P2 | `PlaylistType` | s.91 | `user` / `ai` / `auto_generated` / `radio` / `curated` | `from(string)` fabrika + `isMachineGenerated()` (ai/auto_generated/radio) |
| V-P3 | `TrackPosition` | s.71 (`position int NOT NULL`) | ≥ 0 tam sayı | `advanceTo` / `swap` işlemleri; negatif ret |
| V-P4 | `CollaboratorRole` | s.22 (`role ENUM`) | `viewer` / `editor` / `admin` | Hiyerarşi: viewer < editor < admin — `atLeast()` karşılaştırması |
| V-P5 | `PlaylistCounters` | s.93–97 | 5 sayaç | `totalTracks` + `totalDurationSec` birlikte güncelleme; tekil değişiklik ret |
| V-P6 | `PlaylistId` (UUID v7) | s.86/s.84 | binary(16) karşılığı | Kimlik sarmalayıcı — `domain-bilesenler.md` §9 C-9 borcu ile doğrudan ilişkili |

### §7.1 VO adaylarının test edilebilirlik notu

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-P1 | Geçersiz görünürlük `from()` çağrısında ret üretmeli |
| V-P3 | `swap(1,3)` sonrası pozisyonlar değişmeli, küme korunmalı |
| V-P4 | `admin.atLeast(editor)` = doğru; `viewer.atLeast(editor)` = yanlış |
| V-P5 | `totalTracks` değişince `totalDurationSec` elle değiştirilememeli |

## §8 DOMAIN EVENT ÖNERİLERİ (MEVCUT KANITLA SINIRLI)

### §8.1 Var olan olay (kanıtlı)

| Olay | Dosya kanıtı | Ad | Payload |
|---|---|---|---|
| `PlaylistCreatedEvent` | `shared/src/Events/Domain/PlaylistCreatedEvent.php` s.20 (implements) · s.39 (`eventName`) | `playlist.created` | playlistId, createdBy, name, timestamp |

### §8.2 Eksik olaylar (öneri — uydurma YASAK, kayıt `❌/⚠️` olarak kalır)

| # | Önerilen olay | Tetikleyici | Mevcut durum |
|---|---|---|---|
| E-P1 | `playlist.track_added` / `track_removed` | `playlist_tracks` satır değişimi | ❌ DOSYA YOK — `Events/Domain/` 9 dosyada yok |
| E-P2 | `playlist.visibility_changed` | s.90 kolon değişimi | ❌ DOSYA YOK |
| E-P3 | `playlist.collaborator_invited` | s.26–27 davet alanları | ❌ DOSYA YOK |
| E-P4 | `playlist.followed` / `unfollowed` | s.37–45 takipçi satırı | ❌ DOSYA YOK |
| E-P5 | `playlist.deleted` (soft) | s.100–101 flag | ❌ DOSYA YOK |

> **Kural:** E-P1…E-P5 satırları `domain-bilesenler.md` §12 protokolüne tabidir:
> olay ancak dosya + `class` satır kanıtıyla envantere girer.

## §9 REPOSITORY SÖZLEŞMESİ ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda playlist repository arayüzü yoktur
>(kanıt: `domain-bilesenler.md` §10 — Repository sütunu 0; §9 C-1).

| # | Yöntem (öneri) | Dönüş | Bağlı invariant |
|---|---|---|---|
| R-P1 | `findByIdHex(string $id): ?array` | kayıt veya null | K-P9 (sahip zorunlu) |
| R-P2 | `findByOwner(string $userId): array` | liste kümesi | §1/2 |
| R-P3 | `create(array $data): array` | yeni kayıt | K-P9, K-P10 |
| R-P4 | `addTrack(string $playlistId, string $musicId, int $position): bool` |başarısızlık yerine başarı/başarısız | K-P2, K-P3, V-P3 |
| R-P5 | `reorder(string $playlistId, array $positions): void` | — | K-P2, K-P3 (toplu atama) |
| R-P6 | `setVisibility(string $id, string $visibility): void` | — | K-P4, V-P1 |
| R-P7 | `softDelete(string $id): void` | — | K-P7 (flag + deleted_at birlikte) |
| R-P8 | `syncCounters(string $id): void` | — | K-P6 (§6.1 uyumsuzluk notu) |
| R-P9 | `attachCollaborator(string $playlistId, string $userId, string $role): bool` | — | K-P1, V-P4 |

### §9.1 Sözleşme tipi notu

Önerilen dönüş tipleri `array` (mevcut `IUserRepository` dili ile uyum —
`IUserRepository.php` s.7–50 tüm dönüşler `?array`/`array`/`void`/`bool`).
Tip sarmalayıcı (VO/Entity nesnesi) `domain-bilesenler.md` §9 C-9 borcunun
kapsama alınmasıyla mümkündür — bu dosya tek başına tip kararı vermez.

## §10 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yönlü veri | Alan adı | Ayrım kuralı |
|---|---|---|---|
| media | playlist ← media | `music_id` (s.70) | Playlist, parça **metaverisini kopyalamaz** — yalnız kimlik tutar |
| music | playlist → music | Aynı `music_id` referansı | Parça adı/süresi read-only gelir; playlist üzerinde düzenlenemez |
| playback | playlist → playback | `position` (s.71) | Sıra **talep** playback'e iletilir; oynatma sırası playback'de uygulanır |
| discovery | playlist → discovery | `visibility='public'` (s.90) | Yalnız public listeler keşif akışına girer (§2/3) |
| recommendation | playlist → recommendation | `playlist_type='ai'` (s.91) | AI listeleri öneriden beslenir, öneriyi **üretmez** |
| user/auth | playlist ← user | `user_id` (s.86), `added_by` (s.71) | Kimlik doğrulama user BC'de; playlist yalnız sahiplik tutar |
| device-management | playlist ↔ media | `device_playlists` (media şeması s.18–31) | Cihaz listesi media şemasında — sahiplik `⚠️ VERIFICATION REQUIRED` |

> **Sınır cümlesi (öneri):** *Playlist; sahibi olan, düzenlenen, sıralanan ve
> görünürlüğü yönetilen parçacık koleksiyonudur. Parça verisi, oynatma ve
> keşif mantığı bu sınırın dışındadır.* — bu cümle §1 kanıtlarından türetilmiştir.

## §11 AÇIK SORULAR (OQ) VE RİSK DETAYI

| # | Soru | Mevcut kayıt | Kapanış için gereken |
|---|---|---|---|
| OQ-P1 | `playlistController` implementasyonu | frontmatter'da kayıtlı (2026-10-15 grep: bulunamadı) | Route/controller taraması |
| OQ-P2 | `PlaylistCreatedEvent` yayını var mı? | §4 R1: `->dispatch(` = 1 isabet ama playlist kaynağı değil | dispatch çağrı noktasının okunması |
| OQ-P3 | `total_tracks` hangi kod tarafında yazıyor? | §6.1 — taranmadı | `total_tracks` grep'i |
| OQ-P4 | Varsayılan `visibility='public'` güvenlikli mi? | K-P10 — tasarım kararı | security-engineer görüşü (OWASP veri ifşası) |
| OQ-P5 | `device_playlists` sahibi media mi, playlist mi? | §5.3 — varsayım | `domain-kapsam.md` §3 ile çapraz okuma |
| OQ-P6 | Sıra boşlukları nasıl onarılıyor? | §6.1 — politika yok | reorder akışı kanıtı |

### §11.1 Risk bağlantıları (§4 genişletmesi)

| Risk | Bu dosyadaki iz | Etki |
|---|---|---|
| R1 (event yayını yok) | OQ-P2 + §8.2 | Olay envanteri kâğıt üzerinde kalır |
| R12 (kapsam dosyaları yarım) | §1–§11 ile bu dosya tamamlandı | Tamamlanma oranı arttı; bağımlı `playlist-bilesenler.md` hâlâ §3–§6 düzeyinde |
| Yeni (kayıt: OQ-P4) | Güvenlik varsayılanı | Veri ifşası riski — `security-engineer` yönlendirmesi gerekir |

## §12 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Bu testler yazılmamıştır; yalnız
> senaryo taslağıdır (`qa-engineer`/`test-writer` kapsamı).

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| T-P1 | Aynı pozisyonda iki parça ekleme | Ret / ikinci eklem pozisyonu kaydırılır | K-P2 |
| T-P2 | Geçersiz görünürlük değeri yazma | VO `from()` ret | K-P4, V-P1 |
| T-P3 | Silme sonrası flag tutarlılığı | `is_deleted=1` + `deleted_at` dolu | K-P7 |
| T-P4 | Sahipsiz liste oluşturma | Ret | K-P9 |
| T-P5 | Aynı kullanıcıyı iki rolle ekleme | Ret | K-P1 |
| T-P6 | Sıralama sonrası sayaç tutarlılığı | `total_tracks` = gerçek satır sayısı | K-P6 |
| T-P7 | `playlist.created` yayınlanınca payload | 4 anahtar (§8.1) | `DomainEventInterface` sözleşmesi |
| T-P8 | Varsayılan görünürlük bilinçli seçimi | Yaratım akışında bildirim/uyarı | K-P10, OQ-P4 |

## §13 ÖLÇÜM VE KAPANIŞ

| Metrik | Değer | Yöntem |
|---|---|---|
| Ekleme öncesi | 62 satır | satır sayım aracı |
| Eklenen bölüm | §5–§13 | append-only; §1–§4 + kardeş bağlantıları korundu |
| Invariant kaydı | 10 (K-P1…K-P10) | hepsi SQL satır kanıtlı, öneri sütunu ayrı |
| VO adayı | 6 (V-P1…V-P6) | hepsi `TASARIM ÖNERİSİ` etiketli |
| Olay kaydı | 1 kanıtlı + 5 `❌ DOSYA YOK` | `Events/Domain/` 2026-10-17 listesi |
| Repository sözleşmesi | 9 yöntem (öneri) | `IUserRepository` dili ile uyumlu |
| Açık soru | 6 (OQ-P1…OQ-P6) | OQ-P1 mevcut kayıttan taşındı |
| Yeni URL | 0 | C4 — URL'ler yalnız `domain-web-arastirma.md` §18.2 |
| Authority | eski (2026-10-15) korundu + yeni (2026-10-17) | master §22 M-6 |

> **Sonraki tur:** OQ-P2 (dispatch) ve OQ-P3 (`total_tracks`) tek tarama ile
> kapanır; OQ-P4 security onayı ister.

## §14 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

> Her karar için seçilen, reddedilen ve neden ayrı sütundadır. Reddedilenler
> bu dosyanın §5–§13 önerilerinden türetilmiştir; yeni kanıt iddiası taşımaz.

| # | Karar | Seçilen | Reddedilen | Neden |
|---|---|---|---|---|
| D-P1 | Aggregate sınırı | Parça kimliği + sıra içeren dar liste aggregate (§5.1) | Parça metaverisini de yutan geniş aggregate | s.70 `music_id` yalnız kimlik tutar (§10 ayrım kuralı) — kopya veri tutarsızlık riski doğurur |
| D-P2 | Sıra kaynağı | `playlist_tracks.position` (s.71, s.78, s.79) | Ayrı sıra/offset tablosu | Mevcut şema üçlü UNIQUE + dizin ile yeterli (K-P2, K-P3); yeni tablo ek maliyet |
| D-P3 | Sayaç tutarlılığı | Yazma anında domain güncellemesi + `syncCounters` onarım yolu (R-P8) | Yalnız trigger ile DB güncellemesi | DB trigger domain'de test edilemez (§6.1 uyumsuzluk notu); çift savunma önerilir |
| D-P4 | Silme modeli | Yumuşak silme ikilisi alan bazlı (K-P7) | Fiziksel silme | s.100–101 alanı zaten var; fiziksel silme FK cascade'i (s.34) ile çelişkili davranır |
| D-P5 | Kimlik biçimi | binary(16) UUID sarmalayıcı (V-P6) | metin UUID | s.84/s.86 kolon tipi zaten binary(16) — alan tipine sadık kalır |
| D-P6 | Olay envanteri | Yalnız dosya kanıtlı `playlist.created` (§8.1) | Kâğıt üzerinde 5 eksik olayı var saymak | Uydurma olay envanteri zero-hallucination'ı ihlal eder — E-P1…E-P5 `❌` kalır |

## §15 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

> Bu akışlar depoda implement edilmemiştir; §6 invariantları ve §9 repository
> sözleşmesinden türetilmiş senaryo taslağıdır. Her adım bağlı invariant'ı gösterir.

### §15.1 Liste yaratımı

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | kullanıcı | Kimlik doğrulama user/auth BC'de tamamlanır (§10) | K-P9 |
| 2 | playlist | `user_id` dolu olmadan kayıt ret edilir | K-P9 (s.86) |
| 3 | playlist | `visibility` açıkça seçilir; seçilmezse DB default'u devreye girer | K-P10 (s.90), OQ-P4 |
| 4 | playlist | `playlist_type` 5 değerden biri olmalıdır | K-P5 (s.91) |
| 5 | playlist | `playlist.created` yayılır (varsa) — payload 4 anahtar | §8.1, T-P7 |

### §15.2 Parça ekleme ve sıralama

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | playlist | Eklenen `music_id` media/music sınırından gelir; metave kopyalanmaz | §10 |
| 2 | playlist | Aynı (playlist, music, position) üçlüsü ikinci kez kabul edilmez | K-P2 (s.78) |
| 3 | playlist | Pozisyon toplu atanır — ara boşluk politikası OQ-P6 | K-P3 (s.79) |
| 4 | playlist | `total_tracks` / `totalDurationSec` birlikte güncellenir | K-P6 (s.93), V-P5 |
| 5 | playback | Sıra talebi playback'e iletilir; uygulama playback'de | §10 |

### §15.3 Görünürlük ve takip

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | playlist | Yalnız `public|private|unlisted` kabul edilir | K-P4 (s.90), V-P1 |
| 2 | discovery | Yalnız `public` kayıt keşif akışına girer | §2/§10 |
| 3 | playlist | Takip satırı ekleme/çıkarma olayı `.followed` | E-P4 — ❌ DOSYA YOK |
| 4 | playlist | Takipçi sayaçları aggregate dışında (§5.3) | D-P1 |

### §15.4 İş birliği ve silme

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | playlist | Kullanıcı tek rolde tutulur; ikinci rol ret | K-P1 (s.31), V-P4 |
| 2 | playlist | Rol hiyerarşi kontrolü `atLeast()` ile | V-P4 |
| 3 | playlist | Soft delete: flag + `deleted_at` birlikte | K-P7 (s.100–101) |
| 4 | repository | FK cascade (s.34) ile alt satırlar iner; domain `is_deleted` yayılımı ayrıca tanımlanmalı | K-P8 |

## §16 OLGUNLUK BANDI (BU DOSYA ÖLÇÜSÜNDE)

> Bantlar kanıt durumuna göre atanmıştır: **Kırmızı** = kanıt yok/iddia yok,
> **Sarı** = kanıt kısmi veya öneri, **Yeşil** = satır kanıtlı.

| Alan | Bant | Gerekçe |
|---|---|---|
| Şema invariantları | 🟢 Yeşil | K-P1…K-P10 hepsi `coremusic_playlist.sql` satır kanıtlı |
| Olay envanteri | 🟡 Sarı | 1 dosya kanıtlı + 5 `❌ DOSYA YOK` (§8) |
| Repository sözleşmesi | 🔴 Kırmızı | Depoda playlist repository arayüzü yok (§9, kanıt §10 C-1) |
| VO katmanı | 🔴 Kırmızı | Depoda VO sınıfı yok — tümü öneri (§7) |
| Akış implementasyonu | 🔴 Kırmızı | §15 tamamı taslak; controller kanıtı OQ-P1'de açık |
| Sayaç tutarlılığı | 🟡 Sarı | Varlık kanıtlı (s.93), yazma noktası taranmadı (§6.1) |
| Komşu sınır ayrımı | 🟡 Sarı | 7 komşu çizelgesi (§10); `device_playlists` sahipliği OQ-P5 |
| Güvenlik varsayılanı | 🟡 Sarı | `DEFAULT 'public'` kanıtlı; uygunluğu OQ-P4 açık |

## §17 RİSK VE TEKNİK BORÇ GENİŞLETME

| # | Borç/İş kalemi | Köken | Öncelik | Kapanış yolu |
|---|---|---|---|---|
| W-P1 | `total_tracks` yazma noktası bilinmiyor | §6.1 / OQ-P3 | Yüksek | Tek grep — `domain-dogrulama.md` turuna yazılır |
| W-P2 | `PlaylistCreatedEvent` dispatch kanıtı yok | §4 R1 / OQ-P2 | Yüksek | `->dispatch(` çağrı noktasının okunması |
| W-P3 | Playlist repository arayüzü yok | §9 / C-1 | Yüksek | Arayüz tasarımı + `IUserRepository` diliyle uyum (§9.1) |
| W-P4 | VO sınıfları yok | §7 | Orta | `domain-bilesenler.md` §9 C-9 tip sarmalayıcı borcu ile birlikte |
| W-P5 | E-P1…E-P5 olayları yok | §8.2 | Orta | Her olay dosyası + `class` + `eventName` kanıtıyla eklenir (§12 protokolü) |
| W-P6 | Sıra boşluk onarım politikası yok | §6.1 / OQ-P6 | Orta | Reorder akışı kararı + T-P1/T-P2 testleri |
| W-P7 | `device_playlists` sahipliği belirsiz | §5.3 / OQ-P5 | Düşük | `domain-kapsam.md` §3 çapraz okuma |
| W-P8 | Varsayılan görünürlük güvenlik değerlendirmesi yok | K-P10 / OQ-P4 | Yüksek | `security-engineer` incelemesi |

## §18 OKUMA YOLU (BU DOSYADAN)

| Amac | Okuma sırası |
|---|---|
| Sınırı anlamak | §1 → §2 → §10 → §5 |
| Invariant'lara hukuki dayanak | §6 → §6.1 → kaynak `coremusic_playlist.sql` satır numaraları |
| Tasarım önerisi üretmek | §5 → §7 → §9 → §12 (hepsi etiketli öneri) |
| Olay envanteri | §8.1 (kanıt) → §8.2 (eksik, `❌`) → `domain-bilesenler.md` §12 protokolü |
| Kapanış | §11 (OQ listesi) → §17 (iş kalemleri) → §20 (ölçüm) |

> **Çapraz referans:** Kapsam komşuları `domain-kapsam.md` §3 ile; bileşen/olay
> protokolü `domain-bilesenler.md` §7–§12 ile; dil örnekleri
> `domain-kod-ornekleri.md` §8 ile birlikte okunur. URL kanıtı bu dosyada yoktur —
> C4 gereği web kanıtı `domain-web-arastirma.md` §18.2'de tek kaynaktır.

## §19 SONRAKİ TUR İŞ KALEMLERİ

| # | İş | Bağlı OQ/Borç | Çıktı |
|---|---|---|---|
| J-P1 | `total_tracks` grep | W-P1 / OQ-P3 | Ya yazma noktası kanıtlanır ya §6.1 satırı `❌` olarak güncellenir |
| J-P2 | dispatch okuma | W-P2 / OQ-P2 | OQ-P2 kapanır veya R1 teyit edilir |
| J-P3 | Güvenlik görüşü | W-P8 / OQ-P4 | `security-engineer` kararı ADR'ye taşınır |
| J-P4 | Repository arayüzü | W-P3 | Yeni arayüz dosyası — yalnız K008 izniyle |
| J-P5 | Çapraz okuma | W-P7 / OQ-P5 | `domain-kapsam.md` §3 ile hizalama |

> Bu işler `domain-dogrulama.md` §13 (tur sonu) içinde raporlanır; URL eklemez
> (C4) yalnız sayısal sonuç yazar.

## §20 NİHAİ ÖLÇÜM (BU TUR)

| Metrik | Değer | Yöntem |
|---|---|---|
| Ekleme öncesi (§13 kapanışındaki durum) | 263 satır | satır sayım aracı (JS ReadAllLines eşdeğeri) |
| Eklenen bölüm | §14–§27 | append-only; §1–§13 + Authority blokları korundu |
| Yeni URL | 0 | C4 — tek URL kaynağı `domain-web-arastirma.md` §18.2 |
| Yeni doğrulanamayan iddia | 0 | her öneri `TASARIM ÖNERİSİ — kanıt DEĞİL` etiketli; olay yokluğu `❌` |
| Mojibake | 0 | §9 R-P4 hücresindeki Cince karakter bu turda düzeltildi |
| Authority | eski (2026-10-15) korundu + 2 Authority satırı eklendi (toplam 3) | master §22 M-6 |


## §21 SQL ERİŞİM VE DİZİN NOTU (KANIT / TARAMA AYRIMI)

> Bu bölümde **kanıtlanan** ile **tarasılmayan** ayrı sütundadır. Tarama
> yapılmayan her satır `⚠️ VERIFICATION REQUIRED` olarak kalır; tahmin yazılmaz.

| # | Erişim paterni | Bilinen kanıt | Tarama durumu |
|---|---|---|---|
| A-P1 | Listeye parça ekleme (3 kolon yazma) | s.78 üçlü UNIQUE + s.79 dizin | 🟢 Kanıt var — çakışma DB'de reddedilir |
| A-P2 | Sahibin listelerini çekme | s.86 `user_id` | 🟡 Sorgu yazılımı taranmadı — `⚠️ VERIFICATION REQUIRED` |
| A-P3 | Public keşif listesi çekme | s.90 `visibility` | 🟡 Aynı — sorgu kanıtı yok |
| A-P4 | Sayaç okuma (`total_tracks`) | s.93 kolonu | 🟡 Okuma yolu taranmadı; yazma noktası W-P1 |
| A-P5 | Takipçi sayımı | s.37–45 takipçi satırları | 🔴 Sayım aggregate dışında (§5.3) — sayaç kolonu var mı taranmadı |
| A-P6 | Cascade silme | s.34 `ON DELETE CASCADE` | 🟢 Kanıt var — DB davranışı |

**Okuma kuralı:** 🟡/🔴 satırları bu dosyaya performans iddiası olarak
**yazılamaz** (ör. "dizinsiz sorgu yavaştır" iddiası kanıtsızdır). İddia için
sorgu planı veya kod kanıtı gerekir.

## §22 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

> Yön ve niyet §2/§10 komşu çizelgesinden türetilmiştir; **çağrı mekanizması**
> (HTTP/koleksiyon/event) iddiası taşımaz — mekanizma kanıtı ayrı tur işidir.

| # | Çağıran → Çağrılan | Niyet | Bağlı kural |
|---|---|---|---|
| P-BC1 | playlist → user/auth | Sahiplik kimliğini doğrulatmak | K-P9 |
| P-BC2 | playlist → media/music | `music_id` geçerliliği / süre bilgisi | §10 |
| P-BC3 | playlist → playback | Sıra talebini iletmek | §10 |
| P-BC4 | discovery → playlist | Public listeleri okumak | §2 |
| P-BC5 | recommendation → playlist | `ai` listelerini okumak | §10 |
| P-BC6 | device-management → playlist (?) | Cihaz listesi eşlemesi | OQ-P5 — yön belirsiz `⚠️` |
| P-BC7 | playlist → (olay yayını) | `playlist.created` | OQ-P2 — dispatch kanıtsız `⚠️` |

## §23 ROL VE SORUMLULUK MATRİSİ (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Sorumluluk atamaları gelecekteki ekip
> düzenine yöneliktir; depoda bu rollerin dağılımına dair kanıt yoktur.

| Karar alanı | Önerilen sahip | Neden | Ataç |
|---|---|---|---|
| Invariant bütünlüğü (K-P1…K-P10) | playlist domain sahibi | Şema ile domain arasındaki fark bu dosyada (§6.1) | §6 |
| Sıra politikası (boşluk onarımı) | playback ile ortak karar | Sıra talebi playback'de uygulanır | OQ-P6, §15.2 |
| Görünürlük güvenliği | `security-engineer` | `DEFAULT 'public'` OWASP veri ifşası bağlamı | OQ-P4, W-P8 |
| Olay envanteri genişlemesi | `domain-bilesenler.md` protokolü | Uydurma olay yasağı merkezi | §8.2, J-P5 |
| Repository sözleşmesi | `architect` + `csharp-developer`(PHP yüzeyi için `php-pro`) | `IUserRepository` diliyle hizalama | §9.1, W-P3 |

## §24 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Bir kullanıcı yeni bir liste oluşturduğunda zincir şöyledir: kimlik user/auth
sınırında doğrulanır; playlist sınırı `user_id` olmadan kaydı reddeder (K-P9);
`visibility` bilinçli seçilmezse DB varsayılanı devreye girer ve kayıt açık
oluşur (K-P10, güvenlik notu OQ-P4); `playlist_type` kapalı kümeden dışına
çıkmaz (K-P5). Parça eklenirken metave kopyalanmaz, yalnız `music_id` tutulur
(§10); aynı üçlü ikinci kez girmez (K-P2); pozisyon toplu atanır (K-P3);
sayaçlar birlikte güncellenir (K-P6, V-P5). Görünürlük değişirse yalnız
`public` kayıt keşfe açılır (§15.3). Silme yumuşaktır: flag ile tarih birlikte
doludur (K-P7), FK cascade'i alt satırları indirirken domain'in `is_deleted`
yayılımı ayrı tanımlanmalıdır (K-P8). Bu zincirin her halkası ya satır kanıtlı
ya da açıkça öneri etiketlidir — arada kalan tek halka, yayını bilinmeyen
`playlist.created` olayıdır (OQ-P2, W-P2).

## §25 TUR SONU KONTROL LİSTESİ (ÖNÜMÜZDEKİ TUR İÇİN)

| # | Kontrol | Beklenen sonuç | Bağlı iş |
|---|---|---|---|
| C-P1 | `total_tracks` yazma noktası taraması | Ya kanıt ya §6.1'de `❌` | J-P1 |
| C-P2 | `->dispatch(` çağrı okuması | OQ-P2 kapanışı veya R1 teyidi | J-P2 |
| C-P3 | Güvenlik görüşü | OQ-P4 kararı | J-P3 |
| C-P4 | `domain-kapsam.md` §3 çapraz okuma | OQ-P5 hizalanması | J-P5 |
| C-P5 | `playlist-bilesenler.md` derinleştirme | Kardeş dosyanın §3–§6'dan yukarı çıkması | bağımlı iş (§11.1 R12) |
| C-P6 | Bu dosyanın §14–§25 ölçümlerinin tekrarı | Satır sayısı + mojibake + URL=0 | rapor (a–d) |

---

## §26 KOMŞU DOSYA DAVET LİSTESİ (BU DOSYADAN ÇAĞRILANLAR)

| Dosya | Neden | Bu dosyadaki ataç |
|---|---|---|
| `domain-master.md` §24/§25 | Invariant/VO üretim kuralı | §6, §7 başlıkları |
| `domain-kapsam.md` §3 | BC envanteri + komşu sahiplik | §10, OQ-P5 |
| `domain-bilesenler.md` §7–§12 | T-1…T-12 eşleme, olay protokolü | §8.2 kural cümlesi, C-P5 |
| `domain-kod-ornekleri.md` §8 | Dil matrisi (PHP/JS örnekleri) | §9.1 tip dili |
| `domain-web-arastirma.md` §18.2 | Tek URL kaynağı (C4) | §18/§20 notu |
| `domain-dogrulama.md` §13 | Tur sonu raporu | §19, §25 |
| `playlist-bilesenler.md` | Kardeş bileşen dosyası — hâlâ §3–§6 düzeyi | §11.1 R12, C-P5 |

## §27 OKUYUCU UYARILARI (BU DOSYANIN SINIRLARI)

1. **Bu dosya şema + öneri dosyasıdır, kod değildir.** §9 repository
   sözleşmesi ve §7 VO'lar depoda yoktur; okuyucu "var" sanıp çağıramaz.
2. **URL aramayın.** C4 gereği tüm web kanıtı
   `domain-web-arastirma.md` §18.2'dedir; buradaki hiçbir cümle kaynağı
   URL ile kanıtlamaz.
3. **§15 akışları test edilmemiştir.** §12'deki T-P1…T-P8 taslaktır —
   `qa-engineer` kapsamına devredilmiştir.
4. **`❌ DOSYA YOK` satırları eksiklik değil, kasıtlı kayıttır.** Uydurma
   olay eklemek protokolü (§8.2) ihlal eder.
5. **Ölçüm satırları tahminle doldurulmaz.** §13/§20 değerleri satır sayım
   aracıyla alınmıştır (±1 newline riski ölçüm yöntemine dahildir).

---

## §28 ÖLÇÜM YÖNTEMİ NOTU (TEKRARLANABİLİRLİK)

| Soru | Cevap |
|---|---|
| Satır nasıl sayılır? | Dosya tam okunur, satır sonları `\r\n` / `\n` / `\r` ile bölünür — JS `ReadAllLines` eşdeğeri |
| Dosya sonu newline'ı sayılır mı? | Bölmeye göre son boş eleman dahil olabilir (±1 newline riski) — bu yüzden eşik değerleri `≥500` ile ölçülür |
| Neden tahmin yok? | §13'te ilk yazılan tahmin yanlıştı, araç ölçümüyle düzeltilmiş bir kayıt var — aynı hata tekrarlanmaz |
| Mojibake nasıl taranır? | U+4E00–U+9FFF (CJK) aralığı dosyanın tamamında aranır; isabet varsa düzeltilir ve §20'ye yazılır |
| URL ihlali nasıl taranır? | Sıkı desen: `https?://` takibi veya `www.` + alfanümerik — `http` tek başına desen açıklamasıdır, isabet sayılmaz; gerçek isabet 0 beklenir (C4) |

---

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · K008-playlist-kapsam · 2026-10-17 · §5–§13 eklendi: aggregate sınırı önerisi · K-P1…K-P10 invariant · V-P1…V-P6 VO · E-P1…E-P5 eksik olay · R-P1…R-P9 repository · 6 açık soru · 8 test senaryosu
**Authority:** Bayram Ali / Vault Steward · K008-playlist-kapsam · 2026-10-17 (tur 2) · §14–§25 eklendi: karar matrisi · akış senaryoları · olgunluk bandı · borç kalemleri · BC etkileşim · rol matrisi · kontrol listesi · §9 mojibake düzeltildi
