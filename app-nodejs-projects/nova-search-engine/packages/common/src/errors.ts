// ===========================================
// @novasearch/common — Hata Siniflari
// ===========================================

export type NovaErrorCode =
  | 'VALIDATION'
  | 'NETWORK'
  | 'PROVIDER'
  | 'DATABASE'
  | 'AI'
  | 'RATE_LIMITED'
  | 'NOT_FOUND'
  | 'INTERNAL';

export class NovaSearchError extends Error {
  readonly code: NovaErrorCode;

  constructor(code: NovaErrorCode, message: string, cause?: unknown) {
    super(message, { cause });
    this.name = 'NovaSearchError';
    this.code = code;
  }
}

export function isNovaSearchError(err: unknown): err is NovaSearchError {
  return err instanceof NovaSearchError;
}

/** Ag hatasi mi? (offline fallback kararlari icin) */
export function isNetworkError(err: unknown): boolean {
  if (isNovaSearchError(err)) return err.code === 'NETWORK';
  if (err instanceof Error) {
    const msg = err.message.toLowerCase();
    return (
      msg.includes('fetch failed') ||
      msg.includes('enotfound') ||
      msg.includes('econnrefused') ||
      msg.includes('etimedout') ||
      msg.includes('econnreset') ||
      msg.includes('network')
    );
  }
  return false;
}
