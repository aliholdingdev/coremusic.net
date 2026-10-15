/** RAM önbelleği — LRU, TTL'li. Seri/paralel erişimde tek süreç varsayımı. */
import type { SearchResultPayload } from '../core/types.ts';

interface Entry {
  value: SearchResultPayload;
  expires: number;
}

export class MemoryCache {
  private map = new Map<string, Entry>();
  private maxEntries: number;
  private ttlMs: number;

  constructor(maxEntries = 200, ttlMs = 5 * 60_000) {
    this.maxEntries = maxEntries;
    this.ttlMs = ttlMs;
  }

  get(key: string): SearchResultPayload | null {
    const e = this.map.get(key);
    if (!e) return null;
    if (Date.now() > e.expires) {
      this.map.delete(key);
      return null;
    }
    // LRU: erişileni sona al
    this.map.delete(key);
    this.map.set(key, e);
    return e.value;
  }

  set(key: string, value: SearchResultPayload): void {
    if (this.map.size >= this.maxEntries) {
      const oldest = this.map.keys().next().value;
      if (oldest !== undefined) this.map.delete(oldest);
    }
    this.map.set(key, { value, expires: Date.now() + this.ttlMs });
  }

  clear(): void {
    this.map.clear();
  }

  get size(): number {
    return this.map.size;
  }
}
