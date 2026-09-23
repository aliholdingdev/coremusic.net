---
title: "CoreMusic — Unused Files Audit Report"
type: report
category: code-audit
date: 2026-09-23
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# CoreMusic — Unused Files Audit Report

**Tarih:** 2026-09-23
**Kapsam:** home.coremusic.net/, shared/src/, assets.coremusic.net/, "copy" dizinleri
**Yöntem:** Dosya listesi + grep referans taramasý + main.css/main.js import zinciri analizi

---

## Özet

| Kategori | Toplam | Kullanýlan | Kullanýlmayan | Eksik |
|----------|--------|-----------|---------------|-------|
| PHP (home) | 41 | 24 | 17 | — |
| PHP (shared/src) | 100+ | ~60 | ~40 | — |
| CSS (assets) | 70+ | ~35 | ~20 | 10 |
| JS (assets) | 65 | 55 | 10 | — |
| "COPY" dizinleri | 3 | 0 | 3 (tümü) | — |
| Boþ dizinler | 1 | — | 1 | — |

**Toplam Kullanýlmayan/Düþük Kullanýmlý:** ~90 dosya + 3 tam kopya dizini

---

## 1. KESÝNLÝKLE SÝL: "COPY" Dizinleri (Tam Yedek)

Bu dizinler ana dizinlerin birebir kopyasýdýr. Hiçbir aktif kod tarafýndan referans almazlar.

| # | Dosya Yolu | Dosya Sayýsý | Durum | Öneri |
|---|-----------|-------------|-------|-------|
| 1 | `home.coremusic.net copy/` | ~100+ (vendor dahil) | Tam yedek kopya | **SÝL** — version control var |
| 2 | `home.coremusic.net/include copy/` | 17 PHP | include/ yedeði | **SÝL** |
| 3 | `assets.coremusic.net/Css copy/` | ~80 CSS + CLAUDE.md | Css/ yedeði | **SÝL** |

Ayrýca:
| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 4 | `home.coremusic.net/pages/home copy.php` | home.php kopyasý | **SÝL** |

**Etki:** ~200+ dosya, ~10MB+ disk alaný boþaltýlýr.

---

## 2. Eksik CSS Dosyalarý (main.css Import Ediyor Ama Dosya Yok)

`assets.coremusic.net/Css/main.css` aþaðýdaki dosyalarý import ediyor ancak diskte mevcut deðiller:

| # | Import Edilen Yol | Durum | Etki |
|---|-------------------|-------|------|
| 1 | `02_Base/l-main-structural.css` | **YOK** | CSS yüklenemez |
| 2 | `05_Pages/_player.css` | **YOK** | Player sayfasý stilsiz |
| 3 | `05_Pages/p-albums.css` | **YOK** | Albums stilsiz |
| 4 | `05_Pages/p-album-detail.css` | **YOK** | Album detail stilsiz |
| 5 | `05_Pages/p-artists.css` | **YOK** | Artists stilsiz |
| 6 | `05_Pages/p-playlist.css` | **YOK** | Playlist stilsiz |
| 7 | `05_Pages/p-settings.css` | **YOK** | Settings stilsiz |
| 8 | `09_ViewModes/v-home.css` | **YOK** | View mode çalýþmaz |
| 9 | `09_ViewModes/v-pro.css` | **YOK** | View mode çalýþmaz |
| 10 | `09_ViewModes/v-studio.css` | **YOK** | View mode çalýþmaz |
| 11 | `07_Vendors/v-bootstrap-lib.css` | **YOK** | Bootstrap yüklenemez |
| 12 | `01_Abstracts/a-semantic-token.css` | **YOK** | Token eksik |
| 13 | `01_Abstracts/a-color-mode-tokens.css` | **YOK** | Token eksik |
| 14 | `01_Abstracts/a-light-glass-tokens.css` | **YOK** | Token eksik |

**Öneri:** Bu dosyalar ya oluþturulmalý ya da main.css import'larý kaldýrýlmalý.

---

