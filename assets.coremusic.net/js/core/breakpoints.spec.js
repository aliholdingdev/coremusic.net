import { describe, it, expect, beforeAll } from 'vitest';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

/* WP2/J2 — Breakpoint SSOT sözleşmesi:
   1) sınır değerleri (birebir eski device-loader davranışı)
   2) UA kalıpları (embedded / TV / mobil / 4k-monitor)
   3) tierOf eşitlikleri (DOM tier sözcük seti: phone|embedded|wide)
   4) PHP PARITY — DeviceDetector.php sabitleri === JS BREAKPOINTS (drift kilidi)
*/

beforeAll(async () => {
  // IIFE'yi çalıştır → window.CoreMusic.BREAKPOINTS / BreakpointAPI oluşur (jsdom)
  await import('./breakpoints.js');
});

const api = () => window.CoreMusic.BreakpointAPI;
const BP = () => window.CoreMusic.BREAKPOINTS;

describe('Breakpoint SSOT — spec', () => {
  it('global API dondurulmus ve eksiksiz', () => {
    expect(typeof BP()).toBe('object');
    expect(typeof api().detectDevice).toBe('function');
    expect(typeof api().detectUA).toBe('function');
    expect(typeof api().tierOf).toBe('function');
    expect(Object.isFrozen(BP())).toBe(true);
  });

  describe('sinir degerleri (eski detect davranisiyla birebir)', () => {
    const U = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';

    it('767 -> phone / 768 -> tablet-embedded siniri', () => {
      expect(api().detectDevice(767, 800, U, true)).toBe('phone');
      expect(api().detectDevice(768, 768, U, true)).toBe('tablet');
      expect(api().detectDevice(768, 600, U, true)).toBe('embedded'); // h<=600
      expect(api().detectDevice(768, 601, U, true)).toBe('tablet'); // 600 asmasi
    });

    it('1024 ustunde tablet degil (TABLET_MAX dahil), 1025 laptop', () => {
      expect(api().detectDevice(1024, 768, U, true)).toBe('tablet');
      expect(api().detectDevice(1025, 768, U, true)).toBe('laptop');
    });

    it('1440 laptop / 1441 desktop / 2560 desktop / 2561 4k bandi', () => {
      expect(api().detectDevice(1440, 900, U, true)).toBe('laptop');
      expect(api().detectDevice(1441, 900, U, true)).toBe('desktop');
      expect(api().detectDevice(2560, 1440, U, true)).toBe('desktop');
      // 2561 + Windows OS + pointer:fine → eski davranisla '4k-monitor'
      // (TV UA yok; asagidaki test TV/belirsiz dallari ayrica kapsar)
      expect(api().detectDevice(2561, 1440, U, true)).toBe('4k-monitor');
    });

    it('2561-3840: TV UA -> 4k-tv; desktop OS + pointer:fine -> 4k-monitor; belirsiz -> 4k-tv', () => {
      expect(api().detectDevice(3000, 2000, U + ' Tizen', true)).toBe('4k-tv');
      expect(api().detectDevice(3000, 2000, U, true)).toBe('4k-monitor'); // Windows + fine
      expect(api().detectDevice(3000, 2000, 'SomeBot/1.0', false)).toBe('4k-tv'); // fallback
    });

    it('3841+ -> 4k-monitor (FOUR_K_TV_MAX asimi)', () => {
      expect(api().detectDevice(3841, 2160, U, true)).toBe('4k-monitor');
      expect(api().detectDevice(5120, 2880, 'x', false)).toBe('4k-monitor');
    });
  });

  describe('UA kaliplari', () => {
    it('embedded UA her boyutta embedded (viewport onemsiz)', () => {
      expect(api().detectDevice(1920, 1080, 'Mozilla/5.0 (X11; Linux aarch64) Chrome', false)).toBe('embedded');
      expect(api().detectDevice(300, 200, 'Raspberry Pi Browser', false)).toBe('embedded');
    });

    it('detectUA mobil eslesmeleri', () => {
      expect(api().detectUA('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Safari')).toBe('phone');
      expect(api().detectUA('Mozilla/5.0 (Linux; Android 13; Pixel 7) Chrome Mobile')).toBe('phone');
      expect(api().detectUA('Mozilla/5.0 (Linux; Android 13; SM-X200) Chrome')).toBe('tablet');
      expect(api().detectUA('Mozilla/5.0 (iPad; CPU OS 17_0) Safari')).toBe('tablet');
      expect(api().detectUA('Mozilla/5.0 (Windows Phone 10.0) Edge')).toBe('phone');
      expect(api().detectUA('')).toBe(null);
      expect(api().detectUA('Mozilla/5.0 (X11; Linux aarch64)')).toBe('embedded');
    });
  });

  describe('tierOf (DOM tier sozcuk seti)', () => {
    it('4k cihazlar wide (d-4k.css ustlenir, ayri tier degil)', () => {
      expect(api().tierOf('4k-tv')).toBe('wide');
      expect(api().tierOf('4k-monitor')).toBe('wide');
    });

    it('telefon/pc/eslesme esitlikleri', () => {
      expect(api().tierOf('phone')).toBe('phone');
      expect(api().tierOf('desktop')).toBe('wide');
      expect(api().tierOf('laptop')).toBe('wide');
      expect(api().tierOf('tablet')).toBe('embedded');
      expect(api().tierOf('embedded')).toBe('embedded');
    });

    it('width fallback (device bosken)', () => {
      expect(api().tierOf('', 500)).toBe('phone');
      expect(api().tierOf('', 2000)).toBe('wide');
      expect(api().tierOf('', 900)).toBe('embedded');
    });

    it('UA embedded onceligi (cihaz ne olursa olsun)', () => {
      expect(api().tierOf('desktop', 1920, 'Raspberry Pi')).toBe('embedded');
    });
  });

  describe('PHP PARITY — DeviceDetector sabitleri', () => {
    let phpConsts;

    beforeAll(() => {
      // jsdom'da import.meta.url file:// degildir → process.cwd() (repo koku,
      // npm script repo kokunden calisir) uzerinden resolve edilir.
      const path = resolve(
        process.cwd(),
        'shared/src/Device/DeviceDetector.php'
      );
      const src = readFileSync(path, 'utf8');
      phpConsts = {};
      const re = /const\s+([A-Z_0-9]+)\s*=\s*(\d+);/g;
      let m;
      while ((m = re.exec(src)) !== null) {
        phpConsts[m[1]] = Number(m[2]);
      }
    });

    it('DeviceDetector.php dosyasi okunabildi (min 6 sabit)', () => {
      expect(Object.keys(phpConsts).length).toBeGreaterThanOrEqual(6);
    });

    it('JS BREAKPOINTS === PHP sabitleri (drift kilidi)', () => {
      expect(BP().PHONE_MAX).toBe(phpConsts.PHONE_MAX);
      expect(BP().TABLET_MAX).toBe(phpConsts.TABLET_MAX);
      expect(BP().EMBEDDED_MAX).toBe(phpConsts.EMBEDDED_MAX);
      expect(BP().LAPTOP_MAX).toBe(phpConsts.LAPTOP_MAX);
      expect(BP().DESKTOP_MAX).toBe(phpConsts.DESKTOP_MAX);
      expect(BP().FOUR_K_TV_MAX).toBe(phpConsts.FOUR_K_TV_MAX);
    });
  });
});