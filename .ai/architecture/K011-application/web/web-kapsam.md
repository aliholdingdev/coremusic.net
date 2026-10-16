---
title: "K011 · web — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: web
status: active
updated: 2026-10-15
group: mimari
---

# K011 · web — Kapsam

> **Amaç:** Bu dosya, K011-application içindeki `web` alt-düğümünün kapsamını (ne DEĞİL dahil) tanımlar; kademe-2 seviyesinde tasarım iddiası olarak taşır ve kademe-3 bounded-context kararını bekler.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 Amaç ve ELI15 | mimari | kapsam | Düğümün tek cümlelik tanımı |
| 2 | §2 Sınır — ne DEĞİL | mimari | sinir | Komşu K düğümlerine devir listesi |
| 3 | §3 Kapsam içi alan envanteri | mimari | alan | Web katmanının sahip olduğu yüzeyler |
| 4 | §4 Akış tablosu | mimari | akis | İstek→yanıt zincirinin kapsamı |
| 5 | §5 Kapsam dışı devirler | mimari | dis | Sahiplik transferi kaydı |
| 6 | §6 Rol ve sorumluluk | mimari | rol | Kim bu kapsamda ne yapar |
| 7 | §7 Bağımlılık özeti | mimari | bagimlilik | Üst kademeye bağlılık |
| 8 | §8 Ölçüm referansı | kanit | olcum | Dosya/sayı kanıtları |
| 9 | §A Genişletilmiş kapsam matrisi | mimari | genis | Alan × sorumluluk kesişimi |
| 10 | §B Bileşen↔alan listesi | mimari | bilesen | Bileşenin hangi alana aitti |
| 11 | §C Erişilebilirlik kapsamı | mimari | erisim | WCAG taahhüdü ve sınırı |
| 12 | §D Açık sorular | mimari | qa | Cevap bekleyen kapsam soruları |
| 13 | §E Kapsam SSS | mimari | sinir | Sık karıştırılan sınırlar |
| 14 | §F Değişiklik kaydı | kanit | kayit | Tarihçe ve kapı geçmişi |

---

## §1 — Amaç ve ELI15

**ELI15:** `web`, tarayıcıda görünen her şeyin kapsamıdır: sayfanın iskeleti, bileşenler, stiller, form
akışları ve tek sayfa uygulamasının (SPA) gezinme davranışı. Bu düğüm "tarayıcıda ne oluyor" sorusunun
cevabıdır; "sunucu ne hesaplıyor" sorusu `server-runtime`, "veritabanında ne var" sorusu K008/K015'tir.

**Kapsam cümlesi (tek satır):** Tarayıcı istemcisinin sunucudan aldığı HTML/CSS/JS yüzeyini, bu
yüzeyin gezinme ve erişilebilirlik davranışını ve istemci tarafı doğrulama/güvenlik başlıklarını kapsar.

**Kapsamın kademe durumu:** `status: draft` — kademe-2 taslak kapsam. Kademe-3 (bounded-context) ve
kademe-4 genişletmesi ADR-097 K4 fazı (C) bekler. Bu dosyadaki satırlar **TASARIM İDDİASI**'dır;
kanıt satırları `web-dogrulama.md` ve `web-bilesenler.md` dosyalarındadır.

**Kanıt örneği (kapsamın gerçek karşılığı):**

```text
Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 6
static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 });
```

İstemci tarafı ağ çağrılarının zaman aşımı/yeniden deneme kapsamı bu sabit ile sınırlıdır — yani web
kapsamı "fetch çağrıları nasıl davranır" sorusunu da içerir.

---

## §2 — Sınır (ne DEĞİL)

