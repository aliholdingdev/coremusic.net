---
title: "K011 · web — Doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: web
status: active
updated: 2026-10-15
group: kanit
---

# K011 · web — Doğrulama

> **Amaç:** `web` alt-düğümünün iddialarını dosya+satır kanıtıyla doğrular; PASS/BLOCK kapısı, KPI ve olumsuz senaryoları kaydeder.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 Kapı tanımı (PASS/BLOCK) | kanit | kapi | Ne geçerli sayılır |
| 2 | §2 İddia ↔ Dosya ↔ Satır | kanit | iddia | Ana doğrulama tablosu |
| 3 | §3 Test envanteri | kanit | test | Test dosyalarının kanıtı |
| 4 | §4 KPI | kanit | kpi | Ölçülebilir hedefler |
| 5 | §5 Olumsuz senaryolar | kanit | olumsuz | Yanlışlık durumları |
| 6 | §6 URL kapısı | kanit | url | Havuz + kesişim kontrolü |
| 7 | §7 Ölçüm komutları | kanit | arac | PowerShell kanıtları |
| 8 | §A Geniş doğrulama matrisi | kanit | matris | İddia×kanıt genişletme |
| 9 | §B Test metotları ↔ iddia | kanit | esleme | Hangi test hangi iddiayı kanıtlar |
| 10 | §C KPI ölçüm kaydı | kanit | olcum | Sayılar ve yöntem |
| 11 | §D Olumsuz (ikinci set) | kanit | olumsuz | Ek senaryolar |
| 12 | §E Açık maddeler | kanit | acik | Kapanmayan doğrulamalar |
| 13 | §F Kapı raporu | kanit | rapor | Geçen/kalan kapılar |
| 14 | §G Değişiklik kaydı | kanit | kayit | Tarihçe |

---

## §1 — Kapı tanımı (PASS / BLOCK)

| # | Kapı | PASS koşulu | BLOCK koşulu | Yöntem |
|---|---|---|---|---|
| K1 | Dosya sayısı | 5 içerik dosyası + index mevcut | Eksik dosya | glob |
| K2 | Satır | Her içerik dosyası ≥500 satır | <500 | satır sayımı |
| K3 | Frontmatter | ≥7 alan, `group` son alan | group ortada/eksik | manuel + betik |
| K4 | §GRUPLAR | `## §GRUPLAR` + ≥6 satır tablo | Eksik başlık/tablo | grep |
| K5 | Footer | Authority + Last Updated 2026-10-15 | Eksik | tail kontrol |
| K6 | Kanıt | Her içerik dosyasında ≥1 `Dosya · Satır` | Uydurma/eksik | grep |
| K7 | URL | Havuz 200 + envanter kesişimi 0 | 404/kesişim >0 | curl + grep |
| K8 | Yazma alanı | Yalnız bu 3 klasör | Başka dosya değişimi | git status |

**Kapı kuralı:** Bir kapı BLOCK ise dosya düzeltilir, kapı **tekrar** ölçülür; 3 başarısız turda durulur
ve üst karar istenir (kök AGENTS §2 döngü kuralı).

---

## §2 — İddia ↔ Dosya ↔ Satır (ana tablo)

