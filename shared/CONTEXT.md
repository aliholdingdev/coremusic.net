---
title: "CoreMusic — shared Klasör Context"
type: docs
category: shared
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# shared — CONTEXT.md

**docType:** context · **Klasör:** `shared/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`shared/`, CoreMusic'in ortak PHP altyapısıdır: cache, config, database, middleware, PageRouter, security, session, event ve API katmanı burada yaşar ve `api/` `auth/` `home/` alt alan adları tarafından tek kaynaktan kullanılır. Bu doküman klasörün envanterini, mimari haritasını ve komşu klasörlerle konuşmasını disk kanıtıyla tanımlar.

| Karar | Kaynak (disk) |
|-------|---------------|
| Paylaşılan altyapı paketi (tek kütüphane) | `shared/composer.json` → `coremusic/shared-infrastructure` v2.0.0, `type: library` |
| PHP >= 8.4 + PSR-4 `CoreMusic\` → `src/` | `shared/composer.json` `require` + `autoload` |
| Route tanımları ortak | `shared/config/routes.php`, `shared/config/auth-routes.php` |
| Domain haritası 9 subdomain | `shared/AGENTS.md` §2 envanter (`config/domain.php`) |
| PDO yalnız, ORM yok (ADR-002) | `shared/AGENTS.md` §4 Zorunlu #1 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `shared/` kök envanteri + alt dizin haritası | `api/` `auth/` `home/` kendi iç yapıları → ilgilerinin `CONTEXT.md`'si |
| Alt alan adlarının shared'e bağımlılığı (composer path repo) | `assets/` statik varlıklar (composer bağımlılığı yok) |
| shared ↔ alt alan adı konuşma haritası | `.ai/` vault içeriği (SSOT, okunur ama burada üretilmez) |
| Alt dizin sorumluluk özeti | Kod içi satır/sınıf dokümantasyonu |

- **Kullananlar:** Backend Architect (src/), Security Engineer (Middleware/Security/Session/OAuth), Data Engineer (Database/migrations), QA Engineer (tests/), MO (bu doküman).
- **Ön koşul:** Klasör diskte mevcut ve `composer.json` okunmuş olmalı; sayısal iddiası olmayan satır `UNKNOWN` taşır.

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1 — disk ölçümü)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `src/` | Ortak PHP kodunun tek evi | 20 alt dizin: AI, Api, Bootstrap, Cache, Component, Config, Contracts, Database, Device, Events, Exception, Log, Middleware, OAuth, PageRouter, Repository, Security, Session, Theme, ViewMode | Alt alan adları aynı kodu 2. kez yazmasın | Ortak davranış eklenince/çıkınca | Burası her sitenin ortak el aleti kutusu; tek yerde durur ki aynı alet iki kez üretilmesin. | Alt alan adların hepsi aynı cache, aynı router ve aynı güvenlik kodunu kullanır. Ayrı ayrı taşinsa bir yerde güvenlik açığı kapatilir, öbüründe kapanir ve tutarsızlik doğar. Kod tek dosyada (tek dizinde) toplandığı için bakım ve inceleme tek yerde biter. Yeni ortak yetenek eklerken ya da bir alt alan adı davranışı değiştirdiğinde buraya dokunursun. |
| `config/` | Route + domain + OAuth sağlayıcı tanımları | `routes.php`, `auth-routes.php`, `domain.php`, `oauth-platforms.php`, `CLAUDE.md` | URL ve domain listesi tek kaynaktan yönetsin | Yeni route/domain/oauth sağlayıcı eklenince | Tüm adresler ve alan adları tek defterde yazılıdır; adres değişince sadece o defter değişir. | Route'lar hem API hem auth hem home tarafından okunur; dağınık olsa her servis kendi adres listesini tutar ve kullanıcı yanlış adrese gider. Domain haritası tek olunca alt alan adı ekleme tek satırlık iş olur. Defteri yalnız yeni adres/domain eklerken düzenlersin. |
| `database/` | Şema hareketleri (migration) | `migrations/` (alt dizin `CLAUDE.md` taşıyan) | Veritabanı değişimi izlenebilir olsun | Şema değişince (Data Engineer) | Veritabanı tablolarını büyütüp küçülten adım dosyalarının kutusu. | Migration ayrı durur ki "ne değişti, ne zaman" sorusu kayıtla cevaplanır. Aynı dosyada kodla karışsa geri alma imkânsızlaşır. Şema değişikliği olduğunda oraya eklersin; kod içinde SQL dağıtmazsın. |
| `tests/` | Ortak katman testleri (PHPUnit) | `Api/`, `Events/`, `OAuth/`, `Unit/` (Config, Device, PageRouter, Security) + `phpunit.xml` kökte | Ortak kod bozulursa test yakalasın | Ortak kod değişince (QA Engineer) | Kodun doğru çalıştığını sınıfta sınayan soru kâğıtları. | Test ayrı olunca kod değişince soru kâğıtları kodun yerine geçmeden doğrular. İçeride olsa test ile kod birlikte değişir, "eski davranış korundu mu" sorusu cevapsız kalır. Yeni ortak davranış eklerken test de oraya eklenir. |
| `composer.json` | Paket tanımı + bağımlılıklar + scriptler | ad, version 2.0.0, require (php-di, nyholm/psr7, respect/validation...), `scripts.test`, `scripts.stan` | Diğer 3 servis `../shared`'i path repo ile bağlasın | Bağımlılık/versiyon değişince | Paketin kimlik kartı ve içindeki alet listesi. | Composer ayrı bir dosyadır çünkü kurulum onu okur; koda gömülse kurulum kırılır. Alt alan adların `repositories: ../shared` bağlantısı bu karttan doğar. Yeni kütüphane eklerken ya da versiyon yükseltirken burayı düzenlersin. |
| `phpunit.xml` | Test koşu yapılandırması | suite tanımları, bootstrap | Testler tek komutla koşsun | Test kapsamı değişince | Sınavların hangi sırayla ve hangi sorularla sorulacağını yazan kâğıt. | Ayrı dosyadır çünkü test komutu onu okur; test koduna gömülse her koşuda değişiklik gerekir. Kapsam değişince (yeni klasör eklenince) düzenlersin. |
| `test_bypass.php` | bypass deneme/çözüm betiği | tek PHP dosyası | ADR-008 bypass akışını elle sınamak | Bypass davranışı değişince | Güvenlik kapısi atlatma denemesini tek dosyada deneyen yardımcı. | Ayrı durur ki bypass denemesi gerçek koda karışmasın. Güvenlik davranışı (ADR-008) değişince bakılır; kalıcı özellik olarak kodlanmaz. |
| `vendor/` | composer'ın kurduğu 3. taraf paketler | composer tarafından üretilir | Çalışma zamanı bağımlılıkları bulunsun | `composer install/update` (elle değil) | Kurulan hazır aletlerin kutusu; elle oynanmaz. | Ayrı ve dokunulmazdır; elle girilse güncellemede kaybolur ve güvenlik yamaları şaşar. Sadece composer yönetir, biz sadece okuruz. |
| `.phpunit.cache/`, `.phpunit.result.cache` | Test önbelleği (üretilmiş) | PHPUnit tarafından yazılır | Test koşusu hızlansın | Hiç (üretilmiş artefakt) | Sınav tekrarının bıraktığı geçici kayıt. | Geçicidir, elle düzenlenmez; anlamlı içerik taşımaz. Silinse PHPUnit yeniden üretir. |
| `AGENTS.md`, `CLAUDE.md` | Klasör kural/rol dokümanları (MEVCUT — dokunulmaz) | v2.0.1 agent registry + klasör kuralları | Bu klasörde kural tek yerde dursun | Vault senkronu (MO) | Bu klasörde çalışırken uyulacak kuralların ve kimin neye dokunacağının yazılı olduğu defter. | Ayrı dosyalardır çünkü kural ile kod farklı hızda değişir; kod içine gömülse her kod revizyonunda kural revize edilir. Yeni bir kural veya rol değişince vault senkronunda düzenlenir. Bu oturumda overwrite YASAKTIR. |

### 3.2 `src/` Alt Dizin Haritası (depth 1 — disk ölçümü: 20 dizin)

| Dizin | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|-------|--------------------|---------------|-----------|---------------------|-------|-------|
| `src/Api/` | JSON gateway + BFF + DTO + API middleware + registry + versioning | `Bff/`, `Dto/Request|Response/`, `Middleware/`, `Registry/`, `Versioning/` (envanter: `shared/AGENTS.md` §2) | API yüzeyi tek yerde dursun | API sözleşmesi değişince | API'nin kapı görevlisinin odası: istek girer, cevap JSON olur çıkar. | Ayrıldı çünkü API'nin kendi dili (sözleşme, sürüm, DTO) var; onu diğer kodla karıştırmak sözleşmeyi bozar. API kuralı değişince (yeni sürüm, yeni DTO) buraya dokunulur; ekran koduna girilmez. |
| `src/PageRouter/` | SPA sayfa yönlendirici (ADR-021/083) | Kernel, HtmlShellRenderer, AuthGuard, RouteRegistry, RequestNormalizer, ResponseEmitter... (`shared/AGENTS.md` §2: 14 dosya) | auth ve home aynı router'ı kullansın | Sayfa akışı değişince | Sayfaları kapı kapı dolaştıran ortak müdür. | Ayrıldı çünkü auth ile home aynı akışı paylaşır; heri ayrı yazsa adresler ve korumalar farklı davranır. Ortak olunca "hangi sayfa kime açık" tek yerden yönetilir. Router kuralı değişince burası değişir, iş mantığı değil. |
| `src/Middleware/` | HTTP middleware (Auth, BypassAuth ADR-008, CSRF ADR-010, RateLimiter ADR-013, SecurityHeaders, Session, CORS, Origin, Permission) | 10+ middleware sınıfı (envanter: `shared/AGENTS.md` §2) | Güvenlik kontrolleri tek zincirde dursun | Güvenlik kuralı değişince (Security Engineer) | İsteklerin geçmeden önce kapıdan geçtiği kontrol sırası. | Ayrıldı çünkü güvenlik sırası tek elden değişmeli; dağınık olsa bir kapı unutulur. Her istek buradan geçtiği için okunması kolaydır. Yeni kontrol eklenince ya ADR değişince düzenlenir. Kapsam genişletmek ADR'siz yasak. |
| `src/Security/` | CacheRateLimiter, ReturnUrlPolicy, SecurityHelper, SessionKeys, UriV7/UuidV7 | güvenlik yardımcı sınıfları | Güvenlik yardımcıları tek yerde dursun | Güvenlik kararı değişince | Kilit, anahtar ve kapı politikasının ortak aletleri. | Ayrıldı çünkü bu yardımcılar hem auth hem api hem home tarafından kullanılır; tek gövdede değişince herkes aynı güvenlik seviyesinde kalır. Güvenlik denetimi buradan okunur. Karar değişince (ADR) düzenlenir. |
| `src/Session/` | Oturum başlatma/yönetim (ADR-011) | SessionBootstrapper, Config, Initializer, Lifecycle (envanter: `shared/AGENTS.md` §2) | Aynı cookie/oturum davranışı her yerde aynı olsun | Oturum kuralı değişince | Oturumun anahtarını veren tek memur. | Ayrıldı çünkü oturum davranışı 3 serviste aynı olmalı; ayrı ayrı yazılsa kullanıcı sürekli çıkış yapar. Okunması, tüm servislerin aynı kurallı uyguladığını görmeni sağlar. Oturum kararı (ADR-011) değişince düzenlenir. |
| `src/Config/` | ConfigManager, DomainConfig, EnvParser (ADR-015), AuthRouteConfig | yapılandırma nesneleri | Ayarlar tek tip okunsun | Ayar formatı değişince | Ayarları toplayıp tek kutuda sunan kasa. | Ayrıldı çünkü ayar okuma biçimi her serviste aynı olmalı; farklı okusa biri http, diğeri https davranır. Okunması, hangi ayarın nereden geldiğini gösterir. Yeni ayar anahtarı eklenince düzenlenir. |
| `src/Cache/` | Apcu, Memory, PageCache adapter (ADR-007) | 3 adaptör (envanter: `shared/AGENTS.md` §2) | Önbellek tek arayüzle değişsin | Önbellek stratejisi değişince | Hızlı hatırlama kutuları; apcu yoksa belleğe düşer. | Ayrıldı çünkü önbellek motoru değiştirilse uygulama farkı görmesin. Arayüz sabit kaldığı için kod bozulmaz. Önbellek kararı (ADR-007) değişince düzenlenir. |
| `src/Database/` | DatabaseManager, DatabaseRegistry (ADR-003/022) + `Config/` | bağlantı/registrasyon sınıfları | PDO bağlantısı tek yerden açılsın | DB bağlantısı değişince | Veritabanına giden tek kapı. | Ayrıldı çünkü bağlantı tek elden kurulmazsa prepared statement kuralı (ADR-002) by-pass edilir. Kapı okununca hangi veritabanına gidildiği anlaşılır. Şema/ortam değişince düzenlenir; iş sorguları buraya yazılmaz. |
| `src/Contracts/` | Tek interface kok: Api, Events, AI, Auth, Config, Database, Middleware, Security | alt klasörlerde interface dosyaları | Katmanlar birbirine sözleşmelerle bağlansın | Sözleşme değişince (ilgili alt alan) | Modüllerin birbirine verdiği "şunu yapacaksın" kâğıtları. | Ayrıldı çünkü implementasyon değişse de sözleşme sabit kalsın; böylece bir taraf değişince diğeri kırılmaz. Okunması, modüller arası sınırı gösterir. Yeni yetenek sözleşmesi eklenince ya arayüz kaldırılınca düzenlenir. `src/Interfaces/` → `src/Contracts/` taşındı (envanter: `shared/AGENTS.md` §2). |
| `src/Events/` | EventDispatcher + domain/integration event (ADR-086) | `Domain/`, `Integration/` alt dizinleri | Olaylar gevşek yayılın | Yeni olay eklenince | Modüllerin birbirine "X oldu" dediği zil sistemi. | Ayrıldı çünkü gönderen, dinleyenin kim olduğunu bilmesin; zil sesi ayrı olunca kopar. Okunması, hangi olayların yayıldığını gösterir. Yeni domain event `Domain/`'e eklenir + testi yazılır (kural: `shared/AGENTS.md` §4). |
| `src/OAuth/` | OAuthManager + `Provider/` (12 sağlayıcı, envanter: `shared/AGENTS.md` §2) | Manager + sağlayıcı sınıfları | Sağlayıcılar tek arayüzle bağlansın | Sağlayıcı eklenince/değişince | Dış servislerle el sıkışma kuralları. | Ayrıldı çünkü her sağlayıcı kendi ufak farkını taşır; gövdeye karışsa temizlenemez. Okunması, hangi sağlayıcının nasıl bağlandığını gösterir. Sağlayıcı listesi değişince (`config/oauth-platforms.php` ile birlikte) düzenlenir. |
| `src/Bootstrap/` | RuntimeBootstrap.php | ortak çalışma zamanı kurulumu | Her istek aynı hazırlığı görsün | Başlangıç adımı değişince | Uygulamayı açmadan önce ışıkları yakan anahtar. | Ayrıldı çünkü başlangıç sırası hatalıysa tüm servisler birlikte çöker; tek dosyada olunca tek yerden düzelir. Başlangıç davranışı değişince düzenlenir. |
| `src/Log/` | LoggerFactory, FileHandler | log sınıfları | Kayıt tek biçimde alınsın | Log biçimi değişince | Günlük tutan ortak defter. | Ayrıldı çünkü log biçimi tek değilse olay geri izlenemez. Format değişince düzenlenir; hassas veri `[REDACTED]` kuralına uyar. |
| `src/Exception/` | 8 exception (BaseCoreMusicException hiyerarşisi, envanter: `shared/AGENTS.md` §2) | hata sınıfları | Hata tipi tek yerde tanımlansın | Yeni hata tipi eklenince | Hata türlerinin ortak sözlüğü. | Ayrıldı çünkü hata tipi dağınıksa yakalama (catch) tutmaz. Sözlüğe yeni hata eklenince düzenlenir; mevcut tiplerin adı değişmez (kırılan çağrılar için). |
| `src/Device/` | DeviceDetector, DeviceManager, DeviceCssMap | cihaz tanıma | CSS/JS seçimi cihaza göre yapılsın | Cihaz listesi değişince | Ekrana göre kapı seçen gözcü. | Ayrıldı çünkü cihaz bilgisi hem backend hem assets tarafından okunur; tek gövdede tutulur. Cihaz eklenince (`js/devices.config.js` ile senkron) düzenlenir. |
| `src/Theme/`, `src/ViewMode/` | ThemeManager (ADR-044), ViewModeManager (ADR-045) | tek dosya sınıflar | Tema/görünüm modu tek yerden yönetsin | Tema/mod değişince | Görünümü değiştiren iki anahtar. | Ayrıldı çünkü tema kararı ayrı, veri ayrıdır; karışsa renk değişimi veriyi bozar. Değişiklik talebinde düzenlenir. |
| `src/Component/`, `src/Repository/` | Diskte mevcut (depth-1 ölçüm) — envanter tablosunda yok | UNKNOWN (içerik okunmadı) | — | — | Bu iki klasör diskte var ama ne işe yaradığı envanterde yazmıyor. | Envanter ile disk arasındaki fark: `shared/AGENTS.md` §2 bu iki dizini saymıyor, disk sayıyor. İçerikleri okunmadan iddia yazılmaz (ZERO-HALLUCINATION); ilgili envanter güncellemesi MO'ya rapor edilir. |

### 3.3 Mimari Harita (istek akışı — disk kanıtı)

```text
api.coremusic.net ─┐
auth.coremusic.net ─┼─ composer path repo: repositories.url = "../shared" (symlink: true)
home.coremusic.net ─┘         │
                              ▼
                     shared/ (coremusic/shared-infrastructure ^2.0)
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
   config/ (route)       src/ (kod)          database/ (şema)
