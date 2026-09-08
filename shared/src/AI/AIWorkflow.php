<?php declare(strict_types=1);
/**
 * CoreMusic — AI Workflow
 * 
 * AI iş akış süreçleri implementasyonu.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 */

namespace CoreMusic\AI;

/**
 * AI Workflow — Recommendation, Audio Analysis, AutoEQ, Hardware, Fault workflows.
 *
 * Her workflow: trigger → process → result → feedback
 */
class AIWorkflow
{
    private AIEngine $engine;
    private AIOrchestrator $orchestrator;
    private KnowledgeBase $knowledge;
    private PromptEngine $promptEngine;
    private MemorySystem $memory;
    private ToolCalling $toolCalling;

    public function __construct(
        AIEngine $engine,
        AIOrchestrator $orchestrator,
        KnowledgeBase $knowledge,
        PromptEngine $promptEngine,
        MemorySystem $memory,
        ToolCalling $toolCalling
    ) {
        $this->engine = $engine;
        $this->orchestrator = $orchestrator;
        $this->knowledge = $knowledge;
        $this->promptEngine = $promptEngine;
        $this->memory = $memory;
        $this->toolCalling = $toolCalling;
    }

    /**
     * Müzik önerisi workflow'u.
     *
     * @param array<string, mixed> $request Kullanıcı isteği
     * @return array{recommendations: array, metadata: array, execution_time: float}
     */
    public function recommendationWorkflow(array $request): array
    {
        $startTime = microtime(true);
        $sessionId = $request['session_id'] ?? 'anonymous';

        // 1. Context collect — session'dan tercihleri çek
        $context = $this->memory->getSession($sessionId, 'preferences') ?? [];
        $context = array_merge($context, $request);

        // 2. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');

        // 3. Knowledge query — benzer önerileri ara
        $query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');
        $knowledgeResults = $this->knowledge->search($query, 5);

        // 4. Prompt generate
        $promptResult = $this->promptEngine->generatePrompt('recommendation', [
            'mood'   => $context['mood'] ?? 'neutral',
            'genre'  => $context['genre'] ?? 'any',
            'energy' => $context['energy'] ?? 0.5,
            'history' => json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'limit'  => $context['limit'] ?? 10,
        ], ['knowledge' => $knowledgeResults]);

        // 5. Recommendation engine
        $recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);

