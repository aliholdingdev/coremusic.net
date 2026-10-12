<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Theme;

use PHPUnit\Framework\TestCase;
use CoreMusic\Theme\ThemeManager;

final class ThemeManagerModeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // detectMode() $_COOKIE['cm_color_mode'] okur — testleri deterministik tut
        unset($_COOKIE['cm_color_mode']);
    }

    public function testDetectModeWithIsilPeriSessionReturnsMode(): void
    {
        $mode = ThemeManager::detectMode(['cm_color_mode' => 'ışıl-peri']);
        $this->assertSame('ışıl-peri', $mode);
    }

    public function testInjectModeAttributeWithIsilPeriReturnsAttribute(): void
    {
        $this->assertSame('data-mode="ışıl-peri"', ThemeManager::injectModeAttribute('ışıl-peri'));
    }

    public function testInjectAttributesWithFemaleAndIsilPeriReturnsCombined(): void
    {
        $this->assertSame(
            'data-gender="female" data-mode="ışıl-peri"',
            ThemeManager::injectAttributes('female', 'ışıl-peri')
        );
    }

    public function testValidModesReturnsThreeModesIncludingIsilPeri(): void
    {
        $modes = ThemeManager::validModes();
        $this->assertCount(3, $modes);
        $this->assertContains('ışıl-peri', $modes);
    }

    public function testDetectModeWithLegacyPinkReturnsNull(): void
    {
        $this->assertNull(ThemeManager::detectMode(['cm_color_mode' => 'pink']));
    }

    public function testInjectInlineStyleLegacySingleArgumentReturnsRootStyleTag(): void
    {
        $html = ThemeManager::injectInlineStyle('female');

        $this->assertStringStartsWith('<style data-cm-theme>:root{', $html);
        $this->assertStringEndsWith('}</style>', $html);
        // Gender tokenlari inline'da (FOUC icin)
        $this->assertStringContainsString('--accent:#ff4fd8', $html);
        $this->assertStringContainsString('--accent-hover:#ff7ae3', $html);
        $this->assertStringContainsString('--accent-soft:rgba(255,79,216,0.15)', $html);
        $this->assertStringContainsString('--glass-bg:rgba(255,79,216,0.08)', $html);
    }

    public function testInjectInlineStyleDoesNotInlineModeTokens(): void
    {
        // Karar 2026-10-04: mod token'lari bilincli olarak inline basilmaz
        // (data-mode attribute + render-blocking CSS flash'i zaten onler).
        // Bu test karari regression'a karsi belgeler.
        $html = ThemeManager::injectInlineStyle('female');

        $this->assertStringNotContainsString('data-mode', $html);
        $this->assertStringNotContainsString('#FF00C8', $html);
        $this->assertStringNotContainsString('rgba(255,0,200', $html);
        $this->assertStringNotContainsString('ışıl-peri', $html);
    }
}
