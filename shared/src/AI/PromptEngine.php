<?php declare(strict_types=1);
/**
 * CoreMusic — Prompt Engine
 * 
 * Prompt üretim ve yönetim motoru implementasyonu.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 */

namespace CoreMusic\AI;

use CoreMusic\AI\Contracts\PromptEngineInterface;

/**
 * Prompt Engine — Prompt üretimi, token yönetimi, validasyon.
 *
 * Bileşenler:
 * - TemplateManager
 * - TokenCounter
 * - PromptOptimizer
 */
class PromptEngine implements PromptEngineInterface
{
    private const MAX_TOKENS_DEFAULT = 4096;
    private const COMPRESSION_THRESHOLD = 0.8;
    private const INJECTION_PATTERNS = [
        '/ignore\s+previous/i',
        '/you\s+are\s+now/i',
        '/system\s*:\s*/i',
        '/act\s+as/i',
        '/pretend\s+you/i',
        '/disregard/i',
        '/override/i',
    ];

    /** @var array<string, array{name: string, category: string, description: string, template: string, tokenEstimate: int}> */
    private array $templates = [];

    public function __construct()
    {
        $this->registerDefaultTemplates();
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrompt(string $templateName, array $variables, array $context = []): array
    {
        if (!isset($this->templates[$templateName])) {
            throw new \InvalidArgumentException("Template not found: {$templateName}");
        }

        $template = $this->templates[$templateName];

        // Template'i doldur
        $prompt = $template['template'];
        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $prompt = str_replace($placeholder, (string) $value, $prompt);
        }

        // Context enjeksiyonu
        if (!empty($context)) {
            $prompt .= "\n\n## Context\n";
            foreach ($context as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                }
                $prompt .= "- {$key}: {$value}\n";
            }
        }

        // Token sayımı
        $tokenCount = $this->countTokens($prompt);

        // Maliyet tahmini (basit model)
        $estimatedCost = $this->estimateCost($tokenCount);

