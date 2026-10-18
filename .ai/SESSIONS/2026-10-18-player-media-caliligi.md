---
title: "2026-10-18 — Player medya tipi kalıcılığı: statik #audio/#video + mediaType/isPlaying cookie↔session + data-kind sinyali"
type: session
category: sessions
version: 1.0.0
status: active
authority: "SSOT: cookie contract mediaType=audio|video · isPlaying=play|pause|stop + session anahtarları GET_MEDIA_TYPE/GET_MEDIA_STATUS (SessionKeys.php)"
updated: 2026-10-18
tier: 2
domain: frontend
ssot: true
risk: low
owner: "Vault Steward"
depends-on: [".ai/AGENTS.md", ".ai/CLAUDE.md", "assets.coremusic.net/js/coreplayer/coreplayer.shared.js", "home.coremusic.net/include/Session/HomeSessionManager.php"]
---

# Player Medya Tipi Kalıcılığı — Kapanış Kaydı

> **Tarih:** 2026-10-18 · **Commits:** `bfab2f6` (12 dosya, +553/−45) + `c9fd51a` (5 dosya, +79/−16)
> **Durum:** TAM — vitest 166/166 · shared 398/398 · home 63/1 (tek fail = bilinen baseline) · canlı smoke 0 konsol hatası

---

## 1. Session Info

| Field | Value |
|-------|-------|
| Session ID | 2026-10-18-player-media-caliligi |
| Date | 2026-10-18 |
| Start Time | UNKNOWN (devam oturumu — oturum başı hook kaydı yok) |
| End Time | 22:38 |
| Duration | UNKNOWN |
| Status | TAM |

## 2. Summary

Player'ın medya tipi/durumu kalıcılığı kuruldu: statik element id'leri (`#audio`/`#video`),
cookie sözleşmesi (`mediaType`, `isPlaying`), session mirror (`GET_MEDIA_TYPE`/`GET_MEDIA_STATUS`),
her tam sayfa yükünde cookie→session hydrate + `window.CM_PLAYER_BOOT` rehidrasyonu, play/pause/stop
kancaları ve video kartlarından otomatik tip geçişi (`data-kind`). Kapanışta history/nowplaying/diskplayer
satırlarına `data-kind` genişlemesi **şema kanıtıyla reddedildi** (§5.5) — kod değişikliği YOK.

## 3. Tasks

| # | Task | Status | Notes |
|---|------|--------|-------|
| 1 | Statik `#audio`/`#video` id'leri | ✅ | `HtmlShellRenderer.php:193` `<video id="video" class="vdisplay">`; 6 hardcoded `#audio` → `CorePlayerShared.getMediaElement()` DRY |
| 2 | `mediaType`/`isPlaying` cookie + session mirror | ✅ | `HomeSessionManager` somut sınıf (+47 satır); `ISessionManager` arayüzüne ekleme YOK; `SessionKeys` +2 sabit (harfiyen `GET_MEDIA_TYPE`/`GET_MEDIA_STATUS`) |
| 3 | Footer hydrate + boot nesnesi | ✅ | `footer.php`: cookie→session mirror her sayfa yükünde; nonce'lu inline `CM_PLAYER_BOOT`; coreplayer `?v=2.0.x → v=2.1.0` cache-buster |
| 4 | Durum kancaları (play/pause/stop) | ✅ | controls/progressbar/seekbar/volume hook'ları `savePlaybackStatus` çağırır |
| 5 | `data-kind` medya tipi sinyali | ✅ | `HomeSongButton` (`k` alanı) → click'de `resolveMediaType(url, hint)`; RecentTracks aynı fabrikayı çağırır → iki kart ailesi kapsanır |
| 6 | Ölü kod temizliği | ✅ | `MM_PlaybackType` repo-wide 0 isabet → silindi, `mediaType` ile değiştirildi |
| 7 | Testler | ✅ | `coreplayer.shared.spec.js` 26 test (eklendi) · `HomeSessionManagerMediaTest` 5 test (eklendi) · `HomeSongButtonTest` +2 |
| 8 | history/nowplaying/diskplayer `data-kind` genişlemesi | ✅ RED | Şema kanıtı: audio-only domain (§5.5); YAGNI — kod değişmedi |

## 4. Decisions

| Decision | Rationale |
|----------|-----------|
| `MM_PlaybackType` silindi → `mediaType` cookie | Repo-wide 0 kullanım (dead code kuralı); tek sözleşme yeri |
| `ISessionManager` arayüzüne metod eklenmedi | YAGNI — somut `HomeSessionManager` yeterli; arayüz şişirilmez |
| Boot nesnesi nonce'lu inline script (`CM_PLAYER_BOOT`) | Footer rehidrasyonu cookie yoksa session'dan geri yazar; ekstra HTTP round-trip yok |
| `data-kind` sinyali `HomeSongButton`'da üretilir | RecentTracksComponent aynı fabrikayı çağırır → iki kart ailesi tek noktada kapsanır |
| `resolveMediaType(url, hint)`: ipucu önce, uzantı fallback | Uzantısız `/stream/{32hex}` URL'lerinde tipin tek kaynağı `data-kind`; geçersiz ipucu reddedilir (bogus testi) |
| history/nowplaying/diskplayer'a `data-kind` YOK | §5.5 — şema audio-only; bugün zaten doğru sonuç veriyor (YAGNI) |

## 5. Issues

