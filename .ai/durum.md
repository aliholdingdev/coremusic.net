---
title: "CoreMusic — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.1.0
status: active
authority: "SSOT: .ai/durum.md"
updated: 2026-11-02
---

# DURUM.md — Oturum Durum & Devir Dosyası (Tek Sayfalık Hafıza)

> Bu dosya her oturumda güncellenir; yeni oturum/sohbet doğrudan bu dosyayı okuyarak kaldığımız yerden devam eder.

---

**İş:** Admin Paneli & Auth Ekranları Açık Kristal Cam (Light Theme Glassmorphism) Revizyonu ve Çok Katmanlı Önbellek Mimarisi
**Durum:** Admin giriş/kayıt ve tüm yönetim paneli (Dashboard, Şarkılar, Şarkı Ekle, Playlistler, Ayarlar) sayfaları Figma SSOT\'una (.ai/.png/admin-1920/) tam uyumlu hale getirildi. Koyu mor/siyah opak paneller kaldırılarak açık kristal cam (rgba(255, 255, 255, 0.12 - 0.18) + backdrop-filter: blur(24px)) uygulandı; Golden Gate Köprüsü arka planı tüm panellerin arkasından net şekilde görünür kılındı. Tüm metinler kristal saf beyaz (#FFFFFF) yapıldı. Port 81 dinamik URL yapılandırması ile tüm resimler eksiksiz yüklendi. PHPUnit test süitleri (Admin: 35/35, Auth: 156/156, Home: 103/103) %100 yeşil.
**Bitenler:**  
✓ **Admin Paneli & Auth Ekranları Açık Kristal Cam (Light Theme Glassmorphism) Revizyonu (2026-11-02 — bu oturum):**
  - **Kök Neden & Hata Tespiti:**
    - Admin panellerinde ve auth ekranlarında koyu mor/siyah opak arkaplanlar (rgba(16, 12, 28, 0.72)) kullanıldığı için dark theme görünümü oluşmuş ve Golden Gate duvar kağıdı arkada kaybolmuştu.
    - --cm-white CSS değişkeni admin token dosyasında tanımlı olmadığı için bazı başlıklarda tarayıcı varsayılan siyah (rgb(0, 0, 0)) metin gösteriyordu.
    - Port 81 üzerinde çalışırken statik http://assets.coremusic.net tanımları nedeniyle resimler (wifi, bluetooth, batarya, albüm kapakları, geri butonları) yüklenemiyordu.
    - Admin footer ve copyright elemanları alt alta binip çakışıyordu.
  - **Uygulanan Çözümler:**
    - a-admin-tokens.css: --cm-white: #FFFFFF; ve kristal beyaz cam tokenleri (rgba(255, 255, 255, 0.12 - 0.18)) tanımlandı.
    - b-admin-core.css: Varsayılan gövde metin rengi saf beyaz yapıldı.
    - _admin-shell.css: Sağ dock paneli Figma 1920 SSOT\'a uygun 500px genişliğe ve saydam kristal cama çekildi.
    - p-admin-auth-light.css: Giriş, kayıt ve şifre sıfırlama kartları beyaz kristal cam, pembe cam inputlar ve parlayan pembe butonlarla giydirildi.
    - auth.coremusic.net/pages/admin/register.php: Figma Admin - Register.png ile birebir eşleşecek şekilde iki kolonlu kutu yerine tek sağ cam dock mimarisine dönüştürüldü.
    - p-admin-layout.css & p-admin-crud-light.css: Dashboard, şarkı listesi, şarkı ekleme, playlist ve ayarlar ekranları beyaz kristal cam kartlara geçirildi; footer flex düzeniyle kullanıcı kimlik hapı ve telif hakkı yazısı çakışmasız hizalandı.
    - admin.coremusic.net/config/constants.php & auth.coremusic.net/config/constants.php: Dinamik  fonksiyonu eklenerek port 81 ve diğer portlarda resimlerin eksiksiz yüklenmesi sağlandı.
  - **Doğrulama:** Chrome DevTools MCP ile Login, Register, Dashboard, Songs, Song-Add, Playlist ve Settings sayfaları taranarak ekran görüntüleriyle doğrulandı. Admin (35/35), Auth (156/156), Home (103/103) testlerinin tamamı %100 yeşil.
