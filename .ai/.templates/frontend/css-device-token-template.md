---
title: "CoreMusic — CSS Cihaz Token Şablonu (01_Abstracts/a-layout-tokens-*)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 1.0.0
status: active
authority: reference
---

# CSS Cihaz Token Şablonu — `01_Abstracts/a-layout-tokens-{width}.css`

**Kapsam:** Cihaz genişliği ailesinin layout token dosyaları — **BASE + cihaz override**.
**Ana şablon:** [[css-template]] §3.3 · **Kardeş:** [[css-abstracts-token-template]] · **Gate:** Guardrail #16
**YENİ DOSYA (2026-10-06) — önceki oturumda raporlanıp diske düşmemişti.**

---

## Purpose (Amaç)

Her cihaz genişliği için ayrı token dosyası yazmak: hangi cihaz için hangi token'ın hangi dosyada olduğu
dosya adından anlaşılır. Tek dosyaya yığmak yasak (`css-template` §4.3 hata#4).

## Location (Konum)

`assets.coremusic.net/Css/01_Abstracts/a-layout-tokens-{width}.css`

| Dosya | Rol |
|-------|-----|
| `a-layout-tokens-1024.css` | **BASE (C1)** — medyasız `:root`, tüm cihaz fallback'i (RPi5 1024×600, Figma ana hedef) |
| `a-layout-tokens-mobile.css` | phone kırılımı |
| `a-layout-tokens-tablet.css` | tablet kırılımı |
| `a-layout-tokens-1920.css` | wide desktop |
| `a-layout-tokens-3540.css` | 4K monitor |
| `a-layout-tokens-3840.css` | 4K TV |

> **C1 — `a-layout-tokens.css` diskte YOK** (git'te hiç track edilmedi; 2026-10-06 doğrulandı) —
> uydurulmaz; BASE bu tabloda `…-1024.css`'tir. Disk kanıtı: `a-layout-tokens-1024.css` L21 medyasız
> `:root {`, `@import` 0 satır.

## Responsibility (Sorumluluk)

- Cihaz genişliğine ait **token değerlerinin** tanımlanması (overlay base).
- Figma SSOT ölçüleri (`node-id` referanslı) + `notes.md` notlarının cihaz bazlı uygulanması.

## Allowed (İzinli)

- `:root { --token: değer; }` — mevcut token'ın değerini base'den **farklı** yazma.
- `@media (min-width) and (max-width)` sarmalayıcısı (dosyanın kendisi cihaz-aralıklıysa).
- `var(--x, fallback)` referansı.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Sınıf seçici / layout kuralı (`.header { … }`) | `css-template` §4.1 #1 · katman saflığı |
| 2 | `a-layout-tokens.css` uydurma | C1 |
| 3 | BASE'te olmayan **yeni** token adı (yalnız değer override) | C6 katman kuralı |
| 4 | Tek dosyaya tüm cihaz yığma | `css-template` §4.3 #4 |
| 5 | Şablonda envanter sayısı iddiası | C4 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `a-layout-tokens-1024.css` | BASE — bu dosyalar onu **ezen overlay**'dir |
| `.ai/ui-design/tokens/` | Figma SSOT (7 breakpoint; 3840/TV token'ı Figma'da boş → ⚠️ VERIFICATION REQUIRED) |
| `08_Devices/d-*.css` | her cihaz dosyası kendi token dosyasını `?v=` ile import eder |
| `notes.md` | not uygulanır + `✓` imzalanır |

## Import Rules (Import Kuralları)

1. Bu dosya **import etmez** (token saflığı) — import edilir.
2. Giriş: `08_Devices/d-{device}.css` ilk satır(lar)ında:
   `@import url("../01_Abstracts/a-layout-tokens-{{width}}.css?v={{v}}");`
3. BASE (`…-1024.css`) cihaz dosyasında **her zaman** en az bir kez yüklenir (fallback); cihaz dosyası
   kendi genişlik dosyasını **sonra** import eder → son yazan kazanır.

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `a-layout-tokens-{width}.css` |
| `{width}` | dosya adında rakam/kısaltma: `1024`, `1920`, `3540`, `3840`, `mobile`, `tablet` |
| Token | `--{kategori}-{ad}` — BASE ile **aynı ad** (farklı ad = yeni token = C6 ihlali) |

## Device Rules (Cihaz Kuralları)

- Dosya = cihaz; cihaz adı dosya adında görünür.
- Cihaz davranışı (hover yok, tap highlight, font-size) bu katmanda DEĞİL → `08_Devices`
  ([[css-device-template]]).
- Cihaz dosyasının `:root` içindeki **değer override'ı** ile bu dosya çakışırsa: `08_Devices` sonradan
  yüklendiği için kazanır; ama **tercih edilen yer** bu dosyadır (token üretimi 01'de — C6).

## Responsive Rules (Responsive)

- Sıralama/öncelik: **base (`…-1024`) → mobile → tablet → 1920 → 3540 → 3840** (daha geniş kırılım
  sonra gelir, dar olanı ezer). Import sırası buna göre düzenlenir.
- Dosya içi `@media` genişlik aralığı dosya adındaki cihazla **eşleşmeli** (ör. mobile dosyası
  ≤767px — değer `a-breakpoint-tokens.css`'ten okunur, tahmin edilmez).
- `@media` içinde `var()` okunamaz (CSS kısıtı).

## Token Rules (Token Kuralları)

1. **Yalnız `--token: değer`** — seçici/kural yok (yalnız `:root` sarmalayıcısı).
2. **Yeni token adı yasak** (C6): override edilen ad BASE'te tanımlı olmalı; yeni ad gerekiyorsa önce
   BASE `a-layout-tokens-1024.css`'e eklenir.
3. Taban `--touch-min: 48px` (C2); cihaz override değerleri `Css/CLAUDE.md` §4.1 #7 ve
   brain.md:806-811 (Phone/Embedded ≥48 · Wide/4K ≥24) ile uyumlu olmalı — 44px **yazımaz**.
4. Ham px/hex bu dosyada serbesttir (bu katman token katmanı).
5. Figma'da değer yoksa (3840/TV token json boş) **uydurulmaz** → `⚠️ VERIFICATION REQUIRED` +
   `05-responsive-architecture` üzerinden türetme.

## Validation (Doğrulama)

- [ ] Dosya adı `a-layout-tokens-{width}.css` kalıbında; `a-layout-tokens.css` (BASE'siz) değil
- [ ] Yalnız `:root` (+ gerekirse `@media`) — sınıf seçici yok
- [ ] Tüm token adları BASE'te tanımlı (yeni ad yok — C6)
- [ ] `--touch-min` ≥ 48px taban; 44px yok (C2)
- [ ] İlgili `d-*.css` bu dosyayı `?v=` ile import ediyor
- [ ] Öncelik sırası base → … → 3840
- [ ] Sayı/envanter iddiası yok (C4)

## Example Structure (Örnek Yapı)

```css
/**
 * 01_Abstracts/a-layout-tokens-{{width}}.css
 * CİHAZ : {{device}} · ARALIK: {{min}}–{{max}}px
 * KAYNAK: .ai/ui-design/tokens/ (Figma SSOT) — node-id={{figma-node}}
 * NOT   : notes.md notu uygulanır + ✓ imzalanır
 * KURAL : yalnız token değeri — seçici/kural YOK · yeni token adı YOK (C6)
 */

@media (min-width: {{min}}px) and (max-width: {{max}}px) {
  :root {
    /* === GENEL ÖLÇÜ (BASE override) === */
    --header-h: 60px;
    --footer-h: 90px;
    --grid-gap: 8px;

    /* === TOUCH (C2 — taban 48px; cihaz büyütür, küçültmez) === */
    --touch-min: 48px;
    --touch-recommended: 56px;

    /* === COMPONENT TOKEN === */
    --card-thumb-size: 140px;
    --widget-grid-cols: 2;

    /* === Z-INDEX === */
    --z-header: 100;
    --z-modal: 300;
  }
}

/* BASE ise @media YOK — doğrudan :root (C1: a-layout-tokens-1024.css L21 gibi) */
```

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R):** `_home-layout.css` (05_Pages) L367-424 → `--touch-min: 48/44/44/64px` — sayfa katmanında
token tanımı + 44px (C6 + C2 ihlali, dokunulmadı).
