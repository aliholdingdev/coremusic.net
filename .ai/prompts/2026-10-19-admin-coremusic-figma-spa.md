---
title: "CoreMusic — Master Prompt: admin.coremusic.net Figma UI & SPA Router Entegrasyonu"
type: prompt
category: admin-panel
version: 1.0.0
status: pending-approval
authority: "prompt-maker v3.0.0 · SSOT: .ai/CLAUDE.md"
date: 2026-10-19
updated: 2026-10-19
governance: "Red Team · Human Mode · Truth Mode"
---

# Master Prompt: admin.coremusic.net — Figma UI, Shared SPA Router & Auth Bridge Entegrasyonu

---

### Bölüm 1 — /eli10 Analiz

`admin.coremusic.net` yönetim paneli, CoreMusic ekosisteminin 9 alt alan adından biri olup yönetici operasyonlarının (şarkı listeleme, şarkı ekleme, playlist yönetimi, müzik sitesi ve panel ayarları) merkezi kontrol noktasıdır. Sistem mimarisi, bağımsız yalıtılmış bir backend yerine `shared/` (`coremusic/shared-infrastructure`) kütüphanesine doğrudan bağlıdır; sayfa yönlendirmeleri `PageRouterKernel`, sayfa iskeleti `HtmlShellRenderer` ve dinamik stil katmanı `ViewModeManager` üzerinden hibrit SPA modeliyle çalışır. Figma `2161:12438` ("Core Music - Music Admin") tasarımı 1920×1080 masaüstü çözünürlüğünde 3 temel kareye dayanır: `Admin - Login` (`2161:12506`), `Admin - Register` (`2161:12529`) ve `Admin - layout` (`2161:12499`). Kimlik doğrulama mimarisi, ADR-058 (Merkezi Auth Servisi) ve ADR-047 gereğince `auth.coremusic.net` tek otoritesiyle yönetilir; admin paneli kendi bağımsız oturum açma motorunu çalıştırmaz, `AdminAuthBridge` aracılığıyla cross-domain `POST /validate-key` protokolü üzerinden oturum transfer eder. CSP kuralları (`form-action 'self'`) gereğince çapraz origin yerel POST engellendiğinden, admin login ve register formları istemci tarafında `admin-shell.js` üzerinden `fetch` ile API uçlarına (`/v1/auth/login`, `/v1/auth/register`) asenkron iletilir; başarılı yanıttaki tek kullanımlık `auth_key` admin servisinin `/auth/callback` ucuna ulaştırılarak güvenli PHP oturumu ayağa kaldırılır. Arayüzün tüm stil (CSS) ve istemci betikleri (JS) kesin bir kural olarak `assets.coremusic.net` üzerinde ITCSS 11 katman standardına (`01_Abstracts/a-admin-tokens.css`, `04_Components/c-adm-*.css`, `05_Pages/p-admin-*.css`, `08_Devices/d-admin-1920.css`, `09_ViewModes/v-admin.css`) uygun konumlandırılır; `HtmlShellRenderer` admin rotalarında `admin` view mode'unu otomatik devreye alarak `v-admin.css`'i head bölümüne enjekte eder. Geliştirme süreci iki aşamalı bir yaklaşımla yürütülecektir: Birinci aşamada Admin Login ve Register ekranları, ikinci aşamada ise Admin Layout (Sidebar: Şarkı Listesi, Şarkı Ekle, Playlist Oluştur, Ayarlar; Navbar ve Footer) ekranları ele alınacak; her aşama PHPUnit birim testleri ve gerçek tarayıcı (Browser Subagent / DevTools) kontrolleriyle doğrulanarak Figma PNG'lerine piksel, token ve sözleşme düzeyinde birebir uyum kanıtlanacaktır.

---

### Bölüm 2 — İstenen Şey Nedir