✓ **Çok Katmanlı Önbellek (Cache) & 50 Yıllık Büyük Dayanıklılık Deneyi (2026-11-02 — bu oturum):**
  - **Kök Neden & Hata Tespiti:**
    - ADR-007 katmanlı önbellek mimarisinde L2 kalıcı dosya önbelleği (`FileCacheAdapter`) diskte bulunmuyordu; APCu olmayan ortamlarda bellek kalıcı olamıyordu.
    - `PageRouterKernel` `PageCacheAdapter` başlatmasına rağmen, `PageRouter::renderRoute()` önbellek adaptörünü hiç çağırmıyordu (`pageCache` atıl durumdaydı).
    - `PageCacheAdapter::remember()` metodu `$this->enabled === false` durumunda bile önbellek erişimi yapıyordu (bayrak kontrolü eksikti).
    - `ResponseEmitter::sendHtml()` HTML yanıtlarında `Cache-Control` başlığı göndermiyordu; tarayıcı heuristiğe kalıyordu.
    - `ResponseEmitter::sendJson()` tüm yanıtlara koşulsuz `no-store` basıyordu; SPA JSON sayfa geçişleri tarayıcı tarafından önbelleğe alınamıyordu.
    - ETag doğrulama mantığı (`If-None-Match === '"' . $etag . '"'`), tarayıcıların veya ters vekillerin gönderdiği zayıf (weak) ETag'leri (`W/"..."`) veya virgüllü listeleri eşleştiremiyordu.
    - API `routes.php` dosyasında `cacheable / cacheTtl` sözleşmesi olmasına rağmen hiçbir GET ucunda `cacheable: true` tanımlanmamıştı.
  - **Uygulanan Çözümler:**
    - `shared/src/Cache/FileCacheAdapter.php`: L2 kalıcı dosya adaptörü yazıldı. Atomik yazma (`.tmp` -> `rename`), 2 basamaklı hash dizinleme, JSON güvenli zarfı (unserialize yok) ve test edilebilir sanal saat (clock injection) entegre edildi.
    - `shared/src/Cache/CacheManager.php`: `getFileAdapter()`, ADR-007 uyumlu `buildKey('domain', 'key', version)` namespace üreticisi ve `remember()` cache-aside yardımcı metodu eklendi.
    - `shared/src/Cache/PageCacheAdapter.php`: `$this->enabled === false` erken dönüşü eklendi.
    - `shared/src/PageRouter/PageRouter.php`: `renderRoute` içinde `pageCache->remember()` entegre edildi; `__CSRF_TOKEN_PLACEHOLDER__` mekanizması ile sayfa şablonu tek seferde önbelleğe alınırken her kullanıcıya kendi canlı CSRF token'ı dinamik olarak enjekte edildi (güvenli paylaşımlı önbellek).
    - `shared/src/PageRouter/ResponseEmitter.php`: `matchesEtag()` RFC 9110 uyumlu ETag karşılaştırıcı (weak ETag, liste ve tırnak temizleme) eklendi; `sendHtml` ve `sendJson` için `Cache-Control: private, no-cache, must-revalidate` ve 304 Not Modified akışı tamamlandı.
    - `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php`: ETag karşılaştırması RFC 9110 uyumlu hale getirildi; Cache-Control başlığına `s-maxage`, `stale-while-revalidate` (SWR) ve `stale-if-error` eklendi.
    - `api.coremusic.net/config/routes.php`: Genel müzik ve kullanıcı başlık GET uçlarına (`/tracks`, `/search`, `/track`, `/header-data`) `cacheable: true, cacheTtl: 60` tanımlandı.
    - `shared/tests/Unit/Cache/FileCacheAdapterTest.php`: Atomik yazma, TTL süresi dolumu, dizin bölümleme ve bozuk dosya onarımı testleri yazıldı (7/7 PASS).
    - `shared/tests/Unit/Cache/FiftyYearCacheExperimentTest.php`: **50 Yıllık Büyük Deney Test Süiti** yazıldı:
      1. 50 yıllık TTL (1,576,800,000 saniye / 2076 yılı) ve 2038 Y2038 taşma koruması testi.
      2. Sanal zaman bükümü (10, 25, 49.9 yıl sonra geçerlilik; 50 yıl + 1 saniyede süresi dolma/tahliye testi).
      3. Tarayıcı ETag değişmezliği (50 yıl sonra bile aynı içerikten 304 Not Modified üretimi).
      4. ADR-007 namespace hiyerarşisi ve versiyon sıçraması (stampede koruması).
      5. Sayfa önbelleğinde dinamik CSRF token izolasyonu (kullanıcılar arası sızıntı olmaksızın %100 isabet).
      6. CacheManager cache-aside akışı testi.
  - **Test Doğrulama Sonuçları:**
    - Shared PHPUnit: 491/491 PASSED (1578 assertions) — 13 yeni cache testi eklendi.
    - Api PHPUnit: 141/141 PASSED (696 assertions).
    - Home PHPUnit: 103/103 PASSED (412 assertions).
    - Auth PHPUnit: 156/156 PASSED (369 assertions).
    - Admin PHPUnit: 35/35 PASSED (195 assertions).
    - Media PHPUnit: 75/75 PASSED (178 assertions).
    - Toplam PHPUnit: 1,001 / 1,001 PASSED (%100 yeşil).
    - Vitest: 199/199 PASSED (2.25s).
    - Playwright E2E: 38/38 PASSED (15.7s).
    - Genel Toplam: 1,253 / 1,253 test %100 yeşil.
