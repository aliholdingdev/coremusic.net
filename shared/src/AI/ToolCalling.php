<?php declare(strict_types=1);
/**
 * CoreMusic — Tool Calling
 * 
 * Dış servis ve araç çağrısı implementasyonu.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 */

namespace CoreMusic\AI;

use CoreMusic\AI\Contracts\ToolCallingInterface;

/**
 * Tool Calling — Tool registry, executor, permission check.
 *
 * Kategoriler:
 * - file: Dosya işlemleri
 * - database: Veritabanı işlemleri
 * - api: HTTP/WS çağrıları
 * - audio: Ses işlemleri
 * - hardware: Donanım işlemleri
 * - security: Güvenlik işlemleri
 */
class ToolCalling implements ToolCallingInterface
{
    private const AGENT_PERMISSIONS = [
        'mo'       => ['file', 'database', 'api', 'audio', 'hardware', 'security'],
        'backend'  => ['file', 'database', 'api'],
        'ui'       => ['file', 'api'],
        'security' => ['file', 'database', 'api', 'security'],
        'data'     => ['file', 'database'],
        'embedded' => ['file', 'hardware', 'audio'],
        'qa'       => ['file', 'database', 'api'],
        'devops'   => ['file', 'api', 'database'],
        'audio-hw' => ['file', 'hardware', 'audio'],
        'dsp-fw'   => ['file', 'hardware', 'audio'],
        'win-sw'   => ['file', 'hardware'],
    ];

    /** @var array<string, array{name: string, description: string, parameters: array<int, array{name: string, type: string, required: bool, description: string}>, executor: callable, category: string}> */
    private array $tools = [];

    public function __construct()
    {
        $this->registerDefaultTools();
    }

