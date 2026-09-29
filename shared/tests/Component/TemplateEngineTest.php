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

    public function testRenderStringInjectsVariablesAndGlobalFunctions(): void
    {
        $engine = new TemplateEngine();
        $engine->registerFunction('shout', static fn (string $v): string => strtoupper($v));

        $html = $engine->renderString(
            'Hi <?= htmlspecialchars($name, ENT_QUOTES) ?>! <?= $__functions["shout"]($tag) ?>',
            ['name' => 'Ada & Co', 'tag' => 'music']
        );

        $this->assertSame('Hi Ada &amp; Co! MUSIC', $html);
    }

    public function testRenderStringBrokenTemplateThrowsAndCleansTempFile(): void
    {
        $engine = new TemplateEngine();
        $before = $this->tempCacheFiles();

        $broken = '<?php echo "unterminated ' . self::LEAK_MARKER;

        try {
            $engine->renderString($broken);
            $this->fail('Bozuk şablon bir Throwable fırlatmalıdır');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\ParseError::class, $e);
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

        $this->assertSame([], $leaked, 'Bozuk şablondan sonra geçici cache dosyası kalmamalı');
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
