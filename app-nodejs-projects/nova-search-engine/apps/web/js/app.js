const API = window.NOVA_API;
// ===========================================
// NovaSearch Web — Vanilla JS (ADR-004)
// API: GET /api/search?q=...&ai=...&filter=...
// ===========================================

const form = document.getElementById('search-form');
const input = document.getElementById('search-input');
const button = document.getElementById('search-button');
const metaEl = document.getElementById('results-meta');
const listEl = document.getElementById('results-list');
const suggestEl = document.getElementById('search-suggest');
const statusEl = document.getElementById('status-text');

// --- Otomatik tamamlama (aroma önerileri) ---
let suggestTimer = null;
input.addEventListener('input', () => {
  clearTimeout(suggestTimer);
  suggestTimer = setTimeout(async () => {
    const q = input.value.trim();
    if (q.length < 2) return;
    try {
      const res = await fetch(API + `/api/suggest?q=${encodeURIComponent(q)}`);
      const data = await res.json();
      suggestEl.textContent = '';
      for (const s of (data.suggestions || []).slice(0, 8)) {
        const opt = document.createElement('option');
        opt.value = s;
        suggestEl.appendChild(opt);
      }
    } catch {
      // öneri hizmeti yoksa sessizce devam
    }
  }, 300);
});

// --- Sistem durumu (DB / cache) ---
async function loadStatus() {
  try {
    const res = await fetch(API + '/api/stats');
    const s = await res.json();
    statusEl.textContent =
      `Depo: ${s.videoCount} video · Cache: ${s.cache.hits} isabet / ${s.cache.misses} kaçırma · ` +
      `Kuyruk: ${s.queue.queued} bekliyor / ${s.queue.completed} bitti`;
  } catch {
    statusEl.textContent = 'Sistem durumu alınamadı';
  }
}
loadStatus();

function field(id) {
  const el = document.getElementById(id);
  return el.type === 'checkbox' ? el.checked : el.value;
}

function formatViews(n) {
  if (n >= 1e9) return (n / 1e9).toFixed(1) + 'B';
  if (n >= 1e6) return (n / 1e6).toFixed(1) + 'M';
  if (n >= 1e3) return (n / 1e3).toFixed(1) + 'K';
  return String(n);
}

function formatDuration(sec) {
  const s = Math.floor(sec || 0);
  const h = Math.floor(s / 3600);
  const m = Math.floor((s % 3600) / 60);
  const r = s % 60;
  return h > 0
    ? `${h}:${String(m).padStart(2, '0')}:${String(r).padStart(2, '0')}`
    : `${m}:${String(r).padStart(2, '0')}`;
}

