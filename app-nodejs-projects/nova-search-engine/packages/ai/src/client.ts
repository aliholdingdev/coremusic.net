// ===========================================
// OpenRouter Client — fetch tabanlı, fallback zincirli
// Kurallar: timeout, retry + backoff+jitter, model
// fallback, JSON çıkarma. Anahtar header'da taşınır,
// log'a ASLA yazılmaz.
// ===========================================

import { NovaSearchError, backoffMs, sleep } from '@novasearch/common';
import type { AIConfig } from './config.js';
import { selectModels } from './models.js';

export interface ChatMessage {
  role: 'system' | 'user' | 'assistant';
  content: string;
}

export class RateLimiter {
  private timestamps: number[] = [];
  constructor(private perMinute: number) {}

  async waitIfNeeded(): Promise<void> {
    const now = Date.now();
    this.timestamps = this.timestamps.filter((t) => now - t < 60_000);
    if (this.timestamps.length >= this.perMinute) {
      const oldest = this.timestamps[0] ?? now;
      const waitMs = 60_000 - (now - oldest) + 50;
      if (waitMs > 0) await sleep(waitMs);
    }
    this.timestamps.push(Date.now());
  }
}

export class OpenRouterClient {
  private limiter: RateLimiter;

  constructor(private config: AIConfig) {
    this.limiter = new RateLimiter(config.rateLimitPerMinute);
  }

  get enabled(): boolean {
    return this.config.apiKey.length > 0;
  }

  /** Mesaj gönder; free model fallback zincirini dener. */
  async chat(messages: ChatMessage[], requestedModel?: string): Promise<string> {
    if (!this.enabled) {
      throw new NovaSearchError('AI', 'OPENROUTER_API_KEY tanımlı değil — AI modu kapalı');
    }
    const models = selectModels(this.config.useFreeModels, requestedModel);
    let lastError: unknown;
    for (const model of models) {
      for (let attempt = 0; attempt <= this.config.maxRetries; attempt++) {
        await this.limiter.waitIfNeeded();
        try {
          return await this.callModel(model, messages);
        } catch (err) {
          lastError = err;
          const msg = err instanceof Error ? err.message : '';
          // 4xx (limit/erişim) → sonraki modele geç; ağ/zaman aşımı → backoff ile tekrar
          if (msg.includes('HTTP 4') && !msg.includes('429')) break;
          if (attempt < this.config.maxRetries) await sleep(backoffMs(attempt));
        }
      }
    }
    throw new NovaSearchError('AI', 'Tüm AI modelleri başarısız oldu', lastError);
  }

  private async callModel(model: string, messages: ChatMessage[]): Promise<string> {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), this.config.timeoutMs);
    try {
      const res = await fetch(`${this.config.apiUrl}/chat/completions`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${this.config.apiKey}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ model, messages, temperature: 0.3, max_tokens: 1024 }),
        signal: controller.signal,
      });
      if (!res.ok) throw new NovaSearchError('AI', `OpenRouter HTTP ${res.status}`);
      const data = (await res.json()) as {
        choices?: Array<{ message?: { content?: string } }>;
      };
      const content = data.choices?.[0]?.message?.content;
      if (!content) throw new NovaSearchError('AI', 'Boş AI yanıtı');
      return content;
    } finally {
      clearTimeout(timer);
    }
  }

  /** JSON çıktı iste; kirli yanıttan ilk JSON bloğunu çıkar. */
  async chatJSON<T>(messages: ChatMessage[], fallback: T, requestedModel?: string): Promise<T> {
    try {
      const raw = await this.chat(messages, requestedModel);
      return extractJson<T>(raw, fallback);
    } catch {
      return fallback;
    }
  }
}

export function extractJson<T>(text: string, fallback: T): T {
  const direct = tryParse<T>(text);
  if (direct !== undefined) return direct;
  const match = text.match(/\{[\s\S]*\}|\[[\s\S]*\]/);
  if (match) {
    const parsed = tryParse<T>(match[0]);
    if (parsed !== undefined) return parsed;
  }
  return fallback;
}

function tryParse<T>(text: string): T | undefined {
  try {
    return JSON.parse(text) as T;
  } catch {
    return undefined;
  }
}
