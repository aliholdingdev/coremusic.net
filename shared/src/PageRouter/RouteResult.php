<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class RouteResult
{
    public static function ok(string $container, array $meta, string $csrfToken, ?string $route = null): array
    {
        return [
            'httpStatus' => 200,
            'type'       => 'json',
            'body'       => [
                'container'  => $container,
                'route'      => $route ?? '',
                'meta'       => $meta,
                'csrf_token' => $csrfToken,
            ],
        ];
    }

    public static function html(string $html): array
    {
        return [
            'httpStatus' => 200,
            'type'       => 'html',
            'body'       => $html,
        ];
    }

    public static function notFound(?string $route = null): array
    {
        $body = ['error' => 'not_found'];
        if ($route !== null) {
            $body['route'] = $route;
        }
        return [
            'httpStatus' => 404,
            'type'       => 'json',
            'body'       => $body,
        ];
    }

    public static function redirect(string $location): array
    {
        return [
            'httpStatus' => 302,
            'type'       => 'redirect',
            'body'       => '',
            'headers'    => ['Location' => $location],
        ];
    }

    public static function forbidden(string $redirect): array
    {
        return [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => [
                'error'    => 'forbidden',
                'redirect' => $redirect,
            ],
        ];
    }

    public static function rateLimitExceeded(): array
    {
        return [
            'httpStatus' => 429,
            'type'       => 'json',
            'body'       => ['error' => 'rate_limit_exceeded'],
            'headers'    => [],
            'halt'       => true,
        ];
    }

    public static function error(string $message = '', bool $debug = false): array
    {
        $body = ['error' => 'server_error'];
        if ($debug && $message !== '') {
            $body['message'] = $message;
        }
        return [
            'httpStatus' => 500,
            'type'       => 'json',
            'body'       => $body,
        ];
    }
}
