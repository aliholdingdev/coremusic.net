<?php declare(strict_types=1);

namespace CoreMusic\Admin\Routing;

/**
 * Admin kök (/) yönlendirmesi — 2026-10-19.
 *
 * Shared PageRouter::handleRootUrl() kök isteği tüm subdomainler için
 * home.coremusic.net'e (oturum açık) veya auth login'ine (oturum yok) atar;
 * admin host'ta bu YANLIŞTIR. Bu sınıf admin kökünün kendi kararını verir:
 *
 *   - oturum açık  → /admin-dashboard
 *   - oturum yok   → /admin-login
 *
 * Saf ve I/O-sızdır (header()/exit YOK) → birim testi doğrudan çağırabilir.
 * bootstrap.php bu sonucu alıp header('Location: ...') + exit uygular.
 *
 * Guardrail: .ai/.templates/backend/php-template.md — strict_types, no ORM.
 */
final class AdminRootRedirector
{
    /**
     * @param bool $isAuthenticated Session'da geçerli kullanıcı var mı
     *                               (PageRouterHelper::checkAuthenticated ölçütü).
     * @return array{status:int, location:string} [302, hedef rota].
     */
    public static function resolve(bool $isAuthenticated): array
    {
        return [
            'status'   => 302,
            'location' => $isAuthenticated ? '/admin-dashboard' : '/admin-login',
        ];
    }
}
