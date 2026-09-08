<?php declare(strict_types=1);
/**
 * CoreMusic — Knowledge Base Interface
 * 
 * Bilgi bankası arayüzü — semantic search, RAG, knowledge lifecycle.
 *
 * @package CoreMusic\AI\Contracts
 * @version 1.0.0
 */

namespace CoreMusic\AI\Contracts;

interface KnowledgeBaseInterface
{
    /**
     * Semantic search yapar.
     *
     * @param string $query    Arama sorgusu
     * @param int    $maxResults Maksimum sonuç sayısı
     * @param array<string, mixed> $filters Filtreler (kategori, tarih, etiket)
     * @return array<int, array{id: string, title: string, content: string, score: float, source: string, category: string}>
     */
    public function search(string $query, int $maxResults = 10, array $filters = []): array;

    /**
     * RAG pipeline çalıştırır — retrieval + augmentation + generation.
     *
     * @param string $query   Kullanıcı sorgusu
     * @param array<string, mixed> $context Ek bağlam
     * @return array{answer: string, sources: array<int, array{id: string, title: string, score: float}>, confidence: float}
     */
    public function ragQuery(string $query, array $context = []): array;

    /**
     * Bilgi ekler/ünceller.
     *
     * @param string $title    Başlık
     * @param string $content  İçerik
     * @param string $category Kategori (adr, architecture, security, audio, hardware)
     * @param array<string, string> $metadata Ek metadata
     * @return string Eklenen kaydın ID'si
     */
    public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string;

    /**
     * Bilgi siler.
     *
     * @param string $id Kayıt ID'si
     * @return bool Başarı durumu
     */
    public function removeKnowledge(string $id): bool;

    /**
     * Knowledge lifecycle yönetimi — eski/yetersiz bilgileri temizler.
     *
     * @return array{removed: int, updated: int, kept: int}
     */
    public function cleanupLifecycle(): array;
}
