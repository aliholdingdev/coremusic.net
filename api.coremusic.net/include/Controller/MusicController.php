<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Service\IMusicService;

/**
 * MusicController — /v1/music/* uçlarını IMusicService'e bağlar (Faz 3 iskeleti).
 *
 * Faz 4 yuvası: service implementasyonu + route implemented:true bağlaması.
 * implemented:false route'lar sayesinde bu sınıf Faz 4'e kadar instantiate EDİLMEZ;
 * sahte veri ÜRETİLMEZ, placeholder yanıt ÜRETİLMEZ.
 *
 * Sözleşme (Faz 1b — DEĞİŞTİRİLEMEZ):
 *   başarı → {success:true, data:{...}}   hata → {error:{code,message}}
 */
final class MusicController extends AbstractController
{
    public function __construct(
        private readonly IMusicService $service,
    ) {}

    /**
     * @param array<string, mixed> $route  RouteTable kaydı (`action` alanı)
     * @param array<string, mixed> $request pipeline isteği
     */
    public function handle(array $route, array $request): array
    {
        $action = (string) ($route['action'] ?? '');

        return match ($action) {
            'tracks' => $this->tracks(),
            'search' => $this->search(),
            'show'   => $this->show(),
            default  => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
        };
        // Servis/Beklenmeyen istisnalar Gateway'e bırakılır: 500 INTERNAL_ERROR.
    }

    // ─── Actions ───

    /** GET /v1/music/tracks — sayfalı liste (?page=1&per_page=20). */
    private function tracks(): array
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));

        return $this->success($this->service->listTracks($page, $perPage));
    }

    /** GET /v1/music/search — hibrit arama (?q=...&limit=20). */
    private function search(): array
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        if ($query === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['q' => 'q is required'],
            ]);
        }
        $limit = min(50, max(1, (int) ($_GET['limit'] ?? 20)));

        return $this->success($this->service->searchTracks($query, $limit));
    }

    /** GET /v1/music/tracks/{id} → action 'show'. */
    private function show(): array
    {
        $trackId = (string) ($_GET['id'] ?? '');
        $track   = $trackId !== '' ? $this->service->getTrack($trackId) : null;

        if ($track === null) {
            return $this->error('NOT_FOUND', 'Şarkı bulunamadı.', 404);
        }

        return $this->success(['track' => $track]);
    }
}
