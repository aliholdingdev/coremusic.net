<?php
declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use CoreMusic\Api\Controller\BillingController;
use CoreMusic\Api\Service\IBillingService;
use PHPUnit\Framework\TestCase;

/**
 * BillingController — own-data + sayfalama + hata haritası (stub IBillingService).
 *
 * Zarf sözleşmesi (openapi.yaml BillingPlanResponse / BillingInvoicesResponse):
 *   başarı → ApiResponse::ok / ApiResponse::paginated = {data, meta}
 *   hata   → {error:{code,message[,fields]}} (UNAUTHORIZED 401 ·
 *            VALIDATION_ERROR 422 · NOT_FOUND 404)
 * Auth'sız istek service'e HİÇ ulaşmamalı (fail-closed).
 */
final class BillingControllerTest extends TestCase
{
    private const USER = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    /** @var array<string, mixed> */
    private array $getBackup = [];

    protected function setUp(): void
    {
        $this->getBackup = $_GET;
        $_GET = [];
        http_response_code(200);
    }

    protected function tearDown(): void
    {
        $_GET = $this->getBackup;
        http_response_code(200);
    }

    /** @param array<string, mixed> $overrides */
    private function service(array $overrides = []): IBillingService
    {
        return new class ($overrides) implements IBillingService {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function plan(string $userId): array
            {
                return $this->config['plan'] ?? [
                    'has_subscription' => false,
                    'subscription'     => null,
                    'plan'             => null,
                ];
            }

            public function invoices(string $userId, int $page, int $pageSize): array
            {
                if (array_key_exists('invoices', $this->config)) {
                    return $this->config['invoices'];
                }

                return ['items' => [], 'total' => 0];
            }
        };
    }

    /** @param array<string, mixed> $body */
    private function handle(IBillingService $service, string $action, ?string $userId = self::USER, array $body = []): array
    {
        $request = ['body' => $body];
        if ($userId !== null) {
            $request['_auth_user'] = ['id' => $userId, 'authenticated' => true, 'method' => 'session'];
        }

        return (new BillingController($service))->handle(['action' => $action], $request);
    }

    public function testPlanWithoutAuthReturns401(): void
    {
        $result = $this->handle($this->service(), 'plan', null);

        $this->assertSame('UNAUTHORIZED', $result['error']['code'] ?? null);
        $this->assertSame(401, http_response_code());
    }

    public function testInvoicesWithoutAuthReturns401(): void
    {
        $result = $this->handle($this->service(), 'invoices', null);

        $this->assertSame('UNAUTHORIZED', $result['error']['code'] ?? null);
        $this->assertSame(401, http_response_code());
    }

    /**
     * Abonelik yoksa service uydurmaz → {has_subscription:false, null, null};
     * başarı zarfı {data, meta} — AbstractController {success,data} DEĞİL.
     */
    public function testPlanReturnsFrameworkEnvelopeWithoutSubscription(): void
    {
        $result = $this->handle($this->service(), 'plan');

        $this->assertFalse((bool) ($result['data']['has_subscription'] ?? true));
        $this->assertArrayHasKey('subscription', $result['data']);
        $this->assertArrayHasKey('plan', $result['data']);
        $this->assertNull($result['data']['subscription'], 'anahtar VAR, değer null olmalı (??/coalesce burada tuzağı)');
        $this->assertNull($result['data']['plan']);
        $this->assertArrayHasKey('meta', $result, 'ApiResponse::ok zarfı meta taşır');
        $this->assertArrayNotHasKey('success', $result, 'success anahtarı bu uçta YOK (zarf {data,meta})');
        $this->assertSame(200, http_response_code());
    }

    public function testPlanReturnsSubscriptionAndPlanShape(): void
    {
        $plan = [
            'has_subscription' => true,
            'subscription'     => [
                'subscription_id'      => '01900000-0000-7000-8000-000000000001',
                'status'               => 'active',
                'current_period_start' => '2026-10-01',
                'current_period_end'   => '2026-11-01',
                'cancel_at_period_end' => false,
                'canceled_at'          => null,
                'trial_end'            => null,
            ],
            'plan'             => [
                'plan_id'                => '01900000-0000-7000-8000-000000000002',
                'plan_code'              => 'premium_monthly',
                'name'                   => 'Premium',
                'tier'                   => 'premium',
                'billing_interval'       => 'monthly',
                'price_amount'           => '49.90',
                'currency'               => 'TRY',
                'max_concurrent_streams' => 3,
                'audio_quality_tier'     => 'lossless_flac',
            ],
        ];

        $result = $this->handle($this->service(['plan' => $plan]), 'plan');

        $this->assertTrue($result['data']['has_subscription']);
        $this->assertSame('premium_monthly', $result['data']['plan']['plan_code']);
        $this->assertSame('49.90', $result['data']['plan']['price_amount'], 'decimal string korunur');
        $this->assertSame('active', $result['data']['subscription']['status']);
        $this->assertSame(200, http_response_code());
    }

