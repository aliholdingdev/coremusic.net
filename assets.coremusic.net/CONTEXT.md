---
title: "CoreMusic — assets.coremusic.net Klasör Context"
type: docs
category: frontend
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# assets.coremusic.net — CONTEXT.md

**docType:** context · **Klasör:** `assets.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/` klasörünün **ne işe yaradığını** ve diskteki **gerçek** envanterini tanımlar: tüm subdomainlerin **statik asset servisi** (Css · js · Fonts · Image + test altyapısı) — ADR-001 (Vanilla JS + ITCSS) mimarisinin tek uygulama noktası. Kapsam: assets = **statik asset servisi**; PHP sayfa sunumu yapmaz. Tüm sayılar 2026-10-03 disk ölçümüdür; `AGENTS.md`/`Css/CONTEXT.md` iddiaları ile disk arasındaki farklar §3.6'da işaretlenmiştir (çelişkide **disk kazanır**).

| Karar | Kaynak (disk) |
|-------|---------------|
| Statik servis kuralları: CORS + cache | `.htaccess` başlık yorumu (font/image/css-js `Access-Control-Allow-Origin`, `max-age=3600`) |
| CSS girişi: `Css/08_Devices/d-*.css` + `Css/auth-bundled.css`; **`main.css` YOK** | `Css/` kök dosya listesi |
| Alt klasör dokümanları: `Css/CONTEXT.md`, `Css/WORKFLOW.MD`, 8× `js/**/CLAUDE.md` | diskte VAR (16 `.md` toplam) |
| Test altyapısı: Vitest + Playwright | `vitest.config.js`, `playwright.config.ts`, `tests/` (5 dosya) |
| Klasör kuralları mevcut `CLAUDE.md` + `AGENTS.md` içinde | bu oturumda okundu, **DEĞİŞTİRİLMEDİ** |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/` kök + `Css/`, `js/`, `Fonts/`, `Image/`, `tests/` envanteri (özet) | `Css/` katman içi detay → [[Css/CONTEXT.md]] (klasör-içi SSOT) |
| Asset servis davranışı (CORS/cache/MIME) ve komşu ilişkileri | `home/` `auth/` `api/` PHP sayfa sunumu |
| Vault ↔ disk çelişki kaydı (§3.6) | Mockup görselleri → `.ai/ui-design/` + `.ai/.png/` (okunur, üretilmez) |
| Yeni CONTEXT/WORKFLOW iskeleti | Mevcut `CLAUDE.md` / `AGENTS.md` / `Css/*` dokümanları (dokunulmaz) |

- **Kullananlar:** UI Designer (birincil — CSS/JS), Security Engineer (CSP/vendor), QA Engineer (`tests/`, Vitest/Playwright), MO (doküman).
- **Ön koşul:** Mockup Before Frontend — UI kodundan önce `.ai/ui-design/01-mockup-index.md` okunur; sayısal iddialar disk ölçümünden.
- **Not:** `Css/07_Vendors/` salt okunur; Font/Image dosyaları onaysız silinmez/adı değiştirilmez (referans kırılır).

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1 — disk ölçümü 2026-10-03)

**Sayı gerçeği:** toplam **467 dosya** (vendor yok) · uzantı dağılımı: `.png 130` · `.ttf 105` · `.js 101` · `.css 95` · `.md 16` · `.map 16` · `.ts 2` · `.htaccess 1` · `.config 1` (web.config).

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `Css/` | Tüm subdomainlerin ITCSS stilleri | 115 dosya: 95 `.css` + 16 `.map` + 4 `.md` (11 katman + kök) | Stil tek yerden servis edilsin | Katman/mockup değişince (detay: [[Css/CONTEXT.md]]) | Sayfaların görünüm kurallarının bulunduğu klasör. | Ayrıdır çünkü ADR-001 gereği sunum kodu tek uygulama noktasında toplanır. İçine token, düzen, bileşen ve cihaz girişi girer. Okunması import zincirini gösterir. Katman içi her sayı için `Css/CONTEXT.md` (klasör-içi SSOT) esastır. |
| `js/` | Vanilla ES6+ modülleri | 106 dosya: 98 `.js` + 8 `CLAUDE.md` (kök 5 · auth 2 · components 36 · core 4 · coreplayer 5 · features 6 · managers 5 · router 35) | Davranış tek yerde | Modül davranışı değişince | Sayfaların davranış kodunun bulunduğu klasör. | Ayrıdır çünkü davranış ile stil ve sunum farklı hızda değişir. İçine cihaz yükleyici, router, yöneticiler ve özellik modülleri girer. `eval`/`var`/`innerHTML` yasakları (AGENTS §4) burada geçerlidir. Klasör içi `CLAUDE.md`ler o modülün yerel notudur. |
| `Fonts/` | Yazı tipleri | 105 `.ttf` (DMSans, PlusJakartaSans, PlayfairDisplay, Respective ağırlıklı) | Yazı tipi servisi | Yeni yazı tipi eklendiğinde (onaylı) | Sayfaların harfleri. | Ayrıdır çünkü yazı tipi binary dosyadır ve cross-origin servis edilir (`.htaccess` CORS). Silinirse/adı değişirse `@font-face` kırılır. Onaysız değişiklik yasak. |
| `Image/` | Görseller | 130 `.png`: `background/` 8 · `profiles/` 1 · `res-pink/` 121 | Görsel varlıkların tek kütüphanesi | Yeni görsel/kaldırma (onaylı) | Sayfaların resimleri. | Ayrıdır çünkü görsel binary ve referansla bağlanır; taşınırsa kırık görsel doğar. `res-pink/` ana ikon kütüphanesidir. Onaysız silme/rename yasak. |
| `tests/` | Frontend testleri | 5 dosya: `components/player-info.spec.js`, `mocks/player-api.mock.js`, `e2e/player-info.spec.ts`, `README.md`, `QUICKSTART.md` | JS davranışı ve uç senaryosu sınanır | JS değişince (QA) | Kodun doğruluğunu kanıtlayan dosyalar. | Ayrıdır ki test, kod değişince yalnız doğrulasın. İçine birim (Vitest) + uç (Playwright) senaryosu, mock ve dokümantasyon girir. Yeni özellik = yeni test. Kapı yeşil olmadan iş bitmez. |
| Kök yapılandırma | Test/CI ve statik servis ayarları | 6 dosya: `.htaccess`, `web.config`, `vitest.config.js`, `playwright.config.ts`, `AGENTS.md`, `CLAUDE.md` (+ yeni `CONTEXT.md`, `WORKFLOW.md`) | Test tek komutla; statik servis kuralları tek yerde | Test/sunucu kuralı değişince | Sınav ayarları ve iki sunucunun yönlendirme kuralları. | Ayrıdırlar çünkü test/sunucu kuralı koddan farklı hızda değişir. Apache ile IIS ayrı dosyada okunur. `.htaccess` CORS/cache kuralının kaynağıdır. |

**eli10 / eli15 blokları (§3.1'deki 6 maddenin karşılığı):**

**`Css/`**
> **eli10 (basit):** Tüm sitenin görünüm kurallarının durduğu klasör.
> **eli15 (detay):** Ayrıdır çünkü stil tek uygulama noktasında toplanır (ADR-001). İçine 11 katman + tek giriş dosyası girer. Okunması import zincirini gösterir. Katman içi sayı ve çelişki kaydı için `Css/CONTEXT.md` okunur; o dosya bu konunun klasör içi SSOT'udur. Değişiklik katman kuralına göre yapılır.

**`js/`**
> **eli10 (basit):** Sayfaların davranışını yazan kod.
> **eli15 (detay):** Ayrıdır çünkü davranış, stil ve sunumdan farklı hızda değişir. İçine router, yöneticiler, özellikler ve cihaz yükleyicisi girer. Yasaklar: `eval`, `var`, `innerHTML`. Klasör içi `CLAUDE.md`ler yerel notlardır. Değişince test (Vitest/Playwright) kapıdan geçer.

**`Fonts/`**
> **eli10 (basit):** Sayfalarda kullanılan yazı tipleri.
> **eli15 (detay):** Ayrıdır çünkü binary dosyadır ve cross-origin sunulur. Silinirse/adı değişirse yazı tipi kırılır. Onaysız değişiklik yasak. Yeni tip yalnız onayla eklenir.

**`Image/`**
> **eli10 (basit):** Arka plan, avatar ve ikonların bulunduğu resim kütüphanesi.
> **eli15 (detay):** Ayrıdır çünkü görsel referansla bağlanır; taşınırsa kırık görsel doğar. `res-pink/` ana kütüphanedir. Onaysız silme/rename yasak. Yeni görsel onayla eklenir.

**`tests/`**
> **eli10 (basit):** JavaScript'in doğru çalıştığını kanıtlayan dosyalar.
> **eli15 (detay):** Ayrıdır ki test, kod değişince yalnız doğrulasın. İçine birim senaryo, uç senaryo, sahte veri (mock) ve doküman girir. Yeni özellik = yeni test. Kapı yeşil olmadan iş bitmez.

**Kök yapılandırma + kök dokümanlar**
> **eli10 (basit):** Test ayarları, sunucu yönlendirmesi ve klasör kâğıtları.
> **eli15 (detay):** Ayrıdırlar çünkü ayar/kural, koddan uzun yaşar. Apache ile IIS ayrı dosyada okunur. `.htaccess` CORS ve cache kuralının kaynağıdır. Mevcut dokümanlar bu üretimde yalnız okundu, değiştirilmedi.

**`Css/` klasör dokümanları (kök + `Css/CONTEXT.md`)**
> **eli10 (basit):** Katmanların kendi kâğıtları da o klasörün içinde durur.
> **eli15 (detay):** `Css/CONTEXT.md` katman envanterinin klasör içi SSOT'udur; bu dosya yalnız özet verir. `Css/WORKFLOW.MD` katman üretim akışını taşır (uzantı büyük harf — disk gerçeği). Sayı iki yerde farklıysa `Css/CONTEXT.md` kazanır, fark §3.6'ya yazılır. Mevcut dokümanlar bu üretimde değiştirilmedi.

### 3.1.1 Ölçüm Notu (2026-10-03)

- **Yöntem:** sayımlar `Get-ChildItem -Recurse -File` ile yapıldı; `vendor/` yoktur; `vendor hariç` ayrımı gerekmemiştir.
- **Uzantı kırılımı toplamı:** 130 + 105 + 101 + 95 + 16 + 16 + 2 + 1 + 1 = **467** (toplam dosya ile eşit).
- **`.js` 101 =** `js/` 98 + kök `vitest.config.js` 1 + `tests/` 2 · **`.ts` 2 =** kök `playwright.config.ts` 1 + `tests/e2e/` 1.
- **`.md` 16 =** kök 2 + `Css/` 4 + `js/` 8 + `tests/` 2 — hepsi klasör dokümanı veya test dokümanıdır.
- **Yeni üretilen 2 dosya** (`CONTEXT.md`, `WORKFLOW.md`) bu ölçüm sonrasındadır; sonraki ölçümde `.md` toplamı **18** olacaktır.

> **eli10 (basit):** Bu sayılar bugün tek tek sayıldı; toplamlar birbirini tutuyor ve uydurma yok.
> **eli15 (detay):** Ölçüm notu, §3.1'deki her sayının nasıl geldiğini açıkça gösterir (hangi uzantı nereden). Böylece ilerideki ölçüm eskiyle karşılaştırılabilir. Yeni dosya üretimi sayıları nasıl etkileyeceği önceden yazılır. Tahmin edilen tek değer yok; hepsi sayım sonucudur.

### 3.2 `Css/` Katman Özeti (özet — detay `Css/CONTEXT.md`)

| Katman | Dosya (disk) | Not |
|--------|--------------|-----|
| `01_Abstracts/` | **21** | token (19 + 2 `copy` yedeği) |
| `02_Base/` | **3** | reset + iskelet |
| `03_Layout/` | **4** | header/footer/sidebar/widget-grid |
| `04_Components/` | **15** | 6 bilinen + 9 `c-*` dosyası **şimdi diskte VAR** (§3.6) |
| `05_Pages/` | **12** | 4 bilinen + 8 dosya **şimdi diskte VAR** (§3.6) |
| `06_Utilities/` | **1** | u-helpers-utility |
| `07_Vendors/` | **33** (17 `.css` + 16 `.map`) | Bootstrap 5.3.8 ailesi — salt okunur |
| `08_Devices/` | **15** | 8 normal + 7 `d-auth-*` |
| `09_ViewModes/` | **4** | iskelet |
| `10_Helpers/` | **1** | iskelet |
| `11_OAuth/` | **1** | oauth.css |
| kök | **1 `.css` + 4 `.md`** | `auth-bundled.css` + AGENTS/CLAUDE/CONTEXT/`WORKFLOW.MD` |

**eli10 / eli15 (§3.2 bloğu):**

> **eli10 (basit):** CSS klasörünün katman katman dökümü — sayılar bugünün disk ölçümü.
> **eli15 (detay):** Özet burada, ayrıntı `Css/CONTEXT.md`'dedir (iki klasör-içi dosya SSOT'ur; bu dosya ikincil kaynaktır). Sayılar değişince önce `Css/CONTEXT.md`, sonra bu tablo güncellenir. İkisi çelişirse `Css/CONTEXT.md` kazanır, çelişki §3.6'ya yazılır. Okunması katman sorumluluğunu baştan gösterir.

### 3.3 `js/` Alt Dizin Dağılımı (disk ölçümü)

| Alt dizin | `.js` | `CLAUDE.md` | İçerik (AGENTS iddiası ile) |
|-----------|-------|-------------|------------------------------|
| kök | 5 | 1 | `main.js`, `device-loader.js`, `device-layout-updater.js`, `devices.config.js`, `oauth-manager.js` — AGENTS listesi ile **uyumlu** |
| `auth/` | 2 | 1 | auth-gender-bg, gender-select — **uyumlu** |
| `components/` | 36 | 0 | AGENTS envanterinde **listelenmiyor** (§3.6) |
| `core/` | 4 | 1 | CoreMusicApp, EventBus, footer.init, helper — **uyumlu** |
| `coreplayer/` | 5 | 1 | AGENTS envanterinde **listelenmiyor** (§3.6) |
| `features/` | 6 | 1 | AGENTS 5 isim sayar — **7 dosya (6 js + 1 md)** (§3.6) |
| `managers/` | 5 | 1 | DeviceManager, ScaleManager, SidebarManager, ThemeManager, ViewModeManager — **uyumlu** |
| `router/` | 35 | 2 | AGENTS "28 dosya" der — **37 dosya (35 js + 2 md)** (§3.6) |

**eli10 / eli15 (§3.3 bloğu):**

> **eli10 (basit):** JavaScript klasörünün oda oda dökümü — sayılar bugünün ölçümü.
> **eli15 (detay):** Tablo, dosya sayısını uzantıya göre ayırır (`.md` ayrı sayılır). AGENTS ile uyuşmayan satırlar §3.6'da işaretlenir; disk kazanır. Klasör içi `CLAUDE.md`ler o modülün yerel notudur. Okunması hangi modülün nerede olduğunu baştan gösterir.

### 3.4 Komşu İlişkileri (assets ↔ sistem)

| Komşu | Yön | Ne taşır (disk kanıtı) | eli10 | eli15 |
|-------|-----|------------------------|-------|-------|
| `../home.coremusic.net` | assets ← home | home sayfaları `Css/` + `js/` çağırır (komşuluk; `<link>`/`<script>` satırları home kodunda bu görevde okunmadı: VERIFICATION REQUIRED) | Görünüm buradan gider. | Asset yolu değişirse home bozulur. Yol/ekleme `devices.config.js` + `DeviceCssMap.php` üzerinden eşzamanlı değişir. |
| `../auth.coremusic.net` | assets ← auth | `Css/auth-bundled.css` (tek auth girişi) + `Css/08_Devices/d-auth-*.css` (7) + `js/auth/` (2) | Giriş sayfalarının görünümü buradan. | Auth CSS/JS üretmez, yalnız çağırır. `d-auth-*` dosyalarında import yoktur (çift yüklenme olmasın). |
| `../api.coremusic.net` | — | api JSON döndürür; composer bağımlılığı yok (api CONTEXT §3.3) | Ayrık çalışırlar. | Doğrudan kod bağımlılığı iddia edilmez; ortak nokta yalnız UI'ın API'yi çağırmış olmasıdır. |
| `../shared` | assets → shared | `shared/src/Device/DeviceCssMap.php`, `DeviceRenderer.php` cihaz → css yolu eşlemesi | Cihaz haritası ortak katmanda. | Cihaz dosyası ekleyince harita dosyalarıyla **eşzamanlı** değişir (AGENTS §4). Tek başına değişirse çift yüklenme/eksik yüklenme doğar. |
| `../.ai` | assets → vault | `[[../.ai/ui-design/01-mockup-index]]` (VAR), `css-template` (VAR), `js-template` (VAR), `Css/CONTEXT.md` (VAR) | Kural, şablon ve görsel vault'tan. | Mockup okunmadan UI kodu yazılmaz. Şablonsuz `.css`/`.js` dosyası üretilmez (Guardrail #16). |

**eli10 / eli15 blokları (§3.4 — 5 komşu):**

**home**
> **eli10 (basit):** Ana sayfa bu klasörün dosyalarını kullanır.
> **eli15 (detay):** Ayrıdır çünkü görünüm üretimi assets'tedir. Yol değişirse home bozulur. Cihaz ekleme harita dosyalarıyla eşzamanlıdır. Çağrı satırları bu görevde okunmadığı için komşuluk olarak yazılır.

**auth**
> **eli10 (basit):** Giriş sayfalarının ayrı bir girişi vardır.
> **eli15 (detay):** `auth-bundled.css` tek kapıdır; `d-auth-*` import taşımaz. Böylece çift yüklenme olmaz. Auth CSS/JS üretmez. Değişiklik yalnız bu klasörde.

**api**
> **eli10 (basit):** Doğrudan bağ yok — ayrı çalışırlar.
> **eli15 (detay):** api JSON döndürür, statik servisle bağımlılığı composer'da yoktur. Kod bağımlılığı iddia edilmez. Ortak nokta yalnız UI'ın çağrısıdır.

**shared**
> **eli10 (basit):** Cihaz → dosya yolu eşlemesi ortak katmanda.
> **eli15 (detay):** Harita (`DeviceCssMap`) shared'dedir. Cihaz ekleyince iki yer birden değişir. Tek başına değişirse yükleme bozulur. Ortak kod assets'e kopyalanmaz.

**vault**
> **eli10 (basit):** Mockup, şablon ve kural vault'ta.
> **eli15 (detay):** Mockup okunmadan UI kodu yazılmaz. Şablonsuz dosya üretilmez. `Css/CONTEXT.md` katman SSOT'u olarak okunur. Kararlar ADR ile bağlanır.

### 3.5 `tests/` ve Test Yapılandırması (disk ölçümü)

| Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-------|--------------------|---------------|-----------|---------------------|
| `vitest.config.js` | Birim test kurulumu (Vitest) | Vite tabanlı ayar | JS testi tek komutla koşsun | Test kapsamı değişince |
| `playwright.config.ts` | Uç test kurulumu (Playwright) | Tarayıcı senaryo ayarı | Gerçek tarayıcıda uç testi | E2E senaryosu değişince |
| `tests/components/player-info.spec.js` | Bileşen birim testi | player-info senaryosu | Bileşen davranışı sabit | Bileşen değişince |
| `tests/mocks/player-api.mock.js` | Sahte API yanıtı | mock veri | Test gerçek servise bağlanmasın | API sözleşmesi değişince |
| `tests/e2e/player-info.spec.ts` | Uç senaryo (TS) | Playwright senaryosu | Kullanıcı akışı sınanır | Akış değişince |
| `tests/README.md`, `tests/QUICKSTART.md` | Test dokümanı | Çalıştırma talimatı | Test koşma yolu anlatılsın | Komut değişince |

**eli10 / eli15 (§3.5 bloğu):**

> **eli10 (basit):** Test ayarları, sahte veri ve iki test dosyası — JS'in doğruluğunu kanıtlar.
> **eli15 (detay):** Ayrıdır ki test, kod değişince yalnız doğrulasın; sahte veri (mock) gerçek servise dokunmaz. Birim test hızlı, uç test gerçek tarayıcıda yürür. İçine ayar, senaryo, mock ve doküman girer. Yeni özellik eklenince test de eklenir. Dosya adları `player-info` eksenindedir; kapsamın genişliği `UNKNOWN` (tüm modüller test edilmedi).

### 3.6 Vault ↔ Disk Çelişkileri (disk kazanır; uydurulmaz)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `assets/AGENTS.md` §2: `Css/main.css` | **YOK** (Css kökünde yalnız `auth-bundled.css`) | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §2: `Css/01_Abstracts/` = 10 token dosyası | **21 dosya** (19 token + 2 `copy`) | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §2: `Css/04_Components/` = 3 dosya | **15 dosya** | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §2: `Css/08_Devices/` = 13 device CSS | **15 dosya** (8 normal + 7 auth) | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §2: `js/router/` = 28 dosya | **37 dosya** (35 `.js` + 2 `CLAUDE.md`) | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §2: `js/components/`, `js/coreplayer/` listelenmiyor | **36** ve **6 dosya** diskte var | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §5: `js-module-architecture.md` yolu | Dosya kendisi "DEAD" notu taşıyor (faz6-D) | ⚠️ VERIFICATION REQUIRED |
| `Css/CONTEXT.md` §3.1: `01=20`, `04=6`, `05=4` | **21 / 15 / 12** (ölçüm sonrası dosya eklenmiş/geri gelmiş) | ⚠️ VERIFICATION REQUIRED |
| `Css/CONTEXT.md` §3.2: `copy` yedekleri `01_Abstracts/` içinde YOK | `a-layout-tokens copy.css` + `copy 2.css` **VAR** | ⚠️ VERIFICATION REQUIRED |
| `Css/CONTEXT.md` §3.4: `c-buttons` … `c-toggle` (9) + `p-*`/`_home-*` (8) "diskte YOK" | **hepsi diskte VAR** (04/05 içinde) | ⚠️ VERIFICATION REQUIRED |
| `assets/AGENTS.md` §2: `Fonts/` = 105 | **105 `.ttf`** — **uyumlu** | doğrulandı |
| `assets/AGENTS.md` §2: `js/` kök 5 + `auth/` 2 + `managers/` 5 | **uyumlu** (dosya adları dahil) | doğrulandı |

**eli10 / eli15 (§3.6 bloğu):**

> **eli10 (basit):** Eski kâğıtlardaki sayılar ve dosya listeleri bugünkü diskle uyuşmuyor; her zaman disk kazanır.
> **eli15 (detay):** Bu tablo yalnız kanıtlanmış farkları taşır: dosya sayıları, eksik listeler ve `Css/CONTEXT.md` tarihsel ölçümü. İçine tahmin girmez. Okunması, eski sayıya güvenmeyi engeller. Fark bulunca vault tarafı düzeltilir; disk değiştirilmez, dosya uydurulmaz. Yeni ölçüm eski sayıyı geçersiz kılar.

### 3.7 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Her dosyanın tek işi | Dosya büyümez, sahibi belli kalır |
| 3 | **Token tek kaynak** | Ölçü/renk `01_Abstracts`'te tek yerde | Her dosyada ayrı değer → tutarsız görünüm |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | `07_Vendors/` + `vendor/` ayrı | Güncelleme bizim kodu bozar |

> **eli10 (basit):** Bilgiler küçük kutulara bölünmüş; bir kutuyu değiştirmek diğerine zarar vermesin diye.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: stil `Css/`'te, davranış `js/`'de, harf `Fonts/`'ta, resim `Image/`'de, soru `tests/`'te durur. Böylece bir yer değişince sadece o değişir. Cihaz ayarları ayrı olunca telefon ayarı masaüstünü bozmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

Klasör kuralı kaynakları (bu üretimde okundu, **değiştirilmedi**): [[CLAUDE.md]] + [[AGENTS.md]] (ve `Css/AGENTS.md`, `Css/CLAUDE.md`).

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | Framework/jQuery/React/Vue yasak — Vanilla JS + ITCSS | ADR-001 bağımlılık/hacim | assets `AGENTS.md` §4 Yasak 1 |
| 2 | `Css/07_Vendors/` dışına vendor kod kopyalanmaz · `vendor/` dokunulmaz | Karantina + güncelleme kaybı | assets `AGENTS.md` §4 Yasak 2 |
| 3 | Inline style/script üretilmez (CSP nonce) | Güvenlik başlığı kırılması | assets `AGENTS.md` §4 Yasak 3 |
| 4 | Font/Image onaysız silinmez/adı değiştirilmez | Referans kırılması | assets `AGENTS.md` §4 Yasak 4 |
| 5 | UI kodu öncesi mockup + ilgili PNG okunur | Kanonik görünüm (Guardrail #11) | [[../.ai/ui-design/01-mockup-index]] |
| 6 | Token yalnız `Css/01_Abstracts/` içinde; ham hex/px katmana yazılmaz | Token SSOT | assets `AGENTS.md` §4 Zorunlu 2 |
| 7 | JS: `var` yasak · `eval()` yasak · `innerHTML` yasak (DOMParser + TrustedTypes) | Güvenlik + modern dil | assets `AGENTS.md` §4 Zorunlu 3 |
| 8 | Device CSS ekleme → `js/devices.config.js` + `DeviceCssMap.php` eşzamanlı | Çift/eksik yüklenme | assets `AGENTS.md` §4 Zorunlu 4 |
| 9 | Yeni `.css`/`.js` dosyası `css-template` / `js-template`'ten türetilir | Guardrail #16 | [[../.ai/.templates/frontend/css-template]] · [[../.ai/.templates/frontend/js-template]] |
| 10 | Faz 1 keşif onaysız kod yok · aynı dosya 2. kez okunmaz · 3 başarısız → DUR | Anti-overthink | [[../AGENTS.md]] §4/§5 |
| 11 | **Commit subagent ATMAZ** | Tarih/entegrasyon orkestratörde | [[../AGENTS.md]] §7/§8 |

> **eli10 (basit):** Bu kurallar görünümün tek yerden, güvenli ve şablona uygun üretilmesini, commit'in tek elden atılmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural, koddan uzun yaşar; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanmasını garantiler. İhlalde revert + `log.md` kaydı işletilir; kural değişince (ADR/kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: CLAUDE+AGENTS (+Css/CONTEXT)] → [K2: MOCKUP oku (PNG > ASCII > Inventory)]
  → [K3: ŞABLON oku (css-template | js-template — Guardrail #16)]
  → UYGULA (Css/ katmanı | js/ modülü) → (cihaz dosyası ise: devices.config.js + DeviceCssMap.php EŞZAMANLI)
  → [K4: TEST — Vitest/Playwright yeşil] → (UI ise [K5: BROWSER — mockup karşılaştırma])]
  → [K6: RAPOR — commit ATMAZ] → ORKESTRATÖR
```

Adım ve kapıların tamamı [[WORKFLOW.md]] §3.1 / §3.2'dedir; katman bazlı üretim adımı ayrıca `Css/WORKFLOW.MD` §5 içindedir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm sırası | §1–§7, ≤3 başlık seviyesi |
| 3 | Envanter | 467 dosya · Css 115 · js 106 · Fonts 105 · Image 130 · tests 5 — disk ölçümü |
| 4 | Çelişki | §3.6 eksiksiz; her fark `VERIFICATION REQUIRED` ile işaretli |
| 5 | Placeholder | Dosyada doldurulmamış şablon değişkeni kalmadı (arama deseni: iki parantez + harf) |
| 6 | Wiki-link | Wiki-link biçimi (çift köşeli parantez) kullanıldı; hedefler diskte var (olmayanlar §3.6'da) |
| 7 | eli10 + eli15 | §3.1–§3.7 + §4 bloklarında etiketli blok var |
| 8 | Halüsinasyon | Diskte olmayan dosya var sayılmadı (`main.css` → YOK) |
| 9 | Dokunulmaz | `CLAUDE.md`, `AGENTS.md`, `Css/*` dokümanları değiştirilmedi |
| 10 | Emoji | Dekoratif emoji yok (yalnız `VERIFICATION REQUIRED` işaretçisi) |
| 11 | Uzunluk | 300–500 satır |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Mevcut kural dokümanı (OKUNDU, değişmedi) |
| Klasör rolleri | [[AGENTS.md]] | Routing + envanter iddiaları (çelişki kaynağı §3.6) |
| Klasör süreç | [[WORKFLOW.md]] | Adım/kapı akışı |
| **Katman context (klasör içi SSOT)** | [[Css/CONTEXT.md]] | 11 katman envanteri + import zinciri |
| Katman süreç | `Css/WORKFLOW.MD` | Katman üretim akışı |
| Kök master | [[../AGENTS.md]] | §1 kapı · §4 keşif · §5 anti-overthink · §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| CSS şablonu | [[../.ai/.templates/frontend/css-template]] | Katman kuralları (Guardrail #16) |
| JS şablonu | [[../.ai/.templates/frontend/js-template]] | Modül dosyası (Guardrail #16) |
| Template registry | [[../.ai/.templates/index]] | Şablon envanteri |
| Mockup indeksi | [[../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend |
| shared context | [[../shared/CONTEXT.md]] | `DeviceCssMap` sahibi ortak katman |
| auth context | [[../auth.coremusic.net/CONTEXT.md]] | `auth-bundled.css` tüketicisi |
| home context | [[../home.coremusic.net/CONTEXT.md]] | Ana sayfa tüketicisi |
| Disk kanıtı | `assets.coremusic.net/` | Bu dokümandaki tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** context