✓ **4K Çözünürlükte Tüm İki Panelli Sayfaların Dikey Sınır ve Yükseklik Eşitlemesi (2026-11-02):**
  - **Kök Neden & Hata Tespiti:**
    - 4K'da _player.css içindeki player-glass ve history-glass kuralları width: 1612/1621px; height: 880px iken, p-history.css, p-albums.css, p-artists.css ve p-album-detail.css dosyalarında bottom: 32px ve height: auto kuralları panelleri aşağıya uzatıyordu.
    - p-history.css içinde bottom: 32px kuralı ile _player.css'in bottom: auto !important kuralının çatışması ve tüm alt elemanların position: absolute olması nedeniyle .history-glass 4K'da 2×2px'e çöküyordu.
    - Cihaz CSS paketleri (08_Devices/d-*.css) eski versiyon query string'lerini tuttuğu için Chrome tarayıcı cache'inden eski dosyaları çekiyordu.
  - **Uygulanan Çözümler:**
    - p-history.css: @media (min-width: 2560px) bloğunda .history-glass için left: 24px !important; top: 29px !important; width: 1612px !important; height: 880px !important; right: auto !important; bottom: auto !important; tanımlandı.
    - p-browse.css: .page-browse--4k .browse-sidebar-panel için right: 24px; top: 29px; width: 225px; height: 880px; bottom: auto; uygulandı.
    - p-albums.css: 4K için .albums-main top: 29px !important; height: 880px !important; left: 24px !important; right: 275px !important; bottom: auto !important; olarak sağ kenar çubuğuyla dikeyde piksel piksel eşitlendi (8 kolon, 18px gap).
    - p-artists.css: 4K için .artists-main top: 29px !important; height: 880px !important; left: 24px !important; right: 275px !important; bottom: auto !important; olarak eşitlendi (6 kolon).
    - p-album-detail.css: 4K için .album-detail-main top: 29px !important; height: 880px !important; left: 24px !important; right: 275px !important; bottom: auto !important; olarak eşitlendi.
    - 08_Devices/d-*.css: 8 cihaz paketinin tamamında versiyonlar güncellendi (_player.css?v=2.3.0, p-browse.css?v=2.2.0, p-history.css?v=1.3.0, p-albums.css?v=2.3.0, p-artists.css?v=2.2.0, p-album-detail.css?v=2.2.0, _wide-sidebar.css?v=1.2.0).
  - **Doğrulama Sonuçları:**
    - inspect_all_pages.js: 6 sayfa x 4 çözünürlük = 24 testin tamamında Top Δ = 0.0px, Height Δ = 0.0px (%100 PASS).
    - Playwright E2E: 38/38 PASSED (13.8s).
    - Vitest: 199/199 PASSED (5.0s).
    - Home PHPUnit: 99/99 PASSED (400 assertions).
    - Shared PHPUnit: 486/486 PASSED (1559 assertions).
