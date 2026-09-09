<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * HomeWidgetsComponent — Widget Grid 2×2 (v2.0.0)
 * PNG: home-1024 sağ 58% / home-1920 top-right (wide'da ek cluster sarmalayıcı)
 * İçerik: Hoparlör, Hava Durumu, Saat, EQ+slotlar, Kitaplığım, Uygulama kısayolları
 */
final class HomeWidgetsComponent extends AbstractComponent
{
    public readonly string $dateLabel;
    public readonly string $imgBt;
    public readonly string $imgSaat;
    public readonly string $imgDate;
    public readonly string $imgFolder;
    public readonly string $imgYoutube;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);
        $this->dateLabel  = $variant->isWide() ? '5 Haziran 2025' : '5 Haziran 2026';
        $this->imgBt      = $this->asset('/Image/res-pink/bluethoot-1-connected.png');
        $this->imgSaat    = $this->asset('/Image/res-pink/saat.png');
        $this->imgDate    = $this->asset('/Image/res-pink/date.png');
        $this->imgFolder  = $this->asset('/Image/res-pink/folder.png');
        $this->imgYoutube = $this->asset('/Image/res-pink/app/youtube.png');
    }

    public function key(): string
    {
        return 'home-widgets';
    }
}
