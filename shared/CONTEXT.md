---
title: "CONTEXT — shared"
type: context
category: shared
date: 2026-10-12
updated: 2026-10-12
version: 2.0.0
status: active
authority: "SSOT: shared/CONTEXT.md — ölçüm 2026-10-12"
docType: context
---

# CONTEXT — `shared/`

> Bu belge `shared/` dizinini tanımlar. Vault (`.ai/`) ile çelişirse **disk kazanır**.
> Bu dosya salt-okunur referanstır; düzenlemeyi ayrı bir görevde yapın.
> K-Space katman referansları (K000–K020, A0–A5, `.ai/architecture/`) bu revizyonda kaldırıldı — güncel mimari kanıt §3.1–§3.3'tedir.

---

## 1. Amaç

`shared/`, CoreMusic subdomain'lerinin (api, auth, home) ortak kullandığı **tek PHP altyapı paketidir**: `coremusic/shared-infrastructure` sürüm 2.0.0 (`composer.json` — type: library, license: proprietary). Sunucu tarafı katman (Router, Middleware, Repository, OAuth, Security, Log, ViewMode) burada yaşar; hiçbir subdomain kendi kopyasını tutmaz.

> **eli10 (basit):** `shared/`, api/auth/home sitelerinin hep birlikte kullandığı ortak kutudur. İçindeki kod bir kez yazılır, üç site kullanır; bir yerde düzeltilince üçü de düzelir.
> **eli15 (detay):** Paket Composer path-repo (`repositories: [{type: path, url: ../shared, options: {symlink: true}}]`) ile tüketilir; her tüketici `coremusic/shared-infrastructure: ^2.0` ister. PSR-4 kökü `CoreMusic\` → `src/`'tir. `shared/` içinde test altyapısı (`tests/`, `phpunit.xml`), yapılandırma şablonları (`config/`) ve şema geçmişi (`database/migrations/`) de paketle birlikte yaşar — çünkü bunlar paketin sözleşmesinin parçasıdır, uygulama katmanı değildir.

---

## 2. Kapsam

| Kapsam içi | Kapsam dışı |
|---|---|
| `src/` — 20 modül, 203 dosya (ölçüm §3.1) | `vendor/` içeriği — üçüncü taraf kod, elle düzenlenmez |
| `tests/` — 56 dosya, 9 test grubu | `.phpunit.cache/`, `.phpunit.result.cache` — test artefaktı |
| `config/` — 8 dosya (yapılandırma şablonları + JWT anahtar çifti) | Subdomain uygulama kodu (`api.coremusic.net/` vb.) |
| `database/migrations/` — 3 migration dosyası | Frontend/CSS katmanı |
| Paket dosyaları: `composer.json`, `composer.lock`, `phpunit.xml`, `test_bypass.php` | |

> **eli10 (basit):** Bu dosya anlatıyor: `shared/` klasörünün içi ne, kim kullanır, kuralları neler. Anlatmıyor: klasördeki üçüncü taraf `vendor/` kodu ve test önbellekleri.
> **eli15 (detay):** Kapsam sınırı bilinçlidir: `vendor/` Composer'ın malıdır (2743 dosya, elle düzenlenmez); `config/` altındaki `.env.example` ve JWT çifti (`jwt/private.pem`, `jwt/public.pem`) paketin kurulum sözleşmesidir, uygulama gizli verisi değil. `test_bypass.php` (28 satır) üretim kodu değil, CLI smoke-test betiğidir (§3.1).

---

## 3. Mimari

### 3.1 Envanter (directory_tree ölçümü 2026-10-12: 15 kök girdi = 9 dosya + 6 dizin)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|---|---|---|---|---|
| `src/` — 203 dosya, 44 dizin, 20 alt dizin | Çekirdek kütüphane: AI, Api, Bootstrap, Cache, Component, Config, Contracts, Database, Device, Events, Exception, Log, Middleware, OAuth, PageRouter, Repository, Security, Session, Theme, ViewMode | `composer.json` PSR-4 tanımı | Alt domainlerde kod tekrarını önlemek için tek doğruluk kaynağı | Yeni modül/sınıf eklendiğinde; davranış değiştiğinde |
| `tests/` — 56 dosya, 21 dizin, 9 alt dizin: Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit | PHPUnit testleri (44 PHP dosyası) | `src/` davranışları | Değişikliğin paketi bozmadığını kanıtlamak | `src/` davranış değiştirdiğinde |
| `config/` — 8 dosya: `routes.php` (131 satır), `auth-routes.php` (62), `oauth-platforms.php` (253), `domain.php` (19), `.env.example`, `CLAUDE.md`, `jwt/private.pem`, `jwt/public.pem` | Rota, alan adı, OAuth platform ve ortam şablonları; JWT anahtar çifti | Subdomain gereksinimleri | Konfigürasyonun paketle birlikte taşınması | Rota/OAuth/alan adı eklendiğinde |
| `database/` — 4 dosya: 3 migration + `CLAUDE.md` | Şema geçmişi (ör. `migrations/oauth_connections_migration.php`) | Veritabanı değişiklikleri | Şemanın sürümlenmesi | Tablo/şema değiştiğinde |
| `composer.json` — 50 satır | Paket sözleşmesi: ad `coremusic/shared-infrastructure`, sürüm 2.0.0, PHP >=8.4, 5 platform + 9 bağımlılık + 2 dev bağımlılık, PSR-4, scripts `test`/`phpstan` | Ekip kararı | Paketin kimliği ve bağımlılıkları | Bağımlılık/sürüm değiştiğinde |
| `composer.lock` — 2760 satır | Kilitli bağımlılıklar: 16 prod + 27 dev = 43 paket (content-hash `6f3426a6e4c5676411252cd4a6063caf`) | `composer update` | Ortamlar arası birebir aynı kurulum | Yalnızca `composer update` ile |
| `phpunit.xml` — 39 satır | Test yapılandırması (bootstrap, test dizinleri) | PHPUnit kuralları | Test çalıştırma standardı | Test kapsamı değiştiğinde |
| `test_bypass.php` — 28 satır | CLI smoke test: `BypassAuthMiddleware` + `SecurityHelper::isTestBypassActive` doğrulaması (`development`/`test_mode=true` ortamında) | Middleware davranışı | Bypass mantığını elle hızlı sınamak | Bypass davranışı değiştiğinde |
| `AGENTS.md` — 105 satır | Alt kural dosyası (boot zinciri 2. adım) | Kök kurallar | Kural detayını ana dosyadan ayırmak | Kural değiştiğinde |
| `CLAUDE.md` — 203 satır | Proje kuralları SSOT (boot zinciri 1. adım) | Kök kurallar + vault | Agent boot'unun tek kaynağı | Kural değiştiğinde |
| `WORKFLOW.md` — 153 satır | İş akışı fazları | Ekip süreci | Sürecin tek kaynağı | Süreç değiştiğinde |
| `CONTEXT.md` — bu dosya | Dizin haritası | 2026-10-12 ölçümü | Yeni okuyucuya hızlı bağlam | Dizin yapısı değiştiğinde |
| `vendor/` — 2743 dosya, 325 dizin, 16 paket dizini + `autoload.php` | Bağımlılık kodu (2464 PHP dosyası) | `composer install` | Paketlerin yerel kurulumu | Elle asla — yalnız Composer |
| `.phpunit.cache/` (1 dosya: `test-results`) + `.phpunit.result.cache` | Test önbelleği/artefaktı | PHPUnit çalıştırması | Geçmiş test sonucu hızlandırması | Elle asla |

> **eli10 (basit):** 15 kök girdi var: 9 dosya + 6 klasör. Kod `src/`'te, test `tests/`'te, ayarlar `config/`'te, kurallar üç `.md` dosyasında, üçüncü taraf kod `vendor/`'da durur.
> **eli15 (detay):** Toplam 3024 dosya / 392 dizin (kök hariç); bunun 2743'ü `vendor/`'da olduğundan proje kodu gerçekte küçüktür: 217 PHP dosyası (src 165 + tests 44 + config 4 + database 3 + test_bypass 1). Proje dışı uzantı dağılımı: 52 `.md`, 6 `.pem` (2'si `config/jwt/` çifti, kalan 4'ün konumu ölçümden bilinmiyor), 1 `.json`, 1 `.lock`, 1 `.xml`, 1 `.example`, 1 `.cache`, 1 uzantısız. `src/` içindeki 20 modül Composer PSR-4 köküne görelidir; `tests/` 9 grubu `src/` modülleriyle birebir örtüşmez (Fixtures ayrı bir gruptur).

### 3.2 Composer Bağımlılıkları (`composer.json` 50 satır + `composer.lock` 2760 satır)

| Tür | Sayı | Paketler |
|---|---|---|
| Platform | 5 | `php >=8.4`, `ext-apcu`, `ext-json`, `ext-mbstring`, `ext-pdo` |
| Prod bağımlılık | 9 | `firebase/php-jwt`, `nyholm/psr7`, `php-di/php-di`, `psr/cache`, `psr/container`, `psr/event-dispatcher`, `psr/log`, `respect/validation`, `symfony/event-dispatcher` |
| Dev bağımlılık | 2 | `phpunit/phpunit ^10.5`, `phpstan/phpstan ^1.10` |
| Lock kaydı | 43 | 16 prod + 27 dev (lock, json'daki 9+2'den fazladır; transitive bağımlılıklar dahil) |

> **eli10 (basit):** Paket, 9 eklenti + PHPUnit + PHPStan'e ihtiyaç duyar. `composer.lock` 43 paketin tam listesini kilitler.
> **eli15 (detay):** Prod ağacının çekirdeği DI (`php-di`), JWT (`firebase/php-jwt`), PSR arayüzleri ve `symfony/event-dispatcher`'dır — yani `shared/` bir uygulama değil, uzantılara açık bir altyapı paketidir. Scripts: `composer test` (PHPUnit), `composer phpstan`. Lock'taki 43 sayı, transitive bağımlılıklarla şişer; güncelleme yalnızca `composer update` ile yapılır, lock elle dokunulmaz.

### 3.3 Tüketici Subdomain'ler (composer.json `repositories` + `require` kontrolü)

| Subdomain | Path-repo | `require shared` | Not |
|---|---|---|---|
| `api.coremusic.net` | `../shared` (symlink:true) | `^2.0` | — |
| `auth.coremusic.net` | `../shared` (symlink:true) | `^2.0` | — |
| `home.coremusic.net` | `../shared` (symlink:true) | `^2.0` | — |
| `media.coremusic.net` | yok | yok | `shared/`'i kullanmaz |
| `assets.coremusic.net` | — | — | `composer.json`'ı yok |

`shared/vendor` dizini **junction/reparse noktası olup olmadığı UNKNOWN**: mevcut araçlar reparse bayrağını gözlemleyemez (dosya bilgisi bu alanı döndürmez). Çapraz kontrol: `shared/vendor` paket dizinleri (phpstan, theseer) `api.coremusic.net/vendor`'dan (coremusic, graham-campbell, vlucas) farklıdır — ikisi aynı ağaç değildir.

> **eli10 (basit):** api, auth ve home siteleri ortak klasörü "yol linki"yle kullanır; media kullanmaz, assets'in composer'ı bile yok. `vendor/`'un sabit bağlantı (junction) olup olmadığı bilinemiyor.
> **eli15 (detay):** Path-repo + symlink, `shared/` üzerindeki her değişikliğin anında üç tüketiciye yansması demektir (yeniden kurulum gerekmez) — bu yüzden `shared/`'de kırıcı değişiklik üç siteyi aynı anda bozar. Junction bilinmiyor çünkü dosya sistemi meta-bilgisi (reparse point) okunamıyor; paket dizini kesişimi bunu kanıtlıyor: `shared/vendor` ve `api.vendor` farklı ağaçlardır, ortak mount noktası varsayılamaz.

### 3.4 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

| Sıra | Dosya | Ne verir |
|---|---|---|
| 1 | `shared/CLAUDE.md` | Proje kuralları SSOT — boot'un ilk dosyası |
| 2 | `shared/AGENTS.md` | Kural detayı, iş akışı ipuçları |
| 3 | `shared/WORKFLOW.md` | Süreç/faz tanımı |
| 4 | `shared/CONTEXT.md` (bu dosya) | Dizin haritası, bağımlılık ve envanter gerçeği |
| 5 | `../.ai/CLAUDE.md` → `../.ai/AGENTS.md` → `../.ai/WORKFLOW.md` | Vault katmanı (yalnız ihtiyaç anında) |

> **eli10 (basit):** Okuma sırası: önce klasörün kendi kural dosyaları, sonra gerekirse vault'taki genel dosyalar.
> **eli15 (detay):** Kök ↔ vault ilişkisi asimetriktir: `shared/` kuralları (CLAUDE/AGENTS/WORKFLOW) klasöre özeldir; vault genel geçerlidir. Çelişki hâlinde önce klasör kuralı, sonra disk ölçümü, en son vault — sırasıyla önceliklidir. `../.ai/` referansları yalnız görevin gerektirdiği anda okunur (boot'a dahil değildir).

### 3.5 Çelişki Kaydı (eski CONTEXT.md ↔ disk — disk kazanır)

| Eski dosya iddiası (2026-10-03) | Disk ölçümü (2026-10-12) | Karar |
|---|---|---|
| `config/` = 5 dosya | 8 dosya (6 kök: routes, auth-routes, oauth-platforms, domain, `.env.example`, `CLAUDE.md` + `jwt/` altında 2 pem) | Disk kazandı |
| `tests/` = 4 grup (Api, Events, OAuth, Unit) | 9 grup: Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit | Disk kazandı |
| `src/` = 20 alt dizin | 20 alt dizin — doğrulandı | İddia doğru |
| Frontmatter sürümü 1.0.0 | `composer.json` sürümü 2.0.0 | Disk kazandı |

> **eli10 (basit):** Eski dosya üç yerde yanlıştı (config dosya sayısı, test klasörü içeriği, sürüm); yenisi ölçüme göre düzeltildi.
> **eli15 (detay):** Çelişki matrisi zorunlu bir denetimdir: her satır eski bir iddia ile güncel disk ölçümünü karşılaştırır. `src/` sayımı doğru çıktı (20/20); `config/` ve `tests/` büyüdü; sürüm `composer.json`'a eşitlendi. Eski dosyadaki mimari referans tabloları bu sürümde kaldırıldı — bkz. §1 notu.

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| İçerik | Neden ayrı dosyada? |
|---|---|
| `composer.json` / `composer.lock` | Composer bu dosyaları makine olarak okur; açıklama metni onları bozar |
| `phpunit.xml` | PHPUnit'un kendi kurulum dosyasıdır; test çalıştırma motoru bunu bekler |
| `config/*.php` | Çalışma zamanı yapılandırması kod tarafından `require` edilir; `.md` olamaz |
| `database/migrations/` | Şema geçmişi zamana sıralıdır; her migration ayrı ve değiştirilmez dosyadır |
| `CLAUDE.md` / `AGENTS.md` / `WORKFLOW.md` | Kurallar boot'ta ayrı ayrı okunur; tek dosya token bütçesini aşar |

> **eli10 (basit):** Her dosya bir makine tarafından okunuyor ya da boot sırasında ayrı ayrı gerekiyor; hepsi bir araya konsa hiçbiri çalışmazdı.
> **eli15 (detay):** Ayrıştırma keyfi değil, tekniktir: Composer/PHPUnit dosyaları makine sözleşmesi, `config/` ve migration'lar kod bağımlılığı, üç kural dosyası ise boot zinciri ve token bütçesi gereği ayrıdır. `.md` ile `.php` karıştırılmaz; belge katmanı (bu dosya) yalnız harita çizdir, kod katmanı yaşar.

---

## 4. Kurallar

| # | Kural | Kanıt |
|---|---|---|
| 1 | Kaynak kod yalnız PSR-4 altındadır: `CoreMusic\` → `src/` | `composer.json` (autoload bölümü) |
| 2 | `vendor/` elle düzenlenmez; tek yazıcı `composer install/update`'tir | Composer sözleşmesi |
| 3 | `composer.lock` elle düzenlenmez; kırılganlık `composer update` ile bilinçli alınır | Lock dosyası (2760 satır) |
| 4 | Tüketici subdomain'ler `^2.0` path-repo ile bağlanır; kopya kod taşımaz | api/auth/home `composer.json` |
| 5 | Yeni bağımlılık önce `composer.json`'a, sonra `composer update` ile lock'a girer | §3.2 |
| 6 | `config/jwt/*.pem` çifti paket sözleşmesidir; üretim ortamında yeniden üretilir | §3.1 envanter |
| 7 | Testler `tests/` altındadır; üretim kodu `tests/`'ten asla import etmez | `phpunit.xml` + dizin yapısı |
| 8 | `test_bypass.php` yalnız geliştirme ortamı içindir (`env=development`, `test_mode=true`) | Betiğin ilk satırı: `declare(strict_types=1)` + ConfigManager ayarları |

> **eli10 (basit):** Kod `src/`'te, bağımlılıklar Composer'la, kilit dosyası elle oynanmaz, testler üretim kodunu bozmaz.
> **eli15 (detay):** Kuralların hepsi disk kanıtına bağlıdır (son sütun). En kritik ikisi: path-repo + symlink (§3.3) sayesinde `shared/` değişikliği anında üç siteye yansır — bu hız kırıcı değişiklik için bilinçli bir test kapısı (§6) gerektirir; ve lock dosyasının dokunulmazlığı, 43 paketlik ağacın bozulmasını önleyen tek mekanizmadır.

---

## 5. Workflow

| Adım | Aksiyon | Çıktı |
|---|---|---|
| 1 | İhtiyaç belirlenir: hangi subdomain, hangi davranış | Değişiklik kapsamı |
| 2 | `src/` ilgili modüle yazılır (mevcut pattern'lere uygun) | Yeni/değişen sınıf |
| 3 | `tests/` ilgili gruba test eklenir | Regresyon güvencesi |
| 4 | `composer test` + `composer phpstan` çalıştırılır | Yeşil test + 0 analiz hatası |
| 5 | Gerekiyorsa `config/` veya `database/migrations/` güncellenir | Şema/ayar değişikliği |
| 6 | Tüketici subdomain'de entegrasyon doğrulanır (path-repo symlink sayesinde yeniden kurulum gerekmez) | Uçtan uca doğrulama |

> **eli10 (basit):** Yaz → test et → statik analiz → ayar/migration gerekirse güncelle → site üzerinde dene.
> **eli15 (detay):** Adım 4 gate'tir: PHPUnit ve PHPStan geçmeden deploy yok. Adım 6'nın ucuzluğu symlink'ten gelir; aynı ucuzluk riskin de kaynağıdır — `shared/`'deki hata üç subdomain'de aynı anda görünür, bu yüzden gate atlanmaz.

---

## 6. Doğrulama

| Komut | Ne doğrular | Beklenen |
|---|---|---|
| `composer test` | 44 test dosyası (PHPUnit 10.5) | Yeşil, 0 hata |
| `composer phpstan` | Statik analiz (PHPStan ^1.10) | 0 hata |
| `php test_bypass.php` | `BypassAuthMiddleware` + test bypass aktifliği (development) | 5 adım çıktısı, exception yok |
| `composer validate` | `composer.json`/`lock` tutarlılığı | geçerli |
| Dizin sayımı (directory_tree) | Envanter tablosu (§3.1) tazeliği | 15 kök girdi |

> **eli10 (basit):** Beş komutla paketin sağlığı kontrol edilir: test, analiz, bypass smoke testi, composer tutarlılığı, envanter tazeliği.
> **eli15 (detay):** Doğrulama zinciri katmansızdır: hepsi yerel çalışır, CI beklenmez. `test_bypass.php` yalnız development ortam koşuluyla çalışır (session cookie kapatılır, `_auth` alanı izlenir) — production'da bu betik çalışmaz, çalışmamalıdır. Envanter tazeliği, §3.1 tablosundaki sayıların diskle eşleşmesi demektir; dizin büyüdüğünde tablo da güncellenir.

---

## 7. Referanslar

| Referans | Ne için |
|---|---|
| [shared/CLAUDE.md](CLAUDE.md) | Proje kuralları SSOT |
| [shared/AGENTS.md](AGENTS.md) | Kural detayı |
| [shared/WORKFLOW.md](WORKFLOW.md) | Süreç/fazlar |
| [shared/composer.json](composer.json) | Paket sözleşmesi (sürüm, bağımlılıklar, PSR-4) |
| [shared/phpunit.xml](phpunit.xml) | Test yapılandırması |
| [config/routes.php](config/routes.php) | Rota tanımı (131 satır) |
| [src/Middleware/MiddlewarePipeline.php](src/Middleware/MiddlewarePipeline.php) | Middleware zinciri örneği |
| [database/migrations/oauth_connections_migration.php](database/migrations/oauth_connections_migration.php) | Migration örneği |
| [`.ai/CLAUDE.md`](../.ai/CLAUDE.md) | Vault anayasası |
| [`.ai/AGENTS.md`](../.ai/AGENTS.md) | Vault agent registry |
| [`.ai/WORKFLOW.md`](../.ai/WORKFLOW.md) | Vault süreç tanımı |
| [`.ai/index.md`](../.ai/index.md) | Vault indeksi |
| [`.ai/log.md`](../.ai/log.md) | Vault oturum günlüğü |
| [`.ai/.templates/index.md`](../.ai/.templates/index.md) | Şablon indeksi |
| [`.ai/.templates/frontend/context-template.md`](../.ai/.templates/frontend/context-template.md) | Bu dosyanın bağlam şablonu |
| [`.ai/.templates/documentation/context-md-template.md`](../.ai/.templates/documentation/context-md-template.md) | CONTEXT.md kılavuzu |
| [`.github/CONTEXT.md`](../.github/CONTEXT.md) | Referans CONTEXT örneği |
| [api.coremusic.net/CONTEXT.md](../api.coremusic.net/CONTEXT.md) | Referans CONTEXT örneği |
| [`.workflows/vault-sync.md`](../.workflows/vault-sync.md) | Vault senkronizasyon akışı |
| [Root CLAUDE.md](../CLAUDE.md) | Global boot pointer |
| [Root AGENTS.md](../AGENTS.md) | Global agent kuralları |

> **eli10 (basit):** Buradaki linkler bu klasörün kurallarını, paket bilgisini ve genel vault dosyalarını gösterir.
> **eli15 (detay):** Referanslar üç katmandır: (1) klasör içi dosyalar — doğrudan kanıt; (2) `.ai/` vault — genel kurallar, yalnız ihtiyaç anında; (3) kardeş CONTEXT örnekleri (`.github/`, `api.coremusic.net/`) — biçim referansı. Her link disktedir; kırık link yoktur (2026-10-12 doğrulandı).

---

**Template Version:** 1.0.0 · **Şablonlar:** [context-template](../.ai/.templates/frontend/context-template.md) · [context-md-template](../.ai/.templates/documentation/context-md-template.md) · **Ölçüm:** 2026-10-12 · **Doc Version:** 2.0.0
