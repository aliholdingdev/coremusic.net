// ===========================================
// Model Registry — OpenRouter ücretsiz modelleri
// SON DOĞRULAMA: 2026-10-16, OpenRouter /api/v1/models
// (465 model tarandı; aşağıdaki liste o gün ücretsizdi)
//
// DİKKAT: deepseek/deepseek-chat, llama-3.1-8b-instruct
// Artık ÜCRETLİDİR — registry'ye paid olarak girilir.
// ===========================================

export interface ModelInfo {
  name: string;
  isFree: boolean;
  maxTokens: number;
}

export const FREE_MODELS: ModelInfo[] = [
  // Açık router: ücretsiz modellere otomatik yönlendirir
  { name: 'openrouter/free', isFree: true, maxTokens: 8192 },
  { name: 'google/gemma-4-26b-a4b-it:free', isFree: true, maxTokens: 8192 },
  { name: 'nvidia/nemotron-3-super-120b-a12b:free', isFree: true, maxTokens: 8192 },
  { name: 'thinkingmachines/inkling:free', isFree: true, maxTokens: 8192 },
];

export const PAID_MODELS: ModelInfo[] = [
  { name: 'deepseek/deepseek-chat', isFree: false, maxTokens: 8192 },
  { name: 'meta-llama/llama-3.1-8b-instruct', isFree: false, maxTokens: 8192 },
];

export function getModel(name: string): ModelInfo | undefined {
  return [...FREE_MODELS, ...PAID_MODELS].find((m) => m.name === name);
}

/**
 * Kullanılacak modeli seç: free moddaysa yalnız ücretsizler.
 * Arıza durumunda sıradakine geç (fallback zinciri).
 */
export function selectModels(preferFree: boolean, requested?: string): string[] {
  if (requested && requested !== 'free') return [requested];
  const pool = preferFree ? FREE_MODELS : [...FREE_MODELS, ...PAID_MODELS];
  return pool.map((m) => m.name);
}