        return [
            'prompt'        => $prompt,
            'tokenCount'    => $tokenCount,
            'estimatedCost' => $estimatedCost,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function countTokens(string $text): int
    {
        // Basit token sayımı — gerçek implementasyonda tokenizer kullanılacak
        // Ortalama 1 token ≈ 4 karakter (İngilizce) veya 2-3 karakter (Türkçe)
        $charCount = mb_strlen($text, 'UTF-8');
        return (int) ceil($charCount / 3.5);
    }

    /**
     * {@inheritdoc}
     */
    public function optimizePrompt(string $prompt, int $maxTokens): array
    {
        $tokenCount = $this->countTokens($prompt);

        if ($tokenCount <= $maxTokens) {
            return [
                'optimized'    => $prompt,
                'tokenCount'   => $tokenCount,
                'wasCompressed' => false,
            ];
        }

        // Compression stratejileri
        $optimized = $prompt;

        // 1. Gereksiz boşlukları temizle
        $optimized = preg_replace('/\s+/', ' ', $optimized);
        $optimized = trim($optimized);

        // 2. Uzun listeleri kısalt
        $optimized = $this->truncateLongLists($optimized, $maxTokens);

        // 3. Hala fazlaysa, context bölümünü kısalt
        if ($this->countTokens($optimized) > $maxTokens) {
            $optimized = $this->truncateContext($optimized, $maxTokens);
        }

        $newTokenCount = $this->countTokens($optimized);

        return [
            'optimized'    => $optimized,
            'tokenCount'   => $newTokenCount,
            'wasCompressed' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function listTemplates(): array
    {
        return array_map(
            fn(array $t) => [
                'name'           => $t['name'],
                'category'       => $t['category'],
                'description'    => $t['description'],
                'tokenEstimate'  => $t['tokenEstimate'],
            ],
            $this->templates
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validatePrompt(string $prompt): array
    {
        $risks = [];
        $sanitized = $prompt;

        // Injection pattern kontrolü
        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $prompt)) {
                $risks[] = "Potential prompt injection detected: {$pattern}";
                $sanitized = preg_replace($pattern, '[FILTERED]', $sanitized);
            }
        }

        // Token bütçesi kontrolü
        $tokenCount = $this->countTokens($prompt);
        $maxTokens = self::MAX_TOKENS_DEFAULT;
        if ($tokenCount > $maxTokens) {
            $risks[] = "Token count ({$tokenCount}) exceeds max ({$maxTokens})";
        }

        // Hassas veri kontrolü
        if (preg_match('/password|secret|api[_-]?key|token/i', $prompt)) {
            $risks[] = "Potential sensitive data in prompt";
        }

        return [
            'valid'      => empty($risks),
            'risks'      => $risks,
            'sanitized'  => $sanitized,
        ];
    }

    /* ─── Private Methods ─── */

    /**
     * Varsayılan şablonları kaydeder.
     */
    private function registerDefaultTemplates(): void
    {
        $this->templates['recommendation'] = [
            'name'        => 'recommendation',
            'category'    => 'music',
            'description' => 'Müzik öneri promptu',
            'template'    => <<<PROMPT
Sen CoreMusic müzik öneri sistemiassistansın.

Kullanıcı tercihleri:
- Mood: {{mood}}
- Genre: {{genre}}
- Energy: {{energy}}

Son dinlenen şarkılar: {{history}}

Lütfen {{limit}} adet şarkı öner. Her öneri için:
- Şarkı adı
- Sanatçı
- Öneri skoru (0-1)
- Öneri gerekçesi

PROMPT
,
            'tokenEstimate' => 200,
        ];

        $this->templates['audio-analysis'] = [
            'name'        => 'audio-analysis',
            'category'    => 'audio',
            'description' => 'Ses analizi promptu',
            'template'    => <<<PROMPT
Ses dosyasını analiz et:
- Dosya: {{file_path}}
- Format: {{format}}
- Süre: {{duration}}

Çıktı: BPM, key, energy, mood, danceability, acousticness
PROMPT
,
            'tokenEstimate' => 100,
        ];

        $this->templates['eq-optimization'] = [
            'name'        => 'eq-optimization',
            'category'    => 'audio',
            'description' => 'EQ optimizasyonu promptu',
            'template'    => <<<PROMPT
Ortam profili: {{room_profile}}
Hedef: Düz frekans tepkisi
31-band EQ eğrisi üret.

PROMPT
,
            'tokenEstimate' => 80,
        ];

        $this->templates['security-audit'] = [
            'name'        => 'security-audit',
            'category'    => 'security',
            'description' => 'Güvenlik denetimi promptu',
            'template'    => <<<PROMPT
OWASP Top 10:2025 kontrol listesi ile denetim yap:
- Hedef: {{target}}
- Kapsam: {{scope}}

Kontrol: CSRF, CSP, XSS, SQL Injection, Session, Auth, Rate Limit
PROMPT
,
            'tokenEstimate' => 150,
        ];
    }

    /**
     * Token maliyeti tahmin eder.
     */
    private function estimateCost(int $tokenCount): float
    {
        // Basit maliyet modeli — $0.002 / 1K token
        return round(($tokenCount / 1000) * 0.002, 6);
    }

    /**
     * Uzun listeleri kısaltır.
     */
    private function truncateLongLists(string $text, int $maxTokens): string
    {
        // Madde işaretli listeleri kısalt
        $lines = explode("\n", $text);
        $result = [];
        $inList = false;
        $listCount = 0;

        foreach ($lines as $line) {
            if (preg_match('/^[\s]*[-*•]/', $line)) {
                $inList = true;
                $listCount++;
                if ($listCount > 10) {
                    $result[] = '... (more items truncated)';
                    $inList = false;
                    continue;
                }
            } else {
                $inList = false;
            }
            $result[] = $line;
        }

        return implode("\n", $result);
    }

    /**
     * Context bölümünü kısaltır.
     */
    private function truncateContext(string $text, int $maxTokens): string
    {
        $contextStart = strpos($text, '## Context');
        if ($contextStart === false) {
            // Context yoksa son portion'u kes
            $chars = (int) ($maxTokens * 3.5 * 0.8);
            return mb_substr($text, 0, $chars, 'UTF-8') . "\n... (truncated)";
        }

        $before = mb_substr($text, 0, $contextStart, 'UTF-8');
        $contextSection = mb_substr($text, $contextStart, null, 'UTF-8');

        // Context bölümünü %50 kısalt
        $maxContextChars = (int) (($maxTokens * 3.5 * 0.3));
        $truncatedContext = mb_substr($contextSection, 0, $maxContextChars, 'UTF-8') . "\n... (context truncated)";

        return $before . $truncatedContext;
    }
}
