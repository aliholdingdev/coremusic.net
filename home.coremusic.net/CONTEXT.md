---
title: "CoreMusic — home.coremusic.net Context"
type: docs
category: domain
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — home.coremusic.net Context

**docType:** context · **Klasör:** `home.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`home.coremusic.net/`, CoreMusic'in **ana medya paneli PHP uygulamasıdır**: `index.php` üzerinden Shared SPA Router (PageRouterKernel) ile sayfayı üretir, CSS/JS'i `assets.coremusic.net`'ten çağırır, kimliği `include/Auth/HomeAuthBridge.php` üzerinden auth alan adına devreder. Bu doküman klasörün disk envanterini (kök 20 girdi = 14 dosya + 6 dizin; yineleme 2168 girdi = 1865 dosya + 303 dizin; proje PHP 38 / vendor PHP 1591 — ölçüm 2026-10-12), komşu ilişkilerini ve vault ↔ disk çelişkilerini kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: K-space katman mimarisi atıfları (K000–K020, A0–A5, `.ai/architecture/`) bu sürümde bilinçli olarak bulundurulmaz — kaldırıldı, yerine yenisi üretilmedi.

| Karar | Kaynak (disk, 2026-10-12) |
|-------|---------------------------|
| Tek giriş kapısı = `index.php` (Shared SPA Router / PageRouterKernel) | `index.php` başlık yorumu + 29 satır okuma ölçümü |
| Bağımlılık yolu: `composer.json` path repo `../shared` (symlink) → `coremusic/shared-infrastructure ^2.0` | `composer.json` 41 satır doğrudan okuma |
| Test kapısı: phpunit ^10.5, `composer.json` `scripts` bloğu YOK | `composer.json` + `phpunit.xml` 18 satır okuma |
| `pages/` = 4 sayfa + 4 parça (redirect.php YOK, history/nowplaying YENİ) | `list_directory pages/` ölçümü |
| Klasör dokümanları `CLAUDE.md` + `AGENTS.md` + `WORKFLOW.md` bu üretimde yalnız OKUNDU | dosya varlık kontrolü |

> **eli10 (basit):** Bu dosya, home klasöründe ne olduğunu, hangi dosyaların bulunduğunu ve her sayının nereden geldiğini yazar.
> **eli15 (detay):** Ayrı yazıldı çünkü envanter koddan hızlı değişir ve eski bilgi yeni oturumu yanlış yönlendirir. Okunması, giriş noktasından test kapısına kadar gerçek dosya listesini gösterir. Yazılması, iddiaların bu oturumun ölçümüyle kanıtlanmasını şart koşar. Ölçülmeyen sayı bu dosyaya girmez; girerse bir sonraki görev yanlış envanterle çalışır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `home.coremusic.net/` kök + `config/`, `include/`, `pages/`, `tests/` envanteri | `vendor/` içeriği (1816 dosya, Composer üretimi — salt okunur) |
| home ↔ auth / assets / shared / api komşuluk haritası | `../shared/src/` ortak katman (bağımlı olunan — ayrı context) |
| Vault ↔ disk çelişki kaydı (§3.5) | `config/.env` içeriği (okunmaz, kopyalanmaz, yazılmaz) |
| Boot okuma sırası + kök ↔ vault ilişkisi | Kural/rol metinlerinin kendisi → `CLAUDE.md` / `AGENTS.md` (dokunulmaz) |

- **Kullananlar:** Backend Architect (birincil), UI Designer (`pages/*.php` görünüm), QA Engineer (`tests/`), MO (bu doküman).
- **Ön koşul:** `index.php`, `autoload.php`, `composer.json`, `phpunit.xml`, `header.php`, `CLAUDE.md` bu oturumda okunmuş; envanter `list_directory` + `search_files` + `get_file_info` ile ölçülmüş; kanıt yoksa sayı `UNKNOWN` yazılır.
- **Dokunulmaz:** bu görevde yalnız `CONTEXT.md` yazılır; başka dosya oluşturulmaz, silinmez, değiştirilmez.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, komşularla ilişkisi ve çelişki kaydı anlatılır; başka klasörlerin içeriği konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman shared katmanının ya da auth kodunun tarifine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçümün ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Envanter (list_directory + search_files + get_file_info ölçümü 2026-10-12)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `index.php` | Tek giriş kapısı (front controller) | 29 satır / 796 bayt: `autoload.php` + `config/constants.php` require, `DEBUG_MODE`'da `opcache_reset()`, `RuntimeBootstrap::boot()`, sonra `config/app.php` → `config/config.php` → `config/bootstrap.php` | Tüm home istekleri tek kapıdan geçsin, bootstrap sırası tek yerde olsun | Routing/bootstrap zinciri değişince (ADR + onay) |
| `autoload.php` | Sınıf yükleyici köprüsü | 49 satır / 1844 bayt: önce `vendor/autoload.php`, yoksa `../shared/vendor/autoload.php` + 8 `CoreMusic\Home\` prefixi elle kayıt, ikisi de yoksa 503 çıkışı | Bağımlılık ve namespace tek yerden yüklensin | Composer/namespace düzeni değişince |
| `header.php` · `footer.php` | Ortak HTML kabuk parçaları | 7045 · 11308 bayt: `CoreMusic\Device\DeviceManager` (shared), `ASSETS_URL` varsayılanı `http://assets.coremusic.net`, cookie/session çıktılarının kaçışlı basımı | Üst/alt kabuk tek yerde dursun | Mockup veya asset yolu değişince |
| `composer.json` · `composer.lock` | Paket tanımı + sürüm kilidi | json 41 satır / 938 bayt: php >=8.4, php-di ^7.0, psr/log ^3.0, psr/container ^2.0, coremusic/shared-infrastructure ^2.0 (path repo `../shared`, symlink), require-dev phpunit ^10.5, PSR-4 `CoreMusic\Home\` → `include/`, autoload-dev `CoreMusic\Home\Test\` → `tests/`; `scripts` bloğu YOK | Her makine aynı sürümü kursun | Bağımlılık eklenince/çıkarılınca |
| `config/` (dizin) | Ayar + bootstrap zinciri | 5 dosya: `.env` (salt — içeriği okunmaz/yazılmaz), `app.php` 1329, `bootstrap.php` 4528, `config.php` 731, `constants.php` 4761 bayt; `bootstrap.php` satır 103 `../shared/config/routes.php` dosyasını yükler | Ayar ve rota koddan ayrı dursun | Yeni sabit veya rota kaydı eklenince |
| `include/` (dizin) | Home'a özgü PHP kodu | 14 `.php` / 8 alt dizin (Auth 1, Class 3, Component 5, Container 1, Interfaces 1, Repository 1, Session 1, Stream 1 — §3.2) | İş mantığı sayfa şablonundan ayrı dursun | Home davranışı değişince |
| `pages/` (dizin) | Sayfa şablonları | 8 `.php`: kök 4 — `home.php` 7260 (v5.0.0 yorumu), `history.php` 7931 (v1.0.0), `nowplaying.php` 7243 (v1.0.0), `health.php` 308 bayt; `components/` 4 — `player-info`, `recent-tracks`, `welcome-banner`, `widget-grid` | Görünüm tek yerde, parça ayrı dosyada olsun | Mockup/parça tasarımı değişince |
| `tests/` (dizin) | PHPUnit testleri | 7 `.php`: Unit 6 — `Bootstrap/EntryPointCacheSafetyTest`, `Component/HomeSongButtonTest`, `Component/RecentTracksComponentTest`, `Layout/HomeLayoutVariantTest`, `Loader/ComponentLoaderTest`, `Page/HomeChromeSmokeTest`; Support 1 — `FakeComponent.php` | Bileşen ve giriş noktası davranışı sabitlensin | Kod değişince (QA) |
| `phpunit.xml` · `test-responsive-layout.php` · `.phpunit.cache/` | Test ayarı + el ile yerleşim testi + test önbelleği | xml 18 satır / 581 bayt: bootstrap `vendor/autoload.php`, suite `tests/Unit`, source `include/`, `cacheDirectory=.phpunit.cache`; `test-responsive-layout.php` 8273 bayt (assets linkleri doğrudan çağırır); `.phpunit.cache/` 1 dosya (üretim artığı) | Test tek komutla koşsun, istekler doğru yere düşsün | Kapsam veya test ayarı değişince |
| `.htaccess` · `web.config` | Apache ve IIS yönlendirme | 1072 · 2310 bayt (uzantı ve boyut `get_file_info`) | Her iki sunucuda istek uygulamaya düşsün | Sunucu kuralı değişince (DevOps) |
| `vendor/` (dizin) | Kurulu 3. taraf paketler | 1816 dosya / 282 dizin; bunların 1591'i `.php` (toplam PHP 1629 − proje PHP 38) | Çalışma zamanı hazır olsun | Yalnız `composer install` — elle değil |
| `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md` | Klasör kural / rol / süreç / envanter dokümanları | 7345 · 2946 · 25183 bayt + bu dosya (öncesi 28597 bayt / 325 satır); `WORKFLOW.md` §1–§7 (grep `^## ` ölçümü, ilk satır 21, son satır 284) | Bilgi dört farklı hızda değişir | Yalnız vault-sync (MO) |

**`index.php`**
> **eli10 (basit):** Uygulamayı açan tek dosya: önce kitaplıkları ve ayarları yükler, sonra yönlendiriciyi başlatır.
> **eli15 (detay):** Ayrıdır çünkü giriş noktası tek olunca hangi isteğin nereden geçtiği belli olur. İçine sırayla autoload, constants, app config, bootstrap bağlanır; geliştirme ortamında opcache temizlenir. Okunması bu sırayı gösterir. Değişiklik yalnız ADR'li sıra değişikliğinde yapılır; değişmezse sayfalar açılmaz.

**`autoload.php`**
> **eli10 (basit):** Sınıfları raftan çeken köprü; kendi kurulumu yoksa ortak kurulumu kullanır.
> **eli15 (detay):** Elle yazılmıştır çünkü iki ayrı vendor (home + shared) ve sekiz namespace tek yükleyiciye sığmaz. Okunması, `CoreMusic\Home\` sınıflarının hangi dizinden geldiğini gösterir. Bağımlılık düzeni değişince düzenlenir. Bozulursa hiçbir sınıf bulunamaz ve sayfa 503 ile döner.

**`header.php` · `footer.php`**
> **eli10 (basit):** Her sayfada ortak görünen üst ve alt parçalar.
> **eli15 (detay):** İkisi ayrıdır çünkü her sayfada aynıdırlar; tek dosyaya gömülse diğer sayfalara taşınmaz. İçine cihaz yöneticisi, varlık adresi ve kaçışlı kullanıcı verisi girer. Mockup veya asset yolu değişince birlikte düzenlenir. Değişmezse sayfalar arasında tutarsız kabuk oluşur.

**`composer.json` · `composer.lock`**
> **eli10 (basit):** Kimlik kartı, alet listesi ve kilitli sürümler.
> **eli15 (detay):** Lock ayrıdır ki her makine aynı sürümü kursun; json tek başına sürüm kaymasına yol açar. Okunmaları, `../shared` path bağlantısını ve test sürümünü (phpunit ^10.5) gösterir. Bağımlılık eklenince birlikte düzenlenir. Elle yazılırsa farklı makinelerde farklı kurulum doğar.

**`config/` (dizin)**
> **eli10 (basit):** Ayarların, sabitlerin ve gizli anahtarların durduğu klasör.
> **eli15 (detay):** Ayrıdır çünkü ayar koddan daha sık değişir ve gizli içerir. İçine uygulama ayarı, bootstrap zinciri, nesne yapılandırması, sabitler ve ortam dosyası girer; bootstrap buradan ortak rota dosyasını çağırır. `.env` içeriği hiçbir yere yazılmaz. Yeni sabit eklenince yalnız `constants.php` düzenlenir.

**`include/` (dizin)**
> **eli10 (basit):** Home'un kendi iş kurallarının bulunduğu klasör.
> **eli15 (detay):** Ayrıdır ki `pages/` yalnız görünüm yazsın; mantık oraya girerse sayfa okunmaz hâle gelir. İçine kimlik köprüsü, bileşen sınıfları, konteyner, oturum, veri kaynağı ve yayın kodu girer. Okunması katman sınırını gösterir. Home davranışı değişince buraya dokunulur.

**`pages/` (dizin)**
> **eli10 (basit):** Ekrana basılan dört sayfa ve onların dört parçası.
> **eli15 (detay):** Ayrıdır çünkü görünüm ile iş mantığı farklı hızda değişir. İçine HTML çıkısı ve bileşen çağrıları girer, sorgu girmez; veri `MusicRepository` üzerinden gelir. Mockup değişince burası güncellenir. Mantık buraya yazılırsa sayfa okunmaz hâle gelir.

**`tests/` (dizin)**
> **eli10 (basit):** Kodun doğru çalıştığını kanıtlayan yedi test dosyası.
> **eli15 (detay):** Ayrıdır ki kod değiştiğinde test yalnızca doğrulasın ve test kodu taklit etmesin. İçine giriş noktası, yükleyici, yerleşim varyantı ve bileşen soruları girer. Yeni bileşen gelince test de eklenir. Kapı yeşil olmadan iş bitmiş sayılmaz.

**`phpunit.xml` · `test-responsive-layout.php` · `.phpunit.cache/`**
> **eli10 (basit):** Sınavın kuralları, tarayıcıda el ile denenen yerleşim sayfası ve sınav artığı klasör.
> **eli15 (detay):** Üçü ayrıdır çünkü biri kural, biri deneme, biri üretilen sonuçtur. Okunması, hangi klasörlerin kapsam içinde olduğunu (`include/`) ve önbellek konumunu gösterir. Test ayarı değişince `phpunit.xml` düzenlenir. `.phpunit.cache` elle düzenlenmez, silinip yeniden üretilir.

**`.htaccess` · `web.config`**
> **eli10 (basit):** İki sunucunun (Apache ve IIS) yönlendirme kuralları.
> **eli15 (detay):** İkisi ayrıdır çünkü sunucular kural dilini farklı okur; tek dosya her ikisinde çalışmaz. Okunmaları, isteğin nereye düştüğünü gösterir. Sunucu kuralı değişince DevOps düzenler. Silinirse yanlış yollar 404 veya doğrudan ham dosya servisi verir.

**`vendor/` (dizin)**
> **eli10 (basit):** Kurulu hazır aletlerin kutusu; elle dokunulmaz.
> **eli15 (detay):** Composer üretir ve Composer siler; elle girilen dosya güncellemede kaybolur. Okunması yalnız envanter amaçlıdır (1816 dosya, 1591 PHP). Yalnız `composer install` ile değişir. Elle düzenlenirse güvenlik yamaları şaşar ve sürüm farkı doğar.

**`CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md`**
> **eli10 (basit):** Bu klasörün kural, rol, süreç ve envanter defterleri; dördü ayrı ayrı durur.
> **eli15 (detay):** Dört dosya ayrıdır çünkü bilgi, kural, rol ve süreç farklı hızda değişir; tek dosyada toplansa her revizyonda tümü okunur. Okunmaları işe doğru sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Bu üretimde yalnız `CONTEXT.md` yeniden yazıldı, diğer üçü okundu.

### 3.2 `include/` Alt Katman (8 alt dizin — list_directory ölçümü 2026-10-12)

| Alt dizin | Dosya sayısı | Dosya | Ne için | Ne zaman düzenlenir |
|-----------|--------------|-------|---------|---------------------|
| `Auth/` | 1 | `HomeAuthBridge.php` (6011 bayt) | auth alanına köprü (dosya yorumu: `auth_callback.php` auth_key alır) | Auth akışı değişince |
| `Class/` | 3 | `AbstractComponent.php`, `ComponentLoader.php`, `HomeLayoutVariant.php` | Bileşen altyapısı + tier varyantı | Bileşen iskeleti değişince |
| `Component/` | 5 | `HomeSongButton.php`, `PlayerInfoComponent.php`, `RecentTracksComponent.php`, `WelcomeBannerComponent.php`, `WidgetGridComponent.php` | Tekrar eden UI parçalarının kodu | Bileşen davranışı değişince |
| `Container/` | 1 | `HomeContainer.php` (1459 bayt) | DI konteyneri | Bağımlılık eklenince |
| `Interfaces/` | 1 | `ComponentInterface.php` (551 bayt) | Bileşen sözleşmesi | Sözleşme değişince (ADR) |
| `Repository/` | 1 | `MusicRepository.php` (4257 bayt) | Veri erişimi (sayfa sorgusu yerine) | Sorgu/şema değişince |
| `Session/` | 1 | `HomeSessionManager.php` (5240 bayt) | Oturum yönetimi tek kapıda | Oturum davranışı değişince |
| `Stream/` | 1 | `MusicStreamHandler.php` (13287 bayt) | Medya yayını akışı | Akış protokolü değişince |

**`Auth/`**
> **eli10 (basit):** Giriş bilgisini doğrulayan tek köprü dosyası.
> **eli15 (detay):** Ayrıdır çünkü kimlik işi güvenlik yüzeyidir ve dağınık yazılırsa açık doğar. İçine auth ile konuşma girer; iş akışı dosya yorumunda tarif edilir. Auth akışı değişince burası güncellenir. Başka yere taşınırsa oturum kurulumu kırılır.

**`Class/`**
> **eli10 (basit):** Bileşenlerin ortak davranışını kuran üç sınıf.
> **eli15 (detay):** Ayrıdır çünkü ortak davranış tek yerde durur. İçine soyut bileşen, yükleyici ve yerleşim varyantı girer. Yeni bileşen türü veya tier eklenince buraya dokunulur. Tekrar ederse her bileşen kendi çözümünü icat eder.

**`Component/`**
> **eli10 (basit):** Ekrandaki tekrar eden beş parçanın kodu.
> **eli15 (detay):** Ayrıdır çünkü her parça kendi dosyasında yaşar. İçine düğme, oynatıcı bilgisi, son parçalar, karşılama afişi ve widget ızgarası girer. Parça değişince yalnız o dosya değişir. Tek dosyada toplansa inceleme imkânsızlaşır.

**`Container/`**
> **eli10 (basit):** Bağımlılıkları hazırlayıp dağıtan kutu.
> **eli15 (detay):** Ayrıdır ki kurulum tek yerden yapılsın. İçine konteyner kodu girer. Bağımlılık eklenince güncellenir. Dağınık yazılırsa her dosya kendi bağımlılığını kurar ve test edilemez.

**`Interfaces/`**
> **eli10 (basit):** Bileşenlerin uyması gereken sözleşme.
> **eli15 (detay):** Ayrıdır çünkü sözleşme koddan uzun yaşar. İçine tek arayüz dosyası girer. Değişikliği ADR gerektirir. Sözleşme kayarsa yükleyici hangi sınıfın geçerli olduğunu bilemez.

**`Repository/`**
> **eli10 (basit):** Veritabanından okuma yeri.
> **eli15 (detay):** Ayrıdır çünkü sorgu sayfada yazılmaz. İçine müzik veri kaynağı girer; sayfa dosyaları bu metodu çağırır. Şema veya sorgu değişince güncellenir. Sayfaya yazılırsa veri erişimi çoğalır ve değişiklik tek yerde toplanamaz.

**`Session/`**
> **eli10 (basit):** Oturumu yöneten tek kapı.
> **eli15 (detay):** Ayrıdır çünkü oturum tüm sayfaları etkiler. İçine oturum yöneticisi girer. Kural değişince burası güncellenir. Klasör kuralı gereği oturum işlemleri başka dosyaya taşınamaz.

**`Stream/`**
> **eli10 (basit):** Ses yayınını taşıyan dosya.
> **eli15 (detay):** Ayrıdır çünkü yayın akışı ağ işidir, sayfa işi değildir. İçine akış işleyicisi girer. Akış protokolü değişince dokunulur. Sayfaya gömülirse ağ hatası tüm görünümü çökertir.

### 3.3 Komşu İlişkileri (home ↔ sistem)

| Komşu | Yön | Ne taşır (disk kanıtı) | Neden | Ne zaman güncellenir |
|-------|-----|------------------------|-------|----------------------|
| `../auth.coremusic.net` | home → auth | `include/Auth/HomeAuthBridge.php` yorumu: `auth_callback.php` auth_key alır, başarısızsa login'e yönlendirir; `pages/auth_callback.php` bu klasörde YOK (dosya listesi ölçümü) | Kimlik işi tek alanda toplansın | Auth akışı değişince |
| `../assets.coremusic.net` | home → assets | `header.php` satır 24 · `footer.php` satır 33 · `pages/home.php` satır 56 · `pages/nowplaying.php` satır 47: `ASSETS_URL` varsayılanı `http://assets.coremusic.net`; `config/constants.php` satır 100 aynı adresi tanımlar; bileşen başlıklarında CSS/JS kontratı | CSS/JS üretimi assets'te kalsın | Asset yolu veya kontrat değişince |
| `../shared` | home → shared | `composer.json` path repo `../shared` (symlink) → `coremusic/shared-infrastructure ^2.0`; `autoload.php` satır 11 `../shared/vendor/autoload.php`; `config/bootstrap.php` satır 103 `../shared/config/routes.php`; `header.php` `CoreMusic\Device\DeviceManager` | Ortak motor tek yerde dursun | Ortak katman değişince |
| `../api.coremusic.net` | home → api | VERIFICATION REQUIRED — bu klasörde `/api/v1` veya `api.coremusic` çağrısı grep ile bulunamadı (2026-10-12); yalnız komşuluk yönü yazılır | Bağımlılık kanıtlanmadan iddia edilmez | Çağrı kanıtlanınca tablo güncellenir |
| `../.ai` | home → vault | `header.php` başlık yorumu (`.ai/.png/home-1024/` + `home-1920/`), `pages/home.php` (Figma node + PNG) ve `pages/history.php` (PNG) SSOT satırları; yeni `.md` için `.ai/.templates/` | Kural ve şablon vault'tan okunur | Mockup veya şablon değişince |

> **eli10 (basit):** Home dört yerden beslenir: kimlik kapısı, görünüm dosyaları, ortak motor ve kural vault'u; veri API'si ise henüz kanıtlanmadı.
> **eli15 (detay):** Komşuluk ayrı tablodur çünkü bağımlılık yönü ile sahiplik ayrı bilgidir; iş kodu kopyalanmaz, çağrılır. Okunması, hangi dosyanın değişirse bu klasörün kırılacağını gösterir. Yazılması sınır ihlalini (ikinci kimlik veya rota kopyası) baştan engeller. Tablo unutulursa herkes `shared` kodunu buraya taşımaya kalkar.

### 3.4 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök (AGENTS.md + CLAUDE.md + CONTEXT.md)
  → home.coremusic.net/ (CLAUDE.md → AGENTS.md → WORKFLOW.md → CONTEXT.md)
    → .ai/ vault yalnız ihtiyaç anında (§7'deki hedeflerle)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `CLAUDE.md` | `authority: Single Source of Truth (SSOT)` + `reference.source_of_truth: .ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md · .ai/WORKFLOW.md` | Klasör bağlamı, tier matrisi, yasaklar |
| `AGENTS.md` | `authority: Single Source of Truth (SSOT)` | Envanter iddiaları + rol/recipe kuralları |
| `WORKFLOW.md` | frontmatter ölçümü bu oturumda OKUNMADI → `UNKNOWN` | §1–§7 süreç (grep `^## ` ölçümü) |
| `CONTEXT.md` (bu dosya) | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Envanter + çelişki kaydı |

> **eli10 (basit):** Önce kök kurallar, sonra bu klasörün dört dosyası, vault ise sadece ihtiyaç anında okunur.
> **eli15 (detay):** İlişki ayrı tabloda çünkü dört dosyanın da ikinci SSOT olmadığı bellidir: otorite satırları `reference.source_of_truth` ile vault'a bağlanır. Okunması boot sırasını ve otorite zincirini gösterir. Yazılması, bu dosyaların bağımsız kaynak sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla çalışır ve bilgi ayrışır.

### 3.5 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (2026-10-03): "`pages/` 7 php — home, health, redirect + components/4" | 8 php: home, history, nowplaying, health + components/4; `redirect.php` YOK | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md`: "`tests/` 5 php" | 7 php — `Page/HomeChromeSmokeTest` ve `Bootstrap/EntryPointCacheSafetyTest` eklendi | **Disk kazanır** |
| 3 | Eski `CONTEXT.md` §6 #3: "Kök 12" · "toplam 1859 dosya / vendor 1815" | kök 20 girdi (14 dosya + 6 dizin); toplam 1865 dosya; vendor 1816 dosya | **Disk kazanır** |
| 4 | `AGENTS.md` §2: "`pages/` — home, player, ayarlar, health, redirect, auth_callback" | `player`, `ayarlar`, `redirect`, `auth_callback` bu klasörde YOK | VERIFICATION REQUIRED — `AGENTS.md` bu görevde değiştirilmez, yalnız işaretlenir |
| 5 | `CLAUDE.md` §2: "`header.php` v8.0.0 · `footer.php` v11.0.0" ve §3 yapısı "`include/config/`" | `header.php` yorumu "Version: 1.0.0"; `pages/home.php` yorumu "v5.0.0"; `include/config/` dizini YOK | **Disk kazanır** (dosya yorumu + dizin listesi) |
| 6 | `composer.json` için test kapısı beklentisi (`composer test`) | `scripts` bloğu YOK; test `phpunit.xml` üzerinden koşulur | **Disk kazanır** |

> **eli10 (basit):** Eski kâğıtlardaki sayım ve sürüm satırları dosya listesiyle uyuşmuyor; uyuşmayan taraf her zaman disk.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. İçine yalnız ölçülmüş fark girer, tahmin girmez. Okunması, eski iddiayı doğru sanmayı engeller. Fark bulunca vault tarafı düzeltilir, disk değiştirilmez; olmayan dosya geri getirilmez, YOK olarak yazılır.

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (ayar → config, mantık → include, görünüm → pages, test → tests) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde durur (`config/constants.php` define bloğu) | Her dosyada ayrı değer → tutarsızlık, değişince tarama gerekir |
| 4 | **Cihaz izolasyonu** | Cihaz/tier farkı başka dosyalara yayılmaz (tier kararı `DeviceManager` + kabuk dosyalarında) | Fark tüm dosyalara sıçrar, 8+ yerde tekrar ve drift doğar |
| 5 | **Vendor karantinası** | 3. taraf kod `vendor/`'ta ayrı durur (1816 dosya) | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: ayar `config/`'te, mantık `include/`'ta, görünüm `pages/`'te, soru `tests/`'te, kurulum `vendor/`'ta durur. Böylece bir yeri değiştirince sadece o değişir, diğerleri bozulmaz. Mantık görünümün içine gömülseydi her mockup değişikliği kod revizyonu sayılır, inceleme imkânsızlaşır. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Kod öncesi keşif — onaysız dosya değişikliği, yeni dosya/dizin/bağımlılık yok | Yanlış dosyaya yazım, katman ihlali |
| 2 | `config/.env` içeriği okunup vault/log/chat'e yazılmaz (Secret Yok) | Anahtar sızıntısı |
| 3 | Oturum işlemleri yalnız `include/Session/HomeSessionManager.php` üzerinden | Çift oturum kaynağı doğar |
| 4 | Kimlik doğrulama akışı `include/Auth/HomeAuthBridge.php` dışına taşınmaz | Güvenlik yüzeyi dağılır |
| 5 | `vendor/` klasörüne elle müdahale yok (yalnız `composer install`) | Sürüm kayması, kırık kurulum |
| 6 | Mockup'ta olmayan UI öğesi eklenmez; görünüm kodundan önce mockup okunur | Kanonik mockup bozulur |
| 7 | PHP'de sunum kararı yazılmaz (`margin`, `padding`, `width`, `height`, `font-size` CSS'e aittir) | Katman ihlali |
| 8 | Yeni `.md` şablondan üretilir (Guardrail #16) | Şablon ihlali, denetlenemez dosya |
| 9 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 10 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.5 tablosunda işaretlenir | SSOT ihlali |
| 11 | Subagent `git commit` ATMAZ — yetki orkestratörde | Yetkisiz commit, revert |

> **eli10 (basit):** Bu kurallar işin sırayla, gizli bilgi sızmadan ve mockup'a sadık yürümesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanabilir olmasını garantiler. İhlalde revert + `log.md` kaydı işletilir; kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
ÖLÇ (envanter — bu oturumda) → OKU (index, autoload, composer, phpunit, header, CLAUDE, AGENTS)
  → YAZ (yalnız CONTEXT.md, şablondan §1–§7) → ÇELİŞKİ KAYDI (§3.5 — disk kazanır)
  → DOĞRULAMA (§6 tablosu) → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Şablon + hedef dosyaları oku | Şablon + kanıt | İskelet ve yasaklar baştan belli olsun | Şablon dışı, denetlenemez dosya |
| 2 | Envanter ölç (`list_directory`, `search_files`, `get_file_info`) | Ölçülen sayılar | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, eski sayının devri |
| 3 | Yaz (§1–§7, 5 sütun, eli10/eli15) | Bu dosya | Tutarlı iskelet (Guardrail #16) | Bölüm sırası ve format kayması |
| 4 | Çelişki tablosunu güncelle (§3.5) | 6 karar satırı | Eski bilgi yeni oturumu yanıltır | "redirect.php var" efsanesi sürer |
| 5 | §6 doğrulamasını çalıştır + rapor | Doğrulama + rapor | Teslimden önce kanıt | Hatalı dosya teslim edilir |

> **eli10 (basit):** İş sırayla yürür: önce say, sonra şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması kapıları önceden bilmeyi sağlar. Yazılması tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve doğrulama) hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Doldurulmamış şablon değişkeni (çift süslü parantez) kalmadı |
| 4 | Envanter | kök 20 girdi · yineleme 2168 girdi / 1865 dosya / 303 dizin · proje PHP 38 / vendor PHP 1591 / toplam 1629 · pages 8 · include 14 · tests 7 · vendor 1816 dosya — bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı `list_directory`/`search_files`/`get_file_info`/okuma ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` biçimi; §7 hedefleri diskte mevcut (2026-10-12 varlık kontrolü) |
| 7 | eli10 + eli15 | §1, §2, §3.1 (12 madde), §3.2 (8 madde), §3.3–§3.6, §4, §5 maddelerinde çift blok var (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4 #10 "disk kazanır" var |
| 9 | Boot sırası | Kök ↔ klasör ↔ vault ilişkisi §3.4'te var |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; katman mimarisi tablosu/eklentisi yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 250–400 satır bandı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör bağlamı | [[CLAUDE.md]] | Tier matrisi + yasaklar (OKUNDU, değişmedi) |
| Klasör rolleri | [[AGENTS.md]] | Envanter iddiaları + rol kuralları (çelişki kaynağı §3.5) |
| Klasör süreci | [[WORKFLOW.md]] | §1–§7 süreç akışı |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Kök vault göstergesi | [[../CONTEXT.md]] | Proje geneli envanter |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | §5 Domain Boundaries, §6 routing |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| Mockup indeksi | [[../.ai/ui-design/01-mockup-index.md]] | Mockup Before Frontend kapısı |
| Auth context | [[../auth.coremusic.net/CONTEXT.md]] | Köprü hedefinin envanteri |
| Assets context | [[../assets.coremusic.net/CONTEXT.md]] | CSS/JS sağlayıcının envanteri |
| Shared context | [[../shared/CONTEXT.md]] | Ortak katman envanteri |
| API context | [[../api.coremusic.net/CONTEXT.md]] | JSON uç tarafı (komşuluk) |
| Giriş kodu kanıtı | [[index.php]] | Bootstrap sırası |
| Autoload köprüsü kanıtı | [[autoload.php]] | Vendor + namespace zinciri |
| Paket tanımı kanıtı | [[composer.json]] | Bağımlılıklar + path repo |
| Test kanıtı | [[phpunit.xml]] | Bootstrap + suite + kapsam |
| Auth köprüsü kanıtı | [[include/Auth/HomeAuthBridge.php]] | auth_callback akışı yorumu |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12 · **docType:** context
