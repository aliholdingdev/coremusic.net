<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

/**
 * Validation Middleware (L1 — Pipeline #10)
 *
 * Request/DTO validasyonu. POST/PUT/DELETE isteklerinde zorunlu alanları kontrol eder.
 * Route meta'sındaki required_fields listesini doğrular.
 *
 * ADR-010 uyumlu. Frozen sıra: ...Permission → Validation → Controller
 */
final class ValidationMiddleware implements IMiddleware
{
    public function handle(array $request, callable $next): array
    {
        $method = strtoupper($request['method'] ?? 'GET');

        // Sadece state-changing istekleri doğrula
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $routeMeta    = $request['_route_meta'] ?? [];
        $requiredFields = $routeMeta['required_fields'] ?? [];

        if (empty($requiredFields)) {
            return $next($request);
        }

        $body = $request['body'] ?? [];
        $errors = [];

        foreach ($requiredFields as $field => $rules) {
            $value = $body[$field] ?? null;
            $label = $rules['label'] ?? $field;

            // Zorunlu alan kontrolü
            if (!empty($rules['required']) && ($value === null || $value === '')) {
                $errors[$field] = "{$label} zorunludur.";
                continue;
            }

            // Minimum uzunluk
            if (isset($rules['min_length']) && is_string($value) && strlen($value) < $rules['min_length']) {
                $errors[$field] = "{$label} en az {$rules['min_length']} karakter olmalıdır.";
                continue;
            }

            // Maksimum uzunluk
            if (isset($rules['max_length']) && is_string($value) && strlen($value) > $rules['max_length']) {
                $errors[$field] = "{$label} en fazla {$rules['max_length']} karakter olmalıdır.";
                continue;
            }

            // Email formatı
            if (!empty($rules['email']) && is_string($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "{$label} geçerli bir e-posta olmalıdır.";
                continue;
            }

            // Enum değerleri
            if (isset($rules['enum']) && !in_array($value, $rules['enum'], true)) {
                $allowed = implode(', ', $rules['enum']);
                $errors[$field] = "{$label} şu değerlerden biri olmalıdır: {$allowed}.";
                continue;
            }
        }

        if (!empty($errors)) {
            return [
                'httpStatus' => 422,
                'type'       => 'json',
                'body'       => ['error' => 'validation_failed', 'errors' => $errors],
                'headers'    => [],
                'halt'       => true,
            ];
        }

        return $next($request);
    }
}
