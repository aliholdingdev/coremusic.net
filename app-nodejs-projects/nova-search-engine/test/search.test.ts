import { test } from 'node:test';
import assert from 'node:assert/strict';
import { isCompleteDetail } from '../src/core/search.ts';
import type { VideoRecord } from '../src/core/types.ts';

/* ---------- isCompleteDetail: tam kayıt → web isteği YOK (daha az istekle bilgi kazıma) ---------- */

function rec(patch: Partial<VideoRecord> = {}): VideoRecord {
  return {
    youtubeId: 'id1', title: 'T', description: '', channelId: 'UC1', channelName: 'K',
    channelUrl: null, durationSec: 50, viewCount: 1000, likeCount: 10, publishedAt: '2026-01-19T02:55:01-08:00',
    category: 'Music', tags: '', thumbnailUrl: null,
    firstSeenAt: '2026-01-01T00:00:00.000Z', lastSeenAt: '2026-01-01T00:00:00.000Z',
    ...patch,
  };
}

test('isCompleteDetail: tam kayıt (description/tags/channelUrl boş olsa bile) → true (istek atlanır)', () => {
  // kanıt: YouTube bazı videolarda desc/tags HİÇ vermez — zorunlu olsaydı her aramada boşuna istek atılırdı
  assert.equal(isCompleteDetail(rec()), true);
  assert.equal(isCompleteDetail(rec({ description: '', tags: '', channelUrl: null })), true);
});

test('isCompleteDetail: eksik zorunlu alanlar → false (webden kazınır)', () => {
  assert.equal(isCompleteDetail(null), false);                       // video DB'de yok
  assert.equal(isCompleteDetail(rec({ channelId: '' })), false);      // arama kaydı enrichment'sız
  assert.equal(isCompleteDetail(rec({ channelName: '' })), false);
  assert.equal(isCompleteDetail(rec({ durationSec: 0 })), false);     // shorts mapper → süre yok
  assert.equal(isCompleteDetail(rec({ likeCount: null })), false);
  assert.equal(isCompleteDetail(rec({ publishedAt: null })), false);
  // eski bozuk değer: göreli metin ISO değil → eksik sayılır, enrichment ISO ile doldurur
  assert.equal(isCompleteDetail(rec({ publishedAt: '6 months ago' as string })), false);
  assert.equal(isCompleteDetail(rec({ category: null })), false);
});