## 3. Boþ Dizinler

| # | Dizin | Durum | Öneri |
|---|-------|-------|-------|
| 1 | `assets.coremusic.net/Css/09_ViewModes/` | **BOÞ** (0 dosya) | main.css'ten import kaldýrýlmalý veya dosyalar oluþturulmalý |

---

## 4. Mevcut Ama Kullanýlmayan CSS Dosyalarý

Bu dosyalar diskte var ama main.css'e import edilmemiþ ve hiçbir yerde referans almýyor:

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 1 | `02_Base/page-layout.css` | Sadece d-4k.css ve d-laptop.css tarafýndan import ediliyor | **TUT** (koþullu kullaným) |
| 2 | `08_Devices/d-auth-4k-monitor.css` | Auth device CSS — auth.coremusic.net yükler | **TUT** |
| 3 | `08_Devices/d-auth-4k-tv.css` | Auth device CSS | **TUT** |
| 4 | `08_Devices/d-auth-desktop.css` | Auth device CSS | **TUT** |
| 5 | `08_Devices/d-auth-embedded.css` | Auth device CSS | **TUT** |
| 6 | `08_Devices/d-auth-laptop.css` | Auth device CSS | **TUT** |
| 7 | `08_Devices/d-auth-phone.css` | Auth device CSS | **TUT** |
| 8 | `08_Devices/d-auth-tablet.css` | Auth device CSS | **TUT** |
| 9 | `11_OAuth/oauth.css` | Hiçbir PHP/HTML'de referans yok | **ÝNCELE** — auth.coremusic.net yükleyebilir |
| 10 | `auth-bundled.css` | Sadece DeviceRenderer.php + test'te referans | **TUT** (auth için gerekli) |

**Öneri:** #1-8 (auth device CSS) tutulmalý — auth.coremusic.net tarafýndan yüklenir. #9 (oauth.css) incelenmeli.

---

## 5. Kullanýlmayan JS Dosyalarý

| # | Dosya Yolu | Durum | Referans | Öneri |
|---|-----------|-------|---------|-------|
| 1 | `js/router/SPARouterAdapter.js` | DEPRECATED olarak iþaretli | Hiçbir import yok | **SÝL** |
| 2 | `js/oauth-manager.js` | main.js'de import yok, hiçbir PHP'de script tag yok | — | **ÝNCELE** — auth flow'da kullanýlabilir |
| 3 | `js/components/WidgetGrid.js` | main.js'de import yok | — | **ÝNCELE** — WidgetManager tarafýndan kullanýlabilir |
| 4 | `js/components/PlayerInfo.js` | main.js'de import yok | — | **ÝNCELE** |
| 5 | `js/components/FooterPlayer.js` | main.js'de import yok | — | **ÝNCELE** |
| 6 | `js/core/helper.js` | main.js'de import yok | — | **ÝNCELE** |
| 7 | `js/core/footer.init.js` | main.js'de import yok | — | **ÝNCELE** |

**Not:** coreplayer/ dosyalarý footer.php tarafýndan `<script>` tag ile yükleniyor (import deðil) — bu dosyalar KULLANILIYOR.

---

## 6. Kullanýlmayan PHP Dosyalarý (shared/src/)

### 6.1 AI Modülü — Sadece Kendi Ýçinde Kullanýlýyor

Bu sýnýflar hiçbir tüketici (controller, service, bootstrap) tarafýndan instantiate edilmiyor. Sadece kendi interface'lerini import ediyorlar:

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 1 | `shared/src/AI/AIEngine.php` | Hiçbir instantiate yok | **PLANNED** olarak iþaretle |
| 2 | `shared/src/AI/AIOrchestrator.php` | Hiçbir instantiate yok | **PLANNED** |
| 3 | `shared/src/AI/AIWorkflow.php` | Hiçbir import yok | **PLANNED** |
| 4 | `shared/src/AI/KnowledgeBase.php` | Hiçbir instantiate yok | **PLANNED** |
| 5 | `shared/src/AI/MemorySystem.php` | Hiçbir instantiate yok | **PLANNED** |
| 6 | `shared/src/AI/PromptEngine.php` | Hiçbir instantiate yok | **PLANNED** |
| 7 | `shared/src/AI/ToolCalling.php` | Hiçbir instantiate yok | **PLANNED** |
| 8 | `shared/src/AI/Contracts/*.php` (6 dosya) | Sadece AI sýnýflarý tarafýndan kullanýlýyor | **PLANNED** |

