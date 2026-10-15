/** Collector — kuyruktaki işleri anti-ban ritmiyle işler; doğrudan sorgu verildiğinde tek tur. */
import { youtubeSearch, youtubeSearchDeep } from '../providers/youtube.ts';
import { upsertVideo, nextJobs, completeJob, enqueueJob } from '../database/db.ts';
import { progressBar } from '../cli/format.ts';
import { config } from '../config.ts';

/**
 * Tek tur collector.
 * - `query` verilmişse doğrudan o sorguyu toplar; `target` > 0 ise derin mod (getNext ile).
 * - Verilmemişse kuyruktaki pending işleri işler.
 * Dönen: işlenen iş sayısı.
 */
export async function runCollector(
  query: string,
  scroll: number,
  _once: boolean,
  opts: { target?: number } = {},
): Promise<number> {
  const offline = config.offline;
  if (offline) {
    console.log('offline mod (NOVA_OFFLINE=1) — toplama yapılmadı.');
    return 0;
  }

  let processed = 0;

  if (query) {
    const target = opts.target ?? 0;
    if (target > 0) {
      // derin toplama: hedefe kadar getContinuation() ile sayfa sayfa
      const { records, pages } = await youtubeSearchDeep(query, target, (found, pg) => {
        process.stdout.write(
          `\rToplanıyor: ${progressBar(found, target)} ${found}/${target} · sayfa ${pg}`,
        );
      });
      process.stdout.write('\n');
      let inserted = 0;
      for (const r of records) if (upsertVideo(r) === 'inserted') inserted++;
      console.log(`deep "${query}": ${records.length} kayıt (${pages} sayfa), ${inserted} yeni.`);
      return 1;
    }

    const { records, pages } = await youtubeSearch(query, { limit: 20, page: 1 }, scroll);
    let inserted = 0;
    for (const r of records) if (upsertVideo(r) === 'inserted') inserted++;
    console.log(`search "${query}": ${records.length} kayıt (${pages} sayfa), ${inserted} yeni.`);
    processed++;
    return processed;
  }

  const jobs = nextJobs(20);
  for (const job of jobs) {
    try {
      const payload = JSON.parse(job.payload) as { query?: string };
      if ((job.type === 'search' || job.type === 'refresh') && payload.query) {
        const { records } = await youtubeSearch(payload.query, { limit: 20, page: 1 }, 2);
        for (const r of records) upsertVideo(r);
        console.log(`job#${job.id} (${job.type} "${payload.query}"): ${records.length} kayıt.`);
      }
      completeJob(job.id, true);
      processed++;
    } catch (e) {
      completeJob(job.id, false);
      console.error(`job#${job.id} başarısız:`, e instanceof Error ? e.message : e);
    }
  }

  if (!processed) console.log('kuyruk boş — bekleyen iş yok. Örn: nova collect "adele"');
  return processed;
}

/** Sonuç yetersizse API/CLI arka planda toplama işi kuyruğa alır. */
export function requestCollection(query: string): void {
  enqueueJob('search', { query });
}
