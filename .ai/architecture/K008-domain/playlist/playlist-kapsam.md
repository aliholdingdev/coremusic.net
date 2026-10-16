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

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
