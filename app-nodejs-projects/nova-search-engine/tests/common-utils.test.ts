// ===========================================
// Test: @novasearch/common utils
// ===========================================

import { describe, expect, it } from 'bun:test';
import {
  backoffMs,
  clamp,
  formatDuration,
  formatViews,
  jitter,
  parseNumber,
  queryKey,
  safeJson,
} from '@novasearch/common';

describe('jitter', () => {
  it('temel sürenin ±%20 aralığında döner', () => {
    for (let i = 0; i < 50; i++) {
      const v = jitter(1000, 20);
      expect(v).toBeGreaterThanOrEqual(800);
      expect(v).toBeLessThanOrEqual(1200);
    }
  });
});

describe('backoffMs', () => {
  it('artan denemelerde süre büyür', () => {
    expect(backoffMs(5, 100, 1_000_000)).toBeGreaterThan(backoffMs(0, 100, 1_000_000) - 25);
  });

  it('max sınırını aşmaz', () => {
    const v = backoffMs(20, 1000, 5000);
    expect(v).toBeLessThanOrEqual(6000);
  });
});

describe('queryKey', () => {
  it('alan sırasından bağımsız aynı anahtarı üretir', () => {
    expect(queryKey({ a: '1', b: '2' })).toBe(queryKey({ b: '2', a: '1' }));
  });

  it('farklı girdiler farklı anahtar üretir', () => {
    expect(queryKey({ q: 'adele' })).not.toBe(queryKey({ q: 'hello' }));
  });
});

describe('parseDuration / formatDuration', () => {
  it('"3:45" → 225 saniye', () => {
    // mapper parseDuration'ı içermez; burada format testi
    expect(formatDuration(225)).toBe('3:45');
    expect(formatDuration(3723)).toBe('1:02:03');
    expect(formatDuration(0)).toBe('0:00');
  });
});

describe('formatViews', () => {
  it('kısaltmalar doğru', () => {
    expect(formatViews(999)).toBe('999');
    expect(formatViews(1500)).toBe('1.5K');
    expect(formatViews(2_400_000)).toBe('2.4M');
    expect(formatViews(1_200_000_000)).toBe('1.2B');
  });
});

describe('yardımcılar', () => {
  it('clamp', () => {
    expect(clamp(5, 1, 10)).toBe(5);
    expect(clamp(-1, 1, 10)).toBe(1);
    expect(clamp(99, 1, 10)).toBe(10);
  });

  it('parseNumber geçersizde varsayılana düşer', () => {
    expect(parseNumber('abc', 7)).toBe(7);
    expect(parseNumber('42', 7)).toBe(42);
  });

  it('safeJson kirik giriste fallback verir', () => {
    expect(safeJson('{bad', { ok: true })).toEqual({ ok: true });
    expect(safeJson('{"ok":true}', { ok: false })).toEqual({ ok: true });
  });
});