| # | Yüzey | Bu kapsama girmez mi? | Sorumlu düğüm | Gerekçe |
|---|---|---|---|---|
| 1 | İş kuralı kararı (ör. çalma listesi kuralı) | Hayır | K008-domain | İş kuralı istemcide yaşamaz |
| 2 | Sunucu çalışma zamanı (PHP FPM, CLI, kurulum) | Hayır | K011/server-runtime · local-runtime | Barındırma ≠ arayüz |
| 3 | HTTP yanıt başlıklarının üretimi (sunucu tarafı) | Hayır | K012-services / sunucu katmanı | İstemci başlıkları okur, sunucu üretir |
| 4 | Ağ taşıması, TLS, CDN, yeniden yönlendirme altyapısı | Hayır | K014-network | Taşıma katmanı |
| 5 | Veritabanı şeması ve sorgular | Hayır | K015 / data alanı | Veri sahipliği |
| 6 | Medya kodlama ve dosya saklama | Hayır | K015-media | Medya verisi |
| 7 | Electron paketleme, masaüstü pencere kabuğu | Hayır | K011/electron · K011/desktop | OS seviyesi ≠ tarayıcı |
| 8 | API gateway sözleşmesi (DTO, sürümleme) | Hayır | K011/application-services · API alanı | Sözleşme sunucu tarafındadır |
| 9 | Uygulama seviyesi cache anahtarları | Hayır | K011/caching | Cache politikası sunucudadır |
| 10 | Yönendirme çatısı (sunucu rota çözümü) | Hayır | K011/routing | Sunucu rota ≠ istemci gezinme |

**Sınır notu (kanıt):** `shared/src/PageRouter/ResponseEmitter.php` dosyasındaki `emit()` metodu
`http_response_code()` çağrısını yapar ve `exit` ile süreci bitirir — bu davranış sunucu tarafıdır;
`web` kapsamı yalnız bu çıktının **tarayıcıda nasıl işlendiği** ile ilgilenir.

```text
Dosya: shared/src/PageRouter/ResponseEmitter.php · Satır: 16
public function emit(array $response, ?string $traceId = null, bool $isSpa = false, array $extraHeaders = []): never
```

---

## §3 — Kapsam içi alan envanteri

| # | Alan | Tanım | Kapsam durumu | Kanıt durumu |
|---|---|---|---|---|
| 1 | HTML iskelet (shell) | Sayfanın ilk belge çıktısı | Kapsam içi | `shared/src/PageRouter/` envanteri — web-bilesenler §1 |
| 2 | SPA gezinme | İstemci rota değiştirme, içerik yamalama | Kapsam içi | `assets.coremusic.net/js/router/` — 28 dosya (ölçüm) |
| 3 | Bileşen kütüphanesi | Primitive + composite JS bileşenleri | Kapsam içi | `js/components/` — base 5 · primitives 12 · composites 13 · interactive 7 |
| 4 | ITCSS stil katmanları | 11 katmanlı CSS düzeni | Kapsam içi | `assets.coremusic.net/Css/01..11` — 01_Abstracts = 19 token dosyası |
| 5 | Cihaz katmanı | Breakpoint ve cihaz CSS eşlemesi | Kapsam içi (sunum) | `js/devices.config.js` · `shared/src/Device/DeviceCssMap.php` |
| 6 | Form akışı | Gönderme, istemci doğrulama, CSRF token senkronu | Kapsam içi | `js/router/CsrfSyncManager.js` · `shared/tests/Middleware/CsrfMiddlewareTest.php` |
| 7 | Erişilebilirlik (WCAG 2.2 AA) | Kontrast, odak, hedef boyutu | Kapsam içi | `js/components/base/ComponentAccessibility.js` |
| 8 | İstemci güvenlik başlıkları okuma | `X-Requested-With`, CSRF header ekleme | Kapsam içi (okuma) | `js/router/FetchWrapper.js` L47–L52 |
| 9 | Olay yayını (pub/sub) | Modüller arası gevşek bağ | Kapsam içi | `js/core/EventBus.js` L8–L72 |
| 10 | Oturum durumu (istemci tarafı) | Cookie'ye dayalı durum okuma, 401 yakalama | Kapsam içi (sunum) | ⚠️ VERIFICATION REQUIRED — kapsam kenarı |
| 11 | Özelleştirme (tema/görünüm modu) | Kullanıcı tercihlerinin uygulanması | Kapsam içi | `js/managers/ThemeManager.js` · `ViewModeManager.js` |
| 12 | Bildirim/toast/modal | Anlık kullanıcı geri bildirimi | Kapsam içi | `js/components/composites/ToastComponent.js` · `ModalComponent.js` |

