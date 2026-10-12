---
title: "CoreMusic — ADR-064: Electronics Platform Architecture (L0-L6 · 5 Cihaz Sınıfı · Cihaz↔Servis Eşleme Matrisi · Servis Sayım Çelişkisi 13/11/9/7)"
type: "architecture-decision"
category: "electronics"
date: "2026-09-30"
updated: "2026-09-30"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic platform / cihaz-servis mimarisi (A0-A5, L0-L6) kararı: (a) **5 cihaz sınıfı** = gömülü · monitör · masaüstü · mobil · otomotiv (kullanıcı onaylı; `ui-design` T07-embedded/T17-monitor hizası) ve bunların `DeviceManager` 7 tipi + 45-tier/11 kategori ile **eşleme tablosu** — `4k-tv` tipinin sınıfı ve otomotiv tipinin PHP tespiti ⚠️ VERIFICATION REQUIRED, (b) **\"13 servis\" iddiası DOĞRULANAMADI** (tek iz `keys.md:163` → `electronic/service-architecture.md` = **0 dosya hayalet**) → bağlayıcı sayı **11 alan servisi** = ADR-039 §2.1 + `.ai/CLAUDE.md:106` \"Infra (11)\"; 13 satırı ⚠️ altında kalır ve vault reset'ine ertelenir, (c) **cihaz↔servis eşleme matrisi** (5 sınıf × 11 servis — BFF 4'lüsü `Desktop/Embedded/Mobile/Spa` IMPLEMENTED, otomotiv BFF **yok** ⚠️), (d) **sayım çelişkisi** 13 / 11 / 9 / 7 / 11-subdomain tek tabloya bağlandı (yeni sayı üretilmez — ADR-039 C1 kuralı), (e) **sınır** = ADR-061 (L6 + bileşen politikası), ADR-062 (DSP pipeline), ADR-063 (tasarım standartları) — üçü de yeniden alınmaz"
kaynak: "Disk kanıtı taraması (2026-09-30: `.ai/.decisions/index.md:95` slug satırı MEVCUT (`ADR-064-electronics-platform-architecture`) · `.ai/index.md:696` · `.ai/brain.md:1012` · `.ai/keys.md:163,288` · `.ai/MEMORY.md:673` · `.ai/VISION.md:253` · bu işlem öncesi `**/ADR-064*.md` = **0 dosya** → metin diskte YOKTU · `electronic/**` glob = **0 dosya** → `keys.md:163` hedefi hayalet · `shared/src/Device/DeviceManager.php:29-40` = **7 tip** (embedded/phone/tablet/laptop/desktop/4k-tv/4k-monitor) · `DeviceCssMap.php:8-33` 7 cihaz CSS + 4 view-mode (`home/pro/studio/car`) · `DeviceDetector.php:21-26` kırılımlar · `shared/src/Api/Bff/` = **4 BFF** (Spa/Mobile/Embedded/Desktop) + BffLayer · `shared/config/domain.php:7-15` = **7 subdomain** · `shared/src/Config/CLAUDE.md:45-55` = **9 satır** domain tablosu · `shared/AGENTS.md` §2 \"9 subdomain\" · `.ai/CLAUDE.md:106` K8 = \"…+ Infra (11)\" · `ADR-039 §2.1` = **11 servis** (3 IMPLEMENTED: auth 70 dosya · home 29 · assets 550; 8 PLANNED) · `architecture/k8-servis/README.md` §2 = **7** işlev · `ecosystem/README.md:27-33` = **7** servis (Download \"IMPLEMENTED\" ↔ ADR-039 \"PLANNED\" ⚠️ drift) · `ecosystem/index.md:302` = \"7 servis, 10 panel, 11 subdomain\" · `ui-design/00-device-matrix.md` = **11 kategori / 45 tier** · `ui-design/screens/` = T07-embedded **12** spec + T17-monitor **2** spec + shared **6** · `ui-design/flow/automotive` = 2 · `home.coremusic.net/pages/home.php` grep = 3 isabet (MEMORY:673 \"5 cihaz bloğu\" ile **kismen** tutuyor → ⚠️) · `ADR-061:219` şart 2 (ADR-064 doğrulama kapısı) · `git ls-files` `*.cpp|*.h|*.hpp|*.c|*.ts` = **0** → C++/Node servis kodu PLANNED) + web araştırması (**6 sorgu / 52 kaynak bildirimi** — 5 websearch + 1 exa; tekrar tespit edilmedi)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-064: Electronics Platform Architecture

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-09-30 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona — 18/2/0 KABUL)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-064-electronics-platform-architecture` (dizin otoritesi: [[../index.md]] satır 95 — gerçek disk slug'ı ile birebir hizalı ✅; üst görevde geçen `ADR-064-platform-architecture` kısası **diskte yoktur**, index'teki tam slug kullanılmıştır → §5.1/9)
> **İlgili kararlar:** [[ADR-061-electronics-architecture]] (L6 üst mimari + 5 bileşen kuralı — `:219` **Şart 2 / ADR-064 doğrulama kapısı bu ADR ile kapanır**) · [[ADR-062-dsp-pipeline-architecture]] (DSP boru hattı — kapsam dışı) · [[ADR-063-hardware-design-standards]] (tasarım standartları — kapsam dışı) · [[ADR-039-7-service-platform-architecture]] (**11 servis envanteri bağlayıcı** — bu ADR sayıyı yeniden almaz, çelişkiyi çözer) · [[ADR-031-mobile-strategy-pwa-flutter]] (mobil strateji) · [[ADR-037-wirelessconnect-integration]] (cihaz keşfi) · [[ADR-029-listening-rooms-social]] (oda senkronu) · [[ADR-058-centralized-auth-service]] (auth servisi — IMPLEMENTED eksen) · karar dizini [[../index.md]] **satır 95**.
> **⚠️ VERIFICATION REQUIRED:** "13 servis" rakamı **hiçbir disk listesinde yok** (tek iz hayalet `electronic/service-architecture.md`) → `brain.md:1012` / `index.md:696` / `keys.md:288` satırlarındaki 13 **bu ADR'de doğrulanmadı, düzeltmesi vault reset'ine ertelendi** (§5.1/1) · otomotiv sınıfının `DeviceManager`/`DeviceDetector` sabiti **yok** (`car` yalnız `DeviceCssMap.php:32` view-mode) · `4k-tv` tipi 5 sınıfın hiçbirine düşmüyor · monitör ↔ `DesktopBff` eşlemesi **kod okunmadan varsayım** · `ADR-083/084/085/086` ve `ADR-082` dosyaları diskte **YOK** → düz metin, wiki-link yok.
> **Bölüm sınırı (ADR-061/062/063 ile kenetli):** ADR-061 L6 katmanını + bileşen politikasını, ADR-062 DSP boru hattını, ADR-063 tasarım standartlarını yazdı; **platform/L0-L6 geneli + cihaz sınıfları + cihaz↔servis matrisi + servis sayım çelişkisi bu ADR'nindir** — üç karar da yeniden yazılmaz.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 7'deki "yeni ADR ≥ 088" ile arşivin 064 slotu arasındaki **numara çakışması ADR-061 §5.1/9 ve ADR-062/063 künyelerinden tekrar raporlanır, düzeltilmez**.

---

## 1. Bağlam (Context)

CoreMusic'in platform tarafı (A0-A5: HW/altyapı) iki sayıyla tanımlanmış durumdaydı: `brain.md:1012` → "ADR-064 | Electronics Platform Architecture (L0-L6, **5 cihaz, 13 servis**)". Bu iki sayı ADR-061'de **kanıtlanamadı** ve ADR-061 `:219`'da şart 2 olarak bu ADR'ye bağlandı: *"\"5 cihaz / 13 servis\" iddiası kanıtlanana kadar ⚠️ VERIFICATION REQUIRED kalır; kanıtsız sayı ADR-061'de tekrarlanmaz."* Aynı anda vault'ta servis envanteri için **birbirine girift beş ayrı sayım** (13 / 11 / 9 / 7 / 11-subdomain) ve cihaz için **üç ayrı katman sayısı** (5 / 7 / 45-tier) yaşıyor; hangisinin bağlayıcı olduğu hiçbir yerde yazılmamış. Bu ADR üç işi tek kayıtta kapatır: **(1)** 5 cihaz sınıfını tanımlar ve gerçek cihaz envanteriyle (7 PHP tipi, 45-tier) eşler, **(2)** "13 servis" iddiasını **doğrular veya düzeltir** (ADR-039'un 11 servisiyle çelişki dahil), **(3)** cihaz↔servis eşleme matrisini çizer. Kod üretmez, servis açmaz, cihaz tanıtmaz.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-30 taraması)

| İddia | Kanıt | Etiket |
|-------|-------|--------|
| ADR-064 slotu ayrılmış mı? | [[../index.md]] `:95` → `\| ../brain.md ADR-064-electronics-platform-architecture \| Electronics Platform Architecture \| Electronics \|` · [[../../index.md]] `:696` · [[../../brain.md]] `:1012` · [[../../keys.md]] `:288` | ✅ **KAYITLI** (4 indeks satırı) |
| ADR-064 dosyası bu işlem öncesi diskte var mıydı? | glob `.ai/.decisions/**/ADR-064*.md` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor |
| **"13 servis" nereden geliyor?** | [[../../keys.md]] `:163` → `service architecture, 13 servis \| electronic/service-architecture.md` · `electronic/**` glob = **0 dosya** · repo genelinde "13 servis" = **tek isabet** (bu keys satırı) | ❌ **HAYALET HEDEF — DOĞRULANAMADI** → §2(b) |
| **11 servis (bağlayıcı)** | [[ADR-039-7-service-platform-architecture]] `§2.1` = main · auth · music · media · download · admin · studio · car · home · assets · dev · [[../../CLAUDE.md]] `:106` K8 = "Control, Media, Audio, Device, Network, AI, Download **+ Infra (11)**" | ✅ **KAYITLI** (kullanıcı onaylı envanter) |
| 11'in fiziksel durumu | ADR-039 §2.1: **3 IMPLEMENTED** (`auth.coremusic.net/` 70 dosya · `home.coremusic.net/` 29 dosya · `assets.coremusic.net/` 550 dosya) + **8 PLANNED** (main/music/media/download/admin/studio/car/dev — 7'sinde dizin **YOK**, `*.ts` = 0) | ✅ **IMPLEMENTED/PLANNED ayrımı yazılı** |
| 9 subdomain | `shared/src/Config/CLAUDE.md:45-55` = **9 satır** (coremusic · music · admin · download · media · auth · home · car · studio) · `shared/AGENTS.md` §2 "domain.php (9 subdomain)" · `DomainConfig.php` "9 subdomain" | ✅ **ÇELİŞKİ (9 ↔ 11)** → §2(d) |
| 7 subdomain | `shared/config/domain.php:7-15` `subdomains` = **7** (auth · home · assets · music · admin · media · **api**) | ✅ **ÇELİŞKİ (7 ↔ 9 ↔ 11)** → §2(d) |
| 7 K8 servisi | `architecture/k8-servis/README.md` §2 (Control · Media · Audio · Device · Network · AI · Download) · `ecosystem/README.md:27-33` aynı 7'li · `ecosystem/index.md:302` "7 servis, 10 panel, 11 subdomain" | ✅ **işlev kesiti** (alan kesiti değil) |
| **"5 cihaz" nereden geliyor?** | [[../../brain.md]] `:1012` · [[../../index.md]] `:696` · [[../../keys.md]] `:288` "5 cihaz ailesi" · [[../../MEMORY.md]] `:673` "DeviceManager.php + home.php v6.0.0 (**5 cihaz HTML bloğu**)" · [[../../VISION.md]] `:253` Free plan "5 cihaz" = **cihaz limiti (farklı anlam)** | ⚠️ **kısmen destekli** → §2(a) |
| Cihaz tipi envanteri (kod) | `shared/src/Device/DeviceManager.php:29-40` = **7 tip** (`embedded, phone, tablet, laptop, desktop, 4k-tv, 4k-monitor`) · `DeviceCssMap.php:8-33` 7 cihaz CSS + **4 view-mode** (`home, pro, studio, car`) · `DeviceDetector.php:21-26` kırılım: phone ≤767 · tablet/embedded ≤1024 · laptop ≤1440 · desktop ≤2560 · 4k-tv ≤3840 | ✅ **IMPLEMENTED** (PHP) |
| UI katmanı | `ui-design/00-device-matrix.md:31-46` = **11 kategori / 45 tier** · `screens/` = T07-embedded **12** spec · T17-monitor-22fhd **2** spec · shared **6** · `flow/automotive` **2** · `flow/watch` **1** | ✅ **IMPLEMENTED (doküman)** |
| BFF (cihaz↔API köprüsü) | `shared/src/Api/Bff/` = `SpaBff.php` · `MobileBff.php` · `EmbeddedBff.php` · `DesktopBff.php` + `BffLayer.php` (**4 cihaz BFF'i**) | ✅ **IMPLEMENTED** — **otomotiv BFF'i yok** ⚠️ |
| `car` sınıfının kod karşılığı | `DeviceCssMap.php:32` `'car' => '09_ViewModes/v-car.css'` (view-mode) · `DeviceManager`/`DeviceDetector`'da `car` sabiti **YOK** | ⚠️ **EKSİK** → §5.1/3 |
| `4k-tv` sınıf karşılığı | 5 onaylı sınıfın (gömülü/monitör/masaüstü/mobil/otomotiv) **hiçbiri TV içermiyor**; `4k-tv` tipi sınıfsız | ⚠️ **BOŞLUK** → §5.1/4 |
| home.php 5 blok iddiası | [[../../MEMORY.md]] `:673` (2026-09-02) vs. bugünkü `home.coremusic.net/pages/home.php` grep = **3 isabet** (embedded class) | ⚠️ **kısmi teyit** — "5 blok" bugün doğrulanamadı |
| Servis↔cihaz bağı (envanter) | `ecosystem/README.md:33` **Download "IMPLEMENTED"** ↔ [[ADR-039-7-service-platform-architecture]] `:121` **"PLANNED"** (`download.coremusic.net/` glob = 0 dosya) | ⚠️ **KATALOG DRIFT** → §4.3/R5 |
| ADR-061 doğrulama şartı | [[ADR-061-electronics-architecture]] `:219` adım 14 "Şart 2 — ADR-064 doğrulama kapısı … ⏳ debate şartı" | ✅ **bu ADR ile kapanır** (§2(b)) |
| Kod kanıtı (C++/Node/Python servis) | `git ls-files` `*.cpp\|*.h\|*.hpp\|*.c\|*.ts` = **0 dosya** · `*.py` = 1 (vault scripti) | ⏳ **PLANNED** — Audio/Device/Network + download servisi kodsuz |
| `ADR-083/084/085/086` (SPA router, gateway, shared, event) | accepted/ glob = **0 bu numaralarda** (081/089/090/092 var) | ⚠️ **dosya yok → düz metin + V.R.** |

### 1.2 Sorun Tanımı

1. **"13 servis" kanıtsız ve tekrarlanıyor.** Tek iz hayalet bir keys satırı (`keys.md:163` → 0 dosyalık `electronic/`); buna karşılık bağlayıcı envanter 11 (ADR-039 + `.ai/CLAUDE.md:106`). Sayı düzeltilmeden her yeni doküman 13'ü veya 7'yi tekrar üretir.
2. **Beş servis sayımı yan yana yaşıyor, hiyerarşi yok.** 13 (beyan) · 11 (alan servisi) · 9 (Config tablosu) · 7 (domain.php subdomain) · 7 (K8 işlev) — hiçbiri diğerini referanslamıyor; "servis" kelimesi hem *alan* hem *işlev* için kullanılıyor (ADR-039 C1 aynı şeyi yazdı, burada platform düzlemi tamamlanır).
3. **Cihaz tarafında üç katman sayısı var, sınıf tanımı yok.** 5 (beyan) · 7 (PHP tipi) · 45 tier / 11 kategori (UI) — hangi tipin hangi "sınıfa" düştüğü, TV ve otomotiv gibi uçların nereye gittiği yazılı değil → cihaz-servis kopukluğu doğrudan buradan çıkıyor.
4. **Cihaz↔servis eşlemesi yok.** 4 BFF var ama hangi cihaz sınıfının hangi servislere gittiği hiçbir tabloda değil; `car` servisi PLANNED, otomotiv BFF'i yok, monitör için ayrı BFF yok.
5. **Katalog drift başlamış bile.** `ecosystem/README` Download'ı IMPLEMENTED yazıyor, diskte 0 dosya ve ADR-039 PLANNED diyor — envanter sürüklenmesinin ilk somut örneği.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "device fleet architecture multi-device platform design patterns 2025 2026" · (2) "service inventory service topology documentation best practices microservices catalog 2025" · (3) "edge cloud architecture split embedded device gateway cloud services 2025 when to process at edge" · (4) "consumer electronics product line architecture platform reuse shared baseline variants 2025" · (5) "multi-device design system architecture responsive desktop mobile TV automotive embedded consistent UI platform 2025" · (6) exa: "platform architecture consumer product across device classes (embedded, monitor, desktop, mobile, automotive) — device class taxonomy, shared services, per-device client vs server responsibilities" |
| Web Search **Konusu** | **(1)** cihaz filosu mimarisi (envanter, OTA, telemetri, kimlik); **(2)** servis envanteri/topoloji kataloğu ve KPI'ları; **(3)** kenut-bulut ayrımı (hangi iş yükü cihazda, hangi bulutta); **(4)** ürün ailesi/platform mimarisi (paylaşılan taban + varyant); **(5)** çok-cihazlı tasarım sistemi ve cihaz sınıfı taxonomisi; **(6)** cihaz sınıfı ↔ sorumluluk (istemci/sunucu) ayrımı. |
| Web Search **Bağlamı** | CoreMusic: vault'ta "5 cihaz / 13 servis" iddiası ADR-061'de doğrulanamadı; servis sayımı 13/11/9/7'ye bölünmüş; cihaz katmanı 7 PHP tipi + 45 tier olarak var ama sınıf tanımı yok; cihaz↔servis matrisi hiç yok. Araştırma bu dört boşluğu hedefliyor; sayılar **iç karar** olduğu için web yalnız *yapı ve kural* için okundu (ADR-039'un 18 kaynağını tekrarlamaz). |
| Web Search **Kısa Açıklama** | **(1)** Filo mimarisi cihaz **envanteri + kimliği + OTA + telemetri** üzerine kurulur; tekilleştirilmiş envanter olmadan filo yönetimi çalışmaz. **(2)** Servis kataloğu tek bir envanterdir: her servis için sahip, veri sınırı, sürüüm ve KPI kayıt altındadır; ikinci bir sayım kataloğu bozar. **(3)** Kenut-bulut ayrımı gecikme/çevrimdışı gereksinimine göre çizilir: gerçek zamanlı cihazda, orkestrasyon bulutta. **(4)** Ürün ailesi mimarisi "doğru sayıda ürün mimarisi" ilkesiyle paylaşılan taban + varyant üretir; platform = ürünlerin yeniden kullandığı ortak yetenekler. **(5)** Çok-cihazlı üründe ortak çekirdek + platform adaptörü (adapter) modeli baskındır; cihaz sınıfı taxonomisi (form factor) ortak framework'ü paylaşır. **(6)** Cihaz soyutlama katmanı + paylaşılan çekirdek, cihaz-servis eşlemesini tek yerde tutar. |
| Web Search **Uzun Açıklama** | **(i)** Filo: ICS/Geotab/FleetDM kaynakları cihaz envanteri, kimlik, OTA güncelleme ve telemetri akışını mimarinin temel direkleri olarak verir; sciencedirect (2025) sistem-wide IoT desenlerinde cihaz→gateway→servis zincirini tarif eder. **(ii)** Servis kataloğu: Cortex, enov8, Graphapp servis kataloğunun **tek envanter** olduğunu, sürüm/geriye uyumluluk bilgisini taşıdığını ve birden fazla sayımın KPI'ları bozduğunu yazar; microservices.io "service registry" deseni adreslenebilirliği ayrı katmanda çözer (ADR-039'un süreç-içi registry uyarısıyla aynı). **(iii)** Edge-cloud: arXiv state-of-practice anketi (2506.02003) kenut-bulut continuum'da istemci-sunucu modelini, Wevolver/TechBuzz gateway'lerin protokol çeviri + toplama yaptığını, gerçek zamanlı işin kenutta kalacağını anlatır. **(iv)** Ürün ailesi: capstera "platform architecture = ürünlerin yeniden kullandığı ortak yetenekler", designsociety "doğru sayıda ürün mimarisi" ilkesi, MDPI 2025 domain-engineering (yeniden kullanılabilir platform + varyant modeli) — az veya çok mimari ikisi de hatalıdır. **(v)** Çok-cihaz: AOSP form-factor bölümü tek framework'ün telefon/otomotiv/TV/giyilebilir form-factor'ları **aynı çekirdeğe** taşıdığını, form-factor'un yalnız kabuk + kısıt eklediğini yazar; basyskom (2025) gömülü HMI'da "gömülü cihaz = tek doğruluk kaynağı, sunucu + yerel/uzak istemci" modelini, Ricardo Lara paylaşılan oyuncu çekirdeği + adaptör (Tizen/webOS/BrightSign) modelini, Developex cihaz soyutlama katmanı + tek kimlik + modüler UI kütüphanesini önerir; Avalonia `OnFormFactor` ile cihaz kategorisinin (Desktop/Mobile) ayrı bir soyutlama olduğunu gösterir. |
| Web Search **Paragraf Veri Uzun** | 6 sorgu / **52 kaynak bildirimi** (tekrar tespit edilmedi): **(1, 10)** FleetDM vaka · sheridantech IoT fleet mimarisi · Saigon fleet development · Medium fleet design · HP Fleet Explorer · Geotab video · heavyvehicleinspection 7 trend · ICS IoT fleet (OTA bölümü) · sciencedirect "System-wide IoT design and programming" (2025, atıf 2) · techahead 2026. **(2, 10)** Cortex microservice catalog · Maruti best practices · LinkedIn dokümantasyon süreci · Wonderment 10 pratik · Graphapp catalog KPI · Stack Overflow catalog vs inventory · goReplay 8 pratik · Catio 2026 · microservices.io "Microservice Architecture" · enov8 "What is a Microservice Catalog". **(3, 7)** arXiv **2506.02003** edge-cloud continuum survey · iMOBDEV edge→cloud IoT pattern · emergentmind edge-cloud collaborative architecture · GeeksforGeeks edge-cloud · speedtesthq device/gateway/edge/cloud topolojileri · Wevolver IoT gateway (protokol çevirisi) · techbuzzonline edge-to-cloud workload placement. **(4, 10)** ResearchGate "Example Product Line Architecture" · MDPI 2025 ML product line engineering (Tekinerdogan, atıf 5) · designsociety "Good product line architecture design principles" (Mortensen, atıf 6) · PTC PureVariants · capstera platform architecture · medium architectural standards · 3DS blog PLE/MBSE consumer products · Centric PLM consumer electronics · tarjomefa SPL primer · LinkedIn platform mimarisi. **(5, 8)** appiko React Native vs Flutter 2025 (**LG webOS TV + Toyota otomotiv**) · digipixel 2025 (platformlar arası tutarlılık) · Hudasoft 2026 · LinkedIn cross-platform video engine ("TV/masaüstüne mobil UX'i dayatma") · Google Patents modular display platform · carletondesign multi-device web · Facebook GulfNews katlanabilir · ResearchGate multi-layered WIS. **(6, 7 — exa)** AOSP Internals **"Device Form Factors"** (tek framework ↔ AAOS/TV/Wear/XR) · Matter "Architecture & Design" (device type base/impl ayrımı, cihaz fabrikası) · Avalonia cross-platform architecture (`OnFormFactor`) · basyskom **"Flutter on Embedded: HMI, Mobile Apps and Middleware"** (2025-01 — gömülü = tek doğruluk kaynağı + sunucu/istemci) · ricardolara **"A shared player core for Tizen, webOS, and BrightSign"** (2026-02 — paylaşılan çekirdek + adaptör) · Microsoft mobile engineering HVC/ortak katman · developex **"Fragmentation Costs in IoT"** (2026-05 — cihaz soyutlama katmanı + tek kimlik + modüler UI + envanter denetimi faz 1). |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** filo mimarisi **tek cihaz envanteri** şart koşar → 5/7/45 sayılarının üç ayrı katman olarak etiketlenmesi ve tek eşleme tablosu zorunlu. **(2) Karar destekleniyor (boşluk doğrulandı):** servis kataloğu **tek** olmalı; CoreMusic'te 5 sayım var → 11'e bağlanan tek bağlayıcı, diğerleri referans kesiti. **(3) Karar destekleniyor:** gömülü/otomotiv sınıfı yerel çalışır, orkestrasyon sunucuda → cihaz↔servis matrisi "kim hangi servise gider" sorusunu tek tabloya indirir. **(4) Karar destekleniyor:** doğru sayıda ürün mimarisi + paylaşılan taban → 5 sınıf, 7 tipin **sınıflandırması**dır (tip silinmez, sınıfa düşer). **(5) Karar destekleniyor:** ortak çekirdek + adaptör + `OnFormFactor` benzeri soyutlama → BFF 4'lüsü bu modelin kod karşılığıdır; **otomotiv adaptörü yokluğu** dış kaynakla da desteklenen bir boşluk. **İtiraz/karşıt bulgu:** dış kaynaklar CoreMusic'in **sayılarını** doğrulayamaz (iç karar) → 13 için **hiçbir destek bulunamadı**, 11 için iç kanıt (ADR-039 + CLAUDE §5) geçerli; ayrıca TV/form-factor örnekleri `4k-tv` sınıfı boşluğunu **doğrular** (dış kaynakta TV ayrı form-factordur). |
| Web Search **Alınan Karar** | **(a) 5 cihaz sınıfı** = gömülü · monitör · masaüstü · mobil · otomotiv **onaylanır** (sınıf = karar; tip ≠ sınıf) ve `DeviceManager` 7 tipi + 45-tier ile **eşleme tablosu** yazılır; `4k-tv` sınıf boşluğu ve otomotiv PHP tespiti ⚠️ + §5.1 kapıları. **(b) "13 servis" DOĞRULANAMADI** → bağlayıcı sayı **11** (ADR-039 §2.1 + `.ai/CLAUDE.md:106`); 13 ⚠️ altında kalır, `brain/index/keys` düzeltmesi vault reset'ine ertelenir (rapor-only). **(c) Cihaz↔servis eşleme matrisi** (5 × 11) bu ADR §2.2-b'de kurulur; BFF 4'lüsü = IMPLEMENTED köprü, otomotiv satırı ⚠️. **(d) Sınır** = ADR-061 (L6 + politika), ADR-062 (pipeline), ADR-063 (standartlar) tekrar alınmaz. **(e) Çelişki kontrolü** = 13/11/9/7/11-subdomain tek tabloya bağlanır; **yeni sayı üretilmez** (ADR-039 C1 kuralı), katalog drift (Download IMPLEMENTED↔PLANNED) §5.1/7'ye yazılır. |
| Web Search **Sonuç** | **6/6 araştırmada karar destekleniyor:** cihaz envanteri tekilleşmeli (filo), servis kataloğu tek olmalı (katalog), kenut-bulut ayrımı sınıf bazlı (edge-cloud), sınıflandırma paylaşılan taban + varyant (product line), ortak çekirdek + adaptör (multi-device/AOSP/basyskom), cihaz soyutlama katmanı (developex). **Dört gerilim açıkça kabul edildi:** (1) "13 servis" **dış kaynakla da, iç listelerle de doğrulanamadı** → ⚠️ + 11'e bağlama; (2) cihaz sayıları **farklı katmanlar** (5 sınıf / 7 tip / 45 tier) — birbirine çevrilemez, eşleme zorunlu; (3) `4k-tv` ve otomotiv **sınıf/eşleme boşluğu** → §5.1/3-4; (4) katalog drift (Download) envanter sürüklenmesinin kanıtı → §4.3/R5. **⚠️ VERIFICATION REQUIRED:** 13 sayısı · monitör↔DesktopBff varsayımı · otomotiv BFF/sabiti · `home.php` 5 blok · `ADR-083/084/085/086` dosyaları · `electronic/service-architecture.md`. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar (ADR-029 / ADR-031 / ADR-037 dahil) |
| ADR-061 sınırı | L6 katman tanımı + kart/modül hiyerarşisi + bileşen **seçim politikası** kararlıdır; bu ADR yalnız **şart 2'yi** (sayı doğrulama) kapatır, L6'yı yeniden almaz |
| ADR-062 sınırı | DSP boru hattı (sıra, hard-RT, parametre yolu) kapsam dışı |
| ADR-063 sınırı | PCB/SI/EMC/güç + kalite standartları kapsam dışı |
| ADR-039 sınırı | **11 servis listesi, sahip+veri sınırı, ayrışma sırası bağlayıcıdır**; bu ADR listeyi değiştirmez, yalnız cihaz düzlemiyle eşler ve 13 çelişkisini çözer |
| Kod kanıtı | `*.cpp/h/hpp/c/ts` = 0 → Audio/Device/Network servisleri ve download (Node) **PLANNED**; "servis çalışıyor / cihaz keşfedildi" iddiası yazılmaz |
| Vault'a yazma yetkisi | Bu işlem yalnız bu ADR dosyasını + `log.md` append'ini yazar; `brain.md`/`index.md`/`keys.md` düzeltmeleri **vault reset'ine ertelenir** (In-Place Refactoring + rapor-only) |
| Numara çakışması | Kural 7 "yeni ADR ≥ 088" ↔ arşiv 064 slotu — ADR-061 §5.1/9 ve ADR-062/063 künyelerinden **aynı çakışma tekrar raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

CoreMusic'in platform düzlemi **bu ADR'de tek kayıtta** sabitlenir: **(a)** **5 cihaz sınıfı** (gömülü · monitör · masaüstü · mobil · otomotiv) tanınır ve mevcut 7 PHP tipi + 45-tier ile eşlenir; **(b)** **"13 servis" iddiası doğrulanamadı** → bağlayıcı servis sayısı **11**'dir (ADR-039 §2.1), 13 ⚠️ VERIFICATION REQUIRED altında vault satır düzeltmesine bağlanır; **(c)** **cihaz↔servis eşleme matrisi** (5 × 11) kurulur, BFF 4'lüsü IMPLEMENTED köprü olarak yazılır; **(d)** **sınır** = ADR-061/062/063 yeniden alınmaz; **(e)** **13 ↔ ADR-039 (11+PLANNED) çelişkisi** 13/11/9/7/11-subdomain sayım tablosunda kapatılır — yeni servis sayısı üretilmez. Karar kod üretmez, servis açmaz.

### 2.1 Neden Bu Seçenek?

Sayılar zaten vault'ta **var** (7 PHP tipi, 4 BFF, 11 servis, 45 tier); eksik olan **sınıf tanımı, eşleme ve hiyerarşi** — yani sorun *envanter değil, karar*. Bu ADR sıfırdan platform çizmek yerine mevcut envanteri **sınıflandırır ve bağlar**: (1) SSOT korunur (servis listesi ADR-039'da kalır, tekrarlanmaz), (2) ADR-061 `:219` şart 2 kapısını kapatır (kanıtsız sayı artık tekrarlanmaz, **düzeltme yolü yazılı**), (3) 5 sınıf karar olarak onaylanır ama **7 tip/45 tier silinmez** (tip ≠ sınıf), (4) matris cihaz-servis kopukluğunu tek tabloya indirir, (5) 13 yerine 11'e bağlanmak dış kaynaklarla **çelişmeyen** tek yoldur (web araştırması 13 için destek bulamadı, 11 için iç kanıt var). Web araştırması altı maddede de kararı **destekledi** (§1.3 Sonuç); karşıt bulgular (TV form-factordur, otomotiv adaptörü yok) risklere yazıldı.

### 2.2 Teknik Detaylar

**(a) 5 cihaz sınıfı — tanım + envanter eşlemesi (sınıf ≠ tip):**

| # | Cihaz sınıfı | UI karşılığı (kanonik) | Kod karşılığı (IMPLEMENTED) | Servis köprüsü | Etiket |
|---|--------------|------------------------|-----------------------------|----------------|--------|
| 1 | **Gömülü** | `EM-T07/T08` → `screens/T07-embedded/` (**12 spec**) | `DeviceManager::EMBEDDED` · `d-embedded.css` · `DeviceDetector` `EMBEDDED_MAX=1024` · `X-Device-Type: embedded` başlığı | `EmbeddedBff.php` | ✅ IMPLEMENTED |
| 2 | **Monitör** | `DM-T17…T24` → `screens/T17-monitor-22fhd/` (**2 spec**) | `FOUR_K_MON` (`4k-monitor`) + `DESKTOP` kırılımları · `d-4k.css` | `DesktopBff.php` (**varsayım** — BffLayer'da monitör BFF'i yok) | ⚠️ V.R. (§5.1/6) |
| 3 | **Masaüstü** | `DM/LP/DA` tier'ları (Laptop T12-T16 · Desktop App T37-T38) | `LAPTOP` · `DESKTOP` · `d-laptop.css` · `d-desktop.css` | `DesktopBff.php` | ✅ IMPLEMENTED |
| 4 | **Mobil** | `PH-T01…T05` · `TB-T06…T11` · `MO-T39/T40` + `flow/auth` akışları | `PHONE` · `TABLET` · `isMobile()`/`isTouchFirst()` · `d-phone.css`/`d-tablet.css` | `MobileBff.php` · `SpaBff.php` (web) | ✅ IMPLEMENTED |
| 5 | **Otomotiv** | `AU-T29/T30` + `flow/automotive/` (**2 md**) | `DeviceCssMap.php:32` `'car' => v-car.css` (**view-mode**) · `DeviceManager`'da `car` sabiti **YOK** | **BFF yok** · `car` servisi **PLANNED** | ⚠️ V.R. (§5.1/3) |

- **Sınıf kapsamları (karar):** *masaüstü* = laptop + desktop + desktop-app tier'ları; *monitör* = desktop-monitor tier'ları (4K dahil, `4k-monitor` tipi); *mobil* = phone + tablet + mobil uygulama tier'ları; *gömülü* = RPi5 T07/T08; *otomotiv* = AU-T29/T30.
- **Sınıfsız tip:** `4k-tv` (`FOUR_K_TV`, TV T25-T28) 5 sınıfın **hiçbirine** düşmüyor — dış kaynaklar TV'yi ayrı form-factor saydığı için (§1.3/5) **uydurulmaz**; ya 6. sınıf olur ya `monitör` kapsamına açıkça alınır → §5.1/4.
- **UI katmanı korunur:** 11 kategori / 45 tier (`00-device-matrix.md`) bu ADR'de **değişmez**; 5 sınıf = *platform* sınıfları, 45 tier = *UI* tier'ları (farklı katman, birbirine çevrilmez).
- **Cihaz limiti ayrı konu:** `VISION.md:253` "Free = 5 cihaz" = **kullanım limiti**, mimari sınıf sayısı **değildir** (anlam ayrımı bu satırda bağlanır).

**(b) Cihaz ↔ servis eşleme matrisi (5 sınıf × 11 servis):**

> Gösterim: ✅ = bu cihaz sınıfı bu servise **gider** ve servis fiziksel olarak var (IMPLEMENTED) · 🔵 = eşleme **karardır**, servis PLANNED · • = bu sınıf için kullanılmaz (karar) · ⚠️ = eşleme kanıtsız. Servis listesi ADR-039 §2.1'in kendisidir (tekrar edilmez, yalnız başlıklar).

| Cihaz sınıfı | main | auth | music | media | download | admin | studio | car | home | assets | dev |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **Gömülü** | 🔵 | ✅ | 🔵 | 🔵 | 🔵 | • | 🔵 | • | ✅ | ✅ | ⚠️ |
| **Monitör** | 🔵 | ✅ | 🔵 | 🔵 | 🔵 | 🔵 | 🔵 | • | ✅ | ✅ | ⚠️ |
| **Masaüstü** | 🔵 | ✅ | 🔵 | 🔵 | 🔵 | 🔵 | 🔵 | • | ✅ | ✅ | ⚠️ |
| **Mobil** | 🔵 | ✅ | 🔵 | 🔵 | 🔵 | • | • | • | ✅ | ✅ | ⚠️ |
| **Otomotiv** | • | ✅ | 🔵 | 🔵 | • | • | • | 🔵 | ✅ | ✅ | ⚠️ |

- **Ortak sütunlar:** `auth` (3 IMPLEMENTED dizinden biri — [[ADR-058-centralized-auth-service]]), `home`, `assets` **beş sınıfın tamamına** açıktır (tek kimlik + tek statik katman — §1.3/6 "shared authentication" dersi).
- **Sınır sütunları:** `admin` yalnız monitör/masaüstü (dokunmatik/gömülü mobilde yönetim arayüzü yok — karar); `download` gömülü/otomotiv hariç (yerel depolama ≠ indirme işi); `studio` masaüstü+monitör (prodüksiyon işi); `car` yalnız otomotiv + gömülü/monitör/masaüstü **uzaktan** (oda/ekran senkronu ayrı konu → ADR-029 kapsamı).
- **Köprü satırı (IMPLEMENTED):** `BffLayer.php` → `SpaBff` (web/tümü) · `MobileBff` (mobil) · `EmbeddedBff` (gömülü) · `DesktopBff` (masaüstü **+ monitör varsayımı ⚠️**) · **otomotiv için BFF yok**.
- **PLANNED olan her şey** (main/music/media/download/admin/studio/car/dev) kod kanıtsızdır (`*.ts` = 0, 8 dizin YOK) → ✅ işareti yalnız 3 IMPLEMENTED servise konur.

**(c) Servis sayım çelişkisi — tek tablo (yeni sayı üretilmez):**

| Sayı | Kaynak (kesit) | Ne saydığı | Bu ADR'deki durumu |
|------|----------------|------------|--------------------|
| **13** | `keys.md:163` → `electronic/service-architecture.md` (**0 dosya**) · `brain.md:1012` · `index.md:696` · `keys.md:288` | bilinmiyor — hayalet hedef | ❌ **DOĞRULANAMADI** → ⚠️ V.R., düzeltme §5.1/1 (reset) |
| **11** | [[ADR-039-7-service-platform-architecture]] §2.1 · `.ai/CLAUDE.md:106` "Infra (11)" | **alan/doman servisi** (main…dev) | ✅ **BAĞLAYICI** |
| **9** | `shared/src/Config/CLAUDE.md:45-55` · `shared/AGENTS.md` §2 | Config tablosu satırı (api/assets **yok**) | ⚠️ alt küme/kesit — 11'e bağlandı |
| **7** | `shared/config/domain.php:7-15` | subdomain listesi (**api** dahil, car/studio/download **yok**) | ⚠️ altyapı kesiti — 11'e bağlandı |
| **7** | `architecture/k8-servis/README.md` §2 · `ecosystem/README.md:27-33` | **işlevsel K8 düğümü** (Control…Download) | ⚠️ farklı düzlem (alan ≠ işlev) |
| **11 subdomain** | `ecosystem/index.md:302` | panel/alt alan sayımı | ⚠️ panel kesiti |

→ **Çözüm:** bağlayıcı envanter **11 alan servisi** (ADR-039); 9/7/7/11-subdomain aynı platformun **farklı kesitleridir**, birbirinin rakibi değildir; **13** hiçbir kesitle örtüşmez ve bu ADR'de **doğrulanmış sayılmaz**. `dev` servisinin `ADR-082` hedefi diskte YOK (ADR-039 C2) → düz metin + ⚠️.

**(d) Sınır (tekrar yok):** L6 katman/hiyerarşi + bileşen politikası → [[ADR-061-electronics-architecture]] · DSP pipeline/RT → [[ADR-062-dsp-pipeline-architecture]] · PCB/SI/EMC/kalite standartları → [[ADR-063-hardware-design-standards]] · servis listesi/sahip+veri sınırı/ayrışma sırası → [[ADR-039-7-service-platform-architecture]] · mobil strateji (PWA/Flutter) → [[ADR-031-mobile-strategy-pwa-flutter]] · cihaz keşfi/WirelessConnect → [[ADR-037-wirelessconnect-integration]] · oda senkronu → [[ADR-029-listening-rooms-social]] · SPA router / gateway / shared / event (ADR-083/084/085/086) **dosyalar diskte YOK → düz metin + ⚠️** (başlık ve slot bilgisi `brain.md:1021-1024`'te vardır).

**(e) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

| Katman | Öğe | Durum | Kanıt |
|--------|-----|-------|-------|
| Cihaz tespiti/CSS | 7 tip + 4 view-mode + kırılımlar | ✅ IMPLEMENTED | `DeviceManager.php:29-40` · `DeviceCssMap.php:8-33` · `DeviceDetector.php:21-26` |
| Cihaz↔API köprüsü | 4 BFF + BffLayer | ✅ IMPLEMENTED | `shared/src/Api/Bff/*.php` (5 dosya) |
| Servis envanteri | auth · home · assets | ✅ IMPLEMENTED | 70 / 29 / 550 dosya (ADR-039 §2.1) |
| Servis envanteri | main · music · media · download · admin · studio · car · dev | ⏳ PLANNED | 8 dizinde dizin YOK, `*.ts` = 0 |
| C++/DSP servisleri | Audio · Device · Network (K8) | ⏳ PLANNED | `*.cpp/h` = 0 |
| Otomotiv adaptörü | `car` tespit sabiti + BFF | ⚠️ V.R. | yalnız `v-car.css` view-mode |
| UI | 45-tier / 11 kategori / 20 spec | ✅ IMPLEMENTED (doküman) | `00-device-matrix.md` · `screens/` (12+2+6) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **"13 servis" olduğu gibi onayla** (brain/keys satırını aynen kabul et) | Mevcut 4 indeks satırına dokunulmaz | 13'ü destekleyen **tek kaynak 0 dosyalık hayalet**; ADR-039'un 11'i ve `.ai/CLAUDE.md:106` ile doğrudan çelişir; ADR-061 şart 2 gereği kanıtsız sayı tekrarlanamaz | Doğrulama ADR-061 `:219`'un şartı; **13 doğrulanamadı → ret** (§2(c)). |
| 2 | **Sayım olarak 7 K8 işlev düğümünü al** (Control…Download) | `ecosystem/README` ile hizalı, az sayı | "Servis" iki şey için kullanılıyor (alan ↔ işlev); 7, alan servisi listesini (main/auth/home/assets…) temsil etmez; ADR-039 C1 aynı ret gerekçesini yazdı | Farklı düzlem, yanlış sayım ekseni → **ret**; kesit olarak tabloda bağlandı (§2(c)). |
| 3 | **"5 cihaz"ı `DeviceManager` 7 tipiyle değiştir** (5 → 7 yap) | Kod kanıtıyla birebir | Sınıf ≠ tip: 45-tier/11 kategori UI katmanı 7 tipi de kapsar; sayı değişimi ADR-061 şartını **kaçırır** (idcia zaten kanıtsızdı, 7 yeni bir iddia olurdu) | 7 tip **korunur**, 5 = sınıflandırma kararı olarak yazılır (§2(a)) → **ret (sayı değişimi)**. |
| 4 | **Cihaz↔servis matrisini ADR-039'a ekle** (bu ADR'yi yazma) | Tek envanter dosyasında toplanır | ADR-039 servis **düzlemi** içindir; cihaz sınıfı ve UI/BFF eşlemesi orada konu dışı; ayrıca ADR-064 slotu ve ADR-061 şart 2 açık kalır | SRP + şart 2 bu ADR'ye bağlandı → **ret**; ADR-039'a **atıf** ile bağlanır. |
| 5 | **`4k-tv`'yi `monitör` sınıfına sessizce dahil et** | Sınıf sayısı 5'te kalır | Dış kaynaklar TV'yi ayrı form-factor (10-foot UI, D-pad) sayar; sessiz dahil etme **uydurmadır** | Boşluk açık yazılır + karar §5.1/4'e (insan/debate) bağlanır → **ret (sessiz absorb)**. |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Şart 2 kapanır:** ADR-061 `:219`'daki "ADR-064 doğrulama kapısı" bu ADR ile **doldu** — 13 doğrulanamadı ve *neden* + *düzeltme yolu* yazılı; kanıtsız sayı artık tekrar edilmez.
- **Tek bağlayıcı servis sayısı:** 11 (ADR-039) → yeni dokümanlar 13/9/7 üretmez; sayım çelişkisi 5 satırlık tabloda **kesit ayrımıyla** kapanır.
- **Cihaz-servis kopukluğu tek tabloya iner:** 5 × 11 matris + BFF köprüsü ile "hangi cihaz hangi servise gider" sorusunun tek cevabı var; boş hücreler (⚠️/PLANNED) görünür.
- **Sınıf/Tip/Tier ayrımı:** 5 sınıf · 7 tip · 45 tier üçü de **korunur** ve eşlenir → iki agent aynı cihazı farklı saymaz.
- **Katalog drift görünür hale gelir:** Download IMPLEMENTED↔PLANNED çelişkisi ilk örnek olarak kayda geçti (§5.1/7).

### 4.2 Olumsuz Sonuçlar

- **Dört vault satırı düzeltilmeden bırakıldı:** `brain.md:1012` · `index.md:696` · `keys.md:288` "13 servis" ve `keys.md:163` hayalet hedef — reset'e ertelendi (bu işlem In-Place Refactoring + rapor-only); **arada kalan sürede 13 sayısı vault'ta hâlâ okunabilir** (⚠️ taşımaya devam).
- **İki eşleme varsayım/eksik kaldı:** monitör↔`DesktopBff` (kod okunmadı) ve otomotiv tespiti — matrisin iki hücresi ⚠️.
- **`4k-tv` sınıf kararı insan/debate kapısında:** 5 sınıf listesi TV'yi kapsamıyor; UI'da 4 tier (T25-T28) buna rağmen var.
- **Matris bir karartma değil, karardır:** `admin`/`download`/`studio` sınırları gerekçeli ama **kodlanmamış**; uygulama PLANNED.
- **Tek ek belge:** vault'ta platform nodu bir ADR daha kazandı; indeks/brain güncellemeleri sonraki vault işlemine kalır (bu işlemde yalnız ADR + log append).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **Envanter sürüklenmesi (inventory drift)** — 5 sayım + ertelenmiş 13 satırı yüzünden yeni doküman eski sayıyı kopyalar | 4 (çok olası) | 3 (orta) | §2(c) tek tablo + §5.1/1 reset düzeltmesi; yeni sayı iddiası bu ADR'ye bağlanır (ADR-039 C1 kuralı) |
| R2 | **Cihaz-servis kopukluğu** — eşleme matrisi kodda değil bu ADR'de; BFF'e yeni cihaz eklendiğinde matris unutulur | 3 (olası) | 4 (yüksek) | §2(a-b) matrisi `BffLayer` + `k8-servis/` ile çapraz bağlanır (§5.1/5); otomotiv adaptörü §5.1/3 |
| R3 | **Tek cihaz bağımlılığı** — tüm sınıf davranışı `DESKTOP` varsayılanına (`DeviceCssMap` fallback) düşerse gömülü/otomotiv sessiz bozulur | 3 (olası) | 4 (yüksek) | `toCssPath` fallback'i `desktop` (kod gerçeği); sınıf bazlı kabul testleri §5.1/6; TV sınıfı kararı §5.1/4 |
| R4 | **Katalog drift** — `ecosystem/README:33` Download IMPLEMENTED ↔ diskte 0 dosya (ilk somut örnek) | 4 (çok olası) | 3 (orta) | §5.1/7 tek yönlü düzeltme: envanter satırları yalnız ADR-039 §2.1'e göre yazılır; IMPLEMENTED etiketi dosya kanıtsız konmaz |
| R5 | **Kanıtsız sayı/idcia yayılımı** — 13 · "5 blok" · monitör BFF · otomotiv sabiti kod kanıtsız | 3 (olası) | 4 (yüksek) | Künye ⚠️ satırı + §1.1 etiket sütunu; hiçbir yere "13 servis var / 5 cihaz kanıtlandı" **yazılmaz**; doğrulama kapıları §5.1/1,3,4,6 |

### 4.4 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| ADR-061 şart 2 (doğrulama kapısı) | [[ADR-061-electronics-architecture]] `:219` | ✅ **bu ADR ile kapandı** (13 → V.R. + 11 bağlama) |
| Servis envanteri bağlayıcılığı | [[ADR-039-7-service-platform-architecture]] §2.1 + `C1/C2` | ✅ çelişki bu ADR §2(c)'de bağlandı, liste **değişmedi** |
| Pipeline ölçüm/PLATFORM ayrımı | [[ADR-062-dsp-pipeline-architecture]] | ✅ sınır korundu (kapsam dışı) |
| Standartlar ayrımı | [[ADR-063-hardware-design-standards]] `:127` "platform ADR-064" | ✅ boşluk bu ADR ile doldu |
| UI tier/ekran envanteri | [[../../ui-design/00-device-matrix]] (`screens/` 12+2+6) | ✅ eşleme §2(a) — tier değişmedi |
| SPA router / gateway / shared / event | `ADR-083 · ADR-084 · ADR-085 · ADR-086` | ⚠️ dosyalar diskte YOK → düz metin |
| `electronic/` hayalet SSOT | `keys.md:163` → `electronic/service-architecture.md` | ⚠️ 0 dosya → §5.1/2 |
| `index.md:95` `[[../brain.md]]` hedefi | [[../index.md]] satır 95 | ⚠️ slug satırı var, **hedef düzeltmesi reset'e ertelendi** (§5.1/9) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | `brain.md:1012` · `index.md:696` · `keys.md:288` satırlarındaki "**13 servis**" → "11 servis (ADR-039)" düzeltmesi + ADR-064 satır metni | MO (vault-updater) | 0.5 gün | ⏳ **bu işlemde dokunulmadı** (reset'e ertelendi — raporlandı) |
| 2 | `keys.md:163` hayalet hedef (`electronic/service-architecture.md`) kaldırma/ yeniden yönlendirme kararı | MO + Vault Steward | 0.5 gün | ⏳ reset'e ertelendi |
| 3 | Otomotiv sınıfı PHP tespiti: `DeviceManager`/`DeviceDetector`'a `car` sabiti + `Embedded`-tarzı başlık/agent kuralı (view-mode `v-car.css` zaten var) | Embedded + Backend | 1 gün | ⏳ PLANNED (⚠️ V.R.) |
| 4 | `4k-tv` (T25-T28) için sınıf kararı: 6. sınıf olarak ekle **ya da** `monitör` kapsamına **açıkça** al (sessiz absorb yasak) | Debate + insan | 0.5 gün | ⏳ PENDING (§3/5 reddedildi) |
| 5 | Cihaz↔servis matrisini `architecture/k8-servis/README.md` + `.ai/architecture/index.md` K8 satırına bağla (matrisin ikinci kopyası değil, **atıf**) | MO (vault-updater) | 0.5 gün | ⏳ PLANNED |
| 6 | `BffLayer.php` okunup monitör↔`DesktopBff` varsayımının teyidi (ya da `MonitorBff` kararı) + sınıf bazlı kabul testleri | Backend + QA | 1 gün | ⏳ PLANNED (⚠️ V.R.) |
| 7 | `ecosystem/README.md:33` "Download IMPLEMENTED" ↔ ADR-039 "PLANNED" katalog drift düzeltmesi | MO (vault-updater) | 0.5 gün | ⏳ **bu işlemde dokunulmadı** (raporlandı) |
| 8 | Debate 3 tur + Tech Lead onayı | MO + persona | 1 gün | ✅ **TAMAMLANDI** (2026-09-30 — 3 tur / 20 persona, 18/2/0 KABUL — §7.2) |
| 9 | `.decisions/index.md:95` `[[../brain.md]]` → gerçek ADR hedefine düzeltme | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi** (bu işlemde rapor-only) |
| 10 | `download/` vb. PLANNED dizinler + C++/Node kodu geldikçe ADR-039 §2.1 durum satırlarının güncellenmesi (envanter tek elden) | Backend + DevOps | sürekli | ⏳ PLANNED |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `[[../index.md]]` satır 95 **silinmez** (değişmez kalır), `log.md`'ye `ADR-064 RED (debate …)` append edilir; bu durumda (i) ADR-061 `:219` şart 2 **açık kalır**, (ii) 13 sayısı doğrulanmamış olarak `⚠️ VERIFICATION REQUIRED` altında yaşamaya devam eder, (iii) cihaz↔servis matrisi **kural olmaktan çıkar** ve §2(a-b) "öneri" statüsüne döner (envanter dosyaları değişmez — SSOT'da bozulma olmaz). Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır, `log.md` satırı **silinmez** (append-only), `brain/index/keys` satırları zaten düzenlenmemiştir. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

---

### 5.3 Debate Şartları (3 şart — bağlayıcı, §7.2 sonucu)

| # | Şart | Kapsam | Sorumlu | İlişkili adım | Durum |
|---|------|--------|---------|---------------|-------|
| 1 | **Sayı düzeltme kapısı** — `brain.md:1012` · `keys.md:163`/`:288` · `index.md:696` satırlarındaki "13 servis" → "11 servis (ADR-039)" düzeltmesi + hayalet `electronic/service-architecture.md` hedefinin kaldırılması/yeniden yönlendirilmesi (**reset kapsamı**) | vault reset | MO (vault-updater) + Vault Steward | §5.1/1 · §5.1/2 | ⏳ açık (rapor-only → reset) |
| 2a | **Cihaz sınıfı tamamlama fazı** — `4k-tv` (T25-T28) sınıf kararı (6. sınıf **ya da** `monitör` kapsamına **açıkça** alma; sessiz absorb yasak) + otomotiv sınıfı için `DeviceManager`/`DeviceDetector`'a `car` sabiti | cihaz sınıfı | Debate + insan · Embedded + Backend | §5.1/3 · §5.1/4 | ⏳ açık (⚠️ V.R.) |
| 2b | **Katalog drift düzeltmesi** — `ecosystem/README.md:33` "Download IMPLEMENTED" ↔ ADR-039 "PLANNED" çelişkisinin `ecosystem/README` çapraz düzeltilmesi (envanter satırları yalnız ADR-039 §2.1'e göre yazılır) | katalog | MO (vault-updater) | §5.1/7 | ⏳ açık (rapor-only) |
| 3 | **Monitör varsayımı doğrulaması** — `BffLayer.php` okunup monitör↔`DesktopBff` eşlemesinin teyidi (ya da `MonitorBff` kararı) + sınıf bazlı kabul testleri | BFF köprüsü | Backend + QA | §5.1/6 | ⏳ açık (⚠️ V.R.) |

> **Kural:** Üç şart da kapanmadan ilgili hücreler ⚠️ VERIFICATION REQUIRED altında taşınır; şartlar §7.2'deki **18 kabul** oyunun bağlayıcı çıktısıdır ve mevcut §5.1 adımlarını **değiştirmez, kenetler**. Frozen'a geçiş yoktur (ADR-001–037 dışı).

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır | Ne | Etiket |
|-------|-------|----|--------|
| `.ai/.decisions/index.md` | `:95` | slug `ADR-064-electronics-platform-architecture` | ✅ hizalı (dosya adı ile birebir) |
| `.ai/index.md` | `:696` | "ADR-064 … (L0-L6, 5 cihaz, 13 servis)" | ⚠️ 13 → §5.1/1 (reset) |
| `.ai/brain.md` | `:1012` | aynı özet satırı | ⚠️ 13 → §5.1/1 |
| `.ai/keys.md` | `:163` · `:288` | hayalet `electronic/service-architecture.md` · "5 cihaz ailesi, 13 servis" | ❌ 0 dosya → ⚠️ §5.1/2 |
| `.ai/CLAUDE.md` | `:106` | K8 = "…+ Infra **(11)**" | ✅ 11 hizası |
| `.ai/.decisions/accepted/ADR-039-7-service-platform-architecture.md` | `§2.1` (`:113-131`) · `C1/C2` | 11 servis + IMPLEMENTED/PLANNED + 7 kesit notu | ✅ dosya var — **bağlayıcı envanter** |
| `.ai/.decisions/accepted/ADR-061-electronics-architecture.md` | `:22` · `:129` · `:219` | V.R. şart 2 + "doğrulanamadı" notu | ✅ dosya var — **bu ADR kapatır** |
| `.ai/.decisions/accepted/ADR-062-dsp-pipeline-architecture.md` · `ADR-063-hardware-design-standards.md` | künye · `:127,178` | sınır ve "ADR-064 diskte YOK" kaydı | ✅ dosya var |
| `.ai/CLAUDE.md` · `.ai/architecture/index.md` · `.ai/ecosystem/README.md` · `.ai/ecosystem/index.md` | `:106` · `:319` · `:27-33` · `:302` | K8 11 · k8 14 dosya · 7 servis (Download ⚠️) · 7 servis/11 subdomain | ✅ / ⚠️ drift → §5.1/7 |
| `.ai/architecture/k8-servis/README.md` | §2 · §3 · §5 | 7 işlev düğümü · 9 olay · doğrudan çağrı yasak | ✅ IMPLEMENTED (doküman) |
| `shared/src/Device/DeviceManager.php` · `DeviceCssMap.php` · `DeviceDetector.php` | `:29-40` · `:8-33` · `:21-26` | 7 tip · 7 CSS + 4 view-mode · kırılımlar | ✅ IMPLEMENTED (kod) |
| `shared/src/Api/Bff/*.php` | 5 dosya | `BffLayer` + Spa/Mobile/Embedded/Desktop BFF | ✅ IMPLEMENTED (kod) — otomotiv **yok** |
| `shared/config/domain.php` · `shared/src/Config/CLAUDE.md` · `shared/AGENTS.md` | `:7-15` · `:45-55` · §2 | 7 subdomain · 9 satır tablo · "9 subdomain" | ⚠️ çelişki → §2(c) |
| `.ai/ui-design/00-device-matrix.md` | `:31-48` · `:373-374` | 11 kategori / 45 tier · EM-T07/T08 | ✅ IMPLEMENTED (doküman) |
| `.ai/ui-design/screens/` · `flow/` | T07 **12** · T17 **2** · shared **6** · automotive **2** | cihaz sınıfı ekran spec'leri | ✅ / `T17 welcome-popup` draft ⚠️ |
| `.ai/MEMORY.md` · `.ai/VISION.md` | `:673` · `:253` | "5 cihaz bloğu" (2026-09-02) · "Free = 5 cihaz" (limit) | ⚠️ kısmi / anlam ayrımı |
| `home.coremusic.net/pages/home.php` | grep = 3 isabet | "5 blok" bugün doğrulanamadı | ⚠️ V.R. |
| `git ls-files` | — | `*.cpp\|h\|hpp\|c\|ts` = **0** | ⏳ PLANNED |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-063-hardware-design-standards]] · sınır kaynağı: [[ADR-061-electronics-architecture]] · envanter kaynağı: [[ADR-039-7-service-platform-architecture]]
- İlgili ADR'ler: [[ADR-062-dsp-pipeline-architecture]] · [[ADR-031-mobile-strategy-pwa-flutter]] · [[ADR-037-wirelessconnect-integration]] · [[ADR-029-listening-rooms-social]] · [[ADR-058-centralized-auth-service]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-026-download-service-architecture]] · [[ADR-032-ipc-contract-versioning]] · [[ADR-005-ultrathink-protocol]]
- Vault kökü: [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../MEMORY.md]] · [[../../log.md]] · [[../../CLAUDE.md]] · [[../index.md]]
- Spec/kanıt dosyaları: [[../../ui-design/00-device-matrix]] · [[../../architecture/index]] · [[../../../shared/src/Config/CLAUDE.md]]
- Dizin kayıtları (düz metin — dizin hedefidir, .md değildir → wiki-link değil): `shared/src/Device/` · `shared/src/Api/Bff/` · `ui-design/screens/T07-embedded/` (12 md) · `ui-design/screens/T17-monitor-22fhd/` (2 md) · `ui-design/flow/automotive/` · `architecture/k8-servis/` · `ecosystem/` · `servers/`
- Diskte **olmayan** (düz metin + ⚠️): `ADR-082` · `ADR-083` · `ADR-084` · `ADR-085` · `ADR-086` · `electronic/` (0 dosya) · `electronic/service-architecture.md` · `download.coremusic.net/` (0 dosya) · C++/Node/Python servis kodu (`*.cpp/h/hpp/c/ts` = 0)

### 6.3 Debate Kaydı

| Kayıt | Değer |
|-------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona (2026-09-30): Tur 1 bulgu · Tur 2 itiraz→çözüm (4 itiraz → 4 çözüm → 3 şart) · Tur 3 oy **18 kabul / 2 çekimser / 0 red → KABUL** (§7.2) |
| Bağlayıcı şartlar | **3 şart** → **§5.3**: (1) sayı düzeltme kapısı (`brain`/`keys`/`index`) · (2a) cihaz sınıfı tamamlama + (2b) katalog drift · (3) monitör varsayımı doğrulaması |
| Tech Lead | ✅ **2026-09-30** (debate sonucu ile) |
| Audit | `.ai/log.md` append (append-only): `ADR-064 yazıldı (debate PENDING)` · `ADR-064 debate 3/20 kaydedildi (18/2/0 KABUL) + Tech Lead ✅ + 3 şart` |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-09-30 | ✅ |
| Tech Lead | — (debate persona onayı) | 2026-09-30 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** (3 tur / 20 persona, 18/2/0 **KABUL**) |
| Tur sayısı | 3 / 3 |
| Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tech Lead | ✅ **TAMAMLANDI (2026-09-30)** |
| Arch Lead | ⏳ (debate kapsamı dışı — onay akışı §7.1'de açık kalır) |

> **Debate kaydı (2026-09-30 — 3 tur / 20 persona):**
>
> **Tur 1 — 20 persona / bulgu:** "13 servis" **DOĞRULANAMADI** (tek kaynak `keys.md:163` → hayalet `electronic/service-architecture.md` = 0 dosya) → bağlayıcı sayı **11** `ADR-039 §2.1` + `.ai/CLAUDE.md:106` "Infra (11)" ile bağlandı; 9/7/7/11-subdomain alt alanların tamamı ⚠️/tablo ile raporlandı; **5 cihaz sınıfı ✅** (gömülü/monitör/masaüstü/mobil/otomotiv) + `DeviceManager` 7 tip + 45-tier/11-kategori eşlemesi doğrulandı; boşluklar: `4k-tv` sınıfsız · otomotiv `DeviceManager` sabiti yok · otomotiv BFF yok · monitör↔`DesktopBff` varsayımı ⚠️ · `home.php` "5 blok" kısmi teyit (bugünkü grep 3) · katalog drift `ecosystem/README.md:33` Download IMPLEMENTED ↔ ADR-039 PLANNED · slug `ADR-064-electronics-platform-architecture` = `index.md:95` hizalı · **52 kaynak / 6 sorgu** · ADR-061 Şart 2 kapısı bu ADR ile kapandı · `brain.md:1012` / `index.md:696` / `keys.md:163,288` düzeltilmedi (reset'e) → **16 kabul/neutral, 4 uyarı** (Critic: V.R. + düzeltme şartı · DevOps: drift · Data: hayalet).
>
> **Tur 2 — itiraz→çözüm:** (1) 13 servis hayaleti + `brain`/`keys` yanlış satırları → **düzeltme kapısı** (reset kapsamı: `brain.md:1012`, `keys.md:163/288`, `index.md:696`) → **Şart 1** · (2) `4k-tv` sınıfsız + otomotiv `DeviceManager` sabiti yok → **cihaz sınıfı tamamlama fazı** → **Şart 2a** · (3) katalog drift (Download IMPLEMENTED ↔ PLANNED) → `ecosystem/README` çapraz düzeltme → **Şart 2b** · (4) monitör↔`DesktopBff` varsayımı kanıtsız → **V.R. + doğrulama** → **Şart 3**.
>
> **Tur 3 — oy:** **18 kabul / 2 çekimser / 0 red → KABUL.** **3 bağlayıcı şart** §5.3'e kenetlendi: **(1)** sayı düzeltme kapısı (`brain`/`keys`/`index`), **(2)** cihaz sınıfı tamamlama (2a) + katalog drift düzeltmesi (2b), **(3)** monitör varsayımı doğrulaması. **Tech Lead ✅**; §5.1/8 kapandı; sonuç `log.md`'ye append edildi. ADR-001–037 frozen kapsamı dışındadır; "5 cihaz / 13 servis" iddiasının **13** bileşeni şart 1 kapanana kadar ⚠️ VERIFICATION REQUIRED **olarak kalır** (ADR-061 `:219` şartının gereği) — debate sonucu bu ⚠️'yi **kaldırmaz, kapıya bağlar**. RED çıksaydı §5.2 uygulanacaktı; sonuç KABUL olduğundan dosya `accepted` kalır.

---

*1.0.0 | 2026-09-30 | Created — ADR-064 Electronics Platform Architecture (5 cihaz sınıfı · 11 servis bağlama · cihaz↔servis matrisi · debate PENDING)*
*1.0.1 | 2026-09-30 | Debate tamamlandı — 3 tur / 20 persona, 18/2/0 KABUL · Tech Lead ✅ · 3 şart (§5.3) · debate ✅*
*Authority: CoreMusic Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
