---
title: "K011 · desktop — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: desktop
status: active
updated: 2026-10-15
group: mimari
---

# K011 · desktop — Kapsam

> **Amaç:** Bu dosya, K011-application içindeki `desktop` alt-düğümünün kapsamını (ne DEĞİL dahil)
> tanımlar; kademe-2 seviyesinde tasarım iddiası olarak taşır ve kademe-3 bounded-context kararını bekler.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 Amaç ve ELI15 | mimari | kapsam | Düğümün tek cümlelik tanımı |
| 2 | §2 Sınır — ne DEĞİL | mimari | sinir | Komşu K düğümlerine devir listesi |
| 3 | §3 Kapsam içi alan envanteri | mimari | alan | Desktop katmanının sahip olduğu yüzeyler |
| 4 | §4 Akış tablosu | mimari | akis | İstemci tespiti→BFF→yanıt zincirinin kapsamı |
| 5 | §5 Kapsam dışı devirler | mimari | dis | Sahiplik transferi kaydı |
| 6 | §6 Rol ve sorumluluk | mimari | rol | Kim bu kapsamda ne yapar |
| 7 | §7 Bağımlılık özeti | mimari | bagimlilik | Üst kademeye bağlılık |
| 8 | §8 Ölçüm ve kanıt referansı | kanit | olcum | Dosya/satır kanıtları |
| 9 | §9 Açık sorular | mimari | qa | Cevap bekleyen kapsam soruları |
| 10 | §A Cihaz bandı kapsam matrisi | mimari | band | desktop bandının diğer bantlarla kesişimi |
| 11 | §B Bileşen↔alan iz düşümü | mimari | bilesen | Hangi dosya hangi alana ait |
| 12 | §C İstemci tipi (BFF) kapsamı | mimari | bff | `client_type=desktop` sözleşmesinin sınırı |
| 13 | §D Kapsam SSS | mimari | sinir | Sık karıştırılan sınırlar |
| 14 | §E Değişiklik kaydı | kanit | kayit | Tarihçe ve kapı geçmişi |

---

## §1 — Amaç ve ELI15

**ELI15:** `desktop`, tarayıcının pencere içinden masaüstü gibi davranmasıyla ilgili her şeyin
kapsamıdır: geniş ekran bandının (1441–2560px) görünüm kuralları, masaüstü işletim sistemi
User-Agent'ının okunması, `client_type=desktop` istemcisine sunulan API yanıt biçimi ve
masaüstüne özgü cihaz CSS/JS eşleşmesi. Bu düğüm "masaüstünde ne görünüyor ve sunucu
masaüstü istemciye ne dönüyor" sorusunun cevabıdır.

**Kapsam cümlesi (tek satır):** Masaüstü cihaz bandı tespiti (viewport + User-Agent),
`desktop` istemci tipi için BFF yanıt transformasyonu, masaüstüne bağlanan cihaz CSS/JS
eş eşleşmesi ve bu yüzeyin test/kalite kapıları kapsar.

