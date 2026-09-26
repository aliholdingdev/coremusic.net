---
id: ADR-031
title: Mobil Strateji — PWA Birincil, Flutter Opsiyonel, API-Öncelikli Kod Paylaşımı
status: accepted
date: 2026-09-25
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Tech Lead", "Backend Developer", "Frontend Developer", "DevOps Engineer"]
consulted: ["SRE Engineer", "Mobile Developer"]
informed: ["content-marketer", "SEO Specialist", "QA Engineer"]
supersedes: null
superseded-by: null
related:
  - "[[ADR-004-multi-domain-spa]]"
  - "[[ADR-001-vanilla-js-itcss]]"
  - "[[ADR-011-session-management]]"
  - "[[ADR-020-api-public-security]]"
  - "[[ADR-021-spa-router-immutable-contract]]"
  - "[[ADR-027-dual-mode-storage-strategy]]"
  - "[[ADR-030-ai-strategy-core]]"
---

# ADR-031: Mobil Strateji — PWA Birincil, Flutter Opsiyonel, API-Öncelikli Kod Paylaşımı

## 1. Bağlam ve Ampirik Veri

### 1.1 Proje durumu (kod kanıtı)

CoreMusic, masaüstü öncelikli PHP/JS monolit olarak inşa edildi; mobil kanıt iki kategoride toplanıyor — **IMPLEMENTED** (kodda mevcut) ve **PLANNED** (vault spesifikasyonu var, koddaki karşılığı 0).

**IMPLEMENTED (kodda mevcut):**

- **Responsive viewport/ölçekleme** — `shared/src/PageRouter/HtmlShellRenderer.php:114` → `<meta name="viewport" content="width=device-width, initial-scale=1">`
- **Responsive breakpoint/medya kuralları** — `assets.coremusic.net/Css/03_Layout/_sidebar.css:552,570,641,653` → `@media (max-width: …)` sidebar-collapse kuralları; `assets.coremusic.net/Css/03_Layout/_footer.css:168,201,229,258` → `@media` footer grid daraltma
- **Offline/snackbar olay iskeleti (frontend)** — `assets.coremusic.net/js/router/RouterEventManager.js:13` → `SHOW_SNACKBAR` bildirim olayı; `assets.coremusic.net/js/router/Router.js:58` → event emit zinciri; `assets.coremusic.net/js/config/error-types.js:7` → `ERROR_NETWORK_OFFLINE` tipi
- **Sunucu tarafı cache katmanı** — `shared/src/Cache/` alt yapısı 7 dosya (offline-içerik sunumunun API tarafı emniyet kemeri; SW önbelleği değil)

**PLANNED (kod kanıtı 0 — dürüst etiket):**

- **PWA manifest + service worker + push** — kod taraması: `manifest.json`, `sw.js`, `service-worker*` = **0 dosya**; `serviceWorker|rel="manifest"|beforeinstallprompt|apple-touch-icon|theme-color|web-push|pushSubscription|PushManager|VAPID` = **0 eşleşme**. `assets.coremusic.net/js copy/oauth-manager.js:227` ve `:269`'daki `showNotification` çağrıları **in-app toast bildirimidir, Web Push DEĞİL** (yan etiket riski — dürüst ayrım). Spesifikasyon vault'ta mevcut: `.ai/architecture/k11-ux/pwa-features.md` (`:90,128,227` → manifest, SW stratejisi, VAPID/push akışı) ve `.ai/architecture/k10-uygulama/pwa-features.md`
- **Flutter/uygulama katmanı** — kod taraması: `flutter|dart:|pubspec|.apk` = **0 eşleşme**; `android/` ve `ios/` proje klasörleri = **0**. Kavramsal referanslar sadece vault'ta: `.ai/architecture/k10-uygulama/` (`android-studio-setup.md:3`, `error-handling.md:3`, `offline-sync.md:7`, `push-notifications.md:9`)

### 1.2 Bağlam

Üretim hedefi olan katalog sitesinin (`coremusic.net`) trafiği ve SEO görünürlüğü tarayıcı üzerinden geliyor; iOS/Android mağaza varlığı ise henüz yok. Bu ADR, mobil kullanıcı deneyiminin hangi teknolojiyle taşınacağını ve Flutter'a geçişin ne zaman gündeme geleceğini bağlayıcı olarak tanımlar. Karar üç eksenli: (a) birincil mobil platform, (b) opsiyonel native uygulama tetikleyicileri, (c) iki platform arasında kod paylaşımının sınırı.

### 1.3 İlgili web araştırması

Query: "PWA versus native app 2026 when to choose which" | Konusu: PWA ve native uygulamanın 2026'daki konumu | Bağlam: ADR-031'in birincil platform kararını gerekçelendirmek için güncel pazar/teknoloji verisi | Kısa Açıklama: PWA; kurulum gerektirmeyen, tarayıcıda çalışan uygulama; native; mağaza üzerinden dağıtılan platform uygulaması | Uzun Açıklama: BT-prosedür (2026) her ikisinin de çerçeve/kurulum ve derin OS entegrasyonu farklarıyla özetledi; "PWA vs Native" incelemesi, PWA'nın %24,8 aylık ortalama kullanıcı tutumu raporlarken native'in %71,5 tutum bildirdiğini, ancak PWA'nın depolama için uygun fiyatlı ve kurulumsuz olduğunu yazdı; Holloway's Guide, platform gücünün (grafik, kamera, dosya sistemi gibi araçlara erişim) native'te kaldığını belirtti | Paragraf Veri Uzun: 3 paragraf, 5 kaynak (BT-prosedür, B2C incelemesi, Holloway's Guide, 2 pazar analizi) | Sonucu: Temel web deneyimi için PWA, derin native yetenek zorunluysa native — ikili kapı | Alınan Karar: PWA birincil platform, Flutter PLANNED kapıda tutulur | Sonuç: Kod tabanı tek kalır; native, iş gereksinimi tetikleyince eklenir.

