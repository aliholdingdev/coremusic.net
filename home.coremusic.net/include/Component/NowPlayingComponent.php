<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * NowPlayingComponent — Şu An Çalan (v2.0.0)
 * PNG: home-1920 sol üst (etiket:değer satırları) / home-1024 sol 42% (başlık+seek)
 * Varyant: wide → etiket:değer + yıldız rating; embedded → başlık + tek satır süre/seek
 */
final class NowPlayingComponent extends AbstractComponent
{
    public readonly string $song;
    public readonly string $album;
    public readonly string $artist;
    public readonly string $bitrate;
    public readonly string $elapsed;
    public readonly string $duration;
    public readonly int $seekPct;
    public readonly string $imgNowArt;
    public readonly string $imgStar;

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
        $this->imgNowArt = (string)($_SESSION['current_art'] ?? $this->asset('/Image/res-pink/album-goksel.png'));
        $this->imgStar   = $this->asset('/Image/res-pink/star-filled.png');
    }

    public function key(): string
    {
        return 'now-playing';
    }
}
