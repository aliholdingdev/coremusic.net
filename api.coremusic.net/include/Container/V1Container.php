<?php declare(strict_types=1);

namespace CoreMusic\Api\Container;

/**
 * V1Container — API v1 Container.
 *
 * Temel API v1 bağımlılık grafiği. ApiContainerBase'den inherit eder.
 * v2 için referans implementasyondur.
 *
 * NOT (F21): `final` kaldırıldı — V2Container bunu extend ediyor ve final
 * sınıfı extend etmek PHP'de fatal'dir (V2Container.php:19). Düzeltme öncesi
 * `new V2Container()` → exit 255 + HTML fatal (JSON-only sözleşmesi ihlali).
 */
class V1Container extends ApiContainerBase
{
    protected static function getVersion(): string
    {
        return 'v1';
    }
}