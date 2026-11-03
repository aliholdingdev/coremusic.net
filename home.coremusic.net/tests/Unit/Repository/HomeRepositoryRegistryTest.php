<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Repository;

use CoreMusic\Database\ConnectionFactory;
use CoreMusic\Home\Container\HomeContainer;
use CoreMusic\Home\Repository\MusicRepository;
use CoreMusic\Home\Repository\PlaylistRepository;
use PHPUnit\Framework\TestCase;

/**
 * P0-1 + P0-2 kapısı (2026-10-30).
 *
 * P0-1: `implements IMusicRepository` kırık working-copy nedeniydi (arayüz home'da
 * YOK → "Interface not found" fatal, 4 test error). Fabrika `fromEnvironment()`
 * 7 çağırıcı tarafından kullanılıyor ve geri kondu.
 * P0-2: `home_music` / `home_playlist` anahtarları registry'de kayıtlı değildi →
 * `registry->get()` RuntimeException. Kayıt HomeContainer::databaseRegistry() ile
 * api ApiContainerBase deseniyle yapılıyor (getOrRegister + providerFor, lazy PDO).
 */
final class HomeRepositoryRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        ConnectionFactory::reset();
    }

    protected function tearDown(): void
    {
        ConnectionFactory::reset();
    }

    public function testMusicRepository_fromEnvironment_returnsInstance(): void
    {
        // Arrange — kırık working-copy'de bu sınıf interface hatasıyla yüklenemiyordu
        // Act
        $repo = MusicRepository::fromEnvironment();

        // Assert
        $this->assertInstanceOf(MusicRepository::class, $repo);
    }

    public function testPlaylistRepository_fromEnvironment_returnsInstance(): void
    {
        // Act
        $repo = PlaylistRepository::fromEnvironment();

        // Assert
        $this->assertInstanceOf(PlaylistRepository::class, $repo);
    }

    public function testDatabaseRegistry_registersHomeMusicAndHomePlaylistKeys(): void
    {
        // Act
        $registry = HomeContainer::databaseRegistry();

        // Assert — yoksa DatabaseRegistry::get() "not registered" RuntimeException
        $this->assertTrue($registry->has('home_music'));
        $this->assertTrue($registry->has('home_playlist'));
        $this->assertTrue(ConnectionFactory::getInstance()->registry()->has('home_music'));
    }

    public function testDatabaseRegistry_isIdempotent_keepsFirstManagerInstance(): void
    {
        // Arrange
        $before = HomeContainer::databaseRegistry()->get('home_music');

        // Act — ikinci çağrı getOrRegister ile mevcut kaydı okur, ezmez
        $after = HomeContainer::databaseRegistry()->get('home_music');

        // Assert
        $this->assertSame($before, $after);
    }

    public function testMusicRepository_classLoadsWithoutMissingHomeInterface(): void
    {
        // Act — Reflection autoload tetikler; `implements IMusicRepository` geri
        // gelirse (dosyasız arayüz) bu satır "Interface not found" fırlatır.
        $interfaces = (new \ReflectionClass(MusicRepository::class))->getInterfaceNames();

        // Assert
        $this->assertNotContains(
            \CoreMusic\Home\Repository\IMusicRepository::class,
            $interfaces
        );
    }
}