**Kapsamın kademe durumu:** `status: draft` — kademe-2 taslak kapsam. Kademe-3
(bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekler. Bu dosyadaki
satırlar **TASARIM İDDİASI**'dır; kanıt satırları `desktop-dogrulama.md` ve
`desktop-bilesenler.md` dosyalarındadır.

**Kanıt örneği (kapsamın gerçek karşılığı):**

```text
Dosya: shared/src/Api/Bff/DesktopBff.php · Satır: 41
$response['meta']['clientType'] = 'desktop';
```

Sunucu, `desktop` istemci tipine dönen yanıtın meta bloğunu bu satırda damgalar — yani
"masaüstü istemciye farklı yanıt" kapsamı gerçek kodda mevcuttur, varsayım değildir.

**İkinci kanıt (cihaz bandı):**

```text
Dosya: shared/src/Device/DeviceDetector.php · Satır: 15
 *   desktop    1441-2560px (varsayılan)
```

Breakpoint haritası dosya başlığında sabitlenmiştir; `desktop` bandı bu aralıktır.

---

## §2 — Sınır (ne DEĞİL)

| # | Yüzey | Bu kapsama girmez mi? | Sorumlu düğüm | Gerekçe |
|---|---|---|---|---|
| 1 | Electron paketleme, pencere kabuğu (OS seviyesi pencere/menü/dosya erişimi) | Hayır | K011/electron | OS kabuğu ≠ tarayıcı içeriği |
| 2 | Tarayıcı geneli SPA gezinme ve DOM davranışı | Hayır | K011/web | Web düğümü tarayıcı yüzeyinin tamamını taşır |
| 3 | Sunucu çalışma zamanı (PHP FPM, CLI, kurulum) | Hayır | K011/server-runtime · K011/local-runtime | Barındırma ≠ istemci bandı |
| 4 | API gateway sözleşmesi (DTO, sürümleme) | Hayır | K011/application-services | Sözleşme sunucu tarafındadır |
| 5 | İş kuralı kararı | Hayır | K008-domain | İş kuralı istemcide yaşamaz |
| 6 | Veritabanı şeması ve sorgular | Hayır | K015 / data alanı | Veri sahipliği |
| 7 | Cihaz kütüphanesinin tamamı (phone/tablet/embedded/4k) | Hayır | K011/web · cihaz katmanı | Bu düğüm yalnız `desktop` bandına sahiptir |
| 8 | Uygulama seviyesi cache politikası | Hayır | K011/caching | Cache politikası sunucudadır |
| 9 | Yönendirme çatısı (sunucu rota çözümü) | Hayır | K011/routing | Sunucu rota ≠ istemci bandı |
| 10 | Gerçek işletim sistemi API'leri (kayıt defteri, dosya sistemi, tray ikonu) | Hayır | ⚠️ VERIFICATION REQUIRED — repo'da karşılığı YOK | PLANNED boşluk (bkz. §9) |

**Sınır notu (kanıt):** `shared/src/Device/DeviceDetector.php` içindeki `isDesktopOS()`
yalnızca User-Agent metnini okur (`Windows NT` / `Macintosh` / `Linux` + `X11`); bu bir
OS API çağrısı değil, metin karşılaştırmasıdır — yani `desktop` düğümü işletim sistemine
**dokunmaz**, yalnız tarayıcının onun hakkındaki beyanını okur.

```text
Dosya: shared/src/Device/DeviceDetector.php · Satır: 232
return stripos($ua, 'Windows NT') !== false
```

---

## §3 — Kapsam içi alan envanteri

| # | Alan | Kapsam içi mi? | Kanıt dosyası | Not |
|---|---|---|---|---|
| 1 | Cihaz bandı tespiti (viewport 1441–2560) | Evet | `shared/src/Device/DeviceDetector.php` | `DESKTOP_MAX = 2560` |
| 2 | User-Agent masaüstü okuması (`isDesktopOS`) | Evet | `shared/src/Device/DeviceDetector.php` | Metin karşılaştırması |
| 3 | `desktop` istemci tipi BFF transformu | Evet | `shared/src/Api/Bff/DesktopBff.php` | `meta.clientType` damgası |
| 4 | İstemci tipi seçimi (header/query) | Evet | `shared/src/Api/Bff/BffLayer.php` | `X-Client-Type` → `client_type` |
| 5 | Masaüstü cihaz CSS eşleşmesi | Evet | `shared/src/Device/DeviceCssMap.php` | `08_Devices/d-desktop.css` |
| 6 | JS tarafı cihaz CSS haritası | Evet | `assets.coremusic.net/js/devices.config.js` | `HOME_CSS`/`AUTH_CSS` |
| 7 | Masaüstü layout konfigürasyonu | Evet | `assets.coremusic.net/js/device-layout-updater.js` | `DEVICE_LAYOUT.desktop` |
| 8 | Masaüstü auth arka planı | Evet | `assets.coremusic.net/js/auth/auth-gender-bg.js` | 2560 eşiği |
| 9 | Masaüstü device CSS paketi (import zinciri) | Evet | `assets.coremusic.net/Css/08_Devices/d-desktop.css` | 40+ import |
| 10 | Shell renderer cihaz beyanı | Evet | `shared/src/PageRouter/HtmlShellRenderer.php` | 7 cihazlı beyaz liste |
| 11 | Log şemasında cihaz tipi | Kısmen | `.ai/.sql/mysql/coremusic_logs.sql` | `device_type` enum içinde `desktop` |
| 12 | Breakpoint spec testi | Evet | `assets.coremusic.net/js/core/breakpoints.spec.js` | 1441/2560 testleri |
| 13 | OS kabuğu (tray, dosya seçici, otomatik güncelleme) | Hayır | — | PLANNED — §9 açık soru |
| 14 | Yerel SQLite masaüstü kuyruğu | Hayır | — | PLANNED — §9 açık soru |

---

## §4 — Akış tablosu

| # | Adım | Girdi | Çıktı | Kapsamda mı? | Kanıt |
|---|---|---|---|---|---|
| 1 | İstemci isteği gelir | `X-Client-Type: desktop` veya `?client_type=desktop` | `clientType = desktop` | Evet | `BffLayer.php` L69-81 |
| 2 | Varsayılan tespit | Header/query yok | `clientType = spa` | Evet | `BffLayer.php` L81 |
| 3 | BFF transformer kaydı | `register()` | `transformers['desktop']` | Evet | `BffLayer.php` L26-29 |
| 4 | Yanıt transformu | `apiResponse` | `meta.system` + `meta.clientType` | Evet | `DesktopBff.php` L34-44 |
| 5 | Cihaz bandı tespiti (sunucu) | UA + viewport | `desktop` (1441–2560) | Evet | `DeviceDetector.php` L124-126 |
| 6 | Viewport'suz tespit | Yalnız UA | `desktop` (varsayılan) | Evet | `DeviceDetector.php` L90-91 |
| 7 | CSS eşleşmesi | `desktop` | `08_Devices/d-desktop.css` | Evet | `DeviceCssMap.php` L13 |
| 8 | JS CSS eşleşmesi | `desktop` | `08_Devices/d-desktop.css` | Evet | `devices.config.js` L18 |
| 9 | Layout konfigürasyonu | `desktop` | `widgets:6, recentCards:7, ...` | Evet | `device-layout-updater.js` L67-78 |
| 10 | Shell'e cihaz beyanı | session cihazı | 7 elemanlı beyaz liste | Evet | `HtmlShellRenderer.php` L95 |
| 11 | OS pencere yönetimi (minimize/tray) | — | — | Hayır | PLANNED (repo'da YOK) |
| 12 | Yerel dosya erişimi | — | — | Hayır | PLANNED (repo'da YOK) |

---

## §5 — Kapsam dışı devirler

| # | Taşınan yüzey | Gittiği düğüm | Kaynak | Tarih |
|---|---|---|---|---|
| 1 | Electron pencere/app API'si | `K011/electron` | Bu düğüm hiçbir zaman OS kabuğuna sahip olmadı | 2026-10-15 |
| 2 | Tarayıcı DOM/gezinme | `K011/web` | `web-kapsam.md` §2 #7 ile çift yönlü sınır | 2026-10-15 |
| 3 | PWA/service worker kurulumu | `K011/web` | PWA havuzu web klasöründedir | 2026-10-15 |
| 4 | Sunucu CLI çalışma zamanı | `K011/local-runtime` | `bin/*.php` CLI scriptleri bu kapsama girmez | 2026-10-15 |
| 5 | Cihaz kütüphanesi (phone/tablet/4k...) | `K011/web` + cihaz katmanı | Bu düğüm yalnız `desktop` bandına sahiptir | 2026-10-15 |
| 6 | SQLite offline kuyruk (masaüstü) | ⚠️ VERIFICATION REQUIRED | Henüz düğüm atanmadı — §9 açık soru | 2026-10-15 |

---

## §6 — Rol ve sorumluluk

| # | Rol | Bu kapsamda ne yapar | Ne YAPMAZ | Kanıt |
|---|---|---|---|---|
| 1 | Backend Architect | `DesktopBff` transformu, `BffLayer` seçim mantığı | Cihaz bandı CSS'ini düzenlemez | `DesktopBff.php` L19 |
| 2 | Backend Architect | `DeviceDetector` desktop dalı | OS API çağırmaz | `DeviceDetector.php` L212-235 |
| 3 | UI Designer | `d-desktop.css` import zinciri + `DEVICE_LAYOUT.desktop` | Commit atmaz | `d-desktop.css` L8-50 |
| 4 | UI Designer | `devices.config.js` desktop eşleşmesi | `DeviceCssMap.php`'yi tek başına değiştirmez (eşzamanlı kural) | `devices.config.js` L18 |
| 5 | QA Engineer | Breakpoint spec (1441/2560 sınırları) | Kodu düzeltmez, bulguyu handover eder | `breakpoints.spec.js` L44-47 |
| 6 | Security Engineer | `X-Client-Type` header spoofing değerlendirmesi | BFF transformunu değiştirmez | `BffLayer.php` L69 |
| 7 | MO | Kapsam kapısı + commit | Bu dosyayı kendi başına kapatmaz (kademe-3 ADR bekler) | spec §1 |

---

## §7 — Bağımlılık özeti

| # | Bağımlılık | Yön | Tür | Not |
|---|---|---|---|---|
| 1 | `shared/` (Bff, Device, PageRouter) | Yukarı | Çalışma zamanı | Tümü ADR-084/ADR-021 kapsamı |
| 2 | `assets.coremusic.net/js` | Aşağı | Varlık | `devices.config.js` + `device-layout-updater.js` |
| 3 | `assets.coremusic.net/Css/08_Devices` | Aşağı | Varlık | `d-desktop.css` + `d-auth-desktop.css` |
| 4 | `K011/web` | Yan | Sınır | Tarayıcı yüzeyi ortak; band ayrımı bu düğümde |
| 5 | `K011/electron` | Yan | Sınır | OS kabuğu; bağımlılık henüz PLANNED |
| 6 | `K011/application-services` | Yukarı | Sözleşme | `client_type` değeri API sözleşmesinin parçası |
| 7 | ADR-084 (API Gateway / BFF) | Yukarı | Karar | `@see ADR-084` docblock'ta yazılı |
| 8 | ADR-097 (K4 fazı C) | Yukarı | Kapı | Kademe-3 genişletmesini bekler |

---

## §8 — Ölçüm ve kanıt referansı

| # | İddia | Dosya | Satır | Durum |
|---|---|---|---|---|
| 1 | `clientType` `desktop` olarak damgalanır | `shared/src/Api/Bff/DesktopBff.php` | 41 | OKUNDU |
| 2 | `getClientType()` `desktop` döner | `shared/src/Api/Bff/DesktopBff.php` | 52 | OKUNDU |
| 3 | Header `X-Client-Type` okunur | `shared/src/Api/Bff/BffLayer.php` | 69 | OKUNDU |
| 4 | Varsayılan istemci `spa`'dır | `shared/src/Api/Bff/BffLayer.php` | 81 | OKUNDU |
| 5 | `desktop` bandı 1441–2560px | `shared/src/Device/DeviceDetector.php` | 15, 124-126 | OKUNDU |
| 6 | `isDesktopOS` üç UA deseni okur | `shared/src/Device/DeviceDetector.php` | 232-234 | OKUNDU |
| 7 | CSS eşleşmesi `d-desktop.css` | `shared/src/Device/DeviceCssMap.php` | 13 | OKUNDU |
| 8 | JS haritası `desktop` → `d-desktop.css` | `assets.coremusic.net/js/devices.config.js` | 18 | OKUNDU |
| 9 | Layout `widgets:6, recentCards:7` | `assets.coremusic.net/js/device-layout-updater.js` | 67-78 | OKUNDU |
| 10 | Shell 7 cihazlı beyaz liste | `shared/src/PageRouter/HtmlShellRenderer.php` | 95 | OKUNDU |
| 11 | Test: 1920 genişlik `desktop` | `shared/tests/Unit/Device/DeviceDetectorTest.php` | 57 | OKUNDU |
| 12 | Spec testi 1441/2560 | `assets.coremusic.net/js/core/breakpoints.spec.js` | 44-47 | OKUNDU |
| 13 | Log şeması `device_type` enum `desktop` içerir | `.ai/.sql/mysql/coremusic_logs.sql` | 235, 346 | OKUNDU |
| 14 | OS kabuğu API'si repo'da YOK | — | — | ⚠️ PLANNED (§9) |

**Ölçüm notu:** `shared/src` toplamı 165 PHP dosyasıdır (glob + grep + recursive list üçü
uyuştu); `shared/tests` 45 dosyadır (spec 47 diyor → ⚠️ VERIFICATION REQUIRED);
`assets.coremusic.net/js/**` 94 dosyadır (spec 98 diyor → ⚠️ VERIFICATION REQUIRED).

---

## §9 — Açık sorular

| # | Soru | Etki | Sahip | Durum |
|---|---|---|---|---|
| 1 | `desktop` düğümü Electron'un paketleme katmanıyla nasıl sınırlandırılacak? | Kademe-3 | architect | AÇIK — ADR bekliyor |
| 2 | OS kabuğu (tray, dosya sistemi, otomatik güncelleme) repo'da hiç yok — PLANNED mi? | Kapsam genişliği | MO | AÇIK — spec §4 gereği boşluk kaydedildi |
| 3 | `X-Client-Type` header'ı istemci tarafından sahtelenebilir — sunucu güvenmeli mi? | Güvenlik | security-engineer | ⚠️ VERIFICATION REQUIRED (ADR-084 kapsamı dışında) |
| 4 | Masaüstü bandı 1920 ve 2560 arasında ikiye ayrılacak mı (2×2 widget grid kuralı)? | UI | ui-designer | AÇIK — kullanıcı kuralı §16.1 ile çelişip çelişmediği değerlendirilecek |
| 5 | Yerel SQLite offline kuyruk (`--sqlite` CLI seçeneği masaüstünde kullanılacak mı?) | local-runtime sınırı | backend-architect | AÇIK — düğüm sınırı belirsiz |
| 6 | `device_type` enum'u (`coremusic_logs.sql` L235) ile cihaz bandı sözlüğü neden farklı? | Veri | data-engineer | ⚠️ VERIFICATION REQUIRED |

---

## §A — Cihaz bandı kapsam matrisi

| Band | Aralıklar | Bu düğümde mi? | Kanıt |
|---|---|---|---|
| phone | ≤767px | Hayır — `K011/web` | `DeviceDetector.php` L21 |
| tablet | 768–1024px | Hayır — `K011/web` | `DeviceDetector.php` L22 |
| embedded | ≤1024px + yükseklik ≤600 | Hayır — embedded düğümü | `DeviceDetector.php` L23, L110-112 |
| laptop | 1025–1440px | Sınırda — komşu band | `DeviceDetector.php` L24 |
| **desktop** | **1441–2560px** | **EVET** | `DeviceDetector.php` L15, L124-126 |
| 4k-tv | 2561–3840px + TV UA | Hayır | `DeviceDetector.php` L26, L130-137 |
| 4k-monitor | ≥3841px | Hayır | `DeviceDetector.php` L140 |

**Kesişim notu:** 2560px hem `desktop` üst sınırı hem `4k-tv` alt sınırıdır; 2560'ın
`desktop` (≤), 2561'in `4k-tv` olduğu `DeviceDetector.php` L124-128'de okunur.

---

## §B — Bileşen↔alan iz düşümü

| Dosya | Alan | Tür |
|---|---|---|
| `shared/src/Api/Bff/DesktopBff.php` | BFF transformu | PHP |
| `shared/src/Api/Bff/BffLayer.php` | İstemci tipi seçimi | PHP |
| `shared/src/Device/DeviceDetector.php` | Band tespiti | PHP |
| `shared/src/Device/DeviceCssMap.php` | CSS eşleşmesi | PHP |
| `shared/src/Device/DeviceManager.php` | `DESKTOP` sabiti | PHP |
| `shared/src/PageRouter/HtmlShellRenderer.php` | Cihaz beyanı | PHP |
| `assets.coremusic.net/js/devices.config.js` | JS CSS haritası | JS |
| `assets.coremusic.net/js/device-layout-updater.js` | Layout konfigürasyonu | JS |
| `assets.coremusic.net/js/auth/auth-gender-bg.js` | Auth arka planı | JS |
| `assets.coremusic.net/Css/08_Devices/d-desktop.css` | Cihaz CSS paketi | CSS |
| `assets.coremusic.net/Css/08_Devices/d-auth-desktop.css` | Auth CSS paketi | CSS |
| `shared/tests/Unit/Device/DeviceDetectorTest.php` | Test | PHP |
| `assets.coremusic.net/js/core/breakpoints.spec.js` | Test | JS |
| `.ai/.sql/mysql/coremusic_logs.sql` | `device_type` enum | SQL |

---

## §C — İstemci tipi (BFF) kapsamı

| # | `client_type` değeri | Transformer | Bu kapsamda? |
|---|---|---|---|
| 1 | `desktop` | `DesktopBff` | EVET |
| 2 | `spa` | `SpaBff` (varsayılan) | Hayır — `K011/web` |
| 3 | `mobile` | `MobileBff` | Hayır |
| 4 | `embedded` | `EmbeddedBff` | Hayır |
| 5 | bilinmeyen | `spa`'ya düşer | Sınır durumu — gözlem altında |

**Sınır kanıtı:**

```text
Dosya: shared/src/Api/Bff/BffLayer.php · Satır: 40
$transformer = $this->transformers['spa'] ?? null;
```

Bilinmeyen istemci tipi `spa` transformer'ına düşer; `desktop` yalnız explicit bildirimde
(`X-Client-Type` veya `client_type`) seçilir.

---

## §D — Kapsam SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | `desktop` ≠ `electron` mı? | Evet. `desktop` tarayıcı içi band + BFF tipi; `electron` OS kabuğu ve paketleme (`K011/electron`). |
| 2 | `desktop` ≠ `web` mı? | Evet ama kesişirler: `web` tarayıcının tamamı, `desktop` yalnız 1441–2560 bandı ve `client_type=desktop` sözleşmesi. |
| 3 | Viewport yoksa `desktop` döner mi? | Evet — varsayılan `desktop` (`DeviceDetector.php` L90-91). |
| 4 | Header sahtelenirse ne olur? | Yanıt `desktop` transformundan geçer; güvenlik değerlendirmesi §9 #3. |
| 5 | Bu düğüm SQL'e sahip mi? | Hayır — yalnız `device_type` enum'unda `desktop` değeri geçer (sahiplik data katmanındadır). |

---

## §E — Değişiklik kaydı

| Tarih | Değişiklik | Kapı |
|---|---|---|
| 2026-10-12 | `desktop/index.md` ilk iskelet (kademe-2 subdomain) | İsim doğrulandı |
| 2026-10-15 | `desktop-kapsam.md` üretildi — 14 bölümlük kapsam | Satır ≥500 ölçülecek |

---

## §F — Kapsam genişletme matrisi (kademe-3 senaryoları)

| # | Senaryo | Kapsama girer mi? | Koşul | Bekleyen karar |
|---|---|---|---|---|
| 1 | Masaüstüne özel kısayol tuşları (kbd) | Evet | Band'a özgü davranış | ui-designer onayı |
| 2 | PWA install prompt (masaüstü) | Hayır | `K011/web` | — |
| 3 | `windowControlsOverlay` (PWA başlık çubuğu) | Hayır | `K011/web` (PWA havuzu) | — |
| 4 | Geolocation izni akışı | Hayır | `K011/web` | — |
| 5 | `getComputedStyle` ile bant ölçümü (JS) | Evet | Yalnız desktop bandı okuması | Test kapısı |
| 6 | Media query `min-width: 1441px` | Evet | `d-desktop.css` içinde | UI denetimi |
| 7 | Electron `BrowserWindow` | Hayır | `K011/electron` | — |
| 8 | Yerel dosya seçici (input type=file) | Kısmen | Tarayıcı içi ise `web`, OS diyalogu ise `electron` | Sınır ADR'si |
| 9 | Desktop BFF'e yeni meta alanı | Evet | `DesktopBff::transform()` içinde | ADR-084 |
| 10 | Tablet/laptop bandına desktop davranışı taşıma | Hayır | Band kirliliği — yasak | Red |

---

## §G — Kapsam içi davranış kuralları (tasarım iddiası)

| # | Kural | Kaynak | Kanıt |
|---|---|---|---|
| 1 | Viewport 1441–2560px ise cihaz `desktop`'tır | Breakpoint haritası | `DeviceDetector.php` L124-126 |
| 2 | Viewport verilmemişse varsayılan `desktop`'tır | Fonksiyon varsayılanı | `DeviceDetector.php` L90-91 |
| 3 | `X-Device-Type: embedded` header'ı band kararını ezer | Öncelik kuralı | `DeviceDetector.php` L62-65 |
| 4 | TV UA varsa viewport `desktop` bile olsa `4k-tv` dönülür | Öncelik kuralı | `DeviceDetector.php` L69-72 |
| 5 | Embedded UA varsa `embedded` dönülür | Öncelik kuralı | `DeviceDetector.php` L74-77 |
| 6 | 1024px genişlik + yükseklik ≤600 → `embedded` (RPi5) | Yükseklik kuralı | `DeviceDetector.php` L108-112 |
| 7 | 1024px + masaüstü OS → `laptop` | OS kuralı | `DeviceDetector.php` L113-117 |
| 8 | Bilinmeyen BFF tipi `spa`'ya düşer | Fallback | `BffLayer.php` L38-46 |
| 9 | `desktop` CSS haritası iki yerde senkron tutulur | SSOT kuralı | `DeviceCssMap.php` L13 + `devices.config.js` L18 |
| 10 | Layout konfigürasyonu bulunamazsa `desktop` config'i fallback'tir | JS fallback | `device-layout-updater.js` L309, L328, L366 |

**Kural 3–5 sıralama kanıtı (kod bloğu):**

```text
Dosya: shared/src/Device/DeviceDetector.php · Satır: 62
// 0. Özel HTTP başlığı veya kiosk bayrağı kontrolü
```

Öncelik sırası: özel header → TV UA → embedded UA → viewport → mobile UA → varsayılan desktop.

---

## §H — Kapsam dışı test senaryoları (olumsuz)

| # | Senaryo | Bu kapsamda? | Ne olmalı? |
|---|---|---|---|
| 1 | 1440px render | Hayır | `laptop` bandı — `web`/cihaz katmanı |
| 2 | 2561px render | Hayır | `4k-tv` bandı |
| 3 | UA sahtesiyle `desktop` zorlama | Kısmen | Band doğru döner; güvenlik ayrı değerlendirme (§9 #3) |
| 4 | `client_type=bogus` | Sınır | `spa` fallback (kapsam içi gözlem) |
| 5 | Electron penceresinde UA | Hayır | OS kabuğu `K011/electron` |
| 6 | 1024×600 RPi ekranı | Hayır | `embedded` |

---

## §I — Kaynak envanteri özeti (bu kapsamdaki dosyalar)

| Kümelenme | Dosya sayısı | Kanıt |
|---|---|---|
| PHP BFF (`Api/Bff/`) | 2 (`DesktopBff`, `BffLayer`) | glob `shared/src/Api/Bff/*.php` |
| PHP Device (`Device/`) | 3 dokunulan (`DeviceDetector`, `DeviceCssMap`, `DeviceManager`) | grep `desktop` |
| JS | 4 (`devices.config`, `device-layout-updater`, `auth-gender-bg`, `breakpoints.spec`) | grep `desktop` |
| CSS | 2 (`d-desktop.css`, `d-auth-desktop.css`) | glob `Css/08_Devices/d-desktop*` |
| Test (PHP) | 1+ (`DeviceDetectorTest`, `DeviceRendererTest`) | grep `desktop` tests/ |
| SQL | 1 referans (`coremusic_logs.sql` enum) | grep `device_type` |
| Toplam | **13 dokunulan yüzey** | §B tablosu |

---

## §J — ADR ve karar bağları

| # | ADR | İlişki | Durum |
|---|---|---|---|
| 1 | ADR-084 (API Gateway / BFF) | `DesktopBff` sınıfının docblock'ında `@see ADR-084` yazıyor | Aktif referans |
| 2 | ADR-021 (SPA Page Router) | `HtmlShellRenderer` cihaz beyanını bu katmanda yapar | Aktif referans |
| 3 | ADR-097 (K4 fazı C) | Kademe-3 bounded-context genişletmesini tetikler | BEKLİYOR |
| 4 | ADR-001 (Vanilla JS + ITCSS) | `d-desktop.css` ITCSS 08_Devices katmanındadır | Aktif |
| 5 | ADR-012 (CSP) | `X-Client-Type` header okuması CSP ile doğrudan ilgili değil; header spoofing ayrı | ⚠️ VERIFICATION REQUIRED |

**Kanıt (docblock):**

```text
Dosya: shared/src/Api/Bff/DesktopBff.php · Satır: 9
 * @see ADR-084-api-gateway-architecture
```

---

## §K — Kapsam riski tablosu

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | Band sözlüğü JS/PHP tarafında kayar | Orta | Yüksek — yanlış CSS | `devices.config.js` + `DeviceCssMap.php` eşzamanlı kural (assets AGENTS §4 #4) |
| 2 | `X-Client-Type` sahtelemesi | Orta | Orta — yanlış meta | §9 #3 değerlendirmesi |
| 3 | OS kabuğu kapsamı belirsiz büyüme | Yüksek | Orta — klasör şişmesi | §2 #10 sınırı + PLANNED disiplini |
| 4 | 2560 eşiği 4k ile çakışma | Düşük | Orta — band hatalı | `DeviceDetector.php` L124-128 sıralaması korunur |
| 5 | Spec/disk sayım farkı (47/45, 98/94) | Gerçekleşti | Düşük — rapor tutarsızlığı | ⚠️ işaretli kayıtlar (§8 notu) |

---

## §L — Kapsam kapısı kontrol listesi (bitişte)

| # | Kapı | Kriter | Kaynak |
|---|---|---|---|
| 1 | Bu dosya ≥500 satır | Ölçüm | spec §1.1 |
| 2 | Frontmatter 8 alan + `group:` son | Kontrol | spec §1.2 |
| 3 | `## §GRUPLAR` 14 satır | Kontrol | spec §1.3 |
| 4 | Footer 2026-10-15 | Kontrol | spec §1.4 |
| 5 | ≥1 `Dosya · Satır` kanıtı | §1, §2, §8, §J | spec §1.5 |
| 6 | Havuz-dışı URL 0 | Bu dosyada URL yok | spec §1.6 |
| 7 | `git commit` yok | Yok | spec §1.7 |

---

## §M — Kapsam — neyi İÇERMEZ ama komşu düğüm isterse devredilir (hazır paket)

| # | Aday yüzey | Taşınacağı an | Hedef düğüm | Hazır mı? |
|---|---|---|---|---|
| 1 | Service worker + cache | PWA kapsamı genişlerse | `K011/web` | Hayır — repo'da SW yok |
| 2 | `window.matchMedia` dinleyicileri | Bant canlı geçişi eklenirse | `K011/web` | Kısmen — `device-loader.js` ölçümleri var |
| 3 | Battery Status (`showBattery`) | Donanım bilgisi genişlerse | ⚠️ VERIFICATION REQUIRED | Konfigürasyon var (`device-layout-updater.js` L63), API okuması aranmalı |
| 4 | `navigator.language` tercihi | Yerelleşme eklenirse | `K011/web` | Hayır |
| 5 | Geolocation izin akışı | Konum özelliği eklenirse | `K011/web` | Hayır — kapsam dışı (havuz web klasöründe) |
| 6 | Tray / global kısayol | Electron olgunlaşırsa | `K011/electron` | Hayır — repo'da YOK |

---

## §N — Bağlam sözlüğü (bu dosyada geçen terimler)

| Terim | Tanım | Kanıt |
|---|---|---|
| Band (bant) | Viewport genişliğine göre cihaz sınıfı (ör. 1441–2560 = desktop) | `DeviceDetector.php` L10-17 |
| BFF | Backend for Frontend — istemci tipine göre yanıt transformu | `BffLayer.php` L17 |
| İstemci tipi | `X-Client-Type` / `client_type` ile bildirilen değer | `BffLayer.php` L66-81 |
| `isDesktopOS` | UA'da masaüstü işletim sistemi izi | `DeviceDetector.php` L212-235 |
| Cihaz beyanı | Session'daki cihazın shell'e yazılması | `HtmlShellRenderer.php` L95 |
| SSOT (cihaz) | `devices.config.js` tek JS kaynağı, `DeviceCssMap.php` tek PHP kaynağı | `devices.config.js` L3 |
| PLANNED | Repo'da karşılığı olmayan, hedeflenen yüzey | spec §4 |

---

## §O — Kapsam dışı komşu dosya referansları (okuma sırası)

| Sıra | Dosya | Ne verir |
|---|---|---|
| 1 | `web/web-kapsam.md` | Komşu sınır — neyi devraldığı |
| 2 | `electron/electron-kapsam.md` | OS kabuğu sınırı (varsa) |
| 3 | `application-bagimliliklar.md` §1 | Üst kademeye bağımlılık etiketleri |
| 4 | `application-kapsam.md` | Modül genelinin kapsamı |
| 5 | `.ai/.decisions/index.md` | ADR-084/097 tam metni (ihtiyaç anında) |

**Okuma kuralı:** Bu dosyalar yalnız ihtiyaç anında okunur; boot'ta toplu okuma yasaktır
(kök `AGENTS.md` §9).

---

## §P — Sınır kararları gerekçe tablosu

| # | Karar | Gerekçe | Alternatif (REDDEDİLDİ) |
|---|---|---|---|
| 1 | `desktop` ayrı bir K011 alt-düğümüdür | Hem band hem BFF tipi hem CSS eşleşmesi tek yerde toplanır | Band'ı `web`'e bırakmak → web-kapsam şişer |
| 2 | OS kabuğu kapsam dışı | Repo'da Electron kodu yok; uydurmak zero-hallucination'ı kırar | "Masaüstü = Electron" varsayımı |
| 3 | BFF tipi kapsam içi | `client_type=desktop` yanıt farkı doğrudan bu düğümün davranışı | BFF'i tamamen `application-services`'e bırakmak |
| 4 | Cihaz kütüphanesi kapsam dışı | phone/tablet/4k onlarca dosya; tek band temsil edilir | Tüm `08_Devices`'i almak |
| 5 | SQL yalnız referans | `device_type` enum sahipliği data katmanında | Tablo şemasını buraya taşımak |

---

## §Q — Kademe-3'e devredilecek açık maddeler (özet)

| # | Madde | Nereye | Etiket |
|---|---|---|---|
| 1 | Bounded-context sınırı (desktop ↔ web ↔ electron) | ADR-097 K4 faz (C) | AÇIK |
| 2 | `X-Client-Type` spoofing politikası | security-engineer değerlendirmesi | ⚠️ VERIFICATION REQUIRED |
| 3 | OS kabuğu PLANNED listesi | `K011/electron` olgunlaşınca | PLANNED |
| 4 | 1920/2560 widget grid ayrımı | Kullanıcı kuralı §16.1 + ui-designer | AÇIK |
| 5 | Spec↔disk sayım farkları (47/45, 98/94) | rapor notu — düzeltme üst ajana | ⚠️ VERIFICATION REQUIRED |

---

## §R — SSS (kapsam genişletilmiş)

| # | Soru | Cevap |
|---|---|---|
| 7 | `desktop` bandı yazın `DESKTOP_MAX` değişirse ne kırılır? | 6 dosya: `DeviceDetector`, JS breakpoint spec, `device-loader`, `auth-gender-bg`, `a-breakpoint-tokens.css`, ui-design cihaz matrisi. |
| 8 | Bu kapsamdaki dosyalar başka ajan tarafından düzenlenebilir mi? | Düzenleme onay ister; bu dosya salt kapsam kaydıdır. |
| 9 | `d-desktop.css` ITCSS'te hangi katmanda? | `08_Devices` (assets Css AGENTS §3.2). |
| 10 | Kademe-2'de kod yazılır mı? | Hayır — bu dosya taslak/kayıt; uygulama koduna yalnız onayla dokunulur. |
| 11 | `desktop` bandı hangi testlerle korunur? | `DeviceDetectorTest` (PHP) + `breakpoints.spec.js` (JS) — §8 satır 11-12. |
| 12 | Havuz URL'leri bu dosyada mı? | Hayır — 5 URL'lik havuz yalnız `desktop-web-arastirma.md` dosyasındadır (spec §1.6). |
| 13 | Kesişim kapısı nasıl ölçülür? | spec §5 PowerShell betiği — repo kökünden orkestratör koşar; bu klasörde havuz-dışı URL 0'dır. |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
