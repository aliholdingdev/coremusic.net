<?php declare(strict_types=1);
/**
 * CoreMusic — Tool Calling Interface
 * 
 * Dış servis ve araç çağrısı arayüzü.
 *
 * @package CoreMusic\AI\Contracts
 * @version 1.0.0
 */

namespace CoreMusic\AI\Contracts;

interface ToolCallingInterface
{
    /**
     * Tool kaydı yapar.
     *
     * @param string $name        Tool adı
     * @param string $description Tool açıklaması
     * @param array<int, array{name: string, type: string, required: bool, description: string}> $parameters Parametre tanımları
     * @param callable $executor  Çalıştırıcı fonksiyon
     * @return bool Başarı durumu
     */
    public function registerTool(string $name, string $description, array $parameters, callable $executor): bool;

    /**
     * Tool çağrısı yapar.
     *
     * @param string $toolName   Tool adı
     * @param array<string, mixed> $params Parametreler
     * @return array{success: bool, result: mixed, error: ?string, executionTime: float}
     */
    public function callTool(string $toolName, array $params): array;

    /**
     * Kayıtlı tool'ları listeler.
     *
     * @return array<int, array{name: string, description: string, parameters: array<int, array{name: string, type: string}>, category: string}>
     */
    public function listTools(): array;

    /**
     * Tool kategorisi filtreler.
     *
     * @param string $category Kategori (file, database, api, audio, hardware, security)
     * @return array<int, array{name: string, description: string}>
     */
    public function getToolsByCategory(string $category): array;

    /**
     * Permission kontrolü yapar.
     *
     * @param string $toolName Tool adı
     * @param string $agentId  Agent ID'si
     * @return array{allowed: bool, reason: ?string}
     */
    public function checkPermission(string $toolName, string $agentId): array;
}
