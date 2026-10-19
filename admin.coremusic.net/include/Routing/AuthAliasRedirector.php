<?php declare(strict_types=1);

namespace CoreMusic\Admin\Routing;

/**
 * G2 (2026-10-19) — Admin login/register lokal alias sözleşmesi.
 *
 * Admin host'ta /login ve /register, kernel ÖNCESİ (AuthGuard yetkisinden önce)
 * lokal Figma formlarına 302 ile yönlenir; auth domain redirect'i YOKTUR.
 * AuthGuard::checkAuthRedirectRoute (shared AuthRouteConfig) bu URI'leri
 * auth.coremusic.net'e atardı — bu sınıf o çakışmayı kernel öncesi çözer.
 *
 * Saf ve I/O-sızdır (header()/exit YOK) → birim testi doğrudan çağırabilir.
 * bootstrap.php bu sonucu alıp header('Location: ...') + exit uygular.
 *
 * Guardrail: .ai/.templates/backend/php-template.md — strict_types, no ORM.
 */
final class AuthAliasRedirector
{
    /** URI → lokal form aliasları. Canonical rotalar: admin-login / admin-register. */
    private const ALIASES = [
        '/login'    => '/admin-login',
        '/register' => '/admin-register',
    ];

    /**
     * Verilen istek URI'si bir login/register alias'ı mı diye bakar.
     *
     * @param string $requestUri  Ham REQUEST_URI (query dahil olabilir).
     * @param string $queryString Ham QUERY_STRING (boş olabilir).
     * @return array{status:int, location:string}|null Alias ise [302, hedef],
     *                                                 değilse null.
     */
    public static function resolve(string $requestUri, string $queryString = ''): ?array
    {
        $path = rtrim(parse_url($requestUri, PHP_URL_PATH) ?: '', '/');

        if (!array_key_exists($path, self::ALIASES)) {
            return null;
        }

        $location = self::ALIASES[$path];
        if ($queryString !== '') {
            $location .= '?' . $queryString;
        }

        return ['status' => 302, 'location' => $location];
    }
}
