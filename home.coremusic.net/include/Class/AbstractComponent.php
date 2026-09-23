<?php declare(strict_types=1);

namespace CoreMusic\Home\Class;

use CoreMusic\Home\Interfaces\ComponentInterface;

/**
 * AbstractComponent — template-method taban (v3.0.0)
 *
 * Figma pixel-perfect measurement helpers eklenmiştir.
 * Tüm bileşenler Figma API'dan çekilen gerçek boyutları kullanır.
 *
 * Figma measurements (API extraction):
 *   1024: node-id=1639-10160 → 1024×600
 *   1920: node-id=2831-13747 → 1920×1080
 *
 * Alt sınıf sözleşmesi:
 *   1. key()                          — registry + partial adı
 *   2. __construct(HomeLayoutVariant) — parent::__construct($variant) ÇAĞIRILMAK ZORUNDA
 */
abstract class AbstractComponent implements ComponentInterface
{
    public function __construct(
        public readonly HomeLayoutVariant $variant,
    ) {
    }

    /** View partial yolu — key() üzerinden türetilir. */
    protected function partial(): string
    {
        return dirname(__DIR__, 2) . '/pages/components/' . $this->key() . '.php';
    }

    final public function render(): string
    {
        ob_start();
        try {
            require $this->partial();
            return (string)ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    final public function display(): void
    {
        echo $this->render();
    }

    /** HTML escape. */
    final protected function h(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** ASSETS_URL önekli varlık adresi. */
    final protected function asset(string $path): string
    {
        return (defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net') . $path;
    }

    /** Session string okuma (salt okuma). */
    final protected function sessionString(string $key, string $default): string
    {
        return (string)($_SESSION[$key] ?? $default);
    }

    /** Session int okuma. */
    final protected function sessionInt(string $key, int $default): int
    {
        return (int)($_SESSION[$key] ?? $default);
    }

    /* ═══════════════════════════════════════════════════════════════
       Figma Pixel-Perfect Measurement Helpers
       Tüm boyutlar Figma API'dan extract edilmiştir.
       ═══════════════════════════════════════════════════════════════ */

    /** Wide (1920+) layout mu? */
    final protected function isWide(): bool
    {
        return $this->variant->isWide();
    }

    /**
     * Cihaz bazlı boyut döndür
     * @param string $key — Token adı (ör: 'player', 'cover', 'footer')
     * @return array{width: int|float, height: int|float}
     */

    /**
     * CSS class döndür (tier'a göre)
     * @param string $base — Base class adı (ör: 'player-info')
     * @return string
     */
    final protected function tierClass(string $base): string
    {
        return $this->isWide() ? "{$base}--wide" : "{$base}--embedded";
    }
}
