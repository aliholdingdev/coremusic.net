---
title: "Backend Rebuild — Adım 07: Playlist CRUD v2 (Detail/Update/Delete/Order/RemoveTrack)"
type: report
category: backend
docType: report
date: 2026-10-22
version: 1.0.0
status: completed
authority: "SSOT: api.coremusic.net/config/routes.php + include/Service/PlaylistService.php + .ai/AGENTS.md"
---

# Adım 07 — Playlist CRUD v2 Raporu

**Tarih:** 2026-10-22 · **Yürütücü:** MO (ana oturum) · **Commit:** `ab7a3f9` (kod) + `71bc6c8` (test)

---

## 1. Route Sözleşmesi (Contract-First — ADR-084)

`RouteTable` exact-path eşleşir (`{id}` parametresi yok) → kimlik alanları
query string'de taşınır. DELETE, `RequestValidationMiddleware::BODY_METHODS`
(POST/PUT/PATCH) dışında olduğu için doğrulama query'den okunur.

| # | Method | Path | Action | Auth | Doğrulama | Başarı |
|---|---|---|---|---|---|---|
| 1 | GET | `/api/v1/playlist` | index | private | — | 200 |
| 2 | POST | `/api/v1/playlist` | create | private | `name` (1-100) | **201** |
| 3 | GET | `/api/v1/playlist/detail?id=` | show | private | `id` | 200 · 404 |
| 4 | PUT | `/api/v1/playlist` | update | private | `id` + en az 1 alan | 200 · 404 · 422 |
| 5 | PUT | `/api/v1/playlist/order` | reorder | private | `playlist_id`, `track_ids[]` | 200 · 404 · 422 |
| 6 | POST | `/api/v1/playlist/tracks` | addTrack | private | `playlist_id`, `track_id` | 200 · 404 · 422 |
| 7 | DELETE | `/api/v1/playlist/tracks?playlist_id=&track_id=` | removeTrack | private | `playlist_id`, `track_id` | 200 · 404 |
| 8 | DELETE | `/api/v1/playlist?id=` | delete | private | `id` | **204** · 404 |

**Hata matrisi:** 401 auth yok · 403 CSRF yok (state-changing) · 405 yanlış
method (path tanımlıysa Allow döner) · 422 alan hataları `error.fields` · 404
IDOR/kayıp (enumerate koruma — varlık sızıntısı yok).

### 1.1 Kod Değişiklikleri

| Dosya | Değişiklik |
|---|---|
| `config/routes.php` | 5 yeni route (detail/update/order/delete/removeTrack) — validation + `public:false` |
| `include/Repository/IPlaylistRepository.php` | `appendTrack`/`updateMeta`/`softDeleteWithTracks`/`findTrackIds`/`replacePositions` eklendi; `insertTrack`/`maxPosition` kaldırıldı (tekilleşti) |
| `include/Repository/PlaylistRepository.php` | 5 metodun impl. — hepsi transaction içinde |
| `include/Service/PlaylistService.php` + `IPlaylistService` | `update`/`delete`/`reorder` — ownership + normalizasyon |
| `include/Controller/PlaylistController.php` | 8 aksiyon; 201/204; visibility whitelist (public/private/unlisted) |
| `include/Controller/AbstractController.php` | `success($data, int $status = 200)` — 201 için |
| `index.php` | 2xx korunur (eski: <400 → hep 200); halt array body JSON encode (csrf 403 `Array` bugfix — gözlemlenebilirlik) |

---

## 2. Yarış Koşulu ve Kalıcılık (Hard Rule 2/3)

**`appendTrack` (ekleme) — tek transaction:**
1. `SELECT id FROM playlists WHERE playlist_id=? FOR UPDATE` → satır kilidi,
   eşzamanlı ekleme serializes olur.
