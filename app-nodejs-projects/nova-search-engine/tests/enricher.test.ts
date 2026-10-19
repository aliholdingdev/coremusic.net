// ===========================================
// Test: "Neden önerildi?" gerekçe üretimi + skor
// ===========================================

import { describe, expect, it } from 'bun:test';
import { buildReasons, scoreVideo } from '@novasearch/ai';
import type { Video } from '@novasearch/common';
import { analyzeQueryHeuristicForTest } from './helpers.js';

function video(overrides: Partial<Video> = {}): Video {
  return {
    youtubeId: 'x',
    url: 'https://www.youtube.com/watch?v=x',
    title: 'Adele Hello Official Video',
    description: 'official video clip',
    channelId: 'c',
    channelName: 'AdeleVEVO',
    durationSeconds: 360,
    viewCount: 2_000_000,
    likeCount: 100,
    publishedAt: '2024-06-01',
    thumbnailUrl: '',
    tags: ['adele', 'pop'],
    isShort: false,
    isLive: false,
    qualityScore: 0.5,
    source: 'youtube',
    ...overrides,
  };
}

describe('buildReasons', () => {
  it('başlık eşleşmesini açıklar', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele hello');
    const v = video();
    const reasons = buildReasons(v, analysis, ['adele', 'hello'], 0.8);
    expect(reasons.some((r) => r.includes('Başlıkta eşleşme'))).toBe(true);
  });

  it('yüksek izlenmeyi açıklar', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele');
    const reasons = buildReasons(video({ viewCount: 50_000_000 }), analysis, ['adele'], 0.5);
    expect(reasons.some((r) => r.includes('Yüksek izlenme'))).toBe(true);
  });

  it('canlı aramasıyla uyumu açıklar', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele live');
    const reasons = buildReasons(video({ isLive: true }), analysis, ['adele'], 0.5);
    expect(reasons.some((r) => r.includes('Canlı'))).toBe(true);
  });

  it('en fazla 4 gerekçe döner', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele hello live official');
    const reasons = buildReasons(
      video({ viewCount: 90_000_000, isLive: true, publishedAt: new Date().toISOString() }),
      analysis,
      ['adele', 'hello', 'live', 'official'],
      0.95,
    );
    expect(reasons.length).toBeLessThanOrEqual(4);
    expect(reasons.length).toBeGreaterThan(0);
  });
});

describe('scoreVideo', () => {
  it('gerekçeleriyle birlikte skor döner', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele hello');
    const scored = scoreVideo(video(), analysis);
    expect(scored.score).toBeGreaterThan(0);
    expect(scored.score).toBeLessThanOrEqual(1);
    expect(scored.matchedTerms.length).toBeGreaterThan(0);
    expect(scored.reasons.length).toBeGreaterThan(0);
  });

  it('alakasız video düşük skor alır', async () => {
    const analysis = await analyzeQueryHeuristicForTest('adele hello');
    const good = scoreVideo(video(), analysis);
    const bad = scoreVideo(
      video({ title: 'Tamamen Farklı Konu', description: 'başka', channelName: 'X', viewCount: 10 }),
      analysis,
    );
    expect(good.score).toBeGreaterThan(bad.score);
  });
});
