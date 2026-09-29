<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Layout;

use CoreMusic\Home\Class\HomeLayoutVariant;
use PHPUnit\Framework\TestCase;

final class HomeLayoutVariantTest extends TestCase
{
    public function testFromFlags_4kAndWideBothTrue_returnsFourK(): void
    {
        $this->assertSame(HomeLayoutVariant::FourK, HomeLayoutVariant::fromFlags(true, true));
    }

    public function testFromFlags_onlyWideTrue_returnsWide(): void
    {
        $this->assertSame(HomeLayoutVariant::Wide, HomeLayoutVariant::fromFlags(true, false));
    }

    public function testFromFlags_only4kTrue_returnsFourK(): void
    {
        // 4K tercih edilir: isWide=false olsa bile FourK kazanır.
        $this->assertSame(HomeLayoutVariant::FourK, HomeLayoutVariant::fromFlags(false, true));
    }

    public function testFromFlags_bothFalse_returnsEmbedded(): void
    {
        $this->assertSame(HomeLayoutVariant::Embedded, HomeLayoutVariant::fromFlags(false, false));
    }

    public function testIsWide_wideAndFourKShareWideMarkup(): void
    {
        $this->assertTrue(HomeLayoutVariant::Wide->isWide());
        $this->assertTrue(HomeLayoutVariant::FourK->isWide());
        $this->assertFalse(HomeLayoutVariant::Embedded->isWide());
    }
}
