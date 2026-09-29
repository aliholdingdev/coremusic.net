<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Loader;

use CoreMusic\Home\Class\ComponentLoader;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\PlayerInfoComponent;
use CoreMusic\Home\Component\RecentTracksComponent;
use CoreMusic\Home\Interfaces\ComponentInterface;
use CoreMusic\Home\Test\Support\FakeComponent;
use PHPUnit\Framework\TestCase;

final class ComponentLoaderTest extends TestCase
{
    public function testDefaultRegistry_exposesBuiltInComponents(): void
    {
        // Arrange
        $loader = new ComponentLoader();

        // Act
        $keys = $loader->keys();

        // Assert
        $this->assertContains('player-info', $keys);
        $this->assertContains('recent-tracks', $keys);
        $this->assertTrue($loader->has('player-info'));
        $this->assertFalse($loader->has('unknown-key'));
    }

    public function testMake_builtinComponent_returnsMatchingKeyInstance(): void
    {
        // Arrange
        $loader = new ComponentLoader();

        // Act
        $player = $loader->make('player-info');
        $tracks = $loader->make('recent-tracks', HomeLayoutVariant::Wide);

        // Assert
        $this->assertInstanceOf(PlayerInfoComponent::class, $player);
        $this->assertSame('player-info', $player->key());
        $this->assertInstanceOf(RecentTracksComponent::class, $tracks);
        $this->assertSame(HomeLayoutVariant::Wide, $tracks->variant);
    }

    public function testRegister_validSubclass_isUsableAndOverwritesSameKey(): void
    {
        // Arrange
        $loader = new ComponentLoader();

        // Act
        $loader->register('fake', FakeComponent::class);
        $made = $loader->make('fake', HomeLayoutVariant::FourK);

        // Assert
        $this->assertTrue($loader->has('fake'));
        $this->assertInstanceOf(FakeComponent::class, $made);
        $this->assertSame(
            '<div class="fake-component">FourK</div>',
            $loader->render('fake', HomeLayoutVariant::FourK)
        );

        // Aynı anahtar ikinci kez kaydedilirse ezilir, istisna fırlatılmaz.
        $loader->register('fake', FakeComponent::class);
        $this->assertSame(['player-info', 'recent-tracks', 'fake'], $loader->keys());
    }

    public function testRegister_classNotImplementingInterface_throwsInvalidArgumentException(): void
    {
        // Arrange
        $loader = new ComponentLoader();

        // Act + Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ComponentInterface uygulamıyor/');

        $loader->register('bad', \stdClass::class);
    }

    public function testMake_unknownKey_throwsInvalidArgumentException(): void
    {
        // Arrange
        $loader = new ComponentLoader();

        // Act + Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Bilinmeyen bileşen: nope/');

        $loader->make('nope');
    }

    public function testDisplay_writesRenderedHtmlToOutputBuffer(): void
    {
        // Arrange
        $loader = new ComponentLoader();
        $loader->register('fake', FakeComponent::class);

        // Act
        ob_start();
        $loader->display('fake', HomeLayoutVariant::Embedded);
        $output = (string)ob_get_clean();

        // Assert
        $this->assertSame('<div class="fake-component">Embedded</div>', $output);
    }

    public function testMadeInstance_satisfiesComponentInterface(): void
    {
        // Arrange
        $loader = new ComponentLoader();

        // Act
        $made = $loader->make('recent-tracks');

        // Assert
        $this->assertInstanceOf(ComponentInterface::class, $made);
    }
}
