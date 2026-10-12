---
title: "CoreMusic — Screen Specification Index (T07 · Shared · T17)"
type: spec
category: index
date: 2026-09-27
updated: 2026-09-29
version: 6.2.0
status: active
authority: "Single Source of Truth (SSOT) — .ai/ui-design/screens/00-ascii-art-index.md"
governance: Red Team · Human Mode · Truth Mode
total_spec_files: 20
total_active: 19
total_draft: 1
total_png_mockups: 19
total_figma_frames: 22
changelog: "v6.0.0 — screens/ dizini tamamen yıkılıp 20 yeni spec dosyasıyla yeniden yazıldı; merkezi indeks sıfırdan üretildi (eski indeks içeriği kullanılmadı). v6.1.0 (2026-09-28) — §7 açık konular: madde 2 (4 spec node etiketi v1.1.0) ve madde 3 (mockup-index v6.1.0 senkronu) düzeltildi; madde 1 = T08→T07 taşıma ÖNERİSİ (owner onayı). v6.2.0 (2026-09-29) — embedded dizin taşındı → T07-embedded (owner onayı, matrix L92): 12 spec `tier: T07` + version 1.1.0; §7 madde 1 kapatıldı."
---

# CoreMusic — Screen Specification Index (T07 · Shared · T17)

**Zorunlu Bağlantılar:** [[../00-device-matrix]] · [[../01-mockup-index]] · [[../02-component-inventory]] · [[../03-implementation-plan]] · [[../04-accessibility-gaps]] · [[../05-responsive-architecture]] · [[../tokens/design-tokens-master]]

---

## 1. Kapsam, SSOT Sırası ve Okuma Protokolü

Bu dosya, `.ai/ui-design/screens/` altındaki **20 screen spec dosyasının tek indeksidir**. Alt dosyalar kendini indeks ilan edemez.

```
PNG Mockup > Figma (extracted) > ASCII Art > Component Inventory > Tokens > Implementation Plan
```

- **PNG > Figma > ASCII:** çelişki durumunda PNG mockup kazanır; Figma yalnız koordinat/dolgu referansıdır; ASCII spec PNG'ye birebir hizalıdır.
- Çelişki → spec içinde `ÇELİŞKİ` etiketi, doğrulanamayan değer → `⚠️ VERIFICATION REQUIRED` (uydurma sayılmaz).
- **Okuma protokolü:** (1) tier satırını bul → (2) spec dosyasını aç, §1 ASCII yerleşim + §7 PNG referansı → (3) token'ları [[../tokens/design-tokens-master]] içinden uygula.

## 2. Tier Matrisi (disk gerçeği — 2026-09-27 doğrulaması)

| Tier | Dizin | Cihaz (spec frontmatter) | Viewport | Spec dosya | Status |
|------|-------|--------------------------|----------|-----------|--------|
| T07 | `screens/T07-embedded/` | RPi5 7" Touch (Embedded) | 1024×600 | 12 | 12 active |
| T07 | `screens/shared/` | RPi5 7" Touch (Embedded) — auth ekranları | 1024×600 | 6 | 6 active |
| T17 | `screens/T17-monitor-22fhd/` | 22" FHD Monitor (Desktop) | 1920×1080 | 2 | 1 active · 1 draft |
| **Toplam** | — | — | — | **20** | **19 active · 1 draft** |

> **✅ KAPANDI (2026-09-29 — tier ataması):** dizin taşındı → `screens/T07-embedded/` (owner onayı) + 12 spec `tier: T07`; kanıt [[../00-device-matrix]] L92 `T07 = RPi5 7" 1024×600`, L93 `T08 = RPi5 10" 1280×800`.
>
> **PNG envanteri:** 19/19 PNG diskte mevcut (`.ai/.png/home-1024/` 12 · `.ai/.png/home-1920/` 1 · `.ai/.png/shared-1024/` 6 → [[../01-mockup-index]]). Spec başına tek PNG; **istisna:** T17 `welcome-popup.md` (draft) — 1920 popup PNG'si yok.

## 3. Screen Files — T07 Embedded (1024×600) — 12 dosya