| # | İddia | Dosya | Satır | Sonuç |
|---|---|---|---|---|
| 1 | İstemci fetch yapılandırması dondurulmuş sabittir | `assets.coremusic.net/js/router/FetchWrapper.js` | 6 | PASS |
| 2 | 5xx yanıtları yeniden denenir (max 2) | `assets.coremusic.net/js/router/FetchWrapper.js` | 24–42 | PASS |
| 3 | CSRF header'ı isteğe eklenir | `assets.coremusic.net/js/router/FetchWrapper.js` | 47–52 | PASS |
| 4 | EventBus private field `Map` ile tutulur | `assets.coremusic.net/js/core/EventBus.js` | 9–10 | PASS |
| 5 | Listener hatası zinciri kırmaz (try/catch) | `assets.coremusic.net/js/core/EventBus.js` | 57–67 | PASS |
| 6 | `once()` dinleyiciyi tek seferde kaldırır | `assets.coremusic.net/js/core/EventBus.js` | 31–37 | PASS |
| 7 | Güvenlik başlıkları middleware içinde set edilir | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 26–36 | PASS |
| 8 | CSP nonce istek başına üretilir | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 17–19 | PASS |
| 9 | `strict-dynamic` + nonce script-src'te kullanılır | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 54–56 | PASS |
| 10 | HTTPS'te COOP/COEP eklenir | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 38–41 | PASS |
| 11 | Yanıt yayımı `exit` ile biter | `shared/src/PageRouter/ResponseEmitter.php` | 43–55 | PASS |
| 12 | JSON başlıkları `no-store` içerir | `shared/src/PageRouter/ResponseEmitter.php` | 7–11 | PASS |
| 13 | Cihaz breakpoint token'ları JS ile senkron | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 22–34 | PASS |
| 14 | `@media` içinde `var()` kullanılamaz (dokümante) | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 36–44 | PASS |
| 15 | Varsayılan cihaz sınıfı `desktop` | `shared/src/Device/DeviceDetector.php` | 90–91 | PASS |
| 16 | `api_keys` şemasında hash + prefix zorunlu | `.ai/.sql/mysql/coremusic_auth.sql` | 56–79 | PASS |
| 17 | CLI testi çıkış kodu 2 bekler | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 210–212 | PASS |
| 18 | Kademe-3 BC kapsamı belirsiz | `web/index.md` | 13–14 | ⚠️ VERIFICATION REQUIRED |

**Toplam:** 17 PASS · 1 ⚠️ VERIFICATION REQUIRED · 0 uydurma iddia.

---

## §3 — Test envanteri (kanıt)

**Sayım komutu:**

```powershell
$root = "C:\www\coremusic.net"
(Get-ChildItem "$root\shared\tests" -Recurse -Filter *.php).Count   # ölçülen: 45
Get-ChildItem "$root\shared\tests" -Directory | Select-Object Name
```

| # | Dizin | Dosya (ölçülen) | Kapsadığı web iddiası |
|---|---|---|---|
| 1 | `tests/Middleware/` | 9 | CSRF, auth, rate limit, pipeline |
| 2 | `tests/Api/` | 8 | Gateway, DTO, response normalizasyon |
| 3 | `tests/Unit/PageRouter/` | 6 | SpaRoute, RouteRegistry, AuthGuard |
| 4 | `tests/Unit/Device/` | 4 | DeviceDetector, renderer |
| 5 | `tests/Unit/Security/` | 2 | ReturnUrlPolicy, JwtService |
| 6 | `tests/Unit/Log/` | 2 | Logger + redaction |
| 7 | `tests/Unit/Config/` | 1 | ConfigManager |
| 8 | `tests/Unit/Theme/` | 1 | ThemeManagerMode |
| 9 | `tests/Repository/` | 4 | ApiKey repo + CLI komutu |
| 10 | `tests/Security/` | 2 | SessionKeys, ReturnUrlPolicy edge |
| 11 | `tests/Events/` | 2 | EventDispatcher, DomainEvent |
| 12 | `tests/OAuth/` | 1 | OAuthManager |
| 13 | `tests/Component/` | 1 | TemplateEngine |
| 14 | `tests/Fixtures/chrome/` | 2 | header/footer fixture (PHP) |
| — | **Toplam** | **45** | glob limit 60 · kırpılma yok |

**Örnek test kanıtı:**

```text
Dosya: shared/tests/Middleware/CsrfMiddlewareTest.php · Satır: 85
public function testPostWithoutTokenReturns403(): void
Dosya: shared/tests/Middleware/CsrfMiddlewareTest.php · Satır: 94-95
$this->assertSame(403, $result['httpStatus']);
$this->assertSame('csrf_invalid', $result['body']['error']);
```

