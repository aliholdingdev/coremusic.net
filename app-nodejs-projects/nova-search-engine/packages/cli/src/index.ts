#!/usr/bin/env node
// ===========================================
// nova-search — Parametrik CLI
// Kullanım: nova-search search "Akasya Durağı" -ai -r 100 -s 500
// Menü yok; her şey --flag ile (kullanıcı kararı).
// ===========================================

import { Command } from 'commander';
import { loadEnvFile, parseNumber } from '@novasearch/common';
import { getStreamInfo } from '@novasearch/youtube';
import { runSearch, withEngine, type RawOptions } from './run.js';
import { renderAnalysis, renderResults, renderStats, renderStream, renderTrending } from './format.js';
import { ProgressBar } from '@novasearch/common';

loadEnvFile();

/** Tek tireli uzun bayrak desteği: -ai → --ai (commander uyumu) */
function normalizeArgv(argv: string[]): string[] {
  const aliases: Record<string, string> = {
    '-ai': '--ai',
    '-query': '--query',
    '-eq': '--extra-query',
    '-udate': '--upload-date',
    '-sb': '--sort-by',
    '-minv': '--min-views',
    '-maxv': '--max-views',
    '-mins': '--min-score',
    '-qa': '--quality',
  };
  return argv.map((a) => aliases[a] ?? a);
}

const program = new Command();

program
  .name('nova-search')
  .description('NovaSearch AI — bağımsız YouTube metadata arama motoru')
  .version('0.1.0');

program
  .command('search')
  .description('Video ara (AI destekli: --ai)')
  .argument('<query>', 'Arama sorgusu')
  .option('--ai', 'AI destekli sorgu genişletme ve skorlama')
  .option('-q, --query <text>', 'AI için ek yönlendirme promptu')
  .option('-eq, --extra-query <text>', 'Ekstra AI sorgusu')
  .option('-r, --result <n>', 'Gösterilecek sonuç sayısı', '20')
  .option('-s, --scroll <n>', 'Kaynaktan çekilecek sonuç (overscan)', '50')
  .option('-f, --filter <type>', 'İçerik: v(video) s(song) sh(shorts) all', 'all')
  .option('-t, --type <type>', 'YouTube tipi: video, channel, playlist, all', 'all')
  .option('--udate, --upload-date <date>', 'hour, today, week, month, year, all', 'all')
  .option('-d, --duration <type>', 'short, medium, long, all', 'all')
  .option('--sb, --sort-by <sort>', 'relevance, rating, upload_date, view_count', 'relevance')
  .option('--minv, --min-views <n>', 'Minimum izlenme', '0')
  .option('--maxv, --max-views <n>', 'Maksimum izlenme (0=sınırsız)', '0')
  .option('--mins, --min-score <n>', 'Minimum AI skoru (0-1)', '0')
  .option('-qa, --quality <level>', 'high, medium, low, all', 'all')
  .option('-p, --page <n>', 'Sayfa numarası', '1')
  .option('-l, --limit <n>', 'Sayfa başına sonuç')
  .option('--offline', 'Yalnız yerel veri (internet kullanma)')
  .option('--no-auto', 'Arka planda otomatik derin toplamayı kapat')
  .option('--json', 'Sonucu JSON yaz')
  .action(async (query: string, opts: RawOptions) => {
    // --ai verilmiş ama AI sorgusu (-q) girilmemişse sor (etkileşimli terminalde)
    if (opts['ai'] && !opts['query'] && process.stdin.isTTY) {
      const readline = await import('node:readline/promises');
      const rl = readline.createInterface({ input: process.stdin, output: process.stderr });
      try {
        const answer = (await rl.question('AI yönlendirme sorgusu (boş bırakılabilir): ')).trim();
        if (answer) opts['query'] = answer;
      } finally {
        rl.close();
      }
    }

    const jsonMode = Boolean(opts['json']);
    const progress = new ProgressBar();
    const res = await runSearch(query, opts, jsonMode ? undefined : (e) => progress.handle(e));
    if (jsonMode) {
      console.log(JSON.stringify(res, null, 2));
    } else {
      console.log('');
      console.log(renderResults(res));
      if (res.aiUsed || opts['ai']) {
        console.log('');
        console.log(renderAnalysis(res));
      }
    }
  });

