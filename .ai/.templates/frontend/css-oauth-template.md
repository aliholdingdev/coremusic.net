---
title: "CoreMusic — CSS OAuth Şablonu (11_OAuth)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 1.0.0
status: active
authority: reference
---

# CSS OAuth Şablonu — `11_OAuth/`

**Kapsam:** OAuth/giriş (social login) akış stilleri · mevcut dosya `oauth.css` (öneksiz — korunur)
**Ana şablon:** [[css-template]] §3.1 · **Denetim:** Security Engineer (`Css/AGENTS.md` §3.2)
**YENİ DOSYA (2026-10-06) — önceki oturumda raporlanıp diske düşmemişti.**

---

## Purpose (Amaç)

OAuth platform kartları/giriş akışı UI'ını izole katmanda tutmak (dosya başlığı: ADR-088 gender-based
social OAuth UI · ADR-001 · BEM). 11. katman = ITCSS'in en dar özelleşmiş katmanı.

## Location (Konum)

`assets.coremusic.net/Css/11_OAuth/oauth.css`
Örnek dosya (2026-10-06 ölçümü — envanter iddiası değil; sayı/envanter yalnız `Css/CONTEXT.md §3.1`'de — C4).

**YOK — uydurulmaz:** `o-*.css` (diskte yok — yeni dosya eklenirse kalıp `o-{{ad}}.css`, §Naming) ·
`main.css`.

## Responsibility (Sorumluluk)

- OAuth platform kartları (`.oauth-platforms`, `.oauth-platform-card` — disk kanıtı L18-30) ve giriş
  akışı bileşen yüzeyi.
- Token tüketimi: dosya başlığı `var(--color-*)`, `var(--theme-*)`, `var(--platform-*)` bildirir
  (adların `01_Abstracts`'te karşılığı **[VERIFY REQUIRED]** — token dosyası okunmadan doğrulanmaz).
- BEM: `.oauth-platform-card` (block) + `__*`/`--*` uzantıları.

## Allowed (İzinli)

- BEM seçiciler (`.oauth-{{block}}`, `__element`, `--modifier`, `.is-*`).
- `var(--token)` tüketimi (adlar `01_Abstracts`'te tanımlı olmalı).
- `@media` yalnız token uyarlaması.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Token tanımı (`--x: …`) | `Css/CLAUDE.md` §4.1 #9 (C6) |
| 2 | Ham hex/px/rem (bu katman `01` DEĞİL) | §4.1 #1 |
| 3 | `!important` (cap 3 — bu iskelette 0) | §4.1 #2 · C5 |
| 4 | ≥2 sayfada tekrar eden parça → `04_Components` | §3.1 ayrım · §4.1 #8 |
| 5 | Header/footer/grid düzeni → `03_Layout` | §3.1 |
| 6 | Dosya sayısı/envanter iddiası | C4 |
| 7 | `oauth.css`'i yeniden adlandırmak | `css-template` §3.1 — mevcut ad korunur |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | `--color-*`, `--theme-*`, `--platform-*` (karşılık [VERIFY REQUIRED]) |
| `04_Components` | OAuth kartı ≥2 sayfada kullanılırsa bileşen oraya taşınır |
| Yükleyici (import/`<link>`) | **UNKNOWN** — `.ai/reports/unused-files-report.md:100`: "Hiçbir PHP/HTML'de referans yok — İNCELE (auth.coremusic.net yükleyebilir)" → ⚠️ VERIFICATION REQUIRED; şablon yükleyici iddiası yazmaz |
| ADR-088 · ADR-001 | bağlayıcı (dosya başlığı `@see`) |

## Import Rules (Import Kuralları)

- **Yükleyici kanıtsız (⚠️ VERIFICATION REQUIRED):** diskte `@import ... 11_OAuth/oauth.css`
  referansı ve PHP `<link>` kanıtı **bulunamadı** (unused-files-report). Bu nedenle bu şablon
  import satırı **UYDURULMAZ**.
- Yükleyici doğrulanana kadar kural: eğer cihaz zincirine eklenirse `10_Helpers`'tan **sonra**,
  `?v={{v}}` ile (11 > 10 katman sırası — `css-template` §3.1).

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Mevcut dosya | `oauth.css` — **değiştirilmez** |
| Yeni dosya | `o-{{ad}}.css` (`css-template` §3.1 `o-` öneki) |
| BEM | `.oauth-{{block}}` — camelCase yasak (§4.1 #6) |

## Device Rules (Cihaz Kuralları)

- Cihaz davranışı `08_Devices` (`d-auth-*`); bu katman cihaz bağımsızdır.
- Mobilde kart düzeni token/`@media` ile uyar; kırılım `a-breakpoint-tokens.css` ile eşleşmeli.

## Responsive Rules (Responsive)

- `@media` yalnız token uyarlaması (padding/gap/min-height); breakpoint tahmin edilmez.
- Dokunma hedefi `var(--touch-min)` — taban 48px (C2).

## Token Rules (Token Kuralları)

1. Yeni `--token` yalnız `01_Abstracts/` (C6).
2. Bu katman **yalnız tüketir**; `var(--color-*)`/`--theme-*`/`--platform-*` adlarının 01'deki
   karşılığı okunmadan yazılmaz → yoksa `⚠️ VERIFICATION REQUIRED`.
3. `--touch-min` taban 48px (C2).

## Validation (Doğrulama)

- [ ] Dosya `oauth.css` (mevcut) veya `o-{{ad}}.css` (yeni) kalıbında
- [ ] Token tanımı yok · ham hex/px/rem yok · `!important` 0
- [ ] ≥2 sayfada tekrar → `04_Components`'e taşındı mı?
- [ ] Kullandığı tüm token adları `01_Abstracts`'te doğrulandı (yoksa [VERIFY REQUIRED])
- [ ] Yükleyici kanıtı varsa (import/link) kaydedildi — yoksa iddia yazılmadı
- [ ] Gerçek sayfada tarayıcı testi yapıldı

## Example Structure (Örnek Yapı)

```css
/**
 * 11_OAuth/oauth.css   (mevcut dosya adı — yeniden adlandırılmaz)
 * ROL   : OAuth/giriş akışı UI (ADR-088 · ADR-001 · BEM)
 * TOKEN : 01_Abstracts'ten TÜKETİR — tanım YOK (C6) · ham hex/px/rem YOK (§4.1 #1)
 * MOCKUP: {{png-path}}
 */

.oauth-platforms {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(var(--oauth-card-min, 280px), 1fr));
  gap: var(--space-md);            /* ham `1rem` → token (§4.1 #1) */
  padding: var(--space-md) 0;
}

.oauth-platform-card {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  min-height: var(--touch-min);    /* C2 — ≥48px */
  padding: var(--space-md) var(--space-lg);
  border-radius: var(--radius-md);
}

.oauth-platform-card__label {
  font-size: var(--text-base);
  color: var(--text-primary);
}

.oauth-platform-card:focus-visible {
  outline: 2px solid var(--color-accent);
  outline-offset: 2px;
}
```

> `--oauth-card-min` kalıbı **örnek** — gerçek token adı `01_Abstracts`'te yoksa uydurulmaz,
> `var(--oauth-card-min, 280px)` fallback'i de §4.1 #1 sayılır → token önce `01_Abstracts`'e eklenir.

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R — dokunulmayan ikincil düzeltmeler):**

| Hedef | Sorun |
|-------|-------|
| `11_OAuth/oauth.css` L18-30 | ham değerler (`1rem`, `1.25rem`, `0.75rem`, `280px`) — §4.1 #1 ihlali (01 dışı sabit) · `minmax(280px,…)` grid kuralı bu katmanda yerleşim sanısı (§3.1 ayrımı — `04`/`05` sınırı kontrol edilmeli) · tam ihlal sayısı `[VERIFY REQUIRED]` (dosyanın tamamı okunmadı) |
| `.ai/reports/unused-files-report.md:100` | `oauth.css` yükleyicisi bilinmiyor (PHP/HTML referansı yok) — açık madde ⚠️ VERIFICATION REQUIRED |
| `assets.coremusic.net/Css/CLAUDE.md` §3.2 | "`o-` önek" + `oauth.css` — mevcut dosya öneksiz (bu şablon: mevcut ad korunur, yeni dosya `o-`) |
