<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * IUserHeaderService — header kısayol verisi iş katmanı sözleşmesi (Adım 05).
 *
 * Header ucu (GET /v1/user/header) tek istekte 5 veri kaynağı özetler:
 * profil + playlist kısayolları + okunmamış bildirim sayısı + rol bazlı menü
 * (Adım 11) + arama önerileri.
 * Katman ihlali düzeltmesi: controller 4 repository'yi doğrudan enjekte
 * ediyordu (Controller→Repository); artık tek servise bağımlı.
 */
interface IUserHeaderService
{
    /**
     * Header özetini üretir.
     *
     * @param string|null $userId null → anonim özet (user: null, boş listeler, menu: []).
     * @return array{
     *     user: array<string, mixed>|null,
     *     is_authenticated: bool,
     *     playlist_shortcuts: list<array<string, mixed>>,
     *     unread_notifications: int,
     *     menu: list<array{ id: string, label: string, route: string, roles: list<string> }>,
     *     search_suggestions: list<array{ id: string, title: string, artist_name: string }>
     * }
     */
    public function getHeaderSummary(?string $userId): array;
}