`admin.coremusic.net` yönetim servisinin Figma `2161:12438` tasarım verilerine ve `.ai/.png/admin-1920/` mockup'larına tam sadık kalınarak, `shared/` modülleri ve SPA router altyapısı üzerinde, `auth.coremusic.net` merkezi kimlik servisi ile hibrit bridge (`AdminAuthBridge`) üzerinden güvenli oturum transferi sağlayacak biçimde; Composer bağımlılıklarının aktif tutulması, stil ve script varlıklarının `assets.coremusic.net` altında ITCSS kurallarıyla yönetilmesi, 'admin' view mode entegrasyonu, PHP 8.4 backend katmanı ve kapsamlı PHPUnit + tarayıcı testleriyle eksiksiz, üretime hazır olarak inşa edilmesidir.

---

### Bölüm 3 — Vault Referansları (.ai/)

- `@.ai/CLAUDE.md` — Anayasa, §6A API-First, §6 Middleware zinciri, §18 BCNF & DB kuralları, §7 LINT
- `@.ai/AGENTS.md` — Master kurallar, §6 Swarm & Persona hiyerarşisi, §8 prompt-maker kuralı, §10 CSS kuralları
- `@.ai/WORKFLOW.md` — Faz kapıları (Explore → Plan → Implement → Test → Verify), Hard Gate kuralları
- `@.ai/brain.md` — Sistem kabuğu ve mimari kayıtlar
- `.ai/.decisions/accepted/ADR-058-centralized-auth-service.md` — auth.coremusic.net tek yetkili auth noktası, `validate-key` köprü mimarisi
- `.ai/.decisions/accepted/ADR-047-login-redirect-session-bridge.md` — Login redirect & cross-domain session bridge
- `.ai/.decisions/accepted/ADR-001-vanilla-javascript-core.md` — Vanilla JS zorunluluğu (framework yasağı)
- `.ai/.decisions/accepted/ADR-002-database-layer-mysql.md` — Raw PDO, prepared statement zorunluluğu, ORM yasağı
- `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` — CSRF token standardı
- `.ai/.decisions/accepted/ADR-012-csp-nonce-strict-dynamic.md` — Strict-dynamic CSP nonce standardı
- `.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md` — APCu rate limiting kuralları
- `.ai/ui-design/reference/figma/extracted-admin-1920.md` — Figma 2161:12438 (Admin 1920) 224 node çıkarım SSOT
- `.ai/ui-design/screens/T17-monitor-22fhd/admin-login.md` — Admin Login ASCII layout, token ve WCAG spec'i
- `.ai/ui-design/screens/T17-monitor-22fhd/admin-register.md` — Admin Register ASCII layout, token ve WCAG spec'i
- `.ai/ui-design/screens/T17-monitor-22fhd/admin-layout.md` — Admin Layout (Sidebar, Navbar, Content, Footer) spec'i
- `.ai/.png/admin-1920/` — Mockup PNG SSOT (`Admin - layout.png`, `Admin - Login.png`, `Admin - Register.png`)

---

### Bölüm 4 — Proje Kod Referansları

