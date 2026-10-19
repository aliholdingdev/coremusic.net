// ===========================================
// @novasearch/ai — Public API
// ===========================================

export { loadAIConfig, isAIEnabled, type AIConfig } from './config.js';
export { OpenRouterClient, RateLimiter, extractJson, type ChatMessage } from './client.js';
export { FREE_MODELS, PAID_MODELS, getModel, selectModels, type ModelInfo } from './models.js';
export {
  analyzeQuery,
  type QueryAnalysis,
  type QueryHints,
  type AnalyzeOptions,
} from './query-analyzer.js';
export {
  scoreVideo,
  rankVideos,
  buildReasons,
  type ScoredVideo,
} from './result-enricher.js';
export { aiRelevanceFilter, type AIJudgment } from './ai-filter.js';
