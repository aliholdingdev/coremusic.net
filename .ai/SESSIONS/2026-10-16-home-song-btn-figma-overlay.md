---
title: "2026-10-16-home-song-btn-figma-overlay — Home song butonları Figma kapak overlay'leri (cd-case + tur rozeti + Show CD 0.35)"
type: session
category: sessions
version: 1.0.0
status: active
authority: "Çıktı: HomeSongButton 3-img markup (art-img/art-case/art-badge/art-cd) + RecentTracks k/cd bayrakları · Component suite 22/22 (76 assertion) · commit b0921d8"
updated: 2026-10-16
tier: 2
domain: frontend
ssot: false
risk: low
owner: "Vault Steward"
depends-on: [".ai/AGENTS.md", ".ai/CLAUDE.md", ".ai/.png/home-1024/"]
---

# Home Song Button — Figma Kapak Overlay'leri Kapanış Kaydı

> **Tarih:** 2026-10-16 · **Hedef:** `home.coremusic.net/include/Component/HomeSongButton.php` + `assets.coremusic.net/Css/04_Components/c-home-song-btn.css`
> **Commit:** `b0921d8` (5 dosya, +345/−9) — pathspec commit (çalışma ağacındaki diğer oturumların dosyalarına GİRİLMEDİ)
> **Durum:** TAMAM — 22/22 test yeşil · görsel son kullanıcı kontrolü beklemede

---

## 1. Bu oturumda ne tamamlandı

1. **Kullanıcı isteği (ilk mesaj):** Figma data'ya göre `home.php` song button'larında
   "cd case ve cd ico ve song/video icon" eksik ("o kesik almış"). Mockup Gate:
   `.ai/.png/home-1024/Linux  1024 - Home Page.png` + `nodes-1024-1920.json` (raw Figma) okundu.
2. **Figma analizi (kanıt):** Kart kapağı = art (`e5750d06`/`8b9c369e`) + `cd_case` rect
   (imgRef `0ade8416` = `cd-case.png`, inset 0) + 10×10 köşe ikonu
   (`80f9900c` = `music.png` / `57e34b59` = `video.png`, sağ-alt 2px inset). Kullanıcı onayıyla
   **CD disk overlay** (aşağıda) eklendi; `icons8-cd-48` diski kartta YOK (yalnız footer/player).
3. **"Show CD" düğümü doğrulandı (tur 2 düzeltmesi):** `istockphoto-172197150-612x612-removebg-preview`
   [I1639:9901;880:10353] — `componentPropertyReferences.visible = "Show CD#880:75"`,
   **opacity 0.35**, rect 51×34 @ (cover x+0, y+4) / cover 51×43, scaleMode FILL.
   Figma API izole render alındı (`images/:key?ids=I1639:9901;880:10353&scale=4`): beyaz disk
   #F1F1F1, alpha 89/255 ≈ 0.35 render'a gömülü. Yerel eşdeğer: `Image/res-pink/cd-ico.png`
   (48×48 opaque beyaz CD) → CSS `opacity: 0.35` ile birebir.
   **Kapsam:** CD düğümü Figma'da SADECE `Type=Plist Video Button` varyantında
   (1639:9901 · 2890:7924 · 2890:7966 = hepsi "En Sevilen Supermix'im"). Plist Song
   (1639:9899/9900) ve En Son butonlarında CD node'u YOK (grep ile kanıtlandı).
4. **Uygulama:** backend-developer (2 dispatch) + ui-designer (2 dispatch) subagent'larla;
   subagent commit ATMADI — orkestratör commitledi.

## 2. Uygulama detayı

| Katman | Değişiklik |
|---|---|
| `HomeSongButton.php` | `k?: string` ('song'→music.png / 'video'→video.png, default 'song') + `cd?: bool` (Show CD) parametreleri; markup `art-img` → `art-case` → `art-badge` → `art-cd` (Figma z-sırası, CD en üstte); `alt="" aria-hidden="true"`, `$h()` escape; stream regex korundu |
| `RecentTracksComponent.php` | seed'lere `'k' => 'song'|'video'` (Kış Masalı Ensturmental + En Sevilen Supermix'im = video) + Supermix'e `'cd' => true`; mapping `'k' => (string)($t['k'] ?? 'song')`, `'cd' => (bool)($p['cd'] ?? false)`; `dbTracks()` dokunulmadı (type kolonu YOK — uydurma kolon yok) |
| `c-home-song-btn.css` | `.art` → `position:relative`; geniş `__art img` seçicisi `__art-img`'e daraltıldı; `__art-case` (inset 0, object-fit:fill, pointer-events:none) · `__art-badge` (sağ-alt 2px, 10px/contain) · `__art-cd` (top 9.3%, height 79%, width 100%, object-fit:cover, **opacity 0.35**, pointer-events:none) · `--wide` rozet 12px |
| Testler | `HomeSongButtonTest` +6 (rozet eşlemesi ×4 · sıralama+kart-başı-teklik · cd görünürlüğü) · `RecentTracksComponentTest` +5 (k sayımı embedded/wide · cd 1/3 + 2/6 · En Son 0 cd · override); **suite 22/22 (76 assertion)** |

