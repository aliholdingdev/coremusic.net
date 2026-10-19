<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\DTO;

/**
 * AuthResponse DTO — Auth işlemlerinden dönen standart yanıt.
 *
 * Tüm auth endpoint'leri bu formatı kullanır.
 */
final class AuthResponse
{
    private function __construct(
        public readonly bool $success,
        public readonly string $redirect,
        public readonly ?string $authKey,
        public readonly ?array $user,
        public readonly ?string $message,
    ) {}

    public static function success(string $redirect, ?string $authKey = null, ?array $user = null): self
    {
        return new self(
            success: true,
            redirect: $redirect,
            authKey: $authKey,
            user: $user,
            message: null,
        );
    }

    public static function message(string $message): self
    {
        return new self(
            success: true,
            redirect: '',
            authKey: null,
            user: null,
            message: $message,
        );
    }

    public function toArray(): array
    {
        $result = ['success' => $this->success];

        if ($this->redirect !== '') {
            $result['redirect'] = $this->redirect;
        }
        if ($this->authKey !== null) {
            $result['auth_key'] = $this->authKey;
        }
        if ($this->user !== null) {
            $result['user'] = $this->user;
        }
        if ($this->message !== null) {
            $result['message'] = $this->message;
        }

        return $result;
    }
}
