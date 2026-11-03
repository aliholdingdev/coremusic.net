<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IBillingRepository;

/**
 * BillingService — coremusic_business iş katmanı (salt okuma).
 *
 * Şekil çevirisi: repository satırı → API sözleşmesi (snake_case);
 * openapi.yaml BillingPlanResponse / BillingInvoicesResponse ile hizalı.
 * Abonelik yoksa uydurulmaz: {has_subscription:false, subscription:null, plan:null}
 * (Zero-Hallucination — dump'taki FREE planı dayatılmaz).
 */
final class BillingService implements IBillingService
{
    public function __construct(
        private readonly IBillingRepository $repository,
    ) {}

    public function plan(string $userId): array
    {
        $row = $this->repository->findCurrentSubscriptionWithPlan($userId);

        if ($row === null) {
            return [
                'has_subscription' => false,
                'subscription'     => null,
                'plan'             => null,
            ];
        }

        return [
            'has_subscription' => true,
            'subscription'     => [
                'subscription_id'       => (string) ($row['subscription_id'] ?? ''),
                'status'                => (string) ($row['subscription_status'] ?? ''),
                'current_period_start'  => $row['current_period_start'] ?? null,
                'current_period_end'    => $row['current_period_end'] ?? null,
                'cancel_at_period_end'  => (bool) ($row['cancel_at_period_end'] ?? false),
                'canceled_at'           => $row['canceled_at'] ?? null,
                'trial_end'             => $row['trial_end'] ?? null,
            ],
            'plan'             => [
                'plan_id'                => (string) ($row['plan_id'] ?? ''),
                'plan_code'              => (string) ($row['plan_code'] ?? ''),
                'name'                   => (string) ($row['plan_name'] ?? ''),
                'tier'                   => (string) ($row['tier'] ?? ''),
                'billing_interval'       => (string) ($row['billing_interval'] ?? ''),
                // decimal(10,2) PDO'da string gelir → korunur (kuruş hassasiyeti).
                'price_amount'           => (string) ($row['price_amount'] ?? '0.00'),
                'currency'               => (string) ($row['currency'] ?? ''),
                'max_concurrent_streams' => (int) ($row['max_concurrent_streams'] ?? 0),
                'audio_quality_tier'     => (string) ($row['audio_quality_tier'] ?? ''),
            ],
        ];
    }

    public function invoices(string $userId, int $page, int $pageSize): array
    {
        $total = $this->repository->countInvoices($userId);
        $items = $total === 0
            ? []
            : $this->repository->findInvoices($userId, $pageSize, ($page - 1) * $pageSize);

        return ['items' => $items, 'total' => $total];
    }
}
