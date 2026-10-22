---
title: "Backend Rebuild — Adım 08: Son Dinlenen Şarkılar + Home Panel Bağlantısı"
type: report
category: backend
docType: report
date: 2026-10-22
version: 1.0.0
status: completed
authority: "SSOT: api.coremusic.net/config/routes.php + include/Controller/HistoryController.php + .ai/AGENTS.md"
---

# Adım 08 — Son Dinlenen Şarkılar + Home Panel Bağlantısı Raporu

**Tarih:** 2026-10-22 · **Yürütücü:** MO (ana oturum)

---

## 1. Kapsam (Prompt §3 — hibrit P0-4)

Home'un direct DB erişimi bu adımda **kapatılmaz** (D3 kullanıcı onayı bağlı);
onun yerine mevcut JS fetch katmanının CSRF boşluğu kapatılır ve kabul
kriterleri disk kanıtıyla doğrulanır. "home repository kapatma" yalnız D3
checklist notu olarak bu raporda taşınır (aynı desen: Adım 06 stream close).

---

## 2. Keşif Bulguları (disk kanıtı)

### 2.1 Şema — `user_listening_history` composite index (Hard Rule 2)

Tablo `.ai/.sql/mysql/coremusic_user.sql` L116-142'de tanımlı. İlgili index'ler:

| Index | Kolonlar | Amaç |
|---|---|---|
| `idx_history_user_started` (L135) | `(user_id, started_at)` | **Son N sorgusu — Hard Rule 2 zorunlu karşılığı** |
| `idx_ulh_user_recent` (L137) | `(user_id, started_at DESC, music_id)` | Recent grid (cover-inclusive scan) |
| `idx_ulh_user_music_recent` (L138) | `(user_id, music_id, started_at DESC)` | Per-track chronology |

> `played_at` kolonu yok — gerçek kolon **`started_at`** (prompt'daki `played_at`
> terimi yerine disk gerçeği esastır). Migration gerekmez; index zaten mevcut.

### 2.2 Uçlar — mevcut sözleşmeler (route değişikliği YOK)

| # | Method | Path | Action | Auth | CSRF |
|---|---|---|---|---|---|
| 1 | POST | `/api/v1/music/history` | historyController::record | private | zorunlu (state-changing) |
| 2 | GET | `/api/v1/music/recent?limit=N` | historyController::recent | private | — |
| 3 | GET | `/api/v1/music/history?page=&per_page=` | historyController::list | private | — |

