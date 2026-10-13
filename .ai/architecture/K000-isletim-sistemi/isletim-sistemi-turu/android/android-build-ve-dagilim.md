---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/android/android-build-ve-dagilim
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/android
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Android — Build ve Dağıtım (Gradle · NDK · ABI · R8/ProGuard · Play dağıtımı)

**Kapsam:** Android uygulama/yerel kod **derleme ve dağıtımı** zincirinin resmi
doküman kanıtlı haritası: ABI matrisi ve yerel kütüphane klasörleri, APK
bölme/APK Split ve App Bundle, R8 kod küçültme (ProGuard kuralları), multidex
(64K referans sınırı), build tipi/ürün çeşidi (variant) kurgusu, Play imzalama
(üç anahtar kavramı) ve 64-bit mimari zorunluluğu. **Bu depoda Android build
YOKTUR** (kanıt §15.1) → gerçek depo kodu yalnızca "Android yokluğu" kanıtı
olarak kullanılır.

**Yöntem (RED TEAM / TRUTH MODE):** Her iddia 2026-10-12 tarihinde HTTP 200 ile
doğrulanmış `developer.android.com` sayfalarından gelir; gövde okunan sayfalardan
alıntı yapılır. Gövde okunmayan sayfalar yalnız başlık kanıtı taşır ve içerik
iddiaları `⚠️ VERIFICATION REQUIRED` işaretlenir. Repo kanıtı gerçek dosya ve
doğrulanmış satır numarasıyla verilir (`Get-Content | Select-Object -Index` ile
2026-10-12 doğrulandı).

## §0 Kanıt düzeyi özeti

| Kaynak | HTTP | Gövde | Kullanım |
|---|---|---|---|
| NDK ABI kılavuzu | 200 | OKUNDU | §2 §3 |
| R8 kod küçültme sayfası | 200 | OKUNDU | §6 §7 |
| APK Split (ABI) sayfası | 200 | OKUNDU | §5 |
| Multidex sayfası | 200 | OKUNDU | §8 |
| Play imzalama sayfası | 200 | OKUNDU | §10 §11 |
| 64-bit mimari sayfası | 200 | OKUNDU | §4 |
| Build varyantları sayfası | 200 | OKUNDU | §9 |
| Bağımlılıklar sayfası | 200 | OKUNDU (gövde 2026-10-12) | §12.1 |
| Hedef API gereksinimi sayfası | 200 | OKUNDU (gövde 2026-10-12) | §13 |
| Gradle ipuçları sayfası | 200 | yönlendirme (gövde = "Meet Android Studio") | §12 · `⚠️` |
| `ndk-build` sayfası | 200 | yönlendirme (gövde = "Meet Android Studio") | §12 · `⚠️` |
| Studio hata ayıklama sayfası | 200 | başlık | §12 · `⚠️` |
| Gradle eklentisi sürüm sayfası | 200 | başlık | §12 · `⚠️` |

## §1 Kapsam ve sınır

| Kapsam İÇİ | Kapsam DIŞI |
|---|---|
| ABI matrisi + yerel kütüphane klasörleri | Ses API'leri → `android-audio-mimarisi.md` |
| APK Split / App Bundle / fat APK | Gecikme/underrun → `android-gercek-zamanli-dinleme.md` |
| R8/ProGuard küçültme ve keep kuralları | HAL/sürücü → `android-hal-ve-surucu.md` |
| Multidex + 64K referans sınırı | Cihaz/route → `android-medya-cihazi-yonetimi.md` |
| Build tipi/ürün çeşidi kurgusu | İzin/living cycle → `android-izin-ve-lifecycle.md` |
| Play imzalama ve 64-bit zorunluluğu | Uygulama mağaza politika metni tamamı → `⚠️` |

## §2 ABI matrisi (NDK kılavuzu gövdesi)

Kaynak: NDK ABI kılavuzu gövdesi (`Kaynak: [RESMİ ALINTI · https://developer.android.com/ndk/guides/abis · 2026-10-12]`).

| ABI | Desteklenen komut seti | Gövde notu |
|---|---|---|
| `armeabi-v7a` | `armeabi` · Thumb-2 · Neon | "Incompatible with ARMv5/v6 devices." |
| `arm64-v8a` | AArch64 | "Armv8.0 only." |
| `x86` | x86 (IA-32) · MMX · SSE/2/3 · SSSE3 | "No support for MOVBE or SSE4." |
| `x86_64` | x86-64 · MMX · SSE/2/3 · SSSE3 · SSE4.1, 4.2 · POPCNT · CMPXCHG16B · LAHF-SAHF | "Full x86-64-v2." |

