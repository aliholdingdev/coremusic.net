/** Konsol çıktı formatlayıcı — Unicode box tablo + AI rozetleri. ANSI renk kapatılabilir (NO_COLOR). */
import type { SearchResultPayload, ScoredResult, VideoDetailPayload } from '../core/types.ts';

const useColor = Boolean(process.stdout.isTTY) && !process.env.NO_COLOR;
const ESC = String.fromCharCode(27); // ANSI
const c = {
  dim: (s: string) => (useColor ? `${ESC}[2m${s}${ESC}[0m` : s),
  bold: (s: string) => (useColor ? `${ESC}[1m${s}${ESC}[0m` : s),
  green: (s: string) => (useColor ? `${ESC}[32m${s}${ESC}[0m` : s),
  yellow: (s: string) => (useColor ? `${ESC}[33m${s}${ESC}[0m` : s),
  cyan: (s: string) => (useColor ? `${ESC}[36m${s}${ESC}[0m` : s),
};

export function fmtViews(n: number): string {
  if (n >= 1_000_000_000) return `${(n / 1_000_000_000).toFixed(1)} Mr`;
  if (n >= 1_000_000) return `${(n / 1_000_000).toFixed(1)} M`;
  if (n >= 1_000) return `${(n / 1_000).toFixed(1)} B`;
  return String(n);
}

export function fmtDuration(sec: number): string {
  if (!sec) return '--:--';
  const h = Math.floor(sec / 3600);
  const m = Math.floor((sec % 3600) / 60);
  const s = sec % 60;
  const pad = (x: number) => String(x).padStart(2, '0');
  return h ? `${h}:${pad(m)}:${pad(s)}` : `${m}:${pad(s)}`;
}

/* ---------- görsel genişlik (UTF-8 + emoji) ---------- */

const WIDE_RE = /[\u{1F000}-\u{1FAFF}\u{231A}-\u{23FA}\u{2B00}-\u{2BFF}\u{2600}-\u{27BF}]/u;

/** Ekranda kaç birim kapladığını sayar (emoji 2, ZWJ/variation selector 0). */
function dispWidth(s: string): number {
  let w = 0;
  for (const ch of s) {
    const cp = ch.codePointAt(0) ?? 0;
    if (cp === 0x200d || (cp >= 0xfe00 && cp <= 0xfe0f) || (cp >= 0x0300 && cp <= 0x036f)) continue;
    w += WIDE_RE.test(ch) ? 2 : 1;
  }
  return w;
}

function padEndW(s: string, n: number): string {
  const w = dispWidth(s);
  return w >= n ? s : s + ' '.repeat(n - w);
}

function padStartW(s: string, n: number): string {
  const w = dispWidth(s);
  return w >= n ? s : ' '.repeat(n - w) + s;
}

/** Görsel genişliğe göre kırp; taşma varsa `…` ekle. */
function truncW(s: string, n: number): string {
  if (dispWidth(s) <= n) return s;
  let out = '';
  let w = 0;
  for (const ch of s) {
    const cw = dispWidth(ch);
    if (w + cw > n - 1) break;
    out += ch;
    w += cw;
  }
  return out + '…';
}

/* ---------- box tablo ---------- */

interface Cols {
  idx: number;
  title: number;
  channel: number;
  dur: number;
  views: number;
  score: number;
  id: number;
  url: number;
}

function tableCols(): Cols {
  const termW = Math.min(Math.max(process.stdout.columns ?? 120, 80), 170);
  // chrome: 9 dikey çizgi + 8 hücre × 2 boşluk = 25 · sabit: 2+14+7+7+7+11+28 = 76
  const title = Math.max(19, termW - 25 - 76);
  return { idx: 2, title, channel: 14, dur: 7, views: 7, score: 7, id: 11, url: 28 };
}

function borderOf(widths: number[], left: string, mid: string, right: string): string {
  const seg = (n: number) => '─'.repeat(n + 2);
  return left + widths.map((n) => seg(n)).join(mid) + right;
}

function border(left: string, mid: string, right: string, cols: Cols): string {
  return borderOf([cols.idx, cols.title, cols.channel, cols.dur, cols.views, cols.score, cols.id, cols.url], left, mid, right);
}

function cell(text: string, n: number, align: 'start' | 'end' = 'start'): string {
  const t = truncW(text, n);
  return ' ' + (align === 'end' ? padStartW(t, n) : padEndW(t, n)) + ' ';
}

function row(cells: string[], wrap: (s: string) => string = (s) => s): string {
  return wrap('│' + cells.join('│') + '│');
}

