<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * WelcomeBannerComponent — Hoş Geldin Banner (v2.0.0)
 * PNG: home-1920 top-center (bg + başlık + kullanıcı + tagline + CTA + 5 istatistik)
 */
final class WelcomeBannerComponent extends AbstractComponent
{
    public readonly string $imgBg;
    public readonly string $username;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);
        $this->imgBg    = $this->asset('/Image/background/login-bg-female.png');
        $this->username = $this->h($this->sessionString('MM_Username', 'Bayram Ali'));
    }

    public function key(): string
    {
        return 'welcome-banner';
    }
}