- `admin.coremusic.net/composer.json` — Admin bağımlılıkları (`coremusic/shared-infrastructure`, fast-route, php-di, nyholm/psr7, phpunit)
- `admin.coremusic.net/index.php` — Giriş noktası, RuntimeBootstrap ve config/bootstrap.php çağrısı
- `admin.coremusic.net/config/routes.php` — Admin SPA rota tanımları (`login`, `register`, `admin-login`, `admin-register`, `auth/callback`, `admin-dashboard`, `admin-songs`, `admin-song-add`, `admin-playlist`, `admin-settings`)
- `admin.coremusic.net/include/Auth/AdminAuthBridge.php` — Cross-domain oturum transfer sınıfı (`validateKey`, session fixation koruması)
- `admin.coremusic.net/pages/layout.php` — Ortak admin kabuğu (navbar, sidebar, breadcrumb, footer)
- `admin.coremusic.net/pages/admin-login.php` — Figma Admin - Login sayfası
- `admin.coremusic.net/pages/admin-register.php` — Figma Admin - Register sayfası
- `admin.coremusic.net/pages/admin-songs.php` — Şarkılar sayfası
- `admin.coremusic.net/pages/admin-song-add.php` — Şarkı Ekle sayfası
- `shared/src/PageRouter/PageRouterKernel.php` — Paylaşılan SPA router çekirdeği
- `shared/src/PageRouter/HtmlShellRenderer.php` — Ortak HTML kabuk üreticisi
- `shared/src/ViewMode/ViewModeManager.php` — `admin` view mode tespit mantığı (`ROUTE_MAP` `/admin*`)
- `shared/src/Device/DeviceCssMap.php` — View mode ve device CSS eşleyicisi (`v-admin.css`)
- `assets.coremusic.net/Css/09_ViewModes/v-admin.css` — Admin view mode CSS kökü
- `assets.coremusic.net/Css/08_Devices/d-admin-1920.css` — 1920px admin layout responsive CSS'i
- `assets.coremusic.net/Css/01_Abstracts/a-admin-tokens.css` — Admin tasarım token'ları
- `assets.coremusic.net/Css/04_Components/c-adm-*.css` — Admin BEM bileşenleri (sidebar, navbar, footer, form, button, auth, panel)
- `assets.coremusic.net/Css/05_Pages/p-admin-*.css` — Admin sayfa özel stilleri (login, register, layout, dashboard)
- `assets.coremusic.net/js/admin/admin-shell.js` — Admin SPA ve auth-bridge istemci betiği
- `admin.coremusic.net/tests/Unit/` — PHPUnit test paketi (`AdminAuthBridgeTest`, `AdminMarkupContractTest`, `RouteResolutionTest`, `ViewModeDetectionTest`)

---

### Bölüm 5 — Kullanıcı Kararları + Sorular + Cevaplar

| Seviye | Soru | Karar / Cevap | Gerekçe / Etki |
|---|---|---|---|
| **P0** (engelleyici) | Paket Yönetimi: "Compositor'ı aktif et" ifadesiyle ne kastediliyor? | Composer (PHP) paketleri ve bağımlılıkları aktif edilecek (`coremusic/shared-infrastructure` path symlink, fast-route, php-di). | Admin servisinin shared modüllere ve DI konteynerine hatasız erişimi sağlanır. |
| **P0** (engelleyici) | Kapsam Sırası: Figma'daki Login, Register ve Layout (Şarkı Listesi, Şarkı Ekle, Playlist, Ayarlar) ekranları hangi sırayla kodlanacak? | Aşama 1: Login ve Register; Aşama 2: Layout ve Şarkı Listesi / Şarkı Ekle. Aşama aşama test edilerek geçilecek, Figma ile birebir aynı olacak. | İki fazlı kademeli geçiş; oturum ve doğrulama tamamlanmadan iç panellere sahte veri basılması engellenir. |
| **P0** (engelleyici) | Kimlik Doğrulama: ADR-058 uyarınca admin'de auth servisi yokken oturum nasıl başlatılacak? | `auth.coremusic.net` bridge (`AdminAuthBridge`) ile cross-domain oturum köprüsü üzerinden başlatılacak. | ADR-058 ve ADR-047 ile tam uyum; auth tek noktada toplanır, servis kendi bağımsız DB auth'unu yazmaz. |
| **P1** (kalite) | Bitti Kanıtı: Görevin bittiği nasıl doğrulanacak ve varlıklar nerede yer alacak? | PHPUnit testleri + DevTools MCP (`chrome-devtools-mcp`) tarayıcı testleri; tüm CSS ve JS dosyaları kesinlikle `assets.coremusic.net` altında toplanacak. | Regresyon önlenir; statik varlıklar alan adı ayrımı prensibine uygun merkezileşir. |
| **P1** (kalite) | Stil Yükleme: HtmlShellRenderer head'e admin CSS'lerini nasıl yükleyecek? | `'admin'` view mode aktif edilecek (`shared/src/ViewMode/ViewModeManager.php` ve `DeviceCssMap.php` üzerinden `09_ViewModes/v-admin.css`). | Route prefix (`/admin*`) veya session `view_mode` üzerinden otomatik CSS enjeksiyonu sağlanır. |
| **P1** (kalite) | Rota Çakışması: Standart `/login` - `/register` yönlendirmeleri ile Figma'daki admin formları nasıl bağdaşacak? | Figma formları auth ile hibrit konuşan `/admin-login` ve `/admin-register` sayfaları olarak açılacak; home ise normal login'e, admin ise admin login'e yönlenecek. | Dış redirect sözleşmesi bozulmaz; admin özel formları fetch POST ile API'ye ulaşıp tek kullanımlık key ile callback'e döner. |
| **P2** (tercih) | İstemci Mimarisi: Admin form ve etkileşim mantığı nasıl çalışacak? | Vanilla JS (`admin-shell.js`) kullanılacak; SPA router gezinmesi korunacak, harici JS framework kullanılmayacak. | ADR-001 (Vanilla JS) ve strict CSP nonce kurallarına tam uyum. |

