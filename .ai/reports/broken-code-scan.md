---
title: "CoreMusic — Broken, Unused & Dead Code Scan Report"
type: report
category: code-quality
date: 2026-09-23
status: active
version: 1.0.0
authority: reference
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Broken, Unused & Dead Code Scan Report

**Tarama Tarihi:** 2026-09-23
**Tarama Alanları:** home.coremusic.net/, auth.coremusic.net/, shared/src/
**Toplam Bulgu:** 18 (CRITICAL: 2, HIGH: 6, MEDIUM: 7, LOW: 3)

---

## Özet

| Öncelik | Sayı | Kategori |
|---------|------|----------|
| CRITICAL | 2 | Güvenlik (SSRF), Yapısal Hata (orphaned HTML) |
| HIGH | 6 | Dead Code, Duplicate Class, Kırık Referans, Eksik Docblock |
| MEDIUM | 7 | Unused Import, Unused Constant, Potansiyel Hata |
| LOW | 3 | Debug Header, Minör Kod Kokusu |

---

## CRITICAL Bulgular

### C1. SSRF — `file_get_contents` ile Doğrulanmamış HTTP İsteği
- **Dosya:** `home.coremusic.net/config/constants.php` satır 41
- **Sorun:** `@file_get_contents($authConfigUrl)` ile auth.coremusic.net'e HTTP isteği atılıyor. URL `.env`'den gelen `AUTH_URL` sabitinden türetiliyor. Saldirgan `AUTH_URL`'yi değiştirirse veya DNS rebinding yaparsa, iç ağdaki services'e erişebilir.
- **Kod:**
  ```php
  $authConfigUrl = (defined('AUTH_URL') ? AUTH_URL : 'http://auth.coremusic.net') . '/bypass-status';
  $authConfigResponse = @file_get_contents($authConfigUrl);
  ```
- **Önerilen Çözüm:** Bu mekanizmayı kaldırın. `FORCE_AUTH_BYPASS` zaten `auth.coremusic.net/config/constants.php`'de `.env`'den okunuyor. Home tarafı bunu HTTP ile çekmek yerine kendi `.env`'sinde tanımlamalı veya `SharedBootstrap` üzerinden okumalı. `@` error suppression operatörü de hata gizlediği için tehlikelidir.
- **Öncelik:** CRITICAL

### C2. Orphaned `</div>` Tag — Yapısal HTML Hatası
- **Dosya:** `home.coremusic.net/pages/home.php` satır 125-127
- **Sorun:** Embedded layout bölümünde `</div>` tag'i içeriği olmayan bir div'i kapatıyor. Bu, tarayıcıda beklenmeyen DOM yapısına yol açar.
- **Kod:**
  ```php
  <div class="home-layout__bottom home-layout__bottom--embedded">
      </div>  <!-- satır 126: boş div kapatıldı -->
  </div>      <!-- satır 127: fazladan kapatma -->
  ```
- **Önerilen Çözüm:** Boş div'i kaldırın veya içeriğini doldurun. Mevcut durumda `recent-tracks` ve `playlists` component'leri render edilmiyor (empty block).
- **Öncelik:** CRITICAL

---

## HIGH Bulgular

### H1. Duplicate `SessionInitializer` Sınıfı — Dead Code
- **Dosyalar:**
  - `shared/src/PageRouter/SessionInitializer.php` — `CoreMusic\PageRouter\SessionInitializer` (134 satır)
  - `shared/src/Session/SessionInitializer.php` — `CoreMusic\Session\SessionInitializer` (52 satır)
- **Sorun:** İki ayrı `SessionInitializer` sınıfı var. `SessionLifecycle.php` satır 13'te "'CoreMusic\PageRouter\SessionInitializer'dan taşındı" diyor. PageRouter versiyonu hiçbir yerde `use` edilmiyor (grep sonucu: 0 import). Bu tamamen dead code.
- **Önerilen Çözüm:** `shared/src/PageRouter/SessionInitializer.php` dosyasını silin. `Session\SessionInitializer` aktif versiyondur.
- **Öncelik:** HIGH

