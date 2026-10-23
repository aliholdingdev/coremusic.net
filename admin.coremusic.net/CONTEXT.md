---
title: "CoreMusic — admin.coremusic.net Context"
type: docs
category: admin
docType: context
date: 2026-10-23
updated: 2026-10-23
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# CoreMusic — admin.coremusic.net Context

**docType:** context · **Klasör:** `admin.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[PRD.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`admin.coremusic.net/`, CoreMusic'in **server-rendered yönetim panelidir**: `PageRouterKernel` üzerinden SPA route tablosuyla çalışır, sayfaları `pages/` altındaki düz PHP dosyaları üretir ve kimlik doğrulamayı `auth.coremusic.net`'e devreder (ADR-043). Bu doküman klasörün disk envanterini (28 non-vendor dosya — ölçüm 2026-10-23), auth callback akışını ve komşu klasörlerle konuşmasını kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

| Karar | Kaynak (disk) |
|-------|---------------|
| Vanilla PHP >= 8.4, framework yok | `composer.json:16` (`php >=8.4`) + her dosyada `declare(strict_types=1)` |
| shared PageRouterKernel deseni | `config/bootstrap.php:15-20` (PageRouterKernel, ConfigManager, DomainConfig, LoggerFactory, SessionBootstrapper) |
| Auth SSOT: formlar auth subdomain'de (ADR-043) | `AuthAliasRedirector.php:29-34` (/login, /register, /admin-login, /admin-register → 302 auth) |
| Session köprüsü: auth_key → cURL validate-key | `include/Auth/AdminAuthBridge.php:146-171` (retry 2, timeout 5 sn, SSL verify açık) |
| Route tablosu admin-local, requiresAuth:true | `config/routes.php:20-60` ("shared/config/routes.php DEĞİŞTİRİLMEZ" yorumu) |
| 28 non-vendor dosya (21 .php) | 2026-10-23 recursive sayım (vendor hariç) |

> **eli10 (basit):** Bu klasör sitenin yönetici odasıdır: giriş auth'dan gelir, sayfalar burada PHP ile dizilir, veri istekleri `shared/` üzerinden yürür.
> **eli15 (detay):** Envanter ayrı yazıldı çünkü panel ile auth farklı hızda değişir; envanter kod içine gömülse revizyonda kaybolur. Okunması, hangi dosyanın ne işe yaradığını ve her sayının hangi ölçümden geldiğini gösterir. Yazılması envanter ile disk gerçeğinin hizasını kanıtlar; ölçülmeyen sayısal iddia bu dosyaya girmez.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `admin.coremusic.net/` kök + `config/`, `include/`, `pages/`, `tests/` envanteri | `../shared/src/` ortak katman (bağımlı olunan — ayrı context) |
| Auth callback akışı (alias → auth → callback → bridge → session) | `../auth.coremusic.net/` auth SSOT (oradan OKUNUR, kopyalanmaz) |
| admin ↔ auth/shared/assets komşuluk haritası | Frontend CSS/JS kodu (`../assets.coremusic.net/` — orada üretilir) |
| Vault ↔ disk çelişki kaydı | ADR metinleri (dokunulmaz — yalnız wiki-link verilir) |

- **Kullananlar:** Backend Architect (birincil), Security Engineer (bridge/CSRF/session), QA Engineer (`tests/`), DevOps (`web.config`/`.htaccess`), MO (bu doküman).
- **Ön koşul:** `index.php`, `config/bootstrap.php`, `include/` dosyaları okunmuş; envanter diskten ölçülmüş; kanıt yoksa sayı `UNKNOWN` yazılır.
- **Dokunulmaz:** `CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md`, `PRD.md`, `config/`, `include/`, `pages/`, `tests/`, `vendor/` bu görevde değiştirilmez — yalnız bu dosya yazılır.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, auth akışı ve komşularla ilişkisi anlatılır; auth kodunun ve frontend'in tarifi konu dışıdır.
> **eli15 (detay):** Kapsam ayrı çizildi ki bu doküman auth ya da shared katmanının ikinci kopyasına dönmesin. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Envanter (disk ölçümü 2026-10-23: 28 non-vendor dosya — vendor hariç)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `index.php` | Giriş: autoload → constants → RuntimeBootstrap → bootstrap | `index.php:11-24`; DEBUG ise `opcache_reset()` | Tek kapı, boot sırası sabit | Boot sırası değişince (ADR + onay) |
| `autoload.php` | SPL autoload köprüsü: kendi vendor + shared vendor fallback + `CoreMusic\Auth\` | `autoload.php:10-11, 46-48` (hiçbiri yoksa 503) | Bağımlılıklar tek yerden yüklensin | Composer/autoload düzeni değişince |
| `config/bootstrap.php` (7.673 B) | HTTPS algılama → DomainConfig → özel rotalar (`/health`, `/dev-login`, auth alias, root redirect, `/auth/callback`) → `PageRouterKernel->handle()` | `bootstrap.php:74-188` | Kernel öncesi kapılar tek yerde | Yeni kernel-öncesi rota eklenince |
| `config/routes.php` (2.070 B) | Admin-local SPA route tablosu: `auth/callback` (requiresAuth:false) + 6 admin sayfası (true) | `routes.php:20-60` | Adres tek kaynakta | Yeni sayfa eklenince |
| `config/constants.php` (3.941 B) | Session/CSRF/rate-limit sabitleri, subdomain URL'leri, `DB_ADMIN_NAME=coremusic_auth` | `constants.php:52-64, 81-101` | Ortam davranışı tek yerde | Yeni sabit eklenince |
| `config/.env.example` | Ortam değişkeni örneği (anahtar içermez) | `APP_PEPPER=CHANGE_ME…` | Kurulum tekrarlanabilsin | Yeni değişken eklenince |
| `include/Auth/AdminAuthBridge.php` (204 satır) | `auth_key` doğrulama (cURL POST `/validate-key`) + session kurma + `session_regenerate_id(true)` | `AdminAuthBridge.php:26-28, 83, 146-171` | Auth doğrulaması tek sınıfta | Auth akışı değişince (ADR + Security onayı) |
| `include/Routing/AdminRootRedirector.php` (38) · `AuthAliasRedirector.php` (60) | `/` → dashboard ya da auth login; login/register aliasları → auth domain | `AdminRootRedirector.php:31-36`, `AuthAliasRedirector.php:29-34` | I/O-sız, test edilebilir yönlendirme | Alias/route değişince |
| `pages/layout.php` (7.842 B) + 5 `admin-*.php` | Sunucu taraflı sayfa üretimi; her sayfa `$content` üretir → `require layout.php`; escaping `$h` (ENT_QUOTES\|ENT_SUBSTITUTE, UTF-8) | `layout.php:27`, `admin-songs.php:181` | Sayfa içerikleri tek şablonda | Yeni sayfa/panel eklenince |
| `tests/bootstrap.php` + `Unit/` (6 test) | AdminAuthBridge, LoginAliasRedirect, MarkupContract (14.178 B), RootRedirect, RouteResolution, ViewMode | `tests/Unit/*` | Sözleşme test ile sabitlenir | Kod değişince (QA) |
| `phpunit.xml` | Bootstrap + kapsam yalnız `include/`, `failOnRisky/Warning=true` | `phpunit.xml` | Test tek komutla koşsun | Kapsam/ayar değişince |
| `web.config` (IIS) + `.htaccess` (Apache) | Rewrite + erişim kapatma (`config|include|pages`, non-index `.php`, `.env/.md/.log/...` → 403) + güvenlik başlıkları | `web.config:14-45`, `.htaccess:18-36` | Gizli dosya açılmasın, istek `index.php`'ye düşsün | Sunucu kuralı değişince (DevOps) |
| `composer.json` + `composer.lock` | `coremusic/shared-infrastructure ^2.0` path-repo (junction → `../shared`); php-di, fast-route, nyholm/psr7, phpdotenv | `composer.json:7-25`; junction kanıtı LinkType=Junction | Her makine aynı sürümü kursun | Bağımlılık değişince |
| `vendor/` (junction) | Kurulu 3. taraf paketler | Composer üretimi | Çalışma zamanı hazır | Yalnız `composer install` |
| Bu doküman + 4 kardeş | Klasör kural/rol/süreç/envanter/gereksinim | 2026-10-23 üretimi | Bilgi beş farklı hızda değişir | Yalnız vault-sync (MO) |

**Auth Callback Akışı (kanıt zinciri):**

1. `/login` · `/register` · `/admin-login` · `/admin-register` → 302 `auth.coremusic.net/admin/*` (query korunur) — `AuthAliasRedirector.php:29-34`
2. `/` kök → session varsa `/admin-dashboard`, yoksa auth login — `AdminRootRedirector.php:31-36` + `bootstrap.php:129-142`
3. `/auth/callback?auth_key=…` → `AdminAuthBridge::validateAndCreateSession()` → cURL POST `auth.coremusic.net/validate-key` (retry 2, timeout 5 sn) → session → 302 `/admin-dashboard` — `bootstrap.php:145-182`
4. Logout doğrudan auth domain: `AUTH_URL/admin/logout` — `pages/layout.php:133`

> **eli10 (basit):** Giriş kartı auth'da basılır, burada kapıdan geçer; sayfalar PHP ile dizilir, testler sözleşmeyi kilitler.
> **eli15 (detay):** Envanter ayrı tablodur çünkü dosya ile sorumluluk farklı hızda değişir. Okunması, hangi dosyanın ne için ve ne zaman düzenleneceğini gösterir. Tablo olmazsa yeni panel işi yanlış dosyadan başlar ve auth sözleşmesi sessizce kırılır.

### 3.2 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | Bakım kolaylığı | Diff küçük | Her şey değişir, inceleme kör olur |
| 2 | Tek sorumluluk | Her dosyanın tek sahibi | "Bunu kim ekledi" cevapsız kalır |
| 3 | Auth/form ayrımı (ADR-043) | Form auth'da, panel burada | İki gerçek auth akışı doğar |
| 4 | Vendor karantinası | `vendor/` junction ayrı | Güncelleme kodu bozar |
| 5 | Test/kod ayrımı | Test yalnız `include/`'ı kapsar | Test kodu taklit eder |

---

## 4. Komşu İlişkiler

| Komşu | Yön | Ne taşır |
|-------|-----|----------|
| `../shared/` | admin → shared | PageRouterKernel, Config, Session, Log (junction — PRD-PLT-005) |
| `../auth.coremusic.net/` | admin → auth | validate-key çağrısı, login/register formları (ADR-043), test referansları (`AdminMarkupContractTest.php:70-94`) |
| `../assets.coremusic.net/` | admin → assets | `p-admin-layout.css`, `js/admin/admin-shell.js` (test kanıtı), Image varlıkları (`layout.php:76,126-134`) |
| `../home.coremusic.net/` | sabit | `MUSIC_URL` (`constants.php:81-86`) |
| `../.ai/` | vault | ADR-043/058, registry, bu dokümanın otoritesi |

> **eli10 (basit):** Bu klasör tek başına yaşamaz: motor `shared`'te, kimlik `auth`'da, görünüm `assets`'tadır.
> **eli15 (detay):** Komşuluk tablosu ayrı çünkü bağımlılık yönü katman kuralıdır (tek yön). Okunması, bir değişikliğin hangi klasöre el uzatabileceğini gösterir. Tablo olmadan admin, auth kodunu kopyalayarak "çözüm" üretir ve iki gerçek doğar.

---

## 5. Bilinen Çelişkiler / Açık Maddeler

| # | Madde | Durum | Davranış |
|---|-------|-------|----------|
| 1 | `RATE_LIMIT_MAX/WINDOW` sabitleri var ama admin-local uygulama kanıtı görünmüyor (`constants.php:54-55`) | ⚠️ VERIFICATION REQUIRED | Uygulama iddiası yazılmaz; denetim Security'de |
| 2 | `/dev-login` sabit admin session kurar — yalnız `DEBUG_MODE` koşulu altında (`bootstrap.php:91-105`) | Bilinçli tasarım | Production'da devre dışı olduğu Security doğrulamasıyla teyit edilir |
| 3 | Dashboard `coremusic_auth` şemasına doğrudan sorgu atar (`SELECT COUNT(*) FROM admin_users` — `admin-dashboard.php:36`) | Kayıtlı | Yeni sorgu ekleme Data/Security handover ister |
| 4 | `.phpunit.result.cache` çalışma ağacında (gitignore adayı) | DevOps notu | `.gitignore` kararı DevOps + kullanıcı onayı |

> **eli10 (basit):** Burada "emin olmayan" şeyler açıkça işaretlidir; tahmin edilmez.
> **eli15 (detay):** Çelişki kaydı ayrı yazıldı ki eksik bilgi yeni oturumlarda "bilinen gerçek" sanılmasın (Zero-Hallucination — kök `AGENTS.md` §3). Kayıt kapanınca madde silinmez, durumu ile güncellenir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | eli10/eli15 | §1, §3, §4 bloklarında etiketli; eli10 ≤2 cümle |
| 5 | Disk kanıtı | 28 dosya, satır sayıları, junction iddiaları 2026-10-23 ölçümüyle uyumlu |
| 6 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 7 | Emoji | Dekoratif emoji yok (yalnız `⚠️ VERIFICATION REQUIRED` işareti) |
| 8 | Dokunulmaz | Kod + kardeş dokümanlar değiştirilmedi |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Boot kuralı |
| Klasör rolleri | [[AGENTS.md]] | Routing + yetki |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Klasör gereksinimleri | [[PRD.md]] | PRD-ADM-001..007 |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination, §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Routing, handover |
| Auth komşusu | [[../auth.coremusic.net/AGENTS.md]] | ADR-043 sahipliği |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-23