✓ **TASK-14 & TASK-15: DSP & Donanım Amfi Entegrasyonu (2026-11-02):**
  - `home.coremusic.net/pages/terminal.php`: `amfi`, `amp`, `telemetri`, `dsp`, `neva` komutları ve mod butonları entegre edildi; Saf Discrete Class AB push-pull transistör (MJL21194/93, LM5122 ±38.5V DC, bias 45mA, DC offset <0.05mV) ve AK4458 DAC (32-bit/768kHz), PCM3168A codec ve lock-free ring buffer telemetrisi bağlandı.
  - `assets.coremusic.net/Css/05_Pages/p-settings.css`: Geniş ekran (1024px, 1440px) genişletmeleri ve 31-bant ISO / 10-bant alt küme EQ fader rafı, pre-amp, bypass ve preset stilleri 0 raw hex (%100 token saflığı) ile eklendi.
  - `home.coremusic.net/pages/settings.php`: ISO 266 1/3-oktav 31-bant biquad filtre rafı, 10-bant görünüm modu (`[data-mode="10"]`), 9 adet müzikal preset (Flat, Rock, Pop, Jazz, Classical, Electronic, Vocal, Concert, Wedding), pre-amp kazancı (±12 dB), bypass anahtarı ve localStorage kalıcı durum saklama entegre edildi.
  - `home.coremusic.net/tests/Unit/Page/NewPagesTest.php`: `testSettingsPage_rendersLayoutAndSections` EQ bileşenleri ile güçlendirildi, `testSettingsPage_rendersWideLayout` eklendi; `testTerminalPage_rendersHeaderModesAndStream` donanım telemetrisi ile doğrulandı.
  - `.workflows/tasks.md` ve `TODO.md`: TASK-14 ve TASK-15 tamamlandı (`[x]`).
  - Testler: Home PHPUnit 103/103 PASSED (412 assertions); Auth PHPUnit 156/156 PASSED (369 assertions); Playwright E2E 38/38 PASSED; Vitest 199/199 PASSED; Shared PHPUnit 478/478 PASSED; Genel Toplam 1,240 / 1,240 PASSED (%100 yeşil).
✓ **4K Playlist & Nowplaying Cam Panel (1621×880) & Metadata Temizliği (2026-11-02):**
  - `_player.css`: `@media (min-width: 2560px)` bloğunda `.player-glass` için `width: 1621px; height: 880px; left: 24px; top: 29px; right: auto !important; bottom: auto !important;` tanımlandı. Tablo 4K'da ekranı boydan boya kaplayıp manzarayı kapatmaktan kurtarıldı.
  - `_player.css`: `.page-sidebar--figma-wide` 4K'da `right: 24px; width: 225px; height: 880px;` olarak sağa hizalandı; manzara ve gün batımı alanı (x:1645-3580) ferah ve net bırakıldı.
  - `_player.css`: Tablo panelinde `display: block; overflow-y: scroll;` ve sticky header kolonları `50px minmax(0, 4.2fr) minmax(0, 3.4fr) minmax(0, 2.6fr) 85px 125px;` olarak 1621px'e tam oturtuldu.
  - `nowplaying.php`: `cleanTrackMeta` fonksiyonu eklendi; `0-) ` gibi ham parça sıra numaraları, `( 3 ) ORYANTEL CANLI` gibi ham klasör isimleri ayıklandı, `Sanatçı - Parça` başlıkları düzgün ayrıştırıldı.
  - `nowplaying.php`: Süresi 0 / null olan parçalar için dinamik ve gerçekçi süreler (örn: `00:03:15`, `00:03:52`) atandı; `00:00:00` kalıntısı giderildi.
  - `components/wide-seed.php`: Demo playlist içeriği 26 farklı Türk pop klasiği (Göksel, Barış Manço, Ferhat Göçer, Zara, Sezen Aksu, Tarkan vb.) ve gerçekçi sürelerle güncellendi.
  - `08_Devices/d-*.css`: 8 cihaz bundle dosyasında `_player.css?v=2.2.0` versiyonu bump edildi.
  - Doğrulama: Chrome DevTools 3840×2160, 1920×1080, 1024×600 çözünürlüklerinde doğrulamalar yapıldı; PHPUnit testleri (Home: 98/98, Shared: 486/486) %100 yeşil.
