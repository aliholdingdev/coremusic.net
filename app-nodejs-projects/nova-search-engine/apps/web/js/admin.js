const API = window.NOVA_API;
// ===========================================
// NovaSearch Admin — sistem durumu + işlemler
// Yerel kullanım içindir (dışa açılımda koruma gerekir).
// ===========================================

const statsEl = document.getElementById('stats');
const metricsEl = document.getElementById('metrics');
const actionOut = document.getElementById('action-out');
const trendingEl = document.getElementById('trending');

async function loadAll() {
  const [stats, metrics] = await Promise.all([
    fetch(API + '/api/stats').then((r) => r.json()),
    fetch(API + '/api/metrics').then((r) => r.json()),
  ]);
  statsEl.textContent = JSON.stringify(stats, null, 2);
  metricsEl.textContent = JSON.stringify(metrics, null, 2);
}

async function runAction(url, label) {
  actionOut.textContent = label + ' çalışıyor…';
  try {
    const res = await fetch(API + url).then((r) => r.json());
    actionOut.textContent = label + ' tamam:\n' + JSON.stringify(res, null, 2);
    await loadAll();
  } catch (e) {
    actionOut.textContent = label + ' hata: ' + e.message;
  }
}

async function loadTrending() {
  const res = await fetch(API + '/api/trending?limit=5').then((r) => r.json());
  trendingEl.textContent = '';
  for (const v of res.top || []) {
    const a = document.createElement('a');
    a.className = 'cm-card';
    a.href = v.url || 'https://www.youtube.com/watch?v=' + v.youtubeId;
    a.target = '_blank';
    a.rel = 'noopener';
    a.textContent = `${v.title} — ${v.channelName}`;
    trendingEl.appendChild(a);
  }
}

document.getElementById('btn-refresh').addEventListener('click', () => runAction('/api/refresh?days=0&limit=50', 'Tazeleme'));
document.getElementById('btn-backup').addEventListener('click', () => runAction('/api/backup', 'Yedek'));
document.getElementById('btn-trending').addEventListener('click', loadTrending);

loadAll();
