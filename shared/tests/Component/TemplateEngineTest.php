<?php declare(strict_types=1);

namespace CoreMusic\Test\Component;

use CoreMusic\Component\TemplateEngine;
use PHPUnit\Framework\TestCase;

/**
 * TemplateEngineTest — renderString (eval'siz cache dosyası + require) ve render() (require) akışları.
 *
 * Guardrail §21 / ADR-091 regresyon kanıtı:
 *  - eval kullanımı yok, davranış aynı (extract + ob_start + exception yeniden yayma)
 *  - geçici cache dosyası her yolda temizlenir
 *  - renderString fail-closed: `<?php` / `<?=` içeren şablon reddedilir (RCE yüzeyi)
 */
final class TemplateEngineTest extends TestCase
{
    /** Sızıntı avında aranan benzersiz işaret. */
    private const LEAK_MARKER = '__CM_TEMPLATE_LEAK_MARKER__';

    /**
     * Temp dizindeki TemplateEngine cache dosyalarını listeler.
     *
     * @return list<string>
     */
    private function tempCacheFiles(): array
    {
        $files = glob(rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_*.php');

        return is_array($files) ? $files : [];
    }

    public function testRenderStringRendersPlainHtmlTemplate(): void
    {
        $engine = new TemplateEngine();

        $this->assertSame(
            '<p>Hello CoreMusic</p>',
            $engine->renderString('<p>Hello CoreMusic</p>')
        );
    }

    /**
     * renderString GÜVENLİK (fail-closed): `<?=` / `<?php` içeren şablon reddedilir —
     * şablonlar yalnız kaynak koddan gelir, user input yasaktır.
     */
    public function testRenderStringRejectsPhpTags(): void
    {
        $engine = new TemplateEngine();

        $rejected = [
            'short_echo' => 'Hi <?= htmlspecialchars($name, ENT_QUOTES) ?>!',
            'php_tag'    => '<?php echo "unterminated ' . self::LEAK_MARKER,
            'php_tag_uppercase' => '<?PHP echo 1;',
        ];

        foreach ($rejected as $label => $template) {
            try {
                $engine->renderString($template, ['name' => 'Ada & Co']);
                $this->fail("PHP içeren şablon reddedilmeli ({$label})");
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('user input yasaktır', $e->getMessage());
            }
        }
    }

    public function testRenderStringRejectedTemplateWritesNoTempFile(): void
    {
        $engine = new TemplateEngine();
        $before = $this->tempCacheFiles();

        try {
            $engine->renderString('<?php echo "unterminated ' . self::LEAK_MARKER);
            $this->fail('Bozuk şablon bir Throwable fırlatmalıdır');
        } catch (\InvalidArgumentException $e) {
            // Guard dosyayı yazmadan önce tetiklenir → yazma/yürütme yok.
            $this->assertStringContainsString('PHP kodu içeremez', $e->getMessage());
        }

        $after = $this->tempCacheFiles();
        $leaked = [];
        foreach ($after as $file) {
            if (in_array($file, $before, true)) {
                continue;
            }
            $body = @file_get_contents($file);
            if (is_string($body) && str_contains($body, self::LEAK_MARKER)) {
                $leaked[] = $file;
            }
        }

        $this->assertSame([], $leaked, 'Reddedilen şablondan sonra geçici cache dosyası kalmamalı');
    }

    public function testRenderUsesTemplateFilePathWithoutRegression(): void
    {
        $engine = new TemplateEngine();
        $file = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cm_render_' . bin2hex(random_bytes(8)) . '.php';
        file_put_contents($file, '<div><?= htmlspecialchars($title, ENT_QUOTES) ?></div>');

        try {
            $this->assertSame(
                '<div>Şarkı Listesi</div>',
                $engine->render($file, ['title' => 'Şarkı Listesi'])
            );
        } finally {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    public function testRenderMissingTemplateFileThrowsRuntimeException(): void
    {
        $engine = new TemplateEngine();

        $this->expectException(\RuntimeException::class);
        $engine->render(rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cm_missing_template.php');
    }
}
