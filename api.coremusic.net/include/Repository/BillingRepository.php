<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;

/**
 * BillingRepository — coremusic_billing veri erişimi (IBillingRepository implementasyonu).
 *
 * Şema SSOT: .ai/.sql/mysql/coremusic_billing.sql (11 tablo, BINARY(16) UUIDv7 PK).
 * Bağlantı: ConnectionFactory/registry anahtarı DB_KEY = 'billing'
 * (ApiContainerBase::getBillingRegistry — mevcut DI kalıbı, yeni PDO yok).
 * ADR-002: prepared statement yalnız · SELECT * yok · UNHEX(:hex) hex-string
 * BINARY(16) bind · soft-delete is_deleted = 0. Yalnız OKUMA (GET uçları).
 */
final class BillingRepository implements IBillingRepository
{
    /** ConnectionFactory / DatabaseRegistry anahtarı. */
    public const DB_KEY = 'billing';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
    ) {}

    public function findCurrentSubscriptionWithPlan(string $userId): ?array
    {
        $rows = $this->db()->execute(
            'SELECT LOWER(HEX(us.id))         AS subscription_id,
                    us.status                 AS subscription_status,
                    us.current_period_start   AS current_period_start,
                    us.current_period_end     AS current_period_end,
                    us.cancel_at_period_end   AS cancel_at_period_end,
                    us.canceled_at            AS canceled_at,
                    us.trial_end              AS trial_end,
                    LOWER(HEX(sp.id))         AS plan_id,
                    sp.plan_code              AS plan_code,
                    sp.name                   AS plan_name,
                    sp.tier                   AS tier,
                    sp.billing_interval       AS billing_interval,
                    sp.price_amount           AS price_amount,
                    sp.currency               AS currency,
                    sp.max_concurrent_streams AS max_concurrent_streams,
                    sp.audio_quality_tier     AS audio_quality_tier
               FROM user_subscriptions us
               JOIN subscription_plans sp ON sp.id = us.plan_id
              WHERE us.user_id = UNHEX(:uid)
                AND us.is_deleted = 0
                AND sp.is_deleted = 0
              ORDER BY us.created_at DESC, us.id DESC
              LIMIT 1',
            ['uid' => $userId]
        );

        return $rows === [] ? null : $rows[0];
    }

    public function countInvoices(string $userId): int
    {
        $rows = $this->db()->execute(
            'SELECT COUNT(*) AS total
               FROM invoices
              WHERE user_id = UNHEX(:uid)
                AND is_deleted = 0',
            ['uid' => $userId]
        );

        return (int) ($rows[0]['total'] ?? 0);
    }

    public function findInvoices(string $userId, int $limit, int $offset): array
    {
        // LIMIT/OFFSET: controller'da tamsayı aralık doğrulaması (1..100 / ≥0)
        // yapıldıktan sonra (int) ile gömülür — prepared parametre olarak
        // bağlanamaz (aynı desen: PlaylistRepository::findTracks LIMIT).
        return $this->db()->execute(
            'SELECT LOWER(HEX(i.id))  AS invoice_id,
                    i.invoice_number  AS invoice_number,
                    i.status          AS status,
                    i.subtotal        AS subtotal,
                    i.tax_rate        AS tax_rate,
                    i.tax_amount      AS tax_amount,
                    i.total_amount    AS total_amount,
                    i.currency        AS currency,
                    i.due_date        AS due_date,
                    i.paid_at         AS paid_at,
                    i.created_at      AS created_at
               FROM invoices i
              WHERE i.user_id = UNHEX(:uid)
                AND i.is_deleted = 0
              ORDER BY i.created_at DESC, i.id DESC
              LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            ['uid' => $userId]
        );
    }

    private function db(): IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }
}