### H2. Duplicate `require_once autoload.php` — Çift Yükleme
- **Dosya:** `home.coremusic.net/index.php` satır 7 ve 20
- **Sorun:** `autoload.php` dosyası iki kez `require_once` ile çağrılıyor. `require_once` sayesinde çalışma zamanında hata çıkarmaz ama gereksiz dosya okuma ve include_path araması yaptırır.
- **Kod:**
  ```php
  require_once __DIR__ . '/autoload.php';  // satır 7
  // ... 12 satır kod ...
  require_once __DIR__ . '/autoload.php';  // satır 20
  ```
- **Önerilen Çözüm:** Satır 7'deki debug header bloğunu (`autoload.php` çağrısı + ReflectionClass) ya tamamen kaldırın ya daautoload'u satır 20'ye taşıyın.
- **Öncelik:** HIGH

### H3. Unused `use ComponentInterface` Import — Dead Import
- **Dosya:** `home.coremusic.net/include/Class/HomeLayoutVariant.php` satır 5
- **Sorun:** `use CoreMusic\Home\Interfaces\ComponentInterface;` import edilmiş ancak dosyada hiçbir yerde kullanılmıyor. Enum sınıfları interface uygulamaz.
- **Önerilen Çözüm:** Satır 5'i silin.
- **Öncelik:** HIGH

### H4. Unused `use ComponentInterface` Import — Dead Import
- **Dosya:** `home.coremusic.net/include/Component/PlayerInfoComponent.php` satır 7
- **Sorun:** `use CoreMusic\Home\Interfaces\ComponentInterface;` import edilmiş ancak sınıf `AbstractComponent`'i extend ediyor (ki o zaten `ComponentInterface`'i implement ediyor). Import kullanılmıyor.
- **Önerilen Çözüm:** Satır 7'yi silin.
- **Öncelik:** HIGH

### H5. Eksik Docblock — Incomplete Method Documentation
- **Dosya:** `home.coremusic.net/include/Class/AbstractComponent.php` satır 91-92
- **Sorun:** Docblock bitmiş ama method tanımı yok. Satır 89'da `@return array{width: int|float, height: int|float}` yazıyor ama fonksiyon imzası eksik. Bu muhtemelen `size()` methodu için planlanmıştı ama implemente edilmemiş.
- **Kod:**
  ```php
  /**
   * Cihaz bazlı boyut döndür
   * @param string $key — Token adı (ör: 'player', 'cover', 'footer')
   * @return array{width: int|float, height: int|float}
   */          // <-- fonksiyon imzası yok!

  /**
   * CSS class döndür (tier'a göre)
   ```
- **Önerilen Çözüm:** Ya `size()` methodunu implemente edin ya da eksik docblock'u kaldırın.
- **Öncelik:** HIGH

### H6. Kırık HTML Attribute — Unclosed Quote
- **Dosya:** `home.coremusic.net/pages/components/player-info.php` satır 13
- **Sorun:** `alt` attribute'unda kapanmamış tırnak işareti var: `alt="Çalan şarkının albüm kapağı loading="lazy"/>`. Bu, HTML parser'ın `loading="lazy"`'yi alt metni olarak algılamasına yol açar.
- **Kod:**
  ```html
  <img src="..." alt="Çalan şarkının albüm kapağı loading="lazy"/>
  ```
- **Önerilen Çözüm:**
  ```html
  <img src="..." alt="Çalan şarkının albüm kapağı" loading="lazy"/>
  ```
- **Öncelik:** HIGH

---

## MEDIUM Bulgular

### M1. `FORCE_AUTH_BYPASS` Tanımlı Ama Kullanılmıyor (auth.coremusic.net)
- **Dosya:** `auth.coremusic.net/config/constants.php` satır 73
- **Sorun:** `FORCE_AUTH_BYPASS` sabiti tanımlanmış ve `app.php`'de array'e eklenmiş, `/bypass-status` endpoint'inde de döndürülüyor. Ancak auth.coremusic.net içinde hiçbir middleware veya handler bu sabiti okuyarak auth bypass uygulamıyor. Değer her zaman `false` (`.env`'de tanımsızsa). Bypass行为 `BypassAuthMiddleware` (shared/src/) tarafından kontrol ediliyor, ama o da `TEST_MODE`'a bakıyor, `FORCE_AUTH_BYPASS`'a değil.
- **Önerilen Çözüm:** Ya `BypassAuthMiddleware`'e `FORCE_AUTH_BYPASS` kontrolü ekleyin ya da gereksiz sabiti kaldırın.
- **Öncelik:** MEDIUM

