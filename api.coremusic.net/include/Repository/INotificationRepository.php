<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

interface INotificationRepository
{
    /**
     * Kullanıcının okunmamış bildirim sayısı.
     * coremusic_system.system_notifications — user_id + is_read=0 + is_deleted=0.
     */
    public function countUnread(string $userId): int;
}
