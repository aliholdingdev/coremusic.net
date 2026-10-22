<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\INotificationRepository;

/**
 * NotificationService — bildirim iş katmanı (INotificationService implementasyonu).
 *
 * Adım 05: SRP — yalnızca okunmamış sayıyı döndürür. Yazma uçları (okundu
 * işaretleme, silme) route'ları tanımlanana kadar eklenmez (YAGNI). Ownership
 * repository'de: user_id + is_read=0 + is_deleted=0 filtresi zorunlu.
 */
final class NotificationService implements INotificationService
{
    public function __construct(
        private readonly INotificationRepository $notifications,
    ) {}

    public function getUnreadCount(string $userId): int
    {
        return $this->notifications->countUnread($userId);
    }
}
