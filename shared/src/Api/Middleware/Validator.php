<?php
declare(strict_types=1);

/**
 * Bağımlılıksız route validatörü (ADR-020 §2.2 / §5.1 — 422 + alan hataları).
 *
 * @file Validator.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

/**
 * RequestValidationMiddleware için minimal kural motoru.
 *
 * Neden Respect/Validation değil: `RequestValidationMiddleware` return type'ı
 * yüklü olmayan bir sınıfa bağlanınca (vendor kaybı / kısmi autoload) fatal
 * oluyordu. Bu sınıf saf PHP ile çalışır; hiçbir method'ta class-typed return
 * yok → TypeError imkânsız.
 *
 * Kural biçimi (route['validation']):
 *   'email' => ['required' => true, 'type' => 'email']
 *   'age'   => ['type' => 'int', 'min' => 18, 'max' => 120]
 *   'role'  => ['type' => 'string', 'in' => ['user', 'admin'], 'regex' => '/^[a-z]+$/']
 */
final class Validator
{
    /**
     * @param array<string, mixed>  $data  doğrulanacak kaynak (body veya query)
     * @param array<string, array<string, mixed>> $rules
     *
     * @return array<string, string> alan => hata mesajı (boş = geçerli)
     */
    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!is_string($field) || !is_array($rule)) {
                continue;
            }

            $exists = array_key_exists($field, $data);
            $value  = $exists ? $data[$field] : null;

            if (!$exists || $value === null || $value === '') {
                if (!empty($rule['required'])) {
                    $errors[$field] = "{$field} is required";
                }
                continue;
            }

            foreach ($this->fieldErrors($field, $value, $rule) as $error) {
                $errors[$field] = $error;
                break;
            }
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $rule
     * @return list<string>
     */
    private function fieldErrors(string $field, mixed $value, array $rule): array
    {
        $errors = [];

        if (isset($rule['type'])) {
            $typeError = $this->typeError($field, $value, (string) $rule['type']);
            if ($typeError !== null) {
                $errors[] = $typeError;
            }
        }

        if ($errors !== []) {
            return $errors;
        }

        if (isset($rule['regex']) && is_string($rule['regex']) && is_scalar($value)) {
            if (@preg_match($rule['regex'], (string) $value) !== 1) {
                $errors[] = "{$field} has an invalid format";
            }
        }

        if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'], true)) {
            $errors[] = "{$field} must be one of: " . implode(', ', array_map('strval', $rule['in']));
        }

        if (isset($rule['min'])) {
            $this->compare($field, $value, (float) $rule['min'], 'min', $errors);
        }

        if (isset($rule['max'])) {
            $this->compare($field, $value, (float) $rule['max'], 'max', $errors);
        }

        return $errors;
    }

    /**
     * @param list<string> $errors
     */
    private function compare(string $field, mixed $value, float $limit, string $kind, array &$errors): void
    {
        // Sayısal değer → değer karşılaştırması; metin → uzunluk karşılaştırması.
        if (is_int($value) || is_float($value)) {
            $ok = $kind === 'min' ? (float) $value >= $limit : (float) $value <= $limit;
            if (!$ok) {
                $errors[] = $kind === 'min'
                    ? "{$field} must be at least {$limit}"
                    : "{$field} must be at most {$limit}";
            }
            return;
        }

        if (is_string($value)) {
            $length = mb_strlen($value);
            $ok = $kind === 'min' ? $length >= (int) $limit : $length <= (int) $limit;
            if (!$ok) {
                $errors[] = $kind === 'min'
                    ? "{$field} must be at least {$limit} characters"
                    : "{$field} must be at most {$limit} characters";
            }
        }
    }

    private function typeError(string $field, mixed $value, string $type): ?string
    {
        return match ($type) {
            'string' => is_string($value) ? null : "{$field} must be a string",
            'int'    => (is_int($value) || (is_string($value) && preg_match('/^-?\d+$/', $value) === 1))
                ? null : "{$field} must be an integer",
            'float'  => is_float($value) || is_int($value)
                ? null : "{$field} must be a number",
            'bool'   => is_bool($value) ? null : "{$field} must be a boolean",
            'array'  => is_array($value) ? null : "{$field} must be an array",
            'email'  => $this->isEmail($value) ? null : "{$field} must be a valid email address",
            default  => null,
        };
    }

    private function isEmail(mixed $value): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
