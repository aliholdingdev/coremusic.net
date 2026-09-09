<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * WelcomeModalComponent — Welcome Modal (v2.0.0, PNG S02: qr-welcome-popup)
 *
 * Markup phone hariç tüm cihazlarda render edilir; GÖRÜNÜRLÜK JS'e aittir:
 * - welcome-modal.js: yalnızca embedded (RPi5 1024) + sessionStorage dismissed değilse açar
 * - device-layout-updater.js: embedded dışı cihazda inline display:none ile kapatır
 * PNG'de × close butonu YOK — kapanış: Başla butonu / Escape.
 */
final class WelcomeModalComponent extends AbstractComponent
{
    public readonly string $logoImg;
    public readonly string $username;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);
        $this->logoImg  = $this->asset('/Image/res-pink/logo/logo-img.png');
        $this->username = $this->h($this->sessionString('MM_Username', 'Bayram Ali'));
    }

    public function key(): string
    {
        return 'welcome-modal';
    }
}
