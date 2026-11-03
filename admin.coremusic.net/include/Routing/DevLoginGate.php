<?php declare(strict_types=1);

namespace CoreMusic\Admin\Routing;

/**
 * /dev-login — P0 üretim gate'i (2026-10-30).
 *
 * Eski kapı (config/bootstrap.php eski 91-105) yalnız `DEBUG_MODE`'a bakıyordu;
 * `APP_ENV_MODE === 'production'` koşulu YOKTU — üretimde DEBUG bayrağı yanlışlıkla
 * açık kalırsa sahte admin oturumu (`adm-uuid-001`) kurulabiliyordu.
 *
 * Yeni sözleşme:
 *   production                    → 404; dev-login aksiyonu HİÇ çalışmaz (session'a
 *                                   admin anahtarı yazılmaz), DEBUG ne olursa olsun.
 *   development/test + DEBUG açık → mevcut davranış AYNEN (session + 302 /admin-dashboard).
 *   development/test + DEBUG kapalı → kapı kapalı, istek kernel'e bırakılır (eski davranış).
 *   /dev-login DIŞındaki rotalar  → null (gate'e hiç girilmez).
 *
 * Saf ve I/O-sızdır (header()/exit/session YOK): dev-login aksiyonunu çağırmak için
 * `$onDevLogin` geri çağrımı alır; bootstrap sonucu uygular (AuthAliasRedirector deseni).
 *
 * Guardrail: .ai/.templates/backend/php-template.md — strict_types, no ORM, PSR-12.
 */
final class DevLoginGate
{
    /** Gate'in koruduğu tek rota (bootstrap aynı normalize edilmiş URI ile besler). */
    public const PATH = '/dev-login';

    /**
     * /dev-login isteğinin üretimde reddedilip reddedilmeyeceğini ve dev/login
     * aksiyonunun çalışıp çalışmayacağını tek yerde karara bağlar.
     *
     * @param string        $envMode     APP_ENV_MODE ('production' | 'development' | 'test')
     * @param bool          $debugMode   DEBUG_MODE bayrağı
     * @param string        $requestUri  Bootstrap'te normalize edilmiş path (query yok, '/' kırpılmış)
     * @param callable():void $onDevLogin Yalnız kapı AÇIKKEN çağrılan dev-login aksiyonu (session + redirect)
     *
     * @return int|null null → bu rota gate'e girmez VEYA kapı kapalı (kernel devam eder);
     *                  404 → üretim reddi (uygulayıcı 404 döner, session'a YAZMAZ);
     *                  302 → $onDevLogin çağrıldı (uygulayıcı exit eder).
     */
    public static function handle(
        string $envMode,
        bool $debugMode,
        string $requestUri,
        callable $onDevLogin,
    ): ?int {
        if ($requestUri !== self::PATH) {
            return null;
        }

        // P0: üretimde kapı ASLA açılmaz — DEBUG bayrağı ne olursa olsun.
        if ($envMode === 'production') {
            return 404;
        }

        // Dev/test: mevcut davranış aynen korunur — yalnız DEBUG açıkken dev-login.
        if (!$debugMode) {
            return null;
        }

        $onDevLogin();

        return 302;
    }
}