<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Service\IPlaylistService;

/**
 * PlaylistController — /v1/playlist/* uçlarını IPlaylistService'e bağlar (Faz 3 iskeleti).
 *
 * Tüm uçlar private: kimlik AuthenticationMiddleware'den gelir (`_auth_user.id`).
 * implemented:false route'lar sayesinde Faz 4'e kadar instantiate EDİLMEZ.
 *
 * Sözleşme (Faz 1b — DEĞİŞTİRİLEMEZ):
 *   başarı → {success:true, data:{...}}   hata → {error:{code,message}}
 */
final class PlaylistController extends AbstractController
{
    public function __construct(
        private readonly IPlaylistService $service,
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

        $body = $this->body($request);

        return match ($action) {
            'index'    => $this->success(['items' => $this->service->listForUser($userId)]),
            'create'   => $this->create($userId, $body),
            'show'     => $this->show($userId),
            'addTrack' => $this->addTrack($userId, $body),
            default    => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
        };
    }

    // ─── Actions ───

    /** POST /v1/playlist — {name, description?, is_public?}. */
    private function create(string $userId, array $body): array
    {
        $name = trim((string) ($body['name'] ?? ''));
        if ($name === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['name' => 'name is required'],
            ]);
        }

        return $this->success($this->service->create($userId, $body));
    }

    /** GET /v1/playlist/{id} → action 'show'. */
    private function show(string $userId): array
    {
        $playlistId = (string) ($_GET['id'] ?? '');
        $playlist   = $playlistId !== '' ? $this->service->getWithTracks($playlistId, $userId) : null;

        if ($playlist === null) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı.', 404);
        }

        return $this->success(['playlist' => $playlist]);
    }

    /** POST /v1/playlist/tracks — {playlist_id, track_id}. */
    private function addTrack(string $userId, array $body): array
    {
        $playlistId = (string) ($body['playlist_id'] ?? '');
        $trackId    = (string) ($body['track_id'] ?? '');
        if ($playlistId === '' || $trackId === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => [
                    'playlist_id' => $playlistId === '' ? 'playlist_id is required' : '',
                    'track_id'    => $trackId === '' ? 'track_id is required' : '',
                ],
            ]);
        }

        // Service null döndürür: liste yok veya sahiplik ihlali → 404 (enumerate koruma).
        $relation = $this->service->addTrack($playlistId, $trackId, $userId);
        if ($relation === null) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı veya erişim yok.', 404);
        }

        return $this->success($relation);
    }

    // ─── Identity (defansif — middleware zaten 401 döndürür) ───

    private function authUserId(array $request): ?string
    {
        $id = $request['_auth_user']['id'] ?? null;

        return is_string($id) && $id !== '' ? $id : null;
    }
}