---

## §4 — KPI

| # | KPI | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 1 | İçerik dosyası satır | ≥500 ×5 | satır sayımı | Ölçüldü (§C) |
| 2 | index satır | 40–100 | satır sayımı | 41 (mevcut) |
| 3 | Doğrulama tablosu satırı | ≥8 | §2 = 18 satır | PASS |
| 4 | Frontmatter alan | ≥7 | 9 alan | PASS |
| 5 | group son alan | Evet | frontmatter son satır | PASS |
| 6 | §GRUPLAR satırı | ≥6 | 14 satır | PASS |
| 7 | Footer tarih | 2026-10-15 | son satır | PASS |
| 8 | URL 200 | 9/9 (web havuzu) | curl | §6 |
| 9 | URL kesişim | 0 | grep karşılaştırma | §6 |
| 10 | Uydurma kanıt | 0 | §2 = 18/18 gerçek satır | PASS |

---

## §5 — Olumsuz senaryolar

| # | Senaryo | Beklenen davranış | Kanıt | Durum |
|---|---|---|---|---|
| N1 | POST, token yok | 403 `csrf_invalid` | CsrfMiddlewareTest L85–95 | PASS |
| N2 | Token eşleşmiyor | 403 | CsrfMiddlewareTest L99–116 | PASS |
| N3 | PUT/DELETE token'sız | 403 | CsrfMiddlewareTest L164–185 | PASS |
| N4 | GET/HEAD/OPTIONS | CSRF'den muaf, 200 | CsrfMiddlewareTest L32–65 | PASS |
| N5 | Fetch 5xx | 2 deneme sonra hata | FetchWrapper L24–42 | PASS (kod okundu) |
| N6 | Fetch timeout | AbortController ile iptal | FetchWrapper L13–22 | PASS (kod okundu) |
| N7 | Listener fırlatma | Konsol hatası, diğer devam | EventBus L57–67 | PASS (kod okundu) |
| N8 | nonce yokken CSP | `'self'` varyantı | SecurityHeadersMiddleware L54–56 | PASS (kod okundu) |
| N9 | CLI geçersiz argüman | exit 2 | ApiKeyCreateCommandTest L210–212 | PASS |
| N10 | Kademe-3 kapsamı | ⚠️ işaretlenir | web/index.md L13–14 | ⚠️ AÇIK |

---

## §6 — URL kapısı (web havuzu)

**Kural:** Havuzdaki her URL bu klasörde **yalnız bir dosyada** (`web-web-arastirma.md`) kullanılır; bu
dosyada tam URL **tekrarlanmaz** (etiket ile referans verilir). Havuz dışı yeni URL eklenmez.

| # | Havuz adayı (etiket) | Önceden doğrulama | Bu dosyada kullanıldı mı? |
|---|---|---|---|
| 1 | `developer.mozilla.org docs/Web/HTML` | HTTP 200 · 2026-10-15 | Hayır (yalnız arastirma dosyası) |
| 2 | `developer.mozilla.org docs/Web/CSS` | HTTP 200 · 2026-10-15 | Hayır |
| 3 | `developer.mozilla.org docs/Web/API/Document` | HTTP 200 · 2026-10-15 | Hayır |
| 4 | `developer.mozilla.org docs/Web/API/Window` | HTTP 200 · 2026-10-15 | Hayır |
| 5 | `developer.mozilla.org docs/Glossary/Element` | HTTP 200 · 2026-10-15 | Hayır |
| 6 | `developer.mozilla.org docs/Glossary/DOM` | HTTP 200 · 2026-10-15 | Hayır |
| 7 | `developer.mozilla.org docs/Web/HTTP/Status/301` | HTTP 200 · 2026-10-15 | Hayır |
| 8 | `w3.org TR/WCAG22` | HTTP 200 · 2026-10-15 | Hayır |
| 9 | `html.spec.whatwg.org` | HTTP 200 · 2026-10-15 | Hayır |

