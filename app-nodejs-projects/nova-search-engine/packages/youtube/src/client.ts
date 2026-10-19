// ===========================================
// YouTube Client — youtubei.js 18.x Innertube
// ADR-001: YouTube Data API v3 KULLANILMAZ.
// Sorumlu toplama: istekler arası insansı gecikme
// (cooldown + ±%jitter), yeniden denemede backoff.
// ===========================================

import { Innertube, Log } from 'youtubei.js';
import { COLLECTOR, NovaSearchError, backoffMs, isNetworkError, jitter, sleep } from '@novasearch/common';

// youtubei.js parser uyarılarını sustur (JIT sınıf üretimi normal davranış);
// yalnız gerçek hatalar görünsün.
Log.setLevel(Log.Level.ERROR);

export class YouTubeClient {
  private yt: Innertube | null = null;
  private lastRequestAt = 0;
  private initPromise: Promise<Innertube> | null = null;

  private get cooldownMs(): number {
    const base = parseInt(process.env['COLLECTOR_COOLDOWN_MS'] ?? '', 10) || COLLECTOR.defaultCooldownMs;
    const jitterPct = parseInt(process.env['COLLECTOR_JITTER_PCT'] ?? '', 10) || COLLECTOR.jitterPct;
    return jitter(base, jitterPct);
  }

  private async getClient(): Promise<Innertube> {
    if (this.yt) return this.yt;
    if (!this.initPromise) {
      this.initPromise = Innertube.create({ lang: 'tr', location: 'TR' })
        .then((yt) => {
          this.yt = yt;
          return yt;
        })
        .catch((err) => {
          this.initPromise = null;
          throw new NovaSearchError('PROVIDER', 'YouTube oturumu açılamadı', err);
        });
    }
    return this.initPromise;
  }

  /** İnsansı ritim: ardışık istekler arası bekleme. */
  private async throttle(): Promise<void> {
    const wait = this.lastRequestAt + this.cooldownMs - Date.now();
    if (wait > 0) await sleep(wait);
    this.lastRequestAt = Date.now();
  }

  /** Genel amaçlı çağrı: throttle + retry (ağ hatalarında backoff). */
  async call<T>(fn: (yt: Innertube) => Promise<T>): Promise<T> {
    let lastErr: unknown;
    for (let attempt = 0; attempt < COLLECTOR.maxRetries; attempt++) {
      await this.throttle();
      try {
        const yt = await this.getClient();
        return await fn(yt);
      } catch (err) {
        lastErr = err;
        if (!isNetworkError(err) && attempt === 0 && this.yt) {
          // oturum bozulmuş olabilir → tazele
          this.yt = null;
        }
        if (attempt < COLLECTOR.maxRetries - 1) await sleep(backoffMs(attempt, COLLECTOR.backoffBaseMs));
      }
    }
    throw new NovaSearchError('NETWORK', 'YouTube çağrısı başarısız (tüm denemeler tükendi)', lastErr);
  }
}

/** Tekil istemci (singleton). */
let shared: YouTubeClient | null = null;
export function getYouTubeClient(): YouTubeClient {
  if (!shared) shared = new YouTubeClient();
  return shared;
}
