<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Security;

use PHPUnit\Framework\TestCase;

/**
 * ADR-008 bypass — production gate regresyonu (kaynak guard'ı).
 *
 * Beklenen davranış:
 *   config/constants.php:77-91  APP_ENV_MODE=production → FORCE_AUTH_BYPASS=false ve
 *                               BYPASS_USER_UUID / BYPASS_ROLE / BYPASS_USERNAME = ''
 *                               (bypass env değerleri üretimde HİÇ OKUNMAZ)
 *   index.php:60-66             $bypassActive = pepper && !production && FORCE_AUTH_BYPASS
 *
 * Dev/test davranışı korunur; yalnızca production kapısının varlığı bağlanır.
 * (BypassAuthMiddleware fail-closed'u shared tarafında: loadBypassConfig()
 *  boş BYPASS_* → [] — shared/** dosyaları bu pakette değiştirilmez.)
 */
final class BypassProductionGateGuardTest extends TestCase
{
    private string $constantsSource;
    private string $indexSource;

    protected function setUp(): void
    {
        $constants = file_get_contents(__DIR__ . '/../../../config/constants.php');
        $index     = file_get_contents(__DIR__ . '/../../../index.php');

        $this->assertNotFalse($constants, 'config/constants.php okunamadı');
        $this->assertNotFalse($index, 'index.php okunamadı');

        $this->constantsSource = (string) $constants;
        $this->indexSource     = (string) $index;
    }

    public function testConstants_productionEnvDetectedFromAppEnvMode(): void
    {
        $this->assertStringContainsString(
            "APP_ENV_MODE === 'production'",
            $this->constantsSource,
            'Gate APP_ENV_MODE=production üzerinden kurulmalı (constants.php:77)'
        );
    }

    public function testConstants_bypassEnvValuesNotReadInProduction(): void
    {
        // FORCE_AUTH_BYPASS yalnız !$isProdEnv ile eşlenmeli; BYPASS_* sabitleri
        // production'da boş kalır (production dalında env değeri okunmaz).
        $this->assertMatchesRegularExpression(
            "/define\\('FORCE_AUTH_BYPASS',\\s*!\\\$isProdEnv\\s*&&/",
            $this->constantsSource,
            'FORCE_AUTH_BYPASS define\'i !$isProdEnv şartıyla başlamalı (constants.php:88)'
        );

        $this->assertMatchesRegularExpression(
            '/if \\(!\\$isProdEnv\\) \\{/',
            $this->constantsSource,
            'Bypass env değerleri production\'da okunmamalı (constants.php:82)'
        );
    }

    public function testConstants_bypassDefaultsAreEmptyOutsideDevGate(): void
    {
        // Gate'in DIŞINDA kalan ilk değerler boş string olmalı → fail-closed.
        $this->assertMatchesRegularExpression(
            "/\\\$bypassRaw\\s*=\\s*'';/",
            $this->constantsSource,
            "\$bypassRaw gate öncesi boş başlamalı (constants.php:78)"
        );
        $this->assertMatchesRegularExpression(
            "/define\\('BYPASS_USER_UUID',\\s*\\\$bypassUuid\\)/",
            $this->constantsSource,
            'BYPASS_USER_UUID gate değişkeninden türetilmeli (constants.php:89)'
        );
    }

    public function testIndex_bypassActiveRequiresNonProductionAndPepper(): void
    {
        $this->assertMatchesRegularExpression(
            '/\\$bypassActive[\\s\\S]{0,200}!\\$isProductionEnv/',
            $this->indexSource,
            '$bypassActive zincirine !$isProductionEnv şartı bağlı kalmalı (index.php:65-66)'
        );

        $this->assertMatchesRegularExpression(
            '/\\$bypassActive[\s\S]{0,200}defined\(\'FORCE_AUTH_BYPASS\'\)\s*&&\s*FORCE_AUTH_BYPASS/',
            $this->indexSource,
            '$bypassActive yalnız FORCE_AUTH_BYPASS ile aktifleşmeli (index.php:66)'
        );
    }
}