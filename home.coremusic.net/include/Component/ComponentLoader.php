<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * ComponentLoader — dinamik bileşen kayıt + yükleme merkezi (v2.0.0)
 *
 * Kullanım (pages/home.php):
 *
 *   $loader = new ComponentLoader();
 *   $loader->display('now-playing', $variant);
 *
 * Yeni bileşen ekleme:
 *   a) Kalıcı: REGISTRYDefaults'a sınıf ekle (varsayılan harita)
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

    /** @return array<string, class-string<ComponentInterface>> */
    public static function defaultRegistry(): array
    {
        return [
            'now-playing'    => NowPlayingComponent::class,
            'welcome-banner' => WelcomeBannerComponent::class,
            'home-widgets'   => HomeWidgetsComponent::class,
            'recent-tracks'  => RecentTracksComponent::class,
            'playlists'      => PlaylistsComponent::class,
            'up-next'        => UpNextComponent::class,
            'welcome-modal'  => WelcomeModalComponent::class,
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
}
