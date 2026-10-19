---
title: "2026-10-18 — nowplaying 1024 embedded Figma hizalama (toolbar + sidebar + tablo) + kısmi commit tekniği"
type: session
category: sessions
version: 1.0.0
status: active
authority: "SSOT: Figma node 2831:9443 'Linux 1024 - Playlist Page' (1024x600, orijin 1751,521) + .ai/ui-design/reference/figma/extracted-1047-15802.md"
updated: 2026-10-18
tier: 2
domain: frontend
ssot: true
risk: low
owner: "Vault Steward"
depends-on: [".ai/AGENTS.md", ".ai/CLAUDE.md", ".ai/ui-design/reference/figma/extracted-1047-15802.md"]
---

# nowplaying 1024 Embedded — Figma Hizalama Kapanış Kaydı

> **Tarih:** 2026-10-18 · **Hedef:** `assets.coremusic.net/Css/05_Pages/_player.css`
> **Commit:** `da859ad` (1 dosya, +203/−15) — **kısmi staging**; kullanıcının WIP'i working copy'de kaldı
> **Durum:** TAMAM — 1024×600 ölçüleri birebir, 1920 wide regresyon 0, 480'te taşma yok

---

## 1. Bu oturumda ne tamamlandı

### 1.1 Tier tespiti düzeltildi (yanlış teşhis elendi)

1920 ölçümünde tier `embedded` görünüyordu. `navigator.userAgent` + `BreakpointAPI.detectDevice/tierOf`
ölçüldü: UA = normal Windows Chrome — **`RE_EMBEDDED_UA` (`Raspberry Pi|RPi|aarch64|armv7|armv8|CrOS`) eşleşmesi YOK**.
Neden: önceki `emulate` çağrısı geçersiz parametre (`deviceSize`) ile yapılmış, viewport 1920'ye
memiş. Doğru parametre **`viewport`** (`"WxHxDPR[,mobile][,touch][,landscape]"`).

Ayrıca keşfedilen yapısal gerçek: `page-player--wide/--embedded` **yalnız SSR** (`nowplaying.php` L137,
`$isWide = $dm->shouldRenderWideLayout() || $is4k`) — client JS bunu değiştirmez. `device--*` /
`layout--*` ise client-side viewport'a göre değişir. CSS'in bağlı olduğu sınıf `page-player--*` = UA kararı.

### 1.2 Hizalama düzeltmeleri (hepsi `.page-player--embedded` kapsamında)

| Alan | Figma SSOT | Uygulanan |
|---|---|---|
| Back btn | 2×30 @x27.677 | 30×30 sol hizalı (taban 40×40 → markup sınırı) |
| Toolbar title | "Avalon" 9.5/500 ls0.712 @x100.677 | font + pozisyon birebir |
| Search box | `2831:9476` 159×25 @x597, fill #FFFDFE 0.25, stroke #FF00B2 α1 0.5px, r4 | 159×25 @596; input left5 w130; ikon 15×15 @+138 |
| Tablo grid | satır-içi sınırlar 0/47/250/433/575/682/725 | `47px 203px 183px 142px 75px 74px` = 724 |
| Row | h31, pitch 46 | h31, `margin-bottom:15`, `margin-left:7`, `margin-right:8` (scrollbar payı) |
| thead "/" | `2831:9467` `"     /"` @rel(33,136) → PNG'den glyph rel 47..49.5 | `padding-left:16` + `text-align:left !important` (taban L2347 `center !important`) |
| td dikey | metin +8 | `height:auto` → `align-items:center` (display:flex korunarak) |
| Yıldızlar | x682'den başlar (2 yıldızlı satırda dâhil) | `justify-content:flex-start; margin-top:0` |
| disc | `2831:9494` cd_case 33.546×30 | 33.546×30 `object-fit:cover` (kaynak 108×84 ≠ aspect) |
| Scrollbar | `2831:9473` 3×320 r50 #C4C4C4 0.42 | `::-webkit-scrollbar{width:3px}` |
| Sidebar panel | 219 genişlik, r8 | `right:23; width:219; border-radius:8` |
| Sidebar dikey ritim | rec-label→6.5, thumb→name, rec1→rec2 | `rec-labels mb1.5` · `.rec-labels+.artist-grid mb5.5` · `artist-name mt3` |
| Sidebar yatay | thumb 804, rec-label 804 | `rec-labels ml9`, `artist-grid ml7` |

### 1.3 Taşma guard'ı (bu oturumun kritik bulgusu)

Sabit track toplamı 724px. `page-player--embedded` UA ile verildiği için **narrow viewport'ta da
aktifleşebiliyor** (480×800 ölçümünde panel `clientW 198 / scrollW 732` = **534px yatay taşma**).

