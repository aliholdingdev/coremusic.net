<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Security;

use PHPUnit\Framework\TestCase;

/**
 * C2 regresyonu — bypass HMAC pepper fallback'i yasak (hardcoded sır veto).
 *
 * Eski kod (index.php:132):
 *   hash_hmac('sha256', 'bypass_' . date('Y-m-d'), defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass')
 * → pepper tanımsızsa GÜNLÜK auth_key herkes için aynı ve tahmin edilebilir üretiliyordu.
 *
 * Beklenen davranış: pepper yoksa ($bypassActive false) bypass bloğu hiç çalışmaz
 * — fail-closed. config/constants.php:61-68 zaten boş pepper'da 500 ile çıkar.
 */
final class PepperFallbackGuardTest extends TestCase
{
    private string $source;

    protected function setUp(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../index.php');
        $this->assertNotFalse($source, 'auth index.php okunamadı');
        $this->source = (string) $source;
    }

    public function testIndex_neverContainsHardcodedPepperFallback(): void
    {
        $this->assertStringNotContainsString(
            "'coremusic-bypass'",
            $this->source,
            'C2: hardcoded pepper fallback yasak — pepper yoksa bypass hiç üretilmemeli (fail-closed)'
        );
    }

    public function testBypassFlag_requiresDefinedNonEmptyPepper(): void
    {
        $this->assertMatchesRegularExpression(
            '/\$bypassActive\s*=\s*defined\(\'APP_PEPPER\'\)\s*&&\s*APP_PEPPER\s*!==\s*\'\'/',
            $this->source,
            'C2: $bypassActive şart zinciri APP_PEPPER tanımlı ve boş değil kontrolüyle başlamalı'
        );
    }
}