```

- **Kanıt:** `api.coremusic.net/composer.json`, `auth.coremusic.net/composer.json`, `home.coremusic.net/composer.json` → üçü de `repositories[0].url: "../shared"`, `options.symlink: true` ve `require: coremusic/shared-infrastructure: ^2.0`.
- **Ek kanıt (api → auth):** `api.coremusic.net/composer.json` `autoload` → `CoreMusic\Auth\` = `../auth.coremusic.net/include/`.
- **`media.coremusic.net` shared'e bağımlı DEĞİL** (kanıt: `media.coremusic.net/composer.json` → shared require yok).
- **`assets.coremusic.net` composer bağımlılığı YOK** (kanıt: `assets.coremusic.net/` kökünde `composer.json` yok; kök dizin listesi Css, Fonts, Image, js, tests + yapılandırma dosyaları).

### 3.4 Hangi Klasörlerle Konuşur?

| Komşu | Yön | Ne taşır (disk kanıtı) | eli10 | eli15 |
|-------|-----|------------------------|-------|-------|
| `../api.coremusic.net` | shared → api | Ortak kod + route + domain config (composer require) | API, adres defterini ve ortak aletleri shared'den alır. | API kendi yazdığıı yalnızca kapıyı (gateway) tutar; kimlik, hız sınırı ve adres listesi shared'den gelir. shared değişince API yeniden kurulum (composer) olmadan aynı kodu kullanır. Uyuşmazlık olursa shared kazanır çünkü tek kaynak odur. |
| `../auth.coremusic.net` | shared → auth | Ortak kod + `config/auth-routes.php` (index.php doğrudan okur) | Auth, ortak kapıları ve route defterini shared'den alır. | Auth'un `index.php`'si `shared/config/auth-routes.php` dosyasını doğrudan `require` eder (kanıt: `auth.coremusic.net/index.php` satır ~209). Ortak güvenlik sırası shared/Middleware'den gelir. Adres ya da güvenlik sırası değişince önce shared'e bakılır. |
| `../home.coremusic.net` | shared → home | Ortak kod + route + PageRouter | Home, aynı router ve aynı oturum kodunu kullanır. | Home'un işi veri ve ekrandır; yönlendirme ve oturum shared'den gelir. shared değişince home kodu değil, shared kodu değişir. Bu sınır korunmazsa home içinde ikinci bir router doğar. |
| `../assets.coremusic.net` | shared ↔ assets | `src/Device/DeviceCssMap` ↔ `js/devices.config.js` ikilisi senkron | Cihaz listesi iki yerde tutulur, birlikte değişir. | Backend cihazı tanımlar, frontend dosyayı seçer; ikisi ayrı yazılır ama birlikte güncellenir (kural: `assets.coremusic.net/AGENTS.md` §4). Biri değişince diğeri unutulursa cihazda yanlış CSS yüklenir. |
| `../media.coremusic.net` | — (bağımlılık yok) | shared require YOK | Media, shared kullanmayan bağımsız bir CLI. | `media.coremusic.net` composer dosyasında shared geçmez; medya tarama/denetim işi tek başına çalışır. Ortak kod ekleme isteği shared'e değil, media'ya ait değildir — sınırlar ayrıca tartışılır. |

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ölçü/renk tek yerde durur (`assets/Css/01_Abstracts/`) | Her dosyada ayrı değer → tutarsız görünüm, ölçü değişince tarama |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz | Her cihaz farkı tüm dosyalara yayılır → tekrar, drift |
| 5 | **Vendor karantinası** | 3. taraf kod `vendor/` + `Css/07_Vendors/`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: adresler tek defterde (`config/`), kod tek dizinde (`src/`), test tek yerde (`tests/`) durur. Böylece biri değişince sadece o değişir, servislerin geri kalanı bozulmaz. Dağınık olsa güvenlik sırası gibi kritik bir şey bir yerde unutulur. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | PDO prepared statement yalnız; DatabaseManager dışında DB bağlantısı açılmaz | SQL injection yüzeyini kapatır | `shared/AGENTS.md` §4 Zorunlu #1 (ADR-002) |
| 2 | `src/` içine subdomain'e özel iş kuralı gömülmez | Ortak katmanı kirletmez, bağımlılık çıkmaza girmez | `shared/AGENTS.md` §4 Yasak #1 |
| 3 | Middleware ekleme → `MiddlewarePipeline` kaydı | Kayıtsız middleware çalışmaz, kontrol atlanır | `shared/AGENTS.md` §4 Zorunlu #2 |
| 4 | Yeni domain event → `src/Events/Domain/` + test | Olay kaybolmaz, sözleşme test ile sabitlenir | `shared/AGENTS.md` §4 Zorunlu #3 |
| 5 | `BypassAuthMiddleware` kapsamı ADR ile değişir | Güvenlik deliği büyümez | `shared/AGENTS.md` §4 Yasak #2 (ADR-008) |
| 6 | ORM, `var`, `eval` yasak | Performans/güvenlik/PHP 8.4 tutarlılığı | `shared/AGENTS.md` §4 Yasak #3 |
| 7 | `config/domain.php` domain listesi onaysız değişmez | Tüm subdomain yönlendirmesi kırılır | `shared/AGENTS.md` §4 Yasak #4 |

> **eli10 (basit):** Bu kurallar, ortak kutunun içindeki aletlerin yanlış kullanılmasını engelliyor; hepsi tek bir sebepten var: herkes aynı güvenli yolu kullansın.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile kod farklı hızda değişir; kod içine gömülse her revizyonda kural aranmak zorunda kalır. Okunmaları, bu klasörde işe başlamadan önce sınırları görmeyi sağlar. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kural değişince (yeni ADR) yalnız bu satır ve ilgili kural dosyası güncellenir; kod sessizce değişmez.

---

## 5. Workflow

```text
GÖREV GELİR → İLGİLİ KURAL OKU (shared/CLAUDE.md + AGENTS.md)
  → DISK KANITI TOPLA (hedef dosyayı 1. kez oku)
  → KARAR VER (1. okumadan sonra)
  → DEĞİŞİKLİK (src/ | config/ | tests/)
  → DOĞRULAMA (composer test / composer stan)
  → RAPOR (commit atmaz — orkestratöre aittir)
