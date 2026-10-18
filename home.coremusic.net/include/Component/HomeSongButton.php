<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * MiniCard — paylaşılan mini kart HTML üretici (v2.0.0)
 *
 * PNG: home-1024 + home-1920 mini kart (art + başlık + alt başlık + meta).
 * Markup PNG birebirdir; değerler ham veridir, içeride escape edilir.
 */
final class HomeSongButton
{
    private function __construct()
    {
    }

    /**
     * @param array{t: string, s?: string, art: string, stream?: string, k?: string} $item ham (escape edilmemiş) kart verisi
     *        k: kart tipi rozeti — 'song' → music.png · 'video' → video.png (default 'song')
     */
    public static function html(array $item, string $metaClass, string $metaLabel): string
    {
        $h = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $assets = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
        $fallback = $assets . '/Image/res-pink/default-album.png';
        $art = is_string($item['art'] ?? null) && $item['art'] !== '' ? $item['art'] : $fallback;

        /* Figma cd_case grubu: kılıf frame (cd-case.png) + tip rozeti (music/video.png).
           Sabit string'lerden üretilir; yine de escape edilir (WCAG: alt="" + aria-hidden). */
        $case = $assets . '/Image/res-pink/cd-case.png';
        $badge = ($item['k'] ?? 'song') === 'video'
            ? $assets . '/Image/res-pink/video.png'
            : $assets . '/Image/res-pink/music.png';

        /* Figma CD katmanı — HER kartta (kullanıcı kapsamı: tüm song/playlist
           kartları); z-sırasında rozetten SONRA (en üstte), opacity CSS'te. */
        $cd = '<img class="home-song__mini-card__art-cd" src="' . $h($assets . '/Image/res-pink/cd-overlay.png') . '" alt="" aria-hidden="true"/>';

        /* Oynatma bağı — yalnızca geçerli /stream/{32hex} değeri eklenir,
           böylece XSS ile attribute enjeksiyonu imkânsızdır. */
        $stream = is_string($item['stream'] ?? null) ? $item['stream'] : '';
        $streamAttr = '';
        if (preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1) {
            /* Medya tipi sinyali — footer.init/CorePlayerShared.resolveMediaType
               bunu okur; uzantısız /stream/{32hex} URL'lerinde tipin tek kaynağıdır. */
            $kind       = ($item['k'] ?? 'song') === 'video' ? 'video' : 'audio';
            $streamAttr = ' data-stream="' . $h($stream) . '"'
                . ' data-kind="' . $kind . '"'
                . ' data-title="' . $h((string)$item['t']) . '"'
                . ' data-artist="' . $h((string)($item['s'] ?? '')) . '"';
        }

        return '<a href="/playlist" class="home-song__mini-card" data-no-spa' . $streamAttr . '>'
            . '<div class="home-song__mini-card__art"><img class="home-song__mini-card__art-img" src="' . $h($art) . '" alt="" width="50" height="50" loading="lazy"/><img class="home-song__mini-card__art-case" src="' . $h($case) . '" alt="" aria-hidden="true"/><img class="home-song__mini-card__art-badge" src="' . $h($badge) . '" alt="" aria-hidden="true"/>' . $cd . '</div>'
            . '<div class="home-song__mini-card__info">'
            . '<h3 class="home-song__mini-card__title">' . $h((string)$item['t']) . '</h3>'
            . '<p class="home-song__mini-card__subtitle">' . $h((string)($item['s'] ?? '')) . '</p>'
            . '<span class="' . $metaClass . '">' . $h($metaLabel) . '</span>'
            . '</div></a>';
    }
}
