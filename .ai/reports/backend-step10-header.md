---
title: "Adım 10 — Header DB Bağlantısı (Hibrit) Raporu"
type: report
category: backend-rebuild
step: 10
version: 1.0.0
date: 2026-10-22
status: complete
---

# Adım 10 — Header DB Bağlantısı (Hibrit) · Rapor

> Prompt: `coremusic-backend-step10-header.md` (KATMAN UI 1/2 · Karar: P0-4 HİBRİT)
> Sözleşme: `GET /api/v1/user/header-data` — prompt'un "`/me/header`" adı gerçek rotaya eşlendi
> (Adım 09 dersi: prompt adı ≠ gerçek route; gerçek route contract'tır).

## §1 Keşif Bulguları

| Yüzey | Durum |
|---|---|
| Backend uç (`UserHeaderController::handle` → `UserHeaderService`) | **Mevcut** (Adım 05) — profil+rol · playlist_shortcuts(≤5) · unread_notifications · anonim özet (user null, liste boş, bildirim 0 — hata değil) |
| `header.php` initial render | Tüm veri cookie/session (`MM_Image`, `MM_Username`) — DB'den gelen YOK → JS fetch tamamlayıcısı |
| `header-manager.js` | **Zaten bağlı** (Adım 05) — fetch + üç blok render mevcuttu |

### §1.1 Adım 10'un asıl işi = mevcut bağın denetimi + boşluk kapatma

| # | Bulgu | Düzeltme |
|---|---|---|
| 1 | **~25 inline `style.*` satırı** (CSP nonce uyumsuz — Css/AGENTS.md §4.1 #3, css-template L74: "Inline style yasak") | Stiller `Css/03_Layout/_header.css`'e taşındı (BEM sınıf); JS'te inline style sıfır |
| 2 | `datalist.innerHTML = ''` (innerHTML yasak — assets kuralları) | `replaceChildren()` |
| 3 | Playlist render **idempotent değildi** (2. çağrıda linkler çoğalırdı) | Her render'da `playlistContainer.replaceChildren()` → çoğalma yok |
| 4 | Bildirim rozeti tetikleyicisi (`.header-action--notifications` / `[data-action="notifications"]`) **DOM'da yok** | Rozet JS'te korundu (buton DOM'a gelince çalışır); **mockup gate**: T17 header sağ küme = Profile·Wifi·BT·Battery·Logout — bildirim ikonu PNG'de YOK → DOM'a eklenmedi (Guardrail #11; §10 yerleşim değişikliği onayı gerekir) |

## §2 Değişen Dosyalar

| Dosya | Değişiklik |
|---|---|
| `assets.coremusic.net/Css/03_Layout/_header.css` | `--header-badge-bg` token (`.header` scope — dosyanın mevcut token deseni); `.header-dropdown-playlists` + `__title` + link; `.header-notification-badge` (+ `[hidden]`) + tetikleyici `position:relative` — hepsi inline'ların birebir taşınması (görünüm değişmedi) |
| `assets.coremusic.net/js/header-manager.js` | Inline style satırları kaldırıldı (class yeterli); idempotent playlist render; `badge.hidden` (display inline yerine); `datalist.replaceChildren()` |
| `assets.coremusic.net/tests/components/header-manager.spec.js` | **Yeni** — 6 test |
| `.ai/reports/backend-step10-curl-pc.ps1` | Sunucu-PC curl kanıtı (12 kontrol) |

## §3 Hard Rules Karşılığı

| Kural | Durum |
|---|---|
| Vanilla ES6+ · `var`/`eval`/`innerHTML` yasak | ✓ (innerHTML→replaceChildren; `node --check` temiz) |
| CSRF: JS token'ı HttpOnly cookie'den okumaz | ✓ Tasarım (Adım 08 katmanı): DOM `input[name=csrf_token]` → `GET /api/v1/auth/me` fallback. header-manager'da state-changing POST yok (logout GET link) → POST 403 kanıtı curl'de |
| XSS: textContent-only | ✓ testte doğrulandı (payload `alert` img OLUŞTURMADI) |
| `credentials:'include'` + fail-safe | ✓ mevcut kodda (`!response.ok`→return, catch→sessiz, PHP render kalır) |
| Mockup gate | ✓ T17 home-dashboard header okundu — yerleşim DEĞİŞMEDİ; bildirim butonu eklenmedi (PNG'de yok) |
| Katman (Css): 03_Layout + BEM + şablon (Guardrail #16) | ✓ css-template.md ön kontrolü yapıldı; `.header-dropdown-playlists__element` BEM |

## §4 Testler

| Suite | Önce | Şimdi |
|---|---|---|
| `header-manager.spec.js` | — | **6/6** (üç blok · CSP inline-sıfır · anonim fail-safe · 500→DOM sabit · idempotent · XSS) |
| JS tam suite (vitest) | 178 | **184/184** |

## §5 Canlı Curl Kanıtı (sunucu PC — ⚠️ VERIFICATION REQUIRED §8)

`.ai/reports/backend-step10-curl-pc.ps1` → `CM_TEST_EMAIL`/`CM_TEST_PASSWORD` sonrası repo kökünden (12 kontrol):

| # | Kontrol | Beklenen |
|---|---|---|
| 1-5 | Oturumsuz header-data | 200 + `is_authenticated:false` + user null + playlist 0 + bildirim 0 (anonim özet — §5.5 oturumsuz header) |
| 6 | Login | 200 |
| 7-11 | Oturumlu header-data | 200 + üç blok: profil adı/rol (dolu) · playlist_shortcuts (dizi) · unread_notifications (int) — §5.5 login senaryosu |
| 12 | csrf'siz POST `/music/history` | **403** (§5.4 CSRF akış kanıtı) |
| 13 | Oturumsuz GET `/music/recent` | 401 |

Ayrıca server PC: `composer test` + history sayfası **header dahil** konsol temizlik kontrolü (header her sayfada görünür → §2 "konsol hatası yok").

## §6 Bilinen Sınırlar / Sonraki Adım

1. **Bildirim butonu**: mockup'ta yok → rozet DOM'a gelene kadar görünmez (tasarım kararı mockup gate'de; istenirse §10 + onay ile eklenir).
2. `search_suggestions` datalist kodu korundu (novasearch P1-1=A — kapsam dışı, dokunulmadı).
3. Header'da logout GET link — login/logout durumları curl + browser ile sunucu PC'de doğrulanır.
