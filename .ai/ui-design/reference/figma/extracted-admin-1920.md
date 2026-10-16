---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Figma Çıkarımı: Music Admin Sayfası (2161:12438)"
type: reference
category: ui-design
date: 2026-10-16
updated: 2026-10-16
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/figma/extracted-admin-1920.md"
  source_of_truth: ".ai/ui-design/reference/figma/raw/page-2161-12438.json · .ai/.png/admin-1920/"
---

# CoreMusic — Figma Çıkarımı: Music Admin Sayfası (2161:12438)

**Zorunlu Bağlantılar:** [[../../01-mockup-index]] · [[../../00-device-matrix]] · [[../../tokens/design-tokens-master]] · [[../../screens/00-ascii-art-index]] · [[_extraction-notes]] · [[extracted-1920]]

---

## 1. Amaç

Figma sayfası **`2161:12438` "Core Music - Music Admin"** ağacının (3 görünür kare + 1 gizli varyant grubu, 224 node) **tek otoriteli ham-çıkarım** dokümanıdır; CoreMusic **admin panel** kodlaması (login · register · ana layout) bu dosyadaki koordinat/renk/efekt/metin verilerinden beslenir. Çıkarım `.ai/.png/admin-1920/` PNG'leriyle çelişirse **PNG kazanır** (AGENTS.md §7.2 referans sırası).

> **⚠️ Mockup Before Frontend:** admin panel CSS/HTML/JS görevlerinde `.ai/.png/admin-1920/` altındaki ilgili PNG okunmadan kod yazılamaz; görsel okunamıyorsa DUR ve bildir.

---

## 2. Kaynak ve Çekim

