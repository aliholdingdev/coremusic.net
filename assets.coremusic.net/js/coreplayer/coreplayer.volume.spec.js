/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, beforeAll, afterEach } from 'vitest';

function makeVolumeDom({
  src = 'http://assets.coremusic.net:81/Image/res-pink/volume-high.png',
  dataBasePath = 'http://assets.coremusic.net:81/Image/res-pink/',
  dataIconName = 'volume-high.png',
} = {}) {
  const container = document.createElement('div');
  container.className = 'footer-player__volume';

  const icon = document.createElement('img');
  icon.id = 'volumeIcon';
  icon.src = src;
  if (dataBasePath !== null) icon.dataset.basePath = dataBasePath;
  if (dataIconName !== null) icon.dataset.iconName = dataIconName;
  container.appendChild(icon);

  const track = document.createElement('div');
  track.className = 'footer-player__volume-track';
  track.id = 'volumeclick';

  const input = document.createElement('input');
  input.type = 'range';
  input.id = 'volume';
  input.className = 'footer-player__volume-slider';
  input.min = '0';
  input.max = '1';
  input.step = '0.01';
  input.value = '1';
  track.appendChild(input);

  const fill = document.createElement('div');
  fill.className = 'footer-player__volume-fill';
  fill.id = 'volume2';
  fill.dataset.progress = '100';
  track.appendChild(fill);

  container.appendChild(track);

  const val = document.createElement('span');
  val.className = 'footer-player__volume-value';
  val.textContent = '% 100';
  container.appendChild(val);

  document.body.appendChild(container);
  return { container, icon, track, input, fill, val };
}

beforeAll(async () => {
  // Ensure CorePlayerShared is available for VolumeRepository
  await import('./coreplayer.shared.js');
  await import('./coreplayer.volume.js');
});

afterEach(() => {
  if (window.VolumeController && typeof window.VolumeController.destroy === 'function') {
    window.VolumeController.destroy();
  }
  document.body.innerHTML = '';
});

describe('VolumeController — icon src ve path güvenliği', () => {
  it('41% ses seviyesinde volumeIcon src adresi doğru base path ve volume-medium.png ile güncellenir', () => {
    const { icon, val, fill } = makeVolumeDom({
      src: 'http://assets.coremusic.net:81/Image/res-pink/volume-high.png',
      dataBasePath: 'http://assets.coremusic.net:81/Image/res-pink/',
    });

    window.VolumeController.init();
    window.VolumeController.setVolume(0.41);

    expect(icon.src).toBe('http://assets.coremusic.net:81/Image/res-pink/volume-medium.png');
    expect(val.textContent).toBe('% 41');
    expect(fill.dataset.progress).toBe('41');
  });

  it('data-base-path verilmediğinde mevcut src üzerinden path doğru çözümlenir (port 81 korunur)', () => {
    const { icon } = makeVolumeDom({
      src: 'http://assets.coremusic.net:81/Image/res-pink/volume-high.png',
      dataBasePath: null,
    });

    window.VolumeController.init();
    window.VolumeController.setVolume(0.2);

    expect(icon.src).toBe('http://assets.coremusic.net:81/Image/res-pink/volume-low.png');
  });

  it('ses 0 olduğunda volume-mute.png ikonu seçilir ve a11y label "Sesi aç" olur', () => {
    const { icon } = makeVolumeDom();

    window.VolumeController.init();
    window.VolumeController.setVolume(0);

    expect(icon.src).toBe('http://assets.coremusic.net:81/Image/res-pink/volume-mute.png');
    expect(icon.getAttribute('aria-label')).toBe('Sesi aç');
  });

  it('geçersiz res-default veya res-blue içeren iconBasePath ezilir ve res-pink kullanılır', () => {
    const { icon } = makeVolumeDom({
      src: 'http://assets.coremusic.net:81/Image/res-pink/volume-high.png',
      dataBasePath: 'http://assets.coremusic.net:81/Image/res-pink/',
    });

    // Hatalı init çağrısı simülasyonu
    window.VolumeController.init({
      iconBasePath: '//assets.coremusic.net/Image/res-default/',
    });
    window.VolumeController.setVolume(0.85);

    expect(icon.src).not.toContain('res-default');
    expect(icon.src).toBe('http://assets.coremusic.net:81/Image/res-pink/volume-high.png');
  });
});
