// ===========================================
// Sinyal Hesapları — dil tespiti + spam skoru
// Saf fonksiyonlar (ağ/bağımlılık yok); mapper
// ve motor bunları ortak kullanır (tek kaynak).
// ===========================================

const TR_CHARS = /[çğıöşüÇĞİÖŞÜ]/;
const TR_WORDS = /\b(ve|bir|bu|için|şarkı|konser|canlı|resmi|bölüm|izle|dizi|film|türkçe)\b/u;
const EN_WORDS = /\b(the|and|official|video|live|lyrics|feat|full|episode|movie)\b/i;

/** Başlık + açıklama metninden dil tahmini. */
export function detectLanguage(...texts: string[]): 'tr' | 'en' | 'other' {
  const text = texts.filter(Boolean).join(' ');
  if (!text.trim()) return 'other';

  const trScore =
    (TR_CHARS.test(text) ? 2 : 0) +
    (text.match(TR_WORDS)?.length ?? 0) +
    (text.match(/[çğıöşü]/gi)?.length ?? 0) * 0.5;
  const enScore =
    (text.match(EN_WORDS)?.length ?? 0) * 1.5 +
    (text.match(/\b[a-z]{2,}\b/gi)?.length ?? 0) * 0.05;

  if (trScore === 0 && enScore === 0) return 'other';
  return trScore >= enScore ? 'tr' : 'en';
}

const CLICKBAIT = [
  'tıkla', 'tikla', 'şok', 'sok oldun', 'inanamayacaksın', 'inamayacaksin',
  'mutlaka izle', 'kaçırma', 'click here', "you won't believe", 'shocking',
  'giveaway', 'ücretsiz kazan', 'hediye kazan', 'tıklama rekoru',
];

/** Başlık + açıklamadan spam/tıklama tuzağı skoru (0-1). Yalnız işarettir. */
export function spamScore(title: string, description = ''): number {
  const text = `${title} ${description}`.trim();
  if (!text) return 0;

  let score = 0;

  // BÜYÜK HARF ORANI (başlıkta anlamsız bağırma)
  const letters = text.replace(/[^a-zçğıöşüA-ZÇĞİÖŞÜ]/g, '');
  if (letters.length > 8) {
    const upper = letters.replace(/[^A-ZÇĞİÖŞÜ]/g, '').length;
    if (upper / letters.length > 0.6) score += 0.35;
  }

  // Aşırı noktalama (!!!, ???)
  if (/!{2,}|\?{3,}/.test(text)) score += 0.2;

  // Tıklama tuzağı kelimeleri
  const lower = text.toLowerCase();
  score += Math.min(0.4, CLICKBAIT.filter((w) => lower.includes(w)).length * 0.2);

  // Aşırı emoji
  const emojis = text.match(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}]/gu)?.length ?? 0;
  if (emojis >= 4) score += 0.15;

  return Math.max(0, Math.min(1, Number(score.toFixed(3))));
}