| Alan | Değer |
|------|-------|
| İstek | `GET /v1/files/{FIGMA_FILE_KEY}/nodes?ids=2161:12438` (+ `X-Figma-Token`) |
| Anahtar SSOT | `.ai/.env.figma` (`FIGMA_TOKEN` / `FIGMA_FILE_KEY`) — değer bu dosyaya **yazılmaz** (AGENTS.md §13.9 #3) |
| Çekim tarihi | 2026-10-16 |
| Ham JSON | `reference/figma/raw/page-2161-12438.json` — 234.298 byte, UTF-8 BOM'suz |
| `lastModified` | `2026-10-16T13:13:42Z` |
| `version` | `2410949079540529039` |
| Node sayısı | **224** (kök dâhil) |
| Önceki çekim | `2401752188763178639` · `2026-09-21T17:51:56Z` · 223 node → **bayattı, güncellendi** |
| Kök node | `2161:12438` — CANVAS "Core Music - Music Admin" |
| PNG kaynağı | `.ai/.png/admin-1920/` → `Admin - layout.png` · `Admin - Login.png` · `Admin - Register.png` (3/3 okundu) |

---

## 3. Sayfa Yapısı (üst düğümler)

| # | Node | Ad | Tip | Boyut | Konum (mutlak) | İlgili PNG | Not |
|---|------|----|-----|-------|----------------|-----------|-----|
| 1 | `2161:12439` | mavi | GROUP | 3880×1080 | (4273, -1629) | — | **`visible: false`** — mavi tema varyantları (`Admin - Login Mavi` `2161:12440`, `Admin - Register Mavi` `2161:12465`) |
| 2 | `2161:12499` | Admin - layout | FRAME | 1920×1080 | (0, 0) | `Admin - layout.png` | 6 çocuk — sidebar + navbar + footer |
| 3 | `2161:12506` | Admin - Login | FRAME | 1920×1080 | (0, -1629) | `Admin - Login.png` | 7 çocuk — sağ auth paneli |
| 4 | `2161:12529` | Admin - Register | FRAME | 1920×1080 | (1960, -1629) | `Admin - Register.png` | 8 çocuk — sağ auth paneli (5 alan) |

> Gizli `mavi` grubu ve `visible: false` node'lardan Figma Images API PNG **üretemez** (bilinen 15 gizli node kuralı, `_extraction-notes`). Varyantlar bu dosyada **boyut/çıkarım** olarak taşınır, görsel referans sayılmaz.

**Koordinat birimi:** alt bölümlerdeki `x,y` değerleri **kare sol-üst köşesine göre görelidir** (mutlak değil).

---

## 4. `2161:12499` — Admin - layout (1920×1080)

| Node | Ad | Tip | x | y | w | h | Dolum / Efekt / Metin |
|------|----|-----|---|---|---|---|-----------------------|
| `2161:12500` | Romantic_Background_03 (2) | RECTANGLE | 0 | 0 | 1920 | 1080 | `IMAGE` scaleMode=FILL (köprü fotoğrafı) |
| `2161:12501` | Siyah Arkaplan Evekt | RECTANGLE | 0 | 0 | 1923 | 1080 | `#000000` alpha .15 — **[HIDDEN]** |
| `2161:12502` | Footer | INSTANCE | 1053 | 1040 | 367 | 29 | alt metin bloğu (aşağıda) |
| `2161:12503` | ContainerA | INSTANCE | 0 | 0 | 1917 | 1080 | içerik kabuğu (aşağıda) |
| `2161:12504` | Menu DivA | INSTANCE | 0 | 0 | 250 | 1080 | sol sidebar (aşağıda) |
| `2161:12505` | Navbar Admin | INSTANCE | 1623 | 18 | 296 | 35 | sağ-üst profil barı (aşağıda) |

**Footer `2161:12502` alt öğeleri (göreli):**

| Node | Ad | x | y | w | h | Metin / Font |
|------|----|---|---|---|---|-----------|
| `I…;463:416` | username | 1081 | 1060 | 113 | 12 | `Username :  Bayram Ali` · Avalon 9/500 · `#FFFFFF` |
| `I…;463:418` | ip adress | 1213 | 1060 | 113 | 12 | `IP : 95.95.0.201` · Avalon 9/500 · `#FFFFFF` |
| `I…;463:420` | user type | 1311 | 1060 | 126 | 12 | `User Type : Administartor` (Figma'daki yazım) · Avalon 9/500 |
| `I…;463:411` | copirght | 1456 | 1053 | 467 | 27 | `Copyright © 2024-2025 Core Music for Ali Admin . All rights reserved.  Core Music` · Respective 18/400 |

**ContainerA `2161:12503` alt öğeleri:**

| Node | Ad | x | y | w | h | Not |
|------|----|---|---|---|---|-----|
| `I…;463:2343` | Rectangle 8 | 268 | 78 | 1631 | 940 | r=10 · `#FFFFFF` alpha .21 — **[HIDDEN]** → planlanmış içerik paneli |
| `I…;463:2344` | bk elleme layout | 0 | 0 | 1917 | 1080 | r=10 · `#FFFFFF` alpha .21 — **[HIDDEN]** |
| `I…;463:2345` | Admin Panelim | 297 | 102 | 117 | 16 | TEXT · Arial 14/400 · `#FFFFFF` |
| `I…;463:2346` | Home / Dashboard | 1749 | 102 | 129 | 18 | TEXT · Avalon 13/500 — **[HIDDEN]** |

**Menu DivA `2161:12504` (sidebar, 250×1080):**

| Node | Ad | x | y | w | h | Not |
|------|----|---|---|---|---|-----|
| `I…;462:6913` | Rectangle 7 | 0 | 0 | 250 | 1080 | `#000000` alpha .20 (cam katman) |
| `I…;463:365` | Menu | 9 | 12 | 22 | 12 | TEXT `Menu` · Avalon 9 · `#EEEEEE` |
| `I…;926:11356` | Admin Menu Btn | 19 | 42 | 68 | 13 | `Music Sitesi Ayarları` · ikon 13×13 @x=19 · yazı 8px `#EEEEEE` |
| `I…;926:11367` | Admin Menu Btn | 19 | 56 | 68 | 13 | `Admin Paneli Ayarları` · 8px |
| `I…;463:407` | Şarkılar | 9 | 109 | 31 | 12 | TEXT grup başlığı · Avalon 9 · `#EEEEEE` |
| `I…;926:11376` | Admin Menu Btn | 20 | 122 | 68 | 13 | `Şarkı listesi` · 8px |
| `I…;926:11394` | Admin Menu Btn | 20 | 136 | 68 | 13 | `Şarkı Ekle` · 8px |
| `I…;926:11403` | Admin Menu Btn | 20 | 150 | 68 | 13 | `Playlist Oluştur` · 8px |
| `I…;917:10940` (her btn) | Rectangle 54 | 19 | +1 | 232 | 11 | `#D9D9D9` alpha .15 — **[HIDDEN]** (hover zemini) |

**Navbar Admin `2161:12505` (296×35 @ 1623,18):**

| Node | Ad | x | y | w | h | Not |
|------|----|---|---|---|---|-----|
| `I…;462:6906` | Rectangle 6 | 1623 | 18 | 296 | 35 | `#000000` alpha .20 |
| `I…;462:6894` | CortexBot (avatar) | 1630 | 25 | 20 | 20 | ELLIPSE `#C4C4C4` |
| `I…;917:10787` | Batterylevel | 1653 | 30 | 70 | 9 | `Bayram Ali - admin` · Avalon 7 · `#FFFFFF` |
| `I…;462:6898` | Wifi | 1731 | 29 | 11 | 12 | IMAGE |
| `I…;462:6899` | Bt | 1745 | 29 | 11 | 12 | IMAGE |
| `I…;897:15861` | battery-icon | 1765 | 27 | 17 | 16 | IMAGE |
| `I…;897:15863` | Batterylevel | 1784 | 32 | 15 | 7 | `100%` · Avalon 5 |
| `I…;462:6907` | icons8-logout-48 | 1898 | 26 | 15 | 16 | r=13 · IMAGE (çıkış) |

---

## 5. `2161:12506` — Admin - Login (1920×1080)

| Node | Ad | Tip | x | y | w | h | Dolum / Efekt / Metin |
|------|----|-----|---|---|---|---|-----------------------|
| `2161:12507` | Romantic_Background_03 (2) | RECT | 0 | 0 | 1920 | 1080 | `IMAGE` FILL |
| `2161:12508` | Rectangle 1 | RECT | 1420 | 0 | 500 | 1080 | r=10 · `#000000` alpha .25 · DROP_SHADOW r=1 dy=1 (**sağ panel**) |
| `2161:12520` | Core Music for Ali Admin | TEXT | 1514 | 107 | 313 | 32 | `#FFFFFF` · style: Avalon 7/500 ⚠️ **ölçekli text** (bbox 313×32 → render ≈ 25-30px; PNG'ye göre uygulanır) |
| `2161:12521` | Sisteme Giriş Yapın | TEXT | 1587 | 169 | 166 | 25 | `#FFFFFF` · font `00543` 23/400 |
| `2161:12517` | Info Notes | GROUP | 1449 | 261 | 445 | 136 | ikon + uyarı metni |
| `2161:12519` | icons8-info-mavi 1 | RECT | 1645 | 261 | 50 | 50 | `IMAGE` (mavi info ikonu) |
| `2161:12518` | Text | TEXT | 1449 | 331 | 445 | 66 | 3 satır uyarı · Avalon 16/500 · `#FFFFFF` · DROP_SHADOW r=4 |
| `2161:12509` | Login Panel | GROUP | 1475 | 507 | 390 | 254 | form bloğu (aşağıda) |
| `2161:12522` | qr image | GROUP | 1479 | 941 | 407 | 100 | QR + mobil giriş notu (aşağıda) |

**Login Panel `2161:12509` içeriği (yukarıdan aşağı):**

| Node | Ad | x | y | w | h | Dolum / Metin |
|------|----|---|---|---|---|-----------|
| `2161:12516` | Kullanıcı adı yada Email | TEXT | 1475 | 507 | 171 | 12 | `#FFFFFF` · `00543` 20/400 |
| `2161:12515` | İnput (kullanıcı adı) | INSTANCE | 1475 | 545 | 390 | 30 | bg `#F200D0` alpha .50 + `#FFFFFF` alpha .05 · r=3 · ikon 15×15 @1481,552 · değer `Bayram Ali` Avalon 12 |
| `2161:12514` | Parola | TEXT | 1475 | 595 | 106 | 12 | `#FFFFFF` · `00543` 20/400 |
| `2161:12513` | İnput (parola) | INSTANCE | 1475 | 633 | 390 | 30 | bg `#F200D0` alpha .50 + `#FFFFFF` alpha .05 · r=20 (bg r=4) · değer `**********` Avalon 18 |
| `2161:12512` | Checkbox "Beni Hatırla" | INSTANCE | 1475 | 683 | 164 | 20 | kutu 15×15 r=1.5 `#FFFFFF` alpha .30 · yazı Avalon 15 `#FFFFFF` |
| `2161:12511` | Şifremi Unuttum | TEXT | 1757 | 685 | 108 | 14 | `#FF3CE3` (+ `#FFFFFF` alpha .36 katman) · Avalon 15 |
| `2161:12510` | Button "Sisteme Giriş Yap" | INSTANCE | 1475 | 723 | 390 | 38 | bg `#FF38E3` + `#FFFFFF` alpha .05 · r=3 · DROP_SHADOW r=1 dy=1 · yazı Avalon 15 `#FFFFFF` |

**qr image `2161:12522` (407×100):**

| Node | Ad | x | y | w | h | Not |
|------|----|---|---|---|---|-----|
| `2161:12523` | Rectangle 4 | 1479 | 946 | 386 | 80 | r=10 · `#FFFFFF` alpha .10 |
| `2161:12524` | phone 1 | 1489 | 956 | 28 | 60 | `IMAGE` telefon |
| `2161:12526` | qr image | 1493 | 976 | 21 | 21 | `IMAGE` QR (alt `Rectangle 3` r=3 `#D9D9D9`) |
| `2161:12527` | Text | 1534 | 968 | 311 | 36 | mobil/QR giriş metni · Avalon 16/500 `#FFFFFF` |
| `2161:12528` | Frame 1 | 1786 | 941 | 100 | 100 | QR alanı (boş frame) |

---

## 6. `2161:12529` — Admin - Register (1920×1080)

| Node | Ad | Tip | x | y | w | h | Dolum / Metin |
|------|----|-----|---|---|---|---|---------------|
| `2161:12530` | Romantic_Background_03 (2) | RECT | 0 | 0 | 1920 | 1080 | `IMAGE` FILL |
| `2161:12531` | Rectangle 1 | RECT | 1420 | 0 | 500 | 1080 | r=10 · `#000000` alpha .25 (sağ panel) |
| `2161:12532` | Core Music for Ali Admin | TEXT | 1514 | 107 | 313 | 32 | ⚠️ ölçekli text (bkz. §5) |
| `2161:12533` | Sisteme Giriş Yapın | TEXT | 1587 | 169 | 166 | 25 | `00543` 23/400 · `#FFFFFF` |
| `2161:12547` | Info Notes | GROUP | 1475 | 239 | 390 | 165 | ikon + uzun uyarı metni |
| `2161:12549` | icons8-info-mavi 1 | RECT | 1645 | 239 | 50 | 50 | `IMAGE` |
| `2161:12548` | Text | TEXT | 1475 | 299 | 390 | 105 | 5 satır · Avalon 15/500 · `#FFFFFF` |
| `2161:12534` | Login Panel | GROUP | 1472 | 439 | 393 | 405 | 5 alan + checkbox |
| `2161:12557` | Button | INSTANCE | 1475 | 864 | 390 | 38 | bg `#FF38E3` · r=3 · yazı `Sisteme Giriş Yap` 15 |
| `2161:12550` | qr image | GROUP | 1479 | 941 | 407 | 100 | §5 ile aynı yapı (metin 15px) |

**Login Panel `2161:12534` — 5 alan (göreli y):**

| # | Etiket (TEXT) | y | Input (INSTANCE) | y | h | Değer metni (örnek) |
|---|---------------|---|------------------|---|---|---------------------|
| 1 | `Adı Söyadı` `2161:12546` | 439 | `2161:12545` | 475 | 30 | `Bayram Ali` (Avalon 12) · bg r=3 |
| 2 | `Kullanıcı Adı` `2161:12544` | 516 | `2161:12543` | 550 | 30 | `aliadmin` |
| 3 | `Email Adresi` `2161:12542` | 591 | `2161:12541` | 625 | 30 | `bayramali@aliadmin.com` |
| 4 | `Parola` `2161:12540` | 666 | `2161:12539` | 700 | 30 | `**********` (Avalon 18) |
| 5 | `Doğum Tarihi` `2161:12538` | 741 | `2161:12537` | 777 | 30 | `Tarih : Gun : 15  Ay : Eylul  Yıl: 2005` · r=20 (bg r=4) |
| — | Checkbox `2161:12535` `Okudum Kabul Ediyorum` | 824 | kutu 15×15 @1476,827 | — | 20 | `#FFFFFF` alpha .30 |
| — | `Şifremi Unuttum` `2161:12536` | 826 | — | — | 14 | x=1727 w=138 · `#FF3CE3` |

> Tüm input zeminleri: `#F200D0` alpha .50 + `#FFFFFF` alpha .05 · ikon 15×15 @ x=1481 · 390×30 · `DROP_SHADOW r=1 dy=1`.

---

## 7. Renk / Efekt / Tipografi

### 7.1 Renk paleti (Figma değeri — ham hex burada meşrudur; CSS'te token'a bağlanır)

| Hex | Opaklık | Kullanım | Örnek node |
|-----|---------|----------|-----------|
| `#FF38E3` | 1 | CTA buton zemini | `I…;873:12336` |
| `#F200D0` | .50 | input zemini (2. katman `#FFFFFF` .05) | `I…;2015:17366` |
| `#FF3CE3` | 1 | "Şifremi Unuttum" link metni | `2161:12511` |
| `#000000` | .15 / .20 / .25 | koyu cam katmanlar (overlay, sidebar, panel) | `2161:12508`, `I…;462:6913` |
| `#FFFFFF` | 1 / .30 / .21 / .10 / .05 | metin, checkbox, içerik paneli, qr kutusu, input üst katman | `2161:12512`, `I…;463:2343` |
| `#EEEEEE` | 1 | sidebar metni | `I…;463:365` |
| `#D9D9D9` | .15 / 1 | gizli hover zemini, QR placeholder | `I…;917:10940`, `2161:12525` |
| `#C4C4C4` | 1 | avatar placeholder | `I…;462:6894` |

> **Çelişki (Token-First):** Figma vurgu `#FF38E3` / `#F200D0` master `--cm-primary` (`#ff4fd8`) ile **birebir aynı değil** → CSS'te token kazanır; çelişki `reference/figma/token-conflicts.md`'ye kayıt adayı (**açık madde**).

### 7.2 Efekt (API `effects`)

| Efekt | Değer | Node | Not |
|-------|-------|------|-----|
| DROP_SHADOW | blur=1, offset(0,1) | panel, input, buton, checkbox, link | en yaygın gölge |
| DROP_SHADOW | blur=4, offset(0,0) | Login info metni `2161:12518` | tek büyük gölge |
| DROP_SHADOW | blur=1, offset(0,1) | Login sağ panel `2161:12508` | r=10 köşe |
| **BACKGROUND_BLUR** | — | — | **API'de YOK** → bkz. §8 madde 1 |

### 7.3 Tipografi

| Font | Ağırlık | Boyutlar | Kullanım |
|------|---------|----------|----------|
| Avalon | 500 (Medium) | 5 · 7 · 8 · 9 · 12 · 15 · 16 | sidebar, navbar, footer, form değerleri, uyarılar |
| `00543` | 400 | 20 · 23 | form etiket başlıkları + "Sisteme Giriş Yapın" |
| Arial | 400 | 14 | "Admin Panelim" |
| Respective | 400 | 18 | copyright satırı |

> ⚠️ `2161:12520` başlığı style alanına göre 7px görünüyor ama bbox'ı 313×32 — Figma'da **ölçeklenmiş text**; render boyutu PNG'den alınır, style değeri kodlanmaz.

---

## 8. Çelişkiler ve Doğrulama Notları

1. **Arka plan bulanıklığı (PNG > Figma):** `.png/admin-1920` PNG'lerinde sağ auth paneli arkasındaki fotoğraf yumuşar; Figma API `effects` alanında `BACKGROUND_BLUR` **yok** (yalnız DROP_SHADOW). Referans sırası gereği PNG kazanır → CSS'te `backdrop-filter` token'a bağlanır (`--cm-glass-blur`), piksel değeri uydurulmaz.
2. **Ölçekli metin:** §5/§6 başlık `2161:12520`/`2161:12532` `style.fontSize=7` iken bbox 313×32 → `⚠️ VERIFICATION REQUIRED` (kodda PNG ölçeği esas alınır).
3. **Gizli node'lar:** `mavi` grubu (2 varyant kare) + `[HIDDEN]` rectangle'lar → Images API NULL; içerik bu dosyada **çıkarım olarak** taşınır.
4. **PNG envanteri:** diskte `.ai/.png/` altında **25 PNG** vardır (`home-1024` 12 · `home-1920` 4 · `shared-1024` 6 · `admin-1920` 3) → `01-mockup-index.md` "19 PNG" iddiası bu revizyonda **25 PNG** olarak senkronize edildi (v6.3.0, 2026-10-16).
5. **Ekran spec'leri:** 3 admin ekranı ASCII layout olarak `screens/T17-monitor-22fhd/` içinde (`admin-layout`, `admin-login`, `admin-register`) — indeks: `screens/00-ascii-art-index.md`.
6. **Yazım hataları korunur:** `Administartor`, `Sisteme Giriş Yapın`, `Adı Söyadı` gibi Figma metinleri **olduğu gibi** aktarıldı; düzeltilmez (kopya doğruluk).

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.1 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 6 |
| Last Updated | 2026-10-16 |
| Node Count | 224 (223 → 224, `version 2410949079540529039`) |
| Frame Count | 3 görünür + 1 gizli varyant grubu (2 alt kare) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
**Mode:** Red Team · Human Mode · Truth Mode
