import { test } from 'node:test';
import assert from 'node:assert/strict';
import { parseArgs, buildSearchOptions, buildVideoOptions, scrollOf, ArgError } from '../src/cli/parse.ts';
import { extractVideoId } from '../src/core/video-detail.ts';

test('kısa ve uzun flag eşitleri', () => {
  const p = parseArgs(['search', 'adele', '-ai', '--limit', '10', '-f', 'v']);
  assert.equal(p.command, 'search');
  assert.deepEqual(p.positionals, ['adele']);
  assert.equal(p.flags.get('ai'), true);
  assert.equal(p.flags.get('limit'), '10');
  assert.equal(p.flags.get('f'), 'v');
});

test('= biçimli flag: --query=x', () => {
  const p = parseArgs(['search', 'q', '--query=ek terim']);
  assert.equal(p.flags.get('query'), 'ek terim');
});

test('birden fazla positional sorguya birleşir', () => {
  const p = parseArgs(['search', 'lofi', 'chill', 'beats']);
  const o = buildSearchOptions(p);
  assert.equal(o.query, 'lofi chill beats');
});

test('bilinmeyen flag → ArgError', () => {
  assert.throws(() => parseArgs(['search', 'x', '-bogus']), ArgError);
});

test('değer bekleyen flag eksik değer → ArgError', () => {
  assert.throws(() => parseArgs(['search', 'x', '-limit']), ArgError);
});

test('sayısal olmayan limit → ArgError', () => {
  const p = parseArgs(['search', 'x', '-limit', 'abc']);
  assert.throws(() => buildSearchOptions(p), ArgError);
});

test('geçersiz filter → ArgError (yalnız v|s|sh)', () => {
  const p = parseArgs(['search', 'x', '-f', 'zzz']);
  assert.throws(() => buildSearchOptions(p), ArgError);
});

test('geçersiz sortBy → ArgError', () => {
  const p = parseArgs(['search', 'x', '-sb', 'yok']);
  assert.throws(() => buildSearchOptions(p), ArgError);
});

test('sorgu yoksa ArgError', () => {
  const p = parseArgs(['search']);
  assert.throws(() => buildSearchOptions(p), ArgError);
});

test('limit ve page normalizationı (clamp)', () => {
  const p = parseArgs(['search', 'x', '-limit', '9999', '-page', '0']);
  const o = buildSearchOptions(p);
  assert.equal(o.limit, 5000); // üst sınır 5000+
  assert.equal(o.page, 1); // alt sınır
});

test('tam parametre kümesi prompt tablosuyla eşleşiyor', () => {
  const p = parseArgs([
    'search', 'Akasya Durağı',
    '-ai', '-deep', '-explain', '-offline',
    '-q', 'ek ai', '-eq', 'terim', '-s', '3', '-r', '100', '-l', '24',
    '-f', 's', '-sb', 'views', '-minv', '1000', '-maxv', '9000000',
    '-mins', '70', '-qa', '60', '-d', '>600', '-p', '2',
    '-t', 'music', '-ud', '<=1y',
  ]);
  const o = buildSearchOptions(p);
  assert.equal(o.ai, true);
  assert.equal(o.deep, true);
  assert.equal(o.explain, true);
  assert.equal(o.offline, true);
  assert.equal(o.extraQuery, 'terim');
  assert.equal(o.scroll, 3);
  assert.equal(o.limit, 100);
  assert.equal(o.filter, 's');
  assert.equal(o.sortBy, 'views');
  assert.equal(o.minViews, 1000);
  assert.equal(o.maxViews, 9000000);
  assert.equal(o.minScore, 70);
  assert.equal(o.quality, 60);
  assert.equal(o.duration, '>600');
  assert.equal(o.page, 2);
  assert.equal(o.type, 'music');
  assert.equal(o.uploadDate, '<=1y');
  assert.equal(o.aiPrompt, 'ek ai'); // positional varken -q AI prompt'a dönüşür
});

test('AI prompt parametresi (-aip / --aiPrompt / -q fallback)', () => {
  const a = buildSearchOptions(parseArgs(['search', 'x', '-aip', 'yalnız resmi klipleri tercih et']));
  assert.equal(a.aiPrompt, 'yalnız resmi klipleri tercih et');

  const b = buildSearchOptions(parseArgs(['search', 'x', '--aiPrompt=sadece 4K']));
  assert.equal(b.aiPrompt, 'sadece 4K');

  const c = buildSearchOptions(parseArgs(['search', 'x', '-q', 'ek prompt']));
  assert.equal(c.aiPrompt, 'ek prompt');

  const d = buildSearchOptions(parseArgs(['search', 'x']));
  assert.equal(d.aiPrompt, undefined);
});

