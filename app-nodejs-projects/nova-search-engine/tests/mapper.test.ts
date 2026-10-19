// ===========================================
// Test: göreli tarih ayrıştırma (zengin metadata)
// ===========================================

import { describe, expect, it } from 'bun:test';
import { parseRelativeDate, parseViews, parseDuration, mapToVideo } from '@novasearch/youtube';

describe('parseRelativeDate', () => {
  it('"2 years ago" → yaklaşık 2 yıl önce', () => {
    const iso = parseRelativeDate('2 years ago');
    expect(iso).not.toBeNull();
    const days = (Date.now() - new Date(iso ?? '').getTime()) / 86_400_000;
    expect(days).toBeGreaterThan(700);
    expect(days).toBeLessThan(760);
  });

  it('"3 ay önce" → yaklaşık 90 gün önce', () => {
    const iso = parseRelativeDate('3 ay önce');
    const days = (Date.now() - new Date(iso ?? '').getTime()) / 86_400_000;
    expect(days).toBeGreaterThan(80);
    expect(days).toBeLessThan(100);
  });

  it('"1 Haz 2016" → 2016-06-01', () => {
    expect(parseRelativeDate('1 Haz 2016')).toBe('2016-06-01T00:00:00.000Z');
  });

  it('"Jun 1, 2016" → 2016-06-01', () => {
    expect(parseRelativeDate('Jun 1, 2016')).toBe('2016-06-01T00:00:00.000Z');
  });

  it('anlamsız metin → null', () => {
    expect(parseRelativeDate('canlı şimdi')).toBeNull();
  });
});

describe('mapToVideo zengin alanlar', () => {
  it('badges/thumbnails/publishedText/url çıkarılır', () => {
    const v = mapToVideo({
      video_id: 'abc',
      title: 'Test Video',
      length_text: '3:45',
      view_count: { text: '1.234.567 views' },
      published: { text: '2 years ago' },
      thumbnails: [{ url: '//i.ytimg.com/1.jpg' }, { url: 'https://i.ytimg.com/2.jpg' }],
      badges: [{ label: '4K' }, { label: 'CC' }],
      author: { id: 'ch1', name: 'Kanal' },
    });
    expect(v).not.toBeNull();
    expect(v?.url).toBe('https://www.youtube.com/watch?v=abc');
    expect(v?.channelUrl).toBe('https://www.youtube.com/channel/ch1');
    expect(v?.viewCountText).toBe('1.234.567 views');
    expect(v?.badges).toEqual(['4K', 'CC']);
    expect(v?.thumbnails?.length).toBe(2);
    expect(v?.thumbnails?.[0]).toBe('https://i.ytimg.com/1.jpg');
    expect(v?.publishedText).toBe('2 years ago');
    expect(v?.publishedAt).not.toBeNull();
    expect(v?.viewCount).toBe(1234567);
    expect(v?.durationSeconds).toBe(225);
  });
});

describe('parseViews / parseDuration', () => {
  it('kısa metinler', () => {
    expect(parseViews('12.345 izlenme')).toBe(12345);
    expect(parseDuration('1:02:03')).toBe(3723);
  });

  it('TR kısaltmaları: B=bin, Mn=milyon, Mr=milyar', () => {
    expect(parseViews('273 B abone')).toBe(273_000);
    expect(parseViews('1,2 Mn izlenme')).toBe(1_200_000);
    expect(parseViews('4,5 Mr izlenme')).toBe(4_500_000_000);
    expect(parseViews('5 bin beğeni')).toBe(5_000);
    expect(parseViews('41 B yorum')).toBe(41_000);
    expect(parseViews('2,73 Mn abone')).toBe(2_730_000);
  });

  it('EN kısaltmaları: K/M/B', () => {
    expect(parseViews('1.2M views')).toBe(1_200_000);
    expect(parseViews('45K views')).toBe(45_000);
  });
});