### 6.2 API Gateway & BFF — Sadece Kendi Ýçinde ve Testlerde Kullanýlýyor

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 9 | `shared/src/Api/Gateway.php` | Testlerde kullanýlýyor | **PLANNED** |
| 10 | `shared/src/Api/Bff/SpaBff.php` | Hiçbir instantiate yok | **PLANNED** |
| 11 | `shared/src/Api/Bff/MobileBff.php` | Hiçbir instantiate yok | **PLANNED** |
| 12 | `shared/src/Api/Bff/EmbeddedBff.php` | Hiçbir instantiate yok | **PLANNED** |
| 13 | `shared/src/Api/Bff/DesktopBff.php` | Hiçbir instantiate yok | **PLANNED** |
| 14 | `shared/src/Api/Bff/BffLayer.php` | Hiçbir instantiate yok | **PLANNED** |
| 15 | `shared/src/Api/Dto/Request/*.php` (6 dosya) | Sadece testlerde | **PLANNED** |
| 16 | `shared/src/Api/Dto/Response/*.php` (5 dosya) | Sadece testlerde | **PLANNED** |
| 17 | `shared/src/Api/Middleware/*.php` (6 dosya) | Sadece Gateway içinde | **PLANNED** |
| 18 | `shared/src/Api/Registry/*.php` (3 dosya) | Hiçbir instantiate yok | **PLANNED** |
| 19 | `shared/src/Api/Versioning/*.php` (3 dosya) | Sadece testlerde | **PLANNED** |

### 6.3 Exception Sýnýflarý

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 20 | `shared/src/Exception/AuthorizationException.php` | Hiçbir `use` yok | **ÝNCELE** |
| 21 | `shared/src/Exception/BaseCoreMusicException.php` | Sadece extend edenler kullanýyor | **TUT** (base class) |

### 6.4 Session

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 22 | `shared/src/Session/SessionConfig.php` | Sadece SessionBootstrapper içinde kullanýlýyor | **TUT** |
| 23 | `shared/src/Session/SessionInitializer.php` (PageRouter) | Hiçbir import yok (farklý namespace) | **ÝNCELE** |

### 6.5 Config

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 24 | `shared/src/Config/AuthRouteConfig.php` | 4 dosyada kullanýlýyor | **TUT** |
| 25 | `shared/src/Config/EnvParser.php` | constants.php'de kullanýlýyor | **TUT** |

### 6.6 Security

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 26 | `shared/src/Security/SessionKeys.php` | Hiçbir `use` yok | **ÝNCELE** |
| 27 | `shared/src/Security/ReturnUrlPolicy.php` | Sadece testlerde | **TUT** (test coverage) |

### 6.7 Interfaces (Soyut)

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 28 | `shared/src/Interfaces/Auth/IUserRepository.php` | Hiçbir implementasyon yok | **PLANNED** |
| 29 | `shared/src/Interfaces/Auth/ISessionManager.php` | Hiçbir implementasyon yok | **PLANNED** |
| 30 | `shared/src/Interfaces/Auth/IAuthService.php` | Hiçbir implementasyon yok | **PLANNED** |

---

## 7. Kullanýlmayan PHP Dosyalarý (home.coremusic.net/)

