<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Component;

use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\WelcomeBannerComponent;
use PHPUnit\Framework\TestCase;

final class WelcomeBannerComponentTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testInstantiation_defaultValuesMatchReference(): void
    {
        $component = new WelcomeBannerComponent(HomeLayoutVariant::Wide);

        $this->assertSame('welcome-banner', $component->key());
        $this->assertSame('female', $component->gender);
        $this->assertSame('Hoş Geldin', $component->greet);
        $this->assertSame('Işıl Peri Yıldız ♡', $component->userName);
        $this->assertSame('Müziğin dünyasına hoş geldin!', $component->tagline);
        $this->assertStringContainsString('default-avatar.png', $component->avatar);
        $this->assertStringContainsString('bkimage1.png', $component->heroImage);
    }

    public function testSessionUser_reflectsInComponent(): void
    {
        $_SESSION['MM_DisplayName'] = 'Aslıhan Kaya';
        $component = new WelcomeBannerComponent(HomeLayoutVariant::Wide);

        $this->assertSame('Aslıhan Kaya', $component->userName);
    }

    public function testMaleSession_configuresMaleAssets(): void
    {
        $_SESSION['cm_gender'] = 'male';
        $component = new WelcomeBannerComponent(HomeLayoutVariant::Wide);

        $this->assertSame('male', $component->gender);
        $this->assertSame('Baran Ateş Kılıç ✦', $component->userName);
        $this->assertSame('Müziğin ritmine katıl!', $component->tagline);
        $this->assertStringContainsString('erkek-gender-select.png', $component->avatar);
        $this->assertStringContainsString('login-bg-male.png', $component->heroImage);
    }

    public function testRender_includesGenderModifiers(): void
    {
        // Female render
        $_SESSION['cm_gender'] = 'female';
        $femaleComp = new WelcomeBannerComponent(HomeLayoutVariant::Wide);
        $femaleHtml = $femaleComp->render();
        $this->assertStringContainsString('welcome-banner--female', $femaleHtml);
        $this->assertStringContainsString('data-gender="female"', $femaleHtml);

        // Male render
        $_SESSION['cm_gender'] = 'male';
        $maleComp = new WelcomeBannerComponent(HomeLayoutVariant::Wide);
        $maleHtml = $maleComp->render();
        $this->assertStringContainsString('welcome-banner--male', $maleHtml);
        $this->assertStringContainsString('data-gender="male"', $maleHtml);
    }
}