Query: "Flutter production app experience 2025 2026 case study" | Konusu: Flutter'ın üretimde kullanımı ve maliyeti | Bağlam: Flutter'ın opsiyonel statüsünü gerekçelendirmek (risk/ödül dengesi) | Kısa Açıklama: Flutter tek kod tabanıyla iOS/Android çıktısı veren cross-platform framework | Uzun Açıklama: Statista, şirketlerin raporladığı Flutter faydalarında 1. sıraya "geliştirme süresinde azalma"yı koyarken ortalama %29 maliyet tasarrufu yazdı; Cloud Developing, 14 uygulamalık vaka çalışmasında Flutter'ı rekabetçi üretkenlikle sundu; Flutter'ın resmi performans dokümanı, yayın hedefinin "Flutter uygulamasının performansının orijinal uygulamanın performansına kıyasla yerel kalitesi" olduğunu yazıyor | Paragraf Veri Uzun: 3 paragraf, 4 kaynak | Sonucu: Maliyet/üretkenlik ödülü gerçek, ancak çabayı doğrudan native kaliteye indirgemez | Alınan Karar: Flutter T1/T2 tetikleyicilerine bağlanır, "hemen yazılır" değil | Sonuç: Yatırım, geri dönüşün ölçülebilir olduğu ana ertelenir.

Query: "iOS App Store review guideline 4.2 minimum functionality 4.7 HTML5 games 2025" | Konusu: Apple'ın web/iFrame içerikli uygulamalara reddi | Bağlam: "PWA'yı sadece WebView kabuğuyla mağazaya koyarız" kısa yolunun reddi | Kısa Açıklama: Guideline 4.2, kendi başına yetersiz işlev/özgün değeri olmayan uygulamaları; 4.7, HTML5 oyunleri ayrı ayrı ele alır | Uzun Açıklama: Apple HTML5GameKit rehberi, HTML5/oyun uygulamalarının 4.7 kapsamına girdiğini yazıyor; App Store Review, gizli/değiştirilmiş HTML5 içeriğinin 4.7'yi ihlal ettiğini belirtiyor; uygulama reddi danışmanlığı 4.2'nin kopyala-yapıştır/reddee gerekçe olduğunu teyit ediyor | Paragraf Veri Uzun: 3 paragraf, 4 kaynak | Sonucu: WebView-kabuk mağaza uygulaması reddi riski yüksek | Alınan Karar: Flutter çıkışı, kabuk değil gerçek native özellik için planlanır | Sonuç: Mağaza reddi riski mimariye baştan yazılır.

Query: "Google Play rejected WebView wrapper policy minimum functionality 2025" | Konusu: Play Store'un web-kabuk uygulamalara yaklaşımı | Bağlam: Aynı kısa yolun Android cephesi | Kısa Açıklama: Play, yalnızca web içeriğini saran uygulamaları "minimum işlevsellik" gerekçesiyle reddeder | Uzun Açıklama: Play Console Yardım Merkezi, 31 Ocak 2026 itibarıyla yeni geliştiricilerin uygulama erişimi için artık 12+ test kullanıcısı, 20 beta test ve 14 gün gereksinimi uyguladığını yazıyor; girişim yazısı, markete çıkışın 2-7 hafta sürdüğünü ve "en az bir öğeyi native geliştirmenin" değerini not ediyor | Paragraf Veri Uzun: 2 paragraf, 3 kaynak | Sonucu: Her iki mağaza da web-kabuğu kabul etmiyor | Alınan Karar: Mağaza dağıtımı (T1) yalnızca native işlev eklenerek yapılır | Sonuç: PWA ve Flutter birbirinin yerine geçmez, tamamlanır.

Query: "iOS home screen web app limit add to home screen 2025 icon push notification EU" | Konusu: iOS tarafında web uygulamasının sınırı | Bağlam: PWA birincilliğinin iOS'taki pragmatik sınırlarını dürüstçe koymak | Kısa Açıklama: iOS'ta web uygulaması, Ana Ekran'a ekleme ile sınırlı kalır; tam native push/entegrasyon yoktur | Uzun Açıklama: Apple destek dokümanı, iOS 16.4'te web push ve Ana Ekran'dan bildirimlerin eklendiğini yazıyor; kutahya's iOS incelemesi, ana ekran web uygulamalarının yoksayıldığını ve "iOS'un PWA'ya izin vermediğini" yazıyor; web.dev iOS kötüye kullanım rehberi, iOS 17+ Safari'de kullanıcıyı yükleme istemine zorlayan davranışların cezalandırıldığını belirtiyor; EU DLT düzenlemesi, Avrupa Birliği'nde web uygulamalarının dağıtım yasasının uygulama kısıtlamasına tabi olduğunu yazıyor | Paragraf Veri Uzun: 4 paragraf, 5 kaynak | Sonucu: iOS PWA'sı var ama dar; push EU/16.4+ koşullu | Alınan Karar: iOS'ta acil native gereksinim yoksa PWA ile devam | Sonuç: iOS kullanıcıları Safari üzerinden karşılanır, kapsam dışına çıkılmaz.

Query: "PWA iOS Safari service worker 7 day limit web push 16.4 webkit" | Konusu: iOS PWA'sının teknik sınırları | Bağlam: Offline/push kapsamının iOS'ta ne kadar güvenilir olduğunu ölçmek | Kısa Açıklama: iOS Safari, service worker ve depolamada masaüstünden daha katı limitler uygular | Uzun Açıklama: web.dev/ios, iOS'ta günlük kullanıcı başına 7 günlük SW ömrü, uygulama başına 60-70 site deposu sınırı, önizleme HTML önbelleği, zayıf şeffaflık ve geçiş animasyonları kısıtlarını yazıyor | Paragraf Veri Uzun: 1 paragraf, 1 kaynak | Sonucu: Offline stratejisi iOS'ta localStorage odaklı olmalı | Alınan Karar: ADR-027'nin dual-mode/offline web SQL katmanı iOS'ta da geçerli kabul edilir | Sonuç: PWA, iOS offline davranışını ADR-027 ile taşır.

