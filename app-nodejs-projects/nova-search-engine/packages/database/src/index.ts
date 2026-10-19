// ===========================================
// @novasearch/database — Public API
// createDatabase(): DB_TYPE=sqlite|mysql
// ===========================================

import type { DbAdapter } from './adapter.js';
import { SqliteAdapter } from './sqlite.js';
import { MysqlAdapter } from './mysql.js';
import { migrate } from './schema.js';
import { ChannelRepo, SearchLogRepo, VideoRepo } from './repositories.js';

export interface Database {
  adapter: DbAdapter;
  videos: VideoRepo;
  channels: ChannelRepo;
  searchLog: SearchLogRepo;
  close(): Promise<void>;
}

export interface DatabaseOptions {
  type?: 'sqlite' | 'mysql';
  sqlitePath?: string;
  mysql?: {
    host: string;
    port: number;
    database: string;
    user: string;
    password: string;
    ssl: boolean;
  };
}

export async function createDatabase(opts: DatabaseOptions = {}): Promise<Database> {
  const type = opts.type ?? 'sqlite';
  let adapter: DbAdapter;
  if (type === 'mysql') {
    if (!opts.mysql) throw new Error('MySQL yapılandırması eksik');
    adapter = new MysqlAdapter(opts.mysql);
  } else {
    adapter = new SqliteAdapter(opts.sqlitePath ?? './data/novasearch.db');
  }
  await migrate(adapter);
  return {
    adapter,
    videos: new VideoRepo(adapter),
    channels: new ChannelRepo(adapter),
    searchLog: new SearchLogRepo(adapter),
    close: () => adapter.close(),
  };
}

export * from './adapter.js';
export * from './repositories.js';
export { SqliteAdapter } from './sqlite.js';
export { MysqlAdapter } from './mysql.js';
export { migrate } from './schema.js';
