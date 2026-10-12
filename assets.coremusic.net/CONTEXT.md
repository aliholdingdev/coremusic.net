---
title: "CoreMusic — assets.coremusic.net Context"
type: docs
category: frontend
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — assets.coremusic.net Context

**docType:** context · **Klasör:** `assets.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`assets.coremusic.net/`, CoreMusic'in **statik asset servisidir**: tüm subdomainlerin CSS, JS, yazı tipi ve görsel varlıkları ile frontend test altyapısı bu klasörde yaşar; PHP sayfa sunumu yapmaz. Bu doküman klasörün disk envanterini (465 dosya — `node_modules/` ve `test-results/` hariç; 15 üst girdi = 8 dosya + 7 dizin — ölçüm 2026-10-12), statik servis kurallarını ve vault dosyaları ile disk arasındaki çelişkileri kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: K-space katman mimarisi atıfları (K000–K020, A0–A5, `.ai/architecture/`) bu sürümde bilinçli olarak bulundurulmaz — kaldırıldı, yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Envanter: 465 dosya, 15 üst girdi (8 dosya + 7 dizin) | `directory_tree` ölçümü (2026-10-12) |
| Dağılım: `Css/` 114 · `js/` 102 · `Fonts/` 105 · `Image/` 130 · `tests/` 6 · kök 8 | `directory_tree` ölçümü (2026-10-12) |
| CSS girişi `Css/08_Devices/d-*.css` + `Css/auth-bundled.css`; `main.css` YOK | glob `**/main.css` → 0 isabet (2026-10-12) |
| CORS + cache + MIME kuralları iki sunucuda ayrı dosyada | `.htaccess` 47 satır (Apache) · `web.config` 37 satır (IIS) — okuma ölçümü |
| Test: Vitest (jsdom, %80 kapsam) + Playwright (chromium) | `vitest.config.js` 33 satır · `playwright.config.ts` 56 satır — okuma ölçümü |
| Klasör kuralları `CLAUDE.md` + `AGENTS.md` içinde | bu görevde yalnız okundu, DEĞİŞTİRİLMEDİ |

> **eli10 (basit):** Bu klasör sitenin resimlerini, yazı tiplerini, stil ve davranış kodunu barındırır; bu kodun testleri de buradadır.
> **eli15 (detay):** Ayrı yazıldı çünkü statik asset tüm subdomainlerin ortak kaynağıdır; tek dosyanın kayması tüm sayfaları bozar. Okunması, her sayının hangi ölçümden geldiğini ve hangi dosyanın ne işe yaradığını gösterir. Yazılması envanter ile disk gerçeğinin hizasını kanıtlar. Ölçülmeyen sayısal iddia bu dosyaya girmez; girerse eski bilgi yeni oturumları yanlış yönlendirir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/` kök + `Css/`, `js/`, `Fonts/`, `Image/`, `tests/` envanteri | `Css/` katman içi detay → [[Css/CONTEXT.md]] (klasör içi SSOT) |
| Statik servis kuralları (CORS, cache, MIME, dizin listeleme) | `home/` `auth/` `api/` PHP sayfa sunumu |
| Test yapılandırması (Vitest + Playwright) ve test dosyaları | `.ai/` vault içeriği (SSOT — okunur, burada üretilmez) |
| Vault ↔ disk çelişki kaydı (§3.7) | Mockup görselleri → `.ai/ui-design/` + `.ai/.png/` (okunur, üretilmez) |

