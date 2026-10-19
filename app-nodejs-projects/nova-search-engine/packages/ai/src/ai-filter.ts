// ===========================================
// AI İlgİ Fİltresİ — sonuçları sorguya göre
// yapay zekâ ile puanla ve elenmesi gerekenleri
// çıkar. ASLA aramayı bozmaz: AI yoksa/hatada
// boş harita döner (değişiklik olmaz).
// ===========================================

import { extractJson, type OpenRouterClient } from './client.js';

export interface AIJudgment {
  id: string;
  relevance: number;
}

/**
 * Top adayları AI ile değerlendir.
 * Dönüş: youtubeId → ilgi puanı (0..1). Boş harita = değerlendirme yok.
 */
export async function aiRelevanceFilter(
  client: OpenRouterClient | undefined,
  query: string,
  directive: string | undefined,
  items: Array<{ youtubeId: string; title: string; description: string }>,
): Promise<Map<string, number>> {
  const out = new Map<string, number>();
  if (!client?.enabled || items.length === 0) return out;

  const payload = items.slice(0, 20).map((it, i) => ({
    n: i + 1,
    id: it.youtubeId,
    title: it.title.slice(0, 120),
    desc: it.description.slice(0, 160),
  }));

  const result = await client.chatJSON<{ judgments?: AIJudgment[] }>(
    [
      {
        role: 'system',
        content:
          'Sen arama sonucu değerlendiricisisin. Kullanıcının arama niyetine göre her videoyu ' +
          '0-1 arası "relevance" puanıyla değerlendir; alakasız olanlara düşük puan ver. ' +
          'SADECE JSON döndür: {"judgments":[{"id":"...","relevance":0.0}]}',
      },
      {
        role: 'user',
        content: JSON.stringify({
          query,
          directive: directive || undefined,
          results: payload,
        }),
      },
    ],
    { judgments: [] },
  );

  for (const j of result.judgments ?? []) {
    if (j && typeof j.id === 'string' && Number.isFinite(j.relevance)) {
      out.set(j.id, Math.max(0, Math.min(1, Number(j.relevance))));
    }
  }
  return out;
}
