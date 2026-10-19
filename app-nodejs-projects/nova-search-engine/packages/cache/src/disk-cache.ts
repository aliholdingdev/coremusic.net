// ===========================================
// Disk Cache — JSON dosyaları + TTL (L2 katmanı)
// İnternet kapalıyken bile önceki sonuçlar diskte.
// ===========================================

import { mkdirSync, readFileSync, writeFileSync, existsSync, unlinkSync, rmSync } from 'node:fs';
import { join } from 'node:path';
import { safeJson } from '@novasearch/common';

interface DiskEntry<T> {
  value: T;
  expiresAt: number;
}

export class DiskCache<T = unknown> {
  constructor(
    private dir: string,
    private defaultTtlSec = 3600,
  ) {
    mkdirSync(dir, { recursive: true });
  }

  private path(key: string): string {
    return join(this.dir, `${key}.json`);
  }

  get(key: string): T | undefined {
    const file = this.path(key);
    if (!existsSync(file)) return undefined;
    const entry = safeJson<DiskEntry<T> | null>(readFileSync(file, 'utf8'), null);
    if (!entry) {
      unlinkSync(file);
      return undefined;
    }
    if (Date.now() > entry.expiresAt) {
      unlinkSync(file);
      return undefined;
    }
    return entry.value;
  }

  set(key: string, value: T, ttlSec = this.defaultTtlSec): void {
    const entry: DiskEntry<T> = { value, expiresAt: Date.now() + ttlSec * 1000 };
    writeFileSync(this.path(key), JSON.stringify(entry), 'utf8');
  }

  delete(key: string): void {
    const file = this.path(key);
    if (existsSync(file)) unlinkSync(file);
  }

  clear(): void {
    rmSync(this.dir, { recursive: true, force: true });
    mkdirSync(this.dir, { recursive: true });
  }
}
