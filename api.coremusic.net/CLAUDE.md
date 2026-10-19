---
title: "CoreMusic — api.coremusic.net Klasör Kuralları"
type: docs
category: api
docType: claude
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# api.coremusic.net — CLAUDE.md

**docType:** claude · **Klasör:** `api.coremusic.net/` · **Sorumlu:** Security + Backend

**Zorunlu Bağlantılar:** [[CONTEXT.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu dosya `api.coremusic.net/` klasöründe çalışırken uyulacak kuralları ve **her kuralın neden var olduğunu** tanımlar. API JSON-only yüzey olduğundan sözleşme (hata biçimi, başlık, sıra) güvenlik demektir; kural ihlali istemci tarafında güvenlik açığına dönüşür.

| Karar | Kaynak (disk) |
|-------|---------------|
| JSON-only + `{error:{code,message}}` | `api.coremusic.net/index.php` başlık yorumu |
| Middleware sabit sıra (ADR-020 §1.1-B.1) | `index.php` pipeline kurulumu |
| Katı CSP + nosniff | `index.php` `$jsonResponse` |
| auth SSOT: `auth.coremusic.net/include/` | `composer.json` autoload + `index.php` route action closure |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `index.php`, `include/`, `config/`, `tests/` değişiklik kuralları | `shared/src/` kendi kuralları → [[../shared/CLAUDE.md]] (MEVCUT) |
| JSON sözleşme + middleware sırası | `auth.coremusic.net/include/` içeriği (SSOT orası) |
| Bu klasörde yazma/yasak listesi | CI/CD, deploy (DevOps) |

- **Kullananlar:** Backend Architect, Security Engineer, QA Engineer.
- **Ön koşul:** Klasör `CONTEXT.md`'si okunmuş; her kuralın diske dayanan kaynağı tabloda.

---

## 3. Mimari

### 3.1 Yüzey Envanteri (kuralların koruduğu dosyalar — disk ölçümü)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `index.php` | İstek kabul + JSON yanıt | alias, health, gateway, middleware, 404/500 | Sözleşmenin tek uygulayıcısı | ADR'li sıra/sözleşme değişince | Kapının tek bekçisi; her istek buradan geçer. | Tek dosyadır çünkü hata biçimi ve başlıklar tek yerde garanti edilsin. Okunması, sıralamayı ve cevap şemasını gösterir. Yazılması, istemcinin güvenle okuyacağı JSON üretir. Yalnız ADR ile değişir; rutin özellik eklemesi buraya değil `include/`'a gider. |
| `include/Controller/AuthController.php` | API auth uçlarının işleyicisi | sınıf kodu (auth sınıfına bağlanır) | API kapısı auth SSOT'ını kullansın | Uç davranışı değişince | API'nin auth işlerini yürüten tek masa. | Ayrıldı çünkü iş kuralı auth'de (SSOT); api yalnız kapı görevini yapar. Okunması, hangi route action'ının nereye gittiğini gösterir. Yazılması, çağrının doğru sınıfa gittiğini garanti eder. Uç davranışı değişince düzenlenir. |
| `include/Container/ApiAuthContainer.php` | Bağımlılık hazırlığı (DI) | konteyner sınıfı | Controller doğru bağımlılıklarla kurulsun | Bağımlılık değişince | Masayı hazırlayan usta: doğru aletleri getirir. | Ayrıldı çünkü kurulum ( wiring ) ile iş mantığı farklıdır; tek gövdede test zorlaşır. Okunması, bağımlılık zincirini gösterir. Bağımlılık eklenince düzenlenir. |
| `config/routes.php` | Uç listesi | route dizisi (`RouteTable::fromArray`) | Adres tek kaynakta | Yeni uç eklenince | Kapıların adres defteri. | Ayrı çünkü adres koddan daha sık değişir; kod içine gömülse her adres kod revizyonu ister. `index.php` bunu okur. Yeni uç eklenince düzenlenir; çifte kaynak (alias hariç) oluşturulmaz. |
| `config/cors.php` | CORS kuralları | izinli origin/metod ayarları | Cross-domain kapısı denetimli | Origin değişince | Hangi sitenin içeri görebileceğinin listesi. | Ayrı çünkü güvenlik ayarı kod içine gömülse revisyonda unutulur. Kural değişince (yeni origin) düzenlenir; asla `*` ile genişletilmez (kanıt: dosya var, içerik güvenlik ayarıdır — değişiklik Security onayı ister). |
| `config/constants.php`, `config/app.php` | Sabit + uygulama ayarı | sabitler, dizi ayarlar | Ortam davranışı tek yerde | Ayar değişince | Anahtarlıklar ve ayar defteri. | Ayrı çünkü ayar koddan farklı hızda değişir. Yeni sabit eklenince düzenlenir; secret içerik vault'a yazılmaz. |
| `tests/Unit/*` | Smoke, RouteConfig, AuthController testleri | 3 test + `bootstrap.php` | Sözleşme test edilir | Kod değişince | Kapının soru kâğıtları. | Ayrı ki kod değişince test değişmesin, yalnız doğrulasın. Yeni uç eklenince test de eklenir. |

### 3.2 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Dosyanın tek sahibi | Kimin ne işi belli değil |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık + tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı ayrı | Yayılır, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme kodu bozar |

> **eli10 (basit):** Kutular ayrı ki birini değiştirirken diğeri bozulmasın.
> **eli15 (detay):** Route, kod, ayar ve test ayrı dosyalardır; çünkü adres en sık değişir, kod daha seyrek, test en son. Ayrı olunca adres eklerken test ve kod değişmez, inceleme küçük kalır. Okunması, kuralın hangi yüzeyi koruduğunu gösterir. Tek dosyada toplansaydı güvenlik ayarı kod revizyonu arasında kaybolabilirdi.

---

## 4. Kurallar (her biri: neden var → eli10/eli15)

| # | Kural | Korunan yüzey | Neden var (gerekçe) | Kaynak (disk) |
|---|-------|---------------|---------------------|---------------|
| 1 | **JSON-only**: HTML/SPA router kullanılmaz | İstemci sözleşmesi | İstemci JSON bekler; HTML gövdesi parse hatası + XSS yüzeyi | `index.php` başlık yorumu |
| 2 | **Hata sözleşmesi**: `{error:{code,message}}`; exception mesajı ASLA dönmez | Sızıntı yüzeyi | Stack/db bilgisi istemciye (saldırgana) sızmasın | `index.php` başlık yorumu + `catch` → `SERVER_INTERNAL_ERROR` |
| 3 | **Middleware sırası sabit**: ResponseNormalization → **OriginCheck** → Cors → RateLimit → Authentication → **[Csrf·yalnız session-auth]** → RequestValidation → Authorization (ADR-020 §1.1-B.1 + **ADR-094 genişletme 2026-10-07**) | Güvenlik kapıları | Sıra değişirse doğrulama yetkisiz isteğe geçebilir | `index.php` pipeline + [[../.ai/.decisions/accepted/ADR-094-api-pipeline-origin-csrf]] |
| 4 | **Başlıklar**: `X-Content-Type-Options: nosniff` + katı CSP `default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'` | JSON yüzeyi | JSON'ın script/iframe olarak yutulmasını kapatır | `index.php` `$jsonResponse` |
| 5 | **Alias tek kabul noktası**: `/v1/*` → `/api/v1/*` (ekleme değil, yeniden yazma) | Route kaynak tekliği | Çift route = çift davranış, bakımı imkânsız | `index.php` "0. Alias" |
| 6 | **Auth uçlarında oturum**: `/api/v1/auth/*` → `SessionBootstrapper::ensureStarted()` (auth ile aynı cookie save path) | Oturum bütünlüğü | Farklı path'te cookie bölünür, kullanıcı sürekli logout | `index.php` auth regex bloğu |
| 7 | **auth kodunu kopyalama**: `CoreMusic\Auth\` autoload ile `../auth.coremusic.net/include/`'den gelir | SSOT | Kopya kod = 2 gerçek, güvenlik yaması tek tarafta kalır | `composer.json` autoload |
| 8 | **ORM yok, `shared` üzerinden PDO** | Injection yüzeyi | Hazır statement dışında SQL yazılmaz (ADR-002) | [[../shared/AGENTS.md]] §4 |
| 9 | **Sabit sıra dışına middleware koyma / eklemek ADR ile** | Kapı bütünlüğü | Kayıtsız kapı çalışmaz, kontrol atlanır | ADR-020 §1.1-B.1 (index.php yorumu) |
| 10 | **Yeni PHP dosyası `php-template.md`'den türetilir** | Şablon disiplini (Guardrail #16) | Şablonsuz dosya vault standardına uymaz | Kök `AGENTS.md` §10 + `shared/AGENTS.md` §4 |

> **eli10 (basit):** Bu kurallar API'nin hep aynı güvenli JSON dilinde konuşmasını ve kapıların sırasının bozulmamasını sağlar.
> **eli15 (detay):** Kurallar ayrı dosyada durur çünkü kural ADR ile gelir, kodla farklı anda değişir; kod içine gömülse sonraki revizyonda aranması gerekir. Okunmaları, bu klasöre giren ilk kişinin yasakları görmasını sağlar. Yazmaları, denetimi ve geri alınabilirliği mümkün kılar. Kural değişince (yeni ADR) hem kod hem bu tablo birlikte güncellenir — sessiz tek taraflı değişiklik yasaktır.

### 4.1 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Yeni uçta hata için `echo $e->getMessage()` | `{error:{code,message}}` + log'a ayrıntı (`index.php` catch) |
| 2 | Middleware'i dizi sonuna eklemek | ADR-020 sırasına göre konum + ADR |
| 3 | AuthController'ı api'ye kopyalamak | `CoreMusic\Auth\` autoload (SSOT auth) |
| 4 | `/v1` için ayrı route yazmak | Alias zaten `/api/v1`'e çevirir |
| 5 | HTML cevap döndürmek | JSON-only (`$jsonResponse`) |

---

## 5. Workflow

```text
KURAL OKU (§4) → CONTEXT + index.php 1. kez oku → KARAR
  → include/ veya config/ değişikliği → tests/Unit test → composer test → RAPOR (commit yok)
```

| # | Adım | Neden | Atlarsan ne olur |
|---|------|-------|------------------|
| 1 | §4 kuralları oku | Sözleşme/sıra/sızıntı yasakları | Güvenlik sözleşmesi ihlali |
| 2 | 1. okumadan karar | Anti-overthink | Gereksiz tekrar |
| 3 | Değişiklik doğru dizine | Sorumluluk sınırı | Dağınık kod |
| 4 | Test + `composer test` | Kapı | Sessiz kırılma |
| 5 | Commit ATMAZ | Yetki | Düzensiz tarih |

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: claude` |
| 2 | Bölüm sırası | §1–§7 |
| 3 | Kural sayısı | §4 = 10 satır, her satırda "Neden var" dolu |
| 4 | eli10 + eli15 | §4 bloğu mevcut (≤2 cümle / 3-4 cümle) |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | `[[...]]`, hedefler var |
| 7 | Disk kanıtı | Her kuralın index.php/composer.json kaynağı var |
| 8 | Dokunulmaz | Kaynak kod + mevcut doküman değişmedi |
| 9 | Emoji | Yalnız `[[...]]` |
| 10 | Halüsinasyon | Doğrulanmayan iddia `VERIFICATION REQUIRED` |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör context | [[CONTEXT.md]] | Envanter + akış |
| Klasör rolleri | [[AGENTS.md]] | Kim neye dokunur |
| Klasör süreç | [[WORKFLOW.md]] | Adım/kapı |
| Kök kurallar | [[../AGENTS.md]] | Master guardrail |
| shared kuralları | [[../shared/CLAUDE.md]] | Ortak katman kuralları (MEVCUT) |
| ADR-020 kaydı | `../.ai/.decisions/` ⚠️ VERIFICATION REQUIRED (dosya adı doğrulanmadı) | Middleware sıra kararı |
| PHP şablonu | `../.ai/.templates/backend/php-template.md` ⚠️ VARLIĞI DOĞRULANMADI | Yeni dosya iskeleti |
| Giriş kodu | `api.coremusic.net/index.php` | Kural kanıtları |
| Template kaynağı | `../.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
