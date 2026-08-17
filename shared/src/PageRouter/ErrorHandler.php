<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class ErrorHandler
{
    public function __construct(
        private readonly HtmlShellRenderer $shellRenderer
    ) {}

    public function buildErrorPage(array $errorInfo, string $csrfToken): string
    {
        $errorType    = $errorInfo['errorType'];
        $errorMessage = $errorInfo['errorMessage'];
        $route        = $errorInfo['route'];

        $errorTitle = match ($errorType) {
            'not_found' => '404 — Sayfa Bulunamadı',
            'forbidden' => '403 — Erişim Yasak',
            'gone'      => '410 — Kaldırıldı',
            'error'     => '500 — Sunucu Hatası',
            default     => 'Hata',
        };

        $container = '<div class="error-page">'
            . '<h1>' . htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') . '</h1>'
            . '<p>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><a href="/" class="btn">Ana Sayfaya Dön</a></p>'
            . '</div>';

        $meta = ['title' => $errorTitle];
        return $this->shellRenderer->render($container, $route, $meta, $csrfToken, []);
    }

    public function getErrorInfo(string $errorType, string $route): array
    {
        $messages = [
            'not_found' => 'Aradığınız sayfa bulunamadı.',
            'forbidden' => 'Bu sayfaya erişim yetkiniz bulunmuyor.',
            'gone'      => 'Bu içerik kalıcı olarak kaldırılmıştır.',
            'error'     => 'Sunucuda bir hata oluştu.',
        ];

        return [
            'errorType'    => $errorType,
            'errorMessage' => $messages[$errorType] ?? 'Bilinmeyen hata.',
            'route'        => $route,
        ];
    }

    public function buildErrorHtmlResult(int $status, array $body = []): array
    {
        $message = match ($status) {
            403     => 'Bu sayfaya erişim yetkiniz yok.',
            404     => 'Aradığınız sayfa bulunamadı.',
            429     => 'Çok fazla istek gönderildi. Lütfen bekleyin.',
            default => 'Bir hata oluştu. Lütfen tekrar deneyin.',
        };

        $html = "<!doctype html><html lang=\"tr\"><head><meta charset=\"utf-8\">"
            . "<title>Hata {$status}</title></head><body>"
            . "<div style=\"text-align:center;padding:4rem;font-family:sans-serif\">"
            . "<h1>{$status}</h1><p>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>"
            . "</div></body></html>";

        return [
            'httpStatus' => $status,
            'type'       => 'html',
            'body'       => $html,
            'headers'    => [],
        ];
    }
}