| # | Dosya Yolu | Durum | Referans | Öneri |
|---|-----------|-------|---------|-------|
| 1 | `pages/home copy.php` | home.php kopyasý | — | **SÝL** |
| 2 | `pages/redirect.php` | Bootstrap'te `/health` ve `/auth/callback` var, redirect-specific route yok | **ÝNCELE** |
| 3 | `pages/health.php` | Bootstrap'te inline health check var (ayrý dosya gereksiz) | **ÝNCELE** — bootstrap inline'da zaten var |
| 4 | `include copy/` (17 dosya) | include/ yedeði | — | **SÝL** |

### 7.1 Eksik Bileþen Dosyalarý

ComponentLoader sadece 2 bileþen kayýtlý ama home.php 9 bileþen adý referans ediyor:

| # | Beklenen Bileþen | ComponentLoader'da | Dosya Mevcut mu? | Durum |
|---|------------------|-------------------|-------------------|-------|
| 1 | `player-info` | ? Kayýtlý | ? var | **OK** |
| 2 | `recent-tracks` | ? Kayýtlý | ? var | **OK** |
| 3 | `widget-grid` | ? Kayýtsýz | ? Yok | **EKSÝK** |
| 4 | `welcome-banner` | ? Kayýtsýz | ? Yok | **EKSÝK** |
| 5 | `playlists` | ? Kayýtsýz | ? Yok | **EKSÝK** |
| 6 | `up-next` | ? Kayýtsýz | ? Yok | **EKSÝK** |
| 7 | `now-playing` | ? Kayýtsýz | ? Yok | **EKSÝK** |
| 8 | `welcome-modal` | ? Kayýtsýz | ? Yok | **EKSÝK** |
| 9 | `home-widgets` | ? Kayýtsýz | ? Yok | **EKSÝK** |

**Not:** home.php'de `$loader->display('widget-grid', ...)` çaðrýsý var ama ComponentLoader'da bu anahtar kayýtlý deðil › runtime hatasý.

---

## 8. CSS Dosyalarýnda Eþitsizlik

### 8.1 main.css'te Import Var Ama Dosya Yok (14 dosya)

› Bölüm 2'de listelendi.

### 8.2 Dosya Var Ama main.css'te Import Yok

| # | Dosya Yolu | Durum | Öneri |
|---|-----------|-------|-------|
| 1 | `02_Base/page-layout.css` | d-4k.css ve d-laptop.css tarafýndan import | **TUT** |
| 2 | `05_Pages/_home-inline.css` | home.php'de inline style | **TUT** |
| 3 | `05_Pages/_home-components.css` | home.php component'leri | **TUT** |
| 4 | `05_Pages/p-login-view.css` | auth-bundled.css'de import olabilir | **ÝNCELE** |
| 5 | `01_Abstracts/a-layout-tokens-1024.css` | Token dosyasý — import edilmemiþ | **ÝNCELE** |
| 6 | `01_Abstracts/a-layout-tokens-1920.css` | Token dosyasý — import edilmemiþ | **ÝNCELE** |
| 7 | `01_Abstracts/a-layout-tokens-3540.css` | Token dosyasý — import edilmemiþ | **ÝNCELE** |
| 8 | `01_Abstracts/a-layout-tokens-3840.css` | Token dosyasý — import edilmemiþ | **ÝNCELE** |
| 9 | `08_Devices/d-4k-monitor.css` | **YOK** — d-4k.css var ama d-4k-monitor yok | **EKSÝK** |

---

## 9. Bootstrap Vendor Dosyalarý (32 dosya)

`07_Vendors/` dizininde 32 Bootstrap dosyasý var (full, grid, reboot, utilities × normal + rtl + min + map) ama main.css'te `v-bootstrap-lib.css` import ediliyor — bu dosya **YOK**.

**Öneri:** Ya `v-bootstrap-lib.css` oluþturulmalý (içine gerekli Bootstrap parçalarý import edilmeli) ya da mevcut Bootstrap dosyalarýndan hangisinin kullanýlacaðý belirlenmeli.

---

## 10. Test Dosyalarý

