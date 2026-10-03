---
title: "CoreMusic — api.coremusic.net Klasör Context"
type: docs
category: api
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# api.coremusic.net — CONTEXT.md

**docType:** context · **Klasör:** `api.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`api.coremusic.net/`, CoreMusic'in **JSON-only API ağ geçididir** (gateway): `/health` canlılık ucu ve `/api/v1/*` uçlarını JSON ile yanıtlar, HTML/SPA router kullanmaz. Bu doküman klasörün envanterini, istek akışını ve komşu klasörlerle konuşmasını disk kanıtıyla tanımlar.

| Karar | Kaynak (disk) |
|-------|---------------|
| JSON-only front controller (PageRouter KULLANMAZ) | `api.coremusic.net/index.php` başlık yorumu |
| Hata sözleşmesi `{error:{code,message}}` — exception mesajı asla dönmez | `api.coremusic.net/index.php` başlık yorumu + 404/500 blokları |
| Middleware sabit sırası (ADR-020 §1.1-B.1) | `api.coremusic.net/index.php` → ResponseNormalization → Cors → RateLimit → Authentication → RequestValidation → Authorization |
| `/v1/*` → `/api/v1/*` aliası | `api.coremusic.net/index.php` "0. Alias" bloğu |
| `shared` path repo + `auth` autoload ödünçü | `api.coremusic.net/composer.json` (`repositories: ../shared`, `autoload: ../auth.coremusic.net/include/`) |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `api.coremusic.net/` kök + `include/`, `config/`, `tests/` envanteri | `auth.coremusic.net/include/` içeriği (oradan OKUNUR, ait olduğu yer orası) |
| İstek akışı: alias → health → gateway → middleware → 404 | `shared/src/` ortak katman (bağımlı olunan) |
| api ↔ auth/shared konuşma haritası | Statik varlıklar (`assets/`), medya CLI (`media/`) |

- **Kullananlar:** Backend Architect (birincil), Security Engineer (middleware), QA Engineer (tests/), MO (doküman).
- **Ön koşul:** `index.php` + `composer.json` 1. kez okunmuş; sayısal iddia disk ölçümünden.

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1-2 — disk ölçümü)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `index.php` | Tek giriş kapısı (front controller) | 191 satır: config yükleme, alias, health, gateway dispatch, middleware zinciri, 404/500 JSON | Tüm API istekleri tek kapıdan geçsin | Route/middleware sırası değişince (ADR) | Uygulamayı açan ana dosya: gelen isteği ya cevaplar ya da kapıdan içeri geçirir. | Tek dosyadır çünkü giriş noktası dağınıksa hangi isteğin nereden geçtiği bilinmez. Okunması, sıralamayı (alias → health → gateway) gösterir. Yazılması, hata sözleşmesini (`{error:{...}}`) ve güvenlik başlıklarını garanti eder. Değişiklik yalnız ADR'li sıra değişikliğinde yapılır. |
| `autoload.php` | Kendi PSR-4 yükleyicisi | Composer üretimi autoload.php (kökte) | Bağımlılıkları ve `CoreMusic\Api\` sınıfını yükle | Composer update | Uygulamanın kitapları raftan çeken dosyası. | Composer üretir; elle düzenlenirse sınıf bulunamaz. Sadece `composer update` ile değişir. |
| `include/` | API'ye özgü kod | `Controller/AuthController.php`, `Container/ApiAuthContainer.php` (2 dosya) | API'nin kendi kodu shared'den ayrı dursun | API davranışı değişince | API'nin kendi odasındaki iki masa: biri uçları yönetir, biri bağımlılıkları hazırlar. | Ayrıldı çünkü API'ye özgü kod ortak `shared/`'e girerse kütüphane şişer. Buradaki sınıflar API'nin kapısını yönetir; iş kuralları `shared/`'edir. Uç (endpoint) davranışı değişince düzenlenir. AuthController'ın kodu `auth.coremusic.net/include/` PSR-4 ile de bağlanır (composer autoload kanıtı). |
| `config/` | Ucuk yapılandırma | `routes.php`, `cors.php`, `constants.php`, `app.php` (4 dosya) | Route/CORS/sabit tek yerde | Route/CORS değişince | Adres listesi, kapı kuralı ve sabit anahtarların tutulduğu defter. | Ayrıldı因为 route ile kod farklı değişir; kod içine gömülse her adres için kod revizyonu gerekir. `index.php` bunları `require` eder. Yeni uç eklenince `routes.php`, kural değişince `cors.php` düzenlenir. |
| `tests/` | API testleri (PHPUnit) | `Unit/SmokeTest.php`, `Unit/RouteConfigTest.php`, `Unit/AuthControllerTest.php`, `bootstrap.php` | Gateway davranışı test edilsin | Kod değişince (QA) | API'nin soru kâğıtları: sağlık, route, auth uçları sınanır. | Test ayrıdır ki kod değişince davranış korunur. Kapı (CI) onları okur. Yeni uç eklenince test de eklenir. |
| `phpunit.xml` | Test yapılandırması | suite ayarları | Test tek komutla koşsun | Kapsam değişince | Sınav kurallarını yazan kâğıt. | Komut onu okur; koda gömülse her koşuda değişiklik gerekir. |
| `web.config` | IIS rewrite | IIS yönlendirme kuralları | IIS'de istek `index.php`'ye düşsün | Sunucu kuralı değişince | IIS'in kapı yönlendirmesi. | Apache/IIS farklı okur; ayrı dosya her iki sunucuda çalışır. |
| `composer.json`, `composer.lock` | Paket + kilit | ad `coremusic/api.coremusic.net`, `require: shared-infrastructure ^2.0, fast-route, nyholm/psr7, phpdotenv` | shared path repo + auth autoload bağlantısı | Bağımlılık değişince | Kimlik kartı + içindeki alet listesi + kilitli versiyonlar. | Kilit (lock) ayrıdır ki her makine aynı sürümü kursun. `autoload` içinde `../auth.coremusic.net/include/` bağlantısı buradan doğar. Bağımlılık eklenince düzenlenir. |
| `vendor/` | Kurulu 3. taraf paketler | composer üretimi | Çalışma zamanı hazır olsun | `composer install` (elle değil) | Kurulan aletlerin kutusu, dokunulmaz. | Elle girilse güncellemede kaybolur. Sadece composer yönetir. |
| `AGENTS.md`, `CLAUDE.md`, `CONTEXT.md`, `WORKFLOW.md` | Klasör dokümanları | bu seri | Kural/süreç tek yerde | Vault senkronu (MO) | Klasörün kural, rol, süreç ve envanter defterleri. | Dört dosya ayrıdır çünkü bilgi (context), kural (claude), rol (agents), süreç (workflow) farklı hızda değişir. Okunmaları işe doğru sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Mevcut üçü bu oturumda okundu; overwrite yok. |

### 3.2 İstek Akışı (Mimari Harita — `index.php` kanıtı)

```text
İstek (JSON)
  │
  ├─ [0] alias:  /v1/*  ──▶ /api/v1/*   (tek kabul noktası, index.php "0. Alias")
  ├─ [1] /health ──▶ 200 JSON {status, service, version, timestamp}
  └─ [2] /api/*  ──▶ MiddlewarePipeline (ADR-020 §1.1-B.1 — sabit sıra)
                        ResponseNormalization → Cors → RateLimit
                        → Authentication → RequestValidation → Authorization
                        ──▶ Gateway (VersionResolver + ServiceRegistry + RouteTable)
                              ├─ route action ─▶ ApiAuthContainer::controller() → AuthController
                              └─ exception ──▶ 500 JSON {error:{SERVER_INTERNAL_ERROR}}
  ┌─ [3] diğer ──▶ 404 JSON {error:{NOT_FOUND}}
```

- **Auth uçları ek oturum başlatır:** `preg_match('#^/api/v1/auth#')` → `SessionBootstrapper::ensureStarted()` (index.php, aynı save path = auth.coremusic.net cookie'si).
- **DomainConfig:** `shared/config/domain.php` üzerine scheme/host/port override (index.php).

### 3.3 Hangi Klasörlerle Konuşur?

| Komşu | Yön | Ne taşır (disk kanıtı) | eli10 | eli15 |
|-------|-----|------------------------|-------|-------|
| `../shared` | api → shared | composer path repo (`../shared`, symlink) → `coremusic/shared-infrastructure ^2.0` | Ortak altyapıyı shared'den alır. | `composer.json` `repositories` bloğu `../shared`'i symlink ile bağlar; Config, Cache, Middleware, Security, Session, Log sınıfları oradan gelir. API yalnız kendi kapısını (gateway) tutar. shared değişince API kodu değil, shared kodu değişir. |
| `../auth.coremusic.net` | api → auth | `autoload` → `CoreMusic\Auth\` = `../auth.coremusic.net/include/` | Auth kodunu doğrudan kullanır. | `ApiAuthContainer` + `AuthController` route action'ını `CoreMusic\Auth\AuthController` sınıfına bağlar (index.php satır ~147). Auth SSOT'ı auth'dedir; api kopyalamaz. auth değişince api'nin beklediği sınıf adı değişirse gateway kırılır — arayüz adları sabit tutulur. |
| `../auth.coremusic.net` (oturum) | api ↔ auth | `SessionBootstrapper::ensureStarted()` — aynı cookie/save path (index.php yorumu) | `/api/v1/auth/*` oturumu paylaşır. | Cookie yolu aynı olunca oturum iki alt alan adı arasında taşınır. Farklı olsa kullanıcı her istekte yeniden giriş yapar. Oturum davranışı `shared/src/Session/`'tadır; api yalnız çağırır. |
| `../home.coremusic.net` | api ← home | home, `/api/v1/*` uçlarını JSON ile tüketir ⚠️ VERIFICATION REQUIRED (home içinde çağıran kod okunmadı) | Home veriyi API'den alır. | API'nin tüketici tarafı home'dur; ancak home kodunda çağrı kanıtlanmadı — iddia olarak değil, komşuluk yönü olarak yazılır. Uç sözleşmesi değişince home etkilenir. |
| `../assets.coremusic.net` | — | API JSON döndürür, statik varlık servisiyle doğrudan bağımlılık yok (composer bağımlılığı yok) | Ayrık çalışırlar. | `assets` statik dosya servisidir; api ile doğrudan bağımlılığı composer'da yoktur. Ortak noktaları yalnız UI'ın API'yi çağırmış olmasıdır; kod bağımlılığı iddia edilmez. |

### 3.4 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Her dosyanın tek işi var | Dosya büyümez, sahibi belli kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık, tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı yayılmaz | Drift |
| 5 | **Vendor karantinası** | `vendor/` + 3. taraf ayrı | Güncelleme bizim kodu bozar |

> **eli10 (basit):** Bilgiler küçük kutulara bölünmüş; bir kutuyu değiştirmek diğerine zarar vermesin diye.
> **eli15 (detay):** Giriş (`index.php`), kod (`include/`), ayar (`config/`) ve test (`tests/`) ayrıdır çünkü farklı hızda değişirler: adres sık değişir, kod az değişir, test en son değişir. Ayrı olunca route eklerken test dosyasına dokunmak gerekmez. Hepsi tek dosyada olsaydı her route değişikliği kod revizyonu sayılır, inceleme imkânsızlaşır. Kapı (middleware) sırası da böylece tek dosyada net durur.

---

## 4. Kurallar

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | JSON-only; HTML/SPA router kullanılmaz | API istemcisi JSON bekler, HTML kırar | `index.php` başlık yorumu |
| 2 | Exception mesajı istemciye dönmez (`SERVER_INTERNAL_ERROR`) | Sızıntı sızdırmaz (bilgi ifşası) | `index.php` başlık yorumu + catch bloğu |
| 3 | Middleware sırası sabit (ADR-020 §1.1-B.1) | Sıra değişirse kontroller atlanabilir | `index.php` pipeline yorumu |
| 4 | Cevap başlıkları: `nosniff`, katı CSP (`default-src 'none'`) | JSON yüzeyi XSS'e kapalı | `index.php` `$jsonResponse` |
| 5 | `/v1/*` yalnız alias → `/api/v1/*` (tek kabul noktası) | Çift route kaynağı olmasın | `index.php` "0. Alias" |
| 6 | ORM yok, `shared` üzerinden PDO (ADR-002) | Injection yüzeyi | `shared/AGENTS.md` §4 |

> **eli10 (basit):** Bu kurallar API'nin hep aynı güvenli JSON dilinde konuşmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü API'nin sözleşmesi (hata biçimi, başlıklar, sıra) kodun kendisinden daha uzun yaşar; kod içine gömülse revisyonda kaybolur. Okunmaları, yeni uç eklerken sözleşme dışı davranışı engeller. Yazmaları, istemcinin (UI')nın tahmin etmeden çalışmasını garantiler. Sözleşme değişince (ADR) yalnız bu tablo ve ilgili kod birlikte güncellenir.

---

## 5. Workflow

```text
GÖREV → KURALLAR oku (CLAUDE/AGENTS) → index.php + composer.json 1. kez oku
  → route/kod değişikliği → test ekle (tests/Unit) → composer test
  → (UI etkisi varsa browser) → RAPOR — commit ATMAZ
```

| # | Adım | Neden | Atlarsan ne olur |
|---|------|-------|------------------|
| 1 | Klasör kuralları + `index.php` oku | Sıra/sözleşme orada | Sözleşme ihlali (HTML/exception sızıntısı) |
| 2 | Route `config/routes.php`'ye, kod `include/`'a | Tek kabul noktası + sorumluluk sınırı | Çift route, dağınık kod |
| 3 | Middleware ekleme/sıra değişikliği yalnız ADR ile | Kapı sırası güvenlik | Kontrol atlanır |
| 4 | `tests/Unit/` + `bootstrap.php` altında test | Gateway davranışı sabitlenir | Sessiz kırılma |
| 5 | `composer test` | Kapı yeşil olmalı | Hatalı değişiklik git'e girer |
| 6 | Rapor; commit orkestratörde | Yetki sınırı | Düzensiz tarih |

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: context` |
| 2 | Bölüm sırası | §1–§7 |
| 3 | Envanter | Kök 6 girdi + `include/` 2, `config/` 4, `tests/` 4 dosya — disk ölçümü |
| 4 | Akış | §3.2 zinciri `index.php` ile birebir |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | `[[...]]`, hedefler diskte var |
| 7 | eli10 + eli15 | §3 satırlarında etiketli blok |
| 8 | Halüsinasyon | home→api tüketimi `⚠️ VERIFICATION REQUIRED` ile işaretli |
| 9 | Dokunulmaz | Kaynak kod + mevcut doküman değişmedi |
| 10 | Emoji | Yalnız `[[...]]` |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasörde kurallar |
| Klasör rolleri | [[AGENTS.md]] | Routing |
| Klasör süreci | [[WORKFLOW.md]] | Adım/kapı |
| Kök kurallar | [[../AGENTS.md]] | Master SSOT |
| Ortak altyapı context | [[../shared/CONTEXT.md]] | shared envanteri |
| Auth context | [[../auth.coremusic.net/CONTEXT.md]] | auth envanteri (autoload hedefi) |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Guardrails |
| Giriş kodu | `api.coremusic.net/index.php` | Akış kanıtı |
| Paket tanımı | `api.coremusic.net/composer.json` | Bağımlılık kanıtı |
| Template kaynağı | `../.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
