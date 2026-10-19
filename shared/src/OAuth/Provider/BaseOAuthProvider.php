<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Base OAuth Provider — Ortak OAuth 2.0 mantığı.
 *
 * Tüm provider'lar bu abstract class'ı extend eder.
 * HTTP istekleri için curl kullanılır (bağımlılık yok).
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
abstract class BaseOAuthProvider implements IOAuthProvider
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected array $config;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        array $config = []
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->config = $config;
    }

    public function getOAuthVersion(): string
    {
        return '2.0';
    }

    public function requiresPkce(): bool
    {
        return false;
    }

    public function getScopes(): array
    {
        return $this->config['scopes'] ?? [];
    }

    /**
     * PKCE code_verifier üret (X/Twitter için).
     */
    protected function generateCodeVerifier(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * PKCE code_challenge üret (S256 method).
     */
    protected function generateCodeChallenge(string $codeVerifier): string
    {
        return rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '=');
    }

    /**
     * State parametresi üret (CSRF koruması).
     */
    public function generateState(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * HTTP GET isteği yap.
     */
    protected function httpGet(string $url, array $headers = []): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("HTTP request failed: " . curl_error($ch));
        }

        $data = json_decode($response, true);
        if ($data === null) {
            throw new \RuntimeException("Invalid JSON response: {$response}");
        }

        return ['status' => $httpCode, 'data' => $data];
    }

    /**
     * HTTP POST isteği yap.
     */
    protected function httpPost(string $url, array $data, array $headers = []): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array_merge([
                'Content-Type: application/x-www-form-urlencoded',
            ], $headers),
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("HTTP request failed");
        }

        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new \RuntimeException("Invalid JSON response: {$response}");
        }

        return ['status' => $httpCode, 'data' => $decoded];
    }

    /**
     * Basic auth header'ı oluştur (Pinterest, Reddit için).
     */
    protected function getBasicAuthHeader(): string
    {
        return 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
    }
}
