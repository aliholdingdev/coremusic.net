// ===========================================
// @novasearch/common — Yardimci Fonksiyonlar
// ===========================================

import { createHash } from 'node:crypto';

/**
 * Insansi gecikme: temel sure + +/- %jitter.
 * Amac: esit aralikli bot davranisi yerine dogal ritim (sorumlu toplama).
 */
export function jitter(baseMs: number, jitterPct = 20): number {
  const delta = (baseMs * jitterPct) / 100;
  return Math.round(baseMs - delta + Math.random() * 2 * delta);
}

/** Exponential backoff + jitter (retry denemeleri icin). */
export function backoffMs(attempt: number, baseMs = 1500, maxMs = 60_000): number {
  const exp = Math.min(baseMs * 2 ** attempt, maxMs);
  return jitter(exp);
}

export function sleep(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/** Sorgu anahtari (cache key) — normalize + hash. */
export function queryKey(parts: Record<string, unknown>): string {
  const normalized = Object.keys(parts)
    .sort()
    .map((k) => `${k}:${String(parts[k] ?? '').trim().toLowerCase()}`)
    .join('|');
  return createHash('sha1').update(normalized).digest('hex');
}

export function clamp(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max);
}

/** YouTube suresi (saniye) -> "12:34" / "1:02:03" */
export function formatDuration(totalSeconds: number): string {
  const s = Math.max(0, Math.floor(totalSeconds));
  const h = Math.floor(s / 3600);
  const m = Math.floor((s % 3600) / 60);
  const sec = s % 60;
  const mm = h > 0 ? String(m).padStart(2, '0') : String(m);
  return h > 0 ? `${h}:${mm}:${String(sec).padStart(2, '0')}` : `${mm}:${String(sec).padStart(2, '0')}`;
}

export function formatViews(n: number): string {
  if (n >= 1_000_000_000) return `${(n / 1_000_000_000).toFixed(1)}B`;
  if (n >= 1_000_000) return `${(n / 1_000_000).toFixed(1)}M`;
  if (n >= 1_000) return `${(n / 1_000).toFixed(1)}K`;
  return String(n);
}

export function parseNumber(value: unknown, defaultValue: number): number {
  const n = typeof value === 'number' ? value : parseInt(String(value), 10);
  return Number.isFinite(n) ? n : defaultValue;
}

/** JSON'u guvenli parse et (kirik cache kayitlarina karsi). */
export function safeJson<T>(text: string, fallback: T): T {
  try {
    return JSON.parse(text) as T;
  } catch {
    return fallback;
  }
}
