# CSS Page Şablonu (05_Pages)

> **Bu şablon AI tarafından CSS yazarken ZORUNLU okunur.**
> SSOT: `Css/05_Pages/` — `p-*.css` ×7 + partial ×5 (`_home.css`, `_home-layout.css`, `_home-inline.css`, `_player.css`, `_welcome.css`) = 12 dosya (2026-10-04)

## 1. Page vs Component ayrım kuralı

| Soru | Evet → |
|---|---|
| Tek bir PHP sayfasında kullanılıyor (`home.coremusic.net/<page>.php`) | **05_Pages/p-\<page\>.css** |
| Figma'da component olarak işaretli VE ≥2 sayfada tekrar | **04_Components/c-\<ad\>.css** |
| Sayfanın yalnız bir bölümünü parçalıyor (import ile giriyor) | **05_Pages/_\<parça\>.css** |
| Bir cihazda farklı davranıyorsa | davranış → **08_Devices**, token → **01_Abstracts** |

Gerçek örnekler: sayfa → `p-albums.css`, `p-artists.css`, `p-playlist.css`,
`p-settings.css`, `p-album-detail.css`; auth sayfası → `p-login-view.css`,
`p-select-gender.css` (bunlar 05'te kalır, 11_OAuth DEĞİL); partial → `_home-layout.css`.

## 2. Şablon

```css
/**
 * CoreMusic — <Sayfa> Page CSS
 * ITCSS Layer: 05_Pages · Dosya: p-<sayfa>.css
 * SSS: tokens → 01_Abstracts; component → 04_Components; cihaz → 08_Devices
 */

/* --- Page root --- */
.<page> {
  display: grid;
  gap: var(--section-gap, 16px);
  padding: var(--page-padding-x, 16px);
}

/* --- Bölümler (BEM block = sayfa, element = bölüm) --- */
.<page>__<section> { /* token ile */ }

/* --- Breakpoint bantları (css-token.md §3 — uydurma yok) --- */
@media (max-width: 767px) { /* phone */ }
@media (min-width: 768px) and (max-width: 1024px) { /* tablet/RPi5 */ }
@media (min-width: 1025px) and (max-width: 1440px) { /* desktop */ }
@media (min-width: 1441px) and (max-width: 1919px) { /* wide */ }
@media (min-width: 2560px) and (max-width: 3839px) { /* 2K+ */ }
@media (min-width: 3840px) { /* 4K */ }
```

## 3. Kurallar

1. Sayfa dosyası **token tanımı yazmaz** (yalnız `var()` okur, `css-token.md`).
2. Component seçicisini **tekrar tanımla** → varsa 04'e ekle, 05'te yalnız layout bağlamı.
3. `@media` içinde `var()` ile okuma **yok**; sadece sabit override.
4. Yeni sayfa CSS'i PHP'e elle `<link>` ile eklenmez → `DeviceRenderer::headLinks()` /
   router zinciri tarafından yüklenir (`css-imports.md`).
5. Tailwind/utility class üretme · framework sınıfı kullanma.
