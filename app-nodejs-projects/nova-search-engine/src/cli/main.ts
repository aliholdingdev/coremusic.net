#!/usr/bin/env node
/** NovaSearch CLI giriş noktası — menü YOK, komut + parametre. */
import { parseArgs, buildSearchOptions, buildVideoOptions, scrollOf, ArgError } from './parse.ts';
import { HELP, VERSION } from './help.ts';
import { formatResults, formatVideoDetail } from './format.ts';
import { search } from '../core/search.ts';
import { videoDetail } from '../core/video-detail.ts';
import { countVideos, trending, suggest, getDb, closeDb } from '../database/db.ts';
import { DiskCache } from '../cache/disk-cache.ts';
import { aiAvailable } from '../ai/engine.ts';
import { runCollector } from '../apps/collector.ts';
import { runEnrich } from '../apps/enrich.ts';
import { startApi } from '../apps/api.ts';
import { startWeb } from '../apps/web.ts';
import { config } from '../config.ts';

async function main(): Promise<number> {
  const parsed = parseArgs(process.argv.slice(2));

  if (parsed.flags.has('version') || parsed.flags.has('v')) {
    console.log(VERSION);
    return 0;
  }
  if (parsed.flags.has('help') || parsed.flags.has('h') || parsed.command === 'help' || !parsed.command) {
    console.log(HELP);
    return 0;
  }

  switch (parsed.command) {
    case 'search': {
      const opts = buildSearchOptions(parsed);
      const payload = await search(opts);
      if (parsed.flags.has('json')) {
        console.log(JSON.stringify(payload, null, 2));
      } else {
        console.log(formatResults(payload, opts.explain));
      }
      return 0;
    }

    case 'collect': {
      const q = parsed.positionals.join(' ').trim();
      const target = Number(parsed.flags.get('target') ?? 0);
      // -s/--scroll: N sayfa · değersiz = -1 (auto, tüm sayfalar)
      const processed = await runCollector(q, scrollOf(parsed.flags), true, {
        target: Number.isFinite(target) ? target : 0,
      });
      console.log(`collect: ${processed} iş işlendi.`);
      return 0;
    }

    case 'video': {
      const o = buildVideoOptions(parsed);
      const payload = await videoDetail(o.videoId, {
        comments: o.comments,
        related: o.related,
        ai: o.ai,
        offline: o.offline,
      });
      if (!payload) {
        console.error(`Geçersiz video ID/URL: ${parsed.positionals.join(' ')}`);
        return 2;
      }
      if (o.json) console.log(JSON.stringify(payload, null, 2));
      else console.log(formatVideoDetail(payload));
      return 0;
    }

    case 'enrich': {
      const limit = Number(parsed.flags.get('limit') ?? parsed.flags.get('l') ?? 20);
      await runEnrich(Number.isFinite(limit) && limit > 0 ? limit : 20);
      return 0;
    }

    case 'stats': {
      const videos = countVideos();
      const db = getDb();
      const ch = (db.prepare(`SELECT COUNT(*) AS c FROM channels`).get() as { c: number }).c;
      const pl = (db.prepare(`SELECT COUNT(*) AS c FROM playlists`).get() as { c: number }).c;
      const hist = (db.prepare(`SELECT COUNT(*) AS c FROM search_history`).get() as { c: number }).c;
      const pending = (db.prepare(`SELECT COUNT(*) AS c FROM jobs WHERE status='pending'`).get() as { c: number }).c;
      console.log(`NovaSearch durum`);
      console.log(`  videolar   : ${videos}`);
      console.log(`  kanallar   : ${ch}`);
      console.log(`  playlist   : ${pl}`);
      console.log(`  arama geçmişi : ${hist}`);
      console.log(`  bekleyen iş   : ${pending}`);
      console.log(`  AI            : ${aiAvailable() ? 'AÇIK' : 'KAPALI (key yok)'}`);
      console.log(`  offline mod   : ${config.offline ? 'AÇIK' : 'kapalı'}`);
      const top = trending(5);
      if (top.length) {
        console.log(`  en çok izlenen:`);
        for (const t of top) console.log(`    · ${t.title} (${t.viewCount})`);
      }
      return 0;
    }

    case 'suggest': {
      const prefix = parsed.positionals.join(' ').trim();
      const list = suggest(prefix);
      for (const s of list) console.log(s);
      return 0;
    }

    case 'cache': {
      const removed = new DiskCache().prune();
      console.log(`cache: ${removed} süresi dolmuş giriş silindi.`);
      return 0;
    }

    case 'serve': {
      await startApi();
      await startWeb();
      return 99; // sonsuz çalışır — return çağrılmaz
    }

    default:
      console.error(`Bilinmeyen komut: ${parsed.command} — -help`);
      return 2;
  }
}

main()
  .then((code) => {
    if (code !== 99) {
      closeDb();
      process.exit(code);
    }
  })
  .catch((e: unknown) => {
    if (e instanceof ArgError) {
      console.error(`Hata: ${e.message}`);
      process.exit(2);
    }
    console.error('Beklenmeyen hata:', e);
    process.exit(1);
  });
