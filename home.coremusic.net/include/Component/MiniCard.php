<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * MiniCard — paylaşılan mini kart HTML üretici (v2.0.0)
 *
 * PNG: home-1024 + home-1920 mini kart (art + başlık + alt başlık + meta).
 * Markup PNG birebirdir; değerler ham veridir, içeride escape edilir.
 */
final class MiniCard
{
    private function __construct()
    {
    }

    /**
     * @param array{t: string, s?: string, art: string} $item ham (escape edilmemiş) kart verisi
     */
    public static function html(array $item, string $metaClass, string $metaLabel): string
    {
        $h = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $fallback = (defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net') . '/Image/res-pink/default-album.png';
        $art = is_string($item['art'] ?? null) && $item['art'] !== '' ? $item['art'] : $fallback;
        return '<a href="/playlist" class="mini-card" data-no-spa>'
            . '<div class="mini-card__art"><img src="' . $h($art) . '" alt="" width="50" height="50" loading="lazy"/></div>'
            . '<div class="mini-card__info">'
            . '<h3 class="mini-card__title">' . $h((string)$item['t']) . '</h3>'
            . '<p class="mini-card__subtitle">' . $h((string)($item['s'] ?? '')) . '</p>'
            . '<span class="' . $metaClass . '">' . $h($metaLabel) . '</span>'
            . '</div></a>';
    }
}
