/**
 * pages.spec.ts — E2E tests for Figma Pages (Playwright)
 * 
 * Coverage:
 * ✓ Browse & File Explorer (/browse)
 * ✓ Albums (/albumler)
 * ✓ Album Detail (/albumler/detay)
 * ✓ Artists (/sanatcilar)
 * ✓ Settings & Wi-Fi Modal (/ayarlar)
 * ✓ Playlist (/playlist)
 * ✓ DiskPlayer (/diskplayer)
 */
import { test, expect } from '@playwright/test';

const BASE_URL = '';

test.describe('Figma Pages E2E', () => {

  test('browse page: renders drive grid, search and explorer drill-down', async ({ page }) => {
    await page.goto(`${BASE_URL}/browse`);

    // Header & Layout
    await expect(page.locator('.page-browse')).toBeVisible();
    await expect(page.locator('.browse-disk-card')).toHaveCount(8);

    // Click first disk card to select, and open drill-down File Explorer (Figma 1976:11757)
    await page.locator('.browse-disk-card').first().click();
    await page.locator('#btn-browse-open').click();
    const explorer = page.locator('#browse-explorer-view');
    await expect(explorer).toHaveClass(/is-active/);
    await expect(page.locator('#explorer-path-text')).toBeVisible();
    await expect(page.locator('#explorer-search')).toBeVisible();

    // Explorer back button returns to disks
    await page.locator('#explorer-back-btn').click();
    await expect(explorer).not.toHaveClass(/is-active/);
  });

  test('albums page: renders genre chips and album grid', async ({ page }) => {
    await page.goto(`${BASE_URL}/albumler`, { waitUntil: 'networkidle' });

    await expect(page.locator('.page-albums')).toBeVisible();
    await expect(page.locator('.albums-tabs__tab')).toHaveCount(9);
    const albumCount = await page.locator('.album-card').count();
    expect(albumCount).toBeGreaterThanOrEqual(12);

    // Clicking genre tab filters
    const arabeskTab = page.locator('.albums-tabs__tab').filter({ hasText: 'Arabesk' });
    await arabeskTab.click();
    await expect(arabeskTab).toHaveClass(/is-active/);
  });

  test('album detail page: renders cover summary and track list', async ({ page }) => {
    await page.goto(`${BASE_URL}/albumler/detay`, { waitUntil: 'networkidle' });

    await expect(page.locator('.page-album-detail')).toBeVisible();
    await expect(page.locator('.album-detail-card')).toBeVisible();
    const trackCount = await page.locator('.album-track-row').count();
    expect(trackCount).toBeGreaterThanOrEqual(7);
  });

  test('artists page: renders circular cards and interactive sidebar', async ({ page }) => {
    await page.goto(`${BASE_URL}/sanatcilar`, { waitUntil: 'networkidle' });

    await expect(page.locator('.page-artists')).toBeVisible();
    const artistCount = await page.locator('.artist-card').count();
    expect(artistCount).toBeGreaterThanOrEqual(10);

    // Click second artist card updates sidebar
    const secondArtist = page.locator('.artist-card').nth(1);
    await secondArtist.click();
    await expect(secondArtist).toHaveClass(/is-active/);
  });

  test('settings page: renders Audio DSP, Bluetooth and Wi-Fi password modal (Figma 2831:9644)', async ({ page }) => {
    await page.goto(`${BASE_URL}/ayarlar`, { waitUntil: 'networkidle' });

    await expect(page.locator('.settings-page')).toBeVisible();
    await expect(page.locator('#section-audio-title')).toBeVisible();
    await expect(page.locator('#section-wifi-title')).toBeVisible();
    await expect(page.locator('#section-bt-title')).toBeVisible();

    // Click "Bağlan" on secured network opens Wi-Fi password modal
    const secureConnectBtn = page.locator('.settings-network-item[data-secure="true"] .settings-btn-connect').first();
    await secureConnectBtn.click();

    const modalOverlay = page.locator('#wifi-modal-overlay');
    await expect(modalOverlay).toHaveClass(/is-active/);
    await expect(page.locator('#wifi-password-input')).toBeFocused();

    // Cancel closes modal
    await page.locator('#wifi-modal-cancel').click();
    await expect(modalOverlay).not.toHaveClass(/is-active/);
  });

  test('playlist page: renders track table and wide sidebar', async ({ page }) => {
    await page.goto(`${BASE_URL}/playlist`, { waitUntil: 'networkidle' });

    await expect(page.locator('.player-table')).toBeVisible();
    await expect(page.locator('.page-sidebar--figma-wide')).toBeVisible();
  });

  test('diskplayer page: renders vinyl record, mini card and wide playlist', async ({ page }) => {
    await page.goto(`${BASE_URL}/diskplayer`, { waitUntil: 'networkidle' });

    await expect(page.locator('.player-content--diskplayer')).toBeVisible();
    await expect(page.locator('#dp-vinyl')).toBeVisible();
    await expect(page.locator('.player-mini-card')).toBeVisible();
    await expect(page.locator('.player-playlist--wide')).toBeVisible();
  });

  test('header quick network: opens and manages Wi-Fi quick panel (Figma 2831:9665)', async ({ page }) => {
    await page.goto(`${BASE_URL}/home`, { waitUntil: 'networkidle' });

    const wifiBtn = page.locator('.header-widget--signal');
    await expect(wifiBtn).toBeVisible();
    await wifiBtn.click();

    const wifiOverlay = page.locator('#quick-wifi-overlay');
    await expect(wifiOverlay).toHaveClass(/is-active/);
    await expect(page.locator('#quick-wifi-title')).toHaveText('WIFI');
    await expect(page.locator('#quick-wifi-current-name')).toBeVisible();

    // Close button closes panel
    await page.locator('#quick-wifi-close').click();
    await expect(wifiOverlay).not.toHaveClass(/is-active/);
  });

  test('header quick network: opens and manages Bluetooth quick panel (Figma 2831:9687)', async ({ page }) => {
    await page.goto(`${BASE_URL}/home`, { waitUntil: 'networkidle' });

    const btBtn = page.locator('.header-widget--bt');
    await expect(btBtn).toBeVisible();
    await btBtn.click();

    const btOverlay = page.locator('#quick-bt-overlay');
    await expect(btOverlay).toHaveClass(/is-active/);
    await expect(page.locator('#quick-bt-title')).toHaveText('Bluetooth');
    await expect(page.locator('#quick-bt-current-name')).toBeVisible();

    // Close button closes panel
    await page.locator('#quick-bt-close').click();
    await expect(btOverlay).not.toHaveClass(/is-active/);
  });

  test('playlist video page: renders video played view, mini player and rating stars (Figma 2831:9710)', async ({ page }) => {
    await page.goto(`${BASE_URL}/playlist?view=video`, { waitUntil: 'networkidle' });

    await expect(page.locator('.page-player--video')).toBeVisible();
    await expect(page.locator('.player-video-title')).toBeVisible();
    await expect(page.locator('.player-video-format-badge')).toHaveText('mp3');
    await expect(page.locator('.player-video-stars')).toBeVisible();
    await expect(page.locator('.player-mini-card')).toBeVisible();
    await expect(page.locator('.player-video-playlist-panel')).toBeVisible();
    await expect(page.locator('.player-video-accent-bar')).toBeVisible();
  });

  test('header quick settings: opens and interacts with settings popup menu (Figma 1047:16153)', async ({ page }) => {
    await page.goto(`${BASE_URL}/home`, { waitUntil: 'networkidle' });

    const settingsBtn = page.locator('#header-settings-btn');
    await expect(settingsBtn).toBeVisible();
    await settingsBtn.click();

    const settingsOverlay = page.locator('#quick-settings-overlay');
    await expect(settingsOverlay).toHaveClass(/is-active/);
    await expect(page.locator('#quick-settings-title')).toHaveText('Ayarlar');

    // Switch tab to audio
    const audioTabBtn = page.locator('.quick-settings-tab-btn[data-qs-tab="audio"]');
    await audioTabBtn.click();
    await expect(audioTabBtn).toHaveClass(/is-active/);
    await expect(page.locator('#qs-pane-audio')).toHaveClass(/is-active/);

    // Switch tab to system
    const sysTabBtn = page.locator('.quick-settings-tab-btn[data-qs-tab="system"]');
    await sysTabBtn.click();
    await expect(sysTabBtn).toHaveClass(/is-active/);
    await expect(page.locator('#qs-pane-system')).toHaveClass(/is-active/);

    // Close button closes panel
    await page.locator('#quick-settings-close').click();
    await expect(settingsOverlay).not.toHaveClass(/is-active/);
  });

  test('kesfet page: renders hero spotlight, mood cards and top charts', async ({ page }) => {
    await page.goto(`${BASE_URL}/kesfet`, { waitUntil: 'networkidle' });

    await expect(page.locator('.kesfet-page')).toBeVisible();
    await expect(page.locator('.kesfet-hero')).toBeVisible();
    await expect(page.locator('.kesfet-hero__title')).toHaveText('Sevil Neşelen');
    await expect(page.locator('.kesfet-chips-bar')).toBeVisible();

    // 6 mood cards
    const moodCards = page.locator('.kesfet-mood-card');
    await expect(moodCards).toHaveCount(6);

    // 10 top charts tracks
    const trackRows = page.locator('.kesfet-track-row');
    await expect(trackRows).toHaveCount(10);

    // Filter chip interaction: click 'pop'
    const popChip = page.locator('.kesfet-chip[data-genre="pop"]');
    await popChip.click();
    await expect(popChip).toHaveClass(/is-active/);

    // Pop tracks should remain visible
    const visiblePopTracks = page.locator('.kesfet-track-row[data-genre="pop"]');
    await expect(visiblePopTracks.first()).toBeVisible();
  });

  test('terminal page: renders Core AI Terminal, mode buttons and submits command (Figma 2135:24715)', async ({ page }) => {
    await page.goto(`${BASE_URL}/terminal`, { waitUntil: 'networkidle' });

    await expect(page.locator('.terminal-page-layout')).toBeVisible();
    await expect(page.locator('.terminal-title')).toHaveText('Core AI Terminal');
    await expect(page.locator('.terminal-modes-bar')).toBeVisible();

    // 5 mode buttons
    const modeBtns = page.locator('.terminal-mode-btn');
    await expect(modeBtns).toHaveCount(5);

    // Initial log stream lines exist
    const lines = page.locator('.terminal-line');
    await expect(lines.first()).toBeVisible();

    // Type and submit command
    const input = page.locator('#terminal-input');
    await input.fill('çal Göksel');
    await page.locator('.terminal-send-btn').click();

    // User command line appears
    await expect(page.locator('.terminal-tag--user').last()).toHaveText('Bayram');
    await expect(page.locator('.terminal-msg').last()).toContainText('çal Göksel');
  });

  test('home page: renders widget grid, player info, and song cards', async ({ page }) => {
    await page.goto(`${BASE_URL}/home`);

    await expect(page.locator('.home-layout')).toBeVisible();
    await expect(page.locator('.header-user')).toBeVisible();
    await expect(page.locator('.site-header__nav')).toBeVisible();

    // Check nav link to Kesfet
    const kesfetNavLink = page.locator('.site-header__nav a[href="/kesfet"]');
    await expect(kesfetNavLink).toBeVisible();

    // Check Player Info
    await expect(page.locator('.player-info')).toBeVisible();

    // Check Widget Grid
    await expect(page.locator('.widget-grid')).toBeVisible();

    // Check Song Cards Dashboard
    await expect(page.locator('.home_song__btn-dashboard').first()).toBeVisible();
  });

  test('history page: renders history toolbar, search and track rows', async ({ page }) => {
    await page.goto(`${BASE_URL}/history`);

    await expect(page.locator('.page-history')).toBeVisible();
    await expect(page.locator('.history-toolbar')).toBeVisible();
    await expect(page.locator('#history-search')).toBeVisible();

    // Track rows should exist
    const rows = page.locator('.history-row');
    await expect(rows.first()).toBeVisible();
  });

  test('header user dropdown: expands profile menu and displays options', async ({ page }) => {
    await page.goto(`${BASE_URL}/home`);

    const headerUser = page.locator('.header-user');
    await expect(headerUser).toBeVisible();

    // Click to expand user dropdown
    await headerUser.click();
    await expect(headerUser).toHaveAttribute('aria-expanded', 'true');

    const dropdown = page.locator('.header-user-dropdown');
    await expect(dropdown).toHaveClass(/header-user-dropdown--open/);
    await expect(dropdown).toBeVisible();
    await expect(dropdown.locator('a[href="/ayarlar"]')).toBeVisible();
    await expect(dropdown.locator('a[href="/history"]')).toBeVisible();
    await expect(dropdown.locator('a[href="/logout"]')).toBeVisible();
  });

  test('setup wizard page: renders 6 steps, interacts with options and steps to complete (Figma 1047:29968)', async ({ page }) => {
    await page.goto(`${BASE_URL}/wizard`);

    await expect(page.locator('#setup-wizard-view')).toBeVisible();
    await expect(page.locator('.wizard-glass')).toBeVisible();
    await expect(page.locator('.wizard-step-item')).toHaveCount(6);

    // Initial step is 1 (Hoş Geldiniz)
    await expect(page.locator('#wizard-step-title')).toHaveText('Hoş Geldiniz');
    await expect(page.locator('#wizard-pane-1')).toHaveClass(/is-active/);

    // Next step -> 2 (Cinsiyet & Enerji)
    await page.locator('#wizard-next-btn').click();
    await expect(page.locator('#wizard-step-title')).toHaveText('Cinsiyet & Enerji');
    await expect(page.locator('#wizard-pane-2')).toHaveClass(/is-active/);

    // Select Male option
    const maleCard = page.locator('.wizard-gender-card[data-gender="male"]');
    await maleCard.click();
    await expect(maleCard).toHaveClass(/is-selected/);

    // Jump to Step 4 (Lisans Sözleşmesi) via sidebar tab
    await page.locator('#wizard-tab-4').click();
    await expect(page.locator('#wizard-step-title')).toHaveText('Lisans Sözleşmesi');
    await expect(page.locator('#wizard-pane-4')).toHaveClass(/is-active/);
    await expect(page.locator('#wizard-license-check')).toBeChecked();

    // Next to Step 5 (Müzik Kaynakları)
    await page.locator('#wizard-next-btn').click();
    await expect(page.locator('#wizard-step-title')).toHaveText('Müzik Kaynakları');
    await expect(page.locator('#wizard-pane-5')).toHaveClass(/is-active/);

    // Connect Deezer
    const deezerBtn = page.locator('.wizard-storage-btn[data-storage="deezer"]');
    await deezerBtn.click();
    await expect(deezerBtn).toContainText('Bağlandı');

    // Next to Step 6 (Tamamlandı)
    await page.locator('#wizard-next-btn').click();
    await expect(page.locator('#wizard-step-title')).toHaveText('Tamamlandı');
    await expect(page.locator('#wizard-pane-6')).toHaveClass(/is-active/);
    await expect(page.locator('#wizard-start-btn')).toBeVisible();
  });

  test('welcome modal: dismisses properly when [Başla] is clicked', async ({ page }) => {
    await page.setViewportSize({ width: 1024, height: 600 });
    await page.goto(`${BASE_URL}/home`);

    const overlay = page.locator('#welcomeModalOverlay');
    if (await overlay.count() > 0) {
      await page.evaluate(() => {
        const el = document.getElementById('welcomeModalOverlay');
        if (el) el.classList.remove('is-hidden');
      });
      await expect(overlay).toBeVisible();
      await page.locator('.welcome-modal__btn').click();
      await expect(overlay).toHaveClass(/is-hidden/);
    }
  });

  test('responsive viewports: renders cleanly at 1024, 1920, 3840 and mobile 440px', async ({ page }) => {
    // 1024 Embedded
    await page.setViewportSize({ width: 1024, height: 600 });
    await page.goto(`${BASE_URL}/home`);
    await expect(page.locator('.home-layout')).toBeVisible();

    // 1920 Desktop Wide
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto(`${BASE_URL}/home?_test_resp=1920`);
    await expect(page.locator('.home-layout--wide')).toBeVisible();

    // 3840 4K
    await page.setViewportSize({ width: 3840, height: 2160 });
    await page.goto(`${BASE_URL}/home?_test_resp=3840`);
    await expect(page.locator('.home-layout--4k')).toBeVisible();

    // 440 Mobile iPhone
    await page.setViewportSize({ width: 440, height: 956 });
    await page.goto(`${BASE_URL}/home`);
    await expect(page.locator('.page-home')).toBeVisible();
  });

  test('quick settings modal: switches theme preview between dark and light', async ({ page }) => {
    await page.goto(`${BASE_URL}/home`);

    const settingsBtn = page.locator('#header-settings-btn');
    await settingsBtn.click();

    const overlay = page.locator('#quick-settings-overlay');
    await expect(overlay).toHaveClass(/is-active/);

    // Theme tab
    const themeTabBtn = page.locator('.quick-settings-tab-btn[data-qs-tab="theme"]');
    await themeTabBtn.click();
    await expect(themeTabBtn).toHaveClass(/is-active/);

    // Select light theme card
    const lightCard = page.locator('.quick-settings-theme-card[data-theme="light"]');
    if (await lightCard.count() > 0) {
      await lightCard.click();
      await expect(lightCard).toHaveClass(/is-selected/);
    }

    // Close settings modal
    await page.locator('#quick-settings-close').click();
    await expect(overlay).not.toHaveClass(/is-active/);
  });

});


