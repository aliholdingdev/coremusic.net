<?php

declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use PHPUnit\Framework\TestCase;

/**
 * API Service — Smoke Test (iskelet)
 *
 * Faz 0: yalnızca dosya/iskelet varlığını doğrular.
 * Gerçek /health uç testi Faz 5'te (test sunucusu kurulumu ile) eklenecek.
 */
final class SmokeTest extends TestCase
{
    public function testFrontControllerExists(): void
    {
        self::assertFileExists(dirname(__DIR__, 2) . '/index.php');
        self::assertFileExists(dirname(__DIR__, 2) . '/autoload.php');
        self::assertFileExists(dirname(__DIR__, 2) . '/config/constants.php');
        self::assertFileExists(dirname(__DIR__, 2) . '/config/app.php');
        self::assertFileExists(dirname(__DIR__, 2) . '/config/cors.php');
    }

    public function testHealthEndpointPlaceholder(): void
    {
        // Faz 5: gerçek HTTP /health 200 kontrolü buraya taşınacak.
        self::assertTrue(true, "Health kontrolü Faz 5'te eklenecek.");
    }
}