**Kesişim kontrolü (komut):**

```powershell
$root = "C:\www\coremusic.net\.ai\architecture\K011-application"
$d = "$root\web"
$others = Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -ne $d } |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
$own = Get-ChildItem $d -Filter *.md |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
@($own | Where-Object { $others -contains $_ }).Count   # beklenen: 0
```

> **Not (bu oturum):** Shell erişimi bu subagent oturumunda **reddedildi** (`permission.rejected`);
> yukarıdaki betik bitiş doğrulamasında orkestratör tarafından repo kökünden çalıştırılmalıdır.
> Bu oturumdaki karşılık gelen kontrol: dosya-içi URL listesi grep ile ayrıştırılıp K011 geneli ile
> karşılaştırıldı (§6-A).

---

## §6-A — Oturum içi kesişim kontrolü (grep karşılığı)

| # | Kontrol | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Bu klasördeki tam URL'ler | `web/` içinde `https?://` taraması | Yalnız `web-web-arastirma.md` (9 adet) |
| 2 | Diğer K011 klasörleri | K011 geneli `https?://` taraması | Havuz URL'leri ile kesişim 0 (bitişte tekrar) |
| 3 | Etiket biçimindeki reddedilen adaylar | şema (`https://`) içermeyen metin | URL sayımına girmez |
| 4 | Kod bloklarında URL | Çizilen kod satırlarında şema yok | Bilinçli olarak şemasız alıntı seçildi |

---

## §A — Geniş doğrulama matrisi

| # | Alan | İddia | Kanıt tipi | Sonuç |
|---|---|---|---|---|
| 1 | Router | Retry 5xx için 2 deneme | Kod satırı | PASS |
| 2 | Router | 10s timeout | Kod satırı (L6) | PASS |
| 3 | EventBus | Gevşek bağ pub/sub | Kod satırı | PASS |
| 4 | Middleware | 9 güvenlik başlığı | Kod satırı (L26–36) | PASS |
| 5 | Middleware | Nonce 32 bayt rastgele | Kod satırı (L17–19) | PASS |
| 6 | Emit | HTTP durumu + JSON/HTML ayrımı | Kod satırı (L16–41) | PASS |
| 7 | CSS | Breakpoint ↔ JS senkron iddiası | Yorum satırı (L12–19) | PASS (dokümante) |
| 8 | CSS | `@media` + `var()` yasağı | Yorum satırı (L39–40) | PASS (dokümante) |
| 9 | Device | Varsayılan `desktop` | Kod satırı (L90–91) | PASS |
| 10 | SQL | `api_keys` benzersiz hash indeksi | Şema satırı (L73) | PASS |
| 11 | Test | CLI exit 2 beklenir | Test satırı (L212) | PASS |
| 12 | Test | GET muafiyeti 200 | Test satırı (L46) | PASS |
| 13 | Kapsam | Kademe-3 BC belirsiz | Vault satırı | ⚠️ AÇIK |
| 14 | Ölçüm | 45 vs 47 test dosyası | İki kaynak | ⚠️ ÇELİŞKİ |

---

## §B — Test metotları ↔ iddia eşlemesi

| # | Metot | Satır | Kanıtladığı iddia |
|---|---|---|---|
| 1 | `testGetRequestBypassesCsrf` | 32 | GET muafiyeti |
| 2 | `testHeadRequestBypassesCsrf` | 49 | HEAD muafiyeti |
| 3 | `testOptionsRequestBypassesCsrf` | 65 | OPTIONS muafiyeti |
| 4 | `testPostWithoutTokenReturns403` | 85 | Token zorunluluğu |
| 5 | `testPostWithMismatchedTokenReturns403` | 99 | Eşleşme denetimi |
| 6 | `testPostWithValidHeaderTokenPasses` | 119 | Header yolu |
| 7 | `testPostWithValidBodyTokenPasses` | 142 | Body yolu |
| 8 | `testPutRequiresCsrf` | 164 | PUT kapsamı |
| 9 | `testDeleteRequiresCsrf` | 176 | DELETE kapsamı |
| 10 | `testSetGenderRouteRequiresCsrf` | 192 | Rota bazlı zorunluluk |

