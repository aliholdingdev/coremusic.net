# Not — CoreMusic Repo Envanteri (Disk Kanıtlı)

Tarih: 2026-10-06 · Kaynak türü: not (repo keşfi) · Kök: `C:\www\coremusic.net`

CoreMusic deposu çok-altyapılı bir PHP 8.4 platformudur. Kök dizinde `package.json` yalnızca `playwright ^1.62.1` bağımlılığını tutar; asıl uygulama kodu `shared/` ve alt alan adı dizinlerinde yaşar. `shared/composer.json` paketi `coremusic/shared-infrastructure` v2.0.0 (type: library, license: proprietary) olarak tanımlıdır ve PHP `>=8.4` ile `ext-apcu`, `ext-pdo`, `ext-json`, `ext-mbstring` uzantılarını zorunlu kılar; PSR-4 autoload namespace'i `CoreMusic\` → `src/` şeklindedir. Test ve statik analiz araçları `phpunit/phpunit ^10.5` ve `phpstan/phpstan ^1.10`'dur; composer scriptleri `test` (phpunit) ve `stan` (phpstan level 5) olarak tanımlıdır. Ek bağımlılıklar: `psr/log`, `psr/cache`, `psr/container`, `psr/event-dispatcher`, `symfony/event-dispatcher ^7.0`, `respect/validation`, `nyholm/psr7`, `php-di/php-di ^7.0`.

Alt alan adı dizinleri beştir ve her biri kendi composer.json, phpunit.xml ve CONTEXT/AGENTS/WORKFLOW dokümanlarıyla modüler kurulmuştur: `api.coremusic.net` (config, include, tests, index.php), `auth.coremusic.net` (handler, routes, pages, tests), `home.coremusic.net` (pages, include, header/footer.php, tests), `media.coremusic.net` (bin, config, docs, src, media — GUI spek serisi), `assets.coremusic.net` (Css, Fonts, Image, js + `playwright.config.ts` + `vitest.config.js`). Frontend tarafında Playwright E2E ve Vitest birim testi altyapısı `assets.coremusic.net` altında konumlanır; kök düzeyindeki Playwright bağımlılığı da bunu besler. `shared/tests` ve her subdomain `tests/` diziniyle PHPUnit kapsamı dağınık ama modülerdir.

Kişi kaydı yoktur: depoda insan adı/rolü içeren bir kaynak tespit edilmemiştir (bilgi boşluğu, uydurulmaz).

## Öne çıkan çıkarımlar

1. Tek repository, 6 modül: `shared/` + 5 alt alan adı; her modül kendi test ve konfigürasyonuna sahip.
2. Zorunlu teknoloji omurgası: PHP 8.4+, PDO, APCu, Composer, PSR arayüzleri, PHP-DI.
3. Test üçlüsü: PHPUnit 10.5 (backend), PHPStan level 5 (statik analiz), Playwright + Vitest (frontend/assets).
4. Lisans: proprietary; paket adı `coremusic/shared-infrastructure`.
5. Kişiler bölümü için ek kaynak gerekir (depo insan verisi içermiyor).