- **Kullananlar:** UI Designer (birincil — CSS/JS), Security Engineer (CSP/vendor), QA Engineer (`tests/`, Vitest/Playwright), MO (bu doküman).
- **Ön koşul:** Mockup Before Frontend — UI kodundan önce [[../.ai/ui-design/01-mockup-index.md]] okunur; sayısal iddialar disk ölçümünden gelir, kanıt yoksa `UNKNOWN`.
- **Dokunulmaz:** `CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md`, `Css/*` dokümanları, `Fonts/` ve `Image/` binary dosyaları bu görevde değiştirilmez — yalnız bu dosya yazılır.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, servis kuralları ve test altyapısı anlatılır; başka klasörlerin içeriği konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman CSS katman tarifine ya da vault özetine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçümün ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Envanter (`directory_tree` ölçümü 2026-10-12: 465 dosya, 15 üst girdi)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `Css/` | Tüm subdomainlerin ITCSS stilleri | 114 dosya: 94 `.css` + 16 `.map` + 4 `.md`; 11 katman + kök (5 dosya) | Stil tek yerden servis edilsin (ADR-001) | Katman/mockup değişince — detay [[Css/CONTEXT.md]] |
| `js/` | Vanilla ES6+ modülleri | 102 dosya: 94 `.js` + 8 `CLAUDE.md`; 12 alt dizin | Davranış stil ve sunumdan ayrı yaşasın | Modül davranışı değişince |
| `Fonts/` | Yazı tipleri | 105 `.ttf`: DMSans 78 + DMSansExtraBlack 2 + PlusJakartaSans 14 + PlayfairDisplay 6 + Respective 1 + Arima 1 + AvalonMedium 1 + Bickham 1 + `00543.ttf` 1 | Cross-origin yazı tipi servisi | Onaylı yeni yazı tipi eklenince |
| `Image/` | Görseller | 130 `.png`: `background/` 8 · `profiles/` 1 · `res-pink/` 121 (8 alt klasör) | Görsel varlıkların tek kütüphanesi | Onaylı görsel eklenince/kaldırılınca |
| `tests/` | Frontend testleri | 6 dosya: 2 birim spec, 1 uç spec (`.ts`), 1 mock, 2 doküman | JS davranışı ve uç senaryo sınanır | JS değişince (QA) |
| `vitest.config.js` | Birim test kurulumu | 33 satır: jsdom, v8 kapsam `%80` (lines/functions/branches/statements), `include: tests/**/*.spec.js + js/**/*.spec.js` (9 spec), timeout 5000 | Birim test tek komutla koşsun | Kapsam/test ayarı değişince |
| `playwright.config.ts` | Uç test kurulumu | 56 satır: `testDir ./tests/e2e`, `baseURL http://home.coremusic.net:81`, tek proje chromium, test timeout 30 sn, `webServer: php -S localhost:81 -t ../../home.coremusic.net/`, json çıktı `test-results/e2e-results.json` | Gerçek tarayıcıda uç test | E2E senaryosu/ortam değişince |
| `.htaccess` | Apache statik servis kuralı | 47 satır: CORS `*` (font/image/css/js), css `max-age=3600`, js `no-cache` (2026-10-07), font `31536000 immutable`, image `604800`, `nosniff`, `X-Frame-Options SAMEORIGIN`, `Options -Indexes` | Apache'de cache/CORS/güvenlik tek yerde | Sunucu kuralı değişince (DevOps) |
| `web.config` | IIS statik servis kuralı | 37 satır: CORS `*`, `Cache-Control no-cache`, 5 font MIME (ttf/otf/woff/woff2/eot), `directoryBrowse false` | IIS aynı politikayı ayrı dille uygular | Sunucu kuralı değişince (DevOps) |
| `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md` | Klasör kural/rol/süreç/envanter dokümanları | 4 dosya; `authority` frontmatter'ları §3.6 tablosunda | Bilgi dört farklı hızda değişir | Yalnız vault-sync (MO) |
| `node_modules/` · `test-results/` | Vite/Vitest önbelleği + e2e çıktısı | `node_modules/` içinde `.vite/vitest/<hash>/results.json` (1 dosya); `test-results/` içinde `e2e-results.json` (1 dosya) | Üretilmiş çalışma artığı | Elle dokunulmaz; envanter sayımına girmez |

**`Css/`**
> **eli10 (basit):** Tüm sitenin görünüm kurallarının durduğu klasör.
> **eli15 (detay):** Ayrıdır çünkü stil tek uygulama noktasında toplanır (ADR-001). İçine 11 katman ve tek auth girişi (`auth-bundled.css`) girer. Okunması import zincirini gösterir. Katman içi her sayı için `Css/CONTEXT.md` esastır; bu dosya yalnız özet verir.

**`js/`**
> **eli10 (basit):** Sayfaların davranışını yazan kodun klasörü.
> **eli15 (detay):** Ayrıdır çünkü davranış, stil ve sunumdan farklı hızda değişir. İçine router, bileşenler, yöneticiler, özellikler ve cihaz yükleyicisi girer. `eval`, `var`, `innerHTML` yasakları ([[AGENTS.md]] §4) burada geçerlidir. Değişince test (Vitest/Playwright) kapıdan geçer.

