<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Service\IHistoryService;

final class HistoryController extends AbstractController
{
    public function __construct(
        private readonly IHistoryService $service,
    ) {}

    /**
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     */
    public function handle(array $route, array $request): array
    {
        $action = (string) ($route['action'] ?? '');
        $userId = $this->authUserId($request);

        if ($userId === null) {
            return $this->error('UNAUTHORIZED', 'Authentication required', 401);
        }

        return match ($action) {
            'recent' => $this->recent($userId),
            'list'   => $this->listHistory($userId),
            'record' => $this->record($userId, $request),
            default  => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
        };
    }

    /** GET /api/v1/music/recent — son dinlenenler */
    private function recent(string $userId): array
    {
        $limit = min(50, max(1, (int) ($_GET['limit'] ?? 10)));
        return $this->success([
            'items' => $this->service->getRecentTracks($userId, $limit)
        ]);
    }

    /** GET /api/v1/music/history — sayfalı geçmiş (Adım 09: from/to + gün gruplu) */
    private function listHistory(string $userId): array
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));
        $from    = $this->validDate($_GET['from'] ?? null);
        $to      = $this->validDate($_GET['to'] ?? null);

        return $this->success($this->service->getHistory($userId, $page, $perPage, $from, $to));
    }

    /**
     * Adım 09 — tarih parametresi kapısı: yalnız `Y-m-d` kabul edilir.
     * Gelecek tarih sunucu saatiyle kırpılır (trust client YASAK — §9 edge case).
     */
    private function validDate(mixed $value): ?string
    {
        if (!is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        $today = date('Y-m-d');
        if ($value > $today) {
            return $today;
        }

        return $value;
    }

    /** POST /api/v1/music/history — dinleme kaydet */
    private function record(string $userId, array $request): array
    {
        $body = $this->body($request);
        $musicId = (string) ($body['music_id'] ?? '');
        $duration = (int) ($body['duration_sec'] ?? 0);
        $device = (string) ($body['device_type'] ?? 'desktop');

        if ($musicId === '') {
            return $this->error('VALIDATION_ERROR', 'music_id is required', 422);
        }

        $recorded = $this->service->recordListening($userId, $musicId, $duration, $device);

        return $this->success(['recorded' => $recorded]);
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
