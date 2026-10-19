<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Class;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Test\Support\BehaviorStubComponent;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * Karakterizasyon testleri — CoreMusic\Home\Class\AbstractComponent (home).
 *
 * Amaç: home AbstractComponent davranışını sabitlemek (K5).
 * NOT (2026-10-18): asset slash normalizasyonu home'a TAŞINDI — drift
 * shared ile çözüldü; home artık shared ile AYNI normalize'ı uygular:
 *
 *   - home asset():   rtrim($base,'/') . '/' . ltrim($path,'/')  (normalize VAR)
 *   - shared asset(): aynı normalize (satır 252-256)
 */
final class HomeAbstractComponentBehaviorTest extends TestCase
{
    /** home config/constants.php:100 ile aynı varsayılan (trailing slash YOK). */
    private const ASSETS_BASE = 'http://assets.coremusic.net';

    /**
     * ASSETS_URL'i sabitler ve döndürür (home config/constants.php:100 ile aynı).
     * Fallback testi BU metodu çağırmaz — o test ayrı süreçte tanımsız dalı doğrular.
     */
    private static function assetsBase(): string
    {
        defined('ASSETS_URL') || define('ASSETS_URL', self::ASSETS_BASE);

        return (string)ASSETS_URL;
    }

    private static function fixture(string $name): string
    {
        return __DIR__ . '/../../Support/Fixtures/' . $name;
    }

    private static function stub(
        HomeLayoutVariant $variant = HomeLayoutVariant::Embedded,
    ): BehaviorStubComponent {
        return new BehaviorStubComponent($variant, self::fixture('render-stub-partial.php'));
    }

    /* ═══════════════════════════════════════════════════════════
     * asset() — slash normalizasyonu YOK (drift kanıtı)
     * ═══════════════════════════════════════════════════════════ */

    public function testAsset_leadingSlashPath_concatenatesDirectly(): void
    {
        // Arrange
        $base = self::assetsBase();
        $stub = self::stub();

        // Act
        $url = $stub->exposedAsset('/x.css');

        // Assert — base trailing slash'sız + path leading slash'lı → TEK slash
        // (normalizasyon değil, girdi biçiminin tesadüfi sonucu)
        $this->assertSame($base . '/x.css', $url);
    }

    public function testAsset_pathWithoutLeadingSlash_addsSeparator(): void
    {
        // Arrange
        $base = self::assetsBase();
        $stub = self::stub();

        // Act
        $url = $stub->exposedAsset('x.css');

        // Assert — normalize (K5): ltrim/rtrim + '/' ekler → shared ile AYNI
        $this->assertSame($base . '/x.css', $url);
    }

    public function testAsset_normalizesMultipleLeadingSlashes(): void
    {
        // Arrange
        $base = self::assetsBase();
        $stub = self::stub();

        // Act
        $url = $stub->exposedAsset('//x.css');

        // Assert — normalize (K5): ltrim tüm leading slash'ları strip eder → tek slash (shared ile AYNI)
        $this->assertSame($base . '/x.css', $url);
    }

    #[RunInSeparateProcess]
    public function testAsset_ASSETS_URLUndefined_fallsBackToDefaultHost(): void
    {
        // Arrange — ayrı süreç: ASSETS_URL bu süreçte TANIMLI DEĞİL
        $this->assertFalse(defined('ASSETS_URL'), 'Ayrı süreçte ASSETS_URL tanımsız olmalı');
        $stub = self::stub();

        // Act
        $url = $stub->exposedAsset('/x.css');

        // Assert — fallback host'u satır 60'taki literal değerle aynı
        $this->assertSame('http://assets.coremusic.net/x.css', $url);
    }

    /* ═══════════════════════════════════════════════════════════
     * h() — ENT_QUOTES|ENT_SUBSTITUTE kaçışı (shared ile AYNI)
     * ═══════════════════════════════════════════════════════════ */

    public function testH_escapesMarkupCharacters(): void
    {
        // Arrange
        $stub = self::stub();

        // Act
        $escaped = $stub->exposedH('<b>');

        // Assert
        $this->assertSame('&lt;b&gt;', $escaped);
        $this->assertStringNotContainsString('<b>', $escaped);
    }

    public function testH_escapesQuotesAndAmpersand_entQuotes(): void
    {
        // Arrange — ENT_QUOTES: hem çift hem tek tırnak kaçar
        $stub = self::stub();

        // Act
        $escaped = $stub->exposedH('"a" & \'b\'');

        // Assert
        $this->assertSame('&quot;a&quot; &amp; &#039;b&#039;', $escaped);
    }

    /* ═══════════════════════════════════════════════════════════
     * render() akışı — ob_start → require partial → ob_get_clean
     * ═══════════════════════════════════════════════════════════ */

    public function testRender_requiresPartialAndReturnsCapturedOutput(): void
    {
        // Arrange
        $stub = self::stub();
        $level = ob_get_level();

        // Act
        $html = $stub->render();

        // Assert — partial çıktısı tam olarak döner, dışarı ECHO edilmez
        $this->assertSame('STUB[Embedded]', $html);
        $this->assertSame($level, ob_get_level(), 'render() buffer sızdırmamalı');
    }

    public function testRender_variantIsVisibleInsidePartial(): void
    {
        // Arrange + Act — farklı varyant, aynı partial
        $html = self::stub(HomeLayoutVariant::Wide)->render();

        // Assert — require $this bağlamında çalışır: $this->variant erişilebilir
        $this->assertSame('STUB[Wide]', $html);
    }

    public function testRender_throwingPartial_cleansBufferAndRethrows(): void
    {
        // Arrange
        $this->expectOutputString(''); // PARTIAL-LEAK dışarı sızmamalı
        $stub = new BehaviorStubComponent(
            HomeLayoutVariant::Embedded,
            self::fixture('render-throwing-partial.php'),
        );
        $level = ob_get_level();

        // Act + Assert — istisna yeniden fırlatılır (sönümlenmez)
        try {
            $stub->render();
            $this->fail('RuntimeException bekleniyordu');
        } catch (\RuntimeException $e) {
            $this->assertSame('partial failed', $e->getMessage());
        }

        // Assert — catch bloğundaki ob_end_clean buffer'ı geri vermiş olmalı
        $this->assertSame($level, ob_get_level(), 'Hata durumunda buffer temizlenmeli');
    }

    public function testDisplay_echoesRenderedHtml(): void
    {
        // Arrange
        $stub = self::stub();

        // Act
        ob_start();
        $stub->display();
        $out = (string)ob_get_clean();

        // Assert — display() render() çıktısını echo eder
        $this->assertSame('STUB[Embedded]', $out);
    }

    /* ═══════════════════════════════════════════════════════════
     * Drift koruması — davranış sessizce değiştirilemesin
     * ═══════════════════════════════════════════════════════════ */

    public function testCoreMethods_areFinal_preventingSilentOverride(): void
    {
        // Arrange + Act + Assert — alt sınıflar render/h/asset/display'i
        // override edip davranışı (örn. slash normalizasyonunu) sessizce
        // değiştiremez; her değişiklik üretim kodunda açıkça yapılır.
        foreach (['render', 'display', 'h', 'asset'] as $method) {
            $ref = new \ReflectionMethod(AbstractComponent::class, $method);
            $this->assertTrue($ref->isFinal(), "{$method}() final olmalı");
        }
    }
}
