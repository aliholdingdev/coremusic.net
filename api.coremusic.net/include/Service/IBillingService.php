<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * BillingService sözleşmesi — abonelik/plan özeti ve fatura listesi.
 *
 * Yalnız kullanıcının KENDİ verisi (own-data, auth zorunlu); admin operasyon
 * ve ödeme/PSP yazma kapsam dışı. Beklenmeyen istisna serviste YAKLANMAZ →
 * Gateway/index.php catch'i 500 döner (mesaj istemciye sızmaz).
 */
interface IBillingService
{
    /**
     * Kimliği doğrulanmış kullanıcının plan özeti.
     *
     * @return array<string, mixed> {has_subscription: bool, subscription: ?array, plan: ?array}
     */
    public function plan(string $userId): array;

    /**
     * Kimliği doğrulanmış kullanıcının faturaları (sayfalı).
     *
     * @param int $page     ≥1 (controller doğrular)
     * @param int $pageSize 1..100 (controller doğrular)
     * @return array{items: list<array<string, mixed>>, total: int}
     */
    public function invoices(string $userId, int $page, int $pageSize): array;
}