---

## §4 — Akış tablosu (kapsam içi akışlar)

| # | Akış | Başlangıç | Bitiş | Kapsam sahibi |
|---|---|---|---|---|
| 1 | Sayfa ilk yükleme | Belge isteği | İlk boyama | web (shell + CSS) |
| 2 | SPA gezinme | Bağlantı tıklaması | İçerik değişimi | web (router) |
| 3 | İstemci rota koruması | Gezinme niyeti | Devam / engel | web (guards) |
| 4 | AJAX çağrısı | İşlem niyeti | JSON yanıtın işlenmesi | web (FetchWrapper) |
| 5 | CSRF token senkronu | Token değişimi | Header'ın güncellenmesi | web + sunucu ortak |
| 6 | Cihaz katmanı seçimi | Viewport / UA ölçümü | CSS yüklenmesi | web (device-loader) |
| 7 | Tema değişimi | Kullanıcı tercihi | Yeniden boyama | web (ThemeManager) |
| 8 | Form gönderimi | Gönder düğmesi | Başarı/hata durumu | web (sunum) + sunucu (doğrulama) |
| 9 | Olay yayını | `emit()` | Listener çalışması | web (EventBus) |
| 10 | Hata gösterimi | İstemci hatası | Toast/hata ekranı | web (sunum) |