    public function testInvoicesReturnsEmptyPaginatedEnvelope(): void
    {
        $result = $this->handle($this->service(), 'invoices');

        $this->assertSame([], $result['data'], 'boş liste data:[] (null DEĞİL)');
        $this->assertSame(
            ['page' => 1, 'pageSize' => 20, 'totalItems' => 0, 'totalPages' => 0],
            $result['meta']['pagination'] ?? null,
            'varsayılan page=1 pageSize=20, totalPages=ceil(0/20)=0'
        );
        $this->assertArrayHasKey('timestamp', $result['meta'] ?? []);
        $this->assertSame('1.0.0', $result['meta']['version'] ?? null);
        $this->assertSame(200, http_response_code());
    }

    public function testInvoicesReturnsPaginatedItems(): void
    {
        $_GET = ['page' => '2', 'pageSize' => '1'];

        $invoice = [
            'invoice_id'     => '01900000-0000-7000-8000-000000000003',
            'invoice_number' => 'INV-2026-0003',
            'status'         => 'paid',
            'total_amount'   => '59.88',
            'currency'       => 'TRY',
        ];
        $result = $this->handle($this->service([
            'invoices' => ['items' => [$invoice], 'total' => 3],
        ]), 'invoices');

        $this->assertSame([$invoice], $result['data']);
        $this->assertSame(
            ['page' => 2, 'pageSize' => 1, 'totalItems' => 3, 'totalPages' => 3],
            $result['meta']['pagination'] ?? null,
            'totalPages = ceil(3/1) = 3'
        );
        $this->assertSame(200, http_response_code());
    }

    public function testInvoicesInvalidPageReturns422WithFields(): void
    {
        $_GET = ['page' => '0'];

        $result = $this->handle($this->service(), 'invoices');

        $this->assertSame('VALIDATION_ERROR', $result['error']['code'] ?? null);
        $this->assertSame(422, http_response_code());
        $this->assertArrayHasKey('page', (array) ($result['error']['fields'] ?? []));
    }

    public function testInvoicesPageSizeAboveCapReturns422(): void
    {
        $_GET = ['pageSize' => '1000'];

        $result = $this->handle($this->service(), 'invoices');

        $this->assertSame('VALIDATION_ERROR', $result['error']['code'] ?? null);
        $this->assertSame(422, http_response_code());
        $this->assertArrayHasKey('pageSize', (array) ($result['error']['fields'] ?? []));
    }

    /**
     * Route kuralı (type:int) gateway'de yakalar; controller katmanı da
     * doğrudan çağrıda savunmalıdır (Validator min/max burada DEĞİL —
     * controller aralık kontrolü yapar).
     */
    public function testInvoicesNonNumericPageReturns422WhenCalledDirectly(): void
    {
        $_GET = ['page' => 'abc'];

        $result = $this->handle($this->service(), 'invoices');

        $this->assertSame('VALIDATION_ERROR', $result['error']['code'] ?? null);
        $this->assertSame(422, http_response_code());
        $this->assertArrayHasKey('page', (array) ($result['error']['fields'] ?? []));
    }

    public function testUnknownActionReturns404(): void
    {
        $result = $this->handle($this->service(), 'refund');

        $this->assertSame('NOT_FOUND', $result['error']['code'] ?? null);
        $this->assertSame(404, http_response_code());
    }

    public function testBillingActionsReturn401WithoutAuth(): void
    {
        foreach (['plan', 'invoices'] as $action) {
            $result = $this->handle($this->service(), $action, null);

            $this->assertSame('UNAUTHORIZED', $result['error']['code'] ?? null, "{$action} auth'sız 401");
            $this->assertSame(401, http_response_code(), "{$action} auth'sız 401 status");
        }
    }
}
