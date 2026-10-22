<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * INotificationService — bildirim uçları iş katmanı sözleşmesi (Adım 05).
 *
 * SRP: yalnızca bildirim okuma/özetleme. Yazma (okundu işaretleme, silme)
 * route'ları tanımlanana kadar bu arayüzde tutulmaz (YAGNI).
 */
interface INotificationService
{
    /** Kullanıcının okunmamış bildirim sayısı (ownership: userId filtresi zorunlu). */
    public function getUnreadCount(string $userId): int;
}
