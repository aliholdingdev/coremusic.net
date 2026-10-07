---
title: "CoreMusic — ADR-046: Cross-View State Preservation (filter/sort/search/scroll/page · 3 katmanlı kaynak: URL + sessionStorage + servis tercihi · geri/ileri uyumu · oturum sonu temizliği · durum yoksa domain varsayılanı)"
type: "architecture-decision"
category: "frontend"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic cross-view state preservation: (a) korunan durum kümesi filter + sort + search + scroll position + page — domain ve görünüm değişiminde korunur (ADR-045'e bağımlı), (b) 3 katmanlı kaynak: URL birincil (paylaşılabilir + geri/ileri), sessionStorage oturum-içi, servis tarafı tercih (hesap), (c) geri/ileri (popstate) ile tam durum + scroll geri yükleme, (d) gizlilik: oturum sonu temizliği (ADR-011), (e) fallback: durum yoksa domain varsayılanı (ADR-045)"
kaynak: "Kullanıcı onaylı kapsam a-e + disk/kod kanıtı taraması (2026-09-29: Router.js:54,63-64 manual scrollRestoration + boot'ta query-strip; ScrollManager kaydet/geri-yükle ayrışması; nav:complete canlı ağaçta 0 yayınlayıcı; filter/sort/page URL 0; sessionStorage yalnız 2 UI-dışı anahtar; SidebarCache.clearAll() 0 çağrı; sunucu tarafı session destroy IMPLEMENTED) + web araştırması (6 sorgu / ≈35 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-046: Cross-View State Preservation (Görünüm-arası Durum Koruma)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam a-e) · **Tarih:** 2026-09-29 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-046-cross-view-state-preservation`
> **İlgili kararlar:** [[ADR-045-multi-domain-view-mode-architecture]] (doğrudan öncül — görünüm/aralık durumunun URL birincil kalıcılık sözleşmesi) · [[ADR-004-multi-domain-spa]] (subdomain SPA iskeleti) · [[ADR-021-spa-router-immutable-contract]] (router immutable contract — durum parametreleri route'u değiştirmez) · [[ADR-016-url-normalization]] (URL normalizasyonu — varsayılanların temizlenmesi) · [[ADR-011-session-management]] (oturum yaşam döngüsü + oturum sonu temizliği) · [[ADR-018-footer-player-vaporwave]] (görsel dil + odak kaybı geçmişi) · [[ADR-001-vanilla-js-itcss]] (framework yasağı — durum deposu yerel yazılır) · [[ADR-005-ultrathink-protocol]] (zero hallucination — §1.1 dürüst etiket) · [[../index.md]] (`:88` slug satırı) · [[../../raw/brain.md]]
> **Ad gerekçesi:** slug `ADR-046-cross-view-state-preservation` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:88` — "Cross-View State Preservation") — uydurulmadı. Bu numara vault'ta **daha önce düz metin olarak** geçiyordu: `.ai/architecture/k10-uygulama/README.md:318` ve ADR-045'in §1.1-F/§6 kayıtları ("ADR-046 dosyası YOK") → bu dosya boşluğu doldurur.
> **Düzeltme ertelendi:** `[[../index.md]]:88` satırındaki `[[../../raw/brain.md]] ADR-046-…` biçiminin `[[accepted/ADR-046-cross-view-state-preservation]]` olarak düzeltilmesi **bir sonraki vault reset'ine ertelenmiştir** (bu işlemde index.md'ye dokunulmadı — onaysız dosya/satır değişikliği yok). Ayrıntı: §5.1 adım 9. **Rapor:** index.md:88 düzeltmesi YAPILMADI, ertelendi.
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.
> **Önkoşul:** Bu karar ADR-045'in (b) kaleminin filter/sort/search/scroll/page alanına **genişletilmesidir**; ADR-045 uygulanmadan tek başına anlamlı değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **ÇELİŞKİ** = iki kayıt birbiriyle uyuşmuyor · **KAPSAM DIŞI** = ilgili alanda hiç kod yok (hiçbiri yumuşatılmadı).

#### A) Scroll (kaydetme / geri yükleme) — kaydetme IMPLEMENTED, geri yükleme ölü

| Kanıt | İçerik | Etiket |
|---|---|---|
| `assets.coremusic.net/js/router/Router.js:54` | `history.scrollRestoration = 'manual'` — tarayıcı otomatik scroll geri yüklemesi **kapalı**, sorunluluk uygulamada | **IMPLEMENTED** |
| `js/router/ScrollRestorer.js` | Yalnızca `scrollToTop` içerir; pozisyon **geri yükleme (restore) yok** | **IMPLEMENTED** (tek yön) / **PLANNED** (restore) |
| `js/router/NavigationOrchestrator.js:71` | Her navigasyonda `scrollRestorer.scrollToTop()` → sayfa her geçişte **başa döner** | **IMPLEMENTED** |
| `js/features/ScrollManager.js` | Yol adına (pathname) göre **bellek-içi Map**'te scroll kaydı (scroll olayında) + `nav:complete` olayında geri yükleme | **IMPLEMENTED** (kaydetme) / **ÖLÜ YOL** (geri yükleme) |
| `nav:complete` yayınlayıcı | Bu olay yalnız `js copy/router/SPARouterAdapter.js:113`'te yayılıyor; **canlı** `js/router/` ağacında `SPARouterAdapter.js` **yok**, `.emit(` **0** → `ScrollManager` geri yükleme kodu **hiç çalışmıyor** (kaydetme çalışır, geri yükleme tetiklenmez) | **ÇELİŞKİ** |
| `js/main.js:106-108` · `CoreMusicApp.js:79` | `ScrollManager` instantiate + register | **IMPLEMENTED** (kayıt) |

#### B) History API / geri-ileri (popstate) + **boot'ta query-strip**

| Kanıt | İçerik | Etiket |
|---|---|---|
| `js/router/HistoryManager.js:24,33` | `pushState` / `replaceState` sarmalayıcıları | **IMPLEMENTED** |
| `js/router/RouterEventManager.js:10` · `js/main.js:142` | `popstate` dinleyicileri (geri/ileri yakalama) | **IMPLEMENTED** |
| `js/router/Router.js:63-64` | **Boot'ta query-strip:** sayfa açılışında `location.search` varsa (yalnız `auth_key=` hariç tutulur) `history.replaceState(null, '', pathname)` çağrılır → **tüm query parametreleri silinir** | **ÇELİŞKİ** (URL birincil katmanı boot'ta kırılır — karar (b) ile doğrudan çatışır) |
| Geri/ileri anında durum + scroll geri yükleme | `popstate` sonrası filter/sort/page/scroll değerlerini URL'den geri uygulayan kod **0** | **PLANNED** |

#### C) Filter / sort / search / page — URL durumu ve API

| Kanıt | İçerik | Etiket |
|---|---|---|
| Canlı JS `filter=\|sort=\|order=\|page=\|offset=\|cursor=` | Kod tabanında **0 isabet** — hiçbir liste/filter durumu URL'ye yazılmıyor | **PLANNED** |
| `js/router/UrlUtils.js:31` | `URLSearchParams` yalnız OAuth `redirect_uri`/`auth_key` okumalarında | **IMPLEMENTED** (auth) / **KAPSAM DIŞI** (liste durumu) |
| `shared/tests/.../ReturnUrlPolicyEdgeCaseTest.php:136` | `/home?page=2` gibi query'li URL'lerin policy tarafından **izinli** olduğu test edilmiş | **IMPLEMENTED** (test — router query'li URL'yi reddetmiyor) |
| API sayfalama | `PaginationResponse.php`, `MusicSearchRequest.php` (`page`/`pageSize`), `MobileBff.php:83-86`, `ApiResponse.php:90` | **IMPLEMENTED** (API) / **PLANNED** (frontend URL — API `page` biliyor, tarayıcı URL'si bilmiyor) |
| `?view=` (ADR-045) | 0 hit (ADR-045 §1.1-C kanısı) | **PLANNED** |

#### D) Depo katmanları (sessionStorage / localStorage)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `sessionStorage` genel toplamı | Yalnız 2 anahtar: `cm_tier_sync_count` (`js/device-loader.js:356-367`) + `cm_welcome_dismissed` (`js/device-layout-updater.js:480`) — **ikisi de UI gezinim durumu değil** | **IMPLEMENTED** (anahtarlar) / **PLANNED** (görünüm/filtre durumu sessionStorage'da **yok**) |
| `localStorage` — `SidebarCache` | `js/managers/SidebarManager.js:40-98`: anahtar `prefix+userId`, alanlar `albumView:'list'`, `albumSort:'name'`, `lastVisit` | **IMPLEMENTED** |
| `SidebarCache.clearAll()` | `SidebarManager.js:74-82` **tanımlı**; kod tabanında **çağrı 0** → istemci tarafı çıkış temizliği **yok** | **IMPLEMENTED** (tanım) / **PLANNED** (çağrı) |
| Diğer localStorage | `cm_gender`, welcome-modal anahtarları — kalıcı tercih kalıbı | **IMPLEMENTED** |

#### E) Oturum sonu temizliği (gizlilik) — sunucu IMPLEMENTED, istemci 0

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Session/SessionBootstrapper.php:38-41` | Oturum başlatma/yaşam döngüsü | **IMPLEMENTED** |
| `shared/src/PageRouter/SessionInitializer.php:67-69` | Sayfa rotası oturum başlatması | **IMPLEMENTED** |
| `auth.coremusic.net/include/Middleware/SessionMiddleware.php:82-88` | Çıkışta `$_SESSION = []; session_destroy()` | **IMPLEMENTED** (sunucu) |
| İstemci depolarının temizliği (localStorage/sessionStorage) | Çıkış akışında `clear()` / `clearAll()` çağrısı **0** | **PLANNED** (boşluk) |

#### F) Erişilebilirlik — odak ve duyuru (navigasyon)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `js/…/FocusManager.js:16,24` | Navigasyonda odak yönetimi + `aria-live="polite"` duyuru | **IMPLEMENTED** (ADR-018 hizası) |
| Scroll/odak kaybı vakası | Göç (migrasyon) kayıtları: ADR-018 "footer player odak kaybı" geçmişi — geri/ileri navigasyonda odak+scroll sözleşmesi **test edilmemiş** | **PLANNED** (test) |

#### G) Vault kayıtları (bu dosyadan önceki iddialar)

| Kayıt | İçerik | Etiket |
|---|---|---|
| `.ai/.decisions/index.md:88` | `[[../../raw/brain.md]] ADR-046-cross-view-state-preservation \| Cross-View State Preservation \| Frontend` — dosya **yoktu** | **ÇELİŞKİ** (bu dosya kapatır; satır düzeltmesi §5.1 adım 9'da ertelendi) |
| `.ai/architecture/k10-uygulama/README.md:318` | ADR-046 (cross-view state) düz metin | DOĞRULANDI (bu dosya ile doldu — README'ye dokunulmadı) |
| `ADR-031-mobile-strategy-pwa-flutter` (`:166`, ADR-045 §6 kanısı) | ADR-045/046 görünüm konsepti referansı | DOĞRULANDI (ADR-045 raporu) |
| `assets.coremusic.net/js copy/` | Canlı `js/` ile çift kaynak (SPARouterAdapter dahil) — drift yüzeyi | **ÇELİŞKİ** (yalnız canlı ağaç kanıt sayılır) |

> **Bulgu özeti:** Scroll **kaydetme var ama geri yükleme ölü** (`nav:complete` canlıda 0 yayınlayıcı), **her navigasyonda başa dönme** var (`ScrollRestorer.scrollToTop`); **URL durumu sıfır** (filter/sort/page 0 hit) ve **boot'ta query-strip** URL katmanını daha açılışta siliyor (`Router.js:63-64`); sessionStorage'da UI durumu yok (yalnız 2 anahtar); istemci çıkış temizliği yok (`clearAll()` 0 çağrı) ama sunucu temizliği sağlam; API sayfalamayı biliyor, tarayıcı URL'si bilmiyor. Bu ADR bu altı boşluğu tek karar altında kapatır.

### §1.2 Sorun Tanımı (Problem)

Kullanıcı bir listede filtreledi/sıraladı/arama yaptı ve sayfaya geçti; sonra (1) başka bir domain'e ya da görünüme geçince (ADR-045 görünüm değişimi dahil) **tüm durum kaybolur** — filter, sort, arama terimi, sayfa ve kaydırma konumu geri gelmez; (2) **geri/ileri tuşları rota değiştirir ama durumu geri getirmez** — üstelik `history.scrollRestoration` manuele alındığı (`Router.js:54`) ve restore yalnız `scrollToTop` olduğu için kaydırma konumu da her seferinde başa döner; (3) `ScrollManager` kaydı tutuyor ama geri yükleme tetikleyicisi (`nav:complete`) canlı ağaçta **hiçbir yerde yayılmıyor** → kayıt çalışır, geri yükleme **ölü kod**; (4) URL'de durum yaşamadığı için **filtreli/sıralı liste paylaşılamaz**, "şu sıralamayla bak" bağlantısı imkânsız; (5) bunun üstüne **boot'taki query-strip** (`Router.js:63-64`) açılışta herhangi bir query parametresini siliyor — URL katmanı daha ilk adımda kırılır; (6) durum üç ayrı depoda (URL, oturum, hesap) tutulmaya başlarsa **drift** ve **gizlilik sızıntısı** (linkte görünen arama terimi, çıkışta silinmeyen localStorage) riskleri doğar. Karar, bu altı sorunu **tek durum sözleşmesi** altında: neyin korunacağı, hangi katmanda yaşayacağı, geri/ileri ve oturum sonu davranışı ve varsayılanlara düşüş.

### §1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | 6 sorgu: (1) "store UI state in URL query parameters filter sort search page SPA best practices" (2) "browser back forward scroll position restoration SPA router manual scrollRestoration best practices" (3) "sessionStorage vs localStorage UI state persistence per tab session UX" (4) "logout clear client storage Clear-Site-Data session end cleanup privacy GDPR best practices" (5) "pagination URL parameter page SEO best practices Google" (6) "state drift multiple sources of truth URL localStorage server preferences resolution order" |
| Web Search **Konusu** | URL'de durum saklama (filter/sort/search/sayfalama), geri-ileri + scroll geri yükleme (manual `scrollRestoration`), sessionStorage oturum-içi kalıcılık ve tab özerkliği, oturum sonu temizliği (`Clear-Site-Data` / çıkış), sayfalama `page` parametresinin SEO'su, çok katmanlı depoda state drift'i ve çözüm sırası |
| Web Search **Bağlam** | Karar, CoreMusic'in mevcut kod gerçeğiyle (Router.js:54 manual scrollRestoration · Router.js:63-64 boot'ta query-strip · ScrollManager ölü geri-yükleme yolu · URL filter/sort/page 0 · sessionStorage 2 UI-dışı anahtar · istemci çıkış temizliği 0) yüzleşirken ADR-045'in URL-öncelikli kalıcılık kararını filter/sort/search/scroll/page'a genişletmeli; ADR-001 (framework yasağı), ADR-004/021/016 (router zinciri) ve ADR-011 (oturum) ile hizalı kalmalı. Araştırma 2026-09-29'da yapıldı; protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (6 sorgu, ≈35 adlandırılmış kaynak, MDN/W3C/Google resmi kaynakları öncelikli) |
| Web Search **Kısa Açıklama** | URL'de tutulan durum **paylaşılan, yer imine konan ve geri/ileri ile gezilebilen** tek gerçekliktir; sessionStorage **tab-özerk ve kapanınca silinir** (oturum-içi ideal), localStorage kalıcıdır ama çıkışta **manuel temizlik** ister; `history.scrollRestoration = 'manual'` ile tarayıcının kendi restore'ı devre dışı bırakıldıysa **pozisyonu uygulamanın kendisi geri yüklemek zorundadır**; paylaşılan URL, linkte görünen arama/filtre terimi yüzünden bir **gizlilik yüzeyi** de olur |
| Web Search **Uzun Açıklama** | Kaynaklar beş eksende buluşuyor. (i) **URL-as-state:** URL yalnız adres değil, **durumun kaynağıdır** — `?filter=&sort=&page=` ile durum paylaşılır, yer imine konur ve yönlendirme motorundan (router) ayrı bir "state" katmanı olarak tutulur; en dayanıklı desen URL birincil + yerel depo fallback'idir (Differ "URL as state", LogRocket ×2, Lorenzo GM, dev.to, front-end.tips, TanStack Router, Type Route, vuejs-ai skills rehberleri). (ii) **Scroll geri yükleme:** tarayıcıların otomatik `history.scrollRestoration`'ı SPA rotalarında güvenilir değildir; `manual`'e alınıysa kaydırma konumunu kaydedip geri yüklemek uygulamanın sorumluluğundadır — Chrome'un scroll restoration yazısı ve MDN `scrollRestoration` dokümanı bu ikiliyi (kaydet + route değişiminde uygula) doğrular; Gatsby ve frontend-routing rehberleri "navigasyonda scroll top'a git, geri/ileride ise kaydet" kuralını tekrarlar (MFA11y odak/scroll geri yükleme yazısı, codeslog). (iii) **Depo seçimi:** sessionStorage tab oturumuna bağlıdır ve kapanınca silinir; localStorage kalıcıdır ve kullanıcı çıkmadan da **aynı-origin JS tarafından okunabilir** — bu yüzden oturum-içi geçici durum (filtre sayfası, kaydırma) sessionStorage'a, kalıcı tercihe localStorage'a konur (StackOverflow, ccdatalab, rdegges, Front-End Checklist GDPR notları). (iv) **Oturum sonu temizliği:** W3C `Clear-Site-Data` başlığı (`"storage"`, `"cache"`) çıkışta tüm istemci depolarını silmenin standart yoludur; web.dev sign-out rehberi bunu sunucu `session_destroy()` ile **birlikte** ister — sunucu temizliği tek başına yeterli değildir, istemci deposu da temizlenmelir. (v) **Sayfalama + SEO:** `page` parametresi URL'de yaşar (Google Search Central pagination, Nuxt SEO) — durum URL'de olduğu için geri/ileri ile sayfa da geri gelir. Ek bulgu: **state drift** (evilmartians / sure-state / symphony-state) — birden fazla depo varsa net bir **çözüm sırası** ve tek yazıcı yoksa kullanıcı "ayarım neden geri geldi" der; ayrıca WCAG 2.4.11 (odak örtülmemeli) ve Vispero/MFA11y notları, geri/ileri sonrası **odak + scroll** birlikte geri yüklenmezse ekran okuyucunun baştan başladığını vurgular (ADR-018 zeminir). |
| Web Search **Paragraf Veri Uzun** | Görünüm-arası durum koruma, üç soruyu aynı anda yanıtlamalıdır: **durum nerede yaşar** (paylaşılabilir mi?), **geçmiş nasıl gezilir** (geri/ileri durumu ve kaydırmayı geri getirir mi?) ve **ne zaman silinir** (oturum/çıkış sonunda iz kalır mı?). Birinci sorunun cevabı web'de tek:pointer: URL. `?q=&sort=&page=` taşıyan bağlantı kopyalandığında alıcı aynı listeyi görür; yer imi ve çok-sekme tutarlılığı da aynı kaynaktan gelir (Differ, LogRocket, TanStack Router). İkinci soru, CoreMusic'in bugünkü en somut açığına değiniyor: `Router.js:54` manual scrollRestoration ile tarayıcının restore'ı kapatılmış, ama karşılığında yazılan `ScrollRestorer` yalnız `scrollToTop` yapıyor ve `ScrollManager`'in geri yükleme tetikleyicisi canlı ağaçta hiç yayılmıyor — yani kullanıcı geri tuşuna bastığında hem filtre hem kaydırma konumu kaybolur; MDN ve Chrome kaynakları, manual modda kaydet-ve-geri-yüklemenin uygulama zorunluluğu olduğunu, aksi halde restore'un sessizce kaybolduğunu söylüyor. Üçüncü soru gizlik tarafı: URL'ye yazılan durum **herkesin gördüğü durumdur** — paylaşılan linkte arama terimi, hassas filtre kümesi veya kişisel liste görünür olur; bu yüzden PII/sır URL'ye yazılmaz, oturum-içi hassas durum sessionStorage'a konur ve W3C Clear-Site-Data + sunucu `session_destroy()` ile oturum sonunda her iki katman da temizlenir. Son olarak drift: üç depo (URL > sessionStorage > servis tercihi) aynı anda canlıysa çözüm sırası sabitlenmezse ilk navigation'da bozulur — bu, ADR-045'in "URL kazanır" kuralının bu ADR'ye de uygulanmasını zorunlu kılar. |
| Web Search **Sonucu** | 6 sorgu ≈ **35 adlandırılmış kaynak** (~6/sorgu); beş kanonik destek: (i) URL birincil durum + geri/ileri gezinme (Differ, LogRocket ×2, Lorenzo GM, dev.to, front-end.tips, TanStack Router, Type Route), (ii) manual scrollRestoration'da kaydet-ve-geri-yüklemenin uygulama sorumluluğu (MDN scrollRestoration, Chrome blog, Gatsby, frontend-routing rehberleri, MFA11y, codeslog), (iii) sessionStorage tab-özerk/oturum-içi + localStorage kalıcı ama çıkışta temizlenmeli (StackOverflow, ccdatalab, rdegges, Front-End Checklist GDPR), (iv) `Clear-Site-Data: "storage"` + `session_destroy()` ikilisi (W3C, web.dev sign-out), (v) `page` URL'de + pagination SEO (Google Search Central, Nuxt SEO). **Olumsuz/negatif bulgular da var:** URL durumu link üzerinden **bilgi sızdırır** (arama terimi görünür); fazla parametre **URL kirliliği** yaratır; `Clear-Site-Data` tüm origin deposunu siler (dikkatsiz uygulamada kullanıcı tercihleri de gider); tarayıcı restore'u ile uygulama restore'u **çakışırsa** çift-sıçrama (scroll thrash) olur. |
| Web Search **Alınan Karar** | Karar a-e kalemleri bu bulgularla sabitlendi: (a) **korunan durum kümesi** `filter` · `sort` · `search` · `scroll position` · `page` — domain ve görünüm (ADR-045) değişiminde korunur, (b) **3 katmanlı kaynak**: URL birincil (paylaşılabilir + geri/ileri), sessionStorage oturum-içi (tab-özerk, kapanınca silinir), servis tarafı tercih (girişli kullanıcıda hesap) — çözüm sırası `URL > sessionStorage > servis tercihi > domain varsayılanı`, (c) **geri/ileri uyumu**: `popstate` hem URL durumunu hem scroll konumunu geri yükler (manual scrollRestoration korunur), (d) **gizlilik**: oturum sonu temizliği — sunucu `session_destroy()` (IMPLEMENTED) + istemci `sessionStorage`/`localStorage` temizliği (`clearAll()` çağrısı + `Clear-Site-Data` hattı) ADR-011'e bağlanır; PII/sır URL'ye yazılmaz (REDACTED), (e) **fallback**: durum yoksa/bozuksa domain varsayılanı (ADR-045) — bozuk `page` → 1, tanınmayan `sort` → domain varsayılanı |
| Web Search **Sonuç** | Araştırma, kullanıcı onaylı kapsamı **destekledi ve iki şartı netleştirdi**: (1) URL katmanı **boot'taki query-strip** (`Router.js:63-64`) olmadan çalışmaz — strip, durum parametrelerini **koruyacak** şekilde değişmelidir; (2) geri/ileri sözleşmesi, `nav:complete` ölü yolunun canlı router'a bağlanmadan **kurulamaz** (bugün kaydetme var, geri yükleme yok). Yeni bir kütüphane gerekmez; mevcut `HistoryManager` + `ScrollManager` + `SidebarCache` üzerine sözleşme eklenir (ADR-001). |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 framework yasağı | Vanilla JS + ITCSS/BEM; durum deposu için Redux/Zustand/nuXvu gibi bir durum kütüphanesi veya `useState` eşdeğeri **yok** — URL + sessionStorage + yerel modül olarak kalır |
| ADR-004 / ADR-021 / ADR-016 router sözleşmesi | Durum parametreleri route'u **değiştirmez** (immutable contract): `/albums` rotası `?sort=` eklense de aynı rotadır; normalizasyon (ADR-016) ile domain varsayılanı olan parametreler URL'den **çıkarılır**. ⚠️ `ADR-083` (SPA router) **diskte dosya olarak YOK** (accepted/draft/rejected taraması boş) → düz metin + `⚠️ VERIFICATION REQUIRED`, wiki-link **yazılmaz** |
| ⚠️ Düzeltme — "ADR-005 router" | **ADR-005 router değildir**: diskte `ADR-005-ultrathink-protocol` (zero hallucination). Router karşılıkları **ADR-004 + ADR-021 + ADR-083 (dosya yok) + ADR-016** zinciridir; bu ADR bu dördü referanslar (ADR-045 §1.4 düzeltmesiyle aynı) |
| ADR-045 önkoşul | (e) fallback "domain varsayılanı" ve görünüm değişiminde durum koruma **ADR-045'e bağımlıdır**; ADR-045 uygulanmadan bu ADR kısmen uygulanır (yalnız filter/sort/page/scroll) |
| ADR-011 oturum yaşam döngüsü | Oturum sonu temizliği ADR-011'in alanıdır: sunucu `session_destroy()` zaten IMPLEMENTED; eklenen **istemci** temizliği bu ADR'de tanımlanır, oturum kimliği/cookie politikasına dokunulmaz |
| ADR-018 a11y | Geri/ileri sonrası odak + scroll kaybı WCAG 2.2 AA 2.4.11'i (odak örtülmemeli) etkiler; `FocusManager` + `aria-live` mevcut kalıbı korunur |
| ADR-001-037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs`; PowerShell write cmdlet'leri yasak |
| Hallucination disiplini | Diskte olmayan hedefe wiki-link **yazılmaz** (`ADR-083-*` → düz metin + `⚠️ VERIFICATION REQUIRED`) |
| REDACTED | Arama terimi/çerez/anahtar/kişisel veri hiçbir koşulda bu ADR'ye (veya örnek URL'lere) yazılmaz |

---

## §2 Karar (Decision)

CoreMusic, alanlar arası gezinimde **beş parçalı durumu** (filter, sort, search, scroll position, page) **tek Cross-View State Preservation sözleşmesi** altında korur: durum **üç katmanlı** bir kaynaktan gelir, **geri/ileri ile tam geri yüklenir**, **oturum sonunda temizlenir** ve **yoksa domain varsayılanına düşer**.

### §2.1 (a) Korunan Durum Kümesi — filter · sort · search · scroll · page

- **Küme (kapalı liste):** `filter` (süzgeç kümesi) · `sort` (sıralama alanı + yönü) · `search` (arama terimi) · `scroll position` (kaydırma konumu) · `page` (sayfa numarası / sayfalama imleci). Bunlar **domain değişirken** (ör. music → home) ve **görünüm değişirken** (ADR-045: liste ↔ grid ↔ kart) **korunur**.
- **Korunma bağlamı iki katman:** (1) **aynı sayfa içi** görünüm değişiminde durum hiç sıfırlanmaz (ADR-045 `?view=` yanına `?sort=` vb. yazılır, mevcut değerler ezilmez); (2) **route/domain değişiminde** durum URL'de taşınır ve geri dönüldüğünde yeniden uygulanır.
- **Kapsam dışı (bilinçli):** form içerikleri (kirli veri), oynatma konumu (player durumu ayrı alan), panel modu `home/pro/studio/car` (ADR-045 alt sözleşmesi), oturum/ kimlik verisi.
- **Küme genişletme kuralı:** yeni bir durum anahtarı ancak yeni ADR ile eklenir (bu küme ADR-046'nın SSOT'udur).

### §2.2 (b) 3 Katmanlı Kaynak — URL birincil · sessionStorage oturum-içi · servis tercihi

| Katman | Ne taşır | Kapsam | Neden |
|---|---|---|---|
| **1 — URL (birincil)** | `q` · `sort` · `order` · `filter` (imza) · `page` · `view` (ADR-045) | Paylaşılan, yer imi, geri/ileri, çok-sekme | Tek paylaşılabilir gerçeklik; router sözleşmesine tabi (route kimliği değişmez) |
| **2 — sessionStorage (oturum-içi)** | URL'ye yazılmayan geçici durum + scroll imleci desteği | Aynı tab, aynı oturum; **tab kapanınca silinir** | Tab-özerk, oturum-özel; localStorage'ın kalıcılığına ihtiyaç duymayan durum için |
| **3 — Servis tercihi (hesap)** | Girişli kullanıcının kalıcı tercihleri (ör. varsayılan sıralama) | Hesaplar arası + cihazlar arası | ADR-040 tek yazıcı + ADR-020 API; oturum süresi dolunca **tercih** kalır |
| **4 — Domain varsayılanı (fallback)** | Yok/bozuk durumda devreye giren domain tanımı | Her kullanıcı | ADR-045 domain matrisi (ör. müzik=liste, sıralama=ad) |

- **Çözüm sırası (tek formül):** `URL > sessionStorage > servis tercihi > domain varsayılanı`. **Çelişkide URL kazanır** (paylaşılan link her zaman haklıdır).
- **Yazma sırası:** kullanıcı durumu değiştirince **URL hemen** güncellenir (`history.replaceState` — sürekli geçişte `pushState` **yasak**, geçmiş kirlenmesin; yeni anlamlı gezinmede router `pushState` kullanır), sonra ilgili depo (sessionStorage veya — girişliyse — servis tercihi).
- **URL yazım kuralı:** domain varsayılanı olan parametreler URL'ye **yazılmaz** (ADR-016 normalizasyonu ile temizlenir) → URL kısa kalır, kirlilik riski düşer.
- **Tek yazıcı:** durum geçişleri tek modülde (`StatePreserver` — §5.1 adım 2) toplanır; birden fazla yer `replaceState` çağıramaz (drift önleme).

### §2.3 (c) Geri / İleri Uyumu (popstate + scroll)

1. **`popstate` her geri/ileride:** URL'deki durum kümesini okur, DOM'a uygular (filter/sort/search/page) — eski duruma **tam** dönüş.
2. **Scroll:** `history.scrollRestoration = 'manual'` (`Router.js:54`) **korunur**; `ScrollManager`'in kaydettiği konum, **canlı router'ın gerçek olayında** geri yüklenir — `nav:complete` ölü yolunun (yalnız `js copy/`te var) canlıya bağlanması **zorunlu önkoşuldur** (§5.1 adım 3). Yeni navigasyonda davranış: rota değişimi → tepe (`scrollToTop` korunur), geri/ileri → **kayıtlı konum**.
3. **Odak:** geri/ileri sonrası odak, `FocusManager` + `aria-live="polite"` ile anlamlı birinci gruba taşınır (ADR-018 hizası; WCAG 2.2 AA 2.4.11).
4. **Boot uyumu:** `Router.js:63-64` query-strip, **izinli durum parametreleri korunacak** biçimde değişir: `auth_key` gibi bilinmeyen/tehlikeli parametreler + bilinmeyen anahtarlar temizlenir, durum kümesi (`q/sort/order/page/filter/view`) **silinmez** (aksi halde (b) katmanı boot'ta ölüdür).
5. **Test kapısı:** paylaşılan link = orijinal görünüm-eşdeğer liste; geri → tam durum + konum; ileri → aynı; bu üçü olmadan karar uygulanmış sayılmaz (§5.1 adım 8).

### §2.4 (d) Gizlilik — Oturum Sonu Temizliği (ADR-011)

- **Sunucu (IMPLEMENTED — korunur):** `session_destroy()` yolu (`SessionMiddleware.php:82-88` vb.) değişmez.
- **İstemci (eklenir):** çıkışta `sessionStorage` (oturum-içi durum) ve oturum-özel `localStorage` anahtarları temizlenir — `SidebarCache.clearAll()` (`SidebarManager.js:74-82`, bugün **0 çağrı**) çıkış akışına **çağrılır**; silinmeyecek kalıcı tercihler (tema, dil) açık listeyle korunur.
- **URL gizliliği:** PII/sır **asla** URL'ye yazılmaz (REDACTED); hassas/kişisel süzgeç kümesi URL'ye değil `sessionStorage`'a konur — paylaşılan link sadece **gösterilebilir** durumu taşır.
- **Standart hedef:** çıkış yanıtında `Clear-Site-Data: "storage"` başlığı (W3C) değerlendirilir — sunucu temizliği tek başına yeterli değildir (web.dev). ⚠️ Başlığın tam uygulanması oturum politikasıyla kesişir → ADR-011 kapsamında Security Engineer doğrulaması gerekir.
- **Depolama bütçesi:** yalnız anahtar-değer durum saklanır; liste/veri gövdeleri sessionStorage'a **yazılmaz** (kota + privacy).

### §2.5 (e) Fallback — Durum Yoksa Domain Varsayılanı (ADR-045)

| Durum | Davranış |
|---|---|
| Parametre hiç yok (temiz giriş) | Domain varsayılanı (ADR-045 matrisi) — sessiz, hatasız |
| Parametre bozuk (`page=abc`, `order=xyz`) | En yakın geçerli değere normalize; geçersizse domain varsayılanı — **hata ekranı yok** |
| `page` sayfa sınırı dışında | En yakın sınıra çekilir (0/negatif → 1) |
| sessionStorage boş / silinmiş | Sessizce URL'ye, o da yoksa varsayılan'a düşer |
| Servis tercihi ile URL çelişir | **URL kazanır** (paylaşılan link her zaman haklı) |
| Üç katman da boş | Domain varsayılanı — ADR-045'e tam uyum |

### §2.6 Neden Bu Seçenek?

Kod yarısını çoktan yapmış: `HistoryManager` (push/replace), `ScrollManager` (kaydetme), `SidebarCache` (localStorage kalıbı), `FocusManager` (odak+düyuru) ve API'de sayfalama var. Eksik olan **sözleşme**: hangi durumun korunacağı, nerede yaşayacağı ve geri/ileride ne geri geleceği. Yeni bir durum kütüphanesi ADR-001'i ihlal eder ve geri/ileri problemini çözmez (tarayıcı restore'u manuel modda zaten devre dışı); URL-öncelikli 3 katman + geri/ileri sözleşmesi mevcut yatırımı ADR-004/016/021/045 çerçevesinde tamamlar.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Yalnız localStorage ile durum** (URL yok) | Kod en yakını (SidebarCache zaten böyle), sıfır router işi | Durum paylaşılamaz, geri/ileri klasik SPA sorununda kalır, çok-sekme tutarsız, SEO'suz `page` | Kapsam (b) URL'yi **birincil** yapar; web araştırmasının da tek pointer'ı URL (Differ/LogRocket/TanStack) |
| 2 | **Yalnız URL (sessionStorage/servis yok)** | Tek gerçeklik kaynağı | Oturum-içi hassas süzgeç URL'ye sızar (privacy), tab kapanınca destek durumu yok, hesaplar arası tercih taşınmaz | Üç katman tek formülde toplandı (§2.2); katmanlar çatışmaz, çözüm sırası sabit |
| 3 | **Hazır durum kütüphanesi** (Redux/Zustand benzeri) | Olgun, devtools, middleware | ADR-001 framework/bağımlılık yasağı, CSP/yüzey artışı, **geri/ileri + scroll'u çözmez** (router işi ayrıca kalır) | Bağımlılık artışı sorunu çözmeden taşır; yerel `StatePreserver` modülü yeterli |
| 4 | **`scrollRestoration`'ı tekrar `'auto'` yap (uygulama restore'u bırak)** | Tarayıcıya devret, kod azalır | Rota kimliği + DOM farklıyken tarayıcı restore'u yanlıştır (MDN/Chrome: SPA'da güvenilmez); double-jump riski | Manuel mod **korunur**; sorumluluk uygulamada kalır (karar (c)) |
| 5 | **Durum yalnız oturum/`$_SESSION`'da** (URL yok) | Sunucu tek nokta, paylaşım yok → privacy "rahat" | Paylaşımlı link yok, geri/ileri URL'siz gezinir, her navigation sunucu durumu kurbanı | Kapsam (a+c) paylaşılabilirlik ve geri/ileriyi şart koşar; servis tercihi 3. katman olarak zaten var |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Filtreli/sıralı/sayfalı liste **paylaşılabilir** (`?q=&sort=&page=`) — link alan da aynı listeyi görür; yer imi ve çok-sekme tutarlılığı tek kaynaktan gelir.
- Geri/ileri tuşu **durumu da geri getirir** (bugün yalnız rotayı) + scroll konumu kayıp kalmaz → "geri basınca her şey sıfırlandı" şikâti kapanır.
- `ScrollManager`'ın ölü geri-yükleme yolu **canlanır** (nav:complete canlıya bağlanır) — mevcut kod çöp olmaktan çıkar (ADR-001).
- Oturum sonu temizliği **iki taraflı** olur: sunucu (IMPLEMENTED) + istemci (`clearAll()` çağrısı) → GDPR/privacy açığı kapanır (ADR-011).
- Durum yoksa **sessiz domain varsayılanı** → hata ekranı yok, ADR-045 ile birebir uyum.
- API'deki sayfalama (`page`/`pageSize`) tarayıcı URL'siyle eşleşir → frontend ve API aynı sayfalama sözleşmesini konuşur.

### 4.2 Olumsuz Sonuçlar

- URL yazımı + üç depo eşzamanlı canlı tutulur → **drift** ve beklenmedik varsayılanlara dönüş riski (mitigasyon §4.3-2).
- `Router.js:63-64` query-strip değişir → boot davranışı değişir; yanlış listede parametre **silinirse** URL katmanı, yanlış temizlenirse **kirli URL** kalır (regresyon yüzeyi).
- Scroll restore eklenirken çift-sıçrama (kaydet + otomatik restore) olabilir → scroll thrash riski.
- Paylaşılan URL artık **bilgi yüzeyidir** (arama terimi görünür) — privacy review gerekir.
- `Clear-Site-Data` geniş uygulanırsa kullanıcının **kalıcı tercihleri de** silinebilir → beyaz liste şart.
- Oturum-özel anahtar sayısı artar → localStorage/sessionStorage envanteri büyüyüp denetimi zorlaşır (etiketleme disiplini gerekir).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **URL kirliliği**: her filter/sort geçişi URL'yi şişirir, router ile yarışır | 3 (Olası) | Orta (okunabilirlik/dağıtım) | Domain varsayılanı URL'ye **yazılmaz/çıkarılır** (ADR-016), yalnız anlamlı fark yazılır, sürekli değişimde `replaceState` (pushState yasak), param anahtarları sabit ve az (`q/sort/order/page/filter`) |
| 2 **State drift**: URL ↔ sessionStorage ↔ servis tercihi çelişir | 4 (Çok olası) | Yüksek (güven kaybı) | Tek çözüm sırası (§2.2: URL kazanır) + tek yazıcı modül + yazma sırası (URL önce) + giriş anında tek yönlü taşıma |
| 3 **Privacy leak**: paylaşılan URL'de arama/süzgeç görünür; sessionStorage same-origin JS'e açık; çıkışta silinmeyen localStorage | 3 (Olası) | Yüksek (KVKK/GDPR) | PII/sır URL'ye yazılmaz (REDACTED), hassas süzgeç → sessionStorage; çıkışta `clearAll()` + `Clear-Site-Data` değerlendirmesi (ADR-011 + Security doğrulaması); depo envanteri + etiket |
| 4 **Geri/ileri kırılması**: boot query-strip (Router.js:63-64) durum parametrelerini siler; popstate'te durum uygulanmaz | 4 (Çok olası) | Yüksek (karar uygulanamaz) | Strip sadece bilinmeyen parametreleri temizler, durum kümesi korunur (§2.3-4); popstate geri-yükleme testi (§5.1 adım 8) **kapı** |
| 5 **Scroll restore ölü yol**: `nav:complete` canlı ağaçta 0 → kayıt var, geri yükleme yok | 4 (Çok olası) | Orta (beklenti karşılanmaz) | Canlı router'a olay bağlama (§5.1 adım 3) + geri/ileri scroll testi; `js copy/` kanıt sayılmaz |
| 6 **Scroll thrash / çift-sıçrama** ve odak kaybı (WCAG 2.4.11) | 3 (Olası) | Orta (a11y + UX) | Tek restore noktası (StatePreserver), `scrollToTop` yalnız yeni rotada; odak `FocusManager` + `aria-live` ile geri bağlanır, test zorunlu |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **URL yazımı kapanırsa** sistem bugünün davranışına döner: sessionStorage/servis tercihi varsa onlar, yoksa domain varsayılanı — veri silinmez.
2. **sessionStorage katmanı eklenmezse** çözüm sırası `URL > servis tercihi > varsayılan` olarak daralır; karar (a) yine karşılanır (URL + tercih).
3. **İstemci temizliği eklenmezse** sunucu `session_destroy()` (IMPLEMENTED) tek başına kalır — bu ADR'nin (d) kalemi **karşılanmaz sayılır**, açık kabul edilir ve §5.1'e geri alınır.
4. **Query-strip değişikliği** geri alınabilir: `Router.js:63-64` eski hâline döner, URL katmanı boot'ta yine silinir — yalnız (b)+(c) kısmen devre dışı kalır, başka hiçbir şey kırılmaz.
5. **Scroll restore** bayrakla kapatılır: `ScrollManager` kaydetmeye devam eder, geri yükleme pasifleşir; `scrollToTop` yolu zaten IMPLEMENTED.
6. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Boot query-strip kararı:** `Router.js:63-64` — durum kümesi (`q/sort/order/page/filter/view`) **korunur**, bilinmeyen/tehlikeli parametreler + `auth_key` akışı değişmeden temizlenir; kapı: boot sonrası `?q=` **silinmiyor** | UI Designer + Backend | 0.5 gün |
| 2 | **StatePreserver modülü:** tek yazıcı — URL okuma/yazma (`replaceState`), çözüm sırası `URL > sessionStorage > servis tercihi > varsayılan`, default temizliği (ADR-016), bozuk değer normalize (§2.5 tablosu) | UI Designer | 1.5 gün |
| 3 | **Scroll hattı:** `nav:complete` (veya eşdeğer router olayı) **canlı** `js/router/` ağacında yayılır (yalnız `js copy/SPARouterAdapter.js:113`te kalmaz) → `ScrollManager` geri yükleme çalışır; davranış: yeni rota = `scrollToTop`, geri/ileri = kayıtlı konum; `scrollRestoration='manual'` korunur | UI Designer | 1 gün |
| 4 | **sessionStorage katmanı:** oturum-içi durum anahtarları (`cm.state.<route>` deseni); yalnız anahtar-değer (liste gövdesi yasak); tab kapanınca kendiliğinden silinir | UI Designer | 0.5 gün |
| 5 | **Servis tercihi katmanı:** girişli kullanıcıda kalıcı tercih (ör. varsayılan sıralama) hesaba yazılır/okunur — ADR-040 tek yazıcı + ADR-020 API deseni; `user_preferences` şema etkisi Data Engineer onaylı | Backend + Data | 1 gün |
| 6 | **Oturum sonu temizliği:** çıkış akışında `SidebarCache.clearAll()` çağrısı (bugün **0**) + oturum-özel sessionStorage anahtar temizliği + `Clear-Site-Data` başlığının ADR-011 kapsamında değerlendirilmesi; kalıcı tercih beyaz listesi | Security + UI | 1 gün |
| 7 | **Geri/ileri a11y:** popstate sonrası odak + `aria-live` duyuru (`FocusManager.js:16,24` kalıbı) + odak görünürlüğü (WCAG 2.2 AA 2.4.11) | UI + QA | 0.5 gün |
| 8 | **Testler (kapı):** (i) paylaşılan link = aynı liste, (ii) geri → tam durum + scroll, (iii) ileri → aynı, (iv) varsayılan/bozuk fallback, (v) drift çözüm sırası, (vi) logout temizliği (depo 0 kalıcı oturum anahtarı). Test yok → karar uygulanmış sayılmaz | QA | 1.5 gün |
| 9 | **Dizin düzeltme (ERTELENDİ — onay gerekiyor):** `.ai/.decisions/index.md:88` `[[../../raw/brain.md]] ADR-046-…` satırının `[[accepted/ADR-046-cross-view-state-preservation]]` biçimine düzeltilmesi **bir sonraki vault reset'ine ertelenmiştir** — bu işlemde index.md'ye dokunulmadı (kural: onaysız satır değişikliği yok) | Vault Steward | 0.1 gün |
| 10 | **Debate (persona turları)** tamamlanır → §7'deki Debate/Tech Lead `⏳` satırları güncellenir | Vault Steward | 0.5 gün |

**Toplam ≈ 9.1 gün** (fazlar tekrarlanabilir; adım 8 kapı olmadan yayına çıkılmaz).

### §5.2 Geri Dönüş Planı

1. **Adım 1 tersi:** `Router.js:63-64` orijinal iki satıra döner (git diff ile) → boot query-strip eski davranışına döner; durum URL'si tekrar açılışta silinir ama başka hiçbir şey kırılmaz.
2. **Adım 2/4 tersi:** `StatePreserver` bayrakla kapatılır (`preserve: false`) → `replaceState` çağrıları yok sayılır; mevcut davranış (durumsuz gezinme) otomatik geri gelir; **hiçbir depo silinmez**.
3. **Adım 3 tersi:** canlı router olay yayımı kaldırılır → `ScrollManager` geri yükleme tekrar ölü hâle gelir (bugünkü durum); `scrollToTop` yolu etkilenmez.
4. **Adım 5 tersi:** servis tercihi okuma/yazma kapanır; `user_preferences` şema değişikliği yapıldıysa `DROP COLUMN` (Data onayıyla) — PHP/JS tercih yoksa sessizce fallback'e düşer.
5. **Adım 6 tersi:** `clearAll()` çağrısı çıkış akışından çıkarılır; `Clear-Site-Data` başlığı eklenmediyse geri alınacak bir şey yoktur (yalnız rapor satırı).
6. **Adım 7/8 tersi:** a11y/test ekleri ayrı commit'lerdedir; geri alma testleri etkisiz kılmaz — davranış bayraklarla kapatılır.
7. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler (001-037) etkilenmez.

### §5.3 Debate Şartları (3 tur / 20 persona — KABUL; kayıt: §7.1)

> Debate çıktısıdır; §5.1 adımlarıyla birebir örtüşür — mevcut adımlar **değiştirilmedi**, yalnız bağlama netleştirildi.

| # | Şart | Karşılığı | Durum |
|---|------|-----------|-------|
| **1a** | **Boot query koruma:** `Router.js:63-64` query-strip, koruma listesiyle (`q/sort/order/page/filter/view`) değişir; `auth_key` + bilinmeyen/tehlikeli parametre temizliği aynen kalır — boot sonrası durum parametresi **silinmiyor** | §5.1 adım 1 · §2.3-4 | AÇIK (uygulama kapısı) |
| **1b** | **Restore bağlama:** `nav:complete` (veya eşdeğer router olayı) canlı `js/router/` ağacında yayılır → `ScrollManager` geri yükleme canlı olaya bağlanır; yeni rota = `scrollToTop`, geri/ileri = kayıtlı konum | §5.1 adım 3 · §2.3-2 | AÇIK (uygulama kapısı) |
| **2** | **Temizlik entegrasyonu:** `SidebarCache.clearAll()` (bugün **0 çağrı**) çıkış/oturum sonu akışına bağlanır + oturum-özel sessionStorage anahtar temizliği; kalıcı tercih beyaz listesi korunur | §5.1 adım 6 · §2.4 | AÇIK (uygulama kapısı) |
| **3** | **Navigation/restore testi:** back/forward + scroll restore + drift çözüm sırası testleri eklenir — test yoksa karar uygulanmış sayılmaz | §5.1 adım 8 · §2.3-5 | AÇIK (uygulama kapısı) |

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../raw/AGENTS.md]] | Agent registry, onay/escalation §10, frozen kuralı §25.3 |
| [[../../raw/WORKFLOW.md]] | Süreçler, fazlar |
| [[../../raw/brain.md]] | Mimari karar özeti |
| [[../../index.md]] | Master katalog |
| [[../../raw/keys.md]] | Keyword haritası |
| [[../../raw/MEMORY.md]] | Session hafızası |
| [[../../log.md]] | Audit trail (append-only) |
| [[../../raw/glossary.md]] | Terimler (state preservation, scroll restoration, sessionStorage) |
| [[../index.md]] | Karar dizini — `:88` slug satırı (**düzeltme §5.1 adım 9'da ertelendi**) |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[../../architecture/k10-uygulama/README]] | `:318` ADR-046 düz metin referansı (bu dosya ile doldu — README'ye dokunulmadı) |
| [[ADR-001-vanilla-js-itcss]] | ITCSS 9 katman + framework yasağı — §2.6 yerel `StatePreserver` |
| [[ADR-004-multi-domain-spa]] | Subdomain SPA iskeleti — domain değişiminde durum koruma bağlamı |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination — §1.1 dürüst etiket (**router değildir**) |
| [[ADR-006-performance-targets]] | Kritik yol tavanları — URL/depo yazım maliyeti |
| [[ADR-011-session-management]] | Oturum yaşam döngüsü — §2.4 oturum sonu temizliği |
| [[ADR-016-url-normalization]] | URL normalizasyonu — domain varsayılanı temizliği |
| [[ADR-018-footer-player-vaporwave]] | Odak kaybı geçmişi — §2.3 geri/ileri odak+a11y |
| [[ADR-020-api-public-security]] | API güven katmanı — servis tercihi uçları |
| [[ADR-021-spa-router-immutable-contract]] | Router immutable contract — durum parametresi route'u değiştirmez |
| [[ADR-031-mobile-strategy-pwa-flutter]] | `:166` ADR-045/046 görünüm konsepti referansı |
| [[ADR-040-database-authority]] | DB tek yazıcı ilkesi — servis tercihi deposu |
| [[ADR-044-dynamic-user-theme-engine]] | Tercih/kalıcılık deseni — servis katmanı hizası |
| [[ADR-045-multi-domain-view-mode-architecture]] | **Doğrudan öncül** — URL birincil kalıcılık + domain varsayılanları (karar a/e) |
| [[../../../assets.coremusic.net/CLAUDE.md]] | Canlı JS envanteri — `js/router/`, `ScrollManager`, `SidebarCache` kanıtları |
| [[../../../shared/CLAUDE.md]] | Oturum/pagination PHP kanıtları (SessionInitializer, PaginationResponse) |
| §2.2 Çözüm Sırası | `URL > sessionStorage > servis tercihi > domain varsayılanı` — drift karşıtı tek formül |
| §5.1 Adım 8 Test Kapısı | Geri/ileri + paylaşım + fallback + temizlik testleri — olmadan karar uygulanmış sayılmaz |
| §5.3 Debate Şartları | Debate 3/20 KABUL çıktısı — 3 şart: (1a) boot query koruma · (1b) restore canlı router bağlama · (2) `clearAll()` oturum sonu temizlik entegrasyonu · (3) back/forward + scroll restore + drift testi |
| ⚠️ `ADR-083` (SPA router) | **Diskte dosya YOK** (accepted/draft/rejected taraması boş) → düz metin + `⚠️ VERIFICATION REQUIRED`, wiki-link yazılmadı |

> **Wiki-link doğrulaması:** Yukarıdaki vault hedeflerinin **tamamı** yazımdan önce `Test-Path` ile diskte doğrulanmıştır (2026-09-29). Diskte **olmayan** hedefe (`ADR-083-*`) wiki-link **yazılmamış**, düz metin + `⚠️ VERIFICATION REQUIRED` kullanılmıştır. `ADR-046-*` bu dosyanın kendisidir (yazım sonrası doğrulanır).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI — 3 tur / 20 persona · Oy: 18 kabul / 2 çekimser / 0 red → KABUL** (2026-09-29).

| Tur | Tür | Sonuç |
|---|---|---|
| 1 | 20 persona bulgu turu | 16 kabul/neutral · 4 uyarı (Frontend: query-strip + `nav:complete` şartı; QA: sessionStorage; Critic: `clearAll()` şartı) |
| 2 | İtiraz → çözüm | 4 itiraz → 4 çözüm → **3 şart** (§5.3) |
| 3 | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

**Tur 1 bulguları (disk/kod kanıtı):** `ScrollManager` kaydetme IMPLEMENTED ama restore `nav:complete` olayına bağlı — bu olay yalnız `js copy/SPARouterAdapter.js:113`'te, canlı `js/router/`'da **0** → restore ölü · `Router.js:54` manuel `scrollRestoration` + her navigasyonda `scrollToTop()` · `Router.js:63-64` boot'ta tüm query parametreleri silinir (`auth_key` hariç) → URL katmanı kırık · filter/sort/page URL'de **0 hit** · API sayfalama `page`/`pageSize` IMPLEMENTED · sessionStorage UI durumu **0** (yalnız 2 anahtar) · `SidebarCache.clearAll()` tanımlı, **0 çağrı** · sunucu `session_destroy()` IMPLEMENTED · ~35 kaynak / 6 sorgu · ADR-083 dosyasız → düz metin + `⚠️ VERIFICATION REQUIRED` · `[[../index.md]]:88` düzeltmesi ertelendi (§5.1 adım 9).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | `Router.js:63-64` boot query-strip durum parametrelerini siliyor | Koruma listesi: `q/sort/order/page/filter/view` (`auth_key` + bilinmeyen/tehlikeli parametre temizliği değişmeden kalır) | §5.3-1a |
| 2 | `nav:complete` canlı `js/router/`'da 0 yayınlayıcı → restore ölü | Restore canlı router olayına bağlanır | §5.3-1b |
| 3 | `SidebarCache.clearAll()` 0 çağrı → istemci temizliği yok | Çıkış/oturum sonu temizlik entegrasyonu | §5.3-2 |
| 4 | Test yok → geri/ileri + scroll restore + drift doğrulanamaz | back/forward + scroll restore + drift testi | §5.3-3 |

**Tur 3 — Oy:** 18 kabul / 2 çekimser / 0 red → **KABUL**; 3 şart §5.3'e eklendi (§5.1 adımları değiştirilmedi — yalnız bağlama netleştirildi).

- **Kapsam:** (a) filter/sort/search/scroll/page — domain + görünüm değişiminde korunur · (b) 3 katmanlı kaynak: URL birincil + sessionStorage oturum-içi + servis tercihi (hesap) · (c) geri/ileri tam geri yükleme (durum + scroll) · (d) gizlilik: oturum sonu temizliği (ADR-011) · (e) fallback: durum yoksa domain varsayılanı (ADR-045) — **kullanıcı onaylı**.
- **Kanıt:** `Router.js:54,63-64` (manual scrollRestoration + boot query-strip) · `ScrollManager` kaydet/geri-yükle ayrışması + `nav:complete` canlıda **0** · filter/sort/page URL **0** · sessionStorage yalnız **2** UI-dışı anahtar · `clearAll()` **0** çağrı · sunucu `session_destroy()` **IMPLEMENTED** · API sayfalama **IMPLEMENTED**.
- **Araştırma:** 6 sorgu / **≈35** adlandırılmış kaynak (protokol `10-web-research-protocol.md`).
- **Tech Lead:** ✅ (2026-09-29 — 3 şartla KABUL, §5.3) · **Arch Lead:** ⏳ (beklemede — bu turda oy yok).

---

*ADR-046 v1.0.0 | 2026-09-29 | Created*
*Authority: CoreMusic Vault — Cross-View State Preservation*
*Mode: Red Team · Human Mode · Truth Mode*
