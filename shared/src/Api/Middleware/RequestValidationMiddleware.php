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
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

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
            $validator = $this->buildValidator($rules);
            $validator->assert($data);
            
            return $next($request);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->getMessages() as $field => $message) {
                $errors[$field] = $message;
            }
            
            return ApiResponse::error(
                'VALIDATION_ERROR',
                'Validation failed',
                422,
                $errors
            );
        }
    }

    /**
     * Build validator from rules array.
     */
    private function buildValidator(array $rules): Validator
    {
        $validator = v::noneOf();
        
        foreach ($rules as $field => $rule) {
            $fieldValidator = $this->mapRuleToValidator($rule);
            $validator = $validator->addValidator(
                v::key($field, $fieldValidator)
            );
        }
        
        return $validator;
    }

    /**
     * Map rule configuration to Respect/Validation validator.
     */
    private function mapRuleToValidator(array $rule): Validator
    {
        $validator = v::noneOf();
        
        if (isset($rule['required']) && $rule['required']) {
            $validator = $validator->addValidator(v::notEmpty());
        }
        
        if (isset($rule['type'])) {
            $typeValidator = match ($rule['type']) {
                'string' => v::stringType(),
                'int' => v::intVal(),
                'float' => v::floatVal(),
                'bool' => v::boolVal(),
                'email' => v::email(),
                'array' => v::arrayType(),
                default => v::noneOf(),
            };
            $validator = $validator->addValidator($typeValidator);
        }
        
        if (isset($rule['min'])) {
            $validator = $validator->addValidator(v::min($rule['min']));
        }
        
        if (isset($rule['max'])) {
            $validator = $validator->addValidator(v::max($rule['max']));
        }
        
        if (isset($rule['regex'])) {
            $validator = $validator->addValidator(v::regex($rule['regex']));
        }
        
        if (isset($rule['in'])) {
            $validator = $validator->addValidator(v::in($rule['in']));
        }
        
        return $validator;
    }
}
