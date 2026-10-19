<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Theme\ThemeManager;

/**
 * WelcomeBannerComponent — Wide/4K üst satır orta sütun "Hoş Geldin" kartı
 *
 * Figma SSOT: node 2831:13747 (1920 Home) → "Banner Div" (id 2849:21492, 491×184)
 *   RECTANGLE, cornerRadius 8, DROP_SHADOW 2/2/4 @0.8 — içerik Figma'da yok;
 *   içerik kaynağı = kullanıcının verdiği referans banner görseli (v4.0.0).
 *
 * Yerleşim: Player Info (sol) ile Widget Grid (sağ) arasında orta sütun
 * (home.php → home-layout__top--wide grid-column: 2).
 *
 * v4.1.0 (2026-10-19) — Çift Cinsiyet Desteği (Kız / Erkek):
 *   select-gender / cm_gender session değerine göre dinamik tetiklenir:
 *   - Female (kız): Romantik gün batımı vadisi (bkimage1.png), taçlı prenses, gül kurusu halo avatar
 *   - Male (erkek): Gece dolunayı ve safir piyano (login-bg-male.png), papyonlu prens avatart, elektrik mavi halo
 *
 * View partial: pages/components/welcome-banner.php
 */
final class WelcomeBannerComponent extends AbstractComponent
{
    public readonly string $gender;
    public readonly string $greet;
    public readonly string $userName;
    public readonly string $tagline;
    public readonly string $avatar;
    public readonly string $heroImage;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        // Cinsiyet tespiti: Session (ThemeManager) + Test/Önizleme GET desteği
        $detected = ThemeManager::detect($_SESSION ?? []);
        if (isset($_GET['gender']) && is_string($_GET['gender'])) {
            $g = strtolower(trim($_GET['gender']));
            if (in_array($g, ['female', 'male', 'neutral'], true)) {
                $detected = $g;
            }
        } elseif (isset($_GET['cm_gender']) && is_string($_GET['cm_gender'])) {
            $g = strtolower(trim($_GET['cm_gender']));
            if (in_array($g, ['female', 'male', 'neutral'], true)) {
                $detected = $g;
            }
        }

        if ($detected === 'neutral') {
            $cookieGender = (string)($_COOKIE['cm_gender'] ?? '');
            if ($cookieGender === 'male' || $cookieGender === 'female') {
                $detected = $cookieGender;
            } else {
                $detected = 'female';
            }
        }

        $this->gender = ($detected === 'male') ? 'male' : 'female';
        $this->greet  = 'Hoş Geldin';

        $rawUser = (string)$this->sessionString('MM_DisplayName', '');
        if ($rawUser === '') {
            $rawUser = (string)$this->sessionString('MM_Username', '');
        }
        if ($rawUser === '' || $rawUser === 'test_user' || $rawUser === 'Misafir') {
            $rawUser = ($this->gender === 'male') ? 'Baran Ateş Kılıç ✦' : 'Işıl Peri Yıldız ♡';
        }
        $this->userName  = $this->h($rawUser);
        $this->tagline   = ($this->gender === 'male') ? 'Müziğin ritmine katıl!' : 'Müziğin dünyasına hoş geldin!';

        $customAvatar = (string)$this->sessionString('avatar_url', '');
        if ($customAvatar !== '') {
            $this->avatar = $customAvatar;
        } else {
            $this->avatar = ($this->gender === 'male')
                ? $this->asset('/Image/res-pink/erkek-gender-select.png')
                : $this->asset('/Image/profiles/default-avatar.png');
        }

        $this->heroImage = ($this->gender === 'male')
            ? $this->asset('/Image/background/login-bg-male.png')
            : $this->asset('/Image/background/bkimage1.png');
    }

    public function key(): string
    {
        return 'welcome-banner';
    }
}
