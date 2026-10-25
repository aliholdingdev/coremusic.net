<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Security;

use CoreMusic\Security\UuidV7;
use PHPUnit\Framework\TestCase;

final class UuidV7Test extends TestCase
{
    public function testBinaryIs16Bytes(): void
    {
        $this->assertSame(16, strlen(UuidV7::generateBinary()));
    }

    public function testVersionNibbleIs7(): void
    {
        $hex = UuidV7::generateHex();
        // 13. hex karakter (index 12) version nibble
        $this->assertSame('7', $hex[12]);
    }

    public function testVariantIs10xx(): void
    {
        $hex = UuidV7::generateHex();
        // Byte 8 (hex index 16) üst 2 bit = 10 → ilk hex karakter 8, 9, a, b
        $this->assertContains($hex[16], ['8', '9', 'a', 'b']);
    }

    public function testFormattedStringMatchesRfcLayout(): void
    {
        $uuid = UuidV7::generate();
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    public function testIsNotVersion4(): void
    {
        $uuid = UuidV7::generate();
        $this->assertNotSame('4', $uuid[14]);
    }

    public function testConsecutiveUuidsAreStringSortedInGenerationOrder(): void
    {
        // Aynı milisaniyede üretilse bile timestamp prefix sıralamayı bozmamalı;
        // ms ayrıştıysa sıralılık kesin, aynı ms ise rastgele kısım kıyaslanır.
        // Bu yüzden timestamp prefix'i (ilk 12 hex) monoton olmalı.
        $uuids = [];
        for ($i = 0; $i < 50; $i++) {
            $uuids[] = UuidV7::generate();
            usleep(1500);
        }
        $sorted = $uuids;
        sort($sorted, SORT_STRING);
        $this->assertSame($uuids, $sorted);
    }

    public function testTimestampPrefixIsNonDecreasing(): void
    {
        $prev = null;
        for ($i = 0; $i < 20; $i++) {
            $prefix = substr(UuidV7::generateHex(), 0, 12);
            if ($prev !== null) {
                $this->assertGreaterThanOrEqual(0, strcmp($prefix, $prev));
            }
            $prev = $prefix;
        }
    }

    public function testUuidsAreUnique(): void
    {
        $set = [];
        for ($i = 0; $i < 500; $i++) {
            $set[UuidV7::generate()] = true;
        }
        $this->assertCount(500, $set);
    }
}
