<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;

final class NotificationRepository implements INotificationRepository
{
    private const DB_KEY = 'system';

    public function __construct(
        private readonly IDatabaseRegistry $systemRegistry,
    ) {}

    public function countUnread(string $userId): int
    {
        if (strlen($userId) !== 32) {
            return 0;
        }

        $sql = 'SELECT COUNT(id) AS unread
                  FROM system_notifications
                 WHERE user_id = UNHEX(:uid)
                   AND is_read = 0
                   AND is_deleted = 0';
        $rows = $this->systemDb()->execute($sql, ['uid' => $userId]);

        return (int) ($rows[0]['unread'] ?? 0);
    }

    private function systemDb(): IDatabaseManager
    {
        return $this->systemRegistry->get(self::DB_KEY);
    }
}
