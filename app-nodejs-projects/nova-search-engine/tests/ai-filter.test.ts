// ===========================================
// Test: AI ilgi filtresi (fallback + JSON çıkarma)
// ===========================================

import { describe, expect, it } from 'bun:test';
import { aiRelevanceFilter, extractJson, OpenRouterClient, loadAIConfig } from '@novasearch/ai';

const items = [
  { youtubeId: 'a1', title: 'Tarkan Kuzu Kuzu', description: 'official' },
  { youtubeId: 'b2', title: 'Rastgele Vlog', description: 'başka konu' },
];

describe('aiRelevanceFilter', () => {
  it('AI yoksa boş harita döner (arama asla bozulmaz)', async () => {
    const out = await aiRelevanceFilter(undefined, 'tarkan', undefined, items);
    expect(out.size).toBe(0);
  });

  it('anahtar yoksa boş harita döner (ağ çağrısı yok)', async () => {
    const cfg = { ...loadAIConfig(), apiKey: '' };
    const client = new OpenRouterClient(cfg);
    const out = await aiRelevanceFilter(client, 'tarkan', 'resmi', items);
    expect(out.size).toBe(0);
  });
});

describe('extractJson (AI çıktısı kurtarma)', () => {
  it('saf JSON parse edilir', () => {
    expect(extractJson('{"a":1}', { a: 0 })).toEqual({ a: 1 });
  });

  it('kirli metindeki ilk JSON bloğu çıkarılır', () => {
    const raw = 'İşte sonuç: {"judgments":[{"id":"a1","relevance":0.9}]} bitti';
    const out = extractJson<{ judgments: Array<{ id: string }> }>(raw, { judgments: [] });
    expect(out.judgments[0]?.id).toBe('a1');
  });

  it('anlamsız yanıtta fallback döner', () => {
    expect(extractJson('cevap yok', { ok: true })).toEqual({ ok: true });
  });
});