### M2. Unused AI Sınıfları — Dead Code (6 dosya)
- **Dosyalar:**
  - `shared/src/AI/AIEngine.php`
  - `shared/src/AI/AIOrchestrator.php`
  - `shared/src/AI/KnowledgeBase.php`
  - `shared/src/AI/MemorySystem.php`
  - `shared/src/AI/PromptEngine.php`
  - `shared/src/AI/ToolCalling.php`
  - `shared/src/AI/AIWorkflow.php`
  - `shared/src/AI/Contracts/` (6 interface)
- **Sorun:** Hiçbir PHP dosyası bu sınıfları `use` etmiyor (grep sonucu: 0 import, sadece kendi içlerindeki import'lar). Tüm AI katmanı PLANNED durumunda ve hiçbir tüketici yok.
- **Önerilen Çözüm:** Bu dosyaları şimdilik koruyun ama `CLAUDE.md`'de "PLANNED — no consumers" olarak işaretleyin. Veya `shared/src/AI/` dizinini tamamen PLANNED klasörüne taşıyın.
- **Öncelik:** MEDIUM

### M3. Unused Event Sınıfları — Dead Code (9 domain + 3 integration)
- **Dosyalar:**
  - `shared/src/Events/Domain/` (9 event sınıfı)
  - `shared/src/Events/Integration/` (3 event sınıfı)
  - `shared/src/Events/EventDispatcher.php`
  - `shared/src/Events/StoppableEventTrait.php`
- **Sorun:** Event sınıfları sadece kendi test dosyalarında kullanılıyor. Production kodunda hiçbir yerde `dispatch()` veya `addEventListener()` çağrısı yok. PSR-14 EventDispatcher implemente edilmiş ama hiçbir yerde tetiklenmiyor.
- **Önerilen Çözüm:** Test korunmalı, production'a geçiş planlanmalı. `ADR-086` (Event Driven Architecture) aktif ama henüz entegre edilmemiş.
- **Öncelik:** MEDIUM

### M4. Unused API Gateway/BFF Sınıfları — Dead Code
- **Dosyalar:**
  - `shared/src/Api/Gateway.php`
  - `shared/src/Api/ApiRequest.php`, `ApiResponse.php`
  - `shared/src/Api/Bff/` (BffLayer + 4 BFF sınıfı)
  - `shared/src/Api/Registry/` (ServiceRegistry, ServiceHealth, ServiceDefinition)
  - `shared/src/Api/Versioning/` (3 sınıf)
  - `shared/src/Api/Middleware/` (4 middleware)
  - `shared/src/Api/Dto/` (request/response DTO'ları)
- **Sorun:** API Gateway ve BFF sınıfları hiçbir entry point tarafından kullanılmıyor. Sadece kendi test dosyalarında import ediliyorlar. `ADR-084` (API Gateway Architecture) aktif ama henüz hiçbir panel tarafından tüketilmiyor.
- **Önerilen Çözüm:** PLANNED olarak işaretleyin. Gateway entegrasyonu `music.coremusic.net` paneli ile başlamalı.
- **Öncelik:** MEDIUM

### M5. Unused Cache Sınıfları — Dead Code
- **Dosyalar:**
  - `shared/src/Cache/MemoryAdapter.php`
  - `shared/src/Cache/ApcuAdapter.php`
  - `shared/src/Cache/PageCacheInterface.php`
- **Sorun:** `CacheManager` sadece `RateLimiterMiddleware` ve `AuthContainer` tarafından kullanılıyor. `MemoryAdapter` ve `ApcuAdapter` hiçbir yerde instantiate edilmiyor. `PageCacheAdapter` sadece auth.coremusic.net index.php'de kullanılıyor.
- **Önerilen Çözüm:** Cache adapter'larını `CacheManager` factory'ye bağlayın veya kullanılmayanları kaldırın.
- **Öncelik:** MEDIUM

### M6. `home.php` Satır 133-134: Boş if Bloğu
- **Dosya:** `home.coremusic.net/pages/home.php` satır 133-134
- **Sorun:** `if (!$dm->isPhone())` bloğu boş. Hiçbir HTML veya JS içermiyor.
- **Kod:**
  ```php
  <?php if (!$dm->isPhone()): ?>
  <?php endif; ?>
  ```
- **Önerilen Çözüm:** Boş bloğu kaldırın.
- **Öncelik:** MEDIUM

### M7. `redirect.php` Eksik `$meta` Tanımı
- **Dosya:** `home.coremusic.net/pages/redirect.php` satır 8-9
- **Sorun:** `@var array $meta` ve `@var string $csrfToken` PHPDoc ile tanımlanmış ama bu değişkenlerin nereden geldiği belli değil. `$csrfToken` hiçbir yerde kullanılmıyor (satır 14'te `$_SESSION['csp_nonce']` kullanılıyor). `$meta` muhtemelen route config'den geliyor ama garanti yok.
- **Önerilen Çözüm:** `$csrfToken` değişkenini kaldırın. `$meta` için fallback ekleyin.
- **Öncelik:** MEDIUM

---

## LOW Bulgular

### L1. Debug Header'lar — Production'da Kaldırılmalı
- **Dosya:** `home.coremusic.net/index.php` satır 8-11
- **Sorun:** `X-DM-File`, `X-DM-Mtime`, `X-DM-Size` header'ları development için eklenmiş. Production'da dosya boyutu ve modification time sızıntısı yaratır.
- **Önerilen Çözüm:** `DEBUG_MODE` kontrolü ile sarın veya kaldırın.
- **Öncelik:** LOW

### L2. `player-info.php` `<player_info>` Custom Element
- **Dosya:** `home.coremusic.net/pages/components/player-info.php` satır 10 ve 53
- **Sorun:** `<player_info>` özel HTML elementi kullanılmış. Bu standart bir HTML elementi değil. `<section>` veya `<article>` kullanılmalı. Custom element'ler `HTMLElement` interface'ini implements etmedikçe tarayıcı davranışları tutarsız olur.
- **Önerilen Çözüm:** `<player_info>` yerine `<section class="player-info">` kullanın.
- **Öncelik:** LOW

### L3. `recent-tracks.php` Boş Embedded Branch
- **Dosya:** `home.coremusic.net/pages/components/recent-tracks.php` satır 26
- **Sorun:** Embedded layout için `else` branch'i boş. Hiçbir HTML render edilmiyor.
- **Kod:**
  ```php
  <?php else: ?>
    <!-- boş -->
  <?php endif; ?>
  ```
- **Önerilen Çözüm:** Embedded layout için mini kart grid'i ekleyin veya `RecentTracksComponent`'de `$variant->isWide()` kontrolü ile erken return yapın.
- **Öncelik:** LOW

---

## shared/src/ Kullanım Durumu Özeti

| Sınıf | Kullanım | Durum |
|-------|----------|-------|
| DeviceManager | home, header, footer, HtmlShellRenderer | ✅ AKTİF |
| DeviceDetector | DeviceManager içinde | ✅ AKTİF |
| DeviceCssMap | DeviceManager içinde | ✅ AKTİF |
| DeviceRenderer | HtmlShellRenderer | ✅ AKTİF |
| ThemeManager | HtmlShellRenderer | ✅ AKTİF |
| ViewModeManager | HtmlShellRenderer | ✅ AKTİF |
| SessionBootstrapper | bootstrap.php, HomeSessionManager, auth index | ✅ AKTİF |
| SessionConfig | SessionBootstrapper içinde | ✅ AKTİF |
| SessionLifecycle | SessionBootstrapper içinde | ✅ AKTİF |
| SessionInitializer (Session) | Hiçbir yerde | ✅ AKTİF |
| SessionInitializer (PageRouter) | Hiçbir yerde | ❌ DEAD CODE |
| ConfigManager | index.php, auth index, HomeContainer | ✅ AKTİF |
| DomainConfig | index.php, auth index | ✅ AKTİF |
| EnvParser | constants.php | ✅ AKTİF |
| AuthRouteConfig | AuthContainer | ✅ AKTİF |
| PageRouterKernel | bootstrap.php, auth index | ✅ AKTİF |
| PageRouter | auth index | ✅ AKTİF |
| AuthGuard | auth index | ✅ AKTİF |
| RouteRegistry | auth index | ✅ AKTİF |
| AuthUrlBuilder | auth index | ✅ AKTİF |
| PageRouterHelper | auth index | ✅ AKTİF |
| PageCacheAdapter | auth index | ✅ AKTİF |
| LoggerFactory | bootstrap.php, auth index | ✅ AKTİF |
| FileHandler | LoggerFactory içinde | ✅ AKTİF |
| DatabaseManager | Hiçbir entry point | ⚠️ PLANNED |
| DatabaseRegistry | Hiçbir entry point | ⚠️ PLANNED |
| DatabaseConfig | DatabaseManager içinde | ⚠️ PLANNED |
| RuntimeBootstrap | index.php | ✅ AKTİF |
| SecurityHelper | Hiçbir yerde | ❌ DEAD CODE |
| ReturnUrlPolicy | Hiçbir yerde | ❌ DEAD CODE |
| UuidV7 | Hiçbir yerde | ❌ DEAD CODE |
| SessionKeys | Hiçbir yerde | ❌ DEAD CODE |
| CacheRateLimiter | RateLimiterMiddleware | ✅ AKTİF |
| SporadicLogger | Hiçbir yerde | ❌ DEAD CODE |
| OAuthManager | auth handler | ✅ AKTİF |
| OAuth Providers (10) | OAuthManager içinde | ✅ AKTİF |
| AIEngine | Hiçbir yerde | ❌ DEAD CODE |
| AIOrchestrator | Hiçbir yerde | ❌ DEAD CODE |
| KnowledgeBase | Hiçbir yerde | ❌ DEAD CODE |
| MemorySystem | Hiçbir yerde | ❌ DEAD CODE |
| PromptEngine | Hiçbir yerde | ❌ DEAD CODE |
| ToolCalling | Hiçbir yerde | ❌ DEAD CODE |
| AIWorkflow | Hiçbir yerde | ❌ DEAD CODE |
| EventDispatcher | Hiçbir yerde (sadece test) | ❌ DEAD CODE |
| Domain Events (9) | Hiçbir yerde (sadece test) | ❌ DEAD CODE |
| Integration Events (3) | Hiçbir yerde (sadece test) | ❌ DEAD CODE |
| Gateway | Hiçbir yerde (sadece test) | ❌ DEAD CODE |
| BFF (4 sınıf) | Hiçbir yerde | ❌ DEAD CODE |
| ServiceRegistry | Hiçbir yerde | ❌ DEAD CODE |
| ApiRequest/Response | Hiçbir yerde | ❌ DEAD CODE |
| MemoryAdapter | Hiçbir yerde | ❌ DEAD CODE |
| ApcuAdapter | Hiçbir yerde | ❌ DEAD CODE |
| ValidationMiddleware | Hiçbir yerde | ❌ DEAD CODE |

---

## Önerilen Aksiyon Sıralaması

| # | Aksiyon | Etki | Süre |
|---|---------|------|------|
| 1 | C1: SSRF düzelt — `file_get_contents` kaldır, `.env` fallback ekle | Güvenlik | 30 dk |
| 2 | C2: home.php orphaned `</div>` düzelt | Yapısal | 5 dk |
| 3 | H6: player-info.php unclosed quote düzelt | HTML | 2 dk |
| 4 | H1: PageRouter/SessionInitializer.php sil | Dead code temizliği | 2 dk |
| 5 | H3-H4: Unused import'ları kaldır | Dead code temizliği | 5 dk |
| 6 | H2: Duplicate autoload.php kaldır | Performans | 5 dk |
| 7 | H5: Eksik docblock düzelt veya `size()` implemente et | Dokümantasyon | 15 dk |
| 8 | L1: Debug header'ları korumalı hale getir | Güvenlik | 10 dk |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
