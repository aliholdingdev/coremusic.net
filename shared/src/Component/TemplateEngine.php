<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * TemplateEngine — Basit PHP template motoru.
 *
 * ob_start + extract ile template'leri render eder.
 * Global template fonksiyonları destekler (formatDate, formatDuration vb.).
 *
 * @package CoreMusic\Component
 */
final class TemplateEngine
{
    /**
     * Global template fonksiyonları — tüm template'lerde kullanılabilir.
     *
     * @var array<string, callable>
     */
    private array $functions = [];

    /**
     * Template dosyasını render eder.
     * Variable'lar extract ile PHP scope'a enjekte edilir.
     *
     * @param string                $templatePath Template dosya yolu
     * @param array<string, mixed> $data         Template değişkenleri
     *
     * @return string Render edilmiş HTML
     *
     * @throws \RuntimeException Dosya bulunamazsa
     */
    public function render(string $templatePath, array $data = []): string
    {
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$templatePath}");
        }

        // Global fonksiyonları data'ya ekle
        $data['__functions'] = $this->functions;

        ob_start();
        try {
            extract($data, EXTR_SKIP);
            require $templatePath;
            return (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Inline string template render eder (dosya gerekmez).
     *
     * @param string                $template PHP template kodu
     * @param array<string, mixed> $data     Template değişkenleri
     *
     * @return string Render edilmiş HTML
     */
    public function renderString(string $template, array $data = []): string
    {
        $data['__functions'] = $this->functions;

        ob_start();
        try {
            extract($data, EXTR_SKIP);
            eval('?>' . $template);
            return (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Global template fonksiyonu kaydeder.
     *
     * @param string   $name Fonksiyon adı (template'de $formatDate(...) olarak çağrılır)
     * @param callable $fn   Fonksiyon referansı
     */
    public function registerFunction(string $name, callable $fn): void
    {
        $this->functions[$name] = $fn;
    }

    /**
     * Toplu fonksiyon kaydı.
     *
     * @param array<string, callable> $functions name => callable mapping
     */
    public function registerFunctions(array $functions): void
    {
        foreach ($functions as $name => $fn) {
            $this->registerFunction($name, $fn);
        }
    }

    /**
     * Kayıtlı fonksiyonları döndürür.
     *
     * @return array<string, callable>
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Cache'i temizler (gelecek optimizasyon için hazır alan).
     */
    public function clearCache(): void
    {
        // Şimdilik no-op — opcache invalidation eklenebilir
    }
}
