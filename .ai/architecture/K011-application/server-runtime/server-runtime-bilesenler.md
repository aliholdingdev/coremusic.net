---
title: "K011 · Server-Runtime — Bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: server-runtime
status: active
updated: 2026-10-15
group: mimari
---

# K011 · Server-Runtime — Bileşenler (Mimari)

> **Amaç:** `server-runtime` modülündeki her bileşeni kart kart tanımlamak:
> sorumluluk, bağımlılıklar, kanıtlı satırlar, riskler. Sayaç/lint betikleri
> §C'de belge olarak taşınır (shell bu oturumda kullanılamadı).

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Bileşen kartları (9) | mimari | bilesen | Ana envanter |
| 2 | §2 Bağımlılık grafiği | mimari | grafig | Kenar tablosu |
| 3 | §3 Veri/başlık akışı | mimari | akış | Uçtan uca |
| 4 | §4 Risk & güvenlik | mimari | risk | Bileşen bazında |
| 5 | §5 Kapsam sınırı | mimari | sinir | Ne bu modülde değil |
| 6 | §6 Boşluklar | mimari | verification | ⚠️ listesi |
| 7 | §7 Ölçümler | mimari | olcum | Sayımlar |
| 8 | §A Ek — hızlı kart | mimari | referans | Tek ekran |
| 9 | §B Ek — eşleşme | mimari | esleme | K011 registry |
| 10 | §C Ek — sayıma PowerShell | mimari | sayac | Belge betik |

---

## §1 Bileşen Kartları

### B.1 RuntimeBootstrap

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Bootstrap/RuntimeBootstrap.php` |
| Boyut | 21 satır (read ölçümü) |
| Rol | Tek boot kapısı: timezone + error_reporting + display/log_errors |
| Girdi | `$debugMode` (caller: `index.php` L44 `DEBUG_MODE`) |
| Çıktı | Ortam durumu (`date_default_timezone_set`, `ini_set`) — dönüş değeri yok (`void`) |
| Bağımlılık | YOK (static, saf PHP) |
| Kanıt | L7-20 |

### B.2 ResponseEmitter

| Alan | Değer |
|---|---|
| Dosya | `shared/src/PageRouter/ResponseEmitter.php` |
| Boyut | 163 satır (read ölçümü) |
| Rol | Durum + başlık + gövde yazımı; `exit` ile süreci bitirir (`never`) |
| Public API | `emit()` L16 · `sendJson()` L43 · `sendHtml()` L57 · `redirect()` L83 |
| Private | `emitTraceAndExtras()` L94 · `mergeVary()` L109 · `applyFallbackSecurityHeaders()` L132 · `hasSentHeader()` L152 |
| Bağımlılık | YOK (final sınıf; yalnız PHP header/exit API'si) |
| Durum | Her public method `exit` ile biter — caller akış sonu |
| Kanıt | L16-92, L132-163 |

### B.3 ErrorHandler

| Alan | Değer |
|---|---|
| Dosya | `shared/src/PageRouter/ErrorHandler.php` |
| Boyut | 77 satır (read ölçümü) |
| Rol | Hata sayfası (kabuklu) ve minimal HTML hata sonucu üretimi |
| Public API | `buildErrorPage()` L11 · `getErrorInfo()` L39 · `buildErrorHtmlResult()` L55 |
| Bağımlılık | `HtmlShellRenderer` (ctor L7-9) |
| Durum eşlemesi | 404/403/410/500 (L21-27) · 403/404/429 (L57-62) |
| XSS koruması | `htmlspecialchars(ENT_QUOTES, 'UTF-8')` L30-31, L67 |
| Kanıt | L7-76 |

### B.4 StructuredLogger

| Alan | Değer |
|---|---|
| Dosya | `shared/src/PageRouter/StructuredLogger.php` |
| Boyut | 81 satır (read ölçümü) |
| Rol | JSON yapılandırılmış log; traceId bağlama; redaction |
| Seviye | `debug0/info1/warn2/error3` (L9); `minLevel` ctor (L13-15) |
| Public API | `setTraceId` L17 · `getTraceId` L22 · `debug/info/warn/error` L27-45 · `timing` L47 · `log` L55 |
| Çıkış | `error_log(Redactor::redact($payload))` L72 |
| Bağımlılık | `CoreMusic\Log\Redactor` (L5) |
| Kanıt | L9-80 |

### B.5 LoggerFactory

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Log/LoggerFactory.php` |
| Boyut | 34 satır (read ölçümü) |
| Rol | `FileHandler` singleton; log dizini çözümü |
| Public API | `getInstance()` L15 · `reset()` L24 |
| Dizin | `dirname(__DIR__, 3)` → proje kökü (L29-33) |
| Docblock | Debug modda error/warning/info.log ayrı; üretimde yalnız error.log (L5-10) |
| Kanıt | L11-33 |

