---
title: "CoreMusic — home.coremusic.net Klasör Context"
type: docs
category: domain
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# home.coremusic.net — CONTEXT.md

**docType:** context · **Klasör:** `home.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `home.coremusic.net/` klasörünün **ne işe yaradığını** ve diskteki **gerçek** envanterini tanımlar: ana medya paneli (home) subdomain'inin giriş noktaları, yapılandırma zinciri, bileşen katmanı, sayfaları ve testleri. Kapsam: home = **subdomain sayfa servisi** — statik asset üreten değil, `assets.coremusic.net`'ten stylesheet/script çekip sayfa HTML'ini basan PHP uygulamasıdır. Tüm sayılar 2026-10-03 disk ölçümüdür; vault/şablon ile çelişen satırlar §3.5'te işaretlenmiştir (çelişkide **disk kazanır**).

| Karar | Kaynak (disk) |
|-------|---------------|
| Tek giriş kapısı = `index.php` (Shared SPA Router / PageRouterKernel) | `home.coremusic.net/index.php` başlık yorumu |
| Auth köprüsü tek yerde: `include/Auth/HomeAuthBridge.php` | dosya var (include/Auth/ tek dosya) |
| Bileşen modeli: `ComponentInterface` + `AbstractComponent` + `ComponentLoader` | `include/Interfaces/`, `include/Class/` dosya listesi |
| Test kapısı phpunit ^10.5 — `composer.json` `scripts` bloğu **YOK** | `composer.json` doğrudan okundu |
| Klasör kuralları mevcut dokümanlarda: `CLAUDE.md` + `AGENTS.md` | bu oturumda okundu, **DEĞİŞTİRİLMEDİ** |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `home.coremusic.net/` kök + `config/`, `include/`, `pages/`, `tests/` envanteri | `vendor/` içeriği (1815 dosya, composer üretimi — salt okunur) |
| Home'un komşularla (auth, assets, shared) konuşma haritası | `shared/src/` ortak katman (bağımlı olunan, ait olduğu yer orası) |
| Vault ↔ disk çelişki kaydı (§3.5) | Statik asset üretimi → `assets.coremusic.net/CONTEXT.md` |
| Yeni CONTEXT/WORKFLOW üretim iskeleti | Mevcut `CLAUDE.md` / `AGENTS.md` revizyonu (dokunulmaz) |

- **Kullananlar:** Backend Architect (birincil), UI Designer (`pages/*.php` görünüm), QA Engineer (`tests/`), MO (doküman).
- **Ön koşul:** `index.php` + `composer.json` 1. kez okunmuş; sayısal iddialar disk ölçümünden.
- **Not:** `config/.env` içeriği okunmaz, kopyalanmaz, vault'a yazılmaz (Secret Yok).

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1-2 — disk ölçümü 2026-10-03)

**Sayı gerçeği:** toplam **1859 dosya** (vendor dahil) · vendor hariç **44 dosya** · vendor hariç uzantı dağılımı: `.php 35` · `.md 2` · `.config 1` (web.config) · `.env 1` · `.xml 1` · `.htaccess 1` · `.json 1` · `.lock 1` · uzantısız 1 (`.phpunit.cache/test-results`).

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `index.php` | Tek giriş kapısı (front controller) | autoload + `config/constants.php` require, DEBUG_MODE'da `opcache_reset()`, PageRouterKernel başlık yorumu | Tüm home istekleri tek kapıdan geçsin | Routing/entry davranışı değişince (ADR) | Uygulamayı açan ana dosya: gelen isteği kapıdan içeri geçirir. | Tek dosyadır çünkü giriş noktası dağınıksa hangi isteğin nereden geçiği bilinmez. Okunması, sırayı (autoload → constants → router) gösterir. Değişiklik yalnız ADR'li sıra değişikliğinde yapılır. |
| `header.php`, `footer.php` | Ortak HTML kabuk parçaları (tek bileşen ilkesi) | Tier koşullu HTML blokları (vault iddiası — kod içi içerik bu görevde OKUNMADI: VERIFICATION REQUIRED) | Sayfa üst/alt yapısı tek yerde dursun | Mockup (`home-1024`) değişince | Sayfanın üst ve alt parçasını tutan iki dosya. | Ayrıdırlar çünkü her sayfada aynıdırlar; tek sayfaya gömülse tekrar etmez. Okunmaları, tier koşullarını gösterir. Yazılmaları mockup ölçüsüne bağlıdır. Değişmezse her sayfada aynı üstbilgi kodu tekrar eder. |
| `autoload.php`, `composer.json`, `composer.lock` | PSR-4 yükleme + paket tanımı | `name: bayramali/home.coremusic.net` · `require-dev: phpunit/phpunit ^10.5` · `scripts` bloğu **YOK** | Bağımlılıklar ve sınıf yükleyici tek yerde | Bağımlılık eklenince (`composer update`) | Kitapları raftan çeken ve kimlik kartını tutan dosyalar. | Kilit (lock) ayrıdır ki her makine aynı sürümü kursun. `scripts` yoksa test doğrudan phpunit ile koşulur. Elle düzenlenirse sınıf bulunamaz. |
| `config/` | Uygulama yapılandırması | 5 dosya: `.env` (salt), `app.php`, `bootstrap.php`, `config.php`, `constants.php` | Ayar koddan ayrı dursun | Ayar/sabit değişince | Ortam ve uygulama ayarlarının tutulduğu oda. | Ayrıdır çünkü ayar sık, kod az değişir; koda gömülse her ayar için kod revizyonu gerekir. `.env` okunur ama içeriği hiçbir yere yazılmaz. Yeni sabit eklenince `constants.php` düzenlenir. |
| `include/` | Home'a özgü PHP kodu | 14 `.php` / 8 alt dizin: Auth 1 · Class 3 · Component 5 · Container 1 · Interfaces 1 · Repository 1 · Session 1 · Stream 1 | İş mantığı sayfalardan ayrı dursun | Home davranışı değişince | Evin içindeki odalar: kimlik köprüsü, bileşenler, oturum, veri. | Ayrıdır çünkü `pages/` yalnız görünüm yazsın; mantık oraya girerse sayfa okunmaz hâle gelir. Okunması, katman sınırını (bileşen ↔ veri ↔ oturum) gösterir. İçine yalnız home'a özgü kod girer; ortak kod `shared/`'edir. |
| `pages/` | Sayfa şablonları | 7 `.php`: `home.php`, `health.php`, `redirect.php` + `components/` 4 (`player-info`, `recent-tracks`, `welcome-banner`, `widget-grid`) | Görünüm tek yerde (ayrı dosya) | Sayfa tasarımı/mockup değişince | Sayfayı ve parçalarını basan dosyalar. | Ayrıdır ki sayfa görünümü ile iş mantığı ayrı hızda değişir. İçine yalnız HTML/çıkı verisi girer, sorgu girmez. Mockup değişince buraya dokunulur. |
| `tests/` | PHPUnit testleri | 5 `.php`: `Support/FakeComponent.php`, `Unit/Component/{HomeSongButton,RecentTracksComponent}Test.php`, `Unit/Layout/HomeLayoutVariantTest.php`, `Unit/Loader/ComponentLoaderTest.php` | Bileşen davranışı sabitlensin | Kod değişince (QA) | Kodun doğruluğunu kanıtlayan soru kâğıtları. | Test ayrıdır ki kod değişince test değişmez, yalnız doğrular. Yeni bileşen eklenince test de eklenir. Kapı (phpunit) yeşil olmadan iş bitmiş sayılmaz. |
| `phpunit.xml`, `.htaccess`, `web.config`, `.phpunit.cache/` | Test + sunucu yapılandırması | phpunit suite ayarı · Apache rewrite/CORS · IIS rewrite · test sonuç önbelleği 1 dosya | Test tek komutla, istekler doğru dosyaya düşsün | Sunucu/test kuralı değişince | Sınav kâğıdı ve iki sunucunun kapı yönlendirmesi. | Apache ile IIS farklı okur; ayrı dosya her ikisinde çalıştırır. `.phpunit.cache` üretim (build) artığıdır, elde düzenlenmez. |
| `vendor/` | Kurulu 3. taraf paketler | 1815 dosya (1591 `.php` + 224 diğer) | Çalışma zamanı hazır olsun | `composer install` (elle değil) | Kurulan aletlerin kutusu, dokunulmaz. | Elle girilse güncellemede kaybolur. Sadece composer yönetir. Sayısı yalnızca bilgi amaçlıdır; içeriğine girilmez. |
| `CLAUDE.md`, `AGENTS.md`, `CONTEXT.md`, `WORKFLOW.md` | Klasör dokümanları | 2 mevcut + 2 yeni (bu üretim) | Kural/rol/süreç/envanter ayrı | Vault senkronu (MO) | Klasörün kural, rol, süreç ve envanter defterleri. | Dört dosya ayrıdır çünkü bilgi, kural, rol, süreç farklı hızda değişir. Okunmaları işe doğru sırayla başlamayı sağlar. Mevcut ikisi bu üretimde yalnızca okundu — değiştirilmedi. |

**eli10 / eli15 blokları (§3.1'deki 10 maddenin karşılığı):**

**`index.php`**
> **eli10 (basit):** Uygulamayı açan tek kapı — gelen istek buradan içeri girer.
> **eli15 (detay):** Ayrı dosyadır çünkü giriş noktası tek olunca hangi isteğin nereden geçtiği belli olur. İçine önce kitaplıklar (autoload), sonra ayar, sonra yönlendirici sırayla bağlanır. Okunması sırayı gösterir. Değişiklik yalnız ADR'li sıra değişikliğinde yapılır; değişmezse sayfalar açılmaz.

**`header.php` · `footer.php`**
> **eli10 (basit):** Her sayfada ortak görünen üst ve alt parçalar.
> **eli15 (detay):** Ayrıdırlar çünkü her sayfada aynıdırlar; tek sayfaya gömülse diğer sayfalara taşınmaz. İçine yalnız üst/alt görsel düzen yazılır. Mockup değişince birlikte düzenlenir. Değişmezse sayfalar arasında tutarsız üstbilgi oluşur.

**`autoload.php` · `composer.json` · `composer.lock`**
> **eli10 (basit):** Sınıfları yükleyen dosya ve paket listesi+kilidi.
> **eli15 (detay):** Üçü ayrıdır çünkü biri çalışma anında okunur, biri niyeti, biri sürümü anlatır. Okunmaları bağımlılığı ve test sürümünü (phpunit ^10.5) gösterir. Yalnız `composer update` ile değişirler. Elle yazılırsa farklı makinelerde farklı kurulum doğar.

**`config/`**
> **eli10 (basit):** Ayarların ve gizli anahtarların tutulduğu klasör.
> **eli15 (detay):** Ayrıdır çünkü ayar, koddan daha sık değişir ve gizli içerir. İçine uygulama ayarı, bootstrap, sabit ve ortam dosyası girer. `.env` içeriği hiçbir yere (vault/log) yazılmaz. Ayar değişince yalnız burası güncellenir.

**`include/`**
> **eli10 (basit):** Home'un kendi iş kurallarının bulunduğu klasör.
> **eli15 (detay):** Ayrıdır ki sayfa dosyaları yalnız görünüm kalsın. İçine kimlik köprüsü, bileşen sınıfları, konteyner, oturum, veri kaynağı ve yayın (stream) kodu girer. Okunması katman sınırını gösterir. Home davranışı değişince buraya dokunulur.

**`pages/`**
> **eli10 (basit):** Ekrana basılan sayfalar ve onların parçaları.
> **eli15 (detay):** Ayrıdır çünkü görünüm ile iş mantığı farklı hızda değişir. İçine HTML çıkısı ve parça çağrıları girer, sorgu girmez. Mockup değişince burası güncellenir. Mantık buraya yazılırsa sayfa okunmaz hâle gelir.

**`tests/`**
> **eli10 (basit):** Kodun doğru çalıştığını kanıtlayan dosyalar.
> **eli15 (detay):** Ayrıdır ki kod değiştiğinde test yalnızca doğrulasın. İçine bileşen, yerleşim ve yükleyici soruları girer. Yeni bileşen gelince test de eklenir. Kapı yeşil olmadan iş bitmiş sayılmaz.

**`phpunit.xml` · `.htaccess` · `web.config` · `.phpunit.cache/`**
> **eli10 (basit):** Test ayarı ve iki sunucunun yönlendirme kuralları.
> **eli15 (detay):** Ayrıdırlar çünkü sunucu kuralı ile uygulama kodu farklı hızda değişir. Apache ile IIS farklı okur; ikisi de ayrı dosyada durur. `.phpunit.cache` üretilen artıktır, elde düzenlenmez. Değişiklik yalnız sunucu/test kuralında.

**`vendor/`**
> **eli10 (basit):** Başkalarının yazdığı, kurulmuş kutu — dokunulmaz.
> **eli15 (detay):** Ayrı klasör karantina gibidir: elle girilirse güncellemede kaybolur. İçine 1815 dosyalık bağımlılık kurulumu girmez, sadece okunur. Yalnız `composer install` yönetir. Sayısı bilgi amaçlıdır; içerik iddiası yapılmaz.

**`CLAUDE.md` · `AGENTS.md` · `CONTEXT.md` · `WORKFLOW.md`**
> **eli10 (basit):** Klasörün kural, rol, bilgi ve süreç defterleri.
> **eli15 (detay):** Dört dosya ayrıdır çünkü kural, rol, bilgi ve süreç farklı hızda değişir. Okunmaları işe sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Mevcut ikisi bu üretimde okundu, değiştirilmedi.

### 3.2 `include/` Alt Katman Detay Tablosu (8 alt dizin — disk ölçümü)

| Alt dizin | Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-----------|-------|--------------------|---------------|-----------|---------------------|
| `Auth/` | 1 | `HomeAuthBridge` — auth ile köprü | `HomeAuthBridge.php` | Kimlik doğrulaması tek yerde | Auth akışı değişince |
| `Class/` | 3 | Bileşen altyapısı | `AbstractComponent`, `ComponentLoader`, `HomeLayoutVariant` | Bileşen yaşam döngüsü + tier varyantı | Bileşen iskeleti değişince |
| `Component/` | 5 | Somut bileşenler | `HomeSongButton`, `PlayerInfoComponent`, `RecentTracksComponent`, `WelcomeBannerComponent`, `WidgetGridComponent` | Tekrar eden UI parçaları kodlansın | Bileşen davranışı değişince |
| `Container/` | 1 | DI konteyneri | `HomeContainer.php` | Bağımlılıklar tek yerde | Bağımlılık eklenince |
| `Interfaces/` | 1 | Sözleşme | `ComponentInterface.php` | Bileşen sözleşmesi sabit kalsın | Sözleşme değişince (ADR) |
| `Repository/` | 1 | Veri erişimi | `MusicRepository.php` | Sorgular sayfadan ayrı | Sorgu/şema değişince |
| `Session/` | 1 | Oturum | `HomeSessionManager.php` | Oturum tek kapıdan (AGENTS kuralı 2) | Oturum davranışı değişince |
| `Stream/` | 1 | Yayın akışı | `MusicStreamHandler.php` | Medya akışı izole | Akış protokolü değişince |

**eli10 / eli15 blokları (§3.2 — 8 alt dizin):**

**`Auth/`**
> **eli10 (basit):** Giriş bilgisini doğrulayan köprü dosyası.
> **eli15 (detay):** Ayrıdır çünkü kimlik işi güvenlik yüzeyidir. İçine tek dosya girer: auth ile konuşma. Auth akışı değişince burası güncellenir. Başka yere taşınırsa oturum kırılır.

**`Class/`**
> **eli10 (basit):** Bileşenlerin ortak davranışını kuran sınıflar.
> **eli15 (detay):** Ayrıdır çünkü ortak davranış tek yerde durur. İçine soyut bileşen, yükleyici ve tier varyantı girer. Yeni tier eklenince buraya dokunulur.

**`Component/`**
> **eli10 (basit):** Ekrandaki tekrar eden parçaların kodu.
> **eli15 (detay):** Ayrıdır çünkü her parça kendi dosyasında yaşar. İçine 5 somut bileşen girer. Parça değişince yalnız o dosya değişir.

**`Container/`**
> **eli10 (basit):** Bağımlılıkları hazırlayıp dağıtan kutu.
> **eli15 (detay):** Ayrıdır ki kurulum tek yerden yapılsın. İçine konteyner kodu girer. Bağımlılık eklenince güncellenir.

**`Interfaces/`**
> **eli10 (basit):** Bileşenlerin uyması gereken sözleşme.
> **eli15 (detay):** Ayrıdır çünkü sözleşme koddan uzun yaşar. İçine tek interface girer. Değişikliği ADR gerektirir.

**`Repository/`**
> **eli10 (basit):** Veritabanından okuma yeri.
> **eli15 (detay):** Ayrıdır çünkü sorgu sayfada yazılmaz. İçine müzik veri kaynağı girer (PDO, prepared — ADR-002). Şema değişince güncellenir.

**`Session/`**
> **eli10 (basit):** Oturumu yöneten tek kapı.
> **eli15 (detay):** Ayrıdır çünkü oturum tüm sayfaları etkiler. İçine oturum yöneticisi girer. Güvenlik kuralı değişince (ADR-011) güncellenir.

**`Stream/`**
> **eli10 (basit):** Ses yayınını taşıyan dosya.
> **eli15 (detay):** Ayrıdır çünkü yayın akışı ağ işidir, sayfa işi değildir. İçine stream handler girer. Akış protokolü değişince dokunulur.

### 3.3 `pages/` ve `tests/` Dosya Listesi (disk ölçümü)

| Grup | Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|------|-------|--------------------|---------------|-----------|---------------------|
| `pages/` kök | 3 | Ana sayfa, sağlık ucu, yönlendirme | `home.php`, `health.php`, `redirect.php` | Sayfa sunumu + canlılık kontrolü | Sayfa/mockup değişince |
| `pages/components/` | 4 | Sayfa içi parçalar | `player-info`, `recent-tracks`, `welcome-banner`, `widget-grid` | Parçalar sayfa ile birlikte basılır | Parça görünümü değişince |
| `tests/Unit/Component/` | 2 | Bileşen testi | `HomeSongButtonTest`, `RecentTracksComponentTest` | Bileşen davranışı sabit | Bileşen değişince |
| `tests/Unit/Layout/` | 1 | Tier varyantı testi | `HomeLayoutVariantTest` | Tier kararı sınanır | Tier değişince |
| `tests/Unit/Loader/` | 1 | Yükleyici testi | `ComponentLoaderTest` | Bileşen yükleme sınanır | Yükleyici değişince |
| `tests/Support/`` | 1 | Test sahtesi | `FakeComponent.php` | Bağımlılığı taklit etmek | Test altyapısı değişince |

**eli10 / eli15 blokları (§3.3 — 6 grup):**

**`pages/` kök (3 dosya)**
> **eli10 (basit):** Ana sayfa, sağlık kontrolü ve yönlendirme dosyaları.
> **eli15 (detay):** Ayrıdır çünkü her biri ayrı iş görür: gösterim, kontrol, geçiş. İçine sayfa kodu girer. Mockup ya da sağlık kuralı değişince güncellenir.

**`pages/components/` (4 dosya)**
> **eli10 (basit):** Sayfanın içinde parça parça basan dosyalar.
> **eli15 (detay):** Ayrıdır ki parçalar ana sayfa dosyasını büyütmeyesin. İçine parça çıkısı girer. Parça tasarımı değişince o dosya değişir.

**`tests/Unit/*` (4 dosya)**
> **eli10 (basit):** Bileşen, yerleşim ve yükleyicinin sınavı.
> **eli15 (detay):** Ayrıdır ki test, kod değişince yalnız doğrulasın. İçine sorular girer. Kapı yeşil olmadan iş bitmez.

**`tests/Support/` (1 dosya)**
> **eli10 (basit):** Testlerde sahte bileşen — gerçek veriye dokunmaz.
> **eli15 (detay):** Ayrıdır çünkü test gerçek servise bağlanmamalı. İçine taklit bileşen girer. Test altyapısı değişince güncellenir.

### 3.4 Komşu İlişkileri (home ↔ sistem)

| Komşu | Yön | Ne taşır (disk kanıtı) | eli10 | eli15 |
|-------|-----|------------------------|-------|-------|
| `../auth.coremusic.net` | home → auth | `include/Auth/HomeAuthBridge.php` (dosya var); AGENTS: auth_callback ile oturum kurulumu ⚠️ VERIFICATION REQUIRED (`pages/auth_callback.php` diskte YOK) | Giriş işini auth servisine devreder. | Köprü dosyası home'un auth ile tek temas noktasıdır; iş oraya taşınırsa oturum güvenliği dağılır. AGENTS'ın andığı `auth_callback.php` diskte yok — iddia değil, çelişki kaydı (§3.5). |
| `../assets.coremusic.net` | home → assets | `Css/` (ITCSS) + `js/` (Vanilla ES6) sayfa şablonlarına `<link>`/`<script>` ile bağlanır ⚠️ VERIFICATION REQUIRED (başlık etiketleri bu görevde okunmadı) | Görünüm buradan gelir. | Home CSS/JS üretmez, yalnız çağırır. Asset yolu değişirse sayfa bozulur; bu yüzden cihaz haritası `devices.config.js` + `DeviceCssMap.php` üzerinden yürür (assets CONTEXT). |
| `../shared` | home → shared | `composer` üzerinden ortak katman: Config, Bootstrap, Device, PageRouter (index.php başlık yorumu: Shared SPA Router) | Ortak altyapı shared'den gelir. | Home yalnız kendi işini tutar; router ve config ortaktır. shared değişirse home kodu değil, shared kodu değişir. |
| `../api.coremusic.net` | home → api | ⚠️ VERIFICATION REQUIRED — home içinde `/api/v1` çağıran kod bu görevde okunmadı; yön komşuluk olarak yazılır | Veri API'den gelir (iddia değil, komşuluk). | API uç sözleşmesi değişirse home etkilenir; ancak çağrı kanıtlanmadığı için kesin bağımlılık iddia edilmez. |
| `../.ai` | home → vault | `.ai/ui-design/01-mockup-index.md` (mockup kapısı), `.ai/.templates/backend/php-template.md` (yeni PHP dosyası) | Kural ve şablon vault'tan. | Mockup okunmadan görünüm kodu yazılmaz; şablonsuz dosya üretilmez (Guardrail #16). |

**eli10 / eli15 blokları (§3.4 — 5 komşu):**

**auth köprüsü**
> **eli10 (basit):** Giriş doğrulaması için tek dosyalık köprü.
> **eli15 (detay):** Ayrıdır çünkü kimlik işi güvenlik yüzeyidir; dağınık yazılırsa açık doğar. İçine auth ile konuşma girer. Auth akışı değişince güncellenir. Köprüsüz home kendi kimlik mantığını icat eder (yasak).

**assets çağrısı**
> **eli10 (basit):** Görünüm dosyaları başka alt alan adından gelir.
> **eli15 (detay):** Ayrıdır çünkü stil/script üretimi assets'tedir; home yalnız çağırır. İçine sayfa `<link>`/`<script>` bağları girer. Yol değişince sayfa bozuk görünür. Üretim home'da yapılmaz.

**shared altyapı**
> **eli10 (basit):** Ortak motor (router, ayar, cihaz) shared'de durur.
> **eli15 (detay):** Ayrıdır ki ortak kod tek yerde olsun. Home yalnız çağırır. shared değişirse home kendi kodunu değiştirmez. Tekrar eden ortak kod home'a taşınmaz.

**api yönü**
> **eli10 (basit):** Veri nereden geliyor — henüz kanıtlanmadı.
> **eli15 (detay):** Bu satır iddia değil, komşuluk yönüdür; home kodu bu görevde tam okunmadığı için işaretlendi. Kanıt gelince tablo güncellenir. Kesin bağımlılık gibi kullanılamaz.

**vault**
> **eli10 (basit):** Kurallar, şablonlar ve mockup'lar vault'ta durur.
> **eli15 (detay):** Ayrıdır çünkü kural koddan uzun yaşar. Okunması sırayı ve yasakları gösterir. Mockup okunmadan görünüm kodu yazılmaz. Şablonsuz dosya üretilmez.

### 3.5 Vault ↔ Disk Çelişkileri (disk kazanır; uydurulmaz)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `home.coremusic.net/AGENTS.md` §2: `pages/` içinde `auth_callback.php` | **diskte YOK** (pages = home, health, redirect + components/4) | ⚠️ VERIFICATION REQUIRED |
| `home.coremusic.net/AGENTS.md` §2: `pages/` içinde `player`, `ayarlar` | **diskte YOK** | ⚠️ VERIFICATION REQUIRED |
| `home.coremusic.net/AGENTS.md` §5: `.ai/subdomains/home.coremusic.net/index.md` | **diskte YOK** (`.ai/.subdomains/` altında yalnız `CLAUDE.md` var) | ⚠️ VERIFICATION REQUIRED |
| `home.coremusic.net/CLAUDE.md` §2: `pages/home.php` v10.0.0 · `header.php` v8.0.0 · `footer.php` v11.0.0 | Dosyalar VAR; **sürüm satırları bu görevde okunmadı** | ⚠️ VERIFICATION REQUIRED |
| `home.coremusic.net/CLAUDE.md` §2: `DeviceManager.php` v2.0.0 (`shared/src/Device/`) | Dosya yolu `shared/src/Device/` içinde VAR; sürüm doğulanmadı | ⚠️ VERIFICATION REQUIRED |
| `composer.json` `scripts` bloğu beklentisi (test kapısı) | **`scripts` YOK** — test `phpunit.xml` + `require-dev phpunit ^10.5` üzerinden | doğrudan okundu |
| `home.coremusic.net/CONTEXT.md` kök varlığı | Bu dosya ilk kez üretiliyor (öncesi YOKTU) | bu üretim |

**eli10 / eli15 (§3.5 bloğu):**

> **eli10 (basit):** Eski kâğıtlardaki bazı satırlar diskteki hâlle uyuşmuyor; uyuşmayan taraf her zaman disk.
> **eli15 (detay):** Bu tablo, klasör dokümanlarındaki iddialar ile gerçek dosya listesini karşılaştırır. İçine yalnız kanıtlanmış fark girer, tahmin girmez. Okunması, eski iddiayı doğru sanmayı engeller. Fark bulunca vault tarafı düzeltilir; disk değiştirilmez. Silinmiş dosyalar geri getirilmez, yok olarak yazılır.

### 3.6 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Her dosyanın tek işi | Dosya büyümez, sahibi belli kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık, tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı yayılmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme bizim kodu bozar |

> **eli10 (basit):** Bilgiler küçük kutulara bölünmüş; bir kutuyu değiştirmek diğerine zarar vermesin diye.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: ayar `config/`'te, mantık `include/`'ta, görünüm `pages/`'te, soru `tests/`'te durur. Böylece bir yer değiştirince sadece o değişir. Mantık görünümün içine gömülseydi her mockup değişikliği kod revizyonu sayılır, inceleme imkânsızlaşır. Vendor ayrı olunca güncelleme bizim kodumuzu bozmaz.

---

## 4. Kurallar

Klasör kuralı kaynakları (bu üretimde okundu, **değiştirilmedi**): [[CLAUDE.md]] + [[AGENTS.md]].

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | Kod öncesi Faz 1 keşif — onaysız değişiklik yok | Yanlış dosyaya yazımı engeller | [[../AGENTS.md]] §4 |
| 2 | `config/.env` içeriği vault/log/chat'e yazılmaz | Secret sızıntısı | home `AGENTS.md` §4 Yasak 1 |
| 3 | Oturum işlemleri yalnız `HomeSessionManager` üzerinden | Çift oturum kaynağı doğmasın | home `AGENTS.md` §4 Zorunlu 2 |
| 4 | Kimlik doğrulama akışı `HomeAuthBridge` dışına taşınmaz | Güvenlik yüzeyi dağılmasın | home `AGENTS.md` §4 Zorunlu 3 |
| 5 | `vendor/` klasörüne elle müdahale yok | Composer güncelleme kaybı | home `AGENTS.md` §4 Yasak 3 |
| 6 | Mockup'ta olmayan UI öğesi eklenmez | Kanonik mockup bozulması | home `AGENTS.md` §4 Yasak 4 + [[../.ai/ui-design/01-mockup-index]] |
| 7 | Yeni PHP dosyası `php-template.md`'den türetilir | Vault standardı (Guardrail #16) | [[../.ai/.templates/backend/php-template]] |
| 8 | Aynı dosya görevde 2. kez okunmaz · 3 başarısız düzeltme → DUR | Anti-overthink | [[../AGENTS.md]] §5 |
| 9 | **Commit subagent ATMAZ** | Tarih/entegrasyon orkestratörde | [[../AGENTS.md]] §7/§8 |

> **eli10 (basit):** Bu kurallar işin sırayla ve güvenli yürümesini, gizli bilginin dışarı çıkmamasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanmasını garantiler. İhlalde revert + `log.md` kaydı işletilir; kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: CLAUDE + AGENTS + CONTEXT oku] → [K2: hedef dosyayı 1. kez oku]
  → UYGULA (config/ | include/ | pages/) → [K3: phpunit yeşil]
  → (UI etkisi varsa K4: BROWSER — mockup ile karşılaştırma)]
  → [K5: RAPOR — commit ATMAZ] → ORKESTRATÖR
```

Adım ve kapıların tamamı [[WORKFLOW.md]] §3.1 / §3.2'dedir; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm sırası | §1–§7, ≤3 başlık seviyesi |
| 3 | Envanter | Kök 12 · config 5 · include 14 · pages 7 · tests 5 · vendor 1815 — disk ölçümü |
| 4 | Çelişki | §3.5 eksiksiz; her fark `VERIFICATION REQUIRED` ile işaretli |
| 5 | Placeholder | Dosyada doldurulmamış şablon değişkeni kalmadı (arama deseni: iki parantez + harf) |
| 6 | Wiki-link | Wiki-link biçimi (çift köşeli parantez) kullanıldı; hedefler diskte var (olmayanlar §3.5'te) |
| 7 | eli10 + eli15 | §3.1/§3.2/§3.3/§3.4/§3.5/§3.6 + §4 bloklarında etiketli blok var |
| 8 | Halüsinasyon | Diskte olmayan dosya var sayılmadı (`auth_callback.php` → YOK olarak yazıldı) |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md` / `AGENTS.md` değiştirilmedi |
| 10 | Emoji | Dekoratif emoji yok (yalnız `VERIFICATION REQUIRED` işaretçisi) |
| 11 | Uzunluk | 300–500 satır |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Mevcut kural dokümanı (OKUNDU, değişmedi) |
| Klasör rolleri | [[AGENTS.md]] | Routing + envanter iddiaları (çelişki kaynağı §3.5) |
| Klasör süreç | [[WORKFLOW.md]] | Adım/kapı akışı |
| Kök master | [[../AGENTS.md]] | §1 kapı · §4 keşif · §5 anti-overthink · §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Template registry | [[../.ai/.templates/index]] | Şablon envanteri |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | Bu dosyanın iskeleti (Guardrail #16) |
| PHP şablonu | [[../.ai/.templates/backend/php-template]] | Yeni PHP dosyası |
| Mockup indeksi | [[../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend |
| Auth context | [[../auth.coremusic.net/CONTEXT.md]] | Köprü hedefinin envanteri |
| Assets context | [[../assets.coremusic.net/CONTEXT.md]] | CSS/JS sağlayıcının envanteri |
| shared context | [[../shared/CONTEXT.md]] | Ortak katman envanteri |
| api context | [[../api.coremusic.net/CONTEXT.md]] | JSON uç tarafı (komşuluk) |
| Disk kanıtı | `home.coremusic.net/` | Bu dokümandaki tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** context