**`Fonts/`**
> **eli10 (basit):** Sayfalarda kullanılan yazı tipleri.
> **eli15 (detay):** Ayrıdır çünkü binary dosyadır ve cross-origin sunulur (`.htaccess` CORS). Silinirse ya da adı değişirse `@font-face` kırılır. Onaysız değişiklik yasaktır. Yeni tip yalnız onayla eklenir.

**`Image/`**
> **eli10 (basit):** Arka plan, avatar ve ikonların bulunduğu resim kütüphanesi.
> **eli15 (detay):** Ayrıdır çünkü görsel referansla bağlanır; taşınırsa kırık görsel doğar. `res-pink/` ana kütüphanedir (121 dosya). Onaysız silme veya yeniden adlandırma yasaktır. Yeni görsel onayla eklenir.

**`tests/`**
> **eli10 (basit):** JavaScript'in doğru çalıştığını kanıtlayan dosyalar.
> **eli15 (detay):** Ayrıdır ki test, kod değişince yalnız doğrulasın. İçine birim senaryo, uç senaryo, sahte veri (mock) ve doküman girer. Yeni özellik = yeni test. Kapı yeşil olmadan iş bitmez.

**`vitest.config.js`**
> **eli10 (basit):** Birim testlerin nasıl koşulacağını ayarlayan dosya.
> **eli15 (detay):** Ayrı durur ki test ayarı kod içine gömülmesin. Okunması, hangi spec'lerin kapsama girdiğini (`tests/` + `js/` içi 9 spec) ve %80 eşiklerini gösterir. Kapsam değişince düzenlenir. Yanlış yazılırsa testler hiç koşulmaz.

**`playwright.config.ts`**
> **eli10 (basit):** Gerçek tarayıcıda uç testi başlatan ayar dosyası.
> **eli15 (detay):** Ayrı durur çünkü uç test farklı ortam ister: adres, tarayıcı ve sunucu burada tanımlıdır. Okunması, testin `home.coremusic.net:81` üzerinde chromium ile koştuğunu ve sonucun `test-results/`e yazıldığını gösterir. Ortam değişince düzenlenir. Ayar bozulursa e2e testleri hiç başlamaz.

**`.htaccess`**
> **eli10 (basit):** Apache sunucusuna verilen cache ve izin kuralları.
> **eli15 (detay):** Ayrıdır çünkü sunucu kuralı koddan farklı hızda değişir ve Apache kendi dilinde okunur. Okunması, hangi uzantının ne kadar önbellekte kaldığını (js `no-cache`, font 1 yıl) gösterir. Sunucu kuralı değişince DevOps düzenler. Silinirse CORS başlığı kalkar ve font/görsel yüklemesi güvenlik hatası verir.

**`web.config`**
> **eli10 (basit):** IIS sunucusu için aynı kuralların ayrı dille yazılmış hâli.
> **eli15 (detay):** Ayrıdır çünkü IIS `.htaccess` okumaz; ikisi ayrı dosyada durur. Okunması, CORS, no-cache ve font MIME kayıtlarını gösterir. Silinirse font MIME IIS'te kırılır ve dizin listeleme açılır. Değişiklik yalnız DevOps onayıyla girer.

**`CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md`**
> **eli10 (basit):** Bu klasörün kural, rol, süreç ve envanter defterleri; dördü ayrı ayrı durur.
> **eli15 (detay):** Dört dosya ayrıdır çünkü bilgi, kural, rol ve süreç farklı hızda değişir; tek dosyada toplansa her revizyonda tümü okunur. Okunmaları işe doğru sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Dördü de vault-sync (MO) tarafından güncellenir; bu görevde yalnız `CONTEXT.md` yeniden yazıldı.

**`node_modules/` · `test-results/`**
> **eli10 (basit):** Test çalıştırınca kendi kendine oluşan önbellek ve çıktı klasörleri.
> **eli15 (detay):** İkisi de üretilmiş olduğu için envanter sayımına girmez ve elle düzenlenmez. Okunmaları yalnız "bu klasör neden var" sorusunu yanıtlar içindir. Silinirse araçlar yeniden üretir. Sayıma dahil edilseydi dosya toplamı şişer ve ölçümler karşılaştırılamazdı.

