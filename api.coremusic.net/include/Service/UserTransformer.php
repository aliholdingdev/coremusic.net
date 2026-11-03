<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * UserTransformer — Kullanıcı verisini API sözleşmesine göre şekillendirme.
 *
 * AuthController'dan user shaping mantığını ayırarak SRP sağlar.
 * Sadece kullanıcı verisini sözleşme formatına dönüştürme sorumluluğu vardır.
 */
final class UserTransformer implements IUserTransformer
{
    /**
     * {@inheritDoc}
     */
    public function transform(array $user): array
    {
        $gender = (string) ($user['gender'] ?? 'neutral');

        return [
            'id'       => (string) ($user['id'] ?? ($user['user_id'] ?? '')),
            'username' => (string) ($user['username'] ?? ''),
            'email'    => (string) ($user['email'] ?? ''),
            'gender'   => $gender !== '' ? $gender : 'neutral',
        ];
    }
}