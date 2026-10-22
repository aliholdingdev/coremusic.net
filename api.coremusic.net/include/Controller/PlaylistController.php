<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Service\IPlaylistService;

/**
 * PlaylistController — /v1/playlist/* uçlarını IPlaylistService'e bağlar
 * (Faz 4 + Adım 07 CRUD: detay · güncelle · sil · şarkı çıkar · sırala).
 *
 * Tüm uçlar private: kimlik AuthenticationMiddleware'den gelir (`_auth_user.id`).
 *
 * Route eşleşmesi exact-path (RouteTable) → id'ler query'de okunur:
 *   GET    /v1/playlist/detail?id=       · DELETE /v1/playlist?id=
 *   DELETE /v1/playlist/tracks?playlist_id=&track_id=
 *   PUT    /v1/playlist {id,...} · PUT /v1/playlist/order {playlist_id, track_ids[]}
 *
 * Sözleşme (Faz 1b — DEĞİŞTİRİLEMEZ):
 *   başarı → {success:true, data:{...}}   hata → {error:{code,message}}
 *   istisna: POST create → 201 · DELETE silme → gövdesiz 204 (halt)
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
            'index'      => $this->success(['items' => $this->service->listForUser($userId)]),
            'create'     => $this->create($userId, $body),
            'show'       => $this->show($userId),
            'update'     => $this->update($userId, $body),
            'delete'     => $this->delete($userId),
            'addTrack'   => $this->addTrack($userId, $body),
            'removeTrack' => $this->removeTrack($userId),
            'reorder'    => $this->reorder($userId, $body),
            default      => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
        };
    }

    // ─── Actions ───

    /** POST /v1/playlist — {name, description?, is_public?} → 201. */
    private function create(string $userId, array $body): array
    {
        $name = trim((string) ($body['name'] ?? ''));
        if ($name === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['name' => 'name is required'],
            ]);
        }

        return $this->success($this->service->create($userId, $body), 201);
    }

    /** GET /v1/playlist/detail?id={id} → action 'show'. */
    private function show(string $userId): array
    {
        $playlistId = (string) ($_GET['id'] ?? '');
        $playlist   = $playlistId !== '' ? $this->service->getWithTracks($playlistId, $userId) : null;

        if ($playlist === null) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı.', 404);
        }

        return $this->success(['playlist' => $playlist]);
    }

    /** PUT /v1/playlist — {id, name?, description?, visibility?} → 200. */
    private function update(string $userId, array $body): array
    {
        $playlistId = (string) ($body['id'] ?? '');
        if ($playlistId === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['id' => 'id is required'],
            ]);
        }

        $mutable = ['name', 'description', 'visibility'];
        $present = array_values(array_intersect($mutable, array_keys($body)));
        if ($present === []) {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['name' => 'at least one of name, description, visibility is required'],
            ]);
        }

        $fields = array_intersect_key($body, array_flip($mutable));
        if (isset($fields['visibility'])
            && !in_array($fields['visibility'], ['public', 'private', 'unlisted'], true)
        ) {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['visibility' => 'visibility must be one of: public, private, unlisted'],
            ]);
        }

        if (isset($fields['name']) && trim((string) $fields['name']) === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['name' => 'name must not be empty'],
            ]);
        }

        $playlist = $this->service->update($playlistId, $fields, $userId);
        if ($playlist === null) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı veya erişim yok.', 404);
        }

        return $this->success(['playlist' => $playlist]);
    }

    /** DELETE /v1/playlist?id={id} → gövdesiz 204 (soft-delete). */
    private function delete(string $userId): array
    {
        $playlistId = (string) ($_GET['id'] ?? '');
        $deleted    = $playlistId !== '' && $this->service->delete($playlistId, $userId);

        if (!$deleted) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı veya erişim yok.', 404);
        }

        // 204 gövdesiz (RFC 9110 §15.3.5) — index.php halt yolu boş gövde basar.
        return ['halt' => true, 'httpStatus' => 204, 'body' => ''];
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

    /** DELETE /v1/playlist/tracks?playlist_id={id}&track_id={id} → 200. */
    private function removeTrack(string $userId): array
    {
        $playlistId = (string) ($_GET['playlist_id'] ?? '');
        $trackId    = (string) ($_GET['track_id'] ?? '');

        if ($playlistId === '' || $trackId === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => [
                    'playlist_id' => $playlistId === '' ? 'playlist_id is required' : '',
                    'track_id'    => $trackId === '' ? 'track_id is required' : '',
                ],
            ]);
        }

        if (!$this->service->removeTrack($playlistId, $trackId, $userId)) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı veya erişim yok.', 404);
        }

        return $this->success(['playlist_id' => $playlistId, 'track_id' => $trackId, 'removed' => true]);
    }

    /** PUT /v1/playlist/order — {playlist_id, track_ids[]} → 200 (kalıcı position). */
    private function reorder(string $userId, array $body): array
    {
        $playlistId = (string) ($body['playlist_id'] ?? '');
        $trackIds   = $body['track_ids'] ?? null;

        if ($playlistId === '' || !is_array($trackIds)) {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => [
                    'playlist_id' => $playlistId === '' ? 'playlist_id is required' : '',
                    'track_ids'   => is_array($trackIds) ? '' : 'track_ids must be an array',
                ],
            ]);
        }

        $fields = [];
        foreach (array_values($trackIds) as $index => $trackId) {
            if (!is_string($trackId) || preg_match('/^[a-f0-9]{32}$/i', $trackId) !== 1) {
                $fields["track_ids.{$index}"] = 'must be a 32-char hex music id';
            }
        }
        if ($fields !== []) {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, ['fields' => $fields]);
        }

        $result = $this->service->reorder(
            $playlistId,
            array_map('strtolower', array_values($trackIds)),
            $userId
        );

        if ($result === null) {
            return $this->error('NOT_FOUND', 'Oynatma listesi bulunamadı veya erişim yok.', 404);
        }

        return $this->success($result);
    }

    // ─── Identity (defansif — middleware zaten 401 döndürür) ───

    private function authUserId(array $request): ?string
    {
        $id = $request['_auth_user']['id'] ?? null;

        return is_string($id) && $id !== '' ? $id : null;
    }
}