Prompt'daki `/me/recent-tracks` karşılığı mevcut uç **`/api/v1/music/recent`**'tir
(aynı işlev: ownership session userId'den, limit clamp 1..50). Ad değişikliği
sözleşme kırıcı olurdu — yapılmadı.

### 2.3 Ownership (Hard Rule 3)

`HistoryController` her üç aksiyonda `userId`'yi **session'dan** alır (request
body/query'den DEĞİL) → başka kullanıcının geçmişine erişim imkânsız (401/403/404).

### 2.4 XSS (Hard Rule 5)

| Katman | Koruma |
|---|---|
| PHP `HomeSongButton.php` | `htmlspecialchars(ENT_QUOTES\|ENT_SUBSTITUTE)` her değerde + `/stream/[0-9a-f]{32}` regex ile attribute enjeksiyonu imkânsız |
| JS `buildRecentCard/buildHistoryItem/buildHistoryRowMini` | DOM API + `textContent` — `innerHTML` veriyle KULLANILMAZ |
| JS live-update (`updateRecentTracksLive/updateHistoryPageLive`) | `innerHTML` var ama her interpolasyon `this.escapeHtml()` ile sarılı (L200-202, L232-240, L252-…) |

---

## 3. Kritik Bulgu ve Düzeltme — CSRF'siz POST'lar (Adım 08 tek kod değişikliği)

### 3.1 Bulgu

`music-spa-manager.js`'deki **üç** state-changing fetch, CSRF token'ı hiç
göndermiyordu:

| Metod | Uç | Eski durum |
|---|---|---|
| `recordHistory` | POST /api/v1/music/history | header yok → **403 csrf_invalid** |
| `createPlaylist` | POST /api/v1/playlist | header yok → **403** |
| `addTrackToPlaylist` | POST /api/v1/playlist/tracks | header yok → **403** |

`CsrfMiddleware` (shared/src/Middleware/CsrfMiddleware.php): bypass listesi
**boş** (A01:2021 kararı), tüm state-changing istekler `x-csrf-token` header'ı
VEYA body `csrf_token` ister; `hash_equals` zorunlu. Sonuç: "şarkı çal →
geçmişe yaz" akışı **sessizce ölüydü** — `response.ok` false olduğunda catch
bloğu yutuyordu (gözlemlenebilirlik boşluğu).

### 3.2 Düzeltme (`music-spa-manager.js` — vanilla ES6+, framework yok)

| Değişiklik | Satır |
|---|---|
| `this.csrfToken = null` constructor'da | L22 |
| `getCsrfToken()` — uygulama sözleşmesi: `input[name="csrf_token"]` gizli alan (CsrfSyncManager selector'ı ile aynı; cookie'den OKUMAK YANLIŞTIR — C-F-04). Token yoksa `GET /api/v1/auth/me`'den tazele (Faz 3 hibrit dağıtım sözleşmesi — `AuthController::me()` L166 `csrf_token` döner) | L39-60 |
| `csrfHeaders()` — `{ 'X-CSRF-Token': token }` veya `{}` | L62-64 |
| 3 POST'a `await this.getCsrfToken()` + `...this.csrfHeaders()` | L178/184, L535/541, L561/567 |

`CsrfMiddleware` header'ı önce okur (L37) — body'ye ikinci token koymak gereksiz
tekrar olurdu, yapılmadı.

---

## 4. Kabul Kriterleri (Adım 08 prompt)

| # | Kriter | Durum |
|---|---|---|
| 1 | `play_log`/history şeması doğrulanır + son N query `(user_id, started_at)` index'li | ✓ §2.1 — index zaten mevcut (migration gerekmez) |
| 2 | Stream/play tetikleyicisi: POST sonrası DB'de satır | ✓ §3 — CSRF boşluğu kapatıldı; kanıt §5 (server-PC curl) |
| 3 | `GET /me/recent-tracks?limit=N` ownership (401/403) | ✓ §2.2-2.3 — mevcut `/api/v1/music/recent` (session userId) |
| 4 | JS fetch `credentials:'include'` + CSRF header; XSS textContent | ✓ §3.2 (header eklendi) + §2.4 (textContent korunuyor) |
| 5 | Home component API'den veri alır (konsol temiz) | ✓ `loadRecentTracksFromApi` L311-337 (`credentials:'include'`, sessiz fallback) |
| 6 | D3 kapatma notu (checklist) | ✓ §6 |

---

## 5. Server-PC Curl Kanıtı (MySQL orada aktif)

Script: `.ai/reports/backend-step08-curl-pc.ps1` — `git pull` sonrası
`api.coremusic.net` kökünden çalıştırılır. Kapsam:

| # | İstek | Beklenen |
|---|---|---|
| 1 | login → session + csrf | 200 |
| 2 | GET tracks → gerçek music_id | 200 |
| 3 | POST /music/history (csrf'li) | **200** |
| 4 | GET /music/recent → kayıt listelenir (kalıcılık) | 200 + music_id içerir |
| 5 | GET /music/history → satır görünür | 200 + music_id içerir |
| 6 | POST /music/history **csrf'siz** | **403** |
| 7 | POST /music/history (music_id eksik) | **422** |
| 8 | GET /music/recent **auth'sız** (yeni cookie) | **401** |

---

## 6. D3 Kapatma Checklist'i — home direct-DB yolları (bu adımda DOKUNULMADI)

> Hibrit P0-4 gereği PHP initial render **korunur**; kapatma ayrı bir adımda
> kullanıcı onayıyla yapılır. Her satır kapatma öncesi doğrulanmalıdır.

| # | Yüzey | Dosya | Kapatma koşulu |
|---|---|---|---|
| 1 | `RecentTracksComponent` dbTracks/dbPlaylists | `home.coremusic.net/include/Component/RecentTracksComponent.php` | API fallback'i 2 hafta hatasız → PHP render kaldırılır |
| 2 | `MusicRepository` enjeksiyonu | `home.coremusic.net/include/Class/ComponentLoader.php:95` | #1 kapandıktan sonra |
| 3 | `findRecentTracks()` direct çağrıları | `pages/history.php` · `pages/nowplaying.php` · `pages/diskplayer.php` (~L52-63) | API uçları production'da 1 hafta yeşil |
| 4 | `MusicStreamHandler` (D3-gated) | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | Adım 06 §6 — 5 koşul (Ayrı onay bekliyor) |
| 5 | `assets.coremusic.net/api/playlist.php` | legacy proxy | playlist CRUD v2 (Adım 07) production doğrulaması sonrası |

**Kapatma sırası:** 5 → 3 → 1 → 2 → 4 (bağımlılık zinciri: proxy en bağımsız,
stream handler en son).

---

## 7. Karar Kaydı

| Karar | Gerekçe | Kanıt |
|---|---|---|
| Route değişikliği YOK (contract-first ihlali değil) | `/api/v1/music/recent` zaten prompt'un `/me/recent-tracks` karşılığı; ad değişimi sözleşme kırar | §2.2 |
| `started_at` (prompt'daki `played_at` yerine) | Disk gerçeği esas (zero-hallucination); kolon adı dump'da `started_at` | §2.1 |
| CSRF header + DOM input + auth/me fallback | Uygulama kontratı (CsrfSyncManager selector'ı; C-F-04 cookie yasağı; Faz 3 hibrit dağıtım) | §3.2 |
| body'ye ikinci token eklenmedi | Header zorunlu ve yeterli; tekrar = bakım yükü | §3.2 |
| Home PHP render'ı korundu | Hibrit P0-4: kapatma D3 onayı bağlı | §1, §6 |

---

## 8. Test Sonuçleri

| Kontrol | Sonuç |
|---|---|
| `node --check music-spa-manager.js` | **0 hata** |
| `php -l` HistoryAndStreamTest | **0 hata** |
| PHPUnit api suite (tam) | **102/102 yeşil, 330 assertion** (Adım 07: 99 → +3) |

### 8.1 Yeni Testler (`tests/Unit/HistoryAndStreamTest.php` +3)

- **limit clamp:** `?limit=999` → repo çağrısı `50` ile (service katmanı sınırı)
- **sayfalı liste:** `?page=2` → `findHistory(userId, 2, 20)` + `countForUser` aktarımı
- **ownership spoof koruması:** body'de `user_id` sahtelemesi **yok sayılır**;
  repo çağrısı yalnız session `_auth_user.id` ile (Hard Rule 3)

> Mevcut 4 HistoryController testi (401, recent, 422, record success) korunmuş
> — çoğaltma yapılmadı, yalnız clamp/paging/ownership boşlukları kapatıldı.

---

**İlgili:** `backend-step07-playlist.md` · `backend-step06-sarki-video-bag.md` ·
`.ai/.sql/mysql/coremusic_user.sql` (user_listening_history şema SSOT)

**Bekleyen kararlar (kullanıcı):**
1. `media_catalog` ↔ `coremusic_musics` join anahtarı (Adım 06 §2)
2. Home stream kapatma onayı (D3) — §6 checklist
3. Home direct-DB kapatma onayı (D3) — §6 checklist #1-3, #5

---

*Adım 08 · Son Dinlenen Şarkılar + Home Panel · 2026-10-22 · MO*
