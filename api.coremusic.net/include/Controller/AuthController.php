<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Contracts\Auth\IUserRepository;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ValidationException;
use CoreMusic\Security\SecurityHelper;
use CoreMusic\Security\JwtService;

/**
 * AuthController — /v1/auth/* uçlarını AuthService'e (SSOT) bağlar.
 *
 * Faz 1b (ADR-084 Contract First · ADR-083 L2 Routing):
 *   İş mantığı KOPYALANMAZ: `auth.coremusic.net/include` içindeki AuthService,
 *   UserRepository ve SessionManager autoload ile kullanılır. Bu sınıf yalnızca
 *   HTTP çevirisini (gövde → servis çağrısı → sözleşme yanıtı) yapar.
 *
 * Sözleşme (Faz 3a UI — DEĞİŞTİRİLEMEZ):
 *   başarı → {success:true, data:{...}}   (forgot/reset → {success:true, message})
 *   hata   → {error:{code,message}}       (422 → + error.fields)
 */
final class AuthController
{
    private const FORGOT_MESSAGE = 'Sıfırlama bağlantısı gönderildi.';
    private const RESET_MESSAGE  = 'Şifreniz sıfırlandı.';

    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionManager $session,
        private readonly string $musicUrl,
        private readonly string $authUrl,
        private readonly ?JwtService $jwt = null,
        private readonly ?IUserRepository $users = null,
    ) {}

    /**
     * @param array<string, mixed> $route  RouteTable kaydı (`action` alanı)
     * @param array<string, mixed> $request pipeline isteği (`server`, `method`, `uri`, ...)
     */
    public function handle(array $route, array $request): array
    {
        $action = (string) ($route['action'] ?? '');
        $body   = $this->body($request);
        $ip     = $this->clientIp($request);

        try {
            return match ($action) {
                'login'          => $this->login($body, $ip),
                'register'       => $this->register($body, $ip),
                'setGender'      => $this->setGender($body),
                'forgotPassword' => $this->forgotPassword($body, $ip),
                'resetPassword'  => $this->resetPassword($body),
                'logout'         => $this->logout(),
                'me'             => $this->me(),
                default          => $this->error('NOT_FOUND', 'Uç bulunamadı.', 404),
            };
        } catch (RateLimitException $e) {
            header('Retry-After: ' . $e->getRetryAfter());

            return $this->error('RATE_LIMITED', 'Çok fazla deneme. Lütfen bekleyin.', 429);
        } catch (ConflictException $e) {
            // Sözleşme kodları: EMAIL_EXISTS → EMAIL_TAKEN, USERNAME_EXISTS → USERNAME_TAKEN
            $code = $e->getErrorCode() === 'USERNAME_EXISTS' ? 'USERNAME_TAKEN' : 'EMAIL_TAKEN';

            return $this->error($code, $e->getMessage(), 409);
        } catch (AuthenticationException $e) {
            // GENDER_MISMATCH sözleşmede 403; askıya alınmış hesap da 403.
            $status = in_array($e->getErrorCode(), ['GENDER_MISMATCH', 'ACCOUNT_BANNED'], true) ? 403 : 401;

            return $this->error($e->getErrorCode(), $e->getMessage(), $status);
        } catch (ValidationException $e) {
            return $this->validationError($e, $action);
        }
        // PDO / beklenmeyen istisnalar Gateway'e bırakılır: orada trace loglanır
        // ve istemciye yalnızca 500 INTERNAL_ERROR döner (ADR-020 §2.2F).
    }

    // ─── Actions ───

    /** @param array<string, mixed> $body */
    private function login(array $body, string $ip): array
    {
        $identity = trim((string) ($body['email'] ?? $body['identity'] ?? $body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        $result = $this->authService->login($identity, $password, $this->visitorGender($body), $ip);

        return $this->success($this->authPayload($result, $body));
    }

    /** @param array<string, mixed> $body */
    private function register(array $body, string $ip): array
    {
        $result = $this->authService->register($body, $ip, $this->visitorGender($body));

        return $this->success($this->authPayload($result, $body));
    }

    /** @param array<string, mixed> $body */
    private function setGender(array $body): array
    {
        $gender = (string) ($body['gender'] ?? '');
        if ($gender === '') {
            return $this->error('VALIDATION_ERROR', 'Validation failed', 422, [
                'fields' => ['gender' => 'gender is required'],
            ]);
        }

        $this->session->setGender($gender);

        return $this->success([
            'gender'   => $gender,
            'redirect' => $this->safeRedirect((string) ($body['redirect_uri'] ?? ''), '/login'),
        ]);
    }

    /** @param array<string, mixed> $body */
    private function forgotPassword(array $body, string $ip): array
    {
        // Enumeration koruması: bilinmeyen e-posta da aynen 200 döner (servis sözleşmesi).
        $result = $this->authService->requestPasswordReset(
            (string) ($body['email'] ?? ''),
            defined('COREMUSIC_SCHEME') ? COREMUSIC_SCHEME : 'http',
            $_SERVER['HTTP_HOST'] ?? 'api.coremusic.net',
            $ip,
        );

        return [
            'success' => true,
            'message' => (string) ($result['message'] ?? self::FORGOT_MESSAGE),
        ];
    }

    /** @param array<string, mixed> $body */
    private function resetPassword(array $body): array
    {
        $this->authService->resetPassword(
            (string) ($body['token'] ?? ''),
            (string) ($body['password'] ?? ''),
        );

        return ['success' => true, 'message' => self::RESET_MESSAGE];
    }

    private function logout(): array
    {
        $this->authService->logout();

        return $this->success(['redirect' => $this->authUrl . '/login']);
    }

    private function me(): array
    {
        $user = $this->authService->getCurrentUser();
        if ($user === null) {
            return $this->error('UNAUTHORIZED', 'Authentication required', 401);
        }

        return $this->success(['user' => $this->shapeUser($user)]);
    }

    // ─── Contract shaping ───

    /**
     * Login/register servis sonucunu sözleşmenin `data` gövdesine çevirir.
     *
     * @param array<string, mixed> $result AuthService::login/register toArray()
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    private function authPayload(array $result, array $body): array
    {
        $authKey = (string) ($result['auth_key'] ?? '');

        $payload = [
            'auth_key' => $authKey,
            'redirect' => $this->resolveRedirect($body, $authKey),
            'user'     => $this->shapeUser(is_array($result['user'] ?? null) ? $result['user'] : []),
        ];

        // P1-9 (B-F-01): hybrid auth — API istemcisine RS256 access token üret.
        // jti user_tokens'ta saklanır (sha256) → logout ile revocation edilebilir.
        // Hata durumunda login YINE de başarılıdır; token alanı eklenmez (fail-safe
        // hata: imzalı token DB'ye yazılamadan dağıtılmaz).
        $userId = (string) ($payload['user']['id'] ?? '');
        if ($this->jwt !== null && $this->users !== null && $userId !== '') {
            $issued = $this->jwt->issue($userId);
            $this->users->saveAccessToken(
                $userId,
                $issued['jti'],
                date('Y-m-d H:i:s', time() + $issued['expires_in'])
            );
            $payload['access_token'] = $issued['token'];
            $payload['token_type']   = $issued['token_type'];
            $payload['expires_in']   = $issued['expires_in'];
        }

        return $payload;
    }

    /**
     * Sözleşme kullanıcı gövdesi: {id, username, email, gender}.
     *
     * @param array<string, mixed> $user
     * @return array{id: string, username: string, email: string, gender: string}
     */
    private function shapeUser(array $user): array
    {
        $gender = (string) ($user['gender'] ?? 'neutral');

        return [
            'id'       => (string) ($user['id'] ?? ($user['user_id'] ?? '')),
            'username' => (string) ($user['username'] ?? ''),
            'email'    => (string) ($user['email'] ?? ''),
            'gender'   => $gender !== '' ? $gender : 'neutral',
        ];
    }

    /**
     * §3.1: doğrulanmış redirect_uri → aksi halde {MUSIC_URL}/home; auth_key eklenir.
     *
     * @param array<string, mixed> $body
     */
    private function resolveRedirect(array $body, string $authKey): string
    {
        $candidate = (string) ($body['redirect_uri'] ?? '');
        if ($candidate === '') {
            $candidate = (string) ($body['redirect'] ?? '');
        }
        if ($candidate === '') {
            $candidate = (string) ($_GET['redirect_uri'] ?? $_GET['redirect'] ?? '');
        }

        return $this->safeRedirect($candidate, $this->musicUrl . '/home', $authKey);
    }

    /** Open-redirect koruması SSOT: SecurityHelper → ReturnUrlPolicy. */
    private function safeRedirect(string $candidate, string $fallback, string $authKey = ''): string
    {
        $target = ($candidate !== '' && SecurityHelper::isRedirectUriSafe($candidate))
            ? $candidate
            : $fallback;

        if ($authKey !== '') {
            $separator = str_contains($target, '?') ? '&' : '?';
            $target   .= $separator . 'auth_key=' . urlencode($authKey);
        }

        return $target;
    }

    /** @param array<string, mixed> $body */
    private function visitorGender(array $body): string
    {
        $fromBody = (string) ($body['gender'] ?? '');
        if ($fromBody !== '') {
            return $fromBody;
        }

        $fromSession = $this->session->getGender();

        return $fromSession !== '' ? $fromSession : 'neutral';
    }

    // ─── Error mapping ───

    private function validationError(ValidationException $e, string $action): array
    {
        $code = $e->getErrorCode();

        // reset-password: süresi dolmuş/geçersiz token → 400 TOKEN_INVALID (sözleşme).
        if ($action === 'resetPassword' && in_array($code, ['TOKEN_EXPIRED', 'INVALID_TOKEN'], true)) {
            return $this->error('TOKEN_INVALID', 'Link geçersiz veya süresi dolmuş.', 400);
        }

        $fields = $e->getErrors();
        if ($fields === []) {
            $field = match ($code) {
                'PASSWORD_TOO_SHORT' => 'password',
                'INVALID_EMAIL'      => 'email',
                'INVALID_TOKEN',
                'TOKEN_EXPIRED'       => 'token',
                default              => 'form',
            };
            $fields[$field] = $e->getMessage();
        }

        return $this->error('VALIDATION_ERROR', 'Validation failed', 422, ['fields' => $fields]);
    }

    // ─── Primitives ───

    /** @param array<string, mixed> $data */
    private function success(array $data): array
    {
        http_response_code(200);

        return ['success' => true, 'data' => $data];
    }

    /** @param array<string, mixed> $extra */
    private function error(string $code, string $message, int $status, array $extra = []): array
    {
        http_response_code($status);

        return ['error' => ['code' => $code, 'message' => $message] + $extra];
    }

    /**
     * Gövde çözümleme: test/önceden çözülmüş gövde yoksa php://input (JSON).
     *
     * @param array<string, mixed> $request
     * @return array<string, mixed>
     */
    private function body(array $request): array
    {
        if (array_key_exists('body', $request)) {
            $body = $request['body'];
            if (is_array($body)) {
                return $body;
            }
            if (is_string($body)) {
                $decoded = json_decode($body, true);

                return is_array($decoded) ? $decoded : [];
            }

            return [];
        }

        $contentType = strtolower((string) ($request['server']['CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? ''));
        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return $_POST;
        }

        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $request */
    private function clientIp(array $request): string
    {
        return (string) ($request['server']['REMOTE_ADDR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    }
}
