const { chromium } = require('c:/www/coremusic.net/assets.coremusic.net/node_modules/playwright');

(async () => {
  const browser = await chromium.launch();
  const testPages = [
    { name: 'playlist', url: 'http://home.coremusic.net:81/playlist', s1: '.player-glass', s2: '.page-sidebar--figma-wide' },
    { name: 'browse', url: 'http://home.coremusic.net:81/browse', s1: '.browse-glass', s2: '.browse-sidebar-panel, .page-sidebar--figma-wide' },
    { name: 'albumler', url: 'http://home.coremusic.net:81/albumler', s1: '.albums-main', s2: '.albums-sidebar, .page-sidebar--figma-wide' },
    { name: 'sanatcilar', url: 'http://home.coremusic.net:81/sanatcilar', s1: '.artists-main', s2: '.artists-sidebar, .page-sidebar--figma-wide' },
    { name: 'album-detail', url: 'http://home.coremusic.net:81/albumler/detay', s1: '.album-detail-main', s2: '.album-detail-sidebar, .page-sidebar--figma-wide' },
    { name: 'history', url: 'http://home.coremusic.net:81/history', s1: '.history-glass, .history-list', s2: '.page-sidebar--figma-wide, .page-history__sidebar' }
  ];

  for (const mode of [
    { desc: '1024 Embedded', vp: { width: 1024, height: 600 }, param: '?_test_resp=1024' },
    { desc: '1024 Standard', vp: { width: 1024, height: 600 }, param: '' },
    { desc: '1920 Wide', vp: { width: 1920, height: 1080 }, param: '' },
    { desc: '3840 4K', vp: { width: 3840, height: 2160 }, param: '?_test_resp=3840' }
  ]) {
    console.log(`\n=================== ${mode.desc} ===================`);
    const page = await browser.newPage({ viewport: mode.vp });
    for (const p of testPages) {
      await page.goto(p.url + mode.param, { waitUntil: 'load' });
      await page.waitForTimeout(50);
      const b1 = await page.locator(p.s1).first().boundingBox();
      const b2 = await page.locator(p.s2).first().boundingBox();
      if (!b1 || !b2) {
        console.log(`${p.name.padEnd(14)} | NOT FOUND (b1=${!!b1}, b2=${!!b2})`);
        continue;
      }
      const dy = b1.y - b2.y;
      const dh = b1.height - b2.height;
      const pass = Math.abs(dy) < 0.1 && Math.abs(dh) < 0.1;
      console.log(`${p.name.padEnd(14)} | ${pass ? 'PASS' : 'FAIL'} | Top: L=${b1.y.toFixed(1)}, R=${b2.y.toFixed(1)} (Δ=${dy.toFixed(1)}) | Height: L=${b1.height.toFixed(1)}, R=${b2.height.toFixed(1)} (Δ=${dh.toFixed(1)})`);
    }
    await page.close();
  }
  await browser.close();
})();
