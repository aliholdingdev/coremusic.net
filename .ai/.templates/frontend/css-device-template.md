---
title: "CoreMusic — CSS Cihaz Şablonu (08_Devices)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Cihaz Şablonu — `08_Devices/`

**Kapsam:** Cihaz için **import zinciri** + **davranış override** · önek `d-` · yerleşim **YAZILMAZ**
**Ana şablon:** [[css-template]] §3.5 · **Auth varyantı:** [[css-auth-device-template]] · **Token:** [[css-device-token-template]]
**Eşzamanlı senkron:** `js/devices.config.js` + `shared/src/Device/DeviceCssMap.php` (ikisi birden)
**v2.0.0 sıfırdan yeniden yazım (2026-10-06) — C1/C2/C4/C5 + katman kuralı (C6) uygulandı.**

---

## Purpose (Amaç)

Tek bir cihaz genişliği için stylesheet zincirini kurmak ve o cihaza özgü **davranışı** (hover yok,
tap highlight, font ölçeği, taşma) override etmek. Yerleşim (grid/boyut kuralı) bu katmanın işi DEĞİL.

## Location (Konum)

`assets.coremusic.net/Css/08_Devices/d-{{device}}.css`
Örnek dosyalar (2026-10-06 ölçümü — envanter iddiası değil; sayı/envanter yalnız `Css/CONTEXT.md §3.1`'de — C4):
`d-phone.css` · `d-tablet.css` · `d-laptop.css` · `d-desktop.css` · `d-embedded.css` · `d-4k.css` ·
`d-4k-monitor.css` · `d-4k-tv.css`

**YOK — uydurulmaz:** `main.css` (2026-09-30 silindi) · `a-layout-tokens.css` (BASE `a-layout-tokens-1024.css` — C1) ·
`d-auth-4k.css` (yalnız `d-auth-4k-monitor` / `d-auth-4k-tv`) · `d-4k-monitor`/`d-4k-tv` bootstrap import'u **yok** (disk: 0 satır).

## Responsibility (Sorumluluk)

- (1) **Import zinciri** — `01 → 02 → 03 → 04 → 05 → 06 → 07 → 09 → 10` sırasıyla `?v={{v}}`.
- (2) **Davranış override** — `@media` içinde cihaz davranışı (scroll, tap, hover, `html{font-size}`).
- Bootstrap reboot/grid'in `02_Base`'den **ÖNCE** gelmesi (reboot base'i ezerse diye bilinçli sıralama —
  disk kanıtı 2026-10-06: `d-phone.css` L11-12 reboot+grid, L13+ base; `d-desktop.css`/`d-laptop.css` L23-24
  reboot+grid, `v-bootstrap-lib.css` L52).

## Allowed (İzinli)

- `@import url("../{katman}/{dosya}.css?v={{v}}")` — tüm katmanlar.
- `@media` içinde **davranış** (element bazlı gizleme/scroll/tap), `html { font-size }` ölçek override'ı.
- Mevcut token'ın `:root` **değer** override'ı (`--touch-min` gibi — yeni ad DEĞİL, C6).
- `07_Vendors` import'u (yalnız bu şablonda; auth paketinde yok).

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Yerleşim kuralı (grid kolonu, `display`, sayfa düzeni) | `Css/CLAUDE.md` §4.1 #5 → `02_Base`/`03_Layout` |
| 2 | **Yeni token adı** (`--yeni: …`) — yalnız mevcut adın değeri | §4.1 #9 (C6) · `.ai/CLAUDE.md:537` |
| 3 | Ham hex/px (token değer override'ı hariç — orası 01'in değeri) | §4.1 #1 |
| 4 | `!important` (cap 3 — bu katmanda varsayılan 0) | §4.1 #2 · C5 |
| 5 | Dosya sayısı/envanter iddiası | C4 |
| 6 | `main.css` / `a-layout-tokens.css` / `d-auth-4k.css` uydurma | § YOK listesi |
| 7 | Dosya eklemede tek taraflı senkron | `Css/CLAUDE.md` §4.2 — `devices.config.js` + `DeviceCssMap.php` |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Tür | Not |
|------------|-----|-----|
| `01_Abstracts/a-layout-tokens-{{width}}.css` | import | ilk satır; BASE `a-layout-tokens-1024.css` her cihazda fallback (C1) |
| `js/devices.config.js` + `DeviceCssMap.php` | senkron | yeni/renamed cihaz → ikisi birden |
| `DeviceRenderer::headLinks()` | ⚠️ | `v-*.css` ayrıca ayrı `<link>` basılır (`DeviceRenderer.php:120-128`, disk kanıtı) → **çift yükleme, ADR bekliyor — ⚠️ VERIFICATION REQUIRED** |
| `.ai/ui-design/reference/05-responsive-architecture` | okunur | §7.4 4K No-Center · §12 fallback zorunlu |
| `notes.md` | okunur | not uygulanır + `✓` imzalanır |

## Import Rules (Import Kuralları)

1. Sıra **sabit**: Abstracts → Base → Layout → Components → Pages → Utilities → Vendors(önce) → ViewModes → Helpers.
   (bootstrap reboot/grid `02_Base`'den önce — §Responsibility disk kanıtı.)
2. Her import `?v={{v}}`; `{{v}}` zincir genelinde **tek değer**.
3. `07_Vendors` yalnız burada import edilir; auth paketinde yok ([[css-auth-device-template]]).
4. `main.css` YOK — giriş bu dosyalardır.

```css
/* 08_Devices/d-{{device}}.css — 1) IMPORT  2) DAVRANIŞ */
@import url("../01_Abstracts/a-layout-tokens-{{width}}.css?v={{v}}");
@import url("../01_Abstracts/a-breakpoint-tokens.css?v={{v}}");
@import url("../07_Vendors/bootstrap-reboot.min.css?v={{v}}");   /* 02_Base'den ÖNCE — bilinçli */
@import url("../07_Vendors/bootstrap-grid.min.css?v={{v}}");
@import url("../02_Base/b-base-core.css?v={{v}}");
@import url("../03_Layout/_header.css?v={{v}}");
@import url("../04_Components/c-{{block}}.css?v={{v}}");
@import url("../05_Pages/p-{{page}}.css?v={{v}}");
@import url("../06_Utilities/u-helpers-utility.css?v={{v}}");
@import url("../09_ViewModes/v-{{mode}}.css?v={{v}}");
@import url("../10_Helpers/h-ellipsis.css?v={{v}}");   /* C5 — 10_Helpers satırı zorunlu */

/* ---- 2) DAVRANIŞ (yerleşim DEĞİL) ---- */
@media (max-width: 767px) {
  html { font-size: 14px; }
  body { overflow-x: hidden; -webkit-overflow-scrolling: touch; }
  * { -webkit-tap-highlight-color: transparent; }
}
```

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `d-{{device}}.css` (auth varyantı `d-auth-{{device}}.css` → ayrı şablon) |
| Cihaz adı | disk envanterindeki ad (örn. `4k-monitor`); `devices.config.js` anahtarıyla eşleşir |
| Token override adı | BASE'te tanımlı **aynı** ad (farklı ad = yeni token = C6 ihlali) |

## Device Rules (Cihaz Kuralları)

1. Görev = **import + davranış**; ikisi dışında bir şey yazılmaz (§4.1 #5).
2. Cihaz token'ı `01_Abstracts/a-layout-tokens-{width}.css`'te ([[css-device-token-template]]);
   burada yalnız `:root` **değer** override'ı kabul edilir (C6).
3. 4K'da ortalamama (`margin-inline: auto`) + geriye dönük fallback **zorunlu** → `08_Devices` davranış alanı
   (`05-responsive-architecture` §7.4/§12).
4. Hover-required cihazlarda (phone/tablet/embedded) hover davranışı kapatılır — davranış, yerleşim değil.
5. Yeni cihaz → `devices.config.js` **+** `DeviceCssMap.php` + import zinciri üçü birden.

## Responsive Rules (Responsive)

- Medya genişlikleri `a-breakpoint-tokens.css` değeriyle **eşleşmeli**; `@media` içinde `var()` okunamaz (CSS kısıtı) → tahmin yasak.
- Cihaz ayarlaması mümkünse **token override** ile (`--header-h` değişir → düzen kendiliğinden uyar);
  doğrudan yerleşim yazmak yerine token tercih edilir.

## Token Rules (Token Kuralları)

- Yeni `--token` yalnız `01_Abstracts/` (C6 katman kuralı).
- Burada yalnız mevcut token'ın `:root` değeri override edilir; `--touch-min` taban 48px (C2),
  cihaz override'ları `Css/AGENTS.md` §3.1 (≥48px) ve brain.md:806-811 (Phone/Embedded ≥48 · Wide/4K ≥24
  — WCAG 2.2 AA 2.5.8 tabanı 24px) ile uyumlu; **44px yazılmaz**.
- Figma'da olmayan değer uydurulmaz → `⚠️ VERIFICATION REQUIRED`.

## Validation (Doğrulama)

- [ ] Dosya adı disk envanterinde var (uydurma cihaz yok — C4: sayı CONTEXT.md §3.1'de)
- [ ] İskelet: import zinciri sırası + `10_Helpers` satırı (C5) + davranış bloğu
- [ ] Yerleşim kuralı yok (§4.1 #5) · yeni token adı yok (C6) · `!important` 0
- [ ] `devices.config.js` + `DeviceCssMap.php` güncellendi
- [ ] Token override'ları `a-layout-tokens-{width}.css` ile çelişmiyor
- [ ] Gerçek cihaz genişliğinde tarayıcı testi yapıldı (≥2 genişlik)

## Example Structure (Örnek Yapı)

```css
/**
 * 08_Devices/d-{{device}}.css
 * CİHAZ : {{device}} · ARALIK: {{min}}–{{max}}px (a-breakpoint-tokens.css)
 * GÖREV : (1) import zinciri  (2) davranış override
 * YASAK : yerleşim (grid/boyut) → 02_Base / 03_Layout · yeni token adı → 01_Abstracts (C6)
 * SENKRO: js/devices.config.js + DeviceCssMap.php (ikisi birden)
 */

/* 1) IMPORT — Kalıp: §Import Rules (bootstrap reboot/grid 02_Base'den önce; 10_Helpers dahil) */

/* 2) DAVRANIŞ */
@media (max-width: 767px) {
  html { font-size: 14px; }              /* ölçek davranışı — token değil, cihaz davranışı */

  body {
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
  }

  * { -webkit-tap-highlight-color: transparent; }

  /* mevcut token'ın değeri override (C6 — yeni ad YAZILMAZ) */
  :root { --touch-min: 48px; }

  /* isteğe bağlı gizleme — yerleşim değil davranış */
  .header-widget { display: none; }
}
```

---

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R — dokunulmayan ikincil düzeltmeler):**

| Hedef | Sorun |
|-------|-------|
| `shared/src/Device/DeviceRenderer.php:120-128` | `v-*.css` hem import zincirinde hem `headLinks()` ayrı `<link>`'i → çift yükleme; ADR bekliyor ⚠️ VERIFICATION REQUIRED |
| `assets.coremusic.net/Css/CLAUDE.md` §3.2 | "`a-layout-tokens.css` base" → diskte YOK (C1; BASE `-1024`) |
| `assets.coremusic.net/Css/CLAUDE.md` §4.1 #2/#10 | kod geneli `!important` 47+ satır ihlal (vendor hariç; auth device minified satır içi çoklu → kesin sayı UNKNOWN) |
