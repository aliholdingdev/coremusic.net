---
title: "CoreMusic — ADR-115: Media Yönetimi Admin Panelde — Faz 3 Media Web Paneli İptali"
type: adr
category: architecture
date: 2026-11-01
status: draft
authority: ADR-115 Karar Metni (SSOT)
related: "ADR-039 (port/servis — B8/M-3 AÇIK) · ADR-112 (media soft-delete + HTTP API) · media.coremusic.net/AGENTS.md §3.2 · .ai/.rules/karar-kurallari.md §4"
---

# ADR-115: Media Yönetimi Admin Panelde — Faz 3 Media Web Paneli İptali

**Durum:** draft — kullanıcı kararı ALINDI (2026-11-01; kullanıcı yanıtındaki yön: "media yönetimi admin.coremusic.net'de yönetilir")
**Karar Veren:** Bayram Ali
**Bulgu kaynağı:** karar turu #4 — B5 / media TODO M-2 "Faz 3 GUI uygulama kararı" (öneri: "ayrı görev" vs "home genişlemesi"; kullanıcı üçüncü yönü verdi: admin)

## 1. Bağlam

| # | Kanıt (disk) | İçerik |
|---|--------------|--------|
| 1 | `media.coremusic.net/docs/faz3-gui-spec.md` v0.1.0 (537 satır, 2026-09-29) | media'nın kendi web arayüzü — 8 ekran (E1 katalog → E8 koleksiyonlar), 6 `/api/*` endpoint, PHP 8.4, ADR-039 port 5000/6000 (satır 26); iki açık VR: breakpoint çakışması (§6, satır 453) ve BEM `Cxx` eşlemesi (§5.4 VR-6) |
| 2 | `media.coremusic.net/AGENTS.md` §3.2 | `faz3-gui-spec.md` → UI Designer, "**uygulama ayrı görev**" — diskte canlı olan tek "ayrı görev" kaydı |
| 3 | `media.coremusic.net/TODO.md:23` (M-2) | kaydı "spec §2 'ayrı görev'"e bağlar — ÇELİŞKİ: "ayrı görev" ifadesi spec'te 0 eşleşme (grep, 2026-11-01; spec'te sadece L72 "Ana görev" başlık sütunu geçer) → disk kazanır, kaynak atfı AGENTS §3.2'dedir |
| 4 | `.ai/TODO.md:98` (B5) | açık karar noktası: "ayrı görev vs home genişlemesi" |
| 5 | `admin.coremusic.net` (2026-11-01 Test-Path + grep ölçümü) | tam boot seti (AGENTS · CLAUDE · CONTEXT · TODO · WORKFLOW · `durum.md`) + `pages/` 5 ekran + layout (`admin-dashboard`, `admin-playlist`, `admin-settings`, `admin-song-add`, `admin-songs`) — **media referansı 0** |
| 6 | media HTTP API | upload kapı hattı + stream (ADR-112 işi, Faz 5) panel kararıyla bağımsız; API dokunulmaz |

## 2. Karar

| # | Kural |
|---|---|
| 1 | Media yönetim arayüzü `admin.coremusic.net` panelinde yürütülür; admin media genişlemesi YENİ ve AYRI bir görevdir. Bu ADR yalnız sahiplik kuralı yazar — admin GUI kodu yazılmaz. |
| 2 | `media.coremusic.net`'in kendi web paneli (`faz3-gui-spec.md` v0.1.0, 8 ekran) İPTAL edilir; spec `status: superseded` + ADR-115 notu ile arşivlenir, screen-spec üretimi bu yoldan yapılmaz. |
| 3 | "Ayrı görev" kayıt zinciri revize edilir: media AGENTS §3.2 uygulama hedefi admin'e bağlanır, media TODO M-2 karara bağlanır, `.ai/TODO.md` B5 karar notu alır. |
| 4 | Admin media ekranları media HTTP API'yi API sözleşmesi üzerinden tüketir; subdomain'ler birbirinin kodunu import etmez (AGENTS §5/§6). Mevcut `admin-*` sayfa deseni yönü korur. |
| 5 | Spec'teki iki VR (breakpoint çakışması §6, BEM `Cxx` §5.4 VR-6) superseded spec'te kapanmaz; admin görevi planlanırken güncel bileşen envanteri ve `05-responsive-architecture` üzerinden yeniden değerlendirilir. |
| 6 | Katalog görüntüleme (herkese açık sanatçı/albüm/parça) ekranları için bugün karar alınmadı — açık madde; görüntüleme ihtiyacı doğarsa ayrı karar gerekir. |

## 3. Seçenekler

| Seçenek | Sonuç | Durum |
|---|---|---|
| **(kabul): admin panelde media yönetimi** | tek yönetim konsolu; mevcut admin oturum/ekran deseni genişler; admin'de media bugün 0 yüzey → greenfield ekran seti | KABUL (kullanıcı, 2026-11-01) |
| A: ayrı görev (media'nın kendi paneli) | 8 ekranlık ikinci panel + ayrı port/auth/deployment yüzeyi (spec'in kaydettiği yön) | RED |
| B: home genişlemesi | yönetim yüzeyi halka açık home panelinde — rol/risk karışıklığı | RED |

## 4. Etkiler

- `faz3-gui-spec.md` → superseded (v0.1.1); içerik geçmiş kayıt olarak korunur.
- `media.coremusic.net/TODO.md` M-2 → kapanır (ADR-115 referanslı); `.ai/TODO.md:98` B5 → karar notu.
- Port kararı B8/M-3 (ADR-039 :5000/:6000) AÇIK KALIR — panel iptali media API'nin yerleşim/port sorusunu ortadan kaldırmaz; ayrı karar.
- `admin.coremusic.net/TODO.md`'ye "media yönetimi (ADR-115)" yeni görev maddesi notu düşülür; detaylı kapsam planlanması ayrı görevdir.

**Referanslar:** karar kural SSOT `.ai/.rules/karar-kurallari.md` §4 · kompakt kural `.ai/.rules/kurallar.md` (KURAL satırı) · `.ai/TODO.md:98` B5
