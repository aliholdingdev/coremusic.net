<?php declare(strict_types=1);
/**
 * CoreMusic — Prompt Engine Interface
 * 
 * Prompt üretim ve yönetim motoru arayüzü.
 *
 * @package CoreMusic\AI\Contracts
 * @version 1.0.0
 */

namespace CoreMusic\AI\Contracts;

interface PromptEngineInterface
{
    /**
     * Prompt üretir.
     *
     * @param string $templateName Şablon adı
     * @param array<string, mixed> $variables Şablon değişkenleri
     * @param array<string, mixed> $context   Ek bağlam
     * @return array{prompt: string, tokenCount: int, estimatedCost: float}
     */
    public function generatePrompt(string $templateName, array $variables, array $context = []): array;

    /**
     * Token sayısını hesaplar.
     *
     * @param string $text Metin
     * @return int Token sayısı
     */
    public function countTokens(string $text): int;

    /**
     * Prompt'u optimize eder (token bütçesine sığdırır).
     *
     * @param string $prompt Ham prompt
     * @param int    $maxTokens Maksimum token bütçesi
     * @return array{optimized: string, tokenCount: int, wasCompressed: bool}
     */
    public function optimizePrompt(string $prompt, int $maxTokens): array;

    /**
     * Template listeler.
     *
     * @return array<int, array{name: string, category: string, description: string, tokenEstimate: int}>
     */
    public function listTemplates(): array;

    /**
     * Prompt validasyonu yapar (injection koruması dahil).
     *
     * @param string $prompt Kontrol edilecek prompt
     * @return array{valid: bool, risks: array<int, string>, sanitized: string}
     */
    public function validatePrompt(string $prompt): array;
}
