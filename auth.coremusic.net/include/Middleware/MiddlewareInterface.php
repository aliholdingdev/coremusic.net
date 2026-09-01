<?php declare(strict_types=1);

namespace CoreMusic\Auth\Middleware;

/**
 * Middleware Interface — PSR-15 uyumlu middleware contract.
 *
 * Tüm middleware'ler bu interface'i implemente eder.
 */
interface MiddlewareInterface
{
    /**
     * İsteği işle.
     *
     * @param array $request İstek verisi
     * @param callable $next Bir sonraki middleware'i çağıran callable
     * @return array Yanıt
     */
    public function process(array $request, callable $next): array;
}
