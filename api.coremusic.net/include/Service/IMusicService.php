<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * IMusicService — müzik uçları iş katmanı sözleşmesi (Faz 3 iskeleti).
 *
 * Faz 4'te implementasyon: MusicRepository + stream (P0-3 /stream API'ye).
 * IMPLEMENTED: hayır — not-implemented repository/service bağlanana kadar
 * route kayıtları implemented:false (405) kalır; sahte veri ÜRETİLMEZ.
 */
interface IMusicService
{
    /**
     * Sayfalı şarkı listesi (cinsiyet segmenti servis katmanında çözülür).
     *
     * @param int $page 1 tabanlı sayfa numarası
     * @param int $perPage Sayfa başına kayıt (max 100)
     * @return array{items: array<int, array<string, mixed>>, total: int, page: int, per_page: int}
     */
    public function listTracks(int $page, int $perPage): array;

    /**
     * Hibrit arama (P0-5): başlık + sanatçı + albüm.
     *
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function searchTracks(string $query, int $limit): array;

    /**
     * Tekil şarkı detayı.
     *
     * @return array<string, mixed>|null Bulunamazsa null
     */
    public function getTrack(string $trackId): ?array;
}