        // 6. Feedback — sonucu kaydet
        $this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);

        $executionTime = microtime(true) - $startTime;

        return [
            'recommendations' => $recommendations,
            'metadata'        => [
                'task_id'        => $taskId,
                'prompt_tokens'  => $promptResult['tokenCount'],
                'knowledge_hits' => count($knowledgeResults),
            ],
            'execution_time'  => round($executionTime, 4),
        ];
    }

    /**
     * Ses analizi workflow'u.
     *
     * @param array<string, mixed> $request Dosya bilgileri
     * @return array{analysis: array, metadata: array, execution_time: float}
     */
    public function audioAnalysisWorkflow(array $request): array
    {
        $startTime = microtime(true);
        $filePath = $request['file_path'] ?? '';
        $trackId = $request['track_id'] ?? null;

        // 1. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('analysis', $request, 'high');

        // 2. Audio analysis
        $analysis = $this->engine->analyzeAudio($filePath);

        // 3. Knowledge enrich — benzer şarkıları bul
        $genre = $analysis['genre'] ?? '';
        $query = "{$analysis['key']} {$analysis['mood']} {$genre}";
        $knowledgeResults = $this->knowledge->search($query, 3);

        // 4. DB'ye kaydet (track_id varsa)
        if ($trackId !== null) {
            $this->toolCalling->callTool('db-query', [
                'sql'      => "UPDATE tracks SET bpm = {$analysis['bpm']}, key_sig = '{$analysis['key']}', energy = {$analysis['energy']} WHERE id = {$trackId}",
                'database' => 'coremusic_musics',
            ]);
        }

        $executionTime = microtime(true) - $startTime;

        return [
            'analysis'       => $analysis,
            'metadata'       => [
                'task_id'        => $taskId,
                'knowledge_hits' => count($knowledgeResults),
            ],
            'execution_time' => round($executionTime, 4),
        ];
    }

    /**
     * Otomatik EQ workflow'u.
     *
     * @param array<string, mixed> $request Ortam profili
     * @return array{eq_curve: array, metadata: array, execution_time: float}
     */
    public function autoEQWorkflow(array $request): array
    {
        $startTime = microtime(true);
        $sessionId = $request['session_id'] ?? 'anonymous';
        $deviceId = $request['device_id'] ?? null;

        // 1. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('eq-optimization', $request, 'medium');

        // 2. Room profile分析
        $roomProfile = [
            'size'               => $request['room_size'] ?? 'medium',
            'reverb'             => $request['reverb'] ?? 0.5,
            'frequency_response' => $request['frequency_response'] ?? [],
        ];

        // 3. EQ optimize
        $eqCurve = $this->engine->optimizeEQ($roomProfile);

        // 4. Device-specific ayarlar (varsa)
        if ($deviceId !== null) {
            $hardwareAnalysis = $this->engine->analyzeHardware($deviceId);
            // Cihaz özelliklerine göre EQ'yu ayarla
        }

        // 5. Cache'le
        $cacheKey = "eq_{$sessionId}_" . md5(serialize($roomProfile));
        $this->memory->remember($cacheKey, fn() => $eqCurve, 'l2', 3600);

        // 6. Prompt generate (LLM için)
        $promptResult = $this->promptEngine->generatePrompt('eq-optimization', [
            'room_profile' => json_encode($roomProfile, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ]);

        $executionTime = microtime(true) - $startTime;

        return [
            'eq_curve'       => $eqCurve,
            'metadata'       => [
                'task_id'       => $taskId,
                'prompt_tokens' => $promptResult['tokenCount'],
            ],
            'execution_time' => round($executionTime, 4),
        ];
    }

    /**
     * Donanım analiz workflow'u.
     *
     * @param array<string, mixed> $request Cihaz bilgileri
     * @return array{analysis: array, metadata: array, execution_time: float}
     */
    public function hardwareAnalysisWorkflow(array $request): array
    {
        $startTime = microtime(true);
        $deviceId = $request['device_id'] ?? '';

        // 1. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('hardware', $request, 'high');

        // 2. Hardware analysis
        $analysis = $this->engine->analyzeHardware($deviceId);

        // 3. Fault prediction
        $faultPrediction = $this->engine->predictFault([
            'snr'          => $analysis['snr'],
            'thd'          => $analysis['thd'],
            'temperature'  => $analysis['temperature'],
            'usage_hours'  => $request['usage_hours'] ?? 0,
        ]);

        // 4. Knowledge enrich
        $query = "{$analysis['status']} hardware {$deviceId}";
        $knowledgeResults = $this->knowledge->search($query, 3);

        $executionTime = microtime(true) - $startTime;

        return [
            'analysis'       => $analysis,
            'fault_prediction' => $faultPrediction,
            'metadata'       => [
                'task_id'        => $taskId,
                'knowledge_hits' => count($knowledgeResults),
            ],
            'execution_time' => round($executionTime, 4),
        ];
    }

    /**
     * Hata tahmini workflow'u.
     *
     * @param array<string, mixed> $request Cihaz metrikleri
     * @return array{prediction: array, metadata: array, execution_time: float}
     */
    public function faultPredictionWorkflow(array $request): array
    {
        $startTime = microtime(true);

        // 1. Task dispatch (critical priority)
        $taskId = $this->orchestrator->dispatchTask('fault', $request, 'critical');

        // 2. Fault prediction
        $prediction = $this->engine->predictFault($request);

        // 3. Alert yönetimi
        if ($prediction['risk'] === 'high') {
            // Güvenlik agent'ına bildir
            $this->orchestrator->routeToAgent('security', 'alert', [
                'type'    => 'hardware_fault',
                'severity' => 'high',
                'details'  => $prediction,
            ]);
        }

        $executionTime = microtime(true) - $startTime;

        return [
            'prediction'     => $prediction,
            'metadata'       => [
                'task_id' => $taskId,
            ],
            'execution_time' => round($executionTime, 4),
        ];
    }
}
