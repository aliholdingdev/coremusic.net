---
title: "CoreMusic — Screen Specification Index (T07 · Shared · T17)"
type: spec
category: index
date: 2026-09-27
updated: 2026-10-16
version: 6.3.0
status: active
authority: "Single Source of Truth (SSOT) — .ai/ui-design/screens/00-ascii-art-index.md"
governance: Red Team · Human Mode · Truth Mode
total_spec_files: 23
total_active: 22
total_draft: 1
total_png_mockups: 25
total_figma_frames: 25
changelog: "v6.0.0 — screens/ dizini tamamen yıkılıp 20 yeni spec dosyasıyla yeniden yazıldı; merkezi indeks sıfırdan üretildi (eski indeks içeriği kullanılmadı). v6.1.0 (2026-09-28) — §7 açık konular: madde 2 (4 spec node etiketi v1.1.0) ve madde 3 (mockup-index v6.1.0 senkronu) düzeltildi; madde 1 = T08→T07 taşıma ÖNERİSİ (owner onayı). v6.2.0 (2026-09-29) — embedded dizin taşındı → T07-embedded (owner onayı, matrix L92): 12 spec `tier: T07` + version 1.1.0; §7 madde 1 kapatıldı. v6.3.0 (2026-10-16) — Figma sayfası `2161:12438` (Core Music - Music Admin) için 3 yeni admin spec (T17): `admin-layout` · `admin-login` · `admin-register`; PNG 19→25 (`admin-1920/` 3 + `home-1920/` 4), figma frame 22→25; §7 madde 5 (tier T17 vs T42) ve madde 6 (spec'siz 3 PNG) açıldı."
---

# CoreMusic — Screen Specification Index (T07 · Shared · T17)

**Zorunlu Bağlantılar:** [[../00-device-matrix]] · [[../01-mockup-index]] · [[../02-component-inventory]] · [[../03-implementation-plan]] · [[../04-accessibility-gaps]] · [[../05-responsive-architecture]] · [[../tokens/design-tokens-master]]

---

## 1. Kapsam, SSOT Sırası ve Okuma Protokolü

Bu dosya, `.ai/ui-design/screens/` altındaki **23 screen spec dosyasının tek indeksidir**. Alt dosyalar kendini indeks ilan edemez.

```
PNG Mockup > Figma (extracted) > ASCII Art > Component Inventory > Tokens > Implementation Plan
```

- **PNG > Figma > ASCII:** çelişki durumunda PNG mockup kazanır; Figma yalnız koordinat/dolgu referansıdır; ASCII spec PNG'ye birebir hizalıdır.
- Çelişki → spec içinde `ÇELİŞKİ` etiketi, doğrulanamayan değer → `⚠️ VERIFICATION REQUIRED` (uydurma sayılmaz).
- **Okuma protokolü:** (1) tier satırını bul → (2) spec dosyasını aç, §1 ASCII yerleşim + §7 PNG referansı → (3) token'ları [[../tokens/design-tokens-master]] içinden uygula.

## 2. Tier Matrisi (disk gerçeği — 2026-10-16 doğrulaması)

| Tier | Dizin | Cihaz (spec frontmatter) | Viewport | Spec dosya | Status |
|------|-------|--------------------------|----------|-----------|--------|
| T07 | `screens/T07-embedded/` | RPi5 7" Touch (Embedded) | 1024×600 | 12 | 12 active |
| T07 | `screens/shared/` | RPi5 7" Touch (Embedded) — auth ekranları | 1024×600 | 6 | 6 active |
| T17 | `screens/T17-monitor-22fhd/` | 22" FHD Monitor (Desktop) — home + admin | 1920×1080 | 5 | 4 active · 1 draft |
| **Toplam** | — | — | — | **23** | **22 active · 1 draft** |

> **✅ KAPANDI (2026-09-29 — tier ataması):** dizin taşındı → `screens/T07-embedded/` (owner onayı) + 12 spec `tier: T07`; kanıt [[../00-device-matrix]] L92 `T07 = RPi5 7" 1024×600`, L93 `T08 = RPi5 10" 1280×800`.
>
> **PNG envanteri:** 25/25 PNG diskte mevcut (`.ai/.png/home-1024/` 12 · `.ai/.png/home-1920/` 4 · `.ai/.png/shared-1024/` 6 · `.ai/.png/admin-1920/` 3 → [[../01-mockup-index]]). 22 spec'in PNG'si var; **istisna:** T17 `welcome-popup.md` (draft) — 1920 popup PNG'si yok; `home-1920/` içindeki **3 PNG'nin spec'i yok** (§7 madde 6).

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

