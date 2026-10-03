---
title: "CoreMusic — auth.coremusic.net Klasör Context"
type: docs
category: domain
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# auth.coremusic.net — CONTEXT.md

**docType:** context · **Klasör:** `auth.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `auth.coremusic.net/` klasörünün **ne işe yaradığını** ve diskteki **gerçek** envanterini tanımlar: kimlik doğrulama servisi (ADR-043) — login, register, şifre sıfırlama, gender seçimi ve OAuth uçlarının hexagonal katmanlı PHP yapısı. Kapsam: auth = **auth subdomain** (tek kimlik kapısı). Tüm sayılar 2026-10-03 disk ölçümüdür; `CLAUDE.md`/`AGENTS.md` iddiaları ile disk arasındaki farklar §3.5'te işaretlenmiştir (çelişkide **disk kazanır**).

| Karar | Kaynak (disk) |
|-------|---------------|
| Tek giriş = `index.php` (Shared SPA Router / PageRouterKernel) | `auth.coremusic.net/index.php` başlık yorumu |
| Hexagonal katman: Container/Controller → Handler/Service → Repository ↔ Domain | `include/` alt dizin listesi (7 alt dizin) |
| Test kapısı phpunit ^10.5; `composer.json` `scripts` bloğu **YOK** | `composer.json` doğrudan okundu |
| Middleware 6 dosya: interface + pipeline + 4 güvenlik middleware'i | `include/Middleware/` dosya listesi |
| Klasör kuralları mevcut `CLAUDE.md` + `AGENTS.md` içinde | bu oturumda okundu, **DEĞİŞTİRİLMEDİ** |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `auth.coremusic.net/` kök + `config/`, `handler/`, `include/`, `pages/`, `routes/`, `tests/` envanteri | `vendor/` içeriği (composer üretimi — salt okunur) |
| Auth'un komşularla (home, shared, assets, api) konuşma haritası | `shared/src/Middleware|Session|Security` ortak katman (bağımlı olunan) |
| Vault ↔ disk çelişki kaydı (§3.5) | DB şema içeriği → `.ai/.sql/mysql/coremusic_auth.sql` (okunur, üretilmez) |
| Yeni CONTEXT/WORKFLOW iskeleti | Mevcut `CLAUDE.md` / `AGENTS.md` revizyonu (dokunulmaz) |

- **Kullananlar:** Backend Architect (birincil), Security Engineer (middleware/ADR-010/011/013), QA Engineer (`tests/`), MO (doküman).
- **Ön koşul:** `index.php` + `composer.json` 1. kez okunmuş; sayısal iddialar disk ölçümünden.
- **Gizli veri:** `config/.env` içeriği okunmaz, kopyalanmaz, vault'a yazılmaz (Secret Yok).

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1-2 — disk ölçümü 2026-10-03)

**Sayı gerçeği:** toplam **2035 dosya** (vendor dahil) · vendor hariç **69 dosya** · vendor hariç uzantı dağılımı: `.php 42` · `.md 18` · `.xml 1` · `.example 1` · `.env 1` · `.config 1` (web.config) · `.lock 1` · `.htaccess 1` · `.gitignore 1` · `.json 1` · `.cache 1`.

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `index.php` | Tek giriş kapısı (front controller) | autoload + `ConfigManager`/`DomainConfig`/`RuntimeBootstrap` kullanımı (ilk 15 satır okundu) | Tüm auth istekleri tek kapıdan geçsin | Routing/entry değişince (ADR) | Uygulamayı açan ana dosya. | Tek dosyadır çünkü giriş dağıksa hangi isteğin nereden geçtiği bilinmez. Okunması, ortak katmanı (shared bootstrap) nasıl çağırdığını gösterir. Değişiklik yalnız ADR'li sıra değişikliğinde yapılır. |
| `autoload.php`, `composer.json`, `composer.lock` | PSR-4 + paket tanımı | `name: bayramali/auth.coremusic.net` · `require-dev: phpunit ^10.5` · `scripts` **YOK** | Bağımlılık ve sınıf yükleyici tek yerde | `composer update` | Kitapları çeken dosyalar ve kimlik kartı. | Kilit (lock) ayrıdır ki her makine aynı sürümü kursun. `scripts` yoksa test phpunit ile koşulur. Elle düzenlenirse sınıf bulunamaz. |
| `config/` | Ayar + CORS + ortam | 5 dosya: `.env`, `.env.example`, `app.php`, `constants.php`, `cors.php` | Ayar koddan ayrı | Ayar/CORS değişince | Ortam, uygulama ve kapı (CORS) ayarlarının odası. | Ayrıdır çünkü ayar sık değişir; koda gömülse her ayar için kod revizyonu gerekir. CORS satırları güvenlik yüzeyidir (Security yetkisi). `.env` içeriği hiçbir yere yazılmaz. |
| `handler/` (kök) | OAuth POST işleyicisi | 1 dosya: `OAuthPostHandler.php` | OAuth form gönderimi izole | OAuth akışı değişince | OAuth formunun POST'unu karşılayan tek dosya. | Ayrıdır çünkü OAuth akışı sayfa akışından farklı hızda değişir. İçine yalnız OAuth POST mantığı girer. Sağlayıcı eklenince güncellenir. |
| `include/` | Auth'a özgü hexagonal kod | 32 dosya: 22 `.php` + 10 klasör-içi `CLAUDE.md` | Katmanlar (Controller/Handler/Service/Repository/Domain) ayrı dursun | Auth davranışı değişince | Katmanlı oda dizisi: giriş, akış, mantık, veri ve değer nesneleri. | Ayrıdır çünkü hexagonal sınırlar güvenlik denetimini mümkün kılar; hepsi tek dosyada olsaydı katman ihlali görünmez olur. İçine yalnız auth kodu girer, ortak kod `shared/`'edir. Değişiklik katman bazında yapılır. |
| `pages/` | Auth sayfaları | 7 `.php`: `login`, `register`, `forgot-password`, `reset-password`, `select-gender`, `set-gender`, `logout` | Görünüm ayrı; iş mantığı Service'te | Sayfa/mockup değişince | Kullanıcının gördüğü giriş/üyelik sayfaları. | Ayrıdır ki görünüm ile iş mantığı farklı hızda değişir. İçine yalnız form/HTML çıkısı girer; mantık yazılması yasak. Mockup değişince güncellenir. |
| `routes/` | OAuth route tanımı | 1 dosya: `oauth.php` | Uç adresleri tek yerde | Yeni uç eklenince | Adres defteri. | Ayrıdır çünkü adres, koddan daha sık değişir. İçine route kayıtları girer. Uç eklenince önce buraya yazılır. |
| `tests/` | PHPUnit testleri | 12 dosya: 6 `.php` (`bootstrap.php` + 5 test) + 6 klasör-içi `CLAUDE.md` | Değer nesnesi/DTO davranışı sabit | Kod değişince (QA) | Kodun doğruluğunu kanıtlayan dosyalar. | Test ayrıdır ki kod değişince test yalnız doğrulasın. Mevcut kapsam: `EmailTest`, `GenderTest`, `PasswordTest`, `UserTest`, `LoginRequestTest`. `Unit/{Service,Handler,Repository}` klasörleri diskte YOK (§3.5). |
| `phpunit.xml`, `.htaccess`, `web.config`, `.phpunit.result.cache`, `.gitignore` | Test + sunucu yapılandırması | 5 dosya | Test tek komutla; istekler `index.php`'ye düşsün | Sunucu/test kuralı değişince | Sınav kâğıdı, iki sunucunun yönlendirmesi, gizli dosya listesi. | Apache ile IIS farklı okur; ayrı dosya her ikisinde çalıştırır. Cache dosyası üretim artığıdır, elde düzenlenmez. `.gitignore` gizli dosyaları korur. |
| `vendor/` | Kurulu 3. taraf paketler | 1966 dosya (2035 − 69) | Çalışma zamanı hazır | `composer install` (elle değil) | Kurulan kutu, dokunulmaz. | Elle girilse güncellemede kaybolur. Sadece composer yönetir. Sayı yalnız bilgi amaçlıdır. |
| `CLAUDE.md`, `AGENTS.md`, `CONTEXT.md`, `WORKFLOW.md` + klasör-içi 16 `CLAUDE.md` | Klasör dokümanları | 2 kök (mevcut) + 2 kök (yeni) + 16 klasör-içi `CLAUDE.md` | Kural/katman notu yerinde tutulur | Vault senkronu (MO) | Klasörün defterleri ve alt klasör notları. | Kök dört dosya ayrıdır (bilgi, kural, rol, süreç). Klasör-içi `CLAUDE.md`'ler o katmanın yerel notudur. Mevcutları bu üretimde yalnız okundu — değiştirilmedi. |

**eli10 / eli15 blokları (§3.1'deki 10 maddenin karşılığı):**

**`index.php`**
> **eli10 (basit):** Uygulamayı açan tek kapı.
> **eli15 (detay):** Ayrıdır çünkü giriş noktası tek olunca istek yolu belli olur. İçine önce kitaplıklar, sonra ortak kurulum bağlanır. Okunması sırayı gösterir. Değişiklik yalnız ADR'li sıra değişikliğinde yapılır.

**`autoload.php` · `composer.json` · `composer.lock`**
> **eli10 (basit):** Sınıf yükleyici, paket listesi ve sürüm kilidi.
> **eli15 (detay):** Üçü ayrıdır çünkü biri çalışma anını, biri niyeti, biri sürümü anlatır. Test sürümü buradan okunur (phpunit ^10.5). Yalnız `composer update` ile değişir. Elle yazılırsa kurulum tutarsızlaşır.

**`config/`**
> **eli10 (basit):** Ayar, CORS ve gizli anahtarların odası.
> **eli15 (detay):** Ayrıdır çünkü ayar koddan sık değişir ve gizli içerir. İçine uygulama ayarı, sabit, CORS ve ortam dosyası girer. `.env` içeriği hiçbir yere yazılmaz. CORS değişince güvenlikçinin onayı gerekir.

**`handler/` (kök)**
> **eli10 (basit):** OAuth form gönderimini karşılayan dosya.
> **eli15 (detay):** Ayrıdır çünkü OAuth akışı sayfa akışından farklıdır. İçine yalnız POST mantığı girer. Sağlayıcı eklenince güncellenir. Sayfa dosyalarına yazılırsa akış dağılır.

**`include/`**
> **eli10 (basit):** Auth'un kendi katmanlı kodu.
> **eli15 (detay):** Ayrıdır çünkü hexagonal sınır güvenlik denetimini mümkün kılar. İçine konteyner, controller, handler, middleware, repository, service ve domain nesneleri girer. Okunması katman sırasını gösterir. Auth davranışı değişince buraya dokunulur.

**`pages/`**
> **eli10 (basit):** Kullanıcının gördüğü 7 giriş/üyelik sayfası.
> **eli15 (detay):** Ayrıdır ki görünüm ile mantık ayrı hızda değişir. İçine yalnız form ve HTML çıkısı girer; iş mantığı yazılması yasak. Mockup değişince güncellenir. Mantık buraya yazılırsa sayfa okunmaz hâle gelir.

**`routes/`**
> **eli10 (basit):** OAuth adreslerinin tutulduğu defter.
> **eli15 (detay):** Ayrıdır çünkü adres, koddan sık değişir. İçine route kayıtları girer. Yeni uç eklenince önce buraya yazılır. Kod içine gömülse her adres için kod revizyonu gerekir.

**`tests/`**
> **eli10 (basit):** Değer nesneleri ve isteklerin sınav dosyaları.
> **eli15 (detay):** Ayrıdır ki test, kod değişince yalnız doğrulasın. İçine 5 test + bootstrap girer. Yeni sınıf eklenince test de eklenir. Kapı (phpunit) yeşil olmadan iş bitmez.

**Yapılandırma dosyaları (`phpunit.xml` · `.htaccess` · `web.config` · cache · `.gitignore`)**
> **eli10 (basit):** Test ayarı, iki sunucunun yönlendirmesi ve gizlilik listesi.
> **eli15 (detay):** Ayrıdırlar çünkü sunucu/test kuralı koddan farklı hızda değişir. Apache ile IIS ayrı dosyada okunur. Cache artığı elde düzenlenmez. `.gitignore` gizli dosyaları korur.

**`vendor/`**
> **eli10 (basit):** Kurulmuş üçüncü taraf kutu — dokunulmaz.
> **eli15 (detay):** Ayrı klasör karantinadır; elle girilirse güncellemede kaybolur. İçine composer kurulumu girer, bizim kodumuz girmez. Yalnız `composer install` yönetir.

**Dokümanlar (kök + klasör-içi `CLAUDE.md`ler)**
> **eli10 (basit):** Klasörün ve alt katmanlarının kâğıtları.
> **eli15 (detay):** Kök dört dosya ayrıdır çünkü bilgi, kural, rol, süreç farklı hızda değişir. Klasör-içi notlar o katmanın yerel kuralını taşır. Okunmaları işe sırayla başlamayı sağlar. Mevcutlar bu üretimde değiştirilmedi.

### 3.2 `include/` Alt Katman Detay Tablosu (disk ölçümü)

| Alt dizin | PHP | CLAUDE.md | Dosyalar | Ne için | Ne zaman düzenlenir |
|-----------|-----|-----------|----------|---------|---------------------|
| `Container/` | 1 | 1 | `AuthContainer.php` | DI konteyneri | Bağımlılık eklenince |
| `Controller/` | 1 | 1 | `AuthController.php` | HTTP girişi (tüm route'lar) | Uç davranışı değişince |
| `Domain/` (+3 alt) | 8 | 4 | DTO: `AuthResponse`, `LoginRequest`, `RegisterRequest` · Entity: `User` · VO: `Email`, `Gender`, `Password`, `UserId` | İş kuralları ve değer nesneleri | Kural değişince (ADR) |
| `Handler/` | 3 | 1 | `AuthKeyRedirectHandler`, `AuthPostHandler`, `AutoRedirectHandler` | İstek akış yönlendirme | Akış değişince |
| `Middleware/` | 6 | 1 | `MiddlewareInterface`, `MiddlewarePipeline`, `OriginCheckMiddleware`, `RateLimitMiddleware`, `SecurityHeadersMiddleware`, `SessionMiddleware` | Kapılar (CORS, hız, başlık, oturum) | Güvenlik kuralı değişince (ADR ile) |
| `Repository/` | 1 | 1 | `UserRepository.php` | PDO kalıcılık (prepared — ADR-002) | Şema/sorgu değişince |
| `Service/` | 2 | 1 | `AuthService.php`, `SessionManager.php` | İş mantığı + oturum yaşam döngüsü | Auth işi değişince |
| **Toplam** | **22** | **10** | — | — | — |

**eli10 / eli15 blokları (§3.2 — 7 alt dizin):**

**`Container/`**
> **eli10 (basit):** Bağımlılıkları hazırlayıp dağıtan kutu.
> **eli15 (detay):** Ayrıdır ki kurulum tek yerden yapılsın. İçine konteyner kodu girer. Bağımlılık eklenince güncellenir. Dağınık kurulumda hangi nereden geliyor bilinmez.

**`Controller/`**
> **eli10 (basit):** HTTP isteklerini karşılayan tek masa.
> **eli15 (detay):** Ayrıdır çünkü giriş katmanı ayrıdır. İçine tek controller girer; tüm route'lar ondan geçer. Uç davranışı değişince güncellenir. İş mantığı buraya yazılmaz.

**`Domain/`**
> **eli10 (basit):** Kuralın kendisi: kullanıcı, e-posta, şifre, cinsiyet.
> **eli15 (detay):** Ayrıdır çünkü iş kuralı framework'ten bağımsız kalmalı (superengel erişimi yasak). İçine entity, değer nesnesi ve istek/yanıt nesneleri girer. Kural değişince ADR ile değişir. Değişmezse tutarsız veri doğar.

**`Handler/`**
> **eli10 (basit):** İsteği doğru akışa yollayan yönlendiriciler.
> **eli15 (detay):** Ayrıdır çünkü akış kararı koddan bağımsız düşünülür. İçine üç handler girer. Akış değişince güncellenir. Controller'a yazılırsa akış karmaşası doğar.

**`Middleware/`**
> **eli10 (basit):** Kapılar: origin, hız sınırı, başlık, oturum.
> **eli15 (detay):** Ayrıdır çünkü güvenlik kapıları ayrı denetlenir. İçine interface + pipeline + 4 kapı girer. Değişiklik ADR ile yapılır (ADR-010/011/013). Sıra değişirse kontrol atlanabilir.

**`Repository/`**
> **eli10 (basit):** Veritabanına yazma/okuma yeri.
> **eli15 (detay):** Ayrıdır çünkü sorgu başka katmanda yazılmaz. İçine tek repository girer; prepared statement kullanır (ORM yasak). Şema değişince güncellenir. Yazım burada değilse injection riski doğar.

**`Service/`**
> **eli10 (basit):** İş mantığı ve oturum yaşam döngüsü.
> **eli15 (detay):** Ayrıdır çünkü iş kuralı görünüm ile veri arasında durur. İçine login/register/logout mantığı ve oturum yöneticisi girer. İş değişince güncellenir. Sayfaya yazılırsa SRP ihlali olur.

### 3.3 Komşu İlişkileri (auth ↔ sistem)

| Komşu | Yön | Ne taşır (disk kanıtı) | eli10 | eli15 |
|-------|-----|------------------------|-------|-------|
| `../home.coremusic.net` | auth ← home | `include/Auth/HomeAuthBridge.php` (home tarafında; dosya var) | Home buradan giriş yapar. | Kimlik tek yerde üretildiğinden home yalnız köprü kurar. Uç davranış değişirse home etkilenir. |
| `../api.coremusic.net` | auth ← api | `api/composer.json` autoload → `../auth.coremusic.net/include/` (`CoreMusic\Auth\`) | API auth kodunu doğrudan kullanır. | PSR-4 yolu auth'un sınıf adlarını bağlar; sınıf adı değişirse gateway kırılır. Arayüz adları sabit tutulur. |
| `../shared` | auth → shared | `CoreMusic\Config\ConfigManager`, `DomainConfig`, `RuntimeBootstrap` (index.php kullanımı) + composer path repo | Ortak altyapı shared'den gelir. | Auth yalnız kendi işini tutar; config/bootstrap ortaktır. Ortak kod buraya kopyalanmaz. |
| `../assets.coremusic.net` | auth → assets | Login/register stilleri `Css/auth-bundled.css` + `Css/08_Devices/d-auth-*` (assets CONTEXT kanıtı) | Görünüm buradan gelir. | Auth CSS/JS üretmez, yalnız çağırır. Cihaz haritası `DeviceCssMap` üzerinden yürür. |
| `../.ai` | auth → vault | `.ai/.decisions/accepted/ADR-043-*.md`, `ADR-010`, `ADR-011` (VAR) · `.ai/.sql/mysql/coremusic_auth.sql` (VAR) | Karar ve şema vault'tan. | Yeni PHP dosyası `php-template.md`'den türetilir (Guardrail #16). Mimari klasör yolları §3.5'te düzeltildi. |

**eli10 / eli15 blokları (§3.3 — 5 komşu):**

**home köprüsü**
> **eli10 (basit):** Giriş işi bu serviste, home yalnız bağlanır.
> **eli15 (detay):** Ayrıdır çünkü kimlik tek yerde üretilir. İçine köprü çağrısı girer (home tarafında). Uç değişirse home etkilenir. Kimlik mantığı ikiye bölünmez.

**api autoload**
> **eli10 (basit):** API, auth sınıflarını doğrudan kullanır.
> **eli15 (detay):** Ayrıdır çünkü kod tekrarı olmasın diye composer ile bağlanır. İçine sınıf adları girer; ad değişirse gateway kırılır. Sınıf adları sabit tutulur. Bağlantı `composer.json` kanıtıdır.

**shared altyapı**
> **eli10 (basit):** Ortak kurulum ve yapı shared'de.
> **eli15 (detay):** Ayrıdır ki ortak kod tek yerde olsun. Auth yalnız çağırır. Tekrar eden ortak kod buraya taşınmaz. shared değişince auth kodu değil, shared değişir.

**assets görünümü**
> **eli10 (basit):** Giriş sayfasının görünümü assets'ten gelir.
> **eli15 (detay):** Ayrıdır çünkü stil üretimi assets'tedir. Auth yalnız çağırır. Cihaz ayarı `d-auth-*` ile ayrıdır. Üretim auth'da yapılmaz.

**vault**
> **eli10 (basit):** Kararlar, şema ve şablonlar vault'ta.
> **eli15 (detay):** Ayrıdır çünkü kural koddan uzun yaşar. ADR'ler okunur, metni kopyalanmaz. Şema oradan okunur. Yeni dosya şablondan türetilir.

### 3.4 `pages/` ve `tests/` Dosya Listesi (disk ölçümü)

| Grup | Dosya | Ne için | Ne zaman düzenlenir |
|------|-------|---------|---------------------|
| `pages/` | 7 | `login`, `register`, `forgot-password`, `reset-password`, `select-gender`, `set-gender`, `logout` | Sayfa/mockup değişince |
| `tests/` kök | 1 | `bootstrap.php` (test kurulumu) | Test altyapısı değişince |
| `tests/Unit/Domain/DTO/` | 1 | `LoginRequestTest.php` | DTO değişince |
| `tests/Unit/Domain/Entity/` | 1 | `UserTest.php` | Entity değişince |
| `tests/Unit/Domain/ValueObject/` | 3 | `EmailTest`, `GenderTest`, `PasswordTest` | VO değişince |
| `tests/Unit/{Service,Handler,Repository}/` | **0** | **diskte YOK** (beklenen, üretilmedi) | Test kapsamı genişletilince |

**eli10 / eli15 blokları (§3.4 — 6 grup):**

**`pages/` (7)**
> **eli10 (basit):** Giriş, üyelik, şifre ve cinsiyet sayfaları.
> **eli15 (detay):** Ayrıdır ki görünüm ile mantık ayrı kalır. İçine form ve çıkı girer. Mockup değişince güncellenir. Mantık buraya yazılmaz.

**`tests/` kök (1)**
> **eli10 (basit):** Testlerin açılış dosyası.
> **eli15 (detay):** Ayrıdır çünkü kurulum tek yerden yapılır. İçine bootstrap girer. Altyapı değişince güncellenir.

**`Unit/Domain/` (5 test)**
> **eli10 (basit):** Kural nesnelerinin sınavları.
> **eli15 (detay):** Ayrıdır ki kural sınaması koddan ayrı dursun. İçine sorular girer. Kural değişince test de değişir. Kapı yeşil olmadan iş bitmez.

**Eksik klasörler (`Unit/{Service,Handler,Repository}`)**
> **eli10 (basit):** Henüz yazılmamış test klasörleri — diskte yoklar.
> **eli15 (detay):** Yok olarak yazılırlar, var sayılmazlar. Kapsam genişletilince üretilirler. `CLAUDE.md` §10'da "Eksik" olarak anılırlar. Uydurma dosya listelenmez.

### 3.5 Vault ↔ Disk Çelişkileri (disk kazanır; uydurulmaz)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `auth/CLAUDE.md` §3: `AuthKeyRedirect.php`, `AuthPost.php`, `AutoRedirect.php` | Gerçek adlar: `AuthKeyRedirectHandler.php`, `AuthPostHandler.php`, `AutoRedirectHandler.php` | ⚠️ VERIFICATION REQUIRED (ad farkı) |
| `auth/CLAUDE.md` §3: `Middleware/Pipeline.php`, `OriginCheck.php`, `RateLimit.php`, `SecurityHeaders.php`, `Session.php` | Gerçek adlar: `MiddlewarePipeline.php`, `OriginCheckMiddleware.php`, `RateLimitMiddleware.php`, `SecurityHeadersMiddleware.php`, `SessionMiddleware.php` | ⚠️ VERIFICATION REQUIRED (ad farkı) |
| `auth/CLAUDE.md` §10: `UserIdTest` testi | **diskte YOK** (tests = Email, Gender, Password, User, LoginRequest) | ⚠️ VERIFICATION REQUIRED |
| `auth/CLAUDE.md` §9: `.ai/decisions/accepted/ADR-*` yolu | **YOK** — gerçek yol `.ai/.decisions/accepted/ADR-*` (ADR-043/011/010 VAR) | ⚠️ VERIFICATION REQUIRED |
| `auth/CLAUDE.md` §9: `.ai/architecture/08-auth/auth-flow.md` · `auth/AGENTS.md` §5: `08-auth/index.md` + `auth-domain.md` | `.ai/architecture/08-auth/` **YOK** (klasör mevcut değil) | ⚠️ VERIFICATION REQUIRED |
| `auth/CLAUDE.md` §6: `.ai/.subdomains/auth.coremusic.net/index.md` | **YOK** — `.ai/.subdomains/` altında yalnız `CLAUDE.md` var | ⚠️ VERIFICATION REQUIRED |
| `auth/AGENTS.md` §5: `shared/src/Middleware/AGENTS.md` | **YOK** (`shared/src/Middleware/` klasörü VAR, AGENTS.md yok) | ⚠️ VERIFICATION REQUIRED |
| `auth/CLAUDE.md` §3: `tests/Unit/Service/` (eksik) | `Unit/{Service,Handler,Repository}` klasörleri **YOK** — iddia ile disk uyumlu (eksik) | uyumlu |
| `auth/CLAUDE.md` §2: "Test kapsamı ValueObject + DTO" | 5 test dosyası VAR (VO 3 + Entity 1 + DTO 1) — Entity testi de var | ⚠️ özet eksik |
| `composer.json` `scripts` bloğu beklentisi | **`scripts` YOK** — test `phpunit.xml` + phpunit ^10.5 | doğrudan okundu |

**eli10 / eli15 (§3.5 bloğu):**

> **eli10 (basit):** Eski kâğıtlardaki bazı dosya adları ve yolları diskteki hâlle uyuşmuyor; her zaman disk kazanır.
> **eli15 (detay):** Bu tablo yalnız kanıtlanmış farkları taşır: dosya adları, test varlığı, vault yolları. İçine tahmin girmez. Okunması, eski adla dosya aramayı engeller. Fark bulunca vault tarafı düzeltilir; disk değiştirilmez, dosya uydurulmaz.

### 3.6 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Her dosyanın tek işi | Dosya büyümez, sahibi belli kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık, tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı yayılmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme bizim kodu bozar |

> **eli10 (basit):** Bilgiler küçük kutulara bölünmüş; bir kutuyu değiştirmek diğerine zarar vermesin diye.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: kapı `Middleware/`'de, iş `Service/`'te, kural `Domain/`'de, veri `Repository/`'de, görünüm `pages/`'te durur. Böylece bir yer değişince sadece o değişir. Güvenlik katmanı tek dosyada toplansaydı denetim imkânsızlaşır. Vendor ayrı olunca güncelleme bizim kodumuzu bozmaz.

---

## 4. Kurallar

Klasör kuralı kaynakları (bu üretimde okundu, **değiştirilmedi**): [[CLAUDE.md]] + [[AGENTS.md]].

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | ORM yasak — Repository yalnız PDO prepared statement | Injection yüzeyi (ADR-002) | auth `AGENTS.md` §4 Zorunlu 1 |
| 2 | Argon2id hash yalnız `Password` ValueObject içinde | Hash tek elden üretilsin | auth `AGENTS.md` §4 Zorunlu 3 |
| 3 | Yeni PHP dosyası `php-template.md`'den türetilir | Vault standardı (Guardrail #16) | [[../.ai/.templates/backend/php-template]] |
| 4 | Yeni middleware → `MiddlewareInterface` + pipeline kaydı | Kapı sistemi bozulmasın | auth `AGENTS.md` §4 Zorunlu 5 |
| 5 | `.env` içeriği vault/log/chat'e yazılmaz · session/cookie log'a yazılmaz | Secret + gizlilik | auth `AGENTS.md` §4 Yasak 1/3 |
| 6 | Domain katmanından superglobal erişimi yasak · `pages/` içine iş mantığı yasak | Katman ihlali + SRP | auth `CLAUDE.md` §8 |
| 7 | Güvenlik yüzeyi (CORS/rate limit/header) → Security onayı | A1 sınırı | [[../AGENTS.md]] §5/§6 |
| 8 | Faz 1 keşif onaysız kod yok · aynı dosya 2. kez okunmaz · 3 başarısız → DUR | Anti-overthink | [[../AGENTS.md]] §4/§5 |
| 9 | **Commit subagent ATMAZ** | Tarih/entegrasyon orkestratörde | [[../AGENTS.md]] §7/§8 |

> **eli10 (basit):** Bu kurallar şifre, kapı ve katmanların doğru yerde kalmasını, gizli bilginin dışarı çıkmamasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü güvenlik metni koddan uzun yaşar; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanmasını garantiler. İhlalde revert + `log.md` kaydı işletilir.

---

## 5. Workflow

```text
GÖREV → [K1: CLAUDE + AGENTS + CONTEXT oku (+ADR gerekirse)]
  → [K2: hedef dosyayı 1. kez oku] → UYGULA (include/ | pages/ | routes/ | config/)
  → (güvenlik yüzeyi ise [K3: SECURITY onayı]) → [K4: phpunit yeşil]
  → (UI etkisi varsa K5: BROWSER)] → [K6: RAPOR — commit ATMAZ] → ORKESTRATÖR
