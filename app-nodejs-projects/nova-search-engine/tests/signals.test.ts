// ===========================================
// Test: sinyaller (dil tespiti + spam) + öneri
// ===========================================

import { describe, expect, it } from 'bun:test';
import { detectLanguage, spamScore } from '@novasearch/common';
import { recommend, SearchIndex } from '@novasearch/search';
import { createDatabase } from '@novasearch/database';
import type { Video } from '@novasearch/common';

describe('detectLanguage', () => {
  it('Türkçe içerik → tr', () => {
    expect(detectLanguage('Akasya Durağı 1. Bölüm', 'Türk komedi aile dizisi')).toBe('tr');
    expect(detectLanguage('Sezen Aksu Şarkı Sözleri')).toBe('tr');
  });

  it('İngilizce içerik → en', () => {
    expect(detectLanguage('Adele Hello Official Video', 'official music video live')).toBe('en');
  });

  it('boş/anlamsız → other', () => {
    expect(detectLanguage('', '')).toBe('other');
    expect(detectLanguage('12345')).toBe('other');
  });
});

describe('spamScore', () => {
  it('temiz başlık → düşük skor', () => {
    expect(spamScore('Tarkan - Kuzu Kuzu (Official Video)')).toBeLessThan(0.3);
  });

  it('tıklama tuzağı → yüksek skor', () => {
    const s = spamScore('ŞOK!! İNANAMAYACAKSIN TIKLA MUTLAKA İZLE!!!');
    expect(s).toBeGreaterThan(0.5);
  });

  it('boş metin → 0', () => {
    expect(spamScore('')).toBe(0);
  });
});

describe('recommend (benzer öneriler)', () => {
  function video(overrides: Partial<Video>): Video {
    return {
      youtubeId: 'x', title: '', description: '', channelId: 'c', channelName: 'ch',
      durationSeconds: 100, viewCount: 100, likeCount: 0, publishedAt: null,
      thumbnailUrl: '', tags: [], isShort: false, isLive: false, qualityScore: 0.5,
      source: 'youtube', url: 'https://www.youtube.com/watch?v=x', ...overrides,
    };
  }

  it('sonuç kümesindekiler önerilmez, benzerler önerilir', async () => {
    const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
    await db.videos.upsertMany([
      video({ youtubeId: 'a', title: 'Adele Hello Live' }),
      video({ youtubeId: 'b', title: 'Adele Paris Concert' }),
      video({ youtubeId: 'c', title: 'Adele Royal Hall' }),
      video({ youtubeId: 'd', title: 'Tamamen Farklı Konu' }),
    ]);
    const index = new SearchIndex(db);
    await index.ensureLoaded();
    const seeds = [video({ youtubeId: 'a', title: 'Adele Hello Live' })];
    const out = await recommend(index, seeds, ['adele'], 2);
    expect(out.map((v) => v.youtubeId)).not.toContain('a');
    expect(out.map((v) => v.youtubeId)).toContain('b');
    expect(out.map((v) => v.youtubeId)).not.toContain('d');
    await db.close();
  });
});
