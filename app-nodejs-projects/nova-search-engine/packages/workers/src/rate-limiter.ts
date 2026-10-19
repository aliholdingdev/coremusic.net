// ===========================================
// Rate Limiter — token bucket + jitter
// Amaç: agresif bot davranışı değil, sürdürülebilir
// ve insansı istek ritmi (sorumlu toplama).
// ===========================================

import { jitter, sleep } from '@novasearch/common';

export class TokenBucket {
  private tokens: number;
  private lastRefill: number;

  constructor(
    private capacity: number,
    private perMinute: number,
    private jitterPct = 20,
  ) {
    this.tokens = capacity;
    this.lastRefill = Date.now();
  }

  private refill(): void {
    const now = Date.now();
    const elapsed = now - this.lastRefill;
    const newTokens = (elapsed / 60_000) * this.perMinute;
    if (newTokens > 0) {
      this.tokens = Math.min(this.capacity, this.tokens + newTokens);
      this.lastRefill = now;
    }
  }

  /** İzin çıkana kadar bekle; bekleme süresine jitter uygulanır. */
  async take(): Promise<void> {
    this.refill();
    if (this.tokens < 1) {
      const deficit = 1 - this.tokens;
      const waitMs = jitter((deficit / this.perMinute) * 60_000, this.jitterPct);
      await sleep(waitMs);
      this.refill();
    }
    this.tokens = Math.max(0, this.tokens - 1);
  }
}