### §2.1 Kaldırılmış ABI'lar (gövde notu)

| ABI | Gövde ifadesi |
|---|---|
| `armeabi` (ARMv5) | "Historically the NDK supported ARMv5 (armeabi)" — kaldırıldı |
| MIPS (32/64) | "32-bit and 64-bit MIPS, but support for these ABIs was removed in NDK r17" |

**RED TEAM bulgu 2-1:** "NDK hâlâ MIPS/ARMv5 destekler" iddiası **REDDEDİLDİ**;
gövde kaldırılma sürümünü (NDK r17) açıkça yazar.

## §3 Fat APK ve boyut ödülü

| Kavram | Gövde ifadesi |
|---|---|
| Fat APK | "include the binaries for each ABI in a single APK, also known as a fat APK" |
| Bedeli | "significantly larger than one containing only the binaries for a single ABI" |
| Kazancı | "gaining wider compatibility, but at the expense of a larger APK" |
| Tavsiye | gövde "It is strongly recommended that you take advantage of either App Bundles or APK Splits" der; cümlenin devamı indirmede kesildi → tam metin `⚠️ VERIFICATION REQUIRED` |

## §4 64-bit mimari zorunluluğu (Play kılavuzu gövdesi)

Kaynak: 64-bit sayfası gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sayfa başlığı | "Support 64-bit architectures" |
| Klasör eşlemesi | "The 64-bit equivalent is arm64-v8a." · "For the x86 architecture, look for x86 for 32-bit and x86_64 for 64-bit." |
| Tablo | "Platform · 32-bit libraries folder · 64-bit libraries folder · ARM: `lib/armeabi-v7a` · `lib/arm64-v8a` · x86: `lib/x86` · `lib/x86_64`" |
| Kontrol | "Ensure that you have native libraries in both of these folders." |
| Yerel kod tanımı | "Your app makes use of native code if it: Uses any C/C++ (native) code in your app." |
| Uyarı akışı | draft release'lerde "64-bit requirement" ile ilgili uyarı görünür |

### §4.1 Klasör ⇄ ABI eşlemesi (gövdeden)

| Mimari | 32-bit klasör | 64-bit klasör |
|---|---|---|
| ARM | `lib/armeabi-v7a` | `lib/arm64-v8a` |
| x86 | `lib/x86` | `lib/x86_64` |

## §5 APK Split — ABI'ye göre bölme

Kaynak: APK Split sayfası gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sorun | "your APK includes files supporting multiple Application Binary Interfaces (ABIs)" |
| Çözüm | "create multiple APKs that contain files for specific ABIs" |
| Gradle rolü | "Gradle can create separate APKs that contain only code and resources specific to each ABI." |
| Evrensel APK | `universalApk` (Groovy) / `isUniversalApk` (Kotlin) — "If true, Gradle generates a universal APK in addition to per-ABI APKs." |
| Varsayılan | "The default value is false." |
| ABI listesi | `reset()` ile "specify an exact list of ABIs" |

### §5.1 Dağıtım biçimi karşılaştırması (gövdeye dayalı)

| Biçim | İçerik | Boyut | Kanıt |
|---|---|---|---|
| Fat APK | tüm ABI'lar tek APK | büyük (§3) | A — gövde |
| ABI Split APK'ları | her ABI kendi APK'sı | küçük | A — gövde |
| Universal APK | split'lere ek tüm-ABI APK | büyük (istisnai) | A — gövde |
| App Bundle (`.aab`) | Play tarafında ABI'ya göre servis | en küçük (türetme) | A — gövde (öneri cümlesi) |

## §6 R8 — kod küçültme (ProGuard halefi)

Kaynak: R8 sayfası gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sayfa başlığı | "Enable app optimization with R8" |
| Varsayılan kurallar | "built-in keep rules for the Android platform, equivalent to `proguard-android-optimize.txt`" |
| Kotlin DSL | `optimization { enable = true }` — "Enables code and resource optimizations." |
| Eski DSL (Kotlin) | `isMinifyEnabled = true` (kod) · `isShrinkResources = true` (kaynak) |
| Tavsiye | "We recommend that you always enable both settings." |
| Eşik | "API versions lower than 9.3" ayrımı gövdede geçer (tam bağlam `⚠️ VERIFICATION REQUIRED`) |

### §6.1 Küçültme ile ilgili iki anahtar (gövdeden)

