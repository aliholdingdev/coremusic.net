// ===========================================
// RAM Cache — LRU + TTL (L1 katmanı)
// ===========================================

interface Entry<T> {
  value: T;
  expiresAt: number;
}

export class RamCache<T = unknown> {
  private map = new Map<string, Entry<T>>();

  constructor(
    private maxEntries = 500,
    private defaultTtlSec = 300,
  ) {}

  get(key: string): T | undefined {
    const entry = this.map.get(key);
    if (!entry) return undefined;
    if (Date.now() > entry.expiresAt) {
      this.map.delete(key);
      return undefined;
    }
    // LRU: erişileni sona taşı
    this.map.delete(key);
    this.map.set(key, entry);
    return entry.value;
  }

  set(key: string, value: T, ttlSec = this.defaultTtlSec): void {
    if (this.map.has(key)) this.map.delete(key);
    if (this.map.size >= this.maxEntries) {
      // en eski (ilk) kayıt çıkarılır
      const oldest = this.map.keys().next();
      if (!oldest.done) this.map.delete(oldest.value);
    }
    this.map.set(key, { value, expiresAt: Date.now() + ttlSec * 1000 });
  }

  delete(key: string): void {
    this.map.delete(key);
  }

  clear(): void {
    this.map.clear();
  }

  get size(): number {
    return this.map.size;
  }
}
