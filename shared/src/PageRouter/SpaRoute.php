<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class SpaRoute
{
    public function __construct(
        public readonly string  $page,
        public readonly bool    $requiresAuth       = true,
        public readonly string  $title              = '',
        public readonly ?string $requiredRole       = null,
        public readonly ?string $requiredPermission = null,
        public readonly bool    $cacheable          = true,
        public readonly array   $meta               = [],
        public readonly string  $path               = '',
        public readonly ?string $handler            = null,
        public readonly ?int    $cacheTtl           = null,
    ) {}
}
