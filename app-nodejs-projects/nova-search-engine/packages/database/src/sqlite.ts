// ===========================================
// SQLite Adapter — Node.js gömülü node:sqlite
// Sıfır bağımlılık; geliştirme/test ortamı.
// ===========================================

import { DatabaseSync } from 'node:sqlite';
import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import type { DbAdapter, RunResult } from './adapter.js';
import { NovaSearchError } from '@novasearch/common';

export class SqliteAdapter implements DbAdapter {
  readonly dialect = 'sqlite' as const;
  private db: DatabaseSync;
  private txDepth = 0;

  constructor(path: string) {
    if (path !== ':memory:') mkdirSync(dirname(path), { recursive: true });
    this.db = new DatabaseSync(path);
    this.db.exec('PRAGMA journal_mode = WAL');
    this.db.exec('PRAGMA foreign_keys = ON');
  }

  async all<T>(sql: string, params: unknown[] = []): Promise<T[]> {
    try {
      return this.db.prepare(sql).all(...(params as never[])) as T[];
    } catch (err) {
      throw new NovaSearchError('DATABASE', `SQLite all() hatası: ${sql}`, err);
    }
  }

  async get<T>(sql: string, params: unknown[] = []): Promise<T | undefined> {
    try {
      return this.db.prepare(sql).get(...(params as never[])) as T | undefined;
    } catch (err) {
      throw new NovaSearchError('DATABASE', `SQLite get() hatası: ${sql}`, err);
    }
  }

  async run(sql: string, params: unknown[] = []): Promise<RunResult> {
    try {
      const res = this.db.prepare(sql).run(...(params as never[]));
      return { changes: Number(res.changes), lastId: Number(res.lastInsertRowid) };
    } catch (err) {
      throw new NovaSearchError('DATABASE', `SQLite run() hatası: ${sql}`, err);
    }
  }

  async exec(sql: string): Promise<void> {
    try {
      this.db.exec(sql);
    } catch (err) {
      throw new NovaSearchError('DATABASE', `SQLite exec() hatası`, err);
    }
  }

  async transaction<T>(fn: (tx: DbAdapter) => Promise<T>): Promise<T> {
    const name = `tx_${this.txDepth}`;
    if (this.txDepth === 0) this.db.exec('BEGIN');
    this.txDepth++;
    try {
      const result = await fn(this);
      this.txDepth--;
      if (this.txDepth === 0) this.db.exec('COMMIT');
      return result;
    } catch (err) {
      this.txDepth--;
      if (this.txDepth === 0) this.db.exec('ROLLBACK');
      void name;
      throw err;
    }
  }

  async close(): Promise<void> {
    this.db.close();
  }
}
