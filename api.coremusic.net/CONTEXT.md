---
title: "CoreMusic — api.coremusic.net Context"
type: docs
category: api
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — api.coremusic.net Context

**docType:** context · **Klasör:** `api.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`api.coremusic.net/`, CoreMusic'in **JSON-only API ağ geçididir**: `/health` canlılık ucunu ve `/api/v1/*` uçlarını JSON ile yanıtlar, SPA/HTML router kullanmaz. Bu doküman klasörün disk envanterini (13 kök dosya + 4 alt dizin; yineleme 2856 dosya / 358 dizin / 3214 girdi, `vendor/` dahil — ölçüm 2026-10-12), istek akışını ve komşu klasörlerle konuşmasını kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: K-space katman mimarisi atıfları (K000–K020, A0–A5, `.ai/architecture/`) bu sürümde bilinçli olarak bulundurulmaz — kaldırıldı, yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| JSON-only front controller (SPA router kullanmaz) | `index.php` başlık yorumu — 223 satır (okuma ölçümü 2026-10-12) |
| Envanter: 13 kök dosya, 4 alt dizin | `directory_tree` ölçümü (2026-10-12) |
| Proje PHP 12 dosya · vendor PHP 2543 · toplam PHP 2555 | `**/*.php` glob ölçümü (2026-10-12) |
| Middleware sabit sırası (ADR-020 §1.1-B.1) | `index.php` pipeline kurulum yorumu |
| auth SSOT: `../auth.coremusic.net/include/` | `composer.json` PSR-4 `CoreMusic\Auth\` + `autoload.php` `spl_autoload_register` |
| shared path repo (symlink) | `composer.json` `repositories: ../shared` → `coremusic/shared-infrastructure ^2.0` |
| Paket kilidi: 24 prod + 26 dev paket | `composer.lock` JSON ölçümü (2026-10-12) |

> **eli10 (basit):** Bu klasör, sitenin veri kapısıdır: soruları JSON olarak yanıtlar, arayüz göstermez.
> **eli15 (detay):** Ayrı yazıldı çünkü API'nin sözleşmesi (hata biçimi, cevap başlığı, kapı sırası) kodun kendisinden uzun yaşar; kod içine gömülse revizyonda kaybolur. Okunması, hangi dosyanın ne işe yaradığını ve her sayının hangi ölçümden geldiğini gösterir. Yazılması envanter ile disk gerçeğinin hizasını kanıtlar. Ölçülmeyen sayısal iddia bu dosyaya girmez; girerse eski bilgi yeni oturumları yanlış yönlendirir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `api.coremusic.net/` kök + `config/`, `include/`, `tests/`, `vendor/` envanteri | `../shared/src/` ortak katman (bağımlı olunan — ayrı context) |
| İstek akışı: alias → health → gateway → middleware → 404 | `../auth.coremusic.net/include/` (auth SSOT — oradan OKUNUR, kopyalanmaz) |
| api ↔ auth/shared komşuluk haritası | Statik varlıklar, medya, CI/CD (diğer klasörlerin konusu) |
| Vault ↔ disk çelişki kaydı | ADR metinleri (dokunulmaz — yalnız wiki-link verilir) |

- **Kullananlar:** Backend Architect (birincil), Security Engineer (middleware/başlıklar), QA Engineer (`tests/`), MO (bu doküman).
- **Ön koşul:** `index.php`, `composer.json` ve `autoload.php` bu oturumda okunmuş; envanter `directory_tree` + glob ile ölçülmüş; kanıt yoksa sayı `UNKNOWN` yazılır.
- **Dokunulmaz:** `CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md`, `config/`, `include/`, `tests/`, `vendor/` bu görevde değiştirilmez — yalnız bu dosya yazılır.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, istek yolu ve komşularla ilişkisi anlatılır; başka klasörlerin içeriği konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman ortak katmanın ya da auth kodunun tarifine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçümün ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Envanter (directory_tree + glob ölçümü 2026-10-12: 13 kök dosya, 4 alt dizin)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `index.php` | Tek giriş kapısı: alias, health, gateway dispatch | 223 satır: config yükleme, `/v1/*` aliası, `/health`, middleware zinciri, 404/500 JSON | Tüm istekler tek kapıdan geçsin, sözleşmesi tek yerde olsun | Route/middleware sırası değişince (ADR + onay) |
| `autoload.php` | Autoload köprüsü (elle yazılmış — Composer üretimi değil) | 69 satır: kendi `vendor/autoload.php` + shared vendor + `CoreMusic\Auth\` kaydı + PSR-4 fallback | Bağımlılıklar ve auth sınıfı tek yerden yüklensin | Composer/autoload düzeni değişince |
| `config/` (dizin, 4 dosya) | Uç, CORS, sabit ve uygulama ayarı | `routes.php` 120 · `constants.php` 142 · `cors.php` 28 · `app.php` 31 satır | Adres ve kural koddan ayrı dursun | Yeni uç eklenince / CORS kuralı değişince |
| `include/` (dizin, 2 dosya) | API'ye özgü kod: container + controller | `Controller/AuthController.php` 355 · `Container/ApiAuthContainer.php` 161 satır | API kodu ortak `shared/`'e karışmasın | Uç davranışı veya bağımlılık grafiği değişince |
| `tests/` (dizin, 4 dosya) | PHPUnit birim testleri | `bootstrap.php` 5 · `Unit/SmokeTest.php` 31 · `Unit/RouteConfigTest.php` 208 · `Unit/AuthControllerTest.php` 330 satır | JSON sözleşmesi test ile sabitlensin | Kod değişince (QA) |
| `phpunit.xml` | Test yapılandırması | bootstrap `tests/bootstrap.php`, suite `tests/Unit`, kapsam `include/`; 29 satır | Test tek komutla koşsun | Kapsam veya test ayarı değişince |
| `composer.json` + `composer.lock` | Paket tanımı + sürüm kilidi | json 45 satır (php >=8.4, shared-infrastructure ^2.0, fast-route, nyholm/psr7, phpdotenv, phpunit ^10.5) · lock 3264 satır / 24 prod + 26 dev paket | Her makine aynı sürümü kursun | Bağımlılık eklenince/çıkarılınca |
| `web.config` + `.htaccess` | IIS ve Apache rewrite + başlık/yasak kuralları | `web.config` 41 · `.htaccess` 42 satır; config/include/tests/vendor erişime kapalı | İstek `index.php`'ye düşsün, gizli dosya açılmasın | Sunucu kuralı değişince (DevOps) |
| `.env.example` | Ortam değişkeni örneği (anahtar içermez) | 53 satır; değerler boş/örnek | Kurulum sıfırdan tekrarlanabilsin | Yeni değişken eklenince |
| `vendor/` (dizin) | Kurulu 3. taraf paketler | 2833 dosya, 351 alt dizin, 49 paket dizini (ölçüm 2026-10-12) | Çalışma zamanı hazır olsun | Yalnız `composer install` — elle değil |
| `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md` | Klasör kural/rol/süreç/envanter dokümanları | 157 · 166 · 157 satır + bu dosya (okuma ölçümü 2026-10-12) | Bilgi dört farklı hızda değişir | Yalnız vault-sync (MO) |

**`index.php`**
> **eli10 (basit):** Uygulamayı açan ana dosya: gelen isteği ya doğrudan cevaplar ya da kapıdan (middleware) içeri geçirir.
> **eli15 (detay):** Tek dosyadır çünkü giriş noktası dağınıksa hangi isteğin nereden geçtiği bilinmez. Okunması, sıralamayı (alias → health → gateway) ve hata sözleşmesini gösterir. Yazılması, cevap başlıklarını (nosniff, katı CSP) ve kapı sırasını garanti eder. Değişiklik yalnız ADR'li sıra değişikliğinde ve onayla yapılır.

**`autoload.php`**
> **eli10 (basit):** Sınıfları raftan çeken köprü: önce kendi kurulumunu, sonra ortak kurulumu yükler.
> **eli15 (detay):** Elle yazıldı çünkü iki ayrı vendor (api + shared) ve auth klasörü tek yükleyiciye sığmaz; Composer tek başına bunu çözmez. Okunması, hangi namespace'in nereden geldiğini gösterir. Yazılması, bağımlılık düzeneği değişince gerekir. Bozulursa hiçbir sınıf bulunamaz ve istek 503 ile döner.

**`config/` (dizin)**
> **eli10 (basit):** Adres listesi, kapı kuralı ve sabit anahtarların tutulduğu dört ayar dosyası.
> **eli15 (detay):** Ayrıldı çünkü ayar ile kod farklı hızda değişir; koda gömülse her adres için kod revizyonu gerekir. Okunması, hangi uca hangi kuralın bağlandığını gösterir (`index.php` bunları `require` eder). Yeni uç eklenince `routes.php`, izin değişince `cors.php`, değer eklenince `constants.php` düzenlenir. Ayrı olmazsa ayar değişikliği güvenlik incelemesini kaybeder.

**`include/` (dizin)**
> **eli10 (basit):** API'nin kendi iki kod dosyası: uçları çeviren controller ve bağımlılıkları kuran container.
> **eli15 (detay):** Ayrıldı çünkü API'ye özgü kod ortak `shared/`'e girerse kütüphane şişer ve auth SSOT'u kopyalanmış olur. Okunması, işin nerede bittiğini (HTTP çevirisinin `include/`'ta, iş kuralının `auth.coremusic.net`'de) gösterir. Uç davranışı değişince düzenlenir. Taşınırsa iş mantığı kopyalanır, iki gerçek oluşur.

**`tests/` (dizin)**
> **eli10 (basit):** API'nin soru kâğıtları: dosya varlığı, route sözleşmesi ve auth uçları sınanır.
> **eli15 (detay):** Test ayrıdır ki kod değişince davranış korunur ve test kodu taklit etmez. Okunması, hangi sözleşmenin kilitli olduğunu (405/422/401 eşlemeleri) gösterir. Yeni uç eklenince test de eklenir, QA sorumlusundadır. Test kalkarsa sessiz kırılma fark edilmez.

**`phpunit.xml`**
> **eli10 (basit):** Sınavın kurallarını yazan yapılandırma: hangi klasörlerin ve hangi kapsamın koşulacağını belirler.
> **eli15 (detay):** Ayrı durur ki test ayarı koda gömülmesin; her koşuda değişiklik gerekmesin. Okunması, bootstrap ve kapsam (`include/`) hedefini gösterir. Kapsam değişince düzenlenir. Yanlış yazılırsa testler hiç koşulmaz veya yanlış dosyalar ölçülür.

**`composer.json` + `composer.lock`**
> **eli10 (basit):** Kimlik kartı, alet listesi ve kilitli sürümler: hangi paketlerin kurulacağını söyler.
> **eli15 (detay):** Lock ayrıdır ki her makine aynı sürümü kursun; json tek başına sürüm kaymasına yol açar. Okunması, bağımlılıkları ve `../shared` path bağlantısı ile auth autoload'unun nereden doğduğunu gösterir. Bağımlılık değişince birlikte düzenlenir. Kilit kırılırsa ortam farkı üretilir ve "bende çalışıyor" sorunu doğar.

**`web.config` + `.htaccess`**
> **eli10 (basit):** İki sunucunun (IIS ve Apache) kapı yönlendirmesi ve gizli dosya yasakları.
> **eli15 (detay):** İkisi ayrıdır çünkü sunucular kural dilini farklı okur; tek dosya her ikisinde çalışmaz. Okunması, hangi yolların 403 olduğunu ve her şeyin `index.php`'ye düştüğünü gösterir. Sunucu kuralı değişince DevOps düzenler. Silinirse `config/` ve `include/` doğrudan dışarıya açılır.

**`.env.example`**
> **eli10 (basit):** Kurulumda hangi ayarların doldurulacağını gösteren boş form; gerçek anahtar içermez.
> **eli15 (detay):** Ayrıdır çünkü gerçek `.env` gizlidir ve repoya girmez; örnek olmadan yeni kurulum tekrarlanamaz. Okunması, beklenen değişken adlarını verir. Yeni değişken eklenince güncellenir. Unutulursa eksik kurulum sonradan ancak hata logundan bulunur.

**`vendor/` (dizin)**
> **eli10 (basit):** Kurulu hazır aletlerin kutusu; elle dokunulmaz.
> **eli15 (detay):** Composer üretir ve Composer siler; elle girilen dosya güncellemede kaybolur. Okunması yalnız envanter amaçlıdır (kaç paket kurulu). Yalnız `composer install` ile değişir. Elle düzenlenirse güvenlik yamaları şaşar ve sürüm farkı doğar.

**`CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `CONTEXT.md`**
> **eli10 (basit):** Bu klasörün kural, rol, süreç ve envanter defterleri; dördü ayrı ayrı durur.
> **eli15 (detay):** Dört dosya ayrıdır çünkü bilgi, kural, rol ve süreç farklı hızda değişir; tek dosyada toplansa her revizyonda tümü okunur. Okunmaları işe doğru sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Dördü de vault-sync (MO) tarafından güncellenir; bu oturumda yalnız `CONTEXT.md` yeniden yazıldı.

### 3.2 İstek Akışı (Mimari Harita — `index.php` kanıtı)

```text
İstek (JSON)
  |
  +- [0] alias:  /v1/*  --> /api/v1/*   (tek kabul noktası, index.php "0. Alias")
  +- [1] /health --> 200 JSON {status, service, version, timestamp}
  +- [2] /api/*  --> /api/v1/auth/* ise SessionBootstrapper::ensureStarted()
  |                  MiddlewarePipeline (index.php kurulum sırası):
  |                  ResponseNormalization -> OriginCheck -> Cors -> RateLimit
  |                  -> Authentication -> [yalnız session-auth: CSRF]
  |                  -> RequestValidation -> Authorization
  |                  --> Gateway (VersionResolver + ServiceRegistry + RouteTable)
  |                        +- route action --> ApiAuthContainer::controller() --> AuthController
  |                        +- exception    --> 500 JSON {error:{SERVER_INTERNAL_ERROR}}
  +- [3] diger   --> 404 JSON {error:{NOT_FOUND}}
```

- Auth uçları: `preg_match('#^/api/v1/auth(/|$)#')` → `SessionBootstrapper::ensureStarted()` (index.php satır 114-116) — auth.coremusic.net ile aynı cookie/save path.
- Route kaydı: `config/routes.php` — method-aware tablo; Faz 1b sözleşmesi 6 POST auth ucu + `GET /v1/auth/me`.
- Domain override: `DomainConfig(dirname(__DIR__) . '/shared/config/domain.php')` (index.php satır 64).

> **eli10 (basit):** Her istek önce adrese, sonra canlılık kontrolüne, en son kapı sırasından geçip yanıt alır.
> **eli15 (detay):** Akış ayrı bölümde çünkü sıra güvenlik demektir: kapılar değişirse bir kontrol atlanabilir. Okunması, hangi isteğin hangi kapıdan geçeceğini ve hatanın neden asla ham metin dönmediğini gösterir. Yazılması `index.php` ile birebir ölçümle yapılır. Tablo kodla uyumsuzlaşsa kod kazanır, bu dosya düzeltilir.

### 3.3 Hangi Klasörlerle Konuşur?

| Komşu | Yön | Ne taşır (disk kanıtı) |
|-------|-----|------------------------|
| `../shared` | api → shared | `composer.json` path repo (`../shared`, symlink) → `coremusic/shared-infrastructure ^2.0`; ayrıca `../shared/vendor/autoload.php` `autoload.php` içinde yüklenir |
| `../auth.coremusic.net` | api → auth | PSR-4 `CoreMusic\Auth\` → `../auth.coremusic.net/include/` (`composer.json` + `autoload.php` `spl_autoload_register`) |
| `../auth.coremusic.net` (oturum) | api ↔ auth | `SessionBootstrapper::ensureStarted()` — aynı `SESSION_NAME` + `.coremusic.net` cookie (index.php yorumu, satır 112-113) |
| `../home.coremusic.net` | api ← home | Uçları JSON ile tüketen taraf; VERIFICATION REQUIRED: home kodu bu oturumda okunmadı, çağrı kanıtlanmadı — yalnız komşuluk yönü yazılır |
| `shared/config/` | api → shared | `domain.php` (DomainConfig override) + `config/jwt/` RS256 anahtar yolları (`ApiAuthContainer::jwtService()`) |
| Sunucu (IIS/Apache) | dış → api | `web.config` + `.htaccess` isteği `index.php`'ye yönlendirir, `config|include|tests|vendor` için 403 verir |

> **eli10 (basit):** API yalnız üç yerden beslenir: ortak kütüphane, auth kodu ve sunucu yönlendirmesi.
> **eli15 (detay):** Komşuluk ayrı tablodur çünkü bağımlılık yönü ile sahiplik ayrı bilgidir; iş kodu kopyalanmaz, çağrılır. Okunması, hangi dosyanın değişirse bu klasörün kırılacağını gösterir. Yazılması sınır ihlalini (ikinci auth kopyası) baştan engeller. Tablo unutulursa herkes `auth.coremusic.net` kodunu buraya taşımaya kalkar.

### 3.4 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök (AGENTS.md + CLAUDE.md)
  → api.coremusic.net/ (CLAUDE.md → AGENTS.md → WORKFLOW.md → CONTEXT.md)
    → .ai/ vault yalnız ihtiyaç anında (§7'deki hedeflerle)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `CLAUDE.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Klasör kuralları + yasaklar |
| `AGENTS.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Dosya sahipliği + routing |
| `WORKFLOW.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Adım → kapı → rapor |
| `CONTEXT.md` (bu dosya) | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Envanter + çelişki kaydı |

> **eli10 (basit):** Önce kök kurallar, sonra bu klasörün dört dosyası, vault ise sadece ihtiyaç anında okunur.
> **eli15 (detay):** İlişki ayrı tabloda çünkü dört dosyanın da ikinci SSOT olmadığı bellidir: hepsi türevdir, çelişkide vault kazanır. Okunması boot sırasını ve otorite zincirini gösterir. Yazılması, bu dosyaların bağımsız kaynak sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla çalışır ve bilgi ayrışır.

### 3.5 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (2026-10-03, 179 satır): "`index.php` 191 satır" | `index.php` 223 satır, 9645 bayt | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md`: "`autoload.php` Composer üretimi autoload.php" | Elle yazılmış köprü: api vendor + shared vendor + auth PSR-4 fallback; 69 satır | **Disk kazanır** |
| 3 | Eski `CONTEXT.md` §5 adım 5: "`composer test`" | `composer.json` içinde `scripts` bloğu YOK (doğrudan okuma); test `phpunit.xml` üzerinden koşulur | **Disk kazanır** |
| 4 | Eski `CONTEXT.md` §6 #3: "Kök 6 girdi" | 13 kök dosya + 4 alt dizin | **Disk kazanır** |
| 5 | `AGENTS.md` §7: "Template kaynağı `../.ai/.templates/php/php-template.md`" | O yol diskte YOK; gerçek dosya `../.ai/.templates/backend/php-template.md` (glob ölçümü) | **Disk kazanır** — `AGENTS.md` bu görevde değiştirilmez, yalnız işaretlenir |

> **eli10 (basit):** Eski dosyalardaki satır sayıları ve yanlış yol tarifleri var; gerçeği disk söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu gösterir. Yazılması ölçüm kanıtıyla olur. Bu tablo olmazsa eski "191 satır" ve "composer test" bilgisi yeni oturumları yanlış yönlendirir.

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (route → config, uç → include, test → tests) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde durur (`config/constants.php` define bloğu) | Her dosyada ayrı değer → tutarsızlık, değişince tarama gerekir |
| 4 | **Cihaz izolasyonu** | Sunucu/ortam farkı başka dosyalara yayılmaz | IIS/Apache farkı tüm kodlara sıçrar, drift doğar |
| 5 | **Vendor karantinası** | 3. taraf kod `vendor/`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: adres `config/routes.php`'de, kod `include/`'ta, test `tests/`'te, kurulum `vendor/`'ta durur. Böylece bir yeri değiştirince sadece o değişir, diğerleri bozulur. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | JSON-only; HTML/SPA router kullanılmaz | İstemci tarafında sözleşme kırılır |
| 2 | Exception mesajı istemciye dönmez (`SERVER_INTERNAL_ERROR`) | Bilgi ifşası |
| 3 | Middleware sırası sabittir (ADR-020 §1.1-B.1) — değişiklik yalnız ADR ile | Güvenlik kontrolü atlanabilir |
| 4 | Cevap başlıkları: `X-Content-Type-Options: nosniff` + katı CSP (`default-src 'none'`) | JSON yüzeyi XSS'e açılır |
| 5 | `/v1/*` yalnız alias → `/api/v1/*` (tek kabul noktası) | Çift route kaynağı doğar |
| 6 | ORM yok; DB erişimi `shared` üzerinden PDO | Injection yüzeyi genişler |
| 7 | Secret yalnız `.env` içinde yaşar; bu dosyalara ve vault `.md`'ye yazılmaz | Anahtar sızıntısı |
| 8 | `vendor/` ve `composer.lock` elle değiştirilmez (yalnız Composer) | Sürüm kayması, kırık kurulum |
| 9 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 10 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.5 tablosunda işaretlenir | SSOT ihlali |
| 11 | Yeni `.md` şablondan üretilir (Guardrail #16); subagent `git commit` ATMAZ | Şablon ihlali, yetkisiz commit |

> **eli10 (basit):** Bu kurallar, API'nin hep aynı güvenli JSON dilinde konuşmasını ve kapıların izinsiz değişmemesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü sözleşmenin kendisi koddan daha uzun yaşar; `index.php` içine gömülse her revizyonda aranır. Okunmaları, yeni uç eklerken sözleşme dışı davranışı engeller. Yazılmaları denetimi tekrarlanabilir kılar. Kural değişince (ADR) yalnız bu tablo ve ilgili kod birlikte güncellenir.

---

## 5. Workflow

```text
ÖLÇ (envanter — bu oturumda) → YAZ (yalnız CONTEXT.md, şablon iskeleti)
  → ÇELİŞKİ KAYDI (vault ↔ disk → disk kazanır) → DOĞRULAMA (§6 tablosu)
  → İLGİLİ INDEX'E 1 SATIR → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural + hedef dosyaları oku (`CLAUDE.md`, `AGENTS.md`, `index.php`, `composer.json`) | Kural + kanıt | Sözleşme ve yasaklar baştan belli olsun | Sözleşme ihlali (ham hata/HTML sızıntısı) |
| 2 | Envanter ölç: `directory_tree` + `**/*.php` glob + satır sayımı | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Şablondan yaz (§1–§7, 5 sütun, eli10/eli15) | Bu dosya | Tutarlı iskelet (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 4 | Çelişki tablosunu güncelle (§3.5) | 5 karar satırı | Eski bilgi yeni oturumu yanıltır | "191 satır" efsanesi sürer |
| 5 | §6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | İlgili index'e 1 satır + rapor; **commit ATMA** | Kayıt + rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması kapıları önceden bilmeyi sağlar. Yazılması tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve doğrulama) hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişken yer tutucusu (çift süslü parantez deseni) kalmadı |
| 4 | Envanter | 13 kök dosya, 4 alt dizin, 12 proje PHP, 2856/358 yineleme — bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı `directory_tree`/glob/okuma ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 7 | eli10 + eli15 | §1, §2, §3.1 (11 satır), §3.2–§3.6, §4, §5 maddelerinde çift blok var (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4 #10 "disk kazanır" var |
| 9 | Boot sırası | Kök ↔ klasör ↔ vault ilişkisi §3.4'te var |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; katman mimarisi tablosu/eklentisi yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 300–500 satır bandı (frontend şablon §4.2) |

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
| Auth context | [[../auth.coremusic.net/CONTEXT.md]] | Auth SSOT envanteri (autoload hedefi) |
| Tüketici context | [[../home.coremusic.net/CONTEXT.md]] | Uçları tüketen taraf |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | §5 Domain Boundaries, §6 routing |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| ADR indeksi | [[../.ai/.decisions/index.md]] | ADR-020 / ADR-084 kararları (metin dokunulmaz) |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| PHP şablonu (doğru yol) | [[../.ai/.templates/backend/php-template.md]] | Yeni PHP dosyası kaynağı (§3.5 #5) |
| Giriş kodu kanıtı | [[index.php]] | Akış + middleware sırası |
| Autoload köprüsü kanıtı | [[autoload.php]] | Vendor + auth kayıt zinciri |
| Paket tanımı kanıtı | [[composer.json]] | Bağımlılıklar + PSR-4 |
| Route kanıtı | [[config/routes.php]] | Faz 1b uç sözleşmesi |
| Test kanıtı | [[phpunit.xml]] | Bootstrap + suite + kapsam |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12
