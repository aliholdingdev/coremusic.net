<?php declare(strict_types=1);
/**
 * CoreMusic — Knowledge Base
 * 
 * Bilgi bankası implementasyonu — semantic search, RAG, knowledge lifecycle.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 */

namespace CoreMusic\AI;

use CoreMusic\AI\Contracts\KnowledgeBaseInterface;

/**
 * Knowledge Base — Semantic search, RAG, knowledge lifecycle.
 *
 * Bileşenler:
 * - SemanticSearch (vector DB)
 * - RAGPipeline (retrieval augmented generation)
 * - KnowledgeLifecycle
 */
class KnowledgeBase implements KnowledgeBaseInterface
{
    private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];
    private const MAX_SEARCH_RESULTS = 50;
    private const LIFECYCLE_MAX_AGE_DAYS = 365;

    /** @var array<string, array{id: string, title: string, content: string, category: string, metadata: array<string, string>, created_at: string, updated_at: string}> */
    private array $knowledge = [];
    private int $nextId = 1;

    /**
     * {@inheritdoc}
     */
    public function search(string $query, int $maxResults = 10, array $filters = []): array
    {
        $maxResults = min($maxResults, self::MAX_SEARCH_RESULTS);
        $queryLower = strtolower($query);
        $results = [];

        foreach ($this->knowledge as $id => $item) {
            // Kategori filtresi
            if (!empty($filters['category']) && $item['category'] !== $filters['category']) {
                continue;
            }

            // Basit keyword matching (gerçek implementasyonda vector cosine similarity)
            $score = $this->calculateRelevanceScore($queryLower, $item);

            if ($score > 0) {
                $results[] = [
                    'id'       => $item['id'],
                    'title'    => $item['title'],
                    'content'  => mb_substr($item['content'], 0, 500),
                    'score'    => $score,
                    'source'   => $item['metadata']['source'] ?? 'vault',
                    'category' => $item['category'],
                ];
            }
        }

        // Skora göre sırala
        usort($results, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $maxResults);
    }

    /**
     * {@inheritdoc}
     */
    public function ragQuery(string $query, array $context = []): array
    {
        // 1. Retrieval — ilgili belgeleri bul
        $retrieved = $this->search($query, 5, $context['filters'] ?? []);

        // 2. Augmentation — prompt'a bağlam ekle
        $augmentedContext = $this->augmentContext($query, $retrieved, $context);

        // 3. Generation — LLM ile yanıt üret (şimdilik placeholder)
        $answer = $this->generateAnswer($query, $augmentedContext);

        return [
            'answer'     => $answer,
            'sources'    => array_map(
                fn(array $r) => ['id' => $r['id'], 'title' => $r['title'], 'score' => $r['score']],
                $retrieved
            ),
            'confidence' => $this->calculateConfidence($retrieved),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string
    {
        if (!in_array($category, self::CATEGORIES, true)) {
            throw new \InvalidArgumentException("Invalid category: {$category}. Must be one of: " . implode(', ', self::CATEGORIES));
        }

        $id = 'kb_' . $this->nextId++;
        $now = date('Y-m-d H:i:s');

        $this->knowledge[$id] = [
            'id'        => $id,
            'title'     => $title,
            'content'   => $content,
            'category'  => $category,
            'metadata'  => $metadata,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function removeKnowledge(string $id): bool
    {
        if (!isset($this->knowledge[$id])) {
            return false;
        }

        unset($this->knowledge[$id]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanupLifecycle(): array
    {
        $removed = 0;
        $updated = 0;
        $kept = 0;
        $cutoffDate = date('Y-m-d', strtotime('-' . self::LIFECYCLE_MAX_AGE_DAYS . ' days'));

        foreach ($this->knowledge as $id => $item) {
            if ($item['created_at'] < $cutoffDate) {
                // Eski kayıtları temizle (veya arşivle)
                unset($this->knowledge[$id]);
                $removed++;
            } else {
                $kept++;
            }
        }

        return [
            'removed' => $removed,
            'updated' => $updated,
            'kept'    => $kept,
        ];
    }

    /* ─── Private Methods ─── */

    /**
     * Relevance skoru hesaplar.
     */
    private function calculateRelevanceScore(string $query, array $item): float
    {
        $titleLower = strtolower($item['title']);
        $contentLower = strtolower($item['content']);

        $score = 0.0;

        // Title match (yüksek ağırlık)
        if (str_contains($titleLower, $query)) {
            $score += 0.6;
        }

        // Content match (düşük ağırlık)
        if (str_contains($contentLower, $query)) {
            $score += 0.3;
        }

        // Keyword partial match
        $queryWords = explode(' ', $query);
        $matchCount = 0;
        foreach ($queryWords as $word) {
            if (strlen($word) < 3) continue;
            if (str_contains($titleLower, $word) || str_contains($contentLower, $word)) {
                $matchCount++;
            }
        }

        if (count($queryWords) > 0) {
            $score += ($matchCount / count($queryWords)) * 0.1;
        }

        return $score;
    }

    /**
     * RAG bağlamını zenginleştirir.
     */
    private function augmentContext(string $query, array $retrieved, array $context): array
    {
        $augmented = $context;
        $augmented['retrieved_documents'] = $retrieved;
        $augmented['query_expansion'] = $query;

        return $augmented;
    }

    /**
     * Yanıt üretir (şimdilik placeholder).
     */
    private function generateAnswer(string $query, array $context): string
    {
        // Gerçek implementasyonda LLM API çağrılacak
        $docCount = count($context['retrieved_documents'] ?? []);

        return "Sorgunuz için {$docCount} ilgili belge bulundu. "
             . "Detaylı yanıt için LLM API entegrasyonu gereklidir.";
    }

    /**
     * Güven skoru hesaplar.
     */
    private function calculateConfidence(array $retrieved): float
    {
        if (empty($retrieved)) {
            return 0.0;
        }

        $avgScore = array_sum(array_column($retrieved, 'score')) / count($retrieved);
        return round(min($avgScore * 1.2, 1.0), 2);
    }
}