| Anahtar | Ne yapar | Kanıt |
|---|---|---|
| `isMinifyEnabled` | kod optimizasyonu | gövde |
| `isShrinkResources` | kaynak optimizasyonu | gövde |

## §7 Keep kuralları — ne neden korunur (kanıt sınırı)

| Soru | Durum |
|---|---|
| Varsayılan keep kuralları platform tarafından gelir | gövde (§6) |
| "proguard-android-optimize.txt" karşılığı gövdede | EVET |
| Reflectif erişimli sınıflar için keep kuralı yazımı | `⚠️ VERIFICATION REQUIRED` (gövdede tam kural listesi okunmadı) |
| Ses/JNI arayüzleri için özel keep kuralı | `UNKNOWN` (bu turda yok) |

## §8 Multidex — 64K referans sınırı

Kaynak: Multidex sayfası gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sayfa başlığı | "Enable multidex for apps with over 64K methods" |
| Ön tavsiye | "take steps to reduce the total number of references called by your app code, including methods defined by your app code or included libraries" |
| Strateji | "Review your app's direct and transitive dependencies" |
| Amaç | "avoid hitting the DEX reference limit" |

### §8.1 Multidex karar sırası (gövde cümlesinden türetme)

| Sıra | Adım | Kanıt |
|---|---|---|
| 1 | Bağımlılıkları gözden geçir (doğrudan + geçirgen) | gövde |
| 2 | Referans sayısını azalt | gövde |
| 3 | Hâlâ sınır üstüyse multidex'i etkinleştir | sayfa başlığı |
| 4 | Sonuç doğrulaması | `⚠️ VERIFICATION REQUIRED` (deterministik ölçüm adımı okunmadı) |

## §9 Build varyantları — tip × çeşit × kaynak seti

Kaynak: Build varyantları sayfası gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Tanım | varyantlar "settings, code, and resources configured in your build types and product flavors" |
| Vurgu | "Although you don't configure build variants directly, you do configure the build types and product flavors that form them." |
| Örnek | gövde "a 'demo' product flavor might specify certain features and device requirements" der; devamı indirmede kesildi → `⚠️ VERIFICATION REQUIRED` |
| Kaynak seti | "in the 'demo' product flavor, 'debug' build type, and main/ source set" |
| Varsayılan tipler | "Android Studio automatically creates the debug and release build types" |
| Yapılandırma yeri | "inside the `android` block of the module-level `build.gradle.kts` file" |

### §9.1 Varyant bileşenleri

| Bileşen | Rol | Kanıt |
|---|---|---|
| Build tipi | `debug` / `release` | gövde |
| Ürün çeşidi | `demo` örneği (özel kod/kaynak/gereksinim) | gövde |
| Kaynak seti | `main/` + varyant/tip üstünde birleşim | gövde |
| Varyant | bileşenlerin bileşimi; doğrudan yapılandırılmaz | gövde |

## §10 Play imzalama — üç anahtar kavramı

Kaynak: İmzalama sayfası gövdesi.

| Kavram | Gövde ifadesi |
|---|---|
| Üretim adımı | "Generate an upload key and keystore" |
| İmzalama adımı | "Sign your app with your upload key" |
| Servis adımı | "Configure Play App Signing" |
| Dağıtım adımı | "Upload your app to Google Play" |
| Bundle akışı | "you need to sign your app bundle with an upload key before uploading it to the Play Console, and Play App Signing takes care of the rest" |
| APK akışı | "For apps distributing using APKs […] you must manually sign your APKs for upload" (aradaki metin gövdede kesik) |
| Anahtar sahipliği | mevcut "app signing key" ile devam veya "Google generate" seçeneği |

### §10.1 Üç anahtar ayrımı

| Anahtar | Sahip | Rol | Kanıt |
|---|---|---|---|
| Upload key | geliştirici | Play'e yükleme imzası | gövde |
| App signing key | Play App Signing | mağaza dağıtım imzası | gövde |
| Upload sertifikası | geliştirici | gövde "register an upload certificate" der; cümle indirmede kesildi | kısmi → `⚠️` |

**RED TEAM bulgu 10-1:** "Tek imzalama anahtarı vardır" iddiası **REDDEDİLDİ**;
kılavuz upload key ile app signing key'i ayrı adımlar olarak ayırır (§10).

## §11 İmzalama akış karar ağacı

