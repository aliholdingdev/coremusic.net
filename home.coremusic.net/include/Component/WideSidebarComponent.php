<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;

/**
 * WideSidebarComponent — "Şu an çalan" sağ kenar çubuğu (Playlist + Geçmiş)
 * ------------------------------------------------------------------
 * View partial : pages/components/wide-sidebar.php
 * Layer        : L3 Presentation · 05_Pages (_player.css — SAĞ KENAR ÇUBUĞU bloğu)
 * SSOT         : .ai/.png/home-1920/Linux 1920 -  Playlist  Page.png → 4 bölüm
 *                .ai/.png/home-1024/Linux  1024 - Playlist Page.png  → yalnız 1+2
 * Veri kaynağı : $_SESSION current_* — PlayerInfoComponent ile AYNI kaynak (şu an çalan)
 * Kullanım     : $loader->display('wide-sidebar', $variant)  → nowplaying.php · history.php
 *
 * Bölüm envanteri (kullanıcı onayı — 2026-10-17):
 *   1) profil kartı : dairesel avatar + şarkı adı + alt satır + 5 aksiyon butonu
 *   2) öneriler     : 2 satır × (2 etiket + 4 dairesel sanatçı)
 *   3) durum kartı  : Şarkı / Albüm / Sanatçı / Yıldız / Bit rate / Süre + progress
 *   4) boş kart
 * Tier: Embedded (1024) → 1+2 · Wide & FourK (1920 / 3840) → 1+2+3+4
 *
 * Not (v2 — 2026-10-17): hardcoded `cover-ursat-gur.png` · `Bit rate : 350 kbps`
 *   · `Süre : 00:00:00` kaldırıldı → gerçek çalan kapak/veri.
 * Not (v3 — 2026-10-17): mockup'a göre profil + öneriler bölümleri eklendi; eski
 *   kare kapak (221×221) dairesel avatar ile değiştirildi.
 * Not: sanatçı dairelerinde yalnız diskte VAR olan `Image/res-pink/*.png`
 *   kullanılır — `album-default.png` diskte YOK (HEAD'de kırık referans idi).
 * ------------------------------------------------------------------
 */
final class WideSidebarComponent extends AbstractComponent
{
    /** Figma-1920 ikon tabanı (`/Image/figma-1920`). */
    public readonly string $figmaBase;

    /** Çalan şarkının kapağı — `$_SESSION['current_art']` fallback album-goksel.png. */
    public readonly string $cover;

    public readonly string $title;
    public readonly string $album;
    public readonly string $artist;
    public readonly int    $stars;
    public readonly string $bitrate;
    public readonly string $elapsed;
    public readonly string $duration;

    /** Wide/FourK (1920/3840) → durum + boş kart render edilir; Embedded (1024) → edilmez. */
    public readonly bool $showStatusCards;

    /** Sanatçı ızgarası link hedefi — routes.php'de tanımlı tek sanatçı rotası. */
    public readonly string $artistsHref;

    /**
     * Öneri satırları — mockup'taki 2×2 düzen: her satır iki etiket + dört dairesel sanatçı.
     *
     * @var list<array{labels: list<string>, artists: list<array{name: string, img: string}>}>
     */
    public readonly array $recRows;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->figmaBase = $this->asset('/Image/figma-1920');

        /* Kapak — eski hardcoded İRSAT GÜR yerine gerçek çalan kapak */
        $this->cover = (string)($_SESSION['current_art'] ?? $this->asset('/Image/res-pink/album-goksel.png'));

        $this->title    = $this->sessionString('current_song', 'Göksel - Sevil Neşelen');
        $this->album    = $this->sessionString('current_album', 'Hayat Rüya Gibi');
        $this->artist   = $this->sessionString('current_artist', 'Göksel');
        $this->stars    = 5;
        $this->bitrate  = $this->sessionString('current_bitrate', '350 kbps');
        $this->elapsed  = $this->sessionString('current_elapsed', '00:00:00');
        $this->duration = $this->sessionString('current_duration', '00:05:00');

        $this->showStatusCards = $this->isWide();
        $this->artistsHref     = '/sanatcilar';

        /* Sanatçı daireleri — yalnız diskte mevcut res-pink görselleri */
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/default-album.png');
        $art3 = $this->asset('/Image/res-pink/album-kursat.png');
        $art4 = $this->asset('/Image/res-pink/girl-1.png');

        $this->recRows = [
            [
                'labels'  => ['Önerilen Sanatçılar', 'Tavsiye Edilenler'],
                'artists' => [
                    ['name' => 'Göksel',    'img' => $art1],
                    ['name' => 'Zara',      'img' => $art2],
                    ['name' => 'Sibel Can', 'img' => $art3],
                    ['name' => 'Dilsöz',    'img' => $art4],
                ],
            ],
            [
                'labels'  => ['Son Dinlenenler', 'Tavsiye Edilen Çalar'],
                'artists' => [
                    ['name' => 'Ferhat Göçer', 'img' => $art4],
                    ['name' => 'Barış Manço',  'img' => $art1],
                    ['name' => 'Zara',         'img' => $art2],
                    ['name' => 'Kış Masalı',   'img' => $art3],
                ],
            ],
        ];
    }

    public function key(): string
    {
        return 'wide-sidebar';
    }
}
