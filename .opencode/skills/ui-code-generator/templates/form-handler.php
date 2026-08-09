<?php
/**
 * Secure Form Handler Template
 * Follows CoreMusic standards: strict_types, PDO, CSRF, input validation
 */

declare(strict_types=1);

namespace App\Handler;

use PDO;
use PDOException;

class FormHandler {
    public function __construct(
        private PDO $pdo,
        private CsrfValidator $csrfValidator,
        private Logger $logger,
    ) {}

    /**
     * Handle form submission with full security checks
     *
     * @param array<string, mixed> $request POST data
     * @param array<string, string> $session Session data
     * @return array<string, mixed> Response
     * @throws ValidationException
     */
    public function handle(array $request, array $session): array {
        $traceId = bin2hex(random_bytes(16));

        try {
            // 1. CSRF Validation
            if (!$this->csrfValidator->validate($request['csrf_token'] ?? '', $session['csrf_token'] ?? '')) {
                throw new ValidationException('CSRF validation failed', 403);
            }

            // 2. Input Validation
            $email = filter_var($request['email'] ?? '', FILTER_VALIDATE_EMAIL);
            if (!$email) {
                throw new ValidationException('Invalid email address', 400);
            }

            $name = trim($request['name'] ?? '');
            if (empty($name) || strlen($name) > 255) {
                throw new ValidationException('Invalid name', 400);
            }

            // 3. Sanitization (HTML encode if storing)
            $name = htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            // 4. Database Insert (PDO prepared statement)
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (email, name, created_at) VALUES (:email, :name, NOW())'
            );
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();

            // 5. Success Response
            $this->logger->info('Form submitted successfully', [
                'email'    => $email,
                'trace_id' => $traceId,
            ]);

            return [
                'success'     => true,
                'message'     => 'Form submitted successfully',
                'trace_id'    => $traceId,
                'csrf_token'  => $this->generateCsrfToken($session),
            ];

        } catch (ValidationException $e) {
            $this->logger->warning('Form validation failed', [
                'error'    => $e->getMessage(),
                'trace_id' => $traceId,
            ]);
            return [
                'success'    => false,
                'error'      => $e->getMessage(),
                'status_code' => $e->getCode(),
                'trace_id'   => $traceId,
            ];

        } catch (PDOException $e) {
            $this->logger->error('Database error', [
                'error'    => $e->getMessage(),
                'trace_id' => $traceId,
            ]);
            return [
                'success'     => false,
                'error'       => 'Database error occurred',
                'status_code' => 500,
                'trace_id'    => $traceId,
            ];
        }
    }

    /**
     * Generate fresh CSRF token
     */
    private function generateCsrfToken(array &$session): string {
        $token = bin2hex(random_bytes(32));
        $session['csrf_token'] = $token;
        return $token;
    }
}

// Usage in page/controller:
/*
$handler = new FormHandler($pdo, $csrfValidator, $logger);
$response = $handler->handle($_POST, $_SESSION);
echo json_encode($response);
*/