| Durum | Soru | Yol | Kanıt |
|---|---|---|---|
| 1 | Dağıtım biçimi nedir? | — | — |
| 2 | App Bundle | upload key ile imzala → Play App Signing | gövde |
| 3 | APK (Play dışı/mağaza dışı) | APK'yı manuel imzala | gövde |
| 4 | Yeni uygulama | anahtarı Google'a mı ürettiriyorsun? | gövde |
| 5 | Mevcut uygulama | mevcut app signing key ile devam | gövde |
| 6 | Anahtar kaybı senaryosu | — | `⚠️ VERIFICATION REQUIRED` (sayfa okunmadı) |

## §12 Başlık-kanıtlı sayfalar (gövde bu turda doğrulanmadı)

| Sayfa | Başlık | Gövde durumu | Durum |
|---|---|---|---|
| Gradle ipuçları | "Gradle tips" | istek gövdesi "Meet Android Studio" döndürdü (yönlendirme) | `⚠️ VERIFICATION REQUIRED` |
| Bağımlılıklar | "Add build dependencies" | **GÖVDE OKUNDU** → §12.1 | §12.1 |
| `ndk-build` | `ndk-build` sayfası | istek gövdesi "Meet Android Studio" döndürdü (yönlendirme) | `⚠️ VERIFICATION REQUIRED` |
| Studio hata ayıklama | debug sayfası | gövde indirilmedi | `⚠️ VERIFICATION REQUIRED` |
| Gradle eklentisi sürümleri | AGP sürüm sayfası | gövde indirilmedi | `⚠️ VERIFICATION REQUIRED` |

**Yasak:** AGP sürüm numarası, minimum Gradle sürümü, `minSdk` sayısal
değeri bu sayfalardan BU DOSYADA ÜRETİLEMEZ (gövde doğrulanmadı → sayı uydurmak yasak).
`targetSdk` eşiği ise AYRI bir sayfanın gövdesinden okunmuş olup §13'te verilmiştir.

## §12.1 Bağımlılık yapılandırmaları — "Add build dependencies" gövdesi (2026-10-12 OKUNDU)

Kaynak: `https://developer.android.com/studio/build/dependencies` gövdesi
(`Kaynak: [RESMİ ALINTI · 2026-10-12]`).

| Kural | Gövde ifadesi |
|---|---|
| Bağımlılık doğrulama | "consider enabling Dependency verification to help ensure the dependencies you download and include in your project are what you expect" |
| Dinamik sürüm yasağı | "you shouldn't use dynamic version numbers, such as 'com.android.tools.build:gradle:3+'" |
| Dinamik sürüm bedeli | "unexpected version updates, difficulty resolving version differences, and poor performance" |
| Bağımlılık kaynağı | "The dependencies can be located on your machine or in a remote repository, and any transitive dependencies they declare are automatically included as well" |
| AGP'ye özgü sayfa | "your Android project must use only the dependency configurations defined on this page" |
| Birincil yöntem | "The best way to add and manage build dependencies is to use version catalogs, the method new projects use by default" |
| Katalog dosyası | `libs.versions.toml` ("under the gradle directory in Project view") |
| BOM desteği | "Some libraries are available in a published Bill of Materials (BOM) that groups families of libraries and their versions" |

### §12.1.1 Yapılandırma davranışları (gövde tablosu)

| Yapılandırma | Gövde davranışı |
|---|---|
| `implementation` | "adds the dependency to the compile classpath and packages the dependency to the build output" · "Most app and test modules should use this configuration." |
| `api` | "wants to transitively export that dependency to other modules" · "Having a large number of api dependencies can significantly increase build time." |
| `compileOnly` | "adds the dependency to the compile classpath only (that is, it's not added to the build output)" |
| `compileOnly` sınırı | "You can't use the compileOnly configuration with Android Archive (AAR) dependencies." |
| `runtimeOnly` | "adds the dependency to the build output only, for use during runtime" · "This is rarely used on Android" |
| `ksp` / `kapt` / `annotationProcessor` | "supply libraries that process annotations and other symbols in your code before it is compiled" |
| İşlemci yasağı | sınıfpath'te işlemci bulunursa "it produces a build error" (imza: `META-INF/services/javax.annotation.processing.Processor`) |
| `lintChecks` | "include a library containing lint checks you want Gradle to execute when building your Android app project" |
| `lintPublish` | "include lint checks you want Gradle to compile into a lint.jar file and package in your AAR" |

### §12.1.2 Varyant ve sıralama kuralları (gövde)

