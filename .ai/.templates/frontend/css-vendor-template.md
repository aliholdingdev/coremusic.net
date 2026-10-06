---
title: "CoreMusic — CSS Vendor Şablonu (07_Vendors)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 1.0.0
status: active
authority: reference
---

# CSS Vendor Şablonu — `07_Vendors/`

**Kapsam:** 3. taraf stiller (Bootstrap ailesi) — **salt okunur** · önek `v-` (sarmalayıcı) + upstream `bootstrap*`
**Ana şablon:** [[css-template]] §3.1 · **Denetim:** Security Engineer (`Css/AGENTS.md` §3.2)
**YENİ DOSYA (2026-10-06) — önceki oturumda raporlanıp diske düşmemişti.**

---

## Purpose (Amaç)

Yeni 3. taraf stylesheet'i bu katmana **yerleştirmenin** (veya mevcut dosyayı **güncellemenin**) kuralını
tanımlamak. Katmanın varlık nedeni: upstream kodu projeden karantinada tutmak — elle değişiklik
upgrade'i imkânsızlaştırır + güvenlik yaması kaybolur (`Css/CLAUDE.md` §4.1 #10 gerekçesi).

## Location (Konum)

`assets.coremusic.net/Css/07_Vendors/`
Örnek dosyalar (2026-10-06 ölçümü — envanter iddiası değil; sayı/envanter yalnız `Css/CONTEXT.md §3.1`'de — C4):
`v-bootstrap-lib.css` (proje sarmalayıcısı — minimal reset) · `bootstrap*.css` + `.map` ailesi
(reboot / grid / utilities varyantları, `?v=5.3.8` disk kanıtı).

**YOK — uydurulmaz:** `bootstrap.bundle*`/JS (CSS katmanı değil) · bu dizin dışında vendor CSS.

## Responsibility (Sorumluluk)

- Upstream dosyaların **dokunulmaz** tutulması; sürüm senkronu yalnız upstream kopyasıyla yapılır.
- `v-bootstrap-lib.css` = projenin minimal vendor reset'i (disk başlığı: "Minimal vendor reset —
  no full Bootstrap"); o da §4.1 #10 kapsamındadır — değişiklik talebi Security + gerekçe ister.

## Allowed (İzinli)

- Upstream dosyayı **aynı içerikle** yenilemek (sürüm bump, upstream dosyasının birebir kopyası).
- `08_Devices/d-*.css` içinden `@import` (yalnız orada; auth paketinde **yok**).
- `.map` dosyalarının birlikte taşınması (debug).

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Dosyayı elle düzenlemek (hotfix, override, minify) | `Css/CLAUDE.md` §4.1 #10 — ihlal = revert |
| 2 | Vendor kodunu `07_Vendors` dışına kopyalamak | `assets.coremusic.net/AGENTS.md` §4 Yasak #2 |
| 3 | `auth-bundled.css` içine vendor import'u | `css-template` §3.6 |
| 4 | Token/`--x` tanımı, proje stilini buraya taşımak | §4.1 #9 (C6) — proje kodu `01`–`06`/`08`–`11`'de |
| 5 | Dosya sayısı/envanter iddiası ("33 dosya" vb.) şablonda | C4 — sayı `CONTEXT.md §3.1`'de |
| 6 | Sürüm/kaynak kanıtsız yeni paket ekleme | Zero-Hallucination |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Tür | Not |
|------------|-----|-----|
| Upstream (getbootstrap.com) | okunur | dosyalar upstream'den gelir; proje elle değiştirmez |
| `08_Devices/d-*.css` | tüketen | reboot/grid: `d-phone`/`d-tablet`/`d-embedded` L11-12; `d-desktop`/`d-laptop` L23-24; `d-4k` (hepsi 02_Base'den önce — disk kanıtı 2026-10-06); `v-bootstrap-lib`: `d-4k`/`d-desktop`/`d-laptop` (L52 vb.). `d-4k-monitor`/`d-4k-tv`: **0 bootstrap import** |
| `auth-bundled.css` | — | vendor **içermez** (§3.6) |
| Security Engineer | denetim | vendor ekleme/güncelleme talebi onay kapısı (`Css/AGENTS.md` §3.1) |
| `?v=` sürüm değeri | senkron | import eden cihaz dosyalarındaki `?v=5.3.8` dosya içeriğiyle eşleşmeli |

## Import Rules (Import Kuralları)

1. Import **bu katmanda yapılmaz**; `08_Devices/d-*.css` içinde, **`02_Base`'den ÖNCE**:
   `@import url("../07_Vendors/bootstrap-reboot.min.css?v={{v}}");`
2. `v-bootstrap-lib.css` zincirde `06` ile `09` arasındadır (disk kanıtı: `d-4k`/`d-desktop` L52).
3. `07_Vendors` yalnız cihaz dosyalarında; auth paketinde yok.
4. `?v={{v}}` zincir genelinde tek değer; vendor sürümü ayrı sabitse (`5.3.8`) dosya içi değerle eşleşir.

## Naming Rules (Adlandırma)

| Tür | Kalıp | Not |
|-----|-------|-----|
| Upstream dosya | `bootstrap[{,-reboot,-grid,-utilities}][.rtl][.min].css` | upstream adı **değiştirilmez** |
| Proje sarmalayıcı | `v-{{ad}}.css` | mevcut: `v-bootstrap-lib.css` |
| Source map | `{{dosya}}.css.map` | ad dosya ile eşleşir |

## Device Rules (Cihaz Kuralları)

- Vendor, cihaz bağımsız upstream kodudur; cihaz davranışı `08_Devices`'e **yazılmaz**, import sırası
  cihaz dosyasında ayarlanır (hangi cihaz kaç paket import eder — disk kanıtı §Dependencies).
- Cihaz için vendor override gerekiyorsa: override `08_Devices` davranış alanında ve mevcut
  seçici/öncelik ile; upstream dosyaya dokunulmaz.

## Responsive Rules (Responsive)

- Vendor'ın kendi `@media`'si upstream'den gelir (dokunulmaz).
- Breakpoint uyumsuzluğu çıkarsa çözüm: vendor'ı **değil**, `08_Devices` import'unu/`01_Abstracts`
  breakpoint token'ını düzelt (`a-breakpoint-tokens.css` — Figma SSOT).

## Token Rules (Token Kuralları)

- Bu katmanda **token yok** (üretim/tüketim yasak — `bootstrap*` hex/px içerir; upstream kodu
  kural dışıdır, proje katmanlarına örnek alınmaz).
- Proje token'ı vendor'a taşımak/`!important` ile bindirmek yasak → çatışma `08_Devices` sırasıyla çözülür.

## Validation (Doğrulama)

- [ ] Değişiklik yok — **salt okunur** (talep varsa: upstream kopyası + Security onayı)
- [ ] Yeni dosya upstream kaynaklı ve `.map`'i ile birlikte
- [ ] Import yalnız `08_Devices`'te ve `02_Base`'den önce (reboot/grid)
- [ ] `auth-bundled.css`'e vendor sızmadı
- [ ] `?v=` değeri dosya içi sürümle eşleşiyor
- [ ] Şablonda/envanter iddiası sayı yok (C4 — sayı `CONTEXT.md §3.1`'de)

## Example Structure (Örnek Yapı — dosya EKLEME/GÜNCELLEME adımları, CSS iskeleti DEĞİL)

```text
1) TALEP  : Security Engineer onayı (Css/AGENTS.md §4.1 — vendor sızması denetimi)
2) KAYNAK : upstream dosyanın birebir kopyası (+ .map) — elle değişiklik YOK
3) KONUM  : assets.coremusic.net/Css/07_Vendors/{bootstrap-*.css|.map | v-{{ad}}.css}
4) IMPORT : yalnız 08_Devices/d-*.css içinden, 02_Base'den ÖNCE, ?v={{v}} tek değer
5) SENKRO : import eden tüm cihaz dosyalarındaki ?v= + Css/CONTEXT.md §3.1 envanteri
6) TEST   : ≥2 cihaz genişliğinde tarayıcı doğrulaması
```

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R — dokunulmayan ikincil düzeltmeler):**

| Hedef | Sorun |
|-------|-------|
| `assets.coremusic.net/Css/CLAUDE.md` §4.1 #10 | "33 dosya salt okunur" — sayı iddiası; disk ölçümü 17 `.css` + 16 `.map` = 33 (2026-10-06 eşleşiyor; envanter sahipliği yine `CONTEXT.md §3.1`, C4) |
| `.ai/.templates/frontend/css-device-template.md` (eski v1) | "`css-imports.md` §3" atfı ölü referanstı — bu revizyonda disk kanıtı (satır numaralarıyla) değiştirildi |
