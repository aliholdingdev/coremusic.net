/**
 * playwright.config.ts — E2E test configuration (WP2/J1 düzeltmesi 2026-10-07)
 *
 * Base URL: http://home.coremusic.net:81 (Apache vhost — hosts 12 girdisi;
 *   localhost:81 IIS varsayilan dizinden 403 doner, bu yuzden vhost adi kullanilir)
 * webServer docroot: home.coremusic.net/ (public/ YOK — index.php koke; A6 notu)
 * Timeout: 30s per test
 * Projects: chromium BASLANGIC (firefox/webkit/mobile sonra genisletilir)
 *
 * Run: npx playwright test -c assets.coremusic.net/playwright.config.ts
 *      (veya katalogdan: npm run test:e2e)
 */
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  testMatch: '**/*.spec.ts',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: [
    ['html'],
    ['list'],
    ['json', { outputFile: 'test-results/e2e-results.json' }],
  ],

  use: {
    baseURL: 'http://home.coremusic.net:81',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    // firefox/webkit/mobile projeleri bilinçli olarak kapali (J1 asgari kapsam);
    // e2e stabillesince geri acilir.
  ],

  webServer: {
    // public/ dizini YOK (disk kaniti); index.php panel koke'de.
    // Not (2026-10-30): Playwright webServer cwd = config dosyasının dizini
    // (assets.coremusic.net/), bu yüzden docroot `../home.coremusic.net/`
    // olmalıdır — `../../` C:\www\home.coremusic.net'e düşer ve yoktur
    // (gerçek hata: "Directory ../../home.coremusic.net/ does not exist").
    // IPv4 bind ZORUNLU: `php -S localhost:81` Windows'ta yalnız [::1]:81'e
    // bağlanır, hosts'taki 127.0.0.1 home.coremusic.net ise IPv4'ten reddedilir
    // (olay: webServer readiness 120s timeout — 2026-10-30 ölçüm).
    command: 'php -S 127.0.0.1:81 -t ../home.coremusic.net/',
    url: 'http://home.coremusic.net:81',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  },

  timeout: 30 * 1000,
  expect: {
    timeout: 5 * 1000,
  },
});
