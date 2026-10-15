import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  parseCountLike, rowsToFields, lockupFields, shortsToRecord,
  commentViewToRecord, lockupToRelated,
} from '../src/providers/youtube.ts';

/* ---------- parseCountLike: compact + binlik ayracı (kanıt: probe "43K"/"106M views") ---------- */

test('parseCountLike: compact ve tam sayı biçimleri', () => {
  assert.equal(parseCountLike('43K'), 43000);
  assert.equal(parseCountLike('1.2M'), 1200000);
  assert.equal(parseCountLike('1.2 Mn'), 1200000);
  assert.equal(parseCountLike('106M views'), 106000000);
  assert.equal(parseCountLike('12K views'), 12000);
  assert.equal(parseCountLike('12,345'), 12345);
  assert.equal(parseCountLike('593'), 593);
  assert.equal(parseCountLike('No likes'), 0);
  assert.equal(parseCountLike(''), 0);
});

/* ---------- rowsToFields: iki kanıtlanmış satır varyantı ---------- */

test('rowsToFields: ilgili varyant [[yazar],[views,tarih]]', () => {
  const f = rowsToFields([['Kastamonu Cide Akbayır Köyü'], ['336K', '4mo ago']]);
  assert.equal(f.channelName, 'Kastamonu Cide Akbayır Köyü');
  assert.equal(f.viewCountText, '336K');
  assert.equal(f.viewCount, 336000);
  assert.equal(f.publishedText, '4mo ago');
});

test('rowsToFields: arama varyantı [[yazar,views,tarih]]', () => {
  const f = rowsToFields([['Tarkan', '106M views', '14 years ago']]);
  assert.equal(f.channelName, 'Tarkan');
  assert.equal(f.viewCountText, '106M views');
  assert.equal(f.viewCount, 106000000);
  assert.equal(f.publishedText, '14 years ago');
});

/* ---------- lockupFields / lockupToRelated: LockupView ---------- */

const lockupVideo = {
  type: 'LockupView',
  content_id: 'cpp69ghR1IM',
  content_type: 'VIDEO',
  metadata: {
    title: { text: 'Tarkan - Şerbetli (Official Music Video)' },
    metadata: {
      metadata_rows: [
        { metadata_parts: [{ text: 'Tarkan' }, { text: '106M views' }, { text: '14 years ago' }] },
      ],
    },
  },
  content_image: { image: [{ url: 'https://i.ytimg.com/a.jpg' }, { url: 'https://i.ytimg.com/big.jpg' }] },
};

test('lockupFields: VIDEO + tek satır rows + thumbnail son (büyük)', () => {
  const f = lockupFields(lockupVideo);
  assert.ok(f);
  assert.equal(f!.id, 'cpp69ghR1IM');
  assert.equal(f!.isShort, false);
  assert.equal(f!.channelName, 'Tarkan');
  assert.equal(f!.viewCount, 106000000);
  assert.equal(f!.publishedText, '14 years ago');
  assert.equal(f!.thumbnailUrl, 'https://i.ytimg.com/big.jpg');
});

test('lockupFields: PLAYLIST → null (sadece VIDEO/SHORT)', () => {
  assert.equal(lockupFields({ ...lockupVideo, content_type: 'PLAYLIST' }), null);
  assert.equal(lockupFields({ ...lockupVideo, content_id: '' }), null);
});

test('lockupToRelated: contentType + kanonik URL', () => {
  const r = lockupToRelated(lockupVideo);
  assert.ok(r);
  assert.equal(r!.contentType, 'VIDEO');
  assert.equal(r!.url, 'https://www.youtube.com/watch?v=cpp69ghR1IM');
  assert.equal(r!.title, 'Tarkan - Şerbetli (Official Music Video)');
});

/* ---------- shortsToRecord: ShortsLockupView (probe-4: "shorts-shelf-item-<id>") ---------- */

test('shortsToRecord: entity_id öneki + secondary_text izlenme + isShort', () => {
  const rec = shortsToRecord({
    type: 'ShortsLockupView',
    entity_id: 'shorts-shelf-item-Yr3UPC8Nhag',
    overlay_metadata: {
      primary_text: { text: 'Tarkan - Şerbetli (Official Music Video)' },
      secondary_text: { text: '12K views' },
    },
    thumbnail: [{ url: 'https://i.ytimg.com/s.jpg' }],
  });
  assert.ok(rec);
  assert.equal(rec!.youtubeId, 'Yr3UPC8Nhag');
  assert.equal(rec!.isShort, true);
  assert.equal(rec!.durationSec, 0);
  assert.equal(rec!.viewCount, 12000);
  assert.equal(rec!.thumbnailUrl, 'https://i.ytimg.com/s.jpg');
});

test('shortsToRecord: geçersiz entity_id → null', () => {
  assert.equal(shortsToRecord({ entity_id: 'shorts-shelf-item-kisa' }), null);
  assert.equal(shortsToRecord({ entity_id: '' }), null);
});

/* ---------- commentViewToRecord (probe-1: like_count "43K", reply_count "593") ---------- */

test('commentViewToRecord: alanlar + kanonik yorum URL', () => {
  const c = commentViewToRecord('NAHRpEqgcL4', {
    comment_id: 'Ugw3NNdjDIVEBYy5zIV4AaABAg',
    content: { text: 'Bu şarkı ana sayfana düşmedi…' },
    published_time: '6 years ago',
    like_count: '43K',
    reply_count: '593',
    is_pinned: false,
    author_is_channel_owner: false,
    author: { name: { text: '@fatmanurcoskun7259' }, id: 'UCmZ-tAqB_KG3KKUhyG4Gh3A' },
  });
  assert.ok(c);
  assert.equal(c!.commentId, 'Ugw3NNdjDIVEBYy5zIV4AaABAg');
  assert.equal(c!.likeCount, 43000);
  assert.equal(c!.replyCount, 593);
  assert.equal(c!.authorChannelId, 'UCmZ-tAqB_KG3KKUhyG4Gh3A');
  assert.equal(c!.url, 'https://www.youtube.com/watch?v=NAHRpEqgcL4&lc=Ugw3NNdjDIVEBYy5zIV4AaABAg');
});

test('commentViewToRecord: boş içerik → null (kaydedilmez)', () => {
  assert.equal(commentViewToRecord('x', { content: { text: '' } }), null);
});
