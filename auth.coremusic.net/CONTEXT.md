---
title: "CoreMusic — auth.coremusic.net Context"
type: docs
category: auth
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — auth.coremusic.net Context

**docType:** context · **Klasör:** `auth.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`auth.coremusic.net/`, CoreMusic'in **kimlik doğrulama servisidir**: login, register, reset-password, forgot-password, select-gender ve set-gender sayfalarını, OAuth bağlantı uçlarını ve oturum yönetimini tek subdomainde toplar; `api` ve `home` subdomainleri bu kodu kopyalamaz, bu klasörün `include/` katmanını çağırır. Bu doküman klasörün disk envanterini (yineleme ölçümü 2026-10-12: **2032 dosya / 346 dizin / 2378 girdi**, `vendor/` dahil; kök **13 dosya + 7 dizin**; `vendor/` hariç **65 dosya / 37 PHP**), istek akışını ve komşu klasörlerle ilişkisini kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: K-space katman mimarisi atıfları (K000–K020, A0–A5, `.ai/architecture/` bölümleri) bu sürümde bilinçli olarak bulundurulmaz — kaldırıldı, yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Tek giriş noktası: `index.php` front controller | `index.php` — 239 satır / 10746 bayt (`get_file_info` + okuma ölçümü 2026-10-12) |
| Middleware sağlayıcı: `../shared/src/Middleware/` | dizin var (11 PHP); `index.php` içinde `OriginCheckMiddleware` + `RateLimiterMiddleware` çağrısı |
| `include/Middleware/` diskte YOK | `include/` listesi (6 alt dizin) + CLAUDE.md B-F-18 notu (2026-10-07 silindi) |
| PHP >= 8.4, PSR-4 `CoreMusic\Auth\` → `include/` | `composer.json` (46 satır) — `require.php` + `autoload.psr-4` |
| Path repo: `../shared` → `coremusic/shared-infrastructure ^2.0` | `composer.json` `repositories` bloğu |
| Paket kilidi: 2842 satır | `composer.lock` (okuma ölçümü 2026-10-12) |
| Test: PHPUnit ^10.5, suite `tests/Unit`, kapsam `include/` | `phpunit.xml` (35 satır) |
| Test çalıştıracak `composer scripts` bloğu YOK | `composer.json` — `scripts` anahtarı yok (doğrudan okuma) |
| SPA route tablosu: 7 `SpaRoute` | `../shared/config/auth-routes.php` (grep: 7 `new SpaRoute`) |
| Şema: 13 `CREATE TABLE` | `.ai/.sql/mysql/coremusic_auth.sql` (grep ölçümü) |
| Köprü: `HomeAuthBridge` | `../home.coremusic.net/include/Auth/HomeAuthBridge.php` (6011 bayt) |

> **eli10 (basit):** Bu klasör sitenin kapı bekçisidir: kullanıcı giriş yapar, kayıt olur, şifresini sıfırlar; buradaki kod diğer sayfaların kimi gördüğini belirler.
> **eli15 (detay):** Auth ayrı yazıldı çünkü güvenlik kodu en az değişen ve en çok denetlenen koddur; diğer işlere karışsa her revizyon güvenlik incelemesi gerektirir. Okunması, hangi dosyanın hangi işi yaptığını ve her sayının hangi ölçümden geldiğini gösterir. Yazılması envanter ile disk gerçeğinin hizasını kanıtlar. Ölçülmeyen sayısal iddia bu dosyaya girmez; girerse eski bilgi yeni oturumları yanlış yönlendirir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `auth.coremusic.net/` kök + `config/`, `handler/`, `include/`, `pages/`, `routes/`, `tests/`, `vendor/` envanteri | `../shared/src/` ortak katman (bağımlı olunan — ayrı context) |
| İstek akışı: config → bootstrap → JSON uçlar → bypass → gender gate → page router | `../api.coremusic.net/` JSON gateway (auth kodunu çağıran taraf) |
| auth ↔ shared/home/api komşuluk haritası | `../home.coremusic.net/` köprüsü (`HomeAuthBridge` — oradan OKUNUR, kopyalanmaz) |
| Vault ↔ disk çelişki kaydı (§3.5) | ADR metinleri (dokunulmaz — yalnız wiki-link verilir) |

- **Kullananlar:** Backend Architect (birincil), Security Engineer (oturum/CSRF/rate limit), QA Engineer (`tests/`), MO (bu doküman).
- **Ön koşul:** `index.php`, `autoload.php`, `composer.json`, `routes/oauth.php` bu oturumda okunmuş; envanter yineleme + glob ile ölçülmüş; kanıt yoksa sayı `UNKNOWN` yazılır.
- **Dokunulmaz:** `CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md`, `config/`, `handler/`, `include/`, `pages/`, `routes/`, `tests/`, `vendor/` bu görevde değiştirilmez — yalnız bu dosya yazılır.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, istek yolu ve komşularla ilişkisi anlatılır; başka klasörlerin içeriği konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman ortak katmanın ya da api kodunun tarifine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçümün ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Envanter (yineleme + list_directory ölçümü 2026-10-12: 13 kök dosya, 7 kök dizin)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `index.php` | Tek giriş kapısı: config, bootstrap, JSON uç, bypass, router dispatch | 239 satır: `config/*.php` yükleme, RuntimeBootstrap, logger, HTTPS kontrolü, `/health`·`/session`·`/validate-key`, `shared/src/Middleware` çağrıları, PageRouterKernel, 500 JSON | Tüm auth istekleri tek kapıdan geçsin, güvenlik sırası tek yerde olsun | Route/middleware sırası değişince (ADR + onay) |
| `autoload.php` | Elle yazılmış autoload köprüsü (Composer üretimi değil) | 49 satır: kendi `vendor/autoload.php` + shared vendor fallback + `CoreMusic\Auth\` PSR-4 kaydı | Sınıflar ve bağımlılıklar tek yerden yüklensin | Composer/autoload düzeni değişince |
| `config/` (dizin, 5 dosya) | Ortam, uygulama, sabit ve CORS ayarı | `.env` (içerik okunmaz/yazılmaz) · `.env.example` · `app.php` 33 · `constants.php` 108 · `cors.php` 20 satır | Ayar koddan ayrı dursun | Ayar/CORS kuralı değişince (Security) |
| `include/` (dizin, 25 dosya) | Auth'a özgü hexagonal kod | 16 PHP + 9 klasör-içi `CLAUDE.md`; 9 alt dizin (ayrıntı §3.2) | Katmanlar (Controller/Service/Repository/Domain) ayrı dursun | Auth davranışı değişince |
| `handler/` (dizin, 1 dosya) | OAuth POST yakalayıcı | `OAuthPostHandler.php` 158 satır | OAuth cevabı tek yerde çevrilsin | OAuth akışı değişince |
| `routes/oauth.php` | OAuth SPA route tanımı | 51 satır: 4 `SpaRoute` (oauth-connect, oauth-callback, oauth-connections, oauth-disconnect) | OAuth adresleri tek dosyada dursun | Yeni OAuth ucu eklenince |
| `pages/` (dizin, 7 dosya) | Sunucu tarafı auth sayfaları | `forgot-password` · `login` · `logout` · `register` · `reset-password` · `select-gender` · `set-gender` — her biri PHP (101–318 satır arası, okuma ölçümü) | Her adım ayrı sayfa olsun | Form/akış değişince |
| `tests/` (dizin, 13 dosya) | PHPUnit birim testleri | 7 PHP (`bootstrap.php` + 6 test) + 6 klasör-içi `CLAUDE.md`; 6 alt dizin | Değer nesnesi/DTO davranışı sabit kalsın | Kod değişince (QA) |
| `phpunit.xml` | Test yapılandırması | 35 satır: bootstrap `tests/bootstrap.php`, suite `tests/Unit`, kapsam `include/` | Test tek yerden koşsun | Kapsam/test ayarı değişince |
| `composer.json` + `composer.lock` | Paket tanımı + sürüm kilidi | json 46 satır (php >=8.4, path repo `../shared`, PSR-4 `CoreMusic\Auth\`, phpunit ^10.5; `scripts` YOK) · lock 2842 satır | Her makine aynı sürümü kursun | Bağımlılık eklenince/çıkarılınca |
| `.htaccess` + `web.config` | Apache ve IIS yönlendirme/yasak kuralları | `.htaccess` 38 · `web.config` 44 satır | İstek `index.php`'ye düşsün | Sunucu kuralı değişince (DevOps) |
| `vendor/` (dizin) | Kurulu 3. taraf paketler | 1967 dosya, 324 alt dizin, 1703 PHP (yineleme ölçümü 2026-10-12) | Çalışma zamanı hazır olsun | Yalnız `composer install` — elle değil |
| `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md` | Klasör kural/rol/süreç/envanter dokümanları | 249 · 80 · 322 satır + bu dosya (eski `CONTEXT.md` 331 satır / 27981 bayt — okuma ölçümü 2026-10-12) | Bilgi dört farklı hızda değişir | Yalnız vault-sync (MO) |

**`index.php`**
> **eli10 (basit):** Uygulamayı açan ana dosya: gelen isteği önce ayarlara bağlar, sonra kapıdan geçirir ve sayfaya yönlendirir.
> **eli15 (detay):** Tek dosyadır çünkü giriş noktası dağınıksa hangi isteğin nereden geçtiği bilinmez. Okunması, sıra sırasını (config → bootstrap → JSON uç → bypass → router) ve hata sözleşmesini gösterir. Yazılması, ortak middleware çağrısını ve 500 JSON cevabını garanti eder. Değişiklik yalnız ADR'li sıra değişikliğinde ve onayla yapılır.

**`autoload.php`**
> **eli10 (basit):** Sınıfları raftan çeken köprü: önce kendi kurulumunu, sonra ortak kurulumu yükler.
> **eli15 (detay):** Elle yazıldı çünkü iki ayrı vendor (auth + shared) tek yükleyiciye sığmaz; Composer tek başına bunu çözmez. Okunması, hangi namespace'in nereden geldiğini gösterir. Yazılması bağımlılık düzeni değişince gerekir. Bozulursa hiçbir sınıf bulunamaz ve istek çöker.

**`config/` (dizin)**
> **eli10 (basit):** Ortam, uygulama, sabit ve kapı (CORS) ayarlarının tutulduğu beş ayar dosyası.
> **eli15 (detay):** Ayrıldı çünkü ayar ile kod farklı hızda değişir; koda gömülse her değer için kod revizyonu gerekir. Okunması, CORS ve sabitlerin nereden geldiğini gösterir (`index.php` bunları `require` eder). Yeni değer eklenince `constants.php`, izin değişince `cors.php` düzenlenir. `.env` içeriği hiçbir yere yazılmaz. Ayrı olmazsa ayar değişikliği güvenlik incelemesini kaybeder.

**`include/` (dizin)**
> **eli10 (basit):** Auth'un kendi kod odaları: çeviren, iş yapan, veriye bakan ve değer nesneleri.
> **eli15 (detay):** Ayrıldı çünkü auth kodu ortak `shared/`'e girerse kütüphane şişer ve tek gerçek kaybolur. Okunması, katman sınırını (Controller → Service → Repository → Domain) ve `Middleware/`'in neden burada olmadığını gösterir. Uç davranışı değişince ilgili katman düzenlenir. Katmanlar tek dosyada toplansa güvenlik denetimi (kim hangi veriye erişir) görünmez olur.

**`handler/` (dizin)**
> **eli10 (basit):** OAuth'dan gelen cevabı yakalayıp çeviren tek yardımcı dosya.
> **eli15 (detay):** Ayrı durur çünkü OAuth cevabı get isteği değil form gönderimidir ve `index.php`'nin akışını bozmamalıdır. Okunması, hangi form alanının nasıl işlendiğini gösterir. OAuth akışı değişince düzenlenir. Kalkarsa OAuth bağlantısı sessizce kopar.

**`routes/oauth.php`**
> **eli10 (basit):** OAuth adreslerinin listesi: bağlan, dön, bağlantıları gör, ayır.
> **eli15 (detay):** Ayrı dosyadır çünkü adres tanımı koddan ayrı hızda değişir ve `PageRouter` tarafından okunur. Okunması, dört OAuth ucunun adını ve giriş zorunluluğunu gösterir. Yeni OAuth ucu eklenince düzenlenir. Kullanım kanıtı bu oturumda bulunamadı (§3.3 — `UNKNOWN`); yanlış varsayım route'u kaybolmuş gibi gösterir.

**`pages/` (dizin)**
> **eli10 (basit):** Kullanıcının gördüğü altı form sayfası ve çıkış sayfası; her adımın kendi dosyası var.
> **eli15 (detay):** Sayfalar ayrıdır çünkü her adımın kendi doğrulama, yönlendirme ve hata mesajı vardır; tek dosyada toplansa koşul satırları iç içe geçer. Okunması, hangi adımın hangi sayfaya gittiğini gösterir. Form/akış değişince yalnız o sayfa düzenlenir. Sayfa kayarsa kullanıcı yanlış adıma düşer.

**`tests/` (dizin)**
> **eli10 (basit):** Kodun doğruluğunu kanıtlayan soru kâğıtları: değer nesneleri, DTO ve oturum yönetimi sınanır.
> **eli15 (detay):** Test ayrıdır ki kod değişince davranış korunur ve test kodu taklit etmez. Okunması, hangi sözleşmenin kilitli olduğunu (Email/Password/UserId doğrulaması, SessionManager akışı) gösterir. Kod değişince QA ekler/düzeltir. Test kalkarsa sessiz kırılma fark edilmez.

**`phpunit.xml`**
> **eli10 (basit):** Sınavın kurallarını yazan yapılandırma: hangi klasörün ve hangi kapsamın koşulacağını belirler.
> **eli15 (detay):** Ayrı durur ki test ayarı koda gömülmesin; her koşuda değişiklik gerekmesin. Okunması, bootstrap (`tests/bootstrap.php`) ve kapsam (`include/`) hedefini gösterir. Kapsam değişince düzenlenir. Yanlış yazılırsa testler hiç koşulmaz veya yanlış dosyalar ölçülür.

**`composer.json` + `composer.lock`**
> **eli10 (basit):** Kimlik kartı, alet listesi ve kilitli sürümler: hangi paketlerin kurulacağını söyler.
> **eli15 (detay):** Lock ayrıdır ki her makine aynı sürümü kursun; json tek başına sürüm kaymasına yol açar. Okunması, PHP sürümünü, `../shared` path bağlantısını ve PSR-4 kaydını gösterir. Bağımlılık değişince birlikte düzenlenir. Kilit kırılırsa ortam farkı üretilir ve "bende çalışıyor" sorunu doğar.

**`.htaccess` + `web.config`**
> **eli10 (basit):** İki sunucunun (Apache ve IIS) kapı yönlendirmesi ve gizli dosya yasakları.
> **eli15 (detay):** İkisi ayrıdır çünkü sunucular kural dilini farklı okur; tek dosya her ikisinde çalışmaz. Okunması, hangi yolların kapalı olduğunu ve her şeyin `index.php`'ye düştüğünü gösterir. Sunucu kuralı değişince DevOps düzenler. Silinirse `config/` ve `include/` doğrudan dışarıya açılır.

**`vendor/` (dizin)**
> **eli10 (basit):** Kurulu hazır aletlerin kutusu; elle dokunulmaz.
> **eli15 (detay):** Composer üretir ve Composer siler; elle girilen dosya güncellemede kaybolur. Okunması yalnız envanter amaçlıdır (kaç dosya/paket kurulu). Yalnız `composer install` ile değişir. Elle düzenlenirse güvenlik yamaları şaşar ve sürüm farkı doğar.

**`CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md`**
> **eli10 (basit):** Bu klasörün kural, rol, süreç ve envanter defterleri; dördü ayrı ayrı durur.
> **eli15 (detay):** Dört dosya ayrıdır çünkü bilgi, kural, rol ve süreç farklı hızda değişir; tek dosyada toplansa her revizyonda tümü okunur. Okunmaları işe doğru sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Dördü de vault-sync (MO) tarafından güncellenir; bu oturumda yalnız `CONTEXT.md` yeniden yazıldı.

### 3.2 `include/` Katman Envanteri (list_directory ölçümü 2026-10-12: 25 dosya, 16 PHP, 9 alt dizin)

| Katman | Dosyalar (satır) | Ne için |
|--------|------------------|---------|
| `Container/` | `AuthContainer.php` (77) | Bağımlılık kurulumu — controller/service nereden gelir |
| `Controller/` | `AuthController.php` (317) | HTTP çevirisi: istek → servis → cevap |
| `Handler/` | `AuthKeyRedirectHandler.php` · `AuthPostHandler.php` · `AutoRedirectHandler.php` | Yönlendirme ve POST akışları |
| `Service/` | `AuthService.php` (350) · `SessionManager.php` (181) | İş mantığı: giriş/kayıt/oturum |
| `Repository/` | `UserRepository.php` (350) | Kullanıcı veri erişimi (PDO) |
| `Domain/DTO/` | `AuthResponse.php` · `LoginRequest.php` · `RegisterRequest.php` | Taşıma nesneleri (33–62 satır arası, okuma ölçümü) |
| `Domain/Entity/` | `User.php` (77) | Kullanıcı varlığı |
| `Domain/ValueObject/` | `Email.php` · `Gender.php` · `Password.php` · `UserId.php` (42–68 satır arası) | Doğrulamalı değerler |
| `Middleware/` | **YOK — diskte bu dizin bulunmaz** | `shared/src/Middleware/` tek gerçek zincirdir (CLAUDE.md B-F-18: 2026-10-07 silindi) |

> **eli10 (basit):** Auth kodu, ayrı odalara bölünmüş: çeviren, iş yapan, veriye bakan ve değeri doğrulayan.
> **eli15 (detay):** Katmanlar ayrıdır çünkü hexagonal sınır güvenlik denetimini mümkün kılar; hepsi tek dosyada olsaydı kimin hangi veriye eriştiği görünmezdi. Okunması, işin nerede bittiğini (HTTP çevirisinin Controller'da, iş mantığının Service'te) gösterir. Kapı (middleware) bu klasörde değildir; `../shared/src/Middleware/` kullanılır, ikinci kopya doğurulmaz. Katman değişince yalnız o satır güncellenir.

### 3.3 İstek Akışı (Mimari Harita — `index.php` kanıtı)

```text
Istek
  |
  +- [0] config yukleme: config/app.php + constants.php + cors.php
  +- [1] RuntimeBootstrap + logger
  +- [2] HTTPS kontrolu
  +- [3] JSON uclari: /health | /session | /validate-key
  |        MiddlewarePipeline (shared/src/Middleware):
  |        OriginCheckMiddleware -> RateLimiterMiddleware
  +- [4] kok yonlendirme (auth kogune gelen istek)
  +- [5] bypass kontrolu
  +- [6] gender gate (select-gender)
  +- [7] varsayilan OAuth redirect
  +- [8] AutoRedirectHandler
  +- [9] PageRouterKernel -> handler zinciri (include/Handler/ + pages/)
  +- [10] ../shared/config/auth-routes.php  (7 SpaRoute)
  +- [11] hata --> 500 JSON {error:{SERVER_INTERNAL_ERROR}}
  +- [?]  routes/oauth.php -- kullanim kaniti bu oturumda BULUNAMADI (UNKNOWN)
```

- Ortak middleware: `../shared/src/Middleware/` (11 PHP) — `index.php` içinde OriginCheck + RateLimiter çağrısı.
- Route tablosu: `../shared/config/auth-routes.php` — login, register, select-gender, forgot-password, reset-password, logout, set-gender (7 `SpaRoute`, grep ölçümü).
- `routes/oauth.php` (4 `SpaRoute`) için kod içi referans bu oturumda ölçüldü: `shared/**.php` ve `auth.coremusic.net/**.php` içinde `oauth.php` adıyla çağrılan **0** nokta; kullanım `UNKNOWN` olarak işaretli kalır, tahmin yazılmaz.

> **eli10 (basit):** Her istek önce ayarlara bağlanır, sonra kapıdan geçer, en sonunda bir sayfaya ya da hata cevabına düşer.
> **eli15 (detay):** Akış ayrı bölümde çünkü sıra güvenlik demektir: kapılar değişirse bir kontrol atlanabilir. Okunması, hangi isteğin hangi kapıdan geçeceğini ve hatanın neden asla ham metin dönmediğini gösterir. Yazılması `index.php` ile birebir ölçümle yapılır. Tablo kodla uyumsuzlaşsa kod kazanır, bu dosya düzeltilir.

### 3.4 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kok (AGENTS.md + CLAUDE.md)
  -> auth.coremusic.net/ (CLAUDE.md -> AGENTS.md -> WORKFLOW.md -> CONTEXT.md)
    -> .ai/ vault yalniz ihtiyac aninda (bolum 7'deki hedeflerle)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `CLAUDE.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Klasör kuralları + yasaklar |
| `AGENTS.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Dosya sahipliği + routing |
| `WORKFLOW.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Adım → kapı → rapor |
| `CONTEXT.md` (bu dosya) | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Envanter + çelişki kaydı |

> **eli10 (basit):** Önce kök kurallar, sonra bu klasörün dört dosyası, vault ise sadece ihtiyaç anında okunur.
> **eli15 (detay):** İlişki ayrı tabloda çünkü dört dosyanın da ikinci SSOT olmadığı bellidir: hepsi türevdir, çelişkide vault ve disk kazanır. Okunması boot sırasını ve otorite zincirini gösterir. Yazılması, bu dosyaların bağımsız kaynak sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla çalışır ve bilgi ayrışır.

### 3.5 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (331 satır / 27981 bayt): "toplam **2035 dosya**, vendor hariç **69 dosya**" | 2032 dosya / 346 dizin / 2378 girdi; vendor hariç 65 dosya | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md`: "`include/` **32 dosya: 22 PHP** + 10 `CLAUDE.md`" | 25 dosya: 16 PHP + 9 `CLAUDE.md` | **Disk kazanır** |
| 3 | Eski `CONTEXT.md`: "`tests/` **12 dosya: 6 PHP**"; "Unit/Service diskte YOK" | 13 dosya: 7 PHP + 6 `CLAUDE.md`; `tests/Unit/Service/SessionManagerTest.php` **VAR** | **Disk kazanır** |
| 4 | Eski `CONTEXT.md`: "vendor **1966 dosya** (2035 − 69)" | vendor 1967 dosya / 324 alt dizin | **Disk kazanır** |
| 5 | Eski `CONTEXT.md`: "vendor hariç uzantı `.php 42`" | vendor hariç `.php` 37 (kök 2 + config 3 + handler 1 + include 16 + pages 7 + routes 1 + tests 7) | **Disk kazanır** |
| 6 | `CLAUDE.md` satır 237: test "`UserIdTest` → `tests/Unit/Domain/ValueObject/`" | O dizinde dosya YOK: `EmailTest`, `GenderTest`, `PasswordTest` | **Disk kazanır** — `CLAUDE.md` bu görevde değiştirilmez, yalnız işaretlenir |
| 7 | `CLAUDE.md` satır 219: ".ai/architecture/k6-guvenlik/" | `.ai/architecture/` içinde yalnız `adr/`, `index.md`, `README.md` | **Disk kazanır** |
| 8 | `CLAUDE.md` satır 185/220: ".ai/.subdomains/auth.coremusic.net/" | `.ai/.subdomains` dizini ENOENT (diskte yok) | **Disk kazanır** |
| 9 | `CLAUDE.md` satır 224–226: ".ai/decisions/accepted/ADR-043..." (noktasız) | Gerçek yol `.ai/.decisions/accepted/` — ADR-043 / ADR-011 / ADR-010 dosyaları var (57343 / 43970 / 70671 bayt) | **Disk kazanır** |
| 10 | `CLAUDE.md` satır 25/182: `HomeAuthBridge` bu klasörün parçası gibi okunuyor | Köprü dosyası `../home.coremusic.net/include/Auth/HomeAuthBridge.php` (6011 bayt); `auth.coremusic.net/include/Auth/` **YOK** | **Disk kazanır** |
| 11 | `routes/oauth.php` satır 9: `@see decisions/accepted/ADR-088-gender-based-social-oauth` (kod içindeki atıf — bağlantı değil, düz metin) | `.ai/.decisions/accepted/` altında ADR-088 dosyası YOK (ADR-086 → ADR-089) | **Disk kazanır** — atıf işaretli kalır, uydurma ADR üretilmez |
| 12 | `autoload.php` PSR-4 `CoreMusic\Auth\Middleware\` → `include/Middleware/` | Hedef dizin diskte YOK (ölü kayıt); canlı middleware `../shared/src/Middleware/` | **Disk kazanır** |

> **eli10 (basit):** Eski dosyalardaki eski sayılar ve yanlış yol tarifleri var; gerçeği disk söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu gösterir. Yazılması ölçüm kanıtıyla olur. Bu tablo olmazsa eski "2035 dosya" ve "32 dosya" bilgisi yeni oturumları yanlış yönlendirir.

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (sayfa → `pages/`, akış → `include/`, test → `tests/`) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Sabit ve ayar tek yerde durur (`config/constants.php`) | Her dosyada ayrı değer → tutarsızlık, değişince tarama gerekir |
| 4 | **Cihaz/ortam izolasyonu** | Sunucu ve ortam farkı başka dosyalara yayılmaz | Apache/IIS farkı tüm kodlara sıçrar, drift doğar |
| 5 | **Vendor karantinası** | 3. taraf kod `vendor/`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: adres `routes/`te ve `shared/config/auth-routes.php`'de, kod `include/`ta, sayfa `pages/`te, test `tests/`te, kurulum `vendor/`'ta durur. Böylece bir yeri değiştirince sadece o değişir, diğerleri bozulmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Auth kodunun tek SSOT'u bu klasördür; `api/` ve `home/` içine kopyalanmaz | İki gerçek doğar, güvenlik açığı |
| 2 | Middleware yalnız `../shared/src/Middleware/` üzerinden çağrılır; `include/Middleware/` yeniden açılmaz | Çift kapı zinciri, atlanan kontrol |
| 3 | Oturum/CSRF/rate limit davranışı ADR-010 · ADR-011 · ADR-013 ile sabittir | Güvenlik kontrolü atlanabilir |
| 4 | Secret yalnız `.env` içinde yaşar; bu dosyalara ve vault `.md`'ye yazılmaz | Anahtar sızıntısı |
| 5 | ORM yok; DB erişimi PDO ile (ADR-002) | Injection yüzeyi genişler |
| 6 | `vendor/` ve `composer.lock` elle değiştirilmez (yalnız Composer) | Sürüm kayması, kırık kurulum |
| 7 | Route/middleware sırası değişikliği yalnız ADR + onay ile | Güvenlik sırası kırılır |
| 8 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 9 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.5 tablosunda işaretlenir | SSOT ihlali |
| 10 | Yeni `.md` şablondan üretilir (Guardrail #16); subagent `git commit` ATMAZ | Şablon ihlali, yetkisiz commit |

> **eli10 (basit):** Bu kurallar, auth kodunun tek yerde yaşamasını ve kapıların izinsiz değişmemesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü yetki metni kod içine gömülmez; kapı (middleware) sırası değişince kod değil, ADR ve bu tablo güncellenir. Okunmaları, göreve başlarken kopya-auth ve secret sızıntısını engeller. Yazılmaları denetimi tekrarlanabilir kılar. Kural değişince yalnız bu tablo ve ilgili kod birlikte güncellenir.

---

## 5. Workflow

```text
OLC (envanter -- bu oturumda) -> YAZ (yalniz CONTEXT.md, sablon iskeleti)
  -> CELISKI KAYDI (vault <-> disk -> disk kazanir) -> DOGRULAMA (bolum 6 tablosu)
  -> ILGILI INDEX'E 1 SATIR -> RAPOR (commit ORCHESTRATORDE -- subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural + hedef dosyaları oku (`CLAUDE.md`, `AGENTS.md`, `index.php`, `composer.json`, `routes/oauth.php`) | Kural + kanıt | Kapsam ve yasaklar baştan belli olsun | Kopya-auth, secret sızıntısı, yetki aşımı |
| 2 | Envanter ölç: yineleme + `list_directory` + satır/bayt sayımı | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, eski "2035 dosya" efsanesi |
| 3 | Şablondan yaz (§1–§7, 5 sütun, eli10/eli15) | Bu dosya | Tutarlı iskelet (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 4 | Çelişki tablosunu güncelle (§3.5) | 12 karar satırı | Eski bilgi yeni oturumu yanıltır | Yanlış dosya yolu ve test adı sürer |
| 5 | Bölüm 6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | İlgili index'e 1 satır + rapor; **commit ATMA** | Kayıt + rapor | Yetki sınırı (kök `AGENTS.md` §7) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması kapıları önceden bilmeyi sağlar. Yazılması tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve doğrulama) hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 9 alan (7 zorunlu + `date` + `docType: context`) |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişen değer yer tutucusu (çift süslü parantez deseni) kalmadı |
| 4 | Envanter | 13 kök dosya, 7 kök dizin, 2032/346/2378 yineleme, vendor hariç 65 dosya / 37 PHP — bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı yineleme/`list_directory`/grep/okuma ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; Bölüm 7 hedefleri diskte mevcut |
| 7 | eli10 + eli15 | §1, §2, §3.1 (13 satır), §3.2–§3.6, §4, §5 maddelerinde çift blok var (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4 #9 "disk kazanır" var; §3.5 12 satır |
| 9 | Boot sırası | Kök ↔ klasör ↔ vault ilişkisi §3.4'te var |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; katman mimarisi tablosu/eklentisi yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 250–400 satır bandı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — okunur, değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Dosya sahipliği + routing |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Kök vault göstergesi | [[../CONTEXT.md]] | Proje geneli envanter |
| Ortak altyapı context | [[../shared/CONTEXT.md]] | `../shared` envanteri (path repo hedefi) |
| Ortak katman kuralları | [[../shared/AGENTS.md]] | `shared` dosya sahipliği |
| API context | [[../api.coremusic.net/CONTEXT.md]] | auth kodunu çağıran JSON gateway |
| Tüketici context | [[../home.coremusic.net/CONTEXT.md]] | `HomeAuthBridge` sahibi taraf |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | §5 Domain Boundaries, §6 routing |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| ADR indeksi | [[../.ai/.decisions/index.md]] | ADR-043 / ADR-011 / ADR-010 / ADR-013 (metin dokunulmaz) |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| Anahtar kelime haritası | [[../.ai/keys.md]] | Konu → dosya araması |
| Vault ana katalog | [[../.ai/index.md]] | Master dizin |
| Şema kanıtı | [[../.ai/.sql/mysql/coremusic_auth.sql]] | 13 `CREATE TABLE` |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| PHP şablonu | [[../.ai/.templates/backend/php-template.md]] | Yeni PHP dosyası kaynağı |
| Giriş kodu kanıtı | [[index.php]] | İstek akışı + middleware çağrıları |
| Autoload köprüsü kanıtı | [[autoload.php]] | Vendor + PSR-4 kayıt zinciri |
| Paket tanımı kanıtı | [[composer.json]] | Bağımlılıklar + path repo + PSR-4 |
| Test kanıtı | [[phpunit.xml]] | Bootstrap + suite + kapsam |
| OAuth route kanıtı | [[routes/oauth.php]] | 4 `SpaRoute` + ADR-088 atfı (§3.5 #11) |
| Ortak route kanıtı | [[../shared/config/auth-routes.php]] | 7 `SpaRoute` (login/register/...) |
| Ortak middleware kanıtı | [[../shared/src/Middleware/]] | OriginCheck + RateLimiter (11 PHP) |
| Köprü dosyası kanıtı | [[../home.coremusic.net/include/Auth/HomeAuthBridge.php]] | auth → home oturum köprüsü |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12