## 3. Kararlar

| Karar | Gerekçe |
|---|---|
| `icons8-cd-48` diski eklenmedi | Figma'da kartta CD DISK node'u yok (Show CD ≠ disk; istockphoto overlay = beyaz CD görseli). PNG mockup zoom da disk göstermiyor (PNG > Figma referans sırası) |
| Show CD = yerel `cd-ico.png` + CSS opacity 0.35 | Figma izole render alpha'sı 0.35 PNG'ye gömülü; opaque yerel asset ile CSS'te birebir uygulanır. Yeni asset dosyası üretilmedi |
| ~~CD yalnız Supermix (Plist Video) kartında~~ → **GENİŞLETİLDİ: tüm kartlar** (commit `c419f8a`) | İlk tur Figma grep'i node'u yalnız `Plist Video Button` varyantında buldu (1639:9901×2, 2890:7924, 2890:7966); kullanıcı ikinci geri bildirimde ("cd ico yok" + ekran görüntüsü) kapsamı düzeltti — Figma'da **Show CD prop'u tüm instance'larda True** (node eksikliği tasarımcı ihmali). `cd` flag'i kaldırıldı, markup her kartta 4 img |
| Plist Video istockphoto 51×34 overlay'ı "skip" kararı GERİ ALINDI | Tur 1'de "PNG'de görünmüyor → skip" denmişti; kullanıcı "cd ico var, opacity düşürmüş" diyerek düzeltti — opaklık 0.35 olduğu için PNG'de seçik kalıyordu |

## 4. Sorunlar ve çözümleri

| Sorun | Çözüm |
|---|---|
| Figma images API 400 (`scale` 0.01–4 arası) | scale=8 yerine scale=4; instance alt-düğüm ID'si (`I…;…`) images API'de kabul edildi |
| FIGMA_FILE_KEY `.env.figma`'da yok (yalnız FIGMA_TOKEN) | `.ai/ui-design/reference/figma/extracted-1047-15802.md:13313` içindeki figma.com URL'inden alındı (dosya `.ai/.env.figma`'ya YAZILMADI — kural gereği) |
| `HistoryPageTest` / `NowPlayingPageTest` full-suite 2 fail | ÖNCEDEN mevcut, bu göreve AİT DEĞİL: başka oturumun `pages/history.php` / `pages/nowplaying.php` working-copy değişiklikleri (`git status: M`). `⚠️ VERIFICATION REQUIRED` — o oturuma devredildi, commit'e alınmadı |
| Çalışma ağacında 100+ dosyalık başka oturum işleri (K0100 rename staging dahil) | Commit **pathspec** ile yapıldı (`git commit -- <5 dosya>`); index'deki staging aynen bırakıldı |

## 5. Değişen dosyalar (commit `b0921d8`)

| Dosya | Action |
|---|---|
| `home.coremusic.net/include/Component/HomeSongButton.php` | modified (k + cd markup) |
| `home.coremusic.net/include/Component/RecentTracksComponent.php` | modified (k/cd seed + mapping) |
| `home.coremusic.net/tests/Unit/Component/HomeSongButtonTest.php` | modified (+6 test) |
| `home.coremusic.net/tests/Unit/Component/RecentTracksComponentTest.php` | modified (+5 test) |
| `assets.coremusic.net/Css/04_Components/c-home-song-btn.css` | modified (overlay kuralları — dosya, önceki wide-layout WIP bloğunu da çalışma ağacından beri barındırıyor) |

## 6. Öğrenilenler (MEMORY)

- **"Show CD" prop ≠ `icons8-cd-48` disk:** Figma component setinde Show CD, `cd_case` grubundaki
  istockphoto beyaz CD overlay'ini (opacity 0.35) açar — disk değil. Prop adından disk sonucu
  çıkarımı YAPILMAZ; `componentPropertyReferences` + düğüm adı okunur.
- **Figma images API instance alt-düğüm render'ı:** `ids=I1639:9901;880:10353` (URL-encoded `;`)
  çalışır; scale ≤4. Node opacity render'ın alfa kanalına gömülü gelir (89/255 = 0.35 ölçüldü).
- **FIGMA_FILE_KEY env'de değil:** raw JSON'daki `figma.com/design/<key>` linkinden bulunabilir;
  `.env.figma`'ya anahtar ekleme YASAK (AGENTS.md §13.9.3).

## 7. Sonraki adımlar

- [ ] Kullanıcı görsel son kontrol (sayfa yenile: Supermix kartında CD overlay, şarkılarda rozet türü)
- [ ] CD'nin tüm kartlara yaygınlaştırılıp yaygınlaştırılmayacağı kullanıcının kararı
      (Figma verisi yalnız Supermix diyor — yaygınlaştırma istenirse tek satır mapping değişikliği)
- [ ] `git push` kararı kullanıcıya ait (atılmadı)
- [ ] Devredilen: `pages/history.php` / `pages/nowplaying.php` working-copy değişikliklerinin sahibi oturum

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