| Kural | Gövde ifadesi |
|---|---|
| Varyanta özgü bağımlılık | yapılandırma adı büyük harf + varyant öneki (ör. `freeImplementation`) |
| Çeşit × tip birleşimi | "you must initialize the configuration name" (ör. `freeDebugImplementation by configurations.creating`) |
| Test yolları | `testImplementation` yerel testler · `androidTestImplementation` enstrümante test APK'si |
| Obsolete uyarısı | "Configuration 'androidTestApi' is obsolete and has been replaced with 'androidTestImplementation'." |
| Öncelik sırası | "The order in which you list your dependencies indicates the priority for each" |
| Birleştirme senaryosu | LIB_A → LIB_C + LIB_D · LIB_B → LIB_C zinciri "flat dependency" grafiğine çözülür (gövde örneği) |
| Sürüm kataloğu örneği | gövde `[versions]` / `[libraries]` / `[plugins]` blokları verir; `agp = "8.3.0"` gövdedeki ÖRNEK değerdir, bu dosyanın hedef sürümü DEĞİLDİR |

## §13 Hedef API gereksinimi — gövde kanıtı (2026-10-12 OKUNDU)

Kaynak: `https://developer.android.com/google/play/requirements/target-sdk` gövdesi
(`Kaynak: [RESMİ ALINTI · 2026-10-12]`). Başlık: "Meet Google Play's target API level requirement".

| Öğe | Gövde ifadesi |
|---|---|
| Sayfa | "Meet Google Play's target API level requirement" · HTTP 200 |
| Giriş | "When you upload an APK, it must meet Google Play's target API level requirements." |
| Eşik (yeni uygulama/güncelleme) | "Starting August 31 2026: New apps and app updates must target Android 16 (API level 36) or higher" |
| Mevcut uygulamalar | "Existing apps must target Android 15 (API level 35) or higher to remain available to new users" |
| Uzatma | "you'll be able to request an extension to November 1, 2026" |
| İstisna | "Permanently private apps that are restricted to users in a specific organization and intended for internal distribution only." |

### §13.1 Form faktörüne göre eşik matrisi (gövdeden)

| Form faktörü | Yeni/güncelleme | Mevcut uygulama | Kanıt |
|---|---|---|---|
| Telefon / tablet / katlanır (varsayılan) | Android 16 · API 36 | Android 15 · API 35 | gövde |
| Wear OS | Android 15 · API 35 | Android 15 · API 35 | gövde |
| Android Automotive OS | Android 15 · API 35 | Android 15 · API 35 (API 31 altı özel madde ayrı) | gövde |
| Android TV | Android 14 · API 34 | Android 15 · API 35 | gövde |
| Android XR | Android 14 · API 34 | Android 15 · API 35 | gövde |

### §13.2 Düşük targetSdk'nın görünürlük bedeli (gövdeden)

| targetSdk | Sonuç |
|---|---|
| Android 14 (API 34) ve altı | yalnızca "devices running Android OS that are the same or lower than your app's target API level" cihazlarda görünür |
| Android 13 (API 33) ve altı — Wear/TV/XR | aynı görünürlük kısıtı (API 34 altı kuralı) |
| Android 12 (API 31) ve altı — Automotive | aynı görünürlük kısıtı |
| Android 5.1 (API 22) ve altı hedefleyen | "as of Android 10 (API level 29), users see a warning when they start an app for the first time" |

### §13.3 Neden yeni SDK hedeflemeli (gövdeden)

| Gerekçe | Gövde ifadesi |
|---|---|
| Güvenlik/performans | "Every new Android version introduces changes that bring security and performance improvements" |
| Açık beyan zorunlu | değişiklikler "only apply to apps that explicitly declare support through their targetSdkVersion manifest attribute" |
| Bildirim yeri | Gradle dosyasındaki `targetSdkVersion` veya manifest'teki `android:targetSdkVersion` ("as described in the documentation for the `<uses-sdk>` manifest element") |

### §13.4 Android 11 → 12 geçiş maddeleri (gövde listesi, müzik uygulaması etkisi işaretli)

| Alan | Gövde maddesi | Ses uygulaması etkisi |
|---|---|---|
| Bluetooth | BLUETOOTH/BLUETOOTH_ADMIN → BLUETOOTH_SCAN / ADVERTISE / CONNECT | BLE MIDI cihaz keşfi için geçerli |
| Konum | "You must request the ACCESS_COARSE_LOCATION permission any time you request ACCESS_FINE_LOCATION" | rota/coğrafi etiket için |
| Intent filtresi | `android:exported` açıkça bildirilmeli | medya butonu/receiver yolları için |
| Hibernation | "runtime permissions and cache are reset, and you can't run jobs or alerts" | arka plan senkronu için |
| PendingIntent | "You must specify the mutability of each PendingIntent object" | medya bildirim akışı için |
| Ön plan servisi | "your app can't start foreground services while it runs in the background" | oynatma servisi için kritik |
| Test listesi | Doze · izin reddi · Alarms/Jobs · paylaşım senaryoları gövdede sıralanır | doğrulama planına alınır |

