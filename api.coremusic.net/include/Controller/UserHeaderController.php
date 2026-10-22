<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Service\IUserHeaderService;

/**
 * UserHeaderController — GET /v1/user/header ucu (Adım 05).
 *
 * Katman ihlali düzeltmesi: 4 repository bağımlılığı IUserHeaderService'e
 * taşındı (Controller→Service→Repository tek yön — Adım 05 §4.1). Controller
 * yalnızca HTTP: kimlik al, servisi çağır, sarmalayıcı döndür.
 */
final class UserHeaderController extends AbstractController
{
    public function __construct(
        private readonly IUserHeaderService $service,
    ) {}

    /**
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     */
    public function handle(array $route, array $request): array
    {
        $summary = $this->service->getHeaderSummary($this->authUserId($request));

        return $this->success($summary);
    }

    private function authUserId(array $request): ?string
    {
        $id = $request['_auth_user']['id'] ?? null;
        if (!is_string($id) || $id === '') {
            return null;
        }

        return $id;
    }
}
