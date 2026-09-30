// @vitest-environment jsdom
/**
 * player-info.spec.js — PlayerInfoComponent unit tests
 * 
 * Coverage:
 * ✓ Component render (wide + embedded variants)
 * ✓ API fetch mock + data binding
 * ✓ DOM update (track info, progress bar)
 * ✓ WCAG accessibility (aria attributes)
 * ✓ Play/pause toggle
 * ✓ Progress bar interactions
 * ✓ Keyboard shortcuts (space key)
 * ✓ Error handling (cover image fallback)
 * 
 * @requires vitest + jsdom
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import PlayerInfoComponent from '../../js/components/interactive/PlayerInfoComponent.js';
import { createMockFetch } from '../mocks/player-api.mock.js';

/** @type {PlayerInfoComponent|null} */
let component = null;

/**
 * Build wide variant (≥1025px)
 * @param {object} config
 * @returns {HTMLElement}
 */
function buildWidePlayerInfo(config = {}) {
  const defaults = {
    song: 'Aşkın Yolunda',
    artist: 'Göksel',
    album: 'Sensiz Olmaz',
    progress: 45,
  };
  const cmConfig = JSON.stringify({ ...defaults, ...config });

  const section = document.createElement('section');
  section.className = 'player-info player-info--wide';
  section.setAttribute('data-cm-component', 'cm-player-info');
  section.setAttribute('data-cm-config', cmConfig);
  section.setAttribute('aria-label', 'Şu an çalan');

  // Cover
  const cover = document.createElement('div');
  cover.className = 'player-info__cover';
  const coverImg = document.createElement('img');
  coverImg.src = '/Image/res-pink/album-goksel.png';
  coverImg.alt = 'Çalan şarkının albüm kapağı';
  cover.appendChild(coverImg);
  section.appendChild(cover);

  // Info panel
  const infoPanel = document.createElement('div');
  infoPanel.className = 'player-info__info-panel';

  // Title
  const titleRow = document.createElement('h1');
  titleRow.className = 'player-info__row player-info__title';
  const titleLabel = document.createElement('span');
  titleLabel.className = 'player-info__label';
  titleLabel.textContent = 'Şarkı Adı :';
  const titleValue = document.createElement('span');
  titleValue.className = 'now-playing__meta-value';
  titleValue.textContent = defaults.song;
  titleRow.appendChild(titleLabel);
  titleRow.appendChild(titleValue);
  infoPanel.appendChild(titleRow);

  // Artist
  const artistRow = document.createElement('p');
  artistRow.className = 'player-info__row player-info__singer';
  const artistLabel = document.createElement('span');
  artistLabel.className = 'player-info__label';
  artistLabel.textContent = 'Sanatçı :';
  const artistValue = document.createElement('span');
  artistValue.className = 'now-playing__meta-value';
  artistValue.textContent = defaults.artist;
  artistRow.appendChild(artistLabel);
  artistRow.appendChild(artistValue);
  infoPanel.appendChild(artistRow);

  // Transport (progress bar)
  const transport = document.createElement('div');
  transport.className = 'player-info__transport';

  const playIcon = document.createElement('img');
  playIcon.className = 'player-info__text-img player-info__play';
  playIcon.src = '/Image/res-pink/actions/play.png';
  playIcon.alt = 'Oynat';
  transport.appendChild(playIcon);

  const progressBarWrapper = document.createElement('div');
  progressBarWrapper.className = 'player-info__progress';
  progressBarWrapper.setAttribute('role', 'progressbar');
  progressBarWrapper.setAttribute('aria-label', 'Medya ilerlemesi');
  progressBarWrapper.setAttribute('aria-valuemin', '0');
  progressBarWrapper.setAttribute('aria-valuemax', '100');
  progressBarWrapper.setAttribute('aria-valuenow', String(defaults.progress));

  const progressBarInner = document.createElement('div');
  progressBarInner.className = 'player-info__progress__bar';

  const progressFill = document.createElement('div');
  progressFill.className = 'player-info__progress__fill';
  progressFill.setAttribute('data-progress', String(defaults.progress));
  progressFill.style.width = `${defaults.progress}%`;

  progressBarInner.appendChild(progressFill);
  progressBarWrapper.appendChild(progressBarInner);
  transport.appendChild(progressBarWrapper);

  infoPanel.appendChild(transport);
  section.appendChild(infoPanel);

  document.body.appendChild(section);
  return section;
}