| # | Dosya | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status | Açıklama |
|---|-------|------------------|-------------------------------|:------:|----------|
| 1 | [[T07-embedded/home-dashboard]] | `1639:10160` (COMPONENT) | `.ai/.png/home-1024/Linux  1024 - Home Page.png` | active | Ana ekran: header, Now Playing + widget sütunları, 3 sütun kart satırı, footer player |
| 2 | [[T07-embedded/welcome-popup]] | `2831:10267` | `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` | active | Karşılama modalı: 600×308 manzara fotoğrafı + hoş geldin metni |
| 3 | [[T07-embedded/albums]] | `2831:9176` | `.ai/.png/home-1024/Linux  1024 - Albumler Page.png` | active | Albümler: sol %60 kart grid, sağ %40 detay paneli |
| 4 | [[T07-embedded/album-detail]] | `2831:10086` (h=612 ⚠️) | `.ai/.png/home-1024/Linux  1024 - Albumler Details Detay Page.png` | active | Albüm detay: 300×300 kapak, parça listesi, metadata |
| 5 | [[T07-embedded/singer]] | `2831:9273` | `.ai/.png/home-1024/Linux  1024 - Singer Page.png` | active | Sanatçılar: dairesel kartlar (radius 50%), sağ detay paneli |
| 6 | [[T07-embedded/playlist]] | `2831:9443` | `.ai/.png/home-1024/Linux  1024 - Playlist Page.png` | active | Çalma listeleri: sol liste, sağ seçili parça paneli |
| 7 | [[T07-embedded/playlist-video]] | `2831:9710` | `.ai/.png/home-1024/Linux  1024 - Playlist Page - Video Played.png` | active | Çalma listesi + video oynatma görünümü |
| 8 | [[T07-embedded/browse]] | `2831:9555` | `.ai/.png/home-1024/Linux  1024 - Göz At Page.png` | active | Dosya yöneticisi: disk/klasör ağacı ve gezinme çubuğu |
| 9 | [[T07-embedded/browse-clicked]] | `2831:10282` (v5) + 4 varyant | `.ai/.png/home-1024/Linux  1024 - Göz At - Tıklama Clicked.png` | active | Tıklama sonrası dosya listesi: 4 kolonlu liste + sağ disk paneli (§6 varyantlar) |
| 10 | [[T07-embedded/wifi-quick]] | `2831:9665` | `.ai/.png/home-1024/Linux  1024 - Wifi Quick Page Base.png` | active | WiFi quick panel: bağlı ağ + ağ listesi, sinyal badge'leri |
| 11 | [[T07-embedded/wifi-connect-light]] | `2831:9644` | `.ai/.png/home-1024/Linux  1024 - Wifi Connect Light.png` | active | WiFi şifre modalı: Bağlan / İptal formu (overlay) |
| 12 | [[T07-embedded/bluetooth-quick]] | `2831:9687` | `.ai/.png/home-1024/Linux  1024 - Bluetooth Quick Page Base.png` | active | Bluetooth quick panel: bağlı + aranabilir cihaz listesi |

## 4. Screen Files — Shared / Auth (1024×600) — 6 dosya

| # | Dosya | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status | Açıklama |
|---|-------|------------------|-------------------------------|:------:|----------|
| 13 | [[shared/login]] | `2831:9826` | `.ai/.png/shared-1024/Linux  1024 - Login Girl.png` | active | Giriş: e-posta/şifre formu, checkbox, sosyal giriş butonları |
| 14 | [[shared/register-step1]] | `2831:9894` | `.ai/.png/shared-1024/Linux  1024 - Register Girl.png` | active | Kayıt adım 1: kullanıcı adı + e-posta, Devam Et |
| 15 | [[shared/register-step2]] | `2831:9957` | `.ai/.png/shared-1024/Linux  1024 - Register Girl step 2.png` | active | Kayıt adım 2: şifre + şifre tekrar, Devam Et |
| 16 | [[shared/register-step3]] | `2831:10020` | `.ai/.png/shared-1024/Linux  1024 - Register Girl step 3.png` | active | Kayıt adım 3: telefon, koşul checkbox'ı, Kayıt Ol |
| 17 | [[shared/select-gender]] | `2831:9748` | `.ai/.png/shared-1024/Linux  1024 - Select Gender.png` | active | Cinsiyet seçimi: Kız / Erkek / Diğer 3 buton |
| 18 | [[shared/select-gender-selected]] | `2831:9787` | `.ai/.png/shared-1024/Linux  1024 - Select Gender - selected.png` | active | Cinsiyet seçimi — seçili durum: pembe vurgulu buton, aktif Devam Et |

## 5. Screen Files — T17 Desktop Monitor (1920×1080) — 2 dosya

| # | Dosya | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status | Açıklama |
|---|-------|------------------|-------------------------------|:------:|----------|
| 19 | [[T17-monitor-22fhd/home-dashboard]] | `2831:13747` | `.ai/.png/home-1920/Linux - 1920 - Home.png` | active | 1920×1080 ana ekran: 3 sütun üst blok, 10+6 kart satırı, footer player |
| 20 | [[T17-monitor-22fhd/welcome-popup]] | `2876:6439` (modal 600×308) | `⚠️ VERIFICATION REQUIRED — 1920 popup PNG'si yok` | **draft** | Karşılama modalı — 1024 popup PNG + Figma'dan türetildi, PNG doğrulaması bekliyor |