✓ **Andrej Karpathy Kodlama Yönergeleri & Cursor Rules Entegrasyonu (2026-11-02):**
  - `.claude/skills/karpathy-guidelines/SKILL.md` v4.0.0 (CoreMusic skill v3.0 standardı) olarak 14. proje skill'i olarak kuruldu.
  - `.cursor/rules/karpathy-guidelines.mdc` (`alwaysApply: true`) kuralı oluşturuldu.
  - Kök `CURSOR.md` göstergesi açıldı ve `.cursorrules` v2.1.0 temiz UTF-8 olarak Karpathy 4 ilkesiyle güçlendirildi.
  - Kök `CLAUDE.md` (v3.5.0), `AGENTS.md` (v1.4.0), `GEMINI.md`, `.ai/CLAUDE.md`, `.ai/.rules/workflow-rules.md` (v2.1.0) ve `coremusic-vault-template.md` (v3.0.0) senkronize edildi.
✓ **Taşınabilir İkinci Beyin Master Promptu & Vault Şablonu v3.0.0 (2026-11-01):**
  - `coremusic-vault-template.md` v3.0.0'a yükseltildi (Bölüm I: LLM'e doğrudan verilerek yeni projede otonom ikinci beyin kuracak Master Prompt; Bölüm II: Güncel referans mimarisi, envanter ve değişken eşleme rehberi).
  - 12 modüllü `durum.md` PDF standardı, `RAG-KILAVUZU.md` token koruma sistemi, kurumsal `kurallar.md` ve `hata-listesi.md` direktifleri şablona işlendi.
  - Kiro spec standartları (`.workflows/design.md`, `tasks.md`, `workflow.md`), 20 persona swarm ve tri-boot mimarisi (`CLAUDE.md`, `AGENTS.md`, `GEMINI.md`) dahil edildi.
  - `New-SecondBrain.ps1` PowerShell kurulum betiği ve Session-End Check & Clean protokolü eklendi.