test('-s/--scroll: değersiz = auto (-1), sayı = N sayfa', () => {
  // değersiz → auto (tüm sayfalar)
  assert.equal(scrollOf(parseArgs(['search', 'x', '-s']).flags), -1);
  assert.equal(scrollOf(parseArgs(['search', 'x', '--scroll']).flags), -1);
  assert.equal(scrollOf(parseArgs(['search', 'x', '-s', '-ai']).flags), -1); // sonraki bayraksa değer değil
  // sayısal değer → N sayfa (alt sınır 1)
  assert.equal(scrollOf(parseArgs(['search', 'x', '-s', '3']).flags), 3);
  assert.equal(scrollOf(parseArgs(['search', 'x', '--scroll=4']).flags), 4);
  assert.equal(scrollOf(parseArgs(['search', 'x', '-s', '0']).flags), 1);
  // hiç verilmezse 1
  assert.equal(scrollOf(parseArgs(['search', 'x']).flags), 1);
  // buildSearchOptions auto'yu scroll=-1 olarak geçirir
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '-s'])).scroll, -1);
  // sayısal olmayan değer → ArgError
  assert.throws(() => scrollOf(parseArgs(['search', 'x', '-s', 'abc']).flags), ArgError);
});

test('collect: -s auto + --target birlikte', () => {
  const p = parseArgs(['collect', 'tarkan', '-s']);
  assert.equal(scrollOf(p.flags), -1);
  const q = parseArgs(['collect', 'tarkan', '-s', '--target', '300']);
  assert.equal(scrollOf(q.flags), -1);
  assert.equal(q.flags.get('target'), '300');
});

test('-detail: bool bayrak + =1/=0 / true/false değerleri → default AÇIK', () => {
  // default açık: bayrak yoksa bile detail=true (boş/null alanlar webden kazınıp dolar)
  assert.equal(buildSearchOptions(parseArgs(['search', 'x'])).detail, true);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '-detail'])).detail, true);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail'])).detail, true);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail=1'])).detail, true);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail=true'])).detail, true);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail=yes'])).detail, true);
  // kapatma: 0 / false / no / off
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail=0'])).detail, false);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail=false'])).detail, false);
  assert.equal(buildSearchOptions(parseArgs(['search', 'x', '--detail=off'])).detail, false);
});

test('tüm bool bayraklar hem 1/0 hem true/false ile', () => {
  const on = buildSearchOptions(parseArgs(['search', 'x', '--ai=1', '--explain=true', '--deep=yes', '--offline=on']));
  assert.equal(on.ai, true);
  assert.equal(on.explain, true);
  assert.equal(on.deep, true);
  assert.equal(on.offline, true);
  const off = buildSearchOptions(parseArgs(['search', 'x', '--ai=0', '--explain=false', '--deep=0', '--offline=false', '--detail=0']));
  assert.equal(off.ai, false);
  assert.equal(off.explain, false);
  assert.equal(off.deep, false);
  assert.equal(off.detail, false);
});

test('--auto kaldırıldı → bilinmeyen parametre hatası', () => {
  assert.throws(() => parseArgs(['collect', 'tarkan', '--auto']), ArgError);
});

/* ---------- nova video <id|url> ---------- */

test('extractVideoId: ham ID / watch URL / youtu.be / shorts', () => {
  assert.equal(extractVideoId('NAHRpEqgcL4'), 'NAHRpEqgcL4');
  assert.equal(extractVideoId('https://www.youtube.com/watch?v=NAHRpEqgcL4'), 'NAHRpEqgcL4');
  assert.equal(extractVideoId('https://youtu.be/NAHRpEqgcL4?t=10'), 'NAHRpEqgcL4');
  assert.equal(extractVideoId('https://www.youtube.com/shorts/Yr3UPC8Nhag'), 'Yr3UPC8Nhag');
  assert.equal(extractVideoId('https://m.youtube.com/watch?v=NAHRpEqgcL4&feature=emb'), 'NAHRpEqgcL4');
});

test('extractVideoId: geçersiz girdi → null', () => {
  assert.equal(extractVideoId(''), null);
  assert.equal(extractVideoId('kısa'), null);
  assert.equal(extractVideoId('https://example.com/watch?v=NAHRpEqgcL4'), 'NAHRpEqgcL4'); // host serbest: v param yeterli
  assert.equal(extractVideoId('https://www.youtube.com/watch?v=kisa'), null);
  assert.equal(extractVideoId('https://www.youtube.com/results?search_query=x'), null);
});

test('video komutu: flags + clamp (comments 0-50, related 0-20)', () => {
  const p = parseArgs(['video', 'NAHRpEqgcL4', '-comments', '5', '-related', '8', '-ai', '-json']);
  const o = buildVideoOptions(p);
  assert.equal(o.videoId, 'NAHRpEqgcL4');
  assert.equal(o.comments, 5);
  assert.equal(o.related, 8);
  assert.equal(o.ai, true);
  assert.equal(o.json, true);

  const c = buildVideoOptions(parseArgs(['video', 'NAHRpEqgcL4', '-comments', '999', '-related', '-5']));
  assert.equal(c.comments, 50);
  assert.equal(c.related, 0);
  assert.equal(c.ai, false);
});

test('video komutu: URL positional kabul + eksik/geçersiz ID ArgError', () => {
  const o = buildVideoOptions(parseArgs(['video', 'https://youtu.be/NAHRpEqgcL4']));
  assert.equal(o.videoId, 'NAHRpEqgcL4');

  assert.throws(() => buildVideoOptions(parseArgs(['video'])), ArgError);
  assert.throws(() => buildVideoOptions(parseArgs(['video', 'geçersiz-id'])), ArgError);
});