### §13.5 Kanıt durumu özeti

| Soru | Durum |
|---|---|
| Yeni uygulama targetSdk eşiği | API 36 (2026-08-31'den itibaren) — gövde |
| Uzatma tarihi | 2026-11-01 — gövde |
| `minSdk` sayısal değeri | `UNKNOWN` (bu sayfada yok · §12 yasağı) |
| Uygulama geçiş son tarihi (bizim ürün) | `UNKNOWN` (karar verilmedi) |

## §14 Red-team — bu dosyada reddedilen iddialar

| # | Reddedilen iddia | Gerekçe |
|---|---|---|
| 1 | "NDK MIPS/ARMv5'i hâlâ destekler" | NDK r17'de kaldırıldı (§2.1) |
| 2 | "Tek APK her zaman en iyidir" | fat APK boyut bedeli belgeli (§3) |
| 3 | "Split gerekli değildir" | kılavuz güçlü tavsiye verir (§3) |
| 4 | "64-bit klasörü aynı isimdir" | `lib/arm64-v8a` ayrı klasördür (§4.1) |
| 5 | "Varsayılan universal APK'dır" | `false` (§5) |
| 6 | "ProGuard hâlâ birincil araçtur" | sayfa R8'i etkinleştirmeyi anlatır (§6) |
| 7 | "Küçültme kaynakları etkilemez" | `isShrinkResources` ayrı anahtardır (§6.1) |
| 8 | "Multidex ilk çözümdür" | önce referans azaltma tavsiye edilir (§8.1) |
| 9 | "Tek imzalama anahtarı vardır" | upload vs app signing ayrımı (§10) |
| 10 | "APK Play'de otomatik imzalanır" | APK dağıtımında manuel imzalama gerekir (§10) |
| 11 | "AGP/Gradle sürümleri bu dosyada yazılır" | gövde doğrulanmadı → `⚠️` (§12) |
| 12 | "Tek bir targetSdk eşiği her form faktörü için geçerlidir" | form faktörüne göre ayrım var (§13.1) |
| 13 | "targetSdk eşiği bu turda bilinmiyor" | gövdeden okundu: API 36 / 2026-08-31 (§13) |
| 14 | "dependencies sayfası yalnız başlık kanıtıdır" | gövde okundu → §12.1 |
| 15 | "gradle-tips / ndk-build gövdesi alındı" | gelen gövde "Meet Android Studio" (yönlendirme) → `⚠️` (§12) |

## §15 Doğrulama matrisi

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| ABI matrisi + NDK r17 kaldırma notu | https://developer.android.com/ndk/guides/abis | 2026-10-12 | HTTP 200 · gövde alındı |
| Fat APK + App Bundle/Split tavsiyesi | https://developer.android.com/ndk/guides/abis | 2026-10-12 | Gövde alıntısı |
| 64-bit klasör eşlemesi | https://developer.android.com/google/play/requirements/64-bit | 2026-10-12 | HTTP 200 · gövde alındı |
| ABI Split + universalApk | https://developer.android.com/studio/build/configure-apk-splits | 2026-10-12 | HTTP 200 · gövde alındı |
| R8/keep varsayılanları + minify anahtarları | https://developer.android.com/studio/build/shrink-code | 2026-10-12 | HTTP 200 · gövde alındı |
| Multidex + 64K + bağımlılık azaltma | https://developer.android.com/build/multidex | 2026-10-12 | HTTP 200 · gövde alındı |
| Varyant = tip × çeşit × kaynak seti | https://developer.android.com/studio/build/build-variants | 2026-10-12 | HTTP 200 · gövde alındı |
| Üç anahtar kavramı + Play App Signing | https://developer.android.com/studio/publish/app-signing | 2026-10-12 | HTTP 200 · gövde alındı |
| Bağımlılık yapılandırmaları + sürüm kataloğu | https://developer.android.com/studio/build/dependencies | 2026-10-12 | HTTP 200 · **gövde alındı** (§12.1) |
| Hedef API eşikleri + uzatma + form faktörü matrisi | https://developer.android.com/google/play/requirements/target-sdk | 2026-10-12 | HTTP 200 · **gövde alındı** (§13) |
| Gradle ipuçları sayfası var | https://developer.android.com/studio/build/gradle-tips | 2026-10-12 | HTTP 200 · başlık kanıtı · yönlendirme gövdesi · `⚠️` |
| `ndk-build` sayfası var | https://developer.android.com/studio/command-line/ndk-build | 2026-10-12 | HTTP 200 · başlık kanıtı · yönlendirme gövdesi · `⚠️` |
| Studio hata ayıklama sayfası var | https://developer.android.com/studio/debug | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️` |
| AGP sürüm sayfası var | https://developer.android.com/studio/releases/gradle-plugin | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️` |

## §15.1 Depo kanıtı — bu depoda Android build YOKTUR

Ölçüm: 2026-10-12 · `Get-Content | Select-Object -Index` ile satır doğrulandı.

| # | Kanıt | Gerçek yol / satır | Doğrulama |
|---|---|---|---|
| 1 | CI workflow varlığı | `Dosya: .github/workflows/ci.yml / Satır: 8` → `name: CI Pipeline` | doğrulandı |
| 2 | CI çalıştırıcı ortamı | `Dosya: .github/workflows/ci.yml / Satır: 27` → `runs-on: ubuntu-latest` | doğrulandı |
| 3 | Dosya uzunluğu | `.github/workflows/ci.yml` = 203 satır | doğrulandı |
| 4 | `android`/`gradle`/`ndk` araması | `Select-String` → **0 isabet** | doğrulandı |
| 5 | Repo `.cpp/.h` sayısı | 0 | doğrulandı |

**Sonuç:** Bu depoda Android/Gradle/NDK geçidi **yoktur**; bu dosyadaki tüm build
iddiaları **resmi doküman** kanıtlıdır, depo kanıtı DEĞİL.

## §16 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| B-1 | AGP/Gradle/NDK sürümleri (bu tur) | `⚠️ VERIFICATION REQUIRED` |
| B-2 | `minSdk` sayısal değeri | `UNKNOWN` (targetSdk eşiği §13'te gövdeden OKUNDU) |
| B-3 | Hedef API son geçiş tarihi | ÇÖZÜLDÜ → 2026-08-31 · uzatma 2026-11-01 (§13) |
| B-4 | Keep kuralları tam listesi | `⚠️ VERIFICATION REQUIRED` |
| B-5 | `ndk-build` vs CMake tercihi | `⚠️ VERIFICATION REQUIRED` (gövde yönlendirme döndürdü) |
| B-6 | Studio hata ayıklama adımları | `⚠️ VERIFICATION REQUIRED` |
| B-7 | İmzalama anahtarı kaybı prosedürü | `⚠️ VERIFICATION REQUIRED` |
| B-8 | Depoda gerçek Android build hedefi | YOK (§15.1) → PLANNED |

## §17 Kanıt toplama kaydı

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | 13 URL doğrulama | 13/13 HTTP 200 |
| 2 | 7 sayfa için gövde indirme + temizleme | düz metin |
| 3 | Anahtar kelime ile alıntı çıkarma | §2–§11 tabloları |
| 4 | `.ai/architecture/**` URL kesişim denetimi | 0 (tümü yeni) |
| 5 | Repo ölçümü (`.cpp/.h`, CI, arama) | §15.1 |
| 6 | Kullanılan UA | tarayıcı User-Agent |
| 7 | `dependencies` + `target-sdk` gövdesi ikinci okuma | §12.1 · §13 (2026-10-12) |
| 8 | `gradle-tips` / `ndk-build` indirme denetimi | gelen gövde "Meet Android Studio" → başlık-kanıtına düşürüldü (§12) |

## §18 Dosyaya tahsis edilen URL listesi (tekil)

| # | URL | Bölüm |
|---|---|---|
| 1 | https://developer.android.com/ndk/guides/abis | §2 §3 |
| 2 | https://developer.android.com/google/play/requirements/64-bit | §4 |
| 3 | https://developer.android.com/studio/build/configure-apk-splits | §5 |
| 4 | https://developer.android.com/studio/build/shrink-code | §6 §7 |
| 5 | https://developer.android.com/build/multidex | §8 |
| 6 | https://developer.android.com/studio/build/build-variants | §9 |
| 7 | https://developer.android.com/studio/publish/app-signing | §10 §11 |
| 8 | https://developer.android.com/studio/build/gradle-tips | §12 |
| 9 | https://developer.android.com/studio/build/dependencies | §12 §12.1 |
| 10 | https://developer.android.com/studio/command-line/ndk-build | §12 |
| 11 | https://developer.android.com/google/play/requirements/target-sdk | §13 |
| 12 | https://developer.android.com/studio/debug | §12 |
| 13 | https://developer.android.com/studio/releases/gradle-plugin | §12 |

Bu 13 URL bu dosyaya ÖZELDİR; `android/` klasörünün diğer dosyalarında tekrarlanmaz.

## §19 Komşu dosya ilişkileri (URL kesişimi olmadan)

| Komşu | Konu ayrımı | Temas noktası |
|---|---|---|
| `android-audio-mimarisi.md` | çerçeve + terminoloji | — |
| `android-aaudio-ve-opensl.md` | NDK ses yolu (AAudio/OpenSL) | NDK varlığı bu dosyada yalnız ABI/`ndk-build` başlıkları |
| `android-hal-ve-surucu.md` | HAL + politika | — |
| `android-medya-cihazi-yonetimi.md` | cihaz/route/session | — |
| `android-gercek-zamanli-dinleme.md` | gecikme/buffer | test uygulaması dağıtımı bu dosyada |
| `android-izin-ve-lifecycle.md` | izin + yaşam döngüsü | manifest konuları orada |
| `android-kod-ornekleri.md` | referans kod örnekleri | Gradle betiği örneği orada DEĞİL |
| `android-web-arastirma.md` | kaynak taraması | bu dosyanın 13 URL'si bu dosyada |

## §20 Sık yapılan hatalar (RED TEAM eğitimi)

| # | Hata | Doğru davranış |
|---|---|---|
| 1 | MIPS/ARMv5'i hâlâ paketlemek | NDK r17'den beri kaldırıldı (§2.1) |
| 2 | Fat APK ile 64-bit zorunluluğunu unutmak | iki klasör de dolu olmalı (§4.1) |
| 3 | `universalApk`'ı varsayılan sanmak | `false` (§5) |
| 4 | R8'yi yalnız silme sanmak | kod + kaynak iki ayrı anahtar (§6.1) |
| 5 | Multidex'i ilk çare sanmak | önce referans azaltma (§8.1) |
| 6 | Upload key'i app signing key sanmak | iki anahtar (§10.1) |
| 7 | APK'yı otomatik imzalı sanmak | manuel imzalama gerekir (§10) |
| 8 | Gövde doğrulanmayan sayfaya sürüm/eşik yazmak | §12'de yasak; yalnız §13 gövde eşiği yazılır |
| 9 | "Depoda Android build var" demek | 0 isabet (§15.1) |
| 10 | `Dosya:/Satır:` uydurmak | yalnız doğrulanmış 2 satır (§15.1) |

## §21 CoreMusic / Neva Engine etkisi

| Etki | Ayrıntı | Durum |
|---|---|---|
| Yerel ses motoru dağıtımı | `lib/arm64-v8a` + `lib/armeabi-v7a` klasörleri zorunlu (§4.1) | PLANNED |
| Boyut disiplini | Split/App Bundle ile ABI ayrımı (§3 §5) | PLANNED |
| Kod gizleme/küçültme | R8 + keep kuralları (§6 §7) | PLANNED · keep listesi `⚠️` |
| CI | depoda Android job'u YOK (§15.1) | PLANNED |
| İmzalama | Play App Signing akışı (§10) | PLANNED |
| Dağıtım mağazası | — | `UNKNOWN` (karar yok) |

## §22 Kapanış ölçümü

| Metrik | Değer |
|---|---|
| Dosya | `android-build-ve-dagilim.md` |
| Toplam satır | 513 (≥500 ✓) |
| Gövde okunan sayfa | 9 (§12.1 dependencies + §13 target-sdk dahil) |
| Başlık-only / yönlendirme sayfa | 4 (gradle-tips · ndk-build · studio/debug · gradle-plugin) |
| Tekil URL | 13 (§18) |
| `## §N` bölüm | 25 (§0–§22 + §12.1 + §15.1) |
| `### §N` alt bölüm | 14 |
| Tablo sayısı | 39 |
| ORİJİNAL ÖRNEK | 0 (yalnız gövde alıntısı; bağımsız Gradle betiği yazılmadı) |
| `⚠️ VERIFICATION REQUIRED` | 17 |
| `...` (dolgu) | 0 |
| Açık soru | 8 (§16 · B-3 ÇÖZÜLDÜ) |
| Doğrulanmış `Dosya:/Satır:` | 2 (§15.1) |
| `git commit` | ATILMADI |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode