---
title: "Gerçek DB'ye geçiş — migration apply + mock veri temizliği"
date: 2026-10-22
session: "real-data-migration"
status: completed
---

# 2026-10-22 — Gerçek Verilere Geçiş (Migration Apply + Mock Temizliği)

## Görev
`.ai/.sql/mysql/` şemalarını canlı MySQL'e kur; hardcoded/geçici (mock) verileri kodlardan temizle; sistem gerçek DB verisiyle çalışsın.

## Yapılan İşler (5 commit)

| # | Commit | İçerik |
|---|--------|--------|
| 1 | `3002178` db | `20261021_apply_pending.sql` — 5 FK + `user_recent_tracks` + VIEW + 7 indeks (apply öncesi 5 DB yedeği `C:/temp/opencode/backup_20261021b`) |
| 2 | `8e3029e` api | `unread_notifications` → gerçek DB (`NotificationRepository::countUnread`, `coremusic_system.system_notifications`); `ApiContainer` system registry singleton; `UserHeaderController` 4. bağımlılık; `UserProfileRepository` email+role; testler |
| 3 | `954e59b` home | `RecentTracksComponent` playlist kartları → `coremusic_playlist.playlists` (`PlaylistRepository::findFeaturedPlaylists`); `history.php` stream URL'leri `API_URL` önekli |
| 4 | `c903c4b` js | `music-spa-manager` http→https/ASSETS_ORIGIN + süre fallback; `header-manager` bildirim rozeti + arama önerileri; `WidgetManager` sahte 28°C silindi |
| 5 | `2220b6f` auth | login/register "Equalzier Presents 5" → anlamlı placeholder |

## Test Kapıları (hepsi yeşil)
- api.coremusic.net: **65/65 PASS** · home: **67/67** · shared: **398/398** · auth: **44/44** · admin: **26 PASS (2 skip normal)** · vitest: **19 dosya/174 PASS** · node --check: 0 hata

## Kararlar
- Mevcut `fk_notifications_user` varken redundant ikinci FK eklenmedi (surgical).
- `user_recent_tracks` canonical isim (sibling `user_` deseni).
- JS asset origin: `window.CoreMusic?.RouterConfig?.assetsUrl || 'https://assets.coremusic.net'`.
- **Admin sayfalarını DB'ye bağlama KAPSAM DIŞI** (kullanıcı: "admini atla şu an") — admin.coremusic.net'te repository katmanı hiç yok, ayrı faz.
- Home seed'leri (`defaultTracks`/`wide-seed.php`) dokunulmadı: yalnız DB yokken devreye giriyor, `wide-seed` bilinçli `?_demo=1` mockup-SSOT modu.

## Commit EDİLMEYEN (auth-UI Figma WIP — kullanıcı işi, 71 dosya)
- 42 CSS (auth/admin cihaz paketi) + 8 yeni `d-admin-*.css` + `d-admin-1920.css` silinmesi
- `DeviceCssMap` + `DeviceRendererTest` (admin cihaz ayrımı) + `device-loader.js`/`devices.config.js`
- admin routing/pages (`admin-login/register.php` silindi, `AdminRootRedirector`, `AuthAliasRedirector`, `routes.php`, `bootstrap.php` + 4 test)
- auth pages (`AuthPostHandler`, `forgot-password`, `logout`, `reset-password`, `select-gender`, `set-gender`)
- root PNG'ler (`gender-current.png`, `login-current.png`) — kirli artefakt, EDİLMEYECEK

## Sonraki Adımlar
1. auth-UI Figma WIP commit'i (kullanıcı kararı).
2. Adım 6: şarkı/video ↔ `media_catalog`/`media/` bağlantısı uçtan uca doğrulama.
3. Adım 7-11: playlist, recent→home panel, history, header, dropdown API↔DB uç doğrulaması.
4. Admin DB faz'ı (repository + pagination) — ertelendi.

## Ortam Notu
- PHP: `C:\wamp64\bin\php\php8.4.15\php.exe` (8.4.7 YOK — 8.4.15).
- MySQL: `C:\wamp64\bin\mysql\mysql8.4.7\bin\mysql.exe -u Ali -p"ali**"` + `--default-character-set=utf8mb4`.

## Ek: Admin Dashboard DB Fix (commit `ea70d5d`)
- **Belirti:** `[admin][dashboard] DB count query error: SQLSTATE[HY000] [1049] Unknown database 'coremusic_admin'`.
- **Kök neden:** `DB_ADMIN_NAME` default'u (`coremusic_admin`) olmayan bir semayı gösteriyordu; `config/.env`'de de aynı yanlış değer tanımlı olduğu için default düzeltmesi yetmiyordu.
- **Karar (kullanıcı onaylı):** Ayrı `coremusic_admin` seması AÇILMADI — `admin_users` zaten `coremusic_auth` semasında (`is_deleted` kolonu dahil, `user_id` FK → `users`). Yeni şema duplicate tablo üretirdi.
- **Değişiklikler:** `constants.php` default → `coremusic_auth` · `.env.example` · `AdminMarkupContractTest` · `config/.env` (gitignored, lokal) · 4. dosya.
- **Doğrulama:** CLI ile dashboard sorgusu (`SELECT COUNT(*) FROM admin_users WHERE is_deleted = 0`) `coremusic_auth`'te BASARILI (COUNT=0 = silinmemiş admin kaydı yok, tutarlı). `php -l` 2/2 temiz · admin PHPUnit 26/26 PASS (2 skip normal).
