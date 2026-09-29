<?php
declare(strict_types=1);

/**
 * Request Validation Middleware for API requests.
 *
 * @file RequestValidationMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

use CoreMusic\Api\ApiResponse;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validatable;
use Respect\Validation\Validator as v;

/**
 * Request Validation Middleware using Respect/Validation.
 */
final class RequestValidationMiddleware
{
    /**
     * Process request validation.
     */
    public function __invoke(array $request, callable $next): array
    {
        // Get validation rules from route
        $route = $request['_route'] ?? null;
        if ($route === null || !isset($route['validation'])) {
            return $next($request);
        }
        
        $rules = $route['validation'];
        $data = $request;
        
        try {
            $this->buildValidator($rules)->assert($data);
            
            return $next($request);
        } catch (NestedValidationException $e) {
            // Respect/Validation 2.x:Aggregate exception -> field => message
            $errors = $e->getMessages();
            
            return ApiResponse::error(
                'VALIDATION_ERROR',
                'Validation failed',
                422,
                $errors
            );
        } catch (ValidationException $e) {
            // Tek kural exception'u (nested olmayan) -> tek mesaj
            return ApiResponse::error(
                'VALIDATION_ERROR',
                'Validation failed',
                422,
                [$e->getId() => $e->getMessage()]
            );
        }
    }

    /**
     * Build validator from rules array.
     *
     * @param array<string, array<string, mixed>> $rules
     */
    private function buildValidator(array $rules): Validatable
    {
        $validators = [];
        
        foreach ($rules as $field => $rule) {
            $validators[] = v::key($field, $this->mapRuleToValidator($rule));
        }
        
        return v::create(...$validators);
    }

    /**
     * Map rule configuration to Respect/Validation validator.
     *
     * @param array<string, mixed> $rule
     */
    private function mapRuleToValidator(array $rule): Validatable
    {
        $validators = [];
        
        if (isset($rule['required']) && $rule['required']) {
            $validators[] = v::notEmpty();
        }
        
        if (isset($rule['type'])) {
            $validators[] = match ($rule['type']) {
                'string' => v::stringType(),
                'int' => v::intVal(),
                'float' => v::floatVal(),
                'bool' => v::boolVal(),
                'email' => v::email(),
                'array' => v::arrayType(),
                default => v::alwaysValid(),
            };
        }
        
        if (isset($rule['min'])) {
            $validators[] = v::min($rule['min']);
        }
        
        if (isset($rule['max'])) {
            $validators[] = v::max($rule['max']);
        }
        
        if (isset($rule['regex'])) {
            $validators[] = v::regex($rule['regex']);
        }
        
        if (isset($rule['in'])) {
            $validators[] = v::in($rule['in']);
        }
        
        return v::create(...$validators);
    }
}