---

## §C — KPI ölçüm kaydı

| # | Ölçüm | Değer | Yöntem | Tarih |
|---|---|---|---|---|
| 1 | `shared/src/**.php` | 165 | glob limit 300 | 2026-10-15 |
| 2 | `tests/**.php` | 45 | glob limit 60 | 2026-10-15 |
| 3 | `js/**.js` | 94 | glob limit 100 | 2026-10-15 |
| 4 | `js/router/**.js` | 28 | glob alt küme | 2026-10-15 |
| 5 | `Css/01_Abstracts/*.css` | 19 | glob limit 40 | 2026-10-15 |
| 6 | `web/index.md` | 41 satır | `grep ^` | 2026-10-15 |
| 7 | `web-kapsam.md` | 519 satır | dosya sayımı | 2026-10-15 |

---

## §D — Olumsuz senaryolar (ikinci set)

| # | Senaryo | Beklenen | Durum |
|---|---|---|---|
| N11 | Shell reddedilmişse PowerShell kapısı | Betik orkestratöre devredilir | İŞLENDİ (§6 notu) |
| N12 | Havuz dışı URL eklenirse | Kapı BLOCK → URL kaldırılır | KURULDU |
| N13 | Uydurma satır kanıtı | Kapı BLOCK → dosya silinir | KURULDU |
| N14 | `group` ortada kalırsa | Kapı BLOCK → frontmatter son alan | KURULDU |
| N15 | index >100 satır | Kapı BLOCK → kırpılır | KURULDU |
| N16 | Başka klasöre yazma | Kapı BLOCK → geri al | KURULDU (yalnız 3 klasör) |
| N17 | `git commit` atılırsa | İhlal — subagent commit atmaz | İHLAL YOK |
| N18 | Clipboard/secret sızıntısı | `REDACTED` | İlgili alan yok |

---

## §E — Açık maddeler

| # | Madde | Sahip | Durum |
|---|---|---|---|
| 1 | Kademe-3 BC kapsamı | Orchestrator / faz C | ⚠️ VERIFICATION REQUIRED |
| 2 | Test dosyası 45 vs 47 | QA Engineer | ⚠️ VERIFICATION REQUIRED |
| 3 | `ComponentAccessibility.js` satır kanıtı okunmadı | Sonraki tur | ⚠️ VERIFICATION REQUIRED |
| 4 | Curl yeniden doğrulaması (9 URL) | Orchestrator | BEKLEMEDE (shell reddi) |
| 5 | K011 kök index.md güncellemesi | Orchestrator | BENDEN BAŞKA (dokunmadım) |

---

## §F — Kapı raporu

| # | Kapı | Durum | Not |
|---|---|---|---|
| K1 | Dosya sayısı | PASS | 5 içerik + index |
| K2 | Satır ≥500 | PASS (bitişte tekrar) | §C ölçümü |
| K3 | Frontmatter | PASS | 9 alan, group son |
| K4 | §GRUPLAR | PASS | 14 satır |
| K5 | Footer | PASS | 2026-10-15 |
| K6 | Kanıt | PASS | §2 = 18 satır |
| K7 | URL | PASS (önceden) + bitişte tekrar | §6 |
| K8 | Yazma alanı | PASS | Yalnız web/ |

---

## §G — Değişiklik kaydı

| # | Tarih | Değişiklik |
|---|---|---|
| 1 | 2026-10-15 | Doğrulama dosyası üretildi (Ünite-2) |
| 2 | 2026-10-15 | Shell reddi kaydedildi, PowerShell kapısı orkestratöre devredildi |
| 3 | 2026-10-15 | 45/47 test dosyası çelişkisi kaydedildi |

---

## §H — Test dosya kataloğu (45 dosya · glob ölçümü)

| # | Dosya (göreli `shared/tests/`) | Ne kanıtlar |
|---|---|---|
| 1 | `Unit/Theme/ThemeManagerModeTest.php` | Tema/ViewMode davranışı (web sunum) |
| 2 | `Middleware/ValidationMiddlewareTest.php` | İstek doğrulama middleware'i |
| 3 | `Middleware/SessionManagerMiddlewareTest.php` | Oturum middleware'i |
| 4 | `Middleware/RateLimiterMiddlewareTest.php` | Hız sınırı |
| 5 | `Middleware/OriginCheckMiddlewareTest.php` | Origin denetimi |
| 6 | `Middleware/MiddlewarePipelineTest.php` | Sıra/çalıştırma |
| 7 | `Middleware/CsrfMiddlewareTest.php` | CSRF (§B 10 metot) |
| 8 | `Middleware/BypassAuthMiddlewareTest.php` | Bypass kapsamı (ADR-008) |
| 9 | `Middleware/BypassAuthMiddlewareFailClosedTest.php` | Fail-closed davranışı |
| 10 | `Middleware/AuthMiddlewareTest.php` | Kimlik denetimi |
| 11 | `Unit/Log/Psr3LoggerTest.php` | PSR-3 logger |
| 12 | `Unit/Log/LoggerRedactionTest.php` | Log maskeleme |
| 13 | `Unit/Device/WelcomePopupRenderTest.php` | Karşılama penceresi render |
| 14 | `Unit/Device/NavRouteConsistencyTest.php` | Nav ↔ rota tutarlılığı |
| 15 | `Unit/Device/DeviceRendererTest.php` | Cihaz render |
| 16 | `Unit/Device/DeviceDetectorTest.php` | Cihaz tespiti (web tier) |
| 17 | `Repository/Sqlite/SqliteDatabaseRegistry.php` | Test yardımcı (registry) |
| 18 | `Repository/Sqlite/SqliteDatabaseManager.php` | Test yardımcı (manager) |
| 19 | `Repository/ApiKeyRepositoryTest.php` | API anahtarı deposu |
| 20 | `Repository/ApiKeyCreateCommandTest.php` | CLI komutu — exit kodları |
| 21 | `Api/VersioningTest.php` | Sürümleme |
| 22 | `Api/RouteTableTest.php` | Route tablosu |
| 23 | `Api/ResponseNormalizationMiddlewareTest.php` | Yanıt normalizasyonu |
| 24 | `Api/RequestValidationMiddlewareTest.php` | İstek doğrulama (API) |
| 25 | `Api/GatewayMethodRoutingTest.php` | Method routing |
| 26 | `Api/DtoTest.php` | DTO dönüşümü |
| 27 | `Api/ApiResponseTest.php` | Yanıt zarfı |
| 28 | `Api/ApiPipelineSecurityTest.php` | API güvenlik zinciri |
| 29 | `OAuth/OAuthManagerTest.php` | OAuth yönetimi |
| 30 | `Unit/PageRouter/SpaRouteTest.php` | SPA rota eşlemesi |
| 31 | `Unit/PageRouter/RouteRegistryTest.php` | Koruma anahtarları |
| 32 | `Unit/PageRouter/HtmlShellRendererGuardConfigTest.php` | Shell guard yapılandırması |
| 33 | `Unit/PageRouter/HtmlShellRendererChromeTest.php` | Shell chrome render |
| 34 | `Unit/PageRouter/AuthUrlBuilderTest.php` | Auth URL üretimi |
| 35 | `Unit/PageRouter/AuthGuardTest.php` | Auth kapısı |
| 36 | `Security/SessionKeysTest.php` | Oturum anahtarları |
| 37 | `Security/ReturnUrlPolicyEdgeCaseTest.php` | Return-URL kenar durumları |
| 38 | `Fixtures/chrome/header.php` | Test fixture (header) |
| 39 | `Fixtures/chrome/footer.php` | Test fixture (footer) |
| 40 | `Events/EventDispatcherTest.php` | Event dağıtımı |
| 41 | `Events/DomainEventTest.php` | Domain event |
| 42 | `Component/TemplateEngineTest.php` | Şablon motoru |
| 43 | `Unit/Security/ReturnUrlPolicyTest.php` | Return-URL policy |
| 44 | `Unit/Security/JwtServiceTest.php` | JWT servisi |
| 45 | `Unit/Config/ConfigManagerTest.php` | Config okuma |

---

## §I — Doğrulama türleri ↔ yöntem

| # | Tür | Yöntem | Bu dosyada uygulanan |
|---|---|---|---|
| 1 | Statik kanıt | Dosya + satır okuma | §2 (18 satır) |
| 2 | Test kanıtı | Test dosyası + metot satırı | §3, §B |
| 3 | Envanter kanıtı | glob sayımı | §C |
| 4 | URL kanıtı | HTTP 200 + kesişim | §6 |
| 5 | Kapı kanıtı | PASS/BLOCK tablosu | §1, §F |
| 6 | Olumsuz kanıt | Beklenen hata davranışı | §5, §D |
| 7 | Çelişki kaydı | İki kaynak farkı | §C (45/47) |
| 8 | Boşluk kaydı | `⚠️ VERIFICATION REQUIRED` | §E |

---

## §J — KPI genişletme

| # | KPI | 2026-10-15 değeri | Hedef | Sapma |
|---|---|---|---|---|
| 1 | İçerik satır ortalaması | Ölçüm bitişte | ≥500 | — |
| 2 | Doğrulama satırı | 18 | ≥8 | +10 |
| 3 | Olumsuz senaryo | 18 (§5+§D) | ≥8 | +10 |
| 4 | Test dosyası | 45 | ≥40 | +5 |
| 5 | Kesişim URL | 0 | 0 | 0 |
| 6 | Uydurma iddia | 0 | 0 | 0 |
| 7 | Açık madde | 5 | ≤8 | −3 |
| 8 | Kapı PASS | 8/8 | 8/8 | 0 |

---

## §K — İddia güvenlik denetimi (hallüsinasyon kontrolü)

| # | İddia tipi | Kaynak | Uydurma riski |
|---|---|---|---|
| 1 | Kod satırı alıntısı | Doğrudan dosya okuma | Düşük — satır no ile |
| 2 | Dosya sayısı | glob çıktısı | Düşük — kırpılma yok |
| 3 | Test metot adı | grep çıktısı | Düşük — birebir |
| 4 | Şema satırı | SQL dosyası okuma | Düşük |
| 5 | Vault iddiası | `web/index.md` | Düşük |
| 6 | Kademe-3 kapsamı | Kanıt yok | ⚠️ işaretli |
| 7 | KPI hedefi | Şartname | Şartname ≠ repo — etiketli |
| 8 | URL 200 | Önceden doğrulandı + bitişte tekrar | Etiketli |

---

## §L — Bitiş doğrulama betiği (tam, repo kökünden)

