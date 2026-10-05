---
title: "CoreMusic — Css/ Guardrail Özeti"
type: guide
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "SSOT (klasör kökü) — detay: .ai/.templates/frontend/css-template.md"
docType: claude
---

# CoreMusic — Css/ Guardrail Özeti

**docType:** claude · **Katman:** L3 sunum (ITCSS) · **Sorumlu:** Security Engineer (inline/CSP denetimi), UI Designer (uygulama)

**Zorunlu Bağlantılar:** [[../../.ai/.templates/index]] · [[../../.ai/.templates/frontend/css-template]] · [[../../.ai/CLAUDE.md]] · [[CONTEXT]] · [[AGENTS]] · [[WORKFLOW]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/Css/` içinde çalışacak her ajan/için **CSS guardrail özeti**dir. Kural numaraları [[../../.ai/.templates/frontend/css-template]] **§4.1 (10 hard guardrail)** içindir; bu dosya özet taşır, **yeni kural üretmez**. İhlal = kod revert edilir.

| Karar | ADR | Bu dosyadaki karşılığı |
|-------|-----|------------------------|
| Vanilla JS + ITCSS; framework/önişlemci yasak | ADR-001 | §4 #1, #4 |
| CSP nonce uyumu — inline style yasak | ADR-012 (ilgili) | §4 #3 |
| Cihaz bazlı import zinciri | ADR-045 | §4 #5, §5 |
| Mockup Before Frontend | Guardrail #16 | §4 #7, §5 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `Css/**/*.css` yazım/denetim kuralları | JS modülleri → `js/` ([[js-template]]) |
| BEM adlandırma, token kullanımı, katman ayrımı | Mockup üretimi → `.ai/ui-design/` |
| `07_Vendors` dokunulmazlığı | PHP controller/service mantığı → Backend |
| Cihaz katmanı = import + davranış | Veritabanı, güvenlik middleware (genel) |

- **Kullananlar:** UI Designer (yazım), QA Engineer (WCAG/responsive doğrulama), Backend Architect (inline style denetimi), Security (CSP).
- **Ön koşul:** `.ai/ui-design/` ilgili görseli okunmadan CSS yazılamaz — okunamıyorsa **DUR**.

---

## 3. Mimari

### 3.1 Ayrım Tablosu — Ne nereye yazılır (css-template §3.1)

| İçerik | Katman | Önek |
|--------|--------|------|
| Sadece `--token: değer` | `01_Abstracts/` | `a-` |
| Bare reset / yapısal iskelet | `02_Base/` | `b-`/`l-`/`page-` |
| Grid / header / footer / sidebar düzeni | `03_Layout/` | `_` |
| Tekrar eden görsel parça (buton, kart, form) | `04_Components/` | `c-`/`_` |
| PHP sayfasına özgü (`pages/**/*.php`) | `05_Pages/` | `p-`/`_` |
| Tek amaclı sıfır mantık sınıfı | `06_Utilities/` | `u-` |
| 3. taraf (Bootstrap) — **dokunulmaz** | `07_Vendors/` | `v-` |
| Cihaz kırılımı: **yalnız import + davranış** | `08_Devices/` | `d-`/`d-auth-` |
| Görünüm modu (home/pro/studio/car) | `09_ViewModes/` | `v-` |
| Tekrarlanabilir yardımcı desen/makro | `10_Helpers/` | `h-` |
| OAuth/login akış | `11_OAuth/` | `o-` |

### 3.2 Token Kuralı

- Token **üretimi** yalnız `01_Abstracts/` — cihaz dosyasına göre ayrılır (`a-layout-tokens.css` base, `-mobile`/`-tablet`/`-1024`/`-1920`/`-3540`/`-3840` cihaz override'ı).
- `06` ve `10` katmanları token **tüketir**, üretmez.
- Tema override **jeton seviyesinde**: `[data-theme] { --token: değer }` — seçici ağacı değişmez (css-template §3.7).

### 3.3 Dosya Adı Kalıpları (css-template §3.2)

| Katman | Desen | Örnek |
|--------|-------|-------|
| 01 | `a-{konu}-token(s).css` | `a-layout-tokens-1024.css` |
| 04 | `c-{bileşen}.css` | `c-footer-seek.css` |
| 05 | `p-{sayfa}.css` | `p-login-view.css` |
| 08 | `d-{cihaz}.css` · `d-auth-{cihaz}.css` | `d-phone.css` |
| 09 | `v-{mod}.css` | `v-studio.css` |
| 10 | `h-` | `h-ellipsis.css` |

---

## 4. Kurallar

### 4.1 Hard Guardrails (css-template §4.1 — 10/10; ihal = revert)

| # | Kural | İhlal Sonucu | **Neden var (gerekçe — ceza değil)** | Ref |
|---|-------|-------------|--------------------------------------|-----|
| 1 | **Hardcoded değer yasak** — her değer `var(--...)`; bileşen içinde sabit `16px` yazılmaz | revert | Tema/ölçü değişimi **tek noktadan** yapılsın diye; hardcoded = her ölçüm değişikliğinde tek tek dosya taraması (teknik borç) + mockup ölçüsüyle sessiz uyumsuzluk | css-template §4.1 #1 · §4.3 hata#1 |
| 2 | **`!important` yasak** — en fazla 3 istisna, gerekçesi yorumda | revert | `!important` specificity yarışını kazanır → **ITCSS katman sırası anlamsızlaşır** (önceki katman okunmaz), bakım maliyeti katlanır | §4.1 #2 |
| 3 | **Inline `style=""` yasak** | revert | CSP stil/içerik HTML'e gömülünce nonce ister; inline = XSS yüzeyi + ADR-012 politika kırılması | §4.1 #3 |
| 4 | **Önişlemci (SCSS/LESS) yasak** — saf CSS (ADR-001) | revert | Build adımı/dependency yok zincirde; önişlemci bağımlılığı vanilla CSS+JS zincirini kırar (derleme, cache, onboarding) | §4.1 #4 |
| 5 | **Cihaz katmanı = import + davranış**; yerleşim `02_Base`/`03_Layout` | katman ihlali | Yerleşim 8 cihaz dosyasına yazılırsa **8 yerde tekrar** = drift; tek evden yönetim korunur | §4.1 #5 · §4.3 hata#2 |
| 6 | **BEM zorunlu** — `.block__element--modifier` + `.is-*`; camelCase (`playerProgress`) yasak | revert | Bir bloğun tüm stilleri tek isim-uzayında → çakışma/öncelik-savaş yok, isim anlaşılır | §4.1 #6 · §3.9 |
| 7 | **WCAG dokunma hedefi** + **Mockup Before Frontend** | erişilebilirlik hatası / DUR | Kontrast/hedef boyut **gerçek kullanıcı erişimi**; görselsiz kod = tahmini UI → israf + revizyon döngüsü | §4.1 #7 · Guardrail #16 |
| 8 | **Component → 04, PHP sayfası → 05** ayrımı zorunlu | dosya taşınır | Component ortak, sayfa özel; karışınca her düzeltme **yan etki üretir** (diğer sayfa kırılır) | §4.1 #8 |
| 9 | **Token → 01_Abstracts** (cihaz dosyasına göre); cihaz dosyasına token yığılmaz | token geri alınır | Token iki yerde olursa **iki gerçeği (split-brain)** olur — hangisi geçerli bilinmez | §4.1 #9 · §4.3 hata#4 |
| 10 | **`07_Vendors/` elle düzenlenmez** (33 dosya salt okunur) | revert | Vendor upstream'e ait; elle değişiklik **upgrade'i imkânsızlaştırır** + güvenlik yaması kaybolur | §4.1 #10 |

**Guardrail eli10/eli15 blokları (§4.1'deki 10 kuralın gerekçesi — ceza değil, neden var):**

**#1 Hardcoded değer yasak**
> **eli10 (basit):** Dosyaya ham ölçü (px, renk) yazmak yerine herkesin kullandığı ad (token) yazılır — çünkü tek elden değişsin diye.
> **eli15 (detay):** Bu kural, aynı ölçünün yüzlerce dosyaya dağılmasını önlemek için var. Ham sayı yazılınca bir ölçü değişince her dosya tek tek aranır ve biri unutulursa site tutarsız görünür. İhlal edilirse kod geri alınır. Düzenin tek noktadan yönetilebilmesi için yazıldı.

**#2 `!important` yasak**
> **eli10 (basit):** "Ben en güçlüyüm" kelimesi yasak — çünkü herkes en güçlü olursa kimse birbirini geçemez, düzen bozulur.
> **eli15 (detay):** `!important`, sıradaki kuralları ezip geçer; katman sırası (01→11) anlamsızlaşır ve bir sonraki geliştirici hangi kuralın neden kazandığını anlayamaz. İhlal edilirse kod geri alınır, en fazla 3 istisna gerekçesiyle kalır. Bakılabilirlik (maintainability) için yazıldı.

**#3 Inline `style=""` yasak**
> **eli10 (basit):** HTML içine doğrudan stil yazmak yasak — çünkü görünmez kirlilik ve güvenlik açığı olur.
> **eli15 (detay):** Inline stil, güvenlik politikasını (CSP) kırar ve veriyle oynayan birisi içeriğe stil sızdırabilir. Ayrıca inline değer token takibinden kaçar. İhlal edilirse kod geri alınır. Güvenlik + tek kaynak için yazıldı.

**#4 Önişlemci (SCSS/LESS) yasak**
> **eli10 (basit):** Ek programlarla CSS küçültme/çıkarma yok — saf CSS yazılır, herkes aynı dili okusun diye.
> **eli15 (detay):** Önişlemci ek bir derleme adımı ve bağımlılık getirir; proje saf CSS + Vanilla JS ile kurulu (ADR-001). Eklenirse herkes o aracı kurmak zorunda kalır. İhlalde kod geri alınır. Bağımlılık sıfır tutmak için yazıldı.

**#5 Cihaz katmanı = import + davranış**
> **eli10 (basit):** Telefon ayarları kendi dosyasında; büyük düzen herkesin gördüğü ana dosyada — çünkü tekrar bozulmasın diye.
> **eli15 (detay):** Yerleşim 8 cihaz dosyasına yazılırsa aynı kural 8 kez tekrar eder ve biri değişince 7'si eski kalır. Cihaz dosyası sadece bağlantı (import) ve küçük davranış farkı taşır. İhlalde dosya taşınır, katman ihlali sayılır. Tekrarı (drift) önlemek için yazıldı.

**#6 BEM zorunlu**
> **eli10 (basit):** Her parça aynı adlandırma düzeniyle yazılır (blok, içine-öge, --değişiklik) — kimin ne olduğu anlaşılsın diye.
> **eli15 (detay):** Düzensiz isimlendirmede aynı parça farklı isimlerle iki kez yazılır, biri güncellenir diğeri bayat kalır. BEM tek isim uzayında her şeyi görünür kılar. İhlalde kod geri alınır. Okunabilirlik ve tekrar önlenmesi için yazıldı.

**#7 WCAG + Mockup Before Frontend**
> **eli10 (basit):** Butonlar dokunulacak kadar büyük olmalı ve kod yazmadan ekran resmi okunmalı — yanlış iş çıkmasın diye.
> **eli15 (detay):** Küçük dokunma hedefi engelli/tembel kullanıcıları dışlar (erişilebilirlik kusuru); görsiz kod ise tahmin demektir, çıkan ürün beğenilmez ve iş iki katına çıkar. İhlalde erişilebilirlik hatası veya DUR oluşur. Gerçek kullanıcı ve gerçek tasarım için yazıldı.

**#8 Component → 04 · Sayfa → 05**
> **eli10 (basit):** Ortak parça ayrı kutuya, tek sayfanın süsü kendi kutusuna — çünkü biri değişince diğeri bozulmasın.
> **eli15 (detay):** Component 05'e yazılırsa tek sayfaya özel kural ortak parçayı kirletir ve diğer sayfaları bozar; tersi de gereksiz yük bindirir. Ayrım sayesinde düzeltme sadece kendi kutusunda kalır. İhlalde dosya taşınır. Sorumluluk sınırı için yazıldı.

**#9 Token → 01_Abstracts**
> **eli10 (basit):** Değer tanımları her zaman aynı rafta (01 klasörü) durur — hangisinin doğru olduğu belli olsun diye.
> **eli15 (detay):** Token iki yerde olursa iki farklı "doğru" değer çıkar ve hangisinin geçerli olduğu bilinmez (split-brain). Cihaz dosyasına token yığılması da cihazlar arası tutarlılığı bozar. İhlalde token geri 01'e alınır. Tek doğruluk kaynağı (SSOT) için yazıldı.

**#10 `07_Vendors` dokunulmaz**
> **eli10 (basit):** Başkalarının yazdığı kütüphaneye elle dokunulmaz — güncellendiğinde kaybolmasın ve güvenlik yamaları şaşmasın diye.
> **eli15 (detay):** Vendor kodu dışarıdan gelir; içine elle değişiklik yapılırsa bir sonraki güncellemede o değişiklik silinir ve üstüne security patch'i de bozulur. 33 dosya salt okunur olarak durur. İhlalde değişiklik revert edilir. Dış bağımlılığı karantinada tutmak için yazıldı.

### 4.2 Ek Kurallar

| Kural | Detay | Ref |
|-------|-------|-----|
| Katman sırası sabit | `01 → 02 → 03 → 04 → 05 → 06 → 07 → 08 → 09 → 10 → 11` — değiştirilemez | css-template §4.2 |
| `main.css` YOK | Giriş `08_Devices/d-*.css` + `auth-bundled.css`; `main.css`'e import eklenmez | §4.3 hata#5 · §7 geçmiş not |
| `d-auth-4k.css` uydurulmaz | Yalnız `-4k-monitor` / `-4k-tv` | §4.3 hata#6 |
| Yerinde refactor | Dosya adı/yolu değişirse **tüm `@import` + `js/devices.config.js` + `DeviceCssMap.php` + PHP docblock** güncellenir | §4.2 |
| Çelişki kuralı | vault ↔ disk → **disk kazanır** + `⚠️ VERIFICATION REQUIRED` | §4.2 |
| notes.md | Kök `notes.md` CSS notu ilgili token dosyasına uygulanır, satır `✓` imzalanır | §4.2 |
| Emoji yasak | Yalnız `.ai/.png/` PNG görseli kullanılır | Kök AGENTS |

### 4.3 Sık Yapılan Hatalar (css-template §4.3)

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Bileşen içinde sabit `16px` | `var(--space-md)` |
| 2 | `08_Devices/` içine yerleşim yazmak | Yerleşim `02_Base`/`03_Layout` |
| 3 | Component'i `05_Pages`'e yazmak | `04_Components/c-*.css` |
| 4 | Tüm cihaz token'ını tek dosyaya yığmak | §3.3 cihaz ayrımı (base + 6 cihaz) |
| 5 | `main.css`'e import eklemek | Giriş `08_Devices/*` + `auth-bundled.css` |
| 6 | `d-auth-4k.css` uydurmak | Yok; `-4k-monitor`/`-4k-tv` |
| 7 | Mockupsuz CSS üretimi | Mockup Before Frontend |

### 4.4 BEM — Yapılır / Yapılmaz (css-template §3.9)

| Yapılır | Yapılmaz |
|---------|----------|
| `.player__progress` | `.playerProgress` |
| `.player--compact` | `.playerCompact` |
| `.player.is-loading` | `.player.loading` |
| `var(--space-md)` | `16px` |
| `[data-theme]` token override | `.dark .block { }` |
| Cihaz: import + davranış | Cihaz: yerleşim |

---

## 5. Workflow

```
NOTES.MD OKU → MOCKUP OKU → KATMAN SEÇ (§3.1 ayrım tablosu)
→ ŞABLONU KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16 / §4.1 10/10 DOĞRULA
→ TARAYICI TESTİ → COMMIT (subagent ATMAZ)
```

1. **NOTES/MOCKUP:** kök `notes.md` + `.ai/ui-design/` görseli; okunamıyorsa DUR.
2. **KATMAN SEÇ:** §3.1 ayrım tablosu (component mi, sayfa mı, token mı, cihaz davranışı mı?).
3. **ŞABLON (Guardrail #16):** css-template §3.3 token · §3.4 bileşen · §3.5 cihaz · §3.6 auth · §3.7 tema.
4. **Cihaz dosyası ekleme/değişikliği → `js/devices.config.js` + `DeviceCssMap.php` eşzamanlı zorunlu.**
5. **DOĞRULA:** css-template §6 kontrol listesi 10/10 + §4.1 10/10.
6. **TARAYICI TESTİ:** gerçek sayfada eleman/layout doğrulaması.
7. **COMMIT:** subagent atmaz — orkestratöre aittir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: claude` |
| 2 | Bölüm | §1–§7, ≤3 başlık seviyesi |
| 3 | Guardrail | §4.1 tablosu 10/10, numaralar css-template ile eşleşiyor |
| 4 | Placeholder | Üretilen içerikte placeholder sözdizimi kalmadı |
| 5 | Yeni kural | Üretilmedi (yalnız özet + ref) |
| 6 | Vendors | "dokunulmaz" ifadesi §4.1 #10 + §2'de mevcut |
| 7 | Wiki-link | `[[...]]` formatı geçerli |
| 8 | Halüsinasyon | Diskte olmayan dosya/kural iddia edilmedi |
| 9 | **eli10 + eli15 bloğu** | §4.1'deki 10 guardrail maddesinin her birinde `> **eli10 (basit):**` (kural neden var, ≤2 cümle) + `> **eli15 (detay):**` (ihlal/neden yazıldı, 3-4 cümle) bloğu var mı; etiket şablonla aynı mı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| CSS ana şablon (SSOT) | [[../../.ai/.templates/frontend/css-template]] | §4.1 guardrail kaynağı |
| Vault anayasası | [[../../.ai/CLAUDE.md]] | 16 Hard Guardrails |
| Agent registry | [[../../AGENTS.md]] | Routing + Guardrail #16 |
| Klasör envanteri | [[CONTEXT]] | Disk sayıları |
| Rol dokümanı | [[AGENTS]] | Kim ne denetler |
| Süreç dokümanı | [[WORKFLOW]] | Adım akışı |
| Template registry | [[../../.ai/.templates/index]] | Şablon kaydı |
| Mockup indeksi | [[../../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend |
| Erişilebilirlik boşlukları | [[../../.ai/ui-design/04-accessibility-gaps]] | WCAG denetimi |

---

**Version:** 1.0.0 · **Last Updated:** 2026-10-03 · **docType:** claude