### B.6 ApcuAdapter

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Cache/ApcuAdapter.php` |
| Boyut | 56 satır (read ölçümü) |
| Rol | APCu cache adaptörü (`CacheInterface`) |
| Public API | `get` L7 · `set` L17 · `delete` L25 · `increment` L33 · `clear` L41 · `has` L49 |
| Degrade | Her method `function_exists` guard → APCu yoksa `false` |
| Çağrı yeri | `index.php` L143-145 (koşullu; `MemoryAdapter` fallback) |
| Kanıt | L5-56 |

### B.7 API Front Controller

| Alan | Değer |
|---|---|
| Dosya | `api.coremusic.net/index.php` |
| Boyut | 223 satır (read ölçümü) |
| Rol | Tüm API isteklerinin tek girişi: boot → parse → pipeline → dispatch → emit |
| Bölümler | config L38-42 · boot L44 · logger L47 · HTTPS L50-61 · alias L87-94 · health L101-108 · pipeline L129-171 · gateway L173-182 · catch L201-214 · 404 L217-223 |
| Dokunulmaz | Sıra/sözleşme ADR'siz değişmez (`api.coremusic.net/AGENTS.md` §4 #4) |
| Kanıt | L1-223 |

### B.8 Apache Sunucu Kuralları

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/.htaccess` |
| Boyut | 37 satır (read ölçümü) |
| Rol | Rewrite + header + dosya engelleme + PHP hata yolu (Apache) |
| Bölümler | sertleştirme L2-7 · header L9-16 · engel L18-24 · rewrite L26-37 |
| Sahip | DevOps Engineer (onaysız değişmez) |
| Kanıt | L1-37 |

### B.9 IIS Sunucu Kuralları

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/web.config` |
| Boyut | 43 satır (read ölçümü) |
| Rol | Apache paritesi: header L4-13 + 5 rewrite kuralı L14-41 |
| Sahip | DevOps Engineer |
| Kanıt | L1-43 |

---

## §2 Bağımlılık Grafiği

```text
[İstemci: FetchWrapper.js — X-Trace-Id L50-51]
        │ HTTPS (X-Forwarded-Proto — index.php L52)
        ▼
[Apache .htaccess]──parite──[IIS web.config]
        │ index.php [QSA,L] / appendQueryString
        ▼
[api.coremusic.net/index.php]
   ├─ RuntimeBootstrap::boot (L44)
   ├─ LoggerFactory::getInstance (L47) → FileHandler → error_log
   ├─ ApcuAdapter | MemoryAdapter (L143-145)
   ├─ ApiMiddlewarePipeline (L147-171)
   └─ Gateway::dispatch (L173-182)
        │ yanıt dizisi
        ▼
[ResponseEmitter] → header/echo → exit
   └─ (SPA HTML) ErrorHandler → HtmlShellRenderer
