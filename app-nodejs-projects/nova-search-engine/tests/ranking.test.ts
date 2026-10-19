// ===========================================
// Test: ranking + AI skorlama (saf fonksiyonlar)
// ===========================================

import { describe, expect, it } from 'bun:test';
import { applyFilters, paginate, sortVideos } from '@novasearch/search';
import { analyzeQueryHeuristicForTest, rankVideosFixture } from './helpers.js';

describe('applyFilters', () => {
  it('minViews süzgeci çalışır', () => {
    const q = rankVideosFixture.query({ minViews: 5000 });
    const out = applyFilters(rankVideosFixture.videos(), q);
    expect(out.every((v) => v.viewCount >= 5000)).toBe(true);
    expect(out.length).toBeLessThan(rankVideosFixture.videos().length);
  });

  it('shorts süzgeci duration=short ile çalışır', () => {
    const q = rankVideosFixture.query({ duration: 'short' });
    const out = applyFilters(rankVideosFixture.videos(), q);
    expect(out.every((v) => v.durationSeconds < 300)).toBe(true);
  });
});

describe('paginate', () => {
  it('sayfa dilimleri doğru', () => {
    const items = [1, 2, 3, 4, 5, 6, 7];
    const q = rankVideosFixture.query({ page: 2, limit: 3 });
    expect(paginate(items, q)).toEqual([4, 5, 6]);
  });
});

describe('sortVideos', () => {
  it('view_count sıralaması', () => {
    const q = rankVideosFixture.query({ sortBy: 'view_count' });
    const out = sortVideos(rankVideosFixture.videos(), q);
    const views = out.map((v) => v.viewCount);
    expect(views).toEqual([...views].sort((a, b) => b - a));
  });
});

describe('analyzeQuery (heuristic)', () => {
  it('canlı/official ipuçlarını yakalar', async () => {
    const a = await analyzeQueryHeuristicForTest('adele live in london official');
    expect(a.hints.wantsLive).toBe(true);
    expect(a.hints.wantsOfficial).toBe(true);
  });

  it('yıl aralığını ayrıştırır', async () => {
    const a = await analyzeQueryHeuristicForTest('rock konserleri 2010-2015');
    expect(a.hints.yearFrom).toBe(2010);
    expect(a.hints.yearTo).toBe(2015);
    expect(a.expandedQueries.length).toBeGreaterThan(1);
  });

  it('türkçe sorguda dil ipucu tr olur', async () => {
    const a = await analyzeQueryHeuristicForTest('akasya durağı şarkı');
    expect(a.hints.language).toBe('tr');
  });
});

describe('rankVideos', () => {
  it('eşleşen terimi olan video daha yüksek skor alır', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele hello');
    const scored = rankVideosFixture.rank(analysis);
    expect(scored[0]?.title.toLowerCase()).toContain('adele');
    expect(scored[0]?.score).toBeGreaterThan(scored[scored.length - 1]?.score ?? 0);
  });
});
