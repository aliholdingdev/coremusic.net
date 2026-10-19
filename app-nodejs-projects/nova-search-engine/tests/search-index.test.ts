// ===========================================
// Test: Search Index (MSA: önce indexle, sonra aday)
// + Metadata Refresh (stale) sorgusu
// ===========================================

import { describe, expect, it } from 'bun:test';
import { createDatabase } from '@novasearch/database';
import { SearchIndex } from '@novasearch/search';
import type { Video } from '@novasearch/common';

function video(overrides: Partial<Video> = {}): Video {
  return {
    youtubeId: 'v1',
    url: 'https://www.youtube.com/watch?v=v1',
    title: 'Tarkan Kuzu Kuzu',
    description: 'official music video',
    channelId: 'c1',
    channelName: 'Tarkan',
    durationSeconds: 240,
    viewCount: 1000,
    likeCount: 10,
    publishedAt: '2020-01-01',
    thumbnailUrl: '',
    tags: ['pop'],
    isShort: false,
    isLive: false,
    qualityScore: 0.5,
    source: 'youtube',
    ...overrides,
  };
}

async function seeded() {
  const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
  await db.videos.upsertMany([
    video(),
    video({ youtubeId: 'v2', title: 'Sezen Aksu Firuze', description: 'lyrics', channelName: 'Sezen', tags: ['arabesk'] }),
    video({ youtubeId: 'v3', title: 'Mor ve Ötesi Bir Derdim Var', description: 'rock', channelName: 'Mor', tags: ['rock'] }),
    video({ youtubeId: 'v4', title: 'Tarkan Yolla', description: 'official video', channelName: 'Tarkan', viewCount: 5000 }),
  ]);
  const index = new SearchIndex(db);
  await index.ensureLoaded();
  return { db, index };
}

describe('SearchIndex', () => {
  it('DB yüklenir ve terim sayısı oluşur', async () => {
    const { db, index } = await seeded();
    const stats = index.stats();
    expect(stats.videos).toBe(4);
    expect(stats.terms).toBeGreaterThan(5);
    await db.close();
  });

  it('doğru adayları bulur (ters indeks)', async () => {
    const { db, index } = await seeded();
    const out = await index.candidates(['tarkan'], 10);
    expect(out.map((v) => v.youtubeId).sort()).toEqual(['v1', 'v4']);
    await db.close();
  });

  it('limit bütçesine uyar (MSA: max N kayda bak)', async () => {
    const { db, index } = await seeded();
    const out = await index.candidates(['tarkan', 'sezen', 'rock'], 2);
    expect(out.length).toBe(2);
    await db.close();
  });

  it('popülerlik eşitliği bozar (v4 > v1)', async () => {
    const { db, index } = await seeded();
    const out = await index.candidates(['tarkan'], 1);
    expect(out[0]?.youtubeId).toBe('v4');
    await db.close();
  });

  it('artımlı ekleme sonrası yeni kayıt bulunur', async () => {
    const { db, index } = await seeded();
    index.add([video({ youtubeId: 'v5', title: 'Yeni Türkü Gemi', channelName: 'Yeni' })]);
    const out = await index.candidates(['türkü'], 10);
    expect(out.map((v) => v.youtubeId)).toContain('v5');
    await db.close();
  });

  it('önek eşleşmesi çalışır (tark → tarkan)', async () => {
    const { db, index } = await seeded();
    const out = await index.candidates(['tark'], 10);
    expect(out.length).toBeGreaterThan(0);
    await db.close();
  });
});

describe('staleVideos (metadata refresh)', () => {
  it('kesim tarihinden eski kayıtları döner', async () => {
    const db = await createDatabase({ type: 'sqlite', sqlitePath: ':memory:' });
    await db.videos.upsertMany([
      video({ youtubeId: 'old1', title: 'Old video' }),
      video({ youtubeId: 'new1', title: 'New video' }),
    ]);
    // hepsi yeni eklendi (updated_at=şimdi) → ileri tarihli kesim = hepsi stale
    const future = new Date(Date.now() + 86_400_000).toISOString();
    const stale = await db.videos.staleVideos(future, 10);
    expect(stale.length).toBe(2);
    // geçmiş kesim = hiçbiri stale değil
    const past = new Date(Date.now() - 86_400_000).toISOString();
    const none = await db.videos.staleVideos(past, 10);
    expect(none.length).toBe(0);
    await db.close();
  });
});
