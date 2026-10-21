---
title: "Backend Restructure Faz 4b — API uçları commit + SPA API bağlantısı (Adım 8/9)"
date: 2026-10-21
session_id: "2026-10-21-backend-restructure-faz4b"
status: completed
tags: [backend, api, spa, php, javascript, vitest]
---

# Backend Restructure Faz 4b — API uçları commit + SPA API bağlantısı

> Önceki oturum: `[[2026-10-21-backend-restructure-faz2-3]]` (Faz 2–4a + 4b kod üretimi).
> Master prompt: `.ai/prompts/2026-10-21-coremusic-backend-restructure-master-prompt.md`

## 1. Bu Oturumda Ne Yapıldı

1. **Faz 4b commit'leri atıldı** (4 commit, yetki orkestratörde):

   | Commit | Kapsam | Dosya |
   |--------|--------|-------|
   | `3daf40f` | api: Faz 4b — history/stream/header uçları + stream auth + ApiContainer onarımı + regresyon testi | 12 dosya (+930) |
   | `b3041b5` | spa: header-manager + music-spa-manager — API'ye frontend bağlantısı (header.php script tag) | 3 dosya (+803) |
   | `5d71d34` | db: `20261021_backend_restructure.sql` indeks migration + vault: master prompt arşivi | 3 dosya (+576) |
   | `d03d54c` | spa: Adım 8/9 — music-spa-manager API ilk yükleme (GET /recent + /history) + PHP render fallback + 8 spec | 2 dosya (+356) |

2. **Adım 8/9 doğrulaması (zero-hallucination):** Master prompt'ta Görev 4.2/4.3 `[x]`
   işaretliydi ama disk kanıtı YALAN söylüyordu: `recent-tracks.php` hâlâ sunucu
   render (`$this->cards`), `history.php` hâlâ eski home `MusicRepository::findRecentTracks()`,
   `InfiniteScroll.js`/`HistoryComponent.js` içinde hiç `fetch` yoktu. Doğrulanmayan
   `[x]` işaretleri bu oturumda gerçekten tamamlandı:
   - `music-spa-manager.js` → `loadInitialData()` (`loadRecentTracksFromApi` +
     `loadHistoryFromApi`) — `credentials: 'include'`, DOM API ile render
     (innerHTML yasağına uygun, `text` alanları `textContent`), `duration_sec` →
     `HH:MM:SS` (`formatDuration`, PHP paritesi).
   - **Fallback tasarımı:** fetch hata/401/boş-items/ ağ reddi → PHP sunucu-render
     sessizce korunur (progressive enhancement; migration apply edilmeden API 404/500
     verse bile sayfa bozulmaz).
3. **Testler (gate):** JS `vitest` **19 dosya / 174 test PASS** (yeni spec 8/8) ·
   PHP api `63/63` (228 assertion) · shared `398/398` — hepsi yeşil.
4. **Kalan working tree:** yalnız auth-UI Figma stream'i (42 M CSS + 8 ?? d-admin-*.css
   + auth/admin pages M + DeviceCssMap/DeviceRendererTest M) + root PNG'ler
   (`gender-current.png`, `login-current.png` — kirli artefakt, commit EDİLMEYECEK).

## 2. Kararlar

| Karar | Gerekçe |
|-------|---------|
| SPA ilk-yükleme PHP render'ı **ezmediği** progressive enhancement | Migration'lar kullanıcıda; API down iken sayfa entegre kalır (FOUC riski yok) |
| `music_hex`/`title`/`artist_name`/`duration_sec` contract'ı JS'te birebir | `HistoryRepository` item shape (139-143) SSOT |
| Auth-UI Figma stream'i ayrı operasyon | Backend restructure ile ilişkisiz; vitest+browser doğrulaması WIP |

## 3. Kullanıcının Bekleyen İşleri

1. **4 migration apply** (sıra): `20261021_coremusic_auth_revision.sql` →
   `create_playlist_tables` → `bcnf_normalize_active_schemas` →
   `create_listening_tables` + notifications → ardından `20261021_backend_restructure.sql`.
2. **Canlı uç denemesi:** apply sonrası home'da "En Son Dinlenen Şarkılar" +
   `/history` API'den mi geliyor (console debug satırları izlenir: "PHP render korunuyor" görünmemeli).
3. **Auth-UI Figma WIP kararı:** commit edilsin mi? (vitest + browser doğrulaması şart)

## 4. Dersler

- **Master prompt `[x]` işaretleri kanıt değildir** — task breakdown işaretleri
  "tasarımda yapıldı" anlamına gelir; her `[x]` disk kanıtıyla (fetch/sorgu/DOM)
  doğrulanmalıdır.
- `InfiniteScroll`/`HistoryComponent` bileşen adları "veri çeker" izlenimi verir ama
  yalnız davranış (scroll/klavye) sağlar — veri katmanı ayrıdır (bu oturumda
  `music-spa-manager.js`'e taşındı).