Çözüm: grid `@media (min-width: 1024px)` içine alındı (`EMBEDDED_MAX:1024` ile SSOT-hizalı; dosyada
L555'te zaten mevcut kalıp). 1024 altında paylaşımlı fr tablona düşer (min toplam 182px).

**Doğrulama:** 480 → `clientW 198 == scrollW 198` ✓ · 1024×600 → grid birebir + `739==739` ✓

## 2. Regresyon ölçümü

| Viewport | Tier | Sonuç |
|---|---|---|
| 1024×600 | embedded | row0 [32,157,724,31] · grid birebir · sidebar [782,219] · search [596,159×25] · panel 739==739 · doc 1024 ✓ |
| 1920×1024 | wide | row h21 · `108px minmax(0,445fr)…` · sidebar 245 @1660 · recLabels ml0 · stars **center** · scrollbar-width auto · **0 değişim** ✓ |
| 480×800 | embedded | fr fallback, taşma yok ✓ |
| 767×900 | — | viewport uygulanır ama SSR sınıfı tazelemeden stale kaldı (test ortamı artefaktı) |

## 3. ÖĞRENİLEN DERS (kısmi commit)

**`git commit -F <msg> -- <pathspec>` kısmi staging'ı EZER.** `-- <path>` formu yolların working-tree
durumunu implicit olarak `git add` eder (`--only` semantiği). İlk denemede kullanıcının WIP'i de
commit'lendi (`75077d8`, 208/56).

**Doğru yöntem:**
1. `git diff -U3 -- <path>` → hunk'ları ayır (ASCII marker ile sınıflandır; Türkçe regex güvenilmez).
2. Yalnız "benim" hunk'ları tut; **hunk başlıklarındaki `+` satır numaralarını yeniden hesapla**
   (`newStart = oldStart + cumulatifOfset`), yoksa patch geçersiz.
3. Patch'i **LF** ile yaz (`WriteAllLines` CRLF üretir → `patch does not apply`). `[IO.File]::WriteAllText(($lines -join "`n"))`.
4. `git apply --cached --check` → `git apply --cached` (disk'e dokunmaz).
5. **`git commit -F <msg>` — PATHSPEC YOK.** Yalnız index commit edilir.
6. `--recount` kullanma; doğru numaralarla gerekmez.

**Doğrulama zinciri:** `git diff --cached --stat` = beklenen sayı · `git diff --stat` = kalan WIP ·
HEAD blob'unda "bulunmamalı" marker'ları = 0 · working copy ` M` durumunda kalmalı.

**Hata anında kurtarma:** `git reset -q --mixed HEAD~1` (disk'e dokunmaz, index HEAD'e döner),
patch'i yeniden uygula, pathspeciz commit.

## 4. Rapor maddeleri (bu oturumda DOKUNULMADI)

1. **Header/footer dikey örtüşme:** glass 29..579, footer 528..600 → 51px örtüşme; thead y120 vs
   Figma 136, row0 y157 vs Figma 173 (**−16px sabit offset**). Paylaşımlı komponent → kapsam dışı.
2. **Poppins eksik:** repo'da yok (`a-fonts-token.css` 19 @font-face, Poppins 0). En yakın yüz
   `"Avalon Medium"` eklendi.
3. **Markup boşlukları (PHP):** home ikonu ve 2. back butonu Figma'da var markup'ta yok · artist
   name altında 2. satır (3.5/300) yok · "Tümünü Göster" ve "Önerilen Sanatcılar" metinleri markup'ta
   yok (canlıda "Önerilen Sanatçılar"+"Tavsiye Edilenler").
4. **`cd_case` görsel uyuşmazlığı:** Figma IMAGE scaleMode=FILL (genel albüm kapağı) vs canlıda
   `disc-playlist.png` disk ikonu → PHP/asset kararı.
5. **`.player-stars` taban kuralına eşzamanlı düzenleme:** `justify-content:center` yorum satırı
   yapılmış (996 ve 2433). Bu **1920 wide'i etkiler** (stars center → flex-start). Bu oturumda
   benim yapılmadığım için commit EDİLMEDİ — wide'de stars'ın sol hizalanması isteniyorsa ADR gerekir.
6. **Actions/btn container tutarsızlığı:** Figma "Actions Btns" ve pill search görünmez durumda.

## 5. Dosyalar

- `assets.coremusic.net/Css/05_Pages/_player.css` — düzeltme hedefi (commit `da859ad`)
- `assets.coremusic.net/js/core/breakpoints.js` — tier mantığı (salt okundu)
- `home.coremusic.net/pages/nowplaying.php` — markup (salt okundu)
- `.ai/ui-design/reference/figma/extracted-1047-15802.md` + `png/2831-9443-...png` — SSOT
