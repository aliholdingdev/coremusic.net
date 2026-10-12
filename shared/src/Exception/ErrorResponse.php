<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class ErrorResponse
{
    public static function create(int $status, string $message, string $errorCode, ?string $traceId = null): array
    {
        $body = [
            'error' => [
                'code'    => $errorCode,
                'message' => $message,
            ],
        ];
        if ($traceId !== null) {
            $body['trace_id'] = $traceId;
        }
        return $body;
    }

    public static function fromException(\Throwable $e): array
    {
        $errorCode = $e instanceof BaseCoreMusicException ? $e->getErrorCode() : 'INTERNAL_ERROR';
        $message = $e->getMessage();
        return [
            'error' => [
                'code'    => $errorCode,
                'message' => $message,
            ],
        ];
    }

    public static function fromExceptionAsResponse(\Throwable $e, ?string $traceId = null): array
    {
        $body = self::fromException($e);
        return [
            'httpStatus' => $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500,
            'type'       => 'json',
            'body'       => $body,
            'headers'    => [],
        ];
    }
}
