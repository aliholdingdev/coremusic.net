<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\IUserRepository;
use CoreMusic\Security\JwtService;

/**
 * TokenService — JWT access token yönetimi (issue + persist + revoke).
 *
 * AuthController'dan token mantığını ayırarak SRP sağlar.
 * Sadece login/register sonrası token üretimi ve kaydetme sorumluluğu vardır.
 */
final class TokenService implements ITokenService
{
    public function __construct(
        private readonly JwtService $jwt,
        private readonly IUserRepository $userRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function buildAuthPayload(array $authResult, array $requestBody): array
    {
        $userId = (string) ($authResult['user']['id'] ?? ($authResult['user']['user_id'] ?? ''));
        if ($userId === '') {
            return [];
        }

        $issued = $this->jwt->issue($userId);
        $this->userRepository->saveAccessToken(
            $userId,
            $issued['jti'],
            date('Y-m-d H:i:s', time() + $issued['expires_in'])
        );

        return [
            'access_token' => $issued['token'],
            'token_type'   => $issued['token_type'],
            'expires_in'   => $issued['expires_in'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function revokeUserTokens(string $userId): void
    {
        $this->userRepository->revokeAccessTokensForUser($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function isValidAccessToken(string $jti): bool
    {
        return $this->userRepository->isValidAccessToken($jti);
    }
}