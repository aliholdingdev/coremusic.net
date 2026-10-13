---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-surucu"
evidence:
  - "shared/composer.json:7"
  - "shared/composer.json:46"
  - "package.json:10"
  - ".github/workflows/ci.yml:44"
  - ".github/workflows/ci.yml:155"
  - "https://clang.llvm.org/docs/Toolchain.html"
  - "https://mesonbuild.com/Manual.html"
  - "https://gcc.gnu.org/onlinedocs/gcc/C-Dialect-Options.html"
validation: "2026-10-13 · 3 yeni URL HEAD 200 · envanter (2633) ile kesişim 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q6 — hedef paket formatları (MSI/deb/AppImage) seti UNKNOWN"
  - "Q-X49 — native derleme kalıbı (CMake+Ninja mı, Make mi) seçilmedi → UNKNOWN"
group: kod
---

# derleme-ve-paketleme-ornekler — Kademe-4 Örnekler

> **Kapsam:** Bu dosya, `-anayasa` ve `-surucu` bölümlerindeki kuralların
> **çalışan karşılıklarını** gösterir. Öncelik sırası: (1) bu depoda **bugün
> çalışan** yapılandırma, (2) PLANNED native derleme kalıbı, (3) yapılmaması
> gerekenler. **Kanıt dili:** S1 = birebir repo satırı · S4 = mimari yorum ·
> S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-ornekler` |
| parent_id | `…/isletim-sistemi-cross/derleme-ve-paketleme` |
| group | `kod` |
| Bağımlı | `derleme-ve-paketleme-anayasa.md` · `derleme-ve-paketleme-surucu.md` |
| Repo gerçeği | Bugün derlenen tek yüzey PHP (Composer) + JS (npm); C++ toolchain **PLANNED** |

> **Repo'da çekirdek/derleme seviyesinde C++ kodu YOK — kaynak: upstream docs.**
> `*.cpp`/`*.h` glob = 0 (L0 altyapısı PLANNED). Aşağıdaki §2.6-§2.9 blokları
> **PLANNED kalıptır**, depoda çalışmaz; §1-§2.5 ise birebir disk kanıtıdır.

## §1 Çalışan yapılandırmalar (repo · disk · 2026-10-13)

### §1.1 `shared/composer.json` — bağımlılık çözümleme yüzeyi

| # | Satır | Alan | Değer | Ne işe yarar |
|---|---|---|---|---|
| 1 | 2 | `name` | `coremusic/shared-infrastructure` | paket kimliği |
| 2 | 5 | `license` | `proprietary` | dağıtım kısıtı (lisans denetimi) |
| 3 | 6 | `version` | `2.0.0` | tek sürüm kaynağı adayı (⚠️ VERIFICATION REQUIRED) |
| 4 | 8 | `require.php` | `>=8.4` | dil sürüm eşiği |
| 5 | 9 | `require.ext-apcu` | `*` | eklenti zorunluluğu |
| 6 | 10 | `require.ext-json` | `*` | eklenti zorunluluğu |
| 7 | 11 | `require.ext-mbstring` | `*` | eklenti zorunluluğu |
| 8 | 12 | `require.ext-pdo` | `*` | eklenti zorunluluğu |
| 9 | 13 | `firebase/php-jwt` | `^7.2` | semver aralığı |
| 10 | 15 | `php-di/php-di` | `^7.0` | semver aralığı |
| 11 | 21 | `symfony/event-dispatcher` | `^7.0` | semver aralığı |
| 12 | 24 | `require-dev.phpunit` | `^10.5` | test çatısı |
| 13 | 25 | `require-dev.phpstan` | `^1.10` | statik analiz |
| 14 | 29 | `autoload.psr-4` | `CoreMusic\` → `src/` | sınıf haritası |
| 15 | 34 | `autoload-dev.psr-4` | `CoreMusic\Test\` → `tests/` | test sınıf haritası |
| 16 | 37 | `minimum-stability` | `stable` | pre-release engeli |
| 17 | 38 | `prefer-stable` | `true` | stabil sürüme eğilim |
| 18 | 43 | `config.sort-packages` | `true` | `require` sıralaması |
| 19 | 44 | `config.optimize-autoloader` | `true` | sınıf haritası önişleme |
| 20 | 47 | `scripts.test` | `phpunit` | test komutu |
| 21 | 48 | `scripts.stan` | `phpstan analyse src --level=5` | statik analiz komutu |

> **S1 kanıt yöntemi:** `Dosya: shared/composer.json / Satır: 44` →
> `Get-Content … | Select-Object -Index 43` ile birebir okunmuştur.

### §1.2 `package.json` (repo kökü) — npm yüzeyi

| # | Satır | Alan | Değer | Ne işe yarar |
|---|---|---|---|---|
| 1 | 2 | `name` | `coremusic.net` | paket kimliği |
| 2 | 3 | `private` | `true` | yanlışlıkla yayın engeli |
| 3 | 5 | `scripts.test:js` | `vitest run --root assets.coremusic.net` | birim test |
| 4 | 6 | `scripts.test:js:watch` | `vitest --root assets.coremusic.net` | izleme modu |
| 5 | 7 | `scripts.test:js:coverage` | `vitest run … --coverage` | kapsam ölçümü |
| 6 | 8 | `scripts.test:e2e` | `playwright test -c assets.coremusic.net/playwright.config.ts` | uçtan uca test |
| 7 | 11 | `@playwright/test` | `^1.63.0` | e2e çatısı |
| 8 | 12 | `@vitest/coverage-v8` | `^5.0.3` | kapsam sağlayıcı |
| 9 | 14 | `jsdom` | `^30.1.1` | DOM taklidi |
| 10 | 15 | `playwright` | `^1.63.0` | tarayıcı sürücüsü |
| 11 | 16 | `vitest` | `^5.0.3` | test çalıştırıcı |

### §1.3 `.github/workflows/ci.yml` — derleme/paketleme pipeline'ı

| # | Satır | Adım | Ne yapar | Etki alanı |
|---|---|---|---|---|
| 1 | 22 | `env.PHP_VERSION` | `8.4` tek sürüm kaynağı | tüm PHP job'ları |
| 2 | 27 | `runs-on` | `ubuntu-latest` | tek test platformu |
| 3 | 37 | `extensions` | `apcu, mbstring, json, pdo, pdo_mysql` | eklenti kurulumu |
| 4 | 44 | `key` | `${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` | platforma bağlı cache |
| 5 | 49 | `composer install` | `--no-progress --prefer-dist` | dağıtım indirme |
| 6 | 50 | `working-directory` | `shared` | monorepo alt dizin |
| 7 | 63 | `composer stan` | statik analiz | kalite kapısı |
| 8 | 73 | `strategy.matrix` | `include` ile 2 proje | test matrisi |
| 9 | 105 | `vendor/bin/phpunit` | test çalıştırma | doğrulama |
| 10 | 126 | `composer audit` | güvenlik denetimi | bağımlılık güvenliği |
| 11 | 151 | `node-version` | `26` | Node sürümü |
| 12 | 155 | `npm ci` | lock'dan temiz kurulum | JS bağımlılığı |
| 13 | 167 | `continue-on-error` | `true` (soft job) | kapsam gate'i yumuşak |
| 14 | 176 | `npm run test:js:coverage` | kapsam raporu | soft gate |
| 15 | 200 | `playwright install --with-deps chromium` | tarayıcı + sistem bağımlılığı | e2e ortamı |

### §1.4 Kilit (lock) dosyaları — sürüm tekrar üretilebilirliği

| # | Dosya | Üreten araç | Okuyan komut | Repo durumu |
|---|---|---|---|---|
| 1 | `composer.lock` (×3 alt alan) | `composer update` | `composer install` | diskte var — `ci.yml:44` `hashFiles` onu hedef alır |
| 2 | `package-lock.json` | `npm install` | `npm ci` | diskte var — `ci.yml:155` onu varsayar |
| 3 | `composer.lock` sürüm alanı | Composer | — | ⚠️ VERIFICATION REQUIRED (içeriği okunmadı) |
| 4 | `package-lock.json` sürüm alanı | npm | — | ⚠️ VERIFICATION REQUIRED (içeriği okunmadı) |

### §1.5 Sürüm ve eklenti eşikleri

| # | Eşik | Değer | Kaynak satırı | Durum |
|---|---|---|---|---|
| 1 | PHP | `>=8.4` | `shared/composer.json:8` | ✅ S1 |
| 2 | PHP (CI) | `8.4` | `.github/workflows/ci.yml:22` | ✅ S1 |
| 3 | Node (CI) | `26` | `.github/workflows/ci.yml:151` | ✅ S1 |
| 4 | PHPUnit | `^10.5` | `shared/composer.json:24` | ✅ S1 |
| 5 | PHPStan | `^1.10` | `shared/composer.json:25` | ✅ S1 |
| 6 | PHPStan analiz seviyesi | `5` | `shared/composer.json:48` | ✅ S1 |
| 7 | Vitest | `^5.0.3` | `package.json:16` | ✅ S1 |
| 8 | Playwright | `^1.63.0` | `package.json:15` | ✅ S1 |

## §2 Kod örnekleri

### §2.1 Composer kurulum adımı (çalışan — `ci.yml:48-50`)

| # | Parça | Değer | Yorum |
|---|---|---|---|
| 1 | komut | `composer install --no-progress --prefer-dist` | lock varsa sürümü değiştirmez |
| 2 | `--no-progress` | log temizliği | CI log satırlarını azaltır |
| 3 | `--prefer-dist` | kaynak yerine arşiv | indirme süresini düşürür |
| 4 | `working-directory` | `shared` | monorepo'da alt dizin kurulumu |
| 5 | benzer adım ×3 | `shared` · `auth.coremusic.net` · `home.coremusic.net` | 3 ayrı bağımlılık ağacı |

### §2.2 npm temiz kurulum adımı (çalışan — `ci.yml:154-155`)

| # | Parça | Değer | Yorum |
|---|---|---|---|
| 1 | komut | `npm ci` | `package-lock.json` olmadan **düşer** |
| 2 | ön koşul | `npm ci` öncesi `setup-node` + `cache: npm` | indirme önbelleği |
| 3 | cache anahtarı | `${{ runner.os }}` gömülü | platform değişince cache sıfırlanır |
| 4 | Node sürümü | `26` | `engines` ile uyum ⚠️ VERIFICATION REQUIRED |

### §2.3 Composer cache anahtarı (çalışan — `ci.yml:43-46`)

| # | Alan | Değer | Neden önemli |
|---|---|---|---|
| 1 | `path` | `~/.cache/composer` | Linux cache dizini |
| 2 | `key` | `${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` | lock değişince cache düşer |
| 3 | `restore-keys` | `${{ runner.os }}-composer-` | kısmi eşleşme ile eski cache |
| 4 | `runner.os` | `Linux` (CI) | **platforma bağlı** — Windows cache'i ayrı |

> §2.3 #4, `-tasinabilirlik-siniri` dalındaki "CI tek platformda koşuyor"
> bulgusunun birebir kanıtıdır.

### §2.4 Composer scripts (çalışan — `shared/composer.json:46-49`)

| # | Script | Komut | CI çağrısı |
|---|---|---|---|
| 1 | `test` | `phpunit` | `ci.yml:105` (doğrudan `vendor/bin/phpunit`) |
| 2 | `stan` | `phpstan analyse src --level=5` | `ci.yml:63` (`composer stan`) |

> **Fark (S4):** `test` scripti CI'da **çağrılmıyor** — CI doğrudan
> `vendor/bin/phpunit` çalıştırır (`ci.yml:105`). Bu, iki yolun **aynı işi
> yaptığı** anlamına gelir; sürüklenme riski `⚠️ VERIFICATION REQUIRED`.

### §2.5 Güvenlik denetimi adımı (çalışan — `ci.yml:123-127`)

| # | Adım | Komut | Kapsam |
|---|---|---|---|
| 1 | kurulum | `composer install --no-progress --prefer-dist` | tek paket ağacı |
| 2 | denetim | `composer audit` | bilinen CVE taraması |
| 3 | tekrar | ×3 (`shared` · `auth` · `home`) | üç ayrı bağımlılık kümesi |
| 4 | sonuç | CI job çıktısı | release gate'e bağlılığı UNKNOWN |

### §2.6 PLANNED — native derleme kalıbı (CMake + Ninja)

> Bu blok **depoda yoktur** (`*.cpp` = 0). Yalnız, `-anayasa` §5'teki toolchain
> seçiminin somut karşılığı olarak **kalıp** olarak yazılır; uygulanacak
> tarih/otorite `⚠️ VERIFICATION REQUIRED`.

| # | Öğe | Öneri | Dayanak |
|---|---|---|---|
| 1 | yapılandırıcı | CMake | https://mesonbuild.com/Manual.html (alternatif: Meson) |
| 2 | üretim | Ninja | `-anayasa` §5 araç zinciri matrisi |
| 3 | dil bayrakları | C++20 + `-Wall -Wextra` | https://gcc.gnu.org/onlinedocs/gcc/C-Dialect-Options.html |
| 4 | araç zinciri soyutlama | LLVM/GCC ortak bayrakları | https://clang.llvm.org/docs/Toolchain.html |
| 5 | MSVC karşılığı | `/W4` + `/permissive-` | https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options-listed-by-category |
| 6 | çıktı | tek `.so`/`.dll` | `-surucu` §13 |
| 7 | sürümlendirme | `SOVERSION` | `-surucu` §12 |

### §2.7 PLANNED — GNU Make kural kalıbı

| # | Öğe | Değer | Kaynak |
|---|---|---|---|
| 1 | örtük kural (implicit rule) | `%.o: %.c` | https://www.gnu.org/software/make/manual/html_node/Implicit-Rules.html |
| 2 | değişken | `CXXFLAGS` | aynı kaynak |
| 3 | bağımlılık otomatikleştirme | `-MMD -MP` | aynı kaynak (⚠️ ayrı HEAD doğrulaması yok) |
| 4 | paralel | `make -j$(nproc)` | aynı kaynak |

### §2.8 PLANNED — depo paketleme kalıbı (Linux)

| # | Format | Üretim | Doğrulama |
|---|---|---|---|
| 1 | `deb` | `debuild -us -uc` | `lintian` |
| 2 | `rpm` | `rpmbuild -ba` | `rpmlint` ⚠️ VERIFICATION REQUIRED (kaynak doğrulanmadı) |
| 3 | `tar.gz` | `git archive` | checksum (`sha256sum`) |
| 4 | `AppImage` | `appimagetool` | gpg imzası (`-surucu` §35) |

### §2.9 PLANNED — Windows paketleme kalıbı

| # | Format | Üretim | Doğrulama |
|---|---|---|---|
| 1 | MSI | WiX `candle` + `light` | `msiexec /i … /qn` sessiz kurulum |
| 2 | MSIX | `MakeAppx` | paket imzası |
| 3 | portable ZIP | `Compress-Archive` | sha256 |
| 4 | imza | `signtool sign /fd SHA256` | Authenticode zinciri |

### §2.10 Ölçüm örnekleri — neyi, nasıl sayarım

| # | Ölçüm | Komut/pseudoadım | Beklenen |
|---|---|---|---|
| 1 | bağımlılık sayısı | `composer show --direct \| wc -l` | ⚠️ VERIFICATION REQUIRED (sayı okunmadı) |
| 2 | bağımlılık sayısı (JS) | `npm ls --depth=0` | ⚠️ VERIFICATION REQUIRED |
| 3 | lock varlığı | 3 × `composer.lock` + `package-lock.json` | diskte mevcut |
| 4 | cache isabeti | CI logunda `cache hit` satırı | ⚠️ VERIFICATION REQUIRED (log yok) |
| 5 | audit sonucu | CI job `composer audit` çıktısı | ⚠️ VERIFICATION REQUIRED |

## §3 Anti-pattern (yapılmayacaklar)

| # | Anti-pattern | Sonuç | Doğrusu |
|---|---|---|---|
| 1 | `composer update` CI'da çalıştırma | sürüm sürüklenmesi | `composer install` (`ci.yml:49`) |
| 2 | `npm install` CI'da | lock değişir, tekrarsızlık kaybı | `npm ci` (`ci.yml:155`) |
| 3 | lock dosyasını commit etmemek | aynı sürüm iki yerde farklı | `composer.lock` + `package-lock.json` commit |
| 4 | `allow-plugins` alanını kaldırmak | Composer plugini sessizce devre dışı | `shared/composer.json:40-42` korunur |
| 5 | `minimum-stability: dev` | öngörülemeyen sürüm | `stable` (`shared/composer.json:37`) |
| 6 | PHP sürümünü iki yerde ayrı yazmak | CI ≠ prod | `composer.json:8` ↔ `ci.yml:22` senkron |
| 7 | `continue-on-error: true` kapatmak (şimdi) | kapsam gate'i kırmızıya döner, bloklar | `ci.yml:167` soft kalır — eşik büyüyünce kapatılır |
| 8 | `hashFiles` yolunu elle sabitlemek | lock yolu değişince cache ölü | `ci.yml:44` glob kalır |
| 9 | `optimize-autoloader: false` | sınıf haritası her istekte yeniden kurulur | `shared/composer.json:44` korunur |
| 10 | Vendor dizinini commit etmek | depo şişer, çakışma artar | `.gitignore` (⚠️ satır kanıtı okunmadı) |
| 11 | Tek `package.json` yerine alt-alan başına ayrı lock | sürümler ayrışır | bugünkü kök tek lock modeli korunur |
| 12 | `composer audit` sonucunu yok saymak | bilinen CVE yayılır | job var; gate bağlantısı UNKNOWN |

### §3.1 Anti-pattern ↔ repo kanıtı eşlemesi

| # | Anti-pattern | İlgili satır | Bu depoda durum |
|---|---|---|---|
| 1 | `composer update` yerine `install` | `ci.yml:49` | ✅ doğru |
| 2 | `npm install` yerine `npm ci` | `ci.yml:155` | ✅ doğru |
| 3 | lock commit'i | `composer.lock` ×3 · `package-lock.json` | ✅ diskte |
| 4 | `allow-plugins` kaldırma | `shared/composer.json:40-42` | ✅ korunuyor |
| 5 | `minimum-stability: dev` | `shared/composer.json:37` | ✅ `stable` |
| 6 | PHP sürüm ikiliği | `composer.json:8` ↔ `ci.yml:22` | ✅ tutarlı (8.4) |
| 7 | soft gate kalıcılaşması | `ci.yml:167` | ⚠️ bilinçli geçici |
| 8 | cache anahtarı sabitleme | `ci.yml:44` | ✅ glob korunuyor |
| 9 | autoloader önişlemesini kapatma | `shared/composer.json:44` | ✅ açık |
| 10 | vendor commit'i | `.gitignore` | ⚠️ VERIFICATION REQUIRED (satır okunmadı) |
| 11 | alt-alan başına ayrı JS lock | tek kök `package.json` | ✅ tek lock |
| 12 | audit görmezden gelme | `ci.yml:126` | ✅ job var; gate UNKNOWN |
| 13 | `tools: composer` sürümü sabitlememek | `ci.yml:36` | ⚠️ sürüm alanı yok |
| 14 | eklenti listesini CI'da elle tutmak | `ci.yml:37` ↔ `composer.json:9-12` | ⚠️ iki liste senkronu doğrulanmadı |

### §3.2 Eklenti senkron matrisi (composer.json ↔ ci.yml)

| # | Eklenti | `composer.json` | `ci.yml:37` | Eşleşme |
|---|---|---|---|---|
| 1 | `apcu` | satır 9 (`ext-apcu`) | var | ✅ |
| 2 | `json` | satır 10 (`ext-json`) | var | ✅ |
| 3 | `mbstring` | satır 11 (`ext-mbstring`) | var | ✅ |
| 4 | `pdo` | satır 12 (`ext-pdo`) | var | ✅ |
| 5 | `pdo_mysql` | composer'da yok | var | ⚠️ CI'da ek |
| 6 | `mbstring` (test job) | satır 11 | `ci.yml:88` | ✅ |

> Satır 5: `pdo_mysql` `composer.json` `require`'ünde **geçmiyor** — bu bilinçli
> bir fark mı, ihmal mi? `⚠️ VERIFICATION REQUIRED`.

## §4 Eşik / kabul kriteri

| # | Kriter | Eşik | Ölçüm | Durum |
|---|---|---|---|---|
| 1 | PHP sürüm tutarlılığı | `composer.json` = CI | iki satır karşılaştırma | ✅ 8.4 = 8.4 |
| 2 | Lock dosyası varlığı | 3 composer + 1 npm | disk glob | ✅ diskte |
| 3 | `npm ci` kullanımı | CI'da `npm install` yok | `ci.yml` taraması | ✅ `ci.yml:155` |
| 4 | Audit adımı | her PHP job'da | `ci.yml:126` | ✅ S1 |
| 5 | Statik analiz | PHPStan `--level=5` | `ci.yml:63` | ✅ S1 |
| 6 | Test platformu sayısı | 1 (`ubuntu-latest`) | `runs-on` sayımı | ✅ tek (Q4 açık) |
| 7 | Bağımlılık CVE sayısı | 0 açık | `composer audit` çıktısı | ⚠️ VERIFICATION REQUIRED |
| 8 | Native toolchain | mevcut değil | `*.cpp` glob = 0 | ✅ PLANNED teyitli |
| 9 | Paket formatı seçimi | tanımlı değil | — | ❌ `UNKNOWN` (Q6) |
| 10 | Tekrar üretilebilirlik | `SOURCE_DATE_EPOCH` uygulanmış | — | ⚠️ VERIFICATION REQUIRED |

### §4.1 Exit kodu → job sonucu eşlemesi

| # | Komut | Exit kodu | CI yorumu | Aksiyon |
|---|---|---|---|---|
| 1 | `composer install` | 0 | bağımlılıklar çözüldü | devam |
| 2 | `composer install` | 1 | lock/erişim hatası | job düşer |
| 3 | `composer audit` | 0 | bilinen CVE yok | devam |
| 4 | `composer audit` | 1 | CVE bulundu | job düşer (gate) |
| 5 | `vendor/bin/phpunit` | 0 | tüm testler geçti | devam |
| 6 | `vendor/bin/phpunit` | 1 | test hatası | job düşer |
| 7 | `composer stan` | 1 | statik analiz bulgusu | job düşer |
| 8 | `npm ci` | 0 | lock doğrulandı | devam |
| 9 | `npm ci` | 1 | lock uyuşmazlığı | job düşer |
| 10 | `npm run test:js` | 1 | birim test hatası | job düşer |
| 11 | `npm run test:js:coverage` | 1 | kapsam/önerme hatası | `continue-on-error` → job sarı |
| 12 | `npm run test:e2e` | 1 | e2e hatası | `continue-on-error` → job sarı |
| 13 | `npx playwright install` | 1 | ağ/tarayıcı hatası | job düşer |
| 14 | `git` (checkout) | 0 | kaynak alındı | devam |

### §4.2 Job bağımlılık matrisi (ci.yml)

| # | Job | `needs` | Bloklayan | Durum |
|---|---|---|---|---|
| 1 | `php-lint` | — | — | çalışır |
| 2 | `php-test` | `php-lint` | lint başarısızsa test başlamaz | çalışır |
| 3 | `composer-audit` | — | — | çalışır |
| 4 | `js-test` | — | — | çalışır |
| 5 | `js-coverage` | `js-test` | test düşerse kapsam başlamaz | soft |
| 6 | `js-e2e` | — | — | soft |
| 7 | (yok) `package` job'ı | — | — | ❌ tanımsız (Q6) |
| 8 | (yok) `sign` job'ı | — | — | ❌ tanımsız (Q-X45) |
| 9 | (yok) `release` job'ı | — | — | ❌ tanımsız |
| 10 | (yok) `windows` matrisi | — | — | ❌ tanımsız (Q-X3) |
| 11 | (yok) `macos` matrisi | — | — | ❌ tanımsız (Q-X3) |
| 12 | (yok) `arm64` matrisi | — | — | ❌ tanımsız (Raspberry Pi hedefi için) |

### §4.3 Artefakt kataloğu (üretilen/üretilecek)

| # | Artefakt | Üreten | Durum |
|---|---|---|---|
| 1 | PHPUnit junit çıktısı | `php-test` | ⚠️ VERIFICATION REQUIRED (`--log-junit` yok) |
| 2 | Vitest coverage raporu | `js-coverage` | ⚠️ VERIFICATION REQUIRED (upload adımı yok) |
| 3 | Playwright HTML raporu | `js-e2e` | ⚠️ VERIFICATION REQUIRED (upload adımı yok) |
| 4 | Composer audit raporu | `composer-audit` | ⚠️ VERIFICATION REQUIRED (dosya yok) |
| 5 | PHPStan raporu | `php-lint` | ⚠️ VERIFICATION REQUIRED (dosya yok) |
| 6 | `build/` dizini | — | ❌ yok (PLANNED) |
| 7 | `.msi` / `.deb` / `.tar.gz` | — | ❌ yok (PLANNED) |
| 8 | SBOM (SPDX/CycloneDX) | — | ❌ yok (PLANNED) |
| 9 | imza dosyası (`.asc`/`.p7s`) | — | ❌ yok (PLANNED) |
| 10 | checksum dosyası (`sha256`) | — | ❌ yok (PLANNED) |

> **Dürüstlük:** `actions/upload-artifact` bu repo'da **geçmiyor** (envanter
> taraması sonucu) → satır 1-5 `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

### §4.4 Ortam değişkenleri ve secret envanteri

| # | Değişken | Kaynak | Kullanım | Durum |
|---|---|---|---|---|
| 1 | `PHP_VERSION` | `ci.yml:22` | kurulum sürümü | ✅ S1 |
| 2 | `COMPOSER_NO_INTERACT` | yok | — | tanımsız |
| 3 | `COMPOSER_CACHE_DIR` | yok | `actions/cache` karşılıyor | ✅ S1 (`ci.yml:43`) |
| 4 | `NODE_VERSION` | `ci.yml:151` | Node kurulumu | ✅ S1 |
| 5 | `npm_config_cache` | yok | `cache: npm` karşılıyor | ✅ S1 (`ci.yml:153`) |
| 6 | `SOURCE_DATE_EPOCH` | yok | tekrar üretilebilirlik | ❌ tanımsız |
| 7 | `TZ` | yok | zaman damgası tutarlılığı | ❌ tanımsız |
| 8 | `LANG`/`LC_ALL` | yok | çıktı kodlaması | ❌ tanımsız |
| 9 | `ARTIFACT_SIGNING_KEY` | yok | imzalama | ❌ tanımsız (Q-X45) |
| 10 | `DEPLOY_TOKEN` | yok | yayın | ❌ tanımsız |
| 11 | `runner.os` | GitHub Actions | cache anahtarı gömülü | ✅ S1 (`ci.yml:44`) |
| 12 | `matrix.project` | `ci.yml:75,79` | test matrisi | ✅ S1 |

### §4.5 Monorepo alt-proje envanteri (bağımlılık çözümleme birimleri)

| # | Alt proje | Bağımlılık dosyası | CI adımı | Kanıt |
|---|---|---|---|---|
| 1 | `shared/` | `composer.json` | `ci.yml:48-50` | ✅ S1 |
| 2 | `auth.coremusic.net/` | `composer.json` | `ci.yml:52-54` | ✅ S1 |
| 3 | `home.coremusic.net/` | `composer.json` | `ci.yml:56-58` | ✅ S1 |
| 4 | kök | `package.json` | `ci.yml:154-155` | ✅ S1 |
| 5 | `assets.coremusic.net/` | (kök lock ile) | `ci.yml:158` (`--root`) | ✅ S1 |
| 6 | `shared/` | `phpunit.xml` | `ci.yml:105` | ✅ S1 |
| 7 | `auth.coremusic.net/` | `phpunit.xml` | `ci.yml:105-107` | ✅ S1 |
| 8 | `home.coremusic.net/` | `phpunit.xml` | — | ⚠️ VERIFICATION REQUIRED (test job'da yok) |
| 9 | `assets.coremusic.net/` | `playwright.config.ts` | `ci.yml:203` | ✅ S1 |
| 10 | `assets.coremusic.net/` | `vitest` yapılandırması | `ci.yml:158` | ✅ S1 |
| 11 | — | `*.cpp` / `CMakeLists.txt` | — | ❌ diskte YOK (PLANNED) |
| 12 | — | `.editorconfig` | — | ⚠️ VERIFICATION REQUIRED (okunmadı) |

### §4.6 Kabul protokolü — paket/derleme kabulünün 10 adımı (PLANNED)

| # | Adım | Alet | Beklenen | Kapı |
|---|---|---|---|---|
| 1 | temiz kurulum | `composer install` (temiz cache) | exit 0 | `ci.yml:49` |
| 2 | statik analiz | `composer stan` | exit 0 | `ci.yml:63` |
| 3 | PHP test | `vendor/bin/phpunit` | exit 0 | `ci.yml:105` |
| 4 | bağımlılık denetimi | `composer audit` | exit 0 | `ci.yml:126` |
| 5 | JS birim test | `npm ci` + `npm run test:js` | exit 0 | `ci.yml:155,158` |
| 6 | JS kapsam | `npm run test:js:coverage` | rapor üretilir | `ci.yml:176` (soft) |
| 7 | e2e | `npm run test:e2e` | rapor üretilir | `ci.yml:203` (soft) |
| 8 | paket oluşturma | — | ⚠️ VERIFICATION REQUIRED | tanımsız |
| 9 | imzalama | — | ⚠️ VERIFICATION REQUIRED | tanımsız |
| 10 | yayın | — | ⚠️ VERIFICATION REQUIRED | tanımsız |

### §4.7 Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | lock sürüklenmesi (`composer update` ile) | orta | yüksek | CI yalnız `install` |
| 2 | PHP sürümü CI ≠ üretim | orta | yüksek | tek `env.PHP_VERSION` + `composer.json:8` |
| 3 | Node sürümü `engines` ile çelişirse | düşük | orta | `ci.yml:151` `engines` senkronu ⚠️ doğrulanmadı |
| 4 | `ubuntu-latest` sürüm değişimi | orta | orta | `runner.os` cache anahtarı sıfırlar |
| 5 | CVE yayımlanması | orta | yüksek | `composer audit` gate bağlantısı UNKNOWN |
| 6 | Secret sızıntısı | düşük | kritik | `secret-scan.yml` (envanter kanıtı) |
| 7 | e2e soft gate'in kalıcılaşması | yüksek | orta | eşik büyüyünce `continue-on-error` kaldırılır |
| 8 | native toolchain seçim gecikmesi | yüksek | orta | `tasinabilirlik-siniri` Q-X51 |
| 9 | tek platform testi (Q4) | yüksek | yüksek | matris job'ları eklenmeli (§4.2 #10-12) |
| 10 | imzasız paket dağıtımı | orta | kritik | `-surucu` §35 zinciri |

### §5.4 Kod satırı doğrulama komutları (reprodüksiyon)

| # | Dosya | Satır | Komut | Beklenen çıktı |
|---|---|---|---|---|
| 1 | `shared/composer.json` | 44 | `Get-Content shared/composer.json \| Select-Object -Index 43` | `"optimize-autoloader": true` |
| 2 | `shared/composer.json` | 48 | `Select-Object -Index 47` | `"stan": "phpstan analyse src --level=5"` |
| 3 | `package.json` | 10 | `Get-Content package.json \| Select-Object -Index 9` | `"devDependencies": {` |
| 4 | `.github/workflows/ci.yml` | 44 | `Select-Object -Index 43` | `key: ${{ runner.os }}-composer-…` |
| 5 | `.github/workflows/ci.yml` | 155 | `Select-Object -Index 154` | `run: npm ci` |
| 6 | `.github/workflows/ci.yml` | 126 | `Select-Object -Index 125` | `composer audit` |
| 7 | `.github/workflows/ci.yml` | 167 | `Select-Object -Index 166` | `continue-on-error: true` |
| 8 | `.github/workflows/ci.yml` | 22 | `Select-Object -Index 21` | `PHP_VERSION: '8.4'` |

> Bu tablo, §5.2'deki S1 kanıtlarının **tekrar üretilebilir doğrulama
> yolu**dur; her satır PowerShell ile tek komutta gözlemlenmiştir.

## §5 Referans

### §5.1 Web doğrulama (2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| LLVM araç zinciri belgeleri toolchain seçimi için resmi kaynaktır | https://clang.llvm.org/docs/Toolchain.html | 2026-10-13 | ✅ HTTP 200 |
| Meson yapılandırıcısının kullanım kılavuzu resmi sitededir | https://mesonbuild.com/Manual.html | 2026-10-13 | ✅ HTTP 200 |
| GCC dil seçenekleri C/C++ derleme bayraklarının tanımlayıcı kaynağıdır | https://gcc.gnu.org/onlinedocs/gcc/C-Dialect-Options.html | 2026-10-13 | ✅ HTTP 200 |

### §5.2 Repo kanıtları (S1 — `Dosya` / `Satır`)

| # | Dosya | Satır | Doğrulama |
|---|---|---|---|
| 1 | `shared/composer.json` | 44 | `Get-Content … \| Select-Object -Index 43` → `optimize-autoloader: true` |
| 2 | `shared/composer.json` | 48 | `Select-Object -Index 47` → `phpstan analyse src --level=5` |
| 3 | `package.json` | 10 | `Select-Object -Index 9` → `devDependencies` bloğu |
| 4 | `.github/workflows/ci.yml` | 44 | `Select-Object -Index 43` → cache `key` satırı |
| 5 | `.github/workflows/ci.yml` | 155 | `Select-Object -Index 154` → `npm ci` |
| 6 | `.github/workflows/ci.yml` | 126 | `Select-Object -Index 125` → `composer audit` |

### §5.3 Komşu referanslar

| # | Dosya | Ne alır |
|---|---|---|
| 1 | `derleme-ve-paketleme-anayasa.md` | toolchain matrisi · bağımlılık modeli · reproducible kuralları |
| 2 | `derleme-ve-paketleme-surucu.md` | paket formatı sürücü matrisi · imza zinciri §35 |
| 3 | `../platform-yuzeyleri/` | OS API/ABI farkları (bu dosyanın kapsam dışı) |
| 4 | `../tasinabilirlik-siniri/` | hangi kod taşınabilir (bu dosyanın kapsam dışı) |
| 5 | `../../isletim-sistemi-testing/` | test koşumu (bu dosya yalnız yapılandırma gösterir) |

### §5.4 Anti-pattern → düzeltme özeti (bu dosyanın §3-§4 kesiti)

| # | Anti-pattern | Bulunduğu § | Düzeltme (§4 karşılığı) | Repo kanıtı |
|---|---|---|---|---|
| 1 | Lock dosyasız bağımlılık kurulumu | §3.1 | `composer.lock` commit + `composer install` (CI) | `ci.yml:155` `npm ci` |
| 2 | `npm install` ile CI kurulumu | §3.1 | `npm ci` (lock dosyasına sadık kurulum) | `ci.yml:155` |
| 3 | OS-özel önbellek anahtarı olmayan cache | §3.2 | `${{ runner.os }}-composer-...` | `ci.yml:44` |
| 4 | Composer sürüm uyuşmazlığı (local ≠ CI) | §3.2 | `composer.json:8` ↔ CI PHP sürümü eşleşmesi | `composer.json:8` |
| 5 | `optimize-autoloader` kapalı üretim kurulumu | §4.3 | `optimize-autoloader: true` | `composer.json:44` |
| 6 | Statik analiz CI dışında çalıştırma | §4.1 | `phpstan analyse src --level=5` | `composer.json:48` |
| 7 | `SOURCE_DATE_EPOCH` sabitlenmemiş tekrar-üretim | §4.4 | bayrak seti (PLANNED — anayasa §4) | repo'da yok |
| 8 | Audit adımı olmadan bağımlılık güncelleme | §4.5 | `composer audit` | `ci.yml:126` |

### §5.5 Sürüm alanları — repo doğrulaması (2026-10-13 okuması)

| # | Dosya | Satır | Alan | Değer / durum |
|---|---|---|---|---|
| 1 | `shared/composer.json` | 6 | `version` | `2.0.0` (okundu, §5.1 anayasa kaydıyla eşleşiyor) |
| 2 | `shared/composer.json` | 8 | `require.php` | CI PHP sürümüyle eşleştirilmeli (§3.2 anti-pattern #4) |
| 3 | `package.json` | 10 | `devDependencies` | blok var; tek tek sürüm değerleri bu dosyada tekrarlanmaz |
| 4 | `.github/workflows/ci.yml` | 155 | `npm ci` | lock-faithful kurulum doğrulandı |
| 5 | `.github/workflows/ci.yml` | 44 | cache `key` | `${{ runner.os }}` öneki — OS ayrımı doğrulandı |
| 6 | `.github/workflows/ci.yml` | 126 | `composer audit` | bağımlılık güvenlik denetimi adımı mevcut |

> `package.json` `version` alanı: repo'da okunmadı — **⚠️ VERIFICATION REQUIRED** (anayasa §10 Q-X31 ile aynı işaret).
> `npm audit` adımı: `ci.yml` okumasında gözlemlenmedi — **UNKNOWN** (audit yalnız composer tarafında kanıtlandı).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