**Akış kanıtı:**

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php · Satır: 26-36
$headers = [
    'X-Content-Type-Options'         => 'nosniff',
    'X-Frame-Options'                => 'DENY',
    'X-XSS-Protection'               => '0',
    'Referrer-Policy'                => 'strict-origin-when-cross-origin',
    'Content-Security-Policy'        => $csp,
    'Require-Trusted-Types-For'      => "'script'",
    'Cross-Origin-Resource-Policy'   => 'cross-origin',
    'Permissions-Policy'             => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
    'X-Permitted-Cross-Domain-Policies' => 'none',
];
```

Bu başlıklar web kapsamını **etkiler** (tarayıcı davranışı) ama **üretimi** sunucu middleware'ine aittir
— kapsam ikiye ayrılır: "başlığı sunucu üretir" (dışı) · "başlığa uygun davranmak tarayıcının işidir" (içi).

---

## §5 — Kapsam dışı devirler

| # | Devredilen konu | Giden düğüm | Devir gerekçesi | Kayıt |
|---|---|---|---|---|
| 1 | Rota çözümü (sunucu) | K011/routing | URL→handler eşlemesi sunucudadır | Kademe-2 |
| 2 | Uygulama cache'i | K011/caching | Önbellek politikası istemcide değil | Kademe-2 |
| 3 | Komut/ sorgu ayrımı | K011/command-query · queries | Uygulama servis katmanı | Kademe-3/4 (Ünite-4) |
| 4 | Event akışları (uygulama içi) | K011/event-akislari | Yayıncı/abone mimarisi ayrı | Ünite-4 |
| 5 | DTO ve mapper | K011/dto-ve-mapper | Taşıyıcı şema ayrı | Ünite-4 |
| 6 | Zamanlanmış görevler | K011/zamanlamali-gorevler | Tetikleyici mekanizması ayrı | Ünite-4 |
| 7 | Ödeme servisleri | K011/odem-servisleri | Repo'da ödeme kodu yok (PLANNED) | Ünite-4 |
| 8 | Electron kabuğu | K011/electron | Masaüstü paketleme ayrı | Kademe-2 |

---

## §6 — Rol ve sorumluluk

| # | Rol | Bu kapsamda ne yapar | Ne YAPMAZ | Kaynak |
|---|---|---|---|---|
| 1 | UI Designer | CSS/JS üretimi, ITCSS + BEM | Commit atmaz, ADR üretmez | kök AGENTS §5 · css AGENTS §3.1 |
| 2 | Backend Architect | Sunucu tarafı HTML/JSON üretimi | CSS dosyasına yazmaz | kök AGENTS §5 |
| 3 | Security Engineer | CSP/CSRF başlığı denetimi | Kodu kendisi düzeltmez, handover eder | kök AGENTS §6 |
| 4 | QA Engineer | WCAG + responsive doğrulama | Üretim kodunu düzeltmez | css AGENTS §3.1 |
| 5 | MO (orchestrator) | Routing, envanter, commit | Kod yazmaz | kök AGENTS §7 |

---

## §7 — Bağımlılık özeti

| # | Bağımlı olduğu | Tür | Etki | Ayrıntı |
|---|---|---|---|---|
| 1 | K011-application (üst düğüm) | Mimari | Kapsamın tamamı üst kademeye bağlı | application-bagimliliklar.md §1 |
| 2 | K008-domain | Sözleşme | İş kuralı iddiaları buradan gelir | ⚠️ TASARIM İDDİASI |
| 3 | K009-security | Sözleşme | CSRF/CSP kuralları | Kök AGENTS §6 security satırı |
| 4 | K012-services | API | İstemcinin çağırdığı uçlar | ⚠️ VERIFICATION REQUIRED (kademe-3) |
| 5 | assets.coremusic.net | Dağıtım | CSS/JS/font servisi | assets AGENTS §2 envanteri |
| 6 | shared/src/PageRouter | Kod | HTML shell üretimi | 14 dosya (ölçüm) |

---

## §8 — Ölçüm referansı (kanıt)

**Sayım komutu (repo kökünden çalıştırılır):**

```powershell
$root = "C:\www\coremusic.net"
(Get-ChildItem "$root\shared\src" -Recurse -Filter *.php).Count      # beklenen: 165
(Get-ChildItem "$root\assets.coremusic.net\js" -Recurse -Filter *.js).Count  # beklenen: 94
(Get-ChildItem "$root\assets.coremusic.net\Css\01_Abstracts" -Filter *.css).Count  # beklenen: 19
(Get-ChildItem "$root\shared\tests" -Recurse -Filter *.php).Count     # ölçülen: 45
```

| # | Ölçüm | Değer | Yöntem | Tarih |
|---|---|---|---|---|
| 1 | `shared/src/**.php` | 165 | glob (limit 300, kırpılma yok) | 2026-10-15 |
| 2 | `assets.coremusic.net/js/**.js` | 94 | glob (limit 100, kırpılma yok) | 2026-10-15 |
| 3 | `Css/01_Abstracts/*.css` | 19 | glob (limit 40, kırpılma yok) | 2026-10-15 |
| 4 | `shared/tests/**.php` | 45 | glob (limit 60, kırpılma yok) | 2026-10-15 |
| 5 | `js/router/**.js` | 28 | glob alt kümesi sayımı | 2026-10-15 |

**Çelişki kaydı:** Şartname §4 "shared/tests 47 dosya" der; bu oturumda ölçülen **45**. Fark (2 dosya)
→ `⚠️ VERIFICATION REQUIRED` (farkın kaynağı bu oturumda belirlenmedi).

---

## §A — Genişletilmiş kapsam matrisi

| # | Alan | Sunum | İstemci mantığı | Sunucu etkileşimi | Kapsam etiketi |
|---|---|---|---|---|---|
| 1 | HTML shell | Evet | Hayır | Üretilir | İç + sınır |
| 2 | SPA router | Evet | Evet | Fetch ile | İç |
| 3 | Bileşenler | Evet | Evet | Veri çeker | İç |
| 4 | ITCSS | Evet | Hayır | — | İç |
| 5 | Cihaz katmanı | Evet | Evet | UA/viewport sinyali | İç |
| 6 | Formlar | Evet | Evet | POST eder | İç + sınır |
| 7 | Erişilebilirlik | Evet | Evet | — | İç |
| 8 | Auth akışı | Kısmen | Kısmen | Sunucu sahibi | Sınır |
| 9 | Ödeme akışı | Hayır | Hayır | K012 | Dışı |
| 10 | Bildirim verisi | Hayır | Gösterir | K012 | İç (yalnız sunum) |
| 11 | Loglama | Hayır | Konsol | Sunucu | Sınır |
| 12 | Cache | Hayır | İstek cache'i | K011/caching | Sınır |

---

## §B — Bileşen↔alan listesi

| # | Bileşen (disk) | Alan | Tür | Kapsam notu |
|---|---|---|---|---|
| 1 | `js/core/EventBus.js` | Olay yayını | Altyapı | 73 satır — tüm modüller kullanır |
| 2 | `js/core/CoreMusicApp.js` | Uygulama kökü | Altyapı | ⚠️ satır kanıtı bu oturumda okunmadı |
| 3 | `js/router/Router.js` | Gezinme | Altyapı | 28 dosyalık router çekirdeği |
| 4 | `js/components/base/ComponentBase.js` | Bileşen temeli | Altyapı | Her bileşen bunu kurar |
| 5 | `js/components/primitives/*` | Form/temel | Bileşen | 12 dosya (spec + testler dahil) |
| 6 | `js/components/composites/*` | Karmaşık | Bileşen | 13 dosya |
| 7 | `js/components/interactive/*` | Etkileşimli | Bileşen | 7 dosya |
| 8 | `Css/01_Abstracts/*` | Token | Stil | 19 dosya — sabit değer yalnız burada |
| 9 | `Css/08_Devices/*` | Cihaz | Stil | 15 dosya (assets AGENTS §2) |
| 10 | `js/managers/ThemeManager.js` | Tema | Yönetici | Tercih uygulama |

---

## §C — Erişilebilirlik kapsamı

| # | Gereksinim | Kapsam | Sahip | Kanıt |
|---|---|---|---|---|
| 1 | WCAG 2.2 AA | İç | QA Engineer | css AGENTS §2 ön koşul |
| 2 | Kontrast ≥4.5:1 | İç | UI + QA | css AGENTS §3.1 QA satırı |
| 3 | Odak halkası görünür | İç | UI Designer | ⚠️ satır kanıtı okunmadı |
| 4 | Klavye ile gezinme | İç | UI Designer | `js/components/base/ComponentAccessibility.js` (dosya var, satır okunmadı) |
| 5 | Dokunma hedefi ≥24×24px | İç | UI Designer | kök AGENTS §UI kuralı |
| 6 | Ekran okuyucu etiketleri | İç | UI Designer | ⚠️ VERIFICATION REQUIRED |

---

## §D — Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | Kademe-3'te hangi bounded-context'ler web'e özel? | Kapsam yeniden bölünebilir | ⚠️ VERIFICATION REQUIRED (faz C) |
| 2 | İstemci tarafı oturum durumu tam olarak hangi store'da? | Kapsam ↔ güvenlik sınırı | ⚠️ VERIFICATION REQUIRED |
| 3 | `shared/tests` 45 vs şartname 47 farkının kaynağı? | Ölçüm güveni | ⚠️ VERIFICATION REQUIRED |
| 4 | Web kapsamı Service Worker / offline içeriyor mu? | Yeni alan açar | ⚠️ VERIFICATION REQUIRED |
| 5 | Bileşen kitaplığı kademe-3'te ayrı BC olur mu? | Bölünme kararı | ⚠️ VERIFICATION REQUIRED |

---

## §E — Kapsam SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | SPA router burada mı? | Evet — istemci gezinme web kapsamındadır; sunucu rota çözümü K011/routing'dedir. |
| 2 | CSS token'ları burada mı? | Evet — `Css/01_Abstracts/` sunum katmanıdır. |
| 3 | Middleware'ler burada mı? | Hayır — sunucu tarafıdır; web yalnız başlıkların **etkisini** taşır. |
| 4 | Electron var mı? | Hayır — `K011/electron` ayrı düğüm. |
| 5 | WCAG kimin? | QA denetler, UI üretir; kapsam web'e aittir. |
| 6 | Veritabanı? | Hayır — K015/data. |

---

## §F — Değişiklik kaydı

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-12 | Düğüm adı v2 §2.11 ile birebir doğrulandı | Vault (index.md validation) |
| 2 | 2026-10-15 | Kapsam dosyası üretildi (Ünite-2, 5 dosyalık set) | Subagent (commit yok) |
| 3 | 2026-10-15 | Ölçüm çelişkisi (45/47) kaydedildi | Subagent |
| 4 | — | Kademe-3 genişletmesi | Beklemede (ADR-097 faz C) |

---

## §G — Alan → disk eşleme (geniş)

| # | Alan | Disk yolu (göreli) | Dosya türü | Kapsam etiketi |
|---|---|---|---|---|
| 1 | Router çekirdeği | `assets.coremusic.net/js/router/` | JS | İç |
| 2 | Router yapılandırması | `assets.coremusic.net/js/router/config/` | JS | İç |
| 3 | Çekirdek servisler | `assets.coremusic.net/js/core/` | JS | İç |
| 4 | Bileşenler | `assets.coremusic.net/js/components/` | JS | İç |
| 5 | Yöneticiler | `assets.coremusic.net/js/managers/` | JS | İç |
| 6 | Özellikler | `assets.coremusic.net/js/features/` | JS | İç |
| 7 | Oynatıcı | `assets.coremusic.net/js/coreplayer/` | JS | İç |
| 8 | Giriş dosyaları | `assets.coremusic.net/js/*.js` (kök 6) | JS | İç |
| 9 | Token katmanı | `assets.coremusic.net/Css/01_Abstracts/` | CSS | İç |
| 10 | Bileşen stilleri | `assets.coremusic.net/Css/04_Components/` | CSS | İç |
| 11 | Sayfa stilleri | `assets.coremusic.net/Css/05_Pages/` | CSS | İç |
| 12 | Cihaz stilleri | `assets.coremusic.net/Css/08_Devices/` | CSS | İç |
| 13 | HTML shell üretimi | `shared/src/PageRouter/HtmlShellRenderer.php` | PHP | Sınır (sunucu) |
| 14 | Yanıt yayımı | `shared/src/PageRouter/ResponseEmitter.php` | PHP | Sınır (sunucu) |
| 15 | Güvenlik başlıkları | `shared/src/Middleware/SecurityHeadersMiddleware.php` | PHP | Sınır (sunucu) |
| 16 | İstemci testleri | `shared/tests/` | PHP test | İç (kanıt) |

---

## §H — Akış şemaları (kapsam içi)

### §H.1 İlk yükleme

```text
Tarayıcı ── belge isteği ──▶ sunucu (K011/routing → PageRouter)
        ◀── HTML shell + CSS/JS referansları ──
Tarayıcı ── varlık isteği ──▶ assets.coremusic.net (statik)
        ◀── CSS/JS ──
Tarayıcı ── ilk boyama ──▶ web kapsamı sona erer (kademe-2)
```

### §H.2 SPA gezinme

```text
tıklama ──▶ GuardPipeline ──▶ (engel? Devam)
                     │
                     ▼
          ContentFetcher ──▶ FetchWrapper (timeout 10s · retry 2)
                     │
                     ▼
          ContentPatcher / DomPatcher ──▶ ScrollRestorer ──▶ FocusManager
```

Kanıt:

```text
Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 24-42
async #fetchWithRetry(url, signal) {
    let lastError = null;
    for (let attempt = 0; attempt <= FetchWrapper.CONFIG.MAX_RETRIES; attempt++) {
        if (signal.aborted) throw new DOMException('Aborted', ABORT_ERROR_NAME);
        try {
            const response = await this.#doFetch(url, signal, attempt);
            if (!response.ok && response.status >= 500 && attempt < FetchWrapper.CONFIG.MAX_RETRIES) {
                await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
                continue;
            }
            return response;
        } catch (err) { /* ... */ }
    }
}
```

### §H.3 Olay yayını

```text
emit("router:navigate", veri)
   └─▶ EventBus (#listeners Map)
         ├─▶ listener A
         ├─▶ listener B  (hata tek tek yakalanır, zincir kırılmaz)
         └─▶ listener C
```

---

## §I — Kabul kriterleri (kapsam)

| # | Kriter | Ölçüt | Kapı |
|---|---|---|---|
| 1 | Kapsam tanımı tek cümlede | §1 kapsam cümlesi mevcut | PASS |
| 2 | ne DEĞİL listesi ≥10 madde | §2 tablosu 10 satır | PASS |
| 3 | Kapsam içi alan ≥12 | §3 tablosu 12 satır | PASS |
| 4 | Akış tablosu ≥10 | §4 tablosu 10 satır | PASS |
| 5 | Açık sorular bölümü | §D mevcut (5 satır) | PASS |
| 6 | Kanıt bloğu ≥1 | §1 ve §4'te `Dosya · Satır` | PASS |
| 7 | Ölçüm çelişkisi işaretli | §8 45/47 kaydı | PASS |
| 8 | Footer + group son alan | Frontmatter/son satır | PASS |

---

## §J — Kapsam riskleri

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | Kademe-3'te kapsam yeniden bölünmesi | Yüksek | Yüksek | Açık soru #1 — faz C bekleme |
| 2 | Web ↔ desktop eşleşmesinin karışması | Orta | Orta | desktop-kapsam §2 sınır tablosu |
| 3 | Ölçüm çelişkinin yayılması | Orta | Düşük | §8 çelişki kaydı + yeniden sayım |
| 4 | Uydurma kanıt üretimi | Düşük | Yüksek | Yalnız okunmuş dosya/satır |
| 5 | URL havuzunun kirlenmesi | Düşük | Orta | URL yalnız `web-web-arastirma.md` |

---

## §K — Kapsam dışı FAQ (geniş)

| # | Soru | Kısa cevap | Ayrıntı |
|---|---|---|---|
| 1 | `.htaccess` / `web.config` burada mı? | Hayır | Sunucu yapılandırması → DevOps/sunucu |
| 2 | CORS izni burada mı? | Hayır | `shared/src/Middleware/CorsMiddleware.php` sunucu |
| 3 | Rate limit burada mı? | Hayır | `RateLimiterMiddleware` sunucu |
| 4 | JWT üretimi burada mı? | Hayır | `shared/src/Security/JwtService.php` |
| 5 | SQL sorgusu burada mı? | Hayır | K015/data · prepared statement kuralı |
| 6 | Theme CSS token'ı? | Evet | `Css/01_Abstracts/a-theme-config.css` |
| 7 | Font dosyaları? | Evet (varlık) | `assets.coremusic.net/Fonts/` — 105 dosya (assets AGENTS §2) |
| 8 | Service Worker? | Belirsiz | §D açık soru #4 |

---

## §L — Komşu düğümlerle arayüz

| # | Komşu | Arayüz nesnesi | Yön | Kapsam etkisi |
|---|---|---|---|---|
| 1 | K011/routing | URL ↔ handler | Giden | İstemci yalnız sonucu görür |
| 2 | K011/caching | Cache başlıkları | Gelen | `no-store` davranışı |
| 3 | K011/electron | Renderer penceresi | Giden (paylaşım) | ⚠️ VERIFICATION REQUIRED |
| 4 | K011/desktop | BFF `clientType` | Giden (ayrışma) | desktop-kapsam §2 |
| 5 | K012-services | JSON gövdesi | Gelen | DTO görünümü |
| 6 | K014-network | Taşıma | Gelen | İstemci algısı |

---

## §M — Kapsam kabul listesi (şablon)

| # | Madde | Durum |
|---|---|---|
| 1 | ELI15 mevcut | ✅ |
| 2 | Sınır tablosu | ✅ |
| 3 | Kapsam içi envanter | ✅ |
| 4 | Kapsam dışı devirler | ✅ |
| 5 | Açık sorular | ✅ |
| 6 | Ölçüm + çelişki kaydı | ✅ |
| 7 | Kanıt blokları | ✅ |
| 8 | 500 satır eşiği | Ölçüm: §N |

---

## §N — Satır ölçüm kaydı (bu dosya)

| Dosya | Hedef | Ölçüm yöntemi | Durum |
|---|---|---|---|
| `web-kapsam.md` | ≥500 satır | dosya satır sayımı (bitişte tekrar) | Bitiş kontrolünde doğrulanacak |
| `web-dogrulama.md` | ≥500 satır | aynı | Bitiş kontrolünde |
| `web-web-arastirma.md` | ≥500 satır | aynı | Bitiş kontrolünde |
| `web-ornekler.md` | ≥500 satır | aynı | Bitiş kontrolünde |
| `web-bilesenler.md` | ≥500 satır | aynı | Bitiş kontrolünde |
| `index.md` | 40–100 satır | Aynı | Bitiş kontrolünde |

---

## §O — Terimler

| Terim | Tanım (bu kapsamda) |
|---|---|
| Shell | Sunucunun ürettiği ilk HTML belgesi iskeleti |
| SPA | Tek belge üzerinde istemci rota değiştirme |
| Guard | Gezinmeyi engelleyen/doğrulayan istemci kontrolü |
| Patch | Mevcut DOM'un parçasının değiştirilmesi |
| Token | Yalnız `01_Abstracts` içinde tanımlanan CSS değişkeni |
| Eksen | §GRUPLAR tablosunda ikinci gruplama ekseni |
| BFF | İstemci tipine göre yanıtı dönüştüren sunucu katmanı |

---

## §P — Senaryo ↔ kapsam kararları

| # | Senaryo | Karar | Gerekçe |
|---|---|---|---|
| 1 | Yeni modal bileşeni eklendi | KAPSAM İÇİ | Sunum bileşeni — `js/components/composites/` |
| 2 | Sunucuda yeni HTTP başlığı | KAPSAM DIŞI | Üretim sunucuda; web yalnız okur |
| 3 | Yeni breakpoint token'ı | KAPSAM İÇİ | `01_Abstracts` token kuralı |
| 4 | Electron pencere başlığı | KAPSAM DIŞI | K011/electron |
| 5 | Kullanıcı tema tercihi | KAPSAM İÇİ | `ThemeManager.js` |
| 6 | Veritabanı indeksi | KAPSAM DIŞI | K015/data |
| 7 | İstemci rota guard'ı | KAPSAM İÇİ | `router/GuardPipeline.js` |
| 8 | CDN cache TTL | KAPSAM DIŞI | K014-network |
| 9 | Toast bildirimi gösterimi | KAPSAM İÇİ | `ToastComponent.js` |
| 10 | Bildirim iş kuralı ("ne zaman") | KAPSAM DIŞI | K008-domain |
| 11 | WCAG kontrast düzeltmesi | KAPSAM İÇİ | UI + QA |
| 12 | API sürüm artışı | KAPSAM DIŞI | API alanı / K012 |

---

## §Q — Kapsam dışı senaryo detayları

| # | Senaryo | Neden dışı | İrtibat noktası |
|---|---|---|---|
| 1 | Yeni API ucu açmak | Sözleşme sunucu tarafı | Backend Architect |
| 2 | Şema migration | Veri sahipliği | Data Engineer |
| 3 | nginx yapılandırması | Sunucu katmanı | DevOps |
| 4 | Composer paketi ekleme | Bağımlılık yönetimi | dependency-manager |
| 5 | Kuyruk/worker | Arka plan servisi | K012-services |
| 6 | Firmware/DSP | Donanım | L0 katmanı |
| 7 | Ödeme akışı | Repo'da kod yok → PLANNED | K011/odem-servisleri |
| 8 | Cron tetikleyici | Zamanlama motoru | K011/zamanlamali-gorevler |

---

## §R — Referanslar (kapsam)

| # | Referans | Tür | Erişim |
|---|---|---|---|
| 1 | `web/index.md` | Klasör künyesi | Bu klasör |
| 2 | `web/web-bilesenler.md` | Envanter | Bu klasör |
| 3 | `web/web-dogrulama.md` | Kanıt | Bu klasör |
| 4 | `application-bagimliliklar.md §1` | Bağımlılık | Üst klasör |
| 5 | kök `AGENTS.md` §5 | Domain boundary | Repo kökü |
| 6 | `assets.coremusic.net/AGENTS.md` | Varlık kuralları | Alt alan |
| 7 | `.ai/.templates/frontend/css-template.md` | CSS şablonu | Vault |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
