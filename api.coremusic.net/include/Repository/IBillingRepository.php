<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

/**
 * BillingRepository sözleşmesi — coremusic_billing salt-okuma erişimi.
 *
 * Şema SSOT: .ai/.sql/mysql/coremusic_billing.sql (ürün şeması dump;
 * canlı şema denetimi tests/Unit/BillingLiveSchemaTest — DDL yasak).
 * Kapsam: kullanıcının KENDİ aboneliği/plani ve faturaları; ödeme/PSP yazma YOK.
 */
interface IBillingRepository
{
    /**
     * Kullanıcının en güncel aboneliği + bağlı plan özeti (en yeni kayıt).
     *
     * @param string $userId 32 hex UUIDv7 (session kanonik formu)
     * @return array<string, mixed>|null abonelik yoksa null
     */
    public function findCurrentSubscriptionWithPlan(string $userId): ?array;

    /**
     * Kullanıcının fatura adedi (soft-delete hariç).
     */
    public function countInvoices(string $userId): int;

    /**
     * Kullanıcının faturaları (en yeni önce, sayfalı).
     *
     * @param string $userId 32 hex UUIDv7
     * @param int    $limit  pageSize (controller 1..100 aralığında doğrular)
     * @param int    $offset (page-1)*pageSize
     * @return list<array<string, mixed>>
     */
    public function findInvoices(string $userId, int $limit, int $offset): array;
}
