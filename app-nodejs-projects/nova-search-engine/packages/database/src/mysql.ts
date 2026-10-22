// ===========================================
// MySQL Adapter — mysql2/promise connection pool
// Üretim ortamı. Hazırlanmış ifadeler (prepared
// statements) zorunlu — SQL injection disiplini.
// ===========================================

import mysql, { type Pool, type RowDataPacket, type ResultSetHeader } from 'mysql2/promise';
import type { DbAdapter, RunResult } from './adapter.js';
import { NovaSearchError } from '@novasearch/common';

export interface MysqlConfig {
  host: string;
  port: number;
  database: string;
  user: string;
  password: string;
  ssl: boolean;
}

export class MysqlAdapter implements DbAdapter {
  readonly dialect = 'mysql' as const;
  private pool: Pool;

  constructor(cfg: MysqlConfig) {
    this.pool = mysql.createPool({
      host: cfg.host,
      port: cfg.port,
      database: cfg.database,
      user: cfg.user,
      password: cfg.password,
      ssl: cfg.ssl ? {} : undefined,
      waitForConnections: true,
      connectionLimit: 10,
      namedPlaceholders: false,
      // Uzak MySQL (95.70.176.119): boşta bekleyen TCP'yi firewall/NAT
      // 26 dk'da kesti → ECONNRESET. Keepalive sondaşı bekletir.
      enableKeepAlive: true,
      keepAliveInitialDelay: 10_000,
    });
  }

  /** MySQL ? placeholder'ları SQLite ile aynı — parametreler ortak. */
  async all<T>(sql: string, params: unknown[] = []): Promise<T[]> {
    try {
      const [rows] = await this.pool.query<RowDataPacket[]>(sql, params);
      return rows as T[];
    } catch (err) {
      throw new NovaSearchError('DATABASE', `MySQL all() hatası: ${sql}`, err);
    }
  }

  async get<T>(sql: string, params: unknown[] = []): Promise<T | undefined> {
    const rows = await this.all<T>(sql, params);
    return rows[0];
  }

  async run(sql: string, params: unknown[] = []): Promise<RunResult> {
    try {
      const [res] = await this.pool.execute<ResultSetHeader>(sql, params as never[]);
      return { changes: res.affectedRows ?? 0, lastId: res.insertId ?? 0 };
    } catch (err) {
      throw new NovaSearchError('DATABASE', `MySQL run() hatası: ${sql}`, err);
    }
  }

  async exec(sql: string): Promise<void> {
    await this.pool.query(sql);
  }

  async transaction<T>(fn: (tx: DbAdapter) => Promise<T>): Promise<T> {
    const conn = await this.pool.getConnection();
    const txAdapter: DbAdapter = {
      dialect: 'mysql',
      all: async (sql, params = []) => {
        const [rows] = await conn.query<RowDataPacket[]>(sql, params);
        return rows as never[];
      },
      get: async (sql, params = []) => {
        const [rows] = await conn.query<RowDataPacket[]>(sql, params);
        return rows[0] as never;
      },
      run: async (sql, params = []) => {
        const [res] = await conn.execute<ResultSetHeader>(sql, params as never[]);
        return { changes: res.affectedRows ?? 0, lastId: res.insertId ?? 0 };
      },
      exec: async (sql) => {
        await conn.query(sql);
      },
      transaction: (inner) => inner(txAdapter),
      close: async () => undefined,
    };
    try {
      await conn.beginTransaction();
      const result = await fn(txAdapter);
      await conn.commit();
      return result;
    } catch (err) {
      await conn.rollback();
      throw err;
    } finally {
      conn.release();
    }
  }

  async close(): Promise<void> {
    await this.pool.end();
  }
}
