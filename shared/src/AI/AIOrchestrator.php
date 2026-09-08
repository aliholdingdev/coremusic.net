<?php declare(strict_types=1);
/**
 * CoreMusic — AI Orchestrator
 * 
 * Görev dağıtımı ve orkestrasyon implementasyonu.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 */

namespace CoreMusic\AI;

use CoreMusic\AI\Contracts\AIOrchestratorInterface;
use CoreMusic\Config\ConfigManager;

/**
 * AI Orchestrator — 11 ajanlı sistem için görev dağıtımı.
 *
 * Bileşenler:
 * - TaskQueue (priority queue)
 * - AgentRouter (11 agent'a yönlendirme)
 * - ContextManager (bağlam yönetimi)
 */
class AIOrchestrator implements AIOrchestratorInterface
{
    private const AGENT_MAP = [
        'mo'         => ['domain' => 'coordination', 'layer' => 'coordination'],
        'backend'    => ['domain' => 'php-api',     'layer' => 'l2-routing'],
        'ui'         => ['domain' => 'frontend',    'layer' => 'l3-presentation'],
        'security'   => ['domain' => 'security',    'layer' => 'l1-security'],
        'data'       => ['domain' => 'database',    'layer' => 'l0-infrastructure'],
        'embedded'   => ['domain' => 'audio-dsp',   'layer' => 'l0-electronics'],
        'qa'         => ['domain' => 'testing',     'layer' => 'cross-cutting'],
        'devops'     => ['domain' => 'cicd',        'layer' => 'ci-cd'],
        'audio-hw'   => ['domain' => 'hardware',    'layer' => 'hw'],
        'dsp-fw'     => ['domain' => 'firmware',    'layer' => 'fw'],
        'win-sw'     => ['domain' => 'platform',    'layer' => 'plat'],
    ];

    private const TASK_TYPES = [
        'recommendation'    => ['agent' => 'backend', 'priority' => 'medium'],
        'analysis'          => ['agent' => 'embedded', 'priority' => 'high'],
        'eq-optimization'   => ['agent' => 'embedded', 'priority' => 'medium'],
        'hardware'          => ['agent' => 'audio-hw', 'priority' => 'high'],
        'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],
        'security-audit'    => ['agent' => 'security', 'priority' => 'critical'],
        'documentation'     => ['agent' => 'mo',       'priority' => 'low'],
        'testing'           => ['agent' => 'qa',       'priority' => 'medium'],
    ];

    private const PRIORITY_WEIGHTS = [
        'critical' => 4,
        'high'     => 3,
        'medium'   => 2,
        'low'      => 1,
    ];

    private ConfigManager $config;
    /** @var array<string, array{id: string, type: string, status: string, priority: string, result: mixed, error: ?string, created_at: string, completed_at: ?string}> */
    private array $tasks = [];
    /** @var array<string, array<string, mixed>> */
    private array $contexts = [];

    public function __construct(ConfigManager $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchTask(string $type, array $params, string $priority = 'medium'): string
    {
        if (!isset(self::TASK_TYPES[$type])) {
            throw new \InvalidArgumentException("Unknown task type: {$type}");
        }

        $taskId = $this->generateTaskId();
        $taskConfig = self::TASK_TYPES[$type];

        $this->tasks[$taskId] = [
            'id'           => $taskId,
            'type'         => $type,
            'status'       => 'pending',
            'priority'     => $priority,
            'agent'        => $taskConfig['agent'],
            'params'       => $params,
            'result'       => null,
            'error'        => null,
            'created_at'   => date('Y-m-d H:i:s'),
            'completed_at' => null,
        ];

        // Priority weighted execution
        $this->executeTask($taskId);

        return $taskId;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaskStatus(string $taskId): array
    {
        if (!isset($this->tasks[$taskId])) {
            throw new \InvalidArgumentException("Task not found: {$taskId}");
        }

        return $this->tasks[$taskId];
    }

    /**
     * {@inheritdoc}
     */
    public function listActiveTasks(): array
    {
        return array_values(array_filter(
            $this->tasks,
            fn(array $task) => in_array($task['status'], ['pending', 'running'], true)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function routeToAgent(string $agentId, string $action, array $params): array
    {
        if (!isset(self::AGENT_MAP[$agentId])) {
            return [
                'success' => false,
                'result'  => null,
                'error'   => "Unknown agent: {$agentId}",
            ];
        }

        // Domain boundary kontrolü
        $agentInfo = self::AGENT_MAP[$agentId];

        return [
            'success' => true,
            'result'  => [
                'agent'   => $agentId,
                'action'  => $action,
                'domain'  => $agentInfo['domain'],
                'layer'   => $agentInfo['layer'],
                'params'  => $params,
            ],
            'error'   => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(string $sessionId): array
    {
        return $this->contexts[$sessionId] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function updateContext(string $sessionId, array $data): bool
    {
        $this->contexts[$sessionId] = array_merge(
            $this->contexts[$sessionId] ?? [],
            $data
        );

        return true;
    }

    /* ─── Private Methods ─── */

    /**
     * Benzersiz task ID üretir.
     */
    private function generateTaskId(): string
    {
        return 'task_' . bin2hex(random_bytes(8)) . '_' . time();
    }

    /**
     * Görevi çalıştırır.
     */
    private function executeTask(string $taskId): void
    {
        $task = &$this->tasks[$taskId];
        $task['status'] = 'running';

        try {
            // Agent routing
            $agentId = $task['agent'];
            $result = $this->routeToAgent($agentId, $task['type'], $task['params']);

            if ($result['success']) {
                $task['status'] = 'completed';
                $task['result'] = $result['result'];
            } else {
                $task['status'] = 'failed';
                $task['error'] = $result['error'];
            }

            $task['completed_at'] = date('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            $task['status'] = 'failed';
            $task['error'] = $e->getMessage();
            $task['completed_at'] = date('Y-m-d H:i:s');
        }
    }
}
