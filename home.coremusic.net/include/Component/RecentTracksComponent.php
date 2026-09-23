<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\HomeSongButton;

/**
 * RecentTracksComponent — En Son Dinlenen Şarkılar (v2.0.0)
 * PNG: home-1920 tam genişlik kart satırı (9 kart) / home-1024 bottom-left (2×2, 4 kart)
 */
final class RecentTracksComponent extends AbstractComponent
{
    /** @var list<string> render edilmiş mini kart HTML'leri */
    public readonly array $cards;

    /**
     * @param list<array{t: string, a: string, d: string, art: string}>|null $tracks
     *        override verisi — null ise PNG varsayılanları kullanılır
     */
    public function __construct(HomeLayoutVariant $variant, ?array $tracks = null)
    {
        parent::__construct($variant);

        $cards = array_map(
            fn (array $t): string => HomeSongButton::html(
                ['t' => (string)$t['t'], 's' => (string)$t['a'], 'art' => (string)($t['art'] ?? '')],
                'mini-card__subtitle',
                (string)$t['d']
            ),
            $tracks ?? $this->defaultTracks()
        );

        $this->cards = $variant->isWide() ? $cards : array_slice($cards, 0, 3);
    }

    public function key(): string
    {
        return 'recent-tracks';
    }

    /**
     * PNG home-1920 satır sırası (9 kart); 'art' mutlak asset URL'dir.
     *
     * @return list<array{t: string, a: string, d: string, art: string}>
     */
    private function defaultTracks(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        return [
            ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00', 'art' => $art1],
            ['t' => 'Göksel - Kabahat Senin Se', 'a' => 'Göksel', 'd' => '00:04:12', 'art' => $art2],
            ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $art1],
            ['t' => 'Göksel - Donbil Neşeler', 'a' => 'Göksel', 'd' => '00:04:37', 'art' => $art2],
            ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00', 'art' => $art1],
            ['t' => 'Göksel - Kabahat Senin Se', 'a' => 'Göksel', 'd' => '00:04:12', 'art' => $art2],
            ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $art1],
            ['t' => 'Göksel - Donbil Neşeler', 'a' => 'Göksel', 'd' => '00:04:37', 'art' => $art2],
            ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $art1],
        ];
    }
}
