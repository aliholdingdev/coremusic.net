// ===========================================
// @novasearch/search — Public API
// ===========================================

export { SearchEngine, type SearchEngineDeps, type ProgressCallback } from './engine.js';
export { applyFilters, paginate, sortVideos, toEnriched } from './ranking.js';
export { SearchIndex, type IndexStats } from './search-index.js';
export { recommend } from './recommend.js';
export { createEngineFromEnv, type EngineContext } from './bootstrap.js';