### 3.2 `Css/` Katmanları (özet — detay [[Css/CONTEXT.md]])

| Katman | Dosya (disk 2026-10-12) | Not |
|--------|--------------------------|-----|
| `01_Abstracts/` | **19** | token dosyaları; `copy` artıkları diskte YOK |
| `02_Base/` | **3** | reset + iskelet |
| `03_Layout/` | **4** | header/footer/sidebar/widget-grid |
| `04_Components/` | **15** | bileşen stilleri |
| `05_Pages/` | **13** | sayfa stilleri — `p-history.css` dahil |
| `06_Utilities/` | **1** | u-helpers-utility |
| `07_Vendors/` | **33** (17 `.css` + 16 `.map`) | Bootstrap ailesi — salt okunur |
| `08_Devices/` | **15** | 8 normal + 7 `d-auth-*` |
| `09_ViewModes/` | **4** | car/home/pro/studio |
| `10_Helpers/` | **1** | h-ellipsis |
| `11_OAuth/` | **1** | oauth.css |
| `Css/` kök | **5** | `auth-bundled.css` + `AGENTS.md` + `CLAUDE.md` + `CONTEXT.md` + `WORKFLOW.MD` |

> **eli10 (basit):** CSS klasörünün katman katman dökümü; sayılar bugünün disk ölçümü.
> **eli15 (detay):** Özet burada, ayrıntı `Css/CONTEXT.md`'dedir; iki dosya çelişirse o dosya kazanır. Sayılar değişince önce orası, sonra bu tablo güncellenir. Okunması katman sorumluluğunu baştan gösterir. Tahmin edilen tek değer yok; hepsi sayım sonucudur.

### 3.3 `js/` Alt Dizin Dağılımı (disk ölçümü 2026-10-12)

| Alt dizin | Dosya | Kırılım |
|-----------|-------|---------|
| kök | **6** | 5 `.js` (`main.js`, `device-loader.js`, `device-layout-updater.js`, `devices.config.js`, `oauth-manager.js`) + `CLAUDE.md` |
| `auth/` | **3** | 2 `.js` + `CLAUDE.md` |
| `components/` | **37** | kökte dosya yok; 4 alt dizin: `base/` 5 · `composites/` 13 · `interactive/` 7 · `primitives/` 12 (6 bileşen + 6 spec) |
| `core/` | **7** | 6 `.js` (5 modül + `breakpoints.spec.js`) + `CLAUDE.md` |
| `coreplayer/` | **6** | 5 `.js` + `CLAUDE.md` |
| `features/` | **7** | 6 `.js` + `CLAUDE.md` |
| `managers/` | **6** | 5 `.js` + `CLAUDE.md` |
| `router/` | **30** | kök 24 (23 `.js` + `CLAUDE.md`) + `config/` 6 (5 `.js` + `CLAUDE.md`) → toplam **28 `.js`** |

> **eli10 (basit):** JavaScript klasörünün oda oda dökümü; sayılar bugünün ölçümü.
> **eli15 (detay):** Tablo dosya sayısını `.js` ve `.md` olarak ayırır, böylece eski sayılarla karşılaştırma yapılabilir. `components/` köksüz, alt dizinlidir; `router/` içinde `config/` alt klasörü vardır. Klasör içi `CLAUDE.md`ler o modülün yerel notudur. Okunması hangi modülün nerede olduğunu baştan gösterir.

### 3.4 Komşu İlişkileri (assets ↔ sistem)

| Komşu | Yön | Ne taşır (disk kanıtı) |
|-------|-----|------------------------|
| `../home.coremusic.net` | assets ← home | home sayfaları `Css/` + `js/` tüketir (komşuluk; çağrı satırları bu görevde okunmadı → VERIFICATION REQUIRED) |
| `../auth.coremusic.net` | assets ← auth | `Css/auth-bundled.css` (tek auth girişi) + `Css/08_Devices/d-auth-*.css` (7) + `js/auth/` (2 `.js`) |
| `../shared` | assets → shared | `shared/src/Device/DeviceCssMap.php` + `DeviceRenderer.php` VAR (`get_file_info` ölçümü) — cihaz → css yolu eşlemesi; `js/devices.config.js` ile senkron |
| `../.ai` | assets → vault | `ui-design/01-mockup-index.md`, `.templates/frontend/css-template.md`, `.templates/frontend/js-template.md` — hepsi VAR (dosya bilgisi ölçümü) |

