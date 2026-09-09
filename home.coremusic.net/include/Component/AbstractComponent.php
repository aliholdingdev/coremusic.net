<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * AbstractComponent — template-method taban (v2.0.0)
 *
 * Tasarım: bileşenler IMMUTABLE'dır. Alt sınıf; readonly property'lerini
 * kendi constructor'ında tanımlar/doldurur, partial bu property'lere
 * $this->prop üzerinden TİP GÜVENLİ erişir (extract() yoktur).
 *
 * Partial kapsamı: render() içinde require edildiği için $this (bileşen)
 * görünürdür; escape $this->h(), asset $this->asset() ile yapılır.
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

    /** Session string okuma (salt okuma — yazma HomeSessionManager'a aittir). */
    final protected function sessionString(string $key, string $default): string
    {
        return (string)($_SESSION[$key] ?? $default);
    }

    /** Session int okuma. */
    final protected function sessionInt(string $key, int $default): int
    {
        return (int)($_SESSION[$key] ?? $default);
    }
}
