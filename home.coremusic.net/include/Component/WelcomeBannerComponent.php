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
 * v2.0.0: İçerik PNG mockup'ından (.ai/.png/home-1920/Linux - 1920 - Home.png,
 * Banner bölgesi origin (552,99)) numeric ölçümle kodlandı — statik/mock
 * metin R5 kuralına göredir: session'da MM_DisplayName / MM_Username varsa
 * kullanıcı adı gösterilir, yoksa "Misafir" placeholder'ı.
 *   eyebrow "Hoş Geldin" · quote · brand "Core Music" ·
 *   CTA "Keşfetmeye Başla" → /kesfet · 5 stat chip · trailing "+" ring
 *
 * Stat ikonları — mevcut asset (yeni dosya YOK, Guardrail #11):
 *   2.450 Toplam Şarkı → music.png · 156 Playlist → playlist1.png ·
 *   87 Albüm → cd-case.png · 42 Sanatçı → mic-1.png ·
 *   1.250 Sadık Dostlar → users.png
 *   (Mockup'taki person-bust / group glyph için repo'da birebir flat ikon
 *   yok — en yakın mevcut flat ikonlar seçildi.)
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
    public readonly string $quote;
    public readonly string $brand;
    public readonly string $ctaLabel;
    public readonly string $ctaHref;
    /** @var list<array{num: string, label: string, icon: string}> */
    public readonly array $stats;

    public function __construct(HomeLayoutVariant $variant)
    {
        parent::__construct($variant);

        $this->eyebrow  = 'Hoş Geldin';
        $this->userName = $this->h((string)(
            $_SESSION['MM_DisplayName']
            ?? $_SESSION['MM_Username']
            ?? 'Misafir'
        ));
        $this->quote    = '"Müzik, ruhun gıdasıdır; her nota, bir hatırayı canlandırır."';
        $this->brand    = 'Core Music';
        $this->ctaLabel = 'Keşfetmeye Başla';
        $this->ctaHref  = '/kesfet';

        $this->stats = [
            ['num' => '2.450', 'label' => 'Toplam Şarkı',  'icon' => (string)$this->asset('/Image/res-pink/music.png')],
            ['num' => '156',   'label' => 'Playlist',      'icon' => (string)$this->asset('/Image/res-pink/playlist1.png')],
            ['num' => '87',    'label' => 'Albüm',         'icon' => (string)$this->asset('/Image/res-pink/cd-case.png')],
            ['num' => '42',    'label' => 'Sanatçı',       'icon' => (string)$this->asset('/Image/res-pink/mic-1.png')],
            ['num' => '1.250', 'label' => 'Sadık Dostlar', 'icon' => (string)$this->asset('/Image/res-pink/users.png')],
        ];
    }

    public function key(): string
    {
        return 'welcome-banner';
    }
}