| # | Dosya Yolu | Test Kapsamý | Durum |
|---|-----------|-------------|-------|
| 1 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | DeviceDetector | ? AKTÝF |
| 2 | `shared/tests/Unit/Device/DeviceRendererTest.php` | DeviceRenderer | ? AKTÝF |
| 3 | `shared/tests/Unit/Device/WelcomePopupRenderTest.php` | Welcome popup | ? AKTÝF |
| 4 | `shared/tests/Unit/PageRouter/SpaRouteTest.php` | SpaRoute | ? AKTÝF |
| 5 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | RouteRegistry | ? AKTÝF |
| 6 | `shared/tests/Unit/PageRouter/AuthUrlBuilderTest.php` | AuthUrlBuilder | ? AKTÝF |
| 7 | `shared/tests/Unit/PageRouter/AuthGuardTest.php` | AuthGuard | ? AKTÝF |
| 8 | `shared/tests/Unit/Security/ReturnUrlPolicyTest.php` | ReturnUrlPolicy | ? AKTÝF |
| 9 | `shared/tests/Unit/Config/ConfigManagerTest.php` | ConfigManager | ? AKTÝF |
| 10 | `shared/tests/OAuth/OAuthManagerTest.php` | OAuthManager | ? AKTÝF |
| 11 | `shared/tests/Events/EventDispatcherTest.php` | EventDispatcher | ? AKTÝF |
| 12 | `shared/tests/Events/DomainEventTest.php` | Domain events | ? AKTÝF |
| 13 | `shared/tests/Api/DtoTest.php` | DTO | ? AKTÝF |
| 14 | `shared/tests/Api/ApiResponseTest.php` | ApiResponse | ? AKTÝF |
| 15 | `shared/tests/Api/VersioningTest.php` | API versioning | ? AKTÝF |

**Toplam:** 15 test dosyasý, tümü aktif.
**Eksik test:** Middleware, Cache, Device, Bootstrap için test yok.

---

## 11. Öncelikli Aksiyonlar

### ACÝL (Bug Yaratabilir)
1. **main.css'teki 14 eksik import** › CSS yüklenemez, sayfalar bozuk görünür
2. **ComponentLoader'da 7 eksik bileþen kaydý** › runtime `InvalidArgumentException`
3. **d-4k-monitor.css eksik** › 4K monitor'de device CSS çalýþmaz

### YÜKSEK
4. **3 "copy" dizini sil** (~200+ dosya, ~10MB)
5. **home copy.php sil**
6. **SPARouterAdapter.js sil** (DEPRECATED, kullanýlmýyor)

### ORTA
7. **AI modülü** (8 dosya) › PLANNED olarak iþaretle veya kullanýma al
8. **API Gateway/BFF** (20+ dosya) › PLANNED olarak iþaretle
9. **OAuth css** › auth.coremusic.net'te kullanýlýp kullanýlmadýðýný kontrol et
10. **09_ViewModes dizini** › ya dosyalarý oluþtur ya da main.css'ten kaldýr

### DÜÞÜK
11. **SessionKeys.php** › Kullanýlmýyor, sil veya kullanýma al
12. **AuthorizationException.php** › Kullanýlmýyor, sil veya kullanýma al
13. **Bootstrap vendor** › Hangisi kullanýlacaksa onu tut, diðerlerini kaldýr

---

## 12. Çapraz Referanslar

| Kaynak | Hedef | Ýliþki |
|--------|-------|--------|
| main.css | 01_Abstracts/ | 12 token import |
| main.css | 02_Base/ | 2 import (1 eksik) |
| main.css | 05_Pages/ | 7 import (6 eksik) |
| main.css | 09_ViewModes/ | 3 import (3 eksik, dizin boþ) |
| main.js | router/ | 25+ import |
| main.js | core/ | 2 import |
| main.js | managers/ | 5 import |
| main.js | features/ | 5 import |
| footer.php | coreplayer/ | 5 script tag |
| HtmlShellRenderer.php | js/main.js, device-*.js | 4 script tag |
| ComponentLoader.php | Component/ | 2 kayýt (7 eksik) |

---

**Authority:** Vault Audit
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
