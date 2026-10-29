<?php declare(strict_types=1);

/**
 * CoreMusic API v2 — Container (ADR-084 Contract First)
 *
 * Thin delegate: v2 dispatch, v1 ApiContainer'ın AYNI controller grafiğini
 * kullanır (kopyalama YOK — v2/index.php başlığında taahhüt edilen reuse).
 * Bu dosya yalnızca Gateway'in v2 resolver sözleşmesini
 * (callable (array $route, array $request): array) karşılar ve v1 dispatch
 * yöntemini koşulsuz çağırır; middleware sırası ve zarf sözleşmesi
 * v2/index.php'de kuruludur (ADR-094/020 frozen sıra korunur).
 *
 * Yeni controller/dependency değişikliğinin ASIL yeri ApiContainer'dır;
 * bu sınıf v2 yüzeyinde çözüm noktası olarak konumlanmıştır (v2 autoloader:
 * CoreMusic\Api\V2\* → v2/*.php).
 */

namespace CoreMusic\Api\V2;

use CoreMusic\Api\Container\ApiContainer;

final class V2Container
{
    private function __construct()
    {
    }

    /**
     * Gateway handler resolver — route['handler'] → ApiContainer dispatch.
     *
     * @param array $route   RouteTable kaydı (service/handler/action/public/...)
     * @param array $request Pipeline request (server/method/uri/headers/body)
     *
     * @return array API sözleşmesi gövdesi ({data,...} | {error:{code,message}})
     */
    public static function handle(array $route, array $request): array
    {
        return ApiContainer::handle($route, $request);
    }
}