```

Adım ve kapıların tamamı [[WORKFLOW.md]] §3.1 / §3.2'dedir; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm sırası | §1–§7, ≤3 başlık seviyesi |
| 3 | Envanter | Kök 11 · config 5 · handler 1 · include 32 · pages 7 · routes 1 · tests 12 — disk ölçümü |
| 4 | Çelişki | §3.5 eksiksiz; her fark `VERIFICATION REQUIRED` ile işaretli |
| 5 | Placeholder | Dosyada doldurulmamış şablon değişkeni kalmadı (arama deseni: iki parantez + harf) |
| 6 | Wiki-link | Wiki-link biçimi (çift köşeli parantez) kullanıldı; hedefler diskte var (olmayanlar §3.5'te) |
| 7 | eli10 + eli15 | §3.1/§3.2/§3.3/§3.4/§3.5/§3.6 + §4 bloklarında etiketli blok var |
| 8 | Halüsinasyon | Diskte olmayan dosya var sayılmadı (`UserIdTest` → YOK, `08-auth/` → YOK) |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md` / `AGENTS.md` + `include/**/CLAUDE.md` değiştirilmedi |
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
| ADR-043 (auth konsolidasyonu) | `../.ai/.decisions/accepted/ADR-043-auth-subdomain-consolidation.md` | Karar kaynağı (metin kopyalanmaz) |
| ADR-011 (session) · ADR-010 (CSRF) | `../.ai/.decisions/accepted/` | Güvenlik kararları |
| DB şema | `../.ai/.sql/mysql/coremusic_auth.sql` | `coremusic_auth` şeması |
| PHP şablonu | [[../.ai/.templates/backend/php-template]] | Yeni dosya (Guardrail #16) |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | Bu dosyanın iskeleti |
| Home context | [[../home.coremusic.net/CONTEXT.md]] | Köprüyü kuran tarafın envanteri |
| api context | [[../api.coremusic.net/CONTEXT.md]] | autoload hedefini kullanan taraf |
| shared context | [[../shared/CONTEXT.md]] | Ortak katman envanteri |
| Assets context | [[../assets.coremusic.net/CONTEXT.md]] | `auth-bundled.css` sağlayıcısı |
| Disk kanıtı | `auth.coremusic.net/` | Bu dokümandaki tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** context
