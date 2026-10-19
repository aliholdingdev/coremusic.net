// ===========================================
// Database Adapter — SQLite (dev) / MySQL (prod)
// Tek sozlesme, iki dialekt. (Debate D7)
// ===========================================

export type Dialect = 'sqlite' | 'mysql';

export interface RunResult {
  changes: number;
  lastId: number;
}

export interface DbAdapter {
  readonly dialect: Dialect;
  all<T>(sql: string, params?: unknown[]): Promise<T[]>;
  get<T>(sql: string, params?: unknown[]): Promise<T | undefined>;
  run(sql: string, params?: unknown[]): Promise<RunResult>;
  exec(sql: string): Promise<void>;
  transaction<T>(fn: (tx: DbAdapter) => Promise<T>): Promise<T>;
  close(): Promise<void>;
}

export function nowIso(): string {
  return new Date().toISOString();
}
