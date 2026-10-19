// ===========================================
// Test: cache katmanları (RAM LRU + TTL + hybrid)
// ===========================================

import { describe, expect, it } from 'bun:test';
import { RamCache, cacheKeyFor } from '@novasearch/cache';

describe('RamCache', () => {
  it('yazılan değer okunur', () => {
    const c = new RamCache<string>(10, 60);
    c.set('k', 'v');
    expect(c.get('k')).toBe('v');
  });

  it('LRU kapasiteyi aşmaz', () => {
    const c = new RamCache<number>(3, 60);
    c.set('a', 1);
    c.set('b', 2);
    c.set('c', 3);
    c.set('d', 4); // 'a' dışarı
    expect(c.get('a')).toBeUndefined();
    expect(c.get('d')).toBe(4);
    expect(c.size).toBe(3);
  });

  it('TTL dolunca değer düşer', async () => {
    const c = new RamCache<string>(10, 0.01); // 10ms
    c.set('k', 'v');
    await new Promise((r) => setTimeout(r, 30));
    expect(c.get('k')).toBeUndefined();
  });

  it('erişilen kayıt LRU koruması kazanır', () => {
    const c = new RamCache<number>(2, 60);
    c.set('a', 1);
    c.set('b', 2);
    c.get('a'); // 'a' yeniden en yeni
    c.set('c', 3); // 'b' dışarı düşmeli
    expect(c.get('a')).toBe(1);
    expect(c.get('b')).toBeUndefined();
    expect(c.get('c')).toBe(3);
  });
});

describe('cacheKeyFor', () => {
  it('kapsam + normalize anahtar üretir', () => {
    const k1 = cacheKeyFor('search', { q: 'Adele', f: 'all' });
    const k2 = cacheKeyFor('search', { f: 'all', q: 'adele ' });
    expect(k1).toBe(k2);
    expect(k1.startsWith('search:')).toBe(true);
  });
});