export function formatResults(payload: SearchResultPayload, explain: boolean): string {
  const { results, meta } = payload;
  const lines: string[] = [];
  lines.push(
    c.bold(`\n "${meta.query}"`) +
      c.dim(
        ` · ${results.length} sonuç · kaynak: ${meta.source} · ${meta.tookMs}ms` +
          (meta.offline ? ' · OFFLINE' : '') +
          (meta.aiEnabled ? ' · AI' : ''),
      ),
  );
  if (meta.expandedQueries?.length) {
    lines.push(c.dim(` deep: ${meta.expandedQueries.join(' | ')}`));
  }
  if (!results.length) {
    lines.push(c.yellow(' Sonuç yok.'));
    lines.push(c.dim(' İpucu: --offline kaldırın veya `nova collect "sorgu"` çalıştırın.'));
    return lines.join('\n');
  }

  const cols = tableCols();
  lines.push('');

  // üst çizgi → başlık satırı → ayraç → veri satırları → alt çizgi
  lines.push(border('┌', '┬', '┐', cols));
  lines.push(
    row(
      [
        cell('#', cols.idx),
        cell('Başlık', cols.title),
        cell('Kanal', cols.channel),
        cell('Süre', cols.dur, 'end'),
        cell('İzlenme', cols.views, 'end'),
        cell('Skor', cols.score, 'end'),
        cell('Video ID', cols.id),
        cell('URL', cols.url),
      ],
      c.bold,
    ),
  );
  lines.push(border('├', '┼', '┤', cols));

  for (let i = 0; i < results.length; i++) {
    const r = results[i];
    const score = r.aiScore != null ? `AI ${r.aiScore}` : `${r.score}`;
    lines.push(
      row(
        [
          cell(String(i + 1).padStart(2, '0'), cols.idx),
          cell(r.title, cols.title),
          cell(r.channelName, cols.channel),
          cell(fmtDuration(r.durationSec), cols.dur, 'end'),
          cell(fmtViews(r.viewCount), cols.views, 'end'),
          cell(score, cols.score, 'end'),
          cell(r.youtubeId, cols.id),
          cell(`https://youtu.be/${r.youtubeId}`, cols.url),
        ],
        (s) => (r.aiScore != null ? c.yellow(s) : c.dim(s)),
      ),
    );
  }
  lines.push(border('└', '┴', '┘', cols));

  // gerekçeler (tablo altında numaralı; URL'ler artık tablo içinde)
  for (let i = 0; i < results.length; i++) {
    const r = results[i];
    const n = c.cyan(String(i + 1).padStart(2, '0'));
    if (explain && r.aiReason) lines.push(' ' + n + c.dim(` > ${r.aiReason}`));
    else if (explain && r.matchPct != null) lines.push(' ' + n + c.dim(` > %${r.matchPct} uyum`));
  }
  lines.push('');
  return lines.join('\n');
}

/** İlerleme çubuğu parçası (collector/enrich paylaşır): [████░░░░] %50 */
export function progressBar(found: number, target: number, width = 24): string {
  const ratio = Math.min(1, found / Math.max(1, target));
  const filled = Math.round(ratio * width);
  return `[${'#'.repeat(filled)}${'.'.repeat(width - filled)}] %${String(Math.round(ratio * 100)).padStart(3)}`;
}

