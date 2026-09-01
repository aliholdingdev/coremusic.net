<?php declare(strict_types=1);

namespace CoreMusic\Auth\Middleware;

/**
 * Middleware Pipeline — Middleware'leri sırayla çalıştırır.
 *
 * Chain of Responsibility pattern kullanır.
 * Her middleware bir sonrakini çağırır veya isteği reddeder.
 */
final class MiddlewarePipeline
{
    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    /**
     * Pipeline'a middleware ekle (sona eklenir).
     */
    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Pipeline'ı çalıştır.
     *
     * @param array $request İstek verisi
     * @param callable $finalHandler Pipeline sonundaki işleyici
     * @return array Yanıt
     */
    public function run(array $request, callable $finalHandler): array
    {
        $pipeline = $finalHandler;

        // Ters sırada zincir oluştur (son eklenen ilk çalışır)
        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = $pipeline;
            $pipeline = function (array $req) use ($middleware, $next) {
                return $middleware->process($req, $next);
            };
        }

        return $pipeline($request);
    }

    /**
     * Pipeline'daki middleware sayısını döndür.
     */
    public function count(): int
    {
        return count($this->middlewares);
    }
}
