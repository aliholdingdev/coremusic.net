<?php declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * Sayaç backend'i (cache) yazamadığında fırlatılır.
 * PRD-SEC-004: hata durumunda istek fail-open geçmez; çağıran katman reddeder.
 */
final class RateLimiterUnavailableException extends \RuntimeException
{
}