## 6. Figma Kare Sayımı (22) + browse-clicked Varyantları

**20 spec md ↔ 22 Figma frame:** 17 md tek kök kare (T07: 11 + shared: 6) + `browse-clicked` tek başına **5 varyant kare** → `17 + 5 = 22`.
T17'nin 2 md'si bu 22'ye **dâhil değil** (kendi 1920 kareleri ayrı referanstır: `2831:13747` Home, `2876:6439` modal) → 1024+1920 toplam referans kare = 24.

**browse-clicked varyantları (Figma 5 kare — spec §7):**

| Varyant | Figma node | Not |
|---------|-----------|-----|
| v1 | `1976:11757` | sağ panel yok, footer kaymış |
| v2 | `1976:12013` | playlist başlığı "Son Dinlenler" (typo) |
| v3 | `1980:13448` | başlık metni değişimi (Favoriler) |
| v4 | `1980:13692` | 4 playlist satırı + süreler |
| **v5 (spec = bu)** | `2831:10282` | "Musics : Root" dosya listesi, 253×420 sağ panel |

## 7. Quality Report (2026-09-28)

| Kontrol | Sonuç |
|---------|-------|
| Spec dosya sayısı | **20/20** (T07 12 · shared 6 · T17 2) ✅ glob + frontmatter doğrulaması |
| Status dağılımı | **19 active · 1 draft** (draft = `T17-monitor-22fhd/welcome-popup`) ✅ |
| PNG eşleştirme | **19/19 PNG diskte var**; 1 spec PNG'siz (T17 welcome, `status: draft`) ✅ |
| Figma kare | **22** (17 kök + browse-clicked 5 varyant) + 2 T17 referans kare ✅ |
| `wiki-link` çözümü | **32 link · 27 benzersiz hedef → 27/27 çözülebilir** (20 spec + 7 üst doküman; çözülemeyen link yok) ✅ |
| Yasaklar | Yazım: bu indeks + 4 düzeltilen spec (İŞ 2 node etiketi, v1.1.0, 2026-09-28); diğer 16 spec'e dokunulmadı ✅ |
| Yazma protokolü | `vault-utf8-writer.mjs write` — UTF-8, BOM'suz + verify (BOM/mojibake/CJK/NUL) ✅ |

**⚠️ AÇIK KONULAR (VERIFICATION REQUIRED):**
1. ✅ **taşındı (2026-09-29 — owner onayı):** embedded dizin 1024×600 viewport ile çelişiyordu → `screens/T07-embedded/` + 12 spec frontmatter `tier: T08 → T07`. Kanıt: [[../00-device-matrix]] L92 `T07 = RPi5 7" 1024×600`, L93 `T08 = RPi5 10" 1280×800`, L95 "Welcome popup (T07)", L295 "1024×600 → T07 Embedded (RPi5 7")". `shared/*` 6 spec zaten `tier: T07` ✓; T17 değişmez.
2. ✅ **düzeltildi (2026-09-28 — node etiketi düzeltmesi, 4 spec v1.1.0):** `shared/login` `2831:9838` → kök `2831:9826` (9838 = GROUP social/btns); `shared/select-gender` `2831:9760` → `2831:9748` (9760 = GROUP button/nötur); `shared/select-gender-selected` `2831:9808` → `2831:9787` (9808 = TEXT "Erkek"); T17 welcome `2831:13747` "Welcome Div" yazımı → Welcome Div = GROUP `2831:10267` (`2831:13747` = FRAME "Linux - 1920 - Home"). Kanıt: spec §7 yerinde düzeltme notları + `reference/figma/extracted-1047-15802.md` / `extracted-1920.md`.
3. ✅ **düzeltildi (2026-09-28):** [[../01-mockup-index]] v6.1.0 — §4 = 20 spec (dosya · Figma node · PNG · status, tier bazlı), §7 = yeni adlar + eski ad haritası (silinenler → yeni karşılık).
4. `screens/T07-embedded/home-dashboard` kök node'u Figma **COMPONENT** (`1639:10160`), FRAME değil. *(bilgi notu — madde 1 taşınması önerilirse korunmalı)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Version:** 6.2.0 — §7 açık konular güncellendi: madde 2-3 düzeltildi (4 spec node etiketi v1.1.0 + mockup-index v6.1.0), madde 1 = T08→T07 taşıması TAMAMLANDI (2026-09-29, owner onayı, matrix L92) — v6.0.0: 20 spec dosyasının tamamı yeniden yazıldı; bu indeks sıfırdan üretildi (eski v2.1.0 içeriği bayat olduğu için yeniden kullanılmadı)
**Mode:** Red Team · Human Mode · Truth Mode