---

### Bölüm 6 — Orijinal Prompt (Değiştirilmeden)

```text
file:///c:/www/coremusic.net/admin.coremusic.net şimide 50 yıılık dnetyim iel protrm makeri ile çalıştır protmu oluşltur ve admin apneli figma data sı ile yapacağız .ai ui-dğiesgn figma dataları iel ayapaılcatırt başla sneiro olarak başla sahred mdolulerini spa rputeri kullan ve başla comspsoeri aktişf et conmspsoer gerkli apkeit yukle admien gercvek başla .ai yı oku # Questions
"Compositor'ı aktif et, gerekli paketleri yükle" — repo'da 'compositor' adında hiçbir paket/dosya YOK (0 eşleşme). Ne kastedildiğini netleştirmem gerekiyor.
Composer (PHP) paketleri
Figma'da 3 ekran var: Login, Register, Layout (sidebar menü: Şarkı listesi / Şarkı Ekle / Playlist Oluştur / Ayarlar). İlk aşamada hangi kapsam gerçek (gerçek veri/DB) olarak kodlanacak?
1 ve 2 ama test ederek diğer boluem egc birberir ayanısı oalsı alzım
ADR-058'e göre admin.coremusic.net'te auth servisi henüz YOK (PLANNED). Gerçek login nasıl başlasın?
auth.coremusic.net bridge (Recommended)
Bu görevin 'bitti' kanıtı ne olacak? (Plan bunu görecek)
PHPUnit + gerçek tarayıcı testi (Recommended) hayır csds ler ev js ler assets.coremusic.net e olmalaıalkıdr # Questions
CSS'in assets.coremusic.net'e taşındığı kesin. Peki admin CSS'i head'e NASIL yüklensin? (HtmlShellRenderer head'i shared'den yönetiyor; route bazlı css hook'u YOK)
'admin' view mode aç (Recommended)
Backend şu an /login ve /register'ı auth.coremusic.net'e yönlendiriyor (home deseni). Ama sen 'figma data ile' dedin ve Figma'da Admin-Login/Register ekranları var. Çakışma nasıl çözülsün?
figma datas da bunu auth a ayarı bri admin-****pagename** oalcka aç ve sharede auth a hibirtt bağla hoem ise olgoin nomrla admin sie admin lofgğine yeöenerisine ct... 
```

---

### Bölüm 7 — Görevlere Bölme (Task Breakdown)

