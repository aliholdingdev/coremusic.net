<?php declare(strict_types=1);

namespace CoreMusic\Auth\Handler;

use CoreMusic\Auth\Controller\AuthController;

/**
 * Auth POST Handler — PageRouter handler adaptörü
 *
 * PageRouter'ın beklediği handle(array $request): array formatında
 * AuthController'ın POST metodlarını yönlendirir.
 */
final class AuthPostHandler
{
    public function __construct(
        private readonly AuthController $controller,
    ) {}

    /**
     * PageRouter handler contract: handle(array $request): array
     */
    public function handle(array $request): array
    {
        $method = strtoupper($request['method'] ?? 'GET');
        $uri    = trim($request['uri'] ?? '', '/');

        if ($method !== 'POST') {
            return [
                'httpStatus' => 405,
                'type'       => 'json',
                'body'       => ['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'POST required.']],
            ];
        }

        $normalizedRequest = $this->normalizeRequest($request);

        try {
            return match ($uri) {
                'login'            => $this->controller->handleLogin($normalizedRequest),
                'register'         => $this->controller->handleRegister($normalizedRequest),
                'logout'           => $this->controller->handleLogout($normalizedRequest),
                'set-gender'       => $this->controller->handleSetGender($normalizedRequest),
                'forgot-password'  => $this->controller->handleForgotPassword($normalizedRequest),
                'reset-password'   => $this->controller->handleResetPassword($normalizedRequest),
                default            => [
                    'httpStatus' => 404,
                    'type'       => 'json',
                    'body'       => ['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Route bulunamadı.']],
                ],
            };
        } catch (\Throwable $e) {
            return [
                'httpStatus' => 500,
                'type'       => 'json',
                'body'       => ['error' => 'internal_error', 'message' => $e->getMessage()],
            ];
        }
    }

    /**
     * AuthController'ın beklediği request formatına dönüştür.
     * JSON body'yi php://input'tan parse eder.
     */
    private function normalizeRequest(array $request): array
    {
        $body = $request['body'] ?? [];
        if (empty($body)) {
            $rawInput = file_get_contents('php://input');
            if ($rawInput !== '' && $rawInput !== false) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $body = $decoded;
                }
            }
        }

        return [
            'method'        => $request['method'] ?? 'POST',
            'uri'           => $request['uri'] ?? '',
            'normalizedUri' => '/' . ($request['uri'] ?? ''),
            'headers'       => $request['headers'] ?? [],
            'body'          => $body,
            'server'        => $request['server'] ?? $_SERVER,
            'query_params'  => $request['query'] ?? $request['query_params'] ?? $_GET,
        ];
    }
}
