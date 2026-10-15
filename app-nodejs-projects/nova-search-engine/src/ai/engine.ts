/** AI Engine — OpenRouter chat. HATA ASLA ARAMAYI DÜŞÜRMEZ (fail-safe): key yok / 404 / timeout → null. */
import { config } from '../config.ts';

export function aiAvailable(): boolean {
  return config.ai.enabled && config.ai.apiKey.length > 0;
}

async function chatOnce(model: string, messages: unknown[], wantJson: boolean, budgetMs: number): Promise<string | null> {
  const ctl = new AbortController();
  const t = setTimeout(() => ctl.abort(), budgetMs);
  try {
    const res = await fetch('https://openrouter.ai/api/v1/chat/completions', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${config.ai.apiKey}`,
        'Content-Type': 'application/json',
        ...(wantJson ? { 'X-Return-Format': 'json' } : {}),
      },
      body: JSON.stringify({
        model,
        messages,
        temperature: 0.2,
        max_tokens: 4000,
        response_format: { type: 'json_object' },
      }),
      signal: ctl.signal,
    });
    if (!res.ok) return null; // 404 ölü slug / 429 kota → fallback denenir
    const data = (await res.json()) as { choices?: { message?: { content?: string } }[] };
    return data.choices?.[0]?.message?.content ?? null;
  } catch {
    return null; // timeout / ağ yok
  } finally {
    clearTimeout(t);
  }
}

/** Model zinciri: ana model → fallback'ler → null. Bütçe PAYLAŞILIR (toplam: config.ai.timeoutMs). */
export async function chat(messages: unknown[]): Promise<string | null> {
  if (!aiAvailable()) return null;
  const deadline = Date.now() + config.ai.timeoutMs;
  const models = [config.ai.model, ...config.ai.fallbackModels];
  for (const m of models) {
    const remaining = deadline - Date.now();
    if (remaining < 3000) break; // bütçe tükendi → null (fail-safe)
    const out = await chatOnce(m, messages, true, remaining);
    if (out) return out;
  }
  return null;
}

function extractJson<T>(text: string): T | null {
  const fenced = text.match(/```(?:json)?\s*([\s\S]*?)```/);
  const candidate = fenced ? fenced[1] : text;
  const start = candidate.indexOf('{');
  const end = candidate.lastIndexOf('}');
  if (start < 0 || end <= start) return null;
  try {
    return JSON.parse(candidate.slice(start, end + 1)) as T;
  } catch {
    return null;
  }
}

/* ---------- Görev 1: Query Understanding Agent ---------- */

/**
 * Sorgu analizi: filtre çıkarımı (bizim CLI parametrelerimize BİREBİR eşlenir) + ek sorgular.
 * Şema: filter=v|s|sh · sortBy · minViews · duration · uploadDate (CLI verilmediyse AI değeri uygulanır).
 */
export interface QueryAnalysis {
  rewritten?: string;
  expandedQueries?: string[];
  filters?: {
    filter?: 'v' | 's' | 'sh';
    sortBy?: 'relevance' | 'views' | 'date' | 'rating';
    minViews?: number;
    duration?: string; // ">600" | "60-300" (sn)
    uploadDate?: string; // "2015" | "<=1y"
  };
}

export async function analyzeQuery(query: string, extra?: string): Promise<QueryAnalysis | null> {
  const sys = [
    'Sen bir arama sorgusu analizcisisin. Sorguyu analiz et, kullanıcıya uygun filtreleri çıkar ve 0-3 ek sorgu üret.',
    'SADECE JSON döndür, açıklama yazma. Şema:',
    '{"rewritten": string,',
    ' "expandedQueries": ["ek sorgu1", "ek sorgu2"],',
    ' "filters": {',
    '   "filter": "v" | "s" | "sh",        // v=video (>60sn), s=şarkı (90-900sn), sh=short (<=60sn); emin değilsen yazma',
    '   "sortBy": "relevance" | "views" | "date" | "rating",',
    '   "minViews": number,                 // sorgu popülerlik beklentisi varsa',
    '   "duration": ">600" | "60-300",      // süre isteği varsa (sn)',
    '   "uploadDate": "2015" | "<=1y"       // tarih isteği varsa',
    ' }}',
    'Kural: sorguda açık istek yoksa filtreyi yazma (null değil, ANAHTARI OLMA).',
  ].join('\n');
  const out = await chat([
    { role: 'system', content: sys },
    { role: 'user', content: `Sorgu: ${query}${extra ? `\nEk terim: ${extra}` : ''}` },
  ]);
  if (!out) return null;
  return extractJson<QueryAnalysis>(out);
}

/* ---------- Görev 2: Quality Agent (toplu skor) ---------- */

export async function scoreResults(
  query: string,
  items: { id: string; title: string; channel: string; views: number; duration: number; published: string | null }[],
): Promise<Record<string, { score: number; reason: string }> | null> {
  const sys =
    'Sen kalite skorlayıcısın. Her videoya (1) sorguya uyumluluk (2) metadata kalitesi için 0-100 puan ver. ' +
    'Puanlama: 90-100 tam isabet · 70-89 güçlü · 50-69 kısmi · <50 alakasız. ' +
    'ZORUNLU: Girdiğin HER id anahtarında puanlanmalı — hiçbir video atlanmaz; alakasızsa 10-40 arası puan ver. ' +
    'Ara düşünme metni yazma; doğrudan geçerli JSON çıktısı ver. ' +
    'SADECE JSON döndür: {"scores": {"<id>": {"score": number, "reason": "tek cümle Türkçe"}}}';
  const out = await chat([
    { role: 'system', content: sys },
    { role: 'user', content: `Sorgu: ${query}\nVideolar:\n${JSON.stringify(items)}` },
  ]);
  if (!out) return null;
  const parsed = extractJson<{ scores?: Record<string, { score: number; reason: string }> }>(out);
  return parsed?.scores ?? null;
}

/* ---------- Görev 3: Deep Search — sorgu genişletme ---------- */

export async function expandQueries(query: string): Promise<string[]> {
  const sys =
    'Sen arama sorgusu genişleticisisin. Sorguyu farklı açılardan 3 alternatif aramaya çevir. ' +
    'SADECE JSON: {"queries": string[3]}';
  const out = await chat([
    { role: 'system', content: sys },
    { role: 'user', content: `Sorgu: ${query}` },
  ]);
  if (!out) return [];
  const parsed = extractJson<{ queries?: string[] }>(out);
  return Array.isArray(parsed?.queries) ? parsed.queries.slice(0, 3) : [];
}