1. **T1 — Composer & Altyapı Denetimi:** `admin.coremusic.net/composer.json`, `shared` symlink, PSR-4 autoload (`CoreMusic\Admin\`), vendor paketleri ve PHP 8.4 uyumluluğu doğrulanacak; eksik paket varsa senkronize edilecek.
2. **T2 — ViewMode & Head CSS Entegrasyonu:** `shared/src/ViewMode/ViewModeManager.php` ve `DeviceCssMap.php` doğrulanacak; `/admin*` route prefix'lerinin `admin` view mode olarak algılandığı ve `09_ViewModes/v-admin.css` linkinin enjekte edildiği test edilecek.
3. **T3 — Assets ITCSS Mimari Doğrulaması:** `assets.coremusic.net/Css/` altındaki admin token'ları (`a-admin-tokens.css`), bileşenleri (`c-adm-*.css`), sayfa stilleri (`p-admin-*.css`) ve cihaz kuralları (`d-admin-1920.css`) Figma `extracted-admin-1920.md` çıkarımına göre denetlenecek, eksik token/kural varsa tamamlanacak.
4. **T4 — Admin-Login Sayfası & İstemci Akışı (Figma 2161:12506):** `pages/admin-login.php` ve `admin-shell.js` entegrasyonu; CSP `form-action 'self'` uyumlu fetch POST `/v1/auth/login`, CSRF doğrulaması, hata geri bildirimi, Beni Hatırla, Şifremi Unuttum ve QR/mobil giriş bloğu tam çalışır hale getirilecek.
5. **T5 — Admin-Register Sayfası & İstemci Akışı (Figma 2161:12529):** `pages/admin-register.php` formu; 5 giriş alanı (Adı Soyadı, Kullanıcı Adı, Email, Parola, Doğum Tarihi), onay kutusu, hata durumları ve API fetch POST akışı inşa edilecek.
6. **T6 — AdminAuthBridge & Oturum Köprüsü:** `include/Auth/AdminAuthBridge.php` ve `/auth/callback` akışı; `validate-key` ile auth.coremusic.net doğrulaması, session fixation koruması ve oturum kimlik değişkenlerinin (`MM_Username`, `MM_UserRole`) güvenli atanması sağlanacak.
7. **T7 — Admin Layout Kabuğu (Figma 2161:12499):** `pages/layout.php` ortak iskeleti; 1920×1080 grid hizalaması, Menu DivA (Sidebar: Menü, Şarkılar grubu), Navbar Admin (avatar, kullanıcı adı, pil/ağ durumları, çıkış butonu), ContainerA (breadcrumb, başlık paneli) ve Footer (kullanıcı, IP, rol, copyright) Figma ile birebir senkronize edilecek.
8. **T8 — Şarkı Yönetimi Sayfaları & Boş-Durum Entegrasyonu:** `pages/admin-songs.php`, `pages/admin-song-add.php`, `pages/admin-playlist.php` ve `pages/admin-settings.php` sayfaları layout kabuğuna bağlanacak; gerçek veri katmanına hazır BEM boş durum ve veri listesi yapıları yerleştirilecek.
9. **T9 — PHPUnit Test Paketi & Regresyon Denetimi:** `admin.coremusic.net/tests/Unit/` altındaki `AdminAuthBridgeTest`, `AdminMarkupContractTest`, `RouteResolutionTest` ve `ViewModeDetectionTest` çalıştırılacak, yeni rotalar ve form kontratları için test kapsamı genişletilecek (≥%80 hedef).
10. **T10 — Gerçek Tarayıcı (DevTools MCP) E2E Doğrulaması:** `admin.coremusic.net` üzerinde `chrome-devtools-mcp` ile gerçek tarayıcı oturumu açılarak (`navigate_page`, `resize_page` 1920×1080, `take_screenshot`, `list_network_requests`, `list_console_messages`, `get_css_styles`); Admin-Login, Admin-Register ve Admin-Dashboard ekranlarının Figma PNG'leriyle görsel eşleşmesi, network istekleri (CSS/JS varlıklarının `assets.coremusic.net`'ten HTTP 200 ile yüklenmesi), DOM elementleri ve konsol hataları (0 error) doğrulanıp raporlanacak.

---

### Bölüm 8 — Kapanış

```text
✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 10 | Cevap: 7
```
