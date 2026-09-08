<?php declare(strict_types=1);
/**
 * CoreMusic — AI Orchestrator Interface
 * 
 * Görev dağıtımı ve orkestrasyon arayüzü.
 *
 * @package CoreMusic\AI\Contracts
 * @version 1.0.0
 */

namespace CoreMusic\AI\Contracts;

interface AIOrchestratorInterface
{
    /**
     * Yeni AI görevi ekler.
     *
     * @param string $type     Görev tipi (recommendation, analysis, eq-optimization, hardware, fault)
     * @param array<string, mixed> $params Görev parametreleri
     * @param string $priority Öncelik (critical, high, medium, low)
     * @return string Görev ID'si
     */
    public function dispatchTask(string $type, array $params, string $priority = 'medium'): string;

    /**
     * Görev durumunu sorgular.
     *
     * @param string $taskId Görev ID'si
     * @return array{id: string, status: string, result: mixed, error: ?string, created_at: string, completed_at: ?string}
     */
    public function getTaskStatus(string $taskId): array;

    /**
     * Tüm aktif görevleri listeler.
     *
     * @return array<int, array{id: string, type: string, status: string, priority: string}>
     */
    public function listActiveTasks(): array;

    /**
     * Belirli bir agent'a yönlendirme yapar.
     *
     * @param string $agentId Agent ID'si (mo, backend, ui, security, data, embedded, qa, devops, audio-hw, dsp-fw, win-sw)
     * @param string $action  Gerçekleştirilecek eylem
     * @param array<string, mixed> $params Eylem parametreleri
     * @return array{success: bool, result: mixed, error: ?string}
     */
    public function routeToAgent(string $agentId, string $action, array $params): array;

    /**
     * Context yönetimi — mevcut bağlamı getirir/günceller.
     *
     * @param string $sessionId Oturum ID'si
     * @return array<string, mixed> Mevcut bağlam
     */
    public function getContext(string $sessionId): array;

    /**
     * Context'i günceller.
     *
     * @param string $sessionId Oturum ID'si
     * @param array<string, mixed> $data Güncellenecek veri
     * @return bool Başarı durumu
     */
    public function updateContext(string $sessionId, array $data): bool;
}
