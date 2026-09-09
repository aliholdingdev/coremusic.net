<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * UpNextComponent — Sıradaki Şarkılar (v2.0.0)
 * PNG: home-1024 bottom-right, tek mini kart
 */
final class UpNextComponent extends AbstractComponent
{
    /** Render edilmiş mini kart HTML'i (MiniCard::html çıktısı) */
    public readonly string $card;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);
        $this->card = MiniCard::html(
            [
                't'   => $this->sessionString('current_song', 'Göksel - Sevil Neşelen'),
                's'   => $this->sessionString('current_album', 'Hayat Rüya Gibi'),
                'art' => $this->asset('/Image/res-pink/album-goksel.png'),
            ],
            'mini-card__album',
            '236 kez'
        );
    }

    public function key(): string
    {
        return 'up-next';
    }
}