> **eli10 (basit):** Bu klasör iki sayfa ailesini besler, ortak kütüphaneye bağlanır ve vault'tan kural alır.
> **eli15 (detay):** Komşuluk ayrı tablodır çünkü bağımlılık yönü ile sahiplik ayrı bilgidir; iş kodu kopyalanmaz, çağrılır. Okunması, hangi dosyanın değişirse bu klasörün kırılacağını gösterir. Yazılması, cihaz dosyası ekleme ile harita senkronunu baştan hatırlatır. Tablo unutulursa herkes auth kodunu buraya taşımaya kalkar.

### 3.5 Test Altyapısı (`tests/` + in-tree spec'ler — disk ölçümü)

| Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|-------|---------|--------|-----------|---------------------|
| `tests/components/player-info.spec.js` | Bileşen birim testi | player-info senaryosu | Bileşen davranışı sabit kalsın | Bileşen değişince |
| `tests/components/history.spec.js` | Bileşen birim testi | history senaryosu | Geçmiş görünümü davranışı sabit kalsın | Bileşen değişince |
| `tests/mocks/player-api.mock.js` | Sahte API yanıtı | mock veri | Test gerçek servise bağlanmasın | API sözleşmesi değişince |
| `tests/e2e/player-info.spec.ts` | Uç senaryo (TS) | Playwright senaryosu | Kullanıcı akışı tarayıcıda sınansın | Akış değişince |
| `tests/README.md` · `tests/QUICKSTART.md` | Test dokümanı | çalıştırma talimatı | Test koşma yolu anlatılsın | Komut değişince |
| `js/**/*.spec.js` (7 dosya) | In-tree birim spec | `components/primitives/` 6 + `core/breakpoints.spec.js` | Vitest `include`'ı bunları da kapsasın | Spec değişince |

> **eli10 (basit):** Test dosyaları, ayarları ve sahte verisi burada; birim test ile uç test ayrı koşar.
> **eli15 (detay):** Ayrıdır ki test, kod değişince yalnız doğrulasın; mock gerçek servise dokunmaz. Vitest `js/**/*.spec.js` ile in-tree 7 spec'i, `tests/**/*.spec.js` ile 2 birim spec'i kapsar; toplam 9 spec dosyası (`search_files` ölçümü). Playwright yalnız `tests/e2e/` içindeki `.ts` senaryosunu koşar. Yeni özellik eklenince test de eklenir.