/** Video detay çıktısı — başlık kartı + YORUMLAR + BENZER VİDEOLAR (AI skorlu) kutu tabloları. */
export function formatVideoDetail(p: VideoDetailPayload): string {
  const m = p.meta;
  const lines: string[] = [];

  if (!p.video && !p.comments.length && !p.related.length) {
    lines.push(c.yellow(`\n Video bulunamadı: ${m.videoId}`));
    lines.push(c.dim(" İpucu: --offline kaldırın; DB'de yoksa online modda getInfo ile çekilir."));
    return lines.join('\n');
  }

  if (p.video) {
    const v = p.video;
    lines.push(c.bold(`\n ${v.title}`));
    const bits = [
      v.channelName || 'kanal ?',
      fmtDuration(v.durationSec),
      `${fmtViews(v.viewCount)} izlenme`,
      v.publishedAt ?? '',
      v.isShort ? 'SHORT' : '',
    ].filter(Boolean);
    lines.push(c.dim(` ${bits.join(' · ')}`));
    lines.push(c.dim(` https://www.youtube.com/watch?v=${v.youtubeId}`));
  } else {
    lines.push(c.yellow(`\n Video kaydı yok: ${m.videoId} (kaynak: ${m.source})`));
  }

  const metaBits = [
    `kaynak: ${m.source}`,
    `yorum: ${m.commentsCount}${m.commentsRequested ? `/${m.commentsRequested}` : ''}`,
    `benzer: ${m.relatedCount}${m.relatedRequested ? `/${m.relatedRequested}` : ''}`,
    m.aiScored ? `AI skorlu: ${m.aiScored}` : '',
    `${m.tookMs}ms`,
    m.commentsFromCache ? 'yorum cache' : '',
    m.relatedFromCache ? 'benzer cache' : '',
    m.offline ? 'OFFLINE' : '',
    m.degraded ? 'DEGRADED (YouTube erişilemedi → boş bölüm)' : '',
  ].filter(Boolean);
  lines.push(c.dim(` ${metaBits.join(' · ')}`));

  const termW = Math.min(Math.max(process.stdout.columns ?? 120, 80), 170);

  // ---- YORUMLAR ----
  lines.push('');
  lines.push(c.bold(` YORUMLAR (${p.comments.length})`));
  if (p.comments.length) {
    // chrome: 6 dikey +5 hücre ×2 =16 · sabit: 2+16+6+13 =37
    const cols = { idx: 2, author: 16, text: Math.max(20, termW - 53), likes: 6, date: 13 };
    lines.push(borderOf([cols.idx, cols.author, cols.text, cols.likes, cols.date], '┌', '┬', '┐'));
    lines.push(
      row(
        [
          cell('#', cols.idx),
          cell('Yazar', cols.author),
          cell('Yorum', cols.text),
          cell('Beğeni', cols.likes, 'end'),
          cell('Tarih', cols.date),
        ],
        c.bold,
      ),
    );
    lines.push(borderOf([cols.idx, cols.author, cols.text, cols.likes, cols.date], '├', '┼', '┤'));
    p.comments.forEach((cm, i) => {
      lines.push(
        row([
          cell(String(i + 1).padStart(2, '0'), cols.idx),
          cell(cm.isChannelOwner ? `${cm.authorName} ✓` : cm.authorName, cols.author),
          cell(cm.isPinned ? `📌 ${cm.text}` : cm.text, cols.text),
          cell(fmtViews(cm.likeCount), cols.likes, 'end'),
          cell(cm.publishedText ?? '', cols.date),
        ]),
      );
    });
    lines.push(borderOf([cols.idx, cols.author, cols.text, cols.likes, cols.date], '└', '┴', '┘'));
  } else {
    lines.push(c.dim(' yorum yok (istek yok · offline · veya YouTube erişilemedi).'));
  }

  // ---- BENZER VİDEOLAR ----
  lines.push('');
  lines.push(c.bold(` BENZER VİDEOLAR (${p.related.length})`));
  if (p.related.length) {
    // chrome: 8 dikey +7 hücre ×2 =22 · sabit: 2+12+7+9+7+28 =65
    const cols = { idx: 2, title: Math.max(19, termW - 87), channel: 12, views: 7, date: 9, score: 7, url: 28 };
    const w = [cols.idx, cols.title, cols.channel, cols.views, cols.date, cols.score, cols.url];
    lines.push(borderOf(w, '┌', '┬', '┐'));
    lines.push(
      row(
        [
          cell('#', cols.idx),
          cell('Başlık', cols.title),
          cell('Kanal', cols.channel),
          cell('İzlenme', cols.views, 'end'),
          cell('Tarih', cols.date),
          cell('AI', cols.score, 'end'),
          cell('URL', cols.url),
        ],
        c.bold,
      ),
    );
    lines.push(borderOf(w, '├', '┼', '┤'));
    for (let i = 0; i < p.related.length; i++) {
      const r = p.related[i];
      const score = r.aiScore != null ? `AI ${r.aiScore}` : '—';
      lines.push(
        row(
          [
            cell(String(i + 1).padStart(2, '0'), cols.idx),
            cell(r.contentType === 'SHORT' ? `⚡ ${r.title}` : r.title, cols.title),
            cell(r.channelName, cols.channel),
            cell(fmtViews(r.viewCount), cols.views, 'end'),
            cell(r.publishedText ?? '', cols.date),
            cell(score, cols.score, 'end'),
            cell(`https://youtu.be/${r.youtubeId}`, cols.url),
          ],
          (s) => (r.aiScore != null ? c.yellow(s) : c.dim(s)),
        ),
      );
    }
    lines.push(borderOf(w, '└', '┴', '┘'));
    // AI gerekçeleri (tablo altında numaralı)
    p.related.forEach((r, i) => {
      if (r.aiReason) lines.push(' ' + c.cyan(String(i + 1).padStart(2, '0')) + c.dim(` > ${r.aiReason}`));
    });
  } else {
    lines.push(c.dim(' benzer video yok (istek yok · offline · veya YouTube erişilemedi).'));
  }

  lines.push('');
  return lines.join('\n');
}
