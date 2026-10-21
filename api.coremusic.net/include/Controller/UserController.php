<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Service\IUserService;

/**
 * UserController — /v1/user/* uçlarını IUserService'e bağlar (Faz 3 iskeleti).
 *
 * Tüm uçlar private: kimlik AuthenticationMiddleware'den gelir (`_auth_user.id`).
 * implemented:false route'lar sayesinde Faz 4'e kadar instantiate EDİLMEZ.
 *
 * Sözleşme (Faz 1b — DEĞİŞTİRİLEMEZ):
 *   başarı → {success:true, data:{...}}   hata → {error:{code,message}}
 */
final class UserController extends AbstractController
{
    public function __construct(
        private readonly IUserService $service,
    ) {}

    /**
     * @param array<string, mixed> $route  RouteTable kaydı (`action` alanı)
     * @param array<string, mixed> $request pipeline isteği
     */
    public function handle(array $route, array $request): array
    {
        $action = (string) ($route['action'] ?? '');
        $userId = $this->authUserId($request);
        if ($userId === null) {
            return $this->error('UNAUTHORIZED', 'Authentication required', 401);
        }

        return match ($action) {
            'profile'        => $this->profile($userId),
            'updateProfile'  => $this->updateProfile($userId, $this->body($request)),
            default          => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
        };
    }

    // ─── Actions ───

    /** GET /v1/user/profile. */
    private function profile(string $userId): array
    {
        $profile = $this->service->getProfile($userId);
        if ($profile === null) {
            return $this->error('NOT_FOUND', 'Profil bulunamadı.', 404);
        }

        return $this->success(['profile' => $profile]);
    }

    /** PUT /v1/user/profile — {display_name?, avatar_url?} (whitelist serviste). */
    private function updateProfile(string $userId, array $body): array
    {
        $profile = $this->service->updateProfile($userId, $body);
        if ($profile === null) {
            return $this->error('NOT_FOUND', 'Profil bulunamadı.', 404);
        }

        return $this->success(['profile' => $profile]);
    }

    // ─── Identity (defansif — middleware zaten 401 döndürür) ───

    private function authUserId(array $request): ?string
    {
        $id = $request['_auth_user']['id'] ?? null;

        return is_string($id) && $id !== '' ? $id : null;
    }
}
