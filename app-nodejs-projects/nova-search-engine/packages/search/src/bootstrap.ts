// ===========================================
// Bootstrap — ortamdan motor kur (CLI + API ortak)
// ===========================================

import { HybridCache, type CacheStats } from '@novasearch/cache';
import { createDatabase, type Database } from '@novasearch/database';
import { CollectorWorker } from '@novasearch/workers';
import type { Video } from '@novasearch/common';
import { SearchEngine } from './engine.js';

export interface EngineContext {
  engine: SearchEngine;
  db: Database;
  collector: CollectorWorker;
  cache: HybridCache<Video[]>;
  cacheStats(): CacheStats;
  close(): Promise<void>;
}

export async function createEngineFromEnv(): Promise<EngineContext> {
  const type = (process.env['DB_TYPE'] ?? 'sqlite') === 'mysql' ? 'mysql' : 'sqlite';
  const db = await createDatabase({
    type,
    sqlitePath: process.env['DB_SQLITE_PATH'] ?? './data/novasearch.db',
    mysql: {
      host: process.env['MYSQL_HOST'] ?? '127.0.0.1',
      port: parseInt(process.env['MYSQL_PORT'] ?? '3306', 10),
      database: process.env['MYSQL_DATABASE'] ?? 'novasearch',
      user: process.env['MYSQL_USER'] ?? 'root',
      password: process.env['MYSQL_PASSWORD'] ?? '',
      ssl: (process.env['MYSQL_SSL'] ?? 'false') === 'true',
    },
  });
  const cache = new HybridCache<Video[]>(process.env['CACHE_DIR'] ?? './.cache/novasearch');
  const collector = new CollectorWorker(db, parseInt(process.env['COLLECTOR_MAX_CONCURRENT'] ?? '2', 10));
  collector.start(); // arka plan kuyruğu: auto-collect + refresh işleri
  const engine = new SearchEngine({ db, cache, collector });
  return {
    engine,
    db,
    collector,
    cache,
    cacheStats: () => cache.stats(),
    close: async () => {
      collector.stop(); // arka plan kuyruğu döngüsünü durdur (süreç çıkışını engeller)
      await db.close();
    },
  };
}
