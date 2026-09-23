<?php declare(strict_types=1);

namespace CoreMusic\Home\Interfaces;

/**
 * ComponentInterface — Home bileşen sözleşmesi (v2.0.0)
 *
 * Tüm bileşenler bu sözleşmeyi uygular; ComponentLoader yalnızca bu
 * arayüzü tanır. key() hem registry anahtarı hem view partial dosya
 * adıdır: pages/components/{key}.php
 */
interface ComponentInterface
{
    /** Bileşen anahtarı — registry + partial dosya adı. */
    public function key(): string;

    /** Bileşen HTML çıktısını üretir. */
    public function render(): string;
}
