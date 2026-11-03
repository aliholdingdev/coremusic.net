<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Api\Service\ITokenService;
use CoreMusic\Api\Service\IRedirectService;
use CoreMusic\Api\Service\IUserTransformer;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ValidationException;

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
 *
 * Refactored (Phase 2): TokenService, RedirectService, UserTransformer kullanır.
 * JWT, redirect, user shaping mantığı servislerde (SRP).
 */
final class AuthController extends AbstractController
{
    private const FORGOT_MESSAGE = 'Sıfırlama bağlantısı gönderildi.';
    private const RESET_MESSAGE  = 'Şifreniz sıfırlandı.';

    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionManager $session,
        private readonly ITokenService $tokenService,
        private readonly IRedirectService $redirectService,
        private readonly IUserTransformer $userTransformer,
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

        return $this->success($this->buildAuthPayload($result, $body));
    }

    /** @param array<string, mixed> $body */
    private function register(array $body, string $ip): array
    {
        $result = $this->authService->register($body, $ip, $this->visitorGender($body));

        return $this->success($this->buildAuthPayload($result, $body));
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
            'redirect' => $this->redirectService->resolveRedirect($body),
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

        return $this->success(['redirect' => $this->redirectService->getDefaultFallback() . '/login']);
    }

    private function me(): array
    {
        $user = $this->authService->getCurrentUser();
        if ($user === null) {
            return $this->error('UNAUTHORIZED', 'Authentication required', 401);
        }

        return $this->success([
            'user'       => $this->userTransformer->transform($user),
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // ─── Payload building ───

    /**
     * Login/register servis sonucunu sözleşmenin `data` gövdesine çevirir.
     *
     * @param array<string, mixed> $result AuthService::login/register sonucu
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    private function buildAuthPayload(array $result, array $body): array
    {
        $authKey = (string) ($result['auth_key'] ?? '');

        $payload = [
            'auth_key'   => $authKey,
            'redirect'   => $this->redirectService->resolveRedirect($body, $authKey),
            'user'       => $this->userTransformer->transform(
                is_array($result['user'] ?? null) ? $result['user'] : []
            ),
            // Faz 3: hibrit CSRF dağıtımı — login sonrası istemci x-csrf-token
            // başlığını bu değerle gönderir (SessionLifecycle üretir; middleware okur).
            'csrf_token' => $this->csrfToken(),
        ];

        // P1-9 (B-F-01): hybrid auth — API istemcisine RS256 access token üret.
        // jti user_tokens'ta saklanır (sha256) → logout ile revocation edilebilir.
        // Hata durumunda login YINE de başarılıdır; token alanı eklenmez (fail-safe
        // hata: imzalı token DB'ye yazılamadan dağıtılmaz).
        $tokenData = $this->tokenService->buildAuthPayload($result, $body);
        if ($tokenData !== []) {
            $payload = array_merge($payload, $tokenData);
        }

        return $payload;
    }

    /** Session'daki CSRF token (SessionLifecycle üretir; boşsa boş döner). */
    private function csrfToken(): string
    {
        $token = $this->session->get('csrf_token');

        return is_string($token) ? $token : '';
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
    // Primitives (success/error/body/clientIp) — AbstractController'a taşındı (Faz 3).
}