    /**
     * {@inheritdoc}
     */
    public function registerTool(string $name, string $description, array $parameters, callable $executor): bool
    {
        if (isset($this->tools[$name])) {
            throw new \InvalidArgumentException("Tool already registered: {$name}");
        }

        $this->tools[$name] = [
            'name'        => $name,
            'description' => $description,
            'parameters'  => $parameters,
            'executor'    => $executor,
            'category'    => $this->inferCategory($name),
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function callTool(string $toolName, array $params): array
    {
        if (!isset($this->tools[$toolName])) {
            return [
                'success'       => false,
                'result'        => null,
                'error'         => "Tool not found: {$toolName}",
                'executionTime' => 0.0,
            ];
        }

        $tool = $this->tools[$toolName];
        $startTime = microtime(true);

        try {
            // Parametre validasyonu
            $this->validateParameters($tool['parameters'], $params);

            // Tool'u çalıştır
            $result = ($tool['executor'])($params);
            $executionTime = microtime(true) - $startTime;

            return [
                'success'       => true,
                'result'        => $result,
                'error'         => null,
                'executionTime' => round($executionTime, 4),
            ];
        } catch (\Throwable $e) {
            $executionTime = microtime(true) - $startTime;

            return [
                'success'       => false,
                'result'        => null,
                'error'         => $e->getMessage(),
                'executionTime' => round($executionTime, 4),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function listTools(): array
    {
        return array_map(
            fn(array $t) => [
                'name'        => $t['name'],
                'description' => $t['description'],
                'parameters'  => $t['parameters'],
                'category'    => $t['category'],
            ],
            $this->tools
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getToolsByCategory(string $category): array
    {
        $filtered = array_filter($this->tools, fn(array $t) => $t['category'] === $category);

        return array_map(
            fn(array $t) => ['name' => $t['name'], 'description' => $t['description']],
            $filtered
        );
    }

    /**
     * {@inheritdoc}
     */
    public function checkPermission(string $toolName, string $agentId): array
    {
        if (!isset($this->tools[$toolName])) {
            return ['allowed' => false, 'reason' => "Tool not found: {$toolName}"];
        }

        if (!isset(self::AGENT_PERMISSIONS[$agentId])) {
            return ['allowed' => false, 'reason' => "Unknown agent: {$agentId}"];
        }

        $toolCategory = $this->tools[$toolName]['category'];
        $allowedCategories = self::AGENT_PERMISSIONS[$agentId];

        if (!in_array($toolCategory, $allowedCategories, true)) {
            return [
                'allowed' => false,
                'reason'  => "Agent '{$agentId}' not allowed to use category '{$toolCategory}' tools",
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /* ─── Private Methods ─── */

    /**
     * Varsayılan tool'ları kaydeder.
     */
    private function registerDefaultTools(): void
    {
        // File tools
        $this->tools['file-read'] = [
            'name'        => 'file-read',
            'description' => 'Dosya okur',
            'parameters'  => [
                ['name' => 'path', 'type' => 'string', 'required' => true, 'description' => 'Dosya yolu'],
            ],
            'executor'    => function(array $params): array {
                $path = $params['path'];
                if (!file_exists($path)) {
                    throw new \RuntimeException("File not found: {$path}");
                }
                return ['content' => file_get_contents($path), 'size' => filesize($path)];
            },
            'category'    => 'file',
        ];

        $this->tools['file-write'] = [
            'name'        => 'file-write',
            'description' => 'Dosya yazar',
            'parameters'  => [
                ['name' => 'path', 'type' => 'string', 'required' => true, 'description' => 'Dosya yolu'],
                ['name' => 'content', 'type' => 'string', 'required' => true, 'description' => 'Dosya içeriği'],
            ],
            'executor'    => function(array $params): array {
                $result = file_put_contents($params['path'], $params['content']);
                return ['bytes_written' => $result];
            },
            'category'    => 'file',
        ];

        $this->tools['file-search'] = [
            'name'        => 'file-search',
            'description' => 'Dosya arar',
            'parameters'  => [
                ['name' => 'pattern', 'type' => 'string', 'required' => true, 'description' => 'Glob pattern'],
                ['name' => 'path', 'type' => 'string', 'required' => false, 'description' => 'Arama dizini'],
            ],
            'executor'    => function(array $params): array {
                $path = $params['path'] ?? '.';
                $files = glob($path . '/' . $params['pattern']);
                return ['files' => $files ?? [], 'count' => count($files ?? [])];
            },
            'category'    => 'file',
        ];

        // Database tools
        $this->tools['db-query'] = [
            'name'        => 'db-query',
            'description' => 'Veritabanı sorgusu çalıştırır (SELECT only)',
            'parameters'  => [
                ['name' => 'sql', 'type' => 'string', 'required' => true, 'description' => 'SQL sorgusu'],
                ['name' => 'database', 'type' => 'string', 'required' => true, 'description' => 'Veritabanı adı'],
            ],
            'executor'    => function(array $params): array {
                // Güvenlik: Sadece SELECT izni
                $sql = trim($params['sql']);
                if (stripos($sql, 'SELECT') !== 0) {
                    throw new \RuntimeException("Only SELECT queries are allowed");
                }
                return ['result' => [], 'rows' => 0];
            },
            'category'    => 'database',
        ];

        // API tools
        $this->tools['http-request'] = [
            'name'        => 'http-request',
            'description' => 'HTTP isteği gönderir',
            'parameters'  => [
                ['name' => 'method', 'type' => 'string', 'required' => true, 'description' => 'HTTP metodu (GET, POST, PUT, DELETE)'],
                ['name' => 'url', 'type' => 'string', 'required' => true, 'description' => 'URL'],
                ['name' => 'body', 'type' => 'string', 'required' => false, 'description' => 'Request body (JSON)'],
            ],
            'executor'    => function(array $params): array {
                $method = strtoupper($params['method']);
                $url = $params['url'];

                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST  => $method,
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                ]);

                if (isset($params['body'])) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body']);
                }

                $response = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    throw new \RuntimeException("HTTP error: {$error}");
                }

                return [
                    'status_code' => $statusCode,
                    'body'        => $response,
                ];
            },
            'category'    => 'api',
        ];
    }

    /**
     * Tool adından kategori tahmin eder.
     */
    private function inferCategory(string $toolName): string
    {
        if (str_starts_with($toolName, 'file-')) return 'file';
        if (str_starts_with($toolName, 'db-')) return 'database';
        if (str_starts_with($toolName, 'http-')) return 'api';
        if (str_contains($toolName, 'audio')) return 'audio';
        if (str_contains($toolName, 'device') || str_contains($toolName, 'hardware')) return 'hardware';
        if (str_contains($toolName, 'auth') || str_contains($toolName, 'security')) return 'security';

        return 'file';
    }

    /**
     * Parametreleri doğrular.
     *
     * @param array<int, array{name: string, type: string, required: bool}> $definitions
     * @param array<string, mixed> $params
     */
    private function validateParameters(array $definitions, array $params): void
    {
        foreach ($definitions as $def) {
            if ($def['required'] && !array_key_exists($def['name'], $params)) {
                throw new \InvalidArgumentException("Missing required parameter: {$def['name']}");
            }

            if (array_key_exists($def['name'], $params)) {
                $value = $params[$def['name']];
                $type = get_debug_type($value);

                if ($type !== $def['type'] && $def['type'] !== 'mixed') {
                    throw new \InvalidArgumentException(
                        "Parameter '{$def['name']}' expects type '{$def['type']}', got '{$type}'"
                    );
                }
            }
        }
    }
}
