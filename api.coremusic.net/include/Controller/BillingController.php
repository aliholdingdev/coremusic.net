<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\ApiResponse;
use CoreMusic\Api\Service\IBillingService;

/**
 * BillingController — abonelik/plan özeti + fatura listesi (GET, own-data).
 *
 * Contract-first: config/routes.php action'ları (plan|invoices) → bu dispatch
 * (openapi.yaml /api/v1/billing/plan + /api/v1/billing/invoices ile hizalı).
 *
 * Zarf: framework {data,meta} — sayfalama meta.pagination gerektirdiği için
 * ApiResponse::ok / ApiResponse::paginated kullanılır (AbstractController
 * success({success,data}) DEĞİL; hata zarfı aynı: {error:{code,message[,fields]}}):
 *   UNAUTHORIZED 401 · VALIDATION_ERROR 422 (+error.fields) · NOT_FOUND 404.
 *
 * Doğrulama iki katman: gateway route kuralı (type:int — sayısal olmayan 422)
 * + controller aralık kontrolü (page ≥1, pageSize 1..100 — Validator min/max
 * string'te UZUNLUK ölçtüğü için route'a konmaz).
 *
 * Beklenmeyen istisna YAKLANMAZ (AuthControllerTest paritesi: generic
 * throwable → Gateway/index.php catch → 500; mesaj asla istemciye dönmez).
 */
final class BillingController extends AbstractController
{
    private const DEFAULT_PAGE_SIZE = 20;
    private const MAX_PAGE_SIZE     = 100;

    public function __construct(
        private readonly IBillingService $service,
    ) {}

    public function handle(array $route, array $request): array
    {
        $action = (string) ($route['action'] ?? '');

        $userId = $this->authUserId($request);
        if ($userId === null) {
            return $this->error('UNAUTHORIZED', 'Authentication required', 401);
        }

        return match ($action) {
            'plan'     => $this->plan($userId),
            'invoices' => $this->invoices($userId),
            default    => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
        };
    }

    /**
     * GET /billing/plan → 200 {data:{has_subscription,subscription,plan}, meta}.
     */
    private function plan(string $userId): array
    {
        return ApiResponse::ok($this->service->plan($userId));
    }

    /**
     * GET /billing/invoices → 200 {data:[...], meta.pagination} · 422 aralık dışı.
     */
    private function invoices(string $userId): array
    {
        $page     = 1;
        $pageSize = self::DEFAULT_PAGE_SIZE;
        $errors   = [];

        $rawPage = $_GET['page'] ?? '';
        if ($rawPage !== '') {
            $parsed = filter_var($rawPage, FILTER_VALIDATE_INT);
            if (!is_int($parsed) || $parsed < 1) {
                $errors['page'] = 'page must be a positive integer';
            } else {
                $page = $parsed;
            }
        }

        $rawSize = $_GET['pageSize'] ?? '';
        if ($rawSize !== '') {
            $parsed = filter_var($rawSize, FILTER_VALIDATE_INT);
            if (!is_int($parsed) || $parsed < 1 || $parsed > self::MAX_PAGE_SIZE) {
                $errors['pageSize'] = 'pageSize must be an integer between 1 and ' . self::MAX_PAGE_SIZE;
            } else {
                $pageSize = $parsed;
            }
        }

        if ($errors !== []) {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, ['fields' => $errors]);
        }

        $result = $this->service->invoices($userId, $page, $pageSize);

        return ApiResponse::paginated($result['items'], $page, $pageSize, $result['total']);
    }

    /**
     * @param array<string, mixed> $request
     */
    private function authUserId(array $request): ?string
    {
        $id = $request['_auth_user']['id'] ?? null;

        return is_string($id) && $id !== '' ? $id : null;
    }
}