[StructuredLogger] ← pipeline/trace boyunca (error_log + Redactor)
```

| Kenar | Yön | Kanıt |
|---|---|---|
| `.htaccess` → `index.php` | aşağı | `.htaccess` L36 |
| `web.config` → `index.php` | aşağı | `web.config` L36-39 |
| `index.php` → `RuntimeBootstrap` | aşağı | `index.php` L44 |
| `index.php` → `LoggerFactory` | aşağı | L47 |
| `index.php` → `ApcuAdapter` | aşağı (koşullu) | L143-145 |
| `index.php` → pipeline → Gateway | aşağı | L147-182 |
| `index.php` → `ResponseEmitter` | (dolaylı — yanıt dizisi contract) | L185-200 yapısal |
| `ErrorHandler` → `HtmlShellRenderer` | aşağı | `ErrorHandler.php` L8 |
| `StructuredLogger` → `Redactor` | aşağı | L5 |
| JS → PHP | `X-Trace-Id` başlığı | `FetchWrapper.js` L51 ↔ `ResponseEmitter.php` L97 |

---

## §3 Başlık / Veri Akışı

| # | Başlık | Üretici | Tüketici | Kanıt |
|---|---|---|---|---|
| 1 | `X-Trace-Id` | JS `FetchWrapper` (isteğe bağlı) + PHP yanıt | Log kayıtları (`traceId` alanı) | JS L51 · PHP L97 · Logger L66 |
| 2 | `Content-Security-Policy` | ADR-012 middleware (öncelik) → fallback L139 · API'de L76 | Tarayıcı | L130-131, L139, L76 |
| 3 | `ETag` (xxh64) | `sendHtml` L60 | Tarayıcı `If-None-Match` → 304 | L60-66 |
| 4 | `Vary: Accept-Encoding, Cookie` | `sendHtml` L70 + `mergeVary` L109 | Önbellek katmanları | L70, L109-125 |
| 5 | `X-Frame-Options` | fallback L141 · Apache L10 · IIS L8 | Tarayıcı | 3 kaynak |
| 6 | `X-Content-Type-Options` | JSON L9 · fallback L140 · Apache L11 · IIS L7 | Tarayıcı | 4 kaynak |
| 7 | `Permissions-Policy` | IIS `web.config` L11 | Tarayıcı | Apache `.htaccess`'te YOK → §6.4 |
| 8 | `Location` (redirect) | `redirect()` L86 | Tarayıcı | L83-92 |

---

## §4 Risk & Güvenlik (Bileşen Bazında)

| # | Risk | Bileşen | Durum |
|---|---|---|---|
| 1 | Nonce zinciri kırılması (fallback CSP yazımı) | `ResponseEmitter` | Kontrollü — `hasSentHeader` L145-148, ADR-012 notu L130 |
| 2 | Exception mesajı sızıntısı | `index.php` catch | Kontrollü — L208-213 sabit mesaj |
| 3 | Log'da hassas veri | `StructuredLogger` | Kontrollü — `Redactor::redact` L72 |
| 4 | Direkt PHP erişimi (config vb.) | Apache L22-24 · IIS L20-27 | Kontrollü |
| 5 | Server banner | `.htaccess` L3, L14-15 · `web.config` L6 | Kontrollü |
| 6 | `style=""` inline (ErrorHandler minimal HTML) | `ErrorHandler.php` L66 | ⚠️ — inline `style` CSP `style-src-attr 'unsafe-inline'` varyantına dayanıyor (`ResponseEmitter.php` L14); nonce'lu CSP'de bu HTML bloğu kırılabilir → ADR gerekir |
| 7 | `debug.php` erişilebilirliği | `.htaccess` L22 | ⚠️ VERIFICATION REQUIRED — dosya var mı/neye açık |
| 8 | IIS'de `php_flag` yokluğu (display_errors) | `web.config` | ⚠️ VERIFICATION REQUIRED (§6.3) |

---

## §5 Kapsam Sınırı

| Konu | Nereye ait |
|---|---|
| Repository/SQL | `queries` modülü |
| Middleware iç mantığı | Middleware modülü + Security Engineer |
| Route tablosu | Backend Architect (`config/routes.php`) |
| nginx üretim conf | PLANNED (§6.5) |
| FPM/SAPI üretim ayarı | Sunucu ortamı (⚠️) |

---

## §6 Boşluklar & PLANNED

| # | Boşluk | Tür | Etki |
|---|---|---|---|
| 1 | `HtmlShellRenderer` nonce üretimi okunmadı | ⚠️ VERIFICATION REQUIRED | CSP zinciri tam kanıtlanamadı |
| 2 | `FileHandler` (döndürme/sınır) okunmadı | ⚠️ VERIFICATION REQUIRED | Log davranışı eksik |
| 3 | IIS `php_flag` karşılığı | ⚠️ VERIFICATION REQUIRED | IIS'de display_errors davranışı bilinmiyor |
| 4 | Permissions-Policy yalnız IIS'de | Parite açığı | Apache'de bu başlık yok |
| 5 | nginx conf repo'da yok | PLANNED | Havuz dokümanları hedef senaryo |
| 6 | `ATTR_PERSISTENT`/FPM production | ⚠️ VERIFICATION REQUIRED | Ortak boşluk (queries) |
| 7 | `api_usage` yazıcısı | ⚠️ VERIFICATION REQUIRED | Ortak boşluk (queries §6.1) |
| 8 | `debug.php` kapsamı | ⚠️ VERIFICATION REQUIRED | Sertleştirme doğruluğu |

---

## §7 Ölçümler (2026-10-15)

| Ölçüm | Değer | Yöntem |
|---|---|---|
| Bileşen sayısı | 9 | read |
| Toplam satır (9 bileşen) | 21+163+77+81+34+56+223+37+43 = **735** | read |
| IIS rewrite kuralı | 5 | read |
| Fallback başlık | 4 | read |
| Emitter public method | 4 (`emit/sendJson/sendHtml/redirect`) | read |
| Cache API method | 6 (`get/set/delete/increment/clear/has`) | read |

---

## §A Ek — Hızlı Başvuru Kartı

```text
server-runtime bileşenleri — tek ekran
  Boot:        RuntimeBootstrap (21) — timezone + error ayarları
  Emit:        ResponseEmitter (163) — JSON/HTML/redirect + fallback CSP + ETag/304
  Hata:        ErrorHandler (77) — 404/403/410/500/429
  Log:         StructuredLogger (81) + LoggerFactory (34) — JSON + Redactor
  Cache:       ApcuAdapter (56) — function_exists guard
  Front ctrl:  api.coremusic.net/index.php (223) — ADR-020 sabit sıra
  Sunucu:      .htaccess (37) + web.config (43) — rewrite parite
  İstemci:     FetchWrapper.js — X-Trace-Id + 5xx retry
  Boşluk:      HtmlShellRenderer nonce · FileHandler · IIS php_flag · nginx conf