## 5. Screen Files — T17 Desktop Monitor (1920×1080) — 5 dosya

| # | Dosya | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status | Açıklama |
|---|-------|------------------|-------------------------------|:------:|----------|
| 19 | [[T17-monitor-22fhd/home-dashboard]] | `2831:13747` | `.ai/.png/home-1920/Linux 1920 - Home.png` | active | 1920×1080 ana ekran: 3 sütun üst blok, 10+6 kart satırı, footer player |
| 20 | [[T17-monitor-22fhd/welcome-popup]] | `2876:6439` (modal 600×308) | `⚠️ VERIFICATION REQUIRED — 1920 popup PNG'si yok` | **draft** | Karşılama modalı — 1024 popup PNG + Figma'dan türetildi, PNG doğrulaması bekliyor |
| 21 | [[T17-monitor-22fhd/admin-layout]] | `2161:12499` | `.ai/.png/admin-1920/Admin - layout.png` | active | Admin ana layout: sol sidebar 250px, navbar, içerik paneli, footer (§7 Tier notu) |
| 22 | [[T17-monitor-22fhd/admin-login]] | `2161:12506` | `.ai/.png/admin-1920/Admin - Login.png` | active | Yönetici girişi: foto sol %74 + sağ 500px glass panel, form + QR bloğu |
| 23 | [[T17-monitor-22fhd/admin-register]] | `2161:12529` | `.ai/.png/admin-1920/Admin - Register.png` | active | Yönetici kaydı: sağ 500px panel, 5 alan + onay kutusu, buton + QR bloğu |

> **Tier notu (2026-10-16):** 3 admin spec `tier: T17` + `viewport: 1920x1080` ile yazıldı — kaynak: [[../00-device-matrix]] §4c CatID eşlemesi `2161:12438` → 1920 Figma sayfaları. Semantik alternatif olan `T42 NAS Web Interface` satırı açık konu olarak §7 madde 5'tedir.

## 6. Figma Kare Sayımı (25) + browse-clicked Varyantları

**23 spec md ↔ 25 Figma frame:** 20 md tek kök kare (T07: 11 + shared: 6 + T17 admin: 3) + `browse-clicked` tek başına **5 varyant kare** → `20 + 5 = 25`.
T17'nin diğer 2 md'si (home-dashboard, welcome-popup) bu 25'e **dâhil değil** (kendi 1920 kareleri ayrı referanstır: `2831:13747` Home, `2876:6439` modal) → 1024+1920 toplam referans kare = **27**.
Sayfa `2161:12438` içinde `visible: false` gizli `mavi` varyant grubu (2 kare) vardır — spec edilmedi, sayılmadı.

**browse-clicked varyantları (Figma 5 kare — spec §7):**

| Varyant | Figma node | Not |
|---------|-----------|-----|
| v1 | `1976:11757` | sağ panel yok, footer kaymış |
| v2 | `1976:12013` | playlist başlığı "Son Dinlenler" (typo) |
| v3 | `1980:13448` | başlık metni değişimi (Favoriler) |
| v4 | `1980:13692` | 4 playlist satırı + süreler |
| **v5 (spec = bu)** | `2831:10282` | "Musics : Root" dosya listesi, 253×420 sağ panel |

## 7. Quality Report (2026-10-16)

