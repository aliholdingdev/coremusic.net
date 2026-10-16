---
node_id: "K008-recommendation-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-recommendation-kapsam"
evidence:
  - "shared/src/AI/AIEngine.php satır 49–76 (2. tur disk okuması)"
  - "shared/src/AI/AIWorkflow.php satır 49–78 (2. tur disk okuması)"
  - ".ai/.sql/mysql/coremusic_ai.sql satır 79–98"
validation: "2026-10-15 — 3 örnek (PHP ×2, SQL ×1); hepsi disk'ten birebir kopyalandı"
open_questions:
  - "öneri PHP sunucusu/controller örneği → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# recommendation — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Üç örnek de disk'ten birebir kopyalanmıştır.

## §1 ÖRNEK 1 — PHP: AIEngine::getRecommendations (skorlama zinciri · satır 49–76)

```php
<?php
// Dosya: shared/src/AI/AIEngine.php · satır 49-76
public function getRecommendations(array $context, int $limit = 20): array
{
    $limit = min($limit, self::MAX_RECOMMENDATIONS);
    $userId = $context['user_id'] ?? null;
    $sessionId = $context['session_id'] ?? null;
    $mood = $context['mood'] ?? null;
    $genre = $context['genre'] ?? null;
    $history = $context['history'] ?? [];

    // 1. Collaborative filtering — benzer kullanıcı tercihleri
    $collaborativeScores = $this->getCollaborativeScores($userId, $history);

    // 2. Content-based — şarkı özellikleri
    $contentScores = $this->getContentBasedScores($context);

    // 3. Hybrid skorlama (ağırlıklı ortalama)
    $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4);

    // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
    $diversified = $this->applyDiversityFilter($hybridScores, $genre);

    // 5. Minimum skor filtresi
    $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE);

    // 6. Sıralama ve limit
    usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);

    return array_slice(array_values($filtered), 0, $limit);
}
```

**Ne kanıtlıyor:** öneri bir **zincirdir** — 0.6/0.4 ağırlıklı hibrit (satır 65),
%30 çeşitlilik (68), 0.7 eşik (71), azalan sıralama (74), limit (76). Sabitler
dosya başındaki `MAX_RECOMMENDATIONS = 20` / `MIN_SCORE = 0.7` /
`DIVERSITY_TARGET = 0.30` (satır 30–32) ile beslenir.

## §2 ÖRNEK 2 — PHP: AIWorkflow::recommendationWorkflow (orkestrasyon · satır 49–78 kesit)

```php
<?php
// Dosya: shared/src/AI/AIWorkflow.php · satır 49-78 (kesit)
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
```

**Ne kanıtlıyor:** orkestrasyon altı adımdır (satır 54–78); varsayılan limit **10**'dur
(satır 71, 75) — motorun kendi varsayılanı 20 (D-RC3) ile **farklıdır**; sonuç
oturum cache'ine yazılır (satır 78), **event olarak yayınlanmaz** (satır 77–78).

## §3 ÖRNEK 3 — SQL: recommendation_history (geri bildirim kaydı · satır 79–98)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_ai.sql · satır 79-98
CREATE TABLE `recommendation_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `music_id` int unsigned NOT NULL,
  `algorithm` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidence` decimal(3,2) DEFAULT NULL,
  `feedback` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `position` tinyint unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rh_user` (`user_id`),
  KEY `idx_rh_music` (`music_id`),
  KEY `idx_rh_algorithm` (`algorithm`),
  KEY `idx_rh_feedback` (`feedback`),
  KEY `idx_rh_created` (`created_at` DESC),
  CONSTRAINT `recommendation_history_chk_1` CHECK ((`algorithm` in (_utf8mb4'collaborative',_utf8mb4'content_based',_utf8mb4'hybrid',_utf8mb4'ai_curated'))),
  CONSTRAINT `recommendation_history_chk_2` CHECK ((`feedback` in (_utf8mb4'none',_utf8mb4'liked',_utf8mb4'disliked',_utf8mb4'skipped',_utf8mb4'saved')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Ne kanıtlıyor:** her öneri **izlenebilirdir** — hangi algoritma (satır 96: 4
değer), hangi güven (85), hangi pozisyon (87) ve kullanıcı ne yaptı (97: 5
değer). DİKKAT: ID'ler `int unsigned` (satır 80–82) — bu tablo **UUID v7
`binary(16)` neslinden önce** yazılmıştır (karşılaştırma: [[playback-ornekler]]
§1).

## §4 DÜZELTME/NOT KAYDI

| # | Not | Durum |
|---|---|---|
| 1 | workflow varsayılan limit 10 vs motor varsayılanı 20 — iki ayrı katman, ikisi de gerçek | ✅ her iki satır da doğrulandı (75 · 51) |
| 2 | `ai_curated` değeri yalnızca bu tabloda var (`music_similar` enum'u 3 değer) | ✅ satır 96 · music satır 226 |

**Kardeş dosyalar:** [[recommendation-kapsam]] · [[recommendation-dogrulama]] · [[recommendation-web-arastirma]] · [[recommendation-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