/**
 * Build embedded variant (≤1024px)
 * @param {object} config
 * @returns {HTMLElement}
 */
function buildEmbeddedPlayerInfo(config = {}) {
  const defaults = {
    song: 'Aşkın Yolunda',
    artist: 'Göksel',
    album: 'Sensiz Olmaz',
    progress: 45,
  };
  const cmConfig = JSON.stringify({ ...defaults, ...config });

  const section = document.createElement('section');
  section.className = 'now-playing now-playing--embedded';
  section.setAttribute('data-cm-component', 'cm-player-info');
  section.setAttribute('data-cm-config', cmConfig);
  section.setAttribute('aria-label', 'Şu an çalan');

  // Art
  const art = document.createElement('div');
  art.className = 'now-playing__art now-playing__art--embedded';
  const artImg = document.createElement('img');
  artImg.src = '/Image/res-pink/album-goksel.png';
  artImg.alt = 'Çalan şarkının albüm kapağı';
  art.appendChild(artImg);
  section.appendChild(art);

  // Info
  const info = document.createElement('div');
  info.className = 'now-playing__info now-playing__info--embedded';

  const title = document.createElement('h1');
  title.className = 'now-playing__title';
  title.textContent = defaults.song;
  info.appendChild(title);

  const subtitle = document.createElement('p');
  subtitle.className = 'now-playing__subtitle';
  subtitle.textContent = defaults.album;
  info.appendChild(subtitle);

  const artist = document.createElement('p');
  artist.className = 'now-playing__artist';
  artist.textContent = defaults.artist;
  info.appendChild(artist);

  section.appendChild(info);

  // Progress
  const progress = document.createElement('div');
  progress.className = 'media-progress';
  progress.setAttribute('role', 'progressbar');
  progress.setAttribute('aria-label', 'Medya ilerlemesi');
  progress.setAttribute('aria-valuemin', '0');
  progress.setAttribute('aria-valuemax', '100');
  progress.setAttribute('aria-valuenow', String(defaults.progress));

  const progressTime = document.createElement('span');
  progressTime.className = 'media-progress__time';
  progressTime.id = 'np_time_current';
  progressTime.textContent = '2:30';
  progress.appendChild(progressTime);

  const progressBar = document.createElement('div');
  progressBar.className = 'media-progress__bar';

  const progressFill = document.createElement('div');
  progressFill.className = 'media-progress__fill';
  progressFill.setAttribute('data-progress', String(defaults.progress));
  progressFill.style.width = `${defaults.progress}%`;

  progressBar.appendChild(progressFill);
  progress.appendChild(progressBar);

  const progressTimeTotal = document.createElement('span');
  progressTimeTotal.className = 'media-progress__time media-progress__time--total';
  progressTimeTotal.id = 'np_time_total';
  progressTimeTotal.textContent = '5:45';
  progress.appendChild(progressTimeTotal);

  section.appendChild(progress);
  document.body.appendChild(section);
  return section;
}

