<?php declare(strict_types=1);

namespace CoreMusic\Home\Class;

use CoreMusic\Home\Interfaces\ComponentInterface;
use CoreMusic\Home\Component\PlayerInfoComponent;
use CoreMusic\Home\Component\RecentTracksComponent;

/**
 * ComponentLoader — dinamik bileşen kayıt + yükleme merkezi (v3.0.0)
 *
 * Figma pixel-perfect component registry.
 * Tüm component'ler Figma API'dan extract edilen boyutları kullanır.
 *
 * Kullanım (pages/home.php):
 *
 *   $loader = new ComponentLoader();
 *   $loader->display('player-info', $variant);
 *   $loader->display('widget-grid', $variant);
 *
 * Yeni bileşen ekleme:
 *   a) Kalıcı: defaultRegistry()'ye sınıf ekle
 *   b) Dinamik: $loader->register('anahtar', BileşenSınıfı::class)
 *
 * Yükleme sözleşmesi: tüm bileşen constructor'ları tek argüman alır:
 *   __construct(HomeLayoutVariant $variant, ...opsiyonel)
 */
final class ComponentLoader
{
    /** @var array<string, class-string<ComponentInterface>> */
    private array $registry;

    public function __construct()
    {
        $this->registry = self::defaultRegistry();
    }

    /**
     * Varsayılan component registry (Figma pixel-perfect)
     *
     * @return array<string, class-string<ComponentInterface>>
     */
    public static function defaultRegistry(): array
    {
        return [
            /* ─── Player & Audio ─── */
            'player-info'      => PlayerInfoComponent::class,
            'recent-tracks'    => RecentTracksComponent::class,
        ];
    }

    /**
     * Runtime bileşen kaydı — mevcut anahtarı ezer, yenisini ekler.
     *
     * @param class-string<ComponentInterface> $class
     */
    public function register(string $key, string $class): void
    {
        if (!is_subclass_of($class, ComponentInterface::class)) {
            throw new \InvalidArgumentException("{$class} ComponentInterface uygulamıyor");
        }
        $this->registry[$key] = $class;
    }

    public function has(string $key): bool
    {
        return isset($this->registry[$key]);
    }

    /**
     * Anahtar + varyant ile bileşen örneği üretir (immutable).
     */
    public function make(string $key, HomeLayoutVariant $variant = HomeLayoutVariant::Embedded): ComponentInterface
    {
        $class = $this->registry[$key]
            ?? throw new \InvalidArgumentException("Bilinmeyen bileşen: {$key}");
        return new $class($variant);
    }

    public function render(string $key, HomeLayoutVariant $variant = HomeLayoutVariant::Embedded): string
    {
        return $this->make($key, $variant)->render();
    }

    /** Bileşen HTML'ini doğrudan çıktıya yazar (include akışı). */
    public function display(string $key, HomeLayoutVariant $variant = HomeLayoutVariant::Embedded): void
    {
        echo $this->make($key, $variant)->render();
    }

    /**
     * Tüm registered component anahtarlarını döndür
     * @return array<string>
     */
    public function keys(): array
    {
        return array_keys($this->registry);
    }
}