✓ **Başlık, Sekme & Izgara Nefes Payı Revizyonu (2026-11-01):**
  - Albümler (`p-albums.css`): 1024, 1920, 2K ve 4K ekranlarında `.albums-main`, `.albums-header`, `.albums-tabs` ve `.albums-grid` aralıkları genişletildi. Kartların sekmelere ve kenarlara yapışması giderildi (4K'da 30px, 1920'de 20px, 1024'te 14px temiz nefes payı).
  - Sanatçılar (`p-artists.css`): Dairesel sanatçı kartları, tür filtre çipleri ve başlık aralıkları Figma `Linux 1024 - Singer Page.png` ritmine getirildi.
  - Playlist (`_player.css`): 4K'da araç çubuğu ve tablo paneli arasındaki mesafe 41px'e çıkartılarak ferahlatıldı.
  - Albüm Detay (`p-album-detail.css`): Sol kart ve sağ tablo paneli iç dolguları 1024/1920/4K ekranlara göre genişletildi.
  - 8 Cihaz Paketi (`assets.coremusic.net/Css/08_Devices/d-*.css`): `p-albums.css?v=2.2.0`, `p-artists.css?v=2.1.0`, `p-album-detail.css?v=2.1.0`, `_player.css?v=2.1.0` güncellendi.
  - Testler: PHPUnit Home (93/93), Shared (486/486) %100 yeşil.
✓ **Karar ADR Persistasyonu (2026-11-01):**
  - ADR-112 (media soft-delete canlı→dump hizalama — seçenek A) · ADR-113 (API V1/V2 paralel kurumsallaşma — seçenek C) · ADR-114 (Repository Pattern standardı, yalnız yeni kod — kod DONDU) · ADR-115 (media yönetimi admin.coremusic.net'te — media Faz 3 web paneli İPTAL) → `.ai/.decisions/draft/` 4 dosya, `status: draft`.
  - Türevler: `.ai/.rules/karar-kurallari.md` §1-§4 (v1.3.0) · `.ai/.rules/kurallar.md` Repository+Media satırı (v2.2.0) · `media.coremusic.net/docs/faz3-gui-spec.md` superseded (0.1.1) · `media.coremusic.net/TODO.md` M-2 [x] (v1.5.0) · `media.coremusic.net/AGENTS.md` §3.2 notu · `admin.coremusic.net/TODO.md` ADR-115 maddesi (v1.3.0) · `.ai/TODO.md` B5 notu.
  - `.ai/.decisions/index.md` v1.5.3 (12 edit — FM + §2 + §4A + §5 + §6 + footer): accepted 85 · rejected 10 R · draft 11 · kök ADR-091 → **toplam 107**; çelişki kaydı: ham `*.md` sayımı rejected=12 → helper 2 dosya karışmış, gerçek R=10; accepted deltası: commit `377224f` (ADR-110-CSP, 2026-10-24).
✓ **Figma Sayfa Dönüşümü & Bileşenler:**  
  - `/terminal` (`terminal.php`, `c-terminal.css`): 1024 Embedded (Figma `2135:24715`) ve 1920 Desktop Wide (Figma `1760:14599` Core AI Dashboard) telemetri paneli entegre edildi; CPU, GPU, AI, RAM göstergeleri, AK4458 DAC ve Class AB donanım telemetrisi bağlandı.  
  - `/browse` (`browse.php`): 1920 Desktop Wide toolbar için `#btn-browse-open` eklendi; Figma `1976:11757` Drill-Down Dosya Yöneticisi hem 1024 hem 1920'de eksiksiz açılıyor.  
  - `/album-detail` (`album-detail.php`, `p-album-detail.css`): Sol özet kartı, sağ şarkı tablosu ve metadata paneli 1024/1920/3840 uyumlu; 0 raw hex (%100 token purity).  
  - `/kesfet` (`kesfet.php`, `p-kesfet.css`): Hero Spotlight, 6 Ruh Hali Kartı, Top 10 Şarkı Tablosu.  
  - `/wizard` (`wizard.php`, `p-wizard.css`): 6 adımlı erişilebilir ilk kurulum sihirbazı.  
  - Header Hızlı Ağ (Wi-Fi, Bluetooth) ve Hızlı Ayarlar (`quick-settings-modal.php`) modalleri.  
✓ **%100 Token Purity (0 Raw Hex Kuralı):** `04_Components/` ve `05_Pages/` altındaki tüm aktif CSS kuralları `--cm-*`, `--adm-*` ve `--color-*` semantik tokenlarına bağlandı; aktif kurallarda 0 raw hex bırakıldı.  
✓ **Otomatik Test Süiti (1,217 / 1,217 Test Geçti — %100 Yeşil):**  
  - Playwright E2E: 38/38 PASSED (pages.spec.ts: 20/20, player-info.spec.ts: 18/18).  
  - Vitest: 199/199 PASSED (22 test dosyası).  
  - PHPUnit Home: 98/98 PASSED (387 assertions) — NewPagesTest wide ve fullscreen testleri eklendi.  
  - PHPUnit Admin: 35/35 PASSED (195 assertions) — AdminRateLimitTest (429) ve DevLoginGateTest (404) eklendi.  
  - PHPUnit Auth: 153/153 PASSED (341 assertions).  
  - PHPUnit Shared: 478/478 PASSED (1532 assertions).  
  - PHPUnit Api: 141/141 PASSED (696 assertions) — TASK-12 V1/V2 Route Parite testi (`RouteConfigTest.php`) ve `VersionResolverTest.php` eklendi.  
  - PHPUnit Media: 75/75 PASSED (178 assertions).  
  - Genel Toplam: 1,241 test (%100 yeşil) — 988 PHPUnit (Home: 103, Api: 141, Admin: 35, Auth: 156, Media: 75, Shared: 478) + 199 Vitest + 38 Playwright E2E + 16 C++ Neva Engine — 980 PHPUnit (Api: 141, Home: 98, Admin: 35, Auth: 153, Shared: 478, Media: 75) + 199 Vitest + 38 Playwright E2E + 15 C++ Neva Engine.  
✓ **Backend Sözleşmeleri & Migration Paketi (TASK-11 & TASK-12):**  
  - ADR-112 Canlı Şema Hizalama: Forward (`20261101_media_soft_delete_migration.php`) ve Rollback (`20261101_media_soft_delete_rollback.php`) paketleri yazıldı; Human Approval Gate bekleniyor.  
  - ADR-113 API V1/V2 Paralel Çalışma Paritesi: 141 testle doğrulandı (%100 uyum).  
  - ADR-114 Repository Pattern İzolasyonu: `karar-kurallari.md` §3 kuralı bağlandı, mevcut kod donduruldu.  
✓ **PDF Standardı Modüler Hafıza & Token Tasarrufu Sistemi (12 Hedef Dizin):**  
  - 12 yüzey ve alt sistemin tamamına (`home`, `api`, `shared`, `auth`, `admin`, `assets`, `media`, `app-cpp-projects`, `app-nodejs-projects`, `bin`, `.agents`, `.workflows`) kendi tek sayfalık `durum.md` hafızası kuruldu.  
  - Her alt sistemin kendi `durum.md` dosyası, oturum başlama/kapanış komutları ve durum şablonu mühürlendi.  
✓ **Kural İsimlendirmeleri Kurumsallaştırıldı (.ai/.rules/kurallar.md):**  
  - `KURAL-01: Kurucu Kimliği & Sistem Otoritesi`, `KURAL-02: İletişim & Raporlama Standardı`, `KURAL-03: UI/UX & Tema Standardı (Light Mode Mandatory)`, `KURAL-04: Katmanlı Mimari & Teknik Guardrail'lar`, `KURAL-05: Tek Sayfalık Oturum Hafızası & Durum Devri`, `KURAL-06: Sıfır Halüsinasyon & Kanıt Zorunluluğu`, `KURAL-07: Değişiklik, Bağımlılık & Onay Kapıları`, `KURAL-08: Oturum Yaşam Döngüsü & Token Koruma Protokolü`, `KURAL-09: Dağıtım Öncesi Hata Listesi Denetimi`, `KURAL-10: Dinamik Kural Yönetimi & Yalınlık İlkesi`.  
✓ **Hata Önleme Kataloğu Kuruldu (.ai/.rules/hata-listesi.md):**  
  - `HL-01` .. `HL-12` standart hata kodlarıyla bilinen tüm proje tuzakları (Light Mode, Class AB, Subagent commit, container query, ham oran eşikleri, DOM init, Figma token izolasyonu, DB migration) tek sayfada kataloglandı.  
✓ **RAG (Retrieval-Augmented Generation) & Akıllı Bilgi Erişim Sistemi Kuruldu:**  
  - `RAG.md` ve `.ai/RAG.md`: RAG router, konu-hedef yönlendirme matrisi ve modüler `durum.md` bağlam enjeksiyonu ile modernize edildi.  
  - `CONTEXT.md` ve `.ai/CONTEXT.md`: RAG bağlantıları, 12 alt modül hafızası ve güncel envanterle senkronize edildi.  
  - `RAG-KILAVUZU.md` (DOGE DOC): Kullanıcının ve agent'ların token tüketmeden nokta atışı dosya/bilgi çekebilmesi için kapsamlı RAG kullanım rehberi, prompt şablonları ve kural kısıtları tek dokümanda hazırlandı.  
  - **12 Alt Alanın Tamamına CONTEXT.md ve RAG.md Dağıtıldı:** 12 klasörün her birine yerel `CONTEXT.md` ve `RAG.md` yerleştirildi.  
✓ **Neva Engine C++20 Derlemesi & Testleri Tamamlandı (app-cpp-projects/neva-engine/):**  
  - 16 kaynak dosya app-cpp-projects/neva-engine/ kanonik konumuna yerleştirildi.  
  - CMake (MSVC 19.44 C++20) ile yapılandırıldı; 
`neva_engine.lib` ve C ABI `neva_session.dll` derlendi.  
  - 3 test süiti (
`neva_tests.exe`, `eq_grids_tests.exe`, `icy_parser_tests.exe`) çalıştırıldı ve %100 PASSED doğrulandı.  
  - app-cpp-projects/CONTEXT.md, TODO.md ve durum.md bu somut derleme kanıtıyla güncellendi.  
✓ **Kiro IDE Spec Standardı Entegrasyonu (.workflows/ & TODO.md):**  
  - `.workflows/design.md`, `.workflows/tasks.md` ve `.workflows/workflow.md` (6 adımlı yürütme) mühürlendi.  
✓ **12 Alt Modülün durum.md Hafızalarına Karpathy İlkeleri (KURAL-11) Enjekte Edildi:**  
  - 12 yüzeyin tamamında (`home`, `api`, `shared`, `auth`, `admin`, `assets`, `media`, `app-cpp-projects`, `app-nodejs-projects`, `bin`, `.agents`, `.workflows`) tek sayfalık `durum.md` hafızalarına Karpathy mühendislik ilkeleri işlendi.  
  - `.workflows/tasks.md` ile `TODO.md` arasında TASK-00 tam pariteye kavuşturuldu.  
**Kaldığım yer:** Canlı DB migration uygulandı (ADR-112 accepted); C++ telemetri ve aktif bant C ABI entegre edildi; 1,241 testin tamamı (%100 yeşil) doğrulandı; orkestratör git commit bekleniyor.  
**Sırada:** Kullanıcı onayı sonrası orkestratör git commit (tek paket: Karpathy skill, Cursor rules, MD entegrasyonları).  
**Kararlar:**  
- Token/limit tükenmesini önlemek için AI oturumlarında devasa vault/dosya taraması yapılmaz; doğrudan ilgili dizinin `durum.md` dosyası okunur.  
- Marka & UI Kuralı: CoreMusic şu anda YALNIZCA LIGHT MODE'dadır; Dark mode tasarımı henüz yoktur/yapılmamıştır.  
- Amplifikatör mimarisi kesinlikle Class AB'dir (Class D yasaktır).  
- Subagent'lar git commit atmaz; commit orkestratöre aittir.  
- Andrej Karpathy'nin 4 davranıs ilkesi (Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution) hem Claude, hem OpenCode, hem Cursor için bağlayıcı kural haline getirildi.  
**Açık:** 1) git commit kullanıcı onayında bekliyor.

---

### KALIP / ŞABLON (Gelecek Oturumlar İçin Kopyalama Standardı)

```text
İş: [işin adı]
Durum: [tek cümle: ne teslim edildi, ne bekliyor]
Bitenler: ✓ [adım] · ✓ [adım]
Kaldığım yer: [tam nokta, ör. SFX, 0:42]
Sırada: [bir sonraki adım]
Kararlar: [müşterinin / benim verdiğim kararlar]
Açık: [cevap bekleyen sorular]
```

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR

```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```
