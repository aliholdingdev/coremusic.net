---
title: "CoreMusic — ADR-093: 09_ViewModes v-*.css Tek Yükleme Yolu (cm-view-css Kanonik)"
type: "architecture-decision"
category: "frontend"
date: "2026-10-06"
updated: "2026-10-06"
version: "1.0.0"
status: "accepted"
authority: "SSOT — 09_ViewModes/v-*.css yüklenmesinin tek kanonik yolu <link id=\"cm-view-css\">'dir; cihaz bundle'ları (08_Devices/d-*.css) içindeki 09_ViewModes @import zinciri KALDIRILMASI KARARLAŞTIRILDI, uygulama kullanıcı kararıyla ERTLENDİ (2026-10-06: şimdilik import'a dokunulmaz)"
kaynak: "Expert taslağı + kullanıcı kararı (2026-10-06) + disk kanıtı: d-desktop.css:53-55, d-laptop.css:53-55, d-4k.css:13, DeviceRenderer.php:127-128/151, device-loader.js:125-141/172/183-191, ViewModeManager.js:70-72, DeviceManager.js:91-102 · şablon .ai/.templates/adr/adr-template.md"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-093: 09_ViewModes v-*.css Tek Yükleme Yolu (Single Load Path for View-Mode CSS)

> **Durum:** ✅ **ACCEPTED** (karar) · **Uygulama:** ⏳ **DEFERRED — ERTLENDİ** (kullanıcı kararı 2026-10-06: import'a dokunulmaz, yalnız ADR yazılır) · **Tarih:** 2026-10-06 · **Ağırlık:** 1 (varsayılan) · **İlgili ADR:** 045
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `view-modes-single-load-path` · **Dosya:** `ADR-093-view-modes-single-load-path.md`
> **İlgili kararlar:** [[ADR-045-multi-domain-view-mode-architecture]] (view mode mimarisi) · [[ADR-001-vanilla-js-itcss]] (ITSS katman sırası) · [[../index.md]] · [[../../brain.md]]
> **Şablon:** `.ai/.templates/adr/adr-template.md` (Guardrail #16 — 10 alanlı frontmatter + §1-§7 iskelet)
> **Numara gerekçesi (disk kanıtı):** `.ai/.decisions/accepted/` içinde en yüksek numara **ADR-092** (`ADR-092-media-dizin-ekseni-ve-ulid.md`); **ADR-091 dolu** (`.ai/.decisions/ADR-091-template-engine-no-eval.md`) → **ilk boş numara 093**. Frozen **001-037'ye dokunulmadı**; ayrı seri `.ai/architecture/adr/` (023-026) **karıştırılmadı**.

---

## §1 Bağlam (Context)

### §1.1 Çift Yol Kanıtları (disk — tarama 2026-10-06)

| # | Kaynak (dosya:satır) | Kanıt | Etiket |
|---|----------------------|-------|--------|
| 1 | `assets.coremusic.net/Css/08_Devices/d-desktop.css:53-55` | `@import "../09_ViewModes/v-home.css?v=2.1.0"` · `v-pro.css?v=2.1.0` · `v-studio.css?v=2.1.0` | IMPLEMENTED (kod) |
| 2 | `assets.coremusic.net/Css/08_Devices/d-laptop.css:53-55` | Aynı 3 import (`v-home` / `v-pro` / `v-studio`) | IMPLEMENTED (kod) |
| 3 | `assets.coremusic.net/Css/08_Devices/d-4k.css:13` | Minified satırda aynı 3 import (`...v-home.css?v=2.1.0");@import ...v-pro...;@import ...v-studio...`) | IMPLEMENTED (kod) |
| 4 | `shared/src/Device/DeviceRenderer.php:127-128` | SSR: `link(deviceCssPath(), LINK_ID_DEVICE)` + `link(viewCssPath(), LINK_ID_VIEW)` → **view ayrı link** | IMPLEMENTED (kod) |
| 5 | `assets.coremusic.net/js/device-loader.js:172` | `loadCSS(base + VIEW_CSS[viewMode] \|\| VIEW_CSS['home'], 'cm-view-css')` → **view ayrı link** | IMPLEMENTED (kod) |
| 6 | `assets.coremusic.net/js/managers/ViewModeManager.js:70-72` | `link.href = baseUrl + cssPath` → `document.head.appendChild(link)` (`id='cm-view-css'`) | IMPLEMENTED (kod) |

**Ölçüm:** `08_Devices/` altında **15 CSS dosyası**; **09_ViewModes import'u yalnız 3'ünde** (d-desktop · d-laptop · d-4k) → **import 3/15 cihazda**. `09_ViewModes/` = 4 dosya (`v-car.css` 363 B · `v-home.css` 365 B · `v-pro.css` 363 B · `v-studio.css` 369 B).

### §1.2 Diğer Kanıtlar

| # | İddia | Kanıt | Durum |
|---|-------|-------|-------|
| 7 | **09_ViewModes = 0 seçici** | `v-home.css` → **10 satır** (yalnız başlık yorumu), seçici sayısı **0** (tüm v-*.css iskelet: "DURUM: iskelet — içerik bekleniyor") | ✅ DOĞRULANDI (disk) |
| 8 | **v-car yalnız linkte** | `v-car.css` hiçbir `d-*.css` içinde import edilmiyor; yalnız link haritalarında: `js/devices.config.js:39` · `managers/ViewModeManager.js:19` · `shared/src/Device/DeviceCssMap.php:32` | ✅ DOĞRULANDI (disk) |
| 9 | **Farklı `?v=` URL'leri = gerçek 2× indirme** | SSR `DeviceRenderer.php:151` → `?v=<cacheBuster>` (view linkine) · import satırları → `?v=2.1.0` sabit (cihaz bundle'ı içine gömülü) → **aynı v-home.css iki farklı URL** | ✅ DOĞRULANDI (kod) |
| 10 | **Guardrail boşluğu — cache-buster tutarsızlığı** | `device-loader.js:125-128` `?v=` **EKLER** (`cssBuster()`) · `DeviceRenderer.php:151` `?v=` **EKLER** · **ama** `DeviceManager.js:91-102` (`#loadCSS`) ve `ViewModeManager.js:61-72` (`#applyMode`) `?v=` **EKLEMEZ** | ✅ DOĞRULANDI (kod) — **ayrı HIGH ticket** |

### §1.3 Latent Bug (kısmi ezilme riski)

`device-loader.js:183-191` `loadDeviceOnly()` yalnız `cm-device-css`'i yükler (`:189`); `loadCSS` her seferinde link'i **head'in SONUNA** ekler (`device-loader.js:141` `document.head.appendChild(link)`). Sonuç: `cm-device-css` yeni link'i, mevcut `cm-view-css`'ten **SONRA** gelir → cihaz bundle'ındaki import edilmiş `v-*.css` kuralları (sıra 1, eşit özgüllükte **geç kazanır**) aktif view kuralını **ezme** potansiyeli taşır. Bugün zararsız (§1.2 madde 7: 0 seçici) — içerik yazıldığı anda **aktif görünümün ezilmesine** dönüşür.

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **Bu görev yalnız ADR yazar** | CSS/JS/PHP/template dosyalarına **DOKUNMAZ**; import kaldırma **uygulaması ertelendi** (kullanıcı kararı 2026-10-06) |
| **Frozen 001-037 immutabel** | Yalnız atıf |
| **Yeni registry OLUŞTURMAZ** | Mevcut `.ai/.decisions/` sistemi (index.md + accepted/draft/rejected) kullanılır; `index.md` kayıt satırı ayrı işlem |
| **Kod dokunma yok** | Karar seviyesi IMPLEMENTED (belge), uygulama seviyesi **DEFERRED** |

---

## §2 Karar (Decision)

**09_ViewModes `v-*.css` dosyalarının TEK kanonik yükleme yolu `<link id="cm-view-css">`'dir; cihaz bundle'larındaki (`08_Devices/d-*.css`) `@import "../09_ViewModes/v-*.css"` zinciri KALDIRILACAKTIR — uygulama ERTLENMİŞTİR.**

### §2.1 Karar Maddeleri (4)

| # | Karar |
|---|-------|
| **1** | **Kanonik yol = `cm-view-css` linki.** View-mode CSS yalnız SSR (`DeviceRenderer.php:127-128`) ve client (`device-loader.js:172` · `ViewModeManager.js:69-72`) üzerinden ayrı bir `<link id="cm-view-css">` ile yüklenir |
| **2** | **3 import kaldırılacak — UYGULAMA ERTLENDİ.** `d-desktop.css:53-55` · `d-laptop.css:53-55` · `d-4k.css:13` içindeki `v-home/v-pro/v-studio` import'ları **karar gereği kalkacak**; **onay kapsamı 2026-10-06'da yalnız ADR'dir → şimdilik import'a dokunulmaz** |
| **3** | **Import yolu hiçbir cihazda yeniden açılmaz.** 08_Devices → 09_ViewModes yönünde **yeni @import eklenemez** (cascade sırası + çift indirme tekrarlanmasın) |
| **4** | **Guardrail (AÇIK — HIGH ticket):** `cm-view-css` / `cm-device-css` yazan **tüm yazıcılar tek cache-buster sözleşmesine** bağlanacak. Bugün sözleşmeyi yalnız `device-loader.js:125-128` ve `DeviceRenderer.php:151` uyguluyor; **`DeviceManager.js:91-102` ve `ViewModeManager.js:61-72` `?v=` eklemiyor** → ayrı HIGH ticket olarak açılır (bu ADR'de kapatılmaz) |

### §2.2 Senior Görüş (ITCSS / Cascade / ViewMode judgment'ı)

**KABUL.** View-mode katmanı (`09_ViewModes`) cihaz bundle'ının **içine gömülmemeli**; ITCSS sırası ve `view > cihaz` önceliği için tek giriş noktası `cm-view-css`'tir — `device-loader.js:141`'in `appendChild` davranışı, `loadDeviceOnly` sonrası cihaz bundle'ındaki import'ların aktif view'ı ezebilmesini (§1.3) doğrulanmış olarak üretiyor. Ertleme (madde 2) de doğru: v-*.css bugün **0 seçici** olduğundan import'u şimdi kaldırmak **0 stil kazancı** getirir ama taşıma riski taşır → karar kabul, uygulama beklemeye alınır.

### §2.3 Rationale (Neden?)

1. **Çift indirme kalkar:** aynı `v-home.css` farklı `?v=` URL'leriyle 2× iniyor (§1.2 madde 9); tek yol = tek istek.
2. **Cascade bilinirliği:** view katmanı her zaman cihaz bundle'ından **sonra** gelir → `view > cihaz` önceliği sıraya değil, **tek link'e** bağlıdır.
3. **Latent bug kapanır:** `loadDeviceOnly` (§1.3) tek başına `cm-device-css`'i sona taşıdığı için, bundle içi import artıkça aktif view ezilebilirdi; import kalkınca bu yol imkânsızlaşır.

---

## §3 Alternatifler

| # | Alternatif | Neden Reddedildi |
|---|-----------|------------------|
| **1** | Import yolunu **şimdi** kaldırmak (uygulamalı) | **RET (kullanıcı kararı 2026-10-06):** kapsam yalnız ADR; 0 seçici olan dosyalarda **0 kazanç**, taşıma riski var |
| **2** | View'ları cihaz bundle'ına **taşımak** (tek yol = import) | **RET:** `loadDeviceOnly`/`ViewModeManager` dinamik view değişimini (`remove + append`) kırar; SSR/client link sözleşmesi (`DeviceRenderer.php:26`, `device-loader.js`) bozulur |
| **3** | Import'ları **@layer** ile yönetmek | **RET:** mevcut katman sözleşmesi yok (`.ai/.templates/frontend/css-template.md` 11 katman — @layer değil, dosya katmanı); ek karmaşıklık, ADR-001 ihlali riski |

---

## §4 Sonuçlar (Consequences)

### §4.1 (+) Olumlu

- **Takas edilebilirlik artar:** cihaz bundle'ı view bilgisinden kopar; d-* dosyaları yalnız cihaz işini taşır (ITCSS 08_Devices sınırı korunur).
- **Çift indirme biter:** tek URL + tek `?v=` → `v-*.css` bir kez indirilir (önbelleklenir).
- **Latent ezilme yolu kapanır:** `loadDeviceOnly` sonrası bundle-içi view kuralları artık `cm-view-css`'i geçemez.

### §4.2 (−) Olumsuz

- **`view > cihaz` önceliği bilinçli bir sıraya bağlı:** JS'siz **ilk boyama SSR linkine** (`DeviceRenderer.php:127-128`) bağımlıdır; SSR link'i üretilmezse view CSS hiç gelmez.
- **İki yazıcı sözleşmesi açık:** `DeviceManager.js:91-102` / `ViewModeManager.js:61-72` `?v=` eklemiyor → cache tutarsızlığı ayrı HIGH ticket'a taşındı (§2.1 madde 4).

### §4.3 (0) Nötr — Bugünkü Etki

- **Stil değişmez:** `09_ViewModes/*.css` = **0 seçici / 10 satır iskelet** (§1.2 madde 7) → import kaldırılsa da **bugün hiçbir kural kaybolmaz**; görsel fark **0**.

### §4.4 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| R1 v-*.css içerik girince ezilme | Orta | Yüksek | Madde 2-3 uygulandığında §1.3 yolu kapanır |
| R2 `?v=` tutarsızlığı önbellek bayatlaması | Orta | Orta | Madde 4 → HIGH ticket |
| R3 SSR link'siz ilk boyama | Düşük | Orta | `DeviceRenderer.php:127-128` sözleşmesi korunur, gözlemlenir |

### §4.5 Performans Kanıtı (2026-10-06 ölçümü)

> **Kaynak:** performance-engineer ölçümü (2026-10-06 · `Get-Item` boyutları) — gzip değerleri **yerel tahmindir**, sunucu sıkıştırma durumu **[UNKNOWN]**.

| # | Bulgu | Değer / Etki |
|---|-------|--------------|
| 1 | Çift yükleme maliyeti (link + import) | ~1,8 KB / +2 istek · `v-car` **çift DEĞİL** — çift yüklenen 3/4 dosya (`v-home` · `v-pro` · `v-studio`, §1.1) |
| 2 | 3. yükleme yolu | `ViewModeManager.js:65-72` — **sorgusuz** link (`?v=` eklenmez → §2.1 madde 4) |
| 3 | Asıl darboğaz — cihaz bundle `@import` zinciri | **48 `@import` + iç içe 2 seviye = 51 render-blocking istek**, **366,9 KB** (~92,8 KB gzip **tahmini**) · preload scanner keşfi **seri** yürütür |
| 4 | Tavsiye a1 (zincirden çıkar / link kalsın) | **ADR kararıyla uyumlu** (§2.1 madde 2-3) · kaskad riski **bugün 0** (09_ViewModes = 0 seçici, §1.2 madde 7) |
| 5 | Gelecek risk | 3 ayrı cache anahtarı (`?v=` tutarsızlığı) + `ViewModeManager` head sonu ekleme → iskelet dolunca **sidebar↔view flip** — **HIGH ticket'a bağlı** (§2.1 madde 4) |
| 6 | Uzun vadeli en yüksek kazanım | **c1:** PHP'de 48 ayrı `<link>`, bundle'sız |

---

## §5 Uygulama (Implementation — DEFERRED)

### §5.1 Adımlar

| # | Adım | Sorumlu | Durum |
|---|------|---------|-------|
| 1 | Bu ADR'yi şablondan üret (Guardrail #16) | Vault Steward | ✅ UYGULANDI (2026-10-06) |
| 2 | `d-desktop.css:53-55` · `d-laptop.css:53-55` · `d-4k.css:13` import kaldırma | UI Designer | ⏳ **DEFERRED** (kullanıcı onayı gerekir) |
| 3 | `?v=` cache-buster tek sözleşmesi (DeviceManager + ViewModeManager) | UI Designer | ⏳ **AÇIK — HIGH ticket** |
| 4 | `.ai/.decisions/index.md` ADR-093 kayıt satırı | MO (vault-updater) | ✅ UYGULANDI (2026-10-06 — index.md §4 satırı eklendi) |
| 5 | Uygulama sonrası browser testi (view değişimi + `loadDeviceOnly` senaryosu) | QA | ⏳ PLANNED (adım 2 ile) |

### §5.2 Geri Dönüş Planı

1. Karar seviyesi `accepted` ama **frozen değil** → vazgeçiş = yeni ADR (094) + `superseded-by` bağı; metin silinmez.
2. Adım 2 henüz yapılmadığı için **geri alınacak kod değişikliği yoktur** (0 dosya).
3. `log.md` append-only → her durumda yeni satır.

---

## §6 İlgili Dokümanlar

| Dosya (wiki-link) | İlişki |
|-------------------|--------|
| [[ADR-045-multi-domain-view-mode-architecture]] | View mode mimarisinin kaynağı — bu ADR onun **yükleme yolunu** sabitler |
| [[ADR-001-vanilla-js-itcss]] | ITCSS katman sırası (08_Devices ↔ 09_ViewModes sınırı) |
| [[../index.md]] | Karar dizini — **ADR-093 kayıt satırı YAZILDI** (2026-10-06 — index.md §4) |
| [[../../brain.md]] | ADR özet tablosu (uygulama sonrası) |
| `.ai/.templates/adr/adr-template.md` | §1-§7 iskelet kaynağı (Guardrail #16) |
| `assets.coremusic.net/js/device-loader.js` · `managers/ViewModeManager.js` · `managers/DeviceManager.js` | Link yazıcıları — §2.1 madde 4 sözleşmesi |
| `shared/src/Device/DeviceRenderer.php` | SSR link üretimi (`LINK_ID_VIEW = 'cm-view-css'`, `:26`) |

---

## §7 Onay

### §7.1 Kabul Kriterleri

| # | Kriter | Durum |
|---|--------|-------|
| 1 | Doğru seri + ilk boş numara (**093**) kullanıldı, frozen'a dokunulmadı | ✅ (Numara gerekçesi — künye) |
| 2 | Kanıt satırları disk ile birebir (§1.1-§1.3) | ✅ DOĞRULANDI (2026-10-06 grep) |
| 3 | Yalnız ADR dosyası yazıldı; kod yüzeyine dokunulmadı | ✅ |
| 4 | Yeni registry dosyası OLUŞTURULMADI (mevcut `.ai/.decisions/` sistemi) | ✅ |
| 5 | Mojibake yok, UTF-8 Türkçe | ✅ |

### §7.2 Karar Künyesi

| Alan | Değer |
|------|-------|
| **Durum** | **accepted** — uygulama: **deferred (ertelendi)** |
| **Tarih** | **2026-10-06** |
| **Ağırlık** | 1 (varsayılan) |
| **İlgili ADR** | 045 |
| **Seriler karıştırılmadı** | `.ai/architecture/adr/` (023-026) ayrı seri |

### §7.3 Onay Tablosu

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-10-06 | ✅ (kullanıcı onayı: yalnız ADR) |
| Senior (ITCSS/BEM/Token) | ui-designer (Senior — §2.2) | 2026-10-06 | ✅ KABUL |
| Tech Lead | — | — | ⏳ |
| Arch Lead | — | — | ⏳ |

---

*ADR-093 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-093 Karar Metni (SSOT)*
*Last Updated: 2026-10-06*
*Mode: Red Team · Human Mode · Truth Mode*
