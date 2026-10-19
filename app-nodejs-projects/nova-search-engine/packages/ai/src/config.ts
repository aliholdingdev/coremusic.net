// ===========================================
// AI Yapılandırması — OpenRouter free
// Anahtar yalnız .env'den okunur; hiçbir yere yazılmaz.
// ===========================================

export interface AIConfig {
  apiKey: string;
  apiUrl: string;
  useFreeModels: boolean;
  rateLimitPerMinute: number;
  timeoutMs: number;
  maxRetries: number;
}

function env(key: string, fallback = ''): string {
  return process.env[key] ?? fallback;
}

function envInt(key: string, fallback: number): number {
  const v = parseInt(process.env[key] ?? '', 10);
  return Number.isFinite(v) ? v : fallback;
}

export function loadAIConfig(): AIConfig {
  return {
    apiKey: env('OPENROUTER_API_KEY'),
    apiUrl: env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1'),
    useFreeModels: (process.env['AI_USE_FREE_MODELS'] ?? 'true') !== 'false',
    rateLimitPerMinute: envInt('AI_RATE_LIMIT_PER_MINUTE', 20),
    timeoutMs: envInt('AI_TIMEOUT_MS', 30_000),
    maxRetries: envInt('AI_MAX_RETRIES', 2),
  };
}

export function isAIEnabled(): boolean {
  return loadAIConfig().apiKey.length > 0;
}
