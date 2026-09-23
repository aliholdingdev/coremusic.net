<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Interfaces\ComponentInterface;

/**
 * PlayerInfoComponent — Player Info / Now Playing Merged Panel
 *
 * View partial: pages/components/player-info.php
 */
final class PlayerInfoComponent extends AbstractComponent
{
    public readonly string $song;
    public readonly string $album;
    public readonly string $artist;
    public readonly string $bitrate;
    public readonly string $elapsed;
    public readonly string $duration;
    public readonly int    $seekPct;
    public readonly string $imgCover;
    public readonly string $imgStar;
    public readonly string $iconPlay;
    public readonly string $iconMusic;
    public readonly string $iconCd;
    public readonly string $iconMic;
    public readonly string $iconStar;
    public readonly string $iconBitrate;
    public readonly string $iconTimer;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->song      = $this->h($this->sessionString('current_song', 'Göksel - Sevil Neşelen'));
        $this->album     = $this->h($this->sessionString('current_album', 'Hayat Rüya Gibi'));
        $this->artist    = $this->h($this->sessionString('current_artist', 'Göksel'));
        $this->bitrate   = $this->h($this->sessionString('current_bitrate', '350 kbps'));
        $this->elapsed   = $this->h($this->sessionString('current_elapsed', '00:00:00'));
        $this->duration  = $this->h($this->sessionString('current_duration', '00:05:00'));
        $this->seekPct   = $this->sessionInt('progress_pct', 30);
        $this->imgCover  = (string)($_SESSION['current_art'] ?? $this->asset('/Image/res-pink/album-goksel.png'));
        $this->imgStar   = $this->asset('/Image/res-pink/star-filled.png');
        $this->iconPlay    = $this->asset('/Image/res-pink/play.png');
        $this->iconMusic   = $this->asset('/Image/res-pink/music.png');
        $this->iconCd      = $this->asset('/Image/res-pink/cd-ico.png');
        $this->iconMic     = $this->asset('/Image/res-pink/mic-1.png');
        $this->iconStar    = $this->asset('/Image/res-pink/star.png');
        $this->iconBitrate = $this->asset('/Image/res-pink/bit-rate.png');
        $this->iconTimer   = $this->asset('/Image/res-pink/timer.png');
    }

    public function key(): string
    {
        return 'player-info';
    }
}