| Issue | Resolution |
|-------|------------|
| `https: → '; Secure'` cookie dalı jsdom'da test edilemedi (jsdom 30 `window.location` non-configurable) | `⚠️ VERIFICATION REQUIRED` işaretli; canlı smoke http:81 üzerinden doğrulandı |
| history/nowplaying satırlarında tip alanı yok | KAPANDI (§5.5): veri kaynağı `musics` = audio-only domain; video satırı hiç gelmiyor |
| `use_strict_mode=1` üretim php.ini doğrulanmadı | Devam eden → DevOps Engineer |
| `album_name/fake` veri | Devam eden → Data Engineer |
| God class bölme kodu | ADR-104 gate'inde ayrı oturum |

### 5.5 history/nowplaying/diskplayer tip verisi araştırması (bu oturum — disk kanıtlı RED)

Soru: bu sayfaların satırlarına `data-kind` bağlanabilir mi? Kaynakları tarandı:

- `history.php` satırları `MusicRepository::findRecentTracks()`'ten gelir → `SELECT HEX(m.id), m.title, m.duration_sec, a.name FROM musics m JOIN artists a JOIN music_files f` — **seçilecek tip alanı yok**.
- Şema (`.ai/.sql/mysql/coremusic_musics.sql`, salt-okunur): `musics` (L305) **tip kolonu taşımaz**; `music_files.file_format` enum (L165) yalnız ses formatları (`mp3/flac/wav/aac/ogg/dsd/mqa`).
- Video ayrı domain: `music_videos` (L276; CHECK `mp4/webm/mkv/avi`, FK→`musics`) + `video_playback_history` (L510, FK→`music_videos.id`). History sayfası **bu tabloları hiç sorgulamıyor** → sayfaya video satırı hiç gelmiyor.
- `resolveMediaType` (coreplayer.shared.js L74): video uzantısı değilse → `'audio'` → uzantısız `/stream/{32hex}` satırları **bugün zaten `audio`'ya çözülüyor**.

Sonuç: `data-kind="audio"` ekleme = sıfır davranış değişikliği (YAGNI). Genişleme ancak video
satırları history'e gir backend phase'inde (video_playback_history birleştirme — Data Engineer +
Backend Architect domain) anlamlı olur; o zaman emitter tip taşımaya başlar.

## 6. Files Changed

| File | Action | Commit |
|------|--------|--------|
| `assets.coremusic.net/js/coreplayer/coreplayer.shared.js` | modified (+`getMediaElement`/`setMediaType`/`savePlaybackStatus`/`resolveMediaType`) | `bfab2f6`+`c9fd51a` |
| `assets.coremusic.net/js/coreplayer/coreplayer.controls.js` | modified (durum kancası) | `bfab2f6` |
| `assets.coremusic.net/js/coreplayer/coreplayer.progressbar.js` | modified (durum kancası) | `bfab2f6` |
| `assets.coremusic.net/js/coreplayer/coreplayer.seekbar.js` | modified (durum kancası) | `bfab2f6` |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` | modified (durum kancası) | `bfab2f6` |
| `assets.coremusic.net/js/coreplayer/coreplayer.shared.spec.js` | added (26 test) | `bfab2f6`+`c9fd51a` |
| `assets.coremusic.net/js/core/footer.init.js` | modified (click handler + `data-kind` + kancalar + boot rehidrasyon) | `bfab2f6`+`c9fd51a` |
| `home.coremusic.net/footer.php` | modified (hydrate + boot + `v=2.1.0`) | `bfab2f6` |
| `home.coremusic.net/include/Session/HomeSessionManager.php` | modified (+47 satır, somut sınıf) | `bfab2f6` |
| `home.coremusic.net/tests/Unit/Session/HomeSessionManagerMediaTest.php` | added (5 test) | `bfab2f6` |
| `shared/src/PageRouter/HtmlShellRenderer.php` | modified (`#video` id) | `bfab2f6` |
| `shared/src/Security/SessionKeys.php` | modified (+2 sabit) | `bfab2f6` |
| `home.coremusic.net/include/Component/HomeSongButton.php` | modified (`data-kind` üretimi) | `c9fd51a` |
| `home.coremusic.net/tests/Unit/Component/HomeSongButtonTest.php` | modified (+2 test) | `c9fd51a` |

## 7. Next Steps

- [ ] Push YOK — commit'ler lokal (`c9fd51a` HEAD); kullanıcı kararı
- [ ] Opsiyonel: `vault-sync-post` ile `.ai/MEMORY.md` session state + tam vault kapanışı
- [ ] Devam eden: `use_strict_mode=1` php.ini (DevOps) · `album_name/fake` (Data Engineer) · god class bölme (ADR-104 gate)
- [ ] Backend phase: video satırları history'e girerse emitter'lara tip alanı + `data-kind` (§5.5)

## 8. Vault Updates

- [x] log.md updated (append-only tek satır — bu kayıtla birlikte)
- [ ] MEMORY.md updated (vault-sync-post bekliyor)
- [ ] ADR created/updated (bu oturumda yeni ADR YOK; ADR-104 draft önceki oturumun)

## 9. Notes

- Doğrulama zinciri: vitest **166/166 (18 dosya)** · shared **398/398** · home **63/1** (tek fail = önceden kırık `ComponentLoaderTest:65` baseline) · `node --check` tüm JS · `php -l` tüm PHP · canlı browser smoke **0 konsol hatası** · canlı roundtrip: `setMediaType('video')` → `#video` element, `data-kind` click → cookie `mediaType=video`, bogus reddi ✓.
- Eşzamanlı başka oturum aynı worktree'de çalışıyor (`.ai/architecture` K-graph commit'leri `be47414`/`026a2bf`; CSS WIP `a-layout-tokens-1920`/`_home-layout`/`_welcome-banner`/`b-base-core`/`d-4k` commit edilmedi, dokunulmadı).
- Commit mechanics: PowerShell `>`/`"` kırıyor → `git commit -F` (C:\temp\opencode\ altına mesaj dosyası); pathspec yerine index + düz commit.