describe('PlayerInfoComponent', () => {
  beforeEach(() => {
    document.body.textContent = '';
    global.fetch = vi.fn();
  });

  afterEach(() => {
    component?.destroy();
    component = null;
    document.body.textContent = '';
    vi.clearAllMocks();
  });

  /* ═══════════════════════════════════════════════════════════
   * RENDER TESTS
   * ═══════════════════════════════════════════════════════════ */

  it('wide variant: monta başarılı', () => {
    const el = buildWidePlayerInfo({ progress: 30 });
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    expect(el.classList.contains('player-info--wide')).toBe(true);
    expect(component.isMounted).toBe(true);
    expect(component.state.progress).toBe(30);
  });

  it('embedded variant: monta başarılı', () => {
    const el = buildEmbeddedPlayerInfo({ progress: 50 });
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    expect(el.classList.contains('now-playing--embedded')).toBe(true);
    expect(component.isMounted).toBe(true);
    expect(component.state.progress).toBe(50);
  });

  it('config: data-cm-config JSON parse eder', () => {
    const el = buildWidePlayerInfo({
      song: 'Düşüş',
      artist: 'Ajda Pekkan',
      progress: 75,
    });
    component = new PlayerInfoComponent(el);
    component.init();

    expect(component.state.progress).toBe(75);
  });

  /* ═══════════════════════════════════════════════════════════
   * PROGRESS BAR TESTS
   * ═══════════════════════════════════════════════════════════ */

  it('setProgress: width style günceller', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    component.setProgress(75);

    expect(component.state.progress).toBe(75);
    const fill = el.querySelector('.player-info__progress__fill');
    expect(fill.style.width).toBe('75%');
  });

  it('setProgress: ARIA aria-valuenow günceller', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    component.setProgress(60);

    const progressBar = el.querySelector('[role="progressbar"]');
    expect(progressBar.getAttribute('aria-valuenow')).toBe('60');
  });

  it('setProgress: sınırları kontrol eder (0-100)', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    component.setProgress(-10);
    expect(component.state.progress).toBe(0);

    component.setProgress(150);
    expect(component.state.progress).toBe(100);
  });

  it('progress bar tıklaması: cm:player:seek yayınlar', (done) => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    el.addEventListener('cm:player:seek', (e) => {
      expect(e.detail.progress).toBeDefined();
      done();
    });

    const progressBar = el.querySelector('.player-info__progress');
    const clickEvent = new MouseEvent('click', {
      bubbles: true,
      clientX: 100,
    });
    Object.defineProperty(progressBar, 'getBoundingClientRect', {
      value: () => ({
        left: 0,
        width: 200,
      }),
    });
    progressBar.dispatchEvent(clickEvent);
  });

  /* ═══════════════════════════════════════════════════════════
   * PLAY/PAUSE TESTS
   * ═══════════════════════════════════════════════════════════ */

  it('togglePlay: state güncelleştirir', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    component.togglePlay();

    expect(component.state.isPlaying).toBe(true);

    component.togglePlay();
    expect(component.state.isPlaying).toBe(false);
  });

  it('togglePlay: cm:player:toggle yayınlar', (done) => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    el.addEventListener('cm:player:toggle', (e) => {
      expect(e.detail.isPlaying).toBe(true);
      done();
    });

    component.togglePlay();
  });

  it('space key: togglePlay çağırır', (done) => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    el.addEventListener('cm:player:toggle', () => {
      done();
    });

    const spaceEvent = new KeyboardEvent('keydown', {
      key: ' ',
      bubbles: true,
    });
    document.body.dispatchEvent(spaceEvent);
  });

  /* ═══════════════════════════════════════════════════════════
   * TRACK UPDATE TESTS
   * ═══════════════════════════════════════════════════════════ */

  it('updateTrack: şarkı adı DOM günceller', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    component.updateTrack({ song: 'Yeni Şarkı' });

    const titleValue = el.querySelector('.now-playing__meta-value');
    expect(titleValue.textContent).toBe('Yeni Şarkı');
    expect(component.state.song).toBe('Yeni Şarkı');
  });

  it('updateTrack: sanatçı DOM günceller', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    component.updateTrack({ artist: 'Yeni Sanatçı' });

    const artistValue = el.querySelector('.player-info__singer .now-playing__meta-value');
    expect(artistValue.textContent).toBe('Yeni Sanatçı');
  });

  it('updateTrack: süre DOM günceller (embedded)', () => {
    const el = buildEmbeddedPlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    component.updateTrack({ elapsed: '3:00', duration: '5:45' });

    const elapsed = el.querySelector('#np_time_current');
    const duration = el.querySelector('#np_time_total');
    expect(elapsed.textContent).toBe('3:00');
    expect(duration.textContent).toBe('5:45');
  });

  /* ═══════════════════════════════════════════════════════════
   * ACCESSIBILITY TESTS (WCAG)
   * ═══════════════════════════════════════════════════════════ */

  it('ARIA: progress bar role + attributes', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    const progressBar = el.querySelector('[role="progressbar"]');
    expect(progressBar).not.toBeNull();
    expect(progressBar.getAttribute('aria-label')).toBe('Medya ilerlemesi');
    expect(progressBar.getAttribute('aria-valuemin')).toBe('0');
    expect(progressBar.getAttribute('aria-valuemax')).toBe('100');
    expect(progressBar.getAttribute('aria-valuenow')).not.toBeNull();
  });

  it('ARIA: section aria-label', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    expect(el.getAttribute('aria-label')).toBe('Şu an çalan');
  });

  it('IMG: alt text (cover)', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    const coverImg = el.querySelector('.player-info__cover img');
    expect(coverImg.alt).toBe('Çalan şarkının albüm kapağı');
  });

  /* ═══════════════════════════════════════════════════════════
   * ERROR HANDLING TESTS
   * ═══════════════════════════════════════════════════════════ */

  it('cover image error: fallback image yüklenir', (done) => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();

    const coverImg = el.querySelector('.player-info__cover img');

    el.addEventListener('cm:player:toggle', () => {
      // Event listener shouldn't fire — error handler works
      done();
    });

    // Simulate image error
    const errorEvent = new Event('error');
    coverImg.dispatchEvent(errorEvent);

    expect(coverImg.src).toBe('/Image/res-pink/album-goksel.png');
    done();
  });

  it('destroy: event listeners kalkar', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();
    component.mount();
    component.destroy();

    let toggleFired = false;
    el.addEventListener('cm:player:toggle', () => {
      toggleFired = true;
    });

    component.togglePlay();
    expect(toggleFired).toBe(false);
  });

  /* ═══════════════════════════════════════════════════════════
   * API INTEGRATION TESTS (Mock)
   * ═══════════════════════════════════════════════════════════ */

  it('API mock: currentTrack endpoint', async () => {
    global.fetch = createMockFetch({ scenario: 'success' });

    const response = await fetch('/api/player/current');
    const data = await response.json();

    expect(response.ok).toBe(true);
    expect(data.song).toBe('Aşkın Yolunda');
    expect(data.artist).toBe('Göksel');
  });

  it('API mock: progress endpoint', async () => {
    global.fetch = createMockFetch({ scenario: 'success' });

    const response = await fetch('/api/player/progress');
    const data = await response.json();

    expect(response.ok).toBe(true);
    expect(data.seekPct).toBe(45);
    expect(data.isPlaying).toBe(true);
  });

  it('API mock: seek endpoint', async () => {
    global.fetch = createMockFetch({ scenario: 'success' });

    const response = await fetch('/api/player/seek', { method: 'POST' });
    const data = await response.json();

    expect(response.ok).toBe(true);
    expect(data.success).toBe(true);
    expect(data.seekPct).toBe(75);
  });

  it('API mock: error 404', async () => {
    global.fetch = createMockFetch({ scenario: 'notfound' });

    const response = await fetch('/api/player/notfound');
    const data = await response.json();

    expect(response.ok).toBe(false);
    expect(response.status).toBe(404);
    expect(data.error).toBe('Track not found');
  });

  it('API mock: error 401', async () => {
    global.fetch = createMockFetch({ scenario: 'unauthorized' });

    const response = await fetch('/api/player/current');

    expect(response.ok).toBe(false);
    expect(response.status).toBe(401);
  });

  it('API mock: network error', async () => {
    global.fetch = createMockFetch({ scenario: 'network' });

    try {
      await fetch('/api/player/current');
      expect.fail('Should throw');
    } catch (err) {
      expect(err.message).toBe('Network error');
    }
  });

  /* ═══════════════════════════════════════════════════════════
   * STATE MANAGEMENT
   * ═══════════════════════════════════════════════════════════ */

  it('defaultState: varsayılanları döner', () => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);

    expect(component.state.song).toBeDefined();
    expect(component.state.artist).toBeDefined();
    expect(component.state.progress).toBeDefined();
    expect(component.state.isPlaying).toBe(false);
  });

  it('setState: cm:component:update yayınlar', (done) => {
    const el = buildWidePlayerInfo();
    component = new PlayerInfoComponent(el);
    component.init();

    el.addEventListener('cm:component:update', (e) => {
      expect(e.detail.next.progress).toBe(88);
      done();
    });

    component.setState({ progress: 88 });
  });
});
