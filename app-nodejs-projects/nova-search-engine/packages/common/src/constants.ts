// ===========================================
// @novasearch/common — Sabitler
// ===========================================

export const CACHE_TTL = {
  /** RAM katmani (saniye) */
  ram: 300,
  /** Disk katmani (saniye) */
  disk: 3600,
  /** AI yanitlari (saniye) */
  ai: 86400,
} as const;

export const COLLECTOR = {
  defaultCooldownMs: 2500,
  jitterPct: 20,
  maxRetries: 3,
  backoffBaseMs: 1500,
} as const;

export const SEARCH = {
  defaultResult: 20,
  defaultScroll: 50,
  maxScroll: 5000,
  maxResult: 1000,
} as const;

export const QUALITY_THRESHOLDS = {
  high: 0.65,
  medium: 0.4,
} as const;