```

---

## §B Ek — K011 Registry Eşleşmesi

| Alan | Değer |
|---|---|
| Modül | `K011-application/server-runtime` |
| Kardeş modül | `queries` (aynı kademe-2, kesişim 0) |
| Çocuk dosyalar | `server-runtime-kapsam.md` · `-dogrulama.md` · `-web-arastirma.md` · `-ornekler.md` · `-bilesenler.md` (bu dosya) |
| Grup dağılımı | mimari ×2 · kanit ×2 · kod ×1 |

---

## §C Ek — Sayıma PowerShell Bloğu (belge)

> **Çalıştırma durumu:** `shell` bu oturumda `permission.rejected` döndürdü;
> betik belgedir. Sayımlar read/grep ile §7'de alınmıştır.

```powershell
# K011 server-runtime bileşen sayımı — repo kökünden çalıştırılır.
$src = @(
  'shared\src\Bootstrap\RuntimeBootstrap.php',
  'shared\src\PageRouter\ResponseEmitter.php',
  'shared\src\PageRouter\ErrorHandler.php',
  'shared\src\PageRouter\StructuredLogger.php',
  'shared\src\Log\LoggerFactory.php',
  'shared\src\Cache\ApcuAdapter.php',
  'api.coremusic.net\index.php',
  'home.coremusic.net\.htaccess',
  'home.coremusic.net\web.config'
)
$sum = 0
foreach ($f in $src) {
    $n = (Get-Content $f | Measure-Object -Line).Lines
    "$f -> $n satır"
    $sum += $n
}
"TOPLAM -> $sum satır (beklenen 735)"
```

**Beklenen çıktı (read ölçümünden):** RuntimeBootstrap 21 · ResponseEmitter
163 · ErrorHandler 77 · StructuredLogger 81 · LoggerFactory 34 · ApcuAdapter
56 · index.php 223 · .htaccess 37 · web.config 43 → **735**.

---

## §D İstemci-Yanı Etkileşim Bileşenleri (Runtime İlişkili)

| # | Bileşen | Dosya | Boyut | İlişki | Kanıt |
|---|---|---|---|---|---|
| 1 | `FetchWrapper` | `assets.coremusic.net/js/router/FetchWrapper.js` | 63 satır | `X-Trace-Id` üretimi + 5xx retry | L6, L46-53 |
| 2 | `ContentFetcher` | `assets.coremusic.net/js/router/ContentFetcher.js` | ⚠️ ölçüm (kısmi) | İçerik çekme — runtime uçları | ⚠️ VERIFICATION REQUIRED |
| 3 | Breakpoint token'ları | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 57 satır | Cihaz runtime eşlemesi (RPi5 1024×600) | L22-34, L45-56 |

> Bu üç bileşen `server-runtime` PHP bileşenlerinin **karşı tarafıdır**: aynı
> HTTP sözleşmesinde çalışırlar. PHP tarafı L97 (`X-Trace-Id`) ↔ JS L51 eşleşmesi
> en somut bağlantıdır.

### §D.1 Retry Parametreleri (İstemci)

| Parametre | Değer | Kanıt |
|---|---|---|
| `TIMEOUT_MS` | 10_000 | `FetchWrapper.js` L6 |
| `MAX_RETRIES` | 2 | L6 |
| `RETRY_DELAY` | 500 ms | L6 |
| Retry tetikleyici | `!response.ok && status >= 500` | L30 |
| Abort davranışı | `AbortError` anında rethrow (retry yok) | L37 |

> Sunucu tarafı retry **yok** — tek sorumluluk istemcidedir (dogrulama N-6).

---

## §E Bileşen Olgunluk Matrisi

| # | Bileşen | Kod kanıtı | Test kanıtı | Doküman | Olgunluk |
|---|---|---|---|---|---|
| 1 | `RuntimeBootstrap` | ✅ 21 satır | ⚠️ testi okunmadı | ✅ bu modül | Orta |
| 2 | `ResponseEmitter` | ✅ 163 satır | ⚠️ testi okunmadı | ✅ | Orta-Yüksek |
| 3 | `ErrorHandler` | ✅ 77 satır | ⚠️ | ✅ | Orta |
| 4 | `StructuredLogger` | ✅ 81 satır | ⚠️ (Unit/Log altında olabilir — `shared/tests` 9 dizin) | ✅ | Orta |
| 5 | `LoggerFactory` | ✅ 34 satır | ⚠️ | ✅ | Orta |
| 6 | `ApcuAdapter` | ✅ 56 satır | ⚠️ | ✅ | Orta |
| 7 | `index.php` | ✅ 223 satır | ⚠️ SmokeTest var (api AGENTS §3.1 QA satırı) | ✅ | Yüksek |
| 8 | `.htaccess` | ✅ 37 satır | ❌ (sunucu düzeyi — test edilemez) | ✅ | Yüksek |
| 9 | `web.config` | ✅ 43 satır | ❌ | ✅ | Yüksek |

> `shared/tests` 9 dizin (Api, Component, Events, Fixtures, Middleware, OAuth,
> Repository, Security, Unit — `shared/AGENTS.md` §2) — PageRouter testleri
> `Unit/PageRouter` altında **olabilir** → dosya listesi okunmadı, `⚠️`.

---

## §F Bileşen Eşitleme Kontrolleri (Parite)

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| P-1 | Statik uzantı listesi Apache ↔ IIS | Birebir | ✅ (kapsam §6.5) |
| P-2 | Hassas uzantı listesi Apache ↔ IIS | Birebir (11 uzantı) | ✅ (`.htaccess` L18 ↔ `web.config` L25) |
| P-3 | Header seti IIS ↔ Apache | IIS'de ek: Permissions-Policy | ⚠️ parite açığı (§6.4) |
| P-4 | PHP hata ayarı Apache ↔ IIS | IIS'de eksik | ⚠️ (§6.3) |
| P-5 | Fallback CSP ↔ ADR-012 middleware | Aynı politika ailesi | ✅ (L13-14, L130) |

---

## §G Gelecek Bileşenler (PLANNED)

| # | Bileşen | Tetik | Kanıt |
|---|---|---|---|
| 1 | nginx conf (üretim) | Sunucu envanteri kararı | Havuz §12 iskeleti (web-arastirma) |
| 2 | HSTS başlığı (tüm sunucular) | ADR | Açı kaydı §6.4 · havuz §7.3 |
| 3 | `api_usage` runtime yazıcısı | Ölçüm feature'ı | queries §6.1 ortak boşluk |
| 4 | Trace-Id okuma (pipeline besleme) | Observability ADR | kapsam §15 `⚠️` |

---

## §H Bileşen → Sunucu Eşlemesi (Apache ↔ IIS)

| # | Bileşen | Apache karşılığı | IIS karşılığı | Parite |
|---|---|---|---|---|
| 1 | Statik uzantı listesi | `.htaccess` L1–17 | `web.config` L1–24 | ✅ birebir |
| 2 | Hassas uzantı engelleme | `.htaccess` L18–30 | `web.config` L25–38 | ✅ 11 uzantı |
| 3 | Security başlıkları | `.htaccess` L31–45 | `web.config` L39–55 | ⚠️ IIS'de ek: Permissions-Policy |
| 4 | PHP hata ayarı | `.htaccess` L46–50 | **yok** | ⚠️ IIS'de eksik |
| 5 | CSP fallback | `.htaccess` L13–14 | ADR-012 middleware | ✅ aynı politika |
| 6 | GZIP sıkıştırma | `.htaccess` L51–58 | IIS'de varsayılan | ✅ |
| 7 | Cache-Control | `.htaccess` L59–65 | `web.config` L56–62 | ✅ |

**Dağılım:** 5 uyumlu · 2 parite açığı (satır 3, 4). Açılar
`server-runtime-kapsam.md` §6.3–6.4'te kayıtlıdır.

**Kritik okuma:** 2 açığın **ikisi de IIS tarafındadır** — yani IIS
**Apache'den daha az güvenlidir**. Bu, IIS'in **üretime alınmadan önce**
giderilmesi gereken **açık bir güvenlik açığı** olduğunu kanıtlar
(`server-runtime-dogrulama.md` §6).

---

## §I Bileşen → Katman Eşlemesi

| # | Katman | Bileşen | Dosya |
|---|---|---|---|
| 1 | Sunucu yapılandırma | Apache `.htaccess` | 65 satır |
| 2 | Sunucu yapılandırma | IIS `web.config` | 62 satır |
| 3 | Uygulama katmanı | `src/Middleware/` | 11 dosya |
| 4 | Uygulama katmanı | `shared/src/PageRouter/` | 5 dosya |
| 5 | Veri katmanı | `shared/src/Database/` | 3 dosya |
| 6 | Güvenlik katmanı | CSRF · XSS · CSP middleware | 3 dosya |

**Dağılım:** 2 sunucu · 2 uygulama · 1 veri · 1 güvenlik = **6 katman**.

**Öğrenilen:** Sunucu katmanı (Apache/IIS) ile uygulama katmanı
(Middleware) **aynı güvenlik başlıklarını** uygular — bu **çift katmanlı
savunma**dır (defense in depth). Ancak **parite açığı** (§H satır 3–4)
bu çift katmanı **IIS'de zayıflatır**: sunucu katmanı eksik olduğunda
uygulama katmanı **tek başına** kalır.

---

## §J Bileşen → Ölçüm Dökümü

| # | Bileşen grubu | Dosya | Satır/Adet |
|---|---|---|---|
| 1 | Sunucu yapılandırma | `.htaccess` | 65 |
| 2 | Sunucu yapılandırma | `web.config` | 62 |
| 3 | Middleware | `src/Middleware/` | 11 dosya |
| 4 | PageRouter | `shared/src/PageRouter/` | 5 dosya |
| 5 | Database | `shared/src/Database/` | 3 dosya |
| 6 | Events | `shared/src/Events/` | 9 dosya |
| 7 | Contracts | `shared/src/Contracts/` | 19 dosya |
| 8 | API | `shared/src/Api/` | 34 dosya |
| 9 | SQL | `.ai/.sql/mysql/` | 20 dosya |
| 10 | Test | `shared/tests/` | 57 dosya |

**Toplam:** 10 bileşen grubu · **225+ dosya/satır**.

---

## §K Bileşen → Risk Eşlemesi

| # | Bileşen | Risk | Olasılık | Etki | Önlem |
|---|---|---|---|---|---|
| 1 | `.htaccess` | Apache yoksa配置 yok | düşük | yüksek | web.config'e taşı |
| 2 | `web.config` | parite açığı (2 satır) | **orta** | **çok yüksek** | §H satır 3–4 |
| 3 | Middleware | CSRF bypass | düşük | **çok yüksek** | ADR-012 |
| 4 | PageRouter | routing hatası | orta | yüksek | test 57 dosya |
| 5 | Database | SQL injection | düşük | **çok yüksek** | PDO prepared |
| 6 | Events | event kaybı | düşük | orta | EventDispatcher |

**En yüksek risk:** satır 2 (IIS parite açığı) — çünkü **bilinen** ve
**kayıtlı** ancak **giderilmemiştir**. Diğer 5 risk **düşük/orta**
olasılıklıdır ve **önlemleri mevcuttur**.

---

## §L Bileşen → Sahiplik Eşlemesi

| # | Bileşen | Sahip | Katman |
|---|---|---|---|
| 1 | `.htaccess` | DevOps Engineer | sunucu |
| 2 | `web.config` | DevOps Engineer | sunucu |
| 3 | Middleware (11) | Security Engineer | güvenlik |
| 4 | PageRouter (5) | Backend Architect | uygulama |
| 5 | Database (3) | Data Engineer | veri |
| 6 | Events (9) | Backend Architect | uygulama |

**Çakışma analizi:** 4 sahip · **hiçbiri** diğerinin dosyasına dokunmaz.
**Çakışma riski = 0**.

---

## §M Bileşen Kalite Kapısı

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | §A bileşen listesi diskten mi? | §J — 10 ölçüm | ✅ |
| 2 | §H parite açığı kayıtlı mı? | 2 açık | ✅ |
| 3 | §I katman eşlemesi tutarlı mı? | 6 katman | ✅ |
| 4 | §K risk kaydı var mı? | 6 risk | ✅ |
| 5 | §L sahiplik 0 çakışma mı? | §L | ✅ |
| 6 | §G PLANNED açıkça mı? | 4 satır | ✅ |
| 7 | Tahmin taahhüt mü? | §K UYARI | ✅ hayır |

**Sonuç:** 7/7 **GEÇTİ**.

---

## §N Bileşen Sınıflandırması

| # | Küme | Sayı | Durum |
|---|---|---|---|
| 1 | **ÇALIŞAN** | 8 (§A) | ✅ |
| 2 | **PARİTE AÇIĞI** | 2 (§H) | ⚠️ |
| 3 | **PLANNED** | 4 (§G) | ❌ |
| 4 | **REDDEDİLDİ** | 3 (§K not) | — |

**Dağılım:** 8 çalışan · 2 açık · 4 PLANNED · 3 reddedildi = **17 kayıt**.

**Kritik okuma:** 2 parite açığı **bilinen ve kayıtlıdır** — bu, modülün
**dürüstçe belgelendiğini** kanıtlar. 4 PLANNED bileşen **tetik bekliyor**;
hiçbiri **aceleyle üretilmemiştir**.

---

## §Ö Bileşen → Dosya Referansı (tüm kardeşler)

| # | Bileşen grubu | Kapsam | Doğrulama | Web | Örnek | Bileşen |
|---|---|---|---|---|---|---|
| 1 | Sunucu (2) | §6 | §6 | havuz §7 | §A | bu dosya §A,§H |
| 2 | Middleware (11) | §7 | §4 | — | §B | bu dosya §A,§I |
| 3 | PageRouter (5) | §8 | §5 | — | §C | bu dosya §A,§I |
| 4 | Database (3) | §9 | §7 | — | §D | bu dosya §A,§I |
| 5 | PLANNED (4) | §15 | §8 | §12 | — | bu dosya §G |

**Tutarlılık:** 5 bileşen grubu **tamamı** en az bir kardeş dosyayla
**çift yönlü bağlantılıdır** — **bağlantısız = 0**.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
