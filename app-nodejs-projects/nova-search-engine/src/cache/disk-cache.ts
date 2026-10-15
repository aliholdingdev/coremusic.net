/** Disk önbelleği — .cache/search/*.json, TTL'li. Offline modda ana kaynak. */
import { readFileSync, writeFileSync, mkdirSync, existsSync, readdirSync, unlinkSync } from 'node:fs';
import { join, resolve } from 'node:path';
import { createHash } from 'node:crypto';
import { config } from '../config.ts';
import type { SearchResultPayload } from '../core/types.ts';

const DIR = resolve(config.cacheDir, 'search');

export function cacheKey(parts: unknown[]): string {
  return createHash('sha256').update(JSON.stringify(parts)).digest('hex').slice(0, 32);
}

export class DiskCache {
  private ttlMs: number;

  constructor(ttlMs = 30 * 24 * 60 * 60_000) {
    this.ttlMs = ttlMs; // 30 gün
  }

  private file(key: string): string {
    return join(DIR, `${key}.json`);
  }

  get<T = SearchResultPayload>(key: string): T | null {
    const f = this.file(key);
    if (!existsSync(f)) return null;
    try {
      const raw = JSON.parse(readFileSync(f, 'utf8')) as { expires: number; data: T };
      if (Date.now() > raw.expires) {
        unlinkSync(f);
        return null;
      }
      return raw.data;
    } catch {
      return null;
    }
  }

  set<T = SearchResultPayload>(key: string, data: T): void {
    if (!existsSync(DIR)) mkdirSync(DIR, { recursive: true });
    writeFileSync(this.file(key), JSON.stringify({ expires: Date.now() + this.ttlMs, data }), 'utf8');
  }

  /** Süresi dolmuş tüm girişleri temizle (CLI: nova cache --prune) */
  prune(): number {
    if (!existsSync(DIR)) return 0;
    let removed = 0;
    for (const f of readdirSync(DIR)) {
      if (!f.endsWith('.json')) continue;
      const p = join(DIR, f);
      try {
        const raw = JSON.parse(readFileSync(p, 'utf8')) as { expires: number };
        if (Date.now() > raw.expires) {
          unlinkSync(p);
          removed++;
        }
      } catch {
        unlinkSync(p);
        removed++;
      }
    }
    return removed;
  }
}
