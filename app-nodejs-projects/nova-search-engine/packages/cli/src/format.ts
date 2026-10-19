// ===========================================
// CLI Çıktı Biçimlendirme — Unicode box tablolar
// ┌─┐│├┤└┘┴┬┼ ile çerçeveli, renkli çıktı.
// ===========================================

import { formatDuration, formatViews } from '@novasearch/common';
import type { SearchResponse } from '@novasearch/common';

const C = {
  reset: '\x1b[0m',
  dim: '\x1b[2m',
  bold: '\x1b[1m',
  green: '\x1b[32m',
  cyan: '\x1b[36m',
  yellow: '\x1b[33m',
};

/** Türkçe karakterleri saymayan genişlik hesabı (görsel hizalama). */
function width(text: string): number {
  // eslint-disable-next-line no-control-regex
  return text.replace(/\x1b\[[0-9;]*m/g, '').length;
}

function pad(text: string, size: number): string {
  return text + ' '.repeat(Math.max(0, size - width(text)));
}

function truncate(text: string, max: number): string {
  return width(text) <= max ? text : text.slice(0, Math.max(0, max - 1)) + '…';
}

/** Tek satırlı hücrelerden box tablo üret. */
export function boxTable(headers: string[], rows: string[][], colWidths: number[]): string {
  const line = (l: string, m: string, r: string) =>
    l + colWidths.map((w) => '─'.repeat(w + 2)).join(m) + r;

  const out: string[] = [];
  out.push(line('┌', '┬', '┐'));
  out.push('│ ' + headers.map((h, i) => pad(h, colWidths[i] ?? 0)).join(' │ ') + ' │');
  out.push(line('├', '┼', '┤'));
  for (const row of rows) {
    out.push('│ ' + row.map((c, i) => pad(truncate(c, colWidths[i] ?? 0), colWidths[i] ?? 0)).join(' │ ') + ' │');
  }
  out.push(line('└', '┴', '┘'));
  return out.join('\n');
}

/** Metni çerçeveli kutuya sar (AI analizi, istatistik vb.). */
export function box(title: string, lines: string[]): string {
  const inner = Math.max(width(title) + 2, ...lines.map((l) => width(l) + 2), 20);
  const out: string[] = [];
  out.push(`┌─${'─'.repeat(inner)}─┐`);
  out.push(`│ ${C.bold}${pad(title, inner)}${C.reset} │`);
  out.push(`├─${'─'.repeat(inner)}─┤`);
  for (const l of lines) {
    out.push(`│ ${pad(l, inner)} │`);
  }
  out.push(`└─${'─'.repeat(inner)}─┘`);
  return out.join('\n');
}

export function renderResults(res: SearchResponse): string {
  if (res.videos.length === 0) {
    return box('SONUÇ', [`${C.yellow}Sonuç bulunamadı.${C.reset}`]);
  }

  const headers = ['#', 'Başlık', 'Kanal', 'İzlenme', 'Beğeni', 'Tarih', 'Süre', 'Skor', 'Video ID', 'URL'];
  const widths = [3, 26, 13, 8, 8, 10, 7, 5, 12, 23];
  const rows = res.videos.map((v, i) => [
    String(i + 1),
    v.title,
    v.channelName,
    formatViews(v.viewCount),
    v.likeCount ? formatViews(v.likeCount) : '—',
    v.publishedAt ? v.publishedAt.slice(0, 10) : (v.publishedText ?? '—'),
    formatDuration(v.durationSeconds),
    `%${Math.round(v.score * 100)}`,
    v.youtubeId,
    `youtu.be/${v.youtubeId}`,
  ]);

  const out: string[] = [];

  // Neden önerildi? — her sonuç için gerekçe kutusu (ilk 5)
  const reasonLines: string[] = [];
  res.videos.slice(0, 5).forEach((v, i) => {
    reasonLines.push(`${C.green}${i + 1}.${C.reset} ${truncate(v.title, 52)}`);
    for (const r of v.reasons.slice(0, 3)) {
      reasonLines.push(`${C.dim}   ✓ ${r}${C.reset}`);
    }
    // Zengin metadata satırı: yayın · beğeni · kategori · rozetler
    const extras: string[] = [];
    if (v.publishedText || v.publishedAt) extras.push(`Yayın: ${v.publishedText ?? (v.publishedAt ?? '').slice(0, 10)}`);
    if (v.likeCount) extras.push(`Beğeni: ${formatViews(v.likeCount)}`);
    if (v.commentCount) extras.push(`Yorum: ${formatViews(v.commentCount)}`);
    if (v.category) extras.push(`Kategori: ${v.category}`);
    if (v.captionTracks && v.captionTracks.length > 0) {
      extras.push(`Altyazı: ${v.captionTracks.slice(0, 4).map((c) => c.language).join(',')}${v.captionTracks.length > 4 ? '+' : ''}`);
    }
    if (v.channelSubscriberCount) extras.push(`Abone: ${formatViews(v.channelSubscriberCount)}`);
    if (v.channelVerified) extras.push('✓ Doğrulanmış kanal');
    if (v.badges && v.badges.length > 0) extras.push(v.badges.slice(0, 3).map((b) => `[${b}]`).join(''));
    if (extras.length > 0) reasonLines.push(`${C.dim}   ▸ ${extras.join(' · ')}${C.reset}`);
    if (v.topComments && v.topComments.length > 0) {
      reasonLines.push(`${C.dim}   💬 ${truncate(v.topComments[0]?.text ?? '', 60)}${C.reset}`);
    }
    reasonLines.push(`${C.dim}   → ${v.url || `https://www.youtube.com/watch?v=${v.youtubeId}`}${C.reset}`);
    if (i < Math.min(res.videos.length, 5) - 1) reasonLines.push('');
  });
  void out;

  const meta: string[] = [
    `${C.cyan}Sorgu:${C.reset} ${res.query}`,
    `${C.cyan}Kaynak:${C.reset} ${res.source} · ${res.total} sonuç · ${res.tookMs}ms` +
      `${res.aiUsed ? ' · AI' : ''}${res.offline ? ' · OFFLINE' : ''}`,
  ];

  return [
    box('ARAMA SONUCU', meta),
    '',
    boxTable(headers, rows, widths),
    '',
    box('NEDEN ÖNERİLDİ?', reasonLines),
  ].join('\n');
}

export function renderAnalysis(res: SearchResponse): string {
  const lines: string[] = [];
  if (res.analysis.directive) {
    lines.push(`${C.cyan}AI sorgunuz:${C.reset} ${res.analysis.directive}`);
    lines.push('');
  }
  lines.push(`Genişletilen sorgular (${res.analysis.expandedQueries.length}):`);
  for (const q of res.analysis.expandedQueries.slice(0, 6)) {
    lines.push(`  • ${q}`);
  }
  return box('AI / SORGU ANALİZİ', lines);
}

export function renderStats(stats: {
  videoCount: number;
  cache: { hits: number; misses: number; ramSize: number };
  queue: { queued: number; processing: number; completed: number; failed: number };
  index?: { videos: number; terms: number; loaded: boolean };
}): string {
  const lines = [
    `Depodaki video   : ${stats.videoCount}`,
    `Cache (RAM)      : ${stats.cache.ramSize} kayıt · ${stats.cache.hits} isabet / ${stats.cache.misses} kaçırma`,
    `Kuyruk           : ${stats.queue.queued} bekliyor · ${stats.queue.processing} işleniyor`,
    `                 : ${stats.queue.completed} bitti · ${stats.queue.failed} başarısız`,
  ];
  if (stats.index) {
    lines.push(`Search Index     : ${stats.index.videos} video · ${stats.index.terms} terim`);
  }
  return box('SİSTEM İSTATİSTİKLERİ', lines);
}

export function renderTrending(top: Array<{ title: string; channelName: string; viewCount: number }>, recent: Array<{ title: string }>): string {
  const lines: string[] = [];
  lines.push(`${C.bold}En çok izlenenler${C.reset}`);
  top.slice(0, 5).forEach((v, i) => {
    lines.push(`  ${i + 1}. ${truncate(v.title, 56)}  ${C.dim}(${formatViews(v.viewCount)})${C.reset}`);
  });
  lines.push('');
  lines.push(`${C.bold}Son eklenenler${C.reset}`);
  recent.slice(0, 5).forEach((v, i) => {
    lines.push(`  ${i + 1}. ${truncate(v.title, 60)}`);
  });
  return box('TRENDING', lines);
}

export function renderStream(info: {
  youtubeId: string;
  title: string;
  durationSeconds: number;
  isLive: boolean;
  formats: Array<{ itag: number; quality: string; mimeType: string; hasAudio: boolean; hasVideo: boolean }>;
}): string {
  const headers = ['itag', 'kalite', 'mime', 'ses', 'video'];
  const rows = info.formats.slice(0, 12).map((f) => [
    String(f.itag),
    f.quality || '—',
    truncate(f.mimeType, 28),
    f.hasAudio ? '✓' : '—',
    f.hasVideo ? '✓' : '—',
  ]);
  return [
    box('AKIŞ BİLGİSİ', [
      `${info.title}`,
      `${C.dim}${info.youtubeId} · ${formatDuration(info.durationSeconds)}${info.isLive ? ' · CANLI' : ''}${C.reset}`,
      `Format sayısı: ${info.formats.length}`,
    ]),
    rows.length > 0 ? boxTable(headers, rows, [5, 10, 30, 4, 5]) : '',
  ].join('\n');
}