### 3.6 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök (AGENTS.md + CLAUDE.md)
  → assets.coremusic.net/ (CLAUDE.md → AGENTS.md → WORKFLOW.md → CONTEXT.md)
    → .ai/ vault yalnız ihtiyaç anında (§7'deki hedeflerle)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `CLAUDE.md` | `authority: Single Source of Truth (SSOT)` — kendi iddiası (v2.0.0, 2026-09-21) | Klasör bağlamı + kurallar |
| `AGENTS.md` | `authority: Single Source of Truth (SSOT)` — kendi iddiası (v1.0.0, 2026-09-06) | Envanter iddiaları + yasaklar (çelişki kaynağı §3.7) |
| `WORKFLOW.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` (v1.0.0, 2026-10-03) | Adım → kapı → rapor |
| `CONTEXT.md` (bu dosya) | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` (v2.0.0) | Envanter + çelişki kaydı |

> **eli10 (basit):** Önce kök kurallar, sonra bu klasörün dört dosyası, vault ise sadece ihtiyaç anında okunur.
> **eli15 (detay):** İlişki ayrı tabloda çünkü dört dosyanın da ikinci SSOT olmadığı bellidir: ikisi kendi iddiasını taşır, ikisi türevdir; içerik çelişkisinde vault, sayı çelişkisinde disk kazanır. Okunması boot sırasını ve otorite zincirini gösterir. Yazılması, bu dosyaların bağımsız kaynak sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla çalışır ve bilgi ayrışır.

### 3.7 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (2026-10-03): "467 dosya — `.js 101`, `.css 95`, `.md 16`" | 465 dosya — `.js 98`, `.css 94`, `.md 17` + `.MD 1` = 18 | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md`: "Css 115 · js 106 · tests 5" | Css **114** · js **102** · tests **6** | **Disk kazanır** |
| 3 | Eski `CONTEXT.md`: "`01_Abstracts/` 21 (19 + 2 `copy`)" | **19** dosya; `copy` artıkları yok | **Disk kazanır** |
| 4 | Eski `CONTEXT.md` + `AGENTS.md` §2: "`05_Pages/` 12" | **13** (`p-history.css` eklendi) | **Disk kazanır** |
| 5 | Eski `CONTEXT.md`: "`js/router/` 37 (35 `.js` + 2 `.md`)" | **30** (28 `.js` + 2 `.md`) | **Disk kazanır** |
| 6 | Eski `CONTEXT.md`: "`js/components/` 36 düz dosya" | **37** — kök dosya yok, 4 alt dizinde | **Disk kazanır** |
| 7 | `AGENTS.md` §2: "`js/core/` 4 dosya · `js/features/` 5 dosya" | 6 `.js` + 1 `.md` · 6 `.js` + 1 `.md` (`breakpoints*`, `welcome-modal.js` fazladan) | **Disk kazanır** — `AGENTS.md` bu görevde değiştirilmez, yalnız işaretlenir |
| 8 | `AGENTS.md` §2: "`js/router/config/` 7 dosya (`events`, `navigate-utils` dahil)" | 5 `.js` + 1 `CLAUDE.md` — iki dosya diskte YOK | **Disk kazanır** |
| 9 | `AGENTS.md` §2: "`01_Abstracts/` 19 token + 2 `copy` artığı" | 19 dosya; `copy` artıkları yok | **Disk kazanır** |
| 10 | Doğrulanan uyuşmalar: `Fonts/` 105 · `04_Components/` 15 · `08_Devices/` 15 · `js/router/` 28 `.js` · `js/` kök 5 · `js/auth/` 2 · `js/managers/` 5 | ölçülen değerlerle birebir | **Uyumlu** — düzeltme gerekmez |

> **eli10 (basit):** Eski kâğıtlardaki sayılar bugünkü diskle uyuşmuyor; her zaman disk kazanır.
> **eli15 (detay):** Bu tablo yalnız kanıtlanmış farkları taşır: dosya sayıları, eksik listeler ve tarihsel ölçüm. İçine tahmin girmez. Okunması, eski sayıya güvenmeyi engeller. Fark bulunca vault tarafı düzeltilir; disk değiştirilmez, dosya uydurulmaz.

### 3.8 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (stil → `Css/`, davranış → `js/`, test → `tests/`) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ölçü/renk `Css/01_Abstracts/`'te tek yerde durur | Her dosyada ayrı `16px` → tutarsız görünüm, ölçü değişince tarama |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz (`08_Devices/` ayrı) | Her cihaz farkı tüm dosyalara yayılır → 8+ yerde tekrar, drift |
| 5 | **Vendor karantinası** | 3. taraf kod `Css/07_Vendors/`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: stil `Css/`'te, davranış `js/`'de, harf `Fonts/`'ta, resim `Image/`'de, soru `tests/`'te durur. Böylece bir yer değişince sadece o değişir. Cihaz ayarları ayrı olunca telefon ayarı masaüstünü bozmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

Klasör kuralı kaynakları (bu görevde okundu, DEĞİŞTİRİLMEDİ): [[CLAUDE.md]] + [[AGENTS.md]] + [[WORKFLOW.md]].

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Framework/jQuery/React/Vue yasak — Vanilla JS + ITCSS (`AGENTS.md` §4 Yasak 1) | Bağımlılık ve hacim artışı |
| 2 | `Css/07_Vendors/` dışına vendor kod kopyalanmaz | Karantina bozulur, güncelleme kaybolur |
| 3 | Inline style/script üretilmez (CSP nonce uyumu) | Güvenlik başlığı kırılır |
| 4 | `Fonts/` ve `Image/` dosyaları onaysız silinmez/adı değiştirilmez | Referans kırılır, sayfa bozulur |
| 5 | UI kodu öncesi ilgili mockup okunur | Kanonik görünüm kaybolur |
| 6 | Token tanımları yalnız `Css/01_Abstracts/` içinde; katmana ham hex/px yazılmaz | Token SSOT kaynar |
| 7 | JS: `var` yasak · `eval()` yasak · `innerHTML` yasak (DOMParser + TrustedTypes) | Güvenlik açığı, modern dil ihlali |
| 8 | Device CSS ekleme → `js/devices.config.js` + `shared/src/Device/DeviceCssMap.php` eşzamanlı | Çift/eksik yüklenme doğar |
| 9 | Yeni `.css`/`.js` dosyası `css-template` / `js-template`'ten türetilir (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 10 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 11 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.7 tablosunda işaretlenir | SSOT ihlali |
| 12 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |

> **eli10 (basit):** Bu kurallar görünümün tek yerden, güvenli ve şablona uygun üretilmesini; commit'in tek elden atılmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural, koddan uzun yaşar; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazılmaları, denetimin tekrarlanmasını garantiler. İhlalde revert + `log.md` kaydı işletilir; kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
ÖLÇ (envanter — bu oturumda) → YAZ (yalnız CONTEXT.md, şablon iskeleti)
  → ÇELİŞKİ KAYDI (vault ↔ disk → disk kazanır) → DOĞRULAMA (§6 tablosu)
  → İLGİLİ INDEX'E 1 SATIR → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Şablon oku: context-template + context-md-template | İskelet + format kuralları | Tutarlı 7 bölüm (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 2 | Envanter ölç (`directory_tree` + glob + okuma) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Yaz (§1–§7, 5 sütun, eli10/eli15 blokları) | Bu dosya | Tek biçimli context dokümanı | Bölüm sırası kayar, arama kırılır |
| 4 | Çelişki tablosunu güncelle (§3.7) | 10 karar satırı | Eski bilgi yeni oturumu yanıltır | "467 dosya" efsanesi sürer |
| 5 | §6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | İlgili index'e 1 satır + rapor; **commit ATMA** | Kayıt + rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve doğrulama) hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` (frontend şablon §3.2) |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişken yer tutucusu (çift süslü parantezli desen) kalmadı |
| 4 | Envanter | 465 dosya · Css 114 · js 102 · Fonts 105 · Image 130 · tests 6 · kök 8 — bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı `directory_tree`/glob/okuma ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 7 | eli10 + eli15 | §1, §2, §3.1 (11 satır), §3.2–§3.8, §4, §5 maddelerinde çift blok var (eli10 en fazla 2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4 #11 "disk kazanır" var; §3.7 onaylı |
| 9 | Boot sırası | Kök ↔ klasör ↔ vault ilişkisi §3.6'da var |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; katman mimarisi tablosu/eklentisi yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 250–400 satır bandı (görev bütçesi) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — okunur, değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Dosya sahipliği + envanter iddiaları (çelişki kaynağı §3.7) |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Katman context (klasör içi SSOT) | [[Css/CONTEXT.md]] | 11 katman envanteri + import zinciri |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Kök vault göstergesi | [[../CONTEXT.md]] | Proje geneli envanter |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | §5 Domain Boundaries, §6 routing |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| Mockup indeksi | [[../.ai/ui-design/01-mockup-index.md]] | Mockup Before Frontend |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon envanteri |
| CSS şablonu | [[../.ai/.templates/frontend/css-template.md]] | Yeni `.css` kaynağı (Guardrail #16) |
| JS şablonu | [[../.ai/.templates/frontend/js-template.md]] | Yeni `.js` kaynağı (Guardrail #16) |
| shared context | [[../shared/CONTEXT.md]] | `DeviceCssMap` sahibi ortak katman |
| auth context | [[../auth.coremusic.net/CONTEXT.md]] | `auth-bundled.css` tüketicisi |
| home context | [[../home.coremusic.net/CONTEXT.md]] | Ana sayfa tüketicisi |
| Birim test kanıtı | [[vitest.config.js]] | jsdom + %80 kapsam + include desenleri |
| Uç test kanıtı | [[playwright.config.ts]] | baseURL, chromium, webServer |
| Disk kanıtı | `assets.coremusic.net/` | Bu dokümandaki tüm sayılar (2026-10-12 ölçümü) |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12 · **docType:** context
