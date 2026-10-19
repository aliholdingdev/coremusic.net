<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Support;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;

/**
 * BehaviorStubComponent — AbstractComponent karakterizasyon testleri için stub.
 *
 * partial() (parent'ta FINAL DEĞİL) override edilerek require hedefi test
 * fixture'ına yönlendirilir; böylece production pages/components/ dizinine
 * DOKUNULMAZ.
 *
 * exposedAsset/exposedH — parent'taki `final protected` metodları testten
 * çağrılabilir kılar (karakterizasyon: davranış değişirse test kırılır).
 */
final class BehaviorStubComponent extends AbstractComponent
{
    public function __construct(
        HomeLayoutVariant $variant,
        private readonly string $partialPath,
    ) {
        parent::__construct($variant);
    }

    public function key(): string
    {
        return 'behavior-stub';
    }

    /** Parent partial() yol çözümlemesini test fixture'ına çevirir. */
    protected function partial(): string
    {
        return $this->partialPath;
    }

    public function exposedAsset(string $path): string
    {
        return $this->asset($path);
    }

    public function exposedH(string $value): string
    {
        return $this->h($value);
    }
}
