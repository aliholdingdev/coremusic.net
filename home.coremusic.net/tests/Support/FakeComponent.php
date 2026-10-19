<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Support;

use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Interfaces\ComponentInterface;

/**
 * FakeComponent — ComponentLoader testleri için gerçek sözleşmeli test double.
 *
 * Render/tpl bağımlılığı yok; yalnızca interface sözleşmesini (key + render)
 * ve loader'ın constructor sözleşmesini (__construct(HomeLayoutVariant)) uygular.
 */
final class FakeComponent implements ComponentInterface
{
    public function __construct(
        public readonly HomeLayoutVariant $variant = HomeLayoutVariant::Embedded,
    ) {
    }

    public function key(): string
    {
        return 'fake-component';
    }

    public function render(): string
    {
        return '<div class="fake-component">' . $this->variant->name . '</div>';
    }
}
