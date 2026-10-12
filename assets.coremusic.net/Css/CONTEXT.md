---
title: "CoreMusic — Css/ Context"
type: docs
category: frontend
version: 2.0.0
status: active
authority: "SSOT (klasör kökü) — çelişkide disk kazanır"
updated: 2026-10-12
docType: context
---

# CoreMusic — Css/ Context

**docType:** context · **Katman:** L3 sunum (ITCSS) · **Sorumlu:** UI Designer (içerik), MO (envanter)

**Zorunlu Bağlantılar:** [[../../.ai/.templates/index]] · [[../../.ai/.templates/frontend/context-template]] · [[../../.ai/.templates/documentation/context-md-template]] · [[../../.ai/.templates/frontend/css-template]] · [[CLAUDE]] · [[AGENTS]] · [[WORKFLOW]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/Css/` klasörünün envanter ve bağlam dosyasıdır: 11 ITCSS katman dizinini, kök giriş dosyalarını, klasör dokümanlarını ve her birinin dosya sayısını tanımlar. Tüm sayılar 2026-10-12 disk ölçümüdür; şablon/vault iddiası ile disk çelişirse **disk kazanır**. Sayı/envanter iddiasının tek tarihli kaynağı burasıdır (css-template §4.2 C4 — şablonlarda sayı yazmaz).

| Karar | Karşılığı |
|-------|-----------|
| Vanilla JS + ITCSS; framework/önişlemci yasak | ADR-001 → [[../../.ai/.templates/frontend/css-template]] §4.1 #1-#4 |
| Cihaz bazlı import zinciri + multi-domain görünüm modu | ADR-045 → css-template §3.5 |
| Cinsiyet bazlı dinamik tema (jeton seviyesinde) | ADR-044 → css-template §3.7 |
| WCAG 2.2 AA — dokunma hedefi tabanı 48px | css-template §4.1 #7 (C2) |

> K-space katman mimarisi referansları (K000–K020, A0–A5, `.ai/architecture/`) bu dosyadan kaldırıldı; ITCSS 01–11 katmanları geçerlidir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/Css/**` — 11 katman dizini + kök dosyalar | JavaScript → `assets.coremusic.net/js/` |
| Katman envanteri: dosya sayıları (2026-10-12 ölçümü) | Mockup görselleri → `.ai/ui-design/` + `.ai/.png/` (okunur, üretilmez) |
| Klasör dokümanı ilişkisi (AGENTS · CLAUDE · WORKFLOW · CONTEXT) | `07_Vendors/` içeriği (salt okunur, kod olarak) |
| Vault ↔ disk çelişki kaydı | PHP sayfa şablonları (`pages/**/*.php`) · CSS kural metni → [[CLAUDE]] |

- **Kullananlar:** UI Designer (birincil), QA Engineer (WCAG/responsive), Backend Architect (inline style denetimi), MO (envanter).
- **Ön koşul (CSS işi):** Mockup Before Frontend — `.ai/ui-design/` görseli okunmadan CSS yazılamaz; okunamıyorsa DUR.
- **Ön koşul (bu dosya):** sayı ölçülmüş olur; ulaşılamayan sayı `UNKNOWN` yazılır, uydurulmaz.

---

## 3. Mimari

### 3.1 Envanter — 5 sütun (2026-10-12 disk ölçümü)

| Katman/Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|--------------|--------------------|---------------|-----------|---------------------|
| `01_Abstracts/` | Tasarım değerinin tek kaynağı — tüm katmanlar buradan token okur | 19 .css: 6 `a-layout-tokens-*` (base + cihaz) + 13 konu token | Hardcoded değer yasak; tema/ölçü değişimi tek noktadan yapılsın | Yeni token eklendiğinde, mockup ölçüsü değiştiğinde |
| `02_Base/` | Bare reset + yapısal iskelet — her sayfanın ortak zemini | 3 .css: b-base-core · l-main-structural · page-layout | Reset tekrarını önlemek; üstüne her şey kurulsun | Global reset/tipografi davranışı değişince |
| `03_Layout/` | Sayfa düzeninin tek evi (header, footer, sidebar, grid) | 4 .css: _header · _footer · _sidebar · _widget-grid | Yerleşim tek yerden yönetilsin; cihaz katmanına yazılmasın | Düzen/mockup ölçüsü değişince |
| `04_Components/` | Tekrar eden görsel parçaların BEM bileşenleri | 15 .css: 12 `c-*` + 3 `_` ( _home-components · _player-info · _welcome-banner ) | ≥2 sayfada ortak parça tek yerde dursun; sayfa dosyaları büyümesin | Bileşen eklendiğinde/değiştiğinde |
| `05_Pages/` | Tek PHP sayfasına özel stiller | 13 .css: 8 `p-*` + 5 `_` ( _home · _home-layout · _home-inline · _player · _welcome ) | Sayfaya özel olan ortak bileşeni kirletmesin | Sayfa tasarımı değişince / yeni sayfa açılınca |
| `06_Utilities/` | Sıfır mantık utility sınıfı (token tüketir, üretmez) | 1 .css: u-helpers-utility | Tek satır işlevler ayrı dursun, kurala dönüşmesin | Utility eklenince/kaldırılınca |
| `07_Vendors/` | 3. taraf Bootstrap ailesi — salt okunur | 33 dosya: 17 .css + 16 .map | Dış kod karantinada kalsın; güvenlik yamaları şaşmasın | Yalnız onaylı sürüm güncellemesinde |
| `08_Devices/` | Cihaz import zinciri + davranış override (yerleşim YAZILMAZ) | 15 .css: 8 `d-*` + 7 `d-auth-*` | Her cihaz tek `<link>` ile açılsın; kırılım/taşma cihaza özelsin | Cihaz/kırılım eklenince → `devices.config.js` + `DeviceCssMap.php` eşzamanlı |
| `09_ViewModes/` | Görünüm modu override (home, pro, studio, car) | 4 .css — iskelet, 0 kural (grep `{` = 0) | Modlar birbirinden bağımsız açılıp kapansın | Mod mockup'ı gelince iskelet doldurulur |
| `10_Helpers/` | Tekrarlanabilir yardımcı desen/makro (kural barındırır) | 1 .css: h-ellipsis — iskelet, 0 kural (grep `{` = 0) | Utility'den (06) ayrılsın; desen ikinci kez yazılmasın | Desen ikinci kez ihtiyaç olunca |
| `11_OAuth/` | OAuth/login akış stilleri | 1 .css: oauth — dolu (47 `{` satırı) | Giriş akışı izole katmanda kalsın | Auth akışı değişince |
| `auth-bundled.css` (kök) | Auth subdomain tek giriş paketi — `07_Vendors` içermez | 1 .css: gruplu `@import` + dosya içi auth kuralları | Giriş tek kapıdan yüklensin; çift yüklenme olmasın | Auth sayfası/token/import eklenince |
| Kök dokümanlar | Klasör kuralı, rolü, süreci ve envanteri | 4 .md: AGENTS · CLAUDE · WORKFLOW.MD · CONTEXT | CSS işine başlayan agent önce buradan başlasın | Ayrı: kural / rol / süreç / ölçüm değişiminde |

**eli10 / eli15 blokları (envanter maddeleri):**

**`01_Abstracts/`**
> **eli10 (basit):** Renk, ölçü, boşluk gibi değerlerin tek sözlüğü — her klasör aynı değeri buradan okur.
> **eli15 (detay):** Ayrı katman çünkü değer tek yerde olunca tema ya da ölçü değişince tek yerden değişir. İçine yalnız token satırları yazılır, seçici yazılmaz. Okunur çünkü bütün site bu isimleri kullanır. Yeni mockup ölçüsü ya da tema jetonu gelince buraya dokunulur.

**`02_Base/`**
> **eli10 (basit):** Tarayıcıların farklı davranıp bozmasını engelleyen ortak başlangıç ayarları.
> **eli15 (detay):** Ayrı katman çünkü temel ayar her sayfayı ilgilendirir, tek sayfaya ait değildir. İçine çıplak HTML ayarları ve yapısal iskelet yazılır. Üstüne her şey kurulduğu için en çok ihtiyaç duyulan katmandır. Tarayıcı davranışı ya da global reset değişince düzenlenir.

**`03_Layout/`**
> **eli10 (basit):** Sayfanın üstteki, alt taraftaki ve kenardaki bölümlerinin yeri.
> **eli15 (detay):** Ayrı katman çünkü başlık, alt bilgi ve yan menü her sayfada aynıdır, tekrar tekrar yazılmaz. İçine yalnız düzen kuralları girer, buton gibi parçalar girmez. Düzeni değiştirmek isteyen tek buraya bakar. Mockup düzeni değişince güncellenir.

**`04_Components/`**
> **eli10 (basit):** Buton, kart, form gibi birden çok yerde kullanılan parçaların kutusu.
> **eli15 (detay):** Ayrı katman çünkü aynı parça birçok sayfada görünür; tek yerde olunca düzeltmek bir kere yeter. İçine parçanın kendisi yazılır, hangi sayfada kullanıldığı değil. Figma bileşen karşılıkları buradadır. Parça tasarımı değişince ya da yeni bileşen gelince düzenlenir.

**`05_Pages/`**
> **eli10 (basit):** Yalnızca tek bir sayfaya özel süslemeler.
> **eli15 (detay):** Ayrı katman çünkü sayfaya özel şey ortak bileşene girerse diğer sayfalara da yayılır. İçine o tek sayfanın kuralı yazılır. PHP sayfa dosyası değişince burası güncellenir. Yeni sayfa açılınca dosyası buraya eklenir.

**`06_Utilities/`**
> **eli10 (basit):** Tek işi olan küçük yardımcı etiketler, örneğin "bunu gizle".
> **eli15 (detay):** Ayrı katman çünkü bu etiketler akıl yürütmek yerine sadece tek bir iş yapar. İçine sıfır mantık sınıf yazılır, kural barındırmaz. Token tüketir, token üretmez. Utility eklenince ya da kullanımdan kalkınca düzenlenir.

**`07_Vendors/`**
> **eli10 (basit):** Başkalarının yazdığı kütüphane — bizim yazmadığımız, karışmamamız gereken kısım.
> **eli15 (detay):** Ayrı klasör karantina gibidir: dışarıdan geldiği için içine elle giriş yapılmaz, yoksa güncellemede kaybolur. Grid ve temel sıfırlama ihtiyacı buradan gelir. Bootstrap ailesi ve harita (.map) dosyaları burada durur. Yalnız onaylı sürüm yükseltmesinde değişir.

**`08_Devices/`**
> **eli10 (basit):** Telefon, televizyon, bilgisayar… her ekranın kendi ayar dosyası.
> **eli15 (detay):** Ayrı dosyalar çünkü ekran ve dokunma farklılıkları cihaza özeldir; hepsi tek dosyada olsaydı telefon ayarı bilgisayarı bozardı. İçine önce gerekli dosyaların bağlantısı, sonra o cihazın davranışı yazılır; yerleşim yazılmaz. Yeni cihaz ya da kırılım eklenince `js/devices.config.js` ve `shared/src/Device/DeviceCssMap.php` ile birlikte düzenlenir.

**`09_ViewModes/`**
> **eli10 (basit):** Pro, stüdyo, araba gibi farklı görünüm modlarının kendi odası.
> **eli15 (detay):** Ayrı katman çünkü modlar birbirinden bağımsız açılıp kapanır. İçine modun kendi override kuralları yazılır, yerleşim yazılmaz. Şu an dört dosya da boştur (içerik `UNKNOWN`); kural uydurulmaz. Mod mockup'ı gelince iskelet doldurulur.

**`10_Helpers/`**
> **eli10 (basit):** Tekrar eden küçük numaraların kutusu, örneğin uzun yazıyı üç noktayla kesme.
> **eli15 (detay):** Ayrı katman çünkü bu yardım deseni tek sınıftan büyüktür ve kendi içinde kural barındırır. Utility katmanı (06) ile karışmasın diye ayrı durur. İçine yalnız tekrarlanabilir desen yazılır. Aynı numara ikinci kez lazım olunca buraya yazılır.

**`11_OAuth/`**
> **eli10 (basit):** Giriş ve üyelik ekranlarının görünüm kuralları.
> **eli15 (detay):** Ayrı katman çünkü giriş akışı ayrı bir adreste çalışır ve izole kalmalı. İçine giriş akışının stilleri yazılır. Auth cihaz ayarları ayrı dosyada (`d-auth-*`) olduğundan tekrar oluşmaz. Giriş akışı değişince düzenlenir.

**Kök `auth-bundled.css`**
> **eli10 (basit):** Giriş sayfalarının hepsini tek parça halinde yükleyen bağlantı dosyası.
> **eli15 (detay):** Ayrı dosya çünkü giriş adresi tek kapıdan beslenir, parça parça yükleme olmasın diye. İçine sırayla bağlantılar ve dosya içi auth kuralları yazılır; kütüphane (vendor) içermez. Giriş sayfası eklenince ya da sıra değişince düzenlenir.

**Kök dokümanlar (`AGENTS.md` · `CLAUDE.md` · `WORKFLOW.MD` · `CONTEXT.md`)**
> **eli10 (basit):** Klasörün kural, rol, süreç ve envanter kitapçıkları.
> **eli15 (detay):** Ayrı dosyalar çünkü kural, rol ve süreç birbirinden farklı sorumluluk taşır; tek dosyada birleşince kim ne yapar kaybolur. CSS işine başlayan agent sırayla bunlardan başlar. Kural değişince CLAUDE, rol değişince AGENTS, süreç değişince WORKFLOW güncellenir. Envanter yalnız ölçümden değişir — o iş bu dosyanındır.

### 3.2 Ölçüm Özeti ve Çelişki Kaydı (disk kazanır)

- **Ölçüm (2026-10-12):** 11 katman dizini · katman içi 109 dosya (93 .css + 16 .map) · kök 5 dosya (1 .css + 4 .md) · genel toplam 114 dosya / 94 .css · katmanlarda alt dizin yok (11/11).
- **Çelişkiler (önceki belge/şablon notu ↔ disk — disk kazanır):**
  - `05_Pages/`: önceki bu dosyada 12 → disk **13** (`p-history.css` eklendi).
  - `01_Abstracts/`: önceki §3.1.1 "20 dosya (7 layout)" iddiası → disk **19** (6 layout token + 13 konu token).
  - `main.css` · `a-layout-tokens.css` · `d-auth-4k.css` · `a-layout-tokens copy*.css`: diskte **YOK** (uydurulmaz; giriş `08_Devices/d-*.css` + `auth-bundled.css`).
  - css-template §R notundaki eski envanter (01=20, 04=6, 05=4) bayattır → güncel ölçüm **19 / 15 / 13**.

### 3.3 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök AGENTS.md + kök CLAUDE.md
  → assets.coremusic.net/AGENTS.md (asset kuralları + Css/ envanteri)
    → Css/CONTEXT.md (envanter — bu dosya) · Css/CLAUDE.md (kural) · Css/AGENTS.md (rol) · Css/WORKFLOW.MD (süreç)
      → ihtiyaç anında: .ai/.templates/frontend/css-template.md (Guardrail #16) · .ai/ui-design/ (mockup)
```

| Klasör (kök) dosyası | Pointer olduğu vault/şablon kaynağı | Ne taşır |
|----------------------|-------------------------------------|----------|
| `Css/AGENTS.md` | [[../../.ai/AGENTS.md]] §6 (authority satırı) | Rol/routing daraltması |
| `Css/CLAUDE.md` | [[../../.ai/.templates/frontend/css-template]] §4.1 (authority satırı) | 10 hard guardrail özeti — yeni kural üretmez |
| `Css/WORKFLOW.MD` | [[../../.ai/.templates/frontend/css-template]] §5 (authority satırı) | Adım akışı + commit kapısı |
| `Css/CONTEXT.md` (bu dosya) | Doğrudan disk ölçümü — vault pointer'ı yok | Envanter + çelişki kaydı |

### 3.4 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ölçü/renk `01_Abstracts`'te tek yerde durur | Her dosyada ayrı `16px` → tutarsız görünüm, ölçü değişince dosya dosya tarama |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz | Her cihaz farkı tüm dosyalara yayılır → 8+ yerde tekrar, drift |
| 5 | **Vendor karantinası** | 3. taraf kod `07_Vendors`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: değerler tek sözlükte, düzen tek evde, parça tek kutuda durur. Böylece bir yeri değiştirince sadece o değişir, diğerleri bozulmaz. Cihaz ayarları ayrı olunca telefon ayarı masaüstünü bozmaz, hazır kütüphane ayrı olunca güncelleme bizim kodumuzu bozmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

Kural numaralarının bağlayıcı kaynağı: [[CLAUDE]] §4.1 (= css-template §4.1, 10 madde; ihlal = revert).

| # | Kural (özet) | Ref |
|---|--------------|-----|
| 1 | Hardcoded piksel/değer yasak — her değer `var(--...)` (01 hariç) | css-template §4.1 #1 |
| 2 | `!important` yasak — en fazla 3 istisna, gerekçe yorumda | §4.1 #2 |
| 3 | Inline `style=""` yasak (CSP nonce uyumsuz) | §4.1 #3 |
| 4 | Önişlemci (SCSS/LESS) yasak — saf CSS (ADR-001) | §4.1 #4 |
| 5 | Cihaz katmanı yalnız import + davranış; yerleşim `02_Base`/`03_Layout` | §4.1 #5 |
| 6 | BEM zorunlu: `.block__element--modifier` + `.is-*` | §4.1 #6 |
| 7 | WCAG 2.2 AA: dokunma hedefi `--touch-min: 48px`; mockup okunmadan CSS yok | §4.1 #7 |
| 8 | Bileşen → `04`, PHP sayfası → `05` ayrımı | §4.1 #8 |
| 9 | Yeni token yalnız `01_Abstracts`; `08_Devices` yalnız mevcut token value override | §4.1 #9 (C6) |
| 10 | `07_Vendors/` salt okunur | §4.1 #10 |

- **Çelişki kuralı:** vault ↔ disk çelişirse **disk kazanır** + `VERIFICATION REQUIRED` işaretlenir; bilinmeyen değer `UNKNOWN` yazılır.
- **Sayı kuralı:** bu dosyadaki her sayı ölçümdür; yeni ölçüm eski sayıyı geçersiz kılar (2026-10-12).
- **Cihaz dosyası ekleme/değişikliği:** `js/devices.config.js` + `shared/src/Device/DeviceCssMap.php` aynı görevde güncellenir.
- **`notes.md`:** CSS notu ilgili dosyaya uygulanır, satır imzalanır.
- **Commit:** subagent atmaz — orkestratöre aittir.

---

## 5. Workflow

```text
DISK ÖLÇ (11 katman + kök) → ŞABLON KONTROL (context-template §1-§7)
→ ENVANTER TABLOSU (5 sütun, ölçülen sayılarla) → eli10/eli15 BLOKLARI
→ ÇELİŞKİ KAYDI (disk kazanır) → GUARDRAIL #16 DOĞRULA
→ .ai/.templates/index.md'E 1 SATIR EKLE → log.md'ye KAYDET → COMMIT (subagent ATMAZ)
```

1. **ÖLÇ:** katman ve kök dosyaları sayılır; sayı yoksa `UNKNOWN`.
2. **YAZ:** §1–§7 iskeleti (context-template §3.4) + 5 sütunlu envanter + her madde için eli10/eli15.
3. **DOĞRULA:** §6 kontrol tablosu; yer tutucu dizgesi ve emoji kalmadığına bakılır.
4. **INDEX + LOG:** `.ai/.templates/index.md`'e 1 yeni satır (mevcut satırlara dokunulmaz) · `.ai/log.md` append-only.
5. **COMMIT:** subagent atmaz — orkestratöre aittir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, aynı başlık, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada yer tutucu dizgesi kalmadı |
| 4 | Emoji | Sıfır emoji (yalnız `[[...]]`/dosya yolu referansı) |
| 5 | Envanter | 5 sütun; sayılar 2026-10-12 ölçümü ile eşit: 19/3/4/15/13/1/33/15/4/1/1 + kök 1 .css + 4 .md |
| 6 | eli10 + eli15 | Her envanter maddesinde iki blok var; etiket/sıra şablonla birebir (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 7 | İçerik neden ayrı | §3.4 tablosu tam 5 satır + bölüm bloğu |
| 8 | Wiki-link | `[[...]]` formatı; her hedef diskte mevcut |
| 9 | K-space | K000–K020 / A0–A5 / `.ai/architecture/` referansı yok; ITCSS 01–11 duruyor |
| 10 | Çelişki | §3.2 disk kazanır kaydı güncel; uydurulmuş dosya/sayı yok |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[../../.ai/.templates/index]] | Şablon envanteri + kayıt satırı |
| Bağlayıcı ortak iskelet | [[../../.ai/.templates/frontend/context-template]] | §1–§7 + eli10/eli15 (çelişkide o kazanır) |
| CONTEXT üretim şablonu | [[../../.ai/.templates/documentation/context-md-template]] | Envanter tablosu + sayım disiplini |
| CSS ana şablon | [[../../.ai/.templates/frontend/css-template]] | 11 katman sırası, §4.1 guardrail, §3.5/§3.7 |
| Vault anayasası | [[../../.ai/CLAUDE.md]] | Guardrail #3 (zero-hallucination) / #16 (şablon) |
| Agent registry (SSOT) | [[../../AGENTS.md]] | Routing: CSS → UI Designer |
| Klasör rol dosyası | [[AGENTS]] | Css/ rol matrisi + handover |
| Klasör kural dosyası | [[CLAUDE]] | 10 hard guardrail özeti |
| Klasör süreç dosyası | [[WORKFLOW]] | Adım akışı + cihaz eşzamanlılığı |
| Mockup indeksi | [[../../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend kapısı |
| CSS notları | [[../../notes]] | Uygulanacak notlar (satır imzası) |
| Disk kanıtı | `assets.coremusic.net/Css/` | Bu dokümandaki tüm sayılar (2026-10-12) |

---

**docType:** context · **Version:** 2.0.0 · **Last Updated:** 2026-10-12