program
  .command('collect')
  .description('Sorgu için metadata topla ve veritabanına yaz (arka plan işi)')
  .argument('<query>', 'Toplanacak sorgu')
  .option('-s, --scroll <n>', 'Çekilecek sonuç sayısı', '100')
  .action(async (query: string, opts: RawOptions) => {
    await withEngine(async (ctx) => {
      const count = parseInt(String(opts['scroll'] ?? '100'), 10) || 100;
      const videos = await ctx.collector.collectNow(query, {
        query, ai: false, result: count, scroll: count,
        filter: 'all', type: 'all', uploadDate: 'all', sortBy: 'relevance',
        minViews: 0, maxViews: 0, minScore: 0, duration: 'all', quality: 'all',
        page: 1, limit: count, offline: false, auto: false,
      });
      console.log(`${videos.length} video toplandı ve veritabanına yazıldı.`);
    });
  });

program
  .command('stats')
  .description('Veritabanı, cache, index ve kuyruk istatistikleri')
  .action(async () => {
    await withEngine(async (ctx) => {
      const videoCount = await ctx.db.videos.count();
      console.log(renderStats({
        videoCount,
        cache: ctx.cacheStats(),
        queue: ctx.collector.stats(),
        index: ctx.engine.indexStats(),
      }));
    });
  });

program
  .command('trending')
  .description('Trend: en çok izlenenler + son eklenenler')
  .option('-r, --result <n>', 'Listelenecek sayı', '5')
  .action(async (opts: RawOptions) => {
    await withEngine(async (ctx) => {
      const n = parseInt(String(opts['result'] ?? '5'), 10) || 5;
      const top = await ctx.db.videos.topViewed(n);
      const recent = await ctx.db.videos.recent(n);
      console.log(renderTrending(top, recent));
    });
  });

program
  .command('refresh')
  .description('Bayat metadata kayıtlarını tazele (istatistik/güncellenebilir alanlar)')
  .option('--days <n>', 'Kaç günden eski kayıtlar tazelensin', '7')
  .option('-l, --limit <n>', 'En fazla kaç kayıt', '50')
  .action(async (opts: RawOptions) => {
    await withEngine(async (ctx) => {
      const days = Math.max(0, parseNumber(opts['days'], 7));
      const limit = Math.max(1, parseNumber(opts['limit'], 50));
      const progress = new ProgressBar();
      progress.draw(0, `Tazeleme: ${days} günden eski kayıtlar…`);
      const updated = await ctx.collector.refreshStale(days, limit);
      progress.finish(`${updated} kayıt tazelendi`);
    });
  });

program
  .command('backup')
  .description('Veritabanı anlık görüntüsü (snapshot) al')
  .action(async () => {
    await withEngine(async (ctx) => {
      const { mkdirSync } = await import('node:fs');
      const { resolve } = await import('node:path');
      const dir = resolve(process.cwd(), 'data', 'backups');
      mkdirSync(dir, { recursive: true });
      const stamp = new Date().toISOString().replace(/[:.]/g, '-');
      if (ctx.db.adapter.dialect === 'sqlite') {
        const target = resolve(dir, `novasearch-${stamp}.db`);
        await ctx.db.adapter.exec(`VACUUM INTO '${target.replace(/'/g, "''")}'`);
        console.log(`Yedek alındı: ${target}`);
      } else {
        console.log('MySQL için: mysqldump kullanın (örn. mysqldump novasearch > backup.sql)');
      }
    });
  });

program
  .command('stream')
  .description('Video akış/format bilgisi (GoogleVideo resolver)')
  .argument('<videoId>', 'YouTube video kimliği')
  .action(async (videoId: string) => {
    const info = await getStreamInfo(videoId);
    if (!info) {
      console.log('Akış bilgisi bulunamadı.');
      return;
    }
    console.log(renderStream(info));
  });

program.parseAsync(normalizeArgv(process.argv)).catch((err: unknown) => {
  console.error(`Hata: ${err instanceof Error ? err.message : String(err)}`);
  process.exit(1);
});
