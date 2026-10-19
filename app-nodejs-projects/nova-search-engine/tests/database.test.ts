// ===========================================
// Test: database (SQLite adapter + repo dedup)
// ===========================================

import { describe, expect, it } from 'bun:test';
import { createDatabase } from '@novasearch/database';
import type { Video } from '@novasearch/common';

function sample(overrides: Partial<Video> = {}): Video {
  return {
    youtubeId: 'abc123',
    url: 'https://www.youtube.com/watch?v=abc123',
    title: 'Adele Live in London',
    description: 'official concert',
    channelId: 'ch1',
    channelName: 'AdeleVEVO',
    durationSeconds: 3600,
    viewCount: 1_000_000,
    likeCount: 10_000,
    publishedAt: '2016-06-01',
    thumbnailUrl: 'https://img/1.jpg',
    tags: ['adele', 'live'],
    isShort: false,
    isLive: false,
    qualityScore: 0.8,
    source: 'youtube',
    ...overrides,
  };
}

describe('SQLite database', () => {
  it('video upsert edilir ve okunur', async () => {
    const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
    await db.videos.upsertMany([sample()]);
    const found = await db.videos.findByYoutubeId('abc123');
    expect(found?.title).toBe('Adele Live in London');
    expect(await db.videos.count()).toBe(1);
    await db.close();
  });

  it('dedup: aynı youtube_id 2. kez ayrı kayıt açmaz, günceller', async () => {
    const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
    await db.videos.upsertMany([sample()]);
    await db.videos.upsertMany([sample({ title: 'Adele Live (Updated)', viewCount: 2_000_000 })]);
    expect(await db.videos.count()).toBe(1);
    const found = await db.videos.findByYoutubeId('abc123');
    expect(found?.title).toBe('Adele Live (Updated)');
    expect(found?.viewCount).toBe(2_000_000);
    await db.close();
  });

  it('yerel arama terimleri bulur', async () => {
    const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
    await db.videos.upsertMany([
      sample(),
      sample({
        youtubeId: 'x1',
        title: 'Hello Piano Tutorial',
        channelName: 'PianoChannel',
        description: 'tutorial',
        tags: ['piano'],
      }),
    ]);
    const hits = await db.videos.searchLocal(['adele'], 10);
    expect(hits.length).toBe(1);
    expect(hits[0]?.youtubeId).toBe('abc123');
    await db.close();
  });

  it('arama loglanır', async () => {
    const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
    await db.searchLog.log('adele', 5, 'local-db', 12);
    const recent = await db.searchLog.recent(5);
    expect(recent.length).toBe(1);
    expect(recent[0]?.query).toBe('adele');
    await db.close();
  });
});
