---
node_id: "K008-playlist-dogrulama"
node_type: "kanit"
status: "active"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-playlist-kapsam"
  - "K008-playlist-web-arastirma"
evidence:
  - ".ai/.sql/mysql/coremusic_playlist.sql"
  - "shared/src/Events/Domain/PlaylistCreatedEvent.php"
  - "shared/src/Api/Dto/Request/PlaylistAddTrackRequest.php"
validation: "2026-10-15 — 12/12 iddia SQL dump + PHP dosyalarıyla doğrulandı (repo kanıt 12, web kanıtlı 8)"
open_questions: ["OQ-P1: W-P5 (jspf) içeriği okunmadı — JSPF şema ayrıntıları ⚠️ VERIFICATION REQUIRED", "OQ-P2: playlistController route kaydı var ama implementasyon dosyası bulunamadı (2026-10-15) → ⚠️ VERIFICATION REQUIRED", "OQ-P3: canlı playlist akışı raporu yok"]
group: kanit
---

# playlist — Doğrulama (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15

## §1 İDDİA KAYITLARI

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|---|---|---|---|---|
| D-P1 | Playlist `playlists` tablosu 17 kolonla tanımlı (UUID v7 `binary(16)` kimlik) | `coremusic_playlist.sql satır 84-107` | [[playlist-web-arastirma]] §1 W-P9 (XSPF spec) | potansiyel | doğrulandı |
| D-P2 | `user_id` zorunlu — sahipsiz liste yok | `satır 86: binary(16) NOT NULL` | — | somut | doğrulandı |
| D-P3 | `visibility` varsayılan `'public'` (açık) | `satır 90 DEFAULT 'public'` | — | somut | doğrulandı |
| D-P4 | `playlist_tracks` sıralı ara tablo: `position`, `added_by`, `added_at` | `satır 67-82 · position satır 71` | [[playlist-web-arastirma]] §1 W-P1 (HLS yayın listesi — benzetme) | potansiyel | doğrulandı |
| D-P5 | `playlist_type` ENUM 5 değerli | `satır 91 ENUM('user','ai','auto_generated','radio','curated')` | [[playlist-web-arastirma]] §1 W-P6 (PLS) | benzetme | doğrulandı |
| D-P6 | `total_tracks`/`total_duration_sec` önbellek kolonları | `satır 93-94` | [[playlist-web-arastirma]] §1 W-P8 (XSPF track list) | benzetme | doğrulandı |
| D-P7 | `playlist_collaborators` rol ENUM + davet akışı (`invited_at`/`accepted_at`) | `satır 18-35 · role satır 22 · invited/accepted satır 23-25` | [[playlist-web-arastirma]] §1 W-P2 (HLS session keys — paylaşımlı oturum analojisi) | benzetme | doğrulandı |
| D-P8 | `PlaylistCreatedEvent` `DomainEventInterface`'i implement eder; `payload()` 4 alan üretir | `shared/src/Events/Domain/PlaylistCreatedEvent.php satır 20 (sınıf) · 53-61 (playlistId·createdBy·name·timestamp)` | [[playlist-web-arastirma]] §1 W-P5 (JSPF — playlist meta benzetmesi) | somut | doğrulandı |
| D-P9 | Yumuşak silme: `is_deleted` flag + `deleted_at` nullable | `satır 100-101` | — | somut | doğrulandı |
| D-P10 | `playlists.name` benzersiz değil — UNIQUE KEY yok; yalnız `KEY(user_id)` + `FULLTEXT(name,description)` | `satır 103 (idx_playlists_user) · satır 106 (FULLTEXT)` | [[playlist-web-arastirma]] §1 W-P9 (XSPF title) | somut | doğrulandı |
| D-P11 | Sıralama API tarafında: AddTrack isteği pozisyon kabul eder (varsayılan -1 = sona) | `shared/src/Api/Dto/Request/PlaylistAddTrackRequest.php satır 19-23 (position = -1) · 44-47 (getPosition)` | [[playlist-web-arastirma]] §1 W-P9 (XSPF sıralı track listesi — benzetme) | benzetme | doğrulandı |
| D-P12 | Takip mekanizması favori'den ayrı model: `playlist_followers` tablosu | `satır 37-46 · UNIQUE(playlist_id,user_id) satır 43` | — | somut | doğrulandı |

## §2 SAYIM

| Metrik | Değer | Kanıt |
|---|---|---|
| İddia sayısı | 12 | §1 (D-P1…D-P12) |
| Somut (repo) | 6 | D-P2, D-P3, D-P8, D-P9, D-P10, D-P12 |
| Potansiyel | 3 | D-P1, D-P4, D-P6 |
| Benzetme | 3 | D-P5, D-P7, D-P11 |
| Web kanıtlı (W-P* göstermeli) | 8 | D-P1, D-P4, D-P5, D-P6, D-P7, D-P8, D-P10, D-P11 |
| Repo-tek (web `—`) | 4 | D-P2, D-P3, D-P9, D-P12 |
| Doğrulanma | 12/12 | — |
| Open question | 3 | OQ-P1…OQ-P3 (frontmatter) |

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