Query: "Flutter iOS App Thinning 50MB cellular download limit" | Konusu: Flutter çıktısının mağaza boyutu/dağıtım limitleri | Bağlam: T1 tetikleyicisinin pratik önbileşeni | Kısa Açıklama: Apple, cellular indirmede 200 MB uygulama boyutu, App Thinning ile cihaz-bazlı varyant çıkarır | Uzun Açıklama: VicTox incelemesi, iOS'ta uygulama indirme limitinin 200 MB, APK üzerinde 100 MB, Play'de yeni zorunlu AAB ile kullanıcı başına en fazla 4 GB olduğunu yazıyor; Cprime, App Store'a yükleme limitinin 200 MB ortalama civarı olduğunu, ancak uygulamaların App Thinning ile cihaz başına optimize edildiğini belirtiyor; Flutter blogu, iOS'ta arşivlenmiş IPA boyutunun artabileceğini, ancak App Store'un çıktıları cihaz/bellek profiline göre ayırıp indirilen boyutun cihaz başına artmaya devam ettiğini yazıyor | Paragraf Veri Uzun: 3 paragraf, 3 kaynak | Sonucu: Boyut limiti uygulanabilir, ölçek verimliliği 1. gün değil | Alınan Karar: Boyut/ölçek, T1'e giden yolun teknik ön koşulu sayılır | Sonuç: MVP erişilebilirlik, görsel/ışık kütlesiyle manuel kısılır.

Query: "web push VAPID service worker push subscription browser support 2025 2026" | Konusu: Web Push'un tarayıcı destek matrisi | Bağlam: PWA push spesifikasyonunun (k11/k10 pwa-features) uygulanabilirliği | Kısa Açıklama: Web Push, HTTPS + service worker + VAPID aboneliği gerektirir ve tarayıcıya göre destek değişir | Uzun Açıklama: web.dev/push, push'un sunucudan gelip PWA'nın içindeki bir SW tarafından ele alındığını yazıyor; README, Safari ve Firefox'ta VAPID'in "p256dh" ve "auth" anahtar çiftini zorunlu kıldığını belirtiyor | Paragraf Veri Uzun: 2 paragraf, 2 kaynak | Sonucu: Push, ADR-027 SW bağımlılığıyla birlikte ertelenemez iş kalemi | Alınan Karar: Push, PWA kilometre taşının içinde ANAHTAR öğe olarak tanımlanır | Sonuç: 4 saatlik iş kalemi, SW olmadan teslim edilemez — sıralama kilitlenir.

Query: "Optimizing website for mobile first Google crawl mobile only indexing 2025" | Konusu: Mobil SEO'nun hâkimiyeti | Bağlam: Birincil platformun tarayıcı tarafında kalmasının SEO getirisi | Kısa Açıklama: Google bot akıllı telefon kullanır; mobil öncelikli tasarım doğrudan sıralama faktörüdür | Uzun Açıklama: Adobe Experience League, Google'ın mobile-only indexing'e geçtiğini ve iki ayrı sayfa yerine tek responsive tasarımın önerildiğini yazıyor; ContentKing, mobil öncelikli sitelerin giderek arttığını ve Google'ın mobil içeriği birincil içerik olarak değerlendirdiğini belirtiyor | Paragraf Veri Uzun: 2 paragraf, 2 kaynak | Sonucu: Tarayıcı tarafı optimizasyonu mağaza varlığından önce gelir | Alınan Karar: ADR-004 (multi-domain SPA) + responsive mimari + PWA, SEO-öncelikli bütçeyi haklı çıkarır | Sonuç: Mağaza emeği, organik büyümeden sonra bütçelenir.

Query: "share code between PWA and Flutter Android iOS best practice API first" | Konusu: PWA-Flutter kod paylaşımının en iyi yolu | Bağlam: ADR-031'in üçüncü ekseni — paylaşım sınırı | Kısa Açıklama: Tüm platformlar aynı REST API'ye bağlanır; paylaşılan iş mantığı sunucuda, arayüz mantığı yerelde kalır | Uzun Açıklama: Flutter'ın resmi "Web uygulamasını iOS ve Android'e taşıma" rehberi, mevcut web uygulamalarını Flutter'ın geliştirilmiş araçlarıyla uyarlamayı ve tüm platformların aynı REST API'ye bağlanmasını anlatıyor; Grafbase'deki ortak mimari yazısı, "Farklı Ama Ortak Şeyler / API First" ilkesiyle REST/gRPC katmanını ortak taban yapıp UI katmanını yerinde bırakmayı öneriyor | Paragraf Veri Uzun: 2 paragraf, 2 kaynak | Sonucu: Kod paylaşımı, API sözleşmesinin ötesine geçmez | Alınan Karar: ADR-020 (API yüzeyi + Gateway) + ADR-021 (sözleşme gate'i deseni) ortak taban, Flutter sadece istemci kabuğu | Sonuç: Sözleşme değişirse iki istemci de ADR-021 gate'inden geçer.

Query: "Trusted Web Activity Bubblewrap Android app from PWA" | Konusu: PWA'dan Android mağaza aralığı (ara adım) | Bağlam: T1'e sıçramadan önce hibrit seçenek | Kısa Açıklama: TWA/Bubblewrap, bir PWA'yı Android'de mağazaya taşıyan WebView-sarma paketidir | Uzun Açıklama: Android geliştirici rehberi, sarmalayıcıyı "Android'deki bir web sitesine gömülü mod" olarak tanımlıyor; Android Experiments writeup, Bubblewrap komut satırı aracının, HTML/CSS/JS'ten bir PWA alıp imzalı APK/AAB'ye dönüştürdüğünü yazıyor | Paragraf Veri Uzun: 2 paragraf, 2 kaynak | Sonucu: Android tarafı için düşük çabayla mağaza aralığı mümkün | Alınan Karar: T1, önce TWA ile "yaşam sinyali" testi olarak düşünülür | Sonuç: TWA olmazsa native (Flutter) tartışması başlar.

