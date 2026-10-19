#!/usr/bin/env node
// ===========================================
// nova-search-wizard — Etkileşimli menü CLI
// Acemi kullanıcılar için; tüm iş mantığı aynı
// run.ts üzerinden yürür (tek kaynak kuralı).
// ===========================================

import * as readline from 'node:readline/promises';
import { stdin, stdout } from 'node:process';
import { loadEnvFile } from '@novasearch/common';
import { runSearch, withEngine, type RawOptions } from './run.js';
import { renderAnalysis, renderResults, renderStats } from './format.js';
import { ProgressBar } from '@novasearch/common';

loadEnvFile();

// Test edilebilirlik: stdin TTY değilse (pipe) cevaplar önceden kuyruğa alınır.
const pipedAnswers: string[] = [];
if (!stdin.isTTY) {
  for await (const chunk of stdin) {
    for (const line of String(chunk).split(/\r?\n/)) {
      if (line.length > 0) pipedAnswers.push(line.trim());
    }
  }
}

const rl = readline.createInterface({ input: stdin, output: stdout });

async function ask(question: string): Promise<string> {
  if (pipedAnswers.length > 0) {
    const answer = pipedAnswers.shift() ?? '';
    stdout.write(`${question}${answer}\n`);
    return answer;
  }
  const answer = await rl.question(question);
  return answer.trim();
}

async function wizardSearch(ai: boolean): Promise<void> {
  const query = await ask('\nArama sorgunuz: ');
  if (!query) return;
  const resultStr = await ask('Kaç sonuç? [20]: ');
  const scrollStr = await ask('Kaynaktan kaç video çekilsin? [50]: ');
  const filterStr = ai
    ? await ask('AI yönlendirmesi (boş bırakılabilir): ')
    : '';
  const extraStr = ai
    ? await ask('Ekstra AI sorgusu (boş bırakılabilir): ')
    : '';

  const opts: RawOptions = {
    ai,
    result: resultStr || '20',
    scroll: scrollStr || '50',
    filter: 'all',
    query: filterStr || undefined,
    extraQuery: extraStr || undefined,
  };
  const progress = new ProgressBar();
  const res = await runSearch(query, opts, (e) => progress.handle(e));
  console.log('\n' + renderResults(res));
  if (res.aiUsed) console.log('\n' + renderAnalysis(res));
}

async function wizardCollect(): Promise<void> {
  const query = await ask('\nToplanacak sorgu: ');
  if (!query) return;
  const countStr = await ask('Kaç video? [100]: ');
  await withEngine(async (ctx) => {
    const count = parseInt(countStr || '100', 10) || 100;
    const videos = await ctx.collector.collectNow(query, {
      query, ai: false, result: count, scroll: count,
      filter: 'all', type: 'all', uploadDate: 'all', sortBy: 'relevance',
      minViews: 0, maxViews: 0, minScore: 0, duration: 'all', quality: 'all',
      page: 1, limit: count, offline: false, auto: false,
    });
    console.log(`\n${videos.length} video toplandı ve veritabanına yazıldı.`);
  });
}

async function wizardStats(): Promise<void> {
  await withEngine(async (ctx) => {
    const videoCount = await ctx.db.videos.count();
    console.log('\n' + renderStats({ videoCount, cache: ctx.cacheStats(), queue: ctx.collector.stats() }));
  });
}

async function main(): Promise<void> {
  console.log('╔══════════════════════════════════════╗');
  console.log('║   NovaSearch AI — Sihirbaz            ║');
  console.log('╚══════════════════════════════════════╝');

  let running = true;
  while (running) {
    console.log(`
  1) Hızlı arama
  2) AI destekli arama
  3) Metadata topla (collect)
  4) İstatistikler
  5) Çıkış`);
    const choice = await ask('Seçiminiz [1-5]: ');
    switch (choice) {
      case '1':
        await wizardSearch(false);
        break;
      case '2':
        await wizardSearch(true);
        break;
      case '3':
        await wizardCollect();
        break;
      case '4':
        await wizardStats();
        break;
      case '5':
      case '':
        running = false;
        break;
      default:
        console.log('Geçersiz seçim.');
    }
  }
  rl.close();
  console.log('Görüşürüz!');
}

main().catch((err: unknown) => {
  console.error(`Hata: ${err instanceof Error ? err.message : String(err)}`);
  process.exit(1);
});
