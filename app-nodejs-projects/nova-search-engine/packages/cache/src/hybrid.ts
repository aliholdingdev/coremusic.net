// ===========================================
// Hybrid Cache — RAM → Disk → (DB → YouTube,
// arama motorunun karar zincirinde)
// Kural: veri önce yerelde aranır, uzak kaynak
// son çaredir (cache-first, offline-first).
// ===========================================

import { RamCache } from './ram-cache.js';
import { DiskCache } from './disk-cache.js';
import { CACHE_TTL, queryKey } from '@novasearch/common';

export interface CacheStats {
  hits: number;
  misses: number;
  ramSize: number;
}

export class HybridCache<T = unknown> {
  private ram: RamCache<T>;
  private disk: DiskCache<T>;
  private hits = 0;
  private misses = 0;

  constructor(dir = './.cache/novasearch') {
    this.ram = new RamCache<T>(500, CACHE_TTL.ram);
    this.disk = new DiskCache<T>(dir, CACHE_TTL.disk);
  }

  get(key: string): T | undefined {
    const fromRam = this.ram.get(key);
    if (fromRam !== undefined) {
      this.hits++;
      return fromRam;
    }
    const fromDisk = this.disk.get(key);
    if (fromDisk !== undefined) {
      this.hits++;
      this.ram.set(key, fromDisk);
      return fromDisk;
    }
    this.misses++;
    return undefined;
  }

  set(key: string, value: T, ttlSec?: number): void {
    this.ram.set(key, value, ttlSec);
    this.disk.set(key, value, ttlSec);
  }

  delete(key: string): void {
    this.ram.delete(key);
    this.disk.delete(key);
  }

  stats(): CacheStats {
    return { hits: this.hits, misses: this.misses, ramSize: this.ram.size };
  }
}

/** Sorgu cache anahtarı üretimi — tek biçim. */
export function cacheKeyFor(scope: string, parts: Record<string, unknown>): string {
  return `${scope}:${queryKey(parts)}`;
}
