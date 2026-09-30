<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;

/**
 * WelcomeBannerComponent — Wide/4K üst satır orta sütun "Hoş Geldin" kartı
 *
 * Figma SSOT: node 2831:13747 (1920 Home) → "Banner Div" (id 2849:21492, 491×184)
 *   RECTANGLE, cornerRadius 8, DROP_SHADOW 2/2/4 @0.8, fill: IMAGE
 *   (children YOK — Figma'da metin tanımı yok, bkz. aşağıdaki not)
 *
 * Yerleşim: Player Info (sol) ile Widget Grid (sağ) arasında orta sütun
 * (home.php → home-layout__top-center, grid-column: 2).
 *
 * NOT — KARIŞTIRMA: Bu component, sayfa ilk açıldığında açılan
 * "Welcome Modal" popup'ı (#welcomeModalOverlay, Figma "Welcome Div" id
 * 2831:10267) DEĞİLDİR ve onu değiştirmez. Bu, üst satırda her zaman
 * görünen statik bir karttır.
 *
 * "Banner Div" Figma'da salt bir RECTANGLE'dır; alt metin node'u yoktur.
 * R5 kuralı gereği statik/mock metin kullanılır: session'da MM_DisplayName
 * / MM_Username varsa kullanıcı adı gösterilir, yoksa "Misafir" placeholder'ı.
 *
 * Embedded (1024, node 1639:10160) altında bu node'un bir eşdeği
 * bulunamadı — component yalnızca WIDE/4K'da render edilir.
 *
 * View partial: pages/components/welcome-banner.php
 */
final class WelcomeBannerComponent extends AbstractComponent
{
    public readonly string $eyebrow;
    public readonly string $userName;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->eyebrow  = 'Hoş Geldin';
        $this->userName = $this->h((string)(
            $_SESSION['MM_DisplayName']
            ?? $_SESSION['MM_Username']
            ?? 'Misafir'
        ));
    }

    public function key(): string
    {
        return 'welcome-banner';
    }
}