2. `MAX(position)` → **tüm satırlar** üzerinden (soft-delete dahil) —
   `UNIQUE(playlist_id, music_id, position)` çakışması önlendi (silinmiş satır
   aynı position'ı taşıyabilir).
3. `INSERT` + `track_count++` → commit.

**`replacePositions` (sıralama) — tek transaction:** mevcut aktif satırların
`music_id → row_id` eşlemesi alınır, toplu `UPDATE` ile yeni position'lar
yazılır. Kalıcı: sonraki `findTracks`/`findTrackIds` aynı sırayı döner.

**`softDeleteWithTracks` (silme) — tek transaction:** liste + şarkı kayıtları
`is_deleted=1`; **hard DELETE yasak** (ADR-002/revizyon).

### 2.1 Reorder Normalizasyonu (Idempotent)

Tam-set zorunluluğu yerine bağışlayıcı normalizasyon: bilinmeyen id yok sayılır,
tekrar eden id ilk geçerli konumda tutulur, istekte eksik bırakılan aktif
şarkılar mevcut sıralarıyla sona eklenir. Sonuç: aynı istek tekrarlanınca aynı
yazılmış sıra (idempotent) — §3 test #13-15.

---

## 3. Statik Doğrulama

| Kontrol | Sonuç |
|---|---|
| `php -l` — 10 dosya (8 prod + 2 test) | **0 hata** |
| PHPUnit api suite | **99/99 yeşil, 319 assertion** |

### 3.1 Yeni Testler (`tests/Unit/`)

**`PlaylistControllerTest` (+17 → 24):** 201 create · 204 halt (delete) ·
404 IDOR (show/update/delete/removeTrack/reorder ownership) · 422 matrisi
(update id yok, update alan yok, visibility geçersiz, reorder track_id format,
addTrack/removeTrack id eksik) · 401 (8 aksiyonun tamamı).

**`PlaylistServiceTest` (yeni, 19):** fake `IPlaylistRepository` ile:
- Sahiplik: sahibi değil → null/false + **DEĞİŞTİRİCİ repo çağrısı YOK** (IDOR)
- Visibility: private non-owner → `findTracks` hiç çağrılmaz
- Reorder: bilinmeyen/tekrar/eksik id normalizasyonu · büyük-harf lowercase ·
  boş istek mevcut sırayı korur
- `update`: yalnız verilen alanlar yazılır · trim · explicit null description
- `delete`: `softDeleteWithTracks` çağrılır (hard DELETE yok)
- `create`: trim + owner aktarımı

---

## 4. Server-PC Curl Kanıtı (MySQL orada aktif)

Script: `.ai/reports/backend-step07-curl-pc.ps1` — `git pull` sonrası
`api.coremusic.net` kökünden çalıştırılır. Kapsam:

| # | İstek | Beklenen |
|---|---|---|
| 1 | login → session + csrf | 200 |
| 2 | GET list | 200 |
| 3 | POST create | **201** |
| 4 | GET detail | 200 |
| 5 | PUT update | 200 |
| 6 | POST addTrack (gerçek music_id) | 200 + position |
| 7 | PUT order (sadece eklenen) | 200 + sıra yazıldı |
| 8 | PUT order **tekrar** (idempotent) | 200 + aynı sıra |
| 9 | GET detail (kalıcılık) | 200 + yeni sıra |
| 10 | CSRF'siz PUT | **403** |
| 11 | bilinmeyen id (32 hex) | **404** (IDOR/kayıp) |
| 12 | POST create name eksik | **422** |
| 13 | GET /order (yanlış method) | **405** |
| 14 | DELETE removeTrack | 200 |
| 15 | DELETE playlist | **204** |

> Gerçek "başka kullanıcı" IDOR senaryosu (iki hesap) opsiyoneldir — script
> bilinmeyen-geçerli-id ile enumerate korumasını ölçer; iki hesap karşılaştırması
> server PC'de manuel eklenebilir.

---

## 5. Kabul Kriterleri (Adım 07 prompt)

| # | Kriter | Durum |
|---|---|---|
| 1 | CRUD curl ile kanıtlanır (200/201/204/403/404) | ✓ §4 (server-PC script) |
| 2 | IDOR 403/404 (başkasının listesi) | ✓ §3.1 (test) + §4 #11 |
| 3 | position kalıcı sıralama | ✓ §2 (replacePositions tx) + §4 #7-9 |
| 4 | soft-delete only (hard DELETE yasak) | ✓ §2 (`softDeleteWithTracks`) |
| 5 | tüm yazım transaction içinde | ✓ §2 (appendTrack/replacePositions/softDeleteWithTracks tx) |
| 6 | route contract-first (`routes.php` önce) | ✓ §1 |
| 7 | `php -l` 0 hata | ✓ 10/10 |
| 8 | PHPUnit yeşil | ✓ 99/99 |

---

## 6. Karar Kaydı

| Karar | Gerekçe | Kanıt |
|---|---|---|
| `RouteTable` exact-path → id query'de (`?id=`) | `{id}` param destructuring yok; mevcut `tracks/tracks` deseni korundu | §1 |
| DELETE doğrulama query'den | `BODY_METHODS`=POST/PUT/PATCH — DELETE body okunmaz | §1 notu |
| `MAX(position)` tüm satırlar (soft-deleted dahil) | `UNIQUE(playlist_id,music_id,position)` — silinmiş satır çakışması | §2 |
| Reorder bağışlayıcı normalizasyon (exact-set 422 değil) | idempotent + UX; bilinmeyen id 422'siz sessizce yok sayılır | §2.1 |
| `FOR UPDATE` playlist satırı (aggregate yerine) | aggregate `FOR UPDATE` InnoDB'de desteklenmez/verimsiz — doğrulanamayan varsayım yok | §2 |
| index.php 2xx + halt JSON fix | 201/204 için şart; csrf 403 `Array` hatası observability P5-32 | §1.1 |

---

## 7. Adım 08'e Geçiş Notu

Adım 08 — home'un direct DB erişimi API'ye taşınır (`RecentTracksComponent`
dbTracks/dbPlaylists, `ComponentLoader.php:95`, `pages/history.php:63`,
`pages/nowplaying.php:63`, `pages/diskplayer.php:52`,
`assets.coremusic.net/api/playlist.php:140`, home `api/music/{history,recent-tracks}.php`).
`music-spa-manager.js` home fallback'leri kaldırılır. Home stream kapatma **D3**
kullanıcı onayı bağlı (Adım 06 §6 — 5 koşul).

---

**İlgili:** `backend-step06-sarki-video-bag.md` · `backend-step05-backend-katman.md` ·
`.ai/.sql/mysql/coremusic_playlist.sql` (şema SSOT)

**Bekleyen kararlar (kullanıcı):**
1. `media_catalog` ↔ `coremusic_musics` join anahtarı (Adım 06 §2 — dosya yolu mı, eşleme tablosu mu?)
2. Home stream kapatma onayı (D3) — 5 koşul sağlandıktan sonra

---

*Adım 07 · Playlist CRUD v2 · 2026-10-22 · MO*
