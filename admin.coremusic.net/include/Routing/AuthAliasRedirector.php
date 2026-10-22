<?php declare(strict_types=1);

namespace CoreMusic\Admin\Routing;

/**
 * Admin login/register → auth domain yönlendirmesi (2026-10-21, kullanıcı kararı).
 *
 * Eski G2 (2026-10-19) sözleşmesi admin host'taki LOKAL Figma formlarına
 * (`/admin-login`) yönleniyordu; bu sayfalar kaldırıldı — admin auth formları
 * artık auth.coremusic.net altındadır (ADR-043 auth subdomain consolidation):
 *
 *   admin.coremusic.net/login            → auth.coremusic.net/admin/login
 *   admin.coremusic.net/register         → auth.coremusic.net/admin/register
 *   admin.coremusic.net/admin-login      → auth.coremusic.net/admin/login  (geriye uyum — eski linkler)
 *   admin.coremusic.net/admin-register   → auth.coremusic.net/admin/register (geriye uyum)
 *
 * Query string korunur (/login?error=x → /admin/login?error=x). OAuth
 * parametreleri (client_id=coremusic-admin, redirect_uri=.../auth/callback)
 * auth domain'inin index.php §4 Default OAuth Redirect'i tarafından eklenir.
 *
 * Saf ve I/O-sızdır (header()/exit YOK) → birim testi doğrudan çağırabilir.
 * bootstrap.php bu sonucu alıp header('Location: ...') + exit uygular.
 *
 * Guardrail: .ai/.templates/backend/php-template.md — strict_types, no ORM.
 */
final class AuthAliasRedirector
{
    /** URI → auth domain path'leri (auth base URL bootstrap'tan parametre gelir). */
    private const ALIASES = [
        '/login'           => '/admin/login',
        '/register'        => '/admin/register',
        '/admin-login'     => '/admin/login',
        '/admin-register'  => '/admin/register',
    ];

    /**
     * Verilen istek URI'si bir login/register alias'ı mı diye bakar.
     *
     * @param string $requestUri  Ham REQUEST_URI (query dahil olabilir).
     * @param string $authBaseUrl Auth domain kök URL (ör. http://auth.coremusic.net).
     * @param string $queryString Ham QUERY_STRING (boş olabilir).
     * @return array{status:int, location:string}|null Alias ise [302, hedef],
     *                                                 değilse null.
     */
    public static function resolve(string $requestUri, string $authBaseUrl, string $queryString = ''): ?array
    {
        $path = rtrim(parse_url($requestUri, PHP_URL_PATH) ?: '', '/');

        if (!array_key_exists($path, self::ALIASES)) {
            return null;
        }

        $location = rtrim($authBaseUrl, '/') . self::ALIASES[$path];
        if ($queryString !== '') {
            $location .= '?' . $queryString;
        }

        return ['status' => 302, 'location' => $location];
    }
}