```

| # | Adım | Neden | Atlarsan ne olur |
|---|------|-------|------------------|
| 1 | `shared/CLAUDE.md` + `shared/AGENTS.md` oku | Klasör kuralları orada (SSOT türevi) | Kural ihlali (ör. bypass kapsamı) |
| 2 | Hedef dosyayı 1. kez oku | Anti-overthink: ilk okumadan karar | Yanlış katmana kod, tekrar tekrar okuma |
| 3 | Değişikliği ilgili `src/` alt dizinine yap | Tek sorumluluk sınırı | `src/` içine iş kuralı sızması (Yasak #1) |
| 4 | Testi `tests/` altında ekle/koru | Regresyon kapısı | Sessiz kırılma, sonradan bulunması pahalı |
| 5 | `composer test` + `composer stan` | Statik analiz level 5 | Tip/analiz hatası production'a taşınır |
| 6 | Rapor yaz, commit ATMA | Subagent commit atmaz (Guardrail) | Yetkisiz tarih, revert riski |

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | Envanter | `src/` 20 dizin + kök girdileri disk ölçümüyle uyumlu |
| 5 | Disk kanıtı | Bağımlılık iddiaları composer.json ile kanıtlı |
| 6 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 7 | eli10 + eli15 | §3 tablo satırları + §4/§5 bloklarında etiketli blok var |
| 8 | Halüsinasyon | `src/Component/`, `src/Repository/` içeriği `UNKNOWN` olarak işaretli |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md`/`AGENTS.md` değiştirilmedi |
| 10 | Emoji | Yalnız `[[...]]` referansı; dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasörde çalışırken uyulacaklar |
| Klasör rolleri | [[AGENTS.md]] | Kim hangi dosyaya dokunur (MEVCUT — okunur) |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Kök kurallar | [[../AGENTS.md]] | Master kurallar (SSOT kök) |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Fazlar ve kapılar |
| Mimari kararlar | [[../.ai/brain.md]] | ADR özeti |
| Paket tanımı | `shared/composer.json` | Bağımlılık + autoload kanıtı |
| Template kaynağı | `../.ai/.templates/frontend/context-template.md` | Bu dokümanın iskeleti (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
