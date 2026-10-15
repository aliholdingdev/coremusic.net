import { readFileSync, existsSync, mkdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

/** Proje kökü: src/config.ts → bir üst dizin */
export const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');

function loadEnvFile(): void {
  const p = join(ROOT, '.env');
  if (!existsSync(p)) return;
  for (const raw of readFileSync(p, 'utf8').split(/\r?\n/)) {
    const line = raw.trim();
    if (!line || line.startsWith('#')) continue;
    const eq = line.indexOf('=');
    if (eq < 0) continue;
    const k = line.slice(0, eq).trim();
    let v = line.slice(eq + 1).trim();
    if ((v.startsWith('"') && v.endsWith('"')) || (v.startsWith("'") && v.endsWith("'"))) v = v.slice(1, -1);
    if (!(k in process.env)) process.env[k] = v;
  }
}
loadEnvFile();

function int(v: string | undefined, d: number): number {
  const n = Number.parseInt(v ?? '', 10);
  return Number.isFinite(n) ? n : d;
}

export const config = {
  dbPath: resolve(ROOT, process.env.DB_SQLITE_PATH ?? './data/novasearch.db'),
  dataDir: join(ROOT, 'data'),
  cacheDir: join(ROOT, '.cache'),
  api: {
    host: process.env.API_HOST ?? '127.0.0.1',
    port: int(process.env.API_PORT, 3100),
  },
  webPort: int(process.env.WEB_PORT, 3110),
  offline: process.env.NOVA_OFFLINE === '1',
  logLevel: process.env.LOG_LEVEL ?? 'info',
  ai: {
    enabled: process.env.AI_ENABLED !== 'false',
    apiKey: process.env.OPENROUTER_API_KEY ?? '',
    model: process.env.OPENROUTER_MODEL ?? 'nvidia/nemotron-3-super-120b-a12b:free',
    fallbackModels: (process.env.OPENROUTER_FALLBACK_MODELS ?? '')
      .split(',')
      .map((s) => s.trim())
      .filter(Boolean),
    timeoutMs: int(process.env.AI_TIMEOUT_MS, 30000),
  },
  /** Anti-ban: dakikada max istek + istekler arası minimum gecikme (ms) */
  collector: {
    maxRequestsPerMinute: int(process.env.COLLECT_RPM, 12),
    minDelayMs: int(process.env.COLLECT_MIN_DELAY_MS, 1500),
    maxDelayMs: int(process.env.COLLECT_MAX_DELAY_MS, 6000),
    maxPages: int(process.env.COLLECT_MAX_PAGES, 5),
  },
};

export function ensureDirs(): void {
  for (const d of [config.dataDir, config.cacheDir]) {
    if (!existsSync(d)) mkdirSync(d, { recursive: true });
  }
}