function renderResults(res) {
  const aiNote = res.analysis && res.analysis.expandedQueries
    ? ` · ${res.analysis.expandedQueries.length} sorgu varyantı`
    : '';
  metaEl.textContent =
    `"${res.query}" → ${res.total} sonuç · kaynak: ${res.source} · ${res.tookMs}ms` +
    (res.aiUsed ? ' · AI' : '') + (res.offline ? ' · OFFLINE' : '') + aiNote;

  listEl.textContent = '';
  if (!res.videos || res.videos.length === 0) {
    const empty = document.createElement('p');
    empty.className = 'cm-results__meta';
    empty.textContent = 'Sonuç bulunamadı.';
    listEl.appendChild(empty);
    return;
  }

  for (const v of res.videos) {
    const card = document.createElement('article');
    card.className = 'cm-card';

    const img = document.createElement('img');
    img.className = 'cm-card__thumb';
    img.src = v.thumbnailUrl || '';
    img.alt = '';
    img.loading = 'lazy';

    const body = document.createElement('div');
    body.className = 'cm-card__body';

    const link = document.createElement('a');
    link.className = 'cm-card__title';
    link.href = `https://www.youtube.com/watch?v=${v.youtubeId}`;
    link.target = '_blank';
    link.rel = 'noopener';
    link.textContent = v.title;

    const meta = document.createElement('p');
    meta.className = 'cm-card__meta';
    meta.textContent =
      `${v.channelName} · ${formatViews(v.viewCount)} izlenme · ${formatDuration(v.durationSeconds)}` +
      (v.publishedText ? ' · ' + v.publishedText : '') +
      (v.language ? ' · ' + v.language.toUpperCase() : '') +
      (v.isShort ? ' · SHORTS' : '') + (v.isLive ? ' · CANLI' : '') +
      (v.badges && v.badges.length ? ' · ' + v.badges.slice(0, 2).join('/') : '');

    // Derin metadata: açıklama + etiketler
    if (v.description) {
      const desc = document.createElement('p');
      desc.className = 'cm-card__desc';
      desc.textContent = v.description.slice(0, 180);
      body.appendChild(link);
      body.appendChild(meta);
      body.appendChild(desc);
    } else {
      body.appendChild(link);
      body.appendChild(meta);
    }

    if (v.tags && v.tags.length > 0) {
      const tags = document.createElement('p');
      tags.className = 'cm-card__tags';
      tags.textContent = v.tags.slice(0, 6).map((t) => '#' + t).join(' ');
      body.appendChild(tags);
    }

    // Skor + neden önerildi (AI açıklaması)
    const score = document.createElement('span');
    score.className = 'cm-card__score';
    const pct = Math.round((v.score != null ? v.score : (v.qualityScore || 0)) * 100);
    score.textContent = `uyum: ${pct}%`;
    body.appendChild(score);

    // Spam/tıklama tuzağı uyarısı (yalnız işaret)
    if ((v.spamScore || 0) > 0.5) {
      const warn = document.createElement('span');
      warn.className = 'cm-card__badge--spam';
      warn.textContent = `⚠ spam skoru: ${Math.round(v.spamScore * 100)}%`;
      body.appendChild(warn);
    }

    if (v.reasons && v.reasons.length > 0) {
      const reasons = document.createElement('ul');
      reasons.className = 'cm-card__reasons';
      for (const r of v.reasons.slice(0, 4)) {
        const li = document.createElement('li');
        li.textContent = r;
        reasons.appendChild(li);
      }
      body.appendChild(reasons);
    }

    // Detay açılır paneli: tam metadata (derin veri)
    const detailBtn = document.createElement('button');
    detailBtn.type = 'button';
    detailBtn.className = 'cm-card__detail-btn';
    detailBtn.textContent = '▸ Detay';
    detailBtn.setAttribute('aria-expanded', 'false');

    const detail = document.createElement('div');
    detail.className = 'cm-card__detail';
    detail.hidden = true;
    const dl = document.createElement('dl');
    dl.className = 'cm-card__dl';
    const rows = [
      ['URL', v.url || 'https://www.youtube.com/watch?v=' + v.youtubeId],
      ['Video ID', v.youtubeId],
      ['Kanal', (v.channelName || '—') + (v.channelVerified ? ' (✓ doğrulanmış)' : '')],
      ['Kanal URL', v.channelUrl || '—'],
      ['Abone', v.channelSubscriberCount ? formatViews(v.channelSubscriberCount) : '—'],
      ['Süre', formatDuration(v.durationSeconds)],
      ['İzlenme', formatViews(v.viewCount) + (v.viewCountText ? ` (${v.viewCountText})` : '')],
      ['Beğeni', v.likeCount ? formatViews(v.likeCount) : '—'],
      ['Yorum', v.commentCount ? formatViews(v.commentCount) : '—'],
      ['Yayın', v.publishedText || v.publishedAt || '—'],
      ['Kategori', v.category || '—'],
      ['Skor', pct + '%'],
      ['Eşleşen', (v.matchedTerms || []).join(', ') || '—'],
      ['Etiketler', (v.tags || []).join(', ') || '—'],
      ['Rozetler', (v.badges || []).join(', ') || '—'],
      ['Altyazılar', (v.captionTracks || []).map((c) => c.name || c.language).join(', ') || '—'],
      ['İlgili video', (v.relatedIds || []).length + ' adet'],
      ['Aile güvenli', v.isFamilySafe === false ? 'hayır' : 'evet'],
      ['Görünürlük', v.isUnlisted ? 'unlisted' : (v.isCrawlable === false ? 'crawl yok' : 'herkese açık')],
      ['Kaynak', v.source || '—'],
    ];
    for (const [k, val] of rows) {
      const dt = document.createElement('dt');
      dt.textContent = k;
      const dd = document.createElement('dd');
      dd.textContent = val;
      dl.appendChild(dt);
      dl.appendChild(dd);
    }
    detail.appendChild(dl);
    if (v.description) {
      const full = document.createElement('p');
      full.className = 'cm-card__desc cm-card__desc--full';
      full.textContent = v.description;
      detail.appendChild(full);
    }

    // İlk yorumlar
    if (v.topComments && v.topComments.length > 0) {
      const h = document.createElement('p');
      h.className = 'cm-card__meta';
      h.textContent = '💬 İlk yorumlar:';
      detail.appendChild(h);
      for (const c of v.topComments.slice(0, 3)) {
        const p = document.createElement('p');
        p.className = 'cm-card__desc';
        p.textContent = `${c.author}: ${c.text}`;
        detail.appendChild(p);
      }
    }

    // Altyazı linkleri
    if (v.captionTracks && v.captionTracks.length > 0) {
      const h = document.createElement('p');
      h.className = 'cm-card__meta';
      h.textContent = 'Altyazılar:';
      detail.appendChild(h);
      for (const c of v.captionTracks.slice(0, 6)) {
        const a = document.createElement('a');
        a.className = 'cm-card__tag-link';
        a.href = c.url;
        a.target = '_blank';
        a.rel = 'noopener';
        a.textContent = (c.name || c.language) + (c.auto ? ' (otomatik)' : '');
        detail.appendChild(a);
      }
    }

    // İlgili videolar
    if (v.relatedIds && v.relatedIds.length > 0) {
      const h = document.createElement('p');
      h.className = 'cm-card__meta';
      h.textContent = 'İlgili videolar:';
      detail.appendChild(h);
      for (const rid of v.relatedIds.slice(0, 5)) {
        const a = document.createElement('a');
        a.className = 'cm-card__tag-link';
        a.href = 'https://www.youtube.com/watch?v=' + rid;
        a.target = '_blank';
        a.rel = 'noopener';
        a.textContent = rid;
        detail.appendChild(a);
      }
    }

    detailBtn.addEventListener('click', () => {
      const open = detail.hidden;
      detail.hidden = !open;
      detailBtn.textContent = open ? '▾ Detay' : '▸ Detay';
      detailBtn.setAttribute('aria-expanded', String(open));
    });

    body.appendChild(detailBtn);
    body.appendChild(detail);

    card.appendChild(img);
    card.appendChild(body);
    listEl.appendChild(card);
  }

  // Benzer öneriler (Recommendation Engine)
  const recs = res.recommendations || [];
  if (recs.length > 0) {
    const h = document.createElement('h2');
    h.className = 'cm-results__meta';
    h.textContent = 'Benzer öneriler';
    listEl.appendChild(h);
    for (const r of recs) {
      const rec = document.createElement('article');
      rec.className = 'cm-card cm-card--rec';
      const a = document.createElement('a');
      a.className = 'cm-card__title';
      a.href = r.url || 'https://www.youtube.com/watch?v=' + r.youtubeId;
      a.target = '_blank';
      a.rel = 'noopener';
      a.textContent = r.title;
      const m = document.createElement('p');
      m.className = 'cm-card__meta';
      m.textContent = `${r.channelName} · ${formatViews(r.viewCount)} izlenme`;
      rec.appendChild(a);
      rec.appendChild(m);
      listEl.appendChild(rec);
    }
  }
  loadStatus();
}

async function doSearch(event) {
  event.preventDefault();
  const query = input.value.trim();
  if (!query) return;

  button.disabled = true;
  button.textContent = 'Aranıyor…';
  metaEl.textContent = 'Aranıyor…';
  const params = new URLSearchParams({
    q: query,
    ai: String(field('f-ai')),
    aiQuery: String(field('f-aiQuery') || ''),
    extraQuery: String(field('f-extraQuery') || ''),
    filter: field('f-filter'),
    uploadDate: field('f-uploadDate'),
    duration: field('f-duration'),
    sortBy: field('f-sortBy'),
    result: field('f-result'),
    scroll: String(Number(field('f-result')) * 3),
  });

  try {
    const res = await fetch(API + `/api/search?${params.toString()}`);
    renderResults(await res.json());
  } catch {
    metaEl.textContent = 'Arama başarısız — API çalışıyor mu?';
  } finally {
    button.disabled = false;
    button.textContent = 'Ara';
  }
}

form.addEventListener('submit', doSearch);