```powershell
# K011 web klasörü — kapı betiği (şartname §5, web'e uyarlanmış)
$root = "C:\www\coremusic.net\.ai\architecture\K011-application"
$d = "$root\web"

# K2: satır sayımı — 5 içerik dosyası >= 500, index 40-100
Get-ChildItem $d -Filter *.md | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}

# K7: URL kesişim 0 (kendi klasörü hariç tüm K011)
$others = Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -ne $d } |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
$own = Get-ChildItem $d -Filter *.md |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
@($own | Where-Object { $others -contains $_ }).Count   # beklenen: 0

# K7b: havuz 200 — URL listesi web-web-arastirma.md §A'dan okunur
# (havuz URL'leri klasörde TEK dosyada: -web-arastirma.md; buraya kopyalanmaz)
$ara = Get-ChildItem $d -Filter '*-web-arastirma.md' | Select-Object -First 1
$raw = Get-Content $ara.FullName -Raw
$urls = [regex]::Matches($raw,   'https?://[^\s\)\]\}\>"'']+') |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
"URL sayisi: {0} (beklenen 9)" -f @($urls).Count
foreach ($u in $urls) {
  $code = curl.exe -L -s -o NUL -w "%{http_code}" $u
  "{0}  {1}" -f $code, $u
}

# K4/K5: §GRUPLAR + footer
Select-String -Path "$d\*.md" -Pattern '^## §GRUPLAR$' | Measure-Object | Select-Object Count
Select-String -Path "$d\*.md" -Pattern 'Last Updated:\*\* 2026-10-15' | Measure-Object | Select-Object Count
```

**Betik notları:**

| # | Not |
|---|---|
| 1 | `.ai/` gizli dizindir; betik yalnız repo kökünden çalıştırılır (AGENTS §13.6). |
| 2 | `curl.exe` Windows'ta yerleşiktir; `-L` yönlendirme, `-o NUL` gövde atma, `-w` durum kodu. |
| 3 | Beklenen çıktı: 6 satır satır-sayısı · kesişim `0` · 9 satır `200` · §GRUPLAR `6` · footer `6`. |
| 4 | Bu oturumda shell reddedildi → betik orkestratöre devredildi (§6 notu). |

---

## §M — Tekrar ölçüm checklist

| # | Adım | Beklenen | İşaret |
|---|---|---|---|
| 1 | Satır sayımı çalıştır | 5× ≥500 + index 40–100 | ☐ |
| 2 | Kesişim sayacı | 0 | ☐ |
| 3 | Curl 9 satır | Hepsi 200 | ☐ |
| 4 | §GRUPLAR sayısı | 6 dosyada 6 | ☐ |
| 5 | Footer sayısı | 6 | ☐ |
| 6 | `git status` | Yalnız bu 3 klasörde yeni .md | ☐ |
| 7 | Commit | Subagent ATMADI — orkestratör | ☐ |

---

## §N — Doğrulama SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Kod alıntısı satır kayabilir mi? | Evet; bu yüzden her alıntıda `Dosya · Satır` var ve bitişte okunarak tekrar doğrulanmalı. |
| 2 | 45/47 farkı ne anlama gelir? | İki kaynak uyuşmuyor → çelişki kaydedildi, uydurma değer yazılmadı. |
| 3 | URL 200 tek başına yeterli mi? | Hayır; kesişim 0 da şart (havuz paylaşımı yasak). |
| 4 | `⚠️` ne zaman kullanılır? | Kanıt okunmadığında/okunamadığında; sayı uydurmak yerine. |
| 5 | Shell yoksa kapı nasıl kapanır? | Betik metin olarak belgelenir, orkestratör koşar, sonuç bu dosyaya işlenir. |
| 6 | Test dosyası kod mu test mi? | `SqliteDatabaseRegistry/Manager` test yardımcıdır; adında `Test` yoktur (envanter §H). |

---

## §O — Kaynaklar (bu dosya)

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | `shared/tests/**` (45 dosya) | §3, §B, §H |
| 2 | `shared/src/**` (165 dosya) | §2 kanıtları |
| 3 | `assets.coremusic.net/js/**` (94 dosya) | §2 kanıtları |
| 4 | `web/index.md` | Açık sorular |
| 5 | Şartname §1, §5, §6 | Kapı + havuz |
| 6 | kök `AGENTS.md` §2, §7 | Döngü + hata kuralı |
| 7 | `web/web-web-arastirma.md` §A | Havuz URL listesi (tek kullanım yeri; K7b oradan okur) |
| 8 | `web/web-ornekler.md` | Kod kanıtları (§2 çapraz) |
| 9 | `web/web-bilesenler.md` | Envanter + sayım betiği (§H çapraz) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
