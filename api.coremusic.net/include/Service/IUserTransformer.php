<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * UserTransformer — Kullanıcı verisini API sözleşmesine göre şekillendirme.
 *
 * AuthController'dan user shaping mantığını ayırarak SRP sağlar.
 * Sadece kullanıcı verisini sözleşme formatına dönüştürme sorumluluğu vardır.
 */
interface IUserTransformer
{
    /**
     * Kullanıcı verisini sözleşme formatına dönüştür.
     *
     * Sözleşme: {id, username, email, gender}
     *
     * @param array<string, mixed> $user Ham kullanıcı verisi
     * @return array{id: string, username: string, email: string, gender: string}
     */
    public function transform(array $user): array;
}