| Kontrol | Sonuç |
|---------|-------|
| Spec dosya sayısı | **23/23** (T07 12 · shared 6 · T17 5) ✅ glob + frontmatter doğrulaması |
| Status dağılımı | **22 active · 1 draft** (draft = `T17-monitor-22fhd/welcome-popup`) ✅ |
| PNG eşleştirme | **25/25 PNG diskte var**; 22 spec PNG'li, 1 spec PNG'siz (T17 welcome, `status: draft`); **3 PNG'nin spec'i yok** (madde 6) ✅ |
| Figma kare | **25** (20 kök + browse-clicked 5 varyant) + 2 T17 referans kare + 2 gizli `mavi` varyant kare (spec edilmedi) ✅ |
| `wiki-link` çözümü | **347 link · 6 raporlanan → gerçek kırık 0** (6'sı `AGENTS.md` §13.7 **bilinen sahte kırık** — şablon değişkeni/kaçış dizgeleri: `flow/auth/01-login`, `flow/auth/04-select-gender`, `flow/music/01-playback`, `flow/settings/04-general`, `reference/legacy-inventory` ×2; düzeltme YASAK) ✅ kökten `wiki-link-check.ps1` ölçümü (2026-10-16) |
| Frontmatter gate | `screens-frontmatter-check.ps1` → **24 dosya / 0 sorunlu** ✅ (kökten, `AGENTS.md` §13.6) |
| Yasaklar | Yazım: bu indeks + 3 yeni admin spec + extract doc (2026-10-16); mevcut 20 spec'e dokunulmadı ✅ |
| Yazma protokolü | `vault-utf8-writer.mjs write` — UTF-8, BOM'suz + verify (BOM/mojibake/CJK/NUL) ✅ |

**⚠️ AÇIK KONULAR (VERIFICATION REQUIRED):**
1. ✅ **taşındı (2026-09-29 — owner onayı):** embedded dizin 1024×600 viewport ile çelişiyordu → `screens/T07-embedded/` + 12 spec frontmatter `tier: T08 → T07`. Kanıt: [[../00-device-matrix]] L92 `T07 = RPi5 7" 1024×600`, L93 `T08 = RPi5 10" 1280×800`, L95 "Welcome popup (T07)", L295 "1024×600 → T07 Embedded (RPi5 7")". `shared/*` 6 spec zaten `tier: T07` ✓; T17 değişmez.
2. ✅ **düzeltildi (2026-09-28 — node etiketi düzeltmesi, 4 spec v1.1.0):** `shared/login` `2831:9838` → kök `2831:9826` (9838 = GROUP social/btns); `shared/select-gender` `2831:9760` → `2831:9748` (9760 = GROUP button/nötur); `shared/select-gender-selected` `2831:9808` → `2831:9787` (9808 = TEXT "Erkek"); T17 welcome `2831:13747` "Welcome Div" yazımı → Welcome Div = GROUP `2831:10267` (`2831:13747` = FRAME "Linux - 1920 - Home"). Kanıt: spec §7 yerinde düzeltme notları + `reference/figma/extracted-1047-15802.md` / `extracted-1920.md`.
3. ✅ **düzeltildi (2026-09-28):** [[../01-mockup-index]] v6.1.0 — §4 = 20 spec (dosya · Figma node · PNG · status, tier bazlı), §7 = yeni adlar + eski ad haritası (silinenler → yeni karşılık).
4. `screens/T07-embedded/home-dashboard` kök node'u Figma **COMPONENT** (`1639:10160`), FRAME değil. *(bilgi notu — madde 1 taşınması önerilirse korunmalı)*
5. **Tier ataması (2026-10-16 — owner onayı bekliyor):** 3 admin ekranı `T17`'ye atandı (viewport 1920×1080 + matrix §4c CatID `DM-T17` eşlemesi `2161:12438` → 1920 Figma sayfaları). [[../00-device-matrix]] §4c'deki `T42 NAS Web Interface (Viewport 1920×1080, Layout: Admin panel)` satırı semantik olarak da uyuşur; T42 `Special/Variant` kategorisindedir ve `screens/T42-*/` dizini **diskte yoktur**. T42'ye taşınma istenirse 3 spec + bu indeks + [[../01-mockup-index]] §4.3/§4.4 birlikte güncellenmelidir (owner onayı gerekir).
6. **Spec'siz PNG'ler (2026-10-16):** `.ai/.png/home-1920/` içindeki `Geçmiş` / `Göz At` / `Playlist` 3 PNG'nin ekran spec'i yok (`⚠️ VERIFICATION REQUIRED` — ya spec yazılır, ya bu üçü envanter dışı bırakılır).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
**Version:** 6.3.0 — Figma `2161:12438` (Music Admin) 3 yeni T17 spec'i (`admin-layout` · `admin-login` · `admin-register`); sayılar 20→23 spec, 19→25 PNG, 22→25 figma kare; §7 madde 5 (T17 vs T42 tier) + madde 6 (spec'siz 3 PNG) açıldı — v6.2.0: T07-embedded taşıması (owner onayı); v6.0.0: 20 spec dosyasının tamamı yeniden yazıldı, bu indeks sıfırdan üretildi
**Mode:** Red Team · Human Mode · Truth Mode
