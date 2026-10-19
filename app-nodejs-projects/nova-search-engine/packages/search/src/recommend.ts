// ===========================================
// Öneri Motoru — "Benzer öneriler"
// Prompt senaryosu: "Adele London Concert" aramasına
// "Adele Paris Concert", "Adele Royal Hall" vb.
// İndeks üzerinden terim benzerliği (V1; embedding V2).
// ===========================================

import type { Video } from '@novasearch/common';
import type { SearchIndex } from './search-index.js';

/**
 * Sonuç kümesinde olmayan benzer videolar önerir.
 * @param index  ters indeks (aday kaynağı)
 * @param seeds  mevcut sonuçlar (hariç tutulur)
 * @param terms  sorgu terimleri
 */
export async function recommend(
  index: SearchIndex,
  seeds: Video[],
  terms: string[],
  limit = 5,
): Promise<Video[]> {
  const exclude = new Set(seeds.map((v) => v.youtubeId));
  // Tohumlardan terim üret (başlık + kanal) — sorgu dar olsa bile benzerlik yakalanır
  const seedTerms = new Set<string>(terms);
  for (const s of seeds.slice(0, 3)) {
    for (const w of s.title.toLowerCase().replace(/[^\p{L}\p{N}\s]/gu, ' ').split(/\s+/)) {
      if (w.length > 2) seedTerms.add(w);
    }
    if (s.channelName) seedTerms.add(s.channelName.toLowerCase());
  }

  const candidates = await index.candidates([...seedTerms], (limit + exclude.size) * 3);
  return candidates.filter((v) => !exclude.has(v.youtubeId)).slice(0, limit);
}
