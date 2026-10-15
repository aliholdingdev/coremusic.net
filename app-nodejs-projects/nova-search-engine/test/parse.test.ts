import { test } from 'node:test';
import assert from 'node:assert/strict';
import { parseArgs, buildSearchOptions, ArgError } from '../src/cli/parse.ts';

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
