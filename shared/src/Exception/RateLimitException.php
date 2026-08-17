<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class RateLimitException extends BaseCoreMusicException
{
    private int $retryAfter;

    private function __construct(string $message, string $errorCode, int $retryAfter)
    {
        parent::__construct($message, $errorCode, 429);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    public static function loginRateLimited(int $seconds): self
    {
        return new self("Çok fazla deneme. {$seconds} saniye bekleyin.", 'LOGIN_RATE_LIMITED', $seconds);
    }

    public static function registerRateLimited(int $seconds): self
    {
        return new self("Çok fazla deneme. {$seconds} saniye bekleyin.", 'REGISTER_RATE_LIMITED', $seconds);
    }

    public static function passwordResetRateLimited(int $seconds): self
    {
        return new self("Çok fazla deneme. {$seconds} saniye bekleyin.", 'PASSWORD_RESET_RATE_LIMITED', $seconds);
    }
}
