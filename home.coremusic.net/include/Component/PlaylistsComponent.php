<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * PlaylistsComponent — Son Oluşturan & Sistem Tarafından Oluşturulan Playlister (v2.0.0)
 * PNG: home-1920 tam genişlik kart satırı (6 kart) / home-1024 bottom-center (2×2, 4 kart + liste butonu)
 */
final class PlaylistsComponent extends AbstractComponent
{
    /** @var list<string> render edilmiş mini kart HTML'leri */
    public readonly array $cards;

    /**
     * @param list<array{t: string, s: string, d: string, art: string}>|null $playlists
     *        override verisi — null ise PNG varsayılanları kullanılır
     */
    public function __construct(HomeLayoutVariant $variant, ?array $playlists = null)
    {
        parent::__construct($variant);

        $cards = array_map(
            fn (array $p): string => MiniCard::html(
                ['t' => (string)$p['t'], 's' => (string)($p['s'] ?? ''), 'art' => (string)($p['art'] ?? '')],
                'mini-card__album',
                (string)$p['d']
            ),
            $playlists ?? $this->defaultPlaylists()
        );

        $this->cards = $variant->isWide() ? $cards : array_slice($cards, 0, 3);
    }

    public function key(): string
    {
        return 'playlists';
    }

    /**
     * PNG home-1920 playlist kartları (6 kart); 'art' mutlak asset URL'dir.
     *
     * @return list<array{t: string, s: string, d: string, art: string}>
     */
    private function defaultPlaylists(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        return [
            ['t' => 'En Sevilen Şarkılarım', 's' => 'Slow ve Türküler', 'd' => '01:04:57', 'art' => $art1],
            ['t' => 'Yeni Çıkan Türk Halk Müziği', 's' => 'Eskiler ve Türküler', 'd' => '00:48:12', 'art' => $art2],
            ['t' => 'En Sevilen Şarkılarım', 's' => 'Akustik Seçkiler', 'd' => '01:12:30', 'art' => $art1],
            ['t' => 'Yeni Çıkan Türk Halk Müziği', 's' => 'Oyun Havaları', 'd' => '00:56:04', 'art' => $art2],
            ['t' => 'En Sevilen Şarkılarım', 's' => 'Nostaljik Slow', 'd' => '01:04:57', 'art' => $art1],
            ['t' => 'Yeni Çıkan Türk Halk Müziği', 's' => 'Damar Şarkılar', 'd' => '00:52:19', 'art' => $art2],
        ];
    }
}