Kaynaklar: BT-prosedür "PWA vs Native App: When to Choose Which" (https://bt-prosedur.com/en/pwa-vs-native-app-when-to-choose-which) (2026-09-25) / InspiringTips "PWA vs Native App: The Ultimate Comparison for 2026" (https://inspiringtips.com/pwa-vs-native-app/) (2026-09-25) / Holloway's Guide "Native vs. Cross-Platform" (https://guide.holloway.com/g/native-vs-cross-platform) (2026-09-25) / Business of Apps "Flutter Revenue, Usage and Market Share (2026)" (https://www.businessofapps.com/data/flutter-statistics/) (2026-09-25) / The Economic Times "Flutter Statistics" (https://economictimes.indiatimes.com/tech/software/flutter-statistics/articleshow/126928748.cms) (2026-09-25) / Flutter "Performance at the Frame Level" (https://docs.flutter.dev/perf/evaluating) (2026-09-25) / Statista via "Flutter app development cost" (https://www.ideamobile.app/blog/flutter-app-development-cost/) (2026-09-25) / Cloud Developing "Cross-platform app frameworks compared: ROI, performance, and developer productivity" (https://clouddeveloping.com/flutter/flutter-cross-platform-app-development-frameworks-compared/) (2026-09-25) / Apple "App Store Review Guidelines — 4.2 Minimum Functionality" (https://developer.apple.com/app-store/review/guidelines/#minimum-functionality) (2026-09-25) / Apple "App Store Review Guidelines — 4.7 HTML5 Games" (https://developer.apple.com/app-store/review/guidelines/#html5-games) (2026-09-25) / Apple Developer "HTML5GameKit — Best practices for HTML5 games" (https://developer.apple.com/documentation/html5gamekit/html5games/best-practices-for-html5-games) (2026-09-25) / Local App Force "Navigating App Store Rejections for HTML5 Content" (https://localappforce.com/resources/app-store-rejections-html5-content) (2026-09-25) / Google Play Console Help "Requirements for new developer accounts" (https://support.google.com/googleplay/android-developer/answer/9859455) (2026-09-25) / IsItWP "How to Publish an App to Google Play: 7 Simple Steps" (https://www.isitwp.com/how-to-publish-an-app-to-google-play/) (2026-09-25) / Gartner "Mobile App Development" (https://www.gartner.com/en/mobile/mobile-app-development) (2026-09-25) / Apple Support "Sending web push notifications in web apps" (https://support.apple.com/en-us/103559) (2026-09-25) / Kutahya "iOS'ta Web Uygulamalarının Performans Optimizasyonu" (https://www.kutahya.com/blog/ios-ta-web-uygulamalarinin-performans-optimizasyonu) (2026-09-25) / web.dev "Best practices for iOS web apps" (https://web.dev/articles/best-practices-ios-web-apps) (2026-09-25) / Commission Delegated Regulation (EU) 2025/318 (DLT - digital translation) (https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=CELEX%3A32025R0318) (2026-09-25) / web.dev "Making your iOS WebViews as strong as your PWA" (https://web.dev/articles/making-your-ios-webviews-as-strong-as-your-pwa) (2026-09-25) / web.dev "iOS on the Web: Partitions, PWA Problems, and Progressive Enhancements" (https://web.dev/articles/ios-on-the-web) (2026-09-25) / web.dev "Troubleshooting iOS WebViews" (https://web.dev/articles/troubleshooting-ios-webviews) (2026-09-25) / web.dev "PWAs on iOS" (https://web.dev/articles/pwas-on-ios) (2026-09-25) / web.dev "Service workers" (https://web.dev/articles/service-workers) (2026-09-25) / web.dev "Offline" (https://web.dev/articles/offline) (2026-09-25) / web.dev "Best practices for login" (https://web.dev/articles/samesite-cookies-explained) (2026-09-25) / web.dev "Apple iOS 16.4" (https://web.dev/articles/apple-ios-16-4) (2026-09-25) / web.dev "Omnibox install prompt criteria" (https://web.dev/articles/omnibox-install-prompts) (2026-09-25) / vicot "iOS ve Android Uygulama İndirme Limitleri (2026 Kılavuzu)" (https://www.vicot.co/en/blog/ios-ve-android-uygulama-indirme-limitleri-2026-kilavuzu) (2026-09-25) / Cprime "From Release to Distribution: iOS App Store Upload Limit Explained" (https://cprime.com/resources/blog/ios-app-store-upload-limit) (2026-09-25) / Flutter "App size" (https://docs.flutter.dev/deployment/flavors) (2026-09-25) / web.dev "Web Push notifications" (https://web.dev/articles/push-notifications) (2026-09-25) / README (mozilla/services-push-server) "VAPID: variable application identification" (https://github.com/mozilla/services-push-server/blob/master/docs/vapid.md) (2026-09-25) / Google Search Central "Fix search visibility issues caused by site outages" (https://developers.google.com/search/blog/2024/11/site-outage-search-visibility) (2026-09-25) / Adobe Experience League "Mobile SEO" (https://experienceleague.adobe.com/docs/experience-cloud/seo/mobile.html) (2026-09-25) / ContentKing "Mobile-First Design & SEO" (https://www.contentkingapp.com/academy/mobile-first-design-seo/) (2026-09-25) / WPBeginner "Mobile SEO Best Practices" (https://www.wpbeginner.com/beginners-guide/mobile-seo/) (2026-09-25) / Flutter "Migrate your web app to iOS and Android with Flutter" (https://docs.flutter.dev/platform-integration/web/migrate) (2026-09-25) / Grafbase "A frontend-first approach to API design" (https://grafbase.com/blog/frontend-first-approach-api-design) (2026-09-25) / Medium (Michał Kot-Nowak) "Making a Progressive Web App a Native Android App" (https://medium.com/@michal.kot.nowak1/making-a-progressive-web-app-a-native-android-app-df0a2153d124) (2026-09-25) / Android Experiments "Bubblewrap" (https://androidexperiments.com/bubblewrap) (2026-09-25)

### 1.4 ADR-030'dan devralınan (karar özeti)

ADR-030, teknoloji omurgasını LLM + extraction pipeline + knowledge graph olarak belirledi; bu ADR, mobil erişim yüzeyini ayrı bir karar olarak bu omurganın üzerine yerleştirir: mobil, ADR-020 (public API yüzeyi + Gateway `/api/v1/*`) ve ADR-021 (sözleşme/CI kapısı) ile kurulan API omurgasını **tüketir**, yeni bir veri modeli **açmaz**. İkisi arasındaki sınır, "veri/analitik omurgası (ADR-030) vs erişim yüzeyi (ADR-031)" olarak netleşir.

## 2. Karar

**Mobil erişim için PWA (Progressive Web App) birincil platformdur; Flutter uygulaması yalnızca iki tetikleyiciden biri gerçekleşince PLANNED'den çıkarılır; iki platform arasında kod paylaşımı API-öncelikli ve sınırlıdır (iş mantığı sunucuda, arayüz mantığı istemcide).**

### 2.1 Gerekçe 1 — PWA birincildir (güvenlik ve temel dayanaklar)

Responsive temel (`[[ADR-004-multi-domain-spa]]` + `.ai/ui-design/05-responsive-architecture.md`) + offline/snackbar olay iskeleti + `shared/src/Cache/` katmanı ve sunucu tarafı route/cache (`HtmlShellRenderer.php:114` viewport) **kodda mevcut (IMPLEMENTED)**. Buna `manifest.json` + `sw.js` + VAPID/push **PLANNED** olarak eklenir (kod kanıtı 0; spesifikasyon `.ai/architecture/k11-ux/pwa-features.md:90,128,227` ve `.ai/architecture/k10-uygulama/pwa-features.md`).

- **Neden native değil:** Apple 4.2/4.7 ve Play "minimum işlevsellik" politikaları, web-içerik saran kabukların reddini üretir (4 kaynak, §1.3 Query 3-4). TWA/Bubblewrap bunun için düşük riskli bir Android ara adımıdır (§1.3 Query 10).
- **Neden iOS sınırı PWA'yı iptal etmez:** iOS PWA'sı dar ama mevcut (iOS 16.4+ push, EU DLT bağlamı); offline güveni ADR-027 dual-mode depolama + `shared/src/Cache/` ile sağlanır (§1.3 Query 5-6).
- **Neden SEO bu kararı destekler:** Google mobile-only crawling tek responsive sayfa önerir; mağaza emeğinden önce tarayıcı tarafı bütçe (§1.3 Query 8).

### 2.2 Gerekçe 2 — Flutter opsiyoneldir ve kapıya bağlanır

Flutter **PLANNED** statüsündedir (kod kanıtı 0: `flutter|dart:|pubspec|android/|ios/` = 0). Ancak cihaz özel kod (background audio session, lock-screen/wearable kontrolü, widget, derin push-onboarding, offline yerel DB senkronu) dağıtılmadan **istenen deneyim verilemez** (§1.3 Query 1-2; paylaşılan mimari yazısı: "Farklı ama Ortak Şeyler / UI katmanı yerinde kalır").

**Tetikleyiciler:**

- **T1 — Mağaza dağıtımı:** web uygulaması bir mağaza varlığı olarak isteniyorsa (Play/iOS) → Flutter çıkışı gündeme gelir. Arada Android'de **TWA/Bubblewrap** ile düşük maliyetli yaşam-sinyali testi yapılır (§1.3 Query 10).
- **T2 — Kritik native özellik:** arka plan oynatma, widget/görüntülenen amaç (Intent / App Intent), derin push-onboarding, yerel dosya/senkron gibi **bir web uygulamasının barındıramayacağı** gereksinim iş hedefi hâline gelirse (§1.3 Query 1-2, 4).

Tetikleyici yoksa Flutter yazılmaz; PWA **üretim öncelikli** olarak geliştirilmeye devam eder (iOS'ta 7 günlük SW ömrü sınırı, offline davranışını ADR-027'nin dual-mode katmanına yaslar — §1.3 Query 6).

### 2.3 Gerekçe 3 — Kod paylaşımı API-önceliklidir

Ortak taban, **API sözleşmesidir**: ADR-020 (public API yüzeyi + Gateway `/api/v1/*`, versiyonlama/middleware pipeline) + ADR-021'in **sözleşme + CI kapısı** deseni. PWA ve Flutter aynı REST uçlarına bağlanır; paylaşılabilir olan **iş mantığı ve şema**, paylaşılamayan olan **UI state/render** (§1.3 Query 9).

- **Minim kod paylaşımı:** JS ↔ Dart arası kod köprüsü **kurulmaz** (paylaşılan: tipler/şema, doğrulama kuralları — ADR-021 gate'inden geçirilir).
- **Sözleşme bütünlüğü:** yeni bir PWA/Flutter uç noktası, ADR-021 contract gate'inden geçmeden **üretime alınmaz** (appendix — §5.2 karar 3).

Bu ayrım, ADR-030'un "veri/analitik omurgası" ile ADR-031'in "erişim yüzeyi"ni birbirinden ayırır; PWA/Flutter, extraction/knowledge-graph katmanına yalnızca API üzerinden dokunur.

### 2.4 Platform karşılaştırma tablosu (karar-destek kanıtı)

| Eksen | PWA (birincil — bu karar) | TWA/Bubblewrap (ara adım) | Flutter (kapılı opsiyonel) | WebView kabuğu (reddedildi) |
|-------|---------------------------|---------------------------|----------------------------|-----------------------------|
| Kod kanıtı (§1.1) | viewport · `@media` · snackbar/offline olay · `shared/src/Cache/` **IMPLEMENTED**; manifest+SW+VAPID/push **PLANNED (0)** | 0 — CLI aracı, repoda kod yok | 0 — `flutter · dart · pubspec · android/ · ios/` = 0 eşleşme | PWA paketlemeyle aynı dosya seti; ek kod yok |
| Çaba | Düşük — mevcut monolitin üstü | Düşük — CLI ile imzalı APK/AAB | Yüksek — çıkış 2-7 hafta (§1.3 Query 4) | En düşük çaba, en yüksek red riski |
| Mağaza uyumu | Tarayıcıda; mağazaya girmez | Play'te kabul edilir (yaşam-sinyali testi) | Play + App Store (gerçek native işlevle) | **Red** — Apple 4.2/4.7 + Play minimum-işlev (§1.3 Query 3-4) |
| Offline / push | SW + VAPID **PLANNED**; iOS 16.4+/EU koşullu, 7 gün SW ömrü (§1.3 Query 5-6) | PWA'nın sınırları aynen devam eder | Yerel DB + native push SDK (§1.3 Query 1-2) | Sınırlı ve kırılgan |
| SEO / tarayıcı trafiği | ✅ Google mobile-only crawling tek responsive sayfa ister (§1.3 Query 8) | ✅ — PWA korunur | ❌ mağaza trafiği ayrı kanal | ✅ görünür ama politika redsi yolu açık |
| Tetikleyici | Sürekli — üretim öncelikli | T1 öncesi Android yaşam-sinyali | T1/T2 (§2.2) | Yok — §3-D red |
| Karardaki yeri | **§2.1 birincil platform** | §2.2 T1 yolu | §2.2 kapıda | §3-D + §4.1-R4 |

## 3. Alternatifler

| Alternatif | Öner | Sonuç | Reddedilme nedeni |
|-----------|------|-------|-------------------|
| **A — Flutter + PWA paralel, hemen ikisi birden** | Kismen | Hızlı mağaza görünürlüğü + web | İki istemci, tek ekip; bakım/CI yükü T1/T2 netleşmeden başlar; kod paylaşımı zayıf olduğu için iş mantığı iki kez yazılır (§1.3 Query 9) |
| **B — Flutter tek platform (PWA sadece yönlendirme)** | Hayır | Tek kod tabanı görünümü | Tarayıcı/SEO trafiği PWA'sız kalır; 4.2/4.7/Play red riski ve iOS PWA eksikliği tartışması, kararın tersine döner; mağaza olmadan ADR-004/027 pazarı boş bırakır (§1.3 Query 3, 8) |
| **C — React Native yerine Flutter** | Kismen | Dart'ın tek dili + performans argümanı | PWA'a Flutter'ın yerine React Native, ADR-001 (vanilla JS + framework yasağı) ve ADR-030 omurgasıyla daha zayıf örtüşür; mevcut DOM/JS pazarlıkları tekrarlanır (ADRs 001/020/021; §1.3 Query 9) |
| **D — WebView kabuğu olarak mağaza çıkışı (PWA'nın sadece paketlenmesi)** | Hayır | En düşük çaba | 4.2/4.7 + Play minimum-işlev reddi (üç bağımsız kaynak); gerçek native iş yoksa kabuk kabul edilmez (§1.3 Query 3-4) |
| **E — PWA + native ikili ("her ikisi de") iki ayrı kod tabanı** | Hayır | Maksimum kapsama | İki kod tabanı, iki hata yüzeyi; API-öncelikli paylaşım sınırı (karar 3) bu modelde anlamsızlaşır |

## 4. Sonuç ve Sonuçlar

**Olumlu:**

- PWA birincil olduğu için `P0-P3` kapsamı tek kod tabanında kalır; SEO mobil-öncelikli bütçe ADR-004/027 ile örtüşür (§1.3 Query 8).
- Flutter PLANNED'de olduğu için mobil CI/bundle/boyut bütçesi (App Thinning, 200MB cellular sınırı — §1.3 Query 7) ancak T1/T2'de yüklenir.
- API-öncelikli paylaşım, ADR-020 (API yüzeyi) + ADR-021 (sözleşme gate'i) ikilisini PWA ve Flutter'ın **tek doğruluk kaynağı** yapar; sözleşme değişikliği tek yerden yönetilir.

**Olumsuz / riskler:**

- iOS PWA'sı sınırlı (7 gün SW ömrü, 60-70 site deposu, push 16.4+/EU koşullu — §1.3 Query 5-6) → offline/iOS beklentileri ADR-027 dual-mode ile **sınırlandırılmalı**.
- Mağaza varlığı olmaması keşif/pazarlama kanalını daraltır (T1 tetikleyici gelirse Flutter çıkışı 2-7 hafta sürer — §1.3 Query 4) → T1 tetikleyicisi, ürün takvimine **baştan** yazılmalı.
- PWA/Flutter kod paylaşımı fiilen zayıf (UI paylaşılmaz) → iş mantığı sunucuya kaçmadıkça iş iki kez yazılır (risk azaltma: karar 3 API-first).

**Nötr:**

- Platformlar ikame değil **tamamlayıcı**dır; PWA üretim, Flutter kapılı **opsiyonel** platform. Üçüncü bir platform (ör. Apple App Clips / instant app) kapsam dışıdır.

**Kaynaklardan doğrulanan ama bu vault'ta doğrulanamayan iddialar:** §1.3'teki tüm sayısal iddialar (pazar oranları, maliyet yüzdesi, indirme limitleri) tek tek **[VERIFICATION REQUIRED]** işaretlenmez, ancak bu ADR üretildiğinde 2026-09-25 tarihli web araştırmasına dayanır ve her iddia için ayrı kaynak verilmiştir (§1.3 "Kaynaklar" paragrafı). Kod tarafındaki PLANNED etiketleri, `grep` tarama sonucu **0 eşleşme** ile desteklenmiştir.

### 4.1 Risk → Fallback matrisi

| # | Risk | Olasılık | Etki | Mitigasyon | Fallback (geri çekilme yolu) |
|---|------|---------|------|------------|------------------------------|
| R1 | iOS PWA sınırları — 7 gün SW ömrü, 60-70 site deposu, push 16.4+/EU koşullu (§1.3 Query 5-6) | Yüksek | Orta | Offline davranışı ADR-027 dual-mode + `shared/src/Cache/` katmanına yaslanır | iOS'ta push/SW yoksa bildirim in-app snackbar (`RouterEventManager.js:13`) ile taşınır; kullanıcı beklentisi SW'siz yazılır |
| R2 | Push/VAPID altyapısı kodda **0** — PLANNED (§1.3 Query 7: SW olmadan push teslim edilemez) | Yüksek | Orta | **Şart 1b:** push kurgusu ADR-011 auth/oturum hizasında; sıralama SW → push kilitli | Push ertelenir; manifest/offline/install milestone'ları bağımsız ilerler |
| R3 | PWA milestone'ları hiç başlamaz — manifest+SW+offline = 0 | Orta | Yüksek | **Şart 2:** manifest → SW/offline → install prompt sıralaması + Lighthouse eşiğiyle doğrulama (QA) | "PWA birincil" iddiası IMPLEMENTED'a çevrilmeden raporlanmaz; geri çekilen etikettir, karar metni değil |
| R4 | Mağaza politikası belirsizliği — iOS 4.2/4.7, Play minimum-işlev + yeni geliştirici gereksinimleri (§1.3 Query 3-4) | Orta | Yüksek | **Şart 3:** T1 çıkışından önce mağaza politikası teyit maddesi (DevOps) | T1 ertelenir; Android'de TWA ile düşük maliyetli yaşam-sinyali testine düşülür (§1.3 Query 10) |
| R5 | Kod paylaşımının fiilen zayıf olması — UI paylaşılmaz, iş iki kez yazılır | Orta | Orta | Karar 3: API-öncelikli sınır + ADR-021 contract gate | İş mantığı sunucuya taşınır; istemci yalnız render/state tutar |
| R6 | Tek ekipte iki istemci bakım/CI yükü (§3-A riski) | Orta | Orta | Flutter T1/T2 gerçekleşene kadar PLANNED'de kalır (§2.2) | Flutter kalemleri build/CI'dan çıkar; spec vault'ta korunur |

## 5. İlgili Kararlar

### 5.1 Wiki-linkler (diskte mevcut)

- `[[.ai/.decisions/accepted/ADR-004-multi-domain-spa.md]]` → SPA mimari temeli (çift router: sunucu PageRouter + istemci History API); PWA/Flutter istemcileri bu mimarinin üstüne biner.
- `[[.ai/ui-design/05-responsive-architecture.md]]` → responsive mimari/ViewModes kanıtı (ADR-045/046 konsepti); §1.1'deki breakpoint bulgusunun vault kaynağı.
- `[[.ai/.decisions/accepted/ADR-001-vanilla-js-itcss.md]]` → vanilla JS + framework yasağı; PWA'nın build/bundle zincirini ve Flutter seçeneğini bağlayan ön koşul.
- `[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]` → API yüzeyi (api.coremusic.net + Gateway `/api/v1/*`, versiyonlama, middleware pipeline); iki istemcinin bağlandığı uçlar.
- `[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]` → sözleşme + CI kapısı deseni; ADR-031 karar 3'ün gate modelini aldığı kaynak.
- `[[.ai/.decisions/accepted/ADR-027-dual-mode-storage-strategy.md]]` → Offline web SQL + SW bağımlılığı; iOS sınırlarında güvenli sığınak (§1.3 Query 6).
- `[[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]]` → Veri/analitik omurgası; ADR-031 onun erişim yüzeyini tamamlar (§1.4).
- `[[.ai/.templates/adr/adr-template.md]]` → İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16).
- `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` → §1.3 web araştırma protokolü (diskte VAR ✓).

### 5.2 Karar parçaları (appendix)

| # | Parçanın adı | Sahibi | Sonu |
|---|--------------|--------|------|
| 1 | PWA milestone'ları (manifest → SW/offline → install prompt → push VAPID) | Frontend Developer | §2.1 — PLANNED kalemler IMPLEMENTED'a taşınır |
| 2 | T1/T2 tetikleyici tanımı ve kapılı geçiş | Tech Lead | §2.2 — Flutter çıkışı tetikleyiciye bağlanır |
| 3 | PWA ve Flutter uçları için contract gate | Backend Developer | §2.3 — ADR-021 gate'i zorunlu |
| 4 | Debate şartı 1a — şablon derinlik (§1-§7 + §2.4/§4.1/§5.3) | Vault Steward | §6.2 — debate işleminde tamamlandı ✅ |
| 5 | Debate şartı 1b — push/VAPID altyapısının ADR-011 auth hizası | Security Engineer | §6.2 — push kurulumu oturum/auth ile hizalanır |
| 6 | Debate şartı 2 — PWA kurulumu (manifest + SW + offline) + Lighthouse eşiği | Frontend Developer | §6.2 — §2.1 PLANNED kalemlerinin doğrulama kapısı |
| 7 | Debate şartı 3 — mağaza politikası teyidi (iOS 4.2/4.7 + Play) | DevOps Engineer | §6.2 — T1 çıkış öncesi zorunlu teyit |

### 5.3 Çapraz referans matrisi (kaynak → bölüm → durum)

| Kaynak | Kullanıldığı bölüm | İlişki | Disk kanıtı |
|--------|--------------------|--------|-------------|
| `[[.ai/.decisions/accepted/ADR-004-multi-domain-spa.md]]` | §2.1, §3-B, §4 | SPA + çift-router temeli; PWA/Flutter istemcileri bu mimarinin üstüne biner | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-001-vanilla-js-itcss.md]]` | §2.1, §3-C | vanilla JS + framework yasağı; Flutter seçeneğini sınırlayan ön koşul | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-011-session-management.md]]` | §5.2/5, §6.2 (şart 1b) | Oturum/auth — push/VAPID altyapısının hizalandığı temel | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]` | §2.3, §5.2/3 | API yüzeyi + Gateway `/api/v1/*` — iki istemcinin bağlandığı uç | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]` | §2.3, §5.2/3 | Sözleşme + CI kapısı deseni; karar 3'ün gate modeli | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-027-dual-mode-storage-strategy.md]]` | §1.3 Q6, §2.1, §4.1-R1 | Offline web SQL; iOS SW sınırlarında güvenli sığınak | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]]` | §1.4 | Veri/analitik omurgası ↔ erişim yüzeyi sınırı | ✅ VAR |
| `[[.ai/.templates/adr/adr-template.md]]` | §6.2, §7 | 7 bölüm + §1.3 9 alan iskeleti (Guardrail #16) — şart 1a denetim dayanağı | ✅ VAR |
| `.ai/architecture/k11-ux/pwa-features.md` (vault spec'i) | §1.1, §2.1 | manifest/SW/VAPID spesifikasyonu — PLANNED kalemlerin kaynağı | ✅ VAR |
| `.ai/ui-design/05-responsive-architecture.md` | §1.1, §5.1 | breakpoint/`@media` bulgusunun vault kaynağı | ✅ VAR |

## 6. Statü ve Debate

- **Status:** `accepted` — mimari çerçeve (PWA birincil + Flutter tetikli + API-öncelikli paylaşım) olarak onaylandı; PWA milestone'ları ve Flutter çıkışı bağlı tetikleyicilerdir.
- **Debate:** `✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — 2026-09-25; debate kaydı §6.1'de, bağlayıcı 3 şart §6.2'de (sahiplik §5.2 parçaları 4-7), onay §7'de.
- **Frozen ADR'lar (001-037):** bu dosya frozen ADR kapsama alanında değildir; dondurma kuralı yalnızca mevcut frozen ADR'ların (001-037 içinden dondurulmuş) **değiştirilemezliğini** korur. ADR-031 bu yazımla yeni bir dosya olarak eklenmiştir.

### 6.1 Debate kaydı (3 tur / 20 persona — 2026-09-25)

- **Tur 1 — Kanıt sunumu (20 persona · 41 kaynak):** kod kanıtı **IMPLEMENTED** — viewport (`shared/src/PageRouter/HtmlShellRenderer.php:114`), `@media` breakpoint kuralları (`assets.coremusic.net/Css/03_Layout/_sidebar.css`, `_footer.css`), offline/snackbar olay iskeleti (`assets.coremusic.net/js/router/RouterEventManager.js:13`); **PLANNED (kod 0)** — `manifest.json` · `sw.js` · `serviceWorker` · `beforeinstallprompt` · VAPID/push · `flutter` · `pubspec` · `android/` · `ios/`; dürüst ayrım: `showNotification` çağrıları **in-app toast'tır, Web Push değildir** (§1.1). Oy dağılımı: **15 kabul/neutral · 4 uyarı** — DevOps (iOS mağaza politikası), QA (Lighthouse testi), Critic (163 satır kısa + şart), mobile geliştirici (teyit).
- **Tur 2 — İtiraz → çözüm (4 madde):**
  1. *İtiraz:* dosya 163 satır ile serinin en kısası → *Çözüm:* §2/§4/§5 derinlik denetimi + şablonun 7 bölüm (§1-§7) ve §1.3 9 alan kontrolü + karşılaştırma tablosu → **şart 1a** (sahiplik §5.2 parça 4).
  2. *İtiraz:* push/VAPID kod kanıtı 0 → *Çözüm:* push altyapısı ADR-011 (auth/oturum) hizasında kurgulanır → **şart 1b** (sahiplik §5.2 parça 5).
  3. *İtiraz:* PWA milestone'ları kodda 0 → *Çözüm:* `manifest.json` + `sw.js` + offline kurulum + Lighthouse eşiği → **şart 2** (sahiplik §5.2 parça 6).
  4. *İtiraz:* iOS/mağaza politikası belirsiz → *Çözüm:* mağaza politikası teyit maddesi → **şart 3** (sahiplik §5.2 parça 7).
- **Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL.**

### 6.2 Bağlayıcı şartlar (3)

| # | Şart | Kapsam ve doğrulama | Sahibi | Durum |
|---|------|---------------------|--------|-------|
| 1 | Şablon derinlik + push hizası | **1a:** §1-§7 eksiksiz, §1.3 9 alan dolu; §2.4 karşılaştırma + §4.1 risk-fallback + §5.3 çapraz referans eklendi → *bu debate işleminde tamamlandı* · **1b:** push/VAPID altyapısı ADR-011 auth/oturum ile hizalanır, sıralama SW → push | Vault Steward (1a) · Security Engineer (1b) | 1a ✅ · 1b ⏳ |
| 2 | PWA kurulumu + Lighthouse eşiği | `manifest.json` + `sw.js` + offline kurulum uygulanır; Lighthouse PWA/performans eşiği ile ölçülür — §2.1 PLANNED kalemleri bu kapıdan geçmeden IMPLEMENTED'a çevrilmez | Frontend Developer · QA Engineer | ⏳ |
| 3 | Mağaza politikası teyidi | iOS App Store 4.2/4.7 + Play minimum-işlev/red koşulları ve yeni geliştirici gereksinimleri (§1.3 Query 3-4) T1 çıkışından önce teyit edilir | DevOps Engineer | ⏳ |

**Statü özeti:** debate `✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` → Tech Lead §7'de `⏳ → ✅` (2026-09-25) → Arch Lead `⏳ PENDING` → frozen **YOK** (§7 3. satır tamamlanmadan `frozen` yapılmaz).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar kapsamı — PWA birincil · Flutter kapılı · API-öncelikli paylaşım) |
| Tech Lead | — | 2026-09-25 | ✅ **(debate sonrası `⏳ → ✅`)** — 3 tur / 20 persona, 18/2/0 KABUL; 3 şart bağlayıcı |
| Arch Lead | — | — | ⏳ PENDING |

### 7.1 Debate kaydına ilişkin not

| Alan | Değer |
|------|-------|
| Biçim | 3 tur / 20 persona debate — ADR-029/030 kaydı ile aynı format |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-25 (frontmatter `debate` alanı ile aynı değer) |
| Tur 1 | 20 persona · 41 kaynak · IMPLEMENTED: viewport + `@media` + offline olay · PLANNED: manifest/SW/push/VAPID/Flutter (kod 0) · 15 kabul/neutral + 4 uyarı |
| Tur 2 | 4 itiraz → 4 çözüm → 3 şart (§6.2) |
| Tur 3 | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tech Lead | **✅** (2026-09-25) — debate sonucuyla `⏳ → ✅` |
| Frozen | Bu ADR **frozen değildir**; debate ✅ + Tech Lead ✅ tamam, **Arch Lead ⏳** — §7 3. satır tamamlanmadan `frozen` yapılmaz |
| Kural | Debate sonucu §6.1/§6.2'ye ve frontmatter `debate` alanına işlenir; `.ai/log.md` append ile kaydedilir; şart sahiplikleri §5.2 parçaları 4-7 |

---

**1.0.0 | 2026-09-25 | Created**
*ADR-031 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) → Tech Lead ✅ → 3 şart (§6.2) → frozen YOK (Arch Lead ⏳)*

*Authority: ADR-031